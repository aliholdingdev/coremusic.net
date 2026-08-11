---
title: CoreMusic — Music Flow: Playback (Detaylı, 500+ Satır)
date: 2026-08-11
version: 2.0.0
platform: home-1024 (Linux Embedded RPi5, 1024×600)
author: Senior Frontend Architect (50+ yıl deneyim)
references:
  - [[screens/B-home/dashboard]]
  - [[screens/D-player/playlist]]
  - [[screens/D-player/video-playback]]
  - [[screens/00-ascii-art-views]] §1, §6, §7
  - [[01-component-inventory]] C09, C13, C12
  - [[ADR-017-dsp-hardware-mode]]
  - [[ADR-025-professional-eq-system]]
---

# Music Flow: Playback — Detaylı Akış Analizi

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

> **⚠️ Ses Sistemi:** PHP backend (Media Service) + C++20 Audio Engine (Neva Engine) kombinasyonu.
> Backend: `media.coremusic.net:5000/6000` → Metadata, playlist yönetimi
> Audio Engine: `audio.coremusic.net:9741/9742` → ASIO/WASAPI, DSP, mixer

---

## 1. GENEL AKIŞ DİYAGRAMI

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                         PLAYBACK AKIŞI                                       │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐                  │
│  │ Şarkı        │ →  │ Frontend     │ →  │ Backend      │                  │
│  │ Seçilir      │    │ JS: Router   │    │ PHP: Media   │                  │
│  │ (kart/liste) │    │ #navigate    │    │ Service      │                  │
│  └──────────────┘    └──────────────┘    └──────────────┘                  │
│                                                     │                       │
│                                              ┌──────┴──────┐               │
│                                              │              │               │
│                                         ┌────▼────┐  ┌─────▼─────┐        │
│                                         │ Metadata│  │ Dosya     │        │
│                                         │ Yüklenir│  │ URL'i     │        │
│                                         └────┬────┘  └─────┬─────┘        │
│                                              │              │               │
│                                              └──────┬───────┘               │
│                                                     │                       │
│                                              ┌──────▼──────┐               │
│                                              │ Audio Engine │              │
│                                              │ (C++ JUCE)   │              │
│                                              └──────┬──────┘               │
│                                                     │                       │
│                                              ┌──────▼──────┐               │
│                                              │ ASIO/WASAPI  │              │
│                                              │ Çıkış        │              │
│                                              └──────┬──────┘               │
│                                                     │                       │
│                                              ┌──────▼──────┐               │
│                                              │ Footer      │               │
│                                              │ Player      │               │
│                                              │ Güncellenir │               │
│                                              └─────────────┘               │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. ŞARKI SEÇME KAYNAKLARI

### 2.1 — Ana Sayfa (Dashboard)

```
┌── NOW PLAYING CARD ──────────────────────────────────────────────┐
│ [Album Art 100×100]  Göksel - Sevil Neşelen                     │
│                       Hayat Rüya Gibi                            │
│                       Göksel                                     │
│ 00:05:00 ═══════════════════════════ 00:05:00                   │
│              ▲ seek bar (pembe)                                  │
└──────────────────────────────────────────────────────────────────┘

Kullanıcı seek bar'a dokunur
  → Pozisyon değişir
  → Audio engine'e seek komutu gönderilir
  → Zaman göstergesi güncellenir
```

### 2.2 — Albümler Sayfası

```
┌── CARD GRID ───────────────┐  ┌── DETAIL PANEL ──────────────┐
│ ┌──────┐ ┌──────┐ ┌──────┐│  │ [300×300 Album Art]           │
│ │ kart │ │ kart │ │ kart ││  │ Nobetci Eczane Ferhat Kasetleri│
│ └──────┘ └──────┘ └──────┘│  │                                │
│ ┌──────┐ ┌──────┐ ┌──────┐│  │ [Hemen Çal] (pembe)           │
│ │ kart │ │ kart │ │ kart ││  │ [Karışık Çal] (sınır)         │
│ └──────┘ └──────┘ └──────┘│  │ [...]                          │
└────────────────────────────┘  └────────────────────────────────┘

Kullanıcı "Hemen Çal" butonuna basar
  → Albümdeki tüm şarkılar sıraya eklenir
  → İlk şarkı çalmaya başlar
  → Footer player güncellenir
```

### 2.3 — Playlist Sayfası

