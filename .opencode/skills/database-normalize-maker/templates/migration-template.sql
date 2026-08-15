-- =========================================================================
-- CoreMusic - Safe Migration Template
-- 
-- Güvenli göç prensipleri:
-- 1. Yabancı anahtarlar pasife alınır.
-- 2. Veri kaybını önlemek için yedek kontrolleri yapılır.
-- 3. Expand-contract pattern tehlikeli değişikliklerde kullanılır.
-- =========================================================================

-- -------------------------------------------------------------------------
-- UP MIGRATION (Uygulama)
-- -------------------------------------------------------------------------
-- ADR: {UP_MIGRATION_ADR}

-- (Örnek: Tablo ekleme)
-- CREATE TABLE {TABLE_NAME} (
--     id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
--     -- {COLUMNS}
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--     deleted_at TIMESTAMP NULL DEFAULT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- (Örnek: Kolon ekleme)
-- ALTER TABLE {TABLE_NAME} ADD COLUMN {COLUMN_NAME} {COLUMN_TYPE} {NULLABILITY} {DEFAULT};

-- (Örnek: Index ekleme)
-- CREATE INDEX idx_{TABLE_NAME}_{COLUMN} ON {TABLE_NAME}({COLUMN});

-- {UP_SCRIPT}


-- -------------------------------------------------------------------------
-- DOWN MIGRATION (Geri Alma)
-- -------------------------------------------------------------------------
-- ⚠️ DİKKAT: Üretim (Production) ortamında veri kaybına yol açabileceği 
-- için DOWN migration'ları DROP yerine kolonları pasif konuma (RENAME/DEPRECATE) 
-- getirecek şekilde ayarlanmalıdır, ancak geliştirme için DROP desteklenir.

-- (Örnek: Kolon silme)
-- ALTER TABLE {TABLE_NAME} DROP COLUMN {COLUMN_NAME};

-- (Örnek: Tablo silme)
-- SET FOREIGN_KEY_CHECKS = 0;
-- DROP TABLE IF EXISTS {TABLE_NAME}_audit;
-- DROP TABLE IF EXISTS {TABLE_NAME};
-- SET FOREIGN_KEY_CHECKS = 1;

-- {DOWN_SCRIPT}


-- -------------------------------------------------------------------------
-- EXPAND-CONTRACT PATTERN (Zero-Downtime)
-- -------------------------------------------------------------------------
-- Tehlikeli değişiklikler için 3 fazlı süreç:

-- FAZ 1: EXPAND (yeni yapı ekle, eski yapıyı koru)
-- ALTER TABLE {TABLE_NAME} ADD COLUMN {NEW_COLUMN} {TYPE} {DEFAULT};
-- CREATE TRIGGER trg_{TABLE_NAME}_sync_{COLUMN}
-- BEFORE UPDATE ON {TABLE_NAME}
-- FOR EACH ROW
-- BEGIN
--     IF NEW.{NEW_COLUMN} != OLD.{OLD_COLUMN} THEN
--         SET NEW.{NEW_COLUMN} = OLD.{OLD_COLUMN};
--     END IF;
-- END;

-- FAZ 2: MIGRATE (veriyi kopyala)
-- UPDATE {TABLE_NAME} SET {NEW_COLUMN} = {OLD_COLUMN} WHERE {NEW_COLUMN} IS NULL;

-- FAZ 3: CONTRACT (eski yapıyı kaldır) — onay sonrası
-- ALTER TABLE {TABLE_NAME} DROP COLUMN {OLD_COLUMN};
-- DROP TRIGGER IF EXISTS trg_{TABLE_NAME}_sync_{COLUMN};
