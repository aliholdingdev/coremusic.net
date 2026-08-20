---
type: template
category: adr-frontend
title: "CoreMusic — ADR Frontend Template (CSS/SPA/UI/Component)"
version: 1.0.0
created: 2026-08-07
updated: 2026-08-07
authority: Vault Steward
governance: Red Team • Human Mode • Truth Mode
usage: "Frontend/CSS/SPA/UI ile ilgili ADR oluştururken bu dosyayı kopyalayın"
related:
  - "[[decisions/accepted/ADR-001-vanilla-js-itcss]]"
  - "[[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]]"
  - "[[decisions/accepted/ADR-044-dynamic-user-theme-engine]]"
  - "[[decisions/accepted/ADR-045-multi-domain-view-mode-architecture]]"
tags: [template, adr, frontend, css, spa, ui, component, itcss]
---

# CoreMusic — ADR Frontend Template

**Bu dosya bir şablondur.** Frontend, CSS, SPA, UI veya Component ile ilgili ADR oluştururken bu dosyayı kopyalayın.

**Kullanım:** `cp .ai/.templates/adr-frontend-template.md .ai/decisions/accepted/ADR-NNN-baslik.md`

---

## 📋 Frontend ADR Kullanım Kılavuzu

### Frontend ADR Ne Zaman Yazılır?

| Durum | Gerekli mi? | Açıklama |
|-------|-------------|----------|
| Yeni CSS katmanı ekleme | ✅ Evet | ITCSS katman yapısını etkiliyor |
| Yeni JS modülü ekleme | ✅ Evet | Mimari yapıyı etkiliyor |
| Component pattern değişikliği | ✅ Evet | Tüm panelleri etkiliyor |
| Responsive breakpoint değişikliği | ✅ Evet | Cihaz uyumluluğunu etkiliyor |
| Theme engine değişikliği | ✅ Evet | Tüm temaları etkiliyor |
| UI library ekleme | ✅ Evet | ADR-001 ile çelişebilir |
| Küçük CSS düzeltmesi | ❌ Hayır | Rutin değişiklik |

### Frontend ADR Yazarken Dikkat

1. **ADR-001'e uygunluk:** Vanilla JS + ITCSS zorunlu
2. **Framework yasağı:** React, Vue, Svelte, Angular KULLANILAMAZ
3. **Build tool yasağı:** Webpack, Rollup, esbuild KULLANILMAZ (tercihen)
4. **DOMParser + TrustedTypes:** Zorunlu
5. **ITCSS 7 katman:** Korunmalıdır

---

## 📄 FRONTEND ADR ŞABLONU

---

```yaml
---
type: decision
id: "NNN"
title: "ADR-NNN: [Frontend Karar Başlığı]"
category: "frontend"
status: "draft|active|frozen"
date: "YYYY-MM-DD"
updated: "YYYY-MM-DD"
authority: "Frontend Architect / UI Designer"
governance: "Red Team • Human Mode • Truth Mode"
supersedes: null
version: 1.0.0
tags: [frontend, css, spa, ui, component, itcss]
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[AGENTS.md]]"
  - "[[keys.md]]"
  - "[[decisions/accepted/ADR-001-vanilla-js-itcss]]"
  - "[[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]]"
  - "[[decisions/accepted/ADR-044-dynamic-user-theme-engine]]"
  - "[[architecture/l3-presentation]]"
---
```

---

## 1. Executive Summary

[Frontend kararının kısa özeti. Ne değişiyor? Neden?]

---

## 2. Status

| Alan | Değer |
|------|-------|
| **Durum** | draft / active / frozen |
| **Versiyon** | 1.0.0 |
| **Oluşturma** | YYYY-MM-DD |
| **Son Güncelleme** | YYYY-MM-DD |
| **Otorite** | Frontend Architect / UI Designer |
| **Onay** | Red Team • Human Mode • Truth Mode |

---

## 3. Context

### 3.1 Problem Tanımı

[Frontend ile ilgili hangi sorun çözülüyor?]

### 3.2 Mevcut Frontend Durumu

| Bileşen | Mevcut Durum | Değişiklik |
|---------|-------------|------------|
| **CSS Framework** | Vanilla JS + ITCSS | [Değişiklik] |
| **JS Framework** | Vanilla JS ES6+ | [Değişiklik] |
| **Build Tool** | Yok (tercihen) | [Değişiklik] |
| **Type Safety** | JSDoc | [Değişiklik] |
| **State Management** | Vanilla | [Değişiklik] |

