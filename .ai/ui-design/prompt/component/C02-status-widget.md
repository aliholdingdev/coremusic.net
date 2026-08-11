---
title: "C02 — Status Widget Component Prompt"
type: component-prompt
category: ui-design
component_id: "C02"
bem_class: ".header-widget"
itcss_layer: "03_Layout"
target_file: "css/03_Layout/_header-widget.css"
version: 1.0.0
status: active
author: "UI Designer Agent"
---

# C02 — Status Widget (.header-widget)

## 1. Bileşen Tanımı

| Özellik | Değer |
|---------|-------|
| **BEM Class** | `.header-widget` |
| **ITCSS Layer** | `03_Layout` |
| **Hedef Dosya** | `css/03_Layout/_header-widget.css` |
| **Kullanım Alanı** | Header'da WiFi + Bluetooth + Battery durum göstergesi |
| **Bileşen Türü** | Status indicator pill |

## 2. PNG Ölçümleri

| Özellik | Değer | Not |
|---------|-------|-----|
| WiFi + BT pill | 65×37.4px | Glassmorphism arka plan |
| Battery pill | ~100px genişlik | Yatay pill |
| Pill border-radius | `--radius-pill` | Tam yuvarlak |
| Glassmorphism blur | `backdrop-filter: blur(10px)` | Glass efekti |
| Gap (pill'ler arası) | 8px | Yatay boşluk |

## 3. Token Referansları

| Token | Amaç | Kullanım |
|-------|------|----------|
| `--glass-bg` | Glassmorphism arka plan | `background: rgba(255,255,255,0.1)` |
| `--glass-border` | Glass kenarlık | `border: 1px solid rgba(255,255,255,0.15)` |
| `--radius-pill` | Tam yuvarlak | `border-radius: 100px` |
| `--color-success` | WiFi aktif | Yeşil gösterge |
| `--color-info` | Bluetooth aktif | Mavi gösterge |
| `--color-warning` | Battery düşük | Sarı gösterge |
| `--space-2` | Pill'ler arası gap | `8px` |
| `--blur-sm` | Backdrop blur | `blur(10px)` |

## 4. WCAG Gereksinimleri

| Kriter | Değer | Durum |
|--------|-------|-------|
| **Touch target** | ≥44×44px | Pill boyutu uygun |
| **Renk kontrastı** | ≥3:1 | Non-text elements için |
| **Icon labels** | `aria-label` zorunlu | WiFi/BT/Battery icon'ları için |
| **Screen reader** | Status durumu text olarak | `role="status"` |
| **Focus indicator** | Gerekmez | Non-interactive element |

## 5. CSS Üretim Talimatları

```css
/* ============================================
   C02 — Status Widget
   ITCSS: 03_Layout
   BEM: .header-widget
   ============================================ */

.header-widget {
  display: flex;
  align-items: center;
  gap: var(--space-2);
}

/* Individual status pill */
.header-widget__pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;

  /* Glassmorphism */
  background: var(--glass-bg);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border: var(--glass-border);

  /* Shape */
  border-radius: var(--radius-pill);

  /* Typography */
  font-family: var(--font-body);
  font-size: 11px;
  font-weight: 500;
  color: var(--color-text);

  /* Icon */
  line-height: 1;
}

.header-widget__pill svg,
.header-widget__pill img {
  width: 14px;
  height: 14px;
  flex-shrink: 0;
}

/* Status indicators */
.header-widget__pill--wifi { }
.header-widget__pill--bluetooth { }
.header-widget__pill--battery { }

.header-widget__pill--wifi svg { color: var(--color-success); }
.header-widget__pill--bluetooth svg { color: var(--color-info); }

.header-widget__pill--battery svg { color: var(--color-warning); }

/* Battery level bar */
.header-widget__battery-bar {
  width: 24px;
  height: 8px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 2px;
  overflow: hidden;
}

.header-widget__battery-fill {
  height: 100%;
  background: var(--color-success);
  border-radius: 2px;
  transition: width 300ms ease;
}

/* Responsive — compact mode */
@media (max-width: 768px) {
  .header-widget__pill {
    padding: 4px 8px;
    font-size: 10px;
  }
  .header-widget__pill svg { width: 12px; height: 12px; }
}
```

## 6. Notlar

- Glassmorphism efekti için `backdrop-filter` desteklenmeyen tarayıcılarda solid fallback gerekli
- WiFi/BT/Battery icon'ları inline SVG veya icon font olabilir
- Battery seviyesi `data-level` attribute'u ile dinamik olarak güncellenir
- `role="status"` ve `aria-live="polite"` screen reader desteği için zorunludur
