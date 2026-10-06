---
title: "K010 Çıkış Aşaması — MJL21194/93"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 — K000-K071"
updated: 2026-10-06
---

# K010 — Çıkış Aşaması — MJL21194/93

> **K numarası:** K010 · **Klasör:** `k010-class-ab-cikis` · **Dosya:** `cikis-asamasi-mjle21194-93`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `performance-engineer` (ikincil)
> **Klasör amacı:** Class AB sürme evresi, bias (quiescent akım) düzeni ve MJL21194/MJL21193 çıkış transistörlerinin SOA/termal sınırlarını tanımlamak.

## 1. Kapsam ve Amaç

Bu dosya **Çıkış Aşaması — MJL21194/93** konusunu ele alır. Kapsamı: NPN/PNP çiftinin yerleşimi, paralelleme, SOA/termal sınırları ve körelme dirençleri.

Yazı, salt-okunur yedek kaynaklardan türetilmiştir; her teknik değer aşağıda
belirtilen kaynak dosyalarında bulunmak zorundadır. Kaynakta bulunmayan her değer
`⚠️ VERIFICATION REQUIRED` ile işaretlenir (ZERO-HALLUCINATION).

## 2. Sinyal / Donanım Akışı

```
      [ VAS + feedback ]
            │
            ▼
      [ Class AB sürücü ]
            │
            ▼
      [ Bias düzeni (quiescent) ]
            │
            ▼
      [ MJL21194 / MJL21193 çifti ]
            │
            ▼
      [ Hoparlör yükü ]
```

**Akış notları:**

1. **VAS + feedback** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
2. **Class AB sürücü** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
3. **Bias düzeni (quiescent)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
4. **MJL21194 / MJL21193 çifti** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
5. **Hoparlör yükü** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.

## 3. Kaynak Envanteri

