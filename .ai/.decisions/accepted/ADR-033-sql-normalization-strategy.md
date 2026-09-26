---
id: ADR-033
title: SQL Normalizasyon Stratejisi — BCNF Birincil + Kontrollü Denormalizasyon, 18 DB Ortak Kural Seti (Normal Form, PK/FK, Index, ENUM/Lookup, Çoğaltma/Audit)
type: adr
category: database
date: 2026-09-25
updated: 2026-09-25
version: 1.0.0
status: accepted
authority: ADR-033 Karar Metni (SSOT)
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
deciders: ["Vault Steward", "Data Engineer"]
consulted: ["Backend Architect", "Security Engineer", "QA Engineer"]
informed: ["Master Orchestrator", "DevOps Engineer"]
supersedes: null
superseded-by: null
related:
  - "[[.ai/.decisions/accepted/ADR-003-multi-db-bcnf.md]]"
  - "[[.ai/.decisions/accepted/ADR-007-cache-namespace.md]]"
  - "[[.ai/.decisions/accepted/ADR-014-multi-db-migration-strategy.md]]"
  - "[[.ai/.decisions/accepted/ADR-022-database-hardened-security.md]]"
  - "[[.ai/.decisions/accepted/ADR-002-pdo-mandatory-no-orm.md]]"
---

# ADR-033: SQL Normalizasyon Stratejisi — BCNF Birincil + Kontrollü Denormalizasyon

**Durum:** accepted (Draft → Review → Active → **Active**; frozen YOK)
**Tarih:** 2026-09-25
**Karar Veren:** Vault Steward (kullanıcı onaylı karar kapsamı) + Data Engineer (domain)
**İlgili ADR'ler:** [[.ai/.decisions/accepted/ADR-003-multi-db-bcnf.md]] (18 DB + BCNF zemini) · [[.ai/.decisions/accepted/ADR-007-cache-namespace.md]] (read-path hızı) · [[.ai/.decisions/accepted/ADR-014-multi-db-migration-strategy.md]] (forward-only migration) · [[.ai/.decisions/accepted/ADR-022-database-hardened-security.md]] (audit alanları) · [[.ai/.decisions/accepted/ADR-002-pdo-mandatory-no-orm.md]] (PDO/ERİŞİM — EXPLAIN kapısı)

---

## 1. Bağlam ve Kod Kanıtı

### 1.1 Proje durumu (kod kanıtı — IMPLEMENTED / PLANNED ayrımı)

Bu ADR, CoreMusic'in **18 veritabanının tamamı** için geçerli tek normalizasyon kural setini yazar: normal form seviyesi, PK/FK kuralları, index politikası, ENUM↔lookup kararı ve çoğaltma/audit alanları. Kanıt, `.ai/.sql/mysql/` altında **18 `.sql` dosyasının satır satır taranmasıyla** derlendi (2026-09-25) ve iki kategoride etiketlendi: **IMPLEMENTED** (şemada mevcut) / **PLANNED** (kural olarak kararlaştırılan, şemada karşılığı henüz yok).

**A) Envanter sayımı (disk kanıtı — IMPLEMENTED):**

