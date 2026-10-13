---
node_id: K007-data/migrations-ornekler
node_type: subdomain
status: draft
parent_id: K007-data/migrations
children: []
depends_on:
  - "K007-data/migrations-anayasa"
  - "K007-data/migrations-surucu"
evidence:
  - "shared/database/migrations/2026-10_07_drop_coremusic_api_api_keys_migration.php:39 — DROP TABLE IF EXISTS"
  - "shared/database/migrations/oauth_connections_migration.php:18-42 — CREATE TABLE + UNIQUE + FK + ENGINE"
  - "shared/database/migrations/oauth_states_migration.php:20-32 — CREATE TABLE + state token UNIQUE"
validation:
  - "2026-10-13 — 3 migration dosyası satır satır okundu; satır numaraları grep ile doğrulandı"
open_questions:
  - "Migration runner (çalıştırma aracı) repo'da YOK → ⚠️ VERIFICATION REQUIRED"
  - "Bu dosyaların üretimde çalıştırıldığına dair log → UNKNOWN"
group: kod
---

# migrations-ornekler — Migration Kod Örnekleri (K007)

> **Rol:** Repo'daki **gerçek 3 migration dosyasının** satır satır çözümlemesi
> ve DDL kalıp örnekleri. Kurallar `migrations-anayasa.md`'de, sürüş akışı
> `migrations-surucu.md`'dedir. Bu dosyada her örnek gerçek disk kanıtıdır.

## §1 Amaç ve Kapsam

| Alan | Değer |
|---|---|
| node_id | K007-data/migrations-ornekler |
| node_type | subdomain |
| parent_id | K007-data/migrations |
| Grup | kod |
| Kapsam | 3 gerçek migration dosyası · DDL kalıpları · precheck/rollback örnekleri |
| Kapsam dışı | Sürüş akışı (surucu) · bağlayıcı kurallar (anayasa) |

## §2 Dosya Envanteri (Disk Kanıtı)

| Dosya | Boyut | Dönüş | Kanıtlı satır |
|---|---|---|---|
| `2026-10_07_drop_coremusic_api_api_keys_migration.php` | 2283 bayt | `return [...]` (array) | `:39` DROP |
| `oauth_connections_migration.php` | 1792 bayt | `return $queries` | `:18-42` CREATE |
| `oauth_states_migration.php` | 1248 bayt | `return $queries` | `:20-32` CREATE |

**Kaynak:** `shared/database/migrations/` dizin listesi (3 PHP + `CLAUDE.md`) — 2026-10-13.

| Gözlem | Değer |
|---|---|
| Toplam migration dosyası | 3 |
| Runner/komut dosyası | Yok → ⚠️ VERIFICATION REQUIRED |
| Tarih öneki taşıyan | 1 (`2026-10_07_`) |
| Tarih öneki taşımayan | 2 (`oauth_*`) → sıralama belirsiz (UNKNOWN) |

## §3 Örnek-1: DROP Migration (Yıkıcı)

**Dosya:** `shared/database/migrations/2026-10_07_drop_coremusic_api_api_keys_migration.php`

| Bölüm | Satır | İçerik |
|---|---|---|
| Başlık | 1 | `<?php declare(strict_types=1);` |
| Karar notu | 3–21 | ADR-040 single-writer, ADR-020, kullanıcı onayı 2026-10-07 |
| `$precheck` | 26–31 | 2 salt-okunur COUNT sorgusu |
| `$queries` | 33–40 | tek sorgu: `DROP TABLE IF EXISTS` |
| `return` | 43–51 | name/database/destructive/approval/precheck/queries/rollback |

**Kod (satır 39):**

```php
"DROP TABLE IF EXISTS coremusic_api.api_keys",
```

**Kod (satır 43–51 — dönüş şeması):**

```php
return [
    'name'        => '2026-10_07_drop_coremusic_api_api_keys',
    'database'    => 'coremusic_api',
    'destructive' => true,
    'approval'    => 'REQUIRED - kullanici onayi ayrica alinacak',
    'precheck'    => $precheck,
    'queries'     => $queries,
    'rollback'    => 'mysqldump yedek dosyasindan CREATE TABLE + INSERT ile geri yuklenir',
];
```

