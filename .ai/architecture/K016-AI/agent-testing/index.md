---
title: "K016-AI/agent-testing — Subdomain Kaydı"
node_id: K016-AI/agent-testing
node_type: subdomain
status: draft
parent_id: K016-AI
children: []        # kademe-3 bu fazda AÇILMADI (ADR-097 K4 — sonraki faz)
depends_on:
  - K016-AI          # domain kökü (tek bağımlılık bu fazda)
evidence:
  - "# bu turda disk kanıtı TOPLANMADI — kanıt bekliyor"
validation:
  - "okuma/gözlem YOK — verify-required"
open_questions:
  - "agent-testing/ vs K019-testing/ sınırı: ajan davranışı burada, genel test altyapısı K019'da (⚠️ VERIFICATION REQUIRED)"
group: mimari
---


# K016-AI/agent-testing

## 1. Rol (ELI15)

Ajanın kendisinin sınanması: davranış testleri, eval setleri, regresyon kontrolü.

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
