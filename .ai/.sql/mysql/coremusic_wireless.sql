-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL → .ai/.sql/mysql SSOT)
-- Kaynak : canlı MySQL · birebir SHOW CREATE · Tarih: 2026-10-07
-- Şema   : TAM (tüm tablolar/views) · Üreteç: full_dump.php
-- VERİ   : YOK — kullanıcı/davranış/credential verisi bilinçli yazılmaz (KVKK/REDACTED)
-- Not    : önceki elle-yazılmış başlık/BCNF comment'leri git geçmişindedir.
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_wireless`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_wireless`;

CREATE TABLE `bluetooth_audio_profiles` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `peer_id` binary(16) NOT NULL COMMENT 'Bluetooth cihaz ID',
  `profile_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Profil adi',
  `codec` enum('sbc','aac','aptx','aptx_hd','ldac','lhdc') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ses codec',
  `sample_rate` int NOT NULL DEFAULT '44100' COMMENT 'Ornek hizi (Hz)',
  `bit_depth` int NOT NULL DEFAULT '16' COMMENT 'Bit derinligi',
  `channels` int NOT NULL DEFAULT '2' COMMENT 'Kanal sayisi',
  `bit_rate` int DEFAULT NULL COMMENT 'Bit hizi (kbps)',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Aktif profil mi?',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  PRIMARY KEY (`id`),
  KEY `idx_bt_audio_profile_peer` (`peer_id`),
  KEY `idx_bt_audio_profile_codec` (`codec`),
  KEY `idx_bt_audio_profile_active` (`is_active`),
  CONSTRAINT `fk_bt_audio_profile_peer` FOREIGN KEY (`peer_id`) REFERENCES `bluetooth_peers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bluetooth ses profilleri (SBC, AAC, aptX, aptX HD, LDAC, LHDC)';

