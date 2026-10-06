---
title: "CoreMusic — Css/ Klasör Bağlamı (ENVANTER)"
type: docs
category: frontend
date: 2026-10-03
updated: 2026-10-03
version: 1.0.0
status: active
authority: "SSOT (klasör kökü) — çelişkide disk kazanır"
docType: context
---

# CoreMusic — Css/ Klasör Bağlamı

**docType:** context · **Katman:** L3 sunum (ITCSS) · **Sorumlu:** UI Designer (içerik), MO (envanter)

**Zorunlu Bağlantılar:** [[../../.ai/.templates/index]] · [[../../.ai/.templates/frontend/css-template]] · [[../../.ai/CLAUDE.md]] · [[../../AGENTS.md]] · [[CLAUDE]] · [[AGENTS]] · [[WORKFLOW]]

---

## 1. Amaç

Bu doküman `assets.coremusic.net/Css/` klasörünün **ne işe yaradığını** ve diskteki **gerçek** envanterini tanımlar: 11 ITCSS katmanı + kök giriş, token cihaz ayrımı, import zinciri haritası. Tüm sayılar 2026-10-03 disk ölçümüdür; vault/şablon ile çelişen her satır §3.6'da işaretlenmiştir (çelişkide **disk kazanır**).

| Karar | ADR | Karşılığı |
|-------|-----|-----------|
| Vanilla JS + ITCSS, framework/önişlemci yasak | ADR-001 | §4 #1-#4 |
| Multi-domain görünüm modu | ADR-045 | §3.2 09_ViewModes |
| Erişilebilirlik WCAG 2.2 AA | — | §4 #7 |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `assets.coremusic.net/Css/**` — 11 katman + kök `auth-bundled.css` | JavaScript → `assets.coremusic.net/js/` ([[js-template]]) |
| Design token cihaz ayrımı (base + 6 cihaz token dosyası) | Mockup görselleri → `.ai/ui-design/` + `.ai/.png/` (okunur, üretilmez) |
| Import zinciri haritası (15 cihaz dosyası + auth-bundled) | `07_Vendors/` içeriği (salt okunur) |
| Template-disk çelişki kaydı | PHP sayfa şablonları (`pages/**/*.php`) |

- **Kullananlar:** UI Designer (birincil), QA Engineer (WCAG/responsive), Backend Architect (inline style denetimi), MO (envanter).
- **Ön koşul:** Mockup Before Frontend — `.ai/ui-design/` görseli okunmadan CSS yazılamaz; okunamıyorsa DUR.
- **Not:** Kök `notes.md` görev başında okunur, ilgili katmana uygulanır, satır `✓` imzalanır.

---

## 3. Mimari

### 3.1 11 Katman + Kök — Disk Kanıtı (2026-10-06 ölçümü — önceki: 2026-10-03)

| # | Katman | Sorumluluk | Önek | Dosya sayısı (disk) | Dosyalar |
|---|--------|-----------|------|---------------------|----------|
| 01 | `01_Abstracts/` | **Yalnız token** — kural/seçici YAZILMAZ | `a-` | **19** (2026-10-06 ölçümü) | a-breakpoint-tokens · a-color-mode-tokens · a-colors-token · a-design-tokens · a-fonts-token · **a-layout-tokens-1024 (BASE — medyasız `:root`, 8/8 cihaz importu, Expert C1)** · a-layout-tokens-1920 · a-layout-tokens-3540 · a-layout-tokens-3840 · a-layout-tokens-mobile · a-layout-tokens-tablet · a-light-glass-tokens · a-login-tokens · a-primitive-tokens · a-scale-hybrid · a-semantic-token · a-theme-config · a-welcome-banner-tokens · a-widget-grid-tokens — `a-layout-tokens.css` diskte **YOK** (yalnız `--/Css copy 2|3` yedeklerinde) |
| 02 | `02_Base/` | Bare HTML reset + yapısal iskelet | `b-`/`l-`/`page-` | **3** | b-base-core · l-main-structural · page-layout |
| 03 | `03_Layout/` | Sayfa düzeni: header, footer, sidebar, widget grid | `_` | **4** | _footer · _header · _sidebar · _widget-grid |
| 04 | `04_Components/` | BEM bileşenleri | `c-`/`_` | **15** (2026-10-06 ölçümü) | c-badge · c-buttons · c-card · c-footer-seek · c-footer-volume · c-forms · c-home-song-btn · c-modal · c-progress · c-scrollbar-accent · c-toast · c-toggle · _home-components · _player-info · _welcome-banner |
| 05 | `05_Pages/` | PHP sayfalarına özel stiller | `p-`/`_` | **12** (2026-10-06 ölçümü) | p-album-detail · p-albums · p-artists · p-login-view · p-playlist · p-select-gender · p-settings · _home · _home-inline · _home-layout · _player · _welcome |
| 06 | `06_Utilities/` | Utility sınıfları (sıfır mantık) | `u-` | **1** | u-helpers-utility |
| 07 | `07_Vendors/` | Bootstrap 5.3.8 ailesi — **DÜZENLENMEZ** | `v-` | **33** (17 .css + 16 .map) | bootstrap · bootstrap-grid · bootstrap-reboot · bootstrap-utilities (+ rtl/min/map varyantları) · v-bootstrap-lib |
| 08 | `08_Devices/` | Cihaz import zinciri + davranış override | `d-`/`d-auth-` | **15** | normal 8: d-4k-monitor · d-4k-tv · d-4k · d-desktop · d-embedded · d-laptop · d-phone · d-tablet — auth 7: d-auth-4k-monitor · d-auth-4k-tv · d-auth-desktop · d-auth-embedded · d-auth-laptop · d-auth-phone · d-auth-tablet |
| 09 | `09_ViewModes/` | Görünüm modu (home, pro, studio, car) | `v-` | **4** | v-home · v-pro · v-studio · v-car — hepsi **iskelet** (içerik bekliyor) |
| 10 | `10_Helpers/` | Yardımcı desen/makro | `h-` | **1** | h-ellipsis — **iskelet** |
| 11 | `11_OAuth/` | OAuth/login akış stilleri | `o-` | **1** | oauth |
| kök | `Css/` | Auth subdomain tek giriş | — | **1** | auth-bundled.css |

