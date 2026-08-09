---
type: adr
category: routing
title: "ADR-009: Clean URL Redirect"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-009: Clean URL Redirect

**Status:** Frozen (değiştirilemez)
**Kategorisi:** Routing
**İlgili Agent:** [[.agents/backend-architect]]
**Frozen:** 2026-05-15 — ADR 001-037 arasında değiştirilemez

---

## 1. Amaç

Bu ADR, CoreMusic platformunda URL yönlendirme stratejisini tanımlar. Clean URL yapısı tüm subdomain'lerde (music, admin, download, media, auth, home, car, studio, pro, landing) zorunludur. `.php` uzantıları, `index.php` yönlendirmeleri ve parametre tabanlı URL'ler temizlenir. 301 kalıcı yönlendirme ile SEO dostu URL yapısı sağlanır.

Bu ADR'nin amacı:
- SEO performansını optimize etmek
- Kullanıcı deneyimini iyileştirmek
- URL yapısını standartlaştırmak
- Eski URL'leri 301 ile yönlendirmek
- Subdomain bazlı routing uyumluluğunu sağlamak
- SPA router (ADR-021) ile entegrasyonu garanti altına almak

## 2. Bağlam

### 2.1 İş Problemi

CoreMusic 10 panelli multi-domain SPA mimarisinde (ADR-004) her panelin URL yapısı tutarlı olmalıdır:

| Panel | Eski URL | Hedef URL | Durum |
|-------|----------|-----------|-------|
| Music | `/index.php?page=music` | `/music` | Temizlenmeli |
| Admin | `/admin/dashboard.php` | `/admin/dashboard` | Temizlenmeli |
| Download | `/download/index.php?id=123` | `/download/123` | Temizlenmeli |
| Media | `/api/v1/users.php` | `/api/v1/users` | Temizlenmeli |
| Auth | `/auth/login.php` | `/auth/login` | Temizlenmeli |
| Home | `/home/index.php` | `/home` | Temizlenmeli |
| Car | `/car/player.php` | `/car/player` | Temizlenmeli |
| Studio | `/studio/mixer.php` | `/studio/mixer` | Temizlenmeli |
| Pro | `/pro/equalizer.php` | `/pro/equalizer` | Temizlenmeli |
| Landing | `/index.php` | `/` | Temizlenmeli |

### 2.2 Teknik Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Framework yasak | ADR-001 ile Vanilla JS zorunlu |
| SPA router | ADR-021 ile custom router |
| Middleware sırası | ADR-010/011/012/013/022 |
| Subdomain routing | ADR-016 ile URL normalization |
| HTTPS zorunlu | Tüm subdomain'lerde |

### 2.3 SEO Gereksinimleri

| Gereksinim | Açıklama |
|------------|----------|
| Canonical URL | Her sayfada canonical tag |
| Sitemap | XML sitemap zorunlu |
| Robots.txt | crawlability tanımlı |
| Structured data | Schema.org uyumlu |
| Mobile-friendly | Responsive tasarım |

### 2.4 İlişkili Kararlar

| ADR | İlişki |
|-----|--------|
| ADR-001 | Vanilla JS + ITCSS frontend |
| ADR-004 | Multi-domain SPA mimarisi |
| ADR-016 | URL normalization |
| ADR-021 | SPA router contract |
| ADR-042 | MSA limit, vault standardı |

---

## 3. Karar

CoreMusic'te **clean URL** yapısı kullanılacak. Tüm subdomain'lerde `.php` uzantıları, `index.php` yönlendirmeleri ve parametre tabanlı URL'ler temizlenecek. 301 kalıcı yönlendirme ile eski URL'ler yeni URL'lere yönlendirilecek.

### 3.1 Karar Özeti

| Parametre | Değer |
|-----------|-------|
| URL yapısı | Clean URL (uzantısız) |
| Yönlendirme | 301 (Kalıcı) |
| Parametre | Path-based (query string yasak) |
| Case | Lowercase zorunlu |
| Trailing slash | Opsiyonel (normalize) |
| Double slash | Temizlenir |
| Fragment | Strip edilir |

### 3.2 URL Dönüşüm Kuralları

