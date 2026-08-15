---
title: "CoreMusic — Veritabanı Oluşturma & Normalizasyon Motoru"
type: skill-instruction
version: 5.0
authority: SSOT
mode:
  - Red Team
  - Truth Mode
  - Human Mode
purpose:
  - Database Creation
  - Table Design & Normalization
  - BCNF Compliance
  - SQL Command Generation (CREATE/ALTER/INSERT/UPDATE/DELETE/SELECT)
  - Schema Architecture
  - Migration Management
  - Index Strategy
  - Query Optimization
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/WORKFLOW.md"
    - ".ai/brain.md"
    - ".ai/index.md"
  architecture:
    - ".ai/ADR/"
    - "Existing project architecture"
    - "Existing database schemas"
  templates:
    - ".ai/.templates/adr/adr-database-template.md"
    - ".ai/.templates/query/Query-Template.md"
    - ".ai/.templates/infrastructure/migration-template.md"
  agents:
    - ".ai/.agents/AGENTS.md"
    - ".ai/.agents/data-engineer.md"
  project_structure:
    - "coremusic.net/"
    - "shared/"
    - ".ai/.sql/"
  decision_priority:
    - "ADR decisions"
    - "BCNF rules"
    - "Security requirements"
    - "Existing implementation"
  update_policy:
    preserve_existing_structure: true
    require_approval_for:
      - "schema strategy change"
      - "normalization rule change"
      - "database engine change"
triggers:
  - "database oluştur"
  - "veritabanı oluştur"
  - "tablo oluştur"
  - "sql yaz"
  - "schema oluştur"
  - "normalize"
  - "normalizasyon"
  - "bcnf"
  - "3nf"
  - "veritabanı tasarla"
  - "migration"
  - "veri modeli"
  - "er diagram"
  - "tablo tasarla"
  - "kolon ekle"
  - "index oluştur"
  - "foreign key"
  - "sql çalıştır"
  - "sorgu yaz"
  - "select sorgusu"
  - "tabloyu düzenle"
  - "kolon sil"
  - "veri ekle"
  - "veri güncelle"
  - "veri sil"
  - "join sorgusu"
  - "aggregate"
  - "transaction"
  - "view oluştur"
changelog:
  - version: 5.0
    date: 2026-08-15
    changes:
      - Complete rewrite with web-research best practices
      - Added Model → Migrate → Validate workflow
      - Added expand-contract zero-downtime migration pattern
      - Added comprehensive anti-pattern catalog
      - Added constraints catalog
      - Added common schema patterns (soft delete, junction table, audit trail)
      - Added index design strategy with query pattern mapping
      - Added expanded verification checklist (25 items)
      - Added MySQL 9 specific rules and syntax
      - Added all SQL command types with examples
      - Added error handling reference
---

# Veritabanı Oluşturma & Normalizasyon Motoru

## 1. Bu Skill Ne Yapar?

Bu skill, CoreMusic projesinde:
- **Veritabanı şeması** tasarlar ve oluşturur
- **Tabloları** normalizasyon kurallarına göre (1NF → 2NF → 3NF → BCNF) düzenler
- **SQL komutlarını** üretir: CREATE, ALTER, INSERT, UPDATE, DELETE, SELECT, JOIN, TRANSACTION, VIEW, TRIGGER
- **Index stratejisi** belirler ve uygular
- **Migration scriptleri** oluşturur (expand-contract zero-downtime)
- **ER diyagramı** üretir (Mermaid.js)
- **Sorgu optimizasyonu** yapar
- **Güvenlik kontrolleri** uygular (PII, şifreleme, audit)

**Kullanmaz:**
- ORM (ADR-021: ORM yasak — sadece PDO prepared statements)
- Framework (ADR-001: Framework yasak)
- `SELECT *` (yasak — tüm kolonları açıkla)
- `FLOAT` para birimi (yasak — `DECIMAL(10,2)` kullan)

**Temel Prensip:** Her SQL komutunu bu belgedeki §6 referansına göre üret. Komutların MySQL 9 uyumlu, BCNF uyumlu ve güvenli olmalıdır.

---

## 2. CoreMusic Veritabanı Mimarisi

### 2.1 11 BCNF Veritabanı

| # | Veritabanı | Amaç | Tablo Sayısı |
|---|-----------|------|--------------|
| 1 | `coremusic_auth` | Kimlik doğrulama | 5 |
| 2 | `coremusic_users` | Kullanıcı yönetimi | 8 |
| 3 | `coremusic_musics` | Müzik kataloğu | 12 |
| 4 | `coremusic_albums` | Albüm yönetimi | 6 |
| 5 | `coremusic_playlist` | Çalma listeleri | 5 |
| 6 | `coremusic_catalog` | Kataloglama | 8 |
| 7 | `coremusic_logs` | Log yönetimi | 4 |
| 8 | `coremusic_media` | Medya dosyaları | 6 |
| 9 | `coremusic_system` | Sistem ayarları | 3 |
| 10 | `coremusic_social` | Sosyal özellikler | 7 |
| 11 | `coremusic_wireless` | Kablosuz bağlantı | 4 |

### 2.2 MySQL 9 Zorunlu Kuralları

| Kural | Detay |
|-------|-------|
| Motor | `ENGINE=InnoDB` |
| Charset | `DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci` |
| Primary Key | `id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY` |
| Timestamp | `created_at`, `updated_at`, `deleted_at` (her tabloda) |
| Para | `DECIMAL(10,2)` — asla `FLOAT` |
| ORM | Yasak — PDO prepared statements only |
| SELECT * | Yasak — açık kolon listesi |
| Stored Procedure | İş mantığı PHP'de, SP minimum |
| Trigger | Sadece audit trail için |

### 2.3 İsimlendirme Standartları

| Öğe | Format | Örnek |
|-----|--------|-------|
| Tablolar | Çoğul, snake_case | `users`, `order_items` |
| Kolonlar | snake_case | `first_name`, `created_at` |
| Primary Key | `id` | `id BIGINT UNSIGNED` |
| Foreign Key | `{tablo}_id` | `user_id`, `product_id` |
| Index | `idx_{kolon}` | `idx_email` |
| Unique Index | `uk_{kolon}` | `uk_username` |
| FK Constraint | `fk_{çocuk}_{ana}` | `fk_orders_users` |
| View | `v_{amaç}` | `v_active_users` |
| Trigger | `trg_{tablo}_{olay}` | `trg_users_audit` |

### 2.4 Zorunlu Kolon Şablonu

Her varlık tablosunda bu kolonlar olmalıdır:

```sql
id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
deleted_at TIMESTAMP NULL DEFAULT NULL
```

---

## 3. Normalizasyon Kuralları

### 3.1 Birinci Normal Form (1NF)

**Kural:** Her kolon atomik değer tutar. Liste, dizi veya çoklu değer yoktur.

