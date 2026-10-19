-- =========================================================================
-- CoreMusic — CANLI DUMP (MySQL → .ai/.sql/mysql SSOT)
-- Kaynak : canlı MySQL · birebir SHOW CREATE · Tarih: 2026-10-07
-- Şema   : TAM (tüm tablolar/views) · Üreteç: full_dump.php
-- VERİ   : YOK — kullanıcı/davranış/credential verisi bilinçli yazılmaz (KVKK/REDACTED)
-- Not    : önceki elle-yazılmış başlık/BCNF comment'leri git geçmişindedir.
-- =========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_albums`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_albums`;

CREATE TABLE `album_credits` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz kredi tanımlayıcı',
  `album_id` binary(16) NOT NULL COMMENT 'Albüm ID',
  `credit_type` enum('producer','engineer','mixer','mastering','artwork','photography','other') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kredi türü',
  `person_name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kişi adı (CoreMusic dışı ise)',
  `person_id` binary(16) DEFAULT NULL COMMENT 'Kişi ID (CoreMusic üyesi ise)',
  `credit_order` int NOT NULL DEFAULT '0' COMMENT 'Kredi sırası',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  PRIMARY KEY (`id`),
  KEY `idx_album_credits_album` (`album_id`) COMMENT 'Albüm filtresi',
  KEY `idx_album_credits_type` (`credit_type`) COMMENT 'Kredi türü filtresi',
  CONSTRAINT `fk_album_credits_album` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Albüm kredileri tablosu';

CREATE TABLE `album_discs` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz disk tanımlayıcı',
  `album_id` binary(16) NOT NULL COMMENT 'Albüm ID',
  `disc_number` int NOT NULL COMMENT 'Disk numarası',
  `disc_title` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Disk başlığı (opsiyonel)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_album_discs_unique` (`album_id`,`disc_number`) COMMENT 'Benzersiz albüm-disk çifti',
  CONSTRAINT `fk_album_discs_album` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Albüm diskleri tablosu';

CREATE TABLE `album_genres` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz ilişki tanımlayıcı',
  `album_id` binary(16) NOT NULL COMMENT 'Albüm ID',
  `genre_id` binary(16) NOT NULL COMMENT 'Tür ID',
  `is_primary` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Birincil tür mü?',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_album_genres_unique` (`album_id`,`genre_id`) COMMENT 'Benzersiz albüm-tür çifti',
  KEY `idx_album_genres_genre` (`genre_id`) COMMENT 'Tür filtresi',
  CONSTRAINT `fk_album_genres_album` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Albüm-tür ilişkisi tablosu';

CREATE TABLE `album_stats` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz istatistik tanımlayıcı',
  `album_id` binary(16) NOT NULL COMMENT 'Albüm ID',
  `daily_plays` int NOT NULL DEFAULT '0' COMMENT 'Günlük çalma sayısı',
  `weekly_plays` int NOT NULL DEFAULT '0' COMMENT 'Haftalık çalma sayısı',
  `monthly_plays` int NOT NULL DEFAULT '0' COMMENT 'Aylık çalma sayısı',
  `total_plays` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam çalma sayısı',
  `daily_downloads` int NOT NULL DEFAULT '0' COMMENT 'Günlük indirme sayısı',
  `total_downloads` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam indirme sayısı',
  `popularity_score` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Popülerlik puanı',
  `stats_date` date NOT NULL COMMENT 'İstatistik tarihi',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Güncellenme zamanı',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_album_stats_unique` (`album_id`,`stats_date`) COMMENT 'Benzersiz albüm-tarih çifti',
  KEY `idx_album_stats_popularity` (`popularity_score`) COMMENT 'Popülerlik sıralaması',
  CONSTRAINT `fk_album_stats_album` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Albüm istatistikleri tablosu';

CREATE TABLE `albums` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 - Benzersiz albüm tanımlayıcı',
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Albüm başlığı',
  `slug` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'URL dostu başlık',
  `artist_id` binary(16) NOT NULL COMMENT 'Sanatçı ID',
  `album_type` enum('album','ep','single','compilation','live','remix','soundtrack') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'album' COMMENT 'Albüm türü',
  `genre_id` binary(16) DEFAULT NULL COMMENT 'Birincil tür ID',
  `release_date` date DEFAULT NULL COMMENT 'Yayın tarihi',
  `record_label` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Plak şirketi',
  `upc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Evrensel Ürün Kodu',
  `cover_art_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Kapak görseli URL',
  `total_discs` int NOT NULL DEFAULT '1' COMMENT 'Toplam disk sayısı',
  `total_tracks` int NOT NULL DEFAULT '0' COMMENT 'Toplam parça sayısı',
  `total_duration_sec` int DEFAULT NULL COMMENT 'Toplam süre (saniye)',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Doğrulanmış albüm mü?',
  `play_count` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam çalma sayısı',
  `like_count` bigint NOT NULL DEFAULT '0' COMMENT 'Toplam beğeni sayısı',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Oluşturulma zamanı',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Güncellenme zamanı',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Yumuşak silme bayrağı',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamanı',
  PRIMARY KEY (`id`),
  KEY `idx_albums_artist` (`artist_id`) COMMENT 'Sanatçı filtresi',
  KEY `idx_albums_type` (`album_type`) COMMENT 'Albüm türü filtresi',
  KEY `idx_albums_release` (`release_date`) COMMENT 'Yayın tarihi sıralaması',
  FULLTEXT KEY `idx_albums_fulltext` (`title`) COMMENT 'Tam metin araması'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Albümler tablosu';

