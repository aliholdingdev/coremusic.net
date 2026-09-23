---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K11 Kullanıcı Deneyimi Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K11: Kullanıcı Deneyimi Layer

**Katman:** K11 (UX)
**Kapsam:** ITCSS 9-layer, BEM, Design Tokens, Theme Engine, PWA, A11y
**Sorumlu Agent:** UI Designer
**Bileşen Sayısı:** 40

---

## 1. Genel Bakış

K11 katmanı, CoreMusic'in tüm kullanıcı arayüzünü standartlaştıran UX katmanını içerir. ITCSS 9-layer yapısı, BEM isimlendirme ve design token sistemi kullanılır.

---

## 2. ITCSS 9-Layer

| # | Layer | Amaç | Dosya |
|---|-------|------|-------|
| 01 | Settings | CSS custom properties | `_variables.css` |
| 02 | Tools | Mixins, functions | `_mixins.scss` |
| 03 | Generic | Reset, normalize | `_reset.css` |
| 04 | Elements | Bare HTML elements | `_base.css` |
| 05 | Objects | Layout patterns | `_layout.css` |
| 06 | Components | BEM bileşenleri | `_header.css` |
| 07 | Utilities | Helper classes | `_helpers.css` |
| 08 | Devices | Behavioral overrides | `d-embedded.css` |
| 09 | Themes | Theme-specific | `_theme-dark.css` |

---

## 3. Design Tokens

### 3.1 Renk Token'ları

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--color-primary` | #6366f1 | Ana renk |
| `--color-secondary` | #ec4899 | İkincil renk |
| `--color-accent` | #06b6d4 | Vurgu |
| `--color-success` | #10b981 | Başarı |
| `--color-warning` | #f59e0b | Uyarı |
| `--color-danger` | #ef4444 | Hata |

### 3.2 Boşluk Token'ları

| Token | Değer |
|-------|-------|
| `--space-xs` | 4px |
| `--space-sm` | 8px |
| `--space-md` | 16px |
| `--space-lg` | 24px |
| `--space-xl` | 32px |
| `--space-2xl` | 48px |

### 3.3 Yükseklik Token'ları

| Token | Değer |
|-------|-------|
| `--header-h` | 60px (desktop: 70px, 4K: 80px) |
| `--footer-h` | 90px (desktop: 104px, 4K: 120px) |
| `--sidebar-w` | 280px (desktop: 300px, 4K: 350px) |

---

## 4. Theme Engine (ADR-044)

### 4.1 Gender-Based Tema

| Cinsiyet | Tema | Renk |
|----------|------|------|
| Female | Pink | #ec4899 |
| Male | Blue | #6366f1 |
| Neutral | Default | #0f172a |

### 4.2 Tema Geçişi

```javascript
// CSS custom properties ile anında geçiş
document.documentElement.style.setProperty('--bg-primary', '#0f172a');
document.documentElement.style.setProperty('--text-primary', '#f8fafc');
```

---

## 5. Responsive CSS

### 5.1 Breakpoint'ler

| Breakpoint | Değer | Cihaz |
|------------|-------|-------|
| Mobile | ≤767px | Telefon |
| Tablet | 768-1024px | Tablet |
| Embedded | ≤1024px | RPi5 |
| Desktop | ≥1920px | Masaüstü |
| 4K TV | ≥3840px | 4K TV |

---

## 6. WCAG 2.2 AA

| Kural | Değer |
|-------|-------|
| Touch Target | min 48×48px |
| Focus Visible | `:focus-visible` outline |
| Contrast | 4.5:1 minimum |
| Keyboard Nav | Full keyboard support |
| Screen Reader | ARIA labels |

---

## 7. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-001 | Vanilla JS + ITCSS, framework yasak |
| ADR-044 | Cinsiyet bazlı dinamik tema |

---

*K11 Kullanıcı Deneyimi Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
*Mode: Red Team · Human Mode · Truth Mode*
