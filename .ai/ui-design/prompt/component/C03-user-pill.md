---
title: "CoreMusic — C03 User Pill Prompt"
type: prompt
category: ui-design
component_id: "C03"
bem_class: ".header-user"
itcss_layer: "03_Layout"
date: 2026-09-20
version: 2.0.0
status: active
---

# C03 — User Pill (.header-user)

## 1. Component Definition

| Property | Value |
|----------|-------|
| **BEM Class** | `.header-user` |
| **ITCSS Layer** | `03_Layout` |
| **Target File** | `css/03_Layout/_header-user.css` |
| **Usage** | Header'da kullanıcı avatar + isim + dropdown |
| **Type** | User profile pill with dropdown |

## 2. ASCII Wireframe

```
┌─── USER PILL ──────────────────────────────┐
│  [Avatar 35×35] Bayram Ali ▾              │
│  circular   name max-width:80px  arrow     │
│  border:2px glass                            │
│  Total: ~150×40px                            │
└─────────────────────────────────────────────┘

┌─── DROPDOWN (is-open) ─────────────────────┐
│  ┌──────────────────────────────────────┐  │
│  │  👤 Profilim                         │  │
│  ├──────────────────────────────────────┤  │
│  │  ⚙ Ayarlar                          │  │
│  ├──────────────────────────────────────┤  │
│  │  🚪 Çıkış Yap                        │  │
│  └──────────────────────────────────────┘  │
│  width: 200px, glass bg, blur(16px)        │
└─────────────────────────────────────────────┘
```

## 3. Tier Sizes

| Tier | Avatar | Font | Dropdown Width |
|------|--------|------|----------------|
| Phone | Hidden (icon only) | — | 200px |
| Embedded | 35px | 13px | 200px |
| Desktop | 35px | 13px | 220px |
| 4K TV | 44px | 15px | 260px |

## 4. States

| State | Visual |
|-------|--------|
| Default | Glass pill, avatar + name + arrow |
| Hover | Slightly lighter glass bg |
| Open | Arrow rotated 180°, dropdown visible |
| Focus-visible | Outline on pill |

## 5. Accessibility

| Criterion | Requirement |
|-----------|-------------|
| Touch target | min 44×44px |
| ARIA | `aria-haspopup="true"`, `aria-expanded` |
| Keyboard | Enter/Space toggle, Escape close, Arrow keys in menu |
| Focus trap | Within dropdown when open |
| Return focus | Back to trigger on close |

## 6. Code Example

```css
/* C03 — User Pill | ITCSS: 03_Layout */
.header-user {
  position: relative;
  display: inline-flex;
  align-items: center;
  gap: var(--space-2);
  cursor: pointer;
  padding: 4px 12px 4px 4px;
  border-radius: var(--radius-pill);
  background: var(--glass-bg);
  backdrop-filter: blur(10px);
  border: var(--glass-border);
  font-family: var(--font-body);
  font-size: 13px;
  font-weight: 500;
  color: var(--color-text);
  min-height: 44px;
  transition: background-color var(--transition-fast);
}

.header-user:hover { background: rgba(255, 255, 255, 0.15); }

.header-user__avatar {
  width: 35px;
  height: 35px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid rgba(255, 255, 255, 0.2);
}

.header-user__name {
  font-weight: 500;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 80px;
}

.header-user__arrow {
  width: 12px;
  height: 12px;
  transition: transform 200ms ease;
  color: var(--color-text-muted);
}

.header-user.is-open .header-user__arrow { transform: rotate(180deg); }

.header-user__dropdown {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  min-width: 200px;
  padding: 8px 0;
  border-radius: 12px;
  background: var(--glass-bg);
  backdrop-filter: blur(16px);
  border: var(--glass-border);
  box-shadow: var(--shadow-lg);
  z-index: 1000;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-8px);
  transition: opacity 200ms ease, transform 200ms ease;
}

.header-user.is-open .header-user__dropdown {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.header-user__dropdown-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  font-size: 13px;
  color: var(--color-text);
  text-decoration: none;
}

.header-user__dropdown-item:hover { background: rgba(255, 255, 255, 0.08); }
.header-user__dropdown-item--danger { color: var(--color-danger); }

@media (max-width: 768px) {
  .header-user__name,
  .header-user__role { display: none; }
  .header-user { padding: 4px; }
}
```

```html
<div class="header-user" aria-haspopup="true" aria-expanded="false" tabindex="0">
  <img class="header-user__avatar" src="/avatar.jpg" alt="Bayram Ali">
  <span class="header-user__name">Bayram Ali</span>
  <svg class="header-user__arrow">...</svg>
  <div class="header-user__dropdown" role="menu">
    <a href="/profile" class="header-user__dropdown-item" role="menuitem">👤 Profilim</a>
    <a href="/settings" class="header-user__dropdown-item" role="menuitem">⚙ Ayarlar</a>
    <a href="/logout" class="header-user__dropdown-item header-user__dropdown-item--danger" role="menuitem">🚪 Çıkış Yap</a>
  </div>
</div>
```

---

*C03 User Pill v2.0.0 — CoreMusic UI Design System*
