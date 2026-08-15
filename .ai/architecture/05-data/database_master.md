---
type: architecture
category: data
title: "Database Master — 18 BCNF"
date: 2026-08-15
updated: 2026-08-15
status: active
version: 5.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Database Master — 18 BCNF

**Zorunlu Bağlantılar:** [[index]] · [[CLAUDE.md]] · [[brain.md]]

## 1. Amaç

CoreMusic'in 18 izole BCNF veritabanını, tüm tablolarını, şema tasarımını, naming conventions'ı ve cross-DB bağımlılıklarını tanımlayan **Tek Doğruluk Kaynağıdır (SSOT)**. Tüm veriler `.ai/.sql/mysql/` dosyalarındandır.

## 2. Database Architecture

| # | Veritabanı | Amaç | Tablo | PK Tipi | Versiyon |
|---|-----------|------|-------|---------|----------|
| 1 | `coremusic_auth` | Kimlik doğrulama, session, roller, credential vault, API anahtarları | 13 | UUID v7 + INT | 8.0.0 |
| 2 | `coremusic_user` | Kullanıcı profilleri, tercihler, dinleme geçmişi, favoriler | 7 | UUID v7 | 8.0.0 |
| 3 | `coremusic_musics` | Şarkılar, sanatçılar, türler, sözler, dosyalar, podcast, video, radyo | 22 | UUID v7 | 8.0.0 |
| 4 | `coremusic_albums` | Albüm koleksiyonları, diskler, istatistikler | 5 | UUID v7 | 8.0.0 |
| 5 | `coremusic_playlist` | Çalma listeleri, işbirlikçileri, takipçiler | 5 | UUID v7 | 7.0.0 |
| 6 | `coremusic_catalog` | Referans verileri (türler, sanatçı rolleri, enstrümanlar, diller) | 8 | UUID v7 | 7.0.0 |
| 7 | `coremusic_logs` | Audit trail, analitik, hata logları, performans metrikleri | 22 | UUID v7 + BIGINT + INT | 7.0.0 |
| 8 | `coremusic_media` | Cihaz yönetimi, medya metadata, erişim kontrolü | 8 | UUID v7 | 8.0.0 |
| 9 | `coremusic_system` | Sistem ayarları, EQ presetleri, bildirimler, dosya yöneticisi, cache, i18n | 17 | UUID v7 + INT | 8.0.0 |
| 10 | `coremusic_social` | Yorumlar, paylaşımlar, aktivite akışı, dinleme odaları | 9 | UUID v7 | 7.0.0 |
| 11 | `coremusic_wireless` | WiFi + Bluetooth ağları, senkronizasyon | 5 | UUID v7 | 7.0.0 |
| 12 | `coremusic_ai` | Kullanıcı tercih profilleri, dinleme özellikleri, öneri geçmişi, model versiyonları | 6 | INT | 8.0.0 |
| 13 | `coremusic_api` | API anahtarları, rate limit, API çağrısı logları, webhook'lar | 4 | INT | 1.0.0 |
| 14 | `coremusic_cms` | Sayfa yönetimi, blog, etiketler, medya varlıkları, SSS, banner'lar | 8 | INT | 8.0.0 |
| 15 | `coremusic_neva` | EQ presetleri, DSP ayarları, yönlendirme matrisi, spektrum analizi | 4 | INT + BIGINT | 8.0.0 |
| 16 | `coremusic_studio` | Stüdyo oturumları, parçaları, presetler, ekipmanlar | 6 | INT | 8.0.0 |
| 17 | `coremusic_patch` | Şema versiyonları, migration logları, yamalar | 3 | INT | 2.0.0 |
| 18 | `coremusic_download` | İndirme kuyruğu, geçmiş, önbellek, kaynak API bilgileri | 4 | INT | 8.0.0 |
| | **TOPLAM** | | **156** | | |

## 3. Tablo Detayları

Her veritabanının tam şeması için ilgili `.sql` dosyasına bakın:

