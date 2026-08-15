---
type: electronic
category: dsp-loudness
title: "CoreMusic — Loudness & ReplayGain"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Loudness & ReplayGain

**See also:** [[electronic/dsp/index]] · [[ADR-017-dsp-hardware-mode]]

---

## 1. Amaç

Loudness & ReplayGain, ses seviyesi yönetimini ve parçalar arası dengeyi sağlayan mekanizmaları tanımlar.

---

## 2. Loudness Standards

| Standart | Hedef | Kullanım |
|----------|-------|----------|
| EBU R128 | -23 LUFS | Yayın |
| ITU-R BS.1770-4 | -24 LUFS | Genel |
| Spotify | -14 LUFS | Streaming |
| YouTube | -14 LUFS | Video |
| Apple Music | -16 LUFS | Streaming |
| CoreMusic Default | -23 LUFS | Varsayılan |

---

## 3. Loudness Measurement

### LUFS (Loudness Units Full Scale)

| Metrik | Açıklama |
|--------|----------|
| Integrated | Parça boyunca ortalama |
| Short-term | 3 saniyelik ortalama |
| Momentary | 400ms ortalama |
| True Peak | Maksimum true peak |

---

## 4. ReplayGain

Her parçanın algılanan ses seviyesini normalize eder.

### ReplayGain Parametreleri

| Parametre | Değer |
|-----------|-------|
| Standard | ReplayGain 2.0 |
| Target | -23 LUFS (EBU R128) |
| Pre-amp | 0dB |
| Clip Prevention | Aktif |
| Track Gain | Parça bazlı |
| Album Gain | Albüm bazlı |

---

## 5. Loudness Normalization Akışı

```
Audio Input ──▶ Loudness Ölç ──▶ {Hedef ile Karşılaştır}
                                    │
                    Düşük ▼         │         ▼ Yüksek
                  Gain Artır       │       Gain Azalt
                       │           │           │
                       │    Uygunlu ▼           │
                       │      Geç              │
                       │       │               │
                       └───────┴───────┬───────┘
                                       ▼
                                Limiter Kontrol
                                       │
                                       ▼
                                Normal Çıkış
```

---

## 6. True Peak Clipping

| Özellik | Değer |
|---------|-------|
| True Peak Limit | -0.3 dBTP |
| Oversampling | 4x |
| Standard | ITU-R BS.1770-4 |
| Clipping | Engellenir |

---

## 7. Loudness Range (LRA)

| Aralık | Kullanım |
|--------|----------|
| <5 LU | Yayın (sıkıştırılmış) |
| 5-15 LU | Müzik (standart) |
| >15 LU | Klasik müzik (geniş dinamik) |

---

## 8. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-017-dsp-hardware-mode]] | DSP hardware |
| [[ADR-006-performance-targets]] | Performans hedefleri |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
