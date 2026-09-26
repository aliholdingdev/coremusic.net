---
title: "CoreMusic — ADR-014: Multi-DB Migration Stratejisi (Özel PHP Runner · DB-Başına Bağımsız Sequence · Expand-Contract · Forward-Only · Online DDL)"
type: adr
category: database
date: 2026-09-24
updated: 2026-09-24
version: 1.0.0
status: accepted
authority: ADR-014 Karar Metni (SSOT)
governance: Red Team · Human Mode · Truth Mode
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-014: Multi-DB Migration Stratejisi (Özel PHP Runner · DB-Başına Bağımsız Sequence · Expand-Contract · Forward-Only · Online DDL)

**Durum:** accepted (kullanılabilir — frozen YOK)
**Tarih:** 2026-09-24
**Karar Veren:** Vault Steward (kullanıcı onaylı — "ADR-014'ü sıfırdan yaz"; karar içeriği tamamı kullanıcı onaylı/Önerilen) · debate: ✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL) · Tech Lead: ✅
**İlgili ADR'ler:** [[ADR-002-pdo-mandatory-no-orm]] (ORM'siz, doğrudan PDO — runner'ın da ORM'siz olması gerektiği; dosya diskte VAR ✅) · [[ADR-003-multi-db-bcnf]] (18 BCNF DB + **DB arası FK yok** → migration'ların bağımsızlığı bu kararın dayanağı; dosya diskte VAR ✅) · **ADR-040** (18 DB şema otoritesi — **düz metin, `ADR-0xx` biçimli wiki-link kurulmaz**, `.ai/.decisions/**` altında dosyası YOK → glob kanıtı) · [[ADR-081-multi-provider-data-sync]] (yedek/restore + reconciliation ruhu — forward-only'in son savunma hattı; dosya diskte VAR ✅) · karar dizini [[../index]] §3 satır 51 `[[ADR-014-multi-db-migration-strategy]]` (slug eşleşmesi ✅).

---

## 1. Bağlam (Context)

CoreMusic **18 BCNF veritabanını** tek projede barındırır (`.ai/.sql/mysql/` altında 18 `coremusic_*.sql` dump dosyası) ve bu şemaların değişimi bugün **hiçbir otomasyonla yürütülmemektedir**: şema dosyaları tam dump (SSOT) olarak durur, numaralı migration dosyası yoktur, çalışan bir migration runner yoktur, uygulanmış migration geçmişi tutan bir tablo kodda bulunmaz. Bu ADR; şema değişiminin **nasıl versiyonlandığını, sıralandığını, doğrulandığını ve geri alındığını** tanımlar — 18 DB'nin operasyonel gerçeği (bağımsız sequence, kısmi uygulama riski, büyük tablo DDL'si) gözetilerek.

Kapsam: (a) migration aracı seçimi (özel PHP runner + versioned SQL), (b) **DB başına bağımsız version sequence** ve `schema_migrations` tablosu, (c) cross-DB değişiklik için **expand-contract** ve iki aşamalı deploy, (d) **forward-only** rollback politikası + fix-forward, (e) MySQL online DDL kilit doğrulaması (`ALGORITHM=INPLACE, LOCK=NONE`), (f) runner'ın dry-run/checksum/lock mekaniği ve (g) mevcut vault iddialarıyla çelişen bulguların işaretlenmesi.

### 1.1 Mevcut Durum

**Kod/vault kanıtları (diskde okundu — IMPLEMENTED/PLANNED etiketleri dosya yolu + satır ile):**

