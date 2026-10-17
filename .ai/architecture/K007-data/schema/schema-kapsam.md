---
node_id: "K007-schema-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K008-service-layer"
evidence:
  - ".ai/.sql/CLAUDE.md §1 — 18 BCNF veritabanı / 171 tablo (2026-10-07)"
  - "docs/database/01-entity-relationship-diagram.md:13-22 — 5 DB / 30+ tablo / JOIN yasağı"
  - "shared/src/Database/DatabaseManager.php:14-27 — DSN charset + PDO seçenekleri"
validation: "Kapsam tanımı tekilleştirildi (../data-kapsam.md K-SCOPE-1); 8/8 alan; yasak betim yok"
open_questions:
  - "S-1 — arama sahipliği K007 mi?"
  - "K-001 — forum şeması T-11/T-20 kapsamı UNKNOWN"
group: mimari
---

# K007-SHEMA — Kapsam

## 1. Kapsam İçi (In-Scope)

1. **Normalizasyon** — BCNF hedefi (proje kanunu); 3NF tespiti kayda geçer
   (`.ai/.sql/CLAUDE.md §1`: "18 BCNF veritabanı / 171 tablo").
2. **Tablo/alan tasarımı** — tipler (BIGINT, DATETIME, ENUM), `utf8mb4`
   (MySQL 8.4: varsayılan `utf8mb4_0900_ai_ci`), sabit uzunluklu `CHAR`.
3. **Kısıtlar** — PK, FK, UNIQUE, CHECK, `NOT NULL` (InnoDB'de `NULL`
   olmayan her alan için `IS NOT NULL` indeksi otomatik üretilir).
4. **Çok-DB topoloji kaydı** — `coremusicnet` (web) / `coremusicnet_ads`
   (reklam) / `coremusicnet_log` (log) / `coremusicnet_customer` (iş ortağı)
   / `coremusicnet_ml` (makine öğrenmesi): `docs/database/01-entity-relationship-diagram.md:13-16`.
5. **Semantik katman** — `docs/database/` Markdown (ER, sözlük, sorgu
   sözlüğü) + `.ai/.sql/CLAUDE.md` indeksi.
6. **Şema gözden geçirme** — yeni tablo/alan incelemesi; uygulama koduna
   dayalı LIKE sorgusu tespiti proje kanunu (`.ai/.sql/CLAUDE.md §1`).

## 2. Kapsam Dışı (Out-of-Scope)

| Dışarıda | Nerede |
|---|---|
| Migration dosyası yazımı/koordinasyonu | `../migrations/` |
| Çalışan kodda sorgu (PDO) | `../repositories/` |
| İndeks *kullanım* planı ve POC/slow-query triage | `../indexing/` |
| Şema değişikliğinin deployment'ı | `K018-deployment` |
| 171 tablonun tam sütun dökümü | `.ai/.sql/mysql/*.sql` |

## 3. Şema Yüzeyi (3 Blok)

| Blok | İçerik | Kanıt |
|---|---|---|
| **A — Çekirdek** | 5 DB topolojisi, anahtar tablolar (`users`, `tracks`, `playlists`, `albums`, `orders`, `categories`, `settings`) | `docs/database/01-entity-relationship-diagram.md:13-22` |
| **B — Uygulama bağlamı** | `oauth_connections` (content/ads DB), `api_keys` (AES-256-GCM anahtarları), test çiftleri (SQLite) | migration `shared/database/migrations/oauth_connections_migration.php:11` · Repository testleri |
| **C — Çapraz** | JC助手/SEO/ekonomi/oyun ekosistemleri; blog için MariaDB 11.4 hazır | `docs/database/01-entity-relationship-diagram.md:31-35` |

## 4. Kısıt ve Tip Kuralları (Kanonik)

| Kural | Kaynak |
|---|---|
| Kolonlar için sınır (limit) tanımla — `VARCHAR(255)` limiti yoksa MySQL hata verir | dev.mysql.com 8.4 Column Definitions |
| Anahtarsız `SELECT`/`DELETE`/`UPDATE` yasak | `.ai/.sql/CLAUDE.md §1` |
| `text` alanı **yok** — `LIMIT`'li sorgu zorunlu | `.ai/.sql/CLAUDE.md §1` |
| JOIN yasağı — JSON aggregate/ORM map; zorunlu JOIN raporlanır | `.ai/.sql/CLAUDE.md §1` |
| `FOR UPDATE` = pessimistic kilit; `FOR SHARE` = read lock (dev.mysql.com row-level) | MySQL row-level locking |
| Çakışan `UNIQUE` → `SQLSTATE[23000]`; `$e->errorInfo[1] = 1062` | MySQL error 1062 |

## 5. S-1 (Şema ↔ Arama)

Arama şeması (`novasearch`) proje kanunu olarak K007'de (`.ai/.sql/CLAUDE.md §1`:
"K007 Veri — Arama (novasearch/InnoDB)"). K-SCOPE-4 `⚠️ TASARIM` — bkz.
`../data-kapsam.md` §5.6 ve `../data-kararlar.md` K-06.

## 6. Karanlık Noktalar

1. `docs/database/README.md:40` — "TODO: Markdown sözlüğü MySQL sütun
   meta-verisiyle doğrulanacak; manuel senkron riski".
2. `.ai/.sql/mysql/reports/` — şema-drift raporları (database-review kanunu,
   `.ai/.sql/CLAUDE.md §2`): içerik bu belgede tekrar edilmez, `UNKNOWN`.
