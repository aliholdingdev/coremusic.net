-- ===========================================================================
-- fix_constraints_20261021.sql — BCNF Denetimi Düzeltme Migrasyonu (TÜM GRUPLAR)
-- ===========================================================================
-- Kaynak: 2026-10-21 tam BCNF denetimi (187 tablo / 20 dump; 4 grup subagent +
--         orkestratör entegrasyonu + web best-practice research)
-- Kapsam: A (logs/download/api/neva) · B (system/studio/patch/wireless)
--         · C (auth/catalog/cms/novasearch) · D (albums/ai/media/media_catalog)
-- Prensip: FORWARD-ONLY (down() YOK — ADR-007/014) · PK/stil DEĞİŞTİRİLMEZ
--          · mevcut sütun ASLA silinmez · cross-DB FK YAZILMAZ (uygulama
--          seviyesi doğrulama — web research §6: "works but discouraged")
-- Pre-check: 2026-10-21 localhost — 33/33 kontrol orphan/dup = 0 (GÜVENLİ).
--            Yeniden uygulamadan önce bu dosyanın BÖLÜM 0 sorguları tekrar
--            çalıştırılabilir (salt-okunur).
-- UYGULANDI: 2026-10-21 localhost — 68 ifade OK · 0 hata (ilk tur 54/63 +
--            14 ifadelik REVİZYON; hatalı 3 desen dosyaya işlendi:
--            3109 generated↔auto-increment yasağı → deleted_at + TO_SECONDS;
--            search_logs/cache_entries → novasearch; idx_bpt_tag drop → kaldırıldı
--            çünkü fk_bpt_tag'ın backing index'i). Doğrulama: 77/77 GECTI
--            (verify_fix_constraints.php · information_schema teyitli).
--            Yedek: C:\temp\opencode\backup_20261021\ (15 DB, mysqldump).
-- MySQL: 8.4 InnoDB · ALGORITHM sunucuya bırakıldı (tablolar küçük);
--        üretimde bakım penceresi + ALGORITHM=INPLACE, LOCK=NONE dene.
-- Yeni sütunlar (research §2/§3 deseni): deleted_key / plural_key / active_key
--        / eq_name_key = STORED generated column (soft-delete + UNIQUE
--        çelişkisini şema seviyesinde çözer — uygulama kodu DEĞİŞMEZ).
-- Uygulama: tek tek ALTER (implicit-commit); hata = loglanır, devam edilir
--        (her ALTER bağımsız). Uygulayıcı: C:\temp\opencode\run_fix_constraints.php
-- ===========================================================================

-- ===========================================================================
-- BÖLÜM 0 — PRE-CHECK (salt-okunur; 2026-10-21 = 33/33 sonuç 0)
-- ===========================================================================
-- (Ayrıntılı 33 sorgu: precheck_constraints.php — bu dosyadan bağımsız çalışır;
--  sonuçlar 2026-10-21 log.md girişinde kayıtlı.)

-- ===========================================================================
-- BÖLÜM A — coremusic_logs · coremusic_api · coremusic_download · coremusic_neva
-- ===========================================================================

-- A-1 correlation_id index'leri (4 log tablosu — WHERE correlation_id=? full-scan)
ALTER TABLE `coremusic_logs`.`log_activity`    ADD INDEX `idx_la_correlation_id`   (`correlation_id`);
ALTER TABLE `coremusic_logs`.`log_performance` ADD INDEX `idx_lp_correlation_id`   (`correlation_id`);
ALTER TABLE `coremusic_logs`.`log_security`    ADD INDEX `idx_ls_correlation_id`   (`correlation_id`);
ALTER TABLE `coremusic_logs`.`log_system`      ADD INDEX `idx_lsys_correlation_id` (`correlation_id`);

-- A-2 audit_logs composite index (entity_type+entity_id hedef-sorgusu)
ALTER TABLE `coremusic_logs`.`audit_logs` ADD INDEX `idx_audit_logs_entity` (`entity_type`, `entity_id`);

-- A-3 analytics_storage tekillik (storage_type, storage_path)
ALTER TABLE `coremusic_logs`.`analytics_storage` ADD UNIQUE KEY `uk_storage_type_path` (`storage_type`, `storage_path`);

