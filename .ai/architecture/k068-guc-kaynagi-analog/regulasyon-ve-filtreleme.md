---
title: "Regülasyon ve Filtreleme - k068-guc-kaynagi-analog"
type: architecture-sublayer
category: architecture
version: 1.0.0
status: active
authority: "Vault (.ai/) SSOT - verbatim aktarım: _backup/arch-2026-10-06_1057/architecture/"
updated: 2026-10-06
---

# Regülasyon ve Filtreleme

> Klasör: `k068-guc-kaynagi-analog` · Dosya: `regulasyon-ve-filtreleme.md`
> Sorumlu persona: `audio-hardware-engineer` (CoreMusic Audio Hardware Engineer)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/` — aktarım bölüm bazında L aralığı ile kanıtlanmıştır.

## Genel Bakış

±35V analog güç kaynağı: güç topolojisi, LM5122 dual boost converter, voltaj ayarı, bileşen listesi ve ripple analizi; EMC filtreleme stratejileri (kanal bazlı filtreler, transformatör/seçici tipleri, yerleşim kuralları).

## Kapsam ve Sınırlar

- **Kapsam:** lineer/boost regülasyon, ripple analizi, EMC filtreleme ve filtrelendirme yerleşimi.
- **Kapsam dışı:** topraklama/decoupling/power sequencing (→ [[topraklama-ve-guc-dagilimi.md]]); koruma devreleri (→ [[../k067-koruma-devreleri/index]]).
- **Bağlı olduğu klasör:** [[index.md]]
- **Çapraz referanslar:** [[../k067-koruma-devreleri/index]] · [[../k070-pcb-tasarim/index]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi ve içerik kaynaktan değiştirilmeden kopyalanmıştır; her bloğun üstündeki `> Aktarım:` satırı kaynak dosyanın disk satır aralığını gösterir.
### `_backup/arch-2026-10-06_1057/architecture/k1-donanim/guc-kaynagi-analog.md` (164 satır)

#### 1. guc-kaynagi-analog.md — giriş, topoloji, LM5122, bileşen listesi, ripple analizi

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/guc-kaynagi-analog.md` - L1-L137

---
title: "±35V Analog Güç Kaynağı"
layer: K1
category: "Güç Kaynağı"
date: 2026-09-20
---

# ±35V Analog Güç Kaynağı

## Genel Bakış

±35V analog güç kaynağı, COREMUSIC'ın Class AB amplifikatörleri için dual rail güç sağlar. LM5122 dual boost converter topolojisi ile AC mains'den yüksek verimli ±35V DC üretir. Low-noise design ile sinyal/gürültü oranını korur.

## Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Çıkış Voltajı | ±35V DC (±%1 tolerance) |
| Maks. Yük Akımı | 8A (toplam, her iki rail) |
| Giriş Voltajı | 100-240V AC (Universal) |
| Giriş Frekansı | 50/60Hz |
| Çıkış Gücü | 560W (8A × 35V × 2) |
| Verimlilik | > %90 (full load) |
| Ripple | < 10mVpp |
| Regülasyon | < %0.1 (line/load) |
| Koruma | Overcurrent, Overvoltage, Thermal |

## Güç Topolojisi

```
AC Mains (100-240V)
     │
     ▼
┌──────────┐
│  EMI     │  X2 kapasitör, common-mode choke
│  Filter  │
└────┬─────┘
     │
     ▼
┌──────────┐
│  Bridge  │  GBPC2510 (25A, 1000V)
│  Rectifier│
└────┬─────┘
     │
     ▼
┌──────────┐
│  PFC     │  CCM PFC (Power Factor Correction)
│  Stage   │  PF > 0.99
└────┬─────┘
     │
     ├──────────────────────────────┐
     │                              │
     ▼                              ▼
┌──────────┐                  ┌──────────┐
│  +35V    │                  │  -35V    │
│  Boost   │                  │  Invert  │
│  LM5122  │                  │  LM5122  │
└────┬─────┘                  └────┬─────┘
     │                              │
     ▼                              ▼
┌──────────┐                  ┌──────────┐
│  LC      │                  │  LC      │
│  Filter  │                  │  Filter  │
└────┬─────┘                  └────┬─────┘
     │                              │
     ▼                              ▼
   +35V                           -35V
   (Analog)                      (Analog)
```

