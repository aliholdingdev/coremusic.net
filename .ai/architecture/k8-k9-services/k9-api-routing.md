---
title: "K9 — API & Routing Katmanı (API & Routing)"
type: architecture
category: layer-definition
date: 2026-09-18
updated: 2026-09-18
status: draft
version: 1.0.0
authority: Bayram Ali / Vault Steward
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/architecture/k9-api-routing.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/brain.md"
  layer: K9
  component_count: 40
  adr:
    - "[[ADR-083-spa-router]]"
    - "[[ADR-084-api-gateway-architecture]]"
    - "[[ADR-086-event-driven-architecture]]"
  github:
    - name: "League Router"
      url: "https://github.com/thephpleague/router"
    - name: "PSR-14 Event Dispatcher"
      url: "https://www.php-fig.org/psr/psr-14/"
    - name: "Nikic Fast Route"
      url: "https://github.com/nikic/FastRoute"
  related:
    - "[[CLAUDE.md]]"
    - "[[AGENTS.md]]"
    - "[[brain.md]]"
---

# K9 — API & Routing Katmanı (API & Routing)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[brain.md]]

**Kapsam:** CoreMusic ekosistemi için API Gateway, SPA Router, CQRS, Event Bus ve API yönetimi. 40 bileşen.

---

## 1. Genel Bakış

K9 katmanı, tüm istemcilerin API'ye erişimini yöneten Gateway, SPA routing, CQRS pattern ve event bus altyapısını tanımlar. Bu katman, K8 (Services) ve K7 (Middleware) ile entegre çalışır.

### 1.1 Routing Mimarisi Diyagramı

```
┌─────────────────────────────────────────────────────────────────────┐
│                   K9 — API & ROUTING LAYER (40)                     │
├──────────────┬──────────────┬──────────────┬────────────────────────┤
│  GATEWAY (6) │  BFF (6)     │  CQRS (4)    │  EVENT (6)             │
│              │              │              │                        │
│  API Gateway │  SPA BFF     │  Command     │  Event Bus (PSR-14)    │
│  Rate Limit  │  Mobile BFF  │  Query       │  Event Listener        │
│  Auth        │  Embedded BFF│  Event       │  Event Subscriber      │
│  Routing     │  Desktop BFF │  Read Model  │  Domain Event          │
│  Validation  │  Admin BFF   │              │  Integration Event     │
│  Logging     │  Car BFF     │              │  Event Store           │
├──────────────┴──────────────┴──────────────┴────────────────────────┤
│  SPA ROUTER (8)  │  API MGMT (6)  │  REALTIME (4) │  DOCS (4)       │
│                  │                │               │                  │
│  JS Router       │  Versioning    │  WebSocket    │  OpenAPI Spec    │
│  Route Guard     │  OpenAPI Spec  │  SSE          │  Generator       │
│  Lazy Load       │  Generator     │  GraphQL      │  Validator       │
│  History API     │  Validator     │  gRPC         │  Changelog       │
│  Route Config    │  REST Resource │               │                  │
│  Guard Pipeline  │  API Docs      │               │                  │
│  DomPatcher      │  Changelog     │               │                  │
│  Cache Layer     │                │               │                  │
└──────────────────┴────────────────┴───────────────┴──────────────────┘
```

---

## 2. API Gateway (6 Bileşen)

Tüm istemcilerin tek giriş noktası. Port: 80/81.

| # | Bileşen | Tanım | Teknoloji |
|---|---------|-------|-----------|
| 1 | API Gateway | Tek giriş noktası (api.coremusic.net) | PHP 8.4 |
| 2 | Gateway — Rate Limit | İstemci bazlı rate limiting | APCu/Redis |
| 3 | Gateway — Auth | Token bazlı kimlik doğrulama | JWT |
| 4 | Gateway — Routing | URL → servis yönlendirme | FastRoute |
| 5 | Gateway — Validation | Request/Response validasyonu | Respect Validation |
| 6 | Gateway — Logging | İstek/yanıt loglama (correlation ID) | PSR-3 |

### 2.1 Gateway Akışı

```
İstemci → API Gateway (#1)
  → Rate Limit (#2) → Auth (#3) → Routing (#4)
  → Validation (#5) → İlgili Servis (K8)
  → Response → Logging (#6) → İstemci
```