| Kural | Örnek Input | Örnek Output |
|-------|-------------|--------------|
| .php kaldır | `/music.php` | `/music` |
| index.php kaldır | `/admin/index.php` | `/admin` |
| Parametre → path | `/download.php?id=123` | `/download/123` |
| Query strip | `/music?ref=home` | `/music` |
| Fragment strip | `/music#section` | `/music` |
| Lowercase | `/Music/Song` | `/music/song` |
| Double slash | `/music//song` | `/music/song` |
| Trailing slash | `/music/song/` | `/music/song` |
| Subdomain | `music.coremusic.net/` | `music.coremusic.net` |

### 3.3 Yönlendirme Matrisi

| Kaynak URL | Hedef URL | Status Code | Neden |
|------------|-----------|-------------|-------|
| `/index.php?page=music` | `/music` | 301 | PHP temizleme |
| `/admin/dashboard.php` | `/admin/dashboard` | 301 | uzantı kaldır |
| `/download/index.php?id=123` | `/download/123` | 301 | parametre dönüşümü |
| `/api/v1/users.php` | `/api/v1/users` | 301 | uzantı kaldır |
| `/auth/login.php` | `/auth/login` | 301 | uzantı kaldır |
| `/Music/Song` | `/music/song` | 301 | lowercase |
| `/music//song` | `/music/song` | 301 | double slash |
| `/music/song/` | `/music/song` | 301 | trailing slash |
| `/music/song?ref=home` | `/music/song` | 301 | query strip |

---

## 4. Teknik Detaylar

### 4.1 Apache .htaccess Yapılandırması

```apache
# CoreMusic Clean URL - Apache mod_rewrite
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /

    # Güvenlik: Directory traversal önleme
    RewriteCond %{QUERY_STRING} (\.\.\/|\.\.\\)
    RewriteRule .* - [F,L]

    # .php uzantısı kaldır (301 redirect)
    RewriteCond %{THE_REQUEST} \s(.+?)\.php[\s?] [NC]
    RewriteRule ^ /%1 [R=301,L,QSA]

    # index.php kaldır (301 redirect)
    RewriteCond %{THE_REQUEST} \s/index\.php[\s?] [NC]
    RewriteRule ^ / [R=301,L,QSA]

    # Parametre tabanlı URL → path-based
    RewriteCond %{QUERY_STRING} ^page=(.+)$ [NC]
    RewriteRule ^$ /%1? [R=301,L,QSA]

    # ID parametresi → path
    RewriteCond %{QUERY_STRING} ^id=(\d+)$ [NC]
    RewriteRule ^(.+)/index\.php$ /%1/%1 [R=301,L,QSA]

    # Double slash temizleme
    RewriteCond %{REQUEST_URI} ^(.*?)\/\/+(.*)$
    RewriteRule .* /%1%2 [R=301,L,QSA]

    # Lowercase dönüşümü
    RewriteCond %{REQUEST_URI} [A-Z]
    RewriteRule ^(.*)$ /${lowercase:$1} [R=301,L,QSA]

    # SPA fallback (tüm route'lar için)
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ /index.php [L,QSA]
</IfModule>
```

### 4.2 Nginx Yapılandırması

