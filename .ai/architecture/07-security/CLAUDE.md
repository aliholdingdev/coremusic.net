---
title: "CoreMusic — .ai/architecture/07-security Bağlam"
type: context
folder: ".ai/architecture/07-security"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
---

# .ai/architecture/07-security — CLAUDE.md

**Zorunlu Bağlantılar:** [[./AGENTS.md]]

## 1. Bağlam
Security Engineer'ın ana referansı. Tüm auth/session/CSRF/CSP sorularının mimari kaynağı.

## 2. Mevcut Durum
| Durum | Değer |
|-------|-------|
| Dosya | 14 (2 mikro klasör: api 1, security 2) |

## 3. Komşu İlişkiler
Parent [[../CLAUDE.md]] · Kod [[../../../shared/src/Security/CLAUDE.md]] *(üretilecek)* · Middleware [[../../../shared/src/Middleware/CLAUDE.md]] *(üretilecek)*

## 4. Değişiklik Protokolü
1. Her değişiklik → security-audit workflow + log
2. ADR frozen güvenlik kararları (008/010/011/012/013/022) dokümanla çelişemez

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
