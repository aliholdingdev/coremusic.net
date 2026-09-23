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

Bir öneri task yada doğrulanmak için bauara göndeirlri eğer doğrulaırsa direk ;
Öneri doğrulanır red ise "rejected" e gönderilir yani buraya 
Eğer Öneri Onayaılır ise sonardan "accepted" e gönderilir taşınır.

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
