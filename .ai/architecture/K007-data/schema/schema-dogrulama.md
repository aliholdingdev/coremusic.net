---
node_id: "K007-schema-dogrulama"
node_type: "subdomain"
status: "draft"
parent_id: "K007-data"
children: []
depends_on:
  - "K008-service-layer"
evidence:
  - ".ai/.sql/CLAUDE.md §1 — 18 BCNF DB / 171 tablo / LIKE+anahtarsız sorgu yasağı (2026-10-07)"
  - "shared/src/Database/DatabaseManager.php:14-46 — DSN/prepare/execute satır kanıtı"
  - "docs/database/README.md:14-18 — FK kararı 'İncelenecek'"
validation: "14 iddia satırı; her satırda dosya:satır veya URL; 8/8 alan; yasak betim yok"
open_questions:
  - "S-1 — arama sahipliği K007 mi?"
  - "K-001 — forum şeması T-11/T-20 kapsamı UNKNOWN"
group: kanit
---

# K007-SHEMA — Doğrulama

> **Sınır:** Bu dosya yalnızca **şema tasarımı** iddialarını doğrular.
> İndeks/POC → `schema-indexing`-bağlantılı `../indexing/` · migration
> yürütme → `../migrations/` · transaksiyon → `../transactions/`.
> Kök çapraz kanıt tablosu: `../data-dogrulama.md`.

## 1. İddia Tablosu

| # | İddia | Kanıt (dosya:satır / URL) | Durum |
|---|---|---|---|
| SÇ-01 | 18 veritabanı BCNF; 171 tablo | `.ai/.sql/CLAUDE.md §1` | ✓ |
| SÇ-02 | LIKE sorgusu uygulama kodundan tespit edilir | `.ai/.sql/CLAUDE.md §1` | ✓ |
| SÇ-03 | Anahtarsız SELECT/DELETE/UPDATE yasak | `.ai/.sql/CLAUDE.md §1` | ✓ |
| SÇ-04 | `text` alanı yok; `LIMIT`'li sorgu zorunlu | `.ai/.sql/CLAUDE.md §1` | ✓ |
| SÇ-05 | JOIN varsa alternatif (JSON aggregate/ORM map) | `.ai/.sql/CLAUDE.md §1` | ✓ |
| SÇ-06 | 5 çekirdek DB topolojisi | `docs/database/01-entity-relationship-diagram.md:13-16` | ✓ |
| SÇ-07 | ER diyagramı 30+ tabloyu kapsar | `docs/database/01-entity-relationship-diagram.md:22` | ✓ |
| SÇ-08 | MariaDB 11.4 blog için hazır (kod hazır) | `docs/database/01-entity-relationship-diagram.md:35` | ✓ |
| SÇ-09 | DSN charset parametreli; `utf8mb4` | `shared/src/Database/DatabaseManager.php:14-20` | ✓ |
| SÇ-10 | 4 PDO seçimi set edilir (`ERRMODE_EXCEPTION` dahil) | `shared/src/Database/DatabaseManager.php:22-27` | ✓ |
| SÇ-11 | FK yönetimi kararı "İncelenecek" | `docs/database/README.md:14-18` | ⚠️ |
| SÇ-12 | Markdown sözlük ↔ MySQL meta senkronu TODO | `docs/database/README.md:40` | ⚠️ |
| SÇ-13 | `oauth_connections` content+ads DB'ye eklendi | `shared/database/migrations/oauth_connections_migration.php:11` | ✓ |
| SÇ-14 | Çapkın (duplicate) kayıt engeli ilişkisel kısıt işidir | `docs/database/01-entity-relationship-diagram.md:30-31` | ✓ |

**Toplam:** 14 iddia · 11 ✓ · 3 ⚠️ · 0 `UNKNOWN`.

## 2. Kod + DB Çift Doğrulama (Uygulama ↔ Şema)

| Senaryo | Kod kanıtı | DB kanıtı | Uyum |
|---|---|---|---|
| Bağlantı | `DatabaseManager.php:14-20` DSN | MySQL 8.4 `utf8mb4` varsayılan (dev.mysql.com) | ✓ |
| Yazma (prepare/execute) | `DatabaseManager.php:30-42` | parametreli sorgu kanunu (`.ai/.sql/CLAUDE.md §1`) | ✓ |
| Sorgu yüzeyi (repository) | `ApiKeyRepository.php:40-41` sabit `SELECT_COLUMNS` | PK'lı `api_keys` tablosu | ✓ |
| Çoğaltma (insert) | `oauth_connections_migration.php:14-41` parametreli `INSERT` | 4 sütunlu tablo (`14` satır başlık) | ✓ |
| Test yüzeyi | `shared/tests/Repository/Sqlite/*Test.php` (çiftler) | SQLite şema sürümleme (`Database/Sqlite/Schema.php`) | `⚠️ TASARIM` |

