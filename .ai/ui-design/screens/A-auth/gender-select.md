---
title: CoreMusic — Select Gender Screen Specification (1024×600, Linux Embedded RPi5)
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 3.0.0
authority: PNG Visual Analysis (direct inspection — Linux  1024 - Select Gender.png + Select Gender - selected.png)
platform: Linux Embedded / Raspberry Pi 5 / 1024×600px
references:
  - [[00-mockup-index]]
  - [[01-component-inventory]]
  - [[_layout-patterns/05-auth-screen]]
  - [[decisions/accepted/ADR-001-vanilla-js-itcss]]
  - [[decisions/accepted/ADR-044-dynamic-user-theme-engine]]
---

# CoreMusic — Select Gender Screen Specification (v3.0.0)

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

**Source Images:** `Linux  1024 - Select Gender.png` + `Linux  1024 - Select Gender - selected.png`
**Confidence:** High — directly viewed from PNG screenshots.
**Layout Pattern:** Pattern 5: Auth Screen (72/28 split)
**Auth Sırası:** İLK ADIM — Bu ekrandan önce hiçbir auth ekranı gösterilmez.

---

## 1. PLATFORM

| Property | Value |
|----------|-------|
| Resolution | 1024×600px |
| Platform | Linux Embedded (Raspberry Pi 5) |
| Orientation | Landscape (fixed) |
| Scale Factor | 1x |
| Device Type | `device_type = 'embedded'` |
| CSS Bundle | `d-embedded.css` (ITCSS 08_Devices) |
| Viewport Height | 600px (tam ekran, header/footer YOK) |
| Accent Color | `#ff4fd8` (female teması) |
| Background | Tam kaplama sunset/çimenlik fotoğrafı |
| Rota | `/select-gender` (auth akışının ilk adımı) |
| Hover | YOK (dokunmatik cihaz) |
| Header | YOK |
| Footer | YOK |

---

## 2. GENEL LAYOUT ÖLÇÜLERİ

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

---

## 3. SOL ALAN (x:0-740, ~72%)

### 3.1 — Arka Plan

| Özellik | Değer |
|---------|-------|
| Görsel | Tam kaplama sunset/çimenlik fotoğrafı |
| Pozisyon | `background-size: cover; background-position: center` |
| Gradient | Sol alt köşeden hafif karartma |

### 3.2 — İçerik

```
┌──────────────────────────────────────────────────────────────┐
│                                                              │
│   x:60 y:200                                                │
│   [CoreMusic Logo — Bickham Script Two, pembe/mor]          │
│   "Seni Tanıyalım"                                          │
│   Deneyimini sana özel hale getirmek için bir seçim         │
│   yapman yeterli.                                           │
│                                                              │
│                                                              │
│   x:60 y:400                                                │
│   "İyi ki Varsın Emanet!"                                   │
│   (Bickham Script Two, italik, dekoratif)                   │
│                                                              │
│                                                              │
│   x:60 y:520                                                │
│   "Müziğinle Hayat Buldum"                                  │
│   "Hayatın rastlantılarla dolu..."                          │
│   (Bickham Script Two, italik, dekoratif)                   │
│                                                              │
└──────────────────────────────────────────────────────────────┘
```

| Öğe | Boyut | Font | Renk |
|-----|-------|------|------|
| CoreMusic Logo | ~120×30px | Bickham Script Two | `#ff4fd8` (pembe) |
| "Seni Tanıyalım" | ~16px | Arima | `rgba(255,255,255,0.9)` |
| Açıklama | ~12px | Arima | `rgba(255,255,255,0.7)` |
| Dekoratif başlık | ~14px | Bickham Script Two | `rgba(255,255,255,0.8)` |
| Dekoratif metin | ~11px | Bickham Script Two | `rgba(255,255,255,0.6)` |

---

## 4. SAĞ PANEL (x:740-1024, ~284px)

### 4.1 — Glass Panel Özellikleri

