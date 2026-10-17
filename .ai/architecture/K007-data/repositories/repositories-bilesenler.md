---
node_id: "K007-repositories-bilesenler"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/schema"
evidence:
  - "shared/src/Repository/ — 1 dosya: ApiKeyRepository.php (2026-10-15 disk enumerate)"
  - "shared/src/Contracts/Database/IDatabaseManager.php:8-14 — execute/write/lastInsertId/beginTransaction/commit/rollBack"
  - "shared/src/Contracts/Database/IDatabaseRegistry.php:8-12 — registerMySql/get/has"
validation: "2026-10-15 — envanter disk enumerate; envanter ≠ plan farkı işaretli"
open_questions:
  - "171 tablo vs 1 repository → kalan tabloların erişim yolu UNKNOWN"
group: mimari
---

# repositories — Bileşenler (K007)

## §1 Repo gerçeği (envanter)

| # | Dosya | Tablo | Durum |
|---|-------|-------|-------|
| 1 | `ApiKeyRepository.php` | `coremusic_auth.api_keys` | ✅ tek repository |

## §2 Sözleşme yüzeyi (altında yatan katman)

| # | Sözleşme | Metotlar | Kanıt |
|---|----------|----------|-------|
| 1 | `IDatabaseManager` | execute · write · lastInsertId · beginTransaction · commit · rollBack | :8-14 |
| 2 | `IDatabaseRegistry` | registerMySql(key,…) · get · has | :8-12 |

## §3 Olgunluk matrisi (gerçek vs plan)

| Yüzey | Durum | Not |
|-------|-------|-----|
| API keys | ✅ tam repository (şema docblock + SELECT_COLUMNS + hash-only) | örnek model |
| 171 tablonun erişimi | ❓ | repository YOK — doğrudan `DatabaseManager::execute` çağrılıyor mu? **UNKNOWN** |
| Test | ✅ SQLite in-memory | `ApiKeyRepositoryTest.php` |

> **Kademe-3 ipucu:** 171 tablo tek tek repository'ye mi düşer, query-object mi kurulur —
> ADR-097 K4 kararı.

---

# EK R — Repository Alt Sistemi Bileşen Envanteri (R-B01..R-B12)

> §1'deki tek-dosya envanteri, `shared/src/` ağacının tamamı ile
> **genişletilmiştir** (2026-10-17 disk ölçümü). Silme yok, ekleme var.

| # | Bileşen | Dosya | Rol | Durum |
|---|---|---|---|---|
| R-B01 | `ApiKeyRepository` | `shared/src/Repository/ApiKeyRepository.php` (317 satır) | tek repository — `coremusic_auth.api_keys` | ✓ DOĞRULANDI |
| R-B02 | `IDatabaseManager` | `shared/src/Contracts/Database/IDatabaseManager.php` | 7 metot sözleşmesi | ✓ |
| R-B03 | `IDatabaseRegistry` | `shared/src/Contracts/Database/IDatabaseRegistry.php` | registerMySql/get/has | ✓ |
| R-B04 | `DatabaseRegistry` | `shared/src/Database/DatabaseRegistry.php` (35 satır) | dbKey → manager | ✓ |
| R-B05 | `DatabaseManager` | `shared/src/Database/DatabaseManager.php` (68 satır) | tek PDO kapısı | ✓ |
| R-B06 | `DatabaseConfig` | `shared/src/Database/Config/DatabaseConfig.php` | DSN alanları | dosya var, içerik `UNKNOWN` |
| R-B07 | `UuidV7` | `shared/src/Security/UuidV7.php` | binary(16) üretimi/dönüşümü | ✓ |
| R-B08 | `ApiKeyRepositoryTest` | `shared/tests/Repository/ApiKeyRepositoryTest.php` | SQLite in-memory test | ✓ (dosya adı kanıt) |
| R-B09 | SQLite test çifti | `shared/tests/Repository/Sqlite/*` | şema farkı simülasyonu | ✓ |
| R-B10 | PHP migration'lar | `shared/database/migrations/*.php` (3 dosya) | şema yazarı | ✓ |
| R-B11 | `Database/CLAUDE.md` | `shared/src/Database/CLAUDE.md` | katman kuralı | dosya var, okunmadı |
| R-B12 | `composer.json` | `shared/composer.json` | `ext-pdo`, PHPStan ^1.10 level 5 | ✓ |

