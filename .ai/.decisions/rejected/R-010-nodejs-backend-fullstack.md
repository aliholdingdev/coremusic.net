---
title: "CoreMusic — R-010: Node.js Full Stack Backend (REDDEDİLMİŞ — PHP Zorunlu)"
type: "architecture-decision"
category: "backend"
date: "2026-10-09"
updated: "2026-10-10"
version: "1.0.0"
status: "rejected"
authority: "SSOT — R-010 red kararı: CoreMusic backend'inin tamamının Node.js full-stack mimarisine taşınması (PHP backend'inin bırakılması) KABUL EDİLMEZ; PHP 8.4 zorunludur. Gerekçe: karar dizini index.md:162 'Node.js Full Stack | PHP zorunlu' + disk kanıtı (17.412 PHP dosyalı üretim backend'i, ADR-002 PDO zorunlu, ADR-001 vanilla JS frontend, ADR-039 11 servis = 10 PHP + 1 Node.js PLANNED) + web araştırması (§1.3, 4 sorgu / ~31 kaynak — Node.js I/O-eşzamanlılıkta hızlı, PHP CPU'da güçlü; 'Node = hızlanır' iddiası CoreMusic'te ölçülmüş değil UNKNOWN). Kapsam: red yalnız 'PHP backend'inin Node.js full-stack ile TAMAMEN değiştirilmesini' reddeder; Node.js'in izole yüzeyleri (vault .mjs araç scriptleri, CI test runner, ADR-026 planlı download servisi :3001, ADR-039 dinamik stack) bu redin KAPSAMI DIŞINDADR (§1.3/§2.2 dürüst sınırlandırma — R-006/R-009 dersi). Debate ✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 RED DOĞRULANDI — §7.2; 4 şart §5.3). Bu dosya salt-okunur seridir (rejected/) — değiştirilmez, yalnız referanslanır."
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 RED DOĞRULANDI)"
---

# CoreMusic — R-010: Node.js Full Stack Backend (Rejected)

> **Durum:** rejected (**debate ✅ TAMAMLANDI — 19/1/0 RED DOĞRULANDI**) — **Tarih:** 2026-10-09 — **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona, 2026-10-10)** — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Seri:** `.ai/.decisions/rejected/` (salt-okunur) — **Dosya:** `R-010-nodejs-backend-fullstack.md` (slug grep ile alındı — R-009 dersi, tahmin yok) — **Dizin slug'ı:** `R-010-nodejs-backend-fullstack` (**index.md:162** — dosya adı ile slug **eşleşiyor** ✅)
> **Dizin satırı:** `| R-010-nodejs-backend-fullstack | Node.js Full Stack | PHP zorunlu <!-- NO FILE on disk 2026-10-06 --> |` — `<!-- NO FILE on disk ... -->` bayrağı **bu işlemde DOKUNULMADI** (temizlik son sıfırlamaya ertelendi → §5.1/3 + §7.1/1).
> **İlgili kararlar:** [[../accepted/ADR-002-pdo-mandatory-no-orm]] (yerini alan — PHP 8.4 + PDO zorunlu, ORM yasak) · [[../accepted/ADR-001-vanilla-js-itcss]] (yerini alan — frontend Vanilla JS, framework yasağı — full-stack Node bunu da ihlal eder) · [[../accepted/ADR-039-7-service-platform-architecture]] (yerini alan — 11 servis = 10 PHP + 1 Node.js download PLANNED) · [[../accepted/ADR-026-download-service-architecture]] (Node.js'in izinli sınırı — download :3001 PLANNED) · [[../accepted/ADR-019-per-os-neva-player]] (ses backend'i ≠ web backend'i — §1.1/4 dürüst ayrıştırma) · karar dizini [[../index]] §5.
> **R-001…R-009 dersi uygulandı:** satır no ve slug **grep ile** alındı (gerçek satır **162**), hedefler `accepted/` glob'ları ile diskten doğrulandı; diskte olmayan hedefe link **kurulmadı**.

---

## 1. Bağlam (Context)

CoreMusic backend'i **PHP 8.4** üzerine kuruludur: `.ai/CLAUDE.md:75` "Temel Teknoloji | PHP 8.4, C++20, Vanilla JS, MySQL 9" · ADR-002 (PDO zorunlu, ORM yasak) · ADR-039 (11 servis mimarisi — Control/Media/Admin/Auth/API… servislerinin **10'u PHP 8.4**, yalnız download servisi Node.js + TS olarak **PLANNED** ve dizin diskte YOK) · 17.412 PHP dosyalı üretim kodu. Karar dizini bu tercihi tek satırla tescil etmiştir (`index.md:162` — "Node.js Full Stack | PHP zorunlu") ama **red metni hiç yazılmamıştır**: elde yalnız dizin satırı + `<!-- NO FILE on disk -->` notu + PHP-zorunluluk ADR'lerinin (001/002/039) gerekçe bağı vardır. Bu dosya, o satırın **gerekçeli red kaydıdır** — yeni bir karar değil, mevcut red'in (a) gerekçe, (b) güncel web araştırması, (c) yerini alan eşleme, (d) yeniden değerlendirme koşulu ile sıfırdan yazımıdır.

### 1.1 Mevcut Durum (disk kanıtı — dürüst etiket, 2026-10-09 taraması)

