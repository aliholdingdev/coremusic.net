---
title: "K006 PCM3168A Dönüşüm Aşaması"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 — K000-K071"
updated: 2026-10-06
---

# K006 — PCM3168A Dönüşüm Aşaması

> **K numarası:** K006 · **Klasör:** `k006-dac-adc-zinciri` · **Dosya:** `pcm3168a-donusum-asamasi`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `data-engineer` (ikincil)
> **Klasör amacı:** Dijital sesin AK4458 üzerinden analoga, PCM3168A üzerinden geri dijital alana dönüşümünü; clock senkronizasyonu, EMI filtreleme ve empedans eşleme şartlarını tek klasörde toplamak.

## 1. Kapsam ve Amaç

Bu dosya **PCM3168A Dönüşüm Aşaması** konusunu ele alır. Kapsamı: PCM3168A 32-bit 8-kanal ADC'nin pin konfigürasyonu, filtre/kondansatör düzeni ve analog→dijital geri dönüş yolu.

Yazı, salt-okunur yedek kaynaklardan türetilmiştir; her teknik değer aşağıda
belirtilen kaynak dosyalarında bulunmak zorundadır. Kaynakta bulunmayan her değer
`⚠️ VERIFICATION REQUIRED` ile işaretlenir (ZERO-HALLUCINATION).

## 2. Sinyal / Donanım Akışı

```
      [ Host (USB-audio) ]
            │
            ▼
      [ XMOS XU316 köprü ]
            │
            ▼
      [ I2S / TDM bus (BCLK · WS · MCLK) ]
            │
            ▼
      [ AK4458 DAC (32-bit) ]
            │
            ▼
      [ Analog zincir (filtre · empedans) ]
            │
            ▼
      [ PCM3168A ADC (32-bit 8-kanal) ]
            │
            ▼
      [ I2S dönüş yolu ]
            │
            ▼
      [ Host ]
```

**Akış notları:**

1. **Host (USB-audio)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
2. **XMOS XU316 köprü** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
3. **I2S / TDM bus (BCLK · WS · MCLK)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
4. **AK4458 DAC (32-bit)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
5. **Analog zincir (filtre · empedans)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
6. **PCM3168A ADC (32-bit 8-kanal)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
7. **I2S dönüş yolu** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
8. **Host** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.

## 3. Kaynak Envanteri

| # | Kaynak dosya (salt-okunur yedek) | Satır | Bu dosyadaki rolü |
|---|---|---:|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/pcm3168a-dac-adc.md` | 118 | birincil kaynak (ham içerik §6) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/analog-sinyal-yolu.md` | 219 | ikincil kaynak (§6) |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/dac-adc-zinciri.md` | 160 | tamamlayıcı kaynak (§6) |

## 4. Teknik Spesifikasyon Tabloları (kaynak kalıbı)

### 4.1 · `k1-donanim/pcm3168a-dac-adc.md`

| Parametre | Değer |
|-----------|-------|
| Çözünürlük | 32-bit |
| Kanal Sayısı | 8 (4 stereo) |
| Örnekleme Hızı | 8kHz – 216kHz |
| Giriş Aralığı | 2.1Vrms (differential) |
| SNR | 118dB (A-Weighted) |
| THD+N | -100dB (%0.01) |
| Giriş Empedansı | 10kΩ (differential) |
| Voltaj Besleme | AVDD = 5V, DVDD = 3.3V |
| Package | TQFP-48, 7×7mm |
| Çalışma Sıcaklığı | -40°C ile +85°C |

### 4.2 · `k1-donanim/pcm3168a-dac-adc.md`

| Referans | Değer | Açıklama |
|----------|-------|----------|
| C1-C8 | 100nF MLCC | Giriş DC bloklama |
| C9-C12 | 1µF MLCC | AVDD dekuplajı |
| C13-C16 | 10µF Elektrolitik | DVDD bulk |
| C17-C20 | 100nF MLCC | DVDD dekuplajı |
| R1-R8 | 100Ω | Giriş seri direnç (EMI) |
| R9-R10 | 4.7kΩ | I2C pull-up |

### 4.3 · `k1-donanim/pcm3168a-dac-adc.md`

| Pin | Ad | Yön | Açıklama |
|-----|-----|------|----------|
| 1 | DVDD | Güç | +3.3V dijital besleme |
| 2 | DGND | Güç | Dijital toprak |
| 3-4 | AINL1± | Giriş | Sol kanal 1 diferansiyel giriş |
| 5-6 | AINR1± | Giriş | Sağ kanal 1 diferansiyel giriş |
| 7-8 | AINL2± | Giriş | Sol kanal 2 diferansiyel giriş |
| 9-10 | AINR2± | Giriş | Sağ kanal 2 diferansiyel giriş |
| 11-12 | AINL3± | Giriş | Sol kanal 3 diferansiyel giriş |
| 13-14 | AINR3± | Giriş | Sağ kanal 3 diferansiyel giriş |
| 15-16 | AINL4± | Giriş | Sol kanal 4 diferansiyel giriş |
| 17-18 | AINR4± | Giriş | Sağ kanal 4 diferansiyel giriş |
| 19 | AGND | Güç | Analog toprak |
| 20 | AVDD | Güç | +5V analog besleme |
| 21-24 | NC | - | Bağlantısız |
| 25 | DOUTA | Çıkış | Seri veri çıkışı A (Ch1-2) |
| 26 | DOUTB | Çıkış | Seri veri çıkışı B (Ch3-4) |
| 27 | BCK | Giriş/Çıkış | Bit clock |
| 28 | LRCK | Giriş/Çıkış | Word select (LR clock) |
| 29 | SCKI | Giriş | System clock input |
| 30 | FMT0 | Giriş | Format seçimi (I2S/TDM) |
| 31 | FMT1 | Giriş | Format seçimi |
| 32 | MD0 | Giriş | Master/Slave modu |
| 33 | MD1 | Giriş | Master/Slave modu |
| 34 | SDA | Bidirectional | I2C veri |
| 35 | SCL | Giriş | I2C clock |
| 36 | ADDR | Giriş | I2C adres seçimi |
| 37-48 | NC/VARIOUS | - | Diğer fonksiyonlar |

### 4.4 · `k1-donanim/pcm3168a-dac-adc.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | TQFP-48 pad layout |
| K1 XMOS | Bağlantı | I2S output → XMOS input |
| K1 Konnektörler | Bağlantı | XLR/RCA giriş |
| K1 Güç Kaynağı | Alt | ±5V analog, +3.3V dijital |
| K5 Analog Sinyal | Üst | Dijital çıkış → DSP'ye |

