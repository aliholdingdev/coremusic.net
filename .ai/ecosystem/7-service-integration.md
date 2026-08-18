---
type: ecosystem
category: service-integration
title: "7-Service Integration — CoreMusic Servis Ekosistemi"
date: 2026-08-08
updated: 2026-08-15
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ecosystem/7-service-integration.md"
  architecture_master: ".ai/architecture/00-overview/architecture-master.md"
  adr:
    - "decisions/accepted/ADR-039-7-service-platform-architecture"
    - "decisions/accepted/ADR-032-ipc-contract-versioning"
    - "decisions/accepted/ADR-084-api-gateway-architecture"
    - "decisions/accepted/ADR-086-event-driven-architecture"
---

# 7-Service Integration — CoreMusic Servis Ekosistemi

**İlgili ADR:** [[decisions/accepted/ADR-039-7-service-platform-architecture]] · [[decisions/accepted/ADR-032-ipc-contract-versioning]] · [[decisions/accepted/ADR-084-api-gateway-architecture]] · [[decisions/accepted/ADR-086-event-driven-architecture]]

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[architecture/00-overview/architecture-master]]

---

## 1. Amaç

CoreMusic'in 7 backend servisinin tam entegrasyon haritası, servisler arası iletişim protokolleri, bağımlılık zincirleri ve veri akışını tanımlayan **Ana Servis Ekosistemi** dosyasıdır. Tüm ekosistem dosyalarının referans noktasıdır.

---

## 2. 7 Servis Tanımı

| # | Servis | Port | Protocol | Stack | Sorumluluk | ADR |
|---|--------|------|----------|-------|------------|-----|
| 1 | **Control Service** | 81 | HTTP | PHP 8.4 | Auth, session, RBAC, routing, middleware pipeline | ADR-039 |
| 2 | **Media Service** | 5000/6000 | HTTP/WS | PHP + FFmpeg | Library, metadata, streaming, encode/decode | ADR-039 |
| 3 | **Audio Service** | 9741/9742 | REST/WS | C++20 JUCE | Player, DSP, mixer, EQ, effects, 8.1 surround | ADR-017 |
| 4 | **Device Service** | — | BLE/WiFi/USB | C++20 | Bluetooth, WiFi, USB connections, device sync | ADR-064 |
| 5 | **Network Audio** | — | WebRTC/P2P | C++20 | Streaming, multi-room, P2P, synchronization | ADR-039 |
| 6 | **AI Service** | — | Internal | PHP + Python | Recommendations, auto-download, EQ auto-tune | ADR-030 |
| 7 | **Download Service** | 3001 | HTTP/WS | Node.js + TypeScript | YouTube/Deezer download, queue, anti-ban | ADR-026 |

*Kaynak: [[architecture/00-overview/architecture-master]] §5.1*

---

## 3. Port Haritası

| Port | Servis | Protokol | Erişim |
|------|--------|----------|--------|
| 80 | admin.coremusic.net | HTTP | Public |
| 81 | music.coremusic.net (Control Service) | HTTP | Public |
| 3001 | download.coremusic.net | HTTP/WS | Public |
| 3306 | MySQL 9 (18 BCNF DB) | TCP | Internal |
| 5000 | Media Service (HTTP) | HTTP | Internal |
| 6000 | Media Service (WebSocket) | WS | Internal |
| 9741 | Audio Service (REST) | HTTP | Internal |
| 9742 | Audio Service (WebSocket) | WS | Internal |

*Kaynak: [[architecture/00-overview/architecture-master]] §6*

---

## 4. Servis Bağımlılık Matrisi

### 4.1 Doğrudan Bağımlılıklar