| # | Kaynak dosya (salt-okunur yedek) | Satır | Bu dosyadaki rolü |
|---|---|---:|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/mjle21194-93.md` | 157 | birincil kaynak (ham içerik §6) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/output-stage.md` | 199 | ikincil kaynak (§6) |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/koruma-devreleri.md` | 190 | tamamlayıcı kaynak (§6) |

## 4. Teknik Spesifikasyon Tabloları (kaynak kalıbı)

### 4.1 · `k1-donanim/mjle21194-93.md`

| Parametre | MJL21194 (NPN) | MJL21193 (PNP) |
|-----------|----------------|-----------------|
| Tip | NPN | PNP |
| VCBO | 250V | -250V |
| VCEO | 200V | -200V |
| VEB0 | 5V | -5V |
| IC Maks. | 16A | -16A |
| PC Maks. | 250W | 250W |
| hFE (min) | 80 @ 1A | 80 @ 1A |
| hFE (max) | 200 @ 1A | 200 @ 1A |
| fT | 4MHz | 4MHz |
| RθJC | 1.22°C/W | 1.22°C/W |
| RθJA | 40°C/W | 40°C/W |
| Package | TO-264 | TO-264 |
| Çalışma Sıcaklığı | -65°C ile +150°C | -65°C ile +150°C |

### 4.2 · `k1-donanim/mjle21194-93.md`

| Parametre | MJL21194 | MJL21193 | Birim |
|-----------|----------|----------|-------|
| VBE(sat) | 1.5 | -1.5 | V @ IC=8A |
| VCE(sat) | 1.2 | -1.2 | V @ IC=8A, IB=0.8A |
| ICES | 50 | -50 | µA @ VCE=200V |
| IEBO | 50 | -50 | µA @ VEB=4V |
| Cob | 100 | 100 | pF @ VCE=10V |

### 4.3 · `k1-donanim/mjle21194-93.md`

| Parametre | MJL21194 | MJL21193 | Birim |
|-----------|----------|----------|-------|
| hFE | 80-200 | 80-200 | - |
| fT | 4 | 4 | MHz |
| Cob | 100 | 100 | pF |
| Kurykcı | 500 | 500 | MHz (gain-bandwidth) |

### 4.4 · `k1-donanim/mjle21194-93.md`

| Durum | IC | VCE | PC | Sıcaklık Yükselmesi |
|-------|-----|------|-----|---------------------|
| Boşta (Idle) | 50mA | 70V | 3.5W | 4.3°C |
| 1W Çıkış | 120mA | 35V | 4.2W | 5.1°C |
| 50W Çıkış | 1.8A | 19.4V | 35W | 42.7°C |
| 100W Çıkış | 3.5A | 10V | 35W | 42.7°C |
| 250W Çıkış | 5.6A | 6.25V | 35W | 42.7°C |

### 4.5 · `k1-donanim/output-stage.md`

| Parametre | Değer |
|-----------|-------|
| Çıkış Empedansı | < 0.1Ω |
| Damaping Faktörü | > 200 (8Ω) |
| Maks. Çıkış Akımı | 16A (peak) |
| Idle Bias Akımı | 50mA (Class AB) |
| Çıkış Gücü | 250W RMS @ 8Ω |
| Crossover Distortion | < %0.001 (1kHz) |
| Slew Rate | > 100V/µs |

### 4.6 · `k1-donanim/output-stage.md`

| Referans | Değer | Tip | Açıklama |
|----------|-------|-----|----------|
| Q1 | MJL21194 | NPN Power | Çıkış (+) |
| Q2 | MJL21193 | PNP Power | Çıkış (-) |
| Q3 | BD139 | NPN Driver | Darlington driver (+) |
| Q4 | BD140 | PNP Driver | Darlington driver (-) |
| Q5 | BD139 | NPN | Thermal tracking |
| R5, R6 | 0.22Ω 5W | Wirewound | Emitter dirençleri |
| R7 | 10kΩ 1/4W | Metal Film | Bias network |
| R8 | 100Ω 1/4W | Metal Film | Bias network |

### 4.7 · `k1-donanim/output-stage.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 VAS Stage | Giriş | VAS çıkış sinyali |
| K1 Feedback | Geri besleme | Çıkış geri besleme noktası |
| K1 Termal | Bağlantı | Soğutucu bağlantısı |
| K1 Koruma | Bağlantı | Overcurrent, DC offset |
| K1 Hoparlör | Çıkış | Speaker binding posts |
| K1 Güç Kaynağı | Alt | ±35V dual rail |

### 4.8 · `k1-donanim/koruma-devreleri.md`

| Koruma Tipi | Tetikleme | Gecikme | Yanıt |
|-------------|-----------|---------|-------|
| DC Offset | > ±1V DC | 0.5s | Speaker disconnect |
| Overcurrent | > 10A peak | Anında | Current limiting |
| Thermal Shutdown | > 85°C | 5s | Speaker disconnect |
| Short Circuit | 0Ω load | Anında | Current limiting |
| Overvoltage | > ±40V | 1ms | PSU shutdown |
| Mains Fuse | > 3A AC | 10ms | Fuse blown |

### 4.9 · `k1-donanim/koruma-devreleri.md`

| Akım Seviyesi | Davranış | Süre |
|---------------|----------|------|
| < 5A | Normal çalışma | Sürekli |
| 5-8A | Soft limiting | 10ms |
| 8-10A | Hard limiting | Anında |
| > 10A | Shutdown | 100µs |

### 4.10 · `k1-donanim/koruma-devreleri.md`

| Sıcaklık (°C) | Davranış |
|---------------|----------|
| < 70 | Normal çalışma |
| 70-80 | Uyarı LED (sarı) |
| 80-85 | Fan maksimum hız |
| > 85 | Thermal shutdown |
| < 75 | Otomatik restart |

### 4.11 · `k1-donanim/koruma-devreleri.md`

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

