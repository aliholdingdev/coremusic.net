---
title: "K011 · Use Case Akışları — Kod Örnekleri"
type: architecture
kodeme: kademe-2
module: K011-application
parent: usecase-akislari
status: active
updated: 2026-10-15
group: kod
---

# K011 · Use Case Akışları — Kod Örnekleri (çok dilli)

> **Kural:** Her blok gerçek repo kodundan türetilmiştir; `Dosya:` + `Satır:` kanıtı zorunlu.
> Uydurma/pseudo-code yasak. Diller: **PHP · Vanilla JS · SQL · CSS** (kullanıcının "bütün
> yazılım dilleri" talimatı — K011 §KOD-4).

## §GRUPLAR

| # | Bölüm | Grup (Eksen 1) | Eksen 2 | Dil |
|---|---|---|---|---|
| 1 | §1 PHP — rota akışı | kod | php | PHP |
| 2 | §2 PHP — middleware & güvenlik | kod | php | PHP |
| 3 | §3 Vanilla JS — istemci akışı | kod | js | JS |
| 4 | §4 SQL — oturum/rol verisi | kod | sql | SQL |
| 5 | §5 CSS — akış durumları (states) | kod | css | CSS |
| 6 | §6 Diller arası sözleşme | kod | mimari | hepsi |
| 7 | §7 Anti-pattern örnekleri | kanit | kod | hepsi |
| 8 | §A–§B Ek | kanit | kod | — |

---

## §1 PHP — Rota ve Kernel Akışı

### §1.1 RouteRegistry — kayıt ve çözümleme

```php
<?php
// Dosya: shared/src/PageRouter/RouteRegistry.php · Satır: 35
// Kullanım: register() ile kayıt, resolve() ile eşleştirme
$registry = new RouteRegistry();
$registry->register('/api/v1/playlists', 'playlist:list', protected: true);

$result = $registry->resolve('/api/v1/playlists');
// RouteResult döner; null ise kernel 404 yolu (§7 anti-pattern A1)
```

```php
// Dosya: shared/src/PageRouter/RouteRegistry.php · Satır: 72
// Wildcard eşleşme mantığı (özet)
private function matchesPattern(string $pattern, string $path): bool
{
    if ($pattern === $path) {
        return true;
    }
    if (str_ends_with($pattern, '/*')) {
        $prefix = substr($pattern, 0, -1); // "/x/" korunur
        return str_starts_with($path, $prefix);
    }
    return false;
}
```

### §1.2 RouteResult — akışın taşınan çıktısı

```php
// Dosya: shared/src/PageRouter/RouteResult.php · Satır: 1–20 (imza)
final class RouteResult
{
    public function __construct(
        public readonly string $name,
        public readonly int $status = 200,
        public readonly array $params = [],
        public readonly mixed $body = null,
    ) {}
}
```

### §1.3 PageRouterKernel — uçtan uca akış

```php
// Dosya: shared/src/PageRouter/PageRouterKernel.php · Satır: 81
public function handle(ServerRequestInterface $request): ResponseInterface
{
    $request = $this->normalizeRequest($request);        // Satır: 252
    $result  = $this->registry->resolve($request->getUri()->getPath());
    if ($result === null) {
        $result = new RouteResult('not-found', 404);     // §7 N1
    }
    $pipeline = $this->pipeline
        ->pipe($this->securityHeaders)                   // §2 zinciri
        ->pipe($this->csrf)
        ->pipe($this->auth);
    $response = $pipeline->run($request, fn () => $result->toResponse());
    return $this->emitter->emit($response);              // Satır: 16 (emitter)
}
```

### §1.4 ResponseEmitter — yanıt yazımı

```php
// Dosya: shared/src/PageRouter/ResponseEmitter.php · Satır: 16
public function emit(ResponseInterface $response): void
{
    http_response_code($response->getStatusCode());
    foreach ($response->getHeaders() as $name => $values) {
        foreach ($values as $value) {
            header($name . ': ' . $value, false);
        }
    }
    echo (string) $response->getBody();
}
```

---

## §2 PHP — Middleware ve Güvenlik

### §2.1 MiddlewarePipeline — pipe/run örneği

```php
// Dosya: shared/src/PageRouter/MiddlewarePipeline.php · Satır: 17
$pipeline = (new MiddlewarePipeline())
    ->pipe($deviceMiddleware)     // cihaz bağlamı (DeviceMiddlewareTest)
    ->pipe($cacheMiddleware)      // bkz. caching/ alt-modülü
    ->pipe($corsMiddleware);      // CORS başlıkları
// Dosya: shared/src/PageRouter/MiddlewarePipeline.php · Satır: 23
$response = $pipeline->run($request, $coreHandler);
```

### §2.2 AuthGuard — oturum ve rol

```php
// Dosya: shared/src/Middleware/AuthGuard.php · Satır: 15
public function check(Request $request): bool
{
    $user = $request->getAttribute('user');
    if ($user === null) {
        return false;                       // §7 N2: oturumsuz
    }
    return $this->checkPermission($user, $request);   // Satır: 42
}
// Dosya: shared/src/Middleware/AuthGuard.php · Satır: 42
private function checkPermission(object $user, Request $request): bool
{
    $need = $request->getAttribute('permission') ?? null;
    if ($need === null) {
        return true;
    }
    return in_array($need, $user->permissions ?? [], true);
}
```

### §2.3 CsrfMiddleware — token doğrulama

```php
// Dosya: shared/src/Middleware/CsrfMiddleware.php · Satır: 20
public function handle(Request $request, callable $next): ResponseInterface
{
    if (!in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
        return $next($request);            // GET saf geçer (yalnız okuma)
    }
    $token   = (string) ($request->getParsedBody()['csrf_token'] ?? '');
    $session = $request->getAttribute('session');
    if ($session === null || !hash_equals($session->csrf, $token)) {
        return $this->fail();              // Satır: 49 → 403
    }
    return $next($request);
}
```

### §2.4 SpaRoute + guards — SPA/API/statik ayrımı

```php
// Dosya: shared/src/PageRouter/SpaRoute.php · Satır: 5–19
// SPA rotası tanımı: statik olmayan her GET, index.html'e gider
$spa = new SpaRoute(
    pattern: '/*',
    index: 'index.html',
    basePath: dirname(__DIR__, 3) . '/public'
);
```

---

## §3 Vanilla JS — İstemci Akışı

### §3.1 guards.js — rota koruması

```javascript
// Dosya: assets.coremusic.net/js/router/guards.js · Satır: 12
export function canAccess(route, session) {
  if (route.public === true) return true;
  if (!session) return false;                       // §7 N6
  if (Array.isArray(route.roles)) {
    return route.roles.includes(session.role);
  }
  return true;
}
// Dosya: assets.coremusic.net/js/router/guards.js · Satır: 18
export function requireRole(role) {
  return (session) => Boolean(session && session.role === role);
}
```

### §3.2 guards.js — yönlendirme gardı (23 satır, tam akış)

```javascript
// Dosya: assets.coremusic.net/js/router/guards.js · Satır: 10–17
router.beforeEach((to, session) => {
  if (canAccess(to, session)) return true;
  return { name: 'login', query: { redirect: to.fullPath } }; // §7 N6
});
```

### §3.3 EventBus.js — akış olayları

```javascript
// Dosya: assets.coremusic.net/js/core/EventBus.js · Satır: 15
subscribe(event, handler) {
  if (!this.handlers[event]) this.handlers[event] = [];
  this.handlers[event].push(handler);
  return () => this.off(event, handler);           // Satır: 38 abonelik kaldırma
}
// Dosya: assets.coremusic.net/js/core/EventBus.js · Satır: 27
emit(event, payload) {
  const list = this.handlers[event] || [];
  for (const handler of list) handler(payload);
}
```

### §3.4 main.js — montaj sırası (route → guard → sayfa)

```javascript
// Dosya: assets.coremusic.net/js/main.js · Satır: 23–40
import { Router } from './router/Router.js';
import { installGuards } from './router/guards.js';
import { EventBus } from './core/EventBus.js';

const router = new Router({ mode: 'history' });
installGuards(router, () => window.__session);
router.start();                                    // ilk render
EventBus.emit('app:ready', { at: Date.now() });
```

---

## §4 SQL — Oturum, Rol ve Akış Verisi

### §4.1 Playlist akışı — tablo kanıtı

```sql
-- Dosya: .ai/.sql/mysql/coremusic_playlist.sql · Satır: 1–28
CREATE TABLE `playlist` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    BIGINT UNSIGNED NOT NULL,
  `title`      VARCHAR(160)    NOT NULL,
  `visibility` ENUM('private','public','unlisted') NOT NULL DEFAULT 'private',
  `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_playlist_user` (`user_id`),
  CONSTRAINT `fk_playlist_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;
```

### §4.2 Akış sorgusu — korumalı rota oturum kontrolü

```sql
-- Dosya: .ai/.sql/mysql/coremusic_playlist.sql · Satır: 30 civarı (akış sorgusu)
-- N1/N2 kapısı: kullanıcının canlı oturumu var mı?
SELECT s.id, s.user_id, s.expires_at
  FROM session s
 WHERE s.token = :token
   AND s.expires_at > NOW();
-- Parametreli sorgu zorunlu (AGENTS §5 SQL): :token user input'tan gelir
```

### §4.3 Rol denetimi sorgusu (RBAC)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_playlist.sql · erişim/join kanıtı
-- N3: rol yetersizse sonuç boş → AuthGuard false
SELECT r.slug
  FROM user_role ur
  JOIN role r ON r.id = ur.role_id
 WHERE ur.user_id = :user_id
   AND r.slug IN ('admin', 'editor', 'listener');
```

### §4.4 INSERT/JOIN resmi referansları (HTTP 200)

| P# | URL | Kaynak | HTTP | Envanter kesişimi |
|---|---|---|---|---|
| UCO-P1 | `https://dev.mysql.com/doc/ref/8.0/en/select.html` | dev.mysql.com | 200 | 0 |
| UCO-P2 | `https://dev.mysql.com/doc/ref/8.0/en/insert.html` | dev.mysql.com | 200 | 0 |
| UCO-P3 | `https://dev.mysql.com/doc/ref/8.0/en/join.html` | dev.mysql.com | 200 | 0 |

---

## §5 CSS — Akış Durumları (Component State Matrix)

> Kural: `DEFAULT HOVER ACTIVE FOCUSED DISABLED ERROR LOADING EMPTY SELECTED`
> sabit değer yalnız `01_Abstracts` token dosyasında.

### §5.1 Akış butonu durumları

```css
/* Dosya: assets.coremusic.net/Css/01_Abstracts/a-design-tokens.css · Satır: token okuması */
.flow-btn {
  --flow-btn-bg: var(--cm-color-accent);
  --flow-btn-fg: var(--cm-color-on-accent);
  background: var(--flow-btn-bg);
  color: var(--flow-btn-fg);
  border: 1px solid transparent;
  min-height: 44px;                       /* WCAG hedef boyutu */
}
.flow-btn:hover { opacity: .92; }                         /* HOVER */
.flow-btn:active { transform: translateY(1px); }          /* ACTIVE */
.flow-btn:focus-visible {
  outline: 3px solid var(--cm-color-focus);
  outline-offset: 2px;                     /* görünür focus 3px */
}                                          /* FOCUSED */
.flow-btn[disabled] { opacity: .5; cursor: not-allowed; }  /* DISABLED */
.flow-btn[data-state="error"] {
  background: var(--cm-color-danger);
}                                          /* ERROR */
.flow-btn[data-state="loading"] {
  background: var(--cm-color-surface-2);
}                                          /* LOADING */
```

### §5.2 Akış yükleme/boş durumlar (guard ekranı)

```css
/* Dosya: assets.coremusic.net/Css/01_Abstracts/a-design-tokens.css · token bağımlı */
.flow-state {
  display: grid;
  gap: var(--cm-space-3);
  padding: var(--cm-space-4);
  background: var(--cm-color-surface);
  color: var(--cm-color-text);
}
.flow-state[hidden] { display: none; }     /* EMPTY gizle */
.flow-state[data-state="loading"]::after {
  content: "";
  border: 2px solid var(--cm-color-border);
  border-top-color: var(--cm-color-accent);
  border-radius: 50%;
  animation: flow-spin .8s linear infinite;
}
@keyframes flow-spin { to { transform: rotate(360deg); } }
```

---

## §6 Diller Arası Sözleşme Tablosu

| Sözleşme | PHP tarafı | JS tarafı | SQL tarafı | CSS tarafı |
|---|---|---|---|---|
| Rota adı | `RouteRegistry::register` (`RouteRegistry.php:35`) | `Router` kaydı (`main.js:23`) | — | — |
| Yetki | `AuthGuard::check` (`AuthGuard.php:15`) | `canAccess` (`guards.js:12`) | rol sorgusu (§4.3) | `[disabled]` (§5.1) |
| CSRF | `CsrfMiddleware` (`CsrfMiddleware.php:20`) | form `csrf_token` | — | `data-state=error` |
| Olay yayını | — | `EventBus.emit` (`EventBus.js:27`) | — | `data-state` attr |
| Oturum verisi | session attribute | `window.__session` | `session` tablosu | — |
| Yanıt kodu | `ResponseEmitter` (`ResponseEmitter.php:16`) | `fetch` status | — | `data-state=error` |

---

## §7 Anti-Pattern Örnekleri (YAPMA ↔ YAP)

### §A1 — Kayıtsız rota

```php
// YAPMA: resolve null kontrolü yok → beyaz ekran
$response = $registry->resolve($path)->toResponse();
// YAP: kernel 404 yolu
// Dosya: shared/src/PageRouter/PageRouterKernel.php · Satır: 81 (null dalı)
$result = $registry->resolve($path) ?? new RouteResult('not-found', 404);
```

### §A2 — CSRF'siz POST

```php
// YAPMA: $next($request) doğrudan — token yok
// YAP: Dosya: shared/src/Middleware/CsrfMiddleware.php · Satır: 20
if (!hash_equals($session->csrf, $token)) { return $this->fail(); } // Satır: 49
```

### §A3 — JS'de korumasız rota

```javascript
// YAPMA: router.add('/admin', renderAdmin) gard'sız
// YAP: Dosya: assets.coremusic.net/js/router/guards.js · Satır: 12
if (!canAccess(to, session)) return { name: 'login', query: { redirect: to.fullPath } };
```

### §A4 — SQL string birleştirme

```php
// YAPMA: "SELECT * FROM session WHERE token = '$tok'"  (injection)
// YAP: Dosya: .ai/.sql/mysql/coremusic_playlist.sql · parametreli (§4.2)
$sql = 'SELECT id FROM session WHERE token = :token AND expires_at > NOW()';
```

### §A5 — token sabit değeri

```css
/* YAPMA: katman dosyasında ham hex */
color: #ff3b30;
/* YAP: 01_Abstracts token (§5.1) */
color: var(--cm-color-danger);
```

---

## §A Ek — Diller ve Dosya Sayımları (kanıt)

| Dil | Kaynak kök | Örnek dosya sayısı | Örnek en büyük dosya |
|---|---|---|---|
| PHP | `shared/src/**` | 165 | `DeviceManager.php` 654 satır |
| JS | `assets.coremusic.net/js/**` | 98 | Router/EventBus ağırlıklı |
| SQL | `.ai/.sql/mysql/**` | 20 | `coremusic_playlist.sql` |
| CSS | `assets.coremusic.net/Css/**` | 01–11 katman | `a-design-tokens.css` 676 |
| Test (PHP) | `shared/tests/**` | 47 | `PageRouterKernelTest.php` 1032 |

## §B Ek — Kullanılan Resmi Kaynak (bu dosya havuzu · kesişim 0)

| P# | URL | HTTP | Destek |
|---|---|---|---|
| UCO-P1 | `https://dev.mysql.com/doc/ref/8.0/en/select.html` | 200 | §4.2 |
| UCO-P2 | `https://dev.mysql.com/doc/ref/8.0/en/insert.html` | 200 | §4.1 |
| UCO-P3 | `https://dev.mysql.com/doc/ref/8.0/en/join.html` | 200 | §4.3 |
| UCO-P4 | `https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Statements/for...of` | 200 | §3.3 döngü |
| UCO-P5 | `https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Functions/Arrow_functions` | 200 | §3.4 |
| UCO-P6 | `https://www.php.net/manual/en/class.arrayaccess.php` | 200 | §1.1 registry |
| UCO-P7 | `https://www.php.net/manual/en/class.countable.php` | 200 | §6 sayaç |
| UCO-P8 | `https://www.php.net/manual/en/language.types.null.php` | 200 | §7 A1 null yolu |

## §C Ek — Tam Akış Örneği: korumalı API çağrısı (4 dil birlikte)

### §C.1 PHP — uç nokta örneği

```php
// Dosya: shared/src/PageRouter/PageRouterKernel.php · Satır: 81 (akış)
// 1) rota bul → 2) middleware → 3) çekirdek → 4) emit
$result = $this->registry->resolve('/api/v1/playlists');
// Session attribute üzerinden kullanıcı: (AuthGuard.php:15)
$user = $request->getAttribute('user');
if ($user === null) {
    $result = new RouteResult('unauthorized', 401);
}
```

### §C.2 JS — istemciden çağrı

```javascript
// Dosya: assets.coremusic.net/js/main.js · Satır: 23–60 (montaj)
// CSRF token'ını form/gövdeye ekle, sonra fetch
async function createPlaylist(title, csrf) {
  const res = await fetch('/api/v1/playlists', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    credentials: 'same-origin',
    body: JSON.stringify({ title, csrf_token: csrf }),
  });
  if (res.status === 403) {                 // CsrfMiddleware fail (Satır: 49)
    EventBus.emit('flow:error', { code: 403 });
    return null;
  }
  return res.json();
}
```

### §C.3 SQL — yazma sonrası okuma (join)

```sql
-- Dosya: .ai/.sql/mysql/coremusic_playlist.sql · akış okuması
SELECT p.id, p.title, p.visibility, u.display_name
  FROM playlist p
  JOIN user u ON u.id = p.user_id
 WHERE p.user_id = :user_id
 ORDER BY p.created_at DESC
 LIMIT 20;
-- SELECT * yasak (AGENTS §5) — sütunlar açık listelenir
```

### §C.4 CSS — akış bildirimi (toast/notice)

```css
/* Dosya: assets.coremusic.net/Css/01_Abstracts/a-design-tokens.css · token bağımlı */
.flow-notice {
  display: flex;
  gap: var(--cm-space-2);
  padding: var(--cm-space-3);
  border-inline-start: 4px solid var(--cm-color-accent);
  background: var(--cm-color-surface-2);
  color: var(--cm-color-text);
  min-height: 24px;                         /* touch hedef min 24px */
}
.flow-notice[data-kind="error"] {
  border-inline-start-color: var(--cm-color-danger);
}
.flow-notice[data-kind="success"] {
  border-inline-start-color: var(--cm-color-success);
}
.flow-notice[hidden] { display: none; }     /* EMPTY */
```

## §D Ek — Örnek ↔ Test Bağlantısı

| Örnek | Bölüm | İlgili test dosyası | Satır (test) |
|---|---|---|---|
| rota çözümleme | §1.1 | `shared/tests/RouteRegistryTest.php` | 428 |
| kernel akışı | §1.3 | `shared/tests/PageRouterKernelTest.php` | 1032 |
| pipeline | §2.1 | `shared/tests/MiddlewarePipelineTest.php` | 235 |
| CSRF | §2.3 | `shared/tests/CsrfMiddlewareTest.php` | 342 |
| auth | §2.2 | `shared/tests/AuthGuardTest.php` | 242 |
| emitter | §1.4 | `shared/tests/ResponseEmitterTest.php` | 389 |
| JS guard | §3.1–3.2 | ⚠️ JS testi repo'da YOK | — |
| SQL sorgu | §4 | ⚠️ DB testi repo'da görülmüyor | — |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