## Devre Tasarımı

### LM5122 Dual Boost Converter

```
LM5122 #1 (+35V Boost)
     │
     ├─ VIN ──▶ PFC Output (+400V DC)
     ├─ SW ───▶ Inductor (33µH) ──▶ Schottky (MBR20100CT)
     ├─ FB ───▶ Resistive Divider (R1=100kΩ, R2=3.3kΩ)
     ├─ COMP ─▶ RC Network (10kΩ + 100nF)
     ├─ SS ───▶ Soft-start Capacitor (100nF)
     └─ GND ──▶ AGND Plane

LM5122 #2 (-35V Inverting Boost)
     │
     ├─ VIN ──▶ PFC Output (+400V DC)
     ├─ SW ───▶ Inductor (33µH) ──▶ Schottky (MBR20100CT)
     ├─ FB ───▶ Resistive Divider (R1=100kΩ, R2=3.3kΩ)
     ├─ COMP ─▶ RC Network (10kΩ + 100nF)
     ├─ SS ───▶ Soft-start Capacitor (100nF)
     └─ GND ──▶ AGND Plane
```

### Voltaj Ayarı

```
Vout = 1.221V × (1 + R1/R2)

R1 = 100kΩ, R2 = 3.3kΩ

Vout = 1.221V × (1 + 100/3.3)
Vout = 1.221V × 31.3
Vout = 38.2V (no-load, slightly higher than 35V)

Load regulation: ±0.5V
Line regulation: ±0.2V
```

## Bileşen Listesi

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | PFC Controller | NCP1654 | 1 | CCM PFC |
| 2 | Boost Converter | LM5122 | 2 | Dual output |
| 3 | Power MOSFET | IRFB4227PBF | 2 | 200V/65A |
| 4 | Schottky Diode | MBR20100CT | 2 | 100V/20A |
| 5 | Inductor | 33µH/10A | 2 | Toroid core |
| 6 | Output Cap | 470µF/50V | 8 | Electrolytic |
| 7 | EMI Filter | X2 100nF | 1 | EMC compliance |
| 8 | Common-mode Choke | 10mH | 1 | EMC compliance |

## Ripple Analizi

```
ΔVout = IL × D × (1-D) / (fsw × Cout)

IL = 8A (max load)
D = 0.175 (duty cycle @ 400V input)
fsw = 200kHz (switching frequency)
Cout = 470µF × 8 = 3.76mF

ΔVout = 8 × 0.175 × 0.825 / (200,000 × 0.00376)
ΔVout = 1.155 / 752
ΔVout = 1.5mVpp (target: < 10mVpp)
```


### `_backup/arch-2026-10-06_1057/architecture/k17-guc-kaynagi/emc-filtering.md` (275 satır)

#### 2. emc-filtering.md — EMC filtreleme stratejileri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k17-guc-kaynagi/emc-filtering.md` - L1-L231

---
title: "EMC Filtreleme"
layer: K17
category: "Güç Kaynağı"
date: 2026-09-20
---

# EMC Filtreleme (EMI/EMC Filtering)

## Genel Bakış

EMC (Electromagnetic Compatibility) filtreleme, COREMUSIC'in hem kendi içinde hem de dış dünya ile uyumlu çalışmasını sağlar. Anahtarlama power supply'lerinden kaynaklanan EMI'yi (Electromagnetic Interference) bastırarak, hassas ses devrelerini korur ve düzenleyici standartlara (CISPR, FCC) uyum sağlar. Çok katmanlı filtreleme stratejisi ile conducted ve radiated emisyonlar kontrol altına alınır.

