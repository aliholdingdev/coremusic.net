---
title: "K010 Class AB Amplifikatör + Çıkış Aşaması"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 — K000-K071"
updated: 2026-10-06
---

# K010 — Class AB Amplifikatör + Çıkış Aşaması

> **K numarası:** K010 · **Klasör:** `k010-class-ab-cikis` · **Dosya:** `class-ab-cikis-asamasi`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `performance-engineer` (ikincil)
> **Klasör amacı:** Class AB sürme evresi, bias (quiescent akım) düzeni ve MJL21194/MJL21193 çıkış transistörlerinin SOA/termal sınırlarını tanımlamak.

## 1. Kapsam ve Amaç

Bu dosya **Class AB Amplifikatör + Çıkış Aşaması** konusunu ele alır. Kapsamı: Sürme evresi topolojisi, bias düzeni, crossovers bölgesi ve çıkış empedansı.

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
| 1 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/class-ab-amplifikator.md` | 162 | birincil kaynak (ham içerik §6) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/output-stage.md` | 199 | ikincil kaynak (§6) |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/mjle21194-93.md` | 157 | tamamlayıcı kaynak (§6) |

## 4. Teknik Spesifikasyon Tabloları (kaynak kalıbı)

### 4.1 · `k1-donanim/class-ab-amplifikator.md`

| Parametre | Değer |
|-----------|-------|
| Çıkış Gücü | 250W RMS (8Ω, %0.5 THD) |
| Frekans Aralığı | 5Hz – 80kHz (±0.5dB) |
| THD+N | < %0.001 (1kHz, 1W) |
| Sinyal/Gürültü | > 120dB (A-Weighted) |
| Damaping Faktörü | > 200 (8Ω) |
| Giriş Empedansı | 47kΩ (differential) |
| Giriş Hassasiyeti | 1.5Vrms (tam çıkış için) |
| Kazanç | 26dB (20x) |
| Güç Topolojisi | ±35V Dual Rail |

### 4.2 · `k1-donanim/class-ab-amplifikator.md`

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Giriş Transistörleri | BC560C | 2 | PNP low-noise |
| 2 | VAS Transistörleri | MPSA06 | 2 | NPN high-voltage |
| 3 | Çıkış Transistörleri | MJL21194 | 4 | NPN power |
| 4 | Çıkış Transistörleri | MJL21193 | 4 | PNP power |
| 5 | Bias Transistörleri | BD139/140 | 2 | Thermal tracking |
| 6 | Miller Kondansatörü | 10pF C0G | 1 | Frequency compensation |
| 7 | Emitter Dirençleri | 0.22Ω 5W | 8 | Current sharing |
| 8 | Feedback Direnci | 20kΩ | 1 | Gain ayarı |
| 9 | Feedback Direnç | 1kΩ | 1 | Gain ayarı |

### 4.3 · `k1-donanim/class-ab-amplifikator.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 AK4458 | Giriş | DAC diferansiyel çıkış |
| K1 Feedback Network | Geri besleme | Negatif geri besleme ağı |
| K1 Güç Kaynağı | Alt | ±35V dual rail |
| K1 Termal | Bağlantı | Soğutucu bağlantısı |
| K1 Koruma | Çıkış | DC offset, overcurrent koruması |

### 4.4 · `k1-donanim/output-stage.md`

| Parametre | Değer |
|-----------|-------|
| Çıkış Empedansı | < 0.1Ω |
| Damaping Faktörü | > 200 (8Ω) |
| Maks. Çıkış Akımı | 16A (peak) |
| Idle Bias Akımı | 50mA (Class AB) |
| Çıkış Gücü | 250W RMS @ 8Ω |
| Crossover Distortion | < %0.001 (1kHz) |
| Slew Rate | > 100V/µs |

### 4.5 · `k1-donanim/output-stage.md`

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

### 4.6 · `k1-donanim/output-stage.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 VAS Stage | Giriş | VAS çıkış sinyali |
| K1 Feedback | Geri besleme | Çıkış geri besleme noktası |
| K1 Termal | Bağlantı | Soğutucu bağlantısı |
| K1 Koruma | Bağlantı | Overcurrent, DC offset |
| K1 Hoparlör | Çıkış | Speaker binding posts |
| K1 Güç Kaynağı | Alt | ±35V dual rail |

### 4.7 · `k1-donanim/mjle21194-93.md`

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

