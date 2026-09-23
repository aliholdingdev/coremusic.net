---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Login Screen Specification"
type: spec
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T08
viewport: 1024x600
device: RPi5 7" Touch (Embedded)
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/shared/login.md"
  source_of_truth: ".ai/.png/shared-1024/Linux  1024 - Login Girl.png"
---

# CoreMusic — Login Screen (T08 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]] · [[tokens/design-tokens-master]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                              x:1024    │
│                                                                                                            │
│ ┌─── LEFT SIDE (60%, w:614) ──────────────┐  ┌─── RIGHT SIDE (40%, w:410) ────────────────────────────┐  │
│ │                                          │  │                                                          │  │
│ │  🏔️ Manzara arka planı                  │  │  👤 Kullanıcı ikonu (48×48, dairesel)                  │  │
│ │  (dağ, çiçekler, gün batımı)            │  │                                                          │  │
│ │                                          │  │  Hoş Geldin                                             │  │
│ │  ┌─── Brand Area (ortada) ───────────┐  │  │  Hesabına giriş yap, müziğin keyfini çıkar             │  │
│ │  │                                   │  │  │                                                          │  │
│ │  │    ✨ Core Music ✨               │  │  │  ─── E-posta, Telefon veya Kullanıcı Adı ───           │  │
│ │  │                                   │  │  │  ┌────────────────────────────────────────────────┐     │  │
│ │  │    Seni Tanıyalım                 │  │  │  │ ● E-postanız veya Kullanıcı Adınız            │     │  │
│ │  │    Müzik deneyimini sana          │  │  │  └────────────────────────────────────────────────┘     │  │
│ │  │    özel hale getirelim            │  │  │                                                          │  │
│ │  │                                   │  │  │  ─── Şifre ───                                         │  │
│ │  └───────────────────────────────────┘  │  │  ┌────────────────────────────────────────────────┐     │  │
│ │                                          │  │  │ ● E-postanız veya Kullanıcı Adınız            │     │  │
│ │                                          │  │  └────────────────────────────────────────────────┘     │  │
│ │                                          │  │                                                          │  │
│ │                                          │  │  ☐ Beni Hatırla                Şifremi Unuttum          │  │
│ │                                          │  │  ┌────────────────────────────────────────────────┐     │  │
│ │                                          │  │  │ ▶ Giriş Yap (gradient pembe)                   │     │  │
│ │                                          │  │  └────────────────────────────────────────────────┘     │  │
│ │                                          │  │                                                          │  │
│ │                                          │  │  ────── veya şu linkler ile devam et ──────            │  │
│ │                                          │  │                                                          │  │
│ │                                          │  │  [🍎 Apple] [🔍 Google] [📘 Facebook]                  │  │
│ │                                          │  │  [💬 WhatsApp] [📷 Instagram] [🎵 TikTok]              │  │
│ │                                          │  │  [🤖 Telegram]                                         │  │
│ │                                          │  │                                                          │  │
│ │                                          │  │                        Hesabın yok mu? Kayıt Ol       │  │
│ └──────────────────────────────────────────┘  └──────────────────────────────────────────────────────────┘  │
│                                                                                                            │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. BEM Sınıfları

| BEM Sınıfı | Açıklama | Piksel |
|------------|----------|--------|
| `.login-page` | Ana sayfa container | full screen, flex row |
| `.login-page__brand` | Sol taraf (manzara + logo) | w:60%, position:relative |
| `.login-page__form` | Sağ taraf (form) | w:40%, bg:white/95 |
| `.login-brand__logo` | Core Music logosu | 48×48, center |
| `.login-brand__title` | "Seni Tanıyalım" | font:24px/700 |
| `.login-brand__subtitle` | "Müzik deneyimini..." | font:14px/400 |
| `.login-form__avatar` | Kullanıcı avatar ikonu | 48×48, circle |
| `.login-form__title` | "Hoş Geldin" | font:28px/700 |
| `.login-form__subtitle` | "Hesabına giriş yap..." | font:14px/400 |
| `.login-form__input` | Input field | h:48, radius:8px |
| `.login-form__remember` | Beni Hatırla checkbox | 16×16 |
| `.login-form__forgot` | Şifremi Unuttum link | font:12px, underline |
| `.login-form__submit` | Giriş Yap butonu | gradient, h:48, radius:8px |
| `.login-form__divider` | Ayırıcı çizgi | 1px solid #ddd |
| `.login-form__social` | Sosyal medya butonları | 6 buton, 2 satır |
| `.login-form__register` | "Kayıt Ol" link | font:14px, bold |

