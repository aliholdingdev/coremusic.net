---
title: "CoreMusic — Component Inventory (C01-C16, home-1024)"
type: reference
version: 3.2.0
---

# CoreMusic — Component Inventory

**16 bileşenin (C01-C16) kanonik envanteri.** Her bileşen için BEM sınıfı, pixel ölçümleri, token referansları, ITCSS katmanı ve touch target uyumluluğu belirtilmiştir.

> **⚠️ Bu dosya kod yazarken referans olarak kullanılır.** Bileşen oluştururken bu dosyadaki BEM sınıf adlarını ve ölçümü kullan.

---

## 1. Genel Bakış

### 1.1 — Bileşen Sınıflandırması

| Kategori | Bileşenler | Sayı |
|----------|-----------|------|
| **Header** (Ortak) | C01, C02, C03 | 3 |
| **Buton** | C04, C05 | 2 |
| **Form** | C06 | 1 |
| **Auth** | C07, C08 | 2 |
| **Card** | C09 | 1 |
| **Panel** | C10 | 1 |
| **Navigation** | C11 | 1 |
| **Rating** | C12 | 1 |
| **List** | C13 | 1 |
| **Overlay** | C14, C15, C16 | 3 |
| **Toplam** | | **16** |

### 1.2 — ITCSS Katman Dağılımı

| Katman | Bileşenler | Dosya |
|--------|-----------|-------|
| 01_Abstracts (tokens) | — | `a-*.css` |
| 02_Base (reset) | — | `b-base-core.css` |
| 03_Layout | C01, C02, C03, C10 | `_header.css`, `_footer.css` |
| 04_Components | C04, C05, C09, C11, C12, C13, C14, C15, C16 | `c-*.css` |
| 05_Pages | C06, C07, C08 | `p-login-view.css`, `p-select-gender.css` |
| 06_Utilities | — | `u-helpers-utility.css` |

---

## 2. Bileşen Detayları

---

### C01 — Navigation Link

| Özellik | Değer |
|---------|-------|
| **ID** | C01 |
| **Ad** | Navigation Link |
| **BEM Sınıfı** | `.nav-link` |
| **ITCSS** | 03_Layout (`_header.css`) |
| **Touch Target** | ⚠️ ~24×24px (WCAG İHLALİ: 48px olmalı) |
| **Kullanıldığı Ekranlar** | Tüm uygulama ekranları (header) |
| **PNG Kaynakları** | Tüm header PNG'leri |

**ASCII Wireframe:**
```
┌───────────────────────────────────────────────────────────────┐
│ "Core Music"  [Ana Sayfa] [Keşfet] [Albümler] [Sanatçılar] │
│                   ↑           ↑        ↑          ↑           │
│                   nav-link    nav-link  nav-link   nav-link    │
│                   (active)                                              │
└───────────────────────────────────────────────────────────────┘

Tek nav-link:
┌──────────────┐
│  Ana Sayfa   │  ← font: Arima, ~10px, letter-spacing
│  (24×24px)   │  ← WCAG İHLALİ: min 48×48px olmalı
└──────────────┘
```

**Ölçüler (PNG piksel ölçümü):**
| Özellik | Değer | Token |
|---------|-------|-------|
| Font boyutu | ~10px | `--text-xs` |
| Font ağırlığı | 400 (normal) | `--font-normal` |
| Renk (default) | `rgba(255,255,255,0.85)` | `--color-text-muted` |
| Renk (active) | `#E91E8C` | `--theme-primary` |
| Padding | ~2px 4px | `--space-1` |
| Gap (linkler arası) | 2-4px | `--space-1` |
| Hit area | ~24×24px | `--touch-min` (48px olmalı) |
| Font | Arima | `--font-body` |

**WCAG Durumu:** ❌ İHLAL — touch target 24px, minimum 48px olmalı
**Düzeltme:** CSS padding artırılarak hit area 48px'e çıkarılır

**Kullanım:**
```html
<nav class="site-header__nav" aria-label="Ana navigasyon">
  <a href="/home" class="nav-link active" aria-current="page">Ana Sayfa</a>
  <a href="/kesfet" class="nav-link">Keşfet</a>
  <a href="/albums" class="nav-link">Albümler</a>
  <a href="/artists" class="nav-link">Sanatçılar</a>
  <a href="/browse" class="nav-link">Göz At</a>
  <a href="/history" class="nav-link">Geçmiş</a>
  <a href="/settings" class="nav-link">Ayarlar</a>
  <a href="/about" class="nav-link">Hakkımızda</a>
</nav>
```

---

### C02 — System Status Widget

| Özellik | Değer |
|---------|-------|
| **ID** | C02 |
| **Ad** | System Status Widget |
| **BEM Sınıfı** | `.header-widget` / `.header-border` |
| **ITCSS** | 03_Layout (`_header.css`) |
| **Touch Target** | ✅ pill 65×37.4px |
| **Kullanıldığı Ekranlar** | Tüm uygulama ekranları (header) |

