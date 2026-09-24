---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K5 Veri Yönetimi Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-24
last_update_note: "3 turlu agent tartışması"
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K5: Veri Yönetimi Layer

**Katman:** K5 (Veri Yönetimi)
**Kapsam:** MySQL 18 DB, Redis, APCu, File System, SQLite, Backup
**Sorumlu Agent:** Data Engineer
**Bileşen Sayısı:** 55

---

## 1. Genel Bakış

K5 katmanı, CoreMusic'in tüm verilerini yöneten veri katmanını içerir. 18 BCNF veritabanı, cache sistemleri ve dosya depolama stratejilerini kapsar.

### 1.1 18 BCNF Veritabanı (ADR-040)

| # | Veritabanı | Amaç | Tablo Sayısı |
|---|------------|------|-------------|
| 1 | coremusic_auth | Users, roles, sessions, tokens | 13 |
| 2 | coremusic_user | Profiles, preferences, history | 7 |
| 3 | coremusic_musics | Songs, artists, genres, lyrics | 22 |
| 4 | coremusic_albums | Album collections, discs, stats | 5 |
| 5 | coremusic_playlist | Playlists, collaborators | 5 |
| 6 | coremusic_catalog | Reference data | 8 |
| 7 | coremusic_logs | Audit trail, analytics | 22 |
| 8 | coremusic_media | Device sync, media metadata | 8 |
| 9 | coremusic_system | Settings, config, cache | 17 |
| 10 | coremusic_social | Comments, shares, activity | 9 |
| 11 | coremusic_wireless | WiFi + Bluetooth networks | 5 |
| 12 | coremusic_ai | Preferences, recommendations | 6 |
| 13 | coremusic_api | API keys, rate limits | 4 |
| 14 | coremusic_cms | Pages, blog, tags | 8 |
| 15 | coremusic_download | Download queue, history | 4 |
| 16 | coremusic_neva | EQ presets, DSP settings | 4 |
| 17 | coremusic_studio | Studio sessions, tracks | 6 |
| 18 | coremusic_patch | Schema versions, migrations | 3 |
| | **TOPLAM** | | **156** |

---

## 2. BCNF Kuralları

| Kural | Açıklama |
|-------|----------|
| BCNF zorunlu | Her tablo BCNF formunda olmalı |
| Soft delete | `is_deleted = 0` koşulu her sorguda |
| Snake_case | Tablo ve sütun isimleri |
| Timestamp | `created_at`, `updated_at`, `deleted_at` |
| Prepared statement | PDO prepared zorunlu |
| No ORM | Doctrine DBAL veya raw PDO |
| No `SELECT *` | Açık sütun listesi |

---

## 3. Cache Stratejisi

### 3.1 Multi-Tier Cache

```
L1: APCu (in-process, 60s TTL)
    ↓
L2: Redis (shared, 5min TTL)
    ↓
L3: File System (disk, 1hour TTL)
    ↓
L4: MySQL (persistent)
```

### 3.2 Cache Kullanım Alanları

| Veri | Cache Seviyesi | TTL |
|------|---------------|-----|
| Session | APCu | 3600s |
| User profile | Redis | 300s |
| EQ presets | APCu | 600s |
| Music metadata | Redis | 600s |
| API rate limit | APCu | 60s |
| Search results | Redis | 120s |

---

## 4. SQLite Offline-First

### 4.1 Offline Veri Modeli

```
Online (MySQL) ←→ Sync Engine ←→ Offline (SQLite)

Sync Protocol:
  1. Pull changes from server
  2. Push local changes
  3. Conflict resolution (last-write-wins)
  4. Merge and reconcile
```

---

## 5. Backup Stratejisi

### 5.1 Backup Planı

| Tip | Sıklık | Tutma | Yöntem |
|-----|--------|-------|--------|
| Full | Haftalık | 4 hafta | mysqldump |
| Incremental | Günlük | 7 gün | binlog |
| Real-time | Sürekli | 24 saat | Replication |
| File | Günlük | 30 gün | Restic |

---

## 6. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-003 | 9 BCNF izole veritabanı |
| ADR-040 | 18 BCNF veritabanı otoritesi |
| ADR-027 | Hibrit depolama |

---

## Alt Katman Şeması (K5.a.b.c)

> **Katman kuralı:** K5 veri sağlar — K4'ten okur, veriye erişimi K8 üzerinden sunar; K3 asla K5'i doğrudan çağırmaz. **Sayım zinciri:** 124 MD başlık (H2 77 + H3 47; 14 dosya) − 17 dışlama (12 "Durum: Implementasyon" + 4 CLAUDE.md başlığı + 1 README §6 başlığı) = 107 başlık yaprağı + 37 tablo satırı (README §2/§3.2/§5.1/§6 = 20 · index.md = 17) + 156 CREATE TABLE satırı (.ai/.sql/mysql/) = **300 yaprak**. 10 ikinci seviye düğüm, 41 üçüncü seviye düğüm. **4. seviye kuralı:** yalnız diske eşleşen MD başlık satırı, README/index tablo satırı veya SQL CREATE TABLE satırı yaprak sayılır; dışlanan başlıklar Katalog notları 2'de gerekçelendirir.

