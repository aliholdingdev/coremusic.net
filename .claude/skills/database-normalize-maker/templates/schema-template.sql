-- =========================================================================
-- AI Agentic Orchestrator - Schema Template
-- 
-- Bu şablon "Zero-Hallucination" (Sıfır Halüsinasyon) politikası ile
-- üretilmiştir. BCNF, OWASP ve CoreMusic standartları uyarınca testlerden 
-- (Truth Mode) geçmiş veri tipleri ve kısıtlamalar içerir.
-- 
-- Hedef Motor: {DB_ENGINE} (Örn: MySQL 8+ veya PostgreSQL 15+)
-- =========================================================================

-- ADR: {ADR_NOTES_GATHERED_FROM_ORCHESTRATOR}

-- -------------------------------------------------------------------------
-- TABLE: {TABLE_NAME}
-- -------------------------------------------------------------------------
CREATE TABLE {TABLE_NAME} (
    -- Primary Key: Sektör standardı Surrogate Key
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    
    -- Public ID (Dış sistemler ve API'ler için)
    uuid CHAR(36) NOT NULL UNIQUE, 
    
    -- (Diğer kolonlar "Truth Mode" doğrulamasından geçerek buraya eklenir)
    -- {COLUMNS}
    
    -- Standart Zaman Damgaları
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL
) {ENGINE_OPTIONS};

-- İndekslemeler (Data Engineer ajanı tarafından kurgulanır)
-- CREATE INDEX idx_{TABLE_NAME}_uuid ON {TABLE_NAME}(uuid);
-- {INDEXES}

-- -------------------------------------------------------------------------
-- AUDIT TRAIL: {TABLE_NAME}_audit
-- Security Engineer zorunluluğu: Hassas veriler için denetim kaydı.
-- -------------------------------------------------------------------------
CREATE TABLE {TABLE_NAME}_audit (
    audit_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    {TABLE_NAME}_id BIGINT UNSIGNED NOT NULL,
    action ENUM('INSERT', 'UPDATE', 'DELETE') NOT NULL,
    changed_fields JSON,
    acted_by BIGINT UNSIGNED,
    action_timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_audit_{TABLE_NAME} FOREIGN KEY ({TABLE_NAME}_id) REFERENCES {TABLE_NAME}(id) ON DELETE CASCADE
) {ENGINE_OPTIONS};

CREATE INDEX idx_{TABLE_NAME}_audit_action ON {TABLE_NAME}_audit({TABLE_NAME}_id, action);
