# cache-patterns.md — Cache Desenleri

> Uygulanan: `packages/cache` (RAM LRU + Disk JSON hybrid).

## Katmanlar

| L | Yapı | TTL | Not |
|---|---|---|---|
| 1 | Map LRU (500 kayıt) | 300sn | erişilen en yeniye taşınır (LRU) |
| 2 | JSON dosyası | 3600sn | lazy eviction (okunurken TTL kontrol) |
| 3 | DB | kalıcı | zaten ana kaynak |

## Desenler

- **Write-through:** `set` L1+L2'ye birden yazar.
- **Read-repair:** L2 isabeti L1'e geri yazılır.
- **Lazy eviction:** TTL dolan kayıt okunurken silinir (sweep job yok).
- **Key normalizasyonu:** alanlar sıralı + lowercase → sha1 (`queryKey`).
  "Adele" ve "adele " aynı anahtarı üretir.

## Anti-desen (yapılmayan)

- Cache stampede koruması (tekil yerel kullanım → gereksiz)
- Dağıtık cache (Redis) — V3; WORKFLOW'taki eski öneri, V1'de YAGNI

## İstatistik

`stats` komutu + web durum çubuğu isabet/kaçırma oranını gösterir.
