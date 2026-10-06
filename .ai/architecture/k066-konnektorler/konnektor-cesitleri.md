---
title: "Konnektör Çeşitleri - k066-konnektorler"
type: architecture-sublayer
category: architecture
version: 1.0.0
status: active
authority: "Vault (.ai/) SSOT - verbatim aktarım: _backup/arch-2026-10-06_1057/architecture/"
updated: 2026-10-06
---

# Konnektör Çeşitleri

> Klasör: `k066-konnektorler` · Dosya: `konnektor-cesitleri.md`
> Sorumlu persona: `audio-hardware-engineer` (CoreMusic Audio Hardware Engineer)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/` — aktarım bölüm bazında L aralığı ile kanıtlanmıştır.

## Genel Bakış

Harici ses ve veri konnektörlerinin türleri, pin konfigürasyonları ve teknik spesifikasyonları (XLR, RCA, USB-C, Optical/Toslink, HDMI ARC); ayrıca katman bileşen listesi, USB Audio Interface bloğu ve BOM maliyet kalemleri.

## Kapsam ve Sınırlar

- **Kapsam:** konnektör türleri, pin tanımları, teknik spesifikasyonlar, ilgili bileşen/BOM satırları.
- **Kapsam dışı:** panel düzeni ve etiketleme (→ [[baglanti-ve-etiketleme.md]]); güç kaynağı/koruma devreleri (→ [[../k068-guc-kaynagi-analog/index]]).
- **Bağlı olduğu klasör:** [[index.md]]
- **Çapraz referanslar:** [[../k067-koruma-devreleri/index]] · [[../k070-pcb-tasarim/index]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi ve içerik kaynaktan değiştirilmeden kopyalanmıştır; her bloğun üstündeki `> Aktarım:` satırı kaynak dosyanın disk satır aralığını gösterir.
### `_backup/arch-2026-10-06_1057/architecture/k1-donanim/konnektorler.md` (194 satır)

#### 1. konnektorler.md — giriş, teknik spesifikasyonlar ve XLR pin konfigürasyonu

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/konnektorler.md` - L1-L44

---
title: "Konnektörler - XLR, RCA, USB-C, Optical, HDMI ARC"
layer: K1
category: "Giriş/Çıkış Konnektörleri"
date: 2026-09-20
---

# Konnektörler - XLR, RCA, USB-C, Optical, HDMI ARC

## Genel Bakış

COREMUSIC konnektör sistemi, çeşitli ses ve kontrol arabirimleri için endüstri standartlarında bağlantı noktaları sağlar. Balanced XLR, unbalanced RCA, dijital USB-C, Optical (Toslink) ve HDMI ARC konnektörleri kullanılır.

## Teknik Spesifikasyonlar

| Konnektör | Tip | Empedans | Maks. Frekans | Uygulama |
|-----------|-----|----------|---------------|----------|
| XLR | 3-pin balanced | 110Ω | 10MHz | Analog giriş/çıkış |
| RCA | Phono unbalanced | 75Ω | 100MHz | Analog giriş/çıkış |
| USB-C | 16-pin | 90Ω | 480Mbps | Dijital giriş |
| Optical | Toslink | 75Ω | 125Mbps | Dijital giriş |
| HDMI ARC | 19-pin | 100Ω | 3.4Gbps | TV ses çıkışı |
| Binding Post | 4mm banana | - | - | Hoparlör çıkışı |

## XLR Konnektörü

### Pin Konfigürasyonu

```
    ┌─────────────────┐
    │    XLR Male     │
    │   (Panel Mount) │
    │                 │
    │     ┌───┐       │
    │     │ 1 │       │  Pin 1: Ground (Shield)
    │    /│   │\      │  Pin 2: Hot (+, Non-inverting)
    │   / │ 2 │ \     │  Pin 3: Cold (-, Inverting)
    │  /  │   │  \    │
    │ / 3 │   │   \   │
    │/    └───┘    \  │
    │               │ │
    └─────────────────┘
```


#### 2. konnektorler.md — RCA, USB-C, Optical (Toslink) ve HDMI ARC pin konfigürasyonları

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/konnektorler.md` - L60-L135


## RCA Konnektörü

### Pin Konfigürasyonu

```
    ┌─────────────────┐
    │    RCA Male     │
    │   (Panel Mount) │
    │                 │
    │     ┌───┐       │
    │     │ + │       │  Center Pin: Signal (+)
    │     └───┘       │  Outer Ring: Ground (Shield)
    │    ╱     ╲      │
    │   ╱       ╲     │
    │  ╱─────────╲    │
    │                 │
    └─────────────────┘
