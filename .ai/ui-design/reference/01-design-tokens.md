---
title: "Design Token Reference (home-1024)"
type: reference
category: design-tokens
updated: 2026-08-11
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Design Token Reference (home-1024)

**Zorunlu Baglantilar:** [[00-mockup-index]] · [[02-implementation-plan]] · [[03-accessibility-gaps]]

---

## 1. Amaç

CoreMusic frontend kodlamasında kullanılacak tüm CSS custom property'lerinin (design token) tek kaynağıdır. Ajanlar bu dosyadan tokenize değerleri okur, hardcoded değer kullanmaz.

---

## 2. Color Tokens

### 2.1 Theme Colors

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--theme-primary` | `#e91e63` (pink), `#2196f3` (blue), `#9c27b0` (default) | Ana tema rengi |
| `--theme-primary-rgb` | `233,30,99` / `33,150,243` / `156,39,176` | rgba() için |
| `--theme-primary-hover` | `%10 daha koyu` | Hover durumu |
| `--theme-primary-active` | `%20 daha koyu` | Active durumu |
| `--theme-secondary` | `#ff9800` | İkincil vurgu |
| `--theme-accent` | `#4caf50` | Accent rengi |

### 2.2 Text Colors

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--text-primary` | `rgba(255,255,255,0.95)` | Ana metin |
| `--text-secondary` | `rgba(255,255,255,0.70)` | İkincil metin |
| `--text-tertiary` | `rgba(255,255,255,0.50)` | Placeholder, disabled |
| `--text-inverse` | `rgba(0,0,0,0.87)` | Açık arka plan üzerinde |
| `--text-link` | `var(--theme-primary)` | Link rengi |
| `--text-link-hover` | `var(--theme-primary-hover)` | Link hover |

### 2.3 Glass Colors

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--glass-bg` | `rgba(255,255,255,0.08)` | Glass panel arka plan |
| `--glass-bg-hover` | `rgba(255,255,255,0.12)` | Glass hover |
| `--glass-bg-active` | `rgba(255,255,255,0.16)` | Glass active |
| `--glass-border` | `rgba(255,255,255,0.12)` | Glass kenar çizgisi |
| `--glass-border-hover` | `rgba(255,255,255,0.20)` | Glass hover kenar |
| `--glass-shadow` | `0 8px 32px rgba(0,0,0,0.37)` | Glass gölge |

### 2.4 Border Colors

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--border-default` | `rgba(255,255,255,0.10)` | Varsayılan kenar |
| `--border-subtle` | `rgba(255,255,255,0.05)` | İnce kenar |
| `--border-strong` | `rgba(255,255,255,0.20)` | Güçlü kenar |
| `--border-focus` | `var(--theme-primary)` | Focus durumu |

### 2.5 Overlay Colors

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--overlay-default` | `rgba(0,0,0,0.50)` | Modal overlay |
| `--overlay-light` | `rgba(0,0,0,0.30)` | Hafif overlay |
| `--overlay-heavy` | `rgba(0,0,0,0.70)` | Koyu overlay |

### 2.6 Status Colors

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--status-success` | `#4caf50` | Başarılı |
| `--status-warning` | `#ff9800` | Uyarı |
| `--status-error` | `#f44336` | Hata |
| `--status-info` | `#2196f3` | Bilgi |
| `--status-connected` | `#4caf50` | Bağlı |
| `--status-connecting` | `#ff9800` | Bağlanıyor |
| `--status-disconnected` | `#f44336` | Bağlantı yok |

---

## 3. Typography Tokens

### 3.1 Font Family

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--font-family-primary` | `'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif` | Ana yazı tipi |
| `--font-family-mono` | `'JetBrains Mono', 'Fira Code', 'Consolas', monospace` | Kod/terminal |
| `--font-family-display` | `'Inter', sans-serif` | Başlık/hero |

### 3.2 Font Sizes

| Token | Değer | px | rem |
|-------|-------|-----|-----|
| `--text-xs` | `0.75rem` | 12px | 0.75 |
| `--text-sm` | `0.875rem` | 14px | 0.875 |
| `--text-base` | `1rem` | 16px | 1 |
| `--text-md` | `1.125rem` | 18px | 1.125 |
| `--text-lg` | `1.25rem` | 20px | 1.25 |
| `--text-xl` | `1.5rem` | 24px | 1.5 |
| `--text-2xl` | `1.875rem` | 30px | 1.875 |
| `--text-3xl` | `2.25rem` | 36px | 2.25 |
| `--text-4xl` | `3rem` | 48px | 3 |

### 3.3 Font Weights

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--font-normal` | `400` | Normal metin |
| `--font-medium` | `500` | Vurgulu metin |
| `--font-semibold` | `600` | Başlıklar |
| `--font-bold` | `700` | Ana başlıklar |

### 3.4 Line Heights

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--leading-none` | `1` | Başlıklar |
| `--leading-tight` | `1.25` | Kısa metin |
| `--leading-normal` | `1.5` | Ana metin |
| `--leading-relaxed` | `1.75` | Uzun metin |

---

## 4. Spacing Tokens

