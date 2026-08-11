---
title: CoreMusic — Welcome Popup Screen Specification (1024×600, Linux Embedded RPi5)
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 2.0.0
authority: PNG Visual Analysis (direct inspection — Linux 1024 - Home Page Welcome Popup.png)
platform: Linux Embedded / Raspberry Pi 5 / 1024×600px
references:
  - [[00-mockup-index]]
  - [[01-component-inventory]]
  - [[_layout-patterns/04-modal]]
  - [[B-home/dashboard]]
---

# CoreMusic — Welcome Popup Screen Specification

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

**Source Image:** `Linux 1024 - Home Page Welcome Popup.png`
**Confidence:** High — directly viewed from PNG screenshot.
**Layout Pattern:** Pattern 4: Modal Overlay (ilk girişte gösterilir)

---

## 1. PLATFORM

| Property | Value |
|----------|-------|
| Resolution | 1024×600px |
| Platform | Linux Embedded (Raspberry Pi 5) |
| Modal Boyutu | 600×308px |
| Modal Merkez | x=512, y=299.5 (tam orta) |
| Rota | `/` (ilk giriş overlay) |
| Tetikleme | İlk girişte veya cookie yoksa |
| Kapatma | Backdrop click veya ✕ butonu |

---

## 2. ASCII WIREFRAME

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────┐
│ 1024×600 — Pattern 4: Modal Overlay — İlk Giriş                                                │
├──────────────────────────────────────────────────────────────────────────────────────────────────┤
│                                                                                                  │
│ [ZONE A: HEADER — arka plan bulanık, backdrop-filter: blur(4px)]                                │
│ [ANA SAYFA (dashboard) — arka plan bulanık, rgba(0,0,0,0.5)]                                   │
│                                                                                                  │
│    ┌── MODAL (600×308px, x:212-812, y:145-453) ──────────────────────────────────────────┐    │
│    │                                                                                        │    │
│    │  ┌────────────────────────────────────────────────────────────────────────────────┐    │    │
│    │  │                                                                                │    │    │
│    │  │                       [CoreMusic Logo — orta hizalı]                          │    │    │
│    │  │                       Hoş geldin                                               │    │    │
│    │  │                                                                                │    │    │
│    │  │                       *İsminizi Girin Buraya*                                 │    │    │
│    │  │                       (Bickham Script Two, italik, pembe)                      │    │    │
│    │  │                                                                                │    │    │
│    │  │       Sana özel seçimler, müzik deneyimlerini ve sunumları                    │    │    │
│    │  │       tamamen sana özel hale getirir. CoreMusic ile rüyalarındaki             │    │    │
│    │  │       müziğin Keyfine dal ♡                                                   │    │    │
│    │  │                                                                                │    │    │
│    │  │                       ┌─────────────┐                                          │    │    │
│    │  │                       │   Başla     │  ← 105×25px (WCAG İHLALİ)               │    │    │
│    │  │                       │  (pembe)    │                                          │    │    │
│    │  │                       └─────────────┘                                          │    │    │
│    │  │                                                                                │    │    │
│    │  └────────────────────────────────────────────────────────────────────────────────┘    │    │
│    │                                                                                        │    │
│    │  Modal arka plan: Glass efekti                                                         │    │
│    │  backdrop-filter: blur(20px) saturate(180%)                                           │    │
│    │  background: rgba(255,255,255,0.1)                                                    │    │
│    │  border: 1px solid rgba(255,255,255,0.1)                                              │    │
│    │  border-radius: 16px                                                                   │    │
│    │                                                                                        │    │
│    └────────────────────────────────────────────────────────────────────────────────────────┘    │
│                                                                                                  │
│ TAM EKRAN ARKA PLAN: Ana sayfa fotoğrafı (sunset, okyanus, pembe tonları)                      │
│ Overlay: rgba(0,0,0,0.5) + backdrop-filter: blur(4px)                                         │
│                                                                                                  │
│ "Başla" butonuna tıklanınca: → Select Gender sayfasına yönlendirme                             │
└──────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. MODAL DETAYLARI

### 3.1 — Modal Container

| Özellik | Değer | Token |
|---------|-------|-------|
| Genişlik | 600px | — |
| Yükseklik | 308px | — |
| Merkez X | 512px (1024/2) | — |
| Merkez Y | 299.5px (600/2) | — |
| Left | 212px (512-300) | — |
| Top | 145px (299.5-154) | — |
| Background | `rgba(255,255,255,0.1)` | `--glass-bg` |
| Backdrop | `blur(20px) saturate(180%)` | `--blur-lg` |
| Border | 1px solid `rgba(255,255,255,0.1)` | `--border-subtle` |
| Border-radius | 16px | `--radius-xl` |
| Padding | 24px | `--space-6` |
| Position | `position: fixed` | — |
| Z-index | 200 | — |

### 3.2 — Overlay

| Özellik | Değer |
|---------|-------|
| Background | `rgba(0,0,0,0.5)` |
| Backdrop | `blur(4px)` |
| Pozisyon | `position: fixed; inset: 0` |
| Z-index | 150 |

---

## 4. İÇERİK DETAYLARI

### 4.1 — Logo

