---
title: "k0 İşletim Sistemi — README"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "Derived: .ai/architecture/k0-isletim-sistemi/index.md (SSOT)"
updated: 2026-10-07
tier: 3
domain: k0-isletim-sistemi
ssot: false
risk: low
owner: "win-sw"
depends-on: [".ai/architecture/k0-isletim-sistemi/index.md"]
---

# k0 İşletim Sistemi — README

**Bu dizinde ne var?** CoreMusic K0 katmanının dosyaları: girişi `index.md` · kurallar `../../rules.md` · süreç `WORKFLOW.md` · kapsam kuralları `AGENTS.md` · anayasa özeti `CLAUDE.md`.

```text
k0-isletim-sistemi/
├── index.md                 ← SSOT girişi (önce bunu oku)
├── README.md                ← bu dosya
├── AGENTS.md · CLAUDE.md · WORKFLOW.md
├── 01-platformlar/          Windows/Linux/macOS/RPi5 çekirdek
├── 02-cekirdek-mekanizmalar/  IPC, thread, bellek, process, syscalls
├── 03-guvenlik-izolasyon/   sandbox, seccomp, capabilities, namespaces
├── 04-tasinabilirlik/       cross-platform API, portability katmanı
└── inventory/               Kx.yy.zzz bileşen envanteri (12 alan)
```

**Okuma sırası:** `index.md` → ilgili `0N-…` → `inventory/*-partNN.md` (P2/P3 lazy — her şeyi okuma).
**Değişiklik:** kural = [[architecture/rules]] · onay = R10 (katman başına) · kapı = R11 (tam tarama).
