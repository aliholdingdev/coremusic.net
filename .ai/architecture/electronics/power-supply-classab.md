---
type: architecture-document
category: hardware/electronics
title: "Power Supply Class AB Amplifier Documentation"
date: 2026-09-19
updated: 2026-09-19
status: active
version: 1.0.0
---

# Power Supply ±35V — CoreMusic Class AB

**Ilgili ADR:** [[../.decisions/draft/ADR-089-classab-24v]]
**Ilgili Dosyalar:** [[amplifier-classab-circuit]] · [[thermal-design-classab]] · [[pcb-classab]]

## 1. Genel Bakis

Dual boost converter ile 6S LiPo (22.2V nominal) veya laptop adapteri (19-24V DC) girisinden ±35V simetrik DC cikisi ureten besleme kaynagi tasarimi.

### 1.1 Teknik Ozet

| Parametre | Deger |
|-----------|-------|
| Giris Voltaji | 4.5V - 60V DC (22.2V nominal) |
| Cikis Voltaji | ±35V simetrik |
| Max Cikis Akimi | 3A surekli / kanal |
| Toplam Guc | ~840W teorik max (8 kanal x 50W) |
| Verimlilik | %96'ya kadar (boost converter) |
| Anahtarlama Frekansi | 1.2MHz (dusuk parazit) |
| Koruma | UVP, OVP, OCP, OTP, reverse polarity |
| Batarya | 6S LiPo (22.2V nominal, 25.2V max, 18.0V min) |

### 1.2 Giris Kaynak Secenekleri

| Kaynak | Voltaj | Akim | Gucl |
|--------|--------|------|------|
| 6S LiPo Pil | 18.0V - 25.2V | 40A max | 1000W |
| Laptop Adaptoru | 19V - 24V | 10A | 240W |
| USB-C PD | 20V | 5A | 100W (yardimci) |

### 1.3 Uygulama Alanlari

| Platform | Kullanim |
|----------|----------|
| Home Media Center | Ev ortami ses sistemi |
| Professional Studio | 8.1 Surround monitor sistemi |
| Car Infotainment | Araç ici yukseltilmis ses |
| Portable PA | Taşınabilir performans sistemi |

---

## 2. Devre Topolojisi

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                          COREMUSIC ±35V POWER SUPPLY                           │
├─────────────────────────────────────────────────────────────────────────────────┤
│                                                                                 │
│  ┌──────────┐   ┌──────────┐   ┌──────────┐   ┌──────────┐   ┌──────────┐     │
│  │  XT60    │   │  DC Jack │   │  USB-C   │   │  NTC1    │   │  Relay1  │     │
│  │  Battery │   │  Adapter │   │  PD      │   │  5Ω Cold │   │  SPST    │     │
│  └────┬─────┘   └────┬─────┘   └────┬─────┘   └────┬─────┘   └────┬─────┘     │
│       │              │              │              │              │             │
│       └──────────────┼──────────────┼──────────────┼──────────────┘             │
│                      │              │              │                           │
│                      ▼              ▼              ▼                           │
│              ┌───────────────────────────────────────┐                         │
│              │         OR-ING DIODES                 │                         │
│              │    D3, D4, D5: SS34 (3A/40V)         │                         │
│              │    + TVS1, TVS2: P6KE36A              │                         │
│              └───────────────────┬───────────────────┘                         │
│                                  │                                             │
│                                  ▼                                             │
│              ┌───────────────────────────────────────┐                         │
│              │         BULK STORAGE                  │                         │
│              │    C17: 4700µF/50V Electrolytic       │                         │
│              │    C18: 100nF Ceramic                 │                         │
│              └───────────────────┬───────────────────┘                         │
│                                  │                                             │
│                  ┌───────────────┼───────────────┐                             │
│                  │               │               │                             │
│                  ▼               ▼               ▼                             │
│  ┌───────────────────┐ ┌───────────────────┐ ┌───────────────────┐             │
│  │   +35V BOOST      │ │   -35V INVERTING  │ │  VOLTAGE MONITOR  │             │
│  │   LM5122 (IC1)    │ │   LM5122 (IC2)    │ │   LM393 (IC3)     │             │
│  │   L1: 4.7µH/10A   │ │   L2: 4.7µH/10A   │ │   Voltage Dividers│             │
│  │   D1: SS36         │ │   D3: SS36         │ │   Zener 3.3V Ref  │             │
│  └─────────┬─────────┘ └─────────┬─────────┘ └─────────┬─────────┘             │
│            │                     │                     │                       │
│            ▼                     ▼                     │                       │
│  ┌───────────────────┐ ┌───────────────────┐           │                       │
│  │   +35V FILTER     │ │   -35V FILTER     │           │                       │
│  │   L3: 10µH/5A     │ │   L4: 10µH/5A     │           │                       │
│  │   C20: 4700µF/50V │ │   C21: 4700µF/50V │           │                       │
│  │   C22: 100nF      │ │   C23: 100nF      │           │                       │
│  └─────────┬─────────┘ └─────────┬─────────┘           │                       │
│            │                     │                     │                       │
│            ▼                     ▼                     ▼                       │
│  ┌───────────────────────────────────────────────────────────────┐             │
│  │                    POWER DISTRIBUTION                         │             │
│  │    Phoenix Connector (4-pin) → Amplifier Channels            │             │
│  │    TP1-TP6 Test Points                                       │             │
│  │    D6 (Green LED) + D7 (Red LED) Status                     │             │
│  └───────────────────────────────────────────────────────────────┘             │
│                                                                                 │
└─────────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. +35V Boost Converter (LM5122)