### 4.5 · `k1-donanim/analog-sinyal-yolu.md`

| Parametre | Değer |
|-----------|-------|
| Toplam Kazanç | 26dB (20x) |
| Frekans Aralığı | 5Hz – 80kHz (±0.5dB) |
| THD+N | < %0.001 (1kHz, 1W) |
| Sinyal/Gürültü | > 120dB (A-Weighted) |
| Giriş Empedansı | 47kΩ (balanced XLR) |
| Çıkış Empedansı | < 0.1Ω |
| Giriş Hassasiyeti | 1.5Vrms (tam çıkış için) |
| Maks. Giriş | 5Vrms (overload) |
| Maks. Çıkış | 28Vrms (250W @ 8Ω) |

### 4.6 · `k1-donanim/analog-sinyal-yolu.md`

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Input Filter R | 100Ω 0402 | 16 | EMI suppression |
| 2 | Input Filter C | 100pF C0G | 16 | Low-pass filter |
| 3 | Input Ferrite | BLM18AG601SN1 | 16 | EMI filter |
| 4 | Diff Pair R | 100Ω 1/4W | 8 | Emitter degeneration |
| 5 | VAS Load R | 1kΩ 1/4W | 2 | Collector load |
| 6 | Output Emitter R | 0.22Ω 5W | 8 | Current sharing |
| 7 | Feedback Rf | 20kΩ 0.1% | 2 | Gain setting |
| 8 | Feedback Rg | 1kΩ 0.1% | 2 | Gain setting |
| 9 | Zobel R | 10Ω 1/4W | 2 | Output damping |
| 10 | Zobel C | 100nF | 2 | Output damping |

### 4.7 · `k1-donanim/analog-sinyal-yolu.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Konnektörler | Giriş | XLR balanced input |
| K1 Diff Pair | Aşama 1 | Differential input |
| K1 VAS | Aşama 2 | Voltage amplification |
| K1 Output Stage | Aşama 3 | Power amplification |
| K1 Feedback | Geri besleme | Negative feedback |
| K1 Koruma | Çıkış | Speaker relay |
| K1 Hoparlör | Çıkış | Binding posts |

### 4.8 · `k1-donanim/dac-adc-zinciri.md`

| Parametre | Değer |
|-----------|-------|
| DAC | AK4458 (32-bit, 8-kanal) |
| ADC | PCM3168A (32-bit, 8-kanal) |
| Clock Frequency | 22.5792MHz (44.1kHz family) / 24.576MHz (48kHz family) |
| I2S Bit Clock | 1.4112MHz (44.1kHz) / 1.536MHz (48kHz) |
| Word Select | 44.1kHz / 48kHz |
| MCLK | 256fs = 11.2896MHz / 12.288MHz |
| Sinyal Seviyesi | 2.1Vrms (differential) |
| Empedans | 100Ω differential |

