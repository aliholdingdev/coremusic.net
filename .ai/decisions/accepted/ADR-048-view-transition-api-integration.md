---
type: adr
category: ui
title: "ADR-048: View Transition API Integration"
date: 2026-08-03
updated: 2026-08-08
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-048: View Transition API Integration

**Status:** Active (güncellenebilir)
**Kategorisi:** UI Animation
**İlgili Agent:** [[.agents/ui-designer]]
**İlgili Division:** Software Division

---

## 1. Amaç

Bu ADR, CoreMusic platformundaki View Transition API entegrasyonunu, fallback stratejisini, animation kurallarını ve view mode geçişlerindeki akıcı animasyon mekanizmasını tanımlar.

CoreMusic'in View Transition API hedefi:
- Akıcı geçişler: View mode değişikliklerinde animasyon
- Fallback: CSS animations (eski tarayıcılar)
- Reduced motion: Engelli kullanıcı desteği
- Performans: 60fps hedef
- Tutarlılık: Tüm view mode geçişlerinde benzer deneyim

---

## 2. Bağlam

### 2.1 Mevcut Durum

CoreMusic'te view mode değişiklikleri (ADR-045) mevcuttur:
- Home ↔ Pro ↔ Studio ↔ Car
- Default ↔ Herhangi bir mode

Bu geçişler şu anda anlık (animasyonsuz) gerçekleşmektedir. Kullanıcı deneyimini iyileştirmek için akıcı geçişler gerekli.

### 2.2 View Transition API Nedir?

View Transition API, tarayıcı tarafından sağlanan.animasyon mekanizmasıdır:
- `document.startViewTransition()` ile sarılır
- DOM değişikliği öncesi ve sonrası snapshots alınır
- Tarayıcı otomatik crossfade animasyonu yapar
- `@view-transition` CSS ile özelleştirilebilir

### 2.3 Gereksinimler

| # | Gereksinim | Değer | Kaynak |
|---|------------|-------|--------|
| R1 | View Transition API | Tarayıcı API | ADR-048 |
| R2 | Fallback | CSS animations | ADR-048 |
| R3 | Reduced motion | prefers-reduced-motion | ADR-048 |
| R4 | Performans | 60fps | ADR-048 |
| R5 | Cross-browser | Chrome, Firefox, Safari, Edge | ADR-048 |
| R6 | Tema geçişi | Theme engine ile entegrasyon | ADR-044 |
| R7 | View mode geçişi | Multi-domain view | ADR-045 |
| 8 | Sayfa içi geçiş | SPA route geçişi | ADR-021 |

### 2.4 Kısıtlamalar

| # | Kısıt | Açıklama |
|---|-------|----------|
| C1 | Framework yasak | Vanilla JS (ADR-001) |
| C2 | Performans | 16ms/frame (60fps) |
| C3 | Memory | Snapshot bellek kullanımı |
| C4 | Eski tarayıcı | Fallback zorunlu |
| C5 | Reduced motion | Engelli kullanıcı |

---

## 3. Karar

CoreMusic'te **View Transition API** kullanılacak.

### 3.1 API Kullanımı

```javascript
// View Transition ile geçiş
if (document.startViewTransition) {
    document.startViewTransition(() => {
        // DOM değişikliği
        updateView(newMode);
    });
} else {
    // Fallback: anlık geçiş
    updateView(newMode);
}
```

### 3.2 Fallback Stratejisi

| Tarayıcı | View Transition | Fallback | Durum |
|----------|----------------|----------|-------|
| Chrome 111+ | ✅ | — | ✅ |
| Firefox | ❌ | CSS animation | ✅ |
| Safari 18+ | ✅ | — | ✅ |
| Safari < 18 | ❌ | CSS animation | ✅ |
| Edge 111+ | ✅ | — | ✅ |
| IE 11 | ❌ | Anlık geçiş | ⚠️ |

### 3.3 Reduced Motion Desteği

```css
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
```

---

## 4. Teknik Detaylar

### 4.1 View Transition CSS

