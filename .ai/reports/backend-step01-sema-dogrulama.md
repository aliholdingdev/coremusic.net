# Adım 01 — DB Şema Doğrulama Raporu

```
VERSION : 1.0.0 · DATE: 2026-10-22 · KATMAN: DB (1/3) · GÖREVLİ: data-engineer
KAYNAK  : .ai/.sql/mysql/*.sql (salt-okunur, dump'lara YAZMA YOK — hard rule 1 korundu)
KANIT   : .ai/tmp/step01-{inventory,count,keys2,fk-full,cols-full,taxo-cmp}.txt (scratch, gitignore)
```

> ⚠️ ÖN NOT: Önceki oturum kayıtlarında "adım 01 tamam + push" iddiası bulunuyordu; git
> nesne envanterinde (`cat-file -t`) doğrulanamadı, `.ai/log.md`'de step01 kaydı yoktu.
> Bu rapor sıfırdan, disk kanıtıyla üretilmiştir.

## (a) Dump Envanteri

**21 dosya / 201 tablo / 2 view** (scope: 20 dump — `novasearch.sql` §9.3 gereği hariç).
201/201 tabloda `PRIMARY KEY` var (0 NO_PK).

| Dosya | Tablo | View | Not |
|---|---|---|---|
| coremusic_ai | 6 | 0 | AI/recommendation |
| coremusic_albums | 11 | 0 | MusicBrainz-hizalı release modeli |
| coremusic_api | 4 | 0 | api_calls · api_keys · rate_limits · webhooks |
| coremusic_auth | 13 | 0 | users · roller · credential/audit |
| coremusic_billing | 8 | 0 | Enterprise (2026-10-22 eklendi) |
| coremusic_catalog | 8 | 0 | catalog_* sözlükler |
| coremusic_cms | 9 | 0 | blog/banner/pages |
| coremusic_download | 4 | 0 | |
| coremusic_logs | 22 | 0 | audit + analytics_daily_* |
| coremusic_media | 8 | 0 | devices · media_access |
| coremusic_musics | 29 | 1 | `v_music_recording_map` |
| coremusic_neva | 4 | 0 | eq_presets seed |
| coremusic_patch | 3 | 0 | migration_log · patches · schema_versions |
| coremusic_playlist | 5 | 0 | playlists + 4 ilişkili |
| coremusic_social | 12 | 0 | follows · comments · social_notifications |
| coremusic_studio | 6 | 0 | |
| coremusic_system | 20 | 0 | system_notifications + ayarlar |
| coremusic_user | 8 | 0 | user_profiles · listening · queue |
| coremusic_wireless | 5 | 0 | |
| media_catalog | 9 | 1 | `v_asset_search` · ULID char(26) · CANLI |
| novasearch | 7 | 0 | AYRI PROJE — kapsam dışı (bkz. d-1) |
| **TOPLAM** | **201** | **2** | |

- **FK:** 185 (81 cross-DB) — hepsi `ON DELETE/UPDATE` aksiyonlu; tam liste **Ek B**.
- **PK yapısı:** 196 tekil `id` (UUID v7 `binary(16)` / ULID `char(26)`) + 5 kompozit PK
  (`api_calls(id,called_at)` · `asset_tag(asset_id,alan,deger)` · `taxonomy(alan,deger)` ·
  `variant(asset_id,dosya)` · `knex_migrations_lock(index)`).
- **Taxonomy senkronu (edge case):** `taxonomy.json` (17 alan) ↔ `media_catalog.taxonomy`
  seed — **DRIFT=0** (17/17 birebir, Ek yok: kanıt `step01-taxo-cmp.txt`).
- **Charset:** utf8mb4/utf8mb4_unicode_ci tutarlı; `COMMENT`'ler UTF-8 doğru (okuma
  encoding'i kaynaklı görünen bozukluk false alarm — byte düzeyinde doğrulandı).
- **Yinelenen tablo adı (3):** `api_keys` (coremusic_auth + coremusic_api) · `playlists` +
  `search_logs` (coremusic_* + novasearch — farklı şema, ayrı proje, çakışma değil).
  Harmanlama/silme YOK (edge case: raporlanır).

## (b) Eksik/Tam Matris — adım 6–11 gerekli yüzeyler

