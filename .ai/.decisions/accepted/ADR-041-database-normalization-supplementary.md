---
title: "CoreMusic — ADR-041: Database Normalization Supplementary (şema adlandırma · veri tipi · view · trigger/procedure · audit tablo · N+1/erişim)"
type: "architecture-decision"
category: "database"
date: "2026-09-26"
updated: "2026-09-26"
version: "1.0.0"
status: "accepted"
authority: "SSOT — ADR-033'ün tamamlayıcısı (birincil ADR-033'tür): adlandırma standardı, veri tipi standardı, view & materialized view politikası, trigger & procedure politikası, audit/log tablo hizası (ADR-022) ve N+1/erişim desenleri (ADR-002/ADR-039)"
kaynak: "Kullanıcı onaylı 6 alt konu (a-f) + disk kanıtı taraması (18 SQL / 156 tablo / 260 PHP, 2026-09-26) + web araştırması (5 sorgu / 33 atıf / 31 benzersiz kaynak)"
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-041: Database Normalization Supplementary

> **Durum:** ✅ **ACCEPTED** (kullanıcı onaylı kapsam: a-f alt konular) · **Tarih:** 2026-09-26 · **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** · **Tech Lead:** ✅ · **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `ADR-041-database-normalization-supplementary`
> **İlgili kararlar:** [[ADR-033-sql-normalization-strategy]] (**birincil** — normal form, PK/FK, index, ENUM↔lookup, çoğaltma/audit alanları; bu ADR o 5 eksenin **dışında** kalır, onu **tamamlar**, devralmaz) · [[ADR-040-database-authority]] (18 DB sahipliği + tek yazar + migration tek kapı) · [[ADR-002-pdo-mandatory-no-orm]] (PDO + query budget/EXPLAIN — §2.2-f'nin zemini) · [[ADR-022-database-hardened-security]] (GRANT matrisi + audit interceptor — §2.2-d/e) · [[ADR-039-7-service-platform-architecture]] (servis sınırı — §2.2-f kural 4) · [[ADR-003-multi-db-bcnf]] (18 BCNF zemini) · [[ADR-014-multi-db-migration-strategy]] (view/trigger şema değişiminin tek kapısı) · [[../index.md]] (`:83` slug satırı)
> **Ad gerekçesi:** slug `ADR-041-database-normalization-supplementary` **diskteki gerçek index kaydından** alınmıştır (`[[../index.md]]:83`) — uydurulmadı.
> **Numara notu (Truth Mode):** genel kural "yeni ADR'ler 088+" der; bu dosya **kullanıcı atamasıyla** ayrılmış numaraya (041) yazıldı, çünkü `[[../index.md]]:83`, `[[../../brain.md]]:1001`, `[[../../keys.md]]:276` ve `[[../../index.md]]:665` bu numarayı **çoktan kayıtlı** tutuyor — numara boş değil, **boşluk dolduruldu** (ADR-033'ün numara gerekçesiyle aynı durum).
> **Frozen notu:** ADR-001-037 **dokunulmamıştır** (yalnız atıf). Bu dosya Active aralığındadır, frozen değildir.

---

## §1 Bağlam (Context)

### §1.1 Mevcut Durum (disk + kod kanıtı — dürüst etiket)

Tüm sayımlar **2026-09-26** taramasıdır: `.ai/.sql/mysql/*.sql` (18 dosya) ve repo geneli PHP (arşiv/vendor hariç 260 dosya). Etiketler: **IMPLEMENTED** = diskte/kodda kanıtlanan · **PLANNED** = kural olarak kararlaştırılan, karşılığı henüz yok.

#### A) Şema adlandırma — IMPLEMENTED (tarama)

| Kanıt | Değer | Etiket |
|---|---|---|
| SQL dosyası / `CREATE TABLE` | **18 dosya · 156 tablo · 155 benzersiz ad** | IMPLEMENTED |
| **Aynı ad, iki DB** | `api_keys` → `[[../../.sql/mysql/coremusic_api.sql]]:31` **ve** `[[../../.sql/mysql/coremusic_auth.sql]]:288` (tek tekrar) | IMPLEMENTED (çakışma) |
| Tablo adı deseni | Tablolarda `coremusic_` öneki **YOK** (o, DB/şema adıdır); **alan önekli**: `user`=14, `system`=13, `music`=9, `analytics`=8, `catalog`=8, `log`=5, `studio`=5, `playlist`=4, `cms`=4, `api`=4, `download`=4, `i18n`=4 … | IMPLEMENTED |
| Kolon adı | camelCase tanımlayıcı **0**; tamamı `snake_case` | IMPLEMENTED |
| Kimlik kolonları | `id BINARY(16)` **96** · `user_id BINARY(16)` **44** · tüm `BINARY(16)` = **252** (128-bit anahtar) | IMPLEMENTED |
| UUIDv7 üretimi kodda | `shared/src/Security/UuidV7.php` + kullanım `auth.coremusic.net/include/Repository/UserRepository.php:7,42,95` (şemada `uuid` metni **0** → biçim kodda, kolon `BINARY(16)`) | IMPLEMENTED (kod) |
| İndeks önekleri | `idx_` **485** · `uk_` **32** (inline `UNIQUE KEY`) · `fk_` **82** | IMPLEMENTED |
| **İki rakip unique-index deseni** | inline `UNIQUE KEY uk_*` (32) **karşılık** `CREATE UNIQUE INDEX idx_*_unique` (**28**: örnek `[[../../.sql/mysql/coremusic_user.sql]]:133,155,219`, `[[../../.sql/mysql/coremusic_albums.sql]]:72,100,122`) | IMPLEMENTED (tutarsızlık) |
| Diğer | `CREATE INDEX` (unique hariç) **88** — hepsi `idx_` | IMPLEMENTED |

#### B) Veri tipleri — IMPLEMENTED (tarama)

| Kanıt | Değer | Etiket |
|---|---|---|
| Sayısal tipler | `INT` **292** · `TINYINT` **197** · `BIGINT` **42** · `INT UNSIGNED` **166** · `BIGINT UNSIGNED` **19** | IMPLEMENTED |
| `VARCHAR` | **360 kullanım · 20 farklı uzunluk**: 2, 5, 7, 8, 10, 17, 20, 30, 45, 50, 64, 100, 200, 255, 500, 512, 1000, 1024, 2000, 2048 (CHAR ailesi ek olarak 12, 16, 36 → 23) | IMPLEMENTED |
| `DECIMAL` | **54 kullanım · 12 farklı (p,s)**: (3,2) (4,3) (5,2) (5,4) (6,2) (6,3) (8,2) (10,2) (12,2) (12,3) (12,4) (20,6) | IMPLEMENTED |
| Zaman tipleri | `TIMESTAMP` **257** · `DATETIME` **102** (toplam, tüm kolonlar) | IMPLEMENTED |
| **`created_at` tutarsızlığı** | `created_at` geçen satırlarda tip dağılımı → **DATETIME baskın 7 dosya**: `ai` 5/0 · `api` 3/0 · `cms` 7/0 · `download` 2/0 · `neva` 3/0 · `patch` 1/0 · `studio` 4/0 · **TIMESTAMP baskın 7 dosya**: `albums` 0/5 · `catalog` 0/8 · `media` 0/8 · `playlist` 0/5 · `social` 0/9 · `user` 0/7 · `wireless` 0/5 · **KARIŞIK 4 dosya**: `auth` 1/11 · `logs` 1/18 · `musics` 7/12 · `system` 4/13 (DT/TS) | IMPLEMENTED (tutarsızlık) |
| ENUM / JSON | `ENUM` **96** · `JSON` **69** → **kapsam dışı** (ADR-033: ENUM↔lookup + 1FN JSON bulgusu) | atıf → ADR-033 |

#### C) View / trigger / procedure — IMPLEMENTED **negatif kanıt**

| Tarama (`.ai/.sql/**`) | Sonuç | Etiket |
|---|---|---|
| `CREATE VIEW` | **0** | IMPLEMENTED (yok) |
| `CREATE TRIGGER` / `TRIGGER` | **0** | IMPLEMENTED (yok) |
| `CREATE PROCEDURE` / `CREATE FUNCTION` | **0** | IMPLEMENTED (yok) |
| `MATERIALIZED` | **0** (yalnız ADR-033 §1.3 araştırma metninde geçer) | IMPLEMENTED (yok) |

**Doküman-seviyesi örnekler (şema değil, metin):**

| Kaynak | İçerik | Durum |
|---|---|---|
| `[[../../architecture/k5-veri-yonetimi/mysql-18-database.md]]:252,273` | `CREATE VIEW vw_popular_tracks` · `CREATE VIEW vw_user_profile_summary` | **PLANNED (doküman)** — şemada karşılığı 0 |
| `[[../../architecture/k5-veri-yonetimi/migration-strategy.md]]:306-307` | trigger ile senkronizasyon önerisi | **SUPERSEDED** (ADR-014 ile) |
| `.claude/skills/database-normalize-maker/*` | view/trigger snippet'leri (şablon) | şablon — şema değil |
| `[[ADR-022-database-hardened-security]]:152,154` | GRANT matrisi: `app` = DML + "gerekirse `INDEX, CREATE VIEW, SHOW VIEW`"; **`TRIGGER` yalnız `backup` hesabında** (dump için); `migration` hesabında `TRIGGER` açık değil | IMPLEMENTED (politika) → trigger açmak GRANT revizyonu ister |

#### D) Audit / log tabloları — IMPLEMENTED (şema) · PLANNED (yazıcı)

| Kanıt | Değer | Etiket |
|---|---|---|
| Adında `log`/`audit` geçen tablo | **16**: `coremusic_logs.sql` **10** (`audit_logs`:23, `user_activity_logs`:49, `search_logs`:72, `error_logs`:96, `rate_limit_logs`:127, `log_events`:454, `log_security`:487, `log_performance`:538, `log_system`:581, `log_activity`:633) · `coremusic_auth.sql` **3** (`credential_audit`:249, `permission_audit`:352, `admin_activity_log`:413) · `coremusic_media.sql` **1** (`media_audit`:204) · `coremusic_patch.sql` **1** (`migration_log`:35) · `coremusic_system.sql` **1** (`system_migration_log`:325) | IMPLEMENTED |
| Audit-sınıfı (ADR-033'ün "6 audit tablosu" dediği küme) | `audit_logs`, `credential_audit`, `permission_audit`, `media_audit`, `rate_limit_logs`, `log_security` — sayı ADR-033 ile **aynı** | IMPLEMENTED |
| PHP'de audit/log `INSERT` | **0** (260 PHP dosyası: `audit_logs`, `credential_audit`, `INSERT INTO ...log` deseni **0 eşleşme**) | **PLANNED** — şema hazır, yazıcı yok |
| Denetim yazı yönü | ADR-022 §1.3-5 sonucu: genel sorgu log'u üretimde pahalı → **uygulama interceptor'ı** (kim, sorgu, parametre, ne zaman) | PLANNED |

#### E) N+1 / erişim desenleri — IMPLEMENTED (negatif kanıt) · PLANNED (kural)

| Tarama (260 PHP, arşiv/vendor hariç) | Sonuç | Etiket |
|---|---|---|
| Döngü (`foreach`/`while`) + 15 satır içinde `prepare(` | **0 aday** | IMPLEMENTED (ihlal yok) |
| `SELECT *` | **0** | IMPLEMENTED (ihlal yok) |
| `prepare(` kullanımı | **3 dosya / 9 çağrı**: `shared/src/Database/DatabaseManager.php:32,40` · `shared/src/OAuth/OAuthManager.php:119,164,179,196,216,233` · `.claude/skills/ui-code-generator/templates/form-handler.php:53` | IMPLEMENTED |
| Kural kaynakları (vault) | `[[ADR-002-pdo-mandatory-no-orm]]` §5.1 #6 (**query budget + `EXPLAIN`**, N+1 riski) · `[[../../.agents/data-engineer.md]]:381` ("N+1 sorgu → istenmez → batch/JOIN") · `[[../../.templates/adr/adr-database-template.md]]` Q2/Q6 + JOIN checklist satırı ("döngü içi sorgu → YOK → repository'de toplu sorgu") | IMPLEMENTED (kural) — **müfettiş yok** |

#### F) Çelişki defteri (Truth Mode — hiçbiri yumuşatılmadı)

| # | Bulgu | Etiket |
|---|---|---|
| **C1** | İki rakip unique-index deseni (`uk_*` inline 32 ↔ `idx_*_unique` 28) — hangisi standart? bu ADR §2.2-a'da cevaplanır | IMPLEMENTED (tutarsızlık) → karar (a) |
| **C2** | `created_at` tipi 3 guruba bölünmüş (7 DATETIME / 7 TIMESTAMP / 4 karışık) — 18 dosyanın 11'inde tutarsız | IMPLEMENTED (tutarsızlık) → karar (b) |
| **C3** | K5 dokümanı 2 view tanımlıyor, şemada 0 view | doküman-şema farkı → karar (c) |
| **C4** | `api_keys` aynı adla 2 DB'de (`coremusic_api:31` + `coremusic_auth:288`) — şemasız bağlanan istemci yanlış tabloya yazabilir | IMPLEMENTED (çakışma) → karar (a) notu |
| **C5** | ADR-033 "550 index" raporlar; bu tarama `idx_` 485 + `uk_` 32 + `CREATE INDEX` 88 + `CREATE UNIQUE INDEX` 28 verir → **sayım deseni farkı** (ADR-040 §1.1-A aynı ⚠️'yi taşır) | ⚠️ **VERIFICATION REQUIRED** (hangi desen bağlayıcı) |
| **C6** | Audit tabloları var, PHP yazıcı **0** → denetim kaydı üretilmiyor | PLANNED boşluk → karar (e) |

### §1.2 Sorun Tanımı (Problem)

1. **Adlandırma standardı yazılı değil:** 156 tablo/485 indeks var ama "yeni tablo nasıl adlandırılır, unique indeks hangi önekle yazılır" hiçbir dosyada tek satırla yok → C1/C4 gibi tutarsızlıklar sessizce çoğalır.
2. **Veri tipi seçimi gerekçesiz:** 20 farklı VARCHAR uzunlığı, 12 farklı DECIMAL biçimi, TIMESTAMP/DATETIME yarı-yarıya → aynı alan farklı şemalarda farklı tipte olabilir; 2038 üst sınırı hiç yazılmamış.
3. **View politikası tanımsız:** şemada 0 view, dokümanda 2 → biri view yazsa kimse "bu gerekçeli mi" diyemez; MySQL 8'de `MATERIALIZED VIEW` sözdiziminin **olmadığı** da yazılmamış.
4. **Trigger/procedure politikası tanımsız:** 0 kullanımda yasak mı serbest mi belirsiz → biri trigger eklerse (i) ADR-002'nin "mantık uygulamada" ilkesi, (ii) ADR-022 GRANT matrisi (TRIGGER yok) ihlal edilir, kimse durdurmaz.
5. **Audit tabloları yarım:** 16 tablo hazır, yazan kod 0 → "audit var" izlenimi var, kayıt üretilmiyor (C6).
6. **N+1 kuralı müfettişsiz:** kural 3 yerde yazılı (ADR-002, data-engineer, database-template) ama bugün 0 ihlal olması şans — denetim deseni/CI kapısı yok, ilk döngü-içi sorgu cezasız girer.

### §1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: `[[../../../.claude/skills/prompt-maker/references/10-web-research-protocol.md]]` (her ana iddia ≥2 çapraz kaynak; kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`).
> **Araç notu:** bu oturumda **5 `websearch` sorgusu** çalıştırıldı; sorgu alanları **konu bazlı** verilmiştir (birebir metin dökümü saklanmadı). Kaynaklar alan adı düzeyinde listelenmiştir.

| Alan | Değer |
|------|-------|
| Web Search **Query** | `1)` database table naming conventions best practices 2025 (snake_case, prefix, index naming) · `2)` SQL data types best practices — INT width, VARCHAR length, TIMESTAMP vs DATETIME UTC, DECIMAL precision · `3)` database views vs materialized views — MySQL use cases / MySQL 8 support · `4)` stored procedure trigger anti-patterns — business logic in database 2025 · `5)` N+1 query problem — batch loading, JOIN, eager loading solution |
| Web Search **Konusu** | Adlandırma standardı · veri tipi seçimi (sayısal genişlik, metin uzunluğu, zaman/tip dilimi, ondalık kesinlik) · view & materyalize view · trigger/procedure politikası · N+1 → batch/J_JOIN erişim desenleri |
| Web Search **Bağlam** | ADR-041 kapsamı: ADR-033'ün 5 ekseni (NF, PK/FK, index, ENUM↔lookup, çoğaltma) **dışında** kalan 6 alt konu (a-f); her karar, 18 SQL + 260 PHP disk kanıtıyla birlikte gerekçelendirilir |
| Web Search **Kısa Açıklama** | **(1)** Adlandırma = `snake_case` + alan öneki + **tekil, öngörülebilir indeks önekleri** (`idx_`/`uk_`/`fk_`); iki rakip desen bulgu olarak işaretlenir. **(2)** Veri tipi "varsayılan büyük" ile değil **alan bilgisiyle** seçilir: int genişlik ihtiyaca göre, VARCHAR uzunluğu bilinçli (255'e atmak kötü pratik), zaman için TIMESTAMP (UTC, otomatik, ama **2038 üst sınırı**) ile DATETIME (kapsam geniş, dilim bağımlılığı uygulamada) ayrımı, DECIMAL'de kesinlik+sürüş iş kuralından gelir. **(3)** View hafif ve faydalıdır ama **MySQL 8'de `MATERIALIZED VIEW` sözdizimi yoktur** → materyalizeleştirme ayrı tablo + yenileme işi demektir. **(4)** İş mantığı veritabanında (trigger/stored proc) test, sürümleme, dağıtım ve portreliği zorlaştırır — varsayılan anti-pattern, istisna gerekçeli. **(5)** N+1 → **JOIN** (1 sorgu) veya **IN ile batch** (2 sorgu, N'den bağımsız); döngüde sorgu yasak, sayaçlar aggregate ile. |
| Web Search **Uzun Açıklama** | **(1)** Bytebase, Devart, dbdesigner, explainanalyze, pipe0, Cygnus Dynamics ve Agentic Developer Cookbook aynı çerçeveyi veriyor: tutarlı `snake_case`, alan/tablo öneki, önek sözlüğü (`idx_`/`uk_`/`fk_`), "düşmanca/kısaltmalı" isimden kaçınma — tutarsızlık kod incelemesinde ve otomatik üretimde (agent/şablon) ilk arıza noktası. **(2)** sqlcheat, Redgate, Databricks, Oracle dokümanları ve techearl: tipleri "büyük tut ki sığsın" diye değil, **erişim kalıbı + iş kuralı** ile seçin; `VARCHAR(255)` nötrü sadece belirsizliğin yansımasıdır; TIMESTAMP 1970–**2038** aralığı + oturum dilimi dönüşümü taşır (UTC audit kolonlarında kullanışlı), DATETIME ise 1000–9999 + dilim dönüşümsüz — ikisinin karışması en pahalı teknik borç türüdür; DECIMAL'de ölçek (sürüş) para biriminin alt biriminden gelir, örn. kripto için daha derin ölçek gerekir. **(3)** dev.mysql.com CREATE VIEW referansı + HeatWave dokümanı view'ın okuma kısayolu olduğunu; mako.ai, sqlism, GeeksForgeeks ve Coding Dude materyalize view'ın **sorgu sonucunu fiziksel tabloya kopyalayıp yenileme gerektirdiğini**, MySQL'de bunun ayrı tablo + job ile taklit edildiğini anlatır (staleness + yenileme maliyeti). **(4)** Hinshelwood (2025), Microsoft "busy database" rehberi, CloudRPS, Aman Singh, Pouya Miri, Leapcell ve explainanalyze: stored proc/trigger iş mantığı gizler, uygulama sürümünden bağımsız versiyonlanır, test edilmesi pahalıdır, gözlem/şema sürümlemeyle çakışır; DB'de kalmalı olanlar yalnızca bütünlük/veri koruma tetikleyicileridir ve bunlar da gerekçeli istisnadır. **(5)** Leapcell, ShiftAsia, caduh, Ebean, PingCAP, DoHost, C# Corner ve doogal: N+1 = 1 ana sorgu + N alt sorgu; iki kanıtlanmış çözüm — eager JOIN (çok-çokta satır patlaması riski) ve `WHERE id IN (...)` batch (tam 2 sorgu, bellek dostu); sayaçlar için aggregate; sorgu sayacı/CI bütçesi ile regresyon yakalanır. |
| Web Search **Paragraf Veri Uzun** | Beş sorgu tek hükmü veriyor: **standardı olmayan şema, standart yazılmadan büyüyemez** — ad, tip ve yapı (view/trigger) kararları önceden yazılmalı, sonra denetlenmeli. CoreMusic bugün tam bu noktada: 156 tablo/485 indeks var, ama `uk_` mü `idx_*_unique` mü, `created_at` TIMESTAMP mi DATETIME mı, view/trigger serbest mi cevapsız (C1-C4). Literatür ayrıca iki şeyde uyarıcı: (i) zaman tipi karışımı sonradan "ufak temizlik" değildir — her dönüşüm migration + test ister (ADR-014 tek kapı disipliniyle uyumlu); (ii) N+1 denetimi kural yazmakla bitmez, **ölçümle** (query budget, sorgu sayacı) olur — kural zaten ADR-002 §5.1 #6'da yazılı, bu ADR onu erişim deseni standardına bağlar. Trigger/procedure için ise hem "anti-pattern" hem "istisna mümkün" sesi var → bu ADR **varsayılanı yasak, istisnayı yazılı yol** olarak ikisini birden alır. |
| Web Search **Sonucu** | **33 atıf / 31 benzersiz kaynak** (5 sorgu; `explainanalyze.io` ve `leapcell.io` ikişer sorguda geçiyor — aşağıda ★ ile işaretli). Çapraz doğrulama ≥2 bağımsız kaynak: adlandırma (7), veri tipi (5), view/MV (6), trigger/proc (7), N+1 (8). Dış kaynakla doğrulanamayan tek konu = CoreMusic'in 18 şema/156 tablo/260 PHP envanteri → iç kanıt (disk taraması) ile sabitlendi. |
| Web Search **Alınan Karar** | **(a) adlandırma standardı** — `snake_case` + alan öneki korunur, `idx_/uk_/fk_` tekilleştirilir (unique → `uk_`), `api_keys` gibi çakışmalar şema-isimli erişimle bağlanır · **(b) veri tipi standardı** — int genişlik sözlüğü, VARCHAR uzunluğu gerekçeli, **zaman = DATETIME (UTC)** (257 TIMESTAMP grandfathered), para = `DECIMAL(12,2)` · **(c) view politikası** — varsayılan yok, gerekçeli istisna `vw_` önekiyle şemaya ADR-014'ten girer; **MV = yeni ADR** (MySQL 8'de sözdizimi yok) · **(d) trigger/procedure** — **varsayılan YASAK**, istisna 4 kapılı yazılı yol · **(e) audit** — 16 tablo envanteri + append-only + yazıcı = ADR-022 interceptor (PLANNED) · **(f) N+1** — döngüde sorgu/`SELECT *` yasak, batch/JOIN + query budget (ADR-002) + servis sınırı (ADR-039) |
| Web Search **Sonuç** | Dış kaynaklar **altı kararın tamamını destekliyor**; iki gerilim noktası tartışmaya açık bırakıldı: (1) TIMESTAMP çoğunluğu (257'ye karşı 102) ile 2038/TZ gerekçesi arasındaki gerilim → §7.1 debate maddesi; (2) view istisna kriterinin sıkılığı (literatür view'ı hem över hem "gizli sorgu" diye uyarır) → §2.2-c kriterleri debate'ye bağlı |

**Kaynak listesi (33 atıf / 31 benzersiz):**

1) Bytebase — database naming conventions · 2) pipe0 — SQL naming conventions · 3) Devart — SQL naming conventions · 4) dbdesigner.net — naming conventions · 5) ★explainanalyze.io — naming convention · 6) Cygnus Dynamics — naming conventions · 7) Agentic Developer Cookbook — schema naming · 8) sqlcheat.com — SQL data types · 9) Redgate — SQL data types · 10) Databricks — data types · 11) docs.oracle.com — TIMESTAMP/VARCHAR2 datatype docs · 12) techearl.com — DECIMAL precision/scale · 13) dev.mysql.com — CREATE VIEW reference · 14) dev.mysql.com — HeatWave view docs · 15) mako.ai — views vs materialized views · 16) sqlism.com — MySQL view · 17) GeeksForgeeks — view vs materialized view · 18) Coding Dude — materialized views · 19) Hinshelwood (blog, 2025) — stored procedure anti-patterns · 20) learn.microsoft.com — busy database (logic placement) · 21) ★explainanalyze.io — triggers/procedures · 22) CloudRPS — MySQL triggers · 23) Aman Singh — stored procedure anti-pattern · 24) ★Leapcell — triggers & procedures · 25) blog.pouyamiri.com — stored procedures · 26) ★Leapcell (2025-06) — N+1 dilemma · 27) ShiftAsia (2025-05) — N+1 problem · 28) caduh.com — N+1 fix · 29) Ebean docs — N+1 queries · 30) PingCAP — solve N+1 · 31) DoHost (2025-08) — avoiding N+1 · 32) C# Corner (2025-11) — refactoring N+1 · 33) doogal.dev — solving N+1.

