---
title: "l6-electronics — Eski L6 Elektronik Katmanı (stub-with-truth)"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md — bu dosya yalnız eski link uyumu stub'udur"
updated: 2026-10-07
tier: 3
domain: architecture-layers
ssot: false
risk: low
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# l6-electronics — L6 Elektronik Katmanı (stub)

**Durum:** `architecture/l6-electronics(.md)` **diskte YOK** (eski ağaç silindi) — 4 link referansı.

## Bugünkü Karşılığı (gerçek kanıt)

| İçerik | Kaynak | Durum |
|--------|--------|-------|
| Class AB amplifikatör tasarımı | ADR-089 · `.ai/brain.md` §5 (H1) | DESIGN |
| Güç kaynağı ±35V (LM5122) | ADR-089 · `.claude/CLAUDE.md` §5 (K17) | DESIGN |
| Termal tasarım | `.claude/CLAUDE.md` §5 (K18) | DESIGN |
| PCB tasarımı | `.claude/CLAUDE.md` §5 (K19) | DESIGN |
| BOM & üretim | `.claude/CLAUDE.md` §5 (K20) · ⚠️ bom-classab.md dosyası silinmiş | DESIGN |
| Elektronik mimari kararları | ADR-061 · ADR-063 · ADR-064 · ADR-090 | DESIGN |

⚠️ Eski `electronics/*` dokümanları (`amplifier-classab-circuit.md`, `pcb-classab.md`,
`thermal-design-classab.md`, `power-supply-classab.md`) — `.ai/brain.md` §5 Electronics Registry
bunları referans verir ama **diskte YOK** (2026-10-07 glob: 0 isabet).

**Modern karşılık:** [[architecture/19-domain-d10-elektronik-tasarim]] (K450–K499)
