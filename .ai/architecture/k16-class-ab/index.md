---
title: "Architecture — Class AB Amplifikatör"
type: docs
category: architecture
version: 0.1.0
status: draft
authority: "SSOT: .ai/architecture/k16-class-ab/index.md (katman girişi — içerik Faz 1+)"
updated: 2026-10-07
tier: 3
domain: k16-class-ab
ssot: true
risk: medium
owner: "audio-hw"
depends-on: [".ai/architecture/00-enterprise-index.md", ".ai/architecture/rules.md"]
---

# Class AB Amplifikatör — Katman Girişi

> **Durum:** 🅿️ STUB (Faz 0 iskeleti) — içerik **sıfırdan**, kod + web kanıtıyla Faz 1+ kapsamında yazılacak (Q8: kopyalama YOK · Q11: hibrit kaynak).

## 1. Kapsam

MJL21194/93 Darlington, 50W/kanal, 8 kanal modüler, THD <0.005%, enable pinli bağımsız kanal.

- **Sahip agent:** audio-hw
- **K-matrix referansı:** [[CLAUDE]] §5 (K-satırı ile hizalanacak)
- **Kurallar (SSOT):** [[architecture/rules]]
- **Katalog:** [[architecture/00-enterprise-index]]

## 2. Bu Katmanda Ne Yaşayacak (iskelet)

```text
k16-class-ab/
├── index.md            # bu dosya — arc42-tam + vault 8-iskelet hibrit (Q25)
├── README.md · AGENTS.md · CLAUDE.md · WORKFLOW.md   # tam set (Q10)
├── 01-…/02-…/          # derinleşme — yalnız gerektiğinde (Q19)
└── inventory/          # tür-bazlı çoklu-md (min 500 satır/md, Q17/Q21)
```

## 3. Envanter

Henüz yok. Faz 1+ kapı onayı sonrası: repo taraması + web (deepwiki/exa) + K-matrix iskeletiyle doldurulur.
Bileşen ID'leri `Kx.yy.zzz` (Q22) · 12 alan (Q26) · doğrulanmayan satır = `UNKNOWN`.

## 4. Sonraki Adım

Faz planı: [[architecture/00-enterprise-index]] §5 · Tarama kapıları: [[architecture/rules]] R11.
