---
title: "Sayfa Prompt — Login"
category: page-prompt
version: "1.0.0"
date: "2026-08-11"
route: "/login"
layout: "auth-screen-72-28"
---

# Login

## Route: `/login`
## Layout Pattern: Auth Screen (72/28 Split)

## Bileşen Listesi

| Bileşen | Adet | Konum | Açıklama |
|---------|------|-------|----------|
| Sol Alan | 1 | Sol (72%) | Manzara + Logo |
| Glass Panel | 1 | Sağ (28%) | Form paneli |
| C06 Form | 1 | Glass panel içinde | Giriş formu |
| C04 Button | 1 | Form altı | Giriş butonu |
| C08 Social | 1 | Form altı | Sosyal giriş |
| Checkbox | 1 | Form altı | Beni hatırla |

## Sol Alan (72% = 737px)

- Arka plan manzara fotoğrafı (`background-size: cover`)
- Merkezde logo (beyaz, hafif gölge)
- Alt kısımda dekoratif metin

## Sağ Alan (28% = 270px) — Glass Panel

```
┌─── Glass Panel ──────────────────┐
│  blur(20px) saturate(180%)       │
│                                   │
│  ┌─ C06 Form ──────────────────┐ │
│  │  E-posta: [______________]  │ │
│  │  Şifre:   [______________]  │ │
│  └──────────────────────────────┘ │
│                                   │
│  ☐ Beni hatırla                  │
│                                   │
│  ┌─ C04 Button ────────────────┐ │
│  │        GİRİŞ YAP            │ │
│  └──────────────────────────────┘ │
│                                   │
│  ── veya ──                       │
│                                   │
│  ┌─ C08 Social ────────────────┐ │
│  │  [Google]  [Apple]          │ │
│  └──────────────────────────────┘ │
│                                   │
│  Hesabın yok mu? Kayıt Ol        │
└───────────────────────────────────┘
```

## Auth Akışı

```
Login (/login) → Home (/)
```

## ASCII Art Referansı

`00-mockup-index.md` §4.15 — Login ASCII Art
