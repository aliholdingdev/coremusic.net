---
node_id: "K007-data/test-stratejisi"
node_type: "domain"
status: "planned"
parent_id: "K007-data"
children: []
depends_on:
  - "K007-data/data-master"
  - "K007-data/data-kararlar"
  - "K007-data/data-riskler"
evidence:
  - "shared/tests/ — 2026-10-12 glob: 44 PHP dosyası (42 test + 2 fixture) · Repository/Sqlite/ SqliteDatabaseManager+SqliteDatabaseRegistry test çiftleri"
  - "shared/tests/Repository/ApiKeyRepositoryTest.php · ApiKeyCreateCommandTest.php — repository testleri mevcut"
  - "shared/composer.json (require-dev PHPUnit) · shared/phpunit.xml — shared/AGENTS.md §2 envanteri"
validation: "2026-10-12 — envanter diskten glob ile sayıldı; test N passed iddiası YOK (çalıştırma kanıtı yok); hedefler taslak olarak işaretli."
open_questions:
  - "phpunit suite'inin güncel geçme durumu (pass/fail) — bu oturumda çalıştırılmadı (UNKNOWN)"
  - "Coverage yüzdesi ölçülmedi — K007 hedefi sayı olarak yazılmadı"
  - "Migration/backup testleri için ortam (canlı DB'ye asla dokunmadan) nasıl kurulacak — UNKNOWN"
group: kod
---

# K007-data / test-stratejisi — Test Stratejisi

> **Dürüstlük notu:** Bu dosyada "X testi geçiyor" veya "coverage %N" iddiası **yoktur** —
> testler bu oturumda çalıştırılmadı. Var olan envanter (disk kanıtı) ile önerilen
> kapsam (taslak) ayrı ayrı işaretlenmiştir.

---

## 1. Mevcut test envanteri (disk kanıtı — 2026-10-12)

**Tarama:** `shared/tests/**/*.php` glob → **44 dosya** (42 test dosyası + 2 fixture
`Fixtures/chrome/header.php`, `Fixtures/chrome/footer.php`).

### K007 ile doğrudan ilgili testler

| Dosya | Ne test eder | K007 çocuğu |
|---|---|---|
| `tests/Repository/ApiKeyRepositoryTest.php` | Repository okuma/yazma/dogrulama akışı | repositories |
| `tests/Repository/ApiKeyCreateCommandTest.php` | Anahtar üretim komutu (hash, prefix) | repositories |
| `tests/Repository/Sqlite/SqliteDatabaseManager.php` | **Test çifti:** SQLite tabanlı sahte DatabaseManager | repositories, transactions |
| `tests/Repository/Sqlite/SqliteDatabaseRegistry.php` | Test çifti sahte registry | repositories |

