---
title: "C03 — User Pill Component Prompt"
type: component-prompt
category: ui-design
component_id: "C03"
bem_class: ".header-user"
itcss_layer: "03_Layout"
target_file: "css/03_Layout/_header-user.css"
version: 1.0.0
status: active
author: "UI Designer Agent"
---

# C03 — User Pill (.header-user)

## 1. Bileşen Tanımı

| Özellik | Değer |
|---------|-------|
| **BEM Class** | `.header-user` |
| **ITCSS Layer** | `03_Layout` |
| **Hedef Dosya** | `css/03_Layout/_header-user.css` |
| **Kullanım Alanı** | Header'da kullanıcı avatar + isim + dropdown |
| **Bileşen Türü** | User profile pill with dropdown |

## 2. PNG Ölçümleri

| Özellik | Değer | Not |
|---------|-------|-----|
| Avatar boyutu | 35×35px | Dairesel avatar |
| Toplam genişlik | ~150px | Avatar + isim + arrow |
| Pill yüksekliği | ~40px | Dikey ortalama |
| Pill border-radius | `--radius-pill` | Tam yuvarlak |
| Avatar gap | 8px | Avatar ile isim arası |
| Dropdown | 200×auto | Aşağı açılır menü |

## 3. Token Referansları

| Token | Amaç | Kullanım |
|-------|------|----------|
| `--glass-bg` | Glassmorphism arka plan | `background: rgba(255,255,255,0.1)` |
| `--glass-border` | Glass kenarlık | `border: 1px solid rgba(255,255,255,0.15)` |
| `--radius-pill` | Tam yuvarlak | `border-radius: 100px` |
| `--color-text` | Kullanıcı ismi rengi | Normal text color |
| `--color-text-muted` | Rol bilgisi rengi | Secondary text |
| `--space-2` | Avatar gap | `8px` |
| `--shadow-lg` | Dropdown gölgesi | `box-shadow` |
| `--blur-sm` | Backdrop blur | Glass efekti |

## 4. WCAG Gereksinimleri

| Kriter | Değer | Durum |
|--------|-------|-------|
| **Touch target** | ≥44×44px | Pill 40px → 44px'e genişletilmeli |
| **Avatar alt text** | `alt` zorunlu | Kullanıcı adı |
| **Dropdown menu** | `role="menu"` | Screen reader desteği |
| **Focus trap** | Dropdown içinde | Keyboard navigasyonu |
| **Escape key** | Dropdown kapatma | Keyboard erişilebilirlik |

## 5. CSS Üretim Talimatları

```css
/* ============================================
   C03 — User Pill
   ITCSS: 03_Layout
   BEM: .header-user
   ============================================ */

.header-user {
  position: relative;
  display: inline-flex;
  align-items: center;
  gap: var(--space-2);
  cursor: pointer;

  /* Pill shape */
  padding: 4px 12px 4px 4px;
  border-radius: var(--radius-pill);

  /* Glassmorphism */
  background: var(--glass-bg);
  backdrop-filter: blur(10px);
  -webkit-backdrop-filter: blur(10px);
  border: var(--glass-border);

  /* Typography */
  font-family: var(--font-body);
  font-size: 13px;
  font-weight: 500;
  color: var(--color-text);

  /* WCAG: hit area */
  min-height: 44px;

  /* Transition */
  transition: background-color var(--transition-fast);
}

.header-user:hover {
  background: rgba(255, 255, 255, 0.15);
}

/* Avatar */
.header-user__avatar {
  width: 35px;
  height: 35px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid rgba(255, 255, 255, 0.2);
}

/* Name */
.header-user__name {
  font-weight: 500;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 80px;
}

/* Role badge */
.header-user__role {
  font-size: 10px;
  color: var(--color-text-muted);
  display: block;
  line-height: 1;
}

/* Dropdown arrow */
.header-user__arrow {
  width: 12px;
  height: 12px;
  transition: transform 200ms ease;
  color: var(--color-text-muted);
}

.header-user.is-open .header-user__arrow {
  transform: rotate(180deg);
}

/* Dropdown menu */
.header-user__dropdown {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  min-width: 200px;
  padding: 8px 0;
  border-radius: 12px;
  background: var(--glass-bg);
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  border: var(--glass-border);
  box-shadow: var(--shadow-lg);
  z-index: 1000;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-8px);
  transition: opacity 200ms ease, transform 200ms ease, visibility 200ms;
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
  transition: background-color 150ms;
}

.header-user__dropdown-item:hover {
  background: rgba(255, 255, 255, 0.08);
}

.header-user__dropdown-item--danger {
  color: var(--color-danger);
}

/* Responsive */
@media (max-width: 768px) {
  .header-user__name { display: none; }
  .header-user__role { display: none; }
  .header-user { padding: 4px; }
}
```

## 6. Notlar

- Dropdown, `click` event ile açılır/kapanır
- `aria-haspopup="true"` ve `aria-expanded` attribute'ları zorunlu
- Escape tuşu ile dropdown kapatılmalıdır
- Click outside ile de kapanmalıdır
- Avatar yüklenemediğinde fallback initial gösterilmelidir
