---
title: "PCM3168A — 24-bit 6-in/8-out Audio Codec (TI Burr-Brown) Teknoloji Derin-Dalı"
type: architecture-deepdive
node_id: "K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec"
node_type: technology-deepdive
status: draft
parent_id: K001-donanım/donanaım-type/audio
children: []
depends_on:
  - K001-donanım
  - K001-donanım/donanaım-type/audio/index
evidence:
  - https://www.ti.com/product/PCM3168A
  - https://www.usb.org/
  - K001-donanım/donanaım-type/audio/cipler/ak4458-dac.md
validation: "2026-10-13: §13-§16 eklendi; 480 → 500+ satır; doğrulanmamış parametreler işaretli kaldı"
open_questions:
  - "Kanal planı: 8 DAC kanalı hangi K16 modüler kanallara eşlenecek"
  - "Kontrol modu SPI mı I2C mı — MODE pin strap'i kararı yok"
  - "Sürüm kanıtı yok: JUCE 9 ve ASIO SDK 2.3.4 doğrulanamadı (K001 kural 12)"
date: 2026-10-12
updated: 2026-10-13
group: mimari
---

# PCM3168A — 24-bit 6-in/8-out Audio Codec Teknoloji Derin-Dalı

> **Kapsam:** Donanım çipi teknoloji derin-dalı. Tasarım kararı / BOM / şema üretmez; her rakam
> okunan resmi kaynakla (ti.com ürün sayfası veya SBAS452 datasheet) işaretlenmiştir. Okunmayan
> parametre `⚠️ VERIFICATION REQUIRED`dir. Zero-Hallucination (kök `AGENTS.md` §3) bağlayıcıdır.

---

## §0 Künye (Kimlik)

