---
title: "CoreMusic — .opencode Agent Talimatları"
type: agent-registry
folder: ".opencode"
category: config
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# .opencode — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[./CLAUDE.md]]

## 1. Amaç

OpenCode aracının proje konfigürasyonu. Skills kümesinin **orijinal kaynağı** burasıdır (`.claude/skills/` bu klasörden miraslanır). Rules dosyaları oturum davranışını düzenler.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `opencode.json` | OpenCode ayarları |
| `opencode.json.bak` | Ayar yedek kopyası |
| `package.json`, `package-lock.json` | OpenCode eklenti/skill bağımlılıkları |
| `.gitignore` | Klasör seviyesi yok sayma kuralları |
| `rules/emoji-yasak.md` | Emoji kullanım yasağı |
| `rules/frontend-png-rules.md` | PNG mockup sadakat kuralları |
| `rules/vault-loading.md` | Vault yükleme sırası |
| `rules/vault-trust.md` | Vault güven protokolü |
| `plans/` | Plan çıktı arşivi |
| `skills/` | 10 skill klasörü (SKILL.md + references + scripts + templates) — orijinal |

## 3. Agent Sorumlulukları

| Agent | Görev |
|-------|-------|
| Master Orchestrator | Skills doğruluğunu denetler; `.claude/skills/` ile diff tutarlılığı sağlar |
| Vault Steward | `rules/*.md` ve `opencode.json` değişikliklerini onaylar |
| Skill Maker | Yeni skill eklerken `[[./skills/skill-maker/SKILL.md]]` akışını izler |

## 4. Kurallar

### Zorunlu
1. Yeni skill **önce** `.opencode/skills/`'e yazılır, sonra `.claude/skills/`'e kopyalanır (yön: opencode → claude)
2. `opencode.json` değişikliği öncesi `.bak` kopyası güncellenir
3. `node_modules/` commit edilmez

### Yasak
1. `skills/*/SKILL.md` dosyalarını onaysız silmek
2. `opencode.json` içine API anahtarı yazmak
3. `plans/` arşivini canlı akışla karıştırmak

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| Claude Code ikili yapı | [[../.claude/AGENTS.md]] |
| Anayasa | [[../.ai/CLAUDE.md]] |
| Skill template | [[../.ai/.templates/documentation/WikiPage-Template.md]] |
| Skill-maker akışı | [[./skills/skill-maker/SKILL.md]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
