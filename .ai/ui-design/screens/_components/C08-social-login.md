---
title: "CoreMusic - C08 Social Login Component Spec"
type: component-spec
category: component-spec
date: 2026-08-11
updated: 2026-09-08
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team * Human Mode * Truth Mode
platform: home-1024
---

# C08 — Social Login Button

## BEM

```css
.social-btn { }
.social-btn--apple { }
.social-btn--google { }
.social-btn--facebook { }
.social-btn--whatsapp { }
.social-btn--instagram { }
.social-btn--tiktok { }
.social-btn--mic { }
```

## ASCII Art

```
Satır 1:  [🍎 Apple]  [G Google]  [f Facebook]
Satır 2:  [💬 WhatsApp] [📷 Instagram] [🎵 TikTok]
Satır 3:  [🎤 Mikrofon]

Her buton: 52×52px, r:12px
```

## Servis Renkleri

| Servis | Background | İkon |
|--------|-----------|------|
| Apple | #000000 | Beyaz |
| Google | #ffffff | Google logosu |
| Facebook | #1877F2 | Beyaz f |
| WhatsApp | #25D366 | Beyaz |
| Instagram | #E4405F | Beyaz |
| TikTok | #000000 | Beyaz |
| Mikrofon | var(--theme-primary) | Beyaz |

## ITCSS: 05_Pages
## WCAG: ✅ UYGUN (52×52px)
## Kullanım: Login, Register

---

*Component Spec C08 Social Login v2.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
