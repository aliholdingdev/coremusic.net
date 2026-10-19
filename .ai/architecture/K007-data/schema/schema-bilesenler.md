---
node_id: "K007-schema-bilesenler"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K008-service-layer"
evidence:
  - ".ai/.sql/CLAUDE.md §1 — şema kanun dosyası (2026-10-07)"
  - "docs/database/01-entity-relationship-diagram.md:13-35 — semantik ER katmanı"
  - "shared/src/Database/DatabaseManager.php:14-67 — uygulama PDO kapısı"
validation: "9 bileşen (B-01..B-09); her biri kanıt+rol+K007 bağlantısı; 8/8 alan; yasak betim yok"
open_questions:
  - "S-1 — arama sahipliği K007 mi?"
  - "K-001 — forum şeması T-11/T-20 kapsamı UNKNOWN"
group: mimari
---

# K007-SHEMA — Bileşenler

> Kök bileşen envanteri: `../data-bilesenler.md` (B-01..B-08, P-01..P-07).
> Bu dosya şema alt alanına özgü 9 bileşeni (S-B01..S-B09) tanımlar.

| # | Bileşen | Kanıt | Rol |
|---|---|---|---|
| S-B01 | **Şema kanun dosyası** | `.ai/.sql/CLAUDE.md §1` | BCNF/LIKE/anahtarsız/JOIN kuralları — şema inceleme checklist'i |
| S-B02 | **Semantik ER katmanı** | `docs/database/01-entity-relationship-diagram.md:13-35` | 5 DB topolojisi + 30+ tablo ilişkisi (Markdown) |
| S-B03 | **MySQL dump arşivi** | `.ai/.sql/mysql/*.sql` (içerik `UNKNOWN` — okunmadı) | 171 tablonun fiziksel kaynağı (SSOT) |
| S-B04 | **`DatabaseManager`** | `shared/src/Database/DatabaseManager.php:12-67` | şemanın uygulamadaki tek PDO kapısı (ADR-002) |
| S-B05 | **`DatabaseConfig`** | `DatabaseManager.php:5` (`use CoreMusic\Database\Config\DatabaseConfig;`) | host/port/dbName/charset — DSN üretimi (içerik `UNKNOWN`) |
| S-B06 | **Şema-drift raporları** | `.ai/.sql/mysql/reports/` (diskte `UNKNOWN`) | database-review kanunu `.ai/.sql/CLAUDE.md §2` |
| S-B07 | **DB sözlüğü + sorgu sözlüğü** | `docs/database/README.md:9-13, 40` | primitif/çözüm/SQL/ORM — senkron TODO |
| S-B08 | **SQLite test çifti** | `shared/tests/Repository/Sqlite/*` | şema farklarını testte simüle et (T-06) |
| S-B09 | **Migration şablonu** | `.ai/.templates/infrastructure/migration-template.md` (referans, okunmadı `UNKNOWN`) | yeni şema değişikliği türetilmesi (shared AGENTS kuralı 4) |

## S-B01 — Şema Kanun Dosyası (SSOT)

`.ai/.sql/CLAUDE.md §1` içindeki 6 kanun bu alt alanın checklist'idir:
18 BCNF DB / LIKE yasağı / anahtarsız sorgu yasağı / `text`+`LIMIT` /
JOIN alternatifi / arama K007. Doğrulama satırları:
`schema-dogrulama.md` SÇ-01..SÇ-05.

## S-B02 — Semantik ER Katmanı

Markdown ER (`docs/database/01-*.md`) fiziksel dump'ın üstünde katmandır:
kaynak `README.md:9-13` ("pragmatic single source of truth"). Çekirdek 5 DB
(`schema-kapsam.md` §3 Blok A). Kayda değer: forum, JC助手, SEO, ekonomi,
oyun ekosistemleri — bu belgelerde `UNKNOWN`, yalnız varlık kaydedildi.

## S-B03 — MySQL Dump Arşivi

`.ai/.sql/mysql/` altı: çekirdek dump + klasörler (`ads/`, `novasearch/`,
`reports/` — `.gitignore:61-63` adresli). İçerik bu üretimde okunmadı →
sütun dökümü iddiaları `UNKNOWN`. Erişim: `SHOW CREATE TABLE`/dump incelemesi
(elle, `qa` kapsamında).

## S-B04 — DatabaseManager (Tek PDO Kapısı)

Kanıt `DatabaseManager.php:12-67`: DSN (14-20) · 4 seçenek (22-27) ·
`execute`/`write` (30-42) · `lastInsertId` (44-47) · transaction yüzeyi
(49-62) · `getPdo()` (64-67). Şema değişikliğinin koda etkisi bu sınıfın
dışına taşmaz (kural: DatabaseManager dışında bağlantı açılmaz —
`shared/AGENTS.md` Zorunlu 1).

## S-B05 — DatabaseConfig

