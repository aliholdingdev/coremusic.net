---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — Ecosystem Overview"
type: ecosystem
category: ecosystem
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Ecosystem Overview

**Toplam Servis:** 7
**Toplam Panel:** 10
**Toplam Subdomain:** 11
**Toplam Veritabanı:** 18 BCNF

---

## 1. Servis Haritası

| # | Servis | Port | Stack | Durum |
|---|--------|------|-------|-------|
| 1 | Control Service | 81 | PHP 8.4 | IMPLEMENTED |
| 2 | Media Service | 5000/6000 | PHP + FFmpeg | IMPLEMENTED |
| 3 | Audio Service | 9741/9742 | C++20 JUCE | PLANNED |
| 4 | Device Service | — | C++20 | PLANNED |
| 5 | Network Audio | — | C++20 | PLANNED |
| 6 | AI Service | — | PHP + Python | PLANNED |
| 7 | Download Service | 3001 | Node.js + TS | IMPLEMENTED |

---

## 2. Panel Haritası

| # | Panel | Subdomain | Durum |
|---|-------|-----------|-------|
| 1 | Landing | coremusic.net | ✅ |
| 2 | Music | music.coremusic.net | ✅ |
| 3 | Admin | admin.coremusic.net | ✅ |
| 4 | Download | download.coremusic.net | ✅ |
| 5 | Media | media.coremusic.net | ✅ |
| 6 | Auth | auth.coremusic.net | ✅ |
| 7 | Home | home.coremusic.net | ✅ |
| 8 | Car | car.coremusic.net | ✅ |
| 9 | Studio | studio.coremusic.net | ✅ |
| 10 | Pro | pro.coremusic.net | ✅ |

---

## 3. Servis İletişim Akışı

```
Client → API Gateway (api.coremusic.net)
              ↓
         Middleware Pipeline (K7)
              ↓
         Control Service (K8-1)
              ↓
         Event Bus (PSR-14)
         ┌────┼────┬────┬────┐
         ↓    ↓    ↓    ↓    ↓
      Media Audio Device AI Download
       (K8-2) (K8-3) (K8-4) (K8-6) (K8-7)
              ↓
         MySQL 18 DB (K5)
              ↓
         Redis Cache (K5)
              ↓
         Response → Client
```

---

*Ecosystem Overview v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
*Mode: Red Team · Human Mode · Truth Mode*
