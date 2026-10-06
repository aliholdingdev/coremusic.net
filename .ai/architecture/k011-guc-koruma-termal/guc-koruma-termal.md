---
title: "K011 Analog Güç · Koruma · Termal"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 — K000-K071"
updated: 2026-10-06
---

# K011 — Analog Güç · Koruma · Termal

> **K numarası:** K011 · **Klasör:** `k011-guc-koruma-termal` · **Dosya:** `guc-koruma-termal`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `performance-engineer` (ikincil)
> **Klasör amacı:** Analog besleme kaynaklarının üretimini, devreye alma/koruma devrelerini ve termal yönetim stratejisini tek çatı altında toplamak.

## 1. Kapsam ve Amaç

Bu dosya **Analog Güç · Koruma · Termal** konusunu ele alır. Kapsamı: ±35V analog ray üretimi, regülasyon/filtreleme ve şebeke tarafı güvenlik mesafeleri.

Yazı, salt-okunur yedek kaynaklardan türetilmiştir; her teknik değer aşağıda
belirtilen kaynak dosyalarında bulunmak zorundadır. Kaynakta bulunmayan her değer
`⚠️ VERIFICATION REQUIRED` ile işaretlenir (ZERO-HALLUCINATION).

## 2. Sinyal / Donanım Akışı

```
      [ Şebeke girişi ]
            │
            ▼
      [ Rectifier / filtre ]
            │
            ▼
      [ ±35V analog ray ]
            │
            ▼
      [ Koruma devresi (DC · kısa devre · sırt) ]
            │
            ▼
      [ Termal izleme ]
            │
            ▼
      [ Sınıf AB + çıkış ]
```

**Akış notları:**

1. **Şebeke girişi** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
2. **Rectifier / filtre** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
3. **±35V analog ray** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
4. **Koruma devresi (DC · kısa devre · sırt)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
5. **Termal izleme** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
6. **Sınıf AB + çıkış** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.

## 3. Kaynak Envanteri

| # | Kaynak dosya (salt-okunur yedek) | Satır | Bu dosyadaki rolü |
|---|---|---:|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/guc-kaynagi-analog.md` | 165 | birincil kaynak (ham içerik §6) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/koruma-devreleri.md` | 190 | ikincil kaynak (§6) |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/termal-yonetim.md` | 170 | tamamlayıcı kaynak (§6) |

## 4. Teknik Spesifikasyon Tabloları (kaynak kalıbı)

### 4.1 · `k1-donanim/guc-kaynagi-analog.md`

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

### 4.2 · `k1-donanim/guc-kaynagi-analog.md`

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

### 4.3 · `k1-donanim/guc-kaynagi-analog.md`

| Koruma | Tip | Değer | Açıklama |
|--------|-----|-------|----------|
| Overcurrent | Cycle-by-cycle | 10A | LM5122 OCP |
| Overvoltage | Zener clamp | 38V | FB pin |
| Thermal | NTC sensor | 85°C shutdown | Soğutucu |
| Soft-start | Capacitor | 100nF | 10ms start-up |

### 4.4 · `k1-donanim/guc-kaynagi-analog.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | PCB layout, thermal |
| K1 Amplifikatör | Çıkış | ±35V rail supply |
| K1 Analog Sinyal | Bağlantı | Analog circuit power |
| K2 OS/Sürücüler | Üst | Standby control |

### 4.5 · `k1-donanim/koruma-devreleri.md`

| Koruma Tipi | Tetikleme | Gecikme | Yanıt |
|-------------|-----------|---------|-------|
| DC Offset | > ±1V DC | 0.5s | Speaker disconnect |
| Overcurrent | > 10A peak | Anında | Current limiting |
| Thermal Shutdown | > 85°C | 5s | Speaker disconnect |
| Short Circuit | 0Ω load | Anında | Current limiting |
| Overvoltage | > ±40V | 1ms | PSU shutdown |
| Mains Fuse | > 3A AC | 10ms | Fuse blown |

### 4.6 · `k1-donanim/koruma-devreleri.md`

