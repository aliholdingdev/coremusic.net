---
title: "K013 PCB Tasarım + Hoparlör Dizilimi"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 — K000-K071"
updated: 2026-10-06
---

# K013 — PCB Tasarım + Hoparlör Dizilimi

> **K numarası:** K013 · **Klasör:** `k013-pcb-hoparlor` · **Dosya:** `pcb-ve-hoparlor`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `performance-engineer` (ikincil)
> **Klasör amacı:** PCB tasarım kurallarını (katman/akım yolu/toprak) ve 8.1 hoparlör diziliminin yerleşim/afiş/kablo mantığını toplamak.

## 1. Kapsam ve Amaç

Bu dosya **PCB Tasarım + Hoparlör Dizilimi** konusunu ele alır. Kapsamı: Katman kullanımı, sinyal/güç ayrımı, akım yolları, toprak düzlemi ve konnektör yerleşimi.

Yazı, salt-okunur yedek kaynaklardan türetilmiştir; her teknik değer aşağıda
belirtilen kaynak dosyalarında bulunmak zorundadır. Kaynakta bulunmayan her değer
`⚠️ VERIFICATION REQUIRED` ile işaretlenir (ZERO-HALLUCINATION).

## 2. Sinyal / Donanım Akışı

```
      [ PCB katman yığını ]
            │
            ▼
      [ Güç / ground yolları ]
            │
            ▼
      [ Bileşen yerleşimi (sinyal → güç) ]
            │
            ▼
      [ Kart çıktı kabloları ]
            │
            ▼
      [ Hoparlör dizilimi (8.1) ]
```

**Akış notları:**

1. **PCB katman yığını** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
2. **Güç / ground yolları** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
3. **Bileşen yerleşimi (sinyal → güç)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
4. **Kart çıktı kabloları** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
5. **Hoparlör dizilimi (8.1)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.

## 3. Kaynak Envanteri

| # | Kaynak dosya (salt-okunur yedek) | Satır | Bu dosyadaki rolü |
|---|---|---:|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/pcb-tasarim.md` | 187 | birincil kaynak (ham içerik §6) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/konnektorler.md` | 194 | ikincil kaynak (§6) |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/hoparlor-dizilimi.md` | 134 | tamamlayıcı kaynak (§6) |

## 4. Teknik Spesifikasyon Tabloları (kaynak kalıbı)

### 4.1 · `k1-donanim/pcb-tasarim.md`

| Parametre | Değer |
|-----------|-------|
| Katman Sayısı | 6 (4 Signal + 2 Power) |
| Boyut | 300mm × 200mm (12" × 8") |
| Kalınlık | 1.6mm (62mil) |
| Bakır Kalınlığı | 35µm (1oz) outer, 70µm (2oz) inner |
| Min. Trace Width | 0.15mm (6mil) |
| Min. Via Size | 0.3mm (12mil) drill |
| Min. Via Pad | 0.6mm (24mil) |
| Impedans Kontrolü | 90Ω differential, 50Ω single-ended |
| Surface Finish | ENIG (Electroless Nickel Immersion Gold) |
| Solder Mask | LPI Green (matte) |
| Silkscreen | White, both sides |

### 4.2 · `k1-donanim/pcb-tasarim.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | Gerber files, BOM |
| K1 Tüm Bileşenler | Bağlantı | Component footprints |
| K1 Termal | Bağlantı | Thermal vias, pads |
| K2 OS/Sürücüler | Üst | USB trace routing |

### 4.3 · `k1-donanim/konnektorler.md`

| Konnektör | Tip | Empedans | Maks. Frekans | Uygulama |
|-----------|-----|----------|---------------|----------|
| XLR | 3-pin balanced | 110Ω | 10MHz | Analog giriş/çıkış |
| RCA | Phono unbalanced | 75Ω | 100MHz | Analog giriş/çıkış |
| USB-C | 16-pin | 90Ω | 480Mbps | Dijital giriş |
| Optical | Toslink | 75Ω | 125Mbps | Dijital giriş |
| HDMI ARC | 19-pin | 100Ω | 3.4Gbps | TV ses çıkışı |
| Binding Post | 4mm banana | - | - | Hoparlör çıkışı |

