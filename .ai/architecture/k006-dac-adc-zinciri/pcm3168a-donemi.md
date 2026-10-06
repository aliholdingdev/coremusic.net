---
title: "K006 — PCM3168A Dönüşüm Aşaması"
type: architecture-sublayer
category: architecture
version: 1.0.0
status: active
authority: "SSOT — alt katman dokümanı (şablon: alt-katman-template)"
updated: 2026-10-06
---

# K006 — PCM3168A Dönüşüm Aşaması

**K numarası:** K006 · **Klasör:** `k006-dac-adc-zinciri` · **Dosya:** `pcm3168a-donemi`
**Üst katman:** [[index.md]] (K006 klasör dizini) · **Alan:** A0
**Kanıt:** (i) diskte bu dosya · **Durum:** implemented (kaynak: salt-okunur yedek)
**Sorumlu persona:** `audio-hardware-engineer` · `dsp-firmware-engineer`

## Amaç

PCM3168A 32-bit 8-kanal ADC'nin dönüşüm aşamasını belgelemek: analog giriş sinyalinin
dijital formata çevrimini, giriş filtre/dekuplaj düzenini, pin konfigürasyonunu ve
I2S + I2C arayüzlerini tek düğümde toplamak. Zincirin genel clock/EMİ yapısı
[[dac-adc-zinciri-mimari]] düğümündedir; bu dosya **çipin kendisini** sahiplenir.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| PCM3168A teknik spesifikasyonları, pin tablosu | Uçtan uca clock hiyerarşisi (→ [[dac-adc-zinciri-mimari]]) |
| Analog giriş DC bloklama + EMI seri dirençleri | XLR/RCA konnektör fizikliği (→ [[../k008-analog-giris/index]]) |
| I2S çıkış eşlemesi (DOUTA/B → XMOS SD0/SD1) | AK4458 DAC tarafı (→ [[../k007-ak4458-xmos/index]]) |
| I2C kontrol adresi, format/master mod pinleri | DSP algoritması (→ [[index.md]] üst katman) |

## Arayüz

- **Analog yukarı:** XLR/RCA girişler → `VINL1±/VINR1±` → 100nF DC bloklama →
  `AINL1±/AINR1±` (Pin 3–18).
- **Dijital aşağı:** `DOUTA/B`, `BCK`, `LRCK`, `SCKI` → XMOS XU316 (`SD0/SD1/SCK/WS/MCLK`).
- **Kontrol:** `SDA` → XMOS `GPIO[0]`, `SCL` → XMOS `GPIO[1]`, `ADDR` → GND (I2C 0x8C).
- **Güç:** `AVDD = 5V` (analog), `DVDD = 3.3V` (dijital) — kaynak L25.

## İçerik / Bileşenler

> **Aktarım kuralı:** aşağıdaki bloklar salt-okunur kaynaktan **verbatim** (birebir)
> aktarılmıştır; her bloğun üstünde gerçek disk kanıt aralığı verilir. Kaynakta olmayan
> hiçbir değer üretilmemiştir.

### 4.1 — Genel Bakış + Teknik Spesifikasyonlar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/pcm3168a-dac-adc.md` - L10-L27

## Genel Bakış

PCM3168A, Texas Instruments tarafından üretilen 32-bit çözünürlüklü, 8 kanallı (4 stereo) high-performance analog-to-analog dönüştürücüdür. COREMUSIC'ta analog giriş sinyallerini dijital formata dönüştürmek için kullanılır. I2S ve TDM formatlarını destekler.

## Teknik Spesifikasyonlar

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

### 4.2 — Devre Tasarımı (Temel Bağlantılar)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/pcm3168a-dac-adc.md` - L29-L55

## Devre Tasarımı

### Temel Bağlantılar

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

### Filtre ve Kondansatörler

| Referans | Değer | Açıklama |
|----------|-------|----------|
| C1-C8 | 100nF MLCC | Giriş DC bloklama |
| C9-C12 | 1µF MLCC | AVDD dekuplajı |
| C13-C16 | 10µF Elektrolitik | DVDD bulk |
| C17-C20 | 100nF MLCC | DVDD dekuplajı |
| R1-R8 | 100Ω | Giriş seri direnç (EMI) |
| R9-R10 | 4.7kΩ | I2C pull-up |

### 4.3 — Pin Konfigürasyonu

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/pcm3168a-dac-adc.md` - L68-L97

## Pin Konfigürasyonu

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

### 4.4 — Bağımlılıklar + Implementasyon Durumu

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/pcm3168a-dac-adc.md` - L99-L117

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | TQFP-48 pad layout |
| K1 XMOS | Bağlantı | I2S output → XMOS input |
| K1 Konnektörler | Bağlantı | XLR/RCA giriş |
| K1 Güç Kaynağı | Alt | ±5V analog, +3.3V dijital |
| K5 Analog Sinyal | Üst | Dijital çıkış → DSP'ye |

## Durum: Implementasyon

**Durum**: 🟢 Hazır

- I2C adresi: 0x8C (ADDR = GND)
- Format: I2S (FMT0=0, FMT1=0)
- Master mod: XMOS Master, PCM3168A Slave (MD0=0, MD1=0)
- Gain ayarı: I2C üzerinden programlanabilir (0dB ile +31.5dB)
- DC offset kalibrasyonu: Otomatik (power-on reset)

## Kurallar

1. **Format sabiti:** FMT0=0, FMT1=0 → I2S modu; TDM'ye geçiş yalnız ADR ile
   (kaynak L114: "Format: I2S (FMT0=0, FMT1=0)").
2. **Clock yönü:** PCM3168A her zaman **slave**; `SCKI/BCK/LRCK` XMOS'tan gelir
   (kaynak L115).
3. **I2C adresi 0x8C** (ADDR = GND) — ikinci bir ADC bu adrese konmaz (kaynak L113).
4. **Giriş koruma:** her analog kanalda 100nF DC bloklama + 100Ω seri EMI direnci
   zorunludur (kaynak L61, L65); direnç/kondansatör olmadan PCB onayı verilmez.
5. **Kısıt:** kaynakta olmayan ölçüm değeri `⚠️ VERIFICATION REQUIRED` ile işaretlenir.

## Bağımlılık Notu

| Ok | Tür | Kaynak |
|----|-----|--------|
| K006 (PCM3168A) → K006 (zincir) | **çağrı** | bu dosya §4.2 (SCKI/BCK/LRCK eşlemesi) |
| K006 → K008 | **gösterim** (analog giriş) | bu dosya §4.2 (XLR/RCA → AIN) |
| K006 → K007 | **gösterim** (XMOS köprüsü) | bu dosya §4.2 (GPIO[0:1] I2C) |

> Bu dosya yeni bağımlılık oku **eklemez** (şablon §4.4).

## İlgili Dosyalar

[[index.md]] · [[dac-adc-zinciri-mimari]] · [[../k007-ak4458-xmos/index]] · [[../k008-analog-giris/index]]

---

**Aktarım Künyesi:**

| Kaynak (salt-okunur) | Toplam satır | Aktarılan aralık |
|---|---:|---|
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/pcm3168a-dac-adc.md` | 117 | L10–L117 (§4.1–§4.4) |
