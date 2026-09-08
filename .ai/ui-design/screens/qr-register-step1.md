---
title: "Register Step 1 — Quick Reference"
type: ascii-qr
screen_id: "S16"
resolution: "1024x600"
layout_pattern: "Auth 78/22"
components: [C04, C06]
png: "shared-1024/Linux  1024 - Register Girl.png"
full_spec: "A-auth/register-step1.md"
---

# Register Step 1

## Layout Wireframe

```
┌── SAĞ PANEL (~22%, ~224px) ─────────────────────────────────────┐
│                                                                    │
│  [Kadın ikonu — beyaz çizim]                                      │
│                                                                    │
│  Hesap Oluştur                                                    │
│  CoreMusic ailesine katıl,                                         │
│  müziğin keyfini çıkar                                             │
│                                                                    │
│  Kullanıcı Adı                                                     │
│  ┌───────────────────────────────────┐                            │
│  │ (C06 input)                       │                            │
│  └───────────────────────────────────┘                            │
│  E-posta                                                          │
│  ┌───────────────────────────────────┐                            │
│  │ (C06 input)                       │                            │
│  └───────────────────────────────────┘                            │
│                                                                    │
│  [Devam Et] (C04, pembe, full-width)                              │
│                                                                    │
│  ── veya ──                                                       │
│  [🍎] [G] [f]  (Apple, Google, Facebook)                          │
│  [💬] [📷] [🎵]  (WhatsApp, Instagram, TikTok)                    │
│  [🎵]  (Spotify)                                                  │
│  Her biri: C08, 52×52px, platform-specific renkler                │
│                                                                    │
│  Hesabın var mı?  Giriş Yap                                       │
└──────────────────────────────────────────────────────────────────┘

Adım 1/3: Kullanıcı Adı + E-posta
Sol alan: Aynı Select Gender arka planı (kadın fotoğrafı, pembe çiçekli manzara)
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
| C08 | `.register-form__social-btn` | Social bölümü | 52×52px, radius: 12px |

## Form Fields

| Alan | Tip | Yükseklik | Not |
|------|-----|-----------|-----|
| Kullanıcı Adı | text | 56px | Zorunlu, 3-20 karakter |
| E-posta | email | 56px | Zorunlu |
| Devam Et | button (C04) | 56px | Pembe, full-width |

## Social Buttons (C08)

| Satır | Butonlar |
|-------|---------|
| 1 | 🍎 Apple, G Google, f Facebook |
| 2 | 💬 WhatsApp, 📷 Instagram, 🎵 TikTok |
| 3 | 🎵 Spotify |

Her buton: 52×52px, border-radius: 12px

## Theme Accent Colors

| Tema | Accent | Accent-BG |
|------|--------|-----------|
| Female | `#ff4fd8` | `rgba(255,79,216,0.15)` |
| Male | `#4f9fff` | `rgba(79,159,255,0.15)` |
| Neutral | `#a0a0b0` | `rgba(160,160,176,0.15)` |

## CSS Hints

```css
.register-form { display: flex; flex-direction: column; gap: var(--space-3); width: 100%; }
.register-form__input { width: 100%; min-height: 56px; background: var(--input-bg); border: var(--input-border); border-radius: var(--input-radius); }
.register-form__input:focus { border: var(--input-focus-border); box-shadow: 0 0 0 3px var(--accent-bg); }
.register-form__input.has-error { border-color: var(--error); }
```
