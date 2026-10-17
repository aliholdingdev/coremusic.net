---
node_id: "K007-data/migrations-web-arastirma"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data/migrations"
children: []
depends_on:
  - "K007-data/data-web-arastirma"
evidence:
  - "4 URL — dev.mysql.com online DDL (2026-10-15 yeni tarama)"
  - "3 URL — Alembic/Flyway/Liquibase (2026-10-12 havuz §5)"
validation: "2026-10-15 — yeni MySQL taraması + mevcut framework havuzu birleştirildi"
open_questions:
  - "Migration çalıştırma aracı (CLI) → UNKNOWN"
group: kanit
---

# migrations — Web Araştırması (K007)

## §1 Yeni tarama — MySQL 8.4 DDL (2026-10-15)

| # | URL | Ne verdi | Durum |
|---|-----|----------|-------|
| WM-1 | dev.mysql.com/doc/refman/8.4/en/innodb-online-ddl-operations.html | ekleme/silme/yeniden adlandırma: INPLACE + concurrent DML | HTTP 200 |
| WM-2 | …/create-index.html | CREATE INDEX = ALTER TABLE eşlemesi · ALGORITHM/LOCK | HTTP 200 |
| WM-3 | …/optimizing-innodb-ddl-operations.html | önce yükle sonra indeksle; PK planı önceden ver | HTTP 200 |
| WM-4 | …/alter-table.html | ALTER TABLE genel çerçevesi · LOCK=NONE seçenekleri | HTTP 200 |

## §2 Mevcut havuz (data-web-arastirma.md §5 · 2026-10-12)

| # | URL | Ne verdi | Tür |
|---|-----|----------|-----|
| WM-5 | alembic.sqlalchemy.org | Python migration (referans) | T1 |
| WM-6 | flyway.org/quickstart | Java migration · versioned | T1 |
| WM-7 | docs.liquibase.com | changelog modeli | T1 |

**K007 karşılığı:** CoreMusic'te migration PHP dosyalarıdır (repo gerçeği) — WM-5..7
yalnız kalıp referansıdır; WM-1..4 üretime doğrudan uygulanır (veritabanı düzeyinde).

## §3 Derinleştirme taraması — implicit commit & CHECK constraints (2026-10-17)

