---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Playback Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-29
status: active
version: 2.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Playback Flow

## 1. Akış Diyagramı (Decision Flow)

### Control Flow

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

## 1A. State Machine (Durum Makinesi)

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

## 2. Ekran Akışı

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

## 3. Hata Senaryoları

| Hata | Çözüm | Otomatik |
|------|-------|:--------:|
| Şarkı bulunamadı | Sonraki şarkıya geç | ✅ |
| Network hatası | Offline mode (cached) | ✅ |
| Buffer underrun | Pause → 50ms → Resume | ✅ |
| Format desteklenmiyor | Uyarı + sonraki şarkı | ✅ |
| Codec hatası | Uyarı + sonraki şarkı | ✅ |

## 3A. Kontroller

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

## 4. Tier-Bazlı Varyasyonlar

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
## 5. BEM Sınıfları

| BEM sınıfı | Rol (bu dosyadaki kanıt) | Durumlar | Kaynak |
|------------|--------------------------|----------|--------|
| `.btn`, `.btn--primary` | blok — oynatma kontrolleri [⏮] [▶] [⏹] [⏭] (bu dosya L125, L162-L164) | default, hover, active, disabled, loading | 02-component-inventory.md L65 (C04) |
| `.toggle`, `.toggle--active` | blok — Karışık / Tekrar / Favori toggle'ları (bu dosya L166-L168) | off, on, disabled | 02-component-inventory.md L105 (C08) |
| `.slider`, `.slider__fill` | blok — ses seviyesi "🔊 ━━━ %" (bu dosya L125) ve seek [T] (L169) | default, dragging, disabled | 02-component-inventory.md L115 (C09) |
| `.progress`, `.progress__fill` | blok — "Progress Bar" (bu dosya L65) / "━━━━━━━━━━━━ 100%" (L142) | determinate, indeterminate, error | 02-component-inventory.md L175 (C15) |

> **Kaynak:** [[../../02-component-inventory]] v4.1.0 (C01-C19, `updated: 2026-09-29`).

---

## 6. Adımlar

> ⚠️ VERIFICATION REQUIRED — Kaynak dosyada bu bölüm yok; numaralı, tek-eylem adım listesi akış doğrulamasından sonra doldurulacak.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