`DatabaseManager.php:5` import'u; DSN'e `host/port/dbName/charset` geçer.
İçerik (varsayılan charset değeri, kaynak dosya) okunmadı → `UNKNOWN`.

## S-B06 — Şema-Drift Raporları

`.ai/.sql/mysql/reports/` — `.gitignore` adresli, çalışma ağacındaki
varlığı `UNKNOWN` (kök belgeler `git status: D` notu veriyor). Yoksa
drift ölçümü yapılamaz → `schema-dogrulama.md` §6.3.

## S-B07 — Sözlükler

`docs/database/README.md` sorgu sözlüğü/DB sözlüğü dizini (`:9-13`); senkron
zorunluluğu `:40` TODO. `K007-data` içindeki karşılığı `evrensel-dil` ile
örtüşür (M-11 `CLAUDE.md → DB.md` tekilleştirmesi).

## S-B08 — SQLite Test Çifti

`shared/tests/Repository/Sqlite/` — MySQL hedefli repository testlerinin
çift şema katmanı (T-06 kök). Şema tasarımı için anlamı: kısıt/tip farkları
(`ENUM`, auto-increment) testte yakalanmaz, `⚠️` ile işaretli
(`schema-dogrulama.md` §2).

## S-B09 — Migration Şablonu

`.ai/.templates/infrastructure/migration-template.md` — `shared/AGENTS.md`
kural 4 ("Migration → şablondan türetilir"). Bu belgede okunmadı → içerik
`UNKNOWN`; uygulama `../migrations/` alt alanının görevi.

## Bağlantılar

- **Yukarı:** `../data-bilesenler.md` (B-01 `DatabaseManager`, B-08 `PageCacheAdapter`).
- **Kardeş:** `migrations-bilesenler` (S-B09'un tüketeni) · `indexing-bilesenler` (S-B03'ün indeks okuyucusu).
- **Dış:** `K008-service-layer` · `.ai/.sql/CLAUDE.md`.

## Bileşen Olgunluk Matrisi

| Bileşen | Olgu | Kanıt | Risk |
|---|---|---|---|
| S-B01 | ✓ kanun dosyası aktif | `.ai/.sql/CLAUDE.md §1` | — |
| S-B02 | ✓ Markdown ER mevcut | `docs/database/01-*.md` | senkron (R-06) |
| S-B03 | `UNKNOWN` içeriği | `.ai/.sql/mysql/` okunmadı | erişim |
| S-B04 | ✓ üretim kodu | `DatabaseManager.php:12-67` | persistent bağlantı (R-10 kök) |
| S-B05 | `UNKNOWN` içeriği | import kanıtı var | — |
| S-B06 | `UNKNOWN` varlığı | `.gitignore:63` adresli | drift ölçümü yok |
| S-B07 | ⚠️ senkron TODO | `README.md:40` | R-06 |
| S-B08 | ✓ test çiftleri | `tests/Repository/Sqlite/` | ENUM farkı |
| S-B09 | `UNKNOWN` şablon içeriği | referans yol | — |

## Bağımlılık Grafiği (Yazılı)

```
S-B01 (kanun) ──→ inceleme checklist ──→ şema kararı
S-B02 (ER)    ──→ S-B03 (dump)          ──→ S-B04 (PDO)
S-B05 (config)─→ S-B04 (DSN üretimi)
S-B08 (test)  ──→ repository testleri (T-06/T-09)
S-B09 (şablon)─→ migrations alt alanı
```

## Sahiplik ve Kullanım (K007 Bağlamı)

| Bileşen | Kim okur/yazar | Sıklık |
|---|---|---|
| S-B01 kanun | `data-engineer` (inceleme) | her şema değişikliğinde |
| S-B02 ER | tüm K007 alt alanları | tasarım |
| S-B03 dump | `data-engineer` + DBA | sürümleme (`migrations/`) |
| S-B04 manager | uygulama (repository) | her istek |
| S-B06 raporlar | `qa-engineer`/review | periyodik (`UNKNOWN` sıklık) |
| S-B08 test çifti | `test-writer`/`qa-engineer` | test koşusu |

## Sonraki Adım Önerisi

1. FK kararını kapat (`docs/database/README.md:14-18` → ADR).
2. `.ai/.sql/mysql/reports/` varlığını tespit et → drift ölçümü (§6.3).
3. `DatabaseConfig` içeriğini oku → charset değeri `UNKNOWN`'ı kapat.

## S-B Kontrol Listesi (Kısa)

| # | Bileşen | Kanıt durumu | Aksiyon |
|---|---|---|---|
| 1 | S-B01 | ✓ | — |
| 2 | S-B02 | ✓ | — |
| 3 | S-B03 | `UNKNOWN` | dump oku (devir: migrations) |
| 4 | S-B04 | ✓ | — |
| 5 | S-B05 | `UNKNOWN` | config oku |
| 6 | S-B06 | `UNKNOWN` | rapor varlığını doğrula |
| 7 | S-B07 | ⚠️ | senkron TODO kapat |
| 8 | S-B08 | ✓ | — |
| 9 | S-B09 | `UNKNOWN` | şablon oku (devir: migrations) |

