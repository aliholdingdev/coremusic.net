---
node_id: K015-AI/mcps
node_type: subdomain
status: draft
parent_id: K015-AI
children:
  - mcps-anayasa.md
  - mcps-surucu.md
  - mcps-ornekler.md
depends_on:
  - K015-AI          # domain kökü (tek bağımlılık bu fazda)
evidence:
  - ".claude/mcp.json (9 satır, 1 sunucu exa-mcp-server aktif — 2026-10-17 TAM okuma)"
  - ".claude/settings.json mcpServers L48-L99 (filesystem/chrome-devtools/claude-mem aktif · browsermcp/playwright/exa disabled) + skill-mandate.cjs künyeli alıntı"
validation:
  - "disk okuması 2026-10-17 — mcp.json/settings.json/hook kanıtları gerçek ölçümlü; içerik 3/3 üretildi (553/537/519)"
open_questions:
  - "çift yüzey exa çakışması: mcp.json exa-mcp-server AKTİF ↔ settings.json exa disabled:true — hangi yüzeyle kapatılacak (anayasa §6-§7, HIGH)"
  - "EXA_API_KEY mcp.json:6 + settings.json:95'te düz metin (REDACTED) — env'e taşınması onay bekliyor (anayasa §14, HIGH)"
group: mimari
---


# K015-AI/mcps

## 1. Rol (ELI15)

MCP (Model Context Protocol) sunucularının yapılandırması ve sürüm yönetimi.

## 2. Kapsam Sınırı

- Bu düğüm yalnız K015-AI (ajan altyapısı) altındadır — K006-yapay-zeka (ML ürün) kapsamı DEĞİLDİR.
- Kademe-3 (Bounded Context/Module) bu üretimde açılmadı; çocuklar yalnız modül dosyalarıdır (3 adet).

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K015-AI) · master: ai-master.md |
| Çocuk modüller | mcps-anayasa.md · mcps-surucu.md · mcps-ornekler.md (2026-10-17 üretildi) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `mcps-anayasa.md` | MCP kuralları ve anayasal çekirdek | Kademe-2 |
| mimari | `mcps-surucu.md` | Tool çağrım akışının sürüm karar ağaçları | Kademe-2 |
| kod | `mcps-ornekler.md` | MCP kod örnekleri (JSON kanıtlı) | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
