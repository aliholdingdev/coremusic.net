---
node_id: "K007-repositories-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/schema"
  - "K007-data/transactions"
evidence:
  - "shared/src/Repository/ApiKeyRepository.php:11-35 — şema docblock + doğrulama akışı (2026-10-15 okundu)"
  - "shared/src/Contracts/Database/IDatabaseManager.php:8-14 — 6 metot sözleşmesi"
  - "ADR-002 — PDO prepared; SELECT * yasak; ORM yasak"
validation: "2026-10-15 — kapsam yazıldı; ADR-002 + 2 repo dosyası"
open_questions:
  - "Repository başına sorgu sayısı ölçümü → UNKNOWN"
  - "N+1 tespit mekanizması → repo'da YOK"
group: mimari
---

# repositories — Kapsam (K007)

## §1 Kapsam İÇİ

| # | Konu | Kanıt |
|---|------|-------|
| 1 | Domain → veri tek kapı kuralı | `ApiKeyRepository.php:11-35` docblock |
| 2 | Prepared statement zorunluluğu (interpolasyon yasak) | ADR-002 · `DatabaseManager` |
| 3 | Kolon listesi disiplini (`SELECT_COLUMNS` sabiti) | `ApiKeyRepository.php` SELECT_COLUMNS |
| 4 | Çok-DB registry erişimi (`dbKey` ile) | `IDatabaseRegistry` · `ApiKeyRepository::DB_KEY='auth'` |
| 5 | Hash-only yazma kuralı (ham anahtar DB'ye girmez) | `ApiKeyRepository.php:18-22` |

## §2 Kapsam DIŞI

| Dışarıda | Nereye | Gerekçe |
|----------|--------|---------|
| SQL çalıştırma motoru | transactions | atomiklik ayrı soru |
| Şema tasarımı | schema | repo şemayı **uygular**, tasarlamaz |
| Cache stratejisi | cache | repo cache'e yazmaz (şimdilik) |
| Domain entity/VO | K008-domain | repo dizi/DTO döner |

## §3 Sınır cümlesi

repositories, "domain'in veriye **tek kapısı**"dır: SQL'i domain'den saklar,
prepared dışına çıkmaz, kolon listesini açıkça yazar.

---

# EK R — PDO Prepared Statement Disiplini (ADR-002 derinlemesine)

## R.1 Neden `ATTR_EMULATE_PREPARES = false`?

Kanıt: `shared/src/Database/DatabaseManager.php:25`.

| | `true` (emülasyon) | `false` (gerçek prepare) |
|---|---|---|
| SQL derleme | PHP string birleştirir, MySQL'e hazır metin gider | MySQL sunucu tarafında `COM_STMT_PREPARE` |
| Parametre bağlama | PHP kaçırır (string interpolation) | sürücü **binary-safe** bağlar |
| SQLi riski | kaçış hatası = açık | bağlama katmanında kapanır |
| Tip desteği | her şey string gelir | `INT`/`BLOB` doğru tipte iletilir |
| **`binary(16)`** | ⚠️ kaçış bozabilir | ✓ bayt-hassa |
| Sorgu planı | her çağrıda yeniden parse | **prepare önbelleği** (statement cache) |

**Proje sonucu:** `false` — ADR-002 ile uyumlu ve `binary(16)` PK'lar için
zorunlu olan seçim.

## R.2 Kötü örnek ↔ iyi örnek (repository yüzeyi)

```php
// YASAK — interpolasyon (ADR-002 ihlali, SQLi açığı)
$db->execute("SELECT id FROM api_keys WHERE key_prefix = '$prefix'");

// YASAK — hazır metnin içine dinamik kolon/sıra (parametre bağlanamaz)
$db->execute('SELECT ' . $userInput . ' FROM api_keys');

// DOĞRU — sabit SQL şablonu + isimli parametre (kanıt: ApiKeyRepository.php:160-163)
$rows = $this->db()->execute(
    'SELECT ' . self::SELECT_COLUMNS . ' FROM api_keys WHERE key_prefix = :prefix AND is_deleted = 0',
    ['prefix' => $prefix]
);
```

## R.3 İsimli (`:name`) vs konumsal (`?`) — CoreMusic tercihi

| | İsimli | Konumsal |
|---|---|---|
| Okunabilirlik | ✓ (`:user_id`) | ✗ (sıra hatası riski) |
| Tekrarlı parametre | ✓ aynı isim iki kez | her biri ayrı slot |
| Kanıt | `ApiKeyRepository.php:103-117` (`:id`, `:user_id`, `:now1`, `:now2`) | — |

**Gözlem:** `created_at`/`updated_at` için **iki ayrı isim** (`:now1`, `:now2`)
kullanılmış — PDO'da aynı ismi iki kez bağlamak yerine tercih edilmiş;
nedeni `UNKNOWN` (olası sürüm uyumu).

## R.4 `binary(16)` bağlama kanıtı

| Adım | Kod | Satır |
|---|---|---|
| Hex → binary | `UuidV7::toBinary($userIdHex)` | `ApiKeyRepository.php:107` |
| Binary üretimi | `UuidV7::generateBinary()` | `:99` |
| Binary → hex (yanıt) | `self::toHexSafe($idBinary)` | `:123` |

**Kural:** DB'ye `binary(16)` gider, API'ye `32 hex karakter` çıkar. `UNHEX()`
SQL fonksiyonu **kullanılmıyor** — dönüşüm PHP'de yapılıyor (daha az sunucu
CPU, statement cache korunur).

