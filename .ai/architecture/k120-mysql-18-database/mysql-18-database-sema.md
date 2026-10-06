---
title: "MySQL 18 Database Şeması - k120-mysql-18-database"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k120-k131)"
updated: 2026-10-06
---

# 120. MySQL 18 Database Şeması - `k120-mysql-18-database`

> Dilim: D04 (k120-k131) · Klasör no: 120 · Dosya 1/2 · Sürüm: 4.0.0 · Tarih: 2026-10-06
> Sorumlu persona: `data-engineer` (şema/BCNF) · `backend-architect` (erişim sınırı) · `database-optimizer` (indeks/performans)
> Kapsadığı MD: 2 (1 içerik + index) · Kanıtlar: satır içi `Kanıt:` + disk taraması ya da `⚠️ VERIFICATION REQUIRED`

## 1. Genel Bakış

Bu belge, CoreMusic'in **18 BCNF çekirdek veritabanının şema otoritesini** tanımlar: hangi kaynak esastır,
tablo/sütun konvansiyonları nelerdir, normalizasyon nasıl denetlenir ve uygulama katmanı bu şemaya nasıl bağlanır.
Belge **tasarım + konvansiyon** belgesidir; çalışan bir kurulum, ölçüm raporu veya performans rakamı **iddia etmez**.

**Doğrulanmış disk tabanı:** `.ai/.sql/mysql/` altında 19 `.sql` dosyası vardır; bunlardan 18'i `coremusic_*.sql`
çekirdek şemasıdır ve içlerinde toplam **156 `CREATE TABLE`** vardır (`media_catalog.sql` ayrı şemadır, +9 tablo).
Sayım bu çalıştırıldığında diskten alınmıştır (aşağıda `## Disk Kanıt Envanteri`).

**Katman bağımlılığı:** K0 (süreç/dosya), K1 (disk/bellek) altyapıdır; K5 içindeki `k121` (kayıt), `k122`
(migration), `k127` (indeks/sorgu) ile doğrudan; K6 (güvenlik/erişim), K8 (servis), K9 (API) ile dolaylı.
K4 (yapay zeka) bu şemadan okur, K7 (kuyruk) olay üretir.

**Sorumluluk sınırı:** Şema dosyaları **tasarımdır ve diskte DDL'dir**; üretim sunucusunda uygulanıp uygulanmadığı,
çalışan MySQL sunucusunun sürümü ve gerçek trafik ölçümü **görülmemiştir**.

## 2. Klasör Özeti (K Tablosu)

| Numara | Ad | Amaç | Bağımlılık | Sorumlu persona | Kanıt | MD |
|---|---|---|---|---|---|---|
| 1 | MySQL 18 DB Şeması | 18 BCNF çekirdek şemanın otoritesi, DDL konvansiyonu, BCNF denetimi, PDO erişim sınırı | K0/K1 (altyapı), k121 kayıt, k122 migration, k127 indeks, K6 erişim, K8/K9 tüketim | data-engineer, backend-architect, database-optimizer | `Kanıt: .ai/.sql/mysql/*.sql (18 dosya · 156 CREATE TABLE)` | [[mysql-18-database-sema.md]] |
| 2 | Klasör Özeti | D04 komşu bağlantıları, çelişki kayıtları, kanıt envanteri | tüm D04 | data-engineer | `Kanıt: .ai/architecture/k120-mysql-18-database/index.md` | [[index.md]] |

## 3. Şema Otoritesi ve Kaynak Sıralaması

Çok sayıda kaynak aynı konuda farklı şey söylüyor. Bu klasörde **kaynak öncelik sırası** sabittir:

| Öncelik | Kaynak | Ne verir | Güven | Not |
|---|---|---|---|---|
| 1 (esas) | `.ai/.sql/mysql/*.sql` | gerçek DDL: tablo, sütun, indeks, ENGINE | en yüksek (dosya var) | bu belgenin sayımları buradan |
| 2 | `.ai/.decisions/accepted/ADR-002/003/040` | karar kısıtları (PDO, multi-DB, otorite) | yüksek (dosyalar diskte) | karar metni okunmadan uygulanmaz |
| 3 | `shared/src/Database/*.php` | erişim katmanı davranışı | yüksek (kod diskte) | PDO seçimi, transaction API |
| 4 | `_backup/.../k5-veri-yonetimi/README.md` | §1.1 18 DB iddia tablosu, §2 BCNF kuralları | orta (iddia) | §1.1 adları DDL ile **çelişir** |
| 5 | `_backup/.../k5-veri-yonetimi/mysql-18-database.md` | §Şema/§Partition/§View/§Index/§Query/§Read Replicas | orta (tasarım) | `coremusic_users`/`coremusic_music` adları diskte YOK |
| 6 | `_backup/.../k5-veri-yonetimi/index.md` | katman özeti, metrikler | düşük (eski v1.0.1) | 18 DB ad listesi README ile de çelişir |
| 7 | `.ai/CLAUDE.md`, `.ai/brain.md`, `AGENTS.md` | katman/kural özeti | düşük (özet) | "MySQL 9", "112 tablo" iddiaları |