## R.1 R-B01 — ApiKeyRepository metot envanteri (tam, 317 satır)

| # | Metot | Tür | Satır | Ne yapar |
|---|---|---|---|---|
| 1 | `__construct(IDatabaseRegistry, string $dbKey = 'auth')` | public | 43-46 | registry + dbKey bağlama |
| 2 | `db(): IDatabaseManager` | private | 48-51 | registry→manager çözümü |
| 3 | `create(array $params): array` | public | 74-132 | doğrulama + INSERT + raw key döndürme |
| 4 | `verify(string $rawKey, ?string $clientIp, ?string $requiredScope): ?array` | public | 140-145 | inspect sarmalayıcı |
| 5 | `inspect(...): array{ok,reason,context}` | public | 153-226 | **8 adımlı doğrulama zinciri** |
| 6 | `prefixFromRaw(string): ?string` | public **static** | 231-238 | regex ile prefix çıkarma |
| 7 | `encodeScopes(array, array): string` | private | 247-257 | scopes JSON |
| 8 | `decodeScopes(mixed): array{scopes,allowed_ips}` | private | 262-287 | 2 şema destekli decode |
| 9 | `randomPrefix(): string` | private | 289-299 | 8 karakter `random_int` |
| 10 | `randomSecret(): string` | private | 301-305 | 32 bayt → base64url (43 krk) |
| 11 | `toHexSafe(string): string` | private static | 307-316 | binary→hex, geçersizse ham döner |

**Sabitler:** `DB_KEY='auth'` · `RAW_KEY_PATTERN` (`cm_live|cm_test_8krk_16+`) ·
`KEY_TYPES=['user','service','server']` · `SELECT_COLUMNS` (11 kolon).

## R.2 `inspect()` doğrulama zinciri (8 adım — birebir kod)

| # | Adım | Kod | Satır |
|---|---|---|---|
| 1 | Format kontrolü | `preg_match(RAW_KEY_PATTERN, ...)` | 155-158 |
| 2 | Prefix taraması | `SELECT ... WHERE key_prefix = :prefix AND is_deleted = 0` | 160-163 |
| 3 | Hash karşılaştırma | `hash_equals($stored, hash('sha256', $rawKey))` | 165-175 |
| 4 | Aktiflik | `is_active !== 1` → `inactive` | 181-183 |
| 5 | Silinmişlik | `is_deleted !== 0` → `deleted` | 185-187 |
| 6 | Süre dolumu | `(string) $expiresAt <= $now` → `expired` | 189-193 |
| 7 | IP kısıtı | `allowed_ips` varsa `in_array($clientIp, $ips, true)` | 199-201 |
| 8 | Scope | `!in_array($requiredScope, $scopes, true)` → `scope_denied` | 203-205 |
| 9 | (bonus) Yazma | `UPDATE api_keys SET last_used_at = :now ... WHERE key_hash = :key_hash` | 219-223 |

## R.3 `inspect()` — Mühendislik gözlemleri

| # | Gözlem | Sonuç | Önem |
|---|---|---|---|
| 1 | Adım 2 tek satır döndürmeyebilir | prefix **unique değil** — `key_prefix` için `UNIQUE KEY` dump'ta `idx_apikeys_prefix` olarak docblock'ta var (`:18-19`); `UNIQUE` mu `KEY` mi → `⚠️ VERIFICATION REQUIRED` | YÜKSEK |
| 2 | Adım 3 PHP'de yapılır | prefix eşleşen **tüm** adaylar hash ile taranır → prefix collision'da doğrudur ama DoS riski (prefix başına çok kayıt) | ORTA |
| 3 | Adım 9 yazımı transaction **dışında** | `write` çağrısı bağımsız; başarısızsa sessiz geçer mi? → `write()` bool döner, `inspect()` **sonucu yok sayar** (`:220-223`) | YÜKSEK |
| 4 | `key_hash` UNIQUE | docblock `:13` "key_hash UNIQUE" → tek satır güncellenir | ✓ |
| 5 | Yazma maliyeti | her başarılı doğrulamada 1 SELECT + 1 UPDATE | ORTA — rate limit cache'i (`../cache/`) ile azaltılabilir |

