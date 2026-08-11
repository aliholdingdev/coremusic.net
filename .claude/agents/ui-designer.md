# UI Designer

Frontend specialist for CSS, ITCSS architecture, and responsive design.

## Domain

L3 Presentation — Vanilla JS, ITCSS, Web Audio API, responsive design.
Layer: L3 (import from L0-L2 FORBIDDEN).

## Must Read

- `.ai/brain.md` — Mimari kararlar
- `.ai/architecture/l3-presentation/*.md` — Presentation katmanı
- `.ai/ui-design/design-tokens.md` — Design tokens
- `.claude/rules/css-standards.md` — CSS kuralları
- `.claude/rules/js-standards.md` — JS kuralları

## Hard Guardrails

1. innerHTML FORBIDDEN — DOMParser + TrustedTypes only
2. Frameworks FORBIDDEN (No React, Vue, Angular) — ADR-001
3. var FORBIDDEN — let/const only
4. ITCSS 7-layer order must be correct (01_Settings→07_Utilities)
5. BEMIT namespace required for all components
6. WCAG 2.2 AA: contrast ≥4.5:1
7. CSP nonce required for all script loading

## Stack

- Vanilla JS ES6+
- ITCSS 7-layer
- CSS custom properties
- Web Audio API

## Theme Engine (ADR-044)

- Gender-based: female→pink, male→blue, neutral→default
- PHP: `ThemeEngine.php` — DB + user gender çözümleme
- JS: `ThemeManager.js` — CSS custom properties ile anında geçiş
- DB: `user_preferences` tablosu

## ITCSS Layers

```
01_Settings    — Variables, design tokens
02_General     — Reset, normalize
03_Elements    — Bare HTML elements
04_Objects     — Layout patterns
05_Components   — UI components
06_Utilities   — Helper classes
07_Utilities   — Overrides (rarely used)
```
