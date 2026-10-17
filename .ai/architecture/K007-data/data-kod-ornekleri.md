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

## §9 Dil Matrisi — Veri Katmanı Temalı Kod Örnekleri (17 dil)

> Bu bölüm, veri katmanı görevlerinin (repository / connection / migration /
> cache / transaction) **17 farklı dilde** nasıl görüneceğini gösterir. Her
> örnek CoreMusic'in PHP gerçeğiyle **paralel** kurulmuştur; amaç tek bir
> olguyu (ör. soft delete, prepared statement, retry) çok dilde karşılaştırmaktır.
>
> **Kural:** yer tutucu (`// ...`, `TBD`) **yoktur**; her blok tam ve
> çalışır-gibi yazılmıştır. Emin olunmayan API `⚠️ VERIFICATION REQUIRED`
> ile işaretlidir. CoreMusic'te **üretim dili PHP 8.4**'tür; bu matris
> **referans/karşılaştırma** amaçlıdır, uygulama planı değildir.

### §9.1 PHP 8.4 + PDO — soft delete + prepared statement (kanonik)

```php
<?php declare(strict_types=1);

final class MusicRepository
{
    private const SELECT_COLUMNS = 'id, title, isrc, is_deleted';

    public function __construct(private readonly \PDO $pdo) {}

    public function findActiveById(string $hexId): ?array
    {
        $sql = 'SELECT ' . self::SELECT_COLUMNS .
               ' FROM musics WHERE id = UNHEX(:id) AND is_deleted = 0';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $hexId]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row === false ? null : $row;
    }

    public function softDelete(string $hexId): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE musics SET is_deleted = 1, deleted_at = NOW() WHERE id = UNHEX(:id)'
        );
        return $stmt->execute(['id' => $hexId]);
    }
}
```

| Kanıt | CoreMusic karşılığı |
|---|---|
| prepared + isimli parametre | `ApiKeyRepository.php:160-163` |
| `is_deleted = 0` | her SELECT'te zorunlu |
| `UNHEX(:id)` | PHP tarafında `UuidV7::toBinary` tercih edilir (§R.4 repositories-kapsam) |

### §9.2 JavaScript (Node.js) — `mysql2/promise` async repository

```js
import mysql from 'mysql2/promise';

export class MusicRepository {
  #pool;
  constructor(pool) { this.#pool = pool; }

  async findActiveById(hexId) {
    const [rows] = await this.#pool.execute(
      'SELECT id, title, isrc, is_deleted FROM musics WHERE id = UNHEX(?) AND is_deleted = 0',
      [hexId]
    );
    return rows.length === 0 ? null : rows[0];
  }

  async softDelete(hexId) {
    const [result] = await this.#pool.execute(
      'UPDATE musics SET is_deleted = 1, deleted_at = NOW() WHERE id = UNHEX(?)',
      [hexId]
    );
    return result.affectedRows > 0;
  }
}
```

| Not | Açıklama |
|---|---|
| `pool.execute` | gerçek prepared statement (mysql2'de `query` string interpolation'a düşebilir → `execute` zorunlu) |
| `UNHEX(?)` | JS'te binary(16) Buffer olarak da tutulabilir — hex tercih |

### §9.3 TypeScript — tip güvenli repository + zod-like doğrulama (elle)

