---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Car Android Auto Home Dashboard Screen Specification"
type: spec
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T29
viewport: 1280x720
device: Android Auto (Orta Boy)
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T29-car-android-auto/home-dashboard.md"
  source_of_truth: ".ai/ui-design/00-device-matrix.md"
---

# CoreMusic — Car Android Auto Home Dashboard (T29 1280×720)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1280, y:0-720)

```
┌──────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                                                        x:1280 │
│ y:0 ┌─── STATUS BAR (h:48) ──────────────────────────────────────────────────────────────────────┐ │
│     │ 📶  🔋 100%  ⏰ 07:00  🌡️ 22°C  🚗 Drive Mode Active                                      │ │
│ y:48 └────────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                                    │
│ y:48 ┌─── CONTENT (h:624) ───────────────────────────────────────────────────────────────────────┐ │
│     │                                                                                             │ │
│     │  ┌─── NOW PLAYING (50%, w:640) ──────────────────┐  ┌─── QUICK ACTIONS (50%, w:640) ─────┐ │ │
│     │  │                                                  │  │                                    │ │ │
│     │  │  🎵 Album Art (240×240)                          │  │  🎵 Shuffle All    🔀              │ │ │
│     │  │  Göksel - Sevil Neşelenen                        │  │                                    │ │ │
│     │  │  Hayat Rüya Gibi                                 │  │  📻 Radio          📡              │ │ │
│     │  │  Göksel                                          │  │                                    │ │ │
│     │  │                                                  │  │  🎤 Podcasts       🎙️              │ │ │
│     │  │  ⏱ 00:05:00 ━━━━━━━━━━━━━━━━━━━━━━━ 100%      │  │                                    │ │ │
│     │  │                                                  │  │  📁 My Music       📂              │ │ │
│     │  │  [⏮] [▶] [⏭]                                   │  │                                    │ │ │
│     │  │                                                  │  │  🔊 Volume: ████████░░ 80%        │ │ │
│     │  └──────────────────────────────────────────────────┘  └────────────────────────────────────┘ │ │
│     │                                                                                             │ │
│     └─────────────────────────────────────────────────────────────────────────────────────────────┘ │
│                                                                                                    │
│ y:672 ┌─── NAVIGATION BAR (h:48) ────────────────────────────────────────────────────────────────┐ │
│     │ 🏠 Home    🎵 Music    📻 Radio    🎤 Podcast    ⚙️ Settings                                │ │
│ y:720 └────────────────────────────────────────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. BEM Sınıfları

| BEM Sınıfı | Açıklama |
|------------|----------|
| `.car-aa` | Car Android Auto layout modifier |
| `.car-aa__status` | Status bar (h:48) |
| `.car-aa__content` | Content area (50/50 split) |
| `.car-aa__now-playing` | Now Playing panel |
| `.car-aa__actions` | Quick actions grid |
| `.car-aa__nav` | Bottom navigation bar (h:48) |
| `.car-nav__item` | Navigation item |
| `.car-nav__item--active` | Active nav item |
| `.car-action` | Quick action card |
| `.car-action__icon` | Action icon (48×48) |
| `.car-action__label` | Action label |

---

## 3. Token Referansları

| Token | Değer |
|-------|-------|
| `--cm-primary` | #ff4fd8 |
| `--cm-radius-lg` | 16px |
| `--cm-spacing-xl` | 24px |
| `--cm-touch-target` | 80px |
| `--cm-font-scale` | 1.125 |
| `--cm-nav-height` | 48px |
| `--cm-status-height` | 48px |

---

## 4. Touch Target

| Cihaz | Min Touch |
|-------|-----------|
| T29 Android Auto | 80×80px (safety-first) |

---

## 5. Güvenlik Kuralları

| Kural | Açıklama |
|-------|----------|
| Minimal Text | Sürücü dikkatini dağıtacak uzun metin yok |
| Large Buttons | Min 80×80px touch target |
| Voice-First | Sesli komut öncelikli |
| Dark Mode | Gece sürüşü için koyu tema |
| High Contrast | Güneş ışığında okunabilirlik |
| No Animations | Sürüş sırasında dikkat dağıtıcı animasyon yok |

---

## 6. PNG Referansı

| PNG | Viewport |
|-----|----------|
| Yok | 1280×720 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
