---
title: "CoreMusic — R-002: MongoDB Document Store (REDDEDİLDİ — BCNF uyumsuz)"
type: "architecture-decision"
category: "database"
date: "2026-10-01"
updated: "2026-10-01"
version: "1.0.0"
status: "rejected"
authority: "SSOT — R-002 red kararı: CoreMusic veri yüzeyine MongoDB (document store) GIRMEZ. Gerekçe: ADR-003 BCNF zorunluluğu (frozen, immutable) + ADR-002 PDO/ORM yasağı + ADR-040 MySQL tek yetki alanı ('BCNF uyumsuz' — index.md:127). Yerini alan: ADR-003-multi-db-bcnf (çoklu-DB BCNF mimarisi) + ADR-040-database-authority (18/19 MySQL sahiplik matrisi). Bu dosya salt-okunur seridir (rejected/) — değiştirilmez, yalnız referanslanır."
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 RED DOĞRULANDI)"
---

# CoreMusic — R-002: MongoDB Document Store (Rejected)

> **Durum:** rejected (**debate ✅ TAMAMLANDI**) — **Tarih:** 2026-10-01 — **Debate:** ✅ **TAMAMLANDI** (3 tur / 20 persona · 19 kabul / 1 çekimser / 0 red → **RED DOĞRULANDI**; 3 şart §5.3) — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Seri:** `.ai/.decisions/rejected/` (salt-okunur) — **Slug:** `R-002-mongodb-document-store` (dizin otoritesi: [[../index]] **satır 127** — dosya adı ile birebir hizalı ✅, 2026-10-01 glob doğrulaması: `rejected/` içinde `R-002*` = **0 dosyaydı** → bu işlemde yazıldı)
> **Dizin satırı:** `| [[R-002-mongodb-document-store]] <!-- dead-link: R-002-mongodb-document-store no source 2026-09-24 --> | MongoDB | BCNF uyumsuz |` — `<!-- dead-link ... -->` bayrağı **bu işlemde DOKUNULMADI** (düzeltme son sıfırlamaya ertelendi → §5.1/4 + §7.1/1)
> **İlgili kararlar:** [[../accepted/ADR-003-multi-db-bcnf]] (yerini alan — BCNF çoklu-DB mimarisi; `:164` "red kararı R-002 (MongoDB — 'BCNF uyumsuz')") · [[../accepted/ADR-040-database-authority]] (yerini alan — MySQL yetki/matris otoritesi) · [[../accepted/ADR-002-pdo-mandatory-no-orm]] (erişim katmanı — PDO tekel, sürücü yasağı) — karar dizini [[../index]] §5.
> **R-001 dersi uygulandı:** wiki-link slug'ları **tahmin edilmedi** — her hedef disk glob'u ile doğrulandı (ADR-003/040/002/033/014/081/039 + brain.md + R-001 = diskte VAR).

---

## 1. Bağlam (Context)

CoreMusic veri katmanı **ilişkisel ve BCNF'dir**: ADR-003 (frozen) her domain'in kendi BCNF şemasında tek başına MySQL olmasını; ADR-002 PDO zorunlu / ORM yasak olmasını; ADR-040 ise hangi verinin hangi MySQL'de olduğunu ve tek-yazar kuralını sabitler. Karar dizini bu seçeneği çoktan reddetmiştir (`index.md:127` — "MongoDB | BCNF uyumsuz") ama **red metni hiç yazılmamıştır**: elde yalnız dizin satırı + `<!-- dead-link ... -->` notu + ADR-003 içinde iki parmak izi (`:164`, `:251`) vardır. Bu dosya, o satırın **gerekçeli red kaydıdır** — yeni bir karar değil, mevcut red'in (a) gerekçe, (b) güncel web araştırması, (c) yerini alan eşleme, (d) yeniden değerlendirme koşulu ile sıfırdan yazımıdır.

### 1.1 Mevcut Durum (disk kanıtı — dürüst etiket, 2026-10-01 taraması)

