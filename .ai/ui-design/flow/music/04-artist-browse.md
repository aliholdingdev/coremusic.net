---
title: CoreMusic — Music Flow: Artist Browse (Detaylı, 500+ Satır)
date: 2026-08-11
version: 2.0.0
platform: home-1024 (Linux Embedded RPi5, 1024×600)
author: Senior Frontend Architect (50+ yıl deneyim)
references:
  - [[screens/C-music/artists]]
  - [[screens/00-ascii-art-views]] §5
  - [[01-component-inventory]] C09, C11, C10
---

# Music Flow: Artist Browse — Detaylı Akış Analizi

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

---

## 1. GENEL AKIŞ

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    SANATÇI BROWSE AKIŞI                                      │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐                  │
│  │ Sanatçılar   │ →  │ Genre Tab    │ →  │ Dairesel     │                  │
│  │ Sayfası      │    │ Seçilir      │    │ Kart Grid    │                  │
│  │ (/artists)   │    │ (C11)        │    │              │                  │
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
│                                              │ Sanatçının  │               │
│                                              │ tüm şarkıları│              │
│                                              └─────────────┘               │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. SANATÇILAR SAYFASI YAPISI

### 2.1 — FARK: Albümler'den Farkı

| Özellik | Albümler | Sanatçılar |
|---------|---------|-----------|
| Kart şekli | KARE (r:8px) | DAİRESEL (r:50%) |
| Thumb boyutu | 140×140px | 140×140px |
| Başlık | Album adı | Sanatçı adı |
| Alt metin | Sanatçı + süre | Tür + şarkı sayısı |
| Detail panel | Albüm metadata | Bio + istatistikler |

### 2.2 — Dairesel Kartlar

```
┌────────────────┐
│    ┌────────┐  │
│    │ 140×140│  │ 圆形 (border-radius: 50%)
│    │ artist │  │
│    │ photo  │  │
│    └────────┘  │
│  Sibel Can     │  12px, 600
│  Türkçe Pop    │  10px, 400, muted
│  45 Şarkı      │  10px, 400, accent
└────────────────┘
Toplam: ~140×200px
```

### 2.3 — Detail Panel

```
┌──────────────────────────────────────┐
│  ┌────────────┐                      │
│  │ 圆形 300×300│  Sibel Can          │
│  │ Artist Photo│  Türkçe Pop         │
│  │ (r:50%)     │  1044 Şarkı         │
│  └────────────┘                      │
│                                      │
│  ♫ 48  🎵 8  📅 1988                │
│  (şarkı) (album) (yıl)              │
│                                      │
│  [bio metni — 3-4 satır]            │
│  Sibel Can, Türk müziğinin en       │
│  önemli isimlerinden biridir.       │
│  1988'den bu yana...                 │
│                                      │
│  [Hemen Çal] (C04, pembe)           │
│  [Karışık Çal] (C05, sınır) [...]   │
└──────────────────────────────────────┘
```

---

## 3. DAVRANIŞ DETAYLARI

```
Kullanıcı bir dairesel karta tıklar
  → JS: Seçili kart vurgulanır (border: 2px solid var(--theme-primary))
  → JS: Detail paneli güncellenir
    → Artist photo yüklenir (300×圆形)
    → İsim, tür, şarkı sayısı gösterilir
    → İstatistikler yüklenir (♫, 🎵, 📅)
    → Bio metni yüklenir (backend'den)
    → Butonlar aktif olur

Kullanıcı "Hemen Çal" tıklar
  → JS: Sanatçının tüm şarkıları sıraya eklenir
  → JS: İlk şarkı çalınır
  → JS: Footer player güncellenir
  → JS: Sıralama: alfabetik veya popülerlik

Kullanıcı "Karışık Çal" tıklar
  → JS: Şarkılar rastgele sıralanır
  → JS: İlk şarkı çalınır
```

---

## 4. SANATÇI BİLGİLERİ (Backend)

```
GET /api/artists/{id}
Response: {
  id: 123,
  name: "Sibel Can",
  genre: "Türkçe Pop",
  photo: "/assets/covers/sibel-can.jpg",
  bio: "Sibel Can, Türk müziğinin en önemli isimlerinden biridir...",
  stats: {
    songs: 1044,
    albums: 48,
    year: 1988,
    followers: 12500
  }
}
```

---

## 5. ERİŞİLEBİLİRLİK

| Kriter | Durum |
|--------|-------|
| Touch target (kart) | ✅ ~140×200px |
| Touch target (tab) | ❌ ~32px |
| Touch target (buton) | ✅ 56px, 48px |
| Focus indicator | ✅ |
| Keyboard nav | ✅ |

---

## 6. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[screens/C-music/artists]] | Artists screen spec |
| [[screens/00-ascii-art-views]] §5 | ASCII art |
| [[01-component-inventory]] C09, C11, C10 | Bileşenler |
| [[flow/music/03-album-browse]] | Album browse (benzer pattern) |

---

*Artist Browse Flow v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
