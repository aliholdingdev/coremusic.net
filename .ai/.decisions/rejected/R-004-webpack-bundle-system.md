---
title: "CoreMusic — R-004: Webpack / Bundle Sistemi (REDDEDİLDİ — Over-engineering)"
type: "architecture-decision"
category: "frontend"
date: "2026-10-02"
updated: "2026-10-02"
version: "1.0.0"
status: "rejected"
authority: "SSOT — R-004 red kararı: CoreMusic frontend yüzeyine webpack ve eşdeğer bundle/build sistemi GIRMEZ. Gerekçe: karar dizini index.md:129 'Webpack | Over-engineering' + ADR-004:83 'ayrı domain bundle'ı, webpack/vite/build sistemi YASAK' + ADR-004:99 tek build disiplini + ADR-001 Vanilla JS ES6+ ve 'npm ile frontend runtime paketi varsayılan kapalı' (ADR-001:85). Yerini alan: ADR-001-vanilla-js-itcss + ADR-004-multi-domain-spa (ikisi de diskte). Bu dosya salt-okunur seridir (rejected/) — değiştirilmez, yalnız referanslanır."
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 19/1/0 RED DOĞRULANDI)"
---

# CoreMusic — R-004: Webpack / Bundle Sistemi (Rejected)

> **Durum:** rejected (**debate ✅ TAMAMLANDI — RED DOĞRULANDI**) — **Tarih:** 2026-10-02 — **Debate:** ✅ **TAMAMLANDI** (3 tur / 20 persona — 19 kabul / 1 çekimser / 0 red → sonuç §5.3 + §7) — **Tech Lead:** ✅ — **Arch Lead:** ⏳
> **Seri:** `.ai/.decisions/rejected/` (salt-okunur) — **Slug:** `R-004-webpack-bundle-system` (dizin otoritesi: [[../index]] **satır 129** — dosya adı ile birebir hizalı ✅; 2026-10-02 glob doğrulaması: `rejected/` içinde `R-004*` = **0 dosyaydı** → bu işlemde yazıldı)
> **Dizin satırı:** `| [[R-004-webpack-bundle-system]] <!-- dead-link: R-004-webpack-bundle-system no source 2026-09-24 --> | Webpack | Over-engineering |` — `<!-- dead-link ... -->` bayrağı **bu işlemde DOKUNULMADI** (temizlik son sıfırlamaya ertelendi → §5.1/3 + §7.1/1)
> **İlgili kararlar:** [[../accepted/ADR-001-vanilla-js-itcss]] (yerini alan — Vanilla JS ES6+ + ITCSS; `:32` bu red'i "R-004 Webpack" diye sayar, `:85` npm frontend paketi kapalı) · [[../accepted/ADR-004-multi-domain-spa]] (`:83` "webpack/vite/build sistemi YASAK", `:99` tek build disiplini, `:159` ayrı-bundle alternatifi bu red ile yasaklanır) · [[../accepted/ADR-012-csp-nonce-strict-dynamic]] (build ekleme = script yüzeyi/CSP etkisi) — karar dizini [[../index]] §5.
> **R-001/R-002/R-003 dersi uygulandı:** wiki-link slug'ları **tahmin edilmedi** — her hedef `Test-Path` / glob ile doğrulandı (ADR-001/004/012 + brain.md + R-001/R-002/R-003 = diskte VAR).

---

## 1. Bağlam (Context)

CoreMusic frontend katmanı (A3 / K10-K11) ADR-001 ile **saf Vanilla JS ES6+ + ITCSS + BEM**'e bağlanmıştır; karar dizini bundle/build sistemini çoktan reddetmiştir (`index.md:129` — "Webpack | Over-engineering") ama **red metni hiç yazılmamıştır**: elde yalnız dizin satırı + `<!-- dead-link ... -->` notu + ADR-001/004 içindeki parmak izleri vardır. Bu dosya, o satırın **gerekçeli red kaydıdır** — yeni bir karar değil, mevcut red'in (a) gerekçe, (b) güncel web araştırması, (c) yerini alan eşleme, (d) yeniden değerlendirme koşulu ile sıfırdan yazımıdır.

### 1.1 Mevcut Durum (disk kanıtı — dürüst etiket, 2026-10-02 taraması)

| # | İddia | Kanıt | Etiket |
|---|-------|-------|--------|
| 1 | Red kaydı var mı? | [[../index]] `:129` → `[[R-004-webpack-bundle-system]]` + "Webpack" + "Over-engineering" + `<!-- dead-link ... no source 2026-09-24 -->` | ✅ **KAYITLI** (dizin satırı tek kanıt; red metni bu işlemde yazılıyor) |
| 2 | Bu işlem öncesi dosya var mıydı? | `.ai/.decisions/rejected/` içinde `CLAUDE.md`, `index.md`, `R-001-*`, `R-002-*`, `R-003-*` ; `R-004*` = **0 dosya** | ❌ **YOKTU** → bu işlemde yazılıyor (klasör zaten var — `rejected/` oluşturulmadı) |
| 3 | Red gerekçesi başka yerde yazılı mı? | ADR-004 `:38` (ayrı bundle maliyeti + R-004 reddi) · ADR-004 `:53` ("ayrı bundle YOK … R-004 webpack reddi korunur") · ADR-004 `:83` (§1.4 kısıt 1 — "webpack/vite/build sistemi YASAK") · ADR-004 `:99` (§2.1/1 "Tek build disiplini korunur") · ADR-004 `:159` (alternatif 1 ret satırı) · ADR-001 `:32` (dizin §5 sayımı) · ADR-001 `:201` (§6 sayımı) | ✅ **7 referans** — ama hiçbiri red'in kendi metni değil, **gerekçe ailesi** |
| 4 | Yerini alan kararlar diskte? | [[../accepted/ADR-001-vanilla-js-itcss]] **VAR** (`status: accepted`) · [[../accepted/ADR-004-multi-domain-spa]] **VAR** (`status: accepted`) | ✅ **IMPLEMENTED** (2026-10-02 `Test-Path` = True/True — glob ile doğrulandı) |
| 5 | §1.4 kısıt 1 diskte mi? | ADR-004 `:81-83` kısıtlar tablosu 1. satır: "Vanilla JS ES6+, ITCSS; ayrı domain bundle'ı, **webpack/vite/build sistemi**, React/Vue/jQuery **YASAK**" | ✅ **DISKTE** (kısıt bağlayıcı) |
| 6 | Kod yüzeyinde build tool izi? | `package.json` (kök, **61 bayt**) → tek alan `playwright: ^1.62.1`, **`scripts`/`build` YOK** · `assets.coremusic.net/**/*.js|mjs|json` taraması `webpack|esbuild|rollup|vite` → **0** · `shared/` + `.github/` (`*.json/*.yml/*.php`) → **0** | ✅ **0 build tool izi** (yapı: doğrudan sunulan ES modülleri; build zinciri hiç kurulmamış) |
| 7 | Sahte/yanltıcı izler? | `.ai/ui-design/tokens/tokens-*.json` içinde `"Build: Vite / Webpack"` = **Figma tasarım token ADI** (94x20 kutu etiketi — bağımlılık değil) · `assets.coremusic.net/js/components/primitives/*.spec.js` → `@requires vitest + jsdom (paket henüz kurulu değil)` | ⚠️ **bağımlılık DEĞİL** — vitest **PLANNED, kurulu değil** (test çalıştırıcı; bundle/build zinciri değil) |
| 8 | `rejected/index.md` durumu? | Dosya **VAR** (757 bayt, v1.0.1, `total: 12`) ama § tablosu **BOŞ** — 12 red'in hiçbiri satırlanmamış | ⚠️ **BOŞ** → bu işlemde **dokunulmadı** → §7.1/2 |
| 9 | Debate sonucu? | 3 tur / 20 persona — 19 kabul / 1 çekimser / 0 red | ✅ **RED DOĞRULANDI** (3 şart §5.3 · debate kaydı §7) |
| 10 | Araştırma protokolü diskte? | `.claude/skills/prompt-maker/references/10-web-research-protocol.md` → `Test-Path` = **True** | ✅ OKUNDU (§1.3 bu protokolle üretildi) |

> **Ders notu:** bu red **hiçbir zaman kodda denenmedi** — build tool kod yüzeyinde 0 (§1.1/6); "reddedildi" = "hiç kurulmadı ve kurulmasına izin verilmedi". ADR-001 `:32` bu red'i (2026-09-24 tarihli) "mevcut" gibi saysa da dosya o tarihte yazılmamıştı — parmak izi, metnin yerine geçmez.

### 1.2 Sorun Tanımı

1. **Red kararı kanıtsız duruyor.** `index.md:129` bir sonuç cümlesi ("Over-engineering") ama **ne 2025-26 ekosistem kanıtı (build tool yaşam döngüsü, no-build olgunluğu) ne yeniden değerlendirme koşulu ne yerini alan eşleme** yazılı — gelecekteki biri "webpack neden yok, bugün de mi yok, Vite/esbuild de mi yasak, ne zaman tekrar sorulur?" sorusuna vault'tan cevap bulamıyor.
2. **Gerekçe ailesi parçalı.** Uzun gerekçe ADR-004 `:38/:53/:83/:99/:159` ve ADR-001 `:32/:85/:201` içinde dağınık; red'in kendi dosyası olmadığı için `grep R-004` sonucu "gerekçe = başka ADR'nin satırı" düzeyinde kalıyor.
3. **Güncellik sorunu + ters kanıt riski.** 2025-26'da build araçları **durmadi, yeniden yazıldı** (Vite 8 → Rolldown, Rspack, Turbopack): "webpack ölüyor" naive anlatısı **doğrudan yanlış** çıkabilir (§1.3 — webpack hâlâ aylık sürüm yayınlıyor). Red'in dayanağının **"ölmüş araç" değil, "CoreMusic için over-engineering + tek build disiplini"** olduğu açıkça yazılmazsa karar yanlış gerekçeyle savunulur.
4. **Koşul tanımsız.** "Hangi durumda bir build sistemi tekrar gündeme gelir?" (ölçülebilir performans/ihtiyaç kanıtı, ADR-001 revizyonu, no-build'in yetersiz kalması) **hiç belgelenmedi** → yeniden değerlendirme tetikleyicisi tanımsız.

### 1.3 Web'den Araştırma Raporu & Sonuçları

> Protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (öncelik: resmî doküman → vendor → bağımsız blog; her iddiaya kaynak). Araştırma 2026-10-02'de yapıldı — **3 sorgu**.

| Alan | Değer |
|------|-------|
| Web Search **Query** | (1) "webpack 2025 2026 maintenance mode decline legacy bundler status" → (2) "Vite adoption 2025 2026 default build tool esbuild rolldown no-build plain ESM trend native ES modules production" → (3) "no-build plain native ES modules production trend 2025 2026 import maps zero JavaScript build step" |
| Web Search **Konusu** | **(1)** webpack'in 2025-26 bakım/degisim durumu (emeklilik iddiası doğru mu) · **(2)** modern build tool benimsenmesi (Vite/Rolldown, esbuild, Rspack/Turbopack) ve no-build/plain-ESM eğilimi · **(3)** native ES modüller + import maps ile build'siz üretim olgunluğu ve sınırları |
| Web Search **Bağlam** | CoreMusic: ADR-001 (Vanilla JS ES6+ — diskte; `:85` npm frontend runtime paketi **kapalı**), ADR-004 `:83/:99/:159` (webpack/vite/build sistemi yasağı + tek build disiplini), ADR-012 (strict CSP — script yüzeyi); kodda build tool **0** (§1.1/6); hedef soru — *"Red bugün hâlâ doğru mu, modern build araçları (Vite/esbuild/Rolldown) bu kararı geçersiz kıldı mı, no-build trendi red'i mi destekliyor, ters kanıt var mı?"* |
| Web Search **Kısa Açıklama** | **(1)** webpack **ölü değil ama varsayılan değil**: 2026 roadmap (webpack.js.org, 2026-02-04), 5.111 sürümü (2026-09-14), npm kaydı "alive" (son yayın 2026-09-18), ~14.808 bağımlı paket; aynı zamanda State of JavaScript'te "yeniden kullanma isteği" ~%26'ya düşmüş, CRA şubat 2025'te deprecate edildi, yeni projeler Vite/Turbopack'e gidiyor. **(2)** Vite 8 (2026-03-12) esbuild+Rollup yerine **tek Rust bundler Rolldown** getirdi (10-30x hız, 65M haftalık indirme); Rolldown 1.0 (2026-05-07); Rspack webpack-uyumlu Rust alternatifi. **(3)** no-build/native-ESM gerçek bir hareket: import maps **Baseline** (Chrome 89+/Firefox 108+/Safari 16.4+), `type="module"` 2018'den beri yerel; ama **dürüst sınır**: 500+ modülde tree-shaking/code-splitting bundler'ın işidir, TypeScript/JSX build ister, import waterfall keşif gecikmesi üretir. |
| Web Search **Uzun Açıklama** | **(1)** webpack.js.org 2026 roadmap açıkça "webpack continues to be a stable and well-supported choice" der, webpack 6'ya zemin hazırlıyor (tekilleştirilmiş minimizer'lar, evrensel target/ESM output, native CSS/HTML/TS); InfoQ (2026-03-11) aynı roadmap'ü "modernizasyon, rakiple rekabet değil" alıntısıyla aktarır. Hivebook (2026-08-31 doğrulaması): `latest` 5.110.2 (2026-08-30), v6 **yok**, aylık minor'lar sürüyor — "legacy workhorse" sözü **benimsemeyle** ilgili, **bakımla değil**. Karşı kanıt: nazarboyko.com (2026-06-02) State of JavaScript verisiyle webpack'i "yeniden kullanma isteği %36 → %26" diye alır, "merkez ağırlığı taşındı; yeni proje webpack ile başlamaz" der; Create React App 2025-02-14'te deprecate edildi, Next.js 16'da Turbopack varsayılan, Angular 17+ esbuild'e geçti. **(2)** Vite 8 duyurusu: iki motor (esbuild dev + Rollup prod) tek motorda birleşti; Rolldown-1.0 (VoidZero) Rust + Rollup-plugin-uyumu, Linear 46s→6s üretim build'i; var.gg bağımsız ölçümü production build'de ~13x hız, dev server'ın **değişmediğini** dürüstçe yazar; typescript.news State of JS 2025: Rolldown kullanımı %1 → %10. **(3)** no-build kanıtı: phpied.com (2026-01-04) "200 satırlık build.js, webpack/vite/rollup yok" der ama **content-hash cache-busting için yine esbuild minify** çağırır (yani tam build'sizlik değil); vivianvoss "98% native ESM, bundle 2012 problemini 2026 fiyatıyla çözüyor" der; titouan.dev (2026-07-16) buildless sayfada **176 isteklik import waterfall** ölçer ve `modulepreload`/`?bundle` ile düzelttiğini anlatır; dev.to (2026-06-04) MIME/CORS tuzaklarını ve "50 dosya = 50 istek, HTTP/2'de tolere edilebilir ama Lighthouse bütçeli marketing sitesinde bundler kazanır" sınırını yazar. |
| Web Search **Paragraf Veri Uzun** | 3 sorgu: **(1)** webpack.js.org/blog/2026-02-04-roadmap-2026 · webpack.js.org/blog/2026-09-14-webpack-5-111 · github.com/webpack/webpack/discussions/17922 · lastseen.dev/npm/webpack (alive, 2026-09-18, 14.808 bağımlı) · hivebook.wiki webpack (2026-08-31: 5.110.2, v6 yok) · infoq.com webpack 2026 roadmap · nazarboyko.com (State of JS %26 retention, CRA deprecate) · pkgpulse vite-vs-rspack-vs-webpack-2026 (webpack ~25M haftalık, Vite ~40M) — **8**. **(2)** vite.dev/blog/announcing-vite8 (2026-03-12, 65M haftalık) · github vitejs/vite announcing-vite8 (aynı metnin repo kopyası) · voidzero.dev/posts/announcing-rolldown-1-0 (2026-05-07) · vite.dev/guide/rolldown · typescript.news Vite 8 beta (Rolldown %1→%10) · var.gg Vite 8 benchmark (~13x prod build) · pkgpulse rolldown-vs-esbuild-2026 · madelinemiller.dev 2025 JS ecosystem ("2026 = tam ESM yılı") — **8** (1 tekrar). **(3)** phpied.com maximally-minimal-build-process (2026-01-04) · vivianvoss.net native ES modules · github webjsdev no-build-via-jspm-io (2026-05-27, esbuild'i runtime'dan çıkardı) · github cloudstreet-dev/The-Zero-Build-Movement · esmodules.com/import-maps (Baseline) · titouan.dev flattening-esm-waterfall (2026-07-16, 176 istek) · dev.to zero-build-tools-2026 (2026-06-04) · dev.to micro-frontends-import-maps (2026-07-25) — **8**. **Toplam ~24 benzersiz kaynak** (1 tekrarlı). |
| Web Search **Sonucu** | **(1) Red destekleniyor — ama gerekçe düzeltmeli:** webpack'in "emekli/bakım dışı" olduğu iddiası **DOĞRULANMADI** (aylık sürüm + 2026 roadmap + alive npm kaydı — 4 kaynak); "yeni projede varsayılan olmaktan çıktığı / retention'ın düşüğü" iddiası **destekleniyor** (State of JS %26, CRA deprecate, Vite/Turbopack varsayılan — 4 kaynak) → red'in doğru dayanağı **araç ömrü değil, CoreMusic'e uygunluk**tır. **(2) Red destekleniyor:** modern build araçları da **npm kurulumu + config + build zinciri** demektir (Vite 8 Node 20.19+ ister, Rolldown native binary indirir) → ADR-001 `:85` (frontend npm runtime kapalı) ve ADR-004 `:83` (webpack/**vite**/build sistemi yasak) ile **doğrudan çakışır** (kaynak: vite.dev ×2, voidzero, pkgpulse — 4). **(3) Red destekleniyor (ana destek):** no-build/native-ESM eğilimi CoreMusic'in **zaten** bulunduğu yeri doğrular — import maps Baseline, `type="module"` yeterli, tree-shaking/splitting 50+ modülde gereksiz (kaynak: vivianvoss, dev.to, phpied, cloudstreet, esmodules — 5). **İtiraz/karşıt bulgu (dürüst):** (i) **webpack "emekli" tezi YANLIŞ** — 5.111 (2026-09-14) ve 2026 roadmap canlı; "over-engineering" gerekçesi **araç sağlığına değil, proje boyutuna** dayanmalı; (ii) no-build'in **gerçek bedeli** var: import waterfall (176 istek örneği), content-hash/cache-busting'in elle ya da minify script'iyle yapılması, TypeScript/JSX'in build istemesi, Lighthouse bütçesinde 50 istek dezavantajı → §4.2'ye yazıldı; (iii) "tam build'siz" iddiası **mutlak değil** (phpied örneği minify+hash için esbuild çağırıyor) → red "build zinciri kurma"yı yasaklar, "byte optimizasyonu yapılmasın" demez (§2.2). |
| Web Search **Alınan Karar** | **Red (R-004) YÜRÜRLÜKTE KALIR.** (a) **webpack, Vite, esbuild, Rollup, Rolldown, Rspack, Turbopack ve eşdeğer herhangi bir bundle/build zinciri** (npm `build` script'i, `webpack.config.js`, `vite.config.*`, `rollup.config.*`, ön-işlemci/bundler pipeline'ı) CoreMusic frontend yüzeyine **GİRMEZ** — ADR-001 `:85` (frontend npm runtime paketi varsayılan kapalı) + ADR-004 `:83` (kısıt 1: "webpack/**vite**/build sistemi YASAK") + ADR-004 `:99` (tek build disiplini) ile doğrudan çakışır; red'in dayanağı 2026'da da geçerlidir ama **"webpack öldü" değil** — "bu proje boyutu ve mimarisi için over-engineering" (dizin `:129` gerekçesi). (b) Frontend yüzeyi **doğrudan sunulan Vanilla JS ES6+ modülleri + ITCSS + BEM** ile sürer (ADR-001/004 — yerini alan §2): import yolları görece (`./x.js`), gerekirse import maps ve `modulepreload` **build zinciri olmadan** değerlendirilir; byte optimizasyonu (minify/cache-hash) ihtiyacı ölçülürse **ADR-001 revizyonu** ile, build sistemi kurulmadan (yalnızca dev-time araç, §2.3/2) ele alınır. (c) **Yeniden değerlendirme koşulu** (§2.3) yazılmadan bu red **otomatik olarak güncellenmez**; "vite/popüler" argümanı tek başına koşul sayılmaz. |
| Web Search **Sonuç** | **3/3 araştırmada red desteklendi** (1 webpack durumu + 2 modern build benimsenmesi + 3 no-build olgunluğu); **1 ters kanıt dürüstçe yazıldı**: webpack emekli değil (aylık sürüm/roadmap/alive) → red'in gerekçesi "over-engineering + tek build disiplini" olarak sabitlendi, araç-ölümü argümanı **kullanılmadı**. **Üç açık işaretlendi:** (i) no-build'in bedeli (waterfall/cache-busting/TS) §4.2'ye taşındı · (ii) vitest test aracı **PLANNED/kurulu değil** (§1.1/7 — build zinciri sayılır mı sorusu açık) · (iii) "0 build tool" etiketi = **önleme**, kurtarma değil (§1.1/6). **Kaynak sayısı: 3 sorgu; §1.3'te adı geçen benzersiz kaynak ~24.** |

