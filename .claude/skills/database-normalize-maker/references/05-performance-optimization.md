# 05. Performans Optimizasyonu

## Genel Bakış

Veritabanı performansı, doğru index seçimi, sorgu optimizasyonu ve kaynak kullanımı ile sağlanır.

## 1. İndeks Tasarım Stratejisi

### Hangi Sorgu Kalıbı Hangi Index'i Gerektirir?

| Sorgu Kalıbı | Index Tipi | Örnek |
|---------------|------------|-------|
| Eşleşme (equality) | B-tree | `WHERE status = 'active'` |
| Aralık (range) | B-tree | `WHERE created_at > '2024-01-01'` |
| Sıralama (sort) | B-tree | `ORDER BY created_at DESC` |
| Full-text arama | FULLTEXT | `WHERE name MATCH 'keyword'` |
| JSON alanı | Virtual column + index | `JSON_EXTRACT(metadata, '$.key')` |
| Çoklu sütun WHERE | Composite index | `WHERE user_id = 1 AND status = 'active'` |

### Composite Index Sıralama Kuralı

```
Composite index: (equality_first, equality_second, range_or_sort)
```

```sql
-- Sorgu: WHERE user_id = ? AND status = ? ORDER BY created_at DESC
-- Doğru index:
CREATE INDEX idx_user_status_date ON orders(user_id, status, created_at);
```

### İndeks Kuralları

| Kural | Açıklama |
|-------|----------|
| Her FK'ya index | `FOREIGN KEY (user_id)` → `INDEX idx_user_id (user_id)` |
| Sık sorgulanan kolonlara | `WHERE email = ?` → `INDEX idx_email (email)` |
| ORDER BY kolonlarına | `ORDER BY created_at` → `INDEX idx_created_at (created_at)` |
| Composite: equality primero | `WHERE a = ? AND b > ?` → `INDEX (a, b)` |
| Over-indexing kaçın | Her index yazma işlemini yavaşlatır |
| Prefix index | Uzun string için: `INDEX idx_name (name(20))` |

### İndeks Takibi

```sql
-- Kullanılmayan index'leri bul
SELECT * FROM performance_schema.table_io_waits_summary_by_index_usage
WHERE INDEX_NAME IS NOT NULL AND COUNT_READ = 0;

-- Kullanılmayan index'i sil
DROP INDEX idx_unused ON users;
```

## 2. Aşırı İndeksleme Tuzağı

| Durum | Öneri |
|-------|-------|
| Write-Heavy tablo | Minimum index (sadece PK + FK) |
| Read-Heavy tablo | Kapsayıcı index'ler ekle |
| Hybrid tablo | Sorgu kalıplarına göre optimize et |

## 3. JSON İndeksleme

MySQL'de JSON kolonlarına doğrudan indeks atılamaz. Virtual Column oluşturulmalı:

```sql
-- Doğru yaklaşım
ALTER TABLE products
    ADD COLUMN metadata_genre VARCHAR(50)
    GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.genre'))) VIRTUAL,
    ADD INDEX idx_metadata_genre (metadata_genre);
```

## 4. Bölümleme (Partitioning)

>100M satır veri varsa, log tabloları tarihe göre partition'lara bölünmeli.

```sql
-- Zaman bazlı partition (log tabloları için)
CREATE TABLE system_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    level ENUM('INFO', 'WARNING', 'ERROR') NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
PARTITION BY RANGE (TO_DAYS(created_at)) (
    PARTITION p2026_01 VALUES LESS THAN (TO_DAYS('2026-02-01')),
    PARTITION p2026_02 VALUES LESS THAN (TO_DAYS('2026-03-01')),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
```

## 5. Sorgu Optimizasyonu

| Sorun | Çözüm |
|-------|-------|
| `SELECT *` | Açık kolon listesi |
| OFFSET tabanlı sayfalama | Cursor-based pagination (`WHERE id > ?`) |
| Fonksiyonlu WHERE | SARGable sorgular |
| N+1 sorgusu | JOIN veya batch loading |
| `UNION` gereksiz | `UNION ALL` tercih (dedup yoksa) |

```sql
-- YANLIŞ: OFFSET tabanlı (yavaş)
SELECT id, username FROM users ORDER BY id LIMIT 20 OFFSET 10000;

-- DOĞRU: Cursor-based (hızlı)
SELECT id, username FROM users WHERE id > 10000 ORDER BY id LIMIT 20;

-- YANLIŞ: Fonksiyonlu WHERE (index kullanmaz)
SELECT * FROM users WHERE YEAR(created_at) = 2026;

-- DOĞRU: SARGable (index kullanır)
SELECT id, username FROM users WHERE created_at >= '2026-01-01' AND created_at < '2027-01-01';
```

## 6. Transaction Performansı

| Kural | Açıklama |
|-------|----------|
| I/O işlemi transaction dışında | Dosya okuma/yazma transaction dışında |
| `SELECT ... FOR UPDATE` dikkatli | Sadece gerekirse |
| Deadlock önleme | Tutarlı satır erişim sırası |
| Batch insert | 500-5000 satır arası |