```
┌── TABLO ──────────────────────────────────────────────────────┐
│ [♪] Göksel - Sevil Neşelen  │ Hayat Rüya Gibi │ 00:00:00    │
│ [♪] Göksel - Sevil Neşelen  │ Hayat Rüya Gibi │ 00:00:00    │
│ [♪] Göksel - Sevil Neşelen  │ Hayat Rüya Gibi │ PEMBE VURGU │
│ [♪] Göksel - Sevil Neşelen  │ Hayat Rüya Gibi │ 00:00:00    │
└───────────────────────────────────────────────────────────────┘

Kullanıcı bir satıra tıklar
  → Seçili şarkı çalmaya başlar
  → Aktif satır pembe vurgu alır
  → Footer player güncellenir
```

### 2.4 — Arama

```
┌── ARAMA SONUÇLARI ──────────────────────────────────────────┐
│ Şarkı: Göksel - Sevil Neşelen    [♪] ▶ Çal                  │
│ Albüm: Hayat Rüya Gibi           [♪] ▶ Çal                  │
│ Sanatçı: Göksel                  [♪] ▶ Tümünü Çal           │
└──────────────────────────────────────────────────────────────┘

Kullanıcı sonuç tıklar
  → İlgili şarkı çalınır
  → Veya sanatçının tüm şarkıları sıraya eklenir
```

---

## 3. AUDIO ENGINE MİMARİSİ

### 3.1 — Katmanlar

```
┌── Frontend (JS) ──────────────────────────────────────────────┐
│ PlayerUI.js → transport controls → seek → volume              │
│   ↓ (WebSocket: audio.coremusic.net:9742)                     │
├── Audio Engine (C++ JUCE) ────────────────────────────────────┤
│ Neva Engine → Mixer → DSP Chain → EQ → Compressor → Limiter  │
│   ↓ (ASIO SDK 2.3.4)                                         │
├── Driver (ASIO/WASAPI) ──────────────────────────────────────┤
│ ASIO Exclusive Mode → Low Latency (~10ms)                     │
│ WASAPI Shared Mode → Fallback (~20ms)                         │
│   ↓ (Hardware)                                                │
├── Donanım ────────────────────────────────────────────────────┤
│ XMOS XU316 → PCM3168A → Amplifikatör → Hoparlörler          │
└───────────────────────────────────────────────────────────────┘
```

### 3.2 — Ses Formatı

| Özellik | Değer |
|---------|-------|
| Sample Format | Float32 (32-bit) |
| Sample Rate | 48kHz |
| Kanal | 2.0 → 8.1 (surround) |
| Buffer | 512 sample (varsayılan) |
| Latency | <10ms (ASIO), <20ms (WASAPI) |

### 3.3 — DSP Zinciri

```
Sinyal Girişi
  → EQ (31-band parametrik)
  → Compressor (otomatik gain)
  → Limiter (clipping önleme)
  → Crossover (8.1 surround için)
  → Mixer (kanal başına gain)
  → Sinyal Çıkışı
```

---

## 4. FOOTER PLAYER DETAYLARI

### 4.1 — Yapı

```
┌── FOOTER (y:510-600, h:90px) ──────────────────────────────────────────┐
│ [pembe ilerleme çubuğu, y=0, full-width, h:3px]                        │
│                                                                          │
│ ┌────────┐ ♪ Şarkı Adı : Göksel - Sevil Neşelen                        │
│ │120×120 │ ● Albümüm  : Hayat Rüya Gibi                                │
│ │Album   │ 🎤 Sanatçı  : Göksel                                         │
│ │Art     │                                                               │
│ └────────┘    [⏮]  [▶]  [⏹]  [⏭]     Süre: 09:00:00 / 00:05:00      │
│               ◯    ◉    ◯    ◯       Bit rate : 320 kbps               │
│               (33px çap)               [🔊 ═══▲═══ ] % 100              │
└──────────────────────────────────────────────────────────────────────────┘
```

### 4.2 — Bileşen Boyutları

| Bileşen | Boyut | Touch Target |
|---------|-------|-------------|
| Album Art | 120×120px | ✅ |
| Track Info | ~300×60px | N/A |
| Prev (⏮) | 33px çap | ✅ (pad ile 48px) |
| Play (▶) | 33px çap | ✅ (pad ile 48px) |
| Stop (⏹) | 33px çap | ✅ (pad ile 48px) |
| Next (⏭) | 33px çap | ✅ (pad ile 48px) |
| Seek bar | Full-width × 15px | ✅ |
| Volume slider | 145px × 15px | ✅ |
| Volume ikon | 20×20px | ✅ (pad ile 44px) |