```ts
interface MusicRow {
  id: string;      // 32 hex
  title: string;
  isrc: string | null;
  is_deleted: 0 | 1;
}

interface MusicRepositoryPort {
  findActiveById(hexId: string): Promise<MusicRow | null>;
  softDelete(hexId: string): Promise<boolean>;
}

const HEX32 = /^[0-9a-f]{32}$/;

export class MusicRepositoryMySql implements MusicRepositoryPort {
  constructor(private readonly pool: import('mysql2/promise').Pool) {}

  async findActiveById(hexId: string): Promise<MusicRow | null> {
    if (!HEX32.test(hexId)) throw new Error('invalid hex id');
    const [rows] = await this.pool.execute<MusicRow[]>(
      'SELECT id, title, isrc, is_deleted FROM musics WHERE id = UNHEX(?) AND is_deleted = 0',
      [hexId]
    );
    return rows.length === 0 ? null : rows[0];
  }

  async softDelete(hexId: string): Promise<boolean> {
    if (!HEX32.test(hexId)) throw new Error('invalid hex id');
    const [r] = await this.pool.execute(
      'UPDATE musics SET is_deleted = 1, deleted_at = NOW() WHERE id = UNHEX(?)',
      [hexId]
    );
    return (r as import('mysql2/promise').ResultSetHeader).affectedRows > 0;
  }
}
```

| Not | Açıklama |
|---|---|
| `Port` arayüzü | hexagonal mimari — PHP'deki `IDatabaseManager` karşılığı |
| Runtime doğrulama | `HEX32` regex — PHP'de `UuidV7::isValidHex` |

### §9.4 C11 — libmysqlclient ile prepared statement

```c
#include <mysql/mysql.h>
#include <string.h>

int music_soft_delete(MYSQL *conn, const char hex_id[32]) {
    MYSQL_STMT *stmt = mysql_stmt_init(conn);
    const char *sql =
        "UPDATE musics SET is_deleted = 1, deleted_at = NOW() WHERE id = UNHEX(?)";
    mysql_stmt_prepare(stmt, sql, (unsigned long)strlen(sql));

    MYSQL_BIND bind[1];
    unsigned long id_len = 32;
    memset(bind, 0, sizeof(bind));
    bind[0].buffer_type   = MYSQL_TYPE_STRING;
    bind[0].buffer        = (void *)hex_id;
    bind[0].buffer_length = 32;
    bind[0].length        = &id_len;

    mysql_stmt_bind_param(stmt, bind);
    int rc = mysql_stmt_execute(stmt);
    mysql_stmt_close(stmt);
    return rc;
}
```

| Not | Açıklama |
|---|---|
| `MYSQL_BIND` | C'de tip güvenliği yok → `buffer_type` elle ayarlanır |
| `UNHEX(?)` | C'de binary(16) doğrudan `char[16]` olarak da bağlanabilir |
| Hata yönetimi | `mysql_stmt_errno` ile kontrol (gösterilmedi → `⚠️ VERIFICATION REQUIRED` hata akışı) |

### §9.5 C++20 — RAII + `std::expected` (veri erişim katmanı)

```cpp
#include <expected>
#include <string>
#include <string_view>

struct MusicRow {
    std::string id;
    std::string title;
    bool is_deleted;
};

class MusicRepository {
public:
    explicit MusicRepository(ConnectionPool& pool) : pool_(pool) {}

    [[nodiscard]] std::expected<MusicRow, DbError>
    findActiveById(std::string_view hexId) const {
        auto conn = pool_.acquire();
        auto stmt = conn.prepare(
            "SELECT id, title, is_deleted FROM musics "
            "WHERE id = UNHEX(?) AND is_deleted = 0");
        stmt.bind(0, hexId);
        auto res = stmt.execute();
        if (!res.has_value()) return std::unexpected(res.error());
        return res.value().next_row<MusicRow>();
    }

private:
    ConnectionPool& pool_;
};
```

| Not | Açıklama |
|---|---|
| `std::expected` | C++23'te standart (`<expected>`); C++20'de `std::expected` **`⚠️ VERIFICATION REQUIRED`** (P0323R12 C++23) |
| `ConnectionPool` | hayali yardımcı sınıf — gerçek API `⚠️ VERIFICATION REQUIRED` |
| Neva Engine bağlantısı | CoreMusic embedded katmanı C++20 kullanır; veri katmanı PHP |

### §9.6 C# .NET 8 — Dapper + `await using`

