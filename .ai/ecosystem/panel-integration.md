---
type: ecosystem
category: panel-integration
title: "Panel Integration — CoreMusic 10-Panel Entegrasyonu"
date: 2026-08-15
updated: 2026-08-15
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ecosystem/panel-integration.md"
  adr:
    - "decisions/accepted/ADR-004-multi-domain-spa"
    - "decisions/accepted/ADR-044-dynamic-user-theme-engine"
    - "decisions/accepted/ADR-045-multi-domain-view-mode-architecture"
    - "decisions/accepted/ADR-084-api-gateway-architecture"
---

# Panel Integration — CoreMusic 10-Panel Entegrasyonu

**İlgili ADR:** [[decisions/accepted/ADR-004-multi-domain-spa]] · [[decisions/accepted/ADR-044-dynamic-user-theme-engine]] · [[decisions/accepted/ADR-045-multi-domain-view-mode-architecture]] · [[decisions/accepted/ADR-084-api-gateway-architecture]]

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[ecosystem/7-service-integration]] · [[architecture/00-overview/architecture-master]]

---

## 1. Amaç

10 panelin backend servislerle nasıl entegre olduğunu, hangi panellerin hangi servisleri kullandığını ve API Gateway üzerinden veri akışını tanımlar.

---

## 2. 10 Panel — Servis Eşleme Matrisi

| Panel | Control | Media | Audio | Device | AI | Download | Auth |
|-------|---------|-------|-------|--------|-----|----------|------|
| **music.coremusic.net** | ✅ | ✅ | ✅ | ❌ | ✅ | ✅ | ✅ |
| **admin.coremusic.net** | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ | ✅ |
| **download.coremusic.net** | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ | ✅ |
| **media.coremusic.net** | ✅ | ✅ | ✅ | ❌ | ✅ | ❌ | ✅ |
| **auth.coremusic.net** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| **home.coremusic.net** | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ |
| **car.coremusic.net** | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ |
| **studio.coremusic.net** | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ |
| **pro.coremusic.net** | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ✅ |
| **coremusic.net** (Landing) | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

---

## 3. Panel Kategorileri

### 3.1 Web Paneller (Cloud — MySQL)

| Panel | Subdomain | Port | Stack | Auth |
|-------|-----------|------|-------|------|
| music.coremusic.net | music | 81 | PHP 8.4 + Vanilla JS | Cross-subdomain JWT |
| admin.coremusic.net | admin | 80 | PHP 8.4 | RBAC (admin) |
| landing.coremusic.net | — | 80 | Vanilla JS | Yok |

### 3.2 Backend Servis Panelleri

| Panel | Subdomain | Port | Stack | Auth |
|-------|-----------|------|-------|------|
| auth.coremusic.net | auth | — | PHP 8.4 | Merkezi |
| media.coremusic.net | media | 5000/6000 | PHP + FFmpeg | API Key |
| download.coremusic.net | download | 3001 | Node.js + TS | API Key |
| api.coremusic.net | api | — | PHP 8.4 | JWT |

### 3.3 Embedded Paneller (RPi5 — SQLite)

| Panel | Subdomain | Donanım | Auth | DB |
|-------|-----------|---------|------|-----|
| home.coremusic.net | home | RPi5 + Touch Screen | Local | SQLite |
| pro.coremusic.net | pro | RPi5 + HDMI Display | Local | SQLite |
| studio.coremusic.net | studio | RPi5 + 8.1 Surround | Local | SQLite |
| car.coremusic.net | car | RPi5 + PCM3168A | Local | SQLite |

---

## 4. API Gateway Akışı

```
┌─────────────────────────────────────────────────────────────┐
│ Tarayıcı / Uygulama                                        │
│                                                             │
│  music.coremusic.net   admin.coremusic.net   home.core     │
│  car.coremusic.net     studio.coremusic.net  pro.core      │
└─────────────┬───────────────┬───────────────┬──────────────┘
              │               │               │
              ▼               ▼               ▼
┌─────────────────────────────────────────────────────────────┐
│ API Gateway (api.coremusic.net)                            │
│                                                             │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐  │
│  │ Routing  │  │ Auth     │  │ Rate     │  │ CORS     │  │
│  │          │  │ (JWT)    │  │ Limit    │  │          │  │
│  └────┬─────┘  └────┬─────┘  └────┬─────┘  └────┬─────┘  │
│       │              │              │              │         │
└───────┼──────────────┼──────────────┼──────────────┼─────────┘
        │              │              │              │
        ▼              ▼              ▼              ▼
┌─────────────────────────────────────────────────────────────┐
│ Backend Servisler                                          │
│                                                             │
│  Control (81)  Media (5000)  Audio (9741)  Download (3001) │
│  AI (internal) Device (BLE) Network (P2P)                  │
└─────────────────────────────────────────────────────────────┘
```

