---
type: adr
category: ui
title: "ADR-045: Multi-Domain View Mode Architecture"
date: 2026-08-03
updated: 2026-08-08
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-045: Multi-Domain View Mode Architecture

**Status:** Active (güncellenebilir)
**Kategorisi:** UI Architecture
**İlgili Agent:** [[.agents/ui-designer]]
**İlgili Division:** Software Division

---

## 1. Amaç

Bu ADR, CoreMusic platformundaki çoklu alan adı görünüm modu (multi-domain view mode) mimarisini, her bir görünüm modunu, cihaz algılama mekanizmasını, responsive tasarım kurallarını ve 10 panel arası görünüm geçişlerini tanımlar.

CoreMusic'in multi-domain view mode hedefi:
- 4 görünüm modu: Home, Pro, Studio, Car
- Cihaz algılama: Desktop, tablet, mobile, car, studio
- Responsive tasarım: Tüm cihazlarda optimal görünüm
- Cross-view state: Görünümler arası durum koruma (ADR-046)
- View Transition API: Akıcı geçişler (ADR-048)

---

## 2. Bağlam

### 2.1 Mevcut Durum

CoreMusic, farklı kullanım senaryolarına sahiptir:
- Ev medya merkezi (home.coremusic.net)
- Profesyonel stüdyo (pro.coremusic.net)
- Stüdyo (studio.coremusic.net)
- Araç içi (car.coremusic.net)
- Web tabanlı müzik yönetimi (music.coremusic.net)

Her senaryonun farklı UI gereksinimleri vardır.

### 2.2 Gereksinimler

| # | Gereksinim | Değer | Kaynak |
|---|------------|-------|--------|
| R1 | Görünüm modu | 4 mod (Home, Pro, Studio, Car) | ADR-045 |
| R2 | Cihaz algılama | Desktop, tablet, mobile, car, studio | ADR-045 |
| R3 | Responsive | Tüm cihaz boyutları | ADR-045 |
| R4 | Cross-view state | Görünümler arası durum | ADR-046 |
| R5 | View Transition | Akıcı geçişler | ADR-048 |
| R6 | Tutarlılık | 10 panel arası tutarlı | ADR-045 |
| R7 | Performans | < 200ms geçiş | ADR-045 |
| R8 | Erişilebilirlik | WCAG 2.2 AA | ADR-044 |

### 2.3 Kısıtlamalar

| # | Kısıt | Açıklama |
|---|-------|----------|
| C1 | Framework yasak | Vanilla JS (ADR-001) |
| C2 | ITCSS zorunlu | 7-layer CSS (ADR-001) |
| C3 | BEM zorunlu | CSS metodolojisi |
| C4 | Performans | 200ms altında geçiş |
| C5 | Cross-browser | Chrome, Firefox, Safari, Edge |

---

## 3. Karar

CoreMusic'te **multi-domain view mode** mimarisi kullanılacak.

### 3.1 Görünüm Modları

| Mod | Kullanım Senaryosu | Panel(ler) | Öncelik |
|-----|-------------------|------------|---------|
| **Home** | Ev medya merkezi | home.coremusic.net | Yüksek |
| **Pro** | Profesyonel | pro.coremusic.net | Yüksek |
| **Studio** | Stüdyo | studio.coremusic.net | Orta |
| **Car** | Araç içi | car.coremusic.net | Yüksek |
| **Default** | Web yönetimi | music.coremusic.net | Yüksek |

### 3.2 Cihaz Algılama

| Cihaz | Breakpoint | View Mode | Örnek |
|-------|------------|-----------|-------|
| Desktop | ≥ 1200px | Default/Pro | PC, Laptop |
| Tablet | 768px – 1199px | Home | iPad, Android tablet |
| Mobile | < 768px | Default | Telefon |
| Car | Özel viewport | Car | Araç içi ekran |
| Studio | ≥ 1920px | Studio | Stüdyo monitörü |

### 3.3 View Mode Mimarisi

```
┌─────────────────────────────────────────────────┐
│ Kullanıcı Cihazı (device-loader.js)              │
│  └→ Viewport boyutu, User-Agent, devicePixelRatio │
└──────────────────────┬──────────────────────────┘
                       ↓
┌──────────────────────────────────────────────────┐
│ Cihaz Algılama (08_Devices/)                      │
│  └→ d-desktop, d-tablet, d-mobile, d-car, d-studio │
└──────────────────────┬───────────────────────────┘
                       ↓
┌──────────────────────────────────────────────────┐
│ View Mode (09_ViewModes/)                         │
│  └→ v-home, v-pro, v-studio, v-car, v-default     │
└──────────────────────┬───────────────────────────┘
                       ↓
┌──────────────────────────────────────────────────┐
│ CSS Custom Properties                             │
│  └→ --cm-view-mode: "home"                        │
│  └→ --cm-layout: "grid" / "list" / "player"      │
└──────────────────────────────────────────────────┘
```

