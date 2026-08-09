# Enterprise Architecture Rules — CoreMusic

**Authority:** ADR-051, ADR-052, ADR-053, ADR-054, ADR-055, ADR-056, ADR-057, ADR-058, ADR-059
**Last Updated:** 2026-08-09
**Governing Rules:** Red Team · Human Mode · Truth Mode

---

## 1. Architectural Principles

### 1.1 Core Principles

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

### 1.2 Layer Dependency

```
L3 Presentation → L2 Routing → L1 Security → L0 Infrastructure
```

**Kural:** ✅ L3→L2, L2→L1, L1→L0 | ❌ L0→L2/L3, L1→L3, L3→L0

Layer Violation tespit edilirse derhal revert + log CRITICAL.

## 2. Domain-Driven Design Rules

### 2.1 Aggregate Root

- Her Aggregate Root bir Repository'ye sahip olmalıdır
- Aggregate Root'lar kendi iç bütünlüklerini korurlar
- Aggregate'ler arası referans ID ile yapılır
- Aggregate'ler transaction boundary oluşturur

### 2.2 Entity

- Her Entity bir ID'ye sahiptir
- Entity'ler mutable olabilir
- Entity'ler equality check yapar

### 2.3 Value Object

- Value Object'ler immutable olmalıdır
- Value Object'ler equality check yapar
- Value Object'ler ID'ye ihtiyaç duymaz

### 2.4 Domain Event

- Domain Event'ler immutable olmalıdır
- Domain Event'ler timestamp içermelidir
- Domain Event'ler aggregate ID içermelidir
- Domain Event'ler payload içermelidir

## 3. CQRS Rules

### 3.1 Command

- Command'ler immutable olmalıdır
- Command'ler handler tarafından işlenir
- Command'ler validation içerebilir
- Command'ler side effect yaratabilir

### 3.2 Query

- Query'ler immutable olmalıdır
- Query'ler handler tarafından işlenir
- Query'ler validation içerebilir
- Query'ler side effect yaratmaz

### 3.3 Handler

- Her Command/Query için bir Handler olmalıdır
- Handler'lar tek sorumluluk prensibine uymalıdır
- Handler'lar bağımlılıklarını constructor injection ile almalıdır

## 4. Event Driven Rules

### 4.1 Domain Event

- Domain Event'ler aggregate değişikliklerinde yayınlanır
- Domain Event'ler同一 aggregate içinde publish edilir
- Domain Event'ler transaction commit sonrası dispatch edilir

### 4.2 Integration Event

- Integration Event'ler servisler arası iletişim için kullanılır
- Integration Event'ler message queue üzerinden yayınlanır
- Integration Event'ler idempotent olmalıdır

### 4.3 Event Handler

- Event Handler'lar side effect yaratabilir
- Event Handler'lar async çalışabilir
- Event Handler'lar error handling içermelidir

## 5. API Design Rules

### 5.1 URL Structure

```
https://api.coremusic.net/v1/{resource}
```

### 5.2 HTTP Methods

| Method | Amaç | Idempotent |
|--------|------|------------|
| GET | Resource okuma | ✅ |
| POST | Resource oluşturma | ❌ |
| PUT | Resource güncelleme | ✅ |
| PATCH | Partial güncelleme | ✅ |
| DELETE | Resource silme | ✅ |

### 5.3 Response Format

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

### 5.4 Error Format

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

### 5.5 Versioning

```
/api/v1/resource
```

## 6. BFF (Backend for Frontend) Rules

### 6.1 Client Types

| Client | BFF | Port |
|--------|-----|------|
| SPA Browser | api.coremusic.net | 81 |
| Embedded (Home/Pro/Studio) | home.api.coremusic.net | 81 |
| Car Infotainment | car.api.coremusic.net | 81 |
| Mobile | mobile.api.coremusic.net | 81 |
| Admin | admin.api.coremusic.net | 81 |

### 6.2 BFF Properties

- Her client type için özel BFF
- BFF'ler client-specific API sunar
- BFF'ler auth logic'i share eder
- BFF'ler rate limiting uygular

## 7. Validation Rules

### 7.1 Request Validation