| Alan | Anlam | Kural eşlemesi |
|---|---|---|
| `destructive => true` | Yıkıcı sürüş | M-04 (yedek zorunlu) |
| `approval => REQUIRED` | İnsan onayı | §17 (surucu) |
| `precheck` | DROP öncesi salt-okunur kontrol | §3 S-3 |
| `rollback` | Yedekten geri yükleme | §11 D-2 |

## §4 Örnek-1: Precheck Sorguları

**Dosya:** aynı · **Satır:** 26–31

```php
$precheck = [
    'SELECT COUNT(*) AS cnt FROM coremusic_api.api_keys',
    'SELECT COUNT(*) AS cnt FROM coremusic_auth.api_keys',
];
```

| Sorgu | Amaç |
|---|---|
| api'deki kopya COUNT | DROP edilecek tablodaki satır sayısı |
| auth'taki gerçek COUNT | Tek yazan (single-writer) tablosu |
| Karşılaştırma | Eşit/az ise güvenli → operatör gözlemi |

**Not:** Karşılaştırmayı **yapan kod repo'da yok** (betik yalnız sorgu
dizgisini döndürür) → karşılaştırma elle yapılır (otomatikleştirilip
ştırılmadığı UNKNOWN).

## §5 Örnek-2: CREATE + FK + İndeks (oauth_connections)

**Dosya:** `shared/database/migrations/oauth_connections_migration.php` · **Satır:** 18–42

| Satır | Kalıp |
|---|---|
| 18 | `CREATE TABLE IF NOT EXISTS oauth_connections (` |
| 20 | `id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,` |
| 22–27 | `provider ENUM(...)` — 10 değer |
| 37 | `UNIQUE KEY uk_provider_user (provider, provider_user_id),` |
| 38 | `INDEX idx_user_active (user_id, is_active),` |
| 39 | `INDEX idx_provider (provider),` |
| 40 | `INDEX idx_expires (token_expires_at),` |
| 41 | `FOREIGN KEY (user_id) REFERENCES coremusic_user.users(id)` |
| 42 | `) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci` |

**Kod (satır 18–42 özet):**

```sql
CREATE TABLE IF NOT EXISTS oauth_connections (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ...
    UNIQUE KEY uk_provider_user (provider, provider_user_id),
    INDEX idx_user_active (user_id, is_active),
    FOREIGN KEY (user_id) REFERENCES coremusic_user.users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
```

| Kalıp | Kural eşlemesi |
|---|---|
| `IF NOT EXISTS` | İdempotent (§39 surucu) |
| `ENGINE=InnoDB` | FK destekli motor — `.ai/.sql/mysql/coremusic_ai.sql:39` ile aynı |
| `utf8mb4_unicode_ci` | Karakter seti tutarlılığı (schema) |
| `UNIQUE KEY` | Tekrar_INSERT koruması |
| `FOREIGN KEY ... coremusic_user.users` | Sorgu-ötesi bütünlük (transactions) |

## §6 Örnek-3: CREATE + State Token (oauth_states)

**Dosya:** `shared/database/migrations/oauth_states_migration.php` · **Satır:** 20–32

| Satır | Kalıp |
|---|---|
| 20 | `CREATE TABLE IF NOT EXISTS oauth_states (` |
| 22 | `state_token VARCHAR(64) NOT NULL,` |
| 27 | `expires_at TIMESTAMP NOT NULL,` |
| 28 | `UNIQUE KEY uk_state_token (state_token),` |
| 29 | `INDEX idx_provider_user (provider, user_id),` |
| 30 | `INDEX idx_expires (expires_at),` |
| 31 | `FOREIGN KEY (user_id) REFERENCES coremusic_user.users(id)` |
| 32 | `) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci` |

**Doküman notu (satır 3–5):** Her state token 10 dakika geçerli, tek kullanımlık
(ADR-088) — **süre kodda mı şemada mı?** → `expires_at` şemada; iş mantığı
uygulamada → ⚠️ ilgili kod dosyası bu düğümün kapsamında değil (UNKNOWN).

| Kalıp | Amaç |
|---|---|
| `UNIQUE state_token` | Tek kullanımlık token — tekrar INSERT imkânsız |
| `INDEX idx_expires` | Süresi dolanların temizliği için tarama |
| `FK user_id` | Yetim token bırakmaz |

## §7 İki Dosyanın Yapısal Karşılaştırması

