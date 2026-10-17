---
node_id: K015-AI/knowledge
node_type: subdomain
status: draft
parent_id: K015-AI
children:
  - knowledge-anayasa.md
  - knowledge-surucu.md
  - knowledge-ornekler.md
depends_on:
  - K015-AI          # domain kökü (tek bağımlılık bu fazda)
evidence:
  - ".ai/RAG.md (177 satır, §3 konu→dosya indeksi, okundu 2026-10-17)"
  - ".ai/keys.md + .ai/index.md + .ai/.decisions/index.md (okundu 2026-10-17)"
  - "shared/src/AI/KnowledgeBase.php (lifecycle/search/ragQuery kod kanıtı, künyeli)"
validation:
  - "disk okuması 2026-10-17 — vault + PHP kanıtları gerçek ölçümlü; içerik 3/3 üretildi (524/618/518)"
open_questions:
  - "shared/AGENTS.md 'architecture kaldırıldı' notu ↔ bu üretim yolu (knowledge-surucu Q4) — dosya yolu değiştirilmedi, kapsam kararı üstte"
  - "RAG pipeline IMPLEMENTED etiketi hâlâ yok (kod yüzeyi 0) → PLANNED (ADR-030 kapısı)"
group: mimari
---


# K015-AI/knowledge

## 1. Rol (ELI15)

Bilgi bankasının düzenlenmesi: hangi bilgi nerede tutulur, nasıl güncellenir.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K015-AI (ajan altyapısı) altındadır — K006-yapay-zeka (ML ürün) kapsamı DEĞİLDİR.
- Kademe-3 (Bounded Context/Module) bu üretimde açılmadı; çocuklar yalnız modül dosyalarıdır (3 adet).

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K015-AI) · master: ai-master.md |
| Çocuk modüller | knowledge-anayasa.md · knowledge-surucu.md · knowledge-ornekler.md (2026-10-17 üretildi) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `knowledge-anayasa.md` | Bilgi kuralları ve anayasal çekirdek | Kademe-2 |
| mimari | `knowledge-surucu.md` | Arama/yazma akışının sürüm karar ağaçları | Kademe-2 |
| kod | `knowledge-ornekler.md` | Bilgi kod örnekleri (kanıt odaklı) | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
