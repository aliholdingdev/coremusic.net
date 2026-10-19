---
node_id: K015-AI/kg
node_type: subdomain
status: draft
parent_id: K015-AI
children:
  - kg-anayasa.md
  - kg-surucu.md
  - kg-ornekler.md
depends_on:
  - K015-AI          # domain kökü (tek bağımlılık bu fazda)
evidence:
  - ".ai/architecture/index.md (K-düğümleri master graph, okundu 2026-10-17)"
  - ".ai/scripts/wiki-link-check.ps1 (43 satır) + kalip-abc-check.ps1 (231 satır) — graph doğrulama betik kanıtı"
  - "shared/src/AI/KnowledgeBase.php + Contracts/AI interface (kod kanıtı, künyeli)"
validation:
  - "disk okuması 2026-10-17 — graph/betik/PHP kanıtları gerçek ölçümlü; içerik 3/3 üretildi (578/556/654)"
open_questions:
  - "gerçek graph algoritması .ai/scripts/** ve shared/src/** = 0 satır (false-positive ayıklanmış: typography⊃graph, Knowledge⊃edge) — graph kodu PLANNED"
  - "SpaBff.php 'relational graphs' yalnız yorum + placeholder (L28-L62) — kod yüzeyi yok"
group: mimari
---


# K015-AI/kg

## 1. Rol (ELI15)

Knowledge graph: kavramlar ve dosyalar arası ilişki grafiği (wiki-link'lerin taşıyıcısı).

## 2. Kapsam Sınırı

- Bu düğüm yalnız K015-AI (ajan altyapısı) altındadır — K006-yapay-zeka (ML ürün) kapsamı DEĞİLDİR.
- Kademe-3 (Bounded Context/Module) bu üretimde açılmadı; çocuklar yalnız modül dosyalarıdır (3 adet).

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K015-AI) · master: ai-master.md |
| Çocuk modüller | kg-anayasa.md · kg-surucu.md · kg-ornekler.md (2026-10-17 üretildi) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `kg-anayasa.md` | Graph kuralları ve anayasal çekirdek | Kademe-2 |
| mimari | `kg-surucu.md` | Traverser/ekleme akışının sürüm karar ağaçları | Kademe-2 |
| kod | `kg-ornekler.md` | Graph kod örnekleri (betik kanıtlı) | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