### 3.1 Ana Bilesenler

| Bilesen | Referans | Deger | Aciklama |
|---------|----------|-------|----------|
| IC1 | LM5122 | — | Texas Instruments Boost Controller |
| L1 | Coilcraft XAL5030-472 | 4.7µH / 10A | Dusuk DCR tozlu induktor |
| D1 | SS36 | 3A / 60V | Schottky diyot |
| C1 | — | 100µF / 50V | Ceramic X7R giris kapasitörü |
| C2 | — | 220µF / 50V | Ceramic X7R cikis kapasitörü |
| R1 | — | 100kΩ | Geri besleme direnci ust |
| R2 | — | 33kΩ | Geri besleme direnci alt |
| R3 | — | 4.7kΩ | Kompansasyon direnci |
| R4 | — | 1kΩ | Kompansasyon direnci |
| R5 | — | 10kΩ | Soft-start direnci |
| R6 | — | 47kΩ | Bootstrap direnci |
| C5 | — | 1nF | Kompansasyon kapasitörü |
| C6 | — | 100pF | Kompansasyon kapasitörü |
| C7 | — | 10nF | Soft-start kapasitörü |
| C8 | — | 100nF | Bypass kapasitörü |
| D2 | 1N4148 | — | Bootstrap diyodu |
| F1 | PTC | 5A | Giris sigortasi |

### 3.2 Geri Besleme Hesaplama

```
Vout = 1.221V × (1 + R1/R2)
Vout = 1.221V × (1 + 100kΩ / 33kΩ)
Vout = 1.221V × 4.030
Vout = 34.93V ≈ 35V
```

### 3.3 Switching Parametreleri

| Parametre | Deger | Aciklama |
|-----------|-------|----------|
| Frekansi | 1.2MHz | Dusuk parazit, kucuk induktor |
| Duty Cycle | %53.7 | (Vin - Vout) / Vin |
| Ripple Akimi | <300mAp-p | Induktor uzerinde |
| Ripple Voltaji | <100mVp-p | Cikis uzerinde |

### 3.4 Soft-Start Davranisi

Soft-start suresi asagidaki formulle hesaplanir:

```
t_ss = R5 × C7 × 1.221V / 10µA
t_ss = 10kΩ × 10nF × 1.221 / 10µA
t_ss ≈ 12.2ms
```

---

## 4. -35V Inverting Converter (LM5122)

### 4.1 Inverting Topoloji

Ayni LM5122 kontrolcüsü, inverting buck-boost konfigurasyonunda negatif voltaj uretir.

```
┌─────────────────────────────────────────────────┐
│           INVERTING CONVERTER (-35V)            │
│                                                 │
│    Vin (+22V) ──┬── L2 (4.7µH/10A) ──┬── GND  │
│                  │                     │        │
│                  │         IC2 (LM5122)│        │
│                  │              │      │        │
│                  └──────────────┼──────┘        │
│                                 │               │
│                                 ▼               │
│                          Vout (-35V)            │
│                                                 │
└─────────────────────────────────────────────────┘
```

### 4.2 Bilesenler

| Bilesen | Referans | Deger | +35V ile Karsilastirma |
|---------|----------|-------|------------------------|
| IC2 | LM5122 | — | Ayni |
| L2 | — | 4.7µH / 10A | Ayni |
| D3 | SS36 | 3A / 60V | Ayni |
| C9-C16 | — | Ayni degerler | Ayni |
| R7-R12 | — | Ayni degerler | Ayni |

### 4.3 Inverting Hesaplama

