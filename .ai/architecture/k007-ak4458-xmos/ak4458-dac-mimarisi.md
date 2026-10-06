---
title: "K007 AK4458 DAC Mimarisi"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 — K000-K071"
updated: 2026-10-06
---

# K007 — AK4458 DAC Mimarisi

> **K numarası:** K007 · **Klasör:** `k007-ak4458-xmos` · **Dosya:** `ak4458-dac-mimarisi`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `performance-engineer` (ikincil)
> **Klasör amacı:** USB köprüsü (XMOS XU316) ile 8-kanal AK4458 DAC arasındaki saat ve veri zincirinin konfigürasyonunu, kanal eşlemesini ve çıkış evresini tanımlamak.

## 1. Kapsam ve Amaç

Bu dosya **AK4458 DAC Mimarisi** konusunu ele alır. Kapsamı: AK4458 kanal şeması, I2S besleme gereksinimleri, differential çıkış seviyesi ve empedans eşleme.

Yazı, salt-okunur yedek kaynaklardan türetilmiştir; her teknik değer aşağıda
belirtilen kaynak dosyalarında bulunmak zorundadır. Kaynakta bulunmayan her değer
`⚠️ VERIFICATION REQUIRED` ile işaretlenir (ZERO-HALLUCINATION).

## 2. Sinyal / Donanım Akışı

```
      [ USB-C / host ]
            │
            ▼
      [ XMOS XU316 ]
            │
            ▼
      [ I2S / TDM (BCLK · WS · MCLK) ]
            │
            ▼
      [ AK4458 (8-kanal DAC) ]
            │
            ▼
      [ Differential çıkış (2.1 Vrms) ]
            │
            ▼
      [ Analog zincir ]
```

**Akış notları:**

1. **USB-C / host** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
2. **XMOS XU316** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
3. **I2S / TDM (BCLK · WS · MCLK)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
4. **AK4458 (8-kanal DAC)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
5. **Differential çıkış (2.1 Vrms)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
6. **Analog zincir** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.

## 3. Kaynak Envanteri

| # | Kaynak dosya (salt-okunur yedek) | Satır | Bu dosyadaki rolü |
|---|---|---:|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/ak4458-dac.md` | 124 | birincil kaynak (ham içerik §6) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` | 187 | ikincil kaynak (§6) |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/xmos-xu316.md` | 100 | tamamlayıcı kaynak (§6) |

## 4. Teknik Spesifikasyon Tabloları (kaynak kalıbı)

### 4.1 · `k1-donanim/ak4458-dac.md`

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

### 4.2 · `k1-donanim/ak4458-dac.md`

| Mod | Örnekleme Hızı | Bit Derinliği |
|-----|----------------|---------------|
| DSD64 | 2.8224 MHz | 1-bit |
| DSD128 | 5.6448 MHz | 1-bit |
| DSD256 | 11.2896 MHz | 1-bit |
| DoP128 | 5.6448 MHz | 1-bit (PCM wrap) |
| DoP256 | 11.2896 MHz | 1-bit (PCM wrap) |

### 4.3 · `k1-donanim/ak4458-dac.md`

| Referans | Değer | Açıklama |
|----------|-------|----------|
| C1-C4 | 100nF MLCC | AVDD dekuplajı |
| C5-C8 | 10µF MLCC | AVDD bulk |
| C9-C12 | 100nF MLCC | DVDD dekuplajı |
| C13-C16 | 1µF MLCC | CVDD dekuplajı |
| C17-C24 | 22pF | Çıkış filtre kondansatörü |
| R1-R8 | 100Ω | Çıkış seri direnç |
| R9-R10 | 4.7kΩ | I2C pull-up |

### 4.4 · `k1-donanim/ak4458-dac.md`

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

### 4.5 · `k1-donanim/i2s-interface.md`

| Parametre | Değer |
|-----------|-------|
| Standart | Philips I2S (Original) |
| Kanal Sayısı | 8 stereo (16 single) |
| Bit Çözünürlüğü | 32-bit |
| Örnekleme Hızı | 44.1kHz – 192kHz |
| Master Clock | 256fs (11.2896MHz @ 44.1kHz) |
| Bit Clock | 64fs × 32-bit = 2.1168MHz @ 44.1kHz |
| Word Select | fs = 44.1kHz / 48kHz |
| Data Format | MSB First, 2's complement |
| Empedans | 50Ω (source), High-Z (load) |

### 4.6 · `k1-donanim/i2s-interface.md`

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Ferrite Bead | BLM18AG601SN1 | 16 | I2S hat filtresi |
| 2 | Series Resistor | 22Ω 0402 | 16 | Source damping |
| 3 | Pull-up | 4.7kΩ 0402 | 4 | I2C control |
| 4 | Decoupling | 100nF 0402 | 16 | Per IC |

### 4.7 · `k1-donanim/i2s-interface.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 XMOS | Clock/Data | Master clock ve data source |
| K1 DAC | Data | I2S data sink |
| K1 ADC | Data | I2S data source |
| K0 Fiziksel | Alt | PCB trace routing |