**Kritik tasarım bulgusu:** Repository testleri canlı MySQL'e değil, **SQLite'a çevrilen
test çiftlerine** çalışır. Bunu destekleyen ikinci kanıt `ApiKeyRepository::toHexSafe()`
içindeki yorumdur (`shared/src/Repository/ApiKeyRepository.php:313-315`: "SQLite test
ortamı gibi binary yerine hex tutan katmanlarda"). **Sonuç:** testlerde `binary(16)`
UUID davranışı ile üretim MySQL davranışı **farklıdır** — bu bir test-prensibi ve aynı
zamanda açık risk (bkz. §4 T-06).

### Komşu (K007'yi dolaylı kapsayan) testler

- `Unit/PageRouter/*` (7 dosya) → sayfa sunumu; `page_auth_`/`page_pub_` anahtarlarının
  çağrısı bu katmanda olabilir (`⚠️ VERIFICATION REQUIRED` — hangi test pageKey'i
  invoker ediyor okunmadı).
- `Middleware/*` (10 dosya) + `Security/*` + `Api/*` (9 dosya) → veriye erişen katmanlar.
- `Events/*` (2 dosya) → K-05 tetikleyicisi için altyapı testleri (event dispatcher).

### Çocuk bazlı mevcut durum

| Çocuk | Doğrudan test | Durum |
|---|---|---|
| schema | Yok (şema doğrulaması test değil, döküm denetimi) | `EKSİK (taslak)` |
| migrations | Yok | `EKSİK (taslak)` |
| repositories | 2 test + 2 test çifti | `MEVCUT` |
| transactions | Yok (transaction testi repository testlerine gizlenmiş olabilir — okunmadı) | `UNKNOWN` |
| indexing | Yok | `EKSİK (taslak)` |
| cache | Yok (CacheManager/PageCache testi listede yok) | `EKSİK (taslak)` |
| search | Yok (motor yok) | `YOK — K-06'ya bağlı` |
| synchronization | Yok | `EKSİK (taslak)` |
| backup | Yok | `EKSİK (taslak)` |
| data-quality | Kısmi: `LoggerRedactionTest` veri-maskeleme benzeri (log yüzeyi) | `KISMİ` |

---

## 2. Test prensipleri (K007 için bağlayıcı)

1. **Canlı DB'ye test yazma:** testler SQLite test çifti ya da izole şema ile çalışır;
   üretim veritabanına `DROP/INSERT` yapan test yasaktır.
2. **Yıkıcı migration testi = kopya üzerinde:** Ö-9 deseni (`precheck` + kopya DB)
   test ortamında birebir uygulanır; canlıda test edilmez.
3. **Kural testleri (lint-yakın):** `SELECT *` yasağı (R-03), `PDO` tek-nokta (K-02),
   ORM yasağı — bunlar unit test değil **statik/dizin taraması** ile denetlenir;
   test stratejisi bu ikisini ayırır.
4. **Deterministik:** `random_int`/`random_bytes` üreten üretim kodu (ApiKeyRepository
   Ö-6) testte enjekte edilebilir değildir → test **davranış** (desen/uzunluk/hash
   eşleşmesi) üzerinden yazılır, sabit değere bağlanmaz.
5. **Doğrulama seviyesi:** `⚠️ VERIFICATION REQUIRED` işaretli hiçbir satır "test edildi"
   diye sayılmaz.

---

## 3. Önerilen test kapsamı (taslak — henüz yazılmadı)

### T-01 · Connection & option locked tests — `transactions`, `schema`

- Assertion: `PDO::ATTR_EMULATE_PREPARES === false`, `ERRMODE === ERRMODE_EXCEPTION`,
  `FETCH_ASSOC`, `PERSISTENT === true` (Ö-1 dört option'ı) — regresyonda kilitlenir.
- Sınır: gerçek bağlantı açmadan **DSN + option dizisini** üreten katman test edilir
  (DatabaseConfig sahtesi ile).

### T-02 · Transaction rollback testi — `transactions`

- Senaryo: begin → iki write → rollBack → satır yok; begin → commit → satır var.
- Sınır: **tek DB** (test çifti üzerinden). Dağıtık senaryo K-07 kararı sonrası (R-01).

### T-03 · Prepared statement zorunluluğu — `repositories`

- Senaryo: paramsız string birleştirme denemesi repository arayüzünden reddedilmeli
  (imkân varsa) ya da depo taraması `execute(` çağrılarının %100'ünde params argümanı
  içerdiğini doğrular.

### T-04 · `SELECT *` yasağı taraması — tüm çocuklar

- Statik tarama: `shared/src/**` içinde `SELECT *` kalıbı → 0 isabet hedefi.
  (Test değil, gate — CI'a `rg "SELECT \*"` adımı.)

### T-05 · Cache anahtar + TTL testleri — `cache`

- `pageKey('x', false)` → `page_pub_` + `md5('x')` öneki; `pageKey('x', true)` →
  `page_auth_` (Ö-4).
- `remember`: cache boş → callback 1× çalışır; cache dolu → callback 0× çalışır.
- **R-05 testi:** cache'lenmiş `false` değeri "yok" sayılmamalı (mevcut davranış
  kırılacaksa bilinçli kırılır).

### T-06 · UUID binary/hex ayrıştırması — `schema`, `repositories`

- Üretim: `toBinary(hex)` 16 bayt; test çifti (SQLite): hex string. `toHexSafe`
  her iki durumda da geçerli hex döndürmeli (Ö-6/Ö-7 alıcısı).
- **Amaç:** test-ortamı ile üretim ayrışmasının regresyonu erken yakalanır.

### T-07 · Migration deseni testi — `migrations`

- Her migration dosyası yüklendiğinde dönüş tipi sözleşmesine uymalı:
  **ya** `list<string>` (Ö-8) **ya** anahtarlı dizi (Ö-9) — ve standart kararı
  verildikten sonra tek tip zorlanır (R-12).
- Yıkıcı migration: `destructive=true` → `approval` ve `precheck` anahtarları
  mevcut olmalı (eksikse test kırmızı).

### T-08 · Soft delete filtresi — `schema`, `repositories`, `data-quality`

- Okuma yollarında `is_deleted = 0` filtresi var mı (Ö-7 örneği);
- Yazma yollarında `deleted_at` set ediliyor mu.

### T-09 · Yedek geri dönüş enstrümanı — `backup`

- Küçük bir kopya DB: `mysqldump` benzeri export → DROP → `CREATE+INSERT` geri yükleme
  → satır sayısı eşit. (Ö-9 rollback tarifinin birebir tatbikatı; yalnızca kopya DB'de.)

### T-10 · Veri kalitesi kapıları — `data-quality`

- Import/seed sonrası nitelik kontrolleri: zorunlu alan null değil, ENUM geçerli,
  FK bütünlüğü (arası-DB referanslar R-02 dikkate alınarak işaretli).

---

## 4. Test pyramid dağılımı (öneri, hedef değil)

| Katman | K007'de ağırlık | Neden |
|---|---|---|
| Unit (test çifti, SQLite) | **Yüksek** | Mevcut 2 repository testi zaten bu katmanda |
| Integration (gerçek MySQL, izole şema) | **Orta** — ama `UNKNOWN` ortam | binary(16)/InnoDB davranışı ancak burada görülür (T-06) |
| Statik/lint gate | **Yüksek** | SELECT *, ORM, tek-PDO kuralları (T-03/T-04) |
| E2E | Düşük (K006/K011 sahası) | K007'nin kullanıcı-arayüzü testi yok |

---

## 5. Çalıştırma & kanıt disiplini

1. Çalıştırma komutu `shared/` içinden PHPUnit ile yapılır (`shared/phpunit.xml`,
   `shared/composer.json` require-dev); ⚠️ **bu oturumda çalıştırılmadı** — sonuç
   `UNKNOWN`.
2. Geçen test sayısı/coverage raporu yazıldığında `validation` alanı güncellenir;
   sayı uydurulmaz.
3. Gate script'ler `.ai/scripts/*.ps1` repo **kökünden** çalıştırılır (kök AGENTS.md
   §13 kuralı) — K007 için bu oturumda gate çalıştırılmadı.
4. Test eklenen her dosya için `data-dogrulama.md`'ye depo satırı eklenir
   (dosya yolu + neyi kanıtlar).

---

## 6. Kabul kriterleri (K007 test olgunluğu)

- [ ] T-01..T-08 taslakları somut test dosyalarına dönüşmüş (henüz yok).
- [ ] R-12 migration deseni kararı verilmiş ve T-07 tek tipe bağlanmış.
- [ ] `shared/tests` envanteri yeniden sayılmış ve bu dosyadaki 44 rakamıyla
  karşılaştırılmış (fark varsa dosya güncellenir).
- [ ] Test çalıştırma çıktısı (pass/fail) `validation` alanına tarihle yazılmış.
- [ ] Coverage ölçümü — **hedef sayı K007'de tanımlı değil** (`UNKNOWN`; kök standart
  QA ≥%80'dir, `.ai/AGENTS.md` §16, K007'e uygulanıp uygulanmayacağı karar bekliyor).
