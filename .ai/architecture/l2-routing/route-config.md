---
type: architecture
category: l2
title: "Route Configuration — SpaRoute DTO & Registry"
date: 2026-08-16
updated: 2026-08-16
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Route Configuration — SpaRoute DTO & Registry

**Zorunlu Bağlantılar:** [[spa-router]] · [[ADR-083-spa-router]]

**Referans Proje:** `reference-project/coremusic-shared/src/PageRouter/SpaRoute.php`, `RouteRegistry.php`, `home.coremusic.net/config/routes.php`

---

## 1. Amaç

SPA route tanımlarını, `SpaRoute` DTO'sunu ve `RouteRegistry` yapısını tanımlar. Her route bir `SpaRoute` instance'ı ile tanımlanır.

---

## 2. SpaRoute — Immutable DTO

```php
<?php
declare(strict_types=1);

namespace CoreMusic\PageRouter;

/**
 * SpaRoute — Tek bir SPA rotasının tanımı.
 *
 * Tüm alanlar readonly — immutable DTO.
 */
final class SpaRoute
{
    public function __construct(
        public readonly string  $page,                // PHP dosya adı (pages/home.php)
        public readonly bool    $requiresAuth = true, // Auth gerekiyor mu?
        public readonly string  $title        = '',   // Sayfa başlığı
        public readonly ?string $requiredRole = null,  // Gerekli rol (null = yok)
        public readonly ?string $requiredPermission = null, // Gerekli izin (null = yok)
        public readonly bool    $cacheable    = true,  // PHP page cache aktif mi?
        public readonly array   $meta         = [],    // Ek route metadata
        public readonly string  $path         = '',    // URL path (opsiyonel)
        public readonly ?string $handler      = null,  // Controller handler (POST için)
        public readonly ?int    $cacheTtl     = null,  // Özel cache TTL
    ) {}
}
```

### Alan Açıklamaları

