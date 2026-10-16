---
title: "K011 · Queries — Kapsam"
type: architecture
kodeme: kademe-2
module: K011-application
parent: queries
status: active
updated: 2026-10-15
group: mimari
---

# K011 · Queries — Kapsam (Mimari)

> **Amaç:** `queries` alt-modülünün ne kapsadığını, sınırını (ne DEĞİL), sorgu
> yaşam döngüsünü ve CoreMusic repo'sundaki gerçek sorgu katmanı bileşenlerini
> ELI15 düzeyinden kanıt satırlarına kadar tanımlamak.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1 ELI15 & tanım | mimari | kapsam | Okuma sırası başı |
| 2 | §2 Sınır — ne DEĞİL | mimari | sinir | Kapsam dışı netleştirme |
| 3 | §3 Katman & bağımlılık | mimari | katman | Üst/alta bağ |
| 4 | §4 Sorgu yaşam döngüsü | mimari | akış | Uçtan uca akış |
| 5 | §5 Çekirdek bileşenler | mimari | bilesen | Envanter + kod kanıtı |
| 6 | §6 Sorgu desenleri | mimari | desen | Tekrar eden kalıplar |
| 7 | §7 Kurallar & yasaklar | mimari | kural | Bağlayıcı kısıtlar |
| 8 | §8 Kapsam dışı | mimari | sinir | Ne yapılmaz |
| 9 | §9 İndeks & performans etkileşimi | mimari | veri | Şema ↔ sorgu |
| 10 | §10 Açık sorular | mimari | verification | ⚠️ işaretli boşluklar |
| 11 | §A Ek — terimler | mimari | referans | Sözlük |

---

## §1 ELI15 & Tanım

### §1.1 ELI15

`queries` = **okuma eylemlerinin taşıyıcısı**. Kullanıcı (veya istemci) "göster",
"listele", "doğrula", "bul" dediğinde veritabanına giden yol bu alt-modüldür.
CoreMusic repo'sunda bu yol üç parçalıdır: (1) PDO bağlantısını kuran
`DatabaseManager`, (2) bağlantıyı veritabanı adına göre dağıtan `DatabaseRegistry`,
(3) somut `SELECT`/`INSERT`/`UPDATE` sorgularını yazan repository sınıfları.

### §1.2 Tanım tablosu

| Terim | Tanım | Repo karşılığı |
|---|---|---|
| Query | Prepared statement ile yürütülen, parametreli SQL ifadesi | `DatabaseManager::execute()` / `write()` |
| Repository | Tek tablo/aile etrafında toplanmış sorgu koleksiyonu | `ApiKeyRepository`, `OAuthRepository`, `MusicRepository` |
| Read model | Sorgunun döndürdüğü ham satır kümesi (dönüştürme yok) | `fetchAll(FETCH_ASSOC)` çıktısı |
| Write query | `INSERT`/`UPDATE`/`DELETE` — bu modülde soft-delete ağırlıklı | `is_deleted = 1` kalıbı |
| Connection owner | PDO bağlantısını açan tek sınıf | `shared/src/Database/DatabaseManager.php` |

### §1.3 Kapsam beyanı

| # | Kapsam içi | Kanıt |
|---|---|---|
| 1 | PDO bağlantısı kurma + seçenekler | `Dosya: shared/src/Database/DatabaseManager.php · Satır: 22` |
| 2 | Parametreli sorgu yürütme (`prepare` + `execute`) | `Dosya: shared/src/Database/DatabaseManager.php · Satır: 32` |
| 3 | Transaction başlangıcı/commit/rollback | `Dosya: shared/src/Database/DatabaseManager.php · Satır: 49` |
| 4 | Çoklu veritabanı erişimi (auth/music/api) | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 33` |
| 5 | API key doğrulama sorgu akışı | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 160` |
| 6 | OAuth bağlantı/state sorguları | `Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 46` |
| 7 | Müzik dosyası JOIN sorguları | `Dosya: home.coremusic.net/include/Repository/MusicRepository.php · Satır: 57` |
| 8 | Şema (tablo + indeks) sorgu sözleşmesi | `Dosya: .ai/.sql/mysql/coremusic_auth.sql · Satır: 56` |

---

## §2 Sınır — Ne DEĞİL

| # | Sınır | Nereye gider | Kanıt / dayanak |
|---|---|---|---|
| 1 | Şema tasarımı (BCNF, migration) | K007-data · Data Engineer | `shared/AGENTS.md` §3 — `database/migrations/` Data Engineer |
| 2 | HTTP middleware (auth/CSRF/rate limit) | application-services · Security | `Dosya: api.coremusic.net/index.php · Satır: 147` (pipeline ayrı katman) |
| 3 | Route eşleme & sayfa render | routing · usecase-akislari | `Dosya: shared/src/PageRouter/PageRouterKernel.php` (usecase-akislari kanıtı) |
| 4 | ORM / ActiveRecord | **YASAK** — repo'da yok | `Dosya: shared/AGENTS.md` §4 Yasak #3 — "ORM (ADR-002)" |
| 5 | Veritabanı sunucusu kurulumu/backup | K018-devops | `queries-index` sınır ifadesi |
| 6 | Cache katmanı (APCu vb.) | caching (Ünite-3) | `Dosya: shared/src/Cache/ApcuAdapter.php · Satır: 5` |
| 7 | İstemci tarafı fetch/istek kütüphanesi | routing (istemci router'ı) — yalnız çapraz referans | `Dosya: assets.coremusic.net/js/router/FetchWrapper.js · Satır: 5` |

> **Sınır cümlesi:** `queries`, SQL ifadesinin PHP içinde **nasıl güvenli biçimde
> yazıldığını ve yürütüldüğünü** kapsar; SQL'in veritabanı tarafında nasıl
> planlandığını (query planner, indeks seçimi fiziksel planı) KAPSAMAZ.

---

## §3 Katman & Bağımlılık

### §3.1 Dikey konum

```text
[İstemci]  assets.coremusic.net/js/router/FetchWrapper.js  (GET, credentials:include)
    │
    ▼
[Giriş noktaları]  api.coremusic.net/index.php · home.coremusic.net/index.php
    │
    ▼
[Uygulama]  Gateway · Repository (ApiKey/OAuth/Music) · Service
    │
    ▼
[SORGU KATMANI — bu modül]  DatabaseRegistry → DatabaseManager → PDO::prepare
    │
    ▼
[Veritabanı]  MySQL 9 · coremusic_auth / coremusic_musics / ... (20 şema dosyası)
```

### §3.2 Bağımlılık tablosu

| Bağımlılık | Yön | Kanıt |
|---|---|---|
| Repository → `IDatabaseManager` (contract) | yukarı | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 5` |
| Repository → `IDatabaseRegistry` (çoklu DB) | yukarı | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 6` |
| `DatabaseManager` → `DatabaseConfig` (değer nesnesi) | aşağı | `Dosya: shared/src/Database/DatabaseManager.php · Satır: 5` |
| `MusicRepository` → `DatabaseManager` (somut) | aşağı | `Dosya: home.coremusic.net/include/Repository/MusicRepository.php · Satır: 6` |
| `OAuthRepository` → dışarıdan enjekte PDO | yan | `Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 13` |
| Şema ↔ repository sütunları | çapraz | `Dosya: .ai/.sql/mysql/coremusic_auth.sql · Satır: 73` |

### §3.3 Sözleşme (contract) yüzeyi

`IDatabaseManager` sözleşmesinin repo'daki somut uygulaması 6 kamu metodundan
oluşur (execute, write, lastInsertId, beginTransaction, commit, rollBack) — hepsi
`Dosya: shared/src/Database/DatabaseManager.php · Satır: 30–67` aralığında tek dosyada
toplanmıştır. `getPdo()` (Satır: 64) kaçış kapısıdır; OAuthRepository gibi PDO
doğrudan isteyen sınıflar bunu kullanır.

---

## §4 Sorgu Yaşam Döngüsü

### §4.1 Adım adım akış (API key doğrulama örneği — gerçek akış)

| # | Adım | Sorgu tipi | Kanıt |
|---|---|---|---|
| 1 | Ham anahtar deseni doğrulanır (`cm_live_…`) | regex — SQL yok | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 233` |
| 2 | Prefix ile aday satırlar çekilir | `SELECT … WHERE key_prefix = :prefix AND is_deleted = 0` | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 161` |
| 3 | Adaylar arasında sabit-zamanlı hash karşılaştırması | PHP — SQL yok | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 171` |
| 4 | Aktiflik/silinmişlik/süre denetimleri | PHP — SQL yok | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 181` |
| 5 | IP allowlist + scope denetimi | PHP — SQL yok | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 199` |
| 6 | `last_used_at` tazelenir | `UPDATE api_keys SET last_used_at = :now …` | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 221` |

### §4.2 Akış diyagramı (metin)

```text
çağıran (middleware/controller)
  → repository->method(params)
    → $this->db()->execute($sql, ['id' => $hex])     // DatabaseManager
      → $this->pdo->prepare($sql)                    // prepared statement
      → $stmt->execute($params)                      // bağlama parametreleri
      → $stmt->fetchAll()                            // satır kümesi
    → repository ham satırı döndürür (dönüştürme min.)
  → çağıran iş mantığında değerlendirir
```

### §4.3 Hata yolu

| Durum | Davranış | Kanıt |
|---|---|---|
| Bağlantı/sorgu hatası | `\PDOException` fırlar — yakalama repository'de DEĞİL | `Dosya: home.coremusic.net/include/Repository/MusicRepository.php · Satır: 49` (docblock: "@throws \Throwable") |
| Geçersiz girdi (hex değil) | `null` / `InvalidArgumentException` — sorgu hiç çalışmaz | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 77` |
| Enum dışı key_type | `\InvalidArgumentException` | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 82` |
| Üst katman yakalar → 500 JSON | hata sözleşmesi `{error:{code,message}}` | `Dosya: api.coremusic.net/index.php · Satır: 208` |

---

## §5 Çekirdek Bileşenler

### §5.1 DatabaseManager — tek bağlantı sahibi

| Özellik | Değer | Kanıt |
|---|---|---|
| DSN | `mysql:host=%s;port=%d;dbname=%s;charset=%s` | `Dosya: shared/src/Database/DatabaseManager.php · Satır: 14` |
| ERRMODE | `PDO::ERRMODE_EXCEPTION` | `Dosya: shared/src/Database/DatabaseManager.php · Satır: 23` |
| Fetch mode | `PDO::FETCH_ASSOC` | `Dosya: shared/src/Database/DatabaseManager.php · Satır: 24` |
| Emulate prepares | `false` (gerçek prepared statement) | `Dosya: shared/src/Database/DatabaseManager.php · Satır: 25` |
| Persistent | `true` | `Dosya: shared/src/Database/DatabaseManager.php · Satır: 26` |

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

### §5.2 DatabaseRegistry — çoklu veritabanı

| Alan | Değer | Kanıt |
|---|---|---|
| Sabit `DB_KEY` | `'auth'` | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 33` |
| Örnek DB anahtarları | `auth`, `music` (MusicRepository `DB_MUSIC_NAME`) | `Dosya: home.coremusic.net/include/Repository/MusicRepository.php · Satır: 32` |
| Şema sayısı (repo) | 20 `.sql` dosyası | `Dosya: .ai/.sql/mysql/` (dizin sayımı — 2026-10-15 ölçüm) |

### §5.3 ApiKeyRepository — doğrulama sorguları

| Metod | Sorgu | Kanıt |
|---|---|---|
| `create()` | `INSERT INTO api_keys (…) VALUES (:id, :user_id, …)` | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 103` |
| `inspect()` SELECT | `SELECT <sütun listesi> FROM api_keys WHERE key_prefix = :prefix` | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 161` |
| `inspect()` UPDATE | `UPDATE api_keys SET last_used_at = :now … WHERE key_hash = :key_hash` | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 221` |
| Sütun listesi sabiti | `SELECT_COLUMNS` — `SELECT *` YASAK'ının kod karşılığı | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 40` |

### §5.4 OAuthRepository — PDO enjeksiyonlu sorgu seti

| Metod | Kalıp | Kanıt |
|---|---|---|
| `upsertConnection()` | `INSERT … ON DUPLICATE KEY UPDATE` | `Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 37` |
| `findUserConnections()` | soft-delete filtresi `is_deleted = 0` | `Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 57` |
| `insertState()` | `DATE_ADD(NOW(), INTERVAL 10 MINUTE)` — süre SQL'de | `Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 88` |
| `findValidState()` | `expires_at > NOW()` + `fetch()` tek satır | `Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 99` |
| `deleteState()` | `DELETE FROM oauth_states WHERE state_token = :state` | `Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 115` |

### §5.5 MusicRepository — JOIN + binary UUID

| Özellik | Değer | Kanıt |
|---|---|---|
| Tembel bağlantı | PDO ilk sorguda açılır (`?DatabaseManager $db = null`) | `Dosya: home.coremusic.net/include/Repository/MusicRepository.php · Satır: 17` |
| Girdi doğrulama | `strlen === 32 && ctype_xdigit` | `Dosya: home.coremusic.net/include/Repository/MusicRepository.php · Satır: 53` |
| `UNHEX(:id)` | hex → binary(16) dönüşümü SQL içinde | `Dosya: home.coremusic.net/include/Repository/MusicRepository.php · Satır: 60` |
| JOIN | `music_files f JOIN musics m ON m.id = f.music_id` | `Dosya: home.coremusic.net/include/Repository/MusicRepository.php · Satır: 59` |
| `HEX(m.id) AS music_hex` | binary → hex geri dönüşümü | `Dosya: home.coremusic.net/include/Repository/MusicRepository.php · Satır: 81` |

### §5.6 CatalogWriter (media) — toplu upsert

| Özellik | Değer | Kanıt |
|---|---|---|
| Tablo temizleme | `$pdo->prepare('DELETE FROM ' . $tablo)->execute()` | `Dosya: media.coremusic.net/src/Media/CatalogWriter.php · Satır: 187` |
| Upsert | `self::upsertSql($tablo, …)` → `prepare` | `Dosya: media.coremusic.net/src/Media/CatalogWriter.php · Satır: 209` |

> ⚠️ VERIFICATION REQUIRED: `upsertSql()` yardımcı metodunun tam SQL şablonu bu
> oturumda okunmadı — iddia yalnızca `prepare()` çağrısıyla sınırlıdır.

### §5.7 Repo geneli prepare() yoğunluğu

| Metrik | Değer | Kaynak |
|---|---|---|
| `prepare(` çağrısı içeren PHP dosyası | 17 eşleşme / 9 dosya | ripgrep ölçümü — 2026-10-15 |
| `shared/src` PHP dosyası | 165 | filesystem sayımı — 2026-10-15 |
| `shared/tests` PHP dosyası | 45 | filesystem sayımı — 2026-10-15 |

---

## §6 Sorgu Desenleri

| # | Desen | Açıklama | Kanıt |
|---|---|---|---|
| 1 | Named parameter binding | `:prefix`, `:id`, `:now` — asla string birleştirme | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 162` |
| 2 | Açık sütun listesi | `SELECT *` yasak; `SELECT_COLUMNS` sabiti | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 40` |
| 3 | Soft delete | `is_deleted = 0` filtresi her SELECT'te | `Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 57` |
| 4 | Binary UUID | `binary(16)` + `UNHEX(:id)` / `HEX(id)` | `Dosya: home.coremusic.net/include/Repository/MusicRepository.php · Satır: 60` |
| 5 | Prefix taraması + sabit-zamanlı hash | index taraması → `hash_equals` | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 169` |
| 6 | JSON alan kodlama | `scopes` → `json_encode(..., JSON_UNESCAPED_SLASHES)` | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 250` |
| 7 | Upsert | `ON DUPLICATE KEY UPDATE` | `Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 37` |
| 8 | SQL içi süre | `DATE_ADD(NOW(), INTERVAL 10 MINUTE)` | `Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 88` |
| 9 | Tembel bağlantı | DB'siz ortamda bağlantı denemesi yok | `Dosya: home.coremusic.net/include/Repository/MusicRepository.php · Satır: 12` |
| 10 | Transaction sarmalayıcı | begin/commit/rollBack kamu API | `Dosya: shared/src/Database/DatabaseManager.php · Satır: 49` |
| 11 | Test double (SQLite) | Aynı prepare/execute sözleşmesi testte | `Dosya: shared/tests/Repository/Sqlite/SqliteDatabaseManager.php · Satır: 26` |
| 12 | CLI aracı sorgusu | `bin/api-key-create.php` de prepare kullanır | `Dosya: bin/api-key-create.php · Satır: 117` |

---

## §7 Kurallar & Yasaklar

| # | Kural | Dayanak | Durum |
|---|---|---|---|
| 1 | PDO prepared statement **yalnız** — ADR-002 | `shared/AGENTS.md` §4 Zorunlu #1 | IMPLEMENTED |
| 2 | `DatabaseManager` dışında DB bağlantısı açılmaz | `shared/AGENTS.md` §4 Zorunlu #1 | IMPLEMENTED (OAuthRepository DTO enjeksiyonu — §10-Q2) |
| 3 | `SELECT *` yasak | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 29` (docblock) | IMPLEMENTED |
| 4 | ORM yasak | `shared/AGENTS.md` §4 Yasak #3 | IMPLEMENTED (repo'da ORM bağımlılığı yok) |
| 5 | SQL string interpolation yok | `Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 8` | IMPLEMENTED (CatalogWriter `$tablo` istisnası — §10-Q3) |
| 6 | Ham anahtar/secret asla sorguya loglanmaz | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 26` | IMPLEMENTED |
| 7 | Şema değişikliği → migration şablonu | `shared/AGENTS.md` §4 Zorunlu #4 | PLANNED (migration dizini 2 dosya) |

---

## §8 Kapsam Dışı (yapılmaz)

| # | Yapılmaz | Neden |
|---|---|---|
| 1 | Yeni veritabanı bağlantısı açmak (repository içinde) | ADR-002 — tek sahiplik |
| 2 | Sorgu sonucunu HTML/JSON'a dönüştürmek | Sunum katmanının işi |
| 3 | Cache yazmak/okumak | `caching` modülünün işi |
| 4 | Rate limit sayacı yazmak | `api_usage` yazımı API middleware katmanında |
| 5 | Şema/migration üretmek | Data Engineer — `database/migrations/` |
| 6 | EXPLAIN/optimizasyon kararları | ⚠️ VERIFICATION REQUIRED — repo'da EXPLAIN kullanımı okunmadı |
| 7 | Stored procedure / trigger | Repo şemalarında tanımlı değil (20 `.sql` dosyası gözden geçirildi — bu oturumda trigger/PROCEDURE eşleşmesi YOK) |

---

## §9 İndeks & Performans Etkileşimi

Sorgu katmanı ile şema arasındaki sözleşme indekslerdir. `api_keys` örneği:

| Sorgu | İndeks | Kanıt |
|---|---|---|
| `WHERE key_prefix = :prefix` | `idx_apikeys_prefix (key_prefix)` | `Dosya: .ai/.sql/mysql/coremusic_auth.sql · Satır: 76` |
| `WHERE key_hash = :key_hash` (UPDATE) | `idx_apikeys_hash UNIQUE (key_hash)` | `Dosya: .ai/.sql/mysql/coremusic_auth.sql · Satır: 73` |
| `WHERE user_id = …` | `idx_apikeys_user (user_id)` | `Dosya: .ai/.sql/mysql/coremusic_auth.sql · Satır: 74` |
| `is_active` filtresi | `idx_apikeys_active (is_active)` | `Dosya: .ai/.sql/mysql/coremusic_auth.sql · Satır: 75` |
| Zaman serisi (`api_usage`) | `idx_apiusage_created (created_at)` | `Dosya: .ai/.sql/mysql/coremusic_auth.sql · Satır: 94` |
| `api_usage.api_key_id` JOIN | `idx_apiusage_key (api_key_id)` | `Dosya: .ai/.sql/mysql/coremusic_auth.sql · Satır: 93` |

**Tasarım notu:** Doğrulama akışı önce düşük kardinaliteli `key_prefix` ile
tarama yapar, hash eşleşmesini PHP'de `hash_equals` ile yapar — böylece tek
eşleşmeli UNIQUE taraması yerine index aralığı kullanılır. Kanıt zinciri:
`Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 161–175`.

---

## §10 Açık Sorular

| # | Soru | Etki | Durum |
|---|---|---|---|
| Q1 | Okuma modeli/projeksiyon sahibi kim? (index.md'den devraldı) | Read-model deseni netleşmez | ⚠️ VERIFICATION REQUIRED |
| Q2 | `OAuthRepository` neden `IDatabaseRegistry` yerine düz `\PDO` alıyor? (DAO sözleşmesi ihlali riski) | Tutarlılık | ⚠️ VERIFICATION REQUIRED |
| Q3 | `CatalogWriter` içindeki `'DELETE FROM ' . $tablo` interpolasyonu hangi girdi denetiminden geçiyor? | Güvenlik denetimi | ⚠️ VERIFICATION REQUIRED |
| Q4 | `ATTR_PERSISTENT => true` üretimde hangi FPM/worker modeliyle eşleşiyor? | Bağlantı havuzu davranışı | ⚠️ VERIFICATION REQUIRED |
| Q5 | `api_usage` yazımı hangi sorgu/katmandan yapılıyor? (repo'da INSERT kanıtlanmadı) | Kullanım metrikleri | ⚠️ VERIFICATION REQUIRED |
| Q6 | Read-replica / ayrı read-only bağlantı planı var mı? | Ölçeklenme | ⚠️ VERIFICATION REQUIRED |

---

## §A Ek — Terim Sözlüğü

| Terim | Kısa tanım |
|---|---|
| Prepared statement | SQL iskeleti + parametre bağlama; injection'a karşı birincil savunma |
| Named parameter | `:isim` biçiminde bağlama (`execute([...])`) |
| Soft delete | Satır silinmez, `is_deleted` bayrağı döner |
| Binary UUID | `binary(16)` sütunda tutulan UUID v7 |
| Upsert | Varsa güncelle, yoksa ekle (`ON DUPLICATE KEY UPDATE`) |
| Connection owner | PDO ömrünü yöneten tek sınıf |
| Fetch assoc | Associative array olarak satır okuma modu |
| Persistent connection | İstekler arası korunan PDO bağlantısı |
| DSN | Data Source Name — bağlantı dizesi (`mysql:host=…;dbname=…`) |
| Key prefix | API key'in görünen 8 karakterlik parçası (`key_prefix`) |
| Scope | API key'e atanmış izin listesi (JSON dizisi) |
| Hash equals | Sabit-zamanlı string karşılaştırması (timing attack koruması) |
| Registry | Veritabanı adına göre `IDatabaseManager` dağıtan katman |
| Projection | Sorgu sonucundan türetilen okuma modeli (sahibi bilinmiyor → §10-Q1) |

---

## §B Ek — Sorgu Tipi Taxonomisi (repo kanıtlı)

| Sorgu tipi | Repo örneği | Dosya | Satır |
|---|---|---|---|
| SELECT tek satır | `findValidState()` → `fetch(FETCH_ASSOC)` | `shared/src/OAuth/OAuthRepository.php` | 108 |
| SELECT çok satır | `findUserConnections()` → `fetchAll(FETCH_ASSOC)` | `shared/src/OAuth/OAuthRepository.php` | 62 |
| SELECT sabit sütun | `SELECT_COLUMNS` + `FROM api_keys WHERE key_prefix` | `shared/src/Repository/ApiKeyRepository.php` | 161 |
| SELECT JOIN + UNHEX | `music_files f JOIN musics m … UNHEX(:id)` | `home.coremusic.net/include/Repository/MusicRepository.php` | 57–64 |
| SELECT JOIN çoklu | `musics m JOIN artists a JOIN music_files f` | `home.coremusic.net/include/Repository/MusicRepository.php` | 85–87 |
| INSERT tek satır | `INSERT INTO api_keys (…) VALUES (:id, …)` | `shared/src/Repository/ApiKeyRepository.php` | 103 |
| INSERT + SQL süre | `INSERT INTO oauth_states … DATE_ADD(NOW(), INTERVAL 10 MINUTE)` | `shared/src/OAuth/OAuthRepository.php` | 87–88 |
| UPSERT | `INSERT … ON DUPLICATE KEY UPDATE` | `shared/src/OAuth/OAuthRepository.php` | 29–44 |
| UPDATE tek satır | `UPDATE api_keys SET last_used_at = :now WHERE key_hash` | `shared/src/Repository/ApiKeyRepository.php` | 221 |
| UPDATE soft delete | `UPDATE oauth_connections SET is_deleted = 1, is_active = 0` | `shared/src/OAuth/OAuthRepository.php` | 69–71 |
| DELETE tek satır | `DELETE FROM oauth_states WHERE state_token = :state` | `shared/src/OAuth/OAuthRepository.php` | 115 |
| DELETE tablo (CLI) | `DELETE FROM ' . $tablo` (interpolasyon — §10-Q3) | `media.coremusic.net/src/Media/CatalogWriter.php` | 187 |
| Son INSERT id | `lastInsertId()` sarmalayıcısı | `shared/src/Database/DatabaseManager.php` | 44 |
| Transaction | `beginTransaction()` / `commit()` / `rollBack()` | `shared/src/Database/DatabaseManager.php` | 49–62 |

---

## §C Ek — Doğrulama Akışı Ayrıntısı (API key)

API key doğrulaması bu modülün en karmaşık sorgu akışıdır; adımları ve sorumlu
sorguları aşağıdadır. Tüm satır numaraları `shared/src/Repository/ApiKeyRepository.php`
dosyasına aittir.

| Adım | Kod bloğu özeti | Satır |
|---|---|---|
| Desen doğrulama | `preg_match(self::RAW_KEY_PATTERN, $rawKey, $matches)` | 233 |
| Prefix çıkarma | `return $matches[2];` | 237 |
| Aday sorgu | `SELECT … WHERE key_prefix = :prefix AND is_deleted = 0` | 160–163 |
| Hash üretimi | `$rawHash = hash('sha256', $rawKey);` | 165 |
| Sabit-zamanlı eşleşme | `hash_equals($stored, $rawHash)` döngüsü | 169–175 |
| Bilinmeyen ön ek | `'unknown_prefix'` / `'hash_mismatch'` ayrımı | 178 |
| Aktiflik | `(int)($row['is_active'] ?? 0) !== 1 → 'inactive'` | 181–183 |
| Silinmişlik | `'deleted'` denetimi | 185–187 |
| Süre | `$expiresAt <= $now → 'expired'` | 189–193 |
| IP allowlist | `in_array($clientIp, $ips, true)` | 199–201 |
| Scope | `in_array($requiredScope, $scopes, true)` | 203–205 |
| Bağlam üretimi | `api_key_id`, `user_id`, `key_prefix`, `scopes`, `rate_limit` | 207–217 |
| Tazeleme | `UPDATE api_keys SET last_used_at …` | 220–223 |

**Neden PHP'de eşleşme?** Prefix index aralığı düşük kardinaliteli olabilir;
`hash` sütununda SQL karşılaştırması yerine PHP'de `hash_equals` ile sabit-zamanlı
karşılaştırma yapılır (timing attack yüzeyini kapatır). Tasarım iddiası —
ADR-020 §2.2-1 docblock'u: `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 21–24`.

---

## §D Ek — Anti-Pattern YAPMA ↔ YAP

| # | YAPMA (yasak) | YAP (repo kalıbı) | Kanıt |
|---|---|---|---|
| 1 | `"SELECT * FROM api_keys WHERE id = $id"` | `SELECT <sabit sütun listesi> … WHERE key_prefix = :prefix` | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 40, 161` |
| 2 | `$pdo->query($userInput)` | `$pdo->prepare($sql); $stmt->execute($params)` | `Dosya: shared/src/Database/DatabaseManager.php · Satır: 32` |
| 3 | Repository içinde `new \PDO(...)` | `IDatabaseRegistry::get($dbKey)` | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 48–51` |
| 4 | Ham anahtar loglamak | Yalnız `key_prefix` loglanır | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 229` |
| 5 | `DELETE FROM` sert silme | `SET is_deleted = 1` soft delete | `Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 69` |
| 6 | Hex ID'yi SQL'e string gömmek | `UNHEX(:id)` parametrik dönüşüm | `Dosya: home.coremusic.net/include/Repository/MusicRepository.php · Satır: 60` |
| 7 | `==` ile secret karşılaştırma | `hash_equals()` sabit-zamanlı | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 171` |
| 8 | Girdi doğrulamayı sorguya bırakmak | `strlen/ctype_xdigit` sorgu öncesi | `Dosya: home.coremusic.net/include/Repository/MusicRepository.php · Satır: 53` |

---

## §E Ek — Bağımlılık Matrisi (kim kimi çağırır)

| Çağıran | Çağrılan | Arayüz | Ömür |
|---|---|---|---|
| `ApiKeyRepository` | `IDatabaseRegistry::get('auth')` | contract | request |
| `ApiKeyRepository` | `IDatabaseManager::execute/write` | contract | request |
| `MusicRepository` | `DatabaseManager::execute` | somut | lazy singleton (sınıf içi) |
| `OAuthRepository` | `\PDO::prepare/execute/fetchAll` | doğrudan PDO | constructor enjeksiyonu |
| `CatalogWriter` | `\PDO::prepare/execute` | doğrudan PDO | CLI süreci |
| `bin/api-key-create.php` | `\PDO::prepare` | doğrudan PDO | CLI süreci |
| `SqliteDatabaseManager` (test) | `\PDO::prepare/execute` | test double | test |
| `DatabaseManager` | `\PDO` (yaratıcı) | somut | persistent |

---

## §F Ek — Şema ↔ Sorgu Uyumu (queries ↔ K007-data sınırı)

| Tablo | Şema dosyası | Kullanan repository | Durum |
|---|---|---|---|
| `api_keys` | `.ai/.sql/mysql/coremusic_auth.sql` (Satır: 56) | `ApiKeyRepository` | IMPLEMENTED |
| `api_usage` | `.ai/.sql/mysql/coremusic_auth.sql` (Satır: 81) | ⚠️ VERIFICATION REQUIRED (§10-Q5) | bilinmiyor |
| `oauth_connections` | `shared/database/migrations/` | `OAuthRepository::upsertConnection` | IMPLEMENTED |
| `oauth_states` | `shared/database/migrations/` | `OAuthRepository::insertState/findValidState/deleteState` | IMPLEMENTED |
| `music_files` + `musics` | `.ai/.sql/mysql/coremusic_musics.sql` | `MusicRepository` | IMPLEMENTED |
| `artists` | `.ai/.sql/mysql/coremusic_musics.sql` | `MusicRepository::findRecentTracks` | IMPLEMENTED |

> **Not:** `oauth_*` migration dosyalarının tam yolları bu oturumda tek tek
> okunmadı → ⚠️ VERIFICATION REQUIRED (dizin varlığı: `shared/AGENTS.md` §2 envanteri).

## §G Ek — İlişkili ADR'ler & Vault Bağlantıları

| ADR / Belge | İlişki | Not |
|---|---|---|
| ADR-002 | Prepared statement + ORM yasağının kaynağı | `shared/AGENTS.md` §4 Zorunlu #1 |
| ADR-003 / ADR-022 | `DatabaseRegistry` çoklu veritabanı | `shared/AGENTS.md` §2 envanteri |
| ADR-020 §2.2-1 | API key doğrulama akışı | `Dosya: shared/src/Repository/ApiKeyRepository.php · Satır: 21` |
| ADR-040 | BCNF şema kuralı | `shared/AGENTS.md` §3 — Data Engineer |
| ADR-015 | `EnvParser` — DB kimlik bilgileri ortam değişkeninden | `shared/AGENTS.md` §2 envanteri |
| K007-data | Şema sahibi (bu modül yalnız okur/yazar) | queries-index sınır ifadesi |
| caching (Ünite-3) | Sorgu sonucu cache'i bu modülün işi DEĞİL | §2 sınır #6 |

---

## §H Ek — Ölçüm Kayıtları (2026-10-15 oturumu)

| Ölçüm | Değer | Yöntem |
|---|---|---|
| `shared/src` PHP dosya sayısı | 165 | filesystem list_directory sayımı |
| `shared/tests` PHP dosya sayısı | 45 | filesystem list_directory sayımı |
| `prepare(` eşleşmesi (tüm repo, `*.php`) | 17 eşleşme / 9 dosya | ripgrep |
| `.ai/.sql/mysql` şema dosyası | 20 | filesystem list_directory sayımı |
| `assets.coremusic.net/js` dosyası | 94 | filesystem sayımı |
| `Css/01_Abstracts` token dosyası | 19 | filesystem sayımı |
| Havuz URL doğrulaması (5/5 → 200) | `queries-web-arastirma.md` §1 | fetch (bitişte tekrar) |

> **Tutarlılık notu:** Kardeş vault dosyaları `shared/tests` için "47 dosya",
> `assets.../js` için "98 dosya" yazıyor (`usecase-akislari` dosyaları). Bu oturumun
> ölçümleri 45/94 verdi — sayım yöntemi (glob vs. dizin) farkı olabilir →
> ⚠️ VERIFICATION REQUIRED (iki sayımın kesişimi kesinleştirilmedi).

---

## §I Ek — Bilinen Sınırlar & Teknik Borç Kayıtları

| # | Sınır | Etki | Kayıt |
|---|---|---|---|
| 1 | `execute()` her zaman `fetchAll()` çağırır (SELECT varsayımı) | Yazma sorguları `write()` ile ayrılmalı | `Dosya: shared/src/Database/DatabaseManager.php · Satır: 34` |
| 2 | `ATTR_PERSISTENT => true` | FPM worker'larında bağlantı birikme riski | `Dosya: shared/src/Database/DatabaseManager.php · Satır: 26` |
| 3 | `OAuthRepository` contract yerine somut `\PDO` | Test edilebilirlik/soyutlama | `Dosya: shared/src/OAuth/OAuthRepository.php · Satır: 13` |
| 4 | `CatalogWriter` tablo interpolasyonu | Girdi denetimi doğrulanmadı | `Dosya: media.coremusic.net/src/Media/CatalogWriter.php · Satır: 187` |
| 5 | `api_usage` yazım katmanı belirsiz | Metrik/kullanım takibi boşluğu | §10-Q5 |
| 6 | `lastInsertId()` string döner | binary(16) PK'lerde dönüşüm çağıranın sorumluluğunda | `Dosya: shared/src/Database/DatabaseManager.php · Satır: 44` |

## §J Ek — Hızlı Başvuru Kartı

```text
queries (sorgu katmanı) — tek ekran özeti
  Sahip:     Backend Architect (repository) + Data Engineer (şema)
  Giriş:     IDatabaseRegistry::get($dbKey) → IDatabaseManager
  Yürütme:   prepare($sql) → execute($params) → fetchAll()/fetch()
  Yasak:     SELECT * · ORM · string interpolation · repository içinde new PDO
  Anahtar:   ADR-002 (prepared) · ADR-020 (api key) · ADR-003/022 (registry)
  Kanıt:     DatabaseManager.php L22-27 (options) · L32/L40 (prepare)
  Boşluk:    api_usage yazıcısı · read-model sahibi · EXPLAIN politikası
```

| Sık yapılan hata | Doğru yol | Referans |
|---|---|---|
| `$pdo->query($sql)` | `$pdo->prepare($sql)` | §D #2 |
| `SELECT *` | Sabit sütun listesi | §D #1 |
| Ham hex ID string'i | `UNHEX(:id)` | §D #6 |
| `==` secret | `hash_equals()` | §D #7 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
