---
node_id: K007-data/transactions-surucu
node_type: subdomain
status: draft
parent_id: K007-data/transactions
children: []
depends_on:
  - "K007-data/transactions-anayasa"
evidence:
  - "shared/src/Database/DatabaseManager.php:49-67 — begin/commit/rollBack sürüş gövdeleri"
  - "shared/src/Contracts/Database/IDatabaseManager.php:10-12 — sözleşme"
  - "shared/src/Database/DatabaseManager.php:23 — ERRMODE_EXCEPTION"
validation:
  - "2026-10-13 — sürüş akışları §3-§9; 4 doğrulanmış URL; 19 kanıt satırı"
open_questions:
  - "Deadlock retry akışı repo'da yok → ⚠️ VERIFICATION REQUIRED"
  - "Bağlantı kopması ortasında davranış → UNKNOWN"
group: mimari
---

# transactions-surucu — Transaction Sürüş Akışı (K007)

> **Rol:** begin/commit/rollback'in **nasıl sürdüğü** — akışlar, durum
> geçişleri, hata kolları, deadlock ve batch sürüşü. Kurallar için
> `transactions-anayasa.md`, gerçek kod için `transactions-ornekler.md`.

## §1 Amaç ve Kapsam

| Alan | Değer |
|---|---|
| node_id | K007-data/transactions-surucu |
| node_type | subdomain |
| parent_id | K007-data/transactions |
| Grup | mimari |
| Kapsam | sürüş akışları · hata kolları · deadlock · batch · zamanlama |

## §2 Sürüş Sözlüğü

| Türkçe | English | Açıklama |
|---|---|---|
| Sürüş | Drive / execution path | Bir transaction'ın adım adım yürütülmesi |
| Hata kolu | Error branch | Exception/false yolunun sürülmesi |
| Zamanlama | Timing | Begin ve commit arasındaki süre |
| Kilidi bekleme | Lock wait | Kaynak kilidi için bekleme |
| Kurtarma | Recovery | Başarısız transaction sonrası durum |
| Yeniden deneme | Retry | Aynı işlemin tekrarı |

## §3 Ana Sürüş Akışı (Happy Path)

| Adım | Eylem | Sözleşme |
|---|---|---|
| 1 | Zarftan çıkar (bağlantı hazır) | — |
| 2 | `beginTransaction()` çağrılır | `IDatabaseManager.php:10` |
| 3 | Dönüş `true` kontrol edilir | `DatabaseManager.php:49-52` |
| 4 | 1..N `write()` / `execute()` | `IDatabaseManager.php:7-8` |
| 5 | Her dönüş `false`/exception kontrolü | `DatabaseManager.php:23` |
| 6 | Tümü başarılı → `commit()` | `IDatabaseManager.php:11` |
| 7 | Dönüş `true` → zarf biter | `DatabaseManager.php:54-57` |

| Durum | Sonraki |
|---|---|
| IDLE → ACTIVE | adım 2 |
| ACTIVE → COMMITTING | adım 6 |
| COMMITTING → COMMITTED | adım 7 |

## §4 Hata Kolu Sürüşü (Failure Path)

| Tetikleyici | Sürüş |
|---|---|
| `beginTransaction()` false | İşlem başlamaz → hata dön |
| `write()` false | → `rollBack()` → hata dön |
| Exception (`ERRMODE_EXCEPTION`) | → catch → `rollBack()` → log → hata dön |
| `commit()` false | → fail-safe dur (anayasa H-03) |
| `rollBack()` false | → fail-safe dur + kritik log |

| Sıra | Hata Sürüşü |
|---|---|
| F-1 | Hata yakalanır |
| F-2 | Kaynak durumu değerlendirilir |
| F-3 | `rollBack()` çağrılır (`IDatabaseManager.php:12`) |
| F-4 | Rollback `false` dönerse fail-safe |
| F-5 | Log (REDacted) yazılır |
| F-6 | Atomik hata çağırıcıya döner |

## §5 Zamanlama Kuralları (Timing)

| Ölçüm | Kural | Kaynak |
|---|---|---|
| Zarf uzunluğu | Mümkünince kısa | anayasa A-01 |
| Ağ/dosya I/O | Zarfta yasak | A-04 / A-05 |
| Rapor sorgusu | Zarf dışı | Y-08 |
| Uyku/nerg | Zarf içinde yasak | Y-01 |
| Batch | Chunk'lı sürüş | §11 |