### 3.3 İtici Güçler

| # | Güç | Açıklama | Kritiklik |
|---|-----|----------|-----------|
| 1 | [Güç 1] | [Açıklama] | Yüksek/Orta/Düşük |
| 2 | [Güç 2] | [Açıklama] | Yüksek/Orta/Düşük |

### 3.4 Teknik Kısıtlamalar

| Kısıtlama | Açıklama | İlgili ADR |
|-----------|----------|------------|
| ADR-001 | Vanilla JS + ITCSS zorunlu | ADR-001 |
| ADR-044 | Theme engine uyumu | ADR-044 |
| WCAG 2.2 AA | Erişilebilirlik standartları | — |
| Lighthouse 95+ | Performans hedefi | ADR-006 |

### 3.5 Ekosistem Etkileşimi

| Etkilenen Alan | Etki | Açıklama |
|---------------|------|----------|
| **L2 Routing** | Doğrudan | SPA Router değişikliği |
| **L1 Security** | Doğrudan | CSP nonce, TrustedTypes |
| **L0 Infrastructure** | Endirekt | Client-side cache |
| **Audio Service** | Endirekt | Web Audio API |

---

## 4. Decision

### 4.1 Karar Bildirimi

**[Net frontend karar cümlesi]**

### 4.2 Frontend Kuralları

| # | Kural | Durum | İlgili ADR |
|---|-------|-------|------------|
| 1 | Vanilla JS ES6+ zorunlu | ✅ Zorunlu | ADR-001 |
| 2 | ITCSS 7 katman yapısı | ✅ Zorunlu | ADR-001 |
| 3 | BEM naming convention | ✅ Zorunlu | ADR-001 |
| 4 | DOMParser + TrustedTypes | ✅ Zorunlu | ADR-001 |
| 5 | `var` kullanımı yasak | ❌ Yasak | ADR-001 |
| 6 | `innerHTML` kullanımı yasak | ❌ Yasak | ADR-001 |
| 7 | `eval()` kullanımı yasak | ❌ Yasak | ADR-001 |
| 8 | `===` strict equality | ✅ Zorunlu | ADR-001 |
| 9 | `const`/`let` zorunlu | ✅ Zorunlu | ADR-001 |
| 10 | Native ES modules | ✅ Zorunlu | ADR-001 |

### 4.3 ITCSS Katman Etkisi

| Katman | Mevcut | Değişiklik | Etki |
|--------|--------|------------|------|
| **01 Settings** | [Durum] | [Değişiklik] | [Etki] |
| **02 Tools** | [Durum] | [Değişiklik] | [Etki] |
| **03 Generic** | [Durum] | [Değişiklik] | [Etki] |
| **04 Elements** | [Durum] | [Değişiklik] | [Etki] |
| **05 Objects** | [Durum] | [Değişiklik] | [Etki] |
| **06 Components** | [Durum] | [Değişiklik] | [Etki] |
| **07 Utilities** | [Durum] | [Değişiklik] | [Etki] |

### 4.4 Component Pattern

```javascript
/**
 * Component: [Component Adı]
 * Dosya: [dosya yolu]
 * ADR: ADR-NNN
 *
 * @description [Component açıklaması]
 * @example
 * const component = new ComponentName({ option: value });
 * component.init();
 */
class ComponentName {
    #privateField;
    #options;

    constructor(options = {}) {
        this.#options = { ...this.#defaults, ...options };
        this.#privateField = null;
    }

    init() {
        this.#bindEvents();
        this.#render();
    }

    #bindEvents() {
        // Event binding logic
    }

    #render() {
        // Render logic using DOMParser + TrustedTypes
    }

    destroy() {
        // Cleanup logic
    }
}
```

### 4.5 CSS Bileşen Yapısı

```css
/**
 * Component: [Component Adı]
 * Dosya: [dosya yolu]
 * ADR: ADR-NNN
 *
 * BEM Naming: .block__element--modifier
 * ITCSS Layer: 06 Components
 */

/* Block */
.component-name {
    /* Temel stiller */
}

/* Element */
.component-name__element {
    /* Element stilleri */
}

/* Modifier */
.component-name--modifier {
    /* Modifier stilleri */
}

/* Responsive */
@media (max-width: 768px) {
    .component-name {
        /* Mobil stiller */
    }
}
```

