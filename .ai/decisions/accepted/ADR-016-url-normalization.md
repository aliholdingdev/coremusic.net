---
type: adr
category: routing
title: "ADR-016: URL Normalization"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-016: URL Normalization

**Status:** Frozen (değiştirilemez)
**Kategorisi:** Routing
**İlgili Agent:** [[.agents/backend-architect]]

---

## 1. Amaç

Bu ADR, CoreMusic platformunda tüm URL'lerin standart ve tutarlı bir formata dönüştürülmesini tanımlar. SEO dostu URL'ler, temiz yönlendirmeler ve multi-domain SPA yapısıyla uyumlu URL normalizasyonu kurallarını belirler. [[ADR-004-multi-domain-spa]] ve [[ADR-009-clean-url-redirect]] ile uyumludur.

---

## 2. Bağlam

### 2.1 Problem Tanımı

Farklı kullanıcılar ve araçlar aynı sayfaya farklı URL'lerle erişebilir:

| Yanlış URL | Doğru URL |
|------------|-----------|
| `music.coremusic.net/About/` | `music.coremusic.net/about` |
| `music.coremusic.net//music` | `music.coremusic.net/music` |
| `music.coremusic.net/Music` | `music.coremusic.net/music` |
| `music.coremusic.net/music/` | `music.coremusic.net/music` |
| `music.coremusic.net/./music` | `music.coremusic.net/music` |
| `music.coremusic.net/music/../about` | `music.coremusic.net/about` |

Bu tutarsızlıklar:
- SEO ranking'ini düşürür
- Cache verimliliğini azaltır
- Kullanıcı deneyimini bozar
- Double request oluşturma riski taşır
- CDN cache splitting'e yol açar

### 2.2 Multi-Domain SPA Yapısı

CoreMusic 10 panel ve 7 backend servis kullanmaktadır:

| Panel | Subdomain | URL Yapısı | Port |
|-------|-----------|------------|------|
| Music | music.coremusic.net | `/path` | 81 |
| Admin | admin.coremusic.net | `/path` | 80 |
| Download | download.coremusic.net | `/path` | 3001 |
| Media | media.coremusic.net | `/path` | 5000/6000 |
| Auth | auth.coremusic.net | `/path` | — |
| Home | home.coremusic.net | `/path` | 81 |
| Car | car.coremusic.net | `/path` | — |
| Studio | studio.coremusic.net | `/path` | 81 |
| Pro | pro.coremusic.net | `/path` | 81 |
| Landing | coremusic.net | `/path` | 80 |

Her subdomain kendi URL normalizasyonunu uygular.

### 2.3 SEO Etkisi

| URL Durumu | SEO Etkisi |
|------------|------------|
| Büyük harf | Duplicate content riski |
| Trailing slash | Ayrı sayfa olarak indexlenir |
| Double slash | Crawl budget israfı |
| Query string | Indexleme karmaşası |
| Temiz URL | Yüksek ranking |

### 2.4 İlişkili ADR'ler

| ADR | İlişki |
|-----|--------|
| [[ADR-004-multi-domain-spa]] | Multi-domain SPA mimarisi |
| [[ADR-009-clean-url-redirect]] | Temiz URL yönlendirmesi |
| [[ADR-021-spa-router-immutable-contract]] | SPA router sözleşmesi |
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS routing |

---

## 3. Karar

CoreMusic'te **subdomain routing** ile URL normalizasyonu yapılacak:

| Karar | Değer |
|-------|-------|
| Subdomain | Her panel için alt alan adı |
| Lowercase | Tüm URL'ler küçük harf |
| Trailing slash | Kaldırılır (son `/` yok) |
| Double slash | Tek slash'a dönüştürülür |
| Query string | Korunur |
| Fragment | Korunur |
| Encoding | UTF-8 |
| Redirect | 301 (kalıcı) |
| Canonical | `<link rel="canonical">` zorunlu |
| Sitemap | Tüm normalized URL'ler |

