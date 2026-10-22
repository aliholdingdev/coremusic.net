-- =========================================================================
-- CoreMusic — ENTERPRISE BILLING & MONETIZATION SCHEMA (SSOT)
-- Kaynak : .ai/.sql/mysql SSOT · BCNF Normalized · Tarih: 2026-10-22
-- Kapsam : Abonelikler, Fiyatlandirma Planlari, Odeme Yontemleri, Islemler,
--          Faturalar, Ezzamanli Cihaz Calma Kilidi (Concurrent Streaming),
--          Telif & Lisans Haklari, Sanatci Hak Edis Dagitim Defteri (Royalties)
-- Yeniden kurulum: .ai/.sql/mysql/rebuild-all.ps1
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_billing`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_billing`;

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

-- -------------------------------------------------------------------------
-- 1. subscription_plans (Abonelik Paketleri)
-- -------------------------------------------------------------------------
DROP TABLE IF EXISTS `subscription_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `subscription_plans` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `plan_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tekil plan kodu (free, premium_monthly, family_yearly vb.)',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Plan gorunen adi',
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tier` enum('free','premium','family','student','studio') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'free',
  `billing_interval` enum('free','monthly','quarterly','yearly') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'monthly',
  `price_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `trial_days` int unsigned NOT NULL DEFAULT '0',
  `max_concurrent_streams` int unsigned NOT NULL DEFAULT '1' COMMENT 'Ayni anda calabilecek aktif yayin siniri',
  `audio_quality_tier` enum('standard','high','lossless_flac','hi_res_dsp') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'standard',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sp_code` (`plan_code`),
  KEY `idx_sp_tier` (`tier`),
  KEY `idx_sp_active` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Abonelik planlari ve fiyatlandirma tarifesi';
/*!40101 SET character_set_client = @saved_cs_client */;

-- -------------------------------------------------------------------------
-- 2. payment_methods (Kayitli Odeme Yontemleri)
-- -------------------------------------------------------------------------
DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_methods` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `user_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_auth.users.id (UUID v7)',
  `gateway` enum('stripe','iyzico','apple_iap','google_play','manual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'stripe',
  `external_payment_method_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Stripe pm_xxx veya Iyzico token',
  `card_brand` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'visa, mastercard, troy, amex',
  `card_last_four` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exp_month` tinyint unsigned DEFAULT NULL,
  `exp_year` smallint unsigned DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `billing_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_country` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pm_user` (`user_id`),
  KEY `idx_pm_gateway_ext` (`gateway`,`external_payment_method_id`),
  CONSTRAINT `fk_pm_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kullanicilarin tokenized odeme araclari';
/*!40101 SET character_set_client = @saved_cs_client */;

-- -------------------------------------------------------------------------
-- 3. user_subscriptions (Kullanici Abonelik Durumu)
-- -------------------------------------------------------------------------
DROP TABLE IF EXISTS `user_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_subscriptions` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `user_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_auth.users.id (UUID v7)',
  `plan_id` binary(16) NOT NULL COMMENT 'FK -> subscription_plans.id',
  `default_payment_method_id` binary(16) DEFAULT NULL COMMENT 'FK -> payment_methods.id',
  `status` enum('active','past_due','canceled','incomplete','trialing','paused') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `current_period_start` datetime NOT NULL,
  `current_period_end` datetime NOT NULL,
  `cancel_at_period_end` tinyint(1) NOT NULL DEFAULT '0',
  `canceled_at` datetime DEFAULT NULL,
  `trial_start` datetime DEFAULT NULL,
  `trial_end` datetime DEFAULT NULL,
  `payment_gateway` enum('stripe','iyzico','apple_iap','google_play','manual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'stripe',
  `external_subscription_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_us_user` (`user_id`),
  KEY `idx_us_plan` (`plan_id`),
  KEY `idx_us_status` (`status`),
  KEY `idx_us_period_end` (`current_period_end`),
  CONSTRAINT `fk_us_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_us_plan` FOREIGN KEY (`plan_id`) REFERENCES `subscription_plans` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_us_pm` FOREIGN KEY (`default_payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kullanici abonelik donemleri ve durum takibi';
/*!40101 SET character_set_client = @saved_cs_client */;

-- -------------------------------------------------------------------------
-- 4. payment_transactions (Odeme Islemleri Defteri)
-- -------------------------------------------------------------------------
DROP TABLE IF EXISTS `payment_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_transactions` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `user_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_auth.users.id (UUID v7)',
  `subscription_id` binary(16) DEFAULT NULL COMMENT 'FK -> user_subscriptions.id',
  `payment_method_id` binary(16) DEFAULT NULL COMMENT 'FK -> payment_methods.id',
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `status` enum('pending','succeeded','failed','refunded','partially_refunded','disputed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `gateway` enum('stripe','iyzico','apple_iap','google_play','manual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'stripe',
  `gateway_transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `failure_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `failure_message` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refunded_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pt_user` (`user_id`),
  KEY `idx_pt_sub` (`subscription_id`),
  KEY `idx_pt_status` (`status`),
  KEY `idx_pt_created` (`created_at` DESC),
  CONSTRAINT `fk_pt_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_pt_sub` FOREIGN KEY (`subscription_id`) REFERENCES `user_subscriptions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_pt_pm` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Gerceklesen tahsilat ve iade hareketleri';
/*!40101 SET character_set_client = @saved_cs_client */;

-- -------------------------------------------------------------------------
-- 5. invoices (Mali Faturalar)
-- -------------------------------------------------------------------------
DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `invoice_number` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tekil fatura no (INV-2026-000001)',
  `user_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_auth.users.id (UUID v7)',
  `subscription_id` binary(16) DEFAULT NULL COMMENT 'FK -> user_subscriptions.id',
  `transaction_id` binary(16) DEFAULT NULL COMMENT 'FK -> payment_transactions.id',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `status` enum('draft','open','paid','uncollectible','void') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `due_date` date DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `invoice_pdf_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `billing_details_json` json DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_inv_number` (`invoice_number`),
  KEY `idx_inv_user` (`user_id`),
  KEY `idx_inv_status` (`status`),
  KEY `idx_inv_paid_at` (`paid_at` DESC),
  CONSTRAINT `fk_inv_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_inv_sub` FOREIGN KEY (`subscription_id`) REFERENCES `user_subscriptions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inv_tx` FOREIGN KEY (`transaction_id`) REFERENCES `payment_transactions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kullanici e-arşiv / e-fatura kayitlari';
/*!40101 SET character_set_client = @saved_cs_client */;

-- -------------------------------------------------------------------------
-- 6. device_playback_sessions (Concurrent Streaming & Cihaz Kilidi)
-- -------------------------------------------------------------------------
DROP TABLE IF EXISTS `device_playback_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `device_playback_sessions` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `user_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_auth.users.id (UUID v7)',
  `device_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_media.devices.id',
  `session_token` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `playback_state` enum('playing','paused','buffering','stopped') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'playing',
  `current_track_id` binary(16) DEFAULT NULL COMMENT 'FK -> coremusic_musics.musics.id',
  `last_heartbeat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dps_token` (`session_token`),
  KEY `idx_dps_user_active` (`user_id`,`is_active`),
  KEY `idx_dps_heartbeat` (`last_heartbeat`),
  CONSTRAINT `fk_dps_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dps_device` FOREIGN KEY (`device_id`) REFERENCES `coremusic_media`.`devices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_dps_track` FOREIGN KEY (`current_track_id`) REFERENCES `coremusic_musics`.`musics` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cihaz bazli ayni anda calabilme kilidi ve canli oynatma seansi';
/*!40101 SET character_set_client = @saved_cs_client */;

-- -------------------------------------------------------------------------
-- 7. content_licenses (Telif ve Yayin Haklari Sozlesmeleri)
-- -------------------------------------------------------------------------
DROP TABLE IF EXISTS `content_licenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `content_licenses` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `content_type` enum('music','podcast','video') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'music',
  `content_id` binary(16) NOT NULL COMMENT 'musics.id, podcast_shows.id veya music_videos.id',
  `licensor_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pro_affiliation` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ASCAP, BMI, SACEM, MESAM, MSG vb.',
  `license_type` enum('direct','blanket','creative_commons','public_domain') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'direct',
  `territory` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'WORLDWIDE',
  `expires_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cl_content` (`content_type`,`content_id`),
  KEY `idx_cl_licensor` (`licensor_name`),
  KEY `idx_cl_active_expires` (`is_active`,`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Icerik telif ve lisans hakki takibi';
/*!40101 SET character_set_client = @saved_cs_client */;

-- -------------------------------------------------------------------------
-- 8. royalty_payout_ledger (Sanatci / Hak Sahibi Havuz Hak Edis Defteri)
-- -------------------------------------------------------------------------
DROP TABLE IF EXISTS `royalty_payout_ledger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `royalty_payout_ledger` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `artist_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_musics.artists.id',
  `period_month` char(7) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Donem: YYYY-MM',
  `total_eligible_streams` int unsigned NOT NULL DEFAULT '0',
  `gross_pool_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `payout_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'USD',
  `payout_status` enum('calculated','pending_approval','paid','held') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'calculated',
  `paid_at` datetime DEFAULT NULL,
  `transaction_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rpl_artist_period` (`artist_id`,`period_month`),
  KEY `idx_rpl_period` (`period_month`),
  KEY `idx_rpl_status` (`payout_status`),
  CONSTRAINT `fk_rpl_artist` FOREIGN KEY (`artist_id`) REFERENCES `coremusic_musics`.`artists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Aylik streaming havuzundan sanatcilara odenen hak edis defteri';
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