```
Control (81) ──→ Auth DB (coremusic_auth)
    ├──→ Media Service (5000)    — medya metadata
    ├──→ Audio Service (9741)    — oynatma kontrolü
    └──→ Download Service (3001) — indirme durumu

Media (5000) ──→ Musics DB (coremusic_musics)
    ├──→ Audio Service (9741)    — ses stream
    ├──→ Download Service (3001) — dosya alma
    └──→ AI Service             — öneri

Audio (9741) ──→ Device Service   — donanım bağlantısı
    ├──→ Network Audio           — multi-room
    └──→ Neva DB (coremusic_neva) — EQ preset

Download (3001) ──→ Musics DB (coremusic_musics)
    ├──→ Media Service (5000)    — dosya kaydetme
    ├──→ Catalog DB (coremusic_catalog) — metadata
    └──→ Download DB (coremusic_download) — kuyruk

AI Service ──→ Musics DB (coremusic_musics)
    ├──→ User DB (coremusic_user) — tercihler
    ├──→ AI DB (coremusic_ai)     — model çıktıları
    └──→ Control Service (81)     — bildirim

Device Service ──→ Wireless DB (coremusic_wireless)
    └──→ Audio Service (9742)     — ses yönlendirme

Network Audio ──→ Audio Service (9742)
    └──→ Wireless DB (coremusic_wireless)
```

### 4.2 Bağımlılık Matrisi (Tablo)

| Kaynak ↓ / Hedef → | Control | Media | Audio | Device | Network | AI | Download |
|---------------------|---------|-------|-------|--------|---------|-----|----------|
| **Control** | — | ✅ | ✅ | ❌ | ❌ | ✅ | ✅ |
| **Media** | ✅ | — | ✅ | ❌ | ❌ | ✅ | ✅ |
| **Audio** | ❌ | ✅ | — | ✅ | ✅ | ❌ | ❌ |
| **Device** | ❌ | ❌ | ✅ | — | ❌ | ❌ | ❌ |
| **Network Audio** | ❌ | ❌ | ✅ | ❌ | — | ❌ | ❌ |
| **AI** | ✅ | ✅ | ❌ | ❌ | ❌ | — | ❌ |
| **Download** | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ | — |

---

## 5. İletişim Protokolleri

| Protokol | Kullanım | Port | Servisler |
|----------|----------|------|-----------|
| **HTTP REST** | Senkron API çağrıları | 81, 3001, 5000, 9741 | Tüm servisler |
| **WebSocket** | Gerçek zamanlı güncelleme | 6000, 9742, 3001 | Media, Audio, Download |
| **Shared Memory** | Yüksek performanslı veri paylaşımı | — | Audio ↔ Device |
| **gRPC** | Yüksek performanslı IPC (gelecek) | 9001-9003 | Servisler arası |
| **WebRTC** | P2P ses akışı | 49152-65535 | Network Audio |

*Detay: [[ecosystem/service-communication]]*

---

## 6. Event Driven Architecture (ADR-086)

Servisler birbirini doğrudan çağırmaz, **event** yayınlar:

```
Service A → Event Bus (PSR-14) → Service B, C, D
```

### 6.1 Event Türleri

| Event | Yayınlayan | Tüketen | Kullanım |
|-------|-----------|---------|----------|
| `UserAuthenticated` | Control | Media, AI | Kullanıcı girişi |
| `TrackDownloaded` | Download | Media, AI | İndirme tamamlandı |
| `PlaybackStarted` | Audio | AI, Device | Oynatma başladı |
| `DeviceConnected` | Device | Audio, Network | Cihaz bağlandı |
| `EQPresetChanged` | Audio | Device | EQ ayarı değişti |
| `LibrarySynced` | Media | AI | Kütüphane güncellendi |

---

## 7. BFF (Backend for Frontend)

Her istemci tipi kendi BFF'sini kullanır (ADR-084):

| İstemci | BFF | Response | Auth |
|---------|-----|----------|------|
| SPA (music.coremusic.net) | SPA BFF | Tam veri | JWT + Session |
| Mobile | Mobile BFF | Minimal | JWT |
| Embedded (RPi5) | Embedded BFF | Ultra-minimal, gzip | Local (SQLite) |
| Desktop | Desktop BFF | Orta boy | JWT + Session |
| Admin | Admin BFF | Full + audit | JWT + RBAC |
| Car | Car BFF | Touch-optimized | Local (SQLite) |

---

## 8. CQRS Akışı

