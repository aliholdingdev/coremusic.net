---
type: architecture
category: layer-definition
title: "K1 — Hardware Infrastructure Layer (120 Components)"
date: 2026-09-18
updated: 2026-09-18
status: draft
version: 1.0.0
authority: Bayram Ali / Vault Steward
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/architecture/k1-hardware-layer.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/brain.md"
  layer: K1
  component_count: 120
---

# K1 — Donanım Altyapısı Katmanı (Hardware Infrastructure Layer)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[brain.md]]

**Kapsam:** CoreMusic donanım platformunun tüm fiziksel ve lojik bileşenleri — DAC/ADC, amplifikatör, konuşmacı, güç kaynağı, konnektörler ve soğutma.

---

## 1. Genel Bakış

K1 katmanı, CoreMusic ses sisteminin donanım altyapısını tanımlar. XMOS XU316 USB ses arayüzü, çok kanallı DAC/ADC, Sınıf AB amplifikatör, 8.1 surround hoparlör düzeni ve tüm güç kaynağı devreleri bu katmanda belirlenir.

### 1.1 Donanım Mimarisi Diyagramı

```
┌─────────────────────────────────────────────────────────────────────┐
│                     K1 — HARDWARE LAYER (120)                      │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌─────────────────┐    ┌─────────────────────────────────────┐    │
│  │  XMOS XU316     │    │         DAC/ADC CHAIN               │    │
│  │  ┌───────────┐  │    │  ┌──────────┐  ┌──────────────┐    │    │
│  │  │XCore Proc │──┼───>│  │PCM3168A  │  │  AK4458      │    │    │
│  │  │USB UAC2   │  │    │  │DAC (8out)│  │  DAC (8ch)   │    │    │
│  │  │I2S Master │  │    │  │ADC (6in) │  │  32-bit      │    │    │
│  │  └───────────┘  │    │  │24-bit    │  │  768kHz      │    │    │
│  └─────────────────┘    │  │192kHz    │  └──────────────┘    │    │
│                          │  └──────────┘                       │    │
│                          └─────────────────────────────────────┘    │
│                                    │                                │
│                                    ▼                                │
│  ┌─────────────────────────────────────────────────────────────┐    │
│  │              AMPLIFIER ARRAY (9 channels)                   │    │
│  │  ┌─────┐┌─────┐┌─────┐┌─────┐┌─────┐┌─────┐┌─────┐┌─────┐│    │
│  │  │CH1  ││CH2  ││CH3  ││CH4  ││CH5  ││CH6  ││CH7  ││CH8  ││    │
│  │  │100W ││100W ││100W ││100W ││100W ││100W ││100W ││100W ││    │
│  │  │@8Ω  ││@8Ω  ││@8Ω  ││@8Ω  ││@8Ω  ││@8Ω  ││@8Ω  ││@8Ω  ││    │
│  │  └─────┘└─────┘└─────┘└─────┘└─────┘└─────┘└─────┘└─────┘│    │
│  │  ┌─────────────────────────────────────────────────────┐    │    │
│  │  │        LFE SUBWOOFER AMP (200W @ 4Ω)               │    │    │
│  │  └─────────────────────────────────────────────────────┘    │    │
│  └─────────────────────────────────────────────────────────────┘    │
│                                    │                                │
│                                    ▼                                │
│  ┌─────────────────────────────────────────────────────────────┐    │
│  │            SPEAKER ARRAY (8.1 Surround)                    │    │
│  │  ┌──────┐┌──────┐┌──────┐┌──────┐┌──────┐┌──────┐         │    │
│  │  │  FL  ││  FR  ││  C   ││  SL  ││  SR  ││  RL  │         │    │
│  │  └──────┘└──────┘└──────┘└──────┘└──────┘└──────┘         │    │
│  │  ┌──────┐┌──────┐┌──────────────────────────────┐         │    │
│  │  │  RR  ││  HL  ││  HR + SUBWOOFER LFE          │         │    │
│  │  └──────┘└──────┘└──────────────────────────────┘         │    │
│  └─────────────────────────────────────────────────────────────┘    │
│                                                                     │
│  ┌──────────────────────┐  ┌──────────────────────────────────┐    │
│  │  POWER SUPPLIES (4)  │  │  CONNECTORS (14)                 │    │
│  │  ±42V, ±15V, +5V,   │  │  USB-C, USB-A, XLR, RCA,       │    │
│  │  +3.3V               │  │  3.5mm, 6.35mm, TOSLINK,        │    │
│  └──────────────────────┘  │  S/PDIF, HDMI ARC, RJ45,        │    │
│                             │  WiFi Ant, BT Ant               │    │
│  ┌──────────────────────┐  └──────────────────────────────────┘    │
│  │  COOLING (2)         │                                          │
│  │  Heatsink + Fan      │                                          │
│  └──────────────────────┘                                          │
└─────────────────────────────────────────────────────────────────────┘
```

