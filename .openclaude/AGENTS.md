---
title: "CoreMusic — .openclaude Agent Talimatları"
type: agent-registry
folder: ".openclaude"
category: config
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# .openclaude — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./CLAUDE.md]]

## 1. Amaç

OpenClaude aracının proje konfigürasyonu. `.claude/` ve `.opencode/` ile birlikte **üçlü config yapısının** üçüncü halkasıdır. Rule seti daha daraltılmıştır (3 dosya).

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `settings.json` | OpenClaude proje ayarları |
| `rules/core-rules.md` | Genel çalışma kuralları |
| `rules/orchestration.md` | Agent orkestrasyon kuralları |
| `rules/vault.md` | Vault kuralları |

## 3. Agent Sorumlulukları

| Agent | Görev |
|-------|-------|
| Master Orchestrator | Üçlü config (`\.claude`, `.openclaude`, `.opencode`) tutarlılığını denetler |
| Vault Steward | Rule değişikliklerini onaylar |

## 4. Kurallar

### Zorunlu
1. `rules/core-rules.md` ve `rules/orchestration.md`, `.claude/rules/` karşılıklarıyla **anlamsal olarak senkron** tutulur (dosya sayısı farklı olsa da içerik çelişmez)
2. Anayasa her zaman `[[../.ai/CLAUDE.md]]`'dir

### Yasak
1. Bu klasörde skill'leri çoğaltmak (skills yalnızca `.claude/skills/` ve `.opencode/skills/`'te ikili yapı olarak yaşar)
2. `settings.json` içine secret yazmak
3. `rules/` dosyalarını onaysız silmek

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Claude Code ikili config | [[../.claude/AGENTS.md]] |
| OpenCode config | [[../.opencode/AGENTS.md]] |
| Anayasa | [[../.ai/CLAUDE.md]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
