---
node_id: K012-services/services-evrensel-dil.md
node_type: subdomain
status: draft
parent_id: K012-services
children: []
depends_on:
  - K003-audio
  - K004-dsp
  - K005-firmware
  - K007-data
  - K008-domain
  - K009-security
  - K010-network
  - K015-AI
evidence:
  - ".ai/architecture/K012-services/services-master.md §2 (Glossary, 41 terim) — okundu 2026-10-18"
  - "api/core/src/Core/ServiceRegistry.php L19-20 (ServiceRegistry) — okundu 2026-10-18"
  - "api/core/src/Core/ServiceDefinition.php L19-32 (Contract + DomainError), L79 (version=1.0.0) — okundu 2026-10-18"
  - "api/core/src/Core/ContractGenerator.php L128-148 (API-VERSION yanıt başlığı, rate-limit) — okundu 2026-10-18"
  - "assets/js/api/ServiceRegistry.js L37 (register), L73-88 (proxy), L110-136 (WS), L145-183 (retry) — okundu 2026-10-18"
validation:
  - "2026-10-18 — 32 terim EN/TR + bağlam; kod örnekleri diskte (services-kod-ornekleri.md §2-§15, 15 blok)"
open_questions:
  - "Q1: ai-service K006 (Yapay Zeka) mü K015 (AI) mı ait? Master Q1 — KURULUM SONRASI karar (K006 + K015)."
  - "Q2: playback ownership (K002 application vs K012 services) — Q2."
  - "Q3: ses hizmeti sınırı (audio-service vs K003-audio) — Q3 (Q2'den sonra)."
  - "Q5: background-jobs motoru (Symfony Messenger vs RabbitMQ) — Q5."
  - "Q6: webhooks için gateway (Kong vs kendi çözüm) — Q6 — K010 + K012."
  - "Q9: servisler arası taşıma (REST yok) — UNKNOWN (event bus / RPC / message broker)."
  - "Q10: kontrat kayıt defteri deposu — UNKNOWN (bkz. bilesenler.md §3.2)."
  - "⚠️ VERIFICATION REQUIRED: timeout=5 birimi (sn/ms) — ServiceDefinition.php:79'da birim YOK (§6.1 karar)."
  - "⚠️ VERIFICATION REQUIRED: 'audio-service' adı kodda YOK (fictional)."
  - "⚠️ VERIFICATION REQUIRED: 'ServiceMesh (mTLS)' — kod YOK."
  - "⚠️ VERIFICATION REQUIRED: circuit breaker / bulkhead / saga / dead-letter / OpenTelemetry — kodda YOK."
  - "⚠️ VERIFICATION REQUIRED: healthService K010; notification-service K008."
group: mimari
---

# K012 SERVICES — Evrensel Dil (Sözlük)

> **Kaynak**: services-master.md §2 (41 terim) — bu dosya 32 temel terimi EN/TR + bağlam + kod referansı ile sabitler.
> Domain dilinin (K008-domain / ubiquitous language) hizmet katmanına yansıması.
> Kod örnekleri: `services-kod-ornekleri.md` §2-§15 (15 blok).

## §1 Kapsam ve Kullanım

1. Bu sözlük K012 (Hizmet Yönetimi)'nin resmi terimlerini tanımlar.
2. Terimler EN (kod/ADR adı) + TR (görsel metin) karşılıklıdır.
3. Kodda EN, arayüzde TR kullanılır (bkz. K008-domain evrensel-dil.md §1 kuralı).
4. **Yeni terim ekleme**: services-master.md §2 + bu dosya birlikte güncellenir (kaynak: master).

## §2 Çekirdek Terimler (20)

| # | EN Terim | TR Karşılık | Bağlam / Kod Referansı |
|---|---|---|---|
| 1 | Service | Hizmet | `ServiceDefinition.php:19` (Contract: health/version/metadata) |
| 2 | Service Registry | Hizmet Kayıt Defteri | `ServiceRegistry.php:19` (singleton, L97 register) |
| 3 | Service ID | Hizmet Kimliği | `ServiceRegistry.php:38` (string); `ServiceDefinition.php:23` (id) |
| 4 | Service Definition | Hizmet Tanımı | `ServiceDefinition.php:19-32` (Contract + metadata) |
| 5 | Service Contract | Hizmet Sözleşmesi | `ContractGenerator.php` (OpenAPI 3.1, ADR-100) |
| 6 | API Version | API Sürümü | `ServiceDefinition.php:26` (1.0.0); yanıt: `API-VERSION` başlığı (`ContractGenerator.php:142-148`) |
| 7 | Service Health | Hizmet Sağlığı | `ServiceHealth.php:34` (ok/breakingChanges/inventory/dependencies/lastChecked) |
| 8 | Health Check | Sağlık Denetimi | `ServiceHealth.php` + registry `health()` (`ServiceRegistry.php:26`) |
| 9 | Breaking Change | Yıkıcı Değişiklik | `ServiceHealth.php:34` (sayı); ADR-013 (dokümantasyon) |
| 10 | Version Conflict | Sürüm Çakışması | `VersionResolver.php:57` (conflict) |
| 11 | Version Strategy | Sürümleme Stratejisi | `VersionResolver.php:57` (strategies) |
| 12 | Registry Health | Kayıt Defteri Sağlığı | `VersionRegistry.php:96` (ok/warnings) |
| 13 | Service Consumer | Hizmet Tüketicisi | Frontend `ServiceRegistry.js` (L37 register) |
| 14 | Service Provider | Hizmet Sağlayıcı | Backend servisler (`Api/`, `Infrastructure/`, `Core/`) |
| 15 | Rate Limit | Hız Sınırı | `RateLimitMiddleware.php:34` (60 istek/60 sn) |
| 16 | Throttle | Yavaşlatma | Rate limit middleware (429 yanıtı) |
| 17 | Service Locator | Hizmet Konumlayıcı | ADR-039 / R-006 (kod YOK — anti-pattern veto) |
| 18 | Domain Event | Alan Olayı | ADR-086; event-contracts.md (konuşma biçimi UNKNOWN) |
| 19 | Event Bus | Olay Veriyolu | ADR-085; `event-bus.md` (motor belirsiz) |
| 20 | Dead Letter | Ölü Mektup Kuyruğu | ADR-086; kod YOK |

