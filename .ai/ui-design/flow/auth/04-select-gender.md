---
type: flow
category: auth
title: "Select Gender Flow (İLK ADIM — Detaylı)"
date: 2026-08-11
version: 2.0.0
platform: home-1024 (Linux Embedded RPi5, 1024×600)
references:
  - [[screens/A-auth/gender-select]]
  - [[screens/00-ascii-art-views]] §13-14
  - [[screens/_layout-patterns/05-auth-screen]]
  - [[01-component-inventory]] C07, C04
  - [[ADR-044-dynamic-user-theme-engine]]
---

# Select Gender Flow — Detaylı Akış Analizi

## ⚠️ BU AKIŞIN İLK ADIMIDIR

Cinsiyet seçimi tema rengini belirler. Bu ekran gösterilmeden Login veya Register'a geçilemez.

---

## 1. AKIŞ DİYAGRAMI

```
┌─────────────────────────────────────────────────────────────────┐
│                    SELECT GENDER AKIŞI                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐      │
│  │ İlk Giriş    │ →  │ Select Gender│ →  │ Tema Kaydet  │      │
│  │ (cookie yok) │    │   (1. adım)  │    │              │      │
│  └──────────────┘    └──────────────┘    └──────┬───────┘      │
│                                                 │               │
│                                          ┌──────▼───────┐      │
│                                          │              │      │
│                                     ┌────▼────┐  ┌─────▼─────┐│
│                                     │ female  │  │ male      ││
│                                     │ #ff4fd8 │  │ #4f9fff   ││
│                                     └────┬────┘  └─────┬─────┘│
│                                          │              │       │
│                                          └──────┬───────┘       │
│                                                 │               │
│                                          ┌──────▼───────┐      │
│                                          │ Login        │      │
│                                          │ Sayfasına    │      │
│                                          │ Yönlendir    │      │
│                                          └──────────────┘      │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 2. SAYFA YAPISI (PNG LAYOUT)

```
┌─────────────────────────────────────────────────────────────────┐
│ x:0                                                          x:1024│
│ Header: YOK (auth sayfası)                                       │
│ Footer: YOK (auth sayfası)                                       │
│                                                                   │
│ ┌── SOL ALAN (x:0-740, ~72%) ──────────────────────────────┐    │
│ │                                                            │    │
│ │   [Tam kaplama manzara fotoğrafı — sunset, çimenlik]      │    │
│ │                                                            │    │
│ │   x:60 y:200                                              │    │
│ │   [CoreMusic Logo — Bickham Script Two, pembe]            │    │
│ │   "Seni Tanıyalım"                                        │    │
│ │   Deneyimini sana özel hale getirmek için bir seçim       │    │
│ │   yapman yeterli.                                         │    │
│ │                                                            │    │
│ │   x:60 y:400                                              │    │
│ │   "İyi ki Varsın Emanet!"                                 │    │
│ │   (Bickham Script Two, italik, dekoratif)                 │    │
│ │                                                            │    │
│ │   x:60 y:520                                              │    │
│ │   "Müziğinle Hayat Buldum"                                │    │
│ │   "Hayatın rastlantılarla dolu..."                        │    │
│ │                                                            │    │
│ └────────────────────────────────────────────────────────────┘    │
│                                                                   │
│ ┌── SAĞ PANEL (x:740-1024, ~284px, glass) ─────────────────┐    │
│ │                                                            │    │
│ │   x:780 y:60                                              │    │
│ │   [Kadın ikonu — line art, ~80×80px]                      │    │
│ │   "Seni Tanıyalım"                                        │    │
│ │   "Müzik deneyimini sana özel hale getirelim"             │    │
│ │                                                            │    │
│ │   x:780 y:160                                             │    │
│ │   ┌──────────────────────────────────────────────┐        │    │
│ │   │ [👩] Kız                     C07 Gender Button│        │    │
│ │   │        Temizlik, saf duygular  (~284×60px)  │        │    │
│ │   │        Pembemsi renk tonları                 │        │    │
│ │   └──────────────────────────────────────────────┘        │    │
│ │   ┌──────────────────────────────────────────────┐        │    │
│ │   │ [👨] Erkek                    C07 Gender Button│        │    │
│ │   │        Güçlü, klasik tonlar     (~284×60px)  │        │    │
│ │   │        Mavimsi renk tonları                 │        │    │
│ │   └──────────────────────────────────────────────┘        │    │
│ │   ┌──────────────────────────────────────────────┐        │    │
│ │   │ [🤷] Diğer                       C07 Gender Button│        │    │
│ │   │        Nötr renk tonları        (~284×60px)  │        │    │
│ │   └──────────────────────────────────────────────┘        │    │
│ │                                                            │    │
│ │   x:780 y:380                                             │    │
│ │   [Devam Et] — pasif (sınır), seçimden sonra pembe        │    │
│ │                                                            │    │
│ │   x:780 y:460                                             │    │
│ │   "Hayatın rastlantılarla dolu..." (dekoratif)            │    │
│ │                                                            │    │
│ │   x:780 y:560                                             │    │
│ │   Devam ederek Gizlilik Politikamızı kabul etmiş olursunuz│    │
│ │                                                            │    │
│ └────────────────────────────────────────────────────────────┘    │
│                                                                   │
│ Layout: 72/28 split — Sol manzara + Sağ glass panel              │
│ Glass panel: backdrop-filter: blur(20px) saturate(180%)          │
└─────────────────────────────────────────────────────────────────┘
```

---

## 3. C07 GENDER BUTTON DETAYI

### 3.1 — Seçilmemiş Hal

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

### 3.2 — Seçili Hal

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

### 3.3 — Buton Özellikleri

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
| **Hover** | ❌ **YOK** (dokunmatik cihaz) | — |
| **Focus-visible** | `2px solid var(--theme-primary)` | — |

### 3.4 — 3 Varyant

| Varyant | İkon | Başlık | Alt Metin | Tema |
|---------|------|--------|-----------|------|
| Kız | 👩 | Kız | Temizlik, saf duygular · Pembemsi renk tonları | female→`#ff4fd8` |
| Erkek | 👨 | Erkek | Güçlü, klasik tonlar · Mavimsi renk tonları | male→`#4f9fff` |
| Diğer | 🤷 | Cinsiyetimi belirtmek istemiyorum | Nötr renk tonları | neutral→`#a0a0b0` |