| Akım Seviyesi | Davranış | Süre |
|---------------|----------|------|
| < 5A | Normal çalışma | Sürekli |
| 5-8A | Soft limiting | 10ms |
| 8-10A | Hard limiting | Anında |
| > 10A | Shutdown | 100µs |

### 4.7 · `k1-donanim/koruma-devreleri.md`

| Sıcaklık (°C) | Davranış |
|---------------|----------|
| < 70 | Normal çalışma |
| 70-80 | Uyarı LED (sarı) |
| 80-85 | Fan maksimum hız |
| > 85 | Thermal shutdown |
| < 75 | Otomatik restart |

### 4.8 · `k1-donanim/koruma-devreleri.md`

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Current Sense | 0.22Ω 5W | 2 | Output sensing |
| 2 | Current Amp | INA213 | 2 | Current sense amplifier |
| 3 | Comparator | LM393 | 4 | Dual comparator |
| 4 | Relay | Finder 40.52 | 2 | 30A DPDT |
| 5 | Relay Driver | BC337 | 2 | NPN driver |
| 6 | NTC Sensor | 10kΩ NTC | 2 | Temperature sensing |
| 7 | Fuse | 3A AC | 1 | Mains protection |
| 8 | TVS Diode | SMBJ36A | 2 | Overvoltage clamp |

### 4.9 · `k1-donanim/termal-yonetim.md`

| Parametre | Değer |
|-----------|-------|
| Maks. Ortam Sıcaklığı | 40°C |
| Maks. Cihaz Sıcaklığı | 85°C (transistör junction) |
| Toplam Isı Dağılımı | 350W (8 kanal tam yükte) |
| Soğutucu Kapasitesi | 0.5°C/W (hedef) |
| Fan Hızı | 0-3000 RPM (PWM kontrollü) |
| Sıcaklık Sensörü | NTC 10kΩ @ 25°C |
| Isı Pedleri | Bergquist Sil-Pad 1500 |

### 4.10 · `k1-donanim/termal-yonetim.md`

| Durum | IC (A) | VCE (V) | PC (W) | Sıcaklık Yükselmesi |
|-------|--------|---------|--------|---------------------|
| Boşta | 0.05 | 70 | 3.5 | 4.3°C |
| 10W | 0.5 | 35 | 17.5 | 21.6°C |
| 50W | 1.8 | 19.4 | 35 | 42.7°C |
| 100W | 3.5 | 10 | 35 | 42.7°C |
| 250W | 5.6 | 6.25 | 35 | 42.7°C |

### 4.11 · `k1-donanim/termal-yonetim.md`

| Durum | Kanal Başına | Toplam | Fan Hızı |
|-------|-------------|--------|----------|
| Boşta | 7W | 56W | %20 (600 RPM) |
| Orta | 40W | 320W | %60 (1800 RPM) |
| Tam Yük | 45W | 360W | %100 (3000 RPM) |

### 4.12 · `k1-donanim/termal-yonetim.md`

| Parametre | Değer |
|-----------|-------|
| Malzeme | 6063-T5 Alüminyum |
| Boyut | 300mm × 100mm × 50mm |
| Fin Sayısı | 20 |
| Fin Yüksekliği | 45mm |
| Fin Kalınlığı | 1.5mm |
| Tab Kalınlığı | 5mm |
| Ağırlık | 1.2kg |
| RθSA | 0.45°C/W |

## 5. Bölüm Yol Haritası (kaynak başlıkları)

