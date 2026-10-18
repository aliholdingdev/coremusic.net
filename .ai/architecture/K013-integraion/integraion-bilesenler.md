---
node_id: K013-integraion/integraion-bilesenler.md
node_type: subdomain
status: draft
parent_id: K013-integraion
children: []
depends_on:
  - K003-audio
  - K007-data
  - K008-domain
  - K009-security
  - K010-network
  - K011-application
  - K012-services
  - K015-AI
evidence:
  - ".ai/architecture/K013-integraion/integration-master.md §1 (bileşen listesi), §4 (architecture), §5.1-§5.5, §14-§17 (bileşen detayları) — okundu 2026-10-18"
  - ".ai/architecture/K013-integraion/integration-point-catalog.md catalog (11 entegrasyon noktası) — okundu 2026-10-18"
  - ".ai/architecture/K013-integraion/event-contracts.md 16 domain event'i — okundu 2026-10-18"
  - ".ai/architecture/K012-services/services-bilesenler.md §3 (K012 bileşenleri — sınır karşılaştırması) — okundu 2026-10-18"
validation:
  - "2026-10-18 — 14 yapı birimi §2-§4; her biri master kaynaklı; fictional durum korundu"
open_questions:
  - "⚠️ K013 bileşenlerinin çoğu 'fictional — KOD YOK' (§4 kapsam tablosu): MusicBrainz/Jellyfin/Plex/Navidrome/YouTubeMusic/Pocketnet/ReadYou/Linn/Stripe/API-Adapter/IntegrationHub/WebhookMotoru."
  - "⚠️ IntegrationHub: tasarım + ADR var; UYGULAMA YOK (fictional)."
  - "⚠️ WebhookMotoru: §14.5-§14.6 tasarım; motor kodu YOK."
  - "⚠️ API-Adapter: §16.6-§16.11 tasarım; kod YOK; §16.4-§16.5 kaynak doğrulanmadı."
  - "⚠️ CircuitBreaker/Bulkhead/DeadLetter/outbox/retry — konuşma biçimi var; KOD YOK (ADR-094)."
  - "⚠️ Event-contracts 16 olay konuşa biçimi belirsiz (Q11)."
  - "⚠️ healthService K010; notification-service K008 (K013 §10.4/§10.6)."
  - "⚠️ Sync sahipliği çelişkisi (K013 playback-sync.md vs K015) — KARAR YOK."
group: mimari
---

# K013 INTEGRAION — Bileşenler

> Kaynak: `integration-master.md` §1 (bileşen listesi) · §4 (architecture) · §5.1-§5.5 · §14-§17 · `integration-point-catalog.md` · `event-contracts.md` · `services-bilesenler.md` (sınır karşılaştırması).
> **Önemli**: K013 bileşenlerinin uygulamaları büyük ölçüde YOK (fictional/tasarım) — master §22.7-§22.9 ("uygulama YOK", "kod YOK", "motor YOK"). Bu durum her bileşende korunmuştur.
> Kod örnekleri (bileşenlerin konuşma biçimleri): `integraion-kod-ornekleri.md`.

## §1 Mimarî Yapı (integration-master.md §1.6-§1.8 — 3 katman)

| Katman | Ad | Rol | Durum |
|---|---|---|---|
| 1 | **integration-data** | Entegrasyon veri modeli (katalog, metadata, sync) | Tasarım — veri modeli K007-data |
| 2 | **integration-health** | Entegrasyon sağlık denetimi (musicbrainz/jellyfin/... health) | Tasarım |
| 3 | **integration-telemetri** | Entegrasyon telemetrisi (audit/metric olayları) | Tasarım |
| Yönetim | **IntegrationHub** | Entegrasyonları görüntüleme/yönetme (UI) | `⚠️ Fictional — KOD YOK` |
| Altyapı | **WebhookMotoru** | Webhook doğrulama/kuyruk/yeniden deneme | `⚠️ Fictional — KOD YOK` (§14.5-§14.6 tasarım) |

## §2 Ana Yapılar (13 — §4 kapsam envanterinden)

### 2.1 MusicBrainz (katalog API entegrasyonu) — `⚠️ Fictional — KOD YOK`