### 1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-001 accepted (frontend paket yüzeyi) | Vanilla JS ES6+ + ITCSS 9 katman + BEM; `:85` "npm ile frontend'e runtime paketi eklenmesi **varsayılan olarak kapalıdır**; herhangi bir npm bağımlılığı eklemek bu ADR'nin revizyonunu gerektirir" → bundler kurmak npm bağımlılığı ister → **ADR-001 revizyonu** şart (bu dosya düzenlenmez, AGENTS.md §25.3 kural 2) |
| ADR-004 §1.4 kısıt 1 (`:83`) | "Vanilla JS ES6+, ITCSS; ayrı domain bundle'ı, **webpack/vite/build sistemi**, React/Vue/jQuery **YASAK**" — kısaltma değil, **doğrudan bağlayıcı satır**; domain farklılığı yalnız route + konfigürasyon + tema katmanında |
| ADR-004 §2.1/1 (`:99`) + alternatif 1 (`:159`) | "Tek build disiplini korunur … R-004 (webpack — over-engineering) reddine aykırıdır"; `:159` ayrı bundle alternatifi "R-004 reddi bu alternatifi doğrudan yasaklar" der → bu red, ADR-004'ün alternatif-ret gerekçesinin **kaynağıdır** |
| Kod yüzeyi gerçeği | Build tool = **0 iz** (`package.json` 61 bayt, build script yok; `assets.coremusic.net` + `shared/` + `.github/` taraması 0 — §1.1/6) → red bir "kaldırma" değil, **girişi engelleme** kararıdır; geri dönüş planı kod tarafında işlem gerektirmez (§5.2) |
| ADR-012 strict CSP | Build zinciri = ek npm/native-binary tedarik yüzeyi (Rolldown/Vite binary indirir — §1.3-2) + `assets.coremusic.net`'e eklenecek paketlenmiş script yüzeyi → CSP nonce/`script-src 'self'` disipliniyle ek denetim ister |
| Araştırma protokolü | §1.3 `10-web-research-protocol.md` ile üretildi; **webpack emekli tezi reddedildi** (ters kanıt) — red gerekçesi buna göre yazıldı (§2) |

