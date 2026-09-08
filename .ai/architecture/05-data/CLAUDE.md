---
title: "CoreMusic — .ai/architecture/05-data Bağlam"
type: context
folder: ".ai/architecture/05-data"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
---

# .ai/architecture/05-data — CLAUDE.md

**Zorunlu Bağlantılar:** [[./AGENTS.md]]

## 1. Bağlam

Data Engineer agent'ının ana referansı. Şema sorularında sıra: `database_master.md` → ilgili ADR → `.sql` dosyası.

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Dosya | 5 |
| DB sayısı | 18 BCNF (ADR-040) |
| Migration klasörü | shared/database/migrations (2 OAuth migration) |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Mimari kök |
| Şema SQL | [[../../.sql/mysql/CLAUDE.md]] *(üretilecek)* | 18 DB dump |
| Kod | [[../../../shared/src/Database/CLAUDE.md]] *(üretilecek)* | DatabaseManager |

## 4. Değişiklik Protokolü

1. Şema değişikliği → BCNF kontrol → `.sql` + migration + doküman üçlü senkron
2. Audit: `[[../../log.md]]`

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