- **Açıklama**: MusicBrainz katalog API entegrasyonu (metadata, release/track eşleme, rate limit 1 sn/kullanıcı — §16.4).
- **Giriş**: Arama sorgusu (album/artist/track), metadata ID.
- **Çıkış**: MusicBrainz metadata (JSON) → catalog-service / domain-katalog.
- **Durum**: `⚠️ Fictional — KOD YOK` (§16: "MusicBrainz API entegrasyonu").
- **Karşılanan**: Katalog metadata (§16.1-§16.3 tasarım) — K008-domain.
- **Karşılamayan**: Discogs eşleme (ayrı adaptör — §16.5). Doğrulanmayan kaynaklar (§16.4-§16.5).
- **Bağımlılık**: `K008-domain` (katalog arama) · `K010-network` (ağ/TLS).

### 2.2 Jellyfin (medya sunucusu entegrasyonu) — `⚠️ Fictional — KOD YOK`

- **Açıklama**: Jellyfin medya sunucusu entegrasyonu (§5.1).
- **Giriş**: Jellyfin sunucu URL/credential (K009 secret).
- **Çıkış**: Medya envanteri/metadata → K014 (oynatma) · catalog (K008).
- **Durum**: `⚠️ Fictional — KOD YOK` (§22.7-§22.9).
- **Karşılanan**: Medya sunucusu metadata paylaşımı (tasarım).
- **Karşılamayan**: Oynatma/transkod (K014) · Plex/Navidrome (ayrı adaptörler).
- **Bağımlılık**: `K014-media` · `K009-security` (credential).

### 2.3 Plex — `⚠️ Fictional — KOD YOK`

- **Açıklama**: Plex medya sunucusu entegrasyonu (§5.2).
- **Giriş/Çıkış**: Jellyfin ile aynı desen (metadata paylaşımı).
- **Durum**: `⚠️ Fictional — KOD YOK` (§23: "Plex/Navidrome fictional").

### 2.4 Navidrome — `⚠️ Fictional — KOD YOK`