```nginx
# CoreMusic Clean URL - Nginx
server {
    listen 80;
    server_name music.coremusic.net;
    root /var/www/music;
    index index.php;

    # Güvenlik başlıkları
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;

    # .php uzantısı kaldır (301 redirect)
    if ($request_uri ~* \.php$) {
        return 301 $scheme://$host$uri;
    }

    # index.php kaldır (301 redirect)
    if ($request_uri ~* /index\.php$) {
        return 301 $scheme://$host$uri;
    }

    # Parametre tabanlı URL → path-based
    if ($arg_page) {
        return 301 $scheme://$host/$arg_page;
    }

    # Double slash temizleme
    if ($request_uri ~* ^(.*?)\/\/+(.*)$) {
        return 301 $scheme://$host$1$2;
    }

    # Lowercase dönüşümü
    if ($request_uri ~* [A-Z]) {
        return 301 $scheme://$host${lowercase:$uri};
    }

    # SPA fallback
    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 4.3 PHP Redirect Handler

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Routing;

class CleanUrlRedirect
{
    private const REDIRECT_RULES = [
        // .php kaldır
        '/\.php$/i' => '',
        // index.php kaldır
        '/\/index\.php$/i' => '',
        // Double slash temizle
        '/\/\/+/' => '/',
        // Trailing slash kaldır (root hariç)
        '/\/$/' => '',
    ];

    private const PARAM_MAP = [
        'page' => '/%s',
        'id' => '/%s',
        'action' => '/%s',
        'section' => '/%s',
    ];

    public function handleRedirect(string $uri, array $queryParams): ?string
    {
        $cleanUri = $uri;

        // Uzantı temizleme
        foreach (self::REDIRECT_RULES as $pattern => $replacement) {
            $cleanUri = preg_replace($pattern, $replacement, $cleanUri);
        }

        // Parametre dönüşümü
        if (!empty($queryParams)) {
            $cleanUri = $this->convertParams($cleanUri, $queryParams);
        }

        // Lowercase dönüşümü
        $cleanUri = strtolower($cleanUri);

        // Değişiklik var mı?
        if ($cleanUri !== $uri) {
            return $cleanUri;
        }

        return null;
    }

    private function convertParams(string $uri, array $params): string
    {
        foreach (self::PARAM_MAP as $param => $template) {
            if (isset($params[$param])) {
                $cleanUri = sprintf($template, $params[$param]);
                unset($params[$param]);
                return $cleanUri;
            }
        }
        return $uri;
    }

    public function isCleanUrl(string $url): bool
    {
        return !preg_match('/\.(php|html|htm)$/i', $url)
            && !str_contains($url, 'index.php')
            && !str_contains($url, '?')
            && !str_contains($url, '#')
            && strtolower($url) === $url;
    }
}
```

### 4.4 JavaScript SPA Router Entegrasyonu

```javascript
'use strict';

const CleanUrlRouter = {
    init() {
        // popstate event
        window.addEventListener('popstate', () => this.handleRoute());

        // Link click delegation
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[data-route]');
            if (link) {
                e.preventDefault();
                this.navigate(link.getAttribute('href'));
            }
        });

        // İlk yükleme
        this.handleRoute();
    },

    navigate(path) {
        // URL temizleme
        const cleanPath = this.cleanUrl(path);

        // 301 redirect simülasyonu (SPA içinde)
        if (cleanPath !== path) {
            window.history.replaceState(null, '', cleanPath);
        }

        // Route'u çalıştır
        window.history.pushState(null, '', cleanPath);
        this.handleRoute();
    },

    cleanUrl(url) {
        let clean = url;

        // .php kaldır
        clean = clean.replace(/\.php$/i, '');

        // index.php kaldır
        clean = clean.replace(/\/index\.php$/i, '');

        // Double slash temizle
        clean = clean.replace(/\/\/+/, '/');

        // Trailing slash (root hariç)
        if (clean.length > 1) {
            clean = clean.replace(/\/$/, '');
        }

        // Lowercase
        clean = clean.toLowerCase();

        // Query string kaldır
        const queryIndex = clean.indexOf('?');
        if (queryIndex !== -1) {
            clean = clean.substring(0, queryIndex);
        }

        // Fragment kaldır
        const fragmentIndex = clean.indexOf('#');
        if (fragmentIndex !== -1) {
            clean = clean.substring(0, fragmentIndex);
        }

        return clean;
    },

    handleRoute() {
        const path = window.location.pathname;
        const cleanPath = this.cleanUrl(path);

        // URL temizlenecek mi?
        if (cleanPath !== path) {
            window.history.replaceState(null, '', cleanPath);
        }

        // Route handler'ı çağır
        const handler = this.routes.get(cleanPath);
        if (handler) {
            handler();
        }
    },

    routes: new Map()
};
```

### 4.5 Canonical URL Yönetimi

```html
<!-- Canonical URL -->
<link rel="canonical" href="https://music.coremusic.net/music/song/123" />

<!-- Open Graph -->
<meta property="og:url" content="https://music.coremusic.net/music/song/123" />

<!-- hreflang (çoklu dil) -->
<link rel="alternate" hreflang="tr" href="https://music.coremusic.net/tr/music/song/123" />
<link rel="alternate" hreflang="en" href="https://music.coremusic.net/en/music/song/123" />
```