### §1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| **Frozen 001-037 immutabel** | Yalnız atıf; metin değiştirilmez |
| **ADR-033 kapsamı tekrarlanmaz** | NF, PK/FK, index politikası, ENUM↔lookup, çoğaltma/audit alanları **o ADR'nin**; burada yalnız tamamlanan 6 konu (a-f) yazılır — çelişki çıkarsa ADR-033 birincildir |
| **Dosya adları değişmez** | In-Place Refactoring: 18 SQL dosyası ve 18 DB adı yeniden adlandırılmaz; tablo/indeks yeniden adlandırma = yeni ADR + ADR-014 migration |
| **Kod 0 → PLANNED disiplini** | View 0, trigger 0, audit yazıcı 0, N+1 denetim kapısı 0 → adımlar bu etiketle yazılır |
| **Diskte olmayan hedefe wiki-link kurulmaz** | Yalnız diskte doğrulanmış hedefler `[[...]]` olur (§6 + satır içi toplam 33 hedef: 33/33) |
| **Tek yazma kanalı** | Vault yazımı `.ai/scripts/vault-utf8-writer.mjs`; `log.md` yalnız append |
| **REDACTED** | DB kullanıcı/şifre/host/GRANT kimlik bilgisi hiçbir koşulda yazılmaz (yalnız DB **adları**) |