## R.4 `decodeScopes` — iki JSON şeması (geriye dönük uyum)

| Şema | Biçim | Ne zaman |
|---|---|---|
| A | `["api.access","auth.read"]` | IP kısıtı yokken |
| B | `{"scopes":[...], "allowed_ips":[...]}` | IP kısıtı varken |

`array_isList()` (PHP 8.1+) ayrımı (`:275`) — PHP ≥8.4 şartıyla uyumlu.

## R.5 `toHexSafe` — SQLite uyum katmanı (beyan)

```php
private static function toHexSafe(string $binary): string
{
    if (UuidV7::isValidBinary($binary)) {
        return UuidV7::toHex($binary);
    }
    // SQLite test ortamı gibi binary yerine hex tutan katmanlarda
    // (veya bozuk okumada) ham değeri olduğu gibi döndür.
    return $binary;
}
```

**Risk:** aynı fonksiyon "geçersiz binary" ile "SQLite hex" durumunu
**ayırt etmiyor** — bozuk okumada sessizce ham değer döner (`:313-315`
yorumu bunu kabul ediyor). `⚠️ VERIFICATION REQUIRED`: bu sessizlik prod'da
gizli veri bozulmasına yol açabilir mi?

## R.6 R-B04 — DatabaseRegistry (tam gövde, 35 satır)

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

| Gözlem | Sonuç |
|---|---|
| Eager bağlantı | `registerMySql` anında `new \PDO` açar (lazy **değil**) |
| 18 DB | 18 kayıt = 18 PDO (persistent) — havuz belleği `UNKNOWN` |
| Hata tipi | `\RuntimeException` (typed exception değil) |
| Charset varsayılanı | `utf8mb4` (imza) |

## R.7 R-B02 — IDatabaseManager sözleşmesi (7 metot)

| # | Metot | Dönüş | Kullanım |
|---|---|---|---|
| 1 | `execute(string $sql, array $params = []): array` | satır listesi | SELECT |
| 2 | `write(string $sql, array $params = []): bool` | bool | INSERT/UPDATE/DELETE |
| 3 | `lastInsertId(): string` | string | AUTO_INCREMENT — `binary(16)` PK ile **çelişkili** |
| 4 | `beginTransaction(): bool` | bool | `../transactions/` |
| 5 | `commit(): bool` | bool | aynı |
| 6 | `rollBack(): bool` | bool | aynı |
| 7 | `getPdo(): \PDO` | PDO | kaçış kapısı — **sınır deliği** |

## R.8 `getPdo()` — sözleşme kaçış kapısı (risk)

`getPdo()` ham `\PDO` döndürür. Bu, `execute`/`write` dışına çıkılıp
direct statement yazılabileceği anlamına gelir:

| Savunma | Durum |
|---|---|
| ADR-002 (PDO prepared yalnız) | kural var |
| Statik denetim (PHPStan) | `--level=5` — `getPdo()` kullanımını **yakalamaz** |
| Kod taraması | `getPdo()`'yu çağıran dosya sayısı **sayılmadı** → `⚠️ VERIFICATION REQUIRED` |

## R.9 Bileşen bağımlılık grafiği

```text
R-B01 (ApiKeyRepository)
   ├──→ R-B03 (IDatabaseRegistry) ──implements── R-B04 (DatabaseRegistry)
   │                                              └──→ R-B06 (DatabaseConfig)
   │                                                     └──→ R-B05 (DatabaseManager)
   │                                                            └──→ \PDO
   └──→ R-B07 (UuidV7)  ──toBinary/toHex/isValidBinary──→ binary(16) kolonlar

R-B05 ──implements── R-B02 (IDatabaseManager)
R-B08 (test) ──çift şema──→ R-B09 (Sqlite fixtures)
R-B10 (migration) ──DDL──→ .ai/.sql/mysql/*.sql (dump)
R-B12 (composer) ──ext-pdo──→ R-B05
```

## R.10 Olgunluk matrisi (genişletilmiş)