| # | SQL Dosyası | Konum |
|---|------------|-------|
| 1 | `coremusic_auth.sql` | `.ai/.sql/mysql/coremusic_auth.sql` |
| 2 | `coremusic_user.sql` | `.ai/.sql/mysql/coremusic_user.sql` |
| 3 | `coremusic_musics.sql` | `.ai/.sql/mysql/coremusic_musics.sql` |
| 4 | `coremusic_albums.sql` | `.ai/.sql/mysql/coremusic_albums.sql` |
| 5 | `coremusic_playlist.sql` | `.ai/.sql/mysql/coremusic_playlist.sql` |
| 6 | `coremusic_catalog.sql` | `.ai/.sql/mysql/coremusic_catalog.sql` |
| 7 | `coremusic_logs.sql` | `.ai/.sql/mysql/coremusic_logs.sql` |
| 8 | `coremusic_media.sql` | `.ai/.sql/mysql/coremusic_media.sql` |
| 9 | `coremusic_system.sql` | `.ai/.sql/mysql/coremusic_system.sql` |
| 10 | `coremusic_social.sql` | `.ai/.sql/mysql/coremusic_social.sql` |
| 11 | `coremusic_wireless.sql` | `.ai/.sql/mysql/coremusic_wireless.sql` |
| 12 | `coremusic_ai.sql` | `.ai/.sql/mysql/coremusic_ai.sql` |
| 13 | `coremusic_api.sql` | `.ai/.sql/mysql/coremusic_api.sql` |
| 14 | `coremusic_cms.sql` | `.ai/.sql/mysql/coremusic_cms.sql` |
| 15 | `coremusic_neva.sql` | `.ai/.sql/mysql/coremusic_neva.sql` |
| 16 | `coremusic_studio.sql` | `.ai/.sql/mysql/coremusic_studio.sql` |
| 17 | `coremusic_patch.sql` | `.ai/.sql/mysql/coremusic_patch.sql` |
| 18 | `coremusic_download.sql` | `.ai/.sql/mysql/coremusic_download.sql` |

## 4. Tablo Listesi (156 Tablo)

### 4.1 coremusic_auth (13 tablo)

| Tablo | PK Tipi | Amaç |
|-------|---------|------|
| `users` | BINARY(16) UUID v7 | Kullanıcı hesapları |
| `user_roles` | BINARY(16) UUID v7 | Kullanıcı-rol eşleme |
| `user_assigned_roles` | BINARY(16) UUID v7 | Atanmış roller |
| `user_sessions` | BINARY(16) UUID v7 | Oturumlar |
| `user_tokens` | BINARY(16) UUID v7 | JWT token'lar |
| `credential_vault` | BINARY(16) UUID v7 | Şifreli credential'lar |
| `credential_keys` | INT UNSIGNED | Credential anahtarları |
| `credential_audit` | INT UNSIGNED | Credential denetim kayıtları |
| `api_keys` | BINARY(16) UUID v7 | API anahtarları |
| `api_usage` | BINARY(16) UUID v7 | API kullanım logları |
| `permission_audit` | BINARY(16) UUID v7 | İzin denetim kayıtları |
| `admin_users` | BINARY(16) UUID v7 | Yönetici hesapları |
| `admin_activity_log` | BINARY(16) UUID v7 | Yönetici aktivite logları |

### 4.2 coremusic_user (7 tablo)

| Tablo | PK Tipi | Amaç |
|-------|---------|------|
| `user_profiles` | BINARY(16) UUID v7 | Kullanıcı profilleri |
| `user_preferences` | BINARY(16) UUID v7 | Kullanıcı tercihleri |
| `user_listening_history` | BINARY(16) UUID v7 | Dinleme geçmişi |
| `user_favorites` | BINARY(16) UUID v7 | Favoriler |
| `user_follows` | BINARY(16) UUID v7 | Takip ilişkileri |
| `playback_queue` | BINARY(16) UUID v7 | Oynatma kuyruğu |
| `user_downloads` | BINARY(16) UUID v7 | Kullanıcı indirmeleri |

### 4.3 coremusic_musics (22 tablo)

