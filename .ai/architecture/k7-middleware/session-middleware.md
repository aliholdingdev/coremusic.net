---
title: "Session Middleware"
layer: K7
category: "Middleware"
date: 2026-09-20
version: "1.0.0"
status: "draft"
---

# Session Middleware

## Genel Bakış

Session Middleware, HTTP istekleri arasında oturum verilerini yönetir. Cookie tabanlı oturum oluşturur, yükler ve sonlandırır. Oturum güvenliği için secure, httponly ve samesite attribut'larını yapılandırarak session fixation ve hijacking saldırılarını önler.

## Pipeline Pozisyonu

```
HTTP İsteği
│
├── [1] Compression
├── [2] Security Headers
├── [3] Rate Limit
├── [4] CORS
├── [5] Origin Check
│
▼
[6] Session Middleware  ← Oturum yönetimi
│
├── [7] CSRF
├── [8] Request Validation
├── [9] Logging
├── [10] Error Handler
│
▼
Handler
```

Session middleware, authentication'dan önce çalışır. Kullanıcı oturumu yüklendikten sonra CSRF ve auth middleware'leri bu oturum bilgisini kullanabilir.

## Teknik Detaylar

### Oturum Yaşam Döngüsü

```
Yeni Kullanıcı
│
├── Cookie yoksa → Yeni oturum oluştur
│   ├── Session ID üret (32 byte random)
│   ├── Cookie'yi Set-Cookie ile gönder
│   └── Sunucu tarafında depola
│
├── Cookie varsa → Mevcut oturumu yükle
│   ├── Session ID'yi doğrula
│   ├── Sunucudan verileri çek
│   ├── Son erişim zamanını güncelle
│   └── Request'e oturum verilerini ekle
│
▼
Sonraki Middleware → Oturum verilerine erişim
│
├── Oturum yenileme (rotate) → Yeni ID üret
├── Oturum sonlandırma → Cookie'yi sil
└── Flash veri → Bir sonraki istek için tek kullanımlık veri
```

### Cookie Konfigürasyonu

```php
namespace CoreMusic\Middleware;

class SessionConfig
{
    public static function getSecureConfig(): array
    {
        return [
            'name' => 'COREMUSIC_SID',
            'lifetime' => 7200,           // 2 saat
            'path' => '/',
            'domain' => '.coremusic.io',
            'secure' => true,             // Sadece HTTPS
            'httponly' => true,            // JS erişimi yok
            'samesite' => 'Lax',          // CSRF koruması
            'cookie_prefix' => '__Host-',  // Security prefix
            'use_strict_mode' => true,     // Geçersiz ID'leri reddet
            'use_cookies' => true,
            'use_only_cookies' => true,    // URL session ID yasak
            'use_trans_sid' => false,      // Transfer ID kapalı
            'regenerate_attack_protection' => true,
        ];
    }
}
```

### Session Implementasyonu

```php
namespace CoreMusic\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class SessionMiddleware implements MiddlewareInterface
{
    private SessionHandlerInterface $handler;
    private SessionConfig $config;

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        // Oturum ID'sini cookie'den al
        $sessionId = $this->extractSessionId($request);

        // Oturum verilerini yükle
        $sessionData = $this->loadSession($sessionId);

        // Request'e oturum ekle
        $request = $this->attachSession($request, $sessionData);

        // Handler'ı çalıştır
        $response = $handler->handle($request);

        // Oturum verilerini kaydet
        $response = $this->saveSession($response, $sessionData);

        // Session fixation koruması
        if ($this->shouldRegenerate($request)) {
            $response = $this->regenerateSession($response);
        }

        return $response;
    }

    private function extractSessionId(ServerRequestInterface $request): ?string
    {
        $cookieName = $this->config->getCookieName();
        return $request->getCookieParams()[$cookieName] ?? null;
    }

    private function loadSession(?string $sessionId): array
    {
        if ($sessionId === null || !$this->isValidSessionId($sessionId)) {
            return $this->createNewSession();
        }

        $data = $this->handler->read($sessionId);

        if ($data === false) {
            return $this->createNewSession();
        }

        return json_decode($data, true) ?? [];
    }

    private function createNewSession(): array
    {
        return [
            'id' => bin2hex(random_bytes(32)),
            'data' => [],
            'flash' => [],
            'created_at' => time(),
            'last_activity' => time(),
            'regenerated' => false,
        ];
    }
}
```

