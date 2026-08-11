---
title: CoreMusic — Auth Flow: Register (Detaylı, 3 Adım)
date: 2026-08-11
version: 2.0.0
platform: home-1024 (Linux Embedded RPi5, 1024×600)
author: Senior Frontend Architect (50+ yıl deneyim)
references:
  - [[screens/A-auth/register-step1]]
  - [[screens/A-auth/register-step2-3]]
  - [[screens/A-auth/gender-select]]
  - [[screens/00-ascii-art-views]] §15-17
  - [[01-component-inventory]] C04, C06, C07, C08
  - [[ADR-010-csrf-protection-strategy]]
  - [[ADR-022-database-hardened-security]]
---

# Auth Flow: Register — Detaylı Akış Analizi

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

---

## 1. GENEL AKIŞ DİYAGRAMI

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        REGISTER AKIŞ DİYAGRAMI                              │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐                  │
│  │ Select Gender│ →  │ Register 1   │ →  │ Register 2   │                  │
│  │   (1. adım)  │    │ Kullanıcı+   │    │ Şifre+       │                  │
│  │              │    │ Email        │    │ Şifre Tekrar │                  │
│  └──────────────┘    └──────────────┘    └──────────────┘                  │
│                                                     │                       │
│                                              ┌──────┴──────┐               │
│                                              │              │               │
│                                    Eşleşme   │    Eşleşmiyor│               │
│                                    kontrolü  │              │               │
│                                              │              │               │
│                                         ┌────▼────┐  ┌─────▼─────┐        │
│                                         │Register │  │ Hata      │        │
│                                         │ 3       │  │ Mesajı    │        │
│                                         └────┬────┘  └───────────┘        │
│                                              │                             │
│                                              ▼                             │
│                                         ┌─────────┐                       │
│                                         │ Kayıt Ol│                       │
│                                         │ Telefon  │                       │
│                                         │ + KVKK   │                       │
│                                         └────┬────┘                       │
│                                              │                             │
│                                         ┌────▼────┐  ┌───────────┐        │
│                                         │ Başarılı │  │ Başarısız │        │
│                                         └────┬────┘  └─────┬─────┘        │
│                                              │              │               │
│                                         ┌────▼────┐  ┌─────▼─────┐        │
│                                         │ Login   │  │ Hata      │        │
│                                         │ Sayfası │  │ Mesajı    │        │
│                                         └─────────┘  └───────────┘        │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. ADIM 1: SELECT GENDER (PNG Layout)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                          x:1024        │
│ y:0 ┌───────────────────────────────────────────────────────────────────┐   │
│     │  ┌── SOL ALAN (~800px) ──────────┐  ┌── SAĞ (~224px) ─────────┐ │   │
│     │  │ [CoreMusic Logo]               │  │  [女神 ikonu 60×60px]    │ │   │
│     │  │ Seni Tanıyalım                 │  │  Seni Tanıyalım          │ │   │
│     │  │ Deneyimini sana özel           │  │  Müzik deneyimini sana    │ │   │
│     │  │ hale getirmek için             │  │  özel getirelim          │ │   │
│     │  │ bir seçim yapman yeterli.      │  │                          │ │   │
│     │  │                                │  │  ┌────────────────────┐  │ │   │
│     │  │ [Tam kaplama fotoğraf]         │  │  │ 👩 Kız              │  │ │   │
│     │  │ (pembe, sunset, fantasy)       │  │  │   Temizlik, saf    │  │ │   │
│     │  │                                │  │  │   Pembemsi tonlar  │  │ │   │
│     │  │                                │  │  └────────────────────┘  │ │   │
│     │  │                                │  │                          │ │   │
│     │  │                                │  │  ┌────────────────────┐  │ │   │
│     │  │                                │  │  │ 👨 Erkek            │  │ │   │
│     │  │                                │  │  │   Güçlü, klasik    │  │ │   │
│     │  │                                │  │  │   Mavimsi tonlar   │  │ │   │
│     │  │                                │  │  └────────────────────┘  │ │   │
│     │  │                                │  │                          │ │   │
│     │  │                                │  │  ┌────────────────────┐  │ │   │
│     │  │                                │  │  │ 🤷 Diğer            │  │ │   │
│     │  │                                │  │  │   Nötr tonlar      │  │ │   │
│     │  │                                │  │  └────────────────────┘  │ │   │
│     │  │                                │  │                          │ │   │
│     │  │                                │  │  [Devam Et] (C04, pembe) │ │   │
│     │  │                                │  │                          │ │   │
│     │  │                                │  │  "Hayatın ritmini..."    │ │   │
│     │  │                                │  │  (Bickham, italik, pembe)│ │   │
│     │  │                                │  │                          │ │   │
│     │  │                                │  │  Devam ederek ·Gizlilik  │ │   │
│     │  │                                │  │  Politikamı kabul etmiş  │ │   │
│     │  │                                │  │  olursunuz. (9px, muted) │ │   │
│     │  └────────────────────────────────┘  └──────────────────────────┘   │
│ y:600└───────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────────┘
```

### Gender Butonları (C07) Detayları

| Özellik | Kız | Erkek | Diğer |
|---------|-----|-------|-------|
| İkon | 👩 | 👨 | 🤷 |
| Başlık | Kız | Erkek | Cinsiyetimi belirtmek istemiyorum |
| Alt metin | Temizlik, saf duygular · Pembemsi renk tonları | Güçlü, klasik tonlar · Mavimsi renk tonları | Nötr renk tonları |
| Tema | female→pink | male→blue | neutral→default |
| Accent | #ff4fd8 | #4f9fff | #a0a0b0 |
| Seçili bg | rgba(255,79,216,0.2) | rgba(79,159,255,0.2) | rgba(160,160,176,0.2) |
| Seçili border | 2px solid #ff4fd8 | 2px solid #4f9fff | 2px solid #a0a0b0 |

### DAVRANIŞ

```
Sayfa yüklenir
  → 3 buton göster (hiçbiri seçili değil)
  → "Devam Et" butonu devre dışı (gri, opacity:0.5)

