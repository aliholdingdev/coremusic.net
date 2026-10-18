---
node_id: K013-integraion/integraion-kararlar.md
node_type: subdomain
status: draft
parent_id: K013-integraion
children: []
depends_on:
  - K007-data
  - K008-domain
  - K009-security
  - K010-network
  - K012-services
  - K015-AI
evidence:
  - ".ai/.decisions/index.md ADR-084 · ADR-086 · ADR-100 · ADR-101 · ADR-081 · ADR-050 · ADR-032 · ADR-020 · ADR-094 · ADR-095 · ADR-037 · ADR-103 (draft) — okundu 2026-10-18"
  - ".ai/architecture/K013-integraion/adr-integration.md 17 entegrasyon kararı — okundu 2026-10-18"
  - ".ai/architecture/K013-integraion/integration-master.md §5 (karar), §14 (webhook), §22-§23 (açık konular) — okundu 2026-10-18"
validation:
  - "2026-10-18 — 12 gerçek ADR + 3 açık konu; uydurma ADR numarası YOK; format Durum/Bağlam/Karar/Alternatifler/Sonuçlar"
open_questions:
  - "⚠️ ADR-103 (audio-streaming-connector kilitli) DRAFT (1/3: architect) — henüz kabul edilmedi."
  - "⚠️ ADR-081 (auth interceptor) DRAFT (1/3: backend-architect)."
  - "⚠️ Sync sahipliği (K013 vs K015) — karar YOK (§22.4)."
  - "⚠️ YouTube Music API doğrulanmadı (§22.5) — Invidious/Piped alternatifi."
  - "⚠️ Event-contracts 16 olay konuşa biçimi belirsiz (ADR-084 ile gerilim)."
  - "⚠️ DeadLetter/CircuitBreaker/Bulkhead — konuşma biçimi var; kod YOK (ADR-094)."
  - "⚠️ healthService aidiyeti (K010 vs K012) — §10.4 K010 diyor; KARAR YOK."
group: mimari
---

# K013 INTEGRAION — Kararlar

> **Kaynaklar**: `.ai/.decisions/index.md` (ADR numaraları — uydurma YOK) · `adr-integration.md` (17 entegrasyon kararı) · `integration-master.md` §5/§14/§22-§23.
> Format (zorunlu): Durum / Bağlam / Karar / Alternatifler / Sonuçlar.

## §1 ADR-084 — Domain Event Adlandırması (accepted)

- **Durum**: accepted (2026-10-18)
- **Bağlam**: 16 domain event'i (event-contracts.md) adlandırılıyor; adlar domain diline (K008) uymalı.
- **Karar**: `domain.event` adları (`entity.action.pastTense`) kullanılır — örn. `playlist.created`, `track.played`.
- **Alternatifler**: CamelCase (`PlaylistCreated`) — OK; snake_case (`playlist_created`) — OK (zorunlu değil); adlandırma yok — REDDEDİLDİ.
- **Sonuçlar**: Domain dili ile hizmet dili uyumlu. Event-contracts.md bu adları kullanır.

## §2 ADR-086 — Event-Driven Architecture (accepted)

- **Durum**: accepted (2026-10-18)
- **Bağlam**: Servisler arası gevşek bağ; senkron zincir kırılgan (K012 §5 kuralı).
- **Karar**: Servisler **event-driven**; senkron zincir yerine event yayını (fan-out). Event store/outbox konuşa biçimine (§4.3).
- **Alternatifler**: Senkron REST zincir — REDDEDİLDİ (kırılgan); RPC — REDDEDİLDİ.
- **Sonuçlar**: Gevşek bağ (K012 §5). **Kod YOK** (master §22.8).

## §3 ADR-100 — OpenAPI 3.1 Kontrat (accepted)