- **`.ai/.sql/mysql/` — 18 tam dump dosyası IMPLEMENTED, migration dosya yapısı YOK (PLANNED):** glob → `coremusic_ai.sql, coremusic_albums.sql, coremusic_api.sql, coremusic_auth.sql, coremusic_catalog.sql, coremusic_cms.sql, coremusic_download.sql, coremusic_logs.sql, coremusic_media.sql, coremusic_musics.sql, coremusic_neva.sql, coremusic_patch.sql, coremusic_playlist.sql, coremusic_social.sql, coremusic_studio.sql, coremusic_system.sql, coremusic_user.sql, coremusic_wireless.sql` (**18 dosya**, 4.1 KB – 46.1 KB). **`0001_*.sql` biçimli numaralı migration dosyası = 0** → versiyonlu migration serisi **hiç başlamamış**; `.ai/.sql/CLAUDE.md` "SQL dump klasörü … Otorite: ADR-003 + ADR-040" der (dump = şema SSOT, migration ≠ dump).
- **`shared/database/migrations/` — 2 dosya IMPLEMENTED (içerik), runner YOK (PLANNED):** `oauth_states_migration.php` + `oauth_connections_migration.php` + `CLAUDE.md`. `oauth_states_migration.php:15-36` → `$queries = [ "CREATE TABLE IF NOT EXISTS oauth_states (...)" ]; return $queries;` — yani dosyalar **dizi döndüren script'lerdir**; `up()/down()` metodlu sınıf, kendi kendine koşan bir CLI veya kilit/checksum mantığı **yoktur**. `shared/database/migrations/CLAUDE.md` satır 19: *"Kural: Forward-only migration (ADR-014). Geri migration yasak."* → vault kuralı bu ADR'ye **hazır ve hizalı** ✅.
- **Migration runner kodu YOK → PLANNED:** `grep -i migration` → `shared/src/**` = **0 eşleşme**; repo geneli `schema_migrations|MigrationRunner|run_migrations` → yalnız `.ai/` dokümanları + `.claude`/`.opencode` skill'leri; `.ai/scripts/` = 4 dosya (`vault-utf8-writer.mjs`, `vault-faz4-sweep.mjs`, `index.md`, `fix-mojibake.py`) — **database script'i yok**. `shared/` klasör yapısında `bin/`, `tools/`, `scripts/` **yok**.
- **Harici migration aracı kurulu değil → IMPLEMENTED (negatif kanıt):** 3 `composer.json` (`shared/`, `auth.coremusic.net/`, `home.coremusic.net/`) repo geneli `phinx|doctrine/migrations|laravel/framework` taramasında **0 eşleşme** → Phinx/Doctrine **kurulu değil**. ⚠️ **İddia-kod çelişkisi:** `.ai/.agents/data-engineer.md:30,43,65,77,114,175` dosyaları **"2 Phinx migration"** ve "migration = Phinx" olarak etiketler; dosyaların içeriği Phinx sınıfı değil `$queries` dizisidir ve composer'da phinx yok → `data-engineer.md:114` satırındaki "phinx bağımlılığı composer'da YOK" itirafı iddiayı kendisi çürütür. `.ai/.agents/windows-software-engineer.md:199` doğru yolu gösterir: `grep -n "phinx" composer.json` → yoksa **migration aracı PLANNED**.
- **`.ai/architecture/k5-veri-yonetimi/migration-strategy.md` — SPEC (IMPLEMENTED-as-spec, kod YOK):** `MigrationManager` sınıf taslağı `migrate()` (satır 37), `rollback($steps)` (satır 59), `getStatus()` (satır 74), `recordMigration()` `down` yönünde `DELETE FROM schema_migrations` (satır 186-189), tek global `schema_migrations` tablosu `CREATE TABLE IF NOT EXISTS` (satır 193-201; **checksum sütunu YOK**), `SELECT * FROM schema_migrations` (satır 167 — `SELECT *` yasağı ile çelişen örnek kod), ve **satır 16: "Her migration forward ve backward olarak uygulanabilir olmalıdır."** → bu dosyadaki **backward/down zorunluluğu ve tek global tablo, bu ADR ile SUPERSEDED olur**; sınıfın kendisi diskte hiçbir yerde `new MigrationManager` ile kullanılmıyor → **PLANNED (spec)**.
- **`.ai/.templates/infrastructure/migration-template.md` — forward-only şablonu IMPLEMENTED-as-spec:** satır 26 "Forward-only migration (revert yasak) | ADR-014", satır 74 "ADR-040 (BCNF) + ADR-014 (forward-only) compliant", satır 104 "ileriye düzeltme (revert YOK, yeni satır eklenir)", satır 384 #1 "Forward-only: migration revert yasak (ADR-014)", satır 422 "mevcut migration satırını düzenlemek → drift | Yeni satır ekle (ADR-014)", satır 451 "reversibles bir migration yazılmaz" → **şablon bu ADR'yi zaten bekliyor**; ADR yazıldığında şablonla çelişen satır kalmaz.
- **Vault iddiası "36 script" → ⚠️ VERIFICATION REQUIRED:** `.ai/.templates/adr/adr-database-template.md:182,498` ".ai/scripts/database/ = 36 script (seed/migrate/backup)" der; glob `.ai/scripts/database/*` = **0** → doğrulanamadı, uydurulmadı.
- **Glossary çelişkisi:** `.ai/glossary.md:440` "ADR-014 (Multi-DB Migration Strategy, **frozen**)" der; bu ADR `accepted` + **frozen YOK** olarak yazılır → iddia-kod çelişkisi, düzeltme ayrı vault işlemi (§5.1 adım 6).
- **Şablon/protokol kanıtları:** `.ai/.templates/adr/adr-template.md` (Guardrail #16, 7 bölüm + §1.3 9 alan) VAR ✅ · `.claude/skills/prompt-maker/references/10-web-research-protocol.md` VAR ✅ · format referansı `ADR-013-rate-limiting-apcu.md` VAR ✅.

### 1.2 Sorun Tanımı

1. **Versiyonlu migration serisi hiç yok:** 18 dump dosyası "şu anki hâl"dir; hangi değişikliğin hangi ortamda uygulandığı bilinmez → **schema drift** denetlenecek bir mekanizma yok.
2. **Runner yok:** 2 mevcut script (`oauth_*`) kimse tarafından koşmuyor; `CREATE TABLE IF NOT EXISTS` idempotent olsa da **uygulama geçmişi, kilit ve doğrulama yok** → aynı migration iki kişice iki kez uygulanabilir.
3. **Araç sözleşmesi çelişkili:** vault "Phinx" der, composer Phinx'e sahip değil, dosyalar Phinx formatında değil → hangisinin doğru olduğu diskten anlaşılmaz (§1.1 çelişki satırı).
4. **Geri alınabilirlik tanımsız:** k5 spec `rollback()`/`down()` ister ama hiçbir down script'i test edilmemiştir; üretime `down` çalıştırmak veri kaybı üretir (DROP edilmiş sütun geri gelmez).
5. **Cross-DB değişiklik tek deploy'da kırılgan:** 18 DB bağımsız (ADR-003: arası FK yok) → tek deploy'da hem eski hem yeni kod aynı anda koşarsa **tek deploy'da uyumsuz sürüm penceresi** açılır.
6. **Büyük tablo DDL'si bilinmiyor:** `coremusic_logs.sql` (30.8 KB), `coremusic_musics.sql` (46.1 KB) gibi şemalarda `ALTER TABLE` kopyalama moduna düşerse tablo dakikalarca kilitlenir → maintenance penceresi gerekir ama kural tanımsız.
7. **18 DB operasyonel yükü:** her DB kendi sequence'ini taşır; kısmi uygulama (yarısı migrate edilmiş deploy) tespit edilemez → tek bakışta `status` raporu yok.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: prompt-maker web araştırma protokolü (`.claude/skills/prompt-maker/references/10-web-research-protocol.md` — diskte VAR ✅) — birincil/resmî kaynak önce (MySQL Reference Manual, Flyway resmî dokümanı, Redgate/Redgate RCA, MariaDB Docs), **her iddiaya ≥2 bağımsız çapraz kaynak**, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. **Odak: (a) zero-downtime migration 2025-26, (b) expand-contract + transmutable/sync mekanizması + backfill, (c) MySQL online DDL locking, (d) multi-DB orchestration, (e) migration checksum drift.**

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "zero-downtime database migrations 2025 expand contract pattern best practices" · (2) "MySQL online DDL ALGORITHM=INPLACE LOCK=NONE alter table locking behavior documentation" · (3) "migration checksum drift detection checksums mismatch already applied migrations Flyway repair" · (4) "forward-only migrations no down migration rollback production data loss risk debate" · (5) "multi-database schema migration orchestration independent version tracking per database best practices" |
| Web Search **Konusu** | (1) Şemayı kesintisiz evrimdesenleri: expand→migrate/backfill→contract, dual-write, batch backfill, tetikleyici/trigger ile çift-senkron ("transmutable" akış), iki aşamalı deploy; (2) InnoDB online DDL'de `ALGORITHM` ve `LOCK` klasörlerinin semantiği, metadata lock fazları, hangi işlemin `LOCK=NONE` ile yapılamadığı; (3) Uygulanmış migration'ların checksum'ı (CRC32/BOM/CRLF) ile dosyadaki checksum arasındaki uyuşmazlık, validate/repair semantiği; (4) Forward-only (fix-forward) vs rollback tartışması ve `down()` yazmama gerekçeleri, yedek/restore'un rollback yerine geçişi; (5) Çoklu veritabanında bağımsız version geçmişi, migration başına hedef DB, orchestrator sarmalayıcısı ve DB-seviyesi kilit. |
| Web Search **Bağlam** | **~24 adlandırılmış kaynak / 5 sorgu** (çoğu 2025-2026): birincil — MySQL 8.4/9.7 Reference Manual §17.12.1 Online DDL + §15.1.9 ALTER TABLE + §17.12.2 Online DDL Performance, MariaDB Docs "InnoDB Online DDL Overview", Flyway resmî `Validate.md`/`Repair.md` (GitHub) + Redgate Flyway Docs ("Rolling out updates … multiple production databases", "Migrations"), Redgate RCA "Implementing a roll back strategy"; mühendislik — JusDB (2025-09), ReliablePenguin (2025-11), dev.to (2026-03), Harness (2026-07), appscale.blog (2026), Bytebase CI/CD (2025-10), QuickBooks Engineering (dual-write + verification), Alembic multi-DB gist; tartışma — SQLServerCentral "Rollback vs. Roll Forward" (2026-03), oneuptime (2026-08), Reddit r/SQL (2025-09), Medium/Laravel `down()` (2023). |
| Web Search **Kısa Açıklama** | **(1) Zero-downtime/expand-contract:** kırıcı her değişiklik üç faza bölünür — *expand* (yeni alan/tablo eklenir, eski korunur), *migrate* (batch backfill + kod iki sürümü de okur), *contract* (eski yalnızca herkes geçtikten sonra düşürülür); "asla tek adımda rename/type-change/drop" kuralı (JusDB: "Never rename columns — add new, migrate, drop old"; dev.to aynı üç fazı örnekler). Backfill küçük batch + `SLEEP` ile yapılır (replication lag önlemi). **(2) MySQL online DDL:** `LOCK=NONE` eşzamanlı sorgu ve DML'e izin verir, desteklenmiyorsa **ifade hata verir** (aradığımız doğrulama mekanizması); `LOCK=SHARED` sorguya izin verir DML'i kilitler; metadata lock üç fazda (init/execute/commit) tutulur ve **uzun transaction DDL'i bekletebilir, bekleyen exclusive MDL sonraki işlemleri bloklar** → kural: DDL öncesi uzun transaction kalmamalı. **(3) Checksum drift:** Flyway uygulama anında CRC32 saklar, validate dosyayı yeniden hesaplar; uyuşmazlık = "dosya sonradan değişti" (BOM/CRLF/relative path değişimi dahil — Flyway #2503) ve `repair` yalnızca geçmişi **yeniden hizalar** (kod tarafını düzeltmez). **(4) Forward-only:** `down()` genelde hiç test edilmez ve veri düşürür; rollback script'i **yedek/restore'un yerine geçmez** (Redgate RCA); üretici ekipler çoğunlukla fix-forward'a eğilir, rollback yalnız "hiç canlı görmemiş DB / yıkıcı durum / local-test" için önerilir. **(5) Multi-DB:** her hedef DB kendi version tablosu + kendi migration klasörüyle izole edilir (Alembic: "each subfolder acts as a separate version history … own alembic_version table"); Flyway **hedef başına ayrı çağrı** ister ("no built-in deploy-to-many"), orchestrator/CI matrix sarmalar ve **ilk düğümde hata → rollout durur (fail-fast)**; her DB'de history tablosuna **DB-seviyesi kilit** alınır (eşzamanlı koşu güvencesi). |
| Web Search **Uzun Açıklama** | **(a) Expand-contract matrisi:** JusDB üç fazı (Expand → Migrate → Contract) ve dört "asla"yı verir: `NOT NULL`'u default'suz aynı deploy'da ekleme, rename etme, tüm referanslar kalkmadan kolon silme, tipi yerinde değiştirme — hepsi expand-contract'a gider; ReliablePenguin "big bang migration" başarısızlıklarının koordinasyon (kod ↔ veri) eksikliğinden geldiğini, appscale.blog ise lifecycle'ı *expand → dual-write/backfill → doğrulama → contract* olarak tanımlar ve **contract için beş şart** sayar (eski sürüm prod'da kalmamalı, kuyruk/uzun işler boşalmalı, replica/CDC/analitik kontrolü, rollback penceresi kapanmış olmalı, taze yedek alınmış olmalı). dev.to örneğinde eski-kolon-okuma tetikleyicisi (trigger) ile "transmutable" çift-senkron, contract'ta trigger fonksiyonuyla birlikte düşürülür; QuickBooks Engineering aynı fazi *dual write → doğrulama işi (verification job) → hedefe okuma* sırasıyla kurar ve **rollback'i yalnız faz geçişinde** kullanır. Backfill operasyonu her yerde aynı: batch + aralıklı uyku + idempotent `WHERE ... IS NULL` tekrarı. **(b) MySQL kilit mekaniği:** §17.12.1 tabloları hangi işlemin `INPLACE+NONE` ile yapılabildiğini listeleyen resmî kanıttır (`ADD COLUMN`, `DROP COLUMN`, `CHANGE/MODIFY COLUMN`, `ADD/DROP INDEX`, `ADD/DROP PRIMARY KEY` çoğu durumda `ALGORITHM=INPLACE, LOCK=NONE` ister — istisna: auto-increment kolon ekleme en az `LOCK=SHARED` ister, FULLTEXT indeksi `COPY`'ye düşebilir); §17.12.2 "daha kısıtlayıcı olmayan LOCK istenirse ifade **hata verir**" der → doğrulama = ifadeyi `LOCK=NONE` ile çalıştırma; aynı bölümün MDL örneği, `SELECT` açık transaction'ı tutan oturumun `ALTER`'i beklettiğini ve bekleyen exclusive MDL'in sonraki transaction'ları blokladığını gösterir. MariaDB "ALTER ONLINE TABLE = LOCK=NONE" eşdeğerliği ve "başlangıç/bitişte kısa exclusive lock" notuyla çaprazlanır. **(c) Checksum drift derinliği:** Flyway `validate` = ad/tip/checksum farkında başarısızlık; `repair` = uygulanmış kayıtları yeniden hizalamak + eksikleri silmek (**uç noktalar manuel temizlenir**); #2503 kanıtı: aynı dosya BOM eklenince/CRLF değişince checksum kırılır → drift'in çoğu "kasıtlı düzenleme" değil **biçim/encode değişimi**dir; Redgate hub makalesi "metadata-only değişiklik" ayrımını yapar. Redgate Flyway çoklu-DB sayfası ekler: **uygulanmış artefakt asla düzenlenmez** (düzenlemek sonraki validate'de mismatch üretir), düzenleme yerine **yeni migration** eklenir — bu bu ADR'nin "immutable + yeni satır" kuralının birebir kaynağı. **(d) Forward-only gerekçesi:** Jori Stein'in dört gerekçesi (rollback yalnız sırayla olur, veri dönüşü test edilmez, kolon drop'u geri gelmez, `down` yazmak için harcanan süre hiç kullanılmaz) + SQLServerCentral 2026 tartışması "veri değiştiyse rollback çoğu zaman imkânsız, fix-forward" der; oneuptime dört ayrı şeyi ("veri geri yükleme / şema reverse / trafik rollback / kod revert") ayırır ve **kod revert'i, yeni yazılan satırları okuyamayan eski binary ile eşleşmediğinde** tehlikeli olduğunu söyler; Redgate RCA en net cümlede "rollback script'leri sağlam backup/restore stratejisinin yerine geçmez — DROP edilen kolon backup'sız geri gelmez" ve "canlı prod'da ileriye gitmek denetim izini de korur" der. Buna karşılık rollback'in meşru olduğu iki yer: hiç canlıya çıkmamış DB ve yerel/test. **(e) Multi-DB orchestration:** Alembic gist'i iki DB için **ayrı klasör + ayrı version tablosu + `env.py` döngüsü** önerir (bir DB'nin migration'ı diğerinin geçmişine sızmasın); Flyway çoklu-DB sayfası "invoke edilir bir kez hedef başına, deploy-to-many yok, orchestrator sen sararsın; sequential + fail-fast varsayılan" ve "aynı DB'de eşzamanlı koşu DB-seviyesi kilit ile güvence altında" der; Bytebase CI/CD makalesi DDL/DML hedef kapsamını (tek DB / çoklu DB / grup), ortam zincirini (dev→test→staging→prod), yüksek riskli DDL için çok katmanlı onayı ve **schema drift tespitini** (pipeline dışı değişiklik alarmı) altı bileşenden biri sayar; CKAN/Alembic "iki head" senaryosu sıralama/branch çakışmasının neye benzediğini gösterir. |
| Web Search **Paragraf Veri Uzun** | expand-contract 3 faz (expand / backfill / contract) · asla rename, asla in-place type change, asla default'suz NOT NULL · dual-write + verification job + transmutable trigger · batch backfill + SLEEP + idempotent `WHERE IS NULL` · contract 5 şartı (eski sürüm kalmamalı, kuyruk boşalmalı, replica/CDC kontrolü, rollback penceresi kapalı, taze yedek) · iki aşamalı deploy · MySQL: `ALGORITHM=INPLACE, LOCK=NONE` = concurrent DML; desteklenmiyorsa **hata** = doğrulama; `LOCK=SHARED` DML'i kilitler; MDL 3 faz; uzun transaction DDL'i bekletir + bekleyen exclusive MDL sonrakileri bloklar; auto-increment kolon ekleme `SHARED` ister · checksum: CRC32 (Flyway) saklanır, validate yeniden hesaplar; BOM/CRLF/path değişimi = mismatch; `repair` = hizalar, kodu düzeltmez; uygulanmış artefakt **immutable**, çözüm yeni migration · forward-only: `down` test edilmez, drop geri gelmez, rollback ≠ backup/restore, fix-forward denetim izini korur; rollback yalnız local/test + hiç canlı görmemiş DB · multi-DB: DB başına ayrı klasör + ayrı version tablosu; hedef başına invoke; sequential + fail-fast; DB-seviyesi kilit; drift tespiti CI bileşeni · 18 DB = kısmi uygulama tespiti için `status` raporu şart. |
| Web Search **Sonucu** | 1) **Expand-contract doğrulandı** (6 kaynak: JusDB, ReliablePenguin, dev.to, Harness, appscale.blog, Medium/Chowdhury) → cross-DB değişiklik = expand → deploy (iki sürüm de okur) → contract; **iki aşamalı deploy tek deploy'da asla**. 2) **MySQL online DDL kuralı netleşti** (4 kaynak: MySQL 8.4 §17.12.1, MySQL 9.7 §17.12.1/§17.12.2, MySQL §15.1.9, MariaDB Online DDL) → LARGE TABLE `ALTER`'i `ALGORITHM=INPLACE, LOCK=NONE` ile **çalıştırılarak** doğrulanır; ifade hata verirse = desteklenmiyor → maintenance window + MDL hijyeni (uzun transaction kapatılır). 3) **Checksum drift mekanizması kanıtlandı** (5 kaynak: Flyway Validate, Flyway Repair, Flyway #2503, Redgate Validate/Repair, Redgate hub) → runner `checksum` sütunu + `verify` komutu zorunlu; **uygulanmış migration asla düzenlenmez**, düzeltme = yeni forward satır; `repair` yalnız onaylı. 4) **Forward-only desteklendi** (5 kaynak: Redgate RCA, SQLServerCentral 2026, oneuptime 2026, Medium/Laravel, Reddit r/SQL) → üretimde `down` yok; sorun = hızlı fix-forward; yedek/restore son savunma; `down` yalnız local/test (semantik olarak güvenli düşürmeler). 5) **Multi-DB bağımsız sequence doğrulandı** (4 kaynak: Alembic multi-DB gist, Redgate Flyway çoklu-DB, Bytebase CI/CD, CKAN migrations) → DB başına ayrı sequence + ayrı `schema_migrations` + tek orchestrator + sequential fail-fast + `status` raporu. |
| Web Search **Alınan Karar** | **ADR-014 KABUL EDİLİR — ÖZEL PHP RUNNER + DB-BAŞINA BAĞIMSIZ SEQUENCE + EXPAND-CONTRACT + FORWARD-ONLY + ONLINE DDL DOĞRULAMASI:** **(A) Aracı:** ORM'siz özel PHP migration runner (ADR-002 ile uyumlu) + versioned SQL dosyaları; **her DB kendi sequence'ine** sahiptir (bağımsız version numarası, DB başına `schema_migrations` tablosu: `version, name, checksum, executed_at, duration_ms, applied_by`). Runner: **dry-run** (plan listesi, yazma yok), **checksum doğrulama** (sha256 — değiştirilmiş/geçmişe müdahale edilmiş migration tespiti), **lock** (aynı anda tek runner — DB-seviyesi kilit), `status`/`verify` komutları. **(B) Sıralama/atomicite:** DB'ler birbirine **bağımsız** (ADR-003: DB arası FK yok → bağımsızlık doğal); cross-DB değişiklik = **expand-contract**: (1) expand — eski şema korunur, yeni alanlar eklenir + backfill, (2) deploy — yeni kod iki sürümü de okur, (3) contract — eski alan/sütun **yalnız herkes geçtikten sonra** düşürülür; **iki aşamalı deploy, tek deploy'da asla** (kaynak 1-6). **(C) Rollback:** **forward-only** — üretimde `down` migration yok (veri kaybı riski); sorun = hızlı **fix-forward** yeni migration (kaynak 16-20); istisna: semantik/tehlikesiz düşürmeler için `down` script yazılır ama **üretimde otomatik çalıştırılmaz** (yalnız local/test); yedek/restore son savunma hattıdır (ADR-081 reconciliation ruhu; kaynak 16). **(D) MySQL locking:** LARGE TABLE `ALTER` için `ALGORITHM=INPLACE, LOCK=NONE` **çalıştırılarak** doğrulama (desteklenmiyorsa ifade hata verir = kanıt), aksi halde maintenance window + uzun transaction temizliği (kaynak 7-10). **(E) Multi-DB orkestrasyon:** tek runner/CI job **hedef DB başına sırayla** koşar, ilk hata → durur (fail-fast), çıktı = DB×sequence `status` raporu (kaynak 21-24). |
| Web Search **Sonuç** | Karar 2025-2026 verisiyle **desteklendi ve netleşti**: expand-contract + backfill (6 kaynak), MySQL online DDL locking (4 kaynak — birincil resmî manuel), checksum drift/validate/repair (5 kaynak — birincil Flyway/Redgate), forward-only vs rollback (5 kaynak), multi-DB bağımsız sequence + orchestrator (4 kaynak) — **toplam ~24 adlandırılmış kaynak, 5 sorgu**; çapraz doğrulama ≥2 kaynak tüm iddialarda karşılanır. Vault bulguları §1.1'de ayrıldı: 18 dump + 2 script + forward-only şablonu/kuralı **IMPLEMENTED**; runner, numaralı seri, kilit/checksum/status **PLANNED** yazıldı; Phinx iddiası ve "36 script" **çelişki/⚠️** olarak işaretlendi, uydurulmadı. Karar maddelerinin her biri §2'de kaynak numarasıyla bağlandı. **Kaynak listesi (24):** 1) JusDB — MySQL Schema Migration Best Practices: Expand-Contract and Batch Backfills (2025-09) · 2) ReliablePenguin — Database Migrations Without Drama: Expand/Contract in Practice (2025-11) · 3) dev.to/Young Gao — Zero-Downtime Database Migrations: Patterns 2026 (2026-03) · 4) Harness — Zero Downtime Database Migrations: Safe Schema Changes (2026-07) · 5) appscale.blog — Zero-Downtime DB Migration Architecture: Expand-Contract/Backfill 2026 · 6) Medium/Rigan M. Chowdhury — Expand and Contract (2025-08) · 7) MySQL 8.4 Reference Manual §17.12.1 Online DDL Operations · 8) MySQL 9.7 Reference Manual §17.12.1 + §17.12.2 (LOCK klasörü, MDL fazları) · 9) MySQL Reference Manual §15.1.9 ALTER TABLE (ALGORITHM/LOCK sözdizimi) · 10) MariaDB Docs — InnoDB Online DDL Overview (ALTER ONLINE TABLE = LOCK=NONE) · 11) Flyway (GitHub) — Validate.md (CRC32 checksum) · 12) Flyway (GitHub) — Repair.md (checksum hizalama) · 13) Flyway issue #2503 — BOM/CRLF checksum mismatch · 14) Redgate Flyway Docs — Rolling out updates from a single schema to multiple production databases (immutable artefact, hedef başına invoke, DB-seviyesi kilit, sequential fail-fast) · 15) Redgate Flyway Docs — Migrations (drift kontrolü, transaction sarmalama) · 16) Redgate RCA — Implementing a roll back strategy (rollback ≠ backup/restore; forward fix prod) · 17) SQLServerCentral — Rollback vs. Roll Forward (2026-03) · 18) oneuptime — Can You Safely Roll Back a Database Change? (2026-08) · 19) Medium/Jori Stein — Laravel: Why you should not rollback (`down()`) · 20) Reddit r/SQL — Forward-only schema evolution vs rollbacks (2025-09) · 21) Alembic multi-DB best practices (gist/Bharathvaj Ganesan) — ayrı klasör + DB başına version tablosu · 22) Bytebase — How to Build a CI/CD Pipeline for Database Schema Migration (2025-10) · 23) QuickBooks Engineering — Doing data migrations without risk (dual-write + verification job) · 24) CKAN Docs — Database migrations (Alembic history/iki head senaryosu). |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-002 (PDO zorunlu, ORM yok) | Runner doğrudan PDO/`DatabaseManager` üzerinden çalışır; Phinx/Doctrine benzeri ORM-içi migration katmanı **kurulmaz** (composer'da zaten yok — §1.1) |
| ADR-003 (18 DB BCNF, DB arası FK yok) | Cross-DB transaction **yoktur**; her DB'nin migration'ı kendi transaction'ında koşar — bağımsızlık bu kararın dayanağı |
| ADR-040 (düz metin — dosya diskte YOK) | 18 DB şema otoritesi referans olarak düz metin anılır; `ADR-0xx` biçimli wiki-link **kurulmaz** (kanıt: `.ai/.decisions/**` glob) |
| ADR-081 (reconciliation/backup ruhu) | Forward-only'in son savunma hattı yedek/restore'dır; `down` script'i onun yerine geçmez (kaynak 16) |
| Frozen ADR-001-037 dokunulmaz | Bu ADR yeni karar üretir; frozen metinler okunur/referanslanır, değiştirilmez (AGENTS.md §25.3 kural 2) |
| In-Place Refactoring | Mevcut `shared/database/migrations/oauth_*.php` dosya adları **onaysız değiştirilemez**; yeni seri yeni dosya/adla eklenir |
| `.ai/log.md` append-only | Bu işlem dahil tüm kayıtlar yalnız ekleme ile yazılır |
| REDACTED | Secret/credential/connection string hiçbir koşulda bu ADR'ye yazılmaz |
| Türkçe + Guardrail #16 | 7 bölüm + §1.3 9 alan şablondan gelir; slug İngilizce, metin Türkçe, mojibake yasak |