### Flash Veri Desteği

```php
// Flash veri ekleme (bir sonraki istek için)
$request->getSession()->flash('success', 'Parola güncellendi.');

// Flash veri okuma (mevcut istek)
$messages = $request->getSession()->getFlash('success');

// Flash veri temizleme
$request->getSession()->clearFlash();
```

### Oturum Yenileme (Session Regeneration)

```php
private function regenerateSession(ResponseInterface $response): ResponseInterface
{
    $newId = bin2hex(random_bytes(32));

    // Eski oturumu yeni ID ile taşı
    $this->handler->destroy($this->currentSessionId);
    $this->handler->write($newId, json_encode($this->sessionData));

    // Yeni cookie ayarla
    return $this->setSessionCookie($response, $newId);
}

// Kullanıcı giriş yaptıktan sonra oturum yenile
if ($auth->attempt($credentials)) {
    $request->getSession()->regenerate();
}
```

### Session Fixation Koruması

```php
private function shouldRegenerate(ServerRequestInterface $request): bool
{
    // İlk giriş após login
    if ($request->getAttribute('just_logged_in', false)) {
        return true;
    }

    // Admin paneline geçiş
    if ($request->getUri()->getPath() === '/admin') {
        return true;
    }

    // Privilege escalation kontrolü
    $oldLevel = $request->getSession()->get('user_level', 0);
    $newLevel = $request->getAttribute('new_user_level', $oldLevel);

    return $newLevel > $oldLevel;
}
```

## Kod / Konfigürasyon

### Tam Implementasyon

```php
namespace CoreMusic\Middleware;

class SessionMiddleware implements MiddlewareInterface
{
    private SessionHandlerInterface $handler;
    private array $config;
    private LoggerInterface $logger;

    public function __construct(
        SessionHandlerInterface $handler,
        array $config = [],
        ?LoggerInterface $logger = null
    ) {
        $this->handler = $handler;
        $this->config = array_merge(SessionConfig::getSecureConfig(), $config);
        $this->logger = $logger;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $sessionId = $this->extractSessionId($request);
        $isNewSession = $sessionId === null;

        $session = $isNewSession
            ? $this->createNewSession()
            : $this->loadExistingSession($sessionId);

        $request = $request->withAttribute('session', $session);

        $response = $handler->handle($request);

        if ($this->isSessionModified($session)) {
            $this->saveSessionData($session);
        }

        if ($isNewSession) {
            $response = $this->setSessionCookie($response, $session['id']);
        }

        $this->addSessionHeaders($response, $session);

        return $response;
    }

    private function setSessionCookie(
        ResponseInterface $response,
        string $sessionId
    ): ResponseInterface {
        $cookie = sprintf(
            '%s=%s; Path=%s; Domain=%s; Max-Age=%d; %s%s; SameSite=%s',
            $this->config['name'],
            $sessionId,
            $this->config['path'],
            $this->config['domain'],
            $this->config['lifetime'],
            $this->config['secure'] ? 'Secure' : '',
            $this->config['httponly'] ? '; HttpOnly' : '',
            $this->config['samesite']
        );

        return $response->withHeader('Set-Cookie', $cookie);
    }
}
```

### Konfigürasyon

```php
// config/session.php
return [
    'driver' => 'redis',       // file, redis, database
    'lifetime' => 7200,
    'name' => 'COREMUSIC_SID',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax',
    'connection' => [
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'port' => env('REDIS_PORT', 6379),
        'database' => 0,
    ],
];
```

## Bağımlılıklar

| Bileşen | Bağımlılık | Zorunlu |
|---------|-----------|---------|
| PSR-7 | `psr/http-message` | Evet |
| PSR-15 | `psr/http-server-middleware` | Evet |
| PSR-3 Logger | `psr/log` | Opsiyonel |
| Redis | `predis/predis` | Session store için |

## Durum: Implementasyon

| Faz | Açıklama | Durum |
|-----|----------|-------|
| Faz 1 | Cookie tabanlı oturum | Planlandı |
| Faz 2 | Redis session store | Planlandı |
| Faz 3 | Flash veri desteği | Planlandı |
| Faz 4 | Session fixation koruması | Planlandı |
| Faz 5 | Concurrent session yönetimi | Planlandı |