---

## 4. Teknik Detaylar

### 4.1 Cihaz Algılama: device-loader.js

```javascript
const DeviceLoader = {
    BREAKPOINTS: {
        mobile: 0,
        tablet: 768,
        desktop: 1200,
        studio: 1920
    },

    detect() {
        const width = window.innerWidth;
        const ua = navigator.userAgent;

        if (/car|android auto|apple carplay/i.test(ua)) return 'car';
        if (/studio/i.test(ua)) return 'studio';
        if (width < this.BREAKPOINTS.tablet) return 'mobile';
        if (width < this.BREAKPOINTS.desktop) return 'tablet';
        if (width >= this.BREAKPOINTS.studio) return 'studio';
        return 'desktop';
    },

    apply() {
        const device = this.detect();
        document.documentElement.setAttribute('data-device', device);
        document.documentElement.classList.add(`d-${device}`);
    }
};

DeviceLoader.apply();
```

### 4.2 CSS View Mode Katmanları

| ITCSS Layer | Dosya | İçerik |
|-------------|-------|--------|
| 01 Settings | `_settings.scss` | Breakpoint değişkenleri |
| 02 Generic | `_generic.scss` | Reset, normalize |
| 03 Elements | `_elements.scss` | HTML elementleri |
| 04 Objects | `_objects.scss` | Layout objeleri |
| 05 Components | `_components.scss` | View mode bileşenleri |
| 06 Utilities | `_utilities.scss` | Yardımcı sınıflar |
| 07 Theme | `_theme.scss` | Tema değişkenleri |

### 4.3 View Mode CSS

```css
/* Home View Mode */
[data-view-mode="home"] {
  --cm-layout: grid;
  --cm-sidebar: none;
  --cm-player: footer;
  --cm-nav: horizontal;
}

/* Pro View Mode */
[data-view-mode="pro"] {
  --cm-layout: list;
  --cm-sidebar: left;
  --cm-player: sidebar;
  --cm-nav: vertical;
}

/* Studio View Mode */
[data-view-mode="studio"] {
  --cm-layout: grid;
  --cm-sidebar: right;
  --cm-player: floating;
  --cm-nav: horizontal;
}

/* Car View Mode */
[data-view-mode="car"] {
  --cm-layout: single;
  --cm-sidebar: none;
  --cm-player: bottom;
  --cm-nav: minimal;
  --cm-font-size: 18px; /* Büyük butonlar */
  --cm-touch-target: 48px; /* Min dokunma alanı */
}
```

### 4.4 Responsive Breakpoint Matrisi

| Breakpoint | Width | View Mode | Layout | Sidebar | Player |
|------------|-------|-----------|--------|---------|--------|
| Mobile | < 768px | Default | Single | None | Bottom |
| Tablet | 768-1199px | Home | Grid | None | Footer |
| Desktop | 1200-1919px | Default | Grid | Left | Sidebar |
| Studio | ≥ 1920px | Studio | Grid | Right | Floating |
| Car | Özel | Car | Single | None | Bottom |

### 4.5 View Mode Geçişi

```javascript
const ViewModeManager = {
    currentMode: 'default',

    switch(newMode) {
        const validModes = ['home', 'pro', 'studio', 'car', 'default'];
        if (!validModes.includes(newMode)) return;

        // View Transition API (ADR-048)
        if (document.startViewTransition) {
            document.startViewTransition(() => {
                this.apply(newMode);
            });
        } else {
            this.apply(newMode);
        }
    },

    apply(mode) {
        document.documentElement.setAttribute('data-view-mode', mode);
        this.currentMode = mode;
        localStorage.setItem('cm-view-mode', mode);
    },

    restore() {
        const saved = localStorage.getItem('cm-view-mode');
        if (saved) this.apply(saved);
    }
};
```

### 4.6 Panel ↔ View Mode Eşleme

| Panel | Varsayılan View Mode | Değişebilir mi? |
|-------|---------------------|-----------------|
| music.coremusic.net | default | ✅ |
| home.coremusic.net | home | ❌ |
| pro.coremusic.net | pro | ❌ |
| studio.coremusic.net | studio | ❌ |
| car.coremusic.net | car | ❌ |
| admin.coremusic.net | default | ❌ |
| download.coremusic.net | default | ✅ |
| media.coremusic.net | default | ✅ |
| auth.coremusic.net | default | ❌ |
| coremusic.net | default | ✅ |

---

## 5. Yasak Örüntüler