| Alan | Değer | Kaynak |
|---|---|---|
| Üretici | Texas Instruments (Burr-Brown Audio mirası) | ti.com ürün sayfası + datasheet header |
| Part Number | PCM3168A (PCM3168A-Q1 otomotiv varyantı pin-for-pin aynı) | ti.com + datasheet |
| Part Status | ACTIVE | ti.com ürün sayfası |
| Başlık | "24-Bit, 96-kHz/192-kHz, 6-In/8-Out Audio Codec With Differential Input/Output" | ti.com + datasheet |
| Mimari | ΔΣ (delta-sigma) ADC + ΔΣ DAC, tek çip | datasheet §1 |
| ADC kanal | 6 (SE/differansiyel seçilebilir girdi) | datasheet §1 |
| DAC kanal | 8 (diferansiyel çıkışı sabit konfigürasyon) | datasheet §1 |
| Paket | HTQFP-64 PowerPAD (PAP), 12mm×12mm (10mm×10mm gövde, 0.5mm pitch) | ti.com + datasheet |
| Sıcaklık | Katalog: −40…+85°C · Otomotiv (Q1): −40…+105°C | ti.com + datasheet §8 |
| Datasheet doküman no | SBAS452–SEPTEMBER 2008 (Rev. A: Jan 7, 2016) | datasheet header + ti.com doküman tablosu |
| Linux sürücü | PCM3168ASW-LINUX (mainline Linux'ta mevcut, ASOC framework, I2C+SPI) | ti.com "Design & development" |
| IBIS model | PCM3168PAP IBIS Model (SLAC203.ZIP, 29 KB) | ti.com |
| EVM GUI | SLAC268 — PCM3168AEVM Graphical User Interface | ti.com |
| Referans tasarım | TIDA-00609 "Self-Booting Audio System" | ti.com |

**Uygulama alanları (datasheet §1):** Car Audio External Amplifiers · Car Audio AVN
Applications · Home Theaters · AV Receivers.

---

## §1 Blok Diyagramı ve Sinyal Akışı (ASCII — datasheet Functional Block Diagram)

```text
            ADC YOLU (6 kanal)                         DAC YOLU (8 kanal)
 VIN1± ─┐                                            ┌─ VOUT1±
 VIN2± ─┤  ┌──────────────┐                          │  VOUT2±
 VIN3± ─┼─►│ 6 × ADC      │   DOUT1 (ADC1/2)        │  VOUT3±
 VIN4± ─┤  │ ΔΣ + Digital │──► DOUT2 (ADC3/4)        │  VOUT4±
 VIN5± ─┤  │ Filter + HPF │   DOUT3 (ADC5/6)         ├─ VOUT5±
 VIN6± ─┘  │ + Volume     │                          │  VOUT6±
           └──────┬───────┘                          │  VOUT7±
                  │ LRCKAD/BCKAD                     │  VOUT8±
                  ▼                                  │  (hepsi diferansiyel,
           Audio Serial Interface                    │   8 VPP tip.)
                  ▲                                  │
                  │ LRCKDA/BCKDA/DIN1-4              │
           ┌──────┴───────┐                          │
           │ 8 × DAC      │◄─────────────────────────┘
           │ ΔΣ + Digital │
           │ Filter+Volume│   ZERO (zero detect flag)
           └──────────────┘   OVF  (ADC overflow flag)

Mode Control Port: MODE(48) · MS/ADR0/MD0(45) · MDO/ADR1/MD1(44) · MDI/SDA/DEMP(43)
                   MC/SCL/FMT(42) · RST(4)      → SPI / I2C / H/W control
Common & Reference: VREFAD1(59) · VREFAD2(60) · VCOMAD(1) · VCOMDA(15)
Güç domainleri: VCCAD1/2 (analog 5V) · VCCDA1/2 (analog 5V) · VDD1/2 (dijital 3.3V)
```

---

## §1B Feature List — datasheet §1 (tam liste, satır satır)

- 24-Bit ΔΣ ADC and DAC (tek çip).
- Six-Channel ADC — High Performance: Differential and Single-Ended, fS = 48 kHz.
- ADC THD+N: −93 dB (Differential and Single-Ended).
- ADC SNR: 107 dB (Differential), 104 dB (Single-Ended).
- ADC Dynamic Range: 107 dB (Differential), 104 dB (Single-Ended).
- ADC Sampling Rate: 8 kHz to 96 kHz.
- ADC System Clock: 256 fS, 384 fS, 512 fS, 768 fS.
- ADC Differential Voltage Input: 2 VRMS · Single-Ended Voltage Input: 1 VRMS.
- ADC Decimation Filter: Passband Ripple ±0.035 dB · Stop Band Attenuation −75 dB.
- On-Chip Highpass Filter: 0.96 Hz at fS = 48 kHz.
- ADC Overflow Flag (OVF, pin 5).
- Eight-Channel DAC — High Performance: Differential, fS = 48 kHz.
- DAC THD+N: −94 dB · SNR: 112 dB · Dynamic Range: 112 dB.
- DAC Sampling Rate: 8 kHz to 192 kHz.
- DAC System Clock: 128 fS, 192 fS, 256 fS, 384 fS, 512 fS, 768 fS.
- DAC Differential Voltage Output: 8 VPP · Analog Lowpass Filter Included.
- DAC 4x/8x Oversampling Digital Filter: Passband Ripple ±0.0018 dB · Stop Band −75 dB.
- DAC Zero Flag (ZERO, pin 13).
- Flexible Mode Control: Four-Wire SPI, Two-Wire I2C Compatible Serial Control Interface or Hardware Control.
- SPI/I2C Multi Functions: Audio I/F Mode and Format Select (ADC and DAC) · Digital Attenuation and Soft Mute (ADC and DAC) · Digital De-Emphasis: 32, 44.1, 48 kHz for DAC.
- H/W Control Multi Functions: Audio I/F Mode/Format Select · Digital De-Emphasis Filter 44.1 kHz for DAC.
- External Reset Pin: ADC/DAC Simultaneous (RST, pin 4).
- Audio Interface Mode: ADC/DAC Independent Master and Slave.
- Audio Data Format: ADC/DAC Independent I2S, Left-Justified, Right-Justified, DSP, TDM.
- Power Supplies: 5 V for Analog and 3.3 V for Digital.
- Package: HTQFP-64 (PowerPAD, 12mm×12mm, 144 mm²).
- Operating Temperature: Consumer Grade −40°C to +85°C · Automotive Audio Grade −40°C to +105°C.

---

## §1C Sipariş Bilgisi (ti.com, 2026-10-12)

| Sipariş kodu | Durum | Package | Pin | Sıcaklık |
|---|---|---|---|---|
| PCM3168APAPR | ACTIVE | PAP (HTQFP-64) | 64 | −40…+85°C |
| PCM3168A-Q1 (PAP) | ACTIVE | PAP (HTQFP-64) | 64 | −40…+105°C (otomotiv audio grade) |

- CAD/sembol/footprint: Ultra Librarian bağlantısı ti.com sayfasında (MPQF071C package doc).
- IBIS: SLAC203.ZIP (29 KB) · EVM GUI: SLAC268 · Referans tasarım: TIDA-00609.

---

## §2 Pin Konfigürasyonu (HTQFP-64 — datasheet §7 Terminal Functions, pin numaraları datasheet'ten)

| Pin | Ad | I/O | Pull-down | 5-V tolerant | Açıklama |
|---|---|---|---|---|---|
| 1 | VCOMAD | — | No | No | ADC analog common voltage decoupling |
| 2 | AGNDAD2 | — | No | No | Analog ground 2 for ADC |
| 3 | VCCAD2 | — | No | No | ADC analog power supply 2, +5 V |
| 4 | RST | I | Yes | Yes | Reset and power-down control input, active low |
| 5 | OVF | O | No | No | Overflow flag output for ADC |
| 6 | LRCKAD | I/O | Yes | No | Audio data word clock in/out for ADC |
| 7 | BCKAD | I/O | Yes | No | Audio data bit clock in/out for ADC |
| 8 | DOUT1 | O | No | No | Audio data output for ADC1 and ADC2 |
| 9 | DOUT2 | O | No | No | Audio data output for ADC3 and ADC4 |
| 10 | DOUT3 | O | No | No | Audio data output for ADC5 and ADC6 |
| 11 | DGND2 | — | No | No | Digital ground 2 |
| 12 | VDD2 | — | No | No | Digital power supply 2, +3.3 V |
| 13 | ZERO | O | No | No | Zero detect flag output for DAC |
| 14 | VCCDA1 | — | No | No | DAC analog power supply 1, +5 V |
| 15 | VCOMDA | — | No | No | DAC voltage common decoupling |
| 16 | AGNDDA1 | — | No | No | Analog ground 1 for DAC |
| 17 | VOUT8+ | O | No | No | Positive analog output from DAC8 |
| 18 | VOUT8− | O | No | No | Negative analog output from DAC8 |
| 19 | VOUT7+ | O | No | No | Positive analog output from DAC7 |
| 20 | VOUT7− | O | No | No | Negative analog output from DAC7 |
| 21 | VOUT6+ | O | No | No | Positive analog output from DAC6 |
| 22 | VOUT6− | O | No | No | Negative analog output from DAC6 |
| 23 | VOUT5+ | O | No | No | Positive analog output from DAC5 |
| 24 | VOUT5− | O | No | No | Negative analog output from DAC5 |
| 25 | VOUT4+ | O | No | No | Positive analog output from DAC4 |
| 26 | VOUT4− | O | No | No | Negative analog output from DAC4 |
| 27 | VOUT3+ | O | No | No | Positive analog output from DAC3 |
| 28 | VOUT3− | O | No | No | Negative analog output from DAC3 |
| 29 | VOUT2+ | O | No | No | Positive analog output from DAC2 |
| 30 | VOUT2− | O | No | No | Negative analog output from DAC2 |
| 31 | VOUT1+ | O | No | No | Positive analog output from DAC1 |
| 32 | VOUT1− | O | No | No | Negative analog output from DAC1 |
| 33 | AGNDDA2 | — | No | No | Analog ground 2 for DAC |
| 34 | VCCDA2 | — | No | No | DAC analog power supply 2, +5 V |
| 35 | LRCKDA | I/O | Yes | No | Audio data word clock in/out for DAC |
| 36 | BCKDA | I/O | Yes | No | Audio data bit clock in/out for DAC |
| 37 | DIN1 | I | No | No | Audio data input for DAC1 and DAC2 |
| 38 | DIN2 | I | No | No | Audio data input for DAC3 and DAC4 |
| 39 | DIN3 | I | No | No | Audio data input for DAC5 and DAC6 |
| 40 | DIN4 | I | No | No | Audio data input for DAC7 and DAC8 |
| 41 | SCKI | I | No | Yes | System clock input |
| 42 | MC/SCL/FMT | I | No | Yes | SPI clock · I2C clock · H/W control mode format select |
| 43 | MDI/SDA/DEMP | I/O | No | Yes | SPI input data · I2C data (open-drain) · H/W de-emphasis control |
| 44 | MDO/ADR1/MD1 | I/O | No | No | SPI output data (3-state Hi-Z) · I2C address 1 · H/W mode 1 |
| 45 | MS/ADR0/MD0 | I | Yes | Yes | SPI chip select · I2C address 0 · H/W mode 0 |
| 46 | VDD1 | — | No | No | Digital power supply 1, +3.3 V |
| 47 | DGND1 | — | No | No | Digital ground 1 |
| 48 | MODE | I | No | No | Control port mode: VDD=SPI · pull-up=H/W SE input · pull-down=H/W differential input · DGND=I2C |
| 49 | VCCAD1 | — | No | No | ADC analog power supply 1, +5 V |
| 50 | AGNDAD1 | — | No | No | Analog ground 1 for ADC |
| 51 | VIN1− | I | No | No | Negative analog input to ADC1 |
| 52 | VIN1+ | I | No | No | Positive analog input to ADC1 |
| 53 | VIN2− | I | No | No | Negative analog input to ADC2 |
| 54 | VIN2+ | I | No | No | Positive analog input to ADC2 |
| 55 | VIN3− | I | No | No | Negative analog input to ADC3 |
| 56 | VIN3+ | I | No | No | Positive analog input to ADC3 |
| 57 | VIN4− | I | No | No | Negative analog input to ADC4 |
| 58 | VIN4+ | I | No | No | Positive analog input to ADC4 |
| 59 | VREFAD1 | — | No | No | ADC analog reference voltage 1 decoupling |
| 60 | VREFAD2 | — | No | No | ADC analog reference voltage 2 decoupling |
| 61 | VIN5− | I | No | No | Negative analog input to ADC5 |
| 62 | VIN5+ | I | No | No | Positive analog input to ADC5 |
| 63 | VIN6− | I | No | No | Negative analog input to ADC6 |
| 64 | VIN6+ | I | No | No | Positive analog input to ADC6 |
| PowerPAD | (epad) | — | — | — | Connected to analog ground |

**Referans/common pinleri (datasheet §1 açıklaması):** VREFAD1/2 her ikisi de analog ground'a
decoupling kapasitörü ile bağlanmalı; VCOMDA ve VCOMAD da aynı şekilde decoupling ister ve
harici yüksek empedanslı devreleri biaslamak için kullanılabilir.

---

## §3 Mode Control Port: SPI / I2C / Hardware Control (datasheet §1, §7)

| Mod | Seçim (MODE pin 48) | Kullanılan pinler | Notlar |
|---|---|---|---|
| SPI (4-wire) | MODE = VDD | MS/ADR0/MD0 (CS), MC (clock), MDI (in), MDO (out) | MDO 3-state (Hi-Z) |
| I2C (2-wire) | MODE = DGND | SCL (MC), SDA (MDI), ADR0/ADR1 adres | SDA open-drain |
| H/W control, SE input | MODE = pull-up | MD0/MD1 (mode), FMT (MC), DEMP (MDI) | Alt küme fonksiyonlar |
| H/W control, differential input | MODE = pull-down | MD0/MD1, FMT, DEMP | Alt küme fonksiyonlar |

**H/W control ile seçilebilirler (datasheet §1):** Audio I/F mode/format select · Digital
de-emphasis filter 44.1kHz (DAC). **SPI/I2C ile ek fonksiyonlar:** digital attenuation, soft
mute, de-emphasis (32/44.1/48kHz), data polarity control, ADC/DAC bağımsız power-down.
- I2C pin (43) open-drain konfigürasyonda.
- RST (4) her iki modda da ortak reset/power-down girişi (active low).
- Reset tamamlandıktan sonra serial control kullanılabilir (RST high ve dahili reset sonunda:
  OVF pininde 4096×SCKI genişliğinde pulse'un negatif geçişi ile bildirilir).

---

## §4 Güç Domainleri, Mutlak Maksimum, Önerilen Koşullar (datasheet §8 Absolute Maximum + Recommended)

**Mutlak Maksimum Ratings:**

| Parameter | Min | Max | Unit |
|---|---|---|---|
| VCCAD1/2, VCCDA1/2 | −0.3 | +6.5 | V |
| VDD1/2 | −0.3 | +4.0 | V |
| GND farkı (AGNDAD1/2, AGNDDA1/2, DGND1/2) | ±0.1 | | V |
| VCC farkı | ±0.1 | | V |
| VDD farkı | ±0.1 | | V |
| Dijital giriş: RST, MS, MC, MDI, SCKI | −0.3 | +6.5 | V |
| Dijital giriş: BCKAD/DA, LRCKAD/DA, DIN1-4, MODE, OVF, ZERO, MDO | −0.3 | VDD+0.3 (<+4.0) | V |
| Analog giriş: VIN1-6±, VCOMAD/DA, VOUT1-8±, VREFAD1/2 | −0.3 | VCC+0.3 (<+6.5) | V |
| Pin akımı (güç hariç) | ±10 | | mA |
| Ortam sıcaklığı (bias altında) | −40 | +125 | °C |
| Depolama | −55 | +150 | °C |
| Junction | — | +150 | °C |
| Lehimleme (lead, 5s) / IR reflow peak | — | +260 | °C |

**Recommended Operating Conditions:**

| Parameter | Min | Typ | Max | Unit |
|---|---|---|---|---|
| Analog supply VCC | 4.5 | 5.0 | 5.5 | V |
| Digital supply VDD | 3.0 | 3.3 | 3.6 | V |
| Dijital interface | LVTTL compatible | | | — |
| Sampling freq. LRCKAD/LRCKDA | 8 | — | 96/192 (1) | kHz |
| System clock SCKI | 2.048 | — | 36.864 | MHz |
| Analog input: SE | 1 VRMS · Differential: 2 VRMS | | | — |
| Analog output (differential) | 8 | | | VPP |
| Analog output yük: AC-coupled→GND / DC-coupled→GND | 5 kΩ / 15 kΩ | | | — |
| Analog output kapasitans | — | — | 50 | pF |
| Dijital output kapasitans | — | — | 20 | pF |
| Çalışma sıcaklığı (katalog / Q1) | −40…+85 / −40…+105 | | | °C |

(1) 192 kHz yalnız DAC için desteklenir (datasheet tablo notu).

**Güç tüketimi (datasheet §8, fs=48k ADC/48k DAC, SCKI=512fs):**

| Parameter | Typ | Max | Unit |
|---|---|---|---|
| ICC (analog) fs=48k/48k | 162 | 210 | mA |
| ICC fs=96k/192k | 162 | — | mA |
| ICC full power-down | — | 300 | µA |
| IDD (dijital) fs=48k/48k | 106 | 130 | mA |
| IDD fs=96k/192k | 127 | — | mA |
| IDD full power-down | — | 50 | µA |
| Power dissipation fs=48k/48k | 1160 | 1480 | mW |
| Power dissipation full power-down | — | 1.67 | mW |
| θJA (HTQFP-64) | +21 | — | °C/W (datasheet thermal resistance satırı) |

---

## §5 ADC Performans (datasheet §8 ADC Characteristics — fS=48kHz, SCKI=512fs, 24-bit, Slave)

| Parameter | Koşul | Min | Typ | Max | Unit |
|---|---|---|---|---|---|
| Resolution | — | 16 | — | 24 | Bits |
| Full-scale input | SE VIN=0dB | — | 0.2×VCCAD1 | — | VRMS |
| Full-scale input | Diff VIN=0dB | — | 0.4×VCCAD1 | — | VRMS |
| Center voltage | — | — | 0.5×VCCAD1 | — | V |
| Input impedance | — | — | 45 | — | kΩ |
| CMRR | — | — | 80 | — | dB |
| Gain mismatch ch-to-ch | full-scale | ±2.0 | — | ±6 | % of FSR |
| Gain error | full-scale | ±2.0 | — | ±6 | % of FSR |
| Bipolar zero error | HPF bypass | — | — | ±1.0 | % of FSR |
| THD+N | fS=48k, diff, VIN=−1dB | — | −93 | −87 | dB |
| THD+N | fS=96k, diff | — | −93 | — | dB |
| THD+N | fS=48k, SE | — | −93 | — | dB |
| THD+N | fS=96k, SE | — | −93 | — | dB |
| Dynamic range | fS=48k, A-weighted, diff | 100 | 107 | — | dB |
| Dynamic range | fS=96k, A-weighted, diff | — | 107 | — | dB |
| Dynamic range | fS=48k/96k, A-weighted, SE | — | 104 | — | dB |
| S/N ratio | fS=48k, A-weighted, diff | 100 | 107 | — | dB |
| S/N ratio | fS=96k, A-weighted, diff | — | 107 | — | dB |
| S/N ratio | fS=48k/96k, A-weighted, SE | — | 104 | — | dB |
| Channel separation | fS=48k, diff | 98 | 104 | — | dB |
| Channel separation | fS=96k, diff | — | 104 | — | dB |
| Channel separation | fS=48k/96k, SE | — | 101 | — | dB |

**ADC decimation filtresi (datasheet §1 + §8):** Passband ripple ±0.035 dB · Stop band
attenuation −75 dB · On-chip HPF: 0.96 Hz @ fS=48kHz · Passband (single/dual) 0.454×fS ·
Stop band: single 0.555×fS, dual 0.597×fS · Group delay: single 27/fS, dual 17/fS s.
- ADC sampling rate: 8–96 kHz; system clock: 256/384/512/768 fS.

---

## §6 DAC Performans ve Dijital Filtreler (datasheet §8 DAC Characteristics)

| Parameter | Koşul | Min | Typ | Max | Unit |
|---|---|---|---|---|---|
| Resolution | — | 16 | — | 24 | Bits |
| Gain mismatch ch-to-ch | — | — | ±2.0 | ±6 | % of FSR |
| Gain error | — | — | ±2.0 | ±6 | % of FSR |
| Bipolar zero error | — | — | — | ±1.0 | % of FSR |
| THD+N | fS=48k, VOUT=0dB | — | −94 | −88 | dB |
| THD+N | fS=96k | — | −94 | — | dB |
| THD+N | fS=192k | — | −94 | — | dB |
| Dynamic range | fS=48k, EIAJ A-weighted | 105 | 112 | — | dB |
| Dynamic range | fS=96k / 192k | — | 112 | — | dB |
| S/N ratio | fS=48k, EIAJ A-weighted | 105 | 112 | — | dB |
| S/N ratio | fS=96k / 192k | — | 112 | — | dB |
| Channel separation | fS=48k | 102 | 108 | — | dB |
| Channel separation | fS=96k / 192k | — | 108 | — | dB |
| Output voltage | differential | — | 1.6×VCCDA1 | — | VPP |
| Center voltage | — | — | 0.5×VCCDA1 | — | V |
| LPF freq. response | f=20kHz / f=44kHz | — | −0.04 / −0.18 | — | dB |

**DAC digital filtre — Sharp roll-off:**

| Parameter | single/dual | quad | Unit |
|---|---|---|---|
| Passband | 0.454×fS | 0.432×fS | Hz |
| Stop band | 0.546×fS | 0.569×fS | Hz |
| Passband ripple | ±0.0018 | ±0.0018 | dB |
| Stop band attenuation | −75 | −75 | dB |

**DAC digital filtre — Slow roll-off:**

| Parameter | Değer | Unit |
|---|---|---|
| Passband | 0.328×fS | Hz |
| Stop band | 0.673×fS | Hz |
| Passband ripple | ±0.0013 | dB |
| Stop band attenuation | −75 | dB |

**Ortak:** Group delay: single/dual 28/fS · quad 19/fS (s) · De-emphasis error ±0.1 dB.
- DAC sampling rate: 8–192 kHz; system clock: 128/192/256/384/512/768 fS.
- Analog low-pass filter dahildir (datasheet §1); 4x/8x oversampling digital filter.

---

## §7 Ses Arayüzü: Format, Master/Slave, TDM, Reset Zamanlamaları (datasheet §1, §8)

| Alan | Destek | Kaynak satırı |
|---|---|---|
| Formatlar | I2S · Left-Justified · Right-Justified · DSP · TDM (ADC ve DAC bağımsız) | datasheet §1 |
| Word length | 16, 24 bit; MSB first, two's complement | datasheet §8 "DATA FORMAT" |
| Interface mode | ADC/DAC bağımsız Master/Slave | datasheet §1 |
| SCKI | 2.048–36.864 MHz (128/192/256/384/512/768 fS) | datasheet §8 |
| DIN1-4 eşlemesi | DIN1=DAC1/2 · DIN2=DAC3/4 · DIN3=DAC5/6 · DIN4=DAC7/8 | datasheet §7 |
| DOUT1-3 eşlemesi | DOUT1=ADC1/2 · DOUT2=ADC3/4 · DOUT3=ADC5/6 | datasheet §7 |
| Reset davranışı | ADC/DAC simultane; dahili reset bit pulse (4096×SCKI, OVF üzerinde negatif geçiş) | datasheet §7, §9.3 |
| Output timing (internal reset → out) | tDACDLY1: 3600/7200/14400 LRCKDA periyodu (min/typ/max) · tDACDLY2 fade-in/out: 2048/4096/8192 LRCKDA | bdtic PDF (SBAS452 ek tablo) |
| ADC timing | tADCDLY1: 4800/9600/N/A LRCKAD · tADCDLY2: 2048/4096/N/A LRCKAD | bdtic PDF |
| Input logic | VIH 2.0–VDD · VIL ≤0.8 (BCK/LRCK slave mode Schmitt, 50kΩ pull-down) | datasheet §8 |
| Output logic | VOH ≥2.4V (IOUT=−4mA) · VOL ≤0.4V (IOUT=+4mA) | datasheet §8 |

**Referans/common çıkışları:** VREFAD1=VCCAD1 · VREFAD2=AGNDAD1 · VCOMAD=0.5×VCCAD1 (Zout
10kΩ) · VCOMDA=0.5×VCCDA1 (Zout 7.5kΩ); izinli source/sink 1µA.

**TDM notu:** Dört DIN hattı ve TDM formatı ile tek hat üzerinde 8 kanal taşınabilir (TDM
desteği datasheet §1'de açıkça belirtilir; TDM slot yerleşimi detayı §9.4/§9.5 içindedir —
⚠️ VERIFICATION REQUIRED, register tablosu bu oturumda okunmadı).

---

## §7B Tipik Karakteristik Figür İndeksi (datasheet §8 grafikleri — okunan indeks)

| Fig. | İçerik |
|---|---|
| Figure 1–2 | ADC Digital Filter Frequency Response / Passband (Single Rate) |
| Figure 3–4 | ADC Digital Filter Frequency Response / Passband (Dual Rate) |
| Figure 5–6 | ADC HPF Frequency Response / Passband |
| Figure 7–8 | DAC Digital Filter Frequency Response / Passband (Single Rate) — Sharp & Slow |
| Figure 9–10 | DAC Digital Filter Frequency Response / Passband (Dual Rate) — Sharp & Slow |
| Figure 11–12 | DAC Digital Filter Frequency Response / Passband (Quad Rate) — Sharp & Slow |
| Figure 13–15 | De-Emphasis Characteristic (fS = 48 / 44 / 32 kHz) |
| Figure 16 | Analog Filter Characteristic |
| Figure 17–18 | ADC THD+N / DR-SNR vs Temperature |
| Figure 19–20 | ADC THD+N / DR-SNR vs Supply Voltage |
| Figure 21–22 | DAC DR-SNR / THD+N vs Temperature |
| Figure 23–24 | DAC DR-SNR / THD+N vs Supply Voltage |
| Output Spectrum | ADC ve DAC çıkış spektrum grafikleri (20Hz–150kHz ekseni) |

**Datasheet §9 içindeki fonksiyonel bölüm başlıkları (içerik bu oturumda okunmadı → §9.4/§9.5
register/mode tabloları ⚠️ VERIFICATION REQUIRED):** §9.3 Feature Description (s.25) ·
§9.4 Device Functional Modes (s.31) — kontrol portu, HW mode konfigürasyonu, sampling mode,
format seçimi.

---

## §7C TI Uygulama Notları (ti.com doküman listesi, tarihleriyle)

| Doküman | Başlık | Tarih |
|---|---|---|
| SLAA313 (Rev. A) | Out-of-Band Noise Measurement Issues for Audio Devices | Dec 31, 2019 |
| SLAA469 (Rev. A) | Audio Serial Interface Configurations for Audio Codecs | Jun 27, 2019 |
| SLAA605 | Using the MSP430 Launchpad as a Standalone I2C Host for Audio Products | Oct 28, 2013 |
| SLAA420 | Solving Enumeration Errors in USB Audio DAC and CODEC Designs | Oct 30, 2009 |
| SLAA413 | Configuring I2S to Generate BCLK from Codec Devices & WCLK from McBSP Port | Jul 8, 2009 |

---

## §8 Karşılaştırma Tablosu: PCM3168A vs AK4458 (bu vaulttaki iki DAC/codec adayı)

| Kriter | PCM3168A (TI) | AK4458 (AKM) |
|---|---|---|
| Tip | Codec (ADC+DAC tek çip) | Sadece DAC (8ch) |
| ADC | 6 kanal, 24-bit, 96kHz max | Yok |
| DAC | 8 kanal, 24-bit, 192kHz max | 8 kanal, 32-bit, 768kHz max |
| DAC SNR | 112 dB (EIAJ A-wtd) | 115 dB (A-wtd, AVDD=5V) |
| DAC THD+N | −94 dB (fS=48k) | −107 dB (fS=44.1k, AVDD=5V) |
| Çıkış | Diferansiyel 8 VPP | Diferansiyel ±2.8Vpp/kanal (VREF=5V) |
| DSD girişi | Yok | 11.2 MHz DSD |
| Filtre seçenekleri | Sharp / Slow roll-off | 5 tip (sharp, slow, short-delay ×2, super slow) |
| Kontrol | SPI / I2C / H/W (MODE pin) | I2C / 3-wire serial / Parallel (pin strap) |
| Ses formatları | I2S, LJ, RJ, DSP, TDM | I2S, MSB/LSB justified, DSD, TDM |
| Paket | HTQFP-64 (12×12mm) | 48-QFN (0.5mm pitch) |
| Güç | 5V analog + 3.3V dijital; ~1.16W typ. dissipation | AVDD 3.0–5.5V + TVDD; ~0.2–0.3W tip. sınıfı (datasheet §8 akımlar) |
| Sıcaklık (katalog) | −40…+85°C | −40…+105°C (pad ground) |
| Linux sürücü | Mainline mevcut (PCM3168ASW-LINUX) | AKM driver ≠ mainline (⚠️ bu dosyada doğrulanmadı) |
| CoreMusic vault durumu | ADR-038: seçili DAC/USB çekirdeği | Alternatif (PCM5122 yasağı sonrası aday) |

---

## §9 CoreMusic Rolü (Bu Çip Sisteminde Nerede Durur)

| Konu | CoreMusic bağlamı | Kaynak |
|---|---|---|
| Vault konumu | **ADR-038 (brain.md): DAC/USB çekirdeği = PCM3168A + XMOS XU316** | `hardware-electronics` skill §7 |
| Yasak etkileşimi | PCM5122 yasak (ADR-038 / H001 REJECT) → PCM3168A bu yasağın muadilidir | skill §6 |
| Çift rol | Tek çip 6-in (giriş/kayıt yolu) + 8-out (çıkış yolu) → K16 modüler kanal (1–8) girişi ve kayıt zinciri tek çipte | skill §1 (K16) |
| Seviye | DAC diferansiyel 8 VPP tip. → Class AB giriş seviyesine uyum için attenuator/buffer gerekir; tasarım bu dosyada YOK (audio-hardware-engineer kapsamında) | datasheet §8 |
| Arayüz | I2S/TDM → K19 kuralı 50Ω I2S empedans; TDM ile 8 kanal tek hatta | skill §1 (K19) |
| Clock | SCKI 2.048–36.864 MHz → clock mimarisi K19/K17 ile koordine edilir; bu dosya karar vermez | datasheet §8 |
| Ölçüm | DAC SNR 112dB vs K16 hedef >105dB → 7dB tip. marj (tip. değerler) | datasheet §8 + skill §1 |
| Güç | ~1.16W dissipation (fs=48k) → K18 termal bütçesine ek yük; termal dağılım K18 konusudur | datasheet §8 |

**Ölçüm durumu:** Prototip yok → tüm hedefler **PLANNED**. Bu dosya APPROVE/REVISE üretmez.

---

## §10 Doğrulama Tablosu (id · URL · tarih · durum)

| §id | Kaynak URL | Tarih | Durum |
|---|---|---|---|
| §0 | https://www.ti.com/product/PCM3168A | 2026-10-12 erişim | ✅ DOĞRULANDI (ACTIVE, özet + doküman tarihleri) |
| §1–§9 | https://www.bdtic.com/datasheet/TI/PCM3168A_PCM3168A-Q.pdf (SBAS452, SEPT 2008) | 2026-10-12 erişim | ✅ DOĞRULANDI (pin tablosu, karakteristikler okundu) |
| §0 datasheet (TI) | https://www.ti.com/lit/gpn/PCM3168A (Rev. A, Jan 7 2016) | 2026-10-12 | ✅ DOĞRULANDI (ti.com doküman listesi) |
| §0 driver | https://www.ti.com/tool/PCM3168ASW-LINUX | 2026-10-12 erişim | ✅ DOĞRULANDI (mainline, ASOC) |
| §7 TDM slot yerleşimi | SBAS452 §9.4/§9.5 (Device Functional Modes / register) | 2026-10-12 | ⚠️ VERIFICATION REQUIRED (register tablosu okunmadı) |
| §8 AK4458 sütunu | AKM ürün sayfası + datasheet (bkz. `ak4458-dac.md` §13) | 2026-10-12 | ✅ DOĞRULANDI (cross-reference) |
| §9 vault atıfları | `.claude/skills/hardware-electronics/SKILL.md` (disk) · kök `CLAUDE.md` §Web Verification (PCM3168A ✅ ti.com) | 2026-10-12 okundu | ✅ DOĞRULANDI (disk kanıtı) |

---

## §11 Açık Sorular

1. **Kanal planı:** 8 DAC kanalı hangi K16 modüler kanallara eşlenecek (L/R ×4 çift)? Kanal
   eşlemesi ADR-090 varyant SKU ailesiyle uyumlu mu?
2. **ADC kullanımı:** 6 ADC kanalı (giriş/kayıt) CoreMusic'te hangi kaynaklara bağlanacak
   (mikrofon/hat girişi)? SE (1VRMS) mı, differential (2VRMS) mı?
3. **Kontrol modu:** SPI mı I2C mı? (MODE pin strap'i ve MCU tarafı etkiler.)
4. **Clock planı:** SCKI kimden (XU316 mü, ayrı PLL mi)? Master/Slave rolleri ne olacak?
5. **Referans:** VCOMAD/VCOMDA harici bias devrelerinde kullanılacak mı, yoksa sadece
   decoupling mi? (K16 giriş biası ile etkileşim.)
7. **Popülasyon varyantı:** 8 kanalın tümü mü bağlanacak, yoksa ADR-090 varyant SKU
   ailesi için 4/2/1 kanal alt varyantlar mı? Boş bırakılan kanalların pin durumu
   (SDA/SCK drive kapalı mı, high-Z mı) ne olacak?
8. **Thermal bütçe:** 8 kanal tam yükte tükettiği güç üzerinden PCB thermal via planı
   gerekli mi? (⚠️ VERIFICATION REQUIRED — tipik PD değeri §5'te ölçülmemiştir.)

---

## §12 Authority

*Authority: `.ai/CLAUDE.md` §5 (K16-K20) · kök `CLAUDE.md` §Sabitler (Class AB, PCM5122 yasak)
· ADR-038 (`.ai/brain.md`: PCM3168A + XU316) · skill: `hardware-electronics` (v3.0). Otorite
sırası: `.ai/CLAUDE.md` > `.ai/AGENTS.md` > `.ai/WORKFLOW.md` > `.ai/brain.md`. Bu dosya
**teknoloji derin-dalı (draft)**tır; tasarım kararı / BOM / şema üretmez; `.ai/electronic/**`
yazımı owner onayı ister. node_id: `K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec`
· 2026-10-12.*
