---
node_id: K015-AI/evaluation
node_type: subdomain
status: draft
parent_id: K015-AI
children:
  - evaluation-anayasa.md
  - evaluation-surucu.md
  - evaluation-ornekler.md
depends_on:
  - K015-AI          # domain kökü (tek bağımlılık bu fazda)
evidence:
  - ".ai/AGENTS.md §24.4-§24.5 + §16 (kalite checklist + standartlar, okundu 2026-10-17)"
  - ".ai/scripts/wiki-link-check.ps1 (43 sat) + kalip-abc-check.ps1 (231 sat) + screens-frontmatter-check.ps1 — gate betik kanıtı"
validation:
  - "disk okuması 2026-10-17 — betik/kanıt ölçümleri gerçek; içerik 3/3 üretildi (557/662/868)"
open_questions:
  - "evaluation/ vs K006-evaluation/ sınırı kademe-3'te tescillenecek (⚠️ VERIFICATION REQUIRED — korunuyor)"
group: mimari
---


# K015-AI/evaluation

## 1. Rol (ELI15)

Ajan çıktısı değerlendirmesi: ajan performansı rubrikleri (model değil, ajan değerlendirilir).

## 2. Kapsam Sınırı

- Bu düğüm yalnız K015-AI (ajan altyapısı) altındadır — K006-yapay-zeka (ML ürün) kapsamı DEĞİLDİR.
- Kademe-3 (Bounded Context/Module) bu üretimde açılmadı; çocuklar yalnız modül dosyalarıdır (3 adet).

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K015-AI) · master: ai-master.md |
| Çocuk modüller | evaluation-anayasa.md · evaluation-surucu.md · evaluation-ornekler.md (2026-10-17 üretildi) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `evaluation-anayasa.md` | Değerlendirme kuralları ve anayasal çekirdek | Kademe-2 |
| mimari | `evaluation-surucu.md` | Değerlendirme akışının sürüm karar ağaçları | Kademe-2 |
| kod | `evaluation-ornekler.md` | Değerlendirme kod örnekleri (betik kanıtlı) | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