### 4.9 · `k1-donanim/dac-adc-zinciri.md`

| Clock | Frequency | Tolerance | Jitter |
|-------|-----------|-----------|--------|
| MCLK | 22.5792MHz | ±50ppm | < 100ps RMS |
| SCK | 1.4112MHz | ±50ppm | < 100ps RMS |
| WS | 44.1kHz | ±50ppm | < 100ps RMS |

### 4.10 · `k1-donanim/dac-adc-zinciri.md`

| Junction | Source Z | Load Z | Matched? |
|----------|----------|--------|----------|
| XMOS → DAC | 50Ω | 100Ω | No (high-Z input) |
| DAC → ADC | 25Ω | 10kΩ | No (voltage mode) |
| ADC → DSP | 100Ω | 50Ω | Yes (differential) |

### 4.11 · `k1-donanim/dac-adc-zinciri.md`

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Ferrite Bead | BLM18AG601SN1 | 16 | I2S hat filtresi |
| 2 | Decoupling Cap | 100nF MLCC | 16 | I2S dekuplajı |
| 3 | Crystal | 22.5792MHz | 1 | 44.1kHz family |
| 4 | Crystal | 24.576MHz | 1 | 48kHz family |
| 5 | Load Cap | 18pF C0G | 4 | Crystal load |
| 6 | Termination | 100Ω | 8 | I2S termination |

## 5. Bölüm Yol Haritası (kaynak başlıkları)

| # | Kaynak | Seviye | Başlık |
|---:|---|---:|---|
| 1 | `k1-donanim/pcm3168a-dac-adc.md` | H1 | PCM3168A 32-Bit 8-Kanal ADC |
| 2 | `k1-donanim/pcm3168a-dac-adc.md` | H2 | Genel Bakış |
| 3 | `k1-donanim/pcm3168a-dac-adc.md` | H2 | Teknik Spesifikasyonlar |
| 4 | `k1-donanim/pcm3168a-dac-adc.md` | H2 | Devre Tasarımı |
| 5 | `k1-donanim/pcm3168a-dac-adc.md` | H3 | Temel Bağlantılar |
| 6 | `k1-donanim/pcm3168a-dac-adc.md` | H3 | Filtre ve Kondansatörler |
| 7 | `k1-donanim/pcm3168a-dac-adc.md` | H2 | Pin Konfigürasyonu |
| 8 | `k1-donanim/pcm3168a-dac-adc.md` | H2 | Bağımlılıklar |
| 9 | `k1-donanim/pcm3168a-dac-adc.md` | H2 | Durum: Implementasyon |
| 10 | `k1-donanim/analog-sinyal-yolu.md` | H1 | Analog Sinyal Yolu |
| 11 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Genel Bakış |
| 12 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Teknik Spesifikasyonlar |
| 13 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Sinyal Yolu Diyagramı |
| 14 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Aşama 1: Giriş Filtresi |
| 15 | `k1-donanim/analog-sinyal-yolu.md` | H3 | Differential Input Filter |
| 16 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Aşama 2: Differential Pair |
| 17 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Aşama 3: VAS Stage |
| 18 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Aşama 4: Output Stage |
| 19 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Aşama 5: Feedback Network |
| 20 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Impedance Matching |
| 21 | `k1-donanim/analog-sinyal-yolu.md` | H3 | Input Impedance |
| 22 | `k1-donanim/analog-sinyal-yolu.md` | H3 | Inter-stage Impedance |
| 23 | `k1-donanim/analog-sinyal-yolu.md` | H3 | Output Impedance |
| 24 | `k1-donanim/analog-sinyal-yolu.md` | H2 | EMI Filtering |
| 25 | `k1-donanim/analog-sinyal-yolu.md` | H3 | Input EMI |
| 26 | `k1-donanim/analog-sinyal-yolu.md` | H3 | Output EMI |
| 27 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Bileşen Değerleri |
| 28 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Bağımlılıklar |
| 29 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Durum: Implementasyon |
| 30 | `k1-donanim/dac-adc-zinciri.md` | H1 | DAC → ADC Sinyal Zinciri |
| 31 | `k1-donanim/dac-adc-zinciri.md` | H2 | Genel Bakış |
| 32 | `k1-donanim/dac-adc-zinciri.md` | H2 | Teknik Spesifikasyonlar |
| 33 | `k1-donanim/dac-adc-zinciri.md` | H2 | Sinyal Yolu Diyagramı |
| 34 | `k1-donanim/dac-adc-zinciri.md` | H2 | Clock Synchronization |
| 35 | `k1-donanim/dac-adc-zinciri.md` | H3 | Master/Slave Konfigürasyonu |
| 36 | `k1-donanim/dac-adc-zinciri.md` | H3 | Clock Accuracy |
| 37 | `k1-donanim/dac-adc-zinciri.md` | H2 | EMI Filtreleme |
| 38 | `k1-donanim/dac-adc-zinciri.md` | H3 | I2S Hat Filtresi |
| 39 | `k1-donanim/dac-adc-zinciri.md` | H2 | Empedans Eşleşme |
| 40 | `k1-donanim/dac-adc-zinciri.md` | H2 | Bileşen Değerleri |
| 41 | `k1-donanim/dac-adc-zinciri.md` | H2 | Bağımlılıklar |
| 42 | `k1-donanim/dac-adc-zinciri.md` | H2 | Durum: Implementasyon |