---

## §2 Karar (Decision)

**CoreMusic veritabanı tamamlayıcı standardı altı alt kararla sabitlenir: (a) şema adlandırma standardı — `snake_case` + alan öneki + tek indeks önek sözlüğü (`idx_`/`uk_`/`fk_`); (b) veri tipi standardı — int genişlik sözlüğü, gerekçeli VARCHAR uzunluğu, zaman = `DATETIME` (UTC), para = `DECIMAL(12,2)`; (c) view politikası — varsayılan YOK, gerekçeli istisna `vw_` ile şemaya girer, materyalize view = yeni ADR; (d) trigger/procedure politikası — varsayılan YASAK, istisna 4 kapılı yazılı yol; (e) audit/log hizası — 16 tablo envanteri, append-only, yazıcı ADR-022 interceptor'ı; (f) N+1/erişim desenleri — döngüde sorgu ve `SELECT *` yasak, batch/JOIN + query budget + servis sınırı.**

### §2.1 Neden Bu Seçenek?

1. **Kapsam boşluğu gerçek:** ADR-033 5 eksen yazdı (NF/PK-FK/index/ENUM/çoğaltma) — ad, tip, view, trigger, audit-yazıcı, N+1 kural olarak **kapsanmadı**; bugün 0 ihlal olması, kuralın var olduğu anlamına gelmez (C1-C4 sessiz çoğalabilir).
2. **Standart = denetlenebilir olan:** "İyi isim ver" denetlenemez; "`uk_` öneki, DATETIME, `vw_` ön eki" denetlenir — CI statik taraması yazılabilir (ADR-040 K1 kapısının ruhu).
3. **Literatürle örtüşüyor (§1.3):** ad/tip/ yapı kararlarının önceden yazılması (7+5+6+7 kaynak), mantığın DB'de değil uygulamada olması (7 kaynak), N+1'in batch/JOIN + ölçümle çözülmesi (8 kaynak) — üçü de çoğunluk sesi.
4. **Değişiklik maliyeti büyümeden yönetilir:** mevcut 257 TIMESTAMP, 28 `idx_*_unique`, 12 DECIMAL biçimi **dokunulmaz (grandfathered)**; standart yalnız yeni/yazılan şemaya uygulanır → bugünden uyum maliyeti 0, ilerideki maliyet sınırlı.
5. **ADR-002/014/022/033/039/040 ile çakışmıyor:** hiçbirini devralmaz; kapıyı (014), yetkiyi (022), bütçeyi (002), birincil normalizasyonu (033), servis sınırını (039/040) **mevcut haliyle kullanır**.

