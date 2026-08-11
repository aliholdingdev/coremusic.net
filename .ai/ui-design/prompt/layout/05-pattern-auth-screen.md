---
title: "Layout Pattern — Auth Screen (72/28 Split)"
category: layout-pattern
version: "1.0.0"
date: "2026-08-11"
viewport: "1024×600"
---

# Layout Pattern: Auth Screen (72/28 Split)

## Kullanım Alanları

- Select Gender (`/gender-select`)
- Login (`/login`)
- Register (`/register` — 3 adım)

## Yapı

```
┌─────────────────────────────────────────────────────────┐
│  HEADER: ❌ Yok                                         │
├──────────────────────────────┬──────────────────────────┤
│                              │                          │
│  LEFT (72% = 737px)        │  RIGHT (28% = 270px)     │
│                              │                          │
│  Manzara fotoğrafı          │  Glass Panel             │
│  (background-image)         │  blur(20px)              │
│                              │  saturate(180%)          │
│  ┌─────────────┐            │                          │
│  │   LOGO      │            │  ┌──────────────────┐   │
│  │  (merkez)   │            │  │  Form Alanı       │   │
│  └─────────────┘            │  │  C06 Form         │   │
│                              │  │  C04 Button       │   │
│  Dekoratif metin            │  │  C08 Social        │   │
│  (alt kısım)               │  └──────────────────┘   │
│                              │                          │
├──────────────────────────────┴──────────────────────────┤
│  FOOTER: ❌ Yok                                         │
└─────────────────────────────────────────────────────────┘
```

## Kurallar

| Parametre | Değer |
|-----------|-------|
| Sol alan | 72% = 737px |
| Sağ alan | 28% = 270px |
| Header | ❌ Yok |
| Footer | ❌ Yok |
| Glass panel | `blur(20px) saturate(180%)` |
| Sol arka plan | Manzara fotoğrafı |

## Glass Panel

Sağ taraftaki form cam efektli paneldir:
```css
.auth-panel {
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(20px) saturate(180%);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
}
```

## Auth Akışı (3 Adım)

```
1. Select Gender (/gender-select)  → Cinsiyet seçimi
2. Login (/login)                  → Giriş formu
3. Register (/register)            → Kayıt formu (3 adım)
```

## Sol Alan Bileşenleri

- Arka plan manzara fotoğrafı (`background-size: cover`)
- Merkezde logo
- Alt kısımda dekoratif metin
