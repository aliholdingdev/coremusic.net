---
title: "CoreMusic — R-008: MySQL MyISAM Motoru (REDDEDİLMİŞ — Transaction Eksik)"
type: "architecture-decision"
category: "database"
date: "2026-10-07"
updated: "2026-10-07"
version: "1.0.0"
status: "rejected"
authority: "SSOT — R-008 red kararı: CoreMusic MySQL veri yüzeyinde 'ENGINE=MyISAM' KABUL EDİLMEZ; 18 BCNF şemasının tamamı InnoDB'dir. Gerekçe: karar dizini index.md:142 'MyISAM | Transaction eksik' + disk kanıtı (.ai/sources/.sql/mysql/ 19 .sql dosyasında ENGINE=InnoDB = 165, ENGINE=MyISAM = 0) + kod yüzeyi (PHP/CSS/JS/SQL taraması = 0 MyISAM izi) + yerini alan ADR-003 (multi-DB BCNF), ADR-040 (18 DB otorite), ADR-041 (kalan DB kuralları). Kapsam: red yalnız MySQL motoru seçimini reddeder; PostgreSQL/SQLite vb. DBMS alternatifleri bu redin kapsamı dışındadır (ADR-002/003 bunları ayrıca ele alır). Web araştırması (§1.3, 5 sorgu / ~26 kaynak) dürüst sonucu yazdı: MyISAM MySQL 9.x'te **kaldırılmamış ve resmî olarak deprecated edilmemiştir** — red'in dayanağı 'deprecated' değil, **mimari** (transaction + BCNF FK + crash dayanıklılığı + eşzamanlı yazma). Debate ✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 RED DOĞRULANDI — §7). Bu dosya salt-okunur seridir (rejected/) — değiştirilmez, yalnız referanslanır."
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 RED DOĞRULANDI)"
---

# CoreMusic — R-008: MySQL MyISAM Motoru (Rejected)

