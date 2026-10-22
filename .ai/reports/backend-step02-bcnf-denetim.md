# Adım 02 — BCNF Normalizasyon Doğrulama Raporu

```
VERSION : 1.0.0 · DATE: 2026-10-22 · KATMAN: DB (2/3) · GÖREVLİ: data-engineer (Senior)
KAYNAK  : .ai/.sql/mysql/*.sql (21 dump, salt-okunur — dump'lara YAZMA YOK)
          .ai/reports/backend-step01-sema-dogrulama.md (§(c) AK/FD ham girdisi)
          shared/database/migrations/20261021_bcnf_normalize_active_schemas.sql (stil şablonu)
          .ai/.sql/mysql/REVISION-REPORT.md (2026-10-21 önceki denetim — çapraz kontrol)
          .ai/log.md (2026-10-22 regen kayıtları, satır 237-252)
KANIT   : doğrudan dump satır referansları (aşağıda "satır N" biçiminde); mysql KURULU DEĞİL
          → doğrulama %100 statik (DDL okuma + yapısal grep taraması), information_schema YOK
YÖNTEM  : BCNF = her non-trivier FD'nin determineri basehtar olmalı. AK/FD listesi her tablo
          için DDL'den (PK/UNIQUE KEY/FK/INDEX satırları) türetildi; determiner iddiası yalnız
          dump'da约束 olarak mevcut KEY/UNIQUE/FK ile kuruldu (uygulama varsayımı ile AK üretilmedi).
```

> **Kapsam gerçeği (step01 onayı):** 0 eksik tablo → iş = mevcut şemanın BCNF denetimi.
> **Derin denetim: 41 tablo** (görev "≈40" derdi; auth 13 + user 8 + playlist 5 + api 4 +
> system 4 + social 1 + musics 6 = 41, sayım düzeltildi). **Yüzey taraması: 153 tablo**
> (201 − novasearch 7 kapsam dışı − 41 derin).

---

## (a) DERİN DENETİM — AK · FD · BCNF KANITI (41 tablo)

Kısaltma: **AK** = aday anahtar (dump kısıtlarından türetildi) · **BK** = basehtar (superkey) ·
**D** = determiner · non-key→non-key bağımlılık yok = 3NF · D=BK her non-trivier FD'de = BCNF.

### a-1. coremusic_auth (13/13)

| Tablo | AK (dump kanıtı) | FD özeti | Durum |
|---|---|---|---|
| `users` | `{id}` · `{username}` (satır 354) · `{email}` (satır 355) | username→tüm non-key · email→tüm non-key · id→tüm; başka D yok | **BCNF ✓** |
| `admin_users` | `{id}` · `{user_id}` uq (satır 70) | user_id→admin_level,department,can_*×4,last_admin_action_at | **BCNF ✓** |
| `api_keys` | `{id}` · `{key_hash}` uq (satır 95) | key_hash→user_id,key_prefix,key_name,scopes,rate_limit,is_active,key_type; user_id→? D yok (bir kullanıcıda çok anahtar) | **BCNF ✓** · not: `scopes` JSON dizi (§c-9) |
| `api_usage` | `{id}` (bigint AI, satır 120) | yalnız trivier; append-only log | **BCNF ✓** |
| `credential_audit` | `{id}` (bigint AI, satır 144) | trivier; action/status CHECK ile kısıtlı (satır 155-156) | **BCNF ✓** |
| `credential_keys` | `{id}` · `{key_name}` uq (176) · `{key_hash}` uq (177) | key_name→… · key_hash→… (hepsi BK) | **BCNF ✓** |
| `credential_vault` | `{id}` · `{service_name,credential_name}` uq (satır 209) | çift→encrypted_value,encryption_*,environment… | **BCNF ✓** |
| `permission_audit` | `{id}` (satır 232) | trivier; append-only | **BCNF ✓** |
| `user_assigned_roles` | `{id}` · `{user_id,role_id}` uq (satır 256) | çift→assigned_by,assigned_at (bağımlı nitelik) | **BCNF ✓** |
| `user_roles` | `{id}` · `{role_name}` uq (satır 278) | role_name→role_description,permissions,is_system | **BCNF ✓** · not: `permissions` JSON dizi (§c-9) |
| `user_sessions` | `{id}` · `{session_token_hash}` uq (satır 299) | token_hash→user_id,ip,user_agent,expires… | **BCNF ✓** |
| `user_tokens` | `{id}` · `{token_hash}` uq (satır 324) | token_hash→tüm non-key | **BCNF ✓** · not: `scope` JSON dizi (§c-9) |
| `admin_activity_log` | `{id}` (satır 44) | trivier; append-only audit | **BCNF ✓** |

