---
node_id: K015-AI/memory
node_type: subdomain
status: draft
parent_id: K015-AI
children:
  - memory-anayasa.md
  - memory-surucu.md
  - memory-ornekler.md
depends_on:
  - K015-AI          # domain kökü (tek bağımlılık bu fazda)
evidence:
  - ".ai/MEMORY.md diskte VAR — 728 satır (okundu 2026-10-17)"
  - "shared/src/AI/MemorySystem.php — HIERARCHY L25-31, L1/L2/L3 L18-21 (kod kanıtı)"
validation:
  - "disk okuması 2026-10-17 — MEMORY.md + MemorySystem.php gerçek ölçümlü"
open_questions:
  - "MemorySystem üretim çağrı yüzeyi ve 10MB limit ölçümü (⚠️ VERIFICATION REQUIRED — anayasa §24)"
group: mimari
---


# K015-AI/memory

## 1. Rol (ELI15)

Ajan hafızası: oturumlar arası hatırlama, MEMORY.md ve kalıcı kayıt mekanizması.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K015-AI (ajan altyapısı) altındadır — K006-yapay-zeka (ML ürün) kapsamı DEĞİLDİR.
- Kademe-3 (Bounded Context/Module) bu üretimde açılmadı; çocuklar yalnız modül dosyalarıdır (3 adet).

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K015-AI) · master: ai-master.md |
| Çocuk modüller | memory-anayasa.md · memory-surucu.md · memory-ornekler.md (2026-10-17 üretildi) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `memory-anayasa.md` | Hafıza kuralları ve anayasal çekirdek | Kademe-2 |
| mimari | `memory-surucu.md` | Okuma/yazma akışının sürüm karar ağaçları | Kademe-2 |
| kod | `memory-ornekler.md` | Hafıza kod örnekleri (kanıt odaklı) | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