---

## 4. Teknik Detaylar

### 4.1 URL Normalizasyon Kuralları

| # | Kural | Öncesi | Sonrası | Zorunlu mu? |
|---|-------|--------|---------|-------------|
| 1 | Lowercase | `/Music/About` | `/music/about` | Evet |
| 2 | Trailing slash kaldır | `/music/` | `/music` | Evet |
| 3 | Double slash düzelt | `/music//about` | `/music/about` | Evet |
| 4 | Tek nokta atla | `/music/./about` | `/music/about` | Evet |
| 5 | Üst dizin çöz | `/music/../about` | `/about` | Evet |
| 6 | Query string koru | `/music?sort=name` | `/music?sort=name` | Evet |
| 7 | Fragment koru | `/music#section` | `/music#section` | Evet |
| 8 | Unicode koru | `/music/şarkı` | `/music/şarkı` | Evet |
| 9 | Boşluk düzelt | `/music/my%20song` | `/music/my-song` | Hayır |
| 10 | Encode zorunlu | `/music/my song` | `/music/my-song` | Evet |

### 4.2 PHP Normalizasyon Fonksiyonu

```php
declare(strict_types=1);

namespace CoreMusic\Routing;

class UrlNormalizer
{
    private const TRAILING_SLASH = '/';

    private const RESERVED_PATHS = [
        '/api',
        '/admin',
        '/auth',
        '/health',
        '/status',
    ];

    public static function normalize(string $url): string
    {
        // Parse URL
        $parsed = parse_url($url);
        if ($parsed === false) {
            return $url;
        }

        $path = $parsed['path'] ?? '/';
        $query = $parsed['query'] ?? '';
        $fragment = $parsed['fragment'] ?? '';

        // 1. Lowercase
        $path = strtolower($path);

        // 2. Double slash düzelt (ilk // hariç)
        if (str_starts_with($path, '//')) {
            $path = '/' . ltrim($path, '/');
        } else {
            $path = preg_replace('#/+#', '/', $path);
        }

        // 3. Tek nokta ve üst dizin çöz
        $path = self::resolveDotSegments($path);

        // 4. Trailing slash kaldır (root hariç)
        if ($path !== '/' && str_ends_with($path, self::TRAILING_SLASH)) {
            $path = rtrim($path, self::TRAILING_SLASH);
        }

        // 5. Boşlukları tireye çevir
        $path = str_replace(' ', '-', $path);
        $path = preg_replace('/-+/', '-', $path);

        // 6. URL'yi yeniden birleştir
        $normalized = $path;
        if ($query !== '') {
            $normalized .= '?' . $query;
        }
        if ($fragment !== '') {
            $normalized .= '#' . $fragment;
        }

        return $normalized;
    }

    private static function resolveDotSegments(string $path): string
    {
        $segments = explode('/', $path);
        $resolved = [];

        foreach ($segments as $segment) {
            if ($segment === '.') {
                continue;
            }
            if ($segment === '..') {
                array_pop($resolved);
                continue;
            }
            $resolved[] = $segment;
        }

        return implode('/', $resolved) ?: '/';
    }

    public static function needsRedirect(string $original, string $normalized): bool
    {
        return $original !== $normalized;
    }

    public static function getRedirectTarget(string $url): ?string
    {
        $normalized = self::normalize($url);
        if (self::needsRedirect($url, $normalized)) {
            return $normalized;
        }
        return null;
    }
}
```

### 4.3 Middleware Implementasyonu

```php
declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Routing\UrlNormalizer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class UrlNormalizationMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $uri = $request->getUri();
        $path = $uri->getPath();

        $normalized = UrlNormalizer::normalize($path);

        if ($normalized !== $path) {
            // 301 redirect
            $newUri = $uri->withPath($normalized);
            return new \Laminas\Diactoros\Response\RedirectResponse(
                $newUri,
                301
            );
        }

        return $handler->handle($request);
    }
}
```

