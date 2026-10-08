---
title: "CoreMusic — Veritabanı Normalizasyon Analizi"
type: guide
category: documentation
version: 1.0.0
status: active
authority: reference
updated: 2026-10-07
---

# CoreMusic — Veritabanı Normalizasyon Analizi

**Zorunlu Bağlantılar:** [[../.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]] · [[CLAUDE]] · [[../.decisions/accepted/ADR-040-database-authority]]

---

## §1 Amaç

Bu doküman, `.ai/.sql/mysql/` altındaki **20 dump dosyasındaki 172 tabloyu** enterprise normalizasyon ölçütlerine (1NF→2NF→3NF→BCNF→4NF→5NF) karşı analiz eder ve **kanıt tabanlı bulgular + onaya sunulan hedef model taslağı** üretir. Hedef kitle: Vault Steward (karar mercii), Data Engineer (migration uygulayıcısı) ve bu alan üzerinde çalışan AI ajanları.

Neden şimdi: promp §36 (Human Approval Gate) gereği herhangi bir şema değişikliğinden ÖNCE DATABASE NORMALIZATION ANALYSIS zorunludur; ayrıca envanter (2026-10-07) vault ile disk arasında 7 çelişki tespit etmiştir (§4.3). Bu rapor **hiçbir dump dosyasını, uygulama kodunu veya canlı veritabanını değiştirmez.**

Etiket sözlüğü (her bulgu için zorunlu): `[PROJECT EVIDENCE]` (disk/kod kanıtı) · `[EXTERNAL VERIFIED]` (web kaynağı) · `[ARCHITECTURAL INFERENCE]` · `[UNKNOWN]` · `[VERIFY REQUIRED]`.

## §2 Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| 20 dump dosyası / 172 tablo: normalizasyon bulguları (1NF-5NF), FD analizi, güvenlik/audit/index incelemesi | Uygulama kodu değişikliği, canlı DB işlemi |
| Hedef model taslağı (MusicBrainz hizalı müzik domaini + onaylı 11 karar) | Migration script üretimi/çalıştırılması (sonraki faz, ayrıca onay) |
| Çelişki envanteri + vault revizyon ÖNERİLERİ (ADR-040, db-engine-notes) | Vault dosyalarının bu fazda revizyonu |
| Riskler + doğrulama planı (§35 L/M) | DROP/TRUNCATE/DELETE içeren herhangi bir işlem |

*Alt konular:* envanter → bulgular → FD/ER → hedef model → kolon kararları → müzik domain doğrulaması → güvenlik → index → migration taslağı → doğrulama → riskler → onay kapısı.
Kapsam dışı için: migration SQL → `database-normalize-maker` skill'i (`templates/migration-template.sql`), karar kaydı → `[[../.templates/adr/adr-template]]`.

## §3 Mimari

### §3.1 Mevcut Şema Envanteri (§35-B — 20 dosya / 172 tablo) `[PROJECT EVIDENCE]`

Ölçüm: `.ai/.sql/mysql/*.sql` glob = 20 dosya; `CREATE TABLE` = 172 (156 = 18 coremusic_* + 9 media_catalog + 7 novasearch); `FOREIGN KEY` clause = 92 (12 dosya); **cross-DB `REFERENCES other_db.` = 28** (social 13 · user 11 · system 4 — grep 2026-10-07); ENUM = 108 (12 dosya); JSON kolon ≈ 40; PK = 172/172 mevcut; `INSERT INTO` = 0 (salt şema); `FLOAT` = 0; `ENGINE=InnoDB` = 172/172.