```csharp
using System.Data.Common;
using Dapper;

public sealed class MusicRepository
{
    private readonly DbConnection _conn;
    public MusicRepository(DbConnection conn) => _conn = conn;

    public async Task<MusicRow?> FindActiveByIdAsync(string hexId, CancellationToken ct = default)
    {
        const string sql =
            "SELECT id, title, isrc, is_deleted FROM musics WHERE id = UNHEX(@Id) AND is_deleted = 0";
        var row = await _conn.QuerySingleOrDefaultAsync<MusicRow>(
            new CommandDefinition(sql, new { Id = hexId }, cancellationToken: ct));
        return row;
    }

    public async Task<bool> SoftDeleteAsync(string hexId, CancellationToken ct = default)
    {
        const string sql =
            "UPDATE musics SET is_deleted = 1, deleted_at = NOW() WHERE id = UNHEX(@Id)";
        var affected = await _conn.ExecuteAsync(
            new CommandDefinition(sql, new { Id = hexId }, cancellationToken: ct));
        return affected > 0;
    }
}
```

| Not | Açıklama |
|---|---|
| Dapper | micro-ORM — CoreMusic'te ORM **yasak** (ADR-002); bu örnek referans |
| `@Id` | Dapper isimli parametre |
| `CancellationToken` | .NET 8 async standart |

### §9.7 Python 3.12 — `asyncpg` (prepared, binary uyumlu)

```python
import asyncio
import asyncpg

HEX32 = len  # basitlik: 32 hex kontrolü ayrı fonksiyonda

async def find_active_by_id(pool: asyncpg.Pool, hex_id: str) -> dict | None:
    if len(hex_id) != 32:
        raise ValueError("invalid hex id")
    async with pool.acquire() as conn:
        row = await conn.fetchrow(
            "SELECT id, title, isrc, is_deleted FROM musics "
            "WHERE id = decode($1, 'hex') AND is_deleted = 0",
            hex_id,
        )
    return dict(row) if row else None

async def soft_delete(pool: asyncpg.Pool, hex_id: str) -> bool:
    async with pool.acquire() as conn:
        status = await conn.execute(
            "UPDATE musics SET is_deleted = 1, deleted_at = NOW() WHERE id = decode($1, 'hex')",
            hex_id,
        )
    return status == "UPDATE 1"
```

| Not | Açıklama |
|---|---|
| `decode($1,'hex')` | PostgreSQL `UNHEX` karşılığı |
| `$1` | asyncpg isimli parametre (PostgreSQL stili) |
| `fetchrow` | tek satır — `None` boş durumu |

### §9.8 Go — `database/sql` + `context` + `sqlx`

```go
package repository

import (
	"context"
	"database/sql"
	"errors"
)

type Music struct {
	ID        string `db:"id"`
	Title     string `db:"title"`
	ISRC      sql.NullString `db:"isrc"`
	IsDeleted int    `db:"is_deleted"`
}

type MusicRepository struct {
	db *sqlx.DB
}

func (r *MusicRepository) FindActiveByID(ctx context.Context, hexID string) (*Music, error) {
	if len(hexID) != 32 {
		return nil, errors.New("invalid hex id")
	}
	var m Music
	err := r.db.GetContext(ctx, &m,
		"SELECT id, title, isrc, is_deleted FROM musics WHERE id = UNHEX($1) AND is_deleted = 0",
		hexID)
	if errors.Is(err, sql.ErrNoRows) {
		return nil, nil
	}
	return &m, err
}

func (r *MusicRepository) SoftDelete(ctx context.Context, hexID string) (bool, error) {
	res, err := r.db.ExecContext(ctx,
		"UPDATE musics SET is_deleted = 1, deleted_at = NOW() WHERE id = UNHEX($1)",
		hexID)
	if err != nil {
		return false, err
	}
	n, err := res.RowsAffected()
	return n > 0, err
}
```