### §2.2 Teknik Detaylar

#### §2.2-a Karar (a) — Şema adlandırma standardı

| Öğe | Standart (yeni/yazılan şema) | Mevcut durum | Etiket |
|---|---|---|---|
| Tablo adı | `snake_case`, **alan öneki zorunlu** (`user_`, `system_`, `music_`, `catalog_` …); önek sözlüğü ADR-003 domain listesinden türetilir | 40+ alan öneki, tutarlı (§1.1-A) | IMPLEMENTED (mevcut korunur) |
| Çoğul/tekil | Yeni tablolarda **çoğul** (`logs`, `keys`); mevcut karışıklık (`credential_audit` ↔ `audit_logs`) **dokunulmaz** | Karışık | PLANNED (yeni) |
| Kolon adı | `snake_case`; PK `id`; FK `<nesne>_id`; zaman `created_at`/`updated_at`; Boolean bayrak `_flag`/`is_` | camelCase 0 ✓ | IMPLEMENTED |
| Kimlik tipi | PK `BINARY(16)` (UUIDv7, kod `shared/src/Security/UuidV7.php`) | 96 `id` + 252 `BINARY(16)` | IMPLEMENTED |
| Non-unique indeks | **`idx_<tablo>_<kolon(ler)>`** | `idx_` 485 + `CREATE INDEX` 88 hepsi `idx_` ✓ | IMPLEMENTED |
| Unique indeks | **`uk_<tablo>_<kolon(ler)>`** — `CREATE UNIQUE INDEX idx_*_unique` deseni **yeni yazımda yasak** | 32 `uk_` ↔ 28 `idx_*_unique` (C1) | karar → yeni yazımda `uk_`; mevcut 28 **grandfathered**, geçiş PLANNED |
| FK constraint | `fk_<tablo>_<kolon>` | `fk_` 82 ✓ | IMPLEMENTED |
| **Aynı tablo adı, iki DB** | Yasak **değil** (şema-isimli referans zorunlu: `db.tablo`) — ama yeni çakışma **kaydın gerekçesiyle** eklenir; `api_keys` (C4) mevcut borç olarak defterde | 1 çakışma | IMPLEMENTED (kural yeni) |
| Yeni tablo yolu | Yalnız ADR-014 runner'ı (versioned SQL, forward-only) | runner PLANNED | PLANNED (ADR-040 §2.2-d) |

#### §2.2-b Karar (b) — Veri tipi standardı

