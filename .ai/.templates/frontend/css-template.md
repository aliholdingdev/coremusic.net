---
title: "CoreMusic — CSS/ITCSS Development Template"
type: template
category: frontend
date: 2026-10-06
updated: 2026-10-06
version: 4.0.0
status: active
authority: reference
---

# CoreMusic — CSS/ITCSS Development Template

**Teknoloji:** ITCSS katman sırası, BEM adlandırma, CSS custom property (design token) · **Katman:** L3 (sunum) · **Sorumlu Agent:** UI Designer

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]] · [[js-template]]

> **v4.0.0 (2026-10-06) — sıfırdan yeniden yazım.** Expert 5 bağlayıcı karar (C1–C5 + katman kuralı) uygulandı:
> BASE token `a-layout-tokens-1024.css` · `--touch-min: 48px` (44px Guardrail #7'den silindi) ·
> `02_Base` önekleri `b- / l- / page-` (`p-` münhasır `05_Pages`) · MUTLAK dosya sayısı şablonda YAZILMAZ ·
> helper iskeleti `!important`siz · yeni `--token` yalnız `01_Abstracts/`.

---

## Purpose (Amaç)

Bu şablon, CoreMusic stylesheet geliştirme standardını (11 katman + kök giriş, BEM, token tabanlı değer
kullanımı, cihaz token ayrımı) ve katman şablonlarının dizinini tanımlar. **Guardrail #16:** yeni `.css`
dosyası bu şablondan ve ilgili katman şablonundan üretilmek ZORUNLUDUR.

| Karar | Karşılığı |
|-------|-----------|
| Vanilla JS + ITCSS, framework/önişlemci yasak | ADR-001 → §4.1 #1-#4 |
| Footer player (vaporwave) | ADR-018 → ilgili bileşen şablonu |
| Cinsiyet bazlı dinamik tema | ADR-044 → §3.7 |
| Multi-domain görünüm modu | ADR-045 → §3.5 |
| WCAG 2.2 AA | §4.1 #7 — `--touch-min: 48px` (C2) |

---

## Location (Konum)

`assets.coremusic.net/Css/` — 11 katman dizini + kök `auth-bundled.css`. Katman sırası **01 → 11**;
alt katman üst katmanı geçersiz kılmaz.

## Responsibility (Sorumluluk)

| Alan | Sorumlu |
|------|---------|
| Yeni `.css` üretimi | UI Designer |
| WCAG/responsive bağımsız doğrulama | QA Engineer (`Css/AGENTS.md` §3.1 — hedef ≥48px) |
| Inline style denetimi (PHP) | Backend Architect |
| `07_Vendors/` salt-okunur | herkes — yalnız sürüm senkronu |

**Ön koşul:** Mockup Before Frontend — `.ai/ui-design/` görseli okunmadan CSS yazılamaz; okunamıyorsa DUR.

---

## Allowed (İzinli)

- Katman dizini + bu şablonun §3.2 önek/dosya kalıbına uyan dosya oluşturma.
- `var(--token)` tüketimi; ham hex/px **yalnız** `01_Abstracts/`.
- `08_Devices/` içinde mevcut token'ın `:root` değer override'ı (§4.1 #9 — C6 katman kuralı).
- `@import ?v={{v}}` cache-busting; `?v` değeri tüm zincirde aynı.

## Forbidden (Yasak)

| # | Yasak | Kaynak |
|---|-------|--------|
| 1 | `innerHTML` / framework (React, Vue, Angular) | ADR-001 |
| 2 | `var` (JS), hash routing (`history.pushState` — ADR-083) | kök AGENTS.md |
| 3 | Emoji (yalnız PNG) | kök AGENTS.md |
| 4 | SCSS/LESS/preprocessor | `Css/CLAUDE.md` §4.1 #4 |
| 5 | Inline `style=""` (CSP) | `Css/CLAUDE.md` §4.1 #3 |
| 6 | Hardcoded hex/px (01 dışında) | `Css/CLAUDE.md` §4.1 #1 |
| 7 | `08_Devices/` içinde YERLEŞİM kuralı | `Css/CLAUDE.md` §4.1 #5 |
| 8 | `08_Devices/` içinde YENİ token adı (§4.1 #9 — C6) | `.ai/CLAUDE.md:537` · css-template §4.1 #9 |
| 9 | Şablonda MUTLAK dosya sayısı/tutarlı envanter iddiası (C4) | Expert kararı C4 |
| 10 | `main.css` · `a-layout-tokens.css` · `d-auth-4k.css` uydurmak (§3.3 YOK listesi) | disk kanıtı 2026-10-06 |

---

## Dependencies (Bağımlılıklar)

| Bağımlılık | Tür | Not |
|------------|-----|-----|
| `.ai/ui-design/` (mockup + tokens) | okunur | Figma SSOT; okunamadan CSS yazılmaz |
| `notes.md` (repo kökü) | okunur | CSS notu ilgili token dosyasına, satır `✓` imzalanır |
| `js/devices.config.js` + `shared/.../DeviceCssMap.php` | senkron | cihaz CSS ekleme/değişikliği → ikisi birden |
| `Css/CLAUDE.md` §4.1 | bağlayıcı | 10 hard guardrail (ihlal = revert) |
| Katman şablonları (§3.4) | üretilen | Guardrail #16 — şablonsuz dosya üretilmez |

**Ölü referanslar (atıf YAPILMAZ — diskte YOK, 2026-10-06 doğrulandı):**
`.ai/.templates/css-structure.md` · `css-token.md` · `css-component.md` · `css-page.md` · `css-imports.md`
→ kök `AGENTS.md` §10'daki bu satırlar ölüdür (rapor: §R).

---

## Import Rules (Import Kuralları)

1. **Sabit sıra:** `01 → 02 → 03 → 04 → 05 → 06 → 07 → 08 → 09 → 10 → 11`; sıra değiştirilemez.
2. Giriş noktaları **yalnız** `08_Devices/d-*.css` (ana subdomain) ve `auth-bundled.css` (auth).
3. `main.css` YOKTUR (2026-09-30 silindi — uydurulmaz).
4. Her `@import` `?v={{v}}` taşır; `{{v}}` zincir genelinde tek değer.
5. `07_Vendors` yalnız cihaz dosyalarında import edilir; auth paketinde **yok** (§3.6).

**Cihaz import iskeleti (C5 — `10_Helpers` satırı eklendi):**

```css
/* 08_Devices/d-{{device}}.css — 1) IMPORT  2) DAVRANIŞ */
@import url("../01_Abstracts/a-layout-tokens-{{width}}.css?v={{v}}");
@import url("../02_Base/b-base-core.css?v={{v}}");
@import url("../03_Layout/_header.css?v={{v}}");
@import url("../04_Components/c-{{block}}.css?v={{v}}");
@import url("../05_Pages/p-{{page}}.css?v={{v}}");
@import url("../06_Utilities/u-helpers-utility.css?v={{v}}");
@import url("../09_ViewModes/v-{{mode}}.css?v={{v}}");
@import url("../10_Helpers/h-ellipsis.css?v={{v}}");   /* C5 — 10_Helpers satırı */
```

---

## Naming Rules (Adlandırma Kuralları)

| Katman | Önek | Dosya kalıbı |
|--------|------|--------------|
| 01 | `a-` | `a-{konu}-token(s).css` · `a-layout-tokens-{width}.css` |
| 02 | `b-` / `l-` / `page-` | `b-base-core.css` · `l-main-structural.css` · `page-layout.css` — **`p-` 02'de YASAK** (C3) |
| 03 | `_` | `_header.css` |
| 04 | `c-` (yeni) / `_` (mevcut korunur) | `c-{bileşen}.css` |
| 05 | `p-` (münhasır) / `_` | `p-{sayfa}.css` · `_home.css` (çatı) |
| 06 | `u-` | `u-{ad}.css` |
| 07 | `v-` (kendi sarmalayıcı) | `v-bootstrap-lib.css` + salt-okunur `bootstrap*` |
| 08 | `d-` / `d-auth-` | `d-{cihaz}.css` · `d-auth-{cihaz}.css` |
| 09 | `v-` | `v-{mod}.css` |
| 10 | `h-` | `h-{ad}.css` |
| 11 | `o-` | `oauth.css` (mevcut ad korunur) |

**BEM:** `.block__element--modifier` + durum `.block.is-*` (§3.9).

---

## Device Rules (Cihaz Kuralları)

1. `08_Devices/` = **import zinciri + davranış**; yerleşim yazımı yasak (`Css/CLAUDE.md` §4.1 #5).
2. Cihaz token'ı ayrı `a-layout-tokens-{width}.css` (§3.3 + [[css-device-token-template]]).
3. Cihaz dosyası **yalnız mevcut token'ın `:root` değerini override eder** — yeni token adı yasak (C6).
4. Yeni cihaz → `devices.config.js` **+** `DeviceCssMap.php` ikisi birden senkron.
5. `d-auth-4k.css` YOKTUR (yalnız `-4k-monitor` / `-4k-tv`) — uydurulmaz.

---

## Responsive Rules (Responsive Kuralları)

- Breakpoint token'ları `01_Abstracts/a-breakpoint-tokens.css` (Figma SSOT — değerleri dosyadan okunur,
  şablonda sabitlenmez). CSS custom property `@media` içinde okunamaz → medya sorgusu genişlikleri
  breakpoint token değeriyle **eşleşmeli**; çelişki raporlanır.
- Sıralama: base (`a-layout-tokens-1024.css`, medyasız `:root`) → mobile → tablet → 1920 → 3540 → 3840.
  Daha geniş kırılım sonra gelir, dar olanı ezer.
- 4K'da ortalama (`margin-inline: auto`) + fallback zorunlu → `08_Devices` davranış alanı.
- Token medya sorgusu `01_Abstracts`'te; bileşen/sayfa medyası yalnız token tüketimi (yeni breakpoint
  değeri tanımlanmaz).

---

## Token Rules (Token Kuralları)

1. **Yeni `--token` yalnız `01_Abstracts/`** (C6 katman kuralı · `Css/CLAUDE.md` §4.1 #9).
2. `08_Devices/` = mevcut token'ın `:root` değer override'ı; **yeni token adı yazımı yasak** (C6).
3. `06`/`10` token tüketir, üretmez.
4. Tema override yalnız `[data-theme]`/`[data-accent]` ile ve **jeton seviyesinde** (ADR-044, §3.7).
5. Figma'da olmayan token **uydurulmaz** → `⚠️ VERIFICATION REQUIRED`.
6. `--touch-min` taban değeri **48px** (C2 — brain.md:808-809 Phone/Embedded ≥48; `Css/AGENTS.md` §3.1
   ≥48px; 44px Guardrail #7'den silindi). Cihaz override: 4K geniş ≥24 (brain.md:810-811) —
   değerler ilgili `a-layout-tokens-*.css` ve `d-*.css` disk dosyalarından okunur.

---

## Mimari — Katman Tablosu (§3)

### 3.1 Katman Sırası (dosya sayısı YAZILMAZ — C4)

> Sayı/envanter yalnız tarihli `Css/CONTEXT.md §3.1`'de tutulur. Şablon = önek + dosya kalıbı + YOK listesi.
> "Örnek dosya" sütunu **2026-10-06 ölçümü** etiketlidir; envanter iddiası değildir.

| # | Katman | Sorumluluk | Önek | Örnek dosya (2026-10-06 ölçümü) |
|---|--------|-----------|------|--------------------------------|
| 01 | `01_Abstracts/` | **Yalnız token** — seçici/kural YAZILMAZ | `a-` | `a-layout-tokens-1024.css`, `a-colors-token.css`, `a-breakpoint-tokens.css` |
| 02 | `02_Base/` | Bare HTML reset, base giriş iskeleti | `b-`/`l-`/`page-` | `b-base-core.css`, `l-main-structural.css`, `page-layout.css` |
| 03 | `03_Layout/` | Sayfa düzeni: header, footer, sidebar, grid | `_` | `_header.css`, `_footer.css`, `_sidebar.css`, `_widget-grid.css` |
| 04 | `04_Components/` | BEM bileşenleri (Figma component karşılığı) | `c-`/`_` | `c-buttons.css`, `c-forms.css`, `_home-components.css` |
| 05 | `05_Pages/` | **PHP sayfasına özel** stiller | `p-`/`_` | `p-login-view.css`, `_home.css`, `_player.css` |
| 06 | `06_Utilities/` | Tek amaclı sıfır mantık sınıf | `u-` | `u-helpers-utility.css` |
| 07 | `07_Vendors/` | 3. taraf (Bootstrap ailesi) — **DÜZENLENMEZ** | `v-` | `v-bootstrap-lib.css` + `bootstrap*` (+ `.map`) |
| 08 | `08_Devices/` | Cihaz import zinciri + davranış override | `d-`/`d-auth-` | `d-phone.css`, `d-auth-phone.css` |
| 09 | `09_ViewModes/` | Görünüm modu override | `v-` | `v-home.css`, `v-pro.css`, `v-studio.css`, `v-car.css` |
| 10 | `10_Helpers/` | Tekrarlanabilir yardımcı desen/makro | `h-` | `h-ellipsis.css` (iskelet — içerik bekleniyor) |
| 11 | `11_OAuth/` | OAuth/login akış stilleri | `o-` | `oauth.css` |
| kök | `auth-bundled.css` | Auth subdomain **tek giriş** (§3.6) | — | `auth-bundled.css` |

**YOK olanlar (uydurulmaz — 2026-10-06 disk doğrulaması):**

| Dosya | Durum |
|-------|-------|
| `main.css` | YOK — 2026-09-30 silindi; giriş `08_Devices/d-*.css` + `auth-bundled.css` |
| `a-layout-tokens.css` | YOK — git'te hiç track edilmedi; BASE = `a-layout-tokens-1024.css` (C1) |
| `d-auth-4k.css` | YOK — yalnız `d-auth-4k-monitor` / `d-auth-4k-tv` |
| `Css copy 2/` | yedektir, referans alınmaz |

**Ayrım kuralı:**

| İçerik | Katman |
|--------|--------|
| PHP sayfasına özgü | `05_Pages/` |
| ≥2 sayfada tekrar eden görsel parça | `04_Components/` |
| Grid/header/footer/sidebar düzeni | `03_Layout/` |
| Sadece `--token: değer` | `01_Abstracts/` |
| Cihaz davranışı / kırılım uyarlaması | `08_Devices/` |
| Tek satır işlev | `06_Utilities/` |
| Kural barındıran tekrarlı desen | `10_Helpers/` |

### 3.2 Katman Şablon Dizini (Guardrail #16)

| Katman | Şablon |
|--------|--------|
| (ana) | **bu dosya** [[css-template]] |
| 01 konu token | [[css-abstracts-token-template]] |
| 01 cihaz token (layout ailesi) | [[css-device-token-template]] |
| 02 | [[css-base-template]] |
| 03 | [[css-layout-template]] |
| 04 | [[css-component-template]] |
| 05 | [[css-page-template]] |
| 06 | [[css-utility-template]] |
| 07 | [[css-vendor-template]] |
| 08 normal | [[css-device-template]] |
| 08 auth | [[css-auth-device-template]] |
| 09 | [[css-viewmode-template]] |
| 10 | [[css-helper-template]] |
| 11 | [[css-oauth-template]] |

### 3.3 Cihaz Token Ailesi (`a-layout-tokens-*`)

```
01_Abstracts/
├── a-layout-tokens-1024.css     # BASE — medyasız :root (C1; eski a-layout-tokens.css YOK)
├── a-layout-tokens-mobile.css   # phone
├── a-layout-tokens-tablet.css   # tablet
├── a-layout-tokens-1920.css     # wide desktop
├── a-layout-tokens-3540.css     # 4K monitor
└── a-layout-tokens-3840.css     # 4K TV
```

İskelet → [[css-device-token-template]] (bu dosya kalıp yazmaz; orası SSOT).

### 3.4 Bileşen İskeleti (özet)

Tam iskelet → [[css-component-template]]. Özet:

```css
.{{block}} { min-height: var(--touch-min); gap: var(--space-md); background: var(--bg-secondary); }
.{{block}}__button { min-width: var(--touch-min); min-height: var(--touch-min); }
.{{block}}--compact { gap: var(--space-xs); }
.{{block}}.is-active { box-shadow: var(--shadow-md); }
```

### 3.5 Cihaz Import Zinciri (`08_Devices/`)

> Görev: (1) import et, (2) davranışı override et. Yerleşim `02_Base`/`03_Layout`'ta kalır.
> İskelet: § "Import Rules" (C5 — `10_Helpers` satırı dahil). Cihaz listesi: [[css-device-template]].
> `v-*.css` ayrıca `DeviceRenderer::headLinks()` ile ayrı `<link>` basılır (çift yükleme — ADR bekliyor;
> ⚠️ VERIFICATION REQUIRED, bu şablonun scope'u dışındadır).

### 3.6 Giriş Noktası — `auth-bundled.css`

Grup sırası `01 → 02 → 05 → 08` · `07_Vendors` **içermez** · cihaz varyantı ayrı `d-auth-*.css`.
Tam iskelet → [[css-auth-device-template]].

### 3.7 Tema Override (ADR-044 — jeton seviyesinde)

```css
[data-theme="dark"]  { --bg-primary: #0b1120; --text-primary: #f1f5f9; }
[data-accent="alt"]  { --color-primary: #ec4899; }
/* Tema YALNIZCA custom property değerini değiştirir; seçici ağacı değişmez. */
```

### 3.8 Utilities (06) vs Helpers (10)

| Katman | Ne konur | Örnek |
|--------|----------|-------|
| `06_Utilities/` | Tek amaclı, **sıfır mantık** sınıf | `.is-hidden`, `.u-sr-only`, `.u-truncate` |
| `10_Helpers/` | Tekrarlanabilir desen — **kural barındırır**, `!important`siz | `.h-ellipsis-2`, `.h-focus-ring`, `.h-skip-link` |

> İkisi de token **tüketir**, token **üretmez**.

### 3.9 BEM — Yapılır / Yapılmaz

| ✅ Yapılır | ❌ Yapılmaz |
|------------|-------------|
| `.player__progress` | `.playerProgress` |
| `.player--compact` | `.playerCompact` |
| `.player.is-loading` | `.player.loading` |
| `var(--space-md)` | `16px` (01 dışında) |
| `[data-theme]` token override | `.dark .block { }` |
| Cihaz: import + davranış + token değer override | Cihaz: yerleşim · yeni token adı |

---

## Kurallar (§4)

### 4.1 Hard Guardrails — `Css/CLAUDE.md` §4.1 (10/10; ihlal = revert)

| # | Kural | Not (v4.0.0) |
|---|-------|--------------|
| 1 | Hardcoded piksel/değer yasak — her değer `var(--...)` (01 hariç) | — |
| 2 | `!important` yasak — **en fazla 3 istisna**, gerekçesi yorumda | iskelet toplamı 2/3 (C5): 06 `1` + 04 `1` |
| 3 | Inline `style=""` yasak (CSP) | — |
| 4 | SCSS/LESS yasak — saf CSS (ADR-001) | — |
| 5 | Cihaz katmanı yalnız **import + davranış**; layout `02_Base`/`03_Layout` | — |
| 6 | BEM adlandırma zorunlu | — |
| 7 | WCAG 2.2 AA: dokunma hedefi **`--touch-min: 48px`**; mockup okunmadan CSS yok | **C2 — 44px silindi** |
| 8 | Component → `04`, PHP sayfa → `05` | — |
| 9 | **Token → `01_Abstracts`**; `08_Devices` yalnız mevcut token `:root` value override'ı — **yeni token adı yasak** | **C6 katman kuralı** |
| 10 | `07_Vendors/` salt-okunur | — |

### 4.2 Ek Kurallar

- **Katman sırası sabit:** `01 → 11`; sıra değiştirilemez.
- **Yerinde refactor:** dosya adı/yolu değişirse tüm `@import` + `devices.config.js` + `DeviceCssMap.php`
  + PHP docblock güncellenir.
- **Çelişki:** vault ↔ disk çelişirse **disk kazanır** + `⚠️ VERIFICATION REQUIRED`.
- **`notes.md`:** CSS notu ilgili token dosyasına uygulanır, satır `✓` imzalanır.
- **Şablonda sayı yok (C4):** envanter/count iddiası yalnız tarihli `Css/CONTEXT.md §3.1`'de.

### 4.3 Sık Yapılan Hatalar

| # | Hata | Doğrusu |
|---|------|---------|
| 1 | Bileşen içinde sabit `16px` | `var(--space-md)` |
| 2 | `08_Devices/` içine yerleşim yazmak | Yerleşim `02_Base`/`03_Layout` |
| 3 | Component'i `05_Pages`'e yazmak | `04_Components/c-*.css` |
| 4 | Tüm cihaz token'ını tek dosyaya yığmak | `a-layout-tokens-{width}.css` ayrımı |
| 5 | `main.css`'e import eklemek | `main.css` YOK |
| 6 | `d-auth-4k.css` uydurmak | Yok; `-4k-monitor` / `-4k-tv` |
| 7 | Mockupsuz CSS üretimi | Mockup Before Frontend |
| 8 | `08_Devices/` içine yeni token adı yazmak | C6: value override — yeni ad `01_Abstracts` |
| 9 | `02_Base` içinde `p-` öneki | `p-` münhasır `05_Pages` (C3) |
| 10 | Şablonda dosya sayısı iddia etmek | C4 — sayı yok, ölçüm etiketi var |

---

## Workflow (§5)

```
NOTES.MD OKU → MOCKUP OKU → KATMANI SEÇ → KATMAN ŞABLONUNU KOPYALA
→ {{VARIABLE}} DOLDUR → GUARDRAIL #16 + §4.1 → TARAYICI TESTİ → COMMIT (orchestrator)
```

1. **NOTES/MOCKUP OKU:** kök `notes.md` + `.ai/ui-design/` görsel; okunamıyorsa DUR.
2. **KATMANI SEÇ:** §3.1 ayrım tablosu (component mi / sayfa mı / token mı / cihaz mı?).
3. **ŞABLONU KOPYALA:** §3.2 dizini — ilgili katman şablonu (Guardrail #16).
4. **`{{VARIABLE}}` DOLDUR:** `{{block}}`, `{{device}}`, `{{width}}`, `{{page}}`, `{{mode}}`, `{{v}}`.
5. **DOĞRULA:** §6 kontrol listesi + `Css/CLAUDE.md` §4.1 (10 madde).
6. **TARAYICI TESTİ:** gerçek sayfada eleman/layout doğrulaması (repo kuralı §7.7).
7. **COMMIT:** subagent atmaz — orkestratöre aittir.

---

## Validation (§6 Doğrulama)

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 8 alan (`title, type, category, date, updated, version, status, authority`) |
| 2 | Contract alanları | Purpose/Location/Responsibility/Allowed/Forbidden/Dependencies/Import Rules/Naming Rules/Device Rules/Responsive Rules/Token Rules/Validation/Example Structure mevcut |
| 3 | Placeholder | `{{VARIABLE}}` kalmadı |
| 4 | Guardrails | §4.1 10/10 (`Css/CLAUDE.md` ile birebir) |
| 5 | Katman | Dosya doğru katmanda; §3.1 sıra |
| 6 | Ayrım | Component → 04 · PHP sayfa → 05 · token → 01 |
| 7 | Token dosyası | Yalnız `--token: değer`; seçici/kural yok |
| 8 | Cihaz dosyası | Import zinciri + davranış + mevcut token value override; **yeni token adı yok** |
| 9 | BEM | `.block__element--modifier` + `.is-*` |
| 10 | Touch | `--touch-min` ≥ 48px taban (C2) |
| 11 | Halüsinasyon | Diskte olmayan katman/dosya adı iddia edilmedi; sayı yok (C4) |
| 12 | `!important` | İskelet ≤2; kod ≤3 (gerekçeli) |

---

## Example Structure (Örnek Yapı)

```
assets.coremusic.net/Css/
├── auth-bundled.css          # auth subdomain tek giriş (§3.6)
├── 01_Abstracts/             # a-*  (token — C6: tek token üretim katmanı)
├── 02_Base/                  # b- / l- / page-
├── 03_Layout/                # _*
├── 04_Components/            # c-* / _*
├── 05_Pages/                 # p-* / _*
├── 06_Utilities/             # u-*
├── 07_Vendors/               # v-* + bootstrap* (salt okunur)
├── 08_Devices/               # d-* / d-auth-*  (import + davranış)
├── 09_ViewModes/             # v-{mod}
├── 10_Helpers/               # h-*
└── 11_OAuth/                 # oauth.css
```

---

## References (§7)

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Template registry | [[.templates/index]] | Envanter |
| Vault anayasası | [[../CLAUDE.md]] | 16 Hard Guardrails |
| Agent registry | [[../../AGENTS.md]] | Routing: CSS → UI Designer |
| CSS guardrail özeti | `assets.coremusic.net/Css/CLAUDE.md` §4.1 | 10 hard guardrail (bağlayıcı) |
| CSS rol tablosu | `assets.coremusic.net/Css/AGENTS.md` §3.1 | QA ≥48px kapısı |
| JS şablonu | [[js-template]] | Katman hizası |
| Mockup indeksi | `../../ui-design/00-mockup-index.md` | Mockup Before Frontend |
| Disk kanıtı | `assets.coremusic.net/Css/` | Katman doğrulaması (2026-10-06) |
| Notlar | `notes.md` (repo kökü) | Uygulanacak CSS notları |
| ADR'ler | ADR-001 · ADR-018 · ADR-044 · ADR-045 · ADR-083 | §1 tablosu |

---

**Template Version:** 4.0.0
**Last Updated:** 2026-10-06

---

**Rapor (§R — bu dosya DOKUNULMAYAN ikincil düzeltmeleri işaretler):**

| Hedef | Sorun |
|-------|-------|
| kök `AGENTS.md` §10 | `css-structure/token/component/page/imports.md` referansları diskte YOK (ölü) |
| `.ai/CLAUDE.md` frontend okuma tablosu (satır 6, :537) | "css-template.md (**v3.0.0**) + `.ai/.templates/css-structure.md`" → sürüm bayat (bu dosya v4.0.0) + `css-structure.md` diskte YOK (ölü atıf) |
| `assets.coremusic.net/Css/CLAUDE.md` §3.2 | "`a-layout-tokens.css` base" → diskte YOK (BASE: `-1024`) |
| `assets.coremusic.net/Css/CONTEXT.md` §3.1 | eski envanter (01=20, 04=6, 05=4) ↔ 2026-10-06 ölçümü 19/15/12 |
| `assets.coremusic.net/AGENTS.md` §2 | "04=13" ↔ disk 15 · "01=20 token" ↔ disk 19 (çelişki) |
| `a-design-tokens.css` L211/221/232 · `_home-layout.css` L386/405 | `--touch-min: 44px` — C2'ye aykırı |
| `08_Devices/d-auth-*.css` | `:root` içinde `--lgn-panel-w`/`--lgn-font-size` — `01_Abstracts`'te tanım YOK (C6 ihlali) |
| `assets.coremusic.net/Css/CLAUDE.md` §4.1 #2 | kod geneli `!important` 47+ satır ihlal (vendor hariç; auth device minified çoklu → kesin sayı UNKNOWN) |
