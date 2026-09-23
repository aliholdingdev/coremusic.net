---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Footer Player Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Footer Player Flow

## 1. Akış Diyagramı (Player Control Flow)

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│ Footer Player   │
│ Görünür         │
│ (Şarkı çalıyorsa)│
└────────┬────────┘
         │
┌────────▼────────┐
│ Etkileşim Seç   │
└────────┬────────┘
    ┌────┼────────────┐
    │    │            │
┌───▼──┐ │ ┌──▼──────┐ │ ┌──▼──────┐
│Play/ │ │ │Seek     │ │ │Tam      │
│Pause │ │ │İleri/   │ │ │Ekran    │
│Toggle│ │ │Geri     │ │ │Aç       │
└───┬──┘ │ └──┬──────┘ │ └──┬──────┘
    │    │    │        │    │
┌───▼──┐ │ ┌──▼──────┐ │ ┌──▼──────┐
│Sonraki│ │ │Ses      │ │ │Playlist │
│Şarkı │ │ │Azalt/   │ │ │Queue    │
│      │ │ │Artır    │ │ │Göster   │
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
│UI     │ │Seek   │ │Progress │
│Güncelle│ │Pozisyon│ │Bar      │
│Buton  │ │Güncelle│ │Güncelle │
│İkonları│ │       │ │         │
└───────┘ └───────┘ └─────────┘
```

## 2. State Machine

```
┌──────────────┐  Play   ┌──────────────┐  Pause  ┌──────────┐
│    Stopped   │────────▶│   Playing    │────────▶│  Paused  │
└──────────────┘         └──────────────┘         └────┬─────┘
       ▲                                                │
       │ Stop                                          Play│
       │                                                │
┌──────┴───────┘                               ┌───────▼──────┐
│    Stopped   │◀──────────────────────────────│   Playing    │
└──────────────┘                               └──────────────┘
```

## 3. Ekran Akışı

```
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 1: Footer Player (Kompakt)                            │
│                                                              │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ 🎵 Göksel - Sevil Neşelenen          [▶] [⏭] [🔊]     │ │
│ │ ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━  │ │
│ │ 00:02:34                                    00:05:00    │ │
│ └─────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────┐
│ EKRAN 2: Footer Player (Geniş - Desktop)                    │
│                                                              │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │ ┌──────────┐                                            │ │
│ │ │ 🎵 KAPAK │  Göksel - Sevil Neşelenen                  │ │
│ │ │ GÖRSELİ  │  Hayat Rüya Gibi                           │ │
│ │ └──────────┘                                            │ │
│ │                                                         │ │
│ │ [⏮] [▶] [⏹] [⏭]     🔊 ━━━━━━━━━━━━━━ %75           │ │
│ │                          00:02:34    00:05:00           │ │
│ │                                                         │ │
│ │ [🔀] [🔁] [❤️] [⬇️] [⋯]                               │ │
│ └─────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────┘

┌──────────────────────────────────────────────────────────────┐
│ EKRAN 3: Now Playing (Tam Ekran)                            │
│                                                              │
│ ┌─────────────────────────────────────────────────────────┐ │
│ │                                                         │ │
│ │              ┌──────────────────────┐                   │ │
│ │              │                      │                   │ │
│ │              │    🎵 KAPAK GÖRSELİ  │                   │ │
│ │              │    (Large)           │                   │ │
│ │              │                      │                   │ │
│ │              └──────────────────────┘                   │ │
│ │                                                         │ │
│ │              Göksel                                      │ │
│ │              Sevil Neşelenen                             │ │
│ │              Hayat Rüya Gibi                             │ │
│ │                                                         │ │
│ │     ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━      │ │
│ │     00:02:34                        00:05:00           │ │
│ │                                                         │ │
│ │              [⏮] [▶] [⏭]                               │ │
│ │                                                         │ │
│ │              [🔀] [🔁] [❤️] [⬇️] [⋯]                  │ │
│ │                                                         │ │
│ │              🔊 ━━━━━━━━━━━━━━ %75                      │ │
│ │                                                         │ │
│ └─────────────────────────────────────────────────────────┘ │
└──────────────────────────────────────────────────────────────┘
```

## 4. Kontroller

| Kontrol | Aksiyon | Kısayol | Durum |
|---------|---------|---------|:-----:|
| ▶ | Oynat/Duraklat | Space | Toggle |
| ⏭ | Sonraki şarkı | Right Arrow | Her zaman |
| ⏮ | Önceki şarkı | Left Arrow | Her zaman |
| ⏹ | Durdur | S | Her zaman |
| 🔊 | Ses +/- | Up/Down | Her zaman |
| [T] | Seek | — | Sadece Playing |
| 🔀 | Karışık toggle | S | Toggle |
| 🔁 | Tekrar toggle | R | Toggle |
| ❤️ | Favori toggle | F | Toggle |
| ⬇️ | İndir | D | Her zaman |
| ⋯ | Daha fazla | — | Her zaman |

## 5. Hata Senaryoları

| Hata | Çözüm |
|------|-------|
| Şarkı yüklenemedi | Sonraki şarkıya geç |
| Seek başarısız | Mevcut pozisyonda kal |
| Ses değiştirme başarısız | Mevcut ses seviyesi korunur |
| Footer gizli | Otomatik göster (şarkı çalıyorsa) |

## 6. Tier-Bazlı Varyasyonlar

| Tier | Footer Tipi | Kontroller | Seek |
|------|-------------|------------|------|
| **Phone** | Kompakt, swipe up | Swipe gesture | Seek bar |
| **Tablet** | Footer bar | Touch | Seek bar |
| **Embedded** | Sabit footer | Touch | Seek bar |
| **Desktop** | Geniş footer + queue | Click | Seek bar |
| **TV** | Large controls, focus | D-pad | Large seek |
| **Car** | Steering wheel | Voice/physical | Simplified |
| **Watch** | Crown + wrist | Crown/gesture | Crown scroll |

## 7. BEM Sınıfları

| BEM Sınıfı | Açıklama |
|------------|----------|
| `.footer-player` | Footer container |
| `.footer-player__info` | Şarkı bilgisi |
| `.footer-player__art` | Albüm kapağı |
| `.footer-player__controls` | Kontrol butonları |
| `.footer-player__seek` | İlerleme çubuğu |
| `.footer-player__volume` | Ses kontrolü |
| `.footer-player__actions` | Ek aksiyon butonları |
| `.footer-player--expanded` | Tam ekran modu |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