---

## 2. Karar (Decision)

CoreMusic'te şema değişimi **özel PHP migration runner + versioned SQL dosyaları** ile yürütülür ve **her veritabanı kendi bağımsız version sequence'ine** sahiptir: DB başına ayrı migration klasörü, 1'den başlayan kendi numaralandırması ve kendi `schema_migrations` tablosu. Uygulama sırası/atomicite **DB'ler arasında bağımsızdır**; **cross-DB değişiklikler expand-contract** ile iki aşamalı deploy'da yapılır (tek deploy'da asla). Rollback **forward-only**'dir: üretimde `down` migration çalışmaz, sorun **fix-forward** ile yeni bir migration'da çözülür; `down` script yalnız local/test içindir ve büyük tablo DDL'si **`ALGORITHM=INPLACE, LOCK=NONE` doğrulamasından** geçmeden üretimde koşmaz.

### 2.1 Neden Bu Seçenek?

- **ORM yok, araç da ORM'siz olmalı:** ADR-002 doğrudan PDO zorunlu kılar; composer'da Phinx/Doctrine **kurulu değil** (§1.1 `grep composer` = 0) → hazır kütüphane getirmek yeni bağımlılık + mevcut 2 script'in formata dönmesi demektir. Runner, `.ai/.templates/infrastructure/migration-template.md` zaten tanımladığı **forward-only SQL** kalıbıyla yazılır (şablon ADR-014 bekliyor).
- **18 DB bağımsız → sequence de bağımsız (kaynak 21, 14):** ADR-003 gereği DB arasında FK yok; tek global sequence kısmi uygulamada tek noktada kilit üretir ve bir DB'nin geride kalmasını tüm sisteme yayar. DB başına `schema_migrations` + ayrı klasör, bir DB'nin geçmişinin diğerine sızmasını engeller.
- **Atomicite sınırı = tek DB:** cross-DB transaction yokken "iki DB'yi aynı transaction'da güvenli değiştirmek" iddiası gerçekçi değil; expand-contract bunun yerine **kod tarafında uzlaşım** sağlar: eski ve yeni şema bir süre birlikte okunabilir kalır (kaynak 1, 5).
- **Forward-only gerçekçiliği (kaynak 16-20):** `down()`'lar test edilmez, DROP'lar geri gelmez, rollback script'i yedeğin yerine geçmez; fix-forward hem daha hızlı hem denetim izini korur. İstisna (semantik güvenli düşürmeler) local/test'te bırakılır.
- **Lock doğrulaması elle değil ifadeyle (kaynak 7-10):** `ALGORITHM=INPLACE, LOCK=NONE` desteklenmiyorsa MySQL **hata verir** → "büyük tablo kilitlenmedi" kanıtı çalıştırma sonucundan gelir, tahminden değil.
- **Zero Hallucination (ADR-005):** her madde dosya yolu/satır ile (IMPLEMENTED/PLANNED) veya §1.3 kaynağıyla kanıtlandı; doğrulanamayanlar `⚠️ VERIFICATION REQUIRED`.