### a-2. coremusic_user (8/8)

| Tablo | AK | FD özeti | Durum |
|---|---|---|---|
| `user_profiles` | `{id}` · `{user_id}` uq (satır 190) | user_id→bio,dob,country,language,timezone,theme_gender,notification_*,profile_visibility | **BCNF ✓** · not: `theme_gender` users.gender ile çakışır (§c-3) |
| `user_preferences` | `{id}` · `{user_id}` uq (satır 164) | user_id→theme,audio_quality,auto_play,crossfade… | **BCNF ✓** |
| `user_listening_history` | `{id}` (satır 131) — dikkat: `{user_id,started_at}` **AK değil** (uq yok; yalnız KEY satır 132/135) | non-superkey D yok; started_at/music_id/playlist_id hepsi satırın niteliği | **BCNF ✓** · **step01 düzeltmesi:** step01 §(c) "user_id,started_at → music_id…" FD'si şemada kısıtlanmıyor, sorgu anahtarıdır |
| `user_recent_tracks` | `{id}` · `{user_id,music_id}` uq (satır 210) | çift→last_played_at,play_count | **BCNF ✓** |
| `user_favorites` | `{id}` · `{user_id,item_type,item_id}` uq (satır 90) | üçlü→created_at; başka D yok | **BCNF ✓** · not: polymorphic `item_id` FK'sız — bütünlük notu (§c-10) |
| `user_follows` | `{id}` · `{follower_id,following_id}` uq (satır 107) | ikili→created_at | **BCNF ✓** |
| `user_downloads` | `{id}` · `{user_id,music_id}` uq (satır 69) | çift→download_status,file_path,file_size,file_format,quality,downloaded_at,expires_at | **BCNF ✓** |
| `playback_queue` | `{id}` (satır 43) — `{user_id,position}` yalnız KEY non-unique (satır 44) → AK değil | position→? kısıt yok; is_playing/source/source_id satır niteliği | **BCNF ✓** |

### a-3. coremusic_playlist (5/5)

| Tablo | AK | FD özeti | Durum |
|---|---|---|---|
| `playlists` | `{id}` (satır 138) — **`{user_id,name}` AK DEĞİL**: dump'da uq yok, yalnız KEY+FULLTEXT (satır 139-143) | user_id→? kısıt yok; sayaçlar bağımlı nitelik (§c-1) | **BCNF ✓** · **step01 düzeltmesi:** step01 §(c) "{user_id,name} (kısmi)" dump'da desteklenmiyor |
| `playlist_tracks` | `{id}` · `{playlist_id,music_id,position}` uq (satır 109) | üçlü→added_by,added_at,note; `{playlist_id,position}` yalnız KEY non-unique (satır 110) | **BCNF ✓** · **step01 düzeltmesi:** step01 §(c) "{playlist_id,position} → music_id,added_by" FD'si şemada kısıtsız (uygulama katmanı doğrulamalı — §f-5) |
| `playlist_collaborators` | `{id}` · `{playlist_id,user_id}` uq (satır 44) | ikili→role,invited_by,invited_at,accepted_at | **BCNF ✓** |
| `playlist_followers` | `{id}` · `{playlist_id,user_id}` uq (satır 63) | ikili→created_at | **BCNF ✓** |
| `playlist_stats` | `{id}` · `{playlist_id,stats_date}` uq (satır 88) | ikili→daily/weekly/monthly/total_plays,followers,popularity_score | **BCNF ✓** |

### a-4. coremusic_api (4/4)

| Tablo | AK | FD özeti | Durum |
|---|---|---|---|
| `api_calls` | `{id,called_at}` PK (satır 44 — partition gereği, §f-1); **`{id}` süperkey** (AI + called_at→tüm non-key) | id→called_at → (id,called_at)→endpoint,method,http_status… | **BCNF ✓** · not: `api_key_id` FK YOK (tip=int unsigned, bkz. §d); FK+PARTITION birlikteliği §f-1 |
| `api_keys` | `{id}` · `{api_key_hash}` uq (satır 88) | api_key_hash→user_id,api_key_prefix,key_label,scopes,allowed_ips,is_active,expires_at | **BCNF ✓** · not: scopes/allowed_ips JSON (§c-9) · sahiplik §d |
| `rate_limits` | `{id}` · `{api_key_id,endpoint_pattern(255)}` uq (satır 112) | ikili→max_requests,window_sec,is_active | **BCNF ✓** · not: 255-karakter prefix-unique — uçak notu §f-9 |
| `webhooks` | `{id}` · `{api_key_id,event_type,callback_url(255)}` uq (satır 137) | üçlü→secret,is_active,retry_count,timeout_ms | **BCNF ✓** · not: prefix-unique §f-9 |

