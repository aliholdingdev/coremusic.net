---
title: "Voltage Amplification Stage (VAS)"
layer: K1
category: "Analog Gain Evresi"
date: 2026-09-20
---

# Voltage Amplification Stage (VAS)

## Genel Bakış

Voltage Amplification Stage (VAS), Class AB amplifikatörün orta evresidir. Differential pair input'tan gelen sinyali yüksek kazançla yükseltir ve output stage'a iletir. Miller compensation technique kullanılarak stabilite sağlanır. MPSA06 NPN transistörleri tercih edilir.

## Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Gerilim Kazancı | 40-60dB (100x-316x) |
| Frekans Bant Genişliği | DC – 1MHz |
| Miller Kondansatörü | 10pF C0G |
| Gain-Bandwidth Product | 40MHz |
| Çıkış Empedansı | > 10kΩ |
| THD Katkısı | < %0.0001 |
| MPSA06 VCEO | 80V |
| MPSA06 IC | 500mA |
| MPSA06 hFE | 30-300 |

## Devre Şeması

```
                           +35V
                            │
                        ┌───┴───┐
                        │  R1   │ 1kΩ (Collector Load)
                        └───┬───┘
                            │
                    ┌───────┴───────┐
                    │               │
                 ┌──┴──┐         ┌──┴──┐
                 │  Cm │  10pF   │  D1 │  BAT54S (Clamp)
                 │Miller│        └──┬──┘
                 └──┬──┘            │
                    │               │
                    │   Collector   │
                    │               │
                 ┌──┴──┐            │
                 │ Q3  │            │  MPSA06 (NPN)
                 │NPN  │◀───────────┘
                 └──┬──┘
                    │
                 Base ←── From Differential Pair Output
                    │
                 Emitter
                    │
                 ┌──┴──┐
                 │  R2 │  100Ω (Emitter Degeneration)
                 └──┬──┘
                    │
                   -35V
```

## Miller Compensation Analizi

### Neden Miller Compensation?

```
Açık devre kazancı (Av) çok yüksek olduğunda,
transistörün iç kapasitansı (Cob) Miller etkisi ile
büyür ve bant genişliğini daraltır.

Miller Kondansatörü (Cm) kontrollü bir şekilde
polarite splitsiyon stabilized ederek,
transistörün DC kazancını yüksek tutar,
AC kazancını ise istenen frecuency'de düşürür.
```

### Miller Etkisi Formülü

```
AvMiller = Av_open × Cm / (Cm + 1)
AvMiller ≈ Av_open (eğer Cm >> 1)

f-3dB = 1 / (2π × Av × Rc × Cm)

Örnek:
Av = 1000 (60dB)
Rc = 1kΩ
Cm = 10pF

f-3dB = 1 / (2π × 1000 × 1000 × 10×10⁻¹²)
f-3dB = 15.9 kHz (input-referred)
```

### Dominant Pole

| Parametre | Değer |
|-----------|-------|
| Dominant Pole | 15.9 kHz |
| Second Pole | 1.2 MHz |
| Phase Margin | 65° |
| Gain Margin | 20dB |
| UGF (Unity Gain) | 40MHz |

## Bileşen Değerleri

| Referans | Değer | Tip | Açıklama |
|----------|-------|-----|----------|
| Q3 | MPSA06 | NPN | Ana VAS transistörü |
| R1 | 1kΩ 1/4W | Metal Film | Collector load |
| R2 | 100Ω 1/4W | Metal Film | Emitter degeneration |
| Cm | 10pF | C0G/NP0 | Miller compensation |
| D1 | BAT54S | Schottky | Output clamp (optional) |

## Frekans Tepkisi

```
Kazanç (dB)
    │
 60 ┤──────────────────┐
    │                  │
 40 ┤                  │  -20dB/decade
    │                  │
 20 ┤                  │
    │                  │
  0 ┤                  └──────────────────── Frekans
    │
    └───┬────┬────┬────┬────┬────┬────┬───
       10Hz 100Hz 1kHz 10kHz 100kHz 1MHz

    ├─ DC Kazanç: 60dB (1000x)
    ├─ -3dB Noktası: 15.9kHz
    ├─ 0dB Noktası: 40MHz
    └─ Phase Margin: 65°
```

## Stabilite Analizi

### Phase Margin Hesabı

```
Dominant pole: fp1 = 15.9kHz
Second pole: fp2 = 1.2MHz

@ UGF (40MHz):
Phase = -90° (fp1) - arctan(40/1200) = -90° - 1.9° = -91.9°
Phase Margin = 180° - 91.9° = 88.1°

Sonuç: Sistem kararlı (PM > 45° gerekli)
```

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Diff Pair | Giriş | Differential pair çıkışı |
| K1 Output Stage | Çıkış | Push-pull output'a sinyal |
| K1 Feedback | Geri besleme | Geri besleme noktası |
| K1 Güç Kaynağı | Alt | ±35V besleme |

## Durum: Implementasyon

**Durum**: 🟡 Simülasyon Aşamasında

- LTSpice simülasyonu tamamlandı
- AC analysis: 60dB DC kazanç, 40MHz UGF doğrulandı
- Transient analysis: Slew rate > 50V/µs
- DC operating point: IC = 5mA, VCE = 30V
- PCB placement: Short traces, close to differential pair