```

## USB-C Konnektörü

### Pin Konfigürasyonu

```
USB-C 16-Pin Connector
     │
     ├─ VBUS (A4, A9, B4, B9) ──▶ +5V (VBUS)
     ├─ GND (A1, A12, B1, B12) ──▶ DGND
     ├─ D+ (A6) ──▶ XU316 USB_DP
     ├─ D- (A7) ──▶ XU316 USB_DM
     ├─ CC1 (A5) ──▶ 5.1kΩ (Sink)
     ├─ CC2 (B5) ──▶ 5.1kΩ (Sink)
     ├─ SBU1 (A8) ──▶ NC (not used)
     └─ SBU2 (B8) ──▶ NC (not used)
```

## Optical (Toslink)

### Pin Konfigürasyonu

```
Toslink Connector
     │
     ├─ TX ──▶ LED Transmitter (TOS1103)
     ├─ RX ──▶ Photodiode Receiver
     └─ GND ──▶ Shield

Not: Optik izolasyon sağlar, ground loop engeller.
```

## HDMI ARC

### Pin Konfigürasyonu

```
HDMI ARC (Audio Return Channel)
     │
     ├─ TMDS Data0+ ──▶ LVDS Receiver
     ├─ TMDS Data0- ──▶ LVDS Receiver
     ├─ TMDS Data1+ ──▶ LVDS Receiver
     ├─ TMDS Data1- ──▶ LVDS Receiver
     ├─ TMDS Data2+ ──▶ LVDS Receiver
     ├─ TMDS Data2- ──▶ LVDS Receiver
     ├─ TMDS Clock+ ──▶ LVDS Receiver
     ├─ TMDS Clock- ──▶ LVDS Receiver
     ├─ CEC ──▶ CEC Controller (I2C)
     ├─ SCL ──▶ EDID I2C Clock
     ├─ SDA ──▶ EDID I2C Data
     ├─ +5V ──▶ Power
     ├─ HPD ──▶ Hot Plug Detect
     └─ GND ──▶ Shield

ARC Modu: TV'den ses sinyalini HDMI kablo üzerinden alır.
```


### `_backup/arch-2026-10-06_1057/architecture/k1-donanim/index.md` (96 satır)

#### 3. index.md — Bileşen Listesi

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/index.md` - L50-L64

## Bileşen Listesi

| # | Bileşen | Model | Adet | Kategori |
|---|---------|-------|------|----------|
| 1 | USB Audio Controller | XMOS XU316 | 1 | Dijital |
| 2 | ADC | PCM3168A | 1 | Dijital/Analog |
| 3 | DAC | AK4458 | 1 | Dijital/Analog |
| 4 | Class AB Amplifikatör | Özel Tasarım | 8 | Analog |
| 5 | Çıkış Transistörleri | MJL21194/93 | 16 | Güç |
| 6 | Hoparlör Sistemi | 8.1 Surround | 1 set | Çıkış |
| 7 | Güç Kaynağı | LM5122 Dual | 1 | Güç |
| 8 | Konnektörler | XLR/RCA/USB-C | çoklu | Giriş/Çıkış |
| 9 | PCB | 6 Katmanlı | 1 | Yapı |
| 10 | Soğutucu | Alüminyum Ekstrüzyon | 1 | Termal |


### `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` (805 satır)

#### 4. README.md — §2.1 USB Audio Interface

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` - L45-L53

### 2.1 USB Audio Interface

| Bileşen | Model | Özellik |
|---------|-------|---------|
| USB Audio | XMOS XU316 | USB Audio Class 2.0, 32-bit |
| USB Interface | USB-C | 24-pin, USB 2.0/3.0 |
| Clock | 22.5792 MHz | 44.1kHz family |
| Clock | 24.576 MHz | 48kHz family |


#### 5. README.md — §7 BOM Maliyet Analizi

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/README.md` - L239-L253

## 7. BOM Maliyet Analizi

| Kategori | Bileşen Sayısı | Toplam Maliyet |
|----------|---------------|---------------|
| USB Audio (XMOS) | 3 | $15.00 |
| DAC (PCM3168A) | 15 | $25.00 |
| Amplifikatör (8 kanal) | 120 | $85.00 |
| Güç Kaynağı | 35 | $45.00 |
| Pasif Bileşenler | 800+ | $180.00 |
| Konnektörler | 25 | $30.00 |
| PCB (6-layer) | 1 | $50.00 |
| **TOPLAM** | **~1,000** | **~$430** |

---

## İlgili Dosyalar

[[index.md]] · [[baglanti-ve-etiketleme.md]] · [[../k067-koruma-devreleri/index]] · [[../k070-pcb-tasarim/index]]