**Kural:** öncelik 1-3 ile çelişen her iddia `⚠️ VERIFICATION REQUIRED` işaretlenir; sayılar tahmin edilmez.
Kaynak önceliği bu dosyanın `## Sürüm ve Çelişki Kayıtları` bölümündeki C-01..C-06 kayıtlarıyla birlikte okunur.

| # | Karar | Kaynak | Durum |
|---|---|---|---|
| A | PDO mandatory, ORM yasak | `.ai/.decisions/accepted/ADR-002-pdo-mandatory-no-orm.md` | dosya diskte var |
| B | Multi-DB BCNF izolasyonu | `.ai/.decisions/accepted/ADR-003-multi-db-bcnf.md` | dosya diskte var |
| C | Veritabanı otoritesi (18 BCNF) | `.ai/.decisions/accepted/ADR-040-database-authority.md` | dosya diskte var |
| D | Migration stratejisi | `.ai/.decisions/accepted/ADR-014-multi-db-migration-strategy.md` | dosya diskte var; bkz. `[[../k122-migration-strategy/index.md]]` |

## 4. 18 Çekirdek Veritabanı Kapsamı

Aşağıdaki tablo **diskten sayım**tır (sütun/indeks sayıları DDL ayrıştırılarak üretildi);
"README §1.1 amacı" sütunu ise `README.md` §1.1'in **iddiasıdır** (aktarım, doğrulama değil).

| # | Veritabanı | Dosya | Tablo | Sütun | İndeks | README §1.1 amacı (iddia) |
|---|---|---|---|---|---|---|
| 1 | `coremusic_ai` | `coremusic_ai.sql` | 6 | 70 | 22 | AI/öneri tercihleri |
| 2 | `coremusic_albums` | `coremusic_albums.sql` | 5 | 49 | 5 | albüm koleksiyonu |
| 3 | `coremusic_api` | `coremusic_api.sql` | 4 | 45 | 23 | API anahtarı/rate limit |
| 4 | `coremusic_auth` | `coremusic_auth.sql` | 13 | 159 | 24 | kullanıcı, rol, oturum, token |
| 5 | `coremusic_catalog` | `coremusic_catalog.sql` | 8 | 75 | 26 | referans veriler |
| 6 | `coremusic_cms` | `coremusic_cms.sql` | 8 | 85 | 33 | sayfa/blog/etiket |
| 7 | `coremusic_download` | `coremusic_download.sql` | 4 | 39 | 24 | indirme kuyruğu/geçmişi |
| 8 | `coremusic_logs` | `coremusic_logs.sql` | 22 | 239 | 117 | denetim izi, analitik |
| 9 | `coremusic_media` | `coremusic_media.sql` | 8 | 93 | 37 | cihaz senkronu, medya metadata |
| 10 | `coremusic_musics` | `coremusic_musics.sql` | 22 | 267 | 52 | şarkı/sanatçı/tür/lyrics |
| 11 | `coremusic_neva` | `coremusic_neva.sql` | 4 | 45 | 24 | EQ preset/DSP ayarları |
| 12 | `coremusic_patch` | `coremusic_patch.sql` | 3 | 30 | 14 | şema sürümü/migration kaydı |
| 13 | `coremusic_playlist` | `coremusic_playlist.sql` | 5 | 53 | 5 | playlist/işbirliği |
| 14 | `coremusic_social` | `coremusic_social.sql` | 9 | 99 | 47 | yorum/paylaşım/aktivite |
| 15 | `coremusic_studio` | `coremusic_studio.sql` | 6 | 70 | 22 | stüdyo oturumu/parça |
| 16 | `coremusic_system` | `coremusic_system.sql` | 17 | 211 | 76 | ayar/konfigürasyon/önbellek |
| 17 | `coremusic_user` | `coremusic_user.sql` | 7 | 74 | 7 | profil/tercih/geçmiş |
| 18 | `coremusic_wireless` | `coremusic_wireless.sql` | 5 | 76 | 27 | WiFi + Bluetooth ağları |
| | **Çekirdek toplam** | 18 dosya | **156** | **1779** | **585** | README §1.1: 156 tablo |
| + | `media_catalog` (ayrı şema) | `media_catalog.sql` | 9 | 124 | 38 | `-- Version: 1.0.0 (forward-only - rollback yok)` |

