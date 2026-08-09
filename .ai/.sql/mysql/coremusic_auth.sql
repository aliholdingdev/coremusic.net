-- coremusic_auth — KIMLIK DOGRULAMA & GUVENLIK VERITABANI
-- Version: 8.0.0
-- BCNF Normalized
-- Author: CoreMusic Data Engineer
-- Date: 2026-08-09
-- charset: utf8mb4_unicode_ci, engine: InnoDB
-- UUID: BINARY(16) — generated in PHP app layer (UUID v7)
-- Soft Delete: is_deleted + deleted_at
-- Timestamps: created_at, updated_at, deleted_at

CREATE DATABASE IF NOT EXISTS coremusic_auth
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE coremusic_auth;

-- =============================================
-- Table: users
-- Purpose: Core user accounts for authentication
-- BCNF: All non-key attributes fully depend on the candidate key
-- =============================================
CREATE TABLE users (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 primary key',
    username VARCHAR(50) NOT NULL COMMENT 'Unique login username',
    email VARCHAR(255) NOT NULL COMMENT 'User email address (unique)',
    password_hash VARCHAR(255) NOT NULL COMMENT 'Argon2id hashed password',
    display_name VARCHAR(100) NULL COMMENT 'User display name',
    gender ENUM('male','female','non-binary','prefer_not_to_say') DEFAULT 'prefer_not_to_say' COMMENT 'User gender for theme engine',
    avatar_url VARCHAR(500) NULL COMMENT 'Profile avatar URL',
    account_type ENUM('free','premium','studio','admin') DEFAULT 'free' COMMENT 'Account tier',
    is_active TINYINT(1) DEFAULT 1 COMMENT 'Account active flag',
    is_banned TINYINT(1) DEFAULT 0 COMMENT 'Banned status flag',
    is_verified TINYINT(1) DEFAULT 0 COMMENT 'Email verified flag',
    failed_login_attempts INT DEFAULT 0 COMMENT 'Consecutive failed login count',
    last_login_at TIMESTAMP NULL COMMENT 'Last successful login timestamp',
    last_login_ip VARCHAR(45) NULL COMMENT 'Last login IP (IPv4/IPv6)',
    password_changed_at TIMESTAMP NULL COMMENT 'Last password change timestamp',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
    is_deleted TINYINT(1) DEFAULT 0 COMMENT 'Soft delete flag',
    deleted_at TIMESTAMP NULL COMMENT 'Soft delete timestamp',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Core user accounts — authentication, profiles, state';

-- Indexes
CREATE UNIQUE INDEX idx_users_username ON users (username) COMMENT 'Unique username lookup';
CREATE UNIQUE INDEX idx_users_email ON users (email) COMMENT 'Unique email lookup';
CREATE INDEX idx_users_is_active ON users (is_active) COMMENT 'Active user filtering';
CREATE INDEX idx_users_account_type ON users (account_type) COMMENT 'Account tier filtering';
CREATE INDEX idx_users_is_banned ON users (is_banned) COMMENT 'Banned user filtering';

-- =============================================
-- Table: user_roles
-- Purpose: RBAC role definitions with permission sets
-- BCNF: role_name is candidate key; permissions fully depend on role
-- =============================================
CREATE TABLE user_roles (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 primary key',
    role_name VARCHAR(50) NOT NULL COMMENT 'Unique role identifier',
    role_description TEXT NULL COMMENT 'Human-readable role description',
    permissions JSON NULL COMMENT 'JSON array of permission strings',
    is_system TINYINT(1) DEFAULT 0 COMMENT 'System role flag (non-deletable)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
    is_deleted TINYINT(1) DEFAULT 0 COMMENT 'Soft delete flag',
    deleted_at TIMESTAMP NULL COMMENT 'Soft delete timestamp',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='RBAC role definitions — permissions, system roles';

-- Indexes
CREATE UNIQUE INDEX idx_user_roles_name ON user_roles (role_name) COMMENT 'Unique role name lookup';

-- =============================================
-- Table: user_assigned_roles
-- Purpose: Many-to-many user-role assignments
-- BCNF: (user_id, role_id) is candidate key; assigned_by fully depends on it
-- =============================================
CREATE TABLE user_assigned_roles (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 primary key',
    user_id BINARY(16) NOT NULL COMMENT 'FK → users.id',
    role_id BINARY(16) NOT NULL COMMENT 'FK → user_roles.id',
    assigned_by BINARY(16) NULL COMMENT 'FK → users.id — who assigned this role',
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Assignment timestamp',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
    is_deleted TINYINT(1) DEFAULT 0 COMMENT 'Soft delete flag',
    deleted_at TIMESTAMP NULL COMMENT 'Soft delete timestamp',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='User-role assignment junction — RBAC bindings';

-- Indexes
CREATE INDEX idx_assigned_roles_user ON user_assigned_roles (user_id) COMMENT 'User role lookup';
CREATE INDEX idx_assigned_roles_role ON user_assigned_roles (role_id) COMMENT 'Role member lookup';
CREATE UNIQUE INDEX idx_assigned_roles_unique ON user_assigned_roles (user_id, role_id) COMMENT 'Prevent duplicate assignments';

-- Foreign Keys
ALTER TABLE user_assigned_roles
    ADD CONSTRAINT fk_assigned_roles_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_assigned_roles_role FOREIGN KEY (role_id) REFERENCES user_roles (id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_assigned_roles_by FOREIGN KEY (assigned_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE;

-- =============================================
-- Table: user_sessions
-- Purpose: Active session tracking for authentication
-- BCNF: session_token_hash is candidate key; all attributes depend on it
-- =============================================
CREATE TABLE user_sessions (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 primary key',
    user_id BINARY(16) NOT NULL COMMENT 'FK → users.id',
    session_token_hash VARCHAR(255) NOT NULL COMMENT 'SHA-256 hash of session token',
    ip_address VARCHAR(45) NULL COMMENT 'Client IP (IPv4/IPv6)',
    user_agent TEXT NULL COMMENT 'Browser/client user agent string',
    device_fingerprint VARCHAR(255) NULL COMMENT 'Device fingerprint hash',
    is_active TINYINT(1) DEFAULT 1 COMMENT 'Session active flag',
    last_activity_at TIMESTAMP NULL COMMENT 'Last activity timestamp',
    expires_at TIMESTAMP NOT NULL COMMENT 'Session expiration timestamp',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
    is_deleted TINYINT(1) DEFAULT 0 COMMENT 'Soft delete flag',
    deleted_at TIMESTAMP NULL COMMENT 'Soft delete timestamp',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='User sessions — authentication, tracking, expiry';

-- Indexes
CREATE UNIQUE INDEX idx_sessions_token ON user_sessions (session_token_hash) COMMENT 'Unique token hash lookup';
CREATE INDEX idx_sessions_user ON user_sessions (user_id) COMMENT 'User session lookup';
CREATE INDEX idx_sessions_active ON user_sessions (is_active) COMMENT 'Active session filtering';
CREATE INDEX idx_sessions_expires ON user_sessions (expires_at) COMMENT 'Expiry cleanup index';

-- Foreign Keys
ALTER TABLE user_sessions
    ADD CONSTRAINT fk_sessions_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- Table: user_tokens
-- Purpose: Password reset, email verify, API tokens
-- BCNF: (user_id, token_type, token_hash) — token_hash uniquely identifies token
-- =============================================
CREATE TABLE user_tokens (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 primary key',
    user_id BINARY(16) NOT NULL COMMENT 'FK → users.id',
    token_type ENUM('password_reset','email_verify','api_key','refresh','access') NOT NULL COMMENT 'Token purpose',
    token_hash VARCHAR(255) NOT NULL COMMENT 'SHA-256 hash of token value',
    token_name VARCHAR(100) NULL COMMENT 'Human-readable token label',
    scope JSON NULL COMMENT 'JSON array of allowed scopes',
    expires_at TIMESTAMP NOT NULL COMMENT 'Token expiration timestamp',
    used_at TIMESTAMP NULL COMMENT 'First use timestamp (single-use tokens)',
    ip_address VARCHAR(45) NULL COMMENT 'Requesting IP address',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
    is_deleted TINYINT(1) DEFAULT 0 COMMENT 'Soft delete flag',
    deleted_at TIMESTAMP NULL COMMENT 'Soft delete timestamp',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='User tokens — reset, verify, API, refresh, access';

-- Indexes
CREATE INDEX idx_tokens_user_type ON user_tokens (user_id, token_type) COMMENT 'User token type lookup';
CREATE INDEX idx_tokens_hash ON user_tokens (token_hash) COMMENT 'Token hash lookup';
CREATE INDEX idx_tokens_expires ON user_tokens (expires_at) COMMENT 'Expiry cleanup index';

-- Foreign Keys
ALTER TABLE user_tokens
    ADD CONSTRAINT fk_tokens_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- Table: credential_vault
-- Purpose: Encrypted credential storage (AES-256-GCM)
-- BCNF: (service_name, credential_name) is candidate key
-- =============================================
CREATE TABLE credential_vault (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 primary key',
    service_name VARCHAR(100) NOT NULL COMMENT 'Target service identifier',
    credential_type ENUM('api_key','secret','token','certificate','password') NOT NULL COMMENT 'Credential type',
    credential_name VARCHAR(100) NOT NULL COMMENT 'Credential identifier within service',
    encrypted_value TEXT NOT NULL COMMENT 'AES-256-GCM encrypted credential value',
    encryption_algorithm VARCHAR(50) DEFAULT 'AES-256-GCM' COMMENT 'Encryption algorithm used',
    encryption_iv BINARY(12) NULL COMMENT 'AES-GCM initialization vector',
    encryption_tag BINARY(16) NULL COMMENT 'AES-GCM authentication tag',
    environment ENUM('production','staging','development') DEFAULT 'production' COMMENT 'Target environment',
    is_active TINYINT(1) DEFAULT 1 COMMENT 'Credential active flag',
    last_rotated_at TIMESTAMP NULL COMMENT 'Last key rotation timestamp',
    expires_at TIMESTAMP NULL COMMENT 'Credential expiration timestamp',
    rotation_interval_days INT DEFAULT 90 COMMENT 'Auto-rotation interval in days',
    metadata JSON NULL COMMENT 'Additional metadata (JSON)',
    created_by BINARY(16) NULL COMMENT 'FK → users.id — creator',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
    is_deleted TINYINT(1) DEFAULT 0 COMMENT 'Soft delete flag',
    deleted_at TIMESTAMP NULL COMMENT 'Soft delete timestamp',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Credential vault — AES-256-GCM encrypted secrets';

-- Indexes
CREATE INDEX idx_credential_service ON credential_vault (service_name) COMMENT 'Service name lookup';
CREATE INDEX idx_credential_type ON credential_vault (credential_type) COMMENT 'Credential type filtering';
CREATE INDEX idx_credential_active ON credential_vault (is_active) COMMENT 'Active credential filtering';
CREATE UNIQUE INDEX idx_credential_unique ON credential_vault (service_name, credential_name) COMMENT 'Unique credential per service';

-- Foreign Keys
ALTER TABLE credential_vault
    ADD CONSTRAINT fk_credential_creator FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE;

-- =============================================
-- Table: credential_keys
-- Purpose: Encryption key version management
-- BCNF: key_name is candidate key; all attributes fully depend on it
-- Security: key_hash = SHA-256(ham_anahtar). Raw key NEVER stored in DB.
-- =============================================
CREATE TABLE credential_keys (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    key_name        VARCHAR(100)        NOT NULL,
    key_hash        CHAR(64)            NOT NULL,   -- SHA-256(ham_anahtar)
    key_purpose     VARCHAR(50)         NOT NULL,   -- credential_encryption | jwt_signing | session_encryption | api_signing
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,
    activated_at    DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deactivated_at  DATETIME                NULL,
    created_by      INT UNSIGNED            NULL,   -- cross-DB FK → coremusic_auth.admin_users.id
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    UNIQUE  KEY uq_ck_name      (key_name),
    UNIQUE  KEY uq_ck_hash      (key_hash),
    INDEX idx_ck_purpose        (key_purpose),
    INDEX idx_ck_active         (is_active),

    CHECK (key_purpose IN ('credential_encryption','jwt_signing','session_encryption','api_signing'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Encryption key version management — hash-only, no raw keys';

-- Varsayılan şifreleme anahtarı kaydı (application boot sırasında eklenecek)
INSERT INTO credential_keys (key_name, key_hash, key_purpose, is_active) VALUES
    ('credential-encryption-v1', '', 'credential_encryption', 1);
-- ⚠️ key_hash, uygulama başlatılırken doldurulur (environment variable'dan okunur).

-- =============================================
-- Table: credential_audit
-- Purpose: Credential access audit trail (APPEND-ONLY)
-- BCNF: (credential_id, action, created_at) — immutable log
-- APPEND-ONLY: ASLA UPDATE veya DELETE yapılmaz. 1 yıl saklama, sonrası arşiv.
-- =============================================
CREATE TABLE credential_audit (
    id                  BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    credential_id       BINARY(16)          NOT NULL,   -- FK → credential_vault.id
    credential_key_id   INT UNSIGNED            NULL,   -- FK → credential_keys.id
    user_id             BINARY(16)              NULL,   -- FK → users.id (cross-DB comment)
    action              VARCHAR(20)         NOT NULL,   -- read | write | rotate | revoke | create
    status              VARCHAR(20)         NOT NULL DEFAULT 'allowed',   -- allowed | denied
    denied_reason       VARCHAR(100)            NULL,   -- expired_key | inactive_key | wrong_version | permission_denied
    accessed_by         VARCHAR(100)        NOT NULL,   -- Servis adı / kullanıcı
    ip_address          VARCHAR(45)             NULL,
    user_agent          VARCHAR(512)            NULL,
    request_id          VARCHAR(64)             NULL,   -- İstek trace ID'si
    occurred_at         DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    INDEX idx_cau_cred       (credential_id),
    INDEX idx_cau_key        (credential_key_id),
    INDEX idx_cau_user       (user_id),
    INDEX idx_cau_action     (action),
    INDEX idx_cau_status     (status),
    INDEX idx_cau_occurred   (occurred_at DESC),
    INDEX idx_cau_request    (request_id),

    CHECK (action IN ('read','write','rotate','revoke','create')),
    CHECK (status  IN ('allowed','denied'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Credential audit trail — append-only access log';

-- Foreign Keys for credential_audit
ALTER TABLE credential_audit
    ADD CONSTRAINT fk_cau_vault FOREIGN KEY (credential_id) REFERENCES credential_vault (id) ON DELETE CASCADE;

-- =============================================
-- Table: api_keys
-- Purpose: API key management for external integrations
-- BCNF: key_hash is candidate key; all attributes depend on it
-- =============================================
CREATE TABLE api_keys (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 primary key',
    user_id BINARY(16) NOT NULL COMMENT 'FK → users.id',
    key_hash VARCHAR(255) NOT NULL COMMENT 'SHA-256 hash of API key',
    key_prefix VARCHAR(8) NOT NULL COMMENT 'Visible key prefix for identification',
    key_name VARCHAR(100) NULL COMMENT 'Human-readable key label',
    scopes JSON NULL COMMENT 'JSON array of allowed API scopes',
    rate_limit INT DEFAULT 1000 COMMENT 'Requests per hour limit',
    is_active TINYINT(1) DEFAULT 1 COMMENT 'Key active flag',
    last_used_at TIMESTAMP NULL COMMENT 'Last usage timestamp',
    expires_at TIMESTAMP NULL COMMENT 'Key expiration timestamp',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
    is_deleted TINYINT(1) DEFAULT 0 COMMENT 'Soft delete flag',
    deleted_at TIMESTAMP NULL COMMENT 'Soft delete timestamp',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='API keys — external integration authentication';

-- Indexes
CREATE UNIQUE INDEX idx_apikeys_hash ON api_keys (key_hash) COMMENT 'Unique key hash lookup';
CREATE INDEX idx_apikeys_user ON api_keys (user_id) COMMENT 'User API keys lookup';
CREATE INDEX idx_apikeys_active ON api_keys (is_active) COMMENT 'Active key filtering';
CREATE INDEX idx_apikeys_prefix ON api_keys (key_prefix) COMMENT 'Key prefix identification';

-- Foreign Keys
ALTER TABLE api_keys
    ADD CONSTRAINT fk_apikeys_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- Table: api_usage
-- Purpose: API request logging and analytics
-- BCNF: (api_key_id, created_at) — immutable log entry
-- =============================================
CREATE TABLE api_usage (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 primary key',
    api_key_id BINARY(16) NOT NULL COMMENT 'FK → api_keys.id',
    endpoint VARCHAR(500) NOT NULL COMMENT 'Requested API endpoint',
    method ENUM('GET','POST','PUT','DELETE','PATCH') NOT NULL COMMENT 'HTTP method',
    status_code INT NULL COMMENT 'HTTP response status code',
    response_time_ms INT NULL COMMENT 'Response time in milliseconds',
    ip_address VARCHAR(45) NULL COMMENT 'Client IP address',
    user_agent TEXT NULL COMMENT 'Client user agent',
    request_body_hash VARCHAR(64) NULL COMMENT 'SHA-256 hash of request body',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Request timestamp',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='API usage log — requests, responses, analytics';

-- Indexes
CREATE INDEX idx_apiusage_key ON api_usage (api_key_id) COMMENT 'API key usage lookup';
CREATE INDEX idx_apiusage_created ON api_usage (created_at) COMMENT 'Time-based usage queries';
CREATE INDEX idx_apiusage_endpoint ON api_usage (endpoint) COMMENT 'Endpoint filtering';
CREATE INDEX idx_apiusage_method ON api_usage (method) COMMENT 'Method filtering';

-- Foreign Keys
ALTER TABLE api_usage
    ADD CONSTRAINT fk_apiusage_key FOREIGN KEY (api_key_id) REFERENCES api_keys (id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- Table: permission_audit
-- Purpose: Permission grant/deny/revoke audit trail
-- BCNF: (user_id, permission, action, created_at) — immutable log
-- =============================================
CREATE TABLE permission_audit (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 primary key',
    user_id BINARY(16) NOT NULL COMMENT 'FK → users.id — affected user',
    permission VARCHAR(100) NOT NULL COMMENT 'Permission string',
    action ENUM('grant','deny','revoke','check') NOT NULL COMMENT 'Permission action',
    resource_type VARCHAR(50) NULL COMMENT 'Target resource type',
    resource_id BINARY(16) NULL COMMENT 'Target resource ID',
    reason TEXT NULL COMMENT 'Reason for action',
    performed_by BINARY(16) NULL COMMENT 'FK → users.id — actor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Audit event timestamp',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Permission audit trail — grants, denials, revocations';

-- Indexes
CREATE INDEX idx_perm_audit_user ON permission_audit (user_id) COMMENT 'User permission audit lookup';
CREATE INDEX idx_perm_audit_permission ON permission_audit (permission) COMMENT 'Permission filtering';
CREATE INDEX idx_perm_audit_action ON permission_audit (action) COMMENT 'Action type filtering';
CREATE INDEX idx_perm_audit_created ON permission_audit (created_at) COMMENT 'Time-based audit queries';

-- Foreign Keys
ALTER TABLE permission_audit
    ADD CONSTRAINT fk_perm_audit_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT fk_perm_audit_by FOREIGN KEY (performed_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE;

-- =============================================
-- Table: admin_users
-- Purpose: Admin panel access and privilege levels
-- BCNF: user_id is candidate key; admin attributes fully depend on it
-- =============================================
CREATE TABLE admin_users (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 primary key',
    user_id BINARY(16) NOT NULL COMMENT 'FK → users.id (unique)',
    admin_level ENUM('super_admin','admin','moderator','support') NOT NULL COMMENT 'Admin privilege level',
    department VARCHAR(100) NULL COMMENT 'Admin department',
    can_manage_users TINYINT(1) DEFAULT 0 COMMENT 'User management permission',
    can_manage_content TINYINT(1) DEFAULT 0 COMMENT 'Content management permission',
    can_manage_system TINYINT(1) DEFAULT 0 COMMENT 'System management permission',
    can_view_analytics TINYINT(1) DEFAULT 0 COMMENT 'Analytics access permission',
    last_admin_action_at TIMESTAMP NULL COMMENT 'Last admin action timestamp',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
    is_deleted TINYINT(1) DEFAULT 0 COMMENT 'Soft delete flag',
    deleted_at TIMESTAMP NULL COMMENT 'Soft delete timestamp',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Admin users — privilege levels, permissions';

-- Indexes
CREATE UNIQUE INDEX idx_admin_user ON admin_users (user_id) COMMENT 'Unique admin user lookup';
CREATE INDEX idx_admin_level ON admin_users (admin_level) COMMENT 'Admin level filtering';

-- Foreign Keys
ALTER TABLE admin_users
    ADD CONSTRAINT fk_admin_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- Table: admin_activity_log
-- Purpose: Admin action audit trail
-- BCNF: (admin_user_id, action, target_type, created_at) — immutable log
-- =============================================
CREATE TABLE admin_activity_log (
    id BINARY(16) NOT NULL COMMENT 'UUID v7 primary key',
    admin_user_id BINARY(16) NOT NULL COMMENT 'FK → admin_users.id',
    action VARCHAR(100) NOT NULL COMMENT 'Admin action description',
    target_type VARCHAR(50) NULL COMMENT 'Target entity type',
    target_id BINARY(16) NULL COMMENT 'Target entity ID',
    old_value JSON NULL COMMENT 'Previous state (JSON)',
    new_value JSON NULL COMMENT 'New state (JSON)',
    ip_address VARCHAR(45) NULL COMMENT 'Admin IP address',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Audit event timestamp',
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Admin activity log — actions, changes, audit trail';

-- Indexes
CREATE INDEX idx_admin_log_user ON admin_activity_log (admin_user_id) COMMENT 'Admin activity lookup';
CREATE INDEX idx_admin_log_action ON admin_activity_log (action) COMMENT 'Action type filtering';
CREATE INDEX idx_admin_log_target ON admin_activity_log (target_type) COMMENT 'Target type filtering';
CREATE INDEX idx_admin_log_created ON admin_activity_log (created_at) COMMENT 'Time-based audit queries';

-- Foreign Keys
ALTER TABLE admin_activity_log
    ADD CONSTRAINT fk_admin_log_user FOREIGN KEY (admin_user_id) REFERENCES admin_users (id) ON DELETE CASCADE ON UPDATE CASCADE;

-- =============================================
-- coremusic_auth Database v8.0.0
-- Tables: 13 (12 core + 1 credential_keys)
-- BCNF Compliant: Yes
-- UUID: BINARY(16) for core tables, INT UNSIGNED for credential_keys
-- Soft Delete: is_deleted + deleted_at
-- =============================================
-- End of coremusic_auth schema
