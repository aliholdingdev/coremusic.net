---
title: CoreMusic — Music Flow: Playlist Queue (Detaylı, 500+ Satır)
date: 2026-08-11
version: 2.0.0
platform: home-1024 (Linux Embedded RPi5, 1024×600)
author: Senior Frontend Architect (50+ yıl deneyim)
references:
  - [[screens/D-player/playlist]]
  - [[screens/00-ascii-art-views]] §6
  - [[01-component-inventory]] C13, C12, C09
---

# Music Flow: Playlist Queue — Detaylı Akış Analizi

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

---

## 1. GENEL AKIŞ

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                    PLAYLIST QUEUE AKIŞI                                       │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐                  │
│  │ Playlist     │ →  │ Şarkı        │ →  │ Sıra        │                  │
│  │ Sayfası      │    │ Seçilir      │    │ Güncellenir │                  │
│  │ (/playlist/  │    │ (satır       │    │ (JS array)  │                  │
│  │  :id)        │    │  tıklama)    │    │              │                  │
│  └──────────────┘    └──────────────┘    └──────────────┘                  │
│                                                     │                       │
│                                              ┌──────┴──────┐               │
│                                              │              │               │
│                                         ┌────▼────┐  ┌─────▼─────┐        │
│                                         │ Playback│  │ Footer    │        │
│                                         │ Başlatır│  │ Güncellenir│       │
│                                         └────┬────┘  └───────────┘        │
│                                              │                             │
│                                         ┌────▼────┐                        │
│                                         │ Sıradaki │                       │
│                                         │ Şarkılar │                       │
│                                         │ Paneli   │                       │
│                                         └─────────┘                        │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. PLAYLIST SAYFASI YAPISI

### 2.1 — Ana Layout (60/40 Pattern)

```
┌── TABLO (sol ~65%) ──────────────────────────────────────────────────────┐
│ / | Şarkı Adı         | Albüm Adı        | Sanatçı | Süre    | ★         │
│───│──────────────────│──────────────────│─────────│────────│──────────│
│[♪]│Göksel-Sevil Neş. │Hayat Rüya Gibi   │Göksel   │00:00:00│ ★★★★★   │
│[♪]│Göksel-Sevil Neş. │Hayat Rüya Gibi   │Göksel   │00:00:00│ ★★☆☆☆   │
│[♪]│Göksel-Sevil Neş. │Hayat Rüya Gibi   │Göksel   │00:00:00│ ★★★★★   │
│[♪]│Göksel-Sevil Neş. │Hayat Rüya Gibi   │Göksel   │00:00:00│ PEMBE    │
│[♪]│Göksel-Sevil Neş. │Hayat Rüya Gibi   │Göksel   │00:00:00│ ★★★★★   │
│[♪]│Göksel-Sevil Neş. │Hayat Rüya Gibi   │Göksel   │00:00:00│ ★★★★★   │
│[♪]│Göksel-Sevil Neş. │Hayat Rüya Gibi   │Göksel   │00:00:00│ ★★★★★   │
└──────────────────────────────────────────────────────────────────────────┘

Tablo sütunları:
  # (30px): Sıra numarası veya ♪ ikonu
  Şarkı Adı (%35): Şarkı başlığı + thumb
  Albüm Adı (%25): Albüm adı
  Sanatçı (%15): Sanatçı adı
  Süre (60px): 00:00:00 formatı
  ★ (100px): 5 yıldız (C12)
```

### 2.2 — Sağ Panel

```
┌── SAĞ PANEL (~300px) ────────────────────────────────────────────────────┐
│ [圆形 Artist Photo 100×100px]                                             │
│ Göksel - Sevil Neşelen                                                   │
│ Göksel, Hayat Rüya Gibi                                                  │
│                                                                           │
│ [♫][♥][▼][⋯]  (aksiyon ikonları, 44×44px hit area)                     │
│                                                                           │
│ ── Önerilen Sanatçılar ──            ── Takip Edilen Sanatçılar ──      │
│ [thumb×4 grid]                       [thumb×4 grid]                       │
│                                                                           │
│ ── Son Öneriler ──                   ── Tüm Sanatçılar ──               │
│ [thumb×4 grid]                       [thumb×4 grid]                       │
└──────────────────────────────────────────────────────────────────────────┘
```

---

## 3. SIRA YÖNETİMİ

### 3.1 — JS Array Yapısı

```javascript
// Playlist queue
const playlist = {
  id: 'playlist-123',
  name: 'Göksel - Sevil Neşelen',
  tracks: [
    { id: 1, title: 'Göksel - Sevil Neşelen', album: 'Hayat Rüya Gibi', artist: 'Göksel', duration: 300, rating: 5 },
    { id: 2, title: 'Göksel - Kabahat Senin', album: 'Hayat Rüya Gibi', artist: 'Göksel', duration: 240, rating: 4 },
    { id: 3, title: 'Göksel - Sevil Neşelen', album: 'Hayat Rüya Gibi', artist: 'Göksel', duration: 300, rating: 5 },
    // ... daha fazla şarkı
  ],
  currentIndex: 0,
  shuffle: false,
  repeat: 'none' // 'none' | 'one' | 'all'
};
```

