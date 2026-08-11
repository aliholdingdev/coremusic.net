---
title: "Sayfa Prompt — WiFi Modal"
category: page-prompt
version: "1.0.0"
date: "2026-08-11"
route: "overlay"
layout: "modal"
---

# WiFi Modal

## Route: overlay (modal)
## Layout Pattern: Modal Overlay

## Bileşen Listesi

| Bileşen | Adet | Konum | Açıklama |
|---------|------|-------|----------|
| Overlay | 1 | Tam ekran | rgba(0,0,0,0.5) + blur(4px) |
| C14 Modal | 1 | Merkez | Glass modal |
| C15 Toggle | 1 | Modal üst | WiFi aç/kapa |
| C16 Network Row | N | Modal iç | Ağ listesi |
| Close Button | 1 | Sağ üst | × butonu |

## Modal Yapısı

```
┌─── C14 Modal ──────────────────────────┐
│  ┌─ Header ───────────────── [×] ─┐   │
│  │  WiFi                           │   │
│  ├─ C15 Toggle ───────────────────┤   │
│  │  WiFi: [ON/OFF] Toggle         │   │
│  ├─ C16 Network Rows ─────────────┤   │
│  │  ┌──────────────────────────┐  │   │
│  │  │ WiFi Adı    Sinyal  🔒  │  │   │
│  │  ├──────────────────────────┤  │   │
│  │  │ WiFi Adı    Sinyal  🔓  │  │   │
│  │  ├──────────────────────────┤  │   │
│  │  │ ...                     │  │   │
│  │  └──────────────────────────┘  │   │
│  └────────────────────────────────┘   │
└───────────────────────────────────────┘
```

## C15 Toggle

- WiFi durumu toggle'ı
- ON: yeşil, OFF: gri
- Minimum dokunma alanı: 44×44px

## C16 Network Row

Her satır:
- WiFi adı (sol)
- Sinyal gücü ikonu (orta)
- Kilit ikonu (sağ, şifreli ise)
- Bağlan butonu (sağ)
- Satır yüksekliği: 48px

## ASCII Art Referansı

`00-mockup-index.md` §4.10 — WiFi Modal ASCII Art
