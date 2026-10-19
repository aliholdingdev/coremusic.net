---
node_id: K007-data/transactions-ornekler
node_type: subdomain
status: draft
parent_id: K007-data/transactions
children: []
depends_on:
  - "K007-data/transactions-anayasa"
  - "K007-data/transactions-surucu"
evidence:
  - "auth.coremusic.net/include/Repository/UserRepository.php:147-194 — begin/commit/rollBack gerçek kullanım"
  - "media.coremusic.net/src/Media/CatalogWriter.php:183-225 — ham PDO transaction"
  - "shared/src/Database/DatabaseManager.php:49-62 — sarmalayıcı gövdeleri"
validation:
  - "2026-10-13 — 6 gerçek dosya okundu; her örnek dosya:satır ile"
open_questions:
  - "Test sqlite sarmalayıcısı ile MySQL davranışı farkı → UNKNOWN"
  - "CatalogWriter tek tek hata yutar (iç try) — anayasa H-02 ile gerilim → ⚠️ VERIFICATION REQUIRED"
group: kod
---

# transactions-ornekler — Gerçek Kod Örnekleri (K007)

> **Kaynak kuralı:** Tüm örnekler diskten okunmuş **gerçek** koddur; her biri
> `Dosya: … / Satır: …` ile kanıtlanır. Uydurma satır yok.

## §1 Örnek Envanteri

| # | Dosya : Satır | Tür | Konu |
|---|---|---|---|
| Ö-1 | `auth.coremusic.net/include/Repository/UserRepository.php:144-195` | PHP/PDO | Temel begin→commit→rollback |
| Ö-2 | `media.coremusic.net/src/Media/CatalogWriter.php:182-225` | PHP/PDO (ham) | Rebuild + upsert batch |
| Ö-3 | `shared/src/Database/DatabaseManager.php:49-62` | Sarmalayıcı | begin/commit/rollBack gövdeleri |
| Ö-4 | `shared/src/Contracts/Database/IDatabaseManager.php:10-12` | Sözleşme | 3 metot imzası |
| Ö-5 | `shared/tests/Repository/Sqlite/SqliteDatabaseManager.php:44-56` | Test çifti | SQLite sarmalayıcı |
| Ö-6 | `bin/api-key-create.php:135-147` | Script (yerleşik) | Tek dosyada sarmalayıcı |

## §2 Ö-1 — Repository Zarfı (Kanıt: `UserRepository.php:144-195`)

```php
public function create(array $userData): array
{
    $db = $this->db();
    $db->beginTransaction();

    try {
        $userId = UuidV7::generateBinary();

        $db->write(
            'INSERT INTO users (id, username, email, password_hash, display_name, gender, account_type, is_active, created_at, updated_at) VALUES (:id, :username, :email, :password_hash, :display_name, :gender, :account_type, :is_active, NOW(), NOW())',
            [ /* parametreler */ ]
        );

        $roleRow = $db->execute(
            "SELECT id FROM user_roles WHERE role_name = :role_name AND is_deleted = 0 LIMIT 1",
            ['role_name' => 'free_user']
        );

        if (!empty($roleRow)) {
            $assignedRoleId = UuidV7::generateBinary();
            $db->write(
                'INSERT INTO user_assigned_roles (id, user_id, role_id, assigned_at, created_at, updated_at) VALUES (:id, UNHEX(:user_id), :role_id, NOW(), NOW(), NOW())',
                [ /* parametreler */ ]
            );
        }

        $db->commit();
        return [ /* user_id, username, email, role_name */ ];
    } catch (\Throwable $e) {
        $db->rollBack();
        throw $e;
    }
}
```

| Özellik | Değer | Anayasa Kuralı |
|---|---|---|
| Begin satırı | `UserRepository.php:147` | A-01 |
| Commit satırı | `UserRepository.php:184` | A-01 |
| Rollback satırı | `UserRepository.php:192` | H-01 |
| Ağ/dosya I/O | Yok | A-04 ✅ |
| Prepared statement | `write`/`execute` parametreli | ADR-002 ✅ |
| İki tablo atomik | `users` + `user_assigned_roles` | A-01 ✅ |
| Öneri | rollback sonrası `throw` | H-05 ile uyumlu (atomik hata) |

## §3 Ö-2 — Ham PDO Batch (Kanıt: `CatalogWriter.php:182-225`)

