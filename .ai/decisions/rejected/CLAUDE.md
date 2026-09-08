---
title: "CoreMusic — .ai/decisions/rejected Bağlam"
type: context
folder: ".ai/decisions/rejected"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# .ai/decisions/rejected — CLAUDE.md

**Zorunlu Bağlantılar:** [[../CLAUDE.md]]

## 1. Bağlam

Bir öneri tekrar geldiğinde önce buraya bakılır: 3×Frontend, 3×Database, 4×Architecture, 1×Security, 1×Mobile red kaydı mevcut.

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Dosya | 13 (index + 12 R kaydı) |
| En sık gerekçe | Framework yasağı (3), BCNF/ORM uyumsuz (3), over-engineering (3) |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Karar merkezi |
| Karşıt bölge | [[../accepted/CLAUDE.md]] | Kabul edilenler |

## 4. Değişiklik Protokolü

1. Yeni red → ADR-creation workflow red kolu → index + log
2. Audit: `[[../../log.md]]`

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
