---
type: architecture
category: overview
title: "System Overview — CoreMusic Sistem Genel Bakışı"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 19.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# System Overview — CoreMusic Sistem Genel Bakışı

**İlgili ADR:** [[decisions/accepted/ADR-004-multi-domain-spa]] · [[decisions/accepted/ADR-039-7-service-platform-architecture]] · [[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]]

---

## 1. Amaç

CoreMusic'in tam sistem mimarisinin genel bakışı. 10 panel, 7 backend servisi, 18 BCNF veritabanı ve 5 deployment modunu kapsar.

---

## 2. Platform Tanımı

| Özellik | Değer |
|---------|-------|
| Platform Adı | CoreMusic |
| Platform Türü | Dijital Medya Yönetim Platformu |
| Hedef Kullanıcılar | Bireysel, Profesyonel, Stüdyo, Araç İçi, Ev Medya |
| Temel Teknoloji | PHP 8.4, C++20, Vanilla JS, MySQL 9 |
| Versiyon | 19.0.0 |

---

## 3. 10 Panel

| # | Panel | Subdomain | Port | Stack |
|---|-------|-----------|------|-------|
| 1 | Landing | `coremusic.net` | 80 | Vanilla JS |
| 2 | Music | `music.coremusic.net` | 81 | PHP 8.4 + JS |
| 3 | Admin | `admin.coremusic.net` | 80 | PHP 8.4 |
| 4 | Download | `download.coremusic.net` | 3001 | Node.js + TS |
| 5 | Media | `media.coremusic.net` | 5000/6000 | PHP + FFmpeg |
| 6 | Auth | `auth.coremusic.net` | — | PHP 8.4 |
| 7 | Home | `home.coremusic.net` | 81 | Vanilla JS |
| 8 | Car | `car.coremusic.net` | — | Vanilla JS |
| 9 | Studio | `studio.coremusic.net` | 81 | Vanilla JS |
| 10 | Pro | `pro.coremusic.net` | 81 | Vanilla JS |

---

## 4. 7 Backend Servis

| # | Servis | Port | Protocol | Stack |
|---|--------|------|----------|-------|
| 1 | Control Service | 81 | HTTP | PHP 8.4 |
| 2 | Media Service | 5000/6000 | HTTP | PHP + FFmpeg |
| 3 | Audio Service | 9741/9742 | REST/WS | C++20 JUCE |
| 4 | Device Service | — | BLE/WiFi/USB | C++20 |
| 5 | Network Audio | — | WebRTC/P2P | C++20 |
| 6 | AI Service | — | Internal | PHP + Python |
| 7 | Download Service | 3001 | HTTP/WS | Node.js + TS |

---

## 5. 18 BCNF Veritabanı

| # | Veritabanı | Amaç |
|---|------------|------|
| 1 | coremusic_auth | Kullanıcılar, roller, session |
| 2 | coremusic_user | Profiller, tercihler |
| 3 | coremusic_musics | Şarkılar, sanatçılar |
| 4 | coremusic_albums | Albüm koleksiyonları |
| 5 | coremusic_playlist | Çalma listeleri |
| 6 | coremusic_catalog | İndirme kuyrukları |
| 7 | coremusic_logs | Uygulama logları |
| 8 | coremusic_media | Medya metadata |
| 9 | coremusic_system | Sistem konfigürasyonu |

---

## 6. Mimari Katmanlar (L0-L3)

| Katman | Kapsam | Teknoloji |
|--------|--------|-----------|
| L3 Presentation | Frontend, UI, DOM | Vanilla JS, ITCSS |
| L2 Routing | SPA router, middleware | PHP 8.4 PageRouter |
| L1 Security | Session, Auth, CSRF, CSP | Middleware pipeline |
| L0 Infrastructure | Database, cache, filesystem | PDO, APCu, Redis |

---

## 7. Port Haritası

| Port | Servis | Protokol |
|------|--------|----------|
| 80 | admin.coremusic.net | HTTP |
| 81 | music.coremusic.net (Control) | HTTP |
| 3001 | download.coremusic.net | HTTP/WS |
| 3306 | MySQL 18 BCNF DB | TCP |
| 5000/6000 | media.coremusic.net | HTTP |
| 9741 | Audio Service (REST) | HTTP |
| 9742 | Audio Service (WebSocket) | WS |

---

## 8. Deployment Modları

| Mod | Platform | Donanım | Auth Modu |
|-----|----------|---------|-----------|
| Home Media Center | Windows/Linux/macOS | PC/Laptop | Cloud/Hybrid |
| Car Audio System | Windows/Android Auto | Raspberry Pi 5 / PCM3168A | Local/Hybrid |
| Professional Studio | Windows (WASAPI/ASIO) | 8.1 Surround + Class AB | Cloud/Hybrid |
| NAS Audio Server | Linux (Docker) | Synology/QNAP | Cloud |
| DAC Control System | Windows/Linux | XMOS XU316 + PCM3168A | Local |
| **RPi5 Home (Embedded)** | **Raspberry Pi OS** | **RPi5 + Touch Screen** | **Local/Hybrid** |
| **RPi5 Pro (Embedded)** | **Raspberry Pi OS** | **RPi5 + HDMI Display** | **Local/Hybrid** |
| **RPi5 Studio (Embedded)** | **Raspberry Pi OS** | **RPi5 + 8.1 Surround** | **Local/Hybrid** |

**Embedded Sistemler (RPi5):** Home, Pro, Studio modları Volumio benzeri yerel medya işletim sistemleridir. Touch screen, HDMI display, full-screen web arayüzü ile çalışır. Browser control panel gibi davranır.

*Kaynak: [[ADR-060-rpi5-embedded-auth]], [[architecture/04-panels]]*

---

## 9. Cross References

| Dosya | Amaç |
|-------|------|
| [[CLAUDE.md]] | Ana sözleşme |
| [[index.md]] | Master katalog |
| [[brain.md]] | Mimari kararlar |
| [[architecture/l0-infrastructure]] | L0 detay |
| [[architecture/l1-security]] | L1 detay |
| [[architecture/l2-routing]] | L2 detay |
| [[architecture/l3-presentation]] | L3 detay |
| [[architecture/06-audio]] | Audio detay |
| [[architecture/08-auth]] | Auth mimarisi |
| [[architecture/08-auth/auth-embedded]] | RPi5 embedded auth |
| [[architecture/04-panels]] | Panel mimarisi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode