---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K10 Uygulama Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K10: Uygulama Layer

**Katman:** K10 (Uygulama)
**Kapsam:** 10 Panel: Music, Home, Car, Studio, Admin, Download, Landing, Pro, Media, Auth
**Sorumlu Agent:** UI Designer
**Bileşen Sayısı:** 45

---

## 1. Genel Bakış

K10 katmanı, CoreMusic'in 10 web panelini içerir. Her panel bağımsız bir subdomain üzerinde çalışır.

---

## 2. 10 Panel Haritası

| # | Panel | Subdomain | Port | Stack | Durum |
|---|-------|-----------|------|-------|-------|
| 1 | Landing | coremusic.net | 80 | Vanilla JS | ✅ |
| 2 | Music | music.coremusic.net | 81 | PHP 8.4 + JS | ✅ |
| 3 | Admin | admin.coremusic.net | 80 | PHP 8.4 | ✅ |
| 4 | Download | download.coremusic.net | 3001 | Node.js + TS | ✅ |
| 5 | Media | media.coremusic.net | 5000/6000 | PHP + FFmpeg | ✅ |
| 6 | Auth | auth.coremusic.net | — | PHP 8.4 | ✅ |
| 7 | Home | home.coremusic.net | 81 | Vanilla JS | ✅ |
| 8 | Car | car.coremusic.net | — | Vanilla JS | ✅ |
| 9 | Studio | studio.coremusic.net | 81 | Vanilla JS | ✅ |
| 10 | Pro | pro.coremusic.net | 81 | Vanilla JS | ✅ |

---

## 3. Panel Detayları

### 3.1 Music Panel (music.coremusic.net:81)

```
Ana medya paneli. Kütüphane, albüm, sanatçı yönetimi.
  - Library view
  - Album browser
  - Artist browser
  - Playlist management
  - Search
  - Now playing
  - Footer player
```

### 3.2 Home Panel (home.coremusic.net:81)

```
Ev medya merkezi. RPi5 optimized.
  - Dashboard widgets
  - Recent tracks
  - Recommendations
  - Radio
  - Podcast
  - Multi-room control
```

### 3.3 Car Panel (car.coremusic.net)

```
Araç içi bilgi-eğlence. Touch-optimized.
  - Large touch targets
  - Minimal UI
  - Voice control
  - Navigation
  - Hands-free
```

### 3.4 Studio Panel (studio.coremusic.net:81)

```
Profesyonel stüdyo.
  - Multi-track view
  - Mixer
  - EQ visualization
  - Metering
  - Reference tracks
```

### 3.5 Admin Panel (admin.coremusic.net:80)

```
Yönetim paneli.
  - User management
  - System settings
  - Analytics
  - Logs
  - Backup management
```

### 3.6 Download Panel (download.coremusic.net:3001)

```
İndirme yönetimi.
  - Download queue
  - Source selection (Deezer/YouTube)
  - Quality settings
  - Cache management
```

---

## 4. 4-Tier Device Manager

| Tier | Cihazlar | Viewport | Layout |
|------|----------|----------|--------|
| Tier 1: Phone | PHONE | ≤767px | Tek sütun |
| Tier 2: Embedded | EMBEDDED, TABLET | ≤1024px | 42/58 split |
| Tier 3: Wide | LAPTOP, DESKTOP | 1025-2560px | 3-sütun |
| Tier 4: 4K | FOUR_K_TV, FOUR_K_MON | ≥2561px | 4K ölçekli |

---

## 5. SPA → ApiClient Kuralı

```
SPA → ApiClient → HTTP → Gateway → Middleware → Use Case → Domain → Repository → Infrastructure

SPA asla PDO, MySQL, Repository, Entity, Infrastructure, Filesystem,
FFmpeg, Redis, Cache veya SQL GÖRMEZ.
```

---

## 6. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-039 | 7-servis platform mimarisi |
| ADR-045 | Multi-domain view mode |
| ADR-046 | Cross-view state koruma |

---

*K10 Uygulama Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
*Mode: Red Team · Human Mode · Truth Mode*
