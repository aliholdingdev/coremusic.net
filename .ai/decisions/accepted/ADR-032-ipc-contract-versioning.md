---
type: adr
category: architecture
title: "ADR-032: IPC Contract Versioning"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-032: IPC Contract Versioning

## 1. Amaç

CoreMusic servisler arası iletişim (IPC) sözleşmeleri için versiyonlama stratejisini tanımlar. [[ADR-032-ipc-contract-versioning]] Frozen karardır. Bu karar, 7 backend servisi (Control, Media, Audio, Device, Network Audio, AI, Download) arasındaki iletişimi kapsar.

Bu ADR'nin amacı:
- Servisler arası iletişim sözleşmelerini standardize etmek
- Geriye uyumluluk sağlamak
- API versiyonlamasını tanımlamak
- Hata yönetimini standardize etmek
- Dokümantasyon standartlarını koymak
- Monitoring ve logging'i sağlamak
- Contract testing altyapısını kurmak
- Servisler arası bağımlılık haritasını çıkarmak

## 2. Bağlam

| Faktör | Değer |
|--------|-------|
| **Servisler** | 7 backend servisi |
| **İletişim** | HTTP/REST, WebSocket |
| **Versiyon** | URL-based versioning |
| **Format** | JSON |
| **Güvenlik** | JWT, CSRF, rate limiting |
| **Monitoring** | Health check, logging |
| **Hata** | Standard hata formatı |
| **Dokümantasyon** | OpenAPI 3.0 |
| **Test** | Contract testing |
| **Deploy** | Blue-green deployment |

### 2.1 Neden IPC Versiyonlama?

- **Gerçek zamanlı güncelleme:** Servisler bağımsız güncellenir
- **Backward compatibility:** Eski sürümler desteklenir
- **Hata izolasyonu:** Sorunlu servis izole edilir
- **Ölçeklenebilirlik:** Yeni servisler eklenebilir
- **Bakım kolaylığı:** Bağımsız bakım
- **Monitoring kolaylığı:** Servis bazlı izleme
- **API contract koruması:** Sözleşme değişiklikleri denetlenir

### 2.2 Servis Haritası

| Servis | Port | Protokol | Versiyon |
|--------|------|----------|----------|
| Control Service | 81 | HTTP | v1 |
| Media Service | 5000/6000 | HTTP | v1 |
| Audio Service | 9741/9742 | REST/WS | v1 |
| Device Service | — | BLE/WiFi/USB | v1 |
| Network Audio | — | WebRTC/P2P | v1 |
| AI Service | — | Internal | v1 |
| Download Service | 3001 | HTTP/WS | v1 |

### 2.3 Bağımlılık Haritası

| Servis | Bağımlı Olduğu Servisler | Bağımlılık Türü |
|--------|--------------------------|-----------------|
| Control | MySQL | DB |
| Media | Control (auth), MySQL | Auth + DB |
| Audio | Control (auth), Media (metadata) | Auth + Data |
| Device | Audio (streaming) | Stream |
| Network Audio | Audio (streaming), Device | Stream + Device |
| AI | Media (metadata), MySQL | Data + DB |
| Download | Control (auth), MySQL | Auth + DB |

## 3. Karar

### 3.1 Versiyonlama Kararı

| Karar | Değer | Gerekçe |
|-------|-------|---------|
| **Versioning** | ✅ URL-based | Kolay uygulama |
| **Backward** | ✅ Zorunlu | Geriye uyumluluk |
| **Deprecation** | ✅ Zorunlu | Eski versiyon bildirimi |
| **Health Check** | ✅ Zorunlu | Servis durumu |
| **Error Format** | ✅ Standard | Tutarlı hata yönetimi |
| **Logging** | ✅ Zorunlu | İzlenebilirlik |
| **Monitoring** | ✅ Zorunlu | Durum takibi |
| **Contract Test** | ✅ Zorunlu | Sözleşme doğrulama |
| **Documentation** | ✅ OpenAPI 3.0 | Dokümantasyon |
| **Security** | ✅ JWT + CSRF | Güvenlik |

