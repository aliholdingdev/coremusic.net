---
title: "AK4458 32-Bit 8-Kanal DAC"
layer: K1
category: "Dijital/Analog Dönüştürücü"
date: 2026-09-20
---

# AK4458 32-Bit 8-Kanal DAC

## Genel Bakış

AK4458, Asahi Kasei Microdevices (AKM) tarafından üretilen premium 32-bit 8 kanallı (4 stereo) digital-to-analog dönüştürücüdür. COREMUSIC'ın ana DAC'ı olarak kullanılır. DSD (Direct Stream Digital) formatını natif olarak destekler ve ultra düşük distorsiyon característicasına sahiptir.

## Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Çözünürlük | 32-bit |
| Kanal Sayısı | 8 (4 stereo) |
| Örnekleme Hızı | Up to 768kHz PCM, DSD256 |
| Çıkış Voltajı | 2.1Vrms (differential) |
| SNR | 125dB (A-Weighted) |
| THD+N | -112dB (%0.00025) |
| Çıkış Empedansı | 25Ω (differential) |
| Voltaj Besleme | AVDD = 5V, DVDD = 3.3V, CVDD = 1.2V |
| Package | LQFP-64, 10×10mm |
| Çalışma Sıcaklığı | -40°C ile +85°C |

## DSD Modu Destekleri

| Mod | Örnekleme Hızı | Bit Derinliği |
|-----|----------------|---------------|
| DSD64 | 2.8224 MHz | 1-bit |
| DSD128 | 5.6448 MHz | 1-bit |
| DSD256 | 11.2896 MHz | 1-bit |
| DoP128 | 5.6448 MHz | 1-bit (PCM wrap) |
| DoP256 | 11.2896 MHz | 1-bit (PCM wrap) |

## Devre Tasarımı

### Temel Bağlantılar

```
XMOS XU316 I2S Output
     │
     ├─ SCK ──────────────────▶ AK4458 TDMCLK (Pin 18)
     ├─ WS ───────────────────▶ AK4458 TDMFS (Pin 19)
     ├─ SD0 (Ch1-2 Data) ────▶ AK4458 TDMD0 (Pin 20)
     ├─ SD1 (Ch3-4 Data) ────▶ AK4458 TDMD1 (Pin 21)
     ├─ SD2 (Ch5-6 Data) ────▶ AK4458 TDMD2 (Pin 22)
     └─ SD3 (Ch7-8 Data) ────▶ AK4458 TDMD3 (Pin 23)

AK4458 Analog Çıkışlar
     │
     ├─ OUTL1+ ──▶ Class AB Amp Input (Kanal 1 Sol)
     ├─ OUTL1- ──▶ Class AB Amp Input (Kanal 1 Sol Negatif)
     ├─ OUTR1+ ──▶ Class AB Amp Input (Kanal 1 Sağ)
     ├─ OUTR1- ──▶ Class AB Amp Input (Kanal 1 Sağ Negatif)
     └─ (Diğer kanallar için devam eder, toplam 8 çift differential)

AK4458 Kontrol (I2C)
     │
     ├─ SDA ──▶ XMOS GPIO[2]
     ├─ SCL ──▶ XMOS GPIO[3]
     └─ CSN ──▶ GND (SPI = I2C modu)
```

### Kondansatör ve Direnç Değerleri

| Referans | Değer | Açıklama |
|----------|-------|----------|
| C1-C4 | 100nF MLCC | AVDD dekuplajı |
| C5-C8 | 10µF MLCC | AVDD bulk |
| C9-C12 | 100nF MLCC | DVDD dekuplajı |
| C13-C16 | 1µF MLCC | CVDD dekuplajı |
| C17-C24 | 22pF | Çıkış filtre kondansatörü |
| R1-R8 | 100Ω | Çıkış seri direnç |
| R9-R10 | 4.7kΩ | I2C pull-up |

## Pin Konfigürasyonu (Önemli Pinler)

| Pin | Ad | Yön | Açıklama |
|-----|-----|------|----------|
| 1-4 | AVDD | Güç | +5V analog besleme |
| 5-8 | AGND | Güç | Analog toprak |
| 9-12 | DVDD | Güç | +3.3V dijital besleme |
| 13-14 | DGND | Güç | Dijital toprak |
| 15-16 | CVDD | Güç | +1.2V çekirdek besleme |
| 17 | CPGND | Güç | Charge pump toprak |
| 18 | TDMCLK | Giriş | TDM clock (MCLK) |
| 19 | TDMFS | Giriş | TDM frame sync (LRCK) |
| 20-23 | TDMD[0:3] | Giriş | TDM data inputs |
| 24 | DIF0 | Giriş | Format seçimi |
| 25 | DIF1 | Giriş | Format seçimi |
| 26 | DIF2 | Giriş | Format seçimi |
| 27-34 | OUTL1±, OUTR1± | Çıkış | Analog çıkış çift 1 |
| 35-42 | OUTL2±, OUTR2± | Çıkış | Analog çıkış çift 2 |
| 43-50 | OUTL3±, OUTR3± | Çıkış | Analog çıkış çift 3 |
| 51-58 | OUTL4±, OUTR4± | Çıkış | Analog çıkış çift 4 |
| 59 | SDA | Bidirectional | I2C veri |
| 60 | SCL | Giriş | I2C clock |
| 61-64 | NC/VARIOUS | - | Diğer |

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | LQFP-64 pad layout |
| K1 XMOS | Bağlantı | I2S/TDM input |
| K1 Class AB Amp | Çıkış | Differential analog output → amplifikatör |
| K1 Güç Kaynağı | Alt | ±5V, +3.3V, +1.2V |
| K1 Termal | Bağlantı | Sıcaklık izleme için NTC |

## Durum: Implementasyon

**Durum**: 🟢 Hazır

- Format: TDM (DIF2:1:0 = 1:0:0)
- Master/Slave: Slave (AK4458 clock XMOS'tan gelir)
- DSD modu: Auto-detect (DSD_DATA pin HIGH)
- I2C adres: 0x10 (CSN = LOW)
- Volume: Dijital potansiyometre ile programlanabilir (0dB ile -∞)
- Soft mute: I2C register MUTE bit ile
