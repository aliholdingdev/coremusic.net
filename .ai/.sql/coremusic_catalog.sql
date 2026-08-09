-- ══════════════════════════════════════════════════════════════════════════════
-- coremusic_catalog — KATEGORİ & LOOKUP TABLOLARI
-- COREMUSIC DB v6.0 | Mart 2026 | MySQL 8.x InnoDB | utf8mb4_turkish_ci
-- NF: BCNF
-- ══════════════════════════════════════════════════════════════════════════════
--
-- [v5 → v6 DEĞİŞİKLİKLER]
-- 1. singer_role_list  : display_name VARCHAR(100) NULL eklendi (Türkçe gösterim)
-- 2. album_types       : display_name VARCHAR(100) NULL eklendi (Türkçe gösterim)
-- 3. playlist_types    : display_name VARCHAR(100) NULL eklendi (Türkçe gösterim)
-- 4. INSERT bloklarındaki inline Türkçe yorum satırları temizlendi (syntax hatası)
-- 5. INSERT'e display_name değerleri eklendi
-- ══════════════════════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS coremusic_catalog
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_turkish_ci;

USE coremusic_catalog;


-- ──────────────────────────────────────────────────────────────────────────────
-- GENRE_LIST — Müzik türleri
-- Normal Form : BCNF
-- Bağımlılık  : id → {genre_name, slug, parent_id, is_active}
-- slug        : URL-safe isim. "Türk Pop" → "turk-pop"  (admin panelde otomatik üretilir)
-- parent_id   : Alt tür hiyerarşisi. Pop(1) → K-Pop(parent_id=1)
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE genre_list (
    id          SMALLINT UNSIGNED   NOT NULL AUTO_INCREMENT,
    genre_name  VARCHAR(100)        NOT NULL,
    slug        VARCHAR(120)        NOT NULL,
    parent_id   SMALLINT UNSIGNED       NULL DEFAULT NULL,
    is_active   TINYINT(1)          NOT NULL DEFAULT 1,

    created_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                    ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_genre_slug   (slug),
    UNIQUE  KEY uq_genre_name   (genre_name),
    FOREIGN KEY fk_genre_parent (parent_id)
        REFERENCES genre_list   (id) ON DELETE SET NULL,

    INDEX idx_genre_parent      (parent_id),
    INDEX idx_genre_active      (is_active)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- SINGER_ROLE_LIST — Sanatçı türleri (lookup)
-- Normal Form : BCNF
-- role_name   : Sistem anahtarı (İngilizce, kod içinde kullanılır)
-- display_name: Kullanıcıya gösterilen Türkçe/yerelleştirilmiş ad
-- Değerler    : solo | band | duo | rapper | dj | orchestra | choir | producer | composer
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE singer_role_list (
    id           TINYINT UNSIGNED    NOT NULL AUTO_INCREMENT,
    role_name    VARCHAR(50)         NOT NULL,
    display_name VARCHAR(100)            NULL,   -- Türkçe/yerelleştirilmiş gösterim adı

    created_at   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                     ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_role_name (role_name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- ALBUM_TYPES — Albüm türleri (lookup)
-- Normal Form : BCNF
-- type_name   : Sistem anahtarı (İngilizce, kod içinde kullanılır)
-- display_name: Kullanıcıya gösterilen Türkçe/yerelleştirilmiş ad
-- Değerler    : album | single | ep | compilation | live | soundtrack | remix_album
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE album_types (
    id           TINYINT UNSIGNED    NOT NULL AUTO_INCREMENT,
    type_name    VARCHAR(50)         NOT NULL,
    display_name VARCHAR(100)            NULL,   -- Türkçe/yerelleştirilmiş gösterim adı

    created_at   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                     ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_album_type_name (type_name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- PLAYLIST_TYPES — Playlist türleri (lookup)
-- Normal Form : BCNF
-- type_name   : Sistem anahtarı (İngilizce, kod içinde kullanılır)
-- display_name: Kullanıcıya gösterilen Türkçe/yerelleştirilmiş ad
-- Değerler    : personal | editorial | radio | algorithmic | collaborative | device | system
-- NOT: 'device' → USB/disk export için sistem tarafından otomatik oluşturulan tür
-- NOT: 'system' → Otomatik oluşturulan sistem listeleri (Tekrar Müzik, Yeni Sevilen vb.)
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE playlist_types (
    id           TINYINT UNSIGNED    NOT NULL AUTO_INCREMENT,
    type_name    VARCHAR(50)         NOT NULL,
    display_name VARCHAR(100)            NULL,   -- Türkçe/yerelleştirilmiş gösterim adı

    created_at   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                     ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_ptype_name (type_name)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ──────────────────────────────────────────────────────────────────────────────
-- INSTRUMENT_TYPES — Enstrüman & Ekipman türleri (lookup)
-- Normal Form : BCNF
-- Bağımlılık  : id → {instrument_name, instrument_model, brand, category, display_name}
--
-- [v7 YENİ] — v6'da yanlışlıkla singer_role_list'e eklenen org-korg-* ve
-- org-yamaha-* kayıtları bu tabloya taşındı.
--
-- category değerleri:
--   keyboard     → Klavye/org enstrümanları (Korg Pa serisi, Yamaha PSR/Genos)
--   string       → Bağlama, gitar, keman vb.
--   wind         → Klarnet, ney, zurna vb.
--   percussion   → Davul, darbuka vb.
--   electronic   → Synthesizer, DAW controller vb.
--   dj_equipment → DJ mixer, turntable vb.
--   other        → Diğer
--
-- Kullanım:
--   coremusic_musics.music_singer ↔ instrument_types (M2M) → ilerleyen sürümde
--   Şu an lookup referans olarak hazır tutulur.
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE instrument_types (
    id               SMALLINT UNSIGNED   NOT NULL AUTO_INCREMENT,
    instrument_key   VARCHAR(80)         NOT NULL,   -- Sistem anahtarı: org-korg-pa3x
    instrument_name  VARCHAR(150)        NOT NULL,   -- "Korg Pa 3x"
    brand            VARCHAR(100)            NULL,   -- "Korg", "Yamaha"
    model_series     VARCHAR(100)            NULL,   -- "Pa serisi", "PSR-A serisi"
    category         VARCHAR(30)         NOT NULL DEFAULT 'other',
    -- keyboard | string | wind | percussion | electronic | dj_equipment | other
    display_name     VARCHAR(200)            NULL,   -- "ORG Korg Pa 3x"
    is_active        TINYINT(1)          NOT NULL DEFAULT 1,

    created_at       DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at       DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                         ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE  KEY uq_it_key       (instrument_key),
    INDEX idx_it_brand          (brand),
    INDEX idx_it_category       (category),
    INDEX idx_it_active         (is_active),

    CHECK (category IN ('keyboard','string','wind','percussion','electronic','dj_equipment','other'))

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;


-- ══════════════════════════════════════════════════════════════════════════════
-- VARSAYILAN VERİLER
-- ══════════════════════════════════════════════════════════════════════════════

-- ── Singer rolleri ─────────────────────────────────────────────────────────────
-- role_name = sistem anahtarı (İngilizce), display_name = Türkçe gösterim
INSERT INTO singer_role_list (role_name, display_name) VALUES
    ('solo',        'Solo Sanatçı'),
    ('band',        'Grup'),
    ('duo',         'İkili'),
    ('rapper',      'Rapper'),
    ('dj',          'DJ'),
    ('orchestra',   'Orkestra'),
    ('choir',       'Koro'),
    ('producer',    'Prodüktör'),
    ('composer',    'Besteci');

-- ── Albüm türleri ──────────────────────────────────────────────────────────────
-- live intentionally kept in English as 'live' (evrensel tanınan terim)
INSERT INTO album_types (type_name, display_name) VALUES
    ('album',       'Albüm'),
    ('single',      'Single'),
    ('ep',          'EP'),
    ('compilation', 'Derleme'),
    ('live',        'Canlı / Live'),
    ('soundtrack',  'Film Müziği'),
    ('remix_album', 'Remix Albüm');

-- ── Playlist türleri ───────────────────────────────────────────────────────────
INSERT INTO playlist_types (type_name, display_name) VALUES
    ('personal',        'Kişisel Liste'),
    ('editorial',       'Editöryal Liste'),
    ('radio',           'Radyo'),
    ('algorithmic',     'Otomatik Liste'),
    ('system',          'Sistem Tarafından Otomatik Liste'),
    ('collaborative',   'Ortak Liste'),
    ('device',          'Cihaz Listesi');

-- ── Müzik türleri ──────────────────────────────────────────────────────────────
INSERT INTO genre_list (genre_name, slug, parent_id) VALUES
    ('Pop',              'pop',         NULL),
    ('Rock',             'rock',        NULL),
    ('Hip-Hop',          'hip-hop',     NULL),
    ('R&B',              'rnb',         NULL),
    ('Electronic',       'electronic',  NULL),
    ('Jazz',             'jazz',        NULL),
    ('Classical',        'classical',   NULL),
    ('Country',          'country',     NULL),
    ('Türk Pop',         'turk-pop',    NULL),
    ('Arabesk',          'arabesk',     NULL),
    ('Türk Halk Müziği', 'turk-halk',   NULL),
    ('Türk Sanat Müziği','turk-sanat',  NULL),
    ('K-Pop',            'k-pop',       1),
    ('House',            'house',       5),
    ('Latin',            'latin',       NULL),
    ('Reggae',           'reggae',      NULL);

-- ── Enstrüman / Ekipman türleri ────────────────────────────────────────────────
-- [v7 YENİ]: v6'da singer_role_list'e yanlışlıkla eklenen org-* kayıtları
-- doğru tabloya taşındı.
-- instrument_key = sistem anahtarı (kod içinde kullanılır)
-- display_name   = UI'da gösterilen ad
INSERT INTO instrument_types (instrument_key, instrument_name, brand, model_series, category, display_name) VALUES
    -- Korg Pa serisi
    ('org-korg-pa3x',       'Korg Pa 3x',       'Korg',   'Pa serisi',   'keyboard', 'ORG Korg Pa 3x'),
    ('org-korg-pa4x',       'Korg Pa 4x',       'Korg',   'Pa serisi',   'keyboard', 'ORG Korg Pa 4x'),
    ('org-korg-pa5x',       'Korg Pa 5x',       'Korg',   'Pa serisi',   'keyboard', 'ORG Korg Pa 5x'),
    ('org-korg-pa3xle',     'Korg Pa 3x LE',    'Korg',   'Pa serisi',   'keyboard', 'ORG Korg Pa 3x LE'),
    ('org-korg-pa700',      'Korg Pa 700',      'Korg',   'Pa serisi',   'keyboard', 'ORG Korg Pa 700'),
    ('org-korg-pa600',      'Korg Pa 600',      'Korg',   'Pa serisi',   'keyboard', 'ORG Korg Pa 600'),
    ('org-korg-pa500',      'Korg Pa 500',      'Korg',   'Pa serisi',   'keyboard', 'ORG Korg Pa 500'),
    ('org-korg-pa300',      'Korg Pa 300',      'Korg',   'Pa serisi',   'keyboard', 'ORG Korg Pa 300'),
    ('org-korg-pa1000',     'Korg Pa 1000',     'Korg',   'Pa serisi',   'keyboard', 'ORG Korg Pa 1000'),
    -- Yamaha OR / PSR / Genos serisi
    ('org-yamaha-or700',    'Yamaha OR 700',    'Yamaha', 'OR serisi',   'keyboard', 'ORG Yamaha Or 700'),
    ('org-yamaha-psr-a5000','Yamaha PSR-A5000',  'Yamaha', 'PSR-A serisi','keyboard', 'ORG Yamaha Psr A5000'),
    ('org-yamaha-psr-a6000','Yamaha PSR-A6000',  'Yamaha', 'PSR-A serisi','keyboard', 'ORG Yamaha Psr A6000'),
    ('org-yamaha-psr-a3000','Yamaha PSR-A3000',  'Yamaha', 'PSR-A serisi','keyboard', 'ORG Yamaha Psr A3000'),
    ('org-yamaha-psr-a2000','Yamaha PSR-A2000',  'Yamaha', 'PSR-A serisi','keyboard', 'ORG Yamaha Psr A2000'),
    ('org-yamaha-genos2',   'Yamaha Genos 2',   'Yamaha', 'Genos serisi','keyboard', 'ORG Yamaha Genos 2'),
    ('org-yamaha-genos1',   'Yamaha Genos',     'Yamaha', 'Genos serisi','keyboard', 'ORG Yamaha Genos'),
    ('org-yamaha-sx900',    'Yamaha SX900',     'Yamaha', 'SX serisi',   'keyboard', 'ORG Yamaha SX900');