```sql
-- YANLIŞ (1NF ihlali — virgülle ayrılmış liste)
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    hobbies TEXT  -- "yüzme, koşu, bisiklet"
);

-- DOĞRU (1NF uyumlu — ayrı tablo)
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_hobbies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    hobby VARCHAR(100) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Kontrol Sorusu:** Her kolonda sadece bir değer mi var? `phone1, phone2, phone3` gibi tekrarlayan gruplar var mı?

### 3.2 İkinci Normal Form (2NF)

**Kural:** 1NF + Kısmi bağımlılık yok. Composite PK'da tüm non-key kolonlar tam bağımlı olmalı.

```sql
-- YANLIŞ (2NF ihlali — product_name sadece product_id'ye bağlı)
CREATE TABLE order_items (
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    product_name VARCHAR(100),  -- order_id'ye bağlı değil
    quantity INT NOT NULL,
    PRIMARY KEY (order_id, product_id)
);

-- DOĞRU (2NF uyumlu)
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Kontrol Sorusu:** Composite PK var mı? Varsa, her non-key kolon her iki key'e de bağımlı mı?

### 3.3 Üçüncü Normal Form (3NF)

**Kural:** 2NF + Geçici bağımlılık yok. Non-key kolonlar sadece primary key'e bağlı olmalı.

```sql
-- YANLIŞ (3NF ihlali — city_name city_id'ye bağlı, id'ye değil)
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    city_id BIGINT UNSIGNED NOT NULL,
    city_name VARCHAR(100),   -- transitive dependency
    city_country VARCHAR(100) -- transitive dependency
);

-- DOĞRU (3NF uyumlu)
CREATE TABLE cities (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    city_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Kontrol Sorusu:** `A → B → C` gibi zincirleme bağımlılık var mı? Varsa, ayrı tabloya taşı.

### 3.4 Boyce-Codd Normal Form (BCNF)

**Kural:** 3NF + Her determinant (belirleyici) bir candidate key olmalı.

```sql
-- YANLIŞ (BCNF ihlali — instructor_id belirleyici ama key değil)
CREATE TABLE course_enrollments (
    student_id BIGINT UNSIGNED NOT NULL,
    course_id BIGINT UNSIGNED NOT NULL,
    instructor_id BIGINT UNSIGNED NOT NULL,
    instructor_name VARCHAR(100),
    PRIMARY KEY (student_id, course_id)
);

-- DOĞRU (BCNF uyumlu)
CREATE TABLE instructors (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE courses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    instructor_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (instructor_id) REFERENCES instructors(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE course_enrollments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    student_id BIGINT UNSIGNED NOT NULL,
    course_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 3.5 Denormalizasyon Ne Zaman Kabul Edilir?

Denormalizasyon SADECE şu şartlarda kabul edilir:

| Şart | Gerekçe |
|------|---------|
| Verinin tarihsel olarak dondurulması | Audit trail, log snapshot |
| Yoğun okunma senaryosu | Join maliyeti ölçülmüş ve çok yüksek |
| ADR ile gerekçelendirilmiş | `ADR-XXX: total_price denormalize edildi, çünkü...` |

```sql
-- Denormalizasyon örneği (ADR gerekçeli)
-- ADR-XXX: 'total_price' kolonu denormalize edildi
-- Gerekçe: Raporlama ekranında çok ağır Join işlemlerini engellemek
CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    item_count INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Kural:** Denormalizasyon her zaman `updated_at` trigger ile senkronize edilmeli.

---

## 4. İlişki Türleri ve Şablonlar

### 4.1 One-to-Many (Bire Çok)

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uk_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4.2 Many-to-Many (Çoktan Çoğa — Junction Table)

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uk_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE groups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uk_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_groups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    group_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (group_id) REFERENCES groups(id) ON DELETE CASCADE,
    UNIQUE KEY uk_user_group (user_id, group_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4.3 One-to-One (Bire Bir)

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uk_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_profiles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    bio TEXT,
    avatar_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4.4 Self-Referencing (Kendi Kendine)

```sql
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    parent_id BIGINT UNSIGNED NULL DEFAULT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_parent_id (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 5. Yaygın Şema Kalıpları (Common Patterns)

### 5.1 Soft Delete

```sql
-- Her tabloda deleted_at kolonu olmalı
-- Hard delete ASLA yapılmaz (ADR kuralı)
UPDATE users SET deleted_at = CURRENT_TIMESTAMP WHERE id = 1;

-- Sorgularken:
SELECT id, username, email
FROM users
WHERE deleted_at IS NULL AND is_active = 1;
```

### 5.2 Audit Trail

```sql
CREATE TABLE users_audit (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    action ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    acted_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 5.3 Metadata (JSON Kolon)

```sql
-- Esnek veri için JSON kolon (MySQL 9 native JSON desteği)
ALTER TABLE products
    ADD COLUMN metadata JSON NULL DEFAULT NULL;

-- JSON index (virtual column ile)
ALTER TABLE products
    ADD COLUMN metadata_genre VARCHAR(50) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.genre'))) VIRTUAL,
    ADD INDEX idx_metadata_genre (metadata_genre);
```

### 5.4 Enum Yerine Lookup Tablosu

```sql
-- YANLIŞ (MySQL ENUM sabit, değişiklik zor)
status ENUM('active', 'inactive', 'banned')

-- DOĞRU (lookup tablosu — esnek, normalize)
CREATE TABLE statuses (
    id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    label VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO statuses (code, label) VALUES ('active', 'Active'), ('inactive', 'Inactive');
```

---

## 6. SQL Komut Referansı

### 6.1 Veritabanı Oluşturma

```sql
CREATE DATABASE IF NOT EXISTS coremusic
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE coremusic;
```

### 6.2 Tablo Oluşturma (CREATE TABLE)

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uk_username (username),
    UNIQUE KEY uk_email (email),
    INDEX idx_is_active (is_active),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 6.3 Tablo Değiştirme (ALTER TABLE)

```sql
-- Kolon ekleme
ALTER TABLE users
    ADD COLUMN phone VARCHAR(20) NULL AFTER email;

-- Kolon silme
ALTER TABLE users
    DROP COLUMN phone;

-- Kolon değiştirme
ALTER TABLE users
    MODIFY COLUMN username VARCHAR(100) NOT NULL;

-- Kolon adını değiştirme (3 faz expand-contract)
-- Faz 1: Yeni kolon ekle
ALTER TABLE users ADD COLUMN user_name VARCHAR(100) NOT NULL DEFAULT '';
-- Faz 2: Veriyi kopyala
UPDATE users SET user_name = username;
-- Faz 3: Eski kolonu sil (onay sonrası)
ALTER TABLE users DROP COLUMN username;
```

### 6.4 Index Oluşturma

```sql
-- Basit index
CREATE INDEX idx_email ON users(email);

-- Benzersiz index
CREATE UNIQUE INDEX uk_username ON users(username);

-- Kapsayıcı (composite) index — equality primero, sonra range
CREATE INDEX idx_user_status ON orders(user_id, status, created_at);

-- Partial index (MySQL 8+)
CREATE INDEX idx_active_users ON users(email) WHERE is_active = 1;

-- MySQL 9 full-text index
CREATE FULLTEXT INDEX ft_search ON products(name, description);
```

### 6.5 Foreign Key Oluşturma

```sql
-- Tablo içi foreign key
CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    INDEX idx_user_id (user_id),
    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sonradan foreign key ekleme
ALTER TABLE orders
    ADD CONSTRAINT fk_orders_users
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE;
```

### 6.6 Tablo Silme (DROP TABLE)

```sql
-- Foreign key kontrolü ile güvenli silme
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS orders;
SET FOREIGN_KEY_CHECKS = 1;
```

### 6.7 Veri Ekleme (INSERT)

```sql
-- Tek satır
INSERT INTO users (username, email, password_hash, first_name, last_name)
VALUES ('bayram', 'bayram@example.com', '$2y$10$...', 'Bayram', 'Ali');

-- Toplu ekleme
INSERT INTO users (username, email, password_hash, first_name, last_name)
VALUES
    ('user1', 'user1@example.com', '$2y$10$...', 'User', 'One'),
    ('user2', 'user2@example.com', '$2y$10$...', 'User', 'Two'),
    ('user3', 'user3@example.com', '$2y$10$...', 'User', 'Three');

-- Upsert (INSERT OR UPDATE)
INSERT INTO settings (key_name, value)
VALUES ('theme', 'dark')
ON DUPLICATE KEY UPDATE value = VALUES(value);
```

### 6.8 Veri Güncelleme (UPDATE)

```sql
-- Tek satır
UPDATE users
    SET first_name = 'Bayram Ali',
        updated_at = CURRENT_TIMESTAMP
    WHERE id = 1;

-- Koşullu güncelleme
UPDATE users
    SET is_active = 0
    WHERE last_login < DATE_SUB(NOW(), INTERVAL 1 YEAR);

-- Join ile güncelleme
UPDATE orders o
    INNER JOIN users u ON o.user_id = u.id
    SET o.status = 'cancelled'
    WHERE u.is_active = 0;
```

### 6.9 Veri Silme (DELETE)

```sql
-- Soft delete (tercih edilen)
UPDATE users
    SET deleted_at = CURRENT_TIMESTAMP
    WHERE id = 1;

-- Hard delete (dikkatli kullanılmalı, onay gerektirir)
DELETE FROM users WHERE id = 1;
```

### 6.10 Sorgu Komutları (SELECT)

```sql
-- Temel sorgu (SELECT * YASAK — açık kolon listesi)
SELECT id, username, email, created_at
FROM users
WHERE is_active = 1 AND deleted_at IS NULL
ORDER BY created_at DESC
LIMIT 20;

-- JOIN sorgusu
SELECT
    u.id,
    u.username,
    u.email,
    o.id AS order_id,
    o.total_price,
    o.created_at AS order_date
FROM users u
INNER JOIN orders o ON o.user_id = u.id
WHERE u.is_active = 1 AND u.deleted_at IS NULL
ORDER BY o.created_at DESC;

-- LEFT JOIN (sol taraftaki tüm kayıtlar)
SELECT
    u.id,
    u.username,
    COUNT(o.id) AS total_orders
FROM users u
LEFT JOIN orders o ON o.user_id = u.id AND o.deleted_at IS NULL
WHERE u.deleted_at IS NULL
GROUP BY u.id, u.username;

-- Aggregate sorgu
SELECT
    u.id,
    u.username,
    COUNT(o.id) AS total_orders,
    SUM(o.total_price) AS total_spent,
    AVG(o.total_price) AS avg_order
FROM users u
LEFT JOIN orders o ON o.user_id = u.id AND o.deleted_at IS NULL
WHERE u.deleted_at IS NULL
GROUP BY u.id, u.username
HAVING total_orders > 0
ORDER BY total_spent DESC;

-- Subquery
SELECT id, username, email
FROM users
WHERE id IN (
    SELECT user_id
    FROM orders
    WHERE total_price > 100 AND deleted_at IS NULL
);

-- EXISTS (subquery'den hızlı olabilir)
SELECT u.id, u.username
FROM users u
WHERE EXISTS (
    SELECT 1 FROM orders o WHERE o.user_id = u.id AND o.deleted_at IS NULL
);
```

### 6.11 Transaction Komutları

```sql
START TRANSACTION;

UPDATE accounts SET balance = balance - 100 WHERE id = 1;
UPDATE accounts SET balance = balance + 100 WHERE id = 2;

-- Hata olursa:
-- ROLLBACK;

-- Hata yoksa:
COMMIT;
```

### 6.12 View Oluşturma

```sql
CREATE VIEW v_active_users AS
SELECT id, username, email, created_at
FROM users
WHERE is_active = 1 AND deleted_at IS NULL;
```

### 6.13 Trigger (Audit Trail)

```sql
DELIMITER //

CREATE TRIGGER trg_users_audit
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    INSERT INTO users_audit (user_id, action, old_values, new_values, acted_by)
    VALUES (
        OLD.id,
        'UPDATE',
        JSON_OBJECT(
            'username', OLD.username,
            'email', OLD.email,
            'is_active', OLD.is_active
        ),
        JSON_OBJECT(
            'username', NEW.username,
            'email', NEW.email,
            'is_active', NEW.is_active
        ),
        @current_user_id
    );
END //

DELIMITER ;
```

---

## 7. Veri Tipi Seçim Tablosu

| Veri Tipi | Kullanım Alanı | Örnek | Boyut |
|-----------|---------------|-------|-------|
| `BIGINT UNSIGNED` | ID, counter | `id BIGINT UNSIGNED AUTO_INCREMENT` | 8 byte |
| `INT UNSIGNED` | Sayı | `quantity INT UNSIGNED` | 4 byte |
| `SMALLINT UNSIGNED` | Kısa sayı | `status SMALLINT UNSIGNED` | 2 byte |
| `TINYINT UNSIGNED` | Bayt | `is_active TINYINT(1)` | 1 byte |
| `VARCHAR(50)` | Kısa metin | `username VARCHAR(50)` | Değişken |
| `VARCHAR(100)` | Orta metin | `name VARCHAR(100)` | Değişken |
| `VARCHAR(255)` | E-posta, URL | `email VARCHAR(255)` | Değişken |
| `TEXT` | Uzun metin | `description TEXT` | Değişken |
| `MEDIUMTEXT` | Çok uzun metin | `content MEDIUMTEXT` | Değişken |
| `LONGTEXT` | Dev metin | `raw_data LONGTEXT` | Değişken |
| `DECIMAL(10,2)` | Para birimi | `price DECIMAL(10,2)` | Sabit |
| `DECIMAL(10,4)` | Hassas sayı | `exchange_rate DECIMAL(10,4)` | Sabit |
| `BOOLEAN` | Evet/Hayır | `is_active BOOLEAN DEFAULT TRUE` | 1 byte |
| `TIMESTAMP` | Tarih/saat | `created_at TIMESTAMP` | 4 byte |
| `DATETIME` | Tarih/saat (timezone yok) | `birth_date DATETIME` | 8 byte |
| `DATE` | Sadece tarih | `order_date DATE` | 3 byte |
| `ENUM` | Sabit seçenekler | `status ENUM('aktif','pasif')` | Değişken |
| `JSON` | Esnek veri | `metadata JSON` | Değişken |
| `BINARY(16)` | UUID | `uuid BINARY(16)` | 16 byte |

---

## 8. İndeks Tasarım Stratejisi

### 8.1 Hangi Sorgu Kalıbı Hangi Index'i Gerektirir?

| Sorgu Kalıbı | Index Tipi | Örnek |
|---------------|------------|-------|
| Eşleşme (equality) | B-tree | `WHERE status = 'active'` |
| Aralık (range) | B-tree | `WHERE created_at > '2024-01-01'` |
| Sıralama (sort) | B-tree | `ORDER BY created_at DESC` |
| Full-text arama | FULLTEXT | `WHERE name MATCH 'keyword'` |
| JSON alanı | Virtual column + index | `JSON_EXTRACT(metadata, '$.key')` |
| Çoklu sütun WHERE | Composite index | `WHERE user_id = 1 AND status = 'active'` |

### 8.2 Composite Index Sıralama Kuralı

```
Composite index: (equality_first, equality_second, range_or_sort)
```

```sql
-- Sorgu: WHERE user_id = ? AND status = ? ORDER BY created_at DESC
-- Doğru index:
CREATE INDEX idx_user_status_date ON orders(user_id, status, created_at);
```

### 8.3 Index Kuralları

| Kural | Açıklama |
|-------|----------|
| Her FK'ya index | `FOREIGN KEY (user_id)` → `INDEX idx_user_id (user_id)` |
| Sık sorgulanan kolonlara index | `WHERE email = ?` → `INDEX idx_email (email)` |
| ORDER BY kolonlarına index | `ORDER BY created_at` → `INDEX idx_created_at (created_at)` |
| Composite index: equality primero | `WHERE a = ? AND b > ?` → `INDEX (a, b)` |
| Over-indexing kaçın | Her index写写'ı yavaşlatır |
| Prefix index uzun string için | `INDEX idx_name (name(20))` |

---

## 9. Migration Stratejisi

### 9.1 Expand-Contract (Zero-Downtime) Pattern

Tehlikeli değişiklikler için 3 fazlı süreç:

```sql
-- =====================================================
-- FAZ 1: EXPAND (yeni yapı ekle, eski yapıyı koru)
-- =====================================================
-- Yeni kolon ekle
ALTER TABLE users ADD COLUMN user_name VARCHAR(100) NOT NULL DEFAULT '';

-- Her iki_WRITE_TRUE: hem eski hem yeni yaz
-- Trigger ile senkronize et
DELIMITER //
CREATE TRIGGER trg_users_sync_name
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
    IF NEW.username != OLD.username THEN
        SET NEW.user_name = NEW.username;
    END IF;
END //
DELIMITER ;

-- =====================================================
-- FAZ 2: MIGRATE (veriyi kopyala)
-- =====================================================
UPDATE users SET user_name = username WHERE user_name = '';

-- =====================================================
-- FAZ 3: CONTRACT (eski yapıyı kaldır)
-- =====================================================
-- Onay sonrası:
ALTER TABLE users DROP COLUMN username;
DROP TRIGGER IF EXISTS trg_users_sync_name;
```

### 9.2 Geri Dönüşü Olan Migration

```sql
-- UP (ileri)
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- DOWN (geri)
DROP TABLE IF EXISTS products;
```

---

## 10. Hata Durumları

| Hata | Kök Neden | Çözüm |
|------|-----------|-------|
| `Duplicate entry` | UNIQUE constraint ihlali | Veriyi kontrol et |
| `Cannot add foreign key` | Tip uyumsuzluğu | BIGINT UNSIGNED olduğundan emin ol |
| `Table doesn't exist` | Sıra hatası | Tabloları doğru sırada oluştur (FK bağımlılıkları) |
| `Column count doesn't match` | Eksik kolon | Tüm kolonları listele |
| `Data too long` | VARCHAR limiti aşımı | Kolon boyutunu artır veya TEXT kullan |
| `Unknown column` | Yazım hatası | Kolon adını kontrol et |
| `Incorrect table definition` | Yanlış syntax | MySQL 9 syntax kontrolü yap |
| `Lock wait timeout exceeded` | Kilit hatası | Transaction süresini kısalt |
| `Row size too large` | Kolon toplamı çok büyük | TEXT/BLOB'ları ayrı tabloya taşı |
| `Key column doesn't exist` | FK referans eksik | Önce ana tabloyu oluştur |

---

## 11. Workflow: Model → Migrate → Validate

### Adım 1: MODEL — Gereksinimleri Anla ve Şemayı Tasarla

Kullanıcıdan şu bilgileri topla:

| Soru | Amaç |
|------|------|
| Hangi tablolar gerekli? | Varlık listesi |
| İlişkiler nasıl? | One-to-many, many-to-many |
| Hangi alanlar sensitive? | PII, şifreleme gerekli mi? |
| Beklenen veri miktarı? | Performans planlaması |
| Ana sorgu kalıpları hangileri? | Index stratejisi |
| Soft delete gerekli mi? | deleted_at kolonu |
| Audit trail gerekli mi? | Audit tablosu |

Eksik bilgi varsa sor:

```markdown
Veritabanını tasarlayabilmem için şu bilgilere ihtiyacım var:

1. **Tablolar:** Hangi tabloları oluşturmak istiyorsunuz?
2. **İlişkiler:** Tablolar arası nasıl ilişkiler var?
3. **Alanlar:** Her tabloda hangi alanlar olacak?
4. **Güvenlik:** Şifrelenmesi gereken alanlar var mı?
5. **Ölçek:** Yaklaşık ne kadar veri bekleniyor?
6. **Sorgular:** En sık çalıştırılacak sorgular hangileri?
```

### Adım 2: DESIGN — Şema Tasarımı

Her tablo için:
1. Kolonları listele
2. Veri tiplerini seç (MySQL 9 uyumlu)
3. Primary key belirle (`id BIGINT UNSIGNED AUTO_INCREMENT`)
4. Foreign key'leri tanımla (`ON DELETE` stratejisi seç)
5. Constraint'leri ekle (UNIQUE, CHECK, NOT NULL)
6. Index'leri planla (FK'lar + sık sorgulanan kolonlar)
7. Audit kolonlarını ekle (created_at, updated_at, deleted_at)

### Adım 3: NORMALIZE — Normalizasyon Kontrolü

Her tabloyu kontrol et:

| Kontrol | Soru |
|---------|------|
| 1NF | Kolonlar atomik mi? Liste var mı? |
| 2NF | Composite PK varsa, kısmi bağımlılık var mı? |
| 3NF | Geçici bağımlılık var mı? (A → B → C) |
| BCNF | Her determinant key mi? |

### Adım 4: MIGRATE — SQL Komutlarını Üret

Oluşturulan şemaya göre SQL komutlarını yaz. Tüm SQL komutlarını bu belgedeki §6 referansına göre üret.

Migration dosyaları oluştur:
- `up.sql` — İleri migration
- `down.sql` — Geri dönüş migration

### Adım 5: DOCUMENT — Çıktıları Oluştur

| Dosya | İçerik |
|-------|--------|
| `schema.sql` | CREATE TABLE komutları |
| `seed_data.sql` | Test verisi (gerçek PII yok) |
| `er_diagram.md` | Mermaid.js ER diyagramı |
| `dictionary.md` | Tablo ve kolon açıklamaları |
| `migration/up.sql` | İleri migration |
| `migration/down.sql` | Geri dönüş migration |

### Adım 6: VALIDATE — Doğrulama

Kontrol listesi (25 madde):

**Tablo Yapısı:**
- [ ] Her tablonun PRIMARY KEY'i var mı?
- [ ] Tüm BIGINT UNSIGNED mi?
- [ ] Charset ve Collation tanımlı mı? (`utf8mb4_unicode_ci`)
- [ ] Engine InnoDB mi?
- [ ] Timestamp kolonları var mı? (created_at, updated_at, deleted_at)

**İlişkiler:**
- [ ] Tüm Foreign Key'ler doğru tanımlı mı?
- [ ] ON DELETE stratejisi seçilmiş mi?
- [ ] FK'lara index eklendi mi?
- [ ] Circular dependency yok mu?

**Normalizasyon:**
- [ ] Tüm tablolar BCNF uyumlu mu?
- [ ] `SELECT *` kullanılmamış mı?
- [ ] Denormalizasyon varsa ADR ile gerekçelendirilmiş mi?

**Güvenlik:**
- [ ] PII alanları şifreli mi?
- [ ] Audit tabloları var mı?
- [ ] Hard delete yerine soft delete kullanılıyor mu?

**Performans:**
- [ ] Sık sorgulanan kolonlara index eklendi mi?
- [ ] Composite index sıralaması doğru mu? (equality primero)
- [ ] Over-indexing yok mu?

**Migration:**
- [ ] Geri dönüş (down) migration var mı?
- [ ] Expand-contract pattern uygulandı mı? (tehlikeli değişikliklerde)
- [ ] Veri kaybı riski yok mu?

**Genel:**
- [ ] Tüm tabloların adı doğru mu? (snake_case, çoğul)
- [ ] Tüm kolonların adı doğru mu? (snake_case)
- [ ] Constraint adlandırma tutarlı mı? (uk_, idx_, fk_)

---

## 12. Anti-Pattern Kataloğu

| Anti-Pattern | Sorun | Çözüm |
|--------------|-------|-------|
| `VARCHAR(255)` her yerde | Gereksiz yer kaplar, niyet gizlenir | Gerçek boyuta göre seç: `VARCHAR(50)`, `VARCHAR(100)` |
| `FLOAT` para birimi | Yuvarlama hataları | `DECIMAL(10,2)` |
| FK constraint yok | Yetim kayıtlar, veri bozulması | Her ilişkiye `FOREIGN KEY` + `ON DELETE` |
| FK'lara index yok | Çok yavaş JOIN'ler | `INDEX idx_{tablo}_id ({tablo}_id)` |
| Tarihler VARCHAR olarak | Sıralama/ karşılaştırma bozulur | `DATE`, `TIMESTAMP`, `DATETIME` |
| `SELECT *` kullanımı | Performans kaybı, API sözleşme kırılması | Açık kolon listesi |
| EAV (Entity-Attribute-Value) | Sorgu karmaşıklığı, tip güvenliği yok | Structured schema + JSON |
| Polymorphic association | FK bütünlüğü yok, karmaşık sorgular | Ayrı tablolar veya `type` + `id` |
| Circular dependency | Populate edilemez, CASCADE kırılır | Dependency analizi |
| `ENUM` sabitleri | Değişiklik zor, ALTER TABLE gerektirir | Lookup tablosu |
| Audit trail yok | Değişiklik takibi yapılamaz | `_audit` tablosu + trigger |
| Soft delete yok | Hard delete geri alınamaz | `deleted_at TIMESTAMP NULL` |
| Composite PK'da eksik index | Kısmi sorgular yavaş | Composite index ekle |
| Gereksiz stored procedure | Bakım zor, PHP'de yapılabilir | İş mantığı PHP'de |

---

## 13. Referans Dosyaları

Alt dizindeki detaylı referans dosyaları:

| Dosya | Amaç |
|-------|------|
| `references/00-overview.md` | Genel bakış |
| `references/01-requirements-gathering.md` | Gereksinim toplama |
| `references/02-normalization-rules.md` | Normalizasyon kuralları |
| `references/03-provider-dialects.md` | Motor diyalektleri |
| `references/04-security-audit.md` | Güvenlik denetimi |
| `references/05-performance-optimization.md` | Performans optimizasyonu |
| `references/06-schema-generation.md` | Şema üretimi |
| `references/07-coremusic-integration.md` | CoreMusic entegrasyonu |
| `references/08-examples.md` | Örnek çıktılar |
| `references/09-anti-patterns.md` | Anti-pattern'ler |
| `references/10-checklist.md` | QA kontrol listesi |
| `scripts/normalize-checker.php` | Normalizasyon kontrol scripti |
| `scripts/schema-to-diagram.php` | Şema → ER diyagramı |
| `scripts/security-audit.php` | Güvenlik denetim scripti |
| `templates/schema-template.sql` | Şema şablonu |
| `templates/seed-template.sql` | Seed veri şablonu |
| `templates/migration-template.sql` | Migration şablonu |

---

## 14. Bağlantılar

- **Vault:** `.ai/brain.md` — Veritabanı mimarisi kararları
- **ADR:** `.ai/ADR/` — Veritabanı ile ilgili ADR'ler
- **SQL:** `.ai/.sql/` — Mevcut şema dosyaları
- **Routing:** `agent-orchestrator` — database keyword yönlendirmesi
- **Agent:** `data-engineer` — Veritabanı uzmanı
- **Agent:** `security-engineer` — Güvenlik kontrolleri

---

*Veritabanı Oluşturma & Normalizasyon Motoru v5.0*
*Authority: Bayram Ali / Vault Steward*
*Mode: Red Team · Truth Mode · Human Mode*
*Last Updated: 2026-08-15*
