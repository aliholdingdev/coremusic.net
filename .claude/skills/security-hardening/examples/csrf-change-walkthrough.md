# Yürüyüş (Walkthrough): CSRF Token'ı Bir Uç Noktaya Ekleme

> Skill: `security-hardening` v4.0.0 · CoreMusic · Güncel: 2026-10-21
> Referans: ADR-010 (frozen) · §1.1 düzeltmesi 2026-10-14: `csrf_token`
> (artı `_csrf_token`→`csrf_token`) · `.ai/CLAUDE.md` §6 pipeline
> (Csrf 7/10, BypassAuth 8/10, Auth 9/10, Validation 10/10).
> **Bu dosya öğretim amaçlıdır — gerçek kodda DEĞİŞİKLİK yapmadan önce
> §7 Devre Dışı Bırakma ve §6 Onay kapılarını uygula.**

---

## 0. Senaryo

`POST /settings/profile` (email/parola güncelleme) —
korunmasız state-changing uç nokta. Hedef: sunucu tarafı synchronizer token
+ sıfır-yeniden-yaratım (idempotent read-only) + doğrulama akışı ADR-010 §2.

---

## 1. Gözlemle (kod yok, henüz — read-only)

```powershell
# 1. Uç nokta + mevcut koruma kontrolü
Get-ChildItem -Recurse -Include *.php -Path .\src,.\app,.\public |
  Select-String -Pattern 'settings/profile|profile.*save' | Select-Object Path,LineNumber,Line

# 2. CSRF koruması var mı? (token adı — ADR-010 §1.1 ihlali kalıntısı?)
Get-ChildItem -Recurse -Include *.php |
  Select-String -Pattern '_csrf_token|csrf_token|X-CSRF-Token' |
  Select-Object Path,LineNumber,Line

# 3. Pipeline'ı ZORUNLU oku (silme yok — kural: subagent pipeline'dan
#    middleware SİLMEZ; §7.4)
Get-Content .\shared\src\Middleware\MiddlewarePipeline.php

# 4. Formda token var mı (view)?
Get-ChildItem -Recurse -Include *.php,*.html,*.twig |
  Select-String -Pattern '<form' -Context 0,6 | Where-Object {$_.Line -match 'profile'}
```

**Çıktı kaydı:** hangi dosya/satırda `POST` işleniyor; Csrf middleware'i
pipeline'da 7/10'da mı (§3 kontrol listesi); view'da `csrf_token` hidden input
var mı; mevcut token adı `csrf_token` mı (`_csrf_token` → 2026-10-14 ihlali).

**STOP kapısı #1:** Csrf middleware'i pipeline'da yoksa (veya 7/10'dan
farklı sırada) → **önce sırayı kur** (references/middleware-security-order.md
§2 — sıra değişikliği ADR gerektirir), sonra token akışına geç.

---

## 2. Tasarım (ADR-010 §2 — kararlar sabit, tartışma yok)

| Soru | Karar | Kaynak |
|---|---|---|
| Token adı | `csrf_token` (POST + `X-CSRF-Token` header) | ADR-010 §1.1 (2026-10-14) |
| Token üretimi | `bin2hex(random_bytes(32))` — 256-bit | ADR-010 §2 |
| Read-only yeniden-yaratım | **idempotent** — session'da varsa DOKUNMA | ADR-010 §2 (76.20-76.30) |
| Regenerate (kural 7) | yalnız **hash-equals BAŞARISI** — output val. öncesi **YOK** | ADR-010 §2 (69.10-69.30) |
| Doğrulama | `hash_equals()` (kural 11) | ADR-010 §2 |
| Eksik token | 400 | ADR-010 §2 (60.20) |
| Başarısız | 403 + `bootstrap_engineering.security.<event>` log | ADR-010 §2 (64.20) |
| Süre | ≤15 dakika — aşıldıysa sil + 403 | ADR-010 §2 (66.10-66.30) |
| Basitleştirilmiş | **double-submit** (session olmadığı uç noktalar) | §SKILL 6.3 |

**Devre dışı bırakma (§7 — ölü kod/flush riski):** akışı dene; gerekirse
`const CSRF_ENFORCED = true` sabiti + log satırıyla aç/kapa (true = enforce;
`false` yalnız development). ADT-012 örneğindeki `-1` output kuralı:
`readToken()`'da `session_regenerate_id()` **yok** — read-only'de regen yasak.

---

