---
title: "CoreMusic — ADR-048: View Transition API Integration (aynı-dokman View Transition API · NavigationOrchestrator sarmalama · progressive enhancement + CSS fallback · prefers-reduced-motion tam kapama · INP/kare bütçesi · router entegrasyonu ADR-004/021 hizası · fragment kapsamı liste→detay + view mode + tema)"
type: "architecture-decision"
category: "frontend"
date: "2026-09-29"
updated: "2026-09-29"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic View Transition API entegrasyonu: (a) aynı-dokman startViewTransition NavigationOrchestrator.commit sarmalayıcıda, (b) progressive enhancement — destek yoksa bugünkü davranış aynen korunur + CSS fallback, (c) prefers-reduced-motion'da geçiş tamamen kapalı (CSS + JS matchMedia), (d) INP/kare bütçesi ADR-006 içinde kalır (kısa callback, kısa animasyon, skipTransition), (e) router entegrasyonu ADR-004/021 sözleşmesi üzerinden — nav:complete ölü yoluna BAĞLANMAZ, (f) fragment kapsamı kademeli: liste→detay, view mode (ADR-045), tema (ADR-044)"
kaynak: "Kullanıcı onaylı kapsam a-f + disk/kod kanıt taraması (2026-09-29: canlı 98 JS dosyasında startViewTransition 0 hit · CSS/JS/PHP/HTML'te view-transition artefaktı 0 hit · 101 CSS dosyası envanteri (ilk-party 84 / vendor 17): @keyframes ilk-party 10 dosya/28 hit, prefers-reduced-motion 29 dosya/153 hit (ilk-party 17/17 + vendor 12/136) · NavigationOrchestrator.js:43-71 canlı commit noktası · main.js:82,85,96-176 boot zinciri → CoreMusicApp.init() hiç çağrılmıyor → SPARouterAdapter örneklenmiyor → nav:complete canlı yayıncı 0 · router/main.js yükleyen 0 · arşiv prompt1-spa-router-2026-08-15.md:423,427,392,556) + web araştırması (6 sorgu / 8 adlandırılmış kaynak)"
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL — §7.1 · bağlayıcı şartlar §5.4)"
---

# CoreMusic — ADR-048: View Transition API Integration (Görünüm Geçiş API Entegrasyonu)

