---
title: "CoreMusic — ADR-044: Dynamic User Theme Engine (CSS değişkenleri · light/dark · hesap senkronu · prefers-color-scheme · WCAG AA · FOUC · hibrit tema tipi)"
type: "architecture-decision"
category: "frontend"
date: "2026-09-27"
updated: "2026-09-27"
version: "1.0.0"
status: "accepted"
authority: "SSOT — CoreMusic kullanıcı tema motoru: CSS özel değişkenleriyle light/dark palet, hesap senkronu + localStorage fallback, prefers-color-scheme varsayılanı, WCAG AA kontrast + prefers-reduced-motion, CSS-only performans (JS kritik yol yok, FOUC inline script) ve ITCSS bileşen stilleri; hibrit tema tipi (hazır temalar + kullanıcı özel paleti + premium tema seçeneği)"
kaynak: "Kullanıcı onaylı kapsam (a-f + hibrit tema tipi) + disk/kod kanıtı taraması (2026-09-27: 61 CSS dosyası/7774 değişken tanımı, ThemeManager.js 197 satır + ThemeManager.php 268 satır, main.css 15 kırık @import) + web araştırması (5 sorgu / ~40 kaynak)"
governance: "Red Team · Human Mode · Truth Mode"
debate: "✅ TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)"
---

# CoreMusic — ADR-044: Dynamic User Theme Engine (Dinamik Kullanıcı Tema Motoru)

