---
title: "BOM - Bill of Materials"
layer: K16
category: "Class AB Amplifikatör"
date: 2026-09-20
---

# BOM - Bill of Materials

## Genel Bakış

Bu doküman, K16 Class AB 8-kanal amplifikatörün tüm bileşenlerini, seçim kriterlerini ve tedarikçi bilgilerini içerir. Her bileşen, ses kalitesi, güvenilirlik ve maliyet dengesi göz önünde bulundurularak seçilmiştir.

## Aktif Bileşenler

### Güç Transistörleri (Her Kanal)

| Ref | Bileşen | Tanım | Paket | Package | Adet/kanal | Toplam | Birim ($) | Toplam ($) |
|---|---|---|---|---|---|---|---|---|
| Q9 | MJL21194 | NPN 250W 250V 16A | TO-264 | - | 1 | 8 | 8.50 | 68.00 |
| Q10 | MJL21193 | PNP 250W 250V 16A | TO-264 | - | 1 | 8 | 8.50 | 68.00 |
| Q11a | BD139 | NPN 80V 1.5A driver | TO-126 | - | 1 | 8 | 0.45 | 3.60 |
| Q11b | BD140 | PNP 80V 1.5A driver | TO-126 | - | 1 | 8 | 0.45 | 3.60 |

### Küçük Sinyal Transistörleri

| Ref | Bileşen | Tanım | Paket | Adet/kanal | Toplam | Birim ($) | Toplam ($) |
|---|---|---|---|---|---|---|---|
| Q1,Q2 | 2SA1015 | PNP low-noise | TO-92 | 2 | 16 | 0.12 | 1.92 |
| Q3-Q4 | 2N5551 | NPN 160V | TO-92 | 2 | 16 | 0.10 | 1.60 |
| Q7 | 2N5551 | NPN VAS | TO-92 | 1 | 8 | 0.10 | 0.80 |
| Q8 | 2N5551 | NPN Vbe Mult | TO-92 | 1 | 8 | 0.10 | 0.80 |
| Q13-Q14 | BC547 | NPN general purpose | TO-92 | 2 | 16 | 0.08 | 1.28 |
| Q15-Q16 | BC547/557 | NPN/PNP current sense | TO-92 | 2 | 16 | 0.08 | 1.28 |
| Q17-Q18 | BD139/140 | SOA limiter | TO-126 | 2 | 16 | 0.45 | 7.20 |

### Diod

| Ref | Bileşen | Tanım | Package | Adet/kanal | Toplam | Birim ($) | Toplam ($) |
|---|---|---|---|---|---|---|---|
| D1 | 1N4148 | Flyback protection | DO-35 | 1 | 8 | 0.02 | 0.16 |
| D2-D3 | 1N4007 | Reverse protection | DO-41 | 2 | 16 | 0.05 | 0.80 |
| D4 | BZX55C33 | 33V Zener clamp | DO-35 | 1 | 8 | 0.15 | 1.20 |
| D5 | LED | Power indicator | 3mm | 1 | 8 | 0.10 | 0.80 |

### Koruma

| Ref | Bileşen | Tanım | Package | Adet/kanal | Toplam | Birim ($) | Toplam ($) |
|---|---|---|---|---|---|---|---|
| KSD301 | KSD301 | 85°C NC thermostat | Panel mount | 1 | 8 | 1.50 | 12.00 |
| RL1 | G2R-1 | 30A SPDT relay 12V | DIP | 1 | 8 | 4.50 | 36.00 |
| F1-F2 | Bel Fuse | 3A/5A slo-blow | Cartridge | 2 | 16 | 0.30 | 4.80 |

## Pasif Bileşenler

### Dirençler (Metal Film %1)

| Ref | Değer | Güç | Package | Adet/kanal | Toplam | Birim ($) | Toplam ($) |
|---|---|---|---|---|---|---|---|
| R_sense | 0.22Ω | 5W | Axial | 1 | 8 | 0.80 | 6.40 |
| Rf | 26kΩ | 1/4W | Axial | 1 | 8 | 0.05 | 0.40 |
| Rg | 1kΩ | 1/4W | Axial | 1 | 8 | 0.05 | 0.40 |
| R_in | 47kΩ | 1/4W | Axial | 1 | 8 | 0.05 | 0.40 |
| R_tail | 100Ω | 1/4W | Axial | 1 | 8 | 0.05 | 0.40 |
| R_ref | 33kΩ | 1/4W | Axial | 1 | 8 | 0.05 | 0.40 |
| R7 | 10kΩ | 1/4W | Axial | 1 | 8 | 0.05 | 0.40 |
| R8 | 100Ω | 1/4W | Axial | 1 | 8 | 0.05 | 0.40 |
| R9 | 2.2kΩ | 1/4W | Axial | 1 | 8 | 0.05 | 0.40 |
| R11-R12 | 0.22Ω | 5W | Axial | 2 | 16 | 0.80 | 12.80 |
| R13 | 100Ω | 1/4W | Axial | 1 | 8 | 0.05 | 0.40 |
| R15-R16 | 10kΩ | 1/4W | Axial | 2 | 16 | 0.05 | 0.80 |
| R17-R18 | 10kΩ | 1/4W | Axial | 2 | 16 | 0.05 | 0.80 |
| R19 | 100Ω | 1W | Axial | 1 | 8 | 0.10 | 0.80 |
| R21-R24 | 470Ω-10kΩ | 1/4W | Axial | 4 | 32 | 0.05 | 1.60 |
| R25-R26 | 4.7kΩ | 1/4W | Axial | 2 | 16 | 0.05 | 0.80 |
| R27-R29 | 10-220Ω | 1/4W | Axial | 3 | 24 | 0.05 | 1.20 |
| R30-R31 | 0.1Ω | 2W | Axial | 2 | 16 | 0.20 | 3.20 |

