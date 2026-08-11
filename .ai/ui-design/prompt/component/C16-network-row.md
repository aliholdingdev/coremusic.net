---
title: "C16 — Network Row Component Prompt"
type: component-prompt
category: ui-design
component_id: "C16"
bem_class: ".network-row"
itcss_layer: "04_Components"
target_file: "css/04_Components/_network-row.css"
version: 1.0.0
status: active
author: "UI Designer Agent"
---

# C16 — Network Row (.network-row)

## 1. Bileşen Tanımı

| Özellik | Değer |
|---------|-------|
| **BEM Class** | `.network-row` |
| **ITCSS Layer** | `04_Components` |
| **Hedef Dosya** | `css/04_Components/_network-row.css` |
| **Kullanım Alanı** — WiFi/BT connection listesinde her satır |
| **Bileşen Türü** — Network/device list item |

## 2. PNG Ölçümleri

| Özellik | Değer | Not |
|---------|-------|-----|
| Row yüksekliği | ~48px | ✅ WCAG compliant |
| Row padding | 12px 16px | İç padding |
| Icon boyutu | 20×20px | WiFi/BT icon |
| Name alan | flex: 1 | Ağ adı |
| Status genişliği | auto | Bağlantı durumu |
| Signal genişliği | 20×16px | Sinyal gücü |
| Gap | 12px | Elemanlar arası |
| Border-radius | 8px | Row corner |

## 3. Token Referansları

| Token | Amaç | Kullanım |
|-------|------|----------|
| `--glass-bg` | Row arka plan | Glassmorphism |
| `--glass-border` | Row kenarlık | Glass border |
| `--color-text` | Ağ adı rengi | Primary text |
| `--color-text-muted` | Status rengi | Secondary text |
| `--color-success` | Bağlı durum | Yeşil indicator |
| `--color-warning` | Şifre gerekli | Sarı indicator |
| `--radius-md` | Border radius | `8px` |
| `--font-size-sm` | Font boyutu | `14px` |
| `--transition-fast` | Animasyon | `150ms ease` |

## 4. WCAG Gereksinimleri

| Kriter | Değer | Durum |
|--------|-------|-------|
| **Minimum yükseklik** | 44px | ✅ 48px |
| **Touch target** | 44×44px | ✅ 48px height |
| **Role** | `listitem` | Her row için |
| **List role** | `list` | Container için |
| **Icon label** | `aria-label` | WiFi/BT icon için |
| **Status text** | Screen reader | "Bağlı" / "Şifre gerekli" |
| **Connect button** | `aria-label` | "Bağlan" |
| **Focus indicator** | visible | Row outline |

## 5. CSS Üretim Talimatları

```css
/* ============================================
   C16 — Network Row
   ITCSS: 04_Components
   BEM: .network-row
   ============================================ */

.network-list {
  display: flex;
  flex-direction: column;
  gap: 4px;
  width: 100%;
}

/* Individual network row */
.network-row {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  min-height: 48px;
  border-radius: var(--radius-md);
  background: var(--glass-bg);
  border: 1px solid transparent;
  cursor: pointer;

  /* Transition */
  transition: background-color var(--transition-fast),
              border-color var(--transition-fast);

  /* Accessibility */
  -webkit-tap-highlight-color: transparent;
}

/* Hover */
.network-row:hover {
  background: rgba(255, 255, 255, 0.08);
  border-color: var(--glass-border);
}

/* Selected / Connected */
.network-row.is-connected {
  background: rgba(var(--theme-primary-rgb), 0.08);
  border-color: rgba(var(--theme-primary-rgb), 0.3);
}

/* Icon */
.network-row__icon {
  width: 20px;
  height: 20px;
  flex-shrink: 0;
  color: var(--color-text-muted);
}

.network-row.is-connected .network-row__icon {
  color: var(--theme-primary);
}

/* Name */
.network-row__name {
  flex: 1;
  font-family: var(--font-body);
  font-size: var(--font-size-sm);
  font-weight: 500;
  color: var(--color-text);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Signal strength */
.network-row__signal {
  display: flex;
  align-items: center;
  gap: 4px;
}

.network-row__signal-bars {
  display: flex;
  align-items: flex-end;
  gap: 2px;
  height: 16px;
}

.network-row__signal-bar {
  width: 3px;
  border-radius: 1px;
  background: var(--border-subtle);
}

.network-row__signal-bar:nth-child(1) { height: 4px; }
.network-row__signal-bar:nth-child(2) { height: 8px; }
.network-row__signal-bar:nth-child(3) { height: 12px; }
.network-row__signal-bar:nth-child(4) { height: 16px; }

/* Active signal bars */
.network-row__signal-bar.is-active {
  background: var(--color-success);
}

/* Status */
.network-row__status {
  font-size: 12px;
  color: var(--color-text-muted);
  flex-shrink: 0;
}

.network-row__status--connected {
  color: var(--color-success);
}

.network-row__status--secured {
  display: flex;
  align-items: center;
  gap: 4px;
}

.network-row__status--secured svg {
  width: 12px;
  height: 12px;
}

/* Connect button */
.network-row__connect {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 8px 16px;
  border: 1px solid var(--theme-primary);
  border-radius: 6px;
  background: transparent;
  color: var(--theme-primary);
  font-family: var(--font-body);
  font-size: 12px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 150ms, color 150ms;
  flex-shrink: 0;
}

.network-row__connect:hover {
  background: var(--theme-primary);
  color: var(--color-white);
}

/* Focus visible */
.network-row:focus-visible {
  outline: 2px solid var(--theme-primary);
  outline-offset: -2px;
}

/* Disabled */
.network-row.is-disabled {
  opacity: 0.5;
  cursor: not-allowed;
  pointer-events: none;
}

/* Responsive */
@media (max-width: 768px) {
  .network-row {
    padding: 12px;
  }

  .network-row__signal { display: none; }
}
```

## 6. Notlar

- Row yüksekliği 48px ile WCAG standardına uygun
- WiFi/BT icon'ları inline SVG veya icon font olabilir
- Signal bars: 4 bar, soldan sağa yükselen (4px, 8px, 12px, 16px)
- Bağlı durumda `is-connected` class'ı ile vurgulanır
- Lock icon, şifreli ağlar için gösterilir
- Connect button, bağlı olmayan ağlar için görünür