### 4.6 Sitemap XML Oluşturma

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Routing;

class SitemapGenerator
{
    private const BASE_URL = 'https://music.coremusic.net';
    private const CHANGE_FREQ = 'weekly';
    private const DEFAULT_PRIORITY = 0.8;

    public function generate(array $routes): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($routes as $route) {
            $xml .= '<url>';
            $xml .= '<loc>' . self::BASE_URL . $route['path'] . '</loc>';
            $xml .= '<lastmod>' . ($route['lastmod'] ?? date('Y-m-d')) . '</lastmod>';
            $xml .= '<changefreq>' . ($route['changefreq'] ?? self::CHANGE_FREQ) . '</changefreq>';
            $xml .= '<priority>' . ($route['priority'] ?? self::DEFAULT_PRIORITY) . '</priority>';
            $xml .= '</url>';
        }

        $xml .= '</urlset>';
        return $xml;
    }
}
```

### 4.7 301 Redirect Cache

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Routing;

class RedirectCache
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getRedirect(string $sourceUrl): ?string
    {
        $stmt = $this->pdo->prepare(
            'SELECT target_url FROM url_redirects 
             WHERE source_url = :source AND is_active = 1 
             LIMIT 1'
        );
        $stmt->execute([':source' => $sourceUrl]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $result ? $result['target_url'] : null;
    }

    public function addRedirect(string $sourceUrl, string $targetUrl, int $statusCode = 301): bool
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO url_redirects (source_url, target_url, status_code, created_at) 
             VALUES (:source, :target, :status_code, NOW()) 
             ON DUPLICATE KEY UPDATE target_url = :target2, updated_at = NOW()'
        );

        return $stmt->execute([
            ':source' => $sourceUrl,
            ':target' => $targetUrl,
            ':status_code' => $statusCode,
            ':target2' => $targetUrl,
        ]);
    }

    public function removeRedirect(string $sourceUrl): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE url_redirects SET is_active = 0 WHERE source_url = :source'
        );

        return $stmt->execute([':source' => $sourceUrl]);
    }

    public function getAllRedirects(): array
    {
        $stmt = $this->pdo->query(
            'SELECT source_url, target_url, status_code, hit_count 
             FROM url_redirects 
             WHERE is_active = 1 
             ORDER BY hit_count DESC'
        );

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
```

### 4.8 URL Validation

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Routing;

class UrlValidator
{
    private const ALLOWED_PATTERNS = [
        '/^\/[a-z0-9\-\/]*$/',           // Clean URL
        '/^\/[a-z0-9\-\/]*\/\d+$/',      // ID-based URL
        '/^\/api\/v\d+\/[a-z\-]+$/',     // API endpoint
    ];

    private const BLOCKED_PATTERNS = [
        '/\.(php|html|htm|asp|jsp)$/i',   // Uzantı
        '/\.\.\//',                         // Directory traversal
        '/\/index\.php/i',                 // index.php
        '/\?/',                            // Query string
        '/#/',                             // Fragment
    ];

    public function isValid(string $url): bool
    {
        // Bloklu pattern kontrolü
        foreach (self::BLOCKED_PATTERNS as $pattern) {
            if (preg_match($pattern, $url)) {
                return false;
            }
        }

        // İzinli pattern kontrolü
        foreach (self::ALLOWED_PATTERNS as $pattern) {
            if (preg_match($pattern, $url)) {
                return true;
            }
        }

        return false;
    }