| Alan | Tip | Varsayılan | Açıklama |
|------|-----|-----------|----------|
| `page` | `string` | — | PHP dosya adı (uzantısız). `pages/{page}.php` olarak çözümlenir |
| `requiresAuth` | `bool` | `true` | Auth gerektiriyor mu? |
| `title` | `string` | `''` | Sayfa başlığı (HTML `<title>` için) |
| `requiredRole` | `?string` | `null` | Gerekli kullanıcı rolü (null = rol kontrolü yok) |
| `requiredPermission` | `?string` | `null` | Gerekli izin (null = izin kontrolü yok) |
| `cacheable` | `bool` | `true` | PHP page cache aktif mi? |
| `meta` | `array` | `[]` | Ek metadata (frontend'e iletilir) |
| `path` | `string` | `''` | URL path (opsiyonel, genelde key kullanılır) |
| `handler` | `?string` | `null` | Controller handler (POST istekleri için) |
| `cacheTtl` | `?int` | `null` | Özel cache TTL (saniye) |

---

## 3. RouteRegistry — Route Kayıt + Çözümleme

```php
<?php
declare(strict_types=1);

namespace CoreMusic\PageRouter;

/**
 * RouteRegistry — Route tanımlarını yükler ve URI çözümler.
 *
 * Çözümleme önceliği:
 *   1. Tam eşleşme (statik route)
 *   2. Parametre tabanlı eşleşme (pattern matching)
 *   3. null → 404
 */
final class RouteRegistry
{
    /** @var array<string, SpaRoute> */
    private array $routes = [];

    public function register(SpaRoute $route): void
    {
        $this->routes[trim($route->path, '/')] = $route;
    }

    public function loadFromFile(string $filePath): void
    {
        if (!is_file($filePath)) {
            throw new \RuntimeException("Route config dosyası bulunamadı: {$filePath}");
        }
        $routes = include $filePath;
        if (!is_array($routes)) {
            throw new \RuntimeException('Route config bir array döndürmelidir.');
        }
        foreach ($routes as $key => $route) {
            if (!$route instanceof SpaRoute) continue;
            $this->routes[trim((string)$key, '/')] = $route;
        }
    }

    public function resolve(string $uri): ?SpaRoute
    {
        $normalized = trim($uri, '/');
        if ($normalized === '') return $this->routes['home'] ?? null;
        if (isset($this->routes[$normalized])) return $this->routes[$normalized];

        foreach ($this->routes as $pattern => $route) {
            if ($this->matchesPattern($pattern, $normalized)) {
                return $route;
            }
        }
        return null;
    }

    public function getProtectedRouteKeys(): array
    {
        $protected = [];
        foreach ($this->routes as $key => $route) {
            if ($route->requiresAuth) $protected[] = $key;
        }
        return $protected;
    }

    private function matchesPattern(string $pattern, string $uri): bool
    {
        if (!str_contains($pattern, '{')) return false;
        $regex = preg_replace('/\{[a-zA-Z_][a-zA-Z0-9_]*\}/', '[^/]+', $pattern);
        $regex = '#^' . $regex . '$#';
        return (bool) preg_match($regex, $uri);
    }
}
```

---

## 4. Route Tanım Örnekleri (routes.php)

```php
<?php
declare(strict_types=1);

use CoreMusic\PageRouter\SpaRoute;

// ── Auth URL Constants ──────────────────────────────────────────────────
$authDomain  = 'http://auth.coremusic.net';
$homeDomain  = 'http://home.coremusic.net';
$clientId    = 'coremusic-web';
$callbackUrl = $homeDomain . '/auth/callback';

return [

    // ── Auth Redirect Routes ─────────────────────────────────────────────
    'login' => new SpaRoute(
        page:         'redirect',
        requiresAuth: false,
        title:        'Giriş',
        meta:         ['redirect_to' => "$authDomain/login?client_id=$clientId&response_type=session&redirect_uri=$callbackUrl"],
    ),

    'register' => new SpaRoute(
        page:         'redirect',
        requiresAuth: false,
        title:        'Kayıt',
        meta:         ['redirect_to' => "$authDomain/register?client_id=$clientId&response_type=session&redirect_uri=$callbackUrl"],
    ),

    'logout' => new SpaRoute(
        page:         'redirect',
        requiresAuth: false,
        title:        'Çıkış',
        meta:         ['redirect_to' => "$authDomain/logout?redirect=$homeDomain/"],
    ),

    // ── Auth-Required Routes ─────────────────────────────────────────────
    'home' => new SpaRoute(
        page:         'home',
        requiresAuth: true,
        title:        'Ana Sayfa',
        cacheable:    true,
        meta:         ['ttlType' => 'user'],
    ),

    'kesfet' => new SpaRoute(
        page:         'kesfet',
        requiresAuth: true,
        title:        'Keşfet',
        cacheable:    true,
        meta:         ['ttlType' => 'user'],
    ),

    'albumler' => new SpaRoute(
        page:         'albumler',
        requiresAuth: true,
        title:        'Albümler',
        cacheable:    true,
        meta:         ['ttlType' => 'user'],
    ),

    'sanatcilar' => new SpaRoute(
        page:         'sanatcilar',
        requiresAuth: true,
        title:        'Sanatçılar',
        cacheable:    true,
        meta:         ['ttlType' => 'user'],
    ),

    'ayarlar' => new SpaRoute(
        page:         'ayarlar',
        requiresAuth: true,
        title:        'Ayarlar',
    ),

    // ── Parameterized Routes ─────────────────────────────────────────────
    'album/{id}' => new SpaRoute(
        page:         'album',
        requiresAuth: false,
        title:        'Albüm',
        cacheable:    true,
        cacheTtl:     3600,
        meta:         ['ttlType' => 'static'],
    ),

    'artist/{id}' => new SpaRoute(
        page:         'artist',
        requiresAuth: false,
        title:        'Sanatçı',
        cacheable:    true,
        cacheTtl:     3600,
        meta:         ['ttlType' => 'static'],
    ),

    'playlist/{id}' => new SpaRoute(
        page:         'playlist',
        requiresAuth: false,
        title:        'Çalma Listesi',
        cacheable:    true,
        cacheTtl:     1800,
        meta:         ['ttlType' => 'static'],
    ),
];
```

---

## 5. Route Çözümleme Akışı

```
GET /album/42
    │
    ▼
RouteRegistry::resolve('album/42')
    │
    ├── 1. Tam eşleşme: 'album/42' → yok
    │
    ├── 2. Pattern eşleşme: 'album/{id}' → eşleşti ✓
    │
    ▼
SpaRoute(page: 'album', requiresAuth: false, cacheable: true, cacheTtl: 3600)
    │
    ▼
PageRouter::renderRoute()
    │
    ├── resolvePageFile(): pages/album.php
    │
    ├── renderPageWithCache(): cache varsa kullan
    │
    ▼
RouteResult::ok($container, $meta, $csrfToken, 'album/42')
```

---

## 6. Parametre Pattern eşleme

| Pattern | URI | Eşleşme |
|---------|-----|---------|
| `album/{id}` | `album/42` | ✅ |
| `artist/{id}` | `artist/john-doe` | ✅ |
| `playlist/{id}` | `playlist/123` | ✅ |
| `user/{id}/playlist/{pid}` | `user/5/playlist/99` | ✅ |
| `album/{id}` | `album/42/tracks` | ❌ (fazla segment) |

**Regex dönüşümü:** `{id}` → `[^/]+`

---

## 7. Auth Redirect Routes

Auth gerektiren sayfalar auth.coremusic.net'e yönlendirilir:

| Route | Auth URL | Callback |
|-------|----------|----------|
| `login` | `auth.coremusic.net/login` | `home.coremusic.net/auth/callback` |
| `register` | `auth.coremusic.net/register` | `home.coremusic.net/auth/callback` |
| `logout` | `auth.coremusic.net/logout` | `home.coremusic.net/` |
| `forgot-password` | `auth.coremusic.net/forgot-password` | `home.coremusic.net/login` |
| `reset-password` | `auth.coremusic.net/reset-password` | `home.coremusic.net/login` |

**ADR-043 Uyumlu:** Auth domain scheme `DomainConfig`'ten alınır, hardcoded URL yasak.

---

## 8. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **Eşleşmeyen route** | `RouteResult::notFound()` | ADR-021 |
| **Auth required + giriş yok** | `AuthGuard::check()` → redirect | ADR-043 |
| **Rol yetkisi yok** | `RouteResult::forbidden()` | ADR-011 |
| **POST + handler tanımlı** | Controller'a yönlendir | ADR-083 |
| **Cache devre dışı** | Doğrudan render | — |
| **Debug modda eksik sayfa** | Placeholder render | — |

---

## 9. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[spa-router]] | PHP SPA PageRouter |
| [[html-shell-renderer]] | HTML shell üretimi |
| [[ADR-083-spa-router]] | SPA Router Architecture |
| [[ADR-021-spa-router-immutable-contract]] | SPA router contract |

---

## 10. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **ADR Uyumlu** | ✅ 021, 083 |
| **Zero Hallucination** | ✅ (referans proje tabanlı) |

---

## 11. Referans ↔ Gerçek Eşleştirme (Faz 2c — 2026-09-08)

| Öğe | Bu Dokümanda | Gerçek Durum | Not |
|-----|--------------|--------------|-----|
| `SpaRoute` DTO | §2 (10 alan, readonly) | `shared/src/PageRouter/` — isim düzeyi doğrulandı | Alan teyidi kod okumasıyla (devam görevi) |
| `RouteRegistry` | §3 (register/loadFromFile/resolve) | Aynı klasörde beklenir | İmza teyidi devam görevi |
| Route örnekleri | §4 (login/register/logout/home/kesfet/albumler/sanatcilar/ayarlar + 3 parametrik) | `routes.php` (3.3KB) incelemesi bekliyor | **l2 index §38 inceleme planı** — bu örnekler referans kaynaklıdır |
| Route dosya konumu | `home.coremusic.net/config/routes.php` | `shared/config/routes.php` (3.3KB) + `auth-routes.php` (1.6KB) + home `config/` | Çoklu konum — hangisi kanonik, kod okumasında netleşecek |
| `kesfet` yazımı | Türkçe karakter içermeyen slug | ui-design screens C-music | `keşfet` vs `kesfet` tutarlılığı kod teyidi |

**Sonuç:** DTO/Registry tasarımı IMPLEMENTED kabulüne yakın; route listesi kanıt bekler. Bu ayrım §17 test senaryolarını da şartlar.

---

## 12. SpaRoute Alanı ↔ Guard Tüketimi (Çapraz)

[[guard-pipeline]] §20 tablosuyla birebir eşleşme:

| SpaRoute Alanı | Guard Kontrolü | Karar |
|----------------|----------------|-------|
| `requiresAuth` | #1 checkAuthRequired | redirect login |
| `requiredRole` | #2 checkRole | forbidden |
| `requiredPermission` | #3 checkPermission | forbidden |
| `page` | render hedefi (adım 9) | `pages/{page}.php` |
| `meta.ttlType` | PageCache anahtarı | user/static |
| `cacheable` + `cacheTtl` | render öncesi önbellek | kullan/üret/atla |

Kural: Alan eklemek hem SpaRoute hem Guard hem bu dokümanı değiştirir — üçlü senkron zorunlu (ADR-021).

---

## 13. Cache Politikası (ttlType + PageCache)

| ttlType | Anlam | Beklenen TTL | Örnek Route |
|---------|-------|--------------|-------------|
| `user` | Kullanıcı-bağlı içerik | Kısa/kapalı (session varyasyonu) | home, kesfet, albumler |
| `static` | Ortak içerik | Uzun (1800-3600sn) | album/{id}, artist/{id}, playlist/{id} |

**Kritik etkileşim uyarısı:** `cacheable: true` + CSP nonce'lu shell = **eski nonce sorunudur** (csp.md §21 risk notu). PageCache devreye girmeden önce çözülmesi gereken tasarım sorusu: cache'lenen HTML nonce'suz mu üretilmeli, yoksa cache nonce sonrası mı uygulanmalı? Karar Faz 2c devamında (l0+l2 ortak).

---

## 14. Route Ekleme Prosedürü

1. **Gereksinim:** Sayfa/auth/rol kararı; Guardrail #17 kontrolü (tek bileşen).
2. **Sayfa dosyası:** `pages/{page}.php` — tek bileşen + responsive CSS.
3. **Route kaydı:** `routes.php`'e `SpaRoute` ekle (alanlar §2).
4. **Cache kararı:** cacheable/TTL/ttlType — §13 politika; nonce çözümü tamamlanmadan `cacheable: true` + nonce'lu shell kombinasyonuna onay verilmez.
5. **Guard etkisi:** requiresAuth/role değerlerini AuthGuard zincirinde simüle et (guard-pipeline §16 senaryoları).
6. **Test:** §17 senaryolardan ilgili olanlar.
7. **Doküman:** bu dosya §4 örnek listesi + l2 index §24; log.md kaydı.

Yasak: routes.php dışından route tanımı, ad-hoc include, auth'suz state değiştiren route (bypass dışı).

---

## 15. Auth Redirect Meta Sözleşmesi

§4 örneklerindeki `meta.redirect_to` kalıbı kanıtlı bir sözleşmedir (referans kod):

```text
authDomain + /login?client_id=coremusic-web&response_type=session&redirect_uri={callback}
logout:  authDomain + /logout?redirect={homeDomain}/
```

| Parametre | Anlam |
|-----------|-------|
| `client_id` | SPA istemci kimliği (`coremusic-web`) |
| `response_type=session` | Hybrid session akışı (ADR-043; OAuth code değil) |
| `redirect_uri` | Callback: `home.../auth/callback` (HomeAuthBridge devri) |
| `redirect` (logout) | Logout sonrası hedef |

**Hardcoded URL uyarısı:** Örnek `http://auth.coremusic.net` sabit yazar; §7 kural "DomainConfig'den alınır, hardcoded yasak" der. Gerçek config okuması hangisinin geçerli olduğunu netleştirecek — şüpheli satır `DOĞRULAMA GEREKLİ` kalır.

---

## 16. Pattern Eşleme Ek Kuralları

1. `{param}` adları `[a-zA-Z_][a-zA-Z0-9_]*` — sayı ile başlayan parametre adı geçersiz.
2. Pattern yalnız tam segment eşler: `{id}` çapraz segment yakalamaz (§6 ❌ satırı).
3. Key normalize: `trim($path, '/')` — leading/trailing slash kaybolur (ADR-016).
4. Boş uri → `'home'` route (Registry resolve satır 118).
5. İlk pattern kazanır — tanım sırası davranışı etkiler; çakışan pattern yazılmaz.

---

## 17. Route Test Senaryoları

| # | Senaryo | Beklenen |
|---|---------|----------|
| 1 | `/` (boş uri) | home route |
| 2 | `/home` auth'suz | Guard #1 → login redirect |
| 3 | `/album/42` | pattern eşleşir, auth'suz 200, cache 3600 |
| 4 | `/album/42/tracks` | pattern eşleşmez → 404 |
| 5 | `/user/5/playlist/99` | çok parametrik pattern (tanımlıysa) eşleşir |
| 6 | `/login` | redirect page + meta.redirect_to |
| 7 | `/logout` auth'lu | Guard #6 → auth logout |
| 8 | `cacheable=false` route | her istekte taze render |
| 9 | handler'lı POST | controller'a yönlendirme |
| 10 | eksik sayfa dosyası | debug: placeholder / prod: 404 |

---

## 18. Diagnostics (tekrarlanabilir)

```powershell
# 1. RouteRegistry/SpaRoute gerçek konum
Get-ChildItem -LiteralPath "shared\src\PageRouter" -Filter "*.php" | Where-Object { $_.Name -match "Route|Spa" } | Select-Object Name

# 2. Gerçek route config konumları
Get-ChildItem -LiteralPath "shared\config" | Select-Object Name, Length
Get-ChildItem -LiteralPath "home.coremusic.net\config" -ErrorAction SilentlyContinue | Select-Object Name, Length

# 3. Kanonik route örnek adları gerçek config'te var mı (kesfet vb.)
Select-String -LiteralPath "shared\config\routes.php" -Pattern "kesfet|albumler|sanatcilar" -ErrorAction SilentlyContinue

# 4. Auth callback hattı
Select-String -LiteralPath "shared\config\routes.php" -Pattern "callback|redirect_to|client_id" -ErrorAction SilentlyContinue
```

---

## 19. Ek SSS

**S: `response_type=session` ne demek — OAuth değil mi?**
C: ADR-043 hybrid session akışı: tam OAuth code exchange yok; auth domain oturumu kurup callback'e auth_key taşır (HomeAuthBridge TTL 300sn). OAuth semantiği yalnız param adlandırmasında yaşar.

**S: Route sayısı kaç?**
C: DOĞRULAMA GEREKLİ — routes.php incelemesi (l2 index §38) tamamlanmadan sayı yazılamaz. §4 örnekleri 11 route gösterir (referans).

**S: `handler` alanı ne zaman dolu?**
C: POST işleyen rotalar (form submit, API benzeri eylemler). Gerçek config incelemesinde dolduğu görülecek — örneklerde hepsi null.

**S: İki route config dosyası çakışırsa?**
C: Yüklenme sırası Registry'de belirlenir; son yüklenen aynı key'i ezer. Çakışma istenmeyen durumdur — kanonik tek kaynak kararı Faz 2c devamında verilecek.

**S: `kesfet` Türkçe karakterli olsaydı?**
C: Slug'lar ASCII-küçük harf standardıdır (ADR-016 normalize uyumu); Türkçe görüntü adı `title` alanında taşınır ('Keşfet').

**S: Bu doküman referans kodu kopyalayabilir miyim?**
C: Hayır (WORKFLOW §8.1C) — gerçek kod `shared/src/PageRouter/`'da. Burası sözleşme referansıdır.

## 21. SpaRoute Varyant Senaryoları

**Senaryo 1 — Yeni korumalı sayfa (sanatci-detay):**

```php
'sanatci-detay/{id}' => new SpaRoute(
    page: 'sanatci-detay', requiresAuth: true, title: 'Sanatçı Detayı',
    cacheable: false, meta: ['ttlType' => 'user'],
)
```
Gerekçe: kullanıcı-bağlı içerik (takip durumu) → cache yok.

**Senaryo 2 — Herkese açık statik sayfa (hakkimizda):**

```php
'hakkimizda' => new SpaRoute(
    page: 'hakkimizda', requiresAuth: false, title: 'Hakkımızda',
    cacheable: true, cacheTtl: 86400, meta: ['ttlType' => 'static'],
)
```
Gerekçe: kullanıcı varyasyonu yok → uzun TTL güvenli (nonce sorununa kadar HTML'de nonce yoksa).

**Senaryo 3 — POST işleyen form route (iletisim):**

```php
'iletisim' => new SpaRoute(
    page: 'iletisim', requiresAuth: false, title: 'İletişim',
    handler: 'ContactHandler::submit',
)
```
Gerekçe: GET render + POST handler ayrımı; CSRF zorunluluğu L1'den gelir.

---

## 22. Alan Ekleme Etki Analizi

SpaRoute'a yeni alan eklenirse (ADR-021 kapsamında):

| Etkilenen | Değişiklik |
|-----------|------------|
| SpaRoute.php | readonly property + constructor |
| GuardPipeline | ilgili kontrol metodu |
| PageRouter | render/dispatch davranışı |
| routes.php | mevcut kayıtlar (varsayılan değerle uyum) |
| route-config.md | §2 alan tablosu |
| guard-pipeline.md | §20 tüketim tablosu |

Kural: tek alan = 6 nokta senkronu; eksik nokta sürüm sapması üretir.

---

## 23. Risk Kaydı (Route Config)

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 1 | Referans route listesinin gerçek sanılması | Yüksek | Orta | §11 eşleştirme + §38 inceleme planı |
| 2 | Hardcoded auth URL'in kodda kalması | Orta | Orta | §15 uyarı — DomainConfig kuralı |
| 3 | cacheable+nonce kombinasyonu | Orta | Yüksek | §13 uyarı — çözülmeden onay yok |
| 4 | Türkçe slug tutarsızlığı | Düşük | Düşük | §19 SSS kural |
| 5 | Çoklu config dosyası ezilmesi | Orta | Orta | Yükleme sırası kuralı (SSS) |

---

## 25. Slug Standardı

| Kural | Örnek | Not |
|-------|-------|-----|
| ASCII küçük harf | `kesfet`, `albumler` | ADR-016 normalize uyumu |
| Tire ayraç | `sanatci-detay` | boşluk yasak |
| Türkçe karakter yok | `kesfet` (title 'Keşfet') | kod tanımlayıcı standardı |
| Parametre adları | `{id}`, `{pid}` | camelCase |
| Sayı ile başlama yasağı | `{id}` ✓, `{1id}` ✗ | regex kısıtı (§16) |

---

## 26. Cache TTL Matrisi (Örnek Setinden)

| Route Grubu | ttlType | TTL | Gerekçe |
|-------------|---------|-----|---------|
| Ana sayfa/keşfet/listeler | user | cacheable=true, TTL tanımsız | kullanıcı varyasyonu — PageCache politikası çözülmeli (§13) |
| Albüm/sanatçı detay | static | 3600 | ortak içerik |
| Playlist | static | 1800 | orta tazelik |
| Ayarlar | — | cacheable tanımsız (true varsayılan!) | **dikkat**: varsayılan true — kullanıcı verisi sayfada TTL'siz cache tehlikeli; gerçek config teyidi şart |

**Uyarı:** `cacheable` varsayılanı `true`'dur (§2 DTO) — kullanıcı-bağlı sayfalarda açıkça `false` yazılmalı; aksi halde varsayılan davranış hatalı cache üretebilir. Bu, DTO varsayılan revizyon adayıdır (ADR-021 kapsam).

---

## 27. Ek SSS

**S: `meta` alanına ne konabilir?**
C: Serbest dizi — frontend'e iletilir. Bilinen anahtarlar: `redirect_to`, `ttlType`. Serbestlik sözleşme değil — kullanım disiplini §22 etki analiziyle sınırlanır.

**S: TTL süresi bittiğinde ne olur?**
C: PageCache miss → taze render → yeniden cache. Stampede koruması (mutex) PLANNED (l0 §8).

**S: requiresAuth=false ama içerik kişisel olabilir mi?**
C: Olmamalı — auth'suz cache'lenebilir sayfalar ortak içerik taşıır. Kişisel veri API hattından (auth sonrası) gelir; shell ortaktır. Bu ayrım ADR-083 hibrit tasarımının özüdür.

**S: Registry config'i ne zaman yükler?**
C: `loadFromFile()` çağrısında — PageRouter başlatımında bir kez; process içi bellek. Hot-reload yok (dev'de web server restart).

**S: Route key ile `path` alanı çakışırsa?**
C: Registry key'i (`trim($key,'/')`) esas alır; `path` alanı opsiyonel/ikincildir (§2 alan tablosu). Tutarlılık için key kullanımı önerilir.

**S: Pattern içinde literal `{` gerekirse?**
C: Regex dönüşümü her `{...}`'ı param sanar — literal süslü parantezli route tasarlanmamalı (URL standardı dışı zaten).

**S: `/home` ile `/home/` ayrı route mu?**
C: Hayır — Registry key normalize edilir (`trim('/')`); trailing slash'lı key yazılmaz. İstek tarafında trailing slash ADR-016 ile 301'e iner — tek route, tek kanonik adres.

**S: SpaRoute readonly ama theme/title runtime değişebilir mi?**
C: Hayır — DTO immutable'dır; çalışma anı değişimi (örn. meta.redirect_to dinamik üretimi) config yükleme aşamasında yapılır, sonra kilitlenir. Runtime override ihtiyacı yeni ADR konusudur.

**S: `cacheTtl` ile PageCache TTL ilişkisi?**
C: SpaRoute.cacheTtl route-bazlı özelleştirmedir; PageCache katmanının genel TTL politikasıyla birleşir (route değeri kazanır). İkisinin de "cacheable+nonce" uyarısıyla birlikte okunması şart (§13).

**S: Yeni route'un AuthGuard etkisini hızlı test etme?**
C: guard-pipeline §16 senaryo tablosundaki 12 vakadan ilgili olanları konsol/DevTools ile koş; kritik üçlü: auth'suz erişim, rol dışı erişim, callback akışı.

**S: Route sayısı arttıkça resolve performansı?**
C: Tam eşleşme O(1) ( associative dizi); pattern taraması O(n) — n küçük (~15 route) için önemsiz. 50+ route'da pattern index'i PLANNED iyileştirmedir.

---

## 28. Registry Yükleme Sırası

```
PageRouter başlatımı
  → RouteRegistry örneği
     → loadFromFile(ana config: shared/config/routes.php)
        → loadFromFile(auth config: auth-routes.php)   [sonra yüklenir — auth key'leri ezme hakkı]
           → (domain config'leri: home/config/ vb. — sıra kod teyidi bekliyor)
```

Uyarı: Son yüklenen aynı key'i ezer (§19 SSS). Bu nedenle **auth-routes.php genel config'ten SONRA** yüklenmelidir — tersi durumda auth rotaları genel tanımla ezilebilir. Gerçek yükleme sırası kod teyidi bekliyor (§18 komut 2).

---

## 29. Kapanış Kontrol Listesi (Route İşlerinde)

| # | Kontrol | Referans |
|---|---------|----------|
| 1 | Slug standardı | §25 |
| 2 | cacheable/TTL/ttlType üçlüsü | §13, §26 |
| 3 | Guard etkisi simülasyonu | §12 + guard-pipeline §16 |
| 4 | Auth redirect parametreleri | §15 |
| 5 | Sayfa dosyası tek bileşen | Guardrail #17 |
| 6 | log.md kaydı | WORKFLOW kuralı |

---

## 24. Revizyon Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 1.0.0 | 2026-08-16 | İlk doküman (referans proje tabanlı) |
| 2.0.0 | 2026-09-08 | Faz 2c: §11 referans↔gerçek eşleştirme; §12 guard çaprazı; §13 cache/nonce politika uyarısı; §14 ekleme prosedürü; §15 meta sözleşmesi + hardcoded uyarısı; §16-§18 kurallar/test/diagnostics; §19 SSS |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-08
**Mode:** Red Team · Human Mode · Truth Mode