## 3. Pipeline (§2 — 4 dosya sırayla; sıra değiştirme yok)

```
1/10 OriginCheck  → Origin farklıysa 403 (ADR-035 §2)   [varsa — §5.2]
2/10 Cors         → whitelist + OPTIONS                 [ADR-009 §2]
3/10 RateLimiter  → 60/60s APCu (ADR-013 §2)            [varsa]
4/10 SecurityHeaders → nonce üret (ADR-012 §2)           [varsa — §5.3]
5/10 SessionManager → $_SESSION['csrf_token'] hazır      [zorunlu — §4.1]
6/10 ...          → (Session 6/10 — CSRF'den ÖNCE oturum)
7/10 Csrf         → bu görevin KALBI                    [§4.1]
8/10 BypassAuth   → prod'da bypass yok (kural 13)       [§5.1]
9/10 Auth         → oturum doğrulama                    [§5.1]
10/10 Validation  → input doğrulama (controller ÖNCESİ)  [§5.1]
```

**Yasak:** Csrf'yi Auth'tan ÖNCEYE almak (session yoksa token doğrulanamaz);
BypassAuth'ı Csrf ÖNCESİNE almak (bypass token'ı by-pass eder — ADR-055 §1);
middleware'i controller'a taşımak (kural 26).

---

## 4. Uygulama (kod — anlatım; gerçek dosya yolları §1 gözleminden gelir)

### 4.1 Csrf middleware (7/10)

```php
declare(strict_types=1);

namespace Shared\Middleware;

use Shared\Middleware\MiddlewarePipeline as Pipeline;

final class Csrf implements MiddlewareInterface
{
    private const MAX_AGE_SECONDS = 900;             // ≤15 dk (ADR-010 §2)

    public function handle(Request $request, Pipeline $next): Response
    {
        $method = strtoupper($request->getMethod());
        if (in_array($method, ['GET', 'HEAD', 'OPTIONS'], true)) {
            $this->writeToken();                     // idempotent — ADR-010 §2
            return $next($request);
        }

        $token = $this->readToken();                 // POST csrf_token | X-CSRF-Token
        if ($token === null) {
            return new Response('Missing CSRF token', 400);   // §2 — 400
        }

        $expected = $_SESSION['csrf_token'] ?? '';
        if ($expected === '' || !hash_equals($expected, $token)) {
            $this->logEvent('csrf_rejected');        // bootstrap_engineering.security.<event>
            return new Response('CSRF validation failed', 403);  // §2 — 403
        }

        $age = time() - ($_SESSION['csrf_issued_at'] ?? 0);
        if ($age > self::MAX_AGE_SECONDS) {
            unset($_SESSION['csrf_token'], $_SESSION['csrf_issued_at']);
            $this->logEvent('csrf_expired');
            return new Response('CSRF token expired', 403);      // §2 — süre
        }

        // hash-equals BAŞARILI → output val. — session_regenerate_id() YOK
        return $next($request);
    }

    private function writeToken(): void
    {
        if (!isset($_SESSION['csrf_token'])) {                    // idempotent
            $_SESSION['csrf_token']      = bin2hex(random_bytes(32));
            $_SESSION['csrf_issued_at']  = time();
        }
    }

    private function readToken(): ?string
    {
        $t = $_POST['csrf_token']
           ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
        return is_string($t) ? $t : null;
    }
}
```

### 4.2 Form / istemci

```php
<!-- view: hidden input — output val., charset öncesi değil, htmlspecialchars -->
<input type="hidden" name="csrf_token"
       value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">

<!-- JS fetch (header kanalı — çift-kanal: S44 2026-10-14) -->
<script nonce="<?= $cspNonce ?>">
  fetch('/settings/profile', {
    method: 'POST',
    headers: {'X-CSRF-Token': document.querySelector('[name=csrf_token]').value},
    body: new URLSearchParams(new FormData(document.querySelector('form')))
  });
</script>
```

### 4.3 Controller (10/10 — Validation ÖNCE pipeline, controller'da YOK)

```php
// YASAK (kural 26): controller'da $this->csrf->check()  — Csrf zaten 7/10'da
// DOĞRU: controller yalnız işini yapar
public function save(Request $r): Response {
    $email = filter_var($r->post('email'), FILTER_VALIDATE_EMAIL);
    if ($email === false) { return new Response('Bad email', 422); }  // Validation
    // ... güncelle
}
```

---