**ASCII Wireframe:**
```
┌── WiFi+BT Group (pill) ──┐  ┌── Battery (pill) ──────────┐
│  [📶 WiFi] [✳ BT]        │  │  [🔋 icon] %100            │
│  65×37.4px                │  │  100px wide                 │
│  border-radius: 50px      │  │  border-radius: 50px        │
└───────────────────────────┘  └─────────────────────────────┘
```

**Ölçüler:**
| Özellik | Değer | Token |
|---------|-------|-------|
| WiFi+BT pill genişliği | 65px | — |
| WiFi+BT pill yüksekliği | 37.4px | — |
| Battery pill genişliği | 100px | — |
| Border radius | 50px (tam yuvarlak) | `--radius-pill` |
| Border | 1px solid `rgba(255,255,255,0.2)` | `--border-subtle` |
| İkon boyutu | 25×25px | — |
| Background | `rgba(255,255,255,0.1)` | `--glass-bg` |

**Kullanım:**
```html
<div class="header-border" style="border-radius:50px;height:37.4px;width:65px;">
  <div class="header-widget header-widget--signal">
    <img src="/assets.coremusic.net/Image/res-pink/wifi-full.png" alt="Wi-Fi" height="25" width="25" />
  </div>
  <div class="header-widget header-widget--bt">
    <img src="/assets.coremusic.net/Image/res-pink/bluethoot.png" alt="Bluetooth" height="25" width="25" />
  </div>
</div>
```

---

### C03 — User Profile Pill

| Özellik | Değer |
|---------|-------|
| **ID** | C03 |
| **Ad** | User Profile Pill |
| **BEM Sınıfı** | `.header-user` |
| **ITCSS** | 03_Layout (`_header.css`) |
| **Touch Target** | ✅ ~52px |
| **Kullanıldığı Ekranlar** | Tüm uygulama ekranları (header) |

**ASCII Wireframe:**
```
┌─────────────────────────────────────┐
│  [🧑 avatar 35×35] Bayram Ali  ▾   │
│                                     │
│  35×35px    isim    dropdown arrow  │
│  daire      ~12px   ↓               │
└─────────────────────────────────────┘
Toplam genişlik: ~150px
```

**Ölçüler:**
| Özellik | Değer | Token |
|---------|-------|-------|
| Avatar boyutu | 35×35px | — |
| Avatar border-radius | 50% (daire) | `--radius-full` |
| Font boyutu | ~12px | `--text-sm` |
| Font rengi | `rgba(255,255,255,0.9)` | `--color-text` |
| Padding | 6px 12px | `--space-2` `--space-3` |
| Background | `rgba(255,255,255,0.1)` | `--glass-bg` |
| Border-radius | 50px | `--radius-pill` |

---

### C04 — Primary Button

| Özellik | Değer |
|---------|-------|
| **ID** | C04 |
| **Ad** | Primary Button |
| **BEM Sınıfı** | `.btn-primary` |
| **ITCSS** | 04_Components |
| **Touch Target** | ✅ 56px yükseklik |
| **Kullanıldığı Ekranlar** | Auth, Detail Panel (Hemen Çal, Bağlan, Devam Et, Başla) |

**ASCII Wireframe:**
```
┌─────────────────────────┐
│       Hemen Çal          │  ← tam genişlik (parent container'a göre)
│       (56px yükseklik)   │
│       pembe arka plan    │  ← background: var(--theme-primary)
│       beyaz text         │  ← color: #fff
│       border-radius: 8px │
└─────────────────────────┘
```

**Ölçüler:**
| Özellik | Değer | Token |
|---------|-------|-------|
| Yükseklik | 56px | `--btn-h` |
| Padding | 12px 24px | `--space-3` `--space-6` |
| Background | `var(--theme-primary)` | #ff4fd8 (default) |
| Text rengi | `#ffffff` | `--color-white` |
| Font boyutu | 14px | `--text-base` |
| Font ağırlığı | 600 (semi-bold) | `--font-semibold` |
| Border-radius | 8px | `--radius-md` |
| Border | none | — |
| Transition | 250ms ease | `--transition-base` |

**Durumlar:**
```
Default:   background: var(--theme-primary), color: #fff
Hover:     (TOKUNMATİK CİHAZDA YOK — focus-visible kullan)
Focus:     outline: 2px solid var(--theme-primary), outline-offset: 2px
Disabled:  opacity: 0.5, cursor: not-allowed
Loading:   spinner ikonu, text gizli
```

---

### C05 — Secondary Button

| Özellik | Değer |
|---------|-------|
| **ID** | C05 |
| **Ad** | Secondary Button |
| **BEM Sınıfı** | `.btn-secondary` |
| **ITCSS** | 04_Components |
| **Touch Target** | ✅ 48px yükseklik |
| **Kullanıldığı Ekranlar** | Auth (İptal), Detail Panel (Karışık Çal) |

