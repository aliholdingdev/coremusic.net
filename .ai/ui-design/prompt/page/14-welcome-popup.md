---
title: "Sayfa Prompt — Hoş Geldin Modalı"
category: page-prompt
version: "1.0.0"
date: "2026-08-11"
route: "/ (ilk giriş)"
layout: "modal-600x308"
---

# Hoş Geldin Modalı (Welcome Popup)

## Route: `/` (ilk giriş)
## Layout Pattern: Modal (600×308px)

## Bileşen Listesi

| Bileşen | Adet | Konum | Açıklama |
|---------|------|-------|----------|
| Overlay | 1 | Tam ekran | rgba(0,0,0,0.5) + blur(4px) |
| C14 Modal | 1 | Merkez | 600×308px modal |
| C04 Button | 1 | Modal iç | Devam butonu |

## Modal Boyutları

| Parametre | Değer |
|-----------|-------|
| Genişlik | 600px |
| Yükseklik | 308px |
| Merkez X | 512 (viewport ortası) |
| Merkez Y | 299.5 (viewport ortası) |
| `border-radius` | 16px |

## Modal Yapısı

```
┌─── C14 Modal (600×308) ─────────────────────┐
│                                               │
│  ┌─────────────────────────────────────────┐ │
│  │                                         │ │
│  │  🎵                                     │ │
│  │                                         │ │
│  │  Hoş Geldin!                            │ │
│  │                                         │ │
│  │  CoreMusic'e hoş geldin.               │ │
│  │  Müzik deneyimini personalleştirmek     │ │
│  │  için temel bilgilerini dolduralım.      │ │
│  │                                         │ │
│  │  ┌─ C04 Button ──────────────────────┐ │ │
│  │  │         BAŞLA                      │ │ │
│  │  └────────────────────────────────────┘ │ │
│  │                                         │ │
│  └─────────────────────────────────────────┘ │
│                                               │
└───────────────────────────────────────────────┘
```

## Glass Efekti

```css
.modal {
  width: 600px;
  height: 308px;
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(20px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
}
```

## Akış

```
İlk giriş → Welcome Popup → Gender Select (/gender-select) → Login/Register
```

## Notlar

- Bu modal sadece ilk girişte gösterilir
- "BAŞLA" butonuna tıklanınca Gender Select sayfasına yönlendirilir
- Modal kapatılamaz (× butonu yok)
- Backdrop click de modalı kapatmaz

## ASCII Art Referansı

`00-mockup-index.md` §4.2 — Welcome Popup ASCII Art
