---
title: "CoreMusic — Platform Tokens (4 Platform)"
type: reference
category: design-system
date: 2026-08-17
updated: 2026-08-17
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
platforms: [rpi5-1024, desktop-1920, mobile-375, tv-3840]
reference:
  authority: ".ai/ui-design/tokens/platform-tokens.md"
  related:
    - ".ai/ui-design/tokens/design-tokens-master.md"
    - ".ai/decisions/accepted/ADR-001-vanilla-js-itcss.md"
    - ".ai/decisions/accepted/ADR-044-dynamic-user-theme-engine.md"
---

# CoreMusic — Platform Tokens (4 Platform)

**Her platform için farklı token değerleri.** CSS media queries ile otomatik değişir.

> **⚠️ Platform tespiti:** `device_type` attribute'u ile yapılır. `device_type="embedded"` → RPi5, `device_type="desktop"` → Masaüstü, vb.

---

## 1. Platform Karşılaştırma Matrisi

### 1.1 — Genel Platform Bilgisi

| Özellik | RPi5 (1024×600) | Desktop (1920×1080) | Mobile (375×812) | TV (3840×2160) |
|---------|-----------------|---------------------|------------------|----------------|
| **Çözünürlük** | 1024×600 | 1920×1080 | 375×812 | 3840×2160 |
| **Cihaz** | Raspberry Pi 5 | PC/Laptop | iPhone/Android | 4K TV |
| **OS** | Linux Embedded | Windows/Linux/macOS | iOS/Android | Tizen/webOS |
| **Girdi** | Dokunmatik | Fare + Klavye | Dokunmatik | Uzaktan Kumanda |
| **Hover** | ❌ Yok | ✅ Var | ❌ Yok | ❌ Yok (focus) |
| **Piksel Ratiosu** | 1x | 1x-2x | 2x-3x | 1x |
| **CSS Bundle** | `d-embedded.css` | `d-desktop.css` | `d-mobile.css` | `d-tv.css` |
| **Orientation** | Landscape (sabit) | Landscape (esnek) | Portrait/Landscape | Landscape (sabit) |
| **Min Touch Target** | 48px | 44px | 48px | 60px |
| **Grid Sütun Max** | 3 | 4 | 2 | 5 |
| **Sidebar** | Sadece Göz At | Var (geniş) | ❌ (drawer/modal) | Sadece Göz At |

### 1.2 — Temel Boyutlar

| Token | RPi5 | Desktop | Mobile | TV | CSS Variable |
|-------|------|---------|--------|-----|--------------|
| `--screen-w` | `1024px` | `1920px` | `375px` | `3840px` | `var(--screen-w)` |
| `--screen-h` | `600px` | `1080px` | `812px` | `2160px` | `var(--screen-h)` |
| `--header-h` | `60px` | `70px` | `56px` | `90px` | `var(--header-h)` |
| `--footer-h` | `90px` | `104px` | `72px` | `138px` | `var(--footer-h)` |
| `--content-h` | `450px` | `906px` | `684px` | `1932px` | `var(--content-h)` |
| `--content-padding-top` | `11px` | `14px` | `8px` | `18px` | `var(--content-padding-top)` |
| `--content-padding-bottom` | `15px` | `20px` | `12px` | `24px` | `var(--content-padding-bottom)` |
| `--page-padding-x` | `16px` | `24px` | `16px` | `32px` | `var(--page-padding-x)` |

---

## 2. RPi5 Embedded (1024×600)

**PNG Kaynağı:** Mevcut 18 PNG'nin tamamı bu platform için tasarlanmıştır.
**CSS Bundle:** `d-embedded.css`

### 2.1 — Boyut Token'ları

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--header-h` | `60px` | Header yüksekliği |
| `--footer-h` | `90px` | Footer/player yüksekliği |
| `--content-h` | `450px` | İçerik bölgesi |
| `--sidebar-w` | `167px` | Sol sidebar (sadece Göz At) |
| `--detail-panel-w` | `366px` | Sağ detay paneli |
| `--card-thumb-size` | `140px` | Kart thumbnail boyutu |
| `--card-thumb-size-lg` | `180px` | Büyük kart thumbnail |
| `--artist-thumb-size` | `120px` | Sanatçı thumbnail |
| `--album-art-size` | `300px` | Albüm sanatı (detail panel) |
| `--player-art-size` | `120px` | Footer player albüm sanatı |
| `--mini-card-art-size` | `50px` | Mini kart thumbnail |
| `--widget-h` | `100px` | Widget yüksekliği |
| `--genre-tab-h` | `32px` | Genre tab yüksekliği |

### 2.2 — Font Ölçekleri

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--text-xs` | `10px` | Nav link, badge |
| `--text-sm` | `11px` | Form label, caption |
| `--text-base` | `12px` | Gövde metni |
| `--text-lg` | `14px` | Kart başlığı |
| `--text-xl` | `16px` | Sayfa başlığı |
| `--text-2xl` | `20px` | Büyük başlık |
| `--text-3xl` | `24px` | Ekran başlığı |