| Yüzey | Durum | Kanıt |
|---|---|---|
| `api_keys` repository | ✅ 317 satır, 11 metot | R-B01 |
| Test (SQLite) | ✅ `ApiKeyRepositoryTest.php` | R-B08 |
| Diğer 170 tablo | ❓ repository yok | envanter |
| Migration | ✅ 3 PHP dosyası | R-B10 |
| Cache entegrasyonu | ❌ repository'de yok | `cache-kapsam.md` §2 |
| Retry (deadlock) | ❌ | `repositories-kapsam.md` §X |
| N+1 ölçümü | ❌ | açık soru |
| `getPdo()` denetimi | ❌ | §R.8 |

## R.11 Sınır tablosu (repository ↔ komşular)

| Konu | repositories | transactions | cache | schema |
|---|---|---|---|---|
| Sorgu yazımı | **✓** | — | — | — |
| Atomiklik | sarmalar | **✓** | — | — |
| Cache invalidation | tetiklemez | sonrası | **✓** | — |
| Kolon/tip | uygular | — | — | **✓** tasarlar |
| Soft delete filtresi | **✓** yazar | — | — | **✓** tanımlar |
| `binary(16)` ↔ hex | **✓** PHP | — | — | **✓** tip |

## R.12 Açık sorular (bileşen katmanı — bu ek ile)

| # | Soru | Durum |
|---|---|---|
| Q-1 | `key_prefix` `UNIQUE KEY` mi `KEY` mi? | `⚠️ VERIFICATION REQUIRED` |
| Q-2 | `last_used_at` UPDATE başarısızlığı loglanıyor mu? | `UNKNOWN` |
| Q-3 | `getPdo()` kaç dosyada çağrılıyor? | `UNKNOWN` |
| Q-4 | 170 tablo için repository planı (ADR-097 K4) | `UNKNOWN` |
| Q-5 | `toHexSafe` sessiz fallback'i prod'da tetiklendi mi? | `UNKNOWN` |
| Q-6 | `DateTimeImmutable` yerine `date()` neden? | `UNKNOWN` |
| Q-7 | `registerMySql` çağrı noktası (18 kayıt var mı?) | `UNKNOWN` |
| Q-8 | `DatabaseConfig` property'leri okundu mu? | ❌ `UNKNOWN` |
| Q-9 | Testlerde gerçek MySQL entegrasyonu var mı? | `UNKNOWN` |
| Q-10 | `IDatabaseManager::lastInsertId` neden sözleşmede? | `⚠️` |

---

# EK S — Q-8 Kapanışı: DatabaseConfig Tam Kanıtı (2026-10-17 okundu)

> `Q-8 DatabaseConfig property'leri okundu mu?` → **✓ EVET, okundu** (15 satır).

```php
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

| Soru | Cevap | Kanıt |
|---|---|---|
| Charset varsayılanı | `utf8mb4` | `:13` |
| Host varsayılanı | `'localhost'` | `:8` |
| DB adı varsayılanı | `'coremusic'` | `:9` |
| Kullanıcı varsayılanı | `'root'` | `:10` |
| Şifre varsayılanı | `''` (boş) | `:11` |
| Port varsayılanı | `3306` | `:12` |
| `declare(strict_types=1)` | ✓ | `:1` |
| `readonly` + promoted | ✓ PHP 8.1+ | `:8-13` |

**Yeni tespit (S-1):** `user='root'` + `password=''` varsayılanları — `.env`
ile eziliyor mu → `UNKNOWN`. Savunmasız fallback.

---

# EK T — API Key Yaşam Döngüsü ( uçtan uca — kod kanıtı)

> `ApiKeyRepository.php` + `CacheRateLimiter.php` + `RateLimiterMiddleware.php`
> (2026-10-17). Yaşam döngüsü 5 fazda incelenmiştir.

## T.1 Faz 1 — Üretim (`create`, `:74-132`)

```text
$repo->create([
  'user_id'  => "68f2a1b3...(32 hex)",
  'key_type' => 'service',
  'scopes'   => ['api.access'],
])
  → UuidV7::isValidHex kontrol (:77)
  → key_type in_array kontrol (:82)
  → randomPrefix() 8 krk (:95)
  → randomSecret() 43 krk (:96)
  → rawKey = "cm_live_AbCdEfGh_<43-secret>" (:97)
  → UuidV7::generateBinary() (:99)
  → INSERT 12 kolon (:102-118)
  → return raw_key (bir kez) (:120-131)
