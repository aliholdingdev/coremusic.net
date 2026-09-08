---
title: "Bluetooth Modal — Quick Reference"
type: ascii-qr
screen_id: "S13"
resolution: "1024x600"
layout_pattern: Modal
components: [C01, C04, C14, C16]
png: "home-1024/Linux  1024 - Bluethoot Qucik Page Base.png"
full_spec: "F-quickpanel/bluetooth.md"
---

# Bluetooth Modal

## Layout Wireframe

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
```

## Key Measurements

| Element | Position | Size | Token |
|---------|----------|------|-------|
| Modal | Merkez | ~380×340px | — |
| Overlay | tam ekran | — | `--overlay-bg: rgba(0,0,0,0.5)` |
| Overlay blur | — | blur(4px) | `--overlay-blur` |
| Modal blur | — | blur(20px) | `--glass-blur` |
| Toggle | — | 50×28px | `--toggle-w, --toggle-h` |
| Satır yüksekliği | — | ~48px | `--network-row-h` |
| Touch target | — | ≥48px | `--touch-min` |

## Component Map

| ID | BEM Class | Position | Size |
|----|-----------|----------|------|
| C14 | `.bluetooth-modal` | Merkez | 380×340px |
| C15 | `.bluetooth-toggle` | Modal header | 50×28px |
| C16 | `.bluetooth-device` | Modal body | 100%×48px |
| C04 | `.bluetooth-device__btn` | Satır sağ | "Eşle" / "Bağlantıyı Kes" |
| C01 | `.bluetooth-section__title` | Bölüm başlığı | text-sm |

## Platform Variants

| Platform | Modal Boyutu | Toggle | Satır H | Touch Target |
|----------|-------------|--------|---------|--------------|
| RPi5 (1024×600) | 380×340px | 50×28px | 48px | ≥48px |
| Desktop (1920×1080) | 480×420px | 60×34px | 48px | ≥44px |
| Mobile (375×812) | 100%×auto (bottom sheet) | 50×28px | 56px | ≥48px |
| TV (3840×2160) | 640×560px | 80×44px | 64px | ≥60px |

## Device Badges

| Badge | Renk | Anlam |
|-------|------|-------|
| A2DP | `var(--theme-primary)` | Yüksek kalite ses profili |
| HFP | `#6366f1` (mor) | Hands-free profil |
| Müzik | `#22c55e` (yeşil) | Müzik servisi |
| Güçlü | `#22c55e` | Sinyal > -50BS |
| Orta | `#eab308` | Sinyal -50 ~ -70BS |
| Zayıf | `#ef4444` | Sinyal < -70BS |

## Theme Accent Colors

| Tema | Accent | Hover | Accent-BG |
|------|--------|-------|-----------|
| Female | `#ff4fd8` | `#e63dc0` | `rgba(255,79,216,0.15)` |
| Male | `#4f9fff` | `#3d8ae6` | `rgba(79,159,255,0.15)` |
| Neutral | `#a0a0b0` | `#8a8a9a` | `rgba(160,160,176,0.15)` |

## CSS Hints

```css
.bluetooth-modal { width: 380px; backdrop-filter: var(--glass-blur) var(--modal-saturate); border: var(--modal-border); border-radius: var(--modal-radius); }
.bluetooth-device { display: flex; align-items: center; min-height: var(--network-row-h); background: var(--network-row-bg); border-radius: var(--network-row-radius); }
.badge--a2dp { background: var(--accent-bg); color: var(--accent); }
@media (min-width: 1024px) { .bluetooth-modal { width: 480px; } }
@media (max-width: 767px) { .bluetooth-modal { width: 100%; border-radius: var(--radius-xl) var(--radius-xl) 0 0; } }
```
