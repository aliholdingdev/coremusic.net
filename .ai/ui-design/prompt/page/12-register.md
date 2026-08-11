---
title: "Sayfa Prompt — Register (3 Adım)"
category: page-prompt
version: "1.0.0"
date: "2026-08-11"
route: "/register"
layout: "auth-screen-72-28"
---

# Register (3 Adım)

## Route: `/register`
## Layout Pattern: Auth Screen (72/28 Split)

## Adımlar

```
Adım 1: Temel Bilgiler → Adım 2: Profil → Adım 3: KVKK Onay
```

## Adım 1: Temel Bilgiler

| Bileşen | Adet | Konum | Açıklama |
|---------|------|-------|----------|
| Sol Alan | 1 | Sol (72%) | Manzara + Logo |
| Glass Panel | 1 | Sağ (28%) | Form paneli |
| C06 Form | 1 | Glass panel içinde | Kayıt formu |
| C04 Button | 1 | Form altı | Devam butonu |
| C08 Social | 1 | Form altı | Sosyal kayıt |
| KVKK Checkbox | 1 | Form altı | KVKK onay |

```
┌─── Glass Panel (Adım 1) ─────────┐
│  blur(20px) saturate(180%)       │
│                                   │
│  ┌─ C06 Form ──────────────────┐ │
│  │  Ad Soyad: [______________]  │ │
│  │  E-posta:  [______________]  │ │
│  │  Şifre:    [______________]  │ │
│  │  Şifre Tekrar: [__________]  │ │
│  └──────────────────────────────┘ │
│                                   │
│  ☐ KVKK aydınlatma metnini okudum│
│                                   │
│  ┌─ C04 Button ────────────────┐ │
│  │        DEVAM ET              │ │
│  └──────────────────────────────┘ │
│                                   │
│  ── veya ──                       │
│                                   │
│  ┌─ C08 Social ────────────────┐ │
│  │  [Google]  [Apple]          │ │
│  └──────────────────────────────┘ │
│                                   │
│  Zaten hesabın var mı? Giriş Yap │
└───────────────────────────────────┘
```

## Adım 2: Profil

- Cinsiyet seçimi (opsiyonel)
- Profil fotoğrafı (opsiyonel)
- Müzik tercihleri (opsiyonel)

## Adım 3: KVKK Onay

- KVKK aydınlatma metni
- Onay checkbox'ı
- Kayıt tamamla butonu

## İlerleme Göstergesi

```
[1] Temel Bilgiler ── [2] Profil ── [3] KVKK
     ● ─────────────── ○ ──────────── ○
```

## ASCII Art Referansı

`00-mockup-index.md` §4.16–4.18 — Register (3 Step) ASCII Art