## 3. Normalizasyon Yargısı

| İddia | Kanıt | Yargı |
|---|---|---|
| Hedef BCNF | `.ai/.sql/CLAUDE.md §1` (BCNF) | ✓ kanun |
| 3NF delilleri: forum `max(userid)` (T-20), test `SELECT *` (T-11) | `K007-data/data-dogrulama.md` M-07/T-06 | `⚠️ VERIFICATION REQUIRED` — kök dosyaya bak |
| Çapraz-DB denetim (coremusicnet ↔ ads ↔ ml) | `docs/database/README.md:37` (aşama 5) | ⚠️ doğrulanmadı |

## 4. Sheme ↔ Kod Drift Riski

| Risk | Kanıt | Etki |
|---|---|---|
| Markdown sözlük elle senkron (R-06 kök) | `docs/database/README.md:40` | doküman/DB uyuşmazlığı |
| FK kararı belirsiz (R-09 kök) | `docs/database/README.md:14-18` | veri bütünlüğü uygulaması |
| `information_schema` raporları arşivli, içeriği `UNKNOWN` | `.ai/.sql/mysql/reports/` (`git status: D` 2026-10-12) | şema-drift ölçümü eksik |

## 5. Test İlişkisi

| Kök test | Kapsam |
|---|---|
| `T-06 — Test katmanı: Oracle→SQLite DB çiftleri` | şema çifti (SQLite) doğrulama |
| `T-09 — test altyapısı envanteri (44 dosya)` | repository testleri şemayı örtüyor |
| `T-11 — single source of truth: test kataloğu ↔ belge` | forum `SELECT *` iddiası |

> Komut/test çalıştırma `qa-engineer` alanında (root `data-test-stratejisi.md`).

## 6. Bilinen Boşluklar

1. **Forum şeması** — T-11/T-20 (K001) `⚠️ VERIFICATION_REQUIRED` (`.ai/CLAUDE.md §6 LINT`).
2. **Çapraz-DB tutarlılık** — `docs/database/README.md:37` "DOĞRULANACAK".
3. **`novasearch` şeması** — bu belgede `UNKNOWN` (içerik `.ai/.sql/mysql/novasearch.sql`;
   okunmadı, bkz. S-1/K-06).

## 7. Doğrulama Yöntemi

1. İddia satırı → kaynak (dosya:satır veya URL) → durum işareti.
2. `⚠️` = eksik/doğrulanmamış kanıt; `UNKNOWN` = kanıt yok.
3. Yeniden ölçüm: `.ai/.sql/mysql/reports/` (mevcut değilse `UNKNOWN`).
4. Nihai kapı: root `../data-dogrulama.md` §4–5 (LINT + doğrulama kapıları).

## 8. Yeniden Ölçüm Protokolü

| Adım | Komut/Yöntem | Beklenen |
|---|---|---|
| 1 | `.ai/.sql/mysql/reports/` var mı? | yoksa `UNKNOWN` (drift yok) |
| 2 | `information_schema.TABLES` sayımı (DB başı) | 171 tablo toplamı (`.ai/.sql/CLAUDE.md §1`) |
| 3 | `information_schema.COLUMNS` → `VARCHAR` sınırsız tarama | 0 satır hedef |
| 4 | Uygulama kodu taraması: `LIKE '%` | 0 isabet (kanun `.ai/.sql/CLAUDE.md §1`) |
| 5 | `SELECT *` taraması (`shared/src/` + subdomain src) | 0 isabet (T-11) |
| 6 | Markdown sözlük ↔ dump sütun karşılaştırma | drift raporu (`README.md:40` TODO) |

> Adım 2-5 `qa-engineer` alanı (root `../data-test-stratejisi.md`); bu dosya
> yalnız **id dia + kanıt** ilişkisini taşır.

## 9. İddia Kırılımı (Alt Konular)

### 9.1 Kısıtlar

| İddia | Kanıt | Durum |
|---|---|---|
| FK depo katmanında (InnoDB) | dev.mysql.com #1 (schema-web-arastirma) | ✓ (kaynak) |
| Proje FK tanımlı mı? | `docs/database/README.md:14-18` "İncelenecek" | ⚠️ |
| CHECK kullanımı | dump okunmadı | `UNKNOWN` |

### 9.2 Charset