| Tablo | PK Tipi | Amaç |
|-------|---------|------|
| `artists` | BINARY(16) UUID v7 | Sanatçılar |
| `genres` | BINARY(16) UUID v7 | Müzik türleri |
| `musics` | BINARY(16) UUID v7 | Şarkılar |
| `music_files` | BINARY(16) UUID v7 | Müzik dosyaları |
| `music_lyrics` | BINARY(16) UUID v7 | Sözler |
| `music_genres` | BINARY(16) UUID v7 | Şarkı-tür eşleme |
| `music_tags` | BINARY(16) UUID v7 | Şarkı etiketleri |
| `music_stats` | BINARY(16) UUID v7 | Şarkı istatistikleri |
| `music_similar` | BINARY(16) UUID v7 | Benzer şarkılar |
| `artist_members` | BINARY(16) UUID v7 | Sanatçı üyeleri |
| `music_audio_features` | BINARY(16) UUID v7 | Ses özellikleri |
| `music_credits` | BINARY(16) UUID v7 | Müzik kredileri |
| `podcast_shows` | BINARY(16) UUID v7 | Podcast gösterileri |
| `podcast_episodes` | BINARY(16) UUID v7 | Podcast bölümleri |
| `podcast_subscriptions` | BINARY(16) UUID v7 | Podcast abonelikleri |
| `podcast_transcripts` | BINARY(16) UUID v7 | Podcast transkriptleri |
| `music_videos` | BINARY(16) UUID v7 | Müzik videoları |
| `video_playback_history` | BINARY(16) UUID v7 | Video oynatma geçmişi |
| `video_subtitles` | BINARY(16) UUID v7 | Video altyazıları |
| `radio_stations` | BINARY(16) UUID v7 | Radyo istasyonları |
| `radio_schedules` | BINARY(16) UUID v7 | Radyo programları |
| `radio_now_playing` | BINARY(16) UUID v7 | Şu an çalan |

### 4.4 coremusic_albums (5 tablo)

| Tablo | PK Tipi | Amaç |
|-------|---------|------|
| `albums` | BINARY(16) UUID v7 | Albümler |
| `album_discs` | BINARY(16) UUID v7 | Albüm diskleri |
| `album_stats` | BINARY(16) UUID v7 | Albüm istatistikleri |
| `album_genres` | BINARY(16) UUID v7 | Albüm-tür eşleme |
| `album_credits` | BINARY(16) UUID v7 | Albüm kredileri |

### 4.5 coremusic_playlist (5 tablo)

| Tablo | PK Tipi | Amaç |
|-------|---------|------|
| `playlists` | BINARY(16) UUID v7 | Çalma listeleri |
| `playlist_tracks` | BINARY(16) UUID v7 | Liste şarkıları |
| `playlist_collaborators` | BINARY(16) UUID v7 | İşbirlikçiler |
| `playlist_followers` | BINARY(16) UUID v7 | Takipçiler |
| `playlist_stats` | BINARY(16) UUID v7 | Liste istatistikleri |

### 4.6 coremusic_catalog (8 tablo)

| Tablo | PK Tipi | Amaç |
|-------|---------|------|
| `catalog_genres` | BINARY(16) UUID v7 | Tür kataloğu |
| `catalog_artist_roles` | BINARY(16) UUID v7 | Sanatçı rolleri |
| `catalog_album_types` | BINARY(16) UUID v7 | Albüm türleri |
| `catalog_playlist_types` | BINARY(16) UUID v7 | Liste türleri |
| `catalog_instruments` | BINARY(16) UUID v7 | Enstrümanlar |
| `catalog_moods` | BINARY(16) UUID v7 | Ruh halleri |
| `catalog_countries` | BINARY(16) UUID v7 | Ülkeler |
| `catalog_languages` | BINARY(16) UUID v7 | Diller |

### 4.7 coremusic_logs (22 tablo)

