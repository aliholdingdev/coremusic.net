-- ══════════════════════════════════════════════════════════════════════════════
-- coremusic_system — SİSTEM AYARLARI, DOSYA YÖNETİCİSİ & BİLDİRİM TABLOLARI
-- COREMUSIC DB v5.0 | Mart 2026 | MySQL 8.x InnoDB | utf8mb4_turkish_ci
-- NF: BCNF
-- ══════════════════════════════════════════════════════════════════════════════
--
-- [v5 YENİ DB — NEDEN AYRI?]
-- Proje dizini incelendi:
--   - assets/css/settings/settings.audio.css     → Ses kalitesi / cihaz ayarları
--   - assets/css/settings/settings.equalizer.css → Ekolayzer
--   - assets/css/settings/settings.theme.css     → Tema
--   - assets/css/settings/settings.system.css    → Sistem ayarları
--   - assets/css/modules/filemanager.css         → Dosya yöneticisi (local mod)
--   - assets/css/modules/wifi/                   → Wi-Fi modülü (local mod)
--   - pages/file_manager/                        → Dosya yöneticisi sayfası (boş)
-- Bunların hiçbiri için mevcut 7 DB'de tablo yoktu.
--
-- TABLOLAR:
--   user_settings            → Kullanıcı başına ayarlar (K-V)
--   user_equalizer_presets   → Ekolayzer preset'leri
--   app_settings             → Global uygulama ayarları (K-V)
--   file_manager_entries     → Local mod: dosya yöneticisi indeksi
--   notifications            → Kullanıcı bildirimleri
--   system_wifi_networks     → Local mod: kayıtlı Wi-Fi ağları
--
-- LOCAL MOD vs SERVER MOD:
--   file_manager_entries, system_wifi_networks → local mod only
--   Diğerleri → her iki mod
-- ══════════════════════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS coremusic_system
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_turkish_ci;

USE coremusic_system;