Kullanıcı bir butona tıklar
  → Seçili buton pembe/mavi/nötr vurgu alır
  → Diğer butonlar default'a döner
  → "Devam Et" butonu aktif olur (pembe)
  → Tema rengi anında değişir (CSS custom properties)
  → Cookie ayarlanır: theme_gender=female|male|neutral

Kullanıcı "Devam Et"'e tıklar
  → Seçim kaydedilir
  → Register Step 1 sayfasına yönlendirme
```

---

## 3. ADIM 2: REGISTER STEP 1 (PNG Layout)

```
┌── SAĞ PANEL (~224px) ─────────────────────────────────────┐
│  [女神 ikonu 60×60px]                                       │
│                                                              │
│  Hesap Oluştur                                              │
│  CoreMusic ailesine katıl,                                   │
│  müziğin keyfini çıkar                                       │
│                                                              │
│  Kullanıcı Adı                                               │
│  ┌───────────────────────────────────┐                      │
│  │ (C06 input, 56px)                 │                      │
│  └───────────────────────────────────┘                      │
│                                                              │
│  E-posta                                                     │
│  ┌───────────────────────────────────┐                      │
│  │ (C06 input, 56px)                 │                      │
│  └───────────────────────────────────┘                      │
│                                                              │
│  [Devam Et] (C04, pembe, full-width, 56px)                  │
│                                                              │
│  ── veya alternatif ile devam et ──                         │
│                                                              │
│  [🍎][G][f][💬][📷][🎵][🎤] (C08, 52×52px)                  │
│                                                              │
│  Hesabın yok mu?  Kayıt Ol                                  │
│                                                              │
│ Sol alan: Aynı Select Gender fotoğrafı                      │
└──────────────────────────────────────────────────────────────┘
```

### Validasyon Kuralları

| Alan | Kural | Hata Mesajı |
|------|-------|-------------|
| Kullanıcı Adı | Boş olamaz | "Kullanıcı adı gereklidir" |
| Kullanıcı Adı | 3-50 karakter | "3-50 karakter arası olmalı" |
| Kullanıcı Adı | Sadece harf, rakam, _ | "Sadece harf, rakam ve _ kullanabilirsiniz" |
| Kullanıcı Adı | Unique | "Bu kullanıcı adı zaten alınmış" |
| E-posta | Boş olamaz | "E-posta gereklidir" |
| E-posta | Geçerli format | "Geçerli bir e-posta giriniz" |
| E-posta | Unique | "Bu e-posta zaten kayıtlı" |

### DAVRANIŞ

```
Form yüklenir
  → Kullanıcı Adı input'a otomatik focus
  → Her iki alan boş
  → "Devam Et" butonu aktif (engeli yok — sadece boş kontrolü)

Kullanıcı "Devam Et"'e tıklar
  → Client-side validasyon
  → Hata varsa: hata mesajı göster, input'a focus
  → Hata yoksa: Backend'e gönder
    → POST /auth/check-username (unique kontrolü)
    → POST /auth/check-email (unique kontrolü)
    → Her ikisi de unique ise → Register Step 2'ye geç
