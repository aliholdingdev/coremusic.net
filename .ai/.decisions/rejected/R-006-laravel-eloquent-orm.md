---
title: "CoreMusic — R-006: Laravel Eloquent ORM (REDDEDİLMİŞ — ORM Yasak)"
type: "architecture-decision"
category: "database"
date: "2026-10-02"
updated: "2026-10-02"
version: "1.0.0"
status: "rejected"
authority: "SSOT — R-006 red kararı: CoreMusic veri erişiminde 'Laravel / Eloquent ORM (ActiveRecord + hydration + relation magic)' KABUL EDİLMEZ. Gerekçe: karar dizini index.md:131 'Eloquent ORM | ORM yasak' + ADR-002 (PDO zorunlu, ORM yasak — §3 alternatif 1 ret satırı) + .ai/CLAUDE.md:416/:517 (kural 9 'No ORM — Raw PDO only'). Yerini alan: ADR-002-pdo-mandatory-no-orm + ADR-014-multi-db-migration-strategy + ADR-033-sql-normalization-strategy + ADR-040-database-authority (dördü de diskte). Bu dosya salt-okunur seridir (rejected/) — değiştirilmez, yalnız referanslanır."
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 RED DOĞRULANDI)"
---

# CoreMusic — R-006: Laravel Eloquent ORM (Rejected)

