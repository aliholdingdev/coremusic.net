# Kaynaklar (Sources)

> Zero-hallucination kanıt tabanı. Her kaynak URL + erişim tarihi ile listelenir.
> Doğrulanamayan URL/sürüm `VERIFICATION REQUIRED` olarak işaretlenir; uydurulmaz.

**Erişim tarihi (tümü):** 2026-10-21

## 1. MySQL 8.0 resmi dokümanı (MySQL Reference Manual, 8.0)

| | |
|--|--|
| URL | https://dev.mysql.com/doc/refman/8.0/en/ |
| Erişim | 2026-10-21 |
| Kullanım | Engine (InnoDB), utf8mb4/collation, veri tipleri, TIMESTAMP/DATETIME, JSON, index, EXPLAIN, CTE/window, feature min-sürüm notları. |
| Güven | Yüksek — Oracle/MySQL resmi reference manual, 8.0 dalı. |

İlgili alt sayfalar (aynı manuel içinde, erişim 2026-10-21):

| Konu | URL |
|------|-----|
| `CREATE INDEX` sözdizimi (partial/filtered index **desteklenmez** kanıtı) | https://dev.mysql.com/doc/refman/8.0/en/create-index.html |
| `EXPLAIN` (plan + `EXPLAIN ANALYZE` 8.0.18+) | https://dev.mysql.com/doc/refman/8.0/en/explain.html |

> Not: Bu alt sayfa yolları MySQL 8.0 manual'inde uzun süredir kararlıdır; yine de canlı
> erişimde 404 görürsen manuel ana sayfasından (…/8.0/en/) gezin — ana URL kalıcıdır.

## 2. Normalizasyon kuramı (güvenilir özet)

| | |
|--|--|
| Başlık | Database normalization (1NF/2NF/3NF/BCNF/4NF, fonksiyonel bağımlılık, aday anahtar) |
| URL | https://en.wikipedia.org/wiki/Database_normalization |
| Erişim | 2026-10-21 |
| Kullanım | Form tanımları, Armstong aksiyomları, kapanım, lossless/dependency-preserving ayrıştırma kavramları. |
| Güven | Orta-yüksek — açık özet; akademik birincil kaynak (Codd; Boyce & Codd) ve Silberschatz/Korth/Sudarshan “Database System Concepts” normalizasyon bölümü ile çapraz doğrulanabilir. |

## 3. ISO/IEC 9075 (SQL standardı) — birincil referans

| | |
|--|--|
| Başlık | ISO/IEC 9075 — Information technology — Database languages — SQL |
| URL | **VERIFICATION REQUIRED** — kesin ISO katalog numarası/URL'si bu oturumda doğrulanmadı; uydurulmadı. |
| Erişim | — |
| Kullanım | Normalizasyon ve SQL tanımlarının standart kaynağı olarak gösterilir; pratik uygulama için 1–2 numaralı MySQL kaynakları esastır. |
| Güven | ISO/IEC birincil standart; URL doğrulanana kadar `VERIFICATION REQUIRED`. |

## Kaynak hiyerarşisi

1. Kullanıcı talimatı > onaylı ADR'ler > proje vault'u (`.ai/`) > resmi doküman (MySQL) > genel özet.
2. Çelişki gizlenmez; vault ile resmi doküman farklıysa `VERIFICATION REQUIRED` + üst karar.
3. Model hafızası ve web, vault'un altındadır (zero-hallucination).
