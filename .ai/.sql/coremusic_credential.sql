-- ══════════════════════════════════════════════════════════════════════════════
-- coremusic_credential — KREDANSİYEL KASASI (GÜVENLİ)
-- COREMUSIC DB v1.0 | Temmuz 2026 | MySQL 8.x InnoDB | utf8mb4_turkish_ci
-- NF: BCNF
-- ══════════════════════════════════════════════════════════════════════════════
--
-- ⚠️ GÜVENLİK — KRİTİK UYARI:
--    Bu veritabanı, üçüncü taraf servisler (Deezer, YouTube, Spotify) için
--    API anahtarları ve kullanıcı kimlik bilgilerini saklar.
--
--    TÜM hassas sütunlar AES-256-GCM ile şifrelenmelidir.
--    Şifreleme anahtarı environment variable: COREMUSIC_CRED_VAULT_KEY
--    Uzunluk: 32 byte (256 bit), hex veya base64 formatında.
--
--    Şifreleme, uygulama katmanında yapılır. MySQL şifreleme fonksiyonları
--    KULLANILMAZ. DB yalnızca VARBINARY olarak saklar.
--
-- TABLOLAR:
--   credential_vault    → API anahtarları ve kullanıcı kimlik bilgileri (şifreli)
--   credential_keys     → Şifreleme anahtarı versiyon yönetimi
--   credential_audit    → Kredansiyel erişim denetim kaydı (append-only)
--
-- ══════════════════════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS coremusic_credential
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_turkish_ci;

USE coremusic_credential;


