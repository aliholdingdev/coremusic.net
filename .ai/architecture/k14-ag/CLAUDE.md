---
title: "CoreMusic — K14 Ağ CLAUDE.md"
type: layer-guide
folder: "architecture/k14-ag"
category: vault
date: 2026-09-20
status: active
version: 1.0.0
authority: SSOT
---

# K14 Ağ — CLAUDE.md

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | TLS zorunlu | Güvenlik açığı |
| 2 | Whitelist-only | Yetkisiz erişim |
| 3 | mDNS standard | Keşif hatası |

## 2. Protokol Kısıtları

| Protokol | Kullanım | Port |
|----------|----------|------|
| HTTP/2 | API | 443 |
| WebSocket | Real-time | 443 |
| mDNS | Keşif | 5353 |
| DLNA | Medya | 1900 |

---

*K14 CLAUDE.md v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
