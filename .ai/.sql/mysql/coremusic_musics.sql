-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL -> .ai/.sql/mysql SSOT)
-- Kaynak : canli MySQL · mysqldump (birebir canli sema) · Tarih: 2026-10-22
-- Semasi : TAM (tum tablolar/views/routines) · Uretec: regen_dumps_20261022.ps1 (mysqldump 9.7)
-- VERI   : YOK — kullanici/davranisman/credential verisi bilincli yazilmaz (KVKK/REDACTED)
-- Yeniden kurulum: .ai/.sql/mysql/rebuild-all.ps1
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_musics`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_musics`;

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
DROP TABLE IF EXISTS `artist_credit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `artist_credit` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `display_name` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Gorunur birlesik ad ("Queen & David Bowie")',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_artist_credit_display` (`display_name`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Artist credit basligi (MusicBrainz hizali ÔÇö karar #10)';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `artist_credit_names`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `artist_credit_names` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `artist_credit_id` binary(16) NOT NULL COMMENT 'FK -> artist_credit.id',
  `artist_id` binary(16) NOT NULL COMMENT 'FK -> artists.id',
  `position` int unsigned NOT NULL DEFAULT '1' COMMENT 'Sira',
  `join_phrase` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Birlestirme metni (" & ", ", ")',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_acn_credit_pos` (`artist_credit_id`,`position`),
  KEY `idx_acn_artist` (`artist_id`),
  CONSTRAINT `fk_acn_artist` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_acn_credit` FOREIGN KEY (`artist_credit_id`) REFERENCES `artist_credit` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Artist credit bilesenleri (N:M + join_phrase ÔÇö karar #10)';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `artist_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `artist_members` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz ├╝ye tan─▒mlay─▒c─▒',
  `artist_id` binary(16) NOT NULL COMMENT 'Sanat├ğ─▒/grup ID',
  `user_id` binary(16) DEFAULT NULL COMMENT 'Kullan─▒c─▒ ID (CoreMusic ├╝yesi ise)',
  `role` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member' COMMENT 'Grup i├ğindeki rol',
  `joined_at` date DEFAULT NULL COMMENT 'Kat─▒l─▒m tarihi',
  `left_at` date DEFAULT NULL COMMENT 'Ayr─▒lma tarihi',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Aktif ├╝ye mi?',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'G├╝ncellenme zaman─▒',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumu┼şak silme bayra─ş─▒',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zaman─▒',
  PRIMARY KEY (`id`),
  KEY `idx_artist_members_artist` (`artist_id`) COMMENT 'Sanat├ğ─▒ filtresi',
  KEY `idx_artist_members_user` (`user_id`) COMMENT 'Kullan─▒c─▒ filtresi',
  KEY `idx_artist_members_active` (`is_active`) COMMENT 'Aktif ├╝ye filtresi',
  CONSTRAINT `fk_artist_members_artist` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sanat├ğ─▒ ├╝ye tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `artists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `artists` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz sanat├ğ─▒ tan─▒mlay─▒c─▒',
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Sanat├ğ─▒ ad─▒',
  `slug` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL dostu sanat├ğ─▒ ad─▒',
  `biography` text COLLATE utf8mb4_unicode_ci COMMENT 'Sanat├ğ─▒ biyografisi',
  `country` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '├£lke kodu (ISO 3166-1 alpha-2)',
  `formed_year` int DEFAULT NULL COMMENT 'Kurulu┼ş/y─▒l',
  `artist_type` enum('solo','band','duo','orchestra','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'solo' COMMENT 'Sanat├ğ─▒ t├╝r├╝',
  `image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Sanat├ğ─▒ foto─şraf─▒ URL',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Do─şrulanm─▒┼ş sanat├ğ─▒ m─▒?',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Aktif sanat├ğ─▒ m─▒?',
  `monthly_listeners` int NOT NULL DEFAULT '0' COMMENT 'Ayl─▒k dinleyici say─▒s─▒',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'G├╝ncellenme zaman─▒',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumu┼şak silme bayra─ş─▒',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_artists_slug` (`slug`) COMMENT 'URL benzersizlik',
  KEY `idx_artists_name` (`name`) COMMENT '─░sim aramas─▒',
  KEY `idx_artists_country` (`country`) COMMENT '├£lke filtresi',
  KEY `idx_artists_type` (`artist_type`) COMMENT 'Sanat├ğ─▒ t├╝r├╝ filtresi',
  FULLTEXT KEY `idx_artists_fulltext` (`name`,`biography`) COMMENT 'Tam metin aramas─▒'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sanat├ğ─▒lar tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `genres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `genres` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz t├╝r tan─▒mlay─▒c─▒',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'T├╝r ad─▒',
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL dostu t├╝r ad─▒',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'T├╝r a├ğ─▒klamas─▒',
  `parent_id` binary(16) DEFAULT NULL COMMENT '├£st t├╝r ID (hiyerar┼şik)',
  `genre_level` int NOT NULL DEFAULT '0' COMMENT 'Hiyerar┼şi seviyesi (0: k├Âk)',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Aktif t├╝r m├╝?',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'G├╝ncellenme zaman─▒',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumu┼şak silme bayra─ş─▒',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_genres_slug` (`slug`) COMMENT 'URL benzersizlik',
  KEY `idx_genres_parent` (`parent_id`) COMMENT '├£st t├╝r ili┼şkisi',
  KEY `idx_genres_level` (`genre_level`) COMMENT 'Hiyerar┼şi seviyesi',
  CONSTRAINT `fk_genres_parent` FOREIGN KEY (`parent_id`) REFERENCES `genres` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='M├╝zik t├╝rleri tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `music_artists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `music_artists` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 (PHP katmaninda uretilir)',
  `music_id` binary(16) NOT NULL COMMENT 'FK -> musics.id (recording adayi)',
  `artist_id` binary(16) NOT NULL COMMENT 'FK -> artists.id',
  `artist_role_id` binary(16) DEFAULT NULL COMMENT 'FK -> coremusic_catalog.catalog_artist_roles.id (opsiyonel rol)',
  `position` int unsigned NOT NULL DEFAULT '1' COMMENT 'Sira (1 = birincil sanatci)',
  `is_primary` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Birincil sanatci bayragi',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme (UTC)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete (UTC)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_music_artists_pair` (`music_id`,`artist_id`),
  UNIQUE KEY `uk_music_artists_pos` (`music_id`,`position`),
  KEY `idx_music_artists_artist` (`artist_id`),
  KEY `idx_music_artists_role` (`artist_role_id`),
  CONSTRAINT `fk_music_artists_artist` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_music_artists_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_music_artists_role` FOREIGN KEY (`artist_role_id`) REFERENCES `coremusic_catalog`.`catalog_artist_roles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT=' Sarki-artc N:M junction + rol + pozisyon (4NF ÔÇö karar #3)';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `music_audio_features`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `music_audio_features` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz ├Âzellik tan─▒mlay─▒c─▒',
  `music_id` binary(16) NOT NULL COMMENT 'M├╝zik ID',
  `danceability` decimal(4,3) DEFAULT NULL COMMENT 'Dans edilebilirlik (0.000-1.000)',
  `energy` decimal(4,3) DEFAULT NULL COMMENT 'Enerji seviyesi (0.000-1.000)',
  `valence` decimal(4,3) DEFAULT NULL COMMENT 'Pozitiflik (0.000-1.000)',
  `acousticness` decimal(4,3) DEFAULT NULL COMMENT 'Akustiklik (0.000-1.000)',
  `instrumentalness` decimal(4,3) DEFAULT NULL COMMENT 'Enstr├╝mantall─▒k (0.000-1.000)',
  `liveness` decimal(4,3) DEFAULT NULL COMMENT 'Canl─▒l─▒k (0.000-1.000)',
  `speechiness` decimal(4,3) DEFAULT NULL COMMENT 'Konu┼şma benzerli─şi (0.000-1.000)',
  `loudness` decimal(6,3) DEFAULT NULL COMMENT 'Ses ┼şiddeti (dB)',
  `tempo` decimal(6,2) DEFAULT NULL COMMENT 'Tempo (BPM)',
  `key_confidence` decimal(4,3) DEFAULT NULL COMMENT 'Anahtar g├╝venilirli─şi (0.000-1.000)',
  `mode_confidence` decimal(4,3) DEFAULT NULL COMMENT 'Mod g├╝venilirli─şi (0.000-1.000)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'G├╝ncellenme zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_music_audio_features_music` (`music_id`) COMMENT 'Benzersiz m├╝zik ili┼şkisi',
  CONSTRAINT `fk_music_audio_features_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='M├╝zik ses ├Âzellikleri tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `music_credits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `music_credits` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz kredi tan─▒mlay─▒c─▒',
  `music_id` binary(16) NOT NULL COMMENT 'M├╝zik ID',
  `credit_type` enum('composer','lyricist','producer','engineer','mixer','mastering','feature','other') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kredi t├╝r├╝',
  `person_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ki┼şi ad─▒ (CoreMusic d─▒┼ş─▒ ise)',
  `person_id` binary(16) DEFAULT NULL COMMENT 'Ki┼şi ID (CoreMusic ├╝yesi ise)',
  `credit_order` int NOT NULL DEFAULT '0' COMMENT 'Kredi s─▒ras─▒',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  PRIMARY KEY (`id`),
  KEY `idx_music_credits_music` (`music_id`) COMMENT 'M├╝zik filtresi',
  KEY `idx_music_credits_type` (`credit_type`) COMMENT 'Kredi t├╝r├╝ filtresi',
  KEY `idx_music_credits_person` (`person_id`) COMMENT 'Ki┼şi filtresi',
  CONSTRAINT `fk_music_credits_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='M├╝zik kredileri tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `music_files`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `music_files` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz dosya tan─▒mlay─▒c─▒',
  `music_id` binary(16) NOT NULL COMMENT 'M├╝zik ID',
  `file_format` enum('mp3','flac','wav','aac','ogg','dsd','mqa') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Dosya format─▒',
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Dosya yolu',
  `file_size` bigint NOT NULL COMMENT 'Dosya boyutu (byte)',
  `bit_rate` int DEFAULT NULL COMMENT 'Bit h─▒z─▒ (kbps)',
  `sample_rate` int DEFAULT NULL COMMENT '├ûrnekleme h─▒z─▒ (Hz)',
  `bit_depth` int DEFAULT NULL COMMENT 'Bit derinli─şi',
  `channels` int NOT NULL DEFAULT '2' COMMENT 'Kanal say─▒s─▒',
  `codec` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codec bilgisi',
  `checksum_sha256` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SHA256 checksum',
  `is_primary` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Birincil dosya m─▒?',
  `quality_level` enum('low_128','medium_192','high_320','lossless','hi_res') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'high_320' COMMENT 'Kalite seviyesi',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'G├╝ncellenme zaman─▒',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumu┼şak silme bayra─ş─▒',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zaman─▒',
  PRIMARY KEY (`id`),
  KEY `idx_music_files_music` (`music_id`) COMMENT 'M├╝zik ili┼şkisi',
  KEY `idx_music_files_format` (`file_format`) COMMENT 'Format filtresi',
  KEY `idx_music_files_quality` (`quality_level`) COMMENT 'Kalite filtresi',
  KEY `idx_music_files_primary` (`is_primary`) COMMENT 'Birincil dosya filtresi',
  KEY `idx_mf_music_primary` (`music_id`,`is_primary`,`is_deleted`),
  CONSTRAINT `fk_music_files_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='M├╝zik dosyalar─▒ tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `music_genres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `music_genres` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz ili┼şki tan─▒mlay─▒c─▒',
  `music_id` binary(16) NOT NULL COMMENT 'M├╝zik ID',
  `genre_id` binary(16) NOT NULL COMMENT 'T├╝r ID',
  `is_primary` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Birincil t├╝r m├╝?',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_music_genres_unique` (`music_id`,`genre_id`) COMMENT 'Benzersiz m├╝zik-t├╝r ├ğifti',
  KEY `idx_music_genres_genre` (`genre_id`) COMMENT 'T├╝r filtresi',
  CONSTRAINT `fk_music_genres_genre` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_music_genres_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='M├╝zik-t├╝r ili┼şkisi tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `music_lyrics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `music_lyrics` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz s├Âz tan─▒mlay─▒c─▒',
  `music_id` binary(16) NOT NULL COMMENT 'M├╝zik ID',
  `lyrics_text` longtext COLLATE utf8mb4_unicode_ci COMMENT '┼Şark─▒ s├Âzleri metni',
  `lyrics_language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en' COMMENT 'Dil kodu (ISO 639-1)',
  `lyrics_source` enum('manual','genius','musixmatch','ai_generated','verified') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual' COMMENT 'S├Âz kayna─ş─▒',
  `synced_lyrics` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Senkronize s├Âz var m─▒?',
  `synced_lyrics_data` json DEFAULT NULL COMMENT 'Senkronize s├Âz verisi (LRC format─▒)',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Do─şrulanm─▒┼ş s├Âzler mi?',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'G├╝ncellenme zaman─▒',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumu┼şak silme bayra─ş─▒',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_music_lyrics_music` (`music_id`) COMMENT 'Benzersiz m├╝zik ili┼şkisi',
  KEY `idx_music_lyrics_lang` (`lyrics_language`) COMMENT 'Dil filtresi',
  FULLTEXT KEY `idx_music_lyrics_fulltext` (`lyrics_text`) COMMENT 'S├Âz aramas─▒',
  CONSTRAINT `fk_music_lyrics_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='┼Şark─▒ s├Âzleri tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `music_similar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `music_similar` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz benzerlik tan─▒mlay─▒c─▒',
  `music_id` binary(16) NOT NULL COMMENT 'M├╝zik ID',
  `similar_music_id` binary(16) NOT NULL COMMENT 'Benzer m├╝zik ID',
  `similarity_score` decimal(3,2) NOT NULL COMMENT 'Benzerlik puan─▒ (0.00-1.00)',
  `algorithm` enum('collaborative','content_based','hybrid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'hybrid' COMMENT 'Benzerlik algoritmas─▒',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_music_similar_unique` (`music_id`,`similar_music_id`) COMMENT 'Benzersiz m├╝zik ├ğifti',
  KEY `idx_music_similar_target` (`similar_music_id`) COMMENT 'Hedef m├╝zik filtresi',
  KEY `idx_music_similar_score` (`similarity_score`) COMMENT 'Benzerlik puan─▒ s─▒ralamas─▒',
  CONSTRAINT `fk_music_similar_source` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_music_similar_target` FOREIGN KEY (`similar_music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Benzer m├╝zik tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `music_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `music_stats` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz istatistik tan─▒mlay─▒c─▒',
  `music_id` binary(16) NOT NULL COMMENT 'M├╝zik ID',
  `daily_plays` int NOT NULL DEFAULT '0' COMMENT 'G├╝nl├╝k ├ğalma say─▒s─▒',
  `weekly_plays` int NOT NULL DEFAULT '0' COMMENT 'Haftal─▒k ├ğalma say─▒s─▒',
  `monthly_plays` int NOT NULL DEFAULT '0' COMMENT 'Ayl─▒k ├ğalma say─▒s─▒',
  `total_plays` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam ├ğalma say─▒s─▒',
  `daily_downloads` int NOT NULL DEFAULT '0' COMMENT 'G├╝nl├╝k indirme say─▒s─▒',
  `weekly_downloads` int NOT NULL DEFAULT '0' COMMENT 'Haftal─▒k indirme say─▒s─▒',
  `monthly_downloads` int NOT NULL DEFAULT '0' COMMENT 'Ayl─▒k indirme say─▒s─▒',
  `total_downloads` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam indirme say─▒s─▒',
  `daily_shares` int NOT NULL DEFAULT '0' COMMENT 'G├╝nl├╝k payla┼ş─▒m say─▒s─▒',
  `weekly_shares` int NOT NULL DEFAULT '0' COMMENT 'Haftal─▒k payla┼ş─▒m say─▒s─▒',
  `monthly_shares` int NOT NULL DEFAULT '0' COMMENT 'Ayl─▒k payla┼ş─▒m say─▒s─▒',
  `total_shares` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam payla┼ş─▒m say─▒s─▒',
  `popularity_score` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Pop├╝lerlik puan─▒',
  `trending_score` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Trend puan─▒',
  `last_played_at` timestamp NULL DEFAULT NULL COMMENT 'Son ├ğal─▒nma zaman─▒',
  `stats_date` date NOT NULL COMMENT '─░statistik tarihi',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'G├╝ncellenme zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_music_stats_unique` (`music_id`,`stats_date`) COMMENT 'Benzersiz m├╝zik-tarih ├ğifti',
  KEY `idx_music_stats_popularity` (`popularity_score`) COMMENT 'Pop├╝lerlik s─▒ralamas─▒',
  KEY `idx_music_stats_trending` (`trending_score`) COMMENT 'Trend s─▒ralamas─▒',
  CONSTRAINT `fk_music_stats_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='M├╝zik istatistikleri tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `music_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `music_tags` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz etiket tan─▒mlay─▒c─▒',
  `music_id` binary(16) NOT NULL COMMENT 'M├╝zik ID',
  `tag` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Etiket ad─▒',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_music_tags_unique` (`music_id`,`tag`) COMMENT 'Benzersiz m├╝zik-etiket ├ğifti',
  KEY `idx_music_tags_music` (`music_id`) COMMENT 'M├╝zik filtresi',
  KEY `idx_music_tags_tag` (`tag`) COMMENT 'Etiket aramas─▒',
  CONSTRAINT `fk_music_tags_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='M├╝zik etiketleri tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `music_videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `music_videos` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `music_id` binary(16) NOT NULL,
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `video_url` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail_url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_format` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mp4',
  `duration_seconds` int unsigned DEFAULT NULL,
  `resolution` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `width_px` int unsigned DEFAULT NULL,
  `height_px` int unsigned DEFAULT NULL,
  `video_size_bytes` bigint unsigned DEFAULT NULL,
  `view_count` int unsigned NOT NULL DEFAULT '0',
  `like_count` int unsigned NOT NULL DEFAULT '0',
  `is_official` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_mv_music` (`music_id`),
  KEY `idx_mv_resolution` (`resolution`),
  KEY `idx_mv_official` (`is_official`),
  KEY `idx_mv_views` (`view_count` DESC),
  CONSTRAINT `fk_mv_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `music_videos_chk_1` CHECK ((`video_format` in (_utf8mb4'mp4',_utf8mb4'webm',_utf8mb4'mkv',_utf8mb4'avi'))),
  CONSTRAINT `music_videos_chk_2` CHECK ((`resolution` in (_utf8mb4'480p',_utf8mb4'720p',_utf8mb4'1080p',_utf8mb4'1440p',_utf8mb4'2160p',_utf8mb4'4320p')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='M├╝zik videolar─▒ ÔÇö music video metadata';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `musics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `musics` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz m├╝zik tan─▒mlay─▒c─▒',
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '┼Şark─▒ ba┼şl─▒─ş─▒',
  `slug` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL dostu ba┼şl─▒k',
  `artist_id` binary(16) NOT NULL COMMENT 'Sanat├ğ─▒ ID',
  `album_id` binary(16) DEFAULT NULL COMMENT 'Alb├╝m ID (opsiyonel)',
  `genre_id` binary(16) DEFAULT NULL COMMENT 'Birincil t├╝r ID',
  `track_number` int DEFAULT NULL COMMENT 'Alb├╝m i├ğindeki s─▒ra numaras─▒',
  `disc_number` int NOT NULL DEFAULT '1' COMMENT 'Disk numaras─▒',
  `duration_sec` int DEFAULT NULL COMMENT 'S├╝re (saniye)',
  `release_date` date DEFAULT NULL COMMENT 'Yay─▒n tarihi',
  `isrc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Uluslararas─▒ Standart Kay─▒t Kodu',
  `upc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Evrensel ├£r├╝n Kodu',
  `bpm` int DEFAULT NULL COMMENT 'Tempo (darbe/dakika)',
  `key_signature` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'M├╝zik anahtar─▒',
  `mood` enum('happy','sad','energetic','calm','dark','uplifting','neutral') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'neutral' COMMENT 'Ruh hali',
  `is_explicit` tinyint(1) NOT NULL DEFAULT '0' COMMENT '─░├ğerik uyar─▒s─▒',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Do─şrulanm─▒┼ş m├╝zik',
  `is_instrumental` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Enstr├╝mantal par├ğa m─▒?',
  `play_count` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam ├ğalma say─▒s─▒',
  `like_count` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam be─şeni say─▒s─▒',
  `download_count` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam indirme say─▒s─▒',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'G├╝ncellenme zaman─▒',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumu┼şak silme bayra─ş─▒',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zaman─▒',
  PRIMARY KEY (`id`),
  KEY `idx_musics_artist` (`artist_id`) COMMENT 'Sanat├ğ─▒ filtresi',
  KEY `idx_musics_album` (`album_id`) COMMENT 'Alb├╝m filtresi',
  KEY `idx_musics_genre` (`genre_id`) COMMENT 'T├╝r filtresi',
  KEY `idx_musics_release` (`release_date`) COMMENT 'Yay─▒n tarihi s─▒ralamas─▒',
  KEY `idx_musics_mood` (`mood`) COMMENT 'Ruh hali filtresi',
  KEY `idx_musics_active_created` (`is_deleted`,`created_at` DESC),
  KEY `idx_musics_artist_created` (`artist_id`,`created_at` DESC),
  FULLTEXT KEY `idx_musics_fulltext` (`title`) COMMENT 'Tam metin aramas─▒',
  CONSTRAINT `fk_musics_artist` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_musics_genre` FOREIGN KEY (`genre_id`) REFERENCES `genres` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='M├╝zik par├ğalar─▒ tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `podcast_episodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `podcast_episodes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `show_id` int unsigned NOT NULL,
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `audio_url` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `audio_format` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mp3',
  `audio_size_bytes` bigint unsigned DEFAULT NULL,
  `duration_seconds` int unsigned DEFAULT NULL,
  `episode_number` int unsigned DEFAULT NULL,
  `season_number` int unsigned DEFAULT NULL,
  `publish_date` datetime DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `play_count` int unsigned NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pe_slug` (`show_id`,`slug`),
  KEY `idx_pe_show` (`show_id`),
  KEY `idx_pe_publish` (`publish_date` DESC),
  KEY `idx_pe_status` (`status`),
  CONSTRAINT `fk_pe_show` FOREIGN KEY (`show_id`) REFERENCES `podcast_shows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `podcast_episodes_chk_1` CHECK ((`status` in (_utf8mb4'draft',_utf8mb4'published',_utf8mb4'scheduled',_utf8mb4'archived'))),
  CONSTRAINT `podcast_episodes_chk_2` CHECK ((`audio_format` in (_utf8mb4'mp3',_utf8mb4'aac',_utf8mb4'ogg',_utf8mb4'flac',_utf8mb4'wav')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Podcast b├Âl├╝mleri ÔÇö episodes, audio, scheduling';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `podcast_shows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `podcast_shows` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `author_user_id` int unsigned NOT NULL,
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `cover_image` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `author_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tr',
  `website_url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `episode_count` int unsigned NOT NULL DEFAULT '0',
  `subscriber_count` int unsigned NOT NULL DEFAULT '0',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ps_slug` (`slug`),
  KEY `idx_ps_author` (`author_user_id`),
  KEY `idx_ps_category` (`category`),
  KEY `idx_ps_language` (`language`),
  KEY `idx_ps_status` (`status`),
  CONSTRAINT `podcast_shows_chk_1` CHECK ((`status` in (_utf8mb4'draft',_utf8mb4'active',_utf8mb4'paused',_utf8mb4'archived')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Podcast g├Âsterileri ÔÇö series, episodes, subscriptions';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `podcast_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `podcast_subscriptions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `show_id` int unsigned NOT NULL,
  `notify_new_episode` tinyint(1) NOT NULL DEFAULT '1',
  `last_played_episode_id` int unsigned DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ps_pair` (`user_id`,`show_id`),
  KEY `idx_ps_sub_show` (`show_id`),
  CONSTRAINT `fk_psub_show` FOREIGN KEY (`show_id`) REFERENCES `podcast_shows` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Podcast abonelikleri ÔÇö user-show bindings';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `podcast_transcripts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `podcast_transcripts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `episode_id` int unsigned NOT NULL,
  `language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tr',
  `content` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `format` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'plain',
  `model_version` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `confidence` decimal(3,2) DEFAULT NULL,
  `generated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pt_pair` (`episode_id`,`language`),
  KEY `idx_pt_language` (`language`),
  CONSTRAINT `fk_pt_episode` FOREIGN KEY (`episode_id`) REFERENCES `podcast_episodes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Podcast transkripsiyonlar─▒ ÔÇö AI-generated transcripts';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `radio_now_playing`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `radio_now_playing` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `station_id` int unsigned NOT NULL,
  `music_id` binary(16) DEFAULT NULL COMMENT 'FK -> musics.id (snapshot kaynagi; NULL = bilinmiyor)',
  `track_title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `artist_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `album_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `genre` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration_seconds` int unsigned DEFAULT NULL,
  `started_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rnp_station` (`station_id`),
  KEY `idx_rnp_music` (`music_id`),
  CONSTRAINT `fk_rnp_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_rnp_station` FOREIGN KEY (`station_id`) REFERENCES `radio_stations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Radyo anl─▒k yay─▒n durumu ÔÇö now playing info';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `radio_schedules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `radio_schedules` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `station_id` int unsigned NOT NULL,
  `day_of_week` tinyint unsigned NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `program_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `host_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_recurring` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rs_schedule` (`station_id`,`day_of_week`,`start_time`),
  KEY `idx_rs_day` (`day_of_week`),
  KEY `idx_rs_time` (`start_time`),
  CONSTRAINT `fk_rsch_station` FOREIGN KEY (`station_id`) REFERENCES `radio_stations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `radio_schedules_chk_1` CHECK (((`day_of_week` >= 0) and (`day_of_week` <= 6)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Radyo yay─▒n programlar─▒ ÔÇö weekly schedules';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `radio_stations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `radio_stations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `genre` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `frequency_mhz` decimal(6,2) DEFAULT NULL,
  `stream_url` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stream_type` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'icecast',
  `website_url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_url` varchar(2048) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bitrate_kbps` int unsigned DEFAULT NULL,
  `listener_count` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rs_slug` (`slug`),
  KEY `idx_rs_genre` (`genre`),
  KEY `idx_rs_country` (`country`),
  KEY `idx_rs_active` (`is_active`),
  CONSTRAINT `radio_stations_chk_1` CHECK ((`stream_type` in (_utf8mb4'icecast',_utf8mb4'shoutcast',_utf8mb4'hls',_utf8mb4'direct')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Radyo istasyonlar─▒ ÔÇö stream URLs, genres, locations';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `recordings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recordings` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kayit adi',
  `duration_ms` int unsigned DEFAULT NULL COMMENT 'Sure (milisaniye) [EXTERNAL VERIFIED]',
  `isrc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ISO 3901 ISRC (musics.isrc tasinacak)',
  `artist_credit_id` binary(16) DEFAULT NULL COMMENT 'FK -> artist_credit.id',
  `work_id` binary(16) DEFAULT NULL COMMENT 'FK -> works.id',
  `legacy_music_id` binary(16) DEFAULT NULL COMMENT 'TA┼ŞIMA ─░Z─░: musics.id (backfill FAZ 2b; FK YOK ÔÇö iz korunur)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_rec_isrc` (`isrc`),
  KEY `idx_rec_legacy` (`legacy_music_id`),
  KEY `idx_rec_credit` (`artist_credit_id`),
  KEY `idx_rec_work` (`work_id`),
  CONSTRAINT `fk_rec_credit` FOREIGN KEY (`artist_credit_id`) REFERENCES `artist_credit` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_rec_work` FOREIGN KEY (`work_id`) REFERENCES `works` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Recording ÔÇö ozgun ses (MusicBrainz hizali ÔÇö karar #10)';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `v_music_recording_map`;
/*!50001 DROP VIEW IF EXISTS `v_music_recording_map`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_music_recording_map` AS SELECT 
 1 AS `music_id`,
 1 AS `recording_id`,
 1 AS `title`,
 1 AS `isrc`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `video_playback_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `video_playback_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `video_id` int unsigned NOT NULL,
  `last_position` int unsigned NOT NULL DEFAULT '0',
  `watch_count` int unsigned NOT NULL DEFAULT '1',
  `completion_pct` decimal(5,2) NOT NULL DEFAULT '0.00',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_vph_user` (`user_id`),
  KEY `idx_vph_video` (`video_id`),
  KEY `idx_vph_updated` (`updated_at` DESC),
  CONSTRAINT `fk_vph_video` FOREIGN KEY (`video_id`) REFERENCES `music_videos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Video oynatma ge├ğmi┼şi ÔÇö playback position, watch count';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `video_subtitles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `video_subtitles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `video_id` int unsigned NOT NULL,
  `language` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subtitle_url` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL,
  `format` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'srt',
  `is_auto_generated` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_vs_pair` (`video_id`,`language`),
  KEY `idx_vs_language` (`language`),
  CONSTRAINT `fk_vs_video` FOREIGN KEY (`video_id`) REFERENCES `music_videos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `video_subtitles_chk_1` CHECK ((`format` in (_utf8mb4'srt',_utf8mb4'vtt',_utf8mb4'ass')))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Video altyaz─▒lar─▒ ÔÇö subtitle tracks, multi-language';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `works`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `works` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Eser adi',
  `iswc` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ISWC (opsiyonel, [VERIFY REQUIRED] format kontrolu)',
  `artist_credit_id` binary(16) DEFAULT NULL COMMENT 'FK -> artist_credit.id (s├Âz/m├╝zik kredisi)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_works_iswc` (`iswc`),
  KEY `idx_works_title` (`title`(100)),
  KEY `fk_works_credit` (`artist_credit_id`),
  CONSTRAINT `fk_works_credit` FOREIGN KEY (`artist_credit_id`) REFERENCES `artist_credit` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Eser/Work varligi (MusicBrainz hizali ÔÇö karar #10)';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50001 DROP VIEW IF EXISTS `v_music_recording_map`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`Ali`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `v_music_recording_map` AS select `m`.`id` AS `music_id`,`r`.`id` AS `recording_id`,`m`.`title` AS `title`,`r`.`isrc` AS `isrc` from (`musics` `m` left join `recordings` `r` on((`r`.`legacy_music_id` = `m`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

SET FOREIGN_KEY_CHECKS = 1;
