---
title: "CoreMusic — C16 Network Row Prompt"
type: prompt
category: ui-design
component_id: "C16"
bem_class: ".network-row"
itcss_layer: "04_Components"
date: 2026-09-20
version: 2.0.0
status: active
---

# C16 — Network Row (.network-row)

## 1. Component Definition

| Property | Value |
|----------|-------|
| **BEM Class** | `.network-row` |
| **ITCSS Layer** | `04_Components` |
| **Target File** | `css/04_Components/_network-row.css` |
| **Usage** | WiFi/BT connection listesinde her satır |
| **Type** | Network/device list item |

## 2. ASCII Wireframe

```
┌─── NETWORK ROW (WiFi) ────────────────────────────────┐
│  📶  MyHomeWiFi        ▮▮▮▯  🔒    [Bağlan]          │
│  icon  name             signal lock   connect btn     │
│  20px  flex:1           bars   12px   outline btn     │
│  min-height: 48px                                      │
└────────────────────────────────────────────────────────┘

┌─── NETWORK ROW (connected) ───────────────────────────┐
│  📶  MyHomeWiFi ✓       ▮▮▮▮  🔒                     │
│  icon primary   status   full  lock                    │
│  bg: primary×0.08, border: primary×0.3                 │
└────────────────────────────────────────────────────────┘
```

## 3. Tier Sizes

| Tier | Row Height | Icon | Font |
|------|------------|------|------|
| Phone | 48px | 18px | 13px |
| Embedded | 48px | 20px | 14px |
| Desktop | 48px | 20px | 14px |
| 4K TV | 56px | 24px | 16px |

## 4. Variants

| Variant | Class | Description |
|---------|-------|-------------|
| WiFi | `.network-row--wifi` | WiFi ağı |
| Bluetooth | `.network-row--bluetooth` | BT cihazı |
| Connected | `.network-row.is-connected` | Bağlı durum |
| Secured | `.network-row__status--secured` | Şifreli |

## 5. States

| State | Visual |
|-------|--------|
| Default | Glass bg, transparent border |
| Hover | bg: white×0.08, border: glass-border |
| Connected | bg: primary×0.08, border: primary×0.3, icon: primary |
| Disabled | opacity: 0.5, pointer-events: none |

## 6. Accessibility

| Criterion | Requirement |
|-----------|-------------|
| Role | `role="listitem"` on row, `list` on container |
| Icon label | `aria-label="WiFi: Bağlı"` |
| Status text | Screen reader: "Bağlı" / "Şifre gerekli" |
| Connect btn | `aria-label="Bağlan"` |
| Focus | 2px outline on row |
| Touch target | min 44×44px (48px height) |

## 7. Code Example

```css
/* C16 — Network Row | ITCSS: 04_Components */
.network-list { display: flex; flex-direction: column; gap: 4px; width: 100%; }

.network-row {
  display: flex; align-items: center; gap: 12px;
  padding: 12px 16px; min-height: 48px;
  border-radius: var(--radius-md);
  background: var(--glass-bg);
  border: 1px solid transparent;
  cursor: pointer;
  transition: background-color var(--transition-fast), border-color var(--transition-fast);
}

.network-row:hover { background: rgba(255, 255, 255, 0.08); border-color: var(--glass-border); }
.network-row.is-connected { background: rgba(var(--theme-primary-rgb), 0.08); border-color: rgba(var(--theme-primary-rgb), 0.3); }

.network-row__icon { width: 20px; height: 20px; flex-shrink: 0; color: var(--color-text-muted); }
.network-row.is-connected .network-row__icon { color: var(--theme-primary); }

.network-row__name { flex: 1; font-size: var(--font-size-sm); font-weight: 500; color: var(--color-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

.network-row__signal { display: flex; align-items: center; gap: 4px; }
.network-row__signal-bars { display: flex; align-items: flex-end; gap: 2px; height: 16px; }
.network-row__signal-bar { width: 3px; border-radius: 1px; background: var(--border-subtle); }
.network-row__signal-bar:nth-child(1) { height: 4px; }
.network-row__signal-bar:nth-child(2) { height: 8px; }
.network-row__signal-bar:nth-child(3) { height: 12px; }
.network-row__signal-bar:nth-child(4) { height: 16px; }
.network-row__signal-bar.is-active { background: var(--color-success); }

.network-row__status { font-size: 12px; color: var(--color-text-muted); flex-shrink: 0; }
.network-row__status--connected { color: var(--color-success); }

.network-row__connect {
  display: flex; align-items: center; justify-content: center;
  padding: 8px 16px;
  border: 1px solid var(--theme-primary); border-radius: 6px;
  background: transparent; color: var(--theme-primary);
  font-size: 12px; font-weight: 500; cursor: pointer;
  transition: background-color 150ms, color 150ms; flex-shrink: 0;
}

.network-row__connect:hover { background: var(--theme-primary); color: var(--color-white); }
.network-row:focus-visible { outline: 2px solid var(--theme-primary); outline-offset: -2px; }
.network-row.is-disabled { opacity: 0.5; cursor: not-allowed; pointer-events: none; }
```

```html
<div class="network-list" role="list" aria-label="WiFi ağları">
  <div class="network-row is-connected" role="listitem">
    <span class="network-row__icon" aria-hidden="true">📶</span>
    <span class="network-row__name">MyHomeWiFi</span>
    <div class="network-row__signal" aria-label="Sinyal gücü: Güçlü">
      <div class="network-row__signal-bar is-active"></div>
      <div class="network-row__signal-bar is-active"></div>
      <div class="network-row__signal-bar is-active"></div>
      <div class="network-row__signal-bar is-active"></div>
    </div>
    <span class="network-row__status network-row__status--connected">✓ Bağlı</span>
  </div>
  <div class="network-row" role="listitem">
    <span class="network-row__icon" aria-hidden="true">📶</span>
    <span class="network-row__name">NeighborWiFi</span>
    <div class="network-row__signal">
      <div class="network-row__signal-bar is-active"></div>
      <div class="network-row__signal-bar is-active"></div>
      <div class="network-row__signal-bar"></div>
      <div class="network-row__signal-bar"></div>
    </div>
    <span class="network-row__status">🔒</span>
    <button class="network-row__connect" aria-label="NeighborWiFi ağına bağlan">Bağlan</button>
  </div>
</div>
```

---

*C16 Network Row v2.0.0 — CoreMusic UI Design System*
