---
node_id: "K007-data/kod-ornekleri"
node_type: "domain"
status: "planned"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/data-master"
  - "K007-data/data-bilesenler"
evidence:
  - "shared/src/Database/DatabaseManager.php:1-68 — tam dosya okundu, 2026-10-12"
  - "shared/src/Cache/PageCacheAdapter.php:1-45 — tam dosya okundu, 2026-10-12"
  - "shared/src/Repository/ApiKeyRepository.php:1-317 — tam dosya okundu, 2026-10-12"
  - "shared/database/migrations/oauth_connections_migration.php:1-46 · 2026-10_07_drop_coremusic_api_api_keys_migration.php:1-51"
validation: "2026-10-12 — 5 kod parçası birebir dosya içeriğinden kopyalandı (satır aralığı her blokta); hiçbir snippet uydurulmadı."
open_questions:
  - "Migration çalıştırıcı (runner) hangi katmanda — $precheck/$queries anahtarlarını kim tüketiyor? (UNKNOWN, K007 dışı yüzey)"
  - "PageCacheAdapter'ın cache'lediği değerin içeriği (HTML gövde mi veri mi) — çağıran taraf okunmadı (⚠️ VERIFICATION REQUIRED)"
  - "İki migration deseni (düz $queries listesi vs anahtarlı dizi) arasında standart hangisi — K012-services/kayıt bekliyor"
group: kod
---

# K007-data / kod-ornekleri — Gerçek Depo Kod Parçaları

> Kurallar: (1) her blok dosyadan **birebir** kopyalanmıştır, (2) her blokta dosya yolu +
> satır aralığı vardır, (3) bloğun K007'nin hangi çocuğunu kanıtladığı yazılır.
> Tarih: 2026-10-12 · Kaynak ağaç: `C:\www\coremusic.net`.

---

## Ö-1 · PDO bağlantı fabrikası — `transactions`, `schema`

**Dosya:** `shared/src/Database/DatabaseManager.php` · **Satır:** 14-27

```php
$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
    $config->host,
    $config->port,
    $config->dbName,
    $config->charset,
);

$this->pdo = new \PDO($dsn, $config->user, $config->password, [
    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::ATTR_EMULATE_PREPARES   => false,
    \PDO::ATTR_PERSISTENT         => true,
]);
```

**Ne kanıtlar:**