CREATE TABLE `labels` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Etiket adi (albums.record_label buraya tasinacak)',
  `country_code` char(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ISO 3166-1 alpha-2',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_labels_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Plak sirketleri lookup (3NF — karar #5/#10)';

CREATE TABLE `media` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `release_id` binary(16) NOT NULL COMMENT 'FK -> releases.id',
  `position` int unsigned NOT NULL DEFAULT '1' COMMENT 'Disk pozisyonu (1 = ilk disk)',
  `format` enum('cd','vinyl','cassette','digital','bluray','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Baski formati (karma ENUM — karar #5)',
  `title` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Disk basligi (opsiyonel)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_media_rel_pos` (`release_id`,`position`),
  CONSTRAINT `fk_media_release` FOREIGN KEY (`release_id`) REFERENCES `releases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Medium — release icindeki disk (MusicBrainz hizali — karar #10)';

CREATE TABLE `release_groups` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Grup adi',
  `type_id` binary(16) DEFAULT NULL COMMENT 'FK -> coremusic_catalog.catalog_album_types.id (karar #5: domain lookup)',
  `legacy_album_id` binary(16) DEFAULT NULL COMMENT 'TAŞIMA İZİ: albums.id (backfill FAZ 2b; FK YOK)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_rg_type` (`type_id`),
  KEY `idx_rg_legacy` (`legacy_album_id`),
  KEY `idx_rg_title` (`title`(100)),
  CONSTRAINT `fk_rg_type` FOREIGN KEY (`type_id`) REFERENCES `coremusic_catalog`.`catalog_album_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Release Group — soyut album/varlik grubu (MusicBrainz hizali — karar #10)';

CREATE TABLE `release_labels` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `release_id` binary(16) NOT NULL COMMENT 'FK -> releases.id',
  `label_id` binary(16) NOT NULL COMMENT 'FK -> labels.id',
  `catalog_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Etikete ozel katalog no',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_rel_label` (`release_id`,`label_id`),
  KEY `idx_rl_label` (`label_id`),
  CONSTRAINT `fk_rl_label` FOREIGN KEY (`label_id`) REFERENCES `labels` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rl_release` FOREIGN KEY (`release_id`) REFERENCES `releases` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Release-Label N:M junction (MusicBrainz hizali — karar #10)';

CREATE TABLE `releases` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `release_group_id` binary(16) NOT NULL COMMENT 'FK -> release_groups.id',
  `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Baski adi',
  `catalog_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Katalog numarasi',
  `release_date` date DEFAULT NULL COMMENT 'Yayin tarihi (opsiyonel)',
  `total_media` int unsigned DEFAULT NULL COMMENT 'Bildirilen toplam disk sayisi (albums.total_disctasinmasi dogrulama)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_rel_group` (`release_group_id`),
  KEY `idx_rel_catno` (`catalog_number`),
  KEY `idx_rel_date` (`release_date`),
  CONSTRAINT `fk_rel_group` FOREIGN KEY (`release_group_id`) REFERENCES `release_groups` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Release — baski/urun (MusicBrainz hizali — karar #10)';

CREATE TABLE `tracks` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `medium_id` binary(16) NOT NULL COMMENT 'FK -> media.id',
  `recording_id` binary(16) NOT NULL COMMENT 'FK -> coremusic_musics.recordings.id (cross-DB, karar #4)',
  `position` int unsigned NOT NULL DEFAULT '1' COMMENT 'Pozisyonda sira',
  `title_override` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Bu baskidaki ad (recording adindan farkliysa)',
  `artist_credit_id` binary(16) DEFAULT NULL COMMENT 'FK -> coremusic_musics.artist_credit.id (opsiyonel override; cross-DB, karar #4)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_track_medium_pos` (`medium_id`,`position`),
  KEY `idx_track_recording` (`recording_id`),
  KEY `idx_track_credit` (`artist_credit_id`),
  CONSTRAINT `fk_track_credit` FOREIGN KEY (`artist_credit_id`) REFERENCES `coremusic_musics`.`artist_credit` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_track_medium` FOREIGN KEY (`medium_id`) REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_track_recording` FOREIGN KEY (`recording_id`) REFERENCES `coremusic_musics`.`recordings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Track — recording''in medium icindeki tekrari (MusicBrainz hizali — karar #10)';

SET FOREIGN_KEY_CHECKS = 1;
