---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K8 Servis Layer"
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

# K8: Servis Layer

**Katman:** K8 (Servis)
**Kapsam:** Control, Media, Audio, Device, Network, AI, Download + Health
**Sorumlu Agent:** Backend Architect
**Bileşen Sayısı:** 55

---

## 1. Genel Bakış

K8 katmanı, CoreMusic'in 7 backend servisini içerir. Servisler birbirini doğrudan çağırmaz, Event Bus (PSR-14) üzerinden iletişim kurar.

---

## 2. 7 Servis Haritası

| # | Servis | Port | Protokol | Stack | Sorumluluk |
|---|--------|------|----------|-------|------------|
| 1 | Control Service | 81 | HTTP | PHP 8.4 | Auth, session, RBAC |
| 2 | Media Service | 5000/6000 | HTTP | PHP (FFmpeg/transcode sahibi: K15 — CLAUDE §5) | Library, metadata, streaming uç noktaları (düzeltme C4: FFmpeg K8 kapsamı dışında) |
| 3 | Audio Service | 9741/9742 | REST/WS | C++20 JUCE | Player, DSP, mixer, EQ |
| 4 | Device Service | — | BLE/WiFi/USB | C++20 | Bluetooth, WiFi, USB |
| 5 | Network Audio | — | WebRTC/P2P | C++20 | Streaming, multi-room |
| 6 | AI Service | — | Internal | PHP + Python | Recommendations |
| 7 | Download Service | 3001 | HTTP/WS | Node.js + TS | Deezer/YouTube indirme |

---

## 3. Event Driven Mimari (ADR-086)

```
Service A → Event Bus (PSR-14) → Service B, C, D

Event Types:
  - user.login
  - user.logout
  - track.play
  - track.download
  - playlist.create
  - playlist.update
  - eq.preset.change
  - device.connect
  - device.disconnect
```

---

## 4. Servis Detayları

### 4.1 Control Service (Port 81)

```
Sorumluluklar:
  - Kimlik doğrulama (Auth)
  - Oturum yönetimi (Session)
  - Rol bazlı erişim (RBAC)
  - Kullanıcı profilleri
  - Tercih yönetimi

API Endpoints:
  POST /api/v1/auth/login
  POST /api/v1/auth/logout
  GET  /api/v1/user/profile
  PUT  /api/v1/user/preferences
```

### 4.2 Media Service (Port 5000/6000)

```
Sorumluluklar:
  - Müzik kütüphanesi yönetimi
  - Metadata çıkarma (ID3, FLAC tags)
  - Medya oynatma (streaming)
  - Kapak görselleri
  - Podcast ve video yönetimi

API Endpoints:
  GET  /api/v1/library/tracks
  GET  /api/v1/library/albums
  GET  /api/v1/stream/{track_id}
  POST /api/v1/library/upload
```

### 4.3 Audio Service (Port 9741/9742)

```
Sorumluluklar:
  - Ses oynatma (Neva Engine)
  - DSP işleme (EQ, reverb, compressor)
  - Mixer yönetimi
  - ASIO/WASAPI kontrolü
  - Cihaz seçimi

WebSocket Events:
  audio.play
  audio.pause
  audio.seek
  audio.eq.change
  audio.volume.change
  audio.device.switch
```

### 4.4 Download Service (Port 3001)

```
Sorumluluklar:
  - Deezer FLAC indirme
  - YouTube indirme
  - Queue yönetimi
  - Cache yönetimi
  - Metadata çıkarma

API Endpoints:
  POST /api/v1/download/url
  GET  /api/v1/download/status/{id}
  DELETE /api/v1/download/{id}
  GET  /api/v1/download/queue
```

---

## 5. Servis İletişim Kuralları

| Kural | Açıklama |
|-------|----------|
| Doğrudan çağrı yasak | Servisler arası HTTP çağrısı yasak |
| Event Bus zorunlu | PSR-14 Event Dispatcher |
| Async processing | Uzun işlemler async |
| Circuit breaker | Başarısızlık koruması |
| Retry policy | Max 3 retry |

