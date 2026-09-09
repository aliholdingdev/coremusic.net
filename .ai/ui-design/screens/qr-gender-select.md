---
title: "Select Gender - Quick Reference"
type: ascii-qr
category: ascii-qr
date: 2026-08-11
updated: 2026-09-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team * Human Mode * Truth Mode
screen_id: "S14"
resolution: "1024x600"
layout_pattern: "Auth 72/28"
components: [C07]
png: "shared-1024/Linux  1024 - Select Gender.png + Linux  1024 - Select Gender - selected.png"
full_spec: "A-auth/gender-select.md"
---

# Select Gender

## Layout Wireframe

```
┌─────────────────────────────────────────────────────────────┐
│ 1024×600 — Auth Screen — Pattern 5: 72/28 Split            │
├─────────────────────────────────────────────────────────────┤
│ Sol Alan: x:0-740, ~72% (manzara fotoğrafı)               │
│ Sağ Panel: x:740-1024, ~284px (glass panel)                │
│ Header: YOK                                                 │
│ Footer: YOK                                                 │
└─────────────────────────────────────────────────────────────┘
```

```
┌── SAĞ PANEL (284px, glass) ─────────────────────────────┐
│                                                          │
│  x:780 y:60                                             │
│  [Kadın ikonu — line art, beyaz, ~80×80px]              │
│  "Seni Tanıyalım"                                       │
│  "Müzik deneyimini sana özel hale getirelim"            │
│                                                          │
│  x:780 y:160                                            │
│  ┌──────────────────────────────────────────────┐       │
│  │ [👩] Kız                     C07 Gender Button│       │
│  │        Temizlik, saf duygular  (~284×60px)  │       │
│  │        Pembemsi renk tonları                 │       │
│  └──────────────────────────────────────────────┘       │
│  ┌──────────────────────────────────────────────┐       │
│  │ [👨] Erkek                    C07 Gender Button│       │
│  │        Güçlü, klasik tonlar     (~284×60px)  │       │
│  │        Mavimsi renk tonları                 │       │
│  └──────────────────────────────────────────────┘       │
│  ┌──────────────────────────────────────────────┐       │
│  │ [🤷] Cinsiyetimi belirtmek istemiyorum       │       │
│  │        Nötr renk tonları        (~284×60px)  │       │
│  └──────────────────────────────────────────────┘       │
│                                                          │
│  x:780 y:380                                            │
│  [Devam Et] butonu                                      │
│                                                          │
│  x:780 y:460                                            │
│  "Hayatın rastlantılarla dolu... ♥"                     │
│                                                          │
│  x:780 y:560                                            │
│  Devam ederek Gizlilik Politikamızı kabul etmiş olursunuz│
│                                                          │
└──────────────────────────────────────────────────────────┘
```

## Key Measurements

| Element | Position | Size | Token |
|---------|----------|------|-------|
| Sol alan | x:0-740 | ~72% | — |
| Sağ panel | x:740-1024 | 284px | — |
| Glass bg | — | — | `--glass-bg: rgba(255,255,255,0.08)` |
| Glass blur | — | blur(20px) | `--glass-blur` |
| Glass border | — | 1px solid rgba(255,255,255,0.1) | `--border-subtle` |
| Panel padding | — | 20px | `--space-5` |
| Gender button | — | ~284×60px | `--btn-h-lg` |
| Devam Et butonu | — | full-width, 56px | `--btn-h` |
| CoreMusic Logo | x:60 y:200 | ~120×30px | — |

## Component Map

| ID | BEM Class | Position | Size |
|----|-----------|----------|------|
| C07 | `.gender-btn` | Sağ panel, x:780 y:160 | 284×60px, radius: 12px |

## C07 Gender Button Detail

| Özellik | Default | Seçili (Selected) |
|---------|---------|-------------------|
| Background | `rgba(255,255,255,0.05)` | `rgba(255,79,216,0.2)` |
| Border | 1px solid `rgba(255,255,255,0.15)` | 2px solid `var(--theme-primary)` |
| Border-radius | 12px | 12px |
| İkon boyutu | ~30×30px | ~30×30px |
| Başlık fontu | 14px, 600 | 14px, 600 |
| Alt metin fontu | 11px, 400 | 11px, 400 |
| Touch target | 60px (WCAG) | 60px |

### 3 Varyant

| Varyant | İkon | Başlık | Alt Metin | data-gender |
|---------|------|--------|-----------|-------------|
| Kız | 👩 | Kız | Temizlik, saf duygular · Pembemsi renk tonları | `female` |
| Erkek | 👨 | Erkek | Güçlü, klasik tonlar · Mavimsi renk tonları | `male` |
| Diğer | 🤷 | Cinsiyetimi belirtmek istemiyorum | Nötr renk tonları | `neutral` |

## Devam Et Butonu

| Özellik | Pasif | Aktif |
|---------|-------|-------|
| Background | transparent | `var(--theme-primary)` |
| Border | 1px solid rgba(255,255,255,0.2) | none |
| Text | rgba(255,255,255,0.5) | #ffffff |
| Font | 14px, 600 | 14px, 600 |
| Border-radius | 8px | 8px |
| Yükseklik | 56px | 56px |

## Theme Effects

| Seçim | data-gender | --theme-primary |
|-------|-------------|-----------------|
| Kız | `female` | `#ff4fd8` (pembe) |
| Erkek | `male` | `#4f9fff` (mavi) |
| Diğer | `neutral` | `#a0a0b0` (nötr) |

## CSS Hints

```css
.auth-screen { display: flex; width: 100vw; height: 100vh; overflow: hidden; }
.auth-screen__panel { width: 284px; background: var(--glass-bg); backdrop-filter: var(--glass-blur) var(--glass-saturate); border-left: 1px solid var(--glass-border); }
.gender-btn { width: 100%; min-height: 60px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.15); border-radius: var(--radius-lg); }
.gender-btn.is-selected { background: var(--accent-bg); border: 2px solid var(--accent); box-shadow: var(--accent-glow); }
```

---

*QR Gender Select v1.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
