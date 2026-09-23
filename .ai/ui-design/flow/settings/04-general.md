---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — General Settings Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# General Settings Flow

## 1. Akış Diyagramı (Settings Flow)

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

## 3. Tema Seçimi Akışı

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

## 4. Hata Senaryoları

| Hata | Çözüm |
|------|-------|
| Tema uygulanamadı | Varsayılan tema |
| Dil değiştirme başarısız | Mevcut dil korunur |
| Kaydetme başarısız | "Kaydetme başarısız" + tekrar dene |
| DB hatası | "Ayarlar kaydedilemedi" |

## 5. Tier-Bazlı Varyasyonlar

| Tier | Ayar Tipi | Kaydetme | Önizleme |
|------|-----------|----------|----------|
| **Phone** | Full-screen | Anında | Anlık |
| **Tablet** | Split-panel | Buton | Anlık |
| **Embedded** | Modal | Buton | Anlık |
| **Desktop** | Side panel | Buton | Anlık |
| **TV** | Full-screen modal | Remote | Anlık |
| **Car** | Simplified list | Otomatik | — |
| **Watch** | Micro toggle | Otomatik | — |

## 6. Ayar Kategorileri

| # | Kategori | Ayarlar | Varsayılan |
|---|----------|---------|------------|
| 1 | Tema | Kız/Erkek/Nötr | Nötr |
| 2 | Dil | Türkçe/İngilizce/Almanca | Türkçe |
| 3 | Bildirimler | Push/Email/SMS toggle | Tümü açık |
| 4 | Oynatma | Karışık/Tekrar/Kalite | Varsayılan |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