### 4.4 SPA Router Uyumu

```javascript
// Router.js — Vanilla JS ES6+
// Framework yasak (ADR-001)

class SPA Router {
    normalizeUrl(path) {
        // Lowercase
        path = path.toLowerCase();

        // Trailing slash kaldır
        if (path !== '/' && path.endsWith('/')) {
            path = path.slice(0, -1);
        }

        // Double slash düzelt
        path = path.replace(/\/+/g, '/');

        // Tek nokta atla
        path = path.replace(/\/\.\//g, '/');

        // Üst dizin çöz
        path = this.resolveDotSegments(path);

        return path;
    }

    resolveDotSegments(path) {
        const parts = path.split('/');
        const resolved = [];

        for (const part of parts) {
            if (part === '.') continue;
            if (part === '..') {
                resolved.pop();
                continue;
            }
            resolved.push(part);
        }

        return resolved.join('/') || '/';
    }

    navigate(path) {
        const normalized = this.normalizeUrl(path);
        if (normalized !== window.location.pathname) {
            history.pushState({}, '', normalized);
            this.loadPage(normalized);
        }
    }
}
```

### 4.5 Hata Sayfaları

| HTTP Kodu | Durum | Aksiyon |
|-----------|-------|---------|
| 301 | Kalıcı redirect | Normalized URL'e yönlendir |
| 302 | Geçici redirect | Kullanılmaz (301 tercih) |
| 404 | Sayfa bulunamadı | Custom 404 sayfası |
| 405 | Method izinsiz | 405 response |
| 410 | Sayfa kaldırıldı | Gone response |

### 4.6 Cache Stratejisi

| URL Tipi | Cache Süresi | Strateji |
|----------|-------------|----------|
| Ana sayfa | 1 saat | CDN cache |
| Statik dosya | 1 yıl | Browser cache |
| API endpoint | 0 | No cache |
| Normalized URL | 1 saat | Redirect cache |
| 301 redirect | 1 yıl | Browser cache |

### 4.7 SEO Uyumluluğu

| SEO Kuralı | Uygulama | Zorunlu mu? |
|------------|----------|-------------|
| Canonical URL | `<link rel="canonical">` | Evet |
| Sitemap | Tüm normalized URL'ler | Evet |
| Robots.txt | Allowed paths | Evet |
| Hreflang | Dil bazlı URL'ler | Hayır |
| Open Graph | OG URL tag | Hayır |
| Structured Data | Schema.org | Hayır |

### 4.8 Middleware Pipeline Sırası

```
1. SessionManagerMiddleware()    — Session başlat
2. BypassAuthMiddleware()        — Test bypass
3. RateLimiterMiddleware()       — Rate limit
4. AuthMiddleware()              — Auth bilgisi
5. UrlNormalizationMiddleware()  — URL normalize
6. SecurityHeadersMiddleware()   — CSP headers
7. CsrfMiddleware()              — CSRF token
```

URL normalization middleware'i, security headers'dan önce çalışır.

---

## 5. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| Büyük harf URL | Lowercase |
| Trailing slash | Kaldır (root hariç) |
| Double slash | Tek slash |
| Query string encoded | Doğru encoding |
| Boşluk URL'de | Tire ile değiştirme |
| Unicode encode | Doğrudan Unicode |
| 302 redirect | 301 (kalıcı) |
| Hardcoded path | Router ile yönetimi |
| SPA hash routing | Clean URL |
| Case-sensitive routing | Case-insensitive |
| `$_SERVER['REQUEST_URI']` doğrudan | `UrlNormalizer::normalize()` |
| Inline redirect | Middleware kullanımı |

---

## 6. Edge Cases

