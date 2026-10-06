---
title: "K007 — AK4458 DAC"
type: architecture-sublayer
category: architecture
version: 1.0.0
status: active
authority: "SSOT — alt katman dokümanı (şablon: alt-katman-template)"
updated: 2026-10-06
---

# K007 — AK4458 DAC

**K numarası:** K007 · **Klasör:** `k007-ak4458-xmos` · **Dosya:** `ak4458-dac`
**Üst katman:** [[index.md]] (K007 klasör dizini) · **Alan:** A0
**Kanıt:** (i) diskte bu dosya · **Durum:** implemented (kaynak: salt-okunur yedek)
**Sorumlu persona:** `audio-hardware-engineer` · `dsp-firmware-engineer`

## Amaç

AK4458 32-bit 8-kanal DAC'nin mimarisini belgelemek: I2S/TDM veri ve saat
girişlerinin eşlenmesini, diferansiyel analog çıkış çiftlerini, DSD modlarını, güç
besleme/dekuplaj düzenini ve pin konfigürasyonunu tek düğümde toplamak. Saat
kaynağının üretimi ve köprüsü [[xmos-xu316-entegrasyon]] düğümündedir; bu dosya
**DAC çipinin kendisini** sahiplenir.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| AK4458 spesifikasyonu, DSD mod tablosu, pin tablosu | XMOS XU316 clock üretimi / firmware (→ [[xmos-xu316-entegrasyon]]) |
| XMOS → AK4458 TDM eşlemesi (TDMCLK/TDMFS/TDMD0-3) | Uçtan uca clock hiyerarşisi (→ [[../k006-dac-adc-zinciri/index]]) |
| Dekuplaj, I2C pull-up, çıkış filtresi değerleri | Class AB çıkış evresi (→ [[../k010-class-ab-cikis/index]]) |
| DSD/DoP format desteği | PCM3168A ADC tarafı (→ [[../k006-dac-adc-zinciri/index]]) |

## Arayüz

- **Dijital giriş:** `TDMCLK` (Pin 18) ← XMOS `SCK` · `TDMFS` (Pin 19) ← XMOS `WS` ·
  `TDMD0-3` (Pin 20-23) ← XMOS `SD0-SD3`.
- **Analog çıkış:** `OUTL1± … OUTR4±` (Pin 27-58) → Class AB amplifikatör girişi
  (toplam 8 çift differential).
- **Kontrol:** `SDA` (Pin 59) → XMOS `GPIO[2]` · `SCL` (Pin 60) → XMOS `GPIO[3]` ·
  `CSN` → GND (I2C modu).
- **Güç:** `AVDD = 5V` · `DVDD = 3.3V` · `CVDD = 1.2V` (kaynak L25).

## İçerik / Bileşenler

> **Aktarım kuralı:** aşağıdaki bloklar salt-okunur kaynaktan **verbatim** (birebir)
> aktarılmıştır; her bloğun üstünde gerçek disk kanıt aralığı verilir. Kaynakta olmayan
> hiçbir değer üretilmemiştir.

### 4.1 — Genel Bakış + Teknik Spesifikasyonlar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/ak4458-dac.md` - L10-L27

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

### 4.2 — DSD Modu Destekleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/ak4458-dac.md` - L29-L37

## DSD Modu Destekleri

| Mod | Örnekleme Hızı | Bit Derinliği |
|-----|----------------|---------------|
| DSD64 | 2.8224 MHz | 1-bit |
| DSD128 | 5.6448 MHz | 1-bit |
| DSD256 | 11.2896 MHz | 1-bit |
| DoP128 | 5.6448 MHz | 1-bit (PCM wrap) |
| DoP256 | 11.2896 MHz | 1-bit (PCM wrap) |

### 4.3 — Devre Tasarımı (Temel Bağlantılar + Bileşenler)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/ak4458-dac.md` - L39-L78

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

### 4.4 — Pin Konfigürasyonu (Önemli Pinler)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/ak4458-dac.md` - L80-L102

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

### 4.5 — Bağımlılıklar + Implementasyon Durumu

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/ak4458-dac.md` - L104-L123

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

## Kurallar

1. **TDM formatı sabittir:** DIF2:1:0 = 1:0:0 (kaynak L118); format değişikliği
   yalnız ADR ile yapılır.
2. **DAC slave'dir:** AK4458 saat üretmez, tüm clock XMOS'tan gelir (kaynak L119).
3. **I2C adresi 0x10** (CSN = LOW) — kaynak L121; ikinci bir AK4458 bu adrese konmaz.
4. **Her güç rayında dekuplaj zorunlu:** AVDD 100nF+10µF, DVDD 100nF, CVDD 1µF
   (kaynak L72–L75); çıplak güç pini ile PCB onayı verilmez.
5. **Kısıt:** kaynakta olmayan ölçüm/tolerans değeri `⚠️ VERIFICATION REQUIRED` ile
   işaretlenir (ZERO-HALLUCINATION).

## Bağımlılık Notu

| Ok | Tür | Kaynak |
|----|-----|--------|
| K007 → K006 | **çağrı** (I2S/TDM kaynağı) | bu dosya §4.3 (XMOS → TDM eşlemesi) |
| K007 → K010 | **gösterim** (Class AB girişi) | bu dosya §4.3 (OUTL/OUTR çiftleri) |
| K007 → K011 | **gösterim** (güç/termal) | bu dosya §4.5 (±5V/+3.3V/+1.2V, NTC) |

> Bu dosya yeni bağımlılık oku **eklemez** (şablon §4.4).

## İlgili Dosyalar

[[index.md]] · [[xmos-xu316-entegrasyon]] · [[../k006-dac-adc-zinciri/index]] · [[../k008-analog-giris/index]] · [[../k010-class-ab-cikis/index]]

---

**Aktarım Künyesi:**

| Kaynak (salt-okunur) | Toplam satır | Aktarılan aralık |
|---|---:|---|
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/ak4458-dac.md` | 123 | L10–L123 (§4.1–§4.5) |
