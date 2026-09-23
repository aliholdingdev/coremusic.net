---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Header Navigation Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Header Navigation Flow

## 1. Akış Diyagramı (Menu Flow)

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

## 3. State Machine

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

## 4. Hata Senaryoları

| Hata | Çözüm |
|------|-------|
| Sayfa yüklenemedi | "Sayfa yüklenemedi" + retry |
| Network hatası | "Bağlantı yok" banner |
| Menü açıkken sayfa değişimi | Menü otomatik kapanır |

## 5. Tier-Bazlı Varyasyonlar

| Tier | Menü Tipi | Öğe Sayısı | Davranış |
|------|-----------|:----------:|----------|
| **Phone** | Bottom tab | 3-4 | Swipe |
| **Tablet** | Hamburger + sidebar | 6-8 | Tap |
| **Embedded** | Top nav bar | 4 | Tap |
| **Desktop** | Top nav bar | 8 | Click |
| **TV** | D-pad focus ring | 6 | D-pad |
| **Car** | Voice + simplified | 3 | Voice |
| **Watch** | Crown scroll | 3 | Crown |

## 6. Nav Link'ler (Cihaz Bazlı)

| Cihaz | Linkler | Sayısı |
|-------|---------|:------:|
| Phone | Ana Sayfa, Kütüphane, Ayarlar | 3 |
| Embedded | Ana Sayfa, Kütüphane, Radyo, Ayarlar | 4 |
| Tablet | Ana Sayfa, Keşfet, Albümler, Kütüphane, Ayarlar | 5 |
| Desktop | Ana Sayfa, Keşfet, Albümler, Sanatçılar, Göz At, Geçmiş, Ayarlar, Hakkımızda | 8 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
