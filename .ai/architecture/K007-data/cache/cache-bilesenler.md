---
node_id: "K007-cache-bilesenler"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/repositories"
evidence:
  - "shared/src/Cache/ — 6 PHP dosyası (2026-10-15 disk enumerate)"
  - "shared/src/Cache/CLAUDE.md §2.1 — dosya envanteri + ADR-007"
  - "composer.json:9 — ext-apcu zorunlu bağımlılık"
validation: "2026-10-15 — 6 bileşen diskte; envanter CLAUDE.md ile birebir"
open_questions:
  - "CacheItem/CachePool (PSR-6) gerçek kullanımı repo'da görünmüyor → VERIFICATION REQUIRED"
group: mimari
---

# cache — Bileşenler (K007)

| # | Dosya | Rol | Kanıt | Durum |
|---|-------|-----|-------|-------|
| 1 | `CacheInterface.php` | sözleşme | dosya var | ✅ |
| 2 | `CacheManager.php` | adapter seçimi + singleton | :12-16 | ✅ DOĞRULANDI |
| 3 | `ApcuAdapter.php` | L1 — APCu | composer ext-apcu | ✅ |
| 4 | `MemoryAdapter.php` | fallback (APCu yoksa) | CacheManager else dalı | ✅ |
| 5 | `PageCacheAdapter.php` | L3 — sayfa cache `page_pub_/page_auth_` TTL 600 | :11,30-31 | ✅ |
| 6 | `PageCacheInterface.php` | sayfa cache sözleşmesi | dosya var | ✅ |

> `CLAUDE.md §2.1` ApcuCacheAdapter/MemoryCacheAdapter/CacheItem/CachePool adlarını verir;
> diskteki gerçek adlar `ApcuAdapter`/`MemoryAdapter`'dir ve CacheItem/CachePool **diskte YOK**.
> Bu tutarsızlık CLAUDE.md'in eski kalıntısıdır — disk kazanır (2026-10-15).

## §1 Katman matrisi (CLAUDE.md §3 ↔ repo)

| Katman | Adapter | Kullanım (plan) | Repo kanıtı |
|--------|---------|-----------------|-------------|
| L1 Hot | APCu | session, rate limit, config | ApcuAdapter ✅ |
| L2 Warm | Memory | query result, API response | MemoryAdapter ✅ (fallback) |
| L3 Cool | PageCache | full-page render | PageCacheAdapter ✅ |

---

**Authority:** Bayram Ali / Vault Steward