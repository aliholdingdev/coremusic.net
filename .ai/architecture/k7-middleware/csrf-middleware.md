---
title: "CSRF Middleware"
layer: K7
category: "Middleware"
date: 2026-09-20
version: "1.0.0"
status: "draft"
---

# CSRF Middleware

## Genel Bakış

CSRF (Cross-Site Request Forgery) Middleware, kullanıcının tarayıcısından yapılan isteklerin gerçekten kullanıcı tarafından başlatılıp başlatılmadığını doğrular. Double Submit Cookie ve synchronizer token pattern kullanarak CSRF saldırılarını engeller.

## Pipeline Pozisyonu

```
HTTP İsteği
│
├── [1] Compression
├── [2] Security Headers
├── [3] Rate Limit
├── [4] CORS
├── [5] Origin Check
├── [6] Session
│
▼
[7] CSRF Middleware  ← Token doğrulama
│
├── [8] Request Validation
├── [9] Logging
├── [10] Error Handler
│
▼
Handler
```

CSRF middleware, session'dan sonra çalışır. Kullanıcı oturumu yüklendikten sonra CSRF token'ı doğrulanır.

## Teknik Detaylar

### CSRF Saldırı Senaryosu

```
Kullanıcı (bank.com'da giriş yapmış)
│
├── Salcosa sitesine yönlendirme
│   ├── <img src="bank.com/transfer?to=hacker&amount=1000">
│   ├── <form action="bank.com/transfer" method="POST">
│   │   <input type="hidden" name="to" value="hacker">
│   │   <input type="hidden" name="amount" value="1000">
│   │ </form>
│   └── <script>fetch('bank.com/transfer', {method:'POST', body:...})</script>
│
├── Tarayıcı otomatik olarak bank.com'a cookie gönderir
│   └── Transfer gerçekleşir → Saldırı başarılı!
│
▼
CSRF Token Koruması:
├── Token doğrulama → Saldırı başarısız (403)
```

### Token Üretimi

```php
namespace CoreMusic\Middleware;

class CsrfTokenManager
{
    private int $tokenLength = 32;
    private int $tokenLifetime = 7200;

    public function generateToken(): string
    {
        return bin2hex(random_bytes($this->tokenLength));
    }

    public function createTokenPair(): array
{
        $token = $this->generateToken();
        $hashedToken = hash('sha256', $token);

        return [
            'token' => $token,           // Cookie'de saklanır
            'hashed' => $hashedToken,    // Request header'da gönderilir
            'expires' => time() + $this->tokenLifetime,
        ];
    }

    public function validateToken(string $cookieToken, string $headerToken): bool
    {
        if (empty($cookieToken) || empty($headerToken)) {
            return false;
        }

        // Timing-safe karşılaştırma
        $cookieHash = hash('sha256', $cookieToken);
        return hash_equals($cookieHash, $headerToken);
    }
}
```

### Double Submit Cookie Pattern

```
İstemci Tarafı:
├── CSRF cookie'si ayarla: csrf_token=abc123
├── Her istekte header'a ekle: X-CSRF-Token: abc123
│
Sunucu Tarafı:
├── Cookie'den token'ı oku
├── Header'dan token'ı oku
├── İkisini karşılaştır
├── Eşleşirse → İsteği işle
└── Eşleşmezse → 403 Forbidden
```

### Middleware Implementasyonu

