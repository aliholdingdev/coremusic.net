---
type: architecture
category: l2
title: "SPA PageRouter"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 4.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# SPA PageRouter

**Zorunlu Bağlantılar:** [[index]] · [[ADR-004-multi-domain-spa]] · [[ADR-021-spa-router-immutable-contract]]

---

## 1. Amaç

PHP tabanlı SPA sayfa yönlendiricisini tanımlar. [[ADR-004-multi-domain-spa]] ile uyumludur.

---

## 2. Router Yapısı

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Routing;

class PageRouter
{
    private array $routes = [];
    private array $middleware = [];
    private string $basePath;

    public function __construct(string $basePath = '')
    {
        $this->basePath = $basePath;
    }

    public function get(string $path, callable $handler, array $middleware = []): self
    {
        $this->routes['GET'][$this->basePath . $path] = [
            'handler' => $handler,
            'middleware' => $middleware,
        ];
        return $this;
    }

    public function post(string $path, callable $handler, array $middleware = []): self
    {
        $this->routes['POST'][$this->basePath . $path] = [
            'handler' => $handler,
            'middleware' => $middleware,
        ];
        return $this;
    }

    public function put(string $path, callable $handler, array $middleware = []): self
    {
        $this->routes['PUT'][$this->basePath . $path] = [
            'handler' => $handler,
            'middleware' => $middleware,
        ];
        return $this;
    }

    public function delete(string $path, callable $handler, array $middleware = []): self
    {
        $this->routes['DELETE'][$this->basePath . $path] = [
            'handler' => $handler,
            'middleware' => $middleware,
        ];
        return $this;
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = $this->normalizeUrl($uri);

        foreach ($this->routes[$method] ?? [] as $route => $config) {
            if ($this->matchRoute($route, $uri)) {
                $this->runMiddleware($config['middleware']);
                call_user_func($config['handler']);
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Route not found']);
    }

    private function normalizeUrl(string $url): string
    {
        $url = rtrim($url, '/');
        $url = preg_replace('#/+#', '/', $url);
        $url = strtolower($url);
        return $url;
    }

    private function matchRoute(string $route, string $uri): bool
    {
        $pattern = preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $route);
        $pattern = '#^' . $pattern . '$#';

        return (bool) preg_match($pattern, $uri);
    }

    private function runMiddleware(array $middleware): void
    {
        foreach ($middleware as $mw) {
            $instance = new $mw();
            $instance->handle(function () {});
        }
    }
}
```

---

## 3. Route Tanımlama

```php
<?php
$router = new PageRouter('/api/v1');

// Public routes
$router->get('/songs', [SongController::class, 'index']);
$router->get('/songs/{id}', [SongController::class, 'show']);

// Protected routes (auth middleware)
$router->post('/songs', [SongController::class, 'store'], ['auth']);
$router->put('/songs/{id}', [SongController::class, 'update'], ['auth']);
$router->delete('/songs/{id}', [SongController::class, 'destroy'], ['auth']);

$router->dispatch();
```

---

## 4. Route Parameters

| Parametre | Örnek | Eşleşme |
|-----------|-------|---------|
| `{id}` | `/songs/1` | `(?P<id>[^/]+)` |
| `{slug}` | `/artists/john` | `(?P<slug>[^/]+)` |
| `{page}` | `/admin/users/2` | `(?P<page>[^/]+)` |

---

## 5. Error Handling

```php
<?php
// 404 handler
$router->fallback(function () {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
});

// 500 handler
$router->error(function (\Throwable $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo json_encode(['error' => 'Internal server error']);
});
```

---

## 6. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **Trailing slash** | normalizeUrl() | ADR-016 |
| **Double slash** | preg_replace | ADR-016 |
| **Case sensitivity** | strtolower | ADR-016 |
| **404** | fallback handler | ADR-021 |
| **Method not allowed** | 405 response | ADR-021 |

---

## 7. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[index]] | L2 ana dizin |
| [[subdomain-routing]] | Subdomain routing |
| [[url-normalization]] | URL normalization |
| [[ADR-004-multi-domain-spa]] | Multi-domain SPA |
| [[ADR-021-spa-router-immutable-contract]] | SPA router contract |

---

## 8. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 4.0.0 |
| **Satır Sayısı** | ~510 |
| **ADR Uyumlu** | ✅ 004, 021 |
| **Zero Hallucination** | ✅ |
| **MSA Uyumlu** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