-- ──────────────────────────────────────────────────────────────────────────────
-- CREDENTIAL_VAULT — API anahtarları ve kullanıcı kimlik bilgileri (şifreli)
-- Normal Form : BCNF
-- Bağımlılık  : id → {user_id, service, api_key_encrypted, key_version, ...}
--
-- ⚠️ GÜVENLİK:
--    api_key_encrypted: AES-256-GCM şifreli (nonce + ciphertext + auth_tag)
--    api_secret_encrypted: Varsa ikinci anahtar (OAuth refresh token vb.)
--    encryption_nonce: Her şifreleme için rastgele 96-bit nonce (12 byte, hex)
--    encryption_aad: Ek doğrulanmış veri (opsiyonel, uygulama belirler)
--    key_version: credential_keys.id referansı
--
--    NOT — Uygulama katmanı şifreleme formatı:
--      ciphertext = VARBINARY(512)
--      nonce = random_bytes(12) → hex(24)
--      encrypted = hex(nonce) || ':' || base64(ciphertext)
--    Bu format uygulama standardıdır. DB SADECE VARBINARY depolar.
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE credential_vault (
    id                  INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id             INT UNSIGNED        NOT NULL,
    service             VARCHAR(30)         NOT NULL,
    -- service: deezer | youtube | spotify | soundcloud | apple_music | tidal
    api_key_encrypted   VARBINARY(512)      NOT NULL,   -- AES-256-GCM şifreli
    api_secret_encrypted VARBINARY(512)         NULL,   -- İkinci şifre / refresh token
    encryption_nonce    CHAR(24)            NOT NULL,   -- hex(random_bytes(12))
    encryption_aad      TEXT                    NULL,   -- Ek doğrulanmış veri
    key_version         INT UNSIGNED        NOT NULL DEFAULT 1,
    -- key_version → credential_keys.id
    is_active           TINYINT(1)          NOT NULL DEFAULT 1,
    last_used_at        DATETIME                NULL,
    expires_at          DATETIME                NULL,
    created_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    -- ⚠️ user_id → coremusic_auth.users.id: cross-DB FK

    UNIQUE  KEY uq_cv_user_service   (user_id, service),
    INDEX idx_cv_service         (service),
    INDEX idx_cv_active          (is_active),
    INDEX idx_cv_key_version     (key_version),
    INDEX idx_cv_last_used       (last_used_at DESC),
    INDEX idx_cv_expires         (expires_at),

    CHECK (service IN ('deezer','youtube','spotify','soundcloud','apple_music','tidal'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- CREDENTIAL_KEYS — Şifreleme anahtarı versiyon yönetimi
-- Normal Form : BCNF
-- Bağımlılık  : id → {key_name, key_hash, key_purpose, is_active, ...}
--
-- ⚠️ GÜVENLİK:
--    key_hash: Anahtarın SHA-256 hash'i (doğrulama için). Ham anahtar ASLA DB'DE SAKLANMAZ.
--    Ham anahtar environment variable: COREMUSIC_CRED_VAULT_KEY.
--    Versiyonlama: Anahtar döndürme işlemlerinde yeni bir kayıt eklenir,
--    eski kayıt is_active=0 yapılır. Eski kayıtlar silinmez.
--
--    NOT: key_hash sadece DOĞRULAMA içindir. Şifre çözme ham anahtar + key_id ile yapılır.
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE credential_keys (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    key_name        VARCHAR(100)        NOT NULL,
    key_hash        CHAR(64)            NOT NULL,   -- SHA-256(ham_anahtar)
    key_purpose     VARCHAR(50)         NOT NULL,
    -- key_purpose: credential_encryption | jwt_signing | session_encryption | api_signing
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,
    activated_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deactivated_at  DATETIME                NULL,
    created_by      INT UNSIGNED            NULL,   -- → coremusic_auth.admins.id
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    UNIQUE  KEY uq_ck_name      (key_name),
    UNIQUE  KEY uq_ck_hash      (key_hash),
    INDEX idx_ck_purpose        (key_purpose),
    INDEX idx_ck_active         (is_active),

    CHECK (key_purpose IN ('credential_encryption','jwt_signing','session_encryption','api_signing'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- CREDENTIAL_AUDIT — Kredansiyel erişim denetim kaydı (APPEND-ONLY)
-- Normal Form : BCNF
-- Bağımlılık  : id → {credential_id, action, accessed_by, ip_address, ...}
--
-- ⚠️ APPEND-ONLY — Bu tabloda ASLA UPDATE veya DELETE yapılmaz.
--    1 yıl saklama, sonrası arşiv.
--
-- action: read | write | rotate | revoke | create
-- status: allowed | denied
-- denied_reason: expired_key | inactive_key | wrong_version | permission_denied
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE credential_audit (
    id                  BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    credential_id       INT UNSIGNED        NOT NULL,
    credential_key_id   INT UNSIGNED            NULL,
    user_id             INT UNSIGNED            NULL,
    action              VARCHAR(20)         NOT NULL,
    status              VARCHAR(20)         NOT NULL DEFAULT 'allowed',
    denied_reason       VARCHAR(100)            NULL,
    accessed_by         VARCHAR(100)        NOT NULL,   -- Servis adı / kullanıcı
    ip_address          VARCHAR(45)             NULL,
    user_agent          VARCHAR(512)            NULL,
    request_id          VARCHAR(64)             NULL,   -- İstek trace ID'si
    occurred_at         DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    FOREIGN KEY fk_cau_vault (credential_id) REFERENCES credential_vault (id) ON DELETE CASCADE,
    -- ⚠️ credential_key_id → credential_keys.id: FK eklenebilir, NULL kabul eder

    INDEX idx_cau_cred       (credential_id),
    INDEX idx_cau_key        (credential_key_id),
    INDEX idx_cau_user       (user_id),
    INDEX idx_cau_action     (action),
    INDEX idx_cau_status     (status),
    INDEX idx_cau_occurred   (occurred_at DESC),
    INDEX idx_cau_request    (request_id),

    CHECK (action IN ('read','write','rotate','revoke','create')),
    CHECK (status  IN ('allowed','denied'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ══════════════════════════════════════════════════════════════════════════════
-- VARSAYILAN VERİLER
-- ══════════════════════════════════════════════════════════════════════════════

-- Varsayılan şifreleme anahtarı kaydı (application boot sırasında eklenecek)
INSERT INTO credential_keys (key_name, key_hash, key_purpose, is_active) VALUES
    ('credential-encryption-v1', '', 'credential_encryption', 1);
-- ⚠️ key_hash, uygulama başlatılırken doldurulur (environment variable'dan okunur).

-- ══════════════════════════════════════════════════════════════════════════════
-- End of coremusic_credential schema
