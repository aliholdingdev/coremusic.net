---
node_id: "K007-cache-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/repositories"
  - "K007-data/schema"
evidence:
  - "shared/src/Cache/CacheManager.php:12-16 — apcu_fetch varsa ApcuAdapter, yoksa MemoryAdapter (2026-10-15 okundu)"
  - "shared/src/Cache/CLAUDE.md §3 — L1 APCu / L2 Memory / L3 PageCache katmanları (2026-10-15 okundu)"
  - "shared/src/Cache/PageCacheAdapter.php:11,30-31 — page_pub_/page_auth_ + md5(uri) TTL 600"
validation: "2026-10-15 — kapsam yazıldı; 3 repo kanıtı satır bazlı"
open_questions:
  - "Write-through invalidation repo'da YOK → VERIFICATION REQUIRED"
  - "Stampede koruması repo'da YOK → VERIFICATION REQUIRED"
  - "APCu production'da enabled mı → php.ini yüzeyi K007 dışı"
group: mimari
---

# cache — Kapsam (K007)

## §1 Kapsam İÇİ

| # | Konu | Kanıt |
|---|------|-------|
| 1 | Katmanlı önbellek mimarisi (L1 APCu · L2 Memory · L3 PageCache) | `Cache/CLAUDE.md §3` |
| 2 | Adapter seçim zinciri (APCu → Memory fallback) | `CacheManager.php:12-16` |
| 3 | Sayfa önbelleği anahtar/TTL şeması | `PageCacheAdapter.php:11,30-31` |
| 4 | TTL politikası (`expires_in`/`expires_at`) | `Cache/CLAUDE.md` · APCu TTL |
| 5 | Invalidation stratejisi (tanımı) | ⚠️ kısmi — repo kanıtı yok |

## §2 Kapsam DIŞI

| Dışarıda | Nereye | Gerekçe |
|----------|--------|---------|
| DB sorgu planı/indeks | indexing | hız ≠ önbellek |
| Redis/memcached dağıtık seçim | K-04 kararı (UNKNOWN) | araç seçimi onaya bağlı |
| php.ini/apcu.enable yapılandırması | K000-isletim-sistemi | OS/runtime yüzeyi |
| Sayfa içerik üretimi | K011-application | cache yalnız kopyalar |

## §3 Sınır cümlesi

cache, "aynı veri ikinci kez istenince ne kadar ucuza döner?" sorusunun sahibidir —
kopyalar, tazeler, kirletir; **doğru olanın kendisi değildir** (o schema'dır).

---

**Authority:** Bayram Ali / Vault Steward