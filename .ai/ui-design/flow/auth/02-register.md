---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Register Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Register Flow (3 Adımlı Wizard)

## 1. Akış Diyagramı (Wizard Flow)

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│  ADIM 1/3       │
│  Kişisel Bilgiler│
│  [👤 Ad Soyad]   │
│  [📧 E-posta]    │
│  [🔒 Şifre]      │
│  [🔒 Tekrar]     │
│  [*]-----[ ]-----[ ]│
└────────┬────────┘
         │
┌────────▼────────┐
│ Email Benzersiz? │
└────────┬────────┘
  Hayır─┤─Evet
  │     │     │
┌─▼───┐ │  ┌──▼────────────┐
│Hata │ │  │ Şifre Güçlü   │
│"Bu  │ │  └──┬────────────┘
│email │ │     │
│var"  │ │  Zayıf─┤─Güçlü
└─────┘ │   │    │    │
        │┌──▼──┐ │ ┌──▼──────┐
        ││Uyarı│ │ │Devam Et │
        │└─────┘ │ └──┬──────┘
        │        │    │
        │   ┌────▼────┐
        │   │Geri Dön │
        │   │Seçeneği │
        │   └─────────┘
         │
┌────────▼────────┐
│  ADIM 2/3       │
│  Cinsiyet Seç   │
│  [👩 Kız]        │
│  [👨 Erkek]      │
│  [[V] Nötr]      │
│  [*]-----[*]-----[ ]│
└────────┬────────┘
         │
┌────────▼────────┐
│ Seçim Yapıldı?  │
└────────┬────────┘
  Hayır─┤─Evet
  │     │     │
┌─▼───┐ │  ┌──▼────────────┐
│Buton│ │  │Theme Uygula    │
│Disabled│  │Kız→pembe     │
│"Devam" │  │Erkek→mavi    │
└─────┘ │  │Nötr→default  │
        │  └──┬────────────┘
        │     │
        │  ┌──▼────────────┐
        │  │Preference Kaydet│
        │  └──┬────────────┘
        │     │
        │  ┌──▼──────┐
        │  │Geri Dön  │
        │  └─────────┘
         │
┌────────▼────────┐
│  ADIM 3/3       │
│  Profil Foto    │
│  [+ Foto Yükle] │
│  (Opsiyonel)    │
│  [*]-----[*]-----[*]│
└────────┬────────┘
         │
┌────────▼────────┐
│ Fotoğraf Yükle? │
└────────┬────────┘
  Hayır─┤─Evet
  │     │     │
┌─▼───┐ │  ┌──▼────────────┐
│Atla │ │  │Upload Başlat   │
└─────┘ │  └──┬────────────┘
        │     │
        │  ┌──▼────────────┐
        │  │Boyut Kontrol  │
        │  │(Max 5MB)      │
        │  └──┬────────────┘
        │     │
        │  Büyü─┤─Uygun
        │   │   │     │
        │┌──▼──┐│  ┌──▼──────────┐
        ││Resize││  │Profili Kaydet│
        │└─────┘│  └──┬──────────┘
        │       │     │
         │  ┌────▼────────────┐
         │  │ Kayıt Başarılı  │
         │  │ → Login Ekranı  │
         │  └─────────────────┘
```

## 2. Ekran Akışı

```
┌──────────────────────────────────────────────────────────────┐
│ ADIM 1/3: Kişisel Bilgiler                                  │
│                                                              │
│ +--- LEFT (60%) ---+  +--- RIGHT (40%) ------------------+ |
│ | 🏔️ Manzara       |  | 👤 Kayıt Ol                      | |
| |                   |  |                                  | |
| |                   |  | [👤 Ad Soyad]                    | |
| |                   |  | [📧 E-posta]                     | |
| |                   |  | [🔒 Şifre]                       | |
| |                   |  | [🔒 Şifre Tekrar]                | |
| |                   |  |                                  | |
| |                   |  | [*]-----[ ]-----[ ]  (1/3)       | |
| |                   |  |                                  | |
| |                   |  | [▶ Devam Et]                     | |
| +-------------------+  +----------------------------------+ |
└──────────────────────────────────────────────────────────────┘
       │
       │ Adım 1 başarılı
       ▼
