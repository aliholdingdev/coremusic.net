---
title: "K8 — Servis Katmanı (Service Layer)"
type: architecture
category: layer-definition
date: 2026-09-18
updated: 2026-09-18
status: draft
version: 1.0.0
authority: Bayram Ali / Vault Steward
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/architecture/k8-services.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/brain.md"
  layer: K8
  component_count: 50
  adr:
    - "[[ADR-039-7-service-platform-architecture]]"
    - "[[ADR-084-api-gateway-architecture]]"
    - "[[ADR-086-event-driven-architecture]]"
  github:
    - name: "Ampache"
      url: "https://github.com/ampache/ampache"
    - name: "Forte"
      url: "https://github.com/kaangiray26/forte"
  related:
    - "[[CLAUDE.md]]"
    - "[[AGENTS.md]]"
    - "[[brain.md]]"
---

# K8 — Servis Katmanı (Service Layer)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[brain.md]]

**Kapsam:** CoreMusic ekosistemi için 7 ana servis ve alt servis bileşenleri. Servisler arası iletişim, event-driven mimari ve CQRS pattern. 50 bileşen.

---

## 1. Genel Bakış

K8 katmanı, CoreMusic platformunun tüm backend servislerini tanımlar. Her servis bağımsız çalışır, birbirini doğrudan çağırmaz — event bus üzerinden iletişim kurar.

### 1.1 Servis Mimarisi Diyagramı

```
┌─────────────────────────────────────────────────────────────────────┐
│                      K8 — SERVICE LAYER (50)                        │
├──────────────┬──────────────┬──────────────┬────────────────────────┤
│  CONTROL (8) │  MEDIA (7)   │  AUDIO (7)   │  DEVICE (5)            │
│              │              │              │                        │
│  Auth        │  Library     │  Player      │  Bluetooth             │
│  Session     │  Metadata    │  DSP         │  WiFi                  │
│  RBAC        │  Cover Art   │  Mixer       │  USB                   │
│  Login       │  Lyrics      │  Recorder    │  Sync                  │
│  Register    │  Streaming   │  EQ          │  Discovery             │
│  Password    │  Podcast     │  Spatial     │                        │
│  Profile     │  Video       │  Dynamics    │                        │
│  Settings    │              │              │                        │
├──────────────┴──────────────┴──────────────┴────────────────────────┤
│  NETWORK (6)  │  AI (5)     │  DOWNLOAD (6)│  INFRA (11)            │
│               │             │              │                        │
│  DLNA        │  Recommend  │  Deezer      │  Health Check          │
│  AirPlay     │  Analysis   │  YouTube     │  Circuit Breaker       │
│  Chromecast   │  AutoEQ     │  Queue       │  Load Balancer         │
│  MultiRoom   │  Voice      │  Cache       │  Service Discovery     │
│  Streaming   │  Genre      │  Progress    │  Message Queue         │
│  P2P         │             │  Metadata    │  Event Producer        │
│               │             │              │  Event Consumer        │
│               │             │              │  Retry Logic           │
│               │             │              │  Fallback              │
│               │             │              │  Rate Limit            │
│               │             │              │  Circuit Monitor       │
└───────────────┴─────────────┴──────────────┴────────────────────────┘
```

---

## 2. Control Service (8 Bileşen)

CoreMusic'in merkezi kimlik doğrulama ve yetkilendirme servisi. Port: 81.

| # | Bileşen | Tanım | Teknoloji |
|---|---------|-------|-----------|
| 1 | Control — Auth | Merkezi kimlik doğrulama (JWT + Session hybrid) | PHP 8.4 |
| 2 | Control — Session | Oturum yönetimi (COREMUSIC_SESS cookie) | PHP session |
| 3 | Control — RBAC | Rol bazlı erişim kontrolü (6 rol) | PHP 8.4 |
| 4 | Control — Login | Kullanıcı giriş akışı (email/password) | Argon2id |
| 5 | Control — Register | Kullanıcı kayıt akışı (email doğrulama) | PHP 8.4 |
| 6 | Control — Password | Şifre sıfırlama ve değiştirme | Argon2id |
| 7 | Control — Profile | Kullanıcı profili yönetimi | PDO |
| 8 | Control — Settings | Kullanıcı tercihleri ve ayarları | PDO |

### 2.1 Auth Akışı

```
Kullanıcı → Login Form
  → Control Auth (#1)
    → Argon2id Verify
    → JWT Token Üret (RS256)
    → Session Başlat
  → Cookie: COREMUSIC_SESS (HTTPOnly, Secure, SameSite=Strict)
  → Response: { access_token, user }
```

---

## 3. Media Service (7 Bileşen)

