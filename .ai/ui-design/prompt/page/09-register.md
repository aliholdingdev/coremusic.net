---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — 09 Register Page Prompt"
type: prompt
category: ui-design
page_id: "09"
route: "/register"
layout: "auth-split-72-28"
date: 2026-09-20
version: 2.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
status: active
---

# 09 — Register Page

## AI Code Generation Prompt

### Context

| Property | Value |
|----------|-------|
| **Route** | `/register` |
| **Layout** | Auth Screen (72/28 split) |
| **Purpose** | Kayıt formu, 3-step wizard |

**Components Used**

| Component | Count | Location |
|-----------|-------|----------|
| C06 Form Input | 4+ | Step 1 |
| C07 Gender Button | 3 | Step 2 |
| C04 Primary Button | 1 | Each step |
| C08 Social Login | 1 | Step 1 |
| Progress Indicator | 1 | Top of form |

### Required Inputs

- `viewport`: 1024×600 (RPi5 7" Touch) · 2 sütun · sidebar yok (kaynak: 00-device-matrix.md L111)
- `touch`: 48px minimum dokunma hedefi (kaynak: 00-device-matrix.md L111)
- `input`: padding 10px 12px · min-h 40px · radius 8px · font 14px/400 · durumlar default/focus/error/success/disabled · varyantlar `--error`/`--success` (kaynak: 02-component-inventory.md L75-L78)
- `button`: `.btn--primary` · padding 8px 16px · min-h 36px · radius 12px · font 14px/600 · durumlar default/hover/active/disabled/loading (kaynak: 02-component-inventory.md L65-L68)
- `button_tier`: Phone 44px min-h (tier uyarlaması) (kaynak: 02-component-inventory.md L69)

### ASCII Reference

```
y=0 ┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
    │ [0,0] Romantik_Background_03 (1024×600) + Siyah Arkaplan Evekt gradient (α .12→.04)                          │
y=120├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ glass panel (x749,y0,275×600, #FFF α.2 + blur 2)  · bg-art 100×100 @(836,15)                                 │
    │ "Hesap Oluştur" (841,120) 90×16 PJS 13/600                                                                    │
y=133├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ "CoreMusic ailesine katıl, müziğin keyfini çıkar" (785,141) 202×13 DM Sans 10/300 #DCDCDC                    │
y=141├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ logo 30×30 @(64,249) · "Core Music" 58×25 @(101,251) Bickham Script Two 15                                   │
    │ left title "Seni /      Tanıyalım " (64,301) 73×30  (son karakter U+00A0 NBSP)                               │
    │ left desc "Deneyimini sana özel hale getirmek için bir seçim yapman yeterli." (62,338) 295×13                │
y=189├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ "Kullanıcı Adı" (776,202) 43×10 PJS 8/500                                                                    │
    │ [input#reg-username] (776,217) 220×19 r3 · stroke #F200D0 0.2 · ph (784,221)                                 │
    │ "Eposta" (776,246) 26×10                                                                                     │
    │ [input#reg-email] (776,261) 220×19 r3 · stroke #F200D0 0.2 · ph (784,265)                                    │
y=311├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ [Devam Et] (776,299) 220×25 r5 · text (870,307) 33×9 PJS 7/500                                                │
y=342├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ social "____________________veya şununla devam et____________________" (777,390) 218×10                       │
    │ row1 y415: [Apple](776) [Google](853) [Facebook](931)  65×25 · icon 15×15                                     │
    │ row2 y450: [Spotify](776) [İnstagram](853) [Tiktok](931) 65×25                                               │
    │ row3 y485: [Github](776) [Google](853) [Facebook](931) 65×25                                                 │
y=462├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ (boş alan — hero art YOK)                                                                                     │
    │ privacy "Devam ederek Gizlilik Politikası'nı kabul etmiş olursunuz." (786,567) 207×10 DM Sans 8/300            │
y=567├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ footer: "Gizlilik"(16,583) · Ellipse(35,587) · "Kullanım Koşulları"(38,583) · Ellipse(87,587) ·               │
    │   "Destek    © 2026  Coremusic"(90,583) 95×8 · Ellipse(112,587)                                              │
    │ glass footer "Hesabın yok mu? Kayıt Ol" (839,567) 94×10                                                       │
y=600└──────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

> Kaynak: [[screens/shared/register-step1]] — L34-L64

### Prompt Template

> ⚠️ VERIFICATION REQUIRED — dosyada JSON Prompt Template bloğu yok

### Expected Output

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

### Validation

- [ ] Gövde ve normal etiket metni kontrastı ≥ 4.5:1 (Primary 18.1:1, Secondary 9.8:1 ✅); Tertiary #707088 4.2:1 ❌ → #8888a0 5.2:1 düzeltmesi uygulanmış (kanıt: 04-accessibility-gaps.md L105-L112, L118)
- [ ] Primary button kontrastı 3.1:1 ❌ → koyu text `#1a1a2e` veya açık pink `#ff6ee4` düzeltmesi uygulanmış (kanıt: 04-accessibility-gaps.md L110, L120-L123)
- [ ] Odaklanabilir tüm öğelerde `outline: 2px solid var(--cm-primary); outline-offset: 2px` görünür; mouse kullanıcısında `outline: none` (kanıt: 04-accessibility-gaps.md L197-L207)
- [ ] `Tab` sırası DOM sırasıyla doğal; kaybolan/kirli odak yok, Toggle/Slider `:focus-visible` belirteci korunmuş (kanıt: 04-accessibility-gaps.md L130-L138, L144-L152)
- [ ] `aria-label`/`role`/`aria-live` eşlemesi korunmuş; Input aria-live, Toast/Toggle/Progress aria-label eksikleri giderilmiş (kanıt: 04-accessibility-gaps.md L167-L175)
- [ ] `prefers-reduced-motion` altında animasyon/transition süreleri `0.01ms`, iteration-count 1 (kanıt: 04-accessibility-gaps.md L181-L190)

---

*09 Register Page v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
### Steps

| Step | Content |
|------|---------|
| 1 | Temel Bilgiler (Ad, Email, Şifre) |
| 2 | Profil (Cinsiyet, Fotoğraf, Tercihler) |
| 3 | KVKK Onay |

---

*09 Register Page v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
