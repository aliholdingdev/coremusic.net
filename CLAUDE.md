# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

---

## ⚠️ BU DOSYA BİR İŞARETÇİDİR (POINTER FILE) — ADR-042 (2026-08-03)

**Kök `CLAUDE.md` artık sadece bootstrap işaretçisidir. Asıl AI talimatı kanonik olarak `.ai/CLAUDE.md` içindedir.**

**Root `CLAUDE.md` is now a bootstrap pointer only. The canonical AI mandate lives in `.ai/CLAUDE.md`.**

---

## 10-Step AI Boot Protocol (ADR-042/C8)

Tüm AI asistanları her yeni görevde bu 10 dosyayı okumalıdır:

| # | Dosya | Amaç |
|---|-------|------|
| 1 | `.ai/CLAUDE.md` | Kanonik AI talimatı |
| 2 | `.ai/AGENTS.md` | Agent kayıt defteri |
| 3 | `.ai/WORKFLOW.md` | Süreçler |
| 4 | `.ai/index.md` | Master katalog |
| 5 | `.ai/keys.md` | Anahtar kelime haritası |
| 6 | `.ai/AGENTS.md` | Agent yetkileri (tekrar) |
| 7 | `.ai/brain.md` | Mimari kararlar |
| 8 | `.ai/MEMORY.md` | Oturum hafızası |
| 9 | `.ai/log.md` | Aktivite günlüğü |
| 10 | `.claude/rules/*` | Tüm kurallar (CSS, JS, PHP, DB, Security, Test) |

Madde 11-14 operasyonel alt-adımlar (ADR-042/C8).

---

## Quick Rules

| Kural | Özet | Kaynak |
|-------|------|--------|
| Zero Code Before Plan | Planlama onaylanmadan kod yok | [[.ai/WORKFLOW.md]] |
| Single Source of Truth | Bilgi sadece `.ai/` vault'tan | [[.ai/CLAUDE.md]] |
| MSA Limit = 15 dosya | Görev başına max 15 dosya | [[.ai/AGENTS.md]] |
| Mandatory 5 Skills | Prompt-maker, brainstorming, vault-sync, hallucination-control, Red Team | [[.ai/AGENTS.md]] |
| No Hallucination | Doğrulanamayan bilgi → `VERIFICATION REQUIRED` | [[.ai/CLAUDE.md]] |
| In-Place Refactoring | Dosya adı/yolu değişmez | [[.ai/WORKFLOW.md]] |

---

## 10 Panel & 7 Backend Service

### 10 Panel (Frontend)

| Panel | Port | Stack | Durum |
|-------|------|-------|-------|
| `music.coremusic.net` | 81 | PHP 8.4 + Vanilla JS | ✅ Ana medya paneli |
| `admin.coremusic.net` | 80 | PHP 8.4 | ✅ Yönetim paneli |
| `download.coremusic.net` | 3001 | Node.js + TypeScript | ✅ İndirme servisi |
| `media.coremusic.net` | 5000/6000 | PHP + FFmpeg | ✅ Medya depolama |
| `auth.coremusic.net` | — | PHP 8.4 | ✅ Kimlik doğrulama |
| `home.coremusic.net` | — | Vanilla JS | ✅ Ev medya merkezi |
| `car.coremusic.net` | — | Vanilla JS | ✅ Araç içi bilgi-eğlence |
| `studio.coremusic.net` | — | Vanilla JS | ✅ Profesyonel stüdyo |
| `pro.coremusic.net` | — | Vanilla JS | ✅ Profesyonel panel |
| `coremusic.net` | — | Vanilla JS | ✅ Landing page |

### 7 Backend Service (Runtime)

| Service | Port | Protocol | Stack |
|---------|------|----------|-------|
| Control Service | 81 | HTTP | PHP 8.4 (Auth, Session, RBAC) |
| Media Service | 5000/6000 | HTTP | PHP + FFmpeg (Library, Metadata) |
| Audio Service | 9741/9742 | REST/WS | C++20 JUCE (Player, DSP, Mixer) |
| Device Service | — | BLE/WiFi/USB | C++20 (Bluetooth, WiFi, USB) |
| Network Audio | — | WebRTC/P2P | C++20 (Streaming, Multi-room) |
| AI Service | — | Internal | PHP + Python (Recommendations, Auto-download) |
| Download Service | 3001 | HTTP/WS | Node.js + TypeScript |

