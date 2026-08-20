---
title: "ADR-079: i18n Database Schema"
status: active
date: 2026-08-10
tags: [database, i18n, localization, schema, active]
---

# ADR-079: i18n Database Schema

---

## 1. Executive Summary

i18n veritabanÄ±, dilleri, Ã§evirileri, UI string'leri ve locale ayarlarÄ±nÄ± yÃ¶netir.

## 2. Tablolar

| # | Tablo | AmaÃ§ |
|---|-------|------|
| 1 | i18n_languages | Desteklenen diller |
| 2 | i18n_translations | Ã‡eviriler |
| 3 | i18n_ui_strings | UI string'leri |
| 4 | i18n_locales | Locale ayarlarÄ± |
| 5 | i18n_date_formats | Tarih formatlarÄ± |

## 3. Decision

| # | Kural | Durum |
|---|-------|-------|
| 1 | BCNF normalization | âœ… Zorunlu |
| 2 | Soft delete | âœ… Zorunlu |
| 3 | Fallback strategy | âœ… Zorunlu |
| 4 | Cache-friendly | âœ… Zorunlu |

### Fallback AkÄ±ÅŸÄ±

```
tr-TR â†’ tr â†’ en â†’ en-US (fallback chain)
```

---

## 4. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-079: i18n Database Schema v1.0.0 â€” CoreMusic Database*
*Authority: Data Engineer Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*