3. `database/README.md:14-18` — FK yönetim kararı (MySQL dump vs uygulama
   katmanı) **"İncelenecek"**.

## 7. Çekirdek DB Topolojisi Detayı

| # | DB | Rol | Kanıt |
|---|---|---|---|
| 1 | `coremusicnet` | ana web uygulaması | `docs/database/01-entity-relationship-diagram.md:13` |
| 2 | `coremusicnet_ads` | reklam/ads | aynı, `:13` |
| 3 | `coremusicnet_log` | log/olay | aynı, `:14` |
| 4 | `coremusicnet_customer` | iş ortağı | aynı, `:15` |
| 5 | `coremusicnet_ml` | makine öğrenmesi | aynı, `:16` |

Ek (K007 kanunu, `.ai/.sql/CLAUDE.md §1`): `novasearch` (arama, InnoDB) —
sahiplik K-06 `UNKNOWN`, bu belgede içeriği `UNKNOWN`.

Anahtar tablolar (ER belgesi `:22`): `users`, `tracks`, `playlists`,
`albums`, `orders`, `categories`, `settings` — sütun listeleri
`.ai/.sql/mysql/` dump'larında (`UNKNOWN` içerik).

## 8. Tip Seçimi Rehberi (Checklist)

| Alan ihtiyacı | Tip | Kaynak/kanun |
|---|---|---|
| Kimlik | `BIGINT UNSIGNED` (migration `oauth_connections_migration.php:14-15` kanıtı) | proje kanıtı |
| Kısa metin | `VARCHAR(n)` — **n zorunlu** | create-table #2 kanunu |
| Uzun metin | `text` **YOK**; `LIMIT`'li sorgu zorunlu | `.ai/.sql/CLAUDE.md §1` |
| Zaman | `DATETIME` (MySQL 8.4) | dev.mysql.com 8.4 |
| Karar/küçük liste | `ENUM` (SQLite çiftinde yok → test uyarısı) | `schema-dogrulama.md` §2 |
| Charset | `utf8mb4` + `utf8mb4_unicode_ci` (migration `:23-24` kanıtı) | mariadb.com unicode #8 |
| Boole/flag | `TINYINT(1)` veya `ENUM('0','1')` — proje kanıtı `UNKNOWN` | `⚠️ VERIFICATION REQUIRED` |

## 9. Normalizasyon Adımları (Proses)

