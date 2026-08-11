---
title: CoreMusic — C07 Gender Button Component Spec
date: 2026-08-11
version: 2.0.0
platform: home-1024
---

# C07 — Gender Button

## BEM

```css
.gender-btn { }
.gender-btn--selected { }
.gender-btn--female { }
.gender-btn--male { }
.gender-btn--neutral { }
```

## ASCII Art

```
┌─────────────────────────────────────────────┐
│  [👩 ikon]  Kız                              │
│             Temizlik, saf duygular           │
│             Pembemsi renk tonları            │
│             ~200×80px, r:12px                │
└─────────────────────────────────────────────┘

Selected:
┌═════════════════════════════════════════════┐
│  [👩 ikon]  Kız  ← pembe vurgu              │
│  ║  border: 2px solid var(--theme-primary)  ║
│  ═══════════════════════════════════════    │
│  bg: rgba(255,79,216,0.2)                   │
└═════════════════════════════════════════════┘
```

## 3 Varyant

| Varyant | Başlık | Tema |
|---------|--------|------|
| Kız | Kız | female→pink |
| Erkek | Erkek | male→blue |
| Diğer | Cinsiyetimi belirtmek istemiyorum | neutral→default |

## ITCSS: 05_Pages
## WCAG: ✅ UYGUN (~200×80px)
## Kullanım: Select Gender