| Tablo | PK Tipi | Amaç |
|-------|---------|------|
| `audit_logs` | BINARY(16) UUID v7 | Denetim logları |
| `user_activity_logs` | BINARY(16) UUID v7 | Kullanıcı aktivite logları |
| `search_logs` | BINARY(16) UUID v7 | Arama logları |
| `error_logs` | BINARY(16) UUID v7 | Hata logları |
| `rate_limit_logs` | BINARY(16) UUID v7 | Rate limit logları |
| `analytics_daily_users` | BIGINT UNSIGNED | Günlük kullanıcı analitik |
| `analytics_daily_platform` | BIGINT UNSIGNED | Günlük platform analitik |
| `analytics_daily_music` | BIGINT UNSIGNED | Günlük müzik analitik |
| `analytics_daily_genre` | BIGINT UNSIGNED | Günlük tür analitik |
| `analytics_realtime_events` | BIGINT UNSIGNED | Gerçek zamanlı olaylar |
| `analytics_performance` | BIGINT UNSIGNED | Performans analitik |
| `analytics_storage` | BIGINT UNSIGNED | Depolama analitik |
| `analytics_retention` | INT UNSIGNED | Saklama politikası |
| `page_views` | INT UNSIGNED | Sayfa görüntüleme |
| `user_events` | INT UNSIGNED | Kullanıcı olayları |
| `performance_metrics` | INT UNSIGNED | Performans metrikleri |
| `daily_stats` | INT UNSIGNED | Günlük istatistikler |
| `log_events` | INT UNSIGNED | Olay logları |
| `log_security` | INT UNSIGNED | Güvenlik logları |
| `log_performance` | INT UNSIGNED | Performans logları |
| `log_system` | INT UNSIGNED | Sistem logları |
| `log_activity` | INT UNSIGNED | Aktivite logları |

### 4.8 coremusic_media (8 tablo)

| Tablo | PK Tipi | Amaç |
|-------|---------|------|
| `device_types` | BINARY(16) UUID v7 | Cihaz türleri |
| `devices` | BINARY(16) UUID v7 | Kullanıcı cihazları |
| `device_playlists` | BINARY(16) UUID v7 | Cihaz-liste senkronizasyonu |
| `device_tracks` | BINARY(16) UUID v7 | Cihaz-şarkı senkronizasyonu |
| `device_sync_history` | BINARY(16) UUID v7 | Cihaz senkronizasyon geçmişi |
| `media_metadata` | BINARY(16) UUID v7 | Medya dosyası metadata |
| `media_access` | BINARY(16) UUID v7 | Medya erişim izinleri |
| `media_audit` | BINARY(16) UUID v7 | Medya denetim kayıtları |

### 4.9 coremusic_system (17 tablo)

| Tablo | PK Tipi | Amaç |
|-------|---------|------|
| `system_settings` | BINARY(16) UUID v7 | Sistem ayarları |
| `system_eq_presets` | BINARY(16) UUID v7 | EQ presetleri |
| `system_notifications` | BINARY(16) UUID v7 | Sistem bildirimleri |
| `system_file_manager` | BINARY(16) UUID v7 | Dosya yöneticisi |
| `system_cache` | BINARY(16) UUID v7 | Sistem cache |
| `system_wifi_networks` | BINARY(16) UUID v7 | WiFi ağları |
| `system_bluetooth_devices` | BINARY(16) UUID v7 | Bluetooth cihazları |
| `system_app_settings` | BINARY(16) UUID v7 | Uygulama ayarları |
| `system_api_endpoints` | BINARY(16) UUID v7 | API endpoint'leri |
| `system_backup` | BINARY(16) UUID v7 | Sistem yedekleri |
| `system_config` | INT UNSIGNED | Sistem konfigürasyonu |
| `system_schema_versions` | INT UNSIGNED | Şema versiyonları |
| `system_migration_log` | INT UNSIGNED | Migration logları |
| `i18n_languages` | INT UNSIGNED | Diller |
| `i18n_translations` | INT UNSIGNED | Çeviriler |
| `i18n_ui_strings` | INT UNSIGNED | UI dizgeleri |
| `i18n_user_locale` | INT UNSIGNED | Kullanıcı yerel ayarları |

### 4.10 coremusic_social (9 tablo)

| Tablo | PK Tipi | Amaç |
|-------|---------|------|
| `comments` | BINARY(16) UUID v7 | Yorumlar |
| `comment_likes` | BINARY(16) UUID v7 | Yorum beğenileri |
| `shares` | BINARY(16) UUID v7 | Paylaşımlar |
| `activity_feed` | BINARY(16) UUID v7 | Aktivite akışı |
| `listening_rooms` | BINARY(16) UUID v7 | Dinleme odaları |
| `listening_room_members` | BINARY(16) UUID v7 | Oda üyeleri |
| `listening_room_queue` | BINARY(16) UUID v7 | Oda kuyruğu |
| `user_achievements` | BINARY(16) UUID v7 | Kullanıcı başarıları |
| `social_notifications` | BINARY(16) UUID v7 | Sosyal bildirimler |

