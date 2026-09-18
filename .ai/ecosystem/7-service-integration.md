---
type: ecosystem
category: service-integration
title: "7-Service Integration â€” CoreMusic Servis Ekosistemi"
date: 2026-09-19
updated: 2026-09-19
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team Â· Human Mode Â· Truth Mode
reference:
  authority: ".ai/ecosystem/7-service-integration.md"
  architecture_master: ".ai/architecture/master-architecture-index.md"
  adr:
    - "decisions/accepted/ADR-039-7-service-platform-architecture"
    - "decisions/accepted/ADR-032-ipc-contract-versioning"
    - "decisions/accepted/ADR-084-api-gateway-architecture"
    - "decisions/accepted/ADR-086-event-driven-architecture"
---

# 7-Service Integration â€” CoreMusic Servis Ekosistemi

**Ä°lgili ADR:** [[decisions/accepted/ADR-039-7-service-platform-architecture]] Â· [[decisions/accepted/ADR-032-ipc-contract-versioning]] Â· [[decisions/accepted/ADR-084-api-gateway-architecture]] Â· [[decisions/accepted/ADR-086-event-driven-architecture]]

**Zorunlu BaÄŸlantÄ±lar:** [[CLAUDE.md]] Â· [[AGENTS.md]] Â· [[architecture/master-architecture-index]]

---

## 1. AmaÃ§

CoreMusic'in 7 backend servisinin tam entegrasyon haritasÄ±, servisler arasÄ± iletiÅŸim protokolleri, baÄŸÄ±mlÄ±lÄ±k zincirleri, veri akÄ±ÅŸÄ± ve "IMPLEMENTED/PLANNED" durumlarÄ±nÄ± tanÄ±mlayan **Ana Servis Ekosistemi** dosyasÄ±dÄ±r. 

---

## 2. 7 Servis TanÄ±mÄ± ve Durum Matrisi (engine.md Â§9.2 Uyumlu)

| # | Servis | Port | Protocol | Stack | Sorumluluk | Durum | KanÄ±t Yolu |
|---|--------|------|----------|-------|------------|-------|------------|
| 1 | **Control Service** | 81 | HTTP | PHP 8.4 + PSR | Auth, session, RBAC, routing, middleware | **IMPLEMENTED** | `shared/src/`, `auth.coremusic.net/` |
| 2 | **Media Service** | 5000/6000 | HTTP/WS | PHP + FFmpeg | Library, metadata, streaming, encode/decode | **PLANNED** | (Kod henÃ¼z yok) |
| 3 | **Audio Service** | 9741/9742 | REST/WS | C++20 JUCE | Player, DSP, mixer, EQ, effects, 8.1 surround | **PLANNED** | `projects/NevaEngine/` (Draft) |
| 4 | **Device Service** | â€” | BLE/WiFi/USB | C++20 | Bluetooth, WiFi, USB connections, device sync | **PLANNED** | (Kod henÃ¼z yok) |
| 5 | **Network Audio** | â€” | WebRTC/P2P | C++20 | Streaming, multi-room, P2P, synchronization | **PLANNED** | (Kod henÃ¼z yok) |
| 6 | **AI Service** | â€” | Internal | PHP + Python | Recommendations, auto-download, EQ auto-tune | **PLANNED** | (Kod henÃ¼z yok) |
| 7 | **Download Service** | 3001 | HTTP/WS | Node.js + TS | YouTube/Deezer download, queue, anti-ban | **PLANNED** | `download.coremusic.net/` (KlasÃ¶r yok) |

---

## 3. Port HaritasÄ± ve AÄŸ KatmanlarÄ±