| # | İddia | Kanıt | Etiket |
|---|-------|-------|--------|
| 1 | Red kaydı var mı? | [[../index]] `:162` → `R-010-nodejs-backend-fullstack` + "Node.js Full Stack" + "PHP zorunlu" + `<!-- NO FILE on disk 2026-10-06 -->` | ✅ **KAYITLI** (dizin satırı tek kanıt; red metni bu işlemde yazılıyor) |
| 2 | Bu işlem öncesi dosya var mıydı? | `.ai/.decisions/rejected/` içinde `R-001-*`…`R-009-*`; `R-010*` = **0 dosya** (glob) | ❌ **YOKTU** → bu işlemde yazılıyor (klasör zaten var) |
| 3 | `R-010` vault grep isabeti | `index.md:162` (slug + gerekçe + NO FILE) · `rejected/R-009-*.md:236` "eski seri R-010…R-012 düz metin" · `rejected/index.md` başlık `total: 12` | ✅ **İSABET = 3** — `.ai/wiki/` dizini diskte **YOK** (grep path hatası → R-009'daki `wiki/vault-decisions.md:172` aynası bu taramada doğrulanamadı `⚠️ VERIFICATION REQUIRED`) |
| 4 | "Yerini alan ADR-005/006/019" iddiası (görev varsayımı) | Glob: `ADR-005-ultrathink-protocol.md` (Zero Hallucination protokolü) · `ADR-006-performance-targets.md` (CWV/API/audio performans hedefleri) · `ADR-019-per-os-neva-player.md` (IAudioBackend — **ses** backend'i, web backend'i değil) | ⚠️ **GÖREV VARSAYIMI DOĞRULANAMADI** — üç ADR diskte var ama konuları "PHP-first / tek dil / web backend" **değil**; bu yüzden **eşleme düzeltildi**: yerini alan = ADR-002 + ADR-001 + ADR-039 (+ADR-026 sınır) — §6 |
| 5 | Backend yüzeyi | 17.412 PHP dosyası (R-009 §1.1/9 sayımı) · `.ai/CLAUDE.md:286-310` servis tablosu (Music/Admin/Auth/Control = PHP 8.4; Download = Node.js+TS :3001) | ✅ **PHP backend IMPLEMENTED, Node.js backend = 0 servis** |
| 6 | Node.js çalışma yüzeyi — `.mjs` | `**/*.mjs` (node_modules hariç) = **2**: `.ai/scripts/vault-utf8-writer.mjs` (vault tek yazma arayüzü) · `.ai/scripts/validate.mjs` (doğrulama betiği) | ✅ **ARAÇ SCRIPTLERİ, SERVİS DEĞİL** — red bu yüzeyi kapsamaz |
| 7 | Node.js çalışma yüzeyi — `.js` | `**/*.js` (node_modules hariç) = **155** — `assets.coremusic.net/js/**` (router, coreplayer, components, managers) + test dosyaları | ✅ **Vanilla frontend** (ADR-001) — tarayıcı JS'i, Node runtime DEĞİL |
| 8 | `package.json` envanteri | Kök `package.json` **tek dosya** (node_modules hariç): 4 script (vitest ×2, playwright e2e) + devDependencies: `@playwright/test ^1.63`, `playwright ^1.63`, `vitest ^5.0.3`, `@vitest/coverage-v8 ^5.0.3`, `jsdom ^30.1.1`, `axe-playwright ^1.0.0` | ✅ **TEST ALTYAPISI** — production Node servisi, express/fastify bağımlılığı **0** |
| 9 | Node.js runtime'ı CI'da mı? | `.github/workflows/ci.yml:149,170,192` → `actions/setup-node@v4` ×3, `node-version: '26'` (js-test, js-coverage, js-e2e adımları) | ✅ **EVET — CI test runner olarak Node 26 kullanıyor** → "Node.js yasak" iddiası **yanlış olurdu**; red yalnız backend mimarisini kapsar (§2.2) |
| 10 | `.ai/CLAUDE.md` PHP/Node kuralı | `:75` "PHP 8.4, C++20, Vanilla JS, MySQL 9" · `:459` "Node.js \| Download Service \| LTS \| ✅ Evet" (dinamik stack satırı) · `:803` "Dinamik stack ilkesi §24 Node.js satırı bağlamı" | ✅ **PHP zorunlu + Node.js'e izinli sınır (Download Service)** — "tek dil yasağı" değil, **backend zorunluluğu** |
| 11 | ADR-026 Node.js yüzeyi | `download.coremusic.net` :3001 Node.js+TS — **dizin YOK, `*.ts` = 0** (`Test-Path = False`) → PLANNED (ADR-026 şart 1a, ADR-039:121) | ✅ **PLANNED, IMPLEMENTED DEĞİL** — red ile çelişmez; bu, Node.js'in vaultça tanınan izinli sınırıdır |
| 12 | Uzun PHP-zorunluluk gerekçesi | ADR-002:24 (PHP 8.4 + PDO + 18 BCNF), ADR-002 §1.3 (OWASP Injection A05:2025, prepared statement kanıtı, CVE-2025-14180), ADR-039:154 (download = "en izole iş" diye Node'a verildi — yani Node yalnız izole hizmette) | ✅ **3 ADR'de gerekçe** — red bu ağın **red-kayıt ayağıdır** |
| 13 | `rejected/index.md` durumu? | Dosya **VAR** (v1.0.1, `total: 12`) ama § tablosu **BOŞ** (hiçbir red satırlanmamış) | ⚠️ **BOŞ** → bu işlemde **dokunulmadı** → §7.1/2 |

> **Ders notu (R-006/R-009):** red **hiç kodda denenmedi** — üretim kodunda Node.js backend **0 servis**; "reddedildi" = "PHP backend Node'a taşınmadı ve taşınmayacak" demektir. Gelecekte biri "backend'i Node'a taşıyalım" derse yanıtı bu dosya + ADR-001/002/039 verir; "denedik mi?" sorusunun yanıtı **hayır** (§5.2).

### 1.2 Sorun Tanımı

1. **Red kararı kanıtsız duruyor.** `index.md:162` bir sonuç cümlesi ("PHP zorunlu") ama **ne 2025-26 ekosistem kanıtı (Node vs PHP performansı, LTS bakım yükü, tek-dil vs çok-dil ekip, PHP 8.5 olgunluğu) ne yerini alan eşleme ne yeniden değerlendirme koşulu** yazılı — gelecekteki biri "Node neden yok, bugün de mi yok, PHP darboğaz olursa ne olur?" sorusuna vault'tan cevap bulamıyor.
2. **Kapsam belirsizliği gerçek risk.** Vault'ta Node.js zaten **yaşıyor** (CI runner, `.mjs` araçlar, ADR-026/039 planlı download servisi) → "Node.js Full Stack" red'inin **neyi** reddettiği yazılmazsa biri "CI'da Node var, demek ki red geçersiz" ya da tersi "Node tamamen yasak" yanlış çıkarımını yapabilir.
3. **"Performans" tuzağı.** Node.js eşzamanlı I/O'da hızlıdır, PHP CPU'da güçlüdür; ama web uygulamasında darboğaz genelde **veritabanıdır** (§1.3-3) → "Node'a geç = hızlanır" iddiası hiçbir yerde **ölçülmemiştir** (UNKNOWN); red gerekçesi hız değil, **kanıtlanmış mevcut yüzey + geçiş bedeli + LTS bakım yükü** olmalıdır.
4. **Koşul tanımsız.** "17.412 PHP dosyası sürdürülemez hâle gelirse ya da PHP ekosistemi çökerse Node'a döner miyiz?" hiç belgelenmedi → tetikleyici tanımsız.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (öncelik: resmî doküman → vendor → bağımsız blog; her iddiaya kaynak). Araştırma 2026-10-09'da yapıldı — **4 sorgu / ~31 adlandırılmış kaynak**.

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "Node.js vs PHP 8.4 performance comparison 2025 benchmark" → (2) "PHP 8.5 new features release 2025 performance improvements" → (3) "Node.js LTS support schedule 2025 2026 maintenance end dates" → (4) "single language full stack team benefits vs polyglot maintenance cost small team" |
| Web Search **Konusu** | **(1)** Node 22 vs PHP 8.4 gerçek-workload benchmark'ları (throughput, CPU, DB, eşzamanlılık) · **(2)** PHP 8.5 (Kas. 2025) özellikleri + sürüm/özellik olgunluğu · **(3)** Node.js LTS/EOL takvimi ve bakım yükü (2026-2029) · **(4)** tek-dil full-stack ekip vs çok-dil ekip — küçük ekip için kazanç/kayıp |
| Web Search **Bağlam** | CoreMusic: PHP 8.4 + PDO + 17.412 PHP dosyası (§1.1/5) · 11 servis = 10 PHP + 1 Node.js PLANNED (ADR-039) · frontend Vanilla JS (ADR-001) · hedef soru — *"backend'in tamamının Node.js full-stack'e taşınması red'i bugün hâlâ doğru mu; gerekçe hız mı, başka bir şey mi?"* |
| Web Search **Kısa Açıklama** | **(1)** Node.js standart konfigürasyonda **throughput'ta önde** (Hello World 38.2k vs PHP-FPM 12.4k req/s; FrankenPHP 29.1k — fark daralır), ama **CPU-ağırlık görevlerde PHP JIT kazanabilir** (~4x — dev.to/mehdibafdil); "gerçek uygulamada darboğaz **veritabanıdır**, runtime değil" (dev.to 2026). **(2)** PHP 8.5.0 (20 Kasım 2025, php.net): URI uzantısı, pipe operator, clone-with, `#[\NoDiscard]`, **persistent cURL share handles** — PHP aktif gelişimde; Tideways: 8.2→8.5 uygulama-performans farkı **hata payı içinde** (framework değil, runtime şişirmez). **(3)** Node LTS ~30 ay sürer, **yılda 1 major upgrade** baskısı: Node 20 EOL Nis. 2026, 22 EOL Nis. 2027, 24 EOL Nis. 2028, 26 LTS Eki. 2026 / EOL Nis. 2029 (nodejs.org, nodesource, dev.to/eofl); topluluğun ~%30'u EOL sürümde çalışıyor → iki runtime = iki upgrade treni. **(4)** Tek dil: veri modeli/API tutarlılığı + FE dev'in backend'i kendisi açabilmesi (HN); ama tek-dil/kalıp-dışı kod "least common denominator"a kayar, güvenlik kusuru **iki katmanı birden** vurur, dil şişer (worklytics, abovesoftware) — "full-stack" unvanının kendisi verimlilik kaybı üretir (Worklytics) |
| Web Search **Uzun Açıklama** | **(1) Performans:** dev.to/syedahmershah (May. 2026) — Hello World: PHP 8.4 FPM 12.400 / FrankenPHP 29.100 / Node 22 38.200 req/s; JSON 9.8k vs 31.5k; DB read 4.2k vs 5.8k; DB write 3.1k vs 4.4k → Node standartta önde; **aynı makale** CPU görevinde PHP'nin 4x önde olduğunu, "the performance difference is not your bottleneck — your database is" sonucunu yazar. itoverdose (aynı deney): basit API 12ms vs 8ms; 500 eşzamanlı istek 1.800ms vs 1.200ms. mehdibafdil (2025): PHP 8.3 JIT CPU-görevlerinde Node'u geçti. hackernoon: Node "significantly faster in its standard configuration" (event-loop, worker yok) — ama FrankenPHP persistent-worker modeli PHP-FPM'in istek-başı init maliyetini siler. Readers'Digests (Kas. 2025): async/API kazançları **runtime değil mimariden** gelir (Swoole/RoadRunner/Octane). borislemke repo: framework overhead (Express 3.4k vs pure Node 20.8k; Laravel 26 req/s!) → **karşılaştırma hep framework+framework, runtime+runtime değil** — dürüst sonuç: CoreMusic-ölçeğinde "Node'a geç = X kat hızlı" **kanıtlanamaz**. **(2) PHP olgunluğu:** php.net 8.5 sürüm notu + migration guide + benjamincrozat/zend/upsun/infoworld/versionlog: URI (RFC 3986/WHATWG), pipe `|>`, clone-with, NoDiscard, sabit ifadede closure, persistent cURL share (bağlantı yeniden kullanımı), `array_first/last`, partitioned cookie (setcookie "partitioned" — CHIPS ile uyum), fatal-error full backtrace; PHP.net haber arşivi: 8.5.1+ 8.4.x+ 8.3.x+ 8.2.x+ 8.1.34 **aynı gün** güvenlik yamaları (çoklu-sürüm bakım desteği). betterCode() 2025: "FrankenPHP Caddy'de Avrupalı açık kaynak alternatif olarak yerleşiyor". Tideways (Kas. 2025): Symfony/Laravel/WordPress demo'larında 8.2→8.5 farkı hata payı içinde → sürüm yükseltme **bedava**; dil şişmesi yok. **(3) LTS yükü:** nodejs.org previous-releases: v26 Current (May. 2026), v24 LTS (Krypton) — LTS ~30 ay, toplam ~36 ay destek; nodesource (Tem. 2026) tablosu: 20 EOL, 22 Maintenance EOL Nis. 2027, 24 Active EOL Nis. 2028, 26 LTS Eki. 2026/EOL Nis. 2029; nodejs.org blog (18 EOL): topluluğun büyük kısmı EOL sürümde kalıyor, "skip 20 → go straight to 22" kampanyası bile upgrade yorgunluğunun kanıtı; eosl.date aynı tarihleri doğrular. Yani Node.js **bakım treni** gerektirir — PHP tarafında aynı yük 8.4→8.5 yükseltmesiyle ve çoklu-sürüm yama desteğiyle çok daha dardır. **(4) Tek-dil ekip:** HN (Mar. 2024): aynı dil = veri modeli/API doğrulama tutarlılığı, FE dev backend'i kendisi açar (haftalarca sprint beklemeye son), ama "full-stack largely unrealistic — people gravitate to one side"; Worklytics: tek-dil/kalıp-birleştirme "least common denominator" kodu üretir (daha uzun/ karmaşık), güvenlik kusuru iki katmanı birden vurur, uzman yerine generalist riski; abovesoftware: tek-dil şişme + güvenlik + sorumluluk bulanıklığı; makotokern/kanerika/wttj: çok-esneklik vs derinlik; polyglot makaleleri: "araç olarak dil, kimlik olarak değil" — CoreMusic zaten poliglot (PHP+JS+C++20+C#/TS planlı), yani "tek dil" argümanı **projenin gerçeğiyle çelişir**. |
| Web Search **Paragraf Veri Uzun** | Node 22 Hello World 38.200 vs PHP-FPM 12.400 vs FrankenPHP 29.100 req/s (dev.to 2026) · JSON 31.5k vs 9.8k · DB read 5.8k vs 4.2k · 500 eşzamanlı 1.2s vs 1.8s (itoverdose) · CPU görevinde PHP JIT ~4x önde (mehdibafdil) · "darboğaz DB'dir, runtime değil" (dev.to) · Express 3.4k vs pure Node 20.8k, Laravel 26 req/s (borislemke — framework overhead) · FrankenPHP persistent worker = FPM init maliyeti silinir · PHP 8.5.0 = 20.11.2025 (URI, pipe, clone-with, NoDiscard, persistent cURL share, partitioned cookie, array_first/last, full backtrace) · PHP 8.2→8.5 uygulama farkı hata payı içinde (Tideways Kas. 2025) · PHP çoklu-sürüm aynı-gün yama (php.net arşiv) · Node 20 EOL 30.04.2026 · Node 22 EOL 30.04.2027 · Node 24 EOL 30.04.2028 · Node 26 LTS 28.10.2026 / EOL 30.04.2029 · LTS ~30 ay / toplam ~36 ay · topluluk ~%30 EOL sürümde · tek dil = tutarlılık + FE dev backend açar (HN) · tek dil = least-common-denominator + güvenlik kusuru iki katman + dil şişme (Worklytics/abovesoftware) · CoreMusic poliglot: PHP + Vanilla JS + C++20 (+ TS/C# planlı) |
| Web Search **Alınan Karar** | **R-010 RED (Node.js Full Stack Backend) YÜRÜRLÜKTE KALIR — debate ⏳ PENDING.** (a) **CoreMusic backend'inin tamamının Node.js full-stack mimarisine taşınması (PHP 8.4'ün bırakılması) KABUL EDİLMEZ**; bunu `index.md:162` + ADR-002 (PDO/PHP zorunlu) + ADR-001 (frontend Vanilla JS — full-stack Node mevcut JS'i de framework'e çevirir) + ADR-039 (11 servis = 10 PHP; Node yalnız izole download servisinde PLANNED) kilitler. (b) **Red gerekçesi 'hız' değil GEÇİŞ BEDELİ + ÖLÇÜLMEMİŞ KAZANÇ + BAKIM TRENİ'dir:** (i) Node standartta I/O-throughput'ta önde ama CPU'da PHP JIT önde ve gerçek darboğaz DB (§1.3-1) → CoreMusic'te "Node = X kat hızlı" **UNKNOWN**; (ii) 17.412 PHP dosyası + ADR-010/011/012/013/022 middleware/oturum/CSRF/CSP/rate-limit/şifreleme + ADR-002 PDO/BCNF katmanının tamamının yeniden yazımı = ölçülemez geçiş riski; (iii) Node LTS yılda 1 major upgrade treni (20 EOL → 22 → 24 → 26) ikinci bir bakım yükü ekler; (iv) PHP 8.5 aktif gelişim + FrankenPHP ile async boşluğu kapanıyor (§1.3-2). (c) **Kapsam (R-006/R-009 dersi):** red yalnız **"PHP backend'inin Node full-stack ile tamamen değiştirilmesini"** reddeder; Node.js'in izole yüzeyleri **kapsam dışı ve izinli**: vault `.mjs` araç scriptleri (2 dosya) · CI test runner (setup-node@v4, Node 26) · kök `package.json` test altyapısı (vitest/playwright) · ADR-026/039 planlı download servisi (:3001, PLANNED) · tarayıcı Vanilla JS (ADR-001 — Node runtime değil). "Node.js yasak" cümlesi **bu dosyada YOKTUR**. (d) **Dürüst beyan:** `package.json`'da production Node bağımlılığı 0'dır; Node yüzeyi bugün **araç + test** seviyesindedir. (e) **Yeniden değerlendirme koşulu** (§2.3) yazılmadan bu red **otomatik olarak güncellenmez**; debate **⏳ PENDING** — sonuç bu dosyaya §7'ye eklenecektir. *(Araştırma anı kaydı — debate §5.3/§7.2'de sonradan tamamlandı: 19/1/0 RED DOĞRULANDI.)* |
| Web Search **Sonuç** | **4/4 sorgu**: (1) **performans dengesi yazıldı** — Node I/O önde, PHP CPU önde, darboğaz DB; "hız" gerekçesi her iki yönde de kanıtlanamaz → red gerekçesinden hız **çıkarıldı**; (2) **PHP olgunluğu doğrulandı** — 8.5 (Kas. 2025) özellikler + çoklu-sürüm yama + FrankenPHP → "PHP ölüyor" argümanı geçersiz; (3) **Node bakım treni doğrulandı** — EOL tarihleri resmî (nodejs.org/nodesource/eosl.date), ~%30 topluluk EOL'de → ikinci runtime bedeli gerçek; (4) **tek-dil argümanı projenin gerçeğiyle çelişti** — CoreMusic zaten poliglot; tek-dil kazançları (tutarlılık) PHP backend + paylaşımlı veri modeliyle zaten var, Node full-stack ek kazanç getirmez. **İki dürüst gerilim yazıldı:** (i) red **hiç denenmedi** (Node backend kodu = 0) → red "tescil"tir, "başarısız deney" değil; (ii) Node izinli yüzeyleri (CI, araçlar, planlı download) red'in kapsamı **dışında** → "Node yasak" okuması yanlıştır, §2.2'ye sabitlendi. **Üç açık işaretlendi:** (i) `.ai/wiki/` ayna satırı doğrulanamadı (dizin diskte YOK — §1.1/3 `⚠️`) · (ii) sayfa-içi derin tur yok · (iii) debate **PENDING** *(→ §7.2’de tamamlandı: 19/1/0)*. **Kaynak sayısı: 4 sorgu; §1.3'te adı geçen benzersiz kaynak ~31.** |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-002 accepted (PDO zorunlu, ORM yasak) | `:24` "PHP 8.4, MySQL 9, 18 BCNF" · `:28` "PHP 8.4 + PDO + PageRouter" · §1.3 (OWASP A05:2025, CVE-2025-14180, ATTR_EMULATE_PREPARES=false) — **PHP backend'inin veri katmanı sözleşmesi**; Node full-stack bu sözleşmenin tümünü geçersizleştirir |
| ADR-001 accepted (Vanilla JS + ITCSS, framework yasağı) | Backend Node olunca Next.js/Nuxt/SvelteKit **zorunlu baskısı** doğar → mevcut 155 `.js` vanilla dosyası + ITCSS katmanları framework'e taşınmak zorunda kalır — red, frontend kararını da korur |
| ADR-039 accepted (11 servis) | `:121/:154` — Node yalnız **download** servisinde ve **PLANNED** (dizin YOK, `*.ts` = 0); diğer 10 servis PHP 8.4 → red mevcut servis haritasını tesciller |
| ADR-026 accepted (download servisi) | `:52` `download.coremusic.net` Node.js/TS dizini **YOK** → Node'un izinli sınırı budur; red bunu **korumaz, ihlal etmez** |
| ADR-019 accepted (Per-OS Neva Player) | IAudioBackend = **ses** backend'i (C++), web backend'i ile **ilgisiz** → görev varsayımındaki eşleme §1.1/4'te düzeltildi |
| `.ai/CLAUDE.md` dinamik stack | `:459` "Node.js \| Download Service \| LTS \| ✅ Evet" + `:803` dinamik stack ilkesi → red **"tek dil yasağı" değil "backend zorunluluğu"** olarak okunur |
| Node LTS gerçeği | 20 EOL Nis. 2026 · 22 EOL Nis. 2027 · 24 EOL Nis. 2028 · 26 LTS Eki. 2026 (§1.3-3) → red dayanaklarından biri **sürdürülebilir bakım** |
| Araştırma protokolü | §1.3 `10-web-research-protocol.md` ile üretildi; ters kanıt (Node throughput üstünlüğü, tek-dil ekip kazancı) dürüstçe yazıldı ama red'i **değiştirmedi, gerekçeyi hızdan geçiş-bedeline kaydırdı** (§2.1/3) |

---

## 2. Karar (Decision)

**R-010 REDDEDİLMİŞTİR: CoreMusic backend'inin tamamının Node.js full-stack mimarisine taşınması (PHP 8.4 backend'inin bırakılması) KABUL EDİLMEZ.** Karar `index.md:162`'te bugünden vardı; bu dosya onu gerekçelendirir: red, ADR-002'nin "PHP 8.4 + PDO zorunlu" hükmünün + ADR-001'in "frontend Vanilla JS, framework yasağı" hükmünün + ADR-039'un "11 servis = 10 PHP + 1 Node.js (download, PLANNED)" haritasının **red-kayıt ayağıdır** ve 2026-10-09 web araştırması (§1.3 — 4 sorgu, ~31 kaynak) red'in **bugün hâlâ doğru olduğunu** doğrulamıştır — "Node daha hızlı olduğu için" **DEĞİL** (§1.3-1: I/O'da Node, CPU'da PHP; darboğaz DB → hız farkı CoreMusic'te UNKNOWN), **geçiş bedeli + ölçülmemiş kazanç + LTS bakım treni + PHP ekosistemi olgunluğu** olduğu için.

### 2.1 Neden Bu Seçenek?

1. **Disk kanıtı PHP backend'i gösteriyor (ölçüldü):** 17.412 PHP dosyası · 11 servis = 10 PHP + 1 Node.js (PLANNED, dizin YOK) · kök `package.json` = test altyapısı (playwright/vitest — production bağımlılık 0) · `.mjs` = 2 vault araç scripti (§1.1/5-8). Red, **hiç kurulmamış** Node full-stack backend'i engeller; "PHP backend vardır" iddiası dosyalardan okunur.
2. **Geçiş bedeli ölçülemez:** ADR-010/011/012/013/022 middleware/oturum/CSRF/CSP/rate-limit/şifreleme + ADR-002 PDO/BCNF katmanı + PageRouter — Node'a geçiş bunların **tamamının yeniden yazımı** demektir; CSRF `hash_equals` kanıtlı implementasyon (`CsrfMiddleware.php:42`) dâhil her security-katmanı yeniden kanıtlanmak zorunda kalır.
3. **Hız gerekçesi yok (§1.3 dürüst düzeltmesi):** Node I/o-throughput'ta önde, PHP CPU'da önde, darboğaz DB (§1.3-1); CoreMusic'te **hiçbir ölçüm yok** → "Node'a geç = X kat hızlı" **iddia edilemez**; red gerekçesinden "hız" kelimesi **çıkarıldı** (R-009 performans dersinin aynısı).
4. **LTS bakım treni gerçek:** Node 20→22→24→26 EOL zinciri (§1.3-3) ikinci bir upgrade treni ekler; PHP çoklu-sürüm aynı-gün yama desteği dar ve öngörülebilir.
5. **PHP ekosistemi ölü değil, olgunlaşıyor:** PHP 8.5 (Kas. 2025) URI/pipe/NoDiscard/persistent-cURL + FrankenPHP persistent-worker (§1.3-2) → "Node'a geçiş = modernizasyon" argümanı zayıf.
6. **Full-stack Node frontend'i de bozardı:** Node backend baskısı Next.js/Nuxt'a iter → ADR-001 (vanilla JS + ITCSS) + 155 `.js` dosyası + Guardrail #16 CSS kanonu ihlal edilir → red **frontend kararını da korur**.
7. **Kapsam dürüstçe sınırlı (R-006/R-008 dersi):** red yalnız **"PHP backend'inin tamamen Node'a taşınmasını"** reddeder; Node.js'in izinli yüzeyleri (CI runner Node 26, `.mjs` araçlar, kök `package.json` test altyapısı, ADR-026/039 planlı download servisi) **reddin kapsamı dışındadır** — "Node.js yasak" cümlesi bu dosyada yoktur (§2.2).

### 2.2 Teknik Detaylar

- **Reddedilen yüzey (Node.js full-stack backend):** tüm web/API/oturum/güvenlik servislerinin Node.js (+Express/Fastify/Nest vb.) ile yeniden yazımı; PHP 8.4 runtime'ının production'dan çekilmesi; ADR-002 PDO katmanının Node ORM/driver'a (Prisma/Drizzle/knex vb.) çevrilmesi; ADR-010/011/012/013/022 middleware zincirinin (OriginCheck → Cors → RateLimit → SecurityHeaders → Session → Csrf → Auth → Permission) Node'a taşınması; PageRouter/route registry'nin Node router'a çevrilmesi; ADR-001'in pratikte ihlali (framework SSR/SSG baskısı); `.env` Parser (ADR-015) + PageRouterKernel pipeline testlerinin yeniden kurulması.
- **İzinli yüzey (yerini alan uygulama — red KAPSAMI DIŞINDA):** (a) **PHP 8.4 backend** korunur (10 servis, PDO, PageRouter, middleware 10-adım sırası değişmez); (b) **CI test runner Node 26** (`.github/workflows/ci.yml:149-194` — vitest/jsdom/playwright); (c) **vault `.mjs` araçları** (vault-utf8-writer, validate); (d) **kök `package.json` test altyapısı** (production bağımlılık 0); (e) **download servisi :3001 Node.js+TS** — ADR-026/039'ta **PLANNED** (dizin YOK, `*.ts` = 0; bu red onu engellemez); (f) **tarayıcı Vanilla JS** (155 `.js` — Node runtime değil); (g) **dinamik stack ilkesi** (`.ai/CLAUDE.md:459/:803`) — gelecekte yeni Node yüzeyleri **yeni ADR + debate** ile açılır.
- **Kod yüzeyi ölçümü (2026-10-09):** `.mjs` = 2 · `.js` = 155 (node_modules hariç) · `package.json` = 1 (test-only, 6 devDependency) · `setup-node@v4` = 3 iş (Node 26) · `download.coremusic.net/` = **YOK** · `*.ts` = 0 · üretim kodunda **Node backend servisi = 0**.
- **Ölçüm boşluğu (dürüst — §4.2):** "Node full-stack'e geçiş kazancı" (p95, throughput, bellek, geliştirici-verimi) **hiçbiri ölçülmemiştir (UNKNOWN)** → §2.3/1 eşiği bugün **sağlanamaz**; red **işlevsel/geçiş-bedeli** gerekçesiyle durur.

### 2.3 Yeniden Değerlendirme Koşulu (şart satırı)

> **Bu red yalnız aşağıdaki koşullardan BİRİ yazılırsa yeniden değerlendirilir; aksi hâlde yürürlükte kalır:** (1) **PHP backend kanıtlanmış bir darboğazda** — talep **Backend + Performance + Vault Steward** raporuyla şunları **ister**: (a) PHP 8.4 + FrankenPHP/Swoole (ya da eşdeğer persistent-worker) ile ölçülmüş **p95/p99 ve throughput** sonuçları (ADR-006 hedefleriyle karşılaştırma), (b) Node.js karşılığının **aynı iş yükünde** ölçülmüş sonuçları (kontrollü PoC, framework-süzme düzeltmesi §1.3-1 dersi), (c) 17.412 PHP dosyasının **taşınma maliyeti tahmini** (tahmini gün/servis + güvenlik-katmanı yeniden kanıtlama planı), (d) Node LTS **upgrade treni yükü** (yıl × ekip-saat) — eşik önceden yazılır ve **ölçüm yoksa kapı kapalı**; (2) **ADR-001/002/039 yeni ADR ile superseded edilirse** (metinler düzenlenmez — `superseded by` ile bağlanır; debate ile) → kapı yalnız **"tek servis-başına geçiş"** kapsamıyla açılır (ör. yalnız download servisi — zaten ADR-026 izinli, §2.2/e); (3) **debate (✅ TAMAMLANDI — 19/1/0 RED DOĞRULANDI) sonucu red'i kuran koşulların değiştiğini** kanıtlarsa → yeni debate + yeni ADR ile yeniden açılır; (4) **PHP sürüm hattı fiilen düşerse** (php.net çoklu-sürüm yama desteği sonlanır ya da PHP 8.x için güvenlik yaması gelmez) → ekosistem-ölümü kapısı açılır ama red **otomatik kalkmaz**. **Bugün: 1 = SAĞLANMADI (ölçüm yok — "Node = X kat hızlı" UNKNOWN), 2 = SAĞLANMADI (ADR-001/002/039 diskte, yürürlükte), 3 = debate ✅ TAMAMLANDI (19/1/0 RED DOĞRULANDI — §7.2) → koşul **SAĞLANMADI**, 4 = kanıt YOK (php.net aktif — §1.3/2) → **red geçerli; debate bu satırı güncelledi (2026-10-10).**

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **Node.js full-stack backend (red'nin hedefi)** | Tek dil (JS/TS) FE+BE; I/O-eşzamanlılık throughput'ı yüksek (§1.3-1); event-loop modeli realtime/I/O işlerinde verimli; npm ekosistemi geniş | 17.412 PHP dosyasının + ADR-010-013/022 güvenlik katmanının **tam yeniden yazımı**; CPU-görevlerinde PHP JIT önde (§1.3-1); LTS upgrade treni (§1.3-3); framework baskısı ADR-001'i ihlal eder; **kazanç ölçülmüş değil (UNKNOWN)** | Dizin `:162` gerekçesi (**PHP zorunlu**) + ADR-002 (PDO/PHP) + ADR-001 (vanilla JS) + ADR-039 (10 PHP servis) — red'in **doğrudan hedefi** |
| 2 | **PHP 8.4 backend (bugünkü uygulama)** | Kanıtlanmış katman: PDO+BCNF (ADR-002), middleware 10-adım (ADR-010-013/022), PageRouter, 17.412 dosya · PHP 8.5 + FrankenPHP ile modernizasyon yolu açık (§1.3-2) · çoklu-sürüm yama desteği | Request-isolation modeli (worker) Node'un tek-process modelinden farklı; async-I_o-native değil (kapatılıyor: FrankenPHP/Swoole §1.3-2) | **Reddedilmedi — bu, yerini alan yaklaşımdır** (ADR-002/039); bedeli §2.2'de dürüstçe yazılı (async boşluğu, worker modeli) |
| 3 | **Hibrit: PHP çekirdek + Node izole servisler** | Node yalnız I/O-izole işlerde (download :3001 ADR-026/039 PLANNED) · PHP auth/session/DB tek katman · en az geçiş | İki runtime = iki bakım treni (§1.3-3); iki dil yetkinliği (zaten var: JS frontend + PHP backend) | **Bu redin alternatifi DEĞİL — bu, CoreMusic'in mevcut/palanı uygulamasıdır** (§2.2 izinli yüzey); red onu değil, **"tamamen Node"**u reddeder |
| 4 | **Next.js/Nuxt full-stack (Node + framework SSR)** | SSR/SSG, routing, state tek framework'te | ADR-001 ihlali (framework yasağı) + 155 vanilla `.js` + ITCSS katmanlarının yok sayılması; bundle şişmesi; CSP nonce (ADR-012) ile framework runtime'ı gerilimi | ADR-001 **frozen** (framework yasağı) — red bunu da korur; ayrı debate ister |
| 5 | **Go/Rust backend (Node değil ama PHP de değil)** | Performans + tek binary | CoreMusic'te Go/Rust backend **kanıtı/şartı yok** (vault'ta yalnız C++20 ses motoru ADR-019/062 kapsamında); tam yeniden yazım bedeli aynı | Bu red **Node'u** reddeder; PHP-zorunluluğu ADR-002/039 hükmüdür — Go/Rust backend talebi **ayrı ADR** ister (düz metin, §2.1/7) |

*(İzinli uygulama alternatifi — PHP backend + Node izole servis — §3'te "reddedilmedi" olarak ayrılmadı; o, yerini alan yaklaşımdır ve bu red'in gerekçe kaynağıdır. Satır 3 "aynı şeyin adı" olarak, satır 5 "kapsam dışı" olarak durur.)*

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Karar tek yerde toplandı:** `grep R-010` → bu dosya + dizin satırı (`index.md:162`) + R-009 §6 düz metni + `rejected/index.md` (boş tablo) → "Node neden yok" sorusunun yanıtı vault'ta yazılı.
- **Kapsam korkusu giderildi:** "Node.js yasak" **değil** — Node'un izinli yüzeyleri (CI runner, `.mjs` araçlar, test `package.json`, ADR-026/039 download servisi) tek tek §2.2'de sayıldı → yanlış okuma (hem "her yerde yasak" hem "red geçersiz çünkü CI'da Node var") **önlendi**.
- **Yanlış iddia önlendi:** "Node daha hızlı" **yazılmadı** — literatür (§1.3-1) I/O'da Node, CPU'da PHP diyor ve darboğaz DB → red gerekçesi **geçiş bedeli + ölçülmemiş kazanç + LTS treni**'ne sabitlendi → Zero-Hallucination korundu.
- **Çapraz kilitlendi:** ADR-001 + ADR-002 + ADR-039 (+ADR-026 sınır, ADR-010-013/022 güvenlik katmanı) → Node full-stack **çok sayıda ADR'nin** girdisini bozar; red tek başına değil, ağ olarak durur.
- **Geri dönüş temiz:** Node full-stack backend hiç kurulmadı → `git revert` edilecek değişiklik **0** (§1.1/5-11, §5.2).
- **Ölçüm kapısı tanımlandı:** "taşınalım mı" tartışması diye beklemez — §2.3/1 **dört metrikle** açılıyor → geçiş argümanı **ölçülebilir** hâle getirildi.
- **Dizin satırı artık kaynağa sahip:** `index.md:162` "NO FILE on disk" iddiası fiilen geçersiz (bu dosya kaynaktır) → bayrak temizliği §5.1/3'te ertelendi, §7.1/1'de raporlandı.

### 4.2 Olumsuz Sonuçlar

- **"PHP zorunlu" kelimesi dar okunmalı:** dizin gerekçesi üç kelime ("PHP zorunlu") — bu dosya red'i **backend-zorunluluğu** olarak daralttı; gelecekteki biri "tek dil yasağı" sanabilir → §2.2 ile telafi edildi ama gerilim **kaldı** (bayrak temizliğiyle birlikte §7.1/1).
- **Node full-stack kazancı hiç ölçülmedi:** PoC/ölçüm yok (UNKNOWN) → §2.3/1 eşiği bugün **sağlanamaz**; red savunması **ölçüm** istiyor.
- **Ekip Node yetkinliği bilinmiyor:** literatür "tek dil = küçük ekip kazancı" der (§1.3-4) — CoreMusic'in ekip boyutu **UNKNOWN** → karşı argüman **yanıtlanmadan** duruyor (dürüst işaretlendi).
- **Download servisi hâlâ PLANNED:** Node'un izinli sınırı (ADR-026/039) **kod 0** (dizin YOK, `*.ts` = 0) → "Node ile ilk gerçek backend deneyimi" hiç yaşanmadı; red bu yüzden "tescil"tir (§1.1 ders notu).
- **Debate tamamlandı (§7.2):** 3 tur / 20 persona **çalıştırıldı** → **19/1/0 RED DOĞRULANDI** + 4 bağlayıcı şart (§5.3) → 20-persona çapraz doğrulama **var**; Tech Lead ✅ (Arch Lead ⏳).
- **Dizin/seri tutarsızlığı sürüyor:** `rejected/index.md` boş (12 red'in hiçbiri satırlanmadı) + `index.md:162` NO FILE bayrağı duruyor + `.ai/wiki/` ayna satırı doğrulanamadı → §7.1/1-3.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| **"Backend'i Node'a taşıyalım" baskısı** ("PHP yavaş/geride kaldı") | 3 (olası) | 4 (yüksek — 17.412 dosya + güvenlik katmanı + frontend kanonu aynı anda kaybolur) | §2.1 + ADR-001/002/039: kırmızı çizgi = PDO/middleware/frontend-kanonu; taşıma talebi **yeni ADR + debate + ölçüm** ister (§2.3/1) |
| **"Hız" gerekçesiyle savunma** (birinin "Node hızlı diye var" demesi) | 3 (olası) | 3 (orta — yanlış gerekçe hallucination üretir) | §1.3-1 + §2.1/3: performans iddiası **her iki yönde de** yasak → ölçüm yoksa `⚠️ VERIFICATION REQUIRED`; red geçiş-bedeli diliyle savunulur |
| **"Node yasak" yanlış okuması** (CI/araç/PLANNED download yüzeyleri görmezden gelinir) | 3 (olası) | 3 (orta — dinamik stack ilkesi gereksiz daralır) | §2.2 izinli yüzey listesi (7 madde) + `.ai/CLAUDE.md:459` satırı → yeni Node yüzeyi talebi **yeni ADR** ile açılır, "yasağı" genişletilmez |
| **LTS bakım treninin zaten var olması** (CI Node 26 — 2027'de EOL) | 4 (çok olası) | 2 (düşük — test-only yüzey) | CI runner yüzeyi redin **kapsam dışı**dır (§2.2/b); upgrade yalnız test altyapısını etkiler; production Node **0** (§1.1/8) |
| **Download servisinin hiç kurulmaması** (Node sınırı kâğıtta kalır) | 3 (olası) | 3 (orta — Deezer/YouTube indirme yeteneği gecikir) | ADR-026 şart 1a (dizin yok etiketi) + §2.3/2: Node izinli sınır **bu redle korunuyor**; servis PHP ile de kurulabilir (ADR-026 "uygulama stream (PHP) + Node opsiyonel" çerçevesi) |
| **PHP sürüm hattının düşmesi** (ekosistem-ölümü senaryosu) | 1 (nadir — php.net 8.5 + çoklu yama aktif, §1.3/2) | 4 (yüksek — tüm backend'i taşımak gerekir) | §2.3/4 kapısı: php.net yama desteği sonlanırsa kapı açılır ama red otomatik kalkmaz; o noktada Go/Rust/Node **hepsi** yeniden değerlendirilir |
| **Ölçümsüz "developer productivity" argümanı** (tek dil = hızlı üretim) | 3 (olası) | 3 (orta) | §1.3-4: tek-dil kazançları (tutarlılık, FE dev backend açar) PHP backend + paylaşımlı veri modeliyle **zaten var**; poliglot gerçeği (PHP+JS+C++20) not edildi → argüman ölçüm ister (§2.3/1-c) |

---

## 5. Uygulama (Implementation)

> Bu kayıt **salt-okunur seri**dir (`rejected/`); "uygulama" = kaydın vault'a doğru yerleştirilmesi ve denetimidir — kod değişikliği **yoktur**.

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu dosyayı `.ai/.decisions/rejected/R-010-nodejs-backend-fullstack.md` olarak yaz (şablon §1-§7, 7 bölüm dolu; **slug `index.md:162`'den grep ile alındı — dosya adı slug ile eşleşiyor** ✅) + `log.md` append ("R-010 yazıldı (debate PENDING)") | Vault Steward | 25 dk |
| 2 | **Ölçüm adımı (§2.3/1 kanıtı):** PHP 8.4 (+ FrankenPHP/Swoole) vs Node.js **aynı iş yükünde** p95/p99 + throughput + bellek + taşınma-maliyeti tahmini + LTS upgrade yükü — sayılar gelmeden "taşınalım" tartışması **açılmaz** | Performance + Backend + DevOps | bir sonraki sprint |
| 3 | **`index.md:162` `<!-- NO FILE on disk ... -->` bayrağı → DOKUNULMADI** (talimat gereği son sıfırlamaya ertelendi — rapor-only) · `rejected/index.md` § tablosu **BOŞ → DOKUNULMADI** (rapor-only) · `.ai/wiki/` ayna satırı **doğrulanamadı — `.ai/wiki/` dizini diskte YOK** (`⚠️ VERIFICATION REQUIRED`, rapor-only) | Vault Steward | son sıfırlama |
| 4 | **Debate** (3 tur / 20 persona — AGENTS.md §6.1: Expert 5 · Senior 5 · Junior 10; kanıt-öncelikli; en az 1 Expert + 1 Senior + 1 Junior zorunlu) → sonuç §5.3'e, tur kayıtları §7.2'ye yazıldı; **Tech Lead onayı** ✅ (2026-10-10) | MO + Tech Lead | ✅ TAMAMLANDI (2026-10-10) |
| 5 | **Sayfa-içi derin doğrulama turu:** (a) FrankenPHP/Swoole persistent-worker PHP performans resmî rakamları (§1.3-2 iddiasının ikinci kaynağı), (b) Node.js 26 LTS resmî EOL tarihi (nodejs.org schedule — §1.3-3), (c) ADR-026/039 download servisi PLANNED etiketinin güncel disk durumu, (d) `.ai/wiki/vault-decisions.md` iddiasının kaynağı (R-009 §1.1/3'te ayna satırı vardı — dosya bu taramada bulunamadı) | Researcher + Backend | üretim öncesi |

### 5.2 Geri Dönüş Planı

Bu karar **kod tarafında geri alınacak bir şey üretmedi** (Node full-stack backend hiç kurulmadı → `git revert` edilecek değişiklik **0**; §1.1/5-11). Geri dönüş = **yeniden değerlendirme** demektir ve yalnız §2.3 koşullarından biri yazılırsa yeni ADR ile açılır: (1) ölçüm eşikleri aşılırsa → yeni ADR (servis-başına Node geçişi ya da tam geçiş); (2) ADR-001/002/039 superseded edilirse → bu dosya `superseded` notuyla **bağlanır, düzenlenmez**; (3) debate sonucu değişirse → debate kaydı + yeni ADR; (4) PHP sürüm hattı düşerse → ekosistem-kapısı ADR'si. Vault bozulursa standart kurtarma `git checkout` + son commit (AGENTS.md §17 #10). **Reddedilen yaklaşımın kodda izi olmadığı için kullanıcı/veri etkisi YOKTUR.**

### 5.3 Debate Şartları

> **Kayıt:** ✅ **TAMAMLANDI** — debate **3 tur / 20 persona çalıştırıldı** (2026-10-10, AGENTS.md §6.1 seti — Expert 5 · Senior 5 · Junior 10, kanıt-öncelikli): sonuç **19/1/0 → RED DOĞRULANDI**. Durum `rejected` **kullanıcı onaylı red** olarak kalır; tur kayıtları §7.2'de, §2.3/3 koşulu güncellendi. Aşağıdaki madde başlıkları **plan** metnidir; **asıl tur kayıtları + bağlayıcı 4 şart** bunların altındadır.

- **Tur 1 (20 persona — bulgu, planlanan):** `index.md:162` gerçek satır (NO FILE bayrağı **dokunulmadı** → §5.1/3) · yerini alan eşleme görev varsayımıyla **çelişti** → düzeltildi (ADR-005/006/019 konu dışı, gerçek: ADR-002/001/039 — §1.1/4) · Node yüzeyi sayımı (`.mjs` 2 · `.js` 155 · `package.json` test-only · `setup-node@v4` ×3 Node 26 · download dizini YOK) · 4 sorgu / ~31 kaynak · **hız gerekçesi çıkarıldı** (I/O Node, CPU PHP, darboğaz DB) · `rejected/index.md` boş → dokunulmadı · `.ai/wiki/` ayna satırı doğrulanamadı (`⚠️ V.R.`).
- **Tur 2 (itiraz → çözüm → şart, planlanan):** (i) "Node throughput'ta önde" → darboğaz DB, CPU'da PHP önde → **Şart A: performans-iddia yasağı + ölçüm kapıları** (§2.3/1); (ii) "CI'da Node var, red çelişkili" → kapsam sınırı yazılı → **Şart B: izinli yüzey listesi bağlayıcılığı** (§2.2); (iii) "eşleme ADR-005/006/019 idi" → disk kanıtıyla düzeltildi → **Şart C: eşleme düzeltmesinin dizine yansıması** (son sıfırlama); (iv) "download servisi Node ile zaten planlı" → kapsam dışı → **şart değil, işaretle**.
- **Tur 3 (uzlaşma, planlanan):** sonuç + bağlayıcı şartlar §7'ye yazılır; red **değişmez** (kullanıcı onaylı), yalnız şartlar bağlanır.

**Tur 1 kaydı (2026-10-10 — bulgular, 20/20):** `index.md:162` gerçek satır = **162** + slug `R-010-nodejs-backend-fullstack` ("PHP zorunlu" + `<!-- NO FILE on disk -->` bayrağı **dokunulmadı** — dosya adı slug ile **eşleşti** ✅, R-009 dersi); görevdeki eşleme varsayımı **yanlıştı** (ADR-005 = Ultrathink Protocol · ADR-006 = Performance Targets · ADR-019 = Per-OS Neva Player) → doğru eşleme **ADR-002 (PDO/PHP zorunlu) + ADR-001 (vanilla JS) + ADR-039 (10 PHP + 1 Node PLANNED) + ADR-026 sınırı** (hepsi glob-doğrulandı, diskte); Node yüzeyi: `.mjs` = **2** (vault-utf8-writer + validate — araç, servis değil) · `.js` = **155** (vanilla frontend, Node runtime değil) · kök `package.json` **tek dosya test-only** (playwright/vitest/jsdom/axe — production bağımlılık **0**) · CI `setup-node@v4` ×3 + Node 26 → "Node yasak" iddiası çelişirdi → red kapsamı §2.2'de dürstüçe **"yalnız PHP backend'inin tamamen Node'a taşınması"** ile sınırlandı (izinli yüzeyler CI/`.mjs`/frontend/ADR-026 download kapsam dışı); `.ai/wiki/` dizini diskte **YOK** → ayna satırı doğrulanamadı `⚠️ V.R.`; **4 sorgu / ~31 kaynak**; red bugün hâlä doğru ama gerekçe **"hız" değil**: I/O'da Node önde, CPU'da PHP önde, darboğaz DB → hız iddiası her iki yönde yasak (**UNKNOWN**), dayanak **geçiş bedeli + LTS treni + PHP 8.5 olgunluğu**; wiki-link **22/22 diskte kırık 0**; `rejected/index.md` **boş → dokunulmadı**. **Oylama:** Expert — `architect` **kırmızı** (eşleme şartı), `security-engineer` **kabul**, `qa-engineer` **uyarı**; Senior — `devops-engineer` **kırmızı** (CI Node çelişkisi şartı), `performance-engineer`/`backend-architect` **kabul**; Junior — **9 neutral** + **2 uyarı** (`docs-writer`, `research-analyst`).

**Tur 2 kaydı (çapraz eleştiri — 4 itiraz → çözüm):** (1) eşleme hatası + CI Node çelişkisi → kırmızı kapsamı kesinleştir: "backend'in tamamen Node'a taşınması"; CI/`.mjs`/frontend Node kapsam dışı; ADR-002+001+039 eşlemesi → **Şart 1**; (2) hız argümanı yasak → dayanağı geçiş bedeli + LTS treni + PHP 8.5 olgunluğuna sabitle → **Şart 2**; (3) benchmark tek kaynak → ikinci kaynak ya da `⚠️` kalıcı → **Şart 3**; (4) Node 26 / setup-node izleme → Node LTS sürüm değişirese V.R. güncelleme kapısı → **Şart 4**.

**Tur 3 kaydı (uzlaşma — kanıt-öncelikli):** Expert `architect` (eşleme + ayrım) + Senior `devops-engineer` (CI kanıtı) kanıtları üstün → **RED DOĞRULANDI (19/1/0)**.

**Bağlayıcı 4 şart (uzlaşma — §2.3/§4.3'e bağlı):**

1. **Şart 1 — Kapsam kesinleştirme + doğru eşleme:** red yalnız **"PHP backend'inin tamamen Node'a taşınmasını"** reddeder; izinli yüzeyler kapsam dışı (CI runner Node 26 · `.mjs` araçlar · tarayıcı Vanilla JS · ADR-026/039 download servisi — §2.2); yerini alan eşleme **ADR-002 + ADR-001 + ADR-039 (+ADR-026 sınırı)** olarak sabit (§1.1/4, §2.2, §6). **UYGULANDI.**
2. **Şart 2 — Gerekçe sabitleme (hız yasağı):** red gerekçesi **geçiş bedeli + LTS bakım treni + PHP 8.5 olgunluğu**'na sabitlendi; "Node = X kat hızlı" iddiası **her iki yönde de** yasak → ölçüm yoksa `⚠️ VERIFICATION REQUIRED` (§1.3-1, §2.1/3, §4.3/2). **UYGULANDI.**
3. **Şart 3 — Benchmark çift kaynak / `⚠️` kalıcı:** §1.3-1 performans rakamları (dev.to/itoverdose/mehdibafdil demeti) **tek kaynak grubudur** → ikinci bağımsız kaynakla doğrulanana dek `⚠️ VERIFICATION REQUIRED` **kalıcı**; §5.1/5 sayfa-içi derin tur bu şartın kapısıdır. **UYGULANDI (⚠️ kalıcı — ikinci bağımsız kaynak bugün YOK).**
4. **Şart 4 — Node LTS izleme kapısı:** CI `setup-node@v4` ×3 / Node 26 yüzeyi red'in **kapsam dışı** kanıtıdır; Node LTS sürümü değişirese (26 → sonraki LTS) §1.1/9 + §1.3-3 + §4.3/4 satırları **`⚠️ VERIFICATION REQUIRED` ile güncellenir** (vault-sync kapısı — §5.1/5-b). **UYGULANDI (kapı yazılı).**

*Ek not (plan Tur 2/iv): "download servisi Node ile zaten planlı" maddesi **şart değil** — §2.2/(e) kapsam dışı işareti olarak durur.*

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../index]] | Karar dizini — bu red'in kaydı §5 `:162` (slug + "PHP zorunlu" + NO FILE bayrağı §5.1/3) |
| [[../accepted/ADR-002-pdo-mandatory-no-orm]] | **Yerini alan (birincil):** PHP 8.4 + PDO zorunlu, ORM yasak (`:24`, `:28`) + §1.3 (OWASP A05:2025, CVE-2025-14180) — Node full-stack bu hükmü doğrudan ihlal eder |
| [[../accepted/ADR-001-vanilla-js-itcss]] | **Yerini alan:** frontend Vanilla JS + ITCSS, framework yasağı — Node full-stack backend baskısı Next.js/Nuxt'a iter, 155 `.js` + ITCSS katmanları çöker |
| [[../accepted/ADR-039-7-service-platform-architecture]] | **Yerini alan:** 11 servis haritası = 10 PHP + 1 Node.js (download, PLANNED; `:121/:154`) — red mevcut haritayı tesciller |
| [[../accepted/ADR-026-download-service-architecture]] | **Node'un izinli sınırı:** download :3001 Node.js+TS — dizin YOK, `*.ts` = 0 (`:52`) → PLANNED; bu red onu **korumaz, ihlal etmez** |
| [[../accepted/ADR-019-per-os-neva-player]] | **Dürüst ayrıştırma:** IAudioBackend = **ses** backend'i (C++ Bridge+Adapter), web backend'i ile ilgisiz → görev varsayımındaki eşleme §1.1/4'te düzeltildi |
| [[../accepted/ADR-010-csrf-protection-strategy]] · [[../accepted/ADR-011-session-management]] · [[../accepted/ADR-012-csp-nonce-strict-dynamic]] | Middleware/oturum/CSP katmanı — Node geçişinde **tamamı yeniden kanıtlanmak** zorunda kalır (§2.1/2) — ADR-011/012 dosyaları diskte VAR ✅ (glob doğruladı) |
| [[../accepted/ADR-006-performance-targets]] | Ölçüm altyapısı — §2.3/1 p95/p99 kapıları ADR-006 hedefleriyle karşılaştırılır (görev varsayımında "tek dil" sanıldı — konu: CWV/API/audio hedefleri) |
| [[../accepted/ADR-005-ultrathink-protocol]] | Zero-Hallucination standardı — §1.3 "hız" iddiasının iki yönlü yasağı bu protokolün gereği (görev varsayımında "PHP-first" sanıldı — konu: ultrathink protokolü) |
| [[../accepted/ADR-003-multi-db-bcnf]] · [[../accepted/ADR-040-database-authority]] | 18 domain DB + sahiplik matrisi — Node ORM/prisma vb. erişim bu kararları da bozar (düz metin, §2.1/2 kapsamı) |
| [[R-009-single-database-architecture]] | Seri kardeşi — format referansı (künye, §1.1 kanıt tablosu, §2.3 şart satırı, §7.1 rapor deseni); `:236`'da R-010'u "eski seri düz metin" olarak anar |
| [[R-001-redux-style-state-management]] · [[R-006-laravel-eloquent-orm]] | Seri kardeşleri — R-001: framework-yasağı deseni (ADR-001 bağı) · R-006: kapsam daraltma dersi (§2.1/7) |
| [[../../CLAUDE]] | `.ai/CLAUDE.md` — `:75` (PHP 8.4 temel teknoloji), `:459` (Node.js = Download Service, LTS, ✅ Evet), `:803` (dinamik stack ilkesi) — red'in izinli-yüzey okumasının kaynağı |
| [[../../AGENTS]] | §4 (Backend = PHP 8.4/PDO/PageRouter · QA = PHPUnit+Vitest+Playwright), §5 domain boundaries, §10 escalation, §17 #10 vault kurtarma |
| [[../../brain]] | Mimari karar özeti — ADR satırları vault-sync ile tazelenir |
| [[../../.templates/adr/adr-template]] | Guardrail #16 — bu dosyanın §1-§7 iskeleti + §1.3 9 alan kaynağı |
| [[../../log]] | Append-only kayıt defteri (bu işlem 1 satır append → §7.1/4) |
| [[../../index]] | Master katalog — ADR/red kayıtları |
| Dizin satırı | `index.md:162` — slug otoritesi + NO FILE bayrağı (§5.1/3); **dosya adı = slug** ✅ (R-009 dersi uygulandı) |
| Debate şartları | Bu dosya **§5.3** — debate ✅ **TAMAMLANDI** (3 tur / 20 persona, 19/1/0 RED DOĞRULANDI); **4 bağlayıcı şart §5.3** + tur kayıtları §7.2 |
| Debate sonucu (4 şart) | **RED DOĞRULANDI (19/1/0)** — Şart 1 kapsam kesinleştirme + doğru eşleme ✅ · Şart 2 gerekçe sabitleme (hız yasağı) ✅ · Şart 3 benchmark çift kaynak / `⚠️` ✅ · Şart 4 Node LTS izleme kapısı ✅ |
| Düz metin (disk kanıtı yok / doğrulanamadı — wiki-link KURULMAZ) | `.ai/wiki/vault-decisions.md` (R-009 §1.1/3 ayna satırı — `.ai/wiki/` dizini bu taramada **YOK** → `⚠️ VERIFICATION REQUIRED`) · Go/Rust backend (vault'ta karar yok — §3/5) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali (kullanıcı onaylı — "R-010'u sıfırdan yaz") | 2026-10-09 | ✅ |
| Tech Lead | — | 2026-10-10 | ✅ |
| Arch Lead | ⏳ | ⏳ | ⏳ |

### 7.1 Rapor Satırları (bu işleme dair — rapor-only, vault'a dokunulmadı)

| # | Konu | Durum |
|---|------|-------|
| 1 | **Slug eşleşmesi:** dosya adı `R-010-nodejs-backend-fullstack.md` = `index.md:162` slug'ı ✅ (R-009 yeniden-adlandırma dersi önlendi) · `<!-- NO FILE on disk 2026-10-06 -->` bayrağı **DOKUNULMADI** → son sıfırlamaya ertelendi | ✅ / ertelendi |
| 2 | **`rejected/index.md` BOŞ** (v1.0.1, `total: 12`, tablo boş) → **DOKUNULMADI** (rapor-only) | ⚠️ ertelendi |
| 3 | **`.ai/wiki/` aynası doğrulanamadı:** `.ai/wiki/` dizini diskte YOK → `wiki/vault-decisions.md:172`-tarzı ayna satırı **doğrulanamadı** → `⚠️ VERIFICATION REQUIRED` (§1.1/3) | ⚠️ V.R. |
| 4 | **log.md:** 1 satır append ("R-010 yazıldı (debate PENDING)") — append-only, son satır `\n` ile bitiyordu (son byte=10) → bölme gerekmedi | ✅ |
| 5 | **Görev varsayımı düzeltmesi:** "yerini alan ADR-005 (PHP-first?) / ADR-006 (tek dil?) / ADR-019 (IAudioBackend)" — glob kanıtı: gerçek konular Ultrathink Protokolü / Performance Targets / Per-OS Neva Player (ses backend'i) → **eşleme ADR-002/001/039 (+ADR-026 sınır) ile düzeltildi** (§1.1/4, §6) | ✅ dürüst düzeltme |
| 6 | **Debate kaydı:** 3 tur / 20 persona (2026-10-10) → **19/1/0 RED DOĞRULANDI** + **4 bağlayıcı şart** (§5.3) + **Tech Lead ✅** (2026-10-10) — plan başlıkları §5.3'te korundu, tur kayıtları §5.3'e eklendi; §2.3/3 + §4.2/5 + §6 güncellendi | ✅ |
| 7 | **log.md append (debate):** tek satır "R-010 debate 3/20 kaydedildi (19/1/0 RED DOĞRULANDI) + Tech Lead ✅ + 4 şart" — `vault-utf8-writer.mjs append` (@file modu, bayt-seviyesi); son satır `\n` ile bitiyordu (lastbyte=10) → bölme gerekmedi | ✅ |

### 7.2 Debate Kaydı

✅ **TAMAMLANDI** — 3 tur / 20 persona **çalıştırıldı** (2026-10-10, AGENTS.md §6.1 seti; kanıt-öncelikli, en az 1 Expert + 1 Senior + 1 Junior): **19/1/0 → RED DOĞRULANDI**. Tur 1 bulguları + Tur 2 (4 itiraz → çözüm) + Tur 3 uzlaşması §5.3'te; **4 bağlayıcı şart** (kapsam kesinleştirme + doğru eşleme · gerekçe sabitleme/hız yasağı · benchmark çift kaynak `⚠️` · Node LTS izleme kapısı) §5.3/§2.3/§4.3'e bağlıdır. Durum **rejected** (kullanıcı onaylı red) olarak kalır. **Tech Lead: ✅ (2026-10-10)** · Arch Lead: ⏳.

---

**R-010 v1.0.0 | 2026-10-09 | Created (debate PENDING)**
**R-010 v1.0.0 | 2026-10-10 | Debate ✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 RED DOĞRULANDI — 4 şart §5.3, Tech Lead ✅)**
*Authority: R-010 Red Kararı — CoreMusic Rejected Decision Record*
*Mode: Red Team · Human Mode · Truth Mode*