-- A-4 rate_limit_logs tekillik (pencere-bazlı dup koruması)
ALTER TABLE `coremusic_logs`.`rate_limit_logs` ADD UNIQUE KEY `uq_rate_limit_identifier_window` (`identifier`, `identifier_type`, `window_start`);

-- A-5 webhooks tekillik (çift tetikleme engeli) — url 255 prefix index
ALTER TABLE `coremusic_api`.`webhooks` ADD UNIQUE KEY `uq_wh_key_event_url` (`api_key_id`, `event_type`, `callback_url`(255));

-- A-6 download_cache mantıksal anahtar (source, source_id)
ALTER TABLE `coremusic_download`.`download_cache` ADD UNIQUE KEY `uq_dc_source_source_id` (`source`, `source_id`);

-- A-7 download_queue aktif-öge tekilliği — research §2 deseni (generated column)
--    NULL active_key = completed/failed → çoğalmaya izin verir; 1 = tek aktif kayıt
ALTER TABLE `coremusic_download`.`download_queue`
  ADD COLUMN `active_key` TINYINT UNSIGNED
    GENERATED ALWAYS AS (IF(`status` IN ('queued','processing'), 1, NULL)) STORED,
  ADD UNIQUE KEY `uq_dq_user_track_active` (`user_id`, `track_id`, `active_key`);

-- A-8 neva.eq_presets soft-delete + unique çelişkisi — research §2 deseni
--    (silinmiş satır aktif adı engellemez; eski UPDATE-rename çözümü yerine)
ALTER TABLE `coremusic_neva`.`eq_presets`
  ADD COLUMN `eq_name_key` VARCHAR(255)
    GENERATED ALWAYS AS (IF(`deleted_at` IS NULL, `preset_name`, NULL)) STORED;
ALTER TABLE `coremusic_neva`.`eq_presets`
  DROP INDEX `uq_eqp_user_name`,
  ADD UNIQUE KEY `uq_eqp_user_name` (`user_id`, `eq_name_key`);

-- A-9 neva redundant index drop (uq_* leading kolonu aynı sorguyu karşılar)
ALTER TABLE `coremusic_neva`.`dsp_settings`  DROP INDEX `idx_dsp_user`;
ALTER TABLE `coremusic_neva`.`routing_matrix` DROP INDEX `idx_rm_user`;
ALTER TABLE `coremusic_neva`.`eq_presets`     DROP INDEX `idx_eqp_user`;

-- ===========================================================================
-- BÖLÜM B — coremusic_studio · coremusic_system · coremusic_wireless
-- ===========================================================================

-- B-1 HIGH: session_equipment 2 FK (orphan pre-check B1/B2 = 0)
ALTER TABLE `coremusic_studio`.`session_equipment`
  ADD CONSTRAINT `fk_session_equipment_session`
    FOREIGN KEY (`session_id`) REFERENCES `coremusic_studio`.`studio_sessions` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_session_equipment_equipment`
    FOREIGN KEY (`equipment_id`) REFERENCES `coremusic_studio`.`studio_equipment` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

-- B-2 HIGH: studio_collaborators session_id FK (pre-check B3 = 0)
--    NOT: user_id (int unsigned) → auth.users.id (binary(16)) TİP UYUŞMAZLIĞI —
--    FK YAZILMAZ, veri taşıma ayrı faz (N3).
ALTER TABLE `coremusic_studio`.`studio_collaborators`
  ADD CONSTRAINT `fk_studio_collaborators_session`
    FOREIGN KEY (`session_id`) REFERENCES `coremusic_studio`.`studio_sessions` (`id`)
    ON UPDATE CASCADE;

-- B-3 HIGH: studio_tracks FK + (session_id, track_number) tekillik
--    (pre-check B4/B5 = 0) — tek composite uq hem FK destekleyicisi hem sıra sorguları
ALTER TABLE `coremusic_studio`.`studio_tracks`
  DROP INDEX `idx_st_session`,
  DROP INDEX `idx_st_number`,
  ADD UNIQUE KEY `uq_studio_tracks_session_number` (`session_id`, `track_number`),
  ADD CONSTRAINT `fk_studio_tracks_session`
    FOREIGN KEY (`session_id`) REFERENCES `coremusic_studio`.`studio_sessions` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

-- B-4 i18n_translations: plural_form NULL deliği + soft-delete
--    DÜZELTME (2026-10-21 ilk uygulama hatası 3109): MySQL 8.4 generated column
--    auto-increment `id`'ye referans EDİLEMEZ → deleted_at kolonu eklenip
--    TO_SECONDS trick'e geçildi (system_config deseni); mevcut silinmiş
--    satırlar backfill ile etiketlenir.
ALTER TABLE `coremusic_system`.`i18n_translations`
  ADD COLUMN `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)';