| # | URL | HTTP | Ne verdi |
|---|-----|------|----------|
| WM-8 | dev.mysql.com/doc/refman/8.4/en/implicit-commit.html | 200 | §15.3.3 — DDL implicit commit listesi · TEMPORARY TABLE istisnası · CREATE TABLE ... SELECT önce+sonra commit |
| WM-9 | dev.mysql.com/doc/refman/8.4/en/create-table-check-constraints.html | 200 | §15.1.20.6 — CHECK syntax · NOT ENFORCED · schema-unique namespace · deterministik fonksiyon kısıtı · CHECK_CONSTRAINTS I_S tablosu |
| WM-10 | dev.mysql.com/doc/refman/8.4/en/innodb-information-schema.html | 200 | §17.15 — InnoDB INFORMATION_SCHEMA tablo ailesi (transaction/locking §17.15.2 · buffer pool · metrics · compression) |
| WM-11 | dev.mysql.com/doc/refman/8.4/en/identifier-case-sensitivity.html | 200 | §11.2.3 — lower_case_table_names 0/1/2 · Windows default 1 · initialize sonrası değiştirilemez · InnoDB lowercase istisnası |
| WM-12 | dev.mysql.com/doc/refman/9.7/en/implicit-commit.html | 200 (redirect; istenen 9.4 sürümü refman'da yok) | §15.3.3 — 8.4 ile aynı liste + XA ACTIVE kısıtı + "transactions cannot be nested" + BEGIN…END istisnası |

### §3.1 Erişim başarısızlıkları (dürüst kayıt — 2026-10-17)

| URL | HTTP | Not |
|-----|------|-----|
| dev.mysql.com/doc/refman/8.4/en/check-constraint.html | **404** | Var olmayan sayfa adı — gerçek sayfa WM-9 (`create-table-check-constraints.html`) |
| dev.mysql.com/doc/refman/8.4/en/constraint-checking.html | **404** | `schema-web-arastirma.md` §13 adayıydı; gerçekleşmedi, elendi |

### §3.2 Implicit commit → migration kuralı (WM-8, WM-12 kanıtı)

Kaynak §15.3.3 (8.4 ve 9.7 aynı içerik) doğrulamasıyla `transactions-kapsam.md` §AE
bulgusu resmi referansla sabitlenmiştir:

1. **DDL = implicit commit.** `ALTER TABLE`, `CREATE TABLE`, `DROP TABLE`, `CREATE INDEX`,
   `DROP INDEX`, `RENAME TABLE`, `TRUNCATE TABLE` ve benzeri tüm DDL listesi, çalıştırılmadan
   önce aktif transaction'ı **COMMIT gibi bitirir**; çoğu durumda sonrasında da bir implicit
   commit tetikler (her statement kendi özel transaction'ında işlenir niyetiyle).
   → **Migration dosyası içindeki DDL bloğu ASLA transaction rollback ile geri alınamaz.**
   Forward-only migration kuralı (ADR-007 türevi) bu fizik gereğin sonucudur.
2. **TEMPORARY TABLE istisnası — atomiklik tuzağı.** `CREATE TEMPORARY TABLE` ve
   `DROP TEMPORARY TABLE` implicit commit TETİKLEMEZ; ancak rollback de EDİLEMEZ —
   transaction geri alınsa bile tablo varlığını sürdürür. Dokümantasyonun kendi ifadesi:
   "the use of such statements causes transactional atomicity to be violated".
   → Migration'da geçici tablo (staging) kullanımı, DDL'ye eşdeğer geri döndürülemezlik taşır.
   (`ALTER TABLE` / `CREATE INDEX` gibi diğer işlemler TEMPORARY tablolarda DA commit tetikler.)
3. **CREATE TABLE InnoDB'de tek transaction** olarak işlenir — kullanıcının ROLLBACK'i bu
   statement'ı geri almaz. `CREATE TABLE ... SELECT` non-temporary tablolarda **önce ve sonra**
   implicit commit tetikler.
4. **Transaction nesting yasağı.** `START TRANSACTION` (ve eşdeğerleri) mevcut transaction'ı
   implicit commit ile bitirir → PHP'de iç içe `beginTransaction()` çağrısı sessiz commit
   üretir; PDO repository şablonlarında bu desen yasaktır (REK-1).
5. **Transaction-control / locking kendisi de commit tetikler:** `BEGIN`, `LOCK TABLES`,
   `UNLOCK TABLES` (yalnız nontransactional lock alınmışsa), `SET autocommit=1` (değer 1
   değilse). `BEGIN…END` compound statement'inin BEGIN'i ise **etkilemez** (stored program
   başlangıcı — farklı sözdizimi, aynı sözcük).
6. **XA ACTIVE state'inde** implicit commit tetikleyen statement kullanılamaz (WM-12 9.7 notu).
7. **mysql DBMS tablosu dokunmaları** (`ALTER USER`, `GRANT`, `REVOKE`, `SET PASSWORD` vb.)
   ve **admin statement'lar** (`ANALYZE TABLE`, `CHECK TABLE`, `OPTIMIZE TABLE`, `REPAIR TABLE`,
   `FLUSH`, `RESET` — `RESET PERSIST` hariç) de implicit commit kapsamındadır.

**K007 uygulama kararı:** Migration planı her adımı "bağımsız commit edilebilir birim" olarak
yazar; DDL grubu + veri backfill grubu AYRI fazlardır; tek transaction varsayımı yalnız DML
(backfill UPDATE/INSERT) fazında geçerlidir ve o faz bile DDL içeremez.

### §3.3 CHECK constraints → soft-delete köprüsü (WM-9 kanıtı)

`transactions-kapsam.md` §EK AJ.2'de tespit edilen **soft-delete + UNIQUE çelişkisi** için
MySQL 8.4 resmi CHECK constraint çerçeve koşulları:

- Sözdizimi: `[CONSTRAINT [symbol]] CHECK (expr) [[NOT] ENFORCED]` — isim verilmezse MySQL
  `{tablo}_chk_{n}` üretir; constraint adı **64 karakter** ile sınırlı, case-sensitive.
- `expr` her satırda `TRUE` veya `UNKNOWN` (NULL) değerlendirmelidir; `FALSE` = ihlal.
- **`NOT ENFORCED`**: constraint tanımlanır ama uygulanmaz — mevcut dump'a constraint'i
  "boş" ekleyip sonraki fazda `ENFORCED` yapmak migrate edilebilir bir köprü desenidir
  (önce veri temizliği, sonra enforce).
- **Namespace gerçeği:** MySQL'de her constraint tipinin kendi namespace'i vardır (SQL
  standardının aksine tümü tek namespace değil) — CHECK adları **schema içinde** benzersiz
  olmalıdır; iki farklı tablo aynı CHECK adını paylaşamaz. `{tablo}_chk_{n}` otomatik
  isimlendirmesi bu yüzden tablo adıyla başlar.
- **Yasaklar:** `AUTO_INCREMENT` kolonu · başka tabloların kolonları · nondeterministic
  built-in fonksiyonlar (`NOW()`, `CURRENT_USER()`, `CONNECTION_ID()`) · stored/loadable
  function · subquery · sistem/user/lokal değişkenler.
- **FK etkileşim yasağı:** FK referans aksiyonu (`ON UPDATE`/`ON DELETE`) bulunan kolonlarda
  CHECK constraint yasaktır; CHECK'li kolonda FK aksiyonu da yasaktır (çift yönlü).
- Değerlendirme INSERT/UPDATE/REPLACE/LOAD DATA/LOAD XML'de yapılır; `IGNORE` varyantlarında
  satır atlanır + warning üretilir (strict mode davranışı SQL mode'a bağlıdır).
- Meta sorgu: `INFORMATION_SCHEMA.CHECK_CONSTRAINTS` tablosu tanımlı CHECK constraint'leri listeler.

**K007 uygulama kararı:** Soft-delete kombinasyon unique kuralı MySQL'de partial index
(desteklenmiyor) yerine **generated column + unique index** veya **CHECK + uygulama
tarafı doğrulama** ile çözülecektir — hangisinin seçildiği ADR ile tescillenecektir
(⚠️ VERIFICATION REQUIRED: dump'larda generated column kullanımı henüz envanterlenmedi).
`NOT ENFORCED` yolu yalnızca veri temizliği tamamlanmış tablolarda geçici köprü olarak
kullanılabilir.

### §3.4 InnoDB INFORMATION_SCHEMA → migration kanıt katmanı (WM-10 kanıtı)

§17.15 doğrulaması: InnoDB, metadata/statistik/telafi bilgisini `INFORMATION_SCHEMA`
içinde bir tablo ailesi olarak yayınlar — transaction/locking bilgisi (§17.15.2), buffer
pool (§17.15.5), metrics (§17.15.6), compression (§17.15.1), temporary table (§17.15.7),
tablespace (§17.15.8). Listeleme kanıtı dokümante edilmiş sorgu:
`SHOW TABLES FROM INFORMATION_SCHEMA LIKE 'INNODB%';`

**K007 uygulama kararı:** Migration öncesi/sonrası doğrulama betiği bu tablo ailesini
"kanıt katmanı" olarak kullanır (ör. migration sonrası tablo/lock durumu teyidi);
ancak bu taramada yalnız üst §17.15 sayfası alınmıştır — alt bölüm tablo adları
(§17.15.2 transaction/locking tablolarının kesin adları) **bu oturumda okunmadı**
(⚠️ VERIFICATION REQUIRED: tablo adları bir sonraki taramada §17.15.2'den alınacaktır).

### §3.5 Identifier case sensitivity → cross-platform migration (WM-11 kanıtı)

§11.2.3 doğrulaması — migration'ın Windows↔Linux taşınabilirliğini etkileyen kurallar:

1. **`lower_case_table_names` (lctn) yalnızca server initialize sırasında ayarlanabilir** —
   sonradan DEĞİŞTİRİLEMEZ. Değerleri: `0` = disk'te orijinal harf, karşılaştırma
   case-sensitive (Unix default) · `1` = tüm tablo adları lowercase saklanır, case-insensitive
   (Windows default) · `2` = orijinal yazılır, lookup'ta lowercase (macOS default; InnoDB
   tabloları yine lowercase saklar).
2. **lctn=0 case-insensitive dosya sisteminde MyISAM tablolarında index corruption riski**
   dokümante edilmiştir (farklı harfli aynı isim erişimi).
3. **InnoDB istisnası:** InnoDB tablo ve view isimleri her zaman lowercase saklanır —
   cross-platform veri transferi hedefleniyorsa dokümantasyonun önerisi **tüm platformlarda
   lctn=1** kullanımıdır.
4. **Kolon, index, partition, stored routine, event isimleri hiçbir platformda case-sensitive
   değildir**; tablo alias'ları ise Unix'te case-sensitive'dır (Windows/macOS'ta değildir).
5. **Duplicate isim kuralı:** cursor/condition/procedure/function/savepoint/parameter/plugin
   isimleri binary collation'da büyük harf eşitliğinde duplicate sayılır — kolon, constraint,
   tablo, trigger, user isimleri için bu kural GEÇERLİ DEĞİLDİR (ayrı namespace'ler).
6. `INFORMATION_SCHEMA` içinde string kolon aramaları dosya sistemi case-sensitivity'inden
   etkilenir (collation §12.8.7'ye referans).

**K007 uygulama kararı:** CoreMusic Windows ortamında geliştiriliyor (lctn=1 default
doğrulanabilir — ⚠️ VERIFICATION REQUIRED: canlı sunucu lctn değeri bu oturumda sorgulanmadı);
zaten tüm dump'larda `snake_case` lowercase tablo/kolon adları kullanılmaktadır (disk kanıtı:
`.ai/.sql/mysql/` 20 dump) — bu, WM-11'in lctn=1 önerisiyle uyumludur ve taşınabilirlik
korkusunu azaltır. Migration betikleri isimlerde asla mixed-case kullanmayacaktır.

### §3.6 Kaynak sayımı (bu dosya)

Toplam benzersiz gerçek kaynak: **12** (WM-1..WM-12) — WM-1..4 2026-10-15 taraması,
WM-5..7 havuz referansları, WM-8..12 bu derinleştirme (2026-10-17). 404 iki URL
(RN-404-1 `check-constraint.html`, RN-404-2 `constraint-checking.html`) kaynak
sayımına DAHİL EDİLMEZ — yalnız erişim günlüğü olarak §3.1'de taşınır.

---

**Authority:** Bayram Ali / Vault Steward