**Çapraz kontrol:**
- README §1.1 "156 tablo" iddiası çekirdek sayım ile **uyumludur** (156 = 156).
- `.ai/CLAUDE.md` L109 "112 tablo" iddiası sayımla **uymaz** → `⚠️ VERIFICATION REQUIRED` (C-02).
- `.ai/.sql/mysql/media_catalog.sql` 18 çekirdek DB arasında değildir; ilan edilmiş bir ADR kapsamı için
  `⚠️ VERIFICATION REQUIRED` (dosya yorumu `ADR-040` anıyor, karar metni ayrıca okunmalıdır).

## 5. DDL Konvansiyonları

### 5.1 Şema oluşturma (diskteki gerçek desen)

```sql
-- kaynak: .ai/.sql/mysql/coremusic_cms.sql L15-L19
CREATE DATABASE IF NOT EXISTS coremusic_cms
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE coremusic_cms;
```

| Kural | Değer | Kanıt |
|---|---|---|
| Charset | `utf8mb4` | 18 dosyanın başlık yorumu (`-- Charset : utf8mb4` / `charset: utf8mb4_unicode_ci`) |
| Collation | `utf8mb4_unicode_ci` (kimi dosyada `utf8mb4_tr_0900_ai_ci` iddiası) | `media_catalog.sql` başlık yorumu |
| ENGINE | `InnoDB` | `-- Engine : InnoDB` başlıkları + tablo sonları |
| Tablo adı | snake_case, çoğul | tüm DDL dosyalarında tutarlı |
| Sütun adı | snake_case | tüm DDL dosyalarında tutarlı |
| Zaman damgası | `created_at`, `updated_at` (`ON UPDATE CURRENT_TIMESTAMP`) | örn. `coremusic_cms.sql` `cms_pages` |
| Soft delete | `is_deleted` (+ `deleted_at`) | 18 dosyanın başlık yorumu `-- Soft Delete : ...` |
| Birincil anahtar | `id INT/BIGINT UNSIGNED NOT NULL AUTO_INCREMENT` | tüm çekirdek dosyalar |
| Özel durum | `coremusic_download.sql`: `-- Soft Delete: Yok (download logları kalıcı)` | başlık yorumu |
| Özel durum | `coremusic_neva.sql`: `-- Soft Delete : is_deleted + deleted_at (spectrum_analysis hariç)` | başlık yorumu |

### 5.2 Tablo iskeleti (kural)

```sql
CREATE TABLE <tablo_adi> (
    id              INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    ... alanlar ...
    is_deleted      TINYINT(1)     NOT NULL DEFAULT 0,
    created_at      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP
                                    ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY <kural_adi> (<aday_anahar>),
    KEY <idx_adi> (<sütun>)
) ENGINE=InnoDB;
```

### 5.3 Yasaklı desenler (uygulama kodu içindir; şema bunu zorlar)

| Yasak | Neden | Şema karşılığı |
|---|---|---|
| `SELECT *` | açık sütun listesi | sütun envanteri (aşağıda ve `## Disk Kanıt Envanteri`) |
| ORM | bağımlılık (ADR-002) | DDL + PDO |
| Soft delete'siz silme | veri kaybı | `is_deleted` kolonu 18 şemada mevcut |
| Çapraz DB `FOREIGN KEY` | izolasyon (ADR-003) | FK yalnız tablo içinde (bkz. §7) |
| `ENUM` keyfi genişletme | migration gerekir | `[[../k122-migration-strategy/index.md]]` |

### 5.4 Çekirdek dosyanın başlık sözleşmesi