### 4.11 coremusic_wireless (5 tablo)

| Tablo | PK Tipi | Amaç |
|-------|---------|------|
| `wifi_networks` | BINARY(16) UUID v7 | WiFi ağları |
| `bluetooth_peers` | BINARY(16) UUID v7 | Bluetooth eşleri |
| `sync_history` | BINARY(16) UUID v7 | Senkronizasyon geçmişi |
| `bluetooth_audio_profiles` | BINARY(16) UUID v7 | Bluetooth ses profilleri |
| `network_profiles` | BINARY(16) UUID v7 | Ağ profilleri |

### 4.12 coremusic_ai (6 tablo)

| Tablo | PK Tipi | Amaç |
|-------|---------|------|
| `user_preference_profiles` | INT UNSIGNED | Kullanıcı tercih profilleri |
| `listening_features` | INT UNSIGNED | Dinleme özellikleri |
| `recommendation_history` | INT UNSIGNED | Öneri geçmişi |
| `audio_features` | INT UNSIGNED | Ses özellikleri |
| `model_versions` | INT UNSIGNED | Model versiyonları |
| `training_jobs` | INT UNSIGNED | Eğitim işleri |

### 4.13 coremusic_api (4 tablo)

| Tablo | PK Tipi | Amaç |
|-------|---------|------|
| `api_keys` | INT UNSIGNED | API anahtarları |
| `rate_limits` | INT UNSIGNED | Rate limit kayıtları |
| `api_calls` | INT UNSIGNED | API çağrı logları |
| `webhooks` | INT UNSIGNED | Webhook tanımları |

### 4.14 coremusic_cms (8 tablo)

| Tablo | PK Tipi | Amaç |
|-------|---------|------|
| `cms_pages` | INT UNSIGNED | CMS sayfaları |
| `blog_posts` | INT UNSIGNED | Blog yazıları |
| `blog_categories` | INT UNSIGNED | Blog kategorileri |
| `blog_tags` | INT UNSIGNED | Blog etiketleri |
| `blog_post_tags` | INT UNSIGNED | Yazı-etiket eşleme |
| `cms_media_assets` | INT UNSIGNED | Medya varlıkları |
| `cms_faqs` | INT UNSIGNED | SSS'ler |
| `cms_banner_slides` | INT UNSIGNED | Banner slaytları |

### 4.15 coremusic_neva (4 tablo)

| Tablo | PK Tipi | Amaç |
|-------|---------|------|
| `eq_presets` | INT UNSIGNED | EQ presetleri |
| `dsp_settings` | INT UNSIGNED | DSP ayarları |
| `routing_matrix` | BIGINT UNSIGNED | Yönlendirme matrisi |
| `spectrum_analysis` | BIGINT UNSIGNED | Spektrum analizi |

### 4.16 coremusic_studio (6 tablo)

| Tablo | PK Tipi | Amaç |
|-------|---------|------|
| `studio_sessions` | INT UNSIGNED | Stüdyo oturumları |
| `studio_tracks` | INT UNSIGNED | Stüdyo parçaları |
| `studio_presets` | INT UNSIGNED | Stüdyo presetleri |
| `studio_equipment` | INT UNSIGNED | Stüdyo ekipmanları |
| `session_equipment` | INT UNSIGNED | Oturum-ekipman eşleme |
| `studio_collaborators` | INT UNSIGNED | Stüdyo işbirlikçileri |

### 4.17 coremusic_patch (3 tablo)

| Tablo | PK Tipi | Amaç |
|-------|---------|------|
| `schema_versions` | INT UNSIGNED | Şema versiyonları |
| `migration_log` | INT UNSIGNED | Migration logları |
| `patches` | INT UNSIGNED | Yamalar |

### 4.18 coremusic_download (4 tablo)

| Tablo | PK Tipi | Amaç |
|-------|---------|------|
| `download_queue` | INT UNSIGNED | İndirme kuyruğu |
| `download_history` | INT UNSIGNED | İndirme geçmişi |
| `download_cache` | INT UNSIGNED | İndirme önbelleği |
| `download_sources` | INT UNSIGNED | İndirme kaynak API'leri |

