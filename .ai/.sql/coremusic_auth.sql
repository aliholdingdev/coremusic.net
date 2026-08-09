-- ══════════════════════════════════════════════════════════════════════════════
-- coremusic_auth — KİMLİK DOĞRULAMA & YETKİLENDİRME TABLOLARI
-- COREMUSIC DB v6.0 | Mart 2026 | MySQL 8.x InnoDB | utf8mb4_turkish_ci
-- NF: BCNF
-- ══════════════════════════════════════════════════════════════════════════════
--
-- [v5 → v6 DEĞİŞİKLİKLER]
-- 1. [YENİ] user_roles             — Kullanıcı rol tanımları (admin_roles ile paralel)
-- 2. [YENİ] user_permissions       — Granüler kullanıcı izinleri (feature-based RBAC)
-- 3. [YENİ] user_role_permissions  — Rol ↔ İzin M2M (admin_role_permissions ile paralel)
-- 4. [YENİ] user_assigned_roles    — Kullanıcı ↔ Rol M2M (admin_user_roles ile paralel)
-- 5. [DÜZELTMESİ] INSERT INTO user_roles: // yorum operatörü kaldırıldı (SQL geçersiz söz dizimi)
-- 6. [DÜZELTMESİ] INSERT INTO user_permissions eklendi (v5'te yalnızca admin_permissions vardı)
--
-- [v5.1 — korunan]
-- 7. admin_sessions — Admin oturum yönetimi (kısa TTL, IP kilidi)
--
-- ══════════════════════════════════════════════════════════════════════════════
--
-- KULLANICI RBAC MİMARİSİ
-- ════════════════════════════════════════════════════════════════════
--
-- users
--   └── user_assigned_roles (M2M)
--         └── user_roles
--               └── user_role_permissions (M2M)
--                     └── user_permissions
--
-- Rol hiyerarşisi (kapsayıcı):
--   ultra_user     → tüm izinler
--   premium_user   → akış + yüksek kalite + cihaz + playlist + lirik
--   streaming_user → Bluetooth/cihaz akışı + offline
--   panel_user     → sadece kullanıcı paneli erişimi
--   free_user      → temel akış + playlist + lirik
--
-- İzin alanları (permission_key format: {alan}.{aksiyon}):
--   stream.*       → Ses/video akışı kalite ve türleri
--   device.*       → Cihaz/offline/bluetooth
--   playlist.*     → Playlist oluşturma ve işbirliği
--   lyrics.*       → Sözler (düz metin ve LRC senkron)
--   equalizer.*    → Ekolayzer erişimi
--   content.*      → İçerik filtresi (explicit vb.)
--   history.*      → Dinleme geçmişi kapsamı
--   ai.*           → AI terminal erişimi (footer AI Terminal butonu)
--   download.*     → Local mod indirme
--
-- ══════════════════════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS coremusic_auth
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_turkish_ci;

USE coremusic_auth;


-- ──────────────────────────────────────────────────────────────────────────────
-- USERS — Ana kullanıcı tablosu
-- Normal Form : BCNF
-- ⚠️ --  password_hash: Argon2id (RFC 9106) MANDATORY. See security-standards.md for parameters. (cost ≥ 12).
--    MD5 / SHA-1 / SHA-256 doğrudan şifre hash olarak YASAK.
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE users (
    id                  INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    username            VARCHAR(50)         NOT NULL,
    email               VARCHAR(255)        NOT NULL,
    password_hash       VARCHAR(512)        NOT NULL,   -- Argon2id (RFC 9106)
    gender              VARCHAR(10)             NULL,   -- male | female | neutral
    display_name        VARCHAR(100)            NULL,
    avatar_url          VARCHAR(2048)           NULL,
    bio                 TEXT                    NULL,
    country_code        CHAR(2)                 NULL,   -- ISO 3166-1: TR, US, GB
    language_code       CHAR(5)             NOT NULL DEFAULT 'tr',
    account_type        VARCHAR(20)         NOT NULL DEFAULT 'free',
    -- account_type: free | premium | family | trial
    is_email_verified   TINYINT(1)          NOT NULL DEFAULT 0,
    is_active           TINYINT(1)          NOT NULL DEFAULT 1,
    is_banned           TINYINT(1)          NOT NULL DEFAULT 0,
    ban_reason          TEXT                    NULL,
    banned_until        DATETIME                NULL,
    last_login_at       DATETIME                NULL,

    created_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON UPDATE CURRENT_TIMESTAMP,
    deleted_at          DATETIME                NULL DEFAULT NULL,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_users_email      (email),
    UNIQUE  KEY uq_users_username   (username),

    INDEX idx_u_active      (is_active),
    INDEX idx_u_banned      (is_banned),
    INDEX idx_u_deleted     (deleted_at),
    INDEX idx_u_type        (account_type),
    INDEX idx_u_gender      (gender),
    INDEX idx_u_country     (country_code),
    INDEX idx_u_login       (last_login_at),
    FULLTEXT KEY ft_u_name  (display_name),

    CHECK (account_type IN ('free','premium','family','trial')),
    CHECK (gender IS NULL OR gender IN ('male','female','neutral'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- USER_TOKENS — Email DOĞRULAMA, şifre sıfırlama, remember_me tokenları
-- Normal Form : BCNF
-- ⚠️ GÜVENLİK: DB'ye token'ın SHA-256 hash'i saklanır, ham token değil.
--    $rawToken = bin2hex(random_bytes(32));
--    $hash     = hash('sha256', $rawToken);
--    Email'e gönderilen: $rawToken (link içinde, tek sefer)
--    DB'ye yazılan: $hash
--    DOĞRULAMA: hash('sha256', $_GET['token']) === $storedHash
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE user_tokens (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED        NOT NULL,
    token_type      VARCHAR(30)         NOT NULL,
    -- email_verify | password_reset | remember_me | api_token | 2fa_backup
    token_hash      CHAR(64)            NOT NULL,   -- SHA-256 hex
    expires_at      DATETIME            NOT NULL,
    used_at         DATETIME                NULL,   -- NULL = kullanılmamış (geçerli)
    ip_address      VARCHAR(45)             NULL,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    FOREIGN KEY fk_ut_user  (user_id) REFERENCES users (id) ON DELETE CASCADE,

    UNIQUE  KEY uq_ut_hash      (token_hash),
    INDEX idx_ut_user           (user_id),
    INDEX idx_ut_type           (token_type),
    INDEX idx_ut_expires        (expires_at),

    CHECK (token_type IN ('email_verify','password_reset','remember_me','api_token','2fa_backup'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- USER_SESSIONS — DB-backed kullanıcı oturum yönetimi
-- Normal Form : BCNF
-- SessionManager.php → include/class/system.security/SessionManager.php
-- config.php → SESSION_NAME = 'COREMUSIC_SESSION', SESSION_LIFETIME = 7200
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE user_sessions (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED        NOT NULL,
    session_token   VARCHAR(128)        NOT NULL,   -- PHP session ID
    ip_address      VARCHAR(45)             NULL,
    user_agent      VARCHAR(512)            NULL,
    device_type     VARCHAR(20)             NULL,
    -- web | mobile | desktop | tablet
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,
    last_activity   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    expires_at      DATETIME            NOT NULL,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    FOREIGN KEY fk_us_user  (user_id) REFERENCES users (id) ON DELETE CASCADE,

    UNIQUE  KEY uq_us_token     (session_token),
    INDEX idx_us_user           (user_id),
    INDEX idx_us_active         (is_active, expires_at),
    INDEX idx_us_last           (last_activity),

    CHECK (device_type IN ('web','mobile','desktop','tablet'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- USER_ROLES — Kullanıcı rol tanımları
-- Normal Form : BCNF
-- Bağımlılık  : id → {role_name, display_name, description, is_active}
-- Admin_roles ile paralel yapı — farklı kapsam (kullanıcı özellikleri)
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE user_roles (
    id           TINYINT UNSIGNED    NOT NULL AUTO_INCREMENT,
    role_name    VARCHAR(50)         NOT NULL,
    display_name VARCHAR(100)            NULL,   -- Kullanıcıya gösterilen ad
    description  VARCHAR(255)            NULL,
    is_active    TINYINT(1)          NOT NULL DEFAULT 1,

    created_at   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                     ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_ur_name  (role_name),
    INDEX idx_ur_active     (is_active)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- USER_PERMISSIONS — Granüler kullanıcı izin tanımları
-- Normal Form : BCNF
-- permission_key format: {alan}.{aksiyon}
--   Alanlar: stream | device | playlist | lyrics | equalizer | content | history | ai | download
--   Örnekler: stream.audio | stream.hd | device.export | ai.terminal
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE user_permissions (
    id              SMALLINT UNSIGNED   NOT NULL AUTO_INCREMENT,
    permission_key  VARCHAR(100)        NOT NULL,
    display_name    VARCHAR(100)            NULL,   -- Türkçe gösterim
    description     VARCHAR(255)            NULL,
    module          VARCHAR(50)             NULL,
    -- stream | device | playlist | lyrics | equalizer | content | history | ai | download

    PRIMARY KEY (id),
    UNIQUE  KEY uq_up_key   (permission_key),
    INDEX idx_up_module     (module)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- USER_ROLE_PERMISSIONS — Kullanıcı Rol ↔ İzin (M2M)
-- Normal Form : BCNF — (role_id, permission_id) composite PK
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE user_role_permissions (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    role_id         TINYINT UNSIGNED    NOT NULL,
    permission_id   SMALLINT UNSIGNED   NOT NULL,

    PRIMARY KEY (id),
    
    UNIQUE KEY uq_urp_role_perm (role_id, permission_id),
    FOREIGN KEY fk_urp_role (role_id)       REFERENCES user_roles       (id) ON DELETE CASCADE,
    FOREIGN KEY fk_urp_perm (permission_id) REFERENCES user_permissions  (id) ON DELETE CASCADE,
    INDEX idx_urp_perm (permission_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- USER_ASSIGNED_ROLES — Kullanıcı ↔ Rol (M2M)
-- Normal Form : BCNF — (user_id, role_id) composite PK
-- admin_user_roles ile paralel yapı
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE user_assigned_roles (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id     INT UNSIGNED        NOT NULL,
    role_id     TINYINT UNSIGNED    NOT NULL,
    assigned_at DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    assigned_by INT UNSIGNED            NULL,   -- Atamayı yapan admin.id (cross-DB)
    expires_at  DATETIME                NULL,   -- NULL = süresiz, DATETIME = süreli yetki

    PRIMARY KEY (id),
    UNIQUE KEY uq_uar_user_role (user_id, role_id),
    FOREIGN KEY fk_uar_user (user_id) REFERENCES users       (id) ON DELETE CASCADE,
    FOREIGN KEY fk_uar_role (role_id) REFERENCES user_roles  (id) ON DELETE CASCADE,

    INDEX idx_uar_role      (role_id),
    INDEX idx_uar_expires   (expires_at)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- ADMINS — Admin hesapları
-- Normal Form : BCNF
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE admins (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    username        VARCHAR(50)         NOT NULL,
    email           VARCHAR(255)        NOT NULL,
    password_hash   VARCHAR(512)        NOT NULL,   -- Argon2id (RFC 9106)
    display_name    VARCHAR(100)            NULL,
    avatar_url      VARCHAR(2048)           NULL,
    custom_theme    LONGTEXT                NULL,
    -- JSON custom tema renkleri. Format: {"primary":"#1a1a2e","accent":"#e94560",...}
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,
    last_login_at   DATETIME                NULL,

    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    deleted_at      DATETIME                NULL DEFAULT NULL,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_admin_email      (email),
    UNIQUE  KEY uq_admin_username   (username),

    INDEX idx_a_active  (is_active),
    INDEX idx_a_deleted (deleted_at),
    INDEX idx_a_login   (last_login_at)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- ADMIN_SESSIONS — Admin oturum yönetimi (user_sessions'dan ayrı)
-- Normal Form : BCNF
-- ⚠️ ip_lock: 1 ise bu oturum sadece ip_created_with'ten kullanılabilir.
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE admin_sessions (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    admin_id        INT UNSIGNED        NOT NULL,
    session_token   VARCHAR(128)        NOT NULL,
    ip_created_with VARCHAR(45)             NULL,
    ip_lock         TINYINT(1)          NOT NULL DEFAULT 1,
    user_agent      VARCHAR(512)            NULL,
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,
    last_activity   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    expires_at      DATETIME            NOT NULL,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    FOREIGN KEY fk_as_admin (admin_id) REFERENCES admins (id) ON DELETE CASCADE,

    UNIQUE  KEY uq_as_token     (session_token),
    INDEX idx_as_admin          (admin_id),
    INDEX idx_as_active         (is_active, expires_at),
    INDEX idx_as_last           (last_activity)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- ADMIN_ROLES — Rol tanımları (lookup)
-- Normal Form : BCNF
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE admin_roles (
    id          TINYINT UNSIGNED    NOT NULL AUTO_INCREMENT,
    role_name   VARCHAR(50)         NOT NULL,
    description VARCHAR(255)            NULL,
    is_active   TINYINT(1)          NOT NULL DEFAULT 1,

    created_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                    ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_ar_name (role_name),
    INDEX idx_ar_active    (is_active)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- ADMIN_PERMISSIONS — Granüler izin tanımları
-- Normal Form : BCNF
-- permission_key format: {module}.{action}
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE admin_permissions (
    id              SMALLINT UNSIGNED   NOT NULL AUTO_INCREMENT,
    permission_key  VARCHAR(100)        NOT NULL,
    description     VARCHAR(255)            NULL,
    module          VARCHAR(50)             NULL,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_ap_key   (permission_key),
    INDEX idx_ap_module     (module)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- ADMIN_ROLE_PERMISSIONS — Rol ↔ İzin (M2M)
-- Normal Form : BCNF — (role_id, permission_id) composite PK
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE admin_role_permissions (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    role_id         TINYINT UNSIGNED    NOT NULL,
    permission_id   SMALLINT UNSIGNED   NOT NULL,

    PRIMARY KEY (id),
    UNIQUE KEY uq_arp_role_perm (role_id, permission_id),
    FOREIGN KEY fk_arp_role (role_id)       REFERENCES admin_roles       (id) ON DELETE CASCADE,
    FOREIGN KEY fk_arp_perm (permission_id) REFERENCES admin_permissions  (id) ON DELETE CASCADE,
    
    INDEX idx_arp_perm (permission_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- ADMIN_USER_ROLES — Admin ↔ Rol (M2M)
-- Normal Form : BCNF — (admin_id, role_id) composite PK
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE admin_user_roles (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    admin_id    INT UNSIGNED        NOT NULL,
    role_id     TINYINT UNSIGNED    NOT NULL,
    assigned_at DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    assigned_by INT UNSIGNED            NULL,

    PRIMARY KEY (id),
    UNIQUE KEY uq_aur_admin_role (admin_id, role_id),
    FOREIGN KEY fk_aur_admin (admin_id) REFERENCES admins      (id) ON DELETE CASCADE,
    FOREIGN KEY fk_aur_role  (role_id)  REFERENCES admin_roles (id) ON DELETE CASCADE,
    
    INDEX idx_aur_role (role_id)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ══════════════════════════════════════════════════════════════════════════════
-- VARSAYILAN VERİLER
-- ══════════════════════════════════════════════════════════════════════════════

-- ── Admin rolleri ──────────────────────────────────────────────────────────────
INSERT INTO admin_roles (role_name, description) VALUES
    ('root',            'Tam yetki — tüm modüller & tüm sistemler'),
    ('super_admin',     'Tam yetki — tüm modüller'),
    ('content_editor',  'Müzik, albüm, playlist içerik yönetimi'),
    ('moderator',       'Kullanıcı yönetimi, ban, şikayet'),
    ('data_analyst',    'Sadece okuma — istatistik ve raporlar'),
    ('support',         'Kullanıcı destek, şikayet görüntüleme');

-- ── Kullanıcı rolleri ──────────────────────────────────────────────────────────
INSERT INTO user_roles (role_name, display_name, description) VALUES
    ('ultra_user',     'Ultra Kullanıcı', 'Tam paket — tüm özellikler ve modüller'),
    ('premium_user',   'Premium Kullanıcı','Orta paket — yüksek kalite akış, cihaz, gelişmiş özellikler'),
    ('streaming_user', 'Yayın Kullanıcısı','Bluetooth / cihaz streaming, offline indirme desteği'),
    ('panel_user',     'Panel Kullanıcısı','Kullanıcı paneli erişimi ve yönetim özellikleri'),
    ('free_user',      'Ücretsiz Kullanıcı','Temel paket — standart ses kalitesi, temel özellikler');

-- ── Admin izinleri ─────────────────────────────────────────────────────────────
INSERT INTO admin_permissions (permission_key, description, module) VALUES
    ('music.view',       'Müzik listesini görüntüle',   'music'),
    ('music.create',     'Yeni müzik ekle',              'music'),
    ('music.edit',       'Müzik bilgilerini düzenle',    'music'),
    ('music.delete',     'Müzik sil',                    'music'),
    ('album.view',       'Albüm listesini görüntüle',    'album'),
    ('album.create',     'Yeni albüm ekle',              'album'),
    ('album.edit',       'Albüm bilgilerini düzenle',    'album'),
    ('album.delete',     'Albüm sil',                    'album'),
    ('playlist.view',    'Playlist listesini görüntüle', 'playlist'),
    ('playlist.edit',    'Playlist düzenle',             'playlist'),
    ('playlist.delete',  'Playlist sil',                 'playlist'),
    ('singer.view',      'Sanatçı listesini görüntüle',  'singer'),
    ('singer.create',    'Yeni sanatçı ekle',            'singer'),
    ('singer.edit',      'Sanatçı bilgilerini düzenle',  'singer'),
    ('singer.delete',    'Sanatçı sil',                  'singer'),
    ('user.view',        'Kullanıcı listesini görüntüle','user'),
    ('user.ban',         'Kullanıcı banla / banı kaldır','user'),
    ('user.delete',      'Kullanıcı hesabını sil',       'user'),
    ('system.config',    'Sistem ayarlarını değiştir',   'system'),
    ('system.logs',      'Log kayıtlarını görüntüle',    'system'),
    ('system.backup',    'Yedek al / geri yükle',        'system');

-- ── Kullanıcı izinleri ─────────────────────────────────────────────────────────
INSERT INTO user_permissions (permission_key, display_name, description, module) VALUES
    -- Akış kalitesi
    ('stream.audio',          'Standart Ses Akışı',    'Standart kalitede ses akışı (≤192kbps)',      'stream'),
    ('stream.hd',             'HD Ses Akışı',          'Yüksek kalitede ses akışı (≥320kbps)',        'stream'),
    ('stream.lossless',       'Kayıpsız Ses Akışı',    'FLAC / lossless formatında akış',             'stream'),
    ('stream.video',          'Video Akışı',           'Müzik videosu izleme',                        'stream'),
    -- Cihaz / offline
    ('device.export',         'Cihaza Aktar',          'USB/SD/disk cihazlarına playlist aktarımı',   'device'),
    ('device.bluetooth',      'Bluetooth Akışı',       'Bluetooth cihazlara akış',                    'device'),
    ('device.offline',        'Çevrimdışı Dinleme',    'Offline/cihaz playlist oluşturma ve dinleme', 'device'),
    -- Playlist
    ('playlist.create',       'Playlist Oluştur',      'Kişisel playlist oluşturma',                  'playlist'),
    ('playlist.collaborate',  'Ortak Playlist',        'Ortak düzenlenen playlist oluşturma',         'playlist'),
    -- Sözler
    ('lyrics.view',           'Sözleri Gör',           'şarkı sözlerini görüntüleme',                 'lyrics'),
    ('lyrics.sync',           'Senkron Sözler (LRC)',  'Karaoke/LRC senkronize sözleri görme',        'lyrics'),
    -- Ekolayzer
    ('equalizer.basic',       'Temel EQ',              'Sistem EQ presetlerini kullanma',             'equalizer'),
    ('equalizer.advanced',    'Gelismis EQ',            'Ozel EQ olusturma ve duzenleme',              'equalizer'),
    -- İçerik
    ('content.explicit',      'Açık İçerik',           'Explicit/18+ içerikleri görüntüleme',         'content'),
    -- Geçmiş
    ('history.basic',         'Temel Geçmiş',          'Son 30 günlük dinleme geçmişi',               'history'),
    ('history.full',          'Tam Geçmiş',            'Sınırsız dinleme geçmişi ve istatistikler',   'history'),
    -- AI / Panel
    ('ai.terminal',           'AI Terminal',           'Footer AI Terminal popup erişimi',            'ai'),
    ('panel.access',          'Panel Erişimi',         'Kullanıcı panel sayfasına erişim',            'panel'),
    -- Local mod
    ('download.local',        'Yerel İndirme',         'Local modda dosya indirme ve tarama',         'download');

-- ── Kullanıcı rol → izin atamaları ───────────────────────────────────────────
-- free_user (id=5): temel akış + playlist + temel sözler + temel EQ + temel geçmiş + panel
INSERT INTO user_role_permissions (role_id, permission_id)
SELECT 5, id FROM user_permissions
WHERE permission_key IN (
    'stream.audio',
    'playlist.create',
    'lyrics.view',
    'equalizer.basic',
    'history.basic',
    'panel.access'
);

-- premium_user (id=2): free + HD akış + video + cihaz export + offline + senkron sözler + gelişmiş EQ + tam geçmiş + explicit
INSERT INTO user_role_permissions (role_id, permission_id)
SELECT 2, id FROM user_permissions
WHERE permission_key IN (
    'stream.audio','stream.hd','stream.video',
    'device.export','device.offline',
    'playlist.create','playlist.collaborate',
    'lyrics.view','lyrics.sync',
    'equalizer.basic','equalizer.advanced',
    'content.explicit',
    'history.basic','history.full',
    'panel.access'
);

-- streaming_user (id=3): ses akış + bluetooth + offline + cihaz export + temel özellikler
INSERT INTO user_role_permissions (role_id, permission_id)
SELECT 3, id FROM user_permissions
WHERE permission_key IN (
    'stream.audio','stream.hd',
    'device.export','device.bluetooth','device.offline',
    'playlist.create',
    'lyrics.view',
    'equalizer.basic',
    'history.basic',
    'panel.access'
);

-- panel_user (id=4): yalnızca panel + temel akış
INSERT INTO user_role_permissions (role_id, permission_id)
SELECT 4, id FROM user_permissions
WHERE permission_key IN (
    'stream.audio',
    'panel.access'
);

-- ultra_user (id=1): tüm izinler
INSERT INTO user_role_permissions (role_id, permission_id)
SELECT 1, id FROM user_permissions;


