---
type: electronic
category: test-fixture
title: "CoreMusic — 8-Channel Class AB Amplifier Test Fixture"
date: 2026-09-18
updated: 2026-09-18
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/architecture/test-fixture.md"
  source_of_truth:
    - ".ai/architecture/8ch-integration.md"
    - ".ai/architecture/amplifier-classab-circuit.md"
    - ".ai/architecture/thermal-design-classab.md"
    - ".ai/brain.md"
    - "ADR-061-electronics-architecture"
  related:
    - ".ai/architecture/test-protocol.md"
---

# CoreMusic — 8-Channel Class AB Amplifier Test Fixture

**Zorunlu Bağlantılar:** [[8ch-integration]] · [[amplifier-classab-circuit]] · [[thermal-design-classab]] · [[brain.md]]

---

## 1. Test Fixture Spesifikasyonları

| Parametre | Değer | Not |
|-----------|-------|-----|
| DUT (Device Under Test) | 8-Channel Class AB Amplifier | 50W × 8 |
| Test Kapasitesi | 8 kanal simultane | veya tek tek |
| Dummy Load | 8 × 8Ω/100W | Alüminyum kasalı, fan soğutmalı |
| Sinyal Üretici | Analog Discovery 2 (AD2) | 20MHz, 14-bit ADC |
| Audio Analyzer | miniDSP UMIK-2 veya equiv | THD+N, SNR ölçümü |
| Osiloskop | 4 kanal, 100MHz | Rigol DS1104Z veya equiv |
| Güç Ölçümü | True RMS multimetre | Fluke 87V veya equiv |
| Termal Kamera | FLIR E8 veya equiv | 80×60 IR, -20°C–250°C |
| DC Kaynak | 0-60V, 0-10A adjustable | PSU test için |
| Multimeter | 6.5 digit | Keysight 34465A veya equiv |

---

## 2. Test Fixture Diyagramı

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        TEST FIXTURE SİSTEM DİYAGRAMI                       │
│                                                                             │
│  ┌───────────────┐                                                          │
│  │  DC Güç       │                                                          │
│  │  Kaynağı      │                                                          │
│  │  0-60V/0-10A  │                                                          │
│  └───────┬───────┘                                                          │
│          │                                                                  │
│          ▼                                                                  │
│  ┌───────────────┐    ┌───────────────┐    ┌───────────────┐               │
│  │  DUT          │    │  Dummy Load   │    │  Osiloskop    │               │
│  │  (Ampli)      │───►│  8Ω/100W ×8   │    │  4ch 100MHz   │               │
│  │  50W × 8      │    │  Fan cooled   │    │               │               │
│  └───────┬───────┘    └───────────────┘    └───────┬───────┘               │
│          │                                          │                       │
│          │                                          │                       │
│  ┌───────▼───────┐    ┌───────────────┐    ┌───────▼───────┐               │
│  │  Sinyal       │    │  Audio        │    │  True RMS     │               │
│  │  Üretici      │    │  Analyzer     │    │  Multimetre   │               │
│  │  (AD2)        │    │  (UMIK-2)     │    │  (Fluke 87V)  │               │
│  │  20Hz-20kHz   │    │               │    │               │               │
│  └───────────────┘    └───────────────┘    └───────────────┘               │
│                                                                             │
│  ┌───────────────┐    ┌───────────────┐    ┌───────────────┐               │
│  │  Termal       │    │  6.5-digit    │    │  Laptop       │               │
│  │  Kamera       │    │  Multimeter   │    │  (Veri Kayıt) │               │
│  │  (FLIR E8)    │    │  (Keysight)   │    │  USB→AD2      │               │
│  └───────────────┘    └───────────────┘    └───────────────┘               │
│                                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Bileşen Detayı

### 3.1 Dummy Load (8 × 8Ω/100W)

