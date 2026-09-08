---
title: "CoreMusic — packages/shared Bağlam"
type: context
folder: "packages/shared"
category: shared
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# packages/shared — CLAUDE.md

**Zorunlu Bağlantılar:** [[./AGENTS.md]] · [[../../shared/CLAUDE.md]]

## 1. Bağlam

Contract paketi: tüketenler auth, home, gelecekte music/admin servisleri. Interface'ler kök `shared/` implementasyonlarıyla eşleşmek zorunda — iki tarafın diff'i tek kaynak kararına kadar senkron tutulur.

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Interface sayısı | 8 (Http 3, Repository 3, Service 2) |
| DTO sayısı | 11 |
| Enum sayısı | 4 |
| ValueObject sayısı | 4 |
| Test | 2 (StringHelper, Email) — Contract/DTO test yok |
| Bilinen risk | Kök `shared/src` ile paralel gelişme (double-source) |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../../packages/CLAUDE.md]] | Paket kökü |
| Implementasyon | [[../../shared/CLAUDE.md]] | Interface'lerin canlı implementasyonu |
| Tüketen | [[../../auth.coremusic.net/CLAUDE.md]] | DTO/ValueObject kullanımı |

## 4. Değişiklik Protokolü

1. Interface/DTO değişikliği → kök `shared` implementasyonu aynı commit'te uyumlanır
2. Test ekleme → phpunit suite yapısına uygun
3. Audit `[[../../.ai/log.md]]`'ye yazılır

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