### 1.2 Bileşen Dağılımı

| Kategori | Bileşen Sayısı | Kritiklik |
|----------|---------------|-----------|
| XMOS XU316 | 1 | Kritik |
| PCM3168A DAC | 1 | Kritik |
| PCM3168A ADC | 1 | Kritik |
| AK4458 DAC | 1 | Yüksek |
| Class AB Amp (CH1-CH8) | 8 | Kritik |
| LFE Subwoofer Amp | 1 | Yüksek |
| Speakers (8.1) | 11 | Yüksek |
| Power Supplies | 4 | Kritik |
| Connectors | 14 | Orta |
| Cooling | 2 | Yüksek |
| **TOPLAM** | **120** | — |

---

## 2. XMOS XU316 (1 Bileşen)

### 2.1 XCore Processor

| Özellik | Değer |
|---------|-------|
| Çekirdek | 16 tile, 32-bit RISC (XCore XS3) |
| Hız | 500 MHz per core |
| RAM | 512KB SRAM |
| Flash | 16MB Quad-SPI |
| USB | USB 2.0 High-Speed |
| I2S | 8-kanal TX/RX (master/slave) |
| DSP | Zero-latency hardware DSP |
| Clock | PLL ile senkronizasyon |
| Güç | 1.0V (core) / 3.3V (I/O) |
| Kullanım | USB UAC2 streaming, I2S master clock |
| Referans | https://github.com/xmos/lib_xua |

### 2.2 USB UAC2 Interface

| Özellik | Değer |
|---------|-------|
| Standard | USB Audio Class 2.0 |
| Kanal | 8 out + 6 in (14 kanal toplam) |
| Bit Derinliği | 32-bit |
| Örnek Hızı | 44.1kHz — 192kHz |
| referans | https://github.com/xmos/lib_usb |

### 2.3 I2S Master

| Özellik | Değer |
|---------|-------|
| Protokol | I2S (Inter-IC Sound) |
| Master Clock | 22.5792MHz / 24.576MHz |
| Frame | 32-bit / 64-bit per channel |
| Kullanım | DAC/ADC clock generation |
| Referans | https://github.com/xmos/lib_i2s |

---

## 3. DAC/ADC Zinciri (2 Bileşen)

### 3.1 PCM3168A DAC (8-Channel Output)

| Özellik | Değer |
|---------|-------|
| Kanal | 8 output |
| Bit Derinliği | 24-bit |
| Örnek Hızı | Up to 192kHz |
| SNR | 112dB (A-weighted) |
| THD+N | -94dB (DAC) |
| Arayüz | I2S, TDM, Left-Justified |
| Çıkış | Voltage (2.1Vrms) |
| Güç | 5V (analog) / 3.3V (digital) |
| Kullanım | Ana çıkış DAC |
| Referans | https://www.ti.com/product/PCM3168A |

**PCM3168A Konfigürasyon Tablosu:**

