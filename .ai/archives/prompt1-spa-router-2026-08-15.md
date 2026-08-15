---
title: "CoreMusic — Prompt 1: SPA Router Mimarisi"
type: prompt
category: routing
date: 2026-08-15
updated: 2026-08-15
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
token_limit: 4000
layers:
  primary: L2-Routing
  secondary: L3-Presentation
reference:
  authority: ".ai/CLAUDE.md"
  shared_base: ".ai/archives/prompt-shared-base.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/brain.md"
  architecture:
    - ".ai/architecture/l2-routing/"
    - ".ai/architecture/l3-presentation/"
    - ".ai/architecture/l1-security/"
  adr:
    - ".ai/decisions/accepted/ADR-001-vanilla-js-itcss.md"
    - ".ai/decisions/accepted/ADR-004-multi-domain-spa.md"
    - ".ai/decisions/accepted/ADR-009-clean-url-redirect.md"
    - ".ai/decisions/accepted/ADR-016-url-normalization.md"
    - ".ai/decisions/accepted/ADR-021-spa-router-immutable-contract.md"
    - ".ai/decisions/accepted/ADR-043-auth-subdomain-consolidation.md"
    - ".ai/decisions/accepted/ADR-045-multi-domain-view-mode-architecture.md"
    - ".ai/decisions/accepted/ADR-046-cross-view-state-preservation.md"
    - ".ai/decisions/accepted/ADR-047-login-redirect-session-bridge.md"
    - ".ai/decisions/accepted/ADR-048-view-transition-api-integration.md"
    - ".ai/decisions/accepted/ADR-083-spa-router.md"
  prompts:
    - ".ai/archives/prompt-shared-base.md"
changelog:
  - version: 2.0.0
    date: 2026-08-15
    changes:
      - Tamamen yeniden yazım — SOLID, Clean Code, L0-L6 uyumlu
      - L2/L3 katman odaklı tasarım
      - Vault cross-reference eklendi
---

# CoreMusic — Prompt 1: SPA Router Mimarisi

**Ortak Temel:** [[prompt-shared-base]] (ROLE, sistem tanımı, L0-L6, SOLID, Clean Code — bu dosyada tekrar edilmez)

**Zorunlu Bağlantılar:** [[../../CLAUDE.md]] · [[../../AGENTS.md]] · [[../../brain.md]] · [[../../architecture/l2-routing/index]] · [[../../architecture/l3-presentation/index]]

**Kullanım Anı:** SPA router geliştirme, route tasarımı, frontend routing görevleri
**Sorumlu Agent'lar:** Backend Architect (L2), UI Designer (L3)

---

## 1. Temel Referans

Bu prompt, CoreMusic SPA Router mimarisini **L2 Routing** ve **L3 Presentation** katmanları düzeyinde tanımlar. Enterprise seviyesinde, SOLID prensiplerine uygun, PSR standartlarında bir router tasarımı hedeflenir.

**Kritik Not:** Mevcut router KULLANILMAYACAKTIR. Sıfırdan Enterprise seviyesinde bir router tasarlanacaktır. Mevcut `C:\www\coremusic.net.old.ref` içindeki auth kodları, router, middleware, session sistemi, login sistemi, controller yapısı ve service yapısı **KESİNLİKLE kopyalanmayacaktır.** Sadece mimari referans olarak incelenecektir.

---

## 2. SPA Router Vizyonu

*Detaylı metadata: [[../../architecture/l2-routing/spa-router]]*

### 2.1 L2 Routing Katmanı

SPA Router, L2 Routing katmanında yer alır. Bu katman:

- **Görev:** HTTP isteklerini doğru controller/handler'a yönlendirme
- **Bağımlılık:** L1 Security (middleware pipeline) kullanır
- **Bağımlılık:** L3 Presentation (frontend) tarafından kullanılır
- **Kurallar:** L0/L1'e bağımlı olamaz, L3'e bağımlı olamaz

### 2.2 Router Mimarisi Prensibi

```
SPA (L3) → Router (L2) → Middleware Pipeline (L1) → Controller → Response
```

SPA **asla** middleware, session, CSRF, auth doğrudan görmez. Tüm güvenlik kararları backend'de (L1) verilir.

---

## 3. Enterprise Router Gereksinimleri

*Detaylı metadata: [[ADR-083]], [[ADR-021]]*

