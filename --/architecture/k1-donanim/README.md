---
title: "k1 Donanım — README"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "Derived: .ai/architecture/k1-donanim/index.md (SSOT)"
updated: 2026-10-07
tier: 3
domain: k1-donanim
ssot: false
risk: low
owner: "audio-hw"
depends-on: [".ai/architecture/k1-donanim/index.md"]
---

# k1 Donanım — README

**Bu dizinde ne var?** CoreMusic K1 katmanının dosyaları: girişi `index.md` · kurallar `../../rules.md` · süreç `WORKFLOW.md` · kapsam kuralları `AGENTS.md` · anayasa özeti `CLAUDE.md`.

```text
k1-donanim/
├── index.md                 ← SSOT girişi (önce bunu oku)
├── README.md                ← bu dosya
├── AGENTS.md · CLAUDE.md · WORKFLOW.md
├── 01-…/02-…/               ← derinleşme (yalnız gerektiğinde, Q19 — henüz yok)
└── inventory/               ← K1.yy.zzz bileşen envanteri (12 alan, Faz 1+)
```

**Kapsam:** PCM3168A/AK4458 DAC zinciri · XMOS XU316 · analog giriş · Class AB çıkış · güç/termal koruma · PCB (k1 index §1).
**Sınır:** sürücü (ASIO/WASAPI/ALSA) → `k2-surucu` · DSP → `k3-ses-motoru` · firmware → `firmware/` (AGENTS §3 D9).

**Okuma sırası:** `index.md` → ilgili derinleşme → `inventory/*-partNN.md` (P2/P3 lazy — her şeyi okuma).
**Değişiklik:** kural = [[architecture/rules]] · onay = R10 (katman başına) · kapı = R11 (tam tarama).