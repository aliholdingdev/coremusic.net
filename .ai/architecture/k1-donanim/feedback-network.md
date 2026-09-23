---
title: "Negative Feedback Network"
layer: K1
category: "Analog Geri Besleme"
date: 2026-09-20
---

# Negative Feedback Network

## Genel Bakış

Negative feedback network, amplifikatörün çıkış sinyalini girişe geri besleyerek kazanç stabilitesini, distorsiyon azaltmasını ve bant genişliği genişletmesini sağlar. Resistive divider ile kazanç ayarı yapılır, frequency compensation ile stabilite garantilenir.

## Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Toplam Kazanç | 26dB (20x) |
| Açık Devre Kazancı | 80dB (10,000x) |
| Geri Besleme Oranı (β) | 0.05 (1/20) |
| Loop Gain | 60dB (1000x) |
| THD Azaltma | 60dB (1000x) |
| Bant Genişliği | DC – 80kHz |
| Sinyal/Gürültü | > 120dB |

## Devre Şeması

```
                         ┌──────────────────────────────────┐
                         │         FEEDBACK NETWORK         │
                         │                                  │
                         │   ┌───────┐                      │
                         │   │  R_f  │  20kΩ (Feedback)     │
                         │   └───┬───┘                      │
                         │       │                          │
                         │   ┌───┴───┐                      │
                         │   │  R_g  │  1kΩ (Ground)        │
                         │   └───┬───┘                      │
                         │       │                          │
                         │      GND                         │
                         │                                  │
                         │   β = Rg / (Rf + Rg)             │
                         │   β = 1kΩ / (20kΩ + 1kΩ)        │
                         │   β = 0.0476                     │
                         │                                  │
                         │   Av = 1/β = 21 (26.4dB)         │
                         │                                  │
                         └──────────────────────────────────┘

Signal Flow:

Input(+) ──▶ Differential Pair ──▶ VAS ──▶ Output ──▶ Speaker
                  ▲                                     │
                  │                                     │
                  └──────── Feedback Network ◀──────────┘
```

## Kazanç Hesaplaması

### Kapalı Devre Kazancı

```
Av_closed = Av_open / (1 + Av_open × β)

Av_open = 10,000 (80dB)
β = 0.0476

Av_closed = 10,000 / (1 + 10,000 × 0.0476)
Av_closed = 10,000 / 477
Av_closed = 20.96 (26.4dB)
```

### Distorsiyon Azaltma

```
THD_open = %0.1 (açık devre)
THD_closed = THD_open / (1 + Av_open × β)
THD_closed = %0.1 / 477
THD_closed = %0.00021 (hedef: < %0.001)
```

## Frequency Compensation

### Bode Plot

```
Kazanç (dB)
    │
 80 ┤─────────────┐
    │             │
 60 ┤             │ -20dB/decade (dominant pole)
    │             │
 40 ┤             │
    │             │
 20 ┤             │
    │             │
  0 ┤             └────────────────── Frekans
    │
    └───┬────┬────┬────┬────┬────┬───
       10Hz 100Hz 1kHz 10kHz 100kHz 1MHz

    ├─ DC Kazanç: 80dB (10,000x)
    ├─ Unity Gain Frequency: 1MHz
    ├─ Phase Margin: > 60°
    └─ Gain Margin: > 20dB
```

### Stabilite Kriterleri

| Kriter | Değer | Durum |
|--------|-------|-------|
| Phase Margin | > 45° | ✅ 65° |
| Gain Margin | > 10dB | ✅ 20dB |
| UGF | 1-10MHz | ✅ 1MHz |
| Peak | < 3dB | ✅ 1.2dB |

## Bileşen Değerleri

| Referans | Değer | Tolerans | Tip | Açıklama |
|----------|-------|----------|-----|----------|
| R_f | 20kΩ | %0.1 | Metal Film | Feedback direnci |
| R_g | 1kΩ | %0.1 | Metal Film | Ground reference |
| C_f | 100pF | %5 | C0G/NP0 | HF compensation |
| R_d | 10Ω | %5 | Metal Film | Damping (optional) |

## Empedans Eşleşme

```
Giriş Empedansı: 47kΩ (differential)
Feedback Empedansı: 21kΩ (Rf + Rg paralel)
Çıkış Empedansı: < 0.1Ω (açık devre feedback ile)

Empedans oranı: 47kΩ / 21kΩ = 2.24:1 (iyi eşleşme)
```

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Diff Pair | Giriş | Feedback giriş noktası |
| K1 Output Stage | Çıkış | Feedback çıkış noktası |
| K1 VAS | Bağlantı | Loop gain katkısı |
| K1 Güç Kaynağı | Alt | ±35V referans |

## Durum: Implementasyon

**Durum**: 🟡 Simülasyon Aşamasında

- Kazanç: 20x (26dB) – LTSpice doğrulandı
- THD: < %0.001 @ 1kHz, 1W – Simülasyon ile verified
- Feedback trace: PCB'de short, shielded routing
- Ground connection: Star ground point'e doğrudan bağlantı
- Component tolerance: %0.1 metal film (precision)
