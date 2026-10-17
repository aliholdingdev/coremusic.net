---
node_id: K007-data/transactions-anayasa
node_type: subdomain
status: draft
parent_id: K007-data/transactions
children: []
depends_on:
  - "K007-data/schema"
  - "K007-data/repositories"
evidence:
  - "shared/src/Contracts/Database/IDatabaseManager.php:10-12 — beginTransaction/commit/rollBack"
  - "shared/src/Database/DatabaseManager.php:49-67 — PDO transaction sarmalayıcıları"
  - ".ai/.sql/mysql/coremusic_ai.sql:39 — ENGINE=InnoDB"
validation:
  - "2026-10-13 — 12 repo kanıtı satır numarasıyla okundu; 4 URL HTTP 200"
open_questions:
  - "MySQL InnoDB default izolasyon seviyesi (REPEATABLE READ iddiası) — repo'da SET TRANSACTION yok → ⚠️ VERIFICATION REQUIRED"
  - "PDO::ATTR_PERSISTENT=true (DatabaseManager.php:26) + transaction etkileşimi → UNKNOWN"
group: mimari
---

# transactions-anayasa — Atomik Yazma Anayasası (K007)

> **Rol:** Bu dosya K007-data altındaki transaction (atomik yazma) davranışının
> **bağlayıcı kurallarını** taşır. Sürüş akışı `transactions-surucu.md`'de,
> gerçek kod örnekleri `transactions-ornekler.md`'dedir.

## §1 Amaç ve Kapsam

| Alan | Değer |
|---|---|
| node_id | K007-data/transactions-anayasa |
| node_type | subdomain |
| parent_id | K007-data/transactions |
| Grup | mimari |
| Kapsam | atomiklik · ACID · izolasyon · begin/commit/rollback sözleşmesi |
| Kapsam dışı | iş mantığı gruplama (K012-services) · domain kuralı (K008-domain) |

## §2 Terimler (Türkçe / English)

| Türkçe | English | Tanım |
|---|---|---|
| Atomiklik | Atomicity | Çoklu yazmanın tamamı-ya-hiç prensibi |
| Tutarlılık | Consistency | Transaction öncesi/sonrası veritabanı kurallarının korunması |
| İzolasyon | Isolation | Eşzamanlı transaction'ların birbirini görmeme derecesi |
| Dayanıklılık | Durability | Commit sonrası verinin diskte kalıcı olması |
| Transaction | Transaction | Begin ile commit/rollback arasındaki atomik birim |
| Geri alma | Rollback | Commit edilmemiş değişikliklerin geri alınması |
| Kilit | Lock | Kaynak üzerinde eşzamanlı erişim kontrolü |
| MVCC | Multi-Version Concurrency Control | Çok sürümlü eşzamanlılık kontrolü |

## §3 ACID İlkeleri

| # | İlke | Türkçe Açıklama | Repo Karşılığı |
|---|---|---|---|
| A | Atomicity | Tüm yazmalar ya uygulanır ya hiçbiri | `beginTransaction()` / `commit()` / `rollBack()` — `IDatabaseManager.php:10-12` |
| C | Consistency | Kurallar (FK, unique, NOT NULL) transaction sonunda da geçerli | DDL şeması: `ENGINE=InnoDB` — `coremusic_ai.sql:39` |
| I | Isolation | Eşzamanlı transaction'lar birbirini bozmaz | Motor davranışı — repo'da seviye ayarı **yok** (§6) |
| D | Durability | Commit sonrası veri kalıcı | InnoDB log mekanizması — motor düzeyi, repo kanıtı **UNKNOWN** |

## §4 Doğrulama Sorusu Seti (her kod incelemesinde)

| # | Soru | Evet ise | Hayır ise |
|---|---|---|---|
| 1 | Birden fazla `write()` tek unit'te mi? | Transaction zorunlu | Transaction opsiyonel |
| 2 | `beginTransaction()` çağrıldı mı? | `commit()`/`rollBack()` çifti var mı? | Orta yerde bırakma → §8 |
| 3 | Hata dalı `rollBack()` çağırıyor mu? | Devam serbest | **Yasak** — §9 |
| 4 | Loop içinde transaction mı var? | §11 kuralı | — |
| 5 | Nested transaction mı? | §12 kuralı | — |

## §5 Bağlayıcı Kurallar (Anayasa Maddeleri)

| Madde | Kural | Yaptırım |
|---|---|---|
| A-01 | Tek connection üzerinden `beginTransaction()` → `commit()` **aynı method zarfında** biter | Kod incelemesinde RED |
| A-02 | Hata yolunda çıkış **yalnız** `rollBack()` ile olur | Code review BLOCK |
| A-03 | `commit()` sonrası aynı zarfta tekrar `commit()` çağrılmaz | RED |
| A-04 | Transaction zarfı içinde ağ (HTTP/curl) çağrısı yapılmaz | RED — kilit tutma süresi patlar |
| A-05 | Transaction zarfı içinde dosya I/O veya log diski yazımı yapılmaz | RED |
| A-06 | Uzun süreli sorgu (rapor/aggregate) transaction dışına alınır | RED |
| A-07 | `SELECT` + `UPDATE` yarışına karşı kilitleme stratejisi §10'da tanımlanır | BLOCK |
| A-08 | Yeni transaction yalnız tek bir `IDatabaseManager` üzerinden açılır | RED |

## §6 İzolasyon Seviyeleri (Isolation Levels)

