---
node_id: "K007-cache-dogrulama"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/cache-web-arastirma"
evidence:
  - "satır D-C1…D-C7 — 2026-10-15 kaydı"
validation: "2026-10-15 — 7 iddia: 5 T4 (repo tek-uç+web), 2 UNKNOWN"
open_questions:
  - "hit/miss oranı ölçümü → repo'da sayaç YOK"
group: kanit
---

# cache — Doğrulama Defteri (K007)

| # | İddia | Repo kanıtı | Web kanıtı | Tür | Durum |
|---|-------|-------------|-----------|-----|-------|
| D-C1 | Önbellek APCu; fallback bellek-içi | `CacheManager.php:12-16` | php.net/manual/book.apcu.php | T4 | ✅ DOĞRULANDI |
| D-C2 | APCu production'da disabled gelebilir | — | php.net apc.enabled/apc.enable_cli | T3 | ✅ HTTP 200 |
| D-C3 | `apcu-store` TTL alır; TTL haritası birebir | `Cache/CLAUDE.md` expires_in/at | php.net/function.apcu-store.php | T4 | ✅ DOĞRULANDI |
| D-C4 | Sayfa cache anahtarı `md5(uri)`, TTL 600 | `PageCacheAdapter.php:11,30-31` | — | T4 | ✅ DOĞRULANDI |
| D-C5 | L1/L2/L3 katman planı | `Cache/CLAUDE.md §3` | — | T4 | ✅ DOĞRULANDI |
| D-C6 | Write-through invalidation | YOK | YOK | — | **UNKNOWN** |
| D-C7 | Stampede koruması | YOK | YOK | — | **UNKNOWN** |

> D-C1 çift uçludur (repo satırı + resmi PHP sayfası) → T4. D-C6/D-C7 tasarım önerisidir,
> kanıtlanana kadar `data-riskler.md` R-cache satırlarına bağlı kalır.

---

**Authority:** Bayram Ali / Vault Steward