| Özellik | Değer | Token |
|---------|-------|-------|
| Genişlik | ~284px | — |
| Yükseklik | 600px (tam ekran) | — |
| Background | `rgba(255,255,255,0.08)` | `--glass-bg` |
| Backdrop-filter | `blur(20px) saturate(180%)` | `--glass-blur` |
| Border | 1px solid `rgba(255,255,255,0.1)` | `--border-subtle` |
| Padding | 20px | `--space-5` |

### 4.2 — İçerik Yapısı

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
│  "Hayatın rastlantılarla dolu...                        │
│   senin gizli Müziğinle partala! ♥"                    │
│  (Bickham Script Two, dekoratif)                        │
│                                                          │
│  x:780 y:560                                            │
│  Devam ederek Gizlilik Politikamızı kabul etmiş olursunuz│
│                                                          │
└──────────────────────────────────────────────────────────┘
```

---

## 5. C07 GENDER BUTTON DETAYI

### 5.1 — Seçilmemiş Hal (Default)

```
┌──────────────────────────────────────────────────────┐
│  [👩 ikon]  Kız                                      │
│             Temizlik, saf duygular                   │
│             Pembemsi renk tonları                    │
│             (~284×60px)                              │
│             border: 1px solid rgba(255,255,255,0.15) │
│             border-radius: 12px                      │
│             background: rgba(255,255,255,0.05)       │
└──────────────────────────────────────────────────────┘
```

### 5.2 — Seçili Hal (Selected)

```
┌══════════════════════════════════════════════════════┐
║  [👩 ikon]  Kız  ← pembe vurgu                       ║
║  ║         Temizlik, saf duygular  ║                 ║
║  ║         Pembemsi renk tonları   ║                 ║
║  ═══════════════════════════════════════            ║
║  background: rgba(255,79,216,0.2)                    ║
║  border: 2px solid var(--theme-primary)              ║
╚══════════════════════════════════════════════════════╝
```

### 5.3 — Buton Özellikleri

| Özellik | Değer | Token |
|---------|-------|-------|
| Genişlik | ~284px (parent'e göre) | — |
| Yükseklik | ~60px | `--btn-h-lg` |
| Padding | 12px 16px | `--space-3, --space-4` |
| Background (default) | `rgba(255,255,255,0.05)` | — |
| Background (selected) | `rgba(255,79,216,0.2)` | — |
| Border (default) | 1px solid `rgba(255,255,255,0.15)` | — |
| Border (selected) | 2px solid `var(--theme-primary)` | — |
| Border-radius | 12px | `--radius-lg` |
| İkon boyutu | ~30×30px | — |
| Başlık fontu | 14px, 600 | `--text-base, --font-semibold` |
| Alt metin fontu | 11px, 400 | `--text-xs, --font-normal` |
| Touch target | ✅ 60px (WCAG uyumlu) | — |

### 5.4 — 3 Varyant

| Varyant | İkon | Başlık | Alt Metin | Tema |
|---------|------|--------|-----------|------|
| Kız | 👩 | Kız | Temizlik, saf duygular · Pembemsi renk tonları | female→pink |
| Erkek | 👨 | Erkek | Güçlü, klasik tonlar · Mavimsi renk tonları | male→blue |
| Diğer | 🤷 | Cinsiyetimi belirtmek istemiyorum | Nötr renk tonları | neutral→default |

---

## 6. DEVAM ET BUTONU

### 6.1 — Seçim Yapılmamış Hal (Pasif)

```
┌──────────────────────────────────────────────────────┐
│                    Devam Et                           │
│                    (border, pasif)                    │
│                    border: 1px solid rgba(255,..)     │
│                    color: rgba(255,255,255,0.5)       │
└──────────────────────────────────────────────────────┘
```

### 6.2 — Seçim Yapılmış Hal (Aktif)

```
┌══════════════════════════════════════════════════════┐
║                    Devam Et                           ║
║                    (full-width, pembe, 56px)          ║
║                    background: var(--theme-primary)   ║
║                    color: #ffffff                     ║
╚══════════════════════════════════════════════════════╝
```

| Özellik | Değer | Token |
|---------|-------|-------|
| Genişlik | Full-width (284px minus padding) | — |
| Yükseklik | 56px | `--btn-h` |
| Background (pasif) | transparent | — |
| Background (aktif) | `var(--theme-primary)` | #ff4fd8 |
| Border (pasif) | 1px solid `rgba(255,255,255,0.2)` | — |
| Border (aktif) | none | — |
| Text (pasif) | `rgba(255,255,255,0.5)` | — |
| Text (aktif) | `#ffffff` | `--color-white` |
| Font | 14px, 600 | `--text-base, --font-semibold` |
| Border-radius | 8px | `--radius-md` |