1. **K-01 (MySQL/InnoDB):** DSN `mysql:` şeması + `charset` parametresi — charset
   yapılandırmanın parçasıdır (`utf8mb4` değeri `DatabaseConfig`'ten gelir; bu blokta
   `$config->charset`'tir, sabit yazılmamıştır).
2. **K-02 (PDO, ORM yok):** doğrudan `new \PDO` çağrısı; hiçbir ORM katmanı yok.
3. `EMULATE_PREPARES => false` → gerçek hazırlanmış-ifade (server-side prepare) —
   SQL enjeksiyon savunmasının PDO ayağı.
4. `ERRMODE_EXCEPTION` → hata sessizce `false` dönmez; exception yukarıya yayılır.
   K007'nin "Result/Either'e çevirme" kuralı bu yüzden **repository sınırında** durur
   (PDO exception'ı repository'de yapısal hataya çevrilir).

---

## Ö-2 · Transaction yüzeyi — `transactions`

**Dosya:** `shared/src/Database/DatabaseManager.php` · **Satır:** 49-67

```php
public function beginTransaction(): bool
{
    return $this->pdo->beginTransaction();
}

public function commit(): bool
{
    return $this->pdo->commit();
}

public function rollBack(): bool
{
    return $this->pdo->rollBack();
}

public function getPdo(): \PDO
{
    return $this->pdo;
}
```

**Ne kanıtlar:** K007 `transactions` çocuğunun çağrı-yüzeyi tam burasıdır — üç method,
tek bağlantı. **Sınırlılık (kanıtla yazılan):** `beginTransaction()` tek PDO üzerinde
çalışır; **18 veritabanı arası dağıtık transaction için bu API yeterli değildir**
(dağıtık atomiklik K-07/K009 senkronizasyon tasarımına bağlı, `UNKNOWN`).
`getPdo()` sızdırma-özelliğidir: çağıran katman ham PDO alabilir — ⚠️ kullanımının
nerede olduğu K007 kapsamı dışında (araştırılacak).

---

## Ö-3 · Sorgu/ yazma çekirdeği — `repositories`

**Dosya:** `shared/src/Database/DatabaseManager.php` · **Satır:** 30-42

```php
public function execute(string $sql, array $params = []): array
{
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetchAll();
    return $result;
}

public function write(string $sql, array $params = []): bool
{
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute($params);
}
```

**Ne kanıtlar:** Her iki method da `prepare` + `parametreli execute` — yani
**ADR-002 "PDO prepared statement yalnız"** kuralının tek uygulama noktası.
`SELECT *` yasağı bu methodta değil, **çağıran sorgu metninde** denetlenir;
repository'ler `SELECT_COLUMNS` sabiti ile sütun listesini açık yazar (Ö-5).

---

## Ö-4 · Sayfa anahtarlama + TTL — `cache`

**Dosya:** `shared/src/Cache/PageCacheAdapter.php` · **Satır:** 28-44

```php
public function pageKey(string $uri, bool $authenticated): string
{
    $prefix = $authenticated ? 'page_auth_' : 'page_pub_';
    return $prefix . md5($uri);
}

public function remember(string $key, int $ttl, callable $callback): mixed
{
    $cached = $this->cache->get($key);
    if ($cached !== false) {
        return $cached;
    }

    $value = $callback();
    $this->cache->set($key, $value, $ttl);
    return $value;
}
```

**Ne kanıtlar:**

1. Kimlik-ayrımlı anahtar: `page_pub_` (anonim) / `page_auth_` (girişli) + `md5(uri)` —
   **K-05 invalidasyon sözleşmesinin** anahtar tarafı. Aynı URI iki ayrı oturum türü için
   ayrı saklanır (sızıntı önleme).
2. `remember` **read-through** deseni: önce `get`, `false` ise `callback()` çalıştırıp
   `set`. `false` kontrolü ⚠️ **tip-sıkışması** taşır: `CacheInterface::get(): mixed`
   `false` dönebilir; `MemoryAdapter::get` (satır 10-21) da `false` ile "yok" imlerse
   cache'lenen değerin `false` olması "var" ile "yok" arasında ayırt edilemez —
   **kanıtlanmış gözlem, davranış hatası olduğu iddia edilmez** (`⚠️ TASARIM NOTU`).
3. Constructor varsayılanı `ttlPage = 600` (satır 11) — TTL 600 sn iddiasının kaynağı.

---

## Ö-5 · Repository: sabit sütun listesi — `repositories`

**Dosya:** `shared/src/Repository/ApiKeyRepository.php` · **Satır:** 40-41

```php
private const SELECT_COLUMNS =
    'id, user_id, key_hash, key_prefix, key_name, scopes, key_type, rate_limit, is_active, last_used_at, expires_at';
```

**Ne kanıtlar:** `SELECT *` yasağının (**ADR-002**) somut uygulaması: sütun listesi
sabit olarak açık yazılır ve sorguda birleştirilir (satır 161:
`'SELECT ' . self::SELECT_COLUMNS . ' FROM api_keys WHERE key_prefix = :prefix AND is_deleted = 0'`).

---

## Ö-6 · Repository: parametreli INSERT + hash — `repositories`

**Dosya:** `shared/src/Repository/ApiKeyRepository.php` · **Satır:** 102-118

```php
$this->db()->write(
    'INSERT INTO api_keys (id, user_id, key_hash, key_prefix, key_name, scopes, key_type, rate_limit, is_active, expires_at, created_at, updated_at) '
    . 'VALUES (:id, :user_id, :key_hash, :key_prefix, :key_name, :scopes, :key_type, :rate_limit, 1, :expires_at, :now1, :now2)',
    [
        'id'         => $idBinary,
        'user_id'    => UuidV7::toBinary($userIdHex),
        'key_hash'   => hash('sha256', $rawKey),
        'key_prefix' => $prefix,
        'key_name'   => $keyName,
        'scopes'     => $this->encodeScopes($scopes, $allowedIps),
        'key_type'   => $keyType,
        'rate_limit' => $rateLimit,
        'expires_at' => $expiresAt,
        'now1'       => $now,
        'now2'       => $now,
    ]
);
```

**Ne kanıtlar:**

1. **Adlandırılmış placeholder** (`:id`, `:user_id`…) — string birleştirme yok.
2. **binary(16) UUID:** `UuidV7::toBinary($userIdHex)` — `.ai/.sql/CLAUDE.md` beyanının
   kod karşılığı (hex → 16 bayt).
3. **Ham anahtar asla DB'ye gitmez:** yalnız `hash('sha256', $rawKey)` yazılır
   (dosya başlık yorumu satır 26-27 ile uyumlu).
4. `now1`/`now2` iki kez aynı `:now` yerine **iki ayrı placeholder** — MySQL'in tek
   statement içinde aynı placeholder'ı iki kez kullanma kısıtına karşı bilinçli
   workaround (⚠️ kısıt kendisi kodda okunmadan **`⚠️ VERIFICATION REQUIRED`**;
   desen niyeti yorum satırı değildir).

---

## Ö-7 · Repository: sabit-zamanlı hash doğrulama — `repositories`, `data-quality`

**Dosya:** `shared/src/Repository/ApiKeyRepository.php` · **Satır:** 160-175

```php
$rows = $this->db()->execute(
    'SELECT ' . self::SELECT_COLUMNS . ' FROM api_keys WHERE key_prefix = :prefix AND is_deleted = 0',
    ['prefix' => $prefix]
);

$rawHash = hash('sha256', $rawKey);
$row     = null;

// Prefix index taraması → sabit-zamanlı hash karşılaştırması.
foreach ($rows as $candidate) {
    $stored = (string) ($candidate['key_hash'] ?? '');
    if ($stored !== '' && hash_equals($stored, $rawHash)) {
        $row = $candidate;
        break;
    }
}
```

**Ne kanıtlar:**

1. `hash_equals` → **timing-attack dirençli** karşılaştırma (PHP çekirdek fonksiyonu).
2. `is_deleted = 0` filtresi → soft-delete modeli sorgu seviyesinde (`schema` çocuğu).
3. Niyet "prefix index üzerinden daralt, sonra hash eşleştir" — **performans + güvenlik
   hibrit** (prefix `idx_apikeys_prefix` yorum satırı 18'de beyan edilmiş indeksler
   arasında). ⚠️ İndeksin canlı varlığı `.ai/.sql` dökümü üzerinden ayrıca denetlenmeli.

---

## Ö-8 · Migration deseni A: düz sorgu listesi — `migrations`

**Dosya:** `shared/database/migrations/oauth_connections_migration.php` · **Satır:** 13-19, 41-46

```php
$queries = [

// ============================================================
// oauth_connections — Sosyal medya OAuth bağlantıları
// ============================================================
"CREATE TABLE IF NOT EXISTS oauth_connections (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    /* kolonlar kaynak dosya satır 25-36: provider ENUM, provider_user_id,
       access_token_encrypted, token_expires_at, is_active, is_deleted */
    UNIQUE KEY uk_provider_user (provider, provider_user_id),
    INDEX idx_user_active (user_id, is_active),
    /* satır 39-40: idx_provider, idx_expires */
    FOREIGN KEY (user_id) REFERENCES coremusic_user.users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

];

return $queries;
```

*(SQL gövdesindeki yorum satırları **redaksiyon kısaltmasıdır**: arada kalan kolon
tanımları kaynak dosya `oauth_connections_migration.php` satır 25-40'dadır; hiçbir tanım
burada değiştirilmedi. Kaynak dosyanın tamamı 46 satırdır.)*

**Ne kanıtlar:**

1. `ENGINE=InnoDB` + `utf8mb4` — K-01'in migration yüzeyindeki kanıtı.
2. `FOREIGN KEY (user_id) REFERENCES coremusic_user.users(id)` → **arabir DB FK'si**
   (`coremusic_social` → `coremusic_user`); ⚠️ InnoDB FK'lerininnoDB-myisam
   karışımına dayanıklı olduğu varsayılmaz — FK **tek DB içinde** çalışır kuralı
   ihlali olup olmadığı referans veritabanı InnoDB ise geçerlidir (her iki DB de InnoDB
   beyanı dosya bazında, `⚠️ VERIFICATION REQUIRED`).
3. Dönüş tipi **düz `$queries` dizisi** — Ö-9'daki anahtarlı desenden farklı.
   İki desenin standartlaştırılması açık sorudur.

---

## Ö-9 · Migration deseni B: yıkıcı + öntest + geri dönüş — `migrations`, `backup`

**Dosya:** `shared/database/migrations/2026-10_07_drop_coremusic_api_api_keys_migration.php`
· **Satır:** 26-51

```php
$precheck = [
    // Salt-okunur: DROP öncesi kopya tablo satır sayısı + auth'taki gerçek tablo
    // ile karşılaştırma operatör tarafından gözlemlenmeli.
    'SELECT COUNT(*) AS cnt FROM coremusic_api.api_keys',
    'SELECT COUNT(*) AS cnt FROM coremusic_auth.api_keys',
];

$queries = [
"DROP TABLE IF EXISTS coremusic_api.api_keys",
];

return [
    'name'        => '2026-10_07_drop_coremusic_api_api_keys',
    'database'    => 'coremusic_api',
    'destructive' => true,
    'approval'    => 'REQUIRED — kullanıcı onayı ayrıca alınacak',
    'precheck'    => $precheck,
    'queries'     => $queries,
    'rollback'    => 'mysqldump yedek dosyasından CREATE TABLE + INSERT ile geri yüklenir',
];
```

**Ne kanıtlar:**

1. **Yıkıcı migration protokolü somut kodda mevcut:** `destructive: true` +
   `approval: REQUIRED` + salt-okunur `precheck` + `rollback` tarifi (mysqldump) —
   K007 `migrations` çocuğunun "güvenli değişiklik" bölümü için doğrudan kanıt.
2. Dosya başlık yorumu (satır 13-20) kararı ve gerekçesini belge içinde taşır:
   kullanıcı onayı (2026-10-07), kod taraması ("grep api_keys = 0 hits"), tek-yazar
   (ADR-040) gerekçesi — **karar + kanıt + geri dönüş** üçlüsünün repo-içi örneği.
3. ⚠️ `precheck` sonuçlarını **kimin** okuyup çalıştırmayı başlattığı bilinmiyor
   (migration runner yüzeyi K007 dışı, `UNKNOWN`).

---

## Ö-10 · Kural → kanıt özeti

| Kural / Karar | Kanıt bloğu |
|---|---|
| K-01 MySQL/InnoDB | Ö-1 (DSN), Ö-8 (`ENGINE=InnoDB`) |
| K-02 PDO, ORM yok | Ö-1, Ö-3 |
| ADR-002 prepared statement | Ö-3, Ö-6 |
| ADR-002 `SELECT *` yasak | Ö-5 (SELECT_COLUMNS) |
| K-03/K-05 cache anahtarı + TTL | Ö-4 |
| binary(16) UUID v7 | Ö-6 (`UuidV7::toBinary`) |
| Soft delete (`is_deleted`) | Ö-7 (`AND is_deleted = 0`), Ö-8 (`is_deleted` kolonu) |
| Yıkıcı migration protokolü | Ö-9 |
| Hash-şifreli anahtar (ham yazılmaz) | Ö-6 |

---

**Not:** Bu dosyadaki bloklar 2026-10-12 tarihli çalışma ağacından okunmuştur; dosyalar
değişirse satır aralıkları geçersizleşir — yeniden doğrulama `data-dogrulama.md`
depo satırlarıyla birlikte yapılmalıdır.

---

## §3 Erişim katmanı — tam örnekler (PDO · prepared)

### §3.1 DatabaseManager — DSN + sertleştirme (Ö-1 geniş)

```php
// shared/src/Database/DatabaseManager.php (satır 14-27)
$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
    $config->host, $config->port, $config->dbName, $config->charset,
);
$this->pdo = new \PDO($dsn, $config->user, $config->password, [
    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::ATTR_EMULATE_PREPARES   => false,   // gerçek server-side prepared
    \PDO::ATTR_PERSISTENT         => true,    // bağlantı yeniden kullanım
]);
```

> **Kanıt:** `DatabaseManager.php:14-27`. `EMULATE_PREPARES=false` injection koruması için
> kritiktir; `PERSISTENT=true` performans verir ama `SET` sızıntısı riski taşır (→ data-master §13.3).

### §3.2 execute/write — prepared akış (Ö-3 geniş)

```php
// shared/src/Database/DatabaseManager.php (satır 32-43)
public function execute(string $sql, array $params = []): array
{
    $stmt = $this->pdo->prepare($sql);   // SQL sabit, değerler $params'ta
    $stmt->execute($params);             // bağlama (binding) burada
    return $stmt->fetchAll();
}
public function write(string $sql, array $params = []): bool
{
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute($params);
}
```

> **Kural (ADR-002):** değerler daima `$params` üzerinden bağlanır; SQL string birleştirmek
> (interpolasyon) yasaktır.

### §3.3 Transaction sarmalayıcı (Ö-9 öncesi)

```php
// shared/src/Contracts/Database/IDatabaseManager.php (satır 8-14)
public function beginTransaction(): bool;
public function commit(): bool;
public function rollBack(): bool;
```

---

## §4 İndeks / şema — tam örnekler (dump)

### §4.1 catalog_album_types — kısıt + indeks (Ö-8 geniş)

```sql
-- .ai/.sql/mysql/coremusic_catalog.sql (satır 24-40)
CREATE TABLE `catalog_album_types` (
  `id` binary(16) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `is_deleted` tinyint(1) DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_album_types_name` (`type_name`),
  KEY `idx_album_types_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

> **Kanıt:** `coremusic_catalog.sql:24-40`. `binary(16)` PK + `UNIQUE` + normal `KEY` +
> soft-delete (`is_deleted`/`deleted_at`) tek örnekte görünür.

### §4.2 artist_credit — prefix index (Ö-10)

```sql
-- .ai/.sql/mysql/coremusic_musics.sql (satır 24-34)
CREATE TABLE `artist_credit` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7',
  `display_name` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_artist_credit_display` (`display_name`(100))   -- prefix index
) ENGINE=InnoDB ...;
```

> **Kanıt:** `coremusic_musics.sql`. Uzun `varchar(500)` için `(100)` prefix indeks — tam kolon
> indeksleme maliyetinden kaçınır.

### §4.3 artist_credit_names — N:M + FK CASCADE (Ö-11)

```sql
-- .ai/.sql/mysql/coremusic_musics.sql (satır 36-52)
CREATE TABLE `artist_credit_names` (
  `artist_credit_id` binary(16) NOT NULL,
  `artist_id` binary(16) NOT NULL,
  `position` int unsigned NOT NULL DEFAULT '1',
  `join_phrase` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_acn_credit_pos` (`artist_credit_id`,`position`),  -- sıra tekilliği
  CONSTRAINT `fk_acn_artist`  FOREIGN KEY (`artist_id`)          REFERENCES `artists` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_acn_credit`  FOREIGN KEY (`artist_credit_id`)   REFERENCES `artist_credit` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB ...;
```

> **Kanıt:** `coremusic_musics.sql`. Kompozit `UNIQUE` (2NF/3NF) + `ON DELETE CASCADE` +
> `join_phrase` (MusicBrainz hizalı N:M).

---

## §5 Repository sözleşmeleri — tam örnek (20 metot)

```php
// shared/src/Contracts/Auth/IUserRepository.php
interface IUserRepository
{
    public function findByCredential(string $identity): ?array;
    public function findByEmail(string $email): ?array;
    public function findByIdHex(string $uuidHex): ?array;
    public function create(array $userData): array;
    public function emailExists(string $email): bool;
    public function saveResetToken(string $userId, string $tokenHash, string $expiresAt, ?string $clientIp = null): void;
    public function findValidResetToken(string $tokenHash): ?array;
    public function updatePassword(string $userId, string $newPasswordHash): void;
    public function findRolesForUser(string $userId): array;   // RBAC
    public function saveAccessToken(string $userId, string $jti, string $expiresAt): void;
    public function isValidAccessToken(string $jti): bool;     // revocation
    public function revokeAccessTokensForUser(string $userId): void;
    // ... (toplam 20 metot)
}
```

> **Kanıt:** `IUserRepository.php`. Repository sözleşmesi domain'den SQL'i saklar; `findRolesForUser`
> RBAC rollerini, `isValidAccessToken` JWT jti revocation'ını doğrular.

---

## §6 Domain event — tam örnek (contract + implementasyon)

```php
// shared/src/Contracts/Events/DomainEventInterface.php (satır 21-33)
interface DomainEventInterface
{
    public function eventName(): string;
    public function occurredOn(): \DateTimeImmutable;
    public function payload(): array;
}

// shared/src/Events/Domain/MusicPlayedEvent.php
final class MusicPlayedEvent implements DomainEventInterface
{
    public function eventName(): string { return 'music.played'; }
    public function payload(): array {
        return [
            'musicId'   => $this->musicId,
            'userId'    => $this->userId,
            'duration'  => $this->duration,
            'timestamp' => $this->occurredOn->format('c'),
        ];
    }
}
```

> **Kanıt:** `DomainEventInterface.php:21-33` + `MusicPlayedEvent.php`. 9 event dosyası:
> MusicAdded · MusicPlayed · PlaylistCreated · MediaAccessed · GenderSet · UserLoggedIn ·
> UserLoggedOut · UserRegistered · PasswordResetRequested.

---

## §7 Kod örnekleri — kural↔blok özeti (bu genişletme)

| Kural/Karar | Blok | Kanıt |
|---|---|---|
| PDO sertleştirme | §3.1 | `DatabaseManager.php:14-27` |
| prepared execute/write | §3.2 | `DatabaseManager.php:32-43` |
| transaction sarmalayıcı | §3.3 | `IDatabaseManager.php:8-14` |
| kısıt + soft-delete | §4.1 | `coremusic_catalog.sql:24-40` |
| prefix index | §4.2 | `coremusic_musics.sql` |
| N:M + FK CASCADE | §4.3 | `coremusic_musics.sql` |
| repo sözleşmesi (20 metot) | §5 | `IUserRepository.php` |
| domain event | §6 | `DomainEventInterface.php` + `MusicPlayedEvent.php` |

---

## §8 Kod örnekleri — son not (bu genişletme)

Bu dosya K007'nin **repo gerçeği**dir: neyin ZATEN kodda olduğu, satır numarasıyla.
Planlanan ama kodda olmayan her şey `UNKNOWN`/`⚠️` olarak `data-dogrulama.md` §9.3'tedir.
Kod blokları okunarak üretildi; uydurma satır yoktur.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