## §6 Deadlock Sürüşü

| Durum | Sürüş | Durum Etiketi |
|---|---|---|
| Deadlock algılandı | Motor hata döner | ⚠️ VERIFICATION REQUIRED |
| Retry var mı? | Repo'da görülmedi | ⚠️ VERIFICATION REQUIRED |
| Kilit bekleme süresi | Ölçüm yok | UNKNOWN |
| Deadlock önleme sırası | Repo'da yok | UNKNOWN |

| İddia | Durum |
|---|---|
| Uygulama tarafında deadlock retry döngüsü | Kanıt YOK |
| Motor tarafında deadlock kırma | Motor davranışı — repo dışı |

## §7 Batch Sürüşü (Chunk)

| Adım | Eylem |
|---|---|
| B-1 | İş chunk'lara bölünür (boyut UNKNOWN — U-6) |
| B-2 | Her chunk için BEGIN |
| B-3 | Chunk yazmaları |
| B-4 | Başarılı → COMMIT, başarısız → ROLLBACK |
| B-5 | Başarısız chunk'ta dur + rapor |
| B-6 | Önceki chunk'lar commit'li kalır |

| Durum | Sonraki |
|---|---|
| Chunk n başarılı | chunk n+1 |
| Chunk n başarısız | DUR (retry yok → U-2) |
| Tüm chunk'lar tamam | iş biter |

## §8 Eşzamanlı Sürüş (Concurrency)

| Senaryo | Sürüş |
|---|---|
| İki transaction aynı satırı yazar | Kilit bekleme → sıraya girer |
| `SELECT ... FOR UPDATE` | Satır kilidi tutulur |
| Kilitsiz `SELECT` → `UPDATE` | Yarış riski (anayasa §10) |
| Deadlock | İki taraf da bekleme → motor müdahalesi |
| Persistent bağlantı | Aynı connection'da ardışık zarflar (U-3) |

## §9 Bağlantı Sürüşü (Connection Lifecycle)

| Adım | Eylem | Kanıt |
|---|---|---|
| C-1 | DSN kurulur | `DatabaseManager.php:14-20` |
| C-2 | `new \PDO` + 4 nitelik | `DatabaseManager.php:22-27` |
| C-3 | Persistent bağlantı | `DatabaseManager.php:26` |
| C-4 | begin/commit/rollback aynı `$pdo` | `DatabaseManager.php:49-62` |
| C-5 | `getPdo()` ham erişim | `DatabaseManager.php:64-67` |

## §10 Hata Türü × Sürüş Matrisi

| # | Hata Türü | Sürüş | Sonuç |
|---|---|---|---|
| 1 | Duplicate key | Exception → catch → rollback | Atomik geri alma |
| 2 | FK ihlali | Exception → catch → rollback | Atomik geri alma |
| 3 | Deadlock (1213 sınıfı) | Exception → catch → rollback | ⚠️ retry yok (U-2) |
| 4 | Lock wait timeout | Exception → catch → rollback | UNKNOWN |
| 5 | Bağlantı reddi (begin) | begin false → işlem yok | Hata dön |
| 6 | Bağlantı kopması (orta) | Exception → rollback denemesi | UNKNOWN (U-3) |
| 7 | Disk dolu (commit) | commit false → fail-safe | H-03 |
| 8 | Charset uyumsuzluğu | Mojibake riski | DSN savunması |
| 9 | Parametre eksik | Exception → rollback | ERRMODE_EXCEPTION |
| 10 | SQL sözdizimi hatası | Exception → rollback | prepare aşaması |

## §11 Durum Geçiş Sürücüsü (Transition Driver)

| Geçiş | Koşul | Kod Kanıtı |
|---|---|---|
| IDLE → ACTIVE | `beginTransaction()` true | `DatabaseManager.php:49-52` |
| ACTIVE → COMMITTED | `commit()` true | `DatabaseManager.php:54-57` |
| ACTIVE → ROLLED_BACK | `rollBack()` true | `DatabaseManager.php:59-62` |
| COMMITTED → IDLE | zarf kapanır | — |
| ROLLED_BACK → IDLE | zarf kapanır | — |
| ACTIVE → FAILED | commit/rollback false | fail-safe |
| FAILED → STOP | H-03 | kural |

