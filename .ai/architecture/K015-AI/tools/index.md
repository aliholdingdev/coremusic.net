---
node_id: K015-AI/tools
node_type: subdomain
status: draft
parent_id: K015-AI
children:
  - tools-anayasa.md
  - tools-surucu.md
  - tools-ornekler.md
depends_on:
  - K015-AI          # domain kökü (tek bağımlılık bu fazda)
evidence:
  - "shared/src/AI/ToolCalling.php — AGENT_PERMISSIONS, checkPermission L145-166, callTool (kod kanıtı, 2026-10-17)"
  - "shared/src/AI/AIOrchestrator.php — AGENT_MAP L26-38, TASK_TYPES L40-49"
  - ".claude/hooks/*.cjs (3 gate dosyası) + .claude/settings.json (okundu 2026-10-17)"
validation:
  - "disk okuması 2026-10-17 — PHP + hook kanıtları gerçek ölçümlü; içerik 3/3 üretildi (522/759/781)"
open_questions:
  - "checkPermission (ToolCalling L145-166) callTool içinde çağrılmıyor — grep 0; izin kapısı çağrı noktasında değil (⚠️ VERIFICATION REQUIRED)"
  - "AIWorkflow L122 db-query ile UPDATE çağırıyor; executor yalnız SELECT kabul eder (ToolCalling L235-237) → çalışma anında success:false çelişkisi"
  - ".claude/settings.json EXA_API_KEY düz metin (değer REDACTED yazıldı) — secret yönetimi denetimi gerekli"
  - "shared/tests/** içinde ToolCalling/AIOrchestrator/checkPermission testi grep 0 — test kapsamı boşluğu"
group: mimari
---


# K015-AI/tools

## 1. Rol (ELI15)

Ajanların çağırabileceği araçların envanteri ve sözleşme kuralları (hangi araç ne yapar, sınırı nedir).

## 2. Kapsam Sınırı

- Bu düğüm yalnız K015-AI (ajan altyapısı) altındadır — K006-yapay-zeka (ML ürün) kapsamı DEĞİLDİR.
- Kademe-3 (Bounded Context/Module) bu üretimde açılmadı; çocuklar yalnız modül dosyalarıdır (3 adet).

## 3. Durum

| Alan | Değer |
|---|---|
| status | draft (kanıt durumu §frontmatter'da) |
| Üreten pay | K-Space V3 Band-1 — A4 (2026-10-12) |
| Domain kökü | [[index]] (K015-AI) · master: ai-master.md |
| Çocuk modüller | tools-anayasa.md · tools-surucu.md · tools-ornekler.md (2026-10-17 üretildi) |

## §GRUPLAR

> İki-ekseni gruplama (ADR-097 §5.5 · registry §8.1) — her dosyanın frontmatter'ında **9. alan `group`**.

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi ve çocuk kataloğu | Kademe-2 |
| mimari | `tools-anayasa.md` | Araç kuralları ve anayasal çekirdek | Kademe-2 |
| mimari | `tools-surucu.md` | Araç kullanım sürümü (karar ağaçları) | Kademe-2 |
| kod | `tools-ornekler.md` | Araç kod örnekleri (kanıt odaklı) | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
