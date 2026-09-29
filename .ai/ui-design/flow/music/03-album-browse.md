---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Album Browse Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-29
status: active
version: 1.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Album Browse Flow

## 1. Akış Diyagramı (Decision Flow)

### Navigation Flow

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│ Ana Sayfa       │
│ [🎵 Albümler]   │
└────────┬────────┘
         │
┌────────▼────────┐
│ Albüm Listesi   │
│ Grid: 2-4 sütun │
│ Filtre: Tür/Yıl │
└────────┬────────┘
         │
┌────────▼────────┐
│ Albüm Seç       │
└────────┬────────┘
         │
┌────────▼────────┐
│ Albüm Detayı    │
│ +---------------│
│ | Kapak Görseli │
│ | Sanatçı Adı   │
│ | Yıl · Tür     │
│ | Şarkı Sayısı  │
│ +---------------│
└────────┬────────┘
         │
┌────────▼────────┐
│ Aksiyon Seç     │
└────────┬────────┘
    ┌────┼────┐
    │    │    │
┌───▼──┐│┌───▼──┐│┌───▼──┐
│Tümünü│││Tek   │││Playliste│
│Oynat │││Şarkı │││Ekle   │
└───┬──┘│└───┬──┘│└───┬──┘
    │   │    │   │    │
    └───┴────┴───┴────┘
         │
    ┌────▼────┐
    │Footer   │
    │Player   │
    │Güncelle │
    └─────────┘
```

## 2. Ekran Akışı

```
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 1: Albüm Listesi (Grid)                               │
│                                                              │
│ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐            │
│ │  🎵         │ │  🎵         │ │  🎵         │            │
│ │  Hayat      │ │  Fantastik  │ │  Çubuklar   │            │
│ │  Rüya Gibi  │ │  Dünyalar   │ │             │            │
│ │  Göksel     │ │  Erkin Koray│ │  Gangsta    │            │
│ │  2024       │ │  1973       │ │  2023       │            │
│ └─────────────┘ └─────────────┘ └─────────────┘            │
│ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐            │
│ │  🎵         │ │  🎵         │ │  🎵         │            │
│ │  Enstrümantal│ │  Pop       │ │  Jazz       │            │
│ │  Kolay      │ │  Hitleri   │ │  Classics   │            │
│ │  Müzik      │ │  2024      │ │  2022       │            │
│ └─────────────┘ └─────────────┘ └─────────────┘            │
└──────────────────────────────────────────────────────────────┘
       │
       │ Albüm seçildi
       ▼
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 2: Albüm Detayı                                        │
│                                                              │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │  ┌──────────┐  Hayat Rüya Gibi                         │ │
│ │  │          │  Göksel · 2024 · Pop                      │ │
│ │  │  KAPAK   │  12 şarkı · 45 dakika                    │ │
│ │  │  GÖRSELİ │                                           │ │
│ │  │          │  [▶ Tümünü Oynat]  [🔀 Karışık]          │ │
│ │  └──────────┘  [❤️] [⬇️] [⋯]                           │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │  1. Sevil Neşelenen          00:05:00  [▶] [⋯]         │ │
│ │  2. Kabahat Sensin           00:05:00  [▶] [⋯]         │ │
│ │  3. Hayat Rüya Gibi          00:04:30  [▶] [⋯]         │ │
│ │  4. Sevgilim                  00:03:45  [▶] [⋯]         │ │
│ │  ...                                                    │ │
│ └─────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────┘
```

## 2A. Filtre Akışı

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│ Filtre Butonu    │
└────────┬────────┘
    ┌────┼────┐
    │    │    │
┌───▼──┐│┌───▼──┐│┌───▼──┐
│Tür   │││Yıl   │││Sanatçı│
│Filtre│││Filtre│││Filtre │
└───┬──┘│└───┬──┘│└───┬──┘
    │   │    │   │    │
    └───┴────┴───┴────┘
         │
    ┌────▼────┐
    │Sonuçları│
    │Güncelle │
    └─────────┘
```

## 3. Hata Senaryoları

| Hata | Çözüm |
|------|-------|
| Albüm bulunamadı | "Albüm bulunamadı" + geri dön |
| Kapak görseli yok | Varsayılan müzik ikonu |
| Şarkı listesi boş | "Bu albümde şarkı yok" |
| Network hatası | Cache'den göster |

## 4. Tier-Bazlı Varyasyonlar

| Tier | Grid | Detay | Oynatma |
|------|------|-------|---------|
| **Phone** | 2 sütun | Full-screen | Mini player |
| **Tablet** | 3 sütun | Split-panel | Footer bar |
| **Embedded** | 3 sütun | Split 42/58 | Footer bar |
| **Desktop** | 4 sütun | Sidebar + detail | Footer + queue |
| **TV** | 2 sütun, large | Large cards | Large controls |
| **Car** | List view | Simplified | Voice |
| **Watch** | List | Micro | Crown |

---
## 5. BEM Sınıfları

> ⚠️ VERIFICATION REQUIRED — Kaynak dosyada bu bölüm yok; sınıflar 02-component-inventory.md (C01-C16) ile eşleştirilerek doldurulacak.

---

## 6. Adımlar

> ⚠️ VERIFICATION REQUIRED — Kaynak dosyada bu bölüm yok; numaralı, tek-eylem adım listesi akış doğrulamasından sonra doldurulacak.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