| # | Özellik | Açıklama | ADR |
|---|---------|----------|-----|
| 1 | Enterprise | Kurumsal seviye, büyük projelere uygun | ADR-083 |
| 2 | SOLID | Tek sorumluluk, açık kapalılık, yerine koyma, arayüz ayrımı, bağımlılık tersi | ADR-083 |
| 3 | PSR Uyumlu | PSR-7 (HTTP Message), PSR-15 (Middleware), PSR-17 (HTTP Factories) | ADR-083 |
| 4 | Middleware Destekli | Her route'a ayrı middleware eklenebilir | ADR-083 |
| 5 | Subdomain Destekli | `music.coremusic.net` gibi subdomain bazlı routing | ADR-016 |
| 6 | Route Group | Prefix gruplama (ör: `/api/v1/*`) | ADR-083 |
| 7 | Attribute Destekli | PHP 8 attribute ile route tanımı | ADR-083 |
| 8 | Route Cache | Production'da route cache (file-based veya APCu) | ADR-007 |
| 9 | Dependency Injection | php-di/php-di entegrasyonu (PSR-11) | ADR-083 |
| 10 | Fluent API | Zincirleme method çağrısı ile route tanımlama | ADR-083 |

---

## 4. Router Mimari Diyagramı

*Detaylı metadata: [[../../architecture/l2-routing/spa-router]] §1A*

### 4.1 Klasör Yapısı

```
shared/src/Router/
├── Router.php                    ← Ana router (nikic/fast-route wrap)
├── RouteDefinition.php           ← Fluent middleware/naming API
├── GroupDefinition.php           ← Prefix grouping
├── Attributes/
│   ├── Route.php                 ← #[Route('GET', '/path')]
│   ├── Middleware.php            ← #[Middleware(['auth', 'csrf'])]
│   ├── Guard.php                 ← #[Guard('admin')]
│   └── Group.php                 ← #[Group('/api/v1')]
├── Cache/
│   └── RouteCache.php            ← File/APCu-based route cache
└── Contracts/
    └── RouterInterface.php       ← PSR-15 uyumlu arayüz
```

### 4.2 Request Akışı

```
HTTP Request
  → SubdomainParser (subdomain çıkarma)
    → RouteCollector (route bulma)
      → RouteMatch (parametre çıkarma)
        → MiddlewarePipeline (L1 katmanı)
          → ControllerResolver (controller bulma)
            → Handler (işlem)
              → Response
```

---

## 5. Route Tanımlama Yöntemleri

### 5.1 Attribute-Based Routing (PHP 8+)

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Music\Controller;

use CoreMusic\Router\Attribute\Route;
use CoreMusic\Router\Attribute\Middleware;
use CoreMusic\Router\Attribute\Guard;

#[Route('GET', '/albums')]
#[Middleware(['session', 'csrf'])]
#[Guard('regular')]
final class AlbumController
{
    public function __invoke(): Response
    {
        // Album listeleme
    }
}
```

### 5.2 Fluent API Routing

```php
<?php

declare(strict_types=1);

$router->get('/albums', AlbumController::class)
    ->middleware('session', 'csrf')
    ->guard('regular')
    ->name('albums.index');

$router->post('/albums', AlbumStoreController::class)
    ->middleware('session', 'csrf', 'auth')
    ->guard('premium')
    ->name('albums.store');
```

### 5.3 Route Group

```php
<?php

declare(strict_types=1);

$router->group('/api/v1', function ($group) {
    $group->get('/albums', AlbumApiController::class);
    $group->post('/albums', AlbumCreateApiController::class);
    $group->get('/albums/{id}', AlbumDetailApiController::class);
})->middleware('auth', 'rate-limit')
  ->guard('api');
```

---

## 6. Middleware Pipeline (L2 Perspektifi)

*Detaylı metadata: [[../../architecture/l1-security/middleware]]*

L2 Routing katmanı, L1 Security katmanındaki middleware pipeline'ı kullanır:

```
Route Match (L2)
  → OriginCheck (L1) → Cors (L1) → RateLimiter (L1)
    → SecurityHeaders (L1) → SessionManager (L1)
      → Csrf (L1) → BypassAuth (L1) → Auth (L1)
        → Permission (L1) → Validation (L1)
          → Controller (L2)
