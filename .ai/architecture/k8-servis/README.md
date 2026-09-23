---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K8 Servis Layer"
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

# K8: Servis Layer

**Katman:** K8 (Servis)
**Kapsam:** Control, Media, Audio, Device, Network, AI, Download + Health
**Sorumlu Agent:** Backend Architect
**Bileşen Sayısı:** 55

---

## 1. Genel Bakış

K8 katmanı, CoreMusic'in 7 backend servisini içerir. Servisler birbirini doğrudan çağırmaz, Event Bus (PSR-14) üzerinden iletişim kurar.

---

## 2. 7 Servis Haritası

| # | Servis | Port | Protokol | Stack | Sorumluluk |
|---|--------|------|----------|-------|------------|
| 1 | Control Service | 81 | HTTP | PHP 8.4 | Auth, session, RBAC |
| 2 | Media Service | 5000/6000 | HTTP | PHP + FFmpeg | Library, metadata, streaming |
| 3 | Audio Service | 9741/9742 | REST/WS | C++20 JUCE | Player, DSP, mixer, EQ |
| 4 | Device Service | — | BLE/WiFi/USB | C++20 | Bluetooth, WiFi, USB |
| 5 | Network Audio | — | WebRTC/P2P | C++20 | Streaming, multi-room |
| 6 | AI Service | — | Internal | PHP + Python | Recommendations |
| 7 | Download Service | 3001 | HTTP/WS | Node.js + TS | Deezer/YouTube indirme |

---

## 3. Event Driven Mimari (ADR-086)

```
Service A → Event Bus (PSR-14) → Service B, C, D

Event Types:
  - user.login
  - user.logout
  - track.play
  - track.download
  - playlist.create
  - playlist.update
  - eq.preset.change
  - device.connect
  - device.disconnect
```

---

## 4. Servis Detayları

### 4.1 Control Service (Port 81)

```
Sorumluluklar:
  - Kimlik doğrulama (Auth)
  - Oturum yönetimi (Session)
  - Rol bazlı erişim (RBAC)
  - Kullanıcı profilleri
  - Tercih yönetimi

API Endpoints:
  POST /api/v1/auth/login
  POST /api/v1/auth/logout
  GET  /api/v1/user/profile
  PUT  /api/v1/user/preferences
```

### 4.2 Media Service (Port 5000/6000)

```
Sorumluluklar:
  - Müzik kütüphanesi yönetimi
  - Metadata çıkarma (ID3, FLAC tags)
  - Medya oynatma (streaming)
  - Kapak görselleri
  - Podcast ve video yönetimi

API Endpoints:
  GET  /api/v1/library/tracks
  GET  /api/v1/library/albums
  GET  /api/v1/stream/{track_id}
  POST /api/v1/library/upload
```

### 4.3 Audio Service (Port 9741/9742)

```
Sorumluluklar:
  - Ses oynatma (Neva Engine)
  - DSP işleme (EQ, reverb, compressor)
  - Mixer yönetimi
  - ASIO/WASAPI kontrolü
  - Cihaz seçimi

WebSocket Events:
  audio.play
  audio.pause
  audio.seek
  audio.eq.change
  audio.volume.change
  audio.device.switch
```

### 4.4 Download Service (Port 3001)

```
Sorumluluklar:
  - Deezer FLAC indirme
  - YouTube indirme
  - Queue yönetimi
  - Cache yönetimi
  - Metadata çıkarma

API Endpoints:
  POST /api/v1/download/url
  GET  /api/v1/download/status/{id}
  DELETE /api/v1/download/{id}
  GET  /api/v1/download/queue
```

---

## 5. Servis İletişim Kuralları

| Kural | Açıklama |
|-------|----------|
| Doğrudan çağrı yasak | Servisler arası HTTP çağrısı yasak |
| Event Bus zorunlu | PSR-14 Event Dispatcher |
| Async processing | Uzun işlemler async |
| Circuit breaker | Başarısızlık koruması |
| Retry policy | Max 3 retry |

---

## 6. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-039 | 7-servis platform mimarisi |
| ADR-086 | Event Driven Architecture |

---

*K8 Servis Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
*Mode: Red Team · Human Mode · Truth Mode*