```css
/* View Transition tanımları */
::view-transition-old(root) {
    animation: fade-out 300ms ease-in;
}

::view-transition-new(root) {
    animation: fade-in 300ms ease-out;
}

/* View mode'a özel geçişler */
::view-transition-old(view-mode) {
    animation: slide-left 300ms ease-in;
}

::view-transition-new(view-mode) {
    animation: slide-right 300ms ease-out;
}

/* Animasyon tanımları */
@keyframes fade-out {
    from { opacity: 1; }
    to { opacity: 0; }
}

@keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slide-left {
    from { transform: translateX(0); }
    to { transform: translateX(-100%); }
}

@keyframes slide-right {
    from { transform: translateX(100%); }
    to { transform: translateX(0); }
}
```

### 4.2 CSS Fallback Animations

```css
/* View Transition desteklemeyen tarayıcılar için */
.view-mode-transition {
    transition: all 300ms ease;
}

.view-mode-transition.fade {
    animation: fade-transition 300ms ease;
}

@keyframes fade-transition {
    0% { opacity: 1; transform: scale(1); }
    50% { opacity: 0; transform: scale(0.98); }
    100% { opacity: 1; transform: scale(1); }
}

.view-mode-transition.slide {
    animation: slide-transition 300ms ease;
}

@keyframes slide-transition {
    0% { transform: translateY(0); }
    50% { transform: translateY(10px); }
    100% { transform: translateY(0); }
}
```

### 4.3 ViewModeManager Entegrasyonu

```javascript
const ViewModeManager = {
    switch(newMode) {
        const validModes = ['home', 'pro', 'studio', 'car', 'default'];
        if (!validModes.includes(newMode)) return;

        if (document.startViewTransition) {
            document.startViewTransition(() => {
                this.apply(newMode);
            });
        } else {
            // Fallback: CSS animation class ekle
            document.documentElement.classList.add('view-mode-transition');
            this.apply(newMode);
            setTimeout(() => {
                document.documentElement.classList.remove('view-mode-transition');
            }, 300);
        }
    }
};
```

### 4.4 Named Transitions

```javascript
// Farklı geçiş türleri için named transitions
document.startViewTransition({
    update: () => updateDOM(),
    types: ['slide-left']
});
```

```css
::view-transition-old(slide-left) {
    animation: slide-out-left 300ms ease-in;
}

::view-transition-new(slide-left) {
    animation: slide-in-right 300ms ease-out;
}
```

### 4.5 Performans Metrikleri

| Metrik | Hedef | Ölçüm |
|--------|-------|-------|
| Animation FPS | 60fps | requestAnimationFrame |
| Geçiş süresi | 300ms | CSS animation |
| Snapshot alma | < 16ms | Performance.now() |
| DOM güncelleme | < 16ms | requestAnimationFrame |
| Memory kullanımı | < 10MB | Performance.memory |

### 4.6 Cross-Browser CSS

```css
/* Modern tarayıcılar */
@supports (animation-timeline: scroll()) {
    .scroll-driven {
        animation: fade-in linear;
        animation-timeline: scroll();
    }
}

/* Eski tarayıcılar */
@supports not (animation-timeline: scroll()) {
    .scroll-driven {
        animation: fade-in 300ms ease;
    }
}
```

---

## 5. Yasak Örüntüler

| # | Yasak | Doğru | Kaynak |
|---|-------|-------|--------|
| 1 | Framework kullanımı | Vanilla JS | ADR-001 |
| 2 | heavy animasyon | Hafif, 300ms altı | ADR-048 |
| 3 | Reduced motion yok | prefers-reduced-motion | WCAG |
| 4 | Senkron DOM | Async update | ADR-048 |
| 5 | Büyük snapshot | Minimal DOM | ADR-048 |
| 6 | var kullanımı | const/let | ADR-001 |
| 7 | Callback hell | Async/await | ADR-001 |
| 8 | Memory leak | Cleanup fonksiyonu | ADR-048 |

---

## 6. Edge Cases

| # | Edge Case | Tetikleyici | Çözüm | ADR |
|---|-----------|-------------|-------|-----|
| 1 | Eski tarayıcı | View Transition yok | CSS fallback | ADR-048 |
| 2 | Reduced motion | Engelli kullanıcı | Anlık geçiş | WCAG |
| 3 | Memory pressure | Çok fazla snapshot | Minimal DOM | ADR-048 |
| 4 | Hızlı geçiş | Çoklu tıklama | Debounce (300ms) | ADR-048 |
| 5 | View mode change | Ani geçiş | Pre/post hook | ADR-045 |
| 6 | Theme change | Tema geçişi | Named transition | ADR-044 |
| 7 | SPA route | Sayfa içi geçiş | Route transition | ADR-021 |
| 8 | High DPI | retina ekran | GPU acceleration | ADR-048 |
| 9 | Low-end device | Yavaş CPU | Simplified animation | ADR-048 |
| 10 | Print mode | Yazdırma | Animation disable | ADR-048 |

