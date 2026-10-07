---
title: "CoreMusic — R-009: Tek (Single) Veritabanı Mimarisi (REDDEDİLMİŞ — Alan İzolasyonu)"
type: "architecture-decision"
category: "database"
date: "2026-10-07"
updated: "2026-10-07"
version: "1.0.0"
status: "rejected"
authority: "SSOT — R-009 red kararı: CoreMusic'in tüm verisini TEK büyük bir veritabanında (single database) toplayan mimari KABUL EDİLMEZ; 19 ayrı şema (18 domain `coremusic_*` + `media_catalog`) korunur. Gerekçe: karar dizini index.md:143 'Single DB | Güvenlik/performans' + disk kanıtı (.ai/sources/.sql/mysql/ 19 .sql dosyasında 19 `CREATE DATABASE IF NOT EXISTS`) + kod yüzeyi (PHP `DatabaseRegistry::registerMySql(key, ...)` çoklu-DB kayıt mimarisi; canlı DSN sabitleri DB_NAME/DB_AUTH_NAME/DB_MEDIA_NAME = 2-3 bağlantı) + yerini alan ADR-003 (18 domain DB + BCNF + DB-içi FK), ADR-040 (18 DB sahiplik matrisi + tek yazar), ADR-041 (kalan DB kuralları). Kapsam: red yalnız 'tek şemaya tüm veriyi toplama' mimarisini reddeder; MySQL instance'ının tek sunucuda olması, MySQL'de DB=schema olması ve cross-DB JOIN'in MySQL'de teknik olarak yasak olmaması bu redin KAPSAMI DIŞINDADR (§1.3 dürüst sınırlandırması — performans gerekçesi tek başına iddia edilmez). Web araştırması (§1.3, 5 sorgu / ~30 kaynak) sonucu: red bugün hâlâ doğru — ama dayanağı 'güvenlik + sahiplik + hata yarıçapı'dır, 'performans' ayağı ölçülmemiştir (UNKNOWN). Debate ✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 RED DOĞRULANDI — §7; 3 şart §5.3). Bu dosya salt-okunur seridir (rejected/) — değiştirilmez, yalnız referanslanır."
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 RED DOĞRULANDI)"
---

# CoreMusic — R-009: Tek (Single) Veritabanı Mimarisi (Rejected)

> **Durum:** rejected (**debate ✅ TAMAMLANDI — 19/1/0 RED DOĞRULANDI**) — **Tarih:** 2026-10-07 — **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona)** — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Seri:** `.ai/.decisions/rejected/` (salt-okunur) — **Dosya:** `R-009-single-database-architecture.md` (**Tur 1 şartı ile yeniden adlandırıldı — §7.1/1**) — **Dizin slug'ı:** `R-009-single-database-architecture` (**index.md:143** — dosya adı ile slug artık **eşleşiyor** ✅)
> **Dizin satırı:** `| R-009-single-database-architecture | Single DB | Güvenlik/performans <!-- NO FILE on disk 2026-10-06 --> |` — `<!-- NO FILE on disk ... -->` bayrağı **bu işlemde DOKUNULMADI** (temizlik son sıfırlamaya ertelendi → §5.1/3 + §7.1/2). Aynı satırın aynısı [[../../wiki/vault-decisions]] `:172`'de de var (ayrıca dokunulmadı → §7.1/3).
> **İlgili kararlar:** [[../accepted/ADR-003-multi-db-bcnf]] (yerini alan — 18 domain DB + BCNF + arıza izolasyonu) · [[../accepted/ADR-040-database-authority]] (yerini alan — 18 DB sahiplik matrisi + tek yazar) · [[../accepted/ADR-041-database-normalization-supplementary]] (yerini alan — kalan DB kuralları) · [[../accepted/ADR-002-pdo-mandatory-no-orm]] (PDO erişim katmanı) · [[../accepted/ADR-014-multi-db-migration-strategy]] (expand-contract) · [[../accepted/ADR-039-7-service-platform-architecture]] (11 servis) · [[../accepted/ADR-050-multi-db-sync-strategy]] · [[../accepted/ADR-081-multi-provider-data-sync]] (MySQL SSOT + Outbox) · karar dizini [[../index]] §5.
> **R-001…R-008 dersi uygulandı:** satır no ve slug'lar **tahmin edilmedi** — `index.md` grep'lemeden (gerçek satır **143**), hedefler `accepted/`/`rejected/` glob'ları ile diskten doğrulandı; diskte olmayan hedefe link **kurulmadı**.

---

## 1. Bağlam (Context)

CoreMusic'in veri yüzeyi **çoklu şema** üzerine kuruludur: `.ai/sources/.sql/mysql/` altındaki **19 `.sql` dosyasının 19'unda** da `CREATE DATABASE IF NOT EXISTS` vardır (18 domain `coremusic_*` + `media_catalog`) — yani üretim/şema katmanında **19 ayrı veritabanı (şema)** mevcuttur. Karar dizini bu tercihi tek satırla tescil etmiştir (`index.md:143` — "Single DB | Güvenlik/performans") ama **red metni hiç yazılmamıştır**: elde yalnız dizin satırı + `<!-- NO FILE on disk -->` notu + çoklu-DB ADR'lerinin (ADR-003/040/041) uzun gerekçe bağı vardır. Bu dosya, o satırın **gerekçeli red kaydıdır** — yeni bir karar değil, mevcut red'in (a) gerekçe, (b) güncel web araştırması, (c) yerini alan eşleme, (d) yeniden değerlendirme koşulu ile sıfırdan yazımıdır.

### 1.1 Mevcut Durum (disk kanıtı — dürüst etiket, 2026-10-07 taraması)