| Ölçüm | Değer | Kanıt |
|-------|-------|-------|
| Şema dosyası | **18** (ADR-003'ün "18 DB" hükmü teyit edildi) | `Get-ChildItem .ai/.sql/mysql -Filter *.sql` → 18 |
| Tablo | **156** `CREATE TABLE` | dosya başı regex sayımı (toplam 156) |
| Primary key | **156** `PRIMARY KEY` → **PK zorunluluğu %100** | 156 = 156 (PK'sız tablo 0) |
| FK tanımı | **82** → 50 satır-içi `FOREIGN KEY` (CREATE TABLE içinde) + 32 `ADD CONSTRAINT ... FOREIGN KEY` satırı; `ADD CONSTRAINT` toplam 54 (22'si PK/UNIQUE, FK değil) | regex sayımı (`FOREIGN KEY` geçen yorum satırı = 0) |
| Cross-DB FK | **28 gerçek constraint / 3 dosya** — `coremusic_user.sql` 11 (`:50,83,114-115,138,160-161,188-189,223-224`) · `coremusic_social.sql` 13 (`:47,67,91,118,119,150,151,180,204,205,235,266,267`) · `coremusic_system.sql` 4 (`:69,100,128,225`) | `REFERENCES <başka DB>` (yorum satırı hariç) — düzeltme sayım hatası: ilk yazım "11 / tek dosya" idi |
| Cross-DB FK'yı "desteklemiyoruz" diyen yorum | **15** satır | `coremusic_albums.sql:53,54,128,154` vb. (`-- Note: ... cross-database FK not supported in MySQL`) |
| Index tanımı | **550** → 364 satır-içi `INDEX` + 70 satır-içi `UNIQUE KEY` + 116 `CREATE [UNIQUE] INDEX` (28'i unique) | dosya başı regex sayımı |
| Index yoğunluğu | **3,5 index / tablo** | 550 / 156 |
| **PK hariç index'i olmayan tablo** | **0** (her tabloda ≥1 ikincil index) | tablo-bazlı tarama (inline + `CREATE INDEX` hedefleri dâhil) |
| Audit alanı | `created_at` → **211** referans | dosya geneli grep |

**B) Normal form ihlalleri / denormalizasyon bulguları (dosya:satır — dürüst etiket):**

1. **ENUM string kolon — 96 adet, 18 dosyanın 11'inde:** [[.ai/.sql/mysql/coremusic_social.sql]]`:26` `entity_type ENUM('music','album','playlist','podcast','radio','video')` · [[.ai/.sql/mysql/coremusic_logs.sql]]`:52` `activity_type ENUM('login','logout',...)` · [[.ai/.sql/mysql/coremusic_auth.sql]]`:30` `account_type ENUM('free','premium','studio','admin')` · [[.ai/.sql/mysql/coremusic_system.sql]]`:27` `setting_type ENUM('string','integer',...)`. Dağılım: system 20 · logs 17 · user 10 · wireless 10 · social 9 · auth 8 · media 8 · musics 7 · playlist 3 · albums 2 · catalog 2. → **lookup tablosu yerine satır-içi sabit küme**; değer eklemek `ALTER TABLE` gerektirir (§1.3 kaynak 28-32).
2. **İşlevsel bağımlılık ihlali (türetilmiş özet kolon) — 79 adet, 12 dosya** (sayım deseni: adında `count`/`total` geçen sayısal kolon): [[.ai/.sql/mysql/coremusic_playlist.sql]]`:31` `total_tracks`, `:33-35` `follow_count`/`like_count`/`play_count` · [[.ai/.sql/mysql/coremusic_albums.sql]]`:34` `total_tracks`, `:37` `play_count` · [[.ai/.sql/mysql/coremusic_musics.sql]]`:106-108` `play_count`/`like_count`/`download_count` · [[.ai/.sql/mysql/coremusic_social.sql]]`:30-31` `like_count`/`reply_count`. → Anahtar olmayan kolon, anahtardan **türetilir** ve satırda saklanır: yazma-anomalis riski + write amplification (§1.3 kaynak 7-14).
3. **Çoğaltma (kopya kolon — FK'sız kopya):** [[.ai/.sql/mysql/coremusic_musics.sql]]`:732-745` `radio_now_playing` (kopya kolonlar `:735-739` → `track_title`, `artist_name`, `album_name`, `genre`, `duration_seconds`) bu kolonlar `musics`/`artists` verisini **kopyalar, FK yok** → kaynak değişince kopya kalır (denorm drift).
4. **Çoğaltma (aynı metrik birden fazla DB'de):** `play_count` **6 noktada / 4 DB'de**: musics `:106`, `:458` · albums `:37` · playlist `:35` · logs `:205`, `:229` → iki gerçeklik kaynağı; hangisinin otorite olduğu tanımsız.
5. **Atomik olmayan JSON array kolon — 45 adet** (JSON geçen satır 49; 4'ü ENUM/COMMENT içeriği, kolon tanımı değil): [[.ai/.sql/mysql/coremusic_auth.sql]]`:62` `permissions JSON` · [[.ai/.sql/mysql/coremusic_system.sql]]`:54` `bands JSON` · [[.ai/.sql/mysql/coremusic_system.sql]]`:141` `cache_tags JSON` · `coremusic_wireless.sql:150` `dns_servers JSON` → 1NF'in atomiklik ruhuna aykırı ama kasıtlı (yapılandırılmış blob).
6. **Klasik 1NF "tekrar eden grup" (ör. `tag1..tag5`):** `\w+[1-9] (VARCHAR|INT)` deseniyle tarama → **0 gerçek eşleşme** (yalnız `checksum_sha256` yanlış pozitifi) → **bulunamadı**; uydurulmadı, yok olarak kaydedildi.
7. **Cross-DB FK çelişkisi (kural vs. şema):** 28 gerçek constraint — [[.ai/.sql/mysql/coremusic_user.sql]]`:50,83,114,115,138,160,161,188,189,223,224` (11) · [[.ai/.sql/mysql/coremusic_social.sql]]`:47,67,91,118,119,150,151,180,204,205,235,266,267` (13) · [[.ai/.sql/mysql/coremusic_system.sql]]`:69,100,128,225` (4) — hepsi `REFERENCES coremusic_auth.*` / `coremusic_musics.*` derken ADR-003 "DB arası FK YOKTUR" (ADR-003 §2.2 Alınan Karar) ve 15 yorum satırı "cross-database FK not supported" diyor → **iki politika yan yana yaşıyor**; düzeltme bu ADR'nin §5.2/3 kalemidir.

**C) Şemanın kendi BCNF iddiası (self-declaration — denetim DEĞİL):**

Dosya başlıkları BCNF diyor: `coremusic_auth.sql:3` "-- BCNF Normalized" · `coremusic_catalog.sql:3` · `coremusic_cms.sql:4` "-- BCNF : Yes" · `coremusic_musics.sql:756` "BCNF Compliant: Yes" · satır-içi gerekçe `coremusic_auth.sql:20` "-- BCNF: All non-key attributes fully depend on the candidate key" · `coremusic_cms.sql:158` "-- BCNF — id -> {question, answer, ...}". Bunlar **iddiadır, denetim değildir**: 156 tablonun determinant/bağımlılık denetimi henüz çalıştırılmadı → **PLANNED**.

**D) IMPLEMENTED / PLANNED ayrımı (dürüst etiket):**

- **IMPLEMENTED (şemada var):** 18 şema · 156 PK · 82 FK · 550 index · BCNF self-declaration · 6 audit tablosu + 211 `created_at` (ADR-022 §1.1) · `schema_versions` tablosu (`.ai/.sql/mysql/coremusic_patch.sql:13-14`, ADR-032 §1.1 ile aynı bulgu).
- **PLANNED (bu ADR ile kurulan kural, şemada karşılığı 0):** 156 tablo BCNF denetimi · istisna defteri (denormalizasyon kayıt tablosu) · ENUM → lookup geçişi · cross-DB FK düzeltmesi (28 satır / 3 dosya) · workload/EXPLAIN tabanlı index denetimi · sayaç reconciliation job'u · `play_count` tek otorite kararı.

**E) İlgili hizalama sorularının dürüst cevapları:**

- **ADR-003 (multi-DB BCNF):** **hizalı** — karar dizini `.ai/.decisions/index.md:40` slug `ADR-003-multi-db-bcnf` (`.ai/index.md:620` ve `keys.md:87` de aynı slug). Dizin satırındaki "**9** BCNF veritabanı" metni 18 ile çelişir; bu sayısal tutarsızlık ADR-003'ün kendi §1.1'inde ("9 eski anlık görüntü, doğru olan 18") kayıtlıdır → tekrar sayı uydurulmadı, 18 kullanıldı. ADR-003'ün §1.3 "Sonucu" 1. madde **BCNF denetimi/dependency-preservation işini açıkça bu ADR'ye devrediyor** ("denetim ADR-033 (sql normalization) ... işidir") → kapsam burada kesişir, tekrar yok.
- **ADR-014 glossary düzeltmesi (frozen → accepted):** **geçerli** — `glossary.md:440` ADR-014'ü "accepted · debate ✅ 3 tur/20 persona, 18/2/0 KABUL — **frozen değil**" olarak yazıyor; frozen/accepted ayrımı düzeltilmiş durumda.
- **ADR-002 satır 18:** "ADR-033-sql-normalization-strategy tekil dosyası diskte YOK" der → bu yazımdan sonra **bayatlar**; ADR-002 frozen (001-037) olduğu için metnine **dokunulmaz**, bayatlık `log.md`'de not edilir (§6.3 usulü).
- **Eski seri numarası:** `.ai/.decisions/index.md:70`, `.ai/index.md:650`, `keys.md:89` ve `brain.md:988` ADR-033 kaydını **çoktan taşıyor** (`SQL normalization` / `BCNF normalizasyon`) → dosya bu ayrılmış numaraya yazılır; `088+` yeni-numara kuralı burada geçerli değildir (bkz. §6).

### 1.2 Sorun Tanımı

18 şemada normalizasyon **iddia** düzeyinde kalıyor: her dosya BCNF yazıyor, ama (1) 156 tablonun hiçbirinde determinant denetimi çalıştırılmamış, (2) kural seti hiçbir dosyada tek yerde toplanmamış — PK zorunluluğu, FK politikası, index yoğunluğu, ENUM↔lookup ve sayaç çoğaltması hakkında **her şema kendi kuralını yazıyor**, (3) bulgular çelişkisiz değil: 28 cross-DB FK (3 dosya), ADR-003'ün "DB arası FK YOK" hükmüyle ve kendi dosyasındaki 15 yorum satırıyla çelişiyor, (4) 96 ENUM + 79 türetilmiş sayaç + tek metriğin 4 DB'de çoğaltılması kayıtsız duruyor — hangisinin kasıtlı "kontrollü denormalizasyon" olduğu, hangisinin ihlal olduğu **hiçbir yerde yazılmıyor**. Sonuç: yeni bir tablo açan geliştirici "BCNF mi, hızlı okuma mı?" sorusuna vault'tan cevap bulamıyor; denormalizasyon gizlice birikiyor ve denetimi imkânsızlaşıyor. Bu ADR **tek SSOT kural seti**ni yazar: neyin kural, neyin belgelenmiş istisna olduğunu birbirinden ayırır.

### 1.3 İlgili web araştırması

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "BCNF vs 3NF normalization 2025 best practices database normalization still relevant" · (2) "denormalization trade-offs write amplification consistency risk materialized counter columns" · (3) "MySQL index strategy best practices covering index selectivity composite index EXPLAIN workload" · (4) "schema design driven by query workload read-heavy OLTP versus analytics" · (5) "ENUM column versus lookup table MySQL referential integrity" |
| Web Search **Konusu** | Normalizasyon kural setinin beş karar ekseninin güncel ekosistem kanıtı: BCNF/3NF hâlâ gerekli mi, denormalizasyonun bedeli (write amplification + tutarlılık), MySQL index tasarımı (composite/covering/EXPLAIN), sorgu workload'ına göre şema tasarımı ve ENUM yerine lookup tablosu. |
| Web Search **Bağlam** | 2025-2026 verisi okundu: DigitalOcean + centron + Medium (Artem Khrenov) + PhoenixAI + Knack + Stack Overflow (normal form tanımları ve BCNF'nin 3NF'e üstünlüğü) · Splunk + DataCamp + codelit + wild.codes + VeloDB + C2 Wiki + LinkedIn (denormalizasyon trade-off) · OneUptime 2026 + Mydbops + MariaDB docs + Redgate Simple Talk + Stack Overflow + Alibaba Cloud (MySQL index) · PuppyGraph + Tinybird + Aerospike + VeloDB + Materialize + Pure Storage (OLTP/OLAP workload) · SitePoint + OneUptime 2026 + Cybertec + dev.to + Stack Exchange (ENUM↔lookup). Protokol: `[[.claude/skills/prompt-maker/references/10-web-research-protocol.md]]` (diskte VAR ✓) — resmi/anahtar kaynak önce, her ana iddia ≥2 bağımsız çapraz kaynak, kanıtsız iddia → `⚠️ VERIFICATION REQUIRED`. |
| Web Search **Kısa Açıklama** | Normalizasyon: BCNF, 3NF'in kaldıramadığı örtüşen candidate key durumlarını da temizler ve OLTP'de hâlâ varsayılandır; denormalizasyon: okuma hızı verir ama bedeli **write amplification + stale/drifting data**dır, bu yüzden "en küçük denormalizasyon" seçilir; MySQL index: composite + covering + `EXPLAIN` ile workload'tan türetilir; ENUM: değeri büyüyen/kod adı taşıyan alanlarda lookup tablosu (`INSERT` ile genişler, `ALTER` gerekmez) ama her sorguya join bedeli getirir. |
| Web Search **Uzun Açıklama** | **(1) Normalizasyon:** DigitalOcean 1NF-BCNF örnekleriyle BCNF'yi "3NF'in örtüşen anahtar boşluklarını kapatan" katman olarak tarif eder; centron "tasarımın ilk aşamasında normalizasyon varsayılandır, denormalizasyon yalnız performans kritikse" der; PhoenixAI tablosunda 3NF "OLTP'de en yaygın", BCNF "karmaşık anahtarlarda" işaretlidir; Knack 3NF sonrası formları (BCNF/4NF/5NF) "özel veri zorlukları" olarak tanımlar; Stack Overflow cevapları klasik "anahtar / bütün anahtar / yalnız anahtar" şerhini BCNF'ye taşır → **BCNF birincil karar desteklenir** (6 kaynak, çapraz ≥2). **(2) Denormalizasyon:** Splunk gerekliliği "ilgili veriyi tek yapıda birleştirip okuma performansı için kasıtlı fazlalık" olarak tanımlar; DataCamp açıkça **write amplification** ve **stale/inconsistency**'yi başarısızlık modu yazıyor ve "en küçük denormalizasyonu seç" kuralını koyuyor; codelit "yazma hacmi yüksekse ve tutarlılık katıysa denormalizasyon risklidir" der; wild.codes takası "işlem yüküne bağlı"ya bağlar; VeloDB/C2/LinkedIn aynı takası tekrarlar → **"kontrollü + belgelenmiş" istisna kuralı** bu kaynaklarla kurulur (8 kaynak). **(3) MySQL index:** OneUptime (2026) B-tree/composite/covering uygulamasını; MariaDB "verilen SELECT için en iyi index" tarifini; Redgate composite ile covering'i ayırır ("covering = sorgunun gereken tüm alanlarını kapsar"); Alibaba Cloud clustered/secondary ayrımı ve `EXPLAIN`/`force index` kullanımını; SO "composite ≠ covering"i netleştirir → **index workload'tan türetilir, "her kolona index" değil** (7 kaynak). **(4) Workload-driven tasarım:** Aerospike "OLTP'de normalize (tutarlılık), analitikte denormalize (okuma)" hükmünü verir; PuppyGraph/Tinybird/VeloDB read-heavy'in denormalize şema istediğini; Pure Storage "daha az, daha geniş tablo + önceden toplama"yı; Materialize ise **denormalizasyon job'larının geliştirici zamanı/complexity** maliyetini yazıyor → "önce normalize + index/cache, sonra proje bazında istisna" (6 kaynak). **(5) ENUM↔lookup:** SitePoint "ek değer için tablo tanımını değiştirmek gerekir" der; OneUptime (2026) "büyüyen/kod metadatası taşıyan alanlarda lookup kazanır, `INSERT` yeterli"; Cybertec üç seçeneği (check string / enum / lookup) karşılaştırıp lookup'ı "esnek ama join bedeli + kötü satır tahmini" ile; dev.to referans bütünlüğünü lookup lehine kullanır; Stack Exchange "değerler kodda referanslanıyorsa" senaryosunu tartışır → **"ENUM dokunulmaz, yeni alan lookup"** ödülü kaynaklarla tutarlı (5 kaynak). |
| Web Search **Paragraf Veri Uzun** | 5 paragraf, **32 adlandırılmış kaynak, 5 sorgu**: normalizasyon/BCNF (6), denormalizasyon trade-off (8), MySQL index (7), workload-driven tasarım (6), ENUM↔lookup (5). Çapraz doğrulama ≥2 bağımsız kaynak beş eksenin beşinde de karşılanır; "156 tablo BCNF" iddiası web'e değil şema taramasına dayanır (§1.1-C) ve **denetimsiz iddia** olarak işaretlidir. |
| Web Search **Sonucu** | (a) **BCNF birincil doğrulandı** (6 kaynak): OLTP'de varsayılan, 3NF boşluklarını kapatır; (b) **denormalizasyonun bedeli doğrulandı** (8 kaynak): write amplification + drift/staleness → istisna zorunlu; (c) **index workload'tan türetilir doğrulandı** (7 kaynak): composite/covering + `EXPLAIN`, her kolona index şişirir; (d) **önce normalize+index/cache** doğrulandı (6 kaynak): analitik/yoktan geniş okuma istisnaya girer; (e) **ENUM↔lookup ödülü doğrulandı** (5 kaynak): büyüyen alanlarda lookup, sabit/alçak maliyetli alanlarda ENUM makul. |
| Web Search **Alınan Karar** | **(a) BCNF birincil** — yeni/özgün şemalar BCNF; 156 mevcut tablo **denetime açılır** (iddia ≠ kanıt); **(b) kontrollü denormalizasyon** — read-path hızı için (cache [[.ai/.decisions/accepted/ADR-007-cache-namespace.md]], rapor/analiz) yalnız **belgelenmiş istisna** olarak: tablo/kolon + neden + risk + senkron mekanizması + tersine plan; **(c) güncelleme yazmalarında tutarlılık korunur** — türetilmiş sayaç tek yazıcıdan (ADR-003 tek-yazıcı ilkesi), drift periyodik reconciliation ile kontrol edilir; **(d) 18 DB ortak kural seti tek SSOT**: PK zorunlu (bugün %100), FK ile ilişki + DB arası FK yasağı (28 ihlal düzeltilecek), index workload/EXPLAIN ile (bugün 3,5/tablo, PK-only tablo 0), ENUM→lookup **yeni alanlarda** (mevcut 96 ENUM dokunulmaz, geçiş PLANNED), çoğaltma/audit alanları ADR-022 ile hizalı. |
| Web Search **Sonuç** | Karar beş eksende de 2025-2026 verisiyle **desteklendi** (32 kaynak / 5 sorgu). Kod tarafı da aynı resmi verdi: şema BCNF'yi **iddia** ediyor ve index/PK altyapısı güçlü (156 PK, 550 index, 0 PK-only tablo), ama denetim **yok**, 96 ENUM, 79 türetilmiş sayaç, tek metrik 4 DB'de çoğaltma ve 28 cross-DB FK (3 dosya) **mevcut** → bu ADR **kural + kapı** kararıdır; denetim ve geçiş uygulaması §5.2 parçalarına bağlıdır. |

**Kaynak listesi (32):** 1) digitalocean.com — Database Normalization: 1NF, 2NF, 3NF & BCNF Examples · 2) centron.de — Database Normalization Explained: 1NF, 2NF, 3NF & BCNF Guide · 3) medium.com/@artemkhrenov — Understanding Database Normalization: From 1NF to BCNF · 4) phoenixdata.ai — Normalization vs Denormalization: The Trade-offs You Need to Know · 5) knack.com — Database Normalization for Beginners (Beyond 3NF: BCNF, 4NF, 5NF) · 6) stackoverflow.com/questions/8437957 — Difference between 3NF and BCNF · 7) splunk.com — Data Denormalization: The Complete Guide · 8) datacamp.com — Denormalization in Databases: When and How to Use It · 9) codelit.io — Database Denormalization Patterns: When, Why & How to Break the Rules · 10) wild.codes — What trade-offs exist between normalization and denormalization · 11) velodb.io — Normalization vs Denormalization · 12) wiki.c2.com — Denormalization Is Ok · 13) linkedin.com (Eugene Koshy) — Normalization vs. Denormalization for Performance · 14) medium.com/@iamprovidence — Denormalize your Database like there is no tomorrow · 15) oneuptime.com (2026-02-02) — How to Create Effective Indexes in MySQL · 16) mydbops.com — A Comprehensive Guide to Efficient MySQL Indexing · 17) mariadb.com — Building the best INDEX for a given SELECT · 18) red-gate.com (Simple Talk) — The nuances of MySQL indexes · 19) stackoverflow.com/questions/8213235 — MySQL covering vs composite vs column index · 20) alibabacloud.com — Deep Dive into MySQL Indexing Strategies · 21) vinodatwal.medium.com — Best Practices for Indexing SQL DB · 22) puppygraph.com — OLTP vs OLAP: Key Differences, Use Cases & Comparison · 23) tinybird.co — OLTP vs OLAP: key differences, use cases, and architectures · 24) aerospike.com — OLTP vs. OLAP Explained · 25) velodb.io — OLAP Database: Definition, Use Cases & Best Tools (2026) · 26) materialize.com — OLTP Queries: Transfer Expensive Workloads · 27) blog.everpuredata.com — Denormalized vs. Normalized Data · 28) sitepoint.com — Understanding ENUM and "Look-up Tables" · 29) oneuptime.com (2026-03-31) — How to Avoid Using ENUM When You Should Use a Lookup Table in MySQL · 30) cybertec-postgresql.com — What is better: a lookup table or an enum type? · 31) dev.to/anwar_nairi — Should you use an enum column or a table to store allowed values in SQL? · 32) softwareengineering.stackexchange.com — SQL: enum vs reference table when values are referenced in the code.

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| Frozen ADR 001-037 dokunulmaz | Bu ADR yeni dosyadır (metni frozen değildir); frozen ADR'ler okunur/referanslanır, düzenlenmez (AGENTS.md §25.3 kural 2) |
| 18 DB tek SSOT + tek yazıcı | Kural seti 18 şemanın tamamına uygulanır; her tablonun tek sahibi vardır (ADR-003), çoğaltma yalnız bu ADR'nin istisna kaydıyla mümkündür |
| ORM yok, PDO + prepared (ADR-002) | Index/politika kararları ORM migration'ına değil, elle yazılmış migration + `EXPLAIN` kapısına dayanır |
| Forward-only migration (ADR-014) | Şema değişikliği revert edilemez → geri dönüş = **yeni düzeltme migration'ı** (fix-forward); bu ADR'nin geri dönüş planı da ona bağlıdır |
| Mevcut ENUM/şema dokunulmaz (grandfathered) | 96 ENUM, 79 sayaç, 28 cross-DB FK **hemen değiştirilmez**; geçiş PLANNED'dir ve ayrı iş kalemi gerektirir |
| Cache ADR-007'e bağımlı | read-path hızlandırması şemada değil cache katmanında; denormalizasyon gerekçesi cache TTL'siz yazılamaz |
| REDACTED | Şema/istisna defterine secret, credential, bağlantı dizesi yazılmaz |
| In-Place Refactoring | Dosya adları (`coremusic_*.sql`) onaysız değiştirilemez; bu ADR yalnız kural ve kapı yazar |