| Sınıf | Standart | Dayanak / mevcut | Etiket |
|---|---|---|---|
| Bayrak / 0-1 | `TINYINT(1) UNSIGNED` (veya mevcut `TINYINT` deseni) | `TINYINT` 197 | IMPLEMENTED + standart |
| Pozitif sayaç / kimlik-sayaç | `INT UNSIGNED` | 166 kullanım | IMPLEMENTED + standart |
| Büyük sayaç (yüksek aralık) | `BIGINT UNSIGNED` | 19 kullanım | IMPLEMENTED + standart |
| Sayaç-dışı tam sayı | `INT` (imzalı) — aralık iş kuralıyla seçilir | 292 kullanım | IMPLEMENTED |
| Metin | `VARCHAR(n)` — **n bilinçli seçilir** (alan bilgisi); "255'e at" varsayılanı **yasak**; yeni bir uzunluk eklenebilir ama §5.1 adımı 6'ya gerekçe yazılır | 20 farklı uzunluk (§1.1-B) | karar (kural yeni) |
| **Zaman (standart)** | **`DATETIME` — UTC saklanır, uygulama saat dilimi dönüştürür**; ms gereken yerde `DATETIME(3)` | 102 DATETIME vs **257 TIMESTAMP**; `created_at` 11/18 dosyada tutarsız (C2) | **karar**; mevcut 257 TIMESTAMP **grandfathered**, geçiş = fazlı migration (§5.1 adım 7, PLANNED) |
| Zaman (gerekçe) | `TIMESTAMP` 1970–**2038-01-19** üst sınırı + oturum dilimi dönüşümü taşır (§1.3 kaynak 8-12); audit kolonunun 2038'de kilitlenmesi kabul edilemez | literatür | — |
| Ondalık / para | **`DECIMAL(12,2)`** (yeni para alanları); ölçek iş kuralından gelir (kripto benzeri derin ölçek = yeni ADR) | 12 farklı (p,s) mevcut → grandfathered | karar |
| ENUM / JSON | **kapsam dışı** → ADR-033 (ENUM↔lookup, 1FN JSON) | ENUM 96, JSON 69 | atıf |

#### §2.2-c Karar (c) — View & materialized view politikası

| Kural | Detay |
|---|---|
| **Varsayılan: view yok** | Yeni okuma yolları önce **uygulamada tek sorgu/batch** ile çözülür (§2.2-f); view, aynı join setinin ≥2 yerde tekrarlanıp bakım riski oluşturduğu **gerekçeli** durumlarda |
| View gerekçesi (kapalı liste) | (i) tekrarlayan çoklu-JOIN okuması, (ii) sahibi belli tek DB içinde (cross-DB view **YASAK** — ADR-003/040), (iii) `vw_<isim>` öneki, (iv) ADR-014 migration'ı ile şemaya yazılır, (v) `EXPLAIN` ile ölçülür (ADR-002) |
| MySQL 8 gerçeği | `MATERIALIZED VIEW` sözdizimi **yok** (§1.3 kaynak 13-18): materyalizeleştirme = ayrı fiziksel tablo + periyodik yenileme işi |
| MV isteği | = **yeni ADR** + ADR-033 denormalizasyon istisna kaydı (neden/risk/senkron/ters plan) + ADR-014 job |
| Mevcut durum | Şemada view **0**; K5 dokümanındaki 2 view (C3) **PLANNED (doküman)** — şemaya yazılmaz, yazılacaksa bu §'deki gerekçeyle |

#### §2.2-d Karar (d) — Trigger & procedure politikası

| Kural | Detay |
|---|---|
| **Varsayılan: YASAK** | 18 dosyada 0 kullanımda (**IMPLEMENTED negatif**); iş mantığı uygulamada (ADR-002: PDO + servis katmanı), DB yalnızca veri/bütünlük |
| Gerekçe | §1.3 (7 kaynak): test edilebilirlik, sürümleme, dağıtım, gözlem ve portrelik; + ADR-022 GRANT matrisi: `app` hesabında `CREATE/ALTER/DROP` yasak, **`TRIGGER` yalnız `backup` hesabında** (`:154`) → yetki düzeyinde de fiilen kapalı |
| **İstisna — 4 kapılı yazılı yol** | (1) **yeni ADR** (bu metin düzenlenmez), (2) ADR-022 GRANT matrisi revizyonu (`migration` hesabına hedef yetki), (3) sahip servis + Data Engineer review + test, (4) ADR-014 forward-only migration + geri dönüş planı |
| İstisna kapsamı sınırı | Yalnız **veri koruma / bütünlük tetikleyicisi** (ör. kritik silmede denetim satırı) kabul edilir; **iş mantığı trigger'ı reddedilir** |
| Mevcut doküman örnekleri | `migration-strategy.md:306-307` trigger-senkron önerisi **SUPERSEDED** (ADR-014); `database-normalize-maker` snippet'leri şablondur → şemaya **kopyalanamaz** |

#### §2.2-e Karar (e) — Audit / log tablo hizası (ADR-022 ile)

| Kural | Detay |
|---|---|
| Envanter | **16 tablo** (§1.1-D, dosya:satır listesi) — bu liste bu ADR'de envanter olarak tescil edilir; yenisi ancak kayıtla eklenir |
| Sınıflandırma | **audit-sınıfı 6** (`audit_logs`, `credential_audit`, `permission_audit`, `media_audit`, `rate_limit_logs`, `log_security`) — ADR-033'ün "6 audit tablosu" ile **aynı** · kalan 10 log/analitik |
| Yazım kuralı | Audit tabloları **append-only** (`UPDATE/DELETE` yasak); sahiplik ADR-040 §2.2-a ile: `coremusic_logs` = çok-yazıcı ortak alan (istisna (b)3), `credential_audit`/`permission_audit` = auth, `media_audit` = media |
| Alan hizası | Zaman/audit kolonları ADR-033 §2.4-5 + ADR-022 §b/c ile hizalı → **tekrar yok, yalnız atıf** |
| **Yazıcı (boşluk C6)** | PHP'de audit `INSERT` **0** → standart: **uygulama interceptor'ı** (kim, sorgu, parametre, ne zaman — ADR-022 §1.3-5 sonucu) ADR-005 logging akışına yazar; DB-hook'u değil (§2.2-d ile tutarlı) → **PLANNED**, §5.1 adım 10 |
| Okuma | Audit verisi rapor/analiz işinde: batch sorgu + aggregate (§2.2-f), servisler arası Gateway üzerinden (ADR-039) |

#### §2.2-f Karar (f) — N+1 / erişim desenleri (ADR-002 + ADR-039 hizası)