---

## 3. Token Referansları

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--cm-primary` | #ff4fd8 | Submit butonu, linkler |
| `--cm-bg-form` | rgba(255,255,255,0.95) | Form arka planı |
| `--cm-bg-input` | #f5f5f5 | Input arka planı |
| `--cm-border-input` | #e0e0e0 | Input kenarlığı |
| `--cm-text-primary` | #0a0a0f | Başlık |
| `--cm-text-secondary` | #666666 | Açıklayıcı metin |
| `--cm-text-placeholder` | #999999 | Input placeholder |
| `--cm-radius-md` | 8px | Input/button radius |
| `--cm-spacing-lg` | 24px | Form padding |
| `--cm-font-size-2xl` | 28px | Başlık |
| `--cm-font-size-lg` | 16px | Normal metin |
| `--cm-font-size-sm` | 12px | Küçük metin |

---

## 4. Touch Target

| Cihaz | Min Touch | Not |
|-------|-----------|-----|
| T08 RPi5 7" | 48×48px | Input h:48, Button h:48 |
| T01 Phone | 48×48px | Minimum |
| T25 TV | 80×80px | D-pad ile navigasyon |

---

## 5. WCAG Uyumu

| Kontrol | Durum | Detay |
|---------|:-----:|-------|
| Kontrast 4.5:1 | ✅ | Başlık #0a0a0f beyaz arka plan |
| Kontrast 3:1 | ✅ | Placeholder #999 yeterli mi? → #666 olmalı |
| Keyboard | ✅ | Tab: Email → Password → Remember → Submit → Social |
| Focus Visible | ✅ | Input focus: border #ff4fd8, outline 2px |
| Screen Reader | ✅ | `aria-label="E-posta"`, `aria-label="Şifre"`, `aria-required="true"` |
| Error State | ✅ | Hatalı input: border #ef4444, aria-describedby |
| Autocomplete | ✅ | `autocomplete="email"`, `autocomplete="current-password"` |

---

## 6. Form Stili

```css
/* ═══ Form Container ═══ */
.login-page__form {
  width: 40%;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  padding: 40px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}

/* ═══ Input ═══ */
.login-form__input {
  width: 100%;
  height: 48px;
  padding: 0 16px;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  font-size: 14px;
  background: #f5f5f5;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.login-form__input:focus {
  outline: none;
  border-color: #ff4fd8;
  box-shadow: 0 0 0 3px rgba(255, 79, 216, 0.15);
}

.login-form__input::placeholder {
  color: #999;
}

/* ═══ Submit Button ═══ */
.login-form__submit {
  width: 100%;
  height: 48px;
  background: linear-gradient(135deg, #ff4fd8, #a855f7);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.15s ease, box-shadow 0.15s ease;
}

.login-form__submit:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 16px rgba(255, 79, 216, 0.3);
}

/* ═══ Social Buttons ═══ */
.login-form__social {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}

.login-form__social-btn {
  height: 40px;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  background: white;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s ease;
}

.login-form__social-btn:hover {
  background: #f5f5f5;
}

/* ═══ Error State ═══ */
.login-form__input--error {
  border-color: #ef4444;
}

.login-form__input--error:focus {
  box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15);
}

.login-form__error-message {
  color: #ef4444;
  font-size: 12px;
  margin-top: 4px;
}
```

---

## 7. PNG Referansı

| PNG | Viewport | Dosya Yolu |
|-----|----------|------------|
| `Linux 1024 - Login Girl.png` | 1024×600 | `.ai/.png/shared-1024/` |

---

## 8. Responsive Davranış

| Tier | Viewport | Layout Değişikliği |
|------|----------|-------------------|
| T01-T05 (Phone) | ≤767px | Tam ekran form, brand area gizli |
| T06-T07 (Tablet) | 768-1024px | Split 50/50 |
| T08 (Embedded) | 1024×600 | Split 60/40 |
| T12-T16 (Laptop) | 1025-2560px | Split 50/50, larger form |
| T25-T28 (TV) | ≥3840px | Split 50/50, font scale 1.5x |

---

## 9. State Durumları

| Durum | Görsel Değişiklik |
|-------|-------------------|
| Default | Input #f5f5f5 bg, #e0e0e0 border |
| Focus | Border #ff4fd8, box-shadow |
| Error | Border #ef4444, error message |
| Success | Border #22c55e, check icon |
| Loading | Submit butonunda spinner |
| Disabled | Opacity:0.5, cursor:not-allowed |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