### 2.3 — Spacing Token'ları

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--grid-gap` | `8px` | Grid boşluğu |
| `--card-gap` | `8px` | Kart arası |
| `--section-gap` | `16px` | Bölüm arası |
| `--page-padding` | `16px` | Sayfa içi padding |
| `--card-padding` | `12px` | Kart içi padding |

### 2.4 — Touch & Etkileşim

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--touch-min` | `48px` | Minimum touch target |
| `--hover-display` | `none` | Hover efekti yok |
| `--cursor` | `pointer` | Parmağa uygun cursor |
| `--glass-blur` | `blur(20px)` | Glass bulanıklık |
| `--glass-saturate` | `saturate(180%)` | Glass doygunluk |

---

## 3. Desktop (1920×1080)

**PNG Kaynağı:** Henüz oluşturulmamıştır.
**CSS Bundle:** `d-desktop.css`

### 3.1 — Boyut Token'ları

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--header-h` | `70px` | Header yüksekliği |
| `--footer-h` | `104px` | Footer/player yüksekliği |
| `--content-h` | `906px` | İçerik bölgesi |
| `--sidebar-w` | `280px` | Sol sidebar (geniş) |
| `--detail-panel-w` | `480px` | Sağ detay paneli |
| `--card-thumb-size` | `180px` | Kart thumbnail boyutu |
| `--card-thumb-size-lg` | `240px` | Büyük kart thumbnail |
| `--artist-thumb-size` | `160px` | Sanatçı thumbnail |
| `--album-art-size` | `400px` | Albüm sanatı (detail panel) |
| `--player-art-size` | `140px` | Footer player albüm sanatı |
| `--mini-card-art-size` | `60px` | Mini kart thumbnail |
| `--widget-h` | `140px` | Widget yüksekliği |
| `--genre-tab-h` | `40px` | Genre tab yüksekliği |

### 3.2 — Font Ölçekleri (1.2×)

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--text-xs` | `12px` | Nav link, badge |
| `--text-sm` | `13px` | Form label, caption |
| `--text-base` | `14px` | Gövde metni |
| `--text-lg` | `16px` | Kart başlığı |
| `--text-xl` | `18px` | Sayfa başlığı |
| `--text-2xl` | `24px` | Büyük başlık |
| `--text-3xl` | `30px` | Ekran başlığı |

### 3.3 — Spacing Token'ları

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--grid-gap` | `12px` | Grid boşluğu |
| `--card-gap` | `12px` | Kart arası |
| `--section-gap` | `24px` | Bölüm arası |
| `--page-padding` | `24px` | Sayfa içi padding |
| `--card-padding` | `16px` | Kart içi padding |

### 3.4 — Touch & Etkileşim

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--touch-min` | `44px` | Minimum touch target (fare) |
| `--hover-display` | `block` | Hover efekti var |
| `--cursor` | `default` | Varsayılan cursor |
| `--glass-blur` | `blur(20px)` | Glass bulanıklık |
| `--glass-saturate` | `saturate(180%)` | Glass doygunluk |

### 3.5 — Desktop Özel Özellikler

| Özellik | Değer | Açıklama |
|---------|-------|----------|
| Hover efektleri | Aktif | `:hover` pseudo-class |
| Scrollbar | Özelleştirilmiş | İnce, transparan |
| Tooltip | Var | Hover'da bilgi |
| Context menu | Var | Sağ tık menüsü |
| Keyboard nav | Var | Tab, Arrow keys |
| Drag & drop | Var | Dosya sürükleme |
| Multi-column | 4 sütun max | Geniş grid |

---

## 4. Mobile (375×812)

**PNG Kaynağı:** Henüz oluşturulmamıştır.
**CSS Bundle:** `d-mobile.css`

### 4.1 — Boyut Token'ları

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--header-h` | `56px` | Header yüksekliği |
| `--footer-h` | `72px` | Tab bar yüksekliği |
| `--content-h` | `684px` | İçerik bölgesi |
| `--sidebar-w` | `YOK` | Sidebar yok (drawer) |
| `--detail-panel-w` | `100%` | Tam genişlik (sheet) |
| `--card-thumb-size` | `120px` | Kart thumbnail boyutu |
| `--card-thumb-size-lg` | `160px` | Büyük kart thumbnail |
| `--artist-thumb-size` | `100px` | Sanatçı thumbnail |
| `--album-art-size` | `280px` | Albüm sanatı (full-width) |
| `--player-art-size` | `100px` | Mini player albüm sanatı |
| `--mini-card-art-size` | `48px` | Mini kart thumbnail |
| `--widget-h` | `80px` | Widget yüksekliği |
| `--genre-tab-h` | `36px` | Genre tab yüksekliği |

### 4.2 — Font Ölçekleri (1×)

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--text-xs` | `10px` | Nav link, badge |
| `--text-sm` | `11px` | Form label, caption |
| `--text-base` | `12px` | Gövde metni |
| `--text-lg` | `14px` | Kart başlığı |
| `--text-xl` | `16px` | Sayfa başlığı |
| `--text-2xl` | `20px` | Büyük başlık |
| `--text-3xl` | `24px` | Ekran başlığı |

