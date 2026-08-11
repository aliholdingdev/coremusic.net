---
title: "Layout Pattern — Fullscreen"
category: layout-pattern
version: "1.0.0"
date: "2026-08-11"
viewport: "1024×600"
---

# Layout Pattern: Fullscreen

## Kullanım Alanları

- Video Playback (`/playlist/:id` video modu)

## Yapı

```
┌─────────────────────────────────────────────────────────┐
│  ← Back Arrow (sadece geri oku)                        │
├──────────────────────────────┬──────────────────────────┤
│                              │                          │
│  VIDEO AREA (70% = 716px)   │  SONG LIST (30% = 292px) │
│                              │                          │
│                              │  Parça listesi           │
│                              │  scrollable              │
│                              │                          │
├──────────────────────────────┴──────────────────────────┤
│  MINI PLAYER (bottom left, h=56)                        │
│  ┌──────────────────────────────┐                       │
│  │ Album Art │ Title │ Controls │                       │
│  └──────────────────────────────┘                       │
└─────────────────────────────────────────────────────────┘
```

## Kurallar

| Parametre | Değer |
|-----------|-------|
| Video alanı | 70% = 716px |
| Parça listesi | 30% = 292px |
| Header | ❌ Sadece geri oku |
| Footer | ❌ Mini Player (bottom left) |
| Mini Player | h=56, sol alt köşe |
| Gap | 16px |

## Mini Player

Mini player sol alt köşede sabitlenmiştir:
- `position: fixed; bottom: 16px; left: 16px`
- Albüm kapağı, başlık, kontrol butonları
- `width: fit-content`
- Glass efekti

## Tam Ekran Notları

- Header ve footer tamamen kaldırılmıştır
- Sadece sol üstte geri oku butonu bulunur
- Video alanı ağırlıklı, parça listesi yan panel
- Mini player sol alt köşede