---

## 4. DEVAM ET BUTONU

### 4.1 — Seçim Yapılmamış (Pasif)

```
┌──────────────────────────────────────────────────────┐
│                    Devam Et                           │
│                    (border, pasif)                    │
│                    border: 1px solid rgba(255,..)     │
│                    color: rgba(255,255,255,0.5)       │
│                    cursor: not-allowed                 │
│                    pointer-events: none                │
└──────────────────────────────────────────────────────┘
```

### 4.2 — Seçim Yapılmış (Aktif)

```
┌══════════════════════════════════════════════════════┐
║                    Devam Et                           ║
║                    (full-width, pembe, 56px)          ║
║                    background: var(--theme-primary)   ║
║                    color: #ffffff                     ║
║                    cursor: pointer                    ║
╚══════════════════════════════════════════════════════╝
```

| Özellik | Değer | Token |
|---------|-------|-------|
| Genişlik | Full-width (~244px) | — |
| Yükseklik | 56px | `--btn-h` |
| Background (pasif) | transparent | — |
| Background (aktif) | `var(--theme-primary)` | #ff4fd8 |
| Border (pasif) | 1px solid `rgba(255,255,255,0.2)` | — |
| Border (aktif) | none | — |
| Text (pasif) | `rgba(255,255,255,0.5)` | — |
| Text (aktif) | `#ffffff` | `--color-white` |
| Font | 14px, 600 | `--text-base, --font-semibold` |
| Border-radius | 8px | `--radius-md` |
| **Hover** | ❌ **YOK** | — |
| **Focus-visible** | `2px solid var(--theme-primary)` | — |
| **Active** | `scale(0.97)` | — |

---

## 5. TEM ETKİSİ

### 5.1 — Mekanizma

```javascript
// Gender seçimi yapıldığında
document.documentElement.setAttribute('data-gender', selectedGender);
// CSS custom property otomatik değişir
```

### 5.2 — Tema Değerleri

| Seçim | data-gender | --theme-primary | Renk |
|-------|-------------|-----------------|------|
| Kız | `female` | `#ff4fd8` | Pembe |
| Erkek | `male` | `#4f9fff` | Mavi |
| Diğer | `neutral` | `#a0a0b0` | Nötr |

### 5.3 — Etkilenen Öğeler

| Öğe | Değişiklik |
|-----|-----------|
| Butonlar (C04, C05) | Background rengi |
| Aktif durumlar | Border rengi |
| Genre tabs | Aktif sekme rengi |
| Seek bar | Dolu kısım rengi |
| Toggle switch | Açık durum rengi |
| Focus ring | Outline rengi |
| Link'ler | Hover rengi |

---

## 6. DAVRANIŞ DETAYLARI

### 6.1 — Sayfa Yükleme