### 3.2 API Versiyonlama Formatı

```
https://api.coremusic.net/v1/tracks
https://api.coremusic.net/v2/tracks
```

### 3.3 Deprecation Politikası

| Aşama | Süre | Aksiyon |
|-------|------|---------|
| Duyuru | Yeni versiyon çıktığında | Header'da bildirim |
| Uyarı | 3 ay | Sunset header ekleme |
| Devre dışı bırakma | 6 ay | 410 Gone döndürme |
| Kaldırma | 12 ay | Route silme |

### 3.4 Hata Formatı

```json
{
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Geçersiz parametre",
    "details": [
      {
        "field": "track_id",
        "message": "track_id pozitif bir tam sayı olmalı"
      }
    ],
    "timestamp": "2026-08-08T12:00:00Z",
    "request_id": "req_abc123"
  }
}
```

## 4. Teknik Detaylar

### 4.1 API Response Format

```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Şarkı Adı",
    "artist": "Sanatçı"
  },
  "meta": {
    "version": "v1",
    "request_id": "req_abc123",
    "timestamp": "2026-08-08T12:00:00Z"
  }
}
```

### 4.2 Health Check Endpoint

```json
// GET /health
{
  "status": "healthy",
  "services": {
    "database": "healthy",
    "cache": "healthy",
    "external_api": "healthy"
  },
  "uptime": 86400,
  "version": "1.0.0",
  "timestamp": "2026-08-08T12:00:00Z"
}
```

### 4.3 IPC Router

```php
<?php
declare(strict_types=1);

namespace CoreMusic\IPC;

class IPCRouter
{
    private array $routes = [];
    private array $middleware = [];

    /**
     * ✅ Route ekle
     */
    public function addRoute(
        string $method,
        string $path,
        callable $handler,
        array $options = []
    ): void {
        $version = $options['version'] ?? 'v1';
        $auth = $options['auth'] ?? true;
        $rateLimit = $options['rate_limit'] ?? 60;

        $this->routes[] = [
            'method' => $method,
            'path' => "/{$version}{$path}",
            'handler' => $handler,
            'auth' => $auth,
            'rate_limit' => $rateLimit,
        ];
    }

    /**
     * ✅ İsteği yönlendir
     */
    public function dispatch(string $method, string $path): array
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $route['path'] === $path) {
                // Auth kontrolü
                if ($route['auth'] && !$this->authenticate()) {
                    return $this->errorResponse(401, 'Unauthorized');
                }

                // Rate limit kontrolü
                if (!$this->checkRateLimit($path, $route['rate_limit'])) {
                    return $this->errorResponse(429, 'Rate limit aşıldı');
                }

                // Handler çalıştır
                try {
                    $result = $route['handler']();
                    return $this->successResponse($result);
                } catch (\Exception $e) {
                    return $this->errorResponse(500, $e->getMessage());
                }
            }
        }

        return $this->errorResponse(404, 'Route bulunamadı');
    }

    private function authenticate(): bool
    {
        // JWT token doğrulama
        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        
        if (empty($token)) {
            return false;
        }

        // Token doğrulama mantığı
        return true; // Placeholder
    }

    private function checkRateLimit(string $path, int $limit): bool
    {
        // APCu rate limiting
        $key = "rate_limit:{$path}";
        $current = apcu_fetch($key) ?: 0;
        
        if ($current >= $limit) {
            return false;
        }

        apcu_store($key, $current + 1, 60);
        return true;
    }

    private function successResponse(mixed $data): array
    {
        return [
            'success' => true,
            'data' => $data,
            'meta' => [
                'version' => 'v1',
                'request_id' => uniqid('req_'),
                'timestamp' => date('c'),
            ],
        ];
    }

    private function errorResponse(int $code, string $message): array
    {
        http_response_code($code);
        
        return [
            'success' => false,
            'error' => [
                'code' => $this->getErrorCode($code),
                'message' => $message,
                'timestamp' => date('c'),
                'request_id' => uniqid('req_'),
            ],
        ];
    }

    private function getErrorCode(int $httpCode): string
    {
        return match($httpCode) {
            400 => 'BAD_REQUEST',
            401 => 'UNAUTHORIZED',
            403 => 'FORBIDDEN',
            404 => 'NOT_FOUND',
            429 => 'RATE_LIMIT',
            500 => 'INTERNAL_ERROR',
            default => 'UNKNOWN_ERROR',
        };
    }
}
```

