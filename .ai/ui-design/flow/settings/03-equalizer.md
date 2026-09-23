---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Equalizer Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Equalizer Flow

## 1. Akış Diyagramı (EQ Settings)

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│ EQ Modu Seç     │
└────────┬────────┘
    ┌────┴────┐
    │         │
┌───▼───┐ ┌──▼───────┐
│Preset │ │Custom    │
│Seç    │ │Bant Ayar │
└───┬───┘ └──┬───────┘
    │         │
┌───▼───┐ ┌──▼───────┐
│Preset │ │31-Band   │
│Liste  │ │Parametrik│
│[Pop]  │ │EQ Slider │
│[Rock] │ │[Her bant]│
│[Jazz] │ └──┬───────┘
│[Klasik]│    │
│[Dans]  │ ┌──▼───────┐
│[Bas]   │ │Kaydet     │
│[Vokal] │ │"Özel"     │
└───┬───┘ └──┬───────┘
    │         │
    └────┬───┘
         │
    ┌────▼────┐
    │Önizleme │
    │Dinle    │
    └────┬────┘
         │
    ┌────▼────┐
    │Uygula   │
    │→ Dinle  │
    └────┬────┘
         │
    ┌────▼────┐
    │Kaydet   │
    │→ DB     │
    └─────────┘
```

## 2. Ekran Akışı

```
┌──────────────────────────────────────────────────────────────┐
│ EKRAN 1: Equalizer                                           │
│                                                              │
│ +--- MODAL (w:600, glass) ------------------------------+   |
│ |                                                        |   |
│ |  🎛️ Equalizer                    [Preset] [Custom]     |   |
│ |                                                        |   |
│ |  ┌──────────────────────────────────────────────────┐  |   |
│ |  │  31-BAND PARAMETRIC EQ                           │  |   |
│ |  │                                                   │  |   |
│ |  │  20Hz  50Hz  100Hz 200Hz 500Hz 1kHz 2kHz 5kHz   │  |   |
│ |  │   │     │     │     │     │     │     │     │    │  |   |
│ |  │  +6    +3     0    -2    +1    +4    +2    0    │  |   |
│ |  │   │     │     │     │     │     │     │     │    │  |   |
│ |  │  ████  ███   ██    █     ███   ████  ███   ██   │  |   |
│ |  │                                                   │  |   |
│ |  │  10kHz 16kHz 20kHz                              │  |   |
│ |  │   │     │     │                                 │  |   |
│ |  │  -1    +2    0                                  │  |   |
│ |  │   │     │     │                                 │  |   |
│ |  │  █     ███   ██                                 │  |   |
│ |  └──────────────────────────────────────────────────┘  |   |
│ |                                                        |   |
│ |  [+6dB] [0dB] [-6dB]     [Sıfırla] [Kaydet]          |   |
│ |                                                        |   |
│ +--------------------------------------------------------+   |
└──────────────────────────────────────────────────────────────┘
```

## 3. Preset Seçimi

```
┌─────────────────┐
│     BAŞLA       │
└────────┬────────┘
         │
┌────────▼────────┐
│ Preset Listesi  │
└────────┬────────┘
    ┌────┼────┬────┐
    │    │    │    │
┌───▼──┐│┌───▼──┐│┌───▼──┐
│Pop   │││Rock  │││Jazz  │
│[Pop] │││[Rock]│││[Jazz]│
└───┬──┘│└───┬──┘│└───┬──┘
    │   │    │   │    │
┌───▼──┐│┌───▼──┐│┌───▼──┐
│Klasik│││Dans  │││Bas   │
│[Class]│││[Dance]│││[Bass]│
└───┬──┘│└───┬──┘│└───┬──┘
    │   │    │   │    │
    └───┴────┴───┴────┘
         │
    ┌────▼────┐
    │Seç →    │
    │Önizle   │
    └────┬────┘
         │
    ┌────▼────┐
    │Uygula   │
    └─────────┘
```

## 4. Hata Senaryoları

| Hata | Çözüm |
|------|-------|
| Preset yüklenemedi | Varsayılan "Flat" preset |
| Kaydetme başarısız | "Kaydetme başarısız" + tekrar dene |
| EQ bant sayısı tutarsız | Varsayılan 31-band |
| DB kaydetme hatası | "Ayarlar kaydedilemedi" |

## 5. Tier-Bazlı Varyasyonlar

| Tier | EQ Tipi | Bant Sayısı | Kaydetme |
|------|---------|:-----------:|----------|
| **Phone** | Slider-based | 10-band | Otomatik |
| **Tablet** | Slider-based | 31-band | Buton |
| **Embedded** | Knob-based | 31-band | Buton |
| **Desktop** | Full parametric | 31-band | Buton |
| **TV** | Large sliders | 10-band | Remote |
| **Car** | Preset only | — | Otomatik |
| **Watch** | Preset only | — | Otomatik |

## 6. Preset Listesi

| # | Preset | Bantlar | Kullanım |
|---|--------|---------|----------|
| 1 | Flat | 0dB tümü | Varsayılan |
| 2 | Pop | Mid boost | Pop müzik |
| 3 | Rock | Bass + Treble boost | Rock müzik |
| 4 | Jazz | Mid boost | Jazz müzik |
| 5 | Classical | Wide boost | Klasik müzik |
| 6 | Dance | Bass boost | Dans müzik |
| 7 | Bass Boost | 20-200Hz boost | Bass ağırlıklı |
| 8 | Vocal | 1-4kHz boost | Vokal ağırlıklı |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-20
**Mode:** Red Team · Human Mode · Truth Mode