### 4.3 — Seek Bar

```
┌── Seek Bar (h:15px, full-width) ─────────────────────────────┐
│                                                               │
│ 00:05:00 ═══════════════════════════════════════ 00:05:00   │
│            ▲                                                   │
│            thumb: 12×12px, pembe                               │
│            track: 3px, rgba(255,255,255,0.2)                  │
│            filled: 3px, var(--theme-primary)                   │
│                                                               │
└───────────────────────────────────────────────────────────────┘

Dokunmatik etkileşim:
  → Parmağı seek bar'a dokun → pozisyon değişir
  → Sürükleme (drag) → sürekli güncelleme
  → Bırak → pozisyona git
```

### 4.4 — Transport Butonları

```
┌── Transport ──────────────────────────────────────────────────┐
│                                                               │
│    [⏮]        [▶]        [⏹]        [⏭]                    │
│   33×33      33×33      33×33      33×33                     │
│   (pad:      (pad:      (pad:      (pad:                     │
│    48×48)     58×58)     48×48)     48×48)                   │
│                                                               │
│   ◯           ◉           ◯           ◯                      │
│  boş         dolu       boş         boş                      │
│  rgba(255,   #fff       rgba(255,   rgba(255,               │
│  255,255,              255,255,    255,255,                  │
│  0.3)                  0.3)        0.3)                      │
│                                                               │
│   play:     ◉ → ▶ (animasyonlu geçiş)                       │
│   stop:     ◉ → ⏹ (animasyonlu geçiş)                       │
│                                                               │
└───────────────────────────────────────────────────────────────┘
```

### 4.5 — Volume Slider

```
┌── Volume ─────────────────────────────────────────────────────┐
│                                                               │
│  [🔊] ═══════════════════▲═════════════════ % 100            │
│  20×20   145px track       thumb                              │
│  ikon    15px yükseklik    12×12px                            │
│                                                               │
│  Mute: 🔊 → 🔇 (ikon değişir)                               │
│  Min: 0%  Max: 100%                                          │
│  Adım: %1 (inç)                                              │
│                                                               │
└───────────────────────────────────────────────────────────────┘
```

---

## 5. DAVRANIŞ DETAYLARI

### 5.1 — Şarkı Başlatma

```
Kullanıcı bir şarkı seçer
  → JS: PlayerUI.js → #selectTrack(trackId)
  → JS: AJAX ile metadata yüklenir
    → GET /api/tracks/{id}
    → Response: { title, artist, album, duration, fileUrl, coverArt }
  → JS: Footer player güncellenir
    → Album art yüklenir
    → Şarkı adı, albüm, sanatçı yazılır
    → Süre gösterilir
  → JS: WebSocket ile Audio Engine'e bildirilir
    → WS: { action: "play", url: fileUrl, trackId: trackId }
  → C++: Neva Engine dosyayı yükler
    → Dosya formatı algılanır (FLAC, MP3, WAV)
    → Sample rate dönüşümü (gerekirse)
    → DSP zinciri uygulanır
  → C++: ASIO/WASAPI ile sinyal çıkışa gönderilir
  → JS: Transport durumu güncellenir
    → ▶ butonu ⏸'ye dönüşür
    → Süre sayacı çalışır (her 1000ms'de güncelleme)
    → Seek bar ilerler
```

### 5.2 — Duraklatma

```
Kullanıcı ⏸ butonuna basar
  → JS: PlayerUI.js → #pauseTrack()
  → JS: WebSocket ile Audio Engine'e bildirilir
    → WS: { action: "pause" }
  → C++: ASIO callback durdurulur (sinyal sıfırlanır)
  → C++: Mevcut pozisyon kaydedilir
  → JS: Transport durumu güncellenir
    → ⏸ butonu ▶'ye dönüşür
    → Süre sayacı durur
    → Seek bar durur
```

### 5.3 — Durdurma

```
Kullanıcı ⏹ butonuna basar
  → JS: PlayerUI.js → #stopTrack()
  → JS: WebSocket ile Audio Engine'e bildirilir
    → WS: { action: "stop" }
  → C++: ASIO callback durdurulur
  → C++: DSP durumu sıfırlanır
  → JS: Transport durumu güncellenir
    → ⏹ butonu aktif kalır
    → Süre 00:00:00'a döner
    → Seek bar başa döner
    → Footer player metadata korunur (sonraki çalma için)
```

### 5.4 — Önceki/Sonraki Şarkı