---

## 2. Karar

**CoreMusic'in 18 veritabanının tamamında TEK normalizasyon kural seti yürürlüğe girer: (a) BCNF birincildir — yeni ve gözden geçirilen şemalar BCNF'dir (ADR-003 ile hizalı); (b) denormalizasyon yalnızca KONTROLLÜDÜR — read-path hızı için yapılan her istisna, nedeni + riski + senkron mekanizması + tersine planıyla BELGELENİR; (c) güncelleme yazmalarında tutarlılık korunur — türetilmiş alan tek yazıcıdan güncellenir ve periyodik reconciliation ile doğrulanır. Kural seti beş eksendedir: normal form, PK/FK, index politikası, ENUM/lookup, çoğaltma/audit.**

### 2.1 Gerekçe 1 — BCNF iddia ediliyor ama denetlenmiyor; kural yerine dosya başlığı var

Şemanın her dosyası BCNF diyor (`coremusic_auth.sql:3`, `coremusic_musics.sql:756`) ve ADR-003 bu mimariyi kurdu; ancak ADR-003 kendi §1.3/1 sonucunda **BCNF denetimini ve dependency-preservation süregelen denetimini açıkça ADR-033'e (bu ADR'ye) bırakıyor**. Yani boşluk kasıtlı olarak buraya ayrılmıştı. §1.3 kaynak 1-6 aynı hükmü veriyor: OLTP'de normalizasyon varsayılandır, BCNF 3NF'in örtüşen-anahtar boşluğunu kapatır → **varsayılanı yaza yazmaya başlıyoruz**, mevcut 156 tabloyu da denetime açıyoruz (iddia ≠ kanıt).

