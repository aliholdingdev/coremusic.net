---
title: Neva Engine — Security
date: 2026-06-24
updated: 2026-06-24
type: spec
status: active
authority: Security Engineer
references: [[../../.templates/cpp-template]]
---

# Neva Engine — Security

See [[../../.templates/cpp-template]] for C++20 security standards.

## IPC Security

- Named object magic number validation (0xCAFE)
- Handshake: `{apiVersion: 2, clientType: "player"|"spa"}`
- PID validation via OpenProcess
- No blocking operations in ASIO callback

## Threat Model

| Threat | Mitigation |
|--------|------------|
| Unauthorized IPC access | Named object ACL + magic validation |
| Handshake spoofing | apiVersion + clientType verification |
| PID spoofing | OpenProcess validation against known PIDs |
| Buffer overflow in ASIO callback | Lock-free ring buffer, pre-allocated, no heap allocation |

## Security Controls

- **Authentication:** Named object existence + handshake protocol
- **Authorization:** Client type in handshake (player, spa) determines allowed commands
- **Audit:** Connection events logged with PID, clientType, timestamp
- **Rate Limiting:** 1000 commands/min per connection
- **Data at Rest:** No persistent data stored in engine
- **Data in Transit:** localhost-only binding (no external network exposure)