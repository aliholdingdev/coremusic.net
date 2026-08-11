---
title: CoreMusic — Auth Flow: Forgot Password (Detaylı)
date: 2026-08-11
version: 2.0.0
platform: home-1024 (Linux Embedded RPi5, 1024×600)
author: Senior Frontend Architect (50+ yıl deneyim)
references:
  - [[screens/A-auth/login]]
  - [[01-component-inventory]] C06, C04
  - [[ADR-011-session-management]]
  - [[ADR-022-database-hardened-security]]
---

# Auth Flow: Forgot Password — Detaylı Akış Analizi

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

---

## 1. GENEL AKIŞ

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                      FORGOT PASSWORD AKIŞI                                   │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐                  │
│  │    Login     │ →  │ Forgot       │ →  │ Email        │                  │
│  │   Sayfası   │    │ Password     │    │ Gönderimi    │                  │
│  │ "Şifremi     │    │   Formu      │    │              │                  │
│  │  Unuttum"    │    │              │    │              │                  │
│  └──────────────┘    └──────────────┘    └──────────────┘                  │
│                                                     │                       │
│                                              ┌──────┴──────┐               │
│                                              │              │               │
│                                         ┌────▼────┐  ┌─────▼─────┐        │
│                                         │ Email   │  │ Email     │        │
│                                         │ Bulundu │  │ Bulunamadı│        │
│                                         └────┬────┘  └─────┬─────┘        │
│                                              │              │               │
│                                         ┌────▼────┐  ┌─────▼─────┐        │
│                                         │ Link    │  │ "Bu email  │        │
│                                         │ Gönder  │  │  kayıtlı   │        │
│                                         └────┬────┘  │  değil"    │        │
│                                              │       └───────────┘        │
│                                         ┌────▼────┐                        │
│                                         │ Email   │                        │
│                                         │ Linki   │                        │
│                                         │ Tıklama │                        │
│                                         └────┬────┘                        │
│                                              │                             │
│                                         ┌────▼────┐  ┌───────────┐        │
│                                         │ Yeni    │  │ Link      │        │
│                                         │ Şifre   │  │ Süresi    │        │
│                                         │ Formu   │  │ Dolmuş    │        │
│                                         └────┬────┘  └─────┬─────┘        │
│                                              │              │               │
│                                         ┌────▼────┐  ┌─────▼─────┐        │
│                                         │ Şifre   │  │ Yeni link  │        │
│                                         │ Güncel- │  │ iste       │        │
│                                         │ lendi   │  │            │        │
│                                         └────┬────┘  └───────────┘        │
│                                              │                             │
│                                         ┌────▼────┐                        │
│                                         │ Login   │                        │
│                                         │ Sayfası │                        │
│                                         └─────────┘                        │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. SAYFA YAPISI

### 2.1 — Forgot Password Formu

```
┌── SAĞ PANEL (~224px) ─────────────────────────────────────┐
│  [女神 ikonu]                                               │
│                                                              │
│  Şifre Sıfırla                                               │
│  E-posta adresinize sıfırlama                                │
│  linki gönderelim                                            │
│                                                              │
│  E-posta                                                     │
│  ┌───────────────────────────────────┐                      │
│  │ (C06 input, 56px)                 │                      │
│  └───────────────────────────────────┘                      │
│                                                              │
│  [Gönder] (C04, pembe, full-width, 56px)                    │
│                                                              │
│  ← Giriş sayfasına dön                                      │
│                                                              │
│ Sol alan: Aynı auth arka planı                              │
└──────────────────────────────────────────────────────────────┘
```

### 2.2 — Yeni Şifre Formu (Email linkinden sonra)

```
┌── SAĞ PANEL (~224px) ─────────────────────────────────────┐
│  [女神 ikonu]                                               │
│                                                              │
│  Yeni Şifre Belirle                                          │
│                                                              │
│  Yeni Şifre                                                   │
│  ┌───────────────────────────────────┐                      │
│  │ (C06, password, 56px)             │                      │
│  └───────────────────────────────────┘                      │
│                                                              │
│  Şifre Tekrar                                                │
│  ┌───────────────────────────────────┐                      │
│  │ (C06, password, 56px)             │                      │
│  └───────────────────────────────────┘                      │
│                                                              │
│  Şifre güçlülük göstergesi (4 segment)                      │
│                                                              │
│  [Kaydet] (C04, pembe, full-width, 56px)                    │
└──────────────────────────────────────────────────────────────┘
```