## 6. Kaynak İçerik (ham, satır bozulmadan)

### 6.1 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/pcm3168a-dac-adc.md`


### PCM3168A 32-Bit 8-Kanal ADC

#### Genel Bakış

PCM3168A, Texas Instruments tarafından üretilen 32-bit çözünürlüklü, 8 kanallı (4 stereo) high-performance analog-to-analog dönüştürücüdür. COREMUSIC'ta analog giriş sinyallerini dijital formata dönüştürmek için kullanılır. I2S ve TDM formatlarını destekler.

#### Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Çözünürlük | 32-bit |
| Kanal Sayısı | 8 (4 stereo) |
| Örnekleme Hızı | 8kHz – 216kHz |
| Giriş Aralığı | 2.1Vrms (differential) |
| SNR | 118dB (A-Weighted) |
| THD+N | -100dB (%0.01) |
| Giriş Empedansı | 10kΩ (differential) |
| Voltaj Besleme | AVDD = 5V, DVDD = 3.3V |
| Package | TQFP-48, 7×7mm |
| Çalışma Sıcaklığı | -40°C ile +85°C |

#### Devre Tasarımı

##### Temel Bağlantılar

```
Analog Girişler (XLR/RCA)
     │
     ├─ VINL1+ ──▶ 100nF ──▶ PCM3168A AINL1+ (Pin 3)
     ├─ VINL1- ──▶ 100nF ──▶ PCM3168A AINL1- (Pin 4)
     ├─ VINR1+ ──▶ 100nF ──▶ PCM3168A AINR1+ (Pin 5)
     ├─ VINR1- ──▶ 100nF ──▶ PCM3168A AINR1- (Pin 6)
     └─ (Diğer kanallar için devam eder)

PCM3168A I2S Çıkışları
     │
     ├─ DOUTA ──▶ XMOS XU316 SD0 (Data L/R Ch1-2)
     ├─ DOUTB ──▶ XMOS XU316 SD1 (Data L/R Ch3-4)
     ├─ BCK ────▶ XMOS XU316 SCK (Bit Clock)
     ├─ LRCK ───▶ XMOS XU316 WS (Word Select)
     └─ SCKI ───▶ XMOS XU316 MCLK (Master Clock)

PCM3168A Kontrol (I2C)
     │
     ├─ SDA ──▶ XMOS GPIO[0] (I2C Data)
     ├─ SCL ──▶ XMOS GPIO[1] (I2C Clock)
     └─ ADDR ──▶ GND (I2C Address = 0x8C)
```

##### Filtre ve Kondansatörler

| Referans | Değer | Açıklama |
|----------|-------|----------|
| C1-C8 | 100nF MLCC | Giriş DC bloklama |
| C9-C12 | 1µF MLCC | AVDD dekuplajı |
| C13-C16 | 10µF Elektrolitik | DVDD bulk |
| C17-C20 | 100nF MLCC | DVDD dekuplajı |
| R1-R8 | 100Ω | Giriş seri direnç (EMI) |
| R9-R10 | 4.7kΩ | I2C pull-up |

#### Pin Konfigürasyonu

