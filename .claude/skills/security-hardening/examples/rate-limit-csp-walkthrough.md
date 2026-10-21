# Yürüyüş: Rate Limit + CSP Nonce'ünü Bir Uç Noktaya Ekleme

> Skill: `security-hardening` v4.0.0 · CoreMusic · Güncel: 2026-10-21
> Referans: ADR-013 (rate limit — APCu, 60/60s) · ADR-012 (CSP nonce —
> 4→6→7→8 zinciri) · `.ai/CLAUDE.md` §6 (RateLimiter 3/10,
> SecurityHeaders 4/10).
> **Önce:** `references/middleware-security-order.md` (sıravideli) +
> `examples/csrf-change-walkthrough.md` (temel CSRF döngüsü).

---

## 0. Senaryo

`GET /api/search?q=` — okuma uç noktası; (a) scraping/brute-force için rate
limit, (b) dönen HTML/JS parçasında inline script → CSP nonce. İkisi bağımsız
görünür ama **pipeline sırası ortak**: RateLimiter (3/10) → SecurityHeaders
(4/10) — ikisi de önce gelir, Auth (9/10) ÖNCESİ.

---

## 1. Gözlemle (read-only)

```powershell
# 1. Uç nokta + pipeline sırası (SIRA DEĞİŞTİRME YOK — §7)
Get-Content .\shared\src\Middleware\MiddlewarePipeline.php

# 2. Mevcut rate limit (var mı, APCu mı, limit ne)
Get-ChildItem -Recurse -Include *.php |
  Select-String -Pattern 'apcu_inc|apcu_add|429|Retry-After|RateLimit' |
  Select-Object Path,LineNumber,Line

# 3. CSP (nonce var mı, unsafe-inline kalıntısı)
Get-ChildItem -Recurse -Include *.php,*.conf,*.htaccess |
  Select-String -Pattern "Content-Security-Policy|csp_nonce|nonce|unsafe-inline" |
  Select-Object Path,LineNumber,Line

# 4. Inline script yüzeyi (nonce'suz <script> kaç tane?)
Get-ChildItem -Recurse -Include *.php |
  Select-String -Pattern '<script(?![^>]*nonce)' | Measure-Object
```

**Kayıt:** limit varsa ADR-013 (60/60s) ile uyumlu mu; `unsafe-inline`
kullanımı → ADR-012 §1 ihlali; nonce kaç dosyada dönüyor (4/6/7/8).

**STOP kapısı #1:** `Content-Security-Policy` başlığı `report-only` DEĞİLSE ve
inline script sayısı >0 ise **önce nonce zincirini kur** (enforce'i sonraya
bırak — §5.3); enforce + nonce'suz script = sayfa kırılır (ADT-009).
**STOP kapısı #2:** pipeline'da RateLimiter yoksa → yeni middleware ekleme
**yeni karar** (ADR ≥088 taslağı — §7.4); mevcut varsa limit değerini
değiştirmek ADR-013 §1 "orantılı" kuralına tabi.

---

## 2. Tasarım

### 2.1 Rate limit (ADR-013 §2 — değerler sabit)

| Karar | Değer | Kaynak |
|---|---|---|
| Algoritma | **fixed window** (token bucket YASAK §1) | ADR-013 §1 |
| Sayaç | **APCu** (`apcu_inc` atomik) | ADR-013 §2 |
| Limit | **60 istek / 60 saniye** (kural 5) | `.ai/CLAUDE.md` kural 5 |
| Kimlik | signed URL → user_id → client_id → ip | ADR-013 §2 |
| Sıra | global → signed URL → endpoint (ADT-014 sırası) | ADR-013 §2 |
| Rate limit tersi/Redis düzleyici | **yok** (ADT-014: read-after-write race) | ADR-013 §2 |
| Fail | sayaç okunamazsa **429** (fail-closed — §1.4) | ADR-013 §1 |
| Header | `Retry-After: 60` | ADR-013 §2 |
| Login-özel eşik | **⚠️ VERIFICATION REQUIRED** (ADR-013'te yok) | §sources |

### 2.2 CSP (ADR-012 §2)

| Karar | Değer | Kaynak |
|---|---|---|
| Nonce üretimi | 256-bit (`bin2hex(random_bytes(32))`) — **SecurityHeaders 4/10** | ADR-012 §2 |
| Zincir | 4/10 üretir → 6/10 session → 7/10 CSRF hidden → 8/10 view `$nonce` | ADR-012 §2 |
| `unsafe-inline` | **yok** (§1 yasağı) | ADR-012 §1 |
| 2. kez yazım | response'a 2. kez nonce koyma (Controller'da `header()` YASAK) | ADR-012 §2 |
| Göz ardı edilen tek satır | report-only → enforce | ADT-009 |

---