- **Durum**: accepted (2026-10-18)
- **Bağlam**: Entegrasyon adaptörlerinin (API-Adapter §16) kontratı açık olmalı.
- **Karar**: OpenAPI 3.1 (JSON Schema 2020-12); adapter Swagger'dan tüketir (master §16.6-§16.10).
- **Alternatifler**: Swagger 2.0 — REDDEDİLDİ (eski); GraphQL şeması — GraphQL proxy için §16.8.
- **Sonuçlar**: Kontrat odaklı adaptör (K012 §7). Kod YOK (§16 fictional).

## §4 ADR-101 — Event Transport (accepted)

- **Durum**: accepted (2026-10-18)
- **Bağlam**: 16 olayın taşınacağı altyapı belirsiz (Q9: "event bus / RPC / message broker — UNKNOWN").
- **Karar**: Event transport **Redis Streams** (ADR-101 başlığı "event transport"); olaylar Redis Stream'e yazılır.
- **Alternatifler**: RabbitMQ (master §6: "event-bus RABBITMQ mu?") — master'da soru; PostgreSQL outbox + CDC — ADR-086 konuşa biçimi.
- **Sonuçlar**: Transport belirlendi (Q9 kısmen). **Olay yayını + outbox konuşa biçimi var; kod YOK**.

## §5 ADR-081 — OAuth 2.1 / PKCE (accepted · draft notu §7)

- **Durum**: accepted (2026-10-18) — **⚠️ adr-integration.md'de 1/3 (backend-architect) olarak işaretli DRAFT olabilir — VERIFICATION REQUIRED.**
- **Bağlam**: Entegrasyonlar (Spotify/Apple/Google OAuth §10.1) güvenli yetkilendirme gerektirir.
- **Karar**: OAuth 2.1 + PKCE; refresh token rotasyonu; scope minimizasyonu (K009 §4).
- **Alternatifler**: OAuth 2.0 implicit — REDDEDİLDİ; API key — REDDEDİLDI (düşük güvenlik).
- **Sonuçlar**: K009 ile uyumlu. K013 yalnız OAuth'u tüketir (§10.1: "OAuth K009'da").

## §6 ADR-050 — JWT (HS256 dev · RS256 prod) (accepted)

- **Durum**: accepted (2026-10-18)
- **Bağlam**: Entegrasyon adaptörlerinin servis içi kimlik doğrulaması.
- **Karar**: JWT — HS256 dev/prod; RS256 yalnız gerekli yerde (K009 §6). Entegrasyonlar K009 JWT'sini tüketir.
- **Alternatifler**: Session cookie — REDDEDİLDİ (API); API key (düz) — REDDEDİLDİ.
- **Sonuçlar**: K009 uyumlu; adaptör JWT header doğrular.

## §7 ADR-032 — Refresh Token (accepted)

- **Durum**: accepted (2026-10-18)
- **Bağlam**: Entegrasyon oturumlarının (medya sunucuları §5.1-§5.3) dayanıklılığı.
- **Karar**: Refresh token (rotasyon + reuse detection — ADR-072).
- **Alternatifler**: Uzun ömürlü access token — REDDEDİLDİ (güvenlik); her istekte şifre — REDDEDİLDİ.
- **Sonuçlar**: K009 ile uyumlu.

## §8 ADR-020 — 401/403 (accepted)

- **Durum**: accepted (2026-10-18)
- **Bağlam**: Entegrasyon adaptörlerinin hata kodları tutarlı olmalı.
- **Karar**: `401 Unauthorized` (kimlik yok/hatalı) · `403 Forbidden` (yetki yok) — K008 §7 ile aynı.
- **Alternatifler**: Tek kod 401 — REDDEDİLDİ; 403'ü 401 ile birleştir — REDDEDİLDİ.
- **Sonuçlar**: Adaptör hata kodları domain ile uyumlu.

## §9 ADR-094 — Resilience4j — CircuitBreaker/RateLimiter (accepted)