Her çekirdek `.sql` dosyasının ilk 30 satırında şu başlık alanları vardır (dosyadan okunur):
`-- Version` (şema sürümü), `-- BCNF`, `-- Tables` (yalnız 6 dosyada), `-- Engine`, `-- Charset`, `-- Soft Delete`.
`-- Tables` iddiası ile gerçek `CREATE TABLE` sayımı karşılaştırıldığında **iki uyumsuzluk** görülür:
`coremusic_logs.sql` iddia 17 / sayım 22; `coremusic_musics.sql` iddia 22 / sayım 22 (uyumlu, kırılma notu ile).
Ayrıntı `## Disk Kanıt Envanteri` → `### SQL Dosya Başlıkları` tablosundadır.

## 6. BCNF Denetimi

| # | Kontrol | Nasıl denetlenir | Beklenen | Kanıt/ durum |
|---|---|---|---|---|
| 1 | Her tabloda birincil anahtar var mı | DDL `PRIMARY KEY` sayısı = tablo sayısı | 156/156 | sayım `.ai/.sql/mysql/*.sql` |
| 2 | Aday anahtar tanımlı mı | `UNIQUE KEY` / başlık `-- BCNF` yorumu | her normalizationed şemada | başlık yorumları `BCNF : Yes` / `BCNF Normalized` |
| 3 | İşlevsel bağımlılık ihlali yok mu (BCNF) | aday anahtar dışı belirleyici yok | ihlal yok | **manuel denetim gerekir** → `⚠️ VERIFICATION REQUIRED` (otomatik denetim betiği diskte yok) |
| 4 | Soft delete kolonu var mı | `is_deleted` sütun varlığı | var (2 dosyada istisna başlıkta) | başlık yorumları |
| 5 | Zaman damgaları var mı | `created_at`/`updated_at` | var | DDL |
| 6 | snake_case mi | ad denetimi | evet | DDL |
| 7 | Çapraz DB referans var mı | `REFERENCES <db>.<tablo>` deseni | yok | `Kanıt: SELECT-String 'REFERENCES' ile denetlendi` |

**Denetim adımları (salt-okunur):**

1. Tablo sayısı: `Select-String -Path .ai\.sql\mysql\*.sql -Pattern 'CREATE TABLE' | Group-Object Filename`
2. PK kapsamı: her dosyada `PRIMARY KEY` geçen satır sayısını tablo sayısıyla karşılaştır.
3. FK sınırları: `Select-String -Path .ai\.sql\mysql\*.sql -Pattern 'FOREIGN KEY|REFERENCES'`
4. Soft delete: `Select-String -Path .ai\.sql\mysql\*.sql -Pattern 'is_deleted' | Group-Object Filename`
5. Çıktıları bu belgeye `Kanıt:` satırı olarak ekle; fark varsa C-02/C-03 kayıtlarına işle.

## 7. İlişki ve Yabancı Anahtar Sınırları

| Kural | Tanım | Kaynak |
|---|---|---|
| İntra-tablo FK | `FOREIGN KEY (x) REFERENCES <aynı_db>.<tablo>(id) ON DELETE CASCADE` | örn. `coremusic_cms.sql` `user_settings` benzeri kalıp |
| Çapraz DB FK | **yasak** — MySQL zaten şema sınırını aşan FK'yu bu düzende desteklemez | ADR-003 (izolasyon) |
| Çapraz DB okuma | uygulama katmanında iki sorgu = iki bağlantı (bkz. `[[../k128-connection-pool/index.md]]`) | tasarım |
| Uygulama düzeyinde bütünlük | transaction ile birden fazla DB'ye yazım | `shared/src/Database/DatabaseManager.php` L51-L61 |
| Uygulama düzeyinde bütünlük sınırlı | tek transaction tek `PDO` = tek DB (DSN `dbname=%s`) | `DatabaseManager.php` L15-L26 |

**Önemli sınır:** `DatabaseManager` tek bir `dbname` ile tek `PDO` kurar; iki DB'ye atomik yazım için
**iki bağlantı + dış koordinasyon** gerekir. Bu sınır `Kanıt: shared/src/Database/DatabaseManager.php L15-L26`
ile doğrulanmıştır; koordinasyon tasarımı bu klasörün **kapsam dışıdır** → `⚠️ VERIFICATION REQUIRED`.

## 8. Erişim Katmanı (PDO) — Diskteki Gerçeğ

