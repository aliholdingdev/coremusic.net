---
title: "CoreMusic — CSS/ITCSS Development Template"
type: template
category: frontend
date: 2026-09-06
updated: 2026-09-23
version: 2.0.0
status: active
authority: SSOT
---

# CoreMusic — CSS/ITCSS Development Template

**Teknoloji:** ITCSS katman sırası, BEM adlandırma, CSS custom property (design token) · **Katman:** L3 (sunum) · **Sorumlu Agent:** UI Designer

**Zorunlu Bağlantılar:** [[.templates/index]] · [[../CLAUDE.md]] · [[../../AGENTS.md]] · [[js-template]] · [[../../ui-design/01-mockup-index]]

---

## 1. Amaç

Bu şablon, CoreMusic stylesheet geliştirme standardını (ITCSS katman sırası, BEM, token tabanlı değer kullanımı) ve kod iskeletlerini tanımlar. **Guardrail #16:** yeni `.css` dosyası bu şablondan üretilmek ZORUNLUDUR.

| Karar | ADR | Şablona gömülü karşılığı |
|-------|-----|--------------------------|
| Vanilla JS + ITCSS, framework/önişlemci yasak | ADR-001 | §4 #1-#2 — saf CSS + katman sırası |
| Footer player (vaporwave) | ADR-018 | §3.4 bileşen iskeleti (`{{BLOCK}}__footer-player`) |
| Cinsiyet bazlı dinamik tema | ADR-044 | §3.6 tema jetonu override |
| Multi-domain görünüm modu | ADR-045 | §3.5 cihaz/domain override |
| Erişilebilirlik (WCAG 2.2 AA) | — | §4 #7 — `--touch-min: 44px` |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `assets.coremusic.net/Css/**/*.css` — token, taban, düzen, bileşen, sayfa, yardımcı, cihaz | JavaScript modülleri → `[[js-template]]` |
| Design token (custom property) tanımı ve override | Mockup görselleri → `.ai/ui-design/` (okunur, üretilmez) |
| BEM adlandırma + cihaz davranış uyarlaması | Vendors kütüphaneleri (Bootstrap) — düzenlenmez, yalnızca güncellenir |
| `main.css` ve `auth-bundled.css` giriş noktaları | PHP/HTML şablonları (inline style yasak → §4 #3) |

- **Kullananlar:** UI Designer (birincil), QA Engineer (WCAG/responsive doğrulama), Backend Architect (inline style denetimi).
- **Ön koşul:** Mockup Before Frontend — `.ai/ui-design/` görseli okunmadan CSS yazılamaz; okunamıyorsa DUR.

---

## 3. Mimari

Şablonun gövdesi: disk kanıtıyla doğrulanmış katman yapısı ve dört kod iskeleti. Katman/ dosya sayıları glob çıktısına dayanır; glob'da çıkmayan katmanlar mevcut gibi sunulmaz.

### 3.1 Disk Kanıtı — Katman Yapısı (`assets.coremusic.net/Css/`)

| Katman | Dosya sayısı | Örnek dosya adları | Sorumluluk |
|--------|--------------|--------------------|------------|
| `01_Abstracts/` | 12 | `a-design-tokens.css`, `a-colors-token.css`, `a-fonts-token.css`, `a-breakpoint-tokens.css`, `a-scale-hybrid.css`, `a-theme-config.css`, `a-login-tokens.css`, `a-widget-grid-tokens.css`, `a-layout-tokens-{1024,1920,3540,3840}.css` | Tasarım jetonu (custom property) |
| `02_Base/` | 2 | `b-base-core.css`, `page-layout.css` | Bare HTML + sayfa iskeleti |
| `03_Layout/` | 3 | `_header.css`, `_footer.css`, `_sidebar.css` | Düzen blokları |
| `04_Components/` | 7 | `c-home-song-btn.css`, `c-footer-seek.css`, `c-footer-volume.css`, `c-scrollbar-accent.css`, `_player-info.css`, `_welcome-banner.css`, `_widget-grid.css` | BEM bileşenleri |
| `05_Pages/` | 4 | `_home.css`, `_home-components.css`, `_home-inline.css`, `p-login-view.css` | Sayfaya özel stiller |
| `06_Utilities/` | 1 | `u-helpers-utility.css` | Yardımcı sınıflar |
| `07_Vendors/` | Bootstrap ailesi | `bootstrap.css`, `bootstrap.min.css`, `.rtl` sürümleri + `.map` | 3. taraf (düzenlenmez) |
| `08_Devices/` | 14 | `d-embedded.css`, `d-desktop.css`, `d-laptop.css`, `d-phone.css`, `d-tablet.css`, `d-4k.css`, `d-4k-tv.css` + `d-auth-{embedded,desktop,laptop,phone,tablet,4k-monitor,4k-tv}.css` | Cihaz davranış uyarlaması |
| Kök | 2 | `main.css`, `auth-bundled.css` | Giriş noktaları |

**Düzeltme (önceki şablon → disk kanıtı):** `01_Settings/`, `02_Tools/`, `04_Elements/`, `05_Objects/`, `09_Themes/` adları ve `d-mobile.css` dosyası glob'da YOKTUR; bu adlar kullanılmaz. `assets.coremusic.net/AGENTS.md` "9 katman (09_ViewModes, 11_OAuth)" iddia eder — glob çıktısında görünmez → ⚠️ VERIFICATION REQUIRED (glob kanıtı esastır).

### 3.2 Token Şablonu (`01_Abstracts/`)

```css
/* 01_Abstracts/a-design-tokens.css — tek doğruluk kaynağı: bu dosya */
:root {
    /* === RENK === */
    --color-primary: #6366f1;
    --color-primary-hover: #4f46e5;
    --color-accent: #06b6d4;
    --color-success: #10b981;
    --color-warning: #f59e0b;
    --color-danger: #ef4444;

    /* === YÜZEY === */
    --bg-primary: #0f172a;
    --bg-secondary: #1e293b;
    --bg-tertiary: #334155;
    --bg-glass: rgba(15, 23, 42, 0.8);
    --bg-glass-blur: blur(12px);

    /* === METİN === */
    --text-primary: #f8fafc;
    --text-secondary: #94a3b8;
    --text-muted: #64748b;

    /* === BOŞLUK (4px ızgara) === */
    --space-xs: 4px;
    --space-sm: 8px;
    --space-md: 16px;
    --space-lg: 24px;
    --space-xl: 32px;
    --space-2xl: 48px;

    /* === ÖLÇÜ === */
    --header-h: 60px;
    --footer-h: 90px;
    --sidebar-w: 280px;
    --content-h: calc(100vh - var(--header-h) - var(--footer-h));

    /* === KÖŞE === */
    --radius-sm: 4px;
    --radius-md: 8px;
    --radius-lg: 12px;
    --radius-full: 9999px;

    /* === GÖLGE === */
    --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.3);
    --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.3);
    --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.3);

    /* === KATMAN SIRASI (z-index) === */
    --z-dropdown: 100;
    --z-sticky: 200;
    --z-modal: 300;
    --z-toast: 400;

    /* === GEÇİŞ === */
    --transition-fast: 150ms ease;
    --transition-normal: 250ms ease;
    --transition-slow: 350ms ease;

    /* === TİPOGRAFİ === */
    --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    --font-mono: 'JetBrains Mono', 'Fira Code', monospace;
    --font-size-sm: 0.875rem;
    --font-size-md: 1rem;
    --font-size-lg: 1.125rem;
    --font-size-xl: 1.25rem;

    /* === ERİŞİLEBİLİRLİK (WCAG 2.2 AA) === */
    --touch-min: 44px;
}
```

### 3.3 Bileşen Şablonu (BEM — `04_Components/c-{{block}}.css`)

```css
/* 04_Components/c-{{block}}.css */
/* BEM: .block__element--modifier + durum: .block.is-... */

.{{block}} {
    display: flex;
    align-items: center;
    gap: var(--space-md);
    min-height: var(--touch-min);
    padding: var(--space-sm) var(--space-md);
    background: var(--bg-secondary);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-sm);
    transition: box-shadow var(--transition-fast);
}

.{{block}}__title {
    margin: 0;
    font-size: var(--font-size-md);
    color: var(--text-primary);
}

.{{block}}__controls {
    display: flex;
    align-items: center;
    gap: var(--space-sm);
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

.{{block}}__button:hover {
    background: var(--color-primary);
}

.{{block}}--compact {
    gap: var(--space-xs);
    padding: var(--space-xs) var(--space-sm);
}

.{{block}}.is-active {
    box-shadow: var(--shadow-md);
}

.{{block}}.is-loading .{{block}}__controls {
    opacity: 0.5;
    pointer-events: none;
}
```

### 3.4 Cihaz Kırılım Şablonu (token override — `01_Abstracts/a-breakpoint-tokens.css`)

```css
/* Yalnızca token DEĞERİ değişir; kural yazımı bileşen dosyasında kalmaz. */

/* === EMBEDDED (1024x600 — Raspberry Pi 5) === */
@media (max-width: 1024px) {
    :root {
        --header-h: 60px;
        --footer-h: 90px;
        --sidebar-w: 0px;
        --space-md: 12px;
    }
}

/* === TABLET === */
@media (min-width: 768px) and (max-width: 1024px) {
    :root {
        --sidebar-w: 0px;
    }
}

/* === PHONE === */
@media (max-width: 767px) {
    :root {
        --footer-h: 80px;
        --sidebar-w: 0px;
        --space-lg: 16px;
    }
}

/* === DESKTOP === */
@media (min-width: 1920px) {
    :root {
        --header-h: 70px;
        --footer-h: 104px;
        --sidebar-w: 300px;
    }
}

/* === 4K === */
@media (min-width: 3840px) {
    :root {
        --header-h: 80px;
        --footer-h: 120px;
        --sidebar-w: 350px;
        --font-size-md: 1.25rem;
    }
}
```

### 3.5 Cihaz Davranış Şablonu (`08_Devices/d-{{device}}.css`)

```css
/* 08_Devices/d-{{device}}.css
 * KURAL: yalnızca davranış/davranış uyarlaması (görünürlük, hedef boyut,
 * kırılma noktası etkileşimi). YERLEŞİM (layout) KURALLARI BURAYA YAZILMAZ —
 * yerleşim 02_Base/ ve 03_Layout/ katmanlarında kalır (§4 #5). */

@media (max-width: 767px) {
    .{{block}}__optional {
        display: none;
    }

    .{{block}} {
        min-height: var(--touch-min);
        padding: var(--space-xs);
    }

    .{{block}}__scroll {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
}

@media (max-width: 1024px) and (max-height: 601px) {
    /* Raspberry Pi 5 1024x600: alt bilgi şeridi sıkışır */
    .{{block}} {
        min-height: calc(var(--footer-h) - var(--space-sm));
    }
}

@media (min-width: 3840px) {
    /* 4K: ortalama ve ölçek büyümesi (fallback zorunlu) */
    .{{block}} {
        max-width: 75vw;
        margin-inline: auto;
    }
}
```

### 3.6 Tema Override Şablonu (ADR-044 — jeton seviyesinde)

```css
/* Tema, sınıf katmanına DEĞİL jeton katmanına yazılır:
 * [data-theme="..."] seçicisi yalnızca custom property değerlerini değiştirir. */

[data-theme="dark"] {
    --bg-primary: #0b1120;
    --bg-secondary: #151d30;
    --text-primary: #f1f5f9;
}

[data-theme="glass"] {
    --bg-glass: rgba(15, 23, 42, 0.7);
    --bg-glass-blur: blur(16px);
    --shadow-glass: 0 8px 32px rgba(0, 0, 0, 0.5);
}

.glass-panel {
    background: var(--bg-glass);
    backdrop-filter: var(--bg-glass-blur);
    -webkit-backdrop-filter: var(--bg-glass-blur);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-glass);
}

/* Cinsiyet/kişiselleştirme jetonları (ADR-044) da bu katmanda override edilir:
 * renk, tipografi ve yoğunluk jetonları değişir; seçici ağacı DEĞİŞMEZ. */
[data-accent="alt"] {
    --color-primary: #ec4899;
    --color-accent: #22d3ee;
}
```

### 3.7 Dosya Adlandırma Önekleri (disk kanıtıyla uyumlu)

| Önek | Katman | Örnek |
|------|--------|-------|
| `a-` | `01_Abstracts/` | `a-design-tokens.css` |
| `b-` | `02_Base/` | `b-base-core.css` |
| `_` (alt çizgi) | `03_Layout/`, `04_Components/`, `05_Pages/` | `_header.css`, `_player-info.css` |
| `c-` | `04_Components/` | `c-footer-seek.css` |
| `p-` | `05_Pages/` | `p-login-view.css` |
| `u-` | `06_Utilities/` | `u-helpers-utility.css` |
| `d-` / `d-auth-` | `08_Devices/` | `d-phone.css`, `d-auth-phone.css` |

### 3.8 Giriş Noktaları (`main.css` ve `auth-bundled.css`)

| Dosya (disk kanıtı) | Rol | Kural |
|---------------------|-----|-------|
| `Css/main.css` | Ana uygulama girişi | Katman sırası §3.1'e göre burada birleştirilir |
| `Css/auth-bundled.css` | Auth akışı için paketlenmiş stil | Yalnızca auth sayfalarına yüklenir; ana pakete ek katman konmaz |
| `Css/07_Vendors/bootstrap*.css` | 3. taraf | Elle düzenlenmez; sürüm değişikliği ayrı commit |
| Yeni katman ekleme | — | §3.1 listesi dışında katman adı uydurulmaz (ör. `09_Themes` glob'da yok) |

### 3.9 BEM — Yapılır / Yapılmaz

| ✅ Yapılır | ❌ Yapılmaz | Neden |
|------------|-------------|-------|
| `.player__progress` (element) | `.playerProgress` | Kırık ayrım, çakışma riski |
| `.player--compact` (modifier) | `.playerCompact` | Modifier ayrılamaz |
| `.player.is-loading` (durum) | `.player.loading` | Durum BEM dışı |
| `var(--space-md)` | `16px` | §4 #1 token zorunluluğu |
| Seçici: `.block__el` | Seçici: `div > ul > li` | Kırılgan hiyerarşi |
| Tema: `[data-theme]` jeton override | Tema: `.dark .block { ... }` | Ağaç tekrarı (§3.6) |
| Cihaz katmanı: davranış | Cihaz katmanı: yerleşim | §4 #5 katman kuralı |

### 3.10 Tercih ve Erişilebilirlik Uyarlamaları

```css
/* Hareket azaltma — vestibüler kullanıcılar (WCAG 2.3.3) */
@media (prefers-reduced-motion: reduce) {
    .{{block}},
    .{{block}}__progress {
        transition-duration: 1ms !important; /* gerekçeli istisna: §4 #2 */
        animation-duration: 1ms !important;
        animation-iteration-count: 1 !important;
        scroll-behavior: auto !important;
    }
}

/* Sistem teması — ADR-044 ile uyumlu, jeton seviyesinde */
@media (prefers-color-scheme: dark) {
    :root:not([data-theme]) {
        --bg-primary: #0f172a;
        --text-primary: #f8fafc;
    }
}

/* Odak görünürlüğü — klavye kullanıcısı (WCAG 2.4.7) */
.{{block}}__button:focus-visible {
    outline: 2px solid var(--color-accent);
    outline-offset: 2px;
}

/* Ekran okuyucu için yalnız-aşağı-sınıf (metin gizleme) */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* Yazdırma çıktısı: kontroller gizlenir, içerik akar */
@media print {
    .{{block}}__controls {
        display: none;
    }
}
```

### 3.11 Teknik Zıplama (Skip Link) ve Landmark Hizası

```css
/* Ana içeriğe atlama — klavye ilk sekmesi (WCAG 2.4.1) */
.skip-link {
    position: absolute;
    left: var(--space-sm);
    top: -100px;
    z-index: var(--z-toast);
    padding: var(--space-sm) var(--space-md);
    background: var(--bg-primary);
    color: var(--text-primary);
    border-radius: var(--radius-md);
    transition: top var(--transition-fast);
}

.skip-link:focus {
    top: var(--space-sm);
}

/* Landmark katmanları ITCSS sırasına uyar: header / main / footer */
header.site-header {
    min-height: var(--header-h);
}

main.site-main {
    min-height: var(--content-h);
}

footer.site-footer {
    min-height: var(--footer-h);
}
```

---

## 4. Kurallar

### 4.1 Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Hardcoded piksel/değer yasak — her değer custom property (`var(--...)`) | Kod revert edilir |
| 2 | `!important` yasak (en fazla 3 istisna, gerekçesi yorumda) | Kod revert edilir |
| 3 | Inline `style=""` yasak (PHP/HTML'de sunum kararı yok) | Kod revert edilir |
| 4 | Önişlemci (SCSS/LESS) yasak — saf CSS (ADR-001) | Bağımlılık artışı |
| 5 | Cihaz katmanı yalnızca davranış uyarlaması; layout orada yazılmaz | Katman ihlali |
| 6 | BEM adlandırma zorunlu (`.block__element--modifier`) | Kod revert edilir |
| 7 | WCAG: dokunma hedefi `--touch-min: 44px`; mockup okunmadan CSS yazılamaz | Erişilebilirlik hatası |

### 4.2 Ek Kurallar

- **Katman sırası sabit:** `01_Abstracts → 02_Base → 03_Layout → 04_Components → 05_Pages → 06_Utilities → 07_Vendors → 08_Devices` (§3.1); sıra değiştirilemez, alt katman üst katmanı geçersiz kılmaz.
- **Token tek kaynak:** yeni değer önce `01_Abstracts/` jeton dosyasına, sonra kullanılır; bileşen içinde doğrudan renk/piksel yazılmaz (§4 #1).
- **Vendors dokunulmaz:** `07_Vendors/` içindeki Bootstrap dosyaları elle düzenlenmez.
- **Yerinde refactor:** dosya adı ve yolu değiştirilmez (In-Place Refactoring); yeni katman adı uydurulmaz — §3.1'deki 8 katman + 2 kök dosya geçerlidir.
- **Çelişki kuralı:** `assets.coremusic.net/AGENTS.md` ile glob çıktısı çelişirse glob (disk kanıtı) kazanır ve çelişki ⚠️ VERIFICATION REQUIRED olarak işaretlenir.
- **ADR hizası:** ADR-001 · ADR-018 · ADR-044 · ADR-045 (§1 tablosu).

### 4.3 Sık Yapılan Hatalar

| # | Hata | Sonuç | Doğrusu |
|---|------|-------|---------|
| 1 | Bileşen içinde sabit `16px` | Kırılgan yerleşim | `var(--space-md)` (§3.2) |
| 2 | `08_Devices/` içine yerleşim yazmak | Katman ihlali | Yerleşim `02_Base`/`03_Layout` (§3.5) |
| 3 | `assets/css/` gibi eski yol kullanmak | Kayıp dosya | `assets.coremusic.net/Css/` (§3.1) |
| 4 | Glob'da olmayan katman adı (ör. `09_Themes`) | Uydurma envanter | §3.1 listesi + ⚠️ işareti |
| 5 | Tema için sınıf ağacı çoğaltmak | Bakım yükü | `[data-theme]` jeton override (§3.6) |
| 6 | `!important` ile sıralama kırmak | Öncelik kaosu | Katman sırası + specificity (§4.2) |
| 7 | Mockupsuz CSS üretimi | Görsel sapma | Mockup Before Frontend (§2) |

---

## 5. Workflow

```
MOCKUP OKU → KATMANI SEÇ → ŞABLONU KOPYALA → {{VARIABLE}} DOLDUR → GUARDRAIL #16 DOĞRULA → COMMIT
```

1. **MOCKUP OKU:** `.ai/ui-design/` ilgili görsel + envanter; okunamıyorsa DUR.
2. **KATMANI SEÇ:** §3.1 tablosundan katman + §3.7 adlandırma eki.
3. **ŞABLONU KOPYALA:** ilgili iskeleti (§3.2 token / §3.3 bileşen / §3.4 kırılım / §3.5 cihaz / §3.6 tema) hedef dosyaya kopyala.
4. **`{{VARIABLE}}` DOLDUR:** `{{TITLE}}`, `{{block}}`, `{{device}}`, `{{DATE}}`.
5. **GUARDRAIL #16 DOĞRULA:** §6 kontrol listesi + §4.1 (7 madde) + mockup uyumu.
6. **COMMIT:** responsive + WCAG gözden geçirmesi; registry/log güncel (log append'i parent yapar).

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter | 7 zorunlu alan (title, type, category, date, updated, version, status, authority) |
| 2 | Bölüm yapısı | §1-§7 numaralı, en fazla 3 başlık seviyesi |
| 3 | Placeholder | `{{VARIABLE}}` kalmadı |
| 4 | Guardrails | §4.1 7/7 — hardcoded değer, `!important`, inline style, önişlemci yok |
| 5 | Katman | §3.1 sırası korundu; dosya doğru katmanda |
| 6 | BEM | `.block__element--modifier` + `.is-*` durumları |
| 7 | Token | Her değer `var(--...)` ile |
| 8 | Cihaz dosyası | Yalnızca davranış kuralı içeriyor (layout yok) |
| 9 | WCAG | `--touch-min: 44px` uygulanır; kontrast mockup ile eşleşiyor |
| 10 | Halüsinasyon | Glob'da olmayan katman/dosya adı iddia edilmedi |

---

## 7. Referanslar

| Kaynak | Yol / Kimlik | Amaç |
|--------|--------------|------|
| Template registry | [[.templates/index]] | Envanter (DRY — burada tekrarlanmaz) |
| Vault anayasası | [[../CLAUDE.md]] | Hard Guardrails, ADR-042 |
| Agent registry | [[../../AGENTS.md]] | §6 yönlendirme: CSS → UI Designer |
| JS şablonu | [[js-template]] | Katman hizası (JS ↔ CSS) |
| Mockup indeksi | [[../../ui-design/01-mockup-index]] | Mockup Before Frontend |
| Disk kanıtı | `assets.coremusic.net/Css/` | Katman ve dosya envanteri (glob) |
| Kategori notu | `assets.coremusic.net/AGENTS.md` | 9 katman iddiası → ⚠️ VERIFICATION REQUIRED |
| İlgili ADR'ler | ADR-001 · ADR-018 · ADR-044 · ADR-045 | §1 tablosunda eşleştirilmiştir |

---

**Template Version:** 2.0.0
**Last Updated:** 2026-09-23