### 4.8 · `k1-donanim/mjle21194-93.md`

| Parametre | MJL21194 | MJL21193 | Birim |
|-----------|----------|----------|-------|
| VBE(sat) | 1.5 | -1.5 | V @ IC=8A |
| VCE(sat) | 1.2 | -1.2 | V @ IC=8A, IB=0.8A |
| ICES | 50 | -50 | µA @ VCE=200V |
| IEBO | 50 | -50 | µA @ VEB=4V |
| Cob | 100 | 100 | pF @ VCE=10V |

### 4.9 · `k1-donanim/mjle21194-93.md`

| Parametre | MJL21194 | MJL21193 | Birim |
|-----------|----------|----------|-------|
| hFE | 80-200 | 80-200 | - |
| fT | 4 | 4 | MHz |
| Cob | 100 | 100 | pF |
| Kurykcı | 500 | 500 | MHz (gain-bandwidth) |

### 4.10 · `k1-donanim/mjle21194-93.md`

| Durum | IC | VCE | PC | Sıcaklık Yükselmesi |
|-------|-----|------|-----|---------------------|
| Boşta (Idle) | 50mA | 70V | 3.5W | 4.3°C |
| 1W Çıkış | 120mA | 35V | 4.2W | 5.1°C |
| 50W Çıkış | 1.8A | 19.4V | 35W | 42.7°C |
| 100W Çıkış | 3.5A | 10V | 35W | 42.7°C |
| 250W Çıkış | 5.6A | 6.25V | 35W | 42.7°C |

## 5. Bölüm Yol Haritası (kaynak başlıkları)

| # | Kaynak | Seviye | Başlık |
|---:|---|---:|---|
| 1 | `k1-donanim/class-ab-amplifikator.md` | H1 | Class AB Amplifikatör Devresi |
| 2 | `k1-donanim/class-ab-amplifikator.md` | H2 | Genel Bakış |
| 3 | `k1-donanim/class-ab-amplifikator.md` | H2 | Teknik Spesifikasyonlar |
| 4 | `k1-donanim/class-ab-amplifikator.md` | H2 | Devre Şeması - Genel Görünüm |
| 5 | `k1-donanim/class-ab-amplifikator.md` | H2 | Devre Tasarımı - Detaylı |
| 6 | `k1-donanim/class-ab-amplifikator.md` | H3 | Differential Pair Input Stage |
| 7 | `k1-donanim/class-ab-amplifikator.md` | H3 | Voltage Amplification Stage (VAS) |
| 8 | `k1-donanim/class-ab-amplifikator.md` | H3 | Push-Pull Output Stage |
| 9 | `k1-donanim/class-ab-amplifikator.md` | H2 | Bileşen Listesi |
| 10 | `k1-donanim/class-ab-amplifikator.md` | H2 | Bağımlılıklar |
| 11 | `k1-donanim/class-ab-amplifikator.md` | H2 | Durum: Implementasyon |
| 12 | `k1-donanim/output-stage.md` | H1 | Push-Pull Output Stage |
| 13 | `k1-donanim/output-stage.md` | H2 | Genel Bakış |
| 14 | `k1-donanim/output-stage.md` | H2 | Teknik Spesifikasyonlar |
| 15 | `k1-donanim/output-stage.md` | H2 | Push-Pull Konfigürasyon |
| 16 | `k1-donanim/output-stage.md` | H3 | Tek transistor (Single-ended output) |
| 17 | `k1-donanim/output-stage.md` | H3 | Push-Pull Output |
| 18 | `k1-donanim/output-stage.md` | H2 | Darlington Configuration |
| 19 | `k1-donanim/output-stage.md` | H3 | Neden Darlington? |
| 20 | `k1-donanim/output-stage.md` | H3 | Darlington Emitters Follower |
| 21 | `k1-donanim/output-stage.md` | H2 | Thermal Tracking |
| 22 | `k1-donanim/output-stage.md` | H3 | Crossover Distortion Sorunu |
| 23 | `k1-donanim/output-stage.md` | H3 | Çözüm: Thermal Tracking |
| 24 | `k1-donanim/output-stage.md` | H2 | Bileşen Değerleri |
| 25 | `k1-donanim/output-stage.md` | H2 | Akım Yolu Analizi |
| 26 | `k1-donanim/output-stage.md` | H3 | Pozitif Yarım Döngü (MJL21194 Active) |
| 27 | `k1-donanim/output-stage.md` | H3 | Negatif Yarım Döngü (MJL21193 Active) |
| 28 | `k1-donanim/output-stage.md` | H2 | Bağımlılıklar |
| 29 | `k1-donanim/output-stage.md` | H2 | Durum: Implementasyon |
| 30 | `k1-donanim/mjle21194-93.md` | H1 | MJL21194/93 NPN/PNP Transistörler |
| 31 | `k1-donanim/mjle21194-93.md` | H2 | Genel Bakış |
| 32 | `k1-donanim/mjle21194-93.md` | H2 | Teknik Spesifikasyonlar |
| 33 | `k1-donanim/mjle21194-93.md` | H2 | DC Karakteristikleri (Tipik @ 25°C) |
| 34 | `k1-donanim/mjle21194-93.md` | H2 | AC Karakteristikleri (Tipik @ 25°C) |
| 35 | `k1-donanim/mjle21194-93.md` | H2 | Devre Bağlantıları |
| 36 | `k1-donanim/mjle21194-93.md` | H3 | Push-Pull Konfigürasyon |
| 37 | `k1-donanim/mjle21194-93.md` | H3 | Thermal Tracking (Bias Transistörleri) |
| 38 | `k1-donanim/mjle21194-93.md` | H2 | Termal Hesaplamalar |
| 39 | `k1-donanim/mjle21194-93.md` | H3 | Güç Tüketimi (Tipik Usage) |
| 40 | `k1-donanim/mjle21194-93.md` | H3 | Soğutucu Gereksinimi |
| 41 | `k1-donanim/mjle21194-93.md` | H2 | Bağımlılıklar |
| 42 | `k1-donanim/mjle21194-93.md` | H2 | Durum: Implementasyon |

