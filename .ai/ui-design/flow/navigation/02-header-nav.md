---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Header Navigation Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-29
status: active
version: 1.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Header Navigation Flow

## 1. Akış Diyagramı (Decision Flow)

### Menu Flow

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│ Header Görünür  │
└────────┬────────┘
         │
┌────────▼────────┐
│ Etkileşim Seç   │
└────────┬────────┘
    ┌────┼────────────┐
    │    │            │
┌───▼──┐ │ ┌──▼──────┐ │ ┌──▼──────┐
│Menu  │ │ │Arama    │ │ │Logo     │
│Hamburger│ │İkonu    │ │Tıkla    │
│Tıkla │ │ │Tıkla    │ │         │
└───┬──┘ │ └──┬──────┘ │ └──┬──────┘
    │    │    │        │    │
┌───▼──┐ │ ┌──▼──────┐ │ ┌──▼──────┐
│Menü  │ │ │Arama    │ │ │Ana      │
│Aç    │ │ │Input    │ │ │Sayfa    │
│Overlay│ │ │Aç       │ │ │Yönlendir│
└───┬──┘ │ └──┬──────┘ │ └─────────┘
    │    │    │        │
┌───▼──┐ │ ┌──▼──────┐
│Menü  │ │ │Ara      │
│Öğeleri│ │ │Sonuçlar │
│Göster│ │ │Göster   │
└───┬──┘ │ └──┬──────┘
    │    │    │
┌───▼──┐ │ ┌──▼──────┐
│Seç → │ │ │Seç →    │
│Navigate│ │ │Oynat/Detay│
└───┬──┘ │ └─────────┘
    │    │
    └────┘
         │
    ┌────▼────┐
    │Menü Kapat│
    │Animasyon │
    └─────────┘
```

## 2. Ekran Akışı

```
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 1: Header (Desktop)                                   │
│                                                              │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ 🎵 CoreMusic    Ana Sayfa  Keşfet  Albümler  Sanatçılar │ │
│ │                   Göz At   Geçmiş  Ayarlar             │ │
│ │                                              🔍 [👤]   │ │
│ └─────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────┐
│ EKRAN 2: Header (Phone - Bottom Tab)                        │
│                                                              │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │                                                         │ │
│ │                    (Sayfa İçeriği)                      │ │
│ │                                                         │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │  🏠 Ana Sayfa   🔍 Keşfet   🎵 Kütüphane   ⚙️ Ayarlar  │ │
│ └─────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────┐
│ EKRAN 3: Header (Embedded - Top Nav)                        │
│                                                              │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ 🎵 CoreMusic    Ana Sayfa  Kütüphane  Radyo  Ayarlar   │ │
│ └─────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────┐
│ EKRAN 4: Hamburger Menu (Phone/Tablet)                      │
│                                                              │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ ┌───────────────────────────────────────────────────┐   │ │
│ │ │                                                   │   │ │
│ │ │  🎵 CoreMusic                                    │   │ │
│ │ │                                                   │   │ │
│ │ │  ─────────────────────────────────────────────    │   │ │
│ │ │  🏠 Ana Sayfa                                     │   │ │
│ │ │  🔍 Keşfet                                        │   │ │
│ │ │  💿 Albümler                                      │   │ │
│ │ │  🎤 Sanatçılar                                    │   │ │
│ │ │  👁️ Göz At                                        │   │ │
│ │ │  📜 Geçmiş                                        │   │ │
│ │ │                                                   │   │ │
│ │ │  ─────────────────────────────────────────────    │   │ │
│ │ │  ⚙️ Ayarlar                                       │   │ │
│ │ │  🚪 Logout                                        │   │ │
│ │ │                                                   │   │ │
│ │ └───────────────────────────────────────────────────┘   │ │
│ └─────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────┘
```

## 2A. State Machine

```
┌──────────────┐  Click  ┌──────────────┐  Select  ┌──────────┐
│    Closed    │────────▶│     Open     │────────▶│Navigate  │
└──────────────┘         └──────────────┘         └────┬─────┘
       ▲                                                │
       │ Close                                          │
       │                                                │
┌──────┴───────┘                               ┌───────▼──────┐
│    Closed    │◀──────────────────────────────│   Loading    │
└──────────────┘                               └──────────────┘
```

## 3. Hata Senaryoları

| Hata | Çözüm |
|------|-------|
| Sayfa yüklenemedi | "Sayfa yüklenemedi" + retry |
| Network hatası | "Bağlantı yok" banner |
| Menü açıkken sayfa değişimi | Menü otomatik kapanır |

## 4. Tier-Bazlı Varyasyonlar

| Tier | Menü Tipi | Öğe Sayısı | Davranış |
|------|-----------|:----------:|----------|
| **Phone** | Bottom tab | 3-4 | Swipe |
| **Tablet** | Hamburger + sidebar | 6-8 | Tap |
| **Embedded** | Top nav bar | 4 | Tap |
| **Desktop** | Top nav bar | 8 | Click |
| **TV** | D-pad focus ring | 6 | D-pad |
| **Car** | Voice + simplified | 3 | Voice |
| **Watch** | Crown scroll | 3 | Crown |

## 4A. Nav Link'ler (Cihaz Bazlı)

| Cihaz | Linkler | Sayısı |
|-------|---------|:------:|
| Phone | Ana Sayfa, Kütüphane, Ayarlar | 3 |
| Embedded | Ana Sayfa, Kütüphane, Radyo, Ayarlar | 4 |
| Tablet | Ana Sayfa, Keşfet, Albümler, Kütüphane, Ayarlar | 5 |
| Desktop | Ana Sayfa, Keşfet, Albümler, Sanatçılar, Göz At, Geçmiş, Ayarlar, Hakkımızda | 8 |

---
## 5. BEM Sınıfları

| BEM sınıfı | Rol (bu dosyadaki kanıt) | Durumlar | Kaynak |
|------------|--------------------------|----------|--------|
| `.nav-link`, `.nav-link--active` | eleman — header menü öğeleri (bu dosya L72-L73, L94) ve hamburger menü linkleri (L107-L116) | default, hover, active, disabled | 02-component-inventory.md L35 (C01) |
| `.input`, `.input__label` | eleman — "Arama Input" (bu dosya L42) ve header 🔍 alanı (L74) | default, focus, error, success, disabled | 02-component-inventory.md L75 (C05) |
| `.dropdown`, `.dropdown__menu` | blok — hamburger menü overlay ("Menü Aç Overlay", bu dosya L43, L99-L118) | closed, open, item-hover, item-active | 02-component-inventory.md L165 (C14) |
| `.avatar` | blok — header ikonu [👤] (bu dosya L74) | default, with-image, with-initials, online, offline | 02-component-inventory.md L135 (C11) |
| `.toast`, `.toast--error` | blok — "Bağlantı yok" banner'ı (bu dosya L142) | showing, hiding, success, error, info | 02-component-inventory.md L185 (C16) |

> **Kaynak:** [[../../02-component-inventory]] v4.1.0 (C01-C19, `updated: 2026-09-29`).

---

## 6. Adımlar

| # | Adım | Ekrana | Aksiyon |
|---|------|--------|---------|
| 1 | Menü Hamburger Tıkla | Hamburger Menu (Phone/Tablet) | hamburger menü ikonuna tıkla |
| 2 | Menü Öğesi Seç | Hamburger Menu (Phone/Tablet) | [💿 Albümler] öğesine tıkla |
| 3 | Arama İkonu Tıkla | Header (Desktop) | 🔍 arama ikonuna tıkla |
| 4 | Arama Sonucu Seç | Arama Sonuçları | arama sonuçlarından birine tıkla |
| 5 | Logo Tıkla | Header (Desktop) | 🎵 CoreMusic logosuna tıkla |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
