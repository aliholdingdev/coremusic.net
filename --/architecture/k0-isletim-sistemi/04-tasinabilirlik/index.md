---
title: "k0/04-tasinabilirlik — Taşınabilirlik & Cross-Platform API"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/k0-isletim-sistemi/04-tasinabilirlik/index.md"
updated: 2026-10-07
tier: 3
domain: k0-isletim-sistemi
ssot: true
risk: medium
owner: "win-sw"
depends-on: [".ai/architecture/k0-isletim-sistemi/index.md"]
---

# k0 · 04-tasinbirlik — Taşınabilirlik & Cross-Platform API

**Kapsam:** 4 platformun üzerindeki **tek ortak arayüz** — üst katmanların (K3/K8/K13) platform bilmeden çağıracağı façade.

## Karar Adayları (PLANNED — onay kapıları R10)

| Aday | Model | Artı | Eksi | Web kanıtı |
|---|---|---|---|---|
| **libuv** | C, reactor + threadpool | Node olgunluğu, IOCP/epoll/kqueue tek API, io_uring fs offload | Callback stili | ✅ 2026 survey |
| **Boost.Asio** | C++ proactor | C++20 coroutine, native IOCP/io_uring | Boost bağımlılığı | ✅ resmi docs |
| **POSIX pthread + özel** | std | sıfır bağımlılık | Windows portu elde | ✅ |
| **SDL2** | cross-platform | medya odaklı | OS servis kapsamı dar | ✅ |

**Öneri (henüz onaysız):** audio dışı genel amaç için libuv, K3 ses yolu için özel lock-free katman — **karar = ADR gerektirir → `.ai/.decisions/` (bu dizinde ADR yazılmaz, R1).**

**Taşınabilirlik sözleşmesi:** `k0-cross-platform-api` arayüzü 4 platformda aynı imzalar; platform-specific semboller bu katmanın **altına** sızamaz (layer violation → revert, `.ai/AGENTS.md` §5).
**Envanter karşılığı:** K0.04.zzz serisi.