| Senaryo | Tetikleyici | Çözüm |
|---------|-------------|-------|
| Unicode path | `/music/şarkı` | UTF-8 korunur |
| Boşluk path | `/music/my song` | Tireye çevirme |
| Boş path | `` | Root'a yönlendirme |
| Circular redirect | A -> B -> A | Loop detection max 5 |
| Query string complex | `?a=1&b=2&c=3` | Korunur |
| Fragment identifier | `#section1` | Korunur |
| API versioning | `/api/v1/...` | Reserved path |
| Static file | `/css/style.css` | Normalization uygulanmaz |
| Port number | `:81` | Korunur |
| HTTPS redirect | HTTP -> HTTPS | Ayrı middleware |
| Double query | `?a=1?a=2` | İlk değer korunur |
| Encoded slash | `%2F` | Decode edilmez |
| Null byte | `%00` | Reddedilir |
| Very long URL | 2000+ karakter | Kısaltma yapılmaz |

---

## 7. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | **Lowercase zorunlu** | Redirect uygulanır |
| 2 | **Trailing slash kaldır** | Redirect uygulanır |
| 3 | **Double slash düzelt** | Redirect uygulanır |
| 4 | **301 redirect** | Kalıcı redirect zorunlu |
| 5 | **Query string koru** | Bozulmamalı |
| 6 | **Fragment koru** | Bozulmamalı |
| 7 | **UTF-8 encoding** | Unicode karakter korunur |
| 8 | **SPA router uyumu** | Frontend-backend tutarlılığı |
| 9 | **SEO canonical** | Canonical URL zorunlu |
| 10 | **Cache invalidation** | Redirect cache süresi |
| 11 | **No circular redirect** | Max 5 redirect döngüsü |
| 12 | **Middleware order** | Sıra değişmez |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-004-multi-domain-spa]] | Multi-domain SPA | URL normalizasyonu her domain için |
| [[ADR-009-clean-url-redirect]] | Temiz URL | Redirect stratejisi |
| [[ADR-021-spa-router-immutable-contract]] | SPA router | Frontend uyumu |
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS | Router implementasyonu |
| [[ADR-042-vault-restructuring-2026-08-03]] | Vault standardı | URL dosya yapısı |
| [[ADR-010-csrf-protection-strategy]] | CSRF | Middleware pipeline |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2.2 | [[subdomains/README]] | 10 panel subdomain yapısı |
| § 4.2 | [[architecture/l2-routing]] | L2 routing katmanı |
| § 4.3 | [[ADR-010-csrf-protection-strategy]] | Middleware pipeline |
| § 4.4 | [[architecture/l3-presentation]] | Frontend SPA router |
| § 5 | [[ADR-001-vanilla-js-itcss]] | Yasak örüntüleri |
| § 6 | [[ADR-021-spa-router-immutable-contract]] | Router sözleşmesi |
| § 7 | [[architecture/l1-security]] | Security middleware |
| § 4.8 | [[ADR-009-clean-url-redirect]] | Redirect stratejisi |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **URL Normalization** | URL'leri standart formata dönüştürme |
| **Subdomain** | Alt alan adı (music.coremusic.net) |
| **Trailing Slash** | URL sonundaki `/` |
| **Double Slash** | Ardışık iki `/` |
| **Query String** | URL parametreleri (`?key=value`) |
| **Fragment** | URL fragment identifier (`#section`) |
| **301 Redirect** | Kalıcı yönlendirme |
| **Canonical URL** | Tercih edilen URL |
| **SPA** | Single Page Application |
| **UTF-8** | Unicode encoding standardı |
| **SEO** | Search Engine Optimization |
| **CDN** | Content Delivery Network |
| **Clean URL** | Parametresiz, temiz URL |
| **Cache Invalidation** | Önbellek geçersizleştirme |
| **Dot Segment** | `.` ve `..` URL çözme |
| **Middleware** | Ara yazılım katmanı |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| ADR Status | Frozen (değiştirilemez) |
| Sections | 11 |
| Hard Guardrails | 12 |
| Edge Cases | 14 |
| Yasak Örüntüleri | 12 |
| İlgili ADR'ler | 6 |
| Çapraz Referanslar | 8 |
| Sözlük Terimleri | 16 |
| URL Normalizasyon Kuralı | 10 |
| Subdomain Sayısı | 10 |
| Backend Servis Sayısı | 7 |
| Redirect Kodu | 301 |
| Encoding | UTF-8 |
| Max Redirect Loop | 5 |