| Pin | Fonksiyon | Değer |
|-----|-----------|-------|
| FMT0 | Format Select 0 | LOW (I2S) |
| FMT1 | Format Select 1 | LOW (I2S) |
| MD0 | Mode Select 0 | HIGH (Master) |
| MD1 | Mode Select 1 | LOW (Slave) |
| SCKI | System Clock In | 256fs / 512fs |
| BCK | Bit Clock | 32fs / 64fs |
| LRCK | Left/Right Clock | fs |

### 3.2 PCM3168A ADC (6-Channel Input)

| Özellik | Değer |
|---------|-------|
| Kanal | 6 input |
| Bit Derinliği | 24-bit |
| Örnek Hızı | Up to 96kHz |
| SNR | 110dB (A-weighted) |
| THD+N | -98dB |
| Arayüz | I2S |
| Giriş | Differential (2Vrms) |
| Kullanım | Analog giriş okuma |
| Referans | https://www.ti.com/product/PCM3168A |

### 3.3 PCM3168A Devre Diyagramı

```
                    PCM3168A DAC
              ┌──────────────────┐
  XMOS I2S ──┤ SCKI  BCK  LRCK ├──> Analog Out CH1-CH8
              │                  │
  +3.3V ─────┤ DVDD  AVDD       │
  GND ───────┤ DGND  AGND       │
              │                  │
  2.1Vrms ───┤ OUT1-OUT8        │
              └──────────────────┘

                    PCM3168A ADC
              ┌──────────────────┐
  Analog In ──┤ IN1-IN6          │
              │                  │
  XMOS I2S ──┤ SCKI  BCK  LRCK ├──> Digital Out
              │                  │
  +3.3V ─────┤ DVDD  AVDD       │
  GND ───────┤ DGND  AGND       │
              └──────────────────┘
```

---

## 4. AK4458 DAC (1 Bileşen)

| Özellik | Değer |
|---------|-------|
| Kanal | 8-channel |
| Bit Derinliği | 32-bit |
| Örnek Hızı | Up to 768kHz PCM / DSD512 |
| SNR | 120dB (A-weighted) |
| THD+N | -112dB |
| Çıkış | Current output (2.0mArms) |
| Arayüz | I2S, DSD |
| Kullanım | High-resolution output, DSD playback |
| Referans | https://www.akm.com/en/products/dac/ak4458 |

### 4.1 AK4458 vs PCM3168A Karşılaştırması

| Özellik | AK4458 | PCM3168A |
|---------|--------|----------|
| Kanal | 8 out | 8 out + 6 in |
| Bit | 32-bit | 24-bit |
| Max Fs | 768kHz | 192kHz |
| SNR | 120dB | 112dB |
| THD+N | -112dB | -100dB |
| DSD | DSD512 | Yok |
| Kullanım | Hi-res output | Ana DAC |
| Maliyet | ~$35 | ~$8 |

---

## 5. Class AB Amplifikatör (8 Bileşen)

### 5.1 Genel Özellikler

| Özellik | Değer |
|---------|-------|
| Toplam Kanal | 8 (CH1-CH8) |
| Güç | 100W @ 8Ω per channel |
|拓扑 | Class AB push-pull |
| Output Transistörleri | MJL21194 (NPN) / MJL21193 (PNP) |
| THD+N | < 0.01% @ 1kHz, 100W |
| Frekans | 20Hz — 20kHz (±0.5dB) |
| Damping Factor | > 200 @ 8Ω |
| SNR | > 110dB (A-weighted) |
| Koruma | DC offset, overcurrent, thermal, short circuit |
| Referans | https://www.onsemi.com/products/discrete-power-modules/transistors/mjl21194 |

### 5.2 Kanal Konfigürasyonu

