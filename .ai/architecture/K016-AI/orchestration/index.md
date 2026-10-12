---
title: "K016-AI/orchestration — Subdomain Kaydı"
node_id: K016-AI/orchestration
node_type: subdomain
status: draft
parent_id: K016-AI
children: []        # kademe-3 bu fazda AÇILMADI (ADR-097 K4 — sonraki faz)
depends_on:
  - K016-AI          # domain kökü (tek bağımlılık bu fazda)
evidence:
  - ".ai/AGENTS.md §6 Keyword→Agent routing · §7 Task Dispatch · §9 Handover · §10 Escalasyon · §12 Context Lock"
validation:
  - "doküman okuması 2026-10-12"
open_questions:
  - "yok (bu tur için)"
---

# K016-AI/orchestration

## 1. Rol (ELI15)

Görev dağıtımı, ajanlar arası handover, eskalasyon ve context lock kuralları.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K016-AI (ajan altyapısı) altındadır — K006-yapay-zeka (ML ürün) kapsamı DEĞİLDİR.
- Kademe-3 (Bounded Context/Module) bu üretimde açılmadı; children bilerek boş bırakıldı.

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K016-AI) · master: ai-master.md |

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
