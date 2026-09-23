---
title: "CoreMusic — 10 Gender Select Page Prompt"
type: prompt
category: ui-design
page_id: "10"
route: "/gender-select"
layout: "auth-split-72-28"
date: 2026-09-20
version: 2.0.0
status: active
---

# 10 — Gender Select Page (First Step)

## 1. Page Definition

| Property | Value |
|----------|-------|
| **Route** | `/gender-select` |
| **Layout** | Auth Screen (72/28 split) |
| **Purpose** | Cinsiyet seçimi, auth akışının ilk adımı |

## 2. Components Used

| Component | Count | Location |
|-----------|-------|----------|
| C07 Gender Button | 3 | Glass panel |
| C04 Primary Button | 1 | Glass panel |

## 3. Theme Mapping

| Gender | Theme | Color |
|--------|-------|-------|
| Kadın | Pembe | #FF69B4 → `--theme-primary` |
| Erkek | Mavi | #4A90D9 → `--theme-primary` |
| Nötr | Varsayılan | #888888 → `--theme-primary` |

## 4. Auth Flow

```
Select Gender (/gender-select) → Login (/login) → Register (/register)
```

## 5. Code Example

```html
<div class="auth-layout">
  <div class="auth-layout__bg">
    <img src="/bg-landscape.jpg" alt="" aria-hidden="true">
    <div class="auth-layout__logo">Core Music</div>
  </div>
  <div class="auth-layout__panel">
    <div class="glass-panel">
      <h1>Cinsiyetini seç</h1>
      <p>tema rengini belirler</p>
      <div class="gender-group" role="radiogroup" aria-label="Cinsiyet seçimi">
        <button class="gender-btn" role="radio" aria-checked="false" data-gender="female">
          <span class="gender-btn__icon">👩</span>
          <span class="gender-btn__text">
            <span class="gender-btn__title">Kadın</span>
            <span class="gender-btn__desc">pembe tema</span>
          </span>
        </button>
        <button class="gender-btn" role="radio" aria-checked="false" data-gender="male">
          <span class="gender-btn__icon">👨</span>
          <span class="gender-btn__text">
            <span class="gender-btn__title">Erkek</span>
            <span class="gender-btn__desc">mavi tema</span>
          </span>
        </button>
        <button class="gender-btn" role="radio" aria-checked="true" data-gender="neutral">
          <span class="gender-btn__icon">🌐</span>
          <span class="gender-btn__text">
            <span class="gender-btn__title">Nötr</span>
            <span class="gender-btn__desc">varsayılan tema</span>
          </span>
        </button>
      </div>
      <button class="btn-primary">DEVAM ET</button>
    </div>
  </div>
</div>
```

---

*10 Gender Select Page v2.0.0 — CoreMusic UI Design System*