| Satır | Kod | Anlam |
|---|---|---|
| L10 | `private \PDO $pdo;` | tekil PDO alanı |
| L15 | `'mysql:host=%s;port=%d;dbname=%s;charset=%s'` | DSN şablonu |
| L22-L26 | `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, `EMULATE_PREPARES => false`, `ATTR_PERSISTENT => true` | hata/fetch/hazırlık kalıcı bağlantı |
| L32 | `$stmt = $this->pdo->prepare($sql);` | prepared statement zorunlu |
| L51-L61 | `beginTransaction()` / `commit()` / `rollBack()` | transaction API |
| L64 | `getPdo(): \PDO` | ham erişim (sınırlı kullanım) |

Kodun tamamı `shared/src/Database/DatabaseManager.php` (56 satır, sürüm/dosya satır sayısı envanterde).
`DatabaseRegistry.php` (27 satır), `DatabaseConfig.php` (13), `IDatabaseManager.php` (11),
`IDatabaseRegistry.php` (8) dosyaları kayıt/erişim sözleşmesini verir → `[[../k121-database-registry/index.md]]`.

**Sürüm notu:** `ATTR_PERSISTENT => true` kalıcı bağlantı kullanır; havuz sınıfı diskte yoktur
(bkz. `[[../k128-connection-pool/index.md]]`). MySQL sunucu sürümü bu dosyadan **çıkarılamaz** → `[UNKNOWN]` (C-01).

## 9. Kenar Durumlar

| # | Kenar durum | Tetikleyici | Davranış/etki | Aksiyon |
|---|---|---|---|---|
| E-01 | Şema adı çelişkisi | backup `index.md` vs README vs DDL adları | yanlış DB'ye bağlanma | DDL dosya adları esas; C-03 |
| E-02 | Tablo sayısı çelişkisi | 156 (sayım) vs 112 (CLAUDE L109) | kapasite planı bozulur | sayım esas; C-02 |
| E-03 | Sürüm bilinmezliği | MySQL `[UNKNOWN]` | özellik iddiası yazılamaz | C-01; sunucu `SELECT VERSION()` gerekir |
| E-04 | `ENUM` değeri değişikliği | yeni durum ekleme | şema migration gerektirir | `[[../k122-migration-strategy/index.md]]` |
| E-05 | `media_catalog` şeması | 18 çekirdek DB dışı | kapsam belirsiz | `⚠️ VERIFICATION REQUIRED` |
| E-06 | Soft delete istisnaları | `download` logları kalıcı | silme sorgularında sızıntı | başlık yorumunu sorguya yansıt |
| E-07 | `-- Tables` başlık hatası | `logs` iddia 17 / gerçek 22 | otomatik araç yanlış sonuç verir | DDL sayımı esas |
| E-08 | Çapraz DB transaction | iki DB'ye atomik yazım | PDO tek DB'ye bağlı | koordinasyon tasarımı yok → `⚠️ VERIFICATION REQUIRED` |
| E-09 | Charset/collation farkı | `utf8mb4_unicode_ci` vs `utf8mb4_tr_0900_ai_ci` iddiası | sıralama farkı | başlık okunarak teyit |
| E-10 | Kalıcı bağlantı ölü | sunucu `wait_timeout` | ilk sorgu hatası | `[[../k128-connection-pool/index.md]]` |
| E-11 | `SELECT *` sızıntısı | geniş sütunlu tablo | ağ/bellek yükü | açık sütun listesi |
| E-12 | Büyüyen `logs` tablosu | 22 tablo / 239 sütun | sorgu yavaşlaması | `[[../k127-query-optimizer/index.md]]` |

## 10. Hata Modları

| Kod | Belirti | Kök neden | Tespit | Düzeltme |
|---|---|---|---|---|
| D4201 | `Table 'x' doesn't exist` | yanlış DB adı / migration eksik | DSN `dbname` + DDL ad kontrolü | C-03 kaynaklı ad düzeltmesi; migration `k122` |
| D4202 | `Unknown column 'y'` | sütun eklenmemiş şema | `Kanıt:` sütun envanteri | migration ile sütun ekleme |
| D4203 | `SQLSTATE[HY000] [2002] connection refused` | sunucu/port yok | port 3306 denetimi (`.ai/CLAUDE.md` L314) | ortam/altyapı; `⚠️ VERIFICATION REQUIRED` |
| D4204 | `Lock wait timeout exceeded` | uzun transaction | `SHOW ENGINE INNODB STATUS` (komut diskte yok → tasarım) | transaction kısa tutma |
| D4205 | `Duplicate entry ... for key` | `UNIQUE KEY` ihlali | hangi `UNIQUE KEY` (envanterde) | uygulama doğrulaması + iddialı mesaj |
| D4206 | `Incorrect string value` | charset uyuşmazlığı | `charset=utf8mb4` DSN kontrolü | DSN/şema charset hizası |
| D4207 | `Foreign key constraint fails` | intra-tablo FK ihlali | `FOREIGN KEY` listesi | sıra/transaction; çapraz DB FK yasak |
| D4208 | `Too many connections` | havuz/tüketim | havuz metriği yok → `⚠️ VERIFICATION REQUIRED` | `[[../k128-connection-pool/index.md]]` |
| D4209 | `Query execution was interrupted` | timeout/uzun sorgu | yavaş sorgu izi | `[[../k127-query-optimizer/index.md]]` |
| D4210 | Sütun tipi taşması (`OUT OF RANGE`) | `TINYINT`/`INT` sınırı | sütun tanımı envanteri | migration ile genişletme |
| D4211 | Soft delete sızıntısı | `is_deleted` filtresi unutulmuş | sorgu denetimi | filtre zorunluluğu (G-05) |
| D4212 | Cross-DB join denemesi | şema sınırı | SQL incelemesi | uygulama düzeyinde iki sorgu |