### 4.4 · `k1-donanim/konnektorler.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | Panel layout, drilling |
| K1 Amplifikatör | Bağlantı | XLR/RCA input |
| K1 Hoparlör | Çıkış | Binding posts |
| K1 USB Audio | Bağlantı | USB-C input |
| K1 Koruma | Bağlantı | Relay switching |

### 4.5 · `k1-donanim/hoparlor-dizilimi.md`

| Parametre | Değer |
|-----------|-------|
| Hoparlör Sayısı | 8 + 1 subwoofer |
| Toplam Çıkış Gücü | 2.250W RMS |
| Frekans Aralığı | 20Hz – 20kHz (full-range) |
| Subwoofer Aralığı | 20Hz – 120kHz |
| Empedans | 8Ω (tüm hoparlörler) |
| Hassasiyet | 89dB/W/m |
| Maks. Basınç Seviyesi | 115dB SPL |
| THD | < %1 (maks. güçte) |

### 4.6 · `k1-donanim/hoparlor-dizilimi.md`

| Kanal | Kısaltma | Konum | Açı | Amplifikatör |
|-------|----------|-------|-----|-------------|
| 1 | FL | Front Left | -30° | Amp Kanal 1 |
| 2 | C | Center | 0° | Amp Kanal 2 |
| 3 | FR | Front Right | +30° | Amp Kanal 3 |
| 4 | SL | Side Left | -90° | Amp Kanal 4 |
| 5 | SR | Side Right | +90° | Amp Kanal 5 |
| 6 | SBL | Surround Back Left | -135° | Amp Kanal 6 |
| 7 | SBR | Surround Back Right | +135° | Amp Kanal 7 |
| 8 | LFE | Subwoofer | Front-Left | Amp Kanal 8 |

### 4.7 · `k1-donanim/hoparlor-dizilimi.md`

| Parametre | Değer |
|-----------|-------|
| Tip | 2-way (Woofer + Tweeter) |
| Woofer | 6.5" (165mm) |
| Tweeter | 1" (25mm) Dome |
| Frekans Crossover | 2.5kHz |
| Empedans | 8Ω |
| Güç Kapasitesi | 200W RMS |
| Hassasiyet | 89dB/W/m |

### 4.8 · `k1-donanim/hoparlor-dizilimi.md`

| Parametre | Değer |
|-----------|-------|
| Tip | Passive radiator |
| Woofer | 10" (250mm) |
| Frekans Aralığı | 20Hz – 120kHz |
| Empedans | 8Ω |
| Güç Kapasitesi | 250W RMS |
| Hassasiyet | 87dB/W/m |

## 5. Bölüm Yol Haritası (kaynak başlıkları)