```
Kullanıcı ⏮ butonuna basar
  → JS: PlayerUI.js → #previousTrack()
  → JS: Playlist array'inde bir önceki index'e geç
  → Eğer baştaysa → son şarkıya sar (loop)
  → Seçili şarkı güncellenir
  → Playback başlatılır (5.1 adımı)

Kullanıcı ⏭ butonuna basar
  → JS: PlayerUI.js → #nextTrack()
  → JS: Playlist array'inde bir sonraki index'e geç
  → Eğer sondaysa → ilk şarkıya sar (loop)
  → Seçili şarkı güncellenir
  → Playback başlatılır (5.1 adımı)
```

### 5.5 — Seek (Pozisyon Değiştirme)

```
Kullanıcı seek bar'a dokunur veya sürükler
  → JS: PlayerUI.js → #seekTo(position)
  → JS: Yeni pozisyon hesaplanır (saniye cinsinden)
  → JS: WebSocket ile Audio Engine'e bildirilir
    → WS: { action: "seek", position: seconds }
  → C++: Dosyada ilgili pozisyona gidilir
  → C++: DSP zinciri yeniden başlatılır
  → JS: Süre göstergesi güncellenir
  → JS: Seek bar güncellenir
```

### 5.6 — Ses Seviyesi

```
Kullanıcı volume slider'ı sürükler
  → JS: PlayerUI.js → #setVolume(percent)
  → JS: Yeni ses seviyesi hesaplanır (0.0 - 1.0)
  → JS: WebSocket ile Audio Engine'e bildirilir
    → WS: { action: "volume", level: 0.85 }
  → C++: Gain node güncellenir
  → JS: Volume slider güncellenir
  → JS: İkon güncellenir (🔊 → 🔇 veya tam tersi)
  → Cookie'ye kaydedilir (sonraki oturum için)
```

### 5.7 — Otomatik Geçiş

```
Şarkı biter (süre = mevcut süre)
  → C++: Audio Engine bildirir: { event: "trackEnded", trackId: xxx }
  → JS: PlayerUI.js → #onTrackEnded()
  → JS: Repeat modu kontrol edilir
    → Tekrar yok → Sonraki şarkıya geç
    → Tekrar 1 → Aynı şarkı tekrar başlar
    → Tüm listeyi tekrar → Sonraki şarkıya geç (sondaysa başa)
  → Shuffle aktifse → Rastgele şarkı seçilir
  → Yeni şarkı başlatılır (5.1 adımı)
```

---

## 6. REPEAT ve SHUFFLE MODLARI

### 6.1 — Repeat

| Mod | Davranış |
|-----|----------|
| Repeat Yok | Liste bitince dur |
| Repeat 1 | Aynı şarkı tekrar başlar |
| Repeat Liste | Liste bitince başa sar |

### 6.2 — Shuffle

| Mod | Davranış |
|-----|----------|
| Shuffle Kapalı | Sıralı çalma |
| Shuffle Açık | Rastgele sıralama |

---

## 7. HATA DURUMLARI

| Hata | Davranış |
|------|----------|
| Dosya bulunamadı | "Şarkı yüklenemedi" mesajı, sonraki şarkıya geç |
| Format desteklenmiyor | "Bu format desteklenmiyor" mesajı |
| Network hatası | Offline modda önbellekten çalma |
| ASIO cihaz hatası | WASAPI fallback |
| Cihaz çıkarma | Sonraki mevcut cihaza geç |
| CPU kullanımı çok yüksek | DSP kalitesini düşür |

---

## 8. PERFORMANS NOTLARI

| Metrik | Hedef |
|--------|-------|
| Playback başlatma | <200ms |
| Seek tepkisi | <50ms |
| Volume değişikliği | Anında |
| Footer güncelleme | <16ms (60fps) |
| Audio latency | <10ms (ASIO) |
| Buffer underrun | Fade-out → 50ms sessizlik → restart |

---

## 9. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[screens/B-home/dashboard]] | Ana sayfa |
| [[screens/D-player/playlist]] | Playlist |
| [[screens/D-player/video-playback]] | Video playback |
| [[screens/00-ascii-art-views]] §1, §6, §7 | ASCII art'lar |
| [[01-component-inventory]] C09, C13, C12 | Bileşenler |
| [[ADR-017-dsp-hardware-mode]] | DSP hardware |
| [[ADR-025-professional-eq-system]] | EQ sistemi |

---

*Playback Flow v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
