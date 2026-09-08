---
title: "CoreMusic — prompt Agent Talimatları"
type: agent-registry
folder: "prompt"
category: config
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# prompt — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./CLAUDE.md]]

## 1. Amaç

Proje planlama promptlarının arşivi. Vault dışındaki tek planlama dokümanı konumu; ADR-035/ADR-036 (System Prompt Engineering, Multi-Project Prompt Maker) kararlarının uygulama girdileridir.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `coremusic-api-plan-prompt.md` | API gateway planlama promptu (ADR-084 bağlamı) |
| `coremusic-electronci-vault-design-plan.md` | Elektronik vault tasarım planı (ADR-061..064 bağlamı) |

## 3. Agent Sorumlulukları

| Agent | Görev |
|-------|-------|
| Master Orchestrator | Plan promptlarının yürütülüp yürütülmediğini ADR'lere karşı denetler |
| Prompt Engineer | Yeni plan promptu → ADR-036 formatına uyum |

## 4. Kurallar

### Zorunlu
1. Yeni prompt dosyası adı: `coremusic-<konu>-plan-prompt.md` pattern'i
2. Prompt tamamlandıysa (uygulandıysa) dosyanın başına durum notu eklenir, dosya silinmez

### Yasak
1. Bu klasörü çalışma hafızası (session notes) olarak kullanmak — o iş `.ai/memory/sessions/`'in
2. Prompt içinde API anahtarı, token, secret taşımak

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Prompt arşivi (tarihsel) | [[../.ai/archives/]] |
| Prompt-maker skill | [[../.opencode/skills/prompt-maker/SKILL.md]] |
| ADR-035 | [[../.ai/decisions/accepted/ADR-035-system-prompt-engineering.md]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