Yazma ve okuma işlemleri tamamen ayrılır (ADR-086):

```
Yazma: Command → Use Case → Repository → MySQL Master
Okuma: Query → Read Model → Cache → Response
```

| İşlem Tipi | Örnek | Akış |
|-----------|-------|------|
| **Yazma** | Kullanıcı kaydı | Command → AuthUseCase → UserRepository → coremusic_auth |
| **Okuma** | Şarkı listesi | Query → MusicReadModel → APCu Cache → Response |
| **Yazma** | İndirme ekleme | Command → DownloadUseCase → DownloadRepository → coremusic_download |
| **Okuma** | EQ preset | Query → NevaReadModel → Redis Cache → Response |

---

## 9. Veritabanı Eşleme

Her servis hangi DB'leri kullanır:

| Servis | Kullanılan DB'ler | Erişim |
|--------|-------------------|--------|
| Control | coremusic_auth, coremusic_user, coremusic_system | Read/Write |
| Media | coremusic_musics, coremusic_albums, coremusic_catalog | Read/Write |
| Audio | coremusic_neva, coremusic_musics | Read |
| Device | coremusic_wireless, coremusic_media | Read/Write |
| Network Audio | coremusic_wireless | Read |
| AI | coremusic_ai, coremusic_user, coremusic_musics | Read/Write |
| Download | coremusic_download, coremusic_musics, coremusic_catalog | Read/Write |

*Kaynak: [[architecture/00-overview/architecture-master]] §3*

---

## 10. Middleware Pipeline (Frozen — 10 Katman)

Tüm HTTP servisleri aynı middleware pipeline'ı kullanır:

```
Request → OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf → BypassAuth → Auth → Permission → Validation → Controller
```

| # | Middleware | Timeout | Servis |
|---|-----------|---------|--------|
| 1 | OriginCheck | — | Tümü |
| 2 | Cors | — | Tümü |
| 3 | RateLimiter | 60s | Tümü |
| 4 | SecurityHeaders | — | Tümü |
| 5 | SessionManager | 3600s | Tümü |
| 6 | Csrf | — | Tümü |
| 7 | BypassAuth | — | Sadece test |
| 8 | Auth | — | Tümü |
| 9 | Permission | — | Tümü |
| 10 | Validation | — | Tümü |