## §12 Yinelemeli Sürüş (Retry)

| Durum | Davranış | Etiket |
|---|---|---|
| Rollback sonrası retry | Repo'da tanım yok | UNKNOWN (U-2) |
| Deadlock retry | Repo'da tanım yok | ⚠️ VERIFICATION REQUIRED |
| Max deneme sayısı | Bilinmiyor | UNKNOWN |
| Backoff | Bilinmiyor | UNKNOWN |
| Retry edilebilir hata sınıflandırması | Bilinmiyor | UNKNOWN |

## §13 Gözlemlenebilir Sürüş (Observability)

| Sinyal | Nerede | Durum |
|---|---|---|
| Begin/commit/rollback log'u | Repo'da log çağrısı **görülmedi** | UNKNOWN |
| Süre ölçümü | Yok | UNKNOWN |
| Hata log'u (REDacted) | Kural (H-04) | Kural olarak var, uygulama kanıtı yok |
| Transaction kimliği | Yok | UNKNOWN |

## §14 Test Sürüşü (Test Drive)

| Test | Sürüş | Beklenen |
|---|---|---|
| Happy path | begin → write → commit | Satırlar görünür |
| Orta hata | begin → write ok → write hata → rollback | Hiç satır yok |
| Exception | begin → throw → catch → rollback | Hiç satır yok |
| Double begin | begin → begin | Hata/yok sayılma |
| Commit sonrası | commit → yeni begin | Yeni zarf çalışır |
| Rollback sonrası | rollback → yeni begin | Yeni zarf çalışır |

## §15 Performans Sürücüsü

| Faktör | Etki | Kural |
|---|---|---|
| Zarf uzunluğu | Kilit süresi | Kısa tut (A-01) |
| Statement sayısı round-trip | Gecikme | Prepare önceden (execute/write) |
| Persistent bağlantı | Bağlantı kurma kazancı | `DatabaseManager.php:26` |
| Emulate prepare off | Gerçek prepared | `DatabaseManager.php:25` |
| Index kullanmayan sorgu | Uzun kilit | §10 / indexing |
| Chunk boyutu | Bellek + süre dengesi | UNKNOWN (U-6) |

## §16 Ortam Sürücüsü (Environment)

| Ortam | Not | Etiket |
|---|---|---|
| Geliştirme | Yerel MySQL | Motor davranışı bilinmiyor |
| Test | İzole DB | Rollback testi (§14) |
| Production | Persistent bağlantı aktif | U-3 |
| CI | repo `ci.yml` var | Çalışma durumu doğrulanmadı (AGENTS §25.2) |

## §17 Denetim Sürücüsü (Audit)

| # | Denetim Sorusu | Kaynak |
|---|---|---|
| 1 | Her begin bir commit/rollback ile eşleşiyor mu? | §3-§4 |
| 2 | Zarf içinde yasaklı I/O var mı? | anayasa A-04/A-05 |
| 3 | Hata yolunda rollback var mı? | §4 |
| 4 | Deadlock stratejisi tanımlı mı? | §6 — şu an HAYIR |
| 5 | Batch chunk'lı mı? | §7 |
| 6 | Ölçüm toplanıyor mu? | §13 — şu an HAYIR |

## §18 Sürüş Senaryoları (Drive Scenarios)

| # | Senaryo | Sürüş Adımları | Sonuç |
|---|---|---|---|
| D-1 | Tek satır insert | begin → write → commit | 1 satır |
| D-2 | İki tablo insert | begin → write A → write B → commit | 2 tablo atomik |
| D-3 | İkinci write hata | begin → write A ok → write B false → rollback | 0 satır |
| D-4 | Exception ortada | begin → write → throw → catch → rollback | 0 satır |
| D-5 | 500 satırlık batch | 5 chunk × (begin → 100 write → commit) | 500 satır |
| D-6 | Chunk 3 hata | 1-2 commit, 3 rollback, DUR | 200 satır |
| D-7 | Parallel iki zarf | iki ayrı begin → kilitleme bekler | sıralı |
| D-8 | Commit false | fail-safe dur | sistematik duruş |
| D-9 | Rollback false | fail-safe dur + kritik log | sistematik duruş |
| D-10 | Salt okuma | begin yok | — |
| D-11 | Oku-kilitle-yaz | begin → SELECT FOR UPDATE → update → commit | yarış çözüldü |
| D-12 | Deadlock | iki zarf karşılıklı bekler → motor hata | rollback (retry yok) |

