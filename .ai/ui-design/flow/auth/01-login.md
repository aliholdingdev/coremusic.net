---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Auth Login Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Auth Login Flow

## 1. Akış Diyagramı (Decision Flow)

```
┌─────────────┐
│    BAŞLA    │
└──────┬──────┘
       │
┌──────▼──────┐
│ Gender Seç  │
│ (İLK ADIM)  │
└──────┬──────┘
       │
┌──────▼──────┐
│ Login Form  │
│ [📧 E-posta]│
│ [🔒 Şifre]  │
│ ☐ Beni Hatırla│
│ [▶ GİRİŞ YAP]│
└──────┬──────┘
       │
┌──────▼──────────────┐
│   Email Geçerli mi? │
└──────┬──────────────┘
  Hayır─┤─Evet
  │     │     │
┌─▼───┐ │  ┌──▼──────────┐
│Hata │ │  │Şifre Kontrol│
│Mesaj│ │  └──┬───────────┘
│"Geç-│ │     │
│ersiz │ │  Hatalı─┤─Doğru
│email"│ │   │     │     │
└─────┘ │┌───▼──┐ │  ┌──▼──────────┐
        ││Kırmız│ │  │Session Başlat│
        ││Border│ │  │JWT Oluştur   │
        │└─────┘ │  └──┬──────────┘
        │        │     │
        │   ┌────▼─────┐
        │   │Max 5     │
        │   │Deneme?   │
        │   └────┬─────┘
        │  Evet─┤─Hayır
        │   │   │     │
        │┌──▼──┐│  ┌──▼────────────┐
        ││Lock ││  │Ana Sayfa'ya   │
        ││30sn ││  │Yönlendir      │
        │└─────┘│  └───────────────┘
        │       │
        │  ┌────▼─────┐
        │  │Şifre     │
        │  │Değiştirme│
        │  │Linki Gönder│
        │  └──────────┘
```

## 2. Ekran Akışı

```
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 1: Select Gender (İLK ADIM)                           │
│                                                              │
│ +--- LEFT (60%) ---+  +--- RIGHT (40%) ------------------+ |
│ | 🏔️ Manzara       |  | 👤 Seni Tanıyalım                | |
| | [*] Core Music     |  |                                  | |
| |                   |  | [👩 Kız] [👨 Erkek] [[V] Nötr]   | |
| |                   |  |                                  | |
| |                   |  | [▶ Devam Et]                     | |
| +-------------------+  +----------------------------------+ |
└──────────────────────────────────────────────────────────────┘
       │
       │ Seçim yapıldı
       ▼
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 2: Login                                               │
│                                                              │
│ +--- LEFT (60%) ---+  +--- RIGHT (40%) ------------------+ |
│ | 🏔️ Manzara       |  | 👤 Hoş Geldin                    | |
| | [*] Core Music     |  |                                  | |
| |                   |  | [📧 E-posta]                     | |
| |                   |  | [🔒 Şifre]                       | |
| |                   |  | ☐ Beni Hatırla  Şifremi Unuttum  | |
| |                   |  | [▶ GİRİŞ YAP]                   | |
| |                   |  | -- veya --                       | |
| |                   |  | [🍎] [🔍] [📘] [💬] [[C]] [🎵]  | |
| |                   |  | Hesabın yok mu? Kayıt Ol         | |
| +-------------------+  +----------------------------------+ |
└──────────────────────────────────────────────────────────────┘
       │
       │ Giriş başarılı
       ▼
┌─────────────────┐
│   ANA SAYFA     │
└─────────────────┘
```

## 3. Hata Senaryoları

| Hata | Tetikleyici | Çözüm | Max Retry |
|------|-------------|-------|-----------|
| Email hatalı | Geçersiz format veya kayıtlı değil | "Geçersiz e-posta" + kırmızı border | 5 |
| Şifre hatalı | Yanlış şifre | "Şifre yanlış" + kırmızı border | 5 |
| Boş alan | E-posta veya şifre boş | "Bu alan zorunlu" | — |
| Hesap kilitlendi | 5 başarısız deneme | "30sn bekle" + countdown | 1 |
| Network hatası | İnternet yok | "Bağlantı yok" + retry butonu | 3 |
| Session süresi doldu | 3600sn idle | Otomatik login ekranına dön | — |

## 4. Tier-Bazlı Varyasyonlar

| Tier | Login Formu | Buton | Klavye |
|------|-------------|-------|--------|
| **Phone** (≤767px) | Full-screen, bottom sheet | Büyük, thumb-area | On-screen |
| **Tablet** (768-1024px) | Split-panel | Orta boy | On-screen |
| **Embedded** (1024×600) | Split 42/58 | Orta boy | On-screen |
| **Desktop** (≥1920px) | Sidebar panel | Normal boy | Physical |
| **TV** (≥3840px) | Large input, remote | Büyük, focus ring | Remote |
| **Car** | Voice-first | Dokunmatik, büyük | Voice |
| **Watch** | Micro UI | Haptic tap | — |

## 5. BEM Sınıfları

| BEM Sınıfı | Açıklama |
|------------|----------|
| `.login` | Login container |
| `.login__form` | Form elementi |
| `.login__input` | Input field |
| `.login__input--error` | Hatalı input (kırmızı border) |
| `.login__button` | Giriş yap butonu |
| `.login__social` | Sosyal medya butonları |
| `.login__forgot` | Şifremi unuttum linki |
| `.login__register` | Kayıt ol linki |

## 6. Adımlar

| # | Adım | Ekrana | Aksiyon |
|---|------|--------|---------|
| 1 | Select Gender | Gender ekranı | Kız/Erkek/Nötré seç |
| 2 | Devam Et | Login ekranına geç | Butona tıkla |
| 3 | E-posta Gir | Login ekranı | E-posta yaz |
| 4 | Şifre Gir | Login ekranı | Şifre yaz |
| 5 | Giriş Yap | Ana sayfa | Butona tıkla |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