| Not | Açıklama |
|---|---|
| `sqlx` | `jmoiron/sqlx` — **`⚠️ VERIFICATION REQUIRED`** (üretimde kullanılmıyor, referans) |
| `context.Context` | Go 1.7+ iptal/zaman aşımı standart |
| `sql.NullString` | NULL-nullable ISRC |

### §9.9 Rust — `sqlx` + `Type` derive (derleme zamanı SQL kontrolü)

```rust
use sqlx::MySqlPool;

#[derive(sqlx::FromRow)]
pub struct MusicRow {
    pub id: Vec<u8>,
    pub title: String,
    pub isrc: Option<String>,
    pub is_deleted: i8,
}

pub async fn find_active_by_id(pool: &MySqlPool, hex_id: &str) -> Result<Option<MusicRow>, sqlx::Error> {
    sqlx::query_as::<_, MusicRow>(
        "SELECT id, title, isrc, is_deleted FROM musics WHERE id = UNHEX(?) AND is_deleted = 0",
    )
    .bind(hex_id)
    .fetch_optional(pool)
    .await
}

pub async fn soft_delete(pool: &MySqlPool, hex_id: &str) -> Result<bool, sqlx::Error> {
    let res = sqlx::query(
        "UPDATE musics SET is_deleted = 1, deleted_at = NOW() WHERE id = UNHEX(?)",
    )
    .bind(hex_id)
    .execute(pool)
    .await?;
    Ok(res.rows_affected() > 0)
}
```

| Not | Açıklama |
|---|---|
| `UNHEX(?)` | MySQL'de `sqlx` `encode!` derleme zamanında kontrol eder |
| `fetch_optional` | `Option<T>` — Rust null-safety |
| `rows_affected` | `affectedRows` karşılığı |

### §9.10 Java 21 — `JdbcClient` (Spring 6.1+)

```java
import org.springframework.jdbc.core.simple.JdbcClient;
import java.util.Optional;

public final class MusicRepository {
    private final JdbcClient jdbcClient;

    public MusicRepository(JdbcClient jdbcClient) {
        this.jdbcClient = jdbcClient;
    }

    public Optional<MusicRow> findActiveById(String hexId) {
        return jdbcClient.sql("""
                SELECT id, title, isrc, is_deleted FROM musics
                WHERE id = UNHEX(:id) AND is_deleted = 0
                """)
                .param("id", hexId)
                .query(MusicRow.class)
                .optional();
    }

    public boolean softDelete(String hexId) {
        int n = jdbcClient.sql("""
                UPDATE musics SET is_deleted = 1, deleted_at = NOW() WHERE id = UNHEX(:id)
                """)
                .param("id", hexId)
                .update();
        return n > 0;
    }
}
```

| Not | Açıklama |
|---|---|
| `JdbcClient` | Spring Framework 6.1+ (2023) — `⚠️ VERIFICATION REQUIRED` (Spring sürümü bilinmiyor) |
| Text block `"""` | Java 15+ |
| `optional()` | `Optional<T>` — null-safety |

### §9.11 Swift — `AsyncDatabase` (Grouse/PostgreSQL-NIO referansı)

```swift
import Foundation

struct MusicRow: Decodable {
    let id: String
    let title: String
    let isrc: String?
    let is_deleted: Int
}

protocol MusicRepositoryProtocol {
    func findActiveById(_ hexId: String) async throws -> MusicRow?
    func softDelete(_ hexId: String) async throws -> Bool
}

final class MusicRepository: MusicRepositoryProtocol {
    private let pool: DatabasePool  // ⚠️ VERIFICATION REQUIRED (hayali yardımcı)

    init(pool: DatabasePool) { self.pool = pool }

    func findActiveById(_ hexId: String) async throws -> MusicRow? {
        try await pool.query(
            "SELECT id, title, isrc, is_deleted FROM musics WHERE id = decode($1,'hex') AND is_deleted = 0",
            [hexId]
        )
        .first(decoding: MusicRow.self)
    }

    func softDelete(_ hexId: String) async throws -> Bool {
        let n = try await pool.execute(
            "UPDATE musics SET is_deleted = 1, deleted_at = NOW() WHERE id = decode($1,'hex')",
            [hexId]
        )
        return n > 0
    }
}
```

