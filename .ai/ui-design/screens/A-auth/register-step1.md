---
title: CoreMusic — Register Step 1 Screen Specification (1024×600, Linux Embedded RPi5)
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 2.0.0
authority: PNG Visual Analysis (direct inspection — Linux 1024 - Register Girl.png)
platform: Linux Embedded / Raspberry Pi 5 / 1024×600px
references:
  - [[00-mockup-index]]
  - [[01-component-inventory]]
  - [[_layout-patterns/05-auth-screen]]
  - [[A-auth/login]]
---

# CoreMusic — Register Step 1 Screen Specification

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

**Source Image:** `Linux 1024 - Register Girl.png`
**Layout Pattern:** Pattern 5: Auth Screen (78/22 split)
**Rota:** Auth flow'un 3a. adımı (Register Step 1/3)

---

## 1. ASCII WIREFRAME

```
┌── SAĞ PANEL (~22%, ~224px) ─────────────────────────────────────┐
│                                                                    │
│  [女神 ikonu — beyaz çizim]                                       │
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
│  ── veya alternatif ile devam et ──                               │
│  [🍎][G][f][💬][📷][🎵][🎤]                                        │
│                                                                    │
│  Hesabın yok mu?  Kayıt Ol                                        │
└──────────────────────────────────────────────────────────────────┘

Adım 1/3: Kullanıcı Adı + E-posta
Sol alan: Aynı Select Gender arka planı
```

---

## 2. FORM DETAYLARI

| Alan | Tip | Yükseklik | Not |
|------|-----|-----------|-----|
| Kullanıcı Adı | text | 56px | Zorunlu |
| E-posta | email | 56px | Zorunlu |
| Devam Et | button | 56px | Pembe, full-width |

---

## 3. WCAG

| Kriter | Durum |
|--------|-------|
| Touch target (input) | ✅ 56px |
| Touch target (buton) | ✅ 56px |
| Touch target (social) | ✅ 52×52px |
| Focus indicator | ✅ |
| Error messaging | ⚠️ EKSİK |

---

*Register Step 1 Screen Spec v2.0.0 — CoreMusic UI Design System*
*Last Updated: 2026-08-11*
