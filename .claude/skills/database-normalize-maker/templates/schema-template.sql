-- =========================================================================
-- CoreMusic - Schema Template
-- 
-- BCNF ve CoreMusic standartlarına uygun veritabanı şablonu.
-- 
-- Hedef Motor: MySQL 9
-- Standard: BIGINT UNSIGNED AUTO_INCREMENT PK (UUID yasak)
-- =========================================================================

-- ADR: {ADR_NOTES}

-- -------------------------------------------------------------------------
-- TABLE: {TABLE_NAME}
-- -------------------------------------------------------------------------
CREATE TABLE {TABLE_NAME} (
    -- Primary Key: CoreMusic standartı
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    
    -- {COLUMNS}
    
    -- Standart Zaman Damgaları (her tabloda zorunlu)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL
    
    -- Index'ler
    -- {INDEXES}
    
    -- Constraint'ler
    -- {CONSTRAINTS}
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------------------------------------------------------
-- AUDIT TRAIL: {TABLE_NAME}_audit
-- Security Engineer zorunluluğu: Hassas veriler için denetim kaydı.
-- -------------------------------------------------------------------------
CREATE TABLE {TABLE_NAME}_audit (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    {TABLE_NAME}_id BIGINT UNSIGNED NOT NULL,
    action ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    acted_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_{TABLE_NAME}_id ({TABLE_NAME}_id),
    INDEX idx_action (action),
    FOREIGN KEY ({TABLE_NAME}_id) REFERENCES {TABLE_NAME}(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
