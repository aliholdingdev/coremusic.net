---
title: "K016-AI/permissions — Subdomain Kaydı"
node_id: K016-AI/permissions
node_type: subdomain
status: draft
parent_id: K016-AI
children: []        # kademe-3 bu fazda AÇILMADI (ADR-097 K4 — sonraki faz)
depends_on:
  - K016-AI          # domain kökü (tek bağımlılık bu fazda)
evidence:
  - ".ai/AGENTS.md §5 Domain Boundaries — 12 dosya tipi sahipliği tablosu"
  - ".claude/settings.json · .claude/settings.local.json — yetki ayarları (varlık, glob 2026-10-12)"
validation:
  - "doküman okuması + glob 2026-10-12"
open_questions:
  - "yok (bu tur için)"
group: mimari
---


# K016-AI/permissions

## 1. Rol (ELI15)

Ajan yetki matrisi: hangi ajan hangi dosya tipine/klasöre dokunabilir, yasaklar.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K016-AI (ajan altyapısı) altındadır — K006-yapay-zeka (ML ürün) kapsamı DEĞİLDİR.
- Kademe-3 (Bounded Context/Module) bu üretimde açılmadı; children bilerek boş bırakıldı.

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K016-AI) · master: ai-master.md |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
