---
title: "CoreMusic — Auth Screen Mockups"
type: reference
category: ui-design/mockups
date: 2026-08-19
updated: 2026-08-19
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
parent: "[[00-mockup-index]]"
screens:
  - "PNG #13 — Select Gender"
  - "PNG #14 — Select Gender (Selected)"
  - "PNG #15 — Login"
  - "PNG #16 — Register Step 1"
  - "PNG #17 — Register Step 2"
  - "PNG #18 — Register Step 3"
png_source: ".ai/.png/shared-1024/ (6 PNG)"
---

# Auth Screen Mockups

**6 PNG — shared-1024/ dizininde.** Auth ekranları tüm subdomain'lerde ortaktır.

> **⚠️ Mockup Before Frontend:** CSS/HTML/JS/layout/bileşen görevlerinde ilgili görsel okunmadan kod yazılamaz.

---

## Auth Ekran Envteri

| # | Ekran | PNG Dosyası | Sıra | Layout Pattern |
|---|-------|-------------|------|---------------|
| 13 | **Select Gender** | `Linux  1024 - Select Gender.png` | **1 (İLK)** | Pattern 5: Auth (72/28) |
| 14 | **Select Gender (Selected)** | `Linux  1024 - Select Gender - selected.png` | 1 (seçili hal) | Pattern 5: Auth |
| 15 | **Login** | `Linux  1024 - Login Girl.png` | **2** | Pattern 5: Auth (72/28) |
| 16 | **Register Step 1** | `Linux  1024 - Register Girl.png` | **3a** | Pattern 5: Auth (72/28) |
| 17 | **Register Step 2** | `Linux  1024 - Register Girl step 2.png` | **3b** | Pattern 5: Auth (72/28) |
| 18 | **Register Step 3** | `Linux  1024 - Register Girl step 3.png` | **3c** | Pattern 5: Auth (72/28) |

---

## Auth Akış Sırası (Doğrulanmış)

```
Select Gender (13) → Devam Et → Login (15) → Giriş Yap veya Kayıt Ol
                                                          ↓
                                              Register Step 1 (16) → Devam Et
                                                          ↓
                                              Register Step 2 (17) → Devam Et
                                                          ↓
                                              Register Step 3 (18) → Kayıt Ol → Tamamlandı
```

> **⚠️ DİKKAT:** Select Gender İLK adımdır. Cinsiyet seçimi tema rengini belirler (female→pink, male→blue, neutral→default). Bu sıra değiştirilemez.

---

## ASCII Art — Auth Ekranları