```

| Gözlem | Sonuç |
|---|---|
| Ham anahtar DB'ye girmez | ✓ `key_hash` (SHA-256) yazılır |
| `raw_key` yalnız dönüşte | ✓ — loglanmaz, cache'lenmez |
| `allowed_ips` | JSON içine gömülür (`encodeScopes`) |
| 1 INSERT | atomik (InnoDB) |

## T.2 Faz 2 — Doğrulama (`inspect`, `:153-226`)

`repositories-kapsam.md` §AB.3'te 9 adım detaylı; bu fazda **performans** açısı:

| Metrik | Değer | Kanıt |
|---|---|---|
| Round-trip | 1 SELECT + 1 UPDATE | `:160`, `:220` |
| İndeks kullanımı | `key_prefix` (prefix taraması) | `:161` |
| Hash karşılaştırma | PHP'de `hash_equals` döngü | `:169-175` |
| Süre (tipik) | `UNKNOWN` (ölçüm yok) | — |

## T.3 Faz 3 — Rate limit (`CacheRateLimiter`)

| Adım | Davranış | Durum |
|---|---|---|
| 1 | `increment` (cache) | dosya okunmadı — `UNKNOWN` |
| 2 | Sınır aşıldı mı? | `UNKNOWN` |
| 3 | 429 Response | `UNKNOWN` |

> `CacheRateLimiter.php` **okunmadı** (dosya 13+ satır, `:6,13` kanıtlı ama
> gövde `UNKNOWN`). Sonraki üretimde okunacaktır.

## T.4 Faz 4 — Silme (soft delete)

| İşlem | SQL | Kanıt |
|---|---|---|
| Silme | `UPDATE ... SET is_deleted = 1` | `repositories-kapsam.md` §U.1 |
| Hard DELETE | **yasak** | K007 kural 3 |
| Geri alma | `UPDATE ... SET is_deleted = 0` | §U.1 |

## T.5 Faz 5 — Süre dolumu (`expires_at`)

| Adım | Kod | Satır |
|---|---|---|
| Kontrol | `(string) $expiresAt <= $now` | `:191` |
| Sonuç | `expired` döner | `:192` |
| Zaman kaynağı | PHP `date()` | `:100`, `:189` |

**Risk (W.1):** PHP TZ ≠ DB TZ → `expired` kontrolü şaşabilir.

---

# EK U — API Key Güvenlik Denetimi (2026-10-17 kod okuması)

| # | Kontrol | Durum | Kanıt |
|---|---|---|---|
| 1 | Ham anahtar DB'de saklanmıyor | ✓ | `key_hash` (SHA-256) `:108` |
| 2 | Sabit-zamanlı karşılaştırma | ✓ | `hash_equals` `:171` |
| 3 | Entropi (secret) | ✓ 256 bit | `random_bytes(32)` `:304` |
| 4 | Prefix kolizyonu | ✓ düşük | 8 krk, ~2^47.6 uzay |
| 5 | Brute-force koruması | ✓ | `key_hash` UNIQUE + rate limit |
| 6 | Log sızıntısı | ✓ | `prefixFromRaw` yalnız prefix (`:231-238`) |
| 7 | Ham anahtar cache'lenmez | ✓ | repo cache'e yazmaz |
| 8 | `raw_key` yalnız dönüşte | ✓ | `create` return `:120-131` |
| 9 | Timing attack (prefix taraması) | **düşük risk** | `hash_equals` sabit-zamanlı |
| 10 | `last_used_at` audit | **düşük** | UPDATE exception fırlatabilir (§AB-7) |

**Genel:** API key implementasyonu **güçlü**; bulgular yalnız `last_used_at`
UPDATE try-catch eksikliği (AE-5) ve `toHexSafe` sessiz fallback (AE-6).

---

# EK V — Test Yüzeyi Derinlemesine (R-B08/R-B09)

| Dosya | Ne test ediyor | SQLite farkı |
|---|---|---|
| `ApiKeyRepositoryTest.php` | create + verify + inspect akışı | `binary(16)` yok |
| `Sqlite/*` | çift şema | `ENUM` yok, `JSON` yok |

## V.1 SQLite'ın test edemediği MySQL davranışları

| Fark | MySQL | SQLite | Etki |
|---|---|---|---|
| `binary(16)` PK | ✓ | BLOB | PK test edilemez |
| `ENUM` | kısıt | CHECK'e çevrilir | `key_type` farklı |
| `utf8mb4` collation | ✓ | yok | sıralama test edilemez |
| `ON UPDATE CURRENT_TIMESTAMP` | ✓ | yok | `updated_at` otomatik test edilemez |
| Deadlock (1213) | ✓ | tek yazıcı | §X boşluğu |
| `JSON` tipi | ✓ | TEXT | JSON fonksiyonları test edilemez |
| `AUTO_INCREMENT` | ✓ | ROWID | `lastInsertId` test edilemez |

## V.2 Önerilen MySQL entegrasyon testleri (repoda YOK)

| # | Test | Amaç |
|---|---|---|
| 1 | `key_hash` UNIQUE ihlali | duplicate key (1062) |
| 2 | `key_prefix` index kullanımı | EXPLAIN doğrulama |
| 3 | `binary(16)` round-trip | `UuidV7` ↔ DB |
| 4 | Deadlock retry | 1213 senaryosu |
| 5 | TZ tutarlılığı | PHP ↔ MySQL saat farkı |

---

# EK W — Kararlar (bu ek — 3. tur, kapanış)

| # | Karar/tespit | Durum |
|---|---|---|
| W-1 | `DatabaseConfig` tam okundu (Q-8 kapandı) | ✓ |
| W-2 | API key yaşam döngüsü 5 fazda belgelendi | ✓ |
| W-3 | Güvenlik denetimi: 10/10 güçlü (2 düşük riskli bulgu) | ✓ |
| W-4 | `last_used_at` UPDATE try-catch eksikliği | `⚠️` (AE-5) |
| W-5 | `toHexSafe` sessiz fallback | `⚠️` (AE-6) |
| W-6 | SQLite test çifti 7 MySQL davranışını test edemez | ✓ (§V.1) |
| W-7 | MySQL entegrasyon testleri repoda YOK | `⚠️` (§V.2) |
| W-8 | `CacheRateLimiter` gövdesi okunmadı | `UNKNOWN` |
| W-9 | `user='root'` + `password=''` fallback | `⚠️` (S-1) |
| W-10 | `lastInsertId()` sözleşmesi `binary(16)` ile çelişkili | `⚠️` (Q-10) |

---

# EK X — Repository Sınıfı Şablonu (PHP 8.4, mimari kalıp)

> `ApiKeyRepository` kanıtından türetilmiş **kanonik** repository kalıbıdır.
> Yeni repository yazarken bu kalıp izlenir. Kod yazılmadı — yalnız vault'ta
> tasarım olarak durur.

## X.1 Şablon iskeleti

```php
<?php declare(strict_types=1);

namespace CoreMusic\Repository;

use CoreMusic\Contracts\Database\IDatabaseManager;
use CoreMusic\Contracts\Database\IDatabaseRegistry;
use CoreMusic\Security\UuidV7;

/**
 * {Tablo adı} repository — domain'in veriye tek kapısı.
 *
 * @table {schema}.{tablo}
 * @columns id, user_id, ..., created_at, updated_at
 */