> **Durum:** rejected (**debate ✅ TAMAMLANDI**) — **Tarih:** 2026-10-07 — **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona, 19/1/0 RED DOĞRULANDI)** — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Seri:** `.ai/.decisions/rejected/` (salt-okunur) — **Slug:** `R-008-mysql-myisam-engine` (dizin otoritesi: [[../index]] **satır 142** — görevde tahmin edilen **134 DEĞİL**; 2026-10-07 grep: `index.md:142` = gerçek satır — R-007'deki 132→133 düzeltmesinin aynısı)
> **Dizin satırı:** `| R-008-mysql-myisam-engine | MyISAM | Transaction eksik <!-- NO FILE on disk 2026-10-06 --> |` — `<!-- NO FILE on disk ... -->` bayrağı **bu işlemde DOKUNULMADI** (temizlik son sıfırlamaya ertelendi → §5.1/3 + §7.1/1). Aynı satırın aynısı [[../../wiki/vault-decisions]] `:171`'de de var (ayrıca dokunulmadı → §7.1/2).
> **İlgili kararlar:** [[../accepted/ADR-003-multi-db-bcnf]] (yerini alan — 18 domain DB + BCNF) · [[../accepted/ADR-040-database-authority]] (yerini alan — 18 DB sahiplik matrisi + tek yazar) · [[../accepted/ADR-041-database-normalization-supplementary]] (yerini alan — şema adlandırma + veri tipi + view/trigger kuralları) · [[../accepted/ADR-002-pdo-mandatory-no-orm]] (MySQL 9 + PDO erişim katmanı) · [[../accepted/ADR-014-multi-db-migration-strategy]] (online DDL / expand-contract) · karar dizini [[../index]] §5.
> **R-001…R-007 dersi uygulandı:** wiki-link slug'ları **tahmin edilmedi** — hedefler `rejected/` ve `accepted/` glob'ları ile diskten doğrulandı; diskte olmayan hedefe link **kurulmadı** (ör. `.ai/brain.md` diskte YOK → `.ai/brain.md`'e link **kurulmadı**; gerçek path `[[../../raw/brain]]` kullanıldı, §7.1/7).

---

## 1. Bağlam (Context)

CoreMusic'in veri yüzeyi **MySQL 9 + 18 BCNF şeması + PDO (ORM yasak)** üzerine kuruludur (ADR-002/003/040/041). Karar dizini bu tercihi tescil etmiştir (`index.md:142` — "MyISAM | Transaction eksik") ama **red metni hiç yazılmamıştır**: elde yalnız dizin satırı + `<!-- NO FILE on disk -->` notu + BCNF/otorite ADR'lerinin gerekçe bağı + şema dosyalarındaki `ENGINE=InnoDB` kanıtı vardır. Bu dosya, o satırın **gerekçeli red kaydıdır** — yeni bir karar değil, mevcut red'in (a) gerekçe, (b) güncel web araştırması, (c) yerini alan eşleme, (d) yeniden değerlendirme koşulu ile sıfırdan yazımıdır.

### 1.1 Mevcut Durum (disk kanıtı — dürüst etiket, 2026-10-07 taraması)

| # | İddia | Kanıt | Etiket |
|---|-------|-------|--------|
| 1 | Red kaydı var mı? | [[../index]] `:142` → `R-008-mysql-myisam-engine` + "MyISAM" + "Transaction eksik" + `<!-- NO FILE on disk 2026-10-06 -->` | ✅ **KAYITLI** (dizin satırı tek kanıt; red metni bu işlemde yazılıyor) |
| 2 | Bu işlem öncesi dosya var mıydı? | `.ai/.decisions/rejected/` içinde `CLAUDE.md`, `index.md`, `R-001-*`…`R-007-*` ; `R-008*` = **0 dosya** | ❌ **YOKTU** → bu işlemde yazılıyor (klasör zaten var — `rejected/` oluşturulmadı) |
| 3 | `R-008` grep isabeti (vault geneli) | `index.md:142` (slug + "Transaction eksik" + NO FILE) · `wiki/vault-decisions.md:171` (aynı satırın aynası) | ✅ **İSABET = 2** — ikisi de **red'in kendi dizin kaydı**; kodda `R-008` = **0** |
| 4 | Red gerekçesi başka yerde yazılı mı? | `index.md:142` ("Transaction eksik" — **tek satır**) · uzun gerekçe **asıl** ADR-003 §2 (18 domain DB + BCNF + DB-içi FK / DB-arası FK yasağı) + ADR-040 §2 (tek yazar + 18 DB sahiplik matrisi + cross-DB politikası) + ADR-041 §2 (şema kuralları) | ✅ **3 ADR'de uzun gerekçe** — ama **hiçbiri `InnoDB` adını anmıyor** (ADR-003/040/041'de `InnoDB` = **0 isabet**, `MyISAM` = **0 isabet**) → motor kararı **şema ADR'lerinde açıkça yazılmamış**; bu dosyanın gerekçesi disk kanıtına (§1.1/5-7) dayanır |
| 5 | Motor kanıtı (şema dosyaları) | `.ai/sources/.sql/mysql/*.sql` → **19 dosya** · `ENGINE=InnoDB` = **165** · `ENGINE=MyISAM` = **0** · `myisam` (büyük/küçük harf duyarsız) = **0** | ✅ **165 / 0** — tek motor InnoDB; MyISAM izi şemada **yok** (görev `.ai/.sql/mysql/` der; o yol diskte YOK → §7.1/5) |
| 6 | Kod yüzeyi: MyISAM? | Repo taraması (PHP/JS/CSS/MD/SQL, `.ai.OLD`/`node_modules`/`_backup`/`.git` hariç, **19.660 dosya**) → `MyISAM` isabeti = **2**, ikisi de vault dizin satırı (`index.md:142`, `wiki/vault-decisions.md:171`) · üretim kodu `MyISAM|myisam` = **0** | ✅ **KODDA MyISAM 0** — MyISAM hiç kullanılmamış; red "girişi engelleme" kararıdır (R-006 deseni) |
| 7 | Yerini alan kararlar diskte? | `accepted/` glob: `ADR-003-multi-db-bcnf.md` · `ADR-040-database-authority.md` · `ADR-041-database-normalization-supplementary.md` — hepsi `status: accepted` · destek: `ADR-002-pdo-mandatory-no-orm.md`, `ADR-014-multi-db-migration-strategy.md` | ✅ **3/3 + 2 destek** (glob ile doğrulandı — R-001 dersi) |
| 8 | InnoDB vault'da geçiyor mu? | Kabul edilen ADR'lerde `InnoDB` isabeti: ADR-073 (12) · ADR-074 (8) · ADR-072 (7) · ADR-014 (3) · ADR-079 (2) · ADR-050 (1) · ADR-075 (1) · ADR-078 (1) — toplam **35** · `ADR-079:132` "coremusic_system.sql … InnoDB … utf8mb4_unicode_ci" | ✅ **InnoDB = 8 ADR'de + 165 şema satırında** — ama ADR-003/040/041'de adı **geçmez** (§1.1/4 dürüst etiketi) |
| 9 | MySQL 9 çelişkisi araştırıldı mı? | Vault MySQL 9 iddiaları: `ADR-002:24` ("PHP 8.4, MySQL 9, 18 BCNF"), `ADR-014:61` ("MySQL 8.4/9.7 Reference Manual"), `ADR-074:61` ("MySQL 9.3 RefManual §26.6"), `.ai/index.md:19`, `.ai/.templates/coremusic-vault-template.md:46/:55/:95` | ✅ **Araştırıldı** → §1.3: MyISAM MySQL 9.x'te **hâlâ mevcut** (RefMan 9.2/9.6/9.7 §18.2); "deprecated/kaldırıldı" iddiası **yanlış olurdu** → red gerekçesi **mimariye** sabitlendi (§2.1/3) |
| 10 | `rejected/index.md` durumu? | Dosya **VAR** (v1.0.1, `total: 12`) ama § tablosu **BOŞ** (başlık satırları var, 12 red'in hiçbiri satırlanmamış) | ⚠️ **BOŞ** → bu işlemde **dokunulmadı** → §7.1/3 |
| 11 | Debate sonucu? | Debate **çalıştırıldı** — 3 tur / 20 persona (AGENTS.md §6.1) → **19/1/0 RED DOĞRULANDI** + **4 bağlayıcı şart** (§5.4) + Tech Lead **✅** | ✅ **TAMAMLANDI** → §5.4 + §7 |
| 12 | Araştırma protokolü diskte? | `.claude/skills/prompt-maker/references/10-web-research-protocol.md` → `Test-Path` = **True** | ✅ OKUNDU (§1.3 bu protokolle üretildi) |
| 13 | Şablon yolu? | Görev `.ai/.templates/adr/adr-template.md` der; disk kanıtı `.ai/.templates/adr/adr-template.md` (`glob` = True, v2.0.2) | ✅ **DÜZELTİLDİ** — gerçek yol `.ai/.templates/adr/` (nokta öneki) → §7.1/6 |

> **Ders notu:** bu red **hiçbir zaman kodda denenmedi** — üretim kodunda MyISAM **0** (§1.1/6); şemada MyISAM **0** (§1.1/5); "reddedildi" = "MyISAM **hiç kurulmadı** ve motor tercihi InnoDB üzerine **sessizce** oturdu". Gelecekte biri "MyISAM kullansak mı?" derse yanıtı bu dosya + 3 yerini alan ADR verir; "zaten denedik mi?" sorusunun yanıtı **hayır, hiç denenmedi** (§5.2). ⚠️ DÜRÜST GERİLİM: MyISAM **deprecated değildir** (§1.3) — red'i ayakta tutan şey "yasak/unsupported" değil, **işlevsel gereksinim**dir (transaction + FK + crash dayanıklılığı).

### 1.2 Sorun Tanımı

1. **Red kararı kanıtsız duruyor.** `index.md:142` bir sonuç cümlesi ("Transaction eksik") ama **ne 2025-26 ekosistem kanıtı (MySQL 9.x'te MyISAM'ın durumu, InnoDB ile performans farkı, FULLTEXT/table-lock/crash recovery) ne yerini alan eşleme ne yeniden değerlendirme koşulu** yazılı — gelecekteki biri "MyISAM neden yok, bugün de mi yok, salt-okunur log tablosunda istisna olur mu?" sorusuna vault'tan cevap bulamıyor.
2. **Motor kararı hiçbir ADR'de adıyla yok.** `ADR-003/040/041`'de `InnoDB` = **0**, `MyISAM` = **0** (§1.1/4) — yani "18 şema InnoDB'dir" iddiası **şema dosyalarından** (165 `ENGINE=InnoDB`) ve **şema ADR'lerinden** (ADR-072/073/074/079) okunuyor, bir karar metninden değil. Bu, SSOT için bir boşluktur: motor seçimi **uygulamada var, karar olarak yok**.
3. **"MySQL 9" çelişkisi denenmedi.** Vault MySQL 9'u hedef gösteriyor (ADR-002:24, ADR-074:61) — MyISAM'ın MySQL 9.x'te gerçekten deprecated olup olmadığı **araştırılmamıştı**; "deprecated" sanılan bir iddia yazılsaydı hallucination olurdu (§1.3 dürüst bulgusu).
4. **Koşul tanımsız.** "Salt-okunur log/analitik tabloda MyISAM istisnası düşünülebilir mi?" hiç belgelenmedi → yeniden değerlendirme tetikleyicisi tanımsız.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (öncelik: resmî doküman → vendor → bağımsız blog; her iddiaya kaynak). Araştırma 2026-10-06/07'da yapıldı — **5 sorgu / ~26 adlandırılmış kaynak**.

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "MyISAM MySQL 9.0 deprecated removed 2025 2026 InnoDB default" → (2) "MySQL 9.0 release notes MyISAM deprecated removal storage engine" → (3) "InnoDB vs MyISAM performance benchmark table locking FULLTEXT crash recovery 2025" → (4) "InnoDB FULLTEXT support since 5.6 MyISAM fulltext advantage obsolete" → (5) "MyISAM table level locking read heavy advantage still recommended use case MySQL 8 9" |
| Web Search **Konusu** | **(1-2)** MySQL 9.x'te MyISAM'ın resmî durumu (kaldırıldı mı, deprecated mi, RefMan hâlâ yazıyor mu, hangi desen deprecated) · **(3-4)** InnoDB vs MyISAM performans/fişti (tablо kilidi, transaction, crash recovery, FULLTEXT) · **(5)** MyISAM'ın **hâlâ geçerli** olduğu okuma-ağırlıklı senaryolar + resmî optimizasyon rehberi |
| Web Search **Bağlam** | CoreMusic: 19 `.sql` dosyasında `ENGINE=InnoDB` = 165 / `MyISAM` = 0 (§1.1/5) · kodda MyISAM **0** (§1.1/6) · 18 BCNF şeması + DB-içi FK (ADR-003) + MySQL 9 hedefi (ADR-002:24) · hedef soru — *"MyISAM red'i bugün hâlâ doğru mu; MySQL 9'da MyISAM deprecated mi; red'in gerekçesi 'Transaction eksik' tek başına yeterli mi; salt-okunur log tablosu istisnası düşünülebilir mi?"* |
| Web Search **Kısa Açıklama** | **(1-2)** MyISAM MySQL 9.x'te **hâlâ belgelenen** bir motordur (RefMan 9.2/9.6/9.7 §18.2 "The MyISAM Storage Engine"); InnoDB **5.5'ten beri varsayılan** (Oracle/lefred) → MyISAM'ı `ENGINE=` ile **açıkça** istemek gerekir (RefMan 9.7 §18.2). MySQL 9.0'un kaldırdığı bir motor **yok** (bytebase: "No storage engine is removed in MySQL 9.0"); 9.0.0'ın deprecated ettiği şey **mixed-engine transaction** desenidir (relnotes 9.0.0) — yani MyISAM + InnoDB **birlikte** yazan transaction'lar. **(3-4)** MyISAM: **tablo kilidi** (yalnız 1 yazıcı), **transaction yok**, **FK yok**, **crash recovery InnoDB'den zayıf**; FULLTEXT avantajı **2013'te (MySQL 5.6) InnoDB'ye geçti** → "FULLTEXT için MyISAM" argümanı **geçersiz** (dev.mysql.com 14.9.5 "Full-text searches are supported for InnoDB and MyISAM tables only"). **(5)** MyISAM'ın kalan yeri: **read-mostly / düşük eşzamanlılık** (RefMan 10.6) ve tek-yazıcı senaryolar (RefMan 10.11.1 "more suitable for read-only, read-mostly, or single-user applications"). |
| Web Search **Uzun Açıklama** | **(1-2) Resmî durum:** dev.mysql.com RefMan 9.2 §18.2, 9.6 §18.2, 9.7 §18.2 MyISAM'ı anlatmaya **devam ediyor**; 9.7 §18.2 "MyISAM storage engine provides **no partitioning support**" + §17.6.1.5 "Partitioned MyISAM tables created in previous versions are **not compatible** with MySQL 9.7 → InnoDB'ye çevirin" (docs.oracle.com MySQL 9.7 kopyası aynı metni veriyor). bytebase "What's New in MySQL 9": **"No storage engine is removed in MySQL 9.0"** — deprecated olan tek şey **mixed transactional/nontransactional transaction** (dev.mysql.com relnotes 9.0.0 Deprecation Notes). Oracle/lefred + devart: InnoDB **5.5'ten (Temmuz 2010) beri varsayılan**. **Yani:** "MyISAM MySQL 9'da deprecated/kaldırıldı" iddiası **kaynaklarla desteklenmiyor** → bu dosyada **yazılmadı**. **(3-4) İşlevsel farklar:** tablo kilidi vs satır kilidi (devart, oneuptime, mironsoft); MyISAM'da **transaction/ACID/FK yok** (lefred: "MVCC, ACID transactions, row-level locking, crash recovery, foreign keys"); crash recovery'de InnoDB **superior** (devart, quora, signalprime); FULLTEXT: **InnoDB 5.6'dan beri destekliyor** (oneuptime: "removing one of the last reasons to use MyISAM"; resmî 14.9.5 iki motoru da kapsıyor) → MyISAM'ın eski tek avantajı **kalmadı**. **(5) MyISAM'ın kalan lehçesi:** RefMan 10.6 "performs best with read-mostly data or low-concurrency operations, because table locks limit simultaneous updates"; RefMan 10.11.1 MyISAM/MEMORY/MERGE = tablo kilidi → "read-only, read-mostly, single-user"; tecadmin/w3tutorials aynı yönde (okuma-ağırlıklıda MyISAM **hızlı olabilir**); oneuptime: MyISAM "legacy engine appropriate only for read-only archive tables". |
| Web Search **Paragraf Veri Uzun** | **Resmî/vendor (1-2):** dev.mysql.com RefMan 9.2 §18.2 "The MyISAM Storage Engine" · dev.mysql.com RefMan 9.6 §18.2 · dev.mysql.com RefMan 9.7 §17.6.1.5 "Converting Tables from MyISAM to InnoDB" · docs.oracle.com MySQL 9.7 §18.2 + §17.6.1.5 · dev.mysql.com "Changes in MySQL 9.0.0 (2024-07-01)" Deprecation Notes · bytebase.com "What's New in MySQL 9 — a DBA's Perspective" · Oracle MySQL Blog "Still using MyISAM? It is time to switch to InnoDB!" · lefred.be aynı makale · dev.mysql.com RefMan 8.4 §10.11.1 "Internal Locking" · dev.mysql.com RefMan 8.4 §10.6 "Optimizing for MyISAM" · dev.mysql.com RefMan 8.4 §18.2 · dev.mysql.com RefMan 8.0 §14.9.5 "Full-Text Restrictions" — **12**. **Karşılaştırma/benchmark (3-4):** devart.com "MyISAM vs InnoDB: Differences & Performance Comparison" · oneuptime.com "MySQL InnoDB vs MyISAM: Which Storage Engine to Use" (2026-03-31) · oneuptime.com "What Is MyISAM in MySQL" (2026-03-31) · oneuptime.com "MySQL table-level locking" (2026-03-31) · mironsoft.de "InnoDB vs. MyISAM: Differences That Still Matter" · signalprime.com "InnoDB vs MyISAM: A Modern Guide" · besthub.dev "Why MyISAM Is Obsolete" · tecadmin.net "Choosing Between InnoDB, MyISAM, and MEMORY" · tonylixu.medium.com "MySQL: Choosing Between InnoDB and MyISAM" · learnomate.org "MySQL Storage Engines (InnoDB vs MyISAM)" · quora.com "What makes InnoDB crash-safe compared to MyISAM" · stackoverflow.com "Why would you choose MYISAM over InnoDB?" · stackoverflow.com "InnoDB vs MyISAM" · dba.stackexchange.com "main differences between InnoDB and MyISAM" · dba.stackexchange.com "Why does MyISAM support Full Text Search and InnoDB does not?" · reddit r/mysql "Why would you choose MYISAM over InDB?" · w3tutorials.net "Why Table-Level Locking is Better Than Row-Level Locking" · medium/imjeetrs "Top 10 Deprecated MySQL Features 2025" · computingforgeeks "Convert MySQL tables from MyISAM into InnoDB" · isitpatched.com "MySQL end-of-life dates (2026)" — **19**. **Toplam ~26 benzersiz adlandırılmış kaynak, 5 sorgu**; ana iddialar (MyISAM RefMan 9.x'te mevcut · MySQL 9.0 motor kaldırmadı · tablo kilidi · FULLTEXT iki motoru da kapsıyor) **≥2 kaynakla** çapraz doğrulanıyor. |
| Web Search **Sonucu** | **(1-2) Dürüst düzeltme:** "MyISAM MySQL 9'da deprecated/kaldırıldı" **DEĞİL** (RefMan 9.7 §18.2 hâlâ var; bytebase "no storage engine removed"; deprecated olan **mixed-engine transaction**) → **red'in tek dayanağı 'Transaction eksik' cümlesi tek başına yetmez**, ama **yanlış da değildir**: MySQL 9.0 mixed-engine transaction'ı zaten deprecated etti → MyISAM + InnoDB karışık yazım **yolun sonuna** gidiyor. **(3-4) Red destekleniyor:** transaction yok + tablo kilidi + crash kaybı + FULLTEXT avantajı **2013'te kalmış** → InnoDB karşısında **işlevsel** üstünlük. **(5) İtiraz (dürüst):** MyISAM **read-mostly** tablolarda hâlâ **resmî olarak** önerilir (RefMan 10.6/10.11.1) ve daha basit/az kaynak kullanır → bu, red'i **değiştirmez** ama **kapsamı daraltır**: red, CoreMusic'in **yazma + transaction + FK içeren** BCNF şeması içindir; salt-okunur log/arşiv istisnası §2.3'e taşındı. **İtiraz/karşıt bulgu:** (i) MyISAM **deprecated değil** → "yasak" dili **kullanılmadı**; (ii) InnoDB'nin küçük/tablolarda **daha yavaş** olabileceği iddiası (read-mostly) **ölçülmemiş** (CoreMusic'te benchmark **UNKNOWN**); (iii) sayfa-içi derin tur **yapılmadı** → başlık/özet düzeyi (§5.1/5). |
| Web Search **Alınan Karar** | **R-008 RED (MySQL MyISAM Motoru) YÜRÜRLÜKTE KALIR.** (a) **CoreMusic MySQL yüzeyinde `ENGINE=MyISAM` KABUL EDİLMEZ** — 18 BCNF şeması InnoDB'dir; bunu `index.md:142` + 165 `ENGINE=InnoDB`/0 `MyISAM` (§1.1/5) + ADR-003 (BCNF + DB-içi FK) + ADR-040 (18 DB otorite) + ADR-041 (şema kuralları) kilitler. (b) **Red gerekçesi 'deprecated' değil MİMARİDİR:** (i) transaction/ACID **yok** (red'in dizin gerekçesi — §1.1/1); (ii) **tablo kilidi** → eşzamanlı yazmada tüm tablo kilitlenir (RefMan 10.11.1); (iii) **crash recovery** InnoDB'den zayıf → kayıt kaybı riski; (iv) **FULLTEXT avantajı kalmadı** (InnoDB 5.6'dan beri destekliyor); (v) MySQL 9.0 **mixed-engine transaction'ı deprecated** etti → MyISAM'ı InnoDB şemasına serpiştirmek **yolun sonu**. (c) **Dürüst beyan:** MyISAM MySQL 9.x'te **kaldırılmamıştır ve deprecated edilmemiştir** (RefMan 9.7 §18.2 + bytebase) → bu dosya "yasak/unsupported" değil, **işlevsel gereksinim** gerekçesiyle red yazar; "MySQL 9'da MyISAM yok" iddiası **yazılmaz** (⚠️ VERIFICATION REQUIRED sayılmaz — **yanlıştır**). (d) **Kapsam:** red **yalnız MyISAM'ı** reddeder; InnoDB'nin read-mostly'de yavaşlığı **ölçülmemiştir** (benchmark UNKNOWN) → "InnoDB her yerde en hızlı" iddiası **kurulmaz** (§4.3/4). (e) **Yeniden değerlendirme koşulu** (§2.3) yazılmadan bu red **otomatik olarak güncellenmez**. |
| Web Search **Sonuç** | **5/5 sorgu**: (1-2) **dürüst düzeltme** — MyISAM MySQL 9'da deprecated **değil** (red gerekçesi mimariye taşındı); (3-4) **red desteklendi** — transaction/tablo kilidi/crash/FULLTEXT kanıtları; (5) **kapsam daraltması** — resmî dokümanlar read-mostly'de MyISAM'a izin veriyor → istisna §2.3 şartına taşındı. **İki dürüst gerilim yazıldı:** (i) "deprecated değil" gerçeği (gerekçe MİMARİ oldu); (ii) InnoDB benchmark'ı **UNKNOWN** (CoreMusic'te ölçülmedi). **Üç açık işaretlendi:** (i) sayfa-içi derin tur yok (§5.1/5) · (ii) mixed-engine deprecated tek resmî dayanak, onun dışında "MySQL 9 yasağı" **yok** · (iii) MySQL sürümü vault'ta 9.0/9.2/9.3/9.7 arasında **dağınık** (ADR-002 "MySQL 9", ADR-074 "9.3", ADR-014 "9.7") → kesin sürüm **UNKNOWN**. **Kaynak sayısı: 5 sorgu; §1.3'te adı geçen benzersiz kaynak ~26.** |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-003 accepted (18 domain DB + BCNF) | H1 "Multi-Domain BCNF Veritabanı Mimarisi"; `:84` cross-DB FK yasağı, `:129` "18 şema, 156 tablo", `:136` "DB arası FK: YOK" — BCNF + FK **InnoDB'nin işlevi**dir (MyISAM'da FK **yoktur**) → **bu red'in bağlayıcı dayanağı** |
| ADR-040 accepted (18 DB otorite) | H1 "Database Authority (18 BCNF sahiplik matrisi · tek yazar · cross-DB politikası)"; `:33` 18 `.sql` dosyası IMPLEMENTED, `:40` cross-DB FK metni 15 satır — tek motor disiplini **bu otoritenin** parçasıdır |
| ADR-041 accepted (kalan DB kuralları) | Şema adlandırma + veri tipi + view/trigger/procedure + audit + N+1 kuralları — motor dışı DB kurallarını tamamlar; red ile **çelişmez**, aynı disiplini tamamlar |
| ADR-002 accepted (PDO, ORM yasak) | `:24` "PHP 8.4, MySQL 9, 18 BCNF" — erişim katmanı PDO'dur; MyISAM'ın transaction'sız yapısı PDO'nun transaction beklentileriyle (`beginTransaction/commit/rollback`) **çatışırdı** |
| ADR-014 accepted (migration) | `:61` "MySQL 8.4/9.7 Reference Manual §17.12.1 Online DDL + §15.1.9 ALTER TABLE" — expand-contract + online DDL **InnoDB odaklıdır**; MyISAM'da online DDL/locking modeli farklıdır |
| Şema dosyası gerçeği (165/0) | `.ai/sources/.sql/mysql/` 19 dosyada `ENGINE=InnoDB` **165**, `ENGINE=MyISAM` **0** → red bir "kaldırma" değil, **mevcut tek-motor disiplininin tescili** (§1.1/5) |
| Kod yüzeyi gerçeği (0) | 19.660 dosyalık taramada üretim kodunda MyISAM **0** → geri dönüş planı kod tarafında işlem gerektirmez (§5.2) |
| MySQL 9 resmî durumu | MyISAM RefMan 9.2/9.6/9.7'de **mevcut**; MySQL 9.0 **hiçbir motoru kaldırmadı**; deprecated = **mixed-engine transaction** → "yasak" dili bu dosyada **yok** (§1.3-1/2) |
| Araştırma protokolü | §1.3 `10-web-research-protocol.md` ile üretildi; "MyISAM read-mostly'de öneriliyor" (ters kanıt) dürüstçe yazıldı ama red'i değiştirmedi, **kapsamı daralttı** (§2.3) |

---

## 2. Karar (Decision)

**R-008 REDDEDİLMİŞTİR: CoreMusic MySQL veri yüzeyinde "ENGINE=MyISAM (MySQL MyISAM depolama motoru)" KABUL EDİLMEZ.** Karar `index.md:142`'de bugünden vardı; bu dosya onu gerekçelendirir: red, ADR-003'ün "18 domain BCNF + DB-içi FK" hükmünün + ADR-040'ın "18 DB sahiplik matrisi + tek yazar" kuralının + ADR-041'in şema kurallarınının **red-kayıt ayağıdır** ve 2026-10-06/07 web araştırması (§1.3 — 5 sorgu, ~26 kaynak) red'in **bugün hâlâ doğru olduğunu** doğrulamıştır — "MyISAM deprecated olduğu" için **DEĞİL** (§1.3: MySQL 9.x'te hâlâ mevcut), **işlevsel gereksinim** olduğu için: transaction + BCNF FK + crash dayanıklılığı + eşzamanlı yazma.

### 2.1 Neden Bu Seçenek?

1. **Disk kanıtı tek motor gösteriyor (ölçüldü):** `.ai/sources/.sql/mysql/` → `ENGINE=InnoDB` = **165**, `ENGINE=MyISAM` = **0**, `myisam` metni = **0** (§1.1/5); üretim kodu `MyISAM` = **0** (§1.1/6). Red, **hiç kurulmamış** bir motoru engeller; "18 şema InnoDB'dir" iddiası **şema dosyalarından** okunur.
2. **BCNF + FK InnoDB'nin işlevidir:** ADR-003 `:84` (cross-DB FK yasağı) + `:136` ("DB arası FK: YOK") + ADR-040 `:40` (cross-DB FK politikası 15 satır) — DB-içi FK'lar (ADR-040 `:53` → `coremusic_social.sql:47` `REFERENCES coremusic_auth.users(id)` vb.) MyISAM'da **fiziksel olarak yazılamaz**; MyISAM seçimi bu üç ADR'nin **dolaylı ihlali** olurdu.
3. **Gerekçe 'deprecated' değil mimari (§1.3 dürüst düzeltmesi):** MyISAM MySQL 9.x'te **hâlâ RefMan'da** ve **deprecated değildir**; deprecated olan **mixed-engine transaction** desenidir (relnotes 9.0.0). Bu yüzden bu dosya **"yasak" dili kullanmaz** — red'i ayakta tutan: (i) transaction yok (dizin gerekçesi), (ii) tablo kilidi (RefMan 10.11.1), (iii) crash recovery zayıflığı, (iv) FULLTEXT avantajının **2013'te** InnoDB'ye geçmesi, (v) MySQL 9.0'ın mixed-engine transaction'ı deprecated etmesi.
4. **Erişim katmanı da transaction bekliyor:** ADR-002 (PDO, ORM yasak) `:24` — PDO transaction API'si (`beginTransaction/commit/rollback`) MyISAM'da **sessizce başarısız olur** (motor transaction kabul etmez) → sessiz veri tutarsızlığı riski, red'in en somut gerekçesi.
5. **Kapsam dürüstçe sınırlı (R-006/R-007 dersi):** red **yalnız MyISAM'ı** reddeder; InnoDB'nin her workload'da **en hızlı** olduğu **iddia edilmez** (benchmark UNKNOWN — §1.3-5); salt-okunur log/arşiv istisnası **bu dosya tarafından adıyla reddedilmez** → §2.3 kapısına taşındı.
6. **Reddedilen teknolojinin izi 0:** şemada 0, kodda 0 (§1.1/5-6) → "hiç denenmedi" beyanı **kanıtlıdır** (R-001 deseni).

### 2.2 Teknik Detaylar

- **Reddedilen yüzey (MyISAM):** `CREATE TABLE ... ENGINE=MyISAM` · `ALTER TABLE ... ENGINE=MyISAM` (dönüşüm) · `.frm/.MYD/.MYI` MyISAM dosya biçimi · `myisamchk`/`mysqlcheck` onarım akışı (InnoDB'de `innodb_force_recovery`/redo log yerine) · MyISAM'a özgü `DELAY_KEY_WRITE`, `PACK_KEYS`, `myisam_recover_options` ayarları · **mixed-engine transaction** (InnoDB tablo + MyISAM tablo aynı transaction'da — MySQL 9.0'da deprecated) · "FULLTEXT için MyISAM" argümanı (InnoDB 5.6'dan beri FULLTEXT destekliyor) · tablo kilidi sayesinde "hızlı okuma" argümanının **eşzamanlı yazma içeren tablolarda** kullanımı.
- **İzinli yüzey (yerini alan uygulama):** (a) **18 BCNF şeması InnoDB** (ADR-003 + `ENGINE=InnoDB` 165 satır); (b) **tek motor otoritesi** = şema dosyaları + ADR-040 sahiplik matrisi (tek yazar); (c) **erişim = PDO** (ADR-002) + transaction API'si InnoDB'de çalışır; (d) **migration/online DDL** InnoDB odaklı (ADR-014: expand-contract, online DDL); (e) **şema kuralları** (ADR-041: adlandırma, veri tipi, view/trigger, audit, N+1) + FULLTEXT gereksinimi çıkarsa **InnoDB FULLTEXT** kullanılır (resmî §14.9.5); (f) DB-içi FK'lar InnoDB'de tanımlanır, DB-arası FK **yok** (ADR-003:136).
- **Kod/şema yüzeyi ölçümü (2026-10-07):** `.ai/sources/.sql/mysql/` **19 .sql** → `ENGINE=InnoDB` **165** · `ENGINE=MyISAM` **0** · `myisam` **0** · repo geneli (19.660 dosya) `MyISAM` **2 isabet = ikisi de vault dizin satırı** (`index.md:142`, `wiki/vault-decisions.md:171`) → **MyISAM yüzeyi 0**.
- **InnoDB'nin bedeli (dürüst — §4.2):** InnoDB satır kilidi + MVCC + redo log için **daha fazla bellek/disk** kullanır (buffer pool, ibdata, redo) ve **çok küçük, salt-okunur, tek-yazıcı** tablolarda MyISAM **ölçümsüz** bir avantaj sunabilir → CoreMusic'te bu karşılaştırma **yapılmadı (UNKNOWN)** → red **performans** gerekçesiyle değil, **işlev** gerekçesiyle savunulur.

### 2.3 Yeniden Değerlendirme Koşulu (şart satırı)

> **Bu red yalnız aşağıdaki koşullardan BİRİ yazılırsa yeniden değerlendirilir; aksi hâlde yürürlükte kalır:** (1) **Yeni bir ADR talebiyle** (ör. "salt-okunur, tek-yazıcı, transaction gerektirmeyen log/arşiv/analitik tablosu için MyISAM istisnası") — talep **Data Engineer + Backend Architect + Vault Steward** raporuyla **ölçüm** ister: (a) tablo **yazma trafiği** (INSERT/dk), (b) **eşzamanlı okuyucu** sayısı, (c) **crash sonrası kayıp kabulü** (MyISAM'da son flush'tan sonraki satırlar kaybolabilir — ROLLING_BACK yok), (d) InnoDB ile **ölçülen** boyut/bellek farkı — **ölçüm yoksa istisna kurulamaz**; (2) **şema ADR'leri (ADR-003/040/041) yeni ADR ile değiştirilirse** (metinler düzenlenmez — `superseded by` ile bağlanır; debate ile) — kapı yalnız "izole, salt-okunur, FK'sız, transaction'sız tablo" kapsamıyla açılır; (3) **MySQL resmî olarak MyISAM'ı deprecated/removed ederse** (RefMan 9.x/10.x release notes) → red **kendiliğinden** güçlenir, geri açılmaz; ama **tersi** (MyISAM'ın resmî olarak tekrar önerilmesi) tek başına **yeterli değildir** — §2.3/1 ölçümü şarttır; (4) **debate (✅ TAMAMLANDI — 19/1/0) sonucu red'i kuran koşulların değiştiğini** kanıtlarsa → yeni debate + yeni ADR ile yeniden açılır. **Bugün: 1 = SAĞLANMADI (ölçüm yok — COREMUSIC MyISAM benchmark **UNKNOWN**), 2 = SAĞLANMADI (ADR-003/040/041 diskte, yürürlükte), 3 = TERSİ YAŞANMADI (MySQL 9.x hâlâ MyISAM içeriyor → red mimari gerekçeyle duruyor), 4 = debate ✅ TAMAMLANDI (3 tur / 20 persona, **19/1/0 RED DOĞRULANDI**) → koşul SAĞLANMADI (red değişmedi; 4 bağlayıcı şart §5.4'e bağlandı) → red geçerli.**

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **MyISAM (tamamen)** | Basit yapı; read-mostly'de hızlı olabilir (RefMan 10.6); tablo kilidi = düşük kilit overhead'i; `myisamchk` ile hızlı onarım | **Transaction/ACID yok** (dizin gerekçesi); **tablo kilidi** → eşzamanlı yazan 2. istemci bekler; **FK yok** → BCNF şeması DB-içi FK'larını yazamaz; **crash recovery zayıf** (son flush sonrası satırlar kaybolabilir); FULLTEXT avantajı **kalmadı** (InnoDB 5.6+); MySQL 9.0 mixed-engine transaction'ı deprecated etti | Dizin `:142` gerekçesi (**Transaction eksik**) + ADR-003 `:84/:136` (BCNF + FK) + ADR-040 `:33/:40` (18 DB otorite) — red'in **doğrudan hedefi** |
| 2 | **InnoDB (bugünkü uygulama)** | Transaction/ACID; satır kilidi (eşzamanlılık); FK + BCNF desteği; crash recovery (redo log); MVCC okuma; FULLTEXT desteği | Bellek/disk overhead (buffer pool, redo); küçük salt-okunur tablolarda **ölçülmemiş** dezavantaj olabilir (UNKNOWN) | **Reddedilmedi — bu, yerini alan yaklaşımdır** (165 `ENGINE=InnoDB` + ADR-003/040/041); bedeli §4.2'de yazılı, örtbas edilmedi |
| 3 | **Mixed-engine (InnoDB şemasına MyISAM serpiştirme)** | Yalnız "sorunlu" tabloda MyISAM avantajı sanısı | MySQL 9.0'da **deprecated** (mixed transactional/nontransactional transaction); transaction bütünlüğü **sessiz bozulur**; bakım = iki motor | **REDDEDİLDİ** — relnotes 9.0.0 deprecation + ADR-002 PDO transaction beklentisi (§2.1/4); "az motor, tek otorite" ilkesi (ADR-040) |
| 4 | **Diğer depolama motorları (MEMORY, ARCHIVE, BLACKHOLE, NDB, federated)** | MEMORY = RAM hızı; ARCHIVE = sıkıştırılmış salt-okunur | Hepsi **başka işlev** (geçici/seri/arşiv); ARCHIVE transaction'sız + FK'sız; NDB/federated = ağ topolojisi karmaşası (ADR-003 `:165` federated reddi) | Bu red'in **kapsamı dışındadır** — disk'te bunları reddeden ayrı satır **yok**; talep **ayrı debate + yeni ADR** ister (R-007 kapsam dersi) |
| 5 | **Farklı DBMS (PostgreSQL/SQLite/TimescaleDB)** | Farklı işlev seti (ör. PostgreSQL kısmi indeks) | 18 şema MySQL + PDO + MySQL 9 hedefi (ADR-002 `:24`) ile **çakışır**; DBMS değişimi proje ölçeğindedir | Bu red **DBMS'i reddetmez**; DBMS kararı ADR-002/003 kapsamındadır (düz metin — §2.1/5 kapsamı) |

*(İzinli uygulama alternatifi — InnoDB — §3'te "reddedilmedi" olarak ayrılmadı; o, yerini alan yaklaşımdır ve bu red'in gerekçe kaynağıdır. Farklı motorlar satırı da "kapsam dışı" olarak durur: red onları adıyla yasaklamaz, ayrı ADR ister.)*

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Motor kararı tek yerde toplandı:** `grep MyISAM` → bu dosya + 2 dizin satırı (`index.md:142`, `wiki/vault-decisions.md:171`); InnoDB kanıtı = 165 şema satırı + 8 ADR (ADR-014/050/072/073/074/075/078/079) → "neden MyISAM yok" sorusunun yanıtı vault'ta yazılı.
- **Yanlış iddia önlendi:** "MyISAM MySQL 9'da deprecated/kaldırıldı" **yazılmadı** — RefMan 9.7 §18.2 + bytebase bunu **çürütüyor** (§1.3-1/2); red gerekçesi **mimariye** sabitlendi (transaction + FK + crash + tablo kilidi) → Zero-Hallucination korundu.
- **BCNF kararlarıyla çapraz kilitlendi:** ADR-003/040/041 + ADR-002 (PDO transaction) + ADR-014 (online DDL) → motor tercihi **tek başına** değil, **3+2 ADR'nin** gereği olarak duruyor.
- **Geri dönüş temiz:** MyISAM hiç kurulmadı → `git revert` edilecek değişiklik **0** (§1.1/5-6, §5.2).
- **İstisna kapısı tanımlandı:** salt-okunur log/arşiv tablosu talebi **§2.3/1 ölçüm kapısına** bağlandı → "MyISAM daha hafif" argümanı **ölçülebilir** hâle getirildi.
- **Dizin satırı artık kaynağa sahip:** `index.md:142` "NO FILE on disk" iddiası fiilen geçersiz (bu dosya kaynaktır) → bayrak temizliği §5.1/3'te ertelendi, §7.1/1'de raporlandı.

### 4.2 Olumsuz Sonuçlar

- **"Yasak" dili yok — savunma daha zayıf:** MyISAM **deprecated değil** (§1.3) → yeni biri "ama MySQL 9 hâlâ MyISAM destekliyor, neden kullanmayalım?" der; yanıt bu dosyanın **işlevsel** gerekçesi olur (transaction/FK/crash), resmî yasak **olmadığı için** savunma **kanıt ister** (§2.3/1 ölçüm kapısı).
- **InnoDB bedeli örtbas edilmedi:** buffer pool + redo log overhead'i gerçek; salt-okunur büyük tablolarda InnoDB **ölçümsüz** daha ağır olabilir → bu karşılaştırma **yapılmadı (UNKNOWN)** (§4.3/4).
- **Şema ADR'lerinde motor adı yok:** ADR-003/040/041'de `InnoDB` = **0** (§1.1/4) → motor kararı bu üç ADR'de **dolaylı**; biri "ADR-003'te InnoDB yazıyor mu?" derse yanıtp **hayır** — kanıt 165 şema satırıdır (dürüst işaretlendi → §7.1/4).
- **MySQL sürümü vault'ta dağınık:** ADR-002 "MySQL 9", ADR-074 "9.3", ADR-014 "9.7" → kesin hedef sürüm **UNKNOWN** (§1.3-3 açık).
- **Benchmark yok:** InnoDB vs MyISAM **performans** karşılaştırması CoreMusic'te yapılmadı → "InnoDB her yerde en hızlı" iddiası **kurulamaz** (yalnız işlev gerekçesi geçerli).
- **Dizin/seri tutarsızlığı sürüyor:** `rejected/index.md` boş (12 red'in hiçbiri satırlanmadı) + `index.md:142` NO FILE bayrağı duruyor → §7.1/1-3.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **"MyISAM deprecated" iddiasının vault'a sızmaması** (birinin MySQL 9'da yasak zannetmesi) | 3 (olası) | 3 (orta — yanlış karar gerekçesi) | §1.3-1/2 + §2.1/3: "deprecated **değil**" beyanı **açıkça** yazıldı; red yalnız işlevsel gerekçe taşır (debate F1 benzeri şart §5.3/1) |
| **Transaction'sız yazma hatası** (MyISAM'a yanlışlıkla `ENGINE=MyISAM` eklenirse PDO `commit` sessiz başarısız olur) | 2 (düşük — 165/0 kanıtı) | 4 (yüksek — veri tutarsızlığı) | §5.1/2 CI/statik kapısı: `ENGINE=MyISAM` = **0** taraması her şema değişikliğinde; ADR-002 PDO transaction sözleşmesi; ADR-040 tek-yazar kuralı |
| **Crash sonrası veri kaybı** (MyISAM flush edilmemiş satırlar — journal yok) | 2 (düşük — MyISAM yok) | 4 (yüksek — kayıp) | Mevcut durumda **yok** (165/0); §2.3/1 istisna talebinde **kayıp kabulü** maddesi zorunlu (c) |
| **"Okumada MyISAM daha hızlı" dış baskısı** (RefMan 10.6/10.11.1 read-mostly önerisi) | 3 (olası) | 2 (düşük) | §1.3-5 + §2.3/1: okuma avantajı **ölçülmeden** geçerli sayılmaz; InnoDB FULLTEXT + index kapsamı + buffer pool zaten okumayı hızlandırır (**ölçüm UNKNOWN → iddia kurulmaz**) |
| **Benchmark boşluğu** — InnoDB'nin MyISAM'a göre yavaş olduğu iddiası/tersi **hiç ölçülmedi** | 4 (çok olası) | 2 (düşük) | §5.1/6 ölçüm adımı (sysbench/tpcc benzeri salt-okunur senaryo) — sayı gelmeden **iki yönde de** iddia yazılmaz (`⚠️ VERIFICATION REQUIRED`) |
| **Mixed-engine geri dönüşü** (InnoDB şemasına MyISAM serpiştirme önerisi) | 2 (düşük) | 3 (orta) | §3/3 + §2.3/2: ayrı debate + yeni ADR; MySQL 9.0 mixed-engine deprecation dayanağı |

---

## 5. Uygulama (Implementation)

> Bu kayıt **salt-okunur seri**dir (`rejected/`); "uygulama" = kaydın vault'a doğru yerleştirilmesi ve denetimidir — kod değişikliği **yoktur**.

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu dosyayı `.ai/.decisions/rejected/R-008-mysql-myisam-engine.md` olarak yaz (şablon §1-§7, 7 bölüm dolu) + `log.md` append ("R-008 yazıldı (debate PENDING)") | Vault Steward | 25 dk |
| 2 | **CI/statik kapı:** her şema commit'inde `.ai/sources/.sql/mysql/*.sql` içinde `ENGINE=MyISAM` = **0** doğrulanır (bugün: 165 InnoDB / 0 MyISAM) + üretim kodunda `MyISAM` = **0** korunur | Data Engineer + QA | her sprint |
| 3 | **`index.md:142` `<!-- NO FILE on disk ... -->` bayrağı → DOKUNULMADI** (talimat gereği son sıfırlamaya ertelendi — rapor-only) · `wiki/vault-decisions.md:171` aynı satır → **DOKUNULMADI** (rapor-only) · `rejected/index.md` § tablosu **BOŞ → DOKUNULMADI** (rapor-only) | Vault Steward | son sıfırlama |
| 4 | **Debate** (3 tur / 20 persona — **✅ TAMAMLANDI 2026-10-07, 19/1/0 RED DOĞRULANDI**; kayıt §7, bağlayıcı şartlar §5.4) + Tech Lead onayı **✅** | MO + Tech Lead | tamamlandı |
| 5 | Sayfa-içi derin doğrulama turu: MySQL 9.x release notes (mixed-engine deprecation tam metni) + RefMan §18.2 §10.6 §10.11.1 §14.9.5 + InnoDB/MyISAM benchmark sayıları (§1.3 başlık/özet düzeyi kaldı) | Researcher | üretim öncesi |
| 6 | **Ölçüm adımı (§2.3/1 kanıtı):** salt-okunur log/arşiv senaryosu için InnoDB vs MyISAM **ölçümü** (bellek, disk, INSERT/dk, okuma gecikmesi, crash sonrası kayıp) — sayı yoksa istisna kapısı **kapalı** kalır | Data + Performance | bir sonraki sprint |

### 5.2 Geri Dönüş Planı

Bu karar **kod tarafında geri alınacak bir şey üretmedi** (MyISAM hiç kurulmadı → `git revert` edilecek değişiklik **0**; §1.1/5-6). Geri dönüş = **yeniden değerlendirme** demektir ve yalnız §2.3 koşullarından biri yazılırsa yeni ADR ile açılır: (1) salt-okunur istisna **ölçümle** (§2.3/1) → yeni ADR; (2) ADR-003/040/041 yeni ADR ile değişirse → bu dosya `superseded` notuyla **bağlanır, düzenlenmez**; (3) MySQL resmî değişikliği (§2.3/3) → red **güçlenir**; (4) debate sonucu değişirse → debate kaydı + yeni ADR. Vault bozulursa standart kurtarma `git checkout` + son commit (AGENTS.md §17 #10). **Reddedilen teknolojinin şemada/kodda izi olmadığı için kullanıcı/veri etkisi YOKTUR.**

### 5.3 Debate Şartları

**Kayıt:** ✅ **TAMAMLANDI (2026-10-07)** — debate **çalıştırıldı** (3 tur / 20 persona, **19/1/0 RED DOĞRULANDI**; durum `rejected` **kullanıcı onaylı red** olarak zaten kayıtlıdır; debate sonucu red'i **değiştirmedi**, yalnız **4 bağlayıcı şart** bağladı → **§5.4**). Aşağıdaki "Planlanan akış / Planlanan bağlayıcı şartlar" maddeleri **debate öncesi plandır**; uygulanan sonuç **§5.4** + **§7**'dedir.

**Planlanan akış:**

- **Tur 1 (20 persona — bulgu):** şemada `ENGINE=InnoDB` **165 / MyISAM 0** (§1.1/5) · kodda MyISAM **0** (19.660 dosya) · red kaynağı `index.md:142` (NO FILE bayrağı **dokunulmadı** → §5.1/3) · yerini alan **3/3 diskte** (ADR-003/040/041) + 2 destek (ADR-002/014) · ADR-003/040/041'de `InnoDB` = **0** (dürüst etiket) · 5 sorgu / ~26 kaynak · MyISAM MySQL 9.x'te **deprecated değil** · `rejected/index.md` boş → dokunulmadı.
- **Tur 2 (itiraz → çözüm → şart):** (i) "MySQL 9'da MyISAM deprecated" iddiası **çürük** (RefMan 9.7 + bytebase) → gerekçe mimariye → **Şart 1 yasak-dili yasağı**; (ii) "read-mostly'de MyISAM öneriliyor" (RefMan 10.6/10.11.1) → kapsam daraltma + ölçüm → **Şart 2 istisna ölçüm kapısı** (§2.3/1); (iii) "InnoDB benchmark'ı yok" → UNKNOWN, iki yönde de iddia yasağı → **Şart 3 ölçüm/iddia kapısı**; (iv) "şema ADR'lerinde InnoDB adı yok" → kanıt = 165 satır → **Şart 4 kanıt-öncelik kaydı**.
- **Tur 3 (oy):** sonuç + bağlayıcı şartlar bu §5.4'e ve §7'ye yazılacak (**✅ yazıldı — §5.4 + §7, 19/1/0 RED DOĞRULANDI**).

**Planlanan bağlayıcı şartlar (debate öncesi plan):**

1. **Şart 1 — Yasak-dili yasağı:** bu dosya **MyISAM'ın "deprecated/kaldırılmış" olduğunu iddia edemez** (§1.3-1/2 kanıtı); red yalnız **işlevsel** gerekçeyle (transaction/FK/crash/tablo kilidi) yazılır.
2. **Şart 2 — İstisna ölçüm kapısı:** salt-okunur log/arşiv tablosu istisnası yalnız §2.3/1 ölçümüyle (yazma trafiği + eşzamanlı okuyucu + kayıp kabulü + InnoDB farkı) tartışılır; ölçüm yoksa kapı kapalı.
3. **Şart 3 — Performans iddia yasağı:** "InnoDB her yerde en hızlı" / "MyISAM okumada daha hızlı" **iki yönde de** ölçülmüş sayı olmadan yazılmaz → `⚠️ VERIFICATION REQUIRED`.
4. **Şart 4 — Kanıt-öncelik:** motor iddiasının kanıtı **şema dosyaları (165/0)** + **şema ADR'leri**dir; ADR-003/040/041'de `InnoDB` adı **geçmez** — bu boşluk gizlenmez, açıkça yazılır.

### 5.4 Debate Sonucu — Bağlayıcı Şartlar (2026-10-07 · 19/1/0 RED DOĞRULANDI)

> Debate **tamamlandı**: 3 tur / 20 persona (AGENTS.md §6.1 — Expert 5 · Senior 5 · Junior 10; **kanıt-öncelikli**; tek seviyeli karar bağlayıcı değil, en az 1 Expert + 1 Senior + 1 Junior görüşü zorunlu). Sonuç **19/1/0 = RED DOĞRULANDI** (Tur 3 uzlaşması: Expert **data-engineer** kanıtı üstün — 165/0 şema sayımı + işlevsel gerekçe). Tur kaydı **§7**; debate öncesi plan **§5.3**. Aşağıdaki 4 şart **bağlayıcıdır**.

1. **Şart 1 — Kanıt zinciri sabitleme:** "InnoDB zorunluluğu" ifadesi ADR-003/040/041'de **geçmez** (§1.1/4, §7.1/4) → motor iddiasının kanıt zinciri **şema satırlarına** (`.ai/sources/.sql/mysql/` → 165 `ENGINE=InnoDB`, 0 `MyISAM`) + **8 şema ADR'sine** (ADR-014/050/072/073/074/075/078/079 = 35 isabet) **sabitlenir**; bu adla başka yerde kanıt **uydurulmaz** — doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` yazılır.
2. **Şart 2 — İşlevsel-red tutarlılığı:** MyISAM MySQL 9.x'te **deprecated değildir** (§1.3-1/2) → ADR boyunca **"yasak değil, işlevsel red"** ifadesi **tutarlı** tutulur; red gerekçesi transaction + FK + crash dayanıklılığı + tablo kilidi + FULLTEXT (InnoDB 5.6) avantaj kaybıdır; "deprecated/kaldırıldı" dili **kullanılmaz**.
3. **Şart 3 — Bağımsız kaynak / ⚠️:** karşılaştırma niteliğindeki **19 kaynak** vendor ağırlıklı gelir → işlevsel iddialar **bağımsız kaynakla** desteklenir ya da `⚠️ VERIFICATION REQUIRED` etiketi taşır; kaynaksız / tek-vendor iddia **kanıt sayılmaz**.
4. **Şart 4 — Salt-okunur istisna kapısı:** MyISAM için **tek** yeniden değerlendirme yolu §2.3/1'dir; kapı yalnız **append-only + transaction gerekmeyen** tablolarda (salt-okunur log/analitik) **ölçümle** açılır (yazma trafiği, eşzamanlı okuyucu, kayıp kabulü, InnoDB farkı) — ölçüm yoksa **kapalı** kalır.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../index]] | Karar dizini — bu red'in kaydı (`:142`, slug + "Transaction eksik" + NO FILE bayrağı §5.1/3) |
| [[../accepted/ADR-003-multi-db-bcnf]] | **Yerini alan (birincil):** 18 domain DB + BCNF + DB-içi FK / DB-arası FK yasağı (`:84`, `:129`, `:136`) — MyISAM'da FK **yok** → BCNF şeması MyISAM'da **kurulamaz** |
| [[../accepted/ADR-040-database-authority]] | **Yerini alan:** 18 DB sahiplik matrisi + tek yazar + cross-DB politikası (`:33` 18 `.sql` IMPLEMENTED, `:40` FK metni) — tek-motor disiplini bu otoritenin parçası |
| [[../accepted/ADR-041-database-normalization-supplementary]] | **Yerini alan:** şema adlandırma + veri tipi + view/trigger/procedure + audit + N+1 kuralları — motor kararını tamamlar |
| [[../accepted/ADR-002-pdo-mandatory-no-orm]] | Erişim katmanı PDO + MySQL 9 + 18 BCNF (`:24`) — PDO transaction API'si MyISAM'da **sessizce başarısız olur** (§2.1/4) |
| [[../accepted/ADR-014-multi-db-migration-strategy]] | Online DDL + expand-contract (`:61` RefMan 8.4/9.7 §17.12.1 + §15.1.9) — InnoDB odaklı migration; MyISAM'da locking modeli farklı |
| [[../accepted/ADR-072-social-database-schema]] | InnoDB + FK örneği (`:55` "InnoDB partition kuralı (FK yasağı + partition anahtarı)") — DB-içi FK/partition InnoDB gereksinimi (FK örneği ADR-040 `:53` üzerinden) |
| [[../accepted/ADR-079-i18n-database-schema]] | `:132` "`coremusic_system.sql` … **InnoDB** … utf8mb4_unicode_ci" — tek tek şema dosyalarında motor kanıtı |
| [[../../sources/.sql/mysql/coremusic_system.sql]] | **Disk kanıtı:** `ENGINE=InnoDB` şema dosyası (19/19 dosyanın temsilcisi; 165 InnoDB / 0 MyISAM sayımı §1.1/5) |
| [[../../wiki/vault-decisions]] | `:171` — `R-008-mysql-myisam-engine` satırının aynası (ayrıca dokunulmadı → §7.1/2) |
| [[../../raw/brain]] | Mimari özet defteri (`.ai/raw/brain.md` — diskte `.ai/raw/brain.md` **YOK**, §7.1/7) |
| [[../../CLAUDE]] | Kural metinleri — Zero-Hallucination + onay kapıları |
| [[../../index]] | Master katalog — ADR kayıtları |
| [[../../log]] | Append-only kayıt defteri (bu işlem 1 satır append → §7.1/8) |
| [[../../.templates/adr/adr-template]] | Guardrail #16 — bu dosyanın §1-§7 iskeleti + §1.3 9 alan kaynağı (**gerçek yol `.ai/.templates/adr/` — §7.1/6**) |
| Dizin satırı | `index.md:142` — slug otoritesi + NO FILE bayrağı (§5.1/3) |
| Debate şartları | Bu dosya **§5.4** (bağlayıcı şartlar 1-4 — debate ✅ **TAMAMLANDI**, **19/1/0 RED DOĞRULANDI**); debate öncesi plan **§5.3**, tur kaydı **§7** |
| [[R-001-redux-style-state-management]] | Seri kardeşi — aynı salt-okunur red kayıt formatı |
| [[R-002-mongodb-document-store]] | Seri kardeşi — aynı salt-okunur red kayıt formatı (kanıt tablosu/dürüst etiket deseni) |
| [[R-003-jquery-ui-framework]] | Seri kardeşi — format referansı (§1.3 9 alan, §7.1 rapor deseni) |
| [[R-004-webpack-bundle-system]] | Seri kardeşi — format referansı (§7.1 rapor deseni) |
| [[R-005-rest-only-api]] | Seri kardeşi — format referansı (künye, §1.1 kanıt tablosu, §2.3 şart satırı, §7.1 rapor) |
| [[R-006-laravel-eloquent-orm]] | Seri kardeşi — format referansı (kapsam daraltma dersi §2.1/5 bu dosyadan uygulandı) |
| [[R-007-firebase-authentication]] | Seri kardeşi — format referansı (satır-no düzeltme dersi: 134→**142**, §7.1/1; kapsam ayrımı dersi) |
| Düz metin | Eski seri R-009…R-012 (`rejected/index.md` tablosu boş → §7.1/3) · PostgreSQL/SQLite/MEMORY/ARCHIVE/NDB/federated (disk'te reddeden satır yok → §3/4-5) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-10-07 | ✅ |
| Tech Lead | — | 2026-10-07 | ✅ |
| Arch Lead | — | — | ⏳ |

**Debate kaydı:** ✅ **TAMAMLANDI (2026-10-07)** — 3 tur / 20 persona (AGENTS.md §6.1: Expert 5 · Senior 5 · Junior 10; **kanıt-öncelikli**; tek seviyeli karar bağlayıcı değil → en az 1 Expert + 1 Senior + 1 Junior görüşü zorunlu). Sonuç: **19/1/0 — RED DOĞRULANDI**; durum **rejected** (kullanıcı onaylı red) olarak kalır — debate sonucu red'i **değiştirmedi**, yalnız **4 bağlayıcı şart** bağladı (**§5.4/1-4**).

**Tur 1 — bulgu (kaynaklı, 20 persona):** `index.md:142` gerçek satır = **142** (görev tahmini **134** yanlıştı) — satır `R-008-mysql-myisam-engine | MyISAM | Transaction eksik <!-- NO FILE on disk 2026-10-06 -->` → bayrak **dokunulmadı**; aynı satır `wiki/vault-decisions.md:171`'de de **dokunulmadı** (§7.1/1-2) · motor sayımı `.ai/sources/.sql/mysql/` (19 dosya) → `ENGINE=InnoDB` **165** / `MyISAM` **0** / `myisam` **0** — görevdeki `.ai/.sql/mysql/` yolu diskte **YOK** (§7.1/5) · repo taraması **19.660 dosya** → `MyISAM` **2 isabet, ikisi de dizin satırı** → **üretim 0** · yerini alan **3/3 diskte** (ADR-003-multi-db-bcnf · ADR-040-database-authority · ADR-041-database-normalization-supplementary + destek ADR-002, ADR-014) · dürüst etiket: bu üç ADR'de **InnoDB = 0 / MyISAM = 0** → "InnoDB zorunluluğu" diskte **bu adla geçmiyor**; kanıt **165 şema satırı + 8 şema ADR'si** (ADR-014/050/072/073/074/075/078/079 = 35 isabet) (§1.1/4, §7.1/4) · MySQL 9 düzeltmesi: MyISAM RefMan 9.2/9.6/9.7 §18.2'de **hâlâ var, deprecated değil**; deprecated olan **mixed-engine transaction** (relnotes 9.0.0) → red **işlevsel** yazıldı (transaction / FK / crash / tablo kilidi / FULLTEXT 5.6 avantaj kaybı) · **5 sorgu / ~26 benzersiz kaynak** (resmî+vendor 12 + karşılaştırma 19) · wiki-link **33 / 22, kırık 0** (diskte olmayan `../../brain`, `../../keys` **kurulmadı** → `[[../../raw/brain]]` + düz metin) · `rejected/index.md` **boş → dokunulmadı** (§7.1/3). **Dağılım:** Expert **4 kabul + 1 kırmızı** (data-engineer: kanıt zinciri şart) · Senior **1 kabul + 1 uyarı** · Junior **8 neutral + 2 uyarı**.

**Tur 2 — çapraz eleştiri (4 itiraz → çözüm → şart):** (1) "InnoDB zorunluluğu" ADR'lerde **geçmiyor** → kanıt zinciri **şema satırlarına + 8 şema ADR'sine** sabitlendi (uydurma yok) → **Şart 1**; (2) MyISAM **deprecated değil** → "yasak değil, işlevsel red" ifadesi ADR boyunca **tutarlı** kılınacak → **Şart 2**; (3) karşılaştırma **19 kaynak** vendor → **bağımsız kaynak ya da ⚠️** → **Şart 3**; (4) **salt-okunur log/analitik istisnası** → yeniden değerlendirme kapısı (yalnız **append-only + transaction gerekmeyen** tablo) → **Şart 4**.

**Tur 3 — uzlaşma (kanıt-öncelikli):** Expert **data-engineer** kanıtı üstün (**165/0** + işlevsel gerekçe) → **RED DOĞRULANDI (19/1/0)**; 4 şart **§5.4**'e bağlayıcı olarak yazıldı — red **değişmedi**.

| Tur | Katılım | Çıktı |
|-----|---------|-------|
| **1 — bulgu** | **20/20** (E5 · S5 · J10) | **Doğrulandı:** `index.md:142` gerçek satır (tahmin **134** yanlış) · NO FILE bayrağı **dokunulmadı** (`wiki/vault-decisions.md:171` dahil) · şema **165 InnoDB / 0 MyISAM** (`.ai/.sql/mysql/` yolu **YOK** → gerçek `.ai/sources/.sql/mysql/`) · kodda MyISAM **0** (2 isabet = dizin satırı) · yerini alan **3/3 diskte** (ADR-003/040/041) + 2 destek (ADR-002/014) · ADR-003/040/041'de `InnoDB` = **0** (dürüst etiket) · 5 sorgu / ~26 kaynak · MyISAM MySQL 9.x'te **deprecated değil** · `rejected/index.md` boş → dokunulmadı · dağılım: Expert **4 kabul + 1 kırmızı**, Senior **1 kabul + 1 uyarı**, Junior **8 neutral + 2 uyarı** |
| **2 — itiraz→çözüm** | **20/20** | **4 itiraz → 4 bağlayıcı şart** (§5.4): (1) "InnoDB zorunluluğu" ADR'lerde geçmiyor → kanıt zinciri şema satırı + 8 şema ADR'si · (2) deprecated değil → işlevsel-red tutarlılığı · (3) 19 karşılaştırma kaynak vendor → bağımsız kaynak / ⚠️ · (4) salt-okunur istisna → ölçüm kapısı (yalnız append-only + transaction gerekmeyen tablo) |
| **3 — oy** | **20/20 · 19/1/0** | **RED DOĞRULANDI** — Expert data-engineer kanıtı üstün (165/0 + işlevsel gerekçe); 4 şart §5.4'e bağlandı; **RED DEĞİŞMEDİ** → yeni debate + yeni ADR gerekmedi |

> **Sonuç:** R-008 **REDDEDİLDİ (kullanıcı onaylı) — debate ✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 RED DOĞRULANDI)** · **Tech Lead:** ✅ (2026-10-07) · **Arch Lead:** ⏳ · bağlayıcı şartlar **§5.4/1-4** (debate öncesi plan **§5.3/1-4**).

### 7.1 Rapor Notları (bu işlemde dokunulmayanlar)

1. **Dead-link/NO FILE bayrağı:** `index.md:142` `<!-- NO FILE on disk 2026-10-06 -->` **olduğu gibi bırakıldı** — artık `no source/NO FILE` iddiası **geçersizdir** (bu dosya kaynaktır) → temizlik son sıfırlamaya ertelendi, burada rapor edildi. **Görev tahmini 134 satırı YANLIŞTı; gerçek satır = 142** (grep 2026-10-07 — R-007'deki 132→133 düzeltmesinin aynısı).
2. **Aynası satır:** `wiki/vault-decisions.md:171` aynı `R-008-mysql-myisam-engine` satırını taşıyor (aynı NO FILE bayrağıyla) → **dokunulmadı**, rapor-only (inceleme: wiki sayfası § dizini de 12 red'i bekliyor).
3. **`rejected/index.md` durumu:** dosya **VAR** (v1.0.1, `total: 12`) ama tablo **BOŞ** (başlık satırları, kayıt satırı yok) → bu işlemde **oluşturulmadı ve doldurulmadı** (dizin satırı düzenleme yetkisi kapsam dışı) → rapor-only. Seri **R-001…R-007** dosyalara karşılık tabloda **0 satır** var.
4. **Şema ADR'lerinde motor adı yok (dürüst):** ADR-003/040/041'de `InnoDB` = **0**, `MyISAM` = **0** isabet → "18 şema InnoDB'dir" iddiası **şema dosyalarından** (165/0) ve **şema ADR'lerinden** (ADR-014/050/072/073/074/075/078/079 → 35 isabet) okunur. Görevin "ADR-003/040 InnoDB zorunluluğu" ifadesi **disk kanıtında böyle geçmiyor** → bu dosya §1.1/4'te dürüstçe etiketledi (uydurma yok).
5. **SQL yolu düzeltmesi:** görev `.ai/.sql/mysql/*.sql` der; o yol diskte **YOK** (`Test-Path .ai\.sql` = **False**); gerçek yol **`.ai/sources/.sql/mysql/`** (19 `.sql` dosyası). ADR-003/040/079'daki `.ai/.sql/mysql/` atıfları da bu **gerçek olmayan yolu** gösteriyor → rapor-only (değişiklik kapsam dışı; ADR'ler `accepted`/frozen dokunulmaz).
6. **Şablon yolu notu:** görev `templates/adr/adr-template.md` der; disk kanıtı `.ai/.templates/adr/adr-template.md` (glob = True, v2.0.2) — bu dosya o şablonun §1-§7 iskeletiyle (§1.3 9 alan, §7 Onay) **R-007 formatı** kullanılarak yazıldı.
7. **Wiki-link disk doğrulaması:** bu dosyadaki tüm wiki-link'ler **çalışma anında diskte doğrulandı** (`.ai/raw/brain.md` = **False** → `.ai/raw/brain.md` / `.ai/raw/keys.md`'e wiki-link **kurulmadı**, `[[../../raw/brain]]` kullanıldı; `.ai/raw/keys.md` = **False** → keys düz metin olarak bırakıldı); `rejected/` kardeşleri R-001…R-007 = 7/7 diskte; `accepted/` hedefleri ADR-002/003/014/040/041/072/079 = 7/7 diskte.
8. **Yazma arayüzü:** `.ai/sources/scripts/vault-utf8-writer.mjs` yolu diskte **YOK**; gerçek yol **`.ai/sources/scripts/vault-utf8-writer.mjs`** (bu dosya o script'le yazıldı). `log.md` append **tek satır**, byte-seviyesi (son byte `10` = `\n` zaten vardı → bölme gerekmedi).
9. **Kaynak derinliği:** §1.3 ~26 benzersiz kaynak **başlık/özet düzeyinde** derlendi (sayfa-içi tur yok) → §5.1/5'e bırakıldı; MySQL sürümü vault'ta **9.0/9.2/9.3/9.7** arasında dağınık → kesin hedef sürüm **UNKNOWN**; InnoDB/MyISAM **benchmark** CoreMusic'te yapılmadı → `⚠️ VERIFICATION REQUIRED` (§4.3/5, Şart 3).

---

*R-008 v1.0.0 | 2026-10-07 | Created + Debate tamamlandı — CoreMusic Vault (.decisions/rejected/ sıfırdan yazım, salt-okunur seri; debate ✅ TAMAMLANDI — 3 tur / 20 persona, 19/1/0 RED DOĞRULANDI, Tech Lead ✅, 4 bağlayıcı şart §5.4)*