| Port | Servis | AÃ§Ä±klama |
|------|--------|----------|
| **80** | API Gateway / Admin | HTTP trafiÄŸi (Reverse Proxy ile 443'e yÃ¶nlendirilir) |
| **81** | Control Service | Dahili/Web Ã¼zerinden yÃ¶netim paneli |
| **443** | API Gateway | Ana HTTPS trafik giriÅŸ noktasÄ± |
| **3001** | Download Service | Node.js TS tabanlÄ± indirme servisi (WebSocket destekli) |
| **5000** | Media Service | Medya streaming ve metadata iÅŸlemleri |
| **9741** | Audio Service | C++ JUCE REST API kontrolcÃ¼ arayÃ¼zÃ¼ |
| **9742** | Audio Service | C++ JUCE WebSocket telemetry arayÃ¼zÃ¼ |

> Detay: [[ecosystem/network-architecture]] Â§3

---

## 4. Servis BaÄŸÄ±mlÄ±lÄ±k Matrisi

### 4.1 DoÄŸrudan BaÄŸÄ±mlÄ±lÄ±klar (Veri AkÄ±ÅŸÄ±)

```
Control (81) â”€â”€â†’ Auth DB (coremusic_auth)
    â”œâ”€â”€â†’ Media Service (5000)    â€” medya metadata istekleri
    â”œâ”€â”€â†’ Audio Service (9741)    â€” oynatma kontrolÃ¼ (Play/Pause)
    â””â”€â”€â†’ Download Service (3001) â€” indirme kuyruÄŸu durumu

Media (5000) â”€â”€â†’ Musics DB (coremusic_musics)
    â”œâ”€â”€â†’ Audio Service (9741)    â€” ses stream aktarÄ±mÄ±
    â”œâ”€â”€â†’ Download Service (3001) â€” yeni dosya alma/iÅŸleme
    â””â”€â”€â†’ AI Service             â€” otomatik etiketleme/Ã¶neri

Audio (9741) â”€â”€â†’ Device Service   â€” donanÄ±m baÄŸlantÄ±sÄ± (BLE/I2S)
    â”œâ”€â”€â†’ Network Audio           â€” multi-room (WebRTC)
    â””â”€â”€â†’ Neva DB (coremusic_neva) â€” EQ preset / DSP profilleri

Download (3001) â”€â”€â†’ Musics DB (coremusic_musics)
    â”œâ”€â”€â†’ Media Service (5000)    â€” tamamlanan dosyayÄ± aktarma
    â”œâ”€â”€â†’ Catalog DB (coremusic_catalog) â€” metadata Ã§ekme
    â””â”€â”€â†’ Download DB (coremusic_download) â€” baÄŸÄ±msÄ±z kuyruk

AI Service â”€â”€â†’ Musics DB (coremusic_musics)
    â”œâ”€â”€â†’ User DB (coremusic_user) â€” dinleme geÃ§miÅŸi ve tercihler
    â”œâ”€â”€â†’ AI DB (coremusic_ai)     â€” model Ã§Ä±ktÄ±larÄ± / Ã¶nbellek
    â””â”€â”€â†’ Control Service (81)     â€” notification / UI gÃ¼ncellemeleri

Device Service â”€â”€â†’ Wireless DB (coremusic_wireless)
    â””â”€â”€â†’ Audio Service (9742)     â€” ses yÃ¶nlendirme komutlarÄ±

Network Audio â”€â”€â†’ Audio Service (9742)
    â””â”€â”€â†’ Wireless DB (coremusic_wireless)
```

### 4.2 BaÄŸÄ±mlÄ±lÄ±k Matrisi (Tablo)

| Kaynak â†“ / Hedef â†’ | Control | Media | Audio | Device | Network | AI | Download |
|---------------------|---------|-------|-------|--------|---------|-----|----------|
| **Control** | â€” | âœ… | âœ… | âŒ | âŒ | âœ… | âœ… |
| **Media** | âœ… | â€” | âœ… | âŒ | âŒ | âœ… | âœ… |
| **Audio** | âŒ | âœ… | â€” | âœ… | âœ… | âŒ | âŒ |
| **Device** | âŒ | âŒ | âœ… | â€” | âŒ | âŒ | âŒ |
| **Network Audio** | âŒ | âŒ | âœ… | âŒ | â€” | âŒ | âŒ |
| **AI** | âœ… | âœ… | âŒ | âŒ | âŒ | â€” | âŒ |
| **Download** | âŒ | âœ… | âŒ | âŒ | âŒ | âŒ | â€” |

---

## 5. Ä°letiÅŸim Protokolleri

| Ä°letiÅŸim TÃ¼rÃ¼ | Protokol | KullanÄ±m Ã–rneÄŸi |
|---------------|----------|-----------------|
| **Senkron RPC** | REST / gRPC | Control Service'in Audio Service'e "Play" komutu gÃ¶ndermesi. |
| **Asenkron Event** | PSR-14 / Redis PubSub | Download Service'in "Dosya Ä°ndirildi" event'i yayÄ±nlamasÄ±. |
| **GerÃ§ek ZamanlÄ±** | WebSocket | Audio Service'ten UI'a VU Meter ve Spectrum verisi aktarÄ±mÄ± (Port 9742). |
| **Medya AkÄ±ÅŸÄ±** | HTTP DASH/HLS | Media Service'ten Browser'a mÃ¼zik aktarÄ±mÄ±. |
| **DÃ¼ÅŸÃ¼k Gecikmeli**| WebRTC | Multi-room senkronize ses aktarÄ±mÄ± (Network Audio). |

---

## 6. Event Driven Architecture (ADR-086)

Servisler birbirini doÄŸrudan Ã§aÄŸÄ±rmaktan ziyade, **event** tabanlÄ± iletiÅŸimle (Loose Coupling) hareket eder:

```
Service A â†’ Event Bus (Redis / RabbitMQ / PSR-14) â†’ Service B, C, D
```

### 6.1 Temel Event TÃ¼rleri

| Event AdÄ± | YayÄ±nlayan | TÃ¼keten(ler) | AmaÃ§ |
|-----------|------------|--------------|------|
| `User.LoggedIn` | Control | AI, Media | Ã–neri modellerini tetikle, son Ã§alÄ±nanlarÄ± Ã¶nbelleÄŸe al |
| `Track.DownloadCompleted` | Download | Media, AI | DosyayÄ± kÃ¼tÃ¼phaneye ekle, analiz et |
| `Audio.PlaybackStateChanged` | Audio | Control, Network | UI gÃ¼ncellemesi, multi-room senkronu |
| `Device.Connected` | Device | Audio, Control | Ses Ã§Ä±kÄ±ÅŸÄ±nÄ± yeni cihaza yÃ¶nlendir, UI bildirimi |

---

## 7. BFF (Backend for Frontend) Mimarisi (ADR-084)

Her istemci tipi kendi kullanÄ±m senaryosuna gÃ¶re Ã¶zelleÅŸmiÅŸ bir BFF kullanÄ±r:

| Ä°stemci | BFF Tipi | Response KarakteristiÄŸi | Auth MekanizmasÄ± |
|---------|----------|-------------------------|------------------|
| SPA (`music.coremusic.net`) | SPA BFF | Tam veri setleri, iliÅŸkili tablolar | JWT + Kurabiye (Session) |
| Mobile App | Mobile BFF | Minimal payload, dÃ¼ÅŸÃ¼k bant geniÅŸliÄŸi | Salt JWT |
| Embedded (RPi5) | Embedded BFF | Ultra-minimal, JSON, gzip | Local (SQLite / Token) |
| Desktop App | Desktop BFF | Orta-YÃ¼ksek payload, donanÄ±m verisi | JWT + Kurabiye |
| Admin Panel | Admin BFF | Tam veri + audit loglarÄ±, istatistikler | JWT + RBAC (Admin) |
| Car Interface | Car BFF | Touch-optimized, iri font, dÃ¼ÅŸÃ¼k dikkat | Local (SQLite) |

---

## 8. CQRS AkÄ±ÅŸÄ±

Yazma ve okuma iÅŸlemleri ayrÄ±ÅŸtÄ±rÄ±lmÄ±ÅŸtÄ±r (ADR-086):

```
Yazma (Write): Command â†’ Use Case â†’ Repository â†’ MySQL Master
Okuma (Read): Query â†’ Read Model â†’ APCu / Redis Cache â†’ Response
```

### 8.1 CQRS Ã–rnek Senaryolar

| Ä°ÅŸlem Tipi | Alan | AkÄ±ÅŸ Ã–rneÄŸi |
|-----------|------|-------------|
| **Yazma** | Auth | `RegisterUserCommand` â†’ `AuthUseCase` â†’ `UserRepository` â†’ `coremusic_auth` DB |
| **Okuma** | MÃ¼zik | `GetPopularTracksQuery` â†’ `MusicReadModel` â†’ `APCu Cache` â†’ `Response` |
| **Yazma** | Ä°ndirme | `AddDownloadQueueCommand` â†’ `DownloadUseCase` â†’ `DownloadRepository` â†’ `coremusic_download` DB |
| **Okuma** | EQ | `GetPresetQuery` â†’ `NevaReadModel` â†’ `Redis Cache` â†’ `Response` |

---

## 9. VeritabanÄ± EÅŸleme (18 BCNF Ä°zolasyonu)

Her servis, belirlenmiÅŸ BCNF veritabanÄ± ÅŸemalarÄ±na (ADR-040) katÄ± bir ÅŸekilde baÄŸlÄ±dÄ±r:

| Servis | Yazma Yetkili DB'ler | Sadece Okuma DB'ler |
|--------|---------------------|---------------------|
| **Control** | coremusic_auth, coremusic_user, coremusic_system, coremusic_social | coremusic_catalog, coremusic_cms |
| **Media** | coremusic_musics, coremusic_albums, coremusic_catalog, coremusic_media | coremusic_download |
| **Audio** | coremusic_neva, coremusic_studio | coremusic_musics (Dosya okuma) |
| **Device** | coremusic_wireless, coremusic_media | - |
| **Network** | - | coremusic_wireless |
| **AI** | coremusic_ai, coremusic_playlist | coremusic_user, coremusic_musics |
| **Download** | coremusic_download | coremusic_musics, coremusic_catalog |

---

## 10. Middleware Pipeline (Frozen â€” 10 Katman)

TÃ¼m HTTP/Web servisleri aynÄ± gÃ¼venlik ve iÅŸleme hattÄ±ndan geÃ§mek zorundadÄ±r:

```
Request â†’ OriginCheck â†’ Cors â†’ RateLimiter â†’ SecurityHeaders â†’ SessionManager â†’ Csrf â†’ BypassAuth â†’ Auth â†’ Permission â†’ Validation â†’ Controller
```

| # | Middleware | Timeout/Limit | Ä°lgili KÄ±sÄ±t |
|---|-----------|---------------|--------------|
| 1 | OriginCheck | â€” | Strict Domain Whitelist |
| 2 | Cors | â€” | Wildcard (*) yasak |
| 3 | RateLimiter | 60 req/60s | APCu tabanlÄ± memory kÄ±sÄ±tÄ± |
| 4 | SecurityHeaders | â€” | HSTS, X-Frame, Dinamik CSP Nonce |
| 5 | SessionManager | 3600s TTL | Secure, HttpOnly, SameSite=Strict |
| 6 | Csrf | â€” | Mutation methodlarÄ± (POST/PUT) korumasÄ± |
| 7 | BypassAuth | Sadece Test | Prod ortamÄ±nda asla aktif edilemez |
| 8 | Auth | â€” | Argon2id Hash doÄŸrulama / JWT validasyon |
| 9 | Permission | â€” | RBAC role check (Admin/User/Pro) |
| 10| Validation | â€” | Request payload strict validation |

**Kritik Kural:** SÄ±ra kesinlikle DEÄÄ°ÅTÄ°RÄ°LEMEZ. Ã–rneÄŸin CSP nonce SecurityHeaders (#4) iÃ§inde Ã¼retilir ve SessionManager (#5) tarafÄ±ndan kaydedilir.

---

## 11. Servis BaÅŸlatma ve SaÄŸlÄ±k KontrolÃ¼

Sistem boot sekansÄ±, servislerin baÄŸÄ±mlÄ±lÄ±k sÄ±rasÄ±na gÃ¶re ayaÄŸa kalkmasÄ±nÄ± gerektirir:

1. VeritabanÄ± ve Redis (L0 - Infrastructure)
2. Control Service (L8 - Core Backend)
3. Media & Download Services (Medya Ä°ÅŸlemleri)
4. Audio & Device Services (DonanÄ±m/Ses ArayÃ¼zÃ¼)
5. AI Service (BaÄŸÄ±msÄ±z Ä°ÅŸleme)

> Servislerin `/health` endpoint'leri Ã¼zerinden (Ã¶rn. `http://localhost:81/health`) 5 durumlu (GREEN, YELLOW, ORANGE, RED, DEAD) saÄŸlÄ±k raporu Ã¼retmesi standarttÄ±r. (Bkz: [[ecosystem/service-health-check]])

---

## 12. Fallback Zincirleri (Hata Kurtarma)

Mikroservis mimarisindeki olasÄ± kopmalara karÅŸÄ± tolerans mekanizmalarÄ±:

| Bozulan Servis | Birincil Tepki | Fallback 1 | Fallback 2 |
|---------|----------|------------|------------|
| Control Service | MySQL session hatasÄ± | Redis/Local Cache | HTTP 503 Service Unavailable |
| Media Service | HTTP 503 | Yerel APCu Cache'den Metadata | Partial content (KapaksÄ±z listeleme) |
| Audio Service | ASIO Driver Ã‡Ã¶kmesi | WASAPI (Windows) veya ALSA (Linux) | Null Output (Sessiz iÅŸlem) |
| Download Service| HTTP 503 | Ä°ndirme isteklerini kuyruÄŸa at | Retry (Exponential Backoff) |
| AI Service | HTTP 503 | Ã–nceden hesaplanmÄ±ÅŸ/Default Ã¶neriler | Rastgele liste (Son Ã§are) |
| Device Service | BLE BaÄŸlantÄ± KopmasÄ± | WiFi Direct | USB Kablo |
| Network Audio | WebRTC DÃ¼ÅŸmesi | P2P Fallback | Local playback (Senkron kopmasÄ± kabul edilir) |

---

## 13. GÃ¼venlik KatmanlarÄ± Ã–zeti

- **Network:** Firewall (UFW/Windows Firewall), Ters Vekil (Nginx/Apache/IIS), DDoS koruma.
- **Transport:** Zorunlu TLS 1.3 (Ãœretim ortamÄ±), HSTS.
- **Application:** OWASP Top 10 korumalarÄ±, Argon2id, AES-256-GCM.
- **Data:** 18 BCNF ayrÄ±mÄ±, yetkisiz DB Ã§apraz eriÅŸiminin engellenmesi.

---

## 14. Scale & Performance (Performans Hedefleri)

| Performans MetriÄŸi | Optimizasyon Hedefi | Minimum Kabul Edilebilir |
|--------------------|---------------------|--------------------------|
| **TTFB (Time to First Byte)** | < 100ms | < 300ms |
| **API YanÄ±t SÃ¼resi** | < 50ms | < 150ms |
| **EÅŸ ZamanlÄ± KullanÄ±cÄ± (Node)** | 10,000+ | 1,000 |
| **Ä°ndirme HÄ±zÄ±** | Gigabit (Hat limitinde) | 10MB/s |
| **Ses Gecikmesi (ASIO C++)** | < 2ms | < 5ms |
| **Ses Gecikmesi (WASAPI C++)** | < 5ms | < 15ms |
| **Cache Hit OranÄ±** | > %95 | > %80 |

---

## 15. Deployment (DaÄŸÄ±tÄ±m) ModlarÄ±

| DaÄŸÄ±tÄ±m Tipi | Aktif Servis Profili | VeritabanÄ± AltyapÄ±sÄ± |
|--------------|----------------------|----------------------|
| **Home Media Center** | Control, Media, Audio, AI | MySQL 9 (18 BCNF) - Yerel Sunucu |
| **Car Audio System** | Control, Audio, Device | SQLite (Tek Dosya, DÃ¼ÅŸÃ¼k Ayak Ä°zi) |
| **Professional Studio** | TÃ¼m servisler (Network Audio aÄŸÄ±rlÄ±klÄ±) | MySQL 9 (18 BCNF) - YÃ¼ksek I/O |
| **NAS Audio Server** | Control, Media, Download | MySQL (NAS Container) |
| **DAC Control System** | Sadece Control, Audio, Device | SQLite (Embedded) |

---

## 16. Troubleshooting (Sorun Giderme)

| Hata / Sorun | Belirti | Ã‡Ã¶zÃ¼m Yolu |
|--------------|---------|------------|
| Control Service Unavailable | ArayÃ¼zde `401 Unauthorized` veya `403 Forbidden` | MySQL servisinin ve Control (Port 81) servisini dinlediÄŸini kontrol et. |
| Media Service Timeout | AlbÃ¼m kapaklarÄ± gelmiyor, metadata yavaÅŸ | FFmpeg loglarÄ±nÄ± kontrol et, APCu cache temizle. |
| Audio Service Sessizlik | UI Ã§alÄ±yor gibi gÃ¶steriyor ancak ses yok | ASIO/WASAPI driver kilitlenmesi; Audio Service (9741) yeniden baÅŸlat. |
| Download Service Stuck | Ä°ndirme kuyruÄŸu %0'da takÄ±lÄ± | Node.js TS loglarÄ±; YouTube/Deezer API key veya ban durumu kontrolÃ¼. |
| AI Service YavaÅŸlÄ±ÄŸÄ± | Ã–neriler ekranÄ± yÃ¼klenmiyor | Python ML Model yÃ¼klenmesini veya veritabanÄ± I/O durumunu incele. |
| Device Service KopmasÄ± | Bluetooth cihazlar UI'da gÃ¶rÃ¼nmÃ¼yor | BLE cihaz yetkileri ve C++ donanÄ±m servis loglarÄ±. |
| Network Audio Senkronizasyonu | Odalar arasÄ± yankÄ±/gecikme | WebRTC baÄŸlantÄ± metrikleri ve NTP (Network Time Protocol) senkronu kontrol. |

---

## 17. Quality Report & Onay Matrisi

| Metrik | DeÄŸer |
|--------|-------|
| **Version** | 3.0.0 (Faz 3 Refactoring) |
| **Status** | Red Team Â· Human Mode Â· Truth Mode |
| **BÃ¶lÃ¼m SayÄ±sÄ±** | 17 |
| **Service Count** | 7 (Durum matrisleriyle geniÅŸletilmiÅŸ) |
| **Port Count** | 8 |
| **Protocol Count** | 5 (HTTP, WS, gRPC, IPC, WebRTC) |
| **DB Mapping** | 18 BCNF tam uyumlu |
| **Fallback Chains** | 7 |
| **Event Types** | Temel 4 Event Grubu |
| **BFF Types** | 6 Ä°stemci profili |
| **DaÄŸÄ±tÄ±m ModlarÄ±** | 5 |
| **Troubleshooting** | 7 kritik senaryo |
| **ADR KapsamÄ±** | 017, 026, 030, 032, 039, 040, 084, 086 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
**Mode:** Red Team Â· Human Mode Â· Truth Mode

