---
type: architecture
category: panels
title: "Panel Architecture — CoreMusic Panel Mimarisi"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Panel Architecture — CoreMusic Panel Mimarisi

**İlgili ADR:** [[decisions/accepted/ADR-004-multi-domain-spa]] · [[decisions/accepted/ADR-044-dynamic-user-theme-engine]] · [[decisions/accepted/ADR-045-multi-domain-view-mode-architecture]] · [[decisions/accepted/ADR-060-rpi5-embedded-auth]]

---

## 1. Amaç

CoreMusic'in 10 panelinin detaylı mimarisi, görünüm modları ve tema entegrasyonu.

---

## 2. 10 Panel

| # | Panel | Subdomain | Port | Stack | Tip | Hedef |
|---|-------|-----------|------|-------|-----|-------|
| 1 | Landing | `coremusic.net` | 80 | Vanilla JS | Static | Tanıtım |
| 2 | Music | `music.coremusic.net` | 81 | PHP 8.4 + JS | Panel | Ana medya |
| 3 | Admin | `admin.coremusic.net` | 80 | PHP 8.4 | Panel | Yönetim |
| 4 | Download | `download.coremusic.net` | 3001 | Node.js + TS | Service | İndirme |
| 5 | Media | `media.coremusic.net` | 5000/6000 | PHP + FFmpeg | Service | Medya |
| 6 | Auth | `auth.coremusic.net` | — | PHP 8.4 | Service | Kimlik |
| 7 | Home | `home.coremusic.net` | 81 | PHP 8.4 | Embedded | Ev merkezi |
| 8 | Car | `car.coremusic.net` | — | PHP 8.4 | Embedded | Araç içi |
| 9 | Studio | `studio.coremusic.net` | 81 | PHP 8.4 | Embedded | Stüdyo |
| 10 | Pro | `pro.coremusic.net` | 81 | PHP 8.4 | Embedded | Profesyonel |

---

## 3. Panel Kategorileri

### 3.1 Web Paneller (Cloud)

| Panel | Port | Stack | Auth | Database |
|-------|------|-------|------|----------|
| `music.coremusic.net` | 81 | PHP 8.4 + JS | Cross-subdomain | MySQL 9 |
| `admin.coremusic.net` | 80 | PHP 8.4 | Cross-subdomain | MySQL 9 |
| `landing.coremusic.net` | 80 | Vanilla JS | Yok | Yok |

### 3.2 Backend Servisler (Cloud)

| Panel | Port | Stack | Auth | Database |
|-------|------|-------|------|----------|
| `auth.coremusic.net` | — | PHP 8.4 | Merkezi | MySQL 9 |
| `media.coremusic.net` | 5000/6000 | PHP + FFmpeg | API Key | MySQL 9 |
| `download.coremusic.net` | 3001 | Node.js + TS | API Key | MySQL 9 |
| `api.coremusic.net` | — | PHP 8.4 | JWT | MySQL 9 |

### 3.3 Embedded Paneller (RPi5 - Local)

| Panel | Donanım | Auth | Database | İnternet |
|-------|---------|------|----------|----------|
| `home.coremusic.net` | RPi5 + Touch Screen | Local (SQLite) | SQLite | Gerekmez |
| `pro.coremusic.net` | RPi5 + HDMI Display | Local (SQLite) | SQLite | Gerekmez |
| `studio.coremusic.net` | RPi5 + 8.1 Surround | Local (SQLite) | SQLite | Gerekmez |
| `car.coremusic.net` | RPi5 + PCM3168A | Local (SQLite) | SQLite | Gerekmez |

---

## 4. Embedded System Detayları

### 4.0 Volumio Benzeri Medya İşletim Sistemi

Home, Pro ve Studio modları **Raspberry Pi 5** üzerinde çalışan, **Volumio benzeri** ancak çok daha gelişmiş **yerel medya işletim sistemleri**dir.

**Temel Özellikler:**
- Tarayıcı arayüzü **tam ekran** olarak çalışır
- **Dokunmatik ekran** desteği (RPi5 Touch Screen / HDMI Display)
- **Ses output kontrolü** (volume, EQ, source selection)
- **Yerel sunucu** modunda çalışır (internet gerekmez)
- **Browser control panel** — tarayıcı bir kontrol paneli gibi davranır
- **Volumio'dan farkı:** Daha geniş medya yönetimi, AI destekli EQ, multi-room desteği

**Donanım Hedefi:**
| Mod | Donanım | Ekran | Kullanım |
|-----|---------|-------|----------|
| Home | RPi5 + PCM3168A | Touch Screen | Ev teybi |
| Pro | RPi5 + Class AB Amp | HDMI Display | Profesyonel |
| Studio | RPi5 + 8.1 Surround | HDMI Display | Stüdyo |

*Kaynak: [[ADR-060-rpi5-embedded-auth]]*

### 4.1 Embedded Auth Akışı

