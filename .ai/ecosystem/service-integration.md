---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — Service Integration"
type: ecosystem
category: ecosystem
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
---

# Service Integration

## Entegrasyon Protokolleri

| Protokol | Kullanım | Servisler |
|----------|---------|-----------|
| HTTP/REST | Senkron API | Tüm servisler |
| WebSocket | Real-time | Audio, Download |
| Event Bus | Async | PSR-14 |
| gRPC | High-perf | Audio → Device |
| IPC | Process间 | Native servisler |

## Event Types

| Event | Publisher | Consumers |
|-------|-----------|-----------|
| user.login | Control | Media, AI |
| track.play | Audio | AI, Logs |
| download.complete | Download | Media |
| device.connect | Device | Audio, Media |
| eq.preset.change | Audio | Media |

---

*Service Integration v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
