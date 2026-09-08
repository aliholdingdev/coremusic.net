---
title: "CoreMusic — assets.coremusic.net/js Bağlam"
type: context
folder: "assets.coremusic.net/js"
category: layer3
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
---

# assets.coremusic.net/js — CLAUDE.md

**Zorunlu Bağlantılar:** [[./AGENTS.md]]

## 1. Bağlam
SPA istemci katmanı: router, cihaz yönetimi, player feature'ları ve OAuth istemcisi.

## 2. Mevcut Durum
| Durum | Değer |
|-------|-------|
| Giriş dosyası | 5 |
| Modül klasörü | 6 (~50 dosya) |

## 3. Komşu İlişkiler
Parent [[../CLAUDE.md]] · Router sözleşmesi [[../../.ai/architecture/l2-routing/CLAUDE.md]] · PHP karşılığı [[../../shared/src/PageRouter/CLAUDE.md]]

## 4. Değişiklik Protokolü
Modül değişimi → EventBus sözleşmesi + CSP kontrolü + log.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