## EMC Mimarisi

```
┌──────────────────────────────────────────────────────────────────────┐
│                    EMC FİLTRELEME MİMARİSİ                            │
├──────────────────────────────────────────────────────────────────────┤
│                                                                      │
│  ┌────────────────────────────────────────────────────────────────┐  │
│  │  GİRİŞ EMC FİLTRESİ                                            │  │
│  │                                                                  │  │
│  │  ┌─────────┐   ┌─────────┐   ┌─────────┐   ┌─────────┐       │  │
│  │  │ X-Cap   │──▶│ CM      │──▶│ Y-Cap   │──▶│ Fuse    │       │  │
│  │  │ 100nF   │   │ Choke   │   │ 4.7nF   │   │ 10A     │       │  │
│  │  │ (Differential)│ 10mH   │   │ (CM)    │   │         │       │  │
│  │  └─────────┘   └─────────┘   └─────────┘   └─────────┘       │  │
│  └────────────────────────────────────────────────────────────────┘  │
│                                                                      │
│  ┌────────────────────────────────────────────────────────────────┐  │
│  │  ÇIKIŞ EMC FİLTRESİ                                            │  │
│  │                                                                  │  │
│  │  ┌─────────┐   ┌─────────┐   ┌─────────┐   ┌─────────┐       │  │
│  │  │ LC      │──▶│ Ferrite │──▶│ Feed-   │──▶│ Bypass  │       │  │
│  │  │ Filter  │   │ Bead    │   │ through │   │ Cap     │       │  │
│  │  │ 10μH/100μF│ │ 600Ω   │   │ Cap     │   │ 100nF   │       │  │
│  │  └─────────┘   └─────────┘   └─────────┘   └─────────┘       │  │
│  └────────────────────────────────────────────────────────────────┘  │
│                                                                      │
│  ┌────────────────────────────────────────────────────────────────┐  │
│  │  TOPLAM EMC PERFORMANSI                                        │  │
│  │                                                                  │  │
│  │  Conducted: CISPR 32 Class B (<30MHz)  ✅ 6dB marjı           │  │
│  │  Radiated:  CISPR 32 Class B (>30MHz)  ✅ 3dB marjı           │  │
│  │  ESD:       IEC 61000-4-2 ±8kV         ✅                      │  │
│  │  Surge:     IEC 61000-4-5 ±2kV         ✅                      │  │
│  └────────────────────────────────────────────────────────────────┘  │
│                                                                      │
└──────────────────────────────────────────────────────────────────────┘
```

## Giriş EMC Filtresi

### X-Capacitor (Differential Mode)

```
L_N ──────┬───── DUT
          │
        C_X (100nF)
          │
L_E ──────┴───── DUT

Kesim Frekansı:
fc = 1 / (2π × Z_source × C_X)
fc ≈ 160kHz @ Z_source = 10Ω

Attenuation @ 1MHz: -20dB/decade
```

### Common Mode Choke

```
       ┌─────── L1 (10mH) ───────┐
L_N ───┤                          ├─── DUT
       │    ╔═══════════════╗    │
       └────║  Ferrite Core ║────┘
            ║  (High μ)     ║
L_E ────────║               ║──── DUT
            ╚═══════════════╝

Parametreler:
• Indüktans: 10mH (her iki sargı için)
• Akım: 10A continuous
• DC Resistance: <50mΩ
• Frekans Aralığı: 10kHz - 100MHz
• Measures: 30×20×15mm
```

### Y-Capacitor (Common Mode)

```
L_N ──────┬───── DUT
          │
        C_Y (4.7nF)  ← Safety rated (Y1)
          │
        FG (Chassis)
          │
        C_Y (4.7nF)  ← Safety rated (Y1)
          │
L_E ──────┴───── DUT

Toplam CM kapasite: 9.4nF
Güvenlik: Y1 rated, 250VAC
```

## Çıkış EMC Filtresi

### LC Output Filter

