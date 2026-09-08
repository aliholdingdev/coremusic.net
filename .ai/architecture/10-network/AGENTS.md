---
title: "CoreMusic — .ai/architecture/10-network Agent Talimatları"
type: agent-registry
folder: ".ai/architecture/10-network"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
governance: Red Team · Human Mode · Truth Mode
---

# .ai/architecture/10-network — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./index.md]]

## 1. Amaç
Ağ iletişim dokümantasyonu: gRPC-IPC, HTTP(S), WebSocket/MQTT, local socket.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `index.md` | Klasör dizini |
| `http-https.md` | Web katmanı iletişimi |
| `websocket-mqtt.md` | Gerçek zamanlı kanallar |
| `grpc-ipc.md` | Servis arası gRPC/IPC (ADR-032) |
| `local-socket.md` | Yerel socket (cihaz ajanı) |

## 3. Agent Kuralları
1. Protokol seçimi → `03-contracts/protocols/protocol-decision.md` ile senkron
2. Port bilgisi → `03-contracts/ports/port-registry.md` tek kayıt

## 5. İlgili Kaynaklar
[[../AGENTS.md]] · [[../../../decisions/accepted/ADR-032-ipc-contract-versioning.md]] · [[../../ecosystem/service-communication.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
