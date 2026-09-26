---
title: "CoreMusic — ADR-040: Database Authority (18 BCNF sahiplik matrisi · tek yazar · cross-DB politikası · migration yetkisi)"
type: "architecture-decision"
category: "database"
date: "2026-09-26"
updated: "2026-09-26"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic veritabanı otoritesi: hangi veri hangi DB (18 satır), tek yazar servis kuralı, cross-DB FK politikası (varsayılan yasak + 28 istisna + fazlı temizlik) ve şema/migration tek kapısı"
kaynak: "Kullanıcı onaylı kapsam (tam: sahiplik + erişim + yetki) + disk/kod kanıtı taraması (18 SQL, 156 tablo, 82 FK) + web araştırması (5 sorgu / 25 kaynak)"
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-040: Database Authority (18 BCNF Otoritesi)

> **Durum:** ✅ **ACCEPTED** (kullanıcı onaylı kapsam) · **Tarih:** 2026-09-26 · **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** · **Tech Lead:** ✅ · **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `ADR-040-database-authority`
> **İlgili kararlar:** [[ADR-003-multi-db-bcnf]] (18 BCNF mimarisi + "DB arası FK YOK" kuralı — bu ADR otorite/matris işini devralır) · [[ADR-033-sql-normalization-strategy]] (156 tablo / 82 FK / 28 cross-DB bulguları) · [[ADR-014-multi-db-migration-strategy]] (özel PHP runner + DB başına bağımsız sequence) · [[ADR-002-pdo-mandatory-no-orm]] (PDO tekelı — erişim katmanı) · [[ADR-039-7-service-platform-architecture]] (11 servis — sahiplik matrisinin servis tarafı) · [[ADR-081-multi-provider-data-sync]] (outbox + WAL — cross-DB tutarlılık telafisi) · [[../index.md]] (`:82` slug satırı) · [[../../brain.md]] (`:1000` ADR-040 slotu, `:296` §11 18 BCNF tablosu)
> **Ad gerekçesi:** slug `ADR-040-database-authority` **diskteki gerçek index kaydından** alınmıştır (`[[../index.md]]:82`) — uydurulmadı. ADR-003 §5.1/7 debate şartı ("18 sayısal yetki ADR-040 envanter kararında tescil edilir") bu dosyada kapanır.
> **Frozen notu:** ADR-001-037 **dokunulmamıştır** (yalnız atıf). Bu dosya Active aralığındadır, frozen değildir.

---

## §1 Bağlam (Context)

### §1.1 Mevcut Durum (disk + kod kanıtı — dürüst etiket)

**A) Şema envanteri — IMPLEMENTED (yeniden tarandı, ADR-003/033 bulgularıyla birebir):**

| Kanıt | Değer | Etiket |
|---|---|---|
| `.ai/.sql/mysql/*.sql` dosya sayısı | **18** (`coremusic_ai` … `coremusic_wireless`) | **IMPLEMENTED** (disk) |
| `CREATE TABLE` toplamı | **156 tablo** (18 dosya, dosya-başına tablo: 6/5/4/13/8/8/4/22/8/22/4/3/5/9/6/17/7/5) | **IMPLEMENTED** (tarama) |
| `PRIMARY KEY` | **156/156 = %100** (PK'sız tablo 0) | **IMPLEMENTED** (tarama) |
| `FOREIGN KEY` satırı | **82** → **54 DB-içi + 28 cross-DB** | **IMPLEMENTED** (tarama; ADR-033 ile aynı) |
| İndeks taraması | **513** satır (desen: `INDEX`/`UNIQUE KEY`/`CREATE INDEX`) — ADR-033 **550** raporlar → **sayım deseni farkı, iki sayı da uydurulmadı** | ⚠️ **VERIFICATION REQUIRED** (hangi desen bağlayıcı) |
| `BCNF` self-declaration | dosya başlıklarında (`coremusic_auth.sql:3`, `coremusic_musics.sql:756`) — **iddiadır, denetim DEĞİL** | **PLANNED** (BCNF denetimi → ADR-033) |
| Cross-DB FK | **28 gerçek constraint / 3 dosya** (aşağıda B) | **IMPLEMENTED** (tarama) |
| Cross-DB FK'yı "desteklemiyor" diyen yorum | **15 satır** (`-- Note: ... cross-database FK not supported in MySQL`) | **IMPLEMENTED** (metin) — teknik iddiası ⚠️ (aşağıda E, C1) |

**B) Cross-DB FK gerçek listesi (28) — hedefe göre dağılım ve örnek satırlar:**

| Kaynak dosya | Adet | Satır listesi (disk kanıtı) |
|---|---|---|
| `coremusic_user.sql` | **11** | `:50, 83, 114, 115, 138, 160, 161, 188, 189, 223, 224` |
| `coremusic_social.sql` | **13** | `:47, 67, 91, 118, 119, 150, 151, 180, 204, 205, 235, 266, 267` |
| `coremusic_system.sql` | **4** | `:69, 100, 128, 225` |
| **TOPLAM** | **28** | 11 + 13 + 4 → hedef: `coremusic_auth.users` **23**, `coremusic_musics.musics` **5** |

Örnek ham satırlar (dosya:satır):

- `[[../../.sql/mysql/coremusic_social.sql]]`:47 → `CONSTRAINT fk_comments_user FOREIGN KEY (user_id) REFERENCES coremusic_auth.users(id) ON DELETE CASCADE ON UPDATE CASCADE`
- `[[../../.sql/mysql/coremusic_user.sql]]`:50 → `ADD CONSTRAINT fk_profiles_user FOREIGN KEY (user_id) REFERENCES coremusic_auth.users (id) ...`
- `[[../../.sql/mysql/coremusic_system.sql]]`:69 → `CONSTRAINT fk_eq_presets_user FOREIGN KEY (user_id) REFERENCES coremusic_auth.users(id) ON DELETE SET NULL ...`
- `[[../../.sql/mysql/coremusic_social.sql]]`:151 → `REFERENCES coremusic_musics.musics` (5 adetlik `musics` hedefi grubunun üyesi)

> **Doğrulama notu:** ADR-033 §1.1'deki satır listeleri (user 11 / social 13 / system 4) bu taramayla **birebir** doğrulanmıştır; sayı 28'dir, "ilk yazım 11 / tek dosya" düzeltmesi (ADR-033 notu) geçerlidir.

**C) Servis↔DB eşlemesi — yalnız kodda tespit edilebilen (geri kalanı uydurulmadı):**

| Servis (ADR-039) | DB | Kod kanıtı | Etiket |
|---|---|---|---|
| **auth** | `coremusic_auth` | `auth.coremusic.net/config/constants.php:37` (`DB_AUTH_NAME` default `coremusic_auth`) + `include/Container/AuthContainer.php:40-43` (`registerMySql('auth', ..., DB_AUTH_NAME)`) — repo genelinde **tek** `registerMySql` çağrısı | **IMPLEMENTED** |
| **home** | `coremusic_user` | `home.coremusic.net/config/constants.php:118` (`DB_HOME_NAME` default `coremusic_user`) — ancak 23 PHP dosyasında `PDO`/`DatabaseManager`/`registerMySql` **çağrısı 0** | ⚠️ **TANIM VAR, KULLANIM 0** |
| **shared (AI modülü)** | `coremusic_musics` | `shared/src/AI/AIWorkflow.php:124` → `'database' => 'coremusic_musics'` (`UPDATE tracks ...`) | **IMPLEMENTED** (yazım çağrısı) — ama `tracks` tablosu `coremusic_musics` şemasında **YOK** (C2) |
| **(shared migration)** | `coremusic_user` · `coremusic_social` | `shared/database/migrations/oauth_states_migration.php:31` (`REFERENCES coremusic_user.users`) · `oauth_connections_migration.php:4,41` ("coremusic_social veritabanına eklenir") | **IMPLEMENTED (içerik)** — runner **PLANNED** |
| **assets** | — | `assets.coremusic.net/` = **0 PHP dosyası** (statik) | **IMPLEMENTED (negatif kanıt): DB yok** |
| main · music · media · download · admin · studio · car · dev | ⚠️ | dizin **YOK** (ADR-039 §2.1) → kodda eşleştirilemez | **PLANNED** — `⚠️ VERIFICATION REQUIRED` |

