---
title: "K011 · Queries — Bileşenler"
type: architecture
kodeme: kademe-2
module: K011-application
parent: queries
status: active
updated: 2026-10-15
group: mimari
---

# K011 · Queries — Bileşenler (Envanter & İz Düşümü)

> **Amaç:** `queries` alt-modülünün bileşen envanterini (dizin/dosya sayımları,
> sayım PowerShell bloğu), bileşen ↔ akış iz düşümünü, bağımlılık grafiğini ve
> boşlukları kanıt satırlarıyla belgelemek.

## §GRUPLAR

| # | Bölüm | Grup | Eksen 2 | Kullanım |
|---|---|---|---|---|
| 1 | §1 Envanter — dizin/dosya sayımları | mimari | envanter | Sayım kaydı |
| 2 | §2 Sayım PowerShell bloğu | mimari | dogrulama | Tekrar üretilebilir |
| 3 | §3 Bileşen kataloğu | mimari | bilesen | Sınıf bazlı |
| 4 | §4 Bileşen ↔ akış iz düşümü | mimari | akış | Uçtan uca |
| 5 | §5 Bağımlılık grafiği | mimari | bagimlilik | Yön okları |
| 6 | §6 Boşluklar & PLANNED | mimari | verification | ⚠️ işaretli |
| 7 | §7 Ölçüm kayıtları | mimari | kanit | 2026-10-15 |
| 8 | §A Ek — hızlı başvuru | mimari | referans | Tek sayfa |

---

## §1 Envanter — Dizin/Dosya Sayımları

### §1.1 Sorgu katmanı dosyaları (doğrudan bileşenler)

| Dosya | Satır | Rol | Durum |
|---|---|---|---|
| `shared/src/Database/DatabaseManager.php` | 68 | PDO bağlantı + yürütme sarmalayıcı | IMPLEMENTED |
| `shared/src/Repository/ApiKeyRepository.php` | 317 | API key sorguları | IMPLEMENTED |
| `shared/src/OAuth/OAuthRepository.php` | 120 | OAuth bağlantı/state sorguları | IMPLEMENTED |
| `home.coremusic.net/include/Repository/MusicRepository.php` | 90+ | Müzik dosyası JOIN sorguları | IMPLEMENTED |
| `media.coremusic.net/src/Media/CatalogWriter.php` | (kısmi okundu) | CLI toplu upsert | IMPLEMENTED (⚠️ §6-Q3) |
| `bin/api-key-create.php` | (kısmi) | CLI API key oluşturucu | IMPLEMENTED |
| `shared/tests/Repository/Sqlite/SqliteDatabaseManager.php` | (kısmi) | Test double | IMPLEMENTED |

> **Satır notu:** `MusicRepository` ve `CatalogWriter` bu oturumda kısmen okundu
> (ilk 90 / ripgrep isabetleri); tam satır sayıları `read` ile doğrulanabilir.

### §1.2 İlişkili dizinler (sayımlar — 2026-10-15 ölçümü)

| Dizin | Dosya sayısı | İçerik | Ölçüm yöntemi |
|---|---|---|---|
| `shared/src` | 165 PHP | 21 alt dizin (Database, Repository, OAuth, …) | filesystem list_directory |
| `shared/tests` | 45 PHP | 9 test dizini | filesystem list_directory |
| `.ai/.sql/mysql` | 20 SQL | Şema dosyaları (auth, musics, api, …) | filesystem list_directory |
| `assets.coremusic.net/js` | 98 JS | İstemci fetch/router yüzeyi | ölçümden aktarıldı (§7 tutarsızlık notu) |
| `assets.coremusic.net/Css/01_Abstracts` | 19 CSS | Token katmanı | filesystem list_directory |
| `prepare(` eşleşmesi (repo geneli) | 17 / 9 dosya | Sorgu yoğunluğu | ripgrep |

> ⚠️ VERIFICATION REQUIRED: `assets.../js` için bu oturumda 94, kardeş vault
> dosyalarında 98 yazıyor (§7) — sayım yöntemi farkı kesinleştirilmedi.

### §1.3 `shared/src` alt dizinleri (sorgu katmanı ile ilişki)

