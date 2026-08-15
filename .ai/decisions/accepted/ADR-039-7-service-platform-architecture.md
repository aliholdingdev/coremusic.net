---
type: decision
id: "039"
title: "ADR-039: 7-Service Platform Architecture"
category: "architecture"
status: "active"
date: "2026-07-20"
updated: "2026-08-15"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [architecture, platform, 7-service, active]
risk-level: "critical"
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[decisions/accepted/ADR-084-api-gateway-architecture]]"
  - "[[decisions/accepted/ADR-086-event-driven-architecture]]"
---

# ADR-039: 7-Service Platform Architecture

---

## 1. Executive Summary

CoreMusic **7 bağımsız backend servis** ile yönetilir. Her servis belirli bir domain'i yönetir. Servisler event-driven iletişim kurar.

## 2. Decision

### 7 Servis

| # | Servis | Port | Protokol | Stack | Sorumluluk |
|---|--------|------|----------|-------|------------|
| 1 | Control Service | 81 | HTTP | PHP 8.4 | Auth, session, RBAC |
| 2 | Media Service | 5000/6000 | HTTP | PHP + FFmpeg | Library, metadata, streaming |
| 3 | Audio Service | 9741/9742 | REST/WS | C++20 JUCE | Player, DSP, mixer, EQ |
| 4 | Device Service | — | BLE/WiFi/USB | C++20 | Bluetooth, WiFi, USB |
| 5 | Network Audio | — | WebRTC/P2P | C++20 | Streaming, multi-room |
| 6 | AI Service | — | Internal | PHP + Python | Recommendations |
| 7 | Download Service | 3001 | HTTP/WS | Node.js + TS | Deezer/YouTube indirme |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | 7 bağımsız servis | ✅ Zorunlu |
| 2 | Event-driven iletişim | ✅ Zorunlu |
| 3 | Service discovery | ✅ Zorunlu |
| 4 | Health check | ✅ Zorunlu |
| 5 | Graceful degradation | ✅ Zorunlu |

---

## 3. Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                     CoreMusic Platform                            │
│                                                                  │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │
│  │ Control  │ │ Media    │ │ Audio    │ │ Device   │          │
│  │ Service  │ │ Service  │ │ Service  │ │ Service  │          │
│  │ :81      │ │ :5000    │ │ :9741    │ │ BLE/WiFi │          │
│  └────┬─────┘ └────┬─────┘ └────┬─────┘ └────┬─────┘          │
│       │             │             │             │                │
│  ┌────▼─────────────▼─────────────▼─────────────▼────┐          │
│  │              Event Bus (PSR-14)                    │          │
│  └────┬─────────────┬─────────────┬─────────────┬────┘          │
│       │             │             │             │                │
│  ┌────▼─────┐ ┌────▼─────┐ ┌────▼─────┐                       │
│  │ Network  │ │ AI       │ │ Download │                       │
│  │ Audio    │ │ Service  │ │ Service  │                       │
│  │ WebRTC   │ │ PHP+Py   │ │ Node.js  │                       │
│  └──────────┘ └──────────┘ └──────────┘                       │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 4. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-039: 7-Service Platform Architecture v2.0.0 — CoreMusic Architecture*
*Authority: Backend Architect · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