Detaylar: **[[.ai/architecture/06-audio/]]** ve **[[.ai/architecture/01-overview/architecture_master.md]]**

---

## Architecture Summary

```
L3 — Presentation  (Frontend, UI, DOM)          ← Vanilla JS, ITCSS
L2 — Routing       (Router, middleware, dispatch) ← PHP 8.4 PageRouter
L1 — Security      (Session, Auth, CSRF, CSP)   ← Middleware Pipeline
L0 — Infrastructure (Database, cache, fs)        ← PDO, APCu, Redis
```

**Middleware Pipeline (değişmez sıra — ADR-010/011/012/013/022):**
`SessionManager → BypassAuth → RateLimiter → Auth → SecurityHeaders → Csrf`

Detaylar: **[[.ai/CLAUDE.md]]**

---

## Architecture Decision Records

43 ADR dosyası mevcuttur (ADR-001 to ADR-044, ADR-014 eksik).

- **Frozen (001-037):** Değiştirilemez
- **Active (038-044):** Yeni kararlar için

Önemli ADR'ler:
- **ADR-001:** Vanilla JS, framework YASAK
- **ADR-002:** PDO mandatory, ORM YASAK
- **ADR-010:** CSRF protection (`csrf_token`)
- **ADR-011:** Session management (`COREMUSIC_SESS`)
- **ADR-017/038:** Audio Engine (PCM3168A, PCM5122 REDDED)
- **ADR-022:** Database hardened security (9 BCNF)
- **ADR-040:** Database authority (9 BCNF canonical)
- **ADR-042:** Vault yeniden yapılandırma
- **ADR-043:** Auth subdomain konsolidasyonu
- **ADR-044:** Dynamic theme engine

Tüm ADR'ler: **[[.ai/decisions/accepted/]]**

---

## Deployment Modes

| Mod | Platform | Donanım | Amaç |
|-----|----------|---------|------|
| 🏠 Home Media Center | Windows/Linux/macOS | PC/Laptop | Ev medya sistemi |
| 🚗 Car Audio System | Windows/Android Auto | Raspberry Pi 5 / PCM3168A | Araç içi bilgi-eğlence |
| 🎛️ Professional Studio | Windows (WASAPI/ASIO) | 8.1 Surround + Class AB | Stüdyo üretim sistemi |
| 📦 NAS Audio Server | Linux (Docker) | Synology/QNAP | Ağ medya deposu |
| 🎵 DAC Control System | Windows/Linux | XMOS XU316 + PCM3168A | Yüksek kalite ses çıkışı |

Detaylar: **[[.ai/architecture/01-overview/startup-strategy.md]]**

---

## Audio Organization

5 bölüm: Hardware, Software, Studio, Consumer, Research.

Detaylar: **[[.ai/electronic/audio-organization]]**

---

## Hardware Roadmap

3 fazlı geliştirme: MVP → Premium (PCM3168A, XMOS XU316, Class AB) → Professional (8.1 surround, multi-room, NAS).

Detaylar: **[[.ai/electronic/hardware-roadmap.md]]**

---

## Test Coverage

| Modül | Minimum | Hedef |
|-------|---------|-------|
| Backend (PHP) | ≥80% | ≥90% |
| Frontend (JS) | ≥80% | ≥90% |
| Audio Engine (C++) | ≥80% | ≥90% |
| Download Service | ≥80% | ≥90% |

Detaylar: **[[.ai/testing/coverage-targets.md]]**

---

## Cross References

- **[[.ai/CLAUDE.md]]** — Kanonik AI talimatı
- **[[.ai/AGENTS.md]]** — Kanonik agent kayıt defteri
- **[[.ai/WORKFLOW.md]]** — Kanonik süreçler
- **[[README.md]]** — İnsana bakan giriş noktası

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-05
**Mode:** Red Team • Human Mode • Truth Mode
