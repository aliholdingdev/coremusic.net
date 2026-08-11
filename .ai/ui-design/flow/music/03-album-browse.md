---
title: CoreMusic — Music Flow: Album Browse (Detaylı, 500+ Satır)
date: 2026-08-11
version: 2.0.0
platform: home-1024 (Linux Embedded RPi5, 1024×600)
author: Senior Frontend Architect (50+ yıl deneyim)
references:
  - [[screens/C-music/albums]]
  - [[screens/C-music/album-detail]]
  - [[screens/00-ascii-art-views]] §3, §4
  - [[01-component-inventory]] C09, C11, C10, C13, C12
---

# Music Flow: Album Browse — Detaylı Akış Analizi

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

---

## 1. GENEL AKIŞ

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    ALBÜM BROWSE AKIŞI                                        │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐                  │
│  │ Albümler     │ →  │ Genre Tab    │ →  │ Kart Grid    │                  │
│  │ Sayfası      │    │ Seçilir      │    │ Filtrelenir  │                  │
│  │ (/albums)    │    │ (C11)        │    │              │                  │
│  └──────────────┘    └──────────────┘    └──────────────┘                  │
│                                                     │                       │
│                                              ┌──────┴──────┐               │
│                                              │              │               │
│                                         ┌────▼────┐  ┌─────▼─────┐        │
│                                         │ Kart    │  │ Detail    │        │
│                                         │ Tıklanır│  │ Panel     │        │
│                                         └────┬────┘  └─────┬─────┘        │
│                                              │              │               │
│                                              └──────┬───────┘               │
│                                                     │                       │
│                                              ┌──────▼──────┐               │
│                                              │ "Hemen Çal" │               │
│                                              │ veya "Detay"│               │
│                                              └──────┬──────┘               │
│                                                     │                       │
│                                              ┌──────▼──────┐               │
│                                              │ Albüm       │               │
│                                              │ Detayı      │               │
│                                              │ (/album/:id)│               │
│                                              └──────┬──────┘               │
│                                                     │                       │
│                                              ┌──────▼──────┐               │
│                                              │ Track List  │               │
│                                              │ + Playback  │               │
│                                              └─────────────┘               │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. ALBÜMLER SAYFASI YAPISI

### 2.1 — Başlık Alanı

```
← Albümler / Tüm Albümler                    [Sanatçı Adı Ara 🔍] [≡]
"Albümler"
Kütüphanede depolanan tüm albümler

[Tümü] [Pop] [Arabesk] [Dans] [Oyun Havası] [Damar] [Org] [Yabancı Pop] [Kpop/Kore]
^^^^^^  ^^^^  ^^^^^^^^  ^^^^^  ^^^^^^^^^^^^  ^^^^^  ^^^   ^^^^^^^^^^^^  ^^^^^^^^^^^
active  default                                                  (yatay scroll)
```

### 2.2 — Card Grid (Sol %60)

```
3 sütun × 3 satır = 9 kart
Gap: 8px
Padding: 12px
Scroll: dikey, overflow-y: auto

Her kart (C09):
┌─────────────────┐
│  ┌───────────┐  │
│  │  140×140  │  │  album thumb (kare, r:8px)
│  │  album    │  │
│  │  art      │  │
│  └───────────┘  │
│  Album Title     │  12px, 600, max 2 satır
│  Artist Name     │  10px, 400, muted
│  00:10:05        │  10px, 400, accent
└─────────────────┘
Toplam: ~140×180px
```

### 2.3 — Detail Panel (Sağ %40)

```
┌──────────────────────────────────────┐
│  ┌────────────┐                      │
│  │ 300×300    │ 圆形 Album Art       │
│  │ (r:50%)    │                      │
│  └────────────┘                      │
│  Nobetci Eczane Ferhat Kasetleri     │  16px, 600
│  Kaset                               │  12px, 400, muted
│                                      │
│  [Hemen Çal] (C04, pembe, full-width)│
│  [Karışık Çal] (C05, sınır)  [...]  │
│                                      │
│  Kalite: 24 Bit / 48 kHz            │  11px, muted
│  Boyut: 2 GB                        │
│  İndirme Sayısı: 2                  │
│  Parça Sayısı: 12                   │
│  Tür: Arabesk                       │
│  Yıl: Bilinmeyen Yıl                │
│  Dinlenme Sayısı: 5                 │
│  Süre: 00:30:00                     │
└──────────────────────────────────────┘
```