### SELECT GENDER — PNG #13 (İLK ADIM)

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header: YOK (auth sayfası)                                                                     │
│ Footer: YOK (auth sayfası)                                                                     │
│                                                                                                 │
│ ┌── SOL ALAN (x:0-740, ~72%) ──────────────────────────────────────────────────────────────┐  │
│ │                                                                                            │  │
│ │   [Tam kaplama manzara fotoğrafı — sunset, okyanus, pembe tonları]                       │  │
│ │                                                                                            │  │
│ │   x:60 y:200                                                                              │  │
│ │   [CoreMusic Logo — Bickham Script Two, pembe/mor]                                        │  │
│ │   "Seni Tanıyalım"                                                                        │  │
│ │   Deneyimini sana özel hale getirmek için bir seçim yapman yeterli.                       │  │
│ │                                                                                            │  │
│ │                                                                                            │  │
│ │   x:60 y:400                                                                              │  │
│ │   "İyi ki Varsın Emanet!"                                                                 │  │
│ │   (Bickham Script Two, italik, dekoratif)                                                 │  │
│ │                                                                                            │  │
│ │                                                                                            │  │
│ │   x:60 y:520                                                                              │  │
│ │   "Müziğinle Hayat Buldum"                                                                │  │
│ │   "Hayatın rastlantılarla dolu..."                                                        │  │
│ │   (Bickham Script Two, italik, dekoratif)                                                 │  │
│ │                                                                                            │  │
│ └────────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                 │
│ ┌── SAĞ PANEL (x:740-1024, ~284px, glass) ─────────────────────────────────────────────────┐  │
│ │                                                                                            │  │
│ │   x:780 y:60                                                                              │  │
│ │   [Kadın ikonu — line art, beyaz, ~80×80px]                                               │  │
│ │   "Seni Tanıyalım"                                                                        │  │
│ │   "Müzik deneyimini sana özel hale getirelim"                                             │  │
│ │                                                                                            │  │
│ │   x:780 y:160                                                                             │  │
│ │   ┌──────────────────────────────────────────────────────────────┐                        │  │
│ │   │ [👩] Kız                                    C07 Gender Button │                        │  │
│ │   │        Temizlik, saf duygular               (~284×60px)     │                        │  │
│ │   │        Pembemsi renk tonları                                 │                        │  │
│ │   └──────────────────────────────────────────────────────────────┘                        │  │
│ │   ┌──────────────────────────────────────────────────────────────┐                        │  │
│ │   │ [👨] Erkek                                   C07 Gender Button│                        │  │
│ │   │        Güçlü, klasik tonlar                  (~284×60px)     │                        │  │
│ │   │        Mavimsi renk tonları                                 │                        │  │
│ │   └──────────────────────────────────────────────────────────────┘                        │  │
│ │   ┌──────────────────────────────────────────────────────────────┐                        │  │
│ │   │ [🤷] Cinsiyetimi belirtmek istemiyorum     C07 Gender Button│                        │  │
│ │   │        Nötr renk tonları                     (~284×60px)     │                        │  │
│ │   └──────────────────────────────────────────────────────────────┘                        │  │
│ │                                                                                            │  │
│ │   x:780 y:380                                                                             │  │
│ │   [Devam Et] butonu — Sadece sınır, pasif (seçim yapıldığında pembe olur)                │  │
│ │                                                                                            │  │
│ │   x:780 y:460                                                                             │  │
│ │   "Hayatın rastlantılarla dolu...                                                        │  │
│ │    senin gizli Müziğinle partala! ♥"                                                     │  │
│ │   (Bickham Script Two, dekoratif)                                                         │  │
│ │                                                                                            │  │
│ │   x:780 y:560                                                                             │  │
│ │   Devam ederek Gizlilik Politikamızı kabul etmiş olursunuz.                              │  │
│ │                                                                                            │  │
│ └────────────────────────────────────────────────────────────────────────────────────────────┘  │
│                                                                                                 │
│ Layout: 72/28 split — Sol manzara + Sağ glass panel                                            │
│ Glass panel: backdrop-filter: blur(20px) saturate(180%), yarı saydam                          │
│ Seçim YAPILMAMIŞ: "Devam Et" butonu pasif (sınır rengi, pembe değil)                          │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

### SELECT GENDER (SELECTED) — PNG #14

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ PNG #13 ile AYNI layout, tek farklar:                                                          │
│                                                                                            │  │
│   "Kız" butonu SEÇİLİ:                                                                       │
│   ┌══════════════════════════════════════════════════════════════════════════════════┐       │  │
│   ║ [👩] Kız ← pembe arka plan (rgba(255,79,216,0.2)), 2px solid pembe border       ║       │  │
│   ║      Temizlik, saf duygular                                                     ║       │  │
│   ║      Pembemsi renk tonları                                                      ║       │  │
│   ══════════════════════════════════════════════════════════════════════════════════       │  │
│                                                                                            │  │
│   "Devam Et" butonu: ARTIK PEMBE (full-width, C04)                                         │  │
│   ┌──────────────────────────────────────────────────────────────┐                          │  │
│   │                    Devam Et                                   │                          │  │
│   │                    (full-width, pembe, 56px)                 │                          │  │
│   └──────────────────────────────────────────────────────────────┘                          │  │
│                                                                                            │  │
│ Diğer但onlar: seçilmemiş (border: 1px solid rgba(255,255,255,0.15))                       │
│ "Devam Et" butonu: SEÇİMLE pembe olur, seçimsiz pasif                                    │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

