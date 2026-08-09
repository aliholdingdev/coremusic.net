# 08. Agentic Output Examples

## Orkestratör Çıktı Örnekleri

Agentic Orchestrator sistemi sadece düz bir SQL üretmez. Ürettiği çıktılar, "Truth Mode" doğrulama notlarını, ADR (Architecture Decision Records) notlarını ve katı kurallara olan uyumu içerir. 

Aşağıda standart bir SQL çıktısının nasıl olması gerektiğine dair bir örnek verilmiştir.

### Örnek: Kullanıcı (Users) ve Denetim (Audit) Tabloları (MySQL 8+)

```sql
-- =========================================================================
-- MODULE: Users Management
-- AGENT: Security Engineer & Data Engineer
-- VALIDATION: BCNF Passed | OWASP Passed | Zero-Hallucination Checked
-- =========================================================================

-- ADR: 'ssn' kolonu AES-256-GCM ile şifreleneceği için TEXT yapılmıştır.
-- ADR: 'email' kolonu için UNIQUE kapsayıcı index atılmıştır.

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE, -- Dış sistemlerle paylaşılacak Public ID
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    ssn TEXT NULL, -- ⚠️ SECURITY ENGINEER ZORUNLULUĞU: Uygulama katmanında şifrelenmeli
    is_active TINYINT(1) DEFAULT 1,
    
    -- Standart CoreMusic Zaman Damgaları
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX idx_users_is_active ON users(is_active);

-- =========================================================================
-- AUDIT TRAIL: Users Tablosu
-- GEREKÇE: PII verisi barındıran kritik tablo olduğu için Audit zorunludur.
-- =========================================================================

CREATE TABLE users_audit (
    audit_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    action ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    changed_fields JSON,
    acted_by BIGINT UNSIGNED,
    action_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_audit_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- INDEX: Denetim kayıtlarının hızlı listelenmesi için kapsayıcı indeks
CREATE INDEX idx_audit_user_action ON users_audit(user_id, action);
```

**Dikkat Edilmesi Gerekenler:**
1. Yorum satırları sıradan bir dökümantasyon değil, Orkestratörün aldığı mimari kararların (ADR) kanıtlarıdır.
2. `users_audit` tablosu Security Engineer tarafından otomatik zorunlu kılınmıştır.
3. UUID ve AUTO_INCREMENT Primary Key bir arada kullanılarak (Internal ID vs Public ID ayrımı) yüksek performans sağlanmıştır.