### 3.2 — Sıra Değişikliği

```
Kullanıcı bir satırı sürükler (drag & drop)
  → JS: Sürükleme başlatılır (touchstart/mousedown)
  → JS: Ghost element oluşturulur (saydam kopya)
  → JS: Parmağın hareketi takip edilir (touchmove/mousemove)
  → JS: Hedef konum hesaplanır (hangi index'e bırakılacak)
  → JS: Sürüklenen öğe hedef konuma taşınır (animasyonlu)
  → JS: Bırakma (touchend/mouseup)
  → JS: Playlist array'i güncellenir
  → JS: Backend'e senkronizasyon isteği gönderilir
    → PUT /api/playlists/{id}/reorder
    → Request: { trackIds: [newOrder] }
  → JS: Tablo yeniden render edilir
```

### 3.3 — Şarkı Ekleme

```
Kullanıcı "Şarkı Ekle" butonuna basar
  → Arama paneli açılır
  → Kullanıcı şarkı arar
  → Sonuçlardan birini seçer
  → Şarkı playlist'e eklenir (sona)
  → Backend'e bildirilir
    → POST /api/playlists/{id}/tracks
    → Request: { trackId: xxx }
  → Tablo güncellenir (animasyonlu ekleme)
```

### 3.4 — Şarkı Silme

```
Kullanıcı bir satırda "Sil" butonuna basar (veya swipe ile)
  → Onay dialog'u gösterilir ("Bu şarkıyı playlist'ten kaldırmak istediğinize emin misiniz?")
  → Onay → Şarkı playlist'ten çıkarılır
  → Backend'e bildirilir
    → DELETE /api/playlists/{id}/tracks/{trackId}
  → Tablo güncellenir (animasyonlu silme)
  → Eğer silinen şarkı şu an çalınıyorsa → sonraki şarkıya geç
```

---

## 4. AKTİF ŞARKI İŞARETLEMESİ

### 4.1 — Vurgulama

```
Aktif satır:
  background: rgba(255,79,216,0.15)  ← pembe arka plan
  border-left: 3px solid var(--theme-primary)  ← sol kenar
  text color: var(--theme-primary)  ← başlık rengi
```

### 4.2 — Scroll

```
Aktif şarkı görünür alanda değilse
  → Tablo otomatik olarak aktif satıra scroll edilir
  → Smooth scroll: behavior: 'smooth'
  → Offset: Üstte 60px (header) + 10px padding
```

---

## 5. YILDIZ DERECELENDİRME (C12)

### 5.1 — Etkileşim

```
Kullanıcı bir yıldıza tıklar
  → JS: Yeni puan hesaplanır (1-5)
  → JS: Backend'e bildirilir
    → PUT /api/tracks/{id}/rating
    → Request: { rating: 4 }
  → JS: Yıldızlar güncellenir (animasyonlu)
  → JS: Tooltip gösterilir ("4/5 yıldız")
```

### 5.2 — Touch Target Düzeltmesi

```
WCAG uyumluluğu için:
  → Her yıldız 20×20px (görsel)
  → Ama hit area 48×48px (pad ile)
  → Veya tam satır tıklanabilir (tüm 5 yıldız tek hit area)
```

---

## 6. SAĞ PANEL DETAYLARI

### 6.1 — Aksiyon İkonları

| İkon | İşlev | Backend |
|------|-------|---------|
| ♫ | Tümünü çal | POST /api/playlists/{id}/play |
| ♥ | Favorilere ekle | POST /api/favorites |
| ▼ | İndir | POST /api/downloads |
| ⋯ | Daha fazla (menü) | — |

### 6.2 — Önerilen Sanatçılar

```
Backend'den öneriler yüklenir
  → GET /api/recommendations/artists?playlistId={id}
  → Response: { artists: [{ id, name, photo, genre }] }
  → 4× grid'de gösterilir
  → Her kart tıklanabilir → Sanatçı sayfasına yönlendirme
```

---

## 7. ERİŞİLEBİLİRLİK

| Kriter | Durum |
|--------|-------|
| Touch target (satır) | ❌ ~40px → 48px olmalı |
| Touch target (yıldız) | ❌ ~20px → 48px olmalı |
| Touch target (ikon) | ✅ 44px |
| Focus indicator | ✅ |
| Keyboard nav | ✅ (Tab ile gezinme) |
| Drag & drop | ⚠️ Touch için alternatif gerekli |
| Screen reader | ⚠️ ARIA labels eksik |

---

## 8. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[screens/D-player/playlist]] | Playlist screen spec |
| [[screens/00-ascii-art-views]] §6 | ASCII art |
| [[01-component-inventory]] C13, C12 | Bileşenler |
| [[flow/music/01-playback]] | Playback akışı |

---

*Playlist Queue Flow v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
