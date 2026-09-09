---
title: "CoreMusic - C07 Gender Button Component Spec"
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

---

*Component Spec C07 Gender Button v2.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
