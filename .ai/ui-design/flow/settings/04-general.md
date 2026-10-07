---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — General Settings Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-29
status: active
version: 1.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# General Settings Flow

## 1. Akış Diyagramı (Decision Flow)

### Settings Flow

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│ Ayar Kategorisi │
│ Seç             │
└────────┬────────┘
    ┌────┼────────────┐
    │    │            │
┌───▼──┐ │ ┌──▼──────┐ │ ┌──▼──────┐
│Tema  │ │ │Dil      │ │ │Bildirim │
│Seç   │ │ │Seç      │ │ │Ayarla   │
└───┬──┘ │ └──┬──────┘ │ └──┬──────┘
    │    │    │        │    │
┌───▼──┐ │ ┌──▼──────┐ │ ┌──▼──────┐
│Pembe │ │ │Türkçe   │ │ │Push     │
│Mavi  │ │ │İngilizce│ │ │Email    │
│Nötr  │ │ │Almanca  │ │ │Sms      │
└───┬──┘ │ └──┬──────┘ │ └──┬──────┘
    │    │    │        │    │
    └────┴────┴────────┴────┘
              │
       ┌──────▼──────┐
       │Değişiklik   │
       │Yapıldı mı? │
       └──────┬──────┘
         Hayır─┤─Evet
         │     │     │
     ┌───▼───┐ │  ┌──▼──────────┐
     │Değişik-│ │  │Kaydet       │
     │lik yok│ │  └──┬──────────┘
     └───────┘ │     │
            ┌──▼──────────┐
            │Onay Modalı  │
            │"Kaydedilsin?"│
            └──┬──────────┘
          Hayır─┤─Evet
          │     │     │
      ┌───▼───┐ │  ┌──▼──────────┐
      │İptal  │ │  │Kaydet       │
      │Geri   │ │  │→ DB         │
      └───────┘ │  └──┬──────────┘
             │     │
          ┌──▼──────────┐
          │Uygula       │
          │→ Sayfa Yenile│
          └─────────────┘
```

## 2. Ekran Akışı

```
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 1: Genel Ayarlar                                      │
│                                                              │
│ +--- MODAL (w:500, glass) ------------------------------+   |
│ |                                                        |   |
│ |  ⚙️ Genel Ayarlar                                      |   |
│ |                                                        |   |
│ |  --- Tema ---                                         |   |
│ |  🎨 Tema Seçimi                                       |   |
│ |  [👩 Kız] [👨 Erkek] [[V] Nötr]                       |   |
│ |                                                        |   |
│ |  --- Dil ---                                          |   |
│ |  🌐 Dil Seçimi                                        |   |
│ |  [Türkçe ▼]                                           |   |
│ |                                                        |   |
│ |  --- Bildirimler ---                                  |   |
│ |  🔔 Push Bildirimleri    ⟷ (toggle)                  |   |
│ |  📧 Email Bildirimleri   ⟷ (toggle)                  |   |
│ |  📱 SMS Bildirimleri     ⟷ (toggle)                  |   |
│ |                                                        |   |
│ |  --- Oynatma ---                                      |   |
│ |  🔀 Varsayılan Karışık    ⟷ (toggle)                  |   |
│ |  🔁 Varsayılan Tekrar     ⟷ (toggle)                  |   |
│ |  📊 Kalite Tercihi        [Otomatik ▼]               |   |
│ |                                                        |   |
│ |  [İptal]  [Kaydet]                                    |   |
│ |                                                        |   |
│ +--------------------------------------------------------+   |
└──────────────────────────────────────────────────────────────┘
```

## 2A. Tema Seçimi Akışı

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│ Tema Seç        │
│ [👩] [👨] [[V]]  │
└────────┬────────┘
         │
┌────────▼────────┐
│ Seçim Yapıldı?  │
└────────┬────────┘
  Hayır─┤─Evet
  │     │     │
┌─▼───┐ │  ┌──▼────────────┐
│Bekle│ │  │Preview         │
└─────┘ │  │Önizleme göster │
        │  └──┬────────────┘
        │     │
        │  ┌──▼────────────┐
        │  │CSS Variables   │
        │  │Geçici güncelle │
        │  └──┬────────────┘
        │     │
        │  ┌──▼────────────┐
        │  │Onayla / İptal │
        │  └──┬────────────┘
        │     │
        │  Onay─┤─İptal
        │   │   │     │
        │┌──▼──┐│  ┌──▼──────────┐
        ││Kaydet││  │Geri al      │
        │└─────┘│  └─────────────┘
```

