---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K9 API & Routing Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-24
source: "3 turlu agent tartışması"
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K9: API & Routing Layer

**Katman:** K9 (API & Routing)
**Kapsam:** Gateway, BFF, CQRS, Event Bus, SPA Router, OpenAPI
**Sorumlu Agent:** Backend Architect
**Bileşen Sayısı:** 30

---

## 1. Genel Bakış

K9 katmanı, tüm istemcilerin API Gateway üzerinden bağlandığı API katmanını içerir. API-First (ADR-084) prensibiyle çalışır.

---

## 2. API Gateway

### 2.1 Gateway Yapısı

```
Client → API Gateway → Middleware Pipeline → Use Case → Repository → Infrastructure

Gateway Responsibilities:
  - Routing
  - Authentication
  - Rate Limiting
  - Validation
  - Logging
  - Correlation ID
```

### 2.2 Gateway Kuralları

| Kural | Açıklama |
|-------|----------|
| Tek giriş noktası | `api.coremusic.net` |
| Auth zorunlu | JWT + Session |
| Rate limit | 60 req/60s |
| Validation | Request/DTO validasyonu |
| Correlation ID | Her isteğe benzersiz ID |

---

## 3. BFF (Backend for Frontend)

| İstemci | BFF | Response |
|---------|-----|----------|
| SPA | SPA BFF | Tam veri |
| Mobile | Mobile BFF | Minimal |
| Embedded (RPi5) | Embedded BFF | Ultra-minimal, gzip |
| Desktop | Desktop BFF | Orta boy |
| Admin | Admin BFF | Full + audit |
| Car | Car BFF | Touch-optimized |

---

## 4. CQRS

```
Write: Command → Use Case → Repository → MySQL Master
Read:  Query → Read Model → Cache → Response
```

### 4.1 Command/Query Ayrımı

| Operation | Type | Target |
|-----------|------|--------|
| Create playlist | Command | MySQL Master |
| Update track | Command | MySQL Master |
| Delete user | Command | MySQL Master |
| Get playlists | Query | Cache → MySQL Read |
| Search tracks | Query | Elasticsearch |
| Get user profile | Query | Redis → MySQL |

---

## 5. SPA Router (ADR-083)

### 5.1 Route Tanımları

```
GET  /                          → Landing page
GET  /music                     → Music panel
GET  /music/library             → Library
GET  /music/albums              → Albums
GET  /music/artists             → Artists
GET  /music/playlists           → Playlists
GET  /home                      → Home panel
GET  /car                       → Car panel
GET  /studio                    → Studio panel
GET  /admin                     → Admin panel
GET  /download                  → Download panel
GET  /pro                       → Pro panel
GET  /media                     → Media panel
GET  /auth/login                → Auth panel
```

### 5.2 SPA Router Implementasyonu

```php
// PageRouter.php
class PageRouter {
    private array $routes = [];

    public function addRoute(string $path, string $handler): void {
        $this->routes[$path] = $handler;
    }

    public function dispatch(string $path): ResponseInterface {
        $handler = $this->routes[$path] ?? null;
        if (!$handler) {
            return new Response(404);
        }
        return $handler->handle($request);
    }
}
```

---

## 6. OpenAPI (API-First)

### 6.1 Sözleşme Akışı

```
OpenAPI Spec → DTO → Contract → Validation → Use Case → Kod

Kod hiçbir zaman sözleşmeden önce yazılmaz.
```

---

## 7. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-083 | SPA Router Architecture |
| ADR-084 | API Gateway Architecture |
| ADR-086 | Event Driven Architecture |

---

*K9 API & Routing Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-24*
*Mode: Red Team · Human Mode · Truth Mode*

---

## Alt Katman Şeması (K9.a.b.c)

> **Revizyon (2026-09-24):** Bu bölüm 3 turlu agent tartışması sonucu eklenmiştir. §1–§7 (mevcut içerik) silinmemiştir. Adlandırma: adlandirma-kurali.md (K{n} → K{n}.a → K{n}.a.b). Event Bus bilinçli olarak K9.9'dadır (plan §2.1 L9.9 ile aynı sıra).

### 1. Kaynak Tablosu

