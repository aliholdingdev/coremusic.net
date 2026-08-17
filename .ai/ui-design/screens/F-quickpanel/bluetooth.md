---
title: CoreMusic — Bluetooth Modal Screen Specification (1024×600, Linux Embedded RPi5)
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 2.0.0
authority: PNG Visual Analysis (direct inspection — Linux  1024 - Bluethoot Qucik Page Base.png)
platform: Linux Embedded / Raspberry Pi 5 / 1024×600px
references:
  - [[00-mockup-index]]
  - [[01-component-inventory]]
  - [[F-quickpanel/wifi]]
---

# CoreMusic — Bluetooth Modal Screen Specification

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

**Source Image:** `Linux  1024 - Bluethoot Qucik Page Base.png`
**Layout Pattern:** Pattern 4: Modal Overlay (WiFi ile aynı pattern)

---

## 1. ASCII WIREFRAME

```
┌─ MODAL (~400×350px) ─────────────────────────────────────────────────────────────────────┐
│                                                                                            │
│  [✳ icon] Bluetooth                                                                       │
│  Cihaz Bağlantıları                                                                       │
│                                                                                            │
│  Bluetooth  [━━━━━━○] (C15 toggle — pembe)                                               │
│                                                                                            │
│  Bağlı Olan Cihaz                                                                         │
│  ┌──────────────────────────────────────────────────────────────────────────────────┐     │
│  │ [🎧] Kim - 50 [Güçlü][A2DP][HFP][Müzik] Tarayıcı · Mükemmel · 100%            │     │
│  │                                              [Bağlantıyı Kes] (pembe)           │     │
│  └──────────────────────────────────────────────────────────────────────────────────┘     │
│                                                                                            │
│  Kullanılabilir Cihazlar                                                                  │
│  ┌──────────────────────────────────────────────────────────────────────────────────┐     │
│  │ [🎧] Kim - 50 [Güçlü][A2DP][HFP]  Tarayıcı · Mükemmel · 100%       [Eşle]    │     │
│  │ [🚗] Car BT [Orta][A2DP][HFP]      Tarayıcı · İyi · -70BS           [Eşle]    │     │
│  │ [📺] Samsung TV [Zayıf][A2DP]       Televizyon · Mükemmel · 100%     [Eşle]    │     │
│  └──────────────────────────────────────────────────────────────────────────────────┘     │
│                                                                                            │
└────────────────────────────────────────────────────────────────────────────────────────────┘

WiFi ile aynı modal pattern — sadece içerik farklı
Cihaz rozetleri: A2DP (pembe), HFP (mor), Müzik (yeşil)
```

---

## 2. CİHAZ ROZETLERİ

| Badge | Background | Anlam |
|-------|-----------|-------|
| A2DP | `var(--theme-primary)` | Yüksek kalite ses profili |
| HFP | `#6366f1` (mor) | Hands-free profil |
| Müzik | `#22c55e` (yeşil) | Müzik servisi |
| Güçlü | `#22c55e` (yeşil) | Sinyal > -50BS |
| Orta | `#eab308` (sarı) | Sinyal -50 ~ -70BS |
| Zayıf | `#ef4444` (kırmızı) | Sinyal < -70BS |

---

## 3. WCAG

| Kriter | Durum |
|--------|-------|
| Touch target (toggle) | ⚠️ ~28px |
| Touch target (buton) | ✅ 48px |
| Touch target (satır) | ✅ 48px |
| Focus indicator | ✅ |
| Escape ile kapatma | ✅ |

---

*Bluetooth Modal Screen Spec v2.0.0 — CoreMusic UI Design System*
*Last Updated: 2026-08-11*