| Kanal | Konum | Hoparlör | Güç |
|-------|-------|----------|-----|
| CH1 | Front Left | FL Speaker | 100W @ 8Ω |
| CH2 | Front Right | FR Speaker | 100W @ 8Ω |
| CH3 | Center | C Speaker | 100W @ 8Ω |
| CH4 | Surround Left | SL Speaker | 100W @ 8Ω |
| CH5 | Surround Right | SR Speaker | 100W @ 8Ω |
| CH6 | Rear Left | RL Speaker | 100W @ 8Ω |
| CH7 | Rear Right | RR Speaker | 100W @ 8Ω |
| CH8 | Height Left | HL Speaker | 100W @ 8Ω |

### 5.3 Class AB Devre Topolojisi

```
         +42V
          │
     ┌────┴────┐
     │  Q1 (NPN) │  ← Driver Stage
     │  MJL21194 │
     └────┬────┘
          │
     ┌────┴────┐
     │  Output  │────> Speaker
     │  Stage   │
     └────┬────┘
          │
     ┌────┴────┐
     │  Q2 (PNP) │  ← Driver Stage
     │  MJL21193 │
     └────┬────┘
          │
         -42V

     Bias Network: Vbe Multiplier
     Feedback: Global NFB (40dB)
     Compensation: Miller (dominant pole)
```

---

## 6. LFE Subwoofer Amplifikatör (1 Bileşen)

| Özellik | Değer |
|---------|-------|
|拓扑 | Class AB |
| Güç | 200W @ 4Ω |
| THD+N | < 0.005% @ 1kHz, 200W |
| Frekans | 20Hz — 200Hz (active crossover) |
| Input Sensitivity | 2.0Vrms |
| Gain | 26dB |
| Kullanım | Subwoofer LFE channel (8.1 surround) |

---

## 7. Hoparlör Dizilimi — 8.1 Surround (11 Bileşen)

### 7.1 Konum Haritası

```
                    ┌─────────────────────┐
                    │     SCREEN/WALL     │
                    └─────────────────────┘
                              │
                    ┌─────────┴─────────┐
                    │    FL (CH1)       │    FR (CH2)
                    │  ┌───────────┐    │    ┌───────────┐
                    │  │  100W@8Ω  │    │    │  100W@8Ω  │
                    │  └───────────┘    │    └───────────┘
                    └───────────────────┘
                              │
                    ┌─────────┴─────────┐
                    │   C (CH3)         │
                    │  ┌───────────┐    │
                    │  │  100W@8Ω  │    │
                    │  └───────────┘    │
                    └───────────────────┘
                              │
              ┌───────────────┴───────────────┐
              │                               │
   ┌──────────┴──────────┐     ┌──────────────┴──────────┐
   │  SL (CH4)           │     │  SR (CH5)               │
   │  ┌───────────┐      │     │  ┌───────────┐          │
   │  │  100W@8Ω  │      │     │  │  100W@8Ω  │          │
   │  └───────────┘      │     │  └───────────┘          │
   └─────────────────────┘     └─────────────────────────┘
              │                               │
   ┌──────────┴──────────┐     ┌──────────────┴──────────┐
   │  HL (CH8)           │     │  HR (height)            │
   │  ┌───────────┐      │     │  ┌───────────┐          │
   │  │  100W@8Ω  │      │     │  │  100W@8Ω  │          │
   │  └───────────┘      │     │  └───────────┘          │
   └─────────────────────┘     └─────────────────────────┘
              │                               │
   ┌──────────┴──────────┐     ┌──────────────┴──────────┐
   │  RL (CH6)           │     │  RR (CH7)               │
   │  ┌───────────┐      │     │  ┌───────────┐          │
   │  │  100W@8Ω  │      │     │  │  100W@8Ω  │          │
   │  └───────────┘      │     │  └───────────┘          │
   └─────────────────────┘     └─────────────────────────┘

                    ┌─────────────────────┐
                    │   SUBWOOFER LFE     │
                    │  ┌───────────┐      │
                    │  │  200W@4Ω  │      │
                    │  └───────────┘      │
                    └─────────────────────┘
```