| # | Yasak | Doğru | Kaynak |
|---|-------|-------|--------|
| 1 | Framework kullanımı | Vanilla JS | ADR-001 |
| 2 | ITCSS dışı CSS | ITCSS 7-layer | ADR-001 |
| 3 | BEM dışı naming | BEM/BEMIT | ADR-001 |
| 4 | innerHTML | DOMParser + TrustedTypes | ADR-001 |
| 5 | Hardcoded breakpoint | CSS variables | ADR-045 |
| 6 | Sayfa yenileme ile geçiş | View Transition API | ADR-048 |
| 7 | var kullanımı | const/let | ADR-001 |
| 8 | Touch target < 48px | Min 48px | WCAG |

---

## 6. Edge Cases

| # | Edge Case | Tetikleyici | Çözüm | ADR |
|---|-----------|-------------|-------|-----|
| 1 | Pencere yeniden boyutlandırma | Resize olayı | Debounce + recalculate | ADR-045 |
| 2 | Car mode'da dokunma | Küçük buton | 48px touch target | ADR-045 |
| 3 | Cross-view state kaybı | View mode değişikliği | State preservation | ADR-046 |
| 4 | Eski tarayıcı | View Transition yok | CSS fallback | ADR-048 |
| 5 | Yüksek DPI | retina ekran | 2x asset | ADR-045 |
| 6 | Landscape orientation | Yatay mod | Responsive override | ADR-045 |
| 7 | Split screen | Çoklu pencere | Min-width korunması | ADR-045 |
| 8 | Dark mode | OS tercihi | prefers-color-scheme | ADR-044 |
| 9 | Reduced motion | Animasyon engeli | prefers-reduced-motion | ADR-048 |
| 10 | Print mode | Yazdırma | Print CSS | ADR-045 |

---

## 7. Hard Guardrails

| # | Guardrail | Uygulama | İhlal Sonucu |
|---|-----------|----------|-------------|
| G1 | 4 görünüm modu | Home, Pro, Studio, Car | UI tutarsızlığı |
| G2 | Cihaz algılama zorunlu | device-loader.js | Yanlış görünüm |
| G3 | ITCSS 7-layer | CSS mimarisi | Bakım zorluğu |
| G4 | BEM naming | CSS metodolojisi | İsim çakışması |
| G5 | Touch target ≥ 48px | Mobil erişilebilirlik | Kullanılamaz |
| G6 | View Transition | Akıcı geçiş | Kötü UX |
| G7 | Performans < 200ms | Geçiş süresi | Görsel gecikme |
| G8 | WCAG 2.2 AA | Erişilebilirlik | Yasal risk |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS | Frontend stack |
| [[ADR-004-multi-domain-spa]] | Multi-domain SPA | Domain yapısı |
| [[ADR-044-dynamic-user-theme-engine]] | Dynamic theme | Tema entegrasyonu |
| [[ADR-046-cross-view-state-preservation]] | Cross-view state | Durum koruma |
| [[ADR-048-view-transition-api-integration]] | View Transition API | Geçiş animasyonu |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef Dosya | İlişki |
|-------|-------------|--------|
| § 3.1 | [[subdomains/README]] | 10 panel |
| § 3.2 | [[assets.coremusic.net/js/device-loader.js]] | Cihaz algılama |
| § 4.2 | [[assets.coremusic.net/Css/08_Devices/]] | Cihaz CSS'leri |
| § 4.3 | [[assets.coremusic.net/Css/09_ViewModes/]] | View mode CSS'leri |
| § 4.5 | [[ADR-048-view-transition-api-integration]] | View Transition |
| § 5 | [[brain.md]] §18 | Coding standards |
| § 7 | [[CLAUDE.md]] §7 | Hard guardrails |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **View Mode** | Görünüm modu — Kullanım senaryosuna göre UI düzeni |
| **Device Detection** | Cihaz algılama — Viewport ve User-Agent analizi |
| **Breakpoint** | Responsive kırılma noktası |
| **Touch Target** | Minimum dokunma alanı (48px) |
| **data-attribute** | HTML data-* öznitelikleri |
| **CSS Custom Properties** | CSS değişkenleri |
| **ITCSS** | It's Time to Create Scaleable Stylesheets |
| **BEM** | Block Element Modifier — CSS metodolojisi |
| **View Transition API** | Tarayıcı tabanlı geçiş animasyonu |
| **Cross-view State** | Görünümler arası durum koruma |
| **Responsive** | Tüm cihazlara uyumlu tasarım |
| **Debounce** | Çoklu tetiklemeyi tek işleme düşürme |
| **First Contentful Paint** | İlk içerik boyama süresi |
| **WCAG** | Web Content Accessibility Guidelines |
| **Media Query** | Cihaz özelliklerine göre CSS kuralları |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| ADR Version | 2.0.0 |
| Status | Active · Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-08 |
| Sections | 11 |
| Görünüm Modu | 4 (Home, Pro, Studio, Car) |
| Cihaz Tipi | 5 (Desktop, Tablet, Mobile, Car, Studio) |
| Breakpoint | 4 |
| ITCSS Layer | 7 |
| CSS Variable | 3 ana değişken |
| Panel ↔ View Mode | 10 eşleme |
| Edge Cases | 10 |
| Hard Guardrails | 8 |
| Yasak Örüntü | 8 |
| İlgili ADR | 5 |
| Çapraz Referans | 7 |
| Sözlük Terim | 15 |

