---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Playback Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Playback Flow

## 1. Akış Diyagramı (Control Flow)

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│ Şarkı Seç       │
│ (Liste/Albüm/   │
│  Arama)         │
└────────┬────────┘
         │
┌────────▼────────┐
│ Footer Player   │
│ Güncelle        │
│ Metadata yükle  │
└────────┬────────┘
         │
┌────────▼────────┐
│ Kontrol Seç     │
└────────┬────────┘
    ┌────┼────────────┐
    │    │            │
┌───▼──┐ │ ┌──▼──────┐ │ ┌──▼──────┐
│Play/ │ │ │Sonraki  │ │ │Önceki   │
│Pause │ │ │Şarkı    │ │ │Şarkı    │
└───┬──┘ │ └──┬──────┘ │ └──┬──────┘
    │    │    │        │    │
┌───▼──┐ │ ┌──▼──────┐ │ ┌──▼──────┐
│Seek  │ │ │Ses      │ │ │Karışık  │
│İleri │ │ │Azalt/   │ │ │Modu     │
│/Geri │ │ │Artır    │ │ │Toggle   │
└───┬──┘ │ └──┬──────┘ │ └──┬──────┘
    │    │    │        │    │
    └────┴────┴────────┴────┘
              │
         ┌────▼────┐
         │State    │
         │Değişikliği│
         └────┬────┘
              │
    ┌─────────┼─────────┐
    │         │         │
┌───▼───┐ ┌──▼────┐ ┌──▼──────┐
│UI     │ │Şarkı  │ │Cache    │
│Güncelle│ │Değiştir│ │Güncelle │
│Progress│ │Metadata│ │         │
│Bar    │ │yükle  │ │         │
│Buton  │ │       │ │         │
│durumu │ │       │ │         │
└───────┘ └───────┘ └─────────┘
```

## 2. State Machine (Durum Makinesi)

```
         ┌──────────┐
    ┌────│ STOPPED  │────┐
    │    └────┬─────┘    │
    │ Play    │    Stop  │
    │    ┌────▼─────┐    │
    │    │ PLAYING  │    │
    │    └────┬─────┘    │
    │ Pause   │    Stop  │
    │    ┌────▼─────┐    │
    └────│ PAUSED   │────┘
         └──────────┘
```

### Durum Tablosu

| Mevcut Durum | Event | Yeni Durum | Aksiyon |
|:------------:|:-----:|:----------:|---------|
| STOPPED | Play | PLAYING | Şarkıyı oynat |
| STOPPED | Next | STOPPED | Sonraki şarkıya geç |
| STOPPED | Previous | STOPPED | Önceki şarkıya geç |
| PLAYING | Pause | PAUSED | Duraklat |
| PLAYING | Stop | STOPPED | Durdur |
| PLAYING | Next | PLAYING | Sonraki şarkıya geç |
| PLAYING | Previous | PLAYING | Önceki şarkıya geç |
| PLAYING | Seek | PLAYING | Pozisyon değiştir |
| PAUSED | Play | PLAYING | Devam et |
| PAUSED | Stop | STOPPED | Durdur |

## 3. Ekran Akışı

```
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 1: Şarkı Seçimi                                       │
│                                                              │
│ +--- Şarkı Listesi --------------------------------------+  |
│ | 🎵 Göksel - Sevil Neşelenen   00:05:00  ★★★★★          |  |
│ | 🎵 Göksel - Kabahat Sensin    00:05:00  ★★★★★          |  |
│ | 🎵 Gangsta - Çubuklar          00:07:19  ★★★★☆          |  |
│ | 🎵 Keyifli Enstrümantal        00:05:13  ★★★☆☆          |  |
│ | 🎵 Erkin Koray - Fantastik     00:10:00  ★★★★★          |  |
│ +----------------------------------------------------------+  |
└──────────────────────────────────────────────────────────────┘
       │
       │ Şarkı seçildi
       ▼
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 2: Footer Player (Her Zaman Görünür)                  │
│                                                              │
│ +----------------------------------------------------------+ |
│ | 🎵 Göksel - Sevil Neşelenen                              | |
│ | 💿 Hayat Rüya Gibi        [⏮] [▶] [⏹] [⏭]    🔊 ━━━ % | |
│ +----------------------------------------------------------+ |
└──────────────────────────────────────────────────────────────┘
       │
       ▼
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 3: Now Playing (Tam Ekran - Opsiyonel)                │
│                                                              │
│ +--- LEFT (60%) ---+  +--- RIGHT (40%) ------------------+ |
│ | 🎵 Tablo          |  | 🎤 Sanatçı Foto                  | |
| | / Şarkı | Süre   |  | +----------+                     | |
| | --------+-------- |  | |  Photo   |                     | |
| | 🎵 S.Neşelenen   |  | +----------+                     | |
| | 🎵 K.Sensin      |  | Göksel - Sevil Neşelenen         | |
| | 🎵 Çubuklar      |  | Hayat Rüya Gibi                  | |
| | -----------------  |  |                                  | |
| | [T] 00:05:00       |  | [❤️] [[V]] [⬇️] [⋯]             | |
| | ━━━━━━━━━━━━ 100% |  |                                  | |
| | [⏮] [▶] [⏭]      |  | Önerilen Sanatçılar              | |
| +-------------------+  +----------------------------------+ |
└──────────────────────────────────────────────────────────────┘
```

## 4. Kontroller

| Kontrol | Aksiyon | Kısayol | Durum |
|---------|---------|---------|:-----:|
| ▶ | Oynat/Duraklat | Space | Play/Pause toggle |
| ⏭ | Sonraki şarkı | Right Arrow | Her zaman aktif |
| ⏮ | Önceki şarkı | Left Arrow | Her zaman aktif |
| 🔊 | Ses +/- | Up/Down Arrow | Her zaman aktif |
| [V] | Karışık toggle | S | Toggle |
| 🔁 | Tekrar toggle | R | Toggle |
| ❤️ | Favori toggle | F | Toggle |
| [T] | Seek (tıklama) | — | Sadece PLAYING |

## 5. Hata Senaryoları

| Hata | Çözüm | Otomatik |
|------|-------|:--------:|
| Şarkı bulunamadı | Sonraki şarkıya geç | ✅ |
| Network hatası | Offline mode (cached) | ✅ |
| Buffer underrun | Pause → 50ms → Resume | ✅ |
| Format desteklenmiyor | Uyarı + sonraki şarkı | ✅ |
| Codec hatası | Uyarı + sonraki şarkı | ✅ |

## 6. Tier-Bazlı Varyasyonlar

| Tier | Player Tipi | Kontroller | Seek |
|------|-------------|------------|------|
| **Phone** | Mini player + swipe up | Swipe gesture | Seek bar |
| **Tablet** | Footer bar | Touch | Seek bar |
| **Embedded** | Footer bar, always visible | Touch | Seek bar |
| **Desktop** | Footer + sidebar queue | Click | Seek bar |
| **TV** | Large controls, focus ring | D-pad | Large seek |
| **Car** | Steering wheel + voice | Voice/physical | Simplified |
| **Watch** | Crown + wrist gesture | Crown/gesture | Crown scroll |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
