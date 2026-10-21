# 03 — Motor Diyalektleri (MySQL 8.0 Odaklı)

> Bu skill **MySQL 8.0 (LTS)** hedefler. CoreMusic vault'unda "MySQL 9" (Innovation hattı)
> geçebilir — **deploy edilen tam sürüm `VERIFICATION REQUIRED`**. Feature eklerken
> sürüm notunu doğrula. Bu dosya MySQL-özel gerçeği kesinleştirir.

## MySQL 8.0 temel gerçekleri

| Konu | Gerçek (8.0) |
|------|--------------|
| Varsayılan engine | InnoDB |
| Varsayılan charset | utf8mb4 (8.0'da `utf8mb4_0900_ai_ci` varsayılan collation; `utf8mb4_unicode_ci` da geçerli) |
| PK önerisi | `BIGINT UNSIGNED AUTO_INCREMENT` |
| BOOLEAN | `TINYINT(1)` alias'ı |
| Partial/filtered index (`... WHERE`) | **DESTEKLENMEZ** (Postgres'e özgü) |
| DESC index | 8.0+ desteklenir (8.0'dan önce her zaman artan) |
| Functional index | 8.0+ (function key part) |
| CTE (`WITH`) | 8.0+ |
| Window functions | 8.0+ |
| JSON tipi | 5.7+; çok-değerli (multi-valued) JSON index = **8.0.17+** |
| CHECK constraint enforce | **8.0.16+** (8.0.16 öncesi parse edilir ama uygulanmaz) |
| `EXPLAIN ANALYZE` | **8.0.18+** |
| Invisible index | 8.0+ |
| `EXPLAIN FORMAT=TREE/JSON` | 8.0+ |

## Partial index yerine MySQL çözümleri

```sql
-- İSTEMEDİĞİMİZ (MySQL'de geçersiz):
-- CREATE INDEX idx_active ON users(email) WHERE is_active = 1;

-- ÇÖZÜM A: composite index + sorgu predicate'ını eşleştir
CREATE INDEX idx_users_active_email ON users (is_active, email);

-- ÇÖZÜM B: generated (stored) kolon + functional index (8.0+)
ALTER TABLE products
  ADD COLUMN price_bucket INT GENERATED ALWAYS AS (IF(price > 100, 1, 0)) STORED,
  ADD INDEX idx_products_price_bucket (price_bucket);

-- ÇÖZÜM C: index hint / optimizer switch ile deneme (nadir) — EXPLAIN ile kanıtla
```

## TIMESTAMP vs DATETIME (8.0)

| | TIMESTAMP | DATETIME |
|--|-----------|----------|
| Aralık | 1970-01-01 … 2038-01-19 | 1000-01-01 … 9999-12-31 |
| Saklama | UTC; session `time_zone` ile gösterim | Doğrudan; tz yok |
| Boyut | 4 byte | 5 byte (fraksiyonsuz) |
| Otomatik | `DEFAULT CURRENT_TIMESTAMP` / `ON UPDATE` | destekli |
| Uygun | “oluşturuldu/güncellendi” anı | iş tarihi, gelecek tarih, 2038 sonrası |

> 2038 sorunu: TIMESTAMP 2038'de taşar. Sistem/audit zaman damgaları TIMESTAMP olabilir
> (2038'e kadar), kritik iş tarihleri DATETIME. Kesin proje tercihi `VERIFICATION REQUIRED`.

## UTF8MB4 & indeks uzunluğu

- InnoDB `DYNAMIC/COMPRESSED` row formatında büyük index sınırları geçerli (255 karakterlik utf8mb4 kolonda index genelde sorun çıkarmaz — 767/3072 bayt sınırları row formatına bağlı).
- Yine de `VARCHAR` boyutunu gerçek ihtiyaca göre seç; “her yerde 255” yasak.
- Collation tutarlılığı: join/unique kolonlar **aynı** collation olmalı; karışım → implicit conversion + index kullanımı kaybı.

## İsimlendirme (MySQL 8.0)

`snake_case`; `idx_{tablo}_{kolon}`, `uk_{tablo}_{kolon}`, `fk_{çocuk}_{ana}`.
Ayrıştırıcı (reserved word) kaçınmak için gerekirse backtick — ama kolon adını
reserved word yapma; standardı koru.

## Özet kural

MySQL 8.0'da yaz; 8.0.16+ / 8.0.17+ / 8.0.18+ feature'larını patch bilmeden production'a koyma.
