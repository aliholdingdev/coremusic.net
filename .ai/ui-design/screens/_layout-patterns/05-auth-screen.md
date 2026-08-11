---
title: CoreMusic — Auth Screen Layout Pattern
date: 2026-08-11
updated: 2026-08-11
type: spec
status: active
version: 2.0.0
---

# CoreMusic — Auth Screen Layout Pattern

## Kullanım

Select Gender, Login, Register (3 adım)

## ASCII Wireframe

```
┌──────────────────────────────────────────────────────────────────────────────┐
│ Header: YOK                                                                  │
│ Footer: YOK                                                                  │
│                                                                              │
│  ┌── SOL ALAN (~78%, ~800px) ────────────────┐  ┌── SAĞ (~22%, ~224px) ──┐│
│  │                                             │  │                          ││
│  │  [CoreMusic Logo — sol üst]                 │  │  [女神 ikonu]            ││
│  │  Başlık + açıklama                          │  │  Başlık + açıklama       ││
│  │                                             │  │                          ││
│  │  [Tam kaplama fotoğraf]                     │  │  [Form inputs]           ││
│  │  (gender'a göre renk değişir)               │  │  [Butonlar]              ││
│  │                                             │  │  [Social buttons]        ││
│  │                                             │  │  [Alt linkler]           ││
│  │                                             │  │                          ││
│  │                                             │  │  Glass efekti            ││
│  │                                             │  │  backdrop-filter: blur   ││
│  └─────────────────────────────────────────────┘  └──────────────────────────┘│
│                                                                              │
└──────────────────────────────────────────────────────────────────────────────┘
```

## Auth Akış Sırası

```
Select Gender (1) → Login (2) → Register Step 1 (3a) → Step 2 (3b) → Step 3 (3c)
```

## Sol Alan

| Özellik | Değer |
|---------|-------|
| Genişlik | ~78% (~800px) |
| Arka plan | Tam kaplama fotoğraf + gradient |
| Logo | Sol üst, ~60×40px |
| Başlık | "Seni Tanıyalım" (24px, Bickham) |
| Alt metin | Açıklama (12px, muted) |

## Sağ Panel

| Özellik | Değer |
|---------|-------|
| Genişlik | ~22% (~224px) |
| Background | `rgba(255,255,255,0.05)` |
| Backdrop | `blur(20px) saturate(180%)` |
| Border-radius | 0 (sol kenar yuvarlak değil) |
| Padding | 20px |
| Glass | Evet |

## Kurallar

1. Header/Footer YOK (auth sayfalarında)
2. Sol alan tam kaplama fotoğraf
3. Sağ panel glass efekti
4. Gender seçimi tema rengini değiştirir
5. Auth akışı zorunlu sıra: Gender → Login → Register

---

*Auth Screen Layout v2.0.0 — CoreMusic UI Design System*