## 6. Kaynak İçerik (ham, satır bozulmadan)

### 6.1 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/class-ab-amplifikator.md`


### Class AB Amplifikatör Devresi

#### Genel Bakış

Class AB amplifikatör, COREMUSIC'ın ses sinyalinin son güçlendirme aşamasıdır. Differential pair input, voltage amplification stage (VAS) ve push-pull output stage olmak üzere üç ana bölümden oluşur. MJL21194/93 çıkış transistörleri ile 250W RMS çıkış gücü sağlar.

#### Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Çıkış Gücü | 250W RMS (8Ω, %0.5 THD) |
| Frekans Aralığı | 5Hz – 80kHz (±0.5dB) |
| THD+N | < %0.001 (1kHz, 1W) |
| Sinyal/Gürültü | > 120dB (A-Weighted) |
| Damaping Faktörü | > 200 (8Ω) |
| Giriş Empedansı | 47kΩ (differential) |
| Giriş Hassasiyeti | 1.5Vrms (tam çıkış için) |
| Kazanç | 26dB (20x) |
| Güç Topolojisi | ±35V Dual Rail |

#### Devre Şeması - Genel Görünüm

```
                           ±35V
                            │
                            ▼
┌─────────────────────────────────────────────────────────┐
│                   CLASS AB AMPLİFİKATÖR                 │
│                                                         │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐          │
│  │ DIFF     │    │   VAS    │    │  OUTPUT  │          │
│  │ PAIR     │───▶│  STAGE   │───▶│  STAGE   │───▶ SPK  │
│  │ INPUT    │    │          │    │          │          │
│  └────┬─────┘    └──────────┘    └──────────┘          │
│       │                                                 │
│       │         ┌──────────┐                            │
│       └────────▶│ FEEDBACK │◀───── Output              │
│                 │ NETWORK  │                            │
│                 └──────────┘                            │
└─────────────────────────────────────────────────────────┘
```

#### Devre Tasarımı - Detaylı

##### Differential Pair Input Stage

```
                    +35V
                     │
                     ▼
                 ┌───┴───┐
                 │  R3   │ 100Ω (Tail direnç)
                 └───┬───┘
                     │
            ┌────────┴────────┐
            │                  │
         ┌──┴──┐            ┌──┴──┐
         │ Q1  │            │ Q2  │  BC560C (PNP)
         │NPN  │            │PNP  │
         └──┬──┘            └──┬──┘
            │                  │
         ┌──┴──┐            ┌──┴──┐
         │  R1 │            │  R2 │  100Ω (Emitter)
         └──┬──┘            └──┬──┘
            │                  │
            ▼                  ▼
         Input(+)          Input(-)
         (Non-inv)         (Inverting)
```

