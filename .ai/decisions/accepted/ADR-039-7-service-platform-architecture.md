---
title: "ADR-039: 7-Service Platform Architecture"
status: active
date: 2026-07-20
tags: [architecture, platform, 7-service, active]
---

# ADR-039: 7-Service Platform Architecture

---

## 1. Executive Summary

CoreMusic **7 baÄŸÄ±msÄ±z backend servis** ile yÃ¶netilir. Her servis belirli bir domain'i yÃ¶netir. Servisler event-driven iletiÅŸim kurar.

## 2. Decision

### 7 Servis

| # | Servis | Port | Protokol | Stack | Sorumluluk |
|---|--------|------|----------|-------|------------|
| 1 | Control Service | 81 | HTTP | PHP 8.4 | Auth, session, RBAC |
| 2 | Media Service | 5000/6000 | HTTP | PHP + FFmpeg | Library, metadata, streaming |
| 3 | Audio Service | 9741/9742 | REST/WS | C++20 JUCE | Player, DSP, mixer, EQ |
| 4 | Device Service | â€” | BLE/WiFi/USB | C++20 | Bluetooth, WiFi, USB |
| 5 | Network Audio | â€” | WebRTC/P2P | C++20 | Streaming, multi-room |
| 6 | AI Service | â€” | Internal | PHP + Python | Recommendations |
| 7 | Download Service | 3001 | HTTP/WS | Node.js + TS | Deezer/YouTube indirme |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | 7 baÄŸÄ±msÄ±z servis | âœ… Zorunlu |
| 2 | Event-driven iletiÅŸim | âœ… Zorunlu |
| 3 | Service discovery | âœ… Zorunlu |
| 4 | Health check | âœ… Zorunlu |
| 5 | Graceful degradation | âœ… Zorunlu |

---

## 3. Architecture

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚                     CoreMusic Platform                            â”‚
â”‚                                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”          â”‚
â”‚  â”‚ Control  â”‚ â”‚ Media    â”‚ â”‚ Audio    â”‚ â”‚ Device   â”‚          â”‚
â”‚  â”‚ Service  â”‚ â”‚ Service  â”‚ â”‚ Service  â”‚ â”‚ Service  â”‚          â”‚
â”‚  â”‚ :81      â”‚ â”‚ :5000    â”‚ â”‚ :9741    â”‚ â”‚ BLE/WiFi â”‚          â”‚
â”‚  â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”˜          â”‚
â”‚       â”‚             â”‚             â”‚             â”‚                â”‚
â”‚  â”Œâ”€â”€â”€â”€â–¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â–¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â–¼â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â–¼â”€â”€â”€â”€â”          â”‚
â”‚  â”‚              Event Bus (PSR-14)                    â”‚          â”‚
â”‚  â””â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”˜          â”‚
â”‚       â”‚             â”‚             â”‚             â”‚                â”‚
â”‚  â”Œâ”€â”€â”€â”€â–¼â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â–¼â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â–¼â”€â”€â”€â”€â”€â”                       â”‚
â”‚  â”‚ Network  â”‚ â”‚ AI       â”‚ â”‚ Download â”‚                       â”‚
â”‚  â”‚ Audio    â”‚ â”‚ Service  â”‚ â”‚ Service  â”‚                       â”‚
â”‚  â”‚ WebRTC   â”‚ â”‚ PHP+Py   â”‚ â”‚ Node.js  â”‚                       â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜                       â”‚
â”‚                                                                  â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

---

## 4. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-039: 7-Service Platform Architecture v2.0.0 â€” CoreMusic Architecture*
*Authority: Backend Architect Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*