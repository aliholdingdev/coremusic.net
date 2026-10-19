---
node_id: K015-AI/agents
node_type: subdomain
status: draft
parent_id: K015-AI
children:
  - anayasa
  - surucu
  - ornekler
depends_on:
  - K015-AI          # domain kökü (tek bağımlılık bu fazda)
evidence:
  - ".ai/.agents/ = 12 .md (11 uzman profili + AGENTS.md indeksi) — glob 2026-10-12"
  - ".claude/agent/ dizini — profil .md'leri mevcut (tam sayı UNKNOWN, liste 100 sonuçta kırptı) — glob 2026-10-12"
  - "shared/src/AI/AIOrchestrator.php:26-38 AGENT_MAP (11 ajan)"
validation:
  - "disk sayımı 2026-10-12"
  - "tur-3 (2026-10-13): 3 içerik dosyası üretildi (>=500 gate)"
open_questions:
  - ".claude/agent/ TAM profil sayısı (UNKNOWN)"
  - "11 proje ajanı ile 118 global registry ajanının eşleme tablosu vault'ta yok mu? (⚠️ VERIFICATION REQUIRED)"
group: mimari
---


# K015-AI/agents

## 1. Rol (ELI15)

Ajanların kimliği: hangi ajan var, hangi alanda uzman, hangi dosyaya dokunabilir.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K015-AI (ajan altyapısı) altındadır — K006-yapay-zeka (ML ürün) kapsamı DEĞİLDİR.
- Kademe-3 (Bounded Context/Module) bu üretimde açılmadı; children bilerek boş bırakıldı.

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K015-AI) · master: ai-master.md |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |

## §TUR-3 (2026-10-13) — Kademe-3 Açılışı

> §2'deki «children bu fazda boş» ifadesi tur-3 itibarıyla **kapanmıştır**; 3 içerik
> dosyası üretilmiştir (ADR-097 K4 kapsam değişikliği değil, planlanan kademenin açılması).

| # | Dosya | group | Eşik | Durum |
|---|---|---|---|---|
| 1 | anayasa.md | mimari | >=500 | üretildi |
| 2 | surucu.md | mimari | >=500 | üretildi |
| 3 | ornekler.md | kod | >=500 | üretildi |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
