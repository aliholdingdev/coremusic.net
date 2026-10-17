---
node_id: K015-AI/agent-testing
node_type: subdomain
status: draft
parent_id: K015-AI
children:
  - agent-testing-anayasa.md
  - agent-testing-surucu.md
  - agent-testing-ornekler.md
depends_on:
  - K015-AI          # domain kökü (tek bağımlılık bu fazda)
evidence:
  - ".ai/AGENTS.md §6 (9 keyword routing) + §7.6 + §11 Health + §16 QA standartları (okundu 2026-10-17)"
  - "shared/tests/ envanteri + .ai/scripts/wiki-link-check.ps1 (43 sat) + kalip-abc-check.ps1 (231 sat) — test/doğrulama kanıtı"
validation:
  - "disk okuması 2026-10-17 — routing/health/betik kanıtları gerçek ölçümlü; içerik 3/3 üretildi (542/904/565)"
open_questions:
  - "agent-testing/ vs K019-testing/ sınırı: ajan davranışı burada, genel test altyapısı K019'da (⚠️ VERIFICATION REQUIRED — korunuyor)"
  - "shared/tests/**'da ajan/routing testi YOK (PHP unit testler mevcut ama ajan davranış testi yok) — ajan testi PLANNED"
group: mimari
---


# K015-AI/agent-testing

## 1. Rol (ELI15)

Ajanın kendisinin sınanması: davranış testleri, eval setleri, regresyon kontrolü.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K015-AI (ajan altyapısı) altındadır — K006-yapay-zeka (ML ürün) kapsamı DEĞİLDİR.
- Kademe-3 (Bounded Context/Module) bu üretimde açılmadı; çocuklar yalnız modül dosyalarıdır (3 adet).

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K015-AI) · master: ai-master.md |
| Çocuk modüller | agent-testing-anayasa.md · agent-testing-surucu.md · agent-testing-ornekler.md (2026-10-17 üretildi) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `agent-testing-anayasa.md` | Ajan test kuralları ve anayasal çekirdek | Kademe-2 |
| mimari | `agent-testing-surucu.md` | Test akışının sürüm karar ağaçları | Kademe-2 |
| kod | `agent-testing-ornekler.md` | Ajan test kod örnekleri (envanter kanıtlı) | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
