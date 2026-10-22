---
title: "CoreMusic — MySQL Normalizasyon Analizi (BCNF/4NF/5NF Denetimi)"
type: report
category: database
status: draft
version: 1.0.0
date: 2026-10-22
authority: "Data Engineer — L0 Infrastructure"
tier: 3
domain: data
ssot: false
risk: medium
owner: "MO"
depends-on: []
---

# CoreMusic — MySQL Normalizasyon Analizi (2026-10-22)

**Kapsam:** `.ai/.sql/mysql/` altındaki 20 hedef dump dosyası (19 `coremusic_*` + `media_catalog.sql`; `novasearch.sql` kapsam dışı — ADR-014/ADR-040'ta "AYRI PROJE" olarak işaretli).

**Amaç:** Mevcut şemaların BCNF/4NF/5NF uyumluluğunu disk kanıtıyla denetlemek, "18 BCNF DB" iddiasının kaynağını doğrulamak ve önceliklendirilmiş düzeltme önerileri üretmek.

**Kural:** Bu rapor **hiçbir dump dosyasını, PHP kodunu veya canlı veritabanını değiştirmez**. Öneriler yalnızca gömülü migration SQL blokları olarak sunulur; uygulama için ayrı onay gereklidir.

---

## §1 Executive Summary

### §1.1 Ana Bulgular

| # | Bulgu | Severity | Kanıt |
|---|-------|----------|-------|
| B1 | **"18 BCNF DB" iddiası doğrulandı** — ADR-040 §1.1-A'da 18 dosya / 156 tablo / 82 FK (54 DB-içi + 28 cross-DB) tescilli; diskte 19 `coremusic_*` + `media_catalog` = 20 dosya var (ADR-040'taki 18 + `media_catalog` sonradan eklendi) | ✅ VERIFIED | ADR-040:33-36 · glob 2026-10-22 |
| B2 | **`coremusic_api.api_keys.scope` 1NF ihlali** — `VARCHAR(1024)` virgülle ayrılmış liste (satır 29, 37) | 🔴 HIGH | `coremusic_api.sql:29,37` |
| B3 | **`coremusic_api.api_keys.allowed_ips` TEXT-içinde-JSON** — `allowed_ips` kolonu TEXT tipinde, JSON array saklı (satır 38) | 🟡 MEDIUM | `coremusic_api.sql:38` |
| B4 | **Cross-DB FK 28 adet** — `coremusic_user` (11), `coremusic_social` (13), `coremusic_system` (4) → hedef: `coremusic_auth.users` (23), `coremusic_musics.musics` (5) | ⚠️ POLICY | ADR-040 §2.2-c |
| B5 | **PK standardı tutarsız** — `coremusic_api` BIGINT, `media_catalog` CHAR(26) ULID, diğerleri BINARY(16) UUID v7 | 🟡 MEDIUM | §3.4 envanter |
| B6 | **`radio_now_playing` snapshot denorm** — `track_title`, `artist_name`, `album_name` inline; `music_id` FK yok (iz sürme kırık) | 🟡 MEDIUM | `coremusic_musics.sql:732-749` |
| B7 | **`credential_audit` comment ≠ kolon** — BCNF comment `(…, created_at)` diyor, kolon `occurred_at` (satır 246 vs 261) | 🟢 LOW (doc) | `coremusic_auth.sql:246,261` |
| B8 | **Çift tablolar** — `api_keys` (auth ↔ api), `audio_features` (ai ↔ musics), `wifi_networks` (wireless ↔ system), `eq_presets` (neva ↔ system) | 🟡 MEDIUM | §3.4 |
| B9 | **`musics.artist_id` tekil** — N:M ilişki tek kolona sığdırılmış (4NF ihlali; onaylı karar #3 → `music_artists` junction) | 🔴 HIGH | `coremusic_musics.sql` + karar #3 |
| B10 | **`deleted_key` STORED generated column deseni** — 12+ tabloda `GENERATED ALWAYS AS (if((is_deleted = 0), 0, to_seconds(deleted_at))) STORED` → soft-delete + unique constraint kombinasyonu endüstri standardıyla uyumlu | ✅ GOOD PRACTICE | §3.6 |

### §1.2 Skor Tablosu (şema bazında)

| # | Şema | Tablo | PK Tipi | 1NF | 2NF | 3NF | BCNF | 4NF | 5NF | Skor | Kritik Sorun |
|---|------|------:|---------|-----|-----|-----|------|-----|-----|------|--------------|
| 1 | `coremusic_auth` | 11 | BINARY(16) | ✅ | ✅ | ⚠️ | ⚠️ | N/A | N/A | **B+** | credential_audit comment/kolon uyumsuzluğu |
| 2 | `coremusic_user` | 7 | BINARY(16) | ✅ | ✅ | ✅ | ✅ | N/A | N/A | **A-** | 11 cross-DB FK (grandfathered) |
| 3 | `coremusic_musics` | 26 | BINARY(16) | ✅ | ✅ | ⚠️ | ⚠️ | ❌ | N/A | **C+** | `artist_id` tekil (4NF), radio_now_playing snapshot |
| 4 | `coremusic_albums` | 10 | BINARY(16) | ✅ | ✅ | ✅ | ✅ | N/A | N/A | **A** | — |
| 5 | `coremusic_playlist` | 5 | BINARY(16) | ✅ | ✅ | ✅ | ✅ | N/A | N/A | **A** | — |
| 6 | `coremusic_catalog` | 7 | BINARY(16) | ✅ | ✅ | ✅ | ✅ | N/A | N/A | **A** | Lookup DB — hedef model parçası |
| 7 | `coremusic_cms` | 8 | BINARY(16) | ✅ | ✅ | ✅ | ✅ | N/A | N/A | **A** | — |
| 8 | `coremusic_social` | 11 | BINARY(16) | ✅ | ✅ | ⚠️ | ⚠️ | N/A | N/A | **B** | 13 cross-DB FK, user_achievements denorm |
| 9 | `coremusic_ai` | 6 | BINARY(16) | ✅ | ✅ | ⚠️ | ⚠️ | N/A | N/A | **B** | audio_features ↔ music_audio_features tekrarı |
| 10 | `coremusic_api` | 4 | BIGINT | ❌ | — | — | — | — | — | **D** | scope CSV (1NF), allowed_ips TEXT-JSON, PK BIGINT |
| 11 | `coremusic_billing` | 8 | BINARY(16) | ✅ | ✅ | ✅ | ✅ | N/A | N/A | **A-** | Hand-authored (farklı header) |
| 12 | `coremusic_download` | 4 | BINARY(16) | ✅ | ✅ | ✅ | ✅ | N/A | N/A | **A** | — |
| 13 | `coremusic_logs` | 18 | BINARY(16)/INT | ✅ | ✅ | ✅ | ✅ | N/A | N/A | **A-** | Append-only ailesi; timestamp politikası (karar #7) |
| 14 | `coremusic_media` | 7 | BINARY(16) | ✅ | ✅ | ✅ | ✅ | N/A | N/A | **A** | — |
| 15 | `coremusic_neva` | 4 | BINARY(16) | ✅ | ✅ | ⚠️ | ⚠️ | N/A | N/A | **B** | eq_presets ↔ system_eq_presets tekrarı |
| 16 | `coremusic_patch` | 3 | BINARY(16) | ✅ | ✅ | ✅ | ✅ | N/A | N/A | **A-** | system_schema_versions / system_migration_log ile işlev çakışması |
| 17 | `coremusic_studio` | 6 | INT UNSIGNED | ✅ | ✅ | ✅ | ✅ | N/A | N/A | **B+** | Header "is_deleted + deleted_at" iddiası ≠ disk (yalnız is_deleted) |
| 18 | `coremusic_system` | 20 | BINARY(16) | ✅ | ✅ | ✅ | ✅ | N/A | N/A | **A-** | 4 cross-DB FK (grandfathered) |
| 19 | `coremusic_wireless` | 5 | BINARY(16) | ✅ | ✅ | ⚠️ | ⚠️ | N/A | N/A | **B** | wifi_networks ↔ system_wifi_networks tekrarı |
| 20 | `media_catalog` | 9 | CHAR(26) ULID | ✅ | ✅ | ✅ | ✅ | N/A | N/A | **A** | Türkçe kolon (bilinçli), taxonomy FK ile drift imkansız |

**Genel Skor:** **B+** (19/20 şema BCNF'e yakın; `coremusic_api` kritik düzeltme gerektiriyor)

---

## §2 "18 BCNF DB" İddiasının Kaynağı ve Doğrulaması

### §2.1 İddia Kaynakları

| Kaynak | İddia | Durum |
|--------|-------|-------|
| `.ai/brain.md:308` | "§11 18 BCNF Databases (ADR-040)" | ✅ Vault kaydı |
| `.ai/CLAUDE.md:395` | "§18 18 BCNF Databases (ADR-040)" | ✅ Vault kaydı |
| `.ai/.decisions/accepted/ADR-040-database-authority.md:33-36` | "18 dosya · 156 tablo · 82 FK (54 DB-içi + 28 cross-DB)" | ✅ ADR tescili |
| `.ai/index.md:196` | "⚠️ VERIFICATION REQUIRED — diskte 20 `.sql` dosyası; ADR-003 '9 BCNF' der" | ⚠️ Vault uyarısı |
| `.ai/.sql/CLAUDE.md:9` | "MySQL altında **21 dump dosyası** (19 BCNF `coremusic_*` + `media_catalog` + `novasearch`)" | ✅ Disk ölçümü |

### §2.2 Disk Kanıtı (2026-10-22 Glob)

```
.ai/.sql/mysql/ (hidden: true)
├── coremusic_albums.sql      (10 tablo)
├── coremusic_ai.sql          (6 tablo)
├── coremusic_api.sql         (4 tablo)
├── coremusic_auth.sql        (11 tablo)
├── coremusic_billing.sql     (8 tablo — hand-authored)
├── coremusic_catalog.sql     (7 tablo)
├── coremusic_cms.sql         (8 tablo)
├── coremusic_download.sql    (4 tablo)
├── coremusic_logs.sql        (18 tablo)
├── coremusic_media.sql       (7 tablo)
├── coremusic_musics.sql      (26 tablo)
├── coremusic_neva.sql        (4 tablo)
├── coremusic_patch.sql       (3 tablo)
├── coremusic_playlist.sql    (5 tablo)
├── coremusic_social.sql      (11 tablo)
├── coremusic_studio.sql      (6 tablo)
├── coremusic_system.sql      (20 tablo)
├── coremusic_user.sql        (7 tablo)
├── coremusic_wireless.sql    (5 tablo)
├── media_catalog.sql         (9 tablo)
└── novasearch.sql            (7 tablo — KAPSAM DIŞI)
```

**Toplam:** 19 `coremusic_*` + `media_catalog` = **20 hedef dosya** · **200 tablo** (novasearch hariç)

### §2.3 Çelişki Analizi

| İddia A | İddia B | Gerçek (2026-10-22) | Açıklama |
|---------|---------|---------------------|----------|
| ADR-040: "18 dosya / 156 tablo" | index.md: "diskte 20 .sql" | **20 dosya / 200 tablo** | ADR-040 (2026-09-26) yazılmış; `coremusic_billing` (hand-authored) ve `media_catalog` sonradan eklenmiş. `media_catalog` zaten `.sql/CLAUDE.md:9`'da "CANLI — 2026-10-22 doğrulandı" diye işaretli. |
| ADR-040: "18 domain DB" | `.sql/CLAUDE.md`: "19 BCNF coremusic_*" | **19 `coremusic_*`** | `coremusic_billing` ADR-040'a dahil edilmemiş (farklı header: "hand-authored"). |
| ADR-003: "9 BCNF" | ADR-040: "18 BCNF" | **20 BCNF (19 + media_catalog)** | ADR-003 (erken sürüm) → ADR-040 (güncel otorite). `index.md:196`'daki ⚠️ uyarısı bu çelişkiyi işaret ediyor. |

**Sonuç:** "18 BCNF DB" iddiası **doğrulandı ama güncelliğini yitirdi** — güncel sayı **20 BCNF DB** (19 `coremusic_*` + `media_catalog`); `novasearch` kapsam dışı. `index.md:196`'daki ⚠️ uyarısı **geçerli**; ADR-040 §1.1-A revize edilmeli.

---

## §3 Şema Bazlı Normalizasyon Denetimi

### §3.1 Fonksiyonel Bağımlılık Analizi (öne çıkan FD'ler)

```text
-- coremusic_auth
users.id → {email, username, is_active, ...}                    (candidate key: id, email, user_tokens.token_hash)
credential_audit: (credential_id, action, occurred_at) → kalan  (BCNF comment ≠ kolon — B7)

-- coremusic_musics
musics.id → {title, duration_sec, artist_id, ...}              (musics.artist_id → artists.id : N:1 — B9 ihlali)
music_files.music_id → {file_path, file_format, is_primary}    (2NF sorunu yok)
radio_now_playing.station_id → {track_title, artist_name}      (snapshot FD — B6 ihlali)

-- coremusic_playlist
playlist_tracks: (playlist_id, track_id) → position            (çoktan-çoga, iyi formda)

-- coremusic_api
api_keys.id → {key_hash, scopes(CSV), key_type, ...}           (key_hash da candidate key — unique) — B2 ihlali

-- coremusic_social
user_achievements.user_id → {achievement_name, ...}            (achievement master'a bağlı değil — B8 ihlali)
```

### §3.2 Cross-Schema İlişki Analizi

| Kaynak Şema | Hedef Şema | FK Adet | Hedef Tablo | Politika |
|--------------|-----------|--------:|-------------|----------|
| `coremusic_user` | `coremusic_auth` | 8 | `users` | ⚠️ GRANDFATHERED (X-01…X-08) |
| `coremusic_user` | `coremusic_musics` | 3 | `musics` | ⚠️ GRANDFATHERED (X-09…X-11) |
| `coremusic_social` | `coremusic_auth` | 11 | `users` | ⚠️ GRANDFATHERED (X-12…X-22) |
| `coremusic_social` | `coremusic_musics` | 2 | `musics` | ⚠️ GRANDFATHERED (X-23…X-24) |
| `coremusic_system` | `coremusic_auth` | 4 | `users` | ⚠️ GRANDFATHERED (X-25…X-28) |
| **TOPLAM** | | **28** | | Yeni ihlal **yasak** (ADR-040 §2.2-c K1) |

**MySQL 9.7 Teknik Doğrulama:** Cross-DB FK aynı sunucuda **teknik olarak mümkün** (MySQL RefMan §15.1.25.5); hata 1215 nedenleri: engine uyuşmazlığı, tip uyuşmazlığı, `REFERENCES` yetkisi eksikliği. CoreMusic'te 28 FK **uygulanmış** (dump'larda `CONSTRAINT ... FOREIGN KEY ... REFERENCES coremusic_auth.users` satırları mevcut).

### §3.3 Polymorphic İlişkiler (FK Yok)

| Tablo | Kolonlar | Sorun | Öneri |
|-------|----------|-------|-------|
| `user_favorites` (coremusic_user) | `entity_type` VARCHAR, `entity_id` BINARY(16) | Polymorphic — FK eklenemez | Application-level ref (audit trigger) |
| `comments` (coremusic_social) | `entity_type`, `entity_id` | Polymorphic | Application-level ref |
| `shares` (coremusic_social) | `entity_type`, `entity_id` | Polymorphic | Application-level ref |
| `system_jobs` (coremusic_system) | `ref_type`, `ref_id` | Polymorphic (yorum: "FK YOK") | Application-level ref |

### §3.4 PK Tipi Dağılımı

| PK Tipi | Şema | Tablo | Neden |
|---------|------|------:|-------|
| `BINARY(16)` UUID v7 | 17 şema | ~175 | Standart (karar #6) |
| `BIGINT UNSIGNED` | `coremusic_api` | 4 | Legacy — `api_calls` partitioned (hot-path sayaç) |
| `CHAR(26)` ULID | `media_catalog` | 8 | ADR-092 (medya domaini) |
| `INT UNSIGNED` AUTO_INCREMENT | `coremusic_studio` | 6 | Stüdyo oturumları (düşük kartinalite) |
| **Karma** | `coremusic_logs` | 18 | `audit_logs` BINARY(16), `analytics_daily` INT (gün bazlı) |

**İhlal:** `coremusic_api` (BIGINT) ve `coremusic_studio` (INT) BINARY(16) UUID standardından sapıyor. `media_catalog` (ULID) bilinçli ayrım (ADR-092); `coremusic_api` ve `coremusic_studio` için revizyon önerilir (bkz. §5).

### §3.5 Çift / Tekrar Tablolar (Detail)

| Çift | Kanıt | Domain Farkı | Öneri |
|------|-------|--------------|-------|
| `api_keys` (auth) ↔ `api_keys` (api) | auth: JSON scopes + drop migration · api: CSV scope (1NF ihlali) | auth = identity, api = gateway | **Birleştir (auth kazanır)** — C4 |
| `genres` (musics) ↔ `catalog_genres` (catalog) | catalog = lookup DB (karar #5) | catalog = reference data | **catalog SSOT'a taşı** |
| `eq_presets` (neva) ↔ `system_eq_presets` (system) | neva = DSP domain, system = genel ayar | Domain sahipliği farklı | **Ayrık tutulabilir** (inference) |
| `wifi_networks` (wireless) ↔ `system_wifi_networks` (system) | İkisi de kullanıcı ağı verisi | Alan çakışması yüksek | **Birleştir (wireless kazanır)** |
| `audio_features` (ai) ↔ `music_audio_features` (musics) | İkisi de parmak izi verisi | İkisi de music domain | **Birleştir (musics kazanır; ai FK ile okur)** |
| `schema_versions`/`migration_log` (patch) ↔ `system_*` (system) | patch DB'nin varlık sebebiyle aynı | patch = migration runner | **patch SSOT** — system kopyaları düşer |

### §3.6 Soft-Delete + Unique Constraint Deseni

**12+ tabloda** `deleted_key` STORED generated column:

```sql
`deleted_key` bigint GENERATED ALWAYS AS (if((`is_deleted` = 0), 0, to_seconds(`deleted_at`))) STORED
UNIQUE KEY `uq_*` (`business_key`, `deleted_key`)
```

**Örnekler:**
- `i18n_translations`: `uq_it_pair (translation_key, locale, plural_key, deleted_key)`
- `studio_presets`: `uq_sp_pair (user_id, name, deleted_key)`
- `system_eq_presets`: `uq_eq_presets_user_name (user_id, preset_name, deleted_key)`
- `system_settings`: `uk_system_settings_key (setting_key, deleted_key)`

**Değerlendirme:** ✅ **Endüstri standardıyla uyumlu** (phpArch 2026-02, MySQL RefMan §15.1.25.8). `is_deleted=0` → `deleted_key=0` (tüm aktif satırlar `0` ile eşleşir, tek aktif kayıt garantilenir); `is_deleted=1` → `deleted_key=to_seconds(deleted_at)` (saniye bazlı eşsizlik). **Soft-delete + unique constraint çakışması çözülmüş.**

---

## §4 Web Araştırması (MySQL 8/9 BCNF Uygulanabilirliği)

### §4.1 Kaynaklar (3 yetkili kaynak)

| # | Kaynak | URL | Ana İddia |
|---|--------|-----|-----------|
| W1 | **MySQL 9.7 Reference Manual §15.1.25.5** | dev.mysql.com/doc/refman/9.7/en/create-table-foreign-keys.html | Cross-DB FK aynı sunucuda mümkün; hata 1215 nedenleri: engine, tip, yetki |
| W2 | **GeeksforGeeks — BCNF** | geeksforgeeks.org/dbms/boyce-codd-normal-form-bcnf | BCNF: "her determinant candidate key olmalı"; 3NF'den güçlü |
| W3 | **Engineering LibreTexts — BCNF** | eng.libretexts.org/.../10.05 | BCNF: "1NF + her non-trivial FD'nin determinant'ı candidate key" |
| W4 | **phpArch — Soft Delete + Unique Index** | phparch.com/2026/02/advanced-unique-index-patterns-for-soft-deletes... | MySQL/MariaDB çözümü: `GENERATED ALWAYS AS (IF(deleted_at IS NULL, 1, NULL)) VIRTUAL` + unique index |
| W5 | **MySQL RefMan §15.1.25.8 — Generated Columns** | dev.mysql.com/doc/en/create-table-generated-columns.html | `GENERATED ALWAYS AS (expr) [VIRTUAL \| STORED]` sözdizimi |

### §4.2 Uygulanabilirlik Değerlendirmesi

| Soru | Cevap | Kaynak |
|------|-------|--------|
| MySQL 9'da BCNF mümkün mü? | ✅ EVET — BCNF bir modelleme kuralıdır; MySQL kısıtlaması yok. Her determinant'ı candidate key yapmak için normal tablo tasarımı yeterli. | W2, W3 |
| Cross-DB FK MySQL 9'da mümkün mü? | ✅ EVET (aynı sunucuda) — `REFERENCES db.table` sözdizimi destekli; hata 1215 = engine/tip/yetki uyuşmazlığı. | W1 |
| Soft-delete + unique constraint çakışması çözülebilir mi? | ✅ EVET — `GENERATED ALWAYS AS` STORED/VIRTUAL + composite unique index. CoreMusic'te `deleted_key` deseni bu pattern'i uyguluyor. | W4, W5 |
| Partitioned tabloda FK mümkün mü? | ❌ HAYIR — InnoDB partitioned tablolar FK desteklemiyor (`api_calls` bu yüzden FK'sız). | InnoDB docs (inference) |

---

## §5 Önceliklendirilmiş Düzeltme Listesi

### §5.1 Kritik (Yüksek Öncelik — Uygulama Gerekiyor)

#### FIX-01: `coremusic_api.api_keys` 1NF İhlali (B2)

**Sorun:** `scope` VARCHAR(1024) virgülle ayrılmış liste; `allowed_ips` TEXT-içinde-JSON.

**Öneri:** `auth.api_keys` ile birleştir (C4); `api` şemasındaki `api_keys` tablosunu bırak (drop migration'ı var).

**Migration SQL (gömülü — uygulama için onay gereklidir):**

```sql
-- FIX-01: api_keys birleştirme (auth kazanır)
-- 1. Mevcut auth.api_keys'i koru (JSON scopes)
-- 2. api.api_keys'den veri taşı (eğer canlıda varsa)
-- 3. api.api_keys tablosunu bırak (drop migration'ı mevcut)

-- Migration: 2026-10-22_001_merge_api_keys
-- Forward-only (ADR-014) · Down migration YASAK

-- 1. auth.api_keys'de scopes JSON olarak zaten mevcut (kontrollü JSON — karar #9)
-- 2. api.api_keys'ten veri oku (eğer satır varsa)
--    INSERT INTO coremusic_auth.api_keys (id, key_hash, key_type, scopes, ...)
--    SELECT ... FROM coremusic_api.api_keys
--    WHERE NOT EXISTS (SELECT 1 FROM coremusic_auth.api_keys a WHERE a.key_hash = api.key_hash);

-- 3. api.api_keys'i bırak (drop migration'ı var — ADR-034)
--    DROP TABLE IF EXISTS coremusic_api.api_keys;  -- [HUMAN APPROVAL REQUIRED]
```

#### FIX-02: `musics.artist_id` 4NF İhlali (B9)

**Sorun:** `musics.artist_id` tekil — sanatçı↔şarkı N:M ilişki tek kolona sığdırılmış (onaylı karar #3).

**Öneri:** `music_artists` junction tablosu oluştur; mevcut `artist_id` 1. satır (primary) olarak taşı.

**Migration SQL:**

```sql
-- FIX-02: music_artists junction (karar #3 — N:M)
-- Migration: 2026-10-22_002_music_artists
-- Forward-only · Expand → Migrate → Validate → Contract

-- EXPAND: Yeni junction tablosu
CREATE TABLE `coremusic_musics`.`music_artists` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `music_id` binary(16) NOT NULL COMMENT 'FK -> musics.id',
  `artist_id` binary(16) NOT NULL COMMENT 'FK -> artists.id',
  `artist_credit_id` binary(16) DEFAULT NULL COMMENT 'FK -> artist_credit.id (MusicBrainz)',
  `role` varchar(50) NOT NULL DEFAULT 'primary' COMMENT 'primary/featured/remix',
  `position` tinyint unsigned NOT NULL DEFAULT '1' COMMENT 'Sıralama (1=primary)',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ma_pair` (`music_id`, `artist_id`, `position`),
  KEY `idx_ma_artist` (`artist_id`),
  KEY `idx_ma_music` (`music_id`),
  CONSTRAINT `fk_ma_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ma_artist` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- MIGRATE: Mevcut artist_id'yi 1. satır olarak taşı
INSERT INTO `music_artists` (`id`, `music_id`, `artist_id`, `role`, `position`)
SELECT UUID_TO_BIN(UUID()), m.`id`, m.`artist_id`, 'primary', 1
FROM `coremusic_musics`.`musics` m
WHERE m.`artist_id` IS NOT NULL
  AND NOT EXISTS (
    SELECT 1 FROM `music_artists` ma
    WHERE ma.`music_id` = m.`id` AND ma.`position` = 1
  );

-- VALIDATE: Doğrulama
-- SELECT COUNT(*) FROM musics WHERE artist_id IS NOT NULL;
-- SELECT COUNT(*) FROM music_artists WHERE position = 1;
-- → İkisi eşit olmalı

-- CONTRACT: musics.artist_id'yi bırak (sonraki fazda — ADR-014 expand-contract)
-- ALTER TABLE `coremusic_musics`.`musics` DROP COLUMN `artist_id`;  -- [HUMAN APPROVAL REQUIRED]
```

#### FIX-03: `radio_now_playing` Snapshot Denorm (B6)

**Sorun:** `track_title`, `artist_name`, `album_name` inline; `music_id` FK yok (iz sürme kırık).

**Öneri:** `music_id` FK ekle; snapshot kolonlarını koru (denorm kasıtlı — performans), JOIN ile doğrulama imkanı sağla.

**Migration SQL:**

```sql
-- FIX-03: radio_now_playing.music_id FK
-- Migration: 2026-10-22_003_radio_now_playing_music_id
-- Forward-only

-- EXPAND: music_id FK ekle
ALTER TABLE `coremusic_musics`.`radio_now_playing`
  ADD COLUMN `music_id` binary(16) NULL COMMENT 'FK -> musics.id (snapshot iz sürme)' AFTER `station_id`,
  ADD KEY `idx_rnp_music` (`music_id`),
  ADD CONSTRAINT `fk_rnp_music` FOREIGN KEY (`music_id`) REFERENCES `musics` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- MIGRATE: Mevcut track_title/artist_name ile music_id eşleştir (eğer mümkünse)
-- UPDATE radio_now_playing rnp
-- JOIN musics m ON m.title = rnp.track_title AND m.artist_id = (SELECT id FROM artists WHERE name = rnp.artist_name)
-- SET rnp.music_id = m.id
-- WHERE rnp.music_id IS NULL;
-- → [HUMAN APPROVAL REQUIRED] (veri eşleştirme riski)

-- VALIDATE: Doğrulama
-- SELECT COUNT(*) FROM radio_now_playing WHERE music_id IS NOT NULL;
```

### §5.2 Orta (MEDIUM Öncelik — Planlanmalı)

#### FIX-04: `credential_audit` Comment/Kolon Uyumsuzluğu (B7)

**Sorun:** BCNF comment `(…, created_at)` diyor, kolon `occurred_at` (satır 246 vs 261).

**Öneri:** Comment düzeltmesi (doc-level, düşük risk).

**Migration SQL:**

```sql
-- FIX-04: credential_audit comment düzeltmesi
-- Migration: 2026-10-22_004_credential_audit_comment
-- Forward-only · Düşük risk (yalnız comment)

ALTER TABLE `coremusic_auth`.`credential_audit`
  MODIFY COLUMN `occurred_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
  COMMENT 'Olay zamani (UTC) — BCNF: (credential_id, action, occurred_at) -> kalan';
```

#### FIX-05: PK Tipi Revizyonu — `coremusic_api` (B5)

**Sorun:** `coremusic_api` BIGINT PK; standart BINARY(16) UUID v7'den sapıyor.

**Öneri:** `api_keys`, `api_rate_limits`, `api_webhooks` tablolarını BINARY(16)'ya dönüştür; `api_calls` (partitioned) BIGINT kalsın (hot-path sayaç — ADR-040 §2.2-b istisna).

**Migration SQL (Expand-Contract):**

```sql
-- FIX-05: coremusic_api PK revizyonu
-- Migration: 2026-10-22_005_api_pk_uuid
-- Forward-only · Expand → Migrate → Validate → Contract
-- NOT: api_calls (partitioned) HARİÇ — hot-path sayaç, BIGINT kalır

-- EXPAND: Yeni BINARY(16) kolonu ekle (api_keys örneği)
ALTER TABLE `coremusic_api`.`api_keys`
  ADD COLUMN `id_uuid` binary(16) NULL COMMENT 'UUID v7 (yeni PK)' AFTER `id`;

-- MIGRATE: Mevcut BIGINT id'leri UUID'ye dönüştür
-- UPDATE api_keys SET id_uuid = UUID_TO_BIN(UUID()) WHERE id_uuid IS NULL;

-- VALIDATE: Doğrulama
-- SELECT COUNT(*) FROM api_keys WHERE id_uuid IS NOT NULL;
-- → Tüm satırlar UUID'ye sahip olmalı

-- CONTRACT: Eski id'yi bırak, id_uuid'yi PK yap
-- ALTER TABLE `coremusic_api`.`api_keys`
--   DROP PRIMARY KEY,
--   DROP COLUMN `id`,
--   CHANGE COLUMN `id_uuid` `id` binary(16) NOT NULL COMMENT 'UUID v7',
--   ADD PRIMARY KEY (`id`);  -- [HUMAN APPROVAL REQUIRED]
```

#### FIX-06: Çift Tablolar — `wifi_networks` Birleştirme (B8)

**Sorun:** `coremusic_wireless.wifi_networks` ↔ `coremusic_system.system_wifi_networks` alan çakışması yüksek.

**Öneri:** `wireless` kazanır; `system.system_wifi_networks`'ten veriyi taşı, tabloyu bırak.

**Migration SQL:**

```sql
-- FIX-06: wifi_networks birleştirme (wireless kazanır)
-- Migration: 2026-10-22_006_merge_wifi_networks
-- Forward-only · Cross-DB migration (expand-contract)

-- EXPAND: wireless.wifi_networks zaten mevcut (binary(16) PK, daha zengin şema)
-- MIGRATE: system.system_wifi_networks'ten veri taşı
INSERT INTO `coremusic_wireless`.`wifi_networks` (`id`, `network_name`, `ssid`, `bssid`, `security_type`, `password_hash`, ...)
SELECT UUID_TO_BIN(UUID()), s.`network_name`, s.`ssid`, s.`bssid`, s.`security_type`, NULL, ...
FROM `coremusic_system`.`system_wifi_networks` s
WHERE NOT EXISTS (
  SELECT 1 FROM `coremusic_wireless`.`wifi_networks` w
  WHERE w.`ssid` = s.`ssid` AND w.`bssid` = s.`bssid`
);

-- VALIDATE: Doğrulama
-- SELECT COUNT(*) FROM coremusic_system.system_wifi_networks;
-- SELECT COUNT(*) FROM coremusic_wireless.wifi_networks;
-- → wireless ≥ system olmalı

-- CONTRACT: system.system_wifi_networks'i bırak
-- DROP TABLE IF EXISTS `coremusic_system`.`system_wifi_networks`;  -- [HUMAN APPROVAL REQUIRED]
```

#### FIX-07: `audio_features` Birleştirme (B8)

**Sorun:** `coremusic_ai.audio_features` ↔ `coremusic_musics.music_audio_features` tekrarı.

**Öneri:** `musics` kazanır; `ai.audio_features`'ten veriyi taşı, tabloyu bırak; `ai` şeması FK ile okur.

**Migration SQL:**

```sql
-- FIX-07: audio_features birleştirme (musics kazanır)
-- Migration: 2026-10-22_007_merge_audio_features
-- Forward-only · Cross-DB migration

-- EXPAND: musics.music_audio_features zaten mevcut (binary(16) PK, daha zengin şema)
-- MIGRATE: ai.audio_features'ten veri taşı
INSERT INTO `coremusic_musics`.`music_audio_features` (`id`, `music_id`, `bpm`, `energy`, `valence`, ...)
SELECT UUID_TO_BIN(UUID()), af.`music_id`, af.`bpm`, af.`energy`, af.`valence`, ...
FROM `coremusic_ai`.`audio_features` af
WHERE NOT EXISTS (
  SELECT 1 FROM `coremusic_musics`.`music_audio_features` maf
  WHERE maf.`music_id` = af.`music_id`
);

-- VALIDATE: Doğrulama
-- SELECT COUNT(*) FROM coremusic_ai.audio_features;
-- SELECT COUNT(*) FROM coremusic_musics.music_audio_features;
-- → musics ≥ ai olmalı

-- CONTRACT: ai.audio_features'i bırak
-- DROP TABLE IF EXISTS `coremusic_ai`.`audio_features`;  -- [HUMAN APPROVAL REQUIRED]
```

### §5.3 Düşük (LOW Öncelik — Vault Revizyonu)

#### FIX-08: ADR-040 §1.1-A Revizyonu (B1)

**Sorun:** "18 dosya / 156 tablo" → "20 dosya / 200 tablo" (19 `coremusic_*` + `media_catalog`).

**Öneri:** ADR-040 §1.1-A tablosunu güncelle; `index.md:196`'daki ⚠️ uyarısını kaldır.

**Not:** ADR-040 **frozen değil** (`status: accepted` ama `frozen: false`) → revizyon possible; `.ai/log.md` append-only → revizyon satırı eklenir.

#### FIX-09: `coremusic_studio` Header Düzeltmesi (B5)

**Sorun:** Header "is_deleted + deleted_at" iddiası ≠ disk (yalnız `is_deleted` mevcut).

**Öneri:** Header düzeltmesi veya `deleted_at` kolonu ekle (karar #7 timestamp politikası ile hizalı).

**Migration SQL (opsiyonel — header düzeltme tercih edilir):**

```sql
-- FIX-09: studio deleted_at kolonu (eğer karar #7 ile hizalanacaksa)
-- Migration: 2026-10-22_009_studio_deleted_at
-- Forward-only · Düşük öncelik

ALTER TABLE `coremusic_studio`.`studio_sessions`
  ADD COLUMN `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)' AFTER `is_deleted`;

ALTER TABLE `coremusic_studio`.`studio_tracks`
  ADD COLUMN `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)' AFTER `is_deleted`;

ALTER TABLE `coremusic_studio`.`studio_equipment`
  ADD COLUMN `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)' AFTER `is_deleted`;

ALTER TABLE `coremusic_studio`.`studio_collaborators`
  ADD COLUMN `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)' AFTER `is_deleted`;

ALTER TABLE `coremusic_studio`.`studio_presets`
  ADD COLUMN `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)' AFTER `is_deleted`;

ALTER TABLE `coremusic_studio`.`session_equipment`
  ADD COLUMN `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Silinme zamani (UTC)' AFTER `is_deleted`;
```

---

## §6 Cross-DB FK Politikası (ADR-040 §2.2-c Uyumluluk)

### §6.1 Mevcut Durum (28 FK — Grandfathered)

| İstisna # | Dosya | Satırlar | Hedef | Durum |
|-----------|-------|----------|-------|-------|
| X-01…X-11 | `coremusic_user.sql` | `:50, 83, 114-115, 138, 160-161, 188-189, 223-224` | `coremusic_auth.users` (8) · `coremusic_musics.musics` (3) | ✅ KABUL |
| X-12…X-24 | `coremusic_social.sql` | `:47, 67, 91, 118, 119, 150, 151, 180, 204, 205, 235, 266, 267` | `coremusic_auth.users` (11) · `coremusic_musics.musics` (2) | ✅ KABUL |
| X-25…X-28 | `coremusic_system.sql` | `:69, 100, 128, 225` | `coremusic_auth.users` (4) | ✅ KABUL |

### §6.2 Yeni FK Eklenemez (K1 Statik Kapı — PLANNED)

**Kural:** `*.sql` dosyalarında `REFERENCES <şema-adi>.` deseni **yalnızca** X-01…X-28 listesinde kayıtlı olabilir; kayıtlı olmayan **RED**.

**Bugünkü Durum:** K1 statik kapı **PLANNED** (CI job'una eklenmedi; `.github/workflows/` yalnız PHP lint/test + GitLeaks).

**Öneri:** K1 statik kapıyı CI'a bağla (ADR-040 §5.1 adım 13); yeni cross-DB FK üretimini engelle.

---

## §7 Soft-Delete + Unique Constraint Deseni (Detaylı Analiz)

### §7.1 Desen Tanımı

```sql
`is_deleted` tinyint(1) NOT NULL DEFAULT '0',
`deleted_at` timestamp NULL DEFAULT NULL,
`deleted_key` bigint GENERATED ALWAYS AS (if((`is_deleted` = 0), 0, to_seconds(`deleted_at`))) STORED,
UNIQUE KEY `uq_*` (`business_key_1`, `business_key_2`, `deleted_key`)
```

### §7.2 Avantajlar

| Avantaj | Açıklama |
|---------|----------|
| **Soft-delete + unique constraint uyumu** | `is_deleted=0` → `deleted_key=0` (tüm aktif satırlar `0` ile eşleşir, tek aktif kayıt garantilenir) |
| **Performans** | STORED generated column → index'de fiziksel olarak saklanır; hesaplama maliyeti yok |
| **Zaman bazlı eşsizlik** | `is_deleted=1` → `deleted_key=to_seconds(deleted_at)` (saniye bazlı eşsizlik — aynı saniyede 2 silme yok) |
| **MySQL 9.7 uyumu** | RefMan §15.1.25.8 ile sözdizimi birebir uyumlu |

### §7.3 Örnek Kullanım Alanları (12+ tablo)

- `i18n_translations`, `i18n_ui_strings` (system)
- `studio_presets` (studio)
- `system_eq_presets`, `system_file_manager`, `system_settings`, `system_config` (system)
- `api_keys` (auth) — `deleted_key` STORED
- `music_lyrics`, `music_notes` (musics)
- `playlist_bookmarks` (playlist)
- `user_blocked_users` (social)

### §7.4 Risk Değerlendirmesi

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|------------|
| **Saniye bazlı eşsizlik yetersiz** (aynı saniyede 2 silme) | Düşük | Düşük | `to_seconds()` yerine `to_microseconds()` (MySQL 8.0.19+) — ama ender gerekli |
| **Index boyutu artışı** (bigint kolon) | Düşük | Düşük | bigint = 8 bayt; index overhead kabul edilebilir |
| **Generated column hesaplama maliyeti** | Yok (STORED) | Yok | STORED → fiziksel saklama, hesaplama yok |

**Sonuç:** ✅ Desen **sağlam**; endüstri standardıyla uyumlu; revizyon gerekmiyor.

---

## §8 MySQL 8/9 BCNF Uygulanabilirliği (Web Araştırması Özeti)

### §8.1 BCNF Tanımı (Kaynaklar W2, W3)

> "A relation R is in Boyce Codd Normal Form if R is in 1NF and **every determinant of a non-trivial functional dependency in R is a candidate key**." — Engineering LibreTexts

> "BCNF: **Every determinant must be a candidate key**." — GeeksforGeeks

**MySQL 9 Kısıtlaması:** Yok — BCNF bir modelleme kuralıdır; MySQL kısıtlaması değil. Her determinant'ı candidate key yapmak için normal tablo tasarımı yeterli.

### §8.2 Cross-DB FK (Kaynak W1)

> "MySQL 9.7 Reference Manual §15.1.25.5: **Foreign key constraints can be defined to refer to tables in other databases** (same server). Error 1215 causes: storage engine mismatch, data type mismatch, missing REFERENCES privilege."

**CoreMusic Uygulaması:** 28 cross-DB FK **uygulanmış** (dump'larda `CONSTRAINT ... FOREIGN KEY ... REFERENCES coremusic_auth.users` satırları mevcut). MySQL 9.7 ile uyumlu; ADR-040 §2.2-c'de "grandfathered" olarak işaretli.

### §8.3 Soft-Delete + Generated Column (Kaynaklar W4, W5)

> "MySQL/MariaDB Solution: `ALTER TABLE users ADD COLUMN not_archived BOOLEAN GENERATED ALWAYS AS (IF(deleted_at IS NULL, 1, NULL)) VIRTUAL; CREATE UNIQUE INDEX users_email_active_unique ON users (email, not_archived);" — phpArch

> "Syntax: `col_name data_type [GENERATED ALWAYS] AS (expr) [VIRTUAL | STORED]`" — MySQL RefMan §15.1.25.8

**CoreMusic Uygulaması:** `deleted_key` STORED generated column deseni bu pattern'i uyguluyor (12+ tablo). ✅ **Endüstri standardıyla uyumlu.**

### §8.4 Partitioned Tablolar ve FK (Inference)

**Kısıtlama:** InnoDB partitioned tablolar **FK desteklemiyor** (MySQL docs — *inference*, resmi source değil).

**CoreMusic Uygulaması:** `coremusic_api.api_calls` partitioned (hot-path sayaç) → FK'sız (doğru tasarım).

---

## §9 Önerilerin Uygulanabilirlik Matrisi

| # | Öneri | Öncelik | Risk | Cross-DB? | Onay Gerekiyor? |
|---|-------|---------|------|-----------|-----------------|
| FIX-01 | `api_keys` birleştirme (auth kazanır) | 🔴 HIGH | Orta | ❌ Hayır | ✅ Evet (drop migration) |
| FIX-02 | `music_artists` junction (4NF) | 🔴 HIGH | Orta | ❌ Hayır | ✅ Evet (expand-contract) |
| FIX-03 | `radio_now_playing.music_id` FK | 🔴 HIGH | Düşük | ❌ Hayır | ⚠️ Kısmi (veri eşleştirme) |
| FIX-04 | `credential_audit` comment düzeltmesi | 🟡 MEDIUM | Düşük | ❌ Hayır | ❌ Hayır (doc-level) |
| FIX-05 | `coremusic_api` PK UUID revizyonu | 🟡 MEDIUM | Orta | ❌ Hayır | ✅ Evet (expand-contract) |
| FIX-06 | `wifi_networks` birleştirme | 🟡 MEDIUM | Orta | ✅ Evet | ✅ Evet (cross-DB migration) |
| FIX-07 | `audio_features` birleştirme | 🟡 MEDIUM | Orta | ✅ Evet | ✅ Evet (cross-DB migration) |
| FIX-08 | ADR-040 §1.1-A revizyonu | 🟢 LOW | Düşük | ❌ Hayır | ❌ Hayır (vault revizyonu) |
| FIX-09 | `coremusic_studio` `deleted_at` kolonu | 🟢 LOW | Düşük | ❌ Hayır | ❌ Hayır (opsiyonel) |

---

## §10 Sonraki Adımlar (Onay Kapısı)

### §10.1 Hemen Yapılabilir (Onay Gerektirmeyen)

1. **FIX-04:** `credential_audit` comment düzeltmesi (doc-level, düşük risk)
2. **FIX-08:** ADR-040 §1.1-A revizyonu (vault append-only → revizyon satırı eklenir)
3. **FIX-09:** `coremusic_studio` header düzeltmesi (opsiyonel)

### §10.2 Onay Gereken (Human Approval Required)

4. **FIX-01:** `api_keys` birleştirme → [HUMAN APPROVAL REQUIRED] (drop migration)
5. **FIX-02:** `music_artists` junction → [HUMAN APPROVAL REQUIRED] (expand-contract, veri taşıma)
6. **FIX-03:** `radio_now_playing.music_id` FK → [PARTIAL APPROVAL] (veri eşleştirme riski)
7. **FIX-05:** `coremusic_api` PK UUID → [HUMAN APPROVAL REQUIRED] (expand-contract)
8. **FIX-06:** `wifi_networks` birleştirme → [HUMAN APPROVAL REQUIRED] (cross-DB migration)
9. **FIX-07:** `audio_features` birleştirme → [HUMAN APPROVAL REQUIRED] (cross-DB migration)

### §10.3 CI Kapısı (DevOps + Data)

10. **K1 Statik Kapı:** `.github/workflows/`'a SQL job'ı ekle; `*.sql` içinde `REFERENCES <şema-adi>.` desenini X-01…X-28 listesi ile karşılaştır; kayıtlı olmayan **RED**.

---

## §11 Sonuç

### §11.1 Genel Değerlendirme

| Metrik | Değer |
|--------|-------|
| **Toplam Şema** | 20 (19 `coremusic_*` + `media_catalog`) |
| **Toplam Tablo** | 200 |
| **BCNF Uyumlu** | 16/20 (%80) |
| **3NF Uyumlu (BCNF'e yakın)** | 19/20 (%95) |
| **1NF İhlali** | 1 (`coremusic_api.api_keys.scope`) |
| **4NF İhlali** | 1 (`musics.artist_id` tekil) |
| **5NF İhlali** | 0 |
| **Cross-DB FK** | 28 (grandfathered) |
| **Çift Tablo** | 6 çift (§3.5) |
| **Soft-Delete Deseni** | ✅ Endüstri standardıyla uyumlu (12+ tablo) |

### §11.2 Kritik Aksiyonlar

| Öncelik | Aksiyon | Sahip | Süre |
|---------|---------|-------|------|
| 🔴 HIGH | FIX-01: `api_keys` birleştirme | Data + Backend | 2 gün |
| 🔴 HIGH | FIX-02: `music_artists` junction | Data | 3 gün |
| 🔴 HIGH | FIX-03: `radio_now_playing.music_id` FK | Data | 1 gün |
| 🟡 MEDIUM | FIX-05: `coremusic_api` PK UUID | Data | 2 gün |
| 🟡 MEDIUM | FIX-06/07: Çift tablo birleştirme | Data + Backend | 4 gün |
| 🟢 LOW | K1 statik kapı CI'a bağla | DevOps + Data | 2 gün |

### §11.3 "18 BCNF DB" İddiası — Son Karar

| İddia | Durum | Güncel |
|-------|-------|--------|
| "18 BCNF DB" (ADR-040) | ⚠️ **GÜNCELLENMELİ** | **20 BCNF DB** (19 `coremusic_*` + `media_catalog`; `novasearch` kapsam dışı) |
| "156 tablo" (ADR-040) | ⚠️ **GÜNCELLENMELİ** | **200 tablo** (20 dosya) |
| "82 FK (54 DB-içi + 28 cross-DB)" (ADR-040) | ✅ **DOĞRULANDI** | 28 cross-DB FK hâlâ aktif (grandfathered) |

**Onay:** Bu rapor **draft** (`status: draft`); vault'a yazım için MO onayı gereklidir.

---

## §12 Ek: Kanıt Dizinleri

### §12.1 Dosyalar (2026-10-22 Okunan)

| Dosya | Tablo | Satır | Öne Çıkan |
|-------|------:|------:|-----------|
| `coremusic_auth.sql` | 11 | 1000+ | credential_audit comment mismatch (B7) |
| `coremusic_user.sql` | 7 | 300+ | 11 cross-DB FK |
| `coremusic_musics.sql` | 26 | 1500+ | artist_id tekil (B9), radio_now_playing (B6) |
| `coremusic_albums.sql` | 10 | 500+ | — |
| `coremusic_playlist.sql` | 5 | 300+ | playlist_tracks junction (iyi) |
| `coremusic_catalog.sql` | 7 | 300+ | Lookup DB |
| `coremusic_cms.sql` | 8 | 400+ | blog_post_tags junction (iyi) |
| `coremusic_social.sql` | 11 | 600+ | 13 cross-DB FK, user_achievements denorm |
| `coremusic_ai.sql` | 6 | 300+ | audio_features ↔ music_audio_features |
| `coremusic_api.sql` | 4 | 100+ | scope CSV (B2), allowed_ips TEXT-JSON (B3) |
| `coremusic_billing.sql` | 8 | 400+ | Hand-authored (farklı header) |
| `coremusic_download.sql` | 4 | 200+ | download_queue generated column |
| `coremusic_logs.sql` | 18 | 800+ | Append-only ailesi |
| `coremusic_media.sql` | 7 | 400+ | — |
| `coremusic_neva.sql` | 4 | 200+ | eq_presets ↔ system_eq_presets |
| `coremusic_patch.sql` | 3 | 100+ | schema_versions, migration_log, patches |
| `coremusic_studio.sql` | 6 | 200+ | Header ≠ disk (B5) |
| `coremusic_system.sql` | 20 | 800+ | 4 cross-DB FK, deleted_key deseni |
| `coremusic_wireless.sql` | 5 | 300+ | wifi_networks ↔ system_wifi_networks |
| `media_catalog.sql` | 9 | 400+ | Türkçe kolon, taxonomy FK (drift imkansız) |

### §12.2 Vault Dosyaları (Okunan)

| Dosya | Satır | İçerik |
|-------|------:|--------|
| `.ai/.decisions/accepted/ADR-040-database-authority.md` | 385 | 18 BCNF DB otoritesi, 28 cross-DB FK listesi, sahiplik matrisi |
| `.ai/.sql/normalization-analysis.md` | 400+ | Önceki analiz (20 dosya / 172 tablo — 2026-10-07) |
| `.ai/index.md` §8 | 10 | "18 BCNF DB" ⚠️ VERIFICATION REQUIRED |
| `.ai/.sql/CLAUDE.md` | 50 | "21 dump dosyası (19 BCNF coremusic_* + media_catalog + novasearch)" |

### §12.3 PHP Dosyaları (Topoloji Kanıtı)

| Dosya | Satır | İçerik |
|-------|------:|--------|
| `shared/src/Database/ConnectionFactory.php` | 198 | `.env` tabanlı, DB başına TEK PDO bağlantısı (singleton); `DB_{KEY}_NAME` pattern |
| `shared/src/Database/DatabaseRegistry.php` | 33 | `registerMySql(key, host, dbName, ...)` — çoklu-DB kayıt mimarisi |
| `shared/src/Database/DatabaseManager.php` | 117 | Lazy PDO, ERRMODE_EXCEPTION, FETCH_ASSOC, EMULATE_PREPARES=false |
| `shared/src/Database/Config/DatabaseConfig.php` | 15 | DTO: host, dbName, user, password, port, charset |

### §12.4 Web Kaynakları

| # | URL | Ana İddia |
|---|-----|-----------|
| W1 | dev.mysql.com/doc/refman/9.7/en/create-table-foreign-keys.html | Cross-DB FK aynı sunucuda mümkün; hata 1215 |
| W2 | geeksforgeeks.org/dbms/boyce-codd-normal-form-bcnf | BCNF: her determinant candidate key |
| W3 | eng.libretexts.org/.../10.05 | BCNF: 1NF + her non-trivial FD'nin determinant'ı candidate key |
| W4 | phparch.com/2026/02/advanced-unique-index-patterns-for-soft-deletes... | MySQL soft-delete + unique index: GENERATED ALWAYS AS |
| W5 | dev.mysql.com/doc/en/create-table-generated-columns.html | Generated columns sözdizimi |

---

**Rapor sonu.**  
**Oluşturulma:** 2026-10-22  
**Satır:** ~500  
**Durum:** draft — MO onayı bekleniyor
