---
title: "CoreMusic — .claude Agent Talimatları"
type: agent-registry
folder: ".claude"
category: config
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# .claude — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] · [[../CLAUDE.md]] · [[./CLAUDE.md]]

## 1. Amaç

Bu klasör, **Claude Code** aracının proje konfigürasyonunu barındırır. Rules dosyaları Claude Code oturumlarına otomatik yüklenen davranış kurallarını içerir.

## 2. İçerik Envanteri

| Yol | Amaç |
|-----|------|
| `settings.json` | Claude Code proje ayarları |
| `rules/core-rules.md` | Genel çalışma kuralları |
| `rules/emoji-yasak.md` | Emoji kullanım yasağı |
| `rules/frontend-png-rules.md` | PNG mockup sadakat kuralları |
| `rules/orchestration.md` | Agent orkestrasyon kuralları |
| `rules/vault-loading.md` | Vault yükleme sırası kuralları |
| `rules/vault-trust.md` | Vault güven protokolü |
| `rules/vault.md` | Vault genel kuralları |
| `skills/` | 10 skill klasörü (SKILL.md + references + templates) — `.opencode/skills/` ile ikili (mirrored) yapı |

## 3. Agent Sorumlulukları

| Agent | Görev |
|-------|-------|
| Master Orchestrator | Bu klasördeki rule dosyalarının Claude Code oturumlarına yüklendiğini doğrular |
| Vault Steward | `rules/*.md` değişikliklerini onaylar; `.opencode/rules/` ile senkron kontrolü yapar |

## 4. Kurallar

### Zorunlu
1. `rules/` altındaki her dosya değişikliği, `.opencode/rules/` karşılığıyla **senkron** tutulmalıdır (emoji-yasak, frontend-png-rules, vault-loading, vault-trust ortak dosyalardır)
2. Skills değişikliği iki konuma da uygulanır: `.claude/skills/` ve `.opencode/skills/`
3. `settings.json` içinde secret yer almaz (Guardrail: ENV Only)

### Yasak
1. `settings.json` içine credential yazmak
2. `rules/` dosyalarını silmek (yalnızca güncelleme; silme için kullanıcı onayı)
3. `.archive` klasörlerini canlı akışa döndürmek

## 5. İlgili Kaynaklar

| Kaynak | Yol |
|--------|-----|
| OpenCode ikili yapı | [[../.opencode/AGENTS.md]] |
| Vault anayasası | [[../.ai/CLAUDE.md]] |
| Skills kaynak (orijinal) | [[../.opencode/skills/AGENTS.md]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