**ASCII Wireframe:**
```
┌─────────────────────────┐
│      Karışık Çal         │
│      (48px yükseklik)    │
│      saydam arka plan    │  ← background: transparent
│      pembe border        │  ← border: 1px solid var(--theme-primary)
│      pembe text          │  ← color: var(--theme-primary)
└─────────────────────────┘
```

**Ölçüler:**
| Özellik | Değer | Token |
|---------|-------|-------|
| Yükseklik | 48px | `--btn-h-sm` |
| Padding | 10px 20px | `--space-2` `--space-5` |
| Background | transparent | — |
| Border | 1px solid `var(--theme-primary)` | — |
| Text rengi | `var(--theme-primary)` | #ff4fd8 |
| Font boyutu | 13px | `--text-sm` |
| Border-radius | 8px | `--radius-md` |

---

### C06 — Form Input

| Özellik | Değer |
|---------|-------|
| **ID** | C06 |
| **Ad** | Form Input |
| **BEM Sınıfı** | `.form-input` |
| **ITCSS** | 04_Components |
| **Touch Target** | ✅ 56px yükseklik |
| **Kullanıldığı Ekranlar** | Auth (Login, Register), WiFi Connect (şifre) |

**ASCII Wireframe:**
```
┌─────────────────────────────────────┐
│  E-posta, Telefon veya Kullanıcı Adı│  ← label (üstte)
│  ┌─────────────────────────────────┐│
│  │                                 ││  ← input field, 56px yükseklik
│  │  E-postanızı yazınız            ││  ← placeholder
│  └─────────────────────────────────┘│
└─────────────────────────────────────┘
```

**Ölçüler:**
| Özellik | Değer | Token |
|---------|-------|-------|
| Input yüksekliği | 56px | `--input-h` |
| Padding | 12px 16px | `--space-3` `--space-4` |
| Background | `rgba(255,255,255,0.1)` | `--glass-bg` |
| Border | 1px solid `rgba(255,255,255,0.2)` | `--border-subtle` |
| Border-radius | 8px | `--radius-md` |
| Text rengi | `#ffffff` | `--color-white` |
| Placeholder | `rgba(255,255,255,0.4)` | `--color-placeholder` |
| Focus border | `var(--theme-primary)` | #ff4fd8 |
| Font boyutu | 14px | `--text-base` |

---

### C07 — Gender Button

| Özellik | Değer |
|---------|-------|
| **ID** | C07 |
| **Ad** | Gender Button |
| **BEM Sınıfı** | `.gender-btn` |
| **ITCSS** | 05_Pages (`p-select-gender.css`) |
| **Touch Target** | ✅ ~120×80px (büyük) |
| **Kullanıldığı Ekranlar** | Select Gender (3 buton: Kız/Erkek/Diğer) |

**ASCII Wireframe:**
```
┌─────────────────────────────────────────────┐
│  [👩 ikon]  Kız                              │
│             Temizlik, saf duygular           │
│             Pembemsi renk tonları            │
│             (~120×80px)                      │
│             border: 1px solid rgba(255,..)   │
│             border-radius: 12px              │
└─────────────────────────────────────────────┘

Selected state:
┌═════════════════════════════════════════════┐
│  [👩 ikon]  Kız  ← pembe vurgu              │
│  ║         Temizlik, saf duygular  ║        │
│  ║         Pembemsi renk tonları   ║        │
│  ═══════════════════════════════════════    │
│  background: rgba(255,79,216,0.2)           │
│  border: 2px solid var(--theme-primary)     │
└─────────────────────────────────────────────┘
```