### a-5. coremusic_system — bildirim/ayar yüzeyi (4/4)

| Tablo | AK | FD özeti | Durum |
|---|---|---|---|
| `system_notifications` | `{id}` (satır 392) | user_id→? D yok; is_read/read_at/priority satır niteliği | **BCNF ✓** |
| `system_app_settings` | `{id}` · `{user_id,setting_key}` uq (satır 153) | ikili→setting_value,setting_type | **BCNF ✓** · not: soft-delete + uq tuzağı §c-8 |
| `system_settings` | `{id}` · `{setting_key,deleted_key}` uq (satır 468; deleted_key gen-col satır 466) | ikili→setting_value,setting_type,setting_group,description,is_public,is_readonly,validation_rules · canlı satırlarda setting_key tekeldir | **BCNF ✓** · deleted_key deseni soft-delete uq sorununu çözer |
| `system_config` | `{id}` · `{config_key,deleted_key}` uq (satır 253; gen-col satır 251) | ikili→config_value,config_type,config_group,description,is_sensitive,is_readonly | **BCNF ✓** · aynı desen |

### a-6. coremusic_social — bildirim yüzeyi (1/1)

| Tablo | AK | FD özeti | Durum |
|---|---|---|---|
| `social_notifications` | `{id}` (satır 291) | user_id/from_user_id/entity_* satır niteliği; non-superkey D yok | **BCNF ✓** · polymorphic entity bütünlük notu §c-10 |

### a-7. coremusic_musics — kritik tablolar (6/6)