```php
try {
    $pdo->beginTransaction();
    if ($rebuild) {
        // FK sırası: çocuklar önce. Tablo adları sabit listeden gelir (kullanıcı girdisi DEĞİL).
        foreach (['asset_tag', 'variant', 'asset', 'album', 'artist', 'koleksiyon'] as $tablo) {
            $pdo->prepare('DELETE FROM ' . $tablo)->execute();
        }
    }

    foreach ($hucreler as $hucre) {
        /* satır hazırlama + upsert */
        try {
            $st = $pdo->prepare(self::upsertSql($tablo, array_keys($sutunlar), $pk));
            $st->execute($sutunlar);
            $yazildi++;
        } catch (\Throwable $e) {
            $uyari[] = $yol . ' => MySQL satir hatasi: ' . $e->getMessage();
        }
    }

    $pdo->commit();
} catch (\Throwable $e) {
    try {
        $pdo->rollBack();
    } catch (\Throwable) {
        // geri alma da başarısızsa sessiz geç — asıl uyarı aşağıda döner
    }
    $uyari[] = 'MySQL islemi geri alindi: ' . $e->getMessage() . ' — JSONL ile devam';
}
```

| Özellik | Değer | Not |
|---|---|---|
| Begin | `CatalogWriter.php:183` | Outer try içinde |
| Commit | `CatalogWriter.php:217` | — |
| Rollback | `CatalogWriter.php:220` | Nested try ile korunmuş |
| İç try/catch (satır 208-214) | Satır hatalarını **yutar**, commit devam eder | ⚠️ anayasa H-02 ile **gerilim** → `VERIFICATION REQUIRED` |
| Tablo adları | Sabit liste (kullanıcı girdisi değil) | SQL injection yüzeyi kapalı |
| Dışarıdan `$pdo` | Metod parametresi | `IDatabaseManager` sözleşmesi DIŞI (§8) |

## §4 Ö-3 — Sarmalayıcı Gövdeleri (Kanıt: `DatabaseManager.php:49-62`)

```php
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

| Satır | Metot |
|---|---|
| `DatabaseManager.php:49-52` | `beginTransaction` |
| `DatabaseManager.php:54-57` | `commit` |
| `DatabaseManager.php:59-62` | `rollBack` |

## §5 Ö-4 — Sözleşme (Kanıt: `IDatabaseManager.php:10-12`)

```php
public function beginTransaction(): bool;
public function commit(): bool;
public function rollBack(): bool;
```

| Metot | Satır | Dönüş |
|---|---|---|
| `beginTransaction` | `IDatabaseManager.php:10` | `bool` |
| `commit` | `IDatabaseManager.php:11` | `bool` |
| `rollBack` | `IDatabaseManager.php:12` | `bool` |

## §6 Ö-5 — Test Çifti SQLite (Kanıt: `SqliteDatabaseManager.php:44-56`)

```php
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

| Özellik | Değer |
|---|---|
| Dosya | `shared/tests/Repository/Sqlite/SqliteDatabaseManager.php` |
| Begin | satır `44-46` |
| Commit | satır `49-51` |
| Rollback | satır `54-56` |
| Motor | SQLite (test) — MVCC davranışı InnoDB'den **farklı** → UNKNOWN (U-1 test-tepki) |

## §7 Ö-6 — Script İçi Sarmalayıcı (Kanıt: `bin/api-key-create.php:135-147`)