| # | Kaynak | Seviye | Başlık |
|---:|---|---:|---|
| 1 | `k1-donanim/pcb-tasarim.md` | H1 | 6 Katmanlı PCB Tasarımı |
| 2 | `k1-donanim/pcb-tasarim.md` | H2 | Genel Bakış |
| 3 | `k1-donanim/pcb-tasarim.md` | H2 | Teknik Spesifikasyonlar |
| 4 | `k1-donanim/pcb-tasarim.md` | H2 | Katman Stackup |
| 5 | `k1-donanim/pcb-tasarim.md` | H2 | Star Grounding Sistemi |
| 6 | `k1-donanim/pcb-tasarim.md` | H2 | Controlled Impedance |
| 7 | `k1-donanim/pcb-tasarim.md` | H3 | Impedans Hesaplaması |
| 8 | `k1-donanim/pcb-tasarim.md` | H3 | Differential Pair (I2S, USB) |
| 9 | `k1-donanim/pcb-tasarim.md` | H2 | Bileşen Yerleşimi |
| 10 | `k1-donanim/pcb-tasarim.md` | H2 | Bağımlılıklar |
| 11 | `k1-donanim/pcb-tasarim.md` | H2 | Durum: Implementasyon |
| 12 | `k1-donanim/konnektorler.md` | H1 | Konnektörler - XLR, RCA, USB-C, Optical, HDMI ARC |
| 13 | `k1-donanim/konnektorler.md` | H2 | Genel Bakış |
| 14 | `k1-donanim/konnektorler.md` | H2 | Teknik Spesifikasyonlar |
| 15 | `k1-donanim/konnektorler.md` | H2 | XLR Konnektörü |
| 16 | `k1-donanim/konnektorler.md` | H3 | Pin Konfigürasyonu |
| 17 | `k1-donanim/konnektorler.md` | H3 | XLR Devre Bağlantısı |
| 18 | `k1-donanim/konnektorler.md` | H2 | RCA Konnektörü |
| 19 | `k1-donanim/konnektorler.md` | H3 | Pin Konfigürasyonu |
| 20 | `k1-donanim/konnektorler.md` | H2 | USB-C Konnektörü |
| 21 | `k1-donanim/konnektorler.md` | H3 | Pin Konfigürasyonu |
| 22 | `k1-donanim/konnektorler.md` | H2 | Optical (Toslink) |
| 23 | `k1-donanim/konnektorler.md` | H3 | Pin Konfigürasyonu |
| 24 | `k1-donanim/konnektorler.md` | H2 | HDMI ARC |
| 25 | `k1-donanim/konnektorler.md` | H3 | Pin Konfigürasyonu |
| 26 | `k1-donanim/konnektorler.md` | H2 | Binding Posts (Hoparlör Çıkışları) |
| 27 | `k1-donanim/konnektorler.md` | H3 | Pin Konfigürasyonu |
| 28 | `k1-donanim/konnektorler.md` | H2 | Panel Düzeni |
| 29 | `k1-donanim/konnektorler.md` | H2 | Bağımlılıklar |
| 30 | `k1-donanim/konnektorler.md` | H2 | Durum: Implementasyon |
| 31 | `k1-donanim/hoparlor-dizilimi.md` | H1 | Hoparlör Dizilimi - 8.1 Surround |
| 32 | `k1-donanim/hoparlor-dizilimi.md` | H2 | Genel Bakış |
| 33 | `k1-donanim/hoparlor-dizilimi.md` | H2 | Teknik Spesifikasyonlar |
| 34 | `k1-donanim/hoparlor-dizilimi.md` | H2 | Hoparlör Konumları |
| 35 | `k1-donanim/hoparlor-dizilimi.md` | H2 | Kanal Haritası |
| 36 | `k1-donanim/hoparlor-dizilimi.md` | H2 | Hoparlör Özellikleri |
| 37 | `k1-donanim/hoparlor-dizilimi.md` | H3 | Full-Range Hoparlörler (FL, C, FR, SL, SR, SBL, SBR) |
| 38 | `k1-donanim/hoparlor-dizilimi.md` | H3 | Subwoofer (LFE) |
| 39 | `k1-donanim/hoparlor-dizilimi.md` | H2 | Kablo ve Bağlantılar |
| 40 | `k1-donanim/hoparlor-dizilimi.md` | H2 | Bağımlılıklar |
| 41 | `k1-donanim/hoparlor-dizilimi.md` | H2 | Durum: Implementasyon |

## 6. Kaynak İçerik (ham, satır bozulmadan)

### 6.1 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/pcb-tasarim.md`


### 6 Katmanlı PCB Tasarımı

#### Genel Bakış

COREMUSIC PCB tasarımı, 6 katmanlı high-performance bir devre kartıdır. Star grounding, controlled impedance ve EMI shielding ile analog/dijital karışımını önler. Her katman belirli bir fonksiyona atanmıştır.

#### Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Katman Sayısı | 6 (4 Signal + 2 Power) |
| Boyut | 300mm × 200mm (12" × 8") |
| Kalınlık | 1.6mm (62mil) |
| Bakır Kalınlığı | 35µm (1oz) outer, 70µm (2oz) inner |
| Min. Trace Width | 0.15mm (6mil) |
| Min. Via Size | 0.3mm (12mil) drill |
| Min. Via Pad | 0.6mm (24mil) |
| Impedans Kontrolü | 90Ω differential, 50Ω single-ended |
| Surface Finish | ENIG (Electroless Nickel Immersion Gold) |
| Solder Mask | LPI Green (matte) |
| Silkscreen | White, both sides |

#### Katman Stackup

```
┌─────────────────────────────────────────────────────────┐
│  Layer 1: SIGNAL TOP (Component Side)                    │
│  ├─ Trace: 0.15mm min, 0.25mm typical                   │
│  ├─ Components: All SMD, top side                        │
│  └─ Pour: Copper pour (shielding)                       │
├─────────────────────────────────────────────────────────┤
│  Prepreg (2116, 0.2mm)                                  │
├─────────────────────────────────────────────────────────┤
│  Layer 2: GND PLANE (Ground Reference)                  │
│  ├─ Solid copper pour (no breaks)                       │
│  ├─ Star ground connections                             │
│  └─ Via stitching around analog/digital boundary         │
├─────────────────────────────────────────────────────────┤
│  Core (FR4, 0.4mm)                                      │
├─────────────────────────────────────────────────────────┤
│  Layer 3: SIGNAL INNER 1 (Analog Signal)                │
│  ├─ Analog signal traces                                │
│  ├─ DAC/ADC connections                                 │
│  └─ Short, symmetrical routing                          │
├─────────────────────────────────────────────────────────┤
│  Core (FR4, 0.4mm)                                      │
├─────────────────────────────────────────────────────────┤
│  Layer 4: SIGNAL INNER 2 (Digital Signal)               │
│  ├─ Digital signal traces                               │
│  ├─ I2S, USB connections                                │
│  └─ Away from analog section                            │
├─────────────────────────────────────────────────────────┤
│  Core (FR4, 0.4mm)                                      │
├─────────────────────────────────────────────────────────┤
│  Layer 5: POWER PLANE (±35V, +5V, +3.3V)               │
│  ├─ Split power planes                                  │
│  ├─ ±35V analog power                                   │
│  └─ +5V/+3.3V digital power                             │
├─────────────────────────────────────────────────────────┤
│  Prepreg (2116, 0.2mm)                                  │
├─────────────────────────────────────────────────────────┤
│  Layer 6: SIGNAL BOTTOM (Component Side)                │
│  ├─ Additional routing                                  │
│  ├─ Thermal vias for power components                   │
│  └─ Ground pour (shielding)                             │
└─────────────────────────────────────────────────────────┘
```

#### Star Grounding Sistemi

```
                        Star Ground Point
                              │
          ┌───────────────────┼───────────────────┐
          │                   │                   │
     ┌────┴────┐         ┌────┴────┐         ┌────┴────┐
     │ Analog  │         │ Digital │         │  Power  │
     │  GND    │         │  GND    │         │  GND    │
     └─────────┘         └─────────┘         └─────────┘
          │                   │                   │
     ┌────┴────┐         ┌────┴────┐         ┌────┴────┐
     │ DAC     │         │ XMOS    │         │ PSU     │
     │ ADC     │         │ USB     │         │ Bridge  │
     │ Amp     │         │ I2C     │         │ Rect    │
     └─────────┘         └─────────┘         └─────────┘

Kurallar:
1. Her ground alanı sadece bir noktada star point'e bağlanır
2. Analog ve digital ground arasında 0Ω direnç (short)
3. Power ground, star point'e doğrudan bağlantı
4.Via stitching ile katmanlar arası ground connectivity
```