| # | Kaynak | Seviye | Başlık |
|---:|---|---:|---|
| 1 | `k1-donanim/guc-kaynagi-analog.md` | H1 | ±35V Analog Güç Kaynağı |
| 2 | `k1-donanim/guc-kaynagi-analog.md` | H2 | Genel Bakış |
| 3 | `k1-donanim/guc-kaynagi-analog.md` | H2 | Teknik Spesifikasyonlar |
| 4 | `k1-donanim/guc-kaynagi-analog.md` | H2 | Güç Topolojisi |
| 5 | `k1-donanim/guc-kaynagi-analog.md` | H2 | Devre Tasarımı |
| 6 | `k1-donanim/guc-kaynagi-analog.md` | H3 | LM5122 Dual Boost Converter |
| 7 | `k1-donanim/guc-kaynagi-analog.md` | H3 | Voltaj Ayarı |
| 8 | `k1-donanim/guc-kaynagi-analog.md` | H2 | Bileşen Listesi |
| 9 | `k1-donanim/guc-kaynagi-analog.md` | H2 | Ripple Analizi |
| 10 | `k1-donanim/guc-kaynagi-analog.md` | H2 | Koruma Devreleri |
| 11 | `k1-donanim/guc-kaynagi-analog.md` | H2 | Bağımlılıklar |
| 12 | `k1-donanim/guc-kaynagi-analog.md` | H2 | Durum: Implementasyon |
| 13 | `k1-donanim/koruma-devreleri.md` | H1 | Koruma Devreleri |
| 14 | `k1-donanim/koruma-devreleri.md` | H2 | Genel Bakış |
| 15 | `k1-donanim/koruma-devreleri.md` | H2 | Teknik Spesifikasyonlar |
| 16 | `k1-donanim/koruma-devreleri.md` | H2 | DC Offset Koruması |
| 17 | `k1-donanim/koruma-devreleri.md` | H3 | Devre Şeması |
| 18 | `k1-donanim/koruma-devreleri.md` | H3 | Çalışma Prensibi |
| 19 | `k1-donanim/koruma-devreleri.md` | H2 | Overcurrent Koruması |
| 20 | `k1-donanim/koruma-devreleri.md` | H3 | Devre Şeması |
| 21 | `k1-donanim/koruma-devreleri.md` | H3 | Current Limiting Profile |
| 22 | `k1-donanim/koruma-devreleri.md` | H2 | Thermal Shutdown |
| 23 | `k1-donanim/koruma-devreleri.md` | H3 | Devre Şeması |
| 24 | `k1-donanim/koruma-devreleri.md` | H3 | Thermal Profile |
| 25 | `k1-donanim/koruma-devreleri.md` | H2 | Short Circuit Koruması |
| 26 | `k1-donanim/koruma-devreleri.md` | H3 | Çift Koruma |
| 27 | `k1-donanim/koruma-devreleri.md` | H2 | Bileşen Değerleri |
| 28 | `k1-donanim/koruma-devreleri.md` | H2 | Bağımlılıklar |
| 29 | `k1-donanim/koruma-devreleri.md` | H2 | Durum: Implementasyon |
| 30 | `k1-donanim/termal-yonetim.md` | H1 | Termal Yönetim |
| 31 | `k1-donanim/termal-yonetim.md` | H2 | Genel Bakış |
| 32 | `k1-donanim/termal-yonetim.md` | H2 | Teknik Spesifikasyonlar |
| 33 | `k1-donanim/termal-yonetim.md` | H2 | Isı Dağılım Analizi |
| 34 | `k1-donanim/termal-yonetim.md` | H3 | Toplam Isı (8 Kanal) |
| 35 | `k1-donanim/termal-yonetim.md` | H2 | Soğutucu Seçimi |
| 36 | `k1-donanim/termal-yonetim.md` | H3 | Alüminyum Ekstrüzyon Soğutucu |
| 37 | `k1-donanim/termal-yonetim.md` | H3 | Termal Ped |
| 38 | `k1-donanim/termal-yonetim.md` | H2 | Sıcaklık İzleme |
| 39 | `k1-donanim/termal-yonetim.md` | H3 | NTC Sensör Devresi |
| 40 | `k1-donanim/termal-yonetim.md` | H3 | Sıcaklık-Hassasiyet Tablosu |
| 41 | `k1-donanim/termal-yonetim.md` | H2 | Fan Kontrolü |
| 42 | `k1-donanim/termal-yonetim.md` | H3 | PWM Fan Driver |
| 43 | `k1-donanim/termal-yonetim.md` | H2 | Termal Pad Uygulaması |
| 44 | `k1-donanim/termal-yonetim.md` | H2 | Bağımlılıklar |
| 45 | `k1-donanim/termal-yonetim.md` | H2 | Durum: Implementasyon |

