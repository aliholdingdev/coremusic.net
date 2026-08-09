---
type: ecosystem
category: service-integration
title: "7-Service Integration"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# 7-Service Integration

**İlgili ADR:** [[decisions/accepted/ADR-039-7-service-platform-architecture]]

## 1. Amaç

7 servisin entegrasyonu ve iletişim kalıpları.

## 2. Servisler

| # | Servis | Port | Protocol | Stack |
|---|--------|------|----------|-------|
| 1 | Control | 81 | HTTP | PHP 8.4 |
| 2 | Media | 5000/6000 | HTTP | PHP + FFmpeg |
| 3 | Audio | 9741/9742 | REST/WS | C++20 JUCE |
| 4 | Device | — | BLE/WiFi/USB | C++20 |
| 5 | Network Audio | — | WebRTC/P2P | C++20 |
| 6 | AI | — | Internal | PHP + Python |
| 7 | Download | 3001 | HTTP/WS | Node.js + TS |

## 3. İletişim Kalıpları

| Kalıp | Kullanım |
|-------|----------|
| Request-Response | HTTP API |
| Event-Driven | Real-time updates |
| Message Queue | Async processing |
| WebSocket | Streaming |

## 4. Servis Bağımlılıkları

```text
Control → Media, Auth, Session
Media → Audio, Metadata
Audio → Device, Network
Download → Catalog, Media
AI → Musics, User
```

## 5. Health Check

| Servis | Endpoint | Port |
|--------|----------|------|
| Control | /health | 81 |
| Media | /health | 5000 |
| Audio | /health | 9741 |
| Download | /health | 3001 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
