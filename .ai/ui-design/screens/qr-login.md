---
title: "Login — Quick Reference"
type: ascii-qr
screen_id: "S15"
resolution: "1024x600"
layout_pattern: "Auth 78/22"
components: [C04, C06, C08]
png: "shared-1024/Linux  1024 - Login Girl.png"
full_spec: "A-auth/login.md"
---

# Login

## Layout Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ 1024×600 — Pattern 5: Auth Screen (78/22) — Header/Footer YOK                                  │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                                  │
│  ┌── SOL ALAN (~78%, ~800px) ────────────────────┐  ┌── SAĞ PANEL (~22%, ~224px) ──────────┐  │
│  │                                                 │  │                                       │  │
│  │  [CoreMusic Logo]                               │  │  [Kadın ikonu — beyaz çizim]          │  │
│  │                                                 │  │                                       │  │
│  │  "Aşkınla"                                      │  │  Hoş Geldin                           │  │
│  │  "milkyenine!"                                  │  │  Hesabına giriş yap, müziğin keyfini  │  │
│  │  (dekoratif, Bickham Script)                    │  │  çıkar                                 │  │
│  │                                                 │  │                                       │  │
│  │  "Sistem. Milyonlarca şarkı, özel               │  │  E-posta, Telefon veya Kullanıcı Adı  │  │
│  │  oluşturulmuş playlistler, sonsuz müzik         │  │  ┌───────────────────────────────┐    │  │
│  │  keyfi. Senin için"                             │  │  │ (C06 input, 56px)              │    │  │
│  │                                                 │  │  └───────────────────────────────┘    │  │
│  │  [Tam kaplama arka plan fotoğrafı]              │  │                                       │  │
│  │  (kadın fotoğrafı — pembe çiçekli manzara)     │  │  Şifre                                │  │
│  │                                                 │  │  ┌───────────────────────────────┐    │  │
│  │                                                 │  │  │ ●●●●●● (C06 input, şifre)     │    │  │
│  │                                                 │  │  └───────────────────────────────┘    │  │
│  │                                                 │  │                                       │  │
│  │                                                 │  │  ☑ Beni Hatırla    Şifremi Unuttum   │  │
│  │                                                 │  │                                       │  │
│  │                                                 │  │  [Giriş Yap] (C04, pembe, full-width) │  │
│  │                                                 │  │                                       │  │
│  │                                                 │  │  ── veya ──                           │  │
│  │                                                 │  │                                       │  │
│  │                                                 │  │  [🍎] [G] [f]  (Apple, Google, FB)    │  │
│  │                                                 │  │  [💬] [📷] [🎵]  (WA, IG, TikTok)    │  │
│  │                                                 │  │  [🎵] (Spotify)                      │  │
│  │                                                 │  │  Her biri: C08, 52×52px               │  │
│  │                                                 │  │                                       │  │
│  │                                                 │  │  Hesabın yok mu?  Kayıt Ol            │  │
│  └─────────────────────────────────────────────────┘  └───────────────────────────────────────┘   │
│                                                                                                  │
│  Auth akışı: Select Gender (1) → Login (2) → Register (3)                                      │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Key Measurements

| Element | Position | Size | Token |
|---------|----------|------|-------|
| Sol alan | x:0-800 | ~78% | — |
| Sağ panel | x:800-1024 | 224px | — |
| Glass bg | — | — | `--glass-bg: rgba(255,255,255,0.08)` |
| Glass blur | — | blur(20px) | `--glass-blur` |
| Input yüksekliği | — | 56px | `--input-h-lg` |
| Buton yüksekliği | — | 56px | `--btn-h-lg` |
| Social buton | — | 52×52px | — |
| Touch target | — | ≥48px | `--touch-min` |

## Component Map

| ID | BEM Class | Position | Size |
|----|-----------| Sağ panel | Size |
| C06 | `.login-form__input` | Form alanları | 100%×56px |
| C04 | `.login-form__submit` | Form sonu | full-width×56px |
| C08 | `.login-form__social-btn` | Social bölümü | 52×52px, radius: 12px |

## Form Fields

| Alan | Tip | Yükseklik | Not |
|------|-----|-----------|-----|
| E-posta/Kullanıcı Adı | text | 56px | Label: 11px muted |
| Şifre | password | 56px | Label: 11px muted |
| Beni Hatırla | checkbox | — | 12px |
| Şifremi Unuttum | link | — | 11px, accent |
| Giriş Yap | button (C04) | 56px | pembe, full-width |

## Social Buttons (C08)

| Satır | Butonlar | Renkler |
|-------|---------|---------|
| 1 | 🍎 Apple, G Google, f Facebook | Siyah, Kırmızı/Beyaz, Mavi |
| 2 | 💬 WhatsApp, 📷 Instagram, 🎵 TikTok | Yeşil, Pembe/Mor, Siyah |
| 3 | 🎵 Spotify | Yeşil |

Her buton: 52×52px, border-radius: 12px, platform-specific renkler

## Theme Accent Colors

| Tema | Accent | Accent-BG |
|------|--------|-----------|
| Female | `#ff4fd8` | `rgba(255,79,216,0.15)` |
| Male | `#4f9fff` | `rgba(79,159,255,0.15)` |
| Neutral | `#a0a0b0` | `rgba(160,160,176,0.15)` |

## CSS Hints

```css
.auth-screen__panel { width: 224px; background: var(--glass-bg); backdrop-filter: var(--glass-blur) var(--glass-saturate); border-left: 1px solid var(--glass-border); }
.login-form__input { width: 100%; min-height: 56px; background: var(--input-bg); border: var(--input-border); border-radius: var(--input-radius); }
.login-form__social-btn { width: 52px; height: 52px; background: var(--glass-bg); border: 1px solid var(--glass-border); border-radius: var(--radius-lg); }
.login-form__input:focus { border: var(--input-focus-border); box-shadow: 0 0 0 3px var(--accent-bg); }
```