### 4.6 Kod Örnekleri

```javascript
// JavaScript Kod Örneği
// Dosya: [dosya yolu]
// ADR: ADR-NNN

import { DOMParser } from 'dom-parser';
import { TrustedTypes } from 'trusted-types';

// [Kod açıklaması]
```

```css
/* CSS Kod Örneği */
/* Dosya: [dosya yolu] */
/* ADR: ADR-NNN */

/* [CSS açıklaması] */
```

---

## 5. Architecture

### 5.1 Component Mimarisi

```
┌─────────────────────────────────────────┐
│           Application Shell              │
│  ┌─────────────────────────────────────┐ │
│  │         Header Component            │ │
│  └─────────────────────────────────────┘ │
│  ┌─────────────┬───────────────────────┐ │
│  │  Sidebar    │    Main Content       │ │
│  │  Component  │    Component          │ │
│  └─────────────┴───────────────────────┘ │
│  ┌─────────────────────────────────────┐ │
│  │         Footer Component            │ │
│  └─────────────────────────────────────┘ │
└─────────────────────────────────────────┘
```

### 5.2 State Management Akışı

```
User Action → Event Handler → State Update → DOM Patch → Re-render
     │              │              │              │            │
     ▼              ▼              ▼              ▼            ▼
[Component]   [addEventListener] [setState]   [DOMParser]  [render]
```

### 5.3 CSS Custom Properties Akışı

```
Theme (ADR-044) → data-gender attribute → CSS Variables → Component Styles
     │                    │                    │                │
     ▼                    ▼                    ▼                ▼
[res-pink/]         <html data-gender>   --theme-primary   .component
[res-blue/]         [female/male]        --theme-accent    [styles]
[res-default/]      [neutral]            --theme-bg
```

---

## 6. Alternatives Considered

### 6.1 Alternatif 1: [Alternatif Adı]

**Açıklama:** [Bu alternatifin kısa açıklaması]

**Avantajlar:**
- [Avantaj 1]
- [Avantaj 2]

**Dezavantajlar:**
- [Dezavantaj 1]
- [Dezavantaj 2]

**Neden Reddedildi:** [Red gerekçesi — ADR-001 ile uyumluluk]

### 6.2 Alternatif 2: [Alternatif Adı]

**Açıklama:** [Bu alternatifin kısa açıklaması]

**Avantajlar:**
- [Avantaj 1]

**Dezavantajlar:**
- [Dezavantaj 1]

**Neden Reddedildi:** [Red gerekçesi]

### 6.3 Karar Matrisi

| Kriter | Ağırlık | Alternatif 1 | Alternatif 2 | Seçilen |
|--------|---------|-------------|-------------|---------|
| ADR-001 Uyumu | %30 | [Puan] | [Puan] | [X] |
| Performans | %25 | [Puan] | [Puan] | [X] |
| Bakım Kolaylığı | %20 | [Puan] | [Puan] | [X] |
| Erişilebilirlik | %15 | [Puan] | [Puan] | [X] |
| Öğrenme Eğrisi | %10 | [Puan] | [Puan] | [X] |

---

## 7. Consequences

### 7.1 Olumlu Sonuçlar

| # | Sonuç | Etki |
|---|-------|------|
| 1 | [Olumlu sonuç 1] | Yüksek/Orta/Düşük |
| 2 | [Olumlu sonuç 2] | Yüksek/Orta/Düşük |

### 7.2 Olumsuz Sonuçlar

| # | Sonuç | Risk | Mitigation |
|---|-------|------|------------|
| 1 | [Olumsuz sonuç 1] | Yüksek/Orta/Düşük | [Çözüm] |
| 2 | [Olumsuz sonuç 2] | Yüksek/Orta/Düşük | [Çözüm] |

### 7.3 ADR-001 Uyumluluk Kontrolü

| Kural | Durum | Açıklama |
|-------|-------|----------|
| Vanilla JS | ✅ Uyumlu | Framework kullanılmıyor |
| ITCSS | ✅ Uyumlu | Katman yapısı korunuyor |
| BEM | ✅ Uyumlu | Naming convention uygulanıyor |
| DOMParser | ✅ Uyumlu | innerHTML yerine kullanılıyor |
| TrustedTypes | ✅ Uyumlu | XSS koruması sağlanıyor |

---

## 8. Implementation Roadmap

### 8.1 Kısa Vadeli (0-3 Ay)

