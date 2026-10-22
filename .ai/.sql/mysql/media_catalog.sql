-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL -> .ai/.sql/mysql SSOT)
-- Kaynak : canli MySQL · mysqldump (birebir canli sema) · Tarih: 2026-10-22
-- Semasi : TAM (tum tablolar/views/routines) · Uretec: regen_dumps_20261022.ps1 (mysqldump 9.7) · SEED: taxonomy
-- VERI   : YOK — kullanici/davranisman/credential verisi bilincli yazilmaz (KVKK/REDACTED)
-- Yeniden kurulum: .ai/.sql/mysql/rebuild-all.ps1
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `media_catalog`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_tr_0900_ai_ci;

USE `media_catalog`;

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
DROP TABLE IF EXISTS `album`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `album` (
  `id` char(26) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'ULID ÔÇö $defs/album.id',
  `slug` varchar(80) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'kebab-case ÔÇö $defs/slug',
  `album` varchar(300) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'kimlik.album (maxLength 300)',
  `album_tip` enum('studyo','canli','derleme','ep','soundtrack','tekli') COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'kimlik.album_tip ÔÇö SYNC: taxonomy.json#album_tip',
  `sanatci_id` char(26) COLLATE utf8mb4_tr_0900_ai_ci DEFAULT NULL COMMENT 'ust.sanatci_id (ulid_null) ÔÇö NULL = tekli/├ğe┼şitli istisnas─▒',
  `yil` smallint DEFAULT NULL COMMENT 'tarih.yil (1900..2100)',
  `ay` tinyint DEFAULT NULL COMMENT 'tarih.ay (1..12)',
  `ulke` char(2) COLLATE utf8mb4_tr_0900_ai_ci DEFAULT NULL COMMENT 'tarih.ulke ISO-3166 alpha-2',
  `fiziksel` enum('lp','45lik','7lik','cd','kaset','dijital') COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'bicim.fiziksel ÔÇö SYNC: taxonomy.json#fiziksel_bicim',
  `sarki_sayisi` smallint unsigned DEFAULT NULL COMMENT 'kapsam.sarki_sayisi (min 0)',
  `sure_sn` int unsigned DEFAULT NULL COMMENT 'kapsam.sure_sn (saniye, min 1)',
  `cover` varchar(255) COLLATE utf8mb4_tr_0900_ai_ci DEFAULT NULL COMMENT 'gorsel.cover (maxLength 255)',
  `etiket_sirketi` varchar(200) COLLATE utf8mb4_tr_0900_ai_ci DEFAULT NULL COMMENT 'kredi.etiket_sirketi',
  `katalog_no` varchar(100) COLLATE utf8mb4_tr_0900_ai_ci DEFAULT NULL COMMENT 'kredi.katalog_no',
  `tag` json NOT NULL COMMENT 'tag {tur[], dil} ÔÇö $defs/album.tag',
  `durum` enum('inbox','aktif','sakli','tekrar','arsiv') COLLATE utf8mb4_tr_0900_ai_ci NOT NULL DEFAULT 'inbox' COMMENT 'SYNC: taxonomy.json#durum',
  `eklenme` datetime NOT NULL COMMENT 'eklenme (+03:00)',
  `guncelleme` datetime DEFAULT NULL COMMENT 'guncelleme (datetime03_null)',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'soft delete',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'silinme zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_album_slug` (`slug`),
  KEY `idx_album_sanatci_id` (`sanatci_id`),
  KEY `idx_album_album_tip` (`album_tip`),
  KEY `idx_album_yil` (`yil`),
  KEY `idx_album_durum` (`durum`),
  CONSTRAINT `fk_album_artist` FOREIGN KEY (`sanatci_id`) REFERENCES `artist` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci COMMENT='Alb├╝m ÔÇö media.schema.json $defs/album (FK sanatci_id ÔåÆ artist, cache s├╝tunu d├╝┼ş├╝ld├╝)';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `artist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `artist` (
  `id` char(26) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'ULID (Crockford base32, 26 karakter) ÔÇö $defs/ulid',
  `slug` varchar(80) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'kebab-case, $defs/slug (maxLength 80)',
  `sanatci` varchar(200) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'kimlik.sanatci (minLength 1, maxLength 200)',
  `tip` enum('kisi','grup','topluluk','cesitli') COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'kimlik.tip ÔÇö SYNC: taxonomy.json#sanatci_tip',
  `varsayilan_tag` json NOT NULL COMMENT 'varsayilan_tag {tur[], dil} ÔÇö $defs/artist.varsayilan_tag',
  `durum` enum('inbox','aktif','sakli','tekrar','arsiv') COLLATE utf8mb4_tr_0900_ai_ci NOT NULL DEFAULT 'inbox' COMMENT 'SYNC: taxonomy.json#durum',
  `eklenme` datetime NOT NULL COMMENT 'eklenme (+03:00 sabit, $defs/datetime03)',
  `guncelleme` datetime DEFAULT NULL COMMENT 'guncelleme (NULL = hi├ğ g├╝ncellenmedi)',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'soft delete (hard delete yasak)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'silinme zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_artist_slug` (`slug`),
  KEY `idx_artist_tip` (`tip`),
  KEY `idx_artist_durum` (`durum`),
  KEY `idx_artist_sanatci` (`sanatci`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci COMMENT='Sanat├ğ─▒ ÔÇö media.schema.json $defs/artist (BCNF: her non-key sat─▒r id/slug''a ba─ş─▒ml─▒)';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `asset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asset` (
  `id` char(26) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'ULID ÔÇö $defs/meta_base.id',
  `tip` enum('ses','video') COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'meta discriminator (meta_ses | meta_video)',
  `slug` varchar(80) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'kimlik.slug ÔÇö kebab-case',
  `baslik` varchar(300) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'kimlik.baslik (minLength 1, maxLength 300)',
  `sira` int unsigned NOT NULL COMMENT 'kimlik.sira (min 1) ÔÇö alb├╝m i├ği s─▒ralama',
  `disk` tinyint unsigned DEFAULT NULL COMMENT 'kimlik.disk (min 1, NULL = tek disk)',
  `album_id` char(26) COLLATE utf8mb4_tr_0900_ai_ci DEFAULT NULL COMMENT 'ust.album_id (ulid_null)',
  `sanatci_id` char(26) COLLATE utf8mb4_tr_0900_ai_ci DEFAULT NULL COMMENT 'ust.sanatci_id (ulid_null) ÔÇö tekli/├ğe┼şitli istisnas─▒nda tek kaynak',
  `koleksiyon_id` char(26) COLLATE utf8mb4_tr_0900_ai_ci DEFAULT NULL COMMENT 'koleksiyon ba─ş─▒ (asset koleksiyon ├╝yeli─şi)',
  `dosya_yolu` varchar(500) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'repo-g├Âreli dosya yolu ÔÇö ingest kan─▒t─▒, UNIQUE',
  `durum` enum('inbox','aktif','sakli','tekrar','arsiv') COLLATE utf8mb4_tr_0900_ai_ci NOT NULL DEFAULT 'inbox' COMMENT 'SYNC: taxonomy.json#durum',
  `eklenme` datetime NOT NULL COMMENT 'eklenme (+03:00)',
  `guncelleme` datetime DEFAULT NULL COMMENT 'guncelleme (datetime03_null)',
  `ekleyen` varchar(60) COLLATE utf8mb4_tr_0900_ai_ci DEFAULT NULL COMMENT 'ekleyen (maxLength 60)',
  `dosya` varchar(255) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'teknik.dosya ÔÇö ses ^audio.(mp3|flac|wav|m4a|ogg|wma)$ / video ^video.(mp4|mkv|avi|webm)$',
  `format` varchar(16) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'teknik.format ÔÇö ^[a-z0-9]{1,16}$',
  `kapsayici` varchar(16) COLLATE utf8mb4_tr_0900_ai_ci DEFAULT NULL COMMENT 'teknik.kapsayici ÔÇö yalniz video (meta_video z-alani)',
  `boyut_bayt` bigint unsigned NOT NULL COMMENT 'teknik.boyut_bayt (min 1)',
  `sha256` char(64) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'teknik.sha256 ÔÇö kucuk harf hex, UNIQUE',
  `sure_sn` int unsigned DEFAULT NULL COMMENT 'teknik.sure_sn (min 1)',
  `bitrate` int unsigned DEFAULT NULL COMMENT 'teknik.bitrate (bps, min 1)',
  `kanal` tinyint unsigned DEFAULT NULL COMMENT 'teknik.kanal (min 1)',
  `orneklem` int unsigned DEFAULT NULL COMMENT 'teknik.orneklem (Hz, min 1)',
  `derinlik` tinyint unsigned DEFAULT NULL COMMENT 'teknik.derinlik (bit, min 1)',
  `lufs` decimal(5,2) DEFAULT NULL COMMENT 'teknik.lufs (-70.00 .. 0.00)',
  `peak_db` decimal(6,2) DEFAULT NULL COMMENT 'teknik.peak_db',
  `cozunurluk` varchar(11) COLLATE utf8mb4_tr_0900_ai_ci DEFAULT NULL COMMENT 'teknik.cozunurluk ÔÇö ^\\d{2,5}x\\d{2,5}$ (yalniz video)',
  `fps` decimal(7,3) DEFAULT NULL COMMENT 'teknik.fps (yalniz video)',
  `video_kodec` varchar(40) COLLATE utf8mb4_tr_0900_ai_ci DEFAULT NULL COMMENT 'teknik.video_kodec (yalniz video)',
  `ses_kodec` varchar(40) COLLATE utf8mb4_tr_0900_ai_ci DEFAULT NULL COMMENT 'teknik.ses_kodec (yalniz video)',
  `bitrate_kbps` int unsigned DEFAULT NULL COMMENT 'teknik.bitrate_kbps (yalniz video)',
  `bpm` smallint unsigned DEFAULT NULL COMMENT 'muzikal.bpm (20..300)',
  `ton` varchar(20) COLLATE utf8mb4_tr_0900_ai_ci DEFAULT NULL COMMENT 'muzikal.ton (maxLength 20)',
  `enerji` tinyint unsigned DEFAULT NULL COMMENT 'muzikal.enerji (1..10)',
  `intro_sn` decimal(8,3) DEFAULT NULL COMMENT 'muzikal.intro_sn (min 0)',
  `outro_sn` decimal(8,3) DEFAULT NULL COMMENT 'muzikal.outro_sn (min 0)',
  `poster` varchar(255) COLLATE utf8mb4_tr_0900_ai_ci DEFAULT NULL COMMENT 'gorsel.poster (yalniz video; ses kaydinda NULL)',
  `poster_zaman` decimal(8,3) DEFAULT NULL COMMENT 'gorsel.poster_zaman (0 .. sure_sn)',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'soft delete',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'silinme zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_asset_dosya_yolu` (`dosya_yolu`),
  UNIQUE KEY `uk_asset_sha256` (`sha256`),
  UNIQUE KEY `uk_asset_slug` (`slug`),
  UNIQUE KEY `uq_asset_album_sira` (`album_id`,`sira`),
  KEY `idx_asset_album_id` (`album_id`),
  KEY `idx_asset_sanatci_id` (`sanatci_id`),
  KEY `idx_asset_koleksiyon_id` (`koleksiyon_id`),
  KEY `idx_asset_tip` (`tip`),
  KEY `idx_asset_durum` (`durum`),
  KEY `idx_asset_album_sira` (`album_id`,`sira`),
  FULLTEXT KEY `ftx_asset_baslik` (`baslik`),
  CONSTRAINT `fk_asset_album` FOREIGN KEY (`album_id`) REFERENCES `album` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_asset_artist` FOREIGN KEY (`sanatci_id`) REFERENCES `artist` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_asset_koleksiyon` FOREIGN KEY (`koleksiyon_id`) REFERENCES `koleksiyon` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci COMMENT='Medya varl─▒─ş─▒ (ses|video) ÔÇö $defs/meta_base; tekil dosya yolu + sha256 ile yinelenme imkans─▒z';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `asset_tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asset_tag` (
  `asset_id` char(26) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'Varl─▒k (FK)',
  `alan` varchar(60) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'tag anahtar─▒ (taxonomy.alan)',
  `deger` varchar(60) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'tag de─şeri (taxonomy.deger) ÔÇö kapal─▒ set',
  PRIMARY KEY (`asset_id`,`alan`,`deger`),
  KEY `idx_asset_tag_alan_deger` (`alan`,`deger`),
  CONSTRAINT `fk_asset_tag_asset` FOREIGN KEY (`asset_id`) REFERENCES `asset` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_asset_tag_taxonomy` FOREIGN KEY (`alan`, `deger`) REFERENCES `taxonomy` (`alan`, `deger`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci COMMENT='Asset Ôåö kapal─▒ s├Âzl├╝k ba─ş─▒ ÔÇö taxonomy FK sayesinde enum drift DB seviyesinde imkansiz';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ingest_batch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ingest_batch` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'Parti kimli─şi (saya├ğ)',
  `kaynak` varchar(500) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'Tarama kayna─ş─▒ (repo-g├Âreli dizin/yol)',
  `mod` enum('dry-run','commit') COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'dry-run = yaln─▒z rapor, commit = yaz',
  `dosya_sayisi` int unsigned NOT NULL DEFAULT '0' COMMENT '─░┼şlenen dosya say─▒s─▒',
  `byte` bigint unsigned NOT NULL DEFAULT '0' COMMENT '─░┼şlenen toplam bayt',
  `baslangic` datetime NOT NULL COMMENT 'Parti ba┼şlang─▒c─▒ (+03:00)',
  `bitis` datetime DEFAULT NULL COMMENT 'Parti biti┼şi (NULL = s├╝r├╝yor)',
  `durum` enum('planlandi','calisiyor','basarili','hatali') COLLATE utf8mb4_tr_0900_ai_ci NOT NULL DEFAULT 'planlandi' COMMENT 'Parti durumu',
  `rapor_yolu` varchar(500) COLLATE utf8mb4_tr_0900_ai_ci DEFAULT NULL COMMENT '├£retilen rapor dosyas─▒ yolu (repo-g├Âreli)',
  PRIMARY KEY (`id`),
  KEY `idx_ingest_batch_durum` (`durum`),
  KEY `idx_ingest_batch_baslangic` (`baslangic`),
  KEY `idx_ingest_batch_mod` (`mod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci COMMENT='Ingest/tarama parti kaydi ÔÇö ADR-092 ┬ğ5.2 ad─▒mlar─▒n─▒n denetlenebilir izi';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `koleksiyon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `koleksiyon` (
  `id` char(26) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'ULID ÔÇö $defs/koleksiyon.id',
  `slug` varchar(80) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'kebab-case ÔÇö $defs/slug',
  `koleksiyon` varchar(300) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'koleksiyon (maxLength 300)',
  `kaynak_tur` enum('usb','disk','indirme','diger') COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'SYNC: taxonomy.json#koleksiyon_kaynak',
  `sarki_sayisi` int unsigned NOT NULL DEFAULT '0' COMMENT 'sarki_sayisi (min 0)',
  `tag` json NOT NULL COMMENT 'tag {tur[]} ÔÇö $defs/koleksiyon.tag',
  `durum` enum('inbox','aktif','sakli','tekrar','arsiv') COLLATE utf8mb4_tr_0900_ai_ci NOT NULL DEFAULT 'inbox' COMMENT 'SYNC: taxonomy.json#durum',
  `eklenme` datetime NOT NULL COMMENT 'eklenme (+03:00)',
  `guncelleme` datetime DEFAULT NULL COMMENT 'guncelleme (datetime03_null)',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'soft delete',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'silinme zaman─▒',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_koleksiyon_slug` (`slug`),
  KEY `idx_koleksiyon_kaynak_tur` (`kaynak_tur`),
  KEY `idx_koleksiyon_durum` (`durum`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci COMMENT='Koleksiyon ÔÇö media.schema.json $defs/koleksiyon (ba─ş─▒ms─▒z varl─▒k, asset''ten ayr─▒ld─▒)';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `path_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `path_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'Monotonic saya├ğ (iz sat─▒r─▒, ULID de─şil)',
  `asset_id` char(26) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT '─░zlenen varl─▒k (FK)',
  `eski_yol` varchar(500) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'Yeniden adland─▒rma ├Âncesi repo-g├Âreli yol',
  `yeni_yol` varchar(500) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'Yeniden adland─▒rma sonras─▒ repo-g├Âreli yol',
  `degistiren` varchar(60) COLLATE utf8mb4_tr_0900_ai_ci DEFAULT NULL COMMENT 'De─şi┼ştiren (agent/kullan─▒c─▒)',
  `zaman` datetime NOT NULL COMMENT 'De─şi┼şiklik zaman─▒ (+03:00)',
  PRIMARY KEY (`id`),
  KEY `idx_path_history_asset_id` (`asset_id`),
  KEY `idx_path_history_zaman` (`zaman`),
  CONSTRAINT `fk_path_history_asset` FOREIGN KEY (`asset_id`) REFERENCES `asset` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci COMMENT='Dosya yolu de─şi┼şiklik izi (rename audit) ÔÇö append-only';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `taxonomy`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `taxonomy` (
  `alan` varchar(60) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'taxonomy.json anahtar─▒ (17 tan─▒m: tur, kullanim, ...)',
  `deger` varchar(60) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'o anahtar─▒n de─şeri (kapal─▒ set ÔÇö yeni de─şer = taxonomy.json''a 1 sat─▒r)',
  `sira` int unsigned NOT NULL DEFAULT '0' COMMENT 'taxonomy.json dizili┼ş s─▒ras─▒ (1..N)',
  `aktif` tinyint(1) NOT NULL DEFAULT '1' COMMENT 's├Âzl├╝kte aktif mi?',
  PRIMARY KEY (`alan`,`deger`),
  KEY `idx_taxonomy_sira` (`alan`,`sira`),
  KEY `idx_taxonomy_aktif` (`aktif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci COMMENT='Kapal─▒ s├Âzl├╝k ÔÇö taxonomy.json''─▒n 1:1 t├╝retilmi┼ş indeksi (17 anahtar)';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `v_asset_search`;
/*!50001 DROP VIEW IF EXISTS `v_asset_search`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_asset_search` AS SELECT 
 1 AS `asset_id`,
 1 AS `asset_slug`,
 1 AS `baslik`,
 1 AS `asset_tip`,
 1 AS `sira`,
 1 AS `disk`,
 1 AS `asset_durum`,
 1 AS `dosya_yolu`,
 1 AS `sure_sn`,
 1 AS `sha256`,
 1 AS `album_id`,
 1 AS `album_slug`,
 1 AS `album`,
 1 AS `album_tip`,
 1 AS `album_yil`,
 1 AS `album_fiziksel`,
 1 AS `sanatci_id`,
 1 AS `sanatci_slug`,
 1 AS `sanatci`,
 1 AS `sanatci_tip`,
 1 AS `tur`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `variant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `variant` (
  `asset_id` char(26) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'Ana varl─▒k (FK) ÔÇö $defs/meta_base.id',
  `dosya` varchar(255) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'varyant dosya yolu (maxLength 255)',
  `format` varchar(16) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'varyant format ÔÇö ^[a-z0-9]{1,16}$',
  `boyut_bayt` bigint unsigned NOT NULL COMMENT 'varyant boyutu (min 1)',
  `sha256` char(64) COLLATE utf8mb4_tr_0900_ai_ci NOT NULL COMMENT 'varyant sha256 ÔÇö hex, UNIQUE (yinelenen kodlama imkans─▒z)',
  PRIMARY KEY (`asset_id`,`dosya`),
  UNIQUE KEY `uk_variant_sha256` (`sha256`),
  CONSTRAINT `fk_variant_asset` FOREIGN KEY (`asset_id`) REFERENCES `asset` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_tr_0900_ai_ci COMMENT='Varyant kodlamalar (teknik.varyantlar[]) ÔÇö PK (asset_id, dosya) BCNF aday anahtar─▒';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50001 DROP VIEW IF EXISTS `v_asset_search`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`Ali`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `v_asset_search` AS select `a`.`id` AS `asset_id`,`a`.`slug` AS `asset_slug`,`a`.`baslik` AS `baslik`,`a`.`tip` AS `asset_tip`,`a`.`sira` AS `sira`,`a`.`disk` AS `disk`,`a`.`durum` AS `asset_durum`,`a`.`dosya_yolu` AS `dosya_yolu`,`a`.`sure_sn` AS `sure_sn`,`a`.`sha256` AS `sha256`,`al`.`id` AS `album_id`,`al`.`slug` AS `album_slug`,`al`.`album` AS `album`,`al`.`album_tip` AS `album_tip`,`al`.`yil` AS `album_yil`,`al`.`fiziksel` AS `album_fiziksel`,`ar`.`id` AS `sanatci_id`,`ar`.`slug` AS `sanatci_slug`,`ar`.`sanatci` AS `sanatci`,`ar`.`tip` AS `sanatci_tip`,coalesce(json_extract(`al`.`tag`,'$.tur'),json_extract(`ar`.`varsayilan_tag`,'$.tur')) AS `tur` from ((`asset` `a` left join `album` `al` on(((`al`.`id` = `a`.`album_id`) and (`al`.`is_deleted` = 0)))) left join `artist` `ar` on(((`ar`.`id` = coalesce(`a`.`sanatci_id`,`al`.`sanatci_id`)) and (`ar`.`is_deleted` = 0)))) where (`a`.`is_deleted` = 0) */;
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

-- SEED (KVKK onayli whitelist — yalniz bu tablolar): taxonomy

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

LOCK TABLES `taxonomy` WRITE;
/*!40000 ALTER TABLE `taxonomy` DISABLE KEYS */;
INSERT INTO `taxonomy` (`alan`, `deger`, `sira`, `aktif`) VALUES ('album_tip','canli',2,1),('album_tip','derleme',3,1),('album_tip','ep',4,1),('album_tip','soundtrack',5,1),('album_tip','studyo',1,1),('album_tip','tekli',6,1),('altyazi_tip','govde',2,1),('altyazi_tip','yan',1,1),('bitrate_tip','abr',4,1),('bitrate_tip','cbr',2,1),('bitrate_tip','vbr',3,1),('bitrate_tip','yok',1,1),('cover_kaynak','dis',3,1),('cover_kaynak','kaynak',1,1),('cover_kaynak','uretilmis',2,1),('dil','ar',7,1),('dil','az',10,1),('dil','de',3,1),('dil','en',2,1),('dil','es',6,1),('dil','fa',8,1),('dil','fr',4,1),('dil','it',5,1),('dil','ku',11,1),('dil','ru',9,1),('dil','sq',12,1),('dil','tr',1,1),('donem','2000ler',5,1),('donem','2010lar',6,1),('donem','2020lar',7,1),('donem','70ler',2,1),('donem','80ler',3,1),('donem','90ler',4,1),('donem','on-yil',1,1),('durum','aktif',2,1),('durum','arsiv',5,1),('durum','inbox',1,1),('durum','sakli',3,1),('durum','tekrar',4,1),('fiziksel_bicim','45lik',2,1),('fiziksel_bicim','7lik',3,1),('fiziksel_bicim','cd',4,1),('fiziksel_bicim','dijital',6,1),('fiziksel_bicim','kaset',5,1),('fiziksel_bicim','lp',1,1),('klip_tipi','acoustic',5,1),('klip_tipi','dance',4,1),('klip_tipi','live',2,1),('klip_tipi','lyric',3,1),('klip_tipi','official',1,1),('koleksiyon_hak','bilinmiyor',3,1),('koleksiyon_hak','kisisel',1,1),('koleksiyon_hak','ticari',2,1),('koleksiyon_kaynak','diger',4,1),('koleksiyon_kaynak','disk',2,1),('koleksiyon_kaynak','indirme',3,1),('koleksiyon_kaynak','usb',1,1),('kullanim','alt-yapi',4,1),('kullanim','ara-show',7,1),('kullanim','dj-set',8,1),('kullanim','dugun',1,1),('kullanim','hane',2,1),('kullanim','kina',5,1),('kullanim','org-esligi',3,1),('kullanim','pasta-nikah',6,1),('media_tipi','canli',4,1),('media_tipi','cover',6,1),('media_tipi','enstrumantal',2,1),('media_tipi','full',1,1),('media_tipi','karaoke',3,1),('media_tipi','remix',5,1),('ruh','duygusal',1,1),('ruh','enerjik',3,1),('ruh','huzunlu',5,1),('ruh','nostaljik',4,1),('ruh','yavas',2,1),('sanatci_tip','cesitli',4,1),('sanatci_tip','grup',2,1),('sanatci_tip','kisi',1,1),('sanatci_tip','topluluk',3,1),('tur','arabesk',1,1),('tur','damar',2,1),('tur','dans',10,1),('tur','enstrumantal',13,1),('tur','halay',8,1),('tur','klasik',11,1),('tur','nostalji',6,1),('tur','oryantel',7,1),('tur','roman-sorti',9,1),('tur','slow',12,1),('tur','turk-pop',3,1),('tur','turk-rock',14,1),('tur','turkce',4,1),('tur','yabanci',5,1),('video_kaynak','cekim',4,1),('video_kaynak','dvd',3,1),('video_kaynak','vhs',2,1),('video_kaynak','yt',1,1);
/*!40000 ALTER TABLE `taxonomy` ENABLE KEYS */;
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