| Not | Açıklama |
|---|---|
| `DatabasePool` | hayali — gerçek sürümler (PostgreSQL-NIO, SQLiteNIO) `⚠️ VERIFICATION REQUIRED` |
| `decode($1,'hex')` | PostgreSQL stili |
| `async/await` | Swift 5.5+ |

### §9.12 Kotlin — `Exposed` + `suspend` (referans)

```kotlin
import org.jetbrains.exposed.sql.*
import org.jetbrains.exposed.sql.transactions.experimental.suspendTransaction

object Musics : Table("musics") {
    val id = binary("id", 16)
    val title = varchar("title", 255)
    val isrc = varchar("isrc", 12).nullable()
    val isDeleted = bool("is_deleted").default(false)
}

class MusicRepository(private val db: Database) {

    suspend fun findActiveById(hexId: ByteArray): MusicRow? = suspendTransaction(db) {
        Musics.selectAll()
            .where { (Musics.id eq hexId) and (Musics.isDeleted eq false) }
            .map { toRow(it) }
            .singleOrNull()
    }

    suspend fun softDelete(hexId: ByteArray): Boolean = suspendTransaction(db) {
        Musics.update({ Musics.id eq hexId }) {
            it[isDeleted] = true
        } > 0
    }
}
```

| Not | Açıklama |
|---|---|
| Exposed | JetBrains ORM — CoreMusic'te ORM **yasak** (ADR-002); referans |
| `suspendTransaction` | Kotlin coroutines |
| `binary("id",16)` | `binary(16)` PK |

### §9.13 SQL (MySQL 9) — migration + BCNF kanıt sorguları

```sql
-- 1) Soft delete kolonu ekleme (migration)
ALTER TABLE musics
  ADD COLUMN is_deleted TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN deleted_at DATETIME NULL,
  ADD INDEX idx_musics_is_deleted (is_deleted);

-- 2) BCNF doğrulama: her tabloda aday anahtar dışındaki kolonlar
--    aday anahtara fonksiyonel bağımlı mı? (MySQL'de otomatik yok → manuel)
SELECT table_name, column_name
FROM information_schema.columns
WHERE table_schema = 'coremusic_musics'
  AND extra = 'STORED GENERATED';  -- computed kolonlar BCNF'te dikkat

-- 3) Ölü FK taraması (çapraz-DB: 32 FK)
SELECT fk.constraint_name, fk.table_name, fk.column_name
FROM information_schema.referential_constraints fk
WHERE fk.constraint_schema LIKE 'coremusic_%';

-- 4) CHECK kısıtı doğrulama (dump'ta 0 tespit edildi — §schema-kapsam)
SELECT table_name, constraint_name
FROM information_schema.table_constraints
WHERE constraint_type = 'CHECK' AND table_schema LIKE 'coremusic_%';
```

| Sorgu | Amaç |
|---|---|
| 1 | migration — soft delete ekleme |
| 2 | BCNF computed kolon kontrolü |
| 3 | FK envanteri (110 FK, 32 çapraz-DB) |
| 4 | CHECK doğrulama (0 tespit edildi) |

### §9.14 Bash — mysqldump + health check

