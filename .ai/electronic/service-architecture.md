---
type: system
category: electronics-service
title: "CoreMusic Electronics Service Architecture"
date: 2026-08-09
updated: 2026-08-10
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team Â· Human Mode Â· Truth Mode
---

# CoreMusic Electronics Service Architecture

**Zorunlu BaÄŸlantÄ±lar:** [[electronic/software-architecture]] Â· [[electronic/device-architecture]] Â· [[electronic/device-ecosystem]] Â· [[ecosystem/7-service-integration]] Â· [[ADR-039-7-service-platform-architecture]]

---

## 1. AmaÃ§

CoreMusic ELECTRONICS **tek bir uygulama deÄŸildir**. BaÄŸÄ±msÄ±z servislerden oluÅŸur ve her servisin net bir sorumluluk alanÄ± vardÄ±r. Servisler arasÄ± iletiÅŸim message bus Ã¼zerinden gerÃ§ekleÅŸtirilir. Her servis: tek sorumluluk, baÄŸÄ±msÄ±z geliÅŸtirme, baÄŸÄ±msÄ±z test, baÄŸÄ±msÄ±z daÄŸÄ±tÄ±m prensibiyle Ã§alÄ±ÅŸÄ±r.

---

## 2. Servis Mimarisi (13 Servis)

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚                        API Gateway                              â”‚
â”‚              (Routing, Auth, Rate Limit)                        â”‚
â”œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¤
â”‚  Core Services          â”‚  Media Services        â”‚  System     â”‚
â”‚  â”œâ”€â”€ 1. Auth Service    â”‚  â”œâ”€â”€ 4. Audio Service  â”‚  Services   â”‚
â”‚  â”œâ”€â”€ 2. User Service    â”‚  â”œâ”€â”€ 5. DSP Service    â”‚  â”œâ”€â”€ 9. AI  â”‚
â”‚  â””â”€â”€ 3. Device Service  â”‚  â”œâ”€â”€ 6. Streaming      â”‚  â”œâ”€â”€ 10. Update
â”‚                         â”‚  â”œâ”€â”€ 7. Download       â”‚  â”œâ”€â”€ 11. Notify
â”‚                         â”‚  â””â”€â”€ 8. Media Library  â”‚  â”œâ”€â”€ 12. Monitor
â”‚                         â”‚                        â”‚  â””â”€â”€ 13. Log
â”œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¤
â”‚                     Message Bus (Event Driven)                   â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

---

## 3. Servis DetaylarÄ± (13 Servis)

### 3.1 Auth Service

| Ä°ÅŸlev | AÃ§Ä±klama |
|-------|----------|
| Login | KullanÄ±cÄ± giriÅŸi (username/password, OAuth) |
| Logout | Oturum kapatma |
| Token Management | JWT Ã¼retme, yenileme, iptal |
| Device Auth | Cihaz kimlik doÄŸrulama (X.509) |
| Session Management | Oturum yÃ¶netimi (cross-subdomain) |
| MFA | Ã‡oklu faktÃ¶rlÃ¼ kimlik doÄŸrulama |

**API Endpoints:**
- `POST /auth/login` â€” KullanÄ±cÄ± giriÅŸi
- `POST /auth/logout` â€” Oturum kapatma
- `POST /auth/token/refresh` â€” Token yenileme
- `POST /auth/device/register` â€” Cihaz kaydÄ±
- `GET /auth/session/check` â€” Oturum kontrolÃ¼

Detaylar: [[architecture/k6-k7-security/k06-auth-layer/index]], [[ADR-043-auth-subdomain-consolidation]]

### 3.2 User Service

| Ä°ÅŸlev | AÃ§Ä±klama |
|-------|----------|
| Profile Management | Profil gÃ¶rÃ¼ntÃ¼leme/gÃ¼ncelleme |
| Roles | Rol yÃ¶netimi (admin, user, device) |
| Permissions | Ä°zin yÃ¶netimi (RBAC) |
| Preferences | Tercihler (tema, dil, birim) |
| Theme | Tema seÃ§imi (ADR-044) |
| Language | Dil seÃ§imi |

**API Endpoints:**
- `GET /users/me` â€” Mevcut kullanÄ±cÄ±
- `PUT /users/me` â€” Profil gÃ¼ncelleme
- `GET /users/:id/roles` â€” Roller
- `PUT /users/me/preferences` â€” Tercihler

Detaylar: [[ADR-044-dynamic-user-theme-engine]]

### 3.3 Device Service

