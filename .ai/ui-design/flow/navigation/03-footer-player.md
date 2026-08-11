---
title: CoreMusic — Navigation Flow: Footer Player (Detaylı, 500+ Satır)
date: 2026-08-11
version: 2.0.0
platform: home-1024 (Linux Embedded RPi5, 1024×600)
author: Senior Frontend Architect (50+ yıl deneyim)
references:
  - [[screens/00-ascii-art-views]] §1 (Footer bölgesi)
  - [[01-component-inventory]] C12, C13
  - [[ADR-017-dsp-hardware-mode]]
---

# Navigation Flow: Footer Player — Detaylı Akış Analizi

## Platform: Linux Embedded / Raspberry Pi 5 / 1024×600px

---

## 1. FOOTER YAPISI (PNG Layout)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ x:0                                                          x:1024        │
│ y:510 ┌──────────────────────────────────────────────────────────────────┐  │
│       │ [pembe ilerleme çubuğu, y=0, full-width, h:3px]                 │  │
│       │                                                                  │  │
│       │ ┌────────┐ ♪ Şarkı Adı : Göksel - Sevil Neşelen                │  │
│       │ │120×120 │ ● Albümüm  : Hayat Rüya Gibi                        │  │
│       │ │album   │ 🎤 Sanatçı  : Göksel                                 │  │
│       │ │art     │                                                       │  │
│       │ └────────┘    [⏮]  [▶]  [⏹]  [⏭]     Süre: 09:00:00 / 00:05:00│  │
│       │               ◯    ◉    ◯    ◯       Bit rate : 320 kbps        │  │
│       │               (33px çap)               [🔊 ═══▲═══ ] % 100      │  │
│ y:600 └──────────────────────────────────────────────────────────────────┘  │
│                                                                             │
│ Header: sabit, z-index:100                                                  │
│ Footer: sabit, z-index:100                                                 │
│ İçerik: scroll edilebilir (y:60-510)                                       │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. FOOTER BİLEŞENLERİ

### 2.1 — İlerleme Çubuğu (Progress Bar)

```
┌── Progress Bar (y=0, full-width, h:3px) ──────────────────────────────────┐
│                                                                            │
│ ════════════════════════════════════════════════════════════════════════   │
│ ▲ current position                                                         │
│                                                                            │
│ Track: h:3px, rgba(255,255,255,0.2)                                       │
│ Filled: h:3px, var(--theme-primary) (#ff4fd8)                             │
│ Thumb: y:510, 12×12px circle, pembe                                       │
│                                                                            │
│ Touch: Tam genişlik tıklanabilir (seek)                                   │
│ Drag: Sürüklenebilir (seek)                                               │
└────────────────────────────────────────────────────────────────────────────┘
```

### 2.2 — Album Art

| Özellik | Değer |
|---------|-------|
| Boyut | 120×120px |
| Border-radius | 8px |
| Pozisyon | Sol köşe, padding: 12px |
| Yükleme | Lazy loading + placeholder |
| Click | Now Playing sayfasına yönlendirme |

### 2.3 — Metadata Alanı

```
♪ Şarkı Adı : Göksel - Sevil Neşelen     ← 14px, 600
● Albümüm  : Hayat Rüya Gibi              ← 12px, 400, muted
🎤 Sanatçı  : Göksel                       ← 12px, 400, muted
```

| Alan | Font | Renk |
|------|------|------|
| Etiket (♪, ●, 🎤) | 12px | `rgba(255,255,255,0.5)` |
| Değer | 12px | `rgba(255,255,255,0.9)` |

### 2.4 — Transport Butonları

```
    [⏮]        [▶]        [⏹]        [⏭]
   Prev       Play       Stop       Next

   Her biri: 33×33px daire
   Padding: 7.5px (toplam hit area: 48×48px)
   Renk: rgba(255,255,255,0.8) (ikon)
   Arka plan: yok (saydam)
   Border-radius: 50%
   
   Play durumu:
     ▶ (boş daire) → ▉ (dolu) animasyonlu geçiş
     ◯ (boş) → ◉ (dolu) daire göstergesi
```

### 2.5 — Süre Göstergesi

```
Süre: 09:00:00 / 00:05:00

Format: SS:DD:SS / SS:DD:SS
Font: 10px, Arima
Renk: rgba(255,255,255,0.6)
Pozisyon: Transport butonlarının altında
```

### 2.6 — Volume Slider

