---
title: "C11 — Genre Tabs Component Prompt"
type: component-prompt
category: ui-design
component_id: "C11"
bem_class: ".genre-tabs"
itcss_layer: "04_Components"
target_file: "css/04_Components/_genre-tabs.css"
version: 1.0.0
status: active
author: "UI Designer Agent"
---

# C11 — Genre Tabs (.genre-tabs)

## 1. Bileşen Tanımı

| Özellik | Değer |
|---------|-------|
| **BEM Class** | `.genre-tabs` |
| **ITCSS Layer** | `04_Components` |
| **Hedef Dosya** | `css/04_Components/_genre-tabs.css` |
| **Kullanım Alanı** | Müzik türü filtreleme (Pop, Rock, Jazz, vb.) |
| **Bileşen Türü** | Horizontal scrollable tab bar |

## 2. PNG Ölçümleri

| Özellik | Değer | Not |
|---------|-------|-----|
| Tab yüksekliği | ~32px | ⚠️ WCAG: 48px minimum |
| Border-radius | 20px | `--radius-pill` (tam yuvarlak) |
| Padding | 0 16px | Yatay padding |
| Gap | 8px | Tab'lar arası |
| Font boyutu | 13px | Tab text |
| Tab genişliği | Auto | İçerik genişliğinde |

## 3. Token Referansları

| Token | Amaç | Kullanım |
|-------|------|----------|
| `--theme-primary` | Aktif tab bg | Seçili tab arka planı |
| `--radius-pill` | Tab border radius | Tam yuvarlak |
| `--color-text` | Aktif tab text | Beyaz text |
| `--color-text-muted` | Pasif tab text | Normal text |
| `--glass-bg` | Pasif tab bg | Glassmorphism |
| `--font-size-sm` | Font boyutu | `13px` |
| `--space-2` | Tab gap | `8px` |
| `--transition-fast` | Animasyon | `150ms ease` |

## 4. WCAG Gereksinimleri

| Kriter | Değer | Durum |
|--------|-------|-------|
| **Minimum yükseklik** | 44px | ⚠️ 32px → 48px'e genişletilmeli |
| **Touch target** | 44×44px | ⚠️ Padding ile genişletilmeli |
| **Role** | `tablist` | Container için |
| **Tab role** | `tab` | Her tab için |
| **Selected** | `aria-selected` | Seçili tab |
| **Keyboard** | Arrow keys | Tab'lar arası gezinme |
| **Focus indicator** | visible | Tab outline |

## 5. CSS Üretim Talimatları

```css
/* ============================================
   C11 — Genre Tabs
   ITCSS: 04_Components
   BEM: .genre-tabs
   ============================================ */

.genre-tabs {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  scrollbar-width: none;
  padding: 8px 0; /* Vertical padding for WCAG hit area */

  /* Hide scrollbar */
  -ms-overflow-style: none;
}

.genre-tabs::-webkit-scrollbar {
  display: none;
}

/* Individual tab */
.genre-tab {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  height: 32px;
  padding: 0 16px;
  border: none;
  border-radius: var(--radius-pill);
  background: var(--glass-bg);
  cursor: pointer;

  /* Typography */
  font-family: var(--font-body);
  font-size: var(--font-size-sm);
  font-weight: 500;
  color: var(--color-text-muted);
  white-space: nowrap;

  /* WCAG: minimum hit area via padding */
  min-height: 44px;

  /* Transition */
  transition: background-color var(--transition-fast),
              color var(--transition-fast);

  /* Accessibility */
  -webkit-tap-highlight-color: transparent;
  user-select: none;
}

/* Hover */
.genre-tab:hover {
  background: rgba(255, 255, 255, 0.1);
  color: var(--color-text);
}

/* Selected / Active */
.genre-tab[aria-selected="true"],
.genre-tab.is-active {
  background: var(--theme-primary);
  color: var(--color-white);
}

/* Focus visible */
.genre-tab:focus-visible {
  outline: 2px solid var(--theme-primary);
  outline-offset: 2px;
}

/* Count badge */
.genre-tab__count {
  margin-left: 6px;
  padding: 1px 6px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.15);
  font-size: 10px;
  font-weight: 600;
  line-height: 1.4;
}

.genre-tab.is-active .genre-tab__count {
  background: rgba(255, 255, 255, 0.25);
}

/* Scroll fade indicators */
.genre-tabs-wrapper {
  position: relative;
}

.genre-tabs-wrapper::before,
.genre-tabs-wrapper::after {
  content: "";
  position: absolute;
  top: 0;
  bottom: 0;
  width: 32px;
  pointer-events: none;
  z-index: 1;
}

.genre-tabs-wrapper::before {
  left: 0;
  background: linear-gradient(to right, var(--bg-primary), transparent);
}

.genre-tabs-wrapper::after {
  right: 0;
  background: linear-gradient(to left, var(--bg-primary), transparent);
}
```

## 6. Notlar

- Tab yüksekliği 32px, WCAG 48px standardının altındadır → `min-height: 48px` ile düzeltilmeli
- `padding: 8px 0` ile vertical hit area genişletilir
- Scrollable tab bar: overflow-x ile yatay kaydırma
- `role="tablist"` ve `role="tab"` screen reader desteği için zorunlu
- Arrow keys ile tab'lar arası gezinme JS ile sağlanmalıdır
