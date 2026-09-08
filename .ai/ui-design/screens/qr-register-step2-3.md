---
title: "Register Steps 2-3 — Quick Reference"
type: ascii-qr
screen_id: "S17"
resolution: "1024x600"
layout_pattern: "Auth 78/22"
components: [C04, C06]
png: "shared-1024/Linux  1024 - Register Girl step 2.png + Linux  1024 - Register Girl step 3.png"
full_spec: "A-auth/register-step2-3.md"
---

# Register Steps 2-3

## Step 2 Wireframe — Şifre

```
┌── SAĞ PANEL (~22%) ─────────────────────────────────────┐
│                                                            │
│  [Kadın ikonu — beyaz çizim]                               │
│                                                            │
│  Hesap Oluştur                                            │
│  CoreMusic ailesine katıl,                                 │
│  müziğin keyfini çıkar                                     │
│                                                            │
│  Şifre                                                     │
│  ┌───────────────────────────────────┐                    │
│  │ (C06 input, type: password)       │                    │
│  └───────────────────────────────────┘                    │
│  Şifre Tekrar                                              │
│  ┌───────────────────────────────────┐                    │
│  │ (C06 input, type: password)       │                    │
│  └───────────────────────────────────┘                    │
│                                                            │
│  [Devam Et] (C04, pembe, full-width)                      │
│                                                            │
│  ── veya ──                                               │
│  [🍎] [G] [f]  [💬] [📷] [🎵]  [🎵]                    │
│  Her biri: C08, 52×52px                                   │
│                                                            │
│  Hesabın var mı?  Giriş Yap                               │
└──────────────────────────────────────────────────────────┘
```

## Step 3 Wireframe — Telefon + KVKK

```
┌── SAĞ PANEL (~22%) ─────────────────────────────────────┐
│                                                            │
│  [Kadın ikonu — beyaz çizim]                               │
│                                                            │
│  Hesap Oluştur                                            │
│  CoreMusic ailesine katıl,                                 │
│  müziğin keyfini çıkar                                     │
│                                                            │
│  Telefon                                                    │
│  ┌───────────────────────────────────┐                    │
│  │ (C06 input, type: tel)            │                    │
│  └───────────────────────────────────┘                    │
│                                                            │
│  ☑ Gizlilik Politikası ve Kullanım şartlarını             │
│    kabul ediyorum  (checkbox, 11px)                        │
│                                                            │
│  [Kayıt Ol] (C04, pembe, full-width)                      │
│                                                            │
│  ── veya ──                                               │
│  [🍎] [G] [f]  [💬] [📷] [🎵]  [🎵]                    │
│  Her biri: C08, 52×52px                                   │
│                                                            │
│  Hesabın var mı?  Giriş Yap                               │
└──────────────────────────────────────────────────────────┘
```

## Key Measurements

| Element | Position | Size | Token |
|---------|----------|------|-------|
| Sol alan | x:0-800 | ~78% | — |
| Sağ panel | x:800-1024 | 224px | — |
| Input yüksekliği | — | 56px | `--input-h-lg` |
| Buton yüksekliği | — | 56px | `--btn-h-lg` |
| Social buton | — | 52×52px | — |
| Touch target | — | ≥48px | `--touch-min` |
| Glass blur | — | blur(20px) | `--glass-blur` |

## Component Map

| ID | BEM Class | Position | Size |
|----|-----------|----------|------|
| C06 | `.register-form__input` | Form alanları | 100%×56px |
| C04 | `.register-form__submit` | Form sonu | full-width×56px |

## Step 2 Form Fields

| Alan | Tip | Yükseklik | Not |
|------|-----|-----------|-----|
| Şifre | password | 56px | Min 8 karakter |
| Şifre Tekrar | password | 56px | Eşleşme kontrolü |
| Devam Et | button (C04) | 56px | Pembe |

## Step 3 Form Fields

| Alan | Tip | Yükseklik | Not |
|------|-----|-----------|-----|
| Telefon | tel | 56px | +90 XXX XXX XX XX |
| KVKK | checkbox | — | Zorunlu onay |
| Kayıt Ol | button (C04) | 56px | Pembe, son adım |

## Navigation Flow

```
Step 1 → "Devam Et" → Step 2
Step 2 → "Devam Et" → Step 3 (şifre eşleşmeli)
Step 3 → "Kayıt Ol" → Select Gender sayfasına yönlendirme
```

## Theme Accent Colors

| Tema | Accent | Accent-BG |
|------|--------|-----------|
| Female | `#ff4fd8` | `rgba(255,79,216,0.15)` |
| Male | `#4f9fff` | `rgba(79,159,255,0.15)` |
| Neutral | `#a0a0b0` | `rgba(160,160,176,0.15)` |

## CSS Hints

```css
.register-form__input { width: 100%; min-height: 56px; background: var(--input-bg); border: var(--input-border); border-radius: var(--input-radius); }
.register-form__input:focus { border: var(--input-focus-border); box-shadow: 0 0 0 3px var(--accent-bg); }
.password-strength__bar { flex: 1; height: 4px; background: rgba(255,255,255,0.2); border-radius: 2px; }
.register-form__kvkk { display: flex; align-items: flex-start; gap: var(--space-2); font-size: var(--text-sm); min-height: 44px; }
.register-form__kvkk input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--accent); }
```
