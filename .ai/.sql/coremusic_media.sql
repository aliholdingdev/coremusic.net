-- ══════════════════════════════════════════════════════════════════════════════
-- coremusic_media — DİNLEME GEÇMİŞİ & CİHAZ SİSTEMİ
-- COREMUSIC DB v6.0 | Mart 2026 | MySQL 8.x InnoDB | utf8mb4_turkish_ci
-- NF: BCNF
-- ══════════════════════════════════════════════════════════════════════════════
--
-- [v3 → v6 DEĞİŞİKLİKLER]
-- 1. [DÜZELTMESİ] media_device_types INSERT: inline Türkçe yorum kaldırıldı (syntax hatası)
-- 2. [GELİŞTİRME]  media_device_types: display_name sütunu eklendi (Türkçe gösterim)
-- 3. [GELİŞTİRME]  Cihaz türleri genişletildi: tf_card, bluetooth, nas,
--                  network_drive, optical_disc, smart_tv, cloud eklendi
--
-- [v2 → v3 korunan düzeltmeler — hepsi aynen korundu]
-- 4. media_history çift tanımı kaldırıldı
-- 5. media_devices: PRIMARY KEY, FK, INDEX eksikleri tamamlandı
-- 6. media_device_playlists / tracks: eksikler tamamlandı
-- 7. Cross-DB FK kısıtlamaları kaldırıldı, uygulama doğrulaması notu eklendi
-- 8. total_capacity / free_space BIGINT UNSIGNED BYTE, dönüşüm app'te
-- 9. sp_create_device_playlist stored procedure korundu
-- ══════════════════════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS coremusic_media
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_turkish_ci;

USE coremusic_media;


-- ══════════════════════════════════════════════════════════════════════════════
-- BÖLÜM 1: CİHAZ LOOKUP
-- ══════════════════════════════════════════════════════════════════════════════