| # | Görev | Sorumlu | Süre |
|---|-------|---------|------|
| 1 | [Görev 1] | [Sorumlu] | [Süre] |
| 2 | [Görev 2] | [Sorumlu] | [Süre] |

### 8.2 Orta Vadeli (3-6 Ay)

| # | Görev | Sorumlu | Süre |
|---|-------|---------|------|
| 1 | [Görev 1] | [Sorumlu] | [Süre] |

---

## 9. Testing Strategy

### 9.1 Frontend Test Kapsamı

| Test Türü | Hedef | Araç |
|-----------|-------|------|
| **Unit Test** | %80+ | Vitest |
| **Integration Test** | %70+ | Vitest |
| **E2E Test** | Kritik akışlar | Playwright |
| **Visual Regression** | [Hedef] | [Araç] |
| **Accessibility Test** | WCAG 2.2 AA | axe-core |

### 9.2 Test Senaryoları

| # | Senaryo | Türü | Beklenen Sonuç |
|---|---------|------|----------------|
| 1 | [Senaryo 1] | Unit | [Sonuç] |
| 2 | [Senaryo 2] | E2E | [Sonuç] |
| 3 | [Senaryo 3] | Accessibility | [Sonuç] |

### 9.3 Test Komutları

```bash
# Frontend Unit Testler
cd assets.coremusic.net && npx vitest run

# E2E Testler
npx playwright test

# Accessibility Audit
npx axe-cli [url]
```

---

## 10. Performance Impact

### 10.1 Core Web Vitals

| Metrik | Mevcut | Hedef | Fark |
|--------|--------|-------|------|
| **LCP** | [ms] | < 2.5s | [ms] |
| **INP** | [ms] | < 200ms | [ms] |
| **CLS** | [ms] | < 0.1 | [ms] |
| **FCP** | [ms] | < 1.8s | [ms] |
| **TTFB** | [ms] | < 800ms | [ms] |

### 10.2 Bundle Size

| Dosya | Mevcut | Hedef | Fark |
|-------|--------|-------|------|
| main.css | [KB] | < 30KB | [KB] |
| main.js | [KB] | < 50KB | [KB] |
| Toplam | [KB] | < 80KB | [KB] |

### 10.3 Lighthouse Hedefleri

| Kategori | Mevcut | Hedef |
|----------|--------|-------|
| Performance | [Skor] | 95+ |
| Accessibility | [Skor] | 95+ |
| Best Practices | [Skor] | 95+ |
| SEO | [Skor] | 95+ |

---

## 11. Accessibility (Erişilebilirlik)

### 11.1 WCAG 2.2 AA Uyumluluğu

| Kural | Durum | Açıklama |
|-------|-------|----------|
| **1.1.1** Non-text Content | ✅/⚠️/❌ | [Açıklama] |
| **1.3.1** Info and Relationships | ✅/⚠️/❌ | [Açıklama] |
| **1.4.3** Contrast (Minimum) | ✅/⚠️/❌ | [Açıklama] |
| **2.1.1** Keyboard | ✅/⚠️/❌ | [Açıklama] |
| **2.4.7** Focus Visible | ✅/⚠️/❌ | [Açıklama] |
| **4.1.2** Name, Role, Value | ✅/⚠️/❌ | [Açıklama] |

### 11.2 ARIA Kullanımı

```html
<!-- Doğru ARIA kullanımı -->
<div role="navigation" aria-label="Ana menü">
    <ul role="menubar">
        <li role="menuitem">
            <a href="/kesfet" aria-current="page">Keşfet</a>
        </li>
    </ul>
</div>

<!-- Yanlış ARIA kullanımı (yasak) -->
<div class="nav">  <!-- role eksik -->
    <ul>
        <li><a href="/kesfet">Keşfet</a></li>  <!-- aria-current eksik -->
    </ul>
</div>
```

### 11.3 Keyboard Navigation

| Tuş | İşlev | Durum |
|-----|-------|-------|
| **Tab** | Bir sonraki focus elemanı | ✅ |
| **Shift+Tab** | Bir önceki focus elemanı | ✅ |
| **Enter** | Buton/link aktivasyonu | ✅ |
| **Escape** | Modal/kapatma | ✅ |
| **Arrow Keys** | Menü navigasyonu | ✅ |

---

## 12. Responsive Design

### 12.1 Breakpoint Matrisi