**Kritik:** Sıra DEĞİŞTİRİLEMEZ. CSP nonce SecurityHeaders (#4) üretilir, SessionManager (#5) session'a kaydeder.

*Kaynak: [[architecture/00-overview/architecture-master]] §2*

---

## 11. Servis Başlatma Sırası

```
1. MySQL (coremusic_auth DB zorunlu)
   ↓
2. Control Service (81) — auth temeli
   ↓
3. Media Service (5000) — medya altyapısı
   ↓
4. Audio Service (9741) — ses motoru
   ↓
5. Download Service (3001) — indirme
   ↓
6. AI Service — öneri (bağımsız)
   ↓
7. Device Service — donanım (son)
   ↓
8. Network Audio — multi-room (son)
```

*Detay: [[ecosystem/service-health-check]]*

---

## 12. Fallback Zincirleri

| Senaryo | Birincil | Fallback 1 | Fallback 2 |
|---------|----------|------------|------------|
| Control Service çökmesi | MySQL session | Local cache | Return 503 |
| Media Service çökmesi | HTTP 503 | Cache'den oku | Partial content |
| Audio Service çökmesi | ASIO | WASAPI | Null Output |
| Download Service çökmesi | HTTP 503 | Kuyrukta bekle | Retry (exponential) |
| AI Service çökmesi | HTTP 503 | Default öneriler | Kullanıcı tercihi |
| Device Service çökmesi | BLE | WiFi Direct | USB |
| Network Audio çökmesi | WebRTC | P2P | Local playback |

*Detay: [[ecosystem/error-recovery]]*

---

## 13. Güvenlik Katmanları

| Katman | Mekanizma | ADR |
|--------|-----------|-----|
| **Transport** | TLS 1.3 (HTTPS) | ADR-022 |
| **Authentication** | Hybrid JWT + Session | ADR-011 |
| **Authorization** | RBAC (7 rol) | ADR-052 |
| **CSRF** | csrf_token (session-bound) | ADR-010 |
| **CSP** | nonce + strict-dynamic | ADR-012 |
| **Rate Limit** | APCu 60 req/60s | ADR-013 |
| **Encryption** | AES-256-GCM (credential vault) | ADR-034 |
| **Password** | Argon2id (64MB/4/2) | ADR-022 |

---

## 14. Scale & Performance

| Metrik | Hedef | Minimum |
|--------|-------|---------|
| **TTFB** | <200ms | <500ms |
| **API Yanıt** | <100ms | <200ms |
| **Eş Zamanlı Kullanıcı** | 1000 | 100 |
| **İndirme Hızı** | 10MB/s | 1MB/s |
| **Ses Gecikmesi (ASIO)** | <5ms | <10ms |
| **Ses Gecikmesi (WASAPI)** | <10ms | <20ms |
| **Cache Hit** | >90% | >70% |

---

## 15. Deployment Modları

| Mod | Aktif Servisler | DB |
|-----|-----------------|-----|
| **Home Media Center** | Control, Media, Audio, AI | MySQL (18 BCNF) |
| **Car Audio System** | Control, Audio, Device | SQLite (local) |
| **Professional Studio** | Control, Media, Audio, Network | MySQL (18 BCNF) |
| **NAS Audio Server** | Control, Media, Download | MySQL (Docker) |
| **DAC Control System** | Control, Audio, Device | SQLite (local) |

*Kaynak: [[architecture/00-overview/architecture-master]] §5.1*

---

## 16. Troubleshooting

| Sorun | Belirti | Çözüm |
|-------|---------|-------|
| Control Service unavailable | 401/403 tüm panellerde | MySQL kontrol, port 81 dinleniyor mu? |
| Media Service yavaş | Metadata yüklenmiyor | FFmpeg kontrol, cache temizle |
| Audio Service kopuk | Ses gelmiyor | ASIO/WASAPI driver, port 9741 |
| Download Service kuyrukta | İndirme başlamıyor | Node.js process, port 3001 |
| AI Service yavaş | Öneriler gelmiyor | Model yükleme, DB bağlantısı |
| Device Service bağlı değil | Bluetooth/WiFi yok | Cihaz driver, BLE scan |
| Network Audio senkronize değil | Multi-room gecikme | WebRTC bağlantısı, buffer ayarı |

---

## 17. Cross References

| Dosya | Amaç | ADR |
|-------|------|-----|
| [[ecosystem/service-health-check]] | Health check endpoint'leri | — |
| [[ecosystem/service-communication]] | İletişim protokolleri | ADR-032 |
| [[ecosystem/panel-integration]] | 10-panel entegrasyonu | ADR-084 |
| [[ecosystem/error-recovery]] | Hata kurtarma stratejileri | — |
| [[ecosystem/state-machines]] | State machine'ler | — |
| [[architecture/03-services]] | Servis detayları | ADR-039 |
| [[architecture/06-audio]] | Audio servis detayı | ADR-017 |
| [[architecture/03-contracts/api-architecture-master]] | API mimarisi | ADR-084 |

---

## 18. Quality Report

| Metrik | Değer |
|--------|-------|
| **Version** | 2.0.0 |
| **Status** | Red Team · Human Mode · Truth Mode verified |
| **Sections** | 18 |
| **Service Count** | 7 |
| **Port Count** | 8 |
| **Protocol Count** | 5 (HTTP, WS, gRPC, IPC, WebRTC) |
| **DB Mapping** | 12 DB (18 BCNF'den) |
| **Fallback Chains** | 7 |
| **Event Types** | 6 |
| **BFF Types** | 6 |
| **Deployment Modes** | 5 |
| **Troubleshooting** | 7 senaryo |
| **Cross References** | 8 |
| **ADR Coverage** | 017, 026, 030, 032, 039, 084, 086 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-15
**Mode:** Red Team · Human Mode · Truth Mode