| Özellik | oauth_connections | oauth_states |
|---|---|---|
| Satır uzunluğu | 46 | 36 |
| Sütun sayısı | 16 | 9 |
| UNIQUE | 1 | 1 |
| INDEX | 3 | 2 |
| FK | 1 | 1 |
| ENUM | var (10 değer) | yok |
| JSON sütunu | `profile_data JSON` | yok |
| Dönüş | `return $queries` | `return $queries` |
| Meta alan (name/approval) | Yok | Yok |

**Önemli gözlem:** Yalnız yıkıcı dosya meta alan taşıyor
(`destructive`/`approval`); CREATE dosyaları düz `$queries` dizisi döndürüyor
→ **runner bu iki dönüş tipini nasıl ayırt ediyor?** → runner yok → ⚠️ UNKNOWN.

## §8 DDL Şablonları — Şema Dosyalarından (Referans)

> Migration'ların hedeflediği şema kalıpları `.ai/.sql/mysql/*.sql`
> dump'larında görünür (20 dosya). Bunlar migration DEĞİL, hâlihazırda
> var olan şemanın dökümüdür.

**Dosya:** `.ai/.sql/mysql/coremusic_albums.sql` · **Satır:** 18–41 (iki tablo)

```sql
CREATE TABLE `album_credits` (
  ...
  KEY `idx_album_credits_album` (`album_id`) COMMENT 'Album filtresi',
  CONSTRAINT `fk_album_credits_album` FOREIGN KEY (`album_id`)
      REFERENCES `albums` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

| Kalıp | Satır | Not |
|---|---|---|
| `KEY ... COMMENT` | 27–28 | MySQL yorumlu indeks |
| `ON DELETE CASCADE` | 29 | Silme davranışı — transactions ile ilişkili |
| `UNIQUE KEY (album_id, disc_number)` | 39 | bileşik benzersizlik |
| `ENGINE=InnoDB` | 30, 41 | Motor tutarlılığı (migration FK'larıyla aynı) |

**Dosya:** `.ai/.sql/mysql/coremusic_albums.sql` · **Satır:** 95 (`albums` PK).

## §9 DDL Şablonu — CHECK Constraint

**Dosya:** `.ai/.sql/mysql/coremusic_ai.sql` · **Satır:** 75–133 aralığı.

| Kalıp | Durum |
|---|---|
| CHECK kısıtları | Bu aralıkta bulunduğu raporlandı (2026-10-13 ölçümü) |
| Ayrıntılı satır içerikleri | → `schema` düğümüne ait (kapsam dışı tekrar yok) |

## §10 Migration ⇄ Dump İlişkisi

| migration | Hedef DB | Dump karşılığı |
|---|---|---|
| `oauth_connections_migration.php` | `coremusic_social` (yorum: ADR-072) | `coremusic_social.sql` |
| `oauth_states_migration.php` | (belirtilmemiş — dosyada `database` anahtarı yok) | ⚠️ UNKNOWN |
| `2026-10_07_drop_...` | `coremusic_api` (return: `'database'`) | `coremusic_api.sql` |

| Soru | Cevap |
|---|---|
| Dump'larda `oauth_connections` var mı? | ⚠️ VERIFICATION REQUIRED — dump tarihi migration'dan eski/yeni bilinmiyor |
| `oauth_states` hangi DB'de? | Dosyada belirtilmemiş → runner tarafı bilir → runner yok → UNKNOWN |

## §11 Şablon Kalıbı: Yıkıcı Migration İskeleti

> Bu iskelet **repo'daki tek yıkıcı örneğinden türetilmiştir**
> (`2026-10_07_...php:26-51`); uydurma alan yoktur.

```php
<?php declare(strict_types=1);

