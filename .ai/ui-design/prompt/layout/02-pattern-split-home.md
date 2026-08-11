---
title: "Layout Pattern — Split Home (42/58)"
category: layout-pattern
version: "1.0.0"
date: "2026-08-11"
viewport: "1024×600"
---

# Layout Pattern: Split Home (42/58)

## Kullanım Alanları

- Home Page (`/`)

## Yapı

```
┌─────────────────────────────────────────────────────────┐
│  HEADER (h=56)                                          │
├────────────────────────┬────────────────────────────────┤
│                        │                                │
│  LEFT (42% = 430px)   │  RIGHT (58% = 578px)          │
│                        │                                │
│  Now Playing Card      │  Widget Area (4×Glass)        │
│  Mini Card Grid        │                                │
│  C09 Cards (8 adet)   │  ┌─────────┬─────────┐       │
│                        │  │ Widget1 │ Widget2 │       │
│  16px gap ─────────────│  ├─────────┼─────────┤       │
│                        │  │ Widget3 │ Widget4 │       │
│                        │  └─────────┴─────────┘       │
├────────────────────────┴────────────────────────────────┤
│  FOOTER PLAYER (h=64)                                   │
└─────────────────────────────────────────────────────────┘
```

## Kurallar

| Parametre | Değer |
|-----------|-------|
| Sol alan | 42% = 430px |
| Sağ alan | 58% = 578px |
| Gap | 16px |
| Header | h=56, fixed |
| Footer Player | h=64, fixed |
| Sidebar | ❌ Yok |
| Glass Panel | 4 widget alanı |

## Widget Alanı

Her widget alanı cam efektli (glass) bir paneldir:
- `backdrop-filter: blur(16px)`
- `background: rgba(255,255,255,0.08)`
- `border-radius: 16px`

## Bileşen Eşlemesi

| Sol Alan (42%) | Sağ Alan (58%) |
|----------------|----------------|
| C02 Now Playing | Widget 1: C09×2 Mini Card |
| C09 Mini Card (C03) | Widget 2: Son Çalınanlar |
| C01×8 Kart | Widget 3: Öneri Listesi |
| C09×6 Kart | Widget 4: Hızlı Erişim |
