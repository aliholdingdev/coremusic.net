---
title: "K006 — DAC→ADC Sinyal Zinciri Mimarisi"
type: architecture-sublayer
category: architecture
version: 1.0.0
status: active
authority: "SSOT — alt katman dokümanı (şablon: alt-katman-template)"
updated: 2026-10-06
---

# K006 — DAC→ADC Sinyal Zinciri Mimarisi

**K numarası:** K006 · **Klasör:** `k006-dac-adc-zinciri` · **Dosya:** `dac-adc-zinciri-mimari`
**Üst katman:** [[index.md]] (K006 klasör dizini) · **Alan:** A0
**Kanıt:** (i) diskte bu dosya · **Durum:** implemented (kaynak: salt-okunur yedek)
**Sorumlu persona:** `audio-hardware-engineer` · `dsp-firmware-engineer`

## Amaç

DAC → ADC sinyal zincirinin uçtan uca mimarisini belgelemek: dijital sinyalin AK4458
ile analoga dönüştürülmesinden, PCM3168A ile geri dijital alana dönüştürülmesine kadar
olan süreci; clock synchronization, impedance matching ve EMI filtering şartlarıyla
birlikte tek düğümde toplamak. Bu dosya **zincirin kendisini** (clock hiyerarşisi, hat
filtreleri, empedans eşleşmesi) sahiplenir; dönüştürücü çiplerinin kendi detayları
komşu düğümlere aittir.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| DAC→ADC uçtan uca akış, clock hiyerarşisi (master/slave) | AK4458 çip içi pin/register detayı (→ [[../k007-ak4458-xmos/index]]) |
| I2S hat filtreleme (ferrite bead + dekuplaj) | PCM3168A pin konfigürasyonu ve I2C gain (→ ikinci konu dosyası) |
| Empedans eşleşme tablosu, bileşen listesi | Analog giriş/yol evreleri (→ [[../k008-analog-giris/index]]) |
| Clock tolerance / jitter şartları | Kararlılık ve geri besleme (→ [[../k009-vas-feedback/index]]) |

## Arayüz

- **Yukarı (üst zincir):** `dsp-firmware-engineer` sahipliğindeki DSP Engine — PCM3168A
  dijital çıkışı (DOUTA/DOUTB) I2S üzerinden DSP'ye gider.
- **Aşağı (alt bileşen):** XMOS XU316 saat kaynağı — MCLK / SCK / WS / SD[0:3]
  üretimini yapar; bu dosya yalnız **alıcı** rolünü belgeler (kaynak: L60–L99).
- **Yan:** AK4458 analog çıkışı → Class AB zinciri; PCM3168A analog girişi → XLR/RCA.
- **Arayüz sinyalleri (kaynakta geçen adlar):** `SCKI`, `TDMCLK`, `TDMFS`, `TDMD0-3`,
  `BCK`, `LRCK`, `DOUTA/B`, `MCLK`.

## İçerik / Bileşenler

> **Aktarım kuralı:** aşağıdaki bloklar salt-okunur kaynaktan **verbatim** (birebir)
> aktarılmıştır; her bloğun üstünde gerçek disk kanıt aralığı verilir. Kaynakta olmayan
  hiçbir değer üretilmemiştir.

### 4.1 — Genel Bakış + Teknik Spesifikasyonlar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/dac-adc-zinciri.md` - L10-L25

## Genel Bakış

DAC → ADC sinyal zinciri, COREMUSIC'da dijital sinyalin analog forma dönüştürülmesinden sonra tekrar dijital alana dönüştürülmesine kadar olan süreci tanımlar. Clock synchronization, impedance matching ve EMI filtering bu zincirde kritik öneme sahiptir.

## Teknik Spesifikasyonlar

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

### 4.2 — Sinyal Yolu Diyagramı

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/dac-adc-zinciri.md` - L27-L54

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

### 4.3 — Clock Synchronization (Master/Slave + Clock Accuracy)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/dac-adc-zinciri.md` - L56-L107

## Clock Synchronization

### Master/Slave Konfigürasyonu

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
┌─────────────────────────────────────────────────────────────────┐
│                                                                 │
│  XMOS XU316 (Master)                                           │
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
│                                                                 │
│  PCM3168A (Slave)                                              │
│  ├─ SCKI ◀──────────────────────────────────────────────┘
│  ├─ BCK ◀───────────────────────────────────────────────┘
│  ├─ LRCK ◀──────────────────────────────────────────────┘
│  └─ DOUTA/B ───────────────────────────────────────────▶ XMOS
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### Clock Accuracy

| Clock | Frequency | Tolerance | Jitter |
|-------|-----------|-----------|--------|
| MCLK | 22.5792MHz | ±50ppm | < 100ps RMS |
| SCK | 1.4112MHz | ±50ppm | < 100ps RMS |
| WS | 44.1kHz | ±50ppm | < 100ps RMS |