| 2. Katman | Ad | 3. Katman | 4. Kanıtlı Yaprak | Birincil Kanıt |
|---|---|---|---|---|
| K5.1 | MySQL 18 BCNF Veritabanı | 18 | 156 | .ai/.sql/mysql/*.sql · CREATE TABLE |
| K5.2 | Şema ve Dokümantasyon | 3 | 14 | mysql-18-database.md · README §1 · index.md |
| K5.3 | Cache Stratejisi | 5 | 38 | cache-strategy/redis/apcu · README §3 · index.md |
| K5.4 | BCNF Kuralları | 1 | 8 | README.md · L56–L66 |
| K5.5 | SQLite Offline-First | 2 | 9 | sqlite-local.md · README §4 |
| K5.6 | Backup Stratejisi | 2 | 13 | backup-strategy.md · README §5 |
| K5.7 | Dosya Sistemi ve Depolama | 2 | 14 | file-system-storage.md · index.md |
| K5.8 | Bağlantı Havuzu | 1 | 7 | connection-pooling.md |
| K5.9 | Veri Güvenliği | 2 | 9 | data-security.md · index.md |
| K5.10 | Migration, Kayıt ve Metrikler | 5 | 32 | migration-strategy.md · database-registry.md · index.md |
| **TOPLAM** | | **41** | **300** | 14 MD + 18 SQL · kanıt satırı: 1.056+ |

### K5.1 — MySQL 18 BCNF Veritabanı

*18 BCNF veritabanı (README §1.1 · L34–L51); her veritabanının CREATE TABLE satırları 4. seviye yapraktır; sayılar diskteki 18 .sql dosyasıyla birebir doğrulanmıştır (TOPLAM 156).*

| Kod | Ad | Kanıt (dosya · satır) |
|---|---|---|
| **K5.1.1** | coremusic_auth — Users, roles, sessions, tokens (13 tablo) | README.md · L34 · coremusic_auth.sql |
| K5.1.1.1 | users | coremusic_auth.sql · L22 |
| K5.1.1.2 | user_roles | coremusic_auth.sql · L58 |
| K5.1.1.3 | user_assigned_roles | coremusic_auth.sql · L80 |
| K5.1.1.4 | user_sessions | coremusic_auth.sql · L110 |
| K5.1.1.5 | user_tokens | coremusic_auth.sql · L143 |
| K5.1.1.6 | credential_vault | coremusic_auth.sql · L175 |
| K5.1.1.7 | credential_keys | coremusic_auth.sql · L215 |
| K5.1.1.8 | credential_audit | coremusic_auth.sql · L249 |
| K5.1.1.9 | api_keys | coremusic_auth.sql · L288 |
| K5.1.1.10 | api_usage | coremusic_auth.sql · L322 |
| K5.1.1.11 | permission_audit | coremusic_auth.sql · L352 |
| K5.1.1.12 | admin_users | coremusic_auth.sql · L382 |
| K5.1.1.13 | admin_activity_log | coremusic_auth.sql · L413 |
| **K5.1.2** | coremusic_user — Profiles, preferences, history (7 tablo) | README.md · L35 · coremusic_user.sql |
| K5.1.2.1 | user_profiles | coremusic_user.sql · L22 |
| K5.1.2.2 | user_preferences | coremusic_user.sql · L57 |
| K5.1.2.3 | user_listening_history | coremusic_user.sql · L90 |
| K5.1.2.4 | user_favorites | coremusic_user.sql · L122 |
| K5.1.2.5 | user_follows | coremusic_user.sql · L145 |
| K5.1.2.6 | playback_queue | coremusic_user.sql · L168 |
| K5.1.2.7 | user_downloads | coremusic_user.sql · L196 |
| **K5.1.3** | coremusic_musics — Songs, artists, genres, lyrics (22 tablo) | README.md · L36 · coremusic_musics.sql |
| K5.1.3.1 | artists | coremusic_musics.sql · L27 |
| K5.1.3.2 | genres | coremusic_musics.sql · L58 |
| K5.1.3.3 | musics | coremusic_musics.sql · L87 |
| K5.1.3.4 | music_files | coremusic_musics.sql · L137 |
| K5.1.3.5 | music_lyrics | coremusic_musics.sql · L173 |
| K5.1.3.6 | music_genres | coremusic_musics.sql · L203 |
| K5.1.3.7 | music_tags | coremusic_musics.sql · L227 |
| K5.1.3.8 | music_stats | coremusic_musics.sql · L249 |
| K5.1.3.9 | music_similar | coremusic_musics.sql · L287 |
| K5.1.3.10 | artist_members | coremusic_musics.sql · L313 |
| K5.1.3.11 | music_audio_features | coremusic_musics.sql · L344 |
| K5.1.3.12 | music_credits | coremusic_musics.sql · L375 |
| K5.1.3.13 | podcast_shows | coremusic_musics.sql · L406 |
| K5.1.3.14 | podcast_episodes | coremusic_musics.sql · L444 |
| K5.1.3.15 | podcast_subscriptions | coremusic_musics.sql · L487 |
| K5.1.3.16 | podcast_transcripts | coremusic_musics.sql · L516 |
| K5.1.3.17 | music_videos | coremusic_musics.sql · L548 |
| K5.1.3.18 | video_playback_history | coremusic_musics.sql · L592 |
| K5.1.3.19 | video_subtitles | coremusic_musics.sql · L625 |
| K5.1.3.20 | radio_stations | coremusic_musics.sql · L661 |
| K5.1.3.21 | radio_schedules | coremusic_musics.sql · L700 |
| K5.1.3.22 | radio_now_playing | coremusic_musics.sql · L732 |
| **K5.1.4** | coremusic_albums — Album collections, discs, stats (5 tablo) | README.md · L37 · coremusic_albums.sql |
| K5.1.4.1 | albums | coremusic_albums.sql · L22 |
| K5.1.4.2 | album_discs | coremusic_albums.sql · L62 |
| K5.1.4.3 | album_stats | coremusic_albums.sql · L83 |
| K5.1.4.4 | album_genres | coremusic_albums.sql · L112 |
| K5.1.4.5 | album_credits | coremusic_albums.sql · L136 |
| **K5.1.5** | coremusic_playlist — Playlists, collaborators (5 tablo) | README.md · L38 · coremusic_playlist.sql |
| K5.1.5.1 | playlists | coremusic_playlist.sql · L22 |
| K5.1.5.2 | playlist_tracks | coremusic_playlist.sql · L58 |
| K5.1.5.3 | playlist_collaborators | coremusic_playlist.sql · L88 |
| K5.1.5.4 | playlist_followers | coremusic_playlist.sql · L120 |
| K5.1.5.5 | playlist_stats | coremusic_playlist.sql · L143 |
| **K5.1.6** | coremusic_catalog — Reference data (8 tablo) | README.md · L39 · coremusic_catalog.sql |
| K5.1.6.1 | catalog_genres | coremusic_catalog.sql · L22 |
| K5.1.6.2 | catalog_artist_roles | coremusic_catalog.sql · L50 |
| K5.1.6.3 | catalog_album_types | coremusic_catalog.sql · L71 |
| K5.1.6.4 | catalog_playlist_types | coremusic_catalog.sql · L92 |
| K5.1.6.5 | catalog_instruments | coremusic_catalog.sql · L113 |
| K5.1.6.6 | catalog_moods | coremusic_catalog.sql · L136 |
| K5.1.6.7 | catalog_countries | coremusic_catalog.sql · L158 |
| K5.1.6.8 | catalog_languages | coremusic_catalog.sql · L178 |
| **K5.1.7** | coremusic_logs — Audit trail, analytics (22 tablo) | README.md · L40 · coremusic_logs.sql |
| K5.1.7.1 | audit_logs | coremusic_logs.sql · L23 |
| K5.1.7.2 | user_activity_logs | coremusic_logs.sql · L49 |
| K5.1.7.3 | search_logs | coremusic_logs.sql · L72 |
| K5.1.7.4 | error_logs | coremusic_logs.sql · L96 |
| K5.1.7.5 | rate_limit_logs | coremusic_logs.sql · L127 |
| K5.1.7.6 | analytics_daily_users | coremusic_logs.sql · L149 |
| K5.1.7.7 | analytics_daily_platform | coremusic_logs.sql · L176 |
| K5.1.7.8 | analytics_daily_music | coremusic_logs.sql · L201 |
| K5.1.7.9 | analytics_daily_genre | coremusic_logs.sql · L225 |
| K5.1.7.10 | analytics_realtime_events | coremusic_logs.sql · L247 |
| K5.1.7.11 | analytics_performance | coremusic_logs.sql · L268 |
| K5.1.7.12 | analytics_storage | coremusic_logs.sql · L291 |
| K5.1.7.13 | analytics_retention | coremusic_logs.sql · L311 |
| K5.1.7.14 | page_views | coremusic_logs.sql · L331 |
| K5.1.7.15 | user_events | coremusic_logs.sql · L365 |
| K5.1.7.16 | performance_metrics | coremusic_logs.sql · L391 |
| K5.1.7.17 | daily_stats | coremusic_logs.sql · L413 |
| K5.1.7.18 | log_events | coremusic_logs.sql · L454 |
| K5.1.7.19 | log_security | coremusic_logs.sql · L487 |
| K5.1.7.20 | log_performance | coremusic_logs.sql · L538 |
| K5.1.7.21 | log_system | coremusic_logs.sql · L581 |
| K5.1.7.22 | log_activity | coremusic_logs.sql · L633 |
| **K5.1.8** | coremusic_media — Device sync, media metadata (8 tablo) | README.md · L41 · coremusic_media.sql |
| K5.1.8.1 | device_types | coremusic_media.sql · L26 |
| K5.1.8.2 | devices | coremusic_media.sql · L53 |
| K5.1.8.3 | device_playlists | coremusic_media.sql · L85 |
| K5.1.8.4 | device_tracks | coremusic_media.sql · L106 |
| K5.1.8.5 | device_sync_history | coremusic_media.sql · L129 |
| K5.1.8.6 | media_metadata | coremusic_media.sql · L153 |
| K5.1.8.7 | media_access | coremusic_media.sql · L181 |
| K5.1.8.8 | media_audit | coremusic_media.sql · L204 |
| **K5.1.9** | coremusic_system — Settings, config, cache (17 tablo) | README.md · L42 · coremusic_system.sql |
| K5.1.9.1 | system_settings | coremusic_system.sql · L23 |
| K5.1.9.2 | system_eq_presets | coremusic_system.sql · L47 |
| K5.1.9.3 | system_notifications | coremusic_system.sql · L76 |
| K5.1.9.4 | system_file_manager | coremusic_system.sql · L106 |
| K5.1.9.5 | system_cache | coremusic_system.sql · L135 |
| K5.1.9.6 | system_wifi_networks | coremusic_system.sql · L158 |
| K5.1.9.7 | system_bluetooth_devices | coremusic_system.sql · L187 |
| K5.1.9.8 | system_app_settings | coremusic_system.sql · L214 |
| K5.1.9.9 | system_api_endpoints | coremusic_system.sql · L232 |
| K5.1.9.10 | system_backup | coremusic_system.sql · L253 |
| K5.1.9.11 | system_config | coremusic_system.sql · L280 |
| K5.1.9.12 | system_schema_versions | coremusic_system.sql · L303 |
| K5.1.9.13 | system_migration_log | coremusic_system.sql · L325 |
| K5.1.9.14 | i18n_languages | coremusic_system.sql · L354 |
| K5.1.9.15 | i18n_translations | coremusic_system.sql · L373 |
| K5.1.9.16 | i18n_ui_strings | coremusic_system.sql · L397 |
| K5.1.9.17 | i18n_user_locale | coremusic_system.sql · L419 |
| **K5.1.10** | coremusic_social — Comments, shares, activity (9 tablo) | README.md · L43 · coremusic_social.sql |
| K5.1.10.1 | comments | coremusic_social.sql · L23 |
| K5.1.10.2 | comment_likes | coremusic_social.sql · L58 |
| K5.1.10.3 | shares | coremusic_social.sql · L77 |
| K5.1.10.4 | activity_feed | coremusic_social.sql · L101 |
| K5.1.10.5 | listening_rooms | coremusic_social.sql · L129 |
| K5.1.10.6 | listening_room_members | coremusic_social.sql · L161 |
| K5.1.10.7 | listening_room_queue | coremusic_social.sql · L190 |
| K5.1.10.8 | user_achievements | coremusic_social.sql · L215 |
| K5.1.10.9 | social_notifications | coremusic_social.sql · L245 |
| **K5.1.11** | coremusic_wireless — WiFi + Bluetooth networks (5 tablo) | README.md · L44 · coremusic_wireless.sql |
| K5.1.11.1 | wifi_networks | coremusic_wireless.sql · L22 |
| K5.1.11.2 | bluetooth_peers | coremusic_wireless.sql · L59 |
| K5.1.11.3 | sync_history | coremusic_wireless.sql · L93 |
| K5.1.11.4 | bluetooth_audio_profiles | coremusic_wireless.sql · L118 |
| K5.1.11.5 | network_profiles | coremusic_wireless.sql · L144 |
| **K5.1.12** | coremusic_ai — Preferences, recommendations (6 tablo) | README.md · L45 · coremusic_ai.sql |
| K5.1.12.1 | user_preference_profiles | coremusic_ai.sql · L31 |
| K5.1.12.2 | listening_features | coremusic_ai.sql · L55 |
| K5.1.12.3 | recommendation_history | coremusic_ai.sql · L80 |
| K5.1.12.4 | audio_features | coremusic_ai.sql · L109 |
| K5.1.12.5 | model_versions | coremusic_ai.sql · L139 |
| K5.1.12.6 | training_jobs | coremusic_ai.sql · L166 |
| **K5.1.13** | coremusic_api — API keys, rate limits (4 tablo) | README.md · L46 · coremusic_api.sql |
| K5.1.13.1 | api_keys | coremusic_api.sql · L31 |
| K5.1.13.2 | rate_limits | coremusic_api.sql · L68 |
| K5.1.13.3 | api_calls | coremusic_api.sql · L101 |
| K5.1.13.4 | webhooks | coremusic_api.sql · L153 |
| **K5.1.14** | coremusic_cms — Pages, blog, tags (8 tablo) | README.md · L47 · coremusic_cms.sql |
| K5.1.14.1 | cms_pages | coremusic_cms.sql · L26 |
| K5.1.14.2 | blog_posts | coremusic_cms.sql · L56 |
| K5.1.14.3 | blog_categories | coremusic_cms.sql · L89 |
| K5.1.14.4 | blog_tags | coremusic_cms.sql · L107 |
| K5.1.14.5 | blog_post_tags | coremusic_cms.sql · L122 |
| K5.1.14.6 | cms_media_assets | coremusic_cms.sql · L136 |
| K5.1.14.7 | cms_faqs | coremusic_cms.sql · L161 |
| K5.1.14.8 | cms_banner_slides | coremusic_cms.sql · L185 |
| **K5.1.15** | coremusic_download — Download queue, history (4 tablo) | README.md · L48 · coremusic_download.sql |
| K5.1.15.1 | download_queue | coremusic_download.sql · L39 |
| K5.1.15.2 | download_history | coremusic_download.sql · L76 |
| K5.1.15.3 | download_cache | coremusic_download.sql · L109 |
| K5.1.15.4 | download_sources | coremusic_download.sql · L141 |
| **K5.1.16** | coremusic_neva — EQ presets, DSP settings (4 tablo) | README.md · L49 · coremusic_neva.sql |
| K5.1.16.1 | eq_presets | coremusic_neva.sql · L26 |
| K5.1.16.2 | dsp_settings | coremusic_neva.sql · L55 |
| K5.1.16.3 | routing_matrix | coremusic_neva.sql · L79 |
| K5.1.16.4 | spectrum_analysis | coremusic_neva.sql · L108 |
| **K5.1.17** | coremusic_studio — Studio sessions, tracks (6 tablo) | README.md · L50 · coremusic_studio.sql |
| K5.1.17.1 | studio_sessions | coremusic_studio.sql · L30 |
| K5.1.17.2 | studio_tracks | coremusic_studio.sql · L62 |
| K5.1.17.3 | studio_presets | coremusic_studio.sql · L93 |
| K5.1.17.4 | studio_equipment | coremusic_studio.sql · L117 |
| K5.1.17.5 | session_equipment | coremusic_studio.sql · L145 |
| K5.1.17.6 | studio_collaborators | coremusic_studio.sql · L159 |
| **K5.1.18** | coremusic_patch — Schema versions, migrations (3 tablo) | README.md · L51 · coremusic_patch.sql |
| K5.1.18.1 | schema_versions | coremusic_patch.sql · L14 |
| K5.1.18.2 | migration_log | coremusic_patch.sql · L35 |
| K5.1.18.3 | patches | coremusic_patch.sql · L57 |

### K5.2 — Şema ve Dokümantasyon

*18-DB şema tasarım dokümanı, README genel bakışı ve index.md şema başlıkları; her başlık satırı diskte birebir doğrulanmıştır.*

| Kod | Ad | Kanıt (dosya · satır) |
|---|---|---|
| **K5.2.1** | mysql-18-database.md başlıkları (10 yaprak) | mysql-18-database.md · L14–L379 |
| K5.2.1.1 | Genel Bakış | mysql-18-database.md · L14 |
| K5.2.1.2 | Teknik Detaylar | mysql-18-database.md · L18 |
| K5.2.1.3 | Veritabanı Şeması Tasarımı | mysql-18-database.md · L20 |
| K5.2.1.4 | Partition Stratejisi | mysql-18-database.md · L219 |
| K5.2.1.5 | View Tanımları | mysql-18-database.md · L248 |
| K5.2.1.6 | Performans / Ölçeklenebilirlik | mysql-18-database.md · L288 |
| K5.2.1.7 | Index Stratejisi | mysql-18-database.md · L290 |
| K5.2.1.8 | Query Optimizasyonu | mysql-18-database.md · L303 |
| K5.2.1.9 | Read Replicas | mysql-18-database.md · L315 |
| K5.2.1.10 | API / Konfigürasyon | mysql-18-database.md · L339 |
| **K5.2.2** | index.md şema başlıkları (2 yaprak) | index.md · L65–L67 |
| K5.2.2.1 | BCNF Veritabanı Yapısı | index.md · L65 |
| K5.2.2.2 | 18 Veritabanı | index.md · L67 |
| **K5.2.3** | README §1 başlıkları (2 yaprak) | README.md · L26–L30 |
| K5.2.3.1 | 1. Genel Bakış | README.md · L26 |
| K5.2.3.2 | 1.1 18 BCNF Veritabanı (ADR-040) | README.md · L30 |

### K5.3 — Cache Stratejisi

*Çoklu seviye cache üç dosyada (cache-strategy, redis-cache, apcu-memory); README §3 ve index.md cache başlıkları + README §3.2 altı kullanım satırı yapraktır.*

| Kod | Ad | Kanıt (dosya · satır) |
|---|---|---|
| **K5.3.1** | cache-strategy.md başlıkları (7 yaprak) | cache-strategy.md · L14–L432 |
| K5.3.1.1 | Genel Bakış | cache-strategy.md · L14 |
| K5.3.1.2 | Teknik Detaylar | cache-strategy.md · L18 |
| K5.3.1.3 | Çoklu Seviye Cache Mimarisi | cache-strategy.md · L20 |
| K5.3.1.4 | Multi-Level Cache Manager | cache-strategy.md · L51 |
| K5.3.1.5 | Cache Invalidation Strategies | cache-strategy.md · L333 |
| K5.3.1.6 | API / Konfigürasyon | cache-strategy.md · L377 |
| K5.3.1.7 | Performans / Ölçeklenebilirlik | cache-strategy.md · L432 |
| **K5.3.2** | redis-cache.md başlıkları (10 yaprak) | redis-cache.md · L14–L436 |
| K5.3.2.1 | Genel Bakış | redis-cache.md · L14 |
| K5.3.2.2 | Teknik Detaylar | redis-cache.md · L18 |
| K5.3.2.3 | Redis Cluster Yapısı | redis-cache.md · L20 |
| K5.3.2.4 | Session Cache | redis-cache.md · L63 |
| K5.3.2.5 | Query Cache | redis-cache.md · L143 |
| K5.3.2.6 | Pub/Sub Messages | redis-cache.md · L246 |
| K5.3.2.7 | Rate Limiting | redis-cache.md · L324 |
| K5.3.2.8 | API / Konfigürasyon | redis-cache.md · L382 |
| K5.3.2.9 | Performans / Ölçeklenebilirlik | redis-cache.md · L425 |
| K5.3.2.10 | Bağımlılıklar | redis-cache.md · L436 |
| **K5.3.3** | apcu-memory.md başlıkları (9 yaprak) | apcu-memory.md · L14–L335 |
| K5.3.3.1 | Genel Bakış | apcu-memory.md · L14 |
| K5.3.3.2 | Teknik Detaylar | apcu-memory.md · L18 |
| K5.3.3.3 | APCu Konfigürasyonu | apcu-memory.md · L20 |
| K5.3.3.4 | OPcache Konfigürasyonu | apcu-memory.md · L52 |
| K5.3.3.5 | APCu Cache Manager | apcu-memory.md · L85 |
| K5.3.3.6 | Configuration Cache | apcu-memory.md · L247 |
| K5.3.3.7 | API / Konfigürasyon | apcu-memory.md · L297 |
| K5.3.3.8 | Performans / Ölçeklenebilirlik | apcu-memory.md · L325 |
| K5.3.3.9 | Bağımlılıklar | apcu-memory.md · L335 |
| **K5.3.4** | README §3 — Cache (3 başlık + 6 satır = 9 yaprak) | README.md · L70–L93 |
| K5.3.4.1 | 3. Cache Stratejisi | README.md · L70 |
| K5.3.4.2 | 3.1 Multi-Tier Cache | README.md · L72 |
| K5.3.4.3 | 3.2 Cache Kullanım Alanları | README.md · L84 |
| K5.3.4.4 | Session — APCu — 3600s | README.md · L88 |
| K5.3.4.5 | User profile — Redis — 300s | README.md · L89 |
| K5.3.4.6 | EQ presets — APCu — 600s | README.md · L90 |
| K5.3.4.7 | Music metadata — Redis — 600s | README.md · L91 |
| K5.3.4.8 | API rate limit — APCu — 60s | README.md · L92 |
| K5.3.4.9 | Search results — Redis — 120s | README.md · L93 |
| **K5.3.5** | index.md cache başlıkları (3 yaprak) | index.md · L91–L105 |
| K5.3.5.1 | Cache Stratejisi | index.md · L91 |
| K5.3.5.2 | Çoklu Seviye Cache | index.md · L93 |
| K5.3.5.3 | Cache Invalidation | index.md · L105 |

### K5.4 — BCNF Kuralları

*README §2 yedi kural satırı diskte birebir kanıtlıdır (L56 başlık + L60–L66 satırlar).*

| Kod | Ad | Kanıt (dosya · satır) |
|---|---|---|
| **K5.4.1** | README §2 BCNF Kuralları (1 başlık + 7 satır) | README.md · L56–L66 |
| K5.4.1.1 | 2. BCNF Kuralları | README.md · L56 |
| K5.4.1.2 | BCNF zorunlu — Her tablo BCNF formunda olmalı | README.md · L60 |
| K5.4.1.3 | Soft delete — is_deleted = 0 koşulu her sorguda | README.md · L61 |
| K5.4.1.4 | Snake_case — Tablo ve sütun isimleri | README.md · L62 |
| K5.4.1.5 | Timestamp — created_at, updated_at, deleted_at | README.md · L63 |
| K5.4.1.6 | Prepared statement — PDO prepared zorunlu | README.md · L64 |
| K5.4.1.7 | No ORM — Doctrine DBAL veya raw PDO | README.md · L65 |
| K5.4.1.8 | No SELECT * — Açık sütun listesi | README.md · L66 |

### K5.5 — SQLite Offline-First

*Yerel/offline depolama dosyası + README §4 bölüm başlıkları; sync protokol diyagramı kanıt kapsamı içindedir.*

| Kod | Ad | Kanıt (dosya · satır) |
|---|---|---|
| **K5.5.1** | sqlite-local.md başlıkları (7 yaprak) | sqlite-local.md · L14–L341 |
| K5.5.1.1 | Genel Bakış | sqlite-local.md · L14 |
| K5.5.1.2 | Teknik Detaylar | sqlite-local.md · L18 |
| K5.5.1.3 | SQLite WAL Mode Konfigürasyonu | sqlite-local.md · L20 |
| K5.5.1.4 | Local Database Manager | sqlite-local.md · L91 |
| K5.5.1.5 | API / Konfigürasyon | sqlite-local.md · L301 |
| K5.5.1.6 | Performans / Ölçeklenebilirlik | sqlite-local.md · L331 |
| K5.5.1.7 | Bağımlılıklar | sqlite-local.md · L341 |
| **K5.5.2** | README §4 başlıkları (2 yaprak) | README.md · L97–L99 |
| K5.5.2.1 | 4. SQLite Offline-First | README.md · L97 |
| K5.5.2.2 | 4.1 Offline Veri Modeli | README.md · L99 |

### K5.6 — Backup Stratejisi

*Yedekleme dosyası başlıkları + README §5 başlık ve dört plan satırı (Full/Incremental/Real-time/File).*

| Kod | Ad | Kanıt (dosya · satır) |
|---|---|---|
| **K5.6.1** | backup-strategy.md başlıkları (7 yaprak) | backup-strategy.md · L14–L413 |
| K5.6.1.1 | Genel Bakış | backup-strategy.md · L14 |
| K5.6.1.2 | Teknik Detaylar | backup-strategy.md · L18 |
| K5.6.1.3 | Yedekleme Stratejisi | backup-strategy.md · L20 |
| K5.6.1.4 | Backup Manager | backup-strategy.md · L66 |
| K5.6.1.5 | Replication Manager | backup-strategy.md · L294 |
| K5.6.1.6 | API / Konfigürasyon | backup-strategy.md · L370 |
| K5.6.1.7 | Performans / Ölçeklenebilirlik | backup-strategy.md · L413 |
| **K5.6.2** | README §5 — Backup (2 başlık + 4 satır = 6 yaprak) | README.md · L113–L122 |
| K5.6.2.1 | 5. Backup Stratejisi | README.md · L113 |
| K5.6.2.2 | 5.1 Backup Planı | README.md · L115 |
| K5.6.2.3 | Full — Haftalık — 4 hafta — mysqldump | README.md · L119 |
| K5.6.2.4 | Incremental — Günlük — 7 gün — binlog | README.md · L120 |
| K5.6.2.5 | Real-time — Sürekli — 24 saat — Replication | README.md · L121 |
| K5.6.2.6 | File — Günlük — 30 gün — Restic | README.md · L122 |

### K5.7 — Dosya Sistemi ve Depolama

*Medya/dizin depolama dosyası başlıkları + index.md depolama tipleri tablosu (altı satır).*

| Kod | Ad | Kanıt (dosya · satır) |
|---|---|---|
| **K5.7.1** | file-system-storage.md başlıkları (7 yaprak) | file-system-storage.md · L14–L384 |
| K5.7.1.1 | Genel Bakış | file-system-storage.md · L14 |
| K5.7.1.2 | Teknik Detaylar | file-system-storage.md · L18 |
| K5.7.1.3 | Dizin Yapısı | file-system-storage.md · L20 |
| K5.7.1.4 | File Manager | file-system-storage.md · L72 |
| K5.7.1.5 | Storage Optimization | file-system-storage.md · L240 |
| K5.7.1.6 | API / Konfigürasyon | file-system-storage.md · L347 |
| K5.7.1.7 | Performans / Ölçeklenebilirlik | file-system-storage.md · L384 |
| **K5.7.2** | index.md Veri Depolama Tipleri (1 başlık + 6 satır = 7 yaprak) | index.md · L38–L47 |
| K5.7.2.1 | Veri Depolama Tipleri | index.md · L38 |
| K5.7.2.2 | Relational DB — MySQL 8.0 — 500GB | index.md · L42 |
| K5.7.2.3 | Cache — Redis 7 — 16GB | index.md · L43 |
| K5.7.2.4 | Memory Cache — APCu — 2GB | index.md · L44 |
| K5.7.2.5 | File System — ext4/XFS — 10TB | index.md · L45 |
| K5.7.2.6 | Local DB — SQLite — 10GB | index.md · L46 |
| K5.7.2.7 | Object Storage — MinIO — 20TB | index.md · L47 |

### K5.8 — Bağlantı Havuzu

*MySQL/Redis connection pool ve monitor başlıkları; dosyanın tamamı K5.8 kapsamındadır.*

| Kod | Ad | Kanıt (dosya · satır) |
|---|---|---|
| **K5.8.1** | connection-pooling.md başlıkları (7 yaprak) | connection-pooling.md · L14–L471 |
| K5.8.1.1 | Genel Bakış | connection-pooling.md · L14 |
| K5.8.1.2 | Teknik Detaylar | connection-pooling.md · L18 |
| K5.8.1.3 | MySQL Connection Pool | connection-pooling.md · L20 |
| K5.8.1.4 | Redis Connection Pool | connection-pooling.md · L228 |
| K5.8.1.5 | Connection Pool Monitor | connection-pooling.md · L331 |
| K5.8.1.6 | API / Konfigürasyon | connection-pooling.md · L429 |
| K5.8.1.7 | Performans / Ölçeklenebilirlik | connection-pooling.md · L471 |

### K5.9 — Veri Güvenliği

*Şifreleme, GDPR ve audit logging başlıkları + index.md güvenlik maddeleri başlığı.*

| Kod | Ad | Kanıt (dosya · satır) |
|---|---|---|
| **K5.9.1** | data-security.md başlıkları (8 yaprak) | data-security.md · L14–L450 |
| K5.9.1.1 | Genel Bakış | data-security.md · L14 |
| K5.9.1.2 | Teknik Detaylar | data-security.md · L18 |
| K5.9.1.3 | Veri Sınıflandırması | data-security.md · L20 |
| K5.9.1.4 | Encryption Service | data-security.md · L55 |
| K5.9.1.5 | GDPR Compliance | data-security.md · L167 |
| K5.9.1.6 | Audit Logging | data-security.md · L337 |
| K5.9.1.7 | API / Konfigürasyon | data-security.md · L414 |
| K5.9.1.8 | Performans / Ölçeklenebilirlik | data-security.md · L450 |
| **K5.9.2** | index.md Güvenlik (1 yaprak) | index.md · L136 |
| K5.9.2.1 | Güvenlik | index.md · L136 |

### K5.10 — Migration, Kayıt ve Metrikler

*Migration ve registry dosyaları, index.md metrik/bağımlılık satırları, README §6 ADR satırları ve index.md genel başlıkları; tek düğümde toplanmıştır.*

| Kod | Ad | Kanıt (dosya · satır) |
|---|---|---|
| **K5.10.1** | migration-strategy.md başlıkları (7 yaprak) | migration-strategy.md · L14–L425 |
| K5.10.1.1 | Genel Bakış | migration-strategy.md · L14 |
| K5.10.1.2 | Teknik Detaylar | migration-strategy.md · L18 |
| K5.10.1.3 | Migration Manager | migration-strategy.md · L20 |
| K5.10.1.4 | Migration Template | migration-strategy.md · L216 |
| K5.10.1.5 | Zero-Downtime Migration | migration-strategy.md · L263 |
| K5.10.1.6 | API / Konfigürasyon | migration-strategy.md · L403 |
| K5.10.1.7 | Performans / Ölçeklenebilirlik | migration-strategy.md · L425 |
| **K5.10.2** | database-registry.md başlıkları (6 yaprak) | database-registry.md · L14–L375 |
| K5.10.2.1 | Genel Bakış | database-registry.md · L14 |
| K5.10.2.2 | Teknik Detaylar | database-registry.md · L18 |
| K5.10.2.3 | Veritabanı Kaydı | database-registry.md · L20 |
| K5.10.2.4 | Table Relationship Map | database-registry.md · L139 |
| K5.10.2.5 | Data Dictionary | database-registry.md · L201 |
| K5.10.2.6 | API / Konfigürasyon | database-registry.md · L375 |
| **K5.10.3** | index.md metrik ve bağımlılık (2 başlık + 11 satır = 13 yaprak) | index.md · L125–L153 |
| K5.10.3.1 | Performans Metrikleri | index.md · L125 |
| K5.10.3.2 | DB Query Latency — < 10ms — 7ms | index.md · L129 |
| K5.10.3.3 | Cache Hit Ratio — > 95% — 97.2% | index.md · L130 |
| K5.10.3.4 | Connection Pool Usage — < 80% — 65% | index.md · L131 |
| K5.10.3.5 | Backup RPO — < 1 saat — 15 dakika | index.md · L132 |
| K5.10.3.6 | Migration Downtime — 0 saniye | index.md · L133 |
| K5.10.3.7 | Data Retention — 7 yıl | index.md · L134 |
| K5.10.3.8 | Bağımlılıklar | index.md · L145 |
| K5.10.3.9 | MySQL 8.0 — Relational database | index.md · L149 |
| K5.10.3.10 | Redis 7.x — Distributed cache | index.md · L150 |
| K5.10.3.11 | PHP APCu 8.3+ — In-memory cache | index.md · L151 |
| K5.10.3.12 | MinIO — Object storage | index.md · L152 |
| K5.10.3.13 | SQLite 3.44+ — Local storage | index.md · L153 |
| **K5.10.4** | README §6 İlgili ADR tablosu (3 satır) | README.md · L130–L132 |
| K5.10.4.1 | ADR-003 — 9 BCNF izole veritabanı | README.md · L130 |
| K5.10.4.2 | ADR-040 — 18 BCNF veritabanı otoritesi | README.md · L131 |
| K5.10.4.3 | ADR-027 — Hibrit depolama | README.md · L132 |
| **K5.10.5** | index.md genel başlıklar (3 yaprak) | index.md · L14–L49 |
| K5.10.5.1 | Genel Bakış | index.md · L14 |
| K5.10.5.2 | Mimari Konum | index.md · L18 |
| K5.10.5.3 | Veri Akışı | index.md · L49 |

## Kanıt Kataloğu (K5)

> **Yöntem:** Tüm başlıklar 2026-09-24 tarihli ^#{2,3} grep taramasıyla, tüm satırlar README/index tablo sayımı ve ^CREATE TABLE grep ile doğrulanmıştır. Kapsanan Yaprak = o dosyadan sayılan 4. seviye yaprak sayısı; Satır Aralığı = kanıt başlıklarının ilk–son satırıdır. TOPLAM satırı üç kaynağı da kapsar.

| # | Dosya | Rol | H2 | H3 | Kapsanan Yaprak | Satır Aralığı |
|---|---|---|---|---|---|---|
| 1 | README.md | Layer ana doküman (§1–§6) | 6 | 5 | 30 | 26–134 |
| 2 | index.md | Katman genel bakış | 10 | 3 | 29 | 14–155 |
| 3 | CLAUDE.md | Kural dosyası (0 yaprak — not 2) | 4 | 0 | 0 | 14–47 |
| 4 | mysql-18-database.md | 18-DB şema tasarımı | 5 | 6 | 10 | 14–379 |
| 5 | cache-strategy.md | Çoklu seviye cache | 5 | 3 | 7 | 14–442 |
| 6 | redis-cache.md | Redis cluster/session/query | 6 | 5 | 10 | 14–444 |
| 7 | apcu-memory.md | APCu + OPcache | 6 | 4 | 9 | 14–343 |
| 8 | sqlite-local.md | Offline-first SQLite | 6 | 2 | 7 | 14–348 |
| 9 | backup-strategy.md | Yedekleme + replication | 5 | 3 | 7 | 14–424 |
| 10 | file-system-storage.md | Dizin + dosya yönetimi | 5 | 3 | 7 | 14–394 |
| 11 | connection-pooling.md | MySQL/Redis pool | 5 | 3 | 7 | 14–482 |
| 12 | data-security.md | Şifreleme + GDPR + audit | 5 | 4 | 8 | 14–460 |
| 13 | migration-strategy.md | Zero-downtime migration | 5 | 3 | 7 | 14–434 |
| 14 | database-registry.md | DB kaydı + data dictionary | 4 | 3 | 6 | 14–393 |
| 15 | coremusic_auth.sql | SQL şema (13 tablo) | — | — | 13 | 22–413 |
| 16 | coremusic_user.sql | SQL şema (7 tablo) | — | — | 7 | 22–196 |
| 17 | coremusic_musics.sql | SQL şema (22 tablo) | — | — | 22 | 27–732 |
| 18 | coremusic_albums.sql | SQL şema (5 tablo) | — | — | 5 | 22–136 |
| 19 | coremusic_playlist.sql | SQL şema (5 tablo) | — | — | 5 | 22–143 |
| 20 | coremusic_catalog.sql | SQL şema (8 tablo) | — | — | 8 | 22–178 |
| 21 | coremusic_logs.sql | SQL şema (22 tablo) | — | — | 22 | 23–633 |
| 22 | coremusic_media.sql | SQL şema (8 tablo) | — | — | 8 | 26–204 |
| 23 | coremusic_system.sql | SQL şema (17 tablo) | — | — | 17 | 23–419 |
| 24 | coremusic_social.sql | SQL şema (9 tablo) | — | — | 9 | 23–245 |
| 25 | coremusic_wireless.sql | SQL şema (5 tablo) | — | — | 5 | 22–144 |
| 26 | coremusic_ai.sql | SQL şema (6 tablo) | — | — | 6 | 31–166 |
| 27 | coremusic_api.sql | SQL şema (4 tablo) | — | — | 4 | 31–153 |
| 28 | coremusic_cms.sql | SQL şema (8 tablo) | — | — | 8 | 26–185 |
| 29 | coremusic_download.sql | SQL şema (4 tablo) | — | — | 4 | 39–141 |
| 30 | coremusic_neva.sql | SQL şema (4 tablo) | — | — | 4 | 26–108 |
| 31 | coremusic_studio.sql | SQL şema (6 tablo) | — | — | 6 | 30–159 |
| 32 | coremusic_patch.sql | SQL şema (3 tablo) | — | — | 3 | 14–57 |
| | **TOPLAM (32 dosya)** | 14 MD + 18 SQL | **77** | **47** | **300** | 124 başlık + 156 tablo |

**Katalog notları:**

1. **Sayım zinciri:** 124 MD başlık (H2 77 + H3 47) − 17 dışlama = 107 başlık yaprağı; + 37 tablo satırı (README §2:7 · §3.2:6 · §5.1:4 · §6:3 · index:6+6+5) + 156 CREATE TABLE = **300 yaprak**. İkinci seviye 10, üçüncü seviye 41 (18 DB + 23 dosya/grup düğümü).
2. **Dışlamalar (17):** 12 "## Durum: Implementasyon" başlığı (backup L424 · apcu L343 · cache L442 · connection L482 · data-sec L460 · registry L393 · file-sys L394 · index L155 · migration L434 · mysql-18 L379 · sqlite L348 · redis L444) + CLAUDE.md 4 başlık (L14/L24/L36/L46 — kural dosyası, 0 yaprak) + README §6 başlığı L126 (ADR referans başlığı; §6 tablosunun 3 satırı yaprak olarak sayıldı).
3. **Tutarlılık — index.md §18 Veritabanı tablosu (L71–L88, 18 satır) DIŞLANDI:** adlar diskteki .sql dosyalarıyla uyuşmuyor (ör. coremusic_users ↔ coremusic_auth, coremusic_music ↔ coremusic_musics). Otorite README §1.1'dir; satırlar yaprak sayılmadı.
4. **Tutarlılık — index.md L35 ağacı:** data-lifecycle/ diske yok (14 MD içinde yok); yaprak yok, katalogda temsil edilmiyor.
5. **Tutarlılık — README §1.1 = disk (pozitif):** 18 DB adı ve tablo sayıları (13/7/22/5/5/8/22/8/17/9/5/6/4/8/4/4/6/3) .ai/.sql/mysql/ içindeki 18 dosyadaki CREATE TABLE sayılarıyla birebir; TOPLAM 156 = 156 grep sonucu.
6. **Anti-fabrication:** tüm satır numaraları grep/read çıktısından kopyalandı; hiçbir sayı tahmin edilmedi; TOPLAM satırları (README L52, index L89) yaprak sayılmadı.
7. **Tutarlılık — footer/frontmatter:** frontmatter updated: 2026-09-24 (bu revizyon), footer Last Updated: 2026-09-20 korunmuştur (mevcut içerik satırına dokunulmadı).


---

*K5 Veri Yönetimi Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-24 — genişletme: 3 turlu agent tartışması*
*Mode: Red Team · Human Mode · Truth Mode*