---

## 12. Authority

| Alan | Değer |
|------|-------|
| Authority | Bayram Ali / Vault Steward |
| Review Status | Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-08 |
| Version | 2.0.0 |
| Immutability | Active (güncellenebilir) |
| Next Review | Yeni view mode eklendiğinde |
| Related Division | Software Division |
| Risk Seviyesi | Orta (UX) |

---

## 13. Deployment Considerations

| # | Konu | Detay |
|---|------|-------|
| 1 | CSS deploy | Static asset CDN |
| 2 | JS deploy | Minified + cached |
| 3 | Breakpoint test | Tüm cihaz boyutları |
| 4 | Touch test | Mobil dokunma |
| 5 | Orientation test | Landscape/Portrait |
| 6 | Performance audit | Core Web Vitals |
| 7 | Documentation | Kullanıcı kılavuzu |
| 8 | Analytics | View mode kullanımı |

---

## 14. Testing Strategy

| Test Türü | Kapsam | Framework |
|-----------|--------|-----------|
| Unit Test | Device detection JS | Vitest |
| Visual Test | View mode görünümleri | Playwright screenshot |
| Responsive Test | Breakpoint uyumluluğu | Playwright |
| Accessibility Test | WCAG uyumluluğu | Lighthouse |
| Performance Test | Geçiş süresi | Performance API |
| Cross-browser Test | Tarayıcı desteği | Playwright |

---

## 15. Risk Assessment

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|----------|------|------------|
| R1 | Responsive hata | Orta | Orta | Breakpoint test |
| R2 | Touch hata | Orta | Yüksek | 48px target |
| R3 | Cross-browser | Orta | Orta | Fallback |
| R4 | Performance | Düşük | Orta | Optimization |
| R5 | Orientation | Düşük | Düşük | Media query |
| R6 | Dark mode | Düşük | Düşük | OS preference |
| R7 | Reduced motion | Düşük | Yüksek | prefers-reduced-motion |
| R8 | Print mode | Düşük | Düşük | Print CSS |

---

## 16. Maintenance Guidelines

| # | Görev | Siklik | Sorumlu |
|---|-------|--------|---------|
| 1 | Responsive audit | Aylık | UI Designer |
| 2 | Accessibility audit | Aylık | QA Engineer |
| 3 | Performance audit | Üç aylık | QA Engineer |
| 4 | Cross-browser test | Yeni sürümde | QA Engineer |
| 5 | Touch target check | Aylık | UI Designer |
| 6 | CSS optimization | Aylık | UI Designer |

---

## 17. Future Considerations

| # | Konu | Durum | Notlar |
|---|------|-------|--------|
| 1 | Foldable device | Araştırılıyor | Katlanabilir ekran |
| 2 | AR/VR view | Gelecek | Artırılmış gerçeklik |
| 3 | Voice interface | Planlanıyor | Ses kontrollü |
| 4 | Gesture control | Araştırılıyor | Hareket tabanlı |
| 5 | Adaptive UI | Planlanıyor | AI-powered adaptasyon |
| 6 | Haptic feedback | Gelecek | Dokunsal geri bildirim |

---

## 18. View Mode Feature Matrix

| Özellik | Home | Pro | Studio | Car | Default |
|---------|------|-----|--------|-----|---------|
| Full player | ✅ | ✅ | ✅ | ✅ | ✅ |
| Sidebar | ❌ | ✅ | ✅ | ❌ | ✅ |
| Playlist view | Grid | List | Grid | Single | Grid |
| EQ panel | Basic | Advanced | Advanced | Basic | Basic |
| Touch targets | 48px | 32px | 32px | 48px | 32px |
| Font size | 16px | 14px | 14px | 18px | 14px |
| Dark mode | ✅ | ✅ | ✅ | ✅ | ✅ |
| Mini player | ✅ | ❌ | ❌ | ✅ | ✅ |

---

## 19. View Mode Transition Matrix

| From → To | Home | Pro | Studio | Car | Default |
|-----------|------|-----|--------|-----|---------|
| Home | — | slide-right | slide-right | fade | fade |
| Pro | slide-left | — | slide-right | fade | fade |
| Studio | slide-left | slide-left | — | fade | fade |
| Car | fade | fade | fade | — | fade |
| Default | fade | slide-right | slide-right | fade | — |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