    public function normalize(string $url): string
    {
        // Lowercase
        $url = strtolower($url);

        // Double slash temizle
        $url = preg_replace('/\/\/+/', '/', $url);

        // Trailing slash kaldır (root hariç)
        if (strlen($url) > 1) {
            $url = rtrim($url, '/');
        }

        // Query string kaldır
        $queryIndex = strpos($url, '?');
        if ($queryIndex !== false) {
            $url = substr($url, 0, $queryIndex);
        }

        // Fragment kaldır
        $fragmentIndex = strpos($url, '#');
        if ($fragmentIndex !== false) {
            $url = substr($url, 0, $fragmentIndex);
        }

        return $url;
    }
}
```

### 4.9 Redirect Chain Optimizasyonu

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Routing;

class RedirectChainOptimizer
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function optimizeChains(): int
    {
        $stmt = $this->pdo->query(
            'SELECT source_url, target_url FROM url_redirects WHERE is_active = 1'
        );
        $redirects = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $optimized = 0;
        foreach ($redirects as $redirect) {
            $finalTarget = $this->resolveChain($redirect['target_url'], $redirects);

            if ($finalTarget !== $redirect['target_url']) {
                $update = $this->pdo->prepare(
                    'UPDATE url_redirects SET target_url = :target WHERE source_url = :source'
                );
                $update->execute([':target' => $finalTarget, ':source' => $redirect['source_url']]);
                $optimized++;
            }
        }

        return $optimized;
    }

    private function resolveChain(string $target, array $redirects, int $depth = 0): string
    {
        if ($depth > 10) {
            return $target;
        }

        foreach ($redirects as $redirect) {
            if ($redirect['source_url'] === $target) {
                return $this->resolveChain($redirect['target_url'], $redirects, $depth + 1);
            }
        }

        return $target;
    }
}
```

### 4.10 Monitoring ve Logging

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Routing;

