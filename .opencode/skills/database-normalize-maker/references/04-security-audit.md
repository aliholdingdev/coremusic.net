# 04. Güvenlik Denetimi

## Genel Bakış

Veritabanı şeması tasarlanırken güvenlik kontrolleri zorunludur. PII verileri, audit trail ve SQL injection koruması göz ardı edilemez.

## 1. PII Şifrelemesi

TC Kimlik No, Kredi Kartı, Sağlık Verisi gibi hassas veriler düz metin olarak saklanamaz.

| Hassas Kolon | Şifreleme | Kolon Tipi |
|--------------|-----------|------------|
| `ssn` | AES-256-GCM | `TEXT` |
| `national_id` | AES-256-GCM | `TEXT` |
| `credit_card` | AES-256-GCM | `TEXT` |
| `medical_data` | AES-256-GCM | `TEXT` |
| `token` | SHA-256 hash | `VARCHAR(255)` |
| `secret` | AES-256-GCM | `TEXT` |

```sql
-- ADR: 'ssn' kolonu AES-256-GCM ile şifrelenacağı için TEXT yapılmıştır.
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    ssn TEXT NULL, -- ⚠️ Şifrelenmeli (AES-256-GCM)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## 2. Audit Trail (Denetim İzi)

Kritik tablolar için `_audit` tablosu zorunludur:

| Tablo | Audit Tablosu | Zorunlu mu? |
|-------|---------------|-------------|
| `users` | `users_audit` | Evet (PII) |
| `payments` | `payments_audit` | Evet (finansal) |
| `orders` | `orders_audit` | Evet (işlem) |
| `settings` | `settings_audit` | Hayır (düşük risk) |

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

## 3. SQL Injection Koruması

| Kural | Uygulama |
|-------|----------|
| Dinamik SQL birleştirme | YASAK |
| Parametrik sorgular | Zorunlu (PDO prepared statements) |
| Tablo/kolon isimleri | Snake_case (büyük harf yok) |
| Kullanıcı girdisi | Asla doğrudan SQL'e eklenmez |

## 4. Soft Delete Politikası

| Tablo Tipi | Soft Delete | Hard Delete |
|------------|-------------|-------------|
| Varlık tabloları (users, products) | `deleted_at` zorunlu | Yasak |
| Pivot/junction tabloları (user_groups) | `deleted_at` YOK | İlişki koptuğunda |
| Log tabloları (audit) | `deleted_at` YOK | Zaman bazlı partition |
| Geçici tablolar (sessions) | `deleted_at` YOK | Süre dolduğunda |

```sql
-- Soft delete (varlık tablolarında)
UPDATE users SET deleted_at = CURRENT_TIMESTAMP WHERE id = 1;

-- Sorgularken her zaman deleted_at filtresi
SELECT id, username, email FROM users WHERE deleted_at IS NULL;

-- Pivot tablolarında hard delete
DELETE FROM user_groups WHERE user_id = 1 AND group_id = 5;
```

## 5. Foreign Key Güvenliği

| Kural | Açıklama |
|-------|----------|
| ON DELETE stratejisi | Her FK'da seçilmeli |
| CASCADE dikkatli | Zincirleme silmeyi tetikleyebilir |
| RESTRICT tercih | Varsayılan olarak güvenli |
| Circular FK | Yasak — dependency analizi required |

```sql
-- Güvenli FK tanımı
CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE RESTRICT    -- Kullanıcı silinemez, önce siparişler silinmeli
        ON UPDATE CASCADE     -- ID değişirse senkronize ol
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```
