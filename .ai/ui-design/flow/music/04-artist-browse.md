---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Artist Browse Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-29
status: active
version: 1.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Artist Browse Flow

## 1. Akış Diyagramı (Decision Flow)

### Navigation Flow

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

## 2A. Keşif Akışı

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

## 3. Hata Senaryoları

| Hata | Çözüm |
|------|-------|
| Sanatçı bulunamadı | "Sanatçı bulunamadı" + geri dön |
| Fotoğraf yok | Varsayılan mikrofon ikonu |
| Biyografi yok | "Biyografi mevcut değil" |
| Albüm listesi boş | "Henüz albüm yok" |
| Network hatası | Cache'den göster |

## 4. Tier-Bazlı Varyasyonlar

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
## 5. BEM Sınıfları

| BEM sınıfı | Rol (bu dosyadaki kanıt) | Durumlar | Kaynak |
|------------|--------------------------|----------|--------|
| `.card`, `.card__image` | blok — sanatçı grid kartları (bu dosya L80-L89) ve albüm kartları (L112-L116) | default, hover, active, loading, skeleton | 02-component-inventory.md L55 (C03) |
| `.tab`, `.tab-list`, `.tab--active` | eleman — [Albümler] [Popüler] [İlişkili] sekme satırı (bu dosya L104) | default, hover, active, disabled | 02-component-inventory.md L85 (C06) |
| `.avatar` | blok — sanatçı FOTOĞRAF alanı (bu dosya L43, L98-L100) | default, with-image, with-initials, online, offline | 02-component-inventory.md L135 (C11) |
| `.btn`, `.btn--primary`, `.btn--ghost` | blok — [▶ Tümünü Oynat], [❤️ Takip] (bu dosya L101-L102) | default, hover, active, disabled, loading | 02-component-inventory.md L65 (C04) |

> **Kaynak:** [[../../02-component-inventory]] v4.1.0 (C01-C19, `updated: 2026-09-29`).

---

## 6. Adımlar

| # | Adım | Ekrana | Aksiyon |
|---|------|--------|---------|
| 1 | Sanatçılar | singer | Ana Sayfa'da [🎤 Sanatçılar] butonuna tıkla [[screens/T07-embedded/singer]] |
| 2 | Sanatçı Seç | singer | sanatçı kartına tıkla [[screens/T07-embedded/singer]] |
| 3 | Tümünü Oynat | singer | [▶ Tümünü Oynat] butonuna tıkla [[screens/T07-embedded/singer]] |
| 4 | Takip Et | singer | [❤️ Takip] butonuna tıkla [[screens/T07-embedded/singer]] |
| 5 | Sekme Seç | singer | [Popüler] sekmesine tıkla [[screens/T07-embedded/singer]] |
| 6 | Şarkı Seç ve Oynat | singer | POPÜLER ŞARKILAR listesinden şarkı satırına tıkla [[screens/T07-embedded/singer]] |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