### Kapasitörler

| Ref | Değer | Volt | Tip | Package | Adet/kanal | Toplam | Birim ($) | Toplam ($) |
|---|---|---|---|---|---|---|---|---|
| Cc | 100pF | 50V | C0G/NP0 | Axial | 1 | 8 | 0.10 | 0.80 |
| Cf | 10pF | 50V | C0G/NP0 | Axial | 1 | 8 | 0.10 | 0.80 |
| C_in | 10μF | 50V | Nichicon Muse | Radial | 1 | 8 | 1.20 | 9.60 |
| C1 | 1μF | 50V | Nichicon FW | Radial | 1 | 8 | 0.50 | 4.00 |
| C2 | 100nF | 50V | Ceramic X7R | Radial | 1 | 8 | 0.05 | 0.40 |
| C3-C6 | 100μF | 50V | Nichicon FW | Radial | 4 | 32 | 1.00 | 32.00 |
| C_bias | 10μF | 25V | Nichicon | Radial | 1 | 8 | 0.50 | 4.00 |

### Güç Kaynağı Kapasitörleri

| Ref | Değer | Volt | Tip | Package | Adet | Birim ($) | Toplam ($) |
|---|---|---|---|---|---|---|---|
| C_F1-C_F4 | 10,000μF | 50V | Nichicon KG | Snap-in | 8 | 15.00 | 120.00 |
| C_byp1-8 | 100μF | 50V | Nichicon FW | Radial | 16 | 1.00 | 16.00 |
| C_HF1-16 | 100nF | 50V | Ceramic X7R | Radial | 32 | 0.05 | 1.60 |

### Potansiyometre

| Ref | Değer | Tip | Package | Adet/kanal | Toplam | Birim ($) | Toplam ($) |
|---|---|---|---|---|---|---|---|
| Rb | 200Ω | Bourns 3296W | Trimpot | 1 | 8 | 0.60 | 4.80 |

## Mekanik Bileşenler

| Bileşen | Tanım | Adet | Birim ($) | Toplam ($) |
|---|---|---|---|---|
| Heatsink | 100×60×40mm Al extruded | 8 | 25.00 | 200.00 |
| Fan | 60mm 12V 20CFM | 8 | 8.00 | 64.00 |
| Toroid trafo | 500VA 2×25V | 1 | 180.00 | 180.00 |
| Bridge rect | KBPC3510 35A | 2 | 12.00 | 24.00 |
| PCB | 8-layer FR4 | 1 | 150.00 | 150.00 |
| Connectors | Speaker binding post | 16 | 1.50 | 24.00 |
| Connectors | RCA input | 8 | 0.80 | 6.40 |
| Hardware | M3 screws, standoffs | 1 kit | 15.00 | 15.00 |
| Thermal compound | Arctic MX-6 4g | 2 | 8.00 | 16.00 |
| Thermal pad | Sil-Pad 1500 | 16 | 0.80 | 12.80 |
| Chassis | 4U rackmount | 1 | 120.00 | 120.00 |
| Power switch | DPST 20A | 1 | 5.00 | 5.00 |
| IEC inlet | With fuse holder | 1 | 3.00 | 3.00 |
| Wire | 14AWG silicone | 5m | 2.00 | 10.00 |

## Maliyet Özeti

| Kategori | Toplam ($) |
|---|---|
| Güç transistörleri | 143.20 |
| Küçük sinyal transistörleri | 12.88 |
| Diodlar | 2.96 |
| Koruma bileşenleri | 52.80 |
| Dirençler | 30.80 |
| Kapasitörler (kanal) | 51.60 |
| Güç kaynağı kapasitörleri | 137.60 |
| Potansiyometreler | 4.80 |
| Mekanik bileşenler | 630.80 |
| **Toplam (8 kanal)** | **1,067.44** |
| **Kanal başına** | **133.43** |

## Tedarikçi Listesi

| Tedarikçi | Ürünler | URL |
|---|---|---|
| Mouser | Transistör, diyot, pasif | mouser.com |
| Digi-Key | Kapasitör, PCB | digikey.com |
| Farnell | Heatsink, mekanik | farnell.com |
| Toroid India | Toroid trafolar | toroid.in |
| Bourns | Potansiyometreler | bourns.com |
| Nichicon | Ses kapasitörleri | nichicon.co.jp |

## Bileşen Seçim Kriterleri

### Transistör Seçimi
- **MJL21194/93**: OnSemi, TO-264, 250W, audio grade
- Eşleştirme: hFE %5 tolerans içinde
- Alternatif: 2SC5200/2SA1943 (Toshiba)

### Kapasitör Seçimi
- **Nichicon KG/KZ**: Audio grade, low ESR
- Ripple akımı: ≥5A @ 100kHz
- Ömür: 2000 saat @ 105°C

### Direnç Seçimi
- Metal film %1 tolerans
- Düşük termal drift (50ppm/°C)
- Axial mount (through-hole)

## Durum: Implementasyon
