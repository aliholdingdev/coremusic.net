---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Forgot Password Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-29
status: active
version: 1.0.1
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

## 4A. Token Süresi

| Parametre | Değer |
|-----------|-------|
| Token uzunluğu | 6 haneli |
| Geçerlilik süresi | 15 dakika |
| Max deneme | 3 |
| Yenileme cooldown | 60 saniye |

---
## 5. BEM Sınıfları

| BEM sınıfı | Rol (bu dosyadaki kanıt) | Durumlar | Kaynak |
|------------|--------------------------|----------|--------|
| `.input`, `.input__label` | eleman — E-posta (bu dosya L95), token alanı (L112), yeni şifre + tekrar (L130-L131) | default, focus, error, success, disabled | 02-component-inventory.md L75 (C05) |
| `.btn`, `.btn--primary` | blok — [▶ Sıfırlama Linki Gönder] (L97), [▶ Doğrula] (L116), [▶ Şifreyi Güncelle] (L135) | default, hover, active, disabled, loading | 02-component-inventory.md L65 (C04) |
| `.progress`, `.progress__fill` | blok — "Şifre gücü: ██████░░░░ (Orta)" çubuğu (bu dosya L133) | determinate, indeterminate, error | 02-component-inventory.md L175 (C15) |
| `.modal` | blok — Embedded tier "Modal overlay" form tipi (bu dosya L164) | closed, opening, open, closing | 02-component-inventory.md L95 (C07) |
| `.nav-link` | eleman — "← Giriş Yap'a Dön" geri bağlantısı (bu dosya L99) | default, hover, active, disabled | 02-component-inventory.md L35 (C01) |

> **Kaynak:** [[../../02-component-inventory]] v4.1.0 (C01-C19, `updated: 2026-09-29`).

---

## 6. Adımlar

| # | Adım | Ekrana | Aksiyon |
|---|------|--------|---------|
| 1 | E-posta Gir | E-posta Girme | [📧 E-posta] alanına e-posta adresi yaz |
| 2 | Sıfırlama Linki Gönder | E-posta Girme | [▶ Sıfırlama Linki Gönder] butonuna tıkla |

> ⚠️ VERIFICATION REQUIRED — Kod çelişkisi nedeniyle satır yazılmadı: EKRAN 2 "Token Doğrulama" (bu dosya L106-L120, 6 haneli kod + [▶ Doğrula]) ve EKRAN 3 "Yeni Şifre" (L125-L136, [🔒 Şifre Tekrar]) adımları, `auth.coremusic.net/pages/forgot-password.php` L60 "Sıfırlama Bağlantısı Gönder" (akış e-posta linki üzerinden) ve `auth.coremusic.net/pages/reset-password.php` L59 tek şifre alanı + URL'den okunan token (L99-L103) ile çelişiyor. §2 ve §4A değiştirilmedi.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