**YOK (uydurulmaz):** `main.css` (2026-10-03 diskte YOK — cihaz girişi `08_Devices/d-*.css`, auth girişi `auth-bundled.css` üzerindendir).

### 3.1.1 Katman Amaç Tablosu — 5 Sütun (4 zorunlu soru, disk kanıtı)

| Katman/Dosya | Ne için kullanılır | Neyden oluşur | Neden var | Ne zaman düzenlenir |
|--------------|--------------------|---------------|-----------|---------------------|
| `01_Abstracts/` | Tasarım değerinin **tek kaynağı** — tüm katmanlar buradan token okur | 20 dosya: 7 layout (base + 6 cihaz) + 13 konu token (breakpoint, color-mode, colors, design, fonts, light-glass, login, primitive, scale-hybrid, semantic, theme-config, welcome-banner, widget-grid); yalnız `--token: değer` | Guardrail #1: hardcoded yasak → tema/ölçü değişimi tek noktadan; ADR-044/045 override'ı | Yeni token, mockup ölçüsü değişimi, `notes.md` notu gelince |
| `02_Base/` | Bare reset + yapısal iskelet — her sayfanın ortak zemini | 3 dosya: b-base-core, l-main-structural, page-layout | Reset tekrarını önlemek; vendor reboot ile sırayı korumak (07 → 02) | Reset/global tipografi davranışı değişince |
| `03_Layout/` | Sayfa düzeninin **tek evi** (grid/header/footer/sidebar) | 4 dosya: _header, _footer, _sidebar, _widget-grid | Guardrail #5: yerleşim cihaz katmanına yazılmasın; düzen tek yerden yönetilsin | Düzen/mockup ölçüsü değişince |
| `04_Components/` | Tekrar eden görsel parçaların BEM bileşenleri | 6 dosya: c-footer-seek, c-footer-volume, c-home-song-btn, _player-info, _welcome-banner, _home-components | Figma component karşılığı; çok-sayfa ortaklığı; Guardrail #8 (component ≠ sayfa) | Bileşen ekleme/değişikliği; taşınma (`_home-components` 2026-10-03) |
| `05_Pages/` | Tek PHP sayfasına özel stiller | 4 dosya: p-login-view, p-select-gender, _home, _welcome | Sayfa-özelinin ortak bileşeni kirletmesini engeller (Guardrail #8) | Sayfa tasarımı değişince / yeni sayfa açılınca |
| `06_Utilities/` | Sıfır mantık utility sınıf | 1 dosya: u-helpers-utility | css-template §3.8 ayrımı: token tüketir, üretmez | Utility eklenince |
| `07_Vendors/` | 3. taraf Bootstrap — **salt okunur** | 33 dosya (17 .css + 16 .map): bootstrap-grid/reboot/utilities/bootstrap (+rtl/min/map) + v-bootstrap-lib; 5.3.8 | Grid/reboot ihtiyacı + upstream takibi — gerçek framework kullanımı DEĞİL | **DOKUNULMAZ** — yalnız onaylı sürüm güncellemesi |
| `08_Devices/` | Cihaz **girişi** (import zinciri) + davranış override | 15 dosya: normal 8 (tam import + davranış), auth 7 (saf override — dosyada import YOK) | ADR-045 çok-çihaz/bağlam; her cihaz tek `<link>` | Cihaz/kırılım değişince → `devices.config.js` + `DeviceCssMap.php` **EŞZAMANLI** |
| `09_ViewModes/` | Görünüm modu override | 4 iskelet: v-home, v-pro, v-studio, v-car (0 kural) | ADR-045 domain/mod ayrımı | Mockup gelince iskelet doldurulur |
| `10_Helpers/` | Tekrarlayan yardımcı desen/makro | 1 iskelet: h-ellipsis | §3.8: 06'dan farkı kendi içinde kural barındırır | Desen ikinci kez yazılınca (kanıt: tekrar) |
| `11_OAuth/` | OAuth/login akış stilleri (css-template §3.1) | 1 dosya: oauth.css (içerik OKUNMADI — ⚠️ VERIFICATION REQUIRED) | Auth akışını izole katmanda tutmak | Auth akışı değişince |
| kök `auth-bundled.css` | Auth subdomain **TEK giriş** | 7 `@import` (01×4 → 02×1 → 05×2) + dosya içi auth kural yorumları; **07 İÇERMEZ** | Auth tek bundle ile yüklensin; 08 ayrı `<link>` → çift yüklenme yok | Auth sayfası/token/import eklenince; `d-auth-*` import edilmez |

**Katman Amaç Blokları (eli10 / eli15 — 12 madde):**

**01_Abstracts/**
> **eli10 (basit):** Renk, ölçü, boşluk gibi değerlerin tek sözlüğü — her klasör aynı değeri buradan okur.
> **eli15 (detay):** Ayrı dosyalar çünkü değer tek yerde olunca tema ya da ölçü değişince tek yerden değişir. İçine sadece "isim: değer" satırları yazılır, kural yazılmaz. Okunur çünkü bütün site bu isimleri kullanır. Yeni bir mockup ölçüsü gelince buraya dokunulur.

**02_Base/**
> **eli10 (basit):** Tarayıcıların farklı davranıp bozmasını engelleyen ortak başlangıç ayarları.
> **eli15 (detay):** Ayrı katman çünkü temel ayar her sayfayı ilgilendirir, tek sayfaya ait değildir. İçine çıplak HTML ayarları ve iskelet yazılır. Üstüne her şey kurulduğu için en çok ihtiyaç duyulan katmandır. Tarayıcı davranışı değişince düzenlenir.

**03_Layout/**
> **eli10 (basit):** Sayfanın üstteki, alt taraftaki ve kenardaki bölümlerinin yeri.
> **eli15 (detay):** Ayrı dosya çünkü başlık, alt bilgi ve yan menü her sayfada aynıdır; tekrar tekrar yazılmaz. İçine yalnız düzen kuralları girer, buton gibi parçalar girmez. Düzeni değiştirmek isteyen tek buraya bakar. Mockup düzeni değişince değiştirilir.

**04_Components/**
> **eli10 (basit):** Buton, kart, form gibi birden çok yerde kullanılan parçaların kutusu.
> **eli15 (detay):** Ayrı dosya çünkü aynı parça birçok sayfada görünür; tek yerde olunca düzeltmek bir kere yeter. İçine parçanın kendisi yazılır, hangi sayfada kullanılacağı değil. Parça tasarımı değişince buraya dokunulur.

**05_Pages/**
> **eli10 (basit):** Yalnızca tek bir sayfaya özel süslemeler.
> **eli15 (detay):** Ayrı dosya çünkü sayfaya özel şey ortak kutuya girerse diğer sayfalara da yayılır. İçine o tek sayfanın kuralı yazılır. Sayfanın PHP dosyası değişince burası güncellenir.

**06_Utilities/**
> **eli10 (basit):** Tek işi olan küçük yardımcı etiketler, örneğin "bunu gizle".
> **eli15 (detay):** Ayrı dosya çünkü bu etiketler akıl yürütmek yerine sadece tek bir iş yapar. Yardımcı sınıf eklenince ya da kullanımdan kalkınca düzenlenir.

**07_Vendors/**
> **eli10 (basit):** Başkalarının yazdığı kütüphane — bizim yazmadığımız, karışmamamız gereken kısım.
> **eli15 (detay):** Ayrı klasör karantina gibidir: dışarıdan geldiği için içine elle giriş yapılmaz, yoksa güncellemede kaybolur. Grid ve temel sıfırlama ihtiyacı buradan gelir. Yalnız onaylı sürüm yükseltmesinde değişir.

**08_Devices/**
> **eli10 (basit):** Telefon, televizyon, bilgisayar… her ekranın kendi ayar dosyası.
> **eli15 (detay):** Ayrı dosyalar çünkü ekran ve dokunma farklılıkları cihaza özeldir; hepsi tek dosyada olsaydı telefon ayarı bilgisayarı bozardı. İçine önce gerekli dosyaların bağlantısı, sonra o cihazın davranışı yazılır; yerleşim yazılmaz. Yeni cihaz ya da kırılım eklenince harita dosyalarıyla birlikte düzenlenir.

**09_ViewModes/**
> **eli10 (basit):** Pro, stüdyo, araba gibi farklı görünüm modlarının kendi odası.
> **eli15 (detay):** Ayrı dosya çünkü modlar birbirinden bağımsız açılıp kapanır. Şu an içeriği yok, sadece yeri hazır (iskelet). Mod değişince ya da yeni mod gelince doldurulur.

**10_Helpers/**
> **eli10 (basit):** Tekrar eden küçük numaraların kutusu, örneğin uzun yazıyı üç noktayla kesme.
> **eli15 (detay):** Ayrı dosya çünkü bu yardım deseni tek sınıftan büyüktür, kendi içinde kuralı vardır. Yardımcı katman (06) ile karışmasın diye ayrı durur. Aynı numara ikinci kez lazım olunca buraya yazılır.

**11_OAuth/**
> **eli10 (basit):** Giriş ve üyelik ekranlarının görünüm kuralları.
> **eli15 (detay):** Ayrı katman çünkü giriş akışı ayrı bir adreste çalışır ve izole kalmalı. İçine giriş akışının stilleri yazılır. Giriş akışı değişince değiştirilir.

**kök `auth-bundled.css`**
> **eli10 (basit):** Giriş sayfalarının hepsini tek parça halinde yükleyen bağlantı dosyası.
> **eli15 (detay):** Ayrı dosya çünkü giriş adresi tek kapıdan beslenir, parça parça yükleme olmasın diye. İçine sırayla bağlantılar yazılır, kütüphane (vendor) içermez. Giriş sayfası eklenince ya da sıra değişince düzenlenir.

### 3.1.2 Dosya Bazlı 4-Satır Amaç Blokları (önemli dosyalar — tek tek)

**`01_Abstracts/a-layout-tokens.css` (BASE — restore 2026-10-03)**
- **Ne için:** Medyasız fallback layout token'ı — tüm `a-layout-tokens-{cihaz}` dosyaları bunu ezer.
- **Neyden oluşur:** Tek `:root` bloğu; **221 token / 8089 byte**; seçici/kural YOK; `@media` YOK.
- **Neden var:** 5 normal cihaz dosyası (`d-phone`, `d-tablet`, `d-embedded`, `d-4k-monitor`, `d-4k-tv`) bu yolu import ediyordu, dosya diskte YOKtu → **kırık import**; kanıt `Css copy 3/01_Abstracts/a-layout-tokens copy.css` (7740 byte) → restore.
- **Ne zaman düzenlenir:** Base'e token taşınması, `notes.md` base notu, cihaz token'ının fallback'e bağlanması.

**`a-layout-tokens-{mobile,tablet,1024,3540,3840}.css` (5 dosya)**
- **Ne için:** O cihaz genişliğinde layout token override'ı.
- **Neyden oluşur:** HÂLÂ **0 byte (boş)** — içeriği yok.
- **Neden var:** §3.3 sıralaması bunları bekler (base → mobile → tablet → 1024 → 3540 → 3840); boş = tasarımdan token gelmedi.
- **Ne zaman düzenlenir:** Figma/`notes.md` o cihaz token'ı geldiğinde; **boşken uydurulmaz**.

**`a-layout-tokens-1920.css`**
- **Ne için:** 1920 cihaz override'ı (tek dolu cihaz token dosyası).
- **Neyden oluşur:** **3563 byte** — footer/header/sidebar + `--cm-player-info-*` + welcome-banner + home-song-card token'ları.
- **Neden var:** 1920 mockup ölçümü 1024'ten farklı (footer-text-size 13px vs base 10px).
- **Ne zaman düzenlenir:** 1920 mockup/Figma ölçüsü değişince.

**`a-layout-tokens copy.css` + `copy 2.css` (01_Abstracts)**
- **Ne için:** Base token yedeği (kurtarma kaynağı).
- **Neyden oluşur:** Eski 3.1.0 içeriği — salt okunur yedek.
- **Neden var:** Plan gereği **KALACAK** (silinmez); bu oturumda worktree'den silinmişti → git HEAD'den geri yüklendi.
- **Ne zaman düzenlenir:** DOKUNULMAZ (yalnız restore/onay).

**`05_Pages/_home.css`**
- **Ne için:** Home sayfası çatısı — parçalarını tek yerden import eder.
- **Neyden oluşur:** 3 `@import` (`_home-layout` ✓, `../04_Components/_home-components` ✓, `_home-inline` ✓ — 2026-10-06 doğrulaması: üçü de diskte, kırık değil) + `.home-layout__*` kuralları.
- **Neden var:** Home parçalarının tek çatıda toplanması; bileşenler ayrı dosyada (Guardrail #8).
- **Ne zaman düzenlenir:** Home parçaları eklenince/taşınınca — **import yolu güncellenir** (2026-10-03: `./_home-components.css` → `../04_Components/...`).

**`04_Components/_home-components.css`**
- **Ne için:** Home bileşen stilleri + bileşen-bazlı scale bloğu.
- **Neyden oluşur:** BEM home bileşen kuralları (kanıt: `ScaleManager.js:251-252` yorumu "…tek tek verilir (_home-components.css 'COMPONENT SCALE' bloğu)").
- **Neden var:** Component ≠ page ayrımı — 05_Pages'te component büyüyordu; 2026-10-03 `git mv` ile 04'e taşındı.
- **Ne zaman düzenlenir:** Home bileşeni değişince / scale kuralı revize edilince.

**Normal `d-*.css` (8 dosya: phone, tablet, laptop, desktop, embedded, 4k, 4k-monitor, 4k-tv)**
- **Ne için:** O cihazın **tek giriş dosyası** — katmanları import eder + cihaz davranışı yazar.
- **Neyden oluşur:** 1) import zinciri (§3.8) 2) `:root`/media davranışı (hover, touch, scrollbar, font-scale).
- **Neden var:** ADR-045: her cihaz kendi kırılımını kendi taşır; tek `<link>` ile yüklenir.
- **Ne zaman düzenlenir:** Breakpoint/davranış değişince → **`js/devices.config.js` + `DeviceCssMap.php` aynı görevde**.

**`d-auth-*.css` (7 dosya)**
- **Ne için:** Auth subdomain cihaz override'ı (panel genişliği, hero, form clamp'leri).
- **Neyden oluşur:** Saf kural (`:root` + seçici) — **import YOK**; dosya başı yorum: *"Token imports removed — auth-bundled.css already loads them"*.
- **Neden var:** `auth-bundled.css` zaten token yükler → import tekrarı çift yüklenme üretirdi.
- **Ne zaman düzenlenir:** Auth cihaz ölçüsü değişince (`DeviceCssMap::authToCssPath` üzerinden yüklenir).

**İskeletler: `v-home/pro/studio/car.css` + `h-ellipsis.css` (5 dosya)**
- **Ne için:** ViewMode ve helper katmanının yer tutucusu.
- **Neyden oluşur:** Şu an yalnız 4 satır başlık yorumu — **0 kural**.
- **Neden var:** Guardrail #16 + import zinciri bu dosyaları bekliyor (d-desktop/d-laptop/d-4k `09_ViewModes/v-*` import ediyor → iskeletle kırıklık çözüldü); içerik **Mockup Before Frontend** kapısında.
- **Ne zaman düzenlenir:** İlgili görünüm modu/helper mockup'ı gelince (iskelet doldurulur).

**`06_Utilities/u-helpers-utility.css` · `11_OAuth/oauth.css` · `02_Base/*` · `03_Layout/*`**
- **Ne için / Neyden / Neden var:** css-template §3.1 katman sorumluluğu (dosya içi içerik OKUNMADI bu görevde — ⚠️ VERIFICATION REQUIRED).
- **Ne zaman düzenlenir:** Katman sorumluluğu değişmez; yalnız içerik sahiplik görevinde (UI Designer).

**Dosya eli10/eli15 blokları (§3.1.2'deki 10 dosya maddesinin karşılığı):**

**`01_Abstracts/a-layout-tokens.css` (BASE)**
> **eli10 (basit):** Her şeyin başladığı, adları tanımlayan tek dosya.
> **eli15 (detay):** Ayrı dosya çünkü bütün ölçümlerin düştüğü yer burasıdır; içine sadece değer satırları yazılır, kural yazılmaz. Boş kalsaydı beş cihaz dosyasındaki bağlantı bozuk kalırdı. Yeni temel ölçüsü gelince düzenlenir.

**`a-layout-tokens-{mobile,tablet,1024,3540,3840}.css` (5 dosya — boş)**
> **eli10 (basit):** Telefon, tablet gibi ekranların kendi ölçü defterleri — henüz boş.
> **eli15 (detay):** Ayrı dosyalar çünkü her ekranın ölçüsü ayrı sayfada tutulur. Şu an içleri boş, çünkü tasarım o ekranlar için henüz gelmedi; boş diye değer uydurulmaz. Ölçü geldiğinde doldurulur.

**`a-layout-tokens-1920.css`**
> **eli10 (basit):** Geniş ekran için ölçü defteri — içi dolu tek cihaz dosyası.
> **eli15 (detay):** Ayrı dosya çünkü geniş ekranın ölçüleri farklı (örneğin alt bilgi boyu 10 yerine 13). İçine o ekranın değerleri yazılır. Geniş ekran mockup'ı değişince düzenlenir.

**`a-layout-tokens copy.css` + `copy 2.css`**
> **eli10 (basit):** Eski halinin saklandığı yedek dosyalar.
> **eli15 (detay):** Ayrı dururlar çünkü kurtarma için gerekebilir; silinmezler. İçlerine yeni bir şey yazılmaz, sadece okunurlar. Yalnız onaylı geri yükleme gerektiğinde kullanılır.

**`05_Pages/_home.css`**
> **eli10 (basit):** Ana sayfanın kendisi ve parçalarını toplayan dosya.
> **eli15 (detay):** Ayrı dosya çünkü ana sayfa birçok parçayı bir araya getirir; içine önce bağlantılar, sonra sayfaya özel kurallar yazılır. Bağlantılardan biri taşınınca (ör. bileşen dosyası) hemen güncellenir. İçi okunur çünkü bağlantı zinciri burada.

**`04_Components/_home-components.css`**
> **eli10 (basit):** Ana sayfa parçalarının toplandığı kutu — yeni yeri bileşen klasörü.
> **eli15 (detay):** Ayrı dosya çünkü parçalar sayfaya değil ortak kutuya ait. Taşınma nedeni sayfa dosyasının büyüyüp okunmaz olmasını önlemekti. Parça eklenince ya da ölçüsü değişince düzenlenir.

**Normal `d-*.css` (8 dosya)**
> **eli10 (basit):** Her ekran için hem bağlayıcı hem küçük ayar dosyası.
> **eli15 (detay):** Ayrı dosyalar çünkü her ekran kendi ayarıyla açılır. İçine önce bağlantılar, sonra o ekranın davranışı yazılır. Ekran ya da kırılım değişince iki harita dosyasıyla birlikte düzenlenir.

**`d-auth-*.css` (7 dosya)**
> **eli10 (basit):** Giriş ekranlarının ekran başına ayarları — bağlantı taşımaz.
> **eli15 (detay):** Ayrı dosyalar çünkü bağlantıları zaten ana giriş dosyası taşıyor, tekrar olmasın diye içleri boş bırakıldı. İçlerine sadece o ekranın ayarı yazılır. Giriş ekranı ölçüsü değişince düzenlenir.

**İskelet dosyalar (v-home/pro/studio/car + h-ellipsis — 5 dosya)**
> **eli10 (basit):** Şimdilik boş, adı hazır kutular — içleri doldurulmayı bekliyor.
> **eli15 (detay):** Ayrı dosyalar çünkü adları sistemin bağlanması için hazır, içleri ise görsel (mockup) gelmeden doldurulmaz. Boşken kural uydurulmaz. İlgili tasarım gelince doldurulur.

**Diğer dosyalar (02/03/06/11 grup)**
> **eli10 (basit):** Temel ayar, düzen, küçük yardımcı ve giriş akışı dosyaları.
> **eli15 (detay):** Ayrı dosyalar çünkü her biri kendi işini yapar: temel, düzen, yardımcı, giriş akışı. İçleri bu görevde okunmadı — şablonun katman tarifine göre konumları belli. İlgili kural değişince düzenlenir.

### 3.2 Token Cihaz Ayrımı — 7 base + cihaz token dosyası (01_Abstracts)

| Dosya | Rol | Durum (2026-10-03 ölçümü) |
|-------|-----|---------------------------|
| `a-layout-tokens.css` | **BASE — medyasız fallback** | **8089 byte / 221 token** — `Css copy 3/01_Abstracts/a-layout-tokens copy.css` (tek `:root` bloğu) restore edildi |
| `a-layout-tokens-mobile.css` | ≤767px | **0 byte (boş)** ⚠️ |
| `a-layout-tokens-tablet.css` | 768–1023px | **0 byte (boş)** ⚠️ |
| `a-layout-tokens-1024.css` | RPi5 1024×600 | **0 byte (boş)** ⚠️ |
| `a-layout-tokens-1920.css` | Full HD / wide desktop | 3563 byte (dolu) |
| `a-layout-tokens-3540.css` | 4K monitor | **1264 byte / 35 satır** (2026-10-06 Get-Item) — dolu ⚠️ yalnızca orphan `d-4k-monitor.css:19`'ten import edilir → runtime'da yüklenmiyor |
| `a-layout-tokens-3840.css` | 4K TV | **1242 byte / 34 satır** (2026-10-06 Get-Item) — dolu ⚠️ yalnızca orphan `d-4k-tv.css:9`'dan import edilir → runtime'da yüklenmiyor |

**Sıralama (öncelik):** base → mobile → tablet → 1024 → 1920 → 3540 → 3840. Base medyasızdır; `@media` yalnız cihaz dosyalarında.

**Copy dosyaları (yedek — SİLİNMEZ):** `Css/01_Abstracts/` içinde `a-layout-tokens copy.css` / `copy 2.css` **diskte YOK** (2026-10-03 doğrulandı — plan iddiası ile disk çelişiyor, §3.6). Yedekler `assets.coremusic.net/Css copy/`, `Css copy 2/`, `Css copy 3/` klasörlerindedir (`Css copy 3/01_Abstracts/a-layout-tokens copy.css` = 7740 byte, restore kaynağı). Bu klasörler referans alınmaz.

### 3.3 Import Zinciri Haritası — 15 cihaz + auth-bundled

> **Normal grup (8 dosya):** her biri TAM import zinciri taşır (01 token → 07 bootstrap → 02 base → 03 layout → 04 components → 05 pages → 06 utilities → 09 viewmodes) + cihaz davranışı override.

| Dosya | Import zinciri | Not |
|-------|----------------|-----|
| `d-phone.css` | VAR | token imports + bootstrap reboot/grid + layout + components |
| `d-tablet.css` | VAR | `a-layout-tokens.css?v=5.1.0` (restore ile çözüldü) |
| `d-laptop.css` | VAR | 1920 token + tam zincir (bakılık başlık "Laptop") |
| `d-desktop.css` | VAR | 1920 token + tam zincir |
| `d-embedded.css` | VAR | `a-layout-tokens.css?v=5.1.0` + behavioral override (hover devre dışı) |
| `d-4k.css` | VAR | 1024 token + tam zincir (başlık "Laptop" — kopya hata, içerik korundu) |
| `d-4k-monitor.css` | VAR | `a-layout-tokens.css` (restore ile çözüldü) + override ağırlıklı |
| `d-4k-tv.css` | VAR | `a-layout-tokens.css` (restore ile çözüldü) + `--touch-*` büyük |

> **Auth grup (7 dosya): dosyada import YOKTUR** — dosya başı yorum: *"Token imports removed — auth-bundled.css already loads them."* Doğrulandı (grep: 0 `@import`). `d-auth-4k.css` YOKTUR (yalnız `-4k-monitor` / `-4k-tv`).

**`auth-bundled.css` (kök giriş) — grup sırası 01 → 02 → 05 → 08 DOĞRULANDI:**

| Grup | Katman | Import | Not |
|------|--------|--------|-----|
| 1 | 01_Abstracts | a-fonts-token · a-theme-config · a-light-glass-tokens · a-login-tokens | sıralı |
| 2 | 02_Base | b-base-core | reset |
| 3 | 05_Pages | p-select-gender · p-login-view | auth sayfaları |
| 4 | 08_Devices | **@import YOK** | `d-auth-*` ayrı `<link id="cm-device-css">` ile `DeviceCssMap` üzerinden dinamik basılır (çift yüklenir) |
| 5 | kök | dosya içi düzeltme kuralları (checkbox + `body.auth-page`) | `07_Vendors` **içermez** |

**Diğer import sahipleri:** `05_Pages/_home.css` 3 import taşır → `./_home-layout.css` (**diskte VAR — kırık değil, 2026-10-06 doğrulaması**), `../04_Components/_home-components.css` (**2026-10-03 taşınma ile GÜNCELLENDİ**), `./_home-inline.css` (**diskte VAR — kırık değil, 2026-10-06 doğrulaması**).

### 3.4 Kırık Import Listesi (disk kanıtı — düzeltilmedi, kapsam dışı ⚠️ VERIFICATION REQUIRED)

| Kırık hedef | Referans eden | Durum |
|-------------|---------------|-------|
| `./_home-layout.css`, `./_home-inline.css` | `05_Pages/_home.css` | diskte VAR — `05_Pages/_home-layout.css` + `05_Pages/_home-inline.css` (2026-10-06 doğrulaması; import göreli yolü doğru, kırık değil; yedek: `Css copy 2/05_Pages/_home-layout.css`) |
| `../04_Components/_widget-grid.css` | d-desktop · d-laptop · d-4k | dosya `03_Layout/_widget-grid.css`'te VAR — **yanlış katman referansı** |
| `../04_Components/c-buttons · c-card · c-forms · c-modal · c-badge · c-toggle · c-toast · c-progress · c-scrollbar-accent` (9 dosya) | d-desktop · d-laptop · d-4k | diskte VAR — "9 dosyanın tamamı `04_Components/` altında (2026-10-06 doğrulaması)" |
| `../05_Pages/_player · p-albums · p-album-detail · p-artists · p-playlist · p-settings` (6 dosya) | d-desktop · d-laptop · d-4k | diskte VAR — "6 dosyanın tamamı `05_Pages/` altında (2026-10-06 doğrulaması)" |

> Bu kırıklar `08_Devices/` (salt okunur) ve "taşma/yeni iskelet dışında dosya içi değişiklik YOK" kuralı gereği **düzeltilmedi** — yalnız raporlandı. Etki: eksik dosyalar sessizce atlanır (CSS spec: kırık `@import` yüklenmez).

### 3.5 Giriş Noktaları

| Giriş | Kime | Sıra |
|-------|------|------|
| `08_Devices/d-{cihaz}.css` | ana subdomainler (`<link>` + device-loader) | token → vendor → base → layout → components → pages → utilities → viewmodes |
| `auth-bundled.css` | auth subdomain | 01 → 02 → 05 (+ 08 dinamik) |
| `main.css` | **YOK** | — |

### 3.6 Template-Disk Çelişkileri (disk kazanır; uydurulmaz)

| İddia (kaynak) | Disk gerçeği (2026-10-03) | İşaret |
|----------------|---------------------------|--------|
| `css-template.md` §3.1: 04'te `c-buttons, c-forms, c-card, c-modal, c-badge, c-toggle, c-toast, c-progress, c-scrollbar-accent` | **beklenen, diskte VAR** (04 = 15 dosya; 2026-10-06 ölçümü) | ✅ |
| `css-template.md` §3.1: 05'te `p-settings, p-artists, p-albums, p-album-detail, p-playlist, _home-layout, _home-inline, _player` | **beklenen, diskte VAR** (05 = 12 dosya; 2026-10-06 ölçümü) | ✅ |
| `css-template.md` §3.1: 01 listesi `a-layout-tokens copy`, `copy 2` içerir | `Css/01_Abstracts/`'ta **YOK**; yedekler `Css copy*` klasörlerinde | ⚠️ |
| `assets.coremusic.net/AGENTS.md` §2: "`Css/main.css`" | **main.css YOK** (2026-09-30 silindi) | ⚠️ |
| `assets.coremusic.net/AGENTS.md` §2: "08 = 13 device CSS" | **15 dosya** (8 normal + 7 auth) | ⚠️ |
| `assets.coremusic.net/AGENTS.md` §2: "04 = 3 dosya (c-footer-seek, c-footer-volume, c-scrollbar-accent)" | **6 dosya; `c-scrollbar-accent` YOK** | ⚠️ |
| `assets.coremusic.net/AGENTS.md` §2: "09 = v-car, v-home, v-pro, v-studio" | dosya adları doğru; **hepsi iskelet** (0 kural) | ✅ iskelet |
| Plan: "`01_Abstracts=21`" | **20 dosya** (restore sonrası; copy dosyaları 01'de değil) | ⚠️ |

| 3.7 | Klasör Dışı İlişkiler (Css/ ↔ sistem) — aşağıda |

### 3.7 Klasör Dışı İlişkiler

| Sistem dosyası | İlişki | Kanıt |
|----------------|--------|-------|
| `assets.coremusic.net/js/devices.config.js` | cihaz breakpoint → css yolu (frontend) | `assets.coremusic.net/AGENTS.md` §2 + css-template §3.5 |
| `assets.coremusic.net/js/device-loader.js` | cihaz kırılımına göre CSS yükler | `d-desktop.css`/`d-4k-tv.css` başlık yorumu: "device-loader.js breakpoint: …" |
| `assets.coremusic.net/js/device-layout-updater.js` | cihaz değişince layout token günceller | `assets.coremusic.net/AGENTS.md` §2 envanteri |
| `shared/src/Device/DeviceRenderer.php` (`headLinks()`, :122-124) | `d-auth-*` ayrı `<link id="cm-device-css">` olarak basılır | `auth-bundled.css` yorum satırı (dosya içi kanıt) |
| `shared/src/Device/DeviceCssMap.php` (`authToCssPath()`) | auth cihaz → css yolu eşlemesi | `auth-bundled.css` yorum satırı |
| PHP docblock yorumları | dosya adı/yolu değişince güncellenir | `css-template` §4.2 |
| `pages/**/*.php` | `05_Pages/` karşılığı | `css-template` §3.1 ayrım kuralı |

### 3.8 Import Sıra Prensipleri (disk kanıtı — `d-desktop.css` tam zincir)

| Sıra | Grup | İçerik | Neden burada |
|------|------|--------|--------------|
| 1 | 01 token | fonts → colors → semantic → color-mode → design → theme → light-glass → login → breakpoint → scale-hybrid → layout-1920 → widget-grid → welcome-banner | her şeyin fallback'i en önce |
| 2 | 07 vendor | `bootstrap-reboot.min` + `bootstrap-grid.min` (v5.3.8) | **02_Base'den ÖNCE** — reboot `body{background-color:#fff}` ve `.container` breakpoint'leri b-base-core'i ezer (dosya yorumu: "NEDEN BURADA") |
| 3 | 02 base | b-base-core → l-main-structural → (page-layout, yalnız d-4k/d-embedded) | reset vendor'dan sonra |
| 4 | 03 layout | _header → _footer → _widget-grid | düzen |
| 5 | 04 components | _player-info, _welcome-banner, c-footer-*, c-home-song-btn (+ §3.4'teki kırıklar) | bileşenler düzeni tüketir |
| 6 | 05 pages | _home (+ §3.4'teki kırıklar) | sayfalar bileşeni tüketir |
| 7 | 06 utilities | u-helpers-utility | en son sınıf katmanı |
| 8 | 07 lib | `v-bootstrap-lib.css` | vendor yardımcı katmanı |
| 9 | 09 viewmodes | v-home → v-pro → v-studio (v-car zincirde YOK — disk kanıtı) | en sonda override |

> `auth-bundled.css` bu sıradan bağımsızdır: **01 → 02 → 05** + 08 dinamik (§3.3).

### 3.9 İçerik Neden Dosyalara Ayrıldı? (katman bölme gerekçesi)

| # | Gerekçe | Ne korur | Boşsa ne olur (her şey tek dosyada olsaydı) |
|---|---------|----------|----------------------------------------------|
| 1 | **Bakım kolaylığı** | Değişiklik tek dosyada kalır, inceleme (diff) küçük ve güvenli olur | Tek dosyada her şey değişince hangi kuralın ne yaptığı kaybolur, inceleme imkânsızlaşır |
| 2 | **Tek sorumluluk (tek iş)** | Her dosyanın tek görevi ve tek sahibi olur | Dosya büyüyüp okunmaz; "bunu kim yazdı, neden" sorusu cevapsız kalır |
| 3 | **Token tek kaynak** | Ölçü/renk `01_Abstracts`'te tek yerde durur | Her dosyada ayrı `16px` → tutarsız görünüm, ölçü değişince dosya dosya tarama |
| 4 | **Cihaz izolasyonu** | Telefon ayarı masaüstünü bozmaz | Her cihaz farkı tüm dosyalara yayılır → 8+ yerde tekrar, uyumsuzluk (drift) |
| 5 | **Vendor karantinası** | 3. taraf kod `07_Vendors`'ta ayrı durur | Dış kod içimize karışıp güncellemede kaybolur, güvenlik yamaları şaşar |

> **eli10 (basit):** Bilgiler tek kocaman dosyaya değil de küçük küçük kutulara konmuş; çünkü kutulardan birini değiştirmek diğerlerine zarar vermez.
> **eli15 (detay):** Ayrı dosyalar çünkü aynı işi yapan herkes aynı yere baksın diye: değerler tek sözlükte, düzen tek evde, parça tek kutuda durur. Böylece bir yeri değiştirince sadece o değişir, diğerleri bozulmaz. Cihaz ayarları ayrı olunca telefon ayarı masaüstünü bozmaz, hazır kütüphane ayrı olunca güncelleme bizim kodumuzu bozmaz. Hepsi tek dosyada toplansa bakım ve kontrol edilebilirlik kaybolurdu.

---

## 4. Kurallar

Guardrail numaralarının tam kaynağı: [[../../.ai/.templates/frontend/css-template]] §4.1 (10 madde).

| # | Kural (özet) | Ref |
|---|--------------|-----|
| 1 | Hardcoded piksel/değer yasak — her değer `var(--...)` | css-template §4.1 #1 |
| 2 | `!important` yasak (en fazla 3 istisna, gerekçe yorumda) | §4.1 #2 |
| 3 | Inline `style=""` yasak (CSP nonce uyumsuz) | §4.1 #3 |
| 4 | Önişlemci (SCSS/LESS) yasak — saf CSS | §4.1 #4 |
| 5 | Cihaz katmanı yalnız import + davranış; yerleşim 02/03 | §4.1 #5 |
| 6 | BEM zorunlu `.block__element--modifier` | §4.1 #6 |
| 7 | WCAG: dokunma hedefi + mockup okunmadan CSS yazılamaz | §4.1 #7 |
| 8 | Component → 04, PHP sayfası → 05 ayrımı | §4.1 #8 |
| 9 | Token → 01_Abstracts (cihaz dosyasına göre) | §4.1 #9 |
| 10 | `07_Vendors/` elle düzenlenmez (33 dosya salt okunur) | §4.1 #10 |

- **Çelişki kuralı:** vault ↔ disk → disk kazanır + `⚠️ VERIFICATION REQUIRED`.
- **Cihaz dosyası ekleme/değişikliği** → `js/devices.config.js` + `DeviceCssMap.php` eşzamanlı (bkz. [[WORKFLOW]]).
- Bu dokümandaki her sayı ölçümdür; yeni ölçümde eski sayı geçersizdir.

---

## 5. Workflow

```
NOTES.MD OKU → MOCKUP OKU → KATMAN SEÇ (css-template §3.1)
→ ŞABLON KOPYALA → {{VARIABLE}} DOLDUR → GUARDRAIL #16 DOĞRULA
→ TARAYICI TESTİ → COMMIT (subagent ATMAZ)
```

Adım detayı: [[WORKFLOW]] §5.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan + `docType` |
| 2 | Bölüm | §1–§7, ≤3 başlık seviyesi |
| 3 | Envanter | Her katman sayısı disk ölçümü ile eşit (19/3/4/15/12/1/33/15/4/1/1 + kök 1 — **2026-10-06 ölçümü**) |
| 4 | Çelişki | §3.6 eksiksiz; §3.4 iddiaları disk kanıtı ile hizalı (2026-10-06) |
| 5 | Kırık import | §3.4 listesi güncel |
| 6 | main.css | İddia edilmedi (YOK olarak yazıldı) |
| 7 | Wiki-link | `[[...]]` formatı, hedefler var |
| 8 | Halüsinasyon | Diskte olmayan dosya var sayılmadı |
| 9 | **eli10 + eli15 bloğu** | §3.1.1 12 katman + §3.1.2 10 dosya + §3.9 bölümünde `> **eli10 (basit):**` / `> **eli15 (detay):**` blokları var mı; etiket/sıra şablonla aynı mı |
| 10 | **İçerik neden ayrı** | §3.9 tablosu 5 satır (bakım · tek sorumluluk · token tek kaynak · cihaz izolasyonu · vendor karantinası) |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| CSS ana şablon | [[../../.ai/.templates/frontend/css-template]] | Katman kuralları, guardrail #1-#10 |
| Template registry | [[../../.ai/.templates/index]] | Şablon envanteri |
| Vault anayasası | [[../../.ai/CLAUDE.md]] | 16 Hard Guardrails |
| Agent registry | [[../../AGENTS.md]] | Routing: CSS → UI Designer |
| Katman dokümanı | [[CLAUDE]] | Guardrail özeti (klasör içi) |
| Rol dokümanı | [[AGENTS]] | Kim ne yapar |
| Süreç dokümanı | [[WORKFLOW]] | Adım akışı + cihaz eşzamanlılık |
| Mockup indeksi | [[../../.ai/ui-design/01-mockup-index]] | Mockup Before Frontend |
| Disk kanıtı | `assets.coremusic.net/Css/` | Bu dokümandaki tüm sayılar |
| Yedek kopyalar | `assets.coremusic.net/Css copy*/` | Base token restore kaynağı (referans alınmaz) |

---

**Version:** 1.0.0 · **Last Updated:** 2026-10-03 · **docType:** context
