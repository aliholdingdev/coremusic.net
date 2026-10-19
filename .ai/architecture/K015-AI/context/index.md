---
node_id: K015-AI/context
node_type: subdomain
status: draft
parent_id: K015-AI
children:
  - context-anayasa.md
  - context-surucu.md
  - context-ornekler.md
depends_on:
  - K015-AI          # domain kökü (tek bağımlılık bu fazda)
evidence:
  - ".ai/AGENTS.md §24 (Ultra Thinking) + §13 (Reading Order) + §12 (Context Lock, okundu 2026-10-17)"
  - "root CLAUDE.md + root AGENTS.md §14 (boot zinciri kanıtı)"
  - ".ai/scripts/*.ps1 (6 betik, context-ornekler'de künyeli alıntı)"
validation:
  - "disk okuması 2026-10-17 — boot/lock/ölçüm kanıtları gerçek; içerik 3/3 üretildi (529/576/771)"
open_questions:
  - "shared/src/** grep Lock = 0 isabet — context lock kod yüzeyi PLANNED (context-surucu §11 dürüst negatif kanıt)"
group: mimari
---


# K015-AI/context

## 1. Rol (ELI15)

Bağlam mühendisliği: her oturumda ne okunur, boot paketi nasıl kurulur, context nasıl korunur.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K015-AI (ajan altyapısı) altındadır — K006-yapay-zeka (ML ürün) kapsamı DEĞİLDİR.
- Kademe-3 (Bounded Context/Module) bu üretimde açılmadı; çocuklar yalnız modül dosyalarıdır (3 adet).

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K015-AI) · master: ai-master.md |
| Çocuk modüller | context-anayasa.md · context-surucu.md · context-ornekler.md (2026-10-17 üretildi) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `context-anayasa.md` | Bağlam kuralları ve anayasal çekirdek | Kademe-2 |
| mimari | `context-surucu.md` | Bağlam hazırlama akışının sürüm karar ağaçları | Kademe-2 |
| kod | `context-ornekler.md` | Bağlam kod örnekleri (kanıt odaklı) | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
