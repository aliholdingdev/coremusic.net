---
title: "CoreMusic — ADR-045: Multi-Domain View Mode Architecture (grid/liste/kart/galeri · URL birincil kalıcılık · domain varsayılanları · ARIA radio + klavye · virtual list veri boyutu eşiği)"
type: "architecture-decision"
category: "frontend"
date: "2026-09-28"
updated: "2026-09-28"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic çok-domain görünüm modu mimarisi: (a) grid/liste/kart/galeri mod kümesi domain'e göre, (b) kalıcılık URL birincil (paylaşılabilir, router hizası) + localStorage fallback, (c) domain bazlı varsayılan (müzik=liste, galeri=kart, admin=tablo) + kalıcı kullanıcı override'ı, (d) klavye geçişi + ARIA role=radio/radiogroup (WCAG 2.2 AA), (e) virtual list vs basit DOM kararı veri boyutu eşiğiyle"
kaynak: "Kullanıcı onaylı kapsam (a-e) + disk/kod kanıtı taraması (2026-09-28: ViewModeManager.js 83 satır, ViewModeManager.php 90 satır, SidebarManager liste/grid + localStorage, 09_ViewModes 6 satır kırık import, user_preferences view_mode yok, view= 0, VirtualScroller 0 çağrı) + web araştırması (8 sorgu / 56 adlandırılmış kaynak)"
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-045: Multi-Domain View Mode Architecture (Çok-Domain Görünüm Modu Mimarisi)

> **Durum:** ✅ **ACCEPTED** (kullanıcı onaylı kapsam a-e) · **Tarih:** 2026-09-28 · **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** · **Tech Lead:** ✅ · **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `ADR-045-multi-domain-view-mode-architecture`
> **İlgili kararlar:** [[ADR-001-vanilla-js-itcss]] (ITCSS + framework yasağı — görünüm katmanı bu iskelete bağlanır) · [[ADR-004-multi-domain-spa]] (subdomain SPA iskeleti — "multi-domain"ın kaynağı) · [[ADR-021-spa-router-immutable-contract]] (router sözleşmesi — URL birincil kalıcılığın dayanağı) · [[ADR-006-performance-targets]] (kritik yol tavanları — liste boyutu eşiği) · [[ADR-018-footer-player-vaporwave]] (görsel dil + `prefers-reduced-motion` hizası) · [[ADR-044-dynamic-user-theme-engine]] (format referansı + tema/öznitelik hizası) · [[ADR-031-mobile-strategy-pwa-flutter]] (ADR-045/046 görünüm konseptini referanslar) · [[ADR-005-ultrathink-protocol]] (zero hallucination protokolü — §1.1 dürüst etiket) · [[../index.md]] (`:87` slug satırı) · [[../../brain.md]]
> **Ad gerekçesi:** slug `ADR-045-multi-domain-view-mode-architecture` **diskteki gerçek index kaydından** alınmıştır (`[[../index.md]]:87`) — uydurulmadı. Bu numara kodda **çoktan referanslanıyor**: `assets.coremusic.net/js/managers/ViewModeManager.js:3` ("Görünüm modu yönetimi (ADR-045)"), `shared/src/ViewMode/CLAUDE.md:21,31`, `shared/AGENTS.md:58`, `.ai/keys.md:220,280`, `.ai/index.md:669`, `.ai/brain.md:1004` → dosya varlığı boşluk doldurur.
> **Düzeltme ertelendi:** `[[../index.md]]:87` satırındaki `[[../brain.md]] ADR-045-…` biçimi `[[accepted/ADR-045-multi-domain-view-mode-architecture]]` olarak düzeltilmesi **bir sonraki vault reset'ine ertelenmiştir** (bu işlemde index.md'ye dokunulmadı — onaysız dosya/satır değişikliği yok). Ayrıntı: §5.1 adım 9.
> **Frozen notu:** ADR-001-037 **dokunulmamıştır** (yalnız atıf). Bu dosya Active aralığındadır, frozen değildir.

---

## §1 Bağlam (Context)

### §1.1 Mevcut Durum (disk + kod kanıtı — dürüst etiket, 2026-09-28 taraması)

Etiketler: **IMPLEMENTED** = diskte kod kanıtıyla ispatlı · **PLANNED** = kararlaştırılmış, karşılığı kodda yok · **ÇELİŞKİ** = iki kayıt birbiriyle uyuşmuyor · **KAPSAM DIŞI** = ilgili alanda hiç kod yok (hiçbiri yumuşatılmadı).

#### A) Görünüm modu çekirdeği (JS + PHP) — IMPLEMENTED, etkileşim PLANNED

| Kanıt | İçerik | Etiket |
|---|---|---|
| `assets.coremusic.net/js/managers/ViewModeManager.js` | **83 satır** — 4 panel modu `home\|pro\|studio\|car` (`:11-20`), docblock "Görünüm modu yönetimi (ADR-045)"; tespit sırası `body[data-view-mode]` → yol öneki (`/studio`,`/pro`,`/car`) → `home` (`:49-58`); `link#cm-view-css` takası + `body[data-view-mode]` yazımı (`:61-77`) | **IMPLEMENTED** |
| `setMode()` tetikleyicisi | `setMode` (`:38`) dışarıdan **çağrılmıyor**; `viewmodechange` yayını (`:45`) için dinleyici **0** | **IMPLEMENTED** (çekirdek) / **PLANNED** (etkileşim) |
| `assets.coremusic.net/js/device-loader.js` | `:161,172,306,411` — `viewMode` seçeneği, `v-{viewMode}.css` yükleme, `data-view-mode` okuma | **IMPLEMENTED** |
| `assets.coremusic.net/js/main.js` | `:81-83` `ViewModeManager` instantiate + `init()` + `registerModule('viewMode')` | **IMPLEMENTED** |
| `shared/src/ViewMode/ViewModeManager.php` | **90 satır** — `detect()`: `session['view_mode']` > route öneki (`/studio`,`/pro`,`/car`) > `home` (`:35-57`); `VIEW_CSS` 4 hedef (`:15-20`); `injectDataAttribute` (`:82-89`) | **IMPLEMENTED** (okuma) |
| `$_SESSION['view_mode']` yazımı | Kod tabanında yazan kod **0** (grep `view_mode` → yalnız okuma + CLAUDE.md iddiası) | **PLANNED** |
| `shared/src/PageRouter/HtmlShellRenderer.php` | `:62` `ViewModeManager::detect($sessionData, $route)` · `:98` shell'e iletim · `:125-126` `data-view="…"` yazımı | **IMPLEMENTED** |
| `shared/src/Device/` | `DeviceRenderer.php:31,85-87,112,141` (`data-view-mode`) · `DeviceManager.php:113,143,319,374` · `DeviceCssMap.php:28,45-55` (`VIEW_MODE_CSS` + fallback `home`) | **IMPLEMENTED** |
| Testler | `shared/tests/Unit/Device/DeviceRendererTest.php:81` (`data-view-mode="home"`), `DeviceDetectorTest.php:170-175` (override ile mod değişimi) | **IMPLEMENTED** (testli) |

#### B) Alan (domain) bazında yerleşim modları — karma etiket