UPDATE `coremusic_system`.`i18n_translations` SET `deleted_at` = CURRENT_TIMESTAMP WHERE `is_deleted` = 1 AND `deleted_at` IS NULL;
ALTER TABLE `coremusic_system`.`i18n_translations`
  ADD COLUMN `plural_key` VARCHAR(20)
    GENERATED ALWAYS AS (IF(`plural_form` IS NULL, '', `plural_form`)) STORED,
  ADD COLUMN `deleted_key` BIGINT
    GENERATED ALWAYS AS (IF(`is_deleted` = 0, 0, TO_SECONDS(`deleted_at`))) STORED;
ALTER TABLE `coremusic_system`.`i18n_translations`
  DROP INDEX `uq_it_pair`,
  ADD UNIQUE KEY `uq_it_pair` (`translation_key`, `locale`, `plural_key`, `deleted_key`);

-- B-5 i18n_ui_strings: soft-delete + unique (aynı desen — deleted_at eklenir)
ALTER TABLE `coremusic_system`.`i18n_ui_strings`
  ADD COLUMN `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)';
UPDATE `coremusic_system`.`i18n_ui_strings` SET `deleted_at` = CURRENT_TIMESTAMP WHERE `is_deleted` = 1 AND `deleted_at` IS NULL;
ALTER TABLE `coremusic_system`.`i18n_ui_strings`
  ADD COLUMN `deleted_key` BIGINT
    GENERATED ALWAYS AS (IF(`is_deleted` = 0, 0, TO_SECONDS(`deleted_at`))) STORED;
ALTER TABLE `coremusic_system`.`i18n_ui_strings`
  DROP INDEX `uq_ius_pair`,
  ADD UNIQUE KEY `uq_ius_pair` (`module`, `string_key`, `locale`, `deleted_key`);

-- B-6 system_config: soft-delete + unique (TO_SECONDS trick — deleted_at mevcut)
ALTER TABLE `coremusic_system`.`system_config`
  ADD COLUMN `deleted_key` BIGINT
    GENERATED ALWAYS AS (IF(`is_deleted` = 0, 0, TO_SECONDS(`deleted_at`))) STORED;
ALTER TABLE `coremusic_system`.`system_config`
  DROP INDEX `uk_config_key`,
  ADD UNIQUE KEY `uk_config_key` (`config_key`, `deleted_key`);

-- B-7 system_settings: aynı desen
ALTER TABLE `coremusic_system`.`system_settings`
  ADD COLUMN `deleted_key` BIGINT
    GENERATED ALWAYS AS (IF(`is_deleted` = 0, 0, TO_SECONDS(`deleted_at`))) STORED;
ALTER TABLE `coremusic_system`.`system_settings`
  DROP INDEX `uk_system_settings_key`,
  ADD UNIQUE KEY `uk_system_settings_key` (`setting_key`, `deleted_key`);

-- B-8 studio_presets: soft-delete + unique
--    DÜZELTME (3109 — auto-increment `id` referansı yasak): deleted_at eklenir
ALTER TABLE `coremusic_studio`.`studio_presets`
  ADD COLUMN `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)';
UPDATE `coremusic_studio`.`studio_presets` SET `deleted_at` = CURRENT_TIMESTAMP WHERE `is_deleted` = 1 AND `deleted_at` IS NULL;
ALTER TABLE `coremusic_studio`.`studio_presets`
  ADD COLUMN `deleted_key` BIGINT
    GENERATED ALWAYS AS (IF(`is_deleted` = 0, 0, TO_SECONDS(`deleted_at`))) STORED;
