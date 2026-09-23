# 08. SQL Çıktı Örnekleri

## Genel Bakış

Bu dosya, veritabanı motorunun ürettiği SQL çıktısının nasıl olması gerektiğine dair kapsamlı örnekler içerir.

## Örnek 1: Users Tablosu + Audit Trail

```sql
-- =========================================================================
-- MODULE: Users Management
-- VALIDATION: BCNF Passed | Security Checked
-- =========================================================================

-- ADR: 'ssn' kolonu AES-256-GCM ile şifreleneceği için TEXT yapılmıştır.
-- ADR: 'email' kolonu için UNIQUE kapsayıcı index atılmıştır.

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    ssn TEXT NULL, -- ⚠️ Şifrelenmeli (AES-256-GCM)
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uk_email (email),
    INDEX idx_is_active (is_active),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- AUDIT TRAIL
CREATE TABLE users_audit (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    action ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    acted_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Dikkat Edilmesi Gerekenler:**
1. Yorum satırları mimari kararların (ADR) kanıtlarıdır.
2. `users_audit` tablosu PII barındırdığı için zorunludur.
3. ID için BIGINT UNSIGNED kullanılmıştır.
4. `deleted_at` soft delete için tanımlıdır.

## Örnek 2: Products + Categories (One-to-Many)

```sql
-- =========================================================================
-- MODULE: Product Management
-- VALIDATION: BCNF Passed
-- =========================================================================

CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_id BIGINT UNSIGNED NULL DEFAULT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uk_slug (slug),
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_parent_id (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock_quantity INT UNSIGNED DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uk_slug (slug),
    INDEX idx_category_id (category_id),
    INDEX idx_price (price),
    INDEX idx_is_active (is_active),
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Örnek 3: Orders + Order Items (Many-to-Many Junction)

```sql
-- =========================================================================
-- MODULE: Order Management
-- VALIDATION: BCNF Passed | Denormalized (ADR justified)
-- =========================================================================

-- ADR: 'total_price' kolonu denormalize edildi.
-- Gerekçe: Raporlama ekranında çok ağır Join işlemlerini engellemek.

CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    total_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    item_count INT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    unit_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Örnek 4: Seed Data

```sql
-- =========================================================================
-- CoreMusic - Seed Data
-- Test verileri KESİNLİKLE gerçek PII içermez
-- =========================================================================

INSERT INTO users (email, password_hash, first_name, last_name, is_active)
VALUES
    ('test1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test', 'User 1', 1),
    ('test2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test', 'User 2', 1),
    ('test3@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test', 'User 3', 0);

INSERT INTO categories (name, slug)
VALUES
    ('Rock', 'rock'),
    ('Pop', 'pop'),
    ('Jazz', 'jazz');

INSERT INTO products (category_id, name, slug, price, stock_quantity)
VALUES
    (1, 'Greatest Rock Album', 'greatest-rock-album', 29.99, 100),
    (2, 'Best Pop Hits', 'best-pop-hits', 24.99, 150);
```

## Örnek 5: ER Diyagramı (ASCII Art)

```
┌─────────────────────┐         ┌─────────────────────┐
│       USERS         │         │      ORDERS         │
├─────────────────────┤         ├─────────────────────┤
│ id (PK, bigint)     │────1:N─│ id (PK, bigint)     │
│ email (UK, varchar) │         │ user_id (FK, bigint)│
│ password_hash       │         │ total_price (decimal)│
│ first_name (varchar)│         │ item_count (int)    │
│ last_name (varchar) │         │ status (enum)       │
│ is_active (tinyint) │         │ created_at          │
│ created_at          │         └──────────┬──────────┘
│ updated_at          │                    │
│ deleted_at          │                    │ 1:N
└─────────────────────┘                    │
                                           ▼
┌─────────────────────┐         ┌─────────────────────┐
│    ORDER_ITEMS      │         │     PRODUCTS        │
├─────────────────────┤         ├─────────────────────┤
│ id (PK, bigint)     │         │ id (PK, bigint)     │
│ order_id (FK, bigint)│        │ category_id (FK)    │
│ product_id (FK)     │◀─N:1───│ name (varchar)      │
│ quantity (int)      │         │ slug (UK, varchar)  │
│ unit_price (decimal)│         │ price (decimal)     │
└─────────────────────┘         │ stock_quantity (int)│
                                └──────────┬──────────┘
                                           │ N:1
                                           ▼
                                ┌─────────────────────┐
                                │    CATEGORIES       │
                                ├─────────────────────┤
                                │ id (PK, bigint)     │
                                │ parent_id (FK)      │◀─┐
                                │ name (varchar)      │  │
                                │ slug (UK, varchar)  │──┘
                                └─────────────────────┘
                                   parent_of (self-ref)
```