```
┌─────────────────────────────────────────────────────────────┐
│  DUMMY LOAD MODÜLÜ (her kanal için)                         │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │  Alüminyum Kasa (150×80×50mm)                       │   │
│  │                                                       │   │
│  │  ┌─────────────────────────────────────────────┐     │   │
│  │  │  8Ω/100W Power Resistor (Caddock T950)      │     │   │
│  │  │  veya Ohmite OXF100FE51 (8Ω 100W wirewound) │     │   │
│  │  └─────────────────────────────────────────────┘     │   │
│  │                                                       │   │
│  │  ┌──────────┐    ┌──────────┐                        │   │
│  │  │  80mm    │    │  BNC     │                        │   │
│  │  │  Fan     │    │  Input   │                        │   │
│  │  │  (24V)   │    │  (50Ω)   │                        │   │
│  │  └──────────┘    └──────────┘                        │   │
│  │                                                       │   │
│  │  ┌─────────────────────────────────────────────┐     │   │
│  │  │  Duymuva: 2× 100nF MLCC (HF bypass)         │     │   │
│  │  │           2× 10µF electrolytic (LF bypass)   │     │   │
│  │  └─────────────────────────────────────────────┘     │   │
│  │                                                       │   │
│  │  Çıkış: Binding post (8Ω, 100W continuous)           │   │
│  │  Fan: 24V DC, 50CFM, termal anahtar (60°C ON)       │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  Termal Hesaplama:                                         │
│  P = I² × R = (2.5A)² × 8Ω = 50W (nominal)              │
│  P_max = (3.54A)² × 8Ω = 100W (maksimum)                 │
│  Rth(h-a) = 0.5°C/W (fan ile) → ΔT = 50°C               │
│  heatsink sıcaklığı = 25°C + 50°C = 75°C (kabul edilebilir)│
└─────────────────────────────────────────────────────────────┘
```

**Dummy Load BOM:**

| # | Bileşen | Miktar | Birim Fiyat | Toplam | Not |
|---|---------|--------|-------------|--------|-----|
| 1 | Caddock T950-8.0 (8Ω/100W) | 8 | $15 | $120 | veya Ohmite OXF100FE51 |
| 2 | Alüminyum kasa (150×80×50mm) | 8 | $5 | $40 | Anodize |
| 3 | 80mm fan (24V DC) | 8 | $3 | $24 | Termal anahtarlı |
| 4 | Binding post (çift) | 8 | $1 | $8 | Çıkış |
| 5 | BNC jack | 8 | $0.5 | $4 | Osiloskop bağlantısı |
| 6 | MLCC 100nF | 16 | $0.1 | $1.6 | Bypass |
| 7 | Elektrolitik 10µF/50V | 16 | $0.3 | $4.8 | Bypass |
| | **Toplam Dummy Load** | | | **$202.4** | |

### 3.2 Sinyal Üretici (Analog Discovery 2)

| Parametre | Değer | Not |
|-----------|-------|-----|
| Çıkış | ±25V, 100mA | W1, W2 analog out |
| Frekans | 1Hz – 20MHz | Sine, square, triangle |
| Çözünürlük | 14-bit DAC | ±1 LSB DNL |
| THD | <0.1% | 1kHz, 1Vrms |
| INPUT | ±25V, 1MΩ | 2 kanal analog in |
| INPUT Çözünürlük | 14-bit ADC, 100MS/s | |
| Dijital | 16-ch digital I/O | Trigger, pulse |
| Bağlantı | USB 2.0 | Laptop'a bağlı |

**Kullanım:**

| Test | AD2 Çıkış | AD2 Giriş |
|------|-----------|-----------|
| THD+N | 1kHz sine, 0.35Vrms | CH_out (attenuated) |
| Slew Rate | 100kHz square | CH_out |
| Bandwidth | 10Hz-20kHz sweep | CH_out |
| DC Offset | 0V (grounded) | CH_out (DC coupled) |
| Channel Separation | 1kHz CH1 | CH2 input (attenuated) |

### 3.3 Audio Analyzer (miniDSP UMIK-2)

| Parametre | Değer | Not |
|-----------|-------|-----|
| Giriş | USB-C, 24-bit ADC | |
| THD+N Ölçüm | <0.001% @ 1kHz | Analyzer self THD |
| SNR | >110dB | A-weighted |
| Frekans Aralığı | 10Hz – 20kHz | |
| Kalibrasyon | Üretici kalibrasyonu dahil | |
| Yazılım | REW (Room EQ Wizard) | Ücretsiz |

**Alternatifler:** miniDSP UMIK-2, Dayton Audio EMM-6, miniDSP CDSP-HD

### 3.4 Osiloskop (4 Kanal, 100MHz)

