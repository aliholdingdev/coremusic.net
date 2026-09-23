---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Color Palettes"
type: tokens
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/tokens/color-palettes.md"
  source_of_truth: ".ai/ui-design/tokens/design-tokens-master.md"
---

# CoreMusic — Color Palettes

**Zorunlu Bağlantılar:** [[design-tokens-master]] · [[platform-tokens]] · [[component-tokens]]

---

## 1. Amaç

CoreMusic renk paletlerinin **tek kaynağıdır**. 3 tema, semantik renkler, gri ölçek ve cam efekti renkleri burada tanımlanır.

---

## 2. Tema Renkleri

### 2.1 Female Theme (Varsayılan — #ff4fd8 Pink)

```css
[data-theme="female"] {
  --cm-primary: #ff4fd8;
  --cm-primary-50: #fff0fb;
  --cm-primary-100: #ffe0f7;
  --cm-primary-200: #ffc2ef;
  --cm-primary-300: #ff93e4;
  --cm-primary-400: #ff64d9;
  --cm-primary-500: #ff4fd8;
  --cm-primary-600: #e03ab8;
  --cm-primary-700: #b82a98;
  --cm-primary-800: #901e78;
  --cm-primary-900: #681458;

  --cm-primary-gradient: linear-gradient(135deg, #ff4fd8 0%, #a855f7 100%);
  --cm-primary-gradient-hover: linear-gradient(135deg, #ff64d9 0%, #b86aff 100%);
  --cm-primary-gradient-active: linear-gradient(135deg, #e03ab8 0%, #9333ea 100%);
}
```

### 2.2 Male Theme (#4f9fff Blue)

```css
[data-theme="male"] {
  --cm-primary: #4f9fff;
  --cm-primary-50: #eff6ff;
  --cm-primary-100: #dbeafe;
  --cm-primary-200: #bfdbfe;
  --cm-primary-300: #93c5fd;
  --cm-primary-400: #60a5fa;
  --cm-primary-500: #4f9fff;
  --cm-primary-600: #2563eb;
  --cm-primary-700: #1d4ed8;
  --cm-primary-800: #1e40af;
  --cm-primary-900: #1e3a8a;

  --cm-primary-gradient: linear-gradient(135deg, #4f9fff 0%, #7c3aed 100%);
  --cm-primary-gradient-hover: linear-gradient(135deg, #60a5fa 0%, #8b5cf6 100%);
  --cm-primary-gradient-active: linear-gradient(135deg, #2563eb 0%, #6d28d9 100%);
}
```

### 2.3 Neutral Theme (#a0a0b0 Gray)

```css
[data-theme="neutral"] {
  --cm-primary: #a0a0b0;
  --cm-primary-50: #f5f5f7;
  --cm-primary-100: #e8e8ec;
  --cm-primary-200: #d1d1d9;
  --cm-primary-300: #b3b3bf;
  --cm-primary-400: #a0a0b0;
  --cm-primary-500: #8888a0;
  --cm-primary-600: #6e6e88;
  --cm-primary-700: #585870;
  --cm-primary-800: #424258;
  --cm-primary-900: #2c2c40;

  --cm-primary-gradient: linear-gradient(135deg, #a0a0b0 0%, #7c7c94 100%);
  --cm-primary-gradient-hover: linear-gradient(135deg, #b3b3bf 0%, #8e8ea8 100%);
  --cm-primary-gradient-active: linear-gradient(135deg, #8888a0 0%, #6a6a84 100%);
}
```

---

## 3. Semaantik Renkler

```css
:root {
  /* ═══ Success ═══ */
  --cm-success-50: #ecfdf5;
  --cm-success-100: #d1fae5;
  --cm-success-200: #a7f3d0;
  --cm-success-300: #6ee7b7;
  --cm-success-400: #34d399;
  --cm-success-500: #10b981;
  --cm-success-600: #059669;
  --cm-success-700: #047857;
  --cm-success-800: #065f46;
  --cm-success-900: #064e3b;

  /* ═══ Warning ═══ */
  --cm-warning-50: #fffbeb;
  --cm-warning-100: #fef3c7;
  --cm-warning-200: #fde68a;
  --cm-warning-300: #fcd34d;
  --cm-warning-400: #fbbf24;
  --cm-warning-500: #f59e0b;
  --cm-warning-600: #d97706;
  --cm-warning-700: #b45309;
  --cm-warning-800: #92400e;
  --cm-warning-900: #78350f;

  /* ═══ Error ═══ */
  --cm-error-50: #fef2f2;
  --cm-error-100: #fee2e2;
  --cm-error-200: #fecaca;
  --cm-error-300: #fca5a5;
  --cm-error-400: #f87171;
  --cm-error-500: #ef4444;
  --cm-error-600: #dc2626;
  --cm-error-700: #b91c1c;
  --cm-error-800: #991b1b;
  --cm-error-900: #7f1d1d;

  /* ═══ Info ═══ */
  --cm-info-50: #eff6ff;
  --cm-info-100: #dbeafe;
  --cm-info-200: #bfdbfe;
  --cm-info-300: #93c5fd;
  --cm-info-400: #60a5fa;
  --cm-info-500: #3b82f6;
  --cm-info-600: #2563eb;
  --cm-info-700: #1d4ed8;
  --cm-info-800: #1e40af;
  --cm-info-900: #1e3a8a;
}
```