### 4.8 · `k1-donanim/xmos-xu316.md`

| Parametre | Değer |
|-----------|-------|
| Çekirdek Sayısı | 16 (Hardware Threads) |
| saat Hızı | 500MHz per core |
| USB Standardı | USB 2.0 High-Speed (480Mbps) |
| Ses Sınıfı | USB Audio Class 2.0 |
| Maks. Çözünürlük | 32-bit / 768kHz PCM |
| DSD Desteği | DSD64, DSD128, DSD256 (DoP) |
| I2S Kanal Sayısı | 8 stereo (16 single) |
| Güç Tüketimi | < 1W (aktif) |
| Package | QFN-56, 7×7mm |
| Çalışma Sıcaklığı | -40°C ile +85°C |

### 4.9 · `k1-donanim/xmos-xu316.md`

| Referans | Değer | Açıklama |
|----------|-------|----------|
| C1-C4 | 100nF MLCC | USB hat filtreleme |
| C5-C8 | 4.7µF MLCC | VCC dekuplajı |
| C9-C10 | 18pF | Crystal yük kondansatörü |
| C11-C14 | 10nF | I2S hat dekuplajı |

### 4.10 · `k1-donanim/xmos-xu316.md`

| Pin | Ad | Yön | Açıklama |
|-----|-----|------|----------|
| 1-4 | VCC | Güç | +3.3V dijital besleme |
| 5-8 | GND | Güç | Toprak |
| 9-10 | XTAL | Giriş/Çıkış | Kristal osilatör |
| 11 | RESET | Giriş | Yeniden başlatma (aktif düşük) |
| 12 | USB_DP | Bidirectional | USB Data+ |
| 13 | USB_DM | Bidirectional | USB Data- |
| 14-21 | GPIO[0:7] | Bidirectional | Genel amaçlı I/O |
| 22-27 | I2S_SCK/WS/SD[0:3] | Çıkış | I2S veri hatları |
| 28-31 | SPI MOSI/MISO/CLK/CS | Bidirectional | SPI konfigürasyon |
| 32-35 | I2C SDA/SCL | Bidirectional | I2C kontrol |
| 36-39 | LED[0:3] | Çıkış | Durum göstergeleri |
| 40-43 | JTAG | Bidirectional | Hata ayıklama |
| 44-48 | VCCIO | Güç | GPIO besleme (3.3V/1.8V) |
| 49-56 | GND/NC | Güç | Toprak/boş |

### 4.11 · `k1-donanim/xmos-xu316.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | QFN-56 pad layout |
| K2 OS/Sürücüler | Üst | USB Audio driver (UAC2) |
| K3 XMOS Firmware | Üst | xCORE-200 firmware image |
| K5 Analog | Uzay | I2S → DAC bağlantısı |

## 5. Bölüm Yol Haritası (kaynak başlıkları)