| # | Kaynak | Kullanım |
|---|--------|----------|
| 1 | .ai/CLAUDE.md §5 (K9 satırı: Gateway, BFF×6, CQRS, Event Bus, SPA Router, OpenAPI · 30 bileşen · sınır: API sözleşmesi ihlal edilemez) | Düzey-2 kapsamı ve sözleşme sınırı (düzenleyici kaynak) |
| 2 | .ai/CLAUDE.md §5 (K8, K10, K12 satırları) | Yön: K10 yalnızca K9 üzerinden · K8'e doğrudan çağrı yasak (Event Bus) · K12 yalnızca K8'den okur |
| 3 | .ai/architecture/frontend-restructuring-plan.md §2.1 L9.1–L9.10 (10 satır) | Düzey-2 eşleme kanıtı (gateway + 6 BFF + CQRS + Event Bus + SPA Router) |
| 4 | k9-api-routing/README.md §2–§7 | Gateway yapı/kuralları, BFF 6 satır, CQRS akış+6 operation, SPA Router 14 route + PageRouter, OpenAPI akış, ADR-083/084/086 |
| 5 | k9-api-routing/index.md (Bileşenler 11 MD · Runtime Stack · Request Lifecycle 10 adım · performans · bağımlılıklar · config yaml · Durum checklist) | K9.5–K9.8 kanıtı + kök kanıtlar + çelişki C1–C5 |
| 6 | .ai/.decisions/index.md (ADR-083 SPA Router · ADR-084 API Gateway · ADR-086 Event Driven satırları) | Karar adları |
| 7 | k9-api-routing/*.md (disk glob: 14 dosya) | Düzey-2/3 disk kanıtları: 11 bileşen MD + README + index + CLAUDE |
| 8 | .ai/architecture/adlandirma-kurali.md | Şema adlandırma kuralları #1–#5 |

### 2. Şema Kuralları

| Kural | Uygulama |
|-------|----------|
| Kök | K9 (plan §2.1 L9 kökü) |
| Düzey-2 | K9.a — küçük harf-tire (api-gateway, bff, … event-bus) |
| Düzey-3 | K9.a.b — yalnızca kanıt varsa (disk MD / README bloğu / index satırı / plan satırı) |
| Düzey-4 | K9.a.b.c — bu katmanda YOK (tek gerçek L4 = K11.1.4.13) |
| .0. yasak | Hiçbir düğümde kullanılmadı |
| K numarası | Belgede her düğüm K9 önekiyle anıldı |

### 3. Düzey-2 Tablosu (K9.a — 9 düğüm)

| # | Düğüm | Görev (kısa) | plan §2.1 | Disk MD |
|---|-------|--------------|:---------:|---------|
| K9.1 | api-gateway | Tek giriş noktası, routing/auth/rate-limit/validation/logging/correlation | L9.1 | api-gateway.md |
| K9.2 | bff | İstemciye özel 6 BFF (SPA, Mobile, Embedded, Desktop, Admin, Car) | L9.2–L9.7 | bff-pattern.md |
| K9.3 | cqrs | Command/Query ayrımı, MySQL Master + Read Model/Cache | L9.8 | cqrs-pattern.md |
| K9.4 | spa-router | 14 panel route + PageRouter dispatch (PHP+JS hibrit) | L9.10 | spa-router.md |
| K9.5 | openapi | API-First sözleşme akışı (spec → DTO → contract → kod) | YOK (index Bileşenler + README §6) | openapi-spec.md |
| K9.6 | versioning | API sürümleme stratejisi + merkezi sürüm yönetimi | YOK (index) | versioning-strategy.md |
| K9.7 | request-throttling | Throttling + öncelik kuyrukları (token bucket) | YOK (index) | request-throttling.md |
| K9.8 | response-caching | ETag + Redis/CDN edge cache | YOK (index) | response-caching.md |
| K9.9 | event-bus | PSR-14 Event Bus — servisler arası tek iletişim kanalı | L9.9 | event-bus.md |

**Sayı notu:** CLAUDE §5 kapsamı 6 BFF'i ayrı sayar (BFF×6); bu şemada 6 BFF tek düğümün çocuklarıdır (K9.2.1–K9.2.6) — düzey-2 = 9 hedefi bu yüzden tutturuldu ( dürüst bir birleştirme, C2'ye bak).

### 4. Düzey-3 Düğümleri (K9.a.b — 34 düğüm)

#### K9.1 api-gateway — 8 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K9.1.1 routing | Request routing, route matching (path/header/method) | README §2.1 · index Request Lifecycle #5 |
| K9.1.2 authentication | JWT + Session zorunlu | README §2.2 · CLAUDE §6 (auth zinciri) |
| K9.1.3 rate-limiting | 60 req/60s | README §2.2 · ADR-013 · CLAUDE §6 #3 |
| K9.1.4 validation | Request/DTO validasyonu | README §2.2 · index Lifecycle #4 |
| K9.1.5 logging | Gateway loglama | README §2.1 |
| K9.1.6 correlation-id | Her isteğe benzersiz ID | README §2.1 · §2.2 |
| K9.1.7 graphql | GraphQL router katmanı (opsiyonel) | index Genel Bakış · Router bloğu · graphql-layer.md |
| K9.1.8 grpc | gRPC router/protokol | index Genel Bakış · Router bloğu · grpc-internal.md |

#### K9.2 bff — 6 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K9.2.1 spa-bff | SPA istemci — tam veri yanıtı | README §3 · plan L9.2 |
| K9.2.2 mobile-bff | Mobile — minimal yanıt | README §3 · plan L9.3 |
| K9.2.3 embedded-bff | Embedded (RPi5) — ultra-minimal, gzip | README §3 · plan L9.4 |
| K9.2.4 desktop-bff | Desktop — orta boy | README §3 · plan L9.5 |
| K9.2.5 admin-bff | Admin — full + audit | README §3 · plan L9.6 |
| K9.2.6 car-bff | Car — touch-optimized | README §3 · plan L9.7 |

#### K9.3 cqrs — 4 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K9.3.1 command-yazma | Command → Use Case → Repository → MySQL Master (create playlist, update track, delete user) | README §4 · §4.1 |
| K9.3.2 query-okuma | Query → Read Model → Cache → Response (get playlists, get user profile: Redis → MySQL) | README §4 · §4.1 |
| K9.3.3 read-model-cache | Cache → MySQL Read zinciri | README §4 · §4.1 |
| K9.3.4 elasticsearch-arama | Search tracks → Elasticsearch | README §4.1 |

#### K9.4 spa-router — 3 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K9.4.1 route-tablosu | 14 route: /, /music (+library/albums/artists/playlists), /home, /car, /studio, /admin, /download, /pro, /media, /auth/login | README §5.1 |
| K9.4.2 page-router | PageRouter: addRoute + dispatch (PHP sınıfı) | README §5.2 |
| K9.4.3 not-found-404 | Bilinmeyen path → Response(404) | README §5.2 |

#### K9.5 openapi — 3 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K9.5.1 sozlesme-akisi | OpenAPI Spec → DTO → Contract → Validation → Use Case → Kod | README §6.1 |
| K9.5.2 dto-validasyon | Sözleşme temelli DTO doğrulama | README §6.1 · index Lifecycle #4 |
| K9.5.3 spec-auto-uretim | OpenAPI spec auto-generation (durum: açık madde) | index Durum checklist |

#### K9.6 versioning — 2 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K9.6.1 surumleme-stratejisi | API versioning stratejisi | index Bileşenler (versioning-strategy.md) |
| K9.6.2 merkezi-surum-yonetimi | Versioning'in gateway'de merkezi yönetimi | index Genel Bakış |

#### K9.7 request-throttling — 2 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K9.7.1 token-bucket | Token Bucket (Redis-backed); default_rps 100, burst 50 (config — C5'ye bak) | index Runtime Stack · config yaml |
| K9.7.2 oncelik-kuyruklari | Priority queues | index Bileşenler (request-throttling.md) |

#### K9.8 response-caching — 3 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K9.8.1 etag | ETags ile koşullu GET | index Bileşenler (response-caching.md) |
| K9.8.2 redis-cdn | Redis Cluster + CDN Edge Cache | index Runtime Stack · config yaml (default_ttl 300) |
| K9.8.3 cache-store | Yanıtın cache edilmesi (lifecycle adım 9) | index Request Lifecycle #9 |

#### K9.9 event-bus — 3 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K9.9.1 psr14-sozlesme | PSR-14 Event Dispatcher sözleşmesi | plan L9.9 · k8-servis/README.md §3 (ADR-086) |
| K9.9.2 pub-sub | Message broker pub/sub modeli | index Bileşenler (event-bus.md) |
| K9.9.3 broker-tasiyici | Apache Kafka / RabbitMQ taşıyıcı (altyapı — C3) | index Runtime Stack · Dış Servisler |

### 5. Çelişki Kayıt Defteri

| # | Çelişki | Kazanan | Gerekçe |
|---|---------|---------|---------|
| C1 | index diyagramı: "K10 SERVİS LAYER" içinde K4 AI/K5 Ses/K6 Playlist/K7 User kutuları + "K11-K20 istemciler" ↔ CLAUDE §5: K10 = Uygulama (10 panel), servisler = K8, K7 = Middleware | CLAUDE §5 | index.md katman numaraları kaymış (SSOT §2.1); diyagram K9 katmanı dışındaki numaralarda yanlıştır |
| C2 | index BFF Layer = Mobile/Web/Desktop (3) ↔ README §3 + plan L9.2–L9.7 = 6 BFF ↔ CLAUDE §5 = BFF×6 | 6 BFF | Çoğunluk + CLAUDE; şemada K9.2.1–K9.2.6 |
| C3 | plan/K8 = Event Bus (PSR-14) ↔ index = Apache Kafka / RabbitMQ | İki katman — birlikte okunur | PSR-14 = dispatcher sözleşmesi (K9.9.1), Kafka/RabbitMQ = taşıyıcı (K9.9.3); çelişki değil, not edildi |
| C4 | README §2.2 = JWT + Session ↔ index Runtime = OAuth 2.0 + JWT + API Key | README + CLAUDE §6 | Auth zinciri (session/CSRF) CLAUDE §6'da kanıtlı; OAuth2/APIKey kanıtlanmadı → reddedildi, açık madde |
| C5 | Rate limit: README §2.2 + CLAUDE §6 #3 + ADR-013 = 60 req/60s ↔ index config = default_rps 100, burst 50 | 60 req/60s | Frozen ADR-013 + CLAUDE kazanır; index config satırı düzeltilmeli |

### 6. Matrisler

**BFF matrisi (README §3 · plan L9.2–L9.7):** SPA=tam veri · Mobile=minimal · Embedded(RPi5)=ultra-minimal+gzip · Desktop=orta boy · Admin=full+audit · Car=touch-optimized (6/6 plan satırıyla eşleşiyor).

**CQRS operation matrisi (README §4.1):** Create playlist / Update track / Delete user = Command → MySQL Master · Get playlists / Get user profile = Query → Cache/Redis → MySQL Read · Search tracks = Query → Elasticsearch (3 write + 3 read).

**Route matrisi (README §5.1):** 14 route — paneller (landing, music +4 alt, home, car, studio, admin, download, pro, media, auth/login) K10 panelleriyle birebir eşleşir (K10 ≠ K9 kapsamı; router K9'dadır, panel K10'dadır).

**Performans hedefleri (index §Performance Hedefleri):**

| Metrik | Hedef |
|--------|-------|
| P50 / P99 latency (gateway overhead) | < 5ms / < 20ms |
| Throughput | > 100K req/s per instance |
| Availability | 99.99% |
| Error Rate | < 0.1% |

**Durum checklist → düğüm eşlemesi (index §Durum):**

| Madde | Durum | Düğüm |
|-------|:-----:|-------|
| API Gateway temel yapı | ✅ | K9.1 |
| Rate limiting (token bucket) | ✅ | K9.1.3 · K9.7.1 |
| JWT authentication middleware | ✅ | K9.1.2 |
| BFF pattern implementasyonu | ⏳ | K9.2 |
| CQRS read/write separation | ⏳ | K9.3 |
| Event bus entegrasyonu | ⏳ | K9.9 |
| GraphQL layer | ⏳ | K9.1.7 |
| gRPC internal communication | ⏳ | K9.1.8 |
| Response caching | ⏳ | K9.8 |
| OpenAPI spec auto-generation | ⏳ | K9.5.3 |

**İlgili ADR'ler (README §7 · .decisions/index.md):** ADR-083 SPA Router Architecture (→ K9.4) · ADR-084 API Gateway Architecture (→ K9.1) · ADR-086 Event Driven Architecture (→ K9.9).

### 7. Katman Sınırları (K10 → K9 → K7/K8 · K9.9)

| Sınır | Kural | Kanıt |
|-------|-------|-------|
| K10 → K9 | K10 yalnızca K9 API üzerinden iletişim kurabilir | CLAUDE §5 K10 |
| K9 → K7 | Client → Gateway → Middleware Pipeline → Use Case (K9 gateway K7 pipeline'ını çağırır) | README §2.1 · k7-middleware/README.md §7 |
| K9 → K8 | Use case = K8 servisleri; servisler arası doğrudan çağrı yok → K9.9 Event Bus | CLAUDE §5 K8/K9 · plan L9.9 |
| K9 sözleşmesi | API sözleşmesi (OpenAPI) ihlal edilemez | CLAUDE §5 K9 sınırı |
| K12 → K9 | K12 yalnızca K8 servislerinden okur; K9'u doğrudan gözlemez (monitoring sinyali index Monitoring satırından gelir) | CLAUDE §5 K12 |

### 8. Kök Kanıtlar (tek düğüme ait olmayan — düzey-2 yapılamaz)

| Kanıt bloğu (index.md) | Neden kök? |
|------------------------|------------|
| Runtime Stack (gateway/protokol/auth/rate-limit/cache/schema/monitoring/event bus) | Tüm K9.1–K9.9'un ortak yığını |
| Request Lifecycle (10 adım: TLS → rate limit → JWT → transform → route → BFF → LB → response → cache → client) | Gateway + BFF + cache + throttling ortak akışı |
| Performance Hedefleri tablosu | Katman geneli SLA |
| Bağımlılıklar (üst: K1/K0 · alt: K10, K11-K20 · dış: Redis, Kafka/RabbitMQ, Consul/etcd, Prometheus/Grafana) | Katman dışı ilişkiler |
| k9-gateway-config.yaml (listen 8443, tls, rate_limit, auth, cache, monitoring) | Çok düğümlü ortak config |
| Durum: Implementasyon checklist (10 madde) | Katman yaşam döngüsü |

### 9. Düzey-4 Durumu

**Bu katmanda düzey-4 düğüm YOKTUR (0 adet).** Doğrulama: şemada benimsenen tek gerçek düzey-4 düğüm K11.1.4.13'tür (plan §2.2, c-player.css). K9 için düzey-4 talebine ek kanıt (disk MD veya plan satırı) zorunludur; uydurma düğüm eklenmez (Guardrail #3).

### 10. Sayım Özeti (K9)

| Seviye | Onaylı hedef (X/Y/Z = T) | Gerçek (2026-09-24 sayımı) | Durum |
|--------|:-------------------------:|:---------------------------:|-------|
| Düzey-2 (K9.a) | 9 | 9 (6 BFF tek düğümde birleşik — C2) | ✅ |
| Düzey-3 (K9.a.b) | 4 * | 34 | * tanım belirsiz — gerçek şemanın kendisinden sayıldı |
| Düzey-4 (K9.a.b.c) | 90 * | 0 | * tanım belirsiz — kanıt yok, uydurulmadı |
| Toplam T | 126 (X·Y+Z=9·4+90) | — | * bkz. not |

> **Not (Truth Mode):** Y ve Z'nin tanımı paylaşılmadı; X·Y+Z denklemi K9'da tutuyor (9·4+90=126). "Her dosya ≥500 satır" ile "7 dosya toplamı 1.235" birlikte sağlanamaz (7×500=3.500). Öncelik: (1) gerçek kanıt, (2) düzey-2 = onaylı X, (3) dosya başı ≥500 satır. Sapmalar raporlanmıştır.

### 11. Düzey-2 → Kaynak Çapraz Referans Matrisi

| Düğüm | plan §2.1 | Disk MD | CLAUDE §5 karşılığı | index Durum |
|-------|:---------:|---------|:-------------------:|:-----------:|
| K9.1 api-gateway | L9.1 | api-gateway.md | Gateway ✔ | ✅ |
| K9.2 bff | L9.2–L9.7 | bff-pattern.md | BFF×6 ✔ | ⏳ |
| K9.3 cqrs | L9.8 | cqrs-pattern.md | CQRS ✔ | ⏳ |
| K9.4 spa-router | L9.10 | spa-router.md | SPA Router ✔ | — (ADR-083) |
| K9.5 openapi | YOK | openapi-spec.md | OpenAPI ✔ | ⏳ |
| K9.6 versioning | YOK | versioning-strategy.md | (index Genel Bakış) | — |
| K9.7 request-throttling | YOK | request-throttling.md | (Gateway rate limiting altı) | ✅ (rate limit) |
| K9.8 response-caching | YOK | response-caching.md | — | ⏳ |
| K9.9 event-bus | L9.9 | event-bus.md | Event Bus ✔ | ⏳ |

**Doğrulama:** 5/9 düğüm plan L9.x satırıyla eşleşiyor (gateway, bff×6, cqrs, event-bus, spa-router); 4/9 (openapi, versioning, request-throttling, response-caching) plan'da satırı YOK — kanıtları index.md Bileşenler tablosu + README §6 + disk MD'lerdir (uydurulmadı, işaretlendi). 9/9 düğümün disk MD'si vardır.

### 12. Şema Kuralı Uyum Matrisi (adlandirma-kurali.md #1–#5)

| Kural | K9 Uygulaması | Durum |
|-------|---------------|-------|
| #1 Kök K{n} | K9 (plan §2.1 L9 kökü) | ✅ |
| #2 Düzey-2 K{n}.a | K9.1–K9.9 (küçük harf-tire) | ✅ |
| #3 Düzey-3 K{n}.a.b | K9.a.b — 34 düğüm (K9.2.1–K9.2.6 = 6 BFF) | ✅ |
| #4 Düzey-4 K{n}.a.b.c | K9'da 0 (yalnız gerçek L4 = K11.1.4.13) | ✅ |
| #5 ".0." yasak | Hiçbir düğümde ".0." yok | ✅ |

### 13. Kapsam Dışı ve Bilinen Boşluklar

| # | Boşluk | Durum | Sonraki Eylem |
|---|--------|-------|---------------|
| 1 | index diyagram K-numaraları (C1) | Açık | index.md revizyonu: K10/K11-K20/K4-K7 etiketleri CLAUDE §5 ile değişmeli |
| 2 | OAuth 2.0 + API Key (C4) | Reddedildi — kanıt yok | Varsa ADR ile kanıtlanmalı; yoksa index Runtime satırı düzeltilmeli |
| 3 | index config rate 100rps/burst50 (C5) | Reddedildi — ADR-013 60/60 | config yaml satırı düzeltilmeli |
| 4 | Kong/Traefik/Rust-Go gateway seçimi (index Runtime) | Açık — alternatif belirsiz | ADR-084 kapsamı teyit edilmeli (README custom PageRouter gösteriyor) |
| 5 | K9.5–K9.8 plan §2.1 L9'de yok | Kabul edildi — işaretli | plan revizyonu L9 satırları eklenirse §11 güncellenir |

### 14. Revizyon ve Denetim Notu

| Öğe | Değer |
|-----|-------|
| Bu revizyon | v1.1.0 — 2026-09-24 (frontmatter updated + source; footer 2026-09-24) |
| Önceki durum | v1.0.0 — 163 satır, §1–§7 (içerik korundu, silme yok) |
| Eklenen H2 | ## Alt Katman Şeması (K9.a.b.c) · ## Kanıt Kataloğu |
| Denetim izi | CLAUDE.md §5 (K8/K9/K10/K12) · plan §2.1 L9.1–L9.10 · k9-api-routing/ (14 dosya) · .decisions/index.md (ADR-083/084/086) |
| Sonraki tetik | BFF sayısı, gateway protokolü veya Event Bus sözleşmesi değişirse §3/§5/§10 + Kanıt Kataloğu yeniden sayılır |

### 15. Request Lifecycle → Düğüm Eşlemesi (index §Request Lifecycle)

| Adım | Kapsam | Düğüm |
|:----:|--------|-------|
| 1 | TLS Termination | K9.1 (gateway) |
| 2 | Rate Limit Check (Redis) | K9.1.3 · K9.7 |
| 3 | JWT/API Key Validation | K9.1.2 (C4: API Key reddedildi) |
| 4 | Request Transformation (header injection, body parsing) | K9.1.4 |
| 5 | Route Matching (path/header/method) | K9.1.1 |
| 6 | BFF Transformation (optional) | K9.2 |
| 7 | Load Balancer → Upstream Service | K9.1 (upstream = K8 — Event Bus K9.9 üzerinden) |
| 8 | Response Transformation | K9.2 (istemciye özel) |
| 9 | Cache Store (if cacheable) | K9.8 |
| 10 | Client Response | K9.1 (tek çıkış noktası) |

### 16. Bağımlılık ve Dış Servis Matrisi (index §Bağımlılıklar + §Konfigürasyon)

| Yön | Bağımlılık | Kullanım | Düğüm |
|-----|-----------|----------|-------|
| Üst (K9'in kullandığı) | K1 Hardware | TLS termination donanım hızlandırma | K9.1 (listen 8443, tls) |
| Üst (K9'in kullandığı) | K0 OS | Network stack, epoll/io_uring | K9.1 |
| Alt (K9'i kullanan) | K10 Uygulama | 10 panel yalnızca K9 API üzerinden | K9.1–K9.4 |
| Alt (K9'i kullanan) | K11 UX | Sadece K10 tarafından tetiklenir → dolaylı | K9.4 route'ları |
| Dış | Redis | Rate limiting, caching, session | K9.1.3 · K9.7.1 · K9.8.2 |
| Dış | Apache Kafka / RabbitMQ | Event bus taşıyıcısı (C3: PSR-14 sözleşmesi ayrı) | K9.9.3 |
| Dış | Consul / etcd | Service discovery | K9.1.1 (upstream keşfi) |
| Dış | Prometheus / Grafana / Jaeger | Monitoring + tracing (port 9090, Jaeger 14268) | kök: index Monitoring |

**Doğrulama:** Üst/alt yönler CLAUDE §5 ile çelişmez (K10→K9 ✔, K11→K12 bağımlılığı YOK ✔ — K11 bu tabloda yalnız K10 üzerinden görünür). Dış servisler katman değildir, bağımlılık satırıdır.

### 17. Çelişki Eylem Listesi (index.md Revizyon Kuyruğu)

| # | Eylem | Hedef dosya | Öncelik |
|---|-------|-------------|:-------:|
| 1 | Diyagram K-numaralarını CLAUDE §5'e göre düzelt (K10 = Uygulama, servis = K8, K7 = Middleware) | k9-api-routing/index.md | P0 (C1) |
| 2 | Rate limit config satırını 60 req/60s yap (ADR-013) | index.md k9-gateway-config.yaml bloğu | P1 (C5) |
| 3 | Auth satırından OAuth2/API Key'i kaldır veya ADR ile kanıtla | index.md Runtime Stack | P1 (C4) |
| 4 | BFF kapsamını 6'ya tamamla (index 3 → 6) | index.md mimari diyagram + BFF Layer | P2 (C2) |
| 5 | Event Bus notu ekle: PSR-14 = sözleşme, Kafka/RabbitMQ = taşıyıcı | index.md Runtime Stack | P3 (C3) |

> Bu listeler bu belgenin eylem çıktısıdır; uygulama yetkisi ilgili katman bakımındadır (Backend Architect).

---

## Kanıt Kataloğu

> Bu katmanın diskteki TÜM .md dosyaları (glob: 14 adet) ve hangi sayımın kanıtını taşıdıkları. Dosya başına 2 satır: açıklama + desteklediği sayaç.

- **README.md** (CoreMusic — K9 API & Routing Layer) — §2 Gateway yapı+kuralları, §3 BFF 6 satır, §4/§4.1 CQRS akış+operation, §5 SPA Router 14 route + PageRouter kodu, §6 OpenAPI akış, §7 ADR-083/084/086.
  → Destek: K9.1–K9.4 düzey-2, K9.1.1–K9.4.3 düzey-3 (21), C2/C4/C5, route/BFF/CQRS matrisleri.
- **index.md** (K9 API & Routing - Genel Bakış) — Bileşenler 11 MD, Runtime Stack, Request Lifecycle 10 adım, performans, bağımlılıklar, k9-gateway-config.yaml, Durum checklist 10 madde.
  → Destek: K9.5–K9.9 düzey-2/3 (13), kök kanıtlar §8 (6 blok), C1–C5, §15 eşleme, durum matrisi.
- **CLAUDE.md** (CoreMusic — K9 API & Routing CLAUDE.md) — katmana özgü CLAUDE kestirmesi/şablon girişi.
  → Destek: kanıt kaynakları tablosu (kural #6), katalog bütünlüğü (14 dosya sayımı).
- **api-gateway.md** (API Gateway) — K9.1'in disk kanıtı.
  → Destek: K9.1, K9.1.1–K9.1.6 (düzey-2/3: 7).
- **bff-pattern.md** (Backend for Frontend Pattern) — K9.2'nin disk kanıtı.
  → Destek: K9.2, K9.2.1–K9.2.6 (7).
- **cqrs-pattern.md** (CQRS Pattern) — K9.3'ün disk kanıtı.
  → Destek: K9.3, K9.3.1–K9.3.4 (5).
- **event-bus.md** (Event Bus) — K9.9'un disk kanıtı.
  → Destek: K9.9, K9.9.1–K9.9.3 (4).
- **spa-router.md** (SPA Router) — K9.4'ün disk kanıtı (ADR-083).
  → Destek: K9.4, K9.4.1–K9.4.3 (4).
- **openapi-spec.md** (OpenAPI 3.0 Specification) — K9.5'in disk kanıtı.
  → Destek: K9.5, K9.5.1–K9.5.3 (4).
- **versioning-strategy.md** (API Versioning Stratejisi) — K9.6'nın disk kanıtı.
  → Destek: K9.6, K9.6.1–K9.6.2 (3).
- **graphql-layer.md** (GraphQL Optional Layer) — K9.1.7'nin disk kanıtı.
  → Destek: K9.1.7 (1) — düzey-4 DEĞİL, düzey-3 kanıtı.
- **grpc-internal.md** (gRPC Internal Communication) — K9.1.8'in disk kanıtı.
  → Destek: K9.1.8 (1) — düzey-4 DEĞİL, düzey-3 kanıtı.
- **request-throttling.md** (Request Throttling) — K9.7'nin disk kanıtı.
  → Destek: K9.7, K9.7.1–K9.7.2 (3).
- **response-caching.md** (Response Caching) — K9.8'in disk kanıtı.
  → Destek: K9.8, K9.8.1–K9.8.3 (4).

**Dürüstlük kaydı:** K9'da disk MD'si OLMAYAN düzey-2 düğüm YOKTUR (9/9). graphql-layer.md ve grpc-internal.md dosyaları kök düzey-2 DEĞİLDİR — K9.1'in çocuklarıdır (index Genel Bakış: "HTTP/gRPC/GraphQL isteklerini alır"). K9.5–K9.8 plan L9'da satır taşımaz; kanıtları index Bileşenler + disk MD'dir.

**Harici kanıt kaynakları (katalog kapsamı):**

| Kaynak | Kullanım |
|--------|----------|
| .ai/CLAUDE.md §5 (K9, K8, K10, K12 satırları) | Kapsam (BFF×6,30 bileşen), sınır (sözleşme, K10→K9) kanıtları |
| .ai/architecture/frontend-restructuring-plan.md §2.1 L9.1–L9.10 | Düzey-2 eşleme (10 satır) |
| .ai/.decisions/index.md ADR-083, ADR-084, ADR-086 | Karar adları |
| .ai/architecture/adlandirma-kurali.md | Şema adlandırma kuralları #1–#5 |
| disk glob (k9-api-routing/*.md) | 14 dosya: 9 düzey-2 + 2 çocuk (graphql/grpc) + 3 bağlam |

*K9 Alt Katman Şeması + Kanıt Kataloğu v1.1.0 — 2026-09-24 · kaynak: 3 turlu agent tartışması*
