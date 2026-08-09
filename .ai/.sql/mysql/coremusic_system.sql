-- coremusic_system — SİSTEM, i18n, VERİTABANI
-- Version: 8.0.0
-- BCNF Normalized
-- Author: CoreMusic Data Engineer
-- Date: 2026-08-10
-- charset: utf8mb4_unicode_ci, engine: InnoDB
-- UUID: BINARY(16) — generated in PHP app layer (UUID v7)
-- Soft Delete: is_deleted + deleted_at
-- Timestamps: created_at, updated_at, deleted_at

CREATE DATABASE IF NOT EXISTS coremusic_system
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE coremusic_system;

-- =============================================
-- BÖLÜM 1: SİSTEM YÖNETİMİ (13 tablo)
-- =============================================
-- =============================================
-- Table: system_settings
-- =============================================
CREATE TABLE system_settings (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  setting_key VARCHAR(200) NOT NULL COMMENT 'Ayar anahtari (benzersiz)',
  setting_value TEXT NULL COMMENT 'Ayar degeri',
  setting_type ENUM('string','integer','float','boolean','json','text') NOT NULL DEFAULT 'string' COMMENT 'Deger tipi',
  setting_group VARCHAR(100) NOT NULL DEFAULT 'general' COMMENT 'Ayar grubu',
  description TEXT NULL COMMENT 'Aciklama',
  is_public TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Herkes erisebilir mi?',
  is_readonly TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Salt okunur mu?',
  validation_rules JSON NULL COMMENT 'Dogrulama kurallari (JSON)',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  is_deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Silindi mi? (soft delete)',
  deleted_at TIMESTAMP NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (id),
  UNIQUE KEY uk_system_settings_key (setting_key),
  INDEX idx_system_settings_group (setting_group),
  INDEX idx_system_settings_public (is_public)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Sistem ayarlari ve konfigurasyon parametreleri';

-- =============================================
-- Table: system_eq_presets
-- =============================================
CREATE TABLE system_eq_presets (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  user_id BINARY(16) NULL COMMENT 'Sahip kullanici ID (NULL = fabrika)',
  preset_name VARCHAR(100) NOT NULL COMMENT 'Preset adi',
  preset_type ENUM('user','factory','community','ai_generated') NOT NULL DEFAULT 'user' COMMENT 'Preset tipi',
  eq_type ENUM('parametric','graphic','shelving','pass_through') NOT NULL DEFAULT 'parametric' COMMENT 'EQ tipi',
  band_count INT NOT NULL DEFAULT 31 COMMENT 'Bant sayisi',
  bands JSON NOT NULL COMMENT 'Bant degerleri (JSON array)',
  preamp_db DECIMAL(6,2) NOT NULL DEFAULT 0.00 COMMENT 'Pre-amplificator degeri (dB)',
  description TEXT NULL COMMENT 'Aciklama',
  is_default TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Varsayilan preset mi?',
  is_public TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Herkes gorebilir mi?',
  use_count INT NOT NULL DEFAULT 0 COMMENT 'Kullanim sayisi',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  is_deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Silindi mi? (soft delete)',
  deleted_at TIMESTAMP NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (id),
  INDEX idx_eq_presets_user (user_id),
  INDEX idx_eq_presets_type (preset_type),
  INDEX idx_eq_presets_default (is_default),
  INDEX idx_eq_presets_public (is_public),
  CONSTRAINT fk_eq_presets_user FOREIGN KEY (user_id) REFERENCES coremusic_auth.users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='EQ on ayarlari (kullanici, fabrika, topluluk, AI uretimli)';

-- =============================================
-- Table: system_notifications
-- =============================================
CREATE TABLE system_notifications (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  user_id BINARY(16) NOT NULL COMMENT 'Hedef kullanici ID',
  notification_type ENUM('info','warning','error','success','system','social','download','update') NOT NULL COMMENT 'Bildirim tipi',
  title VARCHAR(200) NOT NULL COMMENT 'Bildirim basligi',
  message TEXT NOT NULL COMMENT 'Bildirim icerigi',
  action_url VARCHAR(500) NULL COMMENT 'Aksiyon URL',
  action_label VARCHAR(100) NULL COMMENT 'Aksiyon butonu etiketi',
  priority ENUM('low','medium','high','urgent') NOT NULL DEFAULT 'medium' COMMENT 'Oncelik',
  is_read TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Okundu mu?',
  is_dismissed TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Gormezden gelindi mi?',
  read_at TIMESTAMP NULL COMMENT 'Okunma zamani (UTC)',
  expires_at TIMESTAMP NULL COMMENT 'Son kullanma zamani (UTC)',
  metadata JSON NULL COMMENT 'Ek veri (JSON)',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  is_deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Silindi mi? (soft delete)',
  deleted_at TIMESTAMP NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (id),
  INDEX idx_notifications_user (user_id),
  INDEX idx_notifications_type (notification_type),
  INDEX idx_notifications_read (is_read),
  INDEX idx_notifications_priority (priority),
  INDEX idx_notifications_created (created_at),
  CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES coremusic_auth.users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Kullanici bildirimleri (sistem, sosyal, indirme, guncelleme)';
-- =============================================
-- Table: system_file_manager
-- =============================================
CREATE TABLE system_file_manager (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  parent_id BINARY(16) NULL COMMENT 'Ust klasor ID (NULL = kok)',
  file_name VARCHAR(500) NOT NULL COMMENT 'Dosya/klasor adi',
  file_path VARCHAR(1000) NOT NULL COMMENT 'Tam dosya yolu',
  file_type ENUM('file','folder') NOT NULL COMMENT 'Dosya tipi',
  file_size BIGINT NOT NULL DEFAULT 0 COMMENT 'Dosya boyutu (bayt)',
  mime_type VARCHAR(100) NULL COMMENT 'MIME tipi',
  owner_id BINARY(16) NULL COMMENT 'Sahip kullanici ID',
  visibility ENUM('private','shared','public') NOT NULL DEFAULT 'private' COMMENT 'Gorunurluk',
  sort_order INT NOT NULL DEFAULT 0 COMMENT 'Siralama',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  is_deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Silindi mi? (soft delete)',
  deleted_at TIMESTAMP NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (id),
  INDEX idx_file_manager_parent (parent_id),
  INDEX idx_file_manager_owner (owner_id),
  INDEX idx_file_manager_type (file_type),
  INDEX idx_file_manager_visibility (visibility),
  INDEX idx_file_manager_sort (sort_order),
  CONSTRAINT fk_file_manager_parent FOREIGN KEY (parent_id) REFERENCES system_file_manager(id) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT fk_file_manager_owner FOREIGN KEY (owner_id) REFERENCES coremusic_auth.users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Dosya ve klasor yonetimi (hiyerarsik agac yapisi)';

-- =============================================
-- Table: system_cache
-- =============================================
CREATE TABLE system_cache (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  cache_key VARCHAR(255) NOT NULL COMMENT 'Onbellek anahtari (benzersiz)',
  cache_value LONGTEXT NULL COMMENT 'Onbellek degeri',
  cache_type ENUM('query','config','session','data','template') NOT NULL DEFAULT 'data' COMMENT 'Onbellek tipi',
  cache_ttl INT NOT NULL DEFAULT 3600 COMMENT 'Yasam suresi (saniye)',
  cache_tags JSON NULL COMMENT 'Etiketler (JSON array)',
  hit_count INT NOT NULL DEFAULT 0 COMMENT 'Erisim sayisi',
  miss_count INT NOT NULL DEFAULT 0 COMMENT 'Kacirma sayisi',
  last_hit_at TIMESTAMP NULL COMMENT 'Son erisim zamani (UTC)',
  expires_at TIMESTAMP NOT NULL COMMENT 'Son kullanma zamani (UTC)',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  PRIMARY KEY (id),
  UNIQUE KEY uk_cache_key (cache_key),
  INDEX idx_cache_type (cache_type),
  INDEX idx_cache_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Uygulama onbellek verisi (query, config, session, data, template)';

-- =============================================
-- Table: system_wifi_networks
-- =============================================
CREATE TABLE system_wifi_networks (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  network_name VARCHAR(200) NOT NULL COMMENT 'Ag adi',
  network_type ENUM('home','studio','car','public','guest') NOT NULL DEFAULT 'home' COMMENT 'Ag tipi',
  security_type ENUM('wpa2','wpa3','open','enterprise') NOT NULL DEFAULT 'wpa2' COMMENT 'Guvenlik tipi',
  ssid VARCHAR(200) NOT NULL COMMENT 'SSID',
  bssid VARCHAR(17) NULL COMMENT 'BSSID (MAC)',
  channel INT NULL COMMENT 'Kanal numarasi',
  signal_strength INT NULL COMMENT 'Sinyal gucu (dBm)',
  frequency VARCHAR(20) NULL COMMENT 'Frekans (2.4GHz/5GHz)',
  is_auto_connect TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Otomomatik baglan?',
  is_hidden TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Gizli ag mi?',
  priority INT NOT NULL DEFAULT 0 COMMENT 'Oncelik',
  metadata JSON NULL COMMENT 'Ek veri (JSON)',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  is_deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Silindi mi? (soft delete)',
  deleted_at TIMESTAMP NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (id),
  INDEX idx_wifi_network_name (network_name),
  INDEX idx_wifi_network_type (network_type),
  INDEX idx_wifi_network_ssid (ssid),
  INDEX idx_wifi_network_auto (is_auto_connect)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='WiFi ag bilgileri (ev, stüdyo, araba, herkese acik, misafir)';

-- =============================================
-- Table: system_bluetooth_devices
-- =============================================
CREATE TABLE system_bluetooth_devices (
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
  last_connected_at TIMESTAMP NULL COMMENT 'Son baglanti zamani (UTC)',
  metadata JSON NULL COMMENT 'Ek veri (JSON)',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  is_deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Silindi mi? (soft delete)',
  deleted_at TIMESTAMP NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (id),
  UNIQUE KEY uk_bt_devices_mac (mac_address),
  INDEX idx_bt_devices_type (device_type),
  INDEX idx_bt_devices_paired (is_paired),
  INDEX idx_bt_devices_connected (is_connected)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Bluetooth cihaz bilgileri (hoparlor, kulaklik, araba, telefon)';
-- =============================================
-- Table: system_app_settings
-- =============================================
CREATE TABLE system_app_settings (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  user_id BINARY(16) NOT NULL COMMENT 'Kullanici ID',
  setting_key VARCHAR(200) NOT NULL COMMENT 'Ayar anahtari',
  setting_value TEXT NULL COMMENT 'Ayar degeri',
  setting_type ENUM('string','integer','float','boolean','json') NOT NULL DEFAULT 'string' COMMENT 'Deger tipi',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  PRIMARY KEY (id),
  UNIQUE KEY uk_app_settings_user_key (user_id, setting_key),
  INDEX idx_app_settings_key (setting_key),
  CONSTRAINT fk_app_settings_user FOREIGN KEY (user_id) REFERENCES coremusic_auth.users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Kullanici bazli uygulama ayarlari';

-- =============================================
-- Table: system_api_endpoints
-- =============================================
CREATE TABLE system_api_endpoints (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  endpoint VARCHAR(500) NOT NULL COMMENT 'API endpoint yolu',
  method ENUM('GET','POST','PUT','DELETE','PATCH') NOT NULL COMMENT 'HTTP metodu',
  description TEXT NULL COMMENT 'Aciklama',
  auth_required TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Kimlik dogrulama gerekli mi?',
  rate_limit INT NOT NULL DEFAULT 100 COMMENT 'Hiz siniri (istek/dakika)',
  is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT 'Aktif mi?',
  version VARCHAR(10) NOT NULL DEFAULT 'v1' COMMENT 'API versiyonu',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  PRIMARY KEY (id),
  UNIQUE KEY uk_api_endpoints_ep_method (endpoint, method),
  INDEX idx_api_endpoints_active (is_active),
  INDEX idx_api_endpoints_version (version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='API endpoint tanimlari ve sinirlamalari';

-- =============================================
-- Table: system_backup
-- =============================================
CREATE TABLE system_backup (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  backup_name VARCHAR(200) NOT NULL COMMENT 'Yedek adi',
  backup_type ENUM('full','incremental','differential') NOT NULL COMMENT 'Yedek tipi',
  backup_scope VARCHAR(500) NULL COMMENT 'Yedek kapsami (DB adi veya tablolar)',
  file_path VARCHAR(500) NOT NULL COMMENT 'Yedek dosya yolu',
  file_size BIGINT NULL COMMENT 'Dosya boyutu (bayt)',
  checksum_sha256 VARCHAR(64) NULL COMMENT 'SHA-256 checksum',
  status ENUM('pending','in_progress','completed','failed') NOT NULL DEFAULT 'pending' COMMENT 'Durum',
  started_at TIMESTAMP NULL COMMENT 'Baslama zamani (UTC)',
  completed_at TIMESTAMP NULL COMMENT 'Tamamlanma zamani (UTC)',
  expires_at TIMESTAMP NULL COMMENT 'Son kullanma zamani (UTC)',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  is_deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Silindi mi? (soft delete)',
  deleted_at TIMESTAMP NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (id),
  INDEX idx_backup_name (backup_name),
  INDEX idx_backup_type (backup_type),
  INDEX idx_backup_status (status),
  INDEX idx_backup_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Veritabani yedekleme kayitlari (tam, artirimli, farkli)';

-- =============================================
-- Table: system_config
-- =============================================
CREATE TABLE system_config (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  config_key VARCHAR(200) NOT NULL COMMENT 'Konfigurasyon anahtari (benzersiz)',
  config_value TEXT NULL COMMENT 'Konfigurasyon degeri',
  config_type ENUM('string','integer','float','boolean','json') NOT NULL DEFAULT 'string' COMMENT 'Deger tipi',
  config_group VARCHAR(100) NOT NULL DEFAULT 'general' COMMENT 'Konfigurasyon grubu',
  description TEXT NULL COMMENT 'Aciklama',
  is_sensitive TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Duyarli veri mi? (sifre, API anahtari)',
  is_readonly TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Salt okunur mu?',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  is_deleted TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Silindi mi? (soft delete)',
  deleted_at TIMESTAMP NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (id),
  UNIQUE KEY uk_config_key (config_key),
  INDEX idx_config_group (config_group),
  INDEX idx_config_sensitive (is_sensitive)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Sistem konfigurasyonu (duyarli veriler: sifre, API anahtari)';

-- =============================================
-- Table: system_schema_versions
-- =============================================
CREATE TABLE system_schema_versions (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  database_name VARCHAR(100) NOT NULL COMMENT 'Veritabani adi',
  version VARCHAR(50) NOT NULL COMMENT 'Shema versiyonu',
  description TEXT NULL COMMENT 'Aciklama',
  migration_file VARCHAR(500) NULL COMMENT 'Migration dosya yolu',
  applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Uygulanma zamani (UTC)',
  execution_time_ms INT NULL COMMENT 'Calisma suresi (ms)',
  status ENUM('applied','rolled_back','failed') NOT NULL DEFAULT 'applied' COMMENT 'Durum',
  checksum VARCHAR(64) NULL COMMENT 'Dosya checksum',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  PRIMARY KEY (id),
  INDEX idx_schema_versions_db (database_name),
  INDEX idx_schema_versions_version (version),
  INDEX idx_schema_versions_status (status),
  UNIQUE KEY uk_schema_versions_db_version (database_name, version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Veritabani shema versiyon takibi (migration gecmisi)';

-- =============================================
-- Table: system_migration_log
-- =============================================
CREATE TABLE system_migration_log (
  id BINARY(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  database_name VARCHAR(100) NOT NULL COMMENT 'Veritabani adi',
  migration_name VARCHAR(500) NOT NULL COMMENT 'Migration adi',
  migration_type ENUM('up','down','seed','fix') NOT NULL COMMENT 'Migration tipi',
  status ENUM('pending','in_progress','completed','failed') NOT NULL DEFAULT 'pending' COMMENT 'Durum',
  started_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Baslama zamani (UTC)',
  completed_at TIMESTAMP NULL COMMENT 'Tamamlanma zamani (UTC)',
  execution_time_ms INT NULL COMMENT 'Calisma suresi (ms)',
  rows_affected INT NOT NULL DEFAULT 0 COMMENT 'Etkilenen satir sayisi',
  error_message TEXT NULL COMMENT 'Hata mesaji',
  checksum VARCHAR(64) NULL COMMENT 'Dosya checksum',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  PRIMARY KEY (id),
  INDEX idx_migration_log_db (database_name),
  INDEX idx_migration_log_type (migration_type),
  INDEX idx_migration_log_status (status),
  INDEX idx_migration_log_started (started_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Migration islem loglari (up, down, seed, fix)';

-- =============================================
-- BÖLÜM 3: ULUSLARARASI LAŞTIRMA - i18n (4 tablo)
-- =============================================

-- ──────────────────────────────────────────────────────────────────────────────
-- I18N_LANGUAGES — Desteklenen diller
-- Normal Form : BCNF — code UNIQUE
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE i18n_languages (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    code            VARCHAR(10)         NOT NULL,
    name            VARCHAR(100)        NOT NULL,
    native_name     VARCHAR(100)        NOT NULL,
    is_default      TINYINT(1)          NOT NULL DEFAULT 0,
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,
    sort_order      INT UNSIGNED        NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE  KEY uq_il_code     (code),
    INDEX idx_il_active     (is_active),
    INDEX idx_il_order      (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- I18N_TRANSLATIONS — Ceviriler
-- Normal Form : BCNF — (key, locale, plural_form) UNIQUE
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE i18n_translations (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    translation_key VARCHAR(500)        NOT NULL,
    locale          VARCHAR(10)         NOT NULL,
    value           TEXT                NOT NULL,
    context         VARCHAR(255)            NULL,
    is_plural       TINYINT(1)          NOT NULL DEFAULT 0,
    plural_form     VARCHAR(20)             NULL,
    is_auto_translated TINYINT(1)       NOT NULL DEFAULT 0,
    translator_note TEXT                    NULL,
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE  KEY uq_it_pair    (translation_key, locale, plural_form),
    INDEX idx_it_locale     (locale),
    INDEX idx_it_key        (translation_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- I18N_UI_STRINGS — UI string'leri
-- Normal Form : BCNF — (module, string_key, locale) UNIQUE
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE i18n_ui_strings (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    module          VARCHAR(50)         NOT NULL,
    string_key      VARCHAR(255)        NOT NULL,
    locale          VARCHAR(10)         NOT NULL,
    value           TEXT                NOT NULL,
    default_value   TEXT                    NULL,
    description     TEXT                    NULL,
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE  KEY uq_ius_pair   (module, string_key, locale),
    INDEX idx_ius_module    (module),
    INDEX idx_ius_locale    (locale)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- I18N_USER_LOCALE — Kullanici dil tercihleri
-- Normal Form : BCNF — user_id UNIQUE
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE i18n_user_locale (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED        NOT NULL,
    locale          VARCHAR(10)         NOT NULL DEFAULT 'tr',
    date_format     VARCHAR(20)         NOT NULL DEFAULT 'DD.MM.YYYY',
    time_format     VARCHAR(5)          NOT NULL DEFAULT '24h',
    number_format   VARCHAR(20)         NOT NULL DEFAULT '1.234,56',
    timezone        VARCHAR(50)         NOT NULL DEFAULT 'Europe/Istanbul',
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE  KEY uq_iul_user    (user_id),
    INDEX idx_iul_locale     (locale)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- ORNEK VERI — Desteklenen diller
-- ──────────────────────────────────────────────────────────────────────────────
INSERT INTO i18n_languages (code, name, native_name, is_default, sort_order) VALUES
    ('tr', 'Turkish',      'Turkce',        1, 1),
    ('en', 'English',      'English',       0, 2),
    ('de', 'German',       'Deutsch',       0, 3),
    ('fr', 'French',       'Francais',      0, 4),
    ('es', 'Spanish',      'Espanol',       0, 5),
    ('it', 'Italian',      'Italiano',      0, 6),
    ('pt', 'Portuguese',   'Portugues',     0, 7),
    ('ru', 'Russian',      'Russkiy',       0, 8),
    ('ja', 'Japanese',     'Nihongo',       0, 9),
    ('ko', 'Korean',       'Hangug-eo',     0, 10),
    ('zh', 'Chinese',      'Zhongwen',      0, 11),
    ('ar', 'Arabic',       'Alarabiya',     0, 12);

-- =============================================
-- coremusic_system Database v8.0.0
-- Tables: 41 (13 system + 4 i18n)
-- BCNF Compliant: Yes
-- UUID: BINARY(16)
-- Soft Delete: is_deleted + deleted_at
-- Collation: utf8mb4_unicode_ci
-- =============================================

-- End of coremusic_system schema
