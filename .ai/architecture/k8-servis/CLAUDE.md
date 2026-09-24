---
title: "CoreMusic — K8 Servis CLAUDE.md"
type: layer-guide
folder: "architecture/k8-servis"
category: vault
date: 2026-09-20
status: active
version: 1.0.0
authority: reference
---

# K8 Servis — CLAUDE.md

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Doğrudan servis çağrısı yasak | Tight coupling |
| 2 | Event Bus zorunlu | Bağımlılık |
| 3 | Max 3 retry | Sonsuz döngü |
| 4 | Circuit breaker zorunlu | Cascade failure |

## 2. Servis Portları

| Servis | Port | Protokol |
|--------|------|----------|
| Control | 81 | HTTP |
| Media | 5000/6000 | HTTP |
| Audio | 9741/9742 | REST/WS |
| Download | 3001 | HTTP/WS |
| AI | — | Internal |
| Device | — | BLE/WiFi |
| Network | — | WebRTC |

## 3. Event Types

| Event | Publisher | Consumers |
|-------|-----------|-----------|
| user.login | Control | Media, AI |
| track.play | Audio | AI, Logs |
| download.complete | Download | Media |

## 4. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-039 | 7-servis platform mimarisi |
| ADR-086 | Event Driven Architecture |

---

*K8 CLAUDE.md v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
