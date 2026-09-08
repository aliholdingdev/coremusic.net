-- ============================================================================
-- CoreMusic — Seed Data (MySQL 9 Uyumlu)
-- ============================================================================
-- Bu dosya: Varsayılan roller, izinler ve admin kullanıcısını içerir
-- Sürüm: 1.0.0
-- Tarih: 2026-09-03
-- MySQL: 9.x uyumlu
-- ============================================================================
-- ÖNEMLİ:
--   - Bu dosya sadeceDevelopment/ staging ortamında çalıştırılmalı
--   - Production'da önce 001-initial-schema.sql çalıştırılmalı
--   - Şifre hash'i Argon2id ile hashlenmiş olmalı
--   - E-posta adresi parametrik olarak değiştirilebilir
-- ============================================================================

-- ============================================================================
-- 1. coremusic_auth — Varsayılan Roller
-- ============================================================================
USE `coremusic_auth`;

-- Roller (idempotent)
INSERT INTO `roles` (`id`, `name`, `display_name`, `description`, `is_active`, `is_deleted`)
VALUES
  ('a1b2c3d4-e5f6-7890-abcd-ef1234567801', 'admin', 'Yönetici', 'Sistem yöneticisi, tüm yetkilere sahip', 1, 0),
  ('a1b2c3d4-e5f6-7890-abcd-ef1234567802', 'user', 'Kullanıcı', 'Standart kullanıcı', 1, 0),
  ('a1b2c3d4-e5f6-7890-abcd-ef1234567803', 'premium', 'Premium Kullanıcı', 'Premium üyelik sahibi kullanıcı', 1, 0),
  ('a1b2c3d4-e5f6-7890-abcd-ef1234567804', 'studio', 'Stüdyo Kullanıcısı', 'Profesyonel stüdyo kullanıcısı', 1, 0),
  ('a1b2c3d4-e5f6-7890-abcd-ef1234567805', 'car', 'Araç Kullanıcısı', 'Araç içi bilgi-eğlence kullanıcısı', 1, 0)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;


-- ============================================================================
-- 2. coremusic_auth — Varsayılan İzinler
-- ============================================================================
-- Her modül için CRUD izinleri + özel eylemler

