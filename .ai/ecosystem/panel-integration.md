---
type: ecosystem
category: panel-integration
title: "Panel Integration â€” CoreMusic 10-Panel Entegrasyonu"
date: 2026-09-19
updated: 2026-09-19
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team Â· Human Mode Â· Truth Mode
reference:
  authority: ".ai/ecosystem/panel-integration.md"
  adr:
    - "decisions/accepted/ADR-004-multi-domain-spa"
    - "decisions/accepted/ADR-044-dynamic-user-theme-engine"
    - "decisions/accepted/ADR-045-multi-domain-view-mode-architecture"
    - "decisions/accepted/ADR-084-api-gateway-architecture"
---

# Panel Integration â€” CoreMusic 10-Panel Entegrasyonu

**Ä°lgili ADR:** [[decisions/accepted/ADR-004-multi-domain-spa]] Â· [[decisions/accepted/ADR-044-dynamic-user-theme-engine]] Â· [[decisions/accepted/ADR-045-multi-domain-view-mode-architecture]] Â· [[decisions/accepted/ADR-084-api-gateway-architecture]]

**Zorunlu BaÄŸlantÄ±lar:** [[CLAUDE.md]] Â· [[ecosystem/7-service-integration]] Â· [[architecture/master-architecture-index]]

---

## 1. AmaÃ§

Bu belge, CoreMusic platformunda bulunan 10 farklÄ± panelin arka uÃ§ (backend) servislerle nasÄ±l entegre olduÄŸunu, UI'dan DB'ye uzanan API Gateway akÄ±ÅŸÄ±nÄ±, tema motorunu ve "IMPLEMENTED/PLANNED" teknoloji durumlarÄ±nÄ± detaylandÄ±rÄ±r.

---

## 2. 10 Panel â€” Servis EÅŸleme Matrisi ve DurumlarÄ±

| # | Panel (Subdomain) | Hedef Kitle | Stack | Durum | Backend Servisleri |
|---|-------------------|-------------|-------|-------|--------------------|
| 1 | **music.coremusic.net** | Standart KullanÄ±cÄ± | Vanilla JS | **PLANNED** | Control, Media, Audio, AI, Download, Auth |
| 2 | **admin.coremusic.net** | Sistem YÃ¶neticisi | PHP 8.4 | **PLANNED** | Control, Media, Download, Auth |
| 3 | **download.coremusic.net** | Ä°ndirme YÃ¶neticisi | Node.js TS | **PLANNED** | Control, Media, Download, Auth |
| 4 | **media.coremusic.net** | KÃ¼tÃ¼phane / API | PHP 8.4 | **PLANNED** | Control, Media, Audio, AI, Auth |
| 5 | **auth.coremusic.net** | Kimlik DoÄŸrulama | PHP 8.4 | **IMPLEMENTED** | Control, Auth |
| 6 | **home.coremusic.net** | Ev/Kiosk (RPi5) | PHP 8.4 | **IMPLEMENTED** | Control, Media, Audio, Device, AI, Auth |
| 7 | **car.coremusic.net** | AraÃ§ Ä°Ã§i (RPi5) | Vanilla JS | **PLANNED** | Control, Media, Audio, Device, AI, Auth |
| 8 | **studio.coremusic.net**| StÃ¼dyo MÃ¼zisyeni | Vanilla JS | **PLANNED** | Control, Media, Audio, Device, AI, Auth |
| 9 | **pro.coremusic.net** | Pro KullanÄ±cÄ± | Vanilla JS | **PLANNED** | Control, Media, Audio, Device, AI, Auth |
| 10| **coremusic.net** | ZiyaretÃ§i (Landing)| Statik HTML | **IMPLEMENTED** | HiÃ§biri (Salt Statik) |

---

## 3. Panel Kategorileri ve AÄŸ DaÄŸÄ±lÄ±mÄ±

### 3.1 Web Panelleri (Cloud â€” MySQL 9)

KullanÄ±cÄ±larÄ±n genel internet Ã¼zerinden (Cloud) eriÅŸtiÄŸi ve merkezi doÄŸrulama (SSO / JWT) kullandÄ±ÄŸÄ± paneller:

| Panel | Port / Gateway | Stack | Auth Stratejisi |
|-------|----------------|-------|-----------------|
| music.coremusic.net | 443 | Vanilla JS SPA | Cross-subdomain JWT + HttpOnly Cookie |
| admin.coremusic.net | 443 | PHP 8.4 SSR | JWT + RBAC (Admin/SuperAdmin) |
| coremusic.net | 443 | HTML/CSS/JS | Yok (Public) |

### 3.2 Backend & API Panelleri

Sistem bileÅŸenlerinin ve 3. parti API'lerin etkileÅŸimde olduÄŸu, doÄŸrudan UI sunmayan ancak veri saÄŸlayan paneller:

| Panel | Port / Gateway | Stack | Auth Stratejisi |
|-------|----------------|-------|-----------------|
| auth.coremusic.net | 443 | PHP 8.4 | Merkezi Login / SSO Endpoint'i |
| media.coremusic.net | 5000/6000 | PHP + FFmpeg | API Key / Token |
| download.coremusic.net| 3001 | Node.js + TS | API Key / Token |

### 3.3 Embedded Paneller (Edge / RPi5 â€” SQLite)

Lokal aÄŸda veya cihazÄ±n kendisinde Ã§alÄ±ÅŸan, kesintili internet baÄŸlantÄ±sÄ±na toleranslÄ± kiosk/donanÄ±m arayÃ¼zleri:

| Panel | Cihaz UyumluluÄŸu | Ã–zel DonanÄ±m ArayÃ¼zÃ¼ | DB YapÄ±sÄ± |
|-------|------------------|----------------------|-----------|
| home.coremusic.net | RPi5 + 7" Touch | Local Audio, Multi-room | SQLite (Offline First) |
| pro.coremusic.net | RPi5 + HDMI Ekran | XMOS XU316 USB DAC | SQLite (Offline First) |
| studio.coremusic.net | RPi5 + 8.1 Setup | PCM3168A 8-Kanal Ã‡Ä±kÄ±ÅŸ | SQLite (Offline First) |
| car.coremusic.net | RPi5 + AraÃ§ Teybi | Bluetooth A2DP, CAN Bus | SQLite (Offline First) |

---

## 4. API Gateway AkÄ±ÅŸÄ± (BFF Mimarisi)

Panelden gelen isteklerin backend servislerine yÃ¶nlendirilmesi (ADR-084):

```text
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚ 1. TarayÄ±cÄ± / Ä°stemci UygulamasÄ±                           â”‚
â”‚                                                             â”‚
â”‚  music.coremusic.net   admin.coremusic.net   home.core     â”‚
â”‚  car.coremusic.net     studio.coremusic.net  pro.core      â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
              â”‚ (HTTPS GET/POST / WS)         â”‚
              â–¼                               â–¼
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚ 2. API Gateway (Reverse Proxy - Nginx / Apache / IIS)      â”‚
â”‚                                                             â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚ SSL/TLS  â”‚  â”‚ YÃ¶nlendirâ”‚  â”‚ RateLimitâ”‚  â”‚ CORS / HSTSâ”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜  â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜  â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜  â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜  â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
        â”‚ (FastCGI / HTTP Proxy)      â”‚
        â–¼                             â–¼
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚ 3. Backend Servisler (PHP 8.4 / Node.js 20 / C++20)        â”‚
â”‚                                                             â”‚
â”‚  Control (81)  Media (5000)  Audio (9741)  Download (3001) â”‚
â”‚  AI (Internal) Device (BLE)  Network (P2P)                 â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

---

## 5. View Mode (GÃ¶rÃ¼nÃ¼m ModlarÄ±) (ADR-045)

CoreMusic UI, cihaz boyutundan baÄŸÄ±msÄ±z olarak kullanÄ±cÄ±nÄ±n niyetine gÃ¶re deÄŸiÅŸen 3 ana *View Mode* iÃ§erir:

| Mod | Ã–ncelikli Hedef Kitle | Karakteristik Ã–zellikleri | Desteklenen Paneller |
|-----|-----------------------|---------------------------|----------------------|
| **Home** | Ev KullanÄ±cÄ±larÄ± | BÃ¼yÃ¼k butonlar, basit kontroller, yÃ¼ksek contrast, kapak gÃ¶rselleri Ã¶n planda. | music, media, home |
| **Pro** | GeliÅŸmiÅŸ/Power Users | YoÄŸun bilgi, listeler, playlist yÃ¶netimi, metadata dÃ¼zenleme. | music, media, admin, pro |
| **Studio**| Ses MÃ¼hendisleri / DJ | 31-Band Parametrik EQ, 8.1 kanal Matrix mikser, VU metreler. | music, media, studio, pro |

---

## 6. Dinamik Tema Motoru (ADR-044)

Panel tasarÄ±mlarÄ± ITCSS ve BEM metodolojisi kullanÄ±larak modÃ¼ler tutulmuÅŸtur. Renk paleti CSS Variable'larÄ± Ã¼zerinden dinamik olarak yÃ¼klenir:

| Tercih (Gender/Vibe) | Tema Ana Rengi (Primary) | UygulandÄ±ÄŸÄ± CSS Variable |
|----------------------|--------------------------|--------------------------|
| **Female / Pink** | `#ff69b4` (Hot Pink) | `--theme-primary: #ff69b4` |
| **Male / Blue** | `#4169e1` (Royal Blue) | `--theme-primary: #4169e1` |
| **Neutral / Dark** | `#6c757d` (Slate Gray) | `--theme-primary: #6c757d` |