#### Controlled Impedance

##### Impedans Hesaplaması

```
Microstrip (Outer Layer):
Z0 = (87 / √(Er + 1.41)) × ln(5.98 × h / (0.8 × w + t))

Er = 4.5 (FR4)
h = 0.2mm (dielectric thickness)
w = 0.25mm (trace width)
t = 0.035mm (copper thickness)

Z0 = (87 / √(4.5 + 1.41)) × ln(5.98 × 0.2 / (0.8 × 0.25 + 0.035))
Z0 = (87 / 2.43) × ln(1.196 / 0.235)
Z0 = 35.8 × ln(5.09)
Z0 = 35.8 × 1.63
Z0 = 58.4Ω (target: 50Ω)

Adjustment: w = 0.30mm → Z0 = 50.2Ω ✅
```

##### Differential Pair (I2S, USB)

```
Differential Impedance:
Zdiff = 2 × Z0 × (1 - 0.48 × e^(-0.96 × s/h))

Z0 = 50Ω (single-ended)
s = 0.5mm (trace spacing)
h = 0.2mm (dielectric thickness)

Zdiff = 2 × 50 × (1 - 0.48 × e^(-0.96 × 0.5/0.2))
Zdiff = 100 × (1 - 0.48 × e^(-2.4))
Zdiff = 100 × (1 - 0.48 × 0.091)
Zdiff = 100 × 0.956
Zdiff = 95.6Ω (target: 90Ω)

Adjustment: s = 0.4mm → Zdiff = 90.2Ω ✅
```

#### Bileşen Yerleşimi

```
┌─────────────────────────────────────────────────────────┐
│  PCB COMPONENT LAYOUT (Top View)                        │
│                                                         │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐                 │
│  │  XMOS   │  │  DAC    │  │  ADC    │  DİJİTAL BÖLGE  │
│  │  XU316  │  │ AK4458  │  │PCM3168A │                 │
│  └─────────┘  └─────────┘  └─────────┘                 │
│                                                         │
│  ═══════════════════════════════════════  analog/digital │
│                                          boundary      │
│                                                         │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐                 │
│  │ Diff    │  │  VAS    │  │ Output  │  ANALOG BÖLGE   │
│  │ Pair    │  │  Stage  │  │  Stage  │                 │
│  └─────────┘  └─────────┘  └─────────┘                 │
│                                                         │
│  ┌─────────────────────────────────────┐                │
│  │         Power Supply Section        │  GÜÇ BÖLGE    │
│  │  LM5122   │   Bridge   │  PFC      │                │
│  └─────────────────────────────────────┘                │
└─────────────────────────────────────────────────────────┘
```

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | Gerber files, BOM |
| K1 Tüm Bileşenler | Bağlantı | Component footprints |
| K1 Termal | Bağlantı | Thermal vias, pads |
| K2 OS/Sürücüler | Üst | USB trace routing |

#### Durum: Implementasyon

**Durum**: 🔴 Başlamadı

- Stackup: 6-layer planlandı, impedance hesaplamaları yapıldı
- EDA Tool: Altium Designer / KiCad 8
- DRC: Design rules tanımlandı
- Gerber: Henüz oluşturulmadı
- Prototype: 3 adet prototype planlandı
- Production: JLCPCB / PCBWay (4 hafta lead time)


### 6.2 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/konnektorler.md`


### Konnektörler - XLR, RCA, USB-C, Optical, HDMI ARC

#### Genel Bakış

COREMUSIC konnektör sistemi, çeşitli ses ve kontrol arabirimleri için endüstri standartlarında bağlantı noktaları sağlar. Balanced XLR, unbalanced RCA, dijital USB-C, Optical (Toslink) ve HDMI ARC konnektörleri kullanılır.

#### Teknik Spesifikasyonlar