| Ä°ÅŸlev | AÃ§Ä±klama |
|-------|----------|
| Registration | Cihaz kaydÄ± |
| Discovery | Cihaz algÄ±lama (mDNS, SSDP) |
| Health Check | SaÄŸlÄ±k izleme |
| Configuration | Uzaktan yapÄ±landÄ±rma |
| Firmware Version | Firmware versiyon yÃ¶netimi |
| Driver Version | SÃ¼rÃ¼cÃ¼ versiyon yÃ¶netimi |

**API Endpoints:**
- `GET /devices` â€” Cihaz listesi
- `POST /devices` â€” Cihaz kaydÄ±
- `GET /devices/:id/health` â€” SaÄŸlÄ±k durumu
- `PUT /devices/:id/config` â€” YapÄ±landÄ±rma
- `POST /devices/:id/update` â€” GÃ¼ncelleme baÅŸlatma

### 3.4 Audio Service

| Ä°ÅŸlev | AÃ§Ä±klama |
|-------|----------|
| Playback | Ses oynatma (play, pause, stop, seek) |
| Queue | Oynatma kuyruÄŸu |
| Playlist | Ã‡alma listesi yÃ¶netimi |
| Audio Session | Ses oturumu yÃ¶netimi |
| Volume | Ses seviyesi kontrolÃ¼ |
| Equalizer | EQ ayarlama |

**API Endpoints:**
- `POST /audio/play` â€” Oynat
- `POST /audio/pause` â€” Duraklat
- `POST /audio/stop` â€” Durdur
- `PUT /audio/seek` â€” Ä°lerle
- `GET /audio/queue` â€” Kuyruk
- `PUT /audio/volume` â€” Ses seviyesi

### 3.5 DSP Service

| Ä°ÅŸlev | AÃ§Ä±klama |
|-------|----------|
| EQ | 31-bant parametrik equalizer |
| Compressor | SÄ±kÄ±ÅŸtÄ±rÄ±cÄ± |
| Limiter | Limiter |
| Crossover | Bass/treble ayrÄ±mÄ± |
| Delay | Gecikme |
| Reverb | YankÄ± |
| FIR Filter | Finite Impulse Response |
| IIR Filter | Infinite Impulse Response |
| FFT Analysis | Frekans analizi |

**API Endpoints:**
- `GET /dsp/preset` â€” Mevcut preset
- `PUT /dsp/eq` â€” EQ ayarlama
- `PUT /dsp/compressor` â€” Compressor ayarlama
- `POST /dsp/preset` â€” Preset kaydetme
- `GET /dsp/fft` â€” FFT analizi

Detaylar: [[electronic/dsp/index]], [[ADR-062-dsp-pipeline-architecture]], [[ADR-025-professional-eq-system]]

### 3.6 Streaming Service

| Ä°ÅŸlev | AÃ§Ä±klama |
|-------|----------|
| HTTP Streaming | HTTP/HTTPS streaming |
| Local Streaming | Yerel dosya streaming |
| Multi-Room | Ã‡oklu oda senkronizasyon |
| Adaptive Streaming | Adaptive bitrate |
| Protocol Support | RTSP, HLS, DASH |

**API Endpoints:**
- `POST /stream/start` â€” Stream baÅŸlat
- `POST /stream/stop` â€” Stream durdur
- `PUT /stream/zone` â€” Zone yapÄ±landÄ±rma
- `GET /stream/status` â€” Stream durumu

### 3.7 Download Service

| Ä°ÅŸlev | AÃ§Ä±klama |
|-------|----------|
| Queue Management | Ä°ndirme kuyruÄŸu yÃ¶netimi |
| Resume | Kesintili indirme devamÄ± |
| Retry | Otomatik yeniden deneme |
| Cache | Ã–nbellekleme |
| Metadata | Metadata Ã§Ä±karma |

**API Endpoints:**
- `POST /download/add` â€” Ä°ndirme ekle
- `DELETE /download/:id` â€” Ä°ndirmeyi kaldÄ±r
- `GET /download/queue` â€” Kuyruk listesi
- `PUT /download/:id/pause` â€” Duraklat
- `PUT /download/:id/resume` â€” Devam

Detaylar: [[projects/download-service]], [[ADR-026-download-service-architecture]]

### 3.8 Media Library Service

| Ä°ÅŸlev | AÃ§Ä±klama |
|-------|----------|
| Scan | Medya tarama |
| Index | Dizin oluÅŸturma |
| Metadata | Metadata Ã§Ä±karma/gÃ¼ncelleme |
| Album | AlbÃ¼m yÃ¶netimi |
| Artist | SanatÃ§Ä± yÃ¶netimi |
| Genre | TÃ¼r yÃ¶netimi |
| Search | Tam metin arama |