## 5. Bölüm Yol Haritası (kaynak başlıkları)

| # | Kaynak | Seviye | Başlık |
|---:|---|---:|---|
| 1 | `k1-donanim/mjle21194-93.md` | H1 | MJL21194/93 NPN/PNP Transistörler |
| 2 | `k1-donanim/mjle21194-93.md` | H2 | Genel Bakış |
| 3 | `k1-donanim/mjle21194-93.md` | H2 | Teknik Spesifikasyonlar |
| 4 | `k1-donanim/mjle21194-93.md` | H2 | DC Karakteristikleri (Tipik @ 25°C) |
| 5 | `k1-donanim/mjle21194-93.md` | H2 | AC Karakteristikleri (Tipik @ 25°C) |
| 6 | `k1-donanim/mjle21194-93.md` | H2 | Devre Bağlantıları |
| 7 | `k1-donanim/mjle21194-93.md` | H3 | Push-Pull Konfigürasyon |
| 8 | `k1-donanim/mjle21194-93.md` | H3 | Thermal Tracking (Bias Transistörleri) |
| 9 | `k1-donanim/mjle21194-93.md` | H2 | Termal Hesaplamalar |
| 10 | `k1-donanim/mjle21194-93.md` | H3 | Güç Tüketimi (Tipik Usage) |
| 11 | `k1-donanim/mjle21194-93.md` | H3 | Soğutucu Gereksinimi |
| 12 | `k1-donanim/mjle21194-93.md` | H2 | Bağımlılıklar |
| 13 | `k1-donanim/mjle21194-93.md` | H2 | Durum: Implementasyon |
| 14 | `k1-donanim/output-stage.md` | H1 | Push-Pull Output Stage |
| 15 | `k1-donanim/output-stage.md` | H2 | Genel Bakış |
| 16 | `k1-donanim/output-stage.md` | H2 | Teknik Spesifikasyonlar |
| 17 | `k1-donanim/output-stage.md` | H2 | Push-Pull Konfigürasyon |
| 18 | `k1-donanim/output-stage.md` | H3 | Tek transistor (Single-ended output) |
| 19 | `k1-donanim/output-stage.md` | H3 | Push-Pull Output |
| 20 | `k1-donanim/output-stage.md` | H2 | Darlington Configuration |
| 21 | `k1-donanim/output-stage.md` | H3 | Neden Darlington? |
| 22 | `k1-donanim/output-stage.md` | H3 | Darlington Emitters Follower |
| 23 | `k1-donanim/output-stage.md` | H2 | Thermal Tracking |
| 24 | `k1-donanim/output-stage.md` | H3 | Crossover Distortion Sorunu |
| 25 | `k1-donanim/output-stage.md` | H3 | Çözüm: Thermal Tracking |
| 26 | `k1-donanim/output-stage.md` | H2 | Bileşen Değerleri |
| 27 | `k1-donanim/output-stage.md` | H2 | Akım Yolu Analizi |
| 28 | `k1-donanim/output-stage.md` | H3 | Pozitif Yarım Döngü (MJL21194 Active) |
| 29 | `k1-donanim/output-stage.md` | H3 | Negatif Yarım Döngü (MJL21193 Active) |
| 30 | `k1-donanim/output-stage.md` | H2 | Bağımlılıklar |
| 31 | `k1-donanim/output-stage.md` | H2 | Durum: Implementasyon |
| 32 | `k1-donanim/koruma-devreleri.md` | H1 | Koruma Devreleri |
| 33 | `k1-donanim/koruma-devreleri.md` | H2 | Genel Bakış |
| 34 | `k1-donanim/koruma-devreleri.md` | H2 | Teknik Spesifikasyonlar |
| 35 | `k1-donanim/koruma-devreleri.md` | H2 | DC Offset Koruması |
| 36 | `k1-donanim/koruma-devreleri.md` | H3 | Devre Şeması |
| 37 | `k1-donanim/koruma-devreleri.md` | H3 | Çalışma Prensibi |
| 38 | `k1-donanim/koruma-devreleri.md` | H2 | Overcurrent Koruması |
| 39 | `k1-donanim/koruma-devreleri.md` | H3 | Devre Şeması |
| 40 | `k1-donanim/koruma-devreleri.md` | H3 | Current Limiting Profile |
| 41 | `k1-donanim/koruma-devreleri.md` | H2 | Thermal Shutdown |
| 42 | `k1-donanim/koruma-devreleri.md` | H3 | Devre Şeması |
| 43 | `k1-donanim/koruma-devreleri.md` | H3 | Thermal Profile |
| 44 | `k1-donanim/koruma-devreleri.md` | H2 | Short Circuit Koruması |
| 45 | `k1-donanim/koruma-devreleri.md` | H3 | Çift Koruma |
| 46 | `k1-donanim/koruma-devreleri.md` | H2 | Bileşen Değerleri |
| 47 | `k1-donanim/koruma-devreleri.md` | H2 | Bağımlılıklar |
| 48 | `k1-donanim/koruma-devreleri.md` | H2 | Durum: Implementasyon |