Medya kütüphanesi, metadata yönetimi ve streaming servisi. Port: 5000/6000.

| # | Bileşen | Tanım | Teknoloji |
|---|---------|-------|-----------|
| 9 | Media — Library | Müzik kütüphanesi yönetimi (CRUD) | PHP + PDO |
| 10 | Media — Metadata | Şarkı metadata'sı (ID3 tag, bitrate, duration) | PHP + getID3 |
| 11 | Media — Cover Art | Albüm kapak görselleri yönetimi | PHP + GD/Imagick |
| 12 | Media — Lyrics | Şarkı sözleri entegrasyonu | API + DB |
| 13 | Media — Streaming | HTTP range request ile ses streaming | PHP + Flysystem |
| 14 | Media — Podcast | Podcast yönetimi (show, episode, subscription) | PHP + PDO |
| 15 | Media — Video | Video dosyaları yönetimi (music videos) | PHP + FFmpeg |

---

## 4. Audio Service (7 Bileşen)

Profesyonel ses işleme, oynatma ve mikser servisi. Port: 9741 (REST) / 9742 (WebSocket).

| # | Bileşen | Tanım | Teknoloji |
|---|---------|-------|-----------|
| 16 | Audio — Player | Ses oynatma motoru (ASIO/WASAPI) | C++20 JUCE |
| 17 | Audio — DSP | Dijital sinyal işleme (EQ, compressor, limiter) | C++20 |
| 18 | Audio — Mixer | Çoklu kanal karıştırma (8.1 surround) | C++20 |
| 19 | Audio — Recorder | Ses kayıt motoru (PCM 32-bit float) | C++20 |
| 20 | Audio — EQ | 31-band parametrik equalizer | C++20 |
| 21 | Audio — Spatial | Spatial audio ve surround işleme | C++20 |
| 22 | Audio — Dynamics | Compressor, limiter, noise gate | C++20 |

### 4.1 Audio DSP Pipeline

```
Input → Gain → Gate → HPF → LPF → EQ (31-band)
  → Compressor → Limiter → Delay → Reverb → Output
```

---

## 5. Device Service (5 Bileşen)

Cihaz keşfi, senkronizasyon ve bağlantı yönetimi.

| # | Bileşen | Tanım | Teknoloji |
|---|---------|-------|-----------|
| 23 | Device — Bluetooth | Bluetooth cihaz keşfi ve eşleşme | C++20 BLE |
| 24 | Device — WiFi | WiFi cihaz keşfi ve akış | C++20 mDNS |
| 25 | Device — USB | USB cihaz algılama ve veri aktarımı | C++20 libusb |
| 26 | Device — Sync | Çoklu cihaz senkronizasyonu | WebSocket |
| 27 | Device — Discovery | Ağ üzerindeki cihazları keşfetme | mDNS/DNS-SD |

---

## 6. Network Audio Service (6 Bileşen)

Ağ üzerinden ses streaming ve multi-room desteği.

| # | Bileşen | Tanım | Teknoloji |
|---|---------|-------|-----------|
| 28 | Network — DLNA | DLNA/UPnP medya sunucusu | C++20 |
| 29 | Network — AirPlay | Apple AirPlay alıcı/sunucu | C++20 |
| 30 | Network — Chromecast | Google Chromecast entegrasyonu | C++20 |
| 31 | Network — MultiRoom | Çoklu oda ses senkronizasyonu | C++20 |
| 32 | Network — Streaming | HTTP/WebRTC ses streaming | C++20 |
| 33 | Network — P2P | Peer-to-peer ses aktarımı | C++20 |

---

## 7. AI Service (5 Bileşen)

Yapay zeka destekli müzik öneri ve analiz servisi.

| # | Bileşen | Tanım | Teknoloji |
|---|---------|-------|-----------|
| 34 | AI — Recommendation | Kişiselleştirilmiş müzik önerileri | PHP + Python |
| 35 | AI — Analysis | Müzik analizi (BPM, key, energy) | Python |
| 36 | AI — AutoEQ | Otomatik EQ ayarı (cihaz bazlı) | C++20 |
| 37 | AI — Voice | Sesli komut entegrasyonu | Python + Whisper |
| 38 | AI — Genre | Otomatik tür sınıflandırma | Python ML |

---

## 8. Download Service (6 Bileşen)

Müzik indirme ve önbellek yönetimi. Port: 3001.

| # | Bileşen | Tanım | Teknoloji |
|---|---------|-------|-----------|
| 39 | Download — Deezer | Deezer'dan FLAC indirme (deemix port) | Node.js |
| 40 | Download — YouTube | YouTube'dan ses çıkarma (yt-dlp) | Node.js |
| 41 | Download — Queue | İndirme kuyruğu yönetimi | Node.js + Redis |
| 42 | Download — Cache | İndirilen dosya önbelleği | Node.js + FS |
| 43 | Download — Progress | İndirme ilerleme takibi | WebSocket |
| 44 | Download — Metadata | İndirilen dosya metadata çıkarma | Node.js |

