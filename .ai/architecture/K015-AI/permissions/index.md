---
node_id: K015-AI/permissions
node_type: subdomain
status: draft
parent_id: K015-AI
children:
  - permissions-anayasa.md
  - permissions-surucu.md
  - permissions-ornekler.md
depends_on:
  - K015-AI          # domain kökü (tek bağımlılık bu fazda)
evidence:
  - ".ai/AGENTS.md §5 (12 dosya tipi, L93-108) + §10-§12 + §17-§18 (okundu 2026-10-17)"
  - "root AGENTS.md §16.4-§16.5 (dokunulmaz yüzey + commit yetkisi) · .claude/settings.json hook kapıları (künyeli) · shared/src/Log/Redactor.php (repo kanıtı)"
validation:
  - "disk okuması 2026-10-17 — yetki/hook/PHP kanıtları gerçek ölçümlü; içerik 3/3 üretildi (630/514/760)"
open_questions:
  - "layer violation log seviyesi çelişkisi: CRITICAL (.ai/CLAUDE.md:184) ↔ ERROR (.ai/AGENTS.md:207) — anayasa §13-§14 işaretli, düzeltilmedi"
  - "settings.local.json içeriği okunmadı (⚠️ VERIFICATION REQUIRED) · settings.json'da `permissions` bloğu YOK (UNKNOWN)"
group: mimari
---


# K015-AI/permissions

## 1. Rol (ELI15)

Ajan yetki matrisi: hangi ajan hangi dosya tipine/klasöre dokunabilir, yasaklar.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K015-AI (ajan altyapısı) altındadır — K006-yapay-zeka (ML ürün) kapsamı DEĞİLDİR.
- Kademe-3 (Bounded Context/Module) bu üretimde açılmadı; çocuklar yalnız modül dosyalarıdır (3 adet).

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K015-AI) · master: ai-master.md |
| Çocuk modüller | permissions-anayasa.md · permissions-surucu.md · permissions-ornekler.md (2026-10-17 üretildi) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `permissions-anayasa.md` | Yetki kuralları ve anayasal çekirdek | Kademe-2 |
| mimari | `permissions-surucu.md` | Yetki kontrol akışının sürüm karar ağaçları | Kademe-2 |
| kod | `permissions-ornekler.md` | Yetki kod örnekleri (JSON/PHP kanıtlı) | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