## 6. Kaynak İçerik (ham, satır bozulmadan)

### 6.1 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/mjle21194-93.md`


### MJL21194/93 NPN/PNP Transistörler

#### Genel Bakış

MJL21194 (NPN) ve MJL21193 (PNP), ON Semiconductor tarafından üretilen high-performance power transistörleridir. COREMUSIC'ın Class AB amplifikatör çıkış evresinde push-pull konfigürasyonda kullanılırlar. Her bir transistör 250W güç ve 200V gerilim dayanımına sahiptir.

#### Teknik Spesifikasyonlar

| Parametre | MJL21194 (NPN) | MJL21193 (PNP) |
|-----------|----------------|-----------------|
| Tip | NPN | PNP |
| VCBO | 250V | -250V |
| VCEO | 200V | -200V |
| VEB0 | 5V | -5V |
| IC Maks. | 16A | -16A |
| PC Maks. | 250W | 250W |
| hFE (min) | 80 @ 1A | 80 @ 1A |
| hFE (max) | 200 @ 1A | 200 @ 1A |
| fT | 4MHz | 4MHz |
| RθJC | 1.22°C/W | 1.22°C/W |
| RθJA | 40°C/W | 40°C/W |
| Package | TO-264 | TO-264 |
| Çalışma Sıcaklığı | -65°C ile +150°C | -65°C ile +150°C |

#### DC Karakteristikleri (Tipik @ 25°C)

| Parametre | MJL21194 | MJL21193 | Birim |
|-----------|----------|----------|-------|
| VBE(sat) | 1.5 | -1.5 | V @ IC=8A |
| VCE(sat) | 1.2 | -1.2 | V @ IC=8A, IB=0.8A |
| ICES | 50 | -50 | µA @ VCE=200V |
| IEBO | 50 | -50 | µA @ VEB=4V |
| Cob | 100 | 100 | pF @ VCE=10V |

#### AC Karakteristikleri (Tipik @ 25°C)

| Parametre | MJL21194 | MJL21193 | Birim |
|-----------|----------|----------|-------|
| hFE | 80-200 | 80-200 | - |
| fT | 4 | 4 | MHz |
| Cob | 100 | 100 | pF |
| Kurykcı | 500 | 500 | MHz (gain-bandwidth) |

#### Devre Bağlantıları

##### Push-Pull Konfigürasyon

