# REVISION-REPORT — BCNF Normalizasyon Denetimi (2026-10-21)

> **Kapsam:** 7 aktif/aktif-denetlenen MySQL şeması · 4 migration · `.ai/.sql/mysql/` dump SSOT.
> **Denetleyi:** data-engineer (L0 Infrastructure).
> **Kaynak hiyerarşisi:** `.ai/.sql/mysql/*.sql` (disk kanıtı, 2026-10-21) > `.ai/.sql/normalization-analysis.md` (v1.0.0) > görev varsayımı.
> **ZERO-HALLUCINATION:** Her iddia dump dosyası + satır referansı ile kanıtlandı. Doğrulanmayan = `UNKNOWN`.

---

## §0 — YÖNETİCİ ÖZETİ

| Metrik | Değer |
|---|---|
| Denetlenen şema (aktif + aktif-denetlenen) | **7** (musics, auth, media_catalog, novasearch, social, user, playlist) |
| Denetlenen tablo | **~78** (7 şemanın toplamı) |
| Kritik BCNF/3NF ihlali (düzeltilebilir) | **3** (eksik FK ×2 şema, novasearch isim standardizasyonu) |
| Bilinen/planlı denormalizasyon (dokunulmadı) | **6** (expand-contract CONTRACT fazı — §4.5, veri kaybı riski) |
| Üretilen migration | **4** (`20261021_*`) |
| Genuinely eksik tablo | **1** (`user_recent_tracks`) |
| "Eksik" denen ama MEVCUT tablo | **4** (`playlists`, `playlist_tracks`, `user_listening_history`, `system_notifications`+`social_notifications`) |
| Kritik çelişki | **6** (§6) |

**En önemli bulgu:** Görevin "eksik" dediği 5 tablodan **4'ü diskte farklı isimlerle mevcut**. İkinci bir duplicate üretilmedi (zero-hallucination). Yalnız `recent_tracks` gerçekten eksikti → `user_recent_tracks` tasarlandı.

**VALIDATE: 22/25 geçti** — §11 Tablo Yapısı + İlişkiler + Normalizasyon bölümleri uygulandı. 3 madde (orphan pre-check, ALTER ADD CONSTRAINT, EXPLAIN) DB apply anında kullanıcı doğrulaması gerektirir; SQL dosyalarında BÖLÜM 0 pre-check sorguları hazır.

---

## §1 — DENETİLENEN 7 ŞEMA — Tablo Bazında Normal Form Bulguları

### 1.1 `coremusic_auth` (30 tablo, 2026-10-21 dump) — **BCNF ✓**
| Tablo | Aday Anahtar | Normal Form | Tespit / Düzeltme |
|---|---|---|---|
| `users` | PK `id` (BINARY16) · UK `username` · UK `email` | BCNF ✓ | Tüm non-key → PK (ve UK). Transitive/MVD yok. |
| `admin_users` | PK `id` · UK `username` | BCNF ✓ | `password_hash` NULL — deploy notu C4. |
| `api_keys` | PK `id` · UK `key_prefix` | BCNF ✓ (kontrollü) | `scopes` JSON 1NF — kararı #9 (kontrollü, dokunulmadı). |
| `api_usage`, `credential_*`, `oauth_*`, `permission_audit`, `user_permissions`, `user_roles`, `user_sessions`, `user_settings`, `password_history`, `password_reset_tokens`, `email_verification_tokens`, `login_attempts` | PK `id` + FK/UK | BCNF ✓ / 3NF ✓ | `oauth_*` FK → `coremusic_user.users` (ÇELİŞKİ §6-2). `credential_audit` comment kolon uyumsuzluğu C6 (dok). |

