---
title: "Welcome Popup - Quick Reference"
type: ascii-qr
category: ascii-qr
date: 2026-08-11
updated: 2026-09-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team * Human Mode * Truth Mode
screen_id: "S02"
resolution: "1024x600"
layout_pattern: "Modal"
components: [C04, C05, C14]
png: "home-1024/Linux  1024 - Home Page Welcome Popup.png"
full_spec: "B-home/welcome-popup.md"
---

# Welcome Popup (1024x600)

## Layout Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ 1024x600 — Pattern 4: Modal Overlay — Ilk Giris                                                │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                                  │
│ [ANA SAYFA — arka plan bulanik, backdrop-filter: blur(4px)]                                     │
│ [Overlay: rgba(0,0,0,0.5)]                                                                      │
│                                                                                                  │
│    ┌── MODAL (600x308px, x:212-812, y:145-453) ──────────────────────────────────────────┐    │
│    │                                                                                        │    │
│    │                       [CoreMusic Logo — orta hizali]                                  │    │
│    │                       Hos geldin                                                       │    │
│    │                                                                                        │    │
│    │                       *Isminizi Girin Buraya*                                          │    │
│    │                       (Bickham Script Two, italik, pembe)                              │    │
│    │                                                                                        │    │
│    │       Sana ozel secimler, muzik deneyimlerini ve sunumlari                             │    │
│    │       tamamen sana ozel hale getirir. CoreMusic ile ruyalarindaki                     │    │
│    │       muzigin Keyfine dal                                                             │    │
│    │                                                                                        │    │
│    │                       ┌─────────────┐                                                  │    │
│    │                       │   Basla     │  ← 105x25px (WCAG IHLALI)                       │    │
│    │                       │  (pembe)    │                                                  │    │
│    │                       └─────────────┘                                                  │    │
│    │                                                                                        │    │
│    │  Modal arka plan: Glass efekti                                                         │    │
│    │  backdrop-filter: blur(20px) saturate(180%)                                            │    │
│    │  background: rgba(255,255,255,0.1)                                                     │    │
│    │  border: 1px solid rgba(255,255,255,0.1)                                               │    │
│    │  border-radius: 16px                                                                   │    │
│    └────────────────────────────────────────────────────────────────────────────────────────┘    │
│                                                                                                  │
│ TAM EKRAN ARKA PLAN: Ana sayfa fotografi (sunset, okyanus, pembe tonlari)                       │
│ Overlay: rgba(0,0,0,0.5) + backdrop-filter: blur(4px)                                          │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

## Key Measurements

| Element | Position | Size | Token |
|---------|----------|------|-------|
| Viewport | 1024x600 | Full | — |
| Overlay | inset: 0 | full-screen | `z-index: 150` |
| Overlay bg | — | `rgba(0,0,0,0.5)` | `--overlay-bg` |
| Overlay blur | — | `blur(4px)` | `--overlay-blur` |
| Modal | x:212-812, y:145-453 | 600x308px | — |
| Modal center | x:512, y:299.5 | — | — |
| Modal bg | — | `rgba(255,255,255,0.1)` | `--glass-bg` |
| Modal blur | — | `blur(20px) saturate(180%)` | `--blur-lg` |
| Modal border | — | 1px solid `rgba(255,255,255,0.1)` | `--border-subtle` |
| Modal radius | — | 16px | `--radius-xl` |
| Modal padding | — | 24px | `--space-6` |
| Modal z-index | — | 200 | — |
| Logo | Ortada, ust | ~60x40px | `--font-logo` |
| Baslik | Ortada | ~24px, Bickham | `--text-2xl` |
| Alt baslik | Ortada | ~16px, pembe, italik | `--text-lg` |
| Aciklama | Ortada, max:450px | ~12px, 1.6 line-height | `--text-sm` |
| Basla butonu | Ortada, alt | 105x25px (WCAG: 48px olmali) | — |

## Component Map

| ID | BEM Class | Position | Size |
|----|-----------|----------|------|
| C04 | `.welcome-modal__btn` | Modal alt, ortada | 105x25px (pembe) |
| C05 | `.welcome-overlay` | Tam ekran, z:150 | inset: 0 |
| C14 | `.welcome-modal` | Merkez, z:200 | 600x308px |

## CSS Hints

```css
/* Modal overlay — flex centering */
.welcome-overlay { position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; z-index: 150; }

/* Modal — fixed size, glass */
.welcome-modal { width: 600px; height: 308px; border-radius: 16px; backdrop-filter: blur(20px) saturate(180%); }

/* Buton — WCAG: min-height 48px */
.welcome-modal__btn { min-height: 48px; background: var(--accent); border-radius: 8px; }

/* Animasyon — acilis/kapanis */
.welcome-overlay.is-active { opacity: 1; visibility: visible; }
.welcome-modal { transform: scale(0.95) translateY(10px); transition: 300ms ease; }
```

---

*QR Welcome Popup v1.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