| Yüzey (adım) | Gerekli tablolar | Durum | Kanıt / indeks |
|---|---|---|---|
| media/music stream (6) | musics · music_files · recordings · music_videos · asset · variant · album · artist · taxonomy | **TAM (9/9)** | `idx_mf_music_primary(music_id,is_primary)` · ULID char(26) · taxonomy drift=0 |
| playlist (7) | playlists · playlist_tracks · playlist_collaborators · playlist_followers · playlist_stats | **TAM (5/5)** | `playlists.user_id` FK ✓ · `idx_playlists_user` · `idx_playlist_tracks_playlist_pos(playlist_id,position)` |
| listen/history (8–9) | user_listening_history · user_recent_tracks · playback_queue | **TAM (3/3)** — *ad farkı, bkz. d-3* | `idx_ulh_user_recent(user_id,started_at DESC,music_id)` → adım 8 "(user_id,played_at) composite" ihtiyacı karşılanır · `uk_user_recent_tracks_pair` + `idx_user_recent_tracks_user_last(user_id,last_played_at)` → son N ideal |
| user_profile/role + notification (10) | user_profiles · user_preferences · users · user_roles · user_assigned_roles · system_notifications · social_notifications | **TAM (7/7)** — *dosya referansı hatalı, bkz. d-2* | `idx_notifications_user_read(user_id,is_read)` + `idx_sn_user_read_created(user_id,is_read,created_at DESC)` → unread count ✓ · `idx_assigned_roles_unique` |

**Sonuç: EKSİK TABLO = 0.** Adım 01'in "eksik şema tasarımı" ön koşulu disk üzerinde
kapalıdır — playlist/history/notification yüzeylerinin eksikleri 2026-10-22'de önceki
turda dump'lara gömülerek canlıya uygulanmış (`.ai/log.md` kaydı + bugün bağımsız
doğrulama: ilgili tablo/kolon/FK/indeks tamam).

## (c) Yeni Şema Taslakları (DDL)

**Gerekmedi — 0 yeni tablo/DDL.** Gerekçe: (b) matrisinde 4/4 yüzey TAM; aday eksikler
(`play_log`/`listen_history`/notification) mevcut tablolarla fonksiyonel olarak
karşılanıyor (d-2/d-3 eşlemesi). Yeni tablo çıkmadığı için step01 §5.4 kabulü
("her yeni tablo için AK") boş küme olarak kapanır; adım 2'ye ham girdi olarak mevcut
yüzey tablolarının AK/FD notları aşağıdadır:

| Tablo | Aday Anahtar (AK) | Fonksiyonel Bağımlılık (özet) |
|---|---|---|
| playlists | `{id}` · `{user_id,name}` (kısmi — tekrar izinli) | id → tüm kolonlar · user_id → (sahiplik, FK users) |
| playlist_tracks | `{id}` · `{playlist_id,position}` | id → tüm · playlist_id,position → music_id,added_by |
| user_listening_history | `{id}` · (sorgu anahtarı user_id,started_at) | id → tüm · user_id,started_at → music_id,playlist_id,listen_duration_sec |
| user_recent_tracks | `{id}` · `{user_id,music_id}` (UNIQUE) | id → tüm · user_id,music_id → last_played_at,play_count |
| system_notifications | `{id}` · (sorgu anahtarı user_id,is_read,created_at) | id → tüm · user_id,is_read → unread sayımı (count) |
| users | `{id}` (+ UNIQUE username, email) | id → tüm kolonlar |
| user_assigned_roles | `{id}` · `{user_id,role_id}` (UNIQUE) | id → tüm |
| musics / music_files | `{id}` | id → tüm · music_id,is_primary → dosya seçimi |
| api_calls (kompozit PK) | `{id,called_at}` | (id,called_at) → tüm · id → called_at (PK tam fonksiyonel) |
| taxonomy / variant / asset_tag (kompozit PK) | `{alan,deger}` · `{asset_id,dosya}` · `{asset_id,alan,deger}` | anahtar = tüm determiner; BCNF uygun |

Denormalizasyon notu (step02 girdisi): `playlists.total_tracks` gibi sayaç kolonlar
performans amaçlı türetilmiş; determiner değiller → BCNF ihlali değil, yazma yolu
senkronizasyonu step05'te kod tarafında garanti altına alınmalı.

## (d) ⚠️ VERIFICATION REQUIRED