---

## 3. BFF — Backend for Frontend (6 Bileşen)

Her istemci tipi kendi BFF'sini kullanır. Minimal veri, optimize yanıt.

| # | Bileşen | Tanım | Response Boyutu |
|---|---------|-------|-----------------|
| 7 | SPA BFF | Tam veri (web panel için) | Büyük |
| 8 | Mobile BFF | Minimal veri (mobil için) | Küçük |
| 9 | Embedded BFF | Ultra-minimal, gzip (RPi5 için) | Minimal |
| 10 | Desktop BFF | Orta boy (masaüstü için) | Orta |
| 11 | Admin BFF | Full + audit (yönetim paneli için) | Büyük + audit |
| 12 | Car BFF | Touch-optimized (araç içi için) | Minimal + touch |

### 3.1 BFF Response Karşılaştırması

| Alan | SPA | Mobile | Embedded | Desktop | Admin | Car |
|------|-----|--------|----------|---------|-------|-----|
| user.profile | Tam | Minimal | Yok | Orta | Tam + audit | Minimal |
| tracks[] | 50/sayfa | 20/sayfa | 10/sayfa | 30/sayfa | 100/sayfa | 15/sayfa |
| metadata | Tümü | Temel | Yok | Orta | Tümü + log | Temel |
| analytics | Yok | Yok | Yok | Yok | Tam | Yok |
| gzip | Evet | Evet | Evet (max) | Evet | Evet | Evet |

---

## 4. CQRS Pattern (4 Bileşen)

Yazma ve okuma işlemleri tamamen ayrılır.

| # | Bileşen | Tanım | Akış |
|---|---------|-------|------|
| 13 | Command | Yazma işlemleri (Create, Update, Delete) | Command → Use Case → Repository → MySQL Master |
| 14 | Query | Okuma işlemleri (Read) | Query → Read Model → Cache → Response |
| 15 | Event | Domain event'lerin yayılması | Service → Event Bus → Subscriber |
| 16 | Read Model | Okuma için optimize edilmiş veri modelleri | Cache + DB replica |

### 4.1 CQRS Akışı

```
WRITE:
  Client → Command → Use Case → Domain → Repository → MySQL Master
    → Event Published → Read Model Updated

READ:
  Client → Query → Read Model → Cache → Response
    (Cache miss → MySQL Replica → Cache → Response)
```

### 4.2 CQRS Kuralları

| Kural | Açıklama |
|-------|----------|
| Write path | Sadece MySQL Master'a yazar |
| Read path | MySQL Replica veya Cache'den okur |
| Event propagation | Async event ile read model güncellenir |
| Consistency | Eventual consistency kabul edilir |
| Snapshot | Sık okunan veriler cache'de snapshot olarak saklanır |

---

## 5. Event Bus — PSR-14 (6 Bileşen)

Servisler birbirini doğrudan çağırmaz, event yayınlar.

| # | Bileşen | Tanım | Teknoloji |
|---|---------|-------|-----------|
| 17 | Event Bus | PSR-14 uyumlu event dispatcher | Symfony EventDispatcher |
| 18 | Event Listener | Event dinleyicileri (tekil işleyiciler) | PSR-14 |
| 19 | Event Subscriber | Event aboneleri (çoklu event dinleme) | PSR-14 |
| 20 | Domain Event | İş mantığı event'leri (UserCreated, TrackPlayed) | Value Object |
| 21 | Integration Event | Servisler arası event'ler (Cross-service) | PSR-14 |
| 22 | Event Store | Event geçmişi ve replay desteği | DB + Redis |

### 5.1 Event Types

```php
// Domain Events
UserLoggedIn::class       // Auth → Notification, Analytics
TrackDownloaded::class    // Download → Media, Cache
PlaybackStarted::class    // Audio → Analytics, Recommendation
PlaylistCreated::class    // Media → Social, Notification
DeviceConnected::class    // Device → Sync, Notification

// Integration Events
OrderPlaced::class        // Download → Queue
MetadataUpdated::class    // Media → AI, Search
CacheInvalidated::class   // Cache → All Services
```

### 5.2 Event Dispatcher Akışı

