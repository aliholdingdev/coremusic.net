---
title: "CoreMusic — shared/src/Cache Bağlam"
type: context
folder: "shared/src/Cache"
category: layer0-infrastructure
date: 2026-09-21
updated: 2026-09-21
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
---

# Cache — CLAUDE.md (Detaylı)

**Zorunlu Bağlantılar:** · [[../CLAUDE.md]] · [[../../.ai/architecture/k0-isletim-sistemi]]

---

## 1. Bağlam

Önbellek adapterları (ADR-007). APCu, Memory ve PageCache adapter'ları ile PSR-6 uyumlu cache altyapısı.

---

## 2. Mevcut Durum

| Durum | Değer |
|-------|-------|
| Toplam dosya | 6 PHP dosyası |
| ADR | ADR-007 (cache namespace) |

### 2.1 Dosya Envanteri

| Dosya | Amaç |
|-------|------|
| `CacheManager.php` | Merkezi cache yöneticisi |
| `ApcuCacheAdapter.php` | APCu adapter (in-memory) |
| `MemoryCacheAdapter.php` | File-based memory adapter |
| `PageCacheAdapter.php` | Full-page cache adapter |
| `CacheItem.php` | PSR-6 CacheItem implementasyonu |
| `CachePool.php` | PSR-6 CachePool implementasyonu |

---

## 3. Cache Stratejisi

| Seviye | Adapter | Kullanım |
|--------|---------|----------|
| L1 (Hot) | APCu | Session, rate limit, config |
| L2 (Warm) | Memory | Query result, API response |
| L3 (Cool) | PageCache | Full-page render |

**Politika:** Read-Through, Write-Through, LRU eviction.

---

## 4. Komşu İlişkileri

| Yön | Hedef | İlişki |
|-----|-------|--------|
| Parent | [[../CLAUDE.md]] | Shared library üst bağlam |
| Kullanıcı | [[../Middleware/CLAUDE.md]] | Rate limit cache |
| Kullanıcı | [[../Database/CLAUDE.md]] | Query cache |
| Referans | [[../../.ai/architecture/k0-isletim-sistemi]] | OS katmanı |
| ADR | [[../../.ai/decisions/accepted/ADR-007-cache-namespace]] | Cache namespace |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-21
**Mode:** Red Team · Human Mode · Truth Mode
