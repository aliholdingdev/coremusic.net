---
title: CoreMusic — Auth Flow: Login (Detaylı)
date: 2026-08-11
version: 2.0.0
platform: home-1024 (Linux Embedded RPi5, 1024×600)
author: Senior Frontend Architect (50+ yıl deneyim)
references:
  - [[screens/A-auth/login]]
  - [[screens/00-ascii-art-views]] §14
  - [[screens/_layout-patterns/05-auth-screen]]
  - [[01-component-inventory]] C06, C08, C04
  - [[ADR-010-csrf-protection-strategy]]
  - [[ADR-011-session-management]]
  - [[ADR-043-auth-subdomain-consolidation]]
---

# Auth Flow: Login — Detaylı Akış Analizi

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

> **⚠️ Touch-First:** Fare yok, hover yok, klavye yok. Parmak ile etkileşim. Minimum touch target 48px.

---

## 1. GENEL AKIŞ DİYAGRAMI

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                           LOGIN AKIŞ DİYAGRAMI                              │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐                  │
│  │ İlk Giriş    │ →  │ Select Gender│ →  │    Login     │                  │
│  │ (cookie yok) │    │   (1. adım)  │    │   (2. adım)  │                  │
│  └──────────────┘    └──────────────┘    └──────────────┘                  │
│                                                     │                       │
│                                              ┌──────┴──────┐               │
│                                              │              │               │
│                                         ┌────▼────┐  ┌─────▼─────┐        │
│                                         │ Başarılı │  │ Başarısız │        │
│                                         └────┬────┘  └─────┬─────┘        │
│                                              │              │               │
│                                         ┌────▼────┐  ┌─────▼─────┐        │
│                                         │ Ana     │  │ Hata      │        │
│                                         │ Sayfa   │  │ Mesajı    │        │
│                                         └─────────┘  └───────────┘        │
│                                                                             │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐                  │
│  │ Var olan     │ →  │    Login     │ →  │ Session      │                  │
│  │ session      │    │ (doğrudan)   │    │ doğrulama    │                  │
│  └──────────────┘    └──────────────┘    └──────────────┘                  │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. SAYFA YAPISI (PNG LAYOUT)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                          x:1024        │
│ y:0 ┌───────────────────────────────────────────────────────────────────┐   │
│     │                                                                   │   │
│     │  ┌── SOL ALAN (~800px, %78) ──────┐  ┌── SAĞ (~224px, %22) ──┐ │   │
│     │  │                                   │  │  [女神 ikonu]          │ │   │
│     │  │  [CoreMusic Logo]                 │  │                       │ │   │
│     │  │  Seni Tanıyalım                   │  │  Hoş Geldin           │ │   │
│     │  │  Sisteme                           │  │  Hesabına giriş yap,  │ │   │
│     │  │  milyonlarca şarkı...             │  │  müziğin keyfini çıkar│ │   │
│     │  │                                   │  │                       │ │   │
│     │  │  [Tam kaplama fotoğraf]           │  │  E-posta, Telefon     │ │   │
│     │  │  (gender'a göre renk değişir)     │  │  veya Kullanıcı Adı  │ │   │
│     │  │                                   │  │  ┌─────────────────┐  │ │   │
│     │  │  y:100                            │  │  │ (C06, 56px)     │  │ │   │
│     │  │  [Logo 60×40px]                   │  │  └─────────────────┘  │ │   │
│     │  │                                   │  │  Şifre                │ │   │
│     │  │  y:160                            │  │  ┌─────────────────┐  │ │   │
│     │  │  "Seni Tanıyalım"                 │  │  │ ●●●●●● (56px)   │  │ │   │
│     │  │  (24px, Bickham Script)           │  │  └─────────────────┘  │ │   │
│     │  │                                   │  │                       │ │   │
│     │  │  y:190                            │  │  ☑ Beni Hatırla      │ │   │
│     │  │  "Sisteme milyonlarca şarkı..."  │  │     Şifremi Unuttum  │ │   │
│     │  │  (12px, Arima, muted)             │  │                       │ │   │
│     │  │                                   │  │  [Giriş Yap]          │ │   │
│     │  │                                   │  │  (C04, pembe, 56px)  │ │   │
│     │  │                                   │  │                       │ │   │
│     │  │                                   │  │  ── veya ──          │ │   │
│     │  │                                   │  │                       │ │   │
│     │  │                                   │  │  [🍎][G][f]           │ │   │
│     │  │                                   │  │  [💬][📷][🎵]         │ │   │
│     │  │                                   │  │  [🎤]                │ │   │
│     │  │                                   │  │  (C08, 52×52px each) │ │   │
│     │  │                                   │  │                       │ │   │
│     │  │                                   │  │  Hesabın yok mu?     │ │   │
│     │  │                                   │  │  Kayıt Ol            │ │   │
│     │  └───────────────────────────────────┘  └───────────────────────┘   │
│ y:600└───────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. SAĞ PANEL FORM DETAYLARI

### 3.1 —女神 (Avatar/Logo)

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| Boyut | ~60×60px | PNG ölçümü |
| Konum | Sağ panel üst, orta hizalı |
| Stil | Beyaz çizim, saydam |
| CSS | `.auth-panel__avatar` |

### 3.2 — Başlık

| Özellik | Değer | Token |
|---------|-------|-------|
| Metin | "Hoş Geldin" | — |
| Font | Bickham Script Two | `--font-logo` |
| Boyut | 20px | `--text-xl` |
| Renk | Beyaz | `--color-white` |
| Hizalama | Ortada |

### 3.3 — Alt Başlık

| Özellik | Değer | Token |
|---------|-------|-------|
| Metin | "Hesabına giriş yap, müziğin keyfini çıkar" | — |
| Font | Arima | `--font-body` |
| Boyut | 11px | `--text-xs` |
| Renk | `rgba(255,255,255,0.6)` | `--color-text-muted` |
| Hizalama | Ortada |

### 3.4 — Email Input (C06)

| Özellik | Değer | Token |
|---------|-------|-------|
| Label | "E-posta, Telefon veya Kullanıcı Adı" | — |
| Label boyutu | 11px | `--text-xs` |
| Label rengi | `rgba(255,255,255,0.6)` | `--color-text-muted` |
| Input yüksekliği | 56px | `--input-h` |
| Genişlik | Tam genişlik (~180px) | — |
| Background | `rgba(255,255,255,0.1)` | `--glass-bg` |
| Border | 1px solid `rgba(255,255,255,0.2)` | `--border-subtle` |
| Border-radius | 8px | `--radius-md` |
| Text | #ffffff | `--color-white` |
| Placeholder | "E-postanızı yazınız" | — |
| Focus border | `var(--theme-primary)` | #ff4fd8 |

### 3.5 — Password Input (C06)

| Özellik | Değer |
|---------|-------|
| Label | "Şifre" |
| Type | password |
| Yükseklik | 56px |
| Diğer | Email input ile aynı |

### 3.6 — Remember Me + Forgot Password

| Özellik | Değer |
|---------|-------|
| Checkbox | ☑ Beni Hatırla (12px) |
| Checkbox boyutu | 16×16px |
| Checkbox renk | `var(--theme-primary)` |
| Link | Şifremi Unuttum |
| Link rengi | `var(--theme-primary)` |
| Link boyutu | 11px |
| Layout | Flex, space-between |

### 3.7 — "Giriş Yap" Butonu (C04)

| Özellik | Değer | Token |
|---------|-------|-------|
| Metin | "Giriş Yap" | — |
| Yükseklik | 56px | `--btn-h` |
| Genişlik | Tam genişlik | — |
| Background | `var(--theme-primary)` | #ff4fd8 |
| Text | #ffffff | — |
| Font | 14px, 600 | `--text-base`, `--font-semibold` |
| Border-radius | 8px | `--radius-md` |
| Border | none | — |
| Margin-top | 12px | `--space-3` |

### 3.8 — Divider

| Özellik | Değer |
|---------|-------|
| Metin | "── veya alternatif ile devam et ──" |
| Font | 11px |
| Renk | `rgba(255,255,255,0.4)` |
| Hizalama | Ortada |
| Padding | 8px 0 |

### 3.9 — Social Buttons (C08)

| Özellik | Değer |
|---------|-------|
| Düzen | 2 satır × 3-4 sütun |
| Boyut | 52×52px |
| Border-radius | 12px |
| Gap | 8px |
| Hizalama | Ortada |

**Satır 1:** 🍎 Apple (#000), G Google (#fff), f Facebook (#1877F2)
**Satır 2:** 💬 WhatsApp (#25D366), 📷 Instagram (#E4405F), 🎵 TikTok (#000)
**Satır 3:** 🎤 Mikrofon (var(--theme-primary))

### 3.10 — Alt Link

| Özellik | Değer |
|---------|-------|
| Metin | "Hesabın yok mu?  Kayıt Ol" |
| Font | 11px |
| Renk | "Hesabın yok mu?" = `rgba(255,255,255,0.4)`, "Kayıt Ol" = `var(--theme-primary)` |
| Hizalama | Ortada |
| Padding | 12px 0 |

---

## 4. SOL ALAN DETAYLARI

| Özellik | Değer |
|---------|-------|
| Genişlik | ~800px (%78) |
| Arka plan | Tam kaplama fotoğraf + gradient |
| Logo | Sol üst, ~60×40px |
| Başlık | "Seni Tanıyalım" (24px, Bickham Script) |
| Alt metin | "Sisteme milyonlarca şarkı..." (12px, Arima, muted) |
| Arka plan fotoğrafı | gender'a göre değişir (female=pembe, male=mavi) |

---

## 5. DAVRANIŞ DETAYLARI

### 5.1 — Sayfa Yükleme

```
Sayfa yüklenir
  → Sağ panel glass efekti ile fade-in (300ms)
  → Sol alan arka plan fotoğrafı yüklenir
  → Form alanları boş
  → "Giriş Yap" butonu aktif (engeli yok)
  → İlk input'a otomatik focus
```

### 5.2 — Form Doğrulama

| Alan | Kural | Hata Mesajı |
|------|-------|-------------|
| Email | Boş olamaz, geçerli format | "E-posta gereklidir" |
| Email | @ içermeli | "Geçerli bir e-posta giriniz" |
| Şifre | Boş olamaz | "Şifre gereklidir" |
| Şifre | Min 8 karakter | "Şifre en az 8 karakter olmalı" |

**Hata gösterimi:** Input altında kırmızı metin, input border kırmızıya döner.

### 5.3 — Submit Akışı

```
Kullanıcı "Giriş Yap"'a tıklar
  → Buton loading durumuna geçer (spinner)
  → Form alanları devre dışı
  → Backend: POST /auth/login
    → Request: { email, password, csrf_token }
    → Response: { success, token, user }
  → Başarılı:
    → Session cookie ayarla
    → Ana sayfaya yönlendirme (/)
    → Header'da kullanıcı bilgileri güncellenir
  → Başarısız:
    → Hata mesajı göster
    → Buton tekrar aktif
    → Şifre alanı temizlenir
    → Email alanı korunur
```

### 5.4 — Social Login Akışı

```
Kullanıcı Apple/Google/Facebook tıklar
  → Popup açılır veya redirect
  → OAuth flow çalışır
  → Başarılı → Backend: social login endpoint
  → Yeni kullanıcıysa → Register flow'a geçiş
  → Mevcut kullanıcıysa → Ana sayfaya yönlendirme
```

### 5.5 — "Şifremi Unuttum" Akışı

```
Kullanıcı "Şifremi Unuttum" tıklar
  → Forgot password sayfasına yönlendirme
  → Email girilir → "Gönder" tıklanır
  → Backend: Sıfırlama linki email ile gönderilir
  → Kullanıcı email'deki linke tıklar
  → Yeni şifre formu gösterilir
  → Şifre + Tekrar girilir
  → Backend: Şifre güncellenir
  → Login sayfasına yönlendirme
```

### 5.6 — "Kayıt Ol" Akışı

```
Kullanıcı "Kayıt Ol" tıklar
  → Register sayfasına yönlendirme
  → Select Gender (1. adım)
  → Register Step 1: Kullanıcı Adı + Email
  → Register Step 2: Şifre + Tekrar
  → Register Step 3: Telefon + KVKK
  → Kayıt tamamlandı → Login sayfasına yönlendirme
```

---

## 6. ERİŞİLEBİLİRLİK (WCAG 2.2 AA)

| Kriter | Durum | Not |
|--------|-------|-----|
| Touch target (input) | ✅ | 56px |
| Touch target (buton) | ✅ | 56px |
| Touch target (social) | ✅ | 52×52px |
| Touch target (checkbox) | ⚠️ | ~16px → 44px olmalı |
| Focus indicator | ✅ | `outline: 2px solid var(--theme-primary)` |
| Keyboard nav | ✅ | Tab ile gezinme |
| ARIA labels | ⚠️ | `aria-label` ekle |
| Renk kontrastı | ✅ | Beyaz text, pembe buton |
| Screen reader | ⚠️ | `role="form"` ekle |

---

## 7. GÜVENLİK NOTLARI

| Kural | Değer | ADR |
|-------|-------|-----|
| CSRF token | `csrf_token` (NOT `_csrf_token`) | ADR-010 |
| Password hashing | Argon2id (64MB/4/2) | ADR-022 |
| Session | `COREMUSIC_SESS`, 3600s idle | ADR-011 |
| Rate limit | 60 req/60s (APCu) | ADR-013 |
| HTTPS | Zorunlu prod ortamında | ADR-020 |

---

## 8. RESPONSIVE NOTLARI

| Platform | Değişiklik |
|----------|-----------|
| 1024px (RPi5) | Mevcut layout (bu dosya) |
| 1920px (Desktop) | Sol alan genişler, sağ panel sabit |
| 3840px (4K TV) | Font boyutları büyür, touch target 64px |
| Mobile | Ayrı uygulama (mobil hariç) |

---

## 9. PERFORMANS NOTLARI

| Metrik | Hedef |
|--------|-------|
| FCP | <1.5s |
| LCP | <2.5s |
| CLS | <0.1 |
| TTI | <3s |
| Font loading | FOUT (Arima + Bickham) |
| Image | Lazy loading (sol alan fotoğrafı) |

---

## 10. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[screens/A-auth/login]] | Login screen spec |
| [[screens/00-ascii-art-views]] §14 | ASCII art view |
| [[screens/_layout-patterns/05-auth-screen]] | Auth layout pattern |
| [[01-component-inventory]] C06, C08, C04 | Bileşen detayları |
| [[flow/auth/02-register]] | Register akışı |
| [[flow/auth/03-forgot-password]] | Şifre sıfırlama |
| [[ADR-010-csrf-protection-strategy]] | CSRF |
| [[ADR-011-session-management]] | Session |
| [[ADR-043-auth-subdomain-consolidation]] | Auth konsolidasyonu |

---

## 11. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Platform | home-1024 |
| Layout Pattern | 5: Auth Screen (78/22) |
| Components | C04, C06, C08 |
| Form Fields | 2 (email, password) |
| Social Buttons | 7 |
| WCAG Gaps | 2 (checkbox, ARIA) |
| Security Rules | 5 (CSRF, Argon2id, Session, Rate limit, HTTPS) |
| Responsive Breakpoints | 4 (1024, 1920, 3840, mobile) |

---

*Login Flow v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
