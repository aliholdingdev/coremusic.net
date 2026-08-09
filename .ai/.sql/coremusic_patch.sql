-- ══════════════════════════════════════════════════════════════════════════════
-- coremusic_patches_v7.sql — DİĞER DB'LERDEKİ EKSİK DÜZELTMELER
-- COREMUSIC DB | Mart 2026 | MySQL 8.x InnoDB
-- ══════════════════════════════════════════════════════════════════════════════
--
-- Bu dosya mevcut tablolara ALTER ile düzeltme uygular.
-- Her düzeltme hangi DB'ye ait olduğu belirtilmiştir.
-- Production'a migration tool (Phinx / Flyway) ile uygulanmalıdır.
--
-- İÇERİK:
-- 1. coremusic_musics.music_detail — music_id PK'ya taşı (tasarım tutarlılığı)
-- 2. coremusic_logs.logs_app_errors — request_method CHECK constraint eksik
-- 3. coremusic_media.media_history  — source CHECK constraint eksik
-- 4. coremusic_logs.logs_audit      — resolved_by cross-DB yorum eksik (yorum patch)
--
-- ══════════════════════════════════════════════════════════════════════════════


-- ══════════════════════════════════════════════════════════════════════════════
-- PATCH 1: coremusic_musics — music_detail tasarım tutarlılığı
-- ══════════════════════════════════════════════════════════════════════════════
--
-- SORUN [TASARIM]:
--   music_detail tablosu music_id ile 1:1 ilişkili olmasına rağmen
--   surrogate `id` PK kullanıyor + UNIQUE KEY uq_md_music (music_id).
--   music_stats, singer_stats, album_stats → hepsi entity_id'yi PK olarak kullanıyor.
--   music_detail bu pattern'dan sapıyor, gereksiz surrogate PK var.
--
-- NEDEN ÖNEMLİ:
--   - Fazladan 4 byte per row (INT surrogate) + 1 gereksiz B+ tree
--   - JOIN'lerde music_id yerine id kullanılabilir → kod belirsizliği
--   - Diğer stats tablolarıyla mimari tutarsızlık
--
-- ⚠️ DİKKAT: Bu migration aşağıdaki adımları gerektirir:
--   1. Varsa id'ye referans veren FK'ları tespit et (şu an yok)
--   2. AUTO_INCREMENT'ı kaldır
--   3. PK'yı music_id'ye taşı
--   4. Artık gereksiz UNIQUE KEY'i kaldır
--
-- Varsayım: Tablo boş veya data migration öncesi. Veri varsa pt-online-schema-change kullan.
-- ──────────────────────────────────────────────────────────────────────────────

USE coremusic_musics;

-- Adım 1: Mevcut PK + AUTO_INCREMENT kaldır
ALTER TABLE music_detail
    DROP PRIMARY KEY,
    MODIFY COLUMN id INT UNSIGNED NOT NULL;  -- AUTO_INCREMENT kaldırılıyor

-- Adım 2: Gereksiz UNIQUE KEY kaldır (music_id zaten PK olacak)
ALTER TABLE music_detail
    DROP KEY uq_md_music;

-- Adım 3: music_id'yi PK yap
ALTER TABLE music_detail
    DROP COLUMN id,
    ADD PRIMARY KEY (music_id);

-- Sonuç: music_stats / singer_stats / album_stats ile tutarlı 1:1 PK tasarımı
-- Normal Form: BCNF — (music_id PK+FK → tüm non-key sütunlar)


-- ══════════════════════════════════════════════════════════════════════════════
-- PATCH 2: coremusic_logs — logs_app_errors.request_method CHECK eksik
-- ══════════════════════════════════════════════════════════════════════════════
--
-- SORUN [ORTA]:
--   request_method VARCHAR(10) — yorum satırında GET|POST|PUT|DELETE|PATCH var
--   ama CHECK constraint yok. Herhangi bir string yazılabilir.
--
USE coremusic_logs;

ALTER TABLE logs_app_errors
    ADD CONSTRAINT chk_ler_method
    CHECK (request_method IS NULL OR request_method IN ('GET','POST','PUT','DELETE','PATCH','HEAD','OPTIONS'));


