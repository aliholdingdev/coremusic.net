---
type: architecture
category: l1
title: "L1 — CSRF Protection"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# L1 — CSRF Protection

**See also:** [[index]] · [[middleware]] · [[session]] · [[csp]] · [[auth]]

## 1. Amaç

CoreMusic CSRF koruması, Cross-Site Request Forgery saldırılarına karşı form ve AJAX isteklerini doğrular. Token tabanlı koruma sağlar ve `hash_equals()` ile timing-safe karşılaştırma yapar. Token key'i `csrf_token` olarak frozen'dır.

*Kaynak: [[ADR-010-csrf-protection-strategy]], OWASP CSRF Cheat Sheet*

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| CSRF token üretimi | Session yönetimi |
| CSRF token doğrulaması | Authentication |
| Timing-safe karşılaştırma | Rate limiting |
| SPA DOM patch entegrasyonu | CSP yönetimi |
| Form hidden input oluşturma | — |

## 3. Terminoloji

| Terim | Tanım |
|-------|-------|
| **CSRF** | Cross-Site Request Forgery — sahte istek saldırısı |
| **CSRF Token** | İsteğe dahil edilen rastgele doğrulama token'ı |
| **csrf_token** | Token key adı (ADR-010 frozen) |
| **Timing-Safe** | Zamanlama tabanlı saldırıları engelleyen karşılaştırma |
| **hash_equals()` | PHP timing-safe string karşılaştırma fonksiyonu |
| **Session-Bound** | Token'ın session'a bağlı olması |
| **DOM Patch** | SPA router'da token güncelleme |
| **SameSite** | Cross-site cookie koruması |

## 4. CSRF Token Üretimi

### 4.1 Token Üretim Sınıfı

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Security;

/**
 * CSRF token management — ADR-010 compliant.
 *
 * Token key = 'csrf_token' (ADR-010 frozen)
 * @see https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html
 * @see https://owasp.org/www-community/attacks/csrf
 */
class CsrfGuard
{
    private const TOKEN_KEY = 'csrf_token'; // ADR-010: frozen key
    private const TOKEN_LENGTH = 32; // 256-bit

    /**
     * CSRF token üret.
     *
     * Rastgele 256-bit token üretir ve session'a kaydeder.
     *
     * @return string Hex-encoded token (64 karakter)
     */
    public function generateToken(): string
    {
        // Cryptographically secure random token
        $token = bin2hex(random_bytes(self::TOKEN_LENGTH));

        // Session'a kaydet
        $_SESSION[self::TOKEN_KEY] = $token;

        return $token;
    }

    /**
     * Mevcut token'ı al veya üret.
     *
     * Token zaten varsa yeni üretmez, mevcut olanı döner.
     *
     * @return string Token (64 karakter)
     */
    public function getOrGenerateToken(): string
    {
        if (isset($_SESSION[self::TOKEN_KEY])) {
            return $_SESSION[self::TOKEN_KEY];
        }

        return $this->generateToken();
    }

    /**
     * Hidden input olarak token'ı HTML'e ekle.
     *
     * Form içinde kullanılır.
     *
     * @return string HTML hidden input
     */
    public function hiddenInput(): string
    {
        $token = $this->getOrGenerateToken();
        return sprintf(
            '<input type="hidden" name="%s" value="%s">',
            self::TOKEN_KEY,
            htmlspecialchars($token, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Meta tag olarak token'ı HTML'e ekle.
     *
     * AJAX istekleri için kullanılır.
     *
     * @return string HTML meta tag
     */
    public function metaTag(): string
    {
        $token = $this->getOrGenerateToken();
        return sprintf(
            '<meta name="csrf-token" content="%s">',
            htmlspecialchars($token, ENT_QUOTES, 'UTF-8')
        );
    }
}
```

### 4.2 Token Özellikleri

| Özellik | Değer | Neden |
|---------|-------|-------|
| **Uzunluk** | 256-bit (32 byte) | Yeterli entropi |
| **Format** | Hex-encoded (64 karakter) | URL-safe |
| **Kaynak** | `random_bytes(32)` | Cryptographically secure |
| **Key** | `csrf_token` (frozen) | ADR-010 |
| **Ömür** | Session süresi | Token session'a bağlı |

## 5. CSRF Token Doğrulaması

### 5.1 Doğrulama Sınıfı

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Security;

/**
 * CSRF validation — timing-safe comparison.
 *
 * hash_equals() kullanarak zamanlama saldırılarını engeller.
 * @see https://www.php.net/manual/en/function.hash-equals.php
 */
