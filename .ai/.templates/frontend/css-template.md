---
title: "CoreMusic — CSS/ITCSS Development Template"
type: template
category: frontend
date: 2026-09-06
updated: 2026-10-03
version: 3.0.1
status: active
authority: reference
---

# CoreMusic — CSS/ITCSS Development Template

**Teknoloji:** ITCSS katman sırası, BEM adlandırma, CSS custom property (design token) · **Katman:** L3 (sunum) · **Sorumlu Agent:** UI Designer

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]] · [[js-template]] · [[../../ui-design/01-mockup-index]]

---

## 1. Amaç

Bu şablon, CoreMusic stylesheet geliştirme standardını (11 katman sırası, BEM, token tabanlı değer kullanımı, cihaz token ayrımı) ve kod iskeletlerini tanımlar. **Guardrail #16:** yeni `.css` dosyası bu şablondan üretilmek ZORUNLUDUR.

| Karar | ADR | Şablona gömülü karşılığı |
|-------|-----|--------------------------|
| Vanilla JS + ITCSS, framework/önişlemci yasak | ADR-001 | §4 #1-#4 — saf CSS + katman sırası |
| Footer player (vaporwave) | ADR-018 | §3.6 bileşen iskeleti |
| Cinsiyet bazlı dinamik tema | ADR-044 | §3.7 tema jetonu override |
| Multi-domain görünüm modu | ADR-045 | §3.5 cihaz import zinciri |
| Erişilebilirlik (WCAG 2.2 AA) | — | §4 #7 — `--touch-min: 44px` |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `assets.coremusic.net/Css/**/*.css` — 11 katman + kök giriş | JavaScript modülleri → `[[js-template]]` |
| Design token (custom property) tanımı ve cihaz override'ı | Mockup görselleri → `.ai/ui-design/` (okunur, üretilmez) |
| BEM adlandırma + cihaz davranış uyarlaması | `07_Vendors/` — düzenlenmez, yalnızca sürüm güncellenir |
| `auth-bundled.css` giriş noktası | PHP/HTML şablonları (inline style yasak → §4 #3) |

- **Kullananlar:** UI Designer (birincil), QA Engineer (WCAG/responsive), Backend Architect (inline style denetimi).
- **Ön koşul:** Mockup Before Frontend — `.ai/ui-design/` görseli okunmadan CSS yazılamaz; okunamıyorsa DUR.
- **Not uygulama:** repo kökü `notes.md` içindeki notlar görev başında okunur ve ilgili katmana uygulanır.

---

## 3. Mimari

### 3.1 Disk Kanıtı — Katman Sırası (`assets.coremusic.net/Css/`)

> Sıra **01 → 11** + kök giriş. Alt katman üst katmanı geçersiz kılmaz.

| # | Katman | Sorumluluk | Önek | Disk kanıtı (dosya) |
|---|--------|-----------|------|---------------------|
| 01 | `01_Abstracts/` | **Yalnız token** — custom property; kural/seçici YAZILMAZ | `a-` | `a-layout-tokens-{1024,…}.css`, `a-colors-token`, `a-fonts-token`, `a-breakpoint-tokens`, `a-semantic-token`, `a-primitive-tokens`, `a-scale-hybrid`, `a-design-tokens`, `a-theme-config`, `a-color-mode-tokens`, `a-light-glass-tokens`, `a-login-tokens`, `a-widget-grid-tokens`, `a-welcome-banner-tokens` |
| 02 | `02_Base/` | Genel yapı — bare HTML reset, base giriş iskeleti | `b-` / `l-` / `p-` | `b-base-core.css`, `l-main-structural.css`, `page-layout.css` |
| 03 | `03_Layout/` | Sayfa düzeni: grid, header, footer, sidebar | `_` | `_header.css`, `_footer.css`, `_sidebar.css`, `_widget-grid.css` |
| 04 | `04_Components/` | BEM bileşenleri: buton, form, menü, logo, kart… (Figma component karşılığı) | `c-` / `_` | `c-buttons`, `c-forms`, `c-card`, `c-modal`, `c-badge`, `c-toggle`, `c-toast`, `c-progress`, `c-scrollbar-accent`, `c-footer-seek`, `c-footer-volume`, `c-home-song-btn`, `_home-components`, `_welcome-banner`, `_player-info` |
| 05 | `05_Pages/` | **PHP sayfalarına özel** stiller (`pages/**/*.php`) | `p-` / `_` | `p-login-view`, `p-select-gender`, `p-settings`, `p-artists`, `p-albums`, `p-album-detail`, `p-playlist`, `_home`, `_home-layout`, `_home-inline`, `_player`, `_welcome` |
| 06 | `06_Utilities/` | Utility sınıfları (tek satır işlev, `.is-hidden` vb.) | `u-` | `u-helpers-utility.css` |
| 07 | `07_Vendors/` | 3. taraf (Bootstrap ailesi) — **DÜZENLENMEZ** | `v-` | `v-bootstrap-lib.css` + `bootstrap*.css` (normal/rtl/min/map) |
| 08 | `08_Devices/` | Cihaz kırılım **import zinciri** + davranış override | `d-` / `d-auth-` | 15 dosya → §3.5 |
| 09 | `09_ViewModes/` | Görünüm modu (home, pro, studio, car) | `v-` | `v-home`, `v-pro`, `v-studio`, `v-car` |
| 10 | `10_Helpers/` | Helper sınıf/makro'ları (§3.8) | `h-` | `h-ellipsis.css` (VAR — iskelet, içerik bekleniyor; 2026-10-04) |
| 11 | `11_OAuth/` | OAuth/login akış stilleri | `o-` | `oauth.css` |
| kök | `auth-bundled.css` | Auth subdomain **tek giriş** → §3.5 | — | `auth-bundled.css` |

**YOK olanlar (uydurulmaz):** `main.css` (2026-10-03 itibarıyla diskte YOK — cihaz girişi `08_Devices/d-*.css` üzerindendir). `a-layout-tokens.css` **diskte YOK** (git ls-files'ta hiç track edilmedi; 5 cihaz dosyası bu yolu import ediyordu → kırık import; BASE artık `a-layout-tokens-1024.css`). `Css copy 2/` klasörü yedektir, referans alınmaz.

**Ayrım kuralı (kritik):**

| İçerik | Katman |
|--------|--------|
| PHP sayfasına özgü (`pages/**/*.php` karşılığı) | `05_Pages/` |
| Birden çok sayfada tekrar eden görsel parça (buton, kart, form alanı, menü, logo) | `04_Components/` |
| Grid / header / footer / sidebar düzeni | `03_Layout/` |
| Sadece `--token: değer` | `01_Abstracts/` |
| Boyut/görünürlük/hover gibi **cihaz davranışı** | `08_Devices/` |

### 3.2 Dosya Adlandırma

| Katman | Desen | Örnek |
|--------|-------|-------|
| 01 | `a-{konu}-token(s).css` | `a-layout-tokens-mobile.css` |
| 02 | `b-` base · `l-` structural · `page-` | `b-base-core.css` |
| 03–05 | `_` (alt çizgi) + konu | `_header.css`, `_home.css` |
| 04 | `c-{bileşen}.css` | `c-buttons.css` |
| 05 | `p-{sayfa}.css` | `p-settings.css` |
| 06 | `u-` | `u-helpers-utility.css` |
| 07 | `v-` | `v-bootstrap-lib.css` |
| 08 | `d-{cihaz}.css` · `d-auth-{cihaz}.css` | `d-phone.css`, `d-auth-phone.css` |
| 09 | `v-{mod}.css` | `v-studio.css` |
| 10 | `h-` | `h-ellipsis.css` |
| 11 | `o-` | `oauth.css` (mevcut ad korunur) |

### 3.3 Token Şablonu — Cihaz Ayrımı (`01_Abstracts/`)

> **KURAL:** Her cihaz genişliği için AYRI token dosyası yazılır — hangi cihaz için hangi token'ın yazıldığı dosya adından anlaşılır.

```
01_Abstracts/
├── a-layout-tokens-1024.css     # BASE — 1024×600 (RPi5) · medyasız :root · tüm cihaz fallback'i
│                                 # (eski a-layout-tokens.css diskte YOK — git track yok)
├── a-layout-tokens-mobile.css   # ≤767px  (phone)
├── a-layout-tokens-tablet.css   # 768–1023px
├── a-layout-tokens-1920.css     # full HD / wide desktop
├── a-layout-tokens-3540.css     # 4K monitor
└── a-layout-tokens-3840.css     # 4K TV
```

```css
/* 01_Abstracts/a-layout-tokens-{{width}}.css */
/* CİHAZ: {{device}} · GÖRÜNÜM: {{min}}–{{max}}px
 * Bu dosya YALNIZCA token değeri değiştirir — seçici/kural yazılmaz.
 * Kaynak: .ai/ui-design/tokens/ (Figma SSOT) · notes.md güncellemeleri buraya. */

@media (min-width: {{min}}px) and (max-width: {{max}}px) {
  :root {
    /* --- Genel ölçü --- */
    --header-h: 60px;
    --footer-h: 90px;
    --sidebar-w: 280px;
    --grid-gap: 8px;
    --touch-min: 48px;

    /* --- Component token'ları (v3.0.0) --- */
    --now-playing-art-size: 100px;
    --widget-grid-cols: 2;
    --footer-album-art-size: 120px;
    --footer-icon: 13px;
    --footer-btn-min-size: 48px;

    /* --- Kart boyutları --- */
    --card-thumb-size: 140px;
    --album-art-size: 300px;
    --mini-card-art-size: 50px;

    /* --- Tipografi (a-scale-hybrid'den akış) --- */
    --text-base: var(--fs-base, 12px);
    --text-lg: var(--fs-lg, 14px);

    /* --- Z-Index --- */
    --z-header: 100;
    --z-dropdown: 200;
    --z-modal: 300;
    --z-toast: 400;
  }
}
```

**Sıralama (öncelik):** base (`a-layout-tokens-1024.css`) → mobile → tablet → 1920 → 3540 → 3840. Daha geniş kırılım daha sonra gelir, dar olanı ezer. (Ayrı `a-layout-tokens.css` base'i **diskte YOK** — 2026-10-03.)

**`a-layout-tokens-1024.css` iç yapısı (referans — Figma SSOT blokları korunur):** component token'ları → kart boyutları → detail panel → home layout grid → embedded home layout (Figma node ref'li) → touch targets → spacing → font scale → glass → device-aware scaling → z-index → player info (`--cm-player-*`).

### 3.4 Bileşen Şablonu (BEM — `04_Components/c-{{block}}.css`)

```css
/* 04_Components/c-{{block}}.css
 * BEM: .block__element--modifier + durum: .block.is-...
 * TOKEN: değerler 01_Abstracts'ten gelir; burada ham hex/px YAZILMAZ. */

.{{block}} {
  display: flex;
  align-items: center;
  gap: var(--space-md);
  min-height: var(--touch-min);
  padding: var(--space-sm) var(--space-md);
  background: var(--bg-secondary);
  border-radius: var(--radius-md);
  transition: box-shadow var(--transition-fast);
}

.{{block}}__title {
  margin: 0;
  font-size: var(--text-base);
  color: var(--text-primary);
}

.{{block}}__button {
  min-width: var(--touch-min);
  min-height: var(--touch-min);
  border: 0;
  border-radius: var(--radius-full);
  background: var(--bg-tertiary);
  color: var(--text-primary);
  cursor: pointer;
}

.{{block}}--compact {
  gap: var(--space-xs);
}

.{{block}}.is-active {
  box-shadow: var(--shadow-md);
}
```

### 3.5 Cihaz Import Zinciri (`08_Devices/`) — 15 dosya

> **Bu katmanın görevi:** (1) gerekli katmanları **import et**, (2) cihaz davranışı override et. Yerleşim (layout) kuralı buraya yazılmaz — yerleşim `02_Base`/`03_Layout`'ta kalır.

**Normal grup (8):**

| Dosya | Cihaz | Not |
|-------|-------|-----|
| `d-phone.css` | ≤767px | touch-first, hover yok |
| `d-tablet.css` | 768–1023px | touch-first |
| `d-laptop.css` | laptop | tam import zinciri |
| `d-desktop.css` | desktop 1920 | tam import zinciri |
| `d-embedded.css` | RPi5 1024×600 | hover devre dışı |
| `d-4k.css` | 4K genel | tam import zinciri |
| `d-4k-monitor.css` | 4K monitor | override ağırlıklı |
| `d-4k-tv.css` | 4K TV (10ft) | `--touch-*` büyük |

**Auth grup (7) → `auth-bundled.css` altında toplanır:**

`d-auth-phone.css` · `d-auth-tablet.css` · `d-auth-laptop.css` · `d-auth-desktop.css` · `d-auth-embedded.css` · `d-auth-4k-monitor.css` · `d-auth-4k-tv.css`

> Auth'ta `d-auth-4k.css` YOKTUR — uydurulmaz (yalnız `-4k-monitor` ve `-4k-tv`).

**İskelet:**

```css
/* 08_Devices/d-{{device}}.css
 * 1) IMPORT — bu cihazın ihtiyacı olan katmanlar (sıra: Abstracts → Base →
 *    Layout → Components → Pages → Utilities → ViewModes)
 * 2) DAVRANIŞ — yalnızca davranış/kırılım uyarlaması.
 * YERLEŞİM KURALI YAZILMAZ (§3.1 ayrım kuralı). */

@import url("../01_Abstracts/a-layout-tokens-{{width}}.css?v={{v}}");
@import url("../02_Base/b-base-core.css?v={{v}}");
@import url("../03_Layout/_header.css?v={{v}}");
@import url("../04_Components/c-{{block}}.css?v={{v}}");
@import url("../05_Pages/p-{{page}}.css?v={{v}}");
@import url("../06_Utilities/u-helpers-utility.css?v={{v}}");
@import url("../09_ViewModes/v-{{mode}}.css?v={{v}}");

@media (max-width: 767px) {
  .{{block}}__optional { display: none; }
  .{{block}} { min-height: var(--touch-min); }
}
```

```css
/* 08_Devices/d-auth-{{device}}.css — auth subdomain */
/* auth-bundled.css üzerinden yüklenir; Bootstrap import EDİLMEZ
 * (auth sayfalarında grid kullanılmıyor → reboot b-base-core'i ezer). */
@import url("../01_Abstracts/a-login-tokens.css?v={{v}}");
@import url("../05_Pages/p-login-view.css?v={{v}}");
/* + cihaz davranışı */
```

**Eşzamanlı senkron zorunluluğu:** device CSS ekleme/değişikliği → `js/devices.config.js` **+** `DeviceCssMap.php` (shared) ikisi birden güncellenir.

### 3.6 Giriş Noktası — `auth-bundled.css`

```css
/* auth-bundled.css — auth subdomain TEK giriş · gruplar halinde, sıra korunur */
/* Grup 1 — 01_Abstracts (token) */
@import url("./01_Abstracts/a-fonts-token.css?v=2.1.1");
@import url("./01_Abstracts/a-theme-config.css?v=2.1.1");
@import url("./01_Abstracts/a-light-glass-tokens.css?v=2.1.1");
@import url("./01_Abstracts/a-login-tokens.css?v=2.1.1");

/* Grup 2 — 02_Base (reset) */
@import url("./02_Base/b-base-core.css?v=2.1.1");

/* Grup 3 — 05_Pages (auth sayfaları) */
@import url("./05_Pages/p-select-gender.css?v=2.1.1");
@import url("./05_Pages/p-login-view.css?v=2.1.1");

/* Grup 4 — 08_Devices/auth (yalnızca cihaz davranışı) */
/* @import url("./08_Devices/d-auth-desktop.css?v=2.1.1");  ← DeviceCssMap tarafından dinamik */

/* Grup 5 — sayfa içi minik düzeltmeler (bileşen büyüyünse 04_Components'e taşınır) */
```

**Kural:** auth paketi `07_Vendors` **içermez** · import grubu **01 → 02 → 05 → 08** · cihaz varyantı ayrı dosyada (`d-auth-*`), ana pakette tekrar edilmez.

### 3.7 Tema Override Şablonu (ADR-044 — jeton seviyesinde)

```css
[data-theme="dark"]  { --bg-primary: #0b1120; --text-primary: #f1f5f9; }
[data-theme="glass"] { --bg-glass-blur: blur(16px); }
[data-accent="alt"]  { --color-primary: #ec4899; }
/* Tema YALNIZCA custom property değerini değiştirir; seçici ağacı değişmez. */
```

### 3.8 Utilities (06) vs Helpers (10) Ayrımı

| Katman | Ne konur | Örnek |
|--------|----------|-------|
| `06_Utilities/` | Tek amaclı, sayfa bağımsız **sıfır mantık** sınıfı — layout token'ı tüketir | `.is-hidden`, `.sr-only`, `.u-truncate` |
| `10_Helpers/` | Tekrarlanabilir **yardımcı desen/makro** — kendi içinde kural barındırır | `.u-ellipsis--2line`, odak halkası, motion-safe sarmalayıcı |

> Kural: iki katman da **token tüketir**, token **üretmez**. Token üretimi yalnız `01_Abstracts`.

### 3.9 BEM — Yapılır / Yapılmaz

| ✅ Yapılır | ❌ Yapılmaz |
|------------|-------------|
| `.player__progress` | `.playerProgress` |
| `.player--compact` | `.playerCompact` |
| `.player.is-loading` | `.player.loading` |
| `var(--space-md)` | `16px` |
| `div` yerine `.block__el` | `div > ul > li` |
| Tema: `[data-theme]` token override | `.dark .block { }` |
| Cihaz katmanı: import + davranış | Cihaz katmanı: yerleşim |

---

## 4. Kurallar

### 4.1 Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Hardcoded piksel/değer yasak — her değer `var(--...)` | Kod revert edilir |
| 2 | `!important` yasak (en fazla 3 istisna, gerekçesi yorumda) | Kod revert edilir |
| 3 | Inline `style=""` yasak (CSP nonce uyumsuz) | Kod revert edilir |
| 4 | Önişlemci (SCSS/LESS) yasak — saf CSS (ADR-001) | Bağlamlık artışı |
| 5 | Cihaz katmanı yalnızca **import + davranış**; layout `02_Base`/`03_Layout` | Katman ihlali |
| 6 | BEM adlandırma zorunlu (`.block__element--modifier`) | Kod revert edilir |
| 7 | WCAG: dokunma hedefi `--touch-min: 44px`; mockup okunmadan CSS yazılamaz | Erişilebilirlik hatası |
| 8 | **Component → 04, PHP sayfası → 05** ayrımı zorunlu (§3.1) | Dosya taşınır |
| 9 | **Token → 01_Abstracts, cihaz dosyasına göre ayrılır** (§3.3) | Token dosyaya geri alınır |
| 10 | `07_Vendors/` dosyaları elle düzenlenmez | Değişiklik revert |

### 4.2 Ek Kurallar

- **Katman sırası sabit:** `01 → 02 → 03 → 04 → 05 → 06 → 07 → 08 → 09 → 10 → 11`; sıra değiştirilemez.
- **Token tek kaynak:** yeni değer önce `01_Abstracts/` ilgili cihaz token dosyasına, sonra tüketilir.
- **Yerinde refactor:** dosya adı/yolu değiştirilirse tüm `@import` + `devices.config.js` + `DeviceCssMap.php` + PHP docblock yorumları güncellenir.
- **Çelişki kuralı:** vault (`AGENTS.md`/`CLAUDE.md`) ile disk çelişirse **disk kazanır** ve ⚠️ VERIFICATION REQUIRED işaretlenir.
- **notes.md:** kök `notes.md` içindeki CSS notları ilgili token dosyasına uygulanır ve uygulanan satır notta `✓` olarak imzalanır.

### 4.3 Sık Yapılan Hatalar

| # | Hata | Doğrusu |
|---|------|---------|
| 1 | Bileşen içinde sabit `16px` | `var(--space-md)` |
| 2 | `08_Devices/` içine yerleşim yazmak | Yerleşim `02_Base`/`03_Layout` |
| 3 | Component'i `05_Pages`'e yazmak | `04_Components/c-*.css` |
| 4 | Tüm cihaz token'ını tek dosyaya yığmak | §3.3 cihaz ayrımı |
| 5 | `main.css`'e import eklemek | `main.css` YOK — giriş `08_Devices/*` + `auth-bundled.css` |
| 6 | `d-auth-4k.css` uydurmak | Yok; `-4k-monitor` / `-4k-tv` |
| 7 | Mockupsuz CSS üretimi | Mockup Before Frontend |

---

## 5. Workflow

```
NOTES.MD OKU → MOCKUP OKU → KATMANI SEÇ → ŞABLONU KOPYALA
→ {{VARIABLE}} DOLDUR → GUARDRAIL #16 DOĞRULA → TARAYICI TESTİ → COMMIT
```

1. **NOTES/MOCKUP OKU:** kök `notes.md` + `.ai/ui-design/` ilgili görsel; okunamıyorsa DUR.
2. **KATMANI SEÇ:** §3.1 tablosu + §3.1 ayrım kuralı (component mi, sayfa mı?).
3. **ŞABLONU KOPYALA:** §3.3 token · §3.4 bileşen · §3.5 cihaz import · §3.6 auth paketi · §3.7 tema.
4. **`{{VARIABLE}}` DOLDUR:** `{{block}}`, `{{device}}`, `{{width}}`, `{{page}}`, `{{v}}`.
5. **GUARDRAIL #16:** §6 kontrol listesi + §4.1 (10 madde).
6. **TARAYICI TESTİ:** gerçek sayfada eleman/layout doğrulaması (repo kuralı §7.7).
7. **COMMIT:** subagent atmaz — orkestratöre aittir.

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan |
| 2 | Bölüm yapısı | §1–§7 numaralı, ≤3 başlık seviyesi |
| 3 | Placeholder | `{{VARIABLE}}` kalmadı |
| 4 | Guardrails | §4.1 10/10 |
| 5 | Katman | §3.1 sırası; dosya doğru katmanda |
| 6 | Ayrım | Component → 04 · PHP sayfası → 05 · token → 01 |
| 7 | Token dosyası | Yalnız `--token: değer`; seçici/kural yok; cihaz adı dosya adında |
| 8 | Cihaz dosyası | Import zinciri + davranış; layout yok |
| 9 | BEM | `.block__element--modifier` + `.is-*` |
| 10 | Halüsinasyon | Diskte olmayan katman/dosya adı iddia edilmedi |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Template registry | [[.templates/index]] | Envanter |
| Vault anayasası | [[../CLAUDE.md]] | Hard Guardrails |
| Agent registry | [[../../AGENTS.md]] | Routing: CSS → UI Designer |
| JS şablonu | [[js-template]] | Katman hizası (JS ↔ CSS) |
| Mockup indeksi | [[../../ui-design/01-mockup-index]] | Mockup Before Frontend |
| Disk kanıtı | `assets.coremusic.net/Css/` | Katman envanteri |
| Notlar | `notes.md` (repo kökü) | Uygulanacak CSS notları |
| İlgili ADR'ler | ADR-001 · ADR-018 · ADR-044 · ADR-045 | §1 tablosu |

---

**Template Version:** 3.0.1
**Last Updated:** 2026-10-03

---

> ⚠️ **GEÇMİŞ NOT (korunur):** `main.css` 2026-09-30'da silinmiştir — "main.css tek giriş" kuralı geçersizdir; güncel girişler `08_Devices/d-*.css` (cihaz) ve `auth-bundled.css` (auth subdomain). `07_Vendors/bootstrap*.css` diskte MEVCUT → vendor referansları geçerlidir.