-- ══════════════════════════════════════════════════════════════════════════════
-- PATCH 3: coremusic_media — media_history.source CHECK eksik
-- ══════════════════════════════════════════════════════════════════════════════
--
-- SORUN [ORTA]:
--   source VARCHAR(30) — yorum satırında değerler var ama CHECK yok.
--
USE coremusic_media;

ALTER TABLE media_history
    ADD CONSTRAINT chk_mh_source
    CHECK (source IS NULL OR source IN ('playlist','device','album','search','recommendation','radio'));


-- ══════════════════════════════════════════════════════════════════════════════
-- PATCH 4: coremusic_albums — total_tracks trigger (bilinçli denormalizasyon koruyucu)
-- ══════════════════════════════════════════════════════════════════════════════
--
-- SORUN [ORTA]:
--   albums.total_tracks bilinçli denormalizasyon olarak belgelenmiş
--   ama güncelleme mekanizması yok. INSERT/DELETE sonrası tutarsız kalabilir.
--   Uygulama katmanında güncelleme VEYA bu trigger — ikisi birden değil.
--
-- Varsayım: Uygulama katmanı yetersiz kaldığında yedek güvenlik olarak trigger.
--
USE coremusic_albums;

DELIMITER $$

CREATE TRIGGER trg_album_musics_after_insert
AFTER INSERT ON album_musics
FOR EACH ROW
BEGIN
    UPDATE albums
    SET total_tracks = (
        SELECT COUNT(*) FROM album_musics
        WHERE album_id = NEW.album_id AND is_active = 1 AND deleted_at IS NULL
    )
    WHERE id = NEW.album_id;
END$$

CREATE TRIGGER trg_album_musics_after_delete
AFTER DELETE ON album_musics
FOR EACH ROW
BEGIN
    UPDATE albums
    SET total_tracks = (
        SELECT COUNT(*) FROM album_musics
        WHERE album_id = OLD.album_id AND is_active = 1 AND deleted_at IS NULL
    )
    WHERE id = OLD.album_id;
END$$

CREATE TRIGGER trg_album_musics_after_update
AFTER UPDATE ON album_musics
FOR EACH ROW
BEGIN
    -- is_active veya deleted_at değişmişse sayacı güncelle
    IF OLD.is_active <> NEW.is_active OR
       (OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL) OR
       (OLD.deleted_at IS NOT NULL AND NEW.deleted_at IS NULL) THEN
        UPDATE albums
        SET total_tracks = (
            SELECT COUNT(*) FROM album_musics
            WHERE album_id = NEW.album_id AND is_active = 1 AND deleted_at IS NULL
        )
        WHERE id = NEW.album_id;
    END IF;
END$$

DELIMITER ;


-- ══════════════════════════════════════════════════════════════════════════════
-- PATCH 5: coremusic_media — media_device_playlists.total_tracks trigger
-- ══════════════════════════════════════════════════════════════════════════════
--
-- media_device_playlists.total_tracks da aynı problem.
-- Trigger: media_device_playlist_tracks'a INSERT/DELETE/UPDATE sonrası güncelle.
--
USE coremusic_media;

DELIMITER $$

CREATE TRIGGER trg_mdpt_after_insert
AFTER INSERT ON media_device_playlist_tracks
FOR EACH ROW
BEGIN
    UPDATE media_device_playlists
    SET total_tracks = (
        SELECT COUNT(*) FROM media_device_playlist_tracks
        WHERE device_playlist_id = NEW.device_playlist_id
    )
    WHERE id = NEW.device_playlist_id;
END$$

CREATE TRIGGER trg_mdpt_after_delete
AFTER DELETE ON media_device_playlist_tracks
FOR EACH ROW
BEGIN
    UPDATE media_device_playlists
    SET total_tracks = (
        SELECT COUNT(*) FROM media_device_playlist_tracks
        WHERE device_playlist_id = OLD.device_playlist_id
    )
    WHERE id = OLD.device_playlist_id;
END$$

DELIMITER ;