### 7.2 Hoparlör Özellikleri

| Hoparlör | Konum | Impedance | Güç | Frekans | Referans |
|----------|-------|-----------|-----|---------|----------|
| Front Left (FL) | Ön sol | 8Ω | 100W | 20Hz-20kHz | — |
| Front Right (FR) | Ön sağ | 8Ω | 100W | 20Hz-20kHz | — |
| Center (C) | Ön orta | 8Ω | 100W | 100Hz-20kHz | — |
| Surround Left (SL) | Yan sol | 8Ω | 100W | 80Hz-20kHz | — |
| Surround Right (SR) | Yan sağ | 8Ω | 100W | 80Hz-20kHz | — |
| Rear Left (RL) | Arka sol | 8Ω | 100W | 80Hz-20kHz | — |
| Rear Right (RR) | Arka sağ | 8Ω | 100W | 80Hz-20kHz | — |
| Height Left (HL) | Yüksek sol | 8Ω | 100W | 80Hz-20kHz | — |
| Height Right (HR) | Yüksek sağ | 8Ω | 100W | 80Hz-20kHz | — |
| Subwoofer LFE | Alt | 4Ω | 200W | 20Hz-200Hz | — |

---

## 8. Güç Kaynakları (4 Bileşen)

### 8.1 Ana Güç Kaynağı (±42V)

| Özellik | Değer |
|---------|-------|
| Çıkış | ±42V DC (symmetric) |
| Kapasite | 800W continuous |
|拓扑 | 4× LM5122 interleaved boost |
| Verimlilik | 96% |
| Koruma | UVP, OVP, OCP, OTP |
| Kullanım | Class AB amplifikatör ana güç |
| Referans | https://www.ti.com/product/LM5122 |

### 8.2 Op-Amp Güç (±15V)

| Özellik | Değer |
|---------|-------|
| Çıkış | ±15V DC |
| Kapasite | 50W |
| Ripple | < 1mVrms |
| Kullanım | Op-amp, preamp, analog devreler |

### 8.3 Digital Logic Güç (+5V)

| Özellik | Değer |
|---------|-------|
| Çıkış | +5V DC |
| Kapasite | 30A |
| Regülasyon | ±1% |
| Kullanım | XMOS, logic gates, relay drivers |

### 8.4 DSP Güç (+3.3V)

| Özellik | Değer |
|---------|-------|
| Çıkış | +3.3V DC |
| Kapasite | 10A |
| Regülasyon | ±0.5% |
| Kullanım | XMOS XU316, DSP, digital audio |

### 8.5 Güç Kaynağı Diyagramı

```
┌──────────────────────────────────────────────────────────┐
│                  POWER SUPPLY SYSTEM                      │
│                                                          │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐     │
│  │ AC Input    │  │ +5V Rail    │  │ +3.3V Rail  │     │
│  │ 220V/110V   │──│ (30A)       │──│ (10A)       │     │
│  │ 50/60Hz     │  │             │  │             │     │
│  └──────┬──────┘  └─────────────┘  └─────────────┘     │
│         │                                                │
│  ┌──────┴──────┐  ┌─────────────┐                       │
│  │ ±42V Boost  │  │ ±15V Rail   │                       │
│  │ (4×LM5122)  │  │ (50W)       │                       │
│  │ 800W        │  │             │                       │
│  └──────┬──────┘  └─────────────┘                       │
│         │                                                │
│  ┌──────┴──────┐                                        │
│  │ UVP/OVP/OCP │                                        │
│  │ Protection  │                                        │
│  └─────────────┘                                        │
└──────────────────────────────────────────────────────────┘
```

---

## 9. Konnektörler (14 Bileşen)

### 9.1 Giriş Konnektörleri

