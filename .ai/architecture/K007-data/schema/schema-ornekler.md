---
node_id: "K007-schema-ornekler"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K008-service-layer"
evidence:
  - "shared/src/Database/DatabaseManager.php:14-46 — DSN/prepare/execute gerçek kod"
  - "shared/database/migrations/oauth_connections_migration.php:11-41 — CREATE TABLE + INSERT"
  - "shared/src/Repository/ApiKeyRepository.php:40-41 — sabit SELECT_COLUMNS"
validation: "6 örnek; her biri gerçek dosya:satır ile; 8/8 alan; yasak betim yok"
open_questions:
  - "S-1 — arama sahipliği K007 mi?"
  - "K-001 — forum şeması T-11/T-20 kapsamı UNKNOWN"
group: kod
---

# K007-SHEMA — Örnekler

> **Kaynak kuralı:** Tüm örnekler diskten okunmuş gerçek koddur (satır
> numarasıyla). Kök şablon örnekleri: `../data-kod-ornekleri.md` Ö-1..Ö-9.

## Ö-S1 — DSN ve Charset (Kanıt: `DatabaseManager.php:14-20`)

```php
$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
    $config->host,
    $config->port,
    $config->dbName,
    $config->charset,
);
```

**Neden doğru:** charset DSN içinde parametreli — bağlantıyla birlikte gelir
(PDO `charset` DSN parçası, `schema-web-arastirma.md` #9). `utf8mb4` kanunu
`.ai/.sql/CLAUDE.md §1` ile uyumlu (`$config->charset` değeri `UNKNOWN` —
`DatabaseConfig` sabit okunmadı).

## Ö-S2 — PDO Seçenekleri (Kanıt: `DatabaseManager.php:22-27`)

```php
$this->pdo = new \PDO($dsn, $config->user, $config->password, [
    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::ATTR_EMULATE_PREPARES   => false,
    \PDO::ATTR_PERSISTENT         => true,
]);
```

**Yorum:** `EMULATE_PREPARES => false` → gerçek prepared statement (ADR-002
"PDO prepared statement yalnız" ile güçlenir; sunucu tarafı plan cache'i).
`PERSISTENT => true` → bağlantı yeniden kullanımı (bağlantı havuzu riski
opsiyonel, `transactions` alt alanının konusu).

## Ö-S3 — Parametreli Yazma (Kanıt: `DatabaseManager.php:30-42`)

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

**Şema bağlantısı:** sorgu gövdesi çağrı yerinde (repository), şema bilgisi
yalnız sütun adlarında — şema değişikliği repository kırılganlığı yaratır
(K-SCOPE-1 riski, `schema-dogrulama.md` §4).

## Ö-S4 — CREATE TABLE Şablonu (Kanıt: `oauth_connections_migration.php:14-24`)

```php
$queries[] = "CREATE TABLE IF NOT EXISTS oauth_connections (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    platform VARCHAR(32) NOT NULL,
    UNIQUE KEY uq_user_platform (user_id, platform),
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
```

**Doğrulama notları:** `VARCHAR(32)` limitli (#2 kanunu) · `NOT NULL` ·
`UNIQUE KEY` çakışma engeli · `PRIMARY KEY` tablo başına 1 (#11) ·
`utf8mb4` charset (#3/#8). Tam satırlar dosyada 11-41.

## Ö-S5 — Şema Türetme (Sabit Sütun Listesi) (Kanıt: `ApiKeyRepository.php:40-41`)

```php
private const SELECT_COLUMNS = "id, client_id, name, scopes, created_at, last_used_at";
```

**Neden doğru:** `SELECT *` yasağına (`schema-dogrulama.md` SÇ-04) karşı
sabit proje listesi; sütun eklemek bilinçli karar gerektirir.

## Ö-S6 — Kısıt Hatası Yakalama (Referans: MySQL error 1062)

```php
try {
    $repo->insert($row);
} catch (\PDOException $e) {
    if ((int)($e->errorInfo[1] ?? 0) === 1062) {
        // UNIQUE çakışması — çoğaltma (duplicate) engeli kısıttan gelir
    }
    throw $e;
}
```

**Kaynak:** MySQL error 1062 `Duplicate entry '%s' for key '%s'` (dev.mysql.com
error-number-reference). Bu örnekteki `$repo->insert` **yazma örneğidir** —
gerçek çağrı noktası `ApiKeyRepository.php:102-118` (parametreli INSERT,
bkz. `../repositories/ornekler`). PHP kodu bu blokta **temsili** olarak
yazılmıştır (`⚠️ TASARIM` — repo'da birebir böyle bir catch yoktur, K007
belgesi içindir).

## Ö-S7 — TEST: Şema Eşleşmesi (Çift DB) (Kanıt: `shared/tests/Repository/Sqlite/`)

Oracle/MySQL hedefli repository testleri SQLite çiftiyle koşar
(`shared/tests/Repository/Sqlite/*` dosya çiftleri, test envanteri
`../data-test-stratejisi.md` T-09). Şema farkları: auto-increment sözdizimi,
`ENUM` yokluğu — `schema-dogrulama.md` §2'de `⚠️ TASARIM` olarak işaretli.

## Ö-S8 — NELER YAPILMAZ

1. Şema dump'ını belgeye kopyalamak (SSOT `.ai/.sql/mysql/`).
2. `LIKE '%…%'` uygulama kodunda (`.ai/.sql/CLAUDE.md §1` yasağı).
3. `SELECT *` (T-11 anti-pattern, `data-dogrulama.md` M-07).
4. Sınırsız `text` alanı + `LIMIT`siz okuma (`.ai/.sql/CLAUDE.md §1`).

## Ö-S9 — Şema İnceleme Kaydı (Şablon)

Yeni tablo/alan incelemesi şu satırla kayda geçer (checklist §10 karşılığı):

```
[şema-inceleme] <tarih> · <tablo/alan> · PK:✓ · NOT NULL:✓ · VARCHAR limit:✓
· utf8mb4:✓ · LIKE yok:✓ · SELECT * yok:✓ · drift:✓ | ⚠️<gerekçe>
```

Bu blok `⚠️ TASARIM` — repo'da böyle bir kayıt dosyası yoktur; kanun
(`.ai/.sql/CLAUDE.md §2` database-review) için önerilen biçimdir.

## Ö-S10 — Kaynak Kırılımı Tablosu

| Örnek | Gerçek kod mu? | Kaynak |
|---|---|---|
| Ö-S1 | ✓ | `DatabaseManager.php:14-20` |
| Ö-S2 | ✓ | `DatabaseManager.php:22-27` |
| Ö-S3 | ✓ | `DatabaseManager.php:30-42` |
| Ö-S4 | ✓ (dump metni, PHP string) | `oauth_connections_migration.php:14-24` |
| Ö-S5 | ✓ | `ApiKeyRepository.php:40-41` |
| Ö-S6 | `⚠️ TASARIM` (temsili catch) | MySQL error 1062 kaynağı |
| Ö-S7 | ✓ (dizin varlığı) | `shared/tests/Repository/Sqlite/` |
| Ö-S9 | `⚠️ TASARIM` (öneri) | `.ai/.sql/CLAUDE.md §2` kanunu |

## Ö-S11 — Bağlantılar

- **Yukarı:** `../data-kod-ornekleri.md` Ö-1 (DatabaseManager) · Ö-8 (migration).
- **Kardeş:** `../migrations/ornekler` (Ö-S4'ün yürütme yüzeyi) ·
  `../repositories/ornekler` (Ö-S5/Ö-S6'nın tüketeni).
- **Dış:** ADR-002 (PDO yalnız) · `.ai/.sql/CLAUDE.md §1` (kanun).

## Ö-S12 — Örnek ↔ Kanun Eşleşmesi

| Örnek | Karşıladığı kanun | Kaynak |
|---|---|---|
| Ö-S1 | charset DSN'de | PDO #9 |
| Ö-S2 | prepared gerçek (emulate yok) | ADR-002 |
| Ö-S3 | parametreli sorgu | `.ai/.sql/CLAUDE.md §1` |
| Ö-S4 | `VARCHAR` limitli + `NOT NULL` + PK 1 | MySQL #2/#11 |
| Ö-S5 | `SELECT *` yok | T-11 |
| Ö-S6 | çakışma = kısıt (UNIQUE) | MySQL 1062 |

## Ö-S13 — Okuma Notu

Örnekler `2026-10-12` dosya okumasıyla alındı; satır numaraları yeniden
üretimde teyit edilmelidir (satır kayması riski, R-02 kök). Ö-S6/Ö-S9
`⚠️ TASARIM` işaretli temsili bloklardır — üretim kodunda birebir
bulunmazlar; amaç kanunun nasıl göründüğünü göstermektir.

## Ö-S14 — Bağlantılar (Özet)

| Karşılık | Dosya |
|---|---|
| Kök örnek havuzu | `../data-kod-ornekleri.md` |
| Bileşen karşılığı | `schema-bilesenler.md` S-B01..S-B09 |
| Doğrulama karşılığı | `schema-dogrulama.md` SÇ-09/SÇ-10/SÇ-13 |