→ **Dürüst toplam:** 18 DB'nin **4'ü** kodda adıyla geçiyor (`coremusic_auth`, `coremusic_user`, `coremusic_musics`, `coremusic_social`); **14'ü** için servis eşleşmesi **yok**. `shared/src/Database/CLAUDE.md:40` "DatabaseRegistry.php — 18 DB kaydı" der; kodda `registerMySql` **1 çağrı** → iddia-kod çelişkisi (C5).

**D) Migration yetkisi — ADR-014 bulguları geçerliliği korunarak yeniden doğrulandı:**

| Kanıt | Durum |
|---|---|
| `shared/database/migrations/` = 2 dosya (`$queries` dizisi döndüren script) + `CLAUDE.md` ("Forward-only migration (ADR-014). Geri migration yasak.") | **IMPLEMENTED (içerik + kural)** |
| Migration runner kodu (`MigrationRunner` / `bin/migrate.php` / `schema_migrations` yazan kod) | **PLANNED — kod 0** (ADR-014 §1.1) |
| Harici araç (Phinx / Doctrine) — 3 `composer.json` taraması | **0 → kurulu değil (IMPLEMENTED negatif kanıt)** |
| `[[../../.sql/mysql/coremusic_patch.sql]]`:14 `schema_versions` + `:35` `migration_log` + `:57` `patches` | **IMPLEMENTED (şema)** |
| `[[../../.sql/mysql/coremusic_system.sql]]`:301-318 `system_schema_versions` (`uk_schema_versions_db_version` = DB-başına versiyon) | **IMPLEMENTED (şema)** |
| `.github/workflows/` | **2 dosya**: `ci.yml` (PHP lint + PHPUnit + composer audit) · `secret-scan.yml` (GitLeaks) → **SQL/cross-DB FK kapısı YOK** |

**E) Çelişki defteri (Truth Mode — hiçbiri yumuşatılmadı):**

| # | Bulgu | Etiket |
|---|---|---|
| **C1** | 15 yorum satırı "cross-database FK not supported in MySQL" ↔ resmi MySQL 8.4 Ref. §15.1.20.5 ("foreign_key_checks disabled … permitted to drop a database that contains tables with foreign keys that are referenced by tables **outside the database**") → sunucu-içi cross-DB referansının mümkün olduğunu ima eder | ⚠️ **VERIFICATION REQUIRED** (canlı MySQL'de test edilmedi) — teknik yeterlilik iddiası bu ADR'ye bağlanmaz |
| **C2** | `AIWorkflow.php:124` `UPDATE tracks` → `coremusic_musics` şemasında `tracks` tablosu **yok** (22 tablo: `musics`, `artists`, …) | ⚠️ **VERIFICATION REQUIRED** (çalışma zamanı davranışı) |
| **C3** | `DB_HOME_NAME` tanımı var, kullanım 0 (C tablosu) | dürüst etiket: **tanım ≠ erişim** |
| **C4** | [[ADR-039-7-service-platform-architecture]] §1.1-C ".github/workflows = **0 dosya**" ↔ diskte **2 workflow** (2026-09-24) | iddia-kod çelişkisi → ADR-039 §5.1 adım 4 kapsamı |
| **C5** | `shared/src/Database/CLAUDE.md:40` "18 DB kaydı" ↔ kodda 1 `registerMySql` çağrısı | iddia-kod çelişkisi |
| **C6** | `.ai/.agents/data-engineer.md` "2 Phinx migration" ↔ Phinx composer'da yok (ADR-014 §1.1'de düzeltilmiş) | ✅ ADR-014 kapattı, tekrar açılmaz |

### §1.2 Sorun Tanımı (Problem)

1. **"Hangi veri hangi DB'de" ile "o DB'yi kim yazar" ayrı dosyalarda:** ADR-003 §2.2-a şema listesini, ADR-039 §2.1 servis listesini veriyor; **18 DB ↔ 11 servis eşlemesi hiçbir yerde tek tabloda yok** → §5.1/5 (ADR-039) "veri sahipliği dağılımı" hâlâ PLANNED.
2. **Cross-DB FK iki politika olarak yaşıyor:** ADR-003 "DB arası FK YOKTUR + CI kapısı RED" diyor; şemada **28** çapraz constraint ve **15** "desteklenmiyor" yorumu var → hangisinin geçerli olduğu yazılmadığı için yenisi sessizce eklenebilir.
3. **Şema/migration tekel tanımsız:** runner kodu 0, Phinx yok, `schema_versions`/`system_schema_versions` tabloları var ama **kim, hangi kapıdan, hangi sırayla** şema değiştirir yazılmamış → herkes "makul" DDL yazabilir.
4. **Erişim/yetki karışığı:** `DatabaseRegistry` "çoklu DB kaydı" olarak sunuluyor ama tek çağrı var; bir servisin başka DB'ye doğrudan yazması bugün **engellenmiyor** (kapı yok) → kural yazılı, müfettiş yok.

### §1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: [[../../../.claude/skills/prompt-maker/references/10-web-research-protocol.md]] (v7.2.0 — resmi/üretici kaynağı önce, her ana iddia ≥2 çapraz kaynak, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`).
> **Araç notu:** 4 `websearch` sorgusu + 1 resmi manuel çekimi (MySQL 8.4 Ref. §15.1.20.5 sayfasının tam metni) çalıştırıldı; sorgu alanları **konu bazlı** verilmiştir (birebir metin dökümü saklanmadı).