-- Kullanıcı yönetimi izinleri
INSERT INTO `permissions` (`id`, `module`, `action`, `display_name`, `description`, `is_active`, `is_deleted`)
VALUES
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567801', 'users', 'create', 'Kullanıcı Oluştur', 'Yeni kullanıcı hesabı oluşturabilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567802', 'users', 'read', 'Kullanıcı Görüntüle', 'Kullanıcı bilgilerini görüntüleyebilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567803', 'users', 'update', 'Kullanıcı Güncelle', 'Kullanıcı bilgilerini güncelleyebilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567804', 'users', 'delete', 'Kullanıcı Sil', 'Kullanıcı hesabını silebilir (soft delete)', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567805', 'users', 'list', 'Kullanıcı Listele', 'Kullanıcı listesini görüntüleyebilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567806', 'users', 'export', 'Kullanıcı Dışa Aktar', 'Kullanıcı verilerini dışa aktarabilir', 1, 0)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Medya yönetimi izinleri
INSERT INTO `permissions` (`id`, `module`, `action`, `display_name`, `description`, `is_active`, `is_deleted`)
VALUES
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567811', 'media', 'upload', 'Medya Yükle', 'Medya dosyası yükleyebilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567812', 'media', 'read', 'Medya Görüntüle', 'Medya dosyasını görüntüleyebilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567813', 'media', 'update', 'Medya Güncelle', 'Medya bilgilerini güncelleyebilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567814', 'media', 'delete', 'Medya Sil', 'Medya dosyasını silebilir (soft delete)', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567815', 'media', 'stream', 'Medya Akışı', 'Medya dosyasını akış halinde dinleyebilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567816', 'media', 'download', 'Medya İndir', 'Medya dosyasını indirebilir', 1, 0)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Çalma listesi izinleri
INSERT INTO `permissions` (`id`, `module`, `action`, `display_name`, `description`, `is_active`, `is_deleted`)
VALUES
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567821', 'playlists', 'create', 'Çalma Listesi Oluştur', 'Yeni çalma listesi oluşturabilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567822', 'playlists', 'read', 'Çalma Listesi Görüntüle', 'Çalma listesini görüntüleyebilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567823', 'playlists', 'update', 'Çalma Listesi Güncelle', 'Çalma listesini güncelleyebilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567824', 'playlists', 'delete', 'Çalma Listesi Sil', 'Çalma listesini silebilir (soft delete)', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567825', 'playlists', 'share', 'Çalma Listesi Paylaş', 'Çalma listesini paylaşabilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567826', 'playlists', 'collaborate', 'İşbirliği Yap', 'Çalma listesine işbirlikçi ekleyebilir', 1, 0)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- İndirme izinleri
INSERT INTO `permissions` (`id`, `module`, `action`, `display_name`, `description`, `is_active`, `is_deleted`)
VALUES
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567831', 'downloads', 'create', 'İndirme Başlat', 'İndirme işlemini başlatabilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567832', 'downloads', 'read', 'İndirme Görüntüle', 'İndirme durumunu görüntüleyebilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567833', 'downloads', 'cancel', 'İndirme İptal', 'İndirme işlemini iptal edebilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567834', 'downloads', 'retry', 'İndirme Yeniden Dene', 'Başarısız indirmeyi yeniden deneyebilir', 1, 0)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Sistem yapılandırması izinleri
INSERT INTO `permissions` (`id`, `module`, `action`, `display_name`, `description`, `is_active`, `is_deleted`)
VALUES
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567841', 'system', 'read', 'Sistem Ayarlarını Görüntüle', 'Sistem yapılandırmasını görüntüleyebilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567842', 'system', 'update', 'Sistem Ayarlarını Güncelle', 'Sistem yapılandırmasını güncelleyebilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567843', 'system', 'feature_flags', 'Özellik Bayraklarını Yönet', 'Özellik bayraklarını açıp kapatabilir', 1, 0)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Analitik izinleri
INSERT INTO `permissions` (`id`, `module`, `action`, `display_name`, `description`, `is_active`, `is_deleted`)
VALUES
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567851', 'analytics', 'read', 'Analitik Görüntüle', 'Analitik verilerini görüntüleyebilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567852', 'analytics', 'export', 'Analitik Dışa Aktar', 'Analitik verilerini dışa aktarabilir', 1, 0)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Cihaz yönetimi izinleri
INSERT INTO `permissions` (`id`, `module`, `action`, `display_name`, `description`, `is_active`, `is_deleted`)
VALUES
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567861', 'devices', 'register', 'Cihaz Kaydet', 'Yeni cihaz kaydedebilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567862', 'devices', 'read', 'Cihaz Görüntüle', 'Kayıtlı cihazları görüntüleyebilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567863', 'devices', 'revoke', 'Cihaz İptal', 'Cihaz erişimini iptal edebilir', 1, 0)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Bildirim izinleri
INSERT INTO `permissions` (`id`, `module`, `action`, `display_name`, `description`, `is_active`, `is_deleted`)
VALUES
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567871', 'notifications', 'read', 'Bildirim Görüntüle', 'Bildirimleri görüntüleyebilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567872', 'notifications', 'manage', 'Bildirim Yönetimi', 'Bildirim tercihlerini yönetebilir', 1, 0)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- API yönetimi izinleri
INSERT INTO `permissions` (`id`, `module`, `action`, `display_name`, `description`, `is_active`, `is_deleted`)
VALUES
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567881', 'api', 'create', 'API Anahtarı Oluştur', 'Yeni API anahtarı oluşturabilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567882', 'api', 'read', 'API Anahtarı Görüntüle', 'API anahtarlarını görüntüleyebilir', 1, 0),
  ('b1b2c3d4-e5f6-7890-abcd-ef1234567883', 'api', 'revoke', 'API Anahtarı İptal', 'API anahtarını iptal edebilir', 1, 0)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;


-- ============================================================================
-- 3. coremusic_auth — Rol-İzin Eşleştirmeleri
-- ============================================================================

-- Admin rolüne tüm izinleri ver
INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `is_deleted`)
SELECT
  UUID() AS `id`,
  'a1b2c3d4-e5f6-7890-abcd-ef1234567801' AS `role_id`,
  `id` AS `permission_id`,
  0 AS `is_deleted`