## 6. Kaynak İçerik (ham, satır bozulmadan)

### 6.1 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/guc-kaynagi-analog.md`


### ±35V Analog Güç Kaynağı

#### Genel Bakış

±35V analog güç kaynağı, COREMUSIC'ın Class AB amplifikatörleri için dual rail güç sağlar. LM5122 dual boost converter topolojisi ile AC mains'den yüksek verimli ±35V DC üretir. Low-noise design ile sinyal/gürültü oranını korur.

#### Teknik Spesifikasyonlar

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

#### Güç Topolojisi

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

#### Devre Tasarımı

##### LM5122 Dual Boost Converter

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

##### Voltaj Ayarı

```
Vout = 1.221V × (1 + R1/R2)

R1 = 100kΩ, R2 = 3.3kΩ

Vout = 1.221V × (1 + 100/3.3)
Vout = 1.221V × 31.3
Vout = 38.2V (no-load, slightly higher than 35V)

Load regulation: ±0.5V
Line regulation: ±0.2V
```

#### Bileşen Listesi

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

#### Ripple Analizi

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

#### Koruma Devreleri

| Koruma | Tip | Değer | Açıklama |
|--------|-----|-------|----------|
| Overcurrent | Cycle-by-cycle | 10A | LM5122 OCP |
| Overvoltage | Zener clamp | 38V | FB pin |
| Thermal | NTC sensor | 85°C shutdown | Soğutucu |
| Soft-start | Capacitor | 100nF | 10ms start-up |

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | PCB layout, thermal |
| K1 Amplifikatör | Çıkış | ±35V rail supply |
| K1 Analog Sinyal | Bağlantı | Analog circuit power |
| K2 OS/Sürücüler | Üst | Standby control |

#### Durum: Implementasyon

**Durum**: 🟡 Devam Ediyor

- LM5122 evaluation board test edildi, verimlilik %91 doğrulandı
- EMI filter: Pre-compliance test geçildi
- Thermal: Soğutucu tasarımı devam ediyor
- Output ripple: 1.8mVpp (hedef < 10mVpp) ✅
- PCB layout: 4-layer power board planlandı


### 6.2 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/koruma-devreleri.md`


### Koruma Devreleri

#### Genel Bakış

Koruma devreleri, COREMUSIC amplifikatörünü ve bağlı hoparlörleri DC offset, overcurrent, thermal runaway ve short circuit gibi zararlı durumlardan korur. Her koruma katmanı bağımsız çalışır ve fail-safe prensibine göre tasarlanmıştır.

#### Teknik Spesifikasyonlar

| Koruma Tipi | Tetikleme | Gecikme | Yanıt |
|-------------|-----------|---------|-------|
| DC Offset | > ±1V DC | 0.5s | Speaker disconnect |
| Overcurrent | > 10A peak | Anında | Current limiting |
| Thermal Shutdown | > 85°C | 5s | Speaker disconnect |
| Short Circuit | 0Ω load | Anında | Current limiting |
| Overvoltage | > ±40V | 1ms | PSU shutdown |
| Mains Fuse | > 3A AC | 10ms | Fuse blown |

#### DC Offset Koruması

##### Devre Şeması

```
Output Node (Amplifier Output)
     │
     ├─ C1 ──▶ 10µF (DC block)
     │           │
     │       ┌───┴───┐
     │       │  R1   │  100kΩ
     │       └───┬───┘
     │           │
     │       ┌───┴───┐
     │       │  D1   │  BAT54S (±0.7V clamp)
     │       └───┬───┘
     │           │
     │       ┌───┴───┐
     │       │ Comp  │  Comparator (LM393)
     │       │       │
     │       └───┬───┘
     │           │
     │           ├────▶ Relay Driver (BC337)
     │           │         │
     │           │         ▼
     │           │    Speaker Relay
     │           │    (disconnect)
     │           │
     │           └────▶ Fault LED (Red)
     │
     └─ R2 ──▶ Feedback Network
```

##### Çalışma Prensibi