---

# EK S — `SELECT_COLUMNS` Disiplini (SELECT * yasaklığının pratik hali)

## S.1 Kanıt

```php
private const SELECT_COLUMNS =
    'id, user_id, key_hash, key_prefix, key_name, scopes, key_type, rate_limit, is_active, last_used_at, expires_at';
```
(`ApiKeyRepository.php:40-41`)

## S.2 Neden kolon listesi zorunlu?

| # | Neden | Açıklama |
|---|---|---|
| 1 | ADR-014 `SELECT *` yasağı | gereksiz veri + şema değişince kırılır |
| 2 | Hassas kolon sızıntısı | `key_hash`, `allowed_ips` gibi alanlar istenmeden dışarı çıkmasın |
| 3 | İndeks-only tarama | kolon listesi küçükse InnoDB **covering index** kullanabilir |
| 4 | Network maliyeti | 11 kolon vs 15 kolon — istek başına kazanç |
| 5 | PHP docblock senkronu | dönüşüm `array{...}` imzası kolonlarla birebir |

## S.3 Kolon listesi ↔ şema senkron riski

`api_keys` tablosuna yeni kolon eklendiğinde **3 yer** senkron olmalı:
`SELECT_COLUMNS` sabiti · docblock şema bloğu (`:12-19`) · dönüşüm array'i
(`:120-131`). Otomatik denetim **YOK** → `⚠️ VERIFICATION REQUIRED`
(K007-data-quality gözlemi).

---

# EK T — N+1 ve JOIN Yasağının Birlikte Yönetimi

| Kural | Kaynak |
|---|---|
| JOIN yasağı | `.ai/.sql/CLAUDE.md §1` — "JOIN yasağı; JSON aggregate/ORM map; zorunlu JOIN raporlanır" |
| N+1 tespit mekanizması | `repositories-kapsam.md` §open_questions: **repo'da YOK** |

## T.1 N+1 senaryosu (repository katmanında nasıl görünür?)

```text
PlaylistRepository::getTracks(playlistId)      → 1 sorgu (track_id listesi)
  └─ MusicRepository::getById(trackId) × N     → N sorgu  ← N+1
```

**Savunma seçenekleri (JOIN yasağı altında):**

| Seçenek | Nasıl | Bedel |
|---|---|---|
| Batch by-id | `WHERE id IN (:a,:b,:c)` — tek sorgu | parametre sınırı (MySQL prepared placeholder üst sınırı `⚠️ VERIFICATION REQUIRED`) |
| JSON aggregate | `JSON_ARRAYAGG` (kanunun önerdiği) | MySQL 5.7.22+; `ORDER BY` dikkat |
| View | `v_music_recording_map` gibi (S-B18) | JOIN view içinde gizlenir — kanuna aykırı mı? `⚠️` |
| Cache | `../cache/` L2 | aynı veri tekrar istenince |

## T.2 Batch IN deseni (prepared, interpolasyon yok)

