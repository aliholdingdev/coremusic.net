---
title: "k0/02-cekirdek-mekanizmalar — Çekirdek Mekanizmalar"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/k0-isletim-sistemi/02-cekirdek-mekanizmalar/index.md"
updated: 2026-10-07
tier: 3
domain: k0-isletim-sistemi
ssot: true
risk: medium
owner: "win-sw"
depends-on: [".ai/architecture/k0-isletim-sistemi/index.md"]
---

# k0 · 02-cekirdek-mekanizmalar — Çekirdek Mekanizmalar

**Kapsam:** IPC · Threading Model · Memory Management · Process Isolation · System Calls — **platform-bağımsız** ortak mekanizmalar (cross-platform API'nin sırtı).

## Alt Dosyalar (PLANNED — repo kanıtı YOK, 2026-10-07)

| Dosya | İçerik | Web doğrulama (2026-10-07) |
|---|---|---|
| `ipc-mekanizmalari.md` | Unix Domain Socket, Named Pipe, Shared Memory, Message Queue, gRPC | ✅ güncel |
| `threading-model.md` | Thread pool, lock-free, atomic, condvar, mutex hiyerarşisi | ✅ GCD/kqueue/io_uring araştırması |
| `memory-management.md` | Virtual memory, pool, slab, buffer management | ✅ |
| `process-isolation.md` | Sandbox, capability drop, chroot, namespaces, seccomp-bpf | → 03 ile ortak sınır |
| `system-calls.md` | POSIX syscall, NT API, io_uring, epoll/kqueue | ✅ proactor/reactor araştırması |

> **ADR-032 bağı (okundu 2026-10-07):** IPC şema/sürüm/geriye-uyumluluk sözleşmesi [[.decisions/accepted/ADR-032-ipc-contract-versioning]] SSOT'unda. Durum: sürümleme HTTP yüzeyinde IMPLEMENTED (shared/src/Api/Versioning/VersionResolver.php:34-48) · süreç-arası IPC mesajlaşma PLANNED (.proto/.cpp = 0). Eski spec satırları: `ipc-mekanizmalari.md:83,94` (IPCMessageHeader) — backup referans, kopya DEĞİL (R1.3).**Sınır kuralı:** `process-isolation` teknik listesi burada, **politika** (hangisi zorunlu) `03-guvenlik-izolasyon` + K6'da.
**Envanter karşılığı:** `inventory/k0-platformlar-part02.md` (K0.02.zzz serisi).
**Teknoloji notu (web-doğrulanmış):** libuv = reactor (epoll/kqueue/IOCP + io_uring fs offload) · Boost.Asio = proactor (native IOCP/io_uring) · tokio = work-stealing reactor.