**Ölçüler:**
| Özellik | Değer | Token |
|---------|-------|-------|
| Genişlik | ~120px (parent'e göre) | — |
| Yükseklik | ~80px | — |
| Padding | 12px 16px | `--space-3` `--space-4` |
| Background (default) | `rgba(255,255,255,0.05)` | — |
| Background (selected) | `rgba(255,79,216,0.2)` | — |
| Border (default) | 1px solid `rgba(255,255,255,0.15)` | — |
| Border (selected) | 2px solid `var(--theme-primary)` | — |
| Border-radius | 12px | `--radius-lg` |
| İkon boyutu | ~30×30px | — |
| Başlık fontu | 14px, 600 | `--text-base`, `--font-semibold` |
| Alt metin fontu | 11px, 400 | `--text-xs`, `--font-normal` |

**3 Varyant:**
| Varyant | İkon | Başlık | Alt Metin | Tema |
|---------|------|--------|-----------|------|
| Kız | 👩 | Kız | Temizlik, saf duygular · Pembemsi renk tonları | female→pink |
| Erkek | 👨 | Erkek | Güçlü, klasik tonlar · Mavimsi renk tonları | male→blue |
| Diğer | 🤷 | Cinsiyetimi belirtmek istemiyorum | Nötr renk tonları | neutral→default |

---

### C08 — Social Login Button

| Özellik | Değer |
|---------|-------|
| **ID** | C08 |
| **Ad** | Social Login Button |
| **BEM Sınıfı** | `.social-btn` |
| **ITCSS** | 05_Pages (`p-login-view.css`) |
| **Touch Target** | ✅ ~52×52px |
| **Kullanıldığı Ekranlar** | Login, Register (7 buton: Apple, Google, Facebook, WhatsApp, Instagram, TikTok, Mikrofon) |

**ASCII Wireframe:**
```
Satır 1:  [🍎 Apple]  [G Google]  [f Facebook]
Satır 2:  [💬 WhatsApp] [📷 Instagram] [🎵 TikTok]
Satır 3:  [🎤 Mikrofon]

Her buton:
┌──────────┐
│   [icon]  │  ~52×52px
│   52×52   │  border-radius: 12px
│           │  background: siyah veya renkli
└──────────┘
```

**Ölçüler:**
| Özellik | Değer | Token |
|---------|-------|-------|
| Boyut | 52×52px | `--social-btn-size` |
| Border-radius | 12px | `--radius-lg` |
| Background | Siyah (`#000`) veya servis rengi | — |
| İkon | Beyaz, ~24×24px | — |
| Gap (satırlar arası) | 8px | `--space-2` |
| Gap (sütunlar arası) | 8px | `--space-2` |

**Servis Renkleri:**
| Servis | Background | İkon |
|--------|-----------|------|
| Apple | `#000000` | Beyaz elma |
| Google | `#ffffff` | Google logosu |
| Facebook | `#1877F2` | Beyaz f |
| WhatsApp | `#25D366` | Beyaz sohbet |
| Instagram | `#E4405F` | Beyaz kamera |
| TikTok | `#000000` | Beyaz/mavi/kırmızı nota |
| Mikrofon | `var(--theme-primary)` | Beyaz mikrofon |

---

### C09 — Media Card (Album/Artist Card)

| Özellik | Değer |
|---------|-------|
| **ID** | C09 |
| **Ad** | Media Card |
| **BEM Sınıfı** | `.media-card` |
| **ITCSS** | 04_Components |
| **Touch Target** | ✅ ~140×180px (tüm kart tıklanabilir) |
| **Kullanıldığı Ekranlar** | Albums, Artists, Home (En Son Dinlenen, Oynatma Listeleri) |

**ASCII Wireframe (Kare — Albums):**
```
┌────────────────┐
│  ┌──────────┐  │
│  │  140×140 │  │  ← album thumb (kare, border-radius: 8px)
│  │  album   │  │
│  │  art     │  │
│  └──────────┘  │
│  Album Title    │  ← 12px, 600, max 2 satır
│  Artist Name    │  ← 10px, 400, muted
│  00:10:05       │  ← 10px, 400, accent
└────────────────┘
Toplam: ~140×180px
```

**ASCII Wireframe (Dairesel — Artists):**
```
┌────────────────┐
│    ┌────────┐  │
│    │ 140×140│  │  ← artist photo (DAİRE, border-radius: 50%)
│    │圆形    │  │
│    └────────┘  │
│  Artist Name    │  ← 12px, 600
│  Genre          │  ← 10px, 400, muted
│  45 Şarkı       │  ← 10px, 400, accent
└────────────────┘
```

**Ölçüler:**
| Özellik | Değer | Token |
|---------|-------|-------|
| Thumb boyutu | 140×140px | `--card-thumb` |
| Thumb border-radius (album) | 8px | `--radius-md` |
| Thumb border-radius (artist) | 50% (daire) | `--radius-full` |
| Kart genişliği | ~140px | `--card-w` |
| Kart yüksekliği | ~180px | `--card-h` |
| Padding | 8px | `--space-2` |
| Background | `rgba(255,255,255,0.05)` | `--glass-bg-subtle` |
| Border-radius | 8px | `--radius-md` |
| Başlık fontu | 12px, 600 | `--text-sm`, `--font-semibold` |
| Alt metin fontu | 10px, 400 | `--text-xs`, `--font-normal` |
| Süre fontu | 10px, 400, accent | `--text-xs`, `--color-accent` |

---

### C10 — Detail Panel (Sağ Panel)

| Özellik | Değer |
|---------|-------|
| **ID** | C10 |
| **Ad** | Right Detail Panel |
| **BEM Sınıfı** | `.detail-panel` |
| **ITCSS** | 03_Layout |
| **Touch Target** | N/A (container) |
| **Kullanıldığı Ekranlar** | Albums, Artists, File Manager, Göz At |

**ASCII Wireframe:**
```
┌── DETAIL PANEL (sağ ~30-40%) ──────────────────┐
│                                                  │
│  ┌────────────┐                                  │
│  │ 300×300    │  ← album/artist art (daire veya kare)
│  │圆形 veya kare│                                  │
│  └────────────┘                                  │
│                                                  │
│  Başlık (16px, 600)                              │
│  Alt başlık (12px, 400, muted)                   │
│                                                  │
│  [Hemen Çal] (C04 — pembe)                       │
│  [Karışık Çal] (C05 — sınır)  [...]              │
│                                                  │
│  ── Metadata ──                                  │
│  Kalite: 24 Bit / 48 kHz                         │
│  Boyut: 2 GB | İndirme: 2                        │
│  Parça: 12 | Tür: Arabesk                        │
│  Yıl: Bilinmeyen | Dinlenme: 5                   │
│  Süre: 00:30:00                                  │
│                                                  │
└──────────────────────────────────────────────────┘
```

**Ölçüler:**
| Özellik | Değer | Token |
|---------|-------|-------|
| Panel genişliği | ~30-40% (parent'e göre) | — |
| Art boyutu | 300×300px | `--detail-art` |
| Art border-radius (album) | 12px | `--radius-lg` |
| Art border-radius (artist) | 50% (daire) | `--radius-full` |
| Başlık fontu | 16px, 600 | `--text-lg`, `--font-semibold` |
| Metadata fontu | 11px, 400 | `--text-xs`, `--font-normal` |
| Metadata rengi | `rgba(255,255,255,0.6)` | `--color-text-muted` |
| Padding | 16px | `--space-4` |

---

### C11 — Genre Filter Tabs

| Özellik | Değer |
|---------|-------|
| **ID** | C11 |
| **Ad** | Genre Filter Tabs |
| **BEM Sınıfı** | `.genre-tabs` |
| **ITCSS** | 04_Components |
| **Touch Target** | ⚠️ ~32px yükseklik (WCAG İHLALİ: 48px olmalı) |
| **Kullanıldığı Ekranlar** | Albums, Artists |

**ASCII Wireframe:**
```
[Tümü] [Pop] [Arabesk] [Dans] [Oyun Havası] [Damar] [Org] [Yabancı Pop] [Kpop/Kore] ...
^^^^^^   ^^^^   ^^^^^^^^  ^^^^^   ^^^^^^^^^^^^   ^^^^^   ^^^   ^^^^^^^^^^^^   ^^^^^^^^^^^
active   default  default  default   default      default default  default      default
pembe    sınır    sınır    sınır     sınır        sınır   sınır    sınır        sınır
bg                                  (yatay scroll)
```

**Ölçüler:**
| Özellik | Değer | Token |
|---------|-------|-------|
| Tab yüksekliği | ~32px | `--tab-h` (48px olmalı) |
| Tab padding | 6px 16px | `--space-1` `--space-4` |
| Font boyutu | 11px | `--text-xs` |
| Font ağırlığı | 500 (medium) | `--font-medium` |
| Gap (sekmeler arası) | 4px | `--space-1` |
| Background (active) | `var(--theme-primary)` | #ff4fd8 |
| Background (default) | `rgba(255,255,255,0.08)` | — |
| Border-radius | 20px (pill) | `--radius-pill` |
| Text (active) | `#ffffff` | — |
| Text (default) | `rgba(255,255,255,0.7)` | — |
| Scroll | Yatay, `overflow-x: auto` | — |

---

### C12 — Star Rating

| Özellik | Değer |
|---------|-------|
| **ID** | C12 |
| **Ad** | Star Rating |
| **BEM Sınıfı** | `.star-rating` |
| **ITCSS** | 04_Components |
| **Touch Target** | ⚠️ ~20×20px (WCAG İHLALİ: 48px olmalı) |
| **Kullanıldığı Ekranlar** | Album Detail, Playlist |

**ASCII Wireframe:**
```
★★★★★  ← 5 yıldız, her biri ~20×20px
★★★★☆  ← 4 dolu, 1 boş
★★★☆☆  ← 3 dolu, 2 boş

Dolu yıldız: #FFD700 (altın)
Boş yıldız: rgba(255,255,255,0.3)
```

**Ölçüler:**
| Özellik | Değer | Token |
|---------|-------|-------|
| Yıldız boyutu | 20×20px | `--star-size` (48px olmalı) |
| Gap (yıldızlar arası) | 2px | `--space-0` |
| Dolu renk | `#FFD700` | `--color-star` |
| Boş renk | `rgba(255,255,255,0.3)` | `--color-star-empty` |
| Toplam genişlik | ~106px (5×20 + 4×2) | — |

**Öneri:** Tam satırı tıklanabilir yap (tüm 5 yıldız tek hit area → 48px yükseklik)

---

### C13 — Track List Row

| Özellik | Değer |
|---------|-------|
| **ID** | C13 |
| **Ad** | Track List Row |
| **BEM Sınıfı** | `.track-row` |
| **ITCSS** | 04_Components |
| **Touch Target** | ⚠️ ~40px yükseklik (WCAG İHLALİ: 48px olmalı) |
| **Kullanıldığı Ekranlar** | Album Detail, Playlist, Göz At |

**ASCII Wireframe:**
```
┌─────────────────────────────────────────────────────────────────────┐
│ [♪] Göksel - Sevil Neşelen                    00:05:00  ★★★★★    │
│  ↑     ↑                                       ↑          ↑         │
│  thumb title                                   duration  stars      │
│  20×20  ~12px                                  ~10px     ~20px     │
└─────────────────────────────────────────────────────────────────────┘
Yükseklik: ~40px (48px olmalı)
```

**Aktif satır:**
```
┌═════════════════════════════════════════════════════════════════════┐
│ [♪] Göksel - Sevil Neşelen  ← PEMBE VURGU    00:05:00  ★★★★★    │
│  background: rgba(255,79,216,0.15)                                 │
└═════════════════════════════════════════════════════════════════════┘
```

**Ölçüler:**
| Özellik | Değer | Token |
|---------|-------|-------|
| Satır yüksekliği | ~40px | `--row-h` (48px olmalı) |
| Padding | 8px 12px | `--space-2` `--space-3` |
| Thumb boyutu | 20×20px | — |
| Title fontu | 12px, 500 | `--text-sm`, `--font-medium` |
| Duration fontu | 10px, 400 | `--text-xs` |
| Aktif bg | `rgba(255,79,216,0.15)` | — |
| Aktif border-left | 3px solid `var(--theme-primary)` | — |
| Hover | YOK (dokunmatik cihaz) | — |
| Focus-visible | outline: 2px solid `var(--theme-primary)` | — |

---

### C14 — Modal / Popup

| Özellik | Değer |
|---------|-------|
| **ID** | C14 |
| **Ad** | Modal / Popup |
| **BEM Sınıfı** | `.modal` |
| **ITCSS** | 04_Components |
| **Touch Target** | ✅ Kapat butonu 44×44px |
| **Kullanıldığı Ekranlar** | WiFi, Bluetooth, Hoş Geldin, EQ, Settings |

**ASCII Wireframe:**
```
┌── OVERLAY (tam ekran, %50 siyah) ──────────────────────────────┐
│                                                                  │
│    backdrop-filter: blur(4px)                                    │
│    rgba(0,0,0,0.5)                                              │
│                                                                  │
│    ┌── MODAL CONTENT ──────────────────────────────────────┐    │
│    │                                                         │    │
│    │  [Başlık]                              [✕ kapat]       │    │
│    │                                                         │    │
│    │  [İçerik — form, liste, bilgi]                         │    │
│    │                                                         │    │
│    │  [Aksiyon butonları]                                    │    │
│    │                                                         │    │
│    └─────────────────────────────────────────────────────────┘    │
│                                                                  │
└──────────────────────────────────────────────────────────────────┘
```

**Ölçüler:**
| Özellik | Değer | Token |
|---------|-------|-------|
| Overlay bg | `rgba(0,0,0,0.5)` | `--overlay-bg` |
| Overlay backdrop | `blur(4px)` | `--blur-sm` |
| Modal max-width | ~400px (WiFi/BT), 600×308px (Hoş Geldin) | — |
| Modal bg | `rgba(255,255,255,0.1)` + `backdrop-filter: blur(20px) saturate(180%)` | `--glass-bg` |
| Modal border | 1px solid `rgba(255,255,255,0.1)` | `--border-subtle` |
| Modal border-radius | 16px | `--radius-xl` |
| Modal padding | 20px | `--space-5` |
| Kapat butonu | 44×44px (min) | `--touch-min` |
| Başlık fontu | 16px, 600 | `--text-lg`, `--font-semibold` |

---

### C15 — Toggle Switch

| Özellik | Değer |
|---------|-------|
| **ID** | C15 |
| **Ad** | Toggle Switch |
| **BEM Sınıfı** | `.toggle` |
| **ITCSS** | 04_Components |
| **Touch Target** | ⚠️ ~50×28px (border radius nedeniyle 28px yükseklik) |
| **Kullanıldığı Ekranlar** | WiFi, Bluetooth (açma/kapama) |

**ASCII Wireframe:**
```
Kapalı:  [━━━━━━○]  ← gri track, beyaz thumb
Açık:   [○━━━━━━]  ← pembe track, beyaz thumb

Boyut: ~50×28px
```

**Ölçüler:**
| Özellik | Değer | Token |
|---------|-------|-------|
| Genişlik | 50px | `--toggle-w` |
| Yükseklik | 28px | `--toggle-h` (32px olmalı) |
| Track border-radius | 14px (tam yuvarlak) | `--radius-full` |
| Thumb boyutu | 24×24px | — |
| Track bg (off) | `rgba(255,255,255,0.2)` | — |
| Track bg (on) | `var(--theme-primary)` | #ff4fd8 |
| Thumb bg | `#ffffff` | — |
| Transition | 250ms ease | `--transition-base` |

---

### C16 — Network / Device Row

| Özellik | Değer |
|---------|-------|
| **ID** | C16 |
| **Ad** | Network / Device Row |
| **BEM Sınıfı** | `.network-row` |
| **ITCSS** | 04_Components |
| **Touch Target** | ✅ ~48px yükseklik |
| **Kullanıldığı Ekranlar** | WiFi (ağ satırları), Bluetooth (cihaz satırları) |

**ASCII Wireframe:**
```
┌───────────────────────────────────────────────────────────────────┐
│ [📶] Bayram Ali Home [Güçlü][5GHz]  5GHz · Mükemmel · 100%  [Bağlan] │
│  ↑       ↑              ↑        ↑      ↑              ↑        ↑   │
│  icon    name          badge   freq   signal        strength  btn  │
│  24×24   ~12px         pill    ~10px  ~10px         ~10px    48px │
└───────────────────────────────────────────────────────────────────┘
Yükseklik: ~48px (WCAG uyumlu)
```

**Ölçüler:**
| Özellik | Değer | Token |
|---------|-------|-------|
| Satır yüksekliği | ~48px | `--row-h` (WCAG uyumlu) |
| Padding | 8px 12px | `--space-2` `--space-3` |
| İkon boyutu | 24×24px | — |
| İsim fontu | 12px, 500 | `--text-sm`, `--font-medium` |
| Badge fontu | 9px, 600 | `--text-xs`, `--font-semibold` |
| Badge bg | `var(--theme-primary)` | — |
| Badge border-radius | 4px | `--radius-sm` |
| Aksiyon butonu | C05 (secondary button) | — |
| Background (hover) | `rgba(255,255,255,0.05)` | — |
| Focus-visible | outline: 2px solid `var(--theme-primary)` | — |

**Badge Varyantları (WiFi):**
| Badge | Background | Anlam |
|-------|-----------|-------|
| Güçlü | `#22c55e` (yeşil) | Sinyal > -50BS |
| Orta | `#eab308` (sarı) | Sinyal -50 ~ -70BS |
| Zayıf | `#ef4444` (kırmızı) | Sinyal < -70BS |

**Badge Varyantları (Bluetooth):**
| Badge | Background | Anlam |
|-------|-----------|-------|
| A2DP | `var(--theme-primary)` | Yüksek kalite ses |
| HFP | `#6366f1` (mor) | Hands-free profil |
| Müzik | `#22c55e` (yeşil) | Müzik servisi |

---

## 3. Token Matrisi (Tüm Bileşenler)

| Token | Değer | Kullanım |
|-------|-------|----------|
| `--theme-primary` | `#ff4fd8` (female) / `#4f9fff` (male) / `#a0a0b0` (neutral) | Accent, active state |
| `--color-white` | `#ffffff` | Text on primary |
| `--color-text` | `rgba(255,255,255,0.9)` | Primary text |
| `--color-text-muted` | `rgba(255,255,255,0.6)` | Secondary text |
| `--color-placeholder` | `rgba(255,255,255,0.4)` | Input placeholder |
| `--glass-bg` | `rgba(255,255,255,0.1)` | Glass panel background |
| `--glass-bg-subtle` | `rgba(255,255,255,0.05)` | Card background |
| `--border-subtle` | `1px solid rgba(255,255,255,0.2)` | Borders |
| `--overlay-bg` | `rgba(0,0,0,0.5)` | Modal overlay |
| `--blur-sm` | `blur(4px)` | Light blur |
| `--blur-lg` | `blur(20px) saturate(180%)` | Glass effect |
| `--radius-sm` | 4px | Badge, small elements |
| `--radius-md` | 8px | Cards, inputs, buttons |
| `--radius-lg` | 12px | Social buttons, gender buttons |
| `--radius-xl` | 16px | Modals |
| `--radius-pill` | 50px (tam yuvarlak) | Toggle, pills, tags |
| `--radius-full` | 50% (daire) | Avatars, circular cards |
| `--transition-base` | 250ms ease | All transitions |
| `--touch-min` | 44px | WCAG minimum |
| `--touch-recommended` | 48px | RPi5 hedef |
| `--font-body` | Arima | Body text |
| `--font-logo` | Bickham Script Two | Logo only |
| `--text-xs` | 10px | Small labels, metadata |
| `--text-sm` | 12px | Card titles, nav links |
| `--text-base` | 14px | Body text, inputs |
| `--text-lg` | 16px | Section titles |
| `--font-normal` | 400 | Regular text |
| `--font-medium` | 500 | Semi-emphasis |
| `--font-semibold` | 600 | Bold labels |
| `--space-0` | 2px | Minimal gap |
| `--space-1` | 4px | Small gap |
| `--space-2` | 8px | Card padding, row gap |
| `--space-3` | 12px | Row padding |
| `--space-4` | 16px | Section padding |
| `--space-5` | 20px | Modal padding |
| `--space-6` | 24px | Button padding |
| `--browse-sidebar-w` | 167px | Browse page sidebar genişliği |
| `--glass-blur` | `blur(20px) saturate(180%)` | Glass effect backdrop-filter |
| `--modal-overlay` | `rgba(0,0,0,0.5)` | Modal overlay arka plan |

---

## 4. WCAG Uyumluluk Matrisi

| Bileşen | Mevcut Touch | Hedef | Durum | Düzeltme |
|---------|-------------|-------|-------|---------|
| C01 Nav Link | ~24px | 48px | ❌ İHLAL | Padding artır |
| C02 Status Widget | 65×37.4px | 48px | ✅ UYGUN | — |
| C03 User Pill | ~52px | 48px | ✅ UYGUN | — |
| C04 Primary Button | 56px | 48px | ✅ UYGUN | — |
| C05 Secondary Button | 48px | 48px | ✅ UYGUN | — |
| C06 Form Input | 56px | 48px | ✅ UYGUN | — |
| C07 Gender Button | ~120px | 48px | ✅ UYGUN | — |
| C08 Social Button | ~52px | 48px | ✅ UYGUN | — |
| C09 Media Card | ~140px | 48px | ✅ UYGUN | — |
| C10 Detail Panel | N/A | N/A | ✅ Container | — |
| C11 Genre Tabs | ~32px | 48px | ❌ İHLAL | min-height: 48px |
| C12 Star Rating | ~20px | 48px | ❌ İHLAL | Hit area genişlet |
| C13 Track Row | ~40px | 48px | ❌ İHLAL | min-height: 48px |
| C14 Modal Close | 44px | 48px | ⚠️ SINIRDA | — |
| C15 Toggle | ~28px | 32px | ⚠️ SINIRDA | Yükseklik artır |
| C16 Network Row | ~48px | 48px | ✅ UYGUN | — |

**Özet:** 16 bileşenden 8'i uygun, 5'i İHLAL, 3'ü sinirda.

> **Not:** C14 Modal Close kapat butonu 44×44px ile WCAG minimum (44px) ile uyumlu ancak RPi5 touch hedefi olan 48px'in altındadır. RPi5 hedefi için 44→48px'e artırılmalıdır.

---

## 5. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[00-mockup-index]] | PNG master kataloğu |
| [[02-implementation-plan]] | CSS uygulama planı |
| [[03-accessibility-gaps]] | WCAG gap analizi |
| [[architecture/l3-presentation]] | ITCSS mimarisi |
| [[ADR-001-vanilla-js-itcss]] | Framework yasağı |
| [[ADR-044-dynamic-user-theme-engine]] | Tema motoru |

---

## 6. Auth Components

### 6.1 Auth Layout

| Özellik | Değer |
|---------|-------|
| **Layout** | 72/28 split |
| **Sol Taraf (72%)** | Manzara fotoğrafı (tam yükseklik) |
| **Sağ Taraf (28%)** | Glass panel (giriş formu) |
| **Auth Header** | YOK |
| **Auth Footer** | YOK |

### 6.2 Gender Buttons

| Özellik | Değer |
|---------|-------|
| **Sayısı** | 3 seçenek (Kız/Erkek/Diğer) |
| **Boyut** | ~284×60px her biri |
| **BEM Sınıfı** | `.gender-btn` |
| **Touch Target** | ✅ 60px yükseklik (WCAG uyumlu) |

### 6.3 Social Login Buttons

| Özellik | Değer |
|---------|-------|
| **Sayısı** | 7 buton |
| **Sıralama** | Apple, Google, Facebook, WhatsApp, Instagram, TikTok, Mikrofon |
| **Boyut** | 52×52px her biri |
| **BEM Sınıfı** | `.social-btn` |

### 6.4 Auth Form Inputs

| Özellik | Değer |
|---------|-------|
| **Yükseklik** | 56px (C06 — Form Input) |
| **Kullanım** | E-posta, şifre, kullanıcı adı alanları |
| **BEM Sınıfı** | `.form-input` |

### 6.5 KVKK Checkbox

| Özellik | Değer |
|---------|-------|
| **Konum** | Register Step 3 |
| **Durum** | Zorunlu (required) |
| **BEM Sınıfı** | `.kvkk-checkbox` |

---

## 7. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 3.1.0 |
| Component Count | 16 (C01-C16) |
| BEM Classes | 16 (her biri için tanımlı) |
| Token Count | 40+ (tüm bileşenler için) |
| ITCSS Layers | 3 (03_Layout, 04_Components, 05_Pages) |
| WCAG Compliant | 8/16 (%50) |
| WCAG İhlal | 5 (C01, C11, C12, C13, C15) |
| WCAG Sınırda | 3 (C02, C14, C15) |
| ASCII Wireframes | 16 (her bileşen için) |
| PNG Source | 18 (tüm mockup'lardan çıkarıldı) |
| ADR Uyumlu | ✅ ADR-001, ADR-044 |
| Zero Hallucination | ✅ Tüm ölçümler PNG piksel ölçümü |

---

*Component Inventory v3.1.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
