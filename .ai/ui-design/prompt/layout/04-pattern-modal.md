---
title: "Layout Pattern — Modal Overlay"
category: layout-pattern
version: "1.0.0"
date: "2026-08-11"
viewport: "1024×600"
---

# Layout Pattern: Modal Overlay

## Kullanım Alanları

- WiFi Modal (overlay)
- Bluetooth Modal (overlay)
- EQ Settings (overlay)
- General Settings (overlay)

## Yapı

```
┌─────────────────────────────────────────────────────────┐
│  Sayfa içeriği (arka plan)                              │
│                                                         │
│  ┌─── OVERLAY ─────────────────────────────────────┐   │
│  │  rgba(0,0,0,0.5) + backdrop-filter: blur(4px)  │   │
│  │                                                   │   │
│  │      ┌─── MODAL ──────────────────────┐         │   │
│  │      │  Glass: blur(20px)              │         │   │
│  │      │  saturate(180%)                 │         │   │
│  │      │                                 │         │   │
│  │      │  ┌─ Header ─── [×] Close ─┐    │         │   │
│  │      │  │                         │    │         │   │
│  │      │  │  Content Area           │    │         │   │
│  │      │  │                         │    │         │   │
│  │      │  └─ Footer ───────────────┘    │         │   │
│  │      └─────────────────────────────────┘         │   │
│  │                                                   │   │
│  └───────────────────────────────────────────────────┘   │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

## Kurallar

| Parametre | Değer |
|-----------|-------|
| Overlay | `rgba(0,0,0,0.5)` |
| Overlay blur | `backdrop-filter: blur(4px)` |
| Modal blur | `backdrop-filter: blur(20px)` |
| Modal saturate | `saturate(180%)` |
| Modal center | `x=512, y=299.5` (1024×600 viewport) |
| Close button | `min 44×44px` |
| Z-index | Overlay: 1000, Modal: 1001 |

## Close Button

- Minimum dokunma alanı: `44×44px`
- Sağ üst köşede × ikonu
- `position: absolute; top: 16px; right: 16px`

## Glass Efekti

Modal cam efekti için:
```css
.modal {
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(20px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
}
```
