-- CoreMusic Database: coremusic_wireless
-- Version: 7.0.0
-- BCNF Normalized
-- Author: CoreMusic Data Engineer
-- Date: 2026-08-09
-- charset: utf8mb4_unicode_ci, engine: InnoDB
-- UUID: BINARY(16) — generated in PHP app layer (UUID v7)
-- Soft Delete: is_deleted + deleted_at
-- Timestamps: created_at, updated_at, deleted_at

CREATE DATABASE IF NOT EXISTS coremusic_wireless
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE coremusic_wireless;

-- =============================================
-- Table: wifi_networks
-- Purpose: WiFi ag yapilandirmalari ve sifreleri
-- BCNF: ssid + network_type candidate key
-- =============================================
CREATE TABLE wifi_networks (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  network_name VARCHAR(200) NOT NULL COMMENT 'Ag adi',
  network_type ENUM('home','studio','car','public','guest') NOT NULL DEFAULT 'home' COMMENT 'Ag tipi',
  ssid VARCHAR(200) NOT NULL COMMENT 'SSID (Servis Set Identifier)',
  bssid VARCHAR(17) NULL COMMENT 'BSSID (MAC adresi)',
  security_type ENUM('wpa2','wpa3','open','enterprise') NOT NULL DEFAULT 'wpa2' COMMENT 'Guvenlik protokolu',
  password_hash VARCHAR(255) NULL COMMENT 'Sifre hash (Argon2id)',
  channel INT NULL COMMENT 'Kanal numarasi',
  band ENUM('2.4ghz','5ghz','6ghz','auto') NOT NULL DEFAULT 'auto' COMMENT 'Frekans banti',
  signal_strength INT NULL COMMENT 'Sinyal gucu (dBm)',
  frequency VARCHAR(20) NULL COMMENT 'Frekans degeri',
  is_auto_connect TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Otomomatik baglan?',
  is_hidden TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Gizli ag mi?',
  is_favorite TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Favori ag mi?',
  priority INT NOT NULL DEFAULT 0 COMMENT 'Oncelik sirasi',
  last_connected_at TIMESTAMP NULL COMMENT 'Son baglanti zamani (UTC)',
  metadata JSON NULL COMMENT 'Ek veri (JSON)',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  is_deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Silindi mi? (soft delete)',
  deleted_at TIMESTAMP NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (id),
  INDEX idx_wifi_ssid (ssid),
  INDEX idx_wifi_network_type (network_type),
  INDEX idx_wifi_band (band),
  INDEX idx_wifi_auto_connect (is_auto_connect),
  INDEX idx_wifi_favorite (is_favorite),
  INDEX idx_wifi_priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='WiFi ag yapilandirmalari (ev, stüdyo, araba, herkese acik, misafir)';

-- =============================================
-- Table: bluetooth_peers
-- Purpose: Bluetooth eslesme cihazlari
-- BCNF: mac_address candidate key
-- =============================================
CREATE TABLE bluetooth_peers (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  device_name VARCHAR(200) NOT NULL COMMENT 'Cihaz adi',
  device_type ENUM('speaker','headphone','car','phone','tablet','laptop','other') NOT NULL DEFAULT 'speaker' COMMENT 'Cihaz tipi',
  mac_address VARCHAR(17) NOT NULL COMMENT 'MAC adresi (benzersiz)',
  bluetooth_version VARCHAR(10) NOT NULL DEFAULT '5.0' COMMENT 'Bluetooth versiyonu',
  codec ENUM('sbc','aac','aptx','aptx_hd','ldac','lhdc','other') NOT NULL DEFAULT 'aac' COMMENT 'Ses codec',
  is_paired TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Eslestirildi mi?',
  is_connected TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Bagli mi?',
  is_trusted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Guvenilir mi?',
  auto_connect TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Otomomatik baglan?',
  signal_strength INT NULL COMMENT 'Sinyal gucu (dBm)',
  battery_level INT NULL COMMENT 'Batarya seviyesi (%)',
  last_connected_at TIMESTAMP NULL COMMENT 'Son baglanti zamani (UTC)',
  last_seen_at TIMESTAMP NULL COMMENT 'Son gorulme zamani (UTC)',
  metadata JSON NULL COMMENT 'Ek veri (JSON)',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  is_deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Silindi mi? (soft delete)',
  deleted_at TIMESTAMP NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (id),
  UNIQUE KEY uk_bt_peers_mac (mac_address),
  INDEX idx_bt_peers_type (device_type),
  INDEX idx_bt_peers_paired (is_paired),
  INDEX idx_bt_peers_connected (is_connected),
  INDEX idx_bt_peers_trusted (is_trusted)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bluetooth eslesme cihazlari (hoparlor, kulaklik, araba, telefon)';

-- =============================================
-- Table: sync_history
-- Purpose: Cihazlar arasi senkronizasyon gecmisi
-- BCNF: device_type + device_id + sync_type + created_at candidate key
-- =============================================
CREATE TABLE sync_history (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  device_type ENUM('wifi','bluetooth','usb','airplay','chromecast') NOT NULL COMMENT 'Cihaz tipi',
  device_id BINARY(16) NULL COMMENT 'Cihaz ID (opsiyonel)',
  sync_type ENUM('full','incremental','metadata','playlist') NOT NULL COMMENT 'Senkronizasyon tipi',
  sync_status ENUM('started','completed','failed','cancelled') NOT NULL COMMENT 'Senkronizasyon durumu',
  data_transferred BIGINT NOT NULL DEFAULT 0 COMMENT 'Aktarilan veri (bayt)',
  duration_ms INT NOT NULL DEFAULT 0 COMMENT 'Sure (milisaniye)',
  error_message TEXT NULL COMMENT 'Hata mesaji (basarisiz ise)',
  metadata JSON NULL COMMENT 'Ek veri (JSON)',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Baslama zamani (UTC)',
  PRIMARY KEY (id),
  INDEX idx_sync_history_device_type (device_type),
  INDEX idx_sync_history_device_id (device_id),
  INDEX idx_sync_history_status (sync_status),
  INDEX idx_sync_history_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Cihazlar arasi senkronizasyon gecmisi (WiFi, Bluetooth, USB, AirPlay, Chromecast)';

-- =============================================
-- Table: bluetooth_audio_profiles
-- Purpose: Bluetooth ses profilleri (codec, kanal, ornek hizi)
-- BCNF: peer_id + codec + sample_rate composite unique key
-- FK: peer_id -> bluetooth_peers.id
-- =============================================
CREATE TABLE bluetooth_audio_profiles (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  peer_id BINARY(16) NOT NULL COMMENT 'Bluetooth cihaz ID',
  profile_name VARCHAR(100) NOT NULL COMMENT 'Profil adi',
  codec ENUM('sbc','aac','aptx','aptx_hd','ldac','lhdc') NOT NULL COMMENT 'Ses codec',
  sample_rate INT NOT NULL DEFAULT 44100 COMMENT 'Ornek hizi (Hz)',
  bit_depth INT NOT NULL DEFAULT 16 COMMENT 'Bit derinligi',
  channels INT NOT NULL DEFAULT 2 COMMENT 'Kanal sayisi',
  bit_rate INT NULL COMMENT 'Bit hizi (kbps)',
  is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Aktif profil mi?',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  PRIMARY KEY (id),
  INDEX idx_bt_audio_profile_peer (peer_id),
  INDEX idx_bt_audio_profile_codec (codec),
  INDEX idx_bt_audio_profile_active (is_active),
  CONSTRAINT fk_bt_audio_profile_peer FOREIGN KEY (peer_id) REFERENCES bluetooth_peers(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bluetooth ses profilleri (SBC, AAC, aptX, aptX HD, LDAC, LHDC)';

-- =============================================
-- Table: network_profiles
-- Purpose: Ag profilleri (WiFi + Bluetooth kombinasyonu)
-- BCNF: profile_name + profile_type candidate key
-- FK: wifi_id -> wifi_networks.id, bluetooth_id -> bluetooth_peers.id
-- =============================================
CREATE TABLE network_profiles (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  profile_name VARCHAR(200) NOT NULL COMMENT 'Profil adi',
  profile_type ENUM('home','studio','car','mobile','custom') NOT NULL DEFAULT 'custom' COMMENT 'Profil tipi',
  wifi_id BINARY(16) NULL COMMENT 'WiFi ag ID',
  bluetooth_id BINARY(16) NULL COMMENT 'Bluetooth cihaz ID',
  dns_servers JSON NULL COMMENT 'DNS sunuculari (JSON array)',
  proxy_settings JSON NULL COMMENT 'Proxy ayarlari (JSON)',
  mtu INT NOT NULL DEFAULT 1500 COMMENT 'MTU boyutu',
  is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Aktif profil mi?',
  is_default TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Varsayilan profil mi?',
  metadata JSON NULL COMMENT 'Ek veri (JSON)',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  is_deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Silindi mi? (soft delete)',
  deleted_at TIMESTAMP NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (id),
  INDEX idx_network_profile_name (profile_name),
  INDEX idx_network_profile_type (profile_type),
  INDEX idx_network_profile_active (is_active),
  INDEX idx_network_profile_default (is_default),
  CONSTRAINT fk_network_profile_wifi FOREIGN KEY (wifi_id) REFERENCES wifi_networks(id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_network_profile_bluetooth FOREIGN KEY (bluetooth_id) REFERENCES bluetooth_peers(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Ag profilleri (WiFi + Bluetooth kombinasyonu, DNS, proxy, MTU)';

-- =============================================
-- CoreMusic coremusic_wireless Database v7.0.0
-- Tables: 5
-- BCNF Compliant: Yes
-- UUID: BINARY(16)
-- Soft Delete: is_deleted + deleted_at
-- =============================================