| Pin | Ad | Yön | Açıklama |
|-----|-----|------|----------|
| 1 | DVDD | Güç | +3.3V dijital besleme |
| 2 | DGND | Güç | Dijital toprak |
| 3-4 | AINL1± | Giriş | Sol kanal 1 diferansiyel giriş |
| 5-6 | AINR1± | Giriş | Sağ kanal 1 diferansiyel giriş |
| 7-8 | AINL2± | Giriş | Sol kanal 2 diferansiyel giriş |
| 9-10 | AINR2± | Giriş | Sağ kanal 2 diferansiyel giriş |
| 11-12 | AINL3± | Giriş | Sol kanal 3 diferansiyel giriş |
| 13-14 | AINR3± | Giriş | Sağ kanal 3 diferansiyel giriş |
| 15-16 | AINL4± | Giriş | Sol kanal 4 diferansiyel giriş |
| 17-18 | AINR4± | Giriş | Sağ kanal 4 diferansiyel giriş |
| 19 | AGND | Güç | Analog toprak |
| 20 | AVDD | Güç | +5V analog besleme |
| 21-24 | NC | - | Bağlantısız |
| 25 | DOUTA | Çıkış | Seri veri çıkışı A (Ch1-2) |
| 26 | DOUTB | Çıkış | Seri veri çıkışı B (Ch3-4) |
| 27 | BCK | Giriş/Çıkış | Bit clock |
| 28 | LRCK | Giriş/Çıkış | Word select (LR clock) |
| 29 | SCKI | Giriş | System clock input |
| 30 | FMT0 | Giriş | Format seçimi (I2S/TDM) |
| 31 | FMT1 | Giriş | Format seçimi |
| 32 | MD0 | Giriş | Master/Slave modu |
| 33 | MD1 | Giriş | Master/Slave modu |
| 34 | SDA | Bidirectional | I2C veri |
| 35 | SCL | Giriş | I2C clock |
| 36 | ADDR | Giriş | I2C adres seçimi |
| 37-48 | NC/VARIOUS | - | Diğer fonksiyonlar |

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | TQFP-48 pad layout |
| K1 XMOS | Bağlantı | I2S output → XMOS input |
| K1 Konnektörler | Bağlantı | XLR/RCA giriş |
| K1 Güç Kaynağı | Alt | ±5V analog, +3.3V dijital |
| K5 Analog Sinyal | Üst | Dijital çıkış → DSP'ye |

#### Durum: Implementasyon

**Durum**: 🟢 Hazır

- I2C adresi: 0x8C (ADDR = GND)
- Format: I2S (FMT0=0, FMT1=0)
- Master mod: XMOS Master, PCM3168A Slave (MD0=0, MD1=0)
- Gain ayarı: I2C üzerinden programlanabilir (0dB ile +31.5dB)
- DC offset kalibrasyonu: Otomatik (power-on reset)


### 6.2 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/analog-sinyal-yolu.md`


### Analog Sinyal Yolu

#### Genel Bakış

Analog sinyal yolu, COREMUSIC'da DAC çıkışından hoparlörlere kadar olan tüm analog aşama zincirini kapsar. Impedance matching, EMI filtering ve signal integrity bu yolun temel tasarım kriterleridir. Her aşama düşük distorsiyon ve yüksek sinyal/gürültü oranı için optimize edilmiştir.

#### Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Toplam Kazanç | 26dB (20x) |
| Frekans Aralığı | 5Hz – 80kHz (±0.5dB) |
| THD+N | < %0.001 (1kHz, 1W) |
| Sinyal/Gürültü | > 120dB (A-Weighted) |
| Giriş Empedansı | 47kΩ (balanced XLR) |
| Çıkış Empedansı | < 0.1Ω |
| Giriş Hassasiyeti | 1.5Vrms (tam çıkış için) |
| Maks. Giriş | 5Vrms (overload) |
| Maks. Çıkış | 28Vrms (250W @ 8Ω) |

#### Sinyal Yolu Diyagramı

```
┌─────────────────────────────────────────────────────────────────┐
│                   ANALOG SİNYAL YOLU                           │
│                                                                 │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐  │
│  │  XLR     │───▶│  Input   │───▶│ Diff     │───▶│  VAS     │  │
│  │  Input   │    │  Filter  │    │  Pair    │    │  Stage   │  │
│  └──────────┘    └──────────┘    └──────────┘    └─────┬────┘  │
│                                                        │       │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐         │       │
│  │ Speaker  │◀───│  Output  │◀───│  Class   │◀────────┘       │
│  │ Relay    │    │  Filter  │    │  AB Amp  │                  │
│  └────┬─────┘    └──────────┘    └──────────┘                  │
│       │                                                         │
│       ▼                                                         │
│  ┌──────────┐                                                   │
│  │Binding   │                                                   │
│  │Post      │                                                   │
│  └──────────┘                                                   │
└─────────────────────────────────────────────────────────────────┘
```

#### Aşama 1: Giriş Filtresi

##### Differential Input Filter

```
XLR Input
     │
     ├─ Pin 2 (Hot) ──▶ R1 (100Ω) ──▶ C1 (100pF) ──▶ AGND
     │                                    │
     │                                    ▼
     │                              Diff Pair Base(+)
     │
     ├─ Pin 3 (Cold) ──▶ R2 (100Ω) ──▶ C2 (100pF) ──▶ AGND
     │                                    │
     │                                    ▼
     │                              Diff Pair Base(-)
     │
     └─ Pin 1 (GND) ──▶ AGND Plane

Low-pass filter:
f-3dB = 1 / (2π × R × C)
f-3dB = 1 / (2π × 100 × 100×10⁻¹²)
f-3dB = 15.9 MHz

EMI suppression: > 40dB @ 100MHz
```

