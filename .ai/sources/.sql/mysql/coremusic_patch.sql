-- ══════════════════════════════════════════════════════════════════════════════
-- coremusic_patch — SCHEMA VERSIONING & MIGRATION TRACKING
-- COREMUSIC DB v2.0 | Ağustos 2026 | MySQL 8.x InnoDB | utf8mb4_unicode_ci
-- NF: BCNF
-- ══════════════════════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS coremusic_patch
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE coremusic_patch;

-- schema_versions: Her DB'nin versiyon takibi
CREATE TABLE schema_versions (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    db_name         VARCHAR(50)         NOT NULL,  -- coremusic_auth, coremusic_user, vb.
    version         VARCHAR(20)         NOT NULL,  -- 1.0.0, 2.0.0
    description     TEXT                    NULL,
    sql_file        VARCHAR(255)            NULL,  -- uygulanan SQL dosyası
    applied_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    applied_by      VARCHAR(100)            NULL,  -- kim uyguladı
    execution_ms    INT UNSIGNED            NULL,  -- ne kadar sürdü
    status          VARCHAR(20)         NOT NULL DEFAULT 'applied',

    PRIMARY KEY (id),
    UNIQUE  KEY uq_sv_db_version (db_name, version),
    INDEX idx_sv_db (db_name),
    INDEX idx_sv_status (status),
    INDEX idx_sv_applied (applied_at DESC),

    CHECK (status IN ('applied', 'rolled_back', 'pending'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- migration_log: Migration geçmişi
CREATE TABLE migration_log (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    migration_name  VARCHAR(255)        NOT NULL,
    direction       VARCHAR(10)         NOT NULL,  -- up | down
    db_name         VARCHAR(50)         NOT NULL,
    started_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at    DATETIME                NULL,
    status          VARCHAR(20)         NOT NULL DEFAULT 'running',
    error_message   TEXT                    NULL,
    tables_affected INT UNSIGNED            NULL,
    rows_affected   BIGINT UNSIGNED         NULL,

    PRIMARY KEY (id),
    INDEX idx_ml_db (db_name),
    INDEX idx_ml_status (status),
    INDEX idx_ml_started (started_at DESC),

    CHECK (direction IN ('up', 'down')),
    CHECK (status IN ('running', 'completed', 'failed', 'rolled_back'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- patches: Patch tracking
CREATE TABLE patches (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    patch_name      VARCHAR(255)        NOT NULL,
    description     TEXT                    NULL,
    target_db       VARCHAR(50)         NOT NULL,
    patch_type      VARCHAR(20)         NOT NULL DEFAULT 'schema',
    sql_content     LONGTEXT            NOT NULL,
    version_from    VARCHAR(20)             NULL,
    version_to      VARCHAR(20)             NULL,
    status          VARCHAR(20)         NOT NULL DEFAULT 'pending',
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    applied_at      DATETIME                NULL,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_patch_name (patch_name),
    INDEX idx_p_target (target_db),
    INDEX idx_p_status (status),
    INDEX idx_p_type (patch_type),

    CHECK (patch_type IN ('schema', 'data', 'fix', 'security')),
    CHECK (status IN ('pending', 'applied', 'skipped', 'failed'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ══════════════════════════════════════════════════════════════════════════════
-- End of coremusic_patch schema