| Dosya (`.ai/.sql/mysql/`) | DB | Tablo | PK tipi | Öne çıkan normalizasyon bulgusu |
|---|---|---:|---|---|
| coremusic_auth.sql | coremusic_auth | 13 | BINARY(16) | `credential_audit` BCNF comment `(…, created_at)` ≠ kolon `occurred_at` (satır 246 vs 261) — comment/column mismatch `[PROJECT EVIDENCE]` · `api_keys.scopes` JSON (kontrollü, karar #9) |
| coremusic_user.sql | coremusic_user | 7 | BINARY(16) | **11 cross-DB FK** → auth.users / musics.musics |
| coremusic_musics.sql | coremusic_musics | 22 | BINARY(16) | `musics.artist_id` tekil (4NF/N:M — karar #3) · `radio_now_playing` (satır 732-749): `track_title/artist_name/album_name` + `station_id` FK ama **music_id FK yok** — snapshot denorm `[PROJECT EVIDENCE]` |
| coremusic_albums.sql | coremusic_albums | 5 | BINARY(16) | `album_credits.person_name` yanındaki person FK — kaçış genişletilmiş, kasıtlı (yorum: "CoreMusic dışı ise") |
| coremusic_playlist.sql | coremusic_playlist | 5 | BINARY(16) | `playlist_tracks` junction mevcut (iyi) — uygulama kodu hiç sorgulamıyor `[PROJECT EVIDENCE]` |
| coremusic_catalog.sql | coremusic_catalog | 8 | BINARY(16) | Lookup DB — hedef modelin parçası (karar #5: domain verisi buraya) |
| coremusic_logs.sql | coremusic_logs | 22 | karışık | Append-only ailesi → timestamp politikası karar #7 (yalnız created_at); `analytics_*` 4 tabloda created_at bile yok |
| coremusic_media.sql | coremusic_media | 8 | BINARY(16) | Bulgu yok (özel) |
| coremusic_system.sql | coremusic_system | 17 | BINARY(16) | **4 cross-DB FK** → auth.users · `system_eq_presets`/`system_wifi_networks` çiftleri (§3.4) |
| coremusic_social.sql | coremusic_social | 9 | BINARY(16) | **13 cross-DB FK** · `user_achievements.achievement_name` inline — achievements master yok, FK yok `[PROJECT EVIDENCE]` |
| coremusic_wireless.sql | coremusic_wireless | 5 | BINARY(16) | `system_wifi_networks`/`system_bluetooth_devices` ile ad-düzlem çakışması (§3.4) |
| coremusic_ai.sql | coremusic_ai | 6 | BINARY(16) | `audio_features` ↔ musics.`music_audio_features` tekrarı (§3.4) |
| coremusic_api.sql | coremusic_api | 4 | BIGINT | **`api_keys.scope` VARCHAR(1024) virgülle ayrılmış liste (satır 29,37) — 1NF ihlali** + `allowed_ips` TEXT içinde JSON (satır 38) `[PROJECT EVIDENCE]` · drop migration'ı var (auth SSOT) |
| coremusic_cms.sql | coremusic_cms | 8 | BINARY(16) | `blog_post_tags` junction mevcut (iyi) |
| coremusic_download.sql | coremusic_download | 4 | BINARY(16) | Hiç `deleted_at` yok (karar #7 ile hizalanacak) |
| coremusic_neva.sql | coremusic_neva | 4 | BINARY(16) | `eq_presets` ↔ `system_eq_presets` tekrarı (§3.4) |
| coremusic_patch.sql | coremusic_patch | 3 | — | Hiç updated_at/deleted_at yok; `system_schema_versions`/`system_migration_log` ile işlev çakışması |
| coremusic_studio.sql | coremusic_studio | 6 | BINARY(16) | Header "is_deleted + deleted_at" iddiası ≠ disk (yalnız is_deleted) `[VERIFY REQUIRED]` |
| media_catalog.sql | media_catalog | 9 | CHAR(26) ULID | Türkçe kolon (`eklenme`/`guncelleme`), collation `utf8mb4_tr_0900_ai_ci` — bilinçli ayrım (header) · PLANNED, deploy edilmedi |
| novasearch.sql | novasearch (USE yok) | 7 | varchar(36) | knex migration tabloları dahil; `channels.keywords/available_tabs TEXT` formatı `[UNKNOWN]` (veri yok) · CREATE DATABASE/USE beyanı yok |

**Uygulama kullanım kanıtı** `[PROJECT EVIDENCE]` (repo taraması 2026-10-07): canlıda sorgulanan tablolar yalnız `auth` (users, user_roles, user_assigned_roles, user_tokens, api_keys), `musics` (musics, artists, music_files), `social` (oauth_connections, oauth_states). 13 DB'de uygulama kodu sorgusu yok. Playlist/album/genre tabloları sorgulanmıyor (UI sabit PNG — `RecentTracksComponent.php:29`). OAuth FK'lar `INT user_id` hedeflerken gerçek kullanıcılar `auth.users` BINARY(16) UUID — **tip uyumsuzluğu** `[PROJECT EVIDENCE]` (migrations vs `UserRepository.php:13-20`).

### §3.2 Fonksiyonel Bağımlılıklar (§35-D — önemli FD'ler) `[PROJECT EVIDENCE]`

```text
users.id → {email, username, is_active, ...}                 (auth.users, candidate key: email, username)
musics.id → {title, duration_sec, artist_id, ...}            (musics.artist_id → artists.id : N:1 bugün)
music_files.music_id → {file_path, file_format, is_primary}  (2NF sorunu yok; composite key yok)
api_keys.id → {key_hash, scopes(JSON), key_type, ...}        (key_hash da candidate key — unique)
user_roles.id → {role_name, permissions(JSON)}              (kontrollü JSON — karar #9)
playlist_tracks: (playlist_id, track_id) → position          (çoktan-çoga, iyi formda)
radio_now_playing.station_id → {track_title, artist_name}    (snapshot FD — kasıtlı denorm, ama music_id yok → iz sürme kırık)
credential_audit: (credential_id, action, occurred_at) → kalan (comment.created_at ≠ kolon.occurred_at)
```

### §3.3 Normalizasyon Bulguları (§35-C)

| Form | Bulgu | Kanıt |
|------|-------|-------|
| **1NF** | **İhlal (1 kolon):** `coremusic_api.api_keys.scope` virgülle ayrılmış liste · TEXT-içinde-JSON: `api_keys.allowed_ips` · `[UNKNOWN]` novasearch `channels.keywords/available_tabs` (verisiz) | dosya:satır yukarıda |
| **2NF** | **İhlal yok tespit** — composite PK'lı tablolar (playlist_tracks, album_discs vb.) kısmi bağımlılık göstermiyor (üretilen DDL okuması) | 92 FK/172 PK taraması |
| **3NF** | **İhlal (gözlemsel):** `radio_now_playing` (transitive/snapshot — kasıtlı ama `music_id` eksik iz), `user_achievements.achievement_name` (achievement master'a bağlı değil), `audit comment ≠ kolon` (`credential_audit`) | §3.1 |
| **BCNF** | Bildirim/iddia: dump yorumları her tablo için "BCNF:" notu taşıyor; determinants büyük ölçüde key. **3 şüpheli** (yukarıdakiler). Kör uygulama yok — prompt §7 gereği gereksiz karmaşıklık üretilmez | dump comment'leri + FD §3.2 |
| **4NF** | **İhlal (iş kararı #3 ile teyitli):** `musics.artist_id` tekil — sanatçı↔şarkı N:M bağımsız multivalued ilişki tek kolona sığdırılmış; hedef: `music_artists` junction | kullanıcı kararı #3 |
| **5NF** | **Gerekli değil** — join dependency kanıtı yok; uydurma bridge table üretilmez (prompt §7.5) | [ARCHITECTURAL INFERENCE] |

### §3.4 Çift / Tekrar Tablolar (karar #8 — tek tek kanıt)

| Çift | Kanıt | Öneri (faz sonunda ayrıca onay) |
|------|-------|-------------------------------|
| `api_keys` auth ↔ api | auth: JSON scopes + canlı SSOT (drop migration'ı var) · api: CSV scope 1NF ihlali | **Birleştir (auth kazanır)** — kanıt güçlü `[PROJECT EVIDENCE]` |
| `genres`(musics) ↔ `catalog_genres`(catalog) | catalog = lookup DB olarak tasarlanmış | **catalog SSOT'a taşı** — domain lookup karar #5 ile uyumlu |
| `eq_presets`(neva) ↔ `system_eq_presets`(system) | neva = DSP domaini, system = genel ayar | **Ayrık tutulabilir** — farklı domain sahibi; tekrar değil, kapsam farkı `[ARCHITECTURAL INFERENCE]` |
| `wifi_networks`(wireless) ↔ `system_wifi_networks`(system) | ikisi de kullanıcı ağı verisi | **Birleştir** (wireless kazanır) — alan çakışması yüksek |
| `audio_features`(ai) ↔ `music_audio_features`(musics) | ikisi de parmak izi verisi | **Birleştir** (musics kazanır; ai FK ile okur) |
| `schema_versions`/`migration_log`(patch) ↔ `system_*`(system) | patch DB'nin varlık sebebiyle aynı | **patch SSOT** — system kopyaları düşer (ayrı onay) |
| `playlists`/`search_logs` coremusic ↔ novasearch | novasearch = knex-yönetimli canlı, kendi DB'inde | **Ayrık tut** — veri kökeni farklı (karar #11: yalnız taşıma önerisi) |

### §3.5 İlişki Analizi (§35-E)

| Tip | Örnek | Not |
|-----|-------|-----|
| 1:N | users → user_tokens, users → api_keys, artists → musics | İyi formda, FK + index mevcut |
| N:M (mevcut) | playlist↔track (playlist_tracks), post↔tag (blog_post_tags), music↔genre (music_genres) | Junction + UNIQUE doğru |
| N:M (eksik) | **music↔artist** — bugün tekil `artist_id` | karar #3 → `music_artists` |
| Opsiyonel | oauth_connections ↔ users | Tip uyuşmazlığı (INT vs BINARY(16)) — Düzeltme gerekli |
| Snapshot | radio_now_playing ↔ musics | `music_id` eklenmeli (kaynak korunur, başlıklar JOIN ile doğrulanabilir) |

## §4 Kurallar

### ZORUNLU: ŞABLON ÖNCE (Template-First)

**Herhangi bir dosyayı yazmadan ÖNCE `.ai/.templates/` dizinindeki ilgili şablonları oku:**

1. Uygun şablonu `[[.templates/index]]` (`.ai/.templates/index.md`) §7.1 tablolarından seç.
2. Şablonu oku; `{{VARIABLE}}` alanlarını doldur, gereksiz bölümleri kaldır.
3. **Şablon varsa ona göre yaz.**
4. **Şablon yoksa** standart 8-bölüm formatına göre yaz ve `⚠️ VERIFICATION REQUIRED` notuyla `log.md`'ye şablon eksiğini bildir.
5. Şablon okunmadan yazılan dosya **geçersizdir** (Guardrail #16): revert edilir, `log.md`'ye ERROR girilir.

| Durum | Aksiyon |
|-------|---------|
| `.ai/.templates/` altında ilgili şablon VAR | Şablonu oku → ona göre yaz |
| Şablon YOK | Standart formata göre yaz + `log.md`'ye "şablon eksiği" kaydı |
| Şablon okundu ama çelişiyor | DUR → `[[../../CLAUDE.md]]` §2.1 SSOT öncelik sırası |
| Vault erişilemiyor | `⚠️ VERIFICATION REQUIRED` → yazım durdurulur, kullanıcıya sor |

### §4.1 Bağlayıcı Normalizasyon Kuralları (bu analiz için)

1. **Veri kaybı = YASAK** (prompt §8): DROP/TRUNCATE/DELETE hiçbir öneri içinde kısayol olarak kullanılmaz; her yıkıcı işlem `[HUMAN APPROVAL REQUIRED]` taşır.
2. **Taban seviye = 3NF**, BCNF/4NF/5NF yalnız kanıtla (prompt §40).
3. **Denormalizasyon** yalnız: dondurulmuş snapshot (audit/log), ölçülmüş join maliyeti veya ADR gerekçesiyle (skill §3.5).
4. **Etiket zorunlu:** her iddia `[PROJECT EVIDENCE]`/`[EXTERNAL VERIFIED]`/`[ARCHITECTURAL INFERENCE]`/`[UNKNOWN]` taşır (prompt §34).
5. **Keşif sorusu yasağı:** `.ai`, `.sql`, repo, migration, test veya doğrulanmış web kaynağıyla cevaplanabilen şey sorulmaz; soru yalnız gerçek iş belirsizliğinde, **tane tane** (prompt §39).

| ✅ Yasak | ✅ Doğru |
|----------|----------|
| Veri kaybına yol açan normalization | EXPAND → MIGRATE → VALIDATE → CONTRACT |
| MusicBrainz'i kör kopyalama | Çekirdek kararlar CoreMusic gereksiniminden |
| Kanıtsız performans iddiası | `EXPLAIN`/ölçüm olmadan "hızlandı" denmez |
| Çift taraflı JSON (api_keys iki şema) | Tek şema, şema comment'i ile |

### §4.2 Onaylı Kararlar (keşifte alındı — bağlayıcı, 11 madde)

| # | Karar |
|---|-------|
| 1 | Kapsam = tüm 20 dump (172 tablo) |
| 2 | Çıktı = analiz + sorular; migration sonraki faz |
| 3 | **Çoklu sanatçı N:M** → `music_artists` junction; mevcut `artist_id` 1. satır (primary) olarak taşınır |
| 4 | **Cross-DB FK korunur** (28 adet); ADR-040 revize edilecek |
| 5 | **ENUM karma:** sabit teknik durum ENUM kalır; domain verisi lookup tablosu |
| 6 | **PK standardı = BINARY(16) UUID-v7**; BIGINT şablonu revize |
| 7 | **Timestamp:** varlık = created+updated+deleted · append-only log/fakt = yalnız created_at |
| 8 | Çift tablolar tek tek kanıtla (§3.4) |
| 9 | **Yetki JSON korunur** (`user_roles.permissions`, `api_keys.scopes`) — kontrollü JSON |
| 10 | **Müzik modeli = tam MusicBrainz hizası** (Recording, Track, Medium, Release, Release Group, Work, Artist Credit) |
| 11 | novasearch/media_catalog: analiz + taşıma önerisi; knex/Türkolon bu fazda korunur |
| 12 | **Hedef şema K katmanlarına göre kurgulanır** (K0-K20 / K000-K499 — `.ai/CLAUDE.md` §5); DB'ler birbirinden rastgele değil, katman sorumluluğuyla eşlenir |
| 13 | **OS-mimarisi:** veritabanı tasarımı işletim sistemi gibi — Çekirdek (identity/config/schema) + Sürücü (donanım/medya/DSP ağabacı) + Servis (sosyal/AI/API/indirme) + Gözlem (log); mimari seviyede yeni karar, hedef modelin üst iskeleti |

### §4.3 Vault Çelişki Envanteri (§35 devamı — revizyon ÖNERİLERİ, bu fazda dosya değiştirilmez)

| # | Çelişki (kaynak A ↔ kaynak B) | Etki | Öneri |
|---|---|---|---|
| C1 | ADR-040 + db-engine-notes §5 "cross-DB FK yasak" ↔ diskte **28** cross-DB FK | İhlal görüntüsü; oysa MySQL 9 aynı sunucuda destekler `[EXTERNAL VERIFIED]` | **Karar #4:** ADR-040 revize — cross-DB FK'ye izin, koşul: aynı MySQL instance + tip eşitliği |
| C2 | db-engine-notes §3 "PK = BIGINT AUTO_INCREMENT" ↔ diskte hakim BINARY(16) (253 kullanım) | Yeni tablo üretiminde ikilik | **Karar #6:** §3 şablonu BINARY(16) UUID-v7'ye revize |
| C3 | db-engine-notes §5 "ENUM → lookup" ↔ diskte 108 ENUM | Kurallı okuma "hepsi ihlal" der | **Karar #5:** §5 kapsamı daraltılır (teknik ENUM hariç) |
| C4 | `coremusic_api.api_keys.scope` CSV ↔ `coremusic_auth.api_keys.scopes` JSON | Aynı kavram iki temsil | auth kazanır (drop migration'ı mevcut); api dosyası hedefte kaldırılır |
| C5 | OAuth migration `INT user_id` FK ↔ gerçek kullanıcılar `BINARY(16)` (auth) | FK doğrulanamaz / yanlış hedef DB | Hedef modelde tip + hedef DB hizalanır (ayrı onay) |
| C5+ | **Sistemik tip uyumsuzluğu (2. tarama 2026-10-07):** `INT UNSIGNED user_id` = **20 kolon / 9 dosya** (api 2, download 3, musics 3 — 3'ü comment'te "cross-DB FK → auth.users" diyor, neva 4, ai 3, studio 3, system 1) + `coremusic_ai.music_id INT UNSIGNED` ×3 (satır 58/83/111) ↔ gerçek UUID `BINARY(16)` | Bu kolonlara FK **hiç eklenemez** (tip eşleşmiyor); yorumlar yanlış hedefi tarif ediyor | Hedef fazda tek tek `BINARY(16)`'ya dönüştür; eklenmeden önce FK teyidi `[HUMAN APPROVAL REQUIRED]` |
| C6 | `credential_audit` comment `created_at` ↔ kolon `occurred_at` | BCNF notu yanlış kolonu adlandırır | Comment düzeltmesi (düşük riskli, doc-level) |
| C7 | studio dump header "deleted_at var" ↔ diskte yok | Envander yanıltıcı | Header düzeltmesi veya kolon ekleme (karar #7 ile) |

### §4.4 Hedef Model Taslağı (§35-F — yalnız onaylı değişiklikler)

```text
coremusic_musics (hedef çekirdek, MusicBrainz hizalı — karar #10)
├── work                      (eser/söz-müzik kimliği; Work)
├── recording                 (özgün ses kaydı; ISRC + süre burada)
│     ├── music_artists       (N:M + artist_credit_id + rol + position — karar #3)
│     ├── recording_genres    (N:M)
│     └── music_files         (fiziksel dosya: codec, sample_rate, channels,
│                              bitrate, duration_ms, checksum, storage_uri —
│                              [EXTERNAL VERIFIED] teknik kolonlar varlık seviyesinde)
├── track                     (recording'in mediumdaki tekrarı: position + title override)
├── medium                    (release içindeki disk: format, position)
├── release                   (satılabilir/yayın baskısı: catalog_number, UPC/EAN, label)
│     └── release_labels      (N:M)
├── release_group             (album/single/EP soyut grubu)
└── artist_credit             (birleştirilmiş görüntü: "Queen & David Bowie")
```

| Mevcut | Hedef | Koruma kuralı |
|--------|-------|---------------|
| `musics` | `recording` + `track` ayrımı | Her mevcut satır → 1 recording + 1 track; ID izi `legacy_music_id` ile korunur |
| `musics.artist_id` | `music_artists` | Mevcut değer → position=1, role='primary' satır |
| `albums` (albums DB) | `release_group` + `release` + `medium` | Mevcut satır → release_group; ilk baskıyı 1 release + 1 medium |
| `genres` (musics) | `catalog_genres` SSOT + `recording_genres` | Karar #5 + §3.4 |
| `music_files` | aynen + teknik kolon teyidi | Karar #11; dosya silinmez |
| yok | `work`, `artist_credit` | Sıfırdan; mevcut veriden türetme kuralı migration fazında tanımlanır |
| `radio_now_playing` | + `music_id` FK (snapshot kolonları kalır) | Snapshot = meşru denorm (skill §3.5), ama izlenebilirlik eklenir |

### §4.4a Hedef Şemanın OS Katmanı İskeleti (karar #12 + #13 — müzik domaini #10 ile iç içe)

```text
┌ ÇEKİRDEK (kernel) ─────────────────────────────────────────┐
│ coremusic_auth    → kimlik, rol, token, credential (tohum)  │
│ coremusic_system  → ayar, i18n, bildirim, uçnokta (syscall)│
│ coremusic_patch   → şema sürümü + migration (boot kayıt)    │
│ coremusic_catalog → domain lookup'lar (syscall tablosu)     │
├ SÜRÜCÜ (drivers) ──────────────────────────────────────────┤
│ coremusic_media   → cihaz/senkron sürücüsü (K2)             │
│ coremusic_wireless→ WiFi/BT sürücüsü (K2/K14)              │
│ coremusic_neva    → DSP/EQ sürücüsü (K3 ses çekirdeği)      │
│ coremusic_studio  → stüdyo I/O sürücüsü                     │
├ VERİ + SES ÇEKİRDEĞİ (K3/K5/K15) ──────────────────────────┤
│ coremusic_musics  → recording/track/work/artist_credit (§4.4)│
│ coremusic_albums  → release_group/release/medium/labels      │
│ coremusic_playlist→ playlist_tracks (çalışma zamanı kuyruğu) │
├ SERVİS (userland services — K8) ───────────────────────────┤
│ coremusic_social · coremusic_ai · coremusic_api · download  │
├ GÖZLEM (K12) ─────────────────────────────────────────────┤
│ coremusic_logs    → audit/metrics/event (append-only)       │
├ YAN SİSTEMLER (çekirdek dışı, karar #11) ──────────────────┤
│ novasearch (knex, canlı) · media_catalog (PLANNED)          │
└────────────────────────────────────────────────────────────┘
```

| OS bileşeni | Karşılık | K katmanı (`.ai/CLAUDE.md` §5) |
|---|---|---|
| Çekirdek | auth, system, patch, catalog | K0 (OS) + K6 (güvenlik) + K5 (veri otoritesi) |
| Sürücü | media, wireless, neva, studio | K2 (sürücü) + K3 (ses motoru) |
| Bellek/çalışma zamanı | musics, albums, playlist | K5 (veri) + K3/K15 (medya) |
| Servisler | social, ai, api, download | K8 (servis) + K9 (API) |
| Gözlem | logs | K12 (izleme) |

**Kural:** yeni tablo/tablo ailesi üretirken önce bu iskeletteki katmana yerleşir; katmanlar arası FK **çekirdek→içe doğru** (ör. servis → auth.users cross-DB FK, karar #4 ile izinli), çekirdek servisleri asla servis tablolarına FK ile bağlanaz (K5.1 Layer Dependency mantığı). Müzik domaini ayrıntısı §4.4 (MusicBrainz, karar #10) bu iskeletin **veri+ses çekirdeği** içindedir — ikisi çakışmaz.

### §4.5 Kolon-Kolon Karar Tablosu (§35-G — değişen kolonlar)

| Mevcut | Önerilen | Gerekçe | Norm. gerekçe | Veri koruma | Migration |
|--------|----------|---------|---------------|-------------|-----------|
| `musics.artist_id` (tekil) | `music_artists(artist_id, recording_id, credit_id, role, position)` | N:M kararı #3 | 4NF | 1:1 ilk satıra yazılır | EXPAND (yeni tablo) → MIGRATE → CONTRACT (eski kolon FK korunur, sonra değerlendirilir) |
| `coremusic_api.scope` CSV | (dosya düşer; auth JSON SSOT) | 1NF ihlali + C4 | 1NF | auth'ta JSON zaten var | CONTRACT (drop migration sonrası) |
| `radio_now_playing.track_title/artist_name` | korunur + `music_id` FK eklenir | snapshot + izlenebilirlik | 3NF/esnek | mevcut satırlar aynen kalır | EXPAND |
| `user_achievements.achievement_name` | `achievements` lookup + `achievement_id` FK; ad kolonu korunur (cache) | 3NF | 3NF | ad, lookup'a kopyalanır | EXPAND → MIGRATE |
| `oauth_*.user_id INT` | `BINARY(16)` + hedef `auth.users` | C5 tip uyumsuzluğu | referans bütünlüğü | hex↔binary dönüşümü doğrulanarak | EXPAND → VALIDATE → CONTRACT |
| `credential_audit` comment `created_at` | comment → `occurred_at` | C6 | doküman | yok (comment) | doc fix |
| `novasearch`/`media_catalog` kolon adları | bu fazda DEĞİŞMEZ | karar #11 | — | — | taşıma önerisi ayrı |

### §4.5a Disk Taraması Düzeltmeleri (iddia-iddia doğrulama, 2026-10-07 — "yeni mi, var mı?")

| İddia | Disk kanıtı | Karar |
|-------|-------------|-------|
| `isrc` yeni | **VAR** — `musics.isrc VARCHAR(20) NULL` (`coremusic_musics.sql:98`) | Yeni değil; `recording`'e **taşınır** |
| `medium` yeni | **KISMEN VAR** — `albums.total_discs` (`albums.sql:33`) + `album_discs(album_id, disc_number, disc_title)` (`albums.sql:62-75`) = baskı/disk kavramı mevcut | `album_discs` → `medium`'a **yeniden adlandırılır/taşınır** (sıfırdan değil) |
| `release_labels` yeni | **KISMEN VAR** — `albums.record_label VARCHAR(200)` inline (`albums.sql:30`) | 3NF: inline kolon → `labels` lookup + `release_labels` junction; veri kopyalanır |
| `music_artists` yeni | **YOK** — `music_genres`, `playlist_tracks`, `artist_members`, `album_credits` junction'ları var ama music↔artist N:M **yok**; `musics.artist_id` tekil (`musics.sql:91`) | Sıfırdan junction + backfill (karar #3) |
| `artist_credit` / `work` / `recording` / `track` / `release` / `release_group` | **YOK** — `CREATE TABLE` taramasında 0 isabet; görevleri bugün `musics` + `albums` taşıyor | Sıfırdan (karar #10), `musics`/`albums` backfill'i ile |
| `achievements` lookup | **YOK** — `user_achievements.achievement_type + achievement_name` inline (`social.sql:218-219`) | Lookup sıfırdan; ad verisi oradan kopyalanır |
| `radio_now_playing.music_id` | **YOK** — tabloda yalnız `station_id` + `track_title/artist_name` snapshot (`musics.sql:732-749`) | Kolon ekleme (EXPAND) |
| `legacy_music_id` | **YOK** | Yeni tabloya eklenir (backfill izi) |

### §4.6 Müzik Alan Doğrulaması (§35-H) `[EXTERNAL VERIFIED]`

MusicBrainz şema **v31 (Q2 2026)** resmi dokümanı: `https://musicbrainz.org/doc/MusicBrainz_Database/Schema` — Recording (özgün karışım; ISRC listesi + süre), Track (yalnız release bağlamında; recording + pozisyon), Medium (release içindeki parça; format/pozisyon), Release (gerçek ürün; tarih, katalog no, etiket), Release Group (soyut album/single/EP grubu), Artist Credit (ad varyantları + birleştirme metni), Work (eser) ayrı varlıklardır. **Referans model, CoreMusic şeması değil** (karar #10 ile bilinçli hizalandı). ISRC ISO 3901: 12 karakter sıkışık / 15 tireli; recording seviyesinde `[EXTERNAL VERIFIED]`. Teknik ses kolonları (codec, sample_rate, channels, duration_ms, checksum) dosya/varlık seviyesinde tutulur — `music_files` ile uyumlu `[EXTERNAL VERIFIED]`. MySQL 9.x: FK kolonlarında index zorunlu (çocukta otomatik oluşur; ebeveynde unique key önce tanımlanır) + geniş tabloların ID-FK ile bölünmesi performans için önerilen `[EXTERNAL VERIFIED: dev.mysql.com/refman/9.7/en/innodb-best-plans + create-table-foreign-keys]`.

**CoreMusic'e uyarlamada domainsal not:** CoreMusic'in dağıtımı/fiziksel baskısı yok; `release`/`medium` varlıkları **kendi katalog beyanı** olarak modellenir (kullanıcı dış satım takibi istemediğini belirtti — prompt §11 uyarınca semantik: "baskı = katalogdaki albüm kaydı", UPP/EAN nullable).

### §4.7 Güvenlik Veritabanı İncelemesi (§35-I) `[PROJECT EVIDENCE]`

| Alan | Mevcut durum | Değerlendirme |
|------|--------------|---------------|
| Kimlik | `auth.users` BINARY(16) UUID-v7, soft-delete, unique email/username | ✅ İyi |
| Oturum | DB'de session tablosu YOK — PHP dosya session'ları (`SessionConfig.php:33`) | ⚠️ [UNKNOWN] ölçek hedefi; karar gerekirse ayrı onay |
| Token | `user_tokens`: hash-only (`jti` sha256), `token_type` ENUM (sabit teknik — karar #5 ile kalır), refresh hiç yazılmıyor `[UNKNOWN]` | ✅ hash-only standartla uyumlu `[EXTERNAL VERIFIED]` |
| API key | `key_hash` unique + `key_prefix` + PHP-side `hash_equals` loop; ama her başarılı doğrulamada `last_used_at` UPDATE (yazma yükü) | ✅ sızıntı direnci · ⚠️ yazma maliyeti — index kanıtı §4.8 |
| RBAC | `user_roles` + `user_assigned_roles` junction ✅; `permissions` JSON (karar #9) | ✅ junction doğru; JSON kontrollü |
| Yetki denetimi | `permission_audit`, `credential_audit` append-only | ✅ |
| Çift api_keys | drop migration'ı mevcut | ✅ tekilleşiyor |
| Audit timestamps | `occurred_at`/`created_at` politikası karar #7 ile hizalanacak | ⚠️ C6/C7 |

### §4.8 Index Stratejisi (§35-J)

| Mevcut | Problem | Önerilen | Sorgu gerekçesi | Takas |
|--------|---------|----------|-----------------|-------|
| `api_keys.key_hash` UNIQUE | ✅ | korunur | `ApiKeyRepository:161-175` prefix+hash doğrulama | — |
| `api_keys.last_used_at` UPDATE / sorgu yok | write amplifikasyon | `idx_apikeys_lastused` yok — sorgu kanıtı yok `[VERIFY REQUIRED]` | — | ek index yazma yavaşlatır; ölçmeden eklenmez |
| FK indexleri | MySQL child'ta ototomatik `[EXTERNAL VERIFIED]` | 28 cross-DB FK'da ebeveyn unique key teyidi migration öncesi zorunlu | FK check | — |
| `music_artists` (yeni) | — | `PK(recording_id, artist_id)` + `idx_artist(artist_id)` + UNIQUE(recording_id, position) | N:M JOIN + sıralı listeleme | 2 index yazma |
| `recording.isrc` (yeni) | — | `idx_isrc` (UNIQUE değil — çoklu ISRC kararı migration'da) `[VERIFY REQUIRED]` | harici eşleştirme | — |
| Genel | 172 tabloda sorgu kanıtı yalnız 6 tabloda | **Kalan index önerileri `EXPLAIN` olmadan üretilmez** (prompt §26/§27) | — | over-indexing |

## §5 Workflow

### §5.1 Analiz Süreci (bu raporun izlediği — tekrarlanabilir)

| Adım | Aksiyon | Çıktı | Kanıt |
|------|---------|-------|-------|
| 1 | Şablon seç + oku | `docs-md-template.md` | Guardrail #16 ✅ |
| 2 | Şema envanteri (glob + grep) | 20 dosya / 172 tablo / 92 FK / 28 cross-DB / 108 ENUM | §3.1 |
| 3 | Uygulama sorgu kanıtı (repo) | canlı tablo listesi + FD'ler | §3.1/§3.2 |
| 4 | Web doğrulama (MusicBrainz v31, MySQL 9.x, RBAC, audio metadata) | `[EXTERNAL VERIFIED]` | §4.6, §4.7 |
| 5 | Keşif soruları (tane tane, 11 karar) | §4.2 | prompt §39 |
| 6 | Bulgular + hedef model taslağı | §3.3-§3.5, §4.4-§4.5 | — |
| 7 | Çelişki + revizyon önerileri | §4.3 | Guardrail #12 |
| 8 | Onay kapısı | §6.3 | prompt §36 |

### §5.2 Migration Planı Taslağı (§35-K — YÜRÜTMEK İÇİN AYRI ONAY)

```text
FAZ 0  ONAY: bu rapor + §4.3 vault revizyonları
FAZ 1  EXPAND (düşük risk): music_artists, achievements lookup, radio_now_playing.music_id,
       oauth tip hizası, credential_audit comment fix  → eski kolonlar durur
FAZ 2  MIGRATE: musics→recording/track backfill (legacy_music_id), artist→junction backfill
       (position=1, role=primary), albums→release_group/release/medium backfill,
       genres→catalog SSOT kopyası, achievement_name→lookup
FAZ 3  VALIDATE: §6.2 kontrolleri (satır sayısı, PK/FK bütünlüğü, orphan, semantic eşitlik)
FAZ 4  CONTRACT (ayrı onay): coremusic_api drop, eski tekil artist_id FK'nın junction'a devri,
       tekil kolonların kaldırılması — YALNIZ açık insan onayıyla
```

Her faz için `up.sql`/`down.sql` ayrı üretilir (`database-normalize-maker` §9 expand-contract); **rollback kanıtı olmadan "[ROLLBACK RISK]" yazılır** (prompt §33).

### §5.3 Riskler (§35-M)

| Sınıf | Risk | Olasılık/etki | Azaltma |
|-------|------|---------------|---------|
| DATA | recording/track/backfill sırasında yanlış eşleme | orta/yüksek | legacy_music_id izi + satır sayısı kontrolü; silme yok |
| MIGRATION | cross-DB FK (28) nedeniyle sıralama kırılması | orta/orta | import öncesi `FOREIGN_KEY_CHECKS=0` yalnız restore anında; ebeveyn unique key teyidi |
| SECURITY | `last_used_at` UPDATE sorgu yüzdesi | düşük/orta | ölç, gerekirse async |
| COMPATIBILITY | uygulama kodu `musics.artist_id` bekliyor (MusicRepository:86) | yüksek/yüksek | EXPAND fazında eski kolon+FK korunur; kod yalnız CONTRACT öncesi güncellenir |
| ARCHITECTURE | MusicBrainz hizası fazla varlık = bakım yükü (release/medium/work) | orta/orta | nullable/opsiyonel modelleme; iş akışı çıkmadan veri üretmez |
| OPERATIONAL | novasearch knex çakışması | düşük/düşük | karar #11: bu fazda dokunulmaz |
| DATA | studio dump header↔disk tutarsızlığı (C7) | düşük/düşük | teyit + header fix |

## §6 Doğrulama

### §6.1 Kontrol Listesi (§35 / prompt §41)

- [x] Actual .ai analyzed (CLAUDE.md, ADR-040, db-engine-notes — çelişkiler §4.3)
- [x] Actual .sql analyzed (20 dosya / 172 tablo envanter §3.1)
- [x] PK / FK / candidate key analizi (§3.1, §3.2 — 172/172 PK, 92 FK, 28 cross-DB)
- [x] 1NF/2NF/3NF/BCNF/4NF/5NF analizi (§3.3 — 5NF gerekmedi, gerekçeli)
- [x] Music domain dış kaynak doğrulaması (MusicBrainz v31 §4.6)
- [x] User/auth + session/token şeması (§4.7)
- [x] Medya + playlist/library şeması (§3.1 satırları + §3.5)
- [x] Identifier / NULL / data-type / unique / audit / soft-delete stratejileri (karar #5-#7, §4.5, §4.7)
- [x] Index stratejisi (§4.8 — kanıtsız index üretilmedi, `[VERIFY REQUIRED]` işaretli)
- [x] Migration + rollback taslağı (§5.2 — EXPAND/MIGRATE/VALIDATE/CONTRACT)
- [x] Veri kaybı önlenmesi (§4.1 kural 1; silme içeren tek satır bile yok)
- [x] Uygulama uyumluluğu (artist_id korunur — risk tablosu)
- [x] Dış araştırma proje kanıtından ayrıldı (etiketler §1)
- [x] Halüsinasyon şeması yok, yıkıcı migration onaysız yok
- [ ] **`[VERIFY REQUIRED]` kalanlar:** novasearch `channels.keywords` formatı (verisiz), studio header/`deleted_at`, `api_keys.last_used_at` sorgu kanıtı, refresh-token yazım yolu, ISRC unique politikası → bunlar migration fazında kapatılır

### §6.2 Migration Sonrası Doğrulama Planı (§35-L — yürütme anında)

- [ ] Satır sayısı eşitliği: her eski tablo → hedef tablo (backfill）
- [ ] PK eşsizliği + FK orphan yok (`LEFT JOIN … WHERE NULL` = 0)
- [ ] Semantic eşitlik: örneklem 100 kayıtta başlık/sanatçı/süre birebir
- [ ] Junction doğrulama: her musics kaydı ≥ 1 `music_artists` satırında
- [ ] Uygulama testleri: PHPUnit 410+ yeşil + `MusicRepository` JOIN sorgusu
- [ ] `EXPLAIN` karşılaştırması (iddia edilen hiçbir performans değişikliği ölçsüz yazılmaz)
- [ ] Rollback provası: down.sql staging'de bir kez çalıştırılır

### §6.3 FINAL STATUS

```text
APPROVAL REQUIRED — MIGRATION PLAN READY
```

(Tekrarlanabilir ölçüm komutu: `.ai/.sql/mysql/` içinde `CREATE TABLE` = 172, `REFERENCES \w+\.` = 28, ENUM = 108 — 2026-10-07.)

## §7 Referanslar

### 7.1 Wiki-link Referansları

| Hedef | İlişki |
|-------|--------|
| `[[CLAUDE]]` | `.sql` klasör bağlamı (20 dump envanteri) |
| `[[../CLAUDE.md]]` | Vault anayasası (Guardrail #2/#3/#12/#16) |
| `[[../.decisions/accepted/ADR-040-database-authority]]` | DB otoritesi — C1 revizyon hedefi |
| `[[../../AGENTS.md]]` | Agent routing (Data Engineer sorumluluğu) |
| `[[../.templates/index]]` | Şablon registry'si (bu raporun şablonu) |
| `[[../../log.md]]` | Audit trail (append-only) |
| Skill | `.claude/skills/database-normalize-maker/` + `references/db-engine-notes.md` |
| Harici | https://musicbrainz.org/doc/MusicBrainz_Database/Schema · https://dev.mysql.com/doc/refman/9.7/en/innodb-best-practices.html · https://dev.mysql.com/doc/refman/9.1/en/create-table-foreign-keys.html |

### 7.2 Değişiklik Geçmişi (append-only)

| Tarih | Versiyon | Değişiklik | Yazar |
|-------|----------|------------|-------|
| 2026-10-07 | 1.0.0 | İlk üretim — 20 dosya/172 tablo analizi, 11 onaylı karar, 7 çelişki, hedef model taslağı, APPROVAL REQUIRED | Bayram Ali / Vault Steward + Claude Code |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-07
**Mode:** Red Team · Human Mode · Truth Mode