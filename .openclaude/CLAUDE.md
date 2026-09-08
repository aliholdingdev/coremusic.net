---
title: "CoreMusic — .openclaude Bağlam"
type: context
folder: ".openclaude"
category: config
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# .openclaude — CLAUDE.md

**Zorunlu Bağlantılar:** [[./AGENTS.md]] · [[../.ai/CLAUDE.md]]

## 1. Bağlam

OpenClaude oturumları bu klasördeki 3 rule dosyasını yükler. En dar config setidir; detay kurallar `.claude/rules/`'ta ikamet eder ve buradaki dosyalar özetleyici niteliktedir.

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Rule sayısı | 3 (core-rules, orchestration, vault) |
| Skill | Yok (isteyerek — ikili skills yapısı `\.claude` + `.opencode` arasında) |
| Senkron partnerleri | `.claude/rules/` (4 ortak konu), `.opencode/rules/` (4 ortak konu) |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../AGENTS.md]] | Kök registry |
| Kardeş | [[../.claude/CLAUDE.md]] | Claude Code config |
| Kardeş | [[../.opencode/CLAUDE.md]] | OpenCode config |
| Anayasa | [[../.ai/CLAUDE.md]] | Çelişkide kazanır |

## 4. Değişiklik Protokolü

1. Rule değişikliği → `.claude/rules/` karşılığı kontrol edilir → çelişki varsa DUR
2. Kullanıcı onayı olmadan dosya eklenmez/silinmez
3. Audit kaydı `[[../.ai/log.md]]`'ye yazılır

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