```
Sayfa yüklenir
  → Sol alan arka plan fotoğrafı yüklenir
  → Sağ panel glass efekti ile fade-in (300ms)
  → 3 gender butonu gösterilir (hiçbiri seçili değil)
  → "Devam Et" butonu pasif (cursor: not-allowed)
  → İlk butona otomatik focus yok (kullanıcı seçsin)
```

### 6.2 — Buton Seçimi

```
Kullanıcı bir gender butonuna tıklar
  → Önceki seçim varsa → seçimi kaldır
  → Seçilen buton: selected state'e geçer
    → background: rgba(255,79,216,0.2)
    → border: 2px solid var(--theme-primary)
  → "Devam Et" butonu: pasif → aktif
    → background: var(--theme-primary)
    → cursor: pointer
  → Tema rengi hemen değişir (canlı önizleme)
  → ARIA: aria-checked="true"
```

### 6.3 — Devam Et Tıklaması

```
Kullanıcı "Devam Et"'e tıklar
  → Seçilen cinsiyet: localStorage'a kaydedilir
  → data-gender attribute: kalıcı olarak ayarlanır
  → CSRF token: backend'e gönderilir
  → Backend: cinsiyet tercihi DB'ye kaydedilir
  → Login sayfasına yönlendirme (/auth/login)
```

### 6.4 — Geri Dönme

```
Kullanıcı Login sayfasından geri döner
  → Seçilen cinsiyet korunur (localStorage)
  → Gender sayfası tekrar gösterilmez
  → Sadece ilk girişte gösterilir
```

---

## 7. BEM YAPISI

```html
<main class="auth-screen">
  <div class="auth-screen__hero">
    <!-- Sol manzara alanı -->
    <div class="auth-screen__logo">CoreMusic</div>
    <h1 class="auth-screen__title">Seni Tanıyalım</h1>
    <p class="auth-screen__subtitle">
      Deneyimini sana özel hale getirmek için bir seçim yapman yeterli.
    </p>
  </div>
  
  <div class="auth-screen__panel">
    <!-- Sağ glass panel -->
    <div class="auth-screen__icon">👩</div>
    <h2 class="auth-screen__heading">Seni Tanıyalım</h2>
    <p class="auth-screen__description">
      Müzik deneyimini sana özel hale getirelim
    </p>
    
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
    
    <button class="btn-primary" disabled aria-disabled="true">Devam Et</button>
    
    <p class="auth-screen__decorative">
      Hayatın rastlantılarla dolu... senin gizli Müziğinle partala! ♥
    </p>
    <p class="auth-screen__legal">
      Devam ederek <a href="/privacy">Gizlilik Politikamızı</a> kabul etmiş olursunuz.
    </p>
  </div>
</main>
```

---

## 8. ERİŞİLEBİLİRLİK (WCAG 2.2 AA)

| Kriter | Durum | Not |
|--------|-------|-----|
| Touch target (gender) | ✅ 60px | WCAG uyumlu |
| Touch target (devam et) | ✅ 56px | WCAG uyumlu |
| Focus visible | ✅ | `2px solid var(--theme-primary)` |
| ARIA role | ⚠️ | `role="radiogroup"` + `role="radio"` ekle |
| ARIA checked | ⚠️ | `aria-checked="true/false"` ekle |
| Keyboard nav | ✅ | Tab + Enter ile seçim |
| Screen reader | ⚠️ | `aria-label` ekle |
| Renk kontrastı | ✅ | Beyaz text, pembe buton |

---

## 9. GÜVENLİK NOTLARI

| Kural | Değer | ADR |
|-------|-------|-----|
| CSRF token | `csrf_token` (NOT `_csrf_token`) | ADR-010 |
| Cinsiyet verisi | PII olarak sınıflandırılır | ADR-022 |
| Session | İlk girişte kaydedilir | ADR-011 |
| localStorage | Sadece tema tercihi (guvenli değil) | — |

---

## 10. PERFORMANS NOTLARI

| Metrik | Hedef |
|--------|-------|
| Sayfa yükleme | <1s |
| Tema değişikliği | Anında (CSS custom property) |
| Animasyon | 300ms fade-in (glass panel) |
| Font loading | FOUT (Bickham Script Two) |

---

## 11. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[screens/A-auth/gender-select]] | Screen spec |
| [[screens/00-ascii-art-views]] §13-14 | ASCII art |
| [[screens/_layout-patterns/05-auth-screen]] | Auth layout |
| [[01-component-inventory]] C07, C04 | Bileşenler |
| [[ADR-044-dynamic-user-theme-engine]] | Tema motoru |
| [[flow/auth/01-login]] | Sonraki adım |

---

*Select Gender Flow v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
