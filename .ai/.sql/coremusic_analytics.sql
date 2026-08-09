-- ══════════════════════════════════════════════════════════════════════════════
-- coremusic_analytics — ANALİTİK & İSTATİSTİK TABLOLARI
-- COREMUSIC DB v1.0 | Temmuz 2026 | MySQL 8.x InnoDB | utf8mb4_turkish_ci
-- NF: BCNF
-- ══════════════════════════════════════════════════════════════════════════════
--
-- TABLOLAR:
--   page_views          → Sayfa görüntüleme logları (GÜNLÜK PARTITION)
--   user_events         → Kullanıcı etkileşim olayları (tıklama, oynatma, ...)
--   performance_metrics → Uygulama/sunucu performans metrikleri
--   daily_stats         → Günlük özet istatistikler (aggregate)
--
-- ══════════════════════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS coremusic_analytics
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_turkish_ci;

USE coremusic_analytics;


-- ──────────────────────────────────────────────────────────────────────────────
-- PAGE_VIEWS — Sayfa görüntüleme logları (GÜNLÜK PARTITION)
-- Normal Form : BCNF
-- Bağımlılık  : id → {user_id, page_url, referrer, device_type, ...}
--
-- ⚠️ PARTITION: GÜNLÜK RANGE partitioning (viewed_at tarihine göre).
--    Veri saklama: 90 gün sonra DROP PARTITION ile temizlenir.
--    Otomasyon ALTYAPISI:
--      CREATE EVENT clean_page_views
--      ON SCHEDULE EVERY 1 DAY
--      DO CALL clean_partitions('coremusic_analytics', 'page_views', 90);
--
-- page_url: normalize edilmiş URL (query string temizlenmiş)
-- device_type: desktop | mobile | tablet | tv | embedded
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE page_views (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED            NULL,   -- NULL = anonim
    page_url        VARCHAR(2048)       NOT NULL,
    referrer        VARCHAR(2048)           NULL,
    device_type     VARCHAR(20)         NOT NULL DEFAULT 'desktop',
    screen_width    SMALLINT UNSIGNED       NULL,
    screen_height   SMALLINT UNSIGNED       NULL,
    ip_address      VARCHAR(45)             NULL,
    user_agent      VARCHAR(512)            NULL,
    country_code    CHAR(2)                 NULL,
    session_id      VARCHAR(64)             NULL,
    load_time_ms    INT UNSIGNED            NULL,
    viewed_at       DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id, viewed_at),
    -- ⚠️ user_id → coremusic_auth.users.id: cross-DB FK

    INDEX idx_pv_user       (user_id),
    INDEX idx_pv_url        (page_url(255)),
    INDEX idx_pv_device     (device_type),
    INDEX idx_pv_referrer   (referrer(255)),
    INDEX idx_pv_ip         (ip_address),
    INDEX idx_pv_country    (country_code),
    INDEX idx_pv_session    (session_id),
    INDEX idx_pv_loadtime   (load_time_ms)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci
PARTITION BY RANGE (TO_DAYS(viewed_at)) (
    PARTITION p_2026_07_01 VALUES LESS THAN (TO_DAYS('2026-07-02')),
    PARTITION p_2026_07_02 VALUES LESS THAN (TO_DAYS('2026-07-03')),
    PARTITION p_2026_07_03 VALUES LESS THAN (TO_DAYS('2026-07-04')),
    PARTITION p_2026_07_04 VALUES LESS THAN (TO_DAYS('2026-07-05')),
    PARTITION p_2026_07_05 VALUES LESS THAN (TO_DAYS('2026-07-06')),
    PARTITION p_2026_07_06 VALUES LESS THAN (TO_DAYS('2026-07-07')),
    PARTITION p_2026_07_07 VALUES LESS THAN (TO_DAYS('2026-07-08')),
    PARTITION p_2026_07_08 VALUES LESS THAN (TO_DAYS('2026-07-09')),
    PARTITION p_2026_07_09 VALUES LESS THAN (TO_DAYS('2026-07-10')),
    PARTITION p_2026_07_10 VALUES LESS THAN (TO_DAYS('2026-07-11')),
    PARTITION p_2026_07_11 VALUES LESS THAN (TO_DAYS('2026-07-12')),
    PARTITION p_2026_07_12 VALUES LESS THAN (TO_DAYS('2026-07-13')),
    PARTITION p_2026_07_13 VALUES LESS THAN (TO_DAYS('2026-07-14')),
    PARTITION p_2026_07_14 VALUES LESS THAN (TO_DAYS('2026-07-15')),
    PARTITION p_2026_07_15 VALUES LESS THAN (TO_DAYS('2026-07-16')),
    PARTITION p_2026_07_16 VALUES LESS THAN (TO_DAYS('2026-07-17')),
    PARTITION p_2026_07_17 VALUES LESS THAN (TO_DAYS('2026-07-18')),
    PARTITION p_2026_07_18 VALUES LESS THAN (TO_DAYS('2026-07-19')),
    PARTITION p_2026_07_19 VALUES LESS THAN (TO_DAYS('2026-07-20')),
    PARTITION p_2026_07_20 VALUES LESS THAN (TO_DAYS('2026-07-21')),
    PARTITION p_2026_07_21 VALUES LESS THAN (TO_DAYS('2026-07-22')),
    PARTITION p_2026_07_22 VALUES LESS THAN (TO_DAYS('2026-07-23')),
    PARTITION p_2026_07_23 VALUES LESS THAN (TO_DAYS('2026-07-24')),
    PARTITION p_2026_07_24 VALUES LESS THAN (TO_DAYS('2026-07-25')),
    PARTITION p_2026_07_25 VALUES LESS THAN (TO_DAYS('2026-07-26')),
    PARTITION p_2026_07_26 VALUES LESS THAN (TO_DAYS('2026-07-27')),
    PARTITION p_2026_07_27 VALUES LESS THAN (TO_DAYS('2026-07-28')),
    PARTITION p_2026_07_28 VALUES LESS THAN (TO_DAYS('2026-07-29')),
    PARTITION p_2026_07_29 VALUES LESS THAN (TO_DAYS('2026-07-30')),
    PARTITION p_2026_07_30 VALUES LESS THAN (TO_DAYS('2026-07-31')),
    PARTITION p_2026_07_31 VALUES LESS THAN (TO_DAYS('2026-08-01')),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);


