---
title: "K007 — XMOS XU316 Entegrasyonu"
type: architecture-sublayer
category: architecture
version: 1.0.0
status: active
authority: "SSOT — alt katman dokümanı (şablon: alt-katman-template)"
updated: 2026-10-06
---

# K007 — XMOS XU316 Entegrasyonu

**K numarası:** K007 · **Klasör:** `k007-ak4458-xmos` · **Dosya:** `xmos-xu316-entegrasyon`
**Üst katman:** [[index.md]] (K007 klasör dizini) · **Alan:** A0
**Kanıt:** (i) diskte bu dosya · **Durum:** implemented (kaynak: salt-okunur yedek)
**Sorumlu persona:** `dsp-firmware-engineer` · `audio-hardware-engineer`

## Amaç

XMOS XU316 USB audio controller'ının sistem entegrasyonunu belgelemek: USB-C
bağlantısı, I2S çıkış eşlemesi, kristal saat seçimi, dekuplaj düzeni, pin
konfigürasyonu ve firmware/güç bağımlılıklarını tek düğümde toplamak. Bu düğüm
**köprünün kendi tarafını** (host ↔ I2S) sahiplenir; AK4458 tarafı
[[ak4458-dac]] düğümündedir.

| Kapsam | Kapsam Dışı |
|--------|-------------|
| XU316 USB-C / I2S / kristal bağlantıları | AK4458 çip detayı, TDM pin eşlemesi (→ [[ak4458-dac]]) |
| Pin tablosu, dekuplaj kondansatörleri | DAC→ADC uçtan uca clock hiyerarşisi (→ [[../k006-dac-adc-zinciri/index]]) |
| UAC2 / firmware bağımlılıkları | PCM3168A ADC pin/I2C düzeni (→ [[../k006-dac-adc-zinciri/index]]) |
| ESD koruma ve güç yolu (VBUS → LDO) | USB sürücü katmanı (→ [[../k034-usb-audio/index]]) |

## Arayüz

- **Host yönü:** USB-C `D+/D-` → `USB_DP` (Pin 12) / `USB_DM` (Pin 13) ·
  `VBUS (5V)` → 3.3V LDO → `VCC` (Pin 44) · `GND` → DGND Plane.
- **Cihaz yönü (I2S):** `SCK` → PCM3168A `BCK` · `WS` → `LRCK` · `SD0/SD1` → `DIN/DIN(B)` ·
  `MCLK` → `SCKI`.
- **Saat:** `XTAL_IN` (Pin 8) / `XTAL_OUT` (Pin 9) → 22.5792MHz kristal.
- **Kontrol/yan:** `GPIO[0:7]` (Pin 14-21), `SPI` (Pin 28-31), `I2C` (Pin 32-35),
  `LED[0:3]` (Pin 36-39), `JTAG` (Pin 40-43).

## İçerik / Bileşenler

> **Aktarım kuralı:** aşağıdaki bloklar salt-okunur kaynaktan **verbatim** (birebir)
> aktarılmıştır; her bloğun üstünde gerçek disk kanıt aralığı verilir. Kaynakta olmayan
> hiçbir değer üretilmemiştir.

### 4.1 — Genel Bakış + Teknik Spesifikasyonlar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/xmos-xu316.md` - L10-L27

## Genel Bakış

XMOS XU316, COREMUSIC'ın USB arabirimi için kullanılan 16 çekirdekli bir xtENDIO mikrodenetleyicisidir. USB Audio Class 2.0 standardını destekler ve yüksek çözünürlüklü ses (hi-res audio) için gerekli tüm timer hassasiyetini sağlar. I2S arayüzü üzerinden DAC'a doğrudan bağlantı kurar.

## Teknik Spesifikasyonlar

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

### 4.2 — Devre Tasarımı (Temel Bağlantılar + Kondansatörler)

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/xmos-xu316.md` - L29-L62

## Devre Tasarımı

### Temel Bağlantılar

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

### Kondansatör Değerleri

| Referans | Değer | Açıklama |
|----------|-------|----------|
| C1-C4 | 100nF MLCC | USB hat filtreleme |
| C5-C8 | 4.7µF MLCC | VCC dekuplajı |
| C9-C10 | 18pF | Crystal yük kondansatörü |
| C11-C14 | 10nF | I2S hat dekuplajı |

### 4.3 — Pin Konfigürasyonu

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/xmos-xu316.md` - L64-L81

## Pin Konfigürasyonu

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

### 4.4 — Bağımlılıklar + Implementasyon Durumu

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/xmos-xu316.md` - L83-L99

## Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | QFN-56 pad layout |
| K2 OS/Sürücüler | Üst | USB Audio driver (UAC2) |
| K3 XMOS Firmware | Üst | xCORE-200 firmware image |
| K5 Analog | Uzay | I2S → DAC bağlantısı |

## Durum: Implementasyon

**Durum**: 🟢 Hazır

- Firmware open-source: `lib_xua` kütüphanesi mevcut
- Crystal seçimi: 22.5792MHz (44.1kHz ailesi) + 24.576MHz (48kHz ailesi) dual
- USB-C connector: USB Type-C 16-pin SMD
- ESD koruması: USBLC6-2SC6 TVS diyotları

## Kurallar

1. **Tek saat üreteci:** MCLK/SCK/WS üretimi XU316'dadır; DAC ve ADC slave'dir
   (kaynak L47 "MCLK (Master)" · L97 dual crystal seçimi). İkinci osilatör eklenmez.
2. **ESD koruması zorunlu:** USB veri hatlarında USBLC6-2SC6 TVS (kaynak L99)
   olmadan PCB onayı verilmez.
3. **Güç yolu:** `VBUS (5V)` asla doğrudan VCC'ye bağlanmaz; 3.3V LDO üzerinden
   (kaynak L38) beslenir.
4. **Firmware sınırı:** `lib_xua` / xCORE-200 image referansı kaynakta verilmiştir
   (kaynak L96); sürüm numarası kaynakta YOK → `⚠️ VERIFICATION REQUIRED`.
5. **Kısıt:** kaynakta olmayan ölçüm değeri `⚠️ VERIFICATION REQUIRED` ile işaretlenir
   (ZERO-HALLUCINATION).

## Bağımlılık Notu

| Ok | Tür | Kaynak |
|----|-----|--------|
| K007 (XMOS) → K006 | **çağrı** (PCM3168A I2S) | bu dosya §4.2 (SCK/WS/SD/MCLK eşlemesi) |
| K007 (XMOS) → K007 (AK4458) | **gösterim** (TDM beslemesi) | bu dosya §4.2 — ayrıntı [[ak4458-dac]] |
| K007 → K034 | **gösterim** (USB Audio) | bu dosya §4.4 (UAC2 sürücü bağımlılığı) |

> Bu dosya yeni bağımlılık oku **eklemez** (şablon §4.4).

## İlgili Dosyalar

[[index.md]] · [[ak4458-dac]] · [[../k006-dac-adc-zinciri/index]] · [[../k034-usb-audio/index]]

---

**Aktarım Künyesi:**

| Kaynak (salt-okunur) | Toplam satır | Aktarılan aralık |
|---|---:|---|
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/xmos-xu316.md` | 99 | L10–L99 (§4.1–§4.4) |