```
Service A
  → EventDispatcher->dispatch(new TrackDownloaded($track))
    → Listener 1: CacheService → cache'le
    → Listener 2: AnalyticsService → kaydet
    → Listener 3: NotificationService → bildirim gönder
    → Subscriber: SearchService → indeksle
```

---

## 6. SPA Router (8 Bileşen)

Frontend SPA routing altyapısı (ADR-083).

| # | Bileşen | Tanım | Teknoloji |
|---|---------|-------|-----------|
| 23 | JS Router | Client-side SPA router (History API) | Vanilla JS |
| 24 | Route Guard | Route koruyucuları (auth, permission) | JS |
| 25 | Lazy Load | Route bazlı modül yükleme | Dynamic import |
| 26 | History API | `pushState` / `popstate` yönetimi | Browser API |
| 27 | Route Config | Route tanımları ve metadata'sı | JS Object |
| 28 | Guard Pipeline | Guard zinciri (sıralı kontrol) | JS |
| 29 | DomPatcher | DOM diff ve patch (minimal güncelleme) | JS |
| 30 | Cache Layer | Route bazlı veri önbelleği | JS Map |

### 6.1 SPA Router Akışı

```
Kullanıcı → Link Tıklama
  → JS Router (#23)
    → Guard Pipeline (#28): Auth → Permission → Validation
    → Lazy Load (#25): Modülü yükle
    → DomPatcher (#29): DOM'u güncelle
    → History API (#26): URL'yi güncelle
```

### 6.2 Route Guard Pipeline

```
Route Change
  → GuardPipeline:
    1. AuthGuard: Giriş yapılmış mı?
    2. PermissionGuard: Yetki var mı?
    3. ValidationGuard: Route parametreleri geçerli mi?
    4. CacheGuard: Veri cache'de var mı?
  → Tüm guard'lar geçerse → Route yüklenir
  → Herhangi biri başarısızsa → Redirect (login/error)
```

---

## 7. API Management (6 Bileşen)

API versiyonlama, dokümantasyon ve yaşam döngüsü yönetimi.

| # | Bileşen | Tanım | Teknoloji |
|---|---------|-------|-----------|
| 31 | API Versioning | URL veya header'dan versiyon (`/api/v1/`) | FastRoute |
| 32 | OpenAPI Spec | OpenAPI 3.1 specification oluşturma | YAML |
| 33 | OpenAPI Generator | Spec'ten API client kodu üretimi | openapi-generator |
| 34 | OpenAPI Validator | Request/Response OpenAPI uyumluluk kontrolü | cebe/psr7-assert |
| 35 | REST Resource | RESTful kaynak tanımları (CRUD) | PSR-7 |
| 36 | API Documentation | Otomatik API dokümantasyonu (Swagger UI) | Swagger |

### 7.1 API Versiyonlama Stratejisi

```
URL-based: /api/v1/tracks, /api/v2/tracks
Header-based: Accept: application/vnd.coremusic.v1+json

Versiyon Kuralı:
  → v1: Mevcut API (stable)
  → v2: Breaking changes (migration gerektirir)
  → Deprecated: Sunset header ile 6 ay uyarı
```

---

## 8. Real-time Communication (4 Bileşen)

Gerçek zamanlı iletişim protokolleri.

| # | Bileşen | Tanım | Teknoloji |
|---|---------|-------|-----------|
| 37 | WebSocket | Tam duplex iletişim (chat, live update) | Ratchet |
| 38 | Server-Sent Events | Tek yönlü streaming (notifications) | PHP |
| 39 | GraphQL | Esnek sorgulama (opsiyonel) | webonyx/graphql-php |
| 40 | gRPC | Yüksek performanslı RPC (opsiyonel) | grpc/grpc |

---

## 9. SPA → ApiClient Kuralı

```
SPA → ApiClient → HTTP → Gateway → Middleware → Use Case → Domain → Repository → Infrastructure
```

**SPA asla** PDO, MySQL, Repository, Entity, Infrastructure, Filesystem, FFmpeg, Redis, Cache veya SQL **görmez.**

### 9.1 ApiClient Implementasyonu