FROM `permissions`
WHERE `is_deleted` = 0
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- User rolüne temel izinleri ver
INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `is_deleted`)
SELECT
  UUID() AS `id`,
  'a1b2c3d4-e5f6-7890-abcd-ef1234567802' AS `role_id`,
  `id` AS `permission_id`,
  0 AS `is_deleted`
FROM `permissions`
WHERE `is_deleted` = 0
  AND (
    (`module` = 'users' AND `action` IN ('read', 'update'))
    OR (`module` = 'media' AND `action` IN ('read', 'stream'))
    OR (`module` = 'playlists' AND `action` IN ('create', 'read', 'update', 'delete'))
    OR (`module` = 'downloads' AND `action` IN ('create', 'read', 'cancel'))
    OR (`module` = 'devices' AND `action` IN ('register', 'read'))
    OR (`module` = 'notifications' AND `action` IN ('read', 'manage'))
  )
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Premium rolüne user + premium izinlerini ver
INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `is_deleted`)
SELECT
  UUID() AS `id`,
  'a1b2c3d4-e5f6-7890-abcd-ef1234567803' AS `role_id`,
  `id` AS `permission_id`,
  0 AS `is_deleted`
FROM `permissions`
WHERE `is_deleted` = 0
  AND (
    (`module` = 'users' AND `action` IN ('read', 'update'))
    OR (`module` = 'media' AND `action` IN ('read', 'stream', 'download'))
    OR (`module` = 'playlists' AND `action` IN ('create', 'read', 'update', 'delete', 'share'))
    OR (`module` = 'downloads' AND `action` IN ('create', 'read', 'cancel', 'retry'))
    OR (`module` = 'devices' AND `action` IN ('register', 'read'))
    OR (`module` = 'notifications' AND `action` IN ('read', 'manage'))
  )
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Studio rolüne premium + studio izinlerini ver
INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `is_deleted`)
SELECT
  UUID() AS `id`,
  'a1b2c3d4-e5f6-7890-abcd-ef1234567804' AS `role_id`,
  `id` AS `permission_id`,
  0 AS `is_deleted`
FROM `permissions`
WHERE `is_deleted` = 0
  AND (
    (`module` = 'users' AND `action` IN ('read', 'update'))
    OR (`module` = 'media' AND `action` IN ('upload', 'read', 'stream', 'download'))
    OR (`module` = 'playlists' AND `action` IN ('create', 'read', 'update', 'delete', 'share', 'collaborate'))
    OR (`module` = 'downloads' AND `action` IN ('create', 'read', 'cancel', 'retry'))
    OR (`module` = 'devices' AND `action` IN ('register', 'read'))
    OR (`module` = 'notifications' AND `action` IN ('read', 'manage'))
  )
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Car rolüne temel izinleri ver (daha sınırlı)
INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `is_deleted`)
SELECT
  UUID() AS `id`,
  'a1b2c3d4-e5f6-7890-abcd-ef1234567805' AS `role_id`,
  `id` AS `permission_id`,
  0 AS `is_deleted`
FROM `permissions`
WHERE `is_deleted` = 0
  AND (
    (`module` = 'users' AND `action` = 'read')
    OR (`module` = 'media' AND `action` IN ('read', 'stream'))
    OR (`module` = 'playlists' AND `action` IN ('read'))
    OR (`module` = 'downloads' AND `action` IN ('read'))
    OR (`module` = 'devices' AND `action` IN ('register', 'read'))
  )
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;


-- ============================================================================
-- 4. coremusic_auth — Varsayılan Admin Kullanıcısı
-- ============================================================================
-- Parametreler:
--   E-posta: admin@coremusic.net (değiştirilebilir)
--   Şifre: Hashlenmiş (Argon2id) — sadece example password
--   UUID: Sabit UUID (deterministik)

-- Admin kullanıcısı
INSERT INTO `users` (`id`, `email`, `password_hash`, `email_verified_at`, `status`, `is_deleted`)
VALUES
  ('c1b2c3d4-e5f6-7890-abcd-ef1234567801', 'admin@coremusic.net', '$argon2id$v=19$m=65536,t=4,p=2$c2FsdHNhbHRzYWx0$placeholder_hash_will_be_replaced', CURRENT_TIMESTAMP, 'active', 0)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Admin kullanıcısına admin rolünü ata
INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `assigned_at`, `is_deleted`)
VALUES
  ('d1b2c3d4-e5f6-7890-abcd-ef1234567801', 'c1b2c3d4-e5f6-7890-abcd-ef1234567801', 'a1b2c3d4-e5f6-7890-abcd-ef1234567801', CURRENT_TIMESTAMP, 0)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Admin profil bilgileri
INSERT INTO `coremusic_user`.`user_profiles` (`id`, `user_id`, `first_name`, `last_name`, `display_name`, `gender`, `country_code`, `language_code`, `timezone`, `is_deleted`)
VALUES
  ('e1b2c3d4-e5f6-7890-abcd-ef1234567801', 'c1b2c3d4-e5f6-7890-abcd-ef1234567801', 'Admin', 'User', 'Admin', 'prefer_not_to_say', 'TR', 'tr_TR', 'Europe/Istanbul', 0)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Admin tercihleri
INSERT INTO `coremusic_user`.`user_preferences` (`id`, `user_id`, `theme`, `language`, `notifications_email`, `notifications_push`, `notifications_sms`, `privacy_profile`, `playback_quality`, `auto_download`, `is_deleted`)
VALUES
  ('f1b2c3d4-e5f6-7890-abcd-ef1234567801', 'c1b2c3d4-e5f6-7890-abcd-ef1234567801', 'dark', 'tr', 1, 1, 0, 'private', 'high', 0, 0)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;


-- ============================================================================
-- 5. coremusic_config — Varsayılan Sistem Ayarları
-- ============================================================================
USE `coremusic_config`;

-- Genel sistem ayarları
INSERT INTO `system_config` (`id`, `config_key`, `config_value`, `config_type`, `category`, `description`, `is_public`, `is_sensitive`, `is_deleted`)
VALUES
  ('g1b2c3d4-e5f6-7890-abcd-ef1234567801', 'app.name', 'CoreMusic', 'string', 'general', 'Uygulama adı', 1, 0, 0),
  ('g1b2c3d4-e5f6-7890-abcd-ef1234567802', 'app.version', '1.0.0', 'string', 'general', 'Uygulama versiyonu', 1, 0, 0),
  ('g1b2c3d4-e5f6-7890-abcd-ef1234567803', 'app.url', 'https://coremusic.net', 'string', 'general', 'Uygulama URL adresi', 1, 0, 0),
  ('g1b2c3d4-e5f6-7890-abcd-ef1234567804', 'app.timezone', 'Europe/Istanbul', 'string', 'general', 'Varsayılan saat dilimi', 1, 0, 0),
  ('g1b2c3d4-e5f6-7890-abcd-ef1234567805', 'app.locale', 'tr_TR', 'string', 'general', 'Varsayılan yerel ayar', 1, 0, 0),
  ('g1b2c3d4-e5f6-7890-abcd-ef1234567806', 'app.debug', 'false', 'boolean', 'general', 'Hata ayıklama modu', 0, 0, 0),
  ('g1b2c3d4-e5f6-7890-abcd-ef1234567807', 'app.maintenance', 'false', 'boolean', 'general', 'Bakım modu', 1, 0, 0)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Güvenlik ayarları
INSERT INTO `system_config` (`id`, `config_key`, `config_value`, `config_type`, `category`, `description`, `is_public`, `is_sensitive`, `is_deleted`)
VALUES
  ('g1b2c3d4-e5f6-7890-abcd-ef1234567811', 'security.session_timeout', '3600', 'integer', 'security', 'Oturum zaman aşımı (saniye)', 0, 0, 0),
  ('g1b2c3d4-e5f6-7890-abcd-ef1234567812', 'security.max_login_attempts', '5', 'integer', 'security', 'Maksimum giriş denemesi', 0, 0, 0),
  ('g1b2c3d4-e5f6-7890-abcd-ef1234567813', 'security.lockout_duration', '900', 'integer', 'security', 'Hesap kilitleme süresi (saniye)', 0, 0, 0),
  ('g1b2c3d4-e5f6-7890-abcd-ef1234567814', 'security.password_min_length', '8', 'integer', 'security', 'Minimum şifre uzunluğu', 0, 0, 0),
  ('g1b2c3d4-e5f6-7890-abcd-ef1234567815', 'security.require_email_verification', 'true', 'boolean', 'security', 'E-posta doğrulama zorunlu mu', 0, 0, 0)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- İndirme ayarları