#### Aşama 2: Differential Pair

```
Diff Pair Gain:
Ad = gm × RC
Ad = 19.2mA/V × 1kΩ
Ad = 19.2 (25.7dB)

Common-mode rejection:
CMRR > 100dB @ 1kHz
```

#### Aşama 3: VAS Stage

```
VAS Gain:
Av = gm × RC
Av = 5mA/V × 10kΩ
Av = 50 (34dB)

Total open-loop gain:
Aol = Ad × Av = 19.2 × 50 = 960 (59.6dB)
```

#### Aşama 4: Output Stage

```
Output Stage:
- Unity voltage gain (emitter follower)
- Current gain: hFE = 100 (Darlington)
- Output impedance: < 0.1Ω
```

#### Aşama 5: Feedback Network

```
Closed-loop gain:
Av(cl) = 1/β = (Rf + Rg) / Rg
Av(cl) = (20kΩ + 1kΩ) / 1kΩ
Av(cl) = 21 (26.4dB)
```

#### Impedance Matching

##### Input Impedance

```
XLR Balanced Input:
- Differential impedance: 47kΩ
- Common-mode impedance: 23.5kΩ per side
- Source impedance (typical): 100Ω
- Mismatch: < 1% (excellent)
```

##### Inter-stage Impedance

```
Diff Pair → VAS:
- Diff pair output Z: ~10kΩ
- VAS input Z: ~100kΩ
- Voltage divider: 100/(100+10) = 0.91 (91% transfer)

VAS → Output Stage:
- VAS output Z: ~10kΩ
- Output stage input Z: ~100kΩ (Darlington)
- Voltage divider: 100/(100+10) = 0.91 (91% transfer)
```

##### Output Impedance

```
Output Stage:
- Zout = R_emit / (1 + hFE)
- Zout = 0.22Ω / 101
- Zout = 2.2mΩ (open loop)
- With feedback: Zout(closed) = Zout / (1 + Aol×β)
- Zout(closed) = 2.2mΩ / 477 = 4.6µΩ (negligible)

Damping Factor:
DF = Zload / Zout = 8Ω / 4.6µΩ = 1,739,130
DF >> 200 (target) ✅
```

#### EMI Filtering

##### Input EMI

```
XLR Input EMI Filter:
- Ferrite bead: BLM18AG601SN1 (600Ω @ 100MHz)
- Filter cap: 100pF C0G
- Attenuation: > 40dB @ 100MHz
```

##### Output EMI

```
Output EMI Filter:
- Ferrite bead: BLM18AG601SN1 (600Ω @ 100MHz)
- Filter cap: 100pF C0G
- Zobel network: 10Ω + 100nF (damping)
```

#### Bileşen Değerleri

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Input Filter R | 100Ω 0402 | 16 | EMI suppression |
| 2 | Input Filter C | 100pF C0G | 16 | Low-pass filter |
| 3 | Input Ferrite | BLM18AG601SN1 | 16 | EMI filter |
| 4 | Diff Pair R | 100Ω 1/4W | 8 | Emitter degeneration |
| 5 | VAS Load R | 1kΩ 1/4W | 2 | Collector load |
| 6 | Output Emitter R | 0.22Ω 5W | 8 | Current sharing |
| 7 | Feedback Rf | 20kΩ 0.1% | 2 | Gain setting |
| 8 | Feedback Rg | 1kΩ 0.1% | 2 | Gain setting |
| 9 | Zobel R | 10Ω 1/4W | 2 | Output damping |
| 10 | Zobel C | 100nF | 2 | Output damping |

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Konnektörler | Giriş | XLR balanced input |
| K1 Diff Pair | Aşama 1 | Differential input |
| K1 VAS | Aşama 2 | Voltage amplification |
| K1 Output Stage | Aşama 3 | Power amplification |
| K1 Feedback | Geri besleme | Negative feedback |
| K1 Koruma | Çıkış | Speaker relay |
| K1 Hoparlör | Çıkış | Binding posts |

#### Durum: Implementasyon

**Durum**: 🟡 Simülasyon Aşamasında

- THD: < %0.001 (LTSpice verified)
- SNR: > 120dB (calculated)
- Frequency response: 5Hz-80kHz ±0.5dB
- Impedance matching: Verified at all stages
- EMI filtering: Pre-compliance test passed
- PCB routing: Symmetrical layout planned


