-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL -> .ai/.sql/mysql SSOT)
-- Kaynak : canli MySQL · mysqldump (birebir canli sema) · Tarih: 2026-10-22
-- Semasi : TAM (tum tablolar/views/routines) · Uretec: regen_dumps_20261022.ps1 (mysqldump 9.7)
-- VERI   : YOK — kullanici/davranisman/credential verisi bilincli yazilmaz (KVKK/REDACTED)
-- Yeniden kurulum: .ai/.sql/mysql/rebuild-all.ps1
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_albums`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_albums`;

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
DROP TABLE IF EXISTS `album_credits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `album_credits` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz kredi tan─▒mlay─▒c─▒',
  `album_id` binary(16) NOT NULL COMMENT 'Alb├╝m ID',
  `credit_type` enum('producer','engineer','mixer','mastering','artwork','photography','other') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kredi t├╝r├╝',
  `person_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ki┼şi ad─▒ (CoreMusic d─▒┼ş─▒ ise)',
  `person_id` binary(16) DEFAULT NULL COMMENT 'Ki┼şi ID (CoreMusic ├╝yesi ise)',
  `credit_order` int NOT NULL DEFAULT '0' COMMENT 'Kredi s─▒ras─▒',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_album_credits_credit` (`album_id`,`credit_type`,`person_id`),
  KEY `idx_album_credits_album` (`album_id`) COMMENT 'Alb├╝m filtresi',
  KEY `idx_album_credits_type` (`credit_type`) COMMENT 'Kredi t├╝r├╝ filtresi',
  CONSTRAINT `fk_album_credits_album` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Alb├╝m kredileri tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `album_discs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `album_discs` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz disk tan─▒mlay─▒c─▒',
  `album_id` binary(16) NOT NULL COMMENT 'Alb├╝m ID',
  `disc_number` int NOT NULL COMMENT 'Disk numaras─▒',
  `disc_title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Disk ba┼şl─▒─ş─▒ (opsiyonel)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_album_discs_unique` (`album_id`,`disc_number`) COMMENT 'Benzersiz alb├╝m-disk ├ğifti',
  CONSTRAINT `fk_album_discs_album` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Alb├╝m diskleri tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `album_genres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `album_genres` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz ili┼şki tan─▒mlay─▒c─▒',
  `album_id` binary(16) NOT NULL COMMENT 'Alb├╝m ID',
  `genre_id` binary(16) NOT NULL COMMENT 'T├╝r ID',
  `is_primary` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Birincil t├╝r m├╝?',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_album_genres_unique` (`album_id`,`genre_id`) COMMENT 'Benzersiz alb├╝m-t├╝r ├ğifti',
  KEY `idx_album_genres_genre` (`genre_id`) COMMENT 'T├╝r filtresi',
  CONSTRAINT `fk_album_genres_album` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Alb├╝m-t├╝r ili┼şkisi tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `album_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `album_stats` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz istatistik tan─▒mlay─▒c─▒',
  `album_id` binary(16) NOT NULL COMMENT 'Alb├╝m ID',
  `daily_plays` int NOT NULL DEFAULT '0' COMMENT 'G├╝nl├╝k ├ğalma say─▒s─▒',
  `weekly_plays` int NOT NULL DEFAULT '0' COMMENT 'Haftal─▒k ├ğalma say─▒s─▒',
  `monthly_plays` int NOT NULL DEFAULT '0' COMMENT 'Ayl─▒k ├ğalma say─▒s─▒',
  `total_plays` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam ├ğalma say─▒s─▒',
  `daily_downloads` int NOT NULL DEFAULT '0' COMMENT 'G├╝nl├╝k indirme say─▒s─▒',
  `total_downloads` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam indirme say─▒s─▒',
  `popularity_score` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Pop├╝lerlik puan─▒',
  `stats_date` date NOT NULL COMMENT '─░statistik tarihi',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'G├╝ncellenme zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_album_stats_unique` (`album_id`,`stats_date`) COMMENT 'Benzersiz alb├╝m-tarih ├ğifti',
  KEY `idx_album_stats_popularity` (`popularity_score`) COMMENT 'Pop├╝lerlik s─▒ralamas─▒',
  CONSTRAINT `fk_album_stats_album` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Alb├╝m istatistikleri tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `albums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `albums` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz alb├╝m tan─▒mlay─▒c─▒',
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Alb├╝m ba┼şl─▒─ş─▒',
  `slug` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL dostu ba┼şl─▒k',
  `artist_id` binary(16) NOT NULL COMMENT 'Sanat├ğ─▒ ID',
  `album_type` enum('album','ep','single','compilation','live','remix','soundtrack') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'album' COMMENT 'Alb├╝m t├╝r├╝',
  `genre_id` binary(16) DEFAULT NULL COMMENT 'Birincil t├╝r ID',
  `release_date` date DEFAULT NULL COMMENT 'Yay─▒n tarihi',
  `record_label` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Plak ┼şirketi',
  `upc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Evrensel ├£r├╝n Kodu',
  `cover_art_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kapak g├Ârseli URL',
  `total_discs` int NOT NULL DEFAULT '1' COMMENT 'Toplam disk say─▒s─▒',
  `total_tracks` int NOT NULL DEFAULT '0' COMMENT 'Toplam par├ğa say─▒s─▒',
  `total_duration_sec` int DEFAULT NULL COMMENT 'Toplam s├╝re (saniye)',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Do─şrulanm─▒┼ş alb├╝m m├╝?',
  `play_count` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam ├ğalma say─▒s─▒',
  `like_count` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam be─şeni say─▒s─▒',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olu┼şturulma zaman─▒',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'G├╝ncellenme zaman─▒',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumu┼şak silme bayra─ş─▒',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_albums_slug` (`slug`),
  KEY `idx_albums_artist` (`artist_id`) COMMENT 'Sanat├ğ─▒ filtresi',
  KEY `idx_albums_type` (`album_type`) COMMENT 'Alb├╝m t├╝r├╝ filtresi',
  KEY `idx_albums_release` (`release_date`) COMMENT 'Yay─▒n tarihi s─▒ralamas─▒',
  FULLTEXT KEY `idx_albums_fulltext` (`title`) COMMENT 'Tam metin aramas─▒'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Alb├╝mler tablosu';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `labels` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Etiket adi (albums.record_label buraya tasinacak)',
  `country_code` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ISO 3166-1 alpha-2',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_labels_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Plak sirketleri lookup (3NF ÔÇö karar #5/#10)';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `media` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `release_id` binary(16) NOT NULL COMMENT 'FK -> releases.id',
  `position` int unsigned NOT NULL DEFAULT '1' COMMENT 'Disk pozisyonu (1 = ilk disk)',
  `format` enum('cd','vinyl','cassette','digital','bluray','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Baski formati (karma ENUM ÔÇö karar #5)',
  `title` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Disk basligi (opsiyonel)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_media_rel_pos` (`release_id`,`position`),
  CONSTRAINT `fk_media_release` FOREIGN KEY (`release_id`) REFERENCES `releases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Medium ÔÇö release icindeki disk (MusicBrainz hizali ÔÇö karar #10)';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `release_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `release_groups` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Grup adi',
  `type_id` binary(16) DEFAULT NULL COMMENT 'FK -> coremusic_catalog.catalog_album_types.id (karar #5: domain lookup)',
  `legacy_album_id` binary(16) DEFAULT NULL COMMENT 'TA┼ŞIMA ─░Z─░: albums.id (backfill FAZ 2b; FK YOK)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_rg_type` (`type_id`),
  KEY `idx_rg_legacy` (`legacy_album_id`),
  KEY `idx_rg_title` (`title`(100)),
  CONSTRAINT `fk_rg_type` FOREIGN KEY (`type_id`) REFERENCES `coremusic_catalog`.`catalog_album_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Release Group ÔÇö soyut album/varlik grubu (MusicBrainz hizali ÔÇö karar #10)';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `release_labels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `release_labels` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `release_id` binary(16) NOT NULL COMMENT 'FK -> releases.id',
  `label_id` binary(16) NOT NULL COMMENT 'FK -> labels.id',
  `catalog_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Etikete ozel katalog no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_rel_label` (`release_id`,`label_id`),
  KEY `idx_rl_label` (`label_id`),
  CONSTRAINT `fk_rl_label` FOREIGN KEY (`label_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rl_release` FOREIGN KEY (`release_id`) REFERENCES `releases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Release-Label N:M junction (MusicBrainz hizali ÔÇö karar #10)';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `releases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `releases` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `release_group_id` binary(16) NOT NULL COMMENT 'FK -> release_groups.id',
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Baski adi',
  `catalog_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Katalog numarasi',
  `release_date` date DEFAULT NULL COMMENT 'Yayin tarihi (opsiyonel)',
  `total_media` int unsigned DEFAULT NULL COMMENT 'Bildirilen toplam disk sayisi (albums.total_disctasinmasi dogrulama)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_rel_group` (`release_group_id`),
  KEY `idx_rel_catno` (`catalog_number`),
  KEY `idx_rel_date` (`release_date`),
  CONSTRAINT `fk_rel_group` FOREIGN KEY (`release_group_id`) REFERENCES `release_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Release ÔÇö baski/urun (MusicBrainz hizali ÔÇö karar #10)';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tracks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tracks` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `medium_id` binary(16) NOT NULL COMMENT 'FK -> media.id',
  `recording_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_musics.recordings.id (cross-DB, karar #4)',
  `position` int unsigned NOT NULL DEFAULT '1' COMMENT 'Pozisyonda sira',
  `title_override` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bu baskidaki ad (recording adindan farkliysa)',
  `artist_credit_id` binary(16) DEFAULT NULL COMMENT 'FK -> coremusic_musics.artist_credit.id (opsiyonel override; cross-DB, karar #4)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_track_medium_pos` (`medium_id`,`position`),
  KEY `idx_track_recording` (`recording_id`),
  KEY `idx_track_credit` (`artist_credit_id`),
  CONSTRAINT `fk_track_credit` FOREIGN KEY (`artist_credit_id`) REFERENCES `coremusic_musics`.`artist_credit` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_track_medium` FOREIGN KEY (`medium_id`) REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_track_recording` FOREIGN KEY (`recording_id`) REFERENCES `coremusic_musics`.`recordings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Track ÔÇö recording''in medium icindeki tekrari (MusicBrainz hizali ÔÇö karar #10)';
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