| Domain / katman | Kanıt | Etiket |
|---|---|---|
| **assets (sidebar)** | `js/managers/SidebarManager.js:388-403` liste/grid butonları (`data-view="list\|grid"`, `aria-label` + `title`), `:697-704` tıklama dinleyicisi, `:729-749` `#setAlbumView` → `.sidebar__items--grid` / `--list` sınıf takası + `sidebar:viewchange` yayını | **IMPLEMENTED** |
| **assets (CSS)** | `Css/03_Layout/_sidebar.css:211` `--active`, `:237,243,252` `items--grid` (`repeat(2,1fr)` grid), `:589` `.vs-spacer` | **IMPLEMENTED** |
| **home** | `home.coremusic.net/pages/components/recent-tracks.php:19` `home_song__btn-card-grid` (+ `:6,13` "2-column grid", `.home-mini-card-grid` Figma v3.0.0); `Css/04_Components/_widget-grid.css` (`.widget-grid`, `.widget-card`) | **IMPLEMENTED** (sabit yerleşim) |
| **home (kullanıcı geçişi)** | `.home-*` / `.widget-*` için liste↔kart geçiş butonu **0**; `Css/05_Pages/*.css` içinde `view-toggle`/`data-view` seçicisi **0** | **PLANNED** |
| **shared bileşen** | `src/Component/ListComponent.php:16,34-40` — `layout` = `default\|compact\|grid` → `cm-list--{layout}` sınıfı | **IMPLEMENTED** (PHP) / **PLANNED** (CSS: canlı `Css/` içinde `cm-list--` seçicisi **0**) |
| **auth** | `auth.coremusic.net` içinde `viewMode` / `view=` / `grid` izi **0** | **KAPSAM DIŞI** |
| **music / admin / media / api** | `shared/config/domain.php:11-14` subdomain **tanımı** var; depoda dizin **yok**, görünüm kodu **yok** → "müzik=liste / admin=tablo" varsayılanları hedefleniyor | **PLANNED** |
| **social** | Depoda `social.coremusic.net` dizini **yok**, `domain.php`'de de **tanımlı değil** (7 subdomain: auth, home, assets, music, admin, media, api) | **PLANNED** (kapsam dışı) |
| **galeri modu** | `gallery` kod tabanında **0** isabet (JS/CSS/PHP) | **PLANNED** |
| **ViewMode CSS** | `Css/09_ViewModes/` **canlı dizinde YOK**; yalnız `Css copy/09_ViewModes/` (v-home/v-pro/v-studio/v-car + CLAUDE.md). JS `VIEW_CSS` (`ViewModeManager.js:15-20`), PHP `VIEW_CSS` (`ViewModeManager.php:15-20`) ve `device-loader.js:172` hepsi bu yolu istiyor | **ÇELİŞKİ** |
| **Kırık importlar** | `Css/main.css:58-60` (`./09_ViewModes/v-home|v-pro|v-studio.css`) + `08_Devices/d-4k.css`, `d-desktop.css`, `d-laptop.css` (her biri `../09_ViewModes/…` ×3) → toplam **6 satır / 12 `@import` hedefi** kırık; `v-car.css` hiçbir `@import`'te geçmiyor (yalnız JS/PHP dinamik yolu) | **ÇELİŞKİ** |
| **Envanter iddiası** | `assets.coremusic.net/AGENTS.md` §2 envanteri `Css/09_ViewModes/`'u canlı ağaçta gösteriyor | **ÇELİŞKİ** |
| **Spec dosyası** | `.ai/architecture/k11-ux/README.md:456` → `view-mode.md` (K11.8) dosyası diskte **yok** | **ÇELİŞKİ** |

#### C) Kalıcılık katmanları (URL / localStorage / oturum / DB)

| Kanıt | İçerik | Etiket |
|---|---|---|
| localStorage | `SidebarManager.js:49-82` `SidebarCache` (`localStorage` getItem/setItem/removeItem/clear, anahtar kullanıcıya göre isimlendirilmiş — `userId \|\| 'guest'`, `:485`); `:94` varsayılan `albumView:'list'`; `:495` açılışta okuma; `:731` yazma; `:901` önbellekten uygulama → **liste/grid tercihi kalıcı** | **IMPLEMENTED** |
| URL parametresi | `[?&]view=` / `searchParams.get('view')` kod tabanında **0** (URLSearchParams yalnız OAuth `redirect_uri` okumalarında: `js/auth/*`, `js/router/UrlUtils.js:31`) → paylaşılabilir görünüm URL'si **yok** | **PLANNED** |
| Oturum | `session['view_mode']` PHP'de okunuyor, yazan **yok** (§A) | **PLANNED** |
| DB | `.ai/.sql/mysql/coremusic_user.sql:57-75` `user_preferences` **15 kolon** (theme, device_type, audio_quality…) — `view_mode` **yok** | **PLANNED** |
| Vault iddiası | `shared/src/ViewMode/CLAUDE.md:38` "DB: user_preferences → view_mode" → şemayla örtüşmüyor | **ÇELİŞKİ** |
| Çift kaynak | `assets.coremusic.net/js copy/` aynı üç dosyayı (ViewModeManager, SidebarManager, device-loader) barındırıyor; `Css copy/` de aynı | **ÇELİŞKİ** (drift) |

#### D) Performans — liste boyutu / sanallaştırma

| Kanıt | İçerik | Etiket |
|---|---|---|
| `SidebarManager.js:104-…` | `VirtualScroller` sınıfı **tanımlı ve export edilmiş** (spacer + `overscan=5` + DOM recycling + RAF `scheduleRender`, `:138-144`); `.vs-spacer` CSS'i `_sidebar.css:589` mevcut | **IMPLEMENTED** (tanım) |
| `new VirtualScroller` | Kod tabanında **0** çağrı → sınıf hiçbir yerde instantiate edilmiyor | **PLANNED** (ölü kod) |
| Ölçüm iddiası | Docblock "1000+ items, <16ms frame" (`SidebarManager.js:6`) — ölçüm/test **yok** | **PLANNED** (kanıtsız) |
| `js/components/interactive/InfiniteScroll.js` | `IntersectionObserver` + sentinel (`:4,57,63`), `cm:infinite-scroll:load/loaded/end` olayları (`:89,102,129`); `main.js:121` `cm-infinite-scroll` kaydı | **IMPLEMENTED** |
| `sidebar:viewchange` | Yayın var (`SidebarManager.js:748`), tüketici **0** | **PLANNED** |

#### E) Erişilebilirlik (görünüm geçişi)

| Kanıt | İçerik | Etiket |
|---|---|---|
| Görünüm butonları | `aria-label` ("Görünüm değiştir" / "Grid görünümü") + `title` mevcut (`SidebarManager.js:389,396`) | **IMPLEMENTED** (kısmi) |
| `role="radio"` / `radiogroup` / `aria-checked` | Görünüm geçişinde **0**; vault'ta yalnız cinsiyet/puanlama kalıbı (`ui-design/screens/shared/select-gender.md:133`, `ui-design/prompt/component/C07-gender-button.md:66`) | **PLANNED** |
| `aria-pressed` | Görünümde **0**; vault'ta player butonlarında (`architecture/k11-ux/accessibility-wcag.md:390-427`) | **PLANNED** |
| Klavye | Sidebar'da genel `keydown` dinleyicisi var (`SidebarManager.js:723-725` → `#handleKeyboard`); **görünüme özel** ok-tuşu/roving-tabindex davranışı **yok** | **PLANNED** |
| Durum duyurusu | Görünüm değişimi için `aria-live` / `role="status"` **0** | **PLANNED** |
| A11y dokümanı | `ui-design/04-accessibility-gaps.md` + `architecture/k11-ux/accessibility-wcag.md` mevcut | **IMPLEMENTED** (doküman) |

#### F) Vault kayıtları (bu dosyadan önceki iddialar)

| Kayıt | İçerik | Etiket |
|---|---|---|
| `.ai/index.md:669`, `.ai/keys.md:220,280`, `.ai/brain.md:1004` | ADR-045 "Multi-domain view mode" kaydı — dosya **yoktu** | **ÇELİŞKİ** (bu dosya kapatır) |
| `.ai/.decisions/index.md:87` | `[[../brain.md]] ADR-045-…` biçiminde düz metin + yanlış hedef | **ÇELİŞKİ** (§5.1 adım 9 — ertelendi) |
| `.ai/.decisions/accepted/ADR-004-multi-domain-spa.md:19` | ADR-045'i "henüz yazılmadı — düz metin" olarak işaretler | DOĞRULANDI (bu dosya ile doldu) |
| `.ai/architecture/k10-uygulama/README.md:318` | ADR-046 (cross-view state) düz metin | **PLANNED** — diskte `ADR-046-*` dosyası **YOK** → wiki-link **kurulmaz** |
| `shared/src/ViewMode/CLAUDE.md:54` | `[[../../.ai/decisions/accepted/ADR-045-…]]` — hedefte `.decisions` noktası eksik (**kırık link**, `.ai/.decisions/` doğru) | **ÇELİŞKİ** |

