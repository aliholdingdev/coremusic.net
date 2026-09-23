---
title: "CoreMusic — 09 Register Page Prompt"
type: prompt
category: ui-design
page_id: "09"
route: "/register"
layout: "auth-split-72-28"
date: 2026-09-20
version: 2.0.0
status: active
---

# 09 — Register Page (3-Step Wizard)

## 1. Page Definition

| Property | Value |
|----------|-------|
| **Route** | `/register` |
| **Layout** | Auth Screen (72/28 split) |
| **Purpose** | Kayıt formu, 3-step wizard |

## 2. Steps

| Step | Content |
|------|---------|
| 1 | Temel Bilgiler (Ad, Email, Şifre) |
| 2 | Profil (Cinsiyet, Fotoğraf, Tercihler) |
| 3 | KVKK Onay |

## 3. Components Used

| Component | Count | Location |
|-----------|-------|----------|
| C06 Form Input | 4+ | Step 1 |
| C07 Gender Button | 3 | Step 2 |
| C04 Primary Button | 1 | Each step |
| C08 Social Login | 1 | Step 1 |
| Progress Indicator | 1 | Top of form |

## 4. Code Example

```html
<div class="auth-layout">
  <div class="auth-layout__bg"><!-- landscape --></div>
  <div class="auth-layout__panel">
    <div class="glass-panel">
      <div class="wizard-progress" aria-label="Kayıt ilerlemesi">
        <span class="wizard-step is-active">1 Temel</span>
        <span class="wizard-step">2 Profil</span>
        <span class="wizard-step">3 KVKK</span>
      </div>
      <!-- Step 1: Temel Bilgiler -->
      <form>
        <div class="form-input">
          <label for="reg-name" class="form-input__label">Ad Soyad</label>
          <input type="text" id="reg-name" class="form-input__field" autocomplete="name">
        </div>
        <div class="form-input">
          <label for="reg-email" class="form-input__label">E-posta</label>
          <input type="email" id="reg-email" class="form-input__field" autocomplete="email">
        </div>
        <div class="form-input">
          <label for="reg-pass" class="form-input__label">Şifre</label>
          <input type="password" id="reg-pass" class="form-input__field" autocomplete="new-password">
        </div>
        <div class="form-input">
          <label for="reg-pass2" class="form-input__label">Şifre Tekrar</label>
          <input type="password" id="reg-pass2" class="form-input__field" autocomplete="new-password">
        </div>
        <label class="toggle"><input type="checkbox"> KVKK aydınlatma metnini okudum</label>
        <button type="submit" class="btn-primary">DEVAM ET</button>
      </form>
      <div class="social-btn-group"><!-- C08 --></div>
      <p>Zaten hesabın var mı? <a href="/login">Giriş Yap</a></p>
    </div>
  </div>
</div>
```

---

*09 Register Page v2.0.0 — CoreMusic UI Design System*