```

### 6.1 Route Bazlı Middleware

Her route kendi middleware zincirini tanımlayabilir:

| Route | Middleware | Guard |
|-------|-----------|-------|
| `GET /` | session | guest |
| `GET /albums` | session, csrf | regular |
| `POST /albums` | session, csrf, auth | premium |
| `GET /admin/*` | session, csrf, auth | admin |
| `POST /api/*` | auth, rate-limit | api |

---

## 7. Subdomain Routing

*Detaylı metadata: [[ADR-016]], [[ADR-043]]*

### 7.1 Subdomain Eşleşmesi

| Subdomain | Controller Namespace | Middleware |
|-----------|---------------------|------------|
| `auth.coremusic.net` | `CoreMusic\Auth\` | session, csrf |
| `music.coremusic.net` | `CoreMusic\Music\` | session, csrf, auth |
| `admin.coremusic.net` | `CoreMusic\Admin\` | session, csrf, auth |
| `home.coremusic.net` | `CoreMusic\Home\` | session |
| `car.coremusic.net` | `CoreMusic\Car\` | session, auth |
| `studio.coremusic.net` | `CoreMusic\Studio\` | session, auth |
| `pro.coremusic.net` | `CoreMusic\Pro\` | session, auth |
| `api.coremusic.net` | `CoreMusic\Api\` | auth, rate-limit |
| `download.coremusic.net` | `CoreMusic\Download\` | session, auth |
| `media.coremusic.net` | `CoreMusic\Media\` | session, auth |

### 7.2 Subdomain Routing Kodu

```php
<?php

declare(strict_types=1);

$router->subdomain('auth', function ($auth) {
    $auth->get('/login', [AuthController::class, 'login']);
    $auth->post('/login', [AuthController::class, 'authenticate']);
    $auth->post('/logout', [AuthController::class, 'logout']);
    $auth->post('/register', [AuthController::class, 'register']);
})->middleware('session', 'csrf');

$router->subdomain('music', function ($music) {
    $music->get('/', [MusicController::class, 'index']);
    $music->get('/albums', [AlbumController::class, 'index']);
    $music->get('/artists', [ArtistController::class, 'index']);
})->middleware('session', 'csrf', 'auth');
```

---

## 8. SOLID Uygulaması (Router İçin)

### 8.1 Single Responsibility (SRP)

| Sınıf | Sorumluluk |
|-------|------------|
| `Router` | Sadece route eşleştirme |
| `RouteDefinition` | Sadece route tanımı |
| `GroupDefinition` | Sadece grup tanımı |
| `MiddlewarePipeline` | Sadece middleware yürütme |
| `RouteCache` | Sadece cache yönetimi |

### 8.2 Open/Closed (OCP)

Yeni route eklemek mevcut kodu değiştirmez:

```php
// Yeni route eklemek için mevcut kod DEĞİŞMEZ
$router->get('/new-feature', NewFeatureController::class);
```

### 8.3 Liskov Substitution (LSP)

Tüm controller'lar `RequestHandlerInterface`'i uygular:

```php
interface RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface;
}
```

### 8.4 Interface Segregation (ISP)

İnce arayüzler:

```php
interface RouteCollectorInterface
{
    public function addRoute(string $method, string $path, callable $handler): void;
}

interface RouteMatcherInterface
{
    public function match(string $method, string $uri): ?RouteMatch;
}

interface MiddlewarePipelineInterface
{
    public function process(ServerRequestInterface $request): ResponseInterface;
}
```

### 8.5 Dependency Inversion (DIP)

Router interface'lere bağımlıdır:

```php
final class Router
{
    public function __construct(
        private readonly RouteCollectorInterface $collector,
        private readonly RouteMatcherInterface $matcher,
        private readonly MiddlewarePipelineInterface $pipeline,
        private readonly RouteCacheInterface $cache,
    ) {}
}
```

---

## 9. Clean Code (L3 Presentation)

*Detaylı metadata: [[ADR-001]], [[../../architecture/l3-presentation/vanilla-js-rules]]*

### 9.1 Vanilla JS Kuralları

| Kural | Açıklama |
|-------|----------|
| Framework yasak | React, Vue, Angular kullanılmaz |
| `const` / `let` | `var` yasak |
| `async` / `await` | Callback hell yasak |
| DOMParser | `innerHTML` yasak |
| TrustedTypes | XSS koruması |
| ES6 modules | `require()` yasak |

### 9.2 SPA Router (JS Tarafı)

```javascript
// Vanilla JS SPA Router — ADR-001 uyumlu
'use strict';

const SpaRouter = {
    routes: new Map(),
    
    init() {
        window.addEventListener('popstate', () => this.navigate(location.pathname));
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[data-spa]');
            if (link) {
                e.preventDefault();
                this.navigate(link.getAttribute('href'));
            }
        });
    },
    
    async navigate(path) {
        history.pushState(null, '', path);
        const response = await fetch(path, { headers: { 'X-SPA-Request': '1' } });
        const html = await response.text();
        const doc = new DOMParser().parseFromString(html, 'text/html');
        document.querySelector('main').replaceWith(doc.querySelector('main'));
    }
};

SpaRouter.init();
```

---

## 10. Auth Entegrasyonu

*Detaylı metadata: [[ADR-043]], [[ADR-047]]*

### 10.1 SPA ↔ Auth İletişimi

```
SPA (L3) → Fetch API → auth.coremusic.net (L1)
  → Middleware Pipeline (L1)
    → Session Check → Auth Check → Response
      → SPA (L3) → UI Update
```

### 10.2 Login Redirect

Kullanıcı giriş yapmadığında:

```
SPA → /login redirect → auth.coremusic.net/login
  → Login form → POST /auth/login
    → Success → session cookie → redirect to original URL
      → SPA → Protected content
```

### 10.3 Cross-Origin Koruması

- Sadece beyaz listedeki subdomainler iletişim kurabilir
- CORS header'ları严格 olarak yapılandırılmıştır
- `credentials: 'include'` ile cookie paylaşımı

---

## 11. State Management

*Detaylı metadata: [[ADR-045]], [[ADR-046]], [[ADR-048]]*

### 11.1 View Mode

| Mod | Subdomain | Özellik |
|-----|-----------|---------|
| v-home | home.coremusic.net | Touch-friendly, basit |
| v-pro | pro.coremusic.net | Profesyonel, detaylı |
| v-studio | studio.coremusic.net | 8.1 surround, multi-track |
| v-car | car.coremusic.net | Touch-optimized, basitleştirilmiş |
| v-admin | admin.coremusic.net | CRUD, dashboard |

### 11.2 Cross-View State

Geçerli durum koruması — sayfa geçişlerinde state korunur:

```javascript
// View state koruma
const ViewState = {
    save(key, value) {
        sessionStorage.setItem(`view_${key}`, JSON.stringify(value));
    },
    get(key) {
        const data = sessionStorage.getItem(`view_${key}`);
        return data ? JSON.parse(data) : null;
    }
};
```

### 11.3 View Transition API

*Detaylı metadata: [[ADR-048]]*

```javascript
// Animasyonlu sayfa geçişleri
document.startViewTransition(async () => {
    await router.navigate(newPath);
});
```

---

## 12. Hata Yönetimi

### 12.1 Error Boundary

```javascript
// SPA hata yakalama
window.addEventListener('error', (event) => {
    console.error('SPA Error:', event.error);
    // Hata raporlama
});

window.addEventListener('unhandledrejection', (event) => {
    console.error('Unhandled Promise:', event.reason);
});
```

### 12.2 Fallback Stratejisi

| Durum | Çözüm |
|-------|-------|
| Fetch başarısız | Full page reload |
| Parse hatası | DOMParser fallback |
| Auth hatası | Login redirect |
| 404 | Custom 404 sayfası |
| 500 | Error page + retry |

---

## 13. Yasak Örüntüler (Frontend)

| ❌ Yasak | ✅ Doğru | ADR |
|----------|----------|-----|
| React / Vue / Angular | Vanilla JS | ADR-001 |
| `innerHTML` | DOMParser + TrustedTypes | ADR-001 |
| `var` | `const` / `let` | ADR-001 |
| `eval()` | Safe alternatives | — |
| `require()` | ES6 modules | ADR-001 |
| localStorage for auth | Session cookie | ADR-011 |
| CSS-in-JS | ITCSS + BEM | ADR-001 |
| jQuery | Vanilla JS Fetch API | ADR-001 |

---

## 14. Kod Örnekleri

### 14.1 PHP Route Definition

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Router\Attribute;

use Attribute;

#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
final readonly class Route
{
    public function __construct(
        public string $method,
        public string $path,
        public ?string $name = null,
    ) {}
}
```

### 14.2 PHP Middleware Interface

```php
<?php

declare(strict_types=1);

namespace CoreMusic\Security\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

interface MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface;
}
```

### 14.3 JS DOMParser Kullanımı

```javascript
// innerHTML YASAK — DOMParser + TrustedTypes kullan
'use strict';

function updateContent(html) {
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const newContent = doc.querySelector('.content');
    
    if (newContent) {
        const existing = document.querySelector('.content');
        existing.replaceWith(newContent);
    }
}
```

---

## 15. Cross References

| Bölüm | Hedef Vault Dosyası | İlişki |
|-------|---------------------|--------|
| §2 Vizyon | [[../../architecture/l2-routing/spa-router]] | L2 Routing |
| §3 Gereksinimler | [[ADR-083]] | SPA Router ADR |
| §4 Mimari | [[../../architecture/l2-routing/spa-router]] §1A | Klasör yapısı |
| §5 Route | [[ADR-083]] | Attribute-based |
| §6 Middleware | [[../../architecture/l1-security/middleware]] | Pipeline |
| §7 Subdomain | [[ADR-016]], [[ADR-043]] | Routing |
| §8 SOLID | [[prompt-shared-base]] §4 | Prensipler |
| §9 Clean Code | [[ADR-001]] | JS kuralları |
| §10 Auth | [[ADR-043]], [[ADR-047]] | Entegrasyon |
| §11 State | [[ADR-045]], [[ADR-046]], [[ADR-048]] | View mode |
| §13 Yasaklar | [[ADR-001]] | Forbidden patterns |
| §14 Kod | [[../../brain.md]] §18 | Standartlar |

---

*Prompt 1: SPA Router Mimarisi v2.0.0 — CoreMusic Prompt System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-15*
*Mode: Red Team · Human Mode · Truth Mode*
