---
node_id: K013-integraion/integraion-kapsam.md
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
  - ".ai/architecture/K013-integraion/integration-master.md §1 (Kapsam + 11 bileşen), §2-§11, §6 (durum), §21-§23 (QA + kısıtlar) — okundu 2026-10-18"
  - ".ai/architecture/K013-integraion/integration-point-catalog.md catalog (bileşen envanteri) — okundu 2026-10-18"
  - ".ai/architecture/K013-integraion/event-contracts.md 16 domain event'i — konuşa biçimi belirsiz — okundu 2026-10-18"
  - ".ai/architecture/K013-integraion/adr-integration.md 17 karar — okundu 2026-10-18"
  - ".ai/.decisions/index.md ADR-084/086/100/101/081/050/020/094/095/103(draft)/037 · R-011 — okundu 2026-10-18"
validation:
  - "2026-10-18 — Bu dosya yalnız mevcut K013 kaynaklarını özetliyor; yeni iddia yok; §5 kısıtları §22-23'ten"
open_questions:
  - "⚠️ sync ownership: playback-synchronization.md 'synchronization'ı kendine ait sayıyor; media-master.md §21.4 'K015'e ait'. Master §23: karar YOK."
  - "⚠️ sync caller: media-master.md §18 media-service çağırır; master §23: K013 çağırabilir — çelişki çözülmedi."
  - "⚠️ YouTube Music (11. entegrasyon noktası): 'no official API' iddiası doğrulanmadı — VERIFICATION REQUIRED."
  - "⚠️ event-contracts.md 16 olay konuşa biçimi belirsiz (ADR-084: 'kodlar konuşmasın' — tek uygun altyapı K013'te)."
  - "⚠️ IntegrationHub (K013 §7): tasarım + ADR var; UYGULAMA YOK (fictional)."
  - "⚠️ CircuitBreaker/Bulkhead/DeadLetter/outbox/retry — konuşma biçimi var; KOD YOK (ADR-094/086)."
  - "⚠️ WebhookMotoru — K013 §14 tasarımı; motor YOK (fictional)."
  - "⚠️ ADR-103 (kilitli audio-streaming-connector) DRAFT (1/3: architect); ADR-081 DRAFT (1/3: backend-architect)."
  - "⚠️ MusicBrainz/Jellyfin/Plex/Navidrome/Invidious/Piped/Pocketnet/ReadYou/Linn — 7/7 kod YOK (fictional)."
  - "⚠️ API-Adapter'ın kodu YOK; K013 §16.6-§16.11 yalnız tasarım."
  - "⚠️ event-contracts.md olay adları media-master §16.3'ten farklı (PlaybackStartedChanged/PlaybackPositionChanged)."
  - "⚠️ Dead-ref'ler korundu (master §5.1/§5.2-§5.5; §19.1'de 404 listesi)."
group: mimari
---

# K013 INTEGRAION — Kapsam

> **Dizin adı `integraion` yazım hatasıdır — donduruldu** (dizin adı değiştirilemez; dosya adları `integraion-` önekli).
> **İstisna**: `integration-master.md` (İngilizce master — mevcut dosya, imzalı EK-2026-10-18).
> Kaynak: `integration-master.md` §1-§23 · `integration-point-catalog.md` · `event-contracts.md` · `adr-integration.md`.
> Doğrulama: `integration-master.md` §21.3 doğrulama = `✅ TAMAMLANDI` (2026-10-18, 23/23 başlık).
> **Web doğrulama (EK-2026-10-18)**: 24 URL = 18 × HTTP 200 + 6 × HTTP 404 (§19-§19.1 — bkz. `integraion-web-arastirma.md`).

## §1 Kapsam — K013 Ne, Değil

**K013 (integraion)**, CoreMusic'in dış dünya ile ilişki kurduğu tüm noktaların (11 bileşen) tek merkezde toplandığı **entegrasyon yönetim** alanıdır: müzik katalogları (MusicBrainz/Discogs), medya sunucuları (Jellyfin/Plex/Navidrome), YouTube Music, podcast sağlayıcıları (Pocketnet/Invidious/Piped), RSS (ReadYou), uzaktan cihaz (Linn) ve ödeme (Stripe); **event-contracts** (16 domain event — konuşma biçimi belirsiz) ve **WebhookMotoru** (tasarım). **Kapsam dışı**: medya oynatma/dönüştürme (K014-media) · K012'de öngörülen hizmet altyapısı (service registry/health) · ödeme motoru uygulaması (K020-discovery §23) · entegrasyon noktası veri modeli (`integration-point-catalog.md`, `K007-data`) · sync sahipliği (K015 ile paylaşılıyor — karar YOK).