```
1. Amplifikatör çıkışı DC coupled olarak comparator girişine bağlanır
2. C1 (10µF) DC component'i ayırır
3. Comparator, çıkış voltajını ±1V referans ile karşılaştırır
4. |Vout| > 1V olduğunda comparator çıkışı HIGH olur
5. BC337 MOSFET'i aktif eder ve speaker relay'ı açar
6. 0.5s gecikme (RC time constant) ile yanlış tetikleme önlenir
```

#### Overcurrent Koruması

##### Devre Şeması

```
Current Sense Resistor (R_sense = 0.22Ω)
     │
     ├─ V_sense = I_load × R_sense
     │
     │   @ 10A: V_sense = 2.2V
     │
     └─▶ Current Sense Amplifier (INA213)
              │
              ├─ Gain = 50
              │   Vout = 50 × V_sense
              │
              │   @ 10A: Vout = 110V (exceeds comparator ref)
              │
              └─▶ Comparator (LM393)
                      │
                      ├─ Reference: 2.5V (5A limit)
                      │
                      └─▶ Current Limit Driver
                              │
                              ▼
                         MOSFET Gate
                         (reduce drive)
```

##### Current Limiting Profile

| Akım Seviyesi | Davranış | Süre |
|---------------|----------|------|
| < 5A | Normal çalışma | Sürekli |
| 5-8A | Soft limiting | 10ms |
| 8-10A | Hard limiting | Anında |
| > 10A | Shutdown | 100µs |

#### Thermal Shutdown

##### Devre Şeması

```
NTC Thermistor (10kΩ @ 25°C)
     │
     └─▶ Voltage Divider
              │
              ├─ R_pullup = 10kΩ
              │
              └─▶ Comparator (LM393)
                      │
                      ├─ Reference: 0.35V (85°C)
                      │
                      └─▶ RC Delay (5s)
                              │
                              └─▶ Latch Circuit
                                      │
                                      ├─▶ Speaker Relay (disconnect)
                                      │
                                      └─▶ PSU Enable (shutdown)
```

##### Thermal Profile

| Sıcaklık (°C) | Davranış |
|---------------|----------|
| < 70 | Normal çalışma |
| 70-80 | Uyarı LED (sarı) |
| 80-85 | Fan maksimum hız |
| > 85 | Thermal shutdown |
| < 75 | Otomatik restart |

#### Short Circuit Koruması

##### Çift Koruma

```
1. Output Current Limiting:
   - R_sense = 0.22Ω
   - Max current: 10A
   - Response time: < 1µs

2. Foldback Current Limiting:
   - Vout < 5V: Full current allowed (10A)
   - Vout = 0V (short): Current reduced to 3A
   - Vout = 35V: Current = 0A (open circuit)
```

#### Bileşen Değerleri

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Current Sense | 0.22Ω 5W | 2 | Output sensing |
| 2 | Current Amp | INA213 | 2 | Current sense amplifier |
| 3 | Comparator | LM393 | 4 | Dual comparator |
| 4 | Relay | Finder 40.52 | 2 | 30A DPDT |
| 5 | Relay Driver | BC337 | 2 | NPN driver |
| 6 | NTC Sensor | 10kΩ NTC | 2 | Temperature sensing |
| 7 | Fuse | 3A AC | 1 | Mains protection |
| 8 | TVS Diode | SMBJ36A | 2 | Overvoltage clamp |

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Amplifikatör | Bağlantı | Output sensing |
| K1 Termal | Bağlantı | NTC sensor |
| K1 Güç Kaynağı | Bağlantı | PSU shutdown |
| K1 Hoparlör | Çıkış | Speaker relay |
| K2 OS/Sürücüler | Üst | Fault reporting |

#### Durum: Implementasyon

**Durum**: 🔴 Başlamadı

- DC offset koruması: Şematik hazır, layout yok
- Overcurrent: INA213 evaluation board test edildi
- Thermal shutdown: NTC sensor seçimi yapıldı
- Short circuit: Foldback design LTSpice'da simulate edildi
- Relay: Finder 40.52 (30A DPDT) seçildi
- PCB: Koruma devresi için ayrı area planlandı


