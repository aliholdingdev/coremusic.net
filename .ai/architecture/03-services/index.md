---
type: architecture
category: services
title: "Service Architecture — CoreMusic Servis Mimarisi"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 19.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Service Architecture — CoreMusic Servis Mimarisi

**İlgili ADR:** [[decisions/accepted/ADR-032-ipc-contract-versioning]] · [[decisions/accepted/ADR-039-7-service-platform-architecture]]

---

## 1. Amaç

CoreMusic'in 7 backend servisinin detaylı mimarisi, iletişim protokolleri ve API sözleşmeleri.

---

## 2. 7 Servis

| # | Servis | Port | Protocol | Stack | Sorumluluk |
|---|--------|------|----------|-------|------------|
| 1 | Control Service | 81 | HTTP | PHP 8.4 | Auth, session, RBAC |
| 2 | Media Service | 5000/6000 | HTTP | PHP + FFmpeg | Library, metadata |
| 3 | Audio Service | 9741/9742 | REST/WS | C++20 JUCE | Player, DSP, mixer |
| 4 | Device Service | — | BLE/WiFi/USB | C++20 | Bluetooth, WiFi |
| 5 | Network Audio | — | WebRTC/P2P | C++20 | Streaming, multi-room |
| 6 | AI Service | — | Internal | PHP + Python | Recommendations |
| 7 | Download Service | 3001 | HTTP/WS | Node.js + TS | Deezer/YouTube |

---

## 3. IPC Sözleşmeleri (ADR-032)

| Protokol | Kullanım | Port |
|----------|----------|------|
| HTTP REST | Servisler arası | 81, 3001, 5000, 9741 |
| WebSocket | Gerçek zamanlı | 9742, 6000 |
| Shared Memory | Yüksek performans | — |

---

## 4. Servis İletişim Akışı

```
Control Service (81) ←→ Media Service (5000/6000)
Control Service (81) ←→ Audio Service (9741/9742)
Control Service (81) ←→ Download Service (3001)
Media Service (5000) ←→ Audio Service (9741)
Download Service (3001) ←→ Media Service (5000)
AI Service ←→ Control Service (81)
Device Service ←→ Audio Service (9742)
```

---

## 5. Health Check

| Servis | Endpoint | Sıklık |
|--------|----------|--------|
| Control | `/health` | 10s |
| Media | `/health` | 10s |
| Audio | `/health` | 10s |
| Download | `/health` | 30s |
| AI | `/health` | 60s |

---

## 6. Cross References

| Dosya | Amaç |
|-------|------|
| [[architecture/01-overview]] | Genel bakış |
| [[architecture/04-panels]] | Panel detayları |
| [[architecture/06-audio]] | Audio servis |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode