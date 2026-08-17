-- ══════════════════════════════════════════════════════════════
-- coremusic_cms — İÇERİK YÖNETİM SİSTEMİ VERİTABANI
-- Version     : 8.0.0
-- BCNF        : Yes
-- Author      : CoreMusic Data Engineer
-- Date        : 2026-08-10
-- Tables      : 8
-- Engine      : InnoDB
-- Charset     : utf8mb4
-- Collation   : utf8mb4_unicode_ci
-- UUID        : INT UNSIGNED + AUTO_INCREMENT
-- Soft Delete : is_deleted + deleted_at
-- ══════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS coremusic_cms
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE coremusic_cms;

-- ──────────────────────────────────────────────────────────────────────────────
-- CMS_PAGES — Statik sayfalar
-- Normal Form : BCNF — slug UNIQUE
-- status      : draft | published | archived
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE cms_pages (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    author_id       INT UNSIGNED        NOT NULL,
    title           VARCHAR(500)        NOT NULL,
    slug            VARCHAR(500)        NOT NULL,
    content         LONGTEXT            NOT NULL,
    excerpt         TEXT                    NULL,
    meta_title      VARCHAR(255)            NULL,
    meta_description VARCHAR(500)           NULL,
    cover_image     VARCHAR(2048)           NULL,
    status          VARCHAR(20)         NOT NULL DEFAULT 'draft',
    is_nav_visible  TINYINT(1)          NOT NULL DEFAULT 1,
    nav_order       INT UNSIGNED        NOT NULL DEFAULT 0,
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE  KEY uq_cp_slug     (slug),
    INDEX idx_cp_author     (author_id),
    INDEX idx_cp_status     (status),
    INDEX idx_cp_nav        (is_nav_visible, nav_order),
    CHECK (status IN ('draft', 'published', 'archived'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- BLOG_POSTS — Blog/duyuru yazilari
-- Normal Form : BCNF — slug UNIQUE
-- status      : draft | published | archived
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE blog_posts (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    author_id       INT UNSIGNED        NOT NULL,
    category_id     INT UNSIGNED            NULL,
    title           VARCHAR(500)        NOT NULL,
    slug            VARCHAR(500)        NOT NULL,
    content         LONGTEXT            NOT NULL,
    excerpt         TEXT                    NULL,
    meta_title      VARCHAR(255)            NULL,
    meta_description VARCHAR(500)           NULL,
    cover_image     VARCHAR(2048)           NULL,
    status          VARCHAR(20)         NOT NULL DEFAULT 'draft',
    published_at    DATETIME                NULL,
    view_count      INT UNSIGNED        NOT NULL DEFAULT 0,
    is_featured     TINYINT(1)          NOT NULL DEFAULT 0,
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE  KEY uq_bp_slug     (slug),
    INDEX idx_bp_author     (author_id),
    INDEX idx_bp_category   (category_id),
    INDEX idx_bp_status     (status),
    INDEX idx_bp_published  (published_at DESC),
    INDEX idx_bp_featured   (is_featured),
    CHECK (status IN ('draft', 'published', 'archived'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- BLOG_CATEGORIES — Blog kategorileri
-- Normal Form : BCNF — slug UNIQUE
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE blog_categories (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    name            VARCHAR(255)        NOT NULL,
    slug            VARCHAR(255)        NOT NULL,
    description     TEXT                    NULL,
    parent_id       INT UNSIGNED            NULL,
    post_count      INT UNSIGNED        NOT NULL DEFAULT 0,
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE  KEY uq_bc_slug     (slug),
    INDEX idx_bc_parent     (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- BLOG_TAGS — Blog etiketleri
-- Normal Form : BCNF — slug UNIQUE
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE blog_tags (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    name            VARCHAR(255)        NOT NULL,
    slug            VARCHAR(255)        NOT NULL,
    post_count      INT UNSIGNED        NOT NULL DEFAULT 0,
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE  KEY uq_bt_slug     (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- BLOG_POST_TAGS — Yazi-etiket eslestirme (junction)
-- Normal Form : BCNF — (post_id, tag_id) UNIQUE
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE blog_post_tags (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    post_id         INT UNSIGNED        NOT NULL,
    tag_id          INT UNSIGNED        NOT NULL,
    PRIMARY KEY (id),
    UNIQUE  KEY uq_bpt_pair    (post_id, tag_id),
    INDEX idx_bpt_tag       (tag_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- CMS_MEDIA_ASSETS — Medya dosyalari
-- Normal Form : BCNF — file_path UNIQUE
-- file_type    : image | video | audio | document
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE cms_media_assets (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    uploader_id     INT UNSIGNED        NOT NULL,
    file_name       VARCHAR(500)        NOT NULL,
    file_path       VARCHAR(500)        NOT NULL,
    file_type       VARCHAR(20)         NOT NULL,
    mime_type       VARCHAR(100)            NULL,
    file_size_bytes BIGINT UNSIGNED        NULL,
    width_px        INT UNSIGNED            NULL,
    height_px       INT UNSIGNED            NULL,
    alt_text        VARCHAR(500)            NULL,
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE  KEY uq_cm_path    (file_path),
    INDEX idx_cm_type       (file_type),
    INDEX idx_cm_uploader   (uploader_id),
    CHECK (file_type IN ('image', 'video', 'audio', 'document'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- CMS_FAQS — SSS kayitlari
-- Normal Form : BCNF — id -> {question, answer, ...}
-- category     : genel | teknik | guvenlik | odeme | hesap | diger
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE cms_faqs (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    question        VARCHAR(1000)       NOT NULL,
    answer          TEXT                NOT NULL,
    category        VARCHAR(50)         NOT NULL DEFAULT 'genel',
    sort_order      INT UNSIGNED        NOT NULL DEFAULT 0,
    is_published    TINYINT(1)          NOT NULL DEFAULT 1,
    view_count      INT UNSIGNED        NOT NULL DEFAULT 0,
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_cf_category    (category),
    INDEX idx_cf_order       (sort_order),
    INDEX idx_cf_published   (is_published),
    CHECK (category IN ('genel', 'teknik', 'guvenlik', 'odeme', 'hesap', 'diger'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ──────────────────────────────────────────────────────────────────────────────
-- CMS_BANNER_SLIDES — Banner/slides yonetimi
-- Normal Form : BCNF — id -> {title, image_url, ...}
-- position    : hero | sidebar | footer | popup
-- ──────────────────────────────────────────────────────────────────────────────
CREATE TABLE cms_banner_slides (
    id              INT UNSIGNED        NOT NULL AUTO_INCREMENT,
    title           VARCHAR(255)        NOT NULL,
    subtitle        VARCHAR(500)            NULL,
    image_url       VARCHAR(2048)       NOT NULL,
    link_url        VARCHAR(2048)           NULL,
    position        VARCHAR(30)         NOT NULL DEFAULT 'hero',
    sort_order      INT UNSIGNED        NOT NULL DEFAULT 0,
    start_date      DATETIME                NULL,
    end_date        DATETIME                NULL,
    is_active       TINYINT(1)          NOT NULL DEFAULT 1,
    click_count     INT UNSIGNED        NOT NULL DEFAULT 0,
    is_deleted      TINYINT(1)          NOT NULL DEFAULT 0,
    created_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
                                        ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_cbs_position   (position),
    INDEX idx_cbs_order      (sort_order),
    INDEX idx_cbs_active     (is_active),
    INDEX idx_cbs_dates      (start_date, end_date),
    CHECK (position IN ('hero', 'sidebar', 'footer', 'popup'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ══════════════════════════════════════════════════════════════
-- End of coremusic_cms schema
-- ══════════════════════════════════════════════════════════════
