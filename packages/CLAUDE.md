---
title: "CoreMusic — packages Bağlam"
type: context
folder: "packages"
category: shared
date: 2026-09-06
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
---

# packages — CLAUDE.md

**Zorunlu Bağlantılar:** [[./AGENTS.md]] · [[./shared/CLAUDE.md]]

## 1. Bağlam

`packages/shared` kök `shared/` klasörünün **paketleştirilmiş hibrit aşamasıdır** (ADR-085). İkisi arasındaki geçiş durumu ve hangisinin canlı olduğu her oturumda doğrulanmalıdır — çift kaynak riski mevcuttur.

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Paket sayısı | 1 (shared) |
| shared/src alt klasör | 13 (Contract, DTO, Enum, Event, Exception, Helper, Http, Security, Validation, ValueObject) |
| Test | Helper + ValueObject (2 unit test) |
| Bilinen risk | `shared/` (kök) ile içeriğin kısmen kopyalanmış olması — tek kaynak netleşene kadar her iki tarafta da güncelleme yapma |

## 3. Komşu İlişkiler

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../AGENTS.md]] | Kök registry |
| Kök ikiliği | [[../shared/CLAUDE.md]] | Aynı kütüphanenin iki konumu — senkron sorusu açık |
| Karar kaynağı | [[../.ai/decisions/accepted/ADR-085-modular-composer-packages.md]] | Hibrit yapı kararı |

## 4. Değişiklik Protokolü

1. Bu klasörde değişiklik öncesi → kök `shared/` karşılığı kontrol → fark varsa DUR + kullanıcıya rapor
2. Yeni paket → ADR şart (mimari değişiklik)
3. Audit `[[../.ai/log.md]]`'ye yazılır

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
