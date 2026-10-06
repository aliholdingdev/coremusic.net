---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — C06 Form Input Prompt"
type: prompt
category: ui-design
component_id: "C06"
bem_class: ".form-input"
itcss_layer: "04_Components"
date: 2026-09-20
version: 2.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
status: active
component: C06
---

# Form Input Component Prompt (C06)

## AI Code Generation Prompt

### Context

| Property | Value |
|----------|-------|
| **BEM Class** | `.form-input` |
| **ITCSS Layer** | `04_Components` |
| **Target File** | `css/04_Components/_form-input.css` |
| **Usage** | Login, register, search, tüm form alanları |
| **Type** | Text input with glass background |

### Required Inputs

| Tier | Height | Font | Icon Size |
|------|--------|------|-----------|
| Phone | 52px | 16px | 18px |
| Embedded | 56px | 16px | 20px |
| Desktop | 56px | 16px | 20px |
| 4K TV | 64px | 18px | 24px |

**Variants**

| Variant | Class | Description |
|---------|-------|-------------|
| Default | `.form-input` | Text input |
| With Icon | `.form-input--icon` | Left icon |
| Password | `.form-input--password` | Toggle show/hide |
| Error | `.form-input--error` | Red border + message |
| Success | `.form-input--success` | Green border |

**States**

| State | Visual |
|-------|--------|
| Default | Glass bg, border-subtle |
| Focus | border: theme-primary, 3px glow ring |
| Error | border: color-danger, error message below |
| Success | border: color-success |
| Disabled | opacity: 0.5, cursor: not-allowed |

### ASCII Reference

```
┌─── FORM INPUT (default) ────────────────────┐
│  E-posta adresiniz                           │
│  ┌──────────────────────────────────────────┐│
│  │  📧 ornek@email.com                      ││
│  │  h:56px, glass bg, border-subtle         ││
│  │  font: 16px (iOS zoom prevention)        ││
│  └──────────────────────────────────────────┘│
└──────────────────────────────────────────────┘

┌─── FORM INPUT (error) ──────────────────────┐
│  E-posta adresiniz *                         │
│  ┌──────────────────────────────────────────┐│
│  │  invalid-email                           ││
│  │  border: 1px solid var(--color-danger)   ││
│  └──────────────────────────────────────────┘│
│  ⚠ Geçerli bir e-posta girin               │
└──────────────────────────────────────────────┘
```

### Prompt Template

```json
{
  "task": "Create form input component for CoreMusic",
  "component": "C06",
  "bem": ".form-input",
  "states": [
    "default",
    "focus",
    "error",
    "success",
    "disabled"
  ],
  "tokens": {
    "--cm-radius-md": "8px",
    "--cm-glass-bg": "rgba(255, 255, 255, 0.05)",
    "--cm-border-subtle": "rgba(255, 255, 255, 0.06)",
    "--cm-error": "#ef4444",
    "--cm-success": "#10b981"
  }
}
```

### Expected Output

```css
/* C06 — Form Input | ITCSS: 04_Components */
.form-input { position: relative; width: 100%; }

.form-input__label {
  display: block;
  font-family: var(--font-body);
  font-size: 13px;
  font-weight: 500;
  color: var(--color-text-muted);
  margin-bottom: 6px;
}

.form-input__label--required::after { content: " *"; color: var(--color-danger); }

.form-input__field {
  width: 100%;
  height: 56px;
  padding: 0 16px;
  border: 1px solid var(--border-subtle);
  border-radius: var(--radius-md);
  background: var(--glass-bg);
  backdrop-filter: blur(10px);
  font-family: var(--font-body);
  font-size: 16px;
  color: var(--color-text);
  transition: border-color var(--transition-fast), box-shadow var(--transition-fast);
  outline: none;
}

.form-input__field::placeholder { color: var(--color-text-muted); opacity: 0.5; }
.form-input__field:focus { border-color: var(--theme-primary); box-shadow: 0 0 0 3px rgba(var(--theme-primary-rgb), 0.15); }

.form-input--error .form-input__field { border-color: var(--color-danger); box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15); }
.form-input__error { display: block; margin-top: 6px; font-size: 12px; color: var(--color-danger); }

.form-input--success .form-input__field { border-color: var(--color-success); }

.form-input--icon .form-input__icon {
  position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
  width: 20px; height: 20px; color: var(--color-text-muted); pointer-events: none;
}
.form-input--icon .form-input__field { padding-left: 48px; }

.form-input__toggle {
  position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
  width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;
  border: none; background: none; cursor: pointer; color: var(--color-text-muted);
}
```

```html
<div class="form-input">
  <label for="email" class="form-input__label form-input__label--required">E-posta</label>
  <input type="email" id="email" class="form-input__field" placeholder="ornek@email.com"
         autocomplete="email" aria-required="true">
</div>

<div class="form-input form-input--error">
  <label for="pass" class="form-input__label">Şifre</label>
  <input type="password" id="pass" class="form-input__field" aria-describedby="pass-error">
  <span class="form-input__error" id="pass-error">En az 8 karakter gerekli</span>
</div>
```

### Validation

| Criterion | Requirement |
|-----------|-------------|
| Label | `<label for>` zorunlu |
| Error | `aria-describedby` error message |
| Required | `aria-required="true"` |
| Autocomplete | `autocomplete` attribute for email/password |
| Font size | ≥16px (iOS zoom prevention) |
| Touch target | min 44×44px (56px height) |

---

*C06 Form Input v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
