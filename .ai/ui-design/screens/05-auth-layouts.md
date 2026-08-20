---
title: "CoreMusic — Auth Layouts (§14-18)"
type: reference
date: 2026-08-11
updated: 2026-08-17
status: active
version: 3.0.0
authority: PNG Visual Analysis (direct pixel inspection — all 18 PNGs)
reference:
  authority: ".ai/ui-design/screens/00-ascii-art-index.md"
  source_of_truth: ".ai/ui-design/screens/"
---

# CoreMusic — Auth Layouts (§14-18)

> **§14 LOGIN · §15 REGISTER STEP 1 · §16 REGISTER STEP 2 · §17 REGISTER STEP 3 · §18 AUTH AKIŞ ÖZETİ**

---

## 14. LOGIN — PNG LAYOUT

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header/Footer: YOK                                                                              │
│                                                                                                 │
│ x:0 ┌── SOL ALAN (w:800px) ──────────────────┐  ┌── SAĞ (w:224px) ────────────────────────┐  │
│      │ [CoreMusic Logo]                        │  │  [女神 ikonu]                            │  │
│      │ Seni Tanıyalım                          │  │                                           │  │
│      │ Sisteme                                  │  │  Hoş Geldin                              │  │
│      │ milyonlarca şarkı, özel                  │  │  Hesabına giriş yap, müziğin keyfini     │  │
│      │ önerilerin, playlistler,                 │  │  çıkar                                    │  │
│      │ sonntaxlar, keyfi Benim için.           │  │                                           │  │
│      │                                         │  │  E-posta, Telefon veya Kullanıcı Adı     │  │
│      │ [Tam kaplama fotoğraf]                  │  │  ┌─────────────────────────────────┐     │  │
│      │ (aynı — gender'a göre renk)            │  │  │ (C06 input, 56px)               │     │  │
│      │                                         │  │  └─────────────────────────────────┘     │  │
│      │                                         │  │  Şifre                                   │  │
│      │                                         │  │  ┌─────────────────────────────────┐     │  │
│      │                                         │  │  │ ●●●●●● (C06, password)          │     │  │
│      │                                         │  │  └─────────────────────────────────┘     │  │
│      │                                         │  │  ☑ Beni Hatırla    Şifremi Unuttum      │  │
│      │                                         │  │                                           │  │
│      │                                         │  │  [Giriş Yap] (pembe, full-width)         │  │
│      │                                         │  │                                           │  │
│      │                                         │  │  ── veya alternatif ile devam et ──      │  │
│      │                                         │  │  [🍎][G][f]  (52×52px each)              │  │
│      │                                         │  │  [💬][📷][🎵]                             │  │
│      │                                         │  │  [🎤]                                    │  │
│      │                                         │  │                                           │  │
│      │                                         │  │  Hesabın yok mu?  Kayıt Ol               │  │
│ x:800└─────────────────────────────────────────┘  └───────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 15. REGISTER STEP 1 — PNG LAYOUT

```
┌── SAĞ PANEL (w:224px) ──────────────────────────────────────┐
│  [女神 ikonu]                                                 │
│                                                                │
│  Hesap Oluştur                                                │
│  CoreMusic ailesine katıl,                                     │
│  müziğin keyfini çıkar                                         │
│                                                                │
│  Kullanıcı Adı                                                 │
│  ┌───────────────────────────────────┐                        │
│  │ (C06 input, 56px)                 │                        │
│  └───────────────────────────────────┘                        │
│  E-posta                                                       │
│  ┌───────────────────────────────────┐                        │
│  │ (C06 input, 56px)                 │                        │
│  └───────────────────────────────────┘                        │
│                                                                │
│  [Devam Et] (pembe, full-width)                               │
│                                                                │
│  [🍎][G][f][💬][📷][🎵][🎤]                                    │
│  Hesabın yok mu?  Kayıt Ol                                    │
└──────────────────────────────────────────────────────────────┘
```

---

## 16. REGISTER STEP 2 — PNG LAYOUT

```
┌── SAĞ PANEL (w:224px) ──────────────────────────────────────┐
│  [女神 ikonu]                                                 │
│                                                                │
│  Hesap Oluştur                                                │
│                                                                │
│  Şifre                                                         │
│  ┌───────────────────────────────────┐                        │
│  │ (C06, password, 56px)             │                        │
│  └───────────────────────────────────┘                        │
│  Şifre Tekrar                                                  │
│  ┌───────────────────────────────────┐                        │
│  │ (C06, password, 56px)             │                        │
│  └───────────────────────────────────┘                        │
│                                                                │
│  [Devam Et] (pembe)                                           │
│                                                                │
│  [🍎][G][f][💬][📷][🎵][🎤]                                    │
│  Hesabın yok mu?  Kayıt Ol                                    │
└──────────────────────────────────────────────────────────────┘
```

---

## 17. REGISTER STEP 3 — PNG LAYOUT

```
┌── SAĞ PANEL (w:224px) ──────────────────────────────────────┐
│  [女神 ikonu]                                                 │
│                                                                │
│  Hesap Oluştur                                                │
│                                                                │
│  Telefon                                                       │
│  ┌───────────────────────────────────┐                        │
│  │ (C06, tel, 56px)                  │                        │
│  └───────────────────────────────────┘                        │
│  ☑ Gizlilik Politikası ve Kullanım şartlarını                 │
│    kabul ediyorum                                              │
│                                                                │
│  [Kayıt Ol] (pembe, full-width)                               │
│                                                                │
│  [🍎][G][f][💬][📷][🎵][🎤]                                    │
│  Hesabın yok mu?  Kayıt Ol                                    │
└──────────────────────────────────────────────────────────────┘
```

---

## 18. AUTH AKIŞ ÖZETİ

```
┌──────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐    ┌──────────────┐
│Select Gender │ →  │    Login     │ →  │ Register 1   │ →  │ Register 2   │ →  │ Register 3   │
│ (13+14)      │    │    (15)      │    │   (16)       │    │   (17)       │    │   (18)       │
│              │    │              │    │              │    │              │    │              │
│ Kız/Erkek/   │    │ Email+Şifre  │    │ Kullanıcı    │    │ Şifre+       │    │ Telefon+     │
│ Diğer        │    │ +Social      │    │ Adı+Email    │    │ Şifre Tekrar │    │ KVKK+Kayıt   │
│              │    │              │    │              │    │              │    │              │
│ Tema rengi   │    │ Auth check   │    │ Validation   │    │ Eşleşme      │    │ DB insert    │
│ belirlenir   │    │              │    │              │    │ kontrolü     │    │              │
└──────────────┘    └──────────────┘    └──────────────┘    └──────────────┘    └──────────────┘

Sol alan: Tüm auth ekranlarında aynı (tam kaplama fotoğraf + gradient)
Sağ panel: Tüm auth ekranlarında aynı (glass efekti, 224px genişlik)
```
