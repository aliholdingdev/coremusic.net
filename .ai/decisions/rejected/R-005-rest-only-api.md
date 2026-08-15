---
type: decision
id: "R-005"
title: "REJECTED: REST-Only API (No WebSocket)"
category: "architecture"
status: "rejected"
date: "2026-08-15"
updated: "2026-08-15"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [rejected, api, rest, websocket]
risk-level: "high"
rejection-reason: "Real-time eksikliği, WebSocket zorunlu"
rejected-by: "ADR-039, ADR-084"
references:
  - "[[decisions/accepted/ADR-039-7-service-platform-architecture]]"
  - "[[decisions/accepted/ADR-084-api-gateway-architecture]]"
---

# REJECTED: REST-Only API (No WebSocket)

---

## 1. Executive Summary

Sadece REST API kullanımı (WebSocket olmadan) **reddedilmiştir**.

## 2. Red Nedeni

| # | Neden | Ağırlık |
|---|-------|---------|
| 1 | Real-time özellikler gerekli | Kritik |
| 2 | Listening rooms WebSocket gerektirir | Yüksek |
| 3 | Player senkronizasyonu | Yüksek |
| 4 | Chat desteği | Orta |

## 3. Alternatif Çözüm

**Seçilen:** REST + WebSocket hybrid (ADR-039)
- REST: CRUD operations
- WebSocket: Real-time updates, listening rooms, chat

---

*Reddedildi: 2026-08-15 · Governance: Red Team · Human Mode · Truth Mode*