| # | Bulgu | Etki | Öneri / Sahip |
|---|---|---|---|
| 1 | `novasearch.sql`'de `v_search_log_singular` view'ı **YOK** (grep: 0); `.ai/.sql/CLAUDE.md` §1/§3 "7 tablo + view" diyor. View yalnız `shared/database/migrations/20261021_*.sql` + canlı DB'de (`CREATE OR REPLACE VIEW`, log.md kanıtlı). **Dump ≠ canlı.** | novasearch kapsam dışı (P1-1=A) — ama dump "TAM şema" iddiasıyla view içermediği için regen'de kaybolur | Regen işi → orkestratör (step01 kapsamı dışı) |
| 2 | `step10-header.md`: "bildirim → `coremusic_api.sql` (notification tablosu)" — o dosyada notification **YOK** (4 tablo: api_calls/api_keys/rate_limits/webhooks). Gerçek kaynak: `coremusic_system.system_notifications` + `coremusic_social.social_notifications`. | Adım 10 yanlış dosyayı tarar | Prompt dosyası düzeltmesi → kullanıcı onayı |
| 3 | `step08`/`step09`: `play_log`/`listen_history` adlı tablo bekliyor; diskte bu adlarda tablo yok. Karşılığı: `user_listening_history` (append-only log) + `user_recent_tracks` (son N). "played_at" beklentisi `started_at` ile karşılanıyor. | Adım 8/9 mevcut tabloyu hedeflemeli | Adım 8/9 uygulanırken bu eşleme kullanılacak (kod tarafı adı `play_log` olabilir) |
| 4 | `api_keys` iki ayrı DB'de (coremusic_auth + coremusic_api), ikisi de `users`'a FK'lı. Farklı şema olduğu için teknik çakışma yok. | Sahiplik/BCNF kararı step02'ye | step02'de karar verilecek |
| 5 | `.ai/.sql/CLAUDE.md` iç tutarsızlığı: §1 "20 dump / 18 BCNF coremusic_*" vs §3/§S2 "21 dosya / 19 coremusic DB". Disk gerçeği: **21 dosya / 19 coremusic_*** (satır 9 eski sayım). | Düşük — sadece sayım satırı | vault-updater düzeltmesi |

## KAPI-A Durumu

| KAPI-A koşulu | Durum |
|---|---|
| Eksik şemalar yazıldı+uygulandı | ✅ **0 eksik** — bağımsız doğrulamayla kanıtlandı (b) |
| BCNF doğrulandı | ⏳ adım 2 (bu rapor AK/FD ham girdisini üretti; yeni şema olmadığı için adım 2 kapsamı "mevcut şemaların BCNF denetimi"ne daralır — **kullanıcı kararı**) |
| PDO bağlantı katmanı testi | ⏳ adım 3 |

---

## Ek A — 201 tablo tam liste (adım 1 kabul: "tablo adları listelendi")


**coremusic_ai.sql** (6):
`audio_features`, `listening_features`, `model_versions`, `recommendation_history`, `training_jobs`, `user_preference_profiles`

**coremusic_albums.sql** (11):
`album_credits`, `album_discs`, `album_genres`, `album_stats`, `albums`, `labels`, `media`, `release_groups`, `release_labels`, `releases`, `tracks`

**coremusic_api.sql** (4):
`api_calls`, `api_keys`, `rate_limits`, `webhooks`

**coremusic_auth.sql** (13):
`admin_activity_log`, `admin_users`, `api_keys`, `api_usage`, `credential_audit`, `credential_keys`, `credential_vault`, `permission_audit`, `user_assigned_roles`, `user_roles`, `user_sessions`, `user_tokens`, `users`

**coremusic_billing.sql** (8):
`subscription_plans`, `payment_methods`, `user_subscriptions`, `payment_transactions`, `invoices`, `device_playback_sessions`, `content_licenses`, `royalty_payout_ledger`

**coremusic_catalog.sql** (8):
`catalog_album_types`, `catalog_artist_roles`, `catalog_countries`, `catalog_genres`, `catalog_instruments`, `catalog_languages`, `catalog_moods`, `catalog_playlist_types`

**coremusic_cms.sql** (9):
`blog_categories`, `blog_post_tags`, `blog_posts`, `blog_tags`, `cms_banner_slides`, `cms_faqs`, `cms_media_assets`, `cms_pages`, `cms_page_revisions`

**coremusic_download.sql** (4):
`download_cache`, `download_history`, `download_queue`, `download_sources`

**coremusic_logs.sql** (22):
`analytics_daily_genre`, `analytics_daily_music`, `analytics_daily_platform`, `analytics_daily_users`, `analytics_performance`, `analytics_realtime_events`, `analytics_retention`, `analytics_storage`, `audit_logs`, `daily_stats`, `error_logs`, `log_activity`, `log_events`, `log_performance`, `log_security`, `log_system`, `page_views`, `performance_metrics`, `rate_limit_logs`, `search_logs`, `user_activity_logs`, `user_events`

**coremusic_media.sql** (8):
`device_playlists`, `device_sync_history`, `device_tracks`, `device_types`, `devices`, `media_access`, `media_audit`, `media_metadata`

**coremusic_musics.sql** (29):
`artist_credit`, `artist_credit_names`, `artist_members`, `artists`, `genres`, `music_artists`, `music_audio_features`, `music_credits`, `music_files`, `music_genres`, `music_lyrics`, `music_similar`, `music_stats`, `music_tags`, `music_videos`, `musics`, `podcast_episodes`, `podcast_shows`, `podcast_subscriptions`, `podcast_transcripts`, `podcast_transcript_segments`, `radio_now_playing`, `radio_play_history`, `radio_schedules`, `radio_stations`, `recordings`, `video_playback_history`, `video_subtitles`, `works`

**coremusic_neva.sql** (4):
`dsp_settings`, `eq_presets`, `routing_matrix`, `spectrum_analysis`

**coremusic_patch.sql** (3):
`migration_log`, `patches`, `schema_versions`

**coremusic_playlist.sql** (5):
`playlist_collaborators`, `playlist_followers`, `playlist_stats`, `playlist_tracks`, `playlists`

**coremusic_social.sql** (12):
`achievements`, `activity_feed`, `comment_likes`, `comments`, `listening_room_members`, `listening_room_queue`, `listening_rooms`, `oauth_connections`, `oauth_states`, `shares`, `social_notifications`, `user_achievements`

**coremusic_studio.sql** (6):
`session_equipment`, `studio_collaborators`, `studio_equipment`, `studio_presets`, `studio_sessions`, `studio_tracks`

**coremusic_system.sql** (20):
`i18n_languages`, `i18n_translations`, `i18n_ui_strings`, `i18n_user_locale`, `system_api_endpoints`, `system_app_settings`, `system_backup`, `system_bluetooth_devices`, `system_cache`, `system_config`, `system_eq_presets`, `system_file_manager`, `system_jobs`, `system_migration_log`, `system_notifications`, `system_schema_versions`, `system_services`, `system_settings`, `system_storage_mounts`, `system_wifi_networks`

**coremusic_user.sql** (8):
`playback_queue`, `user_downloads`, `user_favorites`, `user_follows`, `user_listening_history`, `user_preferences`, `user_profiles`, `user_recent_tracks`

**coremusic_wireless.sql** (5):
`bluetooth_audio_profiles`, `bluetooth_peers`, `network_profiles`, `sync_history`, `wifi_networks`

**media_catalog.sql** (9):
`album`, `artist`, `asset`, `asset_tag`, `ingest_batch`, `koleksiyon`, `path_history`, `taxonomy`, `variant`

**novasearch.sql** (7):
`cache_entries`, `channels`, `knex_migrations`, `knex_migrations_lock`, `playlists`, `search_logs`, `videos`

## Ek B — 185 FK tam liste (adım 1 kabul: "FK hedefleri listelendi")