```
               +35V
                │
            ┌───┴───┐
            │  R_E  │  0.22Ω 5W (Emitter Direnci)
            └───┬───┘
                │
         ┌──────┴──────┐
         │    Collector │
         │              │
         │   MJL21194   │  NPN Power Transistor
         │    (Q1)      │
         │              │
         │    Base ─────┼──── VAS Stage Output
         │              │
         │   Emitter    │
         └──────┬──────┘
                │
                ├───────────────────── Output to Speaker
                │
         ┌──────┴──────┐
         │    Emitter   │
         │              │
         │   MJL21193   │  PNP Power Transistor
         │    (Q2)      │
         │              │
         │    Base ─────┼──── VAS Stage Output (Inverted)
         │              │
         │   Collector  │
         └──────┬──────┘
                │
            ┌───┴───┐
            │  R_E  │  0.22Ω 5W (Emitter Direnci)
            └───┬───┘
                │
               -35V
```

##### Thermal Tracking (Bias Transistörleri)

```
         ┌─────────────────────────────┐
         │      BIAS TRACKING          │
         │                             │
         │   ┌───────┐                 │
         │   │ BD139 │ NPN             │
         │   └───┬───┘                 │
         │       │                     │
         │   ┌───┴───┐                 │
         │   │ Thermal│  MJL21194 ile  │
         │   │ Pad   │  temas halinde  │
         │   └───────┘                 │
         │                             │
         │   Bu transistör VBE         │
         │   sıcaklığa göre değişir    │
         │   ve bias akımını stabilize │
         │   eder.                     │
         └─────────────────────────────┘
```

#### Termal Hesaplamalar

##### Güç Tüketimi (Tipik Usage)

| Durum | IC | VCE | PC | Sıcaklık Yükselmesi |
|-------|-----|------|-----|---------------------|
| Boşta (Idle) | 50mA | 70V | 3.5W | 4.3°C |
| 1W Çıkış | 120mA | 35V | 4.2W | 5.1°C |
| 50W Çıkış | 1.8A | 19.4V | 35W | 42.7°C |
| 100W Çıkış | 3.5A | 10V | 35W | 42.7°C |
| 250W Çıkış | 5.6A | 6.25V | 35W | 42.7°C |

##### Soğutucu Gereksinimi

| Parametre | Değer |
|-----------|-------|
| Maks. Güç (ikili transistör) | 70W (2 × 35W) |
| RθJC | 1.22°C/W |
| RθCS | 0.5°C/W (termal ped ile) |
| RθSA | < 1.5°C/W (alüminyum soğutucu) |
| Toplam RθJA | < 3.2°C/W |
| Maks. Sıcaklık Yükselmesi | 42.7°C @ 250W |
| Maks. Cihaz Sıcaklığı | 67.7°C (25°C ambient) |

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Class AB Amp | Bağlantı | Push-pull output stage |
| K1 Termal | Bağlantı | Heatsink bağlantısı |
| K1 Koruma | Bağlantı | Overcurrent koruması |
| K1 Güç Kaynağı | Alt | ±35V rail |

#### Durum: Implementasyon

**Durum**: 🟢 Hazır

- Mouser/Farnell stok: MJL21194 (100+), MJL21193 (100+)
- eşleştirme: hFE ±%10 tolerance ile çift halinde
- Mounting: M3 vidalar, termal ped (Bergquist Sil-Pad 1500)
- Torque: 0.5 Nm (vidalar için)
- Lot eşleştirme: Üretim tarihi ve part number aynı olmalı


### 6.2 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/output-stage.md`


### Push-Pull Output Stage

#### Genel Bakış

Push-pull output stage, amplifikatörün son güçlendirme evresidir. VAS'tan gelen sinyali düşük empedanslı çıkışa dönüştürerek hoparlörü sürer. Darlington configuration ile yüksek akım kazancı, thermal tracking ile crossover distortion minimizasyonu sağlanır.

#### Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Çıkış Empedansı | < 0.1Ω |
| Damaping Faktörü | > 200 (8Ω) |
| Maks. Çıkış Akımı | 16A (peak) |
| Idle Bias Akımı | 50mA (Class AB) |
| Çıkış Gücü | 250W RMS @ 8Ω |
| Crossover Distortion | < %0.001 (1kHz) |
| Slew Rate | > 100V/µs |