ALTER TABLE `coremusic_studio`.`studio_presets`
  DROP INDEX `uq_sp_pair`,
  ADD UNIQUE KEY `uq_sp_pair` (`user_id`, `name`, `deleted_key`);

-- B-9 system_eq_presets: (user_id, preset_name) tekillik yoktu (pre-check B6 = 0)
ALTER TABLE `coremusic_system`.`system_eq_presets`
  ADD COLUMN `deleted_key` BIGINT
    GENERATED ALWAYS AS (IF(`is_deleted` = 0, 0, `id`)) STORED,
  ADD UNIQUE KEY `uq_eq_presets_user_name` (`user_id`, `preset_name`, `deleted_key`);

-- B-10 system_file_manager: (parent_id, file_name) dosya-sistemi tekilliği (B7 = 0)
ALTER TABLE `coremusic_system`.`system_file_manager`
  ADD COLUMN `deleted_key` BIGINT
    GENERATED ALWAYS AS (IF(`is_deleted` = 0, 0, `id`)) STORED,
  ADD UNIQUE KEY `uq_file_manager_parent_name` (`parent_id`, `file_name`, `deleted_key`);

-- B-11 bluetooth_audio_profiles: (peer_id, profile_name) tekillik (B8 = 0)
ALTER TABLE `coremusic_wireless`.`bluetooth_audio_profiles`
  ADD UNIQUE KEY `uq_bt_audio_profiles_peer_name` (`peer_id`, `profile_name`);

-- B-12 LOW: notifications unread-list composite index
ALTER TABLE `coremusic_system`.`system_notifications`
  DROP INDEX `idx_notifications_user`,
  ADD INDEX `idx_notifications_user_read` (`user_id`, `is_read`);

-- B-13 LOW: redundant index drop (uq_* ön eki aynı sorguyu karşılar)
ALTER TABLE `coremusic_system`.`system_schema_versions` DROP INDEX `idx_schema_versions_db`;
ALTER TABLE `coremusic_patch`.`schema_versions`        DROP INDEX `idx_sv_db`;

-- ===========================================================================
-- BÖLÜM C — coremusic_auth · coremusic_cms · novasearch
-- ===========================================================================

-- C-1 credential_audit: append-only audit + CASCADE ihlali → RESTRICT + 2 FK
--    (pre-check C1/C2 = 0). fk_cau_vault adı dump L121 ile doğrulandı.
ALTER TABLE `coremusic_auth`.`credential_audit` DROP FOREIGN KEY `fk_cau_vault`;
ALTER TABLE `coremusic_auth`.`credential_audit`
  ADD CONSTRAINT `fk_cau_vault`
    FOREIGN KEY (`credential_id`) REFERENCES `coremusic_auth`.`credential_vault` (`id`)
    ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `coremusic_auth`.`credential_audit`
  ADD CONSTRAINT `fk_cau_key`
    FOREIGN KEY (`credential_key_id`) REFERENCES `coremusic_auth`.`credential_keys` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cau_user`
    FOREIGN KEY (`user_id`) REFERENCES `coremusic_auth`.`users` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE;

-- C-2 user_tokens.token_hash UNIQUE (pre-check C3 = 0)
ALTER TABLE `coremusic_auth`.`user_tokens`
  DROP INDEX `idx_tokens_hash`,
  ADD UNIQUE KEY `idx_tokens_hash` (`token_hash`);

-- C-3 cms FK iskeleti (pre-check C4/C5/C6 = 0; cms'te 0 FK vardı)
ALTER TABLE `coremusic_cms`.`blog_categories`
  ADD CONSTRAINT `fk_bc_parent`
    FOREIGN KEY (`parent_id`) REFERENCES `coremusic_cms`.`blog_categories` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE `coremusic_cms`.`blog_post_tags`
  ADD CONSTRAINT `fk_bpt_post`
    FOREIGN KEY (`post_id`) REFERENCES `coremusic_cms`.`blog_posts` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bpt_tag`
    FOREIGN KEY (`tag_id`) REFERENCES `coremusic_cms`.`blog_tags` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `coremusic_cms`.`blog_posts`
  ADD CONSTRAINT `fk_bp_category`
    FOREIGN KEY (`category_id`) REFERENCES `coremusic_cms`.`blog_categories` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

