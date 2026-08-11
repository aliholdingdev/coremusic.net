---
title: "Sayfa Prompt — Select Gender (İLK ADIM)"
category: page-prompt
version: "1.0.0"
date: "2026-08-11"
route: "/gender-select"
layout: "auth-screen-72-28"
---

# Select Gender (İlk Adım)

## Route: `/gender-select`
## Layout Pattern: Auth Screen (72/28 Split)

## Bileşen Listesi

| Bileşen | Adet | Konum | Açıklama |
|---------|------|-------|----------|
| Sol Alan | 1 | Sol (72%) | Manzara + Logo |
| Glass Panel | 1 | Sağ (28%) | Form paneli |
| C07 Gender Button | 3 | Glass panel içinde | Cinsiyet seçimi |
| C04 Button | 1 | Form altı | Devam butonu |

## Auth Akışı Başlangıcı

```
Select Gender (/gender-select) → Login (/login) → Register (/register)
```

**Bu sayfa auth akışının İLK adımıdır.** Kullanıcı giriş/kayıt yapmadan önce cinsiyet seçer.

## Sol Alan (72% = 737px)

- Arka plan manzara fotoğrafı (`background-size: cover`)
- Merkezde logo (beyaz, hafif gölge)
- Alt kısımda dekoratif metin

## Sağ Alan (28% = 270px) — Glass Panel

```
┌─── Glass Panel ──────────────────┐
│  blur(20px) saturate(180%)       │
│                                   │
│  Cinsiyetini seç                 │
│  (tema rengini belirler)         │
│                                   │
│  ┌─ C07 Button ────────────────┐ │
│  │  👩  Kadın                   │ │
│  │  (pembe tema)               │ │
│  └──────────────────────────────┘ │
│                                   │
│  ┌─ C07 Button ────────────────┐ │
│  │  👨  Erkek                   │ │
│  │  (mavi tema)                │ │
│  └──────────────────────────────┘ │
│                                   │
│  ┌─ C07 Button ────────────────┐ │
│  │  🌐  Nötr                    │ │
│  │  (varsayılan tema)          │ │
│  └──────────────────────────────┘ │
│                                   │
│  ┌─ C04 Button ────────────────┐ │
│  │        DEVAM ET              │ │
│  └──────────────────────────────┘ │
└───────────────────────────────────┘
```

## C07 Gender Button

Her buton:
- İkon (sol)
- Metin (orta)
- Tema rengi bilgisi (alt, küçük)
- Boyut: tam genişlik, h=56px
- Seçili: kenarlık vurgusu
- `border-radius: 12px`

## Tema Eşlemesi

| Cinsiyet | Tema | Renk |
|----------|------|------|
| Kadın | Pembe | `#FF69B4` |
| Erkek | Mavi | `#4A90D9` |
| Nötr | Varsayılan | `#888888` |

## ASCII Art Referansı

`00-mockup-index.md` §4.13–4.14 — Gender Select ASCII Art