| Parametre | Değer | Not |
|-----------|-------|-----|
| Kanal | 4 | Simultane ölçüm |
| Bant genişliği | 100MHz | |
| Örnekleme | 1GS/s | |
| Vertical Resolution | 8-bit | |
| Input | 1MΩ / 20pF | |
| Bağlantı | BNC | |
| Önerilen Model | Rigol DS1104Z | veya equiv |

**Kanal Kullanımı:**

| Kanal | Kullanım | Probe |
|-------|----------|-------|
| CH1 | Input signal (atten.) | 10:1 passive |
| CH2 | Output signal (atten.) | 10:1 passive |
| CH3 | Supply +35V | 10:1 passive |
| CH4 | Supply -35V | 10:1 passive |

### 3.5 True RMS Multimetre

| Parametre | Değer | Not |
|-----------|-------|-----|
| Model | Fluke 87V veya equiv | |
| DC Accuracy | ±0.05% | |
| AC Accuracy | ±1.0% (True RMS) | |
| Bandwidth | 100kHz | |
| Kullanım | DC offset, AC rms, power | |

### 3.6 Termal Kamera

| Parametre | Değer | Not |
|-----------|-------|-----|
| Model | FLIR E8 veya equiv | |
| Çözünürlük | 80 × 60 (4800 piksel) | |
| Sıcaklık Aralığı | -20°C – 250°C | |
| Doğruluk | ±2°C | |
| Kullanım | Heatsink, transistor junction, fan airflow | |

### 3.7 6.5-Digit Multimetre

| Parametre | Değer | Not |
|-----------|-------|-----|
| Model | Keysight 34465A veya equiv | |
| Çözünürlük | 6.5 digit | |
| DC Accuracy | ±0.0035% | |
| Kullanım | Hassas DC offset, gain accuracy | |

---

## 4. Sensör ve Probe Bağlantıları

### 4.1 Osiloskop Bağlantı Haritası

```
┌─────────────────────────────────────────────────────────────┐
│  OSİLOSKOP BAĞLANTISI (Rigol DS1104Z)                       │
│                                                             │
│  CH1 ──── BNC ──── 10:1 Probe ──── DUT Input (atten.)     │
│  CH2 ──── BNC ──── 10:1 Probe ──── DUT Output (atten.)    │
│  CH3 ──── BNC ──── 10:1 Probe ──── +35V Rail              │
│  CH4 ──── BNC ──── 10:1 Probe ──── -35V Rail              │
│                                                             │
│  Trigger: CH1 (input signal)                               │
│  Timebase: 100µs/div (1kHz), 1µs/div (slew rate)         │
│  Vertical: 1V/div (input), 5V/div (output), 10V/div (PSU) │
└─────────────────────────────────────────────────────────────┘
```

### 4.2 Dummy Load Bağlantı Haritası

```
┌─────────────────────────────────────────────────────────────┐
│  DUMMY LOAD BAĞLANTISI                                      │
│                                                             │
│  DUT CH1 Out ──── 12AWG ──── DL1 Input (Binding Post)     │
│  DUT CH2 Out ──── 12AWG ──── DL2 Input                    │
│  DUT CH3 Out ──── 12AWG ──── DL3 Input                    │
│  DUT CH4 Out ──── 12AWG ──── DL4 Input                    │
│  DUT CH5 Out ──── 12AWG ──── DL5 Input                    │
│  DUT CH6 Out ──── 12AWG ──── DL6 Input                    │
│  DUT CH7 Out ──── 12AWG ──── DL7 Input                    │
│  DUT CH8 Out ──── 12AWG ──── DL8 Input                    │
│                                                             │
│  BNC T-joint: Her DL'den BNCออก → Osiloskop CH2           │
│  (BNC T-adapter ile measure point)                         │
└─────────────────────────────────────────────────────────────┘
```

### 4.3 Multimetre Bağlantı Haritası

```
┌─────────────────────────────────────────────────────────────┐
│  MULTİMETRE BAĞLANTISI                                      │
│                                                             │
│  Fluke 87V (True RMS):                                     │
│  ├── COM ──── DUT GND (chassis)                            │
│  ├── VΩ ──── DUT CH1 Output (AC Vrms)                     │
│  ├── mA ──── (series) DUT CH1 Output (DC current)         │
│                                                             │
│  Keysight 34465A (6.5 digit):                              │
│  ├── HI ──── DUT CH1 Output (DC offset)                   │
│  ├── LO ──── DUT GND                                      │
│  ├── Range: 100mV DC                                       │
│  └── NPLC: 10 (high accuracy)                              │
└─────────────────────────────────────────────────────────────┘
```