| Alan | Değer |
|------|-------|
| Web Search **Query** | `1)` data ownership / domain-driven design — tek yazar (single-writer) sahiplik 2025-26 · `2)` database per service pattern — servis kendi verisini sahiplenir, şema + migration dahil · `3)` cross-database foreign key anti-pattern (MySQL sunucu-içi referans + hata 1215) · `4)` schema ownership — şema/migration yetkisi kime ait · `5)` migration authority + database change management CI gate (2025-26) |
| Web Search **Konusu** | Veri sahipliğinin tanımı (domain-driven), database-per-service, cross-DB FK'nın statüsü ve bedeli, şema sahipliği, migration yetkisi ve CI kapısı |
| Web Search **Bağlam** | ADR-040 kapsamı: 18 DB sahiplik matrisi, tek yazar kuralı, cross-DB politikası (yasadışılık + 28 istisna + fazlı temizlik), şema/migration tek kapısının (ADR-014 runner) gerekçelendirilmesi |
| Web Search **Kısa Açıklama** | **(1)** Veri sahipliği = **tek yazıcılı otorite**: servis/takım veriyi, şemayı, yazma yolunu ve migration'ı kontrol eder; **(2)** database-per-service bunun kurumsal biçimidir (özel veri deposu, cross-service join/direct access yok); **(3)** MySQL'de çapraz-DB FK **teknik olarak bir sunucu içinde mümkün** görünse de (resmi manuel: başka veritabanınca referans alan FK ile veritabanı droplanamaz) mimari olarak **anti-pattern** sayılır — bütünlük DB'ye devredilmez, servis sınırını deler; **(4)** şema sahipliği "kim migration yazar" sorusunu da çözer: tek sahip + tek kapı; **(5)** migration otoritesi CI'da **kapı** olarak uygulanır (history doğrulama, test, lock/lock-risk kaydı, forward-only/immutable) |
| Web Search **Uzun Açıklama** | **(1)** Confluent *Data Ownership* (Ben Stopford single-writer) "her mikroservis kendi verisine sahip olmalı, okuma/yazmanın tamamını kontrol etmeli" der; Microsoft *Data sovereignty per microservice* "her mikroservis kendi domain verisini ve mantığını sahiplenmelidir" kuralını koyar; Manu's Vault *Data Ownership Patterns* tek-yazar-otorite ilkesini "her veri parçasının yaşam döngüsünü kontrol eden tek bir gerçeklik kaynağı" olarak tanımlar; architectureandgovernance *Domain-Driven Services* ve Medium DDD-microservices yazıları aynı hükmü tekrarlar; ScienceDirect (Özkan 2025, 41 alıntı) DDD'nin API tasarımı, **veri sahipliği** ve servis entegrasyonunu birlikte belirlediğini söyler. **(2)** microservices.io *Database per service* — "her servis kendi DB'sine sahiptir, başka servis doğrudan erişemez" + schema-per-service'in sahipliği netleştirdiği; AakashX "tek servis: şema, yazma yolu, iş kuralları, **migrations** ve denetim davranışının sahibidir"; GeeksForgeeks/TechTrailCamp/Medium aynı izolasyon kuralını verir; Dataversity ölçek/test esnekliğini gerekçe gösterir. **(3)** MySQL 8.4 §15.1.20.5 kısıtları: aynı engine, `REFERENCES` yetkisi, benzer tipler — ve `foreign_key_checks` maddesinde "başka veritabanındaki tablolarca referans alan FK ile veritabanı droplanamaz" ifadesi; SO #24019880 ve DBA.SE #290676 pratikte `db.table` referansının kullanıldığını, aradaki engelin **yetki/engine/sentaks** olduğunu; hata 1215 kaynakları (Bytebase, Percona) tip/engine uyuşmazlığının asıl neden olduğunu; Cockroach Labs ve MariaDB ise FK ile ilişkisel bütünlüğün tek başına yeter olmadığını, sharding/partition sınırında FK'nın düştüğünü anlatır → **teknik mümkün ≠ mimari doğru**. **(4)** AakashX + Confluent + Microsoft: şema sahibi = migration sahibi (ayrı ekip/kapı yok). **(5)** Harness *zero downtime migrations*, Bytebase *schema change*, Atlas *CI/CD declarative migrations*, mydbops **6 kontrol noktası** (migration history doğrulama → test → temsili veriyle prova → lock/replication riski → gate → deploy) ve oneuptime/Medium **forward-only + immutable** (rollback script yerine fix-forward) 2025-26'da standarttır; r/aws "üretimde migration'ı otomatik çalıştırma, ayrı iş olarak koştur" uyarısını verir. |
| Web Search **Paragraf Veri Uzun** | Beş sorgu aynı hükmü veriyor: **veri sahipliği tek yazıcı ile ölçülür** — kim yazıyorsa o sahiptir; kim migration yazıyorsa şema da onundur. CoreMusic bugün tersini yaşıyor: 18 şema diskte ama **kodda 1 DB çağrısı**, 28 çapraz FK ise "yazan" ile "sahip olan"ı ayırmadan önce çizilmiş çizgiler. Literatür bu çizgileri iki gerekçeyle keser: (i) çapraz FK bütünlüğü DB'ye değil servis sınırına devreder, servisler arası gizli senkronizasyon (kilit, lock-step deploy) üretir; (ii) tek kapısız migration'da "kim neyi ne zaman değiştirdi" izlenemez → drift. Tersi de doğru: **cross-DB FK'yı bir gecede silmek** veri kaybı/uygulama kırılması üretir; standart yol envanter + istisna kaydı + CI kapısı + fazlı temizliktir (mydbops gate sırası, Harness expand-contract ruhu — ADR-014 ile aynı). Bu üç bulgu §2.2-b/c/d kararlarının doğrudan gerekçesidir. |
| Web Search **Sonucu** | **25 adlandırılmış kaynak** (aşağıda); her ana iddia ≥2 bağımsız kaynakla çaprazlandı: tek-yazar sahiplik (6 kaynak), database-per-service (5), cross-DB FK statüsü (7), şema↔migration sahipliği (5), migration CI kapısı + forward-only (7), DDD-2025 (2). **Dış kaynakla doğrulanamayan tek konu = CoreMusic'in 18 DB envanteri ve 11 servis listesi** → iç kanıt (disk taraması + kullanıcı onayı) ile sabitlendi, `⚠️ VERIFICATION REQUIRED` değil (iç karardır). **Tek ⚠️ kalan:** 15 yorum satırındaki "MySQL cross-DB FK desteklemiyor" iddiasının canlı doğrulaması (§1.1-E C1). |
| Web Search **Alınan Karar** | **(a) 18 DB sahiplik matrisi** (§2.2-a) her satırda "veri ↔ DB ↔ sahip servis ↔ dayanak" taşır; **(b) tek yazar kuralı**: bir DB'yi yalnız sahibi servis yazar, diğerleri **okuma API + olay (outbox)** üzerinden okur; **(c) cross-DB politikası: varsayılan YASAK** — mevcut **28 FK istisna defterine** yazılır (kaldırılmaz, taşınmaz), **yenisi CI ile RED**, temizlik 3 fazda; **(d) şema sahipliği + migration tek kapısı**: `.ai/.sql/mysql/*.sql` = şema SSOT, yazım yalnız ADR-014 runner'ından (versioned SQL, DB-başına bağımsız sequence, forward-only), elle DDL yasak |
| Web Search **Sonuç** | Dış kaynaklar **tek-yazar sahiplik + database-per-service + CI'da migration kapısı + forward-only** tezini **destekliyor**; cross-DB FK'nın **teknik mümkün olması** ile **mimari olarak yanlış olması** ayrımı kaynaklarda net (§3 alternatif 1 ve 4); "hızlı temizlik" (big-bang FK kaldırma) için destek **yok** → kademeli faz planı (§2.2-c) ve riskler (§4.3 R1, R3) bu ayrımı taşır |

**Kaynak listesi (25):** 1) dev.mysql.com — MySQL 8.4 Ref. §15.1.20.5 FOREIGN KEY Constraints (resmi, tam metin çekildi) · 2) stackoverflow.com/q/24019880 — SQL foreign key in another database · 3) dba.stackexchange.com/q/290676 — linking FKs across multiple databases · 4) bytebase.com — MySQL error 1215 · 5) percona.com — MySQL error 1215 · 6) cockroachlabs.com — common foreign key mistakes · 7) mariadb.com/docs — Foreign Keys · 8) developer.confluent.io — Data Ownership (single-writer) · 9) learn.microsoft.com — Data sovereignty per microservice · 10) microservices.io — Database per service · 11) medium.com/@artemkhrenov — Database per service (sole owner of data store) · 12) aakashx.com — Database ownership in microservices (şema + yazma yolu + migrations) · 13) geeksforgeeks.org — Database per service pattern · 14) techtrailcamp.com — Database per service · 15) dataversity.net — Data management patterns for microservices · 16) architectureandgovernance.com — Domain-driven services (just the right size) · 17) sciencedirect.com — Domain-Driven Design in software development (Özkan 2025, 41 alıntı) · 18) bursasiu.ro — Data Ownership Patterns (single-writer ownership) · 19) harness.io — Zero downtime database migrations · 20) bytebase.com/blog — How to handle database schema change · 21) atlasgo.io — CI/CD for declarative migrations · 22) mydbops.com — Automate database schema migrations in CI/CD (6 checkpoint) · 23) oneuptime.com (2026-02) — forward-only migrations · 24) medium.com/@jasminfluri — Database rollbacks in CI/CD: immutable + forward fixes · 25) reddit.com/r/aws — DB migrations in a CI/CD pipeline.

### §1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| **Frozen 001-037 immutabel** | Yalnız atıf; metin değiştirilmez |
| **ADR-003/033 hizası bozulmaz** | Bölme ölçütü (domain), "DB içi FK serbest / DB arası FK yasak", 156 tablo + 28 cross-DB bulgusu bu ADR'de **tekrar sayılmaz, devralınır** |
| **Şema dosyası = SSOT, dosya adları değişmez** | In-Place Refactoring: `.ai/.sql/mysql/*.sql` adları ve 18 DB adı yeniden adlandırılmaz; değişiklik = yeni ADR |
| **Kod 0 → PLANNED disiplini** | Migration runner (ADR-014), SQL kapısı olan CI job'ı, 8 servis dizini = **kod 0** → adımlar bu etiketle yazılır |
| **Diskte olmayan ADR'ye wiki-link kurulmaz** | ADR-041, ADR-050 (index kayıtları var, dosya YOK) → düz metin + `⚠️ VERIFICATION REQUIRED` (§6) |
| **Tek yazma kanalı** | Vault yazımı `.ai/scripts/vault-utf8-writer.mjs`; `log.md` yalnız append |
| **REDACTED** | DB kullanıcı/şifre/host hiçbir koşulda yazılmaz (yalnız DB **adları** — herkese açık şema adıdır) |