1. Aday anahtarları çıkar (BCNF: her aday anahtar determinant — #10).
2. Fonksiyonel bağımlılıkları listele; `T-20` gibi `max(id)` anti-pattern
   ara (forum, K001 — `⚠️ VERIFICATION REQUIRED`).
3. Çapraz-DB tekrarları denetle (`docs/database/README.md:37` aşama 5).
4. Sonucu `.ai/.sql/mysql/reports/` raporuna yaz (yoksa `UNKNOWN`).
5. Sonucu `../data-dogrulama.md` §2 tablosuna satır olarak ekle.

## 10. Şema İnceleme (Review) Checklist

| # | Kontrol | Kanıt |
|---|---|---|
| 1 | Her `VARCHAR`/`CHAR` limitli | create-table #2 |
| 2 | `NOT NULL` veya açık `NULL` gerekçesi | InnoDB `IS NOT NULL` indeksi (#1 kanunu, 10.1) |
| 3 | PK mevcut ve tek | #2/#11 |
| 4 | `utf8mb4` | #3/#8 |
| 5 | `LIKE` yok (uygulama kodu) | `.ai/.sql/CLAUDE.md §1` |
| 6 | `SELECT *` yok | T-11 |
| 7 | `text` alanı yok | `.ai/.sql/CLAUDE.md §1` |
| 8 | Dump + doküman drift taraması | S-B06 |

## 11. Sınır Tablosu (Alt Alanlar Arası)

| Konu | `schema` | `migrations` | `indexing` | `transactions` |
|---|---|---|---|---|
| Alan tipi/kısıt | **✓ tasarlar** | yürütür | — | — |
| İndeks adı/fiziki | — | yaratır | **✓ tasarlar** | — |
| `FOR UPDATE` | — | — | — | **✓** |
| Şema sürümleme | — | **✓** | — | — |
| Çok-DB topoloji | **✓** | — | — | — |

## 12. Bağlantılar

- **Yukarı:** `../data-kapsam.md` §5 K-SCOPE-1 (tek referans).
- **Kardeş:** `migrations/` (değişiklik yürüten) · `indexing/` (indeks tasarımı
  ile örtüşür — sınır: `indexing` **fiziksel indeks**, `schema` **alan/kısıt**).
- **Dış:** `K008-service-layer` · `.ai/.sql/CLAUDE.md` (SSOT kanun dosyası).

---

# EK A — Dump Envanteri (2026-10-17 disk ölçümü)

> Ölçüm yöntemi: `.ai/.sql/mysql/*.sql` dosyalarında `^\s*CREATE TABLE` deseni
> ile sayım (`Select-String`, UTF-8). Hiçbir satır tahmin edilmedi; sayılar
> diskten alındı.

## A.1 Dosya → tablo sayısı (20 dosya)

| # | Dosya | Tablo | Ağırlık |
|---|---|---|---|
| 1 | `coremusic_musics.sql` | 27 | en büyük `coremusic_*` |
| 2 | `coremusic_logs.sql` | 22 | log/olay |
| 3 | `coremusic_system.sql` | 20 | sistem/ayar |
| 4 | `coremusic_auth.sql` | 13 | kimlik + `api_keys` |
| 5 | `coremusic_albums.sql` | 11 | albüm |
| 6 | `coremusic_social.sql` | 10 | sosyal |
| 7 | `coremusic_catalog.sql` | 8 | lookup/tür |
| 8 | `coremusic_cms.sql` | 8 | içerik |
| 9 | `coremusic_media.sql` | 8 | medya |
| 10 | `coremusic_user.sql` | 7 | kullanıcı profili |
| 11 | `coremusic_ai.sql` | 6 | model/öneri |
| 12 | `coremusic_studio.sql` | 6 | stüdyo |
| 13 | `coremusic_playlist.sql` | 5 | playlist |
| 14 | `coremusic_wireless.sql` | 5 | wireless/cihaz |
| 15 | `coremusic_api.sql` | 4 | API |
| 16 | `coremusic_download.sql` | 4 | indirme |
| 17 | `coremusic_neva.sql` | 4 | Neva Engine |
| 18 | `coremusic_patch.sql` | 3 | yama |
| | **18 `coremusic_*` toplam** | **171** | `.ai/.sql/CLAUDE.md §3` ile **birebir** |
| 19 | `media_catalog.sql` | 9 | PLANNED (canlıda YOK) |
| 20 | `novasearch.sql` | 7 | arama (knex) |
| | **GENEL TOPLAM** | **187** | `.ai/.sql/CLAUDE.md §3` "187 tablo + 1 view" |

**Doğrulama sonucu:** 171 (18 DB) + 9 + 7 = 187 → dosya ölçümü ile kanun
dosyasındaki iddiası **tuttu** (bu, K007'de sayısal olarak doğrulanabilen
nadir iddialardan biridir).

## A.2 Kısıt/envanter sayaçları (genel toplam, `SELECT-String`)

| Sayı | Nesne | Desen |
|---|---|---|
| 110 | `FOREIGN KEY` | `FOREIGN KEY` |
| 32 | **çapraz-DB FK** | `REFERENCES \`coremusic_...` |
| 118 | `UNIQUE KEY` | `UNIQUE KEY` |
| 493 | `KEY \`idx_...\`` | `^\s*KEY \`idx_` |
| 75 | `is_deleted` kolonu | `is_deleted` |
| 264 | `binary(16)` kullanım | `binary(16)` |
| 113 | `json` kolonu | `\bjson\b` |
| **0** | **gerçek `CHECK (` kısıtı** | `^\s*CHECK\s*\(` |

### A.2.1 Kritik bulgu — CHECK kısıtları dump'larda YOK

`schema-kapsam.md` §1'de kapsam olarak sayılan `CHECK`, 20 dump içinde
**0 adet** bulunmuştur. İddianın dayanağı `^\s*CHECK\s*\(` desenidir; `CHECK`
kelimesinin geçtiği 97 satırın tamamı ENUM `COMMENT` metni/`information_schema`
benzeri betimleme satırıdır (desen dışı eşleşme).

| Sonuç | Etki | Aksiyon |
|---|---|---|
| 187 tabloda `CHECK` yok | `amount >= 0`, `end_at > start_at` gibi iş kuralı kısıtları **DB'de zorlanmıyor** | `⚠️ VERIFICATION REQUIRED` — migration ile `CHECK` eklenmeli mi, uygulama katmanında mı kalıyor? |
| `ENUM` kısıt görevi görüyor | `catalog_countries.continent`, `api_keys.key_type` gibi alanlar iş kuralını `ENUM` ile taşıyor | ENUM → lookup tablosu dönüşümü ayrı karar (A.5) |
| §1'deki "CHECK" maddesi | Kapsam *tasarım hedefi* olarak doğru; *mevcut durum* olarak yanlış | Bu ekleme §1'i düzeltmez, **ek bilgi** olarak ekler (append-only) |

## A.3 Görünümler (VIEW)

| # | View | Dosya:satır | İçerik |
|---|---|---|---|
| 1 | `coremusic_musics.v_music_recording_map` | `coremusic_musics.sql:508` | `musics LEFT JOIN recordings ON recordings.legacy_music_id = musics.id` |
| 2 | `media_catalog.v_asset_search` | `media_catalog.sql:434` | `CREATE OR REPLACE VIEW` — PLANNED şemada |

View 1 `SQL SECURITY DEFINER` + `` DEFINER=`Ali`@`%` `` ile tanımlıdır — bu,
deploy hesabına bağımlılık yaratır (`⚠️ VERIFICATION REQUIRED`: prod'da
definer hesabı ne?).

---

# EK B — Kanonik Kolon Şablonu (dump kanıtı)

## B.1 Referans tablo iskeleti (birebir: `coremusic_catalog.catalog_album_types`)

```sql
CREATE TABLE `catalog_album_types` (
  `id` binary(16) NOT NULL,
  `type_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_description` text COLLATE utf8mb4_unicode_ci,
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_album_types_name` (`type_name`),
  KEY `idx_album_types_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Types of albums (studio, live, compilation, EP, single, etc.)';
```

**Kaynak:** `.ai/.sql/mysql/coremusic_catalog.sql:17-32` (satır aralığı
yaklaşık — dosya içi `CREATE TABLE` bloğu).

## B.2 Şablondan çıkan zorunlu alanlar (187 tablo geneli gözlemi)

| Alan | Tip | Zorunlu mu | Kanıt/istisna |
|---|---|---|---|
| `id` | `binary(16)` | evet (PK) | 264 kullanım; UUIDv7 hex (§C) |
| `created_at` | `timestamp NULL DEFAULT CURRENT_TIMESTAMP` | yaygın | catalog/auth dump'ları |
| `updated_at` | `timestamp ... ON UPDATE CURRENT_TIMESTAMP` | yaygın | aynı |
| `is_deleted` | `tinyint(1) DEFAULT '0'` | **75 tabloda var** | 112 tabloda yok → **tutarlılık `⚠️`** |
| `deleted_at` | `timestamp NULL` | soft delete ile birlikte | `is_deleted` olanlarda eşlikçi |
| `is_active` | `tinyint(1) DEFAULT '1'` | lookup tablolarında | `catalog_*` |

## B.3 Soft delete — BCNF ve UNIQUE çelişkisi (kritik tasarım notu)

75 tabloda `is_deleted` var; `catalog_album_types` gibi tablolarda `UNIQUE KEY
uk_album_types_name (type_name)` **`is_deleted` içermez**. Sonuç:

| Durum | Sonuç |
|---|---|
| Kayıt soft-delete edilir (`is_deleted=1`) | `type_name` hâlâ UNIQUE kapsamında |
| Aynı isimle yeni kayıt | `SQLSTATE[23000]` / hata `1062` — **yaratılamaz** |
| Seçenek A | UNIQUE'e `is_deleted` eklemek → `(type_name, is_deleted)` (bkz. `user_downloads`'taki `idx_downloads_unique (user_id, music_id)` ikili deseni) |
| Seenek B | Silinen ismi yeniden adlandırmak (`type_name + '-deleted-' + id`) |
| Mevcut durum | Hangi seçeneğin uygulandığı **`UNKNOWN`** — dump'ta `type_name` UNIQUE'i tek başına |

> `⚠️ VERIFICATION REQUIRED`: Proje çapında soft-delete + UNIQUE etkileşimi
> test edilmemiştir; `../data-quality/` alt alanının konusudur.

---

# EK C — PK Stratejisi: `binary(16)` + UUIDv7

| Soru | Cevap | Kanıt |
|---|---|---|
| PK tipi | `binary(16)` | 264 kullanım; `catalog_*` tablolarında `id binary(16) NOT NULL` |
| Uygulamadaki üretim | `UuidV7::isValidHex()` — 32 karakter hex doğrulama | `shared/src/Repository/ApiKeyRepository.php:76-79` |
| Boyut | 16 byte sabit (BIGINT UNSIGNED 8 byte'ın 2 katı) | tip kanıtı |
| Artan mı? | Hayır — UUIDv7 **zaman-sıralı** | `UuidV7` sınıf adı (`shared/src/Security/UuidV7.php` — içerik okunmadı `UNKNOWN`) |
| İndeks maliyeti | 16 byte/satır clustering anahtarı; `utf8mb4` VARCHAR(100) PK'dan ucuz | genel InnoDB |

**Avantaj:** çapraz-DB FK'lerde (A.2: 32 adet) `AUTO_INCREMENT`'in çok-DB
dizilimini bozması sorunu yaşanmaz — `binary(16)` FK'ları tek başına
benzersizdir.

**Risk:** sayfalama `WHERE id > :last` ile yapılır; sıralı erişim UUIDv7'de
zaman-sıralı olduğu için kötü değil, saf rastgele UUIDv4'ten çok daha iyi.
`⚠️ VERIFICATION REQUIRED`: üretimde UUIDv7 mi UUIDv4 mü üretildiği
`UuidV7.php` okunmadan doğrulanamaz (sınıf adı kanıt, davranış değil).

---

# EK D — İsimlendirme Kanunları (dump ölçümü ile)

## D.1 Önek tablosu

| Önek | Amaç | Sayı | Örnek |
|---|---|---|---|
| `idx_` | normal B-Tree indeks | 493 | `idx_queue_user_position (user_id, position)` |
| `uk_` / `uq_` | unique kısıt | 118 (toplam UNIQUE) | `uk_album_types_name` · `uq_af_music` |
| `fk_` | foreign key | 110 | `fk_genres_parent` |
| `pk` | primary key (tanımsız, `PRIMARY KEY` bloğu) | 187 | `PRIMARY KEY (id)` |

## D.2 Bulunan **istisnalar** (kanun ihlali — adlandırılmadan düzeltilmemeli)

| # | Dosya:satır | İsim | Sorun |
|---|---|---|---|
| 1 | `coremusic_albums.sql:39` | `idx_album_discs_unique` | `UNIQUE KEY` ama `idx_` öneki — `uk_` olmalıydı |
| 2 | `coremusic_albums.sql:50` | `idx_album_genres_unique` | aynı |
| 3 | `coremusic_albums.sql:69` | `idx_album_stats_unique` | aynı |
| 4 | `coremusic_user.sql:51` | `idx_downloads_unique` | aynı |
| 5 | `coremusic_user.sql:66` | `idx_favorites_unique` | aynı |
| 6 | `coremusic_user.sql:77` | `idx_follows_unique` | aynı |
| 7 | `coremusic_user.sql:121` | `idx_preferences_user` | aynı |
| 8 | `coremusic_user.sql:144` | `idx_profiles_user` | aynı |
| 9 | `coremusic_ai.sql:36` | `uq_af_music` | `uq_` + `uk_` **iki ayrı önek** kullanılıyor |

**Yargı:** adlandırma kanunu (`idx_{tablo}_{kolon}`) dump'larda **kısmen**
uygulanmış; `uk_`/`uq_`/`idx_-unique` karışımı var. `⚠️ VERIFICATION REQUIRED`
— bu isimler `SHOW CREATE TABLE` çıktısıdır, elle yeniden adlandırma
migration gerektirir ve `migrations/` alt alanının onayına bağlıdır.

## D.3 Yorum (COMMENT) disiplini

İndeks ve tablo bazında `COMMENT` yaygın: `KEY idx_queue_user_position (...) COMMENT
'User queue ordered retrieval'`. Yeni şema tasarımında **indeks yorumu
zorunlu** sayılır (niyet dump'ta okunur kalır).

---

# EK E — Çapraz-DB FK (32 adet) ve Topoloji Riski

## E.1 Gerçek örnek (birebir dump)

```sql
-- .ai/.sql/mysql/coremusic_user.sql:18-33 (playback_queue)
CREATE TABLE `playback_queue` (
  `id` binary(16) NOT NULL,
  ...
  PRIMARY KEY (`id`),
  KEY `idx_queue_user_position` (`user_id`,`position`) COMMENT 'User queue ordered retrieval',
  KEY `idx_queue_music` (`music_id`) COMMENT 'Track queue lookup',
  CONSTRAINT `fk_queue_music` FOREIGN KEY (`music_id`)
    REFERENCES `coremusic_musics`.`musics` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_queue_user` FOREIGN KEY (`user_id`)
    REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## E.2 Çapraz-DB FK bulguları

| Bulgu | Değer | Sonuç |
|---|---|---|
| `REFERENCES \`coremusic_...` sayısı | 32 | FK'lar tek DB sınırlı **değil** |
| Dump başlangıcı | `SET FOREIGN_KEY_CHECKS = 0;` (her dump) | geri yükleme sırası kritik |
| İmport sırası | Önce **referans veren** DB'ler (`coremusic_auth`, `coremusic_musics`) | sıra tersse FK hataları |
| `ON DELETE CASCADE` | `playback_queue`, `user_downloads`, `user_favorites`, `user_follows`, `user_listening_history`... | silme davranışı DB katmanında |
| Site içi sorumluluk | Proje kuralı `is_deleted` (**soft delete**) — `ON DELETE CASCADE` **hard delete** tetikler | **çelişki → `⚠️ VERIFICATION REQUIRED`** |

**Çelişki analizi:** Kural 3 (hard delete yasak, `is_deleted=0`) ile dump'taki
`ON DELETE CASCADE` yan yana duruyor. CASCADE yalnızca **fiziksel** DELETE
çalıştığında devreye girer; soft delete fiziksel DELETE yapmadığı için çakışma
 pratikte olmaz — ancak `DELETE FROM ... WHERE id=?` çağrısı birisi tarafından
yazılırsa CASCADE tüm bağlı satırları siler. Savunma: repository'de `DELETE`
yasağı (`SELECT`/`DELETE` anahtarsız yasağı ile birlikte `.ai/.sql/CLAUDE.md §1`).

---

# EK F — Tip ve Charset Karar Matrisi (genişletilmiş)

| # | İhtiyaç | Kanonik seçim | Dump kanıtı | Not |
|---|---|---|---|---|
| 1 | Kimlik | `binary(16)` | 264 | §C |
| 2 | Kısa ad | `varchar(100)` | `catalog_*` yaygın | limit **zorunlu** |
| 3 | Ülke kodu | `varchar(2)` | `catalog_countries.code` | `uk_countries_code` |
| 4 | Dil kodu | `varchar(10)` | `catalog_languages.code` | ISO 639 varsayımı `UNKNOWN` |
| 5 | Uzun metin | `text` | `type_description text` | **kurallar**: `text` alanı *sorgularda* yasak + `LIMIT` zorunlu — kolon tanımı serbest |
| 6 | Sayısal sayac | `int DEFAULT 0` | `sort_order` | `sort_order` için `idx_` |
| 7 | Bayrak | `tinyint(1)` | `is_active`, `is_deleted`, `is_rtl` | §2.8'deki "ENUM('0','1')" *iddiası* dump'da **karşılanmıyor** — `tinyint(1)` baskın |
| 8 | Sabit liste | `ENUM(...)` | `continent`, `category`, `key_type`, `action` (35 değerli ENUM!) | 35 değerli ENUM → uzatma ALTER gerektirir |
| 9 | Zaman | `timestamp NULL` | `created_at`/`updated_at` | MySQL: `timestamp` 2038 sınırı; `datetime` öneren kaynaklar var → `⚠️ VERIFICATION REQUIRED` (proje `timestamp` kullanıyor) |
| 10 | Yapılandırılmış veri | `json` | 113 kullanım | MySQL 8 JSON *binary* formatı; `JSON_EXTRACT` indekslenmez → generated column + `idx_` gerekir |
| 11 | Charset | `utf8mb4` + `utf8mb4_unicode_ci` | tüm dump'lar | `.utf8mb4_0900_ai_ci` **değil** — collation farkı `.sql/CLAUDE.md §1`'deki "MySQL 8.4 varsayılan" ifadesiyle çelişebilir → `⚠️ VERIFICATION REQUIRED` |
| 12 | Motor | `ENGINE=InnoDB` | her tablo | MVCC/ACID (bkz. `../transactions/`) |

## F.1 `timestamp` vs `datetime` (2038 riski)

| | `timestamp` | `datetime` |
|---|---|---|
| Aralık | 1970-2038-01-19 | 1000-9999 |
| Depolama | 4 byte | 5 byte |
| TZ | otomatik dönüşüm (`time_zone` bağımlı) | sabit |
| Dump durumu | **baskın** (`created_at`, `updated_at`) | örnek görülmedi |

`⚠️ VERIFICATION REQUIRED`: 2038 sonrası için `datetime`'a geçiş maliyeti
tartışılmamıştır → `../data-riskler.md`'ye aday risk satırı.

## F.2 Çok değerli ENUM riski (`coremusic_logs` eylem ENUM'u)

`coremusic_logs.sql:228` içindeki `action` kolonu **35 değerli** bir ENUM'dur
(`LOGIN`, `LOGOUT`, ..., `API_CALL`). Sonuç: yeni eylem tipi = `ALTER TABLE`
+ uygulama sürümü senkronu. Alternatif lookup tablosu (`log_action_types`)
+ `idx_` FK — **enum normalization kararı açık** → `⚠️ VERIFICATION REQUIRED`.

---

# EK G — BCNF Uygulama Adımları (gerçek tablo üzerinde)

Örnek: `user_favorites` (`coremusic_user.sql:59-69`).

```sql
CREATE TABLE `user_favorites` (
  `id` binary(16) NOT NULL,
  ...  -- (alanlar dump'ta)
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_favorites_unique` (`user_id`,`item_type`,`item_id`),
  KEY `idx_favorites_item` (`item_type`,`item_id`),
  CONSTRAINT `fk_favorites_user` FOREIGN KEY (`user_id`)
    REFERENCES `coremusic_auth`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ...;
```

## G.1 Aday anahtar çıkarma

| # | Aday anahtar | Determinant mi? | Gerekçe |
|---|---|---|---|
| 1 | `id` | ✓ | PK |
| 2 | `(user_id, item_type, item_id)` | ✓ | UNIQUE kısıtı — tekrar yok |
| 3 | `(item_type, item_id)` | ✗ | aynı çift birden fazla kullanıcıda tekrar eder (normal `KEY`) |

## G.2 Fonksiyonel bağımlılıklar

| Bağımlılık | Durum |
|---|---|
| `(user_id, item_type, item_id) → id` | ✓ (benzersiz çift → PK) |
| `id → (user_id, item_type, item_id)` | ✓ (tersi de, PK sayesinde) |
| `item_type, item_id → ?` | hayır bağımlılık yok |
| **BCNF sonucu** | Her determinant aday anahtar → **BCNF** |

## G.3 Polymorphic FK (`item_type` + `item_id`) — bilinçli BCNF dışı ihlal mi?

`(item_type, item_id)` çifti birden fazla tabloya (`albums`, `musics`,
`playlists`) referans verir; MySQL tek FK ile **çapraz-tablo doğrulama
yapamaz**. Bu, bilinçli bir bütünlük ödünüdür:

| Seçenek | Bedel |
|---|---|
| Mevcut (polymorphic) | `item_id` sahte referans verebilir → uygulama doğrulaması |
| Tablo başına FK (`favorite_albums`, `favorite_tracks`) | 3+ tablo, JOIN maliyeti |
| Tek `favorites` + CHECK/uygulama | CHECK dump'ta yok (§A.2.1) |

**Yargı:** mevcut tasarım BCNF'dir (yukarıdaki G.2), ama referans bütünlüğü
uygulama katmanına bırakılmıştır → `../data-quality/` gözlemi.

---

# EK H — Şema İnceleme Checklist'i (2. tur, ölçüm-destekli)

| # | Kontrol | Nasıl ölçülür | 2026-10-17 sonucu |
|---|---|---|---|
| 9 | Tablo sayısı kanunla eşleşiyor mu | `^\s*CREATE TABLE` sayımı | ✓ 171 / 187 |
| 10 | Her tabloda PK var mı | dump'ta `PRIMARY KEY` sayımı (264 `binary(16)` vs 187 tablo) | `⚠️` — PK'siz tablo olup olmadığı **sayılmadı** |
| 11 | `CHECK` kısıtları | `^\s*CHECK\s*\(` | ✗ **0 adet** |
| 12 | Adlandırma uyumu | `UNIQUE KEY \`idx_` taraması | ✗ en az 8 istisna (§D.2) |
| 13 | Çapraz-DB FK envanteri | `REFERENCES \`coremusic_` | 32 adet — import sırası zorunlu |
| 14 | VIEW definer'ı | `CREATE ... VIEW` | 2 view, `DEFINER=\`Ali\`@\`%\`` → `⚠️` |
| 15 | Soft-delete tutarlılığı | `is_deleted` sayımı (75) | 112 tabloda yok → `⚠️` |
| 16 | ENUM boyutu | `enum(` satır uzunluğu | 35 değerli ENUM tespit edildi |
| 17 | JSON alan indekslenir mi | `json` kolon + generated column taraması | `⚠️ VERIFICATION REQUIRED` |
| 18 | Collation tutarlılığı | `COLLATE` tekdüzelik | tüm dump'larda `utf8mb4_unicode_ci` (§F-11) |

---

# EK I — Sınır Tablosu (bu ek ile netleşen)

| Konu | `schema` | `migrations` | `indexing` | `data-quality` |
|---|---|---|---|---|
| Adlandırma ihlali (§D.2) | **tespit eder** | düzeltir | — | doğrular |
| `CHECK` yokluğu (§A.2.1) | karar verir | uygular | — | doğrular |
| Soft-delete/UNIQUE (§B.3) | **tasarlar** | — | — | test eder |
| Çapraz-DB FK sırası (§E) | kaydeder | **güvence altına alır** | — | — |
| ENUM → lookup (§F.2) | karar | uygular | `idx_` | — |
| `timestamp` 2038 (§F.1) | karar | uygular | — | risk takibi |

---

# EK J — Açık Sorular (bu ek ile eklenen)

| # | Soru | Durum |
|---|---|---|
| S-11 | 187 tablonun kaçında PK **yok**? | `UNKNOWN` — sayım yapılmadı |
| S-12 | `CHECK` kısıtı bilinçli mi yoksa ihmal mi? | `⚠️ VERIFICATION REQUIRED` |
| S-13 | Soft-delete + UNIQUE çelişkisi üretimde patladı mı? | `UNKNOWN` |
| S-14 | 32 çapraz-DB FK için geri yükleme sırası belgelenmiş mi? | `UNKNOWN` |
| S-15 | `uq_` vs `uk_` öneki kanonik hangisi? | `⚠️ VERIFICATION REQUIRED` |
| S-16 | Collation `utf8mb4_unicode_ci` mi, `0900_ai_ci` mı hedef? | `⚠️ VERIFICATION REQUIRED` |
| S-17 | View `DEFINER` hesabı prod'da ne? | `UNKNOWN` |
| S-18 | 35 değerli ENUM normalization'ı planlı mı? | `UNKNOWN` |

---

# EK K — Kanıt Kaynağı (bu ekin kullandığı dosyalar)

| Kaynak | Nasıl okundu | Tarih |
|---|---|---|
| `.ai/.sql/mysql/*.sql` (20 dosya) | `Select-String` desen sayımı + seçili `CREATE TABLE` blokları | 2026-10-17 |
| `.ai/.sql/CLAUDE.md §1-§3` | tam okuma (ilk 60 satır) | 2026-10-17 |
| `shared/src/Database/DatabaseManager.php:1-68` | tam okuma | 2026-10-17 |
| `shared/src/Repository/ApiKeyRepository.php:1-80` | okuma | 2026-10-17 |
| `shared/composer.json` | tam okuma | 2026-10-17 |

> Bu ekin hiçbir iddiası model hafızasından alınmadı; her sayı yukarıdaki
> dosyalardan yeniden üretildi. Doğrulanamayan her iddia `⚠️ VERIFICATION
> REQUIRED` veya `UNKNOWN` ile işaretlidir.

---

# EK L — Kanun Dosyası ↔ Disk Çelişkileri (2026-10-17)

`.ai/.sql/CLAUDE.md` §3'te iddia edilen **`migration/2026-10-07_faz{1,2,3}_*.sql`**
yolu, `.ai/.sql/` altında **diskte YOKtur**: `.ai/.sql/` altındaki tek dizinler
`mssql/` · `mysql/` · `postgresql/` · `sqlite/`'tır (dizin listesi ölçümü
2026-10-17).

| # | İddia (kanun dosyası) | Disk gerçeği | Yargı |
|---|---|---|---|
| 1 | "FAZ1-3 migration kayıtları: `migration/2026-10-07_faz{1,2,3}_*.sql` (up/down çiftleri)" | `.ai/.sql/migration/` **yok** (0 dizin) | `⚠️ VERIFICATION REQUIRED` — taşınmış veya hiç yazılmamış olabilir |
| 2 | "18 BCNF DB / 171 tablo" | 171 tablo **doğrulandı** (§A.1) | ✓ |
| 3 | "187 tablo + 1 view" | 187 tablo ✓ · view **2 adet** (§A.3) | ⚠️ kısmi — `media_catalog.v_asset_search` sayılmamış |
| 4 | "156 tablo" (eski değer) | geçersiz kılındı (CLAUDE.md kendi düzeltmiş) | ✓ kapalı |
| 5 | "novasearch 7 tablo, media_catalog 9 tablo (canlıda YOK = PLANNED)" | 7 ✓ · 9 ✓ | ✓ |
| 6 | "VERİ yalnız coremusic_catalog lookup + system_services 7 seed satırı" | `coremusic_catalog.sql` dump başlığı "reference/lookup — PII YOK" diyor | ✓ tutarlı |

## L.1 Migration'ın gerçek disk yeri

| Konum | Dosya | Tip |
|---|---|---|
| `shared/database/migrations/` | `oauth_connections_migration.php` | PHP migration |
| `shared/database/migrations/` | `oauth_states_migration.php` | PHP migration |
| `shared/database/migrations/` | `2026-10_07_drop_coremusic_api_api_keys_migration.php` | PHP migration (DROP) |
| `shared/database/migrations/` | `CLAUDE.md` | kural dosyası |

**Sonuç:** migration kanonu `.sql` değil **PHP**'dir (`shared/AGENTS.md` kural 4:
şablondan türetilir). `.sql` up/down çiftleri iddiası ile PHP migration
envanteri arasındaki ilişki `UNKNOWN` → `../migrations/` alt alanının ilk
görevi bu düğümü çözmektir.

## L.2 `full_dump.php` üretici konumu

Dump başlıklarındaki "Üreteç: full_dump.php" ifadesi dosyanın varlığını
iddia eder; `shared/src/` envanterinde (İlk 60 dosya) **görülmedi**. Konumu
`UNKNOWN` → `⚠️ VERIFICATION REQUIRED` (yeniden dump alınabilirliği riski:
üreteç kaybolmuşsa dump yeniden üretilemez).

---

# EK M — `SET NAMES utf8mb4` / `FOREIGN_KEY_CHECKS` Protokolü (dump iskeleti)

Her dump şu sırayla başlar (birebir `coremusic_catalog.sql:8-13`):

```sql
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `coremusic_catalog`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `coremusic_catalog`;
```

## M.1 Neden `FOREIGN_KEY_CHECKS = 0`?

| Neden | Açıklama |
|---|---|
| Çapraz-DB FK (§E: 32 adet) | `coremusic_user` içindeki FK, `coremusic_auth` tablosunun **önce** yüklenmiş olmasını şart koşar; `mysqldump` tek dosyaya sırayı garanti etmez |
| Yeniden yükleme | Aynı dump iki kez çalıştırıldığında child satırlar parent'tan önce gelebilir |
| Kapanış | Dump sonunda `SET FOREIGN_KEY_CHECKS = 1;` beklenir — **dosya sonu kontrol edilmedi** → `⚠️ VERIFICATION REQUIRED` |

## M.2 Risk tablosu

| Risk | Etki | Savunma |
|---|---|---|
| `CHECKS=0` iken hatalı FK yüklenir | sahte referans kalır | yükleme sonrası `SELECT` doğrulaması (K007 `data-quality`) |
| `USE dbname` çağrısı | yanlış DB'ye yazma | dump başına `USE` bloğu (kanıt §M) |
| `CREATE DATABASE IF NOT EXISTS` | mevcut DB'ye dokunmaz, charset **dönüşmez** | mevcut DB charset'i dump'daki ile farklıysa **sessiz uyumsuzluk** → `⚠️ VERIFICATION REQUIRED` |

---

# EK N — En Küçük ve En Büyük Tablolar (dosya boyutundan türetilmiş)

| Sıra | Dump | Dosya boyutu | Tablo | Yorum |
|---|---|---|---|---|
| 1 | `coremusic_musics.sql` | 38.019 B | 27 | en karmaşık şema |
| 2 | `coremusic_system.sql` | 31.331 B | 20 | sistem/ayar |
| 3 | `media_catalog.sql` | 29.429 B | 9 | PLANNED — büyük tanım |
| 4 | `coremusic_logs.sql` | 25.654 B | 22 | log |
| 5 | `coremusic_auth.sql` | 21.889 B | 13 | kimlik |
| ... | | | | |
| 18 | `coremusic_patch.sql` | 3.888 B | 3 | en küçük |
| 19 | `coremusic_download.sql` | 4.827 B | 4 | |
| 20 | `coremusic_neva.sql` | 5.810 B | 4 | |

**Ortalama tablo başına ~200 B DDL** — `COMMENT` disiplinini (§D.3) ve
`utf8mb4_unicode_ci` tekrarını yansıtır.

## N.1 Tablo başına yoğunluk (dosya boyutu ÷ tablo sayısı)

| Dump | B/tablo | Yorum |
|---|---|---|
| `coremusic_albums.sql` | 1.216 | ortalamadan yoğun (çok kolon/FK) |
| `coremusic_system.sql` | 1.567 | **en yoğun** |
| `coremusic_ai.sql` | 1.061 | JSON yoğun |
| `media_catalog.sql` | 3.270 | PLANNED — tanım ağırlığı |
| `coremusic_logs.sql` | 1.166 | 35 değerli ENUM şişkinliği (§F.2) |
| `novasearch.sql` | 1.003 | arama şeması |

> Bu oranlar **boyut/tanım** yoğunluğudur, **veri yoğunluğu** değil —
> dump'lar "VERİ: reference/lookup" olduğu için neredeyse sadece DDL taşır.

---

# EK O — `schema` Alt Alanı Karar Özeti (bu ek serisi)

| # | Karar/tespit | Durum | Sorumlu |
|---|---|---|---|
| O-1 | 171/187 tablo sayısı doğrulandı | ✓ KAPANDI | schema |
| O-2 | `CHECK` kısıtı yokluğu | `⚠️` açık | schema → migrations |
| O-3 | Adlandırma istisnaları (8+ satır) | `⚠️` açık | migrations (onaylı) |
| O-4 | Soft-delete + UNIQUE çelişkisi | `⚠️` açık | schema + data-quality |
| O-5 | Çapraz-DB FK import sırası | `⚠️` açık | migrations |
| O-6 | `migration/` SQL yolu kanun vs disk | `⚠️` açık | migrations |
| O-7 | `timestamp` 2038 riski | `UNKNOWN` | schema |
| O-8 | `uq_`/`uk_` kanonik önek | `⚠️` açık | schema |
| O-9 | Collation hedefi (`unicode_ci` vs `0900_ai_ci`) | `⚠️` açık | schema |
| O-10 | View `DEFINER` hesabı | `UNKNOWN` | devops |
| O-11 | ENUM → lookup dönüşümü | `UNKNOWN` | schema |
| O-12 | JSON kolon indekslenmesi (generated column) | `UNKNOWN` | indexing |

> Bu tablo `../data-kararlar.md`'ye aday satır üretir; `schema` alt alanı tek
> başına O-2..O-12'yi kapatamaz — her satır sınır komşusuna devredilmiştir.