## 3. Hata Senaryoları

| Hata | Çözüm |
|------|-------|
| Tema uygulanamadı | Varsayılan tema |
| Dil değiştirme başarısız | Mevcut dil korunur |
| Kaydetme başarısız | "Kaydetme başarısız" + tekrar dene |
| DB hatası | "Ayarlar kaydedilemedi" |

## 4. Tier-Bazlı Varyasyonlar

| Tier | Ayar Tipi | Kaydetme | Önizleme |
|------|-----------|----------|----------|
| **Phone** | Full-screen | Anında | Anlık |
| **Tablet** | Split-panel | Buton | Anlık |
| **Embedded** | Modal | Buton | Anlık |
| **Desktop** | Side panel | Buton | Anlık |
| **TV** | Full-screen modal | Remote | Anlık |
| **Car** | Simplified list | Otomatik | — |
| **Watch** | Micro toggle | Otomatik | — |

## 4A. Ayar Kategorileri

| # | Kategori | Ayarlar | Varsayılan |
|---|----------|---------|------------|
| 1 | Tema | Kız/Erkek/Nötr | Nötr |
| 2 | Dil | Türkçe/İngilizce/Almanca | Türkçe |
| 3 | Bildirimler | Push/Email/SMS toggle | Tümü açık |
| 4 | Oynatma | Karışık/Tekrar/Kalite | Varsayılan |

---
## 5. BEM Sınıfları

| BEM sınıfı | Rol (bu dosyadaki kanıt) | Durumlar | Kaynak |
|------------|--------------------------|----------|--------|
| `.modal`, `.modal__content` | blok — MODAL (w:500, glass) (bu dosya L77); Embedded tier "Modal" (L158) | closed, opening, open, closing | 02-component-inventory.md L95 (C07) |
| `.toggle`, `.toggle--active` | blok — Push / Email / SMS / Karışık / Tekrar toggle'ları (bu dosya L90-L96) | off, on, disabled | 02-component-inventory.md L105 (C08) |
| `.dropdown`, `.dropdown__menu` | blok — dil seçimi [Türkçe ▼] (bu dosya L87) ve kalite [Otomatik ▼] (L97) | closed, open, item-hover, item-active | 02-component-inventory.md L165 (C14) |
| `.btn`, `.btn--primary`, `.btn--ghost` | blok — [İptal] [Kaydet] (bu dosya L99) | default, hover, active, disabled, loading | 02-component-inventory.md L65 (C04) |

> **Kaynak:** [[../../02-component-inventory]] v4.1.0 (C01-C19, `updated: 2026-09-29`).

---

## 6. Adımlar

| # | Adım | Ekrana | Aksiyon |
|---|------|--------|---------|
| 1 | Tema Seç | Genel Ayarlar | 🎨 Tema Seçimi'nden bir butona tıkla (örn. [👩 Kız]) |
| 2 | Dil Seç | Genel Ayarlar | [Türkçe ▼] menüsünden dil seç |
| 3 | Bildirim Ayarla | Genel Ayarlar | 🔔 Push Bildirimleri toggle'ını değiştir |
| 4 | Oynatma Ayarla | Genel Ayarlar | 🔀 Varsayılan Karışık toggle'ını değiştir |
| 5 | Kaydet | Genel Ayarlar | [Kaydet] butonuna tıkla |
| 6 | Kaydetmeyi Onayla | Onay Modalı | "Kaydedilsin?" onay modalında Evet'i seç |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