### 6.3 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/dac-adc-zinciri.md`


### DAC → ADC Sinyal Zinciri

#### Genel Bakış

DAC → ADC sinyal zinciri, COREMUSIC'da dijital sinyalin analog forma dönüştürülmesinden sonra tekrar dijital alana dönüştürülmesine kadar olan süreci tanımlar. Clock synchronization, impedance matching ve EMI filtering bu zincirde kritik öneme sahiptir.

#### Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| DAC | AK4458 (32-bit, 8-kanal) |
| ADC | PCM3168A (32-bit, 8-kanal) |
| Clock Frequency | 22.5792MHz (44.1kHz family) / 24.576MHz (48kHz family) |
| I2S Bit Clock | 1.4112MHz (44.1kHz) / 1.536MHz (48kHz) |
| Word Select | 44.1kHz / 48kHz |
| MCLK | 256fs = 11.2896MHz / 12.288MHz |
| Sinyal Seviyesi | 2.1Vrms (differential) |
| Empedans | 100Ω differential |

#### Sinyal Yolu Diyagramı

```
┌─────────────────────────────────────────────────────────────────┐
│                     DAC → ADC SİNYAL ZİNCİRİ                   │
│                                                                 │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐  │
│  │  XMOS    │───▶│  I2S     │───▶│   DAC    │───▶│  Analog  │  │
│  │  XU316   │    │  Bus     │    │  AK4458  │    │  Output  │  │
│  └────┬─────┘    └──────────┘    └──────────┘    └─────┬────┘  │
│       │                                                 │       │
│       │         ┌──────────┐    ┌──────────┐           │       │
│       └────────▶│  Clock   │◀───│  Crystal │           │       │
│                 │  Sync    │    │  Osc.    │           │       │
│                 └──────────┘    └──────────┘           │       │
│                                                        │       │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐         │       │
│  │  ADC     │◀───│  I2S     │◀───│  Analog  │◀────────┘       │
│  │ PCM3168A │    │  Bus     │    │  Input   │                  │
│  └────┬─────┘    └──────────┘    └──────────┘                  │
│       │                                                         │
│       ▼                                                         │
│  ┌──────────┐                                                   │
│  │  DSP     │  Dijital İşleme                                  │
│  │  Engine  │                                                   │
│  └──────────┘                                                   │
└─────────────────────────────────────────────────────────────────┘
```

#### Clock Synchronization

##### Master/Slave Konfigürasyonu

```
Clock Hierarchy:

Primary Clock Source: XMOS XU316 (Master)
     │
     ├─ MCLK Output ──▶ AK4458 SCKI (DAC Master Clock)
     │                   PCM3168A SCKI (ADC Master Clock)
     │
     ├─ SCK Output ──▶ AK4458 TDMCLK (Bit Clock)
     │                  PCM3168A BCK (Bit Clock)
     │
     ├─ WS Output ──▶ AK4458 TDMFS (Word Select)
     │                 PCM3168A LRCK (LR Clock)
     │
     └─ SD0-SD3 ──▶ AK4458 TDMD0-3 (Data)
                     PCM3168A DOUTA/B (Data)

Clock Distribution:
┌─────────────────────────────────────────────────────────┐
│                                                         │
│  XMOS XU316 (Master)                                   │
│  ├─ MCLK (22.5792MHz) ──────────────────────────┐     │
│  ├─ SCK (1.4112MHz) ────────────────────────┐   │     │
│  ├─ WS (44.1kHz) ──────────────────────┐   │   │     │
│  └─ SD[0:3] ──────────────────────┐   │   │   │     │
│                                   │   │   │   │     │
│  AK4458 (Slave) ◀────────────────┘   │   │   │     │
│  ├─ SCKI ◀──────────────────────────┘   │   │     │
│  ├─ TDMCLK ◀────────────────────────────┘   │     │
│  ├─ TDMFS ◀──────────────────────────────────┘     │
│  └─ TDMD[0:3] ◀─────────────────────────────────────┘
│                                                         │
│  PCM3168A (Slave)                                      │
│  ├─ SCKI ◀──────────────────────────────────────────────┘
│  ├─ BCK ◀───────────────────────────────────────────────┘
│  ├─ LRCK ◀──────────────────────────────────────────────┘
│  └─ DOUTA/B ───────────────────────────────────────────▶ XMOS
│                                                         │
└─────────────────────────────────────────────────────────┘
```

##### Clock Accuracy

| Clock | Frequency | Tolerance | Jitter |
|-------|-----------|-----------|--------|
| MCLK | 22.5792MHz | ±50ppm | < 100ps RMS |
| SCK | 1.4112MHz | ±50ppm | < 100ps RMS |
| WS | 44.1kHz | ±50ppm | < 100ps RMS |

