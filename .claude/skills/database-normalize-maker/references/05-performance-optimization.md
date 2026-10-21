# 05 — Performans Optimizasyonu (Index, EXPLAIN, N+1)

> Optimizasyon tahminle değil, `EXPLAIN`/`EXPLAIN ANALYZE` (8.0.18+) ile ölçülür.
> Kanıtsız “yavaş” iddiası yasak.

## Index tasarım ilkeleri

| Kural | Gerekçe |
|-------|---------|
| Her FK'ya index | InnoDB JOIN/DELETE/FK kontrolü index ister |
| Covering index | SELECT kolonları index'te → `Using index`, table access yok |
| Equality → equality → range/sort | Composite index soldan sağa okunur; range sonrası kolon verimsiz |
| Over-indexing kaçın | Her index INSERT/UPDATE/DELETE maliyeti + disk |
| Prefix index yalnız uzun string | `KEY (name(30))` — tam unique değil |
| Sorgu sırasını index'e uydur | ORDER BY/GROUP BY index sırasıyla eşleşirse filesort önlenir |

## Composite index sıralama

```sql
-- Sorgu: WHERE user_id=? AND status=? ORDER BY created_at DESC
CREATE INDEX idx_orders_user_status_created ON orders (user_id, status, created_at);
```

Eşitlik kolonları önce, range/ORDER kolonu en son.

## EXPLAIN okuma tablosu

| Alan | Tehlikeli değer | Aksiyon |
|------|-----------------|---------|
| `type` | `ALL` (full scan) | İlgili kolona/composite index |
| `key` | `NULL` | Index eşleşmedi; predicate/index sırası |
| `Extra` | `Using filesort` | ORDER BY'ı index sırasına uydur |
| `Extra` | `Using temporary` | GROUP BY/JOIN için composite index |
| `Extra` | `Using index` | Covering — iyi |
| `rows` | Çok yüksek | Kapsamı daralt |

```sql
EXPLAIN SELECT u.id, u.email, o.total
FROM users u JOIN orders o ON o.user_id = u.id
WHERE u.deleted_at IS NULL AND o.status = 'paid';

EXPLAIN ANALYZE SELECT ... ;  -- 8.0.18+: gerçek süre + actual rows
```

## N+1 tespiti ve çözümü

Belirti: döngü içinde tekrarlanan tekil SELECT (N satır → N+1 sorgu).

```sql
-- KÖTÜ (N+1): her kullanıcı için ayrı SELECT
--   SELECT ... FROM users WHERE id=?;  (N kez)

-- İYİ (toplu): tek sorgu
SELECT u.id, u.email, o.total
FROM users u LEFT JOIN orders o ON o.user_id = u.id
WHERE u.id IN (?, ?, ?) AND u.deleted_at IS NULL;
```

Alternatif: JOIN ile eager load; veya `WHERE EXISTS` (bazen daha hızlı).

## Denormalizasyon vs index

Önce index/covering dene; denormalizasyon son çare ve ADR'li (SKILL.md §3.4).
Raporlama için materialized özet/`_summary` tablosu, `updated_at` ile senkron.

## Ölçüm disiplini

1. `EXPLAIN` ile planı göster.
2. `EXPLAIN ANALYZE` ile gerçek metrik al (8.0.18+).
3. Değişiklik öncesi/sonrası karşılaştır.
4. Index'i üretimde `online` ekle (InnoDB `ALGORITHM=INPLACE` mümkünse).