##### Voltage Amplification Stage (VAS)

```
            ┌─────────────────────────────┐
            │         VAS STAGE           │
            │                             │
         ┌──┴──┐                       ┌──┴──┐
         │ Q3  │                       │ Q4  │  MPSA06 (NPN)
         │NPN  │                       │NPN  │
         └──┬──┘                       └──┬──┘
            │                             │
            ▼                             ▼
         Collector                      Collector
            │                             │
         ┌──┴──┐                       ┌──┴──┐
         │  Cm │  10pF Miller          │  R4 │  1kΩ
         └──┬──┘  Compansasyon          └──┬──┘
            │                             │
            └──────────┬──────────────────┘
                       │
                    -35V
```

##### Push-Pull Output Stage

```
                    +35V
                     │
                 ┌───┴───┐
                 │  R5   │  0.22Ω (Emitter)
                 └───┬───┘
                     │
                 ┌───┴───┐
                 │ MJL   │  MJL21194 (NPN)
                 │21194  │  250W/200V/16A
                 └───┬───┘
                     │
                     ├─────────────────── Output
                     │
                 ┌───┴───┐
                 │ MJL   │  MJL21193 (PNP)
                 │21193  │  250W/200V/16A
                 └───┬───┘
                     │
                 ┌───┴───┐
                 │  R6   │  0.22Ω (Emitter)
                 └───┬───┘
                     │
                    -35V
```

#### Bileşen Listesi

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Giriş Transistörleri | BC560C | 2 | PNP low-noise |
| 2 | VAS Transistörleri | MPSA06 | 2 | NPN high-voltage |
| 3 | Çıkış Transistörleri | MJL21194 | 4 | NPN power |
| 4 | Çıkış Transistörleri | MJL21193 | 4 | PNP power |
| 5 | Bias Transistörleri | BD139/140 | 2 | Thermal tracking |
| 6 | Miller Kondansatörü | 10pF C0G | 1 | Frequency compensation |
| 7 | Emitter Dirençleri | 0.22Ω 5W | 8 | Current sharing |
| 8 | Feedback Direnci | 20kΩ | 1 | Gain ayarı |
| 9 | Feedback Direnç | 1kΩ | 1 | Gain ayarı |

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 AK4458 | Giriş | DAC diferansiyel çıkış |
| K1 Feedback Network | Geri besleme | Negatif geri besleme ağı |
| K1 Güç Kaynağı | Alt | ±35V dual rail |
| K1 Termal | Bağlantı | Soğutucu bağlantısı |
| K1 Koruma | Çıkış | DC offset, overcurrent koruması |

#### Durum: Implementasyon

**Durum**: 🟡 Simülasyon Aşamasında

- LTSpice simülasyonu tamamlandı, THD < %0.001 doğrulandı
- Termal simülasyon: MJL21194 için RθJC = 1.22°C/W
- PCB layout: Star grounding uygulandı
- Soğutucu: Alüminyum ekstrüzyon, 150mm uzunluk
- Bias akımı: 50mA idle (Class AB crossover region)


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


### 6.3 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/mjle21194-93.md`


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


## 7. Kenar Durumlar

| # | Kenar durum | Etki | Kaynak / Durum |
|---:|---|---|---|
| 1 | Quiescent akım hedef değeri kaynakta sayısal olarak doğrulanamıyor | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 2 | Crossover distorsiyon ölçümü vault'ta yok | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |

## 8. Hata Modları

1. Bias drift'i → termal kaçak ve sıcak nokta (kaynak: `class-ab-amplifikator`).
2. Yük kısa devresinde koruma tetiklenmemesi → transistör kaybı (kaynak: `koruma-devreleri` çapraz referans).

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

- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/class-ab-amplifikator.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/output-stage.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/mjle21194-93.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` bu dosya üretildikten sonra `vault-utf8-writer verify` ile BOM/UTF-8 kontrolü yapılır.
- `⚠️ VERIFICATION REQUIRED` — kaynakta bulunmayan ölçüm/sürüm/ADR değerleri bu dosyaya yazılmamıştır.
- Klasör geneli bağımlılıklar ve okuma sırası: `[[index]]`.

---
*K010 · Class AB Amplifikatör + Çıkış Aşaması — SSOT: `.ai/architecture/k010-class-ab-cikis/`; ham kaynak: `_backup/arch-2026-10-06_1057/architecture/`.*