| Tablo | AK | FD özeti | Durum |
|---|---|---|---|
| `musics` | `{id}` (satır 410) — `slug` uq DEĞİL (yalnız FULLTEXT satır 418) | artist_id/genre_id tekil FK (satır 419-420) + junction mevcut (expand done/contract pending — karar #3); sayaçlar bağımlı (§c-1) | **BCNF ✓** · not: slug tekilliği garanti değil (§f-4) |
| `music_files` | `{id}` (satır 225) — `{music_id,is_primary}` uq DEĞİL (yalnız KEY satır 229-230) | format/path/size/bit_rate/satır niteliği | **BCNF ✓** · **step01 düzeltmesi:** step01 §(c) "music_id,is_primary → dosya seçimi" şemada garanti değil (0..N birincil — §c-11) |
| `recordings` | `{id}` (satır 673) — `isrc` uq DEĞİL (yalnız KEY satır 674) | title,duration_ms,artist_credit_id,work_id satır niteliği; legacy_music_id iz (FK yok, bilinçli — satır 668) | **BCNF ✓** |
| `artist_credit` | `{id}` (satır 38) — display_name uq DEĞİL (KEY satır 39) | display_name = artist_credit_names çıktısı önbelleği (§c-5) | **BCNF ✓** |
| `artist_credit_names` | `{id}` · `{artist_credit_id,position}` uq (satır 56) | ikili→artist_id,join_phrase | **BCNF ✓** |
| `music_artists` | `{id}` · `{music_id,artist_id}` uq (satır 148) · `{music_id,position}` uq (satır 149) | her iki ikili de BK; is_primary/role parçanın niteliği | **BCNF ✓ · 4NF ✓** (karar #3; iki MVD birbirinden bağımsız değil — position par niteliği) |

**Derin denetim sonucu: 41/41 BCNF ✓. Derin sette YENİ BCNF İHLALİ = 0.**
Üç maddede step01 §(c) AK/FD iddiası dump kısıtlarıyla **düzeltildi** (yukarıda kalın notlar):
`playlists {user_id,name}` · `playlist_tracks {playlist_id,position}` · `music_files (music_id,is_primary)` —
üçü de dump'da kısıtlanmayan sorgu/uygulama varsayımlarıdır; şema BCNF'yi bozmaz.

---

## (b) YÜZEY TARAMASI — 153 tablo (hafif kriter)

**Hafif kriter seti (tüm 21 dump'a uygulandı — grep kanıtlı):**

| Kriter | Sonuç (kanıt) |
|---|---|
| PK yokluğu | **0** (step01 201/201; bu denetimde tekrarlanan sayım gerekmedi) |
| Çok-değerli kolon (`*_ids`/`*list`/`ids` varchar) | **0 isabet** (grep, 21 dump) |
| Yinelenen grup göstergesi (`col_0..col_4` numaralı kolon) | **0 isabet** (grep) |
| JSON kolon anahtar/KK pozisyonunda | **0** — 55 JSON kolonun tamamı veri alanında (grep listesi §c-9) |
| Sayaç kolon determinerliği | **0** — sayaçların tamamı bağımlı nitelik (§c-1) |
| Soft-delete + uq deseni (deleted_key) | 7 tablo (grep) — §c-8 |
| Transitive göstergesi (user atribütü kopyalama) | yalnız `channel_name` (novasearch, kapsam dışı) + bilinen snapshot'lar (§c-6/7) |

> **Sınır dürüstlüğü:** yüzey taraması tam FD türetmez; kriter = yukarıdaki yapısal
> kırmızı-bayrak göstergeleri + 2026-10-21 REVISION-REPORT bulgularının bugünkü dump'da
> doğrulanması. Tam derin FD analizi yalnız (a)'daki 41 tabloya uygulandı.

| DB (yüzey) | Tablo | Sonuç | Gerekçe |
|---|---|---|---|
| coremusic_ai (6) | audio_features · listening_features · model_versions · recommendation_history · training_jobs · user_preference_profiles | **geçti** (6/6) | Hafif kriterler temiz; JSON `features/metrics/genre_weights/mood_preferences` veri alanında |
| coremusic_albums (11) | album_credits · album_discs · album_genres · album_stats · albums · labels · media · release_groups · release_labels · releases · tracks | **geçti** (11/11) | Hafif kriterler temiz; sayaçlar (total_plays:93, total_tracks:123, total_duration_sec:124, play_count:126) bağımlı nitelik |
| coremusic_billing (8) | subscription_plans · payment_methods · user_subscriptions · payment_transactions · invoices · device_playback_sessions · content_licenses · royalty_payout_ledger | **geçti** (8/8) | Yeni şema (2026-10-22, log:247); her tabloda PK + doğal-key uq kanıtı (plan_code:55, invoice_number:186, session_token:218, artist_id+period_month:276) |
| coremusic_catalog (8) | catalog_album_types · catalog_artist_roles · catalog_countries · catalog_genres · catalog_instruments · catalog_languages · catalog_moods · catalog_playlist_types | **geçti** (8/8) | Sözlük/lookup; hafif kriterler temiz |
| coremusic_cms (9) | blog_categories · blog_post_tags · blog_posts · blog_tags · cms_banner_slides · cms_faqs · cms_media_assets · cms_pages · cms_page_revisions | **geçti** (9/9) | Hafif kriterler temiz; junction'lar (post×tag) par anahtarlı |
| coremusic_download (4) | download_cache · download_history · download_queue · download_sources | **geçti** (4/4) | Hafif kriterler temiz |
| coremusic_logs (22) | analytics_daily_genre · analytics_daily_music · analytics_daily_platform · analytics_daily_users · analytics_performance · analytics_realtime_events · analytics_retention · analytics_storage · audit_logs · daily_stats · error_logs · log_activity · log_events · log_performance · log_security · log_system · page_views · performance_metrics · rate_limit_logs · search_logs · user_activity_logs · user_events | **geçti** (22/22) | Append-only log/analitik; JSON alanları (metadata/context/request_body/threat_indicators/tags_json/event_data) veri alanında; sayaçlar bağımlı |
| coremusic_media (8) | device_playlists · device_sync_history · device_tracks · device_types · devices · media_access · media_audit · media_metadata | **geçti** (8/8) | Hafif kriterler temiz; `metadata/metadata_json` veri alanında |
| coremusic_musics (23) | artist_members · genres · music_audio_features · music_credits · music_genres · music_lyrics · music_similar · music_stats · music_tags · music_videos · podcast_episodes · podcast_shows · podcast_subscriptions · podcast_transcripts · podcast_transcript_segments · radio_now_playing · radio_play_history · radio_schedules · radio_stations · video_playback_history · video_subtitles · works | **geçti (22) + notlu (1: podcast_shows)** | music_stats: uq (music_id,stats_date:324) → aggregate BCNF ✓; radio_now_playing: uq (station_id:572) + bilinçli snapshot (§c-6) → BCNF ✓; **podcast_shows: `author_name` (satır 471) — author_user_id non-NULL iken users.display_name ile senkron riski = minor transitive (§c-7); author_user_id NULL iken (RSS import) geçerli tek kaynak → bilinçli, dokunulmadı** |
| coremusic_neva (4) | dsp_settings · eq_presets · routing_matrix · spectrum_analysis | **geçti** (4/4) | params_json/bands_json/spectrum_json veri alanında; eq_presets seed whitelist (log:239) |
| coremusic_patch (3) | migration_log · patches · schema_versions | **geçti** (3/3) | Altyapı tabloları |
| coremusic_social (11) | achievements · activity_feed · comment_likes · comments · listening_room_members · listening_room_queue · listening_rooms · oauth_connections · oauth_states · shares · user_achievements | **geçti (10) + İHLAL (1: user_achievements)** | **`user_achievements` — TEK GERÇEK İHLAL:** `achievement_type → achievement_name,achievement_description,achievement_icon,rarity` (satır 309-313); determiner `achievement_type` BK değil (uq = {user_id,achievement_type} satır 323); gerçek determiner `achievements` lookup'ı (uk achievement_type satır 44) → **3NF/BCNF ihlali (transitive)**. Expand TAMAM (`achievement_id` nullable FK eklendi, satır 308+329), **CONTRACT bekliyor** (inline 4 kolon drop = yıkıcı → erteli, normalization-analysis §4.5 kararı). **Migrate ÜRETİLMEDİ** (bkz. §e) |
| coremusic_studio (6) | session_equipment · studio_collaborators · studio_equipment · studio_presets · studio_sessions · studio_tracks | **geçti** (6/6) | studio_presets deleted_key deseni (satır 112-114) soft-delete uq'yu çözer; settings JSON veri alanında |
| coremusic_system (16) | i18n_languages · i18n_translations · i18n_ui_strings · i18n_user_locale · system_api_endpoints · system_backup · system_bluetooth_devices · system_cache · system_eq_presets · system_file_manager · system_jobs · system_migration_log · system_schema_versions · system_services · system_storage_mounts · system_wifi_networks | **geçti** (16/16) | i18n_translations: AK {id}/{translation_key,locale,plural_key,deleted_key} (satır 69) — çoğul formlar ayrı satır = 1NF uygun (plural_key gen-col satır 66); deleted_key varyant notu §c-8; system_cache uq cache_key; system_services uq service_code |
| coremusic_wireless (5) | bluetooth_audio_profiles · bluetooth_peers · network_profiles · sync_history · wifi_networks | **geçti** (5/5) | dns_servers/proxy_settings/metadata JSON veri alanında |
| media_catalog (9) | album · artist · asset · asset_tag · ingest_batch · koleksiyon · path_history · taxonomy · variant | **geçti** (9/9) | Kompozit AK'lar ({alan,deger}, {asset_id,dosya}, {asset_id,alan,deger}) tüm determiner; ULID char(26); DB collation istisnası bilinçli = utf8mb4_tr_0900_ai_ci (satır 14 — REVISION-REPORT §1.4 ile tutarlı) |

**Yüzey sonucu: 152 geçti / 1 ihlal (user_achievements — expand-done, contract-erteli) / 0 yeni şüpheli.**

---

## (c) DENORMALİZASYON NOTLARI (BCNF İHLALİ DEĞİL — yazma yolu / karar notları)

1. **Sayaç kolonlar determiner DEĞİL — step01 §(c) DOĞRULANDI:** `playlists.total_tracks/total_duration_sec/follow_count/like_count/play_count` (satır 129-133) · `musics.play_count/like_count/download_count` (satır 403-405) · `artists.monthly_listeners` (satır 98) · `podcast_shows.episode_count/subscriber_count` (satır 476-477). Hiçbir FD'de determiner pozisyonunda değiller → BCNF ihlali değil. **Yazma yolu senkronizasyonu step05'te kod tarafında garanti altına alınmalı** (tek transaction / tek kaynak stratejisi).
2. **Çift sayaç kümeleri (cross-table):** `playlists.follow_count` ↔ `playlist_stats.total_followers` (ikisi de `playlist_followers` satırlarından türetilir) · `musics.play_count` ↔ `music_stats.total_plays` · `musics.download_count` ↔ `music_stats.total_downloads`. Aynı metrik iki tabloda — yazma yolu tekilleştirilmeli (step05), şema düzeyinde BCNF ihlali değil.
3. **`users.gender` ↔ `user_profiles.theme_gender`:** aynı enum domain'i (male/female/neutral) iki yerde; uygulama senkronu step05 notu.
4. **`musics.artist_id` ↔ `music_artists(is_primary,position)` + `musics.genre_id` ↔ `music_genres`:** expand-done / contract-pending (karar #3, REVISION-REPORT §1.3). Sözleşme (eski kolon drop) ayrı kullanıcı kararı — dokunulmadı.
5. **`artist_credit.display_name` ↔ `artist_credit_names` join çıktısı:** display_name önbellek niteliğinde ("Queen & David Bowie"); `artist_credit_names` INSERT/UPDATE/DELETE yolunda yeniden hesaplama tetikleyicisi step05'te şart (aksi halde bozuk önbellek).
6. **`radio_now_playing` snapshot'ı** (track_title/artist_name/album_name/genre/duration_seconds, satır 562-566): bilinçli snapshot — `music_id` NULL olabilir (yayın kataloğun dışındaki parça). uq (station_id:572) tek-satır-garantisi. İzinli denorm.
7. **`podcast_shows.author_name` (satır 471):** `author_user_id` NULL iken (RSS/dış import) geçerli tek kaynak; non-NULL iken `users.display_name` ile senkron riski = minor transitive (REVISION-REPORT §1.3 ile tutarlı). İzinli snapshot, dokunulmadı.
8. **Soft-delete + UNIQUE tuzağı (expand-contract adayı — KARAR KULLANICIYA):** `is_deleted` soft delete varken uq anahtarına sahip tablolarda, silinen kaydın yerine aynı doğal anahtarla yeniden kayıt engellenir. Desenin çözümü dump'ta 7 tabloda mevcut (generated `deleted_key` kolonu + uq'ya eklenir): `i18n_translations`(67-69) · `i18n_ui_strings`(89-91) · `system_config`(251-253) · `system_eq_presets`(278-280) · `system_file_manager`(306-308) · `system_settings`(466-468) · `studio_presets`(studio:112-114). Desensiz kalan uq'lar (yeniden-ekleme yolu varsa tehlike): `system_app_settings`(153) · `user_downloads`(69) · `user_favorites`(90) · `user_assigned_roles`(256) · `playlist_collaborators`(44) · `playlist_followers`(63) · `music_genres`(247) · `music_artists`(148-149) · `user_recent_tracks`(210) · `users.username/email`(354-355 — bilinçli: isim/eposta mirası kalmaz) · `credential_keys`(176-177 — bilinçli: anahtar adı miras). **İki deleted_key varyantı var:** `to_seconds(deleted_at)` (saniye-çakışması riski, §f-6) ve `id` varyantı (`system_eq_presets:278`, `system_file_manager:306` — PK'dan türetildiği için çakışmasız, üstün). BCNF ihlali DEĞİL; düzeltme = ALTER ADD + uq yeniden tanımlama (backward-compatible, silme yok) → **migrate bu oturumda ÜRETİLMEDİ, karar kullanıcıya** (hangi tablolarda yeniden-ekleme yolu gerçekten var? uygulama bilgisi gerekir).
9. **Kontrollü JSON (1NF-AAD):** `user_roles.permissions`(271) · `api_keys.scopes`(auth:84) · `user_tokens.scope`(315) · `api_keys.scopes/allowed_ips`(api:78-79) + 44 veri-alanı JSON kolonu daha (55 toplam). Hiçbiri anahtar pozisyonunda değil → BCNF etkilenmez. ADR bekliyor (log.md:214: "1NF JSON — ADR bekliyor"); atomik-junction formuna geçiş ayrı mimari karardır — dokunulmadı.
10. **Polymorphic bütülüm notu:** `user_favorites.item_type/item_id` (satır 83-84) ve `social_notifications.entity_type/entity_id` (satır 281-282): item_id 7 muhtemel tabloya işaret eder, FK kurulamaz → referans bütünlüğü uygulama katmanında. Bilinçli mevcut tasarım (REVISION-REPORT §1.2/§1.6 aynı doğrultuda).
11. **`music_files.is_primary` 0..N:** birincil dosya garantisi şemada yok (uq yok, idx non-unique satır 230). Uygulama yazma yolunda tek-birincil kuralı step05'te garanti edilmeli.

---

## (d) api_keys SAHİPLİK KARARI (FD + tip kanıtlı — DROP YASAK, karar kullanıcıya)

**Tablo 1 — FD/tip karşılaştırması (bugünkü dump):**

| | `coremusic_auth.api_keys` | `coremusic_api.api_keys` |
|---|---|---|
| id tipi | `binary(16)` UUID v7 (satır 79) | `int unsigned` AI (satır 73) |
| Doğal anahtar (AK) | `key_hash` varchar(255) uq (satır 95) | `api_key_hash` char(64) uq (satır 88) |
| Çocuk FK'lar (referans yükü) | **1:** `api_usage.api_key_id → id` (Ek B:209 / satır 125) | **2:** `rate_limits.api_key_id → id` (satır 115) · `webhooks.api_key_id → id` (satır 141) |
| Tip hizası | — | `api_calls.api_key_id int unsigned` (satır 33) = `coremusic_api.api_keys.id` tipi ile birebir (auth'un binary(16) ile DEĞİL); FK kısıtı yok ama tip seçimi kasıtlı eşleşme |
| Ek yaşam-döngüsü atribütleri | `key_type` enum(user/service/server) — ADR-020 (satır 93) · `rate_limit` (satır 85) · `key_name` (satır 83) | `allowed_ips` JSON (satır 79) · `key_label` (satır 77) |
| BCNF | ✓ (her ikisi de kendi şemasında bağımsız-BCNF) | ✓ |

**Değerlendirme:** Sorun normalizasyon değil — **varlık ikiliği** (aynı iş-varlığı iki relation'da).
Her iki tablo kendi iç BCNF'de temiz; FD'si olan determinerler (id, key_hash/api_key_hash) basehtar.
Referans yükü + tip kanıtı `coremusic_api.api_keys'i` **çalışma-zamanı sahibi** yapar: rate-limit
kuralı ve webhook tanımları FK ile ona bağlı, partition'lı API çağrım logunun (`api_calls`) anahtarı
onun tipiyle hizalı. `coremusic_auth.api_keys` ise **yaşam-döngüsü/kayıt defteri** niteliğinde
(ADR-020 key_type, key_name, rate_limit).

**Öneri (uygulanmadı — karar kullanıcıya):** hedef tek-tablo = `coremusic_api.api_keys`;
expand-and-contract ile `key_type`/`rate_limit`/`key_name` auth'tan API'ye **ALTER ADD** ile taşınır
(eski kolon silinmez, DROP YASAK), uygulama yazma yolu geçer, boşalan tablo CONTRACT'ında ayrı
kararla ele alınır. **Çelişki notu:** REVISION-REPORT (2026-10-21) §4-3 "api_keys auth'a taşınsın"
önermişti (gerekçe: normalization-analysis §3.1 "kod sorgulamıyor"); bugünkü dump FK/tip kanıtı
bunun **tersini** gösteriyor (rate_limits/webhooks/coremusic_api.api_calls_api.api_keys hizası).
Hangi tabloyu canlı kodun sorguladığı **adım03 (backend) doğrulamasıdır** (§f-2).

---

## (e) ÜRETİLEN MIGRATION

**YOK — 0 dosya.** Gerekçe:

- Derin denetim 41/41 BCNF ✓ → yeni normalize edilecek gerçek ihlal yok.
- Yüzeydeki tek gerçek ihlal (`user_achievements` inline kolonlar) **CONTRACT = kolon DROP = yıkıcı DDL** → hard rule gereği yasak; expand zaten tamam (`achievement_id` FK mevcut); zamanlama normalization-analysis §4.5 kararıyla erteli → **karar-gerektiren, migrate yok**.
- Soft-delete + uq tuzağı (§c-8) BCNF ihlali değil; hangi tablolarda uygulama düzeyinde yeniden-ekleme yolu olduğu bilinmiyor → **şüpheli/karar-gerektiren → DUR + raporla**.
- api_keys sahiplik birleşimi §d kararı gerektirir (DROP + uygulama geçişi) → kullanıcı onayı bekliyor.

Apply/rollback talimatı: bu adımda üretilen dosya olmadığı için **yok**. Mevcut `20261021_*`
migration'ları bu raporla **çakışmıyor** (log.md:241 kanıtı: bekleyen 20261021 migration'ları
canlıya uygulanmış; step01 §(b) 4/4 yüzey TAM).

---

## (f) ⚠️ VERIFICATION REQUIRED KÜMESİ (10 madde)

| # | Bulgu (dump kanıtı) | Etki | Öneri / Sahip |
|---|---|---|---|
| 1 | `api_calls`: `CREATE TABLE` içinde hem FK `fk_ac_user` (satır 52) hem `PARTITION BY RANGE` (satır 54-67) var; MySQL 8+ bilinen kısıtı partitioned InnoDB tabloda FK desteklemez ve log.md:214 bu yüzden "api_calls FK uygulanmadı" diyor. Dump (mysqldump live iddiası) FK'yu gösteriyor — **çelişki, mysql kurulu değil, canlı SHOW CREATE TABLE teyit edilemedi.** | Bu dump dosyası başka bir makinede yeniden import edilirse FK/partition çakışması hatası çıkabilir | Canlı teyit → DBA/kullanıcı; gerekirse dump yeniden regen |
| 2 | api_keys sahiplik: hangi tablo canlı kodda sorgulanıyor (auth vs api) statik olarak kanıtlanamaz | §d birleştirme kararının ön koşulu | Adım03 (backend — kod taraması) |
| 3 | `user_roles.permissions` / `api_keys.scopes` / `allowed_ips` / `user_tokens.scope` 1NF-atomiklik ADR'si açık (log.md:214 "ADR bekliyor") | RBAC/scope sorgulama modeli | ADR + kullanıcı kararı |
| 4 | `playlists` + `musics.slug` + `recordings.isrc` + `artist_credit.display_name` için doğal-anahtar uq yok — tekrarlı kayıt şemada engellenmiyor | URL çözümleme / ISRC tekilliği uygulama katmanında | Uygulama katmanı doğrulaması (step05) veya ADR ile uq ekleme kararı |
| 5 | `playlist_tracks` uygulaması `(playlist_id,position)→music_id` tekilliği varsayabilir; şemada idx non-unique (satır 110) | Aynı pozisyonda iki parça possibility | Backend step03-05 doğrulaması |
| 6 | `deleted_key = to_seconds(deleted_at)` deseninde aynı saniyede iki soft-delete aynı doğal anahtarla UNIQUE çakışması üretir (5 tablo: i18n_translations, i18n_ui_strings, system_config, system_settings, studio_presets). `id` varyantı (system_eq_presets, system_file_manager) bu riski taşımaz | Nadir ama veri-girişi hatası | Canlı davranış testi (mysql yok); yeni eklemelerde `id` varyantı öner |
| 7 | `user_achievements` CONTRACT (inline 4 kolon drop) zamanlaması | Yıkıcı DDL yasağı nedeniyle ertelendi | Kullanıcı onayı + normalize-analysis §4.5 |
| 8 | REVISION-REPORT (2026-10-21) tablo sayıları (auth 30, user 15, social 30) ≠ bugünkü dump (13, 8, 12); eski rapor tablolarından bugünkü dump'da olmayanlar: `user_settings`, `password_history`, `password_reset_tokens`, `email_verification_tokens`, `login_attempts`, `user_permissions`, social `likes/user_blocks/user_mutes/reports/...`. 10-22 regen kayıtları (log:237-247) tablo KALDIRMA kaydı içermiyor | Eski raporun §1.4-§1.6 tabloları artık arşiv niteliğinde; hangi fazda kaldırıldıkları kayıtta yok | Vault-updater: 10-21→10-22 arası şema daralma geçmişi |
| 9 | `rate_limits.uq(api_key_id, endpoint_pattern(255))` ve `webhooks.uq(...callback_url(255))` 255-karakter PREFIX unique — 255. karakterden sonra farklılaşan iki değer girişinde 1062 hatası | Uzun endpoint/URL'lerde sahte çakışma | Canlı veri kontrolü; gerekirse ADR |
| 10 | `novasearch.sql` bugün kod şemasına yazıldı (3 tablo, `search_log` TEKİL — satır 5/34-80; log.md:252); canlı legacy knex şemasının (7 tablo, `search_logs` ÇOĞUL + `v_search_log_singular` view gömülü, log.md:241) durumu dump dosyasında görünmüyor | Dump ≠ canlı (step01 §d-1 ile aynı aile) | novasearch AYRI PROJE — kapsam dışı; sadece not |

### §5 — Yinelenen tablo adları (kapsam notu)

- `api_keys` (coremusic_auth + coremusic_api): **canlı çift** → §d kararı.
- `playlists` / `search_logs` (coremusic_* + novasearch): bugünkü `novasearch.sql` **artık bu
  tabloları içermiyor** (kod şeması: channels/videos/search_log — satır 34-80); legacy 7-tablo
  şeması git geçmişinde (log.md:252). Çakışma dump düzeyinde kapanmış sayılır; canlı legacy
  DB durumu §f-10. **novasearch'a dokunulmadı.**

---

## DOĞRULAMA ÖZETİ

- 41 derin tablonun tamamı DDL'den okundu (AK/FD satır referanslı).
- 153 yüzey tablosu 7 kriterli yapısal grep + REVISION-REPORT çapraz kontrolü ile tarandı.
- step01 §(c) doğrulandı (sayaçlar determiner değil); step01 §(c) 3 AK iddiası dump kısıtıyla
  düzeltildi; step01 §(d) #4 (api_keys) FD kanıtıyla karara bağlandı (öneri — kullanıcı onayı).
- Migration üretilmedi (0 gerçek yeni ihlal) · dump dosyalarına yazılmadı · canlı DB'ye
  uygulanmadı · novasearch/app-cpp/assets*/Css dokunulmadı · git commit ATILMADI.

*data-engineer (L0 Infrastructure) · CoreMusic · 2026-10-22 · backend-step02-bcnf-denetim v1.0.0*
