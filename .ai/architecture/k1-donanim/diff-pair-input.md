---
title: "Differential Pair Input Stage"
layer: K1
category: "Analog Giriş Evresi"
date: 2026-09-20
---

# Differential Pair Input Stage

## Genel Bakış

Differential pair input stage, Class AB amplifikatörün giriş evresidir. Diferansiyel sinyalleri alır,(Common Mode Rejection Ratio) yüksek CMRR ile gürültü bastırması sağlar ve VAS (Voltage Amplification Stage)'a düşük distorsiyonlu sinyal iletir. BC560C low-noise PNP transistörleri kullanılır.

## Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Giriş Empedansı | 47kΩ (differential) |
| Giriş Hassasiyeti | 1.5Vrms (tam çıkış için) |
| CMRR | > 100dB @ 1kHz |
| THD | < %0.0005 (1kHz, 1Vrms) |
| Giriş Gürültüsü | < 1nV/√Hz |
| Bias Akımı | 1mA (tail current) |
| Tail Direnci | 100Ω |
| Transistör | BC560C (PNP, matched pair) |
| RθJC (BC560C) | 200°C/W |

## Devre Şeması

```
                           +35V
                            │
                        ┌───┴───┐
                        │  R5   │ 100Ω (Tail Direnci)
                        └───┬───┘
                            │
                     ┌──────┴──────┐
                     │  Tail Node   │
                     │              │
                  ┌──┴──┐       ┌──┴──┐
                  │ Q1  │       │ Q2  │  BC560C (PNP)
                  │NPN  │       │PNP  │  Matched Pair
                  └──┬──┘       └──┬──┘
                     │             │
                  ┌──┴──┐       ┌──┴──┐
                  │  R1 │       │  R2 │  100Ω (Emitter)
                  └──┬──┘       └──┬──┘
                     │             │
                     │             │
                  ┌──┴──┐       ┌──┴──┐
                  │  R3 │       │  R4 │  47kΩ (Input)
                  └──┬──┘       └──┬──┘
                     │             │
                     ▼             ▼
                  Input(+)     Input(-)
                  (Non-Inv)    (Inverting)
```

## Bias Current Hesaplaması

### Tail Current

```
ITail = (V+ - VBE - V-) / RTail
ITail = (35V - 0.7V - (-35V)) / 100Ω
ITail = 70V / 100Ω = 700mA (maksimum)
```

### Operating Point

| Parametre | Değer |
|-----------|-------|
| ITail | 1mA (tasarım değeri) |
| IC1 = IC2 | 0.5mA (her biri) |
| VCE | ~35V (her transistör) |
| gm | 19.2 mA/V (IC/VT, VT=26mV) |
| rπ | 5.2kΩ (β/gm, β=100) |
| r0 | 100kΩ (Early voltage consideration) |

## CMRR Analizi

### CMRR Formülü

```
CMRR = Ad / Acm

Ad = Differential Gain = gm × RC
Acm = Common-Mode Gain = gm × RC / (1 + 2 × gm × RE)

CMRR = 1 + 2 × gm × RE
```

### hesaplama

| Parametre | Değer |
|-----------|-------|
| gm | 19.2 mA/V |
| RE (tail) | 100Ω |
| CMRR (hesaplanan) | 100.8 dB |
| CMRR (hedef) | > 100dB |

## Gürültü Analizi

### Giriş Gürültüsü Kaynakları

| Kaynak | Değer | Etki |
|--------|-------|------|
| Thermal (RTail) | 1.29 nV/√Hz | Düşük |
| Shot (IC) | 0.28 nV/√Hz | Düşük |
| Flicker (1/f) | ~5 nV/√Hz @ 10Hz | Orta |
| Toplam Giriş | < 1 nV/√Hz @ 1kHz | Kabul edilebilir |

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 AK4458 | Giriş | DAC diferansiyel çıkış |
| K1 VAS Stage | Çıkış | VAS girişine sinyal iletir |
| K1 Feedback | Geri besleme | Negatif geri besleme ağı |
| K1 Güç Kaynağı | Alt | ±35V besleme |

## Durum: Implementasyon

**Durum**: 🟡 Simülasyon Aşamasında

- BC560C seçimi: VBE eşleme < 2mV, hFE eşleme < %5
- Matching fixture: Test düzeneği hazır
- Input coupling: DC coupled (no coupling capacitor)
- Input impedance: 47kΩ differential (standart audio)
- PCB placement: Symmetrical layout, short traces
