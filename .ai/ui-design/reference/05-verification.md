---
title: "Verification Checklist"
type: reference
category: verification
updated: 2026-08-11
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Verification Checklist

**Zorunlu Baglantilar:** [[01-design-tokens]] · [[02-implementation-plan]] · [[03-accessibility-gaps]] · [[00-mockup-index]]

---

## 1. Amaç

Frontend kodu mockup'lar ile eşleşiyor mu diye doğrulamak için kullanılan kontrol listesidir. Ajanlar bu dosyadaki checklist'i kod yazma sonrasında uygular.

---

## 2. Layout Doğrulama

| # | Kontrol | 1024px | 1920px | 3840px | Durum |
|---|---------|--------|--------|--------|-------|
| 1 | Header yüksekliği platform ile eşleşiyor | 60px | 64px | 72px | ☐ |
| 2 | Footer/player yüksekliği platform ile eşleşiyor | 90px | 80px | 96px | ☐ |
| 3 | Sidebar genişliği doğru (Göz At sayfasında) | 240px | 280px | 320px | ☐ |
| 4 | İçerik alanı maksimum genişliği aşılmıyor | 1200px | 1400px | 1800px | ☐ |
| 5 | Touch target minimum boyutu sağlanıyor | ≥48px | ≥44px | ≥56px | ☐ |

---

## 3. Touch Target Doğrulama

| # | Kontrol | Minimum | Hedef | Durum |
|---|---------|---------|-------|-------|
| 6 | Tüm butonlar ≥48px (RPi5) veya ≥44px (desktop) | 44-48px | 56px | ☐ |
| 7 | Checkbox/radio butonları ≥48px | 48px | 56px | ☐ |
| 8 | Link'ler tıklanabilir alana sahip | 44px | 48px | ☐ |
| 9 | Close butonları ≥48px | 48px | 56px | ☐ |
| 10 | Icon butonları ≥48px (padding dahil) | 48px | 56px | ☐ |

---

## 4. Glass Effect Doğrulama

| # | Kontrol | Değer | Durum |
|---|---------|-------|-------|
| 11 | Glass blur değeri ≥16px | 20px | ☐ |
| 12 | Glass saturate değeri ≥1.5 | 1.8 | ☐ |
| 13 | Glass arka plan opacity ≤ %16 | %8 | ☐ |
| 14 | Glass border opacity ≤ %20 | %12 | ☐ |
| 15 | Glass shadow kullanılıyor | `0 8px 32px rgba(0,0,0,0.37)` | ☐ |

---

## 5. Auth Flow Doğrulama

| # | Kontrol | Beklenen Sıra | Durum |
|---|---------|---------------|-------|
| 16 | Auth flow: Select Gender → Login → Register | Sıralı | ☐ |
| 17 | Gender select ekranı ilk sırada | 1. ekran | ☐ |
| 18 | Login ekranı gender'dan sonra | 2. ekran | ☐ |
| 19 | Register ekranı login'den sonra | 3. ekran | ☐ |
| 20 | Back butonu doğru ekranlara gidiyor | Doğru yönlendirme | ☐ |

---

## 6. Navigation Doğrulama

| # | Kontrol | Durum |
|---|---------|-------|
| 21 | Sidebar sadece Göz At sayfasında görünüyor | ☐ |
| 22 | Header tüm sayfalarda görünüyor | ☐ |
| 23 | Footer/player tüm sayfalarda görünüyor | ☐ |
| 24 | Aktif sayfa doğru highlight ediliyor | ☐ |
| 25 | SPA routing doğru çalışıyor | ☐ |

---

## 7. Responsive Doğrulama

| # | Kontrol | Durum |
|---|---------|-------|
| 26 | 1024px'de responsive layout doğru | ☐ |
| 27 | 1920px'de responsive layout doğru | ☐ |
| 28 | 3840px'de responsive layout doğru | ☐ |
| 29 | Touch device'da hover state yok | ☐ |
| 30 | Scroll behavior doğru | ☐ |

---