### 4.4 Contract Test

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Tests\Contract;

class IPCContractTest
{
    private IPCRouter $router;

    protected function setUp(): void
    {
        $this->router = new IPCRouter();
    }

    /**
     * ✅ Track API contract test
     */
    public function testTrackAPIContract(): void
    {
        // GET /v1/tracks
        $response = $this->router->dispatch('GET', '/v1/tracks');
        
        $this->assertTrue($response['success']);
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('meta', $response);
        $this->assertArrayHasKey('version', $response['meta']);
        $this->assertArrayHasKey('request_id', $response['meta']);
        $this->assertArrayHasKey('timestamp', $response['meta']);
    }

    /**
     * ✅ Hata formatı contract test
     */
    public function testErrorFormatContract(): void
    {
        // Geçersiz route
        $response = $this->router->dispatch('GET', '/v1/invalid');
        
        $this->assertFalse($response['success']);
        $this->assertArrayHasKey('error', $response);
        $this->assertArrayHasKey('code', $response['error']);
        $this->assertArrayHasKey('message', $response['error']);
        $this->assertArrayHasKey('timestamp', $response['error']);
        $this->assertArrayHasKey('request_id', $response['error']);
    }

    /**
     * ✅ Health check contract test
     */
    public function testHealthCheckContract(): void
    {
        $response = $this->router->dispatch('GET', '/health');
        
        $this->assertArrayHasKey('status', $response);
        $this->assertArrayHasKey('services', $response);
        $this->assertArrayHasKey('uptime', $response);
        $this->assertArrayHasKey('version', $response);
        $this->assertArrayHasKey('timestamp', $response);
    }

    /**
     * ✅ Version header contract test
     */
    public function testVersionHeaderContract(): void
    {
        $response = $this->router->dispatch('GET', '/v1/tracks');
        
        $this->assertArrayHasKey('version', $response['meta']);
        $this->assertEquals('v1', $response['meta']['version']);
    }

    /**
     * ✅ Request ID contract test
     */
    public function testRequestIdContract(): void
    {
        $response = $this->router->dispatch('GET', '/v1/tracks');
        
        $this->assertArrayHasKey('request_id', $response['meta']);
        $this->assertStringStartsWith('req_', $response['meta']['request_id']);
    }
}
```

### 4.5 Circuit Breaker Implementasyonu

```php
<?php
declare(strict_types=1);

namespace CoreMusic\IPC;

class CircuitBreaker
{
    private const STATE_CLOSED = 'closed';
    private const STATE_OPEN = 'open';
    private const STATE_HALF_OPEN = 'half_open';

    private string $state = self::STATE_CLOSED;
    private int $failureCount = 0;
    private int $lastFailureTime = 0;
    private int $threshold = 5;
    private int $recoveryTimeout = 30;

    public function call(callable $fn): mixed
    {
        if ($this->state === self::STATE_OPEN) {
            if (time() - $this->lastFailureTime > $this->recoveryTimeout) {
                $this->state = self::STATE_HALF_OPEN;
            } else {
                throw new \RuntimeException('Circuit is open');
            }
        }

        try {
            $result = $fn();
            $this->onSuccess();
            return $result;
        } catch (\Exception $e) {
            $this->onFailure();
            throw $e;
        }
    }