class RedirectMonitor
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function logRedirect(string $source, string $target, int $statusCode): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO redirect_logs (source_url, target_url, status_code, ip_address, user_agent, created_at)
             VALUES (:source, :target, :status, :ip, :ua, NOW())'
        );

        $stmt->execute([
            ':source' => $source,
            ':target' => $target,
            ':status' => $statusCode,
            ':ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            ':ua' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        ]);

        // Hit count güncelle
        $update = $this->pdo->prepare(
            'UPDATE url_redirects SET hit_count = hit_count + 1 WHERE source_url = :source'
        );
        $update->execute([':source' => $source]);
    }

    public function getRedirectStats(): array
    {
        $stmt = $this->pdo->query(
            'SELECT 
                source_url,
                target_url,
                hit_count,
                status_code,
                created_at
             FROM url_redirects 
             WHERE is_active = 1 
             ORDER BY hit_count DESC 
             LIMIT 100'
        );

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getOrphanRedirects(): array
    {
        $stmt = $this->pdo->query(
            'SELECT source_url, target_url, hit_count 
             FROM url_redirects 
             WHERE is_active = 1 AND hit_count = 0 
             ORDER BY created_at ASC'
        );

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
```

### 4.11 URL Haritası

| Subdomain | Route Pattern | Handler | Durum |
|-----------|---------------|---------|-------|
| music.coremusic.net | `/music` | MusicController::index | ✅ |
| music.coremusic.net | `/music/song/:id` | MusicController::song | ✅ |
| music.coremusic.net | `/music/album/:id` | MusicController::album | ✅ |
| music.coremusic.net | `/music/artist/:id` | MusicController::artist | ✅ |
| music.coremusic.net | `/music/genre/:id` | MusicController::genre | ✅ |
| admin.coremusic.net | `/admin` | AdminController::dashboard | ✅ |
| admin.coremusic.net | `/admin/users` | AdminController::users | ✅ |
| admin.coremusic.net | `/admin/settings` | AdminController::settings | ✅ |
| download.coremusic.net | `/download` | DownloadController::index | ✅ |
| download.coremusic.net | `/download/:id` | DownloadController::status | ✅ |
| media.coremusic.net | `/media` | MediaController::index | ✅ |
| media.coremusic.net | `/media/stream/:id` | MediaController::stream | ✅ |
| auth.coremusic.net | `/auth/login` | AuthController::login | ✅ |
| auth.coremusic.net | `/auth/register` | AuthController::register | ✅ |
| home.coremusic.net | `/home` | HomeController::index | ✅ |
| car.coremusic.net | `/car/player` | CarController::player | ✅ |
| studio.coremusic.net | `/studio/mixer` | StudioController::mixer | ✅ |
| pro.coremusic.net | `/pro/equalizer` | ProController::equalizer | ✅ |
| coremusic.net | `/` | LandingController::index | ✅ |

### 4.12 SEO Uyumluluğu

| Özellik | Uygulama | Durum |
|---------|----------|-------|
| Canonical URL | `<link rel="canonical">` | Zorunlu |
| Sitemap XML | `/sitemap.xml` | Zorunlu |
| Robots.txt | `/robots.txt` | Zorunlu |
| Structured data | Schema.org JSON-LD | Zorunlu |
| Open Graph | og:title, og:description | Zorunlu |
| Twitter Cards | twitter:card | Opsiyonel |
| hreflang | Dil alternatifleri | Opsiyonel |
| Breadcrumb | JSON-LD breadcrumb | Zorunlu |
| Mobile-friendly | Responsive | Zorunlu |

### 4.13 Hata Yönetimi

| HTTP Status | Kullanım | Yönlendirme |
|-------------|----------|-------------|
| 200 | Başarılı | — |
| 301 | Kalıcı yönlendirme | Clean URL |
| 302 | Geçici yönlendirme | Maintenance |
| 404 | Bulunamadı | Custom 404 sayfası |
| 410 | Kalıcı olarak kaldırıldı | — |
| 500 | Sunucu hatası | Error page |

### 4.14 Performans Metrikleri

| Metrik | Hedef | Ölçüm |
|--------|-------|-------|
| Redirect süresi | <50ms | Server response |
| Redirect chain | Max 3 | Zincir analizi |
| Cache hit | >95% | Redirect cache |
| 404 oranı | <1% | Error logs |
| SEO crawl | 100% | Sitemap coverage |

### 4.15 Test Senaryoları

| # | Senaryo | Input | Beklenen Output | Status |
|---|---------|-------|-----------------|--------|
| 1 | .php kaldırma | `/music.php` | `/music` (301) | ✅ |
| 2 | index.php kaldırma | `/admin/index.php` | `/admin` (301) | ✅ |
| 3 | Parametre dönüşümü | `/download?id=123` | `/download/123` (301) | ✅ |
| 4 | Lowercase | `/Music/Song` | `/music/song` (301) | ✅ |
| 5 | Double slash | `/music//song` | `/music/song` (301) | ✅ |
| 6 | Trailing slash | `/music/song/` | `/music/song` (301) | ✅ |
| 7 | Query strip | `/music?ref=home` | `/music` (301) | ✅ |
| 8 | Fragment strip | `/music#section` | `/music` (301) | ✅ |
| 9 | Zaten temiz URL | `/music/song` | `/music/song` (200) | ✅ |
| 10 | API endpoint | `/api/v1/users` | `/api/v1/users` (200) | ✅ |

---

## 5. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru | ADR | İhlal Sonucu |
|----------|----------|-----|-------------|
| `.php` uzantısı | Clean URL | ADR-009 | SEO düşüşü |
| `index.php` yönlendirmesi | Kök URL | ADR-009 | Gereksiz redirect |
| Query string (`?page=`) | Path-based (`/music`) | ADR-009 | SEO düşüşü |
| 302 geçici yönlendirme | 301 kalıcı yönlendirme | ADR-009 | SEO etkisi |
| Uppercase URL | Lowercase URL | ADR-009 | Duplicate content |
| Double slash | Tek slash | ADR-009 | URL tutarsızlığı |
| Trailing slash (opsiyonel) | Tutarlı politika | ADR-009 | Duplicate content |
| Fragment (#section) | URL'den çıkar | ADR-009 | Tracking sorunu |
| Hardcoded URL'ler | Dynamic routing | ADR-009 | Bakım zorluğu |
| Redirect chain (uzun) | Max 3 redirect | ADR-009 | Performans düşüşü |

---

## 6. Edge Cases

| Senaryo | Tetikleyici | Çözüm | ADR |
|---------|-------------|-------|-----|
| Redirect loop | A→B→A zinciri | Chain detection + loop break | ADR-009 |
| URL encoded chars | `%20`, `%C3%A9` | Decode → clean → redirect | ADR-009 |
| Case sensitivity | `/Music` vs `/music` | Lowercase normalizasyonu | ADR-009 |
| Query parametre koruma | `?token=abc` | Bazı parametreler korunur | ADR-009 |
| API endpoint | `/api/v1/users.php` | Uzantı kaldır, API koru | ADR-009 |
| Static dosya | `/css/style.css` | Redirect yok, doğrudan sun | ADR-009 |
| Subdomain routing | `music.coremusic.net/music` | Subdomain bazlı routing | ADR-004 |
| SPA fallback | Tanınmayan route | index.php'ye yönlendir | ADR-021 |
| Eski bookmark | Eski URL ile erişim | 301 redirect → yeni URL | ADR-009 |
| Crawler bot | Googlebot, Bingbot | Doğru canonical URL | ADR-009 |
| HTTPS redirect | HTTP erişimi | HTTPS'ye yönlendir | ADR-022 |
| CDN cache | CDN üzerinde eski URL | Cache invalidation | ADR-007 |

---

## 7. Hard Guardrails

| # | Kural | ADR | İhlal Sonucu |
|---|-------|-----|-------------|
| 1 | 301 kalıcı yönlendirme zorunlu (302 yasak) | ADR-009 | SEO düşüşü |
| 2 | Lowercase URL zorunlu | ADR-009 | Duplicate content |
| 3 | .php uzantısı kaldırma zorunlu | ADR-009 | SEO düşüşü |
| 4 | Query string → path dönüşümü zorunlu | ADR-009 | SEO düşüşü |
| 5 | Redirect chain max 3 | ADR-009 | Performans sorunu |
| 6 | Canonical URL zorunlu | ADR-009 | SEO sorunu |
| 7 | SPA fallback zorunlu | ADR-021 | 404 hataları |
| 8 | HTTPS zorunlu | ADR-022 | Güvenlik açığı |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS | Frontend stack |
| [[ADR-004-multi-domain-spa]] | Multi-domain SPA | Subdomain routing |
| [[ADR-016-url-normalization]] | URL normalization | URL temizleme |
| [[ADR-021-spa-router-immutable-contract]] | SPA router | Router contract |
| [[ADR-022-database-hardened-security]] | DB security | HTTPS zorunluluğu |
| [[ADR-042-vault-restructuring-2026-08-03]] | Vault standardı | Bilgi kaynağı |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 Karar | [[ADR-016-url-normalization]] | URL normalizasyonu |
| § 4.1 Apache | [[architecture/l2-routing]] | Routing layer |
| § 4.4 SPA | [[ADR-021-spa-router-immutable-contract]] | SPA router |
| § 4.5 Canonical | [[ADR-004-multi-domain-spa]] | Multi-domain |
| § 5 Yasaklar | [[CLAUDE.md]] §21 | Yasak örüntüleri |
| § 6 Edge | [[ADR-042-vault-restructuring-2026-08-03]] | Edge cases |
| § 7 Guardrails | [[ADR-022-database-hardened-security]] | HTTPS |
| § 8 ADR | [[ADR-001-vanilla-js-itcss]] | Frontend stack |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Clean URL** | Uzantısız, kullanıcı dostu URL yapısı |
| **301 Redirect** | Kalıcı yönlendirme — SEO dostu |
| **302 Redirect** | Geçici yönlendirme — SEO etkisi yok |
| **Canonical URL** | Tercih edilen URL versiyonu |
| **SPA** | Single Page Application |
| **Query String** | URL parametreleri (`?key=value`) |
| **Path-based URL** | Parametrelerin URL path'inde olduğu yapı |
| **Lowercase** | Küçük harf normalizasyonu |
| **Trailing Slash** | URL sonundaki `/` |
| **Double Slash** | `//` çift slash sorunu |
| **Redirect Chain** | Yönlendirme zinciri |
| **Sitemap XML** | Arama motoru site haritası |
| **robots.txt** | Crawler yönlendirme dosyası |
| **Structured data** | Schema.org JSON-LD |
| **Open Graph** | Sosyal medya paylaşım etiketleri |
| **hreflang** | Dil alternatifleri etiketi |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 11 |
| Frozen | 2026-05-15 |
| URL Dönüşüm Kuralları | 9 |
| Yönlendirme Matrisi | 9 |
| Apache/Nginx Config | 2 |
| PHP Sınıfları | 5 |
| JS Router | 1 |
| Test Senaryoları | 10 |
| SEO Özellikleri | 9 |
| Hata Durumları | 6 |
| Yasak Örüntüleri | 10 |
| Edge Cases | 12 |
| Hard Guardrails | 8 |
| ADR References | 6 |
| Cross References | 8 |
| Glossary Terms | 16 |
| Authority | SSOT |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode