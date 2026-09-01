# 06. Şema Üretimi & Migration

## Genel Bakış

Motor, tüm kontrollerden geçen şemayı SQL dosyası olarak üretir. Migration dosyaları da dahil edilir.

## Doğrulama Kilitleri

Aşağıdaki durumlarda kod üretilmez:

| Kilit | Açıklama |
|-------|----------|
| Desteklenmeyen veri tipi | MySQL'de `ARRAY`, `UUID[]`, `INET` yasak |
| Kayıp ilişkiler | FK'nin işaret ettiği tablo yok |
| Eksik audit trail | PII tablosunun `_audit` tablosu yok |
| Sentaks uyumsuzluğu | MySQL 9 syntax hatası |
| Eksik charset | `utf8mb4_unicode_ci` tanımlı değil |
| Eksik engine | `ENGINE=InnoDB` tanımlı değil |

## Çıktı Dosyaları

| Dosya | İçerik |
|-------|--------|
| `schema.sql` | CREATE TABLE komutları (BCNF uyumlu) |
| `seed_data.sql` | Test verisi (gerçek PII yok) |
| `er_diagram.md` | Mermaid.js ER diyagramı |
| `dictionary.md` | Tablo ve kolon açıklamaları |
| `migration/up.sql` | İleri migration |
| `migration/down.sql` | Geri dönüş migration |

## Expand-Contract (Zero-Downtime) Migration

Tehlikeli değişiklikler için 3 fazlı süreç:

```sql
-- =====================================================
-- FAZ 1: EXPAND (yeni yapı ekle, eski yapıyı koru)
-- =====================================================
ALTER TABLE users ADD COLUMN user_name VARCHAR(100) NOT NULL DEFAULT '';

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
ALTER TABLE users DROP COLUMN username;
DROP TRIGGER IF EXISTS trg_users_sync_name;
```

## Geri Dönüşü Olan Migration

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

## Migration Sıralaması

Tablolar FK bağımlılıklarına göre sırayla oluşturulmalı:

```
1. Ana tablolar (users, products, categories)
2. Bağımlı tablolar (orders, order_items)
3. Junction tabloları (user_groups, product_tags)
4. Audit tabloları (users_audit, orders_audit)
```

## Schema SQL Çıktı Formatı

```sql
-- =========================================================================
-- MODULE: {Modül Adı}
-- VALIDATION: BCNF Passed | Security Checked
-- =========================================================================

-- ADR: {Karar notu}

CREATE TABLE {tablo_adi} (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    -- {kolonlar}
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    -- {index'ler}
    -- {constraint'ler}
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```