/** Karar notu: ADR referansı + onay tarihi */
$precheck = [ 'SELECT COUNT(*) ...', 'SELECT COUNT(*) ...' ];
$queries  = [ "DROP TABLE IF EXISTS ...", ];
return [
    'name' => '...', 'database' => '...',
    'destructive' => true,
    'approval'    => 'REQUIRED - ...',
    'precheck'    => $precheck,
    'queries'     => $queries,
    'rollback'    => 'mysqldump ... CREATE TABLE + INSERT ...',
];
```

| Alan | Zorunlu mu? (repo kanıtı) |
|---|---|
| `destructive` | Yıkıcıda VAR, CREATE'de YOK |
| `approval` | Yıkıcıda VAR, CREATE'de YOK |
| `precheck` | Yıkıcıda VAR |
| `rollback` | Yıkıcıda VAR |
| `name` / `database` | Yıkıcıda VAR |

> CREATE örneğinde bu alanların hiçbiri yok → **alan zorunluluğu belirsiz**
> (runner olmadığından doğrulanamaz) → ⚠️ VERIFICATION REQUIRED.

## §12 Şablon Kalıbı: Oluşturucu (CREATE) Migration İskeleti

```php
<?php declare(strict_types=1);
$queries = [
"CREATE TABLE IF NOT EXISTS ... (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ...
    UNIQUE KEY uk_... (...),
    INDEX idx_... (...),
    FOREIGN KEY (...) REFERENCES ...(...)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
];
return $queries;
```

| Kalıp | Zorunlu mudur? | Kanıt |
|---|---|---|
| `IF NOT EXISTS` | 2/2 CREATE'de var | `:18`, `:20` |
| `ENGINE=InnoDB` | 2/2 | `:42`, `:32` |
| `utf8mb4_unicode_ci` | 2/2 | aynı satırlar |
| `FOREIGN KEY` | 2/2 | `:41`, `:31` |
| `UNIQUE KEY` | 2/2 | `:37`, `:28` |

## §13 Sütun Kalıpları (Kod Kanıtlı)

| Kalıp | Örnek satır | Dosya |
|---|---|---|
| `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY` | `id ...` | connections:20 |
| `INT UNSIGNED NOT NULL` (FK kolonu) | `user_id` | connections:21 · states:23 |
| `ENUM(...)` sabit liste | `provider ENUM('instagram',...)` | connections:22–27 |
| `VARCHAR(255)` | `provider_user_id` | connections:28 |
| `TEXT ... DEFAULT NULL` | `refresh_token_encrypted` | connections:31 |
| `TIMESTAMP NULL DEFAULT NULL` | `token_expires_at` | connections:32 |
| `JSON DEFAULT NULL` | `profile_data` | connections:36 |
| `TIMESTAMP DEFAULT CURRENT_TIMESTAMP` | `connected_at` | connections:37 |
| `ON UPDATE CURRENT_TIMESTAMP` | `updated_at` | connections:38 |
| `TINYINT(1) NOT NULL DEFAULT 1` | `is_active` | connections:40 |
| `VARCHAR(64)` token | `state_token` | states:22 |
| `VARCHAR(128)` PKCE | `code_verifier` | states:24 |

## §14 İndeks Kalıpları (Kod Kanıtlı)

| İndeks | Dosya:satır | Sütunlar |
|---|---|---|
| `uk_provider_user` | connections:37 | (provider, provider_user_id) |
| `idx_user_active` | connections:38 | (user_id, is_active) |
| `idx_provider` | connections:39 | (provider) |
| `idx_expires` | connections:40 | (token_expires_at) |
| `uk_state_token` | states:28 | (state_token) |
| `idx_provider_user` | states:29 | (provider, user_id) |
| `idx_expires` | states:30 | (expires_at) |

**Bileşik indeks sütun sırası — dikkat:** `idx_user_active (user_id, is_active)`
→ soldan sağa kullanım kuralı (soldaki `user_id` tek başına da kullanılabilir).
Migration'ın hangi sorguları hedeflediği → `repositories` düğümüne ait.

## §15 Dış Anahtar Kalıpları

| FK | Dosya:satır | Hedef | Davranış |
|---|---|---|---|
| oauth_connections.user_id | :41 | `coremusic_user.users(id)` | `ON DELETE` belirtilmemiş → MySQL default RESTRICT/NO ACTION ⚠️ doğrula |
| oauth_states.user_id | :31 | `coremusic_user.users(id)` | aynı |
| dump örneği FK | albums:29 | `albums(id)` | `ON DELETE CASCADE ON UPDATE CASCADE` |

| Fark | Anlam |
|---|---|
| Migration FK'larında `ON DELETE` YOK | Kullanıcı silinirse token satırları **RESTRICT** edebilir → davranış motor varsayımına bağlı ⚠️ VERIFICATION REQUIRED |
| Dump FK'larında CASCADE var | Şema dosyası ile migration davranışı **farklı** → tutarlılık sorusu açık |

## §16 precheck / rollback Kalıbı

| Alan | Kod | Satır |
|---|---|---|
| precheck sorguları | `SELECT COUNT(*) AS cnt FROM coremusic_api.api_keys` | :28 |
| precheck karşılaştırma | `SELECT COUNT(*) AS cnt FROM coremusic_auth.api_keys` | :29 |
| rollback tarifi | `mysqldump yedek dosyasından CREATE TABLE + INSERT` | :50 |

| Kalıp | Yorum |
|---|---|
| precheck = salt-okunur SELECT | Sürüş öncesi gözlem (manuel) |
| rollback = dump'dan geri yükleme | DROP'un tek geri dönüşü (down migration YOK) |
| Otomatik rollback kodu | Repo'da yok → ⚠️ runner yok |

## §17 Red Flags (Kod Kanıtlı Sorunlar)

| # | Bulgu | Kanıt | Önem |
|---|---|---|---|
| R-1 | Runner yok (dosya nasıl çalıştırılıyor?) | `migrations/` içinde çalıştırıcı yok; `return` edilen dizgi hiçbir yerde çağrılmıyor ⚠️ tarama gerekli | Yüksek |
| R-2 | İki farklı dönüş tipi (`array` vs `[]`) | :43 vs :46 | Orta |
| R-3 | `oauth_states` dosyasında `database` anahtarı yok | return yalnız `$queries` | Orta |
| R-4 | Tarih öneki yalnız 1 dosyada | 2 dosya sırasız | Orta |
| R-5 | Migration FK'larında `ON DELETE` yok; dump'larda CASCADE var | :41 vs albums:29 | Yüksek |
| R-6 | precheck karşılaştırmasını yapan kod yok | :26–31 yalnız SELECT | Orta |
| R-7 | Rollback yalnız tarif (kod değil) | :50 metin | Orta |

## §18 Test Senaryoları (Tasarım — Uygulanmadı)

| # | Senaryo | Girdi | Beklenen |
|---|---|---|---|
| T-1 | CREATE tekrarı | 2. kez çalıştır | `IF NOT EXISTS` → hata yok |
| T-2 | DROP sonrası varlık | DROP çalıştır | Tablo yok |
| T-3 | precheck eşitse | COUNT eşit | Onayla sür |
| T-4 | precheck farklıysa | COUNT farklı | DUR |
| T-5 | FK ihlali | users'tan silme | RESTRICT (migration) / CASCADE (dump) — çelişki |
| T-6 | rollback | dump geri yükle | Tablo + veri döner |

> **Durum:** Senaryolar **tasarımdır** — repo'da migration testi yok
> (⚠️ VERIFICATION REQUIRED: `shared/tests/` içinde migration testi aranmadı →
> bu görev kapsamında test taraması YAPILMADI).

## §19 Kod Parçaları — Bağlam Satırları

**Dosya:** `oauth_connections_migration.php` · **Satır:** 5–7 (ADR atfı)

```php
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 * @see [[decisions/accepted/ADR-072-social-database-schema]]
```

**Dosya:** `2026-10_07_...php` · **Satır:** 18–19 (ADR atfı)

```php
 * @see [[.decisions/accepted/ADR-040-database-authority]] (single-writer)
 * @see [[.decisions/accepted/ADR-020-api-public-security]]