```
              L_OUT (10μH)
VIN_BOOST ────┤├────────────┬──── VOUT (+35V)
                          │
                      C_OUT (100μF)
                          │
                         GND

Kesim Frekansı:
fc = 1 / (2π × √(L × C))
fc = 1 / (2π × √(10μH × 100μF))
fc = 5.03kHz

Attenuation @ 1MHz:
A = (fSW / fc)² = (1MHz / 5.03kHz)² = 39524
A_dB = 20 × log(39524) = 92dB
```

### Ferrite Bead

```
VOUT ────┤ FB (600Ω @ 100MHz) ├──── Load

Parametreler:
• Empedans: 600Ω @ 100MHz
• DC Resistance: 20mΩ
• Maks Akım: 3A
• Paket: 0805
```

## Frekans Spektrumu Analizi

```
EMI Seviyesi (dBμV)
  100│
     │    ╔═══════════════════════════════════════╗
   80│    ║  CISPR 32 Class B Limit               ║
     │    ╚═══════════════════════════════════════╝
   60│  ████
     │  ████
   40│  ████  ████
     │  ████  ████  ████
   20│  ████  ████  ████  ████
     │  ████  ████  ████  ████  ████
    0│──████──████──████──████──████──████─────────
     │  100k  500k  1M    5M   10M   30M   100M
     │               Frekans (Hz)
     
     ████: Filtre Öncesi (ses mühendisliği için tehlikeli)
     ▒▒▒▒: Filtre Sonrası (CISPR limitinin altında)
```

## ESD Koruma

### TVS Diyot Ağı

```
        ┌─────────────────────────────────┐
        │  ESD Koruma Devresi              │
        │                                   │
USB ────┤─── TVS1 (USBLC6-2) ──── MCU    │
        │       │                          │
        │    6.8nF                         │
        │       │                          │
GND ────┤───────┴──────────────────────────│
        │                                   │
Audio ──┤─── TVS2 (SMBJ5.0A) ──── Op-Amp │
        │       │                          │
        │    10nF                          │
        │       │                          │
GND ────┤───────┴──────────────────────────│
        └─────────────────────────────────┘

ESD Specs:
• Contact: ±8kV
• Air: ±15kV
• Response: <1ns
```

## Ferrite Seçimi

| Frekans | Empedans | Malzeme | Kullanım |
|---------|----------|---------|----------|
| 1MHz | 100Ω | MnZn | Low-freq filtering |
| 10MHz | 300Ω | MnZn | Mid-freq filtering |
| 100MHz | 600Ω | NiZn | High-freq filtering |
| 500MHz | 1000Ω | NiZn | Very high-freq |

## PCB Layout EMC Kuralları

```
┌─────────────────────────────────────────────────────────────┐
│  EMC LAYOUT KURALLARI                                        │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  1. GROUNDING                                                 │
│     • Solid ground plane (bottom layer)                      │
│     • Star ground topology                                   │
│     • Analog ve digital ground ayrımı                       │
│     • Via stitching: her 200mil'de bir                       │
│                                                               │
│  2. TRACE ROUTING                                             │
│     • High-current traces: 2oz copper                        │
│     • Switching nodes: minimal loop area                     │
│     • Sensitive traces: ground guard                         │
│     • 3W rule: trace spacing ≥ 3× trace width               │
│                                                               │
│  3. COMPONENT PLACEMENT                                      │
│     • Input filter: connector'a yakın                        │
│     • Output filter: load'a yakın                            │
│     • Bypass caps: pin'e 500mil içinde                       │
│     • Ferrite beads: noise source'e yakın                    │
│                                                               │
│  4. SHIELDING                                                 │
│     • Metal shield, switching converter üzerinde                │
│     • Connector shield: chassis ground                       │
│     • Cable shield: 360° termination                         │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

## İlgili Dosyalar

[[index.md]] · [[topraklama-ve-guc-dagilimi.md]] · [[../k067-koruma-devreleri/index]] · [[../k070-pcb-tasarim/index]]