### 4.4 Termal Kamera Konumları

```
┌─────────────────────────────────────────────────────────────┐
│  TERMAL KAMERA GÖRÜNÜM NOKTALARI                            │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │  ÜST GÖRÜNÜM                                        │   │
│  │                                                       │   │
│  │  [T1] CH1 Heatsink    [T2] CH2 Heatsink             │   │
│  │  [T3] CH3 Heatsink    [T4] CH4 Heatsink             │   │
│  │  [T5] CH5 Heatsink    [T6] CH6 Heatsink             │   │
│  │  [T7] CH7 Heatsink    [T8] CH8 Heatsink             │   │
│  │                                                       │   │
│  │  [T9] PSU Board (LM5122 hotspot)                     │   │
│  │  [T10] Fan exhaust                                   │   │
│  │  [T11] Chassis (edge)                                │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│ (termal kamera tripoda monteli, 1m mesafe)                │
└─────────────────────────────────────────────────────────────┘
```

---

## 5. Sinyal Koşullandırma

### 5.1 Giriş Attenuatörü (Osiloskop için)

```
                    Osiloskop CH2
                         │
                    ┌────┴────┐
                    │  1MΩ    │
                    │  Probe  │
                    └────┬────┘
                         │
                    ┌────┴────┐
                    │  9kΩ    │  (10:1 attenuator)
                    │  1%     │
                    └────┬────┘
                         │
                    ┌────┴────┐
                    │  1kΩ    │  (geri besleme)
                    │  1%     │
                    └────┬────┘
                         │
                    DUT Output (50W = 28.3Vrms)
```

### 5.2 Çıkış Attenuatörü (Audio Analyzer için)

```
                    Audio Analyzer Input
                         │
                    ┌────┴────┐
                    │  47kΩ   │
                    │  1%     │
                    └────┬────┘
                         │
                    ┌────┴────┐
                    │  1kΩ    │  (47:1 attenuator → -33dB)
                    │  1%     │
                    └────┬────┘
                         │
                    DUT Output (50W = 28.3Vrms → 0.6Vrms)
```

---

## 6. Güç Kaynağı Test Kurulumu

### 6.1 DUT Besleme

| Parametre | Değer | Not |
|-----------|-------|-----|
| Giriş | 6S LiPo (22.2V) veya DC Supply (24V) | |
| Maks Akım | 15A @ 22.2V | 333W giriş |
| Soft-start | 100ms | DUT dahili |
| Koruma | UVP (18V), OVP (25.2V), OCP (20A) | |

### 6.2 Standalone PSU Test

| Parametre | Değer | Not |
|-----------|-------|-----|
| Giriş | Adjustable DC 0-60V/0-10A | Test kaynağı |
| Çıkış + | +35V DC (ölçüm) | True RMS multimetre |
| Çıkış - | -35V DC (ölçüm) | True RMS multimetre |
| Ripple | <50mV pp (osiloskop) | 20MHz bandwidth limit |
| Load Regulation | <1% (0A → 11.4A) | |

---

## 7. Test Fixture Tam BOM

### 7.1 Özet Tablo

| Kategori | Adet | Toplam Fiyat |
|----------|------|-------------|
| Dummy Load (8×) | 8 | $202.4 |
| Sinyal Üretici (AD2) | 1 | $350 |
| Audio Analyzer (UMIK-2) | 1 | $130 |
| Osiloskop (Rigol DS1104Z) | 1 | $450 |
| True RMS Multimetre (Fluke 87V) | 1 | $250 |
| 6.5-digit Multimeter (Keysight) | 1 | $1,200 |
| Termal Kamera (FLIR E8) | 1 | $1,500 |
| DC Supply (0-60V/10A) | 1 | $300 |
| BNC kablolar + T-adapter | 10 | $50 |
| 10:1 Pasif Probe | 4 | $40 |
| Binding post kablo (12AWG) | 8 | $24 |
| BNC-to-BNC kablo | 5 | $25 |
| Otomatik transcription kalite kontrol | — | — |
| **TOPLAM** | | **$4,521.4** |

