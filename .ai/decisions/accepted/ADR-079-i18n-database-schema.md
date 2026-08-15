---
type: decision
id: "079"
title: "ADR-079: i18n Database Schema"
category: "database"
status: "active"
date: "2026-08-10"
updated: "2026-08-15"
authority: "Data Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [database, i18n, localization, schema, active]
risk-level: "low"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-040-database-authority]]"
---

# ADR-079: i18n Database Schema

---

## 1. Executive Summary

i18n veritabanı, dilleri, çevirileri, UI string'leri ve locale ayarlarını yönetir.

## 2. Tablolar

| # | Tablo | Amaç |
|---|-------|------|
| 1 | i18n_languages | Desteklenen diller |
| 2 | i18n_translations | Çeviriler |
| 3 | i18n_ui_strings | UI string'leri |
| 4 | i18n_locales | Locale ayarları |
| 5 | i18n_date_formats | Tarih formatları |

## 3. Decision

| # | Kural | Durum |
|---|-------|-------|
| 1 | BCNF normalization | ✅ Zorunlu |
| 2 | Soft delete | ✅ Zorunlu |
| 3 | Fallback strategy | ✅ Zorunlu |
| 4 | Cache-friendly | ✅ Zorunlu |

### Fallback Akışı

```
tr-TR → tr → en → en-US (fallback chain)
```

---

## 4. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-079: i18n Database Schema v1.0.0 — CoreMusic Database*
*Authority: Data Engineer · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
