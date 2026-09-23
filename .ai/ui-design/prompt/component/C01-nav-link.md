---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Nav Link Component Prompt"
type: prompt
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
component: C01
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Nav Link Component Prompt (C01)

## AI Code Generation Prompt

### Context
CoreMusic navigasyon linki bileşeni. Header ve bottom tab'da kullanılır.

### Required Inputs
- `label`: Link metni
- `icon`: İkon (opsiyonel)
- `active`: Aktif durum (boolean)
- `href`: Yönlendirme URL'i

### ASCII Reference
```
Default:     [Ana Sayfa]           opacity: 0.7, font-weight: 400
Hover:       [Ana Sayfa]           opacity: 1, color: #ff4fd8, underline
Active:      [Ana Sayfa]           opacity: 1, font-weight: 600, border-bottom
Focus:       [Ana Sayfa]           outline: 2px solid #ff4fd8
```

### Prompt Template
```json
{
  "task": "Create nav link component for CoreMusic",
  "component": "C01",
  "bem": ".nav-link",
  "states": ["default", "hover", "active", "focus", "disabled"],
  "tokens": {
    "--cm-primary": "#ff4fd8",
    "--cm-text-secondary": "#b0b0c0",
    "--cm-font-size-base": "14px",
    "--cm-spacing-md": "16px"
  }
}
```

### Expected Output
```html
<a class="nav-link nav-link--active" href="/home">
  <span class="nav-link__icon">🏠</span>
  <span class="nav-link__label">Ana Sayfa</span>
</a>
```

```css
.nav-link {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 16px;
  color: var(--cm-text-secondary);
  text-decoration: none;
  font-size: 14px;
  font-weight: 400;
  transition: color 0.2s ease;
}
.nav-link:hover { color: var(--cm-primary); }
.nav-link--active { color: var(--cm-primary); font-weight: 600; border-bottom: 2px solid var(--cm-primary); }
.nav-link:focus-visible { outline: 2px solid var(--cm-primary); outline-offset: 2px; }
```

### Validation
- [ ] Min height 44px (touch target)
- [ ] Focus visible outline
- [ ] Active state clear
- [ ] Hover transition smooth
