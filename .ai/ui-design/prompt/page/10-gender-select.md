---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — 10 Gender Select Page Prompt"
type: prompt
category: ui-design
page_id: "10"
route: "/gender-select"
layout: "auth-split-72-28"
date: 2026-09-20
version: 2.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
status: active
---

# 10 — Gender Select Page

## AI Code Generation Prompt

### Context

| Property | Value |
|----------|-------|
| **Route** | `/gender-select` |
| **Layout** | Auth Screen (72/28 split) |
| **Purpose** | Cinsiyet seçimi, auth akışının ilk adımı |

**Components Used**

| Component | Count | Location |
|-----------|-------|----------|
| C07 Gender Button | 3 | Glass panel |
| C04 Primary Button | 1 | Glass panel |

### Required Inputs

- `viewport`: 1024×600 (RPi5 7" Touch) · 2 sütun · sidebar yok (kaynak: 00-device-matrix.md L111)
- `touch`: 48px minimum dokunma hedefi (kaynak: 00-device-matrix.md L111)
- `button`: `.btn--primary`/`.btn--secondary`/`.btn--ghost` · padding 8px 16px · min-h 36px · radius 12px · durumlar default/hover/active/disabled/loading (kaynak: 02-component-inventory.md L65-L68)

### ASCII Reference

```
y=0 ┌──────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
    │ [0,0] Romantik_Background_03 (1024×600) + Siyah Arkaplan Evekt gradient (α .12→.04)                          │
y=120├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ glass panel (x749,y0,275×600, #FFF α.2 + blur 2)  · bg-art 100×100 @(836,15)                                 │
    │ "Seni Tanıyalım" (844,120) 87×16 PJS 13/600                                                                  │
y=133├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ "Müzik deneyimini sana özel hale getirelim" (794,141) 187×13 DM Sans 10/300 #DCDCDC                          │
y=141├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ logo 30×30 @(64,249) · "Core Music" 58×25 @(101,251) Bickham Script Two 15                                   │
    │ left title "Seni /      Tanıyalım" (64,301) 73×30  (son karakter U+00A0 NBSP)                                │
    │ left desc "Deneyimini sana özel hale getirmek için bir seçim yapman yeterli." (62,338) 295×13                │
y=369├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ [btn/Kız      ] (776,213) 220×40 r5 · icon 20×20 (787,223) · "Kız" (815,224) · sub (815,234)                 │
    │ [btn/Erkek    ] (776,263) 220×40 r5 · icon 20×20 (787,273) · "Erkek" (815,274) · sub (815,284)               │
    │ [btn/nötur    ] (776,313) 220×40 r5 · icon 20×20 (787,323) · "Cinsiyetimi söylemek istemiyorum" (815,324)     │
    │   sub "Genel, soft netural vibes" (815,334)                                                                  │
y=403├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ [Devam Et] (776,363) 220×25 r5 · text (870,371) 33×9 PJS 7/500                                               │
y=414├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ hero art (dekoratif illüstrasyon) 196×130 @(788,414)                                                         │
y=544├──────────────────────────────────────────────────────────────────────────────────────────────────────────────┤
    │ privacy "Devam ederek Gizlilik Politikası'nı kabul etmiş olursunuz." (786,567) 207×10 DM Sans 8/300           │
    │ footer: "Gizlilik"(16,583) · Ellipse(35,587) · "Kullanım Koşulları"(38,583) · Ellipse(87,587) ·               │
    │   "Destek    © 2026  Coremusic"(90,583) 95×8 · Ellipse(112,587)                                              │
    │ glass footer "Hesabın yok mu? Kayıt Ol" (839,567) 94×10                                                       │
y=600└──────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

> Kaynak: [[screens/shared/select-gender]] — L34-L59

### Prompt Template

```json
{
  "task": "Create gender select page for CoreMusic",
  "page": "10-gender-select",
  "viewport": "1024x600",
  "components": [
    "gender-button",
    "primary-button"
  ],
  "tokens": {
    "--cm-touch-target-lg": "48px",
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

### Validation

- [ ] Gövde ve normal etiket metni kontrastı ≥ 4.5:1 (Primary 18.1:1, Secondary 9.8:1 ✅); Tertiary #707088 4.2:1 ❌ → #8888a0 5.2:1 düzeltmesi uygulanmış (kanıt: 04-accessibility-gaps.md L105-L112, L118)
- [ ] Odaklanabilir tüm öğelerde `outline: 2px solid var(--cm-primary); outline-offset: 2px` görünür; mouse kullanıcısında `outline: none` (kanıt: 04-accessibility-gaps.md L197-L207)
- [ ] `Tab` sırası DOM sırasıyla doğal; kaybolan/kirli odak yok, Toggle/Slider `:focus-visible` belirteci korunmuş (kanıt: 04-accessibility-gaps.md L130-L138, L144-L152)
- [ ] `aria-label`/`role`/`aria-live` eşlemesi korunmuş; Input aria-live, Toast/Toggle/Progress aria-label eksikleri giderilmiş (kanıt: 04-accessibility-gaps.md L167-L175)
- [ ] `prefers-reduced-motion` altında animasyon/transition süreleri `0.01ms`, iteration-count 1 (kanıt: 04-accessibility-gaps.md L181-L190)

---

*10 Gender Select Page v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
### Theme Mapping

| Gender | Theme | Color |
|--------|-------|-------|
| Kadın | Pembe | #FF69B4 → `--theme-primary` |
| Erkek | Mavi | #4A90D9 → `--theme-primary` |
| Nötr | Varsayılan | #888888 → `--theme-primary` |

### Auth Flow

```
Select Gender (/gender-select) → Login (/login) → Register (/register)
```

---

*10 Gender Select Page v2.0.0 — CoreMusic UI Design System*

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