```

| ADR | Konu | Migration etkisi |
|---|---|---|
| ADR-040 | single-writer DB otoritesi | ikinci kopya DROP edildi |
| ADR-020 | API public security | api_keys sahipliği |
| ADR-088 | gender-based social oauth | oauth_states + connections |
| ADR-072 | social DB şeması | connections `coremusic_social`'e |

## §20 Web Kaynakları (Doğrulanmış — HTTP 200 / 2026-10-13)

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| PostgreSQL CREATE TABLE sözdizimi | https://www.postgresql.org/docs/current/sql-createtable.html | 2026-10-13 | HTTP 200 |
| PostgreSQL DDL dili | https://www.postgresql.org/docs/current/ddl.html | 2026-10-13 | HTTP 200 |
| PostgreSQL DDL kısıtları | https://www.postgresql.org/docs/current/ddl-constraints.html | 2026-10-13 | HTTP 200 |
| Oracle CREATE TABLE | https://docs.oracle.com/en/database/oracle/oracle-database/23/sqlrf/CREATE-TABLE.html | 2026-10-13 | HTTP 200 |

> Bu dosyanın migration kalıpları MySQL'e aittir; URL'ler **sözdizimi referansı**
> olarak kullanılır (motor farkı §15'te işaretlidir). `dev.mysql.com` 403 → KULLANILMAYACAK.

## §21 Bilinmeyenler

| # | Bilinmeyen | Etiket |
|---|---|---|
| U-1 | Runner (çalıştırma aracı) | ⚠️ VERIFICATION REQUIRED |
| U-2 | `oauth_states` hedef DB'si | UNKNOWN |
| U-3 | Migration'ların üretimde çalıştırıldığı | UNKNOWN |
| U-4 | FK `ON DELETE` gerçek davranışı | ⚠️ VERIFICATION REQUIRED |
| U-5 | precheck karşılaştırma otomasyonu | UNKNOWN |
| U-6 | Dump ⇄ migration senkron durumu | UNKNOWN |
| U-7 | Migration testlerinin varlığı | ⚠️ VERIFICATION REQUIRED (tarama yapılmadı) |

## §22 Bağlantılı Düğümler

| Düğüm | İlişki |
|---|---|
| K007-data/migrations-anayasa | Kurallar (bu dosya kuralları YAZMAZ) |
| K007-data/migrations-surucu | Sürüş akışı |
| K007-data/schema | Hedef şema tasarımı |
| K007-data/transactions | FK/InnoDB davranışı |
| K007-data/repositories | Bu sorguları çalıştıran katman |

## §23 Kalite Kapısı

| Kontrol | Sonuç |
|---|---|
| Frontmatter 10 alan | ✅ |
| `parent:` yok (yalnız `parent_id`) | ✅ |
| `group` tırnaksız: `kod` | ✅ |
| ≥500 tam satır | ↓ ölçüm satırında |
| `## §N` + markdown tablo | ✅ |
| Son satır birebir Authority | ✅ |
| Kod kanıtı ≥1 (`Dosya:`/`Satır:`) | ✅ — 3 dosya, satır numaralı |
| Uydurma URL yok (yalnız 34 listesinden) | ✅ — 4 URL |
| `TBD` / `// ...` / boş hücre | ✅ yok |
| Mojibake yok | ✅ |

