---
title: "k0/03-guvenlik-izolasyon — Güvenlik & İzolasyon"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/k0-isletim-sistemi/03-guvenlik-izolasyon/index.md"
updated: 2026-10-07
tier: 3
domain: k0-isletim-sistemi
ssot: true
risk: high
owner: "security-engineer"
depends-on: [".ai/architecture/k0-isletim-sistemi/index.md", ".ai/architecture/k6-guvenlik/index.md"]
---

# k0 · 03-guvenlik-izolasyon — Güvenlik & İzolasyon

> **risk: high** — K6 güvenlik anayasasıyla (`.ai/CLAUDE.md`) hizalıdır; bu dosya **politika üretmez**, OS seviyesi mekanizmaları listeler. Politika = [[architecture/k6-guvenlik]] (SSOT).

**Kapsam (OS seviyesi savunma katmanı):**
- Process sandbox (chroot, namespace izolasyonu, seccomp-bpf syscall filtresi)
- Capability dropping (`capset`, minimum yetki)
- Windows: Job Objects, UAC ayrıştırma, AppContainer
- macOS: App Sandbox, Hardened Runtime, entitlements
- K6'ya bağlantı: auth bypass'ı OS seviyesinde de mümkün olmamalı (BypassAuth yalnız dev'de — ADR-008)

**Sınır kuralı:** `.ai/architecture/k6-guvenlik/index.md` bağlantı `.ai/architecture/k0-isletim-sistemi/index.md` bağlantı — yalnız mekanizma; CSRF/CSP/auth = K6.
**Durum:** PLANNED (repo'da sandbox/seccomp kodu kanıtı YOK).
**Envanter karşılığı:** K0.03.zzz serisi.