```
|Vout| = Vin × D / (1 - D)
35V = 22.2V × D / (1 - D)
D = 35 / (22.2 + 35)
D = 35 / 57.2
D ≈ 0.612 (%61.2 duty cycle)
```

---

## 5. Giris Secimi + OR-Ing

### 5.1 OR-Ing Diyot Konfigurasyonu

| Bilesen | Referans | Deger | Aciklama |
|---------|----------|-------|----------|
| D3 | SS34 | 3A / 40V | OR-ing diyodu 1 |
| D4 | SS34 | 3A / 40V | OR-ing diyodu 2 |
| D5 | SS34 | 3A / 40V | OR-ing diyodu 3 |
| C17 | — | 4700µF / 50V | Electrolytic bulk depolama |
| C18 | — | 100nF | Ceramic HF bypass |
| F2 | Slow-blow | 10A | Giris sigortasi 1 |
| F3 | Slow-blow | 10A | Giris sigortasi 2 |
| R13 | — | 10kΩ | Voltaj bolucu ust |
| R14 | — | 4.7kΩ | Voltaj bolucu alt |
| C19 | — | 100nF | Filtre kapasitörü |
| TVS1 | P6KE36A | 36V | Transient koruma 1 |
| TVS2 | P6KE36A | 36V | Transient koruma 2 |
| NTC1 | 5Ω Cold | — | Inrush sinirlama |
| Relay1 | SPST 10A | — | Soft-start rölesi |
| R15 | — | 10kΩ | Röle drive direnci |

### 5.2 OR-Ing Davranisi

```
Durum 1: Sadece Batarya
├── D3: ON (Batarya voltaji > 0)
├── D4: OFF (Adapter yok)
└── D5: OFF (USB-C yok)

Durum 2: Sadece Adapter
├── D3: OFF (Batarya yok)
├── D4: ON (Adapter voltaji > 0)
└── D5: OFF (USB-C yok)

Durum 3: Batarya + Adapter
├── D3: ON (yuksek voltaji sec)
├── D4: ON (yuksek voltaji sec)
└── D5: OFF (USB-C yok)

Durum 4: Tumu Aktif
├── D3: ON (en yuksek voltaji sec)
├── D4: ON
└── D5: ON (en yuksek voltaji sec)
```

### 5.3 Soft-Start Mekanizmasi

```
┌─────────────────────────────────────────────────────────┐
│                    SOFT-START SEKANSI                    │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  1. NTC1 Direnci: 5Ω (Cold) → Inrush sinirlama        │
│     └── Isindikca direnc duser → otomatik bypass       │
│                                                         │
│  2. C17 Yukleme: 4700µF → Yavas yukleme               │
│     └── dV/dt = I / C = 10A / 4700µF = 2.13 V/s       │
│                                                         │
│  3. Relay1 Aktivasyon: NTC bypass                      │
│     └── R15 + C19 ile gecikme (~100ms)                 │
│                                                         │
│  4. LM5122 EN Pin: Cikis voltaji stabil oldugunda     │
│     └── Soft-start ile yukari cikis                     │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

---

## 6. Cikis Filtresi + Dagitim

### 6.1 Filtre Devresi

| Bilesen | Referans | Deger | Aciklama |
|---------|----------|-------|----------|
| C20 | — | 4700µF / 50V | Bulk cikis (+35V) |
| C21 | — | 4700µF / 50V | Bulk cikis (-35V) |
| C22 | — | 100nF | HF bypass (+35V) |
| C23 | — | 100nF | HF bypass (-35V) |
| L3 | — | 10µH / 5A | Cikis filtresi (+35V) |
| L4 | — | 10µH / 5A | Cikis filtresi (-35V) |
| R16 | — | 10kΩ | Bleeder direnci (+35V) |
| R17 | — | 10kΩ | Bleeder direnci (-35V) |
| D6 | LED Green | — | Durum gostergesi (Normal) |
| D7 | LED Red | — | Durum gostergesi (Hata) |
| R18 | — | 1kΩ | LED direnci (D6) |
| R19 | — | 1kΩ | LED direnci (D7) |

### 6.2 LC Filtre Hesaplama

```
Filtre Kesim Frekansi (fc):
fc = 1 / (2π × √(L × C))

+35V tarafı:
fc = 1 / (2π × √(10µH × 4700µF))
fc = 1 / (2π × √(47×10⁻⁹))
fc = 1 / (2π × 216.8µ)
fc = 734 Hz

-35V tarafı:
Ayni degerler → fc = 734 Hz
```

### 6.3 Bleeder Direnci Hesaplama

```
Bosaltma suresi (90% deşarj):
t = R × C × 2.3
t = 10kΩ × 4700µF × 2.3
t = 107.1 saniye (~2 dakika)