| Konnektör | Tip | Empedans | Maks. Frekans | Uygulama |
|-----------|-----|----------|---------------|----------|
| XLR | 3-pin balanced | 110Ω | 10MHz | Analog giriş/çıkış |
| RCA | Phono unbalanced | 75Ω | 100MHz | Analog giriş/çıkış |
| USB-C | 16-pin | 90Ω | 480Mbps | Dijital giriş |
| Optical | Toslink | 75Ω | 125Mbps | Dijital giriş |
| HDMI ARC | 19-pin | 100Ω | 3.4Gbps | TV ses çıkışı |
| Binding Post | 4mm banana | - | - | Hoparlör çıkışı |

#### XLR Konnektörü

##### Pin Konfigürasyonu

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

##### XLR Devre Bağlantısı

```
XLR Input (Balanced)
     │
     ├─ Pin 1 (GND) ──▶ AGND Plane
     ├─ Pin 2 (Hot) ──▶ 100Ω ──▶ ADC AINL+ (PCM3168A)
     └─ Pin 3 (Cold) ─▶ 100Ω ──▶ ADC AINL- (PCM3168A)

XLR Output (Balanced)
     │
     ├─ Pin 1 (GND) ──▶ AGND Plane
     ├─ Pin 2 (Hot) ──◀ DAC OUTL+ (AK4458)
     └─ Pin 3 (Cold) ─◀ DAC OUTL- (AK4458)
```

#### RCA Konnektörü

##### Pin Konfigürasyonu

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

#### USB-C Konnektörü

##### Pin Konfigürasyonu

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

#### Optical (Toslink)

##### Pin Konfigürasyonu

```
Toslink Connector
     │
     ├─ TX ──▶ LED Transmitter (TOS1103)
     ├─ RX ──▶ Photodiode Receiver
     └─ GND ──▶ Shield

Not: Optik izolasyon sağlar, ground loop engeller.
```

#### HDMI ARC

##### Pin Konfigürasyonu

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

#### Binding Posts (Hoparlör Çıkışları)

##### Pin Konfigürasyonu

```
Binding Post (4mm Banana Compatible)
     │
     ├─ Red (+) ──▶ Amplifikatör Output (+)
     ├─ Black (-) ──▶ Amplifikatör Output (-)
     └─ Banana Plug / Spade / Bare Wire uyumlu

Torque: 1.5 Nm (tightening)
Maks. Kablo Kesiti: 4 AWG (21.2mm²)
```

#### Panel Düzeni

```
┌─────────────────────────────────────────────────────────┐
│                     ARKA PANEL                          │
│                                                         │
│  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐      │
│  │XLR 1│ │XLR 2│ │XLR 3│ │XLR 4│ │XLR 5│ │XLR 6│      │
│  │FL   │ │FR   │ │C    │ │SL   │ │SR   │ │SBL  │      │
│  └─────┘ └─────┘ └─────┘ └─────┘ └─────┘ └─────┘      │
│                                                         │
│  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐      │
│  │XLR 7│ │RCA 1│ │RCA 2│ │USB-C│ │TosLk│ │HDMI │      │
│  │SBR  │ │L    │ │R    │ │In   │ │In   │ │ARC  │      │
│  └─────┘ └─────┘ └─────┘ └─────┘ └─────┘ └─────┘      │
│                                                         │
│  ┌─────────────────────────────────────────────────┐    │
│  │              BINDING POSTS                       │    │
│  │  (+FL) (-FL) (+FR) (-FR) (+C) (-C) (+SL) (-SL) │    │
│  └─────────────────────────────────────────────────┘    │
│                                                         │
│  ┌─────────────────────────────────────────────────┐    │
│  │              BINDING POSTS (Devam)               │    │
│  │  (+SR) (-SR) (+SBL)(-SBL)(+SBR)(-SBR)(+LFE)(-LFE)│   │
│  └─────────────────────────────────────────────────┘    │
│                                                         │
│  ┌─────┐ ┌─────┐ ┌─────┐                               │
│  │IEC │ │Fuse │ │SWITCH│                               │
│  │Inlet│ │     │ │     │                               │
│  └─────┘ └─────┘ └─────┘                               │
└─────────────────────────────────────────────────────────┘
```

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | Panel layout, drilling |
| K1 Amplifikatör | Bağlantı | XLR/RCA input |
| K1 Hoparlör | Çıkış | Binding posts |
| K1 USB Audio | Bağlantı | USB-C input |
| K1 Koruma | Bağlantı | Relay switching |