---

## 3. GENRE TAB ETKİLEŞİMİ (C11)

```
Kullanıcı bir genre sekmesine tıklar
  → JS: Seçili tab güncellenir (pembe arka plan)
  → JS: AJAX ile albümler filtrelenir
    → GET /api/albums?genre=pop&page=1&limit=9
    → Response: { albums: [...], total: 45, page: 1 }
  → JS: Card grid güncellenir (animasyonlu geçiş)
  → JS: Detail panel sıfırlanır (yeni seçim beklenir)
  → JS: Scroll pozisyonu sıfırlanır

Genre tab yapısı:
  Aktif: background: var(--theme-primary), text: #fff
  Default: background: rgba(255,255,255,0.08), text: rgba(255,255,255,0.7)
  Scroll: yatay, overflow-x: auto
  Yükseklik: ~32px (WCAG: 48px olmalı)
```

---

## 4. KART ETKİLEŞİMİ

```
Kullanıcı bir karta tıklar
  → JS: Seçili kart vurgulanır (border: 2px solid var(--theme-primary))
  → JS: Detail paneli güncellenir
    → Album art yüklenir (300×圆形)
    → Başlık, sanatçı, tür gösterilir
    → Metadata yüklenir (kalite, boyut, parça sayısı, vb.)
    → Butonlar aktif olur

Kullanıcı "Hemen Çal" tıklar
  → JS: Albümdeki tüm şarkılar sıraya eklenir
  → JS: İlk şarkı çalınır
  → JS: Footer player güncellenir
  → JS: Playlist sayfasına yönlendirme (opsiyonel)

Kullanıcı "Karışık Çal" tıklar
  → JS: Albümdeki şarkılar rastgele sıralanır
  → JS: İlk şarkı çalınır
  → JS: Footer player güncellenir
```

---

## 5. ARAMA

```
Kullanıcı "Sanatçı Adı Ara" kutusuna yazar
  → JS: Gerçek zamanlı arama (300ms debounce)
  → JS: AJAX ile arama yapılır
    → GET /api/search?q=gorgeous&type=artist&page=1
    → Response: { artists: [...], total: 5 }
  → JS: Sonuçlar dropdown'da gösterilir
  → Kullanıcı bir sonuca tıklar
  → JS: Sanatçı sayfasına yönlendirme (/artist/:id)
```

---

## 6. PAGINATION

```
Albüm sayısı 9'dan fazlaysa
  → Sayfalama butonları gösterilir (alt kısım)
  → [1] [2] [3] ... [5] →
  → Kullanıcı bir sayfaya tıklar
  → JS: Yeni sayfa yüklenir (animasyonlu geçiş)
  → JS: Scroll pozisyonu sıfırlanır
```

---

## 7. ERİŞİLEBİLİRLİK

| Kriter | Durum |
|--------|-------|
| Touch target (kart) | ✅ ~190×220px |
| Touch target (tab) | ❌ ~32px → 48px olmalı |
| Touch target (buton) | ✅ 56px, 48px |
| Focus indicator | ✅ |
| Keyboard nav | ✅ |
| ARIA labels | ⚠️ eksik |
| Screen reader | ⚠️ eksik |

---

## 8. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[screens/C-music/albums]] | Albums screen spec |
| [[screens/C-music/album-detail]] | Album detail spec |
| [[screens/00-ascii-art-views]] §3, §4 | ASCII art'lar |
| [[01-component-inventory]] C09, C11, C10 | Bileşenler |
| [[flow/music/01-playback]] | Playback akışı |

---

*Album Browse Flow v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