| Özellik | Değer |
|---------|-------|
| Konum | Ortada, üst kısım |
| Boyut | ~60×40px (tahmini) |
| Logo | CoreMusic ikonu + "CoreMusic" yazısı |
| Font | Bickham Script Two |
| Renk | Beyaz |

### 4.2 — Başlık

| Özellik | Değer | Token |
|---------|-------|-------|
| Metin | "Hoş geldin" | — |
| Font | Bickham Script Two | `--font-logo` |
| Boyut | ~24px | `--text-2xl` |
| Renk | Beyaz | `--color-white` |
| Hizalama | Ortada |
| Stil | Normal (italik değil) |

### 4.3 — Alt Başlık (İsim Girme Alanı)

| Özellik | Değer | Token |
|---------|-------|-------|
| Metin | "*İsminizi Girin Buraya*" | — |
| Font | Bickham Script Two | `--font-logo` |
| Boyut | ~16px | `--text-lg` |
| Renk | `var(--theme-primary)` (pembe) | — |
| Stil | İtalik, yıldız işareti ile süslenmiş |
| Hizalama | Ortada |

### 4.4 — Açıklama Metni

| Özellik | Değer | Token |
|---------|-------|-------|
| Metin | "Sana özel seçimler, müzik deneyimlerini ve sunumları tamamen sana özel hale getirir. CoreMusic ile rüyalarındaki müziğin Keyfine dal ♡" | — |
| Font | Arima | `--font-body` |
| Boyut | ~12px | `--text-sm` |
| Renk | `rgba(255,255,255,0.8)` | `--color-text` |
| Hizalama | Ortada |
| Satır yüksekliği | 1.6 | — |
| Maks genişlik | ~450px (modal içinde ortalanmış) |

### 4.5 — "Başla" Butonu

| Özellik | Değer | Token |
|---------|-------|-------|
| Metin | "Başla" | — |
| Genişlik | 105px | — |
| Yükseklik | 25px | ⚠️ WCAG İHLALİ (48px olmalı) |
| Background | `var(--theme-primary)` (#ff4fd8) | — |
| Text rengi | `#ffffff` | — |
| Font | Arima, 12px, 600 | — |
| Border-radius | 8px | `--radius-md` |
| Border | none | — |
| Pozisyon | Ortada, alt kısım |
| Padding | 6px 20px | — |

**WCAG İhlali:**
- Mevcut: 105×25px
- Minimum: 44×44px (WCAG 2.5.8)
- RPi5 Hedef: 48×48px
- **Karar:** Mockup değiştirilsin,但on 105×48px

---

## 5. DAVRANIŞ

### 5.1 — Tetikleme

```
Sayfa yüklenir
  → Cookie kontrolü: 'welcome_shown'
    → Yoksa: Modal göster
    → Varsa: Modal gösterilme
```

### 5.2 — Kapatma

```
Kullanıcı "Başla" butonuna basar
  → Cookie ayarla: 'welcome_shown=1'
  → Modal kapat (fade-out)
  → Select Gender sayfasına yönlendir

VEYA

Kullanıcı backdrop'a tıklar
  → Modal kapat (fade-out)
  → Ana sayfada kal
```

### 5.3 — Animasyon

| Durum | Animasyon | Süre |
|-------|----------|------|
| Açılış | `opacity: 0→1`, `scale: 0.9→1` | 300ms ease |
| Kapanış | `opacity: 1→0`, `scale: 1→0.9` | 200ms ease |
| Backdrop | `opacity: 0→1` | 300ms ease |

---

## 6. BİLEŞEN KULLANIMI

| Bileşen | ID | Kullanım |
|---------|-----|----------|
| Modal Container | C14 | Ana modal |
| Primary Button | C04 | "Başla" butonu |
| — | — | Logo (özel) |
| — | — | Metin blokları (özel) |

---

## 7. WCAG DURUMU

| Kriter | Durum | Not |
|--------|-------|-----|
| Touch target (buton) | ❌ İHLAL | 25px → 48px olmalı |
| Focus indicator | ✅ UYGUN | `:focus-visible` ile |
| Keyboard navigation | ✅ UYGUN | Enter ile "Başla" |
| ARIA | ⚠️ EKSİK | `role="dialog"`, `aria-modal="true"` ekle |
| Screen reader | ⚠️ EKSİK | `aria-label="Hoş geldin popup'ı"` ekle |
| Renk kontrastı | ✅ UYGUN | Beyaz text, pembe buton |
| Escape ile kapatma | ✅ UYGUN | Modal kapat |

---

## 8. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[00-mockup-index]] | PNG master kataloğu |
| [[01-component-inventory]] | C04, C14 detayları |
| [[_layout-patterns/04-modal]] | Modal layout pattern |
| [[B-home/dashboard]] | Ana sayfa (arka plan) |
| [[A-auth/gender-select]] | Sonraki adım |

---

## 9. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Modal Size | 600×308px |
| Pattern | 4: Modal Overlay |
| Components | 3 (C14, C04, özel logo/metin) |
| WCAG Gaps | 3 (button size, ARIA, screen reader) |
| ADR Uyumlu | ✅ ADR-001, ADR-044 |

---

*Welcome Popup Screen Spec v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