#### EMI Filtreleme

##### I2S Hat Filtresi

```
XMOS Output ──▶ Ferrite Bead (600Ω @ 100MHz) ──▶ 100nF ──▶ DAC/ADC

Her I2S hattı için:
- Ferrite bead: BLM18AG601SN1 (600Ω, 0603)
- Decoupling: 100nF MLCC (0402)
- Trace length: < 50mm
- Impedans: 90Ω differential
```

#### Empedans Eşleşme

| Junction | Source Z | Load Z | Matched? |
|----------|----------|--------|----------|
| XMOS → DAC | 50Ω | 100Ω | No (high-Z input) |
| DAC → ADC | 25Ω | 10kΩ | No (voltage mode) |
| ADC → DSP | 100Ω | 50Ω | Yes (differential) |

#### Bileşen Değerleri

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Ferrite Bead | BLM18AG601SN1 | 16 | I2S hat filtresi |
| 2 | Decoupling Cap | 100nF MLCC | 16 | I2S dekuplajı |
| 3 | Crystal | 22.5792MHz | 1 | 44.1kHz family |
| 4 | Crystal | 24.576MHz | 1 | 48kHz family |
| 5 | Load Cap | 18pF C0G | 4 | Crystal load |
| 6 | Termination | 100Ω | 8 | I2S termination |

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 XMOS | Clock | Master clock source |
| K1 DAC | Çıkış | AK4458 analog output |
| K1 ADC | Giriş | PCM3168A digital output |
| K3 DSP | Üst | Dijital sinyal işleme |

#### Durum: Implementasyon

**Durum**: 🟡 Simülasyon Aşamasında

- Clock synchronization: LTSpice ile simulate edildi
- I2S timing: Eye diagram analizi yapıldı
- EMI: Pre-compliance test ile ferrite bead seçimi doğrulandı
- Crystal: Dual crystal (22.5792MHz + 24.576MHz) seçildi
- PCB routing: I2S traces length-matched (±1mm tolerance)


## 7. Kenar Durumlar

| # | Kenar durum | Etki | Kaynak / Durum |
|---:|---|---|---|
| 1 | 8 kanalın eşzamanlı örneklemesinde kanallar arası faz kayması ölçülmemiş | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 2 | ADC referans gerilimi beslemesi ayrı besleme hatından geliyor mu? Kaynakta net değil | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |

## 8. Hata Modları

1. Filtre/kondansatör değerlerinin sapması durumunda antialiasing davranışının bozulması (kaynak: `pcm3168a-dac-adc` §Filtre ve Kondansatörler).
2. Pin konfigürasyon hatalarında kanal eşlemesinin kayması (kaynak: `pcm3168a-dac-adc` §Pin Konfigürasyonu).

## 9. Bağımlılık Matrisi

| Komşu K | Yön | İlişki | Kanıt |
|---|:---:|---|---|
| `[[../k007-ak4458-xmos/index]]` | ↓ | DAC/köprü konfigürasyonu bu klasörün veri yolu devamıdır | `.ai/architecture/k007-ak4458-xmos/index.md` |
| `[[../k008-analog-giris/index]]` | ↓ | ADC girişi analog giriş yolu ile beslenir | `.ai/architecture/k008-analog-giris/index.md` |
| `[[../k012-dijital-arayuz/index]]` | ↓ | I2S/USB taşıyıcı arayüz tanımları | `.ai/architecture/k012-dijital-arayuz/index.md` |
| `[[../k014-surucu-yigin/index]]` | ↓ | Sürücü yığını buffer/clock talebini bu zincire iletir | `.ai/architecture/k014-surucu-yigin/index.md` |

Yerel dosyalar:

- `[[dac-adc-zinciri]]` — DAC–ADC Sinyal Zinciri
- `[[pcm3168a-donusum-asamasi]]` — PCM3168A Dönüşüm Aşaması

## 10. Kanıt ve Doğrulama

- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/pcm3168a-dac-adc.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/analog-sinyal-yolu.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/dac-adc-zinciri.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` bu dosya üretildikten sonra `vault-utf8-writer verify` ile BOM/UTF-8 kontrolü yapılır.
- `⚠️ VERIFICATION REQUIRED` — kaynakta bulunmayan ölçüm/sürüm/ADR değerleri bu dosyaya yazılmamıştır.
- Klasör geneli bağımlılıklar ve okuma sırası: `[[index]]`.

---
*K006 · PCM3168A Dönüşüm Aşaması — SSOT: `.ai/architecture/k006-dac-adc-zinciri/`; ham kaynak: `_backup/arch-2026-10-06_1057/architecture/`.*
