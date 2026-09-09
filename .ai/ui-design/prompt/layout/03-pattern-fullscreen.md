---
title: "Layout Pattern - Fullscreen"
type: layout-prompt
category: layout-pattern
date: 2026-08-11
updated: 2026-09-08
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team * Human Mode * Truth Mode
viewport: "1024x600"
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

---

*Layout Pattern Fullscreen v1.0.0 - CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-08*
*Mode: Red Team * Human Mode * Truth Mode*
