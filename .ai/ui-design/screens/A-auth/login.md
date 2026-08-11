---
title: CoreMusic — Login Screen Specification (1024×600, Linux Embedded RPi5)
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 2.0.0
authority: PNG Visual Analysis (direct inspection — Linux 1024 - Login Girl.png)
platform: Linux Embedded / Raspberry Pi 5 / 1024×600px
references:
  - [[00-mockup-index]]
  - [[01-component-inventory]]
  - [[_layout-patterns/05-auth-screen]]
  - [[A-auth/gender-select]]
---

# CoreMusic — Login Screen Specification

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

**Source Image:** `Linux 1024 - Login Girl.png`
**Confidence:** High — directly viewed from PNG screenshot.
**Layout Pattern:** Pattern 5: Auth Screen (78/22 split)
**Rota:** Auth flow'un 2. adımı

---

## 1. ASCII WIREFRAME

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ 1024×600 — Pattern 5: Auth Screen (78/22) — Header/Footer YOK                                  │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                                  │
│  ┌── SOL ALAN (~78%, ~800px) ────────────────────┐  ┌── SAĞ PANEL (~22%, ~224px) ──────────┐  │
│  │                                                 │  │                                       │  │
│  │  [CoreMusic Logo]                               │  │  [女神 ikonu — beyaz çizim]           │  │
│  │  Seni Tanıyalım                                 │  │                                       │  │
│  │  Sisteme                                         │  │  Hoş Geldin                           │  │
│  │  milyonlarca şarkı, özel                        │  │  Hesabına giriş yap, müziğin keyfini  │  │
│  │  önerilerin, playlistler,                       │  │  çıkar                                 │  │
│  │  sonntaxlar, keyfi Benim için.                 │  │                                       │  │
│  │                                                 │  │  E-posta, Telefon veya Kullanıcı Adı  │  │
│  │  [Tam kaplama arka plan fotoğrafı]              │  │  ┌───────────────────────────────┐    │  │
│  │  (aynı arka plan — gender'a göre renk değişir) │  │  │ (C06 input)                    │    │  │
│  │                                                 │  │  └───────────────────────────────┘    │  │
│  │                                                 │  │                                       │  │
│  │                                                 │  │  Şifre                                │  │
│  │                                                 │  │  ┌───────────────────────────────┐    │  │
│  │                                                 │  │  │ ●●●●●● (C06 input, şifre)     │    │  │
│  │                                                 │  │  └───────────────────────────────┘    │  │
│  │                                                 │  │                                       │  │
│  │                                                 │  │  ☑ Beni Hatırla    Şifremi Unuttum   │  │
│  │                                                 │  │  (11px, muted)    (11px, accent link) │  │
│  │                                                 │  │                                       │  │
│  │                                                 │  │  [Giriş Yap] (C04, pembe, full-width) │  │
│  │                                                 │  │                                       │  │
│  │                                                 │  │  ── veya alternatif ile devam et ──   │  │
│  │                                                 │  │  (11px, muted, ortala)                │  │
│  │                                                 │  │                                       │  │
│  │                                                 │  │  [🍎] [G] [f]  (Apple, Google, FB)    │  │
│  │                                                 │  │  [💬] [📷] [🎵]  (WA, IG, TikTok)    │  │
│  │                                                 │  │  [🎤] (mikrofon — sesli giriş)        │  │
│  │                                                 │  │  Her biri: C08, 52×52px               │  │
│  │                                                 │  │                                       │  │
│  │                                                 │  │  Hesabın yok mu?  Kayıt Ol            │  │
│  │                                                 │  │  (11px, muted)  (11px, accent link)   │  │
│  └─────────────────────────────────────────────────┘  └───────────────────────────────────────┘   │
│                                                                                                  │
│ Auth akışı: Select Gender (1) → Login (2) → Register (3)                                      │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. FORM DETAYLARI

### 2.1 — Email/Username Input (C06)

| Özellik | Değer |
|---------|-------|
| Label | "E-posta, Telefon veya Kullanıcı Adı" (11px, muted) |
| Placeholder | — |
| Yükseklik | 56px |
| Genişlik | Tam genişlik (~200px) |
| Background | `rgba(255,255,255,0.1)` |
| Border | 1px solid `rgba(255,255,255,0.2)` |
| Focus border | `var(--theme-primary)` |

### 2.2 — Password Input (C06)

| Özellik | Değer |
|---------|-------|
| Label | "Şifre" (11px, muted) |
| Type | password |
| Yükseklik | 56px |

### 2.3 — Remember Me + Forgot Password

| Özellik | Değer |
|---------|-------|
| Checkbox | ☑ Beni Hatırla (12px) |
| Link | Şifremi Unuttum (11px, accent, sağa hizalı) |

### 2.4 — Social Buttons (C08)

| Satır | Butonlar |
|-------|---------|
| 1 | 🍎 Apple, G Google, f Facebook |
| 2 | 💬 WhatsApp, 📷 Instagram, 🎵 TikTok |
| 3 | 🎤 Mikrofon |

Her buton: 52×52px, border-radius: 12px

---

## 3. WCAG DURUMU

| Kriter | Durum |
|--------|-------|
| Touch target (input) | ✅ 56px |
| Touch target (buton) | ✅ 56px |
| Touch target (social) | ✅ 52×52px |
| Touch target (checkbox) | ⚠️ ~16px → 44px |
| Focus indicator | ✅ |
| Keyboard nav | ✅ |
| ARIA labels | ⚠️ EKSİK |

---

*Login Screen Spec v2.0.0 — CoreMusic UI Design System*
*Last Updated: 2026-08-11*