┌──────────────────────────────────────────────────────────────┐
│ ADIM 2/3: Cinsiyet Seçimi                                   │
│                                                              │
│ +--- LEFT (60%) ---+  +--- RIGHT (40%) ------------------+ |
│ | 🏔️ Manzara       |  | 👤 Cinsiyetini Seç              | |
| |                   |  |                                  | |
| |                   |  | [👩 Kız]                         | |
| |                   |  | Theme pembe, öneriler pembe      | |
| |                   |  |                                  | |
| |                   |  | [👨 Erkek]                       | |
| |                   |  | Theme mavi, öneriler mavi        | |
| |                   |  |                                  | |
| |                   |  | [[V] Belirtmek İstemiyorum]      | |
| |                   |  | Theme nötr, öneriler nötr        | |
| |                   |  |                                  | |
| |                   |  | [*]-----[*]-----[ ]  (2/3)       | |
| |                   |  |                                  | |
| |                   |  | [▶ Devam Et]                     | |
| +-------------------+  +----------------------------------+ |
└──────────────────────────────────────────────────────────────┘
       │
       │ Adım 2 başarılı
       ▼
┌──────────────────────────────────────────────────────────────┐
│ ADIM 3/3: Profil Fotoğrafı                                  │
│                                                              │
│ +--- LEFT (60%) ---+  +--- RIGHT (40%) ------------------+ |
│ | 🏔️ Manzara       |  | 👤 Profil Fotoğrafı              | |
| |                   |  |                                  | |
| |                   |  |  +----------+                    | |
| |                   |  |  |  [C]      |                    | |
| |                   |  |  |  Yükle   |                    | |
| |                   |  |  +----------+                    | |
| |                   |  |                                  | |
| |                   |  | [*]-----[*]-----[*]  (3/3)       | |
| |                   |  |                                  | |
| |                   |  | [▶ Kayıt Ol]                     | |
| +-------------------+  +----------------------------------+ |
└──────────────────────────────────────────────────────────────┘
       │
       │ Kayıt başarılı
       ▼
┌─────────────────┐
│   LOGIN EKRANI  │
└─────────────────┘
```

## 3. Hata Senaryoları

| Hata | Adım | Çözüm | Max Retry |
|------|:----:|-------|:---------:|
| Email zaten var | 1 | "Bu email ile hesap mevcut" | — |
| Şifre zayıf | 1 | "Şifre en az 8 karakter olmalı" | — |
| Şifreler uyuşmuyor | 1 | "Şifreler eşleşmiyor" | — |
| Geçersiz email | 1 | "Geçersiz email formatı" | — |
| Seçim yapılmadı | 2 | Buton disabled kalır | — |
| Fotoğraf çok büyük | 3 | "Max 5MB" uyarısı | — |
| Fotoğraf formatı hatalı | 3 | "JPG/PNG destekleniyor" | — |
| Network hatası | 1-3 | "Bağlantı yok" + retry | 3 |

## 4. Wizard Bileşenleri

| BEM Sınıfı | Açıklama |
|------------|----------|
| `.wizard` | Wizard container |
| `.wizard__step` | Adım göstergesi ([*]-----[*]-----[ ]) |
| `.wizard__step--active` | Aktif adım |
| `.wizard__step--completed` | Tamamlanan adım |
| `.wizard__content` | Adım içeriği |
| `.wizard__actions` | İleri/Geri butonları |
| `.wizard__back` | Geri butonu |
| `.wizard__next` | Devam Et butonu |

## 5. Tier-Bazlı Varyasyonlar

| Tier | Wizard Tipi | Navigasyon | Fotoğraf |
|------|-------------|------------|----------|
| **Phone** | Full-screen wizard | Swipe between steps | Camera/Gallery |
| **Tablet** | Split-panel wizard | Button navigation | Camera/Gallery |
| **Embedded** | Split 42/58 | Button navigation | File upload |
| **Desktop** | Modal wizard | Button navigation | Drag-drop |
| **TV** | Large modal | Remote navigation | USB import |
| **Car** | Simplified | Voice/simplified | — |
| **Watch** | Micro wizard | Crown scroll | — |

## 6. Adımlar

| # | Adım | Alanlar | Zorunlu |
|---|------|---------|:-------:|
| 1 | Kişisel Bilgiler | Ad Soyad, E-posta, Şifre, Şifre Tekrar | ✅ |
| 2 | Cinsiyet Seçimi | Kız, Erkek, Nötr | ✅ |
| 3 | Profil Fotoğrafı | Fotoğraf yükle | ❌ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