---

## 9. Infrastructure Components (11 Bileşen)

Servis altyapısı ve dayanıklılık bileşenleri.

| # | Bileşen | Tanım | Teknoloji |
|---|---------|-------|-----------|
| 45 | Infra — Health Check | Servis sağlık kontrolü endpoint'leri | PHP |
| 46 | Infra — Circuit Breaker | Devre kesici pattern (hata eşiklerinde) | PHP |
| 47 | Infra — Load Balancer | Yük dengeleme (round-robin, weighted) | Nginx |
| 48 | Infra — Service Discovery | Servis keşfi (static config + DNS) | Config |
| 49 | Infra — Message Queue | Servisler arası mesaj kuyruğu | Redis |
| 50 | Infra — Event Producer | Event oluşturma ve yayma | PSR-14 |
| 51 | Infra — Update Service | Firmware/driver/DSP/AI model güncelleme | PHP |
| 52 | Infra — Notification Service | Push, email, in-app, webhook bildirimleri | PHP |
| 53 | Infra — Logging Service | Yapılandırılmış log, audit trail, log rotation | PSR-3 |
| 54 | Infra — Event Consumer | Event dinleme ve işleme | PSR-14 |
| 55 | Infra — Retry Logic | Başarısız işlemler için üstel geri çekilme | PHP |

### 9.1 Event-Driven Mimari (ADR-086)

```
Service A → Event Bus (PSR-14) → Service B, C, D

Event Types:
  ├── UserLoggedIn → Auth Service → Notification Service
  ├── TrackDownloaded → Media Service → Cache Service
  ├── PlaybackStarted → Analytics Service → Recommendation Service
  ├── DeviceConnected → Device Service → Sync Service
  └── PlaylistCreated → Social Service → Notification Service
```

**Message Bus Mimarisi:**

Tüm servislerarası iletişim **event-driven** message bus üzerinden gerçekleşir. Servisler birbirinin DB'sine **erişemez**.

```
┌─────────────────────────────────────────────────────────────────┐
│                        API Gateway                              │
│              (Routing, Auth, Rate Limit)                        │
├─────────────────────────────────────────────────────────────────┤
│  Core Services          │  Media Services        │  System     │
│  ├── 1. Auth Service    │  ├── 4. Audio Service  │  Services   │
│  ├── 2. User Service    │  ├── 5. DSP Service    │  ├── 9. AI  │
│  └── 3. Device Service  │  ├── 6. Streaming      │  ├── 10. Update
│                         │  ├── 7. Download       │  ├── 11. Notify
│                         │  └── 8. Media Library  │  ├── 12. Monitor
│                         │                        │  └── 13. Log
├─────────────────────────────────────────────────────────────────┤
│                     Message Bus (Event Driven)                   │
└─────────────────────────────────────────────────────────────────┘
```

**Event Kataloğu:**

| Event | Kaynak | Hedef |
|-------|--------|-------|
| `UserCreated` | Auth Service | User, Monitoring, Logging |
| `UserLoggedIn` | Auth Service | Monitoring, Logging |
| `DeviceRegistered` | Device Service | Monitoring, Notification |
| `DeviceOffline` | Device Service | Monitoring, Notification |
| `DeviceOnline` | Device Service | Monitoring, Notification |
| `FirmwareUpdated` | Update Service | Device, Notification, Logging |
| `AudioPlaybackStarted` | Audio Service | DSP, Monitoring |
| `AudioPlaybackStopped` | Audio Service | DSP, Monitoring |
| `PlaylistCreated` | Media Library | Monitoring |
| `DownloadCompleted` | Download Service | Media Library, Notification |
| `DSPPresetChanged` | DSP Service | Audio, Monitoring |
| `HealthCheckFailed` | Monitoring Service | Notification, Logging |
| `SecurityAlertDetected` | Auth Service | Notification, Logging |

**Servis İletişim Kuralları:**

| Kural | Açıklama |
|-------|----------|
| Async First | Tüm iletişim asenkron |
| Event Sourcing | Olaylar kaydedilir |
| Idempotency | Tekrarlanabilir işlemler |
| Circuit Breaker | Bağımlılık kırılma noktası |
| Retry with Backoff | Üstel geri çekilme ile yeniden deneme |
| Dead Letter Queue | Başarısız mesajlar |
| Service Isolation | Hiçbir servis diğerinin DB'sine erişmez |