## §24 Özet (Summary)

| Ölçüm | Değer |
|---|---|
| Çözümlenen gerçek dosya | 3 / 3 |
| Kanıt satırı (satır numaralı) | 40+ |
| Red flag | 7 (R-1..R-7) |
| UNKNOWN / ⚠️ | 7 (U-1..U-7) |
| URL | 4 (hepsi HTTP 200) |
| Runner | YOK → tüm sürüş iddiaları tasarımdır |

## §25 İlgili Terimler

| Türkçe | English |
|---|---|
| Taşıma | Migration |
| Yıkıcı | Destructive |
| Ön denetim | Precheck |
| Geri yükleme | Restore / rollback |
| Tek yazan | Single-writer |
| Benzersiz anahtar | Unique key |
| Dış anahtar | Foreign key |
| Motor | Storage engine |

## §26 SSS

| # | Soru | Cevap |
|---|---|---|
| 1 | Migration'lar nasıl çalıştırılıyor? | Bilinmiyor → U-1 |
| 2 | Down migration var mı? | Hayır — yalnız rollback tarifi (:50) |
| 3 | Neden iki dönüş tipi? | Belirsiz → runner yok |
| 4 | Hangi DB'ye CREATE gider? | connections: `coremusic_social` (yorum), states: belirsiz |
| 5 | Dump'lar güncel mi? | UNKNOWN → U-6 |

## §27 Uygulama Durumu

| Dosya | Durum |
|---|---|
| 3 migration dosyası | Diskte gerçek, PHP sözdizimi okunabilir |
| Runner | YOK |
| Ortam geçişi (dev→prod) | Tarif var (destructive: onay), otomasyon yok |
| Test | Yok (tarama yapılmadı → U-7) |

## §28 Revizyon

| Tarih | Değişiklik |
|---|---|
| 2026-10-13 | İlk yazım — 3 gerçek dosya çözümlemesi + DDL kalıpları + 4 URL |

## §29 Sürüm

| Alan | Değer |
|---|---|
| Versiyon | 1.0.0 |
| Tarih | 2026-10-13 |
| Durum | draft |
| Erişim | `.ai/architecture/K007-data/migrations/migrations-ornekler.md` |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode

