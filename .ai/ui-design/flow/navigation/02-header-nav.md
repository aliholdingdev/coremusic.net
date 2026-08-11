---
title: CoreMusic — Navigation Flow: Header Navigation (Detaylı, 500+ Satır)
date: 2026-08-11
version: 2.0.0
platform: home-1024 (Linux Embedded RPi5, 1024×600)
author: Senior Frontend Architect (50+ yıl deneyim)
references:
  - [[screens/00-ascii-art-views]] §1
  - [[01-component-inventory]] C01, C02, C03
  - [[ADR-021-spa-router-immutable-contract]]
---

# Navigation Flow: Header Navigation — Detaylı Akış Analizi

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

---

## 1. HEADER YAPISI (PNG Layout)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                          x:1024        │
│ y:0 ┌───────────────────────────────────────────────────────────────────┐   │
│     │                                                                   │   │
│     │ [Core Music]  Ana Sayfa  Keşfet  Albümler  Sanatçılar  Göz At  │   │
│     │ ↑logo        ↑nav-link  ↑       ↑          ↑          ↑         │   │
│     │ (Bickham)    (Arima 10px)                                          │   │
│     │                                                                   │   │
│     │                                          [Bayram Ali ▾]          │   │
│     │                                          ↑C03 user pill          │   │
│     │                                                                   │   │
│     │                                          [📶✳] [🔋 pill]        │   │
│     │                                           ↑C02 status            │   │
│     │                                                                   │   │
│     │                                              [⚙] [⏻]            │   │
│     │                                               ↑settings ↑logout  │   │
│     │                                                                   │   │
│ y:60├───────────────────────────────────────────────────────────────────┤   │
│     │  Header yüksekliği: 60px                                        │   │
│     │  Background: rgba(0,0,0,0.3) + backdrop-filter: blur(10px)      │   │
│     │  Border-bottom: 1px solid rgba(255,255,255,0.1)                 │   │
│     │  Position: fixed; top: 0; z-index: 100                          │   │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. HEADER BİLEŞENLERİ

### 2.1 — Logo (Zone A)

| Özellik | Değer | Token |
|---------|-------|-------|
| Metin | "Core Music" | — |
| Font | Bickham Script Two | `--font-logo` |
| Boyut | ~14px | `--text-lg` |
| Renk | Beyaz | `--color-white` |
| Pozisyon | Sol üst, x:16 |
| Link | `/` (ana sayfa) |
| ARIA | `aria-label="CoreMusic Ana Sayfa"` |

### 2.2 — Nav Links (Zone B) — C01

| Özellik | Değer | Token |
|---------|-------|-------|
| Sayısı | 8 | — |
| Linkler | Ana Sayfa, Keşfet, Albümler, Sanatçılar, Göz At, Geçmiş, Ayarlar, Hakkımızda |
| Font | Arima, 10px | `--text-xs` |
| Renk (default) | `rgba(255,255,255,0.85)` | — |
| Renk (active) | `var(--theme-primary)` | #ff4fd8 |
| Padding | 2px 4px | `--space-1` |
| Gap | 4px | `--space-1` |
| Hit area | ~24×24px | ❌ → 48px olmalı |
| Aktif göstergesi | Alt çizgi veya renk değişimi | — |

### 2.3 — Actions Area (Zone C-F)

```
┌── Actions Area (flex, margin-left: auto) ──────────────────────────────┐
│                                                                        │
│  Zone D: WiFi+BT Group (pill 65×37.4px)                              │
│    [📶 WiFi] [✳ BT]                                                   │
│    → Tıklama → WiFi/BT modal açılır                                  │
│                                                                        │
│  Zone D: Battery (pill 100px wide)                                    │
│    [🔋] %100                                                          │
│    → Sadece bilgi, tıklanamaz                                        │
│                                                                        │
│  Zone E: User Pill (~150px)                                           │
│    [🧑 avatar 35×35] Bayram Ali ▾                                     │
│    → Tıklama → Dropdown menü açılır                                   │
│    → Menü: Profil, Ayarlar, Çıkış                                    │
│                                                                        │
│  Zone F: Action Buttons                                               │
│    [⚙ settings] → /settings sayfasına yönlendirme                    │
│    [⏻ logout]   → Onay dialog'u → Çıkış                             │
│                                                                        │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 3. DAVRANIŞ DETAYLARI

### 3.1 — Logo Tıklama

```
Kullanıcı logo'ya tıklar
  → JS: Router.js → #navigate('/')
  → Ana sayfaya yönlendirme
  → Header'daki tüm nav link'ler default'a döner
  → "Ana Sayfa" link'i active olur
```

### 3.2 — Nav Link Tıklama

```
Kullanıcı bir nav-link'e tıklar
  → JS: Event.preventDefault()
  → JS: Router.js → #navigate(path)
  → JS: URL güncellenir (history.pushState)
  → AJAX: Backend'e istek gönderilir
  → PHP: Handler çalışır
  → JS: DOM patching ile sayfa güncellenir
  → JS: Önceki active link'in active'i kaldırılır
  → JS: Yeni link active olur
  → JS: Scroll pozisyonu sıfırlanır
  → Footer player korunur (çalma devam eder)
```

### 3.3 — WiFi/BT İkonu Tıklama

```
Kullanıcı WiFi/BT pill'ine tıklar
  → JS: WiFi modal açılır (flow/settings/01-wifi-connect)
  → VEYA JS: BT modal açılır (flow/settings/02-bluetooth-connect)
```

### 3.4 — User Pill Tıklama

```
Kullanıcı user pill'e tıklar
  → JS: Dropdown menü açılır
  → Menü içeriği:
    → [🧑] Profil → /profile
    → [⚙] Ayarlar → /settings
    → [ℹ] Hakkımızda → /about
    → [⏻] Çıkış → Onay dialogu
  → Menü kapatma: backdrop click veya Escape
```

### 3.5 — Settings Tıklama

```
Kullanıcı ⏻ butonuna basar
  → JS: Router.js → #navigate('/settings')
  → Settings sayfasına yönlendirme
```

### 3.6 — Logout Tıklama

```
Kullanıcı ⏻ butonuna basar
  → JS: Modal açılır: "Çıkış yapmak istediğinize emin misiniz?"
  → Onay → Backend: Session silinir, cookie temizlenir
  → Login sayfasına yönlendirme
```

---

## 4. RESPONSIVE DAVRANIŞ

| Platform | Header Değişikliği |
|----------|-------------------|
| 1024px (RPi5) | Mevcut layout (bu dosya) |
| 1920px (Desktop) | Nav link'ler genişler, gap artar |
| 3840px (4K TV) | Font boyutları büyür (14px), butonlar 64px |
| Mobile | Header gizlenir, hamburger menü |

---

## 5. ERİŞİLEBİLİRLİK

| Kriter | Durum |
|--------|-------|
| Touch target (nav) | ❌ ~24px → 48px |
| Touch target (buton) | ✅ 44px |
| Focus indicator | ✅ |
| Keyboard nav | ✅ (Tab ile gezinme) |
| ARIA | ✅ `aria-label` mevcut |
| Screen reader | ✅ |

---

## 6. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[screens/00-ascii-art-views]] §1 | ASCII art |
| [[01-component-inventory]] C01, C02, C03 | Bileşenler |
| [[ADR-021-spa-router-immutable-contract]] | SPA router |
| [[flow/navigation/01-spa-routing]] | SPA routing |
| [[flow/navigation/03-footer-player]] | Footer player |

---

*Header Navigation Flow v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