### 4.4 — EMI Filtreleme (I2S Hat Filtresi)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/dac-adc-zinciri.md` - L109-L121

## EMI Filtreleme

### I2S Hat Filtresi

```
XMOS Output ──▶ Ferrite Bead (600Ω @ 100MHz) ──▶ 100nF ──▶ DAC/ADC

Her I2S hattı için:
- Ferrite bead: BLM18AG601SN1 (600Ω, 0603)
- Decoupling: 100nF MLCC (0402)
- Trace length: < 50mm
- Impedans: 90Ω differential
```

### 4.5 — Empedans Eşleşme + Bileşen Değerleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/dac-adc-zinciri.md` - L123-L140

## Empedans Eşleşme

| Junction | Source Z | Load Z | Matched? |
|----------|----------|--------|----------|
| XMOS → DAC | 50Ω | 100Ω | No (high-Z input) |
| DAC → ADC | 25Ω | 10kΩ | No (voltage mode) |
| ADC → DSP | 100Ω | 50Ω | Yes (differential) |

## Bileşen Değerleri

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Ferrite Bead | BLM18AG601SN1 | 16 | I2S hat filtresi |
| 2 | Decoupling Cap | 100nF MLCC | 16 | I2S dekuplajı |
| 3 | Crystal | 22.5792MHz | 1 | 44.1kHz family |
| 4 | Crystal | 24.576MHz | 1 | 48kHz family |
| 5 | Load Cap | 18pF C0G | 4 | Crystal load |
| 6 | Termination | 100Ω | 8 | I2S termination |

### 4.6 — Bağımlılıklar + Implementasyon Durumu

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/dac-adc-zinciri.md` - L142-L159

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 XMOS | Clock | Master clock source |
| K1 DAC | Çıkış | AK4458 analog output |
| K1 ADC | Giriş | PCM3168A digital output |
| K3 DSP | Üst | Dijital sinyal işleme |

## Durum: Implementasyon

**Durum**: 🟡 Simülasyon Aşamasında

- Clock synchronization: LTSpice ile simulate edildi
- I2S timing: Eye diagram analizi yapıldı
- EMI: Pre-compliance test ile ferrite bead seçimi doğrulandı
- Crystal: Dual crystal (22.5792MHz + 24.576MHz) seçildi
- PCB routing: I2S traces length-matched (±1mm tolerance)

## Kurallar

1. **Tek saat kaynağı:** MCLK/SCK/WS üretimi XMOS XU316'dadır; AK4458 ve PCM3168A
   **slave**'dir (bu dosya §4.3 kaynak zinciri: `dac-adc-zinciri.md` L63
   "Primary Clock Source: XMOS XU316 (Master)" · L86 "AK4458 (Slave)" ·
   L92 "PCM3168A (Slave)"). İkinci bir saat kaynağı eklenmez.
2. **EMI filtresi zorunlu:** her I2S hattı ferrite bead (600Ω @ 100MHz) + 100nF dekuplaj
   olmadan PCB'den geçirilmez (kaynak L114–L121).
3. **Veri bütünlüğü:** kaynakta olmayan voltaj/akım/part number yazılmaz; eksik değer
   `⚠️ VERIFICATION REQUIRED` işaretlenir (ZERO-HALLUCINATION).
4. **Simülasyon etiketi:** bu zincir kaynakta "🟡 Simülasyon Aşamasında" olduğu için
   hiçbir satır `implemented` kabul edilmez; ölçüm gerektiren iddialar simülasyon
   kanıtına bağlanır.

## Bağımlılık Notu

| Ok | Tür | Kaynak |
|----|-----|--------|
| K006 → K007 | **çağrı** (AK4458/XMOS tarafı) | bu dosya §4.3 (clock distribution) — bağımlılık satırı eKLENMEZ, yalnız gösterim |
| K006 → K008 | **gösterim** (analog giriş yolu) | bu dosya §4.5 (DAC → ADC junction) |
| K006 → K012 | **gösterim** (dijital arayüz) | komşu katman — matristeki mevcut ok |

> Bu dosya yeni bağımlılık oku **eklemez**; yalnız mevcut README/matris oklarını
> "çağrı/gösterim" etiketiyle anar (şablon §4.4).

## İlgili Dosyalar

[[index.md]] · [[../k007-ak4458-xmos/index]] · [[../k008-analog-giris/index]] · [[../k009-vas-feedback/index]]

---

**Aktarım Künyesi:**

| Kaynak (salt-okunur) | Toplam satır | Aktarılan aralık |
|---|---:|---|
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/dac-adc-zinciri.md` | 159 | L10–L159 (§4.1–§4.6) |
