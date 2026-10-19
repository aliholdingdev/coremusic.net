---
node_id: "K007-data/transactions-kapsam"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data/transactions"
children: []
depends_on:
  - "K007-data/schema"
  - "K007-data/repositories"
evidence:
  - "shared/src/Contracts/Database/IDatabaseManager.php:8-14 — beginTransaction/commit/rollBack (2026-10-12 okundu)"
  - "master §6 satır 18 — InnoDB + MVCC + atomik UPDATE (2026-10-12)"
  - "dev.mysql.com/doc/refman/8.4/en/innodb-transaction-isolation-levels.html — HTTP 200 (2026-10-15)"
validation: "2026-10-15 — kapsam yazıldı; sözleşme satırı + MySQL resmi URL"
open_questions:
  - "Çok-DB transaction CoreMusic'te kullanılıyor mu → UNKNOWN (18 DB topolojisi)"
  - "Deadlock sayacı/izleme → repo'da YOK"
group: mimari
---

# transactions — Kapsam (K007)

## §1 Kapsam İÇİ

| # | Konu | Kanıt |
|---|------|-------|
| 1 | ACID / begin-commit-rollback sözleşmesi | IDatabaseManager.php:8-14 |
| 2 | İzolasyon seviyesi (REPEATABLE READ varsayılan) | MySQL 8.4 §17.7.2.1 (2026-10-15) |
| 3 | Deadlock (çift kilit) davranışı | MySQL 8.4 §13.7.1 |
| 4 | Tek DB atomik güncelleme (CHECK/UNIQUE/FK) | master §6 satır 18 |

## §2 Kapsam DIŞI

| Dışarıda | Nereye | Gerekçe |
|----------|--------|---------|
| Şema tasarımı | schema | kısıt orada tasarlanır |
| Çok DB senkronizasyonu | synchronization | transaction sınırı tek DB |
| DB kurulumu/innoDB yapılandırması | K17-devops / K000 | runtime yapılandırma |
| Lock timeout politikası değeri | → UNKNOWN | ölçüm yok |

## §3 Sınır cümlesi

transactions, "tek adımda hepsi ya hiç" sorusunun sahibidir — tek DB'yi korur;
DB'ler arası koordinasyon synchronization'ın konusudur (sınır açık, S-3/S-9).

---

# EK T — ACID ↔ InnoDB Gerçekleştirme Matrisi

| ACID | InnoDB mekanizması | CoreMusic'te durumu | Kanıt |
|---|---|---|---|
| **A** — Atomiklik | undo log; rollback tüm transaction'ın undo zincirini uygular | `IDatabaseManager::rollBack()` sözleşmede | `IDatabaseManager.php:8-14` |
| **C** — Tutarlılık | kısıtlar (PK/FK/UNIQUE) her INSERT/UPDATE'de zorlanır | 110 FK, 118 UNIQUE, 0 CHECK | `schema-kapsam` §A.2 |
| **I** — İzolasyon | MVCC + kilitler; 4 izolasyon seviyesi | **REPEATABLE READ** (MySQL varsayılan) — session ayarı repo'da **yok** | `.ai/.sql/CLAUDE.md §1`; repo kanıtı: `ATTR_*` seçenekleri arasında izolasyon ayarı **yok** (`DatabaseManager.php:22-27`) |
| **D** — Dayanıklılık | redo log (ib_logfile) + fsync; `innodb_flush_log_at_trx_commit` | yapılandırma repo'da **yok** → `UNKNOWN` (K17-devops) | — |

## T.1 Kanıt boşluğu — izolasyon seviyesi açıkça ayarlanmıyor

`DatabaseManager::__construct` 4 seçenek kurar:
`ERRMODE_EXCEPTION` · `DEFAULT_FETCH_MODE=FETCH_ASSOC` · `EMULATE_PREPARES=false` ·
`PERSISTENT=true`. Bunların **hiçbiri** izolasyon seviyesi değil.

| Seçenek | Sonuç |
|---|---|
| Sunucu varsayılanı (REPEATABLE READ) | fiilen geçerli |
| `SET TRANSACTION ISOLATION LEVEL ...` | repo'da **görülmedi** → `⚠️ VERIFICATION REQUIRED` |
| `SELECT ... FOR UPDATE` / `FOR SHARE` | repo'da **görülmedi** → `⚠️ VERIFICATION REQUIRED` |

---

# EK U — MVCC (Multi-Version Concurrency Control) Derinlemesine

## U.1 Çekirdek kavramlar

| Kavram | Açıklama | CoreMusic etkisi |
|---|---|---|
| Read view | Her SELECT'te görünüm anı oluşturulur | REPEATABLE READ'da transaction'ın **ilk SELECT'i** belirleyici |
| Undo log | Eski sürüm satırları; rollback + okuma için | Büyük UPDATE'lerde undo büyümesi |
| Snapshot | Okuyucu, yazıcının commit'ini **beklemez** | okuma-yazma çakışması azalır |
| GHOST/temizlik | Purge thread'i artık sürüm siler | uzun transaction'lar purge'u **durdurur** → disk şişmesi |