---

## §2 Karar (Decision)

**CoreMusic veritabanı otoritesi dört alt kararla sabitlenir: (a) 18 DB'nin her biri için "hangi veri hangi DB" sahiplik matrisi (ADR-003/033 ile hizalı, tek tablo); (b) her DB'nin tek yazar servisi — diğer servisler yalnızca okuma API'si ve olay üzerinden okur; (c) cross-DB politikası: varsayılan YASAK, mevcut 28 FK istisna defterine yazılır, yenisi CI ile engellenir, temizlik üç fazda yürür; (d) şema sahipliği + migration yetkisi: şema dosyaları SSOT'tur ve yazım yalnız ADR-014 özel PHP runner'ının tek kapısından yapılır (elle DDL yasak).**

### §2.1 Neden Bu Seçenek?

1. **Sahiplik ölçüsü tek-yazarlıktır (§1.3 kaynak 8, 9, 16, 18):** kim yazıyorsa o sahiptir; bu nedenle sahiplik matrisi "veri listesi" değil, **yazma yetkisi** olarak yazılır.
2. **Mevcut durum zaten kararsız:** ADR-003 "FK yasak" derken şemada 28 çapraz FK var → kuralı çiğneyen yeni bir FK eklemek bugün cezasız. Politika = kuralın **müfettişi** (CI) + **istisna defteri** (kayıtlı borç) birlikte.
3. **Migration tek kapısı literatürle örtüşüyor (kaynak 19-25):** CI'da kapı + forward-only + immutable; ADR-014 runner'ı zaten bu kapıyı tarif ettiği için sıfırdan araç icat edilmez.
4. **Kademeli temizlik tek desteklenen yol (kaynak 19, 20, 22):** big-bang FK kaldırma veri/uygulama kırılması üretir; expand-contract ruhu (ADR-014) temizliğe de uygulanır.

### §2.2 Teknik Detaylar

#### §2.2-a 18 DB sahiplik matrisi (hangi veri hangi DB · hangi servis yazar · dayanak)

> **Dayanak etiketi:** `KOD` = kodda doğrulandı · `ALAN` = ADR-003 domain gerekçesi + ADR-039 servis listesi (kod kanıtı yok) · `ATAMA` = servis listesi ile alan uyuşmazlığı → **debate/Tech Lead doğrulaması şart**.