## §19 Çağrı Zincisi (Call Chain)

| Katman | Rol | Kanıt |
|---|---|---|
| Controller / Service | Zarftan çıkar | — (iş mantığı K012) |
| Repository | `IDatabaseManager` kullanır | sözleşme |
| `IDatabaseManager` | begin/commit/rollBack sözleşmesi | `IDatabaseManager.php:10-12` |
| `DatabaseManager` | PDO sarmalayıcı | `DatabaseManager.php:49-67` |
| PDO | Gerçek transaction | `beginTransaction`/`commit`/`rollBack` |
| MySQL InnoDB | MVCC + kilit | `ENGINE=InnoDB` (`coremusic_ai.sql:39`) |

| Sınır | Kural |
|---|---|
| İş mantığı transaction **içinde** değil | Zarfta kalmalı (A-04/A-05) |
| Repository transaction **açmaz** | Sarmalar (§19 sınır notu — mimari kural) |

## §20 Hata Kodu Sürüşü

| # | Durum | Sürüş |
|---|---|---|
| 1 | begin false | İşlem hiç başlamadı — hata dön |
| 2 | write false | Rollback + hata |
| 3 | write exception | Catch → rollback + log |
| 4 | commit true | COMMITTED |
| 5 | commit false | Fail-safe (H-03) |
| 6 | rollback true | ROLLED_BACK → hata dön |
| 7 | rollback false | Fail-safe + kritik log |
| 8 | PDO yok / bağlantı yok | Exception → dur |

## §21 Öncelikli Müdahale (Priority Response)

| Öncelik | Durum | Müdahale | Kaynak |
|---|---|---|---|
| CRITICAL | Rollback başarısız | Sistem durur | `.ai/AGENTS.md §8` |
| CRITICAL | Commit false + veri belirsiz | Fail-safe + insan | §16 anayasa H-03 |
| HIGH | Deadlock tekrarı | ⚠️ strateji yok → ADR | U-2 |
| HIGH | Lock wait uzun | Zarf kısaltma | §5 |
| MEDIUM | Batch chunk hata | DUR + rapor | §7 |
| LOW | Performans yavaşlığı | §15 |

## §22 Sürüş Sınıfları (Drive Classes)

| Sınıf | Tanım | Örnek |
|---|---|---|
| K1 — Tek yazma | Tek statement | Basit insert |
| K2 — Çok yazma, tek zarf | İlişkili tablolar | A-01 |
| K3 — Oku-yaz | Yarış riski | §10 |
| K4 — Batch | Chunk'lı | §7 |
| K5 — Kilitli okuma | `SELECT ... FOR UPDATE` | §10 |
| K6 — Kritik (bakiye) | Fail-safe zorunlu | H-03 |

## §23 Sürüş Sınırları (Limits)

| Sınır | Değer | Etiket |
|---|---|---|
| Max zarf süresi | Tanımsız | UNKNOWN (U-1) |
| Max statement/zarf | Tanımsız | UNKNOWN (U-6) |
| Max chunk boyutu | Tanımsız | UNKNOWN (U-6) |
| Kilit bekleme süresi | Tanımsız | UNKNOWN (U-2) |
| Retry max | Tanımsız | UNKNOWN (U-2) |
| Connection timeout | Config'te | `DatabaseConfig` — okunmadı → UNKNOWN |

## §24 Gözlem Noktaları (Instrumentation Points)

| # | Nokta | Ne ölçülür | Durum |
|---|---|---|---|
| 1 | `beginTransaction()` sonrası | Başarı oranı | eklenmedi |
| 2 | `commit()` öncesi | Zarf süresi | eklenmedi |
| 3 | `rollBack()` | Rollback sayısı | eklenmedi |
| 4 | Exception catch | Hata türü | eklenmedi |
| 5 | Batch chunk sonu | İlerleme | eklenmedi |

> Tüm gözlem noktaları **eklenmedi** → metrik toplama `UNKNOWN`.

## §25 Sürüş Şablonları (Pseudocode)

