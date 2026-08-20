---
type: architecture
category: contracts
title: "Enterprise Architecture Rules & Governance"
date: 2026-08-09
updated: 2026-08-12
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Enterprise Architecture Rules & Governance

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]] · [[ADR-087-master-implementation-plan]]

## 1. Amaç

CoreMusic platformunda geçerli olan mimari kuralları, governance süreçlerini ve standartları tanımlar.

## 2. Mimari Prensipler

### 2.1 Temel İlkeler

| İlke | Açıklama |
|------|----------|
| **SOLID** | Tek Sorumluluk, Açık Kapalılık, Yerine Koyma, Arayüz Ayrımı, Bağımlılık Tersi |
| **Clean Architecture** | Infrastructure → Security → Routing → Presentation |
| **Hexagonal Architecture** | Adapter/Port pattern ile bağımsızlık |
| **Domain-Driven Design** | Aggregate Root, Entity, Value Object, Domain Event |
| **CQRS** | Command Query Responsibility Segregation |
| **Event Driven** | Domain Events, Integration Events |
| **DRY** | Tekrarlanan kod yasağı |
| **YAGNI** | Gereksiz özellik ekleme yasağı |

### 2.2 Katman Bağımlılığı

```
L3 Presentation → L2 Routing → L1 Security → L0 Infrastructure
```

Kural: ✅ L3→L2, L2→L1, L1→L0 | ❌ L0→L2/L3, L1→L3, L3→L0

Layer Violation tespit edilirse derhal revert + log CRITICAL.

## 3. Domain-Driven Design Kuralları

### 3.1 Aggregate Root

- Her Aggregate Root bir Repository'ye sahip olmalıdır
- Aggregate Root'lar kendi iç bütünlüklerini korurlar
- Aggregate'ler arası referans ID ile yapılır
- Aggregate'ler transaction boundary oluşturur

### 3.2 Entity

- Her Entity bir ID'ye sahiptir
- Entity'ler mutable olabilir
- Entity'ler equality check yapar

### 3.3 Value Object

- Value Object'ler immutable olmalıdır
- Value Object'ler equality check yapar
- Value Object'ler ID'ye ihtiyaç duymaz

### 3.4 Domain Event

- Domain Event'ler immutable olmalıdır
- Domain Event'ler timestamp içermelidir
- Domain Event'ler aggregate ID içermelidir
- Domain Event'ler payload içermelidir

## 4. CQRS Kuralları

### 4.1 Command

- Command'ler immutable olmalıdır
- Command'ler handler tarafından işlenir
- Command'ler validation içerebilir
- Command'ler side effect yaratabilir

### 4.2 Query

- Query'ler immutable olmalıdır
- Query'ler handler tarafından işlenir
- Query'ler validation içerebilir
- Query'ler side effect yaratmaz

### 4.3 Handler

- Her Command/Query için bir Handler olmalıdır
- Handler'lar tek sorumluluk prensibine uymalıdır
- Handler'lar bağımlılıklarını constructor injection ile almalıdır

## 5. Event Driven Kuralları

### 5.1 Domain Event

- Domain Event'ler aggregate değişikliklerinde yayınlanır
- Domain Event'ler同一 aggregate içinde publish edilir
- Domain Event'ler transaction commit sonrası dispatch edilir

### 5.2 Integration Event

- Integration Event'ler servisler arası iletişim için kullanılır
- Integration Event'ler message queue üzerinden yayınlanır
- Integration Event'ler idempotent olmalıdır

### 5.3 Event Handler

- Event Handler'lar side effect yaratabilir
- Event Handler'lar async çalışabilir
- Event Handler'lar error handling içermelidir

## 6. API Tasarım Kuralları

### 6.1 URL Yapısı

```
https://api.coremusic.net/v1/{resource}
```

### 6.2 HTTP Methodları

