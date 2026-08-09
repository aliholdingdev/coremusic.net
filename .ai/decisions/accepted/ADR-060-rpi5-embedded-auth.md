---
type: adr
category: architecture
title: "ADR-060 Raspberry Pi 5 Embedded Auth Architecture"
date: 2026-08-09
version: 1.0.0
status: active
author: Bayram Ali
governance: Red Team · Human Mode · Truth Mode
---

# ADR-060: Raspberry Pi 5 Embedded Auth Architecture

## Bağlam

CoreMusic'in Home, Pro ve Studio modları Raspberry Pi 5 üzerinde çalışacaktır. Bu modlar Volumio benzeri bir medya işletim sistemi olacaktır. Tarayıcıda açılsa bile oynatıcı kontrol paneli gibi çalışacaktır. PC'de tam ekran web sitesi gibi çalışmayacaktır, asıl hedef Raspberry Pi 5 Touch Screen, HDMI Display, Ev Teybi, Araç Teybi arayüzüdür.

## Karar

### 1. Embedded Mod Tanımları

| Mod | Kullanım | Donanım | Amaç |
|-----|----------|---------|------|
| Home | Ev medya sistemi | RPi5 + Touch Screen | Volumio benzeri ev teybi |
| Pro | Profesyonel ortam | RPi5 + HDMI Display | Profesyonel medya yönetimi |
| Studio | Stüdyo ortamı | RPi5 + 8.1 Surround | Stüdyo ses sistemi |
| Car | Araç içi | RPi5 + PCM3168A | Araç bilgi-eğlence |

### 2. Embedded Auth Akışı

```
┌─────────────────────────────────────────────────────────────────┐
│ RPi5 (Home/Pro/Studio)                                         │
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
│  │                 │  - No cloud     │                      │  │
│  │                 └─────────────────┘                      │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ Touch Screen UI                                          │  │
│  │                                                           │  │
│  │  - Tam ekran arayüz                                      │  │
│  │  - Dokunmatik kontroller                                 │  │
│  │  - Volume knob (fiziksel + dijital)                      │  │
│  │  - Play/Pause/Next/Prev butonları                        │  │
│  │  - Album art display                                     │  │
│  │  - EQ controls                                           │  │
│  │  - Source selection (USB, Bluetooth, WiFi, NAS)          │  │
│  └──────────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

### 3. Embedded vs Web Auth Farkları

| Özellik | Web (cloud) | Embedded (local) |
|---------|-------------|------------------|
| Auth Sunucusu | auth.coremusic.net | Local (aynı RPi5) |
| Database | MySQL 9 (9 BCNF) | SQLite (1 DB) |
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

### 4. Embedded Local Auth

```php
// RPi5 Local Auth Config
define('AUTH_MODE', 'local');
define('DB_DRIVER', 'sqlite');
define('DB_PATH', '/var/lib/coremusic/coremusic.db');
define('JWT_SECRET', 'local-random-key');  // İlk kurulumda üretilir
define('SESSION_LIFETIME', 86400);          // 24 saat
define('RATE_LIMIT_ENABLED', false);
define('HTTPS_ENABLED', false);             // Local network
```

### 5. Embedded First-Boot Setup

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

### 6. Embedded Session Management

```php
// RPi5 Local Session
session_name('COREMUSIC_EMBEDDED_SESS');
session_set_cookie_params([
    'lifetime' => 86400,       // 24 saat
    'path' => '/',
    'domain' => '',            // Local domain
    'secure' => false,         // Local network
    'httponly' => true,
    'samesite' => 'Lax',
]);
```

### 7. Embedded vs Web Mimari Farkı

```
┌─────────────────────────────────────────────────────────────────┐
│ WEB (Cloud)                                                     │
│                                                                 │
│  Browser → auth.coremusic.net → Session + JWT → Redirect       │
│                                                                 │
│  - Cross-subdomain auth                                        │
│  - Merkezi auth sunucusu                                        │
│  - Multi-user                                                   │
│  - Remote database                                              │
│  - HTTPS zorunlu                                                │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ EMBEDDED (Local)                                                │
│                                                                 │
│  Touch Screen → Local Auth → Session → Dashboard               │
│                                                                 │
│  - Local auth (aynı RPi5)                                      │
│  - SQLite database                                              │
│  - Tek kullanıcı (admin)                                        │
│  - Offline-first                                                │
│  - No HTTPS (local)                                             │
│  - Full-screen UI                                               │
│  - Touch-optimized controls                                     │
└─────────────────────────────────────────────────────────────────┘
```

### 8. Embedded Dashboard Layout

```
┌─────────────────────────────────────────────────────────────────┐
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │  HEADER: [Logo] [Mode: Home] [Settings] [Power]            │ │
│ └─────────────────────────────────────────────────────────────┘ │
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │                                                             │ │
│ │                    ALBUM ART DISPLAY                        │ │
│ │                                                             │ │
│ │                   ┌─────────────────┐                      │ │
│ │                   │                 │                      │ │
│ │                   │   Album Cover   │                      │ │
│ │                   │                 │                      │ │
│ │                   └─────────────────┘                      │ │
│ │                                                             │ │
│ │              Artist Name - Song Title                       │ │
│ │                    Album Name                               │ │
│ │                                                             │ │
│ └─────────────────────────────────────────────────────────────┘ │
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │  PROGRESS BAR: [=============>        ] 2:34 / 4:12        │ │
│ └─────────────────────────────────────────────────────────────┘ │
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │  CONTROLS: [⏮] [⏯] [⏭] [🔀] [🔁] [Volume: ████░░]      │ │
│ └─────────────────────────────────────────────────────────────┘ │
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │  SOURCES: [USB] [Bluetooth] [WiFi] [NAS] [Radio]          │ │
│ └─────────────────────────────────────────────────────────────┘ │
│ ┌─────────────────────────────────────────────────────────────┐ │
│ │  QUICK ACCESS: [Home] [Pro] [Studio] [EQ] [Playlists]     │ │
│ └─────────────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────┘
```

## Sonuçlar

### Olumlu
- Offline-first çalışma
- Hızlı yanıt süresi (lokal DB)
- Dokunmatik ekran optimizasyonu
- Volumio benzeri kullanım
- İnternet bağlantısına bağımlı değil

### Olumsuz
- Sync mekanizması gerekli (web ↔ embedded)
- Farklı auth mekanizması (local vs cloud)
- Bakım ve güncelleme karmaşıklığı

## İlgili ADR'ler

- [[ADR-058-cross-subdomain-auth-flow]] — Cross-subdomain auth
- [[ADR-052-hybrid-auth-architecture]] — Hybrid Auth
- [[ADR-017-dsp-hardware-mode]] — DSP hardware mode
- [[ADR-038-8.1-sound-card-chip-selection]] — Ses donanımı

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