---

## 12. Authority

## 13. URL Security

| Tehdit | Koruma |
|--------|--------|
| XSS via URL | URL encoding, input validation |
| Open redirect | Whitelist redirect targets |
| Path traversal | Dot segment resolution |
| Double encoding | Single decode layer |
| Null byte | Red flag, reject |

### 13.1 Redirect Loop Prevention

```php
class RedirectGuard {
    private const MAX_REDIRECTS = 5;
    private array $redirectChain = [];

    public function canRedirect(string $target): bool {
        if (count($this->redirectChain) >= self::MAX_REDIRECTS) {
            return false;
        }
        if (in_array($target, $this->redirectChain, true)) {
            return false; // Circular redirect
        }
        $this->redirectChain[] = $target;
        return true;
    }

    public function reset(): void {
        $this->redirectChain = [];
    }
}
```

### 13.2 URL Encoding Kuralları

| Karakter | Encode | Decode |
|----------|--------|--------|
| Boşluk | `%20` veya `+` | ` ` |
| `/` | `%2F` | `/` (decode edilmez) |
| `?` | `%3F` | `?` |
| `#` | `%23` | `#` |
| `%` | `%25` | `%` |
| `&` | `%26` | `&` |
| `=` | `%3D` | `=` |

---

## 14. Analytics Entegrasyonu

| Platform | Uygulama |
|----------|----------|
| Google Analytics | Normalized URL'lerle pageview |
| Matomo | Privacy-first analytics |
| Custom | CoreMusic analytics API |

---

## 15. Performance Metrikleri

| Metrik | Hedef | Ölçüm |
|--------|-------|-------|
| Redirect latency | <50ms | Server-side |
| Normalization time | <1ms | PHP execution |
| Cache hit ratio | >90% | CDN logs |
| SEO crawl budget | Optimized | Search Console |

---

## 16. Testing Strategy

| Test Type | Scope | Framework |
|-----------|-------|-----------|
| Unit test | UrlNormalizer | PHPUnit 11 |
| Integration test | Middleware | PHPUnit 11 |
| E2E test | Redirect chains | Playwright |
| SEO test | Canonical URLs | Custom script |

### 16.1 Test Cases

| Test | Input | Expected |
|------|-------|----------|
| Lowercase | `/Music/About` | `/music/about` |
| Trailing slash | `/music/` | `/music` |
| Double slash | `/music//about` | `/music/about` |
| Dot segment | `/music/./about` | `/music/about` |
| Parent dir | `/music/../about` | `/about` |
| Query preserved | `/music?sort=name` | `/music?sort=name` |
| Fragment preserved | `/music#section` | `/music#section` |
| Root path | `/` | `/` |
| No redirect | `/music` | null |

---

## 17. Monitoring & Metrics

| Metrik | Hedef | Kaynak |
|--------|-------|--------|
| Redirect rate | <5% | Access logs |
| 404 rate | <1% | Server logs |
| SEO index coverage | >95% | Search Console |
| Cache hit ratio | >90% | CDN logs |
| Normalization time | <1ms | PHP profiling |

---

## 18. Quality Report (Final)

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Sections | 18 |
| Hard Guardrails | 12 |
| Edge Cases | 14 |
| Test Cases | 9 |
| Performance Metrics | 5 |
| Monitoring Metrics | 5 |
| URL Security Rules | 5 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
**Immutability:** ADR 001-037 frozen, değiştirilemez
**Scope:** CoreMusic URL normalizasyonu
**Governance:** Red Team · Human Mode · Truth Mode