### 2.2 Teknik Detaylar

#### 2.2a Dosya Yapısı ve Adlandırma (PLANNED — bugün 0 numaralı dosya)

```text
shared/database/migrations/
├── CLAUDE.md                          # mevcut — forward-only kuralı (satır 19) IMPLEMENTED
├── oauth_states_migration.php         # mevcut — $queries dizisi (onaysız AD DEĞİŞMEZ)
├── oauth_connections_migration.php    # mevcut
├── coremusic_auth/                    # YENİ — DB başına kendi sequence'i
│   ├── 0001_baseline.sql              # dump'tan türetilen başlangıç (opsiyonel)
│   ├── 0002_add_login_attempts.sql
│   └── 0003_add_oauth_states.sql      # mevcut script'in SQL'e dönüştürülmüş hâli
├── coremusic_user/  0001_*.sql …
└── … 18 klasör (her DB için)
```

| Kural | Karar |
|-------|-------|
| Adlandırma | `NNNN_<slug>.sql` — 4 hane, **her DB'de 0001'den başlar** (bağımsız sequence); sıralama yalnız kendi klasöründe |
| Dosya içeriği | Yalnız **up** SQL (forward-only); başlıkta `-- @id`, `-- @checksum-source` (sha256) yorum satırı |
| İsim = sözleşme | Dosya adı yazıldıktan sonra **değiştirilmez** (checksum kırılır → §4.3 risk 1) |
| Mevcut 2 PHP script | Adları korunur; içerik SQL serisine **yeni dosya olarak** taşınır (eski dosya silinmez/yeniden adlandırılmaz — In-Place Refactoring) |
| Baseline | İlk `0001_baseline.sql`, `.ai/.sql/mysql/<db>.sql` dump'ından üretilir; dump = şema SSOT (ADR-003/040), migration = **değişiklik** geçmişi |

