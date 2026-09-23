---
title: "CoreMusic — C02 Status Widget Prompt"
type: prompt
category: ui-design
component_id: "C02"
bem_class: ".header-widget"
itcss_layer: "03_Layout"
date: 2026-09-20
version: 2.0.0
status: active
---

# C02 — Status Widget (.header-widget)

## 1. Component Definition

| Property | Value |
|----------|-------|
| **BEM Class** | `.header-widget` |
| **ITCSS Layer** | `03_Layout` |
| **Target File** | `css/03_Layout/_header-widget.css` |
| **Usage** | Header'da WiFi + Bluetooth + Battery durum göstergesi |
| **Type** | Status indicator pill |

## 2. ASCII Wireframe

```
┌─── STATUS WIDGET GROUP ─────────────────────┐
│  ┌──────────┐ ┌──────────┐ ┌────────────┐  │
│  │ 📶 WiFi  │ │ ✳ BT     │ │ 🔋 %100    │  │
│  │ 65×37px  │ │ 65×37px  │ │ 100×37px   │  │
│  │ glass bg │ │ glass bg │ │ glass bg   │  │
│  └──────────┘ └──────────┘ └────────────┘  │
│  gap: 8px between pills                     │
└─────────────────────────────────────────────┘
```

## 3. Tier Sizes

| Tier | Pill Height | Font Size | Icon Size |
|------|-------------|-----------|-----------|
| Phone | 32px | 10px | 12px |
| Embedded | 37px | 11px | 14px |
| Desktop | 37px | 11px | 14px |
| 4K TV | 44px | 13px | 16px |

## 4. Variants

| Variant | Class | Description |
|---------|-------|-------------|
| WiFi | `.header-widget--wifi` | WiFi sinyal durumu |
| Bluetooth | `.header-widget--bluetooth` | Bluetooth bağlantı durumu |
| Battery | `.header-widget--battery` | Pil seviyesi + bar |
| Combined | `.header-widget--combined` | WiFi+BT tek pill |

## 5. States

| State | Visual |
|-------|--------|
| Connected | Yeşil icon, normal glass bg |
| Disconnected | Gri icon, low opacity |
| Warning (battery) | Sarı icon, pulse animation |
| Critical (battery <10%) | Kırmızı icon, blink |

## 6. Accessibility

| Criterion | Requirement |
|-----------|-------------|
| Touch target | ≥44×44px |
| Screen reader | `role="status"`, `aria-live="polite"` |
| Icon labels | `aria-label="WiFi: Bağlı"`, `aria-label="Bluetooth: Kapalı"` |
| Color alone | Icon + text birlikte (sadece renk yeterli değil) |

## 7. Code Example

```css
/* C02 — Status Widget | ITCSS: 03_Layout */
.header-widget {
  display: flex;
  align-items: center;
  gap: var(--space-2);
}

.header-widget__pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  background: var(--glass-bg);
  backdrop-filter: blur(10px);
  border: var(--glass-border);
  border-radius: var(--radius-pill);
  font-family: var(--font-body);
  font-size: 11px;
  font-weight: 500;
  color: var(--color-text);
  line-height: 1;
}

.header-widget__pill svg {
  width: 14px;
  height: 14px;
  flex-shrink: 0;
}

.header-widget__pill--wifi svg { color: var(--color-success); }
.header-widget__pill--bluetooth svg { color: var(--color-info); }
.header-widget__pill--battery svg { color: var(--color-warning); }

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
```

```html
<div class="header-widget" role="status" aria-live="polite">
  <div class="header-widget__pill header-widget__pill--wifi" aria-label="WiFi: Bağlı">
    <svg>...</svg> WiFi
  </div>
  <div class="header-widget__pill header-widget__pill--bluetooth" aria-label="Bluetooth: Kapalı">
    <svg>...</svg> BT
  </div>
  <div class="header-widget__pill header-widget__pill--battery" aria-label="Pil: %100">
    <svg>...</svg>
    <div class="header-widget__battery-bar">
      <div class="header-widget__battery-fill" style="width: 100%"></div>
    </div>
  </div>
</div>
```

---

*C02 Status Widget v2.0.0 — CoreMusic UI Design System*