```
coremusic_ai.audio_features  fk_af_music  (music_id) -> coremusic_musics.musics(id)
coremusic_ai.listening_features  fk_lf_user  (user_id) -> coremusic_auth.users(id)
coremusic_ai.listening_features  fk_lf_music  (music_id) -> coremusic_musics.musics(id)
coremusic_ai.recommendation_history  fk_rh_user  (user_id) -> coremusic_auth.users(id)
coremusic_ai.recommendation_history  fk_rh_music  (music_id) -> coremusic_musics.musics(id)
coremusic_ai.training_jobs  fk_training_jobs_model_versions  (model_version_id) -> coremusic_ai.model_versions(id)
coremusic_ai.user_preference_profiles  fk_upp_user  (user_id) -> coremusic_auth.users(id)
coremusic_albums.album_credits  fk_album_credits_album  (album_id) -> coremusic_albums.albums(id)
coremusic_albums.album_discs  fk_album_discs_album  (album_id) -> coremusic_albums.albums(id)
coremusic_albums.album_genres  fk_album_genres_album  (album_id) -> coremusic_albums.albums(id)
coremusic_albums.album_stats  fk_album_stats_album  (album_id) -> coremusic_albums.albums(id)
coremusic_albums.media  fk_media_release  (release_id) -> coremusic_albums.releases(id)
coremusic_albums.release_groups  fk_rg_type  (type_id) -> coremusic_catalog.catalog_album_types(id)
coremusic_albums.release_labels  fk_rl_label  (label_id) -> coremusic_albums.labels(id)
coremusic_albums.release_labels  fk_rl_release  (release_id) -> coremusic_albums.releases(id)
coremusic_albums.releases  fk_rel_group  (release_group_id) -> coremusic_albums.release_groups(id)
coremusic_albums.tracks  fk_track_credit  (artist_credit_id) -> coremusic_musics.artist_credit(id)
coremusic_albums.tracks  fk_track_medium  (medium_id) -> coremusic_albums.media(id)
coremusic_albums.tracks  fk_track_recording  (recording_id) -> coremusic_musics.recordings(id)
coremusic_api.api_calls  fk_ac_user  (user_id) -> coremusic_auth.users(id)
coremusic_api.api_keys  fk_api_keys_user  (user_id) -> coremusic_auth.users(id)
coremusic_api.rate_limits  rate_limits_ibfk_1  (api_key_id) -> coremusic_api.api_keys(id)
coremusic_api.webhooks  webhooks_ibfk_1  (api_key_id) -> coremusic_api.api_keys(id)
coremusic_auth.admin_activity_log  fk_admin_log_user  (admin_user_id) -> coremusic_auth.admin_users(id)
coremusic_auth.admin_users  fk_admin_user  (user_id) -> coremusic_auth.users(id)
coremusic_auth.api_keys  fk_apikeys_user  (user_id) -> coremusic_auth.users(id)
coremusic_auth.api_usage  fk_apiusage_key  (api_key_id) -> coremusic_auth.api_keys(id)
coremusic_auth.credential_audit  fk_cau_key  (credential_key_id) -> coremusic_auth.credential_keys(id)
coremusic_auth.credential_audit  fk_cau_user  (user_id) -> coremusic_auth.users(id)
coremusic_auth.credential_audit  fk_cau_vault  (credential_id) -> coremusic_auth.credential_vault(id)
coremusic_auth.credential_keys  fk_ck_user  (created_by) -> coremusic_auth.users(id)
coremusic_auth.credential_vault  fk_credential_creator  (created_by) -> coremusic_auth.users(id)
coremusic_auth.permission_audit  fk_perm_audit_by  (performed_by) -> coremusic_auth.users(id)
coremusic_auth.permission_audit  fk_perm_audit_user  (user_id) -> coremusic_auth.users(id)
coremusic_auth.user_assigned_roles  fk_assigned_roles_by  (assigned_by) -> coremusic_auth.users(id)
coremusic_auth.user_assigned_roles  fk_assigned_roles_role  (role_id) -> coremusic_auth.user_roles(id)
coremusic_auth.user_assigned_roles  fk_assigned_roles_user  (user_id) -> coremusic_auth.users(id)
coremusic_auth.user_sessions  fk_sessions_user  (user_id) -> coremusic_auth.users(id)
coremusic_auth.user_tokens  fk_tokens_user  (user_id) -> coremusic_auth.users(id)
coremusic_billing.payment_methods  fk_pm_user  (user_id) -> coremusic_auth.users(id)
coremusic_billing.user_subscriptions  fk_us_user  (user_id) -> coremusic_auth.users(id)
coremusic_billing.user_subscriptions  fk_us_plan  (plan_id) -> coremusic_billing.subscription_plans(id)
coremusic_billing.user_subscriptions  fk_us_pm  (default_payment_method_id) -> coremusic_billing.payment_methods(id)
coremusic_billing.payment_transactions  fk_pt_user  (user_id) -> coremusic_auth.users(id)
coremusic_billing.payment_transactions  fk_pt_sub  (subscription_id) -> coremusic_billing.user_subscriptions(id)
coremusic_billing.payment_transactions  fk_pt_pm  (payment_method_id) -> coremusic_billing.payment_methods(id)
coremusic_billing.invoices  fk_inv_user  (user_id) -> coremusic_auth.users(id)
coremusic_billing.invoices  fk_inv_sub  (subscription_id) -> coremusic_billing.user_subscriptions(id)
coremusic_billing.invoices  fk_inv_tx  (transaction_id) -> coremusic_billing.payment_transactions(id)
coremusic_billing.device_playback_sessions  fk_dps_user  (user_id) -> coremusic_auth.users(id)
coremusic_billing.device_playback_sessions  fk_dps_device  (device_id) -> coremusic_media.devices(id)
coremusic_billing.device_playback_sessions  fk_dps_track  (current_track_id) -> coremusic_musics.musics(id)
coremusic_billing.royalty_payout_ledger  fk_rpl_artist  (artist_id) -> coremusic_musics.artists(id)
coremusic_catalog.catalog_genres  fk_genres_parent  (parent_id) -> coremusic_catalog.catalog_genres(id)
coremusic_cms.blog_categories  fk_bc_parent  (parent_id) -> coremusic_cms.blog_categories(id)
coremusic_cms.blog_post_tags  fk_bpt_post  (post_id) -> coremusic_cms.blog_posts(id)
coremusic_cms.blog_post_tags  fk_bpt_tag  (tag_id) -> coremusic_cms.blog_tags(id)
coremusic_cms.blog_posts  fk_bp_author  (author_id) -> coremusic_auth.users(id)
coremusic_cms.blog_posts  fk_bp_category  (category_id) -> coremusic_cms.blog_categories(id)
coremusic_cms.cms_media_assets  fk_cm_uploader  (uploader_id) -> coremusic_auth.users(id)
coremusic_cms.cms_pages  fk_cp_author  (author_id) -> coremusic_auth.users(id)
coremusic_cms.cms_page_revisions  fk_cpr_page  (page_id) -> coremusic_cms.cms_pages(id)
coremusic_cms.cms_page_revisions  fk_cpr_user  (changed_by) -> coremusic_auth.users(id)
coremusic_download.download_history  fk_dh_user  (user_id) -> coremusic_auth.users(id)
coremusic_download.download_history  fk_dh_music  (music_id) -> coremusic_musics.musics(id)
coremusic_download.download_queue  fk_dq_user  (user_id) -> coremusic_auth.users(id)
coremusic_download.download_queue  fk_dq_music  (music_id) -> coremusic_musics.musics(id)
coremusic_download.download_sources  fk_ds_user  (user_id) -> coremusic_auth.users(id)
coremusic_media.device_playlists  fk_device_playlists_devices  (device_id) -> coremusic_media.devices(id)
coremusic_media.device_sync_history  fk_device_sync_history_devices  (device_id) -> coremusic_media.devices(id)
coremusic_media.device_tracks  fk_device_tracks_devices  (device_id) -> coremusic_media.devices(id)
coremusic_media.devices  fk_devices_device_types  (device_type_id) -> coremusic_media.device_types(id)
coremusic_media.devices  fk_devices_user  (user_id) -> coremusic_auth.users(id)
coremusic_media.media_access  fk_media_access_user  (user_id) -> coremusic_auth.users(id)
coremusic_media.media_audit  fk_media_audit_user  (user_id) -> coremusic_auth.users(id)
coremusic_media.media_metadata  fk_media_metadata_music  (music_id) -> coremusic_musics.musics(id)
coremusic_musics.artist_credit_names  fk_acn_artist  (artist_id) -> coremusic_musics.artists(id)
coremusic_musics.artist_credit_names  fk_acn_credit  (artist_credit_id) -> coremusic_musics.artist_credit(id)
coremusic_musics.artist_members  fk_artist_members_artist  (artist_id) -> coremusic_musics.artists(id)
coremusic_musics.genres  fk_genres_parent  (parent_id) -> coremusic_musics.genres(id)
coremusic_musics.music_artists  fk_music_artists_artist  (artist_id) -> coremusic_musics.artists(id)
coremusic_musics.music_artists  fk_music_artists_music  (music_id) -> coremusic_musics.musics(id)
coremusic_musics.music_artists  fk_music_artists_role  (artist_role_id) -> coremusic_catalog.catalog_artist_roles(id)
coremusic_musics.music_audio_features  fk_music_audio_features_music  (music_id) -> coremusic_musics.musics(id)
coremusic_musics.music_credits  fk_music_credits_music  (music_id) -> coremusic_musics.musics(id)
coremusic_musics.music_files  fk_music_files_music  (music_id) -> coremusic_musics.musics(id)
coremusic_musics.music_genres  fk_music_genres_genre  (genre_id) -> coremusic_musics.genres(id)
coremusic_musics.music_genres  fk_music_genres_music  (music_id) -> coremusic_musics.musics(id)
coremusic_musics.music_lyrics  fk_music_lyrics_music  (music_id) -> coremusic_musics.musics(id)
coremusic_musics.music_similar  fk_music_similar_source  (music_id) -> coremusic_musics.musics(id)
coremusic_musics.music_similar  fk_music_similar_target  (similar_music_id) -> coremusic_musics.musics(id)
coremusic_musics.music_stats  fk_music_stats_music  (music_id) -> coremusic_musics.musics(id)
coremusic_musics.music_tags  fk_music_tags_music  (music_id) -> coremusic_musics.musics(id)
coremusic_musics.music_videos  fk_mv_music  (music_id) -> coremusic_musics.musics(id)
coremusic_musics.musics  fk_musics_artist  (artist_id) -> coremusic_musics.artists(id)
coremusic_musics.musics  fk_musics_genre  (genre_id) -> coremusic_musics.genres(id)
coremusic_musics.podcast_episodes  fk_pe_show  (show_id) -> coremusic_musics.podcast_shows(id)
coremusic_musics.podcast_shows  fk_pshow_author  (author_user_id) -> coremusic_auth.users(id)
coremusic_musics.podcast_subscriptions  fk_psub_show  (show_id) -> coremusic_musics.podcast_shows(id)
coremusic_musics.podcast_subscriptions  fk_psub_user  (user_id) -> coremusic_auth.users(id)
coremusic_musics.podcast_transcripts  fk_pt_episode  (episode_id) -> coremusic_musics.podcast_episodes(id)
coremusic_musics.podcast_transcript_segments  fk_pts_transcript  (transcript_id) -> coremusic_musics.podcast_transcripts(id)
coremusic_musics.radio_now_playing  fk_rnp_music  (music_id) -> coremusic_musics.musics(id)
coremusic_musics.radio_now_playing  fk_rnp_station  (station_id) -> coremusic_musics.radio_stations(id)
coremusic_musics.radio_play_history  fk_rph_station  (station_id) -> coremusic_musics.radio_stations(id)
coremusic_musics.radio_play_history  fk_rph_music  (music_id) -> coremusic_musics.musics(id)
coremusic_musics.radio_schedules  fk_rsch_station  (station_id) -> coremusic_musics.radio_stations(id)
coremusic_musics.recordings  fk_rec_credit  (artist_credit_id) -> coremusic_musics.artist_credit(id)
coremusic_musics.recordings  fk_rec_work  (work_id) -> coremusic_musics.works(id)
coremusic_musics.video_playback_history  fk_vph_user  (user_id) -> coremusic_auth.users(id)
coremusic_musics.video_playback_history  fk_vph_video  (video_id) -> coremusic_musics.music_videos(id)
coremusic_musics.video_subtitles  fk_vs_video  (video_id) -> coremusic_musics.music_videos(id)
coremusic_musics.works  fk_works_credit  (artist_credit_id) -> coremusic_musics.artist_credit(id)
coremusic_neva.dsp_settings  fk_dsp_user  (user_id) -> coremusic_auth.users(id)
coremusic_neva.eq_presets  fk_eq_user  (user_id) -> coremusic_auth.users(id)
coremusic_neva.routing_matrix  fk_rm_user  (user_id) -> coremusic_auth.users(id)
coremusic_neva.spectrum_analysis  fk_sa_user  (user_id) -> coremusic_auth.users(id)
coremusic_playlist.playlist_collaborators  fk_playlist_collaborators_playlist  (playlist_id) -> coremusic_playlist.playlists(id)
coremusic_playlist.playlist_collaborators  fk_playlist_collaborators_user  (user_id) -> coremusic_auth.users(id)
coremusic_playlist.playlist_followers  fk_playlist_followers_playlist  (playlist_id) -> coremusic_playlist.playlists(id)
coremusic_playlist.playlist_followers  fk_playlist_followers_user  (user_id) -> coremusic_auth.users(id)
coremusic_playlist.playlist_stats  fk_playlist_stats_playlist  (playlist_id) -> coremusic_playlist.playlists(id)
coremusic_playlist.playlist_tracks  fk_playlist_tracks_music  (music_id) -> coremusic_musics.musics(id)
coremusic_playlist.playlist_tracks  fk_playlist_tracks_playlist  (playlist_id) -> coremusic_playlist.playlists(id)
coremusic_playlist.playlists  fk_playlists_user  (user_id) -> coremusic_auth.users(id)
coremusic_social.activity_feed  fk_activity_target  (target_user_id) -> coremusic_auth.users(id)
coremusic_social.activity_feed  fk_activity_user  (user_id) -> coremusic_auth.users(id)
coremusic_social.comment_likes  fk_comment_likes_comment  (comment_id) -> coremusic_social.comments(id)
coremusic_social.comment_likes  fk_comment_likes_user  (user_id) -> coremusic_auth.users(id)
coremusic_social.comments  fk_comments_parent  (parent_id) -> coremusic_social.comments(id)
coremusic_social.comments  fk_comments_user  (user_id) -> coremusic_auth.users(id)
coremusic_social.listening_room_members  fk_room_members_room  (room_id) -> coremusic_social.listening_rooms(id)
coremusic_social.listening_room_members  fk_room_members_user  (user_id) -> coremusic_auth.users(id)
coremusic_social.listening_room_queue  fk_room_queue_added_by  (added_by) -> coremusic_auth.users(id)
coremusic_social.listening_room_queue  fk_room_queue_music  (music_id) -> coremusic_musics.musics(id)
coremusic_social.listening_room_queue  fk_room_queue_room  (room_id) -> coremusic_social.listening_rooms(id)
coremusic_social.listening_rooms  fk_listening_rooms_host  (host_user_id) -> coremusic_auth.users(id)
coremusic_social.listening_rooms  fk_listening_rooms_music  (current_music_id) -> coremusic_musics.musics(id)
coremusic_social.oauth_connections  fk_oauth_conn_user  (user_id) -> coremusic_auth.users(id)
coremusic_social.oauth_states  fk_oauth_states_user  (user_id) -> coremusic_auth.users(id)
coremusic_social.shares  fk_shares_user  (user_id) -> coremusic_auth.users(id)
coremusic_social.social_notifications  fk_social_notif_from  (from_user_id) -> coremusic_auth.users(id)
coremusic_social.social_notifications  fk_social_notif_user  (user_id) -> coremusic_auth.users(id)
coremusic_social.user_achievements  fk_achievements_ach  (achievement_id) -> coremusic_social.achievements(id)
coremusic_social.user_achievements  fk_achievements_user  (user_id) -> coremusic_auth.users(id)
coremusic_studio.session_equipment  fk_session_equipment_equipment  (equipment_id) -> coremusic_studio.studio_equipment(id)
coremusic_studio.session_equipment  fk_session_equipment_session  (session_id) -> coremusic_studio.studio_sessions(id)
coremusic_studio.studio_collaborators  fk_studio_collaborators_session  (session_id) -> coremusic_studio.studio_sessions(id)
coremusic_studio.studio_collaborators  fk_studio_collaborators_user  (user_id) -> coremusic_auth.users(id)
coremusic_studio.studio_presets  fk_studio_presets_user  (user_id) -> coremusic_auth.users(id)
coremusic_studio.studio_sessions  fk_studio_sessions_host  (host_user_id) -> coremusic_auth.users(id)
coremusic_studio.studio_tracks  fk_studio_tracks_session  (session_id) -> coremusic_studio.studio_sessions(id)
coremusic_system.i18n_user_locale  fk_iul_user  (user_id) -> coremusic_auth.users(id)
coremusic_system.system_app_settings  fk_app_settings_user  (user_id) -> coremusic_auth.users(id)
coremusic_system.system_eq_presets  fk_eq_presets_user  (user_id) -> coremusic_auth.users(id)
coremusic_system.system_file_manager  fk_file_manager_owner  (owner_id) -> coremusic_auth.users(id)
coremusic_system.system_file_manager  fk_file_manager_parent  (parent_id) -> coremusic_system.system_file_manager(id)
coremusic_system.system_notifications  fk_notifications_user  (user_id) -> coremusic_auth.users(id)
coremusic_user.playback_queue  fk_queue_music  (music_id) -> coremusic_musics.musics(id)
coremusic_user.playback_queue  fk_queue_user  (user_id) -> coremusic_auth.users(id)
coremusic_user.user_downloads  fk_downloads_music  (music_id) -> coremusic_musics.musics(id)
coremusic_user.user_downloads  fk_downloads_user  (user_id) -> coremusic_auth.users(id)
coremusic_user.user_favorites  fk_favorites_user  (user_id) -> coremusic_auth.users(id)
coremusic_user.user_follows  fk_follows_follower  (follower_id) -> coremusic_auth.users(id)
coremusic_user.user_follows  fk_follows_following  (following_id) -> coremusic_auth.users(id)
coremusic_user.user_listening_history  fk_history_music  (music_id) -> coremusic_musics.musics(id)
coremusic_user.user_listening_history  fk_history_playlist  (playlist_id) -> coremusic_playlist.playlists(id)
coremusic_user.user_listening_history  fk_history_user  (user_id) -> coremusic_auth.users(id)
coremusic_user.user_preferences  fk_preferences_user  (user_id) -> coremusic_auth.users(id)
coremusic_user.user_profiles  fk_profiles_user  (user_id) -> coremusic_auth.users(id)
coremusic_user.user_recent_tracks  fk_user_recent_tracks_music  (music_id) -> coremusic_musics.musics(id)
coremusic_user.user_recent_tracks  fk_user_recent_tracks_user  (user_id) -> coremusic_auth.users(id)
coremusic_wireless.bluetooth_audio_profiles  fk_bt_audio_profile_peer  (peer_id) -> coremusic_wireless.bluetooth_peers(id)
coremusic_wireless.network_profiles  fk_network_profile_bluetooth  (bluetooth_id) -> coremusic_wireless.bluetooth_peers(id)
coremusic_wireless.network_profiles  fk_network_profile_wifi  (wifi_id) -> coremusic_wireless.wifi_networks(id)
media_catalog.album  fk_album_artist  (sanatci_id) -> media_catalog.artist(id)
media_catalog.asset  fk_asset_album  (album_id) -> media_catalog.album(id)
media_catalog.asset  fk_asset_artist  (sanatci_id) -> media_catalog.artist(id)
media_catalog.asset  fk_asset_koleksiyon  (koleksiyon_id) -> media_catalog.koleksiyon(id)
media_catalog.asset_tag  fk_asset_tag_asset  (asset_id) -> media_catalog.asset(id)
media_catalog.asset_tag  fk_asset_tag_taxonomy  (alan, deger) -> media_catalog.taxonomy(alan, deger)
media_catalog.path_history  fk_path_history_asset  (asset_id) -> media_catalog.asset(id)
media_catalog.variant  fk_variant_asset  (asset_id) -> media_catalog.asset(id)
novasearch.playlists  playlists_ibfk_1  (channel_id) -> novasearch.channels(id)
novasearch.videos  videos_ibfk_1  (channel_id) -> novasearch.channels(id)
```
