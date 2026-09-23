---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Bluetooth Modal Screen Specification"
type: spec
category: ui-design
date: 2026-09-20
status: active
version: 1.0.0
tier: T08
viewport: 1024x600
device: RPi5 7" Touch (Embedded)
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/ui-design/screens/T08-embedded/bluetooth-modal.md"
  source_of_truth: ".ai/.png/home-1024/Linux  1024 - Bluetooth Quick Page Base.png"
---

# CoreMusic — Bluetooth Modal (T08 Embedded 1024×600)

**Zorunlu Bağlantılar:** [[00-device-matrix]] · [[01-mockup-index]] · [[02-component-inventory]]

---

## 1. ASCII Layout (Piksel Düzeyinde — x:0-1024, y:0-600)

```
┌────────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ y:0 ┌─── HOME DASHBOARD (ARKA PLAN — Bulanık) ────────────────────────────────────────────────────────┐  │
│ y:600└────────────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                            │
│     ┌─── MODAL OVERLAY (bg: rgba(0,0,0,0.5)) ────────────────────────────────────────────────────────┐  │
│     │                                                                                                  │  │
│     │     ┌─── BLUETOOTH MODAL (w:480, h:380, center, glass) ────────────────────────────────────┐   │  │
│     │     │                                                                                      │   │  │
│     │     │  🔵 Bluetooth                       Cihaz Bağlantıları                               │   │  │
│     │     │  Bluetooth ⟷ (toggle: pembe)                                                         │   │  │
│     │     │                                                                                      │   │  │
│     │     │  ─── Bağlı Olan Cihaz ───                                                            │   │  │
│     │     │  ┌──────────────────────────────────────────────────────────────────────────────┐     │   │  │
│     │     │  │ 🎧 Km - 50  🟢Mevcut  🟣Hoparlör  🔊Ses  🎵Müzik  📶Bağlantı türü       │     │   │  │
│     │     │  │    Hoparlör · Müzikal_vurguad · 100% · AAC · SBC · %100                     │     │   │  │
│     │     │  └──────────────────────────────────────────────────────────────────────────────┘     │   │  │
│     │     │                                                                                      │   │  │
│     │     │  ─── Kullanılabilir Cihazlar ───                                                    │   │  │
│     │     │  ┌──────────────────────────────────────────────────────────────────────────────┐     │   │  │
│     │     │  │ 🎧 Km - 50  🟣Hoparlör  🔊Ses  🎵Müzik  │  Eşleştir │ (pembe buton)     │     │   │  │
│     │     │  │    Hoparlör · Müzikal_vurguad · 100% · AAC                                │     │   │  │
│     │     │  ├──────────────────────────────────────────────────────────────────────────────┤     │   │  │
│     │     │  │ 🚗 Car BT  🟣Hoparlör  🔊Ses  🎵Müzik  │  Eşleştir │ (pembe buton)       │     │   │  │
│     │     │  │    Hoparlör · Müzikal_vurguad · 100%                                       │     │   │  │
│     │     │  ├──────────────────────────────────────────────────────────────────────────────┤     │   │  │
│     │     │  │ 📺 Samsung TV  🟣Televizyon  🔊Ses  🎵Müzik  │  Eşleştir │ (pembe buton)  │     │   │  │
│     │     │  │    Televizyon · Müzikal_vurguad · 100%                                     │     │   │  │
│     │     │  └──────────────────────────────────────────────────────────────────────────────┘     │   │  │
│     │     │                                                                                      │   │  │
│     │     └──────────────────────────────────────────────────────────────────────────────────────┘   │  │
│     │                                                                                                  │  │
│     └──────────────────────────────────────────────────────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. BEM Sınıfları

| BEM Sınıfı | Açıklama |
|------------|----------|
| `.bt-modal` | Modal container (w:480, glass) |
| `.bt-modal__header` | Başlık + toggle |
| `.bt-modal__toggle` | Bluetooth açma/kapama toggle |
| `.bt-modal__connected` | Bağlı cihaz bölümü |
| `.bt-modal__available` | Kullanılabilir cihazlar |
| `.bt-device` | Cihaz satırı |
| `.bt-device__icon` | Cihaz ikonu (🎧🚗📺) |
| `.bt-device__name` | Cihaz adı |
| `.bt-device__badges` | Badge'ler (Hoparlör, Ses, Müzik) |
| `.bt-device__signal` | Sinyal gücü |
| `.bt-device__info` | Tür, protokol |
| `.bt-device__action` | Eşleştir butonu |
| `.bt-device--connected` | Bağlı cihaz (highlight) |

---

## 3. Token Referansları

| Token | Değer |
|-------|-------|
| `--cm-primary` | #ff4fd8 |
| `--cm-bg-glass` | rgba(255,255,255,0.15) |
| `--cm-badge-green` | #22c55e |
| `--cm-badge-purple` | #a855f7 |
| `--cm-radius-md` | 12px |
| `--cm-spacing-md` | 16px |

---

## 4. PNG Referansı

| PNG | Viewport |
|-----|----------|
| `Linux 1024 - Bluetooth Quick Page Base.png` | 1024×600 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