> **Bulgu özeti:** Görünüm modu **çekirdeği kurulu ve testli** (JS 83 satır + PHP 90 satır + shell/device öznitelikleri); **liste/grid geçişi sidebar'da canlı** ve localStorage'a yazılıyor; ama (1) panel modu CSS'i (`09_ViewModes/`) canlı ağaçta **fiziksel olarak yok** (12 kırık `@import`), (2) **URL kalıcılığı sıfır** (paylaşılabilir link yok), (3) oturum/DB yazımı yok, (4) virtual scroller **ölü kod**, (5) geçiş ARIA/klavye sözleşmesi **eksik**, (6) galeri ve tablo modları ile music/admin/social domain'leri **kodda yok**. Bu ADR bu altı boşluğu tek karar altında kapatır.

### §1.2 Sorun Tanımı (Problem)

Kullanıcılar koleksiyonlarını (çalma listesi, albüm, arşiv, panel) **görmek biçimini** seçmek istiyor; sistem bunu parçalı karşılıyor: (1) panel modu (home/pro/studio/car) hem JS hem PHP'te tanımlı ama kullanıcının seçtiği mod **hiçbir yere yazılmıyor** — `setMode()` çağıran yok, `$_SESSION['view_mode']` yazan yok; (2) liste/grid tercihi yalnızca **sidebar** kapsar, sayfa içeriği için ayrı bir sözleşmesi yok ve `cm-list--grid` CSS'i eksik; (3) tercih **URL'de yaşamadığı** için bir görünüm bağlantısı paylaşılamıyor, sekme arası/kopya-link tutarsızlığı doğuyor; (4) `09_ViewModes/` canlı ağaçta olmadığından JS/PHP'in istediği CSS **404** (12 kırık import), yani karar "var" görünüyor ama görsel karşılığı yok; (5) sanallaştırılmış liste hazır ama kullanılmıyor, 1000+ öğede hem DOM yükü hem ekran okuyucu odak kaybı riski hesaplanmamış; (6) geçiş butonları `aria-label` taşıyor ama `role="radio"`/`aria-checked`/ok-tuşu/duyuru yok → WCAG 2.2 AA sözleşmesi kurulmamış. Karar, bu altı boşluğu **tek görünüm modu mimarisi** altında, framework'süz ve URL'yi paylaşılabilir sözleşmenin merkezine alarak kapatmaktır.

### §1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | 8 sorgu: (1) "grid list view toggle UX best practices 2025 persistence user preference" (2) "virtual scrolling accessibility screen reader aria-live problems solution 2025" (3) "store UI state in URL query parameters vs localStorage shareable links best practice" (4) "virtualized list accessibility ARIA aria-setsize aria-rowindex keyboard focus WCAG 2.2" (5) "grid vs list view content density decision default view pattern design system 2025" (6) "layout shift CLS when changing list to grid view lazy images content-visibility" (7) "WAI-ARIA radiogroup pattern toolbar toggle buttons view switcher keyboard arrow keys accessible" (8) "personalized default view per user role dashboard adaptive UI defaults research" |
| Web Search **Konusu** | Liste/grid anahtarlaması UX'i, görünüm tercihi kalıcılığı (URL vs localStorage), sanallaştırılmış listenin erişilebilirliği (aria-setsize/rowindex, live region), içerik yoğunluğuna göre varsayılan görünüm, mod geçişinde CLS, ARIA radiogroup/toolbar klavye kalıbı, rol/domain bazlı varsayılanlar |
| Web Search **Bağlam** | Karar, CoreMusic'in 4 panel modu + sidebar liste/grid mekanizmasını (JS 83 satır + PHP 90 satır, `09_ViewModes` 12 kırık import) URL-öncelikli kalıcılık ve WCAG 2.2 AA sözleşmesiyle birleştirirken ADR-001 (framework yasağı), ADR-004/021 (router), ADR-006 (performans) ve ADR-044 (tema öznitelikleri) ile hizalı kalmalı; araştırma 2026-09-28'de yapıldı, protokol: `.claude/skills/prompt-maker/references/10-web-research-protocol.md` (8 sorgu, 56 adlandırılmış kaynak, resmi spec öncelikli) |
| Web Search **Kısa Açıklama** | Görünüm anahtarlama bir **tekli seçim**tir (radiogroup/radio veya toolbar içinde toggle) — anlık sonuç vermeli, kısa etiketli olmalı, durumu yüksek kontrastla göstermeli; tercih paylaşılabiliyorsa URL'de, yalnız o oturumdaysa localStorage'da tutulur; sanallaştırmada DOM'da olmayan öğeler `aria-setsize`/`aria-rowindex` ile bildirilmezse okuyucu liste sayısını yanlış duyurur |
| Web Search **Uzun Açıklama** | Kaynaklar dört eksende buluşuyor. (i) **Kalıcılık:** URL'de tutulan durum bağlantı paylaşımı, yer imi ve çok-sekme tutarlılığı sağlar; localStorage kalıcı ama paylaşılamaz, cookie oturum ölçeklidir — en dayanıklı desen URL birincil + yerel fallback'tir (TanStack Router tartışması, LogRocket, StackOverflow, React topluluğu, dev.to). (ii) **Görünüm seçimi:** grid göz atmayı, liste yönetmeyi kodlar; tablo karşılaştırmada, kart görsel karar vermede öne çıkar — varsayılan veri işine göre seçilmeli (NN/g, UX Patterns, Smart Interface Patterns, Digital Surgeons). (iii) **Erişilebilirlik:** görünüm grubu `role="radiogroup"` + `role="radio"` + `aria-checked`, toolbar içindeyse `Space`/`Enter` ile seçimi değiştirir ve ok tuşları gezinimi değiştirmez (W3C APG Radio Group Pattern, MDN radiogroup); odak `aria-activedescendant`/roving tabindex ile korunur, değişiklik `aria-live="polite"` ile duyurulur (MDN live regions, Sara Soueidan, Vispero) — yoksa odak başa döner. (iv) **Sanallaştırma:** `aria-setsize` DOM'da olmayan öğelerin toplamını, `aria-rowindex`/`aria-posinset` konumunu bildirir (WAI-ARIA 1.2, MDN); WCAG 2.2 AA'da odak görünürlüğü (2.4.11 Focus Not Obscured) ve görünür odak göstergesi zorunludur. Ayrıca `content-visibility`/boyut değişimi CLS'yi etkiler — mod geçişinde sabit en-boy verilmelidir (web.dev CLS, StackOverflow içerik-görünürlüğü raporu). |
| Web Search **Paragraf Veri Uzun** | Görünüm modu kararı, üç katmanı aynı anda tatmin etmelidir: **bulma** (kullanıcı doğru görünüme nasıl ulaşır), **hatırlama** (tercih sekmeler/cihazlar arasında kaybolur mu) ve **duyurma** (ekran okuyucu değişimi fark eder mi). URL birincil kalıcılık ilk ikisini aynı anda çözer: `?view=grid` bağlantıyı paylaşılan herkes aynı görünüme getirir, URL normalizasyonu ve router sözleşmesi (ADR-004/021) sayesinde görünüm parametresi yönlendirmeyle yarışmaz; localStorage yalnızca URL'de yokken (yer imi, temiz giriş) devreye girer ve domain varsayılanının (müzik=liste, galeri=kart, admin=tablo) üzerine kullanıcı override'ını yazar. Üçüncü katman ise bedeli: `role="radio"` tek başına yeterli değildir, klavye sözleşmesi (grup içinde tek tab durağı + ok tuşları, toolbar içinde Space/Enter), `aria-checked` durumu ve `aria-live="polite"` duyurusu birlikte kurulmazsa buton görünür olarak değişir ama sistem "görünüm değişti" demez. Sanallaştırmada işler daha da kritikleşir: DOM'dan çıkarılan öğeler odaktan düştüğünde `aria-setsize` yoksa toplam sayı kaybolur, odak başa atladığında kullanıcı listeyi yeniden tarar — bu yüzden veri boyutu eşiği bir performans kararı değil, erişilebilirlik kararı olarak yazılır: eşik altında basit DOM (tahmin edilebilir odak), eşik üstünde sanallaştırma + setsize/posinset + live region. CLS tarafında ise mod geçişi, görsel en-boy'ları sabitlenmeden yapılırsa Core Web Vital'ı doğrudan düşürür; `content-visibility` benzeri geciktirmeler de ölçülen kaymayı büyütebilir. |
| Web Search **Sonucu** | 8 sorgu ≈ **56 adlandırılmış kaynak** (~7/sorgu); beş kanonik destek: (i) URL birincil + localStorage fallback kalıcılık (LogRocket, TanStack Router, StackOverflow, dev.to, React topluluğu), (ii) `radiogroup`/`radio` + `aria-checked` + toolbar `Space`/`Enter` kalıbı (W3C APG, MDN, Carbon, Primer, Compound), (iii) `aria-setsize`/`aria-rowindex` + `aria-live="polite"` ile sanallaştırılmış liste duyurusu (WAI-ARIA 1.2, MDN, Sara Soueidan, Vispero), (iv) grid=liste-varsayılanı veri işine göre seçimi (NN/g, UX Patterns, Smart Interface Patterns, Digital Surgeons, UX Planet), (v) mod geçişinde CLS + `content-visibility` uyarısı (web.dev CLS, StackOverflow, Jess B Peck, Aditude). Ek bulgu: rol/domain bazlı varsayılan görünüm ürünlerde yaygın ve talep görüyor (Home Assistant per-user default view, IBM default view, Demandbase role-based views) — "varsayılan domain'e göre, override kullanıcıya göre" kalıbını destekliyor. |
| Web Search **Alınan Karar** | Karar a-e kalemleri bu bulgularla sabitlendi: (a) **mod kümesi** grid/liste/kart/galeri + panel modları (home/pro/studio/car) domain'e göre mevcut, (b) **kalıcılık** URL birincil (`?view=`, router sözleşmesi) + localStorage fallback (mevcut `SidebarCache` kalıbı) + oturum/DB yazımı sırayla tamamlanır, (c) **varsayılanlar** domain'e göre (müzik=liste, galeri=kart, admin=tablo) + kalıcı kullanıcı override'ı, (d) **erişilebilirlik** `role="radiogroup"`/`role="radio"` + `aria-checked` + tek tab durağı + ok tuşu sözleşmesi (toolbar içinde `Space`/`Enter`) + `aria-live="polite"` duyuru + WCAG 2.2 AA odak görünürlüğü, (e) **performans** veri boyutu eşiği: eşik altı basit DOM, eşik üstü `VirtualScroller` + `aria-setsize`/`aria-rowindex` + sabit en-boy (CLS koruması) |
| Web Search **Sonuç** | Araştırma, kullanıcı onaylı kapsamı **destekledi ve netleştirdi**: yeni bir UI kütüphanesi veya sanallaştırma kütüphanesi gerekmez; mevcut `SidebarCache` (localStorage), `SidebarRenderer` (toggle) ve `VirtualScroller` (tanım) üzerine **sözleşme + kalıcılık hattı + ARIA** eklenir. Tek şartla: `09_ViewModes/` fiziksel olarak canlı ağaçta yoksa panel modu kararı **görsel olarak uygulanamaz** — onarım önceliklidir. |