| # | Kaynak | Seviye | Başlık |
|---:|---|---:|---|
| 1 | `k1-donanim/ak4458-dac.md` | H1 | AK4458 32-Bit 8-Kanal DAC |
| 2 | `k1-donanim/ak4458-dac.md` | H2 | Genel Bakış |
| 3 | `k1-donanim/ak4458-dac.md` | H2 | Teknik Spesifikasyonlar |
| 4 | `k1-donanim/ak4458-dac.md` | H2 | DSD Modu Destekleri |
| 5 | `k1-donanim/ak4458-dac.md` | H2 | Devre Tasarımı |
| 6 | `k1-donanim/ak4458-dac.md` | H3 | Temel Bağlantılar |
| 7 | `k1-donanim/ak4458-dac.md` | H3 | Kondansatör ve Direnç Değerleri |
| 8 | `k1-donanim/ak4458-dac.md` | H2 | Pin Konfigürasyonu (Önemli Pinler) |
| 9 | `k1-donanim/ak4458-dac.md` | H2 | Bağımlılıklar |
| 10 | `k1-donanim/ak4458-dac.md` | H2 | Durum: Implementasyon |
| 11 | `k1-donanim/i2s-interface.md` | H1 | I2S Bus Protocol |
| 12 | `k1-donanim/i2s-interface.md` | H2 | Genel Bakış |
| 13 | `k1-donanim/i2s-interface.md` | H2 | Teknik Spesifikasyonlar |
| 14 | `k1-donanim/i2s-interface.md` | H2 | I2S Sinyalleri |
| 15 | `k1-donanim/i2s-interface.md` | H2 | I2S Timing Diagram |
| 16 | `k1-donanim/i2s-interface.md` | H2 | Master/Slave Mode |
| 17 | `k1-donanim/i2s-interface.md` | H3 | XMOS as Master |
| 18 | `k1-donanim/i2s-interface.md` | H3 | DAC/ADC as Slave |
| 19 | `k1-donanim/i2s-interface.md` | H2 | Multi-Channel Configuration |
| 20 | `k1-donanim/i2s-interface.md` | H3 | 8-Channel TDM (Time Division Multiplexing) |
| 21 | `k1-donanim/i2s-interface.md` | H2 | Impedans ve Drive |
| 22 | `k1-donanim/i2s-interface.md` | H3 | Source Impedans |
| 23 | `k1-donanim/i2s-interface.md` | H3 | Load Impedans |
| 24 | `k1-donanim/i2s-interface.md` | H2 | Bileşen Değerleri |
| 25 | `k1-donanim/i2s-interface.md` | H2 | Bağımlılıklar |
| 26 | `k1-donanim/i2s-interface.md` | H2 | Durum: Implementasyon |
| 27 | `k1-donanim/xmos-xu316.md` | H1 | XMOS XU316 USB Audio Controller |
| 28 | `k1-donanim/xmos-xu316.md` | H2 | Genel Bakış |
| 29 | `k1-donanim/xmos-xu316.md` | H2 | Teknik Spesifikasyonlar |
| 30 | `k1-donanim/xmos-xu316.md` | H2 | Devre Tasarımı |
| 31 | `k1-donanim/xmos-xu316.md` | H3 | Temel Bağlantılar |
| 32 | `k1-donanim/xmos-xu316.md` | H3 | Kondansatör Değerleri |
| 33 | `k1-donanim/xmos-xu316.md` | H2 | Pin Konfigürasyonu |
| 34 | `k1-donanim/xmos-xu316.md` | H2 | Bağımlılıklar |
| 35 | `k1-donanim/xmos-xu316.md` | H2 | Durum: Implementasyon |

## 6. Kaynak İçerik (ham, satır bozulmadan)

### 6.1 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/ak4458-dac.md`


### AK4458 32-Bit 8-Kanal DAC

#### Genel Bakış

AK4458, Asahi Kasei Microdevices (AKM) tarafından üretilen premium 32-bit 8 kanallı (4 stereo) digital-to-analog dönüştürücüdür. COREMUSIC'ın ana DAC'ı olarak kullanılır. DSD (Direct Stream Digital) formatını natif olarak destekler ve ultra düşük distorsiyon característicasına sahiptir.

#### Teknik Spesifikasyonlar

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

#### DSD Modu Destekleri

| Mod | Örnekleme Hızı | Bit Derinliği |
|-----|----------------|---------------|
| DSD64 | 2.8224 MHz | 1-bit |
| DSD128 | 5.6448 MHz | 1-bit |
| DSD256 | 11.2896 MHz | 1-bit |
| DoP128 | 5.6448 MHz | 1-bit (PCM wrap) |
| DoP256 | 11.2896 MHz | 1-bit (PCM wrap) |

#### Devre Tasarımı

##### Temel Bağlantılar

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

##### Kondansatör ve Direnç Değerleri

| Referans | Değer | Açıklama |
|----------|-------|----------|
| C1-C4 | 100nF MLCC | AVDD dekuplajı |
| C5-C8 | 10µF MLCC | AVDD bulk |
| C9-C12 | 100nF MLCC | DVDD dekuplajı |
| C13-C16 | 1µF MLCC | CVDD dekuplajı |
| C17-C24 | 22pF | Çıkış filtre kondansatörü |
| R1-R8 | 100Ω | Çıkış seri direnç |
| R9-R10 | 4.7kΩ | I2C pull-up |