| # | Veritabanı | Tablo | Kapsam (brain §11) | Sahip servis (ADR-039) | Dayanak | Durum |
|---|---|---|---|---|---|---|
| 1 | `coremusic_auth` | 13 | Kullanıcılar, roller, session, token, credential vault, API key | **auth** | **KOD** (`constants.php:37`, `AuthContainer.php:40-43`) | IMPLEMENTED |
| 2 | `coremusic_user` | 7 | Profiller, tercihler, geçmiş, favoriler | **home** | **KOD (tanım)** — kullanım 0 (C3) | ⚠️ TANIM/KULLANIM FARKI |
| 3 | `coremusic_musics` | 22 | Şarkılar, sanatçılar, türler, dosyalar, podcast, video, radyo | **music** | **KOD (parça)** `AIWorkflow.php:124`; music dizini YOK | PLANNED (servis) |
| 4 | `coremusic_albums` | 5 | Albüm koleksiyonları, diskler, istatistikler | **music** | ALAN (ADR-003 #4) | ATAMA |
| 5 | `coremusic_playlist` | 5 | Çalma listeleri, işbirlikçiler, takipçiler | **music** | ALAN (ADR-003 #5) | ATAMA |
| 6 | `coremusic_catalog` | 8 | Referans veriler (tür, rol, enstrüman, ruh hali) | **music** | ALAN (salt-okunur sözlük; yazan tek servis) | ATAMA |
| 7 | `coremusic_logs` | 22 | Audit trail, analitik, hata logları, performans metrikleri | **dev** (gözlem) | **ATAMA — istisna:** append-only, **çok yazıcı** (tüm servisler) | ⚠️ İSTİSNA KAYDI (b)2 |
| 8 | `coremusic_media` | 8 | Cihaz senkronizasyonu, medya metadata, erişim kontrolü | **media** | ALAN (ADR-003 #8) | ATAMA (dizin YOK) |
| 9 | `coremusic_system` | 17 | Ayarlar, config, cache, EQ, bildirimler, i18n | **admin** | ALAN (tek yazımcı / çok okuyucu — ADR-003 #9) | ATAMA |
| 10 | `coremusic_social` | 9 | Yorumlar, paylaşımlar, aktivite, dinleme odaları | **main** | **ATAMA** — `entity_type` polymorphic (music/album/playlist/podcast/radio/video) → tek alana sığmaz, platform sahibi | ⚠️ debate |
| 11 | `coremusic_wireless` | 5 | WiFi + Bluetooth ağları, cihaz senkronu | **car** | **ATAMA** — ADR-037 sahipliği Data/Embedded (kod 0) | ⚠️ debate |
| 12 | `coremusic_ai` | 6 | Tercih profilleri, dinleme özellikleri, öneriler, model versiyonları | **music** | ALAN (ADR-030 önerisi müzik keşfi) | ATAMA (LLM kodu 0) |
| 13 | `coremusic_api` | 4 | API anahtarları, rate limit, çağrı logları, webhook | **dev** (Gateway/altyapı) | **ATAMA — istisna:** hot-path sayaç, Gateway yazar | ⚠️ debate |
| 14 | `coremusic_cms` | 8 | Sayfalar, blog, etiketler, medya varlıkları, SSS | **admin** | ALAN (editoryal yayın — ADR-003 #14) | ATAMA |
| 15 | `coremusic_download` | 4 | İndirme kuyruğu, geçmiş, önbellek, kaynak API'leri | **download** | ALAN (ADR-026 + ADR-039 §2.2-d 1. sıra) | PLANNED (kod 0) |
| 16 | `coremusic_neva` | 4 | EQ preset'leri, DSP ayarları, yönlendirme matrisi, spektrum | **main** (çok-yüzey oynatıcı) | **ATAMA** — ADR-019 per-OS oynatıcı her yüzeyde | ⚠️ debate |
| 17 | `coremusic_studio` | 6 | Stüdyo oturumları, parçalar, preset'ler, ekipman | **studio** | ALAN (ADR-003 #17) | PLANNED (dizin YOK) |
| 18 | `coremusic_patch` | 3 | `schema_versions`, `migration_log`, `patches` | **migration runner (ADR-014)** — servis değil araç | **ATAMA — istisna:** yalnız runner yazar (b)3 | IMPLEMENTED (şema) |
| | **TOPLAM** | **156** | 18 domain | 11 servis listesiyle eşleşen 11 + 3 istisna | | |

**Sahipsiz/örselenmiş alanlar debate çıkışıdır:** `social`, `wireless`, `neva`, `api` (⚠️ ATAMA) — ADR-039 §5.1 adım 5 (veri sahipliği dağılımı) bu satırları kapatır; kapanana kadar bu matris **geçici sahiplik** olarak okunur.

#### §2.2-b Sahiplik ve erişim kuralları (bağlayıcı)

| # | Kural | Detay | Uygulama |
|---|---|---|---|
| 1 | **Tek yazar servis** | Bir DB'yi yalnız §2.2-a'daki sahip servis yazar (`INSERT/UPDATE/DELETE`, `ALTER`, indeks kararı) — başka servis **asla** doğrudan yazamaz | Repository katmanı: kayıt `@see ADR-040` + code review; ihlal → §4.3 R1 |
| 2 | **Diğerleri okuma API + olay** | Çapraz okuma (i) sahibin **read API**'si, (ii) **olay** (yayın → abone, ADR-081 outbox + WAL, PSR-14 event) ile — doğrudan `SELECT` başka DB'ye **yok** | Gateway (`shared/src/Api/Gateway.php`) üzerinden; servis↔servis HTTP zaten yasak (ADR-039 §2.2-b) |
| 3 | **Açık istisnalar (kayıtlı)** | (a) **`coremusic_logs`** = append-only ortak alan (çok yazıcı) · (b) **`coremusic_patch`** = yalnız ADR-014 runner · (c) **`coremusic_api`** = Gateway hot-path · (d) **`coremusic_catalog`** = salt-okunur sözlük (tek yazıcı seed/owner) | Her istisna bu ADR'de yazılı; yenisi ancak **yeni ADR** ile eklenir |
| 4 | **Erişim = en az yetki** | DB kullanıcıları şema-başına ayrılır; bir servisin diğer şemada `GRANT`'ı olmaz (bugün `GRANT` tanımı yok → ADR-022 §1.1 boşluğu) | `⚠️ VERIFICATION REQUIRED` — sunucu tarafı GRANT envanteri bu repo'da yok (REDACTED: kimlik bilgisi yazılmaz) |
| 5 | **Şema-sahibi ≠ servis-sahibi** | Şema dosyasının sahibi **Data Engineer** (`.ai/AGENTS.md` §5: `*.sql` → Data Engineer); verinin sahibi servistir | Migration kapısı Data Engineer + Backend ortak (§2.2-d) |

#### §2.2-c Cross-DB politikası: varsayılan YASAK + belgelenmiş istisnalar + kademeli temizlik

**Kural:** ADR-003 §2.2-b ("DB arası FK: YOK") **bu ADR ile güçlendirilir**: yasak yalnız kağıtta kalmaz, **statik kapı ile** uygulanır; mevcut 28 ihlal **kaldırılmaz**, **istisna defterine** yazılır.

**İstisna defteri (mevcut borç — 28/28 kayıt, hiçbir silinmedi):**

| İstisna # | Dosya | Satırlar | Hedef | Durum |
|---|---|---|---|---|
| X-01 … X-11 | `coremusic_user.sql` | `:50, 83, 114-115, 138, 160-161, 188-189, 223-224` | `coremusic_auth.users` (8) · `coremusic_musics.musics` (3) | **KABUL (grandfathered)** |
| X-12 … X-24 | `coremusic_social.sql` | `:47, 67, 91, 118, 119, 150, 151, 180, 204, 205, 235, 266, 267` | `coremusic_auth.users` (11) · `coremusic_musics.musics` (2) | **KABUL (grandfathered)** |
| X-25 … X-28 | `coremusic_system.sql` | `:69, 100, 128, 225` | `coremusic_auth.users` (4) | **KABUL (grandfathered)** |
| **Toplam** | 3 dosya | **28** | `auth.users` 23 · `musics.musics` 5 | Yeni ihlal **yasak** |

**Kapı (yenisi engellenir):**

| Kapı | Ne yapar | Durum |
|---|---|---|
| **K1 Statik tarama (CI job)** | `*.sql` içinde `REFERENCES <şema-adi>.` deseni → bu defterde kayıtlı değilse **RED** (ADR-003 §2.2-b "CI/statik kapı RED" maddesinin uygulaması) | **PLANNED** — `.github/workflows/` bugün yalnız PHP lint/test + composer audit + GitLeaks (`ci.yml`, `secret-scan.yml`) → SQL job'u **0** |
| **K2 Migration kapısı** | Yeni şema değişikliği yalnız ADR-014 runner'ından → runner `--verify` çıktısı CI'a bağlanır | **PLANNED** (runner kod 0) |
| **K3 Kod review notu** | Repository/service kodunda başka DB adına sorgu → review RED | **PLANNED** (kural bugün yazılı, müfettiş yok) |

**Kademeli temizlik (3 faz — big-bang YASAK):**

| Faz | İş | Çıkış ölçütü | Durum |
|---|---|---|---|
| **Faz 0 — Kayıt** | 28 FK + 15 "not supported" yorumu bu §2.2-c defterine işlenir; C1 (MySQL teknik yeterlilik) canlı ortamda test edilir | Bu ADR yayında = defter dolu | ✅ **BU ADIMDA YAPILDI** (defter §2.2-c) |
| **Faz 1 — Kapı** | K1 statik tarama CI'a eklenir; yeni cross-DB FK üretilemez | CI'da kırmızı örnek testi yeşil | ⏳ PLANNED (2 gün) |
| **Faz 2 — Temizlik** | 28 FK'nın **uygulanma durumu** doğrulanır (uygulanıyorsa DDL çalıştırılmıştır; uygulanmadıysa dump-uyumsuzluk) → her satır için: **application-level ref** (owner DB'de anahtar + uygulama doğrulaması) veya **outbox olay** (ADR-081) ile yeniden kurulur; `ON DELETE` davranışları uygulama orkestrasyonuna taşınır | 28 → 0 ya da 28 → **gerekçeli yeni istisna ADR'si** | ⏳ PLANNED (10 gün) |
| **Faz 3 — Kapanış** | 15 yorum satırı düzeltmesi (yanlış teknik iddia varsa), istisna defteri "0 borç" veya "kalıcı istisna" olarak güncellenir, `log.md` + `brain.md` özeti | Defter durumu = son satır | ⏳ PLANNED |

#### §2.2-d Şema sahipliği ve migration yetkisi (tek kapı)

| Kural | Karar | Dayanak |
|---|---|---|
| **Şema SSOT** | `.ai/.sql/mysql/*.sql` (18 dosya) şemanın **tek kaynağıdır**; kod içi `CREATE/ALTER` yok | ADR-003 §2.3 · disk kanıtı (18 dosya) |
| **Tek kapı** | Şema değişimi **yalnız** ADR-014 özel PHP runner'ından: versioned SQL (`NNNN_<slug>.sql`), **DB başına bağımsız sequence**, `schema_migrations` (`version, name, checksum, executed_at, duration_ms, applied_by`), `--dry-run` / `--verify` / `--status`, DB-seviyesi lock, **forward-only** (üretimde `down` yok) | [[ADR-014-multi-db-migration-strategy]] (kural IMPLEMENTED, runner **PLANNED kod 0**) |
| **Sürüm tabloları** | `coremusic_patch.sql:14` `schema_versions` + `coremusic_system.sql:301-318` `system_schema_versions` (DB-başına unique) → runner bu tabloları kullanır, ikinci sürüm tablosu açılmaz | IMPLEMENTED (şema) |
| **Elle DDL yasak** | Üretimde/development'da konsoldan `ALTER` = politika ihlali; istisna yalnız **acil hotfix** ve bu durumda `log.md` append + derhal runner'a geri kayıt | Kural (yeni) — denetim K2 |
| **İndeks/performans kararı** | Yavaş sorgu + indeks kararı sahip servisin işidir, şema sahibi Data Engineer **onaylar** (`.ai/AGENTS.md` §5) | ADR-002 (`EXPLAIN` kapısı) + ADR-033 (indeks politikası sorgu workload'una bağlı) |
| **Cross-DB değişiklik** | İki DB birden değişiyorsa **expand → deploy → contract** (iki aşamalı, tek deploy'da asla) — ADR-014 §2.2 ile aynı kapı | [[ADR-014-multi-db-migration-strategy]] |

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Statü quo — kural yaz, müfettiş koyma** (cross-DB FK serbestçe çoğalmaya devam etsin) | Sıfır çaba; 28 FK zaten var | ADR-003 "yasak"ı kâğıtta kalır; 29., 30. FK sessizce eklenir; sahiplik sorusu cevapsız kalır | §1.3 kaynak 12/19/22: sahiplik ve change gate olmadan drift kaçınılmaz; bu ADR'nin tam varlık nedeni (§1.2 m.2) |
| 2 | **Big-bang temizlik — 28 FK bugün kaldırılsın** | Defter 0'a iner, kural anında tutarlı | `ON DELETE CASCADE` davranışları kalkar → yetim kayıt riski; uygulama tarafında karşılığı henüz yazılmadı (kod 0); veri kaybı potansiyeli | §1.3 kaynak 19-24 (expand-contract, immutable/forward-only) tek aşamalı yıkımı reddeder; ADR-014 de aynı gerekçeyle `down`'ı yasaklar |
| 3 | **Tek dev veritabanı (18 → 1)** | Tek join, tek transaction, FK'lar içeri taşınır | Reddedilen karar **R-009** (`.ai/.decisions/index.md` §5); 156 tabloda tek arıza noktası, alanlar arası kilit | ADR-003 §3 alt.1 — bu ADR onu **devralır, tekrar açmaz** |
| 4 | **Cross-DB FK'yı teknik olarak serbest bırak (MySQL müsaade ediyor, kullanalım)** | Bütünlük DB'de kalır, kod az değişir | Servis sınırını deler; `coremusic_auth.users` silme/güncelleme tüm şemaları cascade ile yürütür → gizli senkronizasyon + lock-step deploy; sahiplik (karar b) anlamsızlaşır | §1.3 kaynak 1-7: teknik imkân ≠ mimari doğruluk; ADR-003 §2.2-b açıkça reddeder; §4.3 R4'e düşer |
| 5 | **Migration yetkisini servislere dağıtmak (her servis kendi şemasını kendi aracıyla değiştirsin)** | Servis bağımsızlığı maksimum | Runner 18 kez çoğalır; checksum/lock/status 18 farklı yorum demek; kapı olmazsa K1 etkisiz | §1.3 kaynak 12/20/22: sahiplik **tek kapı** ile birlikte anlamlı; ADR-014 "tek runner/CI job, hedef DB başına sırayla" der → bu alternatif ADR-014'ü çürütür |

---

## §4 Sonuçlar (Consequences)

### §4.1 Olumlu Sonuçlar

- **Tek tablo:** 18 DB ↔ veri ↔ sahip servis ↔ dayanak (§2.2-a) → "şu veri nerede, kim yazar" tek satırda cevaplanır; ADR-039 §5.1 adım 5'in girdisi hazır olur.
- **Yasak denetlenebilir hâle gelir:** K1 statik kapı ile ADR-003'ün "RED" maddesi fiilen çalışır; 28 mevcut ihlal gizlenmez, **kayıtlı borç** olur (şeffaflık).
- **Migration tek kapı:** elle DDL ve 18 ayrı araç yolu kapanır; checksum + lock + forward-only (ADR-014) sayesinde drift izlenebilir.
- **İstisnalar tanımlı:** `logs` (çok yazıcı), `patch` (yalnız runner), `api` (Gateway), `catalog` (salt-okunur) — istisnasız kural uygulanamaz; istisnalar yazılı olduğu için istisna çoğalamaz.
- **Dış destek:** tek-yazar sahiplik + database-per-service + CI kapısı + forward-only 25 kaynakla çaprazlandı (§1.3).

### §4.2 Olumsuz Sonuçlar

- **Çapraz okuma pahalılaşır:** doğrudan `SELECT` yerine API/olay → ilk dönemde ek kod ve gecikme (API composition / projection yazımı).
- **PLANNED yükü:** runner (kod 0), SQL CI job'u (0), 8 servis dizini (0) → bu ADR **karar/iskelet** seviyesindedir, üretim garantisi vermez.
- **Matris tartışmalı satırlar taşır:** `social`, `wireless`, `neva`, `api` atamaları kod kanıtı olmadan yapıldı (⚠️ ATAMA) → debate yanlış çıkarsa yeni ADR gerekir.
- **Geçici tutarlılık:** cross-DB FK/transaction yokluğu → yetim kayıt toleransı bilinçli kabul edilir (ADR-003 ruhu), outbox (ADR-081) olmadan telafi zayıf kalır.
- **`status: accepted` ≠ uygulama:** kapılar (K1/K2) fiilen çalışana kadar kural yalnız dokümantdır.

### §4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **R1 Veri sahipliği belirsizliği — matrisin ⚠️ ATAMA satırları yanlış sahip çıkar, servis kendi verisini yazamaz/yabancı veriyi yazar** | Yüksek (>%70) | Yüksek | Debate (⏳) + Tech Lead doğrulaması; ADR-039 §5.1 adım 5 ile kapanır; yanlışlık = **yeni ADR** (NNN+1), bu metin düzenlenmez |
| **R2 Kilitlenme — migration lock / cross-DB expand-contract sırasında bekleyen transaction ve MDL kilitleri** | Orta (%40-70) | Yüksek | ADR-014: DB-seviyesi lock + `ALGORITHM=INPLACE, LOCK=NONE` doğrulaması + DDL öncesi uzun transaction temizliği; K2 kapısı olmadan DDL yasak |
| **R3 Temizlik gerilimi — 28 FK'nın kaldırılması uygulama davranışını (cascade delete) bozar** | Orta (%40-70) | Yüksek | 3 faz (§2.2-c): Faz 0 kayıt ✅, Faz 2'de **önce** uygulama tarafındaki karşılık (application-level ref / outbox) **sonra** FK; big-bang yasak (§3 alt.2) |
| **R4 MySQL teknik yeterlilik belirsizliği (C1) — "desteklenmiyor" iddiası yanlışsa 28 FK fiilen uygulanmış olabilir** | Orta (%40-70) | Orta | Faz 0'da canlı doğrulama (`SHOW CREATE TABLE` + bilinçli test DB); sonuç ne olursa olsun **politika değişmez** (yasadışılık mimari karardır, teknik değildir) |
| **R5 Kapılar hiç kurulmazsa (K1/K2 PLANNED kalırsa)** | Orta (%40-70) | Orta | §5.1 adımları 4-6 sahipli ve süreli; `log.md` append ile izlenir; ADR-039 benzeri "debate şartı" olarak bağlanabilir |
| **R6 Çoğul yazıcı istisnası genişler (`logs` örneği çoğaltılır)** | Yüksek (>%70) | Orta | İstisna listesi (b)3 **kapalı kümedir**; yenisi yalnız yeni ADR ile eklenir |

### §4.4 Fallback (geri birleşim / geri dönüş)

1. **Matris tartışmalı satırı debate'de düşerse:** o satır `⚠️ SAHİPSİZ` işaretlenir, uydurma sahip yazılmaz; eşleşme ADR-039 §5.1/5 çıktısında verilir → **yeni ADR** ile güncellenir (bu dosya düzeltilmez).
2. **K1 CI kapısı kurulamazsa:** statik tarama `.ai/scripts/` içinde tek komut olarak (vault-utf8-writer `scan` ruhu) çalıştırılır ve `log.md`'ye elle eklenir → kapı yoksa en azından **denetim izi** kalır.
3. **Faz 2 temizlik uygulamayı kırarsa:** ilgili FK **geri eklenir** (yeni migration satırı, forward-only) ve o satır "kalıcı istisna"ya taşınır → borç görünür kalır, sessizce 29. olmaz.
4. **Runner hiç yazılmazsa:** şema değişimi ADR-014 §5.1/1'e kadar **elle + `schema_versions` kaydı ile** yürütülemez → kapalı kapı: `⚠️` işaretli hotfix akışı (§2.2-d) dışında DDL yok.
5. **Tam geri dönüş:** bu karar frozen değil → yeni ADR "revert of ADR-040" olarak yazılır; `.ai/.decisions/index.md:82` satırı `—` işaretlenir (silinmez); `log.md` append-only olduğu için geri dönüş de **yeni satırdır**.

---

## §5 Uygulama (Implementation)

### §5.1 Adımlar

| # | Adım | Sorumlu | Süre | Durum |
|---|------|---------|------|-------|
| 1 | Bu ADR'yi şablondan üret + künye/§1-§7 dolu (Guardrail #16) | Vault Steward | 3 dk | ✅ UYGULANDI (2026-09-26) |
| 2 | Şema + servis kanıtı taraması (18 dosya, 156 tablo, PK/FK sayımı, 28 cross-DB listesi, `registerMySql`/`DB_*_NAME` taraması) | Vault Steward + Data | 2 dk | ✅ UYGULANDI (2026-09-26) |
| 3 | `.ai/log.md`'ye 1 satır append (kayıt: `ADR-040-database-authority yazildi (48181 bayt, 385 satir, debate PENDING) + .decisions/index.md satir 82 duzeltildi`) | Vault Steward | 1 dk | ✅ UYGULANDI (2026-09-26) |
| 4 | **`[[../index.md]]:82` satırının** `[[../brain.md]] ADR-040-database-authority` biçiminden bu dosyaya bağlanması (`[[accepted/ADR-040-database-authority]]`) | Vault Steward | 1 dk | ✅ UYGULANDI (2026-09-26) |
| 5 | **K1 statik kapı:** `*.sql` `REFERENCES <şema>.` taraması CI job'una (`ci.yml` yanına yeni job) + istisna defteri (§2.2-c) deny-listesi | DevOps + Data | 2 gün | ⏳ PLANNED |
| 6 | **K2 kapı:** ADR-014 runner MVP (`shared/src/Database/Migration/` + `bin/migrate.php`) + `--verify` çıktısının CI'a bağlanması | Data + Backend | 2-3 oturum | ⏳ PLANNED (ADR-014 §5.1/1) |
| 7 | **Faz 2 temizlik:** 28 FK'nın canlı DB'de uygulanma durumu → application-level ref / outbox (ADR-081) karşılığı yazılır, sonra FK düşürülür | Data + Backend | 10 gün | ⏳ PLANNED |
| 8 | **C1 doğrulaması:** 15 "not supported" yorumunun teknik iddiası test ortamında çalıştırılır; sonuç bu ADR'nin **revizyon satırı** olarak `log.md`'ye append edilir | Data + QA | 1 gün | ⏳ PLANNED |
| 9 | **C2 düzeltmesi:** `AIWorkflow.php:124` `UPDATE tracks` → şemada `tracks` yok; ya hedef tablo düzeltilir ya ADR-030 kapsamına not düşülür | Backend + AI | 2 saat | ⏳ PLANNED |
| 10 | **C3 kapatma:** `DB_HOME_NAME` tanımının gerçekten kullanıldığı home DB erişim yolu (ya da tanımın kaldırılması) | Backend | 1 gün | ⏳ PLANNED |
| 11 | **Tartışmalı matris satırları debate'si:** `social`/`wireless`/`neva`/`api` sahipliği + `logs`/`api`/`patch`/`catalog` istisna listesinin onayı | Tech Lead + Data | 2 gün | ✅ DEBATE TAMAMLANDI (2026-09-26, 3 tur / 20 persona, 18/2/0 KABUL) — kalan kapanış = Şart 2 (adım 15) |
| 12 | **`brain.md:1000` özeti + `AGENTS.md` §5/domain boundary notu** ("tek yazar servis" kuralının vault'a işlenmesi) | MO (vault-updater) | 30 dk | ⏳ PLANNED (vault sync) |
| 13 | **(Debate Şartı 1a)** Cross-DB FK denetimi **CI job'una bağlanır** (K1 statik tarama, §2.2-c): `*.sql` içindeki `REFERENCES <şema-adi>.` deseni istisna defteri (X-01…X-28) ile karşılaştırılır, kayıtlı olmayan **RED**; çıkış ölçütü: CI'da kırmızı örnek testi yeşil (adım 5 ile aynı ölçüt) | DevOps + Data | 2 gün | ⏳ PLANNED (debate şartı) |
| 14 | **(Debate Şartı 1b)** **FK kapalı satır envanteri:** §1.1-A'daki **15** "cross-database FK not supported in MySQL" yorum satırının dosya:satır listesi + her satır için **neden** (C1 çelişki defteri) yazılır; sonuç adım 8 (C1 doğrulaması) ile birlikte kapatılır | Data + QA | 1 gün | ⏳ PLANNED (debate şartı) |
| 15 | **(Debate Şartı 2)** **18 DB sahiplik matrisi tamamlanır:** §2.2-a'nın 18 satırı **PLANNED / dizin YOK** satırlar dahil kapanır (⚠️ ATAMA → IMPLEMENTED ya da gerekçeli **SAHİPSİZ**); servis—DB eşleşmesi 4/18 → 18/18; çıkış ölçütü: her satırda sahip servis + dayanak + etiket | Tech Lead + Data | 2 gün | ⏳ PLANNED (debate şartı) |
| 16 | **(Debate Şartı 3)** **İstisna defteri + temizlik fazı testi:** 28/28 istisna kaydı (§2.2-c) doğrulanır ve Faz 2 temizliği **test ile** kapatılır (FK kaldırılmasına karşı uygulama davranışı: application-level ref / outbox); çıkış ölçütü: test yeşil + defter son satırı = durum | QA + Data | 3 gün | ⏳ PLANNED (debate şartı) |

### §5.2 Geri Dönüş Planı

1. **Karar seviyesi:** `status: accepted` ama **frozen değil** → vazgeçiş = yeni ADR (NNN+1, "revert of ADR-040") + bu dosya `superseded-by` ile bağlanır; metin **silinmez/değiştirilmez**.
2. **Matris seviyesi:** Bir sahiplik satırı değişirse yalnız o satır yeni ADR'de değişir; §2.2-a'nın bu sürümü `log.md` append ile tarihlenir (değiştirilmez).
3. **Kapı seviyesi (K1/K2):** Kapı yanlış kırmızı verirse job **atlanır** (workflow `continue-on-error`), kapı kalkmaz; denetim izi `log.md`'de kalır.
4. **Faz 2 seviyesi:** FK geri eklenirse yeni migration satırı (forward-only) açılır; istisna defterine "geri alındı" notu **yeni satır** olarak yazılır (eski satır silinmez).
5. **Dizin seviyesi:** `index.md` kayıt satırı geri alınırsa `—` olarak işaretlenir, silinmez.
6. **Bozulma durumunda:** `node .ai/scripts/vault-utf8-writer.mjs repair --file <dosya>` (yedek alır) → gerekirse `git checkout` (`.ai/AGENTS.md` §17 #10).

---

## §6 İlgili Dokümanlar (ilişki tablosu — tüm wiki-link hedefleri diskte doğrulandı: 36/36)

| Dosya (wiki-link) | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme — §18 "18 BCNF veritabanı, 156 tablo" (envanter özeti) |
| [[../../brain.md]] | `:296` §11 18 BCNF tablosu (bu matrisin girdisi) · `:1000` `ADR-040 \| 18 BCNF veritabanı otoritesi` slotu · `:868/:1041` ADR-040 atıfları |
| [[../../AGENTS.md]] | Agent registry — §5 domain boundary (`*.sql` → Data Engineer), §18 #7 layer violation |
| [[../../WORKFLOW.md]] | Süreç/fazlar — uygulama adımlarının bağlandığı akış |
| [[../../index.md]] | Master katalog |
| [[../../log.md]] | Audit trail — bu ADR'nin append kaydı |
| [[../index.md]] | Karar dizini `:82` slug satırı (bu dosyaya bağlanması §5.1 adım 4) |
| [[../CLAUDE.md]] | Karar dizini kuralı |
| [[../../.templates/adr/adr-template.md]] | Guardrail #16 şablonu (bu dosyanın iskeleti) |
| [[../../.agents/data-engineer.md]] | Şema sahipliği sorumlusu + 18 dosya envanteri (§2.2-d) |
| [[../../../.claude/skills/prompt-maker/references/10-web-research-protocol.md]] | §1.3 araştırma protokolü |
| [[ADR-002-pdo-mandatory-no-orm]] | Erişim katmanı (PDO tekelı) — §2.2-b erişim kurallarının zemini |
| [[ADR-003-multi-db-bcnf]] | **Devralınan:** 18 domain/DB + "DB içi FK serbest / DB arası FK YOK" + CI kapısı maddesi (§2.2-b/c) |
| [[ADR-014-multi-db-migration-strategy]] | **Devralınan:** tek kapı (özel PHP runner + versioned SQL + bağımsız sequence + forward-only) |
| [[ADR-033-sql-normalization-strategy]] | **Devralınan:** 156 tablo / 82 FK / 28 cross-DB / 15 yorum satırı bulguları |
| [[ADR-039-7-service-platform-architecture]] | 11 servis listesi — §2.2-a sahiplik matrisinin servis tarafı; §5.1 adım 5'in girdisi |
| [[ADR-081-multi-provider-data-sync]] | Outbox + WAL — cross-DB tutarlılık telafisi (§2.2-b kural 2, Faz 2) |
| [[ADR-004-multi-domain-spa]] | Mevcut çok-domainli iskelet — sahipliğin bağlandığı alt alan adları |
| [[ADR-022-database-hardened-security]] | DB erişim/GRANT boşluğu (§2.2-b kural 4 — `⚠️ VERIFICATION REQUIRED`) |
| [[ADR-032-ipc-contract-versioning]] | `schema_versions` ↔ sözleşme sürümü bağının vault kaynağı (§2.2-d) |
| [[ADR-034-credential-vault-normalization]] | `DB_NAME`/`DB_AUTH_NAME` ad alanı drift'i — §2.2-b erişim/yetki konfigürasyonuyla ilişkili |
| [[ADR-026-download-service-architecture]] | `coremusic_download` sahibi (§2.2-a satır 15) |
| [[ADR-028-anti-ban-system]] | download verisi (kuyruk/kaynak API) sahipliğinin gerekçesi |
| [[ADR-029-listening-rooms-social]] | `coremusic_social` kapsamı — matris satır 10 gerekçesi |
| [[ADR-030-ai-strategy-core]] | `coremusic_ai` şeması + `AIWorkflow` kod kanıtı (§1.1-C, C2) |
| [[ADR-037-wirelessconnect-integration]] | `coremusic_wireless` 5 tablo + Data/Embedded sahipliği (matris satır 11) |
| [[ADR-019-per-OS-neva-player]] | `coremusic_neva` çok-yüzey okuma alanı (matris satır 16 gerekçesi) |
| [[../../architecture/k5-veri-yonetimi/migration-strategy.md]] | MigrationManager **spec'i** — ADR-014 ile SUPERSEDED (§2.2-d) |
| [[../../architecture/k5-veri-yonetimi/README.md]] | K5 veri katmanı kataloğu |
| [[../../architecture/k0-isletim-sistemi/README.md]] | L0 altyapı katmanı — 18 DB'nin barınma zemini |
| [[../../architecture/k8-servis/README.md]] | K8 servis katmanı — sahiplik eşleşmesinin (b)2 olay kuralı |
| [[../../.sql/mysql/coremusic_user.sql]] | **11 cross-DB FK** (istisna X-01…X-11) |
| [[../../.sql/mysql/coremusic_social.sql]] | **13 cross-DB FK** (istisna X-12…X-24) |
| [[../../.sql/mysql/coremusic_system.sql]] | **4 cross-DB FK** (istisna X-25…X-28) |
| [[../../.sql/mysql/coremusic_patch.sql]] | `schema_versions` / `migration_log` / `patches` — sürüm takibi IMPLEMENTED |
| [[../../.sql/mysql/coremusic_auth.sql]] | Cross-DB FK'ların **hedefi** (`users`) + BCNF self-declaration |

**Wiki-link KURULMAYAN (diskte dosya YOK → düz metin + `⚠️ VERIFICATION REQUIRED`):**

| Referans | Durum |
|---|---|
| **ADR-041-database-normalization-supplementary** | `[[../index.md]]:83` kaydı var, `.ai/.decisions/**` altında dosya **YOK** → `⚠️ VERIFICATION REQUIRED` |
| **ADR-050-multi-db-sync-strategy** | `[[../index.md]]:91` kaydı var, dosya **YOK** → `⚠️ VERIFICATION REQUIRED` |
| **Arazide çalışan MySQL sürümü / GRANT envanteri** | Bu repo'da yok (üretim sunucusu kapsam dışı) → `⚠️ VERIFICATION REQUIRED` (REDACTED) |

**Debate şartlarının bağlandığı dokümanlar (§7.1 — 3 şart):** Şart 1a → §2.2-c K1 kapısı + §5.1 adım 13 (CI'da cross-DB FK denetimi) · Şart 1b → §1.1-A 15 yorum satırı + §5.1 adım 14 (FK kapalı satır envanteri + neden) · Şart 2 → §2.2-a 18 satır matris + §5.1 adım 15 (sahiplik matrisi, PLANNED dahil) · Şart 3 → §2.2-c istisna defteri (28) + Faz 2 temizliği + §5.1 adım 16 (istisna defteri / temizlik fazı testi).

---

## §7 Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali / Vault Steward | 2026-09-26 | ✅ |
| Tech Lead | — | 2026-09-26 | ✅ |
| Arch Lead | — | — | ⏳ |

### §7.1 Tartışma Kaydı (Debate)

| Alan | Değer |
|---|---|
| **Durum** | ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — frontmatter `debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"` |
| **Tur 1 — Bulgular (20 persona)** | cross-DB FK **28 doğrulandı** (`coremusic_user` 11 / `coremusic_social` 13 / `coremusic_system` 4 → hedef `coremusic_auth.users` 23 · `coremusic_musics.musics` 5; toplam **82 FK = 54 same-DB + 28 cross-DB**) · **15** "cross-database FK not supported" yorum satırı → **C1** · servis↔DB eşleşmesi yalnız **4/18** (auth · home tanım var/kullanım 0 · AI `coremusic_musics` · 2 shared migration) · ADR-014 **runner kod 0**, Phinx **0**, CI SQL kapısı **0** (C4) · şema takibi **IMPLEMENTED** (`coremusic_patch.sql:14/:35/:57`, `coremusic_system.sql:301-318`) · **25 kaynak** · **C1-C6 çelişki defteri** · **15 kabul/neutral + 4 uyarı** (DevOps: CI kapısı şart; QA: matris 4/18; Critic: "not supported" 15 + PLANNED sahiplik şart) |
| **Tur 2 — İtiraz→çözüm** | 1) CI SQL kapısı **0** → cross-DB FK denetimi CI'a bağlanır → **Şart 1a** · 2) "not supported" **15 satır** (C1) → FK kapalı satır envanteri + gerekçesi → **Şart 1b** · 3) Servis↔DB **4/18** → 18 DB sahiplik matrisi tamamlanır (**PLANNED dahil**) → **Şart 2** · 4) Test yok → istisna defteri + temizlik fazı testi → **Şart 3** |
| **Tur 3 — Oy** | **18 kabul / 2 çekimser / 0 red → KABUL** |
| **Şartlar (bağlayıcı)** | **Şart 1:** CI'da cross-DB FK denetimi (1a) + FK kapalı satır envanteri (1b) · **Şart 2:** 18 DB sahiplik matrisinin tamamlanması (PLANNED dahil) · **Şart 3:** istisna defteri + temizlik fazı testi → uygulama **§5.1 adımlar 13-16** |
| **Karar kapsamı kaynağı** | Kullanıcı onaylı **tam kapsam**: (a) 18 DB sahiplik matrisi (ADR-003/033 hizalı) · (b) sahiplik kuralları (tek yazar servis, diğerleri okuma API + olay) · (c) cross-DB politikası (varsayılan yasak + 28 istisna + CI kapısı + 3 fazlı temizlik) · (d) şema sahipliği + migration yetkisi (ADR-014 tek kapı) |
| **Debate'de doğrulanacaklar** | 1) §2.2-a **⚠️ ATAMA** satırları (`social`, `wireless`, `neva`, `api`) ve 4 istisna (§2.2-b kural 3) · 2) K1 CI kapısının `ci.yml`'e eklenme biçimi (§5.1 adım 5) · 3) Faz 2 temizlik sırası (önce uygulama karşılığı, sonra FK) · 4) C1 (MySQL teknik iddiası) doğrulama sonucu |
| **Bekleyenler** | Arch Lead (⏳) · §5.1 adımları 5-16 (12'si PLANNED, 4'ü debate şartı: adım 13-16) · `brain.md:1000` özeti (vault sync) |
| **Statü özeti** | `status: accepted` (kullanıcı onaylı kapsam) · debate **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** · **Tech Lead ✅** · **Arch Lead ⏳** · **frozen YOK** (ADR-001-037 dokunulmaz; bu dosya Active) · **3 bağlayıcı şart** (§5.1 adımlar 13-16) · uygulama adımlarının **12/16'sı PLANNED** (kod 0 kanıtı §1.1-C/D) · wiki-link **36/36 diskte**, 3 referans diskte olmadığı için düz metin + `⚠️ VERIFICATION REQUIRED` |

---

*ADR-040 v1.0.0 — CoreMusic Architecture Decision Record*
*Authority: ADR-040 Karar Metni (SSOT)*
*Last Updated: 2026-09-26*
*Mode: Red Team · Human Mode · Truth Mode*