#### Push-Pull Konfigürasyon

##### Tek transistor (Single-ended output)

```
Tek transistor output stage sadece Class A veya Class B modunda
çalışabilir. Class AB için push-pull (takviyeli) konfigürasyon gerekir.
```

##### Push-Pull Output

```
                           +35V
                            │
                        ┌───┴───┐
                        │  R5   │  0.22Ω 5W (Emitter)
                        └───┬───┘
                            │
                        ┌───┴───┐
                        │  Q1   │  MJL21194 (NPN)
                        │       │  250W/200V/16A
                        └───┬───┘
                            │
                            │
                    ┌───────┴───────┐
                    │   OUTPUT NODE │──── To Speaker
                    │               │
                        ┌───┴───┐
                        │  Q2   │  MJL21193 (PNP)
                        │       │  250W/200V/16A
                        └───┬───┘
                            │
                        ┌───┴───┐
                        │  R6   │  0.22Ω 5W (Emitter)
                        └───┬───┘
                            │
                           -35V
```

#### Darlington Configuration

##### Neden Darlington?

```
Tek bir power transistör (MJL21194) için gereken base akımı:

IB = IC / hFE
IB = 8A / 100 = 80mA

Bu akım VAS'tan doğrudan sağlanamaz.
Darlington konfigürasyonu ile equivalent hFE yükseltilir:
hFE(total) = hFE1 × hFE2 = 100 × 100 = 10,000

IB = 8A / 10,000 = 0.8mA (VAS'tan kolayca sağlanabilir)
```

##### Darlington Emitters Follower

```
                    Collector (Common)
                         │
                    ┌────┴────┐
                    │  Q_drv  │  Driver Transistor
                    │ (BD139) │  NPN
                    └────┬────┘
                         │
                         │  Base
                         │
                    ┌────┴────┐
                    │  Q_out  │  Output Transistor
                    │(MJL21194)│  NPN Power
                    └────┬────┘
                         │
                         │  Emitter
                         │
                    ┌────┴────┐
                    │  R_emit │  0.22Ω
                    └────┬────┘
                         │
                       Output
```

#### Thermal Tracking

##### Crossover Distortion Sorunu

```
Class AB amplifikatörde, crossover bölgesinde her iki transistör
de yarı-iletken modunda çalışır. Sıcaklık değişimleri VBE'yi
değiştirir ve bias akımını etkiler.

Sıcaklık artışı → VBE azalır → Bias akımı artar → Termal runaway riski
```

##### Çözüm: Thermal Tracking

```
                    +35V
                     │
                 ┌───┴───┐
                 │  R7   │  10kΩ
                 └───┬───┘
                     │
                 ┌───┴───┐
                 │  Q_th │  BD139 (NPN)
                 │       │  Soğutucuya monte edilmiş
                 └───┬───┘
                     │
                     ├────────────────── Bias Point
                     │
                 ┌───┴───┐
                 │  R8   │  100Ω
                 └───┬───┘
                     │
                    -35V

Q_th, soğutucu ile aynı sıcaklıktadır.
Sıcaklık arttığında VBE azalır ve bias akımı otomatik olarak ayarlanır.
```

#### Bileşen Değerleri

| Referans | Değer | Tip | Açıklama |
|----------|-------|-----|----------|
| Q1 | MJL21194 | NPN Power | Çıkış (+) |
| Q2 | MJL21193 | PNP Power | Çıkış (-) |
| Q3 | BD139 | NPN Driver | Darlington driver (+) |
| Q4 | BD140 | PNP Driver | Darlington driver (-) |
| Q5 | BD139 | NPN | Thermal tracking |
| R5, R6 | 0.22Ω 5W | Wirewound | Emitter dirençleri |
| R7 | 10kΩ 1/4W | Metal Film | Bias network |
| R8 | 100Ω 1/4W | Metal Film | Bias network |