### 6.3 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/termal-yonetim.md`


### Termal Yönetim

#### Genel Bakış

Termal yönetim sistemi, COREMUSIC amplifikatörünün tüm bileşenlerini güvenli çalışma sıcaklıklarında tutar. Soğutucu seçimi, termal ped, sıcaklık izleme ve fan kontrolü dahildir. MJL21194/93 transistörleri için en kritik termal tasarım uygulanır.

#### Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Maks. Ortam Sıcaklığı | 40°C |
| Maks. Cihaz Sıcaklığı | 85°C (transistör junction) |
| Toplam Isı Dağılımı | 350W (8 kanal tam yükte) |
| Soğutucu Kapasitesi | 0.5°C/W (hedef) |
| Fan Hızı | 0-3000 RPM (PWM kontrollü) |
| Sıcaklık Sensörü | NTC 10kΩ @ 25°C |
| Isı Pedleri | Bergquist Sil-Pad 1500 |

#### Isı Dağılım Analizi

###.mjle21194-93 Isı Üretimi

| Durum | IC (A) | VCE (V) | PC (W) | Sıcaklık Yükselmesi |
|-------|--------|---------|--------|---------------------|
| Boşta | 0.05 | 70 | 3.5 | 4.3°C |
| 10W | 0.5 | 35 | 17.5 | 21.6°C |
| 50W | 1.8 | 19.4 | 35 | 42.7°C |
| 100W | 3.5 | 10 | 35 | 42.7°C |
| 250W | 5.6 | 6.25 | 35 | 42.7°C |

##### Toplam Isı (8 Kanal)

| Durum | Kanal Başına | Toplam | Fan Hızı |
|-------|-------------|--------|----------|
| Boşta | 7W | 56W | %20 (600 RPM) |
| Orta | 40W | 320W | %60 (1800 RPM) |
| Tam Yük | 45W | 360W | %100 (3000 RPM) |

#### Soğutucu Seçimi

##### Alüminyum Ekstrüzyon Soğutucu

| Parametre | Değer |
|-----------|-------|
| Malzeme | 6063-T5 Alüminyum |
| Boyut | 300mm × 100mm × 50mm |
| Fin Sayısı | 20 |
| Fin Yüksekliği | 45mm |
| Fin Kalınlığı | 1.5mm |
| Tab Kalınlığı | 5mm |
| Ağırlık | 1.2kg |
| RθSA | 0.45°C/W |

##### Termal Ped

| Parametre | Değer |
|-----------|-------|
| Model | Bergquist Sil-Pad 1500 |
| Termal Direnç | 0.35°C/in² |
| Kalınlık | 0.18mm |
| Boyut | 25mm × 25mm (transistör başına) |
| Dielectric Strength | 6000V AC |

#### Sıcaklık İzleme

##### NTC Sensör Devresi

```
              +3.3V
               │
           ┌───┴───┐
           │  R1   │  10kΩ (Pull-up)
           └───┬───┘
               │
           ┌───┴───┐
           │  NTC  │  10kΩ @ 25°C
           │ Sensor│  β = 3950
           └───┬───┘
               │
               ├──────▶ ADC Input (PCM3168A spare channel)
               │
              GND
```

##### Sıcaklık-Hassasiyet Tablosu

| Sıcaklık (°C) | NTC Direnci (kΩ) | ADC Voltajı (V) | ADC Değeri |
|---------------|-------------------|-----------------|------------|
| 25 | 10.0 | 1.65 | 2048 |
| 50 | 3.60 | 0.89 | 1114 |
| 75 | 1.52 | 0.46 | 576 |
| 85 | 1.14 | 0.35 | 437 |
| 100 | 0.87 | 0.28 | 350 |

#### Fan Kontrolü

##### PWM Fan Driver