## §2 Amaçlar

1. **Tek merkez**: 11 entegrasyon noktası tek yapıda (`integration-master.md` §1: "bileşik tek merkez").
2. **Sözleşme güvencesi**: 16 domain event'i belge (`event-contracts.md`) — §4.
3. **Dayanıklılık**: hatalı entegrasyon ana sistemi çökertmez (§5 kısıtlar: "hasta servisler asla mantıklı veri üretmemeli").
4. **Değişiklik yalıtımı**: 3. taraf değişikliği (API sürümü/kapanma) uygulamayı bozmaz (ADR-094 konuşla biçimi).
5. **Denetlenebilirlik**: her entegrasyon health + audit ile izlenir (§8; `audit`/`metric` olayları).

## §3 Kapsam Dışı (Bu Alanda Değil)

| Alan | Sahip | Not |
|---|---|---|
| Medya oynatma/dönüştürme/transkod | `K014-media` | §6: "oynatma sahipliği K014". §5.5: "streaming playback is out-of-scope". |
| Hizmet altyapısı (registry/health) | `K012-services` | K013 §1.6-§1.8: katalog/sağlayıcı/telemetri katmanları K012'de öngörülü |
| Ödeme motoru uygulaması | `K020-discovery` §23 | K013 §11: "Stripe ödeme entegrasyonu" — motor K020 |
| Entegrasyon noktası veri modeli | `K007-data` | §3.4: "veri modeli K007-data" |
| Sync sahipliği | `K015-AI` (media-master §21.4) / K013 (playback-sync.md) | ⚠️ ÇELİŞKİ — karar YOK |
| Podcast sağlayıcı entegrasyonları (Pocketnet/Invidious/Piped) | §10.2: "K012'de de olabilir" | ⚠️ ÇELİŞKİ — karar YOK |
| OAuth entegrasyonu | `K009-security` | §10.1: OAuth K009, K013 yalnız tüketir |
| Katalog arama | `K008-domain` | §10.3: arama K008, K013 entegrasyonu |
| Bildirim | `K008-domain` | §10.6: bildirim K008; K013 yalnız kuyruk/gateway |
| Sağlık denetimi | `K010-network` | §10.4: healthService K010 (⚠️ K012'de ServiceHealth var — KARAR YOK) |

## §4 Bileşen Envanteri (11 + 2 — diskte)

| # | Bileşen | Dosya (değiştirilemez ad) | Durum |
|---|---|---|---|
| 1 | MusicBrainz (katalog API) | `integration-master.md` §16 | `⚠️ Fictional — KOD YOK` |
| 2 | Jellyfin (medya sunucusu) | §5.1 | `⚠️ Fictional — KOD YOK` |
| 3 | Plex | §5.2 | `⚠️ Fictional — KOD YOK` |
| 4 | Navidrome | §5.3 | `⚠️ Fictional — KOD YOK` |
| 5 | YouTube Music | §17 | `⚠️ Fictional — YOUTUBE MÜZİK` |
| 6 | Pocketnet (podcast) | §10.2 | `⚠️ Fictional — PODCAST SAĞLAYICI` |
| 7 | Invidious / Piped (YouTube proxy) | §10.2 | `⚠️ Fictional` |
| 8 | ReadYou (RSS) | §10.2 | `⚠️ Fictional` |
| 9 | Linn (uzaktan cihaz) | §10.5 | `⚠️ Fictional — `master.md §5.5'de` referans` |
| 10 | Stripe (ödeme webhook) | §11 | `⚠️ Fictional` (motor K020) |
| 11 | API-Adapter (çoklu protokol köprüsü) | §16 | `⚠️ Fictional — KOD YOK (tasarım)` |
| 12 | IntegrationHub (yönetim arayüzü) | §7 | `⚠️ Fictional — tasarım + ADR` |
| 13 | WebhookMotoru | §14 | `⚠️ Fictional — tasarım (motor YOK)` |

Ek yapılar: **event-contracts** (16 olay) · **adr-integration** (17 karar — ADR-084/086/100/101/081/050/020/094/095/103(draft)/037 + R-011).

## §5 Proje Kısıtları (§22-§23 — bağlayıcı)

1. **§22.3**: "Entegrasyon mimarisi `integration-point-catalog.md`, `event-contracts.md`, `adr-integration.md` içinde tanımlıdır" (imzalı: 2026-10-18).
2. **§22.4**: "Sync sahipliği belirsiz — playback-synchronization.md kendine ait sayıyor, media-master.md §21.4 K015'e veriyor. Karar YOK".
3. **§22.5**: "YouTube Music'in resmi API'si yok — doğrulanmadı. Invidious/Piped proxy alternatifi".
4. **§22.6**: "Event-contracts 16 olay — konuşa biçimi belirsiz".
5. **§22.7-§22.9**: "IntegrationHub tasarım + ADR var; uygulama YOK. CircuitBreaker/Bulkhead/DeadLetter/outbox/retry — konuşma biçimi var; kod YOK. WebhookMotoru tasarımı var; motor YOK".
6. **§23.1-§23.11**: 11 nokta — §3-§4'teki çelişkiler/fictional'lar bu maddelerin özeti (master'da tam metin).

## §6 Bağımlılıklar (Diğer K'ler — §10)

| Bağımlı K | Rol (§10 maddeleri) | Not |
|---|---|---|
| `K009-security` | OAuth entegrasyonu (§10.1) | §10.1: "OAuth K009'da; K013 yalnız tüketir" |
| `K008-domain` | Katalog arama (§10.3) + bildirim (§10.6) | 16 olay = domain event'ler |
| `K010-network` | healthService (§10.4) + ağ (TLS/proxy) | ⚠️ healthService aidiyeti KARAR YOK |
| `K012-services` | servis altyapısı (§1.6-§1.8, §10.7) | §10.7: "Servis registry K012'de; K013 entegrasyonları K012'ye kaydeder" |
| `K015-AI` | sync (§21.4) + podcast (§10.2) | ⚠️ sync çelişkisi |
| `K014-media` | oynatma (§5.5 kapsam dışı) | PlaybackPositionChanged/K014 |
| `K007-data` | entegrasyon noktası veri modeli (§3.4) | |
| `K011-application` | UI (IntegrationHub, §7) | §23.11: "IntegrationHub UI'da K011'e devredilir" |

## §7 Doğrulama Geçmişi (§21)

1. **§21.3**: `✅ TAMAMLANDI` — 23/23 başlık (`integration-master.md`, 2026-10-18).
2. **§21.1-§21.2**: K008/K010 atıfları düzeltildi (2026-10-14 — `git grep`).
3. **§19**: 24 web kaynağı — 18 × 200 + 6 × 404 (`integration-master.md` EK-2026-10-18; bkz. `integraion-web-arastirma.md`).
4. **K013 içeriği**: `integration-point-catalog.md` + `event-contracts.md` + `adr-integration.md` (bileşen envanteri + 16 olay + 17 karar).

## §8 Sınır Dışı Bağımlılıklar (Çevresel — sahibi olan alan)

| Dış | Sahip | Not |
|---|---|---|
| MusicBrainz/Discogs/Pocketnet/ReadYou/Linn API'leri | 3. taraf (dış) | §16.4-§16.5: "kaynak/URL doğrulanmadı" |
| OAuth servisleri (Spotify/Apple/Google) | 3. taraf | §10.1 |
| Stripe webhook | 3. taraf | §11 — `⚠️ Fictional` (motor K020 §23) |
| Ağ (TLS/proxy/healthService) | `K010-network` | §10.4: "Ağ K010'da; K013 yalnız tüketir" |
| Ödeme motoru | `K020-discovery` §23 | §11: "motor uygulaması K020" |
| Entegrasyon veri modeli | `K007-data` | §3.4 |
| UI (IntegrationHub) | `K011-application` | §7.7-§7.8: "UI'da K011'e devredilir" |