## 5. Cross-DB Foreign Key Haritası

```
coremusic_auth.users.id
  ├──► coremusic_user.user_profiles.user_id
  ├──► coremusic_user.user_preferences.user_id
  ├──► coremusic_user.user_listening_history.user_id
  ├──► coremusic_user.user_favorites.user_id
  ├──► coremusic_user.user_follows.follower_id / following_id
  ├──► coremusic_user.playback_queue.user_id
  ├──► coremusic_user.user_downloads.user_id
  ├──► coremusic_musics.music_stats.user_id
  ├──► coremusic_musics.music_similar.user_id
  ├──► coremusic_musics.podcast_subscriptions.user_id
  ├──► coremusic_playlist.playlists.user_id
  ├──► coremusic_playlist.playlist_collaborators.user_id
  ├──► coremusic_playlist.playlist_followers.user_id
  ├──► coremusic_logs.audit_logs.user_id
  ├──► coremusic_logs.user_activity_logs.user_id
  ├──► coremusic_logs.search_logs.user_id
  ├──► coremusic_media.devices.user_id
  ├──► coremusic_media.media_access.user_id
  ├──► coremusic_media.media_audit.user_id
  ├──► coremusic_social.comments.user_id
  ├──► coremusic_social.shares.user_id
  ├──► coremusic_social.activity_feed.user_id
  ├──► coremusic_social.listening_room_members.user_id
  ├──► coremusic_social.user_achievements.user_id
  ├──► coremusic_social.social_notifications.user_id
  ├──► coremusic_download.download_queue.user_id
  ├──► coremusic_download.download_history.user_id
  ├──► coremusic_download.download_sources.user_id
  └──► coremusic_ai.user_preference_profiles.user_id

coremusic_musics.musics.id
  ├──► coremusic_musics.music_files.music_id
  ├──► coremusic_musics.music_lyrics.music_id
  ├──► coremusic_musics.music_genres.music_id
  ├──► coremusic_musics.music_tags.music_id
  ├──► coremusic_musics.music_stats.music_id
  ├──► coremusic_musics.music_similar.music_id / similar_music_id
  ├──► coremusic_musics.music_audio_features.music_id
  ├──► coremusic_musics.music_credits.music_id
  ├──► coremusic_musics.music_videos.music_id
  ├──► coremusic_media.device_tracks.music_id
  ├──► coremusic_media.media_metadata.music_id
  ├──► coremusic_user.user_listening_history.music_id
  ├──► coremusic_user.user_favorites.music_id
  ├──► coremusic_user.playback_queue.music_id
  ├──► coremusic_playlist.playlist_tracks.music_id
  ├──► coremusic_download.download_queue.track_id
  └──► coremusic_download.download_history.track_id

coremusic_musics.artists.id
  ├──► coremusic_musics.artist_members.artist_id
  ├──► coremusic_musics.music_credits.artist_id
  └──► coremusic_albums.album_credits.artist_id

coremusic_albums.albums.id
  ├──► coremusic_albums.album_discs.album_id
  ├──► coremusic_albums.album_stats.album_id
  ├──► coremusic_albums.album_genres.album_id
  ├──► coremusic_albums.album_credits.album_id
  └──► coremusic_musics.music_files.album_id

coremusic_playlist.playlists.id
  ├──► coremusic_playlist.playlist_tracks.playlist_id
  ├──► coremusic_playlist.playlist_collaborators.playlist_id
  ├──► coremusic_playlist.playlist_followers.playlist_id
  ├──► coremusic_playlist.playlist_stats.playlist_id
  └──► coremusic_media.device_playlists.playlist_id

coremusic_media.devices.id
  ├──► coremusic_media.device_playlists.device_id
  ├──► coremusic_media.device_tracks.device_id
  └──► coremusic_media.device_sync_history.device_id

coremusic_social.listening_rooms.id
  ├──► coremusic_social.listening_room_members.room_id
  └──► coremusic_social.listening_room_queue.room_id

coremusic_musics.podcast_shows.id
  ├──► coremusic_musics.podcast_episodes.show_id
  ├──► coremusic_musics.podcast_subscriptions.show_id
  └──► coremusic_musics.podcast_transcripts.episode_id

coremusic_musics.radio_stations.id
  ├──► coremusic_musics.radio_schedules.station_id
  └──► coremusic_musics.radio_now_playing.station_id

coremusic_musics.music_videos.id
  ├──► coremusic_musics.video_playback_history.video_id
  └──► coremusic_musics.video_subtitles.video_id
```