> *Temalar kullanÄ±cÄ± profilinden (`coremusic_user` DB) yÃ¼klenir ve Control Service tarafÄ±ndan UI'a JWT Payload veya HTTP Cookie aracÄ±lÄ±ÄŸÄ±yla inject edilir.*

---

## 7. Panel-Servis API KullanÄ±mÄ± (Ã–rnek Endpointler)

Her panelin kullandÄ±ÄŸÄ± yetki setleri ve endpoint alanlarÄ±:

| Panel | En Ã‡ok KullanÄ±lan API Endpoint'leri | Yetki SÄ±nÄ±rÄ± |
|-------|-------------------------------------|--------------|
| **music** | `/api/v1/tracks`, `/api/v1/albums`, `/api/v1/playlists`, `/api/v1/playback` | `ROLE_USER` |
| **admin** | `/api/v1/users`, `/api/v1/system`, `/api/v1/logs`, `/api/v1/audit` | `ROLE_ADMIN` |
| **download** | `/api/v1/downloads`, `/api/v1/queue`, `/api/v1/sources` | `ROLE_SERVICE` / API Key |
| **media** | `/api/v1/library`, `/api/v1/metadata`, `/api/v1/stream` | `ROLE_SERVICE` / API Key |
| **home** | `/api/v1/tracks`, `/api/v1/playback`, `/api/v1/eq` | Lokal / SQLite Auth |
| **car** | `/api/v1/playback`, `/api/v1/bluetooth`, `/api/v1/navigation` | Lokal / SQLite Auth |
| **studio** | `/api/v1/tracks`, `/api/v1/playback`, `/api/v1/surround`, `/api/v1/eq` | Lokal / SQLite Auth |
| **pro** | `/api/v1/tracks`, `/api/v1/playback`, `/api/v1/eq`, `/api/v1/routing` | Lokal / SQLite Auth |

---

## 8. Embedded Panel DetayÄ± (RPi5 Offline-First Mimari)

Volumio benzeri donanÄ±ma entegre panellerin yaÅŸam dÃ¶ngÃ¼sÃ¼:

1. **Boot:** RPi5 aÃ§Ä±lÄ±r, C++ Audio/Device servisleri baÅŸlar.
2. **Kiosk Mode:** Chromium/Webkit tarayÄ±cÄ± tam ekranda baÅŸlatÄ±lÄ±r (`http://localhost`).
3. **Local Auth:** `home.coremusic.net` backend'i (SQLite) Ã¼zerinden yerel kimlik doÄŸrulama yapÄ±lÄ±r.
4. **Cloud Sync:** EÄŸer internet varsa, `auth.coremusic.net` ile iletiÅŸim kurulup metadata/token senkronize edilir. Aksi takdirde %100 Ã§evrimdÄ±ÅŸÄ± Ã§alÄ±ÅŸmaya devam eder.

---

## 9. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Version** | 2.0.0 (Faz 3 Refactoring) |
| **Status** | Red Team Â· Human Mode Â· Truth Mode |
| **Panel SayÄ±sÄ±** | 10 |
| **Panel Kategorileri** | 3 (Web, Service, Embedded) |
| **GÃ¶rÃ¼nÃ¼m ModlarÄ±** | 3 (Home, Pro, Studio) |
| **Tema VaryantlarÄ±** | 3 (Pink, Blue, Dark) |
| **ADR KapsamÄ±** | 004, 044, 045, 084 |
| **KanÄ±tlar** | `auth.coremusic.net/` ve `home.coremusic.net/` dosyalarÄ± kodda IMPLEMENTED olarak doÄŸrulanmÄ±ÅŸtÄ±r. DiÄŸerleri PLANNED aÅŸamasÄ±ndadÄ±r. |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
**Mode:** Red Team Â· Human Mode Â· Truth Mode

---

## Faz 3 DoÃ„Å¸rulamasÃ„Â±: Servis Durum Matrisi

| Servis | Entegrasyon Durumu | KanÃ„Â±t / AÃƒÂ§Ã„Â±klama |
|--------|--------------------|------------------|
| Control Service | **IMPLEMENTED** | shared/src/, uth.coremusic.net/ aktif |
| Media Service | **PLANNED** | TasarÃ„Â±m aÃ…Å¸amasÃ„Â±nda |
| Audio Service | **PLANNED** | C++ NevaEngine taslak |
| Device Service | **PLANNED** | DonanÃ„Â±m (I2S/BLE) beklemede |
| Network Audio | **PLANNED** | WebRTC mimarisi ÃƒÂ§izildi |
| AI Service | **PLANNED** | Python entegrasyonu planlandÃ„Â± |
| Download Service | **PLANNED** | Node.js servis klasÃƒÂ¶rÃƒÂ¼ yok |