#### 2.2b Runner Mimarisi (PLANNED — `shared/src/Database/Migration/`)

```text
php bin/migrate.php  --db=coremusic_auth  [--dry-run] [--status] [--verify] [--target=NNNN]
        │
        ├── 1. LOCK    : DB-seviyesi kilit alınır (aynı anda tek runner) ── alınamazsa → BEKLE/DUR
        ├── 2. VERIFY  : schema_migrations.checksum ↔ dosya sha256 ── UYUŞMAZLIK → DUR (drift)
        ├── 3. PLAN    : klasördeki NNNN dosyaları − uygulanmış = beklemedekiler (sıralı)
        ├── 4. (--dry-run) → planı yazdır, ÇIK (hiçbir DDL çalışmaz)
        ├── 5. APPLY   : her dosya tek transaction'da koşulur + süre/checksum kaydı
        └── 6. REPORT  : DB × sequence × status çıktısı (18 DB tek bakışta)
```

| # | Özellik | Davranış | Dayanak |
|---|---------|----------|---------|
| 1 | **Bağımsız sequence** | Her DB'nin kendi `schema_migrations` tablosu: `version (PK), name, checksum, executed_at, duration_ms, applied_by` | kaynak 21, 14 |
| 2 | **Dry-run** | `--dry-run` yalnız plan üretir; DDL çalışmaz (kaynak 24 "evaluation mode" ruhu) | §1.3 s.24 |
| 3 | **Checksum doğrulama** | `sha256( dosya )` kaydedilir; `--verify`/her koşuda karşılaştırılır → **uyuşmazlık = dur** (değiştirilmiş geçmiş migration tespiti) | kaynak 11, 13, 14 |
| 4 | **Lock** | Aynı DB'de tek runner (DB-seviyesi kilit / kilit satırı); ikinci runner **bekler, kilit boşalınca koşar ya da zaman aşımında çıkar** — eşzamanlı iki koşu yok | kaynak 14 |
| 5 | **Idempotent** | Uygulanmış version tekrar uygulanmaz (`NOT EXISTS` kontrolü); `CREATE ... IF NOT EXISTS` tek başına yeter sayılmaz | kaynak 15 |
| 6 | **Sequential + fail-fast** | Birden çok DB hedefi tek tek, **sırayla**; ilk hata → rollout durur (kısmi uygulama izole kalır) | kaynak 14, 22 |
| 7 | **Atomicite** | Tek migration = tek DB transaction'ı; DDL bazı durumlarda implicit commit eder → kritik dosyalar **tek statement** kalabalığından kaçınılır ve `--verify` sonrası gözden geçirilir | MySQL implicit DDL commit |
| 8 | **Immunity** | Uygulanmış dosya düzenlenmez; hata = **yeni** `NNNN+1` fix-forward satırı | kaynak 14, 16 |

#### 2.2c Sıralama, Atomicite ve Expand-Contract (cross-DB)

```text
AŞAMA 1 — EXPAND (deploy #1, eski kod hâlâ üretimde)
   DB-A: ALTER TABLE t ADD COLUMN new_col …        ← eski kolon/dışlar KORUNUR
   DB-B: ALTER TABLE u ADD COLUMN new_ref …        ← bağımsız sequence, kendi transaction'ında
   backfill: batch + idempotent (WHERE new_col IS NULL) + aralıklı uyku   [kaynak 1, 3]
   ★ DB'ler arası transaction YOK (ADR-003) → her DB kendi sequence'inde durur/ilerler

AŞAMA 2 — DEPLOY (kod) : yeni sürüm hem eski hem yeni alanı OKUR/YAZAR (dual-read/write)
   ★ bu pencere KASITLIDIR: iki sürüm birlikte yaşar [kaynak 1, 5, 23]

AŞAMA 3 — CONTRACT (deploy #2 — AYRI deploy, ASLA tek deploy'da değil)
   şartlar: eski sürüm prod'da kalmadı · kuyruk/uzun işler boşaldı · replica/CDC/analitik
   kontrolü · rollback penceresi kapandı · taze yedek alındı          [kaynak 5]
   DB-A: ALTER TABLE t DROP COLUMN old_col …        ← ancak bu 5 şartla
```