| Konnektör | Tip | Kullanım | Özellik |
|-----------|-----|----------|---------|
| USB-C | USB Audio 2.0 | Ana dijital giriş | 24-pin, 3A |
| USB-A | USB 3.0 | peripheral连接 | Legacy device support |
| XLR Balanced (×2) | balanced analog | Profesyonel giriş | 3-pin, gold-plated |
| RCA Unbalanced (×2) | unbalanced analog | Consumer giriş | Gold-plated |
| 3.5mm TRS | headphone/line | Kulaklık çıkışı | Gold-plated |
| 6.35mm TRS | instrument | Profesyonel çıkış | Gold-plated |
| Optical TOSLINK | S/PDIF | Dijital optik giriş | 24-bit/192kHz |
| Coaxial S/PDIF | S/PDIF | Dijital koaksiyel | 75Ω impedance |

### 9.2 Çıkış Konnektörleri

| Konnektör | Tip | Kullanım | Özellik |
|-----------|-----|----------|---------|
| XLR Balanced (×8) | balanced analog | Hoparlör çıkışları | gold-plated, locking |
| HDMI ARC | HDMI ARC | TV audio return | CEC support |
| Ethernet RJ45 | Gigabit Ethernet | Ağ bağlantısı | Cat6, shielded |

### 9.3 Wireless Antenler

| Anten | Tip | Kullanım | Özellik |
|-------|-----|----------|---------|
| Wi-Fi Antenna | 2.4/5GHz | Kablosuz ağ | omnidirectional |
| Bluetooth Antenna | BLE 5.3 | Kablosuz ses | integrated, 10m |

---

## 10. Soğutma Sistemi (2 Bileşen)

### 10.1 Heatsink Assembly

| Özellik | Değer |
|---------|-------|
| Malzeme | Aluminum (anodized) |
| Boyut | 200mm × 100mm × 50mm |
| Yüzey Alanı | 0.5m² (with fins) |
| Thermal Resistance | 0.3°C/W |
| Kullanım | Class AB transistor cooling |
| Referans | https://www.fischer-sk.com/heatsinks |

### 10.2 Active Cooling Fan

| Özellik | Değer |
|---------|-------|
| Boyut | 80mm × 80mm × 25mm |
| Hız | 800-2000 RPM (PWM controlled) |
| Hava Akışı | 25 CFM max |
| Gürültü | < 25 dBA @ 1000 RPM |
| Sensör | KSD301 thermal cutoff @ 85°C |
| Kullanım | Heatsink active cooling |

### 10.3 Termal Yönetim Diyagramı

```
┌──────────────────────────────────────────────┐
│           THERMAL MANAGEMENT                  │
│                                              │
│  ┌─────────────┐     ┌─────────────┐        │
│  │ Temperature │────>│ PWM Control │        │
│  │ Sensors     │     │ Logic       │        │
│  │ (NTC×8)     │     │             │        │
│  └─────────────┘     └──────┬──────┘        │
│                              │               │
│  ┌─────────────┐     ┌──────┴──────┐        │
│  │ KSD301      │     │ Fan Driver  │        │
│  │ Thermal     │────>│ (MOSFET)    │        │
│  │ Cutoff      │     │             │        │
│  │ @ 85°C      │     └──────┬──────┘        │
│  └─────────────┘            │               │
│                              ▼               │
│                    ┌─────────────┐          │
│                    │ 80mm Fan    │          │
│                    │ 800-2000RPM │          │
│                    └─────────────┘          │
└──────────────────────────────────────────────┘
```

---

## 11. Bileşen Sayacı