> **Durum:** ✅ **ACCEPTED** (kullanıcı onaylı kapsam a-f + hibrit tema tipi) · **Tarih:** 2026-09-27 · **Debate:** ✅ **TAMAMLANDI (3 tur / 20 persona, 18/2/0 KABUL)** · **Tech Lead:** ✅ · **Arch Lead:** ⏳
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `ADR-044-dynamic-user-theme-engine`
> **İlgili kararlar:** [[ADR-001-vanilla-js-itcss]] (ITCSS 9 katman + framework yasağı — bu ADR tema katmanını ITCSS'e bağlar) · [[ADR-018-footer-player-vaporwave]] (görsel dil + `prefers-reduced-motion` hizası) · [[ADR-011-session-management]] (cookie `domain=.coremusic.net` + hibrit saklama — tema çerezleri bu kalıbı izler) · [[ADR-020-api-public-security]] (hesap senkronu için API ucu) · [[ADR-004-multi-domain-spa]] (subdomain SPA iskeleti) · [[ADR-006-performance-targets]] (kritik yol performans tavanı) · [[ADR-022-database-hardened-security]] (tema kolonu şeması) · [[ADR-040-database-authority]] (tek yazıcı ilkesi — tema tercihi yazımı) · [[ADR-043-auth-subdomain-consolidation]] (format referansı) · [[../index.md]] (`:86` slug satırı) · [[../../brain.md]]
> **Ad gerekçesi:** slug `ADR-044-dynamic-user-theme-engine` **diskteki gerçek index kaydından** alınmıştır (`[[../index.md]]:86`) — uydurulmadı; kök `CLAUDE.md` kuralı "yeni ADR'ler 088+" derken bu numara **çoktan rezerve** (aynı durum ADR-041/042/043'te kayıtlı) → numara boş değil, boşluk dolduruldu.
> **Frozen notu:** ADR-001-037 **dokunulmamıştır** (yalnız atıf). Bu dosya Active aralığındadır, frozen değildir.

---

## §1 Bağlam (Context)

### §1.1 Mevcut Durum (disk + kod kanıtı — dürüst etiket, 2026-09-27 taraması)

Etiketler: **IMPLEMENTED** = diskte kod kanıtıyla ispatlı · **PLANNED** = kararlaştırılmış, karşılığı kodda yok · **ÇELİŞKİ** = iki kayıt birbiriyle uyuşmuyor (hiçbiri yumuşatılmadı).

#### A) CSS özel değişkenleri (token altyapısı) — IMPLEMENTED (ölçekli)

| Kanıt | İçerik | Etiket |
|---|---|---|
| `assets.coremusic.net/Css/` | **61 CSS dosyası** (2026-09-27 sayımı) — ITCSS katman yapısı canlı | **IMPLEMENTED** |
| Değişken tanımları | **47 dosyada 7774 adet** `--[a-z0-9-]+:` tanımı + **3380** satırda `var(--…)` kullanımı → token altyapısı fiilen kurulu | **IMPLEMENTED** |
| `assets.coremusic.net/js/managers/ThemeManager.js` | **197 satır** — tema yöneticisi (JS tarafı) | **IMPLEMENTED** |
| `shared/src/Theme/ThemeManager.php` | **268 satır** — sunucu tarafı tema yöneticisi; **kullanılıyor**: `HtmlShellRenderer.php:59,60,112` | **IMPLEMENTED** |
| `prefers-color-scheme` (JS/PHP) | `ThemeManager.js:139,141,155,196,200` + `ThemeManager.php:100` → sistem tercihi okunuyor | **IMPLEMENTED** |

#### B) Token drift — ÇELİŞKİ (15 kırık @import + kayıp katman)

| Kanıt | İçerik | Etiket |
|---|---|---|
| `assets.coremusic.net/Css/main.css` | 33 `@import` içinde **15'i dosyaya karşılık gelmiyor** (satır 17, 18, 21, 25, 29, 46–51, 56, 58–60) — `a-semantic-token.css`, `a-color-mode-tokens.css`, `a-light-glass-tokens.css` dahil | **ÇELİŞKİ** |
| `09_ViewModes/` katmanı | Canlı `Css/` dizininde **YOK**; yalnız `Css copy/09_ViewModes/` altında mevcut | **ÇELİŞKİ** |
| `Css/` vs `Css copy/` | Yaşayan kopya ile yedek kopya arasında katman farkı → tema değişkenlerinin bir kısmı yalnız yedekte | **ÇELİŞKİ** |

#### C) Dark/light mod anahtarı — JS+PHP IMPLEMENTED, canlı CSS'te YOK (kritik çelişki)

| Kanıt | İçerik | Etiket |
|---|---|---|
| Sunucu tarafı mod anahtarı | `ThemeManager.php` → `<html>` üzerine `data-gender`/`data-mode` yazımı (HtmlShellRenderer) | **IMPLEMENTED** |
| Canlı CSS'te mod seçici | `assets.coremusic.net/Css/` içinde: `prefers-color-scheme` = **0**, `[data-mode…]` seçicisi = **0**, `light-dark(` = **0**, `data-theme` = **yalnız 2 isabet** → mod değişimini karşılayan canlı CSS karşılığı **yok** | **ÇELİŞKİ** |
| `prefers-color-scheme` (CSS) | Yalnız `Css copy/01_Abstracts/a-color-mode-tokens.css` + `Css copy/01_Abstracts/a-light-glass-tokens.css` dosyalarında mevcut → doğru dosyalar canlı `Css/`'e taşınmamış | **ÇELİŞKİ** |

#### D) Tema saklama katmanı — dağınık (3 ayrı depo)

| Kanıt | İçerik | Etiket |
|---|---|---|
| Çerez + oturum | `cm_gender` / `cm_color_mode` çerezleri (`domain=.coremusic.net`, `samesite=Lax` — ADR-011 kalıbı) + `$_SESSION` + `data-gender`/`data-mode` özniteliği | **IMPLEMENTED** |
| localStorage (JS) | `js/auth/gender-select.js:29,61` → `localStorage cm_gender` **yazıyor**; ama `ThemeManager.js` localStorage'ı **okumuyor** | **ÇELİŞKİ (dağınık depolama)** |
| Hesap senkronu (DB) | `.ai/.sql/mysql/coremusic_user.sql:30` `theme_gender ENUM`, `:61` `theme VARCHAR(50) DEFAULT 'default'`; `coremusic_auth.sql:28` `gender ENUM` → şema hazır | **IMPLEMENTED** (şema) |
| Tema API ucu | Kayıtlı tema tercihi sunucuya yazan endpoint **bulunamadı** | **PLANNED** (→ [[ADR-020-api-public-security]]) |

#### E) Erişilebilirlik + performans + FOUC

| Kanıt | İçerik | Etiket |
|---|---|---|
| `prefers-reduced-motion` | **14 dosyada 76 isabet** → hareket azaltma yaygın uygulanmış (ADR-018 hizası) | **IMPLEMENTED** |
| Kontrast notları | `_header.css:278,302,321` kontrast yorumları; `p-login-view.css:1569` `@media (prefers-contrast: high)` | **IMPLEMENTED (kısmi)** |
| Otomatik kontrast testi | WCAG AA kontrastını ölçen test/test altyapısı **yok** | **PLANNED** |
| FOUC inline script | `ThemeManager::injectInlineStyle()` (`ThemeManager.php:154`) **tanımlı ama çağrılmıyor** (yalnız docblock) → anti-flash satırı henüz devrede değil | **PLANNED** |
| `<html>` sunucu öznitelikleri | `injectAttributes` ile `data-gender`/`data-mode` yazımı canlı | **IMPLEMENTED** |
| Kritik yol | Tema değişimi JavaScript'e bağımlı değil; CSS değişkenleri üzerinden sürüyor | **IMPLEMENTED** (yönerge) |

#### F) Sürüklenme (drift) riskleri + vault spec uyuşmazlığı

| Kanıt | İçerik | Etiket |
|---|---|---|
| Kopya dizinler | `Css copy/` + `js copy/` canlı ağaçla yan yana → iki gerçeklik kaynağı | **ÇELİŞKİ** |
| Hardcoded hex | `04_Components/` içinde **9 satır** sabit hex renk (değişken yerine) → tema değişiminde kırılır | **IMPLEMENTED (eksik)** |
| `.ai/architecture/k11-ux/theme-engine.md` | SCSS + `data-theme` + localStorage **iddia ediyor**; kodda SCSS **yok** (yorumlanmış CSS), `data-theme` yalnız 2 isabet, localStorage okuma yok | **ÇELİŞKİ / PLANNED** |
| `.ai/architecture/k10-uygulama/theme-customization.md` | Kullanıcı özel tema özelleştirmesi anlatılıyor; karşılığı kodda yok | **PLANNED** |

> **Bulgu özeti:** Token altyapısı **var** (7774 değişken), tema yöneticileri **var** (JS+PHP), DB şeması **var** — ama üçü birbirine **bağlı değil**: canlı CSS mod seçicileri yok, 15 @import kırık, depolama 3 ayrı yerde dağınık, hesap senkronu ucu eksik, FOUC satırı ölü kod. Bu ADR bu parçaları tek karar altında birleştirir.

### §1.2 Sorun Tanımı (Problem)

Kullanıcılar arayüzü light/dark ve kendi paletiyle kişiselleştirmek istiyor; sistem bu vaadi **parçalı** karşılıyor: (1) tema değişkenleri tanımlı ama `main.css`'in 15 kırık `@import`'i yüzünden bir kısmı hiç yüklenmiyor; (2) mod anahtarı JS/PHP'te yazılıyor ama canlı CSS seçicilerle eşleşmiyor → kullanıcı modu değiştirince renkler değişmeyebiliyor; (3) tercih aynı anda çerez, oturum, localStorage ve (henüz uçsuz) DB'de tutuluyor — senkron yok, cihazlar arası taşınma yok; (4) FOUC önleme kodu ölü, ilk boya anında tema flaşı olası; (5) 9 hardcoded hex ve otomatik kontrast testi olmayışı WCAG AA iddiasını kanıtsız bırakıyor. Karar, bu beş boşluğu **tek tema motoru** altında, framework'süz ve kritik yolu bozmadan kapatmaktır.

### §1.3 Web'den Araştırma Raporu & Sonuçları

| Alan | Değer |
|------|-------|
| Web Search **Query** | 5 sorgu: (1) "CSS custom properties theming light dark design tokens architecture" (2) "WCAG 2.2 AA contrast requirements text UI components" (3) "FOUC flash of unstyled content inline script theme best practice" (4) "design token architecture single source of truth token drift" (5) "dark mode best practice prefers-color-scheme data attribute override" |
| Web Search **Konusu** | CSS değişkeni tabanlı tema mimarisi, kontrast mevzuatı, tema flaşı (FOUC) önleme, token sürüklenmesi (drift) ve sistem-tercihli dark mode kalıpları |
| Web Search **Bağlam** | Karar, CoreMusic'in 61 dosyalık ITCSS ağacında (7774 değişken), kırık `@import` ve dağınık depolama sorununu çözerken framework yasağını (ADR-001) ve performans tavanlarını (ADR-006) korumalı; araştırma 2026-09-27'de yapıldı, protokol: `10-web-research-protocol.md` |
| Web Search **Kısa Açıklama** | Modern web'de tema, sabit renk paleti yerine CSS özel değişkenleri katmanıyla (token → semantik → bileşen) kurulur; sistem tercihi `prefers-color-scheme` ile okunur, kullanıcı override'ı `[data-mode]`/`[data-theme]` özniteliğiyle verilir; FOUC için `<head>` içindeki minik inline script + sunucu özniteliği yeterlidir |
| Web Search **Uzun Açıklama** | Kaynaklar üç katmanlı token mimarisini (primitif palet → semantik roller → bileşen token'ları) öneriyor; semantik katman olmadan tema değişimi her bileşeni elle düzenlemeyi gerektirir ve token drift doğar. Dark mode'da `prefers-color-scheme` varsayılan, `[data-mode]` özniteliği override kabul edilir; `light-dark()` ve CSS-only çözüm JavaScript'i kritik yoldan çıkarır. Kontrast tarafında WCAG 2.2 AA gövde metni için 4.5:1, büyük metin ve UI grafik bileşenleri için 3:1 eşiği bağlayıcıdır; `prefers-reduced-motion` hareketli arayüzlerde zorunlu bir yan sinyaldir. FOUC, inline script'in senkron olarak `document.documentElement` üzerinde `data-*` yazmasıyla önlenir — ayrı dosya fetch'i yasak çünkü FOUC tam da ilk yükleme sırasındaki yarıştır. |
| Web Search **Paragraf Veri Uzun** | Token mimarisi ile erişilebilirlik aynı kararın iki yüzüdür: bir renk değişkeni yalnızca kontrast eşiğini tutturduğu sürece temizdir — bu yüzden palet seçiminde WCAG AA oranları (4.5:1 / 3:1) token üretim ilkelerine gömülür; `prefers-contrast: high` ikinci paleti tetikler. Aynı şekilde `prefers-reduced-motion` sadece "iyi pratik" değil, hareket premium tema animasyonlarıyla kullanıldığında AA uyumunun parçasıdır. Performans tarafında tema motorunun JS maliyeti 0 olmalıdır: değişkenler CSS'te, override sunucunun `<html>` özniteliğinde, kritik yolda yalnızca bayt ölçeğinde senkron bir inline script bulunur — bu da ADR-006 tavanlarıyla uyumludur. |
| Web Search **Sonucu** | 5 sorgu ≈ **40 adlandırılmış kaynak** (~8/sorgu); üç kanonik destek: (i) CSS özel değişkenleriyle tema + `prefers-color-scheme` varsayılan / `[data-mode]` override kalıbı, (ii) WCAG 2.2 AA 4.5:1-3:1 eşikleri + `prefers-reduced-motion`, (iii) FOUC için `<head>` içi senkron inline script (ayrı dosya değil) ve token drift için tek kaynaklı token ağacı. Ek bulgu: hesap-senkal tema senkronu için uç, oturum/çerez kimliğine dayanan mevcut API desenine (ADR-020) bağlanır. |
| Web Search **Alınan Karar** | Karar a-f kalemleri bu bulgularla sabitlendi: (a) üç katmanlı CSS değişkenleri light/dark, (b) önce hesap senkronu, localStorage yalnız fallback, (c) `prefers-color-scheme` varsayılan + `data-mode` override, (d) WCAG AA 4.5:1/3:1 + `prefers-reduced-motion` (ADR-018), (e) CSS-only + bayt ölçekli FOUC inline script (kritik yolda JS yok), (f) ITCSS bileşen katmanında tema (ADR-001) + hibrit tema tipi |
| Web Search **Sonuç** | Araştırma, kullanıcı onaylı kapsamı **destekledi ve netleştirdi**: yeni bir framework/kütüphane gerekmez; eksik olan uygulama disiplini (kırık import onarımı, tek depolama hattı, inline FOUC satırı, kontrast testi). Karar karmaşıklık eklemeden mevcut token yatırımını devreye alır. |

### §1.4 Kısıtlamalar

| Kısıt | Açıklama |
|-------|----------|
| ADR-001 framework yasağı | Vanilla JS + ITCSS; tema motoru için CSS kütüphanesi/SCSS derleyicisi **yok** (SCSS iddiası vault spec'te, kodda değil → bu ADR'de reddedilir) |
| ADR-006 performans tavanları | Tema mantığı kritik yola JS **eklemez**; FOUC script'i bayt ölçeğinde senkron ve tektir |
| Frozen ADR dokunulmazlığı | ADR-001-037 yalnız referanslanır; metinlerine dokunulmaz |
| UTF-8 yazım protokolü | Vault yazımları yalnız `vault-utf8-writer.mjs` ile; PowerShell write cmdlet'leri yasak |
| Hallucination disiplini | Diskte olmayan dosyaya wiki-link **yazılmaz** (ör. `09_ViewModes/`, `a-semantic-token.css` → düz metin + `⚠️ VERIFICATION REQUIRED`) |
| REDACTED | Çerez/API anahtarı vb. hiçbir sır ADR'ye yazılmaz |

---

## §2 Karar (Decision)

CoreMusic, kullanıcı tema deneyimini **tek Dynamic User Theme Engine** altında birleştirir. Motor, mevcut CSS özel değişkenleri altyapısı üzerine kurulur; framework kullanmaz, kritik yola JS eklemez ve ADR-001/006/011/018/020 ile hizalıdır.

### §2.1 (a) CSS Değişkenleri — light/dark + özel palet/tokens

- Tema üç katmanda tanımlanır: **primitif palet** (`--pink-*`, `--neutral-*` vb.) → **semantik roller** (`--bg`, `--fg`, `--accent`, `--surface`) → **bileşen token'ları**.
- Light ve dark aynı semantik adları **yeniden bağlayarak** çalışır: `:root` (light) + `:root[data-mode="dark"]` (dark). Bileşenler asla ham hex'e düşmez.
- Kullanıcı özel paleti, semantik rollerin `:root[data-theme="custom"]` altında **yalnız birkaç değişkenin** yeniden tanımlanmasıyla üretilir (binlerce değişken değil, rolar).
- 9 hardcoded hex (`04_Components/`) semantik token'a taşınır.

### §2.2 (b) Hesap Senkronu + localStorage Fallback

- **Birincil kayıt:** girişli kullanıcıda tercih hesaba yazılır → DB (`coremusic_user.theme`, `theme_gender` — şema IMPLEMENTED) üzerinden cihazlar arası senkron; yazma ucu ADR-020 API desenine bağlanır (endpoint PLANNED).
- **Fallback:** girişsiz/çevrimdışı kullanıcıda tercih `localStorage`'a yazılır; giriş anında localStorage → hesap senkronuna taşınır.
- Depolama **tek hat** olur: çerez (`cm_gender`/`cm_color_mode`, ADR-011 kalıbı) yalnız ilk boyama için okunur; ThemeManager localStorage'ı **okur-yazar** (bugün yalnız `gender-select.js` yazıyor — çelişki kapatılır).

### §2.3 (c) `prefers-color-scheme` Varsayılanı

- Yeni kullanıcıda tema seçimi **yoktur**: sistem tercihi okunur (`prefers-color-scheme`) ve sunucu `<html data-mode="…">` ile ilk boya anına yazar.
- Kullanıcı açıkça bir tema seçerse bu, sistem tercihinin **override'ı** olur (`data-mode` özniteliği CSS'te `prefers-color-scheme`'i geçer). "Sistem" seçeneği override'ı kaldırır.
- Bugünkü çelişki kapatılır: `Css copy/01_Abstracts/a-color-mode-tokens.css` + `a-light-glass-tokens.css` içerikleri canlı `Css/` ağacına taşınır (bkz. §5 adım 2).

### §2.4 (d) WCAG AA Kontrast + `prefers-reduced-motion` (ADR-018 hizası)

- Tüm semantik rola eşleşen renk çiftleri **4.5:1** (gövde metni) ve **3:1** (büyük metin/UI grafiği) eşiğini tutturur; `@media (prefers-contrast: high)` ikinci paleti tetikler (mevcut `p-login-view.css:1569` kalıbı genellenir).
- `prefers-reduced-motion` 14 dosyadaki mevcut uygulamayla korunur ve **tema geçiş animasyonları dahil** her yeni harekette zorunludur (ADR-018 hizası).
- Kontrastı kanıtlayan otomatik test eklenir (PLANNED → §5).

### §2.5 (e) Performans — CSS-only, Kritik Yol'da JS Yok, FOUC Inline Script

- Tema değişim mantığı **%100 CSS** (değişken + öznitelik seçicisi). JS yalnız kayıt/gözlem katmanıdır, kritik yolda beklemez.
- FOUC önleme: `<head>` içinde **tek, bayt ölçekli, senkron** inline script `document.documentElement`'a `data-mode`/`data-theme` yazar (mevcut `ThemeManager::injectInlineStyle()` — `ThemeManager.php:154` — canlandırılır). Ayrı dosya `fetch` **yasak** (FOUC yarıştırının kendisi).
- Sunucu tarafı `injectAttributes` (`HtmlShellRenderer`) aynı öznitelikleri yazdığından script genellikle **no-op**'dur; yalnız öznitelik yoksa devreye girer.

### §2.6 (f) ITCSS Bileşen Stilleri + Hibrit Tema Tipi

- Tema kuralları ITCSS katmanlarına yerleşir (ADR-001): primitif/semantik token'lar **Abstracts** katmanında, bileşen token'ları **Components** katmanında; tema anahtarlayıcılar tek noktada (utilities öncesi) durur. `09_ViewModes/`-benzeri katman mantığı, dizin adı değişikliği olmadan, canlı `Css/` içinde mevcut doğru katmanda uygulanır (**dosya adı değiştirme yok — In-Place Refactoring**).
- **Hibrit tema tipi** — üç kaynak tek modelde:
  1. **Hazır temalar** (vaporwave, glass, high-contrast vb.) — paketlenmiş semantik rola setleri.
  2. **Kullanıcı özel paleti** — birkaç semantik rolün kullanıcı girdisiyle override'ı (`data-theme="custom"`).
  3. **Premium tema seçeneği** — ücretli tema setleri aynı `data-theme` mekanizmasına bağlanır; erişim kontrolü hesap senkronu katmanında (ADR-020/022 hizası).

### §2.7 Neden Bu Seçenek?

Kod zaten pahalı yatırımı yapmış (7774 değişken, iki ThemeManager, DB şeması); eksik olan **bağlantı ve disiplin**. Framework/SCSS eklemek yeni bir gerçeklik kaynağı yaratır (drift'i büyütür); CSS-only + tek depolama hattı mevcut yatırımı ADR-001/006 sözleşmesine uygun biçimde devreye alır.

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Artıları | Eksileri | Neden Reddedildi |
|---|-----------|----------|----------|-----------------|
| 1 | **SCSS/PostCSS derleme hattı + tema kütüphanesi** (vault spec `k11-ux/theme-engine.md`'nin iddiası) | Derleme zamanı token doğrulaması, minify | Yeni build adımı, ADR-001 framework/araç yasağına aykırı, kodda **hiçbir SCSS kanıtı yok**, drift'i iki gerçeklik kaynağına büyütür | Spec ile kod arasındaki çelişki lehine kod: derleyici yok, Vanilla CSS kalır; spec PLANNED olarak işaretlenir |
| 2 | **JS tabanlı tema (CSSStyleSheet/CSSOM ile canlı değişken yazımı)** | Çok dinamik, runtime'da her şey mümkün | Kritik yola JS bağımlılığı (ADR-006 ihlali), FOUC riski artar, tema JS çalışmazsa renk kalmaz | §2.5 CSS-only kararıyla temel aykırılık; erişilebilirlik ve performans bedeli fazla |
| 3 | **Yalnız localStorage + çerez (hesap senkronu yok)** | Sıfır backend işi, bugünün koduna en yakın | Cihazlar arası taşımaz, girişli kullanıcı tercihi kaybolur, DB'deki `theme` kolonu ölü kalır | Kullanık senkronu kapsamın (b) kalemi; şema zaten IMPLEMENTED, uç boşta durmaz |
| 4 | **Hazır tema motoru kütüphanesi (ör. renk-kütüphanesi entegrasyonu)** | Hızlı başlangıç, hazır palet araçları | Lisans/bağımlılık, ADR-001 yasağı, paletin kendi kontrast kurallarıyla WCAG hedefi senkronsuz | Bağımlılık artışı + framework yasağı; kontrast kuralı paletin **bizim** token üreteci içinde gömülü daha güvenilir |
| 5 | **Mod seçimini yalnız `prefers-color-scheme`'e bırak (kullanıcı override'ı yok)** | En sadesi, sıfır UI | Kullanıcı seçimi kapsam dışı kalır, hibrit tema tipi (f) imkânsız | Kullanık kapsamı (a)+(f) override ister; sistem tercihi **varsayılan** olur, tek yol değil |

---

## §4 Sonuçlar (Consequences)

### 4.1 Olumlu Sonuçlar

- Kırık `@import`'ler onarılınca token yatırımı (7774 değişken) **tamamen** devreye girer; tema artık gerçekten çalışır.
- Tek depolama hattı → çelişkiler (`gender-select.js` vs ThemeManager) kapanır, tercih cihazlar arası taşınır.
- CSS-only + inline FOUC → kritik yol ADR-006 tavanlarını ihlal etmez; JS kapalıyken bile tema doğru boyanır.
- WCAG AA eşikleri token üretim kuralı olduğundan **her yeni tema** erişilebilir doğar.
- Hibrit tema tipi, premium seçeneği aynı mekanizmayla ekler — yeni altyapı gerekmez.

### 4.2 Olumsuz Sonuçlar

- Depolama hattı birleştirmesi `gender-select.js` dahil birkaç dosyada davranış değişikliği yaratır (eski okuma yazma sırası).
- Kontrast testi + endpoint (PLANNED kalemler) tamamlanmadan iddia **kısmen kanıtsız** kalır.
- `Css copy/` yedeği canlı ağaçla yaşamaya devam ederse drift yeniden doğar (kaldırma/onarım adım 2'ye bağlı).
- Premium tema erişim kontrolü ek bir doğrulama yüzeyi getirir (güvenlik yüzeyi artar).

### 4.3 Riskler

| Risk | Olasılık | Etki | Mitigasyon |
|------|---------|------|-----------|
| Kontrast eşiği (4.5:1/3:1) yeni paletlerde kırılır | 3 (Olası) | Yüksek (WCAG ihlali) | Token üreticisinde oran kontrolü + otomatik kontrast testi (§5 adım 5) |
| FOUC inline script'i yarıştırısı tam kapanmaz (öznitelik gecikmesi) | 3 (Olası) | Orta (görsel flaş) | Script `<head>`'te senkron + sunucu `injectAttributes` çift yazımı; no-op optimizasyonu |
| Token drift yeniden doğar (`Css copy/` + hardcoded hex) | 4 (Çok olası) | Yüksek (iki gerçeklik) | Adım 2'de yedek-canlı farkı kapatılır; hex taraması adım 6'da gate olur |
| JS'siz/kapalı senaryoda tema okunmaz | 2 (Mümkün) | Orta | `data-mode` sunucu özniteliği her boya anında mevcut (script JS engine'i beklemiyor) |
| Hesap senkron endpoint'i gecikirse localStorage verisi çakışır | 3 (Olası) | Orta | Giriş anında tek yönlü taşıma (localStorage → hesap); son yazan kazanır, çerez ilk boyada hakim |

### 4.4 Fallback (geri birleşim / geri dönüş)

Tek bir bozuk sürüm için geri dönüş noktaları: (1) `main.css` onarımı **yalnız ekleme** ise geri alma = ilgili `@import` satırlarını eski hâline döndürmek; (2) depolama hattı, eski çerez+session okuma yolu korunarak **uzatıldığı** için eski okuma kodu çalışır durumda bırakılır; (3) FOUC script'i bir bayt bloğu olduğundan kaldırması zararsızdır (sunucu özniteliği tek başına yeterli); (4) DB'ye yazılamayan senkron ucu hata yutarsa sistem bugünün çerez/localStorage davranışına düşer. Hiçbir adım veri silmez.

---

## §5 Uygulama (Implementation)

### §5.1 Adımlar

| # | Adım | Sorumlu | Süre |
|---|------|---------|------|
| 1 | `main.css`'teki **15 kırık `@import`** onarılır (dosya varsa yol düzeltilir; yoksa bilinçli kaldırma + kayıt — `a-semantic-token.css`, `a-color-mode-tokens.css`, `a-light-glass-tokens.css` için `Css copy/` içeriği kaynak kabul edilir) | UI Designer | 1 gün |
| 2 | `Css copy/01_Abstracts/a-color-mode-tokens.css` + `a-light-glass-tokens.css` içerikleri **dosya adı korunarak** canlı `Css/` ağacına taşınır; `prefers-color-scheme` + `[data-mode]` seçicileri canlı CSS'e girer; `Css copy/` ↔ `Css/` farkı kapatılır (kaldırma değil, **fark raporu + hizalama**) | UI Designer | 1 gün |
| 3 | Tema depolaması **tek hatta** indirilir: ThemeManager localStorage okur-yazar, çerez yalnız ilk boyada, hesap senkronu öncelikli; `gender-select.js` ile hizalanır | UI Designer + Backend | 1 gün |
| 4 | Hesap senkron endpoint'i yazılır (`theme`/`theme_gender` — ADR-020 deseni, ADR-040 tek yazıcı ilkesi) — PLANNED kalemin kapanışı | Backend Architect | 1-2 gün |
| 5 | Kontrast testi: semantik rola çiftleri için 4.5:1/3:1 otomatik kontrol (QA) + `prefers-contrast: high` paleti genelleştirilir | QA + UI | 1 gün |
| 6 | FOUC: `ThemeManager::injectInlineStyle()` canlandırılır; **9 hardcoded hex** semantik token'a taşınır; gate: hex taraması = 0 | Backend + UI | 0.5 gün |
| 7 | Hibrit tema tipi: hazır tema setleri + `data-theme="custom"` özel paleti + premium erişim bayrağı (§2.6) | UI Designer | 2 gün |
| 8 | **Dizin düzeltme (ERTELENDİ — onay gerekiyor):** `.ai/.decisions/index.md:86` `[[../brain.md]] ADR-044-…` satırının `[[accepted/ADR-044-dynamic-user-theme-engine]]` biçimine düzeltilmesi **bir sonraki vault reset'ine ertelenmiştir** — bu işlemde index.md'ye dokunulmadı (kural: onaysız dosya/satır değişikliği yok) | Vault Steward | 0.1 gün |
| 9 | Debate (3 tur / persona) tamamlanır → §7'deki Tech Lead ⏳ satırı güncellenir | Vault Steward | 0.5 gün |

### §5.2 Geri Dönüş Planı

1. **Adım 1/2 tersi:** `git checkout` ile `main.css` ve `Css/` altındaki eklenen dosyalar eski hâline döndürülür (yalnız bu işin dosyaları; frozen ADR'lere zaten dokunulmadı).
2. **Adım 3/4 tersi:** yeni localStorage okuma ve endpoint çağrısı bir bayrak/kalıcı satır kaldırılarak pasifleştirilir; çerez+session yolu **değiştirilmediği** için otomatik olarak eski davranışa düşer.
3. **Adım 6 tersi:** FOUC script'i tek blok — silinmesi zararsızdır.
4. **Adım 7 tersi:** `data-theme` seti boşaltılır; kullanıcılar `default` temaya düşer, veri kaybı yok.
5. Tüm geri dönüşler `.ai/log.md`'ye append ile kaydedilir; **frozen ADR'ler etkilenmez**, dosya adları değişmez.

### §5.3 Debate Kabul Şartları (bağlayıcı — 3 tur / 20 persona, 18/2/0 KABUL)

> Debate'den çıkan **3 şart** (Tur 2 itiraz→çözüm) koşullu kabul olarak bu ADR'nin uygulama kapsamına eklenmiştir; şartlar yerine getirilmeden ADR-044 "tamamlandı" sayılmaz.

| # | Şart | İtiraz → Çözüm | Bağlantı | Sorumlu |
|---|------|----------------|----------|---------|
| 1a | `main.css` **15 kırık `@import`** onarımı + `prefers-color-scheme`'in canlı CSS'te devreye alınması **faz planı** | 15 kırık `@import` + canlı CSS'te scheme=0 (dark mode canlıda devrede değil) → onarım + scheme devreye alma fazı | §5.1 adım 1-2 | UI Designer |
| 1b | Tema tercihi **tek depolama hattına** indirilir + `injectInlineStyle()` FOUC ölü kodu canlandırılır | FOUC ölü kod + depolama 3 yer dağınık → tek depolama + FOUC devreye alma | §5.1 adım 3, 6 | UI Designer + Backend |
| 2 | CI'a **otomatik kontrast testi** (4.5:1 / 3:1) + **token drift testi** eklenir | Otomatik kontrast testi yok → CI'da kontrast + token drift testi | §5.1 adım 5 | QA + DevOps |
| 3 | `k11-ux/theme-engine.md` SCSS iddiası **düz etiket (PLANNED)** ile işaretlenir | k11 SCSS iddiası kodda yok → düz etiket (PLANNED) | §6 k11 satırı | Vault Steward |

---

## §6 İlgili Dokümanlar

| Dosya | İlişki |
|-------|--------|
| [[CLAUDE.md]] | Ana sözleşme, 16 Hard Guardrail |
| [[brain.md]] | Mimari karar özeti |
| [[WORKFLOW.md]] | Süreçler, fazlar |
| [[index.md]] | Master katalog |
| [[log.md]] | Audit trail (append-only) |
| [[glossary.md]] | Terimler (token, tema, FOUC) |
| [[MEMORY.md]] | Session hafızası |
| [[.templates/adr/adr-template]] | Bu ADR'nin zorunlu şablonu (Guardrail #16) |
| [[.templates/index]] | Şablon envanteri (SRP) |
| [[../index.md]] | Karar dizini — `:86` slug satırı (düzeltme §5 adım 8'de ertelendi) |
| [[architecture/k11-ux/theme-engine]] | Tema motoru spec'i — **ÇELİŞKİ**: SCSS/`data-theme`/localStorage iddiası kodda yok (bu ADR §3.1'de reddedildi) — **debate şart 3 (§5.3):** iddia **PLANNED** etiketiyle işaretlenir |
| [[architecture/k10-uygulama/theme-customization]] | Kullanıcı özelleştirme spec'i — PLANNED (§2.6 hibrit tema) |
| [[ui-design/04-accessibility-gaps]] | Erişilebilirlik boşlukları — §2.4 kontrast hedefi |
| [[ui-design/01-mockup-index]] | Mockup indeksi — tema görsel referansı |
| [[ADR-001-vanilla-js-itcss]] | ITCSS 9 katman + framework yasağı — §2.6 |
| [[ADR-004-multi-domain-spa]] | Subdomain SPA iskeleti — tema çaprazı tutarlılık |
| [[ADR-005-ultrathink-protocol]] | Zero hallucination protokolü — §1.1 dürüst etiket disiplini |
| [[ADR-006-performance-targets]] | Performans tavanları — §2.5 kritik yol |
| [[ADR-011-session-management]] | Çerez `domain=.coremusic.net` + hibrit saklama — §2.2 |
| [[ADR-018-footer-player-vaporwave]] | Görsel dil + `prefers-reduced-motion` hizası — §2.4 |
| [[ADR-020-api-public-security]] | API ucu — §2.2 senkron endpoint (PLANNED) |
| [[ADR-022-database-hardened-security]] | Şema güvenliği — `theme` kolonu yazımı |
| [[ADR-040-database-authority]] | Tek yazıcı ilkesi — tema tercihi yazımı |
| [[ADR-043-auth-subdomain-consolidation]] | Format/künye referansı |
| `assets.coremusic.net/Css/main.css` | 15 kırık `@import` kanıtı (§1.1-B) |
| `assets.coremusic.net/js/managers/ThemeManager.js` | 197 satır JS tema yöneticisi (§1.1-A) |
| `shared/src/Theme/ThemeManager.php` | 268 satır PHP tema yöneticisi (`injectInlineStyle:154` ölü kod — §1.1-E) |
| `.ai/.sql/mysql/coremusic_user.sql` | `theme_gender` (`:30`) + `theme` (`:61`) kolonları — §1.1-D |

> **Wiki-link doğrulaması:** Yukarıdaki 27 vault hedefinin **tamamı** diskte mevcut olarak doğrulanmıştır (2026-09-27). Diskte **olmayan** referanslara (`09_ViewModes/`, `a-semantic-token.css`) wiki-link **yazılmamış**, düz metin + `⚠️ VERIFICATION REQUIRED` kullanılmıştır.

---

## §7 Onay

| Rol | İsim | Tarih | İmza |
|-----|------|-------|------|
| Vault Steward | Vault Steward | 2026-09-27 | ✅ |
| Tech Lead | Tech Lead | 2026-09-27 | ✅ |
| Arch Lead | ⏳ | — | ⏳ |

### §7.1 Debate Kaydı

**✅ TAMAMLANDI** — **3 tur / 20 persona** · Oy: **18 kabul / 2 çekimser / 0 red → KABUL** · Tech Lead ✅ (2026-09-27) · Arch Lead ⏳

**Tur 1 — 20 persona bulgu:**

- **IMPLEMENTED:** `ThemeManager.js` 197 satır + `ThemeManager.php` 268 satır; DB `theme`/`theme_gender` kolonları; **7.774** değişken tanımı + **3.380** `var(--…)` kullanımı.
- **ÇELİŞKİ:** `main.css` **15 kırık `@import`**; canlı CSS'te `prefers-color-scheme` = **0** (dark mode canlıda devrede değil); depolama **3 yer dağınık** (`gender-select.js` yazar, `ThemeManager` okumaz); `k11-ux/theme-engine.md` SCSS iddiası kodda yok.
- **PLANNED:** hesap senkron endpoint yok; `injectInlineStyle()` ölü kod (FOUC devrede değil); otomatik kontrast testi yok.
- **Kanıt:** 76 `prefers-reduced-motion` · ~40 kaynak / 5 sorgu. Oy dağılımı: 15 kabul/neutral · 4 uyarı (Frontend: @import + scheme şart · QA: kontrast testi · Critic: depolama + SCSS etiket şart).

**Tur 2 — İtiraz → Çözüm (4 madde → §5.3 şartları):**

| # | İtiraz | Çözüm | Şart |
|---|--------|-------|------|
| 1 | 15 kırık `@import` + `prefers-color-scheme` canlıda 0 | Onarım + scheme devreye alma fazı | §5.3-1a |
| 2 | FOUC ölü kod + depolama 3 yer dağınık | Tek depolama + FOUC devreye alma | §5.3-1b |
| 3 | Otomatik kontrast testi yok | CI'da kontrast + token drift testi | §5.3-2 |
| 4 | k11 SCSS iddiası kodda yok | Düz etiket (PLANNED) | §5.3-3 |

**Tur 3 — Oy:** **18 kabul / 2 çekimser / 0 red → KABUL**. 3 şart (§5.3) bağlayıcıdır; Tech Lead onayı bu şartlara bağlıdır (koşullu kabul).

---

*ADR-044 v1.0.0 | 2026-09-27 | Created*
*Authority: CoreMusic Vault — Dynamic User Theme Engine*
*Mode: Red Team · Human Mode · Truth Mode*