```text
ŞABLON-1 (temel)
  BEGIN
    write(sql1, params1)
    write(sql2, params2)
  on error -> ROLLBACK + log + return error
  COMMIT

ŞABLON-2 (okuma-kilitleme)
  BEGIN
    row = execute("SELECT ... FOR UPDATE")
    validate(row)
    write(update)
  on error -> ROLLBACK
  COMMIT

ŞABLON-3 (batch chunk)
  for chunk in chunks:
    BEGIN
      for item in chunk: write(item)
    on error -> ROLLBACK, DUR, rapor
    COMMIT
```

> Pseudocod'dur; gerçek kod örnekleri `transactions-ornekler.md` §-ilerisinde.

## §26 Doğrulama Adımları (Verification Drive)

| # | Adım | Yöntem | Kriter |
|---|---|---|---|
| V-1 | Sözleşme eşleşmesi | `IDatabaseManager` vs `DatabaseManager` imzalar | 6/6 metot eşleşir |
| V-2 | Hata modu | `DatabaseManager.php:23` okundu | `ERRMODE_EXCEPTION` |
| V-3 | Eşleşme sayımı | begin ↔ commit/rollback | 1:1 |
| V-4 | Yasaklı I/O taraması | Zarfta ağ çağrısı | 0 |
| V-5 | Motor kanıtı | `ENGINE=InnoDB` | `coremusic_ai.sql:39` |
| V-6 | İzolasyon ayarı | `SET TRANSACTION` taraması | 0 → U-1 |
| V-7 | Deadlock stratejisi | retry kodu taraması | 0 → U-2 |
| V-8 | Metrik toplama | Log/counter taraması | 0 → §24 |

## §27 Sürüş × Ortam Matrisi

| Ortam | Persistent | İzolasyon | Ölçüm | Durum |
|---|---|---|---|---|
| Geliştirme | `true` (kodda sabit) | bilinmiyor | yok | kanıt: satır 26 |
| Test | `true` (kodda sabit) | bilinmiyor | yok | — |
| CI | `ci.yml` mevcut | bilinmiyor | yok | CI geçti mi → doğrulanmadı |
| Production | `true` (kodda sabit) | bilinmiyor | yok | ⚠️ U-3 |

## §28 Sürüş Riskleri

| # | Risk | Olasılık | Etki | Savunma |
|---|---|---|---|---|
| R-1 | Açık transaction sızıntısı (persistent) | Orta | Yüksek | §9 C-3 + U-3 |
| R-2 | Uzun zarf kilit | Orta | Yüksek | §5 |
| R-3 | Deadlock tekrarı | Düşük | Orta | U-2 (ADR) |
| R-4 | Yarış durumu (bakiye) | Orta | Yüksek | §10 |
| R-5 | Batch yarım kapanış | Orta | Orta | §7 |
| R-6 | Commit false | Düşük | Kritik | H-03 |
| R-7 | Ölçüm eksikliği | Yüksek | Orta | §24 |

## §29 Sürüş Rolleri (Drive Ownership)

| Görev | Sorumlu |
|---|---|
| Sürüş kalıbının tasarımı | Data Engineer |
| Sözleşme (`IDatabaseManager`) | Backend Architect |
| Hata yönetimi (exception) | Backend Architect |
| Test sürüşleri | QA Engineer |
| Deadlock ADR'si | Architect |
| Gözlem metrikleri | DevOps Engineer |

## §30 Sürüş Karar Matrisi (Decision Matrix)

| Durum | Karar | Bölüm |
|---|---|---|
| Tek statement | Transaction yok | §19 |
| Çoklu statement | BEGIN zorunlu | §3 |
| Hata | Rollback zorunlu | §4 |
| Ağ/dosya I/O | Zarf dışına | §5 |
| Deadlock | Strateji yok → ADR | §6 |
| Batch > tek zarf | Chunk'la | §7 |
| Ölçüm yok | Metrik ekle | §24 |
| Rollback false | Fail-safe | §4 |

## §31 Web Kaynakları (Doğrulanmış — HTTP 200 / 2026-10-13)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| BEGIN/COMMIT/ROLLBACK sürüş anlatımı | https://www.postgresql.org/docs/current/tutorial-transactions.html | 2026-10-13 | 200 OK |
| Açık kilitleme sürüşü | https://www.postgresql.org/docs/current/explicit-locking.html | 2026-10-13 | 200 OK |
| MVCC sürüş davranışı | https://www.postgresql.org/docs/current/mvcc.html | 2026-10-13 | 200 OK |
| Session yaşam döngüsü (sürüş örneği) | https://docs.sqlalchemy.org/en/20/orm/session_basics.html | 2026-10-13 | 200 OK |