## 11. Doğrulama Adımları

1. Dosya envanteri: `Get-ChildItem .ai\.sql\mysql\*.sql | Select-Object Name,Length`
2. Tablo sayımı: `Select-String -Path .ai\.sql\mysql\*.sql -Pattern 'CREATE TABLE' | Measure-Object | Select Count` (beklenen 165; çekirdek 156)
3. Çekirdek ayrımı: `coremusic_*.sql` 18 dosya sayısını doğrula (`(Get-ChildItem .ai\.sql\mysql\coremusic_*.sql).Count` → 18)
4. İddia karşılaştırması: `README.md` §1.1 (156) · `.ai/CLAUDE.md` L109 (112) · başlık `-- Tables` değerleri
5. PDO doğrulaması: `shared/src/Database/DatabaseManager.php` L10-L66 okunur; `ATTR_PERSISTENT` ve `prepare()` teyit edilir
6. Çelişki güncellemesi: yeni fark çıkarsa C-01..C-06 tablosuna satır ekle (seçim yapma, yalnız kayıt)
7. Markdown doğrulaması: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/k120-mysql-18-database/mysql-18-database-sema.md`

## 12. Kanıt Kaynakları

| Kanıt | Yol | Ne kanıtlıyor |
|---|---|---|
| `Kanıt:` | `.ai/.sql/mysql/*.sql` (19 dosya) | gerçek DDL, 165 `CREATE TABLE` (çekirdek 156), 1903 sütun, 623 indeks |
| `Kanıt:` | `.ai/.sql/mysql/coremusic_cms.sql` L15-L45 | `CREATE DATABASE`/charset/ENGINE/iskelet kalıbı |
| `Kanıt:` | `shared/src/Database/DatabaseManager.php` L10-L66 | PDO, DSN, `prepare`, transaction, `ATTR_PERSISTENT` |
| `Kanıt:` | `.ai/.decisions/accepted/ADR-002-pdo-mandatory-no-orm.md` | PDO zorunlu, ORM yasak |
| `Kanıt:` | `.ai/.decisions/accepted/ADR-003-multi-db-bcnf.md` | multi-DB BCNF izolasyonu |
| `Kanıt:` | `.ai/.decisions/accepted/ADR-040-database-authority.md` | veritabanı otoritesi kararı |
| `Kanıt:` | `_backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/README.md` §1.1-§2 | 156 iddiası, BCNF kuralları |
| `Kanıt:` | `_backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/mysql-18-database.md` §Şema-§Query | tasarım iddiaları (adlar diskle çelişir) |
| `⚠️ VERIFICATION REQUIRED` | MySQL sunucu sürümü | C-01 çelişkisi; sunucuda `SELECT VERSION()` çalıştırılmadı |
| `⚠️ VERIFICATION REQUIRED` | 112 tablo iddiası | `.ai/CLAUDE.md` L109 ile sayım uyuşmuyor (C-02) |
| `⚠️ VERIFICATION REQUIRED` | partition/view/read-replica uygulanmış mı | yalnız `_backup/.../mysql-18-database.md` iddiası; DDL'de `PARTITION BY`/`VIEW` kanıtı için ayrıca taranmalı |
| `⚠️ VERIFICATION REQUIRED` | üretimde uygulanmış şema | sunucu erişimi/ölçüm yok |

<!--BOOST-->
