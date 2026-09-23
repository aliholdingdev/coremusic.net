---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Forgot Password Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Forgot Password Flow

## 1. Akış Diyagramı (Decision Flow)

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│  E-posta Gir    │
│  [📧 E-posta]   │
│  [▶ Gönder]     │
└────────┬────────┘
         │
┌────────▼────────┐
│ Email Kayıtlı?  │
└────────┬────────┘
  Hayır─┤─Evet
  │     │     │
┌─▼───┐ │  ┌──▼────────────┐
│Hata │ │  │Token Gönder    │
│"Bu  │ │  │(15 dk geçerli) │
│email │ │  └──┬────────────┘
│kayıtlı│ │     │
│değil"│ │  ┌──▼────────────┐
└─────┘ │  │Başarılı Mesaj │
        │  │"Email gönderildi"│
        │  └──┬────────────┘
        │     │
        │  ┌──▼────────────┐
        │  │Token Gir       │
        │  │[🔑 Token]      │
        │  │[▶ Doğrula]     │
        │  └──┬────────────┘
        │     │
        │  ┌──▼────────────┐
        │  │Token Geçerli? │
        │  └──┬────────────┘
        │ Hatalı─┤─Geçerli
        │  │     │     │
        │┌─▼───┐ │  ┌──▼────────────┐
        ││Hata │ │  │Yeni Şifre Gir  │
        ││"Geç-│ │  │[🔒 Yeni Şifre] │
        ││ersiz │ │  │[🔒 Tekrar]     │
        ││token"│ │  └──┬────────────┘
        │└─────┘ │     │
        │    ┌────▼─────┐
        │    │Şifre     │
        │    │Güncellendi│
        │    │[OK]       │
        │    └────┬─────┘
        │         │
        │    ┌────▼─────┐
        │    │Login     │
        │    │Ekranına  │
        │    │Yönlendir │
        │    └──────────┘
        │
        │  ┌──▼────────────┐
        │  │Süre Doldu mu? │
        │  └──┬────────────┘
        │ Evet─┤─Hayır
        │  │   │     │
        │┌─▼───┐│  ┌──▼──────────┐
        ││Yeni ││  │Bekle        │
        ││Token││  │Süre dolana  │
        ││İste ││  │kadar        │
        │└─────┘│  └─────────────┘
```

## 2. Ekran Akışı

```
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 1: E-posta Girme                                      │
│                                                              │
│ +--- LEFT (60%) ---+  +--- RIGHT (40%) ------------------+ |
│ | 🏔️ Manzara       |  | 🔑 Şifre Sıfırlama               | |
| |                   |  |                                  | |
| |                   |  | E-posta adresinizi girin         | |
| |                   |  | [📧 E-posta]                     | |
| |                   |  |                                  | |
| |                   |  | [▶ Sıfırlama Linki Gönder]      | |
| |                   |  |                                  | |
| |                   |  | ← Giriş Yap'a Dön               | |
| +-------------------+  +----------------------------------+ |
└──────────────────────────────────────────────────────────────┘
       │
       │ Link gönderildi
       ▼
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 2: Token Doğrulama                                    │
│                                                              │
│ +--- LEFT (60%) ---+  +--- RIGHT (40%) ------------------+ |
│ | 🏔️ Manzara       |  | 🔑 Token Doğrulama               | |
| |                   |  |                                  | |
| |                   |  | E-postanıza 6 haneli kod gönderildi│
| |                   |  | [🔑 _ _ _ _ _ _]                 | |
| |                   |  |                                  | |
| |                   |  | Kalan süre: 14:32                | |
| |                   |  |                                  | |
| |                   |  | [▶ Doğrula]                      | |
| |                   |  |                                  | |
| |                   |  | Kod gelmedi mi? Tekrar gönder    | |
| +-------------------+  +----------------------------------+ |
└──────────────────────────────────────────────────────────────┘
       │
       │ Token geçerli
       ▼
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 3: Yeni Şifre                                         │
│                                                              │
│ +--- LEFT (60%) ---+  +--- RIGHT (40%) ------------------+ |
│ | 🏔️ Manzara       |  | 🔑 Yeni Şifre Belirle            | |
| |                   |  |                                  | |
| |                   |  | [🔒 Yeni Şifre]                  | |
| |                   |  | [🔒 Şifre Tekrar]                | |
| |                   |  |                                  | |
| |                   |  | Şifre gücü: ██████░░░░ (Orta)    | |
| |                   |  |                                  | |
| |                   |  | [▶ Şifreyi Güncelle]             | |
| +-------------------+  +----------------------------------+ |
└──────────────────────────────────────────────────────────────┘
       │
       │ Şifre güncellendi
       ▼
┌─────────────────┐
│   LOGIN EKRANI  │
│   "Başarılı!"  │
└─────────────────┘
```

## 3. Hata Senaryoları

| Hata | Çözüm | Max Retry |
|------|-------|:---------:|
| Email kayıtlı değil | "Bu email ile hesap bulunamadı" | — |
| Token hatalı | "Geçersiz kod" + tekrar dene | 3 |
| Token süresi dolmuş | "Kodun süresi doldu, yenisini gönder" | — |
| Şifre zayıf | "En az 8 karakter, 1 büyük harf" | — |
| Şifreler uyuşmuyor | "Şifreler eşleşmiyor" | — |
| Network hatası | "Bağlantı yok" + retry | 3 |

## 4. Tier-Bazlı Varyasyonlar

| Tier | Form Tipi | Token Giriş | Timer |
|------|-----------|-------------|-------|
| **Phone** | Full-screen form | On-screen numeric | Görsel ring |
| **Tablet** | Split-panel | On-screen numeric | Sayısal |
| **Embedded** | Modal overlay | On-screen numeric | Sayısal |
| **Desktop** | Sidebar panel | Physical keyboard | Sayısal |
| **TV** | Large input | Remote numeric | Büyük font |
| **Car** | Voice input | Voice dictation | Sesli uyarı |
| **Watch** | Micro input | Crown scroll | Haptic |

## 5. Token Süresi

| Parametre | Değer |
|-----------|-------|
| Token uzunluğu | 6 haneli |
| Geçerlilik süresi | 15 dakika |
| Max deneme | 3 |
| Yenileme cooldown | 60 saniye |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
