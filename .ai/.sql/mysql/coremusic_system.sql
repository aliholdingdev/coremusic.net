-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL → .ai/.sql/mysql SSOT)
-- Kaynak : canlı MySQL · birebir SHOW CREATE · Tarih: 2026-10-07
-- Şema   : TAM (tüm tablolar/views) · Üreteç: full_dump.php
-- VERİ   : YALNIZ system_services seed (7 satır — PII YOK)
-- Not    : önceki elle-yazılmış başlık/BCNF comment'leri git geçmişindedir.
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_system`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_system`;

CREATE TABLE `i18n_languages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `native_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_il_code` (`code`),
  KEY `idx_il_active` (`is_active`),
  KEY `idx_il_order` (`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `i18n_translations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `translation_key` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `context` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_plural` tinyint(1) NOT NULL DEFAULT '0',
  `plural_form` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_auto_translated` tinyint(1) NOT NULL DEFAULT '0',
  `translator_note` text COLLATE utf8mb4_unicode_ci,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_it_pair` (`translation_key`,`locale`,`plural_form`),
  KEY `idx_it_locale` (`locale`),
  KEY `idx_it_key` (`translation_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `i18n_ui_strings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `string_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `default_value` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ius_pair` (`module`,`string_key`,`locale`),
  KEY `idx_ius_module` (`module`),
  KEY `idx_ius_locale` (`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `i18n_user_locale` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `locale` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'tr',
  `date_format` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DD.MM.YYYY',
  `time_format` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '24h',
  `number_format` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.234,56',
  `timezone` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Europe/Istanbul',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_iul_user` (`user_id`),
  KEY `idx_iul_locale` (`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `system_api_endpoints` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `endpoint` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'API endpoint yolu',
  `method` enum('GET','POST','PUT','DELETE','PATCH') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'HTTP metodu',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Aciklama',
  `auth_required` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Kimlik dogrulama gerekli mi?',
  `rate_limit` int NOT NULL DEFAULT '100' COMMENT 'Hiz siniri (istek/dakika)',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Aktif mi?',
  `version` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'v1' COMMENT 'API versiyonu',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_api_endpoints_ep_method` (`endpoint`,`method`),
  KEY `idx_api_endpoints_active` (`is_active`),
  KEY `idx_api_endpoints_version` (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='API endpoint tanimlari ve sinirlamalari';

CREATE TABLE `system_app_settings` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `user_id` binary(16) NOT NULL COMMENT 'Kullanici ID',
  `setting_key` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ayar anahtari',
  `setting_value` text COLLATE utf8mb4_unicode_ci COMMENT 'Ayar degeri',
  `setting_type` enum('string','integer','float','boolean','json') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string' COMMENT 'Deger tipi',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_app_settings_user_key` (`user_id`,`setting_key`),
  KEY `idx_app_settings_key` (`setting_key`),
  CONSTRAINT `fk_app_settings_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kullanici bazli uygulama ayarlari';

CREATE TABLE `system_backup` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `backup_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Yedek adi',
  `backup_type` enum('full','incremental','differential') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Yedek tipi',
  `backup_scope` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Yedek kapsami (DB adi veya tablolar)',
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Yedek dosya yolu',
  `file_size` bigint DEFAULT NULL COMMENT 'Dosya boyutu (bayt)',
  `checksum_sha256` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'SHA-256 checksum',
  `status` enum('pending','in_progress','completed','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Durum',
  `started_at` timestamp NULL DEFAULT NULL COMMENT 'Baslama zamani (UTC)',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT 'Tamamlanma zamani (UTC)',
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Son kullanma zamani (UTC)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Silindi mi? (soft delete)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (`id`),
  KEY `idx_backup_name` (`backup_name`),
  KEY `idx_backup_type` (`backup_type`),
  KEY `idx_backup_status` (`status`),
  KEY `idx_backup_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Veritabani yedekleme kayitlari (tam, artirimli, farkli)';

CREATE TABLE `system_bluetooth_devices` (
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
  `last_connected_at` timestamp NULL DEFAULT NULL COMMENT 'Son baglanti zamani (UTC)',
  `metadata` json DEFAULT NULL COMMENT 'Ek veri (JSON)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Silindi mi? (soft delete)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_bt_devices_mac` (`mac_address`),
  KEY `idx_bt_devices_type` (`device_type`),
  KEY `idx_bt_devices_paired` (`is_paired`),
  KEY `idx_bt_devices_connected` (`is_connected`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bluetooth cihaz bilgileri (hoparlor, kulaklik, araba, telefon)';

CREATE TABLE `system_cache` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `cache_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Onbellek anahtari (benzersiz)',
  `cache_value` longtext COLLATE utf8mb4_unicode_ci COMMENT 'Onbellek degeri',
  `cache_type` enum('query','config','session','data','template') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'data' COMMENT 'Onbellek tipi',
  `cache_ttl` int NOT NULL DEFAULT '3600' COMMENT 'Yasam suresi (saniye)',
  `cache_tags` json DEFAULT NULL COMMENT 'Etiketler (JSON array)',
  `hit_count` int NOT NULL DEFAULT '0' COMMENT 'Erisim sayisi',
  `miss_count` int NOT NULL DEFAULT '0' COMMENT 'Kacirma sayisi',
  `last_hit_at` timestamp NULL DEFAULT NULL COMMENT 'Son erisim zamani (UTC)',
  `expires_at` timestamp NOT NULL COMMENT 'Son kullanma zamani (UTC)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_cache_key` (`cache_key`),
  KEY `idx_cache_type` (`cache_type`),
  KEY `idx_cache_expires` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Uygulama onbellek verisi (query, config, session, data, template)';

CREATE TABLE `system_config` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `config_key` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Konfigurasyon anahtari (benzersiz)',
  `config_value` text COLLATE utf8mb4_unicode_ci COMMENT 'Konfigurasyon degeri',
  `config_type` enum('string','integer','float','boolean','json') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string' COMMENT 'Deger tipi',
  `config_group` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general' COMMENT 'Konfigurasyon grubu',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Aciklama',
  `is_sensitive` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Duyarli veri mi? (sifre, API anahtari)',
  `is_readonly` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Salt okunur mu?',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Silindi mi? (soft delete)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_config_key` (`config_key`),
  KEY `idx_config_group` (`config_group`),
  KEY `idx_config_sensitive` (`is_sensitive`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sistem konfigurasyonu (duyarli veriler: sifre, API anahtari)';

CREATE TABLE `system_eq_presets` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `user_id` binary(16) DEFAULT NULL COMMENT 'Sahip kullanici ID (NULL = fabrika)',
  `preset_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Preset adi',
  `preset_type` enum('user','factory','community','ai_generated') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user' COMMENT 'Preset tipi',
  `eq_type` enum('parametric','graphic','shelving','pass_through') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'parametric' COMMENT 'EQ tipi',
  `band_count` int NOT NULL DEFAULT '31' COMMENT 'Bant sayisi',
  `bands` json NOT NULL COMMENT 'Bant degerleri (JSON array)',
  `preamp_db` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT 'Pre-amplificator degeri (dB)',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Aciklama',
  `is_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Varsayilan preset mi?',
  `is_public` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Herkes gorebilir mi?',
  `use_count` int NOT NULL DEFAULT '0' COMMENT 'Kullanim sayisi',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Silindi mi? (soft delete)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (`id`),
  KEY `idx_eq_presets_user` (`user_id`),
  KEY `idx_eq_presets_type` (`preset_type`),
  KEY `idx_eq_presets_default` (`is_default`),
  KEY `idx_eq_presets_public` (`is_public`),
  CONSTRAINT `fk_eq_presets_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='EQ on ayarlari (kullanici, fabrika, topluluk, AI uretimli)';

CREATE TABLE `system_file_manager` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `parent_id` binary(16) DEFAULT NULL COMMENT 'Ust klasor ID (NULL = kok)',
  `file_name` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Dosya/klasor adi',
  `file_path` varchar(1000) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tam dosya yolu',
  `file_type` enum('file','folder') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Dosya tipi',
  `file_size` bigint NOT NULL DEFAULT '0' COMMENT 'Dosya boyutu (bayt)',
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'MIME tipi',
  `owner_id` binary(16) DEFAULT NULL COMMENT 'Sahip kullanici ID',
  `visibility` enum('private','shared','public') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'private' COMMENT 'Gorunurluk',
  `sort_order` int NOT NULL DEFAULT '0' COMMENT 'Siralama',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Silindi mi? (soft delete)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (`id`),
  KEY `idx_file_manager_parent` (`parent_id`),
  KEY `idx_file_manager_owner` (`owner_id`),
  KEY `idx_file_manager_type` (`file_type`),
  KEY `idx_file_manager_visibility` (`visibility`),
  KEY `idx_file_manager_sort` (`sort_order`),
  CONSTRAINT `fk_file_manager_owner` FOREIGN KEY (`owner_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_file_manager_parent` FOREIGN KEY (`parent_id`) REFERENCES `system_file_manager` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Dosya ve klasor yonetimi (hiyerarsik agac yapisi)';

CREATE TABLE `system_jobs` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 (PHP katmaninda uretilir)',
  `job_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Is tipi (alan-olcumlu: media.process, patch.apply, ...)',
  `status` enum('queued','running','done','failed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'queued' COMMENT 'Surec durumu (teknik ENUM — karar #5)',
  `priority` int NOT NULL DEFAULT '100' COMMENT 'Kucuk = once (cekirdek cizelge)',
  `payload` json DEFAULT NULL COMMENT 'Kontrollu JSON argumanlar (karar #9)',
  `ref_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Isin bagli oldugu varlik tipi (opsiyonel)',
  `ref_id` binary(16) DEFAULT NULL COMMENT 'Isin bagli oldugu varlik ID (opsiyonel; polymorphic — FK YOK)',
  `attempts` tinyint unsigned NOT NULL DEFAULT '0' COMMENT 'Deneme sayisi (max 5 onerilir)',
  `last_error` text COLLATE utf8mb4_unicode_ci COMMENT 'Son hata (varsa)',
  `available_at` timestamp NULL DEFAULT NULL COMMENT 'Baslayabilecegi zaman (zamanlanmis is)',
  `started_at` timestamp NULL DEFAULT NULL COMMENT 'Baslangic',
  `finished_at` timestamp NULL DEFAULT NULL COMMENT 'Bitis',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_jobs_status_avail` (`status`,`available_at`),
  KEY `idx_jobs_type` (`job_type`),
  KEY `idx_jobs_priority` (`priority`,`created_at`),
  KEY `idx_jobs_ref` (`ref_type`,`ref_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cekirdek surec/cizelge tablosu (OS metaforu — sistem geneli is kuyrugu)';

CREATE TABLE `system_migration_log` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `database_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Veritabani adi',
  `migration_name` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Migration adi',
  `migration_type` enum('up','down','seed','fix') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Migration tipi',
  `status` enum('pending','in_progress','completed','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Durum',
  `started_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Baslama zamani (UTC)',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT 'Tamamlanma zamani (UTC)',
  `execution_time_ms` int DEFAULT NULL COMMENT 'Calisma suresi (ms)',
  `rows_affected` int NOT NULL DEFAULT '0' COMMENT 'Etkilenen satir sayisi',
  `error_message` text COLLATE utf8mb4_unicode_ci COMMENT 'Hata mesaji',
  `checksum` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Dosya checksum',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  PRIMARY KEY (`id`),
  KEY `idx_migration_log_db` (`database_name`),
  KEY `idx_migration_log_type` (`migration_type`),
  KEY `idx_migration_log_status` (`status`),
  KEY `idx_migration_log_started` (`started_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Migration islem loglari (up, down, seed, fix)';

CREATE TABLE `system_notifications` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `user_id` binary(16) NOT NULL COMMENT 'Hedef kullanici ID',
  `notification_type` enum('info','warning','error','success','system','social','download','update') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Bildirim tipi',
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Bildirim basligi',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Bildirim icerigi',
  `action_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Aksiyon URL',
  `action_label` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Aksiyon butonu etiketi',
  `priority` enum('low','medium','high','urgent') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium' COMMENT 'Oncelik',
  `is_read` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Okundu mu?',
  `is_dismissed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Gormezden gelindi mi?',
  `read_at` timestamp NULL DEFAULT NULL COMMENT 'Okunma zamani (UTC)',
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Son kullanma zamani (UTC)',
  `metadata` json DEFAULT NULL COMMENT 'Ek veri (JSON)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Silindi mi? (soft delete)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (`id`),
  KEY `idx_notifications_user` (`user_id`),
  KEY `idx_notifications_type` (`notification_type`),
  KEY `idx_notifications_read` (`is_read`),
  KEY `idx_notifications_priority` (`priority`),
  KEY `idx_notifications_created` (`created_at`),
  CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Kullanici bildirimleri (sistem, sosyal, indirme, guncelleme)';

CREATE TABLE `system_schema_versions` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `database_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Veritabani adi',
  `version` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Shema versiyonu',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Aciklama',
  `migration_file` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Migration dosya yolu',
  `applied_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Uygulanma zamani (UTC)',
  `execution_time_ms` int DEFAULT NULL COMMENT 'Calisma suresi (ms)',
  `status` enum('applied','rolled_back','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'applied' COMMENT 'Durum',
  `checksum` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Dosya checksum',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_schema_versions_db_version` (`database_name`,`version`),
  KEY `idx_schema_versions_db` (`database_name`),
  KEY `idx_schema_versions_version` (`version`),
  KEY `idx_schema_versions_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Veritabani shema versiyon takibi (migration gecmisi)';

CREATE TABLE `system_services` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 (PHP katmaninda uretilir)',
  `service_code` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kisa kod (§10): control, media, audio, device, network, ai, download',
  `service_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Gorunur ad (§10)',
  `port_primary` int unsigned DEFAULT NULL COMMENT 'Birincil port (§11: 81/5000/9741/3001 ...; NULL = atanmamis)',
  `port_secondary` int unsigned DEFAULT NULL COMMENT 'Ikincil port (§11: 6000 media, 9742 audio WS)',
  `protocol` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'http' COMMENT 'Protokol (§10: HTTP/REST/WS/BLE/WiFi/WebRTC/Internal)',
  `stack` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Teknoloji (§10: PHP 8.4, C++20 JUCE, Node.js+TS ...)',
  `base_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Temel URL (varsa)',
  `lifecycle` enum('active','planned','deprecated') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'planned' COMMENT '§9 hedef-mimari notu: kanitlanan aktif servis yoksa planned',
  `status` enum('up','degraded','down') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'down' COMMENT 'Canli saglik (varsayilan down — olcum yokken dürüst deger)',
  `config` json DEFAULT NULL COMMENT 'Kontrollu JSON ek yapilandirma (karar #9)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_services_code` (`service_code`),
  KEY `idx_services_lifecycle` (`lifecycle`),
  KEY `idx_services_port` (`port_primary`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cekirdek servis kayit defteri (.ai/CLAUDE.md §10/§11 — OS servis tablosu)';

INSERT INTO `system_services` (`id`,`service_code`,`service_name`,`port_primary`,`port_secondary`,`protocol`,`stack`,`base_url`,`lifecycle`,`status`,`config`,`created_at`,`updated_at`,`deleted_at`) VALUES
(X'49d9779dc27f11f1a14f00155de6b252','control','Control Service',81,NULL,'HTTP','PHP 8.4',NULL,'active','down',NULL,'2026-10-07 21:45:38','2026-10-07 21:45:38',NULL),
(X'49d9b7ffc27f11f1a14f00155de6b252','media','Media Service',5000,6000,'HTTP','PHP + FFmpeg',NULL,'active','down',NULL,'2026-10-07 21:45:38','2026-10-07 21:45:38',NULL),
(X'49d9b9f7c27f11f1a14f00155de6b252','audio','Audio Service',9741,9742,'REST/WS','C++20 JUCE',NULL,'planned','down',NULL,'2026-10-07 21:45:38','2026-10-07 21:45:38',NULL),
(X'49d9ba61c27f11f1a14f00155de6b252','device','Device Service',NULL,NULL,'BLE/WiFi/USB','C++20',NULL,'planned','down',NULL,'2026-10-07 21:45:38','2026-10-07 21:45:38',NULL),
(X'49d9babbc27f11f1a14f00155de6b252','network','Network Audio',NULL,NULL,'WebRTC/P2P','C++20',NULL,'planned','down',NULL,'2026-10-07 21:45:38','2026-10-07 21:45:38',NULL),
(X'49d9bf05c27f11f1a14f00155de6b252','ai','AI Service',NULL,NULL,'Internal','PHP + Python',NULL,'planned','down',NULL,'2026-10-07 21:45:38','2026-10-07 21:45:38',NULL),
(X'49d9bf77c27f11f1a14f00155de6b252','download','Download Service',3001,NULL,'HTTP/WS','Node.js + TS',NULL,'active','down',NULL,'2026-10-07 21:45:38','2026-10-07 21:45:38',NULL);

CREATE TABLE `system_settings` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `setting_key` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ayar anahtari (benzersiz)',
  `setting_value` text COLLATE utf8mb4_unicode_ci COMMENT 'Ayar degeri',
  `setting_type` enum('string','integer','float','boolean','json','text') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string' COMMENT 'Deger tipi',
  `setting_group` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general' COMMENT 'Ayar grubu',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Aciklama',
  `is_public` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Herkes erisebilir mi?',
  `is_readonly` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Salt okunur mu?',
  `validation_rules` json DEFAULT NULL COMMENT 'Dogrulama kurallari (JSON)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Silindi mi? (soft delete)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_system_settings_key` (`setting_key`),
  KEY `idx_system_settings_group` (`setting_group`),
  KEY `idx_system_settings_public` (`is_public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sistem ayarlari ve konfigurasyon parametreleri';

CREATE TABLE `system_storage_mounts` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 (PHP katmaninda uretilir)',
  `mount_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kisa ad (orn: library-main, nas-archive)',
  `mount_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kok yol (orn: D:/CoreMusic/library, /mnt/nas/music)',
  `storage_type` enum('local','nas','s3','external','removable') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'local' COMMENT 'Depolama turu (§14: NAS/PC/arac — teknik ENUM, karar #5)',
  `is_readonly` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Salt okunur mu (dis kaynak)',
  `status` enum('mounted','unmounted','error') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unmounted' COMMENT 'Mount durumu (teknik ENUM)',
  `config` json DEFAULT NULL COMMENT 'Kontrollu JSON ( kimlik bilgisi DEGIL — sadece yapılandırma; sır icermemeli)',
  `last_seen_at` timestamp NULL DEFAULT NULL COMMENT 'Son erisim (saglik)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_mounts_name` (`mount_name`),
  KEY `idx_mounts_type` (`storage_type`),
  KEY `idx_mounts_status` (`status`),
  KEY `idx_mounts_path` (`mount_path`(100))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Cekirdek mount/depolama kayit tablosu (OS mount table — §14 deployment modlari)';

CREATE TABLE `system_wifi_networks` (
  `id` binary(16) NOT NULL COMMENT 'Benzersiz tanimlayici (UUID v7)',
  `network_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ag adi',
  `network_type` enum('home','studio','car','public','guest') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'home' COMMENT 'Ag tipi',
  `security_type` enum('wpa2','wpa3','open','enterprise') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'wpa2' COMMENT 'Guvenlik tipi',
  `ssid` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SSID',
  `bssid` varchar(17) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'BSSID (MAC)',
  `channel` int DEFAULT NULL COMMENT 'Kanal numarasi',
  `signal_strength` int DEFAULT NULL COMMENT 'Sinyal gucu (dBm)',
  `frequency` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Frekans (2.4GHz/5GHz)',
  `is_auto_connect` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Otomomatik baglan?',
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Gizli ag mi?',
  `priority` int NOT NULL DEFAULT '0' COMMENT 'Oncelik',
  `metadata` json DEFAULT NULL COMMENT 'Ek veri (JSON)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Olusturma zamani (UTC)',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Guncelleme zamani (UTC)',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Silindi mi? (soft delete)',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)',
  PRIMARY KEY (`id`),
  KEY `idx_wifi_network_name` (`network_name`),
  KEY `idx_wifi_network_type` (`network_type`),
  KEY `idx_wifi_network_ssid` (`ssid`),
  KEY `idx_wifi_network_auto` (`is_auto_connect`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='WiFi ag bilgileri (ev, stüdyo, araba, herkese acik, misafir)';

SET FOREIGN_KEY_CHECKS = 1;