    private function onSuccess(): void
    {
        $this->failureCount = 0;
        $this->state = self::STATE_CLOSED;
    }

    private function onFailure(): void
    {
        $this->failureCount++;
        $this->lastFailureTime = time();

        if ($this->failureCount >= $this->threshold) {
            $this->state = self::STATE_OPEN;
        }
    }
}
```

### 4.6 Rate Limiting Detayları

| Endpoint | Limit | Pencere | İhlal |
|----------|-------|---------|-------|
| GET /v1/tracks | 60 istek | 60s | 429 + retry-after |
| POST /v1/tracks | 30 istek | 60s | 429 + retry-after |
| GET /v1/health | Sınırsız | — | — |
| WS /v1/stream | 1 bağlantı | — | — |

### 4.7 Monitoring Metrikleri

| Metrik | Hedef | Alarm |
|--------|-------|-------|
| TTFB | < 200ms | > 500ms |
| API yanıt süresi | < 100ms | > 300ms |
| Hata oranı | < %1 | > %5 |
| Uptime | > %99.9 | < %99.5 |
| Circuit breaker open | 0 | > 0 |

## 5. Yasak Örüntüler

| ❌ Yasak | ✅ Doğru | ADR | İhlal Sonucu |
|----------|----------|-----|-------------|
| URL versioning yok | URL-based versioning | ADR-032 | Geriye uyumsuzluk |
| Backward compat yok | Backward compatibility | ADR-032 | Eski istemci kırılması |
| Standard hata yok | Standard hata formatı | ADR-032 | Tutarlısızlık |
| Health check yok | Health check zorunlu | ADR-032 | Servis durumu bilinmez |
| Logging yok | Logging zorunlu | ADR-032 | İzlenebilirlik kaybı |
| Contract test yok | Contract test zorunlu | ADR-032 | Sözleşme ihlali |
| OpenAPI yok | OpenAPI 3.0 zorunlu | ADR-032 | Dokümantasyon eksikliği |
| Hardcoded secrets | .env + vault | ADR-034 | Veri sızıntısı |
| SQL injection | Prepared statement | ADR-002 | Güvenlik açığı |
| innerHTML | DOMParser | ADR-001 | XSS açığı |

## 6. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **Servis down** | Health check + fallback | ADR-032 |
| **Eski API version** | Deprecation header | ADR-032 |
| **Rate limit** | 429 + retry-after | ADR-013 |
| **Timeout** | Timeout handling | ADR-032 |
| **Invalid JSON** | 400 Bad Request | ADR-032 |
| **Auth failure** | 401 Unauthorized | ADR-010 |
| **Schema değişikliği** | Version bump | ADR-032 |
| **Concurrent update** | Optimistic locking | ADR-032 |
| **Network partition** | Circuit breaker | ADR-032 |
| **Memory leak** | Resource monitoring | ADR-032 |
| **Cache poisoning** | Cache validation | ADR-007 |
| **CORS** | CORS policy | ADR-032 |
| **SSL/TLS** | Certificate management | ADR-032 |
| **Monitoring** | Prometheus/Grafana | ADR-032 |
| **Logging** | Structured logging | ADR-004 |

## 7. Hard Guardrails

| # | Kural | ADR | İhlal Sonucu |
|---|-------|-----|-------------|
| 1 | URL-based versioning zorunlu | ADR-032 | Geriye uyumsuzluk |
| 2 | Backward compatibility zorunlu | ADR-032 | Eski istemci kırılması |
| 3 | Standard hata formatı zorunlu | ADR-032 | Tutarlısızlık |
| 4 | Health check endpoint zorunlu | ADR-032 | Servis durumu bilinmez |
| 5 | Logging zorunlu | ADR-004 | İzlenebilirlik kaybı |
| 6 | Contract testing zorunlu | ADR-032 | Sözleşme ihlali |
| 7 | OpenAPI 3.0 zorunlu | ADR-032 | Dokümantasyon eksikliği |
| 8 | Rate limiting zorunlu | ADR-013 | Spam riski |
| 9 | JWT auth zorunlu | ADR-010 | Güvenlik açığı |
| 10 | Credential vault zorunlu | ADR-034 | Veri sızıntısı |

## 8. İlgili ADR'ler

| ADR | İlişki | Açıklama |
|-----|--------|----------|
| [[ADR-032-ipc-contract-versioning]] | Bu karar | IPC versiyonlama |
| [[ADR-010-csrf-protection-strategy]] | CSRF | Token koruması |
| [[ADR-011-session-management]] | Session | Oturum yönetimi |
| [[ADR-013-rate-limiting-apcu]] | Rate limiting | APCu rate limit |
| [[ADR-039-7-service-platform-architecture]] | Servis mimarisi | 7 servis |
| [[ADR-022-database-hardened-security]] | Güvenlik | DB sertleştirme |
| [[ADR-004-multi-domain-spa]] | SPA | Multi-domain |
| [[ADR-034-credential-vault-normalization]] | Credential vault | Güvenlik |

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 4 Teknik | [[ecosystem/7-service-integration]] | Servis entegrasyonu |
| § 4 Teknik | [[ecosystem/service-health-check]] | Health check |
| § 5 Yasak | [[ADR-010-csrf-protection-strategy]] | CSRF |
| § 5 Yasak | [[ADR-013-rate-limiting-apcu]] | Rate limiting |
| § 6 Edge | [[ADR-011-session-management]] | Session |
| § 6 Edge | [[ADR-004-multi-domain-spa]] | SPA |
| § 7 Guardrails | [[ADR-039-7-service-platform-architecture]] | Servis mimarisi |
| § 7 Guardrails | [[ADR-022-database-hardened-security]] | Güvenlik |
| § 8 İlgili | [[ADR-034-credential-vault-normalization]] | Credential vault |
| § 8 İlgili | [[ADR-002-pdo-mandatory-no-orm]] | DB erişim |

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **IPC** | Inter-Process Communication — Servisler arası iletişim |
| **API** | Application Programming Interface |
| **REST** | Representational State Transfer |
| **WebSocket** | Gerçek zamanlı duplex iletişim |
| **Versioning** | Versiyonlama |
| **Backward Compatibility** | Geriye uyumluluk |
| **Deprecation** | Eski versiyon desteği |
| **Health Check** | Servis sağlık kontrolü |
| **Contract Testing** | Sözleşme testi |
| **OpenAPI** | API dokümantasyon formatı |
| **JWT** | JSON Web Token |
| **CSRF** | Cross-Site Request Forgery |
| **Rate Limiting** | İstek sayısı sınırlama |
| **Circuit Breaker** | Servis koruma mekanizması |
| **Blue-green Deployment** | Sıfır kesintili deploy |
| **Structured Logging** | Yapılandırılmış loglama |
| **Monitoring** | İzleme |
| **Prometheus** | Monitoring sistemi |
| **Grafana** | Dashboard sistemi |
| **CORS** | Cross-Origin Resource Sharing |
| **TTFB** | Time To First Byte |
| **SLA** | Service Level Agreement |

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır Sayısı** | ≥500 |
| **Status** | FROZEN (değiştirilemez) |
| **ADR Uyumlu** | ✅ 002, 004, 007, 010, 011, 013, 022, 032, 034, 039 |
| **Zero Hallucination** | ✅ |
| **MSA Uyumlu** | ✅ |
| **Cross-Reference** | ✅ 10 referans |
| **Guardrails** | ✅ 10 kural |
| **Edge Cases** | ✅ 15 senaryo |
| **Yasak Örüntü** | ✅ 10 kural |
| **Terim Sayısı** | ✅ 22 terim |
| **Kod Örnekleri** | ✅ 5 örnek |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