-- ──────────────────────────────────────────────────────────────────────────────
-- MEDIA_DEVICE_TYPES — Cihaz türleri (lookup)
-- Normal Form : BCNF
-- Değerler    : usb | sd_card | local_disk | external_hdd | phone | car_system
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE media_device_types (
    id          TINYINT UNSIGNED    NOT NULL AUTO_INCREMENT,
    type_name   VARCHAR(50)         NOT NULL,
    -- usb | sd_card | local_disk | external_hdd | phone | car_system
    display_name VARCHAR(100)            NULL,   -- Türkçe/yerelleştirilmiş gösterim adı

    PRIMARY KEY (id),
    UNIQUE  KEY uq_dt_name (type_name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ══════════════════════════════════════════════════════════════════════════════
-- BÖLÜM 2: CİHAZ KAYIT SİSTEMİ
-- ══════════════════════════════════════════════════════════════════════════════

-- ──────────────────────────────────────────────────────────────────────────────
-- MEDIA_DEVICES — Kayıtlı cihazlar (USB, disk, telefon, araba sistemi, NAS vb.)
-- Normal Form : BCNF
-- Bağımlılık  : id → {device_label, device_type_id, owner_user_id, ...}
-- device_uuid : USB serial no veya disk GUID (cihazın fiziksel kimliği)
-- total_capacity / free_space: BIGINT UNSIGNED BYTE cinsinden
--   Dönüşüm uygulama katmanında yapılır:
--   PHP: if($b>=1099511627776) return round($b/1099511627776,1).' TB';
--        elseif($b>=1073741824) return round($b/1073741824,2).' GB';
--        elseif($b>=1048576)    return round($b/1048576,2).' MB';
--        else                   return round($b/1024).' KB';
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE media_devices (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    device_label    VARCHAR(255)        NOT NULL,   -- "Araba USB", "Taşınabilir Disk 1"
    device_type_id  TINYINT UNSIGNED        NULL,
    device_uuid     VARCHAR(128)            NULL,   -- USB serial no / disk GUID
    owner_user_id   INT UNSIGNED        NOT NULL,
    total_capacity  BIGINT UNSIGNED         NULL,   -- BYTE (dönüşüm app'te: KB/MB/GB/TB)
    free_space      BIGINT UNSIGNED         NULL,   -- BYTE (son sync anındaki boş alan)
    mount_path      VARCHAR(512)            NULL,   -- "/media/usb0" veya "E:\"
    last_seen_at    DATETIME                NULL,   -- Son bağlantı zamanı
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,

    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    FOREIGN KEY fk_mdev_type    (device_type_id)
        REFERENCES media_device_types (id) ON DELETE SET NULL,

    UNIQUE  KEY uq_mdev_uuid    (device_uuid),
    INDEX idx_mdev_owner        (owner_user_id),
    INDEX idx_mdev_active       (is_active),
    INDEX idx_mdev_seen         (last_seen_at)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ══════════════════════════════════════════════════════════════════════════════
-- BÖLÜM 3: CİHAZ / OFFLİNE OYNATMA LİSTELERİ
-- "music_playlist_$playlistID" mantığının doğru veritabanı karşılığı
-- Sistem otomatik oluşturur → stored procedure ile
-- ══════════════════════════════════════════════════════════════════════════════

-- ──────────────────────────────────────────────────────────────────────────────
-- MEDIA_DEVICE_PLAYLISTS — Cihaz/Offline oynatma listeleri
-- Normal Form : BCNF
-- Bağımlılık  : id → {device_id, playlist_name, owner_user_id, status, ...}
--
-- ═══ "music_playlist_$playlistID" MANTIKSAL KARŞILIĞI ═══
-- Her satır = eski sistemdeki bir "music_playlist_$id" tablosu.
-- id = 742 → eskiden "music_playlist_742" adlı tablo demekti.
-- Artık: WHERE device_playlist_id = 742
--
-- status: pending | syncing | synced | failed | outdated
--   pending   → export kuyruğa alındı
--   syncing   → dosyalar kopyalanıyor
--   synced    → cihaza başarıyla yazıldı
--   failed    → hata oluştu
--   outdated  → kaynak playlist güncellendi, re-sync gerekiyor
--
-- source_playlist_id → coremusic_playlist.playlists(id)
--   ⚠️ Cross-DB FK desteklenmez. Uygulama seviyesinde doğrula.
-- total_size: BIGINT UNSIGNED BYTE (dönüşüm app'te)
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE media_device_playlists (
    id                  INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    device_id           INT UNSIGNED        NOT NULL,
    owner_user_id       INT UNSIGNED        NOT NULL,
    playlist_name       VARCHAR(500)        NOT NULL,
    source_playlist_id  INT UNSIGNED            NULL,   -- → coremusic_playlist.playlists.id
    status              VARCHAR(20)         NOT NULL DEFAULT 'pending',
    -- status: pending | syncing | synced | failed | outdated
    total_tracks        SMALLINT UNSIGNED   NOT NULL DEFAULT 0,
    total_size          BIGINT UNSIGNED     NOT NULL DEFAULT 0,   -- BYTE (dönüşüm app'te)
    export_path         VARCHAR(512)            NULL,   -- Cihazdaki klasör yolu
    synced_at           DATETIME                NULL,   -- Son başarılı sync zamanı
    error_message       TEXT                    NULL,   -- Hata varsa mesaj
    is_active           TINYINT(1)          NOT NULL DEFAULT 1,

    created_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    FOREIGN KEY fk_mdp_device   (device_id)
        REFERENCES media_devices (id) ON DELETE CASCADE,
    -- ⚠️ source_playlist_id → coremusic_playlist DB: cross-DB FK yok.

    INDEX idx_mdp_device        (device_id),
    INDEX idx_mdp_owner         (owner_user_id),
    INDEX idx_mdp_status        (status),
    INDEX idx_mdp_source        (source_playlist_id),
    INDEX idx_mdp_active        (is_active),

    CHECK (status IN ('pending','syncing','synced','failed','outdated'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- MEDIA_DEVICE_PLAYLIST_TRACKS — Cihaz playlist ↔ Şarkı (M2M)
-- Normal Form : BCNF
-- cached_file_path : Cihazdaki dosya yolu ("/music/Tarkan/Şımarık.mp3")
-- file_size        : BYTE cinsinden (dönüşüm app'te)
-- quality_label    : Export kalitesi (128kbps, 320kbps, lossless)
-- sync_status      : pending | copied | failed
-- music_id         → coremusic_musics.musics(id) — cross-DB, uygulama doğrulaması
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE media_device_playlist_tracks (
    id                  INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    device_playlist_id  INT UNSIGNED        NOT NULL,
    music_id            INT UNSIGNED        NOT NULL,   -- → coremusic_musics.musics.id
    track_order         SMALLINT UNSIGNED   NOT NULL DEFAULT 0,
    quality_label       VARCHAR(30)             NULL,   -- Hangi kalitede export edildi
    cached_file_path    VARCHAR(512)            NULL,   -- Cihazdaki tam dosya yolu
    file_size           BIGINT UNSIGNED         NULL,   -- BYTE (dönüşüm app'te)
    sync_status         VARCHAR(20)         NOT NULL DEFAULT 'pending',
    -- sync_status: pending | copied | failed
    sync_error          VARCHAR(500)            NULL,
    synced_at           DATETIME                NULL,

    created_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_mdpt_pair        (device_playlist_id, music_id),
    FOREIGN KEY fk_mdpt_playlist    (device_playlist_id)
        REFERENCES media_device_playlists (id) ON DELETE CASCADE,
    -- ⚠️ music_id → coremusic_musics DB: cross-DB FK yok.

    INDEX idx_mdpt_music            (music_id),
    INDEX idx_mdpt_status           (sync_status),
    INDEX idx_mdpt_order            (device_playlist_id, track_order),

    CHECK (sync_status IN ('pending','copied','failed'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ══════════════════════════════════════════════════════════════════════════════
-- BÖLÜM 4: DİNLEME GEÇMİŞİ
-- ══════════════════════════════════════════════════════════════════════════════

-- ──────────────────────────────────────────────────────────────────────────────
-- MEDIA_HISTORY — Dinleme geçmişi
-- Normal Form : BCNF
-- ⚠️ En hızlı büyüyen tablo. Partitioning planı:
--   PARTITION BY RANGE (YEAR(started_at)) — ilerleyen aşamada ekle
-- playlist_id        → coremusic_playlist.playlists(id)       — cross-DB
-- device_playlist_id → media_device_playlists(id)          — aynı DB ✅
-- music_id           → coremusic_musics.musics(id)          — cross-DB
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE media_history (
    id                  BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    user_id             INT UNSIGNED        NOT NULL,
    music_id            INT UNSIGNED        NOT NULL,   -- → coremusic_musics.musics.id
    playlist_id         INT UNSIGNED            NULL,   -- → coremusic_playlist.playlists.id
    device_playlist_id  INT UNSIGNED            NULL,   -- → media_device_playlists.id
    source              VARCHAR(30)             NULL,
    -- source: playlist | device | album | search | recommendation | radio
    started_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    played_sec          INT UNSIGNED        NOT NULL DEFAULT 0,
    is_completed        TINYINT(1)          NOT NULL DEFAULT 0,

    PRIMARY KEY (id),
    -- ⚠️ playlist_id → coremusic_playlist DB: cross-DB FK yok. Uygulama doğrulaması.
    FOREIGN KEY fk_mh_device_pl (device_playlist_id)
        REFERENCES media_device_playlists (id) ON DELETE SET NULL,

    INDEX idx_mh_user           (user_id, started_at DESC),
    INDEX idx_mh_music          (music_id),
    INDEX idx_mh_playlist       (playlist_id),
    INDEX idx_mh_device_pl      (device_playlist_id),
    INDEX idx_mh_started        (started_at),
    INDEX idx_mh_source         (source),

    CHECK (source IS NULL OR source IN ('playlist','device','album','search','recommendation','radio'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ══════════════════════════════════════════════════════════════════════════════
-- STORED PROCEDURE — Sistem otomatik device playlist oluşturma
--
-- Kullanım:
--   CALL sp_create_device_playlist(device_id, user_id, 'Araba Listesi', source_playlist_id);
--
-- PHP tarafı:
--   $stmt = $pdo->prepare("CALL sp_create_device_playlist(?,?,?,?)");
--   $stmt->execute([$deviceId, $userId, $name, $sourcePlistId]);
--   $row = $stmt->fetch();
--   $id = $row['new_device_playlist_id'];
--   // Bu ID → eskiden "music_playlist_$id" tablo adındaki sayıya karşılık gelir.
-- ══════════════════════════════════════════════════════════════════════════════
DELIMITER $$

CREATE PROCEDURE sp_create_device_playlist(
    IN  p_device_id          INT UNSIGNED,
    IN  p_user_id            INT UNSIGNED,
    IN  p_playlist_name      VARCHAR(500),
    IN  p_source_playlist_id INT UNSIGNED   -- NULL = manuel, sayı = kaynak plist id
)
BEGIN
    INSERT INTO media_device_playlists (
        device_id,
        owner_user_id,
        playlist_name,
        source_playlist_id,
        status
    ) VALUES (
        p_device_id,
        p_user_id,
        p_playlist_name,
        p_source_playlist_id,
        'pending'
    );

    SELECT LAST_INSERT_ID() AS new_device_playlist_id;
END$$

DELIMITER ;


-- ══════════════════════════════════════════════════════════════════════════════
-- VARSAYILAN VERİLER
-- type_name = sistem anahtarı (kod içinde kullanılır)
-- display_name = Türkçe/yerelleştirilmiş gösterim
-- ══════════════════════════════════════════════════════════════════════════════
INSERT INTO media_device_types (type_name, display_name) VALUES
    ('usb',           'USB Bellek'),
    ('sd_card',       'SD Kart'),
    ('tf_card',       'TF (MicroSD) Kart'),
    ('local_disk',    'Yerel Disk'),
    ('external_hdd',  'Taşınabilir HDD/SSD'),
    ('phone',         'Telefon / Mobil Cihaz'),
    ('bluetooth',     'Bluetooth Cihazı'),
    ('car_system',    'Araç Sistemi'),
    ('nas',           'NAS / Ağ Depolama'),
    ('network_drive', 'Ağ Sürücüsü (Samba/NFS)'),
    ('optical_disc',  'Optik Disk (CD/DVD)'),
    ('smart_tv',      'Akıllı TV'),
    ('cloud',         'Bulut Depolama');


-- ══════════════════════════════════════════════════════════════════════════════
-- ÖRNEK SORGULAR
-- ══════════════════════════════════════════════════════════════════════════════

-- ── Device playlist içeriğini getir ────────────────────────────────────────
/*
SELECT
    t.track_order,
    t.music_id,
    t.cached_file_path,
    t.quality_label,
    t.file_size,
    t.sync_status
FROM media_device_playlist_tracks t
WHERE t.device_playlist_id = :device_playlist_id
  AND t.sync_status = 'copied'
ORDER BY t.track_order ASC;
*/

-- ── Kullanıcının tüm cihazları ve export durumu ────────────────────────────
/*
SELECT
    d.id             AS device_id,
    d.device_label,
    dt.type_name     AS device_type,
    dt.display_name  AS device_type_tr,
    d.free_space,
    dp.id            AS device_playlist_id,
    dp.playlist_name,
    dp.status,
    dp.total_tracks,
    dp.total_size,
    dp.synced_at
FROM media_devices d
LEFT JOIN media_device_types     dt ON dt.id        = d.device_type_id
LEFT JOIN media_device_playlists dp ON dp.device_id = d.id AND dp.is_active = 1
WHERE d.owner_user_id = :user_id
  AND d.is_active = 1
ORDER BY d.id, dp.id;
*/

-- ── Sync istatistiği ───────────────────────────────────────────────────────
/*
SELECT
    sync_status,
    COUNT(*) AS track_count
FROM media_device_playlist_tracks
WHERE device_playlist_id = :device_playlist_id
GROUP BY sync_status;
*/