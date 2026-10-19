---
reference_doc: CoreMusic UI Design System
title: "CoreMusic — Equalizer Flow"
type: flow
category: ui-design
date: 2026-09-20
updated: 2026-09-29
status: active
version: 1.0.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Equalizer Flow

## 1. Akış Diyagramı (Decision Flow)

### EQ Settings

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

## 2A. Preset Seçimi

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

## 3. Hata Senaryoları

| Hata | Çözüm |
|------|-------|
| Preset yüklenemedi | Varsayılan "Flat" preset |
| Kaydetme başarısız | "Kaydetme başarısız" + tekrar dene |
| EQ bant sayısı tutarsız | Varsayılan 31-band |
| DB kaydetme hatası | "Ayarlar kaydedilemedi" |

## 4. Tier-Bazlı Varyasyonlar

| Tier | EQ Tipi | Bant Sayısı | Kaydetme |
|------|---------|:-----------:|----------|
| **Phone** | Slider-based | 10-band | Otomatik |
| **Tablet** | Slider-based | 31-band | Buton |
| **Embedded** | Knob-based | 31-band | Buton |
| **Desktop** | Full parametric | 31-band | Buton |
| **TV** | Large sliders | 10-band | Remote |
| **Car** | Preset only | — | Otomatik |
| **Watch** | Preset only | — | Otomatik |

## 4A. Preset Listesi

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
## 5. BEM Sınıfları

| BEM sınıfı | Rol (bu dosyadaki kanıt) | Durumlar | Kaynak |
|------------|--------------------------|----------|--------|
| `.modal`, `.modal__content` | blok — MODAL (w:600, glass) (bu dosya L71) | closed, opening, open, closing | 02-component-inventory.md L95 (C07) |
| `.slider`, `.slider__track`, `.slider__fill` | blok — EQ Slider / 31-band parametrik bantlar (bu dosya L36-L39, L76-L88); Phone/Tablet "Slider-based" (L144-L145) | default, dragging, disabled | 02-component-inventory.md L115 (C09) |
| `.tab`, `.tab--active` | eleman — [Preset] [Custom] sekme seçimi (bu dosya L73) | default, hover, active, disabled | 02-component-inventory.md L85 (C06) |
| `.btn`, `.btn--secondary` | blok — [+6dB] [0dB] [-6dB] [Sıfırla] [Kaydet] (bu dosya L91) | default, hover, active, disabled, loading | 02-component-inventory.md L65 (C04) |

> **Kaynak:** [[../../02-component-inventory]] v4.1.0 (C01-C19, `updated: 2026-09-29`). Preset listesi (bu dosya L154-L163) veri tablosudur, BEM sınıfı değildir.

---

## 6. Adımlar

| # | Adım | Ekrana | Aksiyon |
|---|------|--------|---------|
| 1 | EQ Modu Seç | Equalizer | [Preset] sekmesine tıkla |
| 2 | Preset Seç | Equalizer | preset listesinden [Pop] öğesini seç |
| 3 | Özel Bant Ayarla | Equalizer | 31-BAND EQ'da bir bant sürgüsünü kaydır |
| 4 | Sıfırla | Equalizer | [Sıfırla] butonuna tıkla |
| 5 | Kaydet | Equalizer | [Kaydet] butonuna tıkla |

> ⚠️ VERIFICATION REQUIRED — §1'deki "Önizleme Dinle" (L49-L52) ve "Uygula → Dinle" (L54-L57) düğümlerinin §2'de karşılık gelen kontrolü yok (bu dosya L91'de yalnız [+6dB] [0dB] [-6dB] [Sıfırla] [Kaydet]) → satırlar yazılmadı.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-29
**Mode:** Red Team · Human Mode · Truth Mode