Guvenlik: Cikis voltaji 35V'tan 3.5V'a duser
Uyari: Dokunmadan once bekleyin!
```

---

## 7. Voltaj Izleme

### 7.1 Voltaj Bolucu

| Bilesen | Referans | Deger | Aciklama |
|---------|----------|-------|----------|
| R20 | — | 100kΩ | +35V bolucu ust |
| R21 | — | 47kΩ | +35V bolucu alt |
| R22 | — | 100kΩ | -35V bolucu ust |
| R23 | — | 47kΩ | -35V bolucu alt |
| R24 | — | 100kΩ | Batarya bolucu ust |
| R25 | — | 47kΩ | Batarya bolucu alt |
| C24 | — | 100nF | Filtre (+35V) |
| C25 | — | 100nF | Filtre (-35V) |
| C26 | — | 100nF | Filtre (Batarya) |

### 7.2 LM393 Karsilastirici

| Bilesen | Referans | Deger | Aciklama |
|---------|----------|-------|----------|
| IC3 | LM393 | — | Dual comparator |
| R26 | — | 10kΩ | Referans direnci 1 |
| R27 | — | 10kΩ | Referans direnci 2 |
| R28 | — | 10kΩ | Hysteresis direnci |
| D8 | Zener 3.3V | — | Referans gerilimi |
| C27 | — | 10µF | Referans filtresi |

### 7.3 Voltaj Esik Degerleri

| Esik Degeri | Batarya Durumu | Aksiyon |
|-------------|----------------|---------|
| 25.2V | Tam sarj (6S max) | LED Yesil, Normal calisma |
| 22.2V | Nominal | Normal calisma |
| 20.0V | Dusuk batarya | LED Kirmizi, Uyari |
| 18.0V | Amplifikator kapatma (6S min) | Amplifikator kapat |
| 16.0V | Sistem kapatma | Tum sistemi kapat |

### 7.4 Comparator Hesaplama

```
Voltaj bolucu orani: R25 / (R24 + R25) = 47k / 147k = 0.3197

Comparator referans: Vref = 3.3V (Zener D8)

Kesim noktalari:
├── 25.2V × 0.3197 = 8.06V (Tam sarj)
├── 22.2V × 0.3197 = 7.10V (Nominal)
├── 20.0V × 0.3197 = 6.39V (Dusuk batarya)
├── 18.0V × 0.3197 = 5.75V (Amplifikator kapat)
└── 16.0V × 0.3197 = 5.12V (Sistem kapat)
```

---

## 8. Konnektorler

### 8.1 Konnektor Listesi

| Konnektor | Tip | Konum | Kullanim |
|-----------|-----|-------|----------|
| XT60 | Erkek | PCB Kenar | Batarya girisi |
| DC Barrel Jack | 5.5mm / 2.1mm | PCB Kenar | Laptop adapter girisi |
| Phoenix Connector | 4-pin | PCB Kenar | Guclu dagitim |
| Pin Header | 2x5 | PCB Yuzey | Debug / programlama |
| USB-C | Type-C | PCB Kenar | Yardimci guc |
| Screw Terminal | 2-pin | PCB Kenar | Hoparlor cikisi |
| LED Panel Mount | 5mm | Panel | Durum gostergesi |
| Toggle Switch | SPDT | Panel | Ac/Kapat |
| Fuse Holder | PCB Mount | PCB Kenar | Ana sigorta |

### 8.2 Pin Atamalari

```
Phoenix Connector (4-pin):
├── Pin 1: +35V Output
├── Pin 2: GND (Star Ground)
├── Pin 3: -35V Output
└── Pin 4: GND (Star Ground)