| Kural | İfade |
|-------|-------|
| Bağımsızlık | Her DB kendi `NNNN` sequence'ini uygular; bir DB başarısız olursa **diğerleriyle senkron geri alınmaz** (zaten bağımsız) |
| Cross-DB değişiklik | **Expand → deploy → contract**; sözleşme gereği **iki deploy** |
| Uyumluluk penceresi | Deploy #1 ile #2 arasında kod **iki sürümü de** okuyabilir (backward-compatible kolonlar: nullable/default'lu) |
| Contract erken düşürme | **YASAK** — eski kod hâlâ okuyorsa `DROP` üretim arızası üretir (§4.3 risk 3) |
| Tek deploy'da rename/type-change | YASAK — her zaman add-new → migrate → drop-old (kaynak 1: "Never rename columns") |

#### 2.2d Rollback: Forward-Only + İstisna

| Durum | Davranış |
|-------|----------|
| Üretimde hata | **Fix-forward**: yeni `NNNN` migration ile düzelt (en geç 1 iş günü) — `down` çalışmaz |
| `down` script | **Yalnız local/test**; semantik olarak tehlikesiz düşürmelerde (ör. geçici indeks, boş tablo) yazılır ama üretimde **otomatik koşmaz**; koşulması manuel onay + runbook gerektirir |
| Veri kaybı riski taşıyan değişiklik | Expand-contract'a zorlanır; contract = kasıtlı veri düşürme, **yedek alınmış** olmalı |
| Son savunma | **Yedek + point-in-time restore** (ADR-081 reconciliation/backup ruhu; kaynak 16: "rollback script'leri backup'ın yerine geçmez") |
| Local/test | `--down` (varsa) ile tekrar tekrar kurulabilir — üretimle aynı kod yolu değil, ayrı mod |

#### 2.2e MySQL Locking Doğrulaması (LARGE TABLE)

```sql
-- Kural: üretim DDL'si bu biçimde "çalıştırılarak" doğrulanır; ifade hata verirse DESTEKLENMİYORDUR.
ALTER TABLE coremusic_logs.events
  ADD COLUMN trace_id CHAR(36) NULL,
  ALGORITHM=INPLACE, LOCK=NONE;      -- [kaynak 7-9]
```

| # | Kural | Detay |
|---|-------|-------|
| 1 | Doğrulama yöntemi | `ALGORITHM=INPLACE, LOCK=NONE` ile çalıştır; MySQL desteklemiyorsa **hata döndürür** → tahmin yok, kanıt var (kaynak 8: kısıtlayıcı olmayan LOCK istenirse ifade başarısız olur) |
| 2 | Desteklenmiyorsa | **Maintenance window** + kapalı pencerede koşu; pencere öncesi uzun transaction/MDL tutan oturumlar kapatılır (kaynak 8 MDL fazları) |
| 3 | Yasaklı/mahcup işlemler | Auto-increment kolon ekleme en az `LOCK=SHARED` (DML durur) → expand-contract ile ayrılır; FULLTEXT/spatial `COPY`'ye düşebilir |
| 4 | Bekleme zinciri | Uzun-running transaction DDL'i bekletir, bekleyen exclusive MDL sonraki transaction'ları bloklar → DDL öncesi `information_schema.innodb_trx` gözlemi |
| 5 | Backfill | Batch + `SLEEP` + idempotent tekrar (kaynak 1, 3); tek seferlik dev `UPDATE` yasak |

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Hazır kütüphane (Phinx)** | Olgun, test edilmiş rollback/checksum | composer'da **kurulu değil** (§1.1 grep=0); mevcut 2 script `$queries` formatı → hepsi yeniden yazılır; ek bağımlılık + kendi konfigürasyon katmanı; 18 DB bağımsız sequence için yine de sarmalayıcı gerekir | YAGNI + ADR-002 ruhu: gereksinim (bağımsız sequence, dry-run, forward-only) runner'ın **kendisiyle** karşılanır; "araç yoksa araç PLANNED" (§1.1 windows-software-engineer kuralı) |
| 2 | **Doctrine Migrations** | `diff` ile şema üretimi | Symfony/DI bağımlılığı, ORM kalıbı ADR-002 (no-ORM) ile gerilir; doctrine composer'da yok | ADR-002 ihlal riski + yeni ekosistem bağımlılığı |
| 3 | **Tek global sequence (18 DB tek şema geçmişi)** | Tek `status` tablosu, basit sayısal sıra | Kısmi uygulama kilitlenir; bir DB geride kalınca herkes beklemez ama sıra **yanlış yorumlanır**; ADR-003'te DB arası FK olmadığından senkron gerekmez | Bağımsızlık gerçek (ADR-003); global sıra sentetik bir senkronizasyon zorunluluğu yaratır (kaynak 21: ayrı klasör + ayrı version tablosu) |
| 4 | **Her migration için `down` + üretimde rollback** | psikolojik "geri dönüş düğmesi" | `down`'lar test edilmez; DROP/transform geri alınamaz; rollback ≠ backup (kaynak 16); üretim rollback'i çoğu zaman veri kaybı üretir | **Forward-only + fix-forward** (kaynak 16-20); `down` yalnız local/test (§2.2d) |
| 5 | **Migration'sız hotfix (canlıda elle DDL)** | anında çözüm | Audit yok, drift üretir, 18 DB'de tutarsızlık → "hangisi hangi ortamda var" bilinmez | Bytebase drift tespiti tam bu sorunu işaretler (kaynak 22); tek yazma arayüzü ilkesi ihlali |
| 6 | **Büyük tablo için online DDL aracı (gh-ost/two-step copy)** | `LOCK=NONE`'a düşmeyen işlemler için kopya-tablo akışı | Ek binary/ops bağımlılığı, ilk aşamada fazla karmaşa | **Reddedilmedi — yedektir:** §2.2e'de `LOCK=NONE` başarısız olursa önce maintenance window, tekrarlanan büyük-volume işlemlerde gh-ost benzeri araç yeniden değerlendirilir (opsiyonel hattı açık tutar) |

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Değişiklik geçmişi:** her şema değişikliği numaralı, checksum'lı, uygulama zamanı kayıtlı dosya olur → **drift tespiti** mümkün (`--verify`), denetlenebilirlik artar.
- **18 DB tek bakışta:** DB × sequence × status raporu kısmi uygulamayı ve geride kalan DB'yi anında gösterir (fail-fast rollout ile).
- **Kesintisizlik:** expand-contract + online DDL doğrulaması ile büyük tablo `ALTER`'leri kilitlenmeden koşar; bakım penceresi yalnız gerçekten ihtiyaç duyulan işlemde açılır.
- **Güvenli geri dönüş mantığı:** forward-only, hiç test edilmemiş `down`'ların üretide patlamasını engeller; yedek/restore açıkça son savunma olarak tanımlanır (ADR-081).
- **ADR-002 uyumu:** ORM/kütüphane eklenmeden, mevcut `DatabaseManager`/PDO üstünde çalışır; mevcut forward-only şablonu ve `migrations/CLAUDE.md` kuralıyla **çelişen yeni metin kalmaz**.

### 4.2 Olumsuz Sonuçlar

