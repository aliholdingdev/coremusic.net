-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL -> .ai/.sql/mysql SSOT)
-- Kaynak : canli MySQL · mysqldump (birebir canli sema) · Tarih: 2026-10-22
-- Semasi : TAM (tum tablolar/views/routines) · BCNF & UUID v7 Normalizasyon
-- VERI   : YOK — kullanici/davranisman/credential verisi bilincli yazilmaz (KVKK/REDACTED)
-- Yeniden kurulum: .ai/.sql/mysql/rebuild-all.ps1
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_ai`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_ai`;

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
DROP TABLE IF EXISTS `audio_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audio_features` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `music_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_musics.musics.id (UUID v7)',
  `bpm` smallint unsigned DEFAULT NULL,
  `key_signature` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mode` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time_signature` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `energy` decimal(3,2) DEFAULT NULL,
  `danceability` decimal(3,2) DEFAULT NULL,
  `valence` decimal(3,2) DEFAULT NULL,
  `acousticness` decimal(3,2) DEFAULT NULL,
  `instrumentalness` decimal(3,2) DEFAULT NULL,
  `liveness` decimal(3,2) DEFAULT NULL,
  `speechiness` decimal(3,2) DEFAULT NULL,
  `loudness_db` decimal(6,2) DEFAULT NULL,
  `features` json DEFAULT NULL,
  `analyzed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_af_music` (`music_id`),
  KEY `idx_af_bpm` (`bpm`),
  KEY `idx_af_key` (`key_signature`),
  CONSTRAINT `fk_af_music` FOREIGN KEY (`music_id`) REFERENCES `coremusic_musics`.`musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `listening_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `listening_features` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_auth.users.id (UUID v7)',
  `music_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_musics.musics.id (UUID v7)',
  `features` json DEFAULT NULL,
  `skip_rate` decimal(3,2) DEFAULT NULL,
  `listen_ratio` decimal(3,2) DEFAULT NULL,
  `repeat_count` int unsigned NOT NULL DEFAULT '0',
  `last_played_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_lf_pair` (`user_id`,`music_id`),
  KEY `idx_lf_music` (`music_id`),
  CONSTRAINT `fk_lf_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_lf_music` FOREIGN KEY (`music_id`) REFERENCES `coremusic_musics`.`musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `model_versions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_versions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `model_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `accuracy_score` decimal(5,4) DEFAULT NULL,
  `f1_score` decimal(5,4) DEFAULT NULL,
  `training_data_size` bigint unsigned DEFAULT NULL,
  `training_duration_seconds` int unsigned DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'training',
  `deployed_at` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_mv_pair` (`model_name`,`version`),
  KEY `idx_mv_type` (`model_type`),
  KEY `idx_mv_status` (`status`),
  CONSTRAINT `model_versions_chk_1` CHECK ((`model_type` in (_utf8mb4'recommendation',_utf8mb4'audio_analysis',_utf8mb4'nlp',_utf8mb4'classification'))),
  CONSTRAINT `model_versions_chk_2` CHECK ((`status` in (_utf8mb4'training',_utf8mb4'active',_utf8mb4'deprecated',_utf8mb4'archived')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `recommendation_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recommendation_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_auth.users.id (UUID v7)',
  `music_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_musics.musics.id (UUID v7)',
  `algorithm` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_version` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `confidence` decimal(3,2) DEFAULT NULL,
  `feedback` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'none',
  `position` tinyint unsigned DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_rh_user` (`user_id`),
  KEY `idx_rh_music` (`music_id`),
  KEY `idx_rh_algorithm` (`algorithm`),
  KEY `idx_rh_feedback` (`feedback`),
  KEY `idx_rh_created` (`created_at` DESC),
  CONSTRAINT `fk_rh_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rh_music` FOREIGN KEY (`music_id`) REFERENCES `coremusic_musics`.`musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `recommendation_history_chk_1` CHECK ((`algorithm` in (_utf8mb4'collaborative',_utf8mb4'content_based',_utf8mb4'hybrid',_utf8mb4'ai_curated'))),
  CONSTRAINT `recommendation_history_chk_2` CHECK ((`feedback` in (_utf8mb4'none',_utf8mb4'liked',_utf8mb4'disliked',_utf8mb4'skipped',_utf8mb4'saved')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `training_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `training_jobs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `model_version_id` int unsigned NOT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'queued',
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `metrics` json DEFAULT NULL,
  `gpu_hours` decimal(8,2) DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tj_model` (`model_version_id`),
  KEY `idx_tj_status` (`status`),
  CONSTRAINT `fk_training_jobs_model_versions` FOREIGN KEY (`model_version_id`) REFERENCES `model_versions` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `training_jobs_chk_1` CHECK ((`status` in (_utf8mb4'queued',_utf8mb4'running',_utf8mb4'completed',_utf8mb4'failed',_utf8mb4'cancelled')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user_preference_profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_preference_profiles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_auth.users.id (UUID v7)',
  `genre_weights` json DEFAULT NULL,
  `mood_preferences` json DEFAULT NULL,
  `tempo_preference` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `energy_preference` decimal(3,2) DEFAULT NULL,
  `acoustic_preference` decimal(3,2) DEFAULT NULL,
  `diversity_score` decimal(3,2) DEFAULT NULL,
  `last_analyzed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_upp_user` (`user_id`),
  CONSTRAINT `fk_upp_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
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

SET FOREIGN_KEY_CHECKS = 1;