- **Açıklama**: Navidrome (Subsonic API'li sunucu) entegrasyonu (§5.3).
- **Durum**: `⚠️ Fictional — KOD YOK`.
- **Not**: Subsonic API konuşa biçimi — K013 §16'da API-Adapter'a dahil (tasarım).

### 2.5 YouTube Music (entegrasyon) — `⚠️ Fictional — YOUTUBE MÜZİK`

- **Açıklama**: YouTube Music entegrasyonu (§17). **⚠️ Resmi API iddiası doğrulanmadı** (§22.5: "no official API — doğrulanmadı").
- **Giriş/Çıkış**: YouTube Music metadata/stream URL (proxy: Invidious/Piped — §10.2).
- **Durum**: `⚠️ Fictional — KOD YOK` + API belirsizliği.
- **Karşılamayan**: Oynatma motoru (K014) · API-Adapter (§16).

### 2.6 Pocketnet (podcast sağlayıcısı) — `⚠️ Fictional`

- **Açıklama**: Pocketnet podcast entegrasyonu (§10.2).
- **Aidiyet çelişkisi**: master §10.2 "K012'de de olabilir" — **KARAR YOK**.
- **Durum**: `⚠️ Fictional — KOD YOK`.

### 2.7 Invidious / Piped (YouTube proxy) — `⚠️ Fictional`

- **Açıklama**: YouTube proxy entegrasyonu (§10.2 — YouTube Music API'siz çözüm önerisi §22.5).
- **Durum**: `⚠️ Fictional — KOD YOK`. Kaynak doğrulanmadı (§16.4-§16.5 benzeri).

### 2.8 ReadYou (RSS) — `⚠️ Fictional`

- **Açıklama**: ReadYou RSS entegrasyonu (§10.2).
- **Durum**: `⚠️ Fictional — KOD YOK`.

### 2.9 Linn (uzaktan cihaz entegrasyonu) — `⚠️ Fictional`

- **Açıklama**: Linn uzaktan cihaz entegrasyonu (§10.5; `master.md §5.5'te referans`).
- **Giriş/Çıkış**: Cihaz keşfi/kontrol komutları (UPnP/RAOP benzeri — konuşa biçimi belirsiz).
- **Durum**: `⚠️ Fictional — KOD YOK`.

### 2.10 Stripe (ödeme webhook entegrasyonu) — `⚠️ Fictional`

- **Açıklama**: Stripe ödeme webhook entegrasyonu (§11).
- **Sınır**: Motor uygulaması **K020-discovery §23**; K013 yalnız webhook akışı (doğrulama + event yayını).
- **Durum**: `⚠️ Fictional — KOD YOK`.

### 2.11 API-Adapter (çoklu protokol köprüsü) — `⚠️ Fictional — KOD YOK (tasarım)`

- **Açıklama**: Farklı API türlerini (REST/GraphQL/SOAP/WebSocket) entegre eden adaptör (§16).
- **Tasarım**: §16.6-§16.11 (protocol adapters: Subsonic, SOAP LMS, RSS, GraphQL proxy, UPnP/DLNA, WebSocket).
- **Durum**: `⚠️ Fictional — KOD YOK`; §16.4-§16.5 kaynak doğrulanmadı.
- **Bağımlılık**: `K012-services` (adapter kaydı) · `K010-network` (transport).

### 2.12 IntegrationHub (entegrasyon yönetimi) — `⚠️ Fictional — KOD YOK`

- **Açıklama**: Entegrasyonları görüntüleme/yönetme (§7).
- **Tasarım**: §7.1 (5 sekme: Overview/Health/Events/Logs/Config) · §7.5 (görsel referans) · §7.7 (K011 UI).
- **Durum**: `⚠️ Fictional — KOD YOK` (§22.7: "tasarım + ADR var; uygulama YOK").
- **Bağımlılık**: `K011-application` (UI katmanı) · `K012-services` (service registry görünümü).

### 2.13 WebhookMotoru — `⚠️ Fictional — KOD YOK (tasarım)`

- **Açıklama**: Webhook doğrulama/kuyruk/işleme/yeniden deneme/idempotency (§14.5-§14.6).
- **Tasarım**: §14.1 (Stripe: `X-Signature: t=,v1=`, HMAC-SHA256) · §14.2 (GitHub: `X-Hub-Signature-256: sha256=`) · §14.3 (imzalı body — raw bytes!) · §14.6 (retry kuyruğu).
- **Durum**: `⚠️ Fictional — KOD YOK` (motor; §14.4: "Stripe/GitHub doğrulama — konuşa biçimi var; motor YOK").
- **Kod örneği**: `integraion-kod-ornekleri.md` §2 (imza doğrulama bloğu).

## §3 Yapısal Yardımcılar (event-contracts + audit/metric — §9)

| Yardımcı | Açıklama | Durum |
|---|---|---|
| **EventContracts** (16 olay) | Domain olayları (Playback/Notification/Audit/Metric/Integration/Catalog) — §9.1-§9.4 | `⚠️ Konuşa biçimi belirsiz` (Q11) |
| **IntegrationHealth** (§8) | 11 health denetimi (musicbrainz-health/jellyfin-health/...) — §8.1-§8.3 | `⚠️ Fictional — KOD YOK` |
| **IntegrationTelemetri** (§9) | audit-event/metric-event (§9.5) | `⚠️ Fictional — KOD YOK` |

## §4 Bileşen → K Kapsamı

| Bileşen | Kapsam Sahibi | Komşu | Not |
|---|---|---|---|
| MusicBrainz | `K008-domain` (katalog arama) | `K013-integraion` | §16: "MusicBrainz arama" |
| Jellyfin/Plex/Navidrome | `K014-media` (medya) + `K008-domain` (katalog) | `K013-integraion` | §5.1-§5.3 |
| YouTube Music | `K014-media`? / `K012-services`? | `K013-integraion` | **KARAR YOK** (§22.5) |
| Pocketnet/Piped/Invidious/ReadYou | `K015-AI` / `K012-services` | `K013-integraion` | **ÇELİŞKİ** (§10.2) |
| Linn | `K001-donanım` (cihaz) + `K014-media` (oynatma) | `K013-integraion` | §10.5 |
| Stripe | `K020-discovery` §23 (motor) | `K013-integraion` (webhook akışı) | §11 |
| API-Adapter | `K013-integraion` | `K010-network` (transport) | §16 |
| IntegrationHub | `K011-application` (UI) + `K012-services` (registry) | `K013-integraion` | §7.7 |
| WebhookMotoru | `K013-integraion` | `K009-security` (imza doğrulama) | §14 |
| healthService | `K010-network` | `K013-integraion` | ⚠️ §10.4: "K010'da; K012'de ServiceHealth var — KARAR YOK" |
| notification-service | `K008-domain` | `K013-integraion` (event-contracts) | §10.6 |
