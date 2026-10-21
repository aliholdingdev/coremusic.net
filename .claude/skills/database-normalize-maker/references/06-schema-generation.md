# 06 — Şema & Migration Üretim Kalıpları

> Pratik kalıplar: create, ilişki, soft delete, audit, JSON, migration (expand-contract).
> Tüm örnekler MySQL 8.0 / InnoDB / utf8mb4.

## Tablo iskeleti (standart)

```sql
CREATE TABLE orders (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uk_orders_id_status (id, status),
    KEY idx_orders_user_status_created (user_id, status, created_at),
    KEY idx_orders_created (created_at),
    CONSTRAINT fk_orders_users FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
```

> Not: `total_price` bir **denormalize toplam** ise ADR gerekçesi olmalı; source-of-truth
> `order_items` satırlarıdır ve senkron zorunludur.

## İlişki kalıpları

- **1:M:** Çocukta `parent_id` + FK + `idx_parent_id`.
- **M:M:** Junction + iki FK + `UNIQUE(child_a, child_b)`.
- **1:1:** Çocukta `parent_id UNIQUE`.
- **Self-ref:** `parent_id NULL` + `ON DELETE SET NULL`.

## Soft delete (paranoid)

```sql
UPDATE users SET deleted_at = CURRENT_TIMESTAMP WHERE id = 1;
SELECT id, email FROM users WHERE deleted_at IS NULL;
```

Soft delete + unique çakışması: MySQL'de NULL'lar unique'de çakışmaz → `(email, deleted_at)`
composite unique, birden fazla silinmiş aynı email'e izin verir.

## JSON kolon + index (8.0)

```sql
ALTER TABLE products ADD COLUMN metadata JSON NULL;

-- 8.0.17+ çok-değerli index (JSON array)
-- veya generated kolon + normal index
ALTER TABLE products
  ADD COLUMN genre VARCHAR(50) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(metadata,'$.genre'))) VIRTUAL,
  ADD INDEX idx_products_genre (genre);
```

## Migration: expand-and-contract

```sql
-- FAZ 1 EXPAND
ALTER TABLE users ADD COLUMN email_canonical VARCHAR(255) NULL;
-- uygulama dual-write; backfill job hazırla.

-- FAZ 2 MIGRATE (chunk'lı, lock kısa)
-- UPDATE users SET email_canonical = LOWER(email) WHERE id BETWEEN ? AND ?;

-- FAZ 3 CONTRACT (doğrulama + root onayı sonra)
-- ALTER TABLE users DROP COLUMN email, CHANGE email_canonical email VARCHAR(255) NOT NULL;
```

| Kural | Ayrıntı |
|-------|---------|
| Forward-only | Prod'da geri dönüş yeni forward migration |
| Backfill batch | Tek büyük UPDATE lock üretir; id aralığıyla parçala |
| Prod DDL | root onayı şart |
| Rollback planı | Her fazın geri alması yazılı |

Şablon: [templates/migration-template.sql](../templates/migration-template.sql).
