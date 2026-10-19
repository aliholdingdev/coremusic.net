---
node_id: cache
node_type: subdomain
status: draft
parent_id: K007-data
children:
  - cache-kapsam
  - cache-bilesenler
  - cache-dogrulama
  - cache-ornekler
  - cache-web-arastirma
depends_on:
  - K007-data/repositories
  - K007-data/schema
evidence:
  - "v2 §2.7 mini-ağaç — coremusic-katmanli-mimari-agaci-v2.md satır 283"
  - "shared/src/Cache/CacheManager.php:12-16 — APCu → Memory fallback"
  - "shared/src/Cache/CLAUDE.md §3 — L1/L2/L3 katman planı"
validation:
  - "2026-10-15 — 5 içerik MD'si yazıldı; children disk enumerate 5/5"
open_questions:
  - "Write-through/invalidation repo kanıtı YOK → VERIFICATION REQUIRED"
  - "Redis (K-04) kararı → UNKNOWN"
group: mimari
---

# cache — K007-data (kademe-2)

| Alan | Değer |
|---|---|
| node_id | cache |
| node_type | subdomain |
| parent_id | K007-data |
| Kaynak | v2 §2.7 (satır 283) |
| Durum | draft |

## Ne işe yarar?

Sıcak verinin hafızadaki TTL'li kopyası: APCu (L1) → Memory (L2 fallback) → PageCache (L3).
Adapter seçimi runtime'da `function_exists('apcu_fetch')` ile belirlenir.

## §1 İçerik dosyaları

| # | Dosya | Rol | Grup |
|---|-------|-----|------|
| 1 | [cache-kapsam.md](cache-kapsam.md) | sınır: kopya sahipliği | mimari |
| 2 | [cache-bilesenler.md](cache-bilesenler.md) | 6 dosya envanteri + CLAUDE.md tutarsızlık notu | mimari |
| 3 | [cache-dogrulama.md](cache-dogrulama.md) | D-C1…D-C7 iddia defteri | kanit |
| 4 | [cache-ornekler.md](cache-ornekler.md) | CacheManager tam kodu | kod |
| 5 | [cache-web-arastirma.md](cache-web-arastirma.md) | APCu/Redis/memcached (5 URL) | kanit |

## §2 Bağımlılık

- Üst düğüm: [../index.md](../index.md) · [../data-master.md](../data-master.md)
- Kardeşler: repositories (sorgu sonucu cache'i) · search (arama cache'i)

## §GRUPLAR

| Grup | Dosya | Amaç | Kademe |
|---|---|---|---|
| mimari | `index.md` | Klasör künyesi | Kademe-2 |
| mimari | `cache-kapsam.md` | Kapsam sınırı | Kademe-2 |
| mimari | `cache-bilesenler.md` | Bileşen envanteri | Kademe-2 |
| kanit | `cache-dogrulama.md` | İddia defteri | Kademe-2 |
| kod | `cache-ornekler.md` | Repo kodu | Kademe-2 |
| kanit | `cache-web-arastirma.md` | Resmi kaynak taraması | Kademe-2 |

---

**Authority:** Bayram Ali / Vault Steward