### §1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-001 framework yasağı | Vanilla JS + ITCSS/BEM; görünüm anahtarlayıcı için React/Vue, sanallaştırma kütüphanesi (ör. react-window eşdeğeri), jQuery **yok** — `VirtualScroller` yerel sınıf olarak kalır |
| ADR-004 / ADR-021 / ADR-083 router sözleşmesi | URL birincil kalıcılık, SPA router'ın immutable route sözleşmesine **tabidir**: görünüm parametresi route'u değiştirmez, normalizasyon (ADR-016) ve router kayıtlarını ihlal etmez |
| ⚠️ Düzeltme — "ADR-005 dual router" | Üst görev notunda geçtiği biçimde **ADR-005 dual router değildir**: diskte `ADR-005-ultrathink-protocol` (zero hallucination). "Multi-domain / dual router" karşılıkları **ADR-004** (multi-domain SPA) + **ADR-021** (router immutable contract) + **ADR-083** (SPA router) + **ADR-016** (URL normalization) zinciridir; bu ADR bu dördü referanslar |
| ADR-006 performans tavanları | Görünüm geçişi kritik yola ek JS **eklemez**; mod değişimi CSS sınıf/öznitelik takasıdır, ilk boya beklemez |
| ADR-018 + ADR-044 hizası | `prefers-reduced-motion` (geçiş animasyonları dahil) ve `data-*` öznitelik yazım kalıbı tema motoruyla **çakışmaz** (`data-view-mode` ile `data-mode` ayrı özniteliklerdir) |
| ADR-001-037 frozen dokunulmazlık | Frozen ADR'ler yalnız referanslanır; metinlerine dokunulmaz |
| UTF-8 yazım protokolü | Vault yazımları yalnız `vault-utf8-writer.mjs`; PowerShell write cmdlet'leri yasak |
| Hallucination disiplini | Diskte olmayan dosyaya wiki-link **yazılmaz** (`ADR-046-*`, `architecture/k11-ux/view-mode.md`, `Css/09_ViewModes/` → düz metin + `⚠️ VERIFICATION REQUIRED`) |
| REDACTED | Çerez/anahtar/kişisel veri hiçbir koşulda bu ADR'ye yazılmaz |

---

## §2 Karar (Decision)

CoreMusic, tüm alanlardaki görünüm seçimini **tek Multi-Domain View Mode Architecture** altında birleştirir. Görünüm, bir **tekli seçim**tir (grid/liste/kart/galeri birbirinin yerine geçer), **URL'de yaşar**, domain'e göre varsayılanlanır ve WCAG 2.2 AA sözleşmesiyle duyurulur.

### §2.1 (a) Görünüm Modu Kümesi — grid / liste / kart / galeri (+ panel modları)

- **Mod kümesi:** `grid` · `list` · `card` · `gallery`. Her domain **mevcut** olanları sunar; olmayanı göstermez (gün kodunda: sidebar = `list|grid` IMPLEMENTED, home kart/grid sabit IMPLEMENTED, galeri PLANNED, admin tablo PLANNED).
- **Panel modları ayrı katmandır:** `home|pro|studio|car` (JS `ViewModeManager` + PHP `ViewModeManager`) bu ADR'nin **alt sözleşmesi** olarak kalır; içerik modu (`grid/list/…`) ile **karıştırılmaz** (farklı öznitelik: `data-view-mode` vs `data-view`).
- **Domain → mod matrisi (hedef, eksikler işaretli):**