> **Durum:** ✅ **ACCEPTED** (kullanıcı onaylı kapsam a-f) · **Tarih:** 2026-09-29 · **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL — §7.1)** · **Tech Lead:** ✅ · **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `ADR-048-view-transition-api-integration`
> **İlgili kararlar:** [[ADR-004-multi-domain-spa]] (dual router — karar (e) zemini) · [[ADR-021-spa-router-immutable-contract]] (router immutable contract — karar (e)) · [[ADR-006-performance-targets]] (INP/kare bütçesi — karar (d)) · [[ADR-018-footer-player-vaporwave]] (prefers-reduced-motion hizası — karar (c)) · [[ADR-044-dynamic-user-theme-engine]] (tema fragment anahtarlama — karar (f)) · [[ADR-045-multi-domain-view-mode-architecture]] (view mode fragment — karar (f)) · [[ADR-046-cross-view-state-preservation]] (nav:complete 0 yayıncı ön koşulu — karar (e)) · [[ADR-005-ultrathink-protocol]] (zero hallucination — §1.1 dürüst etiket) · [[ADR-016-url-normalization]] (URL sözleşmesi korunur) · [[ADR-047-login-redirect-session-bridge]] (önceki ADR — biçim referansı) · [[../index.md]] · [[../../raw/brain.md]]
> **Ad gerekçesi:** slug `ADR-048-view-transition-api-integration` **disk kanıtından** alınmıştır — `.ai/.decisions/index.md:89` bu adı taşır; ayrıca `.ai/archives/prompt1-spa-router-2026-08-15.md` satır 423 (`*Detaylı metadata: [[ADR-048]]*`), 392 ve 556 ADR-048'i referanslar, satır 427 `document.startViewTransition(async () => {` örnek kodunu içerir — bu arşiv slug'ın ve kapsamın kaynağıdır. Bu dosya o boşluğu doldurur.
> **⚠️ Düzeltme (prompt ↔ disk):** Üst görevde "ADR-005 dual router" denmişti; **diskte `ADR-005-ultrathink-protocol` (zero hallucination) — dual router DEĞİLDİR.** Dual/multi-domain router kararları **ADR-004** (multi-domain SPA) + **ADR-021** (router immutable contract) + **ADR-016** (URL normalization) zinciridir; **ADR-083** (SPA router) için diskte dosya **YOK** → düz metin + `⚠️ VERIFICATION REQUIRED`. Aynı düzeltme [[ADR-045-multi-domain-view-mode-architecture]] satır 132'de kayıtlıdır; bu ADR tekrarlar.
> **Index durumu:** `.ai/.decisions/index.md:89`'da ADR-048 satırı **vardır** (`| [[../../raw/brain.md]] ADR-048-view-transition-api-integration | View Transition API | Frontend |`); satır 88/90'daki ADR-046/049 satırlarıyla **aynı biçimdedir**. Slug'ı doğru hedefe bağlayan wiki-link düzeltmesi **bir sonraki vault reset'ine ertelenmiştir** (bu işlemde index.md'ye dokunulmadı — report-only).
> **Frozen notu:** ADR-001-037 **dokunulmamıştır** (yalnız atıf). Bu dosya Active aralığındadır, frozen değildir.

---

## §1 Bağlam (Context)

### §1.1 Mevcut Durum (disk + kod kanıt — dürüst etiket, 2026-09-29 taraması)

Etiketler: **IMPLEMENTED** = diskte kod kanıtıyla ispatlı · **PLANNED** = kararlaştırılmış, karşılığı kodda yok · **ÇELİŞKİ** = iki kayıt uyuşmuyor · **KAPSAM FARKI** = ölçüm kapsamı/tarih farklılığı (çelişki değil) · **KAPSAM DIŞI** = ilgili alanda kod yok (hiçbiri yumuşatılmadı).

#### A) startViewTransition — canlı kodda 0 (henüz hiç kullanılmamış)

| Kanıt | İçerik | Etiket |
|---|---|---|
| `assets.coremusic.net/js/**` (98 dosya, recursive tarama) | `startViewTransition` **0 isabet** — hiçbir JS dosyası çağırmıyor | **PLANNED** (bu ADR ile kurulacak) |
| CSS/JS/PHP/HTML geneli | `.view-transition`, `view-transition-name`, `@view-transition` **0 isabet** | **PLANNED** (CSS katmanı yok) |
| `.ai/archives/prompt1-spa-router-2026-08-15.md:427` | `document.startViewTransition(async () => {` örnek kodu (§11.3 View Transition API) | DOĞRULANDI (arşiv — tek geçiş, slug kaynağı) |
| `.ai/archives/prompt1-spa-router-2026-08-15.md:423,392,556` | `[[ADR-048]]` referansları | DOĞRULANDI (arşiv) |

#### B) Animasyon envanteri (101 CSS dosyası — hepsi `assets.coremusic.net/Css`; ilk-party 84 / vendor 17)

| Ölçüm | İlk-party | Vendor | Etiket |
|---|---|---|---|
| `@keyframes` | 10 dosya / 28 isabet (en yüksek: `01_Abstracts\a-theme-config.css` 13, `05_Pages\p-login-view.css` 6, `04_Components\c-toast.css` 2) | 4 dosya / 20 isabet (`bootstrap.css` / `.min` / `.rtl` / `.rtl.min` — 5'er isabet) | **IMPLEMENTED** |
| `transition` (özellik geçen satırlar) | 35 dosya / 141 isabet | 5 dosya / 7 isabet | **IMPLEMENTED** |
| `animation:` | 9 dosya / 28 isabet | 5 dosya / 50 isabet | **IMPLEMENTED** |
| `prefers-reduced-motion` | 17 dosya / 17 isabet | 12 dosya / 136 isabet (toplam 29 / 153) | **IMPLEMENTED** |

> **ADR-044 hizası:** [[ADR-044-dynamic-user-theme-engine]] satır 70 "`prefers-reduced-motion` 14 dosyada 76 isabet" kaydını taşır; bugünkü ölçüm (29 dosya / 153 isabet, vendor dâhil; ilk-party 17/17) **kapsam farkıdır — ÇELİŞKİ değildir** (farklı tarama kapsamı/tarih; vendor 12 dosya/136 isabet ilk-party olmayan kaynak). İlk-party'de 17 dosyada **tek tek** isabet (dosya başına 1 blok) görüldü.

#### C) Router durumu — canlı boot zinciri ve nav:complete (ADR-046 ön koşulu)

| Kanıt | İçerik | Etiket |
|---|---|---|
| `shared/src/PageRouter/HtmlShellRenderer.php:165` | `<script src=".../js/main.js?v=..." type="module" defer>` — **tek canlı giriş** (`:67` cache-buster listesinde de `js/main.js`) | **IMPLEMENTED** |
| `assets.coremusic.net/js/main.js:2,7,182` | sürüm `6.1.0` | **IMPLEMENTED** |
| `main.js:82` → `:85` | `router = new Router({...})` + `window.CoreMusic.Router = router` — canlı router **doğrudan** kurulur | **IMPLEMENTED** |
| `main.js:96-170,176` | `app.registerModule('device'/'scale'/'theme'/'viewMode'/...)` + `app.setRunning()` — **`app.init()` (CoreMusicApp.init) HİÇ ÇAĞRILMAZ** | **IMPLEMENTED** (kayıt) / **ÇELİŞKİ** (boot yolu ölü) |
| `js/core/CoreMusicApp.js:56` (`async init()`) → `:75` `['router', modClasses.SPARouterAdapter, false]` | SPARouterAdapter boot sırası **yalnız `init()` içinde** — init çağrılmadığı için **hiç örneklenmez** | **ÇELİŞKİ** (boot yolu erişilemez) |
| `js/router/SPARouterAdapter.js:113` `this.#eventBus.emit('nav:complete', { url })` | `nav:complete` **tek yayıncısı** — canlı ağaçta dosya VAR ama örneklenmediği için **yayınlamıyor**; `main.js`'te import'u **0** | **IMPLEMENTED** (tanım) / **ÇELİŞKİ** (canlıda 0 yayıncı) |
| `js/features/ScrollManager.js:26` `this.#eventBus.on('nav:complete', ...)` | tek dinleyici — ölü yayına bağlı (ADR-046 bulgusu) | **IMPLEMENTED** (dinleyici) / **ÇELİŞKİ** (geri-yükleme ölü) |
| `.ai/archives/` dışındaki `js copy/` dizini | `js copy/` dizini **diskte YOK** (2026-09-29) — ADR-046'nın `js copy/router/SPARouterAdapter.js:113` yolu artık mevcut değil; canlı yol `js/router/SPARouterAdapter.js` | **KAPSAM FARKI** (ADR-046 gerekçesi güncellendi; **sonuç aynı: canlıda 0 yayıncı**) |
| `js/router/main.js` (loader) | yükleyen **0** (`js/**/*.js` + `PageRouter/*.php` taraması) → legacy/ölü | ⚠️ **ölü kod** (dokunulmaz, raporlanır) |

> **ADR-046 ile hiza:** [[ADR-046-cross-view-state-preservation]] "nav:complete canlıda 0 yayıncı" bulgusu **hâlâ doğrudur**; gerekçe netleşti: yayıncı dosyası canlı ağaçta mevcut, ama **boot yolu (CoreMusicApp.init → SPARouterAdapter) hiç çalışmıyor**. Karar (e) bu yüzden VT sarmalayıcısını `nav:complete`'e ** değil**, canlı commit noktasına bağlar.

#### D) Canlı commit noktası (startViewTransition'ın sarılacağı yer)

| Kanıt | İçerik | Etiket |
|---|---|---|
| `js/router/NavigationOrchestrator.js:43` | `async navigate(url, pushState = true, depth = 0)` — canlı navigasyon giriş noktası | **IMPLEMENTED** |
| `NavigationOrchestrator.js:51` / `:69` | `aria-busy=true` (başlangıç) / `aria-busy=false` (başarı) — çoklu hata dalları `:54,58,60,61,62,75` | **IMPLEMENTED** |
| `NavigationOrchestrator.js:57` | `contentFetcher.fetch(target, container)` — ağ + DOM hazırlık | **IMPLEMENTED** |
| `NavigationOrchestrator.js:64` → `:65` | `contentPatcher.patch(responseData, target, container)` → `history.pushState(null, document.title, target)` — **DOM commit + URL commit sırası** | **IMPLEMENTED** |
| `NavigationOrchestrator.js:70-71` | `focusManager.moveFocus()` → `scrollRestorer.scrollToTop()` — commit sonrası odak/kaydırma | **IMPLEMENTED** |
| `js/router/Router.js:54` | `history.scrollRestoration = 'manual'` | **IMPLEMENTED** (ADR-046 ile hizalı) |
| `js/router/Router.js:63-64` | boot'ta query-strip (`auth_key=` hariç tutulur → `history.replaceState` ile aralık temizlenir) | **IMPLEMENTED** (ADR-046 ile hizalı) |

#### E) Fragment anahtarlama kancaları (karar (f) yüzeyi)

| Kanıt | İçerik | Etiket |
|---|---|---|
| `js/managers/ViewModeManager.js:75` | `document.body.setAttribute('data-view-mode', mode)` + `<link id="cm-view-css">` CSS swap (`:65,71,80`) — ADR-045 görünüm değişim yüzeyi | **IMPLEMENTED** |
| `js/managers/ThemeManager.js:156-158` (+ `:232`) | `documentElement` üzerinde `data-mode` remove/set — ADR-044 tema geçiş yüzeyi | **IMPLEMENTED** |
| `js/core/EventBus.js` | pub/sub altyapısı (`SPARouterAdapter.js:113` emit kalıbı) | **IMPLEMENTED** (altyapı) |
| `Css copy/` dizini | **diskte YOK** (üst görevde geçen varsayım — False) | DÜZELTİLDİ (varsayım çürütüldü) |

#### F) Vault kayıtları (bu dosyadan önceki iddialar)

| Kayıt | İçerik | Etiket |
|---|---|---|
| `.ai/.decisions/index.md:89` | `| [[../../raw/brain.md]] ADR-048-view-transition-api-integration | View Transition API | Frontend |` — satır **var**; `[[../../raw/brain.md]]` biçimi satır 88/90 (ADR-046/049) ile aynı | DOĞRULANDI (düzeltme reset'e ertelendi) |
| `.ai/archives/prompt1-spa-router-2026-08-15.md:392,423,556` | `[[ADR-048]]` / `[[.decisions/accepted/ADR-045-...]]` biçimli referanslar | DOĞRULANDI (arşiv; `.decisions` yolu nokta eksik — arşive dokunulmaz) |
| Üst görev yönergesi | "ADR-005 dual router" | **ÇELİŞKİ** → disk: dual router = **ADR-004 + ADR-021 (+ ADR-016)**, ADR-005 = ultrathink (§ başlık notu + ADR-045:132) |

> **Bulgu özeti:** View Transition API **hiçbir yerde kullanılmıyor** (JS 0, CSS 0 — slug yalnız arşivde ve index.md'de yaşıyor); animasyon envanteri geniş (101 CSS, `@keyframes` 14 dosya (ilk-party 10/28 + vendor 4/20 — §1.1-B), `prefers-reduced-motion` 29 dosya) ve ADR-018/044 hizası **kapsam farkıyla** tutarlı; canlı router `main.js` → `NavigationOrchestrator` zinciridir ve `nav:complete` **canlıda 0 yayıncı** (boot yolu ölü — ADR-046 bulgusu teyit, gerekçe netleşti); entegrasyon için hazır üç kanca (`NavigationOrchestrator.navigate()`, `ViewModeManager.applyMode`, `ThemeManager` `data-mode`) mevcuttur. Karar, API'yi bu üç noktaya kademeli ve geri alınabilir biçimde bağlar.

### §1.2 Sorun Tanımı (Problem)

CoreMusic çok-alan SPA (ADR-004/021) üzerinde liste→detay, görünüm modu ve tema değişimleri **ani DOM değişimleri** olarak yaşanıyor: `NavigationOrchestrator.navigate()` içinde `contentPatcher.patch()` (`:64`) tüm ana içerik alanını bir anda değiştiriyor, `ViewModeManager`/`ThemeManager` `data-*` niteliklerini anlık flip ediyor — kullanıcının gözünde tutarlı bir "geçiş" yok, mevcut `@keyframes`/`transition` envanteri (14 dosya keyframes, 35 dosya transition) **tek tek bileşen animasyonu** yapıyor, sayfa durumu değişimine eşlik eden bir hikâye yaratmıyor. Tarayıcı ekosistemi bu boşluğu doldurdu: aynı-dokman View Transition API Chrome/Edge 111+, Safari 18+, Firefox 144 ile **Baseline (2025-10-16)** statüsüne girdi — API artık "ileride" değil. İkinci sorun **risk**: API kullanılmadan geçmek de kullanmak da maliyetlidir — kullanılmazsa UX kaybı sürer, kullanılırken INP'yi (ADR-006 hedefleri) kötüleştirebilir, `prefers-reduced-motion` kullanıcılarını (ADR-018/044 sözleşmesi) ihlal edebilir, `view-transition-name` çakışmaları ve tam-viewport snapshot maliyeti (GPU ~200-400 ms, bellek yoğun) yeni hata yüzeyi açar. Üçüncü sorun **entegrasyon yeri**: `nav:complete` olayı **canlıda 0 yayıncı**dır (ADR-046 — boot yolu ölü), yani "router olayına bağla" reçetesi bugünkü kodda **çalışmaz**. Karar; API'yi nereye (canlı commit noktası), hangi şartlarla (feature-detect, reduced-motion tam kapama, INP bütçesi) ve hangi kademede (fragment kapsamı) bağlayacağını sabitler.

### §1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | 6 sorgu: (1) "View Transition API browser support 2026 Chrome Edge Safari Firefox Baseline same-document" (2) "caniuse View Transitions API global browser usage percentage" (3) "startViewTransition SPA router integration best practices view-transition-name" (4) "View Transitions API performance INP frozen rendering skipTransition long animations" (5) "prefers-reduced-motion view transitions accessibility WCAG 2.3.3 matchMedia" (6) "View Transitions API pitfalls view-transition-name unique clipping contain rasterization" |
| Web Search **Konusu** | Aynı-dokman ve belgeler-arası View Transition API tarayıcı desteği (Baseline/web.dev, caniuse), SPA router ile entegrasyon kalıbı (`startViewTransition` callback'i + `view-transition-name`), performans/INP etkisi (donan rendering, `skipTransition`, GPU snapshot maliyeti), erişilebilirlik (`prefers-reduced-motion` + WCAG 2.3.3 Animation) ve bilinen tuzaklar (flat pseudo-tree/clipping, bitmap snapshot, isim çakışması, `contain`) |
| Web Search **Bağlam** | Karar CoreMusic'in disk gerçeğiyle yüzleşiyor: `startViewTransition` JS'te 0, view-transition CSS artefaktı 0 (§1.1-A), animasyon envanteri 101 CSS dosyası (§1.1-B), canlı commit `NavigationOrchestrator.js:43-71` (§1.1-D) ve `nav:complete` 0 yayıncı (§1.1-C). Araştırma 2026-09-29'da yapıldı; protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (6 sorgu, 8 adlandırılmış kaynak; web.dev/MDN/W3C resmi kaynakları öncelikli) |
| Web Search **Kısa Açıklama** | Aynı-dokman View Transition API **Baseline'a girdi** (web.dev: Newly available 2025-10-16 — Chrome/Edge 111+, Safari 18+, Firefox 144); entegrasyon **feature-detect** ile yapılır (`typeof document.startViewTransition === 'function'`), desteksiz tarayıcıda davranış bugünküyle **aynı** kalır; `prefers-reduced-motion`'da geçiş **tamamen kapatılır** (yalnız CSS media query yetmez — JS `matchMedia` guard'ı da şart); animasyonlar **kısa** tutulur (INP: rendering callback sırasında donar, uzun geçişler INP'yi öldürür; kaçış kapısı `skipTransition`) |
| Web Search **Uzun Açıklama** | Kaynaklar beş eksende buluşuyor. (i) **Destek:** web.dev Baseline kartı aynı-dokman VT'yi 2025-10-16'da Newly Available ilan eder (Chrome/Edge 111+, Safari 18.x, Firefox 144); **belgeler-arası** (`@view-transition { navigation: auto }`) Chrome 126+/Safari 18.2+ tarafındadır ve **Firefox'ta desteklenmez** — SPA (tek dokman) kararını besleyen budur; caniuse'nin ~%94 küresel kapsaması **ikincil** bir sayıdır, tek başına dayanak yapılmaz. (ii) **SPA router entegrasyonu:** web.dev "View Transitions for SPAs", Chrome for Developers ve MDN aynı kalıbı verir: router navigasyonunu `document.startViewTransition(async () => { /* DOM update */ })` içine sar; geçiş adları `view-transition-name` ile verilir, güncelleme işi (fetch) callback dışında, commit işi (DOM) callback içinde yapılır; `document.startViewTransition().skipTransition()` kaçış kapısıdır. (iii) **Performans/INP:** Smashing Magazine ve Chrome kaynakları, geçiş sırasında **rendering'in donduğunu** (callback + anlık görüntü alma süresi boyunca ana iş parçacığı bloke değildir ama kare bütçesi harcanır), uzun/çok animasyonun INP'yi kötüleştirdiğini, tam-viewport anlık görüntülerinin **GPU'da ~200-400 ms** sürdüğünü ve **bellek yoğun** olduğunu vurgular → geçiş süresi kısa, kapsam dar tutulur. (iv) **Erişilebilirlik:** W3C WCAG 2.3.3 (Animation — AAA) hareket tetikleyen arayüzler için azaltma ister; Deque/MDN/AAArdvark kaynakları `prefers-reduced-motion`'ın **hem CSS media query hem JS `matchMedia`** ile ele alınmasını önerir — VT'yi JS başlatıyor olduğumuz için JS guard **zorunludur**. (v) **Tuzaklar:** pseudo-tree **düz** olduğu için `overflow`/clipping davranışları normal DOM'dan farklıdır; anlık görüntü **bitmap** olduğu için `object-fit`/bazı filtreler geçişte kaybolur; aynı anda iki öğe **aynı `view-transition-name`** taşırsa geçiş hata verir/atlarnır; `contain: paint` gibi mülkiyetler devre dışı kalabilir → isim envanteri + kapsam testi şart. |
| Web Search **Paragraf Veri Uzun** | View Transition API, CoreMusic'in tam da ihtiyaç duyduğu şey gibi görünüyor: tek satırlık bir sarmalayıcı (`document.startViewTransition`) liste→detay geçişini, tema flip'ini ve view mode değişimini **aynı görsel dile** çevirir — ama web bu fikri dört şartla veriyor. Birincisi **kademelilik**: aynı-dokman API Baseline iken belgeler-arası hâlâ Firefox'suzdur, SPA router (ADR-004/021) zaten tek dokman yaşadığı için karar aynı-dokman kanalıdır ve feature-detect **zorunludur**; desteksiz tarayıcıda hiçbir davranış değişmez (bugünkü `patch` + `pushState` aynen çalışır). İkincisi **hareket azaltma**: `prefers-reduced-motion` kullanıcısına geçiş göstermek WCAG 2.3.3'e aykırıdır; CSS `@media (prefers-reduced-motion: reduce)` tek başına yeter değildir çünkü geçişi **JS başlatmaktadır** — `matchMedia` guard'ı ile `startViewTransition` hiç çağrılmaz. Üçüncüsü **bütçe**: callback kısa sürer (fetch zaten dışarıdadır, içinde yalnız DOM commit vardır), animasyon yüzlerce ms değil ~250-300 ms mertebesindedir; rendering geçiş anında donduğu için uzun animasyon INP'yi (ADR-006) doğrudan düşürür, `skipTransition` ve kapsam daraltma kaçış kapısıdır. Dördüncüsü **yüzey**: `view-transition-name` çakışması geçişin kendisini kırar, flat pseudo-tree clipping'i değiştirir, bitmap snapshot `object-fit`'i yutar — bu yüzden kapsam üç fragment (liste→detay, view mode, tema) ile sınırlanır ve her ek isim bir envanter kaydıdır. Bugünkü kod bu dört şartı karşılayacak konumdadır: sarmalayıcı noktası nettir (`NavigationOrchestrator.js:64-65`), kancalar hazırdır (`ViewModeManager.js:75`, `ThemeManager.js:156-158`), reduced-motion envanteri zaten 29 dosyada mevcuttur (§1.1-B) ve `nav:complete` gibi ölü bir yola bağlanmak gerekmez. |
| Web Search **Sonucu** | 6 sorgu / **8 adlandırılmış kaynak**: web.dev (Baseline + "View Transitions for SPAs" rehberi), MDN (`startViewTransition()`, `@view-transition`, `prefers-reduced-motion`), Chrome for Developers (entegrasyon + performans), caniuse (ikincil ~%94 kapsama), Smashing Magazine (INP/donma/snapshot maliyeti), W3C (WCAG 2.3.3 Animation), Deque (reduced-motion iki katman rehberi), AAArdvark (WCAG 2.3.3 uygulama notları). **Olumsuz/negatif bulgular da var:** Firefox belgeler-arası VT'yi desteklemiyor; geçiş sırasında rendering donar (uzun animasyon = INP riski); tam-viewport anlık görüntü GPU ~200-400 ms + bellek yoğun; flat pseudo-tree clipping'i değiştirir; bitmap snapshot `object-fit`/filtreleri yutar; aynı `view-transition-name` eşzamanlı kullanımı hatadır; `prefers-reduced-motion` yalnız CSS ile **tam** karşılanmaz. |
| Web Search **Alınan Karar** | Karar a-f kalemleri bu bulgularla sabitlendi: (a) **aynı-dokman VT**, sarmalayıcı `NavigationOrchestrator.navigate()` commit'inde (`:64-65` patch + pushState) — fetch/guard akışı **callback dışında** kalır; (b) **progressive enhancement**: `typeof document.startViewTransition === 'function'` yoksa bugünkü davranış birebir korunur, mevcut CSS `transition`/`@keyframes` **fallback** olarak kalır; `prefers-reduced-motion: reduce` → **hem CSS media query hem JS `matchMedia`** ile geçiş tamamen kapalı; (c) **INP/kare bütçesi** (ADR-006): callback yalnız DOM commit, animasyon süresi kısa (yaklaşık ≤300 ms), uzun işler geçişe taşınmaz, gerekirse `skipTransition`; (d) **router entegrasyonu** ADR-004 + ADR-021 (+ ADR-016) sözleşmesi üzerinden — `nav:complete` **ölü yayıncı** olduğu için VT **ona bağlanmaz**, doğrudan canlı commit noktasına bağlanır (ADR-046 ön koşulu); (e) **fragment kapsamı** kademeli: önce liste→detay route geçişleri, sonra view mode (`ViewModeManager.js:75`), sonra tema (`ThemeManager.js:156-158`) — her fragment `view-transition-name` envanterine kaydedilir; (f) **kapsam dışı/ertelenmiş:** belgeler-arası `@view-transition` (Firefox yok), `router/main.js` ölü loader ve index.md düzeltmesi (reset'e erteli) — dokunulmaz, yalnız raporlanır. |
| Web Search **Sonuç** | Araştırma, kullanıcı onaylı kapsamı **destekledi ve dört şartı netleştirdi**: (1) **feature-detect + CSS fallback zorunlu** — API desteksizse davranış değişmez (Baseline aynı-dokman için geçerli, belgeler-arası Firefox'suz); (2) **reduced-motion tam kapama** JS `matchMedia` ile — yalnız CSS media query kabul edilmez (WCAG 2.3.3 + web'deki ortak reçete); (3) **INP bütçesi** — kısa callback + kısa animasyon + `skipTransition`; ölçüm olmadan adım tamamlanmaz (ADR-006); (4) **sarmalama noktası `NavigationOrchestrator`** — `nav:complete` bağlaması bugünkü kodda çalışmaz (0 yayıncı), `view-transition-name` isimleri envanterle çakışmasızdır. Yeni bir kütüphane/animation framework gerekmez (ADR-001); mevcut router + ITCSS + EventBus kalıbı üzerine native API eklenir. |

### §1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-004 + ADR-021 (+ ADR-016) router sözleşmesi | Dual/multi-domain router davranışı **değişmez**; VT sarmalayıcı `NavigationOrchestrator.navigate()` akışının **üstünde** bir katmandır — guard/fetch/hata dalları, `pushState` sırası ve URL normalizasyonu korunur (⚠️ prompt'taki "ADR-005 dual router" düzeltildi — ADR-005 = ultrathink; ADR-083 dosyası diskte YOK) |
| ADR-006 performance targets | Geçiş animasyonu ve callback'i INP/kare hedeflerini **kötüleştiremez** — kısa callback, kısa animasyon, ölçüm kanıtı olmadan adım tamamlanmaz |
| ADR-018 + ADR-044 reduced-motion sözleşmesi | `prefers-reduced-motion` mevcut 29 dosyalık uygulaması **korunur ve genişletilir**; VT, hareket azaltma isteğinde **hiç başlatılmaz** (CSS + JS iki katman) |
| ADR-045 + ADR-044 fragment kancaları | `ViewModeManager.js:75` (`data-view-mode` + `cm-view-css` swap) ve `ThemeManager.js:156-158` (`data-mode`) mevcut davranışları **değiştirilmez**; VT yalnız üstüne bindirilir |
| ADR-046 nav:complete ön koşulu | `nav:complete` canlıda **0 yayıncı** (boot yolu ölü); VT bu olaya **bağlanmaz** — bağlanırsa karar uygulanmamış sayılır |
| ADR-001 vanilla JS / framework yasağı | View Transition API **native**'dir; GSAP benzeri harici animasyon kütüphanesi ve polyfill **getirilmez** |
| ADR-005 zero hallucination | Diskte/kodda kanıtlanmayan hiçbir satır yazılmaz; `⚠️ VERIFICATION REQUIRED` etiketi kullanılır (ADR-083, arşiv `.decisions` yolları) |
| ADR-001-037 frozen dokunulmazlık | Frozen ADR'ler yalnız referanslanır; metinlerine dokunulmaz |
| UTF-8 yazım protokolü | Vault yazımları yalnız `vault-utf8-writer.mjs` (log.md = append-only); PowerShell write cmdlet'leri yasak |
| REDACTED | Token, anahtar, cookie, kullanıcı verisi bu ADR'ye yazılmaz |
| Hallucination disiplini | Diskte olmayan hedefe wiki-link **yazılmaz**; `index.md:89` slug düzeltmesi bu işlemde **eklenmez** (reset'e erteli); ölü kod (`router/main.js`) silinmez — raporlanır |

---

## §2 Karar (Decision)

CoreMusic, aynı-dokman **View Transition API**'yi kademeli olarak devreye alır: geçişin sarmalayıcısı **`NavigationOrchestrator.navigate()` commit noktasıdır** (`:64-65` patch + pushState); API **feature-detect** ile korunur (destek yoksa bugünkü davranış birebir aynı), `prefers-reduced-motion`'da **tamamen kapalıdır**, animasyon/kare bütçesi **ADR-006** sınırları içindedir, router sözleşmesi **ADR-004/021**'dir ve `nav:complete` ölü yoluna **bağlanmaz**; kapsam üç fragment ile sınırlıdır: liste→detay, view mode (ADR-045), tema (ADR-044).

### §2.1 (a) Aynı-Dokman VT — NavigationOrchestrator Sarmalayıcı

- **Kapı:** `typeof document.startViewTransition === 'function'` — kapı kapalıysa kod **bugünkü akışta** yürüdür (branch yok, davranış aynı).
- **Sarmalama:** `NavigationOrchestrator.navigate()` içinde **fetch/guard sonrası** commit işleri VT callback'ine alınır: `contentPatcher.patch()` (`:64`) + `history.pushState` (`:65`) → callback **yalnız DOM/URL commit**; `contentFetcher.fetch()` (`:57`) ve guard bekleme callback **dışında** kalır (iş bitirmeden snapshot alınmaz).
- **Odak/kaydırma:** `focusManager.moveFocus()` (`:70`) ve `scrollRestorer.scrollToTop()` (`:71`) commit sonrası adımlardır — ilk sürümde **callback dışında** tutulur (WCAG odak davranışı + INP); gerekirse tartışma turlarında değerlendirilir.
- **Hata dalları:** `:54,58,60,61,62,75` hata/redirect yollarında DOM commit yok → VT **başlatılmaz** (`aria-busy` davranışı aynen korunur).
- **Kapı kaçışı:** beklenmeyen durumda `skipTransition()` — sessiz düşüş, navigasyon asla VT'ye bağımlı değildir.

### §2.2 (b) Progressive Enhancement + CSS Fallback + Reduced-Motion Tam Kapama

| Katman | Karar |
|---|---|
| **Destek yoksa** | `startViewTransition` yoksa sarmalayıcı **devre dışı** — bugünkü `patch` + `pushState` akışı birebir (Safari 17-/eski Firefox senaryosu) |
| **CSS fallback** | Mevcut `@keyframes` (14 dosya) + `transition` (35 dosya) envanteri **korunur**; VT katmanı bunların **üstünde** ek katmandır, hiçbir bileşen animasyonu VT yüzünden kaldırılmaz |
| **`::view-transition` CSS** | Yeni kurulacak CSS (VT grup/keyframe'leri) ITCSS yapısında, süre **≈ ≤300 ms** mertebesinde; ayrı dosya/blok envantere kaydedilir |
| **Reduced-motion (CSS)** | `@media (prefers-reduced-motion: reduce)` içinde VT animasyonları **`animation: none`** (mevcut 29 dosyalık uygulama hizası — ADR-018/044) |
| **Reduced-motion (JS)** | `window.matchMedia('(prefers-reduced-motion: reduce)')` **true** ise `startViewTransition` **hiç çağrılmaz** — JS katmanı olmadan tam kapama **sağlanmaz** (web'deki ortak reçete); `change` dinleyicisiyle canlı geçiş |

### §2.3 (c) INP / Kare Bütçesi (ADR-006)

1. Callback minimal: **yalnız DOM commit** (fetch/guard/serializasyon dışarıda).
2. Animasyon süresi kısa (≈ ≤300 ms mertebesi, `::view-transition-group` süreleri tek yerden yönetilir) — "uzun ve gösterişli" geçiş yasaktır.
3. Geçiş sırasında rendering donduğu için **uzun iş callback'e taşınmaz**; ağır işler (varsa) önceden/sonraya alınır.
4. **Ölçüm kapı:** INP/long-task ölçümü öncesi-sonrası yapılır; ADR-006 hedeflerini kötüleştiren sürüm yayına **çıkmaz**.
5. Kaçış: `skipTransition()` + kapsam daraltma (bileşen bazlı kapatma) → sorun çıkaran fragment VT'den düşürülür, navigasyon etkilenmez.

### §2.4 (d) Router Entegrasyonu — ADR-004/021 Hizası (⚠️ ADR-005 düzeltmesi tekrarı)

- VT, **router'ın içine değil üstüne** biner: router sözleşmesi (immutable contract, guard zinciri, query-strip `Router.js:63-64`, `scrollRestoration='manual'` `Router.js:54`) **dokunulmaz** — ADR-021.
- Sarmalama **yalnızca** canlı commit noktasında (`NavigationOrchestrator.js:64-65`); `nav:complete` **ölü yayıncı** olduğu için (§1.1-C, ADR-046) VT bu olaya **bağlanmaz** — bağlanırsa sistem test edilemez hale gelir.
- `js/router/main.js` (yükleyen 0) ve `SPARouterAdapter` boot yolu (CoreMusicApp.init çağrılmıyor) **kullanılmaz**; ölü kod **silinmez** (In-Place Refactoring) — yalnız raporlanır.
- ⚠️ **Düzeltme:** "ADR-005 dual router" **yanlıştır** — diskte `ADR-005-ultrathink-protocol`; dual router = [[ADR-004-multi-domain-spa]] + [[ADR-021-spa-router-immutable-contract]] (+ [[ADR-016-url-normalization]]); `ADR-083` dosyası **YOK** → `⚠️ VERIFICATION REQUIRED`. [[ADR-045-multi-domain-view-mode-architecture]]:132 ile aynı düzeltme (ADR-048'de tekrarlanır).

### §2.5 (e) Fragment Kapsamı — Kademeli (liste→detay · view mode · tema)

| Kademe | Fragment | Kanca | `view-transition-name` sorumlusu |
|---|---|---|---|
| 1 | **Liste→detay route geçişleri** | `NavigationOrchestrator` sarmalayıcı (§2.1) | Ana içerik konteyneri (tek isim, tek sahip) |
| 2 | **View mode** (ADR-045) | `ViewModeManager.js:75` `data-view-mode` + `cm-view-css` swap (`:65-80`) | Gövde/ızgara kökü — CSS swap ile **eşzamanlı** |
| 3 | **Tema** (ADR-044) | `ThemeManager.js:156-158` `data-mode` | `documentElement`/arka plan katmanı — palet geçişi VT ile **değil**, mevcut CSS geçişiyle kalmaya yakın (ADR-044 sözleşmesi) |

- Her kademe **ayrı** açılır/kapatır (bileşen bazlı bayrak); kademe 2/3, kademe 1 stabil olmadan açılmaz.
- **Kapsam dışı (ertelenmiş):** belgeler-arası `@view-transition { navigation: auto }` (Firefox desteklemiyor), `router/main.js` canlandırma, `nav:complete` canlandırma (ADR-046'nın kendi işi).

### §2.6 Neden Bu Seçenek?

Sarmalayıcı noktası zaten **tek ve nettir**: DOM commit'i `NavigationOrchestrator.js:64`'te, URL commit'i `:65`'te — VT'yi başka hiçbir yere bağlamak gerekmez (ve `nav:complete` gibi ölü bir yola bağlamak **çalışmazdı**). Reduced-motion altyapısı 29 dosyada **zaten mevcut**, animasyon envanteri ITCSS içinde **kodlanmış** durumda — API'yi bu iskeletin üstüne eklemek sıfırdan sistem kurmaktır. Feature-detect + kademeli kapsam, web araştırmasının dört şartını (fallback, reduced-motion, INP, isim envanteri) tek kararla karşılar; harici kütüphane (alternatif 2) ADR-001'i, MPA `@view-transition` (alternatif 3) ADR-004/021'ı ihlal eder.

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Hiç dokunmama (status quo)** — yalnız mevcut CSS `transition`/`@keyframes` | Sıfır risk, sıfır iş | Liste→detay gibi **büyük DOM değişimlerinde** tutarlı "geçiş" yok (kayıp sürüyor); API Baseline'a girdiği için fırsat kaçıyor | ADR-018/044/045'teki UX hedefleri tek başına karşılanmıyor; mevcut envanter **fallback olarak korunur** — sıfırlanmaz (§2.2) |
| 2 | **Harici animasyon kütüphanesi** (GSAP tarzı timeline) | Zengin zaman çizelgesi, eski tarayıcı desteği | ADR-001 vanilla/framework yasağı ihlali, bağımlılık/benizersizlik artışı, yine de natif geçiş hissi vermez | Web araştırması + ADR-001: native API yeterli; bağımlılık maliyeti getiriyor |
| 3 | **Belgeler-arası `@view-transition { navigation: auto }`** (MPA'daki gibi) | Router'a hiç dokunmadan tüm geçişler | **Firefox desteklemiyor**; SPA router (ADR-004/021) ile uyumsuz (belge değişimi yok — zaten aynı dokman); kontrol yüzeyi dar | Kapsam 3 (a): aynı-dokman API; belgeler-arası **ertelendi** (§2.5 kapsam dışı) |
| 4 | **Her navigasyonda VT** (kapsam sınırı olmadan) | Tek bayrak, genel his | INP riski artar (ADR-006), reduced-motion ihlali yüzeyi büyür, `view-transition-name` çakışma olasılığı ve snapshot maliyeti (GPU ~200-400 ms, bellek) ölçeklenir | Kademeli kapsam (§2.5) + INP bütçesi (§2.3): önce 3 fragment, ölçümle açılır |
| 5 | **`nav:complete` üzerinden entegrasyon** (SPARouterAdapter olayına bağla) | Olay zaten tanımlı (`SPARouterAdapter.js:113`) | Canlıda **0 yayıncı** (boot yolu ölü — §1.1-C); bağlanırsa kod **hiç çalışmaz** ve test edilemez | ADR-046 bulgusu: ölü yola bağlanılmaz; sarmalayıcı canlı `NavigationOrchestrator`'dır (§2.4) |

---

## §4 Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- Liste→detay, view mode ve tema değişimleri **tek görsel dile** kavuşur; bugünkü anlık DOM flip'leri kademeli olarak geçişe dönüşür — UX kaybı (§1.2) kapanır.
- Entegrasyon **tek noktada** (`NavigationOrchestrator.js:64-65`) ve **geri alınabilir**: sarmalayıcı kaldırılırsa sistem bugünkü davranışına döner, router sözleşmesi hiç değişmez (ADR-021).
- **Progressive**: desteksiz/eski tarayıcıda **davranış değişmez** — risk yüzeyi yalnız destekli tarayıcılarda açılır ve orada da reduced-motion/INP kapıları vardır.
- Reduced-motion envanteri (29 dosya) **genişletilir**, ADR-018/044 sözleşmesi güçlenir; JS guard ile WCAG 2.3.3 yönü kapanır.
- Harici bağımlılık **yok** (ADR-001): native API + mevcut ITCSS/EventBus kalıpları.

### 4.2 Olumsuz Sonuçlar

- **Yeni hata yüzeyi:** `view-transition-name` çakışmaları, flat pseudo-tree clipping farkları, bitmap snapshot'ın `object-fit`/filtre kaybı — envanter + test gerektirir.
- **Ölçüm zorunluluğu:** INP öncesi/sonrası ölçümü yapılmadan adım tamamlanamaz (ADR-006) — iş yükü ekler.
- **Tarayıcı davranışı çeşitliliği:** Safari 18.x ile Chrome/Firefox 144 arasında ince farklar; fallback dalı **ömür boyu** bakılacak (feature-detect kuru).
- `::view-transition` CSS'i **yeni katman** → ITCSS yerleşimi + envanter kaydı (§2.2) — mevcut 101 dosyalık yapıya bir sorumluluk daha.
- Odak/kaydırma (`:70-71`) ilk sürümde VT dışında tutulduğu için **geçiş + anında scroll** kombinasyonu görsel olarak sert durabilir (değerlendirme §5.1 adım 8 — debate şartı 4).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| 1 **INP kötüleşme**: uzun animasyon + commit ağır → ADR-006 hedefleri aşılır | 3 (Olası) | Yüksek (UX + SEO) | Kısa callback (yalnız commit, §2.3-1) + süre ≈ ≤300 ms (§2.3-2) + **ölçüm kapı** (§2.3-4) + `skipTransition` (§2.3-5) |
| 2 **Desteksiz tarayıcıda davranış değişimi**: yanlış sarmalama eski akışı kırar | 2 (Mümkün) | Yüksek (navigasyon kırılır) | Feature-detect kapısı (§2.2) + "kapalıyken kod tamamen eski yol" testi (§5.1 adım 7) — **regresyon testi kapı** |
| 3 **Reduced-motion ihlali**: JS guard unutulur → hareket azaltma isteğiyle geçiş gösterilir (WCAG 2.3.3) | 3 (Olası) | Orta-Yüksek (erişilebilirlik + ADR-018/044 ihlali) | JS `matchMedia` guard **zorunlu** (§2.2) + CSS media query + otomatik test (reduced-motion profile'da `startViewTransition` çağrı sayısı 0) |
| 4 **`nav:complete`'e bağlama hatası**: ölü yayıncıya bağılanır → özellik hiç çalışmaz | 2 (Mümkün) | Yüksek (sessiz başarısızlık) | Sarmalayıcı **yalnız** `NavigationOrchestrator` (§2.4) + ön koşul testi: `nav:complete` yayını olmadan da geçiş çalışır (§5.1 adım 6) |
| 5 **`view-transition-name` çakışması**: iki öğe aynı isim → geçiş hata/atlama | 3 (Olası) | Orta (geçiş bozulur, navigasyon etkilenmez) | İsim envanteri (§2.5 — tek sahip kuralı) + konsolda eşzamanlılık testi + `skipTransition` geri düşüşü |
| 6 **Snapshot maliyeti**: tam-viewport anlık görüntü GPU ~200-400 ms + bellek (özellikle düşük güçlü cihaz) | 3 (Olası) | Orta (kare düşüşü) | Kapsamı dar tut (§2.5 — 3 fragment) + animasyon kısa + gerektiğinde kapsam daraltma/bileşen bazlı kapatma (§2.3-5) |

### 4.4 Fallback (geri birleşim / geri dönüş)

1. **Sarmalayıcı kapatılırsa** (`vt: false` / tek satır geri alma) sistem bugünkü davranışına döner: `patch` + `pushState` doğrudan — kayıp yok, (a) karşılanmamış sayılır.
2. **Destek kapalıysa** (feature-detect false) zaten bugünkü yol yürür — hiçbir kullanıcı etkilenmez.
3. **Reduced-motion tetiklenirse** VT hiç başlamaz; mevcut CSS envanteri (29 dosya) devrede kalır.
4. **Bir fragment sorun çıkarırsa** yalnız o kademe kapatılır (§2.5 kademeli yapı) — diğerleri ve navigasyon etkilenmez.
5. **Ölçüm INP düşüşü gösterirse** sürüm yayına çıkmaz; kapsam daraltılır veya karar debate'e geri döner (§5.3 şart 3).
6. Tüm geri dönüşler `.ai/log.md`'ye **append** ile kaydedilir; dosya adları değişmez (In-Place Refactoring), frozen ADR'ler (001-037) etkilenmez.

---

## §5 Uygulama (Implementation)

### §5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **Feature-detect sarmalayıcı:** `NavigationOrchestrator.navigate()` içinde `document.startViewTransition` kapısı; commit işleri (`:64-65`) VT callback'ine, fetch/guard (`:57`) ve hata dalları dışarıda; kapı kapalıyken **eski yol birebir** | UI Designer | 1 gün |
| 2 | **CSS VT katmanı:** `::view-transition-*` blokları ITCSS yapısında, süre ≈ ≤300 ms tek merkezden; envantere kayıt (dosya adı + süre) | UI Designer | 0.5 gün |
| 3 | **Reduced-motion iki katman:** CSS media query (mevcut hiza) + JS `matchMedia` guard (`change` dinleyicisi dâhil); guard **true** ise VT hiç başlamaz | UI + a11y | 0.5 gün |
| 4 | **`view-transition-name` envanteri:** kademe 1-3 için isimler atanır (tek sahip kuralı); çakışma testi (eşzamanlı iki isim = hata) | UI Designer | 1 gün |
| 5 | **INP/kare ölçümü:** öncesi-sonrası INP + long-task ölçümü (ADR-006 hedefleri) — **rapor olmadan adım tamamlanmaz** | QA | 1 gün |
| 6 | **nav:complete ön koşul testi:** VT, `nav:complete` yayını **olmadan da** çalışır (0 yayıncı senaryosu simüle edilir); `SPARouterAdapter`/`router/main.js` ölü yollarına **bağlantı yok** | QA | 0.25 gün |
| 7 | **Regresyon testleri:** destekli/desteksiz tarayıcı, reduced-motion profile (VT çağrı 0), fetch/hata dallarında VT'siz akış, geri/ileri (popstate), `aria-busy` davranışı | QA | 1 gün |
| 8 | **Odak/kaydırma kararı:** `:70-71` adımlarının VT içinde/dışında değerlendirilmesi (§4.2-4) — debate şartı 4 ile birlikte kapatılır | UI + QA | 0.5 gün |
| 9 | **Debate 3 tur** tamamlanır → §5.3 şartları bağlayıcı hale gelir; §7 Debate/Tech Lead satırları güncellenir — ✅ **tamamlandı (2026-09-29: 3 tur / 20 persona, 18/2/0 KABUL → §5.4 + §7.1)** | Vault Steward | 0.5 gün ✅ |
| 10 | **Ertelemeler (report-only):** (i) `.ai/.decisions/index.md:89` slug wiki-link düzeltmesi → **bir sonraki vault reset'i** (bu işlemde dokunulmadı); (ii) `js/router/main.js` ölü loader + `SPARouterAdapter` boot yolu temizliği → **Tech Lead onayı** (In-Place Refactoring: onaysız değişiklik yok) | Vault Steward + Tech Lead | 0.2 gün |

**Toplam ≈ 6.45 gün** (adım 5 + 7 kapı — bu ikisiz yayına çıkılmaz).

### §5.2 Geri Dönüş Planı

1. **Adım 1 tersi:** VT sarmalayıcı tek bayrakla kapatılır → `navigate()` bugünkü akışına (`:64-65` doğrudan) döner; router sözleşmesi hiç değişmediği için **yan etkisi yoktur**.
2. **Adım 2 tersi:** `::view-transition` CSS blokları kaldırılır — VT aktif olsa bile geçiş **anonim/varsayılan** olur; mevcut animasyon envanteri etkilenmez.
3. **Adım 3 tersi (geri alınamaz şart):** JS guard **kaldırılmaz** — kaldırılırsa (c) ve ADR-018/044 ihlal edilir; yalnız CSS katmanı gevşetilebilir, guard kalır.
4. **Adım 4 tersi:** isimler kaldırılır → geçişler tekil/parçalı çalışır (kötü görünüm, kırık navigasyon değil); isim envanteri kaydı **silinmez**, "pasif" işaretlenir.
5. **Adım 5/6/7 geri alma yoktur:** bu adımlar **yalnız ölçüm/test** içerir — geçici pasifleştirme mümkün, kalıcı silme **yasak** (kanıt kaybı).
6. **Adım 10 geri alma yoktur:** ertelenen işler henüz **yapılmadı**.
7. Tüm geri dönüşler `.ai/log.md`'ye **append** ile kaydedilir; dosya adları değişmez (In-Place Refactoring), frozen ADR'ler (001-037) etkilenmez.

---

### §5.3 Debate Şartları (4 şart — debate taslağı; ✅ debate TAMAMLANDI → bağlayıcı karşılığı §5.4)

> **Durum:** Debate ✅ **tamamlandı** (3 tur / 20 persona, 18/2/0 → KABUL, 2026-09-29 — §7.1). Bu tablo debate öncesi **taslağıdır**; debate'in bağlayıcı **3 şartı §5.4**'te toplandı (aşağıdaki 4 taslak madde §5.4 eşleme sütununda korunur). §5.3 + §5.4 şartları **bağlayıcıdır**; oynanmadan (a)-(f) kalemleri uygulanmış sayılmaz.

| # | Şart | Kapsam | Kabul ölçütü |
|---|---|---|---|
| 1 | **Feature-detect + fallback** | Kapı kapalıyken kod **tamamen eski yol**; destekli/desteksiz tarayıcıda navigasyon davranışı **aynı** | §5.1 adım 7 regresyon seti geçti — eski-tarayıcı senaryosunda davranış farkı 0 |
| 2 | **Reduced-motion tam kapama** | CSS media query **+** JS `matchMedia` guard; guard true iken `startViewTransition` çağrı sayısı **0** | Otomatik test: reduced-motion profile'da VT çağrı 0 + mevcut 29 dosyalık envanter korunmuş |
| 3 | **INP/kare bütçesi (ADR-006)** | Kısa callback + ≈ ≤300 ms animasyon; öncesi/sonrası ölçüm | Ölçüm raporu: INP ADR-006 hedeflerini **kötüleştirmedi** — adım 5 olmadan yayına çıkılmaz |
| 4 | **Sarmalama noktası + odak kararı** | VT yalnız `NavigationOrchestrator`'a bağlıdır; `nav:complete` **bağlantı yasak** (0 yayıncı); `:70-71` odak/kaydırma konumu debate'te kesinleşir | Adım 6 ön koşul testi geçti + odak/kaydırma kararı §7.1'de kayıtlı |

> **Kaynak:** §7.1 Debate Kaydı (✅ TAMAMLANDI — 3 tur / 20 persona, 18/2/0 KABUL). Bu taslak şartlar debate ile **bağlayıcı** hâle geldi; bağlayıcı 3 şart §5.4'tedir.

### §5.4 Debate Bağlayıcı Şartları (3 şart — ✅ KABUL, 2026-09-29)

> **Durum:** ✅ Debate **tamamlandı** (3 tur / 20 persona — tur 1: 16 kabul/neutral + 4 uyarı · tur 2: 4 itiraz→çözüm · tur 3: 18 kabul / 2 çekimser / 0 red → **KABUL**; kayıt §7.1). Aşağıdaki 3 şart **bağlayıcıdır**; §5.3'teki 4 taslak şart bu maddelerde toplanır ve eşlenir. Şartlar karşılanmadan (a)-(f) kalemleri uygulanmış sayılmaz.

| # | Şart | Kapsam | Kabul ölçütü | §5.3 eşlemesi |
|---|---|---|---|---|
| 1a | **Hücre düzeltmesi — vendor `@keyframes` 4/20** | §1.1-B vendor `@keyframes` hücresi "1 dosya/1 isabet" → disk kanıtı **4 dosya/20 isabet** (`bootstrap.css` + `.min` + `.rtl` + `.rtl.min`, 5'er); türetilmiş toplamlar (§1.1-B bulgu özeti, §1.2, §2.2) 11 → **14 dosya** | Vault hücresi = disk taraması (4/20); `@keyframes` toplam **14 dosya / 48 isabet** | — (debate bulgusu — §1.1-B doğruluğu) |
| 1b | **Nav zinciri bağı (ADR-046)** | `app.init()` **0 çağrı** + `nav:complete` **0 yayıncı** (`SPARouterAdapter.js:113` emit / `ScrollManager.js:26` tek dinleyici) ADR-046 kapsamına bağlanır; VT bu olaya **bağlanmaz**, canlı `NavigationOrchestrator` sarmalayıcısında (§2.1) devreye alınır — nav zinciri çözüldükten sonra VT-olay entegrasyonu yeniden değerlendirilir (karar (e) değişmez) | §5.1 adım 6 ön koşul testi geçti (`nav:complete` yayını **olmadan** VT çalışır) + boot yolu temizliği Tech Lead onayı (§5.1 adım 10(ii)) | §5.3 şart 4 (sarmalama noktası) |
| 2 | **INP ölçüm kapısı** | Kare düşüşü + INP öncesi/sonrası ölçüm (ADR-006); **ölçümsüz** adım kapanmaz | §5.1 adım 5 ölçüm raporu: INP ADR-006 hedeflerini kötüleştirmedi + kare düşüşü eşiği içinde | §5.3 şart 3 |
| 3 | **Feature-detect + reduced-motion CI testi** | İki katman otomatik test: (i) API **destek yokken** davranış birebir eski yol, (ii) `prefers-reduced-motion: reduce` profile'da `startViewTransition` çağrı sayısı **0** | §5.1 adım 7 regresyon seti CI'da yeşil — bu iki test olmadan yayın yok | §5.3 şart 1 + şart 2 |

---

## §6 İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme, 16 Hard Guardrail |
| [[../../raw/AGENTS.md]] | Agent registry, onay/escalation §10, frozen kuralı §25.3 |
| [[../../raw/WORKFLOW.md]] | Süreçler, fazlar |
| [[../../raw/brain.md]] | Mimari karar özeti |
| [[../../index.md]] | Master katalog |
| [[../../raw/keys.md]] | Keyword haritası |
| [[../../raw/MEMORY.md]] | Session hafızası |
| [[../../log.md]] | Audit trail (append-only) |
| [[../../raw/glossary.md]] | Terimler (View Transition, view-transition-name, INP, feature-detect) |
| [[../index.md]] | Karar dizini — **ADR-048 satırı VAR (satır 89); slug wiki-link düzeltmesi reset'e ertelendi (§5.1 adım 10)** |
| [[CLAUDE]] | `accepted/` dizin kuralı |
| [[../../.templates/adr/adr-template]] | Bu ADR'nin zorunlu şablonu (Guardrail #16) |
| [[../../.templates/index]] | Şablon envanteri (SRP) |
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS/framework yasağı — native API kararı (§3-2 reddi) |

| Dosya | İlişki |
|-------|--------|
| [[ADR-004-multi-domain-spa]] | Dual/multi-domain router zemini — karar (d) (⚠️ "ADR-005 dual router" düzeltmesi) |
| [[ADR-005-ultrathink-protocol]] | Zero hallucination — §1.1 dürüst etiket (**dual router değildir**) |
| [[ADR-006-performance-targets]] | INP/kare bütçesi — karar (c) + şart 3 |
| [[ADR-016-url-normalization]] | URL normalizasyon sözleşmesi — karar (d) korunur |
| [[ADR-018-footer-player-vaporwave]] | `prefers-reduced-motion` hizası — karar (b) |
| [[ADR-021-spa-router-immutable-contract]] | Router immutable contract — karar (d): VT router'ın **üstünde** katman |
| [[ADR-044-dynamic-user-theme-engine]] | Tema fragment kanı (`data-mode`) — karar (e) kademe 3; reduced-motion envanter kaydı (satır 70 — kapsam farkı §1.1-B) |
| [[ADR-045-multi-domain-view-mode-architecture]] | View mode fragment kanı (`data-view-mode`) — karar (e) kademe 2; dual router düzeltmesi (satır 132) ile aynı |
| [[ADR-046-cross-view-state-preservation]] | `nav:complete` 0 yayıncı + `Router.js:54,63-64` bulguları — karar (d) ön koşulu |
| [[ADR-047-login-redirect-session-bridge]] | Önceki ADR — biçim/kanıt etiketi referansı |
| §2.1 Sarmalayıcı | `NavigationOrchestrator.js:64-65` commit sarmalama + `:57` fetch dışarıda |
| §2.2 İki Katman Fallback | Feature-detect + CSS fallback + JS `matchMedia` guard |
| §5.3 Debate Şartları (4) | **KABUL koşulu (debate sonrası bağlayıcı):** (1) feature-detect + fallback, (2) reduced-motion tam kapama, (3) INP bütçesi ölçümü, (4) sarmalama noktası + odak kararı |
| §5.4 Debate Bağlayıcı Şartları (3) | **✅ KABUL bağlayıcı (3 tur / 20 persona, 18/2/0):** (1a) vendor `@keyframes` 4/20 hücre düzeltmesi + (1b) nav zinciri ADR-046 bağı, (2) INP ölçüm kapısı, (3) feature-detect/reduced-motion CI testi |
| `assets.coremusic.net/js/router/NavigationOrchestrator.js` | Canlı commit noktası (`:43-71`) — **kod kanıtı (düz metin, wiki-link değil)** |
| `assets.coremusic.net/js/main.js` (v6.1.0) · `js/core/CoreMusicApp.js:56,75` · `js/router/SPARouterAdapter.js:113` · `js/features/ScrollManager.js:26` | Boot zinciri + ölü `nav:complete` yolu — **kod kanıtı (düz metin)** |
| `shared/src/PageRouter/HtmlShellRenderer.php:165` | Tek canlı script yüklemesi — **kod kanıtı (düz metin)** |
| `.ai/archives/prompt1-spa-router-2026-08-15.md:392,423,427,556` | **Düz metin:** ADR-048 adının + `startViewTransition` örneğinin geçtiği arşiv satırları (slug kaynağı; arşiv `.decisions` yolu nokta eksik — wiki-link yazılmadı) |
| ⚠️ `ADR-083` (SPA router) | **Diskte dosya YOK** → düz metin + `⚠️ VERIFICATION REQUIRED`, wiki-link yazılmadı |

> **Wiki-link doğrulaması:** Yukarıdaki vault hedeflerinin **tamamı** yazımdan önce `Test-Path` ile diskte doğrulanmıştır (2026-09-29) — 24 hedef / 24 diskte. Diskte **olmayan** hedefe (`ADR-083-*`, arşiv `.decisions` yolu) wiki-link **yazılmamış**, düz metin + `⚠️ VERIFICATION REQUIRED` kullanılmıştır. `ADR-048-*` bu dosyanın kendisidir (yazım sonrası doğrulanır).

---

## §7 Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Vault Steward | 2026-09-29 | ✅ |
| Tech Lead | Tech Lead | 2026-09-29 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### §7.1 Debate Kaydı

**✅ TAMAMLANDI (2026-09-29) — 3 tur / 20 persona · sonuç: 18 kabul / 2 çekimser / 0 red → KABUL · bağlayıcı 3 şart → §5.4.**

| Tur | Tür | Sonuç |
|---|---|---|
| 1 | **Bulgu turu (20 persona)** | `startViewTransition` canlı 98 JS'de **0 isabet** → §1.1-A **PLANNED** · `.view-transition` CSS **0 isabet** → PLANNED · ilk-party `@keyframes` **10 dosya/28**, `transition` **35 dosya/141 isabet** (§1.1-B; üst görevde 140 yazıyordu — disk 141) · `prefers-reduced-motion` ilk-party **17** + vendor **12** (toplam 29/153) · `main.js:82,85` canlı Router var ama **`app.init()` 0 çağrı** → SPARouterAdapter örneklenmiyor → **`nav:complete` 0 yayıncı** (`SPARouterAdapter.js:113` emit, `ScrollManager.js:26` tek dinleyici) · `NavigationOrchestrator.js:43-71` doğrulandı · Baseline **2025-10-16** (web.dev/MDN/caniuse doğrulandı) · **hatalı hücre** vendor `@keyframes` 1/1 → disk **4/20** (bootstrap.css/.min/.rtl/.rtl.min, 5'er) · 6 sorgu / 8 kaynak · düzeltme: **ADR-005 = ultrathink-protocol (dual router değil)** | **16 kabul/neutral · 4 uyarı** (Perf: INP bütçesi + hatalı hücre · QA: feature-detect · Critic: nav zinciri ADR-046 şartı) |
| 2.1 | **İtiraz → çözüm (1/4)** | Hatalı hücre vendor `@keyframes` **1/1 → 4/20** → §1.1-B hücresi düzeltildi + türetilmiş toplamlar 11 → 14 dosya (§1.1-B bulgu özeti, §1.2, §2.2) | **Şart 1a** |
| 2.2 | **İtiraz → çözüm (2/4)** | `app.init()` **0** + `nav:complete` **0 yayıncı** → nav zinciri **ADR-046'ya bağlanır**; VT bu olaya bağlanmaz, nav zinciri çözüldükten sonra VT devreye/kademelendirme yeniden değerlendirilir | **Şart 1b** |
| 2.3 | **İtiraz → çözüm (3/4)** | INP bütçesi **ölçümsüz** → kare düşüşü/INP ölçüm kapısı: ölçüm raporu olmadan adım kapanmaz | **Şart 2** |
| 2.4 | **İtiraz → çözüm (4/4)** | Feature-detect **zorunlu** → feature-detect + reduced-motion **iki katman CI testi** | **Şart 3** |
| 3 | **Oy (3. tur)** | **18 kabul / 2 çekimser / 0 red → KABUL** · 3 şart §5.4'te bağlayıcı hâle geldi | §5.4 |

- **Kapsam:** (a) aynı-dokman VT `NavigationOrchestrator` sarmalayıcı · (b) progressive enhancement + CSS fallback + `prefers-reduced-motion` tam kapama (CSS + JS) · (c) INP/kare bütçesi (ADR-006, ≈ ≤300 ms) · (d) router entegrasyonu ADR-004/021 (⚠️ "ADR-005 dual router" düzeltildi; `nav:complete` bağlantısı yasak) · (e) fragment kapsamı kademeli: liste→detay → view mode (ADR-045) → tema (ADR-044) · (f) kapsam dışı/ertelenmiş: belgeler-arası `@view-transition`, `router/main.js`/`SPARouterAdapter` ölü yol temizliği, index.md:89 düzeltmesi — **kullanıcı onaylı**.
- **Kanıt:** canlı 98 JS'te `startViewTransition` **0** · view-transition CSS artefaktı **0** · 101 CSS envanteri (`@keyframes` ilk-party 10/28; `prefers-reduced-motion` 29/153) · `NavigationOrchestrator.js:43-71` commit zinciri · `main.js:82,85,96-176` → `CoreMusicApp.init()` **0 çağrı** → `SPARouterAdapter` örneklenmiyor → `nav:complete` **0 yayıncı** · `router/main.js` yükleyen **0** · arşiv `prompt1:423,427`.
- **Araştırma:** 6 sorgu / **8** adlandırılmış kaynak (protokol `10-web-research-protocol.md`).
- **Tech Lead:** ✅ **KABUL** (2026-09-29 — 3 şart §5.4 bağlayıcı) · **Arch Lead:** ⏳ (beklemede).
- **Odak/kaydırma kararı (§5.3 şart 4 · §5.1 adım 8):** debate'te **kapatılmadı** — açık madde olarak §5.1 adım 8'de kaldı (karar kaydı **YOK** → `⚠️ VERIFICATION REQUIRED`).
- **Wiki-link teyidi:** 24/24 hedef `Test-Path` ile doğrulandı (2026-09-29).

---

*ADR-048 v1.0.0 | 2026-09-29 | Created*
*Authority: CoreMusic Vault — View Transition API Integration*
*Mode: Red Team · Human Mode · Truth Mode*