## 6. Naming Conventions

| Kural | Değer |
|-------|-------|
| **DB İsim** | `coremusic_[servis]` — snake_case |
| **Tablo İmi** | snake_case, çoğul (`users`, `music_files`) |
| **PK (UUID tablolar)** | `id BINARY(16) NOT NULL` — UUID v7 |
| **PK (INT tablolar)** | `id INT UNSIGNED NOT NULL AUTO_INCREMENT` |
| **FK Kolon** | `[tablo]_id` — BINARY(16) veya INT UNSIGNED |
| **Timestamp** | `created_at`, `updated_at`, `deleted_at` |
| **Soft Delete** | `is_deleted TINYINT(1) DEFAULT 0` + `deleted_at TIMESTAMP NULL` |
| **Boolean** | `TINYINT(1) DEFAULT 0/1` |
| **Enum** | MySQL ENUM (sabit değer listesi) |
| **Index** | `idx_[tablo]_[kolon]` |
| **Unique** | `uk_[tablo]_[kolon]` |
| **Engine** | InnoDB |
| **Charset** | utf8mb4_unicode_ci |

## 7. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | **ORM Yasak** — Sadece PDO prepared statement | SQL injection riski |
| 2 | **SELECT * Yasak** — Açık sütun listesi zorunlu | SQL injection riski |
| 3 | **Hard Delete Yasak** — Soft delete zorunlu (`is_deleted = 0`) | Veri kaybı |
| 4 | **BCNF Zorunlu** — Tüm tablolar BCNF formunda olmalı | Normal form ihlali |
| 5 | **Cross-DB FK** — Application-level join, DB-level FK yok | Referans bütünlüğü riski |
| 6 | **Snake Case** — Tüm isimler snake_case | Tutarlılık ihlali |
| 7 | **UUID v7** — Yeni tablolar BINARY(16) UUID v7 kullanmalı | Versiyonluluk sorunu |
| 8 | **Prepared Statement** — Tüm SQL sorguları prepared statement olmalı | SQL injection |
| 9 | **Explicit Columns** — INSERT/SELECT'te açık kolon listesi | Güvenlik riski |
| 10 | **Soft Delete** — `is_deleted` + `deleted_at` zorunlu | Veri kaybı |
| 11 | **Timestamp** — `created_at` + `updated_at` zorunlu | İzlenebilirlik |
| 12 | **InnoDB** — Tüm tablolar InnoDB motoru kullanmalı | ACID uyumsuzluğu |
| 13 | **UTF8MB4** — Tüm tablolar utf8mb4_unicode_ci | Karakter seti sorunu |
| 14 | **No Secret in Log** — Hassas veri log'da `[REDACTED]` | Güvenlik ihlali |
| 15 | **18 BCNF** — Toplam 18 izole veritabanı | Mimari ihlal |

## 8. Platform Desteği

| Platform | Veritabanı | Durum |
|----------|-----------|-------|
| Windows (Tier 1) | MySQL 9 | ✅ Ana geliştirme |
| Linux (Tier 2) | MySQL 9 | ✅ Destekli |
| macOS (Tier 3) | MySQL 9 | ✅ Destekli |
| Raspberry Pi (Tier 4) | MySQL 9 | ✅ Destekli |
| ReactOS (Tier 5) | MySQL 9 | ⚠️ Experimental |

## 9. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 DB Architecture | [[CLAUDE.md]] §18 | 18 BCNF tanımı |
| § 5 Cross-DB FK | [[ADR-040-database-authority]] | FK stratejisi |
| § 7 Guardrails | [[brain.md]] §11 | BCNF kuralları |
| § SQL Dosyaları | `.ai/.sql/mysql/` | Kaynak şemalar |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-15
**Mode:** Red Team · Human Mode · Truth Mode