> **Durum:** rejected (**debate ✅ TAMAMLANDI**) — **Tarih:** 2026-10-02 — **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona, 19/1/0 RED DOĞRULANDI)** — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Seri:** `.ai/.decisions/rejected/` (salt-okunur) — **Slug:** `R-006-laravel-eloquent-orm` (dizin otoritesi: [[../index]] **satır 131** — dosya adı ile birebir hizalı ✅; 2026-10-02 glob doğrulaması: `rejected/` içinde `R-006*` = **0 dosyaydı** → bu işlemde yazıldı)
> **Dizin satırı:** `| [[R-006-laravel-eloquent-orm]] <!-- dead-link: R-006-laravel-eloquent-orm no source 2026-09-24 --> | Eloquent ORM | ORM yasak |` — `<!-- dead-link ... -->` bayrağı **bu işlemde DOKUNULMADI** (temizlik son sıfırlamaya ertelendi → §5.1/3 + §7.1/1)
> **İlgili kararlar:** [[../accepted/ADR-002-pdo-mandatory-no-orm]] (yerini alan — PDO zorunlu + ORM yasak, uzun gerekçe §3/§4.4) · [[../accepted/ADR-014-multi-db-migration-strategy]] (yerini alan — özel PHP runner, ORM'siz) · [[../accepted/ADR-033-sql-normalization-strategy]] (yerini alan — BCNF birincil) · [[../accepted/ADR-040-database-authority]] (yerini alan — 18 DB otoritesi + tek yazar) · karar dizini [[../index]] §5.
> **R-001…R-005 dersi uygulandı:** wiki-link slug'ları **tahmin edilmedi** — 19 hedefin tamamı `Test-Path` ile doğrulandı (hepsi True).

---

## 1. Bağlam (Context)

CoreMusic'in veri erişim katmanı **ham PDO + prepared statement** üzerine kuruludur; karar dizini bu tercihi çoktan **tescil etmiştir** (`index.md:131` — "Eloquent ORM | ORM yasak") ama **red metni hiç yazılmamıştır**: elde yalnız dizin satırı + `<!-- dead-link ... -->` notu + ADR-002/CLAUDE/brain içindeki dağınık parmak izleri vardır. Bu dosya, o satırın **gerekçeli red kaydıdır** — yeni bir karar değil, mevcut red'in (a) gerekçe, (b) güncel web araştırması, (c) yerini alan eşleme, (d) yeniden değerlendirme koşulu ile sıfırdan yazımıdır.

### 1.1 Mevcut Durum (disk kanıtı — dürüst etiket, 2026-10-02 taraması)

| # | İddia | Kanıt | Etiket |
|---|-------|-------|--------|
| 1 | Red kaydı var mı? | [[../index]] `:131` → `[[R-006-laravel-eloquent-orm]]` + "Eloquent ORM" + "ORM yasak" + `<!-- dead-link ... no source 2026-09-24 -->` | ✅ **KAYITLI** (dizin satırı tek kanıt; red metni bu işlemde yazılıyor) |
| 2 | Bu işlem öncesi dosya var mıydı? | `.ai/.decisions/rejected/` içinde `CLAUDE.md`, `index.md`, `R-001-*`…`R-005-*` ; `R-006*` = **0 dosya** | ❌ **YOKTU** → bu işlemde yazılıyor (klasör zaten var — `rejected/` oluşturulmadı) |
| 3 | `R-006` grep isabeti (repo geneli, literal) | `index.md:131` (slug + dead-link) · `R-005:224` ("Eski seri R-006…R-012" düz metni) | ✅ **2 isabet** — ikisi de **red'in kendi metni değil**; kodda `R-006` = **0** |
| 4 | Red gerekçesi başka yerde yazılı mı? | `.ai/CLAUDE.md:416` ("ORM yasak (ADR-002)") · `:517` (kural 9 "No ORM — Raw PDO only (ADR-002)") · `:670` (karşılaştırma: "ORM (Eloquent, Doctrine) → Raw PDO") · `brain.md:143` ("Laravel Eloquent → ORM yasak (ADR-002) → PDO") · `.opencode/opencode.json:128` (Data Engineer kural 2: "ORM FORBIDDEN (No Doctrine, No Eloquent)") · `shared/AGENTS.md` §4 Yasak 3 ("ORM (ADR-002), var, eval") | ✅ **6 referans** — hepsi **tek cümlelik** kural satırı, hiçbiri red'in gerekçe metni değil |
| 5 | Uzun gerekçe diskte mi? | [[../accepted/ADR-002-pdo-mandatory-no-orm]] `:137` (§3 alternatif 1 — Eloquent/Doctrine ret satırı), `:140` (alternatif 4 — query builder **zorunlu kılınmadı, serbest**), `:174-184` (§4.4 zaruri istisna kapısı — 5 şart), `:201` (§5.1/6 debate şartı — N+1/query budget + `EXPLAIN`) | ✅ **DISKTE** (bağlayıcı gerekçe — sayfa başlığı birebir: "PDO Zorunlu, ORM Yasak") |
| 6 | Yerini alan kararlar diskte? | [[../accepted/ADR-002-pdo-mandatory-no-orm]] · [[../accepted/ADR-014-multi-db-migration-strategy]] (özel PHP **runner**, "Phinx/Doctrine composer'da yok") · [[../accepted/ADR-033-sql-normalization-strategy]] (BCNF) · [[../accepted/ADR-040-database-authority]] (18 DB otoritesi) — `status: accepted` (2026-10-02 `Test-Path` = True ×4) | ✅ **4/4 IMPLEMENTED** (glob + ön-oku ile doğrulandı — R-001 dersi) |
| 7 | Kod yüzeyi: ORM bağımlılığı? | `composer.json` ×5 (api/auth/home/media/shared) içinde `laravel\|illuminate\|eloquent\|doctrine/orm\|propel\|phinx\|medoo` = **0** · PHP/JS/TS içinde `Eloquent\|Illuminate\|laravel` = **0** (tek isabet `.opencode/opencode.json:128` = **agent kuralı metni**, kod değil) | ✅ **ORM YÜZEYİ 0** — Laravel kurulu değil, Eloquent kodda hiç yok |
| 8 | Kod yüzeyi: PDO ne kadar yaygın? | `->prepare(` üretim PHP = **12 isabet** (`shared/src/Database/DatabaseManager.php` 2 · `shared/src/OAuth/OAuthRepository.php` 6 · `media.coremusic.net/src/Media/CatalogWriter.php` 2 · `bin/api-key-create.php` 2) + test 2 + skill şablonu 1 = 15 tarama isabeti · `new PDO\|PDO::\|PDOStatement` üretim = **13 isabet** (toplam 25) | ✅ **ADR-002 UYGULANMIŞ** — tek bağlantı kapısı `DatabaseManager` (`ATTR_EMULATE_PREPARES=false`, `ERRMODE_EXCEPTION`) + repository'lerde prepared |
| 9 | Kod yüzeyi: query builder (ara katman)? | PHP `QueryBuilder\|->from(\|whereRaw\|queryBuilder` = **0 isabet** | ⚠️ **ARA KATMAN YOK** — ORM de yok, query builder da yok; tek katman **ham prepared SQL** (ADR-002 §3/4'ün "serbest bırakıldı" hükmü praktikte **kullanılmıyor**) |
| 10 | ADR-014 "özel SQL runner" diskte mi? | dosya adı `ADR-014-multi-db-migration-strategy.md`, H1: "Özel PHP Runner · DB-Başına Bağımsız Sequence · Expand-Contract · Forward-Only · Online DDL" (`:2`, `:14`) · §1.4 `:73` "Runner doğrudan PDO üzerinden; Phinx/Doctrine kurulmaz" | ✅ **DISKTE** (görevdeki "özel SQL runner" tanımı dosya içeriğiyle eşleşiyor) |
| 11 | `rejected/index.md` durumu? | Dosya **VAR** (v1.0.1, `total: 12`) ama § tablosu **BOŞ** (başlık satırları var, 12 red'in hiçbiri satırlanmamış) | ⚠️ **BOŞ** → bu işlemde **dokunulmadı** → §7.1/2 |
| 12 | Debate sonucu? | Debate **çalıştırıldı** — 3 tur / 20 persona | ✅ **RED DOĞRULANDI** (19/1/0 — şartlar §5.3, sonuç §7) |
| 13 | Araştırma protokolü diskte? | `.claude/skills/prompt-maker/references/10-web-research-protocol.md` → `Test-Path` = **True** | ✅ OKUNDU (§1.3 bu protokolle üretildi) |

> **Ders notu:** bu red **hiçbir zaman kodda denenmedi** — üretim kodunda ORM **0** (§1.1/7); "reddedildi" = "Eloquent/Laravel **hiç kurulmadı** ve veri erişimi ADR-002 ile **ham PDO**'ya bağlandı". Gelecekte biri "Laravel kullansak mı?" derse yanıtı bu dosya + ADR-002 verir; "zaten denedik mi?" sorusunun yanıtı **hayır, hiç denenmedi** (§5.2).

### 1.2 Sorun Tanımı

1. **Red kararı kanıtsız duruyor.** `index.md:131` bir sonuç cümlesi ("ORM yasak") ama **ne 2025-26 ekosistem kanıtı (Eloquent benimsenmesi, N+1/hydration ölçümleri, query builder'ın ara konumu, BCNF ile ilişki modeli) ne yeniden değerlendirme koşulu ne yerini alan eşleme** yazılı — gelecekteki biri "Eloquent neden yok, bugün de mi yok, query builder'a ne oldu, ne zaman tekrar sorulur?" sorusuna vault'tan cevap bulamıyor.
2. **Gerekçe ailesi parçalı ve tek cümlelik.** Uzun gerekçe ADR-002 `:137/:140/:174-184/:201` içinde; CLAUDE/brain/opencode.json/shared-AGENTS dört ayrı dosyada aynı yasağı tekrar ediyor — ama **hiçbiri "bugün hâlâ doğru mu" sorusunu araştırmamış**.
3. **Güncellik sorunu + ters kanıt riski.** 2025-26'da Laravel PHP'nin en çok kullanılan framework'ü (JetBrains %64) ve Eloquent'in N+1 savunmaları olgunlaşmış durumda (`preventLazyLoading`, Laravel 12.8 otomatik eager-load); literatürün ana akışı **hibrit** ("80% ORM / 20% raw") → "ORM yasak" ifadesi **tek başına fazla geniş** kabul edilirse red yanlış gerekçeyle savunulur. Dürüst ayrım §1.3 ve §2.1/3'tedir: red **Eloquent/Laravel iskeletini** reddeder; **query builder'ı yasaklamaz** (ADR-002 §3/4: serbest — §1.1/9) ve **ham SQL'i** zaten hâkim kılar.
4. **Koşul tanımsız.** "Hangi durumda ORM kapısı yeniden açılır?" (ölçülmüş üretkenlik/eksik-sorgu kanıtı, ADR-002 istisna kapısının işletilmesi, ekip SQL yeterliliği) **hiç belgelenmedi** → yeniden değerlendirme tetikleyicisi tanımsız.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (öncelik: resmî doküman → vendor → bağımsız blog; her iddiaya kaynak). Araştırma 2026-10-02'de yapıldı — **4 sorgu / ~32 adlandırılmış kaynak**.

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "ORM vs raw SQL 2025 2026 performance overhead when to use" → (2) "Eloquent N+1 problem Laravel performance query overhead 2025" → (3) "Laravel Eloquent market share adoption 2025 2026 PHP framework survey" → (4) "query builder vs ORM vs raw SQL middle ground Laravel DB::table type safety abstraction leaky" |
| Web Search **Konusu** | **(1)** ORM'nin gerçek maliyeti (soyutlama mı, sorgu sayısı mı? — 2025-26 benchmark'ları) · **(2)** Eloquent'e özgü N+1 problemi ve Laravel'in savunmaları (`preventLazyLoading`, eager load, 12.8 otomatik eager-load) · **(3)** Laravel/Eloquent benimsenmesi (red bugün "popüler değil" diye savunulabilir mi?) · **(4)** üçüncü katman: **query builder** (ORM ile ham SQL arası) — red kapsamı neyi içermeli? + tip güvenliği/soyutlama sızıntısı |
| Web Search **Bağlam** | CoreMusic: ADR-002 (PDO zorunlu, ORM yasak — diskte), ADR-014 (özel PHP migration runner — diskte), ADR-033/ADR-003 (18 BCNF DB, DB arası FK yok), ADR-040 (18 DB otoritesi + tek yazar), ADR-001 (iskelet/kütüphane yasağı — framework yasağının dayanağı) · üretim kodunda ORM **0**, query builder **0**, `->prepare(` üretim **12** (§1.1/7-9) · hedef soru — *"Eloquent red'i bugün hâlâ doğru mu; hibrit literatür red'i çürütüyor mu; query builder bu redin içinde mi dışında mı?"* |
| Web Search **Kısa Açıklama** | **(1)** Soyutlama-overhead'i **ölçülebilir ama ikincil**: Prisma +%100 sorgu süresi / Drizzle +%21 (dcdhameliya 2025), Prisma vs ham SQL → ham SQL **5× hızlı, 6-9× az CPU** (ScienceDirect 2025 benchmark); asıl maliyet **N+1 (5.291%!) ve yanlış desen seçimi** (goldlapel 2026, 9 kalıp), "sorgu kalitesi değil sorgu sayısı" (openreplay 2026) → **ham SQL lehine ölçüm var**. **(2)** Laravel N+1 literatürü olgun: 101 sorgu → 2 sorgu, 1.450 ms → 80 ms (achour 2025), 10.000 kayıtta ~95 sn (maxw3ll 2025), API 3 sn → 200 ms (mejba 2025) — savunmalar `preventLazyLoading()` (Laravel 8.43+) + `withCount` + Laravel **12.8 otomatik eager-load** (laracasts 2025). **(3)** Benimsenme **güçlü**: JetBrains State of PHP 2025 → **Laravel %64** (WP %25, Symfony %23); Reflex 2026 → Laravel 11.x %61,2 üretimde; stateofdb → Eloquent'i duyan **%15,4** (tüm respondentler), kullananların %53'ü hâlâ kullanıyor, **fikir birliği YOK** (yeni projede Eloquent'e oy bölünmüş). **(4)** Query builder literatürde **net ara katman**: PDO parametre bağlama ile SQL injection'a karşı korur, hydration yok, "raw SQL'e göre hafif ek soyutlama, ORM'e göre tam kontrol" (laravel.com docs · 91bytes · dasroot 2026 · nazarboyko 2024); `DB::raw`/`whereRaw` **kaçış kapısı** olduğu için Laravel dokümanı açıkça "Laravel cannot guarantee…" der → **soyutlama sızıntısı resmî olarak kabul edilmiş**. |
| Web Search **Uzun Açıklama** | **(1)** Üç bağımsız ölçüm aynı yönde: (a) ScienceDirect S187705092502722X — ham SQL, Prisma ORM'ye karşı 8 sorgu tipinde **her metrikte** üstün (5× hız, 6-9× CPU, 2-3× bellek); (b) dcdhameliya — Prisma +100% sorgu süresi + 45 KB/sorgu, Drizzle +21%; bir API'de tüm soyutlama katmanları toplam 7,55 ms = ~%38 ek yük; (c) goldlapel — basit WHERE/join/aggregation'da **fark %2-9 (ihmal edilebilir)**, asıl açık N+1 (**%5.291**), alt-sorgu seçimi (IN vs EXISTS, **%1.633**), toplu insert (**%600**) ve **ORM'in ifade edemediği** pencereler/LATERAL/COPY. Yani "ORM yavaş" cümlesi yanlıştır; doğru cümle "**ORM'in varsayılan desenleri yavaş olabilir ve bazı SQL'i hiç ifade etmez**". **(2)** Eloquent tarafı da aynı resmi doğruluyor: laravel-news (200 kitap → 21 sorgu), achour (100 satır → 101 sorgu), maxw3ll (10.000 kullanıcıda 10.001 sorgu ≈ 95 sn), mejba (accessor/resource gizli N+1'leri; `withCount`/`whenLoaded` ile tek round-trip), dev.to/rgalstyan (eager-load bile 4 sorgu → JSON aggregation ile 1 sorgu, **%83 hızlanma**; hydration'ı atlamak belleği %91 düşürüyor). Yani red'in "sihir/kayıp kontrol" gerekçesi **bugün sayılarla da destekleniyor** — ama Laravel aynı zamanda bu tuzaklara **çözüm üretiyor** (bu, red'i değil, **gerekçeyi günceller**: sorun "ORM çözülemez" değil, "ekstra katman + framework bağımlılığı"). **(3)** Benimsenme bulgusu red'in **zayıf noktasıdır**: Laravel %61-64, 4090 katılımlı State of Laravel, Packagist ekosistemi → "popüler değil" argümanı **kullanılamaz**; red'i savunmak için gerekçe **benimsenme değil, mimari** olmalıdır (CoreMusic: 18 BCNF şema, DB arası FK yok [ADR-003], açık JOIN + `EXPLAIN` disiplini [ADR-002 §5.1/6], framework/iskelet yasağı [ADR-001], tek yazıcı otorite [ADR-040], kendi migration runner'ı [ADR-014]). Ayrıca Eloquent **Laravel iskeletiyle** gelir (model + service provider + artisan + package ecosystem) → composer 0 (§1.1/7) demek **framework'ün de yok** olması demektir. **(4)** Ara katman tartışması red'i **daraltıyor**: literatür "ORM ↔ query builder ↔ raw SQL" üçlüsünde query builder'ı **varsayılan** yapar (openreplay: "query builder = eksik üçüncü seçenek"; dasroot: "ORM'den fazla kontrol, raw'dan fazla soyutlama"; Stackademic: "listeler/join'ler için builder"); ADR-002 §3/4 de builder'ı **yasaklamaz** ("serbest bırakıldı") → dürüst red kapsamı: **Laravel/Eloquent iskeleti yasak; builder yasak değil; ham PDO hâkim**. Tip güvenliği: PHP'de sorgu-öncesi tip güvenliği literatürde de tartışmalı (thetutlage: "partial type safety yoktur"; Kysely/Drizzle tartışması JS/TS'e özgü) → PHP tarafında tip güvenliği **DTO + prepared binding** ile sağlanır, ORM ile değil. |
| Web Search **Paragraf Veri Uzun** | **(1)** sciencedirect.com S187705092502722X (Prisma vs ham SQL benchmark) · dcdhameliya.com "Measuring the Cost of Abstractions" (2025-06) · docs.bswen.com "ORM vs Raw SQL: When to Use Each" (2026-02-11) · goldlapel.com "ORM vs Raw SQL Performance" (2026-03-05, 9 kalıp) · jamalhansen.com "ORM vs Raw SQL Decision Framework" (2026-06-01) · mukulkadel.com "ORM vs Raw SQL Comparison" (2026-05-23) · blog.openreplay.com "ORM or Raw SQL" (2026-07-15) · exa.ai/library/y89rdxf124q (SQLAlchemy + FK + N+1 deneyi) — **8**. **(2)** alaminahamed.com "Hunting N+1 in Laravel" (2025-01) · achour.dev "Eloquent and the N+1 Problem" (2025-10-30) · maxw3ll.com "Laravel N+1 Query Problem" (2025-09-15) · mejba.me "Fix N+1 Queries: 3s → 200ms" (2025-10-30) · laracasts.com ep.19 "Laravel Just Destroyed Your N+1 Problem" (2025-04-17) · laravel-news.com "Eloquent Performance: 4 N+1 Examples" (2022-05-20) · laraveldaily.com tag/n1-query · dev.to/rgalstyan "83% Faster Laravel API" (2025-12-15) — **8**. **(3)** blog.jetbrains.com "State of PHP 2025" · stateofdb.com/tools/eloquent · zend.com "PHP Usage Trends 2026" (2026-04-16) · getreflex.dev "State of Laravel Infrastructure 2026" (2026-05-01) · laravel-news.com "State of Laravel 2026 Survey" (2026-08-26) · itmarkerz.co.in JetBrains 2025 özeti · edmondscommerce.co.uk framework adoption (JetBrains 2024) · tms-outsource.com Laravel istatistikleri (2025-08-23) — **8**. **(4)** laravel.com/docs/queries (resmî query builder) · 91bytes.com "Choosing the Right Database Approach" · github.com/thetutlage/meta/discussions/8 (type-safety) · dasroot.net "Building a Database Abstraction Layer" (2026-01-14) · joelbutcher.dev "Beyond ORM: Raw Queries in Laravel" (2025-02-09) · maiobarbero.dev "Eloquent vs Query Builder vs Raw SQL anatomy" (2026-09-18) · blog.stackademic.com "Eloquent vs Query Builder vs Raw SQL" (2025-09-04) · nazarboyko.com "Where Laravel Helps and Hides Too Much" (2024-11-08) — **8**. **Toplam ~32 benzersiz kaynak / 4 sorgu.** |
| Web Search **Sonucu** | **(1) Red destekleniyor — gerekçe netleşti:** basit sorgularda ORM overhead **ihmal edilebilir** (%2-9), asıl kayıp **N+1/yanlış desen/ifade tavanı** → red'in dayanağı "hız" değil, **"ek katman + kayıp kontrol + ifade tavanı"** olmalıdır (kaynak 8). **(2) Red destekleniyor — ölçümlerle:** Eloquent N+1 vakaları (101 sorgu / 95 sn / 3 sn→200 ms) ve hydration maliyeti (100.000 satırda ~6× bellek, dev.to %91) red'in "sihir + maliyet" gerekçesini **doğrular**; ama Laravel'in savunmaları (`preventLazyLoading`, 12.8 otomatik eager-load) **çözüm ürettiği için** red "Eloquent başarısız" diye değil, "CoreMusic'te bu katmana gerek yok" diye yazıldı (kaynak 8). **(3) Dürüst gerilim — benimsenme:** Laravel %61-64 = PHP ekosisteminin birincisi (kaynak 8) → **"popüler değil" argümanı REDDEDİLDİ**; red yalnız mimari gerekçeyle (18 BCNF + ADR-001 framework yasağı + ADR-014 kendi runner'ımız + composer 0) savunulur. **(4) Ara katman netleşti:** query builder literatürde **varsayılan orta yol**, ADR-002'de de **serbest**; red **Eloquent/Laravel iskeletini** hedef alır, builder'ı **hedef almaz** → kapsam §2.1/3'te sabitlendi. **İtiraz/karşıt bulgu (dürüst):** (i) benimsenme verisi red aleyhine (açıkça yazıldı); (ii) "hibrit 80/20" ana akışı ham-SQL-tektir stratejisine **karşı** → red bunu ** CoreMusic istisnası** (18 şema, hazır JOIN'ler, `EXPLAIN` kapısı, ekip SQL yeterliliği) ile karşılar, literatürü **inkâr etmez**; (iii) derin sayfa-içi turu **yapılmadı** → sayılar başlık/özet düzeyindedir (§5.1/6). |
| Web Search **Alınan Karar** | **R-006 RED (Laravel / Eloquent ORM) YÜRÜRLÜKTE KALIR.** (a) **CoreMusic veri erişiminde Laravel framework'ü ve Eloquent ORM (ActiveRecord model + hydration + relation magic + service provider iskeleti) KABUL EDİLMEZ** — veri erişimi ADR-002 gereği **ham PDO + prepared statement** ile `DatabaseManager` üzerinden yürür; bunu `:137` (ADR-002 alternatif 1) + `.ai/CLAUDE.md:416/:517` kilitler. (b) **Red gerekçesi "hız" değil mimaridir:** (i) framework/iskelet yasağı (ADR-001) — composer'da Laravel **0** (§1.1/7); (ii) 18 BCNF şema + DB arası FK yok (ADR-003/033) → açık JOIN + `EXPLAIN` disiplini (ADR-002 §5.1/6); (iii) kendi migration runner'ımız ORM'siz (ADR-014 §1.4); (iv) tek yazıcı otorite (ADR-040); (v) N+1/hydration ölçümleri (§1.3-2) bu katmanın **varsayılan tuzaklarını** doğrular. (c) **Query builder bu redin KAPSAM DIŞIDIR** (ADR-002 §3/4 "serbest bırakıldı"; kodda kullanım 0 — §1.1/9): red **Eloquent'i** reddeder, **builder'ı yasaklamaz**; "ORM yasak" ifadesinin bağlamı `.ai/CLAUDE.md:517` "Raw PDO only" ile okunur. (d) **Benimsenme argümanı kullanılmaz** (Laravel %61-64 — §1.3-3): "popüler değil" denmez, "CoreMusic mimarisine uymuyor" denir. (e) **Yeniden değerlendirme koşulu** (§2.3) yazılmadan bu red **otomatik olarak güncellenmez**; "Laravel popüler" veya "N+1 artık çözüldü" argümanları tek başına koşul sayılmaz. |
| Web Search **Sonuç** | **4/4 araştırmada red desteklendi** (overhead/N+1 ölçümleri + Eloquent savunmaları + mimari eşleşme + ara katman kapsamı); **iki dürüst gerilim yazıldı**: (1) benimsenme verisi red aleyhine (Laravel %61-64 → gerekçe hız değil mimari oldu), (2) literatürün ana akışı **hibrit** (80/20) — red bunu CoreMusic istisnasıyla karşıladı, inkâr etmedi. **Üç açık işaretlendi:** (i) sayfa-içi derin tur yok (§5.1/6) · (ii) benchmark rakamları **başka ORM/DB** (Prisma/SQLAlchemy/Drizzle) → Eloquent'e **doğrudan** uydurulmadı, yalnız Eloquent'e özgü Laravel ölçümleri ayrı verildi (§1.3-2) · (iii) stateofdb "Eloquent'i duyan %15,4" oranı **tüm respondentler** içindir, PHP'ye özgü değildir (yanlış okunmamalı). **Kaynak sayısı: 4 sorgu; §1.3'te adı geçen benzersiz kaynak ~32.** |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-002 accepted (PDO zorunlu, ORM yasak) | `:13` H1 "PDO Zorunlu, ORM Yasak"; `:137` Eloquent/Doctrine alternatifi **ret** ("OWASP ORM injection ayrı tür; 18 BCNF'de otomatik join seçim planını kapatır; mapping + service provider = iskelet bağımlılığı; sorgu görememe → denetim açığı"); `:141` stored proc ret; `:174-184` §4.4 zaruri istisna kapısı (5 şart + yeni ADR) — **bu red'in bağlayıcı dayanağı** |
| ADR-002 §3/4 — query builder **serbest** | `:140` "Query builder'ı zorunlu kılmak" alternatifi ret edilmedi: "**serbest bırakıldı** (§2.2 d)" → red **builder'ı kapsamaz** (§1.1/9); koruma katmanı prepared statement'tir |
| ADR-002 §5.1/6 (debate şartı) | "N+1 / query budget + `EXPLAIN`" — elle yazılmış sorgularda JOIN/batch disiplini + kritik sorguların `EXPLAIN` ile doğrulanması → **red'in uygulama karşılığı** (Eloquent olmadan N+1 benzeri risk nasıl kapatılır) |
| ADR-014 accepted (özel PHP runner) | H1 "Özel PHP Runner"; `:73` "Phinx/Doctrine benzeri ORM-içi migration katmanı kurulmaz (composer'da zaten yok)"; `:206` §3 alternatif 1 Phinx ret → **migrasyon da ORM'siz** |
| ADR-033 / ADR-003 / ADR-040 (18 BCNF) | BCNF birincil + kontrollü denormalizasyon (ADR-033), 18 DB + DB arası FK yok (ADR-003), tek yazıcı otorite + cross-DB FK politikası (ADR-040) → Eloquent'in relation/eager-load modeli bu şema disiplinini **otomatik karşılamaz**, açık JOIN şart |
| ADR-001 (iskelet/kütüphane yasağı) | Eloquent tek başına kütüphane değil, **Laravel iskeletiyle** gelir (model, provider, artisan, package) → framework yasağı ile **doğrudan** çakışır (§1.1/7: composer 0) |
| Kod yüzeyi gerçeği | Üretimde ORM **0** (§1.1/7), query builder **0** (§1.1/9), üretim `->prepare(` **12** (§1.1/8) → red bir "kaldırma" değil, **girişi engelleme** kararıdır; geri dönüş planı kod tarafında işlem gerektirmez (§5.2) |
| Araştırma protokolü | §1.3 `10-web-research-protocol.md` ile üretildi; **"Laravel popüler + N+1 çözüldü" itirazı** (ters kanıt) dürüstçe yazıldı ama red'i değiştirmedi (§1.3-3/4) |

---

## 2. Karar (Decision)

**R-006 REDDEDİLMİŞTİR: CoreMusic veri erişim katmanında "Laravel framework'ü + Eloquent ORM (ActiveRecord model, hydration, relation magic, service provider iskeleti)" KABUL EDİLMEZ.** Karar `index.md:131`'da bugünden vardı; bu dosya onu gerekçelendirir: red, ADR-002'nin "PDO zorunlu, ORM yasak" hükmünün (`:13/:137/:174-184`) + `.ai/CLAUDE.md:416/:517` kural 9'unun **red-kayıt ayağıdır** ve 2026-10-02 web araştırması (§1.3 — 4 sorgu, ~32 kaynak) red'in **bugün hâlâ doğru olduğunu** doğrulamıştır — "Eloquent yavaş" olduğu için değil, **framework/iskelet bağımlılığı + ifade/kontrol kaybı + 18 BCNF şemanın açık JOIN disiplini** olduğu için; N+1/hydration ölçümleri bu gerekçeyi destekler, Laravel'in benimsenmesi ise **red'in gerekçesini hızdan mimariye kaydırır**.

### 2.1 Neden Bu Seçenek?

1. **İlke tutarlılığı (kanıtlı):** ADR-002 `:13/:137` ORM'yi açıkça ret eder, `:174-184` istisna kapısını 5 şartla kilitler; `.ai/CLAUDE.md:416/:517` (kural 9 "Raw PDO only"), `brain.md:143`, `.opencode/opencode.json:128` (Data Engineer kural 2), `shared/AGENTS.md` §4 yasak 3 aynı hükmü tekrarlar; `index.md:131` gerekçesi "ORM yasak" — ayrı bir karar değil, **aynı ilkenin red-kayıt ayağı**.
2. **Kod kanıtı red'i destekliyor (ölçüldü):** composer ×5 → Laravel/Illuminate/Eloquent/Doctrine/Propel/Phinx = **0**; üretim PHP'de `Eloquent|Illuminate` = **0**; üretim `->prepare(` = **12** isabet ve tek bağlantı kapısı `DatabaseManager` (`EMULATE_PREPARES=false`) → ADR-002 **uygulanmış durumda**; query builder **0** (§1.1/7-9). Red bugün "hiç kurulmamış" bir katmanı engeller.
3. **Kapsam dürüstçe daraltıldı (araştırma + ADR-002 ile):** literatürün ana akışı **hibrit** ("80% ORM / 20% raw", "query builder = varsayılan ara katman") ve Laravel'in N+1 savunmaları olgun → red **bunu inkâr etmez**: **query builder kapsam dışıdır** (ADR-002 `:140` serbest), **ham PDO hâkim** kalır; red yalnız **Laravel iskeleti + Eloquent model katmanını** hedef alır. "ORM yasak" cümlesi `.ai/CLAUDE.md:517` "Raw PDO only" bağlamında okunur.
4. **Benimsenme argümanı reddedildi (dürüst):** Laravel %61-64 (JetBrains 2025, Reflex 2026) → "popüler değil / ölüyor" **denmez**; red'in savunması **mimari**tir: 18 BCNF şema + DB arası FK yok (ADR-003/033) → otomatik relation yerine **açık JOIN + `EXPLAIN`** (ADR-002 §5.1/6); kendi migration runner'ımız (ADR-014 `:73/:206`); tek yazıcı otorite (ADR-040); iskelet yasağı (ADR-001).
5. **Maliyet gerekçesi ölçüme bağlandı (kanıtlı):** Eloquent N+1 vakaları 101 sorgu → 2 sorgu, 1.450 ms → 80 ms; 10.000 kayıtta ~95 sn; hydration 100.000 satırda ~6× bellek (§1.3-2) → "sihir katmanı varsayılan tuzak üretir" iddiası **sayılarla** yazılı; buna karşılık red **ölçüm kapısı** ister (§2.3/2): karşı-kanıt olmadan red değişmez.

### 2.2 Teknik Detaylar

- **Reddedilen yüzey (Laravel + Eloquent):** `Illuminate\*` paket ailesi ve `composer require laravel/framework` · Eloquent `Model` soyutlaması (hydration, casts, accessor/mutator, global scope, model event'leri) · relation/eager-load mekanizması (`hasMany`/`with` — N+1 yüzeyi) · service provider / iskelet bağımlılığı (artisan, package ecosystem) · ORM tabanlı migration aracı (Phinx/Doctrine/Laravel migrate — ADR-014 `:206` ret) · "model = tablo" varsayımının 18 ayrı BCNF şemasına otomatik uygulanması.
- **İzinli yüzey (yerini alan uygulama):** (a) **ham PDO + prepared statement** — `EMULATE_PREPARES=false`, `ERRMODE_EXCEPTION`, `charset=utf8mb4`, `MULTI_STATEMENTS=false` (ADR-002 §2.2; kod: `shared/src/Database/DatabaseManager.php` tek bağlantı kapısı); (b) **repository deseni** — `OAuthRepository` gibi sınıf bazlı sorgu sahipliği (`->prepare(` ×6); (c) **query builder serbest ama kullanılmıyor** (ADR-002 `:140`; bugün 0 → yeni kodda **gereksinim varsa** eklenebilir, bu red'i ihlal etmez); (d) **kendi migration runner'ımız** — özel PHP + versioned SQL, DB başına bağımsız sequence (ADR-014); (e) **BCNF + açık JOIN + `EXPLAIN`** disiplini (ADR-033 + ADR-002 §5.1/6).
- **Kod yüzeyi ölçümü (2026-10-02):** composer 5/5 paketinde ORM **0** · üretim PHP `Eloquent|Illuminate|laravel` **0** · `->prepare(` üretim **12** (DatabaseManager 2 · OAuthRepository 6 · CatalogWriter 2 · bin/api-key-create 2) + test 2 + skill şablonu 1 = 15 tarama isabeti · `QueryBuilder|->from(|whereRaw` **0** → **ORM 0 / builder 0 / PDO 1** katman resmi.
- **N+1 savunması (Eloquent olmadan):** query budget + toplu JOIN + `EXPLAIN` kapısı ADR-002 §5.1/6'da zaten yazılı; liste sorgularında sorgu sayısı **testle** sınırlandırılabilir (QA: query count assertion — Laravel'in `preventLazyLoading` karşılığı **bizim** statik/test kapımızdır).

### 2.3 Yeniden Değerlendirme Koşulu (şart satırı)

> **Bu red yalnız aşağıdaki koşullardan BİRİ yazılırsa yeniden değerlendirilir; aksi hâlde yürürlükte kalır:** (1) **ADR-002 yeni bir ADR ile değiştirilirse** (mevcut ADR metinleri düzenlenmez — yeni ADR `superseded by` ile bağlar; ADR-002 §4.4'teki 5 şartlı istisna kapısı işletilirse debate ile) — kapı yalnız "izole bileşen/adayı, çekirdek auth/catalog/sosyal şeması DEĞİL" kapsamıyla açılır; (2) **ölçülmüş karşı-kanıt** Data Engineer + Backend Architect raporuyla belgelenirse — 18 BCNF şemada **ham PDO ile** üretilemeyen ve **query builder ile de** karşılanamayan bir gereksinim **sayılarla** yazılmalı (metrik: sorgu başına geliştirme süresi, üretilen sorgu sayısı, `EXPLAIN` plan kalitesi, N+1 benzeri olay sayısı, ekip SQL yeterlilik ölçümü) — ölçüm yoksa "Eloquent üretken" iddiası kurulamaz; (3) **framework yasağı (ADR-001) yeni bir ADR ile gevşetilirse** (Laravel'in/iskeletin kabulü ayrı karardır — o karar verilmeden bu red **tek başına** tartışılmaz); (4) **debate tamamlanıp red'i kuran koşullar değişirse** (§5.3) yeni debate + yeni ADR ile yeniden açılır. **Bugün: 1 = SAĞLANMADI (ADR-002 diskte, yürürlükte), 2 = ÖLÇÜLMEDİ (karşı-kanıt 0 — §1.1/7-9), 3 = SAĞLANMADI (ADR-001 iskelet yasağı diskte), 4 = debate ✅ TAMAMLANDI (19/1/0 RED DOĞRULANDI — §7) → red geçerli.**

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Laravel + Eloquent ORM (tam iskelet)** | Eksiksiz ekosistem (routing, auth, queue, artisan); %61-64 benimsenme (§1.3-3); hazır relation/cast/accessor; N+1 savunmaları olgun (`preventLazyLoading`, 12.8 otomatik eager-load) | Framework/iskelet bağımlılığı (ADR-001 ihlali); composer'da **0** (§1.1/7) → sıfırdan kurulum = tüm katmanların yeniden yazımı; 18 BCNF'de otomatik relation + hydration (N+1/bellek ölçümleri §1.3-2); migration kendi ekosistemine taşınır (ADR-014 çakışması); sorgu görememe → denetim açığı | Dizin `:131` gerekçesi (**ORM yasak**) + ADR-002 `:137` açık ret + `.ai/CLAUDE.md:416/:517` — red'in **doğrudan hedefi** |
| 2 | **Doctrine ORM (entity/DQL)** | Strict typing'e yakın DQL; unit-of-work; DB bağımsızlığı | Aynı ActiveRecord/Entity sorunları (hydration, mapping, iskelet bağımlılığı); `composer` **0**; ADR-002 `:137` adıyla ret eder; ADR-014 `:73/:206` migration katmanı olarak da ret | ADR-002 §3/1 ret satırı — **ORM sınıfının tamamı** yasak, Eloquent'e özel değil |
| 3 | **Hazır migration ORM'si (Phinx / Laravel migrate / Doctrine Migrations)** | Olgun checksum/rollback; hazır CLI | composer'da **0** (ADR-014 §1.1); kendi runner'ımız zaten kararlaştırıldı (özel PHP, DB başına bağımsız sequence, forward-only) | ADR-014 `:206` §3 alternatif 1 (YAGNI + ADR-002 ruhu) — **red'in migrasyon ayağı** |
| 4 | **Query builder zorunlu kılınmadı, serbest (bugünkü statü)** | ADR-002 `:140` hükmü: prepared statement koruma katmanıdır, builder değil → ek öğrenme katmanı yok; `DB::raw` benzeri kaçış kapısı riski taşımaz | Literatürde "varsayılan ara katman" (§1.3-4); bugün kodda **0 kullanım** → üretkenlik artışı ölçülmedi | **Reddedilmedi — bu, redin DIŞINDAKİ serbest alandır** (ADR-002 `:140` "serbest bırakıldı"); §1.1/9: kullanımı 0, ihtiyacı olursa ADR-002 şartlarıyla eklenir |
| 5 | **Ham PDO + prepared + repository (bugünkü uygulama)** | Tam sorgu görünürlüğü + `EXPLAIN` denetimi; 12 üretim `->prepare(` + tek bağlantı kapısı; 18 BCNF'de açık JOIN; framework bağımlılığı 0 | Elle yazılmış sorgu = N+1/tekrar riski (ADR-002 §5.1/6 query budget ile kapatılır); geliştirme süresi ORM kadar kısa değil; DTO/map elle yazılır | **Reddedilmedi — bu, yerini alan yaklaşımdır** (ADR-002); bedeli §4.2/§4.3'te yazılı, örtbas edilmedi |

*(Kabul edilen uygulama alternatifi — ham PDO + repository — §3'te "reddedilmedi" olarak ayrılmadı; o, ADR-002 kapsamındaki **yerini alan** yaklaşımdır ve bu red'in gerekçe kaynağıdır. Query builder satırı da aynı sebeple "serbest" olarak durur: red onu yasaklamaz.)*

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Veri erişim kararı tek yerde toplandı:** `grep R-006` → bu dosya; ADR-002 `:13/:137/:140/:174-184/:201` + `.ai/CLAUDE.md:416/:517` + `brain.md:143` + `opencode.json:128` + `shared/AGENTS.md` §4/3 + `index.md:131` ile gerekçe ailesi tek çatıya alındı; "Eloquent neden yok" sorusunun yanıtı vault'ta yazılı.
- **Red 2026 verisiyle yeniden sınandı:** 4 sorgu / ~32 kaynak — overhead ölçümleri + N+1 vakaları + mimari eşleşme + ara katman kapsamı lehine çalıştı; **iki ters/iki-yönlü kanıt dürüstçe yazıldı** (benimsenme %61-64 red aleyhine; hibrit ana akış red'i "tek yol" değil "CoreMusic istisnası" kıldı — §1.3-3/4).
- **Kapsam hatası önlendi:** "ORM yasak" ifadesi **yalnız Eloquent/Doctrine/entity sınıfını** kapsar; **query builder serbest** (ADR-002 `:140`) → red, ADR-002 ile **çelişmiyor**, onu tamamlıyor (§2.1/3).
- **Geri dönüş temiz:** hiçbir zaman kurulmadığı için `git revert` edilecek değişiklik **0** (§1.1/7, §5.2).
- **Dizin satırı artık kaynağa sahip:** `index.md:131` "no source" iddiası fiilen geçersiz (bu dosya kaynaktır) → bayrak temizliği §5.1/3'te ertelendi, §7.1/1'de raporlandı.

### 4.2 Olumsuz Sonuçlar

- **Üretkenlik maliyeti kabul edildi:** ORM'siz kod = elle yazılmış sorgu + elle DTO/map + elle JOIN disiplini → yeni özellik başına sorgu yazımı **daha uzun**; bu bedel §4.3/1 query budget ile yönetilir, gizlenmez.
- **N+1 benzeri risk kendi elimizde:** Laravel'in `preventLazyLoading` yerine **bizim** kapımız statik test + query budget'tır (ADR-002 §5.1/6) — bugün CI'da sorgu-sayısı assertion'ı **var mı?** `⚠️ VERIFICATION REQUIRED` (QA kapsamı ayrı).
- **Ekip SQL yeterliliği tek dayanak:** ekip SQL bilmezse ham PDO yol maliyeti artar → §2.3/2'deki "ekip yeterlilik ölçümü" şartının sahibi yok (Data Engineer).
- **Benimsenme baskısı sürecek:** Laravel %61-64 + ekosistem (Laravel Cloud, Boost/MCP) → yeni ekip/agent önerisi "neden Laravel yok?" der; yanıt bu dosyada yazılı (**§2.1/3-4**), savunma kolay değil.
- **Dizin/seri tutarsızlığı sürüyor:** `rejected/index.md` boş (12 red'in hiçbiri satırlanmadı) + `index.md:131` dead-link bayrağı duruyor → §7.1/1-2.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **N+1 / sorgu patlaması** — elle yazılmış liste sorgularında döngü-içi sorgu (Eloquent'in tuzakının PDO karşılığı) | 3 (olası) | 4 (yüksek — sayfa başına sorgu sayısı) | Query budget + toplu JOIN + `EXPLAIN` zorunluluğu (ADR-002 §5.1/6); QA'da sorgu-sayısı testi (§4.2/2) |
| **Üretkenlik/darboğaz** — her özellik için sorgu + DTO elle yazılır, teslim süresi uzar | 4 (çok olası) | 3 (orta) | Repository deseni + ortak sorgu şablonları (php-template Guardrail #16); query builder **serbest** (ADR-002 `:140`) → gereksinim varsa ölçümle eklenir |
| **Benimsenme baskısı** — "Laravel %64, siz niye yok" (yeni ekip/agent/AI önerisi) | 4 (çok olası) | 3 (orta) | Bu dosya §1.3-3 + §2.1/4 (gerekçe hız değil mimari) + §2.3/3 (iskelet yasağı ayrı ADR ile değişir) — yanıt vault'ta yazılı |
| **Sızıntı soyutlama kaçakları** — ileride builder eklenirse `whereRaw`/concat kaçışları | 3 (olası) | 4 (yüksek — SQLi) | Prepared kuralı istisnasız (ADR-002 §4.4/4); statik kapı: concat deseni + `EMULATE_PREPARES` taraması (ADR-002 §5.1/4 hedefi 0) |
| **Ekip SQL yeterliliği düşüşü** — uzun süre ORM'siz çalışma + yeni üyelerin SQL'i zayıf | 3 (olası) | 3 (orta) | §2.3/2 ölçüm şartı (ekip yeterlilik metriği); BCNF/normalizasyon kural seti ADR-033 eğitimi |
| **"N+1 artık çözüldü" dış baskısı** (Laravel 12.8 otomatik eager-load haberleri) | 3 (olası) | 2 (düşük) | §1.3-2: çözüm **Laravel içinde**; CoreMusic'te sorun **katmanın kendisi** (iskelet bağımlılığı) — yanıt §2.1/1/3 |

---

## 5. Uygulama (Implementation)

> Bu kayıt **salt-okunur seri**dir (`rejected/`); "uygulama" = kaydın vault'a doğru yerleştirilmesi ve denetimidir — kod değişikliği **yoktur**.

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu dosyayı `.ai/.decisions/rejected/R-006-laravel-eloquent-orm.md` olarak yaz (şablon §1-§7, 7 bölüm dolu) + `log.md` append ("R-006 yazıldı (debate PENDING)") | Vault Steward | 25 dk |
| 2 | **Debate** (3 tur / 20 persona — **✅ TAMAMLANDI**, sonuç §7: 19/1/0 RED DOĞRULANDI) + Tech Lead onayı ✅ | MO + Tech Lead | 2026-10-02 |
| 3 | **`index.md:131` `<!-- dead-link ... -->` bayrağı → DOKUNULMADI** (talimat gereği son sıfırlamaya ertelendi — rapor-only) · `rejected/index.md` § tablosu **BOŞ → DOKUNULMADI** (rapor-only) | Vault Steward | son sıfırlama |
| 4 | **Ölçüm adımı (§2.3/2 için kanıt):** ham PDO ile bir liste/detail ekranında sorgu sayısı, `EXPLAIN` plan raporu, sorgu başına geliştirme süresi, ekip SQL yeterlilik notu — **"Eloquent gerekli" iddiası bu ölçümle** tartılır; sayı yoksa red değişmez | Data Engineer + Backend Architect | bir sonraki sprint |
| 5 | Periyodik denetim: `composer.json` ×5 içinde `laravel\|illuminate\|eloquent\|doctrine\|propel\|phinx` = **0** korunur + üretim PHP `Eloquent\|Illuminate` = **0** (veri erişimi **yalnız ADR-002 üzerinden**) | Backend Architect + DevOps Engineer | her sprint |
| 6 | Sayfa-içi derin doğrulama turu: ScienceDirect/goldlapel benchmark sayıları + JetBrains %64 + stateofdb %15,4 oranının tabanı (§1.3 başlık/özet düzeyi kaldı) | Researcher | üretim öncesi |

### 5.2 Geri Dönüş Planı

Bu karar **kod tarafında geri alınacak bir şey üretmedi** (Laravel/Eloquent hiç kurulmadı → `git revert` edilecek değişiklik **0**; §1.1/7). Geri dönüş = **yeniden değerlendirme** demektir ve yalnız §2.3 koşullarından biri yazılırsa yeni ADR ile açılır: (1) ADR-002 §4.4 istisna kapısı işletilirse → bu dosya `superseded` notuyla **bağlanır, düzenlenmez**; (2) ölçülmüş karşı-kanıt (§2.3/2 metrikleri) → §2.3/2 kapısı; (3) ADR-001 iskelet yasağı değişirse → §2.3/3 (framework kararı ayrı ADR); (4) debate sonucu değişirse → debate kaydı + yeni ADR. Vault bozulursa standart kurtarma `git checkout` + son commit (AGENTS.md §17 #10). **Reddedilen teknolojinin kodda izi olmadığı için kullanıcı/veri etkisi YOKTUR.**

### 5.3 Debate Şartları

**Kayıt:** ✅ **TAMAMLANDI** (bu yazım debate **öncesinde** yapıldı — sonuç §7'ye işlendi: 3 tur / 20 persona, 19/1/0 RED DOĞRULANDI; durum `rejected` **kullanıcı onaylı red** olarak zaten kayıtlıdır).

**Planlanan akış:**

- **Tur 1 (20 persona — bulgu):** üretim kodunda ORM **0** + query builder **0** + `->prepare(` üretim **12** (§1.1/7-9) · red kaynağı `index.md:131` (dead-link **dokunulmadı** → §5.1/3) · yerini alan 4 ADR `Test-Path` **True** · gerekçe ailesi ADR-002 `:137/:140/:174-184/:201` + CLAUDE `:416/:517` · 4 sorgu / ~32 kaynak · `rejected/index.md` boş → dokunulmadı.
- **Tur 2 (itiraz → çözüm → şart):** (i) "Laravel %64, Eloquent olgun" → red gerekçesi **hızdan mimariye** kaydırıldı (iskelet yasağı + 18 BCNF) → **Şart 1 kapsam sabitleme**; (ii) "hibrit 80/20 ana akışı" → **query builder kapsam dışı** (ADR-002 `:140` serbest) → **Şart 2 builder ayrımı**; (iii) "N+1 Laravel'de çözüldü" → CoreMusic'te kapımız query budget + `EXPLAIN` (ADR-002 §5.1/6) → **Şart 3 ölçüm kapısı** (§2.3/2).
- **Tur 3 (oy):** sonuç + bağlayıcı şartlar buraya ve §7'ye yazılacak.

**Planlanan bağlayıcı şartlar (debate öncesi plan — kesinleşen 2 bağlayıcı şart aşağıdaki bloktadır):**

1. **Şart 1 — Kapsam sabitleme:** red kapsamı **"Laravel framework'ü + Eloquent/Doctrine/entity ORM katmanı"** olarak sabitlenir; **query builder açıkça reddin dışındadır** (ADR-002 `:140`).
2. **Şart 2 — Ölçüm kapısı:** "Eloquent gerekli/üretken" iddiası yalnız §2.3/2 metrikleriyle (sorgu sayısı, geliştirme süresi, `EXPLAIN`, ekip yeterliliği) kurulabilir; ölçüm yoksa red değişmez.
3. **Şart 3 — İstisna kapısı disiplini:** herhangi bir ORM talebi ADR-002 §4.4'ün 5 şartıyla **yeni ADR** olarak açılır; bu dosya **düzeltmez**, yalnız `superseded` ile bağlanır.

**Kesinleşen debate şartları (Tur 2→3 çıktısı — 2026-10-02, bağlayıcı — debate öncesi planlanan şart bloğu bu2 sonuçla kapanmıştır):**

1. **Şart 1 — Kapsam sabitleme + dürüstlük bayrakları (bağlayıcı):**
   - **1a (kapsam sabitleme):** red kapsamı **"red = Laravel framework + Eloquent ORM; query builder serbest"** olarak sabitlenir (query builder'ın dayanağı ADR-002 `:140` — kapsam dışı); bu satır dosyanın bağlayıcı kapsam şartıdır.
   - **1b (dürüstlük bayrakları → reset düzeltme listesi):** 3 dürüstlük bayrağı reset **düzeltme listesine** yazıldı: (i) ADR-014 `:19` ADR-040'ı "yok" der — ADR-040 diskte **var** (§7.1/6); (ii) ADR-002 `:17` ADR-003/ADR-033'ü "yok" der — ikisi de diskte **var** (§7.1/6); (iii) ADR-002 self-status vs `.ai/CLAUDE.md:870` "Frozen" çelişkisi (§7.1/7). Frozen ADR metinleri **dokunulmaz**; düzeltme ayrı vault revizyonunda yapılır.
2. **Şart 2 — Yeniden değerlendirme kapısı (net tane, bağlayıcı):** Laravel %61-64 benimsenme verisi (§1.3-3) **tek başına kapıyı açmaz**; kapı yalnız **ekip Laravel yetkinliğini kazanırsa** (ölçülebilir yetkinlik değerlendirmesi — sahibi: Tech Lead) **ve** §2.3 koşullarından biri yazılırsa yeniden değerlendirilir.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../index]] | Karar dizini — bu red'in kaydı (`:131`, slug + "ORM yasak" + dead-link bayrağı §5.1/3) |
| [[../accepted/ADR-002-pdo-mandatory-no-orm]] | **Yerini alan (birincil):** PDO zorunlu + ORM yasak; `:137` Eloquent/Doctrine ret · `:140` query builder serbest · `:174-184` §4.4 istisna kapısı · `:201` N+1/query budget + `EXPLAIN` |
| [[../accepted/ADR-014-multi-db-migration-strategy]] | **Yerini alan (ikincil):** özel PHP migration runner (ORM'siz); `:73` Phinx/Doctrine kurulmaz · `:206` hazır kütüphane ret |
| [[../accepted/ADR-033-sql-normalization-strategy]] | **Yerini alan:** BCNF birincil + kontrollü denormalizasyon (18 DB ortak kural seti) — eager-load değil **açık JOIN** disiplini |
| [[../accepted/ADR-040-database-authority]] | **Yerini alan:** 18 BCNF sahiplik matrisi + tek yazar + cross-DB FK politikası — ORM'nin otomatik ilişkisi bu otoriteyi atlar |
| [[../accepted/ADR-003-multi-db-bcnf]] | 18 şema + **DB arası FK yok** → relation/eager-load modeli bu yapıda doğal değil (ADR-014 `:19` dayanağı) |
| [[../accepted/ADR-001-vanilla-js-itcss]] | **Framework/iskelet yasağı** — Eloquent'in Laravel iskeletiyle gelmesi bu ilkeyle çakışır (§2.3/3 kapısının dayanağı) |
| [[../accepted/ADR-005-ultrathink-protocol]] | Kanıt standardı — `⚠️ VERIFICATION REQUIRED` etiketleri (§1.1, §1.3, §4.2, §5.3) |
| [[../../CLAUDE]] | Kural metinleri — `:416` ORM yasak · `:517` kural 9 "No ORM — Raw PDO only" · `:670` ORM→Raw PDO · `:870` ADR-002 kaydı |
| [[../../raw/brain]] | `:143` "Laravel Eloquent → ORM yasak (ADR-002) → PDO" karşılaştırma satırı |
| [[../../index]] | Master katalog — ADR kayıtları (veri erişim satırları) |
| [[../../raw/keys]] | Keyword haritası — "PDO / ORM" arama eşiği |
| [[../../.templates/adr/adr-template]] | Guardrail #16 — bu dosyanın §1-§7 iskeleti + §1.3 9 alan kaynağı |
| Dizin satırı | `index.md:131` — slug otoritesi + dead-link bayrağı (§5.1/3) |
| Debate şartları | Bu dosya **§5.3** — debate ✅ **TAMAMLANDI** (bağlayıcı 2 şart: 1a-1b + 2) + debate kaydı **§7** (19/1/0 RED DOĞRULANDI) |
| Debate Şart 1 (1a-1b) | **Bağlayıcı:** kapsam sabitleme ("red = Laravel framework + Eloquent ORM; query builder serbest") + 3 dürüstlük bayrağı reset düzeltme listesi (ADR-014 `:19` · ADR-002 `:17` · ADR-002 self-status vs CLAUDE `:870`) → §5.3 |
| Debate Şart 2 | **Bağlayıcı:** yeniden değerlendirme kapısı — yalnız **ekip Laravel yetkinliği kazanırsa** + §2.3 koşulundan biri → §5.3 |
| Debate sonucu | **§7** — 3 tur / 20 persona: **19 kabul / 1 çekimser / 0 red → RED DOĞRULANDI** (Tech Lead ✅, 2026-10-02) |
| [[R-001-redux-style-state-management]] | Seri kardeşi — aynı salt-okunur red kayıt formatı |
| [[R-002-mongodb-document-store]] | Seri kardeşi — aynı salt-okunur red kayıt formatı (kanıt tablosu/dürüst etiket deseni) |
| [[R-003-jquery-ui-framework]] | Seri kardeşi — format referansı (§1.3 9 alan, §7.1 rapor deseni) |
| [[R-004-webpack-bundle-system]] | Seri kardeşi — format referansı (§7.1 rapor deseni + satır-no düzeltmesi usulü) |
| [[R-005-rest-only-api]] | Seri kardeşi — format referansı (künye, §1.1 kanıt tablosu, §2.3 şart satırı, §7.1 rapor) |
| Düz metin | Eski seri R-007…R-012 (`rejected/index.md` tablosu boş → §7.1/2) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-10-02 | ✅ |
| Tech Lead | — | 2026-10-02 | ✅ |
| Arch Lead | — | — | ⏳ |

**Debate kaydı:** ✅ **TAMAMLANDI (2026-10-02)** — **3 tur / 20 persona** (akış §5.3) · **Sonuç: 19 kabul / 1 çekimser / 0 red → RED DOĞRULANDI** · **Tech Lead:** ✅ · **Arch Lead:** ⏳. Durum **rejected** (kullanıcı onaylı red) olarak kayıtlıdır — debate sonucu **red'i değiştirmez**, yalnız **2 bağlayıcı şartı** bağlar (§5.3).

| Tur | Katılım | Çıktı |
|-----|---------|-------|
| **1 — bulgu** | ✅ 20 persona tamamlandı | `R-006` grep **2 isabet** (index `:131` slug + R-005 `:224`) · yerini alan 4/4 diskte (ADR-002 · ADR-014 · ADR-033 · ADR-040) · composer ORM bağımlılığı **0** · üretim `Eloquent\|Illuminate\|laravel` **0** · `->prepare(` üretim **12/15** · PDO/PDOStatement **13/25** · query builder deseni **0** → ADR-002 uygulanmış · **daralan kırmızı kapsam:** query builder RED değil (ADR-002 `:140` serbest), red = Laravel framework + Eloquent ORM · Laravel ~%61-64 kabul red aleyhine şeffaf yazıldı · 3 dürüstlük bayrağı · 4 sorgu / ~32 kaynak · wiki-link **19/19** diskte · `index.md:131` dead-link + boş `rejected/index.md` **dokunulmadı** · 17 kabul/neutral, 3 uyarı (Critic: kapsam+bayraklar şart · DevOps: tetikleyici) |
| **2 — itiraz→çözüm** | ✅ 3 itiraz → 3 çözüm → 2 şart | (1) kapsam daraltması → **"red = Laravel framework + Eloquent ORM; query builder serbest" şart satırı → şart 1a**; (2) 3 dürüstlük bayrağı → **ADR-014/ADR-002 yanlış "yok" iddiaları reset düzeltme listesine → şart 1b**; (3) Laravel %61-64 tetikleyicisi → **yeniden değerlendirme kapısı net tane (ekip Laravel yetkinliği kazanırsa) → şart 2** |
| **3 — oy** | ✅ **19 kabul / 1 çekimser / 0 red** | **RED DOĞRULANDI** — **2 bağlayıcı şart** (1: 1a-1b kapsam sabitleme + dürüstlük bayrakları · 2: yeniden değerlendirme kapısı) §5.3'e yazıldı |

> **Sonuç:** R-006 **REDDEDİLDİ (kullanıcı onaylı) — debate ✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0) → RED DOĞRULANDI** · **Tech Lead:** ✅ · **Arch Lead:** ⏳ · bağlayıcı şartlar §5.3 (**2 şart**).

### 7.1 Rapor Notları (bu işlemde dokunulmayanlar)

1. **Dead-link bayrağı:** `index.md:131` `<!-- dead-link: R-006-laravel-eloquent-orm no source 2026-09-24 -->` **olduğu gibi bırakıldı** — artık `no source` iddiası **geçersizdir** (bu dosya kaynaktır) → temizlik son sıfırlamaya ertelendi, burada rapor edildi.
2. **`rejected/index.md` durumu:** dosya **VAR** (v1.0.1, `total: 12`) ama tablo **BOŞ** (başlık satırları, kayıt satırı yok) → bu işlemde **oluşturulmadı ve doldurulmadı** (dizin satırı düzenleme yetkisi kapsam dışı) → rapor-only. Seri **R-001…R-005** dosyalara karşılık tabloda **0 satır** var.
3. **Slug hizası:** dosya adı `R-006-laravel-eloquent-orm` = `index.md:131` slug **birebir** ✅ (R-001…R-005 dersi: tahmin yok, `rejected/` glob'u ile doğrulandı — bu işlem öncesi `R-006*` = 0 dosya).
4. **Debate:** ✅ **TAMAMLANDI** (3 tur / 20 persona — 19/1/0 RED DOĞRULANDI; sonuç §7) · **Tech Lead:** ✅ · **Arch Lead:** ⏳ · **status: rejected** (kullanıcı onaylı red) · kayıt §7.
5. **Kod yüzeyi kanıtı:** composer ×5 ORM **0** · üretim PHP `Eloquent|Illuminate|laravel` **0** (tek isabet `.opencode/opencode.json:128` = agent kuralı metni) · `->prepare(` üretim **12** isabet (DatabaseManager 2 · OAuthRepository 6 · CatalogWriter 2 · bin/api-key-create 2) + test 2 + skill şablonu 1 = 15 · `new PDO|PDO::|PDOStatement` üretim **13** (tarama toplamı 25) · query builder desenleri **0** → ADR-002 uygulanmış, ara katman yok.
6. **ADR-040/ADR-033 disk kanıtı:** görev "ADR-014 (özel SQL runner — diskte), ADR-033 (BCNF)" der; glob `accepted/` altında **her ikisini de** buldu (ADR-040-database-authority.md dahil, `status: accepted`). **Çelişki (dürüst):** ADR-014 `:19` "ADR-040 … `.ai/.decisions/**` altında dosyası YOK" ve ADR-002 `:17` "ADR-003 … ADR-033 tekil dosyaları diskte YOK → ⚠️" satırları **eski/idari** — bugün **üçü de diskte VAR** → frozen ADR metinleri **dokunulmadı**, yalnız burada rapor edildi (bir sonraki vault revizyonunda düzeltilebilir).
7. **ADR-002 durum çelişkisi (dürüst):** ADR-002 kendi künyesinde `status: accepted` + "frozen YOK; okunur + yazılabilir" derken `.ai/CLAUDE.md:870` aynı ADR'yi **"Frozen"** kaydeder → iki SSOT kaynağı çelişiyor; bu red her iki durumda da **dokunmadan** referanslar (rapor-only).
8. **Şablon yolu notu:** görev `templates/adr/adr-template.md` der; disk kanıtı `.ai/.templates/adr/adr-template.md` (`Test-Path` = **True**) — bu dosya o şablonun §1-§7 iskeletiyle (§1.3 9 alan, §7 Onay) **R-005 formatı** kullanılarak yazıldı.
9. **Kaynak derinliği:** §1.3 ~32 benzersiz kaynak **başlık/özet düzeyinde** derlendi (sayfa-içi tur yok) → §5.1/6'ya bırakıldı; benchmark rakamları **başka ORM/DB** (Prisma/SQLAlchemy/Drizzle) üzerindendir ve **Eloquent'e uydurulmamıştır**; stateofdb "%15,4" oranı **tüm respondentler** tabanındır (PHP'ye özgü değildir) → her iki nokta §1.3-7'de işaretli.

---

*R-006 v1.0.0 | 2026-10-02 | Created — CoreMusic Vault (.decisions/rejected/ sıfırdan yazım, salt-okunur seri)*
*Authority: R-006 Red Karar Metni · Mode: Red Team · Human Mode · Truth Mode*
