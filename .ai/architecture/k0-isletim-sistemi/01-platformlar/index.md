---
title: "k0/01-platformlar — Platform Çekirdekleri"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/k0-isletim-sistemi/01-platformlar/index.md"
updated: 2026-10-07
tier: 3
domain: k0-isletim-sistemi
ssot: true
risk: medium
owner: "win-sw"
depends-on: [".ai/architecture/k0-isletim-sistemi/index.md"]
---

# k0 · 01-platformlar — Platform Çekirdekleri

**Kapsam:** Windows Core · Linux Core · macOS Core · RPi5 Core — her platformun çekirdek servis erişimi.
**Derinleşme gerekçesi (Q6/Q19):** 4 platform bağımsız alt-sistem → tek dizin içinde ayrı doclara bölündü.

> **ADR-019 bağı (okundu 2026-10-07):** Per-OS Neva Player [[.decisions/accepted/ADR-019-per-os-neva-player]] — ortak çekirdek + ince adapter; `IAudioBackend` arayüzü bu ADR'de tanımlanır (repo grep: 0 → PLANNED). Çekirdek/adapter sınırı k0/k2 ayrımını bağlar; backend seçim sırası: ASIO → WASAPI → Null. Eski spec: `windows-api.md:258` (ADR-019 kaydı) + `windows-core.md:237-244` (`\\.\pipe\CoreMusicIPC`) — backup referans (R1.3).## Alt Dosyalar

| Dosya | Platform | Kanıt durumu |
|---|---|---|
| `windows-core.md` | Win12 hedef — Process/Memory/Threading/Registry/Service/EventLog/WMI/COM/IPC/JobObjects/UAC | PLANNED (repo'da C++/WinRT kodu YOK) |
| `linux-core.md` | Lin10 — cgroups v2, namespaces, systemd, epoll, inotify, seccomp, capabilities | PLANNED |
| `macos-core.md` | Mac8 — GCD, XPC, LaunchAgent, App Sandbox, Hardened Runtime | PLANNED |
| `rpi5-core.md` | RPi5 — GPIO, DMA, PWM audio, I2S | PLANNED |

> ⚠️ Bu dosyalar henüz yazılmadı (Faz 1 kapsamı: envanter önce, detay docları hibrit script+elle ile Q38).

**Envanter karşılığı:** `inventory/k0-platformlar-part01.md` (K0.01.zzz serisi).
**İlişki:** `02-cekirdek-mekanizmalar` platform bağımsız mekanizmaları taşır — çakışma yasağı: platform-spezifik detay YALNIZ burada.