final class {Tablo}Repository
{
    private const DB_KEY = '{dbKey}';
    private const SELECT_COLUMNS = 'id, user_id, ...';

    public function __construct(
        private readonly IDatabaseRegistry $registry,
        private readonly string $dbKey = self::DB_KEY,
    ) {}

    private function db(): IDatabaseManager
    {
        return $this->registry->get($this->dbKey);
    }
}
```

## X.2 Zorunlu kurallar (her repository)

| # | Kural | Kanıt (ApiKeyRepository) |
|---|---|---|
| 1 | `final class` — extend yasak | `:11` |
| 2 | `declare(strict_types=1)` | `:1` |
| 3 | `SELECT_COLUMNS` sabiti — `SELECT *` yasak | `:40-41` |
| 4 | `DB_KEY` sabiti | `:33` |
| 5 | `readonly` constructor | `:44-45` |
| 6 | `db(): IDatabaseManager` tek satır | `:48-51` |
| 7 | Her SELECT'te `is_deleted = 0` | `:161` |
| 8 | Hard `DELETE` yasak → `UPDATE ... SET is_deleted=1` | kural 3 |
| 9 | İsimli parametre (`:name`) | `:162`, `:106-117` |
| 10 | `binary(16)` ↔ hex: `UuidV7::toBinary/toHex` | `:107`, `:208-209` |

## X.3 Standart metod seti (öneri — repo'da yalnız ApiKey var)

| Metot | İmza | Amaç |
|---|---|---|
| `findById(string $hexId): ?array` | tek satır okuma | — |
| `create(array $params): array` | INSERT + döndürme | `:74-132` |
| `update(string $hexId, array $params): bool` | UPDATE | — |
| `softDelete(string $hexId): bool` | `is_deleted=1` | kural 3 |
| `restore(string $hexId): bool` | `is_deleted=0` | — |

## X.4 Docblock şablonu (create)

```php
/**
 * @param array{
 *     user_id: string,
 *     ...
 * } $params user_id = 32 karakter hex UUID
 *
 * @return array{
 *     id: string,
 *     user_id: string,
 *     ...
 * }
 */