-- ──────────────────────────────────────────────────────────────────────────────
-- USER_SETTINGS — Kullanıcı ayarları (K-V, şema-esnek)
-- Normal Form : BCNF
-- Bağımlılık  : (user_id, setting_key) → {setting_value, updated_at}
--
-- setting_key örnekleri:
--   Ses   : audio.quality (low|medium|high|lossless), audio.output_device, audio.normalize
--   Tema  : theme.mode (dark|light|system), theme.accent_color, theme.font_size
--   Oynat : playback.autoplay, playback.crossfade_sec, playback.gapless
--   Genel : general.language, general.notifications_enabled, general.show_explicit
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE user_settings (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED        NOT NULL,   -- → coremusic_auth.users.id
    setting_key     VARCHAR(100)        NOT NULL,
    setting_value   TEXT                    NULL,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_us_pair  (user_id, setting_key),
    -- ⚠️ user_id → coremusic_auth DB: cross-DB FK yok. Uygulama doğrulaması.

    INDEX idx_uset_user (user_id),
    INDEX idx_uset_key  (setting_key)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- USER_EQUALIZER_PRESETS — Kullanıcı ekolayzer preset'leri
-- Normal Form : BCNF
-- Bağımlılık  : id → {user_id, preset_name, bands_json, is_active}
--
-- bands_json format (10 bant standart EQ):
-- [
--   {"hz":32,"gain":0},{"hz":64,"gain":2},{"hz":125,"gain":0},
--   {"hz":250,"gain":-1},{"hz":500,"gain":0},{"hz":1000,"gain":1},
--   {"hz":2000,"gain":2},{"hz":4000,"gain":0},{"hz":8000,"gain":-1},
--   {"hz":16000,"gain":0}
-- ]
-- gain değerleri: -12.0 ile +12.0 dB arası (0.5 adımlarla)
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE user_equalizer_presets (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED        NOT NULL,   -- → coremusic_auth.users.id
    preset_name     VARCHAR(100)        NOT NULL,
    bands_json      JSON                NOT NULL,   -- EQ band değerleri
    is_active       TINYINT(1)          NOT NULL DEFAULT 0,
    -- is_active: 1 = şu an aktif preset (kullanıcı başına max 1 olmalı — uygulama zorlar)
    is_system       TINYINT(1)          NOT NULL DEFAULT 0,
    -- is_system: 1 = sistem tanımlı (Flat, Rock, Pop...), user_id = 0
    sort_order      TINYINT UNSIGNED    NOT NULL DEFAULT 0,

    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    -- ⚠️ user_id → coremusic_auth DB: cross-DB FK yok.

    INDEX idx_ueq_user      (user_id),
    INDEX idx_ueq_active    (user_id, is_active),
    INDEX idx_ueq_system    (is_system)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- APP_SETTINGS — Global uygulama ayarları (K-V, tek satır per key)
-- Normal Form : BCNF
-- Bağımlılık  : setting_key → {setting_value, setting_type, description}
--
-- setting_type: string | integer | boolean | json
-- Örnekler:
--   app.version, app.maintenance_mode, app.max_upload_mb,
--   server.streaming_domain, server.cdn_url,
--   local.music_scan_path, local.default_quality
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE app_settings (
    id              SMALLINT UNSIGNED   NOT NULL AUTO_INCREMENT,
    setting_key     VARCHAR(100)        NOT NULL,
    setting_value   TEXT                    NULL,
    setting_type    VARCHAR(20)         NOT NULL DEFAULT 'string',
    -- string | integer | boolean | json
    description     VARCHAR(255)            NULL,
    is_public       TINYINT(1)          NOT NULL DEFAULT 0,
    -- is_public: 1 = frontend JS'e gönderilebilir (hassas değil)
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    updated_by      INT UNSIGNED            NULL,   -- → coremusic_auth.admins.id

    PRIMARY KEY (id),
    UNIQUE  KEY uq_as_key   (setting_key),
    INDEX idx_as_public     (is_public),

    CHECK (setting_type IN ('string','integer','boolean','json'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- FILE_MANAGER_ENTRIES — Local mod dosya yöneticisi indeksi
-- Normal Form : BCNF
-- Bağımlılık  : id → {file_path, file_name, file_size, mime_type, ...}
--
-- ⚠️ LOCAL MOD ONLY — server modda kullanılmaz.
-- Bu tablo yerel dosya sisteminin indekslenmesidir.
-- music.coremusic.net/pages/file_manager/ sayfası için.
--
-- entry_type değerleri:
--   audio    → .mp3, .flac, .wav, .aac
--   video    → .mp4, .mkv, .avi
--   image    → .jpg, .png, .webp (cover art)
--   folder   → Klasör girişi
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE file_manager_entries (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED        NOT NULL,   -- Hangi kullanıcının taraması
    entry_type      VARCHAR(20)         NOT NULL DEFAULT 'audio',
    -- entry_type: audio | video | image | folder
    file_path       VARCHAR(2048)       NOT NULL,   -- Tam dosya yolu (C:\Music\... veya /media/...)
    file_name       VARCHAR(512)        NOT NULL,
    file_ext        VARCHAR(20)             NULL,   -- mp3, flac, wav, mp4 ...
    file_size       BIGINT UNSIGNED         NULL,   -- BYTE (dönüşüm app'te)
    mime_type       VARCHAR(100)            NULL,   -- audio/mpeg, audio/flac, video/mp4
    duration_sec    INT UNSIGNED            NULL,   -- Ses/video süresi
    -- ID3 / metadata sütunları (dosyadan okunur, DB'ye kopyalanır)
    meta_title      VARCHAR(500)            NULL,
    meta_artist     VARCHAR(255)            NULL,
    meta_album      VARCHAR(255)            NULL,
    meta_year       SMALLINT UNSIGNED       NULL,
    meta_genre      VARCHAR(100)            NULL,
    local_star_count DECIMAL(2,1)       NOT NULL DEFAULT 0.0,
    -- Linkli müzik (DB'ye tanımlanmışsa)
    linked_music_id INT UNSIGNED            NULL,   -- → coremusic_musics.musics.id
    scan_status     VARCHAR(20)         NOT NULL DEFAULT 'pending',
    -- scan_status: pending | indexed | error | skipped
    scan_error      VARCHAR(500)            NULL,
    checksum_sha256 CHAR(64)                NULL,   -- Duplicate tespiti
    last_scanned_at DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_fme_path     (user_id, file_path(500)),

    INDEX idx_fme_user          (user_id),
    INDEX idx_fme_type          (entry_type),
    INDEX idx_fme_status        (scan_status),
    INDEX idx_fme_linked        (linked_music_id),
    INDEX idx_fme_ext           (file_ext),
    INDEX idx_fme_star          (user_id, local_star_count DESC),
    FULLTEXT KEY ft_fme_title   (meta_title, meta_artist),

    CHECK (entry_type   IN ('audio','video','image','folder')),
    CHECK (scan_status  IN ('pending','indexed','error','skipped')),
    CHECK (local_star_count IN (0.0, 0.5, 1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 4.5, 5.0))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- NOTIFICATIONS — Kullanıcı bildirimleri
-- Normal Form : BCNF
-- Bağımlılık  : id → {user_id, notif_type, title, body, entity_type, entity_id, ...}
--
-- notif_type örnekleri:
--   system          → Sistem mesajı (bakım, güncelleme)
--   new_follower    → Biri takip etti (gelecek özellik)
--   playlist_update → Takip edilen playlist güncellendi
--   singer_release  → Takip edilen sanatçıdan yeni şarkı
--   account         → Hesap aktivitesi (şifre değişikliği, login uyarısı)
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE notifications (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED        NOT NULL,   -- → coremusic_auth.users.id
    notif_type      VARCHAR(30)         NOT NULL,
    -- system | new_follower | playlist_update | singer_release | account
    title           VARCHAR(255)        NOT NULL,
    body            TEXT                    NULL,
    entity_type     VARCHAR(30)             NULL,   -- music | album | playlist | singer
    entity_id       INT UNSIGNED            NULL,   -- İlgili entity ID
    action_url      VARCHAR(2048)           NULL,   -- Tıklandığında yönlendir
    is_read         TINYINT(1)          NOT NULL DEFAULT 0,
    read_at         DATETIME                NULL,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    -- ⚠️ user_id → coremusic_auth DB: cross-DB FK yok.

    INDEX idx_n_user        (user_id, is_read, created_at DESC),
    INDEX idx_n_type        (notif_type),
    INDEX idx_n_entity      (entity_type, entity_id),
    INDEX idx_n_created     (created_at)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- SYSTEM_WIFI_NETWORKS — Local mod: kayıtlı Wi-Fi ağları
-- Normal Form : BCNF
-- ⚠️ LOCAL MOD ONLY — server modda kullanılmaz.
-- assets/css/modules/wifi/ → Wi-Fi modülü bu tabloyu kullanır.
--
-- ⚠️ GÜVENLİK: wifi_password_encrypted sütunu AES-256-GCM ile şifrelenmeli.
--    ASLA plain text saklanmamalı. Şifreleme anahtarı environment variable'da olmalı.
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE system_wifi_networks (
    id                      INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    ssid                    VARCHAR(255)        NOT NULL,
    bssid                   CHAR(17)                NULL,   -- MAC: "AA:BB:CC:DD:EE:FF"
    frequency_mhz           SMALLINT UNSIGNED       NULL,
    -- 2412–2484 → 2.4GHz bant | 5180–5825 → 5GHz bant
    -- Örnek: 2437 (Ch.6 / 2.4GHz), 5180 (Ch.36 / 5GHz)
    security_type           VARCHAR(20)         NOT NULL DEFAULT 'WPA2',
    -- security_type: Open | WEP | WPA | WPA2 | WPA3
    wifi_label_type         VARCHAR(20)             NULL,
    -- UI etiketi: open | encrypted | pin | enterprise | hidden
    -- ai_db_nromalzs_e.txt: "güvenli şifreli şifreiz etc."
    wifi_password_encrypted VARCHAR(512)            NULL,
    -- ⚠️ AES-256-GCM şifreli — plain text ASLA
    is_auto_connect         TINYINT(1)          NOT NULL DEFAULT 1,
    is_hidden               TINYINT(1)          NOT NULL DEFAULT 0,
    signal_strength         TINYINT               NULL,   -- -100 ile 0 dBm arası
    usage_bytes             BIGINT UNSIGNED     NOT NULL DEFAULT 0,
    -- Bu ağ üzerinden toplam trafik (BYTE). Dönüşüm app'te: KB/MB/GB/TB
    -- ai_db_nromalzs_e.txt: "usage size gb mb tb"
    last_connected_at       DATETIME                NULL,
    priority                TINYINT UNSIGNED    NOT NULL DEFAULT 0,
    -- priority: düşük sayı = daha yüksek öncelik (0 = en yüksek)

    created_at              DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                                ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_wifi_ssid    (ssid, bssid),
    INDEX idx_wifi_connect      (is_auto_connect),
    INDEX idx_wifi_priority     (priority),
    INDEX idx_wifi_freq         (frequency_mhz),

    CHECK (security_type    IN ('Open','WEP','WPA','WPA2','WPA3')),
    CHECK (wifi_label_type IS NULL OR wifi_label_type IN ('open','encrypted','pin','enterprise','hidden'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- SYSTEM_WIFI_AVAILABLE — Wi-Fi tarama sonuçları (dinamik, kısa yaşam)
-- Normal Form : BCNF
-- ⚠️ LOCAL MOD ONLY
--
-- Bağlı olmayan, sadece havada görünen ağlar burada tutulur.
-- system_wifi_networks'ten farkı: şifre yok, kalıcı değil, scan timestamp'li.
-- Periyodik temizleme: occurred_at < NOW() - INTERVAL 5 MINUTE → DELETE
--
-- ai_db_nromalzs_e.txt:
--   "kullanılabilir ağlar: wifi available networks id: name, mhz, desc,
--    signal level is label: güvenli şifreli şifreiz etc."
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE system_wifi_available (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    ssid            VARCHAR(255)        NOT NULL,
    bssid           CHAR(17)                NULL,
    frequency_mhz   SMALLINT UNSIGNED       NULL,
    signal_strength TINYINT                 NULL,   -- dBm
    security_type   VARCHAR(20)             NULL,
    wifi_label_type VARCHAR(20)             NULL,
    -- open | encrypted | pin | enterprise | hidden
    description     VARCHAR(255)            NULL,
    is_saved        TINYINT(1)          NOT NULL DEFAULT 0,
    -- 1 = system_wifi_networks'te kayıtlı bu ağ
    scanned_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    -- BSSID varsa benzersiz tara; yoksa (hidden SSID) SSID'e göre
    UNIQUE  KEY uq_wfa_bssid    (bssid),

    INDEX idx_wfa_signal        (signal_strength DESC),
    INDEX idx_wfa_scanned       (scanned_at),
    INDEX idx_wfa_saved         (is_saved),

    CHECK (wifi_label_type IS NULL OR wifi_label_type IN ('open','encrypted','pin','enterprise','hidden'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ══════════════════════════════════════════════════════════════════════════════
-- BÖLÜM: BLUETOOTH YÖNETİMİ (LOCAL MOD)
-- ══════════════════════════════════════════════════════════════════════════════
--
-- [v6.1 YENİ] — UI'da Bluetooth Quick Panel mevcut:
--   1024 - Bluethoot Qucik Page Base.png
--   1024 - Bluethoot Qucik Page Mavi.png
-- ai_db_nromalzs_e.txt:
--   "bt: enable bt
--    bağlı olan bt connected btlist db: connected bt id: name, type, desc,
--    signal level, battery size %, connect type: aac
--    kullanılabilir bt: btavailable networks id: name, type, desc, signal level
--    is label: bağlı değil, şifresi, pin kodlu etc."
-- ══════════════════════════════════════════════════════════════════════════════

-- ──────────────────────────────────────────────────────────────────────────────
-- SYSTEM_BLUETOOTH_DEVICES — Kayıtlı (paired/saved) Bluetooth cihazları
-- Normal Form : BCNF
-- Bağımlılık  : id → {bt_address, device_name, device_type, ...}
-- ⚠️ LOCAL MOD ONLY
--
-- connect_type : Ses iletim protokolü — A2DP, AAC, SBC, aptX, aptX HD, LDAC
--   ai_db: "connect type : aac"
-- battery_level: % 0–100. NULL = cihaz batarya bilgisi göndermiyor.
-- device_type  : headphones | speaker | car_audio | phone | tablet | keyboard | other
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE system_bluetooth_devices (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    bt_address      CHAR(17)            NOT NULL,   -- "AA:BB:CC:DD:EE:FF" (MAC)
    device_name     VARCHAR(255)        NOT NULL,
    device_type     VARCHAR(30)         NOT NULL DEFAULT 'other',
    -- headphones | speaker | car_audio | phone | tablet | keyboard | other
    description     VARCHAR(255)            NULL,
    connect_type    VARCHAR(20)             NULL,
    -- A2DP | AAC | SBC | aptX | aptX_HD | LDAC | HFP | other
    -- ai_db: "connect type: aac"
    is_connected    TINYINT(1)          NOT NULL DEFAULT 0,
    -- Şu an bağlı mı?
    is_trusted      TINYINT(1)          NOT NULL DEFAULT 0,
    -- Eşleştirilmiş (paired) ve güvenilir
    is_auto_connect TINYINT(1)          NOT NULL DEFAULT 1,
    signal_strength TINYINT                 NULL,   -- dBm (bağlıyken)
    battery_level   TINYINT UNSIGNED        NULL,   -- % 0-100 | NULL = bilgi yok
    -- ai_db: "battery size %"
    last_connected_at DATETIME              NULL,

    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_bt_address       (bt_address),

    INDEX idx_bt_connected          (is_connected),
    INDEX idx_bt_type               (device_type),
    INDEX idx_bt_last               (last_connected_at),

    CHECK (device_type IN ('headphones','speaker','car_audio','phone','tablet','keyboard','other')),
    CHECK (connect_type IS NULL OR connect_type IN ('A2DP','AAC','SBC','aptX','aptX_HD','LDAC','HFP','other')),
    CHECK (battery_level IS NULL OR (battery_level >= 0 AND battery_level <= 100))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- SYSTEM_BLUETOOTH_AVAILABLE — BT tarama sonuçları (dinamik, kısa yaşam)
-- Normal Form : BCNF
-- ⚠️ LOCAL MOD ONLY
--
-- Eşleştirilmemiş, havada görünen BT cihazları.
-- system_bluetooth_devices'tan farkı: kalıcı değil, tarama sonucu, pin bilgisi yok.
-- Periyodik temizleme: scanned_at < NOW() - INTERVAL 30 SECOND → DELETE
--
-- is_label_type : UI etiketi
--   ai_db: "is label: bağlı değil, şifreli, pin kodlu etc."
--   değerler: available | paired_elsewhere | requires_pin | requires_confirmation | unknown
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE system_bluetooth_available (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    bt_address      CHAR(17)            NOT NULL,   -- "AA:BB:CC:DD:EE:FF"
    device_name     VARCHAR(255)            NULL,
    device_type     VARCHAR(30)             NULL,
    description     VARCHAR(255)            NULL,
    signal_strength TINYINT                 NULL,   -- dBm
    is_label_type   VARCHAR(30)             NULL,
    -- available | paired_elsewhere | requires_pin | requires_confirmation | unknown
    -- ai_db: "bağlı değil, şifreli, pin kodlu etc."
    is_saved        TINYINT(1)          NOT NULL DEFAULT 0,
    -- 1 = system_bluetooth_devices'ta kayıtlı
    scanned_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_bta_address  (bt_address),

    INDEX idx_bta_signal        (signal_strength DESC),
    INDEX idx_bta_scanned       (scanned_at),
    INDEX idx_bta_saved         (is_saved),

    CHECK (is_label_type IS NULL OR is_label_type IN (
        'available','paired_elsewhere','requires_pin','requires_confirmation','unknown'
    ))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ══════════════════════════════════════════════════════════════════════════════
-- VARSAYILAN VERİLER
-- ══════════════════════════════════════════════════════════════════════════════

-- Sistem EQ preset'leri (is_system=1, user_id=0)
INSERT INTO user_equalizer_presets (user_id, preset_name, bands_json, is_active, is_system, sort_order) VALUES
(0, 'Flat (Varsayılan)',
 '[{"hz":32,"gain":0},{"hz":64,"gain":0},{"hz":125,"gain":0},{"hz":250,"gain":0},{"hz":500,"gain":0},{"hz":1000,"gain":0},{"hz":2000,"gain":0},{"hz":4000,"gain":0},{"hz":8000,"gain":0},{"hz":16000,"gain":0}]',
 0, 1, 0),
(0, 'Arabesk / Türkü',
 '[{"hz":32,"gain":4},{"hz":64,"gain":3},{"hz":125,"gain":1},{"hz":250,"gain":0},{"hz":500,"gain":-1},{"hz":1000,"gain":0},{"hz":2000,"gain":1},{"hz":4000,"gain":2},{"hz":8000,"gain":2},{"hz":16000,"gain":1}]',
 0, 1, 1),
(0, 'Pop',
 '[{"hz":32,"gain":1},{"hz":64,"gain":2},{"hz":125,"gain":2},{"hz":250,"gain":0},{"hz":500,"gain":-1},{"hz":1000,"gain":0},{"hz":2000,"gain":1},{"hz":4000,"gain":2},{"hz":8000,"gain":3},{"hz":16000,"gain":3}]',
 0, 1, 2),
(0, 'Rock',
 '[{"hz":32,"gain":4},{"hz":64,"gain":3},{"hz":125,"gain":2},{"hz":250,"gain":1},{"hz":500,"gain":0},{"hz":1000,"gain":-1},{"hz":2000,"gain":0},{"hz":4000,"gain":2},{"hz":8000,"gain":3},{"hz":16000,"gain":4}]',
 0, 1, 3),
(0, 'Bas Arttır',
 '[{"hz":32,"gain":6},{"hz":64,"gain":5},{"hz":125,"gain":4},{"hz":250,"gain":2},{"hz":500,"gain":0},{"hz":1000,"gain":0},{"hz":2000,"gain":0},{"hz":4000,"gain":0},{"hz":8000,"gain":0},{"hz":16000,"gain":0}]',
 0, 1, 4);

-- Global uygulama ayarları
INSERT INTO app_settings (setting_key, setting_value, setting_type, description, is_public) VALUES
    ('app.name',                'CoreMusic',            'string',  'Uygulama adı',                          1),
    ('app.version',             '1.0.0',                'string',  'Uygulama versiyonu',                     1),
    ('app.mode',                'server',               'string',  'Çalışma modu: local | server',           1),
    ('app.maintenance_mode',    '0',                    'boolean', 'Bakım modu (1=aktif)',                   1),
    ('app.max_upload_mb',       '500',                  'integer', 'Maksimum dosya yükleme boyutu (MB)',      0),
    ('app.default_quality',     'medium',               'string',  'Varsayılan ses kalitesi',                1),
    ('app.allow_explicit',      '0',                    'boolean', 'Explicit içerik göster (varsayılan)',     0),
    ('server.streaming_domain', 'media.coremusic.net',  'string',  'Streaming domain',                       1),
    ('local.auto_scan',         '0',                    'boolean', 'Başlangıçta otomatik tarama yap',        0),
    ('local.bt_enabled',        '0',                    'boolean', 'Bluetooth modülü aktif (local mod)',      0),
    ('local.wifi_enabled',      '0',                    'boolean', 'WiFi modülü aktif (local mod)',           0);