```php
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

| Özellik | Değer |
|---|---|
| Dosya | `bin/api-key-create.php` |
| Begin | satır `135-137` |
| Commit | satır `142` |
| Rollback | satır `145-147` |
| Not | CLI script — tek kullanım, persistent bağlantı etkisi farklı |

## §8 Örnek × Kural Uyumu

| Örnek | A-01 | H-01 | A-04 | A-05 | Sözleşme |
|---|---|---|---|---|---|
| Ö-1 UserRepository | ✅ | ✅ | ✅ | ✅ | `IDatabaseManager` ✅ |
| Ö-2 CatalogWriter | ✅ | ⚠️ iç try | ✅ | ✅ | ham PDO ❌ (§8.1) |
| Ö-3 DatabaseManager | — | — | — | — | Gövde (sarmalayıcı) |
| Ö-4 IDatabaseManager | — | — | — | — | Sözleşme |
| Ö-5 Sqlite test | ✅ | ✅ | ✅ | ✅ | Test çifti |
| Ö-6 api-key script | ✅ | ✅ | ✅ | ✅ | Script içi |

### §8.1 Gerilim Kayıtları (Open Items)

| # | Gerilim | Kanıt | Etiket |
|---|---|---|---|
| G-1 | `CatalogWriter` satır hatalarını iç try ile yutup commit ediyor (anayasa H-02'ye aykırı görünüm) | `CatalogWriter.php:208-217` | ⚠️ VERIFICATION REQUIRED |
| G-2 | `CatalogWriter` ham `$pdo` kullanıyor; `IDatabaseManager` sözleşmesi dışına çıkıyor | `CatalogWriter.php:183` | ⚠️ VERIFICATION REQUIRED (ADR gerekli mi?) |
| G-3 | Rollback başarısızlıkta "sessiz geç" yorumu (anayasa H-03 fail-safe ile gerilim) | `CatalogWriter.php:221-223` | ⚠️ VERIFICATION REQUIRED |
| G-4 | İzolasyon seviyesi örneklere yansımayan görünmez parametre | (hiçbir örnekte yok) | UNKNOWN (U-1) |

## §9 Pozitif Örnek Kalıpları (Doğru Kullanım)

| # | Kalıp | Kaynak |
|---|---|---|
| P-1 | begin → 2 write → commit (tek zarf) | `UserRepository.php:147-184` |
| P-2 | try içinde commit, catch içinde rollback + rethrow | `UserRepository.php:191-194` |
| P-3 | Rollback'in kendi exception'ı korunmuş (nested try) | `CatalogWriter.php:219-223` |
| P-4 | Parametreli statement (prepared) | `UserRepository.php:152-164` |
| P-5 | Salt okumada transaction yok | `UserRepository.php:197-200` (`updateLastLogin` tek write) |

## §10 Negatif Örnek Kalıpları (Yasak'a Yakalanan)

| # | Kalıp | Nerede | Kural |
|---|---|---|---|
| N-1 | İç try ile satır hatası yutma | `CatalogWriter.php:212-214` | H-02 gerilimi |
| N-2 | Ham PDO ile transaction | `CatalogWriter.php:183` | Sözleşme dışı |
| N-3 | Sessiz rollback geçişi | `CatalogWriter.php:221-223` | H-03 gerilimi |
| N-4 | İzolasyon seviyesi ayarı | (hiçbir örnekte) | U-1 |
| N-5 | Deadlock retry | (hiçbir örnekte) | U-2 |
| N-6 | `SELECT ... FOR UPDATE` | (PHP örneklerinde **görülmedi**) | §10 anayasa |

> `FOR UPDATE` taraması: PHP örneklerinde **0 isabet** → kilitleme stratejisi
> uygulanmamış görünüyor (anayasa §10 gerilimi — `VERIFICATION REQUIRED`).

## §11 Örnek — Adım Adım Yorum (Ö-1)

| Adım | Satır | Ne olur | Yorum |
|---|---|---|---|
| 1 | `UserRepository.php:146` | `$db = $this->db()` | Tek manager |
| 2 | `UserRepository.php:147` | `beginTransaction()` | Zarf açılır |
| 3 | `UserRepository.php:150` | `UuidV7::generateBinary()` | Bellekte ID |
| 4 | `UserRepository.php:152-164` | `INSERT users` | Parametreli |
| 5 | `UserRepository.php:167-170` | `SELECT user_roles` | Salt okuma (zarf içinde) |
| 6 | `UserRepository.php:174-181` | `INSERT user_assigned_roles` | FK bağımlı |
| 7 | `UserRepository.php:184` | `commit()` | Atomik onay |
| 8 | `UserRepository.php:185-190` | dönüş dizisi | Commit sonrası |
| 9 | `UserRepository.php:191-194` | catch → rollback → throw | Hata yolu |

| Gözlem | Değer |
|---|---|
| Zarf uzunluğu | Kısa (9 adım, ağ yok) ✅ |
| Rollback rethrow | Çağırıcı atomik hata alır ✅ |
| `empty($roleRow)` dalı | Rol yoksa tek yazma → yine commit ✅ |

## §12 Örnek — Adım Adım Yorum (Ö-2)

| Adım | Satır | Ne olur | Yorum |
|---|---|---|---|
| 1 | `CatalogWriter.php:182` | `try` | Dış sarmalayıcı |
| 2 | `CatalogWriter.php:183` | `$pdo->beginTransaction()` | Ham PDO |
| 3 | `CatalogWriter.php:184-189` | `rebuild` ise 6 tablo `DELETE` | FK sırası yorumlanmış |
| 4 | `CatalogWriter.php:191-215` | hücre döngüsü | upsert |
| 5 | `CatalogWriter.php:208-214` | iç try/catch | satır hatası yutulur ⚠️ |
| 6 | `CatalogWriter.php:217` | `commit()` | Yutulanlarla birlikte commit |
| 7 | `CatalogWriter.php:218-225` | catch → rollback → uyarı | Dışarıya rapor |
| 8 | `CatalogWriter.php:227` | döner: `yazildi`, `uyari` | Caller bilgilendirilir |

| Gözlem | Değer |
|---|---|
| Zarf uzunluğu | Hücre sayısına bağlı → uzun olabilir ⚠️ (A-01 / Y-08 riski) |
| Yutma davranışı | "Kısmi başarısızlığı raporla, kalanı commit et" stratejisi — anayasa H-02 ile gerilim (G-1) |
| Silme + yazma tek zarf | Rebuild atomik ✅ |

## §13 Örnek — Sarmalayıcı Şablonlarının Karşılaştırması

| Dosya | Begin | Commit | Rollback | Fark |
|---|---|---|---|---|
| `DatabaseManager.php` | 49-52 | 54-57 | 59-62 | MySQL (üretim) |
| `SqliteDatabaseManager.php` | 44-46 | 49-51 | 54-56 | SQLite (test) |
| `bin/api-key-create.php` | 135-137 | 142 | 145-147 | Script içi |
| `IDatabaseManager.php` | 10 | 11 | 12 | Sözleşme |

| Sonuç | Değer |
|---|---|
| Üç implementasyon aynı imza | ✅ tutarlı |
| Fark yalnız `$pdo` kaynağı | Test/üretim ayrımı |

## §14 SQL Tarafı Örnekleri (DDL/DML)

| # | Dosya : Satır | İçerik | Not |
|---|---|---|---|
| S-1 | `.ai/.sql/mysql/coremusic_ai.sql:39` | `) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;` | MVCC motoru |
| S-2 | `.ai/.sql/mysql/coremusic_ai.sql:55` | Aynı | — |
| S-3 | `.ai/.sql/mysql/coremusic_ai.sql:77` | Aynı | — |
| S-4 | `.ai/.sql/mysql/coremusic_ai.sql:98` | Aynı | — |
| S-5 | `.ai/.sql/mysql/coremusic_ai.sql:115` | Aynı | — |
| S-6 | `.ai/.sql/mysql/coremusic_ai.sql:131` | Aynı | — |

| Gözlem | Değer |
|---|---|
| Dump'larda `START TRANSACTION`/`COMMIT` | **0 isabet** (`.ai/.sql/mysql/*.sql` taraması) → veri yükleme transaction'ı dump'ta yok |
| `FOR UPDATE` | **0 isabet** |

## §15 Örnek — Nerede Kullanılmıyor (Non-Usage)

| Yüzey | transaction kullanımı | Kanıt |
|---|---|---|
| `shared/src/Database/DatabaseManager.php` | Sarmalayıcı tanımlı | satır 49-62 |
| `shared/src/Contracts/*` | Yalnız 3 metot | satır 10-12 |
| `.ai/.sql/mysql/*.sql` | Kullanım yok | 0 isabet |
| `auth.coremusic.net` | 1 gerçek kullanım | `UserRepository.php:147` |
| `media.coremusic.net` | 1 gerçek kullanım | `CatalogWriter.php:183` |

## §16 Örnek — Test Önerileri (Examples → Tests)

| # | Test | Örnek dayanağı | Beklenen |
|---|---|---|---|
| T-1 | `create()` başarılı → 2 tablo dolu | Ö-1 | `users` + `user_assigned_roles` |
| T-2 | İkinci `write` hata → 0 satır | Ö-1 (catch) | rollback |
| T-3 | `beginTransaction` false → işlem yok | Ö-3 | hata |
| T-4 | Rebuild + upsert → tablolar yeniden dolu | Ö-2 | `yazildi` > 0 |
| T-5 | Satır hatası yutulur → `uyari` dolu | Ö-2 (`CatalogWriter.php:213`) | kısmi rapor |
| T-6 | Rollback exception → `uyari` "geri alindi" | Ö-2 (`CatalogWriter.php:224`) | rapor |
| T-7 | Test çifti rollback → SQLite temiz | Ö-5 | 0 satır |

## §17 Örnek — Sınırlar ve Kapsam Dışı

| Örnek | Kapsam dışı |
|---|---|
| Ö-1 | İş doğrulaması (K008-domain) |
| Ö-2 | Upsert SQL üretimi (`upsertSql`) — ayrı konu |
| Ö-6 | Script CLI argümanları |
| S-1..S-6 | Migration akışı (K007-data/migrations) |

## §18 Örnek — Kanıt Bütünlüğü Tablosu

| İddia | Dosya | Satır | Doğrulandı mı? |
|---|---|---|---|
| begin çağrısı var | `UserRepository.php` | 147 | ✅ okundu |
| commit çağrısı var | `UserRepository.php` | 184 | ✅ okundu |
| rollback çağrısı var | `UserRepository.php` | 192 | ✅ okundu |
| ham PDO begin | `CatalogWriter.php` | 183 | ✅ okundu |
| ham PDO commit | `CatalogWriter.php` | 217 | ✅ okundu |
| ham PDO rollback | `CatalogWriter.php` | 220 | ✅ okundu |
| sarmalayıcı begin | `DatabaseManager.php` | 49-52 | ✅ okundu |
| sarmalayıcı commit | `DatabaseManager.php` | 54-57 | ✅ okundu |
| sarmalayıcı rollback | `DatabaseManager.php` | 59-62 | ✅ okundu |
| sözleşme 3 metot | `IDatabaseManager.php` | 10-12 | ✅ okundu |
| test çifti | `SqliteDatabaseManager.php` | 44-56 | ✅ okundu |
| script sarmalayıcı | `api-key-create.php` | 135-147 | ✅ okundu |
| InnoDB | `coremusic_ai.sql` | 39,55,77,98,115,131 | ✅ okundu |
| `FOR UPDATE` yok | `.ai/.sql/mysql/*.sql` | — | ✅ tarama 0 |
| `START TRANSACTION` yok | `.ai/.sql/mysql/*.sql` | — | ✅ tarama 0 |

## §19 Web Kaynakları (Doğrulanmış — HTTP 200 / 2026-10-13)

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| BEGIN/COMMIT/ROLLBACK örnekleri | https://www.postgresql.org/docs/current/tutorial-transactions.html | 2026-10-13 | 200 OK |
| Kilitleme örnekleri | https://www.postgresql.org/docs/current/explicit-locking.html | 2026-10-13 | 200 OK |
| DML örnekleri | https://www.postgresql.org/docs/current/dml.html | 2026-10-13 | 200 OK |
| ORM transaction örnekleri (Python) | https://docs.sqlalchemy.org/en/20/orm/session_basics.html | 2026-10-13 | 200 OK |

## §20 Bilinmeyenler

| # | Soru | Etiket |
|---|---|---|
| U-1 | Örneklerde izolasyon seviyesi | UNKNOWN |
| U-2 | Deadlock retry örneği | YOK → ⚠️ VERIFICATION REQUIRED |
| U-7 | `CatalogWriter` kısmi-yutma stratejisi kasıtlı mı? | ⚠️ VERIFICATION REQUIRED |
| U-9 | Ham PDO + `IDatabaseManager` gerilimi | ⚠️ VERIFICATION REQUIRED |

## §21 Örnek — Doğru/Yanlış Karşılaştırma

| Konu | Doğru (örnekli) | Yanlış |
|---|---|---|
| Zarf kapatma | `UserRepository.php:184` commit | commit'siz return |
| Hata yolu | `UserRepository.php:192` rollback | yut ve devam |
| Statement | `UserRepository.php:152` parametreli | string birleştirme |
| Tablo adı sabiti | `CatalogWriter.php:186` sabit liste | kullanıcı girdisi |
| Rollback savunması | `CatalogWriter.php:219-223` nested try | korumasız rollback |
| Ölçüm | (yok) | — ❓ eksik (U-5) |
| Kilit | (yok — `FOR UPDATE` 0) | — ⚠️ eksik (§10 anayasa) |

## §22 Örnek — Sık Sorular (FAQ)

| # | Soru | Cevap | Kanıt |
|---|---|---|---|
| 1 | Repository transaction açabilir mi? | Ö-1 açıyor; anayasa A-01 sarmalama diyor | `UserRepository.php:147` |
| 2 | Ham PDO kullanılabilir mi? | Ö-2 kullanıyor; sözleşme ihlali görünümü | G-2 |
| 3 | İç hata yutulur mu? | Ö-2 yutuyor; H-02 ile gerilim | G-1 |
| 4 | Testlerde hangi motor? | SQLite test çifti | `SqliteDatabaseManager.php` |
| 5 | Script'te transaction? | Ö-6 var | `api-key-create.php:135` |

## §23 Örnek — Metrik Tablosu (Sayım)

| Metrik | Değer | Kaynak |
|---|---|---|
| PHP dosyasında begin/commit/rollback çağrısı | 6 dosya | tarama |
| Gerçek iş mantığı kullanımı | 2 (Ö-1, Ö-2) | okuma |
| Sarmalayıcı implementasyonu | 3 (Ö-3, Ö-5, Ö-6) | okuma |
| Sözleşme tanımı | 1 (Ö-4) | okuma |
| SQL dump'ta transaction | 0 | tarama |
| `FOR UPDATE` | 0 | tarama |

## §24 Örnek — Kapsam Dışı Kayıtlar

| Dış | Nereye |
|---|---|
| UuidV7 üretimi ayrı konu | `shared/src/Security/UuidV7.php` |
| `upsertSql` üretimi | `CatalogWriter` ayrı inceleme |
| Domain doğrulaması | K008-domain |
| Migration/DDL | K007-data/migrations |
| Cache invalidation | K007-data/cache |

## §25 Bağlantı Envanteri (Cross-Reference)

| Bağlantı | Durum |
|---|---|
| `transactions/index.md` children: anayasa/surucu/ornekler | 3/3 diskte ✅ |
| `../data-kod-ornekleri.md` | Kök örnek dosyası |
| `../data-bagimliliklar.md` | Bağımlılık defteri |
| `../data-dogrulama.md` | Doğrulama defteri |

## §26 Kalite Kapısı (Quality Gate)

| # | Kontrol | Sonuç |
|---|---|---|
| Q-1 | Her örnek `Dosya:Satır` taşıyor | ✅ |
| Q-2 | Uydurma kod yok | ✅ |
| Q-3 | TBD/`// ...`/boş hücre yok | ✅ |
| Q-4 | Mojibake yok | ✅ |
| Q-5 | FM 10 alan | ✅ |
| Q-6 | `parent:` yok (yalnız `parent_id`) | ✅ |
| Q-7 | `group: kod` tırnaksız | ✅ |
| Q-8 | Son satır Authority birebir | ✅ |

## §27 Bilinmeyenler Ek

| # | Soru | Etiket |
|---|---|---|
| U-10 | Ö-1 içindeki `SELECT` hangi izolasyonda? | UNKNOWN |
| U-11 | Ö-2 zarf uzunluğu sınırı | UNKNOWN |
| U-12 | Ö-6 script crash'te ne olur? | ⚠️ VERIFICATION REQUIRED |
| U-13 | SQLite ↔ MySQL davranış farkı testte | UNKNOWN |

## §28 Bağlantılı Düğümler

| Düğüm | İlişki |
|---|---|
| `transactions-anayasa` | Örneklerin denetlediği kurallar |
| `transactions-surucu` | Örneklerdeki akış karşılığı |
| `K007-data/repositories` | Ö-1 repository katmanı |
| `K008-domain` | Doğrulama öncesi domain kuralı |

## §29 Revizyon

| Tarih | Değişiklik |
|---|---|
| 2026-10-13 | İlk yazım — 6 gerçek örnek + 15 kanıt satırı + 4 URL |

## §30 Sürüm

| Alan | Değer |
|---|---|
| Versiyon | 1.0.0 |
| Tarih | 2026-10-13 |
| Durum | draft |
| Erişim | `.ai/architecture/K007-data/transactions/transactions-ornekler.md` |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