---

## 2. Karar (Decision)

**R-004 REDDEDİLMİŞTİR: webpack ve eşdeğer herhangi bir bundle/build sistemi (Vite, esbuild, Rollup, Rolldown, Rspack, Turbopack, Parcel, `npm run build` zinciri dahil) CoreMusic frontend yüzeyine alınmaz.** Karar `index.md:129`'da bugünden vardı; bu dosya onu gerekçelendirir: red, ADR-001'in frontend-paket-yüzeyi sınırının (`:85`) + ADR-004'ün §1.4 kısıt 1'inin (`:83` — "webpack/**vite**/build sistemi YASAK") + tek build disiplininin (`:99`) **build-zinciri ayağıdır** ve 2026-10-02 web araştırması (§1.3) red'in **bugün hâlâ doğru olduğunu** doğrulamıştır — webpack'in kendisi ölmüş olduğu için değil (ters kanıt: aylık sürüm + 2026 roadmap), **bu proje boyutu ve mimarisi için over-engineering** olduğu için.

### 2.1 Neden Bu Seçenek?

1. **İlke tutarlılığı (kanıtlı):** ADR-001 frontend paket yüzeyini kapatır (`:85`); ADR-004 `:83` aynı yasağı "webpack/**vite**/build sistemi" diye yazar; ADR-004 `:159` ayrı bundle alternatifini bu red'e dayandırır; ADR-001 `:32/:201` bu red'i dizinde sayar — ayrı bir karar değil, **aynı ilkenin build-zinciri ayağı**.
2. **İhtiyaç kanıtı yok (kanıtlı):** kodda build tool **0** (§1.1/6 — `package.json` build script'i yok, `assets.coremusic.net` doğrudan ES modülü olarak sunuluyor); "build gerekli" şikayeti vault'ta belgeli değil (**UNKNOWN**) → boş yüzeye zincir girmez.
3. **no-build trendi lehine (kanıtlı):** import maps Baseline, `type="module"` 2018'den beri yerel; 50+ modül olmayan yüzeylerde tree-shaking/splitting "karmaşıklığı çözmüyor, karmaşıklığın kendisi" (§1.3-3, 5 kaynak) → CoreMusic'in mevcut yaklaşımı endüstri yönüyle **çelişmiyor**, örtüşüyor.
4. **Tedarik + bakım maliyeti (kanıtlı):** build zinciri = npm kurulumu + native binary (Rolldown/Vite) + config + CI dakikası + sürüm takibi; ADR-001 §1.3 verisi (npm malware dalgası) ve ADR-004 `:83` bu maliyeti zaten reddetmiş (§1.3-2, 4 kaynak).
5. **Ters kanıt maskelemedi (dürüst):** webpack **aktif bakım altında** (5.111 — 2026-09-14, 2026 roadmap) → red gerekçesi "araç ölmüş" üzerine **kurulmadı**; dayanak proje-uygunluğudur (§1.3-1).

### 2.2 Teknik Detaylar

- **Yasak yüzeyi:** `webpack`, `webpack-cli`, `webpack-dev-server`, `vite`, `rolldown`, `esbuild`, `rollup`, `parcel`, `rspack`, `@rspack/*`, `turbopack`, `@vue/cli`, `react-scripts` · ilgili config dosyaları (`webpack.config.js`, `vite.config.*`, `rollup.config.*`, `esbuild.*`) · `package.json` içinde frontend `build`/`dev` script'i · CI'da JS bundle adımı (`.github/workflows/*` — bugün yok, §1.1/6) · CDN'den paketlenmiş/minified üçüncü-party **uygulama** bundle'ı (kütüphane-script'i değil — ADR-001 "paket serbesttir" sınırı uygulanır).
- **İzinli yüzey (yerini alan uygulama):** (a) doğrudan sunulan Vanilla JS ES6+ modülleri + ITCSS 9 katman + BEM (ADR-001/004); (b) görece import yolları (`./x.js`) + dinamik `import()` ile route-bazlı yükleme (ADR-004 istemci router `:160`); (c) **build'siz** araçlar: `modulepreload`, import maps (Baseline), HTTP/2/HTTP push'sız paralel keşif, `Cache-Control` uzun-cache (hash yerine path-tablanan asset varsa) — bunlar zincir kurmaz; (d) **dev-time** araçlar (test çalıştırıcı vitest §1.1/7, lint, minify script'i) **ölçülmüş bir ihtiyaçla** ve **frontend runtime'a paket eklemeyerek** değerlendirilir — ADR-001 revizyonu şartıyla; (e) Composer paketleri backend'de serbesttir (ADR-001 sınırı — frontend runtime'ı beslemez).
- **Kod yüzeyi ölçümü (2026-10-02):** `webpack|esbuild|rollup|vite` → `assets.coremusic.net/**`, `shared/**`, `.github/**` = **0 isabet**; kök `package.json` = 61 bayt, tek bağımlılık `playwright`, `scripts` yok. `.md`/token taramasındaki isabetler **dokümantasyon ve Figma token adıdır** (§1.1/7) → sürüm/build-notu **yok** çünkü yüzey boş.
- **Cache-busting sorunu (dürüst):** hash'li bundle'siz dünyada uzun-ömür-cache **kaynak dosya adıyla ya da `modulepreload`+import map ile** kurulur; PHP tarafında `PageRouter`/asset URL versiyonlaması **⚠️ VERIFICATION REQUIRED** (bu dosyada ölçülmedi — §5.1/5'e adım olarak yazıldı).

### 2.3 Yeniden Değerlendirme Koşulu (şart satırı)

> **Bu red yalnız aşağıdaki koşullardan BİRİ yazılırsa yeniden değerlendirilir; aksi hâlde yürürlükte kalır:** (1) **ADR-001 frontend-paket-yüzeyi (`:85`) veya ADR-004 §1.4 kısıt 1 (`:83`) yeni bir ADR ile değiştirilirse** (mevcut ADR metinleri düzenlenmez — yeni ADR `superseded by` ile bağlar); (2) **ölçülebilir bir iş/performans ihtiyacı** UI Designer + DevOps raporuyla belgelenirse — mevcut doğrudan-sunum yaklaşımının (a) modül sayısının **≥ ~200 modül** eşiğini aştığı ya da (b) gerçek bir cache/performans CWV sorunu ürettiği (ölçüm: LCP/INP, istek sayısı, asset byte) **sayılarla** yazılmadan "bundle gerekli" iddiası kurulamaz — bu durumda önce **build'siz seçenekler** (modulepreload, import map, HTTP/2) değerlendirilir, bundler **son çare** olarak yeni ADR'de tartılır; (3) **debate tamamlanıp red'i kuran koşullar değişirse** (§5.3) yeni debate + yeni ADR ile yeniden açılır. **Bugün: 1 = SAĞLANMADI (ADR-001/004 diskte, yürürlükte), 2 = ÖLÇÜLMEDİ (ihtiyaç kanıtı 0 — §1.1/6), 3 = SAĞLANMADI (debate PENDING — sonuç henüz yazılmadı) → red geçerli.**

---

## 3. Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **webpack + loader/pipeline (klasik yapı)** | olgun plugin ekosistemi, Module Federation, belgelenmiş config | config karmaşası + Node tek-çipli performans tavanı; npm bağımlılık ağacı (ADR-001 §1.3); kodda ihtiyaç kanıtı **0** (§1.1/6) | Dizin `:129` gerekçesi (**Over-engineering**) + ADR-004 `:83/:159` ihlali; red'in **doğrudan hedefi** |
| 2 | **Vite / Rolldown (modern, Rust)** | çok hızlı dev server + build (Vite 8: 10-30x — §1.3-2), Baseline-ötesi ekosistem | Node 20.19+ + native binary indirimi + config; **ADR-004 `:83` adıyla yasaklar** ("vite" geçer); frontend runtime bağımlılığı ADR-001 `:85` ihlali | **Aynı gerekçe ailesi** — modern olması red'i geçersiz kılmaz; araç değişti, maliyet (zincir + npm + CI) aynı kaldı |
| 3 | **esbuild/rollup tekil minify-script (tam değil, yerel `build.js`)** | config'e göre çok küçük; "bundle" değil minify+hash | yine de npm `devDependency` + script + CI adımı; phpied örneği tamamen **build'siz olmadığını** gösteriyor (§1.3-3) | Şu an **ihtiyaç kanıtı yok** (§1.1/6); ölçülürse §2.3/2 üzerinden **ADR-001 revizyonuyla** değerlendirilir — bu red'i "zero-tooling" değil "zincir kurma" olarak okur |
| 4 | **Turbopack / Rspack (Rust, webpack-uyumlu)** | 5-10x build hızı, webpack config uyumu | Next.js/Turbopack bağlayıcılığı; Rspack 2.0 breaking changes; aynı npm/zincir maliyeti | **Aynı gerekçe ailesi** (ADR-004 `:83` "build sistemi" yasağı); CoreMusic'te Next.js yok → uyumsuz |
| 5 | **Hiçbir build sistemi — doğrudan sunulan ES modülleri** | 0 config, 0 npm runtime, granüler cache, anlık geri bildirim; import maps/modulepreload ile genişletilebilir | import waterfall keşif gecikmesi, minify/hash'in elle/bytestüretime kalması, TS/JSX kullanılamaması (ölçüm §4.2) | **Reddedilmedi — bu, yerini alan yaklaşımdır** (§2.2); bedeli §4.2'de yazılı, örtbas edilmedi |

*(Kabul edilen uygulama alternatifi — doğrudan sunulan Vanilla JS + ITCSS + BEM — §3'te "reddedilmedi" olarak ayrılmadı; o, §2.2'deki **yerini alan** yaklaşımdır ve ADR-001/004 kapsamındadır.)*

---

## 4. Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- **Sıfır frontend build zinciri korunur:** 0 bundler, 0 config dosyası, 0 npm frontend bağımlılığı, 0 CI bundle dakikası (§1.1/6) — ADR-001 tedarik-zinciri verisi (npm malware dalgası) frontend'e hiç ulaşmaz.
- **Red gerekçesi artık izlenebilir:** `grep R-004` → bu dosya; ADR-004 `:38/:53/:83/:99/:159` ve ADR-001 `:32/:85/:201` ile gerekçe ailesi tek yerde toplandı.
- **Güncelleme yapıldı:** red 2026 web verisiyle (3 sorgu, ~24 kaynak) yeniden sınandı — no-build/native-ESM eğilimi ve "vite dahil yasağı" lehine çalıştı; **ters kanıt (webpack emekli değil)** dürüstçe yazıldı ama red'i değiştirmedi (§1.3-1).
- **Endüstri yönüyle uyum:** import maps Baseline + `type="module"` olgunluğu, mevcut mimariyi "geride" değil **eğilimle hizalı** gösterir (§1.3-3).
- **Geri dönüş temiz:** build tool hiç kurulmadığı için `git revert` edilecek değişiklik **0** (§5.2).

### 4.2 Olumsuz Sonuçlar

- **Cache-busting/minify işi projede kalır:** hash'li bundle yerine dosya-adı/URL versiyonlama veya dev-time minify gerekir → bugünün çözümü **⚠️ VERIFICATION REQUIRED** (ölçülmedi — §2.2, §5.1/5).
- **Modül grafiği keşif maliyeti:** build'siz dünyada derin import zinciri waterfall üretir (176 istek örneği — §1.3-3) → `modulepreload`/import map **elle** bakımı gerekir (bundler'ın yaptığı işin manuel karşılığı).
- **TypeScript/JSX/JSDoc-tipi dönüşümü yok:** tip kullanmak istenirse build adımı kaçınılmaz → §2.3/2 kapısı (ADR-001 revizyonu) devreye girer.
- **Ekip beklentisi:** yeni gelen geliştirici "burada neden Vite yok?" sorar → bu dosya + §1.3 yanıtı üretir; yanıtsız kalırsa gölge bağımlılık riski (§4.3/1).
- **Byte büyüklüğü/paketleme optimizasyonları elle:** tree-shaking/code-splitting yerine dinamik `import()` bölmesi ve elle byte disiplini gerekir (ADR-001 byte bütçesi bağlamı).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| Gizli build zinciri girişi ("küçük Vite" adıyla `package.json` script'i veya config) | 3 (olası) | 4 (yüksek — ADR-001 `:85` / ADR-004 `:83` ihlali) | §2.2 yasak listesi + `package.json`/config/CI taraması (**0** korunur — §5.1/4) |
| Cache/performans CWV sorunu çıkınca "hemen bunder" refleksi | 3 (olası) | 3 (orta) | §2.3/2: önce ölçüm (LCP/INP, istek sayısı), önce build'siz seçenekler, bundler **son çare** + yeni ADR |
| Import waterfall'un kullanıcı hissine yansıması | 2 (mümkün) | 3 (orta) | `modulepreload` + dinamik `import()` + görece derinliği sığlaştırma (§4.2) |
| Test/lint araçlarının (vitest §1.1/7) zamanla build zincirine dönmesi | 2 (mümkün) | 3 (orta) | vitest **dev-time, kurulmadı**; frontend runtime paketi eklemek ADR-001 revizyonu ister — ayrım §2.2/d'de yazılı |
| "Webpack/Vite popüler, kullanalım" dış baskısı (yeni ekip) | 3 (olası) | 2 (düşük) | Bu dosya + §1.3 (no-build + yasağın kapsamı) + §2.3 satırı — yanıt vault'ta yazılı |

---

## 5. Uygulama (Implementation)

> Bu kayıt **salt-okunur seri**dir (`rejected/`); "uygulama" = kaydın vault'a doğru yerleştirilmesi ve denetimidir — kod değişikliği **yoktur**.

### 5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | Bu dosyayı `.ai/.decisions/rejected/R-004-webpack-bundle-system.md` olarak yaz (şablon §1-§7, 7 bölüm dolu) + `log.md` append ("R-004 yazıldı (debate PENDING)") | Vault Steward | 25 dk |
| 2 | **Debate** (3 tur / 20 persona — ✅ **TAMAMLANDI**, sonuç §5.3 + §7 debate kaydı) + Tech Lead onayı ✅ | MO + Tech Lead | 2026-10-02 |
| 3 | **`index.md:129` `<!-- dead-link ... -->` bayrağı → DOKUNULMADI** (talimat gereği son sıfırlamaya ertelendi — rapor-only) · `rejected/index.md` § tablosu **BOŞ → DOKUNULMADI** (rapor-only) | Vault Steward | son sıfırlama |
| 4 | Periyodik denetim: `package.json` (frontend `build` script'i) + `webpack.config|vite.config|rollup.config|esbuild` + `.github/workflows/*` JS bundle adımı = **0** korunur | UI Designer + DevOps Engineer | her sprint |
| 5 | Cache-busting/asset-versiyonlama gerçeği + CWV ölçümü → §2.3/2 koşulu için kanıt üretir (rakam ölçülmeden yazılmaz; bugün **⚠️ VERIFICATION REQUIRED**) | UI Designer + DevOps | ihtiyaç anında |

### 5.2 Geri Dönüş Planı

Bu karar **kod tarafında geri alınacak bir şey üretmedi** (build tool hiç kurulmadı → `git revert` edilecek değişiklik **0**; `package.json`'da build script yok, §1.1/6; config dosyası yok). Geri dönüş = **yeniden değerlendirme** demektir ve yalnız §2.3 koşullarından biri yazılırsa yeni ADR ile açılır: (1) ADR-001 `:85` / ADR-004 `:83` değişirse → bu dosya `superseded` notuyla **bağlanır, düzenlenmez**; (2) ölçülmüş performans/ihtiyaç kanıtı → önce build'siz seçenekler, sonra UI Designer + DevOps raporuyla yeni ADR; (3) debate sonucu değişirse → debate kaydı + yeni ADR. Vault bozulursa standart kurtarma `git checkout` + son commit (AGENTS.md §17 #10). **Reddedilen teknolojinin kodda izi olmadığı için kullanıcı/veri etkisi YOKTUR.**

### 5.3 Debate Şartları

**Kayıt:** ✅ **TAMAMLANDI** — 3 tur / 20 persona (R-001/R-002/R-003 formatı) · **Sonuç: 19 kabul / 1 çekimser / 0 red → RED DOĞRULANDI** (2026-10-02 — debate kaydı §7).

**Akış (3 tur tamamlandı):**

- **Tur 1 (20 persona — bulgu):** build tool kod izi **0** (`package.json` 61 bayt/build script yok · `assets.coremusic.net` + `shared/` + `.github/` = 0 — §1.1/6) · red kaynağı `index.md:129` (dead-link **dokunulmadı** → §5.1/3) · yerini alan [[../accepted/ADR-001-vanilla-js-itcss]] + [[../accepted/ADR-004-multi-domain-spa]] `Test-Path` doğrulandı · 3 sorgu / ~24 benzersiz kaynak: webpack 2026 roadmap + 5.111 (emekli değil — **ters kanıt**), Vite 8/Rolldown (65M haftalık), no-build/import maps Baseline, waterfall 176 istek · **3/3 red destekli, 1 ters kanıt dürüst** · `rejected/index.md` boş → dokunulmadı.
- **Tur 2 (itiraz → çözüm → şart):** (i) "webpack öldü" tezinin **yanlışlığı** → gerekçe "over-engineering + tek build disiplini"ye sabitlendi (→ şartsız, §2.1/5); (ii) no-build bedeli (waterfall/cache-busting/TS) → §4.2 + §5.1/5 ölçüm adımı (→ **şart 1**); (iii) test/lint araçlarının build'e kayması riski → dev-time/runtime ayrımı (→ **şart 2**).
- **Tur 3 (oy):** **19 kabul / 1 çekimser / 0 red → RED DOĞRULANDI**; 3 bağlayıcı şart kesinleşti (aşağıda) — debate kaydı §7.

**Planlanan bağlayıcı şartlar (Tur 2 çıktısı — debate sonrası kesinleşir):**

1. **Şart 1 — Ölçüm kapısı:** "bundle gerekli" iddiası yalnız §2.3/2 ölçümüyle (modül sayısı ≥ ~200 **veya** CWV/istek-sayısı kanıtı) kurulabilir; ölçüm yoksa red değişmez (§2.3).
2. **Şart 2 — Dev-time/runtime ayrımı:** test/lint/minify araçları frontend **runtime**'ına paket ekleyemez; eklerse ADR-001 revizyonu + bu dosyada debate kaydı açılır (§2.2/d).

**Debate'te kesinleşen 3 bağlayıcı şart (Tur 2 itiraz→çözüm, Tur 3 oyu 19/1/0 ile onaylandı — bağlayıcı):**

1. **Şart 1 — Gerekçe-bağlantısı:** "over-engineering" gerekçesi **tek başına** savunulamaz; her red savunması **3 somut maliyete** bağlanır — **cache-busting** (elle hash/versiyonlama — §4.2) + **import waterfall** (176 istek örneği — §1.3-3) + **bakım** (npm bağımlılığı + config + CI + sürüm takibi — §2.1/4).
2. **Şart 2 — İzleme kapısı (import maps / Rolldown):** import maps'in Baseline olgunluğu veya Rolldown/Vite 8 olgunlaşması (§1.3-2/3) red'i **otomatik geçirmez** — izlenir; eşik sağlandığında §2.3 kapısı + yeni debate ile yeniden değerlendirilir.
3. **Şart 3 — Test build tool ADR kapısı:** vitest spec'leri **PLANNED** (paket kurulu değil — §1.1/7); test/build tool talebi gelirse bu dosya değil **yeni ADR** açılır (§2.2/d + ADR-001 `:85`).

> **Debate sonucu:** ✅ **RED DOĞRULANDI** (3 tur / 20 persona — 19/1/0) · **Tech Lead:** ✅ · **Arch Lead:** ⏳.

---

## 6. İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../index]] | Karar dizini — bu red'in kaydı (`:129`, slug + "Over-engineering" + dead-link bayrağı §5.1/3) |
| [[../accepted/ADR-001-vanilla-js-itcss]] | **Yerini alan (birincil):** Vanilla JS ES6+ + ITCSS + BEM; `:85` frontend npm runtime paketi kapalı; `:32/:201` bu red'i sayar (parmak izi) |
| [[../accepted/ADR-004-multi-domain-spa]] | **Yerini alan (ikincil) + gerekçe ailesi:** `:83` kısıt 1 "webpack/vite/build sistemi YASAK", `:99` tek build disiplini, `:159` ayrı-bundle alternatifi bu red'e dayanır, `:38/:53` red referansları |
| [[../accepted/ADR-012-csp-nonce-strict-dynamic]] | Script yüzeyi — build zinciri ek paket/script girişi demektir (§1.4 kısıt) |
| [[../../brain]] | Mimari karar özeti (frontend/build satırı) |
| [[R-001-redux-style-state-management]] | Seri kardeşi — aynı salt-okunur red kayıt formatı (format referansı); framework/over-engineering gerekçe ailesi |
| [[R-002-mongodb-document-store]] | Seri kardeşi — aynı salt-okunur red kayıt formatı (kanıt tablosu/dürüst etiket deseni) |
| [[R-003-jquery-ui-framework]] | Seri kardeşi — format referansı (§1.3 9 alan, §7.1 rapor deseni) |
| Dizin satırı | `index.md:129` — slug otoritesi + dead-link bayrağı (§5.1/3) |
| Debate şartları | Bu dosya **§5.3** — debate ✅ **TAMAMLANDI** (RED DOĞRULANDI 19/1/0) + **3 bağlayıcı şart** (gerekçe-bağlantısı · izleme kapısı · test build tool ADR kapısı) + debate kaydı **§7** |
| Düz metin | Eski seri R-005…R-012 (`rejected/index.md` tablosu boş → §7.1/2) |

---

## 7. Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Bayram Ali | 2026-10-02 | ✅ |
| Tech Lead | — | 2026-10-02 | ✅ |
| Arch Lead | — | — | ⏳ |

**Debate kaydı (3 tur / 20 persona — 2026-10-02):**

| Tur | Katılım | Çıktı |
|-----|---------|-------|
| 1 | 20 persona — bulgu | R-004 grep **7 isabet** (ADR-004 `:38/:53/:83/:99/:159` + ADR-001 `:32/:201`) · görev satırı "ADR-004:160" hatası düzeltildi (`:160` = framework SPA router — §7.1/8) · yerini alanlar `Test-Path` True · build tool izi **0** · sahte izler dürüst etiketlendi · 3 sorgu / ~24 kaynak · **3/3 red destekli + 1 ters kanıt** (webpack emeklilik DOĞRULANMADI → tez kullanılmadı) · 17 kabul/nötr + 3 uyarı |
| 2 | itiraz → çözüm | (1) "over-engineering" tek başına → **3 somut maliyete** bağlandı → **şart 1** · (2) import maps/Rolldown olgunlaşması → **izleme kapısı** → **şart 2** · (3) vitest PLANNED → **test build tool ADR kapısı** → **şart 3** |
| 3 | oy | **19 kabul / 1 çekimser / 0 red → RED DOĞRULANDI** |

> **Sonuç:** R-004 **RED DOĞRULANDI** · **Tech Lead:** ✅ · **Arch Lead:** ⏳ · 3 bağlayıcı şart: §5.3.

### 7.1 Rapor Notları (bu işlemde dokunulmayanlar)

1. **Dead-link bayrağı:** `index.md:129` `<!-- dead-link: R-004-webpack-bundle-system no source 2026-09-24 -->` **olduğu gibi bırakıldı** — artık `no source` iddiası **geçersizdir** (bu dosya kaynaktır) → temizlik son sıfırlamaya ertelendi, burada rapor edildi.
2. **`rejected/index.md` durumu:** dosya **VAR** (757 bayt, v1.0.1, `total: 12`) ama tablo **BOŞ** (başlık satırları, kayıt satırı yok) → bu işlemde **oluşturulmadı ve doldurulmadı** (dizin satırı düzenleme yetkisi kapsam dışı) → rapor-only.
3. **Slug hizası:** dosya adı `R-004-webpack-bundle-system` = `index.md:129` slug **birebir** ✅ (R-001/R-002/R-003 dersi: tahmin yok, `rejected/` glob'u ile doğrulandı — bu işlem öncesi `R-004*` = 0 dosya).
4. **Debate:** ✅ **TAMAMLANDI** (3 tur / 20 persona — 19/1/0 **RED DOĞRULANDI**; 3 şart §5.3, debate kaydı §7) · **Tech Lead:** ✅ · **Arch Lead:** ⏳ (değiştirilmedi).
5. **Kod yüzeyi kanıtı:** `webpack|esbuild|rollup|vite` = **0 isabet** (`assets.coremusic.net/**`, `shared/**`, `.github/**`); kök `package.json` **61 bayt** → tek bağımlılık `playwright`, `scripts` **yok**. `.ai/ui-design/tokens/**` içindeki `"Build: Vite / Webpack"` **Figma token adıdır** (bağımlılık değil) · vitest spec'leri "paket henüz kurulu değil" der (**PLANNED**) → §1.1/6-7.
6. **Şablon yolu notu:** görev `templates/adr/adr-template.md` der; disk kanıtı `.ai/.templates/adr/adr-template.md`'dir (glob) — bu dosya o şablonun §1-§7 iskeletiyle (§1.3 9 alan, §7 Onay) yazıldı.
7. **Araştırma düzeltmesi (dürüst):** görev tanımı "webpack emekliliği/maintenance" başlığını araştırma eksenine koyuyordu; **sonuç: emeklilik DOĞRULANMADI** (webpack 5.111 — 2026-09-14; 2026 roadmap; npm "alive" 2026-09-18) → red gerekçesi buna göre **"over-engineering + tek build disiplini"** olarak yazıldı; emeklilik iddiası kullanılmadı (§1.3-1, §2.1/5).
8. **Satır-no düzeltmesi:** görev tanımı "ADR-004:160 (uzun gerekçe)" der; disk ölçümü **`:160` = alternatif 2 (framework SPA router) satırıdır** ve R-004 içermez. R-004 gerekçe ailesinin doğru satırları: **`:38`, `:53`, `:83`, `:99`, `:159`** (hepsi `Select-String` ile doğrulandı) — §1.1/3 bu satırlarla yazıldı.

---

*R-004 v1.0.0 | 2026-10-02 | Created — CoreMusic Vault (.decisions/rejected/ sıfırdan yazım, salt-okunur seri)*
*Authority: R-004 Red Karar Metni · Mode: Red Team · Human Mode · Truth Mode*