- Tüm input'lar validate edilmelidir
- Validation controller level'da yapılır
- Validation error'ları detaylı mesaj döner
- Validation error'ları loglanır

### 7.2 Response Validation

- Response'lar OpenAPI spec'e uygun olmalıdır
- Response'lar test edilmelidir
- Response'lar version control altında olmalıdır

## 8. Error Handling Rules

### 8.1 Exception Hierarchy

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

### 8.2 Error Response

- Tüm hatalar structured response döner
- Hatalar loglanır
- Hassas bilgiler error response'da yer almaz
- Error'lar client'a faydalı mesaj döner

## 9. Logging Rules

### 9.1 Log Levels

| Level | Kullanım |
|-------|----------|
| DEBUG | Geliştirme bilgisi |
| INFO | Normal operasyon |
| WARNING | Uyarı durumu |
| ERROR | Hata durumu |
| CRITICAL | Kritik hata |

### 9.2 Log Format

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

### 9.3 Mandatory Logging

- Tüm auth event'leri loglanmalıdır
- Tüm API request'leri loglanmalıdır
- Tüm hatalar loglanmalıdır
- Tüm security event'leri loglanmalıdır
- Hassas veriler log'da masked olmalıdır

## 10. Monitoring Rules

### 10.1 Health Check

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

### 10.2 Metrics

- Request count
- Response time
- Error rate
- Active connections
- Memory usage
- CPU usage

### 10.3 Alerting

- High error rate
- High response time
- Memory overflow
- Disk full
- Service down

## 11. Deployment Rules

### 11.1 Environment

| Environment | Kullanım |
|-------------|----------|
| local | Geliştirme |
| development | Test |
| staging | Pre-production |
| production | Canlı |

### 11.2 Deployment Pipeline

```
Code → Test → Build → Deploy → Monitor
```

### 11.3 Rollback

- Her deployment için rollback planı olmalıdır
- Rollback test edilmelidir
- Rollback时间 hedefi: <5 dakika

## 12. Code Review Rules

### 12.1 Review Requirements

- Tüm PR'lar reviewed olmalıdır
- En az 1 onay gereklidir
- CI checks passed olmalıdır
- Security review gerekli olabilir

### 12.2 Review Criteria

- Kod kalitesi
- Test coverage
- Security uyumluluğu
- Performance
- Dokümantasyon

## 13. Testing Rules

### 13.1 Test Pyramid

```
         E2E (10%)
        Integration (20%)
       Unit (70%)
```

### 13.2 Test Coverage

| Modül | Minimum | Hedef |
|-------|---------|-------|
| Backend (PHP) | ≥80% | ≥90% |
| Frontend (JS) | ≥80% | ≥90% |
| Audio Engine (C++) | ≥80% | ≥90% |

### 13.3 Test Types

- Unit Test
- Integration Test
- E2E Test
- Performance Test
- Security Test

## 14. Documentation Rules

### 14.1 Mandatory Documents

- README.md
- API Documentation (OpenAPI)
- Architecture Decision Records (ADR)
- Code Comments
- CHANGELOG.md

### 14.2 ADR Lifecycle

```
Draft → Review → Active → Frozen
```

## 15. Forbidden Patterns

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

## 16. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | SOLID Principles | Kod revert edilir |
| 2 | Clean Architecture | Layer violation |
| 3 | DDD Rules | Domain bozulması |
| 4 | CQRS | Performans düşüklüğü |
| 5 | Event Driven | Bağımlılık artışı |
| 6 | API Design Rules | Tutarlılık bozulması |
| 7 | BFF Pattern | Gereksiz veri transferi |
| 8 | Validation Rules | Güvenlik açığı |
| 9 | Error Handling | Hata yönetimi bozulması |
| 10 | Logging Rules | İzlenebilirlik düşer |
| 11 | Monitoring Rules | Sistem görünürlüğü düşer |
| 12 | Deployment Rules | Deployment riski |
| 13 | Code Review Rules | Kod kalitesi düşer |
| 14 | Testing Rules | Test coverage düşer |
| 15 | Documentation Rules | Dokümantasyon eksikliği |

---

*Enterprise Architecture Rules v1.0.0 — CoreMusic Enterprise*
*Last Updated: 2026-08-09*
