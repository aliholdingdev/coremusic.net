---
title: "CoreMusic — 08 Login Page Prompt"
type: prompt
category: ui-design
page_id: "08"
route: "/login"
layout: "auth-split-72-28"
date: 2026-09-20
version: 2.0.0
status: active
---

# 08 — Login Page

## 1. Page Definition

| Property | Value |
|----------|-------|
| **Route** | `/login` |
| **Layout** | Auth Screen (72/28 split) |
| **Purpose** | Giriş formu, split layout |

## 2. Components Used

| Component | Count | Location |
|-----------|-------|----------|
| C06 Form Input | 2 | Glass panel (email, password) |
| C04 Primary Button | 1 | Glass panel (Giriş Yap) |
| C08 Social Login | 1 | Glass panel (Google, Apple) |
| Glass Panel | 1 | Right (28%) |

## 3. Layout

```
LEFT (72%): Manzara fotoğrafı + Logo (merkez)
RIGHT (28%): Glass Panel → Form + Button + Social + Link
```

## 4. Code Example

```html
<div class="auth-layout">
  <div class="auth-layout__bg">
    <img src="/bg-landscape.jpg" alt="" aria-hidden="true">
    <div class="auth-layout__logo">Core Music</div>
  </div>
  <div class="auth-layout__panel">
    <div class="glass-panel">
      <h1>Giriş Yap</h1>
      <form>
        <div class="form-input">
          <label for="login-email" class="form-input__label">E-posta</label>
          <input type="email" id="login-email" class="form-input__field" autocomplete="email">
        </div>
        <div class="form-input">
          <label for="login-pass" class="form-input__label">Şifre</label>
          <input type="password" id="login-pass" class="form-input__field" autocomplete="current-password">
        </div>
        <label class="toggle"><input type="checkbox"> Beni hatırla</label>
        <button type="submit" class="btn-primary">GİRİŞ YAP</button>
      </form>
      <div class="divider">— veya —</div>
      <div class="social-btn-group"><!-- C08 --></div>
      <p>Hesabın yok mu? <a href="/register">Kayıt Ol</a></p>
    </div>
  </div>
</div>
```

---

*08 Login Page v2.0.0 — CoreMusic UI Design System*
