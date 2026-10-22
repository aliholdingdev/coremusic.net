-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL -> .ai/.sql/mysql SSOT)
-- Kaynak : canli MySQL · mysqldump (birebir canli sema) · Tarih: 2026-10-22
-- Semasi : TAM (tum tablolar/views/routines) · Uretec: regen_dumps_20261022.ps1 (mysqldump 9.7) · SEED: eq_presets
-- VERI   : YOK — kullanici/davranisman/credential verisi bilincli yazilmaz (KVKK/REDACTED)
-- Yeniden kurulum: .ai/.sql/mysql/rebuild-all.ps1
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_neva`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_neva`;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `dsp_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `dsp_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `slot_order` tinyint unsigned NOT NULL,
  `effect_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `params_json` json NOT NULL,
  `is_bypassed` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dsp_slot` (`user_id`,`slot_order`),
  KEY `idx_dsp_type` (`effect_type`),
  KEY `idx_dsp_order` (`slot_order`),
  KEY `idx_dsp_bypass` (`is_bypassed`),
  CONSTRAINT `dsp_settings_chk_1` CHECK ((`slot_order` between 0 and 15)),
  CONSTRAINT `dsp_settings_chk_2` CHECK ((`effect_type` in (_utf8mb4'equalizer',_utf8mb4'compressor',_utf8mb4'gate',_utf8mb4'reverb',_utf8mb4'delay',_utf8mb4'chorus',_utf8mb4'spatial',_utf8mb4'limiter')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `eq_presets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `eq_presets` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `preset_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `eq_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'graphic',
  `band_count` tinyint unsigned NOT NULL DEFAULT '10',
  `bands_json` json NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` tinyint unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `eq_name_key` varchar(255) COLLATE utf8mb4_unicode_ci GENERATED ALWAYS AS (if((`deleted_at` is null),`preset_name`,NULL)) STORED,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_eqp_user_name` (`user_id`,`eq_name_key`),
  KEY `idx_eqp_type` (`eq_type`),
  KEY `idx_eqp_active` (`user_id`,`is_active`),
  KEY `idx_eqp_system` (`is_system`),
  KEY `idx_eqp_order` (`sort_order`),
  CONSTRAINT `eq_presets_chk_1` CHECK ((`eq_type` in (_utf8mb4'graphic',_utf8mb4'parametric',_utf8mb4'shelving',_utf8mb4'peak'))),
  CONSTRAINT `eq_presets_chk_2` CHECK ((`band_count` in (2,5,10,15,31)))
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `routing_matrix`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `routing_matrix` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `route_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `input_source` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `input_channel` tinyint unsigned NOT NULL DEFAULT '0',
  `output_destination` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `output_channel` tinyint unsigned NOT NULL DEFAULT '0',
  `gain_db` decimal(5,2) NOT NULL DEFAULT '0.00',
  `is_muted` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rm_user_name` (`user_id`,`route_name`),
  KEY `idx_rm_input` (`input_source`),
  KEY `idx_rm_output` (`output_destination`),
  KEY `idx_rm_active` (`is_active`),
  CONSTRAINT `routing_matrix_chk_1` CHECK ((`input_source` in (_utf8mb4'system',_utf8mb4'microphone',_utf8mb4'file',_utf8mb4'stream',_utf8mb4'plugin'))),
  CONSTRAINT `routing_matrix_chk_2` CHECK ((`output_destination` in (_utf8mb4'speakers',_utf8mb4'headphones',_utf8mb4'bluetooth',_utf8mb4'file',_utf8mb4'stream'))),
  CONSTRAINT `routing_matrix_chk_3` CHECK ((`gain_db` between -(60.00) and 24.00))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `spectrum_analysis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spectrum_analysis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `session_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `bin_count` smallint unsigned NOT NULL DEFAULT '256',
  `fft_size` int unsigned NOT NULL DEFAULT '4096',
  `sample_rate_hz` int unsigned NOT NULL DEFAULT '48000',
  `min_freq_hz` int unsigned NOT NULL DEFAULT '20',
  `max_freq_hz` int unsigned NOT NULL DEFAULT '24000',
  `spectrum_json` json NOT NULL,
  `rms_level_db` decimal(5,2) DEFAULT NULL,
  `peak_level_db` decimal(5,2) DEFAULT NULL,
  `sampled_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_sa_session` (`session_id`),
  KEY `idx_sa_user` (`user_id`),
  KEY `idx_sa_sampled` (`sampled_at` DESC),
  KEY `idx_sa_bins` (`bin_count`),
  CONSTRAINT `spectrum_analysis_chk_1` CHECK ((`bin_count` in (128,256,512,1024))),
  CONSTRAINT `spectrum_analysis_chk_2` CHECK ((`fft_size` in (1024,2048,4096,8192,16384))),
  CONSTRAINT `spectrum_analysis_chk_3` CHECK ((`sample_rate_hz` in (44100,48000,96000,192000))),
  CONSTRAINT `spectrum_analysis_chk_4` CHECK (((`rms_level_db` is null) or (`rms_level_db` between -(96.00) and 24.00))),
  CONSTRAINT `spectrum_analysis_chk_5` CHECK (((`peak_level_db` is null) or (`peak_level_db` between -(96.00) and 24.00)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- SEED (KVKK onayli whitelist — yalniz bu tablolar): eq_presets

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

LOCK TABLES `eq_presets` WRITE;
/*!40000 ALTER TABLE `eq_presets` DISABLE KEYS */;
INSERT INTO `eq_presets` (`id`, `user_id`, `preset_name`, `eq_type`, `band_count`, `bands_json`, `is_active`, `is_system`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES (1,0,'Flat (Varsayilan)','graphic',10,'[{\"hz\": 32, \"gain\": 0}, {\"hz\": 64, \"gain\": 0}, {\"hz\": 125, \"gain\": 0}, {\"hz\": 250, \"gain\": 0}, {\"hz\": 500, \"gain\": 0}, {\"hz\": 1000, \"gain\": 0}, {\"hz\": 2000, \"gain\": 0}, {\"hz\": 4000, \"gain\": 0}, {\"hz\": 8000, \"gain\": 0}, {\"hz\": 16000, \"gain\": 0}]',0,1,0,'2026-08-17 11:41:25','2026-08-17 11:41:25',NULL),(2,0,'Pop','graphic',10,'[{\"hz\": 32, \"gain\": 1}, {\"hz\": 64, \"gain\": 2}, {\"hz\": 125, \"gain\": 2}, {\"hz\": 250, \"gain\": 0}, {\"hz\": 500, \"gain\": -1}, {\"hz\": 1000, \"gain\": 0}, {\"hz\": 2000, \"gain\": 1}, {\"hz\": 4000, \"gain\": 2}, {\"hz\": 8000, \"gain\": 3}, {\"hz\": 16000, \"gain\": 3}]',0,1,1,'2026-08-17 11:41:25','2026-08-17 11:41:25',NULL),(3,0,'Rock','graphic',10,'[{\"hz\": 32, \"gain\": 4}, {\"hz\": 64, \"gain\": 3}, {\"hz\": 125, \"gain\": 2}, {\"hz\": 250, \"gain\": 1}, {\"hz\": 500, \"gain\": 0}, {\"hz\": 1000, \"gain\": -1}, {\"hz\": 2000, \"gain\": 0}, {\"hz\": 4000, \"gain\": 2}, {\"hz\": 8000, \"gain\": 3}, {\"hz\": 16000, \"gain\": 4}]',0,1,2,'2026-08-17 11:41:25','2026-08-17 11:41:25',NULL),(4,0,'Jazz','graphic',10,'[{\"hz\": 32, \"gain\": 2}, {\"hz\": 64, \"gain\": 2}, {\"hz\": 125, \"gain\": 1}, {\"hz\": 250, \"gain\": 1}, {\"hz\": 500, \"gain\": 0}, {\"hz\": 1000, \"gain\": 0}, {\"hz\": 2000, \"gain\": 1}, {\"hz\": 4000, \"gain\": 1}, {\"hz\": 8000, \"gain\": 2}, {\"hz\": 16000, \"gain\": 2}]',0,1,3,'2026-08-17 11:41:25','2026-08-17 11:41:25',NULL),(5,0,'Klasik','graphic',10,'[{\"hz\": 32, \"gain\": 3}, {\"hz\": 64, \"gain\": 2}, {\"hz\": 125, \"gain\": 1}, {\"hz\": 250, \"gain\": 0}, {\"hz\": 500, \"gain\": 0}, {\"hz\": 1000, \"gain\": 0}, {\"hz\": 2000, \"gain\": 0}, {\"hz\": 4000, \"gain\": 1}, {\"hz\": 8000, \"gain\": 2}, {\"hz\": 16000, \"gain\": 3}]',0,1,4,'2026-08-17 11:41:25','2026-08-17 11:41:25',NULL),(6,0,'Bas Arttir','graphic',10,'[{\"hz\": 32, \"gain\": 6}, {\"hz\": 64, \"gain\": 5}, {\"hz\": 125, \"gain\": 4}, {\"hz\": 250, \"gain\": 2}, {\"hz\": 500, \"gain\": 0}, {\"hz\": 1000, \"gain\": 0}, {\"hz\": 2000, \"gain\": 0}, {\"hz\": 4000, \"gain\": 0}, {\"hz\": 8000, \"gain\": 0}, {\"hz\": 16000, \"gain\": 0}]',0,1,5,'2026-08-17 11:41:25','2026-08-17 11:41:25',NULL),(7,0,'Vokal','graphic',10,'[{\"hz\": 32, \"gain\": -2}, {\"hz\": 64, \"gain\": -1}, {\"hz\": 125, \"gain\": 0}, {\"hz\": 250, \"gain\": 1}, {\"hz\": 500, \"gain\": 2}, {\"hz\": 1000, \"gain\": 3}, {\"hz\": 2000, \"gain\": 3}, {\"hz\": 4000, \"gain\": 2}, {\"hz\": 8000, \"gain\": 1}, {\"hz\": 16000, \"gain\": 0}]',0,1,6,'2026-08-17 11:41:25','2026-08-17 11:41:25',NULL);
/*!40000 ALTER TABLE `eq_presets` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


SET FOREIGN_KEY_CHECKS = 1;