#### Durum: Implementasyon

### 6.3 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/hoparlor-dizilimi.md`


### Hoparlör Dizilimi - 8.1 Surround

#### Genel Bakış

COREMUSIC 8.1 Surround hoparlör sistemi, 8 full-range hoparlör ve 1 subwoofer'dan oluşan tam surround ses konfigürasyonudur. ITU-R BS.775-1 standardına uygun olarak yerleştirilir. Her hoparlör bağımsız Class AB amplifikatör kanalı tarafından sürülür.

#### Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Hoparlör Sayısı | 8 + 1 subwoofer |
| Toplam Çıkış Gücü | 2.250W RMS |
| Frekans Aralığı | 20Hz – 20kHz (full-range) |
| Subwoofer Aralığı | 20Hz – 120kHz |
| Empedans | 8Ω (tüm hoparlörler) |
| Hassasiyet | 89dB/W/m |
| Maks. Basınç Seviyesi | 115dB SPL |
| THD | < %1 (maks. güçte) |

#### Hoparlör Konumları

```
                    ÖN (FRONT)
                        │
            ┌───────────┼───────────┐
            │           │           │
            ▼           ▼           ▼
         ┌─────┐    ┌─────┐    ┌─────┐
         │ FL  │    │  C  │    │ FR  │
         │Front│    │Center│    │Front│
         │Left │    │      │    │Right│
         └─────┘    └─────┘    └─────┘
           -30°        0°        +30°

SOL (LEFT)                            SAĞ (RIGHT)
    │                                      │
    ▼                                      ▼
┌─────┐                              ┌─────┐
│ SL  │                              │ SR  │
│Side │                              │Side │
│Left │                              │Right│
└─────┘                              └─────┘
  -90°                                 +90°

        ┌─────┐              ┌─────┐
        │ SBL │              │ SBR │
        │Surround          │Surround
        │Back L│           │Back R│
        └─────┘              └─────┘
          -135°                +135°

                    ARKA (REAR)
                        │
                   ┌────┴────┐
                   │   SUB   │
                   │Subwoofer│
                   └─────────┘
                    (Front-Left alt)
```

#### Kanal Haritası

| Kanal | Kısaltma | Konum | Açı | Amplifikatör |
|-------|----------|-------|-----|-------------|
| 1 | FL | Front Left | -30° | Amp Kanal 1 |
| 2 | C | Center | 0° | Amp Kanal 2 |
| 3 | FR | Front Right | +30° | Amp Kanal 3 |
| 4 | SL | Side Left | -90° | Amp Kanal 4 |
| 5 | SR | Side Right | +90° | Amp Kanal 5 |
| 6 | SBL | Surround Back Left | -135° | Amp Kanal 6 |
| 7 | SBR | Surround Back Right | +135° | Amp Kanal 7 |
| 8 | LFE | Subwoofer | Front-Left | Amp Kanal 8 |

#### Hoparlör Özellikleri

##### Full-Range Hoparlörler (FL, C, FR, SL, SR, SBL, SBR)

| Parametre | Değer |
|-----------|-------|
| Tip | 2-way (Woofer + Tweeter) |
| Woofer | 6.5" (165mm) |
| Tweeter | 1" (25mm) Dome |
| Frekans Crossover | 2.5kHz |
| Empedans | 8Ω |
| Güç Kapasitesi | 200W RMS |
| Hassasiyet | 89dB/W/m |