```php
// id'ler repo'da binary(16); placeholder sayısı dinamik → şablon döngüde kurulur
$ids   = array_map(static fn (string $hex): string => UuidV7::toBinary($hex), $hexIds);
$slots = implode(',', array_fill(0, count($ids), '?'));
$sql   = 'SELECT id, title FROM musics WHERE id IN (' . $slots . ') AND is_deleted = 0';
$rows  = $db->execute($sql, $ids);
```

| Not | Açıklama |
|---|---|
| `IN (...)` güvenli mi? | Evet — **placeholder** dolduruluyor, değer değil; `slots` yalnız `?` ve `,` içerir |
| Boyut sınırları | 100'lük gruplar; üst sınır için `⚠️ VERIFICATION REQUIRED` |
| `is_deleted = 0` | soft-delete filtresi her SELECT'te zorunlu (K007 kural 3) |

---

# EK U — Soft Delete Repository Deseni (kanonik)

## U.1 Zorunlu filtre matrisi

| İşlem | SQL | Not |
|---|---|---|
| Okuma | `WHERE ... AND is_deleted = 0` | kanıt `:161` |
| Silme | `UPDATE ... SET is_deleted = 1, deleted_at = :now WHERE id = :id` | **hard DELETE yasak** |
| Geri alma | `UPDATE ... SET is_deleted = 0, deleted_at = NULL WHERE id = :id` | — |
| Unique tekrarı | §B.3 (schema-kapsam): `UNIQUE` `is_deleted` içermez → çakışma | `⚠️` |

## U.2 Yazma sorgularının atomikliği

Tek satır `UPDATE` atomiktir (InnoDB). Birden fazla tabloya yazım →
`../transactions/` sarmalayıcısı; repository transaction **açmaz**, sarmalar
(transactions-anayasa §36).

---

# EK V — Çok-DB Erişimi (registry tabanlı)

| # | Soru | CoreMusic cevabı | Kanıt |
|---|---|---|---|
| 1 | DB seçimi nasıl? | `dbKey` string'i | `ApiKeyRepository::DB_KEY = 'auth'` (`:33`) |
| 2 | Nereden gelir? | `IDatabaseRegistry::get($dbKey)` | `:48-51` |
| 3 | 18 DB tek registry'de mi? | `registerMySql` çağrısı sayısı `UNKNOWN` | `DatabaseRegistry.php:16-26` |
| 4 | Cross-DB JOIN? | SQL seviyesinde imkânsız değil (32 çapraz-DB FK var) ama repository tek DB'ye bağlı | `schema-kapsam` §E |
| 5 | Cross-DB transaction? | `DatabaseManager` tek PDO → **tek DB atomikliği** | `:49-62` |

---

# EK W — Repository İmza Kalıbı (PHP 8.4, strict_types)

Kanıt: `ApiKeyRepository.php` (imzalar birebir).

| Desen | İmza | Not |
|---|---|---|
| Sınıf | `final class ApiKeyRepository` | `final` — extend yasak |
| Bağımlılık | `__construct(private readonly IDatabaseRegistry $registry, private string $dbKey = self::DB_KEY)` | readonly + promoted |
| DB erişimi | `private function db(): IDatabaseManager` | tek satırda registry çözümü |
| Dönüşüm | `array{...}` docblock | runtime `array` |
| Doğrulama | `throw new \InvalidArgumentException(...)` | `:78`, `:83` |
| Zaman | `date('Y-m-d H:i:s')` | `:100`, `:189` — **timezone `UNKNOWN`** |
| Hash | `hash('sha256', $rawKey)` | `:108`, `:165` |
| Karşılaştırma | `hash_equals($stored, $rawHash)` | `:171` — sabit-zamanlı |

## W.1 Zaman üretimi riski (repository ≠ DB clock)

`date('Y-m-d H:i:s')` PHP tarafında üretilir; `created_at`/`updated_at`
DB'de `CURRENT_TIMESTAMP` default'lu olmasına rağmen repository **explicit**
yazar (`:115-116`). İki saat kaynağı:

| Kaynak | Risk |
|---|---|
| PHP `date()` | sunucu TZ'sine bağlı; `DATE_DEFAULT_TIMEZONE` ayarı `UNKNOWN` |
| MySQL `NOW()` | TZ sunucu ayarına bağlı |

`⚠️ VERIFICATION REQUIRED`: iki kaynak arasındaki fark `expired` kontrolünü
(`:191`) etkileyebilir.

---

# EK X — Hata Yüzeyi ve SQLSTATE

