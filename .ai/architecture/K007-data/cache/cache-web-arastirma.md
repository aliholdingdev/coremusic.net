---
node_id: "K007-cache-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/data-web-arastirma"
evidence:
  - "5 URL — php.net APCu + Redis + memcached (2026-10-12 tarama, 2026-10-15 derleme)"
validation: "2026-10-15 — mevcut havuz §2 derlendi; yeni URL eklenmedi"
open_questions:
  - "Redis sürüm/ürüm kararı (K-04) → UNKNOWN"
group: kanit
---

# cache — Web Araştırması (K007)

## §1 Tarama (data-web-arastirma.md §2 havuzu · 2026-10-12)

| # | URL | Ne verdi | Tür | Durum |
|---|-----|----------|-----|-------|
| WC-1 | php.net/manual/en/book.apcu.php | APCu = kullanıcı uzayında paylaşımlı bellek; apc.enabled/apc.enable_cli | T1/T3 | HTTP 200 |
| WC-2 | php.net/manual/en/function.apcu-fetch.php | apcu_fetch semantiği | T3 | HTTP 200 |
| WC-3 | php.net/manual/en/function.apcu-store.php | apcu_store TTL parametresi | T3 | HTTP 200 |
| WC-4 | redis.io/docs/latest/ | veri yapıları + EXPIRE TTL | T1 | HTTP 200 |
| WC-5 | github.com/memcached/memcached | distributed simple KV + LRU | T1 | HTTP 200 |

## §2 K007 karşılığı

- **D-C1/D-C3** bu taramanın çift uçlu (T4) karşılığıdır.
- Redis (WC-4) → K-04 kararı **askıda**: depoda Redis referansı YOK (master §4 satır 13).
- ⚠️ hiçbir "hızlı/ucuz" iddiası bağımsız benchmark ile doğrulanmadı (W49).

---

**Authority:** Bayram Ali / Vault Steward