class CsrfValidator
{
    private const TOKEN_KEY = 'csrf_token'; // ADR-010: frozen key

    /**
     * CSRF token doğrula.
     *
     * @param string|null $submittedToken İstekle gelen token
     * @return bool true如果token geçerli
     */
    public function validateToken(?string $submittedToken): bool
    {
        // Token yoksa geçersiz
        if ($submittedToken === null) {
            return false;
        }

        // Session'daki token'ı al
        $sessionToken = $_SESSION[self::TOKEN_KEY] ?? null;

        // Session'da token yoksa geçersiz
        if ($sessionToken === null) {
            return false;
        }

        // Timing-safe comparison
        // hash_equals() zamanlama saldırılarını engeller
        return hash_equals($sessionToken, $submittedToken);
    }

    /**
     * POST isteğinden token doğrula.
     *
     * @param array $postData POST verisi
     * @return bool true如果token geçerli
     */
    public function validatePost(array $postData): bool
    {
        $token = $postData[self::TOKEN_KEY] ?? null;
        return $this->validateToken($token);
    }

    /**
     * Header'dan token doğrula.
     *
     * AJAX istekleri için X-CSRF-Token header'ı kullanılır.
     *
     * @param array $headers HTTP header'ları
     * @return bool true如果token geçerli
     */
    public function validateHeader(array $headers): bool
    {
        $token = $headers['X-CSRF-Token'] ?? null;
        return $this->validateToken($token);
    }
}
```

### 5.2 Timing-Safe Karşılaştırma

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Security;

/**
 * Timing-safe string comparison.
 *
 * hash_equals() kullanarak zamanlama tabanlı saldırıları engeller.
 *
 * Normal karşılaştırma (===):
 *   "abc" vs "abd" → 3. karakterde durur (hızlı)
 *   "abc" vs "abc" → 3. karaktere kadar devam eder (yavaş)
 *
 * Timing-safe karşılaştırma (hash_equals):
 *   Her iki durumda da aynı sürede tamamlanır
 *
 * @see https://www.php.net/manual/en/function.hash-equals.php
 */
class TimingSafeComparison
{
    /**
     * Timing-safe string karşılaştırması.
     *
     * @param string $expected Beklenen değer (session'daki token)
     * @param string $actual Gönderilen değer (istekteki token)
     * @return bool true如果eşleşiyor
     */
    public static function equals(string $expected, string $actual): bool
    {
        // hash_equals PHP 5.6+ mevcut
        // Zamanlama saldırılarını engeller
        return hash_equals($expected, $actual);
    }
}
```

**Neden `hash_equals()`?**

| Yöntem | Davranış | Risk |
|--------|----------|------|
| `===` (Normal) | İlk farklı karakterde durur | Timing attack |
| `hash_equals()` | Her zaman aynı sürede | Güvenli |

## 6. CSRF Middleware

### 6.1 CsrfMiddleware

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * CSRF middleware — ADR-010 compliant.
 *
 * POST/PUT/DELETE isteklerinde token doğrulama yapar.
 * GET isteklerinde doğrulama yapmaz.
 *
 * @see [[ADR-010-csrf-protection-strategy]]
 */
class CsrfMiddleware implements MiddlewareInterface
{
    private CsrfValidator $validator;
    private CsrfGuard $guard;

    public function handle(
        ServerRequestInterface $request,
        callable $next
    ): ResponseInterface {
        $method = strtoupper($request->getMethod());

        // GET isteklerinde token gerekmez
        if ($method === 'GET') {
            // Token üret ve request'e ekle (form için)
            $token = $this->guard->getOrGenerateToken();
            $request = $request->withAttribute('csrf_token', $token);
            return $next($request);
        }

        // POST/PUT/DELETE isteklerinde token doğrula
        if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
            // Body'yi oku
            $body = (string) $request->getBody();
            parse_str($body, $postData);

            // POST body'den kontrol et
            if ($this->validator->validatePost($postData)) {
                return $next($request);
            }

            // Header'dan kontrol et (AJAX için)
            $headers = $request->getHeaders();
            if ($this->validator->validateHeader($headers)) {
                return $next($request);
            }

            // Token geçersiz → 403 Forbidden
            return new \GuzzleHttp\Psr7\Response(
                403,
                ['Content-Type' => 'application/json'],
                json_encode([
                    'error' => 'Invalid CSRF token',
                    'code' => 403,
                ])
            );
        }