```
┌─────────────────────────────────────────────────────────────────┐
│ RPi5 (Home/Pro/Studio/Car)                                      │
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ Local Web Server (Apache/Nginx)                          │  │
│  │ Port: 80 (prod), 81 (dev)                                │  │
│  │                                                           │  │
│  │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐     │  │
│  │  │ home.core   │  │ pro.core    │  │ studio.core │     │  │
│  │  │ .music.net  │  │ .music.net  │  │ .music.net  │     │  │
│  │  │             │  │             │  │             │     │  │
│  │  │ PHP 8.4     │  │ PHP 8.4     │  │ PHP 8.4     │     │  │
│  │  │ Local DB    │  │ Local DB    │  │ Local DB    │     │  │
│  │  └──────┬──────┘  └──────┬──────┘  └──────┬──────┘     │  │
│  │         │                │                │              │  │
│  │         └────────────────┼────────────────┘              │  │
│  │                          │                               │  │
│  │                 ┌────────┴────────┐                      │  │
│  │                 │  Local Auth     │                      │  │
│  │                 │  (Standalone)   │                      │  │
│  │                 │                 │                      │  │
│  │                 │  - SQLite DB    │                      │  │
│  │                 │  - Local admin  │                      │  │
│  │                 │  - İlk kurulumda│                      │  │
│  │                 │    auth.core'a  │                      │  │
│  │                 │    bağlanır     │                      │  │
│  │                 │  - Offline-first│                      │  │
│  │                 └─────────────────┘                      │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ Touch Screen UI                                          │  │
│  │                                                           │  │
│  │  - Tam ekran arayüz (Volumio benzeri)                    │  │
│  │  - Dokunmatik kontroller (48px minimum touch target)     │  │
│  │  - Volume knob (fiziksel + dijital)                      │  │
│  │  - Play/Pause/Next/Prev butonları                        │  │
│  │  - Album art display                                     │  │
│  │  - EQ controls                                           │  │
│  │  - Source selection (USB, Bluetooth, WiFi, NAS)          │  │
│  │  - Görünüm modları: Home / Pro / Studio (tek tıkla geçiş)│  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

### 4.2 Embedded vs Web Auth Farkları

| Özellik | Web (cloud) | Embedded (local) |
|---------|-------------|------------------|
| Auth Sunucusu | auth.coremusic.net | Local (aynı RPi5) |
| Database | MySQL 9 (18 BCNF) | SQLite (1 DB) |
| Session | Cross-subdomain | Local |
| JWT | RS256 (production key) | HS256 (local key) |
| Rate Limit | 60 req/60s | Devre dışı |
| CSRF | Aktif | Aktif |
| CSP | Aktif | Aktif |
| HTTPS | Zorunlu | Opsiyonel (local) |
| Internet | Gerekli | Gerekmez (offline) |
| Multi-user | Evet | Tek kullanıcı (admin) |
| Touch UI | Hayır | Evet |
| Full-screen | Hayır | Evet |

### 4.3 Embedded First-Boot Setup

```
┌─────────────────────────────────────────────────────────────────┐
│ First Boot Wizard                                               │
│                                                                 │
│  1. Dil seçimi (TR/EN)                                        │
│  2. Admin şifresi belirle                                      │
│  3. Ağ yapılandırması (WiFi/Ethernet)                          │
│  4. Medya klasörü seçimi (/mnt/usb, /home/pi/Music)           │
│  5. Audio output seçimi (HDMI, 3.5mm, USB DAC, I2S)          │
│  6. Touch screen kalibrasyonu                                   │
│  7. Tamamla → Ana ekran                                        │
└─────────────────────────────────────────────────────────────────┘
```

---

## 5. View Mode (ADR-045)

| Mod | Hedef Kullanıcı | Tema |
|-----|-----------------|------|
| Home | Ev kullanıcısı | Basit, büyük butonlar |
| Pro | Profesyonel | Gelişmiş kontrol |
| Studio | Stüdyo mühendisi | 8.1 surround, EQ |

---

## 6. Tema Motoru (ADR-044)

| Cinsiyet | Renk | Tema |
|----------|------|------|
| Female | Pembe | Pink |
| Male | Mavi | Blue |
| Neutral | Varsayılan | Default |

---

## 7. Panel Eşleme Matrisi

| Panel | Home | Pro | Studio | Özel |
|-------|------|-----|--------|------|
| music | ✅ | ✅ | ✅ | — |
| admin | ❌ | ✅ | ❌ | Admin-only |
| download | ❌ | ✅ | ❌ | Queue view |
| media | ✅ | ✅ | ✅ | Library view |
| auth | ❌ | ❌ | ❌ | Login/Register |
| home | ✅ | ❌ | ❌ | TV interface |
| car | ✅ | ❌ | ❌ | Touch-optimized |
| studio | ❌ | ✅ | ✅ | Recording |
| pro | ❌ | ✅ | ✅ | Advanced |
| landing | ❌ | ❌ | ❌ | Marketing |

---

## 8. Cross References

| Dosya | Amaç |
|-------|------|
| [[architecture/01-overview]] | Genel bakış |
| [[architecture/l3-presentation]] | Frontend detay |
| [[architecture/03-services]] | Backend servis |
| [[architecture/03-contracts/project-structure]] | Proje yapısı |
| [[ADR-060-rpi5-embedded-auth]] | RPi5 embedded auth |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