### LOGIN — PNG #15

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Header: YOK                                                                                    │
│ Footer: YOK                                                                                    │
│                                                                                                 │
│ ┌── SOL ALAN (x:0-740, ~72%) ──┐  ┌── SAĞ PANEL (x:740-1024, ~284px, glass) ─────────────┐  │
│ │                                │  │                                                        │  │
│ │ [Manzara fotoğrafı]            │  │  [Kadın ikonu — line art, ~80×80px]                   │  │
│ │ (sunset, pembe tonları)        │  │  "Hoş Geldin"                                         │  │
│ │                                │  │  "Hesabına giriş yap, müziğin keyfini çıkar."         │  │
│ │ x:60 y:200                     │  │                                                        │  │
│ │ [CoreMusic Logo]               │  │  x:780 y:160                                           │  │
│ │ "Seni Tanıyalım"              │  │  E-posta, Telefon veya Kullanıcı Adı                  │  │
│ │                                │  │  ┌──────────────────────────────────────────────┐     │  │
│ │ x:60 y:400                     │  │  │ E-postanızı yazınız             (C06, 56px) │     │  │
│ │ "İyi ki Varsın Emanet!"       │  │  └──────────────────────────────────────────────┘     │  │
│ │                                │  │  Şifre                                               │  │
│ │ x:60 y:520                     │  │  ┌──────────────────────────────────────────────┐     │  │
│ │ "Müziğinle Hayat Buldum"      │  │ │ ●●●●●●●●                          (C06, 56px) │     │  │
│ │                                │  │  └──────────────────────────────────────────────┘     │  │
│ │                                │  │                                                        │  │
│ │                                │  │  ☐ Hatırla Beni          [Şifremi Unuttum]             │  │
│ │                                │  │                                                        │  │
│ │                                │  │  [Giriş Yap] (C04, pembe, full-width, 56px)            │  │
│ │                                │  │                                                        │  │
│ │                                │  │  ── veya şu şekilde devam et ──                        │  │
│ │                                │  │                                                        │  │
│ │                                │  │  [🍎][G][f]   ← Satır 1: Apple, Google, Facebook      │  │
│ │                                │  │  [💬][📷][🎵]  ← Satır 2: WhatsApp, Instagram, TikTok │  │
│ │                                │  │  [🎤]          ← Satır 3: Mikrofon                    │  │
│ │                                │  │  (C08 Social Login, 52×52px, gap:8px)                 │  │
│ │                                │  │                                                        │  │
│ │                                │  │  x:780 y:560                                           │  │
│ │                                │  │  Hesabın yok mu? [Kayıt Ol]                           │  │
│ └────────────────────────────────┘  └────────────────────────────────────────────────────────┘  │
│                                                                                                 │
│ Layout: 72/28 split — Same as Gender Select                                                    │
│ Auth sayfalarında header/footer YOK                                                            │
│ Left side: Sabit manzara + logo + dekoratif text (tüm auth sayfalarında aynı)                 │
│ Right side: Glass panel + form                                                                 │
│ Social Login: 2 satır × 3 sütun + 1 tek satır (Mikrofon)                                     │
│ Input'lar: pembe arka plan (focus durumunda)                                                  │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

### REGISTER STEP 1 — PNG #16

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ PNG #15 (Login) ile AYNI layout, sağ paneldeki form farklı:                                   │
│                                                                                                 │
│ Sol: Aynı manzara + logo + dekoratif text                                                      │
│ Sağ: [Kadın ikonu] "Hesap Oluştur"                                                            │
│      "CoreMusic ailesine katıl, müziğin keyfini çıkar"                                        │
│                                                                                                 │
│ x:780 y:160                                                                                    │
│ Kullanıcı Adı                                                                                  │
│ ┌──────────────────────────────────────────────────────────────┐                              │
│ │ Kullanıcı Adınız                             (C06, 56px)   │                              │
│ └──────────────────────────────────────────────────────────────┘                              │
│ E-posta                                                                                       │
│ ┌──────────────────────────────────────────────────────────────┐                              │
│ │ E-postanızı yazınız                          (C06, 56px)   │                              │
│ └──────────────────────────────────────────────────────────────┘                              │
│                                                                                                 │
│ [Devam Et] (C04, pembe, full-width, 56px)                                                     │
│                                                                                                 │
│ ── veya şu şekilde devam et ──                                                                │
│ [🍎][G][f]                                                                                   │
│ [💬][📷][🎵]                                                                                 │
│ [🎤]                                                                                          │
│ Hesabın yok mu? [Kayıt Ol]                                                                    │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