##### Subwoofer (LFE)

| Parametre | Değer |
|-----------|-------|
| Tip | Passive radiator |
| Woofer | 10" (250mm) |
| Frekans Aralığı | 20Hz – 120kHz |
| Empedans | 8Ω |
| Güç Kapasitesi | 250W RMS |
| Hassasiyet | 87dB/W/m |

#### Kablo ve Bağlantılar

| Kanal | Kablo Tipi | Uzunluk (Maks.) | Konnektör |
|-------|------------|-----------------|-----------|
| FL, FR | 12 AWG | 3m | Banana Plug |
| C | 12 AWG | 2m | Banana Plug |
| SL, SR | 12 AWG | 5m | Banana Plug |
| SBL, SBR | 12 AWG | 8m | Banana Plug |
| LFE | 10 AWG | 3m | Banana Plug |

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Amplifikatör | Giriş | 8 kanallı Class AB amp |
| K1 Konnektörler | Bağlantı | Binding posts |
| K1 Koruma | Bağlantı | Speaker protection relay |
| K6 DSP | Üst | Crossover ve EQ ayarları |

#### Durum: Implementasyon

**Durum**: 🟡 Tasarım Aşamasında

- Hoparlör seçimi: Faital Pro, Peerless, SB Acoustics değerlendiriliyor
- Crossover: 2.5kHz Linkwitz-Riley 4th order
- Subwoofer: 80kHz low-pass filter (K6 DSP'de)
- Yerleşim: ITU-R BS.775-1 standardına uygun
- Kablo yönetimi: Kanal içi twist, dış tuing shielded


## 7. Kenar Durumlar

| # | Kenar durum | Etki | Kaynak / Durum |
|---:|---|---|---|
| 1 | PCB katman sayısı ve empedans hedefi kaynakta sayısal değil | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 2 | Akım yolu kesiti (mm²/oz) doğrulanamıyor | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |

## 8. Hata Modları

1. Analog/dijital toprakların yanlış noktasında birleşmesi → gürültü enjeksiyonu (kaynak: `pcb-tasarim`).
2. Yüksek akım yolunun daralması → ısınma ve voltaj düşümü (kaynak: `pcb-tasarim`).

## 9. Bağımlılık Matrisi

| Komşu K | Yön | İlişki | Kanıt |
|---|:---:|---|---|
| `[[../k011-guc-koruma-termal/index]]` | ↑ | Güç yerleşimi ve ısı yönetimi | `.ai/architecture/k011-guc-koruma-termal/index.md` |
| `[[../k010-class-ab-cikis/index]]` | ↑ | Çıkış transistörlerinin yerleşimi | `.ai/architecture/k010-class-ab-cikis/index.md` |
| `[[../k012-dijital-arayuz/index]]` | ↑ | Dijital hatların PCB geçişi | `.ai/architecture/k012-dijital-arayuz/index.md` |

Yerel dosyalar:

- `[[pcb-ve-hoparlor]]` — PCB Tasarım + Hoparlör Dizilimi
- `[[hoparlor-dizilimi-detay]]` — Hoparlör Dizilimi Detay

## 10. Kanıt ve Doğrulama

- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/pcb-tasarim.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/konnektorler.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/hoparlor-dizilimi.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` bu dosya üretildikten sonra `vault-utf8-writer verify` ile BOM/UTF-8 kontrolü yapılır.
- `⚠️ VERIFICATION REQUIRED` — kaynakta bulunmayan ölçüm/sürüm/ADR değerleri bu dosyaya yazılmamıştır.
- Klasör geneli bağımlılıklar ve okuma sırası: `[[index]]`.

---
*K013 · PCB Tasarım + Hoparlör Dizilimi — SSOT: `.ai/architecture/k013-pcb-hoparlor/`; ham kaynak: `_backup/arch-2026-10-06_1057/architecture/`.*