## 3. Pipeline (§2 — sıra görünümü)

```
1/10 OriginCheck   (varsa)     → Origin reddi (ADR-035 §2)
2/10 Cors          (varsa)     → whitelist (ADR-009 §2)
3/10 RateLimiter               → BU GÖREVIN 1. YARISI (60/60s, 429)
4/10 SecurityHeaders           → BU GÖREVIN 2. YARISI (nonce üret)
5/10 SessionManager            → 6/10'a veri (csp_nonce + rate kimlik)
6/10 Session                   → ...
7/10 Csrf                      → (varsa — POST'lar için)
8/10 BypassAuth                → prod bypass yok (kural 13)
9/10 Auth                      → oturum
10/10 Validation               → input
→ Controller (search işi)
```

**Yasaklar:** RateLimiter'ı Auth SONRASINA almak (kimlik doğrulama
resource'unu korumaz — anonih saldırılara açık); SecurityHeaders'ı controller'a
taşımak; `apcu_*` çağrılarını view'da tekrarlamak (sayaç controller'da
double-count).

---

## 4. Uygulama

### 4.1 RateLimiter (3/10)

```php
declare(strict_types=1);

namespace Shared\Middleware;

final class RateLimiter implements MiddlewareInterface
{
    private const LIMIT = 60;            // kural 5 — ADR-013 §2
    private const WINDOW = 60;           // saniye

    public function handle(Request $request, Pipeline $next): Response
    {
        if (!function_exists('apcu_inc')) {
            // fail-closed — ADR-013 §1: sayaç yoksa GEÇİRME
            return new Response('Service Unavailable', 503);
        }

        $key = 'rl:' . $this->identity($request);   // global→signed→client→ip
        $now = time();
        $start = apcu_add($key . ':t', $now, self::WINDOW);   // atomik pencere başlangıcı
        if ($start === false) {                                 // pencere içinde
            $count = apcu_inc($key, 1, $ok);                    // atomik artış
            if (!$ok || $count > self::LIMIT) {
                return $this->throttled();                      // 429
            }
        }
        // yeni pencere: sayaç yok → ilk istek, sayılmaz (apcu_add zaten saydı)
        return $next($request);
    }

    private function throttled(): Response
    {
        $r = new Response('Too Many Requests', 429);
        $r->headers->set('Retry-After', (string) self::WINDOW);
        return $r;
    }

    private function identity(Request $r): string
    {
        $signed = $r->query('sig') ?? null;                       // signed URL
        if ($signed !== null && $this->signedUrlValid($signed)) {
            return 'u:' . $this->userIdFromSignedUrl($signed);    // 1. global değil — 2. signed
        }
        $cid = $r->header('X-Client-Id') ?? null;                 // client_id (DB doğrulama — §4.4)
        if ($cid !== null && $this->clientExists($cid)) { return 'c:' . $cid; }
        return 'i:' . ($r->ip() ?? 'unknown');                    // 3. ip (gateway'den)
    }
}
```

**Nüanslar (§SKILL 6.3):** `apcu_add(..., WINDOW)` pencere başlangıcını da
atomik kurar; `apcu_inc` atomiktir ama **interval ≥ window** olmalı
(5/sn × 60 = 300/60s olur — kural ihlali). Kimlik kestirme: `X-Forwarded-For`
yalnız gateway'den (ADR-013 §2).

### 4.2 SecurityHeaders (4/10 — nonce üretimi)

```php
// handle() içinde — üretimi TEK YERDE (ADR-012 §2)
$nonce = bin2hex(random_bytes(32));                 // 256-bit
$_SESSION['csp_nonce'] = $nonce;                    // 6/10 Session'a taşı
$request->attributes->set('csp_nonce', $nonce);     // request üzerinden Controller'a

$csp = "default-src 'self'; "
     . "script-src 'self' 'nonce-{$nonce}'; "       // unsafe-inline YOK
     . "style-src 'self'; img-src 'self' data:; "
     . "connect-src 'self'; object-src 'none'; "
     . "base-uri 'self'; form-action 'self'; frame-ancestors 'none'";
$resp->headers->set('Content-Security-Policy', $csp);
// İLK GEÇİŞ (ADT-009): Policy-Report-Only başlığına aynı değer koyup
// console/report gözlemi → enforce'e taşı; ikisi AYNI anda değil (fallback).
```

### 4.3 İstemci (nonce'lu inline — view 8/10)

```php
<script nonce="<?= htmlspecialchars($_SESSION['csp_nonce'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
  // küçük davranış — ADR-012 §2: 2. kez header() YASAK
  document.querySelector('#search').addEventListener('input', debounce(run, 300));
</script>
```

