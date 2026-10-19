---
title: "K011 · Queries — Örnekler"
type: architecture
kodeme: kademe-2
module: K011-application
parent: queries
status: active
updated: 2026-10-15
group: kod
---

# K011 · Queries — Örnekler (Gerçek Repo Kodu)

> **Amaç:** `queries` alt-modülünün sorgu kalıplarını **repo'dan okunmuş gerçek
> kod** üzerinden göstermek: PHP (PDO/repository), Vanilla JS (istemci tarafı
> veri çekme), SQL (şema sorgu sözleşmesi) ve CSS (veri durumu token'ları).
> Her blokta `Dosya: … · Satır: …` kanıtı zorunludur; uydurma kod YASAKTIR.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1 PHP — DatabaseManager | kod | php | Bağlantı + yürütme |
| 2 | §2 PHP — ApiKeyRepository | kod | php | Doğrulama sorguları |
| 3 | §3 PHP — OAuthRepository | kod | php | CRUD kalıpları |
| 4 | §4 Vanilla JS — FetchWrapper/ContentFetcher | kod | js | İstemci veri çekme |
| 5 | §5 SQL — Şema sorgu sözleşmesi | kod | sql | İndeks ↔ sorgu |
| 6 | §6 CSS — Veri durumu token'ları | kod | css | Boş/yükleniyor durumları |
| 7 | §7 Anti-pattern YAPMA ↔ YAP | kod | kural | Veto örnekleri |
| 8 | §8 Açık sorular | kod | verification | ⚠️ boşluklar |

---

## §1 PHP — DatabaseManager (bağlantı + yürütme)

### §1.1 Bağlantı kurulumu (doğru — tek nokta)

```php
// Dosya: shared/src/Database/DatabaseManager.php · Satır: 22-27
$this->pdo = new \PDO($dsn, $config->user, $config->password, [
    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::ATTR_EMULATE_PREPARES   => false,
    \PDO::ATTR_PERSISTENT         => true,
]);
```

**Neden doğru:** (1) `ERRMODE_EXCEPTION` — sessiz hata yok; (2) `FETCH_ASSOC` —
tüm repository'ler aynı okuma modu; (3) `EMULATE_PREPARES=false` — sunucu
tarafı gerçek prepared statement (injection savunması); (4) tek `new \PDO` —
bağlantı sahipliği tek elde.

### §1.2 SELECT sarmalayıcı

```php
// Dosya: shared/src/Database/DatabaseManager.php · Satır: 30-36
public function execute(string $sql, array $params = []): array
{
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->fetchAll();
    return $result;
}
```

### §1.3 Yazma sarmalayıcı

```php
// Dosya: shared/src/Database/DatabaseManager.php · Satır: 38-42
public function write(string $sql, array $params = []): bool
{
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute($params);
}
```

### §1.4 Transaction üçlüsü

```php
// Dosya: shared/src/Database/DatabaseManager.php · Satır: 49-62
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
```

---

## §2 PHP — ApiKeyRepository (doğrulama sorguları)

### §2.1 Sabit sütun listesi (`SELECT *` yerine)

```php
// Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 40-41
private const SELECT_COLUMNS =
    'id, user_id, key_hash, key_prefix, key_name, scopes, key_type, rate_limit, is_active, last_used_at, expires_at';
```

**Neden doğru:** `SELECT *` yasak (ADR-002); yalnız ihtiyaç duyulan sütunlar
çekilir — şema değişiklikleri sorguyu kırmaz, transfer küçülür.

### §2.2 Parametreli INSERT

```php
// Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 102-118
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

**Neden doğru:** Tüm değerler named parameter; ham anahtar DB'ye değil, yalnız
`hash('sha256', …)` yazılır (Satır: 108).

### §2.3 Prefix taraması + sabit-zamanlı hash

```php
// Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 160-175
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

### §2.4 Son kullanım tazeleme (UPDATE)

```php
// Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 220-223
$this->db()->write(
    'UPDATE api_keys SET last_used_at = :now, updated_at = :now2 WHERE key_hash = :key_hash AND is_deleted = 0',
    ['now' => $now, 'now2' => $now, 'key_hash' => $rawHash]
);
```

---

## §3 PHP — OAuthRepository (CRUD kalıpları)

### §3.1 Upsert (ON DUPLICATE KEY UPDATE)

```php
// Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 29-47
$sql = 'INSERT INTO oauth_connections
            (user_id, provider, provider_user_id, provider_username,
             access_token_encrypted, refresh_token_encrypted,
             token_expires_at, scopes, profile_data)
         VALUES
            (:user_id, :provider, :provider_user_id, :provider_username,
             :access_token, :refresh_token,
             :expires_at, :scopes, :profile_data)
         ON DUPLICATE KEY UPDATE
            access_token_encrypted = VALUES(access_token_encrypted),
            refresh_token_encrypted = VALUES(refresh_token_encrypted),
            token_expires_at = VALUES(token_expires_at),
            scopes = VALUES(scopes),
            profile_data = VALUES(profile_data),
            is_active = 1,
            updated_at = CURRENT_TIMESTAMP';

$stmt = $this->pdo->prepare($sql);
$stmt->execute($params);
```

### §3.2 Soft delete UPDATE

```php
// Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 69-77
$sql = 'UPDATE oauth_connections
         SET is_deleted = 1, is_active = 0
         WHERE user_id = :user_id AND provider = :provider';

$stmt = $this->pdo->prepare($sql);
return $stmt->execute([
    'user_id' => $userId,
    'provider' => $provider,
]);
```

### §3.3 SQL içi süre (state geçerliliği)

```php
// Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 87-91
$sql = 'INSERT INTO oauth_states (state_token, provider, user_id, code_verifier, expires_at)
         VALUES (:state, :provider, :user_id, :code_verifier, DATE_ADD(NOW(), INTERVAL 10 MINUTE))';

$stmt = $this->pdo->prepare($sql);
$stmt->execute($params);
```

### §3.4 Tek satır fetch + null dönüşü

```php
// Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 97-109
$sql = 'SELECT code_verifier FROM oauth_states
         WHERE state_token = :state AND provider = :provider
           AND user_id = :user_id AND expires_at > NOW()';

$stmt = $this->pdo->prepare($sql);
$stmt->execute([
    'state' => $stateHash,
    'provider' => $provider,
    'user_id' => $userId,
]);

$row = $stmt->fetch(\PDO::FETCH_ASSOC);
return is_array($row) ? $row : null;
```

### §3.5 MusicRepository — JOIN + UNHEX (binary UUID)

```php
// Dosya: home.coremusic.net/include/Repository/MusicRepository.php · Satır: 53-68
if (strlen($musicIdHex) !== 32 || !ctype_xdigit($musicIdHex)) {
    return null;
}

$sql = 'SELECT f.file_path, f.file_format, f.file_size
          FROM music_files f
          JOIN musics m ON m.id = f.music_id
         WHERE f.music_id = UNHEX(:id)
           AND m.is_deleted = 0
           AND f.is_deleted = 0
         ORDER BY f.is_primary DESC, f.id ASC
         LIMIT 1';

$rows = $this->db()->execute($sql, ['id' => $musicIdHex]);

return $rows === [] ? null : $rows[0];
```

**Neden doğru:** (1) Girdi sorgu öncesi doğrulanır; (2) `UNHEX(:id)` — hex→binary
dönüşümü parametre içinde, birleştirme yok; (3) `LIMIT 1` + deterministik
`ORDER BY`; (4) soft-delete filtreleri iki tabloda da var.

---

## §4 Vanilla JS — İstemci Tarafı Veri Çekme

### §4.1 FetchWrapper — timeout + retry + abort

```js
// Dosya: assets.coremusic.net/js/router/FetchWrapper.js · Satır: 5-22
export default class FetchWrapper {
    static CONFIG = Object.freeze({ TIMEOUT_MS: 10_000, MAX_RETRIES: 2, RETRY_DELAY: 500 });
    #controller = null;
    #csrfSync;
    #logger;

    constructor(logger, csrfSync = null) { this.#logger = logger; this.#csrfSync = csrfSync; }

    async fetch(url, options = {}) {
        this.abort();
        this.#controller = new AbortController();
        const { signal: timeoutSignal, clear: clearTimeoutFn } = createTimeout(FetchWrapper.CONFIG.TIMEOUT_MS);
        const combinedSignal = options.signal
            ? combineSignals(options.signal, this.#controller.signal, timeoutSignal)
            : combineSignals(this.#controller.signal, timeoutSignal);
        try { return await this.#fetchWithRetry(url, combinedSignal); }
        finally { clearTimeoutFn(); this.#controller = null; }
    }
```

### §4.2 5xx retry döngüsü

```js
// Dosya: assets.coremusic.net/js/router/FetchWrapper.js · Satır: 24-42
async #fetchWithRetry(url, signal) {
    let lastError = null;
    for (let attempt = 0; attempt <= FetchWrapper.CONFIG.MAX_RETRIES; attempt++) {
        if (signal.aborted) throw new DOMException('Aborted', ABORT_ERROR_NAME);
        try {
            const response = await this.#doFetch(url, signal, attempt);
            if (!response.ok && response.status >= 500 && attempt < FetchWrapper.CONFIG.MAX_RETRIES) {
                await this.#delayWithAbort(FetchWrapper.CONFIG.RETRY_DELAY, signal);
                continue;
            }
            return response;
        } catch (err) {
            lastError = err;
            if (err.name === ABORT_ERROR_NAME) throw err;
            if (attempt < FetchWrapper.CONFIG.MAX_RETRIES) await this.#delayWithAbort(FetchWrapper.CONFIG.RETRY_DELAY, signal);
        }
    }
    throw lastError || new Error('Fetch failed');
}
```

### §4.3 Header + CSRF + traceId ekleme

```js
// Dosya: assets.coremusic.net/js/router/FetchWrapper.js · Satır: 46-53
async #doFetch(url, signal, attempt) {
    const headers = { [HEADERS.X_REQUESTED_WITH]: AJAX_VALUE, [HEADERS.ACCEPT]: CONTENT_TYPES.JSON_HTML };
    const csrfHeader = this.#csrfSync?.getHeader();
    if (csrfHeader) Object.assign(headers, csrfHeader);
    const traceId = this.#logger?.traceId;
    if (traceId) headers[HEADERS.X_TRACE_ID] = traceId;
    return fetch(url, { method: 'GET', headers, credentials: 'include', signal });
}
```

### §4.4 ContentFetcher — content-type'a göre ayrım + cache

```js
// Dosya: assets.coremusic.net/js/router/ContentFetcher.js · Satır: 39-60
static async parseResponse(response, contentType, target, cache, logger) {
    if (contentType.includes(CONTENT_TYPES.JSON)) {
        let json;
        try {
            json = await response.json();
        } catch {
            return null;
        }
        if (json.error === ERROR_TYPES.FORBIDDEN && json.redirect) {
            return { error: ERROR_TYPES.FORBIDDEN, redirect: json.redirect };
        }
        if (json.error === ERROR_TYPES.NOT_FOUND) {
            return { error: ERROR_TYPES.NOT_FOUND };
        }
        const html = json.container || '';
        cache.set(target, { html, meta: json.meta || {} }, json.meta?.cacheable !== false);
        return { html, meta: json.meta || {}, csrfToken: json.csrf_token };
    }

    const html = await response.text();
    cache.set(target, { html, meta: {} }, true);
    return { html, meta: {} };
}
```

**Neden doğru:** (1) `innerHTML` yok — yanıt `container` alanından gelir;
(2) hata tipleri (`forbidden`/`not_found`) ayrıştırılır; (3) `cacheable`
bayrağı cache kararını verir.

---

## §5 SQL — Şema Sorgu Sözleşmesi

### §5.1 `api_keys` — sorgu hedefli indeks seti

```sql
-- Dosya: .ai/.sql/mysql/coremusic_auth.sql · Satır: 56-79
CREATE TABLE `api_keys` (
  `id` binary(16) NOT NULL COMMENT 'UUID v7 primary key',
  `user_id` binary(16) NOT NULL COMMENT 'FK → users.id',
  `key_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SHA-256 hash of API key',
  `key_prefix` varchar(8) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Visible key prefix for identification',
  `key_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Human-readable key label',
  `scopes` json DEFAULT NULL COMMENT 'JSON array of allowed API scopes',
  `rate_limit` int DEFAULT '1000' COMMENT 'Requests per hour limit',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Key active flag',
  `last_used_at` timestamp NULL DEFAULT NULL COMMENT 'Last usage timestamp',
  `expires_at` timestamp NULL DEFAULT NULL COMMENT 'Key expiration timestamp',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Record creation timestamp',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Last update timestamp',
  `is_deleted` tinyint(1) DEFAULT '0' COMMENT 'Soft delete flag',
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete timestamp',
  `key_type` enum('user','service','server') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user' COMMENT 'ADR-020 key yasam dongusu: user|service|server',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_apikeys_hash` (`key_hash`) COMMENT 'Unique key hash lookup',
  KEY `idx_apikeys_user` (`user_id`) COMMENT 'User API keys lookup',
  KEY `idx_apikeys_active` (`is_active`) COMMENT 'Active key filtering',
  KEY `idx_apikeys_prefix` (`key_prefix`) COMMENT 'Key prefix identification',
  KEY `idx_apikeys_type` (`key_type`),
  CONSTRAINT `fk_apikeys_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='API keys — external integration authentication';
```

**Sorgu↔indeks eşlemesi:**

| Sorgu WHERE/UPDATE | İndeks | Satır |
|---|---|---|
| `key_prefix = :prefix` | `idx_apikeys_prefix` | 76 |
| `key_hash = :key_hash` | `idx_apikeys_hash` UNIQUE | 73 |
| `is_deleted = 0` | (filtre — PK aralığı) | 69 |

### §5.2 `api_usage` — zaman serisi sorgu sözleşmesi

```sql
-- Dosya: .ai/.sql/mysql/coremusic_auth.sql · Satır: 81-98 (özet)
CREATE TABLE `api_usage` (
  `id` binary(16) NOT NULL,
  `api_key_id` binary(16) NOT NULL,
  `endpoint` varchar(500) NOT NULL,
  `method` enum('GET','POST','PUT','DELETE','PATCH') NOT NULL,
  `status_code` int DEFAULT NULL,
  `response_time_ms` int DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `request_body_hash` varchar(64) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_apiusage_key` (`api_key_id`),
  KEY `idx_apiusage_created` (`created_at`),
  KEY `idx_apiusage_endpoint` (`endpoint`),
  KEY `idx_apiusage_method` (`method`),
  CONSTRAINT `fk_apiusage_key` FOREIGN KEY (`api_key_id`) REFERENCES `api_keys` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='API usage log — requests, responses, analytics';
```

> ⚠️ VERIFICATION REQUIRED: bu tabloyu yazan PHP kodu bu oturumda okunmadı
> (§8-Q1) — şema sözleşmesi mevcut, yazıcı bilinmiyor.

---

## §6 CSS — Veri Durumu Token'ları (queries ile ilişkisi)

> **İlişki:** Sorgu sonuçlarının sunum durumları (boş/yükleniyor/hata) CSS
> token katmanında tanımlanır. Aşağıdaki blok repo'dan okunmuş gerçek
> token dosyasıdır; sorgu arayüzlerinin cihaz kırılımlarını besler.

```css
/* Dosya: assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css · Satır: 22-34 */
:root {
  /* ============================================================
     DEVICE BREAKPOINTS (px cinsinden — media query için)
     device-loader.js ile senkronize
     ============================================================ */
  --bp-device-phone:      767px;
  --bp-device-tablet:     1024px;
  --bp-device-embedded:   1024px;   /* RPi5: 1024x600 */
  --bp-device-laptop:     1440px;
  --bp-device-medium:     1919px;   /* Medium desktop: 1441-1919px */
  --bp-device-desktop:    2560px;
  --bp-device-4k-tv:      3840px;
  --bp-device-4k-monitor: 3841px;
```

```css
/* Dosya: assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css · Satır: 45-56 */
  --bp-1024: 1024px;  /* = --bp-device-tablet = --bp-device-embedded */
  --bp-1920: 1920px;  /* wide desktop alt sınırı */

  /* ============================================================
     DEVICE BREAKPOINTS (integer — JS hesaplama için)
     ============================================================ */
  --bp-embedded:   1024;
  --bp-laptop:     1440;
  --bp-medium:     1919;
  --bp-desktop:    2560;
  --bp-4k-tv:      3840;
  --bp-4k-monitor: 3841;
}
```

**Kural (dosya içi not — Satır: 39-40):** `@media` içinde `var()` KULLANILAMAZ;
bu nedenle hem token (`--bp-1024`) hem aynı değerin `@media` yorumu birlikte
taşınır. Sorgu arayüzü responsive kırılımlarında bu eşleşme korunur.

---

## §7 Anti-Pattern YAPMA ↔ YAP

| # | YAPMA | YAP | Kanıt |
|---|---|---|---|
| 1 | `$pdo->query("SELECT … $id")` | `$this->db()->execute($sql, ['id' => $id])` | `DatabaseManager.php` L32 |
| 2 | `SELECT * FROM api_keys` | `SELECT <SELECT_COLUMNS> FROM api_keys` | `ApiKeyRepository.php` L40, L161 |
| 3 | `==` ile hash karşılaştırma | `hash_equals($stored, $rawHash)` | `ApiKeyRepository.php` L171 |
| 4 | `DELETE FROM api_keys WHERE …` | `UPDATE … SET is_deleted = 1` | `OAuthRepository.php` L69 |
| 5 | `WHERE id = '$hex'` | `WHERE music_id = UNHEX(:id)` | `MusicRepository.php` L60 |
| 6 | Repository'de `new \PDO(...)` | `IDatabaseRegistry::get($dbKey)` | `ApiKeyRepository.php` L50 |
| 7 | `PDO::ERRMODE_SILENT` | `PDO::ERRMODE_EXCEPTION` | `DatabaseManager.php` L23 |
| 8 | `EMULATE_PREPARES => true` | `EMULATE_PREPARES => false` | `DatabaseManager.php` L25 |
| 9 | JS: `innerHTML = response` | `json.container` alanından DOMParser kalıbı | `ContentFetcher.js` L53 |
| 10 | JS: timeout yok | `createTimeout(TIMEOUT_MS)` + abort | `FetchWrapper.js` L16 |

---

## §8 Açık Sorular

| # | Soru | Durum |
|---|---|---|
| 1 | `api_usage` INSERT örneği hangi dosyada? | ⚠️ VERIFICATION REQUIRED |
| 2 | `write()` çağrısını test eden PHPUnit örneği var mı? | ⚠️ VERIFICATION REQUIRED |
| 3 | `CatalogWriter::upsertSql()` şablonu nasıl görünüyor? | ⚠️ VERIFICATION REQUIRED |
| 4 | Sorgu sonucu DTO'ya dönüşümü hangi katmanda? (repository `array` döndürüyor) | ⚠️ VERIFICATION REQUIRED |
| 5 | `MusicRepository::findRecentTracks` LIMIT değeri? (okuma L90'da kesildi) | ⚠️ VERIFICATION REQUIRED |
| 6 | CSS veri-durumu token'ları (skeleton/empty) ayrı dosyada mı? | ⚠️ VERIFICATION REQUIRED |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