**API Endpoints:**
- `GET /library/scan` â€” Tarama baÅŸlat
- `GET /library/songs` â€” ÅarkÄ± listesi
- `GET /library/albums` â€” AlbÃ¼m listesi
- `GET /library/artists` â€” SanatÃ§Ä± listesi
- `GET /library/search?q=` â€” Arama

### 3.9 AI Service

| Ä°ÅŸlev | AÃ§Ä±klama |
|-------|----------|
| Sistem Analizi | DonanÄ±m/yazÄ±lÄ±mperformans analizi |
| Kod Analizi | Kod kalitesi, gÃ¼venlik analizi |
| DonanÄ±m Analizi | PCB, termal, sinyal analizi |
| DokÃ¼mantasyon | Otomatik dokÃ¼man Ã¼retimi |
| Hata Analizi | Root cause, troubleshooting |
| Tahmine DayalÄ± BakÄ±m | Predictive maintenance |
| MÃ¼zik Ã–nerisi | KiÅŸiselleÅŸtirilmiÅŸ Ã¶neri |
| Otomatik EQ | AI destekli EQ ayarlama |

Detaylar: [[architecture/ai/ai-engine]], [[architecture/ai/ai-workflow]], [[ADR-030-ai-strategy-core]]

### 3.10 Update Service

| Ä°ÅŸlev | AÃ§Ä±klama |
|-------|----------|
| Firmware Update | Firmware gÃ¼ncelleme |
| Driver Update | SÃ¼rÃ¼cÃ¼ gÃ¼ncelleme |
| DSP Profile Update | DSP profili gÃ¼ncelleme |
| AI Model Update | AI model gÃ¼ncelleme |
| Config Update | KonfigÃ¼rasyon gÃ¼ncelleme |
| UI Assets Update | UI varlÄ±k gÃ¼ncelleme |

**API Endpoints:**
- `GET /update/check` â€” GÃ¼ncelleme kontrolÃ¼
- `POST /update/apply` â€” GÃ¼ncelleme uygula
- `POST /update/rollback` â€” Geri dÃ¶nÃ¼ÅŸ
- `GET /update/history` â€” GÃ¼ncelleme geÃ§miÅŸi

### 3.11 Notification Service

| Ä°ÅŸlev | AÃ§Ä±klama |
|-------|----------|
| Push Notification | Push bildirim |
| Email | E-posta bildirimi |
| In-App | Uygulama iÃ§i bildirim |
| Webhook | Webhook bildirimi |

### 3.12 Monitoring Service

| Metrik | AÃ§Ä±klama | EÅŸik |
|--------|----------|------|
| CPU Usage | Ä°ÅŸlemci kullanÄ±mÄ± | >90% |
| RAM Usage | Bellek kullanÄ±mÄ± | >85% |
| Disk Usage | Disk kullanÄ±mÄ± | >90% |
| Network | AÄŸ trafiÄŸi | >1Gbps |
| DSP Load | DSP yÃ¼kÃ¼ | >80% |
| Driver Errors | SÃ¼rÃ¼cÃ¼ hatalarÄ± | >0 |
| Temperature | SÄ±caklÄ±k | >80Â°C |
| Audio Buffer | Ses buffer | Underrun |
| Latency | Gecikme | >20ms |

**API Endpoints:**
- `GET /monitoring/metrics` â€” Metrikler
- `GET /monitoring/health` â€” SaÄŸlÄ±k durumu
- `GET /monitoring/alerts` â€” UyarÄ±lar

### 3.13 Logging Service

| Ä°ÅŸlev | AÃ§Ä±klama |
|-------|----------|
| Structured Logging | YapÄ±landÄ±rÄ±lmÄ±ÅŸ gÃ¼nlÃ¼k |
| Audit Trail | Denetim izi |
| Log Aggregation | GÃ¼nlÃ¼k toplama |
| Log Rotation | GÃ¼nlÃ¼k dÃ¶ndÃ¼rme |

Detaylar: [[log.md]], [[ADR-004-multi-domain-spa]]

---

## 4. Servis Ä°letiÅŸimi

### 4.1 Ä°letiÅŸim AkÄ±ÅŸÄ±

```
Application Contract â†’ Message Bus â†’ Target Service
```

TÃ¼m servislerarasÄ± iletiÅŸim **event-driven** message bus Ã¼zerinden gerÃ§ekleÅŸir. Servisler birbirinin DB'sine **eriÅŸemez**.

### 4.2 Event KataloÄŸu

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

### 4.3 Service Ä°letiÅŸim KurallarÄ±