**Alternatif (önerilen):** inline script'i `/assets/search.js` dosyasına taşı
→ nonce gerekmez (`script-src 'self'` yeter) — ADR-012 §2'nin "script-src
'nonce-...' + inline" şartını basitleştirir. Bu **tasarım tercihi** —
vault'ta zorunlu inline kararı yok → **⚠️ VERIFICATION REQUIRED**
(ADR-012 §1'in "inline" yorumu: tüm inline'lar yasak mı, nonce'lu inline
serbest mi — satır bazlı okunmalı).

---

## 5. Kenar durumlar (§5/§6.4)

### 5.1 Fail-open/fail-closed matrisi

| Durum | Yanlış davranış (YASAK) | Doğru davranış |
|---|---|---|
| APCu yok/devre dışı | geç | **503** (fail-closed — §1.4) |
| `apcu_inc` `false` | geç | 429/503 |
| Nonce üretilemiyor (`random_bytes` throw) | `unsafe-inline` ekle | **500 + log** (fail-closed) |
| report-only rapor gelmiyor | enforce'e geç | kal + **⚠️ VERIFICATION REQUIRED** (ADT-009 log mekaniği) |

### 5.2 Origin/CSRF katmanları
Rate limit + CSP **CSRF yerine geçmez** (başka savunma halkaları — §6.4);
`/api/search` GET + stateless → CSRF'siz (READ_ONLY_EXIT §7.4 zorunlu GET
listesi); state-changing uç nokta → CSRF walkthrough'a dön.

### 5.3 report-only → enforce (ADT-009, `.ai/.rules` skill anlatımı)

1. `Content-Security-Policy-Report-Only` + `report-uri /csp-report`
   (report endpoint'i **⚠️ VERIFICATION REQUIRED** — var mı vault'ta yok).
2. N gün gözlem (console + rapor) → **0 gerçek ihlal** (eklenti/kalan inline).
3. Aynı policy'yi `Content-Security-Policy` başlığına al; report-only'yı kaldır.
4. **Tek satır** göze batarsa: `unsafe-inline` ekleme → kaynağı nonce'la
   (inline) veya dosyaya taşı (`'self'`).

---

## 6. Doğrulama (§3 — ölçütler)

| # | Ölçüt | Nasıl | Beklenen |
|---|---|---|---|
| 1 | `php -l` | ilgili dosyalar | No syntax errors |
| 2 | 61. istek | `1..61 ×` curl (hızlı döngü) | 1-60 → 200; **61 → 429** |
| 3 | Retry-After | `-i` ile başlık oku | `Retry-After: 60` |
| 4 | Pencere sıfırlama | 60 sn bekle → 1 istek | 200 (yeni pencere) |
| 5 | CSP başlığı | `curl -I` | `script-src 'self' 'nonce-<hex>'` — `unsafe-inline` YOK |
| 6 | Nonce değişimi | 2 istek → 2 başlık | **farklı nonce** (istek-başına) |
| 7 | Nonce'suz script | nonce'suz `<script>` enjekte et (test sayfası) | **engellendi** (console) |
| 8 | Zincir | grep `csp_nonce` | 4 dosya (SecurityHeaders/Session/Csrf/Controller) — §SKILL 6.2 |
| 9 | Pipeline sırası | okuma | RateLimiter 3/10, SecurityHeaders 4/10 |
| 10 | Log | 429 üret | istek/kimlik log satırı (⚠️ event adı) |

**Hızlı komut (2/3):**
```powershell
1..61 | ForEach-Object {
  $r = Invoke-WebRequest -Uri 'https://coremusic.net/api/search?q=a' -SkipHttpErrorCheck
  if ($r.StatusCode -eq 429) { "429 at request $_ — Retry-After: $($r.Headers['Retry-After'])"; break }
}
```

---

## 7. Kapanış (§4/§7.5)

1. **Rapor:** `.ai/reports/security/[tarih]-security-audit.md` —
   2 bulgu satırı (rate limit durumu, CSP durumu) + OWASP 2025 karşılığı
   (A06 Design / A05 Misconfiguration / A03 XSS).
2. **Süreç:** enforce'e geçiş kaydı (ADT-009 — kaç gün report-only).
3. **Geri alma:** `MiddlewarePipeline.php` sırası değiştiyse §6 (🔴) öncesi;
   yeni middleware eklendiyse ADR taslağı (≥088 — §7.4) ekle/kal-dir notu.
4. **Onay:** §6 Devre Dışı Bırakma — rate limit **asla** prod'da kapatılmaz
   (kural 5); CSP kapatma yalnız report-only döngüsüne dönmek (anlık enforce
   kaldırma = XSS penceresi).

**Bitti mi?** 10 ölçüt ✓ → evet. Değilse §1 gözlemine dön; yeni dosya/
bağımlılık eklediysen §7.4 (onay/ADR) kapılarını ayrıca kapat.