## 8. Theme Token Doğrulama

| # | Kontrol | Durum |
|---|---------|-------|
| 31 | Hiçbir hardcoded renk yok | ☐ |
| 32 | Tüm renkler CSS custom property kullanıyor | ☐ |
| 33 | `--theme-primary` doğru kullanılıyor | ☐ |
| 34 | `--text-primary/secondary/tertiary` doğru kullanılıyor | ☐ |
| 35 | Glass token'ları doğru kullanılıyor | ☐ |

---

## 9. BEM & ITCSS Doğrulama

| # | Kontrol | Durum |
|---|---------|-------|
| 36 | BEM naming convention takip ediliyor | ☐ |
| 37 | `block__element--modifier` formatı kullanılıyor | ☐ |
| 38 | ITCSS layer order korunuyor | ☐ |
| 39 | `globals/` → `tools/` → `generic/` → `elements/` → `objects/` → `components/` → `utilities/` | ☐ |
| 40 | Component-specific stiller `components/` içinde | ☐ |

---

## 10. Accessibility Doğrulama

| # | Kontrol | Durum |
|---|---------|-------|
| 41 | Tüm img'lerde `alt` attribute'u var | ☐ |
| 42 | Form input'larda `label` var | ☐ |
| 43 | Keyboard navigation çalışıyor | ☐ |
| 44 | Focus state görünür | ☐ |
| 45 | Color contrast ≥4.5:1 (normal), ≥3:1 (large) | ☐ |

---

## 11. Performance Doğrulama

| # | Kontrol | Durum |
|---|---------|-------|
| 46 | Lazy loading uygulanmış | ☐ |
| 47 | Image formatları optimize (WebP/AVIF) | ☐ |
| 48 | CSS/JS minify edilmiş | ☐ |
| 49 | Font loading optimize | ☐ |
| 50 | CLS < 0.1 | ☐ |

---

## 12. Security Doğrulama

| # | Kontrol | Durum |
|---|---------|-------|
| 51 | Hiçbir secret/client-side'da yok | ☐ |
| 52 | CSRF token doğru kullanılıyor | ☐ |
| 53 | XSS koruması uygulanmış | ☐ |
| 54 | CSP nonce doğru kullanılıyor | ☐ |
| 55 | Input sanitization uygulanmış | ☐ |

---

## 13. Dosya Yapısı Doğrulama

| # | Kontrol | Durum |
|---|---------|-------|
| 56 | CSS dosyaları ITCSS layer order'ında | ☐ |
| 57 | JS dosyaları `assets.coremusic.net/js/` içinde | ☐ |
| 58 | Image dosyaları tema dizininde | ☐ |
| 59 | Font dosyaları `assets.coremusic.net/fonts/` içinde | ☐ |
| 60 | Hiçbir dosya `node_modules/` içinden import etmiyor | ☐ |

---

## 14. Quick Reference

| Kontrol Grubu | Dosya |
|---------------|-------|
| Layout | [[01-design-tokens]] §7 |
| Touch | [[03-accessibility-gaps]] |
| Glass | [[01-design-tokens]] §8 |
| Auth | [[00-mockup-index]] §A |
| Theme | [[ADR-044-dynamic-user-theme-engine]] |
| BEM | [[01-component-inventory]] |
| ITCSS | [[02-implementation-plan]] |

---

## 15. Cross References

| Kaynak | Hedef |
|--------|-------|
| Bu dosya | [[01-design-tokens]] — Token değerleri |
| Bu dosya | [[02-implementation-plan]] — Uygulama adımları |
| Bu dosya | [[03-accessibility-gaps]] — Erişilebilirlik |
| Bu dosya | [[00-mockup-index]] — PNG referansları |
| Bu dosya | [[ADR-044-dynamic-user-theme-engine]] — Tema engine |

---

## 16. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Total Checks | 60 |
| Categories | 12 |
| Status | Red Team · Human Mode · Truth Mode verified |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-11
**Mode:** Red Team · Human Mode · Truth Mode
