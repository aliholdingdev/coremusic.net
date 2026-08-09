---
type: electronic
category: dsp-equalizer
title: "CoreMusic — Equalizer System"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Equalizer System

**See also:** [[electronic/dsp/index]] · [[ADR-025-professional-eq-system]]

---

## 1. Amaç

Equalizer System, CoreMusic ELECTRONICS platformunun grafik ve parametrik EQ sistemini tanımlar. 31 bant'a kadar destek sunar.

---

## 2. EQ Tipleri

### Graphic Equalizer

| Bant Sayısı | Frekans Aralığı | Kullanım |
|-------------|------------------|----------|
| 2 Band | Low/High | Basit ton |
| 3 Band | Low/Mid/High | Standart |
| 5 Band | 5 frekans | Ev ses |
| 8 Band | 8 frekans | Orta seviye |
| 10 Band | 10 frekans | Profesyonel |
| 15 Band | 15 frekans | Yüksek |
| 31 Band | 31 frekans | Stüdyo |

### Parametric Equalizer

| Bant Sayısı | Parametreler | Kullanım |
|-------------|-------------|----------|
| 2 Band | Freq, Gain, Q | Basit |
| 5 Band | Freq, Gain, Q | Orta |
| 10 Band | Freq, Gain, Q | Yüksek |
| 31 Band | Freq, Gain, Q | Stüdyo |

---

## 3. 31-Band Graphic EQ Frekansları

```
31Hz    63Hz    125Hz   250Hz   500Hz
1kHz    2kHz    4kHz    8kHz    16kHz
+ ara frekanslar...
```

| Bant | Frekans | Octave |
|------|---------|--------|
| 1 | 20Hz | — |
| 2 | 25Hz | — |
| 3 | 31.5Hz | 1/3 octave |
| 4 | 40Hz | — |
| 5 | 50Hz | — |
| 6 | 63Hz | — |
| 7 | 80Hz | — |
| 8 | 100Hz | — |
| 9 | 125Hz | — |
| 10 | 160Hz | — |
| 11 | 200Hz | — |
| 12 | 250Hz | — |
| 13 | 315Hz | — |
| 14 | 400Hz | — |
| 15 | 500Hz | — |
| 16 | 630Hz | — |
| 17 | 800Hz | — |
| 18 | 1kHz | — |
| 19 | 1.25kHz | — |
| 20 | 1.6kHz | — |
| 21 | 2kHz | — |
| 22 | 2.5kHz | — |
| 23 | 3.15kHz | — |
| 24 | 4kHz | — |
| 25 | 5kHz | — |
| 26 | 6.3kHz | — |
| 27 | 8kHz | — |
| 28 | 10kHz | — |
| 29 | 12.5kHz | — |
| 30 | 16kHz | — |
| 31 | 20kHz | — |

---

## 4. Parametric EQ Parametreleri

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Frequency | 20Hz - 20kHz | 1kHz |
| Gain | -24dB - +24dB | 0dB |
| Q Factor | 0.1 - 10 | 1.0 |
| Filter Type | Peaking, LowShelf, HighShelf | Peaking |

---

## 5. EQ Preset Sistemi

| Preset | Kullanım |
|--------|----------|
| Flat | Tüm bantlar 0dB |
| Bass Boost | Düşük frekans güçlendirme |
| Treble Boost | Yüksek frekans güçlendirme |
| Vocal | Orta frekans vurgu |
| Rock | Bass + Treble boost |
| Jazz | yumuşak ton |
| Classical | Denge |
| Custom | Kullanıcı tanımlı |

---

## 6. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-025-professional-eq-system]] | 31-band EQ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