## U.2 CoreMusic senaryosu: API key doğrulama

`ApiKeyRepository::inspect()` (§R.2) SELECT + UPDATE içerir:

| Adım | İşlem | MVCC sonucu |
|---|---|---|
| SELECT `WHERE key_prefix = :prefix` | salt okuma | read view — snapshot |
| PHP'de `hash_equals` | — | — |
| UPDATE `WHERE key_hash = :key_hash` | yazma | satır kilidi (exclusive) |
| COMMIT | — | redo + undo zinciri kapanır |

**Riskli senaryo (eşzamanlı):** aynı anda iki `inspect()` — ikisi de aynı
satırı okur, sonra ikisi de UPDATE dener. İkinci UPDATE, ilkinin commit'ini
**row lock** ile bekler (lock wait). `innodb_lock_wait_timeout` varsayılanı
50 saniyedir (MySQL varsayımı → `⚠️ VERIFICATION REQUIRED`: prod değeri bilinmiyor).

## U.3 Uzun transaction riski (repository çağrısı zinciri)

| Tehlike | Belirti | Savunma |
|---|---|---|
| Uzun SELECT transaction | undo şişer, purge durur | transaction **yalnızca yazma** için; salt okumada BEGIN yok |
| HTTP request içinde transaction | istek timeout = açık transaction | transaction service katmanında dar tutulur (`transactions-anayasa` A-01) |
| `PERSISTENT=true` + transaction unutulan `commit` | bağlantı havuzda transaction açık döner | `PERSISTENT` (canıt: `DatabaseManager.php:26`) + havuz reuse → **kritik** `⚠️ VERIFICATION REQUIRED` |

> `ATTR_PERSISTENT = true` ile transaction'ın unutulması, sonraki isteğin
> beklenmedik davranışla karşılaşmasına yol açabilir (aynı fiziksel bağlantı
> yeniden kullanılır). `rollBack()` çağrısının `finally` içinde zorunlu
> kılınması `transactions-anayasa` §13/§29 AP-1'de; **kodda zorunlu
> kılınmadığı** repo envanterinde doğrulandı (`ApiKeyRepository` transaction
> açmıyor — iyi).

---

# EK V — İzolasyon Seviyeleri ve Anomali Tablosu