| # | Kategori | Alt Bileşenler | Toplam |
|---|----------|---------------|--------|
| 1 | XMOS XU316 | XCore, USB UAC2, I2S Master | 3 |
| 2 | PCM3168A DAC | 8-channel, 24-bit, 192kHz | 1 |
| 3 | PCM3168A ADC | 6-channel, 24-bit, 96kHz | 1 |
| 4 | AK4458 DAC | 8-channel, 32-bit, 768kHz | 1 |
| 5 | Class AB Amp CH1-CH8 | 100W @ 8Ω each | 8 |
| 6 | LFE Sub Amp | 200W @ 4Ω | 1 |
| 7 | Speakers | FL, FR, C, SL, SR, RL, RR, HL, HR, Sub | 10 |
| 8 | Power Supply ±42V | 800W boost | 1 |
| 9 | Power Supply ±15V | 50W linear | 1 |
| 10 | Power Supply +5V | 30A switching | 1 |
| 11 | Power Supply +3.3V | 10A LDO | 1 |
| 12 | USB-C Connector | USB Audio 2.0 | 1 |
| 13 | USB-A Connector | USB 3.0 | 1 |
| 14 | XLR Balanced Input | 2× 3-pin | 2 |
| 15 | XLR Balanced Output | 8× 3-pin | 8 |
| 16 | RCA Unbalanced | 2× | 2 |
| 17 | 3.5mm TRS | headphone | 1 |
| 18 | 6.35mm TRS | instrument | 1 |
| 19 | Optical TOSLINK | S/PDIF | 1 |
| 20 | Coaxial S/PDIF | 75Ω | 1 |
| 21 | HDMI ARC | CEC | 1 |
| 22 | Ethernet RJ45 | Cat6 | 1 |
| 23 | Wi-Fi Antenna | 2.4/5GHz | 1 |
| 24 | Bluetooth Antenna | BLE 5.3 | 1 |
| 25 | Heatsink Assembly | Aluminum | 1 |
| 26 | Active Cooling Fan | 80mm PWM | 1 |
| | **TOPLAM** | | **120** |

---

## 12. GitHub Referansları

| Bileşen | Repository | URL |
|---------|-----------|-----|
| XMOS USB Audio | lib_xua | https://github.com/xmos/lib_xua |
| XMOS I2S | lib_i2s | https://github.com/xmos/lib_i2s |
| XMOS USB | lib_usb | https://github.com/xmos/lib_usb |
| XMOS General | xmos | https://github.com/xmos |
| PCM3168A | TI Product Page | https://www.ti.com/product/PCM3168A |
| AK4458 | AKM Product Page | https://www.akm.com/en/products/dac/ak4458 |
| LM5122 | TI Product Page | https://www.ti.com/product/LM5122 |
| MJL21194 | ON Semi | https://www.onsemi.com/products/discrete-power-modules/transistors/mjl21194 |

---

## 13. İlgili Dosyalar

| Dosya | İlişki |
|-------|--------|
| [[k0-os-layer]] | K1 donanımını destekler |
| [[k2-driver-layer]] | K1 donanımını kontrol eder |
| [[k3-audio-engine]] | K1 ses sinyalini işler |
| [[electronics/amplifier-classab-circuit]] | K1 Class AB devre detayları |
| [[electronics/bom-classab]] | K1 bileşen listesi |
| [[electronics/pcb-classab]] | K1 PCB tasarım kuralları |
| [[electronics/power-supply-classab]] | K1 güç kaynağı detayları |

---

## Class AB Amplifikatör Donanımı

Bu katman Class AB amplifikatör donanımını kapsar:
- [[electronics/amplifier-classab-circuit]] — MJL21194/MJL21193 Darlington topolojisi
- [[electronics/power-supply-classab]] — LM5122 boost converter (±35V)
- [[electronics/thermal-design-classab]] — Fischer SK53-100-SA heatsink
- [[electronics/pcb-classab]] — 6-layer PCB design
- [[electronics/bom-classab]] — 1020 bileşen BOM

### Kritik Bileşenler
| Bileşen | Görev | Datasheet |
|---------|-------|-----------|
| MJL21194 | NPN Output | ON Semi |
| MJL21193 | PNP Output | ON Semi |
| LM5122 | Boost Controller | TI |
| Fischer SK53 | Heatsink | Fischer |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Version:** 1.0.0
**Status:** draft
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
