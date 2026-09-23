---
title: "CoreMusic — K11 UX CLAUDE.md"
type: layer-guide
folder: "architecture/k11-ux"
category: vault
date: 2026-09-20
status: active
version: 1.0.0
authority: SSOT
---

# K11 UX — CLAUDE.md

## 1. Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Framework yasak (ADR-001) | Kod revert edilir |
| 2 | ITCSS sırası değişmez | Katman ihlali |
| 3 | BEM naming zorunlu | Tutarsızlık |
| 4 | Hardcoded value yasak | CSS variable kullan |

## 2. Yasaklı Örüntüler

```css
/* ❌ YASAK */
.player { height: 90px; }           /* Hardcoded */
@import url('bootstrap.css');       /* Framework */
var(--primary) !important;          /* !important */

/* ✅ DOĞRU */
.player { height: var(--footer-h); }
/* CSS variables ile */
```

## 3. Design Token Değerleri

| Token | Değer |
|-------|-------|
| `--color-primary` | #6366f1 |
| `--header-h` | 60px |
| `--footer-h` | 90px |
| `--space-md` | 16px |
| `--radius-md` | 8px |

## 4. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-001 | Vanilla JS + ITCSS, framework yasak |
| ADR-044 | Cinsiyet bazlı dinamik tema |

---

*K11 CLAUDE.md v1.0.0 — CoreMusic*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
