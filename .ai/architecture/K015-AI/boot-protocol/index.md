---
node_id: K015-AI/boot-protocol
node_type: subdomain
status: draft
parent_id: K015-AI
children:
  - boot-protocol-anayasa.md
  - boot-protocol-surucu.md
  - boot-protocol-ornekler.md
depends_on:
  - K015-AI          # domain kökü (tek bağımlılık bu fazda)
evidence:
  - ".ai/MEMORY.md §5 (20 adım boot listesi, okundu 2026-10-17)"
  - ".claude/hooks/*.cjs (3 dosya: skill-mandate 195 · ai-risk-gate 73 · ai-validate-post 44 satır, künyeli alıntı)"
validation:
  - "disk okuması 2026-10-17 — hook kanıtları gerçek ölçümlü; içerik 3/3 üretildi (570/541/662)"
open_questions:
  - ".ai/MEMORY.md mojibake VAR (1063 × U+00C3 + 441 × U+00E2; .ai/AGENTS.md = 0) — Vault Steward yetkisi, düzeltilmedi (surucu §6/§17)"
  - "brain.md 1115 satır > 1000 limit — boot bütçe aşımı adayı (surucu §17)"
  - "boot-protocol-anayasa.md içerik eşlemesi (paralel üretim) ⚠️ VERIFICATION REQUIRED"
group: mimari
---


# K015-AI/boot-protocol

## 1. Rol (ELI15)

Oturum/agent açılış protokolü: sıralı zorunlu okuma, hook tetikleyicileri, kapılar.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K015-AI (ajan altyapısı) altındadır — K006-yapay-zeka (ML ürün) kapsamı DEĞİLDİR.
- Kademe-3 (Bounded Context/Module) bu üretimde açılmadı; çocuklar yalnız modül dosyalarıdır (3 adet).

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K015-AI) · master: ai-master.md |
| Çocuk modüller | boot-protocol-anayasa.md · boot-protocol-surucu.md · boot-protocol-ornekler.md (2026-10-17 üretildi) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `boot-protocol-anayasa.md` | Boot kuralları ve anayasal çekirdek | Kademe-2 |
| mimari | `boot-protocol-surucu.md` | Oturum başı akışının sürüm karar ağaçları | Kademe-2 |
| kod | `boot-protocol-ornekler.md` | Boot kod örnekleri (hook kanıtlı) | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