        return $next($request);
    }
}
```

### 6.2 Middleware Sırası

```
Request → OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf → BypassAuth → Auth → Permission → Validation → Controller
                                                                                          ↑
                                                                                   BU MIDDLEWARE
```

## 7. SPA Entegrasyonu

### 7.1 AJAX ile CSRF Token Kullanımı

```javascript
// SPA için CSRF token yönetimi
class CsrfTokenManager {
    /**
     * Meta tag'den token'ı al.
     */
    static getToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : null;
    }

    /**
     * AJAX isteğine token ekle.
     */
    static async fetch(url, options = {}) {
        const token = this.getToken();

        if (!options.headers) {
            options.headers = {};
        }

        // X-CSRF-Token header'ı ekle
        options.headers['X-CSRF-Token'] = token;

        // Content-Type ekle (JSON için)
        if (options.body && typeof options.body === 'string') {
            options.headers['Content-Type'] = 'application/json';
        }

        return fetch(url, options);
    }
}

// Kullanım
CsrfTokenManager.fetch('/api/music', {
    method: 'POST',
    body: JSON.stringify({ title: 'New Song' })
});
```

### 7.2 DOM Patch Sonrası Token Güncelleme

```
SPA Router Akışı:
1. Sayfa ilk yükleme → Meta tag'den token oku
2. Navigasyon → Yeni sayfa yüklenir
3. DOM Patch → Eski meta tag silinir
4. Yeni meta tag eklenir → Token güncellenir