-- C-4 admin_activity_log composite (target_type, target_id)
ALTER TABLE `coremusic_auth`.`admin_activity_log`
  DROP INDEX `idx_admin_log_target`,
  ADD KEY `idx_admin_log_target` (`target_type`, `target_id`);

-- C-5 search_logs tarih-erişim index'i
--    DÜZELTME (tablo coremusic_auth'ta DEĞİL): search_logs = novasearch + coremusic_logs
--    (coremusic_logs.search_logs'ta hangi indexlerin olduğu ayrıca denetlenmeli —
--    Grup A kapsamındaydı, bu turda yalnız novasearch'a uygulanır)
CREATE INDEX `idx_search_logs_created` ON `novasearch`.`search_logs` (`created_at`);

-- C-6 blog_posts yayın-akış composite index
CREATE INDEX `idx_bp_status_published` ON `coremusic_cms`.`blog_posts` (`status`, `published_at` DESC);

-- C-7 blog_tags tekillik (pre-check C7 = 0)
ALTER TABLE `coremusic_cms`.`blog_tags` ADD UNIQUE KEY `uq_bt_name` (`name`);

-- C-8 redundant index drop (FK/UK ön eki karşılar)
--    DÜZELTME: cache_entries = novasearch (auth'ta değil);
--    idx_bpt_tag drop'u ÇIKARILDI — fk_bpt_tag'ın backing index'idir (hata 1553)
ALTER TABLE `coremusic_auth`.`credential_vault`    DROP INDEX `idx_credential_service`;
ALTER TABLE `coremusic_auth`.`user_assigned_roles` DROP INDEX `idx_assigned_roles_user`;
ALTER TABLE `novasearch`.`cache_entries`           DROP INDEX `idx_cache_key`;
ALTER TABLE `novasearch`.`channels` DROP INDEX `idx_youtube_id`;
ALTER TABLE `novasearch`.`playlists` DROP INDEX `idx_youtube_id`;
ALTER TABLE `novasearch`.`videos`    DROP INDEX `idx_youtube_id`;

-- ===========================================================================
-- BÖLÜM D — coremusic_ai · coremusic_media · coremusic_albums · media_catalog
-- ===========================================================================

-- D-1 HIGH: training_jobs → model_versions FK (pre-check D1 = 0)
ALTER TABLE `coremusic_ai`.`training_jobs`
  ADD CONSTRAINT `fk_training_jobs_model_versions`
    FOREIGN KEY (`model_version_id`) REFERENCES `coremusic_ai`.`model_versions` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE;

-- D-2 HIGH: device_* → devices FK (pre-check D2/D3/D4 = 0)
ALTER TABLE `coremusic_media`.`device_playlists`
  ADD CONSTRAINT `fk_device_playlists_devices`
    FOREIGN KEY (`device_id`) REFERENCES `coremusic_media`.`devices` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `coremusic_media`.`device_sync_history`
  ADD CONSTRAINT `fk_device_sync_history_devices`
    FOREIGN KEY (`device_id`) REFERENCES `coremusic_media`.`devices` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `coremusic_media`.`device_tracks`
  ADD CONSTRAINT `fk_device_tracks_devices`
    FOREIGN KEY (`device_id`) REFERENCES `coremusic_media`.`devices` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

-- D-3 HIGH: devices → device_types FK (pre-check D5 = 0)
ALTER TABLE `coremusic_media`.`devices`
  ADD CONSTRAINT `fk_devices_device_types`
    FOREIGN KEY (`device_type_id`) REFERENCES `coremusic_media`.`device_types` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE;

-- D-4 HIGH: albums.slug UK + index (pre-check D6 = 0; 2000B < 3072B limiti)
ALTER TABLE `coremusic_albums`.`albums` ADD UNIQUE KEY `uq_albums_slug` (`slug`);

-- D-5 MEDIUM: album_credits tekillik (pre-check D7 = 0; NULL person_id
--    MySQL'de eşit sayılmaz → person_name mükerreri uygulama katmanı)
ALTER TABLE `coremusic_albums`.`album_credits`
  ADD UNIQUE KEY `uq_album_credits_credit` (`album_id`, `credit_type`, `person_id`);

-- D-6 MEDIUM: media_access izin tekilliği (pre-check D8 = 0)
ALTER TABLE `coremusic_media`.`media_access`
  ADD UNIQUE KEY `uk_media_access_grant` (`user_id`, `file_key`, `access_type`);

-- D-7 MEDIUM: media_catalog.asset sıra + slug tekillik (pre-check D9/D10 = 0)
--    ⚠️ REBUILD NOTU: media_catalog `bin\scan.php --rebuild` ile yeniden üretilir;
--    bu 2 UK, üreteç + shared/database/migrations/media_catalog_migration.php
--    içine de YAZILMALI (takip işi — log.md).
ALTER TABLE `media_catalog`.`asset` ADD UNIQUE KEY `uq_asset_album_sira` (`album_id`, `sira`);
ALTER TABLE `media_catalog`.`asset` ADD UNIQUE KEY `uk_asset_slug` (`slug`);

-- D-8 LOW: ters-sorgu index'leri (FK eklenmedi; reverse lookup boşluğu)
ALTER TABLE `coremusic_media`.`device_playlists` ADD INDEX `idx_device_playlists_playlist_id` (`playlist_id`);
ALTER TABLE `coremusic_media`.`device_tracks`    ADD INDEX `idx_device_tracks_music_id` (`music_id`);

-- ===========================================================================
-- BÖLÜM NOT — BİLEREK UYGULANMAYAN (gerekçe + sonraki faz)
-- ===========================================================================
-- N1 [cross-DB FK · research §6] analytics_daily_* (genre/music/user),
--    audio_features/listening_features/recommendation_history/user_preference_profiles,
--    albums.artist_id/genre_id, media_metadata.music_id, devices.user_id,
--    blog_posts.author_id, cms_media_assets.uploader_id, cms_pages.author_id,
--    credential_keys.created_by → hedef DB/tipo doğrulanmadan FK YAZILMAZ;
--    uygulama seviyesi doğrulama (repository guard, ADR-002) tercih edilir.
-- N2 [TİP UYUŞMAZLIĞI] int unsigned user_id ↔ binary(16) users.id: 7 tablo
--    (studio_collaborators/presets/sessions, i18n_user_locale, cms author alanları)
--    → veri taşıma = ADR + repository değişikliği + ayrı faz.
-- N3 [1NF · research §4] user_roles.permissions, api_keys.scope/allowed_ips JSON
--    → ilişki tablosu kararı ADR bekliyor (JSON'da kalırsa multi-valued index eklenebilir).
-- N4 [BCNF · novasearch] playlists/videos channel_name transitive → app JOIN
--    geçişi + ADR sonrası DROP COLUMN (bu dosyada ASLA silinmez).
-- N5 [FK · partition] api_calls RANGE partition'lı — MySQL 8.4'te partitioned
--    tabloda FK desteklenmez → uygulama seviyesi doğrulama.
-- N6 [FK · semantik] download_history.file_hash → download_cache: cache
--    eviction bu FK'yi engeller → uygulama seviyesi doğrulama.
-- N7 [stil · yasak] int AI ↔ binary(16) UUID ↔ varchar(36)/ULID PK farkları:
--    veri taşıma = backward-incompatible, değiştirilmez.
-- N8 [opsiyonel · research §7] preset/ayar gibi eşzamanlı düzenlenen tablolara
--    version INT (optimistic lock) kolonu — uygulama kodu değişikliği gerektirir,
--    ADR sonrası.
-- ===========================================================================
-- UYGULAMA SONRASI DOĞRULAMA: her tablo SHOW CREATE TABLE ile teyit;
-- information_schema.referential_constraints / statistics sayım artışı beklenir.
-- ===========================================================================