```
┌── Volume ──────────────────────────────────────────────────────────────┐
│                                                                        │
│  [🔊] ═══════════════════════════════════════════▲═════════ % 100    │
│  20×20px  145px track (1024px için)               thumb                │
│  ikon     15px yükseklik                          12×12px              │
│                                                                        │
│  Track: rgba(255,255,255,0.2)                                         │
│  Filled: var(--theme-primary)                                         │
│  Mute: 🔊 → 🔇 (ikon değişir)                                       │
│                                                                        │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 3. DAVRANIŞ DETAYLARI

### 3.1 — İlk Yükleme

```
Sayfa yüklenir
  → Footer player gösterilir (son çalınan şarkı bilgileri ile)
  → Veya varsayılan durum (boş)
  → Progress bar: 0%
  → Transport: ▶ (çalma bekliyor)
  → Volume: Son kaydedilen değer (cookie'den)
```

### 3.2 — Şarkı Seçimi

```
Herhangi bir sayfada şarkı seçildiğinde
  → Footer player güncellenir (animasyonlu geçiş)
  → Album art yüklenir (fade-in: 200ms)
  → Metadata güncellenir
  → Süre gösterilir
  → Transport ▶ → ▉ (çalma başladı)
  → Progress bar sıfırlanır
  → Süre sayacı başlar (her 1000ms'de güncelleme)
```

### 3.3 — Progress Bar Etkileşimi

```
Kullanıcı progress bar'a tıklar
  → JS: Tıklama pozisyonu hesaplanır (pixel → percentage)
  → JS: Yeni süre hesaplanır (percentage × toplam süre)
  → JS: Audio Engine'e seek komutu gönderilir
  → JS: Progress bar güncellenir
  → JS: Süre göstergesi güncellenir

Kullanıcı progress bar'ı sürükler
  → JS: Sürükleme pozisyonu takip edilir
  → JS: Progress bar güncellenir (canlı)
  → JS: Süre göstergesi güncellenir (canlı)
  → JS: Bırakma → Audio Engine'e seek komutu
```

### 3.4 — Transport Etkileşimi

```
▶ (Play) tıklanır
  → JS: PlayerUI.js → #play()
  → JS: Audio Engine'e play komutu
  → JS: ▶ → ▉ (animasyonlu geçiş)
  → JS: Süre sayacı başlar

⏸ (Pause) tıklanır
  → JS: PlayerUI.js → #pause()
  → JS: Audio Engine'e pause komutu
  → JS: ▉ → ▶ (animasyonlu geçiş)
  → JS: Süre sayacı durur

⏹ (Stop) tıklanır
  → JS: PlayerUI.js → #stop()
  → JS: Audio Engine'e stop komutu
  → JS: Progress bar sıfırlanır
  → JS: Süre 00:00:00'a döner

⏮ (Previous) tıklanır
  → JS: PlayerUI.js → #previousTrack()
  → JS: Bir önceki şarkıya geç
  → JS: Footer güncellenir

⏭ (Next) tıklanır
  → JS: PlayerUI.js → #nextTrack()
  → JS: Bir sonraki şarkıya geç
  → JS: Footer güncellenir
```

### 3.5 — Volume Etkileşimi

```
Kullanıcı volume slider'ı sürükler
  → JS: Yeni ses seviyesi hesaplanır (0.0 - 1.0)
  → JS: Audio Engine'e volume komutu
  → JS: Slider güncellenir
  → JS: İkon güncellenir (🔊 → 🔇 veya tam tersi)
  → JS: Cookie'ye kaydedilir

Kullanıcı 🔊 ikonuna tıklar
  → JS: Mute toggle
  → JS: Önceki ses seviyesi kaydedilir
  → JS: Ses 0'a ayarlanır
  → JS: İkon 🔇'ye dönüşür
```

---

## 4. SAYFA DEĞİŞİKLİĞİNDE DAVRANIŞ

```
Kullanıcı sayfa değiştirir (SPA routing)
  → Footer player KORUNUR (çalma devam eder)
  → Header güncellenir (active link)
  → İçerik güncellenir (DOM patching)
  → Footer'daki hiçbir şey değişmez
  → Progress bar devam eder
  → Süre sayacı devam eder
```

---

## 5. PERFORMANS NOTLARI

| Metrik | Hedef |
|--------|-------|
| Footer render | <16ms (60fps) |
| Progress bar güncelleme | Her 1000ms'de |
| Metadata güncelleme | Şarkı değiştiğinde |
| Album art yükleme | Lazy loading |
| Font rendering | FOUT (Arima) |

---

## 6. ERİŞİLEBİLİRLİK

| Kriter | Durum |
|--------|-------|
| Touch target (album art) | ✅ 120×120px |
| Touch target (transport) | ✅ 48×48px (pad ile) |
| Touch target (seek bar) | ✅ full-width, 15px |
| Touch target (volume) | ✅ 145px, 15px |
| Focus indicator | ✅ |
| Keyboard nav | ✅ (Space=play/pause, ←→=seek) |
| ARIA | ⚠️ eksik |
| Screen reader | ⚠️ eksik |

---

## 7. İLGİLİ DOSYALAR

| Dosya | Amaç |
|-------|------|
| [[screens/00-ascii-art-views]] §1 | Footer ASCII art |
| [[01-component-inventory]] C12, C13 | Bileşenler |
| [[ADR-017-dsp-hardware-mode]] | DSP hardware |
| [[flow/music/01-playback]] | Playback akışı |
| [[flow/navigation/01-spa-routing]] | SPA routing |
| [[flow/navigation/02-header-nav]] | Header nav |

---

*Footer Player Flow v2.0.0 — CoreMusic UI Design System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-11*
*Mode: Red Team · Human Mode · Truth Mode*