| İddia | Kanıt | Durum |
|---|---|---|
| DSN charset parametreli | `DatabaseManager.php:14-20` | ✓ |
| Tablo charset'i `utf8mb4` | `oauth_connections_migration.php:23-24` (1 tablo) | ✓ (kısmi) |
| 171 tablo tamamı `utf8mb4` | dump okunmadı | `UNKNOWN` |

### 9.3 PK / Sınırsız Alan

| İddia | Kanıt | Durum |
|---|---|---|
| PK tablo başına 1 (kural) | dev.mysql.com #2/#11 | ✓ (kaynak) |
| 171 tabloda PK var | rapor `UNKNOWN` | `UNKNOWN` |
| Sınırsız `VARCHAR` yok | tarama protokolü §8.3 | `UNKNOWN` (ölçülmedi) |

## 10. Kanıt Zayıflıkları

| Zayıflık | Etki | Azaltma |
|---|---|---|
| 1 tablo üzerinden charset genellemesi | kanıt kapsamı dar | dump taraması (§8.3) |
| Markdown doküman = ikincil kanıt | senkron riski (R-06) | dump ↔ doküman drift (§8.6) |
| `.ai/.sql/mysql/reports/` içeriği `UNKNOWN` | drift ölçümü yok | raporları üret/erisim ver |
| Forum şeması K001'e bağlı | T-11/T-20 `⚠️` | K001 doğrulaması |

## 11. Doğrulama Geçmişi

| Tarih | Ne doğrulandı | Araç |
|---|---|---|
| 2026-10-07 | 18 BCNF DB / 171 tablo (kanun kaydı) | `.ai/.sql/CLAUDE.md §1` |
| 2026-10-10 | 5 DB / 30+ tablo ER | `docs/database/01-entity-relationship-diagram.md` |
| 2026-10-12 | kod satır kanıtları (DatabaseManager, migration, Repository) | dosya okuma |
| 2026-10-12 | 11 web kaynağı erişilebilirliği | `webfetch` |

## 12. Bu Alt Alanın Kök Kapılarıyla İlişkisi

| Kök kapı (`../data-dogrulama.md`) | Şema karşılığı |
|---|---|
| §4 — LINT kontrolleri | bu dosya §1 (iddia satırları) |
| §5 — doğrulama kapıları | bu dosya §8 (yeniden ölçüm) |
| M-07 (`max(userid)` T-20) | §3 normalizasyon yargısı |
| T-06/T-09/T-11 | §5 test ilişkisi |

> ADR-099 "Doğrulama 5 kapı" bu alt alan için §8 protokolüne indirgenmiştir;
> kapı 5 (LINT) root'ta kalır.

## 13. Sorgu Sözlüğü İddiaları

| # | İddia | Kanıt | Durum |
|---|---|---|---|
| SÇ-15 | Sorgu sözlüğü 6+ sorgu içerir | `docs/database/README.md` (dizin kaydı) | ⚠️ sayı okunmadı |
| SÇ-16 | DB sözlüğü mevcut | `docs/database/README.md:9-13` | ✓ |
| SÇ-17 | Forum şeması "inceleme bekliyor" (K001) | K001 kök kaydı | ⚠️ |

## 14. Bu Dosyanın Kapsam Sınırı

- **Yapılan:** iddia toplama + kaynak eşleştirme + yeniden ölçüm protokolü.
- **Yapılmayan:** gerçek sorgu çalıştırma (DB erişimi yok), rapor üretimi
  (`.ai/.sql/mysql/reports/` yazımı), dump okuma (S-B03 `UNKNOWN`).
- Bunlar `qa-engineer` (test) ve `data-engineer` (rapor) alanındadır.

## 15. İddia Sayaç Özeti

| İşaret | Adet | Satırlar |
|---|---|---|
| ✓ | 13 | SÇ-01..SÇ-10, SÇ-13, SÇ-14, SÇ-16 |
| ⚠️ | 4 | SÇ-11, SÇ-12, SÇ-15, SÇ-17 |
| `UNKNOWN` | 6 | §2 test satırı, §3 çapraz-DB, §9.1 CHECK, §9.2 171 tablo charset, §9.3 PK/sınırsız, §6.3 drift |
| Toplam iddia | 17 | §1 (14) + §13 (3) |

## 16. Bağlantılar

- **Yukarı:** `../data-dogrulama.md` (kök iddia tablosu) · `index.md`.
- **Kardeş:** `schema-kapsam` (checklist §10) · `schema-web-arastirma`
  (URL kanıtları) · `schema-ornekler` (kod kanıtları).
- **Dış:** `.ai/.sql/CLAUDE.md §1` · `qa-engineer` (root T-01..T-10).