### 9.2 Circuit Breaker Durumları

```
CLOSED (Normal)
  → Hata sayacı < eşik (5 hata/60s)
  → Devam et

OPEN (Hata)
  → Hata sayacı >= eşik
  → Fallback yanıt döndür
  → 30s bekle

HALF-OPEN (Kontrol)
  → 30s sonra tek istek gönder
  → Başarılı → CLOSED
  → Başarısız → OPEN
```

---

## 10. Servis Yaşam Döngüsü

```
Initialize → Config → Dependency → Health → Ready → Running → Monitoring → Shutdown
```

| Aşama | Açıklama |
|-------|----------|
| Initialize | Servis başlatma, bağımlılıklar kontrol |
| Config | Konfigürasyon yükleme (env, DB, file) |
| Dependency | Bağımlılık servislerinin hazır olması |
| Health | İlk sağlık kontrolü |
| Ready | Trafik almaya hazır |
| Running | Aktif servis |
| Monitoring | Sürekli izleme, metrik toplama |
| Shutdown | Graceful kapanma, cleanup |

---

## 11. API Gateway

| Özellik | Açıklama |
|---------|----------|
| Routing | URL tabanlı yönlendirme |
| Authentication | Token doğrulama |
| Rate Limiting | İstek sınırlandırma (60 req/60s) |
| Validation | Giriş doğrulama |
| Logging | İstek günlüğü |
| Load Balancing | Yük dengeleme |
| Circuit Breaker | Servis koruması |

---

## 12. Servis Sağlık Kontrolü

| Durum | Tanım | Aksiyon |
|-------|-------|---------|
| Healthy | Servis normal | Devam |
| Degraded | Yavaş yanıt | Uyarı |
| Failed | Servis çalışmıyor | Escalation |
| Unknown | Durum bilinmiyor | Yeniden kontrol |

---

## 13. Servis İletişim Matrisi

| Kaynak → Hedef | Protocol | Format | Güvenlik |
|-----------------|----------|--------|----------|
| SPA → API Gateway | HTTP/HTTPS | JSON | JWT + CSRF |
| API Gateway → Control | HTTP | JSON | Internal |
| API Gateway → Media | HTTP | JSON | Internal |
| Control → Media | Event Bus | PSR-14 | Internal |
| Media → Audio | WebSocket | Binary | Internal |
| Audio → Device | BLE/WiFi | Binary | Internal |
| Download → Media | HTTP | JSON | Internal |
| AI → Media | HTTP | JSON | Internal |
| Tüm servisler → Log | PSR-3 | Structured | Internal |

---

## 11. Servis Port Haritası

| Servis | Port | Protokol | Stack |
|--------|------|----------|-------|
| Control Service | 81 | HTTP | PHP 8.4 |
| Media Service | 5000/6000 | HTTP | PHP + FFmpeg |
| Audio Service | 9741 | REST | C++20 JUCE |
| Audio Service | 9742 | WebSocket | C++20 |
| Device Service | — | BLE/WiFi/USB | C++20 |
| Network Audio | — | WebRTC/P2P | C++20 |
| AI Service | — | Internal | PHP + Python |
| Download Service | 3001 | HTTP/WS | Node.js + TS |

---

## 12. Cross References

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| K8 Services | K6 Security | Auth, RBAC bileşenleri |
| K8 Services | K7 Middleware | Pipeline integrasyonu |
| K8 Services | K9 API Routing | Gateway, BFF, CQRS |
| K8 Services | K5 Data | Repository pattern |
| K8 → ADR-039 | 7 Servis | Platform mimarisi |
| K8 → ADR-084 | API Gateway | API-First |
| K8 → ADR-086 | Event Driven | PSR-14 |
| K8 → Ampache | Referans | Open-source müzik platformu |
| K8 → Forte | Referans | Müzik streaming |

---

## 13. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.1.0 |
| Status | draft |
| Total Components | 55 |
| Control Service | 8 |
| Media Service | 7 |
| Audio Service | 7 |
| Device Service | 5 |
| Network Audio | 6 |
| AI Service | 5 |
| Download Service | 6 |
| Infrastructure | 16 (11 + Update + Notify + Log + Event Consumer + Retry) |
| ADR Coverage | 3 ADR referansı |
| GitHub References | Ampache, Forte |
| Event Types | 13+ |
| Service Lifecycle | 8 aşamalı |

---

## 14. Class AB Servis Entegrasyonu

Audio Service, Class AB amplifikator ile entegre calisir:
- [[electronics/amplifier-classab-circuit]] — Amplifikator kontrol servisi
- [[electronics/power-supply-classab]] — Guç durumu servisi
- [[electronics/thermal-design-classab]] — Termal izleme servisi

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