---

## 3. DAVRANIŞ DETAYLARI

### 3.1 — "Şifremi Unuttum" Tıklama

```
Login sayfasında "Şifremi Unuttum" linki tıklanır
  → /auth/forgot-password sayfasına yönlendirme
  → Form gösterilir (sadece email alanı)
  → E-posta input'a otomatik focus
```

### 3.2 — Email Gönderimi

```
Kullanıcı email girer → "Gönder" tıklar
  → Client-side validasyon (boş mu, format doğru mu?)
  → Backend: POST /auth/forgot-password
    → Request: { email, csrf_token }
    → Backend kontrolü:
      → Email veritabanında var mı?
        → EVET: Sıfırlama token'ı oluştur (crypto.randomUUID)
        → Token'ı DB'ye kaydet (15 dakika geçerli)
        → Email'e sıfırlama linki gönder
        → "Sıfırlama linki email adresinize gönderildi" mesajı
        → Login sayfasına yönlendirme
      → HAYIR: "Bu email adresi kayıtlı değil" mesajı
        → (Güvenlik için: "Bu email kayıtlı değil" demek YASAK — "Eğer bu email kayıtlıysa bir link gönderilmiştir" denmeli)
```

### 3.3 — Email Linki Tıklama

```
Kullanıcı email'deki linke tıklar
  → /auth/reset-password?token=xxx sayfasına yönlendirme
  → Backend: Token doğrulama
    → Token geçerli mi? (DB'de var mı, süresi dolmuş mu?)
      → EVET: Yeni şifre formu gösterilir
      → HAYIR: "Link süresi dolmuş" mesajı
        → "Yeni link iste" butonu
```

### 3.4 — Şifre Sıfırlama

```
Kullanıcı yeni şifre girer → "Kaydet" tıklar
  → Client-side validasyon
    → Şifre güçlülük kontrolü
    → Eşleşme kontrolü
  → Backend: POST /auth/reset-password
    → Request: { token, password, csrf_token }
    → Token doğrulama
    → Şifre hashleme (Argon2id)
    → DB'de güncelleme
    → Tüm mevcut session'ları iptal et (güvenlik)
    → "Şifreniz güncellendi" mesajı
    → Login sayfasına yönlendirme
```

---

## 4. GÜVENLİK NOTLARI

| Kural | Değer |
|-------|-------|
| Token ömrü | 15 dakika |
| Token tipi | crypto.randomUUID (36 karakter) |
| Token hash | SHA-256 ile hashlenmiş olarak DB'de saklanır |
| Rate limit | 3 istek/saat (email başına) |
| Session iptali | Şifre sıfırlandığında tüm session'lar silinir |
| CSRF | `csrf_token` zorunlu |
| Email bilgisi sızıntısı | "Bu email kayıtlı değil" yerine "Link gönderildi" |

---

## 5. HATA DURUMLARI

| Hata | Mesaj | Davranış |
|------|-------|----------|
| Email bulunamadı | "Eğer bu email kayıtlıysa bir link gönderilmiştir" | Login sayfasında kal |
| Token süresi dolmuş | "Bu link süresi dolmuş. Yeni bir link isteyebilirsiniz." | "Yeni link iste" butonu |
| Token geçersiz | "Geçersiz link. Lütfen yeni bir link isteyin." | Forgot password formu |
| Şifre eşleşmiyor | "Şifreler eşleşmiyor" | Hata mesajı |
| Şifre zayıf | "Şifre en az 8 karakter, 1 büyük harf, 1 rakam içermeli" | Hata mesajı |
| Rate limit | "Çok fazla deneme. 15 dakika sonra tekrar deneyin." | Buton devre dışı |

---

## 6. ERİŞİLEBİLİRLİK

| Kriter | Durum |
|--------|-------|
| Touch target (input) | ✅ 56px |
| Touch target (buton) | ✅ 56px |
| Focus indicator | ✅ |
| Error messaging | ✅ (input altında) |
| ARIA | ⚠️ eksik |
| Screen reader | ⚠️ eksik |

---

## 7. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[screens/A-auth/login]] | Login screen spec |
| [[01-component-inventory]] C06, C04 | Bileşenler |
| [[flow/auth/01-login]] | Login akışı |
| [[ADR-011-session-management]] | Session yönetimi |
| [[ADR-022-database-hardened-security]] | Şifreleme |

---

*Forgot Password Flow v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