```

---

## 4. ADIM 3: REGISTER STEP 2 (PNG Layout)

```
┌── SAĞ PANEL (~224px) ─────────────────────────────────────┐
│  [女神 ikonu]                                               │
│                                                              │
│  Hesap Oluştur                                              │
│                                                              │
│  Şifre                                                       │
│  ┌───────────────────────────────────┐                      │
│  │ (C06, password, 56px)             │                      │
│  └───────────────────────────────────┘                      │
│                                                              │
│  Şifre Tekrar                                                │
│  ┌───────────────────────────────────┐                      │
│  │ (C06, password, 56px)             │                      │
│  └───────────────────────────────────┘                      │
│                                                              │
│  [Devam Et] (C04, pembe, full-width, 56px)                  │
│                                                              │
│  [🍎][G][f][💬][📷][🎵][🎤]                                  │
│  Hesabın yok mu?  Kayıt Ol                                  │
└──────────────────────────────────────────────────────────────┘
```

### Validasyon Kuralları

| Alan | Kural | Hata Mesajı |
|------|-------|-------------|
| Şifre | Boş olamaz | "Şifre gereklidir" |
| Şifre | Min 8 karakter | "Şifre en az 8 karakter olmalı" |
| Şifre | 1 büyük harf | "En az 1 büyük harf içermeli" |
| Şifre | 1 rakam | "En az 1 rakam içermeli" |
| Şifre Tekrar | Eşleşme | "Şifreler eşleşmiyor" |

### Şifre Güçlülük Göstergesi

```
┌─────────────────────────────────────┐
│ Şifre: ●●●●●●●●                     │
│ ┌───┬───┬───┬───┐                   │
│ │   │   │   │   │  ← 4 segment     │
│ │ K │ O │ İ │ G │  ← Kuvvetli      │
│ └───┴───┴───┴───┘                   │
│ Zayıf → Orta → Güçlü → Kuvvetli    │
└─────────────────────────────────────┘
Renk: kırmızı → turuncu → sarı → yeşil
```

---

## 5. ADIM 4: REGISTER STEP 3 (PNG Layout)

```
┌── SAĞ PANEL (~224px) ─────────────────────────────────────┐
│  [女神 ikonu]                                               │
│                                                              │
│  Hesap Oluştur                                              │
│                                                              │
│  Telefon                                                     │
│  ┌───────────────────────────────────┐                      │
│  │ (C06, tel, 56px)                  │                      │
│  └───────────────────────────────────┘                      │
│                                                              │
│  ☑ Gizlilik Politikası ve Kullanım şartlarını               │
│    kabul ediyorum  (checkbox, 11px)                          │
│                                                              │
│  [Kayıt Ol] (C04, pembe, full-width, 56px)                  │
│                                                              │
│  [🍎][G][f][💬][📷][🎵][🎤]                                  │
│  Hesabın yok mu?  Kayıt Ol                                  │
└──────────────────────────────────────────────────────────────┘
```

### Validasyon Kuralları

| Alan | Kural | Hata Mesajı |
|------|-------|-------------|
| Telefon | Boş olamaz | "Telefon numarası gereklidir" |
| Telefon | +90 formatı | "+90 XXX XXX XX XX formatında olmalı" |
| KVKK | Onay zorunlu | "Devam etmek için şartları kabul etmelisiniz" |

---

## 6. TAMAMLANMA AKIŞI

```
Kullanıcı "Kayıt Ol"'a tıklar
  → Buton loading durumuna geçer
  → Backend: POST /auth/register
    → Request: { username, email, password, phone, gender, csrf_token }
    → Response: { success, user_id }
  → Başarılı:
    → Cookie ayarla: theme_gender, welcome_shown
    → Ana sayfaya yönlendirme (/)
    → Hoş geldin popup'ı gösterilir
  → Başarısız:
    → Hata mesajı göster
    → İlgili adıma geri dön
```

---

## 7. BACKEND GEREKSİNİMLERİ

| Endpoint | Method | Request | Response |
|----------|--------|---------|----------|
| `/auth/check-username` | GET | `?username=xxx` | `{ available: bool }` |
| `/auth/check-email` | GET | `?email=xxx` | `{ available: bool }` |
| `/auth/register` | POST | `{ username, email, password, phone, gender }` | `{ success, user_id }` |
| `/auth/verify-email` | GET | `?token=xxx` | `{ success }` |

### Şifre Hashing

| Parametre | Değer | ADR |
|-----------|-------|-----|
| Algoritma | Argon2id | ADR-022 |
| Memory | 64MB | ADR-022 |
| Time | 4 iterations | ADR-022 |
| Threads | 2 | ADR-022 |

---

## 8. ERİŞİLEBİLİRLİK (WCAG 2.2 AA)

| Kriter | Step 1 | Step 2 | Step 3 |
|--------|--------|--------|--------|
| Touch target (input) | ✅ 56px | ✅ 56px | ✅ 56px |
| Touch target (buton) | ✅ 56px | ✅ 56px | ✅ 56px |
| Touch target (checkbox) | — | — | ⚠️ ~16px |
| Focus indicator | ✅ | ✅ | ✅ |
| Error messaging | ✅ | ✅ | ✅ |
| Password strength | — | ✅ | — |
| ARIA | ⚠️ eksik | ⚠️ eksik | ⚠️ eksik |

---

## 9. GÜVENLİK NOTLARI

| Kural | Değer |
|-------|-------|
| CSRF token | `csrf_token` |
| Password hashing | Argon2id |
| Email verification | Zorunlu |
| Rate limit | 10 kayıt/dakika |
| Brute force lock | 5 başarısız → 15dk kilit |

---

## 10. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[screens/A-auth/register-step1]] | Step 1 spec |
| [[screens/A-auth/register-step2-3]] | Step 2-3 spec |
| [[screens/A-auth/gender-select]] | Gender spec |
| [[screens/00-ascii-art-views]] §13-17 | ASCII art'lar |
| [[01-component-inventory]] C04, C06, C07, C08 | Bileşenler |
| [[flow/auth/04-select-gender]] | Gender akışı |
| [[flow/auth/01-login]] | Login akışı |

---

*Register Flow v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