-- ──────────────────────────────────────────────────────────────────────────────
-- USER_EVENTS — Kullanıcı etkileşim olayları
-- Normal Form : BCNF
-- Bağımlılık  : id → {user_id, event_type, event_value, page_url, ...}
--
-- event_type örnekleri:
--   playback.start     → Şarkı oynatma başlatma
--   playback.complete  → Şarkı tam dinleme
--   playback.skip      → Şarkı atlama
--   search.query       → Arama sorgusu
--   playlist.create    → Playlist oluşturma
--   playlist.add       → Playlist'e şarkı ekleme
--   like.toggle        → Beğeni durumu
--   share              → Paylaşma
--   download           → İndirme
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE user_events (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    user_id         INT UNSIGNED        NOT NULL,
    event_type      VARCHAR(50)         NOT NULL,
    event_value     VARCHAR(255)            NULL,   -- music_id, album_id, arama terimi ...
    event_data      JSON                    NULL,   -- Ekstra bağlamsal veri
    page_url        VARCHAR(2048)           NULL,
    ip_address      VARCHAR(45)             NULL,
    session_id      VARCHAR(64)             NULL,
    occurred_at     DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    -- ⚠️ user_id → coremusic_auth.users.id: cross-DB FK

    INDEX idx_ue_user       (user_id),
    INDEX idx_ue_type       (event_type),
    INDEX idx_ue_value      (event_value),
    INDEX idx_ue_session    (session_id),
    INDEX idx_ue_occurred   (occurred_at DESC),
    INDEX idx_ue_page       (page_url(255))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- PERFORMANCE_METRICS — Uygulama/sunucu performans metrikleri
-- Normal Form : BCNF
-- Bağımlılık  : id → {metric_name, metric_value, source, tags_json, ...}
--
-- metric_name örnekleri:
--   api.response_time   → API yanıt süresi (ms)
--   db.query_time       → Veritabanı sorgu süresi (ms)
--   memory.usage        → Bellek kullanımı (MB)
--   cpu.load            → CPU yükü (yüzde)
--   cache.hit_rate      → Önbellek isabet oranı (0.00–1.00)
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE performance_metrics (
    id              BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
    metric_name     VARCHAR(100)        NOT NULL,
    metric_value    DECIMAL(12,4)       NOT NULL,
    source          VARCHAR(50)         NOT NULL DEFAULT 'app',
    -- source: app | db | cache | engine | download
    tags_json       JSON                    NULL,   -- {"endpoint":"/api/music","method":"GET"}
    sampled_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    INDEX idx_pm_name       (metric_name),
    INDEX idx_pm_source     (source),
    INDEX idx_pm_sampled    (sampled_at DESC),
    INDEX idx_pm_name_time  (metric_name, sampled_at DESC),

    CHECK (source IN ('app','db','cache','engine','download'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- DAILY_STATS — Günlük özet istatistikler (aggregate)
-- Normal Form : BCNF
-- Bağımlılık  : (stats_date, user_id) → {total_plays, total_listen_ms, ...}
--
-- Bu tablo, günlük cron job ile doldurulur (gece yarısı).
-- Yalnızca özet bilgiler — detay için page_views ve user_events tablosuna bakın.
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE daily_stats (
    id                  INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    stats_date          DATE                NOT NULL,
    user_id             INT UNSIGNED            NULL,   -- NULL = tüm kullanıcılar
    total_plays         INT UNSIGNED        NOT NULL DEFAULT 0,
    total_listen_ms     BIGINT UNSIGNED     NOT NULL DEFAULT 0,
    total_skips         INT UNSIGNED        NOT NULL DEFAULT 0,
    total_searches      INT UNSIGNED        NOT NULL DEFAULT 0,
    total_playlist_creates INT UNSIGNED     NOT NULL DEFAULT 0,
    total_playlist_adds INT UNSIGNED        NOT NULL DEFAULT 0,
    total_likes         INT UNSIGNED        NOT NULL DEFAULT 0,
    total_shares        INT UNSIGNED        NOT NULL DEFAULT 0,
    total_downloads     INT UNSIGNED        NOT NULL DEFAULT 0,
    total_unique_tracks INT UNSIGNED        NOT NULL DEFAULT 0,
    total_unique_artists INT UNSIGNED       NOT NULL DEFAULT 0,
    total_page_views    INT UNSIGNED        NOT NULL DEFAULT 0,
    avg_session_minutes DECIMAL(8,2)            NULL,
    top_genre           VARCHAR(50)             NULL,
    created_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                            ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_ds_user_date   (stats_date, user_id),
    -- ⚠️ user_id → coremusic_auth.users.id: cross-DB FK (NULL için geçerli değil)

    INDEX idx_ds_date       (stats_date DESC),
    INDEX idx_ds_user       (user_id),
    INDEX idx_ds_plays      (total_plays DESC),
    INDEX idx_ds_listen     (total_listen_ms DESC)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ══════════════════════════════════════════════════════════════════════════════
-- End of coremusic_analytics schema
