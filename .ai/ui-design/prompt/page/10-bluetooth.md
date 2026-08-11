---
title: "Sayfa Prompt — Bluetooth Modal"
category: page-prompt
version: "1.0.0"
date: "2026-08-11"
route: "overlay"
layout: "modal"
---

# Bluetooth Modal

## Route: overlay (modal)
## Layout Pattern: Modal Overlay

## Bileşen Listesi

| Bileşen | Adet | Konum | Açıklama |
|---------|------|-------|----------|
| Overlay | 1 | Tam ekran | rgba(0,0,0,0.5) + blur(4px) |
| C14 Modal | 1 | Merkez | Glass modal |
| C15 Toggle | 1 | Modal üst | Bluetooth aç/kapa |
| C16 Device Row | N | Modal iç | Cihaz listesi |
| Close Button | 1 | Sağ üst | × butonu |

## Modal Yapısı

```
┌─── C14 Modal ──────────────────────────┐
│  ┌─ Header ───────────────── [×] ─┐   │
│  │  Bluetooth                      │   │
│  ├─ C15 Toggle ───────────────────┤   │
│  │  Bluetooth: [ON/OFF] Toggle     │   │
│  ├─ C16 Device Rows ──────────────┤   │
│  │  ┌──────────────────────────┐  │   │
│  │  │ Cihaz Adı   Tür    🔗   │  │   │
│  │  ├──────────────────────────┤  │   │
│  │  │ Cihaz Adı   Tür    📱   │  │   │
│  │  ├──────────────────────────┤  │   │
│  │  │ ...                     │  │   │
│  │  └──────────────────────────┘  │   │
│  └────────────────────────────────┘   │
└───────────────────────────────────────┘
```

## C15 Toggle

- Bluetooth durumu toggle'ı
- ON: mavi, OFF: gri
- Minimum dokunma alanı: 44×44px

## C16 Device Row

Her satır:
- Cihaz adı (sol)
- Cihaz türü ikonu (orta): 🎧 Kulaklık, 📱 Telefon, 🔊 Hoparlör
- Bağlan/bağlantıyı kes butonu (sağ)
- Satır yüksekliği: 48px
- Bağlı cihaz: yeşil nokta

## ASCII Art Referansı

`00-mockup-index.md` §4.12 — Bluetooth Modal ASCII Art