INSERT INTO `system_config` (`id`, `config_key`, `config_value`, `config_type`, `category`, `description`, `is_public`, `is_sensitive`, `is_deleted`)
VALUES
  ('g1b2c3d4-e5f6-7890-abcd-ef1234567821', 'download.max_concurrent', '3', 'integer', 'download', 'Eşzamanlı maksimum indirme sayısı', 0, 0, 0),
  ('g1b2c3d4-e5f6-7890-abcd-ef1234567822', 'download.max_retries', '3', 'integer', 'download', 'Maksimum yeniden deneme sayısı', 0, 0, 0),
  ('g1b2c3d4-e5f6-7890-abcd-ef1234567823', 'download.chunk_size', '1048576', 'integer', 'download', 'Parça boyutu (byte)', 0, 0, 0),
  ('g1b2c3d4-e5f6-7890-abcd-ef1234567824', 'download.allowed_formats', '["flac","mp3","wav","aac"]', 'json', 'download', 'İzin verilen formatlar', 0, 0, 0)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Média ayarları
INSERT INTO `system_config` (`id`, `config_key`, `config_value`, `config_type`, `category`, `description`, `is_public`, `is_sensitive`, `is_deleted`)
VALUES
  ('g1b2c3d4-e5f6-7890-abcd-ef1234567831', 'media.max_upload_size', '104857600', 'integer', 'media', 'Maksyük yükleme boyutu (byte, 100MB)', 0, 0, 0),
  ('g1b2c3d4-e5f6-7890-abcd-ef1234567832', 'media.allowed_mime_types', '["audio/mpeg","audio/flac","audio/wav","audio/aac","audio/ogg"]', 'json', 'media', 'İzin verilen MIME türleri', 0, 0, 0),
  ('g1b2c3d4-e5f6-7890-abcd-ef1234567833', 'media.cover_art_max_size', '5242880', 'integer', 'media', 'Kapak görseli maks boyutu (byte, 5MB)', 0, 0, 0)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;

-- Özellik bayrakları
INSERT INTO `feature_flags` (`id`, `flag_key`, `display_name`, `description`, `is_enabled`, `rollout_percentage`, `target_audience`, `is_deleted`)
VALUES
  ('h1b2c3d4-e5f6-7890-abcd-ef1234567801', 'ai_recommendations', 'AI Önerileri', 'AI destekli müzik öneri sistemi', 0, 0, 'none', 0),
  ('h1b2c3d4-e5f6-7890-abcd-ef1234567802', 'social_features', 'Sosyal Özellikler', 'Sosyal dinleme odaları ve paylaşım', 0, 0, 'none', 0),
  ('h1b2c3d4-e5f6-7890-abcd-ef1234567803', 'podcast_support', 'Podcast Desteği', 'Podcast dinleme ve abonelik', 0, 0, 'none', 0),
  ('h1b2c3d4-e5f6-7890-abcd-ef1234567804', 'radio_support', 'Radyo Desteği', 'Radyo istasyonu dinleme', 0, 0, 'none', 0),
  ('h1b2c3d4-e5f6-7890-abcd-ef1234567805', 'video_support', 'Video Desteği', 'Müzik videosu oynatma', 0, 0, 'none', 0),
  ('h1b2c3d4-e5f6-7890-abcd-ef1234567806', 'car_mode', 'Araç Modu', 'Araç içi bilgi-eğlence modu', 0, 0, 'none', 0),
  ('h1b2c3d4-e5f6-7890-abcd-ef1234567807', 'studio_mode', 'Stüdyo Modu', 'Profesyonel stüdyo özellikleri', 0, 0, 'none', 0),
  ('h1b2c3d4-e5f6-7890-abcd-ef1234567808', 'offline_mode', 'Çevrimdışı Mod', 'Çevrimdışı erişim ve senkronizasyon', 0, 0, 'none', 0)
ON DUPLICATE KEY UPDATE `updated_at` = CURRENT_TIMESTAMP;


-- ============================================================================
-- SON
-- ============================================================================
