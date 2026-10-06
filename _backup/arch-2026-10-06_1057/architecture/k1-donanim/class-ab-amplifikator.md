---
title: "Class AB Amplifikatör Devresi"
layer: K1
category: "Güç Amplifikatörü"
date: 2026-09-20
---

# Class AB Amplifikatör Devresi

## Genel Bakış

Class AB amplifikatör, COREMUSIC'ın ses sinyalinin son güçlendirme aşamasıdır. Differential pair input, voltage amplification stage (VAS) ve push-pull output stage olmak üzere üç ana bölümden oluşur. MJL21194/93 çıkış transistörleri ile 250W RMS çıkış gücü sağlar.

## Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Çıkış Gücü | 250W RMS (8Ω, %0.5 THD) |
| Frekans Aralığı | 5Hz – 80kHz (±0.5dB) |
| THD+N | < %0.001 (1kHz, 1W) |
| Sinyal/Gürültü | > 120dB (A-Weighted) |
| Damaping Faktörü | > 200 (8Ω) |
| Giriş Empedansı | 47kΩ (differential) |
| Giriş Hassasiyeti | 1.5Vrms (tam çıkış için) |
| Kazanç | 26dB (20x) |
| Güç Topolojisi | ±35V Dual Rail |

## Devre Şeması - Genel Görünüm

```
                           ±35V
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│                   CLASS AB AMPLİFİKATÖR                 │
│                                                         │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐          │
│  │ DIFF     │    │   VAS    │    │  OUTPUT  │          │
│  │ PAIR     │───▶│  STAGE   │───▶│  STAGE   │───▶ SPK  │
│  │ INPUT    │    │          │    │          │          │
│  └────┬─────┘    └──────────┘    └──────────┘          │
│       │                                                 │
│       │         ┌──────────┐                            │
│       └────────▶│ FEEDBACK │◀───── Output              │
│                 │ NETWORK  │                            │
│                 └──────────┘                            │
└─────────────────────────────────────────────────────────┘
```

## Devre Tasarımı - Detaylı

### Differential Pair Input Stage

```
                    +35V
                     │
                     ▼
                 ┌───┴───┐
                 │  R3   │ 100Ω (Tail direnç)
                 └───┬───┘
                     │
            ┌────────┴────────┐
            │                  │
         ┌──┴──┐            ┌──┴──┐
         │ Q1  │            │ Q2  │  BC560C (PNP)
         │NPN  │            │PNP  │
         └──┬──┘            └──┬──┘
            │                  │
         ┌──┴──┐            ┌──┴──┐
         │  R1 │            │  R2 │  100Ω (Emitter)
         └──┬──┘            └──┬──┘
            │                  │
            ▼                  ▼
         Input(+)          Input(-)
         (Non-inv)         (Inverting)
```

### Voltage Amplification Stage (VAS)

```
            ┌─────────────────────────────┐
            │         VAS STAGE           │
            │                             │
         ┌──┴──┐                       ┌──┴──┐
         │ Q3  │                       │ Q4  │  MPSA06 (NPN)
         │NPN  │                       │NPN  │
         └──┬──┘                       └──┬──┘
            │                             │
            ▼                             ▼
         Collector                      Collector
            │                             │
         ┌──┴──┐                       ┌──┴──┐
         │  Cm │  10pF Miller          │  R4 │  1kΩ
         └──┬──┘  Compansasyon          └──┬──┘
            │                             │
            └──────────┬──────────────────┘
                       │
                    -35V
```

### Push-Pull Output Stage

```
                    +35V
                     │
                 ┌───┴───┐
                 │  R5   │  0.22Ω (Emitter)
                 └───┬───┘
                     │
                 ┌───┴───┐
                 │ MJL   │  MJL21194 (NPN)
                 │21194  │  250W/200V/16A
                 └───┬───┘
                     │
                     ├─────────────────── Output
                     │
                 ┌───┴───┐
                 │ MJL   │  MJL21193 (PNP)
                 │21193  │  250W/200V/16A
                 └───┬───┘
                     │
                 ┌───┴───┐
                 │  R6   │  0.22Ω (Emitter)
                 └───┬───┘
                     │
                    -35V
```

## Bileşen Listesi

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Giriş Transistörleri | BC560C | 2 | PNP low-noise |
| 2 | VAS Transistörleri | MPSA06 | 2 | NPN high-voltage |
| 3 | Çıkış Transistörleri | MJL21194 | 4 | NPN power |
| 4 | Çıkış Transistörleri | MJL21193 | 4 | PNP power |
| 5 | Bias Transistörleri | BD139/140 | 2 | Thermal tracking |
| 6 | Miller Kondansatörü | 10pF C0G | 1 | Frequency compensation |
| 7 | Emitter Dirençleri | 0.22Ω 5W | 8 | Current sharing |
| 8 | Feedback Direnci | 20kΩ | 1 | Gain ayarı |
| 9 | Feedback Direnç | 1kΩ | 1 | Gain ayarı |

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 AK4458 | Giriş | DAC diferansiyel çıkış |
| K1 Feedback Network | Geri besleme | Negatif geri besleme ağı |
| K1 Güç Kaynağı | Alt | ±35V dual rail |
| K1 Termal | Bağlantı | Soğutucu bağlantısı |
| K1 Koruma | Çıkış | DC offset, overcurrent koruması |

## Durum: Implementasyon

**Durum**: 🟡 Simülasyon Aşamasında

- LTSpice simülasyonu tamamlandı, THD < %0.001 doğrulandı
- Termal simülasyon: MJL21194 için RθJC = 1.22°C/W
- PCB layout: Star grounding uygulandı
- Soğutucu: Alüminyum ekstrüzyon, 150mm uzunluk
- Bias akımı: 50mA idle (Class AB crossover region)