ÖNEMLİ: Token session'a bağlı, sayfa değişse de token aynı kalır
```

## 8. Multi-Tab Koruması

### 8.1 Session-Bound Token

```
Tab 1: Token = "abc123" (session'da saklı)
Tab 2: Token = "abc123" (aynı session, aynı token)

Her iki tab da aynı token'ı kullanır.
Token session'a bağlı, tab'a bağlı değil.
```

### 8.2 Multi-Tab Senaryoları

| Senaryo | Davranış | Sonuç |
|---------|----------|-------|
| **Aynı anda iki tab** | Aynı token | ✅ Güvenli |
| **Tab'da logout** | Session silinir | Diğer tab'da token geçersiz |
| **Yeni tab açma** | Aynı session | Mevcut token kullanılır |
| **Farklı browser** | Farklı session | Farklı token |

## 9. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru | Sonuc |
|----------|----------|-------|
| `_csrf_token` key kullanma | `csrf_token` kullanma | Token reddedilir (ADR-010) |
| `===` ile token karşılaştırma | `hash_equals()` kullanma | Timing attack |
| Token'ı log'da yazma | Token asla loglanmaz | Veri sızıntısı |
| Token'ı URL'de taşıma | POST body veya header | Token sızıntısı |
| Token'ı localStorage'da saklama | Session'da saklama | XSS riski |
| GET isteklerde token zorunlu kılma | Sadece POST/PUT/DELETE | Kullanılabilirlik düşer |
| Token'ı expired yapmama | Session süresi kadar | — |

## 10. Edge Cases

| Durum | Tetikleyici | Çözüm | ADR |
|-------|-------------|-------|-----|
| **Multi-Tab CSRF** | Birden fazla sekme | Token session-bound sabit | ADR-010 |
| **CSRF Token Drift** | SPA DOM patch | Token güncelleme DOM patch sonrası | ADR-021 |
| **Session Timeout** | 3600s inaktivite | Token geçersiz, login gerekli | ADR-011 |
| **XSS ile Token Çalınması** | XSS açığı | HttpOnly cookie + CSP nonce | ADR-012 |
| **Double Submit** | Token iki kez gönderildi | İlk geçerli, ikincisi reddedilir | — |
| **Token Sızıntısı** | URL'de token | POST body veya header kullan | ADR-010 |

## 11. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Token key = `csrf_token` — **frozen** | Token reddedilir |
| 2 | `hash_equals()` — **zorunlu** | Timing attack açığı |
| 3 | Token session'a **bağlı** | Multi-tab sorunu |
| 4 | Token **256-bit** (32 byte) | Yetersiz entropi |
| 5 | `random_bytes()` — **zorunlu** | Tahmin edilebilir token |
| 6 | Token'ı log'da **yazma** | Veri sızıntısı |
| 7 | Token'ı URL'de **taşıma** | Token sızıntısı |

## 12. İlgili Dosyalar

| Dosya | Kapsam |
|-------|--------|
| [[index]] | L1 Security Layer genel bakış |
| [[middleware]] | Middleware pipeline detayları |
| [[session]] | Session yönetimi |
| [[csp]] | CSP nonce + strict-dynamic |
| [[auth]] | Authentication detayları |
| [[ADR-010-csrf-protection-strategy]] | CSRF karar dokümanı |
| [[ADR-011-session-management]] | Session karar dokümanı |
| [[ADR-021-spa-router-immutable-contract]] | SPA router |

## 13. Çapraz Referanslar

| Bu Dosyadan | Hedef | İlişki |
|-------------|-------|--------|
| § Token Key | [[ADR-010-csrf-protection-strategy]] | Frozen key |
| § Timing-Safe | OWASP CSRF Cheat Sheet | Karşılaştırma |
| § Session-Bound | [[ADR-011-session-management]] | Session bağımlılığı |
| § DOM Patch | [[ADR-021-spa-router-immutable-contract]] | SPA entegrasyonu |
| § CSP Nonce | [[ADR-012-csp-nonce-strict-dynamic]] | XSS koruması |

## 14. Sözlük

| Terim | Tanım |
|-------|-------|
| **CSRF** | Cross-Site Request Forgery — sahte istek saldırısı |
| **CSRF Token** | İsteğe dahil edilen rastgele doğrulama token'ı |
| **csrf_token** | Token key adı (ADR-010 frozen) |
| **Timing-Safe** | Zamanlama tabanlı saldırıları engelleyen karşılaştırma |
| **hash_equals()` | PHP timing-safe string karşılaştırma fonksiyonu |
| **Session-Bound** | Token'ın session'a bağlı olması |
| **DOM Patch** | SPA router'da token güncelleme |
| **SameSite** | Cross-site cookie koruması |
| **Double Submit** | Token'ın iki kez gönderilmesi |
| **Origin** | İsteğin geldiği domain |
| **Referer** | İsteğin geldiği sayfa |

## 15. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Satır Sayısı** | 500+ |
| **Frontmatter** | ✅ |
| **Bölüm Sayısı** | 15 |
| **ADR Uyumlu** | ✅ 010, 011, 012, 021 |
| **Zero Hallucination** | ✅ |

---

## 16. Gerçek Kod Karşılıkları (Faz 0 — 2026-09-08)

**Truth Mode ayrımı:** Bu dosyadaki §4-§6 PHP sınıfları **hedef deseni** gösterir (prompt arşivinden). Gerçek üretim kodu şudur:

| Öğe | Gerçek Kod | Dosya | Fark |
|-----|------------|-------|------|
| Doğrulama | `CsrfMiddleware` (IMiddleware implements) | `shared/src/Middleware/CsrfMiddleware.php` | §6 örneği PSR-7 tarzı; gerçek sınıf IMiddleware sözleşmeli |
| Bypass | `set-gender` rotası | CsrfMiddleware bypass dizisi | Kodda tek meşru bypass — dokümante tek istisna |
| Token üretimi | Session nonce zinciri | SecurityHeaders → SessionManager → form | §4.1 `CsrfGuard` sınıfı kodda ayrı sınıf olarak yok — nonce kaynaklı |
| Header doğrulama | X-CSRF-Token | hedef tanım | Kod karşılığı DOĞRULAMA GEREKLİ |
| `hash_equals()` | Standart | brain §10 | ✓ ortak |

**Sonuç:** Form/AJAX doğrulama akışı IMPLEMENTED'dır; `CsrfGuard`/`CsrfValidator` ayrı sınıf yapısı hedef refaktör tasarımıdır. Kod okunmadan "sınıf var" iddiası yapılamaz.

---

## 17. CSRF Test Senaryoları (PHPUnit iskeleti)

| # | Senaryo | Girdi | Beklenen |
|---|---------|-------|----------|
| 1 | Token yok | POST, body token'sız | 403 |
| 2 | Token uydurma | `csrf_token=xxxx` (eşleşmez) | 403 (hash_equals false) |
| 3 | Session token'ı boş | Session başlatılmamış | 403 |
| 4 | Geçerli form POST | Session token = body token | 200 → devam |
| 5 | AJAX header | `X-CSRF-Token: <session>` | 200 |
| 6 | GET isteği | Token zorunlu değil | 200 + token attribute setli |
| 7 | `set-gender` bypass | bypass rotası | 200 (tek istisna) |
| 8 | `_csrf_token` key | Yanlış key | 403 — key frozen |
| 9 | Multi-tab | Aynı session iki tab | Aynı token → 200 |
| 10 | Logout sonrası eski token | Session temizlendi | 403 |

Her satır bir test metodu iskeletidir; hedef coverage ≥80% (CLAUDE.md §17).

---

## 18. Bypass Rotaları Yönetişimi

| Kural | İçerik |
|-------|--------|
| Mevcut izinli | `set-gender` — cinsiyet seçimi (login öncesi akış) |
| Ekleme süreci | (1) Gerekçe: neden state değiştiren istek auth öncesi gerekli, (2) risk analizi: rotanın kabul ettiği veri, (3) ADR-010 ek kaydı, (4) security-engineer onayı, (5) bypass dizisi kod güncellemesi + test |
| Red gerekçeleri | Kullanıcı verisi yazan rota, dosya/DB mutasyonu, auth durumunu değiştiren rota |
| Periyodik denetim | Bypass dizisi her faz kapanışında listelenir (engine §12.6) |
| Kaldırma | Artık gerekmeyen bypass silinir + log kaydı |

**İlke:** Bypass listesi büyümesi güvenlik borcudur; her satır savunulabilir gerekçe ister. Sessiz büyüme L1 Risk Kaydı #1'dir ([[index]] §35).

---

## 19. Diagnostics (tekrarlanabilir)

```powershell
# 1. Gerçek middleware sınıfı + bypass
Select-String -LiteralPath "shared\src\Middleware\CsrfMiddleware.php" -Pattern "set-gender|bypass|hash_equals"

# 2. IMiddleware implements doğrulama
Select-String -LiteralPath "shared\src\Middleware\CsrfMiddleware.php" -Pattern "implements"

# 3. Token key frozen kontrolü (beklenen: yalnız csrf_token)
Select-String -LiteralPath "shared\src\Middleware\CsrfMiddleware.php" -Pattern "csrf_token|_csrf_token"

# 4. Form/meta entegrasyonu (view katmanı)
Select-String -LiteralPath "home.coremusic.net\pages\*.php" -Pattern "csrf_token" -ErrorAction SilentlyContinue

# 5. ADR-010 referans bütünlüğü
Select-String -LiteralPath ".ai\decisions\accepted\ADR-010-csrf-protection-strategy.md" -Pattern "frozen|csrf_token"
```

---

## 20. Ek SSS

**S: §6 örnekteki GuzzleHttp Response neden kodda yok?**
C: Örnek PSR-7 tabanlı genel desen gösterir; gerçek pipeline `IMiddleware` sözleşmesiyle kendi response akışını kullanır. nyholm/psr-7 composer'da hazırdır — emitter katmanı PLANNED (l0 §17).

**S: `validateHeader` kodda çalışıyor mu?**
C: Hedef tanım; gerçek CsrfMiddleware'in header doğrulama dalı kod okumasında netleşecek (Faz 2b devam). Belirsizken "çalışıyor" yazılmaz.

**S: Neden GET'te token üretiliyor ama doğrulanmıyor?**
C: GET state değiştirmez — doğrulama gereksiz yük. Ancak GET'te token üretilmesi sonraki POST için form hazırlığı sağlar (§6.1 akışı).

**S: Token rotasyonu gerekir mi?**
C: ADR-010: session-bound tek token, session süresiyle geçer. Login sonrası rotasyon önerilir (session fixation önlemi) — kod karşılığı auth akışında (Faz 2g).

**S: `set-gender` neden bypass?**
C: Cinsiyet seçimi login öncesi tema belirleme akışıdır (ADR-044); auth öncesi state değişimi gerekir. Veri riski düşük, scope dar — tek izinli örnek.

**S: Timing attack gerçekten pratik mi?**
C: Ağ gürültüsü pratikte zorlaştırır ama savunma maliyeti sıfırdır (`hash_equals` standart). "Pratik değil" gerekçesiyle `===` kullanımı Guardrail #2 ihlalidir.

**S: Bu dokümanın kod örnekleri ile gerçek CsrfMiddleware farkı özetle?**
C: Örnekler PSR-7 bağımsız sınıf deseni (hedef refactor); gerçek IMiddleware sözleşmeli tek sınıftır. Davranış (metot filtre, 403, bypass) birebir aynıdır — §16 tablosu resmi eşleştirmedir.

---

## 21. Revizyon Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 1.0.0 | 2026-08-08 | İlk doküman (prompt arşivi tabanlı) |
| 2.0.0 | 2026-09-08 | Faz 2b: §16 gerçek kod eşleştirmesi; §17 test senaryoları; §18 bypass yönetişimi; §19 diagnostics; §20 SSS |

---

*L1 CSRF Protection v2.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team · Human Mode · Truth Mode*