## §3 Entegrasyon Terimleri (12)

| # | EN Terim | TR Karşılık | Bağlam / Kaynak |
|---|---|---|---|
| 21 | Integration | Entegrasyon | K013-integraion (dizin adı — yazım hatası donduruldu) |
| 22 | Integration Hub | Entegrasyon Merkezi | `K013/integration-master.md` (tek merkez) |
| 23 | Integration Point | Entegrasyon Noktası | 11 bileşen (K013 kapsam.md §2) |
| 24 | Webhook | Web Kancası | K013 §14 (Stripe/GitHub); kuyruk motoru UNKNOWN |
| 25 | API Adapter | API Uyarlayıcı | K013 §16 (MusicBrainz/Jellyfin — 7/7 kod YOK) |
| 26 | Message Broker | Mesaj Aracısı | ADR-101 (Redis Streams); transport: UNKNOWN |
| 27 | Outbox | Çıkış Kutusu | microservices.io (K013 web-arastirma §5); ADR-084 (konuşma biçim) |
| 28 | Saga | Saga (Dağıtık İşlem) | microservices.io (saga pattern); kod YOK |
| 29 | Compensation | Tazmin (Geri Alma) | Saga compensating transaction; kod YOK |
| 30 | Idempotency | Teyekullanılamazlık | Stripe docs (K013 web-arastirma §6); kod YOK |
| 31 | Circuit Breaker | Devre Kesici | ADR-094 (konuşma biçimi); kod YOK |
| 32 | Bulkhead | Bölme | ADR-094 (konuşma biçimi); kod YOK |

## §4 İlişki Ağı (Özet)

```text
K012 (hizmet yönetimi)          K013 (entegrasyon)              K008 (domain)
─────────────────────          ──────────────────              ────────────
ServiceDefinition (1.0.0)  ←→  event-contracts.md (konuşma)  ←→  Domain Event (ADR-086)
ServiceHealth (health)     ←→  IntegrationHealth (hasta)     ←→  -
RateLimitMiddleware        ←→  MusicBrainz (1 sn/kullanıcı)  ←→  -
ContractGenerator (OpenAPI) ←→  API Adapter (Swagger)         ←→  -

K012 ←→ K015: ai-service sahipliği Q1 (KURULUM SONRASI)
K012 ←→ K010: healthService aidiyeti (UNKNOWN) + mTLS (kod YOK)
K012 ←→ K016: CircuitBreaker / dead-letter (konuşma biçimi; ADR-094)
```

## §5 Eşanlamlı / Kaçınılacak Terimler

| Yanlış / Eski | Doğru | Not |
|---|---|---|
| Service Locator (ADR-039) | Container (DI) | Locator kod YOK (R-006 veto) |
| `K019-testing` / `K018-devops` / `K017-observability` | `K18-testing` / `K17-devops` / `K016-observability` | 2026-10-16'tan beri adlar — yeni atıf yasak |
| audio-service | K003-audio (alan) | audio-service fictionel (Q3) |
| K020-licence / K020-comilance / K020-comere | K019-comilance / K020-discovery | Eski klasör adları — yeni atıf yasak |
| K0100-identitiy | K009-security | ⚠️ Eski ad (services-master.md §5.1'de duruyor — silinmedi) |

## §6 Bağlantılı Dosyalar

| Dosya | İçerik | Ad (değiştirilemez) |
|---|---|---|
| `.ai/architecture/K012-services/services-master.md` | §2 glossary (41 terim) — SSOT | services-master |
| `.ai/architecture/K012-services/services-kod-ornekleri.md` | Kod örnekleri (15 blok) | services-kod-ornekleri |
| `.ai/architecture/K008-domain/evrensel-dil.md` | Domain dili (ubiquitous language) | evrensel-dil |
| `.ai/architecture/K013-integraion/integraion-evrensel-dil.md` | Entegrasyon sözlüğü | integraion-evrensel-dil |
