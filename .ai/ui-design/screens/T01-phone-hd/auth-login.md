---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Phone HD Auth Login Screen Specification"
type: spec
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T01
viewport: 720x1280
device: Samsung Galaxy J7 (2016) / Galaxy A13
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T01-phone-hd/auth-login.md"
  source_of_truth: ".ai/ui-design/00-device-matrix.md"
---

# CoreMusic — Phone HD Auth Login (T01 720×1280)

## 1. ASCII Layout (x:0-720, y:0-1280)

```
┌────────────────────────────────────────────┐
│ x:0                                x:720  │
│ y:0 ┌─── STATUS BAR (h:24) ────────────┐ │
│     │ 📶  🔋 100%  ⏰ 07:00            │ │
│ y:24 └──────────────────────────────────┘ │
│                                            │
│ y:24 ┌─── CONTENT (h:1176) ────────────┐ │
│     │                                   │ │
│     │  ┌─── BRAND (top %30) ─────────┐ │ │
│     │  │  🏔️ Manzara arka planı      │ │ │
│     │  │  ✨ Core Music ✨            │ │ │
│     │  │  Seni Tanıyalım              │ │ │
│     │  └──────────────────────────────┘ │ │
│     │                                   │ │
│     │  ┌─── FORM (bottom %70) ───────┐ │ │
│     │  │ 👤 Avatar (64×64, circle)    │ │ │
│     │  │ Hoş Geldin                   │ │ │
│     │  │ Hesabına giriş yap...        │ │ │
│     │  │                              │ │ │
│     │  │ ┌──────────────────────────┐ │ │ │
│     │  │ │ E-posta veya Kullanıcı  │ │ │ │
│     │  │ └──────────────────────────┘ │ │ │
│     │  │ ┌──────────────────────────┐ │ │ │
│     │  │ │ Şifre                    │ │ │ │
│     │  │ └──────────────────────────┘ │ │ │
│     │  │ ☐ Beni Hatırla  Şifremi Ut  │ │ │
│     │  │ ┌──────────────────────────┐ │ │ │
│     │  │ │ ▶ GİRİŞ YAP (gradient)  │ │ │ │
│     │  │ └──────────────────────────┘ │ │ │
│     │  │                              │ │ │
│     │  │ ── veya ──                   │ │ │
│     │  │ [🍎] [🔍] [📘]              │ │ │
│     │  │ [💬] [📷] [🎵]              │ │ │
│     │  │                              │ │ │
│     │  │ Hesabın yok mu? Kayıt Ol    │ │ │
│     │  └──────────────────────────────┘ │ │
│     └───────────────────────────────────┘ │
│                                            │
│ y:1256 ┌─── HOME INDICATOR (h:24) ────┐ │
│ y:1280└──────────────────────────────────┘ │
└────────────────────────────────────────────┘
```

---

## 2. BEM Sınıfları

| BEM Sınıfı | Açıklama |
|------------|----------|
| `.auth-phone` | Phone auth layout |
| `.auth-phone__brand` | Brand area (manzara + logo) |
| `.auth-phone__form` | Form area |
| `.auth-phone__avatar` | User avatar (64×64) |
| `.auth-phone__input` | Input field (h:48) |
| `.auth-phone__submit` | Submit button (gradient) |
| `.auth-phone__social` | Social login grid (3×2) |
| `.auth-phone__register` | Register link |

---

## 3. Token Referansları

| Token | Değer |
|-------|-------|
| `--cm-primary` | #ff4fd8 |
| `--cm-bg-form` | rgba(255,255,255,0.95) |
| `--cm-radius-md` | 8px |
| `--cm-touch-target` | 48px |

---

## 4. Touch Target

| Cihaz | Min Touch |
|-------|-----------|
| T01 Phone HD | 48×48px |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
