---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — 08 Login Page Prompt"
type: prompt
category: ui-design
page_id: "08"
route: "/login"
layout: "auth-split-72-28"
date: 2026-09-20
version: 2.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
status: active
---

# 08 — Login Page

## AI Code Generation Prompt

### Context

| Property | Value |
|----------|-------|
| **Route** | `/login` |
| **Layout** | Auth Screen (72/28 split) |
| **Purpose** | Giriş formu, split layout |

**Components Used**

| Component | Count | Location |
|-----------|-------|----------|
| C06 Form Input | 2 | Glass panel (email, password) |
| C04 Primary Button | 1 | Glass panel (Giriş Yap) |
| C08 Social Login | 1 | Glass panel (Google, Apple) |
| Glass Panel | 1 | Right (28%) |

### Required Inputs

- `viewport`: 1024×600 (RPi5 7" Touch) · 2 sütun · sidebar yok (kaynak: 00-device-matrix.md L111)
- `touch`: 48px minimum dokunma hedefi (kaynak: 00-device-matrix.md L111)
- `input`: padding 10px 12px · min-h 40px · radius 8px · font 14px/400 · durumlar default/focus/error/success/disabled · varyantlar `--error`/`--success` (kaynak: 02-component-inventory.md L75-L78)
- `button`: `.btn--primary` · padding 8px 16px · min-h 36px · radius 12px · font 14px/600 · durumlar default/hover/active/disabled/loading (kaynak: 02-component-inventory.md L65-L68)

### ASCII Reference

```
LEFT (72%): Manzara fotoğrafı + Logo (merkez)
RIGHT (28%): Glass Panel → Form + Button + Social + Link
```

### Prompt Template

```json
{
  "task": "Create login page for CoreMusic",
  "page": "08-login",
  "viewport": "1024x600",
  "components": [
    "form-input",
    "primary-button",
    "social-login",
    "glass-panel"
  ],
  "tokens": {
    "--cm-touch-target-lg": "48px",
    "--cm-radius-md": "8px",
    "--cm-radius-lg": "12px"
  }
}
```

### Expected Output

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

### Validation

- [ ] Gövde ve normal etiket metni kontrastı ≥ 4.5:1 (Primary 18.1:1, Secondary 9.8:1 ✅); Tertiary #707088 4.2:1 ❌ → #8888a0 5.2:1 düzeltmesi uygulanmış (kanıt: 04-accessibility-gaps.md L105-L112, L118)
- [ ] Primary button kontrastı 3.1:1 ❌ → koyu text `#1a1a2e` veya açık pink `#ff6ee4` düzeltmesi uygulanmış (kanıt: 04-accessibility-gaps.md L110, L120-L123)
- [ ] Odaklanabilir tüm öğelerde `outline: 2px solid var(--cm-primary); outline-offset: 2px` görünür; mouse kullanıcısında `outline: none` (kanıt: 04-accessibility-gaps.md L197-L207)
- [ ] `Tab` sırası DOM sırasıyla doğal; kaybolan/kirli odak yok, Toggle/Slider `:focus-visible` belirteci korunmuş (kanıt: 04-accessibility-gaps.md L130-L138, L144-L152)
- [ ] `aria-label`/`role`/`aria-live` eşlemesi korunmuş; Input aria-live, Toast/Toggle/Progress aria-label eksikleri giderilmiş (kanıt: 04-accessibility-gaps.md L167-L175)
- [ ] `prefers-reduced-motion` altında animasyon/transition süreleri `0.01ms`, iteration-count 1 (kanıt: 04-accessibility-gaps.md L181-L190)

---

*08 Login Page v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
