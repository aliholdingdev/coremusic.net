---
title: "CoreMusic — .claude Bağlam"
type: context
folder: ".claude"
category: config
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# .claude — CLAUDE.md

**Zorunlu Bağlantılar:** [[./AGENTS.md]] · [[../AGENTS.md]] · [[../.ai/CLAUDE.md]]

## 1. Bağlam

Claude Code bu klasördeki `rules/*.md` dosyalarını otomatik yükler. Vault (`[[../.ai/CLAUDE.md]]`) anayasadır; bu klasördeki kurallar anayasaya **ikincildir**. Çelişki durumunda vault kazanır.

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Rule sayısı | 7 |
| Skill sayısı | 10 |
| `.opencode` ile senkron | Ortak 4 rule dosyası + 10 skill (ikili yapı) |
| Bilinen risk | `prompt-maker/.archive` (30 dosya) canlı olmayan içerik |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../AGENTS.md]] | Kök registry |
| Parent | [[../CLAUDE.md]] | Kök anayasa pointer |
| Twin | [[../.opencode/CLAUDE.md]] | OpenCode ikili konfigürasyonu — değişiklikler çift taraflı uygulanır |
| Canonical | [[../.ai/CLAUDE.md]] | AI anayasası (SSOT) |
| Vault okuma sırası | [[../.ai/index.md]] | Katalog |

## 4. Değişiklik Protokolü

1. Her değişiklik önce **kullanıcı onayı** alır (Human Mode)
2. Değişiklik `.claude` **ve** `.opencode` karşılığına aynı anda uygulanır
3. Audit kaydı `[[../.ai/log.md]]`'ye yazılır
4. Bu dosya yalnızca bağlam günceller; anayasa ([[../.ai/CLAUDE.md]]) asla bu klasörden yönetilmez

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