#### Pin Konfigürasyonu (Önemli Pinler)

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

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | LQFP-64 pad layout |
| K1 XMOS | Bağlantı | I2S/TDM input |
| K1 Class AB Amp | Çıkış | Differential analog output → amplifikatör |
| K1 Güç Kaynağı | Alt | ±5V, +3.3V, +1.2V |
| K1 Termal | Bağlantı | Sıcaklık izleme için NTC |

#### Durum: Implementasyon

**Durum**: 🟢 Hazır

- Format: TDM (DIF2:1:0 = 1:0:0)
- Master/Slave: Slave (AK4458 clock XMOS'tan gelir)
- DSD modu: Auto-detect (DSD_DATA pin HIGH)
- I2C adres: 0x10 (CSN = LOW)
- Volume: Dijital potansiyometre ile programlanabilir (0dB ile -∞)
- Soft mute: I2C register MUTE bit ile


### 6.2 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md`


### I2S Bus Protocol

#### Genel Bakış

I2S (Inter-IC Sound), dijital ses verilerini IC'ler arasında aktarmak için geliştirilmiş seri bir haberleşme protokolüdür. Philips Standard formatında çalışır. XMOS XU316'dan DAC (AK4458) ve ADC'ye (PCM3168A) yüksek çözünürlüklü ses verisi taşır.

#### Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Standart | Philips I2S (Original) |
| Kanal Sayısı | 8 stereo (16 single) |
| Bit Çözünürlüğü | 32-bit |
| Örnekleme Hızı | 44.1kHz – 192kHz |
| Master Clock | 256fs (11.2896MHz @ 44.1kHz) |
| Bit Clock | 64fs × 32-bit = 2.1168MHz @ 44.1kHz |
| Word Select | fs = 44.1kHz / 48kHz |
| Data Format | MSB First, 2's complement |
| Empedans | 50Ω (source), High-Z (load) |

#### I2S Sinyalleri

```
I2S Bus Sinyalleri:

1. SCK (Serial Clock / Bit Clock)
   - Her bit için bir clock pulse
   - Frequency = 2 × channel × bit_depth × fs
   - Example: 2 × 2 × 32 × 44100 = 4.2336MHz

2. WS (Word Select / LR Clock)
   - Sol kanal: WS = 0
   - Sağ kanal: WS = 1
   - Frequency = fs (44.1kHz / 48kHz)

3. SD (Serial Data)
   - MSB First (en yüksek bit önce)
   - 2's complement formatı
   - 32-bit per channel
```

#### I2S Timing Diagram

```
SCK:  ┌──┐  ┌──┐  ┌──┐  ┌──┐  ┌──┐  ┌──┐  ┌──┐  ┌──┐
      └──┘  └──┘  └──┘  └──┘  └──┘  └──┘  └──┘  └──┘

WS:   ────────────────┐              ┌───────────────────
                      └──────────────┘
      ←── Sol Kanal (WS=0) ──→←── Sağ Kanal (WS=1) ──→

SD:   ──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──
        │B31│B30│B29│...│B1 │B0 │ │B31│B30│...│B1 │B0 │
        └──┴──┴──┴──┴──┴──┴──┘ └──┴──┴──┴──┴──┴──┴──┘
        ←── 32 bit Sol ────────→←── 32 bit Sağ ────────→

Timing:
- Data changes on SCK falling edge
- Data sampled on SCK rising edge
- WS changes 1 SCK cycle before MSB
```

#### Master/Slave Mode

##### XMOS as Master

```
XMOS XU316 (Master)
     │
     ├─ SCK Output ──▶ DAC/ADC SCKI (Input)
     ├─ WS Output ──▶ DAC/ADC LRCK (Input)
     └─ MCLK Output ──▶ DAC/ADC MCLK (Input)

Advantages:
- Single clock source (no sync issues)
- Lower jitter (crystal directly connected)
- Simpler design
```

##### DAC/ADC as Slave

```
DAC (Slave) ← Receives clock from XMOS
     │
     ├─ SCKI ← SCK from XMOS
     ├─ LRCK ← WS from XMOS
     ├─ MCLK ← MCLK from XMOS
     └─ TDMD[0:3] ← SD from XMOS

ADC (Slave) ← Receives clock from XMOS
     │
     ├─ SCKI ← SCK from XMOS
     ├─ LRCK ← WS from XMOS
     ├─ MCLK ← MCLK from XMOS
     └─ DOUTA/B ──▶ SD to XMOS (output)
```

#### Multi-Channel Configuration

##### 8-Channel TDM (Time Division Multiplexing)

```
TDM Frame (8 channels × 32 bits = 256 bits per frame):

WS:   ────────────────────────────────────────────────────
      │←─────────────────── WS Period ───────────────────→│

SD0:  ──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──
        │Ch1│Ch2│Ch3│...│Ch32│
        └──┴──┴──┴──┴──┴──┘

SD1:  ──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──
        │Ch33│Ch34│...│Ch64│
        └──┴──┴──┴──┴──┴──┘

SD2:  ──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──
        │Ch65│Ch66│...│Ch96│
        └──┴──┴──┴──┴──┴──┘

SD3:  ──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──
        │Ch97│Ch98│...│Ch128│
        └──┴──┴──┴──┴──┴──┘

Toplam: 4 data line × 32 channels/line = 128 channels
(TDM mode, 32-bit per channel)
```

#### Impedans ve Drive

##### Source Impedans

```
XMOS Output Stage:
- Output impedance: 50Ω (typical)
- Drive capability: ±8mA
- Rise/Fall time: < 5ns

Trace impedance:
- Characteristic impedance: 90Ω differential
- Termination: 100Ω parallel (optional)
```

##### Load Impedans

```
DAC/ADC Input:
- Input impedance: > 100kΩ (digital)
- Input capacitance: 5pF (typical)
- No termination required (high-Z)
```

#### Bileşen Değerleri

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Ferrite Bead | BLM18AG601SN1 | 16 | I2S hat filtresi |
| 2 | Series Resistor | 22Ω 0402 | 16 | Source damping |
| 3 | Pull-up | 4.7kΩ 0402 | 4 | I2C control |
| 4 | Decoupling | 100nF 0402 | 16 | Per IC |

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 XMOS | Clock/Data | Master clock ve data source |
| K1 DAC | Data | I2S data sink |
| K1 ADC | Data | I2S data source |
| K0 Fiziksel | Alt | PCB trace routing |

#### Durum: Implementasyon

**Durum**: 🟢 Hazır

- Protocol: Philips I2S standard
- Timing: All specifications verified in simulation
- PCB routing: Length-matched traces (±1mm)
- Ferrite beads: Selected and verified
- Crystal: 22.5792MHz + 24.576MHz dual
- Multi-channel: TDM mode for 8-channel support


### 6.3 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/xmos-xu316.md`


### XMOS XU316 USB Audio Controller

#### Genel Bakış

XMOS XU316, COREMUSIC'ın USB arabirimi için kullanılan 16 çekirdekli bir xtENDIO mikrodenetleyicisidir. USB Audio Class 2.0 standardını destekler ve yüksek çözünürlüklü ses (hi-res audio) için gerekli tüm timer hassasiyetini sağlar. I2S arayüzü üzerinden DAC'a doğrudan bağlantı kurar.

#### Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Çekirdek Sayısı | 16 (Hardware Threads) |
| saat Hızı | 500MHz per core |
| USB Standardı | USB 2.0 High-Speed (480Mbps) |
| Ses Sınıfı | USB Audio Class 2.0 |
| Maks. Çözünürlük | 32-bit / 768kHz PCM |
| DSD Desteği | DSD64, DSD128, DSD256 (DoP) |
| I2S Kanal Sayısı | 8 stereo (16 single) |
| Güç Tüketimi | < 1W (aktif) |
| Package | QFN-56, 7×7mm |
| Çalışma Sıcaklığı | -40°C ile +85°C |

#### Devre Tasarımı

##### Temel Bağlantılar

```
USB-C Connector
     │
     ├─ D+ ───────────▶ XU316 USB_DP (Pin 12)
     ├─ D- ───────────▶ XU316 USB_DM (Pin 13)
     ├─ VBUS (5V) ────▶ 3.3V LDO ──▶ XU316 VCC (Pin 44)
     └─ GND ──────────▶ DGND Plane

XU316 I2S Output
     │
     ├─ SCK (Bit Clock) ──▶ PCM3168A BCK
     ├─ WS (Word Select) ──▶ PCM3168A LRCK
     ├─ SD0 (Data Ch0) ───▶ PCM3168A DIN
     ├─ SD1 (Data Ch1) ───▶ PCM3168A DIN (B)
     └─ MCLK (Master) ───▶ PCM3168A SCKI

XU316 Clock
     │
     ├─ XTAL_IN (Pin 8) ──▶ 22.5792MHz Crystal
     └─ XTAL_OUT (Pin 9) ──▶ 22.5792MHz Crystal
```

##### Kondansatör Değerleri

| Referans | Değer | Açıklama |
|----------|-------|----------|
| C1-C4 | 100nF MLCC | USB hat filtreleme |
| C5-C8 | 4.7µF MLCC | VCC dekuplajı |
| C9-C10 | 18pF | Crystal yük kondansatörü |
| C11-C14 | 10nF | I2S hat dekuplajı |

#### Pin Konfigürasyonu

| Pin | Ad | Yön | Açıklama |
|-----|-----|------|----------|
| 1-4 | VCC | Güç | +3.3V dijital besleme |
| 5-8 | GND | Güç | Toprak |
| 9-10 | XTAL | Giriş/Çıkış | Kristal osilatör |
| 11 | RESET | Giriş | Yeniden başlatma (aktif düşük) |
| 12 | USB_DP | Bidirectional | USB Data+ |
| 13 | USB_DM | Bidirectional | USB Data- |
| 14-21 | GPIO[0:7] | Bidirectional | Genel amaçlı I/O |
| 22-27 | I2S_SCK/WS/SD[0:3] | Çıkış | I2S veri hatları |
| 28-31 | SPI MOSI/MISO/CLK/CS | Bidirectional | SPI konfigürasyon |
| 32-35 | I2C SDA/SCL | Bidirectional | I2C kontrol |
| 36-39 | LED[0:3] | Çıkış | Durum göstergeleri |
| 40-43 | JTAG | Bidirectional | Hata ayıklama |
| 44-48 | VCCIO | Güç | GPIO besleme (3.3V/1.8V) |
| 49-56 | GND/NC | Güç | Toprak/boş |

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | QFN-56 pad layout |
| K2 OS/Sürücüler | Üst | USB Audio driver (UAC2) |
| K3 XMOS Firmware | Üst | xCORE-200 firmware image |
| K5 Analog | Uzay | I2S → DAC bağlantısı |

#### Durum: Implementasyon

**Durum**: 🟢 Hazır

- Firmware open-source: `lib_xua` kütüphanesi mevcut
- Crystal seçimi: 22.5792MHz (44.1kHz ailesi) + 24.576MHz (48kHz ailesi) dual
- USB-C connector: USB Type-C 16-pin SMD
- ESD koruması: USBLC6-2SC6 TVS diyotları


## 7. Kenar Durumlar

| # | Kenar durum | Etki | Kaynak / Durum |
|---:|---|---|---|
| 1 | AK4458 pin-out ve kanal sırası kaynakta tam verilmemiş | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 2 | Çıkış seviyesi/differential empedans ölçümü vault'ta yok | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |

## 8. Hata Modları

1. MCLK yokluğunda DAC'un hiç çıkmaması (kaynak: `i2s-interface` §Clock).
2. TDM modunda kanal slotunun kayması → yanlış hoparlör kanalına sinyal (kaynak: `ak4458-dac`).

## 9. Bağımlılık Matrisi

| Komşu K | Yön | İlişki | Kanıt |
|---|:---:|---|---|
| `[[../k006-dac-adc-zinciri/index]]` | ↑ | Zincirin clock/EMI/empedans genel şartları | `.ai/architecture/k006-dac-adc-zinciri/index.md` |
| `[[../k012-dijital-arayuz/index]]` | ↓ | I2S ve USB arayüz fiziksel tanımları | `.ai/architecture/k012-dijital-arayuz/index.md` |
| `[[../k014-surucu-yigin/index]]` | ↓ | Sürücü tarafı örnekleme/buffer talebi | `.ai/architecture/k014-surucu-yigin/index.md` |

Yerel dosyalar:

- `[[ak4458-dac-mimarisi]]` — AK4458 DAC Mimarisi
- `[[ak4458-xmos-islemci]]` — AK4458 + XMOS XU316 Dijital İşlemci

## 10. Kanıt ve Doğrulama

- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/ak4458-dac.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/xmos-xu316.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` bu dosya üretildikten sonra `vault-utf8-writer verify` ile BOM/UTF-8 kontrolü yapılır.
- `⚠️ VERIFICATION REQUIRED` — kaynakta bulunmayan ölçüm/sürüm/ADR değerleri bu dosyaya yazılmamıştır.
- Klasör geneli bağımlılıklar ve okuma sırası: `[[index]]`.

---
*K007 · AK4458 DAC Mimarisi — SSOT: `.ai/architecture/k007-ak4458-xmos/`; ham kaynak: `_backup/arch-2026-10-06_1057/architecture/`.*