---

## 5. Embedded Panel Detayı (RPi5)

### 5.1 Volumio Benzeri Mimari

Home, Pro, Studio, Car panelleri **Raspberry Pi 5** üzerinde çalışır:
- Tarayıcı arayüzü **tam ekran** (kiosk mode)
- **Dokunmatik ekran** desteği (48px minimum touch target)
- **Yerel sunucu** modu (internet gerekmez)
- **SQLite** tabanlı yerel veritabanı
- İlk kurulumda `auth.coremusic.net`'e bağlanarak senkronizasyon

### 5.2 Embedded Auth Akışı

```
RPi5 (Home/Pro/Studio/Car)
  → Local Web Server (Apache, port 81)
    → PHP 8.4 + SQLite
      → Local Auth (standalone)
        → İlk kurulum: auth.coremusic.net'e bağlan
        → Sonraki: Offline-first (SQLite)
```

### 5.3 Embedded vs Web Auth

| Özellik | Web (Cloud) | Embedded (Local) |
|---------|-------------|------------------|
| Auth Sunucusu | auth.coremusic.net | Local (aynı RPi5) |
| Database | MySQL 9 (18 BCNF) | SQLite (1 DB) |
| JWT | RS256 (production) | HS256 (local) |
| Rate Limit | 60 req/60s | Devre dışı |
| Internet | Gerekli | Gerekmez (offline) |
| Multi-user | Evet | Tek kullanıcı |

---

## 6. View Mode (ADR-045)

| Mod | Hedef | Tema | Paneller |
|-----|-------|------|----------|
| **Home** | Ev kullanıcısı | Basit, büyük butonlar | music, media, home |
| **Pro** | Profesyonel | Gelişmiş kontrol | music, media, admin, pro |
| **Studio** | Stüdyo mühendisi | 8.1 surround, EQ | music, media, studio, pro |

---

## 7. Tema Motoru (ADR-044)

| Cinsiyet | Renk Token | CSS Custom Property |
|----------|------------|---------------------|
| Female | Pembe | `--theme-primary: #ff69b4` |
| Male | Mavi | `--theme-primary: #4169e1` |
| Neutral | Varsayılan | `--theme-primary: #6c757d` |

---

## 8. Panel-Servis API Kullanımı

| Panel | Çağrılan API Endpoint'leri |
|-------|---------------------------|
| **music** | `/api/v1/tracks`, `/api/v1/albums`, `/api/v1/playlists`, `/api/v1/playback` |
| **admin** | `/api/v1/users`, `/api/v1/system`, `/api/v1/logs` |
| **download** | `/api/v1/downloads`, `/api/v1/queue` |
| **media** | `/api/v1/library`, `/api/v1/metadata`, `/api/v1/stream` |
| **home** | `/api/v1/tracks`, `/api/v1/playback`, `/api/v1/eq` |
| **car** | `/api/v1/playback`, `/api/v1/bluetooth`, `/api/v1/navigation` |
| **studio** | `/api/v1/tracks`, `/api/v1/playback`, `/api/v1/surround`, `/api/v1/eq` |
| **pro** | `/api/v1/tracks`, `/api/v1/playback`, `/api/v1/eq`, `/api/v1/routing` |

---

## 9. Cross References

| Dosya | Amaç |
|-------|------|
| [[ecosystem/7-service-integration]] | Servis entegrasyonu |
| [[ecosystem/service-health-check]] | Sağlık kontrolü |
| [[architecture/04-panels]] | Panel mimarisi |
| [[architecture/l3-presentation]] | Frontend detay |
| [[architecture/03-contracts/api-architecture-master]] | API mimarisi |

---

## 10. Quality Report

| Metrik | Değer |
|--------|-------|
| **Version** | 1.0.0 |
| **Status** | Red Team · Human Mode · Truth Mode verified |
| **Panel Count** | 10 |
| **Panel Categories** | 3 (Web, Service, Embedded) |
| **View Modes** | 3 (Home, Pro, Studio) |
| **Theme Variants** | 3 (Female, Male, Neutral) |
| **ADR Coverage** | 004, 044, 045, 084 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-15
**Mode:** Red Team · Human Mode · Truth Mode