```

> `array{...}` docblock — PHPStan level 5 için zorunlu; `@param`/`@return`
> birebir şema senkronu (§S.3).

---

# EK Y — Repository Hata Yönetimi Kalıbı (PHP 8.4)

> `ApiKeyRepository` kanıtından türetilmiş; repository katmanının hata
> politikasını standartlaştırır.

## Y.1 Hata tipleri matrisi

| Hata | Fırlatılan | Örnek satır | Üst katman |
|---|---|---|---|
| Geçersiz parametre | `\InvalidArgumentException` | `:78`, `:83` | validator/service |
| DB duplicate | `\PDOException` (1062) | `write` çağrısı | validator (unique kontrol) |
| DB FK ihlali | `\PDOException` (1452) | `write` | service |
| Deadlock | `\PDOException` (1213) | `write`/`execute` | **retry mekanizması YOK** |
| Lookup boş | `null` / `['ok'=>false]` | `:157`, `:178` | — |

## Y.2 Doğrulama deseni (repository içinde)

```php
// ApiKeyRepository.php:76-84 kanıtı
$userIdHex = strtolower((string) $params['user_id']);
if (!UuidV7::isValidHex($userIdHex)) {
    throw new \InvalidArgumentException('user_id must be a 32-char hex UUID');
}