| # | İddia | Kanıt | Etiket |
|---|-------|-------|--------|
| 1 | Red kaydı var mı? | [[../index]] `:127` → `[[R-002-mongodb-document-store]]` + "BCNF uyumsuz" + `<!-- dead-link ... no source 2026-09-24 -->` | ✅ **KAYITLI** (dizin satırı tek kanıt; red metni bu işlemde yazılıyor) |
| 2 | Bu işlem öncesi dosya var mıydı? | `.ai/.decisions/rejected/` içinde yalnız `CLAUDE.md`, `index.md`, `R-001-*` ; `R-002*` = **0 dosya** | ❌ **YOKTU** → bu işlemde yazılıyor (klasör zaten var — `rejected/` oluşturulmadı) |
| 3 | Red gerekçesi başka yerde yazılı mı? | ADR-003 `:164` (§3 alternatif #2 — polyglot ret satırı, "red kararı R-002 (MongoDB — 'BCNF uyumsuz')") · ADR-003 `:251` (§6 — "index.md §5 → R-002 (MongoDB/BCNF)") | ✅ **2 referans** — ama hiçbiri red'in kendi metni değil, **gerekçe ailesi** |
| 4 | Yerini alan BCNF mimarisi diskte? | [[../accepted/ADR-003-multi-db-bcnf]] **VAR** (accepted/ glob; frozen kapsamı 001-037 → immutable) | ✅ **IMPLEMENTED** |
| 5 | Yerini alan DB otoritesi diskte? | [[../accepted/ADR-040-database-authority]] **VAR** — title: "Database Authority (18 BCNF sahiplik matrisi · tek yazar · cross-DB politikası · migration yetkisi)" · debate ✅ (18/2/0) | ✅ **IMPLEMENTED** |
| 6 | Erişim katmanı diskte? | [[../accepted/ADR-002-pdo-mandatory-no-orm]] **VAR** — PDO zorunlu / ORM yasak (ADR-003 `:32`, `:86`) | ✅ **IMPLEMENTED** |
| 7 | Kod yüzeyinde MongoDB izi? | `*.php/*.js/*.mjs/*.cjs/*.json` + 5 adet `composer.json` (shared, api, auth, media, home) taraması: `mongo\|mongodb\|mongoose\|pymongo\|doctrine/mongodb` → **0 isabet** | ✅ **0** (MongoDB hiç girmedi — red bir "kaldırma" değil, **girişi engelleme** kararıdır) |
| 8 | SQL şeması diskte? | `.ai/.sql/mysql/*.sql` glob → **19 dosya** (2026-10-01) ; ADR-040 `:33` "18 (coremusic_ai … coremusic_wireless)" der → **1 dosya fark** | ⚠️ **FARK** — yorum uydurulmadı → §7.1/5 rapor-only |
| 9 | MySQL JSON yüzeyi var mı? | `media_catalog.sql:456-457` → `JSON_EXTRACT(al.tag, ...)` (şema düzeyinde **var**) · `shared/**/*.php` JSON fonksiyon taraması → **0 isabet** | ⚠️ **ŞEMADA VAR / KODDA 0** (PHP tarafı UNKNOWN — ölçülmedi) |
| 10 | `rejected/index.md` durumu? | Dosya **VAR** (v1.0.1, `total: 12`) ama § tablosu (`:18-19` başlık satırları) **BOŞ** — 12 red'in hiçbiri satırlanmamış | ⚠️ **BOŞ** → bu işlemde **dokunulmadı** → §7.1/2 |
| 11 | Debate sonucu? | 3 tur / 20 persona · 19 kabul / 1 çekimser / 0 red (2026-10-01 debate kaydı, §5.3) | ✅ **RED DOĞRULANDI** — 3 şart §5.3 |

> **Ders notu:** bu red **hiçbir zaman kodda denenmedi** — MongoDB kod yüzeyinde 0 (§1.1/7); "reddedildi" = "yazılmadı ve yazılmasına izin verilmedi". Gerekçe dizin satırında tek satır, uzun gerekçe ise yalnız ADR-003'ün alternatif tablosunda duruyordu. Bu dosya o boşluğu kapatır.

### 1.2 Sorun Tanımı

1. **Red kararı kanıtsız duruyor.** `index.md:127` bir sonuç cümlesi ("BCNF uyumsuz") ama **ne 2025-26 ekosistem kanıtı ne yeniden değerlendirme koşulu ne yerini alan eşleme** yazılı — gelecekteki biri "MongoDB neden yok, bugün de mi yok, ne zaman tekrar sorulur?" sorusuna vault'tan cevap bulamıyor.
2. **Gerekçe ailesi parçalı.** Uzun gerekçe ADR-003 `:164` (polyglot alternatifi ret satırı) ve `:251` (dizin özeti) içinde dağınık; red'in kendi dosyası olmadığı için `grep R-002` sonucu "gerekçe = başka ADR'nin alternatif satırı" düzeyinde kalıyor.
3. **Güncellik sorunu.** MongoDB 2025-26'da durmadı: multi-document ACID transactions (4.0/2018 → 4.2 sharded), schema validation, vektör/arama yetenekleri ve FY2026'da $2.46B abonelik geliri var (§1.3). Red'in **bugün hâlâ doğru** olup olmadığı araştırılmadan yazılmıştı — "eski bilgiye dayanan karar" sınıfı (şablon §1.1 uyarısı).
4. **Koşul tanımsız.** "Hangi durumda MongoDB tekrar gündeme gelir?" (BCNF mi değişti, ölçek mi yetmiyor, vektör işi mi zorunlu oldu) **hiç belgelenmedi** → yeniden değerlendirme tetikleyicisi tanımsız.

### 1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "MongoDB 2025 2026 relational database vs document store adoption trends" → (2) "MongoDB multi-document ACID transactions 4.0 schema validation 2025" → (3) "MySQL JSON functions vs MongoDB document store when to choose relational 2025" → (4) "schema drift denormalization document database drawbacks operational overhead joins $lookup performance" |
| Web Search **Konusu** | **(1)** MongoDB'nin 2025-26 konumu: popülerlik/benimseme eğilimi, document store vs relational sıralaması · **(2)** multi-document ACID transactions (4.0+), sınırlar ve schema validation'ın gerçek kapsamı · **(3)** MySQL'in JSON kapasitesi (native JSON tipi, JSON_TABLE, JSON_SCHEMA_VALID) vs document store — "doküman işi için Mongo şart mı?" · **(4)** document store'un bedelleri: schema drift, denormalization/kopya senkronu, `$lookup` join maliyeti. |
| Web Search **Bağlam** | CoreMusic: ADR-003/002/040 (BCNF + PDO + MySQL tek yetki) frozen; kodda MongoDB **0** (§1.1/7); şemada `JSON_EXTRACT` izi var (§1.1/9); hedef soru — *"Red bugün hâlâ doğru mu, yoksa document store 2026'da bu mimariyi gerçekten geçti mi, ters kanıt var mı?"* Araştırma 2026-10-01'de yapıldı; **4 sorgu / 40 isabet** (sorgu başına 10). Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (öncelik: resmi doküman → vendor → bağımsız blog; her iddiaya kaynak). |
| Web Search **Kısa Açıklama** | **(1)** DB-Engines Eyl 2026: MongoDB **#5** (381.31; aylık -3.57, yıllık +0.81) — ilk 4'ün tamamı ilişkisel (Oracle, MySQL #2 844.82, SQL Server, PostgreSQL); document store kategorisinde **#1**. **(2)** Multi-doc ACID **2018'den beri var** (4.0 replica set, 4.2 sharded) ama resmi doküman kendi sınırlarını yazar: 60 sn varsayılan timeout, işlem başına ~1.000 yazım önerisi, işlem içinde koleksiyon/index oluşturma YOK, dağıtık işlem "efektif şema tasarımının yerine geçmez". **(3)** MySQL 8.4 native JSON tipi + `JSON_TABLE` + `JSON_SCHEMA_VALID()` taşır → esnek/yarı-yapısal veri MySQL'de de mümkün. **(4)** Schema drift document store'un belgelenmiş hastalığı; `$lookup` MongoDB'nin kendi anti-pattern dokümanında "yavaş ve kaynak tüketici" diye anılır. |
| Web Search **Uzun Açıklama** | **(1) Ekosistem:** DB-Engines (Eyl/Ağu 2026) MongoDB'yi #5'te tutuyor, yıllık puan değişimi +0.81 (yatay); ilk sırada ilişkisel motorlar ve MySQL yıllık -46.95 ile düşüyor — yani **iki taraf da baskın kazanan değil**, popülerlik tek başına red gerekçesi değil. MongoDB FY2026 (2026-03-02 yatırımcı bülteni): abonelik geliri $2.386M (FY2025 $1.944M), Atlas geliri artıyor; şirket blogu (Q2 2026 earnings atıfı) Fortune 500'ün %70'inden fazlasını müşteri diyor (**vendor iddiası, bağımsız doğrulama YOK**). **(2) Transaction/şema:** MongoDB resmi manual — tek doküman işlemi atomik, multi-doc transaction'lar koleksiyon/veritabanı/shard arası destekli; ama kendi best-practice blogu (2020) 60 sn abort, 1.000 yazım tavanı, WiredTiger cache baskısı uyarısı yapar; Percona/TechTarget/DZone 4.0 kapsamını doğrular (4.0 = replica set, 4.2 = sharded; işlem içinde koleksiyon/index yaratılamaz, 16MB oplog/doküman sınırı). Schema validation `$jsonSchema` ile **opsiyoneldir** ve ağırlıklı olarak **yeni yazım**ları kapsar (golang-quickstart örneği + developer-tech analizi). **(3) MySQL JSON:** dev.mysql.com 8.4 ref — JSON tipi otomatik doğrulama + binary format, 30+ JSON fonksiyonu, `JSON_TABLE` (JSON→satır), `JSON_SCHEMA_VALID`/`JSON_SCHEMA_VALIDATION_REPORT`; yani "doküman benzeri alan" ihtiyacı MySQL'de ek motora gerek kalmadan kısmen karşılanır. **(4) Bedeller:** developer-tech (2026-03-19) schema drift'i gerçek pipeline kırılmalarıyla anlatır (adres alanı string → object dönüşümü `$split`'i patlatır) ve validation'ın geç/eksik uygulanmasını vurgular; MongoDB kendi docs'unda "Reduce $lookup Operations" anti-pattern sayfası açar (tek koleksiyon sorgusu yerine join = yavaş); dev.to/Franck Pachot (2026-07) benchmark'ında varsayılan strateji **nested loop join** (5M satır ~64 sn), hash join ancak koşullu (allowDiskUse + SBE + küçük foreign) çalışır; handbook-academy + Teradata + ScienceDirect denormalization'ın bedelini yineler: yazım pahalı, kopya senkronu + backfill + "silent drift". |
| Web Search **Paragraf Veri Uzun** | 4 sorgu / 40 isabet: **(1)** db-engines.com ranking (Eyl 2026: MongoDB #5 381.31 / MySQL #2 844.82 / sıralama tablosu) · db-engines document-store ranking (Ağu 2026: MongoDB doküman deposu #1) · investors.mongodb.com FY2026 Q4 bülteni (2026-03-02, abonelik $2.386M) · mongodb.com "From Niche NoSQL To Enterprise Powerhouse" (Fortune 500 %70+, 4.0 ACID "game-changer", 8.2 sürümü, Voyage AI entegrasyonu) · mongodb.com "Busting The Top Myths" (şema/ACID/JOIN mitleri) · mongodb.com "Relational vs Non-Relational" (tek okuma = tek nesne) · mongodb.com "Document Database basics" · oneuptime "MySQL vs MongoDB" (2026-03-31 karşılaştırma tablosu) · Gartner Peer Insights EDB/IBM vs MongoDB — **10**. **(2)** mongodb.com/docs/manual/core/transactions · mongodb.com/docs/manual/core/transactions-operations · mongodb.com/products/capabilities/transactions · mongodb.com blog "Transactions And Read/Write Concerns" (2020: 60 sn, 1.000 yazım) · percona.com/blog mongodb-4.0 ACID (sınırlar listesi) · techtarget "MongoDB 4.0 takes ACID transactions..." (2018) · dzone "Multi-Document Transactions on MongoDB 4.0" (snapshot isolation, 16MB) · github mongodb/specifications transactions.md (Accepted) · mongodb-developer golang-quickstart ($jsonSchema + transaction) · mongodb.com press release 4.0 (2018-02-15) — **10**. **(3)** dev.mysql.com/doc/en/json-functions.html (8.4) · dev.mysql.com json-modification-functions · docs.oracle.com mysql-8.4 json-function-reference (JSON_SCHEMA_VALID, JSON_TABLE) · docs.oracle.com mysql-8.4 "The JSON Data Type" · dev.mysql.com json-attribute-functions (8.0/8.4/9.7) · mongodb.com "MongoDB vs MySQL" karşılaştırması · mongodb.com "Why not just use JSON in a relational" · oneuptime MySQL vs MongoDB · Gartner Peer Insights — **10**. **(4)** developer-tech.com "Schema drift is breaking your document database pipelines" (2026-03-19) · dev.to franckpachot "$lookup join strategies" (2026-07-09, nested loop 5M ~64 sn) · mongodb.com/docs "Reduce $lookup Operations" · mongodb.com/docs schema-advisor · github handbook-academy normalization-vs-denormalization · sciencedirect "Denormalization" · teradata.com "Denormalization Issues" · systemoverflow "Backfills, Hot Keys, and Drift" · arxiv.org/pdf/2501.07449 (normalizasyon/perf ölçümü) · ucl.ac.uk ICSE2008 şema değişimi etki analizi — **10**. |
| Web Search **Sonucu** | **(1) Kısmen karşıt bulgu (dürüst):** MongoDB kötü/çürümüş DEĞİL — #5, doküman kategorisinde #1, geliri büyüyor; ama ilk 4 ilişkisel ve CoreMusic'in zaten MySQL'de olduğu gerçeğini değiştirmez → red "MongoDB başarısız" değil, **"CoreMusic mimarisine uymuyor"** gerekçesiyle duruyor (kaynak: db-engines ×2, mongodb investor, mongodb blog — 4). **(2) Red destekleniyor:** ACID var ama sınırlı ve pahalı (60 sn, 1.000 yazım, işlem içi DDL yok, şema tasarımı yerine geçmez — resmi dokümanın kendi sözü) → "MongoDB artık ilişkisel gibi" argümanı CoreMusic'in transaction yoğun işleri için ek risk (kaynak: mongodb docs ×4, percona, techtarget, dzone — 7). **(3) Red destekleniyor:** MySQL 8.4 JSON tipi + JSON_TABLE + JSON_SCHEMA_VALID esnek/yarı-yapısal ihtiyacı motora gerek kalmadan karşılar; şemada `JSON_EXTRACT` izi zaten var (§1.1/9) → "doküman lazım" bahanesi MySQL içinde çözülür (kaynak: dev.mysql.com, oracle docs — 4). **(4) Red destekleniyor:** schema drift + opsiyonel/geride kalan validation + `$lookup` nested-loop maliyeti + denormalization kopya senkronu = BCNF'siz mimarinin ölçülmüş bedelleri (kaynak: developer-tech, dev.to, mongodb anti-pattern docs, handbook, teradata, sciencedirect — 6). **İtiraz/karşıt bulgu (dürüst):** (i) Fortune 500 %70+ ve gelir rakamları **vendor kaynaklı** → bağımsız doğrulama yok, ⚠️ işaretli; (ii) DB-Engines popülerlik bir **kaynak-kod bağışıklığı** endeksidir, teknik yeterlilik ölçümü değil → karar tek başına buna bağlanmadı; (iii) MySQL JSON yeteneği "MongoDB'nin tam yerine geçmez" (sharding/arama/vektör ekosistemi) → CoreMusic için bu yeteneklerin **ihtiyaç kanıtı 0** (kod yüzeyi §1.1/7-9). |
| Web Search **Alınan Karar** | **Red (R-002) YÜRÜRLÜKTE KALIR.** (a) MongoDB (veya eşdeğer document store: Firestore, DocumentDB, Cosmos-document) CoreMusic veri yüzeyine **GİRMEZ** — ADR-003 BCNF zorunluluğu + ADR-002 PDO/sürücü tekelı + ADR-040 MySQL yetki matrisi ile doğrudan çelişir; "BCNF uyumsuz" gerekçesi 2026 verisiyle de geçerli (BCNF = ilişkisel kavram, doküman modelinde denetlenemez). (b) Veri yüzeyi **MySQL BCNF + JSON tipi** ile sürer: esnek/yarı-yapısal alanlar (tag, i18n, AI metin vb.) gerektiğinde MySQL JSON kolonunda tutulur (şemada örnek: `media_catalog.sql:456`) — ek motor, ek sürücü, ek operasyon yok. (c) Polyglot persistence **domain split** olarak var (ADR-003 §2.2-c: 18/19 şema MySQL, teknoloji split'i DEĞİL). (d) **Yeniden değerlendirme koşulu** (§2.3) yazılmadan bu red **otomatik olarak güncellenmez**; vendor pazarlama iddiaları (Fortune 500 %70+) tek başına koşul sayılmaz. |
| Web Search **Sonuç** | **4/4 araştırmada red desteklendi** (1 ekosistem bağlamı + 2 transaction/sınır + 3 MySQL JSON kapasitesi + 4 bedeller). **Dört açık işaretlendi:** (i) MongoDB geliri/Fortune 500 rakamları **vendor kaynaklı** → bağlayıcı yapılmadı (yalnız bağlam) · (ii) DB-Engines popülerlik = benimsenme sinyali, teknik yeterlilik ölçütü değil → red kararı ona bağlanmadı · (iii) MySQL JSON ↔ PHP kullanımı **0** (§1.1/9) → "JSON yolundan gidiliyor" değil, "**izinli ve şemada denenmiş** bir yol" olarak yazıldı · (iv) 19 SQL dosyası ↔ ADR-040'ın "18" iddiası arasındaki **1 fark** çözümü yapılmadı (§7.1/5). **Kaynak sayısı: 4 sorgu / 40 isabet; §1.3'te adı geçen benzersiz kaynak ~28.** |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-003 frozen (immutable) | `ADR-001 → ADR-037` değiştirilemez; bu red onun veri ayağıdır — BCNF zorunluluğunu kaldırmak **yeni ADR** ister, bu dosya düzenlenmez (AGENTS.md §25.3 kural 2) |
| ADR-002 erişim tekelı | PDO zorunlu / ORM yasak; MongoDB PHP sürücüsü (`mongodb/mongodb` + extension) bu tekelin dışında bir **ikinci erişim yolu** demektir — red, sürücü tekelinin veri katmanındaki uygulamasıdır |
| ADR-040 tek yetki alanı | 18/19 MySQL sahiplik matrisi + tek-yazar kuralı + migration tek kapısı (ADR-014); document store bu matrisin **hiçbir satırına girmez** |
| Kod yüzeyi gerçeği | MongoDB = **0 bağımlılık / 0 isabet** (§1.1/7); red bir "kaldırma" değil, **girişi engelleme** kararıdır — geri dönüş planı kod tarafında işlem gerektirmez (§5.2) |
| Ölçüm eksikliği | "MySQL JSON yolu CoreMusic işlerini karşılar mı?" (kaç kolon, kaç sorgu) **UNKNOWN** — şemada 1 örnek var, kodda kullanım 0 → eşik uydurulmaz, §2.3 koşulu "ölçülerek belirlenecek" der |

---

## 2. Karar (Decision)

**R-002 REDDEDİLMİŞTİR: MongoDB ve eşdeğer document store'lar (Firestore, Cosmos DB document API, Amazon DocumentDB, Couchbase vb.) CoreMusic veri yüzeyine alınmaz.** Karar `index.md:127`'de bugünden vardı; bu dosya onu gerekçelendirir: red, ADR-003'ün BCNF zorunluluğu + ADR-002'nin sürücü/ORM tekelı + ADR-040'ın MySQL yetki alanının uygulamasıdır ve 2026-10-01 web araştırması (§1.3) red'in **bugün hâlâ doğru olduğunu** doğrulamıştır — MongoDB'nin zayıflığından değil, CoreMusic mimarisinin BCNF/MySQL sabitlerinden dolayı.

### 2.1 Neden Bu Seçenek?

1. **İlke tutarlılığı (kanıtlı):** ADR-003 §2.2-c "bölme ölçütü = domain, **teknolojiye göre değil**; tüm şemalar MySQL'dir" der; ADR-003 `:164` bu red'i zaten polyglot alternatifinin gerekçesi olarak sınıflandırmış — ayrı bir karar değil, **aynı ilkenin NoSQL ayağı**.
2. **BCNF = ilişkisel kavram (kanıtlı):** determinant-superkey/lossless-join denetimi doküman modelinde **denetlenemez** (ADR-003 `:164` "BCNF kavramı ilişkisel olmayan motorda anlamsızlaşır"); CoreMusic'in 156 tablo/82 FK disiplini (ADR-033/040) tek başına yeterli gerekçe.
3. **İhtiyaç kanıtı yok (kanıtlı):** kodda `mongo*` = **0** (5 composer.json dahil — §1.1/7); ölçek/performans şikayeti, shard ihtiyacı veya shema-esnekliği şikayeti **vault'ta belgeli değil** (UNKNOWN).
4. **Aynı iş MySQL'de çözülüyor (kanıtlı):** şemada `JSON_EXTRACT` izi var (`media_catalog.sql:456-457`) + MySQL 8.4 native JSON/JSON_TABLE/JSON_SCHEMA_VALID (§1.3-3) → "esnek alan" ihtiyacı ek motor gerektirmez.
5. **Ek motor = ek bedel (kanıtlı):** ADR-003 `:164` üç bedeli sayar — heterojen operasyon/bakım, sınırlı MySQL-dış yetkinlik, tutarlılık telafisi (outbox/Saga); §1.3-4 schema drift ve `$lookup` bedelini buna ekler.

### 2.2 Teknik Detaylar

- **Yasak yüzeyi:** `mongodb`, `mongodb/mongodb`, `mongodb-php-library`, `pymongo`, `mongoose`, `doctrine/mongodb-odm`, `com.mongodb.*`, `MongoClient`, Firestore/DocumentDB/Cosmos-document istemcileri — yani **document store erişim kütüphanesi** (ADR-002 sürücü tekelinin paraleli). İstemci kütüphanesi **kütüphane değil, ikinci veri yoludur** (ADR-001/002 ayrımının veri karşılığı).
- **İzinli yüzey (yerini alan uygulama):** (a) MySQL 9 + PDO + prepared statement (ADR-002); (b) BCNF şemalar, DB içi FK, DB arası FK yok (ADR-003 §2.2); (c) **MySQL JSON tipi** — esnek/yarı-yapısal alanlar için (`JSON_EXTRACT`, `JSON_TABLE`, `JSON_SCHEMA_VALID`); şema örneği: `media_catalog.sql:456`; (d) yetki/sahiplik/migration = ADR-040 + ADR-014; (e) DB arası tutarlılık = outbox (ADR-081).
- **Kod yüzeyi ölçümü (2026-10-01):** `mongo|mongodb|mongoose|pymongo|doctrine/mongodb` → **0 isabet** (`*.php/*.js/*.mjs/*.cjs/*.json` + 5 `composer.json`); PHP tarafı JSON fonksiyonu (`JSON_EXTRACT` vb.) da **0** → MySQL JSON yolu şemada başlamış, kodda başlamamış (PLANNED/UNKNOWN).
- **Join/ölçü notu:** ilişkisel join'ler (DB içi) korunur; document store'a geçiş olsaydı `$lookup` nested-loop maliyeti girerdi (dev.to 2026-07: 5M satır ~64 sn) — red bu maliyeti **hiç yaşamadı** — ölçülmüş bir kayıp değil, önlenmiş bir kayıptır.

### 2.3 Yeniden Değerlendirme Koşulu (şart satırı)

> **Bu red yalnız aşağıdaki koşullardan BİRİ yazılırsa yeniden değerlendirilir; aksi hâlde yürürlükte kalır:** (1) **BCNF zorunluluğu + MySQL tekeli yeni bir ADR ile değiştirilirse** (ADR-003/ADR-040 frozen metinleri düzenlenmez — yeni ADR `superseded by` ile bağlar); (2) **ölçülebilir bir iş ihtiyacı** Data Engineer raporuyla belgelenirse — MySQL'in (ADR-002/003/040 kapsamında) karşılayamadığı, ölçülmüş tek bir gereksinim (ör. tek şemanın taşıyamadığı ölçek/arama/vektör iş yükü) yazılmadan "MongoDB gerekli" iddiası kurulamaz; (3) **debate tamamlanıp red'i kuran koşullar değişirse** (§5.3 — debate ✅ TAMAMLANDI, RED DOĞRULANDI 19/1/0) yeni ADR ile yeniden açılır. **Bugün: 1 = SAĞLANMADI (ADR-003/040 diskte, frozen), 2 = ÖLÇÜLMEDİ (ihtiyaç kanıtı 0), 3 = SAĞLANMADI (debate ✅ 2026-10-01 — red doğrulandı; değişiklik yalnız yeni ADR ile) → red geçerli.**

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **MongoDB (document store) — tüm domain'ler için tek alternatif motor** | esnek şema, native sharding, tek doküman okuması hızlı (resmi iddia), olgun ACID (4.0+) | **BCNF denetlenemez** (ADR-003 `:164`); ikinci motor = operasyon/bakım + yetkinlik bedeli; ikinci sürücü = ADR-002 ihlali; `$lookup` join maliyeti + schema drift (§1.3-4); transaction sınırları (60 sn, 1.000 yazım, işlem içi DDL yok) | **BCNF uyumsuz** (dizin `:127` gerekçesi) + frozen ADR-003/002/040 ihlali; kodda ihtiyaç kanıtı **0** (§1.1/7) |
| 2 | **Polyglot persistence (DB başına farklı DBMS — NoSQL/search/graph dahil)** | workload'a en uygun motor; ADR-003 §3'te ayrıca değerlendirildi | 18/19 farklı motor operasyon/bakım yükü; heterojen tutarlılık bedeli (event/CQRS zorunlu); ekibin MySQL dışındaki yetkinliği sınırlı | ADR-003 `:164` **bu alternatifi de reddetmiştir** — karar domain split, teknoloji split'i için verilmedi; bu red o satırın document-store ayağıdır |
| 3 | **"MongoDB yerine MySQL'i document store gibi kullan" (X DevAPI / MySQL Shell document koleksiyonları)** | doküman API'si + aynı motor, ek sistem yok | MySQL PHP ekosisteminde X DevAPI yüzeyi PDO tekelinin (ADR-002) dışına düşer; CoreMusic'te **0 kullanım kanıtı** (UNKNOWN); belgelenmiş ihtiyaç yok | **Gereksiz yeni erişim yolu** — aynı iş (JSON kolonu + normal SELECT) PDO'dan yapılabilir; "ihtiyaç 0" iken yeni API yüzeyi açılmaz |

*(Kabul edilen uygulama alternatifi — MySQL BCNF + JSON tipi + PDO — §3'te "reddedilmedi" olarak ayrılmadı; o, §2.2'deki **yerini alan** yaklaşımdır ve ADR-002/003/040 kapsamındadır.)*

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Tek motor disiplini:** veri katmanında paket, sürücü, sürüm, tedarik-zinciri ve operasyon yüzeyi eklemek **0** (18/19 MySQL tek ADR-040 matrisinde kalır).
- **Red gerekçesi artık izlenebilir:** `grep R-002` → bu dosya; ADR-003 `:164/:251` ile gerekçe ailesi tek yerde toplandı.
- **Güncelleme yapıldı:** red 2026 web verisiyle (4 sorgu / 40 isabet) yeniden sınandı — "eski bilgiye dayanan karar" sınıfından çıktı; ters kanıt (MongoDB'nin büyümesi) dürüstçe yazıldı ama red'i değiştirmedi.
- **Kod tarafında sıfır iş:** geri hiçbir kod/dependency kaldırılmadı (zaten 0 — §1.1/7).
- **BCNF denetimi tek yerde:** 156 tablo/82 FK lossless-join disiplini bozulmadı (ADR-003/033/040).

### 4.2 Olumsuz Sonuçlar

- **Yatay ölçek esnekliği kullanılmaz:** native sharding/otomatik bölme MySQL'de Vitess benzeri işlerle sağlanır ( CoreMusic'te böyle bir ihtiyaç **belgeli değil** — UNKNOWN).
- **Esnek alan işi kodda yapılır:** şema-evrimi/alan ekleme MySQL'de `ALTER TABLE` + uygulama kodudur (pt-osc/gh-ost sınıfı araçlar) — doküman şemasındaki anlık esneklik yoktur.
- **Araman/vektör ekosistemi ayrı:** MongoDB'nin vektör/arama/Atlas entegrasyonları (FY2026 Voyage AI) kullanılmaz; CoreMusic için böyle bir iş yükü **kanıtsız** (§2.3/2 beklemede).
- **Vendor momentum'ı izlenmek zorunda:** document store kategorisinde #1 olan teknolojinin ilerlemesi (transaction sınırlarının kalkması vb.) izleme yükü doğurur → §2.3 kapısı tutulur.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| Gizli document-store girişi ("küçük entegrasyon" adıyla Firestore/Atlas bağlantısı) | 2 (mümkün) | 4 (yüksek — ADR-002/003/040 ihlali) | §2.2 yasak listesi + composer/`npm` taraması (`mongo*` = 0 korunur — §5.1/5) |
| MySQL JSON kolonlarının **kontrolsüz** büyümesi → JSON içinde şema drift (document store hastalığının MySQL versiyonu) | 3 (olası) | 3 (orta) | JSON alanlarına `JSON_SCHEMA_VALID`/kritik alan denetimi + §1.3-4 dersi (validation erken konmalı) — **eşik UNKNOWN, ölçüm §5.1/2** |
| Ölçek/arama ihtiyacı ileride belgelenirse geç kalınmış göç | 2 (mümkün) | 3 (orta) | §2.3/2 koşulu — ölçülmüş gereksinim + yeni ADR, dosya düzenlenmez |
| "Neden MongoDB yok?" dış baskısı (yeni ekip/klon proje) | 3 (olası) | 2 (düşük) | Bu dosya + §2.3 satırı — yanıt vault'ta yazılı |
| DB-Engines/vendor rakamlarının yanlış yorumlanarak red'in "popülerlik" gerekçesi sanılması | 2 (mümkün) | 2 (düşük) | §1.3 işaretli: karar **BCNF/MySQL ilkesine** dayanır, popülerliğe değil |

---

## 5. Uygulama (Implementation)

> Bu kayıt **salt-okunur seri**dir (`rejected/`); "uygulama" = kaydın vault'a doğru yerleştirilmesi ve denetimidir — kod değişiği **yoktur**.

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu dosyayı `.ai/.decisions/rejected/R-002-mongodb-document-store.md` olarak yaz (şablon §1-§7, 7 bölüm dolu) + `log.md` append ("R-002 yazıldı (debate PENDING)") | Vault Steward | 25 dk |
| 2 | **Ölçüm:** MySQL JSON yüzeyi sayımı (kaç JSON kolonu, kaç PHP JSON çağrısı, kaç esnek-alan ihtiyacı) → eşik ÖNERİSİ üret (rakam ölçülmeden yazılmaz) | Data Engineer + Backend Architect | 1 saat |
| 3 | **Debate ✅ TAMAMLANDI** (3 tur / 20 persona · 19/1/0 **RED DOĞRULANDI** — sonuç + 3 şart §5.3) + Tech Lead onayı ✅ | MO + Tech Lead | 2026-10-01 |
| 4 | **`index.md:127` `<!-- dead-link ... -->` bayrağı → DOKUNULMADI** (talimat gereği son sıfırlamaya ertelendi — rapor-only) · `rejected/index.md` § tablosu **BOŞ → DOKUNULMADI** (rapor-only) | Vault Steward | son sıfırlama |
| 5 | Periyodik denetim: `mongo*` composer/kod taraması = **0** korunur + `JSON_EXTRACT` büyümesi izlenir (§2.2 yasak listesi) | Data Engineer + QA Engineer | her sprint |

### 5.2 Geri Dönüş Planı

Bu karar **kod tarafında geri alınacak bir şey üretmedi** (MongoDB hiç girmedi → `git revert` edilecek değişiklik **0**; composer.lock'ta mongo paketi yok, §1.1/7). Geri dönüş = **yeniden değerlendirme** demektir ve yalnız §2.3 koşullarından biri yazılırsa yeni ADR ile açılır: (1) BCNF/MySQL tekelini kaldıran yeni ADR → bu dosya `superseded` notuyla **bağlanır, düzenlenmez**; (2) ölçülmüş iş ihtiyacı → Data Engineer kanıtıyla yeni ADR; (3) debate sonucu değişirse → debate kaydı + yeni ADR. Vault bozulursa standart kurtarma `git checkout` + son commit (AGENTS.md §17 #10). **Reddedilen teknolojinin kodda izi olmadığı için veri/kullanıcı etkisi YOKTUR.**

### 5.3 Debate Şartları

**Kayıt:** 3 tur / 20 persona · **Tur 1:** grep 3 vault isabeti (index.md:127 · ADR-003:164/251 — hiçbiri red'in kendi metni değil), yerini alanlar diskte (ADR-003/040/002/033/014/081/039), mongodb kod izi 0, 4 sorgu/40 isabet (~28 benzersiz) → 17 kabul/neutral + 3 uyarı (Critic: vendor kaynak · Data: argüman sabitleme · DevOps: 19↔18 SQL) · **Tur 2:** 3 itiraz → 3 şart · **Tur 3 oy:** 19 kabul / 1 çekimser / 0 red → **RED DOĞRULANDI**.

**3 Şart (bağlayıcı):**

| # | Şart | Kaynak itiraz (Tur 2) | Durum |
|---|------|----------------------|-------|
| 1 | **Ters kanıt bağımsız kaynakla doğrulanır ya da ⚠️ vendor işareti kalıcı tutulur** — MongoDB geliri ($2,39M FY2026) ve Fortune 500 %70+ rakamları vendor kaynaklıdır; tek başına karar gerekçesi yapılmaz (§1.3, §7.1/7) | Ters kanıt tek kaynaklı (vendor) | ✅ YAZILDI |
| 2 | **Karar argümanı popülerliğe değil, BCNF/operasyon gerekçesine sabitlenir** — DB-Engines popülerlik ölçütü (MongoDB #5 / dokümanda #1) teknik uygunluk ölçütü değildir; red ADR-003 BCNF zorunluluğu + ADR-002 PDO + ADR-040 MySQL yetkisine bağlanır (§2.1, §4.3) | DB-Engines popülerlik ≠ teknik uygunluk | ✅ YAZILDI |
| 3 | **Yeniden değerlendirme kapısı** — MySQL JSON kapasitesi yetmezleşirse veya polyglot domain split kararı değişirse §2.3 koşulu üzerinden yeni ADR ile yeniden açılır; bu dosya düzenlenmez | Yeniden değerlendirme koşulu tanımsız | ✅ YAZILDI (§2.3) |

> **Debate sonucu:** ✅ **TAMAMLANDI** (3 tur / 20 persona · 19 kabul / 1 çekimser / 0 red → **RED DOĞRULANDI**) · **Tech Lead:** ✅ · **Arch Lead:** ⏳.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../index]] | Karar dizini — bu red'in kaydı (`:127`, slug + "BCNF uyumsuz" + dead-link bayrağı §5.1/4) |
| [[../accepted/ADR-003-multi-db-bcnf]] | **Yerini alan (birincil):** çoklu-DB BCNF mimarisi; `:164` uzun gerekçe (polyglot ret + "red kararı R-002"), `:251` dizin özeti, `:142` "tüm 18 şema MySQL'dir" |
| [[../accepted/ADR-040-database-authority]] | **Yerini alan (yetki/matris):** 18 BCNF sahiplik matrisi + tek-yazar + migration tek kapısı (debate ✅ 18/2/0) |
| [[../accepted/ADR-002-pdo-mandatory-no-orm]] | Erişim katmanı — PDO tekelı: ikinci document-store sürücüsü bu tekelin paralelidir (§2.2) |
| [[../accepted/ADR-033-sql-normalization-strategy]] | 156 tablo / 82 FK / 28 cross-DB bulguları — BCNF denetiminin ayrıntısı |
| [[../accepted/ADR-014-multi-db-migration-strategy]] | Migration tek kapısı (özel PHP runner) — document store bu kapıya girmez |
| [[../accepted/ADR-081-multi-provider-data-sync]] | Outbox + WAL — DB arası tutarlılığın red-dışı telafisi |
| [[../accepted/ADR-039-7-service-platform-architecture]] | 11 servis — sahiplik matrisinin servis tarafı |
| [[../../raw/brain]] | Mimari karar özeti (veri/DB satırı) |
| [[R-001-redux-style-state-management]] | Seri kardeşi — aynı salt-okunur red kayıt formatı (bu dosyanın format referansı) |
| Debate şartları | Bu dosya **§5.3** — debate ✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 **RED DOĞRULANDI**) + **3 şart**: (1) bağımsız kaynak/⚠️ (2) argüman sabitleme (3) yeniden değerlendirme kapısı |
| Düz metin | Eski seri R-003…R-012 (`rejected/index.md` tablosu boş → §7.1/2) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-10-01 | ✅ |
| Tech Lead | — | 2026-10-01 | ✅ |
| Arch Lead | — | — | ⏳ |

### 7.1 Rapor Notları (bu işlemde dokunulmayanlar)

1. **Dead-link bayrağı:** `index.md:127` `<!-- dead-link: R-002-mongodb-document-store no source 2026-09-24 -->` **olduğu gibi bırakıldı** — artık `no source` iddiası **geçersizdir** (bu dosya kaynaktır) → temizlik son sıfırlamaya ertelendi, burada rapor edildi.
2. **`rejected/index.md` durumu:** dosya **VAR** (`rejected/index.md`, v1.0.1, `total: 12`) ama tablo **BOŞ** (`:18-19` başlık satırları, kayıt satırı yok) → bu işlemde **oluşturulmadı ve doldurulmadı** (dizin satırı düzenleme yetkisi kapsam dışı) → rapor-only.
3. **Slug hizası:** dosya adı `R-002-mongodb-document-store` = `index.md:127` slug **birebir** ✅ (R-001 dersi: tahmin yok, `rejected/` glob'u ile doğrulandı).
4. **Debate:** ✅ **TAMAMLANDI** (3 tur / 20 persona · 19 kabul / 1 çekimser / 0 red → **RED DOĞRULANDI** · 3 şart §5.3) · **Tech Lead:** ✅ · **Arch Lead:** ⏳ (bu işlemde dokunulmadı).
5. **SQL dosya sayısı farkı (rapor-only):** `.ai/.sql/mysql/*.sql` glob = **19 dosya** (2026-10-01), ADR-040 `:33` "**18** (`coremusic_ai` … `coremusic_wireless`)" der → **1 fark**; farkın nedeni **bu işlemde araştırılmadı, yorum uydurulmadı** (ör. `media_catalog.sql`/`coremusic_media.sql` ayrımı UNKNOWN) → sonraki ADR-040 revizyonuna/not-taşıyıcıya bırakıldı.
6. **Kod yüzeyi kanıtı:** `mongo*` = **0 isabet** (5 `composer.json` + tüm `*.php/*.js/*.json` — §1.1/7); MySQL JSON fonksiyonu kodda 0, şemada 1 örenek (`media_catalog.sql:456-457` — §1.1/9).
7. **Vendor iddiaları:** Fortune 500 %70+ / gelir rakamları **mongodb.com kaynaklıdır**, bağımsız doğrulanmadı → §1.3'te ⚠️ işaretli, bağlayıcı yapılmadı.

---

*R-002 v1.0.0 | 2026-10-01 | Created — CoreMusic Vault (.decisions/rejected/ sıfırdan yazım, salt-okunur seri)*
*Authority: R-002 Red Karar Metni · Mode: Red Team · Human Mode · Truth Mode*