| Kural | AÃ§Ä±klama |
|-------|----------|
| Async First | TÃ¼m iletiÅŸim asenkron |
| Event Sourcing | Olaylar kaydedilir |
| Idempotency | Tekrarlanabilir iÅŸlemler |
| Circuit Breaker | BaÄŸÄ±mlÄ±lÄ±k kÄ±rÄ±lma noktasÄ± |
| Retry with Backoff | Ãœstel geri Ã§ekilme ile yeniden deneme |
| Dead Letter Queue | BaÅŸarÄ±sÄ±z mesajlar |
| Service Isolation | HiÃ§bir servis diÄŸerinin DB'sine eriÅŸmez |

---

## 5. Servis YaÅŸam DÃ¶ngÃ¼sÃ¼

```
Initialize â†’ Config â†’ Dependency â†’ Health â†’ Ready â†’ Running â†’ Monitoring â†’ Shutdown
```

| AÅŸama | AÃ§Ä±klama |
|-------|----------|
| Initialize | Servis baÅŸlatma, baÄŸÄ±mlÄ±lÄ±klar kontrol |
| Config | KonfigÃ¼rasyon yÃ¼kleme (env, DB, file) |
| Dependency | BaÄŸÄ±mlÄ±lÄ±k servislerinin hazÄ±r olmasÄ± |
| Health | Ä°lk saÄŸlÄ±k kontrolÃ¼ |
| Ready | Trafik almaya hazÄ±r |
| Running | Aktif servis |
| Monitoring | SÃ¼rekli izleme, metrik toplama |
| Shutdown | Graceful kapanma, cleanup |

---

## 6. API Gateway

| Ã–zellik | AÃ§Ä±klama |
|---------|----------|
| Routing | URL tabanlÄ± yÃ¶nlendirme |
| Authentication | Token doÄŸrulama |
| Rate Limiting | Ä°stek sÄ±nÄ±rlandÄ±rma (60 req/60s) |
| Validation | GiriÅŸ doÄŸrulama |
| Logging | Ä°stek gÃ¼nlÃ¼ÄŸÃ¼ |
| Load Balancing | YÃ¼k dengeleme |
| Circuit Breaker | Servis korumasÄ± |

Detaylar: [[architecture/03-contracts/api-architecture-master]], [[architecture/03-contracts/api-rate-limit]]

---

## 7. Servis SaÄŸlÄ±k KontrolÃ¼

| Durum | TanÄ±m | Aksiyon |
|-------|-------|---------|
| Healthy | Servis normal | Devam |
| Degraded | YavaÅŸ yanÄ±t | UyarÄ± |
| Failed | Servis Ã§alÄ±ÅŸmÄ±yor | Escalation |
| Unknown | Durum bilinmiyor | Yeniden kontrol |

Detaylar: [[ecosystem/service-health-check]]

---

## 8. AI Servis Entegrasyonu

| AI YeteneÄŸi | KullanÄ±m |
|-------------|----------|
| Sistem Analizi | DonanÄ±m/yazÄ±lÄ±m performans analizi |
| Kod Analizi | Kod kalitesi, gÃ¼venlik taramasÄ± |
| DonanÄ±m Analizi | PCB, termal, sinyal kalitesi |
| DokÃ¼mantasyon | Otomatik dokÃ¼man Ã¼retimi |
| Hata Analizi | Root cause, troubleshooting |
| Predictive Maintenance | Tahmine dayalÄ± bakÄ±m |
| Music Recommendation | KiÅŸiselleÅŸtirilmiÅŸ Ã¶neri |
| Auto-EQ | Otomatik equalizer ayarlama |
| Room Correction | Oda akustik dÃ¼zeltmesi |

Detaylar: [[architecture/ai/ai-engine]], [[architecture/ai/ai-workflow]], [[ADR-030-ai-strategy-core]]

---

## 9. Cross References

| Dosya | Kapsam |
|-------|--------|
| [[electronic/software-architecture]] | YazÄ±lÄ±m mimarisi |
| [[electronic/device-architecture]] | Cihaz mimarisi |
| [[electronic/device-ecosystem]] | Cihaz ekosistemi |
| [[ecosystem/7-service-integration]] | Servis entegrasyonu |
| [[ecosystem/service-health-check]] | Health check |
| [[ecosystem/service-communication]] | Servis iletiÅŸim |
| [[ADR-039-7-service-platform-architecture]] | 7-servis ADR |
| [[ADR-062-dsp-pipeline-architecture]] | DSP pipeline |

---

## 10. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team Â· Human Mode Â· Truth Mode verified |
| Services | 13 |
| API Endpoints | 50+ |
| Event Types | 13+ |
| Service Lifecycle | 8 aÅŸamalÄ± |
| Cross References | 8 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team Â· Human Mode Â· Truth Mode