| # | İddia | Kanıt | Etiket |
|---|-------|-------|--------|
| 1 | Red kaydı var mı? | [[../index]] `:143` → `R-009-single-database-architecture` + "Single DB" + "Güvenlik/performans" + `<!-- NO FILE on disk 2026-10-06 -->` | ✅ **KAYITLI** (dizin satırı tek kanıt; red metni bu işlemde yazılıyor) |
| 2 | Bu işlem öncesi dosya var mıydı? | `.ai/.decisions/rejected/` içinde `CLAUDE.md`, `index.md`, `R-001-*`…`R-008-*` ; `R-009*` = **0 dosya** | ❌ **YOKTU** → bu işlemde yazılıyor (klasör zaten var — `rejected/` oluşturulmadı) |
| 3 | `R-009` grep isabeti (vault) | `index.md:143` (slug + gerekçe + NO FILE) · `wiki/vault-decisions.md:172` (aynı satırın aynası) · `rejected/R-008-*.md` §6'da "R-009…R-012 düz metin" · `rejected/index.md` başlık `total: 12` | ✅ **İSABET = 4 (3'ü vault metni, 1'i dosya yokluğu sayımı)** — kodda `R-009` = **0** |
| 4 | Yerini alan kararlar diskte? | `accepted/` glob: `ADR-003-multi-db-bcnf.md` · `ADR-040-database-authority.md` · `ADR-041-database-normalization-supplementary.md` — hepsi `status: accepted`; destek `ADR-002-pdo-mandatory-no-orm.md`, `ADR-014-multi-db-migration-strategy.md`, `ADR-039-7-service-platform-architecture.md`, `ADR-050-multi-db-sync-strategy.md`, `ADR-081-multi-provider-data-sync.md` | ✅ **3/3 + 5 destek diskte** (glob ile doğrulandı — R-001 dersi) |
| 5 | DB envanteri (şema sayısı) | `.ai/sources/.sql/mysql/` → **19 .sql** · `CREATE DATABASE IF NOT EXISTS` = **19** · adlar: `coremusic_ai, _albums, _api, _auth, _catalog, _cms, _download, _logs, _media, _musics, _neva, _patch, _playlist, _social, _studio, _system, _user, _wireless` (18) + `media_catalog` (1) | ✅ **19 AYRI ŞEMA / 19 dosya** — 1:1 eşleşme; görevdeki `.ai/.sql/mysql/` yolu diskte **YOK** (§7.1/5) |
| 6 | Kod yüzeyi: DSN kaç farklı? | DSN şablonu **1 fiziksel desen**: `sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', ...)` (`shared/src/Database/DatabaseManager.php:15`) · `mysql:host=` isabeti **5 dosya** (shared DatabaseManager + 3 vendor kopyası `api./auth./home.coremusic.net/vendor/.../DatabaseManager.php` + `media.coremusic.net/src/Media/CatalogWriter.php:245` kendi DSN'i) | ✅ **DSN kurucu 2 ayrı kod yolu** (shared registry + media CatalogWriter), **5 fiziksel dosya** (3'ü vendor kopyası) |
| 7 | Tek mi çoklu bağlantı? | `shared/src/Database/DatabaseRegistry.php:14` → `registerMySql(string $key, ...)` + `$this->managers[$key]` (dizi) = **çoklu-DB registry**; çalışma anı çağrıları **3 gerçek site** (`--\bin\api-key-create.php:439`, `api.coremusic.net/include/Container/ApiAuthContainer.php:49`, `auth.coremusic.net/include/Container/AuthContainer.php:40`) + media `CatalogWriter` (`DB_MEDIA_NAME`, varsayılan `media_catalog`, `:248`) | ✅ **MİMARİ ÇOKLU, YAŞAYAN DSN 2-3** — tek DSN'e sabitlenmemiş (registry tekilleştirme yok) |
| 8 | Ortam değişkenleri hangi DB? | `api.coremusic.net/config/.env:9` `DB_NAME=coremusic_auth` · `auth.coremusic.net/config/.env:9` `DB_AUTH_NAME=coremusic_auth` · `shared/config/.env.example:29` not: `home -> coremusic_user \| auth -> coremusic_auth` (credential değerleri REDACTED) | ✅ **Canlı DB adı yüzeyi = 2-3**; 19 şemanın **çoğuna doğrudan bağlantı yok** (ADR-040 `:71` "18 DB'nin 4'ü kodda adıyla geçiyor" ile uyumlu) |
| 9 | PHP'de adıyla geçen DB'ler | 17.412 PHP dosyası taraması → `coremusic_auth` **25**, `coremusic_user` **9**, `coremusic_musics` **8**, `coremusic_social` **8**, `media_catalog` **5** isabet (toplam 55) → **5 farklı DB adı** | ✅ **5 DB adı kodda** (ADR-040'ın 4'ü + `media_catalog`) |
| 10 | Çoklu-DB uzun gerekçe nerede? | [[../accepted/ADR-003-multi-db-bcnf]] `:94` (her domain tek başına bir DB), `:99` (BCNF denetlenebilirliği tek dev şemada zorlaşır), `:100` (arıza/risk izolasyonu), `:177` (ar-ıza & ölçek izolasyonu), `:186` (18 şema = 18 backup/migration/monitoring hedefi — **bedel açıkça yazılmış**) | ✅ **3 ADR'de uzun gerekçe** — red'in "performans" ayağı **yalnız ADR-003:100/177'de** dolaylı; hiçbirinde **ölçülmüş performans sayısı YOK** (§4.3/4) |
| 11 | Sahiplik/tek-yazar gerekçesi | [[../accepted/ADR-040-database-authority]] `:137` (4 alt karar: matris + tek yazar + cross-DB politikası + migration yetkisi), `:148` (18 satırlık sahiplik matrisi), `:36` (82 FK = 54 DB-içi + 28 cross-DB) | ✅ **sahiplik sınırı = red'in ikinci dayanağı** |
| 12 | `rejected/index.md` durumu? | Dosya **VAR** (v1.0.1, `total: 12`) ama § tablosu **BOŞ** (başlık satırları var, 12 red'in hiçbiri satırlanmamış) | ⚠️ **BOŞ** → bu işlemde **dokunulmadı** → §7.1/4 |
| 13 | Şablon/protokol diskte? | Görev `.ai/templates/adr/adr-template.md` der → `Test-Path` = **False**; gerçek yol `.ai/.templates/adr/adr-template.md` = **True** (v2.0.2, Guardrail #16) · `.claude/skills/prompt-maker/references/10-web-research-protocol.md` = **True** | ✅ **DÜZELTİLDİ** (§7.1/6) — §1.3 bu protokolle üretildi |

> **Ders notu (R-008'den):** bu red **hiç kodda denenmedi** — üretim kodunda tek-DB birleştirme **0** (§1.1/7-9: registry çoklu kalıyor, hiçbir yerde 19 şemayı birleştiren migration yok); şemada 19 `CREATE DATABASE` **var** → "reddedildi" = "tek DB'ye birleştirme **hiç kurulmadı** ve çoklu-ŞEMA disiplini **oturmuş** durumda". Gelecekte biri "hepsini tek DB'ye toplasak mı?" derse yanıtı bu dosya + 3 yerini alan ADR verir; "denedik mi?" sorusunun yanıtı **hayır** (§5.2).

### 1.2 Sorun Tanımı

1. **Red kararı kanıtsız duruyor.** `index.md:143` bir sonuç cümlesi ("Güvenlik/performans") ama **ne 2025-26 ekosistem kanıtı (tek DB vs çoklu DB, şema izolasyonunun güvenlik değeri, bağlantı havuzu/bakım maliyeti, backup granülaritesi) ne yerini alan eşleme ne yeniden değerlendirme koşulu** yazılı — gelecekteki biri "tek DB neden yok, bugün de mi yok, 19 şemanın yükü artarsa ne olur?" sorusuna vault'tan cevap bulamıyor.
2. **"Performans" ayağı ölçülmüş değil.** MySQL'de aynı sunucudaki farklı şemalar **aynı buffer pool'u** paylaşır ve sorgu planı açısından şema sınırı bir maliyet yaratmaz (§1.3-5) → "çoklu DB daha hızlı" iddiası **kanıtlanamaz**; red'in dayanağı bu yüzden **güvenlik + sahiplik + hata yarıçapı** olmalıdır (§2.1).
3. **Bedel tarafı yalnız tek satır.** 19 hedefe migration/backup/monitoring yükü ADR-003 `:186`'da tek satır — bu yükün **bugün ölçülmüş değeri yok** (migration süresi, yedek süresi, bağlantı sayısı = **UNKNOWN**) → yeniden değerlendirme kapısının eşikleri tanımsız.
4. **Koşul tanımsız.** "19 şema operasyonel olarak taşınamaz hâle gelirse tek DB'ye döner miyiz?" hiç belgelenmedi → tetikleyici tanımsız.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (öncelik: resmî doküman → vendor → bağımsız blog; her iddiaya kaynak). Araştırma 2026-10-07'de yapıldı — **5 sorgu / ~30 adlandırılmış kaynak**.

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "single database vs multiple databases per service architecture pros cons 2025" → (2) "database per service pattern microservices drawbacks connection pool backup restore" → (3) "schema isolation security multi-tenant database per tenant shared schema risk SQL injection cross-tenant data leak" → (4) "too many databases operational overhead backup restore point in time recovery connection pool max_connections many small databases" → (5) "MySQL cross database join same server allowed vs separate database isolation performance backup granularity" |
| Web Search **Konusu** | **(1-2)** tek DB vs çoklu DB'nin 2025-26'daki konumu (database-per-service, shared-database anti-pattern'i, ne zaman aşırı) · **(3)** şema/DB izolasyonunun **güvenlik** değeri (tenant/domain ayrımı, credential sınırı, RLS/GRANT) · **(4)** çoklu DB'nin **operasyonel bedeli** (bağlantı bütçesi, backup granülaritesi, bakım katları) · **(5)** **MySQL'e özel** gerçeği: aynı sunucuda DB=schema, cross-DB JOIN, performans farkı yok |
| Web Search **Bağlam** | CoreMusic: 19 `.sql` → 19 `CREATE DATABASE` (§1.1/5) · registry çoklu-DB ama yaşayan DSN 2-3 (§1.1/7-8) · 18 domain BCNF + DB-içi FK (ADR-003) + sahiplik matrisi (ADR-040) · hedef soru — *"tek DB red'i bugün hâlâ doğru mu; 'performans' ayağı ayakta mı; 19 şemanın bedeli ölçülmeden red savunulabilir mi?"* |
| Web Search **Kısa Açıklama** | **(1-2)** Database-per-service'in kazançları: gevşek bağlantı, hizalı hata izolasyonu, bağımsız ölçekleme; bedeli: cross-DB transaction/sorgu zorluğu, **çoklu DB bakımı** (her DB'ye ayrı yedek/izleme/yükseltme) — "küçük ekip için erken bölünme operasyonel yükü yener" (microservices.io; systemdesignschool: "5 servis = 5 DB'ye 5 kat operasyon"); orta yol **aynı instance içinde şema-ayrımlı erişim + GRANT bariyeri**. **(3)** OWASP: ayrı DB = en güçlü sınır (credential + network + backup izolasyonu) ama en pahalısı; şema ayrımı **disiplinli GRANT** ister; paylaşımda tek `WHERE` hatası tüm kiracı verisini açar. **(4)** Bağlantı bütçesi **instance/genel**dir (PostgreSQL'de `max_connections` cluster-geneli; MySQL'de `max_connections` sunucu-geneli) → "daha fazla DB = daha fazla izolasyon" **yanlıştır**; MySQL yedek granülaritesi sunucu/DB/tablo kademesinde. **(5)** MySQL'de aynı sunucuda şemalar arası JOIN **normal performansta fark yaratmaz** ("databases are essentially just catalogues"); asıl fark **FK'nın şemalar arası yazılamaması** + izin/GRANT bariyeridir. |
| Web Search **Uzun Açıklama** | **(1-2) Tek DB lehine dürüst kanıt:** paylaşılan tek DB'nin gerçek maliyetleri — şema değişikliğinde **koordinasyon** (EventSourcingDB: "şema değişikliği koordinasyon kâbusu"), **gizli coupling** (paylaşılan tablo = sözleşmesiz API), **çalışma zamanı çatışması** (uzun sorgu kilitleri, bağlantı havuzunu tüketme), **tek arıza alanı**. Ama microservices.io "Shared database" deseninin eksileri de aynı: development-time + runtime coupling. **database-per-service ne zaman gereksiz:** tek ekip/tek ürün/kısmi bağımsızlık (yaroslavkladko: "Does splitting unblock teams?"; systemdesignschool: "Avoid for small systems"; sadamkhan: "3 geliştirici varsa paylaşımlı DB yönetilebilir"). **Orta yol (CoreMusic'in yaptığı):** "bounded-context-per-schema with tightly controlled access (DB users with grants only on their schema) gets most of the isolation benefit without proliferating DB instances" (yaroslavkladko). **(3) Güvenlik:** OWASP tablosu — ayrı DB = "database and credential boundary" (en güçlü, en pahalı), şema = "namespace and database-role boundary" (GRANT disiplini şart), paylaşımlı satır = RLS/politika + test zorunlu; WorkOS üç modeli sıralar ve "strict isolation gereksiniminde paylaşımlı şema tercih edilmez" der; safeguard.sh: pool modelinde "izolasyon mimarinin özelliği değil, her sorgunun özelliği olur" + en yaygın pentest bulgusu **IDOR/eksik tenant filtresi**; redis.io: ayrı DB instance "regulated industries" standardı. **(4) Operasyonel bedel:** PostgreSQL SME Cookbook — `max_connections`, buffer, WAL **cluster-geneli bütçe**; "daha çok DB = daha çok izolasyon" **mit**; yedek: fiziksel tüm-cluster / mantıksal DB-kademesi (MySQL RefMan 8.4 §9.1: sunucu/tablo/DB granülaritesi, InnoDB'de dosya-kademesi); dbi-blog: 8.000 DB denemesinde 28GB boş yer + yedek/otovakuum derdi; wiki.postgresql.org bağlantı sayısı reçetesi: havuz **(çekirdek×2)+disk** civarı. **(5) MySQL gerçek:** stackoverflow 3224136 — "MySQL'de 'database'ler fiilen katalogdur; farklı DB'deki iki tabloyu sorgulamak aynı DB ile aynıdır (buffer tek set)"; 3259530 — "performans farkı YOK, iki sorun: (1) DB'ler arası FK kurulamaz, (2) izinleri tablo bazında ayırmak yönetim avantajı"; 19986683 — `GRANT SELECT/INSERT... ON db.*` ile **şema-bazlı izin bariyeri** mümkün; MySQL RefMan 9.7 §15.2.13.2 JOIN — tam nitelikli `db1.t` ile şemalar arası JOIN desteklenir (sunucu-arası **desteklenmez**). |
| Web Search **Paragraf Veri Uzun** | **Resmî/standart (1-4):** microservices.io "Pattern: Database per service" (özel şema/özel sunucu seçenekleri + bariyer olarak ayrı DB kullanıcıları/GRANT) · microservices.io "Pattern: Shared database" (development/runtime coupling) · AWS Prescriptive Guidance "Database-per-service pattern" (dezavantaj: çoklu DB yönetimi + cross-store transaction) · Microsoft Azure "Data Considerations for Microservices" (2025-11-21: "Aynı fiziksel instance paylaşılabilir; sorun paylaşılan şema/tabloludur") · OWASP "Multi-Tenant Application Security Cheat Sheet" (izolasyon stratejileri tablosu: Separate Databases / Separate Schemas / Row-Level) · dev.mysql.com RefMan 8.4 §9.1 "Backup and Recovery Types" · dev.mysql.com RefMan 9.7 §15.2.13.2 "JOIN Clause" · wiki.postgresql.org "Number Of Database Connections" · postgresql.codeguides.io "The PostgreSQL Cluster" (max_connections cluster-geneli; izolasyon seçenekleri tablosu) — **9**. **Karşılaştırma/blog (1-5):** docs.eventsourcingdb.io "One Database to Rule Them All" (2025-12-11) · techcommunity.microsoft.com "SaaS Databases – Single DB or DB per Client" (2022-10-24: kiracı başına ayrı DB = "better place", tek WHERE uzağındasın) · yaroslavkladko.dev "Database per Service — What It Really Costs" (orta yol: bounded-context-per-schema + GRANT) · sadamkhan.spiralsync.com "Database Per Service" (2025-06-10: erken bölünme = distributed monolith riski) · pcsalt.com "Database Per Service — Patterns for Data Isolation" (2025-07-15: paylaşımlı şemada "ad=true izolasyon değil") · systemdesignschool.io "A Database Per Microservice" (küçük sistemlerde kullanma; 5 DB = 5 kat operasyon) · thecodeforge.io "Database per Service Pattern" (2026-05-23: gerçek izolasyon = ayrı connection string + credential) · reintech.io "Deploying Microservices with AWS RDS" · workos.com "Tenant isolation in multi-tenant systems" (2025-02-27) · safeguard.sh "Multi Tenant Data Isolation" (2026-05-07) · redis.io "Data isolation in multi-tenant SaaS" (2026-02-06) · multi-tenant-saas.com "Schema-Per-Tenant Architecture" + "Preventing SQL Injection in Multi-Tenant Apps" · oryvelon.com "Tenant isolation explained" (2026-09-26) · dbi-services "8000 databases in one PostgreSQL cluster" (2021) · serverfault 263656 · postgrespro "One huge db vs many small dbs" · IBM Cloud "Point-in-time recovery" · stackoverflow 3224136 · stackoverflow 3259530 · stackoverflow 19986683 · stackoverflow 61170828 · softwareengineering.SE 379685 · devart "MySQL CROSS JOIN" (cross-server: FEDERATED/ETL) — **21** |
| Web Search **Sonucu** | **(1-2) Red destekleniyor ama gerekçe daraltılıyor:** tek DB, tek ekip/tek ürün için **pragmatiktir** — CoreMusic ise 11 servis + 18 domain'lik **sahiplik** modeli kurmuştur (ADR-039/040) → "tek DB" bu sahiplik haritasını **silme** demektir; endüstri orta yolu (şema-ayrımlı erişim + GRANT) tam olarak **bugünkü** CoreMusic modelidir → red **bugün de doğru**. **(3) Güvenlik ayağı AYAKTA:** ayrı DB/şema credential + GRANT bariyeri, hata yarıçapı ve backup granülaritesi sağlar (OWASP, WorkOS, safeguard, redis.io); paylaşımlı tek şemada tek filtre hatası tüm veriyi açar. **(4) Bedel dürüst yazıldı:** bağlantı bütçesi **sunucu-genelidir** (daha çok DB = havuz şişmez, ama **bakım katları** çoğalır: 19 hedefe yedek/migration/izleme — ADR-003:186 ile aynı şey); şema-drift + cross-DB sorgu sınırı gerçek maliyettir. **(5) PERFORMANS AYAĞI DÜŞTÜ:** MySQL'de aynı sunucuda şemalar arası sorgu **performans olarak eşdeğerdir** (buffer tek set; stackoverflow 3224136/3259530) → "çoklu DB daha hızlı" **iddia edilemez**; "tek DB daha hızlı" da **iddia edilemez** (CoreMusic'te ölçüm **UNKNOWN**). **İtiraz/karşıt bulgu (dürüst):** (i) MySQL'de DB=schema ve **cross-DB JOIN yasak değil** → red "yasağı" değil **politikayı** korur (ADR-003 DB-arası FK yasağı); (ii) küçük ekip için çoklu DB **aşırı** olabilir (sadamkhan/systemdesignschool) — CoreMusic'in ekip boyutu **UNKNOWN**, ama 19 şema zaten mevcut (maliyet değil, **tescil**); (iii) backup granülaritesi **tek DB'de de** tablo kademesinde sağlanabilir (mysqldump) → "tek DB'de yedek alınamaz" **yanlış olurdu**, yazılmadı; (iv) sayfa-içi derin tur **yapılmadı** → başlık/özet düzeyi (§5.1/5). |
| Web Search **Alınan Karar** | **R-009 RED (Tek Veritabanı Mimarisi) YÜRÜRLÜKTE KALIR — debate ⏳ PENDING.** (a) **CoreMusic'te tüm veriyi tek bir veritabanında toplayan mimari KABUL EDİLMEZ**; 19 şema (18 `coremusic_*` + `media_catalog`) korunur — bunu `index.md:143` + 19 `CREATE DATABASE` (§1.1/5) + ADR-003 (18 domain DB + BCNF + arıza izolasyonu) + ADR-040 (sahiplik matrisi + tek yazar) + ADR-041 (şema kuralları) kilitler. (b) **Red gerekçesi 'performans' değil GÜVENLİK + SAHİPLİK + HATA YARIÇAPI'dır:** (i) credential/GRANT bariyeri (OWASP ayrı-DB = en güçlü sınır; MySQL `GRANT ON db.*` şema-bazlı), (ii) sahiplik matrisinin tek şemada kaybolması (ADR-040:148), (iii) hata/ayar izolasyonu (ADR-003:100/177), (iv) backup/güncelleme granülaritesi (MySQL RefMan §9.1). (c) **Dürüst beyan:** MySQL'de aynı sunucuda şemalar arası sorgu **performans farkı ölçülmemiştir ve teorik olarak eşdeğerdir** → "çoklu DB daha hızlı" **yazılmaz**; CoreMusic performans karşılaştırması **UNKNOWN**. (d) **Kapsam:** red yalnız **tek-DB birleştirmeyi** reddeder; MySQL instance'ının fiziksel olarak tek sunucu olması, DB=schema oluşu ve sunucu-içi JOIN'in yasak olmaması **bu redin kapsamı dışındadır** (bunlar zaten mevcut durumdur); DBMS seçimi ADR-002/003'ün konusudur. (e) **Yeniden değerlendirme koşulu** (§2.3) yazılmadan bu red **otomatik olarak güncellenmez**; debate **⏳ PENDING** — sonuç bu dosyaya §5.3/§7'ye eklenecektir. *(Araştırma anı kaydı — debate §5.3/§7'de sonradan tamamlandı: 19/1/0 RED DOĞRULANDI.)* |
| Web Search **Sonuç** | **5/5 sorgu**: (1-2) **red desteklendi + orta yol tespiti** (endüstri CoreMusic'in şema-ayrımlı modelini "doğru orta yol" sayıyor; erken/tek-DB birleştirme sahiplik modelini siler); (3) **güvenlik ayağı doğrulandı** (OWASP/WorkOS/safeguard: ayrı DB/şema = credential + politika bariyeri); (4) **bedel dürüst yazıldı** (bağlantı bütçesi sunucu-genel — DB sayısı havuzu şişirmez; bakım katları çoğalır; 19 hedef yükü ölçülmüş değil **UNKNOWN**); (5) **performans ayağı düşürüldü** (MySQL'de şema sınırı sorgu performansını değiştirmez → red gerekçesinden "hız" kelimesi çıkarıldı, §2.1/3). **İki dürüst gerilim yazıldı:** (i) "Güvenlik/performans" gerekçesinin **yalnız güvenlik yarısı** kanıtlanabilir; (ii) 19 şemanın operasyonel bedeli **ölçülmedi** (ADR-003:186 tek satır) → §2.3 kapısına ölçüm şartı. **Üç açık işaretlendi:** (i) sayfa-içi derin tur yok (§5.1/5) · (ii) ekip boyutu/bakım süreleri **UNKNOWN** · (iii) debate **PENDING** → sonuç henüz yok *(→ §7'de tamamlandı: 19/1/0)*. **Kaynak sayısı: 5 sorgu; §1.3'te adı geçen benzersiz kaynak ~30.** |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-003 accepted (18 domain DB + BCNF) | `:94` "her domain kendi BCNF şemasında tek başına bir veritabanıdır", `:99` (BCNF denetlenebilirliği), `:100/:177` (arıza & ölçek izolasyonu), `:186` (18 hedefe bakım yükü = **red'in bedeli de bu satırda**) → **bu red'in birincil dayanağı** |
| ADR-040 accepted (18 DB otorite) | `:137` dört alt karar, `:148` sahiplik matrisi (18 satır), `:36` 82 FK (54 DB-içi + 28 cross-DB) — tek şemaya inilirse **matris + tek-yazar kuralı anlamsızlaşır** |
| ADR-041 accepted (kalan DB kuralları) | şema adlandırma + veri tipi + view/trigger + audit + N+1 — çoklu-ŞEMA disiplinini tamamlar; red ile **çelişmez** |
| ADR-002 accepted (PDO, ORM yasak) | `:24` "PHP 8.4, MySQL 9, 18 BCNF" — erişim PDO; registry `DatabaseRegistry` **çoklu DB**'yi taşıyor (§1.1/7) → tek-DB'ye geçiş registry sözleşmesini de değiştirir |
| ADR-039/050/081 accepted | 11 servis mimarisi · çoklu-DB senkronizasyonu · MySQL SSOT + Outbox — tek-DB bunların **girdi varsayımlarını** bozar |
| Şema dosyası gerçeği (19/19) | `.ai/sources/.sql/mysql/` → 19 dosyada **19 `CREATE DATABASE IF NOT EXISTS`** = 19 şema (§1.1/5) → red bir "yeni inşa" değil, **mevcut durumun tescili** |
| Kod yüzeyi gerçeği (5 DB adı / 2-3 canlı DSN) | registry çoklu; 19 şemanın 14-16'sına **doğrudan bağlantı yok** (ADR-040:71 ile uyumlu) → tek-DB birleştirme **kazanç getirmez**, yalnız matrisi siler |
| MySQL gerçeği (performans) | Aynı sunucuda şemalar arası sorgu **performans olarak eşdeğer** (§1.3-5) → "performans" gerekçesi bu dosyada **iddia edilmez**; red güvenlik/sahiplik üzerinedir |
| Araştırma protokolü | §1.3 `10-web-research-protocol.md` ile üretildi; ters kanıt (erken bölünme, tek DB pragmatizmi) dürüstçe yazıldı ama red'i **değiştirmedi, gerekçeyi daralttı** (§2.1/3) |

---

## 2. Karar (Decision)

**R-009 REDDEDİLMİŞTİR: CoreMusic'in tüm verisini tek bir veritabanında toplayan mimari ("single database") KABUL EDİLMEZ.** Karar `index.md:143`'te bugünden vardı; bu dosya onu gerekçelendirir: red, ADR-003'ün "her domain kendi DB'si + BCNF + arıza izolasyonu" hükmünün + ADR-040'ın "18 DB sahiplik matrisi + tek yazar" kuralının + ADR-041'in şema kurallarınının **red-kayıt ayağıdır** ve 2026-10-07 web araştırması (§1.3 — 5 sorgu, ~30 kaynak) red'in **bugün hâlâ doğru olduğunu** doğrulamıştır — "daha hızlı olduğu" için **DEĞİL** (§1.3-5: MySQL'de şema sınırı sorgu performansını değiştirmez), **alan izolasyonu + sahiplik + hata yarıçapı** olduğu için.

### 2.1 Neden Bu Seçenek?

1. **Disk kanıtı 19 şema gösteriyor (ölçüldü):** `.ai/sources/.sql/mysql/` → **19 dosya / 19 `CREATE DATABASE IF NOT EXISTS`** (18 `coremusic_*` + `media_catalog`), kodda **5 farklı DB adı** + **2-3 canlı DSN** (§1.1/5-9). Red, **hiç kurulmamış** tek-DB birleştirmeyi engeller; "19 şema vardır" iddiası dosyalardan okunur.
2. **Sahiplik haritası tek şemada silinir:** ADR-040 `:148` 18 satırlık matris + `:137` tek-yazar kuralı — tek DB'ye inince "hangi DB'yi kim yazar" sorusu **cevapsız** kalır; OWASP/WorkOS şema-bazlı GRANT'ın izolasyonu da kapanır (§1.3-3).
3. **Gerekçe 'performans' değil, güvenlik/sahiplik (§1.3 dürüst düzeltmesi):** MySQL aynı sunucuda şemalar arası sorguyu **eşdeğer** yürütür (buffer tek set) → "çoklu DB daha hızlı" **yazılmadı**; "tek DB daha hızlı" da ölçülmüş değil (**UNKNOWN**). Dizin gerekçesinin "güvenlik" yası ayakta, "performans" yası **ölçüme** bağlandı (§4.3/4).
4. **Hata yarıçapı:** tek şemada uzun sorgu/kilit/kapasite sorunu **tüm alanları** vurur (§1.3-1: runtime coupling; ADR-003:100); 19 şemada `coremusic_logs` yükü `coremusic_auth`'ı durdurmaz.
5. **Backup/güncelleme granülaritesi gerçek ama dürüst:** MySQL mantıksal yedek **sunucu/DB/tablo** kademesinde (RefMan §9.1) → çoklu DB'de **DB-kademesi** doğrudan; tek DB'de tablo listesini elle seçmek gerekir — bu bir ** kolaylık** farkıdır, "tek DB'de yedek alınamaz" **değil** (yalancı iddia yazılmadı).
6. **Bedel örtbas edilmedi:** 19 hedefe migration/yedek/izleme yükü (ADR-003:186) gerçek ve **ölçülmemiş** (UNKNOWN) → bu yüzden §2.3'e ölçüm şartı kondu; red "bedelsiz" diye savunulmaz.
7. **Kapsam dürüstçe sınırlı (R-006/R-007/R-008 dersi):** red yalnız **tek-DB birleştirmeyi** reddeder; MySQL instance'ının tek sunucu olması, DB=schema oluşu, sunucu-içi JOIN'in yasak olmaması ve DBMS seçimi **reddin konusu değildir**.

### 2.2 Teknik Detaylar

- **Reddedilen yüzey (single database):** tüm `CREATE TABLE`'ların **tek bir şemaya** taşınması (19 `CREATE DATABASE` → 1); `coremusic_<domain>` adlandırma ve şema-ayrımlı `GRANT`'ın (§1.1/5, §1.3-5) ortadan kalkması; ADR-040 sahiplik matrisinin ve "tek yazar servis" kuralının geçersizleşmesi; cross-DB FK istisna defterinin (28 constraint, ADR-040:36) tek DB'de FK'ya dönüp **sahiplik kuralını bozması**; tek şemada tablo adı uzayının/paylaşılan indeks adlarının birleşmesi (156 tablo → tek namespace); rapor/analitik sorguların doğrudan domain tablolarına JOIN edilebilir hâle gelmesi (gizli coupling).
- **İzinli yüzey (yerini alan uygulama):** (a) **19 şema** korunur (18 domain + `media_catalog`); (b) **şema-bazlı erişim**: servis başına ayrı DB kullanıcıları + yalnız kendi şemasına GRANT (§1.3-1 bariyer önerisi); (c) **tek yazar** (ADR-040), diğerleri okuma API/olay; (d) **cross-DB FK yasağı + istisna defteri** (ADR-003/040); (e) erişim **PDO + registry** (ADR-002; `DatabaseRegistry::registerMySql($key, ...)`), DSN'ler `DB_NAME`/`DB_AUTH_NAME`/`DB_MEDIA_NAME` ortam değişkenleriyle (credential REDACTED); (f) migration **expand-contract + tek orkestratör** (ADR-014), senkronizasyon **Outbox** (ADR-081).
- **Kod/şema yüzeyi ölçümü (2026-10-07):** 19/19 dosyada `CREATE DATABASE` · `mysql:host=` 5 dosya (1 desen + 1 media DSN + 3 vendor kopyası) · gerçek kayıt çağrıları 3 site + media writer · kodda DB adı **5 farklı** (55 isabet) · üretim kodunda **tek-DB birleştirme izi 0**.
- **Ölçüm boşluğu (dürüst — §4.2):** 19 hedefe migration süresi, yedek süresi, eşzamanlı bağlantı sayısı, şema-drift olay sayısı **hiçbiri ölçülmemiştir (UNKNOWN)** → §2.3/1 eşiği bugün **sağlanamaz**; red **işlevsel/sahiplik** gerekçesiyle durur.

### 2.3 Yeniden Değerlendirme Koşulu (şart satırı)

> **Bu red yalnız aşağıdaki koşullardan BİRİ yazılırsa yeniden değerlendirilir; aksi hâlde yürürlükte kalır:** (1) **19 şemanın operasyonel yükü ölçülebilir eşikleri aşarsa** — talep **Data Engineer + DevOps + Vault Steward** raporuyla şu sayıları **ister**: (a) tek migration döngüsünün **toplam süresi ve hedef başına ortalama**, (b) tam yedek + tek şema geri yükleme **süresi**, (c) eşzamanlı **bağlantı sayısı** (max_connections bütçesi içindeki payı), (d) **şema-drift/uyumsuz şema sürümü olay sayısı** — eşik önceden yazılır (ör. "migration döngüsü > X saat ya da drift olayı > Y/adet") ve **ölçüm yoksa kapı kapalı**; (2) **ADR-003/040/041 yeni ADR ile superseded edilirse** (metinler düzenlenmez — `superseded by` ile bağlanır; debate ile) → kapı yalnız **"alan izolasyonunu GRANT/RLS ile koruyabilen tek-şema"** kapsamıyla açılır; (3) **debate (✅ TAMAMLANDI — 19/1/0 RED DOĞRULANDI) sonucu red'i kuran koşulların değiştiğini** kanıtlarsa → yeni debate + yeni ADR ile yeniden açılır; (4) **güvenlik denetimi** tek-şemada izolasyonun GRANT+RLS ile **eşdeğer** ve **test edilmiş** olduğunu kanatlarsa (OWASP tablosundaki "separate schemas" koşulu: disiplinli GRANT + negatif test) → kapsam daraltılır ama red **otomatik kalkmaz**. **Bugün: 1 = SAĞLANMADI (ölçüm yok — 19 hedefe bakım süreleri UNKNOWN), 2 = SAĞLANMADI (ADR-003/040/041 diskte, yürürlükte), 3 = debate ✅ TAMAMLANDI (19/1/0 **RED DOĞRULANDI** — §7) → koşul **SAĞLANMADI**, 4 = kanıt YOK (test kaydı yok) → **red geçerli; debate bu satırı güncelledi (2026-10-07).**

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Tek dev veritabanı (19 şemanın biri)** | Tek yedek/izleme/kurulum; tek DSN ve tek credential; cross-domain JOIN doğrudan (MySQL sunucu-içi destekli); küçük ekip/tek ürün için **pragmatik** (§1.3-1) | **Sahiplik matrisi + tek-yazar kuralı silinir** (ADR-040:148); tek `WHERE`/filtre hatası tüm veriyi açar (OWASP); runtime coupling: uzun sorgu/kilit **tüm alanları** vurur; şema değişikliği çapraz koordinasyon (EventSourcingDB); credential/GRANT bariyeri **tek kullanıcıya** iner; şema-drift yerine tablo-namespace çatışması riski | Dizin `:143` gerekçesi (**Güvenlik/performans**) + ADR-003 `:94/:100/:177` (alan/arıza izolasyonu) + ADR-040 `:137/:148` (sahiplik + tek yazar) — red'in **doğrudan hedefi** |
| 2 | **19 şema (bugünkü uygulama)** | Alan izolasyonu (şema + GRANT bariyeri); hata yarıçapı dar; DB-kademesi yedek/geri yükleme; sahiplik matrisi uygulanabilir; BCNF denetimi alan bazlı | 19 hedefe migration/yedek/izleme yükü (ADR-003:186, **ölçülmemiş**); cross-DB JOIN FK'sız (uygulama/Outbox ile); bağlantı/kurulum karmaşası (registry) | **Reddedilmedi — bu, yerini alan yaklaşımdır** (19 `CREATE DATABASE` + ADR-003/040/041); bedeli §2.2'de yazılı, örtbas edilmedi |
| 3 | **Tek instance içinde şema-ayrımlı erişim (GRANT bariyeri)** | "bounded-context-per-schema + grants" = endüstri orta yolu (§1.3-1); izolasyonun çoğunu **instance maliyeti olmadan** alır | MySQL'de DB zaten schema = CoreMusic'te **aynen budur**; tek instance'ın `max_connections`/buffer/paylaşım bütçesi ortaktır (§1.3-4) | **Bu redin alternatifi DEĞİL — bu, CoreMusic'in mevcut uygulamasıdır** (19 şema = 19 schema; §1.1/5); red onu değil, **tek şemaya inmeyi** reddeder |
| 4 | **Paylaşımlı tek şema + satır-bazlı izolasyon (tenant/domain ID)** | En ucuz; tek yedek; sorgu içi filtre | "izolasyon her sorgunun özelliği olur" (§1.3-3); unutulan filtre = veri sızıntısı (IDOR); MySQL'de RLS **yoktur** (MySQL 9.x'te row-level security bulunmaz — PostgreSQL özelliği) → dayanak PostgreSQL'e bağlı **⚠️ VERIFICATION REQUIRED** (MySQL RLS için sürüm doğrulaması gerekir) | OWASP: "Shared Tables (Row-Level)" en zayıf sınır; MySQL'de zorunlu uygulama-filtresi disiplini CoreMusic'in 156 tablosunda **kanıtlanmamış** → güvenlik gerekçesi buna izin vermez |
| 5 | **Polyglot / DBMS başına ayrım (PostgreSQL, SQLite, MongoDB…)** | İşlevsel farklılık | 18 şema MySQL + PDO + MySQL 9 hedefi (ADR-002:24) ile **çakışır**; proje ölçeği | Bu red **DBMS'i reddetmez**; DBMS kararı ADR-002/003 kapsamındadır (düz metin — §2.1/7 kapsamı) |

*(İzinli uygulama alternatifi — 19 şema — §3'te "reddedilmedi" olarak ayrılmadı; o, yerini alan yaklaşımdır ve bu red'in gerekçe kaynağıdır. Satır 3 "aynı şeyin adı" olarak, satır 5 "kapsam dışı" olarak durur.)*

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Karar tek yerde toplandı:** `grep R-009` → bu dosya + 2 dizin satırı (`index.md:143`, `wiki/vault-decisions.md:172`) + `rejected/index.md` (boş tablo) → "tek DB neden yok" sorusunun yanıtı vault'ta yazılı.
- **Yanlış iddia önlendi:** "çoklu DB daha hızlıdır" **yazılmadı** — MySQL aynı sunucuda şema sınırının performansı değiştirmediğini söylüyor (§1.3-5); red gerekçesi **güvenlik + sahiplik + hata yarıçapına** sabitlendi → Zero-Hallucination korundu.
- **Çapraz kilitlendi:** ADR-003/040/041 + ADR-002 (PDO/registry) + ADR-014 (migration) + ADR-039 (11 servis) + ADR-050/081 (senkronizasyon) → tek-DB birleştirme **6+ ADR'nin** girdisini bozar; red tek başına değil, ağ olarak durur.
- **Geri dönüş temiz:** tek-DB birleştirme hiç kurulmadı → `git revert` edilecek değişiklik **0** (§1.1/5-9, §5.2).
- **Ölçüm kapısı tanımlandı:** 19 hedefin bakımı "rağbet edilirse" diye beklemiyor — §2.3/1 **dört sayısal metrikle** açılıyor → "operasyonel yük" argümanı **ölçülebilir** hâle getirildi.
- **Dizin satırı artık kaynağa sahip:** `index.md:143` "NO FILE on disk" iddiası fiilen geçersiz (bu dosya kaynaktır) → bayrak temizliği §5.1/3'te ertelendi, §7.1/2'de raporlandı.

### 4.2 Olumsuz Sonuçlar

- **"Performans" kelimesi dizinde kalıyor:** `index.md:143` gerekçesi "Güvenlik/performans" — bu dosya performans ayağını **düşürdü** (§1.3-5) → dizin satırı ile metin arasında **kalan gerilim** işaretlendi (§7.1/2); temizlik son sıfırlamada.
- **19 şemanın bedeli ölçülmüş değil:** migration/yedek/izleme süreleri **UNKNOWN** (ADR-003:186 tek satır) → bugün §2.3/1 eşiği **sağlanamaz**; red savunması **ölçüm** istiyor.
- **Ekip boyutu bilinmiyor:** literatür "küçük ekip için çoklu DB aşırı olabilir" der (§1.3-1) — CoreMusic'in ekip boyutu **UNKNOWN** → bu karşı argüman **yanıtlanmadan** duruyor (dürüst işaretlendi).
- **MySQL RLS yok (doğrulanacak):** satır-bazlı izolasyon alternatifi MySQL'de PostgreSQL kadar yok → "tek şema + RLS" kapısı MySQL'de **zayıf** (`⚠️ VERIFICATION REQUIRED` — sürüm doğrulaması §5.1/5).
- **Debate tamamlandı (§7):** 3 tur / 20 persona **çalıştırıldı** → **19/1/0 RED DOĞRULANDI** + 3 bağlayıcı şart (§5.3) → 20-persona çapraz doğrulama **var**; Tech Lead ✅ (Arch Lead ⏳).
- **Dizin/seri tutarsızlığı sürüyor:** `rejected/index.md` boş (12 red'in hiçbiri satırlanmadı) + `index.md:143` NO FILE bayrağı duruyor + **dosya adı ≠ dizin slug'ı** → §7.1/1-4.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **Tek DB'ye birleştirme baskısı** ("19 şema fazla, hepsi bir yere toplansın") | 3 (olası) | 4 (yüksek — sahiplik modeli + BCNF denetimi + GRANT bariyeri aynı anda kaybolur) | §2.1 + ADR-003/040: kırmızı çizgi = sahiplik matrisi; birleştirme talebi **yeni ADR + debate** ister (§2.3/2) |
| **"Performans" gerekçesinin çürük kanıtla savunulması** (birinin "çoklu DB hızlı diye var" demesi) | 3 (olası) | 3 (orta — yanlış gerekçe hallucination üretir) | §1.3-5 + §2.1/3: performans iddiası **her iki yönde de** yasak → ölçüm yoksa `⚠️ VERIFICATION REQUIRED`; red güvenlik/sahiplik diliyle savunulur |
| **Bağlantı havuzu şişmesi / max_connections baskısı** (19 DB'nin 2-3 canlı DSN'den fazlaya çıkması) | 2 (düşük — bugün 2-3 DSN) | 3 (orta — bağlantı hataları) | §1.1/7-8 sayımı (registry tekilleştirmez); gerekirse servis-başı tek kayıt + havuz ölçümü (§2.3/1-c); MySQL'de bütçe **sunucu-geneldir**, DB sayısı otomatik şişme **getirmez** (§1.3-4) |
| **Şema drift** (19 şemada senkron sürüm tutmama) | 3 (olası) | 3 (orta — beklenmeyen davranış) | ADR-014 tek migration orkestratörü + `coremusic_patch` schema_versions şablonu; drift olayı §2.3/1-d metriği |
| **Backup/restore karmaşası** (19 hedefe yedek; yanlış hedefe geri yükleme) | 3 (olası) | 3 (orta) | DB-kademeli yedek envanteri (RefMan §9.1 granülaritesi) + tek orkestratör; restore tatbikatı §2.3/1-b ölçümü |
| **Cross-DB JOIN / FK sınırının uygulama yükü** (28 cross-DB FK istisnası, JOIN'ler uygulamada) | 3 (olası) | 2 (düşük-orta) | ADR-040 istisna defteri + ref-check job; Outbox/olay replikasyonu (ADR-081); API composition (§1.3-1) |
| **Tek MySQL instance = tek nokta** (19 şema aynı instance'ta) | 3 (olası) | 4 (yüksek — instance arızası hepsini vurur) | Ayrı instance'a geçiş **red'i ihlal etmez** (19 şema korunarak bölünebilir — §3/3); high-availability planı **⚠️ VERIFICATION REQUIRED** (vault'ta HA kararı bulunamadı) |

---

## 5. Uygulama (Implementation)

> Bu kayıt **salt-okunur seri**dir (`rejected/`); "uygulama" = kaydın vault'a doğru yerleştirilmesi ve denetimidir — kod değişikliği **yoktur**.

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu dosyayı `.ai/.decisions/rejected/R-009-single-database-architecture.md` olarak yaz (şablon §1-§7, 7 bölüm dolu; **Tur 1 şartıyla yeniden adlandırıldı — §7.1/1**) + `log.md` append ("R-009 yazıldı (debate PENDING)") | Vault Steward | 25 dk |
| 2 | **Ölçüm adımı (§2.3/1 kanıtı):** 19 hedefe migration süresi · tam yedek + tek şema geri yükleme süresi · eşzamanlı bağlantı sayısı · şema-drift olay sayısı — sayılar gelmeden birleştirme/azaltma tartışması **açılmaz** | Data + DevOps + Performance | bir sonraki sprint |
| 3 | **`index.md:143` `<!-- NO FILE on disk ... -->` bayrağı → DOKUNULMADI** (talimat gereği son sıfırlamaya ertelendi — rapor-only) · `wiki/vault-decisions.md:172` aynı satır → **DOKUNULMADI** (rapor-only) · `rejected/index.md` § tablosu **BOŞ → DOKUNULMADI** (rapor-only) · dizin slug'ı ≠ dosya adı → **dizin değiştirilmedi** (rapor-only) | Vault Steward | son sıfırlama |
| 4 | **Debate** (3 tur / 20 persona — AGENTS.md §6.1: Expert 5 · Senior 5 · Junior 10; kanıt-öncelikli; en az 1 Expert + 1 Senior + 1 Junior zorunlu) → sonuç §5.3'e, tur kaydı §7'ye yazıldı; **Tech Lead onayı** ✅ (2026-10-07) | MO + Tech Lead | ✅ TAMAMLANDI (2026-10-07) |
| 5 | **Sayfa-içi derin doğrulama turu:** (a) MySQL 9.x'te row-level security var mı (§4.2/4 `⚠️`), (b) MySQL `max_connections`/bağlantı bütçesi resmî tanımı, (c) cross-DB FK + `GRANT ON db.*` resmî kısıtları (RefMan), (d) ADR-040'ın 28 cross-DB FK istisnasının bugünkü envanteri | Researcher + Data | üretim öncesi |

### 5.2 Geri Dönüş Planı

Bu karar **kod tarafında geri alınacak bir şey üretmedi** (tek-DB birleştirme hiç kurulmadı → `git revert` edilecek değişiklik **0**; §1.1/5-9). Geri dönüş = **yeniden değerlendirme** demektir ve yalnız §2.3 koşullarından biri yazılırsa yeni ADR ile açılır: (1) ölçüm eşikleri aşılırsa → yeni ADR (tek-DB ya da şema azaltma); (2) ADR-003/040/041 superseded edilirse → bu dosya `superseded` notuyla **bağlanır, düzenlenmez**; (3) debate sonucu değişirse → debate kaydı + yeni ADR; (4) güvenlik denetimi izolasyon eşdeğerliğini kanıtlarsa → kapsam daraltma ADR'si. Vault bozulursa standart kurtarma `git checkout` + son commit (AGENTS.md §17 #10). **Reddedilen yaklaşımın şemada/kodda izi olmadığı için kullanıcı/veri etkisi YOKTUR.**

### 5.3 Debate Şartları

**Kayıt:** ✅ **TAMAMLANDI** — debate **3 tur / 20 persona çalıştırıldı** (2026-10-07, AGENTS.md §6.1 seti — Expert 5 · Senior 5 · Junior 10, kanıt-öncelikli): sonuç **19/1/0 → RED DOĞRULANDI**. Durum `rejected` **kullanıcı onaylı red** olarak kalır; tur tablosu §7'de, §2.3/3 koşulu güncellendi. Aşağıdaki madde başlıkları **plan** metnidir; **asıl tur kayıtları + bağlayıcı 3 şart** bunların altındadır.

- **Tur 1 (20 persona — bulgu, planlanan):** `index.md:143` gerçek satır (NO FILE bayrağı **dokunulmadı** → §5.1/3) · 19 `CREATE DATABASE` / 19 şema (`.ai/.sql/mysql/` yolu **YOK** → gerçek `.ai/sources/.sql/mysql/`) · kodda 5 DB adı + 2-3 canlı DSN + registry çoklu · yerini alan **3/3 diskte** (ADR-003/040/041) + 5 destek (ADR-002/014/039/050/081) · 5 sorgu / ~30 kaynak · **performans ayağı düşürüldü** (MySQL'de şema sınırı = performans farkı yok) · `rejected/index.md` boş → dokunulmadı · dosya adı ≠ dizin slug'ı.
- **Tur 2 (itiraz → çözüm → şart, planlanan):** (i) "performans gerekçesi çürük" → gerekçe güvenlik/sahiplik/arıza → **Şart A: performans-iddia yasağı**; (ii) "19 şemanın yükü ölçülmüyor" → ölçüm kapıları → **Şart B: ölçüm eşikleri** (§2.3/1); (iii) "küçük ekip için aşırı" (§1.3-1) → ekip boyutu UNKNOWN → **Şart C: bilgi boşluğu kaydı**; (iv) "tek instance tek nokta" → 19 şema instance'tan ayrılabilir → **Şart D: HA ayrı konu** kaydı.
- **Tur 3 (uzlaşma, planlanan):** sonuç + bağlayıcı şartlar §7'ye yazılır; red **değişmez** (kullanıcı onaylı), yalnız şartlar bağlanır.

**Tur 1 kaydı (2026-10-07 — bulgular, 20/20):** `index.md:143` gerçek satır = 143 + ayna `wiki/vault-decisions.md:172` (**ikisi de dokunulmadı**); DB envanteri **19 ayrı veritabanı/şema** (`.ai/sources/.sql/mysql/` → 19 `.sql`, 19 `CREATE DATABASE` = 18 `coremusic_*` + `media_catalog`; görevdeki `.ai/.sql/mysql/` yolu **YOK**); DSN **2 kod yolu** (`shared/src/Database/DatabaseManager.php:15`, `media.coremusic.net/src/Media/CatalogWriter.php:245`), `DatabaseRegistry::registerMySql` **çoklu-DB**, canlı bağlantı **2-3** (`DB_NAME`/`DB_AUTH_NAME`/`DB_MEDIA_NAME`), PHP'de **5 farklı DB adı** (55 isabet); yerini alan **3/3 diskte** (ADR-003-multi-db-bcnf · ADR-040-database-authority · ADR-041-database-normalization-supplementary + destek ADR-002/014/039/050/081); **5 sorgu / ~30 adlandırılmış kaynak**; dürüst: **performans ayağı MySQL'de kanıtlanamaz** → gerekçe güvenlik + sahiplik + hata yarıçapına sabitlendi, ölçüm boşluğu `⚠️ VERIFICATION REQUIRED`; wiki-link **38/38 diskte** (benzersiz 25, kırık **0**); `rejected/index.md` **boş → dokunulmadı**. **Oylama:** Expert — `data-engineer` **kırmızı** (rename şart), `security-engineer`/`architect` **kabul**, `qa-engineer` **uyarı**; Senior — `performance-engineer` **kırmızı** (performans şart), `backend-architect`/`devops-engineer` **kabul**; Junior — **9 neutral** + `research-analyst` **uyarı**.

**Tur 2 kaydı (çapraz eleştiri — 4 itiraz → çözüm):** (1) dosya adı ≠ index slug → dosyayı `R-009-single-database-architecture.md` olarak yeniden adlandır → **Şart 1**; (2) performans ayağı kanıtsız → gerekçeyi güvenlik + sahiplik + hata yarıçapına sabitle, ölçüm boşluğu `⚠️ VERIFICATION REQUIRED` → **Şart 2**; (3) 19 DB operasyon yükü → yeniden değerlendirme kapısı (bağlantı havuzu / bakım eşiği aşılırsa) → **Şart 3**; (4) multi-tenant karşı-örneci → **şart değil, işaretle** (tek kiracılı proje = **kapsam dışı**).

**Tur 3 kaydı (uzlaşma — kanıt-öncelikli):** Expert `data-engineer` (rename + envanter) + Senior `performance-engineer` (V.R.) kanıtları üstün → **RED DOĞRULANDI (19/1/0)**.

**Bağlayıcı 3 şart (uzlaşma — §2.3/§4.3'e bağlı):**

1. **Şart 1 — Dosya yeniden adlandırma:** kayıt `.ai/.decisions/rejected/R-009-single-database-architecture.md` adıyla yaşar (`index.md:143` slug ile eşleşme ✅). **UYGULANDI (2026-10-07 — fs rename, bayt korunarak; eski ad diskte YOK → §7.1/1).**
2. **Şart 2 — Gerekçe sabitleme + ölçüm boşluğu:** red gerekçesi **güvenlik + sahiplik + hata yarıçapına** sabitlendi; "performans" ayağı her iki yönde de iddia edilemez → ölçüm boşluğu `⚠️ VERIFICATION REQUIRED` (**§1.3-5, §2.1/3, §4.2/2**). **UYGULANDI.**
3. **Şart 3 — 19 DB operasyon kapısı:** 19 şemanın operasyonel yükü (bağlantı havuzu / bakım eşiği) ölçülüp eşik aşılırsa red yeniden değerlendirilir → **§2.3/1 dört metrik** (migration süresi, yedek+restore süresi, eşzamanlı bağlantı, şema-drift olayı) + **§4.3 "bağlantı havuzu" risk satırı**. **UYGULANDI (kapı yazılı, eşikler bugün ölçülmüyor — UNKNOWN).**

*Ek not (Tur 2/4): multi-tenant karşı-örneği şart değildir — CoreMusic tek kiracılı bir projedir (kapsam dışı); OWASP multi-tenant tablosu yalnız karşılaştırma amacıyla §1.3-3'te referanslanır.*

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../index]] | Karar dizini — bu red'in kaydı (`:143`, slug + "Güvenlik/performans" + NO FILE bayrağı §5.1/3) |
| [[../accepted/ADR-003-multi-db-bcnf]] | **Yerini alan (birincil):** 18 domain DB + BCNF (`:94`), denetlenebilirlik (`:99`), arıza izolasyonu (`:100`, `:177`), bedel satırı (`:186`) — tek DB bu hükmü doğrudan ihlal eder |
| [[../accepted/ADR-040-database-authority]] | **Yerini alan:** 18 DB sahiplik matrisi (`:148`) + tek yazar + cross-DB politikası (`:137`) + FK envanteri (`:36`: 54 DB-içi / 28 cross-DB) — tek şemada matris anlamsızlaşır |
| [[../accepted/ADR-041-database-normalization-supplementary]] | **Yerini alan:** şema adlandırma + veri tipi + view/trigger/procedure + audit + N+1 kuralları — çoklu-ŞEMA disiplinini tamamlar |
| [[../accepted/ADR-002-pdo-mandatory-no-orm]] | Erişim katmanı PDO + "PHP 8.4, MySQL 9, 18 BCNF" (`:24`); `DatabaseRegistry` çoklu-DB kayıt mimarisi bununla hizalı (§1.1/7) |
| [[../accepted/ADR-014-multi-db-migration-strategy]] | expand-contract + online DDL — 19 hedefe migration'ın orkestrasyonu (§2.3/1-a metriği buradan) |
| [[../accepted/ADR-039-7-service-platform-architecture]] | 11 servis — şema-servis eşlemesinin yarısı (`18 DB → 11 servis`, ADR-040:97) tek DB'de kaybolur |
| [[../accepted/ADR-050-multi-db-sync-strategy]] | Çoklu-DB senkronizasyon stratejisi — tek-DB birleştirme bu kararın varsayımını bozar |
| [[../accepted/ADR-081-multi-provider-data-sync]] | MySQL SSOT + Outbox — cross-DB veri akışının güncel taşıyıcısı |
| [[../accepted/ADR-033-sql-normalization-strategy]] | Normal form / PK-FK / index kuralları — ADR-041'in birincil kaynağı |
| [[../../sources/.sql/mysql/coremusic_system.sql]] | **Disk kanıtı:** 19/19 dosyanın temsilcisi — `CREATE DATABASE IF NOT EXISTS` (19 şema sayımı §1.1/5) |
| [[../../wiki/vault-decisions]] | `:172` — `R-009-single-database-architecture` satırının aynısı (ayrıca dokunulmadı → §7.1/3) |
| [[../../raw/brain]] | Mimari karar özeti (`.ai/raw/brain.md` — Test-Path = **True**, 2026-10-07) |
| [[../../CLAUDE]] | Kural metinleri — Zero-Hallucination + onay kapıları + Guardrail #16 |
| [[../../index]] | Master katalog — ADR kayıtları |
| [[../../log]] | Append-only kayıt defteri (bu işlem 1 satır append → §7.1/7) |
| [[../../.templates/adr/adr-template]] | Guardrail #16 — bu dosyanın §1-§7 iskeleti + §1.3 9 alan kaynağı (**gerçek yol `.ai/.templates/adr/` — §7.1/6**) |
| Dizin satırı | `index.md:143` — slug otoritesi + NO FILE bayrağı (§5.1/3); **dosya adı ≠ slug** (§7.1/1) |
| Debate şartları | Bu dosya **§5.3** — debate ✅ **TAMAMLANDI** (3 tur / 20 persona, 19/1/0 RED DOĞRULANDI); **3 bağlayıcı şart §5.3** + tur kayıtları §7 |
| Debate sonucu (3 şart) | **RED DOĞRULANDI (19/1/0)** — Şart 1 dosya yeniden adlandırma ✅ · Şart 2 gerekçe sabitleme + ölçüm `⚠️ V.R.` ✅ · Şart 3 19 DB operasyon kapısı (§2.3/1) ✅ |
| [[R-001-redux-style-state-management]] | Seri kardeşi — aynı salt-okunur red kayıt formatı |
| [[R-002-mongodb-document-store]] | Seri kardeşi — kanıt tablosu/dürüst etiket deseni |
| [[R-003-jquery-ui-framework]] | Seri kardeşi — format referansı (§1.3 9 alan, §7.1 rapor deseni) |
| [[R-004-webpack-bundle-system]] | Seri kardeşi — format referansı (§7.1 rapor deseni) |
| [[R-005-rest-only-api]] | Seri kardeşi — format referansı (künye, §1.1 kanıt tablosu, §2.3 şart satırı) |
| [[R-006-laravel-eloquent-orm]] | Seri kardeşi — kapsam daraltma dersi (§2.1/7) |
| [[R-007-firebase-authentication]] | Seri kardeşi — satır-no dersi (tahmin yok, grep ile 143) |
| [[R-008-mysql-myisam-engine]] | Seri kardeşi — format referansı + ".ai/.sql/mysql/ YOK" dersi (§7.1/5) + ölçütsüz-iddia yasağı deseni |
| Düz metin | Eski seri R-010…R-012 (`rejected/index.md` tablosu boş → §7.1/4) · PostgreSQL/SQLite/MongoDB (disk'te reddeden satır yok → §3/5) · MySQL row-level security (`⚠️ VERIFICATION REQUIRED` → §5.1/5a) · MySQL instance HA kararı (vault'ta bulunamadı → `⚠️ VERIFICATION REQUIRED`, §4.3/7) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-10-07 | ✅ |
| Tech Lead | — | 2026-10-07 | ✅ |
| Arch Lead | — | — | ⏳ |

**Debate kaydı:** ✅ **TAMAMLANDI** — 3 tur / 20 persona **çalıştırıldı** (2026-10-07, AGENTS.md §6.1 seti; kanıt-öncelikli, en az 1 Expert + 1 Senior + 1 Junior): **19/1/0 → RED DOĞRULANDI**. Tur 1 bulguları + Tur 2 (4 itiraz → çözüm) + Tur 3 uzlaşması §5.3'te; **3 bağlayıcı şart** (dosya yeniden adlandırma · gerekçe sabitleme + ölçüm `⚠️ V.R.` · 19 DB operasyon kapısı) §5.3/§2.3/§4.3'e bağlıdır. Durum **rejected** (kullanıcı onaylı red) olarak kalır. **Tech Lead: ✅ (2026-10-07)** · Arch Lead: ⏳.

### 7.1 Rapor / Ertelenen İşler

1. **Dosya adı ≠ dizin slug'ı → GİDERİLDİ (Tur 1 şart 1):** `index.md:143` slug'ı `R-009-single-database-architecture`; dosya önce `R-009-single-database.md` adıyla yazılmıştı → **bu işlemde bayt korunarak yeniden adlandırıldı**: `.ai/.decisions/rejected/R-009-single-database-architecture.md`. Kaynak ad **diskte YOK** (rename tamamlandı — copy+boşaltma yapılmadı), dosyadaki self-referanslar (§künye, §5.1/1) yeni ada uyarlandı. Dizin satırı **dokunulmadı** (§7.1/2) — slug ile dosya adı artık **eşleşiyor ✅**.
2. **`index.md:143` `<!-- NO FILE on disk 2026-10-06 -->` bayrağı DOKUNULMADI** — talimat gereği son sıfırlamaya ertelendi; ayrıca satırdaki "Güvenlik/performans" gerekçesinin performans yarısı bu dosyada düşürüldü (§1.3-5) → satır metni **iki sebeple** temizliğe ertelendi.
3. **`wiki/vault-decisions.md:172` aynı satır DOKUNULMADI** (aynı bayrak + aynı gerekçe) → rapor-only.
4. **`rejected/index.md` VAR ama § tablosu BOŞ** (`total: 12`, 0 satır) → **DOKUNULMADI**; 12 red'in satırlanması ayrı işlem.
5. **Görevdeki `.ai/.sql/mysql/` yolu diskte YOK** (R-008 ile aynı ders) → gerçek kanıt yolu `.ai/sources/.sql/mysql/` (19 .sql) kullanıldı.
6. **Görevdeki şablon yolu `.ai/templates/adr/adr-template.md` diskte YOK** (`Test-Path` = False) → gerçek yol `.ai/.templates/adr/adr-template.md` (v2.0.2) kullanıldı.
7. **`log.md` append:** tek satır "R-009 yazıldı (debate PENDING)" — `vault-utf8-writer.mjs append` moduyla, bayt-seviyesi (§7/7'de doğrulanır).
8. **Ölçüm boşlukları:** 19 hedefe bakım süreleri, ekip boyutu, MySQL 9.x row-level security durumu, MySQL instance HA kararı → **UNKNOWN / `⚠️ VERIFICATION REQUIRED`** (§4.2, §4.3/7, §5.1/5).
9. **`log.md` append (debate):** tek satır "R-009 debate 3/20 kaydedildi (19/1/0 RED DOĞRULANDI) + Tech Lead ✅ + 3 şart + dosya yeniden adlandırıldı" — UTF-8 append (`.ai/scripts/vault-utf8-writer.mjs` **diskte YOK** → bayt-güvenli `fs.appendFileSync` fallback; PowerShell write cmdlet **kullanılmadı**), son satır zaten `\n` ile bitiyordu → ek bölme gerekmedi (§7/7'de doğrulanır).