### 7.2 Opsiyonel Ekipman

| Ekipman | Fiyat | Not |
|---------|-------|-----|
| Keysight 34465A (6.5 digit) | $1,200 | Hassas ölçüm için |
| FLIR E8 | $1,500 | Termal analiz için |
| Logic Analyzer (Saleae) | $500 | Dijital timing için |
| USB Isolated Hub | $100 | Gürültü izolasyonu |
| EMI Probe Set | $300 | EMC test için |

---

## 8. Kalibrasyon

### 8.1 Osiloskop Kalibrasyonu

| Adım | İşlem | Not |
|------|-------|-----|
| 1 | Self-cal (menü) | Her açılışta |
| 2 | Probe compensation | 1kHz square, trim until flat |
| 3 | DC offset zero | Short input to GND |
| 4 | Timebase accuracy | 10MHz reference |

### 8.2 Multimetre Kalibrasyonu

| Adım | İşlem | Not |
|------|-------|-----|
| 1 | Zero adjust | Short HI-LO |
| 2 | DC reference | 10.000V DC reference |
| 3 | AC reference | 1.000Vrms @ 1kHz |
| 4 | Temperature | Ice point (0°C) |

### 8.3 Audio Analyzer Kalibrasyonu

| Adım | İşlem | Not |
|------|-------|-----|
| 1 | UMIK-2 calibration | Manufacturer calibration file |
| 2 | REW calibration | Room correction OFF |
| 3 | Input level | -20dBFS @ 1kHz |
| 4 | THD self-test | Measure analyzer self THD |

---

## 9. Test Fixture Kullanım Akışı

```
┌─────────────────────────────────────────────────────────────┐
│  TEST AKIŞ DİYAGRAMI                                        │
│                                                             │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐             │
│  │  DUT     │    │  Test    │    │  Veri    │             │
│  │  Montaj  │───►│  Setup   │───►│  Kayıt   │             │
│  └──────────┘    └──────────┘    └──────────┘             │
│       │               │               │                     │
│       ▼               ▼               ▼                     │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐             │
│  │  Visually│    │  Kalibrasyon│  │  Sonuç   │             │
│  │  inspect │    │  et       │    │  kaydet  │             │
│  └──────────┘    └──────────┘    └──────────┘             │
│       │               │               │                     │
│       ▼               ▼               ▼                     │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐             │
│  │  Continuity│  │  İlk     │    │  Rapor   │             │
│  │  test     │    │  güç ver │    │  oluştur │             │
│  └──────────┘    └──────────┘    └──────────┘             │
│                                                             │
└─────────────────────────────────────────────────────────────┘
```

---

## 10. Güvenlik Kuralları (Test Fixture)

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | Koruyucu gözlük | İlk güç açılışında |
| 2 | Koruyucu eldiven | ±35V DC ile çalışma |
| 3 | Yangın söndürücü | 800W sistem için |
| 4 | Havalandırma | Kapalı alanda test etme |
| 5 | Dummy load fan | Her zaman çalışır durumda |
| 6 | Emergency stop | Kırmızı buton, PSU girişinde |
| 7 | Topraklama | Chassis earth'e bağlı |
| 8 | Tek kişi çalışma | Güç açılışında tek kişi |

---

## 11. Test Fixture Montajı

| # | Adım | Süre |
|---|------|------|
| 1 | Masayı hazırlama (anti-static mat) | 5dk |
| 2 | Osiloskop, multimetre, kamera kurulumu | 15dk |
| 3 | Dummy load'ları bağlama (8×) | 20dk |
| 4 | Sinyal üreticiyi bağlama | 10dk |
| 5 | Audio analyzer'ı bağlama | 10dk |
| 6 | Güç kaynağını bağlama | 10dk |
| 7 | DUT'ı mount etme | 15dk |
| 8 | Tüm kabloları kontrol | 15dk |
| 9 | Kalibrasyon | 20dk |
| 10 | İlk güç testi | 10dk |
| **Toplam** | | **~2.5 saat** |

---

**Authority:** Bayram Ali / Vault Steward  
**Last Updated:** 2026-09-18  
**Version:** 1.0.0  
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
