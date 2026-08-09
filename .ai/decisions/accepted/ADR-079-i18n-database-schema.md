---
type: adr
category: database
title: "ADR-079: i18n Database Schema"
date: 2026-08-10
updated: 2026-08-10
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-079: i18n Database Schema

**Status:** Active (güncellenebilir)
**Kategorisi:** Database
**İlgili Agent:** [[.agents/data-engineer]]

---

## 1. Tablolar

| # | Tablo | Amaç |
|---|-------|------|
| 1 | `i18n_languages` | Desteklenen diller |
| 2 | `i18n_translations` | Çeviriler (key, locale, value) |
| 3 | `i18n_ui_strings` | UI string'leri |
| 4 | `i18n_user_locale` | Kullanıcı dil tercihleri |

## 2. BCNF Uyumluluğu

| Tablo | Functional Dependency | Candidate Key |
|-------|----------------------|---------------|
| i18n_languages | id → {code, name, native_name, ...} | code UNIQUE |
| i18n_translations | id → {key, locale, value, ...} | (key, locale) UNIQUE |
| i18n_ui_strings | id → {module, string_key, locale, ...} | (module, string_key, locale) UNIQUE |
| i18n_user_locale | id → {user_id, locale, ...} | user_id UNIQUE |

---

## 3. İlgili ADR'ler

| ADR | İlişki |
|-----|--------|
| [[ADR-003-multi-db-9-databases]] | DB mimarisi |
| [[ADR-040-database-authority]] | DB otoritesi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team · Human Mode · Truth Mode