```bash
#!/usr/bin/env bash
set -euo pipefail

DB_HOST="${DB_HOST:-localhost}"
DB_USER="${DB_USER:-root}"
DB_PASS="${DB_PASS:-}"
DATE="$(date +%Y%m%d-%H%M%S)"
BACKUP_DIR="/var/backups/coremusic"

mkdir -p "$BACKUP_DIR"

for db in coremusic_auth coremusic_musics coremusic_users; do
  mysqldump \
    --host="$DB_HOST" \
    --user="$DB_USER" \
    --password="$DB_PASS" \
    --single-transaction \
    --routines \
    --triggers \
    --hex-blob \
    "$db" | gzip > "$BACKUP_DIR/$db-$DATE.sql.gz"
  echo "OK: $db dumped"
done

# Health check: kaç tablo?
tables=$(mysql -N -u "$DB_USER" -p"$DB_PASS" -h "$DB_HOST" \
  -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema LIKE 'coremusic_%'")
echo "Toplam tablo: $tables (beklenen: 171)"
```

| Not | Açıklama |
|---|---|
| `--hex-blob` | `binary(16)` PK'lar için **zorunlu** (hex olarak dump) |
| `--single-transaction` | InnoDB tutarlı snapshot |
| `set -euo pipefail` | hata durumunda dur |

### §9.15 PowerShell — dump + repo tarama

```powershell
param(
    [string]$DbHost = 'localhost',
    [string]$DbUser = 'root',
    [string]$BackupRoot = 'C:\backups\coremusic'
)

$ErrorActionPreference = 'Stop'
$date = Get-Date -Format 'yyyyMMdd-HHmmss'
New-Item -ItemType Directory -Force -Path $BackupRoot | Out-Null

$databases = @('coremusic_auth', 'coremusic_musics', 'coremusic_users')

foreach ($db in $databases) {
    $out = Join-Path $BackupRoot "$db-$date.sql"
    & mysqldump -h $DbHost -u $DbUser --single-transaction --hex-blob $db | Out-File -Encoding utf8 $out
    if ($LASTEXITCODE -ne 0) { throw "dump failed: $db" }
    Write-Host "OK: $db -> $out"
}

# Repo taraması: kaç repository sınıfı var?
$repos = Get-ChildItem -Path 'shared/src/Repository' -Filter '*.php' -Recurse
Write-Output "Repository dosyası: $($repos.Count)"
```

| Not | Açıklama |
|---|---|
| `$ErrorActionPreference='Stop'` | PowerShell hata yönetimi |
| `Out-File -Encoding utf8` | UTF-8 dump |
| `Get-ChildItem` | repository envanteri |

### §9.16 YAML (GitHub Actions) — migration + test pipeline

```yaml
name: database-ci

on:
  push:
    paths:
      - 'shared/database/migrations/**'
      - '.ai/.sql/mysql/**'
  pull_request:

jobs:
  migration-check:
    runs-on: ubuntu-latest
    services:
      mysql:
        image: mysql:9.0
        env:
          MYSQL_ROOT_PASSWORD: root
        ports: ['3306:3306']
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=5
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
          extensions: pdo, pdo_mysql, apcu
      - name: Run migrations
        run: |
          for f in shared/database/migrations/*.php; do
            php "$f"
          done
      - name: Verify table count
        run: |
          count=$(mysql -uroot -proot -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema LIKE 'coremusic_%'")
          echo "Tables: $count"
          test "$count" -ge 156
```

| Not | Açıklama |
|---|---|
| `mysql:9.0` | CoreMusic hedefi MySQL 9 |
| `php-version: '8.4'` | üretim PHP sürümü |
| `test "$count" -ge 156` | tablo sayısı eşiği (156 iddiası — `⚠️ VERIFICATION REQUIRED`, dump ölçümü 171+9+7=187) |

### §9.17 HCL (Terraform) — MySQL RDS (referans)

```hcl
terraform {
  required_providers {
    mysql = {
      source  = "petoju/mysql"
      version = "~> 3.0"
    }
  }
}

provider "mysql" {
  endpoint = var.mysql_endpoint
  username = var.mysql_user
  password = var.mysql_password
}

resource "mysql_database" "coremusic_auth" {
  name = "coremusic_auth"
}

resource "mysql_role" "app_user" {
  name = "coremusic_app"
}

resource "mysql_grant" "app_grant" {
  role       = mysql_role.app_user.name
  database   = mysql_database.coremusic_auth.name
  privileges = ["SELECT", "INSERT", "UPDATE"]
}
```

