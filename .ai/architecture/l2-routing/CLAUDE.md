---
title: "CoreMusic — .ai/architecture/l2-routing Bağlam"
type: context
folder: ".ai/architecture/l2-routing"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
---

# l2-routing — CLAUDE.md

**Zorunlu Bağlantılar:** [[./AGENTS.md]]

## 1. Bağlam
PHP PageRouter ile JS SPA router'ın katman sözleşmesi; subdomain erişimi buradan yönlendirilir.

## 2. Mevcut Durum
| Durum | Değer |
|-------|-------|
| Dosya | 10 (index dahil) |

## 3. Komşu İlişkiler
Parent [[../CLAUDE.md]] · Kod [[../../../shared/src/PageRouter/CLAUDE.md]] *(üretilecek)* · JS router [[../../../assets.coremusic.net/CLAUDE.md]]

## 4. Değişiklik Protokolü
Router değişimi → ADR (immutable sözleşme kontrolü) + iki kod tarafı + log.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