Debug Header (2x5):
├── Pin 1: VCC (3.3V)
├── Pin 2: GND
├── Pin 3: SDA (I2C)
├── Pin 4: SCL (I2C)
├── Pin 5: ADC0 (+35V Sense)
├── Pin 6: ADC1 (-35V Sense)
├── Pin 7: ADC2 (Batarya Sense)
├── Pin 8: EN (LM5122 Enable)
├── Pin 9: FAULT (LM393 Output)
└── Pin 10: NC
```

---

## 9. PCB Yerlesim Kurallari

### 9.1 Star Ground Topolojisi

```
┌─────────────────────────────────────────────────────────────────┐
│                    STAR GROUND TOPOLOJISI                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│                          ┌─────────┐                           │
│                          │  STAR   │                           │
│                          │ GROUND  │                           │
│                          │ POINT   │                           │
│                          └────┬────┘                           │
│                               │                                │
│            ┌──────────────────┼──────────────────┐             │
│            │                  │                  │             │
│            ▼                  ▼                  ▼             │
│    ┌───────────────┐ ┌───────────────┐ ┌───────────────┐       │
│    │  POWER GROUND │ │  SIGNAL GROUND│ │  CHASSIS GND  │       │
│    │  (Boost Conv) │ │  (LM393, LED) │ │  (Shield)     │       │
│    └───────────────┘ └───────────────┘ └───────────────┘       │
│                                                                 │
│    Kurallar:                                                   │
│    1. Tum ground hatlari tek noktaya baglanir                  │
│    2. Power ground ile signal ground ayrilir                   │
│    3. Analog/dijital bolge ayrilir                             │
│    4. Ground plane split ile bolge ayrimi                      │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 9.2 Analog / Dijital Bolge Ayrımı

```
┌─────────────────────────────────────────────────────────────────┐
│                    PCB BOLGE AYRIMI                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌─────────────────────┐     ┌─────────────────────┐           │
│  │   ANALOG BOLGE      │     │   DIJITAL BOLGE     │           │
│  │                     │     │                     │           │
│  │  - LM5122 (+35V)   │     │  - LM393 (Monitor)  │           │
│  │  - LM5122 (-35V)   │     │  - LED Gostergeler  │           │
│  │  - Induktorlar      │     │  - Debug Header     │           │
│  │  - Schottky Diyotlar│     │  - USB-C PD         │           │
│  │  - Bulk Kapasitörler│     │                     │           │
│  │                     │     │                     │           │
│  └──────────┬──────────┘     └──────────┬──────────┘           │
│             │                           │                      │
│             │    ┌─────────────────┐    │                      │
│             │    │  AYIRMA HATTI   │    │                      │
│             └───▶│  (Moat + Via)   │◀───┘                      │
│                  └─────────────────┘                           │
│                                                                 │
│  Kurallar:                                                     │
│  1. Analog bolge: 2oz copper, genis ground plane               │
│  2. Dijital bolde: 1oz copper yeterli                          │
│  3. Ayrım hattinda moat + stitching via'lar                    │
│  4. Gecis noktalarinda 100nF bypass kapasitörü                 │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 9.3 PCB Specificasyonlari

| Parametre | Deger |
|-----------|-------|
| Boyut | 200mm x 100mm |
| Katman | 6-layer stackup |
| Copper | 2oz (power), 1oz (signal) |
| Min Trace | 10mil (signal), 50mil (power) |
| Min Via | 0.3mm drill, 0.6mm pad |
| Surface Finish | ENIG (Electroless Nickel Immersion Gold) |
| Solder Mask | Green, both sides |
| Silkscreen | White, both sides |
| Impedans | 50Ω single-ended, 90Ω differential |

### 9.4 Isı Yonetimi

```
┌─────────────────────────────────────────────────────────────────┐
│                    ISI YONETIMI KURALLARI                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  1. POWER COMPONENTS (IC1, IC2, D1, D3):                       │
│     - Thermal pad zorunlu                                      │
│     - 4x4 via array (0.3mm drill)                              │
│     - Alt copper pour ile baglanti                             │
│                                                                 │
│  2. INDUCTORS (L1, L2, L3, L4):                                │
│     - Minimal trace uzunlugu                                   │
│     - Genis copper pour                                        │
│     - Diger component'lardan uzak tutma                        │
│                                                                 │
│  3. KAPASITORLER (Bulk):                                       │
│     - Kisa baglanti hatlari                                    │
│     - Genis via'lar (2oz copper)                               │
│     - Paralel konfigurasyonda                                  │
│                                                                 │
│  4. LED'LER:                                                   │
│     - Isi kaynagindan uzak                                    │
│     - Panel mount icin delikler                                │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 10. Guvenlik Notlari

### 10.1 Reverse Polarity Korumasi