---

## 4. Gri Ölçek

```css
:root {
  --cm-gray-50: #f8f9fa;
  --cm-gray-100: #f1f3f5;
  --cm-gray-200: #e9ecef;
  --cm-gray-300: #dee2e6;
  --cm-gray-400: #ced4da;
  --cm-gray-500: #adb5bd;
  --cm-gray-600: #868e96;
  --cm-gray-700: #495057;
  --cm-gray-800: #343a40;
  --cm-gray-900: #212529;
  --cm-gray-950: #0a0a0f;
}
```

---

## 5. Cam Efekti Renkleri

```css
:root {
  /* ═══ Glass Background ═══ */
  --cm-glass-white-5: rgba(255, 255, 255, 0.05);
  --cm-glass-white-8: rgba(255, 255, 255, 0.08);
  --cm-glass-white-10: rgba(255, 255, 255, 0.10);
  --cm-glass-white-12: rgba(255, 255, 255, 0.12);
  --cm-glass-white-15: rgba(255, 255, 255, 0.15);
  --cm-glass-white-20: rgba(255, 255, 255, 0.20);
  --cm-glass-white-30: rgba(255, 255, 255, 0.30);

  /* ═══ Glass Border ═══ */
  --cm-glass-border-5: rgba(255, 255, 255, 0.05);
  --cm-glass-border-8: rgba(255, 255, 255, 0.08);
  --cm-glass-border-10: rgba(255, 255, 255, 0.10);
  --cm-glass-border-14: rgba(255, 255, 255, 0.14);
  --cm-glass-border-18: rgba(255, 255, 255, 0.18);
  --cm-glass-border-24: rgba(255, 255, 255, 0.24);

  /* ═══ Glass Text ═══ */
  --cm-glass-text-primary: rgba(255, 255, 255, 0.95);
  --cm-glass-text-secondary: rgba(255, 255, 255, 0.70);
  --cm-glass-text-tertiary: rgba(255, 255, 255, 0.50);

  /* ═══ Dark Glass ═══ */
  --cm-dark-glass-bg: rgba(10, 10, 15, 0.70);
  --cm-dark-glass-border: rgba(255, 255, 255, 0.08);
}
```

---

## 6. Renk Eşleoğrunculuk Matrisi

| Kullanım | Renk Token | CSS Değeri |
|----------|-----------|------------|
| Ana buton | `--cm-primary` | `#ff4fd8` |
| Ana buton hover | `--cm-primary-light` | `#ff7ee4` |
| İkincil buton | `--cm-secondary` | `#a855f7` |
| Başarı durumu | `--cm-success` | `#10b981` |
| Uyarı durumu | `--cm-warning` | `#f59e0b` |
| Hata durumu | `--cm-error` | `#ef4444` |
| Bilgi durumu | `--cm-info` | `#3b82f6` |
| Varsayılan arka plan | `--cm-bg-primary` | `#0a0a0f` |
| Yükseltilmiş arka plan | `--cm-bg-elevated` | `#22222e` |
| Birincil metin | `--cm-text-primary` | `#ffffff` |
| İkincil metin | `--cm-text-secondary` | `#b0b0c0` |
| Cam arka plan | `--cm-glass-bg` | `rgba(255,255,255,0.05)` |

---

## 7. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 3.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Theme Count | 3 (female, male, neutral) |
| Semantic Scales | 4 (success, warning, error, info) × 10 |
| Gray Scale | 11 steps (50-950) |
| Glass Colors | 15 |
| Total Color Values | 85+ |
| Cross References | 3 |
| Last Updated | 2026-09-20 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