| Breakpoint | Cihaz | CSS Dosyası | Durum |
|-----------|-------|-------------|-------|
| **< 480px** | Telefon | d-phone.css | ✅ |
| **480-768px** | Tablet | d-tablet.css | ✅ |
| **768-1024px** | Laptop | d-laptop.css | ✅ |
| **1024-1440px** | Desktop | d-desktop.css | ✅ |
| **1440-2560px** | 4K Monitor | d-4k-monitor.css | ✅ |
| **> 2560px** | 4K TV | d-4k-tv.css | ✅ |
| **Gömülü** | RPi5 | d-embedded.css | ✅ |

### 12.2 View Mode Matrisi

| View Mode | Kullanım | CSS Dosyası | Durum |
|-----------|---------|-------------|-------|
| **Home** | Ev medya merkezi | v-home.css | ✅ |
| **Pro** | Profesyonel panel | v-pro.css | ✅ |
| **Studio** | Stüdyo üretimi | v-studio.css | ✅ |

### 12.3 Touch Target Boyutları

| Cihaz | Min Boyut | Hedef | Durum |
|-------|-----------|-------|-------|
| **Mobil** | 44x44px | 48x48px | ✅ |
| **Tablet** | 44x44px | 48x48px | ✅ |
| **Desktop** | 36x36px | 44x44px | ✅ |
| **TV** | 48x48px | 56x56px | ✅ |

---

## 13. Theme Engine Uyumu (ADR-044)

### 13.1 CSS Custom Properties

| Property | Değer | Kaynak |
|----------|-------|--------|
| `--theme-primary` | [Renk] | res-pink/res-blue/res-default |
| `--theme-accent` | [Renk] | Tema dizini |
| `--theme-bg` | [Renk] | Tema dizini |
| `--theme-text` | [Renk] | Tema dizini |
| `--view-accent` | [Renk] | View mode CSS |
| `--view-font-scale` | [Sayı] | View mode CSS |
| `--view-density` | [String] | View mode CSS |

### 13.2 data-gender Attribute

```html
<!-- Tema seçimi -->
<html data-gender="female">  <!-- Pembe tema -->
<html data-gender="male">    <!-- Mavi tema -->
<html data-gender="neutral"> <!-- Nötr tema -->
```

### 13.3 Theme Switching Akışı

```
User selects gender → AuthService → DB (user_preferences) → Session
     │                    │              │                    │
     ▼                    ▼              ▼                    ▼
[select-gender.php]  [AuthService]  [user_preferences]  [COREMUSIC_SESS]
     │
     ▼
CSS Custom Properties update → No page reload
```

---

## 14. Security Considerations

### 14.1 XSS Koruması

| Kontrol | Durum | Detay |
|---------|-------|-------|
| **DOMParser** | ✅ Zorunlu | innerHTML yerine |
| **TrustedTypes** | ✅ Zorunlu | Policy creation |
| **CSP Nonce** | ✅ Zorunlu | Per-request nonce |
| **Input Sanitization** | ✅ Zorunlu | DOMPurify |

### 14.2 CSP Uyumluluğu

```javascript
// TrustedTypes Policy
const policy = trustedTypes.createPolicy('default', {
    createHTML: (string) => {
        // Sanitize HTML
        return DOMPurify.sanitize(string);
    },
    createScriptURL: (string) => {
        // Validate URL
        return new URL(string, document.baseURI).href;
    }
});
```

---

## 15. Rollback Plan

| Senaryo | Tetikleyici | Geri Alma Adımları |
|---------|-------------|-------------------|
| CSS Bozulması | Layout kırılması | 1. Eski CSS'i geri yükle 2. Cache temizle |
| JS Hatası | Konsol hataları | 1. Eski JS'i geri yükle 2. Service Worker reset |
| Performans Düşüşü | Lighthouse < 80 | 1. Değişiklikleri geri al 2. Benchmark çalıştır |

---

## 16. Related Decisions

| ADR | Başlık | İlişki |
|-----|--------|--------|
| ADR-001 | Vanilla JS + ITCSS | Ana kural |
| ADR-018 | Footer Player Vaporwave | UI etkisi |
| ADR-021 | SPA Router Immutable | Routing etkisi |
| ADR-044 | Dynamic Theme Engine | Tema uyumu |
| ADR-045 | Multi-Domain View Mode | View mode uyumu |
| ADR-046 | Cross-View State | State management |
| ADR-048 | View Transition API | Animasyon |

