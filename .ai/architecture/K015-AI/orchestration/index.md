---
node_id: K015-AI/orchestration
node_type: subdomain
status: draft
parent_id: K015-AI
children:
  - orchestration-anayasa.md
  - orchestration-surucu.md
  - orchestration-ornekler.md
depends_on:
  - K015-AI          # domain kökü (tek bağımlılık bu fazda)
evidence:
  - ".ai/AGENTS.md §6-§12 (routing/dispatch/handover/escalation/health/lock, okundu 2026-10-17)"
  - "shared/src/AI/AIOrchestrator.php — AGENT_MAP L26-38, TASK_TYPES L40-49 (kod kanıtı)"
validation:
  - "disk okuması 2026-10-17 — vault + PHP kanıtları gerçek; içerik 3/3 üretildi (559/973/884)"
open_questions:
  - "TASK_PRIORITY sabiti kodda YOK — öncelik TASK_TYPES satır içi priority alanıdır (orchestration-anayasa dürüst negatif)"
  - "AIOrchestrator.php retry/lock/health/escalation kodu 0 satır — vault kuralı vs kod PLANNED çelişkisi (surucu §16)"
  - "engine.md 11 keyword grubu ↔ AGENTS.md §6 9 satır farkı (⚠️ VERIFICATION REQUIRED)"
group: mimari
---


# K015-AI/orchestration

## 1. Rol (ELI15)

Görev dağıtımı, ajanlar arası handover, eskalasyon ve context lock kuralları.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K015-AI (ajan altyapısı) altındadır — K006-yapay-zeka (ML ürün) kapsamı DEĞİLDİR.
- Kademe-3 (Bounded Context/Module) bu üretimde açılmadı; çocuklar yalnız modül dosyalarıdır (3 adet).

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K015-AI) · master: ai-master.md |
| Çocuk modüller | orchestration-anayasa.md · orchestration-surucu.md · orchestration-ornekler.md (2026-10-17 üretildi) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `orchestration-anayasa.md` | Orkestrasyon kuralları ve anayasal çekirdek | Kademe-2 |
| mimari | `orchestration-surucu.md` | Dispatch/handover akışının sürüm karar ağaçları | Kademe-2 |
| kod | `orchestration-ornekler.md` | Orkestrasyon kod örnekleri (kanıt odaklı) | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