```php
namespace CoreMusic\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class CsrfMiddleware implements MiddlewareInterface
{
    private CsrfTokenManager $tokenManager;
    private array $excludedRoutes;
    private string $cookieName;
    private string $headerName;

    public function __construct(
        CsrfTokenManager $tokenManager,
        array $config = []
    ) {
        $this->tokenManager = $tokenManager;
        $this->excludedRoutes = $config['excluded_routes'] ?? [];
        $this->cookieName = $config['cookie_name'] ?? 'XSRF-TOKEN';
        $this->headerName = $config['header_name'] ?? 'X-CSRF-Token';
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // GET ve HEAD istekleri için token oluştur
        if ($request->getMethod() === 'GET') {
            return $this->ensureCsrfToken($request, $handler);
        }

        // Devre dışı bırakılmış rotaları kontrol et
        if ($this->isExcluded($request)) {
            return $handler->handle($request);
        }

        // Token doğrulaması
        if (!$this->validateCsrfToken($request)) {
            return $this->createCsrfErrorResponse();
        }

        return $handler->handle($request);
    }

    private function ensureCsrfToken(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $existingToken = $request->getCookieParams()[$this->cookieName] ?? null;

        if ($existingToken === null) {
            $pair = $this->tokenManager->createTokenPair();
            $request = $request->withAttribute('csrf_token', $pair['token']);

            $response = $handler->handle($request);
            return $this->setCsrfCookie($response, $pair['token']);
        }

        $request = $request->withAttribute('csrf_token', $existingToken);
        return $handler->handle($request);
    }

    private function validateCsrfToken(ServerRequestInterface $request): bool
    {
        $cookieToken = $request->getCookieParams()[$this->cookieName] ?? '';
        $headerToken = $request->getHeaderLine($this->headerName);

        // POST body'den de kontrol et (form submissions)
        $bodyToken = $this->extractBodyToken($request);

        $tokenToValidate = $headerToken ?: $bodyToken;

        return $this->tokenManager->validateToken($cookieToken, $tokenToValidate);
    }

    private function extractBodyToken(ServerRequestInterface $request): string
    {
        $contentType = $request->getHeaderLine('Content-Type');

        if (strpos($contentType, 'application/json') !== false) {
            $body = json_decode((string) $request->getBody(), true);
            return $body['_csrf_token'] ?? '';
        }

        parse_str((string) $request->getBody(), $body);
        return $body['_csrf_token'] ?? '';
    }

    private function isExcluded(ServerRequestInterface $request): bool
    {
        $path = $request->getUri()->getPath();

        foreach ($this->excludedRoutes as $route) {
            if (fnmatch($route, $path)) {
                return true;
            }
        }

        return false;
    }

    private function setCsrfCookie(
        ResponseInterface $response,
        string $token
    ): ResponseInterface {
        $cookie = sprintf(
            'XSRF-TOKEN=%s; Path=/; Max-Age=%d; SameSite=Strict',
            $token,
            7200
        );

        return $response->withHeader('Set-Cookie', $cookie);
    }

    private function createCsrfErrorResponse(): ResponseInterface
    {
        $response = new \GuzzleHttp\Psr7\Response(403);
        $response->getBody()->write(json_encode([
            'error' => 'CSRF_TOKEN_MISMATCH',
            'message' => 'CSRF token doğrulaması başarısız.',
            'status' => 403,
        ]));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
```

## Kod / Konfigürasyon

### Konfigürasyon

```php
// config/csrf.php
return [
    'cookie_name' => 'XSRF-TOKEN',
    'header_name' => 'X-CSRF-Token',
    'token_lifetime' => 7200,
    'excluded_routes' => [
        '/api/v1/webhooks/*',        // Webhook'lar
        '/api/v1/health',            // Sağlık kontrolü
        '/api/v1/auth/refresh',      // Token yenileme
    ],
    'token_length' => 32,
    'samesite' => 'Strict',
];
```

### JavaScript Entegrasyonu

```javascript
// CSRF token'ı cookie'den oku
function getCsrfToken() {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? match[1] : null;
}

// Her istekte header'a ekle
axios.interceptors.request.use(config => {
    const token = getCsrfToken();
    if (token) {
        config.headers['X-CSRF-Token'] = token;
    }
    return config;
});
```

## Bağımlılıklar

| Bileşen | Bağımlılık | Zorunlu |
|---------|-----------|---------|
| PSR-7 | `psr/http-message` | Evet |
| PSR-15 | `psr/http-server-middleware` | Evet |

## Durum: Implementasyon

| Faz | Açıklama | Durum |
|-----|----------|-------|
| Faz 1 | Double Submit Cookie pattern | Planlandı |
| Faz 2 | Timing-safe token karşılaştırma | Planlandı |
| Faz 3 | AJAX form entegrasyonu | Planlandı |
| Faz 4 | Token yenileme stratejisi | Planlandı |
| Faz 5 | Edge case testleri | Planlandı |
