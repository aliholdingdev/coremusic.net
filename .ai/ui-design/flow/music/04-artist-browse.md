---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Artist Browse Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Artist Browse Flow

## 1. Akış Diyagramı (Navigation Flow)

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│ Ana Sayfa       │
│ [🎤 Sanatçılar] │
└────────┬────────┘
         │
┌────────▼────────┐
│ Sanatçı Listesi │
│ Grid veya Liste │
│ Filtre: A-Z     │
└────────┬────────┘
         │
┌────────▼────────┐
│ Sanatçı Seç     │
└────────┬────────┘
         │
┌────────▼────────┐
│ Sanatçı Detayı  │
│ +---------------│
│ | Fotoğraf      │
│ | Ad Soyad      │
│ | Biyografi     │
│ | Takipçi Sayısı│
│ +---------------│
└────────┬────────┘
         │
┌────────▼────────┐
│ İçerik Seç      │
└────────┬────────┘
    ┌────┼────┐
    │    │    │
┌───▼──┐│┌───▼──┐│┌───▼──┐
│Albümler││Popüler││İlişkili│
│Listesi ││Şarkılar││Sanatçılar│
└───┬──┘│└───┬──┘│└───┬──┘
    │   │    │   │    │
    └───┴────┴───┴────┘
         │
    ┌────▼────┐
    │Şarkı Seç│
    │→ Oynat  │
    └────┬────┘
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
│ EKRAN 1: Sanatçı Listesi                                    │
│                                                              │
│ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐            │
│ │  🎤         │ │  🎤         │ │  🎤         │            │
│ │  Göksel     │ │  Erkin Koray│ │  Gangsta    │            │
│ │  125K takip │ │  89K takip  │ │  67K takip  │            │
│ └─────────────┘ └─────────────┘ └─────────────┘            │
│ ┌─────────────┐ ┌─────────────┐ ┌─────────────┐            │
│ │  🎤         │ │  🎤         │ │  🎤         │            │
│ │  Barış      │ │  Tarkan     │ │  Sezen Aksu │            │
│ │  Manço      │ │  200K takip │ │  150K takip │            │
│ └─────────────┘ └─────────────┘ └─────────────┘            │
└──────────────────────────────────────────────────────────────┘
       │
       │ Sanatçı seçildi
       ▼
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 2: Sanatçı Detayı                                     │
│                                                              │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │  ┌──────────┐  Göksel                                   │ │
│ │  │          │  125K takipçi · 8 albüm · 156 şarkı      │ │
│ │  │  FOTOĞRAF│                                           │ │
│ │  │          │  [▶ Tümünü Oynat]  [🔀 Karışık]          │ │
│ │  └──────────┘  [❤️ Takip] [⋯]                           │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │  [Albümler] [Popüler] [İlişkili]                       │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │  POPÜLER ŞARKILAR                                      │ │
│ │  1. Sevil Neşelenen          00:05:00  1.2M dinlenme   │ │
│ │  2. Kabahat Sensin           00:05:00  980K dinlenme   │ │
│ │  3. Hayat Rüya Gibi          00:04:30  850K dinlenme   │ │
│ ├─────────────────────────────────────────────────────────┤ │
│ │  ALBÜMLER                                              │ │
│ │  ┌──────────┐ ┌──────────┐ ┌──────────┐               │ │
│ │  │ Hayat    │ │ Yaz      │ │ Kış      │               │ │
│ │  │ Rüya Gibi│ │ Geceleri │ │ Şarkıları│               │ │
│ │  │ 2024     │ │ 2023     │ │ 2022     │               │ │
│ │  └──────────┘ └──────────┘ └──────────┘               │ │
│ └─────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────┘
```

## 3. Keşif Akışı

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│ Önerilen        │
│ Sanatçılar      │
└────────┬────────┘
    ┌────┼────┐
    │    │    │
┌───▼──┐│┌───▼──┐│┌───▼──┐
│Keşfet│││Takip │││Rastgele│
│Et    │││Et    │││Keşfet  │
└───┬──┘│└───┬──┘│└───┬──┘
    │   │    │   │    │
    └───┴────┴───┴────┘
         │
    ┌────▼────┐
    │Profil   │
    │Güncelle │
    └─────────┘
```

## 4. Hata Senaryoları

| Hata | Çözüm |
|------|-------|
| Sanatçı bulunamadı | "Sanatçı bulunamadı" + geri dön |
| Fotoğraf yok | Varsayılan mikrofon ikonu |
| Biyografi yok | "Biyografi mevcut değil" |
| Albüm listesi boş | "Henüz albüm yok" |
| Network hatası | Cache'den göster |

## 5. Tier-Bazlı Varyasyonlar

| Tier | Liste | Detay | Takip |
|------|-------|-------|-------|
| **Phone** | 2 sütun grid | Full-screen | Buton |
| **Tablet** | 3 sütun grid | Split-panel | Buton |
| **Embedded** | 3 sütun grid | Split 42/58 | Buton |
| **Desktop** | 4 sütun grid | Sidebar + detail | Buton |
| **TV** | 2 sütun, large | Large cards | Buton |
| **Car** | List view | Simplified | Voice |
| **Watch** | List | Micro | Crown |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