| Method | Amaç | Idempotent |
|--------|------|------------|
| GET | Resource okuma | ✅ |
| POST | Resource oluşturma | ❌ |
| PUT | Resource güncelleme | ✅ |
| PATCH | Partial güncelleme | ✅ |
| DELETE | Resource silme | ✅ |

### 6.3 Response Formatı

```json
{
  "status": "success",
  "data": {},
  "meta": {
    "timestamp": "2026-08-09T12:00:00Z",
    "version": "v1"
  }
}
```

### 6.4 Hata Formatı

```json
{
  "status": "error",
  "error": {
    "code": "VALIDATION_ERROR",
    "message": "Invalid input",
    "details": []
  }
}
```

### 6.5 Versioning

```
/api/v1/resource
```

## 6.1 Enterprise Router Kuralları

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| **Engine** | `nikic/fast-route` | — |
| **DI Container** | `php-di/php-di` (PSR-11) | — |
| **HTTP** | `nyholm/psr7` (PSR-7) | — |
| **Middleware** | PSR-15 (`MiddlewareInterface`) | — |
| **HTTP Emitter** | `laminas/laminas-httphandlerrunner` | — |

### Enterprise Router Özellikleri

| Özellik | Açıklama |
|---------|----------|
| **Attribute Routes** | PHP 8 attributes ile route tanımlama (`#[Route('/path')]`) |
| **Route Groups** | Prefix bazlı gruplama (`/api/v1` altında tüm API route'ları) |
| **Subdomain Routing** | Alt adrese göre route yönlendirme |
| **Route Cache** | APCu + file cache ile route caching |
| **Named Routes** | İsimlendirilmiş route'lar (`route('login')`) |
| **Middleware Binding** | Route bazlı middleware atama |
| **DI Integration** | Controller'lar DI container tarafından resolve edilir |

### Route Tanımlama Örneği

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Router\Attributes;

use Attribute;

#[Route('/api/v1/songs', methods: ['GET'], middleware: ['auth', 'rate-limit'])]
class SongController
{
    public function index(): ResponseInterface
    {
        // Controller logic
    }
}
```

### Subdomain Routing Örneği

```
home.coremusic.net   → HomeController   (port 81)
pro.coremusic.net    → ProController    (port 81)
studio.coremusic.net → StudioController (port 81)
admin.coremusic.net  → AdminController  (port 80)
auth.coremusic.net   → AuthController   (port 80/443)
```

## 7. BFF (Backend for Frontend) Kuralları

### 7.1 Client Types

| Client | BFF | Port |
|--------|-----|------|
| SPA Browser | api.coremusic.net | 81 |
| Embedded (Home/Pro/Studio) | home.api.coremusic.net | 81 |
| Car Infotainment | car.api.coremusic.net | 81 |
| Mobile | mobile.api.coremusic.net | 81 |
| Admin | admin.api.coremusic.net | 81 |

### 7.2 BFF Özellikleri

- Her client type için özel BFF
- BFF'ler client-specific API sunar
- BFF'ler auth logic'i share eder
- BFF'ler rate limiting uygular

## 8. Validation Kuralları

### 8.1 Request Validation

- Tüm input'lar validate edilmelidir
- Validation controller level'da yapılır
- Validation error'ları detaylı mesaj döner
- Validation error'ları loglanır

### 8.2 Response Validation

- Response'lar OpenAPI spec'e uygun olmalıdır
- Response'lar test edilmelidir
- Response'lar version control altında olmalıdır

## 9. Error Handling Kuralları

### 9.1 Exception Hierarchy

```
CoreMusicException
├── AuthenticationException
├── AuthorizationException
├── ValidationException
├── NotFoundException
├── ConflictException
├── RateLimitException
├── ExternalServiceException
└── InfrastructureException
```

### 9.2 Error Response

- Tüm hatalar structured response döner
- Hatalar loglanır
- Hassas bilgiler error response'da yer almaz
- Error'lar client'a faydalı mesaj döner

## 10. Logging Kuralları

### 10.1 Log Levels

| Level | Kullanım |
|-------|----------|
| DEBUG | Geliştirme bilgisi |
| INFO | Normal operasyon |
| WARNING | Uyarı durumu |
| ERROR | Hata durumu |
| CRITICAL | Kritik hata |

### 10.2 Log Formatı

```json
{
  "timestamp": "2026-08-09T12:00:00Z",
  "level": "INFO",
  "channel": "auth",
  "message": "User logged in",
  "context": {
    "user_id": "123",
    "ip": "192.168.1.1"
  }
}
```

### 10.3 Loglama Zorunlulukları

- Tüm auth event'leri loglanmalıdır
- Tüm API request'leri loglanmalıdır
- Tüm hatalar loglanmalıdır
- Tüm security event'leri loglanmalıdır
- Hassas veriler log'da masked olmalıdır

## 11. Monitoring Kuralları

### 11.1 Health Check

```
GET /health
```

Response:
```json
{
  "status": "healthy",
  "timestamp": "2026-08-09T12:00:00Z",
  "version": "1.0.0"
}
```

### 11.2 Metrics

- Request count
- Response time
- Error rate
- Active connections
- Memory usage
- CPU usage

### 11.3 Alerting

- High error rate
- High response time
- Memory overflow
- Disk full
- Service down

## 12. Deployment Kuralları

### 12.1 Environment

| Environment | Kullanım |
|-------------|----------|
| local | Geliştirme |
| development | Test |
| staging | Pre-production |
| production | Canlı |

### 12.2 Deployment Pipeline

```
Code → Test → Build → Deploy → Monitor
```

### 12.3 Rollback

- Her deployment için rollback planı olmalıdır
- Rollback test edilmelidir
- Rollback时间 hedefi: <5 dakika

## 13. Code Review Kuralları

### 13.1 Review Gereksinimleri

- Tüm PR'lar reviewed olmalıdır
- En az 1 onay gereklidir
- CI checks passed olmalıdır
- Security review gerekli olabilir

### 13.2 Review Kriterleri

- Kod kalitesi
- Test coverage
- Security uyumluluğu
- Performance
- Dokümantasyon

## 14. Testing Kuralları

### 14.1 Test Pyramid

```
         E2E (10%)
        Integration (20%)
       Unit (70%)
```

### 14.2 Test Coverage

| Modül | Minimum | Hedef |
|-------|---------|-------|
| Backend (PHP) | ≥80% | ≥90% |
| Frontend (JS) | ≥80% | ≥90% |
| Audio Engine (C++) | ≥80% | ≥90% |

### 14.3 Test Types

- Unit Test
- Integration Test
- E2E Test
- Performance Test
- Security Test

## 15. Documentation Kuralları

### 15.1 Zorunlu Dokümanlar

- README.md
- API Documentation (OpenAPI)
- Architecture Decision Records (ADR)
- Code Comments
- CHANGELOG.md

### 15.2 ADR Yaşam Döngüsü

```
Draft → Review → Active → Frozen
```

## 16. Yasaklar

| Yasak | Açıklama |
|-------|----------|
| Custom JWT | JWT implementasyonu yeniden yazılmaz |
| Custom Crypto | Şifreleme algoritması yeniden yazılmaz |
| Custom Hash | Hash algoritması yeniden yazılmaz |
| ORM | Doctrine/Eloquent yasak, PDO zorunlu |
| Framework | Laravel/Symfony yasak, Vanilla PHP zorunlu |
| `SELECT *` | Açık sütun listesi zorunlu |
| `eval()` | Yasak |
| Hardcoded Secret | Kodda secret yasak |
| `mysql_*` | Deprecated fonksiyonlar yasak |
| MD5/SHA1 | Güvensiz hash algoritmaları yasak |

## 17. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Mimari İlke** | 8 |
| **Katman** | 4 |
| **RBAC Rolü** | 7 |
| **API Method** | 5 |
| **BFF Sayısı** | 5 |
| **Zero Hallucination** | ✅ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