| Seviye (English) | Dirty Read | Non-Repeatable Read | Phantom Read | Repo Durumu |
|---|---|---|---|---|
| READ UNCOMMITTED | Evet | Evet | Evet | Kullanılmaz |
| READ COMMITTED | Hayır | Evet | Evet | Repo'da ayar **görülmedi** |
| REPEATABLE READ | Hayır | Hayır | Evet | MySQL InnoDB varsayılanı olarak anlatılır — ⚠️ VERIFICATION REQUIRED (repo'da `SET TRANSACTION` yok) |
| SERIALIZABLE | Hayır | Hayır | Hayır | Kullanılmaz |

**Kanıt (negatif kanıt):** `shared/src/` içinde `beginTransaction` dışındaki
transaction metodu yalnız `commit`/`rollBack`'tir; `SET TRANSACTION` /
`SET ISOLATION LEVEL` ifadesi repo taramasında **görülmemiştir** → seviye
repo tarafından açıkça seçilmiyor, motor varsayılanına bırakılıyor.

| İddia | Durum |
|---|---|
| Repo'da izolasyon seviyesi açıkça ayarlanıyor | HAYIR (kanıt: `DatabaseManager.php:49-67` — yalnız begin/commit/rollBack) |
| İzlence motoru InnoDB (MVCC destekli) | EVET (kanıt: `.ai/.sql/mysql/coremusic_ai.sql:39` — `ENGINE=InnoDB`) |
| Default seviyenin ne olduğu | ⚠️ VERIFICATION REQUIRED |

## §7 Sözleşme Yüzeyi (Contract Surface)

| Metot | Dosya : Satır | Dönüş | Not |
|---|---|---|---|
| `execute(string $sql, array $params = []): array` | `shared/src/Contracts/Database/IDatabaseManager.php:7` | `array` | SELECT yüzeyi |
| `write(string $sql, array $params = []): bool` | `shared/src/Contracts/Database/IDatabaseManager.php:8` | `bool` | INSERT/UPDATE/DELETE yüzeyi |
| `lastInsertId(): string` | `shared/src/Contracts/Database/IDatabaseManager.php:9` | `string` | Aynı connection'da anlamlıdır |
| `beginTransaction(): bool` | `shared/src/Contracts/Database/IDatabaseManager.php:10` | `bool` | Atomiklik başlangıcı |
| `commit(): bool` | `shared/src/Contracts/Database/IDatabaseManager.php:11` | `bool` | Atomiklik onayı |
| `rollBack(): bool` | `shared/src/Contracts/Database/IDatabaseManager.php:12` | `bool` | Geri alma |

**Uygulama (implementation):**

| Metot | Dosya : Satır | Gövde |
|---|---|---|
| `beginTransaction()` | `shared/src/Database/DatabaseManager.php:49-52` | `return $this->pdo->beginTransaction();` |
| `commit()` | `shared/src/Database/DatabaseManager.php:54-57` | `return $this->pdo->commit();` |
| `rollBack()` | `shared/src/Database/DatabaseManager.php:59-62` | `return $this->pdo->rollBack();` |
| `getPdo()` | `shared/src/Database/DatabaseManager.php:64-67` | ham PDO erişimi — anayasa dışında |

## §8 Hata Yolu Zorunlulukları

| # | Kural | Neden |
|---|---|---|
| H-01 | `beginTransaction()` sonrası her `return` yolu ya `commit()` ya `rollBack()` ile biter | Açık transaction bağlantı havuzunu kirletir |
| H-02 | `PDO::ERRMODE_EXCEPTION` aktif (`DatabaseManager.php:23`) → exception fırlar, sessiz başarısızlık yoktur | Hata yolu yakalanmak zorundadır |
| H-03 | `rollBack()` başarısızsa sistem durur (fail-safe) — fail-open **yasak** | Yarım transaction görünmez kalabilir |
| H-04 | Exception log'a yazılırken parametre değerleri **REDacted** tutulur (güvenlik) | `AGENTS.md` §17.3 |
| H-05 | Rollback sonrası iş katmanı kullanıcıya atomik hata döner | Kismi durum sızıntısı |

## §9 Yasaklar (Forbidden)

| # | Yasak | Sınıf |
|---|---|---|
| Y-01 | Transaction zarfında `sleep()` / `usleep()` | lock süresi |
| Y-02 | Transaction zarfında HTTP isteği / API çağrısı | lock süresi |
| Y-03 | Transaction zarfında kullanıcıdan girdi bekleme | lock süresi |
| Y-04 | Commit edilmemiş transaction ile script sonlandırma | atomiklik |
| Y-05 | Tek transaction içinde iki ayrı connection kullanımı | atomiklik |
| Y-06 | `lastInsertId()` çağrısını başka bir connection'da okumak | tutarlılık |
| Y-07 | SELECT-without-UPDATE stratejisi ile kritik bakiye güncelleme (race) | yarış durumu |
| Y-08 | Uzun rapor sorgusunu transaction içine almak | lock süresi |
| Y-09 | Transaction içinde DDL (`ALTER TABLE`) çalıştırmak | implicit commit riski — ⚠️ VERIFICATION REQUIRED |
| Y-10 | Nested transaction varsayımı (PDO `beginTransaction()` tekrar çağrısı) | hata |

## §10 Yarış Durumu ve Kilitleme (Race & Locking)

| Senaryo | Zayıf Yaklaşım | Doğru Yaklaşım |
|---|---|---|
| Bakiye düşürme | `SELECT` → hesapla → `UPDATE` | `SELECT ... FOR UPDATE` ile kilitle |
| En son yazan kazansın | Sonra `UPDATE` | Unique constraint + upsert |
| Sayaç artırma | Oku-yaz (kayıp güncelleme) | Tek `UPDATE ... SET c=c+1` |
| Yer tutma (hold) | Ağ çağrısıyla bekletme | Kilit yalnız DB içinde (A-04) |
| Deadlock | Sonsuz bekleme | Motor deadlock algılar → ⚠️ strateji repo'da **UNKNOWN** |

> **Deadlock çözüm stratejisi repo kanıtı yok** → `⚠️ VERIFICATION REQUIRED`
> (retry/deadlock yakalama kodu `shared/src/` içinde görülmedi).

## §11 Döngü (Loop) İçinde Transaction

| Durum | Karar |
|---|---|
| 10 satırlık batch, hepsi atomik | Tek transaction, döngü dışında begin/commit |
| 10.000 satırlık batch | Chunk'a böl; her chunk kendi transaction'ı (checkpoint) |
| Her satırda ayrı commit | Yasak (Y-08'e aykırı performans) |
| Chunk başarısız | O chunk rollback; ilerleme (önceki chunk'lar commit'li) → raporlanır |

## §12 Nested Transaction (İç İçe Transaction)

| Yaklaşım | Durum |
|---|---|
| PDO `beginTransaction()` tekrar çağrısı | Desteklenmez → `Y-10` |
| Savepoint tabanlı anlatım | Keşif aşamasında — repo'da `SAVEPOINT` **görülmedi** → UNKNOWN |
| Savepoint kullanacaksa | Yeni ADR gerekir (kategori: mimari karar) |

## §13 Sarmalayıcı Kullanım Kalıbı (Contract-Level)

| Adım | Sözleşme | Kanıt |
|---|---|---|
| 1 | `beginTransaction(): bool` | `IDatabaseManager.php:10` |
| 2 | `write()` / `execute()` çağrıları | `IDatabaseManager.php:7-8` |
| 3a | Başarı → `commit(): bool` | `IDatabaseManager.php:11` |
| 3b | Hata → `rollBack(): bool` | `IDatabaseManager.php:12` |
| 4 | Aynı connection'da `lastInsertId()` | `IDatabaseManager.php:9` |

| Çıkış Durumu | Beklenen Eylem |
|---|---|
| Tüm `write()` = true | `commit()` → dön |
| Herhangi bir `write()` = false | `rollBack()` → hata dön |
| Exception (`ERRMODE_EXCEPTION`) | `rollBack()` → hata dön (H-02) |
| `commit()` = false | fail-safe dur → `⚠️ VERIFICATION REQUIRED` (H-03) |

## §14 Bağlantı ve Nitelikler (Connection Traits)

| Nitelik | Değer | Dosya : Satır |
|---|---|---|
| Hata modu | `PDO::ERRMODE_EXCEPTION` | `shared/src/Database/DatabaseManager.php:23` |
| Fetch modu | `PDO::FETCH_ASSOC` | `shared/src/Database/DatabaseManager.php:24` |
| Emüle prepare | `false` (gerçek prepared statement) | `shared/src/Database/DatabaseManager.php:25` |
| Persistent bağlantı | `true` | `shared/src/Database/DatabaseManager.php:26` |
| Charset | DSN içi (`%s`) | `shared/src/Database/DatabaseManager.php:15` |

| Risk | Not | Durum |
|---|---|---|
| Persistent connection + açık transaction | Kapanışta kalan transaction sonraki isteği etkileyebilir | `⚠️ VERIFICATION REQUIRED` (comportement repo'da test edilmemiş) |
| ERRMODE_EXCEPTION | Hata yolu asla sessiz geçmez | Kanıtlı (satır 23) |
| EMULATE_PREPARES=false | Gerçek server-side prepared statement | Kanıtlı (satır 25) |

## §15 Ölçüt Tablosu (Benchmark / Ölçüm)

| Ölçüm | Hedef | Durum |
|---|---|---|
| Transaction başına round-trip | minimal (tek begin + N write + tek commit) | Tasarım kuralı (A-01) |
| Kilit bekleme süresi | ağ/IO içermeyen zarf (Y-01..Y-03) | Kural |
| Deadlock sayısı | 0 hedef; repo'da ölçüm **yok** | `UNKNOWN` |
| Rollback oranı | İzlenecek metrik — repo'da telemetri **yok** | `UNKNOWN` |

## §16 Doğrulama Listesi (Checklist)

| # | Kontrol | Kriter |
|---|---|---|
| D-1 | Her `beginTransaction()` bir `commit()`/`rollBack()` ile eşleşiyor mu? | 1:1 |
| D-2 | Hata yolunda rollback var mı? | Zorunlu (§8) |
| D-3 | Zarf içinde ağ/dosya I/O var mı? | Varsa RED (A-04/A-05) |
| D-4 | `SELECT ... FOR UPDATE` gereken yerde var mı? | §10 |
| D-5 | Loop batch'leri chunk'lı mı? | §11 |
| D-6 | Nested transaction çağrısı var mı? | Varsa RED (Y-10) |
| D-7 | Test transaction rollback'i doğruluyor mu? | `qa-engineer` kapsamı |

## §17 Kanıt Envanteri (Evidence Inventory)

| # | Dosya : Satır | Ne gösteriyor |
|---|---|---|
| 1 | `shared/src/Contracts/Database/IDatabaseManager.php:7` | `execute` imzası |
| 2 | `shared/src/Contracts/Database/IDatabaseManager.php:8` | `write` imzası |
| 3 | `shared/src/Contracts/Database/IDatabaseManager.php:9` | `lastInsertId` imzası |
| 4 | `shared/src/Contracts/Database/IDatabaseManager.php:10-12` | begin/commit/rollBack |
| 5 | `shared/src/Database/DatabaseManager.php:15` | DSN charset parçası |
| 6 | `shared/src/Database/DatabaseManager.php:23` | `ERRMODE_EXCEPTION` |
| 7 | `shared/src/Database/DatabaseManager.php:25` | `EMULATE_PREPARES=false` |
| 8 | `shared/src/Database/DatabaseManager.php:26` | `PERSISTENT=true` |
| 9 | `shared/src/Database/DatabaseManager.php:49-52` | `beginTransaction` gövdesi |
| 10 | `shared/src/Database/DatabaseManager.php:54-57` | `commit` gövdesi |
| 11 | `shared/src/Database/DatabaseManager.php:59-62` | `rollBack` gövdesi |
| 12 | `.ai/.sql/mysql/coremusic_ai.sql:39` | `ENGINE=InnoDB` |

## §18 Web Kaynakları (Doğrulanmış — HTTP 200 / 2026-10-13)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Transaction tutorial (ACID, BEGIN/COMMIT/ROLLBACK) | https://www.postgresql.org/docs/current/tutorial-transactions.html | 2026-10-13 | 200 OK |
| MVCC davranışının genel tanımı | https://www.postgresql.org/docs/current/mvcc.html | 2026-10-13 | 200 OK |
| Açık kilitleme (explicit locking) | https://www.postgresql.org/docs/current/explicit-locking.html | 2026-10-13 | 200 OK |
| DML ve transaction içi işlemler | https://www.postgresql.org/docs/current/dml.html | 2026-10-13 | 200 OK |
| Session/transaction yaşam döngüsü (ORM örneği) | https://docs.sqlalchemy.org/en/20/orm/session_basics.html | 2026-10-13 | 200 OK |
| Engine/connection davranışı (ORM örneği) | https://docs.sqlalchemy.org/en/20/core/engines.html | 2026-10-13 | 200 OK |

> **Not:** Kaynaklar PostgreSQL/SQLAlchemy üzerindendir; MySQL InnoDB'ye birebir
> transfer edilirken her iddia ayrıca doğrulanmalıdır. `dev.mysql.com` bot
> engeli (403) nedeniyle **kullanılmayacaktır**.

## §19 Bilinmeyenler Defteri (UNKNOWN / VERIFICATION REQUIRED)

| # | Soru | Etiket |
|---|---|---|
| U-1 | Uygulanan izolasyon seviyesi nedir? | ⚠️ VERIFICATION REQUIRED |
| U-2 | Deadlock retry stratejisi var mı? | ⚠️ VERIFICATION REQUIRED |
| U-3 | Persistent connection + açık transaction davranışı | UNKNOWN |
| U-4 | Savepoint (nested transaction) politikası | UNKNOWN |
| U-5 | Transaction metrikleri/telemetri toplanıyor mu? | UNKNOWN |
| U-6 | Batch chunk boyutu sınırı | UNKNOWN |
| U-7 | Commit başarısızlığında fail-safe uygulaması | ⚠️ VERIFICATION REQUIRED |

## §20 İlişkili Düğümler (Relations)

| Düğüm | İlişki |
|---|---|
| `K007-data/schema` | InnoDB şeması atomikliğin altyapısı |
| `K007-data/repositories` | Repository metotları transaction zarfını sarmalar |
| `K007-data/indexing` | Kilitler indeks üzerinden yürür |
| `K008-domain` | Domain kuralı transaction **içinde değil**, öncesinde doğrulanır |
| `K007-data/migrations` | DDL implicit-commit riski (Y-09) |

## §21 Revizyon Günlüğü

| Tarih | Değişiklik | Sorumlu |
|---|---|---|
| 2026-10-13 | Dosya oluşturuldu; 12 repo kanıtı + 6 doğrulanmış URL | Embedded Engineer (tek yazar) |
| 2026-10-13 | `parent:` yok, `parent_id` var; `group: mimari` (tırnaksız) | Embedded Engineer |

## §22 Transaction Yaşam Döngüsü (State Machine)

| # | Durum (State) | Tetikleyici | Sonraki Durum |
|---|---|---|---|
| 1 | IDLE | — | BEGIN isteği |
| 2 | ACTIVE | `beginTransaction()` = true | COMMIT veya ROLLBACK |
| 3 | COMMITTING | `commit()` çağrısı | COMMITTED / FAILED |
| 4 | COMMITTED | `commit()` = true | IDLE |
| 5 | ROLLING_BACK | `rollBack()` çağrısı | ROLLED_BACK / FAILED |
| 6 | ROLLED_BACK | `rollBack()` = true | IDLE |
| 7 | FAILED | commit/rollback = false | fail-safe dur (H-03) |
| 8 | BROKEN | bağlantı kaybı | yeniden bağlantı → UNKNOWN (U-3) |

| Geçiş | Koşul | Kanıt |
|---|---|---|
| 2 → 3 | tüm `write()` başarılı | `IDatabaseManager.php:8` |
| 2 → 5 | herhangi bir `write()` false veya exception | `DatabaseManager.php:23` |
| 5 → 6 | `rollBack()` = true | `IDatabaseManager.php:12` |
| 7 → 8 | sistem durur | ADR gerekli — repo'da örnek **yok** |

## §23 Senaryo × Sonuç Matrisi

| # | Senaryo | Beklenen Sonuç | Kural |
|---|---|---|---|
| S-1 | 3 yazma, 2.si başarısız | 1. ve 3. uygulanmaz | A-01 |
| S-2 | Yazma sonrası exception | Tümü geri alınır | H-02 |
| S-3 | Commit sonrası exception | Veri commit'lidir | §22 (4) |
| S-4 | Ağ çağrısında beklenir | Yasak | Y-02 |
| S-5 | Aynı transaction 2 connection | Atomiklik bozulur | Y-05 |
| S-6 | Loop'da her adımda commit | Performans bozuk | §11 |
| S-7 | Deadlock | Motor müdahale eder | U-2 |
| S-8 | `lastInsertId()` yanlış connection | Yanlış ID | Y-06 |
| S-9 | Transaction içinde DDL | Implicit commit riski | Y-09 |
| S-10 | Persistent bağlantıda açık kapanış | Sonraki isteğe bulaşabilir | U-3 |
| S-11 | Rapor sorgusu transaction içinde | Uzun kilit | Y-08 |
| S-12 | Chunk'lı batch, chunk 5 başarısız | 1-4 commit'li, 5+ geri | §11 |

## §24 İşlem Tipi × İzolasyon Etkisi

| İşlem Tipi | Transaction Zorunlu mu? | Neden |
|---|---|---|
| Tek `INSERT` | Hayır (tek statement atomiktir) | Tek statement kendi transaction'ındadır |
| İki tabloya bağlı yazma | **Evet** | FK tutarlılığı |
| `UPDATE` + `lastInsertId()` okuma | **Evet** | Y-06 |
| `SELECT` (salt okuma) | Hayır | Kilit gerekmez |
| `SELECT` → `UPDATE` | **Evet** + kilitleme | §10 |
| Migration DDL | Başka sınıf (K007-data/migrations) | Y-09 |
| Cache invalidation | Transaction dışı | K007-data/cache |

## §25 PDO/Pitfall Tablosu

| # | Pitfall | Etki | Savunma |
|---|---|---|---|
| P-1 | `beginTransaction()` ikinci kez | Hata/yok sayılma | Y-10 |
| P-2 | Hata sonrası rollback yapılmamış | Açık transaction | §8 (H-01) |
| P-3 | Emulate prepares açık | Sinsi SQL kırılması | Kapalı — satır 25 |
| P-4 | Persistent connection | Transaction sızıntısı | U-3 |
| P-5 | Exception'ı yutma | Yarım atomiklik | H-02 |
| P-6 | Commit'i try içinde, rollback'i dışında | Çift rollback riski | §13 |
| P-7 | Charset DSN'de değilse mojibake | Karakter bozulması | DSN satır 15 |
| P-8 | `getPdo()` ile raw erişim | Sözleşme ihlali | §7 notu |

## §26 Batch İşlem Politikası

| Parametre | Karar | Durum |
|---|---|---|
| Chunk boyutu | Repo'da tanımlı değil | UNKNOWN (U-6) |
| Chunk başına transaction | Evet (§11) | Kural |
| Başarısız chunk davranışı | Dur + rapor | Kural |
| İlerleme kaydı | Bilinmiyor | UNKNOWN (U-6) |
| Yeniden deneme (retry) | Bilinmiyor | UNKNOWN (U-2) |

## §27 Ölçüm ve Gözlem (Observability)

| Metrik | Kaynak | Durum |
|---|---|---|
| Aktif transaction sayısı | DB sunucusu | repo'da toplanmıyor → UNKNOWN |
| Ortalama transaction süresi | Uygulama ölçümü | yok → UNKNOWN |
| Rollback sayısı | Uygulama sayacı | yok → UNKNOWN |
| Deadlock sayısı | DB sunucusu | yok → UNKNOWN |
| Bağlantı havuzu doluluğu | PDO/Persistent | yok → UNKNOWN |

## §28 Roller ve Sorumluluklar (RACI)

| Görev | Sorumlu | Onay |
|---|---|---|
| Transaction sözleşmesi değişikliği | Data Engineer | Backend Architect |
| Şema/InnoDB değişikliği | Data Engineer | Architect |
| Domain doğrulama kuralları | Backend Architect | — |
| Transaction testleri | QA Engineer | Data Engineer |
| ADR açma (savepoint/nested) | Architect | Vault Steward |

## §29 Sık Yapılan Hatalar (Anti-Pattern)

| # | Anti-Pattern | Doğrusu |
|---|---|---|
| AP-1 | `try` bloğunda begin, `catch`'te sessiz geç | `catch` → `rollBack()` + log |
| AP-2 | Transaction'ı controller'a yaymak | Zarfta repository/service dar tut (A-01) |
| AP-3 | Her okuma için transaction | Salt okumada gereksiz |
| AP-4 | Transaction içinde mail/queue publish | Transaction sonrası kuyruğa al |
| AP-5 | Büyük JSON blob'u transaction içinde işleme | Dışarıda hazırla, kısa tut |
| AP-6 | Testte gerçek commit | Testte rollback ile izole et |

## §30 Uyum Matrisi (Standard'a Aykırılık)

| Kural | Kaynak | Uyum |
|---|---|---|
| PDO prepared statement yalnız | `shared/AGENTS.md §4.1` | ✅ `EMULATE_PREPARES=false` (satır 25) |
| DatabaseManager dışında bağlantı yok | `shared/AGENTS.md §4.1` | ✅ tek `new \PDO` (satır 22) |
| BCNF / no ORM / no SELECT * | `.ai/AGENTS.md §16` | ✅ sözleşmede ORM yok |
| Katman ihlali → revert | `.ai/AGENTS.md §18.6` | İlgili (transaction sarmalayıcı `shared/src/` içinde) |
| Secret dosyaya yazılmaz | `.ai/AGENTS.md` Zero-Hallucination | ✅ DSN parametreleri config'ten |

## §31 Terim Karşılıkları (TR ↔ EN ↔ Kod)

| Türkçe | English | Kod Karşılığı |
|---|---|---|
| Başlat | Begin | `beginTransaction()` |
| Onayla | Commit | `commit()` |
| Geri al | Rollback | `rollBack()` |
| Son kimlik | Last insert id | `lastInsertId()` |
| Sorgu çalıştır | Execute | `execute()` |
| Yaz | Write | `write()` |
| Kalıcı bağlantı | Persistent connection | `ATTR_PERSISTENT` |
| Hazırlanmış ifade | Prepared statement | `ATTR_EMULATE_PREPARES=false` |
| Hata kipleri | Error mode | `ATTR_ERRMODE` |
| Çoklu sürüm denetimi | MVCC | `ENGINE=InnoDB` |

## §32 İade Edilen Değerler (Return Semantics)

| Metot | `true` | `false` | Kanıt |
|---|---|---|---|
| `beginTransaction()` | Transaction başladı | Başlatılamadı (zaten açık olabilir) | `DatabaseManager.php:49-52` |
| `commit()` | Değişiklikler onaylandı | Commit başarısız | `DatabaseManager.php:54-57` |
| `rollBack()` | Geri alma tamamlandı | Rollback başarısız (fail-safe) | `DatabaseManager.php:59-62` |
| `write()` | Statement başarılı | Statement başarısız | `DatabaseManager.php:38-42` |
| `execute()` | `array` satırlar | (boş dizi) / exception | `DatabaseManager.php:30-36` |

## §33 İstisnai Durumlar

| # | Durum | Davranış | Etiket |
|---|---|---|---|
| E-1 | Bağlantı koptu (transaction ortasında) | PDO exception → rollback denenir | UNKNOWN (U-3) |
| E-2 | Sunucu timeout | Exception → rollback | ⚠️ VERIFICATION REQUIRED |
| E-3 | Deadlock hatası | Motor hata döner | ⚠️ VERIFICATION REQUIRED (U-2) |
| E-4 | Disk dolu (commit) | Commit false | fail-safe (H-03) |
| E-5 | Duplicate key | Exception → rollback | EVET (ERRMODE_EXCEPTION) |
| E-6 | FK ihlali | Exception → rollback | EVET |
| E-7 | Charset uyumsuzluğu | Mojibake riski | DSN satır 15 savunması |

## §34 Test Gereksinimleri

| # | Test | Kriter |
|---|---|---|
| T-1 | Başarılı commit testi | Tüm satırlar görünür |
| T-2 | Orta başarısızlık testi | Hiçbir satır görünmez |
| T-3 | Exception testi | Rollback çağrılır |
| T-4 | Çift begin testi | Hata/yok sayılma (Y-10) |
| T-5 | Eşzamanlı yazma testi | İzolasyon davranışı (U-1) |
| T-6 | Rollback sonrası bağlantı sağlığı | Yeni transaction açılabilir |

## §35 Karar Ağacı (Decision Tree)

```text
Birden fazla yazma var mı?
  ├─ HAYIR  → tek statement; transaction gerekmez (§24)
  └─ EVET   → İlişkili tablolar mı?
        ├─ EVET  → BEGIN (§13)
        │        → hepsi başarılı mı?
        │             ├─ EVET   → COMMIT
        │             └─ HAYIR  → ROLLBACK + log
        └─ HAYIR → yine de atomiklik gerekiyorsa BEGIN
Kritik oku-yaz (bakiye) mı?
  └─ EVET → §10 kilitleme + transaction
Ağ/dosya/IO işlemi var mı?
  └─ EVET → transaction dışına taşı (A-04/A-05)
```

## §36 Dış Bağımlılıklar ve Sınırlar (Boundaries)

| Dış Kaynak | İlişki | Kural |
|---|---|---|
| `K007-data/schema` | Tablo/şema atomikliğin taşıyıcısı | Şema değişikliği ADR gerektirir |
| `K007-data/repositories` | Transaction sarmalayıcısının çağıranı | Repository transaction **açmaz**, sarmalar |
| `K007-data/indexing` | Kilit genişliği indeks seçimiyle belirlenir | §10 |
| `K007-data/migrations` | DDL implicit commit | Y-09 |
| `K007-data/cache` | Cache invalidation transaction **sonrası** | AP-4 |
| `K008-domain` | Domain kuralı doğrulaması transaction öncesi | §20 |
| `K012-services` | İş akışı orkestrasyonu | Kapsam dışı (§1) |
| PDO sürücüsü | Altta yatan kütüphane | Sürüm kanıtı: composer — ⚠️ VERIFICATION REQUIRED |

| Sınır | Durum |
|---|---|
| Yazılım transaction'ı (XA/2PC) | Kullanılmıyor — repo'da kanıt **yok** |
| Distributed transaction | Kapsam dışı |
| Savepoint | UNKNOWN (U-4) |
| Read-only transaction ayarı | Repo'da **görülmedi** |

## §37 Ek Kanıt Satırları (Ek Okuma)

| # | Dosya : Satır | Ne gösteriyor |
|---|---|---|
| 13 | `shared/src/Database/DatabaseManager.php:12-20` | DSN kurulumu (charset dahil) |
| 14 | `shared/src/Database/DatabaseManager.php:22-27` | PDO option bloğu |
| 15 | `shared/src/Database/DatabaseManager.php:30-36` | `execute` gövdesi (prepare+execute+fetchAll) |
| 16 | `shared/src/Database/DatabaseManager.php:38-42` | `write` gövdesi |
| 17 | `shared/src/Database/DatabaseManager.php:44-47` | `lastInsertId` gövdesi |
| 18 | `shared/src/Contracts/Database/IDatabaseManager.php:1-13` | Arayüz tamamı (13 satır) |
| 19 | `.ai/.sql/mysql/coremusic_ai.sql:39` | `ENGINE=InnoDB DEFAULT CHARSET=utf8mb4` |

## §38 Sürüm ve Otorite

| Alan | Değer |
|---|---|
| Versiyon | 1.0.0 (ilk yazım) |
| Tarih | 2026-10-13 |
| Otorite | Bayram Ali / Vault Steward |
| Mod | Red Team · Human Mode · Truth Mode |
| Durum | draft (U-1..U-7 açık) |
| Erişim | `.ai/architecture/K007-data/transactions/transactions-anayasa.md` |

## §39 Ek — İyileştirme Kaydı (2026-10-17, append-only)

§1-§38 silinmemiştir; bu bölüm 2026-10-17 disk ölçümüyle eklenmiştir.

### §39.1 Ölçüm sonuçları (repo yüzeyi)

| Ölçüm | Sonuç | Kanıt |
|---|---|---|
| `beginTransaction` tanımı | 1 | `DatabaseManager.php:49-52` |
| `commit` tanımı | 1 | `:54-57` |
| `rollBack` tanımı | 1 | `:59-62` |
| Repository'de transaction kullanımı | **0** | `ApiKeyRepository.php` (317 satır) — §36 |
| `SELECT ... FOR UPDATE` | **0** | tarama |
| `FOR SHARE` | **0** | tarama |
| `SET TRANSACTION ISOLATION` | **0** | tarama |
| `SHOW ENGINE INNODB STATUS` | **0** | tarama |
| `performance_schema.data_lock*` sorgusu | **0** | tarama |
| Deadlock retry döngüsü | **0** | tarama |

### §39.2 Yeni tespit — `ATTR_PERSISTENT` + transaction etkileşimi

`DatabaseManager.php:26` → `\PDO::ATTR_PERSISTENT => true`.

| Risk | Mekanizma | Savunma |
|---|---|---|
| Açık transaction havuza döner | `commit`/`rollBack` çağrılmadan bağlantı bırakılırsa | `finally` zorunluluğu |
| Sonraki istek beklenmedik durumla karşılaşır | havuz aynı fiziksel bağlantıyı verir | istek başına `inTransaction()` kontrolü |
| `PDO::inTransaction()` | mevcut API — repo'da **çağrılmıyor** | `⚠️` |

### §39.3 Yeni tespit — deadlock retry politikası (tasarım boşluğu)

| Seviye | Davranış | CoreMusic |
|---|---|---|
| MySQL | deadlock'da bir transaction'ı **seçip öldürür** (`1213`) | — |
| PDO | exception `ERRMODE_EXCEPTION` ile yükselir | ✓ (`:23`) |
| Repository | yakalamaz, yükseltir | ✓ (bilinçli) |
| Service/Controller | `UNKNOWN` | `⚠️` |
| Retry | **yok** | ❌ |

**Önerilen politika (kod yazılmadan):** en fazla 3 deneme, 10-50 ms
rastgele jitter, yalnız `40001`/`1213` için; `1205` için retry **yapılmaz**.

### §39.4 Yeni tespit — salt okuma transaction'ı

| Durum | Sonuç |
|---|---|
| `execute()` salt okumada transaction | repo'da yok ✓ (AP-3 uygun) |
| Amaç olsaydı | `START TRANSACTION READ ONLY` + `SET TRANSACTION READ ONLY` |
| Avantaj | undo kullanmaz, replica uyumu |

### §39.5 İyileştirme kuyruğu (öncelik sıralı)

| # | İyileştirme | Öncelik | Sahip |
|---|---|---|---|
| 1 | `finally` + `inTransaction()` guard | YÜKSEK | transactions |
| 2 | Deadlock retry yardımcı sınıfı | YÜKSEK | transactions |
| 3 | İzolasyon seviyesini açıkça ayarla | ORTA | transactions |
| 4 | `data_lock_waits` izleme sorgusu | ORTA | observability |
| 5 | `FOR UPDATE` rehberi | DÜŞÜK | transactions |
| 6 | Savepoint kararı (U-4) | DÜŞÜK | ADR |

---

# §40 Çapraz-DB Transaction Analizi (32 FK kanıtı türevli)

> Kanıt: `.ai/.sql/mysql/` dump'larında **110 FK, bunların 32'si çapraz-DB**
> (şema-kapsam.md §FK). Çapraz-DB FK, MySQL'de InnoDB'de yalnız
> `foreign_key_checks=0` ile mümkün; distributed transaction **farklı konudur**.

## §40.1 Çapraz-DB FK ↔ transaction etkileşimi

| Senaryo | Tek DB | Çapraz-DB (coremusic_*) |
|---|---|---|
| `BEGIN ... COMMIT` | atomik ✓ | **atomik ✓** (InnoDB tek undo log havuzu — aynı sunucu) |
| `ROLLBACK` | tam geri alma ✓ | tam geri alma ✓ (aynı sunucu) |
| Deadlock | `1213` | `1213` (fark yok) |
| FK child→parent sırası | FK sırası korunur | FK kontrolü `foreign_key_checks=0` ile **atlanabilir** |
| Farklı sunucu (XA) | — | **BU REPO'DA YOK** — tek MySQL sunucu, XA gerekmez |

**Kritik bilgi:** 32 çapraz-DB FK'nın varlığı, verilerin **aynı MySQL instance'ında**
olduğunu ima eder; dump dosyaları tek `mysqldump` oturumundan geldiği için bu
`⚠️ VERIFICATION REQUIRED` (prod topolojisi bilinmiyor). Ama **farklı sunucularda**
olsaydı bu FK'lar hiç kurulamazdı → tek-instance varsayımı **güçlü** ama kanıt
dolaylı.

## §40.2 `foreign_key_checks=0` risk matrisi

| Risk | Açıklama | Önlem |
|---|---|---|
| Yetim satır | child, olmayan parent'a referans edebilir | migrate sonrası `CHECK TABLE` / orphan sorgu |
| Çevrim | parent↔child döngüsü | BCNF + dump sırası |
| İndeks atlaması | FK kontrolü kapalıyken optimizer davranışı değişebilir | migrate sonrası `ANALYZE TABLE` |
| Restore hatası | `SET FOREIGN_KEY_CHECKS=0` restore'da **zorunlu** | backup planına yazıldı |

## §40.3 Transaction boyutu sınırları (InnoDB)

| Parametre | Varsayılan | CoreMusic durumu |
|---|---|---|
| `innodb_log_file_size` | MySQL 8+ varsayılanı sürüm bağımlı → `⚠️ VERIFICATION REQUIRED` | sunucu ayarı — repo'da yok |
| `max_allowed_packet` | varsayılan sürüm bağımlı | dump'larda büyük `json` kolonlar (113 adet) → **packet boyutu riski** |
| Batch boyutu | — | migration'lar (3 PHP dosyası) içinde `UNKNOWN` |

## §40.4 Retry matrisi (§39.3 üzerine genişletme)

| Hata kodu | MySQL kaynağı | Retry? | Jitter |
|---|---|---|---|
| `40001` (SRDEADLOCK) | deadlock victim | ✓ evet | 10-50ms rastgele |
| `1213` (ER_LOCK_DEADLOCK) | deadlock victim | ✓ evet | 10-50ms rastgele |
| `1205` (ER_LOCK_WAIT_TIMEOUT) | lock timeout | **hayır** | — (lock kaynaklı, tekrarlanır) |
| `1040` (ER_CON_COUNT_ERROR) | bağlantı taşması | ✓ evet ama azami 2 | 100ms+ |
| `2006` / `2013` | bağlantısı koptu | **transaction durumu belirsiz** → uygulama seviyesinde reconcile gerekir |

## §40.5 Uygulanmamış ama tasarlanmış: `data_lock_waits` izleme

```sql
-- MySQL 8/9 Performance Schema tabanlı (sorgu yapısı MySQL 8.0+ varsayımı —
-- sürüm-specific sütunlar ⚠️ VERIFICATION REQUIRED, sunucu sürümü bilinmiyor)
SELECT r.trx_id              AS waiting_trx_id,
       r.trx_mysql_thread_id AS waiting_thread,
       r.trx_query          AS waiting_query,
       b.trx_id              AS blocking_trx_id,
       b.trx_mysql_thread_id AS blocking_thread,
       b.trx_query          AS blocking_query
FROM performance_schema.data_lock_waits w
JOIN information_schema.innodb_trx b ON b.trx_id = w.requesting_trx_id
JOIN information_schema.innodb_trx r ON r.trx_id = w.blocking_trx_id;
```

> `performance_schema.data_lock_waits` adı MySQL 8.0+ içindir; MySQL 5.7'de
> `innodb_lock_waits` idi. Sorgu **repoya yazılmadı** — bu vault'ta öneri
> olarak duruyor (iyileştirme kuyruğu madde 4).

## §40.6 İyileştirme kuyruğu güncellemesi

| # | İyileştirme | Öncelik | § |
|---|---|---|---|
| 7 | Migrate sonrası orphan satır taraması (orphan FK) | YÜKSEK | §40.2 |
| 8 | `2006`/`2013` bağlantı kopması politikası | YÜKSEK | §40.4 |
| 9 | `ANALYZE TABLE` sonrası doğrulama | ORTA | §40.2 |
| 10 | Büyük `json` kolonlar için `max_allowed_packet` denetimi | ORTA | §40.3 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