| Hata | SQLSTATE | MySQL kodu | Repository davranışı |
|---|---|---|---|
| Duplicate key | `23000` | `1062` | exception (`ERRMODE_EXCEPTION`) → `transactions-anayasa` §33 E-5 |
| FK ihlali | `23000` | `1452` | exception → rollback |
| Deadlock | `40001` | `1213` | retry edilmelidir — **retry mekanizması YOK** (`⚠️`) |
| Lock wait timeout | `HY000` | `1205` | exception |
| Kolon yok | `42S22` | `1054` | şema/senkron bozulması → §S.3 |
| Tablo yok | `42S02` | `1146` | şema/senkron |

**Kritik boşluk:** deadlock (`1213`) ve lock timeout (`1205`) için repository
katmanında **yeniden deneme (retry) yoktur**; exception doğrudan üst katmana
çıkar. `⚠️ VERIFICATION REQUIRED` → `../transactions/` ve `../synchronization/`
ile ortak karar.

---

# EK Y — Repository Katman Anti-Pattern Denetimi (2026-10-17)

| # | Anti-pattern | Mevcut durum | Kanıt |
|---|---|---|---|
| 1 | SQL string interpolasyonu | ✗ görülmedi | `:161` prepared |
| 2 | `SELECT *` | ✗ | `SELECT_COLUMNS` |
| 3 | Hard `DELETE` | ✗ | `is_deleted` filtresi |
| 4 | Transaction'ın controller'a yayılması | ✗ (repo transaction **açmıyor**) | `:102-118` tek write |
| 5 | `lastInsertId()` bağımlılığı | **var** — `IDatabaseManager::lastInsertId()` sözleşmede (`:8-14`) | `binary(16)` PK'da **anlamsız** (AUTO_INCREMENT değil) → `⚠️ VERIFICATION REQUIRED` (kim kullanıyor?) |
| 6 | N+1 | ölçüm yok | açık soru |
| 7 | Cache entegrasyonu | ✗ (repo cache'e yazmaz) | §2 kapsam dışı |
| 8 | Lazy loading | ✗ (repo dizi/DTO döner) | §2 |

> `lastInsertId()` sözleşmesi, `binary(16)` UUID PK şemasıyla **çelişir**:
> MySQL `lastInsertId()` yalnız `AUTO_INCREMENT` için anlamlıdır. 187 tablo
> içinde `AUTO_INCREMENT` **sayılmadı** → `⚠️ VERIFICATION REQUIRED`.

---

# EK Z — Test Yüzeyi (repository)

| Test | Durum | Kanıt |
|---|---|---|
| `ApiKeyRepositoryTest.php` | ✓ SQLite in-memory | `repositories-bilesenler.md` §3 |
| SQLite ↔ MySQL farkları | `ENUM` yok, `binary(16)` yok, `JSON` yok, `CURRENT_TIMESTAMP ON UPDATE` yok | `schema-kapsam` §8, `schema-dogrulama` §2 |
| Gerçek MySQL entegrasyon testi | `UNKNOWN` | `shared/tests/` envanteri: 9 dizin (Api, Component, Events, Fixtures, Middleware, OAuth, Repository, Security, Unit) |

## Z.1 SQLite çiftinin yakalayamadığı şeyler (repository açısından)

| Fark | MySQL | SQLite | Test sonucu |
|---|---|---|---|
| `binary(16)` | var | yok (BLOB) | PK davranışı test edilemez |
| `ENUM` | kısıt | CHECK'e çevrilir | `key_type` geçerliliği testte farklı |
| `utf8mb4_unicode_ci` | var | yok | collation sıralaması test edilemez |
| Deadlock/lock | var | tek yazıcı | §X boşluğu |
| `ON UPDATE CURRENT_TIMESTAMP` | var | yok | `updated_at` test edilemez |

---

# EK AA — Repository Katmanı Kararları (bu ek)

| # | Karar | Durum |
|---|---|---|
| AA-1 | İsimli parametre (`:name`) kanonik | ✓ (kanıt) |
| AA-2 | `binary(16)` ↔ hex dönüşümü PHP'de yapılır (`UNHEX` yok) | ✓ (kanıt) |
| AA-3 | `SELECT_COLUMNS` sabiti zorunlu | ✓ (kanıt) |
| AA-4 | Soft delete filtresi her SELECT'te | ✓ (kanıt `:161`) |
| AA-5 | `lastInsertId()` sözleşmesi `binary(16)` ile çelişkili | `⚠️` açık |
| AA-6 | Deadlock retry mekanizması yok | `⚠️` açık |
| AA-7 | N+1 ölçüm/tespit aracı yok | `UNKNOWN` |
| AA-8 | Zaman kaynağı (PHP `date()` vs DB `NOW()`) | `⚠️` açık |
| AA-9 | Batch IN üst sınırı | `UNKNOWN` |
| AA-10 | 171 tablo erişim yolu (repository mi query-object mi) | `UNKNOWN` (ADR-097 K4) |

---

# EK AB — ApiKeyRepository Tam Analiz (317 satır kod kanıtı)

> `shared/src/Repository/ApiKeyRepository.php` 2026-10-17'de **tam okunmuştur**
> (satır 1-317). Bu ek, kodun satır düzeyi davranışını belgeler.

## AB.1 Dosya yapısı (satır haritası)

| Satır | İçerik |
|---|---|
| 1-10 | `declare(strict_types=1)` + `namespace` + `use` (UuidV7, IDatabaseManager, IDatabaseRegistry) |
| 11-35 | `api_keys` docblock şema bloğu + `DB_KEY='auth'` + `KEY_TYPES` + `RAW_KEY_PATTERN` |
| 40-41 | `SELECT_COLUMNS` sabiti (11 kolon) |
| 43-46 | constructor: `readonly IDatabaseRegistry` + `readonly string $dbKey = self::DB_KEY` |
| 48-51 | `db(): IDatabaseManager` — registry çözümü |
| 74-132 | `create(array $params): array` — ham anahtar üretimi + INSERT |
| 140-145 | `verify(...): ?array` — `inspect` sarmalayıcı |
| 153-226 | `inspect(...): array` — 8 adımlı doğrulama akışı |
| 231-238 | `prefixFromRaw(string): ?string` — regex parse |
| 247-257 | `encodeScopes(array, array): string` — JSON encode (2 şema) |
| 262-287 | `decodeScopes(mixed): array` — JSON decode (2 şema) |
| 289-299 | `randomPrefix(): string` — 8 karakter `[a-zA-Z0-9]` |
| 301-305 | `randomSecret(): string` — 32 bayt → base64url (43 karakter) |
| 307-316 | `toHexSafe(string): string` — `binary(16)` ↔ hex güvenli dönüşüm |

## AB.2 API key formatı (kod kanıtı)

```text
raw_key = 'cm_' . $environment . '_' . $prefix . '_' . $secret
        = "cm_" + ("live"|"test") + "_" + 8-char prefix + "_" + 43-char base64url
        = 3 + 1 + 4|5 + 1 + 8 + 1 + 43 = 61 veya 62 karakter
```

| Parça | Uzunluk | Entropi | Üretim |
|---|---|---|---|
| `cm_` | 3 | sabit | — |
| environment | 4-5 | 1 bit | `'live'`/`'test'` (`:86`) |
| prefix | 8 | 62^8 ≈ 2^47.6 | `random_int` (`:289-299`) |
| secret | 43 | 256 bit | `random_bytes(32)` → base64url (`:301-305`) |
| **Toplam** | 61-62 | ≈ 304 bit | — |

| Gözlem | Sonuç |
|---|---|
| `random_bytes` (CSPRNG) | ✓ kriptografik güvenli |
| `random_int` (prefix) | ✓ kriptografik güvenli (PHP docs) |
| prefix kolizyonu | 8 karakter → ~2^47.6 uzay; 1M anahtar → ≈ 2^-13 olasılık (düşük) |
| `rtrim(base64, '=')` | URL-safe dönüşüm (`+/` → `-_`) ✓ ADR-020 §1.3 |

## AB.3 `inspect()` 8 adımlı doğrulama akışı (birebir kod)

| # | Adım | Kod satırı | Hata `reason` |
|---|---|---|---|
| 1 | `prefixFromRaw` regex | `:155-158` | `malformed_key` |
| 2 | `SELECT ... WHERE key_prefix = :prefix` | `:160-163` | — |
| 3 | `hash_equals` karşılaştırma | `:169-175` | `unknown_prefix` / `hash_mismatch` |
| 4 | `is_active` kontrolü | `:181-183` | `inactive` |
| 5 | `is_deleted` kontrolü | `:185-187` | `deleted` |
| 6 | `expires_at` kontrolü | `:189-193` | `expired` |
| 7 | `allowed_ips` kontrolü | `:199-201` | `ip_not_allowed` |
| 8 | `requiredScope` kontrolü | `:203-205` | `scope_denied` |
| 9 | `last_used_at` UPDATE | `:220-223` | — (sessiz) |

## AB.4 `verify` vs `inspect` ayrımı

| Metot | Dönüş | Kullanım |
|---|---|---|
| `verify` | `?array` (context) | normal akış — `null` = başarısız |
| `inspect` | `array{ok, reason, context}` | log/audit — nedenle birlikte |

**Neden iki metot?** `verify` `inspect`'i çağırır (`:142`) ve yalnız `ok=true`
durumunda context döner. Loglama için `reason` gerekli → `inspect`.
Tasarım temizdir; `verify` yalnız ince sarmalayıcı.

## AB.5 Hash karşılaştırma güvenliği (adım 3)

```php
$rawHash = hash('sha256', $rawKey);
foreach ($rows as $candidate) {
    $stored = (string) ($candidate['key_hash'] ?? '');
    if ($stored !== '' && hash_equals($stored, $rawHash)) { ... }
}
```

| Özellik | Değer | Neden |
|---|---|---|
| `hash_equals` | sabit-zamanlı | timing attack koruması |
| `hash('sha256')` | 64 hex karakter | hız + güvenlik dengesi |
| `key_hash` UNIQUE | `:219` yorumu | prefix eşleşirse **tek satır** |
| Pre-hash (HMAC yerine) | `hash('sha256', $rawKey)` | salt yok → yüksek entropi (256 bit) nedeniyle KDF gereksiz |

**Güvenlik değerlendirmesi:** 256-bit rastgele secret + SHA-256 → kaba kuvvet
**teorik olarak imkânsız** (2^256 uzay). `key_hash` UNIQUE index'i zorunlu →
`prefix` ile lookup O(1).

## AB.6 `scopes` JSON şeması (2 biçim — `:247-287`)

| Şema | Biçim | Tetikleyici |
|---|---|---|
| A | `["api.access", "auth.read"]` | `allowed_ips === []` |
| B | `{"scopes": [...], "allowed_ips": [...]}` | `allowed_ips !== []` |

**Şema geçiş riski:** `encodeScopes` formatı **koşullu** → A'dan B'ye geçişte
`decodeScopes` her iki formatı da okur (`:275-286`) ✓ backward-uyumlu.

| Gözlem | Sonuç |
|---|---|
| `array_is_list($decoded)` | PHP 8.1+ fonksiyonu — PHP 8.4 hedefi ile uyumlu ✓ |
| `JSON_UNESCAPED_SLASHES` | `api.access` gibi dot-scope'lar okunabilir |
| `strval` map | tip güvenliği — string olmayan scope'lar atılır |
| Boş/null scope | `[]` döner (`:264-266`) |

## AB.7 `last_used_at` UPDATE — yazma okuma sonrası (adım 9)

```php
$this->db()->write(
    'UPDATE api_keys SET last_used_at = :now, updated_at = :now2 WHERE key_hash = :key_hash AND is_deleted = 0',
    ['now' => $now, 'now2' => $now, 'key_hash' => $rawHash]
);
```

| Gözlem | Sonuç |
|---|---|
| SELECT + UPDATE = **2 round-trip** | her verify'de 2 sorgu |
| atomiklik | tek UPDATE atomiktir (InnoDB) |
| eşzamanlılık | 2 farklı istek aynı anda verify → son UPDATE kazanır (kayıp: `last_used_at` gecikmesi, önemsiz) |
| hata durumu | UPDATE exception fırlatırsa **context döner mi?** → kod `:220-223`'te try-catch **YOK** → exception üst katmana çıkar, `ok=true` döndürülmez → **audit kaybı riski** |

**Tespit (AB-7 yeni):** `last_used_at` UPDATE'i **try-catch içinde değil**;
UPDATE başarısız olursa (örn. lock timeout `1205`) `inspect()` exception
fırlatır — oysa doğrulama başarılıydı. Üst katmanın bunu yakalayıp yakalamadığı
`UNKNOWN` (middleware okunmadı).

## AB.8 `toHexSafe` — SQLite uyumluluk köprüsü (`:307-316`)

```php
private static function toHexSafe(string $binary): string
{
    if (UuidV7::isValidBinary($binary)) {
        return UuidV7::toHex($binary);
    }
    return $binary; // SQLite test ortamı: hex zaten string
}
```

| Ortam | DB'de tip | `toHexSafe` davranışı |
|---|---|---|
| MySQL (prod) | `binary(16)` | `isValidBinary` ✓ → `toHex` |
| SQLite (test) | `TEXT` (hex) | `isValidBinary` ✗ → ham string döner |

**Neden bu köprü?** SQLite `binary(16)` desteklemez → test çiftinde PK hex
string olarak tutulur (`schema-kapsam` §8). `toHexSafe` iki ortamı tek imzada
birleştirir.

| Risk | Açıklama |
|---|---|
| Bozuk okuma | MySQL'de `binary(16)` bozuk olursa (örn. 15 bayt) → `isValidBinary` ✗ → ham string döner → sessiz veri sızıntısı |
| İsim yanıltıcı | "safe" → sessiz fallback; hata yerine ham string |

---

# EK AC — Repository Sınıf Envanteri (disk taraması 2026-10-17)

| # | Dosya | DB_KEY | Tablo | Durum |
|---|---|---|---|---|
| 1 | `ApiKeyRepository.php` | `'auth'` | `api_keys` | ✓ 317 satır tam okundu |
| 2 | Diğer repository sınıfları | — | — | `UNKNOWN` (tarama yapılmadı) |

**Çağrı:** `shared/src/Repository/` dizini altında kaç repository sınıfı var →
`UNKNOWN`. Tarama önerisi: `Get-ChildItem shared/src/Repository -Filter *.php`.
Bu ek **yalnız** `ApiKeyRepository` derinliğini kapsar; diğer repository'ler
sonraki üretimde okunacaktır.

---

# EK AD — Repository → Şema Eşleme (api_keys örneği)

> `ApiKeyRepository.php:11-35` docblock ↔ `coremusic_auth.api_keys` dump.

| Docblock alanı | Muhtemel SQL tipi | Not |
|---|---|---|
| `id` | `binary(16)` PK | `UuidV7::generateBinary()` |
| `user_id` | `binary(16)` FK | `UuidV7::toBinary($hex)` |
| `key_hash` | `char(64)` UNIQUE | SHA-256 hex |
| `key_prefix` | `char(8)` | 8 karakter |
| `key_name` | `varchar` | — |
| `scopes` | `json` | 2 şema (§AB.6) |
| `key_type` | `enum` | `KEY_TYPES` sabiti |
| `rate_limit` | `int` | varsayılan 1000 |
| `is_active` | `tinyint(1)` | 0/1 |
| `is_deleted` | `tinyint(1)` | soft delete (kural 3) |
| `created_at`/`updated_at` | `datetime` | PHP `date()` yazılı |
| `last_used_at` | `datetime NULL` | UPDATE ile |
| `expires_at` | `datetime NULL` | — |

| Eşleşme doğrulaması | Durum |
|---|---|
| Docblock ↔ dump birebir | `⚠️ VERIFICATION REQUIRED` — `coremusic_auth.sql` dump'ında `api_keys` sütun dökümü okunmadı |
| `allowed_ips` kolonu | docblock `create` params'ında var (`:64`) ama `SELECT_COLUMNS`'ta **YOK** → JSON içinde saklanıyor (§AB.6) |

**Gözlem (AD-1):** `allowed_ips` **fiziksel kolon değil**, `scopes` JSON'ının
içinde (`:254` `{"scopes":..., "allowed_ips":...}`). Docblock'ta `allowed_ips`
parametresi olarak görünse de SELECT'te ayrı kolon olarak alınmıyor → JSON
alanı `scopes` hem scope'ları hem IP'leri taşır. Tasarım kararı: tek JSON kolonu
ile esneklik.

---

# EK AE — Kararlar (bu ek — 2. tur)

| # | Karar/tespit | Durum |
|---|---|---|
| AE-1 | `inspect()` 9 adımlı akış birebir belgelendi | ✓ |
| AE-2 | API key formatı: `cm_{env}_{8-prefix}_{43-secret}` | ✓ (kod kanıtı) |
| AE-3 | `hash_equals` sabit-zamanlı karşılaştırma | ✓ |
| AE-4 | `scopes` JSON 2 şema backward-uyumlu | ✓ |
| AE-5 | `last_used_at` UPDATE try-catch **YOK** → audit kaybı riski | `⚠️` yeni tespit (AB-7) |
| AE-6 | `toHexSafe` sessiz fallback riski (bozuk okuma) | `⚠️` |
| AE-7 | `allowed_ips` JSON içinde (fiziksel kolon değil) | ✓ (kod kanıtı) |
| AE-8 | `shared/src/Repository/` altındaki diğer repository'ler | `UNKNOWN` (tarama yapılmadı) |
| AE-9 | `key_prefix` 8 karakter — kolizyon olasılığı ≈ 2^-13 (1M anahtar) | ✓ (hesap) |

---

# EK AF — Repository Sınıfı Şablonu (PHP 8.4, mimari kalıp)

> Bu şablon, `ApiKeyRepository` kanıtından türetilmiş **kanonik** repository
> kalıbıdır. Yeni repository yazarken bu kalıp izlenir. Kod yazılmadı —
> yalnız vault'ta tasarım olarak durur.

## AF.1 Şablon iskeleti

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

## AF.2 Zorunlu kurallar (her repository)

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

## AF.3 Standart metod seti (öneri — repo'da yalnız ApiKey var)

| Metot | İmza | Amaç |
|---|---|---|
| `findById(string $hexId): ?array` | tek satır okuma | — |
| `create(array $params): array` | INSERT + döndürme | `:74-132` |
| `update(string $hexId, array $params): bool` | UPDATE | — |
| `softDelete(string $hexId): bool` | `is_deleted=1` | kural 3 |
| `restore(string $hexId): bool` | `is_deleted=0` | — |

## AF.4 Docblock şablonu (create)

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

# EK AG — Repository Hata Yönetimi Kalıbı (PHP 8.4)

> `ApiKeyRepository` kanıtından türetilmiş; repository katmanının hata
> politikasını standartlaştırır.

## AG.1 Hata tipleri matrisi

| Hata | Fırlatılan | Örnek satır | Üst katman |
|---|---|---|---|
| Geçersiz parametre | `\InvalidArgumentException` | `:78`, `:83` | validator/service |
| DB duplicate | `\PDOException` (1062) | `write` çağrısı | validator (unique kontrol) |
| DB FK ihlali | `\PDOException` (1452) | `write` | service |
| Deadlock | `\PDOException` (1213) | `write`/`execute` | **retry mekanizması YOK** |
| Lookup boş | `null` / `['ok'=>false]` | `:157`, `:178` | — |

## AG.2 Doğrulama deseni (repository içinde)

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

## AG.3 Doğrulama → DB sırası (neden önemli)

| Sıra | Sonuç |
|---|---|
| Önce uygulama, sonra DB | geçersiz girdi DB'ye ulaşmaz — DB yükü az |
| Sadece DB | her invalid istek DB round-trip'i |

**Tespit:** `ApiKeyRepository` **her zaman** önce uygulama seviyesinde
doğruluyor (`:77-93`) → DB'ye yalnızca geçerli girdi gider ✓ (iyi kalıp).

---

# EK AH — Kararlar (bu ek — 3. tur, kapanış)

| # | Karar/tespit | Durum |
|---|---|---|
| AH-1 | Repository şablonu (10 zorunlu kural) kanonikleşti | ✓ |
| AH-2 | Hata yönetimi: 3 tipi `\InvalidArgumentException` + PDO exception | ✓ |
| AH-3 | Doğrulama → DB sırası (uygulama önce) | ✓ |
| AH-4 | `array{...}` docblock zorunlu | ✓ |
| AH-5 | `final class` extend yasak | ✓ |
| AH-6 | İsimli parametre (`:name`) kanonik | ✓ (AA-1) |
| AH-7 | `binary(16)` ↔ hex: `UuidV7::toBinary/toHex` | ✓ (AA-2) |
| AH-8 | `SELECT_COLUMNS` sabiti zorunlu | ✓ (AA-3) |
| AH-9 | Soft delete filtresi her SELECT'te | ✓ (AA-4) |
| AH-10 | `DbKey` sabiti | ✓ (`:33`) |

---

**Authority:** Bayram Ali / Vault Steward