### 4.3 — Spacing Token'ları

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--grid-gap` | `8px` | Grid boşluğu |
| `--card-gap` | `8px` | Kart arası |
| `--section-gap` | `16px` | Bölüm arası |
| `--page-padding` | `16px` | Sayfa içi padding |
| `--card-padding` | `12px` | Kart içi padding |

### 4.4 — Touch & Etkileşim

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--touch-min` | `48px` | Minimum touch target |
| `--hover-display` | `none` | Hover efekti yok |
| `--cursor` | `pointer` | Parmağa uygun cursor |
| `--glass-blur` | `none` | Glass bulanıklık yok (performans) |
| `--glass-saturate` | `none` | Glass doygunluk yok |

### 4.5 — Mobile Özel Özellikler

| Özellik | Değer | Açıklama |
|---------|-------|----------|
| Tab bar | Alt kısımda | 5 tab max |
| Swipe gestures | Var | Yatay kaydırma |
| Pull to refresh | Var | Aşağı çekerek yenile |
| Bottom sheet | Var | Detay paneli için |
| Haptic feedback | Var | Dokunma geri bildirimi |
| Safe area | Var | Çentik/çubuk alanı |
| Orientation | Portrait/Landscape | Her ikisi de |
| Virtual keyboard | Var | Input odaklandığında |
| Infinite scroll | Var | Liste kaydırma |
| Pinch to zoom | Var | Görseller için |

---

## 5. TV (3840×2160)

**PNG Kaynağı:** Henüz oluşturulmamıştır.
**CSS Bundle:** `d-tv.css`

### 5.1 — Boyut Token'ları

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--header-h` | `90px` | Header yüksekliği |
| `--footer-h` | `138px` | Footer/player yüksekliği |
| `--content-h` | `1932px` | İçerik bölgesi |
| `--sidebar-w` | `320px` | Sol sidebar |
| `--detail-panel-w` | `640px` | Sağ detay paneli |
| `--card-thumb-size` | `280px` | Kart thumbnail boyutu |
| `--card-thumb-size-lg` | `360px` | Büyük kart thumbnail |
| `--artist-thumb-size` | `240px` | Sanatçı thumbnail |
| `--album-art-size` | `600px` | Albüm sanatı (detail panel) |
| `--player-art-size` | `180px` | Footer player albüm sanatı |
| `--mini-card-art-size` | `80px` | Mini kart thumbnail |
| `--widget-h` | `200px` | Widget yüksekliği |
| `--genre-tab-h` | `56px` | Genre tab yüksekliği |

### 5.2 — Font Ölçekleri (1.6×)

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--text-xs` | `16px` | Nav link, badge |
| `--text-sm` | `18px` | Form label, caption |
| `--text-base` | `20px` | Gövde metni |
| `--text-lg` | `24px` | Kart başlığı |
| `--text-xl` | `28px` | Sayfa başlığı |
| `--text-2xl` | `36px` | Büyük başlık |
| `--text-3xl` | `48px` | Ekran başlığı |

### 5.3 — Spacing Token'ları

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--grid-gap` | `16px` | Grid boşluğu |
| `--card-gap` | `16px` | Kart arası |
| `--section-gap` | `32px` | Bölüm arası |
| `--page-padding` | `32px` | Sayfa içi padding |
| `--card-padding` | `20px` | Kart içi padding |

### 5.4 — Touch & Etkileşim

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--touch-min` | `60px` | Minimum touch target (uzaktan) |
| `--hover-display` | `none` | Hover efekti yok |
| `--cursor` | `default` | Varsayılan cursor |
| `--glass-blur` | `blur(4px)` | Hafif bulanıklık (performans) |
| `--glass-saturate` | `none` | Glass doygunluk yok |

### 5.5 — TV Özel Özellikler

| Özellik | Değer | Açıklama |
|---------|-------|----------|
| Focus management | D-Pad | Ok tuşları ile navigasyon |
| Focus ring | Büyük, belirgin | 4px accent renk |
| Focus trap | Var | Modal içinde odak tuzağı |
| Remote control | Uzaktan kumanda | D-Pad, Enter, Back |
| Overscan safe | Var | TV kenar boşlukları |
| 4K scaling | 2× | Piksel netliği |
| Large text | Var | Okunabilirlik |
| High contrast | Var | Görüş engelliler için |
| Animation reduce | Var | `prefers-reduced-motion` |

