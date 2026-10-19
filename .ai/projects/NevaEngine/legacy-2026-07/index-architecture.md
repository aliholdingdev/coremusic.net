---
title: Neva Engine — Architecture
date: 2026-06-24
updated: 2026-06-24
type: spec
status: active
authority: Embedded Systems Engineer
references: [[../../.templates/cpp-template]], [[../../../decisions/031-ecosystem-port-allocation]], [[../../../decisions/032-ipc-contract-versioning]]
---

# Neva Engine — Architecture

**Template:** [[../../.templates/cpp-template]]
**Framework:** JUCE 7+ with ASIO

## Layer Structure

| Layer | Components |
|-------|------------|
| L0: Infrastructure | ASIO callback, lock-free ring buffer, shared memory (NevaEngineStatus, NevaAudioRingBuffer) |
| L1: Security | IPC handshake (apiVersion, clientType), PID validation, named object ACL |
| L2: Routing | REST dispatch (9741), WebSocket JSON-RPC (9742) |
| L3: Presentation | Response serialization, FFT spectrum streaming (binary float32) |

## Key Specs

- Ports: 9741 (REST), 9742 (WS), fallback 9743-9840
- Latency target: <2.3ms
- Ring buffer: float32 PCM @ 48kHz, power-of-2 buffer

## Subdirectory Reference

| Directory | Purpose |
|-----------|---------|
| `architecture/l0-infrastructure/` | Low-level infrastructure specs (ASIO, ring buffer, IPC) |
| `architecture/l1-security/` | Security layer specs (handshake, ACL, PID validation) |
| `architecture/l2-routing/` | Routing layer specs (REST dispatch, WS JSON-RPC) |
| `architecture/l3-presentation/` | Presentation layer specs (serialization, streaming) |
| `architecture/domains/` | Domain models (audio, EQ, effects, plugins) |
| `architecture/database/` | Persistence and state specs |
| `routes/` | Route definitions and handler registrations |
| `controllers/` | Controller implementations |
| `services/` | Service layer and business logic |
| `database/` | Database schemas and migrations |
| `security/` | Security policies and threat models |
| `specs/` | Technical specifications |
| `tests/` | Test plans and test suites |