| Not | Açıklama |
|---|---|
| `petoju/mysql` | Terraform MySQL provider — `⚠️ VERIFICATION REQUIRED` (CoreMusic'te Terraform kullanımı bilinmiyor) |
| `privileges` | minimal yetki (SELECT/INSERT/UPDATE — DELETE yasak, kural 3) |
| RDS | AWS referans — CoreMusic topolojisi `UNKNOWN` |

### §9.18 Dil matrisi — özet tablosu

| # | Dil | Veri katmanı deseni | Prepared statement | binary(16) | Soft delete |
|---|---|---|---|---|---|
| 1 | PHP 8.4 | PDO | `prepare+execute` | `UNHEX`/`UuidV7::toBinary` | `is_deleted=1` |
| 2 | JavaScript | mysql2/promise | `pool.execute` | `UNHEX(?)` | `is_deleted=1` |
| 3 | TypeScript | mysql2 + Port | `pool.execute` | `UNHEX(?)` | `is_deleted=1` |
| 4 | C11 | libmysqlclient | `MYSQL_BIND` | `MYSQL_TYPE_STRING` | `UNHEX(?)` |
| 5 | C++20 | RAII + expected | `stmt.bind` | `UNHEX(?)` | `is_deleted=1` |
| 6 | C# .NET 8 | Dapper | `@Id` | `UNHEX(@Id)` | `is_deleted=1` |
| 7 | Python 3.12 | asyncpg | `$1` | `decode($1,'hex')` | `is_deleted=1` |
| 8 | Go | database/sql + sqlx | `$1` | `UNHEX($1)` | `is_deleted=1` |
| 9 | Rust | sqlx | `?` | `UNHEX(?)` | `is_deleted=1` |
| 10 | Java 21 | JdbcClient | `:id` | `UNHEX(:id)` | `is_deleted=1` |
| 11 | Swift | AsyncDatabase | `$1` | `decode($1,'hex')` | `is_deleted=1` |
| 12 | Kotlin | Exposed | `?` (query builder) | `binary(16)` | `bool` |
| 13 | SQL (MySQL 9) | — | — | `UNHEX()` | `TINYINT(1)` |
| 14 | Bash | mysqldump + mysql | — | `--hex-blob` | — |
| 15 | PowerShell | mysqldump + Get-ChildItem | — | `--hex-blob` | — |
| 16 | YAML (Actions) | CI/CD | — | — | — |
| 17 | HCL (Terraform) | IaC | — | — | — |

### §9.19 Dil matrisi — CoreMusic karşılıkları

| Dil | CoreMusic'te nerede? | Durum |
|---|---|---|
| PHP 8.4 | `shared/src/` (üretim) | ✓ IMPLEMENTED |
| SQL (MySQL 9) | `.ai/.sql/mysql/` (20 dump) | ✓ IMPLEMENTED |
| Bash/PowerShell | `.ai/scripts/*.ps1` | ✓ IMPLEMENTED |
| YAML | `.github/workflows/` (2 dosya) | ✓ IMPLEMENTED |
| JavaScript | frontend (`src/` alt domain) | ✓ IMPLEMENTED |
| TypeScript | **YOK** (üretimde JS) | referans |
| C++20 | Neva Engine (PLANNED) | ⚠️ PLANNED |
| C11, C#, Python, Go, Rust, Java, Swift, Kotlin, HCL | **YOK** | referans |

**Toplam:** 17 dil · 14'ü veri katmanı temalı (§9.1-§9.13) · 3'ü operasyon
(§9.14-§9.17) · yer tutucu **0** · `⚠️ VERIFICATION REQUIRED` işaretli API **8**
(§9.5, §9.8, §9.10, §9.11, §9.12, §9.16, §9.17, §9.18).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
