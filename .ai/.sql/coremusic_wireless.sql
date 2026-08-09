-- ══════════════════════════════════════════════════════════════════════════════
-- coremusic_wireless — KABLOSUZ AĞ & SENKRONİZASYON TABLOLARI
-- COREMUSIC DB v1.0 | Temmuz 2026 | MySQL 8.x InnoDB | utf8mb4_turkish_ci
-- NF: BCNF
-- ══════════════════════════════════════════════════════════════════════════════
--
-- ⚠️ LOCAL MOD ONLY — Bu veritabanı yalnızca local modda (RPi5, Windows desktop)
--    kullanılır. Server modunda bu tablolar boştur veya kullanılmaz.
--
-- TABLOLAR:
--   wifi_networks       → Kayıtlı Wi-Fi ağları (şifreli, öncelikli)
--   bluetooth_peers     → Eşleştirilmiş Bluetooth cihazları
--   sync_history        → Kablosuz senkronizasyon geçmişi
--
-- ══════════════════════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS coremusic_wireless
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_turkish_ci;

USE coremusic_wireless;


-- ──────────────────────────────────────────────────────────────────────────────
-- WIFI_NETWORKS — Kayıtlı Wi-Fi ağları
-- Normal Form : BCNF
-- Bağımlılık  : id → {ssid, bssid, security_type, password_encrypted, ...}
--
-- ⚠️ GÜVENLİK: password_encrypted AES-256-GCM ile şifrelenmeli.
--    ASLA plain text saklanmamalı. Şifreleme anahtarı environment variable'da.
--
-- security_type: Open | WEP | WPA | WPA2 | WPA3 | WPA2_Enterprise | WPA3_Enterprise
-- band: 2.4 | 5 | 6 (GHz)
-- priority: düşük sayı = daha yüksek öncelik (0 = en yüksek)
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE wifi_networks (
    id                      INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id                 INT UNSIGNED        NOT NULL,
    ssid                    VARCHAR(255)        NOT NULL,
    bssid                   CHAR(17)                NULL,   -- "AA:BB:CC:DD:EE:FF"
    security_type           VARCHAR(20)         NOT NULL DEFAULT 'WPA2',
    password_encrypted      VARBINARY(512)          NULL,   -- AES-256-GCM şifreli
    encryption_nonce        CHAR(24)                NULL,   -- hex(random_bytes(12))
    band                    VARCHAR(5)              NULL,   -- 2.4 | 5 | 6
    frequency_mhz           SMALLINT UNSIGNED       NULL,
    channel                 TINYINT UNSIGNED        NULL,
    signal_strength         TINYINT                 NULL,   -- dBm (-100 ile 0 arası)
    is_auto_connect         TINYINT(1)          NOT NULL DEFAULT 1,
    is_hidden               TINYINT(1)          NOT NULL DEFAULT 0,
    is_active               TINYINT(1)          NOT NULL DEFAULT 1,
    priority                TINYINT UNSIGNED    NOT NULL DEFAULT 100,
    usage_bytes             BIGINT UNSIGNED     NOT NULL DEFAULT 0,
    last_connected_at       DATETIME                NULL,
    created_at              DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                                ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    -- ⚠️ user_id → coremusic_auth.users.id: cross-DB FK

    UNIQUE  KEY uq_wn_ssid_bssid   (ssid, bssid),
    INDEX idx_wn_user          (user_id),
    INDEX idx_wn_autoconnect   (is_auto_connect, priority),
    INDEX idx_wn_priority      (priority),
    INDEX idx_wn_band          (band),
    INDEX idx_wn_active        (is_active),
    INDEX idx_wn_last_conn     (last_connected_at DESC),

    CHECK (security_type  IN ('Open','WEP','WPA','WPA2','WPA3','WPA2_Enterprise','WPA3_Enterprise')),
    CHECK (band            IS NULL OR band IN ('2.4','5','6')),
    CHECK (signal_strength IS NULL OR (signal_strength BETWEEN -100 AND 0)),
    CHECK (priority BETWEEN 0 AND 255)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- BLUETOOTH_PEERS — Eşleştirilmiş Bluetooth cihazları
-- Normal Form : BCNF
-- Bağımlılık  : id → {bt_address, device_name, device_type, connect_protocol, ...}
--
-- device_type: headphones | speaker | car_audio | phone | tablet | keyboard | mouse | other
-- connect_protocol: A2DP | AAC | SBC | aptX | aptX_HD | LDAC | HFP | HSP | AVRCP
-- battery_level: % 0–100. NULL = cihaz batarya bilgisi göndermiyor.
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE bluetooth_peers (
    id                  INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    user_id             INT UNSIGNED        NOT NULL,
    bt_address          CHAR(17)            NOT NULL,   -- "AA:BB:CC:DD:EE:FF"
    device_name         VARCHAR(255)        NOT NULL,
    device_type         VARCHAR(30)         NOT NULL DEFAULT 'other',
    connect_protocol    VARCHAR(20)             NULL,
    description         VARCHAR(255)            NULL,
    is_connected        TINYINT(1)          NOT NULL DEFAULT 0,
    is_trusted          TINYINT(1)          NOT NULL DEFAULT 0,
    is_auto_connect     TINYINT(1)          NOT NULL DEFAULT 1,
    signal_strength     TINYINT                 NULL,   -- dBm
    battery_level       TINYINT UNSIGNED        NULL,   -- % 0–100
    last_connected_at   DATETIME                NULL,
    created_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    -- ⚠️ user_id → coremusic_auth.users.id: cross-DB FK

    UNIQUE  KEY uq_bp_address       (bt_address),
    INDEX idx_bp_user           (user_id),
    INDEX idx_bp_type           (device_type),
    INDEX idx_bp_connected      (is_connected),
    INDEX idx_bp_trusted        (is_trusted),
    INDEX idx_bp_autoconnect    (is_auto_connect),
    INDEX idx_bp_last_conn      (last_connected_at DESC),

    CHECK (device_type      IN ('headphones','speaker','car_audio','phone','tablet','keyboard','mouse','other')),
    CHECK (connect_protocol IS NULL OR connect_protocol IN ('A2DP','AAC','SBC','aptX','aptX_HD','LDAC','HFP','HSP','AVRCP')),
    CHECK (battery_level    IS NULL OR (battery_level >= 0 AND battery_level <= 100))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- SYNC_HISTORY — Kablosuz senkronizasyon geçmişi
-- Normal Form : BCNF
-- Bağımlılık  : id → {user_id, sync_type, transport, status, ...}
--
-- sync_type: music_library | playlist | settings | eq_preset | download
-- transport: wifi | bluetooth | usb | ethernet
-- status: started | in_progress | completed | failed | cancelled
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE sync_history (
    id                  BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    user_id             INT UNSIGNED        NOT NULL,
    sync_type           VARCHAR(30)         NOT NULL,
    transport           VARCHAR(20)         NOT NULL,
    status              VARCHAR(20)         NOT NULL DEFAULT 'started',
    source_device       VARCHAR(255)            NULL,   -- Kaynak cihaz adı
    target_device       VARCHAR(255)            NULL,   -- Hedef cihaz adı
    total_items         INT UNSIGNED            NULL,   -- Toplam öğe sayısı
    synced_items        INT UNSIGNED            NULL,   -- Senkronize edilen öğe
    failed_items        INT UNSIGNED            NULL,   -- Başarısız öğe sayısı
    total_bytes         BIGINT UNSIGNED         NULL,   -- Toplam veri boyutu
    transfer_speed_bps  BIGINT UNSIGNED         NULL,   -- Transfer hızı (bit/sn)
    error_message       TEXT                    NULL,
    started_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    completed_at        DATETIME                NULL,

    PRIMARY KEY (id),
    -- ⚠️ user_id → coremusic_auth.users.id: cross-DB FK

    INDEX idx_sh_user       (user_id),
    INDEX idx_sh_type       (sync_type),
    INDEX idx_sh_transport  (transport),
    INDEX idx_sh_status     (status),
    INDEX idx_sh_started    (started_at DESC),
    INDEX idx_sh_completed  (completed_at DESC),

    CHECK (sync_type  IN ('music_library','playlist','settings','eq_preset','download')),
    CHECK (transport  IN ('wifi','bluetooth','usb','ethernet')),
    CHECK (status     IN ('started','in_progress','completed','failed','cancelled'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ══════════════════════════════════════════════════════════════════════════════
-- End of coremusic_wireless schema