---

## 17. Glossary

| Terim | Tanım |
|-------|-------|
| **ITCSS** | Inverted Triangle CSS — katmanlı CSS mimarisi |
| **BEM** | Block Element Modifier — CSS naming convention |
| **DOMParser** | innerHTML yerine güvenli HTML parsing |
| **TrustedTypes** | XSS koruması için Google politikası |
| **LCP** | Largest Contentful Paint — performans metriği |
| **CLS** | Cumulative Layout Shift — performans metriği |
| **INP** | Interaction to Next Paint — performans metriği |
| **FCP** | First Contentful Paint — performans metriği |

---

## 18. Edge Cases

| Durum | Belirti | Çözüm |
|-------|---------|-------|
| CSS yükleme gecikmesi | FOUC | Critical CSS inline |
| JS yükleme gecikmesi | Boş sayfa | Async/defer attributes |
| Eski tarayıcı | Desteklenmeyen API | Graceful degradation |
| Mobil cihaz | Küçük ekran | Responsive breakpoints |
| Yavaş ağ | uzun yükleme | Offline-first strategy |

---

## 19. Warnings

> [!WARNING]
> **ADR-001 İhlali:** React, Vue, Svelte veya Angular kullanımı KESİNLİKLE yasaktır.

> [!WARNING]
> **innerHTML Yasağı:** DOMParser + TrustedTypes zorunludur.

> [!WARNING]
> **Framework Yasak:** Bootstrap, Tailwind, Material UI kullanımı yasaktır.

---

## 20. Limitations

| # | Sınırlama | Etki | Gelecek Çözüm |
|---|-----------|------|---------------|
| 1 | IE11 desteği yok | Düşük | Tier 2 tarayıcı desteği |
| 2 | SSR yok | Orta | Gelecekte düşünülebilir |
| 3 | Type safety sınırlı | Orta | JSDoc genişletme |

---

## 21. Dependencies

| Bağımlılık | Versiyon | Kullanım |
|------------|---------|---------|
| Vanilla JS | ES6+ | Ana dil |
| CSS | Level 3+ | Stillendirme |
| DOMParser | — | HTML parsing |
| TrustedTypes | — | XSS koruması |
| Web Audio API | — | Ses işleme |

---

## 22. Future Roadmap

| Versiyon | Hedef | Tahmini |
|----------|-------|---------|
| v1.1 | View Transition API entegrasyonu | 2026-Q3 |
| v1.2 | CSS Nesting desteği | 2026-Q4 |
| v2.0 | Container Queries | 2027-Q1 |

---

## 23. Related Documents

| Dosya | Amaç |
|-------|------|
| [[architecture/l3-presentation]] | L3 Presentation katmanı |
| [[ui-design/00-mockup-index]] | Mockup indeksi — 18 PNG (frontend görevlerinde ZORUNLU) |
| [[ui-design/01-component-inventory]] | Bileşen envanteri — C01-C16 |
| [[ui-design/tokens/design-tokens-master]] | Design token'lar |
| [[ui-design/02-implementation-plan]] | CSS uygulama planı |
| `.ai/.png/home-1024/` (12 PNG) + `.ai/.png/shared-1024/` (6 PNG) | Mockup görselleri — RPi5 1024×600 |

---

## 24. Cross References

```
ADR-NNN (Frontend)
    │
    ├─► decisions/accepted/ADR-001-vanilla-js-itcss (ana kural)
    │
    ├─► architecture/l3-presentation (L3 katmanı)
    │
    ├─► ui-design/ (UI design dosyaları)
    │
    ├─► assets.coremusic.net/Css/ (CSS dosyaları)
    │
    └─► assets.coremusic.net/js/ (JS dosyaları)
```

---

## 25. Approval

| Rol | Kişi | Onay | Tarih |
|-----|------|------|-------|
| Frontend Architect | [İsim] | ✅/❌ | YYYY-MM-DD |
| UI Designer | [İsim] | ✅/❌ | YYYY-MM-DD |
| Security Engineer | [İsim] | ✅/❌ | YYYY-MM-DD |
| Vault Steward | [İsim] | ✅/❌ | YYYY-MM-DD |

---

*CoreMusic ADR Frontend Template v1.0.0 — 2026-08-07*
*Authority: Vault Steward*
*Governance: Red Team • Human Mode • Truth Mode*