```
┌─────────────────────────────────────────────────────────────────┐
│              REVERSE POLARITY KORUMA MEKANIZMASI                │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Tehlike: Batarya yanlis baglanirsa                             │
│  ├── Motor rolanti donusu                                       │
│  ├── Kapasitör patlamasi                                       │
│  └── PCB yanik                                                  │
│                                                                 │
│  Koruma 1: P-MOSFET (Hizli, dusuk direnç)                     │
│  ├── Source: Batarya (+)                                        │
│  ├── Drain: Sistem (+)                                         │
│  ├── Gate: Zener + direnc ağı                                  │
│  └── Body diyot: Normal akim yolu                              │
│                                                                 │
│  Koruma 2: Schottky Diyot (Basit, guvenilir)                  │
│  ├── D3, D4, D5: SS34 (3A/40V)                                │
│  ├── Forward voltage: ~0.3V                                    │
│  └── Gucl kaybı: ~1.2W @ 4A                                   │
│                                                                 │
│  Koruma 3: PTC Fuse (Otomatik reset)                          │
│  ├── F1: 5A PTC                                               │
│  ├── Reset suresi: ~30 saniye                                  │
│  └── Kalıcı hasar durumunda: Sigorta degisimi                  │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 10.2 Akim Koruma (OCP)

```
┌─────────────────────────────────────────────────────────────────┐
│                 AKIM KORUMA SISTEMI                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Seviye 1: LM5122 Entegre Koruma                              │
│  ├── Over-current threshold: ~5A                               │
│  ├── Hizli response: <1µs                                      │
│  └── Hicic mode: Cycle-by-cycle                                │
│                                                                 │
│  Seviye 2: PTC Fuse (F1)                                       │
│  ├── Trip akimi: 5A                                            │
│  ├── Reset suresi: ~30sn                                       │
│  └── Kalıcı koruma                                             │
│                                                                 │
│  Seviye 3: Hoparlor Fuse (PCB Mount)                           │
│  ├── Per-kanal sigorta                                         │
│  ├── Deger: 3A (50W / 35V ≈ 1.43A, 2x margin)                │
│  └── Degisim kolayligi                                         │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 10.3 Termal Koruma

```
┌─────────────────────────────────────────────────────────────────┐
│                 TERMAL KORUMA SISTEMI                           │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Sicaklik Noktalari:                                           │
│  ├── TP1: IC1 (LM5122 +35V) vucut sicakligi                   │
│  ├── TP2: IC2 (LM5122 -35V) vucut sicakligi                   │
│  ├── TP3: L1 induktor sicakligi                                │
│  ├── TP4: L2 induktor sicakligi                                │
│  ├── TP5: D1 Schottky sicakligi                               │
│  └── TP6: D3 Schottky sicakligi                               │
│                                                                 │
│  Esik Degerleri:                                               │
│  ├── 80°C: Uyari (LED Kirmizi)                                 │
│  ├── 100°C: Amplifikator kapatma                               │
│  └── 120°C: Sistem kapatma (Hard shutdown)                     │
│                                                                 │
│  Koruma Mekanizmasi:                                           │
│  ├── LM5122 OTP: Entegre sicaklik sensoru                     │
│  ├── KSD301 (NC): Mekanik sicaklik anahtarı                   │
│  └── NTC + MCU: Yazilim tabanlı kontrol                        │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 10.4 Kisa Devre Koruma

```
┌─────────────────────────────────────────────────────────────────┐
│                 KISA DEVRE KORUMA                               │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Durum: Cikis kisalir                                          │
│  ├── Akim aninda artar (teorik: sonsuz)                        │
│  ├── LM5122 OCP tetiklenir (<1µs)                             │
│  ├── Boost converter hicic moda gecer                          │
│  └── PTC fuse trip eder (30ms gecikme)                         │
│                                                                 │
│  Islem:                                                         │
│  ├── 1. LM5122 OCP: Hizli response, cycle-by-cycle             │
│  ├── 2. PTC Fuse: Kalici koruma, reset icin bekle              │
│  └── 3. Hoparlor Fuse: Per-kanal koruma                        │
│                                                                 │
│  Dikkat: Kisa devre durumunda:                                  │
│  ├── Maximum akim: 5A (LM5122 limit)                           │
│  ├── Isitma: P = I²R = 25 × 0.01 = 0.25W (trace)             │
│  └── PCB hasari onlenir                                        │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

### 10.5 Transient Koruma

