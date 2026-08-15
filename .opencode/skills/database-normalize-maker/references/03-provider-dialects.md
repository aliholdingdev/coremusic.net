# 03. MySQL 9 Diyalektleri & SQL Komut Referansı

## Genel Bakış

Veritabanı motoru, varsayılan olarak **MySQL 9** mimarisine odaklanır. Tüm SQL komutları MySQL 9 uyumlu olmalıdır.

## MySQL 9 Zorunlu Kuralları

| Kural | Detay |
|-------|-------|
| Primary Key | `BIGINT UNSIGNED AUTO_INCREMENT` |
| Tarih Saat | `TIMESTAMP` tercih edilir (timezone farkındalığı) |
| Charset | `utf8mb4` ve `utf8mb4_unicode_ci` zorunlu |
| Engine | `ENGINE=InnoDB` |
| Para Birimi | `DECIMAL(10,2)` — `FLOAT` veya `DOUBLE` yasak |
| JSON | Doğrudan index atılamaz, Virtual Column oluşturulmalı |
| Boolean | `TINYINT(1)` olarak temsil edilir |

## Yasaklı Eylemler

| Yasak | Sebep |
|-------|-------|
| `SELECT *` | Açık kolon listesi zorunlu |
| ORM | PDO prepared statements only |
| `FLOAT` para birimi | Yuvarlama hataları |
| `VARCHAR(255)` her yerde | Gereksiz yer kaplar |
| Tarihler VARCHAR olarak | Sıralama/karşılaştırma bozulur |
| Stored Procedure (fazla) | İş mantığı PHP'de |

## SQL Komut Kategorileri

### DDL (Data Definition Language)

```sql
-- Veritabanı oluşturma
CREATE DATABASE IF NOT EXISTS coremusic
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Tablo oluşturma
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    UNIQUE KEY uk_username (username),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tablo değiştirme
ALTER TABLE users ADD COLUMN phone VARCHAR(20) NULL AFTER email;
ALTER TABLE users DROP COLUMN phone;
ALTER TABLE users MODIFY COLUMN username VARCHAR(100) NOT NULL;

-- Index oluşturma
CREATE INDEX idx_email ON users(email);
CREATE UNIQUE INDEX uk_username ON users(username);
CREATE INDEX idx_composite ON orders(user_id, status, created_at);

-- Tablo silme
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS orders;
SET FOREIGN_KEY_CHECKS = 1;
```

### DML (Data Manipulation Language)

```sql
-- INSERT
INSERT INTO users (username, email, password_hash, first_name, last_name)
VALUES ('bayram', 'bayram@example.com', '$2y$10$...', 'Bayram', 'Ali');

-- Toplu INSERT
INSERT INTO users (username, email, password_hash, first_name, last_name)
VALUES
    ('user1', 'user1@example.com', '$2y$10$...', 'User', 'One'),
    ('user2', 'user2@example.com', '$2y$10$...', 'User', 'Two');

-- Upsert
INSERT INTO settings (key_name, value)
VALUES ('theme', 'dark')
ON DUPLICATE KEY UPDATE value = VALUES(value);

-- UPDATE
UPDATE users SET first_name = 'Bayram Ali' WHERE id = 1;

-- JOIN ile UPDATE
UPDATE orders o
    INNER JOIN users u ON o.user_id = u.id
    SET o.status = 'cancelled'
    WHERE u.is_active = 0;

-- DELETE (soft delete tercih)
UPDATE users SET deleted_at = CURRENT_TIMESTAMP WHERE id = 1;

-- Hard delete (onay gerektirir)
DELETE FROM users WHERE id = 1;
```

### DQL (Data Query Language)

```sql
-- Temel sorgu (SELECT * YASAK)
SELECT id, username, email, created_at
FROM users
WHERE is_active = 1 AND deleted_at IS NULL
ORDER BY created_at DESC
LIMIT 20;

-- INNER JOIN
SELECT u.id, u.username, o.id AS order_id, o.total_price
FROM users u
INNER JOIN orders o ON o.user_id = u.id
WHERE u.is_active = 1;

-- LEFT JOIN
SELECT u.id, u.username, COUNT(o.id) AS total_orders
FROM users u
LEFT JOIN orders o ON o.user_id = u.id AND o.deleted_at IS NULL
WHERE u.deleted_at IS NULL
GROUP BY u.id, u.username;

-- Aggregate
SELECT u.id, u.username,
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
SELECT id, username FROM users
WHERE id IN (SELECT user_id FROM orders WHERE total_price > 100);

-- EXISTS
SELECT u.id, u.username FROM users u
WHERE EXISTS (SELECT 1 FROM orders o WHERE o.user_id = u.id);
```

### TCL (Transaction Control Language)

```sql
START TRANSACTION;
UPDATE accounts SET balance = balance - 100 WHERE id = 1;
UPDATE accounts SET balance = balance + 100 WHERE id = 2;
COMMIT;
-- Hata olursa: ROLLBACK;
```

### DCL (Data Control Language — View & Trigger)

```sql
-- View
CREATE VIEW v_active_users AS
SELECT id, username, email, created_at
FROM users WHERE is_active = 1 AND deleted_at IS NULL;

-- Trigger
DELIMITER //
CREATE TRIGGER trg_users_audit
AFTER UPDATE ON users
FOR EACH ROW
BEGIN
    INSERT INTO users_audit (user_id, action, old_values, new_values)
    VALUES (OLD.id, 'UPDATE',
        JSON_OBJECT('username', OLD.username, 'email', OLD.email),
        JSON_OBJECT('username', NEW.username, 'email', NEW.email)
    );
END //
DELIMITER ;
```

## Veri Tipi Seçim Tablosu

| Veri Tipi | Kullanım Alanı | Boyut |
|-----------|---------------|-------|
| `BIGINT UNSIGNED` | ID, counter | 8 byte |
| `INT UNSIGNED` | Sayı | 4 byte |
| `SMALLINT UNSIGNED` | Kısa sayı | 2 byte |
| `TINYINT(1)` | Boolean | 1 byte |
| `VARCHAR(50)` | Kısa metin | Değişken |
| `VARCHAR(100)` | Orta metin | Değişken |
| `VARCHAR(255)` | E-posta, URL | Değişken |
| `TEXT` | Uzun metin | Değişken |
| `DECIMAL(10,2)` | Para birimi | Sabit |
| `TIMESTAMP` | Tarih/saat | 4 byte |
| `DATE` | Sadece tarih | 3 byte |
| `JSON` | Esnek veri | Değişken |