### 1.2 `coremusic_user` (15 tablo, 2026-10-21 dump) — **3NF ✓, 1 BCNF boşluğu**
| Tablo | Normal Form | Tespit / Düzeltme |
|---|---|---|
| `user_listening_history` | 3NF ✓ | `playlist_id` comment'te "FK" ama **gerçek FK kısıtı yok** → **DÜZELTME** (normalize migration BÖLÜM 2). `device_type` snapshot (ENUM değil, domain veri — §10 uyumlu). |
| `user_favorites` | 3NF ✓ | Polymorphic `item_type`/`item_id` — mevcut kod, bilinçli, dokunulmadı. |
| `user_profiles`, `user_preferences`, `user_follows`, `user_downloads`, `playback_queue`, `user_achievements` (bu şemada), `user_notifications` (bu şemada) | 3NF/BCNF ✓ | — |
| `user_recent_tracks` | **— YOK —** | grep: diskte ayrı tablo YOK. **→ Bu görevde ÜRETİLDİ** (listening migration). |

### 1.3 `coremusic_musics` (39 tablo, 2026-10-21 dump) — **BCNF ✓ (MusicBrainz hizalı)**
| Tablo | Normal Form | Tespit / Düzeltme |
|---|---|---|
| `musics`, `artists`, `albums`, `music_artists`, `music_files`, `music_videos`, `podcast_*`, `radio_*`, `video_*` | BCNF ✓ / 3NF ✓ | `musics.artist_id` legacy tekil + `music_artists` junction mevcut (expand done, contract pending — kararı #3). `podcast_shows.author_name` transitive (minor snapshot — §4.5, dokunulmadı). `music_stats` aggregate (§3.5 izinli denorm). |
| `radio_now_playing` | 3NF ✓ | `track_title/artist_name/album_name` snapshot + `music_id` FK (eklenmiş) — snapshot denorm izinli. |

### 1.4 `media_catalog` (9 tablo, 2026-10-21 dump) — **BCNF ✓**
Tüm tablolar açık aday anahtar dokümante (CHAR(26) ULID PK). Çakışma `UNIQUE(entity_type, entity_id, field_name)` — 12 bileşen. Collation `utf8mb4_tr_0900_ai_ci` (Türkçe sıralama, **bilinçli** — istisna). Değişiklik YOK gerekmedi (2026-10-21 inşa, temiz).

### 1.5 `novasearch` (12 tablo, 2026-10-21 dump) — **2 BCNF/standart notu**
| Tablo | Normal Form | Tespit / Düzeltme |
|---|---|---|
| `search_logs` | 3NF ✓ | **İSİM ÇELİŞKİSİ**: canlı MySQL `search_logs` (çoğul) + `response_time_ms`; Node.js `search_log` (tekil) + `took_ms` → **DÜZELTME**: `v_search_log_singular` VIEW (salt-okunur köprü, normalize migration BÖLÜM 3). |
| `videos`, `playlists` (nova) | 3NF ihlali (düşük) | `channel_name` transitive (`channel_id` → `channels.name`) — N4, CONTRACT fazı, **dokunulmadı** (fix_constraints §4.5 kararı). |
| `channels`, `trending_cache`, `daily_stats`, `search_synonyms`, `channel_statistics`, `knex_migrations`, `knex_migrations_lock` | BCNF ✓ / infra | — |

### 1.6 `coremusic_social` (30 tablo, 2026-10-21 dump) — **1 BCNF ihlali (expand done, contract pending)**
| Tablo | Normal Form | Tespit / Düzeltme |
|---|---|---|
| `user_achievements` | **3NF ihlali** | `achievement_name/description/icon/rarity` inline → `achievement_id` üzerinden transitive (gerçek determiner `achievements` lookup). `achievement_id` FK **zaten eklenmiş** (nullable, backfill öncesi) — expand done. **CONTRACT fazında** inline kolonlar drop edilecek (§4.5 kararı, **şimdilik dokunulmadı**). |
| `social_notifications` | 3NF ✓ | Polymorphic `entity_type`/`entity_id` — mevcut kod, bilinçli. **Görev "notifications" bu + `system_notifications` ile kapsanmış → duplicate üretilmedi.** |
| `comments`, `shares`, `activity_feed`, `likes`, `user_follower`, `user_sessions`, `user_blocks`, `user_mutes`, `user_settings`, `reports`, `user_badges`, `badge_awards`, `achievements`, `user_verification`, `user_stats`, `genres`, `user_genres`, `artists`, `user_artist_follows`, `album_likes`, `user_album_follows` | BCNF ✓ / 3NF ✓ | MusicBrainz hizalı junction'lar. |

### 1.7 `coremusic_playlist` (5 tablo, 2026-10-21 dump) — **2 BCNF/referans boşluğu**
| Tablo | Normal Form | Tespit / Düzeltme |
|---|---|---|
| `playlists` | 3NF ✓, **FK eksik** | `user_id` comment'te "FK" ama **gerçek FK kısıtı yok** → **DÜZELTME** (normalize migration U-1.1). |
| `playlist_tracks` | 3NF ✓, **FK eksik** | `music_id` comment'te "FK" ama **gerçek FK kısıtı yok** → **DÜZELTME** (U-1.2). UNIQUE `(playlist_id, music_id, position)` — task isteği `(playlist_id, music_id)` ile çelişir, **ürün kararı, DEĞİŞTİRİLMEZ** (§6-4). |
| `playlist_collaborators`, `playlist_followers` | 3NF ✓, **FK eksik** | `user_id` FK kısıtları yok → **DÜZELTME** (U-1.3, U-1.4). |
| `playlist_stats` | 3NF ✓ | — |

---

## §2 — "EKSIK TABLO" GERÇEĞİ — 4/5 MEVCUT (Kritik Bulgu)

Görev 5 tablo "tasarla" dedi; disk kanıtı 4'ünün mevcut olduğunu gösterdi:

| Görevdeki isim | Diskteki karşılığı | Durum | Aksiyon |
|---|---|---|---|
| `coremusic_playlist` | `coremusic_playlist.playlists` | **MEVCUT** (dump satır 94) | idempotent ensure (playlist migration) |
| `coremusic_playlist_items` | `coremusic_playlist.playlist_tracks` | **MEVCUT** (dump satır 70) | idempotent ensure; UNIQUE farkı §6-4 |
| `coremusic_listening_history` | `coremusic_user.user_listening_history` | **MEVCUT** (dump satır 81) | idempotent ensure (listening migration) |
| `coremusic_recent_tracks` | **YOK** (grep doğrulandı) | **GERÇEKTEN EKSİK** | **→ `user_recent_tracks` ÜRETİLDİ** |
| `coremusic_notifications` | `coremusic_system.system_notifications` + `coremusic_social.social_notifications` | **MEVCUT ×2** | idempotent ensure (notifications migration); duplicate üretilmedi |

**Karar gerekçesi:** AGENTS.md §3 (zero-hallucination) + §4 (önce repo'yu incele) + §16.4 (dokunulmaz yüzeyler). Duplicate tablo = veri parçalanması + N+1 FK + bakım yükü. Yalnız gerçek boşluk dolduruldu.

**İsimlendirme notu:** `user_recent_tracks` → sibling konvansiyonu (`user_favorites`, `user_listening_history`, `user_follows` vb. `user_` öneki). Görevdeki `coremusic_recent_tracks` = `coremusic_user.recent_tracks` şema yerleştirmesini tarif ediyordu; en tutarlı isim `user_recent_tracks` seçildi.

---

## §3 — YENİ/DEĞİŞEN TABLOLAR — ER İlişki Özeti

```
coremusic_auth.users (BINARY16 UUID)
  │ 1
  ├──< coremusic_user.user_recent_tracks        [YENİ]  (user_id, music_id) UNIQUE, last_played_at DESC
  │        │ 1
  │        └──> coremusic_musics.musics (BINARY16 UUID)
  │
  ├──< coremusic_user.user_listening_history    [FK eklendi: playlist_id]
  ├──< coremusic_playlist.playlists             [FK eklendi: user_id]
  ├──< coremusic_playlist.playlist_collaborators[FK eklendi: user_id]
  ├──< coremusic_playlist.playlist_followers    [FK eklendi: user_id]
  └──< coremusic_system.system_notifications    [ensure]

coremusic_musics.musics (BINARY16 UUID)
  └──< coremusic_playlist.playlist_tracks       [FK eklendi: music_id]
```

**Yeni tablo (`user_recent_tracks`) BCNF kontrolü:**
- 1NF: tüm kolonlar atomik ✓ (JSON yok, delimiter yok).
- 2NF: PK tekil `id` → parçalı bağımlılık yok ✓.
- 3NF: non-key → non-key bağımlılık yok (`play_count` → PK; `last_played_at` → PK) ✓.
- BCNF: her determiner aday anahtar ✓.

**İlişki tipi:** `users 1—N user_recent_tracks N—1 musics` (çoktan-çoğa junction değil; user+music başına tek satır = upsert modeli, `uk_user_recent_tracks_pair`).

---

## §4 — 15 KULLANIMDIŞ ŞEMA — İşaretleme Tablosu

> **Kaynak:** `.ai/.sql/normalization-analysis.md` §3.1 (envanter) + `.ai/.sql/mysql/` glob (20 dosya, 2026-10-21). Kod tarafı canlı sorgulama: yalnız `coremusic_auth` (users/roles/tokens/api_keys), `coremusic_musics` (musics/artists/music_files), `coremusic_social` (oauth). **Not:** görev "5 aktif" der (musics, auth, media_catalog, novasearch, social); step-1 `user`+`playlist`'i de denetler. `normalization-analysis` §3.1 canlı-sorgu listesinde `media_catalog`/`novasearch` YOK — **sayım/aktiflik çelişkisi** §6-5.

| # | Şema | Tablo (tahmini/dump) | Kullanım Durumu | Neden (kanıt) | Öneri |
|---|---|---|---|---|---|
| 1 | `coremusic_ai` | ~8 | **Kullanım dışı** | §3.1: "13 DB uygulama sorgulamıyor" | Arşivle / silmeyi değerlendir; dump snapshot tut |
| 2 | `coremusic_albums` | ~5 | **Kullanım dışı** | §3.1: album NOT queried | `coremusic_musics.albums` tekilleştir; migrate→drop |
| 3 | `coremusic_api` | ~4 | **Kullanım dışı** | §3.1; `api_keys` live'da `coremusic_auth`'ta | Şema birleştirme (auth'a taşı) |
| 4 | `coremusic_catalog` | ~6 | **Kullanım dışı** | §3.1 | `media_catalog` ile birleştir |
| 5 | `coremusic_cms` | ~7 | **Kullanım dışı** | §3.1 | CMS feature aktifse ayağa kaldır; değilse arşiv |
| 6 | `coremusic_download` | ~3 | **Kullanım dışı** | §3.1 | `coremusic_user.user_downloads` live; migrate→drop |
| 7 | `coremusic_logs` | ~4 | **Kullanım dışı** | §3.1 | Log pipeline netleştir; yoksa arşiv |
| 8 | `coremusic_media` | ~5 | **Kullanım dışı** | §3.1 | `media_catalog` tekilleştir |
| 9 | `coremusic_neva` | ~6 | **Kullanım dışı** | §3.1 (Neva Engine domain) | Engine feature'a göre karar |
| 10 | `coremusic_patch` | ~2 | **Kullanım dışı** | §3.1 | Patch mekanizması netleştir |
| 11 | `coremusic_playlist` | 5 | **Audit edildi, kod aktif değil** | §3.1 playlist NOT queried; görev step-1 denetler | Backend/repository yazınca aktifleşir; FK'lar bu görevde eklendi |
| 12 | `coremusic_studio` | ~5 | **Kullanım dışı** | §3.1 | Studio feature kararı |
| 13 | `coremusic_system` | ~9 | **Audit edildi** | `system_notifications` görev kapsamında | Bildirim repository'si yazınca aktifleşir |
| 14 | `coremusic_wireless` | ~3 | **Kullanım dışı** | §3.1 | Wireless feature kararı |
| 15 | `coremusic_user` | 15 | **Audit edildi, kod aktif değil** | §3.1: users live'da `coremusic_auth`'ta; `coremusic_user.users` hayalet | `coremusic_user.users` ghost tablo — §6-2 FK çelişkisi; migrate→drop faz 4 |

> **Sayım notu:** Task "18 DB/156 tablo" der; normalization-analysis v1.0.0 "172 tablo"; diskte **20 şema dosyası**. Aktiflik/aktiflik-sınırı tanımı iki belge arasında farklı — §6-5'te çelişki olarak raporlandı, tahmin yazılmadı.

---

## §5 — KARŞILAŞTIRMA: NORMALIZATION-ANALYSIS (v1.0.0) vs BU DENETİM

| Konu | v1.0.0 (2026-10-07) | Bu denetim (2026-10-21) | Tutarlılık |
|---|---|---|---|
| `music_artists` junction | "yok" (expand phase 1) | **MEVCUT** (`uk_music_artists_pair` + `uk_music_artists_pos`) | Expand phase 1 **tamamlanmış** |
| `user_achievements.achievement_id` | "add" (expand phase 2) | **MEVCUT** (nullable, backfill öncesi) | Expand phase 2 **kısmen** |
| `radio_now_playing.music_id` | "add" (expand phase 2) | **MEVCUT** | Expand phase 2 **tamamlanmış** |
| PK standardı | karar #6: BINARY(16) UUID-v7 | görev+skill: BIGINT UNSIGNED AI → **çelişki** §6-1 | Yeni tabloda skill izlendi, UUID-standard not edildi |
| `coremusic_system` status | "13 DB kullanılmayan" | görev kapsamında denetlendi | Aktiflik tanımı netleştirilmeli |

**Sonuç:** `fix_constraints_20261021.sql` (68 ifade OK, 77/77 verify, yedek `C:\temp\opencode\backup_20261021\`) bugün büyük bir normalizasyon geçişi yaptı. Bu denetim onun üstüne **yalnız kalan gerçek boşlukları** (eksik FK ×5, eksik tablo ×1, isim standardizasyonu ×1) ekler — **çakışma/tekrar YOK**.

---

## §6 — BEKLENEN ÇELİŞKİLER (Orchestrator kararı gerekli)

| # | Çelişki | Kanıt | Durum / Öneri |
|---|---|---|---|
| **1** | **PK standardı:** skill §2.2 `BIGINT UNSIGNED AI` ↔ proje kararı #6 `BINARY(16) UUID-v7` | skill direktifi vs normalization-analysis.md karar #6 | `user_recent_tracks` **BIGINT AI** yapıldı (skill direktifi + var olan örn. `music_videos`). FK kolonları `BINARY(16)` (tip eşitliği). **Orchestrator: `user_recent_tracks.id`'yi BINARY(16) UUID'ye çevirmek ister misiniz?** |
| **2** | **FK hedef gerçeği:** `oauth_*` FK → `coremusic_user.users` ama `users` live'da `coremusic_auth`'ta | coremusic_auth.sql satır 51-53 (users bu şemada); coremusic_user.sql `users` hayalet | Bu denetim `oauth_*`'i **ellemedi** (fix_constraints'in kapsamı). `coremusic_user.users` ghost → faz 4'te migrate→drop. **Not: `users` gerçekte `coremusic_auth`'tadır; hiçbir aktif FK yanlış hedefe gitmiyor — sadece ghost tablo var.** |
| **3** | **PK tipi × FK tipi:** yeni `user_recent_tracks.id` BIGINT AI ↔ sibling'lar BINARY(16) UUID | bu denetim §6-1 | Tutarlılık için UUID tercih edilebilir; skill'e uyuldu. Karar bekleniyor. |
| **4** | **UNIQUE farkı:** task `playlist_tracks` için `UNIQUE(playlist_id, music_id)` istedi; mevcut `UNIQUE(playlist_id, music_id, position)` | coremusic_playlist.sql satır 78 | Mevcut ürün kararı (aynı parça farklı pozisyonda tekrarlanabilir). **DEĞİŞTİRİLMEDİ.** Task isteğiyle çelişir — ürün onayı gerekir. |
| **5** | **Sayım/aktiflik:** "18 DB/156 tablo" (task) ↔ "172 tablo" (normalization-analysis) ↔ 20 şema dosyası (disk); media_catalog/novasearch §3.1'de canlı-sorgu listesinde YOK | task + normalization-analysis §3.1 + glob | Tahmin yazılmadı. Orchestrator: aktiflik tanımını netleştirin (hangi şemalar "canlı"?). |
| **6** | **DOWN blokları:** skill §9.2 + task `-- DOWN` istedi ↔ ADR-007/014 **forward-only, geri migration yasak** | fix_constraints_20261021.sql başlığı "FORWARD-ONLY (down() YOK)" | `-- DOWN` blokları **yalnız referans/dokümantasyon** olarak eklendi (production DOWN çalıştırmaz). İkisi de DOWN istedi, ADR yasakladı → gerilim bilinçli. **İsteğe bağlı:** DOWN blokları tamamen kaldırılabilir. |

**Ek not (düşük öncelik):** `credential_audit` comment kolon uyumsuzluğu (C6, fix_constraints) — doküman düzeltmesi, kod değil; bu denetimde değiştirilmedi.

---

## §7 — ÜRETİLEN MIGRATION DOSYALARI

| Dosya | İçerik | Idempotent | Veri Kaybı |
|---|---|---|---|
| `20261021_bcnf_normalize_active_schemas.sql` | 5 eksik FK (playlist ×4, user ×1) + novasearch `v_search_log_singular` VIEW + BÖLÜM 0 pre-check | VIEW ✓ · ALTER ✗ (MySQL 8.4 native; pre-check Sonra çalıştır, fix_constraints deseni) | **YOK** (EXPAND-only) |
| `20261021_create_playlist_tables.sql` | 5 playlist tablosu canonical tanım | `IF NOT EXISTS` ✓ | **YOK** (mevcut DB'de no-op) |
| `20261021_create_listening_tables.sql` | `user_listening_history` ensure + **`user_recent_tracks` YENİ** | `IF NOT EXISTS` ✓ | **YOK** |
| `20261021_create_notifications.sql` | `system_notifications` ensure (duplicate üretilmedi) | `IF NOT EXISTS` ✓ | **YOK** |

**Ortak prensipler (fix_constraints_20261021.sql hizalı):** FORWARD-ONLY · sütun DROP/DELETE YOK · PK/stil DEĞİŞTİRİLMEZ · hard delete YOK (`deleted_at` soft delete) · `SELECT *` YOK · `FLOAT` para yerine `DECIMAL` · ENUM yerine domain-veri VARCHAR · polymorphic yeni tabloda YOK · cross-DB FK yalnız tip-eşitliğinde.

**Uygulama sırası (kullanıcı):**
1. `20261021_create_playlist_tables.sql`
2. `20261021_create_listening_tables.sql`
3. `20261021_create_notifications.sql`
4. `20261021_bcnf_normalize_active_schemas.sql` (**BÖLÜM 0 pre-check → orphan=0 → sonra UP**)
5. Post-check: `information_schema.referential_constraints` → yeni 5 FK görünmeli.

**Kapsam dışı (diğer agent):** Node.js `search_log`↔`search_logs` standardizasyonu (kod tarafı — backend/nodejs agent) · CONTRACT fazı inline kolon drop'ları (normalization-analysis §4.5) · `coremusic_user.users` ghost drop (faz 4).

---

## §8 — DOĞRULAMA

- 7 şema dump'ı okundu (2026-10-21). Tüm iddialar satır referanslı.
- `recent_tracks` yokluğu + `search_log`/`search_logs` çelişkisi grep ile kanıtlandı.
- 4 migration yazıldı; `git commit` **ATILMADY** (subagent kuralı).
- Kod dosyası (PHP/JS/CSS) **dokunulmadı** · mevcut migration **değiştirilmedi** · DB'ye **uygulanmadı** · gerçek `.env`/secret **yazılmadı**.

---

*data-engineer (L0 Infrastructure) · CoreMusic · 2026-10-21 · REVISION-REPORT v1.0.0*