### 2.2 Gerekçe 2 — Denormalizasyon zaten var, kayıtsız: kural koymazsa büyür

Bulgu net: 79 türetilmiş sayaç, tek metriğin 4 DB'de çoğaltması, FK'sız kopya kolonlar (`radio_now_playing`), 45 JSON array, 96 ENUM. Bunlar bugün **ne serbest ne yasak** — bu gri alan denetimi imkânsızlaştırır. §1.3 kaynak 7-14 "en küçük denormalizasyonu seç + her istisnayı yaz" derken, kaynak 24-27 denormalizasyon job'larının maliyetini yazıyor → karar: denormalizasyonu **yasaklamak değil, faturalandırmak** (5 alan zorunlu).

### 2.3 Gerekçe 3 — Index politikası workload'tan türetilmeli, sayıyla değil

Bugün sayı sağlıklı (550 index, 3,5/tablo, PK-only tablo **0**) — ama "kaç index" bir politika değil. §1.3 kaynak 15-21: composite/covering `EXPLAIN` ile doğrulanır, her kolona index yazma-okuma dengesini bozar (index şişmesi). ADR-003 de `coremusic_musics`'i "okuma-yoğun, indeks ağırlıklı" diye işaretliyor → politika **sorgu workload'una** bağlanır, sabit sayıya değil (ADR-002'nin `EXPLAIN` kapısıyla aynı kapıda çalışır).

### 2.4 Teknik Detaylar

**(a) Strateji — üç madde:**

| # | Madde | Hükmün anlamı |
|---|-------|----------------|
| a1 | **BCNF birincil** | Yeni tablo/şema + gözden geçirilen şema: her determinant = candidate key; lossless-join + bağımlılık korunumu aranır |
| a2 | **Kontrollü denormalizasyon** | Yalnız read-path hızı için (cache ADR-007, rapor/analiz okuması); **her istisna 5 alanla belgelenir**; belgelenmeyen = ihlal |
| a3 | **Güncelleme yazmalarında tutarlılık** | Türetilmiş alan tek yazıcıdan (ADR-003) transaction içinde güncellenir; drift periyodik reconciliation ile aranır |

**(b) 18 DB ortak kural seti (tek SSOT — 5 eksen):**

| # | Eksen | Kural (bağlayıcı) | Bugünkü durum (kanıt) | Geçiş |
|---|-------|-------------------|------------------------|-------|
| 1 | **Normalizasyon seviyesi** | BCNF; istisna = belgelenmiş denormalizasyon (a2) | 156/156 tablo **BCNF iddiası**, denetim **0** (PLANNED) | Denetim §5.2/1 |
| 2 | **PK/FK kuralları** | PK her tabloda zorunlu; ilişki FK ile kurulur; **DB arası FK yasak** (ADR-003) — çapraz referans application-level ref veya outbox event | PK **156/156** ✅ · FK 82 · **DB arası FK 28 ihlal** (3 dosya: user 11 / social 13 / system 4) | İhlal düzeltmesi §5.2/3 (PLANNED) |
| 3 | **Index politikası** | Sorgu workload'ına göre: composite/covering `EXPLAIN` ile; FK/kolon sorguları için index zorunlu; **"her kolona index" yasak**; PK hariç index'i olmayan tablo kabul edilmez | 550 index / 3,5 tablo · **PK-only tablo 0** ✅ · workload ölçümü **0** (PLANNED) | Denetim §5.2/2 |
| 4 | **ENUM / lookup tabloları** | **Yeni** büyüyen/kod metadatası taşıyan alanlar lookup tablosu (INSERT ile genişler); ENUM'da kalan alan **dokunulmaz**, değer ekleme ayrı migration | **96 ENUM / 11 dosya**; lookup'a geçmiş alan **0** | Geçiş PLANNED §5.2/4 |
| 5 | **Çoğaltma / audit alanları** | `created_at` + `updated_at` + soft-delete bayrağı her tabloda; audit alanları ADR-022 ile hizalı; çoğaltma yalnız a2 istisnası | `created_at` **211** · 6 audit tablosu (ADR-022) ✅ · çoğaltma kayıtsız 79+6 | İstisna defteri §5.2/5 |

**(c) İstisna defteri (kontrollü denormalizasyonun yazılı kanıtı) — 5 zorunlu alan:**

| Alan | Zorunluluk |
|------|-----------|
| Tablo + kolon (dosya:satır) | İhlalde yeri bulunsun diye |
| Neden (hangi read-path, hangi sorgu) | Kod yoksa gerekçe de yok |
| Risk (hangi anomali: güncelleme/kalıcılık/çelişki) | Kaynağın adı |
| Senkron mekanizması (uygulama / scheduled rebuild / cache invalidation ADR-007) | Tutarlılığın nasıl korunduğu |
| Tersine plan (geri alma adımı) | Geri alınamaz olmasın |

Defter bu ADR'nin §2.4-(d) tablosudur; yeni istisna **yeni satır** olarak eklenir (ADR Active iken sınırlı revizyon — şablon §6.3 usulü; metin silinmez). Kayıt yeri tekdir (SSOT); ikinci bir defter açılmaz.

**(d) Mevcut kayıtlar (grandfathered — hemen değiştirilmez, ama artık görünür):**

| # | İstisna | Konum | Neden (okuma yolu) | Risk | Senkron | Tersine plan |
|---|---------|-------|--------------------|------|---------|--------------|
| I1 | `radio_now_playing` kopya kolonları | `coremusic_musics.sql:732-745` | anlık "çalan" okuması join'siz | drift (kaynak değişince kopya kalır) | istasyon yazımında uygulama tarafında set | `music_id` FK ekle + okumayı join'e çevir (yeni migration) |
| I2 | 79 türetilmiş sayaç (`play_count` vb.) | playlist:31-35, albums:34/37, musics:106-108, social:30-31, logs:205/229 | sayaç sorguları tablo taramasın | write amplification + drift | tek yazıcı + periyodik reconciliation | sayaç kolonunu kalıcı tut, kaynağı hesaplama tablosuna taşı |
| I3 | `play_count` 4 DB çoğaltması | musics:106,458 · albums:37 · playlist:35 · logs:205,229 | domain içi okuma (raporlama ayrımı) | **iki gerçeklik kaynağı** | otorite = `coremusic_musics` (kural), diğerleri projection | projection'ı düşür, tek kaynağı oku |
| I4 | 45 JSON array kolon | auth:62, system:54/141, wireless:150 | yapılandırma blob'u (sorgulanmıyor) | içindeki liste sorgulanamaz | yapılandırma yazımında set | alanları lookup'a taşı (yalnız sorgulanıyorsa) |
| I5 | 96 ENUM string kolon | 11 dosya (§1.1-B/1) | sabit/değişmeyen küme, join bedelsiz | değer eklemek `ALTER` (tabloyu yeniden yazar) | migration (ADR-014 forward-only) | lookup'a geç = yeni kolon + backfill + eskiyi bırak (fix-forward) |

**(e) Tutarlılık mekanizması (a3'ün uygulanışı):**

1. **Tek yazıcı:** sayaç/özet güncellemesi yalnız owner DB'de ve transaction içinde (ADR-003 tek-yazıcı ilkesi); başka DB o tabloya yazmaz.
2. **Reconciliation:** günlük/job bazında türetilmiş değer ↔ kaynak sayımı karşılaştırılır; fark varsa log + düzeltme (ADR-081 reconciliation ruhu, ADR-022 audit kaydı).
3. **Cache invalidation:** okuma tarafı cache (ADR-007) invalidation'sız yazılmaz; çoğaltma ile cache aynı anda kullanılmaz (iki hız katmanı tek gerekçe olmaz).
4. **Kapı:** şema farkı migration (ADR-014 checksum/lock) ile, sorgu farkı `EXPLAIN` (ADR-002) ile doğrulanır.

---

## 3. Alternatifler

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Hiç kural yok (statü quo)** — her dosya kendi BCNF/yorumunu yazmaya devam etsin | Sıfır çaba; şema bozulmaz | 96 ENUM + 79 sayaç + 28 cross-DB FK **kayıtsız** büyür; yeni tabloda kural sorulamaz | Zaten görülen sonuç: çelişki ve denetimsiz birikim (§1.1-B, §1.2) |
| 2 | **Sert tam normalizasyon (denormalizasyon yasak)** | Anomali riski sıfıra yakın; denetim basit | Sayaç/anlık okumalar her seferinde join/aggregate → okuma yolu cache'e (ADR-007) ve raporlara pahalıya mal olur; mevcut 79 sayaç anında ihlal sayılır | §1.3 kaynak 7-14, 22-27: read-path için en küçük denormalizasyon meşru; yasak, mevcut şemayla ve performans hedefiyle (ADR-006) çatışır |
| 3 | **Serbest denormalizasyon ("performans gerekeni yap")** | Geliştirici hızı ilk gün yüksek | Her istisna gizli kalır → denorm drift + çoğaltma tutarsızlığı ölçülemez; denetim imkânsız | §1.3 kaynak 8-9 tam bu başarısızlık modunu yazıyor; SSOT kural seti bunu önlemek için var |
| 4 | **Tüm ENUM'ları hemen lookup tablosuna çevir** | Referans bütünlüğü + `ALTER`sız değer ekleme | 96 kolon + veri backfill + 18 şema migration'ı; okuma başına ek join (§1.3 kaynak 30-31'in "join bedeli" uyarısı) | Maliyet/kazanç dengesiz; ADR-014 forward-only ile büyük çapta dönüşüm riskli → **yeni alanlarda lookup, mevcut ENUM dokunulmaz, geçiş PLANNED** |
| 5 | **Raporlama için ayrı denormalize/OLAP şeması kurmak** | Analitik yük OLTP'den ayrılır (§1.3 kaynak 22-27) | Bugünkü rapor hacmi/coremusic_logs projection'ları ile ayrı store işletimi ağır; ek altyapı | Ölçek henüz zorlamıyor; mevcut `logs` özet tabloları + cache (ADR-007) aynı işi görür → kapı açık bırakılır, ölçümle açılır |

---

## 4. Sonuç ve Sonuçlar

**Olumlu:**

- **Tek SSOT kural seti**: 18 DB için "normal form + PK/FK + index + ENUM + çoğaltma" beş ekseni tek yerde; yeni tablo açan geliştirici cevabı vault'tan okur (§2.4-b).
- **BCNF denetimi ilk kez iş kalemine dönüşüyor**: ADR-003'ün bize bıraktığı boşluk (§1.1-E) kapanıyor; iddia → kanıt rotası §5.2/1 ile başlıyor.
- **Index tabanı sağlam çıkıyor**: 156 PK (yüzde 100), 550 index, **PK-only tablo 0** → politika "eksikleri kapat" değil, "workload ile doğrula" olarak kuruluyor (düşük maliyetli giriş).
- **Denormalizasyon görünür hâle geliyor**: I1-I5 kayıtları ve 5 zorunlu alan sayısız gizli kopya yerine denetlenebilir tek defter bırakıyor.
- **Cache/rapor/audit ile çakışmıyor**: gerekçe ADR-007'ye, çoğaltma tek-yazıcı ilkesine, alanlar ADR-022'ye bağlandı → üç ADR ile de uyumlu.

**Olumsuz:**

- **Denetim ve geçiş işi yaratıyor**: 156 tablo BCNF denetimi + 96 ENUM geçiş planı + 28 cross-DB FK düzeltmesi gerçek iş kalemleri; hepsi PLANNED ve sahiplik gerektiriyor.
- **İstisna defteri elle yaşar**: 5 alan zorunluluğu uygulanmazsa defter kendisi bir sapma kaynağı olur (mitigasyon: §5.2/5 sahipliği + review kapısı).
- **ENUM→lookup geçişinde geçiş dönemi join bedeli**: okuma yolu geçici olarak pahalılaşabilir (§1.3 kaynak 30-31).

**Nötr:**

- Index yoğunluğu (3,5/tablo) bugün makul; workload ölçümü yapılmadan **ne şişirilir ne azaltılır** — sayı politika değil kanıt olacak.
- Cross-DB FK düzeltmesinin yönü (constraint'i kaldırmak mı, application-level ref mı) bu ADR'de sabitlenmedi: ADR-003'ün "DB arası FK yok" hükmü bağlayıcı, mekanizma iş kaleminde seçilecek.

### 4.1 Risk → Fallback matrisi

| # | Risk | Olasılık | Etki | Mitigasyon | Fallback (geri çekilme yolu) |
|---|------|---------|------|------------|------------------------------|
| R1 | **Denorm drift** — I1-I3 kopya/sayaç kaynağından sapar, okuma yanlış veri gösterir | Olası (3) | Yüksek (4) | İstisna zorunlu 5 alan + tek yazıcı + periyodik reconciliation; fark log'a (ADR-022) | Sapmada okuma yolu **kaynak tablodan yeniden hesaba** alınır (kopya kolon yazımı durdurulur), defter I1-I3 geçici askıya alınır |
| R2 | **Index şişmesi** — workload'sız eklenen index yazmayı yavaşlatır | Mümkün (2) | Orta (3) | §2.4-b/3: yeni index yalnız `EXPLAIN` kanıtıyla (ADR-002 kapısı); "her kolona index" yasak | `DROP INDEX` ile kaldırılır (online DDL, ADR-014 kilit doğrulaması) — index silmek veri kaybettirmez |
| R3 | **Çoğaltma tutarsızlığı** — `play_count` 4 DB'de birbirine girer | Olası (3) | Orta (3) | I3 otorite kuralı: `coremusic_musics` tek gerçeklik, diğerleri projection + reconciliation | Projection'lar düşürülür, rapor sorgusu kaynağı okur (geçici yavaşlama, veri doğruluğu kazanır) |
| R4 | **ENUM geçişinin maliyeti** — 96 kolon/18 şema dönüşümü üretimde kilit üretir | Olası (3) | Yüksek (4) | Geçiş PLANNED + expand-contract + tek seferde tek domain (ADR-014) | Geçiş durdurulur, ENUM'da kalınır; kural "yeni alanlarda lookup" olarak uygulanmaya devam eder (eski alan ihlal sayılmaz) |
| R5 | **Cross-DB FK düzeltmesi veri bütünlüğünü düşürür** — 28 constraint kaldırılınca yetim satır kalır | Mümkün (2) | Yüksek (4) | Önce uygulama seviyesinde doğrulama + rapor, sonra constraint kaldırma (expand-contract) | Uygulama doğrulaması açık kalır; constraint geri eklenemezse yetim satır job'u + audit logu devreye girer |
| R6 | **Denetim hiç yapılmaz** → ADR "kağıtta kalır" | Olası (3) | Orta (3) | §5.2 adımları sahipli + süreli; debate/Tech Lead onayı kapı olarak | Kural seti yürürlükte kalır ama "BCNF doğrulanmıştır" iddiası **kullanılmaz**; şablonlardaki self-declaration `⚠️ VERIFICATION REQUIRED` etiketiyle işaretlenir |

---

## 5. İlgili Kararlar

### 5.1 Wiki-linkler (diskte doğrulandı — hepsi VAR ✅)

- `[[.ai/.decisions/accepted/ADR-003-multi-db-bcnf.md]]` → 18 DB + BCNF zemini + **tek-yazıcı ilkesi** + "DB arası FK yok" + bu ADR'ye devredilen denetim işi (§1.1-A/E, §2.4-b/2, §4.1-R3).
- `[[.ai/.decisions/accepted/ADR-007-cache-namespace.md]]` → read-path hızının sahibi; denormalizasyon gerekçesi cache'siz yazılmaz (§2 a2, §2.4-e/3, §3 #2).
- `[[.ai/.decisions/accepted/ADR-014-multi-db-migration-strategy.md]]` → **forward-only + fix-forward**: ENUM/lookup ve cross-DB FK geçişlerinin tek geri dönüş usulü (§1.4, §2.4-b/4, §4.1-R4).
- `[[.ai/.decisions/accepted/ADR-022-database-hardened-security.md]]` → 6 audit tablosu + `created_at`/audit alan standardı (§1.1-A, §2.4-b/5).
- `[[.ai/.decisions/accepted/ADR-002-pdo-mandatory-no-orm.md]]` → PDO + prepared; `EXPLAIN` kapı partneri (§1.4, §2.4-e/4).
- `[[.ai/.decisions/accepted/ADR-006-performance-targets.md]]` → hız hedefleri; sert normalizasyonun reddi gerekçesi (§3 #2).
- `[[.ai/.decisions/accepted/ADR-081-multi-provider-data-sync.md]]` → reconciliation/outbox ruhu; drift kontrolünün usulü (§2.4-e/2).
- `[[.ai/.decisions/accepted/ADR-032-ipc-contract-versioning.md]]` → `schema_versions` şema-sürümü bulgusunun kaynak ADR'si (§1.1-D).
- `[[.ai/.decisions/index.md]]` → karar dizini; `ADR-033-sql-normalization-strategy` satırı **diskte mevcut** (`:70`) — kayıt satırı ayrı işlemdir.
- `[[.ai/.templates/adr/adr-template.md]]` → 7 bölüm + §1.3 9 alan iskeleti (Guardrail #16).
- `[[.claude/skills/prompt-maker/references/10-web-research-protocol.md]]` → §1.3 web araştırma protokolü (diskte VAR ✓).
- `[[.ai/.sql/mysql/coremusic_musics.sql]]` → I1 kopya kolonlar (`:732-745`), I2 sayaçlar (`:106-108`), BCNF self-declaration (`:756`).
- `[[.ai/.sql/mysql/coremusic_user.sql]]` → **11 cross-DB FK** (`:50,83,114-115,138,160-161,188-189,223-224`) — R5 ihlal kaynağı.
- `[[.ai/.sql/mysql/coremusic_social.sql]]` → **13 cross-DB FK** (`:47,67,91,118,119,150,151,180,204,205,235,266,267`), ENUM örneği (`:26`), sayaç (`:30-31`).
- `[[.ai/.sql/mysql/coremusic_system.sql]]` → **4 cross-DB FK** (`:69,100,128,225`), ENUM yoğunluğu (`:27`, 20 adet), JSON array (`:54,:141`).
- `[[.ai/.sql/mysql/coremusic_logs.sql]]` → ENUM (`:52`), özet/DB-çarpım sayaçları (`:205,:229`).
- `[[.ai/.sql/mysql/coremusic_playlist.sql]]` → `total_tracks`/`play_count` (`:31-35`).
- `[[.ai/.sql/mysql/coremusic_albums.sql]]` → cross-DB FK yorum satırları (`:53,:54,:128,:154`), `play_count` (`:37`).
- `[[.ai/.sql/mysql/coremusic_auth.sql]]` → BCNF self-declaration (`:3,:20`), ENUM (`:30`), `permissions JSON` (`:62`).
- `[[.ai/glossary.md]]` → ADR-014 frozen→accepted düzeltmesi (`:440`) — geçerli (§1.1-E).
- `[[.ai/brain.md]]` → envanter §11 (18 şema/156 tablo) + ADR-033 özeti satırı (`:988`).
- `[[.ai/index.md]]` → master katalog `:650` ADR-033 kaydı + `:620` "9 BCNF veritabanı" eski sayı notu (§1.1-E).
- `[[.ai/keys.md]]` → `:89`/`:268` ADR-033 keyword kaydı.
- `[[.ai/log.md]]` → audit trail (append-only; bu ADR'nin 1 satırlık kaydı).

### 5.2 Karar parçaları (appendix)

| # | Parçanın adı | Sahibi | Son |
|---|--------------|--------|-----|
| 1 | 156 tablo BCNF/dependency denetimi (dosya:tablo bazında rapor + ihlal listesi) | Data Engineer | §2.4-b/1 — iddia → kanıt |
| 2 | Index workload denetimi (`EXPLAIN` + slow-query okuma; PK hariç index kuralı kapısı) | Data Engineer + Backend Architect | §2.4-b/3 — ADR-002 kapısıyla birlikte |
| 3 | Cross-DB FK düzeltmesi (28 satır / 3 dosya: user 11 · social 13 · system 4) | Data Engineer | §2.4-b/2, §4.1-R5 |
| 4 | ENUM → lookup geçiş planı (yeni alanlar: lookup; mevcut: PLANNED domain bazlı) | Data Engineer | §2.4-b/4, §4.1-R4 |
| 5 | İstisna defteri I1-I5 + yeni istisna girişi (5 alan) | Data Engineer (kayıt) + Vault Steward (review) | §2.4-c/d |
| 6 | Sayaç reconciliation job'u + tek-yazıcı doğrulaması | Backend Architect + Data Engineer | §2.4-e/1-2, §4.1-R1/R3 |
| 7 | Debate turu + Tech Lead onayı (§6) | Vault Steward + Tech Lead | §6, §7 |
| 8 | **Şart 1a** — Cross-DB FK istisna listesi + belgelenmiş denorm etiketi (28 constraint / 3 dosya) | Data Engineer | §6.2/1a, §5.2/3 |
| 9 | **Şart 1b** — Sayaç envanteri (79 sayaç: kalıcı vs düzeltilecek) | Data Engineer + Backend Architect | §6.2/1b, §5.2/6 |
| 10 | **Şart 2** — Otomatik normal form denetim scripti (CI) | Data Engineer + QA Engineer | §6.2/2, §5.2/1 |
| 11 | **Şart 3** — ENUM→lookup geçiş faz planı (96 ENUM / 11 dosya, PLANNED) | Data Engineer | §6.2/3, §5.2/4 |

### 5.3 Çapraz referans matrisi (kaynak → bölüm → durum)

| Kaynak | Kullanıldığı bölüm | İlişki | Disk kanıtı |
|--------|--------------------|--------|-------------|
| `[[.ai/.decisions/accepted/ADR-003-multi-db-bcnf.md]]` | §1.1-A/E, §2.1, §2.4-b, §4.1-R3 | 18 DB + BCNF + denetimin bize devri | ✅ VAR |
| `[[.ai/.decisions/accepted/ADR-007-cache-namespace.md]]` | §2 a2, §2.4-e/3, §3 #2 | Denormalizasyon gerekçesinin sahibi | ✅ VAR |
| `[[.ai/.decisions/accepted/ADR-014-multi-db-migration-strategy.md]]` | §1.4, §2.4-b/4, §4.1-R4 | Forward-only → geri dönüş = fix-forward | ✅ VAR |
| `[[.ai/.decisions/accepted/ADR-022-database-hardened-security.md]]` | §1.1-A, §2.4-b/5 | Audit alanları + 6 audit tablosu | ✅ VAR |
| `[[.ai/.decisions/accepted/ADR-002-pdo-mandatory-no-orm.md]]` | §1.4, §2.4-e/4 | `EXPLAIN`/migration kapı partneri (satır 18'deki "ADR-033 diskte YOK" notu bu yazımla bayatlar — frozen, dokunulmaz) | ✅ VAR |
| `.ai/.sql/mysql/*.sql` (8 dosya, §5.1) | §1.1-B/C, §2.4-d | Şema kanıtı — dosya:satır | ✅ VAR (18 dosya) |
| `[[.ai/.decisions/index.md]]`, `[[.ai/index.md]]`, `[[.ai/keys.md]]`, `[[.ai/brain.md]]` | §1.1-E, §5.1 | ADR-033 kayıtları zaten mevcut | ✅ VAR |
| `[[.ai/.templates/adr/adr-template.md]]` + `[[.claude/skills/prompt-maker/references/10-web-research-protocol.md]]` | §6, §7, §1.3 | İskelet + araştırma protokolü | ✅ VAR |
| Web (32 kaynak, §1.3) | §1.3, §2.1-2.3, §3, §4 | Güncel ekosistem kanıtı | ✅ 5 sorgu / 32 kaynak |

---

## 6. Statü ve Debate

- **Status:** `accepted` — karar kapsamı (a) BCNF birincil · (b) kontrollü denormalizasyon + belgeleme · (c) güncelleme yazmalarında tutarlılık · (d) 18 DB 5 eksenli ortak kural seti · (e) sonuç/risk/fallback) **kullanıcı onaylı** olarak kabul edildi; uygulama kalemleri §5.2 parçalarına bağlıdır.
- **Debate:** `✅ TAMAMLANDI (3 tur / 20 persona — 18/2/0 KABUL)` — kayıt §6.1'de, bağlayıcı şartlar §6.2'de; Tech Lead §7'de `⏳ → ✅` geçişini yaptı (2026-09-25).
- **Frozen ADR'lar (001-037):** bu dosya frozen değildir ve frozen ADR metnine dokunmaz. **Numaralandırma notu (Truth Mode):** genel kural "yeni ADR'ler 088+" der; bu dosya **eski seri ayrılmış numarasına** (ADR-033) yazıldı, çünkü `.ai/.decisions/index.md:70`, `.ai/index.md:650`, `keys.md:89/268` ve `brain.md:988` bu numarayı **çoktan kayıtlı** tutuyor ve ADR-003/ADR-002 bu numarayı "sql normalization" olarak referanslıyor → numara boş değil, **boşluk dolduruldu**; ayrıca 001-037 aralığındaki tek eksik metin buydu (frozen = mevcut metin dokunulmaz; var olmayan metin yazılabilir).
- **§6.1 Debate kaydı:** `✅ TAMAMLANDI` — 3 tur / 20 persona · oy 18/2/0 KABUL · 3 şart / 4 kalem §6.2.

### 6.1 Debate kaydı

**✅ TAMAMLANDI — 3 tur / 20 persona · 2026-09-25 · sonuç 18 kabul / 2 çekimser / 0 red → KABUL**

**Tur 1 — Şema kanıtı + 20 persona taraması:**

- Sunulan kanıt: 18 dosya · 156 tablo · PK %100 (156/156) · FK 82 · index 550 · PK-only tablo 0.
- İhlaller: 28 cross-DB FK (user 11 / social 13 / system 4 — ADR-003 §2.2'ye aykırı) · 79 türetilmiş sayaç · 96 ENUM / 11 dosya · 1FN JSON 45 kolon · `musics:732-745` kopya kolonlar.
- 32 kaynak (§1.3); 14 sayısal hata bu turda düzeltildi.
- Oy: 15 kabul / 5 nötr — **4 uyarı**: QA Engineer (otomatik NF denetimi) · DevOps Engineer (cross-DB FK şart) · Critic (sayaç envanteri + ENUM faz şartı).

**Tur 2 — İtiraz → çözüm:**

| # | İtiraz | Çözüm | Şart |
|---|--------|-------|------|
| 1 | 28 cross-DB FK ↔ ADR-003 §2.2 çelişkisi | İstisna listesi + belgelenmiş denormalizasyon etiketi (5 alan, §2.4-c) | §6.2/1a |
| 2 | 79 türetilmiş sayaç belirsiz (kalıcı mı, düzeltilecek mi) | Sayaç envanteri: kalıcı vs düzeltilecek ayrımı | §6.2/1b |
| 3 | NF ihlali denetimi manuel | Otomatik normal form denetim scripti (CI kapısı) | §6.2/2 |
| 4 | 96 ENUM geçişsiz kaldı | ENUM→lookup geçiş faz planı (PLANNED, ADR-014 fix-forward) | §6.2/3 |

**Tur 3 — Final oyu:** **18 kabul / 2 çekimser / 0 red → KABUL** (Tech Lead §7 `⏳ → ✅`).

### 6.2 Bağlayıcı şartlar

**✅ 3 şart / 4 kalem — debate KABUL'ünün koşulu; yerine getirilmeden §5.2 uygulama kalemleri kapanmaz.**

| # | Şart | İçerik (kabul ölçütü) | Sahip | Çıktı / referans |
|---|------|------------------------|-------|------------------|
| 1a | Cross-DB FK istisna listesi | 28 constraint (user 11 · social 13 · system 4) tek tek istisna listesine alınır; her biri belgelenmiş denormalizasyon etiketiyle (5 zorunlu alan, §2.4-c) işaretlenir; ADR-003 §2.2 çelişkisi kapanana kadar yeni cross-DB FK eklenmez | Data Engineer | §2.4-b/2 · §5.2/3 + §5.2/8 · §4.1-R5 |
| 1b | Sayaç envanteri | 79 türetilmiş sayaç tek tek sınıflandırılır: **kalıcı** (I2 istisnası, senkron mekanizması zorunlu) vs **düzeltilecek** (kaynağa/projection'a taşınacak); sahip + senkron yazılır | Data Engineer + Backend Architect | §2.4-d/I2 · §5.2/6 + §5.2/9 |
| 2 | Otomatik NF denetimi | 156 tablo için normal form / bağımlılık denetim scripti CI'a bağlanır; ihlal CI raporunda görünür — manuel denetim kabul edilmez (QA Engineer uyarısı) | Data Engineer + QA Engineer | §2.4-b/1 · §5.2/1 + §5.2/10 |
| 3 | ENUM→lookup geçiş faz planı | 96 ENUM / 11 dosya için domain bazlı faz planı yazılır (PLANNED; expand-contract, tek seferde tek domain, ADR-014 fix-forward) — Critic'in ENUM faz şartı | Data Engineer | §2.4-b/4 · §5.2/4 + §5.2/11 · §4.1-R4 |

Şart 1, debate sonunda 1a + 1b olarak iki kaleme bölündü (Tech Lead koşulu: "cross-DB FK istisna + sayaç envanteri"); şart 2 QA Engineer'ın, şart 3 Critic'in Tur 1 uyarısından doğdu. Şartlar yerine getirilmezse §4.1-R6 fallback'i uygulanır: kural seti yürürlükte kalır ama "BCNF doğrulanmıştır" iddiası kullanılmaz.

**Statü özeti:** debate `✅ 3/20 (18/2/0 KABUL)` → Tech Lead `✅` → Arch Lead `⏳` → **frozen YOK** (§7 üç satırı tamamlanmadan `frozen` yapılmaz).

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-09-25 | ✅ (kullanıcı onaylı karar kapsamı — (a) BCNF birincil · (b) kontrollü denormalizasyon + 5 alan belgeleme · (c) güncelleme yazmalarında tutarlılık · (d) 18 DB 5 eksenli ortak kural seti · (e) sonuç + risk + fallback) |
| Tech Lead | — | 2026-09-25 | ✅ (debate 3 tur / 20 persona — 18/2/0 KABUL; 3 bağlayıcı şart §6.2) |
| Arch Lead | — | — | ⏳ (Tech Lead sonrası) |

**Debate:** ✅ 3 tur / 20 persona — 18 kabul / 2 çekimser / 0 red → **KABUL**; 3 bağlayıcı şart §6.2. Arch Lead onayı bekleniyor → frozen YOK.

---

**1.0.0 | 2026-09-25 | Created**
*ADR-033 debate | 2026-09-25 | ✅ 3/20 KABUL (18/2/0) → Tech Lead ✅ → Arch Lead ⏳ → frozen YOK*

*Authority: ADR-033 Karar Metni (SSOT)*
*Mode: Red Team · Human Mode · Truth Mode*