- **Durum**: accepted (2026-10-18)
- **Bağlam**: Hasta entegrasyon (MusicBrainz/Jellyfin down §5.1-§5.3) ana sistemi çökertmemeli.
- **Karar**: **Resilience4j** desenleri — `CircuitBreaker` + `RateLimiter`; devrede `failureRate=50%, waitDuration=30s, slidingWindowSize=10`; entegrasyon adaptörleri (§16) bu desenlerle sarılır.
- **Alternatifler**: Polly — REDDEDİLDİ (JVM dışı); Hystrix — REDDEDİLDİ (deprecated); kod yok, sadece try-catch — REDDEDİLDİ (master §22.8: "konuşa biçimi var; kod YOK").
- **Sonuçlar**: **Desen konuşa biçiminde (ADR-094 başlığı); kod YOK** (fictional). DeadLetter/Outbox/Retry da konuşa biçiminde (§10).

## §10 ADR-095 — Bulkhead/Timeout/Retry (accepted)

- **Durum**: accepted (2026-10-18)
- **Bağlam**: ADR-094'ün tamamlayıcısı — entegrasyon izolasyonu + timeout + retry.
- **Karar**: `Bulkhead` (izole thread pool) + `Timeout` + `Retry` (exponential backoff, jitter).
- **Alternatifler**: Sınırsız eşzamanlılık — REDDEDİLDİ; sabit aralıklı retry — REDDEDİLDİ.
- **Sonuçlar**: K012 §7.4 (timeout) + §7.7 (retry) ile uyumlu. **Kod YOK**.

## §11 ADR-037 — Outbox + Debezium CDC (accepted)

- **Durum**: accepted (2026-10-18)
- **Bağlam**: 16 olayın (event-contracts.md) veritabanı değişikliğiyle atomik yayını.
- **Karar**: Outbox + Debezium CDC — olaylar DB transaction'ıyla yazılır, CDC ile yayınlanır.
- **Alternatifler**: İki-adım (DB + publish) — REDDEDİLDİ (tutarlılık); anında yayım (kuyruk) — REDDEDİLDİ (kayıp riski).
- **Sonuçlar**: microservices.io/transactional-outbox (K013 web-arastirma §5; HTTP 200). **Kod YOK**.

## §12 ADR-103 — audio-streaming-connector (⚠️ DRAFT)

- **Durum**: **⚠️ draft** (1/3: architect — 2026-10-18) — henüz kabul edilmedi.
- **Bağlam**: Ses akışı entegrasyonu (K014 boundary).
- **Karar (taslak)**: audio-streaming-connector — ses akışı adaptörü K013'te (connector deseni).
- **Alternatifler**: K014'te (media-connector) — taslakta değerlendirilmedi.
- **Sonuçlar**: **DRAFT — kabul edilene kadar uygulanmaz** (zero-hallucination: durum korundu).

## §13 Açık Konular (Master §22-§23 — henüz ADR yok)

| # | Konu | Durum | Not |
|---|---|---|---|
| 1 | Sync sahipliği (K013 playback-sync.md vs K015) | **Karar YOK** | §22.4: "media-master §21.4 K015 diyor" — çelişki |
| 2 | YouTube Music API doğrulanmadı | **VERIFICATION REQUIRED** | §22.5: "no official API"; Invidious/Piped alternatifi |
| 3 | Event-contracts 16 olay konuşa biçimi | **Belirsiz** | ADR-084 (adlandırma) var; transport ADR-101; uygulama YOK |
| 4 | IntegrationHub uygulaması | **YOK** | §22.7: "tasarım + ADR var; uygulama YOK" |
| 5 | CircuitBreaker/Bulkhead/DeadLetter/outbox/retry kodu | **YOK** | §22.8: "konuşa biçimi var; kod YOK" |
| 6 | WebhookMotoru kodu | **YOK** | §22.9: "motor YOK" (tasarım §14.5-§14.6) |
| 7 | healthService aidiyeti (K010 vs K012 ServiceHealth) | **Karar YOK** | §10.4 K010 diyor; K012 §3.4-§3.5'te ServiceHealth var |