---

## 7. Hard Guardrails

| # | Guardrail | Uygulama | İhlal Sonucu |
|---|-----------|----------|-------------|
| G1 | View Transition API | Modern tarayıcıda zorunlu | Kötü UX |
| G2 | CSS Fallback | Eski tarayıcı desteği | Kullanılamaz |
| G3 | Reduced motion | WCAG zorunlu | Yasal risk |
| G4 | 60fps | Performans hedefi | Görsel gecikme |
| G5 | 300ms max | Animasyon süresi | Yavaş his |
| G6 | Minimal DOM | Snapshot boyutu | Memory kullanımı |
| G7 | Debounce | Hızlı geçiş koruması | Çakışma |
| G8 | Cleanup | Memory leak önleme | Bellek sızıntısı |

---

## 8. İlgili ADR'ler

| ADR | Konu | İlişki |
|-----|------|--------|
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS | Frontend stack |
| [[ADR-021-spa-router-immutable-contract]] | SPA router | Route geçişleri |
| [[ADR-044-dynamic-user-theme-engine]] | Dynamic theme | Tema geçişi |
| [[ADR-045-multi-domain-view-mode-architecture]] | Multi-domain view | View mode geçişi |

---

## 9. Çapraz Referanslar

| Bölüm | Hedef Dosya | İlişki |
|-------|-------------|--------|
| § 3.1 | [[architecture/l3-presentation]] | JS/CSS kuralları |
| § 4.1 | [[ADR-045-multi-domain-view-mode-architecture]] | View mode |
| § 4.2 | [[ADR-044-dynamic-user-theme-engine]] | Tema engine |
| § 4.3 | [[assets.coremusic.net/Css/]] | CSS dosyaları |
| § 5 | [[brain.md]] §18 | Coding standards |
| § 6 | [[testing/coverage-targets]] | Test coverage |
| § 7 | [[CLAUDE.md]] §7 | Hard guardrails |

---

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **View Transition API** | Tarayıcı tabanlı geçiş animasyonu |
| **Named Transition** | İsimlendirilmiş geçiş |
| **Crossfade** | Çapraz solma animasyonu |
| **Snapshot** | DOM durumunun anlık görüntüsü |
| **Fallback** | Eski tarayıcı desteği |
| **Reduced Motion** | Engelli kullanıcı modu |
| **CSS Animation** | CSS tabanlı animasyon |
| **requestAnimationFrame** | Frame callback |
| **Debounce** | Çoklu tetiklemeyi tek işleme |
| **GPU Acceleration** | Grafik hızlandırma |
| **Memory Leak** | Bellek sızıntısı |
| **DOM Update** | Belge nesnes modeli güncelleme |
| **Animation Timeline** | Animasyon zaman çizelgesi |
| **Scroll-driven** | Kaydırma tabanlı animasyon |
| **Performance.now()** | Milisaniye hassasiyetinde zaman |

---

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| ADR Version | 2.0.0 |
| Status | Active · Red Team · Human Mode · Truth Mode verified |
| Last Updated | 2026-08-08 |
| Sections | 11 |
| API Desteği | View Transition API |
| Fallback | CSS animations |
| Cross-browser | 4 tarayıcı |
| Animasyon Süresi | 300ms |
| Performans Hedefi | 60fps |
| Reduced Motion | ✅ |
| Edge Cases | 10 |
| Hard Guardrails | 8 |
| Yasak Örüntü | 8 |
| İlgili ADR | 4 |
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
| Next Review | Yeni animasyon eklendiğinde |
| Related Division | Software Division |
| Risk Seviyesi | Düşük (UX) |

---

## 13. Deployment Considerations