| Token | Değer | px | rem | Kullanım |
|-------|-------|-----|-----|----------|
| `--space-0` | `0` | 0px | 0 | Sıfır boşluk |
| `--space-1` | `0.25rem` | 4px | 0.25 | Minimum boşluk |
| `--space-2` | `0.5rem` | 8px | 0.5 | Küçük boşluk |
| `--space-3` | `0.75rem` | 12px | 0.75 | Orta boşluk |
| `--space-4` | `1rem` | 16px | 1 | Standart boşluk |
| `--space-5` | `1.5rem` | 24px | 1.5 | Büyük boşluk |
| `--space-6` | `2rem` | 32px | 2 | En büyük boşluk |

---

## 5. Border Radius Tokens

| Token | Değer | px | Kullanım |
|-------|-------|-----|----------|
| `--radius-sm` | `4px` | 4px | Buton, input |
| `--radius-md` | `8px` | 8px | Kart, panel |
| `--radius-lg` | `12px` | 12px | Modal, dropdown |
| `--radius-xl` | `16px` | 16px | Büyük kart |
| `--radius-pill` | `9999px` | — | Pill shape |
| `--radius-full` | `50%` | — | Daire |

---

## 6. Transition Tokens

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--transition-fast` | `150ms ease` | Hover, focus |
| `--transition-normal` | `250ms ease` | Normal geçiş |
| `--transition-slow` | `350ms ease` | Animasyonlu geçiş |
| `--transition-spring` | `300ms cubic-bezier(0.34,1.56,0.64,1)` | Bounce animasyon |

---

## 7. Layout Tokens

| Token | Değer | px | Kullanım |
|-------|-------|-----|----------|
| `--header-h` | `60px` | 60px | Header yüksekliği |
| `--footer-h` | `90px` | 90px | Footer/player yüksekliği |
| `--sidebar-w` | `280px` | 280px | Sidebar genişliği |
| `--content-max-w` | `1200px` | 1200px | Maksimum içerik genişliği |
| `--touch-target-min` | `48px` | 48px | Minimum touch target |
| `--touch-target-comfy` | `56px` | 56px | Rahat touch target |
| `--player-h` | `80px` | 80px | Mini player yüksekliği |
| `--modal-max-w` | `480px` | 480px | Modal maks genişlik |

---

## 8. Glass Effect Tokens

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--glass-blur` | `20px` | Glass arka plan bulanıklığı |
| `--glass-saturate` | `1.8` | Glass doygunluk |
| `--glass-backdrop` | `blur(var(--glass-blur)) saturate(var(--glass-saturate))` | Tam backdrop-filter |

---

## 9. Z-Index Tokens

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--z-base` | `0` | Varsayılan |
| `--z-dropdown` | `100` | Dropdown menü |
| `--z-sticky` | `200` | Sticky header |
| `--z-modal` | `300` | Modal overlay |
| `--z-toast` | `400` | Toast notification |
| `--z-tooltip` | `500` | Tooltip |
| `--z-max` | `9999` | En üst katman |

---

## 10. Platform-Specific Overrides

### 10.1 1024px (RPi5 / Home Panel)

| Token | Değer | Not |
|-------|-------|-----|
| `--header-h` | `60px` | Standart |
| `--footer-h` | `90px` | Player bar |
| `--touch-target-min` | `48px` | RPi5 dokunmatik |
| `--sidebar-w` | `240px` | Daha dar |
| `--text-base` | `16px` | Okunabilirlik |

### 10.2 1920px (Desktop / Pro Panel)

| Token | Değer | Not |
|-------|-------|-----|
| `--header-h` | `64px` | Biraz yüksek |
| `--footer-h` | `80px` | Daha kompakt |
| `--touch-target-min` | `44px` | WCAG standart |
| `--sidebar-w` | `280px` | Geniş |
| `--content-max-w` | `1400px` | Daha geniş |

### 10.3 3840px (4K / Studio Panel)

| Token | Değer | Not |
|-------|-------|-----|
| `--header-h` | `72px` | Büyük ekran |
| `--footer-h` | `96px` | Büyük player |
| `--touch-target-min` | `56px` | Uzaktan kumanda |
| `--sidebar-w` | `320px` | Geniş |
| `--content-max-w` | `1800px` | 4K için |

---

## 11. Quick Reference

| Token Grubu | Doğrulama |
|-------------|-----------|
| Renk | Glass efekt şeffaflık ≤ %16 |
| Tipografi | Minimum 14px (touch), 12px (desktop) |
| Boşluk | 4px grid multiplier |
| Touch | Min 48px (RPi5), 44px (desktop) |
| Glass | blur ≥ 16px, saturate ≥ 1.5 |
| Z-index | Modal > dropdown > sticky |

---

## 12. Cross References

| Kaynak | Hedef |
|--------|-------|
| Bu dosya | [[02-implementation-plan]] — CSS uygulama adımları |
| Bu dosya | [[03-accessibility-gaps]] — Touch target kontrolleri |
| Bu dosya | [[00-mockup-index]] — PNG referansları |
| Bu dosya | [[ADR-044-dynamic-user-theme-engine]] — Tema engine |

---

## 13. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Total Tokens | 85+ |
| Categories | 10 |
| Platform Overrides | 3 |
| Status | Red Team · Human Mode · Truth Mode verified |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-11
**Mode:** Red Team · Human Mode · Truth Mode