| Alt dizin | İlişki | Not |
|---|---|---|
| `Database/` | DOĞRUDAN | `DatabaseManager`, `DatabaseRegistry`, `Config/DatabaseConfig` |
| `Repository/` | DOĞRUDAN | `ApiKeyRepository` (+ diğer repository'ler) |
| `OAuth/` | DOĞRUDAN | `OAuthRepository` (+ `OAuthManager` çağırıcı) |
| `Contracts/Database/` | DOĞRUDAN | `IDatabaseManager`, `IDatabaseRegistry` |
| `Api/` | DOLAYLI | Gateway katmanı repository'leri çağırır |
| `Middleware/` | DOLAYLI | RateLimit/Authentication sorgu tetikler |
| `Cache/` | DIŞ | Sorgu sonucu cache'i (caching modülü) |
| `PageRouter/` | DIŞ | HTTP katmanı (server-runtime) |
| `Session/`, `Security/`, `Log/` | DIŞ | Yan etki katmanları |

---

## §2 Sayım PowerShell Bloğu (tekrar üretilebilir)

```powershell
# queries bileşen sayımı — repo kökünden çalıştır (spec §1.7)
$root = "C:\www\coremusic.net"

# 1) Sorgu katmanı dosyalarının satır sayısı
@(
  "$root\shared\src\Database\DatabaseManager.php",
  "$root\shared\src\Repository\ApiKeyRepository.php",
  "$root\shared\src\OAuth\OAuthRepository.php",
  "$root\home.coremusic.net\include\Repository\MusicRepository.php",
  "$root\media.coremusic.net\src\Media\CatalogWriter.php",
  "$root\bin\api-key-create.php",
  "$root\shared\tests\Repository\Sqlite\SqliteDatabaseManager.php"
) | ForEach-Object {
  if (Test-Path $_) {
    "{0,6}  {1}" -f [System.IO.File]::ReadAllLines($_).Length, $_.Replace("$root\","")
  } else {
    "  MISS  $_"
  }
}

# 2) Dizin bazlı PHP/SQL sayımları
(Get-ChildItem "$root\shared\src" -Recurse -Filter *.php).Count          # beklenen: 165
(Get-ChildItem "$root\shared\tests" -Recurse -Filter *.php).Count        # beklenen: 45
(Get-ChildItem "$root\.ai\.sql\mysql" -Filter *.sql).Count               # beklenen: 20
(Get-ChildItem "$root\assets.coremusic.net\Css\01_Abstracts" -Filter *.css).Count  # beklenen: 19

# 3) prepare() yoğunluğu (repo geneli *.php)
(Select-String -Path "$root\**\*.php" -Pattern 'prepare\(' -SimpleMatch).Count  # beklenen: 17
```

> ⚠️ VERIFICATION REQUIRED: bu blok bu oturumda **çalıştırılamadı** (`shell`
> → `permission.rejected`); sayımlar `filesystem`/`grep` araçlarıyla alındı ve
> §1.2/§7'de kayıtlıdır. Blok, orkestratörün repo kökünden çalıştırması için.

---

## §3 Bileşen Kataloğu (sınıf bazlı)

### §3.1 DatabaseManager (68 satır)

| Üye | Tip | Satır | Not |
|---|---|---|---|
| `$pdo` | `\PDO` (private) | 10 | Tek bağlantı örneği |
| `__construct(DatabaseConfig)` | kurucu | 12–28 | DSN + 4 option |
| `execute(string, array): array` | kamu | 30–36 | SELECT sarmalayıcı |
| `write(string, array): bool` | kamu | 38–42 | Yazma sarmalayıcı |
| `lastInsertId(): string` | kamu | 44–47 | Son INSERT id |
| `beginTransaction(): bool` | kamu | 49–52 | Transaction |
| `commit(): bool` | kamu | 54–57 | Transaction |
| `rollBack(): bool` | kamu | 59–62 | Transaction |
| `getPdo(): \PDO` | kamu | 64–67 | Kaçış kapısı |

### §3.2 ApiKeyRepository (317 satır)

| Üye | Satır | Not |
|---|---|---|
| `DB_KEY = 'auth'` | 33 | Veritabanı anahtarı |
| `RAW_KEY_PATTERN` | 36 | `cm_live|cm_test` deseni |
| `KEY_TYPES` | 38 | `user/service/server` |
| `SELECT_COLUMNS` | 40–41 | Açık sütun listesi |
| `__construct(IDatabaseRegistry, string)` | 43–46 | Registry enjeksiyonu |
| `db(): IDatabaseManager` | 48–51 | Registry çözümleme |
| `create(array): array` | 74–132 | INSERT + raw key (bir kez) |
| `verify(...): ?array` | 140–145 | `inspect` sarmalayıcı |
| `inspect(...): array` | 153–226 | Tam doğrulama akışı |
| `prefixFromRaw(string): ?string` | 231–238 | Statik yardımcı |
| `encodeScopes(array, array): string` | 247–257 | JSON encode |
| `decodeScopes(mixed): array` | 262–287 | JSON decode (2 şema) |
| `randomPrefix(): string` | 289–299 | 8 karakter |
| `randomSecret(): string` | 301–305 | 32 bayt → base64url |
| `toHexSafe(string): string` | 307–316 | binary→hex (test uyumu) |

### §3.3 OAuthRepository (120 satır)

| Üye | Satır | Sorgu |
|---|---|---|
| `__construct(\PDO)` | 13–15 | Enjeksiyon |
| `upsertConnection(array): int` | 27–50 | UPSERT |
| `findUserConnections(int): array` | 53–64 | SELECT (soft delete) |
| `softDeleteConnection(int, string): bool` | 67–78 | UPDATE soft delete |
| `insertState(array): void` | 85–92 | INSERT + SQL süre |
| `findValidState(string, string, int): ?array` | 95–110 | SELECT tek satır |
| `deleteState(string): bool` | 113–119 | DELETE |

### §3.4 MusicRepository (90+ satır)

| Üye | Satır | Sorgu |
|---|---|---|
| `$db: ?DatabaseManager` | 17 | Tembel bağlantı |
| `__construct(6 string/int)` | 19–27 | Config enjeksiyonu |
| `fromEnvironment(): self` | 30–43 | Ortam değişkenleri |
| `findPrimaryFile(string): ?array` | 51–69 | JOIN + UNHEX + LIMIT 1 |
| `findRecentTracks(): array` | 79–90+ | 3'lü JOIN + HEX |

---

## §4 Bileşen ↔ Akış İz Düşümü

### §4.1 API key doğrulama akışı (istek başına)

```text
HTTP istek (Bearer / api.coremusic.net)
  → AuthenticationMiddleware          [api.coremusic.net/index.php L154-159]
    → ApiKeyRepository::verify($raw)   [ApiKeyRepository.php L140]
      → inspect($raw)                  [L153]
        → prefixFromRaw (regex)        [L155/L233]
        → db()->execute(SELECT …)      [L160] → DatabaseManager::execute [L30]
          → pdo->prepare + execute     [L32-33]
        → hash_equals döngüsü          [L169-175]
        → denetimler (aktif/süre/IP/scope) [L181-205]
        → db()->write(UPDATE last_used) [L220]
      ← context | null
    ← middleware kararı (401/next)
```

### §4.2 OAuth state akışı (callback)

```text
auth akışı → OAuthRepository::insertState       [OAuthRepository.php L85]
  → prepare + execute (DATE_ADD 10 dk)          [L88-91]
callback → OAuthRepository::findValidState      [L95]
  → prepare + execute + fetch                   [L101-108]
  → OAuthRepository::deleteState (tek kullanımlık) [L113]
bağlantı kaydı → upsertConnection               [L27]
```

### §4.3 Müzik dosya akışı (home stream)

```text
home.coremusic.net player
  → MusicRepository::fromEnvironment             [MusicRepository.php L30]
  → findPrimaryFile($musicIdHex)                 [L51]
    → girdi denetimi (32 hex)                    [L53]
    → db()->execute(JOIN + UNHEX)                [L66]
      → DatabaseManager::execute                 [L30]
  ← {file_path, file_format, file_size} | null
  → stream handler (dosya sunumu)
```

### §4.4 CLI API key oluşturma

```text
bin/api-key-create.php
  → PDO prepare (2 çağrı)                        [L117, L125]
  → (ayrı kod yolu — DatabaseManager üzerinden değil)
```

> ⚠️ VERIFICATION REQUIRED: CLI'nin DatabaseManager kullanmadan doğrudan PDO
  açması ADR-002 ile gerilim yaratıyor mu? (§6-Q4)

---

## §5 Bağımlılık Grafiği

```text
                    ┌──────────────────────────────┐
                    │  IDatabaseManager (contract) │
                    │  IDatabaseRegistry (contract)│
                    └────────────▲─────────────────┘
                                 │ implements
                    ┌────────────┴─────────────────┐
                    │       DatabaseManager        │
                    │  (DatabaseConfig → new PDO)  │
                    └────────────▲─────────────────┘
                                 │ get($dbKey)
                    ┌────────────┴─────────────────┐
                    │      DatabaseRegistry        │
                    └───────▲──────────▲───────────┘
                            │          │
      ┌─────────────────────┘          └──────────────────────┐
      │ uses IDatabaseRegistry        uses IDatabaseManager    │
┌─────┴───────────────┐          ┌────────────────────────────┴──┐
│  ApiKeyRepository   │          │       MusicRepository         │
│  (auth DB)          │          │  (music DB · lazy PDO)        │
└─────────▲───────────┘          └────────────────▲──────────────┘
          │ called by                              │ called by
┌─────────┴───────────┐                   ┌────────┴─────────────┐
│ Authentication      │                   │ home stream handler  │
│ Middleware (api)    │                   │ + component fetch    │
└─────────────────────┘                   └──────────────────────┘

  (ayrı kol — doğrudan PDO enjeksiyonu)
┌─────────────────────┐     ┌───────────────────────┐
│  OAuthRepository    │     │  CatalogWriter (CLI)  │
│  (OAuthManager → PDO)│     │  bin/api-key-create   │
└─────────────────────┘     └───────────────────────┘
```

| Kenar | Yön | Kanıt |
|---|---|---|
| `ApiKeyRepository` → `IDatabaseRegistry` | yukarı | `ApiKeyRepository.php` L6 |
| `ApiKeyRepository` → `IDatabaseManager` | yukarı | `ApiKeyRepository.php` L5 |
| `DatabaseManager` → `\PDO` | aşağı | `DatabaseManager.php` L22 |
| `DatabaseManager` → `DatabaseConfig` | aşağı | `DatabaseManager.php` L5 |
| `MusicRepository` → `DatabaseManager` | aşağı | `MusicRepository.php` L6 |
| `OAuthRepository` → `\PDO` | aşağı (yan) | `OAuthRepository.php` L13 |
| `AuthenticationMiddleware` → `ApiKeyRepository` | aşağı | `api.coremusic.net/index.php` L154 (yapısal) |

---

## §6 Boşluklar & PLANNED

| # | Boşluk | Tür | Etki |
|---|---|---|---|
| 1 | `api_usage` yazıcısı okunmadı | ⚠️ VERIFICATION REQUIRED | Metrik akışı bilinmiyor |
| 2 | `DatabaseRegistry` dosyası okunmadı (yalnız contract + usage) | ⚠️ VERIFICATION REQUIRED | Çoklu DB davranışı özeti eksik |
| 3 | `CatalogWriter::upsertSql()` şablonu | ⚠️ VERIFICATION REQUIRED | SQL interpolasyonu denetimi |
| 4 | CLI `api-key-create` ADR-002 uyumu | ⚠️ VERIFICATION REQUIRED | Bağlantı sahipliği istisnası |
| 5 | Repository test dosyaları (ApiKey/Music) | ⚠️ VERIFICATION REQUIRED | Test kapsamı bilinmiyor |
| 6 | Read-model/projeksiyon sahibi | ⚠️ VERIFICATION REQUIRED (index.md devralma) | Mimari boşluk |
| 7 | Migration dizini (2 dosya) içerikleri | ⚠️ VERIFICATION REQUIRED | Şema evrimi kanıtı |
| 8 | `ATTR_PERSISTENT` üretim FPM ayarı | ⚠️ VERIFICATION REQUIRED | Bağlantı davranışı |

---

## §7 Ölçüm Kayıtları (2026-10-15)

| Ölçüm | Değer | Yöntem |
|---|---|---|
| `shared/src` PHP | 165 | filesystem |
| `shared/tests` PHP | 45 | filesystem |
| `.ai/.sql/mysql` SQL | 20 | filesystem |
| `Css/01_Abstracts` CSS | 19 | filesystem |
| `prepare(` eşleşme | 17 / 9 dosya | ripgrep |
| DatabaseManager satır | 68 | read |
| ApiKeyRepository satır | 317 | read |
| OAuthRepository satır | 120 | read |
| MusicRepository | 90+ (kısmi) | read (offset 90) |

---

## §A Ek — Hızlı Başvuru Kartı

```text
queries bileşenleri — tek ekran
  Çekirdek:    DatabaseManager (68) → DatabaseRegistry → Repository'ler
  Sorgular:    ApiKeyRepository (317) · OAuthRepository (120) · MusicRepository (90+)
  Sözleşmeler: IDatabaseManager · IDatabaseRegistry (Contracts/Database)
  Test:        SqliteDatabaseManager (SQLite double)
  Şema:        20 dosya — sorgu sözleşmesi coremusic_auth.sql
  Sayımlar:    shared/src 165 · tests 45 · sql 20 · abstracts-css 19
  Boşluk:      api_usage yazıcısı · registry dosyası · upsertSql şablonu
```

---

## §B Bileşen Kartları (Detay)

### B.1 DatabaseManager

| Alan | Değer |
|---|---|
| Dosya | `shared/src/Database/DatabaseManager.php` |
| Boyut | 68 satır (read ölçümü) |
| Sorumluluk | `\PDO` bağlantı kurulumu, `DatabaseConfig` okuma |
| Bağımlılık (yukarı) | `DatabaseConfig` — `DatabaseManager.php` L5 |
| Bağımlılık (aşağı) | `\PDO` — `DatabaseManager.php` L22 |
| Kalıcı bağlantı | `ATTR_PERSISTENT` davranışı üretim FPM ayarına bağlı — ⚠️ VERIFICATION REQUIRED |
| Kullanım alanı | `MusicRepository` (`MusicRepository.php` L6) |

### B.2 ApiKeyRepository

| Alan | Değer |
|---|---|
| Dosya | `home.coremusic.net/include/Repository/ApiKeyRepository.php` |
| Boyut | 317 satır (read ölçümü — modülün en büyük repository'si) |
| Sorumluluk | API anahtarı doğrulama, `api_keys` okuma/yazma |
| Bağımlılık (yukarı) | `IDatabaseRegistry` — L6 · `IDatabaseManager` — L5 |
| Şema | `coremusic_auth.sql` `api_keys` — L56 (tablo tanımı L56-79) |
| Çağrı yeri | `AuthenticationMiddleware` — `api.coremusic.net/index.php` L154 (yapısal) |
| Hazır statement | `prepare(` kanıtı modül geneli: 17 eşleşme / 9 dosya (ripgrep) |

### B.3 OAuthRepository

| Alan | Değer |
|---|---|
| Dosya | `home.coremusic.net/include/Repository/OAuthRepository.php` |
| Boyut | 120 satır (read ölçümü) |
| Sorumluluk | OAuth akışları (`OAuthManager` içinde) |
| Bağımlılık (aşağı) | `\PDO` — `OAuthRepository.php` L13 (yan kol) |
| Şema | ⚠️ VERIFICATION REQUIRED — OAuth tablo dosyası okunmadı |

### B.4 MusicRepository

| Alan | Değer |
|---|---|
| Dosya | `home.coremusic.net/include/Repository/MusicRepository.php` |
| Boyut | 90+ satır (kısmi okuma — offset 90 ile kesildi) |
| Sorumluluk | Katalog okuma, JOIN + `UNHEX` desenleri |
| Kanıt (JOIN) | `MusicRepository.php` L53-68 |
| Kanıt (UNHEX) | `MusicRepository.php` L81-87 |
| Bağımlılık (aşağı) | `DatabaseManager` — L6 (lazy PDO) |

### B.5 Sözleşme (Contract) Bileşenleri

| Contract | Kullanıcı | Kanıt |
|---|---|---|
| `IDatabaseManager` | `ApiKeyRepository` | `ApiKeyRepository.php` L5 |
| `IDatabaseRegistry` | `ApiKeyRepository` | `ApiKeyRepository.php` L6 |
| `\PDO` (doğrudan) | `DatabaseManager` · `OAuthRepository` | L22 · L13 |

> Konum: `shared/src/Contracts/Database/` — dosya listesi ⚠️ VERIFICATION REQUIRED (katalog okunmadı).

---

## §C Sorgu Yüzeyi Envanteri

| Yüzey | Değer | Yöntem |
|---|---|---|
| `prepare(` toplam eşleşme | 17 | ripgrep (2026-10-15) |
| `prepare(` içeren dosya | 9 | ripgrep |
| Kardeş doküman iddiası | 47 / 98 | ⚠️ VERIFICATION REQUIRED — benim ölçümümle (17/9) ÇELİŞİYOR |
| `shared/src` PHP dosyası | 165 | filesystem |
| `shared/tests` PHP dosyası | 45 | filesystem |
| `.ai/.sql/mysql` SQL dosyası | 20 | filesystem |

**Çelişki kaydı:** Kardeş `-kapsam`/indeks dokümanları 47 hazır statement / 98 dosya öne sürer; diske yapılan ripgrep ölçümü 17 / 9 verir. Fark kaynakları (hipotez, doğrulanmadı): (a) kardeş ölçüm farklı kök/desen kullanmış olabilir, (b) `prepare` yazımı değişken (`$stmt->prepare`, `DB::prepare` vb.) ile kaçmış olabilir. **Karar: bu modülde ölçüm değeri 17/9 olarak taşınır; 47/98 `⚠️ VERIFICATION REQUIRED` olarak kalır.**

---

## §D Şema → Bileşen Eşlemesi

| Tablo | Dosya | Satır | Yazan/Okuyan Bileşen | Kanıt |
|---|---|---|---|---|
| `api_keys` | `.ai/.sql/mysql/coremusic_auth.sql` | L56-79 | `ApiKeyRepository` (okuma/yazma) | sql L56 · repo usage |
| `api_usage` | `.ai/.sql/mysql/coremusic_auth.sql` | L81-98 | **yazıcı bilinmiyor** | ⚠️ VERIFICATION REQUIRED (§6.1) |
| Katalog tabloları | ⚠️ VERIFICATION REQUIRED | — | `MusicRepository` (JOIN/UNHEX) | `MusicRepository.php` L53-68 |
| OAuth tabloları | ⚠️ VERIFICATION REQUIRED | — | `OAuthRepository` | — |

---

## §E Test Bileşenleri

| Bileşen | Rol | Kanıt |
|---|---|---|
| `SqliteDatabaseManager` | SQLite test double — `IDatabaseManager` sahtesi | shared/tests (45 PHP) |
| `shared/tests` klasörü | 45 PHP dosyası | filesystem ölçümü |
| `ApiKeyRepository` testi | ⚠️ VERIFICATION REQUIRED | dosya varlığı doğrulanmadı |
| `MusicRepository` testi | ⚠️ VERIFICATION REQUIRED | dosya varlığı doğrulanmadı |

---

## §F Bileşenler Arası Veri Akışı (Adım Adım)

```text
[1] AuthenticationMiddleware (api index.php L154 yapısı)
      → ApiKeyRepository::find/hash karşılaştır
          → IDatabaseManager (L5) / IDatabaseRegistry (L6)
              → DatabaseManager → \PDO (L22)
                  → coremusic_auth.auth DB (api_keys, sql L56)
[2] home stream/component handler
      → MusicRepository (L6 lazy)
          → DatabaseManager (L22) → music DB
              → JOIN (L53-68) + UNHEX (L81-87)
[3] OAuthManager kolu (yan)
      → OAuthRepository → \PDO (L13) → auth DB
[4] CLI (bin/api-key-create)
      → CatalogWriter → PDO  (ADR-002 bağlantısız kural — istisna denetimi ⚠️)
```

| # | Kenar | Kanıt |
|---|---|---|
| 1 | Middleware → ApiKeyRepository | `api.coremusic.net/index.php` L154 (yapısal) |
| 2 | ApiKeyRepository → IDatabaseManager | `ApiKeyRepository.php` L5 |
| 3 | DatabaseManager → \PDO | `DatabaseManager.php` L22 |
| 4 | MusicRepository → DatabaseManager | `MusicRepository.php` L6 |
| 5 | OAuthRepository → \PDO | `OAuthRepository.php` L13 |

---

## §G Risk & Güvenlik Notları (Bileşen Bazında)

| # | Risk | Bileşen | Durum |
|---|---|---|---|
| 1 | SQL injection | Tüm repository'ler | `prepare(` 17/9 ölçümü hazır statement kanıtı; yine de 9 dosya dışındaki sorgu yüzeyi denetlenmedi → ⚠️ |
| 2 | `SELECT *` yasağı (AGENTS §5 SQL) | `MusicRepository` JOIN bloğu L53-68 | ⚠️ VERIFICATION REQUIRED — sütun listesi okundu mu doğrulanmadı |
| 3 | N+1 | Katalog akışı | ⚠️ VERIFICATION REQUIRED |
| 4 | `api_usage` ölçüm kaybı | metrik yazıcısı yok | §6.1 boşluğu |
| 5 | Bağlantı sızıntısı | `ATTR_PERSISTENT` | üretim FPM ayarı bilinmiyor → ⚠️ |
| 6 | CLI ADR-002 ihlali | `CatalogWriter::upsertSql()` | interpolasyon şablonu okunmadı → ⚠️ |

---

## §H Sık Kullanılan Sorgu Desenleri (Kanıtlı)

| Desen | Bileşen | Kanıt |
|---|---|---|
| Lazy PDO bağlantısı | `MusicRepository` | `MusicRepository.php` L6 |
| Registry üzerinden bağlantı çözümleme | `ApiKeyRepository` | L5-L6 |
| JOIN + `UNHEX` | `MusicRepository` | L53-68 · L81-87 |
| Config üzerinden `\PDO` kurulumu | `DatabaseManager` | L5 (config) · L22 (PDO) |
| Doğrudan `\PDO` enjeksiyonu (yan kol) | `OAuthRepository` | L13 |

**Yok (kanıtlanamadı):** cursor/`fetchAll` kullanımı, transaction/`beginTransaction`, seviye (LVL) sorguları — her biri ⚠️ VERIFICATION REQUIRED.

---

## §I Kapsam Dışı (Bu Modüle Girmeyenler)

| Konu | Nereye ait |
|---|---|
| HTTP yanıt üretimi (`ResponseEmitter`) | `server-runtime` modülü |
| Boot/error-reporting (`RuntimeBootstrap`) | `server-runtime` modülü |
| Rewrite/redirect (`.htaccess`, `web.config`) | `server-runtime` modülü |
| nginx upstream/proxy modülleri (web havuzu) | `server-runtime` web-arastirma (kendi havuzu) |

---

## §J Modül Sınırı Özeti (Bileşen Görünümü)

| Soru | Cevap | Kanıt |
|---|---|---|
| Modülün çekirdek bileşeni hangisi? | `DatabaseManager` (68 satır) — tüm PDO bağlantısının merkezi | `DatabaseManager.php` L22 |
| En büyük bileşen? | `ApiKeyRepository` (317 satır) | read ölçümü |
| Sözleşmeli bağımlılık sayısı? | 2 (`IDatabaseManager` + `IDatabaseRegistry`) | L5 · L6 |
| Doğrudan `\PDO` alan bileşen sayısı? | 2 (`DatabaseManager` L22 · `OAuthRepository` L13) | read ölçümü |
| Test sahtesi? | `SqliteDatabaseManager` | shared/tests |
| Ölçüm çelişkisi? | Var: prepare( 17/9 vs kardeş iddia 47/98 | §C |

> Bu tablo `-bilesenler` dosyasının kapanış kontrolüdür; her satır yukarıdaki bölümlerdeki kanıta işaret eder.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