| # | Konu | Detay |
|---|------|-------|
| 1 | CSS deploy | Animasyon dosyaları CDN üzerinden |
| 2 | JS deploy | View Transition API polyfill opsiyonel |
| 3 | Feature detection | startViewTransition kontrolü |
| 4 | Fallback test | Eski tarayıcı doğrulama |
| 5 | Performance audit | 60fps doğrulama |
| 6 | Reduced motion test | prefers-reduced-motion |
| 7 | Memory test | Snapshot bellek kullanımı |
| 8 | Cross-browser matrix | Chrome, Firefox, Safari, Edge |

---

## 14. Testing Strategy

| Test Türü | Kapsam | Framework |
|-----------|--------|-----------|
| Unit Test | ViewTransitionManager JS | Vitest |
| Visual Test | Geçiş animasyonları | Playwright screenshot |
| Performance Test | 60fps frame rate | Performance API |
| Accessibility Test | Reduced motion | Lighthouse |
| Cross-browser Test | Tarayıcı desteği | Playwright |
| Memory Test | Snapshot sızıntısı | Heap snapshot |
| E2E Test | View mode geçişi | Playwright |
| Regression Test | Eski animasyon bozulması | Playwright |

---

## 15. Risk Assessment

| # | Risk | Olasılık | Etki | Mitigasyon |
|---|------|----------|------|------------|
| R1 | Eski tarayıcı | Orta | Orta | CSS fallback |
| R2 | Reduced motion ihlali | Düşük | Yüksek | Media query |
| R3 | Memory leak | Düşük | Yüksek | Cleanup |
| R4 | Performance düşüşü | Düşük | Orta | Profiling |
| R5 | Snapshot boyutu | Düşük | Orta | Minimal DOM |
| R6 | Cross-browser tutarsız | Orta | Orta | Feature detection |
| R7 | Animasyon çakışması | Düşük | Düşük | Debounce |
| R8 | GPU acceleration | Düşük | Düşük | will-change |

---

## 16. Maintenance Guidelines

| # | Görev | Siklik | Sorumlu |
|---|-------|--------|---------|
| 1 | Performance audit | Aylık | QA Engineer |
| 2 | Cross-browser test | Yeni sürümde | QA Engineer |
| 3 | Memory leak check | Üç aylık | QA Engineer |
| 4 | Accessibility audit | Aylık | UI Designer |
| 5 | CSS optimization | Aylık | UI Designer |
| 6 | Animation review | Değişiklikte | UI Designer |

---

## 17. Future Considerations

| # | Konu | Durum | Notlar |
|---|------|-------|--------|
| 1 | Scroll-driven animations | Planlanıyor | CSS animation-timeline |
| 2 | View Transition Level 2 | Araştırılıyor | Güncel API |
| 3 | Custom easing | Planlanıyor | cubic-bezier özelleştirme |
| 4 | GPU layers | Optimize | will-change kullanımı |
| 5 | Animation presets | Planlanıyor | Hazır animasyon seti |
| 6 | Micro-interactions | Gelecek | Küçük geçiş efektleri |

---

## 18. Animation Pattern Library

| Pattern | Kullanım Alanı | Süre | Easing |
|---------|---------------|------|--------|
| fade-in | Yeni element | 300ms | ease-out |
| fade-out | Kaldırılan element | 200ms | ease-in |
| slide-left | View mode geçişi | 300ms | ease-in-out |
| slide-right | View mode geçişi | 300ms | ease-in-out |
| scale-in | Büyüyen element | 250ms | ease-out |
| scale-out | Küçülen element | 200ms | ease-in |
| blur-in | Odaklanma | 200ms | ease-out |
| blur-out | Odak kaybı | 150ms | ease-in |

---

## 19. Browser Support Matrix

| Tarayıcı | View Transition | Named Transition | CSS Animation | Fallback |
|----------|----------------|------------------|---------------|----------|
| Chrome 111+ | ✅ | ✅ | ✅ | — |
| Chrome < 111 | ❌ | ❌ | ✅ | CSS |
| Firefox 110+ | ❌ | ❌ | ✅ | CSS |
| Safari 18+ | ✅ | ✅ | ✅ | — |
| Safari < 18 | ❌ | ❌ | ✅ | CSS |
| Edge 111+ | ✅ | ✅ | ✅ | — |
| IE 11 | ❌ | ❌ | ✅ | Anlık |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode
