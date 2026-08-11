---
title: CoreMusic — Music Flow: Search (Detaylı, 500+ Satır)
date: 2026-08-11
version: 2.0.0
platform: home-1024 (Linux Embedded RPi5, 1024×600)
author: Senior Frontend Architect (50+ yıl deneyim)
references:
  - [[screens/00-ascii-art-views]] §1 (Header arama)
  - [[01-component-inventory]] C06
---

# Music Flow: Search — Detaylı Akış Analizi

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

---

## 1. GENEL AKIŞ

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         SEARCH AKIŞI                                         │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐                  │
│  │ Header'da    │ →  │ Arama Input  │ →  │ Gerçek       │                  │
│  │ 🔍 tıklanır │    │ Açılır       │    │ Zamanlı      │                  │
│  └──────────────┘    └──────────────┘    │ Arama        │                  │
│                                          └──────┬──────┘                  │
│                                                 │                           │
│                                          ┌──────▼──────┐                   │
│                                          │ Backend     │                   │
│                                          │ Arama       │                   │
│                                          └──────┬──────┘                   │
│                                                 │                           │
│                                          ┌──────▼──────┐                   │
│                                          │ Sonuçlar    │                   │
│                                          │ Dropdown'da │                   │
│                                          └──────┬──────┘                   │
│                                                 │                           │
│                                    ┌────────────┼────────────┐             │
│                                    │            │            │             │
│                               ┌────▼────┐ ┌────▼────┐ ┌────▼────┐        │
│                               │ Şarkı   │ │ Albüm   │ │ Sanatçı │        │
│                               │ Sonucu  │ │ Sonucu  │ │ Sonucu  │        │
│                               └────┬────┘ └────┬────┘ └────┬────┘        │
│                                    │            │            │             │
│                                    └────────────┼────────────┘             │
│                                                 │                           │
│                                          ┌──────▼──────┐                   │
│                                          │ Tıklanır →  │                   │
│                                          │ İlgili sayfa │                   │
│                                          └─────────────┘                   │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. ARAMA ALANI YAPISI

### 2.1 — Header'da Arama

```
Header'da (y:0-60):
┌─────────────────────────────────────────────────────────────────────────────┐
│ "Core Music" [Nav links...] [Bayram Ali ▾] [📶✳] [🔋] [⚙] [⏻]          │
│                                                         ↑                   │
│                                                   Arama ikonu 🔍            │
│                                                   (opsiyonel, header'da)    │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 2.2 — Arama Input'u (Açıldığında)

```
┌── ARAMA PANELİ (header altında, full-width) ─────────────────────────────┐
│                                                                           │
│  🔍 [Arama yapın...                                    ] [✕ kapat]     │
│                                                                           │
│  ── Sonuçlar ──                                                          │
│  ┌─────────────────────────────────────────────────────────────────────┐ │
│  │ 🎵 Şarkılar                                                         │ │
│  │ [♪] Göksel - Sevil Neşelen              ▶ Çal                      │ │
│  │ [♪] Göksel - Kabahat Senin              ▶ Çal                      │ │
│  │ [♪] Bergen - Sana En Güzel              ▶ Çal                      │ │
│  │                                                                     │ │
│  │ 💿 Albümler                                                         │ │
│  │ [thumb] Hayat Rüya Gibi                                           │ │
│  │ [thumb] Bergen - Tüm Şarkılar                                     │ │
│  │                                                                     │ │
│  │ 🎤 Sanatçılar                                                       │ │
│  │ [圆形] Göksel                                                      │ │
│  │ [圆形] Bergen                                                       │ │
│  │                                                                     │ │
│  │ 📋 Çalma Listeleri                                                  │ │
│  │ Pop Şarkıları Ali                                                  │ │
│  │ FAVORITELER                                                         │ │
│  └─────────────────────────────────────────────────────────────────────┘ │
│                                                                           │
└───────────────────────────────────────────────────────────────────────────┘
```

---

## 3. DAVRANIŞ DETAYLARI

### 3.1 — Arama Başlatma

```
Kullanıcı 🔍 ikonuna tıklar
  → JS: Arama paneli açılır (slide-down: 200ms)
  → JS: Arama input'a otomatik focus
  → JS: Placeholder: "Arama yapın..."
  → JS: Previous search varsa gösterilir (history)
```

### 3.2 — Gerçek Zamanlı Arama

```
Kullanıcı yazmaya başlar
  → JS: 300ms debounce (hızlı yazma için bekleme)
  → JS: Min 2 karakter zorunlu
  → JS: AJAX ile arama yapılır
    → GET /api/search?q={query}&limit=10
    → Response: {
        songs: [{ id, title, artist, album, duration }],
        albums: [{ id, title, artist, coverArt }],
        artists: [{ id, name, photo, genre }],
        playlists: [{ id, name, trackCount }]
      }
  → JS: Sonuçlar dropdown'da gösterilir
    → Kategori bazlı gruplandırma
    → Her sonuç için ikon + başlık + aksiyon
```

### 3.3 — Sonuç Seçimi

```
Kullanıcı bir sonuca tıklar
  → Şarkı: Playback başlatılır (flow/music/01-playback)
  → Albüm: Albüm detay sayfasına yönlendirme (/album/:id)
  → Sanatçı: Sanatçı sayfasına yönlendirme (/artist/:id)
  → Playlist: Playlist sayfasına yönlendirme (/playlist/:id)
  → Arama paneli kapatılır
```

### 3.4 — Arama Geçmişi

```
Her arama kaydedilir (LocalStorage)
  → Son 10 arama saklanır
  → Arama paneli açıldığında gösterilir
  → Kullanıcı bir geçmiş aramaya tıklarsa → tekrar aranır
  → "Temizle" butonu ile geçmiş silinebilir
```

---

## 4. ARAMA ALGORİTMASI (Backend)

```
GET /api/search?q={query}&limit=10

Backend arama sırası:
  1. Tam eşleşme (title = query) → en üstte
  2. Kısmi eşleşme (title LIKE %query%) → sıralı
  3. Sanatçı adı eşleşmesi
  4. Albüm adı eşleşmesi
  5. Playlist adı eşleşmesi

Minimum 2 karakter zorunlu
Rate limit: 30 arama/dakika
```

---

## 5. ERİŞİLEBİLİRLİK

| Kriter | Durum |
|--------|-------|
| Touch target (input) | ✅ 56px |
| Touch target (sonuç) | ✅ ~48px yükseklik |
| Focus indicator | ✅ |
| Keyboard nav | ✅ (↑↓ ile seçim, Enter ile onay) |
| Escape ile kapatma | ✅ |
| ARIA | ⚠️ eksik |
| Screen reader | ⚠️ eksik |

---

## 6. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[screens/00-ascii-art-views]] §1 | Header ASCII art |
| [[01-component-inventory]] C06 | Form input |
| [[flow/music/01-playback]] | Playback (şarkı seçildiğinde) |
| [[flow/music/03-album-browse]] | Album browse |
| [[flow/music/04-artist-browse]] | Artist browse |

---

*Search Flow v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