> MySQL InnoDB'ye aktarım her iddiada ayrıca doğrulanmalıdır (`dev.mysql.com` 403 → kullanılmayacak).

## §32 Bilinmeyenler (UNKNOWN / VERIFICATION REQUIRED)

| # | Soru | Etiket |
|---|---|---|
| U-1 | Aktif izolasyon seviyesi | ⚠️ VERIFICATION REQUIRED |
| U-2 | Deadlock/lock-wait retry stratejisi | ⚠️ VERIFICATION REQUIRED |
| U-3 | Persistent bağlantı + açık transaction | UNKNOWN |
| U-4 | Savepoint sürüşü | UNKNOWN |
| U-5 | Gözlem metrikleri | UNKNOWN |
| U-6 | Chunk/limit değerleri | UNKNOWN |
| U-8 | Connection timeout değeri | UNKNOWN |

## §33 Sürüş Sözlüğü Ek (Drive Glossary)

| Türkçe | English | Not |
|---|---|---|
| Mutlak zarf | Atomic envelope | begin-commit arası |
| Kilit genişliği | Lock scope | İndeks üzerinden (§19) |
| Kırılma | Abort | Motorun transaction sonlandırması |
| Kurtarma günlüğü | Recovery log | Motor içi — repo dışı |
| Yarı iş | Partial work | Rollback ile silinir |
| İlerleme işareti | Checkpoint | Batch'te bilinmiyor (U-6) |
| Kuyruk | Queue | AP-4 — transaction sonrası |
| Fail-safe | Fail-safe | H-03, fail-open yasak |
| Kanıt | Evidence | Dosya:satır zorunlu |
| Belirsizlik etiketi | Uncertainty flag | UNKNOWN / VERIFICATION REQUIRED |

## §33.1 Sürüş Kayıtları (Drive Log Formatı)

| Alan | Zorunlu mu | Not |
|---|---|---|
| Zarf kimliği | Bilinmiyor | U-5 |
| Başlangıç zamanı | Bilinmiyor | U-5 |
| Statement sayısı | Bilinmiyor | U-6 |
| Sonuç (commit/rollback) | Zorunlu (kural) | §4 |
| Hata sınıfı | Zorunlu (kural) | §10 |
| Süre | Bilinmiyor | U-5 |

> Tümü repo'da **eklenmedi** → gözlem `UNKNOWN` (§24).

## §34 Sürüş-Kontrol Listesi (Drive Checklist)

| # | Kontrol | Geçerse | Kalırsa |
|---|---|---|---|
| C-1 | BEGIN 1:1 COMMIT/ROLLBACK | Devam | RED |
| C-2 | Hata yolunda rollback | Devam | BLOCK |
| C-3 | Zarf süresi kısa | Devam | RED |
| C-4 | Ağ/dosya I/O yok | Devam | RED |
| C-5 | Ölçüm noktası var | Devam | UNKNOWN işaretle |
| C-6 | Deadlock stratejisi tanımlı | Devam | ADR aç |
| C-7 | Batch chunk'lı | Devam | §7 |
| C-8 | Test sürüşü geçiyor | Tamam | QA bloğu |

## §35 Bağlantılı Düğümler

| Düğüm | İlişki |
|---|---|
| `transactions-anayasa` | Kurallar bu dosyadan gelir |
| `transactions-ornekler` | Gerçek kod bu dosyada |
| `K007-data/repositories` | Çağrıran katman |
| `K007-data/indexing` | Kilitleri etkiler |
| `K007-data/migrations` | DDL sürüşü ayrı |

## §36 Revizyon

| Tarih | Değişiklik |
|---|---|
| 2026-10-13 | İlk yazım — sürüş akışları + 19 kanıt + 4 URL |

## §37 Sürüm

| Alan | Değer |
|---|---|
| Versiyon | 1.0.0 |
| Tarih | 2026-10-13 |
| Durum | draft |
| Erişim | `.ai/architecture/K007-data/transactions/transactions-surucu.md` |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