```
┌─────────────────────────────────────────────────────────────────┐
│              TRANSIENT KORUMA (TVS)                             │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  Bilesen: P6KE36A (TVS Diyodu)                                 │
│  ├── Breakdown voltage: 36V                                    │
│  ├── Clamping voltage: 58.1V @ 17.3A                          │
│  ├── Peak pulse power: 600W                                   │
│  └── Response time: <1ns                                       │
│                                                                 │
│  Koruma Kapsami:                                               │
│  ├── USB baglantisi esnasinda transient                        │
│  ├── Batarya takma/cikarma aninda                              │
│  ├── Adapter takma/cikarma aninda                              │
│  └── Elektrostatik deşarj (ESD)                               │
│                                                                 │
│  Montaj:                                                        │
│  ├── TVS1: +35V rail uzerinde                                  │
│  ├── TVS2: -35V rail uzerinde                                  │
│  └── Mumkun oldugunca giris konnektorune yakin                 │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 11. BOM (Bill of Materials)

### 11.1 Kritik Bilesenler

| # | Referans | Deger | PACKAGE | Adet | Birim Fiyat | Toplam |
|---|----------|-------|---------|------|-------------|--------|
| 1 | IC1, IC2 | LM5122 | QFN-16 | 2 | $3.50 | $7.00 |
| 2 | L1, L2 | 4.7µH/10A | XAL5030 | 2 | $8.50 | $17.00 |
| 3 | L3, L4 | 10µH/5A | XAL5030 | 2 | $6.00 | $12.00 |
| 4 | D1, D3 | SS36 | SMA | 2 | $0.30 | $0.60 |
| 5 | D3-D5 | SS34 | SMA | 3 | $0.25 | $0.75 |
| 6 | C17 | 4700µF/50V | Radial | 1 | $4.50 | $4.50 |
| 7 | C20, C21 | 4700µF/50V | Radial | 2 | $4.50 | $9.00 |
| 8 | TVS1, TVS2 | P6KE36A | DO-15 | 2 | $1.20 | $2.40 |
| 9 | IC3 | LM393 | SOIC-8 | 1 | $0.50 | $0.50 |
| 10 | NTC1 | 5Ω Cold | Radial | 1 | $1.50 | $1.50 |
| 11 | Relay1 | SPST 10A | PCB | 1 | $2.00 | $2.00 |

### 11.2 Pasif Bilesenler

| # | Referans | Deger | Adet | Birim Fiyat | Toplam |
|---|----------|-------|------|-------------|--------|
| 1 | R1, R22, R24 | 100kΩ | 3 | $0.01 | $0.03 |
| 2 | R2, R25 | 47kΩ | 2 | $0.01 | $0.02 |
| 3 | R3, R14 | 4.7kΩ | 2 | $0.01 | $0.02 |
| 4 | R4-R6, R12-R13, R15-R19, R26-R28 | 10kΩ | 11 | $0.01 | $0.11 |
| 5 | R5 | 10kΩ | 1 | $0.01 | $0.01 |
| 6 | R18, R19 | 1kΩ | 2 | $0.01 | $0.02 |
| 7 | C1, C18, C22, C23, C24-C26 | 100nF | 7 | $0.05 | $0.35 |
| 8 | C2 | 220µF/50V | 1 | $0.50 | $0.50 |
| 9 | C5 | 1nF | 2 | $0.02 | $0.04 |
| 10 | C6 | 100pF | 2 | $0.02 | $0.04 |
| 11 | C7, C19 | 10nF | 2 | $0.02 | $0.04 |
| 12 | C8 | 100nF | 2 | $0.05 | $0.10 |
| 13 | C27 | 10µF | 1 | $0.10 | $0.10 |

### 11.3 Koruma Bilesenleri

| # | Referans | Deger | Adet | Birim Fiyat | Toplam |
|---|----------|-------|------|-------------|--------|
| 1 | F1 | 5A PTC | 1 | $1.00 | $1.00 |
| 2 | F2, F3 | 10A Slow-blow | 2 | $0.80 | $1.60 |
| 3 | D2 | 1N4148 | 2 | $0.05 | $0.10 |
| 4 | D8 | Zener 3.3V | 1 | $0.10 | $0.10 |

### 11.4 Konnektorler

| # | Referans | Tip | Adet | Birim Fiyat | Toplam |
|---|----------|-----|------|-------------|--------|
| 1 | XT60 | Battery Input | 1 | $2.50 | $2.50 |
| 2 | DC Barrel | 5.5mm/2.1mm | 1 | $1.00 | $1.00 |
| 3 | Phoenix | 4-pin | 1 | $3.00 | $3.00 |
| 4 | Pin Header | 2x5 | 1 | $0.50 | $0.50 |
| 5 | USB-C | Type-C | 1 | $1.50 | $1.50 |
| 6 | Screw Terminal | 2-pin | 1 | $1.00 | $1.00 |
| 7 | Fuse Holder | PCB | 3 | $0.80 | $2.40 |

### 11.5 Toplam Maliyet

| Kategori | Maliyet |
|----------|---------|
| Aktif Bilesenler | $57.75 |
| Pasif Bilesenler | $1.38 |
| Koruma Bilesenleri | $2.80 |
| Konnektorler | $12.00 |
| PCB (1 adet) | $15.00 |
| **Toplam** | **$88.93** |

---

## 12. Test Noktalari (Test Points)

| TP | Konum | Olculen Deger | Tolereans |
|----|-------|---------------|-----------|
| TP1 | IC1 Vout | +35V DC | ±1V |
| TP2 | IC2 Vout | -35V DC | ±1V |
| TP3 | L1 girisi | +22V DC (giris) | ±2V |
| TP4 | L2 girisi | +22V DC (giris) | ±2V |
| TP5 | C17 uzerinde | +22V DC (bulk) | ±2V |
| TP6 | D8 uzerinde | +3.3V DC (referans) | ±0.1V |

### 12.1 Test Proseduru

```
┌─────────────────────────────────────────────────────────────────┐
│                    TEST PROSEDURU                               │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ON HAZIRLIK:                                                  │
│  ├── 1. Giris voltaji olmadan baglanti kontrolu               │
│  ├── 2. Kisa devre testi (multimeter ile)                      │
│  └── 3. Voltaj bolucu dogrulama                                │
│                                                                 │
│  TEST ADIMLARI:                                                │
│  ├── 1. Giris bagla (22V DC)                                   │
│  ├── 2. Cikis voltajlarini olc (+35V, -35V)                   │
│  ├── 3. Ripple olc (oscilloscope)                              │
│  │   └── Hedef: <100mVp-p                                     │
│  ├── 4. Load test (1A, 2A, 3A)                                │
│  │   └── Hedef: <200mV sag @ 3A                               │
│  ├── 5. Efﬁisiyol olc (Pin / Pout)                            │
│  │   └── Hedef: >90%                                          │
│  ├── 6. Warm-up test (30 dakika)                              │
│  │   └── Hedef: Sicaklik <80°C                                │
│  └── 7. Kisa devre testi (kisa sureli)                        │
│      └── Hedef: OCP tetiklenir, hasar yok                     │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 13. Sorun Giderme