### REGISTER STEP 2 — PNG #17

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Sol: Aynı manzara + logo + dekoratif text                                                      │
│ Sağ: [Kadın ikonu] "Hesap Oluştur"                                                            │
│                                                                                                 │
│ x:780 y:160                                                                                    │
│ Şifre                                                                                          │
│ ┌──────────────────────────────────────────────────────────────┐                              │
│ │ E-postanızı yazınız                          (C06, 56px)   │                              │
│ └──────────────────────────────────────────────────────────────┘                              │
│ Şifre Tekrar                                                                                  │
│ ┌──────────────────────────────────────────────────────────────┐                              │
│ │ E-postanızı yazınız                          (C06, 56px)   │                              │
│ └──────────────────────────────────────────────────────────────┘                              │
│                                                                                                 │
│ [Devam Et] (C04, pembe, full-width, 56px)                                                     │
│                                                                                                 │
│ ── veya şu şekilde devam et ──                                                                │
│ [🍎][G][f]                                                                                   │
│ [💬][📷][🎵]                                                                                 │
│ [🎤]                                                                                          │
│ Hesabın yok mu? [Kayıt Ol]                                                                    │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

### REGISTER STEP 3 — PNG #18

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│ Sol: Aynı manzara + logo + dekoratif text                                                      │
│ Sağ: [Kadın ikonu] "Hesap Oluştur"                                                            │
│                                                                                                 │
│ x:780 y:160                                                                                    │
│ Telefon                                                                                         │
│ ┌──────────────────────────────────────────────────────────────┐                              │
│ │ E-postanızı yazınız                          (C06, 56px)   │                              │
│ └──────────────────────────────────────────────────────────────┘                              │
│                                                                                                 │
│ ☐ Kullanım şartlarını ve Gizlilik Politikasını kabul ediyorum.                               │
│                                                                                                 │
│ [Kayıt Ol] (C04, pembe, full-width, 56px)                                                     │
│                                                                                                 │
│ ── veya şu şekilde devam et ──                                                                │
│ [🍎][G][f]                                                                                   │
│ [💬][📷][🎵]                                                                                 │
│ [🎤]                                                                                          │
│ Hesabın yok mu? [Kayıt Ol]                                                                    │
│                                                                                                 │
│ KVKK checkbox zorunlu — seçilmemişse "Kayıt Ol" pasif                                        │
└─────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## Auth Akış Diyagramı

```
┌──────────────┐     ┌──────────────┐     ┌──────────────┐
│ Select Gender │────→│    Login      │────→│ Register S1  │
│   (PNG #13)   │     │  (PNG #15)   │     │  (PNG #16)   │
│   İLK ADIM    │     │              │     │ Kullanıcı Adı│
│  3 seçenek:   │     │ Email+Şifre  │     │ + E-posta    │
│  Kız/Erkek/   │     │ + Sosyal     │     │              │
│  Diğer        │     │ + Kayıt Ol   │     │ [Devam Et]   │
└──────────────┘     └──────────────┘     └──────┬───────┘
                                                  │
                                                  ↓
                     ┌──────────────┐     ┌──────────────┐
                     │ Register S3  │←────│ Register S2  │
                     │  (PNG #18)   │     │  (PNG #17)   │
                     │ Telefon+KVKK │     │ Şifre+Tekrar │
                     │              │     │              │
                     │ [Kayıt Ol]   │     │ [Devam Et]   │
                     └──────────────┘     └──────────────┘

Tema Etkisi: Select Gender seçimi → data-gender attribute → CSS custom properties
female → #ff4fd8 (pembe)
male → #4f9fff (mavi)
neutral → #a0a0b0 (nötr)
```

---

## İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[00-mockup-index]] | Ana indeks |
| [[02-home-screens]] | Home ekranları |
| [[03-music-screens]] | Music ekranları |
| [[04-player-screens]] | Player ekranları |
| [[05-filemanager-screens]] | FileManager ekranları |
| [[06-settings-screens]] | Settings ekranları |
| [[07-reference-tables]] | Referans tabloları |

---

*Auth Screen Mockups v1.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-19*
*Mode: Red Team · Human Mode · Truth Mode*