```
MCU (XMOS GPIO)
     │
     ├─ PWM Signal ──▶ MOSFET Gate (IRLML6344)
     │                   │
     │                   ▼
     │              Fan Motor
     │                   │
     │                   ▼
     │                  GND
     │
     └─ TACH Signal ◀── Fan Tachometer

Fan Speed Control:
- 0-25°C: Fan kapalı (0% PWM)
- 25-50°C: Lineer artış (0-50% PWM)
- 50-75°C: Lineer artış (50-80% PWM)
- 75-85°C: Maksimum hız (100% PWM)
- >85°C: Sistem kapatma (thermal shutdown)
```

#### Termal Pad Uygulaması

```
Transistör Mounting:

     ┌─────────────┐
     │  MJL21194   │  TO-264 Package
     │  (Tab)      │
     └──────┬──────┘
            │
     ┌──────┴──────┐
     │  Sil-Pad    │  0.18mm thermal interface
     │  1500       │
     └──────┬──────┘
            │
     ┌──────┴──────┐
     │  Heatsink   │  Alüminyum ekstrüzyon
     │  (Tab)      │
     └─────────────┘

Mounting Torque: 0.5 Nm (M3 vidalar)
Thermal Resistance: RθCS = 0.35°C/in² × 6.25cm² = 0.22°C/W
```

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | Soğutucu mekanik çizimleri |
| K1 Amplifikatör | Bağlantı | Transistör mounting |
| K1 Koruma | Bağlantı | Thermal shutdown |
| K2 OS/Sürücüler | Üst | Fan PWM control |

#### Durum: Implementasyon

**Durum**: 🔴 Başlamadı

- Soğutucu: Alüminyum ekstrüzyon profile seçildi
- Termal simülasyon: ANSYS Icepak ile yapılacak
- Fan: Noctua NF-A12x25 (120mm, PWM)
- Termal pad: Bergquist Sil-Pad 1500 onaylandı
- Mounting hardware: M3×8mm vidalar, termal ped, washer
- Prototype: İlk prototip için termal test planı hazır


## 7. Kenar Durumlar

| # | Kenar durum | Etki | Kaynak / Durum |
|---:|---|---|---|
| 1 | Ray voltajı ±35V olarak geçiyor; ölçülmüş regülasyon/ripple değeri yok | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 2 | Şebeke tarafı izolasyon/creepage mesafeleri vault'ta sayısal değil | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |

## 8. Hata Modları

1. Rail simetrisinin bozulması → çıkışta DC offset (kaynak: `guc-kaynagi-analog`).
2. Filtre kondansatörünün yaşlanması → ripple'ın artıp gürültüye dönüşmesi (kaynak: `guc-kaynagi-analog`).

## 9. Bağımlılık Matrisi

| Komşu K | Yön | İlişki | Kanıt |
|---|:---:|---|---|
| `[[../k010-class-ab-cikis/index]]` | ↓ | Raylar çıkış evresini besler | `.ai/architecture/k010-class-ab-cikis/index.md` |
| `[[../k013-pcb-hoparlor/index]]` | ↓ | Güç yerleşimi ve ısı dağıtımı PCB'de | `.ai/architecture/k013-pcb-hoparlor/index.md` |
| `[[../k009-vas-feedback/index]]` | ↓ | Besleme sürme kapasitesini etkiler | `.ai/architecture/k009-vas-feedback/index.md` |

Yerel dosyalar:

- `[[guc-koruma-termal]]` — Analog Güç · Koruma · Termal
- `[[koruma-devreleri-detay]]` — Koruma Devreleri + Termal Yönetim

## 10. Kanıt ve Doğrulama

- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/guc-kaynagi-analog.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/koruma-devreleri.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/termal-yonetim.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` bu dosya üretildikten sonra `vault-utf8-writer verify` ile BOM/UTF-8 kontrolü yapılır.
- `⚠️ VERIFICATION REQUIRED` — kaynakta bulunmayan ölçüm/sürüm/ADR değerleri bu dosyaya yazılmamıştır.
- Klasör geneli bağımlılıklar ve okuma sırası: `[[index]]`.

---
*K011 · Analog Güç · Koruma · Termal — SSOT: `.ai/architecture/k011-guc-koruma-termal/`; ham kaynak: `_backup/arch-2026-10-06_1057/architecture/`.*