## 5. Kenar durumlar (§6.3/§6.4)

### 5.1 Oturum yoksa / API
- Session yoksa double-submit (§6.3): token cookie'de (HttpOnly DEĞİL —
  JS okuyacak), header ile gönderilir; `hash_equals` aynı değeri.
- Auth (9/10) zaten oturumu doğrular; Csrf (7/10) salt token — ikisi farklı
  görev (CSRF ≠ auth).

### 5.2 Origin/Referer (ek savunma — §6.1)
`OriginCheck` 1/10'da: Origin var ve whitelist dışındaysa **403** (ADR-035 §2);
Origin yoksa §forbidden-patterns §1.4 sırası (CSRF token + Fetch Metadata).
`Sec-Fetch-Site: cross-site` reddi — **⚠️ VERIFICATION REQUIRED**
(ADR-035/OriginCheck kodu bu oturumda okunmadı).

### 5.3 CSP (ADR-012 §1 — `unsafe-inline` yasağı)
Formdaki inline `<script nonce>` **zorunlu**; nonce 4/10 SecurityHeaders'da
üretilir (§SKILL 6.2 — 2. kez üretilirse Controller 8/10'a bak).

### 5.4 Rate limit (ADR-013)
POST `/settings/profile` kimlik-kestrilemez; global 60/60s (§SKILL 6.3).
Login'e özel eşik **⚠️ VERIFICATION REQUIRED** (ADR-013'te ayrı limit yok).

---

## 6. Doğrulama (§3 — ölçütler; her biri → check)

| # | Ölçüt | Nasıl | Beklenen |
|---|---|---|---|
| 1 | `php -l` 3 dosya | `php -l <file>` ×3 | `No syntax errors` |
| 2 | GET sonrası token | tarayıcı → view source | hidden `csrf_token` dolu (idempotent — 2. GET'te aynı değer) |
| 3 | Token'sız POST | `curl -X POST ... -d "a=b"` | **400** |
| 4 | Yanlış token | `-d "csrf_token=deadbeef"` | **403** + log satırı |
| 5 | Doğru token | gerçek token ile | **200/302** (işlem) |
| 6 | Eski token (16 dk) | token'ı bekle/süre modifiye | **403** + session'dan silindi |
| 7 | Header kanalı | `-H "X-CSRF-Token: <doğru>"` (body'siz) | 200 — çift-kanal çalışıyor |
| 8 | GET'te regen | GET sonrası tekrar GET | token **değişmez** (idempotent — ADT-012 `-1` yok) |
| 9 | Origin farklı | `-H "Origin: https://evil.example"` | **403** (OriginCheck 1/10) |
| 10 | Pipeline sırası | `MiddlewarePipeline.php` okuma | Csrf 7/10, BypassAuth 8/10 |

**Test komutu örneği (4/5):**
```powershell
$login = Invoke-WebRequest -Uri 'https://coremusic.net/login' -SessionVariable s
$token = ([regex]::Match($login.Content,'name="csrf_token" value="([^"]+)"')).Groups[1].Value
Invoke-WebRequest -Uri 'https://coremusic.net/settings/profile' -WebSession $s `
  -Method POST -Body @{csrf_token=$token; email='user@example.com'}   # → 200/302
Invoke-WebRequest -Uri 'https://coremusic.net/settings/profile' -WebSession $s `
  -Method POST -Body @{csrf_token='x'; email='user@example.com'}      # → 403
```

---

## 7. Kapanış (§4/§7.5)

1. **Rapor:** `.ai/reports/security/[tarih]-security-audit.md` — §bulgu,
   severity (CSRF eksik = high/A01-A08), fix owner `backend-architect`
   (rule 16 — bu yürüyüşte kodu security YAZMAZ).
2. **Log doğrulama:** 4/5/6 testlerinde `bootstrap_engineering.security.<event>`
   satırı görünmeli (ADR-010 §2).
3. **Geri alma:** pipeline sırası değiştiyse §6 (🔴) öncesi hâline döndür +
   ADR notu.
4. **Onay:** §6 Devre Dışı Bırakma — geliştirmede `CSRF_ENFORCED=false`
   ile geçici kapama; **production'da daima true** (rule 13 zihniyeti:
   bypass asla prod).

**Bitti mi?** 10 ölçütün 10'u ✓ → evet. Aksi: §1'e dön, gözlem kaydını
güncelle (kör düzeltme yasak — kural 3/4).