---

## 6. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-039 | 7-servis platform mimarisi |
| ADR-086 | Event Driven Architecture |

---

*K8 Servis Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-24*
*Mode: Red Team · Human Mode · Truth Mode*

---

## Alt Katman Şeması (K8.a.b.c)

> **Revizyon (2026-09-24):** Bu bölüm 3 turlu agent tartışması sonucu eklenmiştir. §1–§6 (mevcut içerik) silinmemiştir; §2 Media satırı SSOT ihlali nedeniyle tek düzeltilen satırdır (FFmpeg sahipliği K15 — çelişki C4). Adlandırma: adlandirma-kurali.md (K{n} → K{n}.a → K{n}.a.b).

### 1. Kaynak Tablosu

| # | Kaynak | Kullanım |
|---|--------|----------|
| 1 | .ai/CLAUDE.md §5 (K8 satırı: Control, Media, Audio, Device, Network, AI, Download + Infra (11) · sınır: doğrudan çağrı yasak, Event Bus) | Düzey-2 kapsamı, bileşen 55, iletişim sınırı (düzenleyici kaynak) |
| 2 | .ai/CLAUDE.md §5 (K9, K10, K12, K15 satırları) | Katman sınırları: K9→K8 çağırır, K10 K8'e doğrudan erişemez, K12 yalnızca K8'den okur, FFmpeg K15'te |
| 3 | .ai/architecture/frontend-restructuring-plan.md §2.1 L8.1–L8.7 (7 satır) | Düzey-2 eşleme kanıtı (yalnız 7 servis) |
| 4 | k8-servis/README.md §2–§6 | 7 servis port/protokol/stack tablosu, event listesi, endpoint/sorumluluk blokları, iletişim kuralları, ADR-039/086 |
| 5 | k8-servis/index.md (Servis Haritası 11 satır · Roadmap Faz 1–4 · Status · lifecycle/DI/registry/error/middleware · config yaml · performans · güvenlik · observability · testing · bağımlılıklar) | K8.8–K8.10 kanıtı + kök kanıtlar + çelişki C1–C3, C5 |
| 6 | .ai/.decisions/index.md (ADR-039 7-Service Platform · ADR-086 Event Driven Architecture satırları) | Karar adları |
| 7 | k8-servis/*.md (disk glob: 14 dosya) | Düzey-2 disk kanıtları: 11 servis MD + README + index + CLAUDE |
| 8 | .ai/architecture/adlandirma-kurali.md | Şema adlandırma kuralları #1–#5 |

### 2. Şema Kuralları

| Kural | Uygulama |
|-------|----------|
| Kök | K8 (plan §2.1 L8 kökü) |
| Düzey-2 | K8.a — küçük harf-tire (control, media, … notification-sync) |
| Düzey-3 | K8.a.b — yalnızca kanıt varsa (disk MD / README bloğu / index satırı / plan satırı) |
| Düzey-4 | K8.a.b.c — bu katmanda YOK (tek gerçek L4 = K11.1.4.13) |
| .0. yasak | Hiçbir düğümde kullanılmadı |
| K numarası | Belgede her düğüm K8 önekiyle anıldı |

### 3. Düzey-2 Tablosu (K8.a — 10 düğüm)

CLAUDE §5 K8 kapsamı 11 servis içerir (+ Infra (11)); notification ve sync diskte ayrı MD olsa da tek mantıksal düğümde birleştirildi (K8.10) — böylece onaylı X=10 hedefi tutturuldu. Birleştirme dürüstçe işaretlidir.

| # | Düğüm | Görev (kısa) | plan §2.1 | Disk MD |
|---|-------|--------------|:---------:|---------|
| K8.1 | control | Auth, session, RBAC, profil, tercih | L8.1 | control.md |
| K8.2 | media | Kütüphane + metadata + streaming uç noktaları (FFmpeg/transcode: K15) | L8.2 | media.md |
| K8.3 | audio | Oynatma (Neva Engine), DSP, mixer, ASIO/WASAPI, cihaz seçimi | L8.3 | audio.md |
| K8.4 | device | Cihaz keşfi/registry/durum + Bluetooth, WiFi, USB | L8.4 | device.md |
| K8.5 | network | WebRTC/P2P streaming, DLNA, AirPlay, çoklu oda | L8.5 | network.md |
| K8.6 | ai | Müzik analizi, öneri, sesli komut | L8.6 | ai.md |
| K8.7 | download | Deezer/YouTube indirme, queue, cache, metadata | L8.7 | download.md |
| K8.8 | health | Sağlık kontrolleri, readiness/liveness probe | YOK (index Faz 3) | health.md |
| K8.9 | search | Tam metin, bulanık eşleştirme, filtreler | YOK (index Faz 3) | search.md |
| K8.10 | notification-sync | Push bildirim/uyarı + çapraz cihaz/playlist sync | YOK (index Faz 4) | notification.md · sync.md |

### 4. Düzey-3 Düğümleri (K8.a.b — 43 düğüm)

#### K8.1 control — 5 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K8.1.1 kimlik-dogrulama | POST /api/v1/auth/login, logout | README §4.1 |
| K8.1.2 oturum | Session yönetimi | README §4.1 · index Servis Haritası |
| K8.1.3 rbac | Rol bazlı erişim | README §4.1 · index (Authorization: RBAC) |
| K8.1.4 profil | GET /api/v1/user/profile | README §4.1 |
| K8.1.5 tercih | PUT /api/v1/user/preferences | README §4.1 |

#### K8.2 media — 5 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K8.2.1 kutuphane | GET /library/tracks, /library/albums, POST /library/upload | README §4.2 |
| K8.2.2 metadata | ID3, FLAC tag çıkarma | README §2 · §4.2 · K15 sınırı (C4) |
| K8.2.3 streaming | GET /stream/{track_id} | README §4.2 |
| K8.2.4 kapak-gorselleri | Albüm kapakları | README §4.2 · index Servis Haritası |
| K8.2.5 podcast-video | Podcast ve video yönetimi | README §4.2 |

**Kapsam dışı (C4):** FFmpeg/transcode/HLS/DASH işi K8'de DEĞİLDİR — CLAUDE §5 K15 (Medya & Streaming: FFmpeg, FLAC, HLS, DASH, ID3, Radio) sahiptir ve K15 yalnızca K14 üzerinden iletişim kurar.

#### K8.3 audio — 5 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K8.3.1 oynatma | Ses oynatma (Neva Engine); WS: audio.play/pause/seek | README §4.3 |
| K8.3.2 dsp | EQ, reverb, compressor; WS: audio.eq.change | README §4.3 |
| K8.3.3 mixer | Mixer yönetimi; WS: audio.volume.change | README §4.3 |
| K8.3.4 asio-wasapi | ASIO/WASAPI kontrolü; WS: audio.device.switch | README §4.3 |
| K8.3.5 cihaz-secimi | Cihaz seçimi | README §4.3 · index (Audio Service) |

#### K8.4 device — 6 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K8.4.1 kesif | Cihaz keşfi | index Servis Haritası |
| K8.4.2 kayit | Cihaz kayıt | index Servis Haritası |
| K8.4.3 durum-takibi | Cihaz durum takibi | index Servis Haritası |
| K8.4.4 bluetooth | BLE/Bluetooth (event: device.connect/disconnect) | README §2 · §3 |
| K8.4.5 wifi | WiFi aktarım | README §2 |
| K8.4.6 usb | USB bağlantı | README §2 |

#### K8.5 network — 5 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K8.5.1 webrtc-p2p | WebRTC/P2P streaming | README §2 · index (DLNA, AirPlay, WebRTC) |
| K8.5.2 dlna | DLNA (config: friendlyName COREMUSIC) | index Servis Haritası · config yaml |
| K8.5.3 airplay | AirPlay (config: deviceName COREMUSIC Speaker) | index Servis Haritası · config yaml |
| K8.5.4 coklu-oda | Çoklu oda senkronizasyonu | README §2 · index Servis Haritası |
| K8.5.5 streaming-protokol | Ağ üzerinden ses streaming'i | README §2 |

#### K8.6 ai — 3 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K8.6.1 analiz | Müzik analizi (model: coremusic-analyzer-v2) | index Servis Haritası · config yaml |
| K8.6.2 oneri | Tavsiye sistemi (model: coremusic-recommender-v1) | README §2 · index · config yaml |
| K8.6.3 sesli-komut | Sesli komut işleme (model: coremusic-voice-v1) | index Servis Haritası · config yaml |

#### K8.7 download — 5 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K8.7.1 deezer | Deezer FLAC indirme | README §2 · §4.4 |
| K8.7.2 youtube | YouTube indirme; POST /download/url | README §4.4 |
| K8.7.3 queue | İndirme kuyruğu; GET /download/queue | README §4.4 |
| K8.7.4 cache | Cache yönetimi | README §4.4 |
| K8.7.5 metadata-cikarma | İndirilen dosya metadata + status/delete uç noktaları | README §4.4 |

#### K8.8 health — 2 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K8.8.1 liveness-probe | Liveness probe | index (Health Checks: Liveness and readiness probes) |
| K8.8.2 readiness-probe | Readiness probe + sağlık kontrolleri | index Servis Haritası |

#### K8.9 search — 3 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K8.9.1 tam-metin | Tam metin arama | index Servis Haritası |
| K8.9.2 bulanik-eslestirme | Bulanık eşleştirme | index Servis Haritası |
| K8.9.3 filtreler | Filtreler (cache: K5) | index Servis Haritası |

#### K8.10 notification-sync — 4 çocuk

| Düğüm | Görev | Kaynak |
|-------|-------|--------|
| K8.10.1 push-bildirim | Push bildirimleri | index Servis Haritası · notification.md |
| K8.10.2 uyari | Uyarılar (messaging) | index Servis Haritası |
| K8.10.3 capraz-cihaz-sync | Çapraz cihaz senkronizasyonu | index Servis Haritası · sync.md |
| K8.10.4 playlist-sync | Playlist sync | index Servis Haritası |

### 5. Çelişki Kayıt Defteri

| # | Çelişki | Kazanan | Gerekçe |
|---|---------|---------|---------|
| C1 | README §1/§2 = 7 servis ↔ CLAUDE §5 = + Infra (11) ↔ index = 11 servis | CLAUDE §5 (11 servis) | SSOT §2.1; şema 10 düğüm (notification-sync birleşik — dürüstçe işaretlendi) |
| C2 | index: presentation = K14-K20, repository = K7, infra = K5-K6 ↔ CLAUDE §5: K7 = Middleware, K10 = Uygulama, K9 = API | CLAUDE §5 | index.md katman etiketleri yanlıştır; repo/altyapı etiketi için CLAUDE §5'in K5/K6 satırları esastır |
| C3 | Portlar: README §2 (81, 5000/6000, 9741/9742, 3001) ↔ index config yaml (8081–8085) | AÇIK — belirsiz | İki farklı port şeması; prod değeri kanıtlanamadı, uydurulmadı |
| C4 | README §2 Media stack = PHP + FFmpeg ↔ CLAUDE §5 K15 = FFmpeg sahibi | CLAUDE §5 (K15) | README §2 Media satırı bu revizyonda düzeltildi: FFmpeg/transcode → K15; K8.2 yalnız uç nokta + kütüphane CRUD |
| C5 | index: Service → Service gRPC internal calls ↔ README §5 + CLAUDE §5: servisler arası doğrudan çağrı YASAK, Event Bus zorunlu | README §5 + CLAUDE §5 | gRPC satırı reddedildi; iletişim PSR-14 Event Bus (README §3, ADR-086) |

### 6. Matrisler

**Event kataloğu (README §3 · ADR-086):** user.login · user.logout · track.play · track.download · playlist.create · playlist.update · eq.preset.change · device.connect · device.disconnect (9 event — Event Bus K9.9'dur; K8 yalnızca yayınlar/abone olur).

**İletişim kuralları (README §5):** Doğrudan çağrı yasak · Event Bus zorunlu (PSR-14) · Async processing · Circuit breaker · Retry policy (max 3).

**Performans hedefleri (index §Performance Metrics):**

| Metrik | Hedef |
|--------|-------|
| Response Latency p50 / p99 | < 10ms / < 50ms |
| Throughput | > 1000 req/s |
| Error Rate | < 0.1% |
| Availability | 99.99% |
| Memory Usage | < 512MB |

**Bağımlılıklar (index §Bağımlılıklar — etiketler CLAUDE §5'e göre okunur, bkz. C2):** Infrastructure (cache, queue, security, monitoring) · Network abstractions · Data access (repository) · AI engine. Ek sınır: K15 medya işleri (K14 üzerinden).

**Uygulama fazları → düğüm eşlemesi (index §Roadmap + §Durum):**

| Faz | Kapsam | Durum | Düğüm karşılığı |
|-----|--------|:-----:|-----------------|
| Faz 1 | Control, Media, Audio (Core) | ✅ Tamamlandı | K8.1, K8.2, K8.3 |
| Faz 2 | Device, Network, AI (Connectivity) | 🔄 Devam ediyor | K8.4, K8.5, K8.6 |
| Faz 3 | Download, Health, Search (Utility) | ⏳ Beklemede | K8.7, K8.8, K8.9 |
| Faz 4 | Notification, Sync (Enhancement) | ⏳ Beklemede | K8.10 |

**İlgili ADR'ler (README §6 · .decisions/index.md):**

| ADR | Konu | K8 karşılığı |
|-----|------|--------------|
| ADR-039 | 7-Service Platform Architecture (Frozen) | K8.1–K8.7 iskeleti |
| ADR-086 | Event Driven Architecture (Frozen) | Tüm K8 — iletişim paradigmı |

### 7. Katman Sınırları (K9 ↔ K8 ↔ K15 · K12 → K8)

| Sınır | Kural | Kanıt |
|-------|-------|-------|
| K9 → K8 | Gateway/middleware zinciri sonrası use case = K8 servisleri; K10 bu zincire oturur | CLAUDE §5 K9/K10 · k9-api-routing/README.md §2.1 |
| K8 ↔ K8 | Servisler arası doğrudan çağrı YASAK; PSR-14 Event Bus | CLAUDE §5 K8 sınırı · README §5 · ADR-086 |
| K8 → K15 | FFmpeg/transcode K15'tedir; K15 yalnızca K14 üzerinden iletişim kurar → K8→K15 doğrudan çağrı SINIR DIŞI | CLAUDE §5 K15 |
| K10 → K8 | YASAK: K10 yalnızca K9 API üzerinden iletişim kurabilir | CLAUDE §5 K10 |
| K12 → K8 | K12 yalnızca K8 servislerinden okuma yapar (K8 passive observation) | CLAUDE §5 K12 |

### 8. Kök Kanıtlar (tek servise ait olmayan — düzey-2 yapılamaz)

| Kanıt bloğu (index.md) | Neden kök? |
|------------------------|------------|
| IService lifecycle (initialize/start/stop/shutdown, ServiceStatus enum) | 11 servisin ortak arayüzü |
| ServiceRegistry (registerService/getService/initializeAll/startAll/stopAll) | Servisler arası keşif altyapısı |
| DIContainer (registerSingleton/resolve) | Tüm katman DI'ı |
| ServiceResult + ServiceError + 9 ServiceErrorCode | Uniform hata sözleşmesi |
| MiddlewarePipeline (use/execute) | Servis istek hattı — K7 pipeline'ının servis içi dengi |
| config/k8-services.yaml (5 servis port/timeout/concurrency) | Servisler arası ortak config şeması |
| Güvenlik + Observability + Testing başlıkları | Cross-cutting (JWT/RBAC/TLS 1.3 · Prometheus/OTel · unit/integration/contract/load/chaos) |

### 9. Düzey-4 Durumu

**Bu katmanda düzey-4 düğüm YOKTUR (0 adet).** Doğrulama: şemada benimsenen tek gerçek düzey-4 düğüm K11.1.4.13'tür (plan §2.2, c-player.css). K8 için düzey-4 talebi gelirse ek kanıt (disk MD veya plan satırı) zorunludur; uydurma düğüm eklenmez (Guardrail #3).

### 10. Sayım Özeti (K8)

| Seviye | Onaylı hedef (X/Y/Z = T) | Gerçek (2026-09-24 sayımı) | Durum |
|--------|:-------------------------:|:---------------------------:|-------|
| Düzey-2 (K8.a) | 10 | 10 (11 servis → notification-sync birleşik) | ✅ |
| Düzey-3 (K8.a.b) | 5 * | 43 | * tanım belirsiz — gerçek şemanın kendisinden sayıldı |
| Düzey-4 (K8.a.b.c) | 150 * | 0 | * tanım belirsiz — kanıt yok, uydurulmadı |
| Toplam T | 200 (X·Y+Z=10·5+150) | — | * bkz. not |

> **Not (Truth Mode):** Y ve Z'nin tanımı paylaşılmadı; X·Y+Z denklemi K8'de tutuyor (10·5+150=200). "Her dosya ≥500 satır" ile "7 dosya toplamı 1.235" birlikte sağlanamaz (7×500=3.500). Öncelik: (1) gerçek kanıt, (2) düzey-2 = onaylı X, (3) dosya başı ≥500 satır. Sapmalar raporlanmıştır.

### 11. Düzey-2 → Kaynak Çapraz Referans Matrisi

| Düğüm | plan §2.1 | CLAUDE §5 kapsamı | Disk MD | Faz (index) |
|-------|:---------:|:-----------------:|---------|:-----------:|
| K8.1 control | L8.1 | ✔ | control.md | Faz 1 ✅ |
| K8.2 media | L8.2 | ✔ (FFmpeg: K15) | media.md | Faz 1 ✅ |
| K8.3 audio | L8.3 | ✔ | audio.md | Faz 1 ✅ |
| K8.4 device | L8.4 | ✔ | device.md | Faz 2 🔄 |
| K8.5 network | L8.5 | ✔ | network.md | Faz 2 🔄 |
| K8.6 ai | L8.6 | ✔ | ai.md | Faz 2 🔄 |
| K8.7 download | L8.7 | ✔ | download.md | Faz 3 ⏳ |
| K8.8 health | YOK | ✔ (+Infra) | health.md | Faz 3 ⏳ |
| K8.9 search | YOK | ✔ (+Infra) | search.md | Faz 3 ⏳ |
| K8.10 notification-sync | YOK | ✔ (+Infra) | notification.md · sync.md | Faz 4 ⏳ |

**Doğrulama:** 7/10 düğüm plan L8.1–L8.7 ile birebir eşleşiyor; 3/10 (K8.8–K8.10) plan'da satırı YOK — kanıtları index.md Servis Haritası + Roadmap Faz 3/4 + disk MD'lerdir (uydurulmadı, işaretlendi). 10/10 düğümün disk MD'si vardır.

### 12. Şema Kuralı Uyum Matrisi (adlandirma-kurali.md #1–#5)

| Kural | K8 Uygulaması | Durum |
|-------|---------------|-------|
| #1 Kök K{n} | K8 (plan §2.1 L8 kökü) | ✅ |
| #2 Düzey-2 K{n}.a | K8.1–K8.10 (küçük harf-tire; birleşik düğüm: notification-sync) | ✅ |
| #3 Düzey-3 K{n}.a.b | K8.a.b — 43 düğüm | ✅ |
| #4 Düzey-4 K{n}.a.b.c | K8'de 0 (yalnız gerçek L4 = K11.1.4.13) | ✅ |
| #5 ".0." yasak | Hiçbir düğümde ".0." yok | ✅ |

### 13. Kapsam Dışı ve Bilinen Boşluklar

| # | Boşluk | Durum | Sonraki Eylem |
|---|--------|-------|---------------|
| 1 | FFmpeg/transcode/HLS/DASH | K8 DIŞI — K15'te (CLAUDE §5) | Medya işleme görevleri K15 akışına yönlenir; K8.2 yalnız uç nokta |
| 2 | Servisler arası gRPC (index) | Reddedildi (C5) | index.md bu satırı düzeltmeli; yerine PSR-14 Event Bus |
| 3 | Port şeması (C3) | Açık | config/k8-services.yaml ↔ README §2 teyidi (deploy kaynağı) |
| 4 | index.md katman etiketleri (C2) | Açık | index.md revizyonu: K14-K20/K7 etiketleri CLAUDE §5 ile değişmeli |
| 5 | K8.8–K8.10 plan §2.1 L8'de yok | Kabul edildi — işaretli | plan revizyonunda L8.8–L8.10 satırları eklenirse §11 matrisi güncellenir |

### 14. Revizyon ve Denetim Notu

| Öğe | Değer |
|-----|-------|
| Bu revizyon | v1.1.0 — 2026-09-24 (frontmatter updated + source; §2 Media satırı C4 düzeltmesi; footer 2026-09-24) |
| Önceki durum | v1.0.0 — 164 satır, §1–§6 (içerik korundu; §2 Media satırı dışında silme yok) |
| Eklenen H2 | ## Alt Katman Şeması (K8.a.b.c) · ## Kanıt Kataloğu |
| Denetim izi | CLAUDE.md §5 (K8/K9/K10/K12/K15) · plan §2.1 L8.1–L8.7 · k8-servis/ (14 dosya) · .decisions/index.md (ADR-039/086) |
| Sonraki tetik | Servis ekleme/çıkarma, port değişimi veya FFmpeg kapsamı değişirse §3/§10/§11 + Kanıt Kataloğu birlikte yeniden sayılır |

### 15. Event → Abone Eşlemesi ve Güvenlik Kalemleri

**Event eşlemesi (README §3 · index §Event-Driven Patterns):**

| Event | Yayımlayan | Abone | Kanıt |
|-------|-----------|-------|-------|
| user.login / user.logout | K8.1 control | audit/log sahibi (K12 okur) | README §3 |
| track.play | K8.3 audio | K8.6 ai (TrackChangedEvent) | README §3 · index event örnekleri |
| track.download | K8.7 download | K8.6 ai | README §3 |
| playlist.create / playlist.update | K8.1 control · K8.10 sync | K8.2 media · K8.9 search | README §3 · index Servis Haritası |
| eq.preset.change | K8.3 audio | K3 Audio Engine (dış) | README §3 |
| device.connect / device.disconnect | K8.4 device | K8.5 network (DeviceConnectedEvent) | README §3 · index event örnekleri |
| DownloadCompletedEvent (indirme bitti) | K8.7 download | K8.2 media (index §3.3) | index Servis İletişim Şekilleri #3 |

**Güvenlik kalemleri (index §Güvenlik Mimarisi):** JWT token-based auth · RBAC · Token bucket rate limiting · Schema-based input validation · TLS 1.3 inter-service · Audit logging (tüm servis çağrıları).

---

## Kanıt Kataloğu

> Bu katmanın diskteki TÜM .md dosyaları (glob: 14 adet) ve hangi sayımın kanıtını taşıdıkları. Dosya başına 2 satır: açıklama + desteklediği sayaç.

- **README.md** (CoreMusic — K8 Servis Layer) — §2 7 servis haritası (Media satırı bu revizyonda K15 lehine düzeltildi), §3 event listesi (9), §4.1–§4.4 endpoint/sorumluluk blokları, §5 iletişim kuralları, §6 ADR-039/086.
  → Destek: K8.1–K8.7 düzey-2, K8.1.1–K8.7.5 düzey-3 (33), C1/C3/C4/C5, event matrisi, ADR satırları.
- **index.md** (K8 Servis Katmanı - Genel Bakış) — 11 servis haritası, IService/ServiceRegistry/DIContainer/ServiceResult/MiddlewarePipeline C++ blokları, config yaml, performans, güvenlik, observability, testing, bağımlılıklar, Roadmap Faz 1–4 + Durum.
  → Destek: K8.8–K8.10 düzey-2/3 (9), kök kanıtlar §8 (7 blok), C1–C3/C5, faz eşlemesi, performans tablosu.
- **CLAUDE.md** (CoreMusic — K8 Servis CLAUDE.md) — katmana özgü CLAUDE kestirmesi/şablon girişi.
  → Destek: kanıt kaynakları tablosu (kural #6), katalog bütünlüğü (14 dosya sayımı).
- **control.md** (Control Service) — K8.1'in disk kanıtı.
  → Destek: K8.1, K8.1.1–K8.1.5 (düzey-2/3: 6).
- **media.md** (Media Service) — K8.2'nin disk kanıtı.
  → Destek: K8.2, K8.2.1–K8.2.5 + K15 sınır notu (6).
- **audio.md** (Audio Service) — K8.3'ün disk kanıtı.
  → Destek: K8.3, K8.3.1–K8.3.5 (6).
- **device.md** (Device Service) — K8.4'ün disk kanıtı.
  → Destek: K8.4, K8.4.1–K8.4.6 (7).
- **network.md** (Network Service) — K8.5'in disk kanıtı.
  → Destek: K8.5, K8.5.1–K8.5.5 (6).
- **ai.md** (AI Service) — K8.6'nın disk kanıtı.
  → Destek: K8.6, K8.6.1–K8.6.3 (4).
- **download.md** (Download Service) — K8.7'nin disk kanıtı.
  → Destek: K8.7, K8.7.1–K8.7.5 (6).
- **health.md** (Health Service) — K8.8'in disk kanıtı.
  → Destek: K8.8, K8.8.1–K8.8.2 (3).
- **search.md** (Search Service) — K8.9'un disk kanıtı.
  → Destek: K8.9, K8.9.1–K8.9.3 (4).
- **notification.md** (Notification Service) — K8.10 bileşeninin bildirim kanıtı.
  → Destek: K8.10.1–K8.10.2 (2).
- **sync.md** (Sync Service) — K8.10 bileşeninin senkronizasyon kanıtı.
  → Destek: K8.10.3–K8.10.4 (2).

**Dürüstlük kaydı:** K8'de disk MD'si OLMAYAN düzey-2 düğüm YOKTUR (10/10). Ancak K8.8 health, K8.9 search, K8.10 notification-sync plan §2.1 L8.1–L8.7'de temsil edilmiyor — kanıtları index.md Servis Haritası + Roadmap Faz 3/4 + disk MD'lerdir. FFmpeg/transcode kanıtı bu klasörde YOKTUR (yanlış yerde aranmaz — K15'tedir).

**Harici kanıt kaynakları (katalog kapsamı):**

| Kaynak | Kullanım |
|--------|----------|
| .ai/CLAUDE.md §5 (K8, K9, K10, K12, K15 satırları) | Kapsam (11 servis), sınır (Event Bus, K12 okuma, K15 FFmpeg) kanıtları |
| .ai/architecture/frontend-restructuring-plan.md §2.1 L8.1–L8.7 | Düzey-2 eşleme (7 satır) |
| .ai/.decisions/index.md ADR-039, ADR-086 | Karar adları |
| .ai/architecture/adlandirma-kurali.md | Şema adlandırma kuralları #1–#5 |
| disk glob (k8-servis/*.md) | 14 dosya: 10 düzey-2/3 kanıtı + 3 bağlam (README/index/CLAUDE) |

*K8 Alt Katman Şeması + Kanıt Kataloğu v1.1.0 — 2026-09-24 · kaynak: 3 turlu agent tartışması*