- **Runner'ı biz yazıyoruz:** hazır kütüphanenin olgunlaştığı test/checksum/edge-case yüzeyi bizim sorumluluğumuz → ilk sürümde kapsam dar tutulur (dry-run/verify/lock/status), özellik genişletmesi sonraya kalır.
- **18 klasör/sequence = operasyonel karmaşıklık:** paralel geliştirmede çakışan `NNNN` numaraları **aynı DB içinde** mümkündür → PR'da sıralama kontrolü (CI) gerekir.
- **Uyumluluk penceresi borç üretir:** expand ile contract arasında kod iki formatı da taşır; contract geç kalırsa bu borç kalıcılaşır (runbook + gözden geçirme gerekli).
- **Backfill maliyeti:** büyük tablolar batch'lerde saatlerce sürebilir → job izleme, idempotentlik ve tekrar çalıştırılabilirlik ek iş.
- **Spec temizliği işi:** k5 `migration-strategy.md` (backward zorunluluğu, tek global `schema_migrations`, `SELECT *` örneği) ve "Phinx/36 script" vault iddiaları bu ADR ile **çelişir** → güncelleme ayrı vault işlemi (§5.1).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Checksum drift** — uygulanmış migration dosyası sonradan değişir (BOM/CRLF/yzimson düzeltmesi/yeniden adlandırma) → `verify` her koşuda uyarır ve iş durur (kaynak 13) | 3 (olası) | 3 (orta) | Dosya **immutable**; düzenleme yerine yeni `NNNN+1` (kaynak 14); adlandırma değişikliği yasak; UTF-8 BOM'suz yazım zorunlu (vault-utf8-writer); `repair`/yeniden hizalama **yalnız onaylı** ve log'lanır |
| **Uzun backfill** — 18 DB'de büyük tablo batch'leri replikasyon/performans baskısı üretir (kaynak 1, 3) | 3 (olası) | 4 (yüksek) | Batch + `SLEEP` + idempotent `WHERE ... IS NULL`; hız/kalıntı metrikleri + lag alarmı; backfill'i expand'tan **ayrı iş** olarak planla; tek seferlik dev `UPDATE` yasak |
| **Contract erken düşürme** — eski sürüm prod'dayken `DROP` → kırılan eski kod / veri kaybı (kaynak 5 contract şartları) | 3 (olası) | 4 (yüksek) | Contract = **ayrı deploy** + 5 şartlı checklist (eski sürüm kalmadı, kuyruk boşaldı, replica/CDC kontrolü, rollback penceresi kapandı, taze yedek); checklist imzasızsa `DROP` çalışmaz |
| **18 DB operasyonel yükü** — kısmi uygulama, geride kalan DB, paralel `NNNN` çakışması | 4 (çok olası) | 3 (orta) | Tek orchestrator sequential + fail-fast (kaynak 14); `status` raporu zorunlu çıktıdır; CI'da sıra/numara kontrolü; ilk hata → rollout durur |
| **Runner bugün yok (PLANNED)** — karar kâğıtta kalır, şema değişimi elle yapılmaya devam eder | 4 (çok olası) | 4 (yüksek) | §5.1 adım 1-2 (MVP: lock+verify+dry-run+status) en yüksek öncelik; `.ai/scripts/database/` = 0 bulgusu ⚠️ (idddia doğrulanamadı) |
| **MDL/lock bekleme** — uzun transaction `ALTER`'i bekletir, bekleyen exclusive MDL sonrakileri bloklar (kaynak 8) | 3 (olası) | 3 (orta) | DDL öncesi aktif transaction kontrolü; `LOCK=NONE` doğrulaması başarısızsa maintenance window (§2.2e); `lock_wait_timeout` sınırı |
| **Vault iddia-kod çelişkileri** — "2 Phinx migration", "36 script", glossary "ADR-014 frozen" | 4 (çok olası) | 2 (düşük) | §1.1'de işaretli; düzeltme §5.1 adım 6'da ayrı append-only işlem; bu ADR yazıldıktan sonra glossary `frozen` ifadesi düzeltilecek |

### 4.4 Vault Çapraz Referans

| Kaynak | İlişki |
|--------|--------|
| [[ADR-002-pdo-mandatory-no-orm]] | Runner ORM'sizdir, doğrudan PDO/`DatabaseManager` kullanır; Phinx/Doctrine alternatifleri bu yüzden ret (§3 1-2) |
| [[ADR-003-multi-db-bcnf]] | 18 BCNF DB + **DB arası FK yok** → migration bağımsızlığının dayanağı; ADR-003 satır 88 ve satır 198'deki "tek migration orkestratörü" ve "eski seri ADR-014 düz metin" notu bu ADR ile tamamlanır |
| ADR-040 (düz metin — dosya diskte YOK) | 18 DB şema otoritesi; dump = şema SSOT, migration = değişiklik geçmişi ayrımının sahibi. **Wiki-link kurulmaz** (glob kanıtı) |
| [[ADR-081-multi-provider-data-sync]] | Yedek/restore + reconciliation ruhu → forward-only'in **son savunma hattı** (§2.2d); veri tutarsızlığında onarım disiplini buradan gelir |
| [[../../architecture/k5-veri-yonetimi/migration-strategy.md]] | MigrationManager **spec'i** — `rollback()/down` (satır 59-72) + tek global `schema_migrations` (196-201) + "forward ve backward olmalıdır" (satır 16) bu ADR ile **SUPERSEDED**; sınıf gövdesi PLANNED kalır |
| [[../../.templates/infrastructure/migration-template.md]] | Forward-only şablonu (satır 26, 74, 384, 422, 451) bu ADR'nin uygulayıcısı — ADR yazıldıktan sonra şablon↔ADR çelişkisi kalmaz |
| `.ai/.agents/data-engineer.md` | "2 Phinx migration" iddiası ↔ kod çelişkisi (§1.1) → düzeltme §5.1 adım 6 |
| `.ai/glossary.md:440` | "ADR-014 frozen" iddiası ↔ bu ADR `accepted` → düzeltme §5.1 adım 6 |

---