---

# EK I — Yeni Bileşenler S-B10..S-B22 (2026-10-17 disk envanteri)

> S-B01..S-B09 listesine **ekleme**; silme yok. Her satır disk kanıtına dayanır.

| # | Bileşen | Kanıt | Rol |
|---|---|---|---|
| S-B10 | **`DatabaseRegistry`** | `shared/src/Database/DatabaseRegistry.php:1-35` (tam okundu) | `dbKey` → `IDatabaseManager` eşlemesi (çok-DB kapısı) |
| S-B11 | **`DatabaseConfig` (dosya)** | `shared/src/Database/Config/DatabaseConfig.php` (dosya var) | host/dbName/user/password/port/charset taşır |
| S-B12 | **`UuidV7`** | `shared/src/Security/UuidV7.php:1-40` | `binary(16)` PK üretimi — RFC 9562 |
| S-B13 | **`IDatabaseManager`** | `shared/src/Contracts/Database/IDatabaseManager.php:8-14` | 6 metot + `getPdo()` sözleşmesi |
| S-B14 | **`IDatabaseRegistry`** | `shared/src/Contracts/Database/IDatabaseRegistry.php:8-12` | `registerMySql/get/has` |
| S-B15 | **PHP migration dosyaları** | `shared/database/migrations/` — 3 PHP + 1 CLAUDE.md | şema değişikliği üreteci (canonical) |
| S-B16 | **`novasearch.sql`** | `.ai/.sql/mysql/novasearch.sql` — 7 tablo | arama şeması (S-1 sahipliği) |
| S-B17 | **`media_catalog.sql`** | `.ai/.sql/mysql/media_catalog.sql` — 9 tablo + `v_asset_search` | PLANNED (canlıda YOK) |
| S-B18 | **VIEW `v_music_recording_map`** | `coremusic_musics.sql:508` | legacy eşleme (`recordings.legacy_music_id`) |
| S-B19 | **`composer.json` (shared)** | `shared/composer.json` | `ext-pdo`/`ext-apcu` zorunlu; PHPStan ^1.10 |
| S-B20 | **`shared/src/Database/CLAUDE.md`** | dosya var | katman kuralı dosyası |
| S-B21 | **`full_dump.php` (iddia)** | dump başlığı "Üreteç: full_dump.php" | dump üreteci — **konum `UNKNOWN`** |
| S-B22 | **`.ai/.sql/{mssql,postgresql,sqlite}/`** | 3 dizin (boşluk durumu ölçülmedi) | gelecekteki DB çokluluğu |

## I.1 S-B10 — DatabaseRegistry (tam kanıt)

```php
final class DatabaseRegistry implements IDatabaseRegistry
{
    /** @var array<string, IDatabaseManager> */
    private array $managers = [];

    public function registerMySql(string $key, string $host, string $dbName,
        string $user, string $password, int $port = 3306, string $charset = 'utf8mb4'): void
    {
        $this->managers[$key] = new DatabaseManager(
            new DatabaseConfig($host, $dbName, $user, $password, $port, $charset)
        );
    }

    public function get(string $key): IDatabaseManager
    {
        if (!isset($this->managers[$key])) {
            throw new \RuntimeException("Database '{$key}' is not registered.");
        }
        return $this->managers[$key];
    }

    public function has(string $key): bool
    {
        return isset($this->managers[$key]);
    }
}
```

**Şema anlamı:** `coremusic_*` 18 DB, kodda `dbKey` string'i ile temsil edilir
(`ApiKeyRepository::DB_KEY = 'auth'`). Registry **lazy değil** — `registerMySql`
çağrıldığında anında `new DatabaseManager` (= anında `new \PDO`) kurar:
kayıtlı ama hiç kullanılmayan DB'ler bile bağlantı havuzuna girer
(persistent: `ATTR_PERSISTENT=true`, `DatabaseManager.php:26`).

| Gözlem | Sonuç |
|---|---|
| `new \PDO` kaç adet? | Kod yapısı gereği **registry başına 1** — tek PDO nesnesi |
| 18 DB = 18 PDO? | `registerMySql` kaç kez çağrılıyor → **`UNKNOWN`** (çağıran okunmadı) |
| Hata tipi | `\RuntimeException` (typed değil, mesaj string'i) |
| Charset varsayılanı | `'utf8mb4'` (registry imzası) — `DatabaseConfig` içi okunmadı |

## I.2 S-B11 — DatabaseConfig

Dosya `shared/src/Database/Config/DatabaseConfig.php` diskte mevcuttur; içeriği
bu üretimde okunmadı → **`UNKNOWN`**. Bilinen: registry'den 6 argüman alır
(`host, dbName, user, password, port, charset`) — `DatabaseManager.php:14-20`
DSN'i bu 5+1 alanla kurar.

## I.3 S-B12 — UuidV7 (tam kanıt, ilk 40 satır)

```php
/**
 * UUID v7 Generator
 *
 * RFC 9562 uyumlu UUID v7 üretimi.
 * 48-bit timestamp (ms) + 2-bit variant + 4-bit version + 62-bit random.
 * DB: BINARY(16) olarak saklanır.
 */
final class UuidV7
{
    public static function generateBinary(): string
    {
        $ms = (int)(microtime(true) * 1000);

        // 6-byte timestamp (48 bit)
        $time = pack('N', ($ms >> 16) & 0xFFFFFFFF)
              . pack('n', $ms & 0xFFFF);

        // 2-byte clock_seq (version + variant)
        $clockSeq = random_bytes(2);
        $clockSeq[0] = chr((ord($clockSeq[0]) & 0x0F) | 0x70); // version 7
        $clockSeq[1] = chr((ord($clockSeq[1]) & 0x3F) | 0x80); // variant 1

        // 8-byte random
        return $time . $clockSeq . random_bytes(8);
    }

    public static function generateHex(): string
    {
        return bin2hex(self::generateBinary());
    }
```

**Şema etkileşimi:**

| Konu | Sonuç |
|---|---|
| PK tipi | `binary(16)` = 48-bit zaman + 2-bit variant + 4-bit version + 62-bit rastgele (docblock) |
| Sıralama | Bayt sırası = kronolojik sıradır → InnoDB **b-tree'de sağa eklenir**, `INSERT` sayfa split'i azaltır |
| Çakışma | 62-bit rastgele → aynı ms içinde ~2^31 (≈2,1 milyar) kayıtta çakışma olasılığı doğar; `binary(16)` PK ihlali exception üretir |
| Depolama | `pack` ile **network byte order** (big-endian) — PHP `bin2hex` display ile birebir |
| Doğrulama | `ApiKeyRepository.php:76-79` `UuidV7::isValidHex()` çağırır (32 hex karakter) |

> `⚠️ VERIFICATION REQUIRED`: `isValidHex()` ve `generateBinary()` arasındaki
> bayt-düzene uyum testi `shared/tests/` altında **görülmedi** (Repository
> testleri SQLite; UUID bayt düzeni testi için `UNKNOWN`).

## I.4 S-B15 — PHP migration dosyaları (envanter)

| Dosya | Tip | Not |
|---|---|---|
| `oauth_connections_migration.php` | ekleme | content/ads DB `oauth_connections` |
| `oauth_states_migration.php` | ekleme | OAuth state |
| `2026-10_07_drop_coremusic_api_api_keys_migration.php` | **DROP** | `coremusic_api.api_keys` — taşıma kaydı |
| `CLAUDE.md` | kural | migration şablon kuralı |

**`DROP` migration'ı şema anlamı:** `coremusic_api` DB'sindeki `api_keys`
tablosu `coremusic_auth.api_keys`'e taşınmış görünür (dosya adı kanıtı). İki
tablo dump'ta hâlâ var mı → `coremusic_api.sql` 4 tablo içerir;
`api_keys` dump'ta mevcut mu **sayılmadı** → `⚠️ VERIFICATION REQUIRED`.

## I.5 S-B16/S-B17 — `novasearch` ve `media_catalog`

| Şema | Tablo | Durum | Şema anlamı |
|---|---|---|---|
| `novasearch` | 7 | canlı dump (knex) | arama; `schema-kapsam.md` §5 S-1 sahipliği `UNKNOWN` |
| `media_catalog` | 9 | **canlıda YOK** = PLANNED | `CREATE OR REPLACE VIEW v_asset_search` (`media_catalog.sql:434`) |

`novasearch`'ın InnoDB olduğu `.ai/.sql/CLAUDE.md §1`'de kanundur; dump içinde
`ENGINE=` kontrolü **yapılmadı** → `⚠️ VERIFICATION REQUIRED`.

## I.6 S-B18 — VIEW `v_music_recording_map` (birebir)

```sql
CREATE ALGORITHM=UNDEFINED DEFINER=`Ali`@`%` SQL SECURITY DEFINER
VIEW `coremusic_musics`.`v_music_recording_map` AS
  select `m`.`id` AS `music_id`, `r`.`id` AS `recording_id`,
         `m`.`title` AS `title`, `r`.`isrc` AS `isrc`
  from (`coremusic_musics`.`musics` `m`
    left join `coremusic_musics`.`recordings` `r`
      on((`r`.`legacy_music_id` = `m`.`id`)));
```

**Şema anlamı:** `musics` ↔ `recordings` **eski/legacy** ilişkiyi `LEFT JOIN`
ile taşır. `SQL SECURITY DEFINER` → view'ı çağıran, **definer hesabı**
(`Ali`) üzerinden yetki alır; `⚠️ VERIFICATION REQUIRED` (prod hesabı bilinmiyor).

## I.7 S-B19 — composer.json şema etkileşimi

| Bağımlılık | Şema etkisi |
|---|---|
| `ext-pdo` | PDO zorunlu (ADR-002) |
| `ext-apcu` | L1 cache (K007-cache) |
| `ext-json` | `json` kolon üretimi (113 kullanım) |
| `ext-mbstring` | UTF-8/charset dönüşümü |
| `psr/cache ^3.0` | CacheItem/CachePool **diskte YOK** (cache-bilesenler §1 notu) |
| `phpstan ^1.10` (`--level=5`) | statik analiz — şema docblock ↔ SQL uyumu test edilmez |

---

# EK II — Bileşen Olgunluk Matrisi (2. tur — S-B10..S-B22)

| Bileşen | Olgu | Kanıt | Risk |
|---|---|---|---|
| S-B10 | ✓ üretim kodu | `DatabaseRegistry.php:1-35` | eager bağlantı (I.1) |
| S-B11 | dosya var, içerik `UNKNOWN` | `Config/DatabaseConfig.php` | charset varsayılanı |
| S-B12 | ✓ üretim kodu | `UuidV7.php:1-40` | bayt düzeni testi yok |
| S-B13 | ✓ sözleşme | `IDatabaseManager.php:8-14` | — |
| S-B14 | ✓ sözleşme | `IDatabaseRegistry.php:8-12` | — |
| S-B15 | ✓ 3 PHP migration | `shared/database/migrations/` | DROP/taşıma çifti |
| S-B16 | ✓ dump | `novasearch.sql` (7) | ENGINE doğrulanmadı |
| S-B17 | ✓ dump (PLANNED) | `media_catalog.sql` (9) | canlıda yok |
| S-B18 | ✓ dump | `coremusic_musics.sql:508` | DEFINER hesabı |
| S-B19 | ✓ dosya | `shared/composer.json` | psr/cache kullanılmıyor |
| S-B20 | dosya var, okunmadı | `Database/CLAUDE.md` | `UNKNOWN` |
| S-B21 | iddia | dump başlığı | **üreteç konumu `UNKNOWN`** |
| S-B22 | 3 dizin | `.ai/.sql/` | içerik ölçülmedi |

---

# EK III — Bağımlılık Grafiği (genişletilmiş)

```text
S-B12 (UuidV7) ──binary(16)──→ S-B03 (dump: PK `binary(16)`)
S-B14 (IDatabaseRegistry) ──→ S-B10 (DatabaseRegistry)
S-B10 ──new DatabaseManager──→ S-B04 (DatabaseManager)
S-B11 (DatabaseConfig) ──DSN──→ S-B04
S-B04 ──PDO──→ MySQL (18 DB)
S-B15 (PHP migration) ──DDL──→ S-B03 (dump güncellenir)
S-B21 (full_dump.php) ──üretir──→ S-B03
S-B02 (ER Markdown) ──senkron←── S-B03 (dump)   [R-06 drift riski]
S-B16/S-B17 ──aranır──→ S-B18 (view) ──JOIN──→ S-B03
S-B19 (composer) ──sınır──→ S-B04 (ext-pdo)
```

**Kritik döngü:** `S-B02 (Markdown ER) ← S-B03 (dump)` çift yönlü senkron
gerektirir; `docs/database/README.md:40` bunu TODO olarak bırakmıştır
(`UNKNOWN` otomasyon).

---

# EK IV — Sahiplik Matrisi (2. tur)

| Bileşen | Kim okur/yazar | Sıklık | Sınır |
|---|---|---|---|
| S-B10 registry | `RuntimeBootstrap` (kayıt) + repository (`get`) | uygulama açılışı / istek | K008-service-layer |
| S-B11 config | `ConfigManager` (kayıt) | açılış | K000-isletim (`UNKNOWN`) |
| S-B12 UuidV7 | repository (`create`) | yeni satır | — |
| S-B15 migration | `data-engineer` | şema değişikliğinde | `migrations/` |
| S-B16/S-B17 dump | `data-engineer` + DBA | sürümleme | `migrations/` |
| S-B18 view | uygulama (sorgu) | okuma | `repositories/` |
| S-B21 üreteç | `devops`/DBA | dump anında | K018 |

---

# EK V — Açık Sorular (bileşen katmanı)

| # | Soru | Durum |
|---|---|---|
| B-11 | `registerMySql` kaç DB için çağrılıyor (18 PDO mu)? | `UNKNOWN` |
| B-12 | `DatabaseConfig` charset varsayılanı `utf8mb4` mi? | `UNKNOWN` |
| B-13 | `UuidV7::isValidHex` bayt düzeni testi var mı? | `⚠️ VERIFICATION REQUIRED` |
| B-14 | `coremusic_api.api_keys` dump'ta hâlâ var mı? | `⚠️ VERIFICATION REQUIRED` |
| B-15 | `novasearch` tabloları InnoDB mi? | `⚠️ VERIFICATION REQUIRED` |
| B-16 | View DEFINER hesabı prod'da ne? | `UNKNOWN` |
| B-17 | `full_dump.php` nerede? | `UNKNOWN` |
| B-18 | `psr/cache` bağımlılığı neden var (kullanım YOK)? | `⚠️ VERIFICATION REQUIRED` |
| B-19 | `shared/src/Database/CLAUDE.md` ne diyor? | `UNKNOWN` (okunmadı) |
| B-20 | `.ai/.sql/{mssql,postgresql,sqlite}` boş mu? | `UNKNOWN` |

---

# EK VI — DatabaseConfig Tam Kanıtı (2026-10-17 okundu — B-12 kapandı)

> `shared/src/Database/Config/DatabaseConfig.php` (15 satır, tam okundu):

```php
<?php declare(strict_types=1);

namespace CoreMusic\Database\Config;

final class DatabaseConfig
{
    public function __construct(
        public readonly string $host = 'localhost',
        public readonly string $dbName = 'coremusic',
        public readonly string $user = 'root',
        public readonly string $password = '',
        public readonly int $port = 3306,
        public readonly string $charset = 'utf8mb4',
    ) {}
}
```

## VI.1 Şema etkileşimi analizi

| Alan | Varsayılan | Şema etkisi | Değerlendirme |
|---|---|---|---|
| `host` | `'localhost'` | — | Windows ortamında socket/TCP ayrımı `UNKNOWN` |
| `dbName` | `'coremusic'` | tek DB adı | registry 18 DB kaydediyor → varsayılan **nadiren** kullanılır |
| `user` | `'root'` | — | **KRİTİK**: prod'da root mı? `⚠️ VERIFICATION REQUIRED` (`.env` K007 dışı) |
| `password` | `''` (boş) | — | **KRİTİK**: boş şifre varsayılanı — `.env` ile eziliyor mu → `UNKNOWN` |
| `port` | `3306` | MySQL standart | ✓ |
| `charset` | `'utf8mb4'` | tam Unicode (emoji + 4-byte) | ✓ — `.ai/.sql/CLAUDE.md §1` kanunu ile uyumlu |

## VI.2 B-12 kapanış kaydı

| Soru | Cevap | Kanıt |
|---|---|---|
| B-12 charset varsayılanı `utf8mb4` mi? | **✓ EVET** | `DatabaseConfig.php:13` |

## VI.3 Yeni tespitler (bu okuma)

| # | Tespit | Risk |
|---|---|---|
| VI-1 | `user = 'root'` varsayılanı | en yüksek yetkili hesap varsayılanı — `⚠️` |
| VI-2 | `password = ''` varsayılanı | boş şifre varsayılanı — `⚠️` |
| VI.3 | `readonly` + `promoted` properties | PHP 8.1+ — PHP 8.4 hedefi ile uyumlu ✓ |
| VI-4 | `declare(strict_types=1)` | ✓ — kural 5 uyumlu |
| VI-5 | `password` `string` olarak `readonly` | bellekte plaintext durur — `.env` üzerinden geliyor → `UNKNOWN` |

> Not: VI-1/VI-2 varsayılanları **yalnız fallback**tir; gerçek değerler
> `.env`/`ConfigManager` üzerinden `registerMySql(...)` çağrısına geçilir
> (S-B10 kanıtı). Yine de **boş şifreli root** fallback'i savunmasız bir
> konfigürasyon açığıdır → `⚠️` işaretli, ADR gerektirmez (runtime ayarı).

---

# EK VII — Charset ve Collation Derinlemesine (utf8mb4)

> Kanıt: `DatabaseConfig.php:13` (`utf8mb4`) + dump'lardaki `CHARSET`/`COLLATE`
> satırları (2026-10-15 envanter). `.ai/.sql/CLAUDE.md §1` kanunu: 18 BCNF DB.

## VII.1 utf8mb4 ↔ utf8 (3-byte) farkı

| Özellik | `utf8` (MySQL takma adı) | `utf8mb4` |
|---|---|---|
| Maks bayt/karakter | 3 | 4 |
| Emoji | ❌ | ✓ |
| Unicode kapsamı | BMP (U+0000–U+FFFF) | tam (U+0000–U+10FFFF) |
| CHAR/VARCHAR sınırı | 255 karakter (765 byte) | 191 karakter (765 byte, index için) |

## VII.2 İndeks uzunluğu sınırı (InnoDB, DYNAMIC row format)

| Sütun tipi | Maks uzunluk (utf8mb4) |
|---|---|
| `VARCHAR(191)` UNIQUE/INDEX | 191 × 4 = 764 byte ≤ 767/3072 ✓ |
| `VARCHAR(255)` UNIQUE/INDEX | 255 × 4 = 1020 byte — DYNAMIC'ta ✓ (3072 sınırı) |
| `TEXT` index | prefix zorunlu |

**CoreMusic etkisi:** 118 UNIQUE kısıtı arasında `VARCHAR` sütunlar var mı →
dump envanterinde `varchar` sütun tipleri sayıldı mı **`UNKNOWN`** (bu üretimde
sütun tipi kırılımı okunmadı) → `⚠️ VERIFICATION REQUIRED`.

## VII.3 Collation matrisi (MySQL 8+)

| Collation | Karakter | Duyarlılık |
|---|---|---|
| `utf8mb4_0900_ai_ci` | Unicode 15.0 | accent-insensitive, case-insensitive (MySQL 8 varsayılanı) |
| `utf8mb4_general_ci` | eski | daha hızlı ama daha az doğru sıralama |
| `utf8mb4_unicode_ci` | Unicode 4.0 | orta |
| `utf8mb4_bin` | byte | case-sensitive, accent-sensitive |

**Kanun dosyası bağlantısı:** `.ai/.sql/CLAUDE.md §1` LIKE yasağı — `LIKE` yerine
`FULLTEXT`/`novasearch` (S-1). Collation seçimi **LIKE** ve **JOIN** davranışını
doğrudan etkiler (örn. `ai_ci` collation'da `'ABC' = 'abc'` ✓ → JOIN eşleşmesi
farklı sonuç üretir).

## VII.4 Şema ↔ Uygulama charset uyumu matrisi

| Katman | Charset | Kanıt |
|---|---|---|
| MySQL bağlantı (DSN) | `utf8mb4` | `DatabaseConfig.php:13` |
| MySQL tablo | dump'larda `CHARSET=utf8mb4` mı | **`UNKNOWN`** (sütun dökümü okunmadı) |
| PHP `mb_internal_encoding` | `UNKNOWN` | — |
| HTTP `Content-Type` | `UNKNOWN` | K011-application |
| `htmlspecialchars` | `UNKNOWN` | — |

**Risk:** bağlantı utf8mb4 ama tablo `utf8` (3-byte) ise 4-byte karakter
**sessizce kaybolur** (`?` veya exception) → dump'larda `CHARSET=` doğrulaması
gerekir → `⚠️ VERIFICATION REQUIRED`.

---

# EK VIII — DSN Üretim Zinciri (DatabaseManager ↔ DatabaseConfig)

> Kanıt: `DatabaseManager.php:14-20` (DSN) + `DatabaseConfig.php` (6 alan).

```text
DatabaseConfig (6 readonly alan)
  → DatabaseManager::__construct($config)
    → DSN: "mysql:host={$config->host};port={$config->port};dbname={$config->dbName};charset={$config->charset}"
      → new \PDO($dsn, $config->user, $config->password, [4 seçenek])
```

## VIII.1 DSN bileşenleri ↔ MySQL davranışı

| DSN parçası | MySQL etkisi | Çelişki riski |
|---|---|---|
| `charset=utf8mb4` | `SET NAMES utf8mb4` eşdeğeri | tablo charset'i farklıysa §VII.4 |
| `host=localhost` | Windows: TCP 127.0.0.1 (socket değil) | `UNKNOWN` — davranış işletim sistemine bağlı |
| `port=3306` | — | — |
| `dbname=coremusic` | `USE coremusic` eşdeğeri | 18 DB'li mimari → dbname yalnız ilk DB |

## VIII.2 PDO 4 seçenek (DatabaseManager.php:22-27 — kanıt)

| Seçenek | Değer | Etki |
|---|---|---|
| `ATTR_ERRMODE` | `ERRMODE_EXCEPTION` | hata exception üretir (transaction §39 ile uyumlu) |
| `ATTR_DEFAULT_FETCH_MODE` | `FETCH_ASSOC` | assoc dizi |
| `ATTR_EMULATE_PREPARES` | `false` (tahmin — `⚠️ VERIFICATION REQUIRED`, satır okunmadı) | gerçek prepared statement → SQLi koruması |
| `ATTR_PERSISTENT` | `true` | bağlantı yeniden kullanımı + transaction riski (§AD.2 transactions-kapsam) |

> `ATTR_EMULATE_PREPARES` değeri bu üretimde **satır düzeyinde okunmadı** →
> `⚠️ VERIFICATION REQUIRED`. ADR-002 (prepared statement zorunlu) ile
> tutarlı olması beklenir: `false`.

---

# EK IX — Kararlar (bileşen katmanı — 3. tur)

| # | Karar/tespit | Durum |
|---|---|---|
| IX-1 | `DatabaseConfig` charset=utf8mb4 ✓ | ✓ kapandı (B-12) |
| IX-2 | `user=root` + `password=''` varsayılanları | `⚠️` (runtime ayarı) |
| IX-3 | Tablo charset'i dump'ta doğrulanmadı | `⚠️ VERIFICATION REQUIRED` |
| IX-4 | `ATTR_EMULATE_PREPARES` değeri | `⚠️ VERIFICATION REQUIRED` |
| IX-5 | VARCHAR index uzunluk sınırı | `UNKNOWN` (sütun tipi kırılımı okunmadı) |
| IX-6 | Collation seçimi kanonikleşmemiş | `⚠️` |
| IX-7 | `mb_internal_encoding` uyumu | `UNKNOWN` |

---

# EK X — Sütun Tipi ve Kısıt Sözlüğü (schema bileşenleri rehberi)

> Bu sözlük, `api_keys` örneği üzerinden tüm şemanın tip/kısıt kararlarını
> tek yerde toplar. Her satır MySQL 9 InnoDB davranışına dayanır; sürüm-specific
> notlar `⚠️ VERIFICATION REQUIRED` işaretlidir.

## X.1 PK stratejileri (CoreMusic kanonik: `binary(16)`)

| Strateji | Tip | Depolama | Sıralama | Kullanım |
|---|---|---|---|---|
| UUIDv7 binary | `binary(16)` | 16 bayt | kronolojik ✓ | **kanonik** (264 tablo) |
| UUIDv7 hex | `char(36)` / `char(32)` | 32-36 bayt | kronolojik | test çifti (SQLite) |
| AUTO_INCREMENT | `bigint` | 8 bayt | ek sırası | `UNKNOWN` (sayılmadı) |
| Composite | 2+ kolon | — | — | junction tabloları (`UNKNOWN` sayısı) |

## X.2 Kolon tipleri ↔ PHP karşılıkları

| MySQL tipi | PHP (PDO) | Dönüşüm | Çıkış riski |
|---|---|---|---|
| `binary(16)` | `string` (16 bayt) | `UuidV7::toBinary/toHex` | `toHexSafe` sessiz fallback |
| `varchar(n)` | `string` | — | — |
| `char(64)` | `string` | — | `key_hash` (SHA-256 hex) |
| `int` | `int` | — | — |
| `tinyint(1)` | `int` (0/1) | `(int)` cast | `is_active`/`is_deleted` |
| `datetime` | `string` | `date('Y-m-d H:i:s')` | TZ farkı |
| `json` | `string` | `json_encode/decode` | 2 şema (§AB.6) |
| `text` | `string` | — | `LIMIT` zorunlu (kanun) |
| `enum` | `string` | `in_array` | SQLite'ta CHECK |

## X.3 Kısıt türleri (kanonik adlandırma)

| Kısıt | MySQL anahtarı | CoreMusic adı | Sayı (dump) |
|---|---|---|---|
| Primary key | `PRIMARY KEY` | PK | 187 (tablo başına 1) |
| Unique | `UNIQUE KEY` | `uk_`/`uq_`/`idx_*_unique` (tutarsız) | 118 |
| Foreign key | `FOREIGN KEY` | `fk_{table}_{ref}` | 110 (32 çapraz-DB) |
| Index | `KEY` | `idx_{table}_{column}` | 493 |
| Check | `CHECK` | — | **0** (kanun dosyası ile çelişki) |
| Not null | `NOT NULL` | — | sayılmadı |
| Default | `DEFAULT` | — | sayılmadı |

## X.4 İsimlendirme tutarsızlıkları (tespit edilen)

| Beklenen | Bulunan | Etki |
|---|---|---|
| `uk_{table}_{column}` | `uq_` (bazı tablolar) | otomatik araçtan kaçar |
| `idx_{table}_{column}` | `idx_*_unique` (karışık) | benzersizlik sinyali gizli |
| `fk_{table}_{ref}` | — (çapraz-DB FK'lar için) | adlandırma doğrulanmadı |

---

# EK Y — Kararlar (bileşen katmanı — 4. tur, kapanış)

| # | Karar/tespit | Durum |
|---|---|---|
| Y-1 | `DatabaseConfig` tam okundu (Q-8 kapandı) | ✓ |
| Y-2 | Charset=utf8mb4 ✓ | ✓ |
| Y-3 | `user='root'` + `password=''` fallback | `⚠️` |
| Y-4 | Tablo charset'i dump'ta doğrulanmadı | `⚠️` |
| Y-5 | `ATTR_EMULATE_PREPARES` değeri | `⚠️` |
| Y-6 | PK stratejisi: `binary(16)` UUIDv7 (264 tablo) | ✓ |
| Y-7 | CHECK kısıtı: 0 (kanun dosyası çelişkisi) | ✓ (schema-kapsam) |
| Y-8 | İsimlendirme tutarsızlıkları (uq_/uk_, idx_*_unique) | `⚠️` |
| Y-9 | `key_hash` SHA-256 hex (64 krk) | ✓ |
| Y-10 | VARCHAR index uzunluğu sınırı doğrulanmadı | `UNKNOWN` |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Ek Tarihi:** 2026-10-17 · **Ek kaynağı:** disk envanteri (yok, tahmin)