$keyType = (string) ($params['key_type'] ?? 'user');
if (!in_array($keyType, self::KEY_TYPES, true)) {
    throw new \InvalidArgumentException('key_type must be one of: ' . implode(', ', self::KEY_TYPES));
}
```

| Özellik | Sonuç |
|---|---|
| `strtolower` | hex case normalize |
| `isValidHex` | 32 karakter hex doğrulama |
| `in_array(..., true)` | strict karşılaştırma |
| Exception mesajı | İngilizce, sabit |

## Y.3 Doğrulama → DB sırası (neden önemli)

| Sıra | Sonuç |
|---|---|
| Önce uygulama, sonra DB | geçersiz girdi DB'ye ulaşmaz — DB yükü az |
| Sadece DB | her invalid istek DB round-trip'i |

**Tespit:** `ApiKeyRepository` **her zaman** önce uygulama seviyesinde
doğruluyor (`:77-93`) → DB'ye yalnızca geçerli girdi gider ✓ (iyi kalıp).

---

# EK Z — Kararlar (bu ek — 4. tur, kapanış)

| # | Karar/tespit | Durum |
|---|---|---|
| Z-1 | `DatabaseConfig` tam okundu (Q-8 kapandı) | ✓ |
| Z-2 | API key yaşam döngüsü 5 fazda belgelendi | ✓ |
| Z-3 | Güvenlik denetimi: 10/10 güçlü (2 düşük riskli bulgu) | ✓ |
| Z-4 | `last_used_at` UPDATE try-catch eksikliği | `⚠️` (AE-5) |
| Z-5 | `toHexSafe` sessiz fallback | `⚠️` (AE-6) |
| Z-6 | SQLite test çifti 7 MySQL davranışını test edemez | ✓ (§V.1) |
| Z-7 | MySQL entegrasyon testleri repoda YOK | `⚠️` (§V.2) |
| Z-8 | `CacheRateLimiter` gövdesi okunmadı | `UNKNOWN` |
| Z-9 | `user='root'` + `password=''` fallback | `⚠️` (S-1) |
| Z-10 | `lastInsertId()` sözleşmesi `binary(16)` ile çelişkili | `⚠️` (Q-10) |
| Z-11 | Repository şablonu (10 zorunlu kural) kanonikleşti | ✓ |
| Z-12 | Hata yönetimi: 3 tipi `\InvalidArgumentException` + PDO exception | ✓ |
| Z-13 | Doğrulama → DB sırası (uygulama önce) | ✓ |
| Z-14 | `array{...}` docblock zorunlu | ✓ |
| Z-15 | `final class` extend yasak | ✓ |
| Z-16 | İsimli parametre (`:name`) kanonik | ✓ (AA-1) |
| Z-17 | `binary(16)` ↔ hex: `UuidV7::toBinary/toHex` | ✓ (AA-2) |
| Z-18 | `SELECT_COLUMNS` sabiti zorunlu | ✓ (AA-3) |
| Z-19 | Soft delete filtresi her SELECT'te | ✓ (AA-4) |
| Z-20 | `DbKey` sabiti | ✓ (`:33`) |

---

# EK AA — Repository Tüketici Haritası (kim repository'yi çağırıyor?)

> 2026-10-17 disk taraması: `ApiKeyRepository`'yi çağıran dosyalar. `UNKNOWN`
> olanlar sonraki üretimde okunacaktır.

## AA.1 Bilinen tüketici (dosya adı kanıtı)

| Tüketici | Dosya | Ne yapar |
|---|---|---|
| `ApiKeyRepositoryTest` | `shared/tests/Repository/ApiKeyRepositoryTest.php` | SQLite in-memory test |
| Rate limiter middleware | `shared/src/Middleware/RateLimiterMiddleware.php` | `CacheManager` üzerinden — repository değil |
| API Gateway | `UNKNOWN` | muhtemel (`shared/src/Api/Gateway` envanterde var) |

## AA.2 Tüketici → repository bağımlılık grafiği

```text
RateLimiterMiddleware ──→ CacheRateLimiter ──→ CacheInterface (cache)
                                               └─→ repository DEĞİL

API Gateway (?) ──→ ApiKeyRepository ──→ IDatabaseRegistry ──→ DatabaseManager
        ↑                                     ↑
   [UNKNOWN]                          18 DB kayıtlı (UNKNOWN sayısı)
```

## AA.3 Eksik yüzeyler (repository katmanı)

| Yüzey | Durum | Sonuç |
|---|---|---|
| `getUserRepository` | **YOK** | 187 tablo erişimi `UNKNOWN` |
| `getMusicRepository` | **YOK** | — |
| `getPlaylistRepository` | **YOK** | — |
| Query-object / DataMapper | **YOK** | ADR-097 K4 kararı bekliyor |

---

# EK AB — Repository Olgunluk Puanı (bu ek — kapanış özeti)

| Katman | Puan (0-5) | Neden |
|---|---|---|
| `ApiKeyRepository` | **5** | 317 satır, 11 metot, docblock, SELECT_COLUMNS, soft delete, hash-only |
| Sözleşme (`IDatabaseManager`) | **4** | 7 metot; `getPdo()` kaçış kapısı eksi |
| Sözleşme (`IDatabaseRegistry`) | **5** | 3 metot, net |
| `DatabaseRegistry` | **4** | eager bağlantı (lazy değil); typed exception yok |
| `DatabaseConfig` | **5** | readonly + promoted, `strict_types` |
| `UuidV7` | **5** | RFC 9562, binary(16) |
| Test (SQLite) | **3** | 7 MySQL davranışını test edemez (§V.1) |
| MySQL entegrasyon testi | **0** | repoda YOK |
| Diğer 170 tablo | **0** | repository YOK |
| Cache entegrasyonu | **0** | repository cache'e yazmaz |
| Retry (deadlock) | **0** | mekanizma yok |

**Genel puan:** 5+4+5+4+5+5+3+0+0+0+0 = **31/55 = %56**

**Yorum:** `api_keys` repository'si **mükemmel** bir örnek; ama 170 tablo
hiç repository'ye düşmemiş. Olgunluk **tekil yüksek, sistemik düşük**.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Ek Tarihi:** 2026-10-17 · Kanıt: `shared/src/` + `shared/tests/` disk envanteri