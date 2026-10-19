---
node_id: K015-AI/rag
node_type: subdomain
status: draft
parent_id: K015-AI
children:
  - rag-anayasa.md
  - rag-surucu.md
  - rag-ornekler.md
depends_on:
  - K015-AI          # domain kökü (tek bağımlılık bu fazda)
evidence:
  - ".ai/RAG.md diskte VAR — 177 satır, status: active, 2026-10-12 (okundu 2026-10-17)"
  - "ADR-030-ai-strategy-core.md — 389 satır, pipeline PLANNED (kod 0 → PLANNED kanıtı)"
validation:
  - "disk okuması 2026-10-17 — .ai/RAG.md + ADR-030 gerçek ölçümlü"
open_questions:
  - "RAG pipeline IMPLEMENTED etiketi hâlâ yok (kod yüzeyi 0) → PLANNED (ADR-030 kapısı)"
group: mimari
---


# K015-AI/rag

## 1. Rol (ELI15)

Bilgi tabanından getirme (retrieval-augmented generation): soruya uygun dokümanı bulup bağlama ekleme.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K015-AI (ajan altyapısı) altındadır — K006-yapay-zeka (ML ürün) kapsamı DEĞİLDİR.
- Kademe-3 (Bounded Context/Module) bu üretimde açılmadı; çocuklar yalnız modül dosyalarıdır (3 adet).

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K015-AI) · master: ai-master.md |
| Çocuk modüller | rag-anayasa.md · rag-surucu.md · rag-ornekler.md (2026-10-17 üretildi) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `rag-anayasa.md` | Retrieval kuralları ve anayasal çekirdek | Kademe-2 |
| mimari | `rag-surucu.md` | Retrieval kullanım sürucusu (karar ağaçları) | Kademe-2 |
| kod | `rag-ornekler.md` | Retrieval kod örnekleri (kanıt odaklı) | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