| Domain | Mevcut (kod) | Hedef varsayılan | Not |
|--------|--------------|------------------|-----|
| home | kart/grid (sabit) | **kart** | kullanıcı geçişi eklenir → PLANNED |
| assets (sidebar/panel) | list + grid | **liste** | `SidebarCache` zaten kalıcı |
| music (album/çalma listesi) | dizin yok → PLANNED | **liste** (yoğun tarama) | NN/g + UX Patterns bulgusu: liste yönetimi, grid göz atımı |
| galeri / media | `gallery` 0 isabet → PLANNED | **kart** | görsel karar verme baskın |
| admin | dizin yok → PLANNED | **tablo** (grid'in karşılaştırma biçimi) | yoğun alan + kolon karşılaştırma |
| auth | 0 iz → **KAPSAM DIŞI** | — | form sayfası, görünüm modu taşımaz |

- Tek nokta: **`data-view`** özniteliği + **`data-cm-view`** sınıfı (BEM modifikatör `cm-list--grid` vb.); her domain aynı sözleşmeyi kullanır.

### §2.2 (b) Kalıcılık — URL birincil, localStorage fallback

1. **Birincil (paylaşılabilir):** görünüm `?view=<mod>` query parametresinde tutulur. Kural: URL'de parametre **varsa** o kazanır (paylaşılan link, yer imi, doğrudan giriş); router sözleşmesine (ADR-004/021/083) aykırı değildir — route kimliği değişmez, yalnızca görünüm **state**'i URL'dedir. URL normalize edilir (`?view=list` domain varsayılanıysa **çıkarılır** → temiz URL, ADR-016 hizası).
2. **İkincil (fallback):** URL'de parametre yoksa `localStorage` okunur — mevcut `SidebarCache` kalıbı (`SidebarManager.js:49-82`, kullanıcıya göre isimlendirilmiş anahtar) **genel görünüm deposuna** genişletilir (`cm.view.<domain>`).
3. **Üçüncü katman (kısmi):** oturum `$_SESSION['view_mode']` (PHP `detect()` bunu zaten **ilk** sırada okuyor — yazıcı eklenir) ve girişli kullanıcıda `user_preferences.view_mode` kolonu (şu an **yok** → §5.1 adım 5).
4. **Çözüm sırası (tek formül):** `URL > oturum/tercih > localStorage > domain varsayılanı`. Yazma sırası tersidir: kullanıcı seçince URL **hemen** güncellenir (history.replaceState — pushState değil, geçmiş kirlenmesin), sonra localStorage, sonra (girişliyse) tercih deposu.
5. **Drift önleme:** üç depo arasındaki çelişkide **URL** kazanır; URL temizlendiğinde son bilinen tercih, o da yoksa domain varsayılanı uygulanır. `data-view` özniteliği her durumda tek yazıcıdan (ViewModeManager) yazılır (ADR-040 tek yazıcı ilkesi hizası).

### §2.3 (c) Domain Bazlı Varsayılan + Kalıcı Kullanıcı Override'ı

- Varsayılanlar §2.1 tablosunda sabittir ve **kodda** tanımlıdır (kullanıcı verisine bağlı değildir).
- Kullanıcı bir mod seçtiyde bu, domain varsayılanının **override'ıdır** ve (b)'deki sırayla kalıcıdır; cihaz/tarayıcı arasında taşınır (localStorage → tercih deposu).
- Girişli kullanıcıda override **hesaba** yazılır (ADR-044 hesap senkronu hattıyla aynı API deseni — ADR-020); girişsiz kullanıcıda yalnız localStorage.
- Panel modu (`home/pro/studio/car`) için mevcut davranış **korunur**: yol öneki > oturum > `home` (mevcut `ViewModeManager::detect` sırası değiştirilmez — davranış değişikliği yaratmaz).

### §2.4 (d) Erişilebilirlik — klavye + ARIA (role=radio/radiogroup)

- Görünüm seçici bir **`role="radiogroup"`**tur; her seçenek `role="radio"` + `aria-checked` taşır (mevcut `aria-label` korunur, `title` tekrarı kaldırılabilir).
- **Klavye sözleşmesi:** grup içinde tek `Tab` durağı (roving tabindex); `←/→` (ve `↑/↓`) odakla birlikte seçimi değiştirir; seçici bir `toolbar` içindeyse ok tuşları **yalnız gezinir**, seçim `Space`/`Enter` ile olur (W3C APG Radio Group Pattern — toolbar içi varyant).
- **Duyuru:** değişim `aria-live="polite"` bölgesinden (veya `role="status"`) "Liste görünümü" biçiminde duyurulur; odak görünüm butonunda **kalır** (sanallaştırmada odak kaybı varsa öge kimliğiyle geri bağlanır).
- **Odak görünürlüğü:** WCAG 2.2 AA 2.4.11/2.4.13 (odak örtülmemeli + görünür odak göstergesi) mod geçişinde de korunur; `prefers-reduced-motion` ile geçiş animasyonu azaltılır (ADR-018/044 hizası).
- `aria-pressed` yalnız **toggle** (aç/kapa) bileşenlerinde kullanılır; görünüm seçimi toggle **değildir** → `aria-checked` zorunludur.

### §2.5 (e) Performans — Virtual List vs Basit DOM (veri boyutu eşiği)

| Koşul | Yaklaşım | Gerekçe |
|-------|----------|---------|
| Öge sayısı **≤ 200** veya ölçülmüş render **≤ 16 ms/kare** | **Basit DOM** (mevcut `SidebarRenderer` yolu) | Tahmin edilebilir odak + arama + ekran okuyucu akışı; gereksiz karmaşa yok (YAGNI) |
| **> 200** öğe **veya** 1000+ satır listeleniyorsa | **`VirtualScroller`** (mevcut yerel sınıf) + `overscan` | `SidebarManager.js:6` iddiası "1000+ / <16ms" — **ölçülmeden yayına alınmaz** (§5.1 adım 7) |
| Sanallaştırma açıkken | `aria-setsize` (toplam) + `aria-rowindex`/`aria-posinset` (konum) + spacer `aria-hidden` | DOM'da olmayan öğeler duyurulmazsa liste sayısı kaybolur |
| Mod geçişi | Sabit en-boy / `aspect-ratio` + içerik değişimi | CLS koruması (web.dev: `content-visibility` ve boyut değişimi kaymayı büyütür) |
| Sonsuz akış | Mevcut `InfiniteScroll` (IntersectionObserver) korunur | Sentinel + olay deseni zaten IMPLEMENTED |

- **Kural:** sanallaştırma **varsayılan değildir**; eşik aşıldığında açılır ve a11y zorunlulukları (setsize/rowindex + live region) **aynı adımda** gelir — performans için erişilebilirlik feda edilemez.

### §2.6 Uygulama Katmanı (ITSS hizası — ADR-001)

- Mod tanımları `01_Abstracts` (token + `data-view` seçicileri), bileşen stilleri `04_Components` (`cm-list--*`, `sidebar__items--*`), panel dosyaları `09_ViewModes` (canlı ağaçta **oluşturulacak** — dosya adı değişikliği değil, kayıp katmanın geri gelmesi) katmanlarında durur.
- `Css copy/` ↔ `Css/` farkı bu ADR kapsamında **fark raporu + hizalama** olarak kapatılır (In-Place Refactoring; dosya adı değiştirme **yok**).

### §2.7 Neden Bu Seçenek?

Kod pahalı kısmi yatırımı çoktan yapmış (JS+PHP yöneticileri, testler, toggle, localStorage deposu, virtual scroller tanımı); eksik olan **sözleşme**: hangi modun nerede geçerli olduğu, tercihin nerede yaşadığı ve ekran okuyucuya nasıl duyurulduğu. Yeni bir UI kütüphanesi gerçeklik kaynağını ikiye böler (drift üretir); URL-öncelikli + domain-varsayılanlı + ARIA sözleşmesi mevcut yatırımı ADR-001/004/006/021 çerçevesinde tamamlar.

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|------------------|
| 1 | **Yalnız localStorage/çerez ile kalıcılık (URL parametresi yok)** | Sıfır router işi, bugünün koduna en yakın (sidebar zaten böyle) | Görünüm paylaşılamaz, yer imi/çok-sekme tutarsız, "bu liste nasıl görünüyor" bağlantısı imkânsız; ADR-046 (düz metin) hedefiyle de zayıf | Kapsamın (b) kalemi URL'yi **birincil** yapar; web araştırması da paylaşılabilir durumun URL'yi işaret ediyor |
| 2 | **Yalnız URL parametresi (localStorage yok)** | Tek doğruluk kaynağı, tam paylaşılabilirlik | Temiz giriş/yer iminde tercih kaybolur; her domain varsayılanına düşülür; mevcut `SidebarCache` çöpe gider | İki dünyanın maliyeti düşük: fallback katmanı mevcut kodun **üzerine** eklenir, ikisi çatışmaz (çözüm sırası §2.2) |
| 3 | **Hazır sanallaştırma/görünüm kütüphanesi** (virtual list + grid galeri paketi) | Olgun, testli, çok mod hazır | ADR-001 framework/bağımlılık yasağı, CSP/farklı lisans yüzeyi, erişilebilirlik sözleşmesi pakete devredilir ve bizim domaın varsayılanlarıyla uyuşmaz | Bağımlılık artışı + lisans + "paket ne derse" riski; mevcut `VirtualScroller` tanımı işi görür (ölçümle) |
| 4 | **Mod seçimini `prefers-reduced-data`/ciyahz/otomatik seçime bırak (kullanıcı override'ı yok)** | En sadesi, sıfır UI | Domain varsayılanı kullanıcıyı dinlemez; kapsam (c) imkânsız; "galeri istiyorum" diyemez | Varsayılan **domain'e** göre, override **kullanıcıya** göre — ikisi birlikte kararın özü |
| 5 | **`09_ViewModes` yerine tüm panel modlarını tek CSS'e gömmek (dosyaları kaldırıp birleştirmek)** | 4 dosya azalır, kırık import kalmaz | Dosya/katman **adı değişikliği** (In-Place Refactoring yasağı), `Css copy/` ile fark büyür, JS/PHP `VIEW_CSS` haritası anlamsızlaşır | Kayıp katmanın **geri getirilmesi** birleştirmeden daha ucuz: 12 import onarımı tek adımda biter |

---

## §4 Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- Paylaşılabilir `?view=` bağlantısı → kopya-link, yer imi ve çok-sekme davranışında **tek gerçeklik**; domain varsayılanı URL temizliğinde devreye girer.
- Var olan üç yatırım (`SidebarCache`, `SidebarRenderer` toggle, `VirtualScroller` tanımı) **sözleşmeye** bağlanır; yeni kütüphane/derleme adımı gerekmez (ADR-001).
- ARIA radio + `aria-live` ile görünüm değişimi **ekran okuyucuya görünür** hale gelir; WCAG 2.2 AA kanıtı (`.ai/ui-design/04-accessibility-gaps.md`) kapanır.
- Veri boyutu eşiği sayesinde 1000+ öğeli listelerde hem kare süresi hem DOM boyutu kontrol altında; sanallaştırma **gerektiğinde** devreye girer.
- `09_ViewModes/` onarımı hem bu ADR'yi hem ADR-044'ün kırık import bulgusunu (§5 adım 1) aynı işte çözer.

### 4.2 Olumsuz Sonuçlar

- URL yazımı (`replaceState`) + fallback sırası üç depoyu eşzamanlı canlı tutar → senkron hatası yüzünden beklenmedik varsayılanlara dönüş riski (mitigasyon §4.3-2).
- `$_SESSION['view_mode']` ve `user_preferences.view_mode` eklenir → oturum ve şema üzerinde ek yazma yüzeyi (ADR-040 tek yazıcı disiplini gerekir).
- `role="radiogroup"` + klavye sözleşmesi mevcut iki butonu **dönüştürür** (`SidebarRenderer`) → regresyon riski olan tek nokta.
- Sanallaştırma + a11y zorunlulukları birlikte yazılmazsa performans kazancı erişilebilirlik kaybıyla takaslanır.
- Panel modu CSS'i (09_ViewModes) canlı ağağa gelene kadar JS/PHP'in yolu **404** kalır — bu ADR tek başına görsel sonucu veremez.

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| 1 **URL kirliliği**: her geçiş `history` ve görünümü kirletir; router ile yarışır | 3 (Olası) | Orta (paylaşım/karmaşa) | `replaceState` (pushState **yasak**), domain varsayılanı URL'den **çıkarılır**, route kimliğine dokunulmaz (ADR-004/016/021), tek parametre adı `view` |
| 2 **State drift**: URL ↔ oturum ↔ localStorage ↔ DB çelişir, kullanıcı "ayarım neden geri geldi" der | 4 (Çok olası) | Yüksek (güven kaybı) | Net çözüm sırası (§2.2.4) + tek yazıcı (`data-view` yalnız ViewModeManager) + yazma sırası (URL → localStorage → tercih) + giriş anında tek yönlü taşıma |
| 3 **Virtual scroll a11y kırılması**: odak/düzleme kaybı, `aria-setsize` yokluğunda yanlış liste duyurusu | 3 (Olası) | Yüksek (WCAG ihlali) | Eşik üstünde sanallaştırma **yalnız** setsize/rowindex + live region ile birlikte; odak geri bağlama testi; AA 2.4.11/2.4.13 kontrolü |
| 4 **CLS**: mod geçişinde görsel en-boy değişimi → Core Web Vital düşüşü | 3 (Olası) | Orta/İzleme skoru) | `aspect-ratio`/sabit en-boy + geçişte `content-visibility` kullanmama + CLS ölçümü (performance budget §5.1 adım 7) |
| 5 **Panel modu CSS'i 404** (09_ViewModes canlıda yok) → mod değişimi görsel sonuç vermez | 4 (Çok olası) | Yüksek (karar uygulanamaz) | §5.1 adım 1: katmanı `Css copy/` kaynağından dosya adlarını **değiştirmeden** geri getir + 12 import onarımı + kapı: import hedefi bulunamayan = 0 |
| 6 **Varsayılan domain matrisi kodda değil** (music/admin/galeri dizinleri yok) → matris kağıtta kalır | 4 (Çok olası) | Orta (kapsam genişlemesi) | Domain başına fazlı teslim; her faz yalnız kendi domain'ini açar; eksik domain'de mod seçicisi **gizlenir** (yanlış vaat yok) |

### 4.4 Fallback (geri birleşim / geri dönüş)

1. **URL yazımı geri alınırsa** sistem otomatik olarak bugünün davranışına düşer: `localStorage` → domain varsayılanı (sidebar `SidebarCache` zaten çalışıyor); veri kaybı yok.
2. **Otuz/DB yazımı** eklenmezse (adım 4-5 geri alınırsa) `detect()` mevcut okuma sırasını **değişmeden** sürdürür (`home` fallback'i kodda).
3. **ARIA dönüşümü** geri alınabilir: `radiogroup` kaldırılıp butonlar eski hâline döner (`aria-label` mevcut), kırık bir şey kalmaz.
4. **Sanallaştırma** eşiğin altına çekilerek pasifleştirilir: `VirtualScroller` hiç instantiate edilmediğinden kapatmak tek satırlık bayraktır; basit DOM yolu zaten IMPLEMENTED.
5. **`09_ViewModes/`** katmanı eklendiği için kaldırılması 12 `@import` satırının eski hâline dönmesi demektir (git diff ile geri alınır) — hiçbir adım veri/klasör **silmez**; frozen ADR'ler etkilenmez.

---

## §5 Uygulama (Implementation)

### §5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | **`09_ViewModes/` katmanını geri getir** (dosya adı **değiştirilmeden**, kaynak `Css copy/09_ViewModes/` — v-home/v-pro/v-studio/v-car) + `main.css:58-60` ve `08_Devices/d-4k|d-desktop|d-laptop` içindeki **12 kırık `@import`** hedefini onar; kapı: `09_ViewModes` hedefi bulunamayan import = **0** | UI Designer | 0.5 gün |
| 2 | **Sözleşme + URL kalıcılığı:** `?view=` okuma/yazma (`replaceState`), çözüm sırası `URL > oturum > localStorage > varsayılan`, domain varsayılan matrisi (§2.1) tek modülde; URL'de domain varsayılanı **temizlenir** | UI Designer + Backend | 1.5 gün |
| 3 | **Tercih hattı:** `SidebarCache` deseninden genel görünüm deposuna genişleme (`cm.view.<domain>`) + `$_SESSION['view_mode']` yazıcısı (PHP `detect()` sırasına dokunmadan) | Backend Architect | 1 gün |
| 4 | **Etkileşim köprüsü:** `ViewModeManager.setMode()` için tetikleyici + `viewmodechange`/`sidebar:viewchange` dinleyicisi (bugün **0** tüketici) → panel modu ile içerik modu aynı olay hattından geçer | UI Designer | 1 gün |
| 5 | **DB kolonu:** `user_preferences.view_mode` eklendi (`coremusic_user.sql` türevi migration, ADR-040 tek yazıcı) + `shared/src/ViewMode/CLAUDE.md:38` iddiasının gerçekleşmesi; girişli kullanıcıda hesap senkronu (ADR-020/044 deseni) | Data Engineer + Backend | 1 gün |
| 6 | **ARIA + klavye:** `role="radiogroup"`/`role="radio"` + `aria-checked` + roving tabindex + toolbar `Space`/`Enter` varyantı + `aria-live="polite"` duyuru; `aria-pressed` yalnız gerçek toggle'larda | UI Designer + QA | 1 gün |
| 7 | **Eşik + ölçüm:** `SidebarManager.js:6` iddiası ölçülür (1000+ öğe, kare süresi); ≤200 basit DOM, >200 `VirtualScroller` + `aria-setsize`/`aria-rowindex`; CLS bütçesi (geçiş öncesi/sonrası) + `cm-list--grid` CSS'inin eklenmesi | QA + UI | 1.5 gün |
| 8 | **Domain fazları:** home (kart geçişi) → music/admin/galeri domain'leri **dizinleri geldikçe** açılır; eksik domain'de seçici gizli kalır | UI Designer | faz bazlı |
| 9 | **Dizin düzeltme (ERTELENDİ — onay gerekiyor):** `.ai/.decisions/index.md:87` `[[../brain.md]] ADR-045-…` satırının `[[accepted/ADR-045-multi-domain-view-mode-architecture]]` biçimine düzeltilmesi **bir sonraki vault reset'ine ertelenmiştir** — bu işlemde index.md'ye dokunulmadı (kural: onaysız satır değişikliği yok) | Vault Steward | 0.1 gün |
| 10 | **Debate (3 tur / persona)** tamamlanır → §7'deki Debate/Tech Lead `⏳` satırları güncellenir | Vault Steward | 0.5 gün |

### §5.2 Geri Dönüş Planı

1. **Adım 1 tersi:** `Css/09_ViewModes/` altındaki eklenen 4 dosya `git checkout` ile kaldırılır ve `main.css`/device `@import` satırları eski hâline döner (yalnız bu işin dosyaları; frozen ADR'lere dokunulmadı).
2. **Adım 2/3 tersi:** `?view=` okuma/yazma tek bayrakla kapatılır; `replaceState` çağrısı yok sayılır → bugünkü davranış (`localStorage` > domain varsayılanı) otomatik devreye girer, veri silinmez.
3. **Adım 4 tersi:** `setMode()` tetikleyicisi ve iki olay aboneliği kaldırılır; panel modu `detect()` yoluyla eskisi gibi çalışır.
4. **Adım 5 tersi:** `view_mode` kolonu migration'ı `DROP COLUMN` ile geri alınır; PHP `detect()` kolon yoksa `home`'a düşer (kod zaten buna dayanıklı, `:35-57`).
5. **Adım 6 tersi:** `radiogroup`/`radio` öznitelikleri kaldırılır, butonlar `aria-label`'li hâline döner.
6. **Adım 7 tersi:** sanallaştırma bayrağı kapanır (hiç açılmadıysa zaten kapalı), basit DOM yolu kalır.
7. Tüm geri dönüşler `.ai/log.md`'ye **append** ile kaydedilir; dosya adları değişmez (In-Place Refactoring), frozen ADR'ler etkilenmez.

### §5.3 Debate Şartları (KABUL koşulları — 3 şart, bağlayıcı)

> Debate sonucu **18 kabul / 2 çekimser / 0 red → KABUL** (§7.1). Aşağıdaki 3 şart §5.1 adımlarıyla birlikte kapanmadan karar **uygulanmış sayılmaz**; her şartın kapanışı `.ai/log.md`'ye append ile kaydedilir.

1. **Şart 1 — Entegrasyon + DB/CSS ekleme (1a-1b).** (1a) **Entegrasyon fazı:** `ViewModeManager.setMode()` çalıştırılır (bugün ölü: çağrı **0**, `viewmodechange` dinleyicisi **0**) ve URL `?view=` parametresine bağlanır (bugün **0** hit → PLANNED). (1b) **DB/CSS ekleme:** `user_preferences.view_mode` kolonu eklenir (bugün **YOK** — 15 kolon) + `Css/09_ViewModes/` canlı ağaçta oluşturulur (bugün **YOK** — `main.css:58-60` + 4 breakpoint ×3 = 12 kırık `@import`, yalnız `Css copy/`) — ADR-044 onarımıyla **aynı işte**. Sorumlu: UI Designer + Backend + Data (§5.1 adım 1, 2, 4, 5).
2. **Şart 2 — Sanallaştırma eşiği.** `VirtualScroller` (`SidebarManager.js:104`, tanımlı / kullanım **0**) için **eşik maddesi** yazılır ve uygulanır: kaç öğeden sonra sanallaştırmaya geçileceği ölçümle sabitlenir (§2.5 tablosu: ≤200 basit DOM, >200 sanallaştırma); `SidebarManager.js:6` "1000+ öğe / <16ms" iddiası ölçülmeden yayına alınmaz. Sorumlu: QA + UI (§5.1 adım 7).
3. **Şart 3 — A11y + kalıcılık testi.** (i) klavye/ARIA testi: `role="radiogroup"`/`role="radio"` + `aria-checked` + ok tuşu sözleşmesi + `aria-live` duyuru; (ii) URL kalıcılık testi: `?view=` yazma/okuma ve paylaşılan linkin aynı görünüme getirmesi. Test yok → şart kapanmaz. Sorumlu: QA (§5.1 adım 6, 7).

---

## §6 İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[../../CLAUDE.md]] | Ana sözleşme, 16 Hard Guardrail |
| [[../../AGENTS.md]] | Agent registry, onay/escalation §10, frozen kuralı §25.3 |
| [[../../WORKFLOW.md]] | Süreçler, fazlar |
| [[../../brain.md]] | Mimari karar özeti (`:1004` ADR-045 satırı) |
| [[../../index.md]] | Master katalog (`:669` ADR-045 satırı) |
| [[../../keys.md]] | Keyword haritası (`:220,280`) |
| [[../../glossary.md]] | Terimler (görünüm modu, sanallaştırma, radiogroup) |
| [[../../MEMORY.md]] | Session hafızası (`:197,219` ViewModeManager satırları) |
| [[../../log.md]] | Audit trail (append-only) |
| [[../index.md]] | Karar dizini — `:87` slug satırı (**düzeltme §5.1 adım 9'da ertelendi**) |
| [[CLAUDE]] | `accepted/` dizin kuralı |
| [[../../.templates/adr/adr-template]] | Bu ADR'nin zorunlu şablonu (Guardrail #16) |
| [[../../.templates/adr/adr-frontend-template]] | Frontend ADR şablonu |
| [[../../.templates/index]] | Şablon envanteri (SRP) |
| [[../../architecture/index]] | K matrisi ana indeksi (K10/K11) |
| [[../../architecture/k11-ux/README]] | **K11.8 view-mode sahipliği** (`:184,312,456`) — `view-mode.md` dosyası diskte **YOK** → düz metin + `⚠️ VERIFICATION REQUIRED` |
| [[../../architecture/k10-uygulama/README]] | `:149,318` ADR-045/046 düz metin referansları (ADR-046 dosyası **yok**) |
| [[../../architecture/k11-ux/accessibility-wcag]] | WCAG kalıpları (`aria-pressed` player örnekleri) — §2.4 hizası |
| [[../../ui-design/04-accessibility-gaps]] | Erişilebilirlik boşlukları — §2.4 hedefi |
| [[../../ui-design/05-responsive-architecture]] | `§09_ViewModes` (v-home/v-pro/v-studio/v-car) — panel modu spec'i |
| [[../../ui-design/01-mockup-index]] | Mockup indeksi — görünüm referansı (Guardrail #11) |
| [[ADR-001-vanilla-js-itcss]] | ITCSS 9 katman + framework yasağı — §2.6 |
| [[ADR-004-multi-domain-spa]] | Subdomain SPA iskeleti — "multi-domain"ın kaynağı; `:19,32` bu ADR'yi düz metin bekliyordu |
| [[ADR-005-ultrathink-protocol]] | Zero hallucination — §1.1 dürüst etiket (**dual router değildir**) |
| [[ADR-006-performance-targets]] | Kritik yol tavanları — §2.5 |
| [[ADR-018-footer-player-vaporwave]] | Görsel dil + `prefers-reduced-motion` — §2.4 |
| [[ADR-021-spa-router-immutable-contract]] | Router immutable contract — §2.2 URL birincil |
| [[ADR-031-mobile-strategy-pwa-flutter]] | `:166` ADR-045/046 görünüm konsepti referansı |
| [[ADR-044-dynamic-user-theme-engine]] | Format/künye referansı + `data-*` öznitelik hizası |
| [[../../../assets.coremusic.net/CLAUDE.md]] | `:128,181` "ViewModeManager.js ← ADR-045" |
| [[../../../assets.coremusic.net/AGENTS.md]] | CSS/JS envanteri — `09_ViewModes` **envanter-kod çelişkisi** (§1.1-B) |
| [[../../../home.coremusic.net/CLAUDE.md]] | `:171` ADR-045 yol referansı (yol `.ai/decisions/…` — `.decisions` noktası eksik, kırık) |
| [[../../../shared/CLAUDE.md]] | `:51` ViewMode/ViewModeManager.php (ADR-045) |
| [[../../../shared/AGENTS.md]] | `:58` ViewModeManager.php → ADR-045 |
| [[../../../shared/src/ViewMode/CLAUDE]] | Domain bağlamı — `:21,31` ADR-045, `:38` DB iddiası (**ÇELİŞKİ**), `:54` kırık wiki-link |
| §5.3 Debate Şartları | Debate **3 bağlayıcı şartı** — 1a/1b entegrasyon + DB/CSS ekleme · 2 sanallaştırma eşiği · 3 a11y + URL kalıcılık testi (§7.1 debate kaydı) |

> **Wiki-link doğrulaması:** Yukarıdaki **35** vault hedefinin **tamamı** diskte mevcut olarak doğrulanmıştır (2026-09-28, `Test-Path` + glob). Diskte **olmayan** referanslara (`ADR-046-cross-view-state-preservation`, `architecture/k11-ux/view-mode.md`, `Css/09_ViewModes/`, `social.coremusic.net`, `music/admin/media/api` dizinleri) wiki-link **yazılmamış**, düz metin + `⚠️ VERIFICATION REQUIRED` kullanılmıştır.

---

## §7 Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Vault Steward | 2026-09-28 | ✅ |
| Tech Lead | Tech Lead | 2026-09-28 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### §7.1 Debate Kaydı

**✅ TAMAMLANDI — 3 tur / 20 persona · 18 kabul / 2 çekimser / 0 red → KABUL** (2026-09-28).

**Tur 1 — Bulgu (20 persona):** `ViewModeManager.js` 83 satır (`setMode` hiç çağrılmıyor, `viewmodechange` **0** dinleyici) · `ViewModeManager.php` 90 satır (session okur / **0** yazar) · `SidebarManager.js:388-403` gerçek liste/grid + `SidebarCache` `albumView` default `list` **IMPLEMENTED** · URL `view=` **0** hit (**PLANNED**) · localStorage fallback **IMPLEMENTED** (yalnız `albumView`) · `VirtualScroller:104` tanımlı **0** kullanım, `InfiniteScroll.js` **IMPLEMENTED** · gallery **0** · `user_preferences.view_mode` kolonu **YOK** (15 kolon) · `Css/09_ViewModes` canlı ağaçta **YOK** (`main.css:58-60` + 4 breakpoint 12 kırık import, yalnız `Css copy/`) · ~56 kaynak / 8 sorgu · `index.md:87` düzeltmesi ertelendi (§5.1 adım 9). Oy: **15 kabul/neutral + 4 uyarı** — Frontend (`setMode` ölü + URL PLANNED şart), QA (kolon şart), Critic (CSS dizini + derinlik notu şart).

**Tur 2 — İtiraz → çözüm:**

| # | İtiraz | Çözüm | Şart |
|---|--------|-------|------|
| 1 | `setMode` ölü + `view=` 0 hit | entegrasyon fazı (manager çalıştır + URL parametresi bağla) | **1a** |
| 2 | `view_mode` kolonu yok + `Css/09_ViewModes` ağaçta yok | DB kolonu + CSS dizini ekleme (ADR-044 onarımı ile birlikte) | **1b** |
| 3 | `VirtualScroller` 0 kullanım | eşik kararı (kaç öğeden sonra sanallaştırma) maddesi | **2** |
| 4 | Test yok | klavye/ARIA + URL kalıcılık testi | **3** |

**Tur 3 — Oy:** **18 kabul / 2 çekimser / 0 red → KABUL**.

- **Kapsam:** (a) grid/liste/kart/galeri mod kümesi · (b) URL birincil + localStorage fallback · (c) domain varsayılanı + kalıcı override · (d) klavye + ARIA radio · (e) virtual list veri boyutu eşiği — **kullanıcı onaylı**.
- **Kanıt:** 4 panel modu (JS 83 satır + PHP 90 satır + testler) · sidebar list/grid + `SidebarCache` localStorage · `view=` **0** · `$_SESSION['view_mode']` yazıcı **0** · `user_preferences.view_mode` **0** · `09_ViewModes` canlıda **YOK** (6 satır/12 import kırık) · `new VirtualScroller` **0** · galeri **0**.
- **Araştırma:** 8 sorgu / **56** adlandırılmış kaynak (protokol `10-web-research-protocol.md`).
- **3 şart:** §5.3'e eklendi ve bağlayıcıdır — (1) entegrasyon + DB/CSS ekleme (1a-1b) · (2) sanallaştırma eşiği · (3) a11y + URL kalıcılık testi.
- **Tech Lead:** ✅ · **Arch Lead:** ⏳ · Debate kaydı §7.1'de tamamlandı (§5.1 adım 10 kapandı).

---

*ADR-045 v1.0.0 | 2026-09-28 | Created*
*Authority: CoreMusic Vault — Multi-Domain View Mode Architecture*
*Mode: Red Team · Human Mode · Truth Mode*

---

> ⚠️ **SİLİNEN CSS REFERANS NOTU (2026-09-30, 0-kanıt temizlik):** Bu ADR'de geçen `main.css` (5 referans — §1.1-B, §5.1, §6, §7) **2026-09-30'da silinmiştir** — kanıt: `Get-ChildItem -Recurse -Filter main.css` → **0 isabet**. ADR metni karar anındaki disk ölçümünü taşıdığı için **düzeltilmedi**; `main.css:58-60` gibi satır numaralı kanıtlar tarihsel kayıttır. `07_Vendors/bootstrap*.css` diskte MEVCUT (33 dosya).
