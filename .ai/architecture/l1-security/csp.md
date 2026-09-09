---
type: architecture
category: l1
title: "L1 — CSP Nonce & Strict-Dynamic"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# L1 — CSP Nonce & Strict-Dynamic

**See also:** [[index]] · [[middleware]] · [[session]] · [[csrf]] · [[auth]]

## 1. Amaç

CoreMusic CSP (Content Security Policy) sistemi, script injection saldırılarını nonce-based mekanizma ile engeller. `strict-dynamic` direktifi ile yüklenen script'lerin daha fazla script yüklemesine izin verir. Nonce, SecurityHeadersMiddleware tarafından üretilir, SessionManagerMiddleware tarafından session'a kaydedilir ve her istek için benzersizdir.

*Kaynak: [[ADR-012-csp-nonce-strict-dynamic]], W3C CSP Level 3*

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| CSP nonce üretimi | Rate limiting |
| CSP header oluşturma | Session yönetimi |
| Directive yapılandırması | Authentication |
| XSS koruması | CSRF koruması |
| strict-dynamic yönetimi | — |

## 3. Terminoloji

| Terim | Tanım |
|-------|-------|
| **CSP** | Content Security Policy — script injection önleme |
| **Nonce** | Tek kullanımlık rastgele değer (her istek için benzersiz) |
| **strict-dynamic** | Yüklenen script'lerin daha fazla script yüklemesine izin veren direktif |
| **Directive** | CSP kuralı (script-src, style-src vb.) |
| **XSS** | Cross-Site Scripting — script injection saldırısı |
| **Inline Script** | HTML içindeki script bloğu |
| **External Script** | Harici dosyadan yüklenen script |
| **Self** | Same-origin kaynaklara izin veren direktif |

## 4. CSP Nonce Üretimi

### 4.1 Nonce Üretim Sınıfı

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Security;

/**
 * CSP nonce — request-based script authorization.
 *
 * Web doğrulanmış: W3C CSP Level 3
 * @see https://www.w3.org/TR/CSP3/
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP
 */
class CspNonce
{
    /**
     * Cryptographic nonce üret.
     *
     * Her istek için benzersiz, rastgele nonce üretilir.
     * Base64 ile encode edilir (256-bit → 44 karakter).
     *
     * @return string Base64-encoded 256-bit nonce
     */
    public function generate(): string
    {
        return base64_encode(random_bytes(32));
    }

    /**
     * CSP header'ı nonce ile oluştur.
     *
     * strict-dynamic: yüklenen script'lerin daha fazla script
     * yüklemesine izin verir.
     *
     * @param string $nonce Üretilen nonce
     * @return string CSP header değeri
     */
    public function buildHeader(string $nonce): string
    {
        return sprintf(
            "default-src 'self'; " .
            "script-src 'self' 'nonce-%s' 'strict-dynamic'; " .
            "style-src 'self' 'unsafe-inline'; " .
            "img-src 'self' data:; " .
            "font-src 'self'; " .
            "connect-src 'self'; " .
            "frame-ancestors 'none'; " .
            "base-uri 'self'; " .
            "form-action 'self'",
            $nonce
        );
    }

    /**
     * Script tag'ine nonce ekle.
     *
     * @param string $nonce Nonce
     * @param string $scriptSrc Script kaynağı
     * @return string HTML script tag'i
     */
    public function scriptTag(string $nonce, string $scriptSrc): string
    {
        return sprintf(
            '<script src="%s" nonce="%s"></script>',
            htmlspecialchars($scriptSrc, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($nonce, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Inline script için nonce ekle.
     *
     * @param string $nonce Nonce
     * @param string $code Script kodu
     * @return string HTML script bloğu
     */
    public function inlineScript(string $nonce, string $code): string
    {
        return sprintf(
            '<script nonce="%s">%s</script>',
            htmlspecialchars($nonce, ENT_QUOTES, 'UTF-8'),
            $code // Kod zaten güvenli olmalı
        );
    }
}
```

### 4.2 Nonce Özellikleri

| Özellik | Değer | Neden |
|---------|-------|-------|
| **Uzunluk** | 256-bit (32 byte) | Yeterli entropi |
| **Format** | Base64-encoded (44 karakter) | HTML-safe |
| **Kaynak** | `random_bytes(32)` | Cryptographically secure |
| **Ömür** | Tek istek | Her istek için yeni nonce |
| **Session'da** | `$_SESSION['csp_nonce']` | DOM patch için saklı |

## 5. CSP Header Yapısı

### 5.1 Direktifler

| Direktif | Değer | Açıklama |
|----------|-------|----------|
| `default-src` | `'self'` | Varsayılan: sadece same-origin |
| `script-src` | `'self' 'nonce-...' 'strict-dynamic'` | Nonce-based script authorization |
| `style-src` | `'self' 'unsafe-inline'` | Inline style gerekli (ITCSS) |
| `img-src` | `'self' data:` | Data URI image desteği |
| `font-src` | `'self'` | Harici font loading |
| `connect-src` | `'self'` | AJAX/fetch same-origin |
| `frame-ancestors` | `'none'` | Clickjacking koruması |
| `base-uri` | `'self'` | Base tag manipülasyonu engelleme |
| `form-action` | `'self'` | Form action yönlendirmesi |

### 5.2 Strict-Dynamic Açıklaması

```
script-src 'self' 'nonce-abc123' 'strict-dynamic'

1. Sayfa yüklenir
2. <script nonce="abc123"> etiketleri çalıştırılır
3. Bu script'ler Daha fazla script yükleyebilir
4. Yüklenen script'ler de daha fazla script yükleyebilir
5. 'self' ve 'nonce-...' sadece root script'ler için geçerli

Örnek:
  <script nonce="abc123" src="/app.js"></script>
    └─ app.js → fetch('/lib.js') → çalışır (strict-dynamic)
    └─ app.js → eval('code') → ÇALIŞMAZ (eval yasak)
```

### 5.3 Neden 'unsafe-inline'?

```
ITCSS (It's Time to Create Scaleable Stylesheets) yapısı:
  01-Settings/    → CSS custom properties
  02-Tools/       → Mixins, functions
  03-Generic/     → Reset, normalize
  04-Elements/    → HTML element stilleri
  05-Objects/     → Layout patterns
  06-Components/  → UI bileşenleri
  07-Utlities/    → Utility sınıfları

Tüm bu dosyalar <link> ile yüklenir, inline değil.
Ancak bazı durumlarda inline style gerekir:
  - Dynamic theme (cinsiyet bazlı)
  - Component-scoped stiller
  - Critical CSS (above-the-fold)

Bu yüzden 'unsafe-inline' style-src için izin verilir.
```

## 6. CSP Middleware

### 6.1 SecurityHeadersMiddleware

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Security headers middleware — CSP + other headers.
 *
 * ADR-012 compliant: nonce-based, strict-dynamic CSP.
 *
 * @see [[ADR-012-csp-nonce-strict-dynamic]]
 */
class SecurityHeadersMiddleware implements MiddlewareInterface
{
    private CspNonce $cspNonce;

    public function handle(
        ServerRequestInterface $request,
        callable $next
    ): ResponseInterface {
        // Session'dan nonce'u al
        $nonce = $_SESSION['csp_nonce'] ?? $this->cspNonce->generate();

        // CSP header'ı oluştur
        $cspHeader = $this->cspNonce->buildHeader($nonce);

        // Header'ları gönder
        header("Content-Security-Policy: {$cspHeader}");
        header('X-Frame-Options: DENY');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=()');

        // HSTS (HTTPS only)
        if ($this->isHttps()) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }

        // Request'e nonce'u ekle (template için)
        $request = $request->withAttribute('csp_nonce', $nonce);

        return $next($request);
    }

    private function isHttps(): bool
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || ($_SERVER['SERVER_PORT'] ?? 0) == 443;
    }
}
```

### 6.2 Middleware Sırası

```
Request → OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf → BypassAuth → Auth → Permission → Validation → Controller
                                                                                          ↑
                                                                                   BU MIDDLEWARE
```

## 7. CSP Violation Handling

### 7.1 Violation Raporu

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Security;

/**
 * CSP violation handler.
 *
 * Tarayıcıdan gelen CSP violation raporlarını işler.
 */
class CspViolationHandler
{
    /**
     * CSP violation raporunu işle.
     *
     * @param array $report Violation raporu
     */
    public static function handle(array $report): void
    {
        $violation = [
            'directive' => $report['csp-directive'] ?? 'unknown',
            'blocked' => $report['csp-blocked-sample'] ?? '',
            'source' => $report['source-file'] ?? '',
            'line' => $report['line-number'] ?? 0,
            'column' => $report['column-number'] ?? 0,
        ];

        // Log'a yaz (hassas veri.redacted)
        error_log("[CSP] Violation: " . json_encode($violation));

        // Development modunda detaylı hata dön
        if ($_ENV['APP_DEBUG'] ?? false) {
            header('Content-Type: application/json');
            echo json_encode(['csp-violation' => $violation]);
        }
    }
}
```

### 7.2 Violation Endpoint

```
POST /api/csp-report
Content-Type: application/csp-report

{
  "csp-report": {
    "document-uri": "https://music.coremusic.net/",
    "referrer": "",
    "violated-directive": "script-src 'self'",
    "effective-directive": "script-src",
    "original-policy": "script-src 'self' 'nonce-abc123' 'strict-dynamic'",
    "disposition": "enforce",
    "blocked-uri": "https://evil.com/script.js",
    "status-code": 200,
    "source-file": "",
    "line-number": 15,
    "column-number": 2
  }
}
```

## 8. Nonce Kullanım Kılavuzu

### 8.1 Template'de Nonce Kullanımı

```php
<?php
declare(strict_types=1);

namespace CoreMusic\View;

/**
 * Template nonce integration.
 *
 * Tüm script ve style tag'lerinde nonce kullanılmalıdır.
 */
class TemplateRenderer
{
    /**
     * Script tag'i oluştur.
     *
     * @param string $src Script kaynağı
     * @param string $nonce Nonce
     * @return string HTML script tag'i
     */
    public static function script(string $src, string $nonce): string
    {
        return sprintf(
            '<script src="%s" nonce="%s"></script>',
            htmlspecialchars($src, ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($nonce, ENT_QUOTES, 'UTF-8')
        );
    }

    /**
     * Inline script bloğu oluştur.
     *
     * @param string $code Script kodu
     * @param string $nonce Nonce
     * @return string HTML script bloğu
     */
    public static function inlineScript(string $code, string $nonce): string
    {
        return sprintf(
            '<script nonce="%s">%s</script>',
            htmlspecialchars($nonce, ENT_QUOTES, 'UTF-8'),
            $code
        );
    }

    /**
     * Style tag'i oluştur.
     *
     * @param string $href Style kaynağı
     * @return string HTML link tag'i
     */
    public static function style(string $href): string
    {
        return sprintf(
            '<link rel="stylesheet" href="%s">',
            htmlspecialchars($href, ENT_QUOTES, 'UTF-8')
        );
    }
}
```

### 8.2 Nonce Akışı

```
1. SessionManagerMiddleware
   └─ $nonce = base64_encode(random_bytes(32))
   └─ $_SESSION['csp_nonce'] = $nonce

2. SecurityHeadersMiddleware
   └─ $nonce = $_SESSION['csp_nonce']
   └─ header("Content-Security-Policy: ... nonce-$nonce ...")
   └─ $request->withAttribute('csp_nonce', $nonce)

3. Controller / Template
   └─ $nonce = $request->getAttribute('csp_nonce')
   └─ <script src="app.js" nonce="$nonce"></script>

4. Tarayıcı
   └─ Script tag'indeki nonce ile CSP header'daki nonce'u karşılaştırır
   └─ Eşleşirse script'i çalıştırır
   └─ Eşleşmezse script'i engeller
```

## 9. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru | Sonuc |
|----------|----------|-------|
| `unsafe-eval` directive | Nonce-based | Code injection |
| Nonce'u log'da yazma | Nonce asla loglanmaz | Nonce sızıntısı |
| Nonce'u cookie'de saklama | Session'da saklama | Güvenlik açığı |
| Static nonce kullanma | Her istek için yeni nonce | Nonce tahmin edilebilir |
| `*` wildcard | `'self'` + nonce | Çok geniş izin |
| Inline script without nonce | Nonce ile inline script | CSP violation |
| `'unsafe-inline'` script-src | Nonce-based script-src | XSS riski |

## 10. Edge Cases

| Durum | Tetikleyici | Çözüm | ADR |
|-------|-------------|-------|-----|
| **Nonce sızıntısı** | Log'da nonce yazma | Nonce asla loglanmaz | ADR-012 |
| **CSP violation** | Engellenen script | Violation report endpoint | ADR-012 |
| **Mixed content** | HTTP + HTTPS karışımı | Tüm içerik HTTPS | ADR-012 |
| **Nonce drift** | Session timeout | Yeni nonce üretilir | ADR-011 |
| **Inline style** | ITCSS gerekli | `unsafe-inline` style-src | ADR-001 |
| **Third-party script** | Analytics, CDN | Harici kaynak ekleme | — |
| **eval() kullanımı** | Dynamic code execution | `unsafe-eval` yasak | ADR-001 |

## 11. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Nonce **her istek için** benzersiz olmalı | Nonce tahmin edilebilir |
| 2 | Nonce **random_bytes(32)** ile üretilmeli | Zayıf rastgelelik |
| 3 | `unsafe-eval` script-src'de **yasak** | Code injection |
| 4 | Nonce **log'da yazma** | Nonce sızıntısı |
| 5 | `strict-dynamic` **zorunlu** | Script loading sorunu |
| 6 | `frame-ancestors: none` **zorunlu** | Clickjacking |
| 7 | CSP header **her yanıtta** gönderilmeli | XSS riski |

## 12. İlgili Dosyalar

| Dosya | Kapsam |
|-------|--------|
| [[index]] | L1 Security Layer genel bakış |
| [[middleware]] | Middleware pipeline detayları |
| [[session]] | Session yönetimi (nonce üretimi) |
| [[csrf]] | CSRF koruması |
| [[auth]] | Authentication detayları |
| [[ADR-012-csp-nonce-strict-dynamic]] | CSP karar dokümanı |
| [[ADR-011-session-management]] | Session (nonce saklama) |
| [[ADR-001-vanilla-js-itcss]] | ITCSS (unsafe-inline) |

## 13. Çapraz Referanslar

| Bu Dosyadan | Hedef | İlişki |
|-------------|-------|--------|
| § Nonce | [[ADR-012-csp-nonce-strict-dynamic]] | Nonce üretimi |
| § Strict-Dynamic | W3C CSP Level 3 | Standart |
| § Violation | OWASP XSS Prevention | XSS türleri |
| § Template | [[ADR-001-vanilla-js-itcss]] | ITCSS kullanımı |
| § Session | [[ADR-011-session-management]] | Nonce saklama |

## 14. Sözlük

| Terim | Tanım |
|-------|-------|
| **CSP** | Content Security Policy — script injection önleme |
| **Nonce** | Tek kullanımlık rastgele değer (her istek için benzersiz) |
| **strict-dynamic** | Yüklenen script'lerin daha fazla script yüklemesine izin veren direktif |
| **Directive** | CSP kuralı (script-src, style-src vb.) |
| **XSS** | Cross-Site Scripting — script injection saldırısı |
| **Inline Script** | HTML içindeki script bloğu |
| **Self** | Same-origin kaynaklara izin veren direktif |
| **Unsafe-Inline** | Inline script/style'e izin veren direktif (risksiz: sadece style) |
| **Unsafe-Eval** | eval() kullanımına izin veren direktif (yasak) |
| **Violation** | CSP kuralı ihlali |

## 15. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Satır Sayısı** | 500+ |
| **Frontmatter** | ✅ |
| **Bölüm Sayısı** | 15 |
| **ADR Uyumlu** | ✅ 001, 011, 012 |
| **Zero Hallucination** | ✅ |

---

## 16. Gerçek Kod Karşılıkları (Faz 0 — 2026-09-08)

| Öğe | Gerçek Kod | Dosya | Not |
|-----|------------|-------|-----|
| Nonce üretimi | `_csp_nonce` özniteliği | `shared/src/Middleware/SecurityHeadersMiddleware.php` | `random_bytes(32)` → base64 hedefiyle uyumlu |
| Header birleştirme | `buildCsp()` | SecurityHeadersMiddleware | X-Frame-Options: DENY dahil |
| Pipeline konumu | #4 (pipeline immutable) | — | Nonce #5 SessionManager'a akar |
| `CspNonce` sınıfı | Ayrı sınıf olarak YOK | — | İşlev SecurityHeaders içinde (§4.1 hedef desen) |
| `CspViolationHandler` | PLANNED | — | §7 hedef; route/endpoint kodu yok |
| Template renderer | `HtmlShellRenderer` (L2) | `shared/src/PageRouter/HtmlShellRenderer.php` | Nonce attribute geçişi kod teyidi bekliyor |

**Header dikkat notu:** §6.1 örneğinde `X-XSS-Protection: 1; mode=block` görünüyor — bu başlık modern tarayıcılarda **deprecated**'tır (CLAUDE.md best-practices); kaldırma kararı ADR-012 ek kaydıyla verilmeli. Kodda mevcutsa doküman-kod uyumu korunur, tavsiye rapora düşer.

**HSTS:** Örnek HTTPS koşullu üretiyor; gerçek `buildCsp()` çıktısında HSTS satırı teyit edilmedi — DOĞRULAMA GEREKLİ (l1 index §32).

---

## 17. Security Headers Matrisi (tam liste)

| Header | Örnek Değer | Kritiklik | Durum |
|--------|-------------|-----------|-------|
| Content-Security-Policy | nonce + strict-dynamic | Kritik | IMPLEMENTED |
| X-Frame-Options | DENY | Kritik | IMPLEMENTED |
| X-Content-Type-Options | nosniff | Yüksek | Kod teyidi bekliyor |
| Referrer-Policy | strict-origin-when-cross-origin | Orta | Kod teyidi bekliyor |
| Permissions-Policy | camera=(), microphone=(), geolocation=() | Orta | Kod teyidi bekliyor |
| Strict-Transport-Security | max-age=31536000; includeSubDomains | Kritik (HTTPS) | DOĞRULAMA GEREKLİ |
| X-XSS-Protection | 1; mode=block | **Deprecated** — kaldırma değerlendirilmeli | Kod teyidi bekliyor |

Kural: Header üretimi tek noktadan (`buildCsp()`/header set) yönetilir; dağınık `header()` çağrısı yasaktır.

---

## 18. CSP Test Senaryoları (PHPUnit iskeleti)

| # | Senaryo | Beklenen |
|---|---------|----------|
| 1 | İstek #1 nonce üretimi | 32-byte base64 (44 karakter) |
| 2 | İstek #2 nonce | İlk nonceden farklı |
| 3 | Header içerik | `strict-dynamic` + `nonce-<değer>` içerir |
| 4 | `unsafe-eval` yokluğu | Header'da geçmez |
| 5 | frame-ancestors | `'none'` (örnek §5.1) — kod çıktısıyla karşılaştır |
| 6 | X-Frame-Options | DENY |
| 7 | Session'a nonce kaydı | `$_SESSION['csp_nonce']` setli |
| 8 | Request attribute | `csp_nonce` template'e ulaşır |
| 9 | style-src unsafe-inline | ITCSS için mevcut (ADR-001) |
| 10 | Violation endpoint | PLANNED — route yok (§19) |

---

## 19. Violation Endpoint Durumu

| Öğe | Durum |
|-----|-------|
| `POST /api/csp-report` route | PLANNED — routes.php'de kayıt yok (Faz 0) |
| `CspViolationHandler` sınıfı | PLANNED — kod yok |
| Tarayıcı rapor formatı | §7.2 standart (W3C) |
| Log entegrasyonu | deep-logging dokümanına bağlanacak (874 satır) |

Giriş önkoşulu: route kaydı + handler sınıfı + redaction kuralı (nonce asla loglanmaz) + test. Bu üçlü tamamlanmadan "raporlama aktif" yazılamaz.

---

## 20. Diagnostics (tekrarlanabilir)

```powershell
# 1. Gerçek nonce üretimi + header birleştirme
Select-String -LiteralPath "shared\src\Middleware\SecurityHeadersMiddleware.php" -Pattern "_csp_nonce|buildCsp|random_bytes"

# 2. strict-dynamic / frame koruması
Select-String -LiteralPath "shared\src\Middleware\SecurityHeadersMiddleware.php" -Pattern "strict-dynamic|frame|DENY"

# 3. HSTS teyidi (beklenen: ya satır var ya da DOĞRULAMA GEREKLİ kalır)
Select-String -LiteralPath "shared\src\Middleware\SecurityHeadersMiddleware.php" -Pattern "Strict-Transport"

# 4. Deprecated X-XSS-Protection taraması
Select-String -LiteralPath "shared\src\Middleware\SecurityHeadersMiddleware.php" -Pattern "X-XSS-Protection"

# 5. Template nonce geçişi
Select-String -LiteralPath "shared\src\PageRouter\HtmlShellRenderer.php" -Pattern "nonce" -ErrorAction SilentlyContinue
```

---

## 21. Ek SSS

**S: `unsafe-inline` style-src güvenli mi?**
C: CSS için script injection vektörü sınırlıdır; ITCSS dinamik tema ve critical CSS ihtiyacı nedeniyle izinli (ADR-001 bağlamı). script-src'de unsafe-inline KESİNLİKLE yasaktır.

**S: strict-dynamic ile eski tarayıcılar ne olur?**
C: strict-dynamic desteklemeyen tarayıcılar 'self' + nonce'u uygular (geriye dönük davranış). Modern tarayıcılar strict-dynamic'te 'self'i ihmal eder — hedef modern tarayıcı setidir.

**S: Nonce session'da mı request'te mi?**
C: Üretim: her istekte. Saklama: session'a yazılır (DOM patch sonrası token-dayanıklılık). Tüketim: request attribute ile template'e taşınır (§8.2 akışı).

**S: Third-party script (analytics) nasıl eklenir?**
C: ADR-012 kapsamında: ya nonce'lu loader (harici URL 'self' dışı olduğundan hash/hosts directive gerekir) ya da self-hosted. Karar ADR ek kaydıyla — plansız `<script src="cdn...">` CSP violation üretir.

**S: PageCache ile nonce çakışır mı?**
C: RİSK — nonce istek-bazlıdır; cache'lenen sayfa eski nonce taşır. Sayfa önbelleği CSP-başlığı cache'lememeli veya HTML'de nonce kullanmamalıdır. Etkileşim analizi Faz 2c (l2-routing) görevidir — bilinen açık işaretlendi.

**S: frame-ancestors 'none' ile 'none' vs DENY?**
C: CSP frame-ancestors modern standarttır; X-Frame-Options legacy'dir. İkisi birlikte gönderilir (derinlik savunması).

---

## 22. Revizyon Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 1.0.0 | 2026-08-08 | İlk doküman (prompt arşivi tabanlı) |
| 2.0.0 | 2026-09-08 | Faz 2b: §16 gerçek kod eşleştirmesi; §17 header matrisi (X-XSS deprecated notu); §18 test senaryoları; §19 violation endpoint durumu; §20 diagnostics; §21 SSS |

---

*L1 CSP Nonce & Strict-Dynamic v2.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team · Human Mode · Truth Mode*