| Seviye | Dirty read | Non-repeatable read | Phantom | InnoDB uygulaması |
|---|---|---|---|---|
| READ UNCOMMITTED | var | var | var | pratikte yok (InnoDB bunu READ COMMITTED'e düşürür) |
| READ COMMITTED | yok | var | var | her statement'ta yeni read view |
| **REPEATABLE READ** (varsayılan) | yok | yok | **kısmen korunur** (next-key lock ile) | CoreMusic fiili seviyesi |
| SERIALIZABLE | yok | yok | yok | SELECT'leri `FOR SHARE`'a çevirir — performans bedeli |

## V.1 "REPEATABLE READ = phantom yok" efsanesi

InnoDB'de RR, **düz SELECT**'lerde phantom'a karşı **next-key lock** ile
korumaz (sadece `SELECT ... FOR UPDATE`/`FOR SHARE` kilitler); normal
okumalarda MVCC snapshot phantom'ı engellemez. Uygulama sonucu:RR'de
"aynı sorgu iki kez aynı sonucu döner" **garantisi SELECT içinde**
geçerlidir; iki ayrı statement arasında **garanti değildir** (bkz. §U.2).

| İddia | Durum |
|---|---|
| "RR'da phantom yok" (yaygın söylem) | ❌ kesin değil → `⚠️ VERIFICATION REQUIRED` (MySQL refman §17.7.2.1, 2026-10-15 HTTP 200) |

---

# EK W — Kilit Kataloğu (InnoDB)

| Kilit | Ne kilitler | Tipik tetikleyici | CoreMusic'te |
|---|---|---|---|
| Record lock | tek satır indeks kaydı | `WHERE id = X` | UPDATE/DELETE |
| Gap lock | indeks aralığı (boşluk) | RR'de range okuma | `WHERE created_at BETWEEN` |
| Next-key | record + gap | range + lock | `FOR UPDATE` |
| Insert intention | insert öncesi gap işareti | INSERT | `create()` |
| Shared (S) | okuma | `FOR SHARE` | — |
| Exclusive (X) | yazma | `FOR UPDATE` / DML | UPDATE |
| Auto-inc lock | auto-increment | `AUTO_INCREMENT` | **PK `binary(16)` → kullanılmaz** |
| Metadata (MDL) | şema değişikliği | DDL | migration anında |

## W.1 Kilit genişliği ve indeks seçimi

Kilit, **kullanılan indeks üzerinden** alınır. Kullanılmayan indeks →
tablo taraması → **tüm satırlar kilitlenir** (RR'de).

| Durum | Sonuç |
|---|---|
| `UPDATE ... WHERE user_id = :u` + `idx_queue_user_position(user_id, position)` | kilit yalnız o kullanıcının satırlarında |
| Aynı sorgu + indeks **yoksa** | tablo taraması → kilit patlaması |

**Bağlantı:** `../indexing/` alt alanı kilit genişliğini **doğrudan** belirler
(bkz. `indexing-kapsam.md`).

---

# EK X — Deadlock (1213) ve Lock Wait Timeout (1205)

| | Deadlock | Lock wait timeout |
|---|---|---|
| MySQL kodu | `1213` | `1205` |
| SQLSTATE | `40001` | `HY000` |
| Neden | iki transaction karşılıklı kilit bekler | tek yönlü bekleme uzar |
| MySQL davranışı | **birini seçer** ve öldürür | bekleyeni hata ile bırakır |
| Uygulama davranışı | **retry gerekir** | retry ya da farklı strateji |
| CoreMusic durumu | **retry mekanizması YOK** | timeout değeri bilinmiyor |

## X.1 Deadlock önleme sıralaması (CoreMusic için)

| # | Kural | Uygulanabilirlik |
|---|---|---|
| 1 | Aynı tablo-kolon sırasıyla yaz | repository tek INSERT (`:102-118`) — uygun |
| 2 | Transaction'ı kısa tut | HTTP + transaction yok — uygun |
| 3 | Uzun işlemi batch'e böl | `IN (...)` batch (`repositories-kapsam` §T.2) |
| 4 | İndeksle kilit daralt | `../indexing/` |
| 5 | Retry (jitter ile) | **EKSIK** → `⚠️` açık |

## X.2 Ölçüm kaynakları (repo'da mevcut değil)

| Araç | Durum |
|---|---|
| `SHOW ENGINE INNODB STATUS` | repo'da çağrı **YOK** |
| `performance_schema.data_lock_waits` | `schema-web-arastirma` #4 (HTTP 200, 2026-10-12) — uygulama kodunda **YOK** |
| `performance_schema.data_locks` | uygulama kodunda **YOK** |
| `sys.innodb_lock_waits` | **YOK** |

> Ölçüm altyapısı olmayan deadlock yönetimi **sezgiseldir** → K007-observability
> (K007 dışı) adayı.

---

# EK Y — DDL ve Implicit Commit

| DDL | Implicit commit tetikler mi? | Not |
|---|---|---|
| `CREATE TABLE` | ✓ | açık transaction varsa önce commit edilir |
| `ALTER TABLE` | ✓ | — |
| `DROP TABLE` | ✓ | — |
| `TRUNCATE TABLE` | ✓ (DDL gibi) | **hard delete** — proje kuralı 3 ile çelişir |
| `CREATE INDEX` | ✓ | — |
| `SET FOREIGN_KEY_CHECKS` | ✗ | transaction dışı oturum ayarı |
| DML (`INSERT/UPDATE/DELETE`) | ✗ | normal transaction içinde |

## Y.1 CoreMusic için sonuç

| Durum | Sonuç |
|---|---|
| Migration PHP dosyaları DDL içerir | `shared/database/migrations/*.php` → implicit commit |
| Migration + transaction birlikte | **transaction koruması yok** — kısmi uygulama riski |
| Yarı-yapılmış migration | elle onarım; `migrations/` alt alanının konusu |

---

# EK Z — Canlı Kodda Transaction Kullanım Envanteri

| Dosya | `beginTransaction` | `commit` | `rollBack` | Yorum |
|---|---|---|---|---|
| `DatabaseManager.php:49-62` | ✓ tanım | ✓ | ✓ | tek implementasyon |
| `IDatabaseManager.php:8-14` | ✓ sözleşme | ✓ | ✓ | — |
| `ApiKeyRepository.php` (317 satır) | **YOK** | **YOK** | **YOK** | tek repository transaction açmıyor |
| Diğer `shared/src/**` | `UNKNOWN` | `UNKNOWN` | `UNKNOWN` | tarama yapılmadı → `⚠️` |

**Sonuç:** K007'nin tek repository'sinde transaction **kullanılmıyor**; bunun
nedeni tek-write operasyonları (INSERT tek satır). İkinci repository
(ör. playlist + track_count güncelleme) geldiğinde transaction zorunlu olacak.

---

# EK AA — Transaction Kural Matrisi (checklist)

| # | Kural | Kaynak | Durum |
|---|---|---|---|
| 1 | Transaction yalnız repository/service katmanında açılır | `transactions-anayasa` A-01 | ✓ (repo'da yok) |
| 2 | `commit`/`rollBack` `finally` içinde | AP-1 | `⚠️` — kod kanıtı yok |
| 3 | Salt okumada BEGIN yok | §24 | ✓ (görülmedi) |
| 4 | Ağ/IO işlemi transaction dışında | AP-4/AP-5 | ✓ (repo'da IO yok) |
| 5 | Deadlock retry | §X | ❌ EKSIK |
| 6 | İzolasyon seviyesi açıkça ayarlı mı | §E.1 | ❌ EKSIK |
| 7 | `FOR UPDATE` kullanımı | §W | ❌ (repo'da YOK) |
| 8 | `PERSISTENT` bağlantıda transaction temizliği | §U.3 | `⚠️` |
| 9 | Savepoint | U-4 (anayasa) | `UNKNOWN` |
| 10 | XA/2PC | anayasa §36 | kanıt **yok** (kullanılmıyor) |

---

# EK AB — Kararlar (bu ek)

| # | Karar/tespit | Durum | Sorumlu |
|---|---|---|---|
| AB-1 | İzolasyon seviyesi sunucu varsayılanına bırakılmış | `⚠️` açık | transactions |
| AB-2 | Deadlock retry yok | `⚠️` açık | repositories + transactions |
| AB-3 | `SHOW ENGINE INNODB STATUS`/P_S izleme yok | `⚠️` açık | observability |
| AB-4 | `TRUNCATE` kullanımı envanteri | `UNKNOWN` | migrations |
| AB-5 | `PERSISTENT` + transaction etkileşimi | `⚠️` açık | transactions |
| AB-6 | `lastInsertId()` + `binary(16)` çelişkisi | `⚠️` açık | repositories |
| AB-7 | RR'da phantom efsanesi düzeltmesi | ✓ kayıt | transactions |

---

# EK AC — İzolasyon Seviyesi Karşılaştırmalı Analiz ve Öneri

> Bu ek, EK T.1'de tespit edilen "izolasyon seviyesi açıkça ayarlanmıyor"
> boşluğunu derinleştirir. MySQL 9 InnoDB varsayılanı **REPEATABLE READ**;
> sürüm-specific doğrulama için MySQL refmanı gereklidir (`⚠️ VERIFICATION
> REQUIRED` — sunucu sürümü bilinmiyor, yalnız dump'lardaki yapılar okundu).

## AC.1 Seviye × Çekirdek işlem matrisi

| İşlem | READ UNCOMMITTED | READ COMMITTED | REPEATABLE READ | SERIALIZABLE |
|---|---|---|---|---|
| API key doğrulama (salt okuma) | ✓ | ✓ | ✓ | ✓ (overkill) |
| Rate limiter increment | ✓ | ✓ | ✓ | ✓ |
| Soft delete (`is_deleted=1`) | dirty read riski | ✓ | ✓ | ✓ |
| UUIDv7 insert + parent-child | ✓ | ✓ | ✓ | ✓ |
| Çapraz-DB yazma (32 FK) | ⚠️ | ✓ | ✓ | ✓ |
| Rapor sorgusu (snapshot) | ✗ tutarsız | ✗ phantom riski | ✓ | ✓ |

## AC.2 Öneri: RC (READ COMMITTED) lehine geçiş gerekçeleri

| # | Gerekçe | Açıklama |
|---|---|---|
| 1 | Kısa transaction'lar | repository katmanı tek sorgu/çok küçük işlem yapıyor (§U.3) |
| 2 | Snapshot büyüklüğü | RR'de transaction sonuna kadar undo log korunur; RC'de statement sonunda bırakılır |
| 3 | Gap lock azalması | RR'de `WHERE` eşleşmeyen aralıklar kilitlenir; RC'de yok |
| 4 | Deadlock yüzeyi | gap lock deadlock'ların yaygın nedenidir; RC'de daha az |

| # | Aleyhine gerekçe | Açıklama |
|---|---|---|
| 1 | Non-repeatable read | aynı transaction'da 2. okuma farklı sonuç verebilir |
| 2 | Uygulama bağımlılığı | `SELECT ... FOR UPDATE` gereken yerlerde davranış değişir (repo'da `FOR UPDATE` YOK — §W) |
| 3 | Sürüm davranışı | MySQL sürümüne göre ince farklar `⚠️ VERIFICATION REQUIRED` |

**Karar durumu:** bu vault'ta **öneridir**; uygulama kararı ADR gerektirir →
`⚠️ VERIFICATION REQUIRED`.

## AC.3 İzolasyon seviyesi ayarlama yolları (MySQL)

| Yer | Sözdizimi | Kapsam |
|---|---|---|
| Global | `SET GLOBAL TRANSACTION ISOLATION LEVEL READ COMMITTED;` | yeni bağlantılar |
| Oturum | `SET TRANSACTION ISOLATION LEVEL READ COMMITTED;` | mevcut oturum |
| Transaction | `SET TRANSACTION ISOLATION LEVEL ...` → `BEGIN` sonrası etkisiz | transaction öncesi |
| Bağlantıdsn | `SET SESSION TRANSACTION ISOLATION LEVEL ...` | mevcut oturum |

**PDO tarafı:** `DatabaseManager.php` izolasyon seviyesi ayarlamıyor (kanıt:
§T.1). Önerilen: bağlantı havuzu açılışında `SET SESSION` çağrısı.

---

# EK AD — Transaction Ömrü ve Bagaj Analizi

> Kanıt: `DatabaseManager.php` (68 satır, tam okundu 2026-10-15) +
> `ApiKeyRepository.php` (317 satır).

## AD.1 Transaction boyutu bileşenleri

```text
BEGIN
  ├── [1] SELECT  (okuma — MVCC snapshot)
  ├── [2] INSERT/UPDATE (undo log yazımı)
  ├── [3] commit veya rollback
  └── [4] (varsa) cache invalidation — repo'da YOK (§E.3 cache-kapsam)
```

| Bileşen | MySQL maliyeti | CoreMusic durumu |
|---|---|---|
| Undo log | transaction boyunca korunur | ✓ (kısa transaction) |
| Redo log (binlog) | commit'te flush | `innodb_flush_log_at_trx_commit` bilinmiyor → `⚠️` |
| Binary log | replica varsa | replica topolojisi `UNKNOWN` |
| Gap lock | RR'de `WHERE` okuma | RR varsayılan (§AC.1) |

## AD.2 `ATTR_PERSISTENT` + transaction etkileşimi (derin analiz)

| Sorun | Açıklama |
|---|---|
| Bağlantı yeniden kullanımı | persistent bağlantı, transaction state'i istekler arası **taşıyabilir** |
| Uncommitted transaction | bir istekte `BEGIN` + exception → `ROLLBACK` **yapılmazsa** sonraki istek transaction içinde başlar |
| `inTransaction()` guard | kodda var mı → **bilinmiyor** (§U.3 `⚠️`) |
| FPM worker havuzu | persistent bağlantı worker'a yapışır; 10 worker × 1 persistent = 10 uzun-lived bağlantı |

**Önerilen davranış (kod yazılmadan):**

```php
// ÖNERİ — repoda YOK
if ($pdo->inTransaction()) {
    $pdo->rollBack(); // önceki istekten artakalan
}
$pdo->beginTransaction();
```

## AD.3 Bağlantı temizliği matrisi

| Olay | Beklenen | Mevcut kod |
|---|---|---|
| Başarılı commit | transaction kapanır | ✓ |
| Exception | `ROLLBACK` | `⚠️` (guard yok — §U.3) |
| `fatal error` (OOM) | MySQL transaction'ı zaman aşımına uğratır | `innodb_lock_wait_timeout` değer bilinmiyor |
| FPM worker ölümü | bağlantı kapanır → transaction auto-rollback | ✓ (MySQL tarafı) |
| Request sonu | açık transaction kalmamalı | `⚠️` (middleware okunmadı) |

---

# EK AE — Migration ↔ Transaction Etkileşimi

> Kanıt: `shared/database/migrations/` (3 PHP dosyası envanteri).

## AE.1 Migration'lar transaction içinde mi?

| Migration aracı | DDL transaction desteği |
|---|---|
| Phinx | **DDL transaction desteklemez** (MySQL DDL implicit commit) — `⚠️ VERIFICATION REQUIRED` (Phinx composer'da YOK, kural 9) |
| Özel PHP migration (repo'da 3 dosya) | `UNKNOWN` — içler okunmadı |
| Ham SQL migration | implicit commit (§Y) |

## AE.2 DDL ↔ implicit commit hatırlatması (EK Y üzerine)

| DDL işlemi | Implicit commit? | Sonuç |
|---|---|---|
| `CREATE TABLE` | ✓ | önceki transaction commit edilir |
| `ALTER TABLE` | ✓ | aynı |
| `DROP TABLE` | ✓ | aynı |
| `CREATE INDEX` | ✓ | aynı |
| `TRUNCATE TABLE` | ✓ (farklı semantik: `DELETE` değil) | geri alınamaz |

**Migration kuralı:** migration DDL içeriyorsa `BEGIN ... COMMIT` anlamsızdır;
her DDL kendi başına commit edilir. Migration rollback'ü **dosya tabanlı** olmalı
(ek migration ile), transaction rollback'i **olamaz**.

---

# EK AF — Transaction Ölçüm ve İzleme (repo'da YOK — öneri)

| Metrik | Sorgu kaynağı | Amaç |
|---|---|---|
| Aktif transaction sayısı | `information_schema.innodb_trx` | uzun transaction tespiti |
| Bekleyen lock | `performance_schema.data_lock_waits` | deadlock öncesi uyarı |
| Undo log boyutu | `innodb_metrics` | snapshot şişmesi |
| Deadlock sayısı | `SHOW ENGINE INNODB STATUS` | §X.2 |
| Commit süresi p99 | uygulama telemetri | performans |

**Öneri SQL (repoya yazılmadı — vault önerisi):**

```sql
-- Uzun transaction tespiti (>30sn)
SELECT trx_id, trx_state, trx_started,
       TIMESTAMPDIFF(SECOND, trx_started, NOW()) AS age_seconds,
       trx_query
FROM information_schema.innodb_trx
WHERE trx_started < NOW() - INTERVAL 30 SECOND;
```

> `information_schema.innodb_trx` MySQL 8.0+ için doğrulanmış yapıdır; MySQL 9'da
> devamı `⚠️ VERIFICATION REQUIRED` (sürüm bilinmiyor).

---

# EK AG — Kararlar (bu ek — genişletilmiş)

| # | Karar/tespit | Durum |
|---|---|---|
| AG-1 | RC'ye geçiş önerisi | `⚠️` ADR gerektirir |
| AG-2 | `SET SESSION` bağlantı havuzu açılışına eklenmeli | `⚠️` |
| AG-3 | Migration'lar transaction rollback'i **desteklemez** (DDL implicit commit) | ✓ kayıt |
| AG-4 | `inTransaction()` guard önerisi | `⚠️` (kodda yok) |
| AG-5 | Uzun transaction izleme sorgusu | ✓ öneri (vault) |
| AG-6 | `ATTR_PERSISTENT` ↔ transaction temizliği | `⚠️` (§AB-5 ile aynı) |
| AG-7 | `innodb_flush_log_at_trx_commit` değeri | `UNKNOWN` |
| AG-8 | Replica topolojisi (binlog gereksinimi) | `UNKNOWN` |

---

# EK AH — Deadlock Senaryoları (CoreMusic'e özgü) ve Önleme Rehberi

> Kaynak: EK X (1213/1205 genel), EK W (kilit kataloğu). Bu ek, CoreMusic'in
> **gerçek** repository desenleri üzerinden somut deadlock senaryoları üretir.
> Senaryolar **kod kanıtı** (ApiKeyRepository 317 satır, Repository base) +
> şema kanıtı (110 FK, 118 UNIQUE, 493 idx) türetilmiştir.

## AH.1 Senaryo 1 — İki Unique çakışması (en olası deadlock)

```text
Transaction A:  INSERT INTO api_keys (name, ...) -- name UNIQUE
Transaction B:  INSERT INTO api_keys (name, ...) -- aynı name
MySQL: A insert eder (index gap lock) → B insert'i bekler →
       A'nın başka bir işlemi B'nin lock'unu isterse → deadlock (1213)
```

| Önlem | Uygulanabilirlik |
|---|---|
| Aynı `name`'e insert eden iki transaction **önceden** önlenir | uygulama seviyesinde `SELECT ... FOR UPDATE` |
| `INSERT ... ON DUPLICATE KEY UPDATE` | deadlock yüzeyini azaltır (tek statement) |
| Sıralı insert | transaction içinde tablo erişim sırası sabitlenir |

## AH.2 Senaryo 2 — Unique → FK sırası (çapraz-DB riski)

```text
Transaction A:  child tabloda insert → parent FK lock bekler
Transaction B:  parent'ta update → child'ta lock bekler
→ döngü → 1213
```

Çapraz-DB FK (32 adet) durumunda lock sırası **veritabanı sınırını aşar** →
MySQL tek instance'ta global lock manager devreye girer. Çok-instance'ta bu
senaryo XA gerektirir (`UNKNOWN` — topoloji bilinmiyor).

## AH.3 Sıralama rehberi (transaction içinde)

| # | Kural | Gerekçe |
|---|---|---|
| 1 | Tabloya **alfabetik** veya sabit bir sırayla dokun | döngü kilitlenmesi önlenir |
| 2 | Önce parent, sonra child insert | FK lock sırası |
| 3 | `SELECT ... FOR UPDATE` her zaman **kısa** | uzun transaction MVCC şişmesi |
| 4 | Aynı transaction'da `SELECT` + `UPDATE` → `FOR UPDATE` ile | non-repeatable read (§V.1) |
| 5 | Deadlock olursa **retry** (yok, §AB-2) | — |

## AH.4 Retry yardımcı sınıfı (tasarım — kod YAZILMADI)

```php
// TASARIM — repoda YOK; EK AB-2 için önerilen çözüm
final class TransactionRetry
{
    private const MAX_ATTEMPTS = 3;
    private const RETRYABLE    = [1213, '40001'];

    public static function run(callable $fn, ?PDO $pdo = null): mixed
    {
        for ($i = 1; $i <= self::MAX_ATTEMPTS; $i++) {
            try {
                return $fn();
            } catch (\PDOException $e) {
                $code = (string)($e->errorInfo[1] ?? $e->getCode());
                if (!in_array($code, self::RETRYABLE, true) || $i === self::MAX_ATTEMPTS) {
                    throw $e;
                }
                usleep(random_int(10_000, 50_000)); // 10-50ms jitter
            }
        }
        throw new \LogicException('unreachable');
    }
}
```

| Not | Açıklama |
|---|---|
| `1205` retry edilmez | lock timeout ≠ deadlock (§X) |
| Jitter | eşzamanlı retry'ı serbest bırakır |
| `inTransaction()` guard | `$fn` içinde `beginTransaction` varsa önce `rollBack` (§AD.2) |

---

# EK AI — Savepoint (U-4) Derinlemesine Analiz

> `U-4` anayasada açık soru olarak kayıtlı; bu ek kararı destekleyen kanıtları
> toplar. Kodda savepoint kullanımı **YOK** (`START TRANSACTION` dışında
> DDL/sözdizimi kanıtı bulunmadı).

## AI.1 Savepoint temel

| Kavram | SQL | Not |
|---|---|---|
| Savepoint tanımla | `SAVEPOINT sp1` | transaction içinde |
| Savepoint'a dön | `ROLLBACK TO sp1` | kısmi geri alma |
| Savepoint'i sil | `RELEASE SAVEPOINT sp1` | bellek serbest |
| PDO karşılığı | `$pdo->savepoint('sp1')` / `rollBack('sp1')` (PDO 7.4+? **⚠️ VERIFICATION REQUIRED**) | — |

## AI.2 CoreMusic'te gereksinim analizi

| Senaryo | Savepoint gerekli mi? | Gerekçe |
|---|---|---|
| Tek tablo yazma (repo) | Hayır | tek statement |
| Multi-table transaction (32 FK) | **Evet olabilir** | kısmi başarısızlıkta her şeyi geri almak yerine yalnız başarısız adımı |
| Batch insert (migration) | Evet | kısmi rollback |
| Service katmanında 2+ repo çağrısı | **Evet** | her repo kendi transaction'ını yönetiyorsa çakışma (§U.3) |

## AI.3 Karar matrisi

| Seçenek | Avantaj | Dezavantaj |
|---|---|---|
| Savepoint yok (mevcut) | basit | tüm transaction rollback |
| Savepoint her repo çağrısında | kısmi geri alma | karmaşıklık, performans |
| Savepoint yalnız batch işlemlerde | dengeli | **önerilen** |

**Karar:** `⚠️ VERIFICATION REQUIRED` — ADR bekliyor (§AB: savepoint kararı
ADR'ye taşındı).

---

# EK AJ — Transaction ↔ Soft Delete Etkileşimi

> Kanıt: 75 tabloda `is_deleted` kolonu (schema-kapsam), kural 3 (hard delete yasak).

## AJ.1 Soft delete bir transaction işlemi midir?

| Durum | Transaction gerekli mi? |
|---|---|
| Tek satır `UPDATE ... SET is_deleted=1` | Tek statement = implicit transaction ✓ |
| Multi-table soft delete (ör. user + api_keys) | **Evet** — atomic olmalı |
| Soft delete + cache invalidation | EK AE: invalidate commit **sonrası** |

## AJ.2 Unique constraint × soft delete çelişkisi (KRİTİK)

```text
1. Kullanıcı "name=premium" kaydını soft-delete eder (is_deleted=1, name=premium hâlâ duruyor)
2. Yeni kullanıcı "name=premium" kaydını eklemek ister
3. UNIQUE(name) → 1294 Duplicate entry — çünkü soft-deleted satır hâlâ "premium" taşıyor
```

| Çözüm | Uygulama |
|---|---|
| Partial unique index | MySQL 8+ **desteklemiyor** (virtual column + index workaround) → `⚠️` |
| Silmede name'i değiştir | `UPDATE SET is_deleted=1, name=CONCAT(name,'__deleted__',id)` — arama bozulur |
| Composite unique: `(name, is_deleted)` | hile: birden fazla `is_deleted=1` kabul edilir |
| Soft delete yok sayan index | uygulama seviyesinde kontrol — yarış durumu |

**Durum:** bu vault'ta tespit; çözüm **ADR gerektirir**.

---

# EK AK — Transaction İstatistikleri (MySQL — repo'da OKUNMUYOR)

| Sorgu | Amaç | Repo'da |
|---|---|---|
| `SHOW ENGINE INNODB STATUS` | deadlock geçmişi | ❌ yok |
| `information_schema.innodb_trx` | aktif transaction | ❌ yok |
| `performance_schema.events_transactions_history` | transaction geçmişi | ❌ yok |
| `status_handler` (MySQL 8+) | custom metric | ❌ yok |

**Önerilen izleme (uygulanmadı):**

```sql
-- deadlock sayısı (InnoDB metric)
SELECT name, count FROM information_schema.innodb_metrics
WHERE name LIKE '%deadlock%';
```

---

# EK AL — Transaction Kararları (son genişletme)

| # | Karar/tespit | Durum |
|---|---|---|
| AL-1 | CoreMusic'te deadlock riski **mevcut** (Unique + FK + RR gap lock) | ✓ kayıt |
| AL-2 | Sıralama rehberi (§AH.3) uygulanmalı | `⚠️` |
| AL-3 | Retry helper sınıfı tasarlandı, kodlanmadı | `⚠️` |
| AL-4 | Savepoint: batch işlemlerde önerilir | `⚠️` ADR |
| AL-5 | Soft delete + UNIQUE çelişkisi tespit edildi (§AJ.2) | `⚠️` kritik |
| AL-6 | InnoDB istatistikleri okunmuyor (§AK) | `⚠️` |

---

# EK AM — Transaction Hızlı Referans Kartı (repository yazarları için)

> Bu kart, repository/DTO yazarlarının her gün ihtiyaç duyduğu transaction
> kurallarını **tek bakışta** özetler. Kanıt satırları ilgili EK'lere işaret eder.

## AM.1 "Şunu yapayım mı?" matrisi

| Senaryo | Transaction gerekli mi? | Neden |
|---|---|---|
| Tek `INSERT` | ❌ hayır | tek statement atomiktir |
| Tek `UPDATE` (soft delete) | ❌ hayır | tek statement atomiktir |
| 2+ tabloya yazım | **✓ evet** | atomicite (§U.3) |
| `SELECT` + `UPDATE` (inspect deseni) | hayır ama dikkat | 2 round-trip (§AB-7 repositories) |
| Batch insert (migration) | **✓ evet** | kısmi başarısızlık |
| Cross-DB yazım (32 FK) | **✓ evet** | InnoDB tek instance'ta atomik (§40.1) |
| Salt okuma raporu | hayır | MVCC snapshot yeterli (§39.4) |
| Yalnız `SELECT ... FOR UPDATE` | evet (implicit) | kilit transaction sonunda bırakılır |

## AM.2 "Nasıl yazarım?" şablonları

```php
// ŞABLON A — basit multi-table yazım (repo'da transaction AÇMAZ, sarmalar)
$this->db()->beginTransaction();
try {
    $this->db()->write('INSERT ... ', $params1);
    $this->db()->write('UPDATE ... ', $params2);
    $this->db()->commit();
} catch (\Throwable $e) {
    if ($this->db()->getPdo()->inTransaction()) {
        $this->db()->rollBack();
    }
    throw $e;
}
```

| Not | Açıklama |
|---|---|
| `inTransaction()` guard | `rollBack()` açık transaction yokken exception fırlatır |
| `getPdo()` kullanımı | `IDatabaseManager`'da `inTransaction()` **sözleşmede yok** → kaçış kapısı (§R.8 repositories) |
| Alternatif | `IDatabaseManager`'a `inTransaction()` eklenmeli → `⚠️` |

## AM.3 Hızlı hata kodu kartı

| Kod | Anlam | Aksiyon |
|---|---|---|
| `1213` | deadlock | retry (jitter'lı, max 3) |
| `40001` | deadlock (SQLSTATE) | retry |
| `1205` | lock timeout | retry YAPMA — lock kaynağı çözülmeli |
| `1062` | duplicate key | uygulama hatası — kontrol eksik |
| `1452` | FK ihlali | parent yok — veri bütünlüğü hatası |
| `2006`/`2013` | bağlantı koptu | transaction durumu **belirsiz** — reconcile |
| `1040` | bağlantı taşması | retry max 2, uzun jitter |

## AM.4 Kaçınılması gerekenler (anti-kalıp)

| Anti-kalıp | Sonuç | Doğrusu |
|---|---|---|
| Transaction içinde `sleep()` / HTTP çağrısı | lock süresi şişer, deadlock | IO transaction dışında |
| Transaction içinde cache invalidation | rollback = geçersiz flush | invalidation commit sonrası |
| Transaction'ı controller'a açtırmak | katman ihlali | service katmanı sarmalar |
| Uzun transaction (rapor sorgusu) | MVCC şişmesi | `READ ONLY` (§39.4) ya da no-tx |
| `SELECT` + uygulamada karar + `UPDATE` | yarış durumu | `SELECT ... FOR UPDATE` |

---

**Authority:** Bayram Ali / Vault Steward