CREATE TABLE `bluetooth_peers` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `device_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Cihaz adi',
  `device_type` enum('speaker','headphone','car','phone','tablet','laptop','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'speaker' COMMENT 'Cihaz tipi',
  `mac_address` varchar(17) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'MAC adresi (benzersiz)',
  `bluetooth_version` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '5.0' COMMENT 'Bluetooth versiyonu',
  `codec` enum('sbc','aac','aptx','aptx_hd','ldac','lhdc','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aac' COMMENT 'Ses codec',
  `is_paired` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Eslestirildi mi?',
  `is_connected` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Bagli mi?',
  `is_trusted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Guvenilir mi?',
  `auto_connect` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Otomomatik baglan?',
  `signal_strength` int DEFAULT NULL COMMENT 'Sinyal gucu (dBm)',
  `battery_level` int DEFAULT NULL COMMENT 'Batarya seviyesi (%)',
  `last_connected_at` timestamp NULL DEFAULT NULL COMMENT 'Son baglanti zamani (UTC)',
  `last_seen_at` timestamp NULL DEFAULT NULL COMMENT 'Son gorulme zamani (UTC)',
  `metadata` json DEFAULT NULL COMMENT 'Ek veri (JSON)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Silindi mi? (soft delete)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_bt_peers_mac` (`mac_address`),
  KEY `idx_bt_peers_type` (`device_type`),
  KEY `idx_bt_peers_paired` (`is_paired`),
  KEY `idx_bt_peers_connected` (`is_connected`),
  KEY `idx_bt_peers_trusted` (`is_trusted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bluetooth eslesme cihazlari (hoparlor, kulaklik, araba, telefon)';

CREATE TABLE `network_profiles` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `profile_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Profil adi',
  `profile_type` enum('home','studio','car','mobile','custom') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'custom' COMMENT 'Profil tipi',
  `wifi_id` binary(16) DEFAULT NULL COMMENT 'WiFi ag ID',
  `bluetooth_id` binary(16) DEFAULT NULL COMMENT 'Bluetooth cihaz ID',
  `dns_servers` json DEFAULT NULL COMMENT 'DNS sunuculari (JSON array)',
  `proxy_settings` json DEFAULT NULL COMMENT 'Proxy ayarlari (JSON)',
  `mtu` int NOT NULL DEFAULT '1500' COMMENT 'MTU boyutu',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Aktif profil mi?',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Varsayilan profil mi?',
  `metadata` json DEFAULT NULL COMMENT 'Ek veri (JSON)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Silindi mi? (soft delete)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (`id`),
  KEY `idx_network_profile_name` (`profile_name`),
  KEY `idx_network_profile_type` (`profile_type`),
  KEY `idx_network_profile_active` (`is_active`),
  KEY `idx_network_profile_default` (`is_default`),
  KEY `fk_network_profile_wifi` (`wifi_id`),
  KEY `fk_network_profile_bluetooth` (`bluetooth_id`),
  CONSTRAINT `fk_network_profile_bluetooth` FOREIGN KEY (`bluetooth_id`) REFERENCES `bluetooth_peers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_network_profile_wifi` FOREIGN KEY (`wifi_id`) REFERENCES `wifi_networks` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Ag profilleri (WiFi + Bluetooth kombinasyonu, DNS, proxy, MTU)';

CREATE TABLE `sync_history` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `device_type` enum('wifi','bluetooth','usb','airplay','chromecast') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Cihaz tipi',
  `device_id` binary(16) DEFAULT NULL COMMENT 'Cihaz ID (opsiyonel)',
  `sync_type` enum('full','incremental','metadata','playlist') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Senkronizasyon tipi',
  `sync_status` enum('started','completed','failed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Senkronizasyon durumu',
  `data_transferred` bigint NOT NULL DEFAULT '0' COMMENT 'Aktarilan veri (bayt)',
  `duration_ms` int NOT NULL DEFAULT '0' COMMENT 'Sure (milisaniye)',
  `error_message` text COLLATE utf8mb4_unicode_ci COMMENT 'Hata mesaji (basarisiz ise)',
  `metadata` json DEFAULT NULL COMMENT 'Ek veri (JSON)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Baslama zamani (UTC)',
  PRIMARY KEY (`id`),
  KEY `idx_sync_history_device_type` (`device_type`),
  KEY `idx_sync_history_device_id` (`device_id`),
  KEY `idx_sync_history_status` (`sync_status`),
  KEY `idx_sync_history_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cihazlar arasi senkronizasyon gecmisi (WiFi, Bluetooth, USB, AirPlay, Chromecast)';

CREATE TABLE `wifi_networks` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `network_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ag adi',
  `network_type` enum('home','studio','car','public','guest') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'home' COMMENT 'Ag tipi',
  `ssid` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SSID (Servis Set Identifier)',
  `bssid` varchar(17) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'BSSID (MAC adresi)',
  `security_type` enum('wpa2','wpa3','open','enterprise') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'wpa2' COMMENT 'Guvenlik protokolu',
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Sifre hash (Argon2id)',
  `channel` int DEFAULT NULL COMMENT 'Kanal numarasi',
  `band` enum('2.4ghz','5ghz','6ghz','auto') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'auto' COMMENT 'Frekans banti',
  `signal_strength` int DEFAULT NULL COMMENT 'Sinyal gucu (dBm)',
  `frequency` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Frekans degeri',
  `is_auto_connect` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Otomomatik baglan?',
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Gizli ag mi?',
  `is_favorite` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Favori ag mi?',
  `priority` int NOT NULL DEFAULT '0' COMMENT 'Oncelik sirasi',
  `last_connected_at` timestamp NULL DEFAULT NULL COMMENT 'Son baglanti zamani (UTC)',
  `metadata` json DEFAULT NULL COMMENT 'Ek veri (JSON)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Silindi mi? (soft delete)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (`id`),
  KEY `idx_wifi_ssid` (`ssid`),
  KEY `idx_wifi_network_type` (`network_type`),
  KEY `idx_wifi_band` (`band`),
  KEY `idx_wifi_auto_connect` (`is_auto_connect`),
  KEY `idx_wifi_favorite` (`is_favorite`),
  KEY `idx_wifi_priority` (`priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='WiFi ag yapilandirmalari (ev, stüdyo, araba, herkese acik, misafir)';

SET FOREIGN_KEY_CHECKS = 1;
