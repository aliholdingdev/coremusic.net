---
title: "CoreMusic — .opencode Bağlam"
type: context
folder: ".opencode"
category: config
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# .opencode — CLAUDE.md

**Zorunlu Bağlantılar:** [[./AGENTS.md]] · [[../.ai/CLAUDE.md]]

## 1. Bağlam

OpenCode bu oturumda birincil çalışma aracıdır. `opencode.json` aracı başlatır, `rules/` davranışı, `skills/` uzmanlığı belirler. Skills'in orijinal kaynağı bu klasördür; `.claude/skills/` kopyadır.

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Rule sayısı | 4 |
| Skill sayısı | 10 |
| Skills kopya konumu | `[[../.claude/skills/]]` (yön: opencode → claude) |
| Arşiv | `plans/`, `skills/prompt-maker/.archive` (30 dosya, canlı değil) |
| Bilinen risk | `opencode.json.bak` — manuel yedek, otomatik senkron yok |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../AGENTS.md]] | Kök registry |
| Twin | [[../.claude/CLAUDE.md]] | Claude Code kopya skills |
| Kardeş | [[../.openclaude/CLAUDE.md]] | Üçüncü config halkası |
| Anayasa | [[../.ai/CLAUDE.md]] | Çelişkide kazanır |
| Vault katalog | [[../.ai/index.md]] | 570+ dosya envanteri |

## 4. Değişiklik Protokolü

1. Skill değişikliği → önce `[[./skills/skill-maker/SKILL.md]]` kural okunur → uygulama → `.claude` senkronu → audit
2. Rule değişikliği → `.claude/rules/` karşılığı ile birlikte → kullanıcı onayı → audit
3. `opencode.json` değişikliği → bak dosyası güncellenir → audit
4. Tüm audit kayıtları `[[../.ai/log.md]]`'ye append-only yazılır

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