```javascript
// assets.coremusic.net/js/api-client.js
class ApiClient {
  constructor(baseUrl) {
    this.baseUrl = baseUrl;
    this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
  }

  async get(endpoint) {
    const response = await fetch(`${this.baseUrl}${endpoint}`, {
      headers: {
        'X-CSRF-Token': this.csrfToken,
        'Accept': 'application/json',
      },
      credentials: 'same-origin',
    });
    return response.json();
  }

  async post(endpoint, data) {
    const response = await fetch(`${this.baseUrl}${endpoint}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': this.csrfToken,
        'Accept': 'application/json',
      },
      credentials: 'same-origin',
      body: JSON.stringify(data),
    });
    return response.json();
  }
}
```

---

## 10. Cross References

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| K9 API Routing | K7 Middleware | Pipeline entegrasyonu |
| K9 API Routing | K8 Services | Servis yönlendirme |
| K9 → ADR-083 | SPA Router | PHP+JS Hybrid |
| K9 → ADR-084 | API Gateway | API-First, BFF, CQRS |
| K9 → ADR-086 | Event Driven | PSR-14 |
| K9 → League Router | GitHub | PHP router |
| K9 → PSR-14 | php-fig.org | Event dispatcher standardı |
| K9 → brain.md §4B | API Architecture | BFF, CQRS detayları |

---

## 11. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | draft |
| Total Components | 40 |
| API Gateway | 6 |
| BFF | 6 |
| CQRS | 4 |
| Event Bus | 6 |
| SPA Router | 8 |
| API Management | 6 |
| Real-time | 4 |
| ADR Coverage | 3 ADR referansı |
| GitHub References | League Router, PSR-14, FastRoute |

---

## 12. Class AB API Entegrasyonu

- [[electronics/amplifier-classab-circuit]] — Amplifikator API endpoint'leri
- [[electronics/power-supply-classab]] — Guç durumu API

---

## Contracts Detayı (Eski 03-contracts/ Referansları)

### API Gateway

```
┌─────────────────────────────────────────────────────────────────────┐
│                    API GATEWAY MİMARİSİ                              │
│                                                                     │
│  ┌──────────────┐     ┌──────────────┐     ┌──────────────┐       │
│  │ SPA Client   │────►│              │     │              │       │
│  ├──────────────┤     │   API        │     │   Backend    │       │
│  │ Mobile Client│────►│   Gateway    │────►│   Services   │       │
│  ├──────────────┤     │              │     │              │       │
│  │ Embedded     │────►│  api.core-   │     │  Control     │       │
│  ├──────────────┤     │  music.net   │     │  Media       │       │
│  │ Desktop      │────►│              │     │  Audio       │       │
│  └──────────────┘     └──────────────┘     └──────────────┘       │
│                                                                     │
│  Gateway Görevleri:                                                 │
│  • Routing (path-based)                                            │
│  • Authentication (JWT verify)                                     │
│  • Rate Limiting (per-client)                                      │
│  • Request Validation (OpenAPI schema)                             │
│  • Logging (correlation ID)                                        │
│  • Response Caching (Redis)                                        │
└─────────────────────────────────────────────────────────────────────┘
```

### BFF (Backend for Frontend)

| İstemci | BFF | Response Boyutu | Özellikler |
|---------|-----|-----------------|------------|
| SPA | SPA BFF | Tam veri | Full metadata |
| Mobile | Mobile BFF | Minimal | Düşük bant genişliği |
| Embedded (RPi5) | Embedded BFF | Ultra-minimal | Gzip sıkıştırma |
| Desktop | Desktop BFF | Orta boy | Cache-first |
| Admin | Admin BFF | Full + audit | Audit trail |
| Car | Car BFF | Touch-optimized | Large touch targets |

### CQRS Pattern

```
YAZMA (Command):
  Command → Use Case → Repository → MySQL Master
  Event → Event Bus → Other Services

OKUMA (Query):
  Query → Read Model → Cache → Response
  (MySQL Slave / Redis Cache)
```

### API Versioning

| Strateji | Kullanım | Örnek |
|----------|----------|-------|
| URL-based | Varsayılan | /api/v1/songs |
| Header-based | Backward compat | Accept: application/vnd.coremusic.v1+json |
| Query param | Geçici | /api/songs?version=1 |

### OpenAPI Spec

- Spec format: OpenAPI 3.1
- Code generation: auto-generated from PHP attributes
- Validation: Request/Response DTO validation
- Documentation: /api/docs endpoint

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