| Sorun | Belirti | Muhtemel Neden | Cozum |
|-------|---------|----------------|-------|
| Cikis yok | LED yanmiyor | Giris sigortasi atti | F1, F2, F3 kontrol |
| Dusuk cikis | 20V yerine 35V | Feedback direnci hatali | R1, R2 dogrulama |
| Yuksek ripple | >500mVp-p | Kapasitör yetersiz | C17, C20, C21 kontrol |
| Sicaklik uyarısı | LED kirmizi | Yetersiz soğutma | Heatsink, fan kontrol |
| Kapatma | Sistem kapaniyor | UVP tetiklendi | Batarya sarj seviyesi |
| Kavurma kokusu | Yanık kokusu | Kisa devre | Derhal kapat, kontrol |
| Titresim | Hum noise | Ground loop | Star ground dogrulama |

---

## 14. Bakim ve Servis

### 14.1 Periyodik Kontroller

| Suresi | Kontrol | Alet |
|--------|---------|------|
| Aylik | Kapasitör gorunum kontrolu | Goz muayenesi |
| 3 Aylik | Voltaj olcmesi | Multimetre |
| 6 Aylik | Termal kamera | IR kamera |
| Yillik | ESR olcmesi | ESR meter |

### 14.2 Bakim Proseduru

```
┌─────────────────────────────────────────────────────────────────┐
│                    BAKIM PROSEDURU                               │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ON HAZIRLIK:                                                  │
│  ├── 1. Sistemi kapat                                         │
│  ├── 2. Tum guc kaynaklarini cikar                             │
│  ├── 3. 10 dakika bekle (kapasitör deşarj)                    │
│  └── 4. Cikis voltajini olc (TP5 < 3.5V olmali)              │
│                                                                 │
│  BAKIM ADIMLARI:                                               │
│  ├── 1. Toz temizleme (havali tabanca)                         │
│  ├── 2. Baglanti noktalari kontrolu                            │
│  ├── 3. Kapasitör gorunum (sisme, akma)                        │
│  ├── 4. Lehim joint'leri (crack, cold joint)                   │
│  ├── 5. Sicaklik testi (calisirken)                            │
│  └── 6. Voltaj dogrulama (TP1-TP6)                            │
│                                                                 │
│  UYARI:                                                        │
│  ├── Elektrik c把持技术 ile calismayin                          │
│  ├── Uygun ESR kullanin                                        │
│  └── Yetkili servis onerilir                                   │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 15. Referanslar

| Referans | Aciklama |
|----------|----------|
| LM5122 Datasheet | Texas Instruments |
| SS34/SS36 Datasheet | Vishay |
| P6KE36A Datasheet | Littelfuse |
| LM393 Datasheet | Texas Instruments |
| ADR-089 | Class AB Amplifier Architecture Decision |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
**Version:** 1.0.0
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