#### Akım Yolu Analizi

##### Pozitif Yarım Döngü (MJL21194 Active)

```
+35V → R5 → Q1(MJL21194) Collector → Q1 Emitter → R_out → Speaker → GND

Peak akım: IC = 5.6A (250W @ 8Ω)
DC akım: IC = 3.5A (100W @ 8Ω)
```

##### Negatif Yarım Döngü (MJL21193 Active)

```
GND → Speaker → R_out → Q2(MJL21193) Emitter → Q2 Collector → R6 → -35V

Peak akım: IC = 5.6A
DC akım: IC = 3.5A
```

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 VAS Stage | Giriş | VAS çıkış sinyali |
| K1 Feedback | Geri besleme | Çıkış geri besleme noktası |
| K1 Termal | Bağlantı | Soğutucu bağlantısı |
| K1 Koruma | Bağlantı | Overcurrent, DC offset |
| K1 Hoparlör | Çıkış | Speaker binding posts |
| K1 Güç Kaynağı | Alt | ±35V dual rail |

#### Durum: Implementasyon

**Durum**: 🟡 Simülasyon Aşamasında

- LTSpice simülasyonu tamamlandı
- Thermal runaway analizi: Stabil (dTC/dt < 0)
- Emitter dirençleri: 0.22Ω wirewound (5W, %1 tolerance)
- PCB placement: Symmetrical, short traces to output connector
- Heatsink: Alüminyum ekstrüzyon, 150mm, 2°C/W


### 6.3 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/koruma-devreleri.md`


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


## 7. Kenar Durumlar

| # | Kenar durum | Etki | Kaynak / Durum |
|---:|---|---|---|
| 1 | Paralel transistör akım paylaşımı toleransı vault'ta yok | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 2 | SOA grafiği için yük çizgisi hesabı kaynakta verilmemiş | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |

## 8. Hata Modları

1. Akım paylaşımının dengesiz olması → tek transistörün aşırı ısınması (kaynak: `mjle21194-93`).
2. Körelme direncinin olmaması/yanlış seçilmesi → termal kaçak (kaynak: `output-stage`).

## 9. Bağımlılık Matrisi

| Komşu K | Yön | İlişki | Kanıt |
|---|:---:|---|---|
| `[[../k009-vas-feedback/index]]` | ↑ | Sürme sinyali VAS'tan gelir | `.ai/architecture/k009-vas-feedback/index.md` |
| `[[../k011-guc-koruma-termal/index]]` | ↑ | ±35V raylar ve koruma/termal devreleri | `.ai/architecture/k011-guc-koruma-termal/index.md` |
| `[[../k013-pcb-hoparlor/index]]` | ↓ | Transistör yerleşimi ve ısı dağıtımı | `.ai/architecture/k013-pcb-hoparlor/index.md` |

Yerel dosyalar:

- `[[class-ab-cikis-asamasi]]` — Class AB Amplifikatör + Çıkış Aşaması
- `[[cikis-asamasi-mjle21194-93]]` — Çıkış Aşaması — MJL21194/93

## 10. Kanıt ve Doğrulama

- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/mjle21194-93.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/output-stage.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/koruma-devreleri.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` bu dosya üretildikten sonra `vault-utf8-writer verify` ile BOM/UTF-8 kontrolü yapılır.
- `⚠️ VERIFICATION REQUIRED` — kaynakta bulunmayan ölçüm/sürüm/ADR değerleri bu dosyaya yazılmamıştır.
- Klasör geneli bağımlılıklar ve okuma sırası: `[[index]]`.

---
*K010 · Çıkış Aşaması — MJL21194/93 — SSOT: `.ai/architecture/k010-class-ab-cikis/`; ham kaynak: `_backup/arch-2026-10-06_1057/architecture/`.*
