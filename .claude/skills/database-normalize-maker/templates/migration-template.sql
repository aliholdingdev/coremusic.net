-- =========================================================================
-- AI Agentic Orchestrator - Safe Migration Template
-- 
-- Güvenli göç (Safe Migration) prensipleri:
-- 1. Yabancı anahtarlar silinmeden önce pasife alınır.
-- 2. Kritik verilerin kazara silinmesini (Data Loss) önlemek için
--    DROP öncesi yedek (veya arşiv) kontrolleri devreye alınmalıdır.
-- =========================================================================

-- -------------------------------------------------------------------------
-- UP MIGRATION (Uygulama)
-- -------------------------------------------------------------------------
-- ADR: {UP_MIGRATION_ADR}

-- (Örnek: Tablo ekleme veya kolon genişletme)
-- CREATE TABLE ...
-- ALTER TABLE ... ADD COLUMN ...

-- {UP_SCRIPT}


-- -------------------------------------------------------------------------
-- DOWN MIGRATION (Geri Alma)
-- -------------------------------------------------------------------------
-- ⚠️ DİKKAT: Üretim (Production) ortamında veri kaybına yol açabileceği 
-- için DOWN migration'ları DROP yerine kolonları pasif konuma (RENAME/DEPRECATE) 
-- getirecek şekilde ayarlanmalıdır, ancak geliştirme için DROP desteklenir.

-- {DOWN_SCRIPT}

-- (Örnek:)
-- SET FOREIGN_KEY_CHECKS = 0;
-- DROP TABLE IF EXISTS {TABLE_NAME}_audit;
-- DROP TABLE IF EXISTS {TABLE_NAME};
-- SET FOREIGN_KEY_CHECKS = 1;