## 5. Uygulama (Implementation)

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Runner MVP (PLANNED → IMPLEMENTED):** `shared/src/Database/Migration/` + `bin/migrate.php` — DB başına `schema_migrations` (checksum sütunlu), `--dry-run`, `--verify`, `--status`, lock, sequential fail-fast (§2.2b) | Data Engineer + Backend Architect | 2-3 oturum |
| 2 | **Adlandırma + klasör iskeleti:** 18 DB klasörü + `NNNN_<slug>.sql` kuralı; `.ai/.sql/mysql/<db>.sql` dump'ından `0001_baseline.sql` üretimi; mevcut 2 PHP script **adları korunarak** seriye eklenir (§2.2a) | Data Engineer | 1-2 oturum |
| 3 | **İlk gerçek migration:** `oauth_states`/`oauth_connections` için SQL serisine karşılık yazımı + local'de dry-run → verify → uygulama (kural: üretim `down` YOK) | Data Engineer | 1 oturum |
| 4 | **Online DDL kuralı + runbook:** `ALGORITHM=INPLACE, LOCK=NONE` doğrulama adımı, başarısızlıkta maintenance window akışı, MDL kontrolü, backfill batch şablonu (§2.2e) | Data Engineer + DevOps Engineer | 1 oturum |
| 5 | **Expand-contract checklist:** 5 şartlı contract formu (deploy #2 öncesi imza) + iki aşamalı deploy talimatı (§2.2c) | Backend Architect + DevOps Engineer | 0.5 oturum |
| 6 | **Vault düzeltmeleri:** `data-engineer.md` "Phinx" iddiası ↔ composer bulgusu; `adr-database-template.md` "36 script" ⚠️; `glossary.md:440` "frozen" → `accepted`; k5 `migration-strategy.md` "backward olmalıdır" → bu ADR'ye atıf/superseded notu — **hepsi append-only/ayrı işlem** | Vault Steward | 1 oturum |
| 7 | **Testler:** dry-run hiçbir DDL çalıştırmaz · checksum uyuşmazlığı → exit≠0 · ikinci runner lock ile durur · kısmi uygulanmış DB `status`'ta görünür · batch backfill idempotent tekrar · `LOCK=NONE` desteklenmeyen işlemde hata → maintenance window dalı | QA Engineer | 1-2 oturum |
| 8 | **CI kapısı:** PR'da migration dosya adı deseni + numara çakışması + BOM/CRLF kontrolü; deploy'da `migrate --dry-run` → `migrate` → `--verify` (sequential, fail-fast) | DevOps Engineer | 1 oturum |

### 5.2 Geri Dönüş Planı

1. **Runner (adım 1):** kod `git revert` edilir; `schema_migrations` tablosu **olduğu gibi kalır** (bilgi kaybı yok, yazma durur) → şema değişimi geçici olarak elle/DIŞI runbook ile yapılır; tablo silinmez. Runner tekrar devreye alınıp `--status` ile geçmiş okunur.
2. **Adım 2-3 (dosya/klasör):** yeni klasör/dosyalar eklendiği için silme = yalnız yeni eklenenlerin kaldırılması; **mevcut `oauth_*.php` dosyalarına dokunulmaz** (In-Place Refactoring) → eski hâline dönmek riskizdir.
3. **Expand-contract yanlış gidiyorsa (contract öncesi):** contract atlanır, eski alanlar **dokunulmamış** olarak kalır (zaten expand fazında korunmuştur) → sistem eski+new birlikte okuyan pencerede bekler; sorun fix-forward ile çözülür. **Contract sonrası geri dönüş = `down` DEĞİL** → yedek/restore (ADR-081) + yeni forward migration.
4. **Veri kaybı riski olan migration üretimde koştuysa:** derhal durdur → taze yedekten point-in-time restore (son savunma) → neden analizi → düzeltme yeni forward satırı olarak eklenir; `down` ile "geri alma" **denemez** (kaynak 16).
5. **Vault bozulması:** her adımdan sonra `.ai/log.md` append + `git diff --stat -- .ai/`; bozulma → `vault-utf8-writer.mjs repair` + `git checkout` (ESki satıra dokunulmaz).

### 5.3 Debate Kaydı

| Alan | Değer |
|------|-------|
| Durum | **✅ TAMAMLANDI** — 3 tur / 20 persona · 18 kabul / 2 çekimser / 0 red → **KABUL** (2026-09-24) |
| Karar içeriği | Tümü **kullanıcı onaylı / Önerilen** (üst görev kapsamı) |
| Beklenen biçim | ADR-004/008/010/011/012/013 formatı — 3 tur / 20 persona (kayıt §7.1'e taşındı) |
| Tech Lead | **✅** — debate sonrası onay (2026-09-24) |

> **Kural:** debate tamamlanmadan bu ADR `frozen` yapılmaz; debate sonuçları §5.3/§7.1 ve frontmatter `debate` alanına işlenir, `.ai/log.md` append ile kaydedilir.

### 5.4 Debate Şartları (Kabul Koşulları — 3/3)

| # | Şart | Kapsam | Durum | Kanıt |
|---|------|--------|-------|-------|
| 1 | **Migration runner iskeleti** | `shared/src/Database/Migration/` + `bin/migrate.php` — dry-run, checksum (sha256), DB-seviyesi lock, status/verify (§2.2b); bugün kod YOK (PLANNED) | ⏳ PLANNED → §5.1 adım 1 | Debate Tur 2 itiraz 1 |
| 2 | **Vault iddia düzeltmeleri** | k5 `migration-strategy.md` → ⚠️ SUPERSEDED notu + ADR-014 link · `glossary.md:440` "frozen" → accepted/debate ✅ · `data-engineer.md` "2 Phinx migration" → Phinx YOK + 2 PHP `$queries` + runner PLANNED (ADR-014) | ✅ UYGULANDI (2026-09-24, bu debate turu) | Debate Tur 2 itiraz 2-4 |
| 3 | **INPLACE doğrulama + checksum drift CI testi** | `ALGORITHM=INPLACE, LOCK=NONE` doğrulama adımı + CI'da checksum uyuşmazlığı → exit≠0 testi (§2.2e, §5.1 adım 7-8) | ⏳ PLANNED → §5.1 adım 7-8 | Debate Tur 2-3 şartı |

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[CLAUDE.md]] | Karar alt registry kuralı (accepted/ dizin sözleşmesi) |
| [[../index]] | Karar dizini — §3 satır 51 `[[ADR-014-multi-db-migration-strategy]]` (slug eşleşmesi ✅) |
| [[../../CLAUDE.md]] | Vault ana sözleşmesi — 16 Hard Guardrail, REDACTED, Guardrail #16 |
| [[../../AGENTS.md]] | Onay akışı §10, frozen kuralı §25.3, routing §6 (`migration` → Data Engineer), §24.3 Data okuma listesi |
| [[../../WORKFLOW.md]] | Debate/onay akışı bağlamı |
| [[../../brain.md]] | Mimari karar özeti — satır 969 `ADR-014 | Forward-only, versioned migration` (bu ADR ile hizalı ✅) |
| [[../../keys.md]] | Keyword haritası — satır 88 `migration, schema degisikligi → ADR-014` ✅ |
| [[../../index.md]] | Master katalog — satır 631 ADR-014 kaydı ✅ |
| [[../../glossary.md]] | Satır 440 "frozen" iddiası → §5.1 adım 6 düzeltmesi |
| [[../../log.md]] | Audit trail — bu işlem tek satır append |
| [[ADR-002-pdo-mandatory-no-orm]] | ORM'siz runner dayanağı (dosya diskte VAR ✅) |
| [[ADR-003-multi-db-bcnf]] | 18 DB + FK yok → bağımsız sequence dayanağı (dosya diskte VAR ✅) |
| [[ADR-081-multi-provider-data-sync]] | Yedek/restore + reconciliation — forward-only son savunma (dosya diskte VAR ✅) |
| ADR-040 (düz metin) | 18 DB şema otoritesi — dosya diskte YOK, wiki-link kurulmaz (glob kanıtı) |
| [[../../architecture/k5-veri-yonetimi/migration-strategy.md]] | MigrationManager spec'i — `down`/tek global tablo iddiaları SUPERSEDED (PLANNED kod) |
| [[../../.templates/infrastructure/migration-template.md]] | Forward-only migration şablonu (ADR-014 atıflı — uygulayıcı) |
| [[../../.templates/adr/adr-template.md]] | Bu ADR'nin şablonu (Guardrail #16) |
| `shared/database/migrations/` | 2 mevcut script (`oauth_states`, `oauth_connections`) + forward-only kuralı (`CLAUDE.md:19`) — IMPLEMENTED |
| `.ai/.sql/mysql/` | 18 dump — şema SSOT; numaralı migration serisi **0 dosya** (PLANNED) |
| `.claude/skills/prompt-maker/references/10-web-research-protocol.md` | §1.3 web araştırma protokolü (diskte VAR ✅) |
| Debate şartları (§5.4) | 3 kabul şartı: (1) migration runner iskeleti, (2) k5 SUPERSEDED + glossary/data-engineer düzeltmeleri ✅, (3) INPLACE doğrulama + checksum drift CI testi |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "ADR-014'ü sıfırdan yaz"; karar içeriği tamamı Önerilen/onaylı) | 2026-09-24 | ✅ |
| Tech Lead | ✅ — debate KABUL (3 tur / 20 persona, 18/2/0) | 2026-09-24 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### 7.1 Debate Kaydı

| Alan | Değer |
|------|-------|
| Biçim | ADR-004/008/010/011/012/013 formatı — 3 tur / 20 persona |
| Durum | **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** — 2026-09-24 |
| Tur 1 | **20 persona** — bulgular: 18 dump · 0 migration · runner yok (2 PHP `$queries` dosyası) · Phinx composer'da yok; 3 doküman çelişkisi. Oy: **14 kabul/neutral, 4 uyarı**; **Critic:** `k5-veri-yonetimi/migration-strategy.md` **superseded işareti şart**. |
| Tur 2 | **İtiraz → çözüm:** (1) runner yok → PHP runner iskeleti (dry-run/checksum/lock) → **şart 1**; (2) k5 dokümanı çelişiyor → superseded işareti + ADR-014 link → **şart 2**; (3) `glossary.md:440` "frozen" yanlış → bu turda düzeltildi; (4) `data-engineer.md` "2 Phinx migration" kodda yok → claim-code düzeltme → **şart 2**'ye bağlandı. |
| Tur 3 | **Oy: 18 kabul / 2 çekimser / 0 red → KABUL** |
| Sonuç | **KABUL** — 3 şart (§5.4): (1) migration runner iskeleti, (2) k5 superseded + glossary/phinx iddia düzeltmeleri, (3) INPLACE doğrulama + checksum drift CI testi |
| Tech Lead | **✅** (2026-09-24) |
| Not | Durum `accepted` (kullanılabilir, frozen YOK); debate §5.3 + §5.4 + §7.1 + frontmatter `debate` alanına işlendi, `.ai/log.md` append ile kaydedildi |

---

*1.0.0 | 2026-09-24 | Created*
*Authority: ADR-014 Karar Metni — CoreMusic Architecture Decision Record*
*Mode: Red Team · Human Mode · Truth Mode*