---

## 6. CSS Media Query Örnekleri

```css
/* === VARSAYILAN: RPi5 (1024×600) === */
:root {
  --header-h: 60px;
  --footer-h: 90px;
  --content-h: 450px;
  --sidebar-w: 167px;
  --card-thumb-size: 140px;
  --touch-min: 48px;
  --text-base: 12px;
}

/* === DESKTOP (1920×1080) === */
@media (min-width: 1024px) {
  :root {
    --header-h: 70px;
    --footer-h: 104px;
    --content-h: 906px;
    --sidebar-w: 280px;
    --card-thumb-size: 180px;
    --touch-min: 44px;
    --text-base: 14px;
  }
}

/* === MOBILE (375×812) === */
@media (max-width: 767px) {
  :root {
    --header-h: 56px;
    --footer-h: 72px;
    --content-h: 684px;
    --sidebar-w: 0;
    --card-thumb-size: 120px;
    --touch-min: 48px;
    --text-base: 12px;
  }
}

/* === TV (3840×2160) === */
@media (min-width: 1920px) {
  :root {
    --header-h: 90px;
    --footer-h: 138px;
    --content-h: 1932px;
    --sidebar-w: 320px;
    --card-thumb-size: 280px;
    --touch-min: 60px;
    --text-base: 20px;
  }
}

/* === 4K TV (3840×2160) === */
@media (min-width: 3840px) {
  :root {
    --header-h: 90px;
    --footer-h: 138px;
    --content-h: 1932px;
    --sidebar-w: 320px;
    --card-thumb-size: 280px;
    --touch-min: 60px;
    --text-base: 20px;
  }
}
```

---

## 7. Platform Bazlı Bileşen Farkları

### 7.1 — Kart Bileşeni

| Özellik | RPi5 | Desktop | Mobile | TV |
|---------|------|---------|--------|-----|
| Thumbnail | 140×140 | 180×180 | 120×120 | 280×280 |
| Padding | 12px | 16px | 12px | 20px |
| Gap | 8px | 12px | 8px | 16px |
| Radius | 12px | 12px | 8px | 16px |
| Font size | 12px | 14px | 12px | 20px |
| Hover | ❌ | ✅ | ❌ | ❌ |

### 7.2 — Buton Bileşeni

| Özellik | RPi5 | Desktop | Mobile | TV |
|---------|------|---------|--------|-----|
| Yükseklik | 48px | 48px | 48px | 64px |
| Padding X | 16px | 20px | 16px | 24px |
| Font size | 12px | 14px | 12px | 20px |
| Radius | 8px | 8px | 8px | 12px |
| Hover | ❌ | ✅ | ❌ | ❌ |

### 7.3 — Input Bileşeni

| Özellik | RPi5 | Desktop | Mobile | TV |
|---------|------|---------|--------|-----|
| Yükseklik | 48px | 48px | 48px | 64px |
| Padding X | 12px | 16px | 12px | 20px |
| Font size | 12px | 14px | 12px | 20px |
| Radius | 8px | 8px | 8px | 12px |
| Focus ring | 2px | 2px | 2px | 4px |

### 7.4 — Modal Bileşeni

| Özellik | RPi5 | Desktop | Mobile | TV |
|---------|------|---------|--------|-----|
| Genişlik | 380px | 480px | 100% | 640px |
| Max yükseklik | 80% | 80% | 90% (bottom sheet) | 80% |
| Padding | 24px | 32px | 20px | 40px |
| Radius | 16px | 16px | 16px (üst köşe) | 20px |
| Blur | 20px | 20px | Yok | 4px |
| Overlay | 0.5 opacity | 0.5 opacity | 0.5 opacity | 0.5 opacity |

### 7.5 — Tab Bileşeni

| Özellik | RPi5 | Desktop | Mobile | TV |
|---------|------|---------|--------|-----|
| Yükseklik | 32px | 40px | 36px | 56px |
| Padding X | 12px | 16px | 12px | 20px |
| Font size | 10px | 12px | 10px | 16px |
| Gap | 4px | 6px | 4px | 8px |
| Radius | 20px | 20px | 20px | 28px |

---

## 8. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[ui-design/responsive-device-mode]] | Responsive Device Mode mimarisi kuralı |
| [[architecture/l3-presentation/device-css]] | Device CSS detayları |

---

## 9. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 1.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Total Platforms | 4 |
| Tokens Per Platform | 30+ |
| Component Comparisons | 5 |
| CSS Media Queries | 5 |
| Platform-Specific Features | 20+ |

---

*Platform Tokens v1.0.0 — CoreMusic Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-17*
*Mode: Red Team · Human Mode · Truth Mode*