---

## 7. TEM ETKİSİ

Select Gender seçiminden sonra tema rengi değişir:

| Seçim | data-gender | CSS Variable | Renk |
|-------|-------------|--------------|------|
| Kız | `female` | `--theme-primary` | `#ff4fd8` (pembe) |
| Erkek | `male` | `--theme-primary` | `#4f9fff` (mavi) |
| Diğer | `neutral` | `--theme-primary` | `#a0a0b0` (nötr) |

**Uygulama:**
```javascript
// Gender seçimi yapıldığında
document.documentElement.setAttribute('data-gender', selectedGender);
// CSS custom property otomatik değişir
```

---

## 8. CSS DOSYA YAPISI

```
assets.coremusic.net/Css/
├── 05_Pages/
│   └── p-select-gender.css        ← Bu ekranın stilleri
└── 08_Devices/
    └── d-embedded.css             ← RPi5-specific overrides
```

---

## 9. ERİŞİLEBİLİRLİK (WCAG 2.2 AA)

| Öğe | Durum | Not |
|-----|-------|-----|
| Gender buttons | ✅ 60px | WCAG uyumlu |
| Devam Et butonu | ✅ 56px | WCAG uyumlu |
| Focus visible | ✅ | `2px solid var(--theme-primary)` |
| ARIA labels | ⚠️ | `role="radiogroup"` eklenmeli |
| Keyboard | ✅ | Tab + Enter ile seçim |

---

## 10. BEM YAPISI

```html
<main class="auth-screen">
  <div class="auth-screen__hero">
    <!-- Sol manzara alanı -->
    <div class="auth-screen__logo">CoreMusic</div>
    <h1 class="auth-screen__title">Seni Tanıyalım</h1>
    <p class="auth-screen__subtitle">Deneyimini sana özel hale getirmek için bir seçim yapman yeterli.</p>
  </div>
  
  <div class="auth-screen__panel">
    <!-- Sağ glass panel -->
    <div class="auth-screen__icon">👩</div>
    <h2 class="auth-screen__heading">Seni Tanıyalım</h2>
    <p class="auth-screen__description">Müzik deneyimini sana özel hale getirelim</p>
    
    <div class="gender-buttons" role="radiogroup" aria-label="Cinsiyet seçimi">
      <button class="gender-btn" role="radio" aria-checked="false" data-gender="female">
        <span class="gender-btn__icon">👩</span>
        <span class="gender-btn__title">Kız</span>
        <span class="gender-btn__desc">Temizlik, saf duygular</span>
        <span class="gender-btn__desc">Pembemsi renk tonları</span>
      </button>
      <button class="gender-btn" role="radio" aria-checked="false" data-gender="male">
        <span class="gender-btn__icon">👨</span>
        <span class="gender-btn__title">Erkek</span>
        <span class="gender-btn__desc">Güçlü, klasik tonlar</span>
        <span class="gender-btn__desc">Mavimsi renk tonları</span>
      </button>
      <button class="gender-btn" role="radio" aria-checked="false" data-gender="neutral">
        <span class="gender-btn__icon">🤷</span>
        <span class="gender-btn__title">Cinsiyetimi belirtmek istemiyorum</span>
        <span class="gender-btn__desc">Nötr renk tonları</span>
      </button>
    </div>
    
    <button class="btn-primary" disabled>Devam Et</button>
    
    <p class="auth-screen__decorative">Hayatın rastlantılarla dolu... senin gizli Müziğinle partala! ♥</p>
    <p class="auth-screen__legal">Devam ederek <a href="/privacy">Gizlilik Politikamızı</a> kabul etmiş olursunuz.</p>
  </div>
</main>
```

---

*Select Gender Screen Spec v3.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