| # | Kural | Uygulama / kanıt |
|---|---|---|
| 1 | **Döngüde sorgu YASAK** → JOIN (tek sorgu) veya `WHERE ... IN` batch (2 sorgu) | Bugün 0 ihlal (§1.1-E) → kural + CI/review deseni (§5.1 adım 11) |
| 2 | **`SELECT *` YASAK** → açık kolon listesi | Bugün 0 ihlal ✓ (ADR-002 ruhu) |
| 3 | **Query budget + `EXPLAIN`** her liste/endpoint için | `[[ADR-002-pdo-mandatory-no-orm]]` §5.1 #6 (mevcut şart — tekrar açılmaz) |
| 4 | **Servis sınırı:** servisler arası JOIN/dorudan sorgu yasak → cross-service N+1 = Gateway batch sorgu + olay (outbox) ile çözülür | ADR-039 (servis) + ADR-040 (tek yazar) + ADR-081 (olay) |
| 5 | **Prepared statement tek yol** | 3 dosya/9 çağrı ✓ (`DatabaseManager.php:32,40`, `OAuthManager.php:119+`, `form-handler.php:53`) |
| 6 | Sayaç/süreçler **aggregate** ile (döngüde `COUNT` yasak), referans tabloları önceden yükle | §1.3 kaynak 26-33 + `[[../../.templates/adr/adr-database-template.md]]` (Q2/Q6) |
| 7 | Teşhis deseni: döngü + `prepare` şüphesi (15 satır penceresi) review checklist'ine girer | `[[../../.agents/data-engineer.md]]:381` ile aynı |

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Tek ADR'de topla (6 konuyu ADR-033'e ek yaz)** | Tek dosya, tek debate | ADR-033 **accepted** ve debate'ü kapalı; onu genişletmek onaylı karar metnini değiştirmek olur (kural: karar değişikliği = YENİ ADR); 5 eksen kapsamı 11 eksene şişer | Şablon kuralı 10 (ADR düzenlemesi yasak, yenisi yazılır) + ADR-033 §2 kendi kapsamını "5 eksen" diye sabitlemiş → bu ADR tamamlayıcı olarak **ayrı** yazılır |
| 2 | **Trigger/stored procedure ile iş mantığını DB'ye taşı** (tetikleyici tabanlı senkron/audit) | Tek sorguda yan etki, uygulama kodu azalır | Test/sürümleme/dağıtım pahalı, gizli yan etki, GRANT'ta yetki yok (ADR-022 `app` yasaklı), 0 kod kanıtı | §1.3 (7 kaynak) anti-pattern diyor; ADR-002 "mantık uygulamada" ile çelişir; istisna yolu §2.2-d'de açık bırakıldı |
| 3 | **View-first okuma katmanı** (her rapor view'ı) | SQL sadeleşir, tekrar azalır | View'lar `EXPLAIN`/n+1 kaynağını gizleyebilir; bugün 0 view; cross-DB view MySQL'de yok (18 DB ayrımı); MV yok → staleness işi ek iş | §1.3 (kaynak 13-18) view'ı "hafif istisna" diyor; kural: varsayılan yok, gerekçeli istisna (§2.2-c) — tamamen yasak da literatüre aykırı |
| 4 | **Mevcut çoğunluğa uy: standart = `TIMESTAMP` (257)** | 11 dosya zaten uyumlu, migration 0 | 2038 üst sınırı + oturum dilimi bağımlılığı; audit kolonlarının 2038'de kilitlenme riski; yeni standart = eski hatanın tekrarı | §1.3 kaynak 8-12; DATETIME (UTC) seçildi, mevcut 257 **grandfathered** (bugün 0 maliyet) → bu gerilim §7.1 debate maddesi |
| 5 | **Tüm şemayı tek seferde standarda çek** (156 tablo yeniden adlandır + 257 TIMESTAMP dönüşümü + `idx_*_unique` → `uk_` big-bang) | Kural anında %100 tutarlı | 156 tablo adı değişimi = In-Place Refactoring yasası + wiki/DB kırılması; 28 `idx_*_unique` yeniden adlandırması live DDL; hata yüzeyi dev | ADR-014/040 expand-contract + forward-only ruhu tek aşamalı yıkımı reddeder → fazlı, yeni/yazılan şemaya uygulanan standart (§2.1-4) |

---

## §4 Sonuçlar (Consequences)

### §4.1 Olumlu Sonuçlar

- **6 boşluk kapanır:** ad, tip, view, trigger, audit-yazıcı, N+1 — hepsi tek satırlık kurala değil, **denetlenebilir önek/tipe** indirgenir (`uk_`, `DATETIME`, `vw_`, yasak listesi).
- **Değişim maliyeti 0:** standart yeni/yazılan şemaya uygulanır; mevcut 156 tablo/257 TIMESTAMP/28 `idx_*_unique` grandfathered → big-bang yok.
- **Şeffaf borç:** C1-C6 çelişki defteri + `api_keys` çakışması gizlenmez, kayıtlıdır.
- **Kapılar çoğalmaz:** trigger istisnası 4 kapılı (ADR → GRANT → review → migration), view istisnası 5 kriterli → istisna istisna çoğaltamaz.
- **ADR-033 yükü hafifler:** "tamamlayıcı" ayrı dosyada olduğu için birincil normalizasyon karar metni şişmez; çelişki halinde ADR-033 birincil kuralı yazılıdır.

### §4.2 Olumsuz Sonuçlar

- **TIMESTAMP gerilimi:** literatür DATETIME derken mevcut 257 sütun TIMESTAMP → geçiş PLANNED kalırsa iki standart birlikte yaşar (C2 açık kalır).
- **PLANNED çok:** audit yazıcı (kod 0), N+1 CI kapısı (0), adlandırma lint'i (0), zaman tipi geçişi (0) → bu ADR **karar/iskelet** seviyesindedir.
- **Kural 7 (§2.2-f) bugün ölçülmüyor:** 0 ihlal, ölçüm yok → ilk regresyon ancak review ile yakalanır.
- **Yeniden adlandırma baskısı:** `credential_audit`↔`audit_logs` gibi tekil/çoğul karmaşası duruyor (bilinçli grandfathering) — okuyucu "neden düzeltilmedi" diye sorar → cevap §2.2-a ve §3-alt.5.
- **`status: accepted` ≠ uygulama:** lint/kapılar/interceptor çalışana kadar kurallar yalnız dokümantdır.

### §4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **R1 Zaman tipi geçişi (257 TIMESTAMP → DATETIME) canlıda veri/uygulama kırarsa** | Orta (%40-70) | Yüksek | Fazlı (§5.1 adım 7): önce tek DB → test → `ALGORITHM=INPLACE` doğrulama; ADR-014 forward-only; geçiş bitsin diye C2 kapanmaz, **faz satırı** `log.md`'ye append edilir |
| **R2 Adlandırma lint'i yanlış kırmızı verirse (grandfathered isimleri yakalarsa)** | Yüksek (>%70) | Orta | Lint deny-listesi: mevcut 28 `idx_*_unique` + tekil tablo adları **beyaz liste**; yalnız **yeni** dosya satırları kırmızı |
| **R3 Trigger istisnası suistimal edilir (iş mantığı trigger'ı diye girer)** | Orta (%40-70) | Yüksek | §2.2-d kapalı kapsam: yalnız veri koruma/bütünlük; 4 kapıdan biri eksikse RED; debate (⏳) şartı olarak bağlanabilir |
| **R4 Audit yazıcı hiç yazılmazsa (C6 açık kalır)** | Yüksek (>%70) | Orta | §5.1 adım 10 sahipli + süreli; ADR-022 §5.1 adımlarıyla birlikte izlenir; envanter (16 tablo) yine de kilitli kalır |
| **R5 N+1 kuralı müfettişsiz kalırsa** | Orta (%40-70) | Orta | ADR-002 §5.1 #6 (query budget) zaten bağlayıcı; adım 11 review checklist'i + data-engineer §381 ile hizalar |
| **R6 İki ADR çelişkisi (041 ↔ 033/040) debate'de çıkarsa** | Düşük (<%10) | Yüksek | Çelişki halinde **ADR-033 birincildir** (§1.4); çözüm = yeni ADR, bu metin düzenlenmez |

### §4.4 Fallback (geri birleşim / geri dönüş)

1. **Zaman tipi kararı debate'de düşerse** (TIMESTAMP çoğunluğu kabul edilirse): yeni ADR "revert of 041 §2.2-b" yazılır; bu metin **değiştirilmez**, `log.md` append ile yön işaretlenir.
2. **Lint kurulamazsa:** adlandırma/tip denetimi `.ai/scripts/` içinde tek okunur komut olarak çalıştırılır (vault-utf8-writer `scan` ruhu) + `log.md` elle append → kapı yoksa en azından denetim izi kalır.
3. **Trigger istisnası geri alınırsa:** trigger ADR-014 forward-only migration ile düşürülür; GRANT matrisi eski hâline döner (ADR-022 yeni satır) — borç kayıtlı kalır.
4. **Audit yazıcı gecikirse:** 16 tablo envanteri + append-only kuralı geçerlidir; yazıcı gelene kadar denetim yalnız DB genel sorgu log'undadır (ADR-022 §1.3-5 kabul edilen geçici durum).
5. **Tam geri dönüş:** bu ADR frozen değil → yeni ADR "revert of ADR-041" + `[[../index.md]]:83` satırı `—` işaretlenir (silinmez); `log.md` append-only olduğu için geri dönüş de yeni satırdır.

---

## §5 Uygulama (Implementation)

### §5.1 Adımlar

| # | Adım | Sorumlu | Süre | Durum |
|---|------|---------|------|-------|
| 1 | Kanıt taraması (18 SQL: tablo/kolon/tip/indeks/view/trigger; 260 PHP: N+1/SELECT */prepare/audit-yazıcı) | Vault Steward + Data | 2 dk | ✅ UYGULANDI (2026-09-26) |
| 2 | Bu ADR'yi şablondan üret + künye/§1-§7 dolu (Guardrail #16), §1.3'ü 5 websearch ile doldur | Vault Steward | 3 dk | ✅ UYGULANDI (2026-09-26) |
| 3 | **`[[../index.md]]:83` satırının** `[[../../brain.md]] ADR-041-...` biçiminden bu dosyaya bağlanması (`[[accepted/ADR-041-database-normalization-supplementary]]`) | Vault Steward | 1 dk | ✅ UYGULANDI (2026-09-26) |
| 4 | `.ai/log.md`'ye 1 satır append (`ADR-041 yazıldı (debate PENDING)`) | Vault Steward | 1 dk | ✅ UYGULANDI (2026-09-26) |
| 5 | **Adlandırma lint'i:** `*.sql`/migration şablonunda `uk_`/`idx_`/`fk_` öneki + `snake_case` denetimi CI job'una (ADR-040 K1 kapısının yanına); deny-liste: mevcut 28 `idx_*_unique` + tekil adlar (R2) | DevOps + Data | 2 gün | ⏳ PLANNED |
| 6 | **Tip/uzunluk registry'si:** migration-template + review checklist'inde VARCHAR uzunluğu gerekçesi ve `DECIMAL(12,2)` zorunlu alanı | Data + Backend | 1 gün | ⏳ PLANNED |
| 7 | **Zaman tipi geçiş faz'ı:** C2'nin 4 karışık + 7 DATETIME dosyası **dokunulmaz**; **257 TIMESTAMP → DATETIME** dönüşümü test DB'de doğrulanır, sonra tek tek DB (ADR-014) | Data + QA | 10 gün (fazlı) | ⏳ PLANNED (R1) |
| 8 | **View kapısı:** `vw_` öneki + 5 kriter (§2.2-c) migration review soru listesine eklenir; K5 dokümanındaki 2 view ya yazılır (kriter sağlanırsa) ya "doküman-only" notu sabitlenir (C3 kapanışı) | Data | 1 gün | ⏳ PLANNED |
| 9 | **Trigger/procedure CI gate:** `.ai/.sql/**` içinde `CREATE TRIGGER|PROCEDURE|FUNCTION` = RED (istisna: bu ADR'nin gerekçeli istisna ADR'si kayıtlıysa) | DevOps + Data | 1 gün | ⏳ PLANNED |
| 10 | **Audit interceptor (C6):** sorgu-level denetim kaydı (kim/sorgu/parametre/ne zaman) → `coremusic_logs` append; ADR-022 §5.1 adımlarıyla eşgüdümlü | Backend + Security | 3 gün | ⏳ PLANNED (ADR-022 bağları) |
| 11 | **N+1 review checklist:** döngü+sorgu / `SELECT *` / döngüde `COUNT` maddeleri review checklist'ine; query budget (ADR-002 §5.1 #6) endpoint bazında yazılır | QA + Backend | 2 gün | ⏳ PLANNED |
| 12 | **Vault sync:** `[[../../brain.md]]:1001` özet satırı, `[[../../keys.md]]:276`, `[[../../index.md]]:665` kayıtlarının bu dosyaya bağlanması (3'ü bugün düz metin/slot) | MO (vault-updater) | 30 dk | ⏳ PLANNED (post-op manuel sync) |
| 13 | **Debate:** 4 madde (§7.1) üzerinden 3 tur — sonuç `log.md` append + frontmatter `debate` güncellemesi (bu dosya Active iken sınırlı revizyon) | Vault Steward + Tech Lead | 1 gün | ✅ DEBATE TAMAMLANDI (2026-09-26, 3 tur / 20 persona, 18/2/0 KABUL) — şartlar adım 14-17 |
| 14 | **(Debate Şartı 1a)** `api_keys` iki-DB çakışmasının (C4) **tek SSOT kararı**: sahip DB + erişimin **şema-isimli (`db.tablo`)** zorunluluğu yazılır (ADR-020 API-key sahipliği + ADR-040 §2.2-d tek yazar hizası); yeni çakışma §2.2-a kuralıyla kayıt altına alınır | Data + Security | 1 gün | ⏳ PLANNED (debate şartı) |
| 15 | **(Debate Şartı 1b)** **Grandfathered kuralı maddeleştirilir:** C1 (`uk_` 32 ↔ `idx_*_unique` 28) ve C2 (`created_at` 11/18 tutarsız) için **mevcut = izinli (beyaz liste), yeni/yazılan = standart** ayrımı §2.2-a/b'de gerekçesiyle sabitlenir; lint deny-listesine (R2) bildirilir | Data + Vault Steward | 1 gün | ⏳ PLANNED (debate şartı) |
| 16 | **(Debate Şartı 2)** **View PLANNED etiketi:** K5 dokümanındaki 2 view (`:252,:273` — C3, doküman 2 / şema 0) **PLANNED (doküman)** olarak sabitlenir, şemaya yazılmaz; şemaya giriş yalnız §2.2-c 5 kriterle | Data | 1 gün | ⏳ PLANNED (debate şartı) |
| 17 | **(Debate Şartı 3)** **CI denetimi + N+1 testi:** naming/typing statik denetimi (adım 5) + N+1 testi (döngü+sorgu / `SELECT *` / döngüde `COUNT` — adım 11) CI job'una bağlanır; çıkış ölçütü: bilinçli ihlal örneği CI'da kırmızı görünür | DevOps + QA + Data | 2 gün | ⏳ PLANNED (debate şartı) |

### §5.2 Geri Dönüş Planı

1. **Karar seviyesi:** `status: accepted` ama **frozen değil** → vazgeçiş = yeni ADR (NNN+1, "revert of ADR-041") + bu dosya `superseded-by` ile bağlanır; metin **silinmez/değiştirilmez**.
2. **Kural seviyesi:** bir alt karar (ör. `DATETIME`) geri alınırsa yalnız o madde yeni ADR ile değişir; §2.2 tablolarının bu sürümü `log.md` append ile tarihlenir.
3. **Geçiş seviyesi (adım 7):** TIMESTAMP dönüşümünde sorun çıkarsa o DB'de geçiş **durdurulur** (faz saturation), kolonlar zaten `DATETIME`'a geçmişse geri dönüş = yeni migration (forward-only) + veri doğrulama raporu; veri kaybı olmaz (tip daraltması değil, genişletme).
4. **Kapı seviyesi (adım 5/9):** lint yanlış kırmızı verirse deny-liste genişletilir, kapı **kalkmaz** (`continue-on-error`); denetim izi `log.md`'de kalır.
5. **Dizin seviyesi:** `index.md:83` kaydı geri alınırsa `—` işaretlenir, silinmez.
6. **Bozulma durumunda:** `node .ai/scripts/vault-utf8-writer.mjs repair --file <dosya>` (yedek alır) → gerekirse `git checkout` (`.ai/AGENTS.md` §17 #10).

---

## §6 İlgili Dokümanlar (ilişki tablosu — tüm wiki-link hedefleri diskte doğrulandı: 33/33)

| Dosya (wiki-link) | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme — Guardrail #16 (şablon), REDACTED |
| [[../../brain.md]] | `:1001` `ADR-041 \| DB normalizasyon ek bilgi` slotu (bu dosyaya bağlanması §5.1 adım 12) |
| [[../../AGENTS.md]] | Agent registry — §5 domain boundary (`*.sql` → Data Engineer), §16 Data standardı (BCNF/no ORM/no SELECT */prepared — §2.2-f ile aynı) |
| [[../../WORKFLOW.md]] | Süreç/fazlar — uygulama adımlarının bağlandığı akış |
| [[../../index.md]] | Master katalog `:665` ADR-041 satırı |
| [[../../log.md]] | Audit trail — bu ADR'nin 1 satırlık append kaydı |
| [[../../keys.md]] | Keyword haritası `:276` ADR-041 kaydı |
| [[../index.md]] | Karar dizini `:83` slug satırı (bu dosyaya bağlanması §5.1 adım 3) |
| [[../CLAUDE.md]] | Karar dizini kuralı |
| [[../../.templates/adr/adr-template.md]] | Guardrail #16 şablonu (bu dosyanın iskeleti) |
| [[../../.templates/adr/adr-database-template.md]] | Q2 (N+1) / Q6 (batch) / JOIN checklist — §2.2-f gerekçesi |
| [[../../.agents/data-engineer.md]] | `:381` "N+1 → batch/JOIN" — §2.2-f kural 7 |
| [[../../../.claude/skills/prompt-maker/references/10-web-research-protocol.md]] | §1.3 araştırma protokolü |
| [[../../architecture/k5-veri-yonetimi/mysql-18-database.md]] | `:252,:273` view örnekleri (C3 — doküman-only) |
| [[../../architecture/k5-veri-yonetimi/migration-strategy.md]] | `:306-307` trigger önerisi — ADR-014 ile SUPERSEDED (§2.2-d) |
| [[../../architecture/k5-veri-yonetimi/README.md]] | K5 veri katmanı kataloğu |
| [[ADR-002-pdo-mandatory-no-orm]] | PDO + **query budget/EXPLAIN** (§5.1 #6 şartı) — §2.2-f zemini |
| [[ADR-003-multi-db-bcnf]] | 18 BCNF zemini + "DB arası FK yok" (view kuralının cross-DB sınırı) |
| [[ADR-014-multi-db-migration-strategy]] | View/trigger/tip geçişi şema değişikliğinin **tek kapısı** (forward-only) |
| [[ADR-022-database-hardened-security]] | **GRANT matrisi** (TRIGGER/VIEW yetkileri) + audit interceptor sonucu — §2.2-d/e |
| [[ADR-033-sql-normalization-strategy]] | **Birincil ADR** — 5 eksen (NF, PK/FK, index, ENUM, çoğaltma); bu ADR onu tamamlar, devralmaz |
| [[ADR-039-7-service-platform-architecture]] | 11 servis — §2.2-f kural 4 (servis sınırı) |
| [[ADR-040-database-authority]] | 18 DB sahipliği + tek yazar + migration tek kapı — envanter/istisna hizası |
| [[../../.sql/mysql/coremusic_logs.sql]] | Audit-sınıfı 6'dan **2'si** (`audit_logs`:23, `rate_limit_logs`:127, `log_security`:487) + 10 log tablosu |
| [[../../.sql/mysql/coremusic_auth.sql]] | `credential_audit`:249 · `permission_audit`:352 · `admin_activity_log`:413 · **`api_keys`:288** (C4 çakışması) |
| [[../../.sql/mysql/coremusic_media.sql]] | `media_audit`:204 |
| [[../../.sql/mysql/coremusic_api.sql]] | **`api_keys`:31** (C4 çakışması ikinci tarafı) |
| [[../../.sql/mysql/coremusic_patch.sql]] | `migration_log`:35 (migration denetim tablosu) |
| [[../../.sql/mysql/coremusic_system.sql]] | `system_migration_log`:325 |
| [[../../.sql/mysql/coremusic_musics.sql]] | `created_at` karışık dosya örneği (7 DT/12 TS — C2) |

**Wiki-link KURULMAYAN (diskte hedef yok/yalnız kod → düz metin):**

| Referans | Durum |
|---|---|
| `shared/src/Security/UuidV7.php` · `auth.coremusic.net/include/Repository/UserRepository.php` | Kod dosyası (wiki-link `.md`'ye bağlanır) → düz metin, varlığı glob ile doğrulandı ✅ |
| `.claude/skills/database-normalize-maker/*` | Skill snippet'leri (şablon) — düz metin |
| **ADR-050-multi-db-sync-strategy** | `[[../index.md]]:91` kaydı var, dosya **YOK** → `⚠️ VERIFICATION REQUIRED` (bu ADR'de referanslanmaz) |
| **Üretim MySQL sürümü / canlı GRANT envanteri** | Bu repo'da yok → `⚠️ VERIFICATION REQUIRED` (REDACTED) |

### §6.1 Debate Şartları → Doküman Eşlemesi (3 şart / 4 madde — yeni wiki-link kurulmadı, §6 hedef sayısı 33/33 korunur)

| Şart | İlgili doküman (§6 satırı) | İlişki |
|---|---|---|
| **Şart 1a** — `api_keys` tek SSOT | `ADR-020-api-public-security` · `ADR-040-database-authority` | C4 iki-DB çakışması tek sahiplik kararına bağlanır; erişim şema-isimli (`db.tablo`) — §5.1 adım 14 |
| **Şart 1b** — grandfathered kuralı | `ADR-033-sql-normalization-strategy` · `ADR-040-database-authority` | Mevcut = izinli (beyaz liste), yeni/yazılan = standart; lint deny-listesi (R2) — §5.1 adım 15 |
| **Şart 2** — view PLANNED etiketi | `k5-veri-yonetimi/mysql-18-database.md` (`:252,:273`) | K5'teki 2 view şemada 0 → PLANNED (doküman) etiketi sabitlenir (C3) — §5.1 adım 16 |
| **Şart 3** — CI denetimi + N+1 testi | `ADR-002-pdo-mandatory-no-orm` · `.agents/data-engineer.md` (`:381`) | Naming/typing denetimi (§5.1/5) + N+1 testi (§5.1/11) query budget'a bağlanır — §5.1 adım 17 |

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
| **Tur 1 — Bulgular (20 persona)** | **31 benzersiz kaynak / 33 atıf** (5 websearch sorgusu) · çelişki defteri: **C1** `uk_` 32 ↔ `idx_*_unique` 28 · **C2** `created_at` 11/18 dosyada tutarsız → **DATETIME (UTC)** · **C3** K5 view **dokümanda 2 / şemada 0** · **C4** `api_keys` 2 DB'de (`coremusic_api:31` + `coremusic_auth:288`) · trigger/PROCEDURE/view kodda **0** · K5 view = **doküman-only** · **grandfathered (mevcut) vs yeni standart** ayrımı · N+1 örnekleri → **15 kabul/neutral + 4 uyarı** (QA: CI denetimi; DevOps: grandfathered net; Critic: C4 SSOT + C3 PLANNED şartı) |
| **Tur 2 — İtiraz→çözüm** | (1) C4 `api_keys` 2 DB → **tek SSOT kararı** (ADR-020/040 hizası) → **Şart 1a** · (2) C1/C2 grandfathered → **mevcut = izinli, yeni = standart** kuralı maddeleştirilir → **Şart 1b** · (3) C3 view doküman-only / şemada 0 → **PLANNED etiketi** → **Şart 2** · (4) denetim yok → **naming/typing CI denetimi + N+1 testi** → **Şart 3** |
| **Tur 3 — Oy** | **18 kabul / 2 çekimser / 0 red → KABUL** |
| **Karar kapsamı kaynağı** | Kullanıcı onaylı **6 alt konu**: (a) şema adlandırma standardı · (b) veri tipi standardı (int/VARCHAR/zaman/decimal) · (c) view & materyalize view politikası · (d) trigger & procedure politikası (varsayılan yasak, istisna belgeli) · (e) audit/log tabloları (ADR-022 hizası) · (f) N+1/erişim desenleri (ADR-039 servis sınırı hizası) |
| **Debate'de doğrulanacaklar** | **1)** §2.2-b **DATETIME standardı** — mevcut 257 TIMESTAMP çoğunluğuna karşı 2038/TZ gerekçesi (§3 alt.4 gerilimi) · **2)** §2.2-d trigger **kapalı istisna yolu** (4 kapı) sıkılığı · **3)** §2.2-c view istisna kriterleri (5 madde) — sıkı mı gevşek mi · **4)** §2.2-a `api_keys` iki-DB çakışmasının **kabul + şema-isimli zorunluluk** kararı (C4) |
| **Tur planı** | Tur 1 = kanıt sunumu (§1.1 A-F + §1.3 33 atıf) · Tur 2 = itiraz→çözüm (4 madde) · Tur 3 = oy (18/2/0 hedefi — ADR-033/040 geleneği) |
| **Şartlar (bağlayıcı)** | **Şart 1:** (1a) C4 `api_keys` iki-DB çakışması → **tek SSOT kararı** (ADR-020/040 hizası) · (1b) C1/C2 **grandfathered** kuralı — **mevcut = izinli, yeni/yazılan = standart** · **Şart 2:** K5 view'ları (C3, doküman 2 / şema 0) → **PLANNED etiketi** sabitlenir · **Şart 3:** **naming/typing CI denetimi + N+1 testi** → uygulama **§5.1 adımlar 14-17** |
| **Bekleyenler** | Arch Lead ⏳ · §5.1 adımları 5-17 (adım 13 debate ✅; **12'si PLANNED**, 4'ü debate şartı: adım 14-17) · `brain.md`/`keys.md`/`index.md` özeti bağlanması (adım 12, vault sync) |
| **Statü özeti** | `status: accepted` (kullanıcı onaylı kapsam) · debate **✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** · **Tech Lead ✅** · **Arch Lead ⏳** · **3 bağlayıcı şart** (§5.1 adımlar 14-17 · §6.1) · **frozen YOK** (ADR-001-037 dokunulmaz; bu dosya Active) · wiki-link **33/33 diskte** · disk kanıtı: 18 SQL / 156 tablo / 260 PHP (2026-09-26) · §1.3: 5 sorgu / 33 atıf / 31 benzersiz kaynak |

---

*ADR-041 v1.0.0 — CoreMusic Architecture Decision Record*
*Authority: ADR-041 Karar Metni (SSOT)*
*Last Updated: 2026-09-26*
*Mode: Red Team · Human Mode · Truth Mode*
