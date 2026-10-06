---
title: "K008 Diff-Pair Giriş Aşaması"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 — K000-K071"
updated: 2026-10-06
---

# K008 — Diff-Pair Giriş Aşaması

> **K numarası:** K008 · **Klasör:** `k008-analog-giris` · **Dosya:** `diff-pair-giris-asamasi`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `performance-engineer` (ikincil)
> **Klasör amacı:** XLR/TRS konnektörden başlayan, koruma/EMI aşamasından geçip diff-pair giriş evresine ulaşan analog giriş yolunu ve empedans/seviye şartlarını tanımlamak.

## 1. Kapsam ve Amaç

Bu dosya **Diff-Pair Giriş Aşaması** konusunu ele alır. Kapsamı: Differential giriş evresinin simetrisi, CMRR etkisi ve VAS'a devri.

Yazı, salt-okunur yedek kaynaklardan türetilmiştir; her teknik değer aşağıda
belirtilen kaynak dosyalarında bulunmak zorundadır. Kaynakta bulunmayan her değer
`⚠️ VERIFICATION REQUIRED` ile işaretlenir (ZERO-HALLUCINATION).

## 2. Sinyal / Donanım Akışı

```
      [ XLR / TRS konnektör ]
            │
            ▼
      [ Koruma + EMI filtre ]
            │
            ▼
      [ Empedans eşleme ]
            │
            ▼
      [ Diff-pair giriş evresi ]
            │
            ▼
      [ VAS amplifikatör ]
```

**Akış notları:**

1. **XLR / TRS konnektör** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
2. **Koruma + EMI filtre** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
3. **Empedans eşleme** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
4. **Diff-pair giriş evresi** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
5. **VAS amplifikatör** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.

## 3. Kaynak Envanteri

| # | Kaynak dosya (salt-okunur yedek) | Satır | Bu dosyadaki rolü |
|---|---|---:|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/diff-pair-input.md` | 131 | birincil kaynak (ham içerik §6) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/vas-stage.md` | 169 | ikincil kaynak (§6) |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/analog-sinyal-yolu.md` | 219 | tamamlayıcı kaynak (§6) |

## 4. Teknik Spesifikasyon Tabloları (kaynak kalıbı)

### 4.1 · `k1-donanim/diff-pair-input.md`

| Parametre | Değer |
|-----------|-------|
| Giriş Empedansı | 47kΩ (differential) |
| Giriş Hassasiyeti | 1.5Vrms (tam çıkış için) |
| CMRR | > 100dB @ 1kHz |
| THD | < %0.0005 (1kHz, 1Vrms) |
| Giriş Gürültüsü | < 1nV/√Hz |
| Bias Akımı | 1mA (tail current) |
| Tail Direnci | 100Ω |
| Transistör | BC560C (PNP, matched pair) |
| RθJC (BC560C) | 200°C/W |

### 4.2 · `k1-donanim/diff-pair-input.md`

| Parametre | Değer |
|-----------|-------|
| ITail | 1mA (tasarım değeri) |
| IC1 = IC2 | 0.5mA (her biri) |
| VCE | ~35V (her transistör) |
| gm | 19.2 mA/V (IC/VT, VT=26mV) |
| rπ | 5.2kΩ (β/gm, β=100) |
| r0 | 100kΩ (Early voltage consideration) |

### 4.3 · `k1-donanim/diff-pair-input.md`

| Parametre | Değer |
|-----------|-------|
| gm | 19.2 mA/V |
| RE (tail) | 100Ω |
| CMRR (hesaplanan) | 100.8 dB |
| CMRR (hedef) | > 100dB |

### 4.4 · `k1-donanim/diff-pair-input.md`

| Kaynak | Değer | Etki |
|--------|-------|------|
| Thermal (RTail) | 1.29 nV/√Hz | Düşük |
| Shot (IC) | 0.28 nV/√Hz | Düşük |
| Flicker (1/f) | ~5 nV/√Hz @ 10Hz | Orta |
| Toplam Giriş | < 1 nV/√Hz @ 1kHz | Kabul edilebilir |

### 4.5 · `k1-donanim/vas-stage.md`

| Parametre | Değer |
|-----------|-------|
| Gerilim Kazancı | 40-60dB (100x-316x) |
| Frekans Bant Genişliği | DC – 1MHz |
| Miller Kondansatörü | 10pF C0G |
| Gain-Bandwidth Product | 40MHz |
| Çıkış Empedansı | > 10kΩ |
| THD Katkısı | < %0.0001 |
| MPSA06 VCEO | 80V |
| MPSA06 IC | 500mA |
| MPSA06 hFE | 30-300 |

### 4.6 · `k1-donanim/vas-stage.md`

| Parametre | Değer |
|-----------|-------|
| Dominant Pole | 15.9 kHz |
| Second Pole | 1.2 MHz |
| Phase Margin | 65° |
| Gain Margin | 20dB |
| UGF (Unity Gain) | 40MHz |

### 4.7 · `k1-donanim/vas-stage.md`

| Referans | Değer | Tip | Açıklama |
|----------|-------|-----|----------|
| Q3 | MPSA06 | NPN | Ana VAS transistörü |
| R1 | 1kΩ 1/4W | Metal Film | Collector load |
| R2 | 100Ω 1/4W | Metal Film | Emitter degeneration |
| Cm | 10pF | C0G/NP0 | Miller compensation |
| D1 | BAT54S | Schottky | Output clamp (optional) |

### 4.8 · `k1-donanim/vas-stage.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Diff Pair | Giriş | Differential pair çıkışı |
| K1 Output Stage | Çıkış | Push-pull output'a sinyal |
| K1 Feedback | Geri besleme | Geri besleme noktası |
| K1 Güç Kaynağı | Alt | ±35V besleme |

### 4.9 · `k1-donanim/analog-sinyal-yolu.md`

| Parametre | Değer |
|-----------|-------|
| Toplam Kazanç | 26dB (20x) |
| Frekans Aralığı | 5Hz – 80kHz (±0.5dB) |
| THD+N | < %0.001 (1kHz, 1W) |
| Sinyal/Gürültü | > 120dB (A-Weighted) |
| Giriş Empedansı | 47kΩ (balanced XLR) |
| Çıkış Empedansı | < 0.1Ω |
| Giriş Hassasiyeti | 1.5Vrms (tam çıkış için) |
| Maks. Giriş | 5Vrms (overload) |
| Maks. Çıkış | 28Vrms (250W @ 8Ω) |

### 4.10 · `k1-donanim/analog-sinyal-yolu.md`

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Input Filter R | 100Ω 0402 | 16 | EMI suppression |
| 2 | Input Filter C | 100pF C0G | 16 | Low-pass filter |
| 3 | Input Ferrite | BLM18AG601SN1 | 16 | EMI filter |
| 4 | Diff Pair R | 100Ω 1/4W | 8 | Emitter degeneration |
| 5 | VAS Load R | 1kΩ 1/4W | 2 | Collector load |
| 6 | Output Emitter R | 0.22Ω 5W | 8 | Current sharing |
| 7 | Feedback Rf | 20kΩ 0.1% | 2 | Gain setting |
| 8 | Feedback Rg | 1kΩ 0.1% | 2 | Gain setting |
| 9 | Zobel R | 10Ω 1/4W | 2 | Output damping |
| 10 | Zobel C | 100nF | 2 | Output damping |

### 4.11 · `k1-donanim/analog-sinyal-yolu.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Konnektörler | Giriş | XLR balanced input |
| K1 Diff Pair | Aşama 1 | Differential input |
| K1 VAS | Aşama 2 | Voltage amplification |
| K1 Output Stage | Aşama 3 | Power amplification |
| K1 Feedback | Geri besleme | Negative feedback |
| K1 Koruma | Çıkış | Speaker relay |
| K1 Hoparlör | Çıkış | Binding posts |

## 5. Bölüm Yol Haritası (kaynak başlıkları)

| # | Kaynak | Seviye | Başlık |
|---:|---|---:|---|
| 1 | `k1-donanim/diff-pair-input.md` | H1 | Differential Pair Input Stage |
| 2 | `k1-donanim/diff-pair-input.md` | H2 | Genel Bakış |
| 3 | `k1-donanim/diff-pair-input.md` | H2 | Teknik Spesifikasyonlar |
| 4 | `k1-donanim/diff-pair-input.md` | H2 | Devre Şeması |
| 5 | `k1-donanim/diff-pair-input.md` | H2 | Bias Current Hesaplaması |
| 6 | `k1-donanim/diff-pair-input.md` | H3 | Tail Current |
| 7 | `k1-donanim/diff-pair-input.md` | H3 | Operating Point |
| 8 | `k1-donanim/diff-pair-input.md` | H2 | CMRR Analizi |
| 9 | `k1-donanim/diff-pair-input.md` | H3 | CMRR Formülü |
| 10 | `k1-donanim/diff-pair-input.md` | H3 | hesaplama |
| 11 | `k1-donanim/diff-pair-input.md` | H2 | Gürültü Analizi |
| 12 | `k1-donanim/diff-pair-input.md` | H3 | Giriş Gürültüsü Kaynakları |
| 13 | `k1-donanim/diff-pair-input.md` | H2 | Bağımlılıklar |
| 14 | `k1-donanim/diff-pair-input.md` | H2 | Durum: Implementasyon |
| 15 | `k1-donanim/vas-stage.md` | H1 | Voltage Amplification Stage (VAS) |
| 16 | `k1-donanim/vas-stage.md` | H2 | Genel Bakış |
| 17 | `k1-donanim/vas-stage.md` | H2 | Teknik Spesifikasyonlar |
| 18 | `k1-donanim/vas-stage.md` | H2 | Devre Şeması |
| 19 | `k1-donanim/vas-stage.md` | H2 | Miller Compensation Analizi |
| 20 | `k1-donanim/vas-stage.md` | H3 | Neden Miller Compensation? |
| 21 | `k1-donanim/vas-stage.md` | H3 | Miller Etkisi Formülü |
| 22 | `k1-donanim/vas-stage.md` | H3 | Dominant Pole |
| 23 | `k1-donanim/vas-stage.md` | H2 | Bileşen Değerleri |
| 24 | `k1-donanim/vas-stage.md` | H2 | Frekans Tepkisi |
| 25 | `k1-donanim/vas-stage.md` | H2 | Stabilite Analizi |
| 26 | `k1-donanim/vas-stage.md` | H3 | Phase Margin Hesabı |
| 27 | `k1-donanim/vas-stage.md` | H2 | Bağımlılıklar |
| 28 | `k1-donanim/vas-stage.md` | H2 | Durum: Implementasyon |
| 29 | `k1-donanim/analog-sinyal-yolu.md` | H1 | Analog Sinyal Yolu |
| 30 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Genel Bakış |
| 31 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Teknik Spesifikasyonlar |
| 32 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Sinyal Yolu Diyagramı |
| 33 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Aşama 1: Giriş Filtresi |
| 34 | `k1-donanim/analog-sinyal-yolu.md` | H3 | Differential Input Filter |
| 35 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Aşama 2: Differential Pair |
| 36 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Aşama 3: VAS Stage |
| 37 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Aşama 4: Output Stage |
| 38 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Aşama 5: Feedback Network |
| 39 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Impedance Matching |
| 40 | `k1-donanim/analog-sinyal-yolu.md` | H3 | Input Impedance |
| 41 | `k1-donanim/analog-sinyal-yolu.md` | H3 | Inter-stage Impedance |
| 42 | `k1-donanim/analog-sinyal-yolu.md` | H3 | Output Impedance |
| 43 | `k1-donanim/analog-sinyal-yolu.md` | H2 | EMI Filtering |
| 44 | `k1-donanim/analog-sinyal-yolu.md` | H3 | Input EMI |
| 45 | `k1-donanim/analog-sinyal-yolu.md` | H3 | Output EMI |
| 46 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Bileşen Değerleri |
| 47 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Bağımlılıklar |
| 48 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Durum: Implementasyon |

## 6. Kaynak İçerik (ham, satır bozulmadan)

### 6.1 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/diff-pair-input.md`


### Differential Pair Input Stage

#### Genel Bakış

Differential pair input stage, Class AB amplifikatörün giriş evresidir. Diferansiyel sinyalleri alır,(Common Mode Rejection Ratio) yüksek CMRR ile gürültü bastırması sağlar ve VAS (Voltage Amplification Stage)'a düşük distorsiyonlu sinyal iletir. BC560C low-noise PNP transistörleri kullanılır.

#### Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Giriş Empedansı | 47kΩ (differential) |
| Giriş Hassasiyeti | 1.5Vrms (tam çıkış için) |
| CMRR | > 100dB @ 1kHz |
| THD | < %0.0005 (1kHz, 1Vrms) |
| Giriş Gürültüsü | < 1nV/√Hz |
| Bias Akımı | 1mA (tail current) |
| Tail Direnci | 100Ω |
| Transistör | BC560C (PNP, matched pair) |
| RθJC (BC560C) | 200°C/W |

#### Devre Şeması

```
                           +35V
                            │
                        ┌───┴───┐
                        │  R5   │ 100Ω (Tail Direnci)
                        └───┬───┘
                            │
                     ┌──────┴──────┐
                     │  Tail Node   │
                     │              │
                  ┌──┴──┐       ┌──┴──┐
                  │ Q1  │       │ Q2  │  BC560C (PNP)
                  │NPN  │       │PNP  │  Matched Pair
                  └──┬──┘       └──┬──┘
                     │             │
                  ┌──┴──┐       ┌──┴──┐
                  │  R1 │       │  R2 │  100Ω (Emitter)
                  └──┬──┘       └──┬──┘
                     │             │
                     │             │
                  ┌──┴──┐       ┌──┴──┐
                  │  R3 │       │  R4 │  47kΩ (Input)
                  └──┬──┘       └──┬──┘
                     │             │
                     ▼             ▼
                  Input(+)     Input(-)
                  (Non-Inv)    (Inverting)
```

#### Bias Current Hesaplaması

##### Tail Current

```
ITail = (V+ - VBE - V-) / RTail
ITail = (35V - 0.7V - (-35V)) / 100Ω
ITail = 70V / 100Ω = 700mA (maksimum)
```

##### Operating Point

| Parametre | Değer |
|-----------|-------|
| ITail | 1mA (tasarım değeri) |
| IC1 = IC2 | 0.5mA (her biri) |
| VCE | ~35V (her transistör) |
| gm | 19.2 mA/V (IC/VT, VT=26mV) |
| rπ | 5.2kΩ (β/gm, β=100) |
| r0 | 100kΩ (Early voltage consideration) |

#### CMRR Analizi

##### CMRR Formülü

```
CMRR = Ad / Acm

Ad = Differential Gain = gm × RC
Acm = Common-Mode Gain = gm × RC / (1 + 2 × gm × RE)

CMRR = 1 + 2 × gm × RE
```

##### hesaplama

| Parametre | Değer |
|-----------|-------|
| gm | 19.2 mA/V |
| RE (tail) | 100Ω |
| CMRR (hesaplanan) | 100.8 dB |
| CMRR (hedef) | > 100dB |

#### Gürültü Analizi

##### Giriş Gürültüsü Kaynakları

| Kaynak | Değer | Etki |
|--------|-------|------|
| Thermal (RTail) | 1.29 nV/√Hz | Düşük |
| Shot (IC) | 0.28 nV/√Hz | Düşük |
| Flicker (1/f) | ~5 nV/√Hz @ 10Hz | Orta |
| Toplam Giriş | < 1 nV/√Hz @ 1kHz | Kabul edilebilir |

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 AK4458 | Giriş | DAC diferansiyel çıkış |
| K1 VAS Stage | Çıkış | VAS girişine sinyal iletir |
| K1 Feedback | Geri besleme | Negatif geri besleme ağı |
| K1 Güç Kaynağı | Alt | ±35V besleme |

#### Durum: Implementasyon

**Durum**: 🟡 Simülasyon Aşamasında

- BC560C seçimi: VBE eşleme < 2mV, hFE eşleme < %5
- Matching fixture: Test düzeneği hazır
- Input coupling: DC coupled (no coupling capacitor)
- Input impedance: 47kΩ differential (standart audio)
- PCB placement: Symmetrical layout, short traces


### 6.2 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/vas-stage.md`


### Voltage Amplification Stage (VAS)

#### Genel Bakış

Voltage Amplification Stage (VAS), Class AB amplifikatörün orta evresidir. Differential pair input'tan gelen sinyali yüksek kazançla yükseltir ve output stage'a iletir. Miller compensation technique kullanılarak stabilite sağlanır. MPSA06 NPN transistörleri tercih edilir.

#### Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Gerilim Kazancı | 40-60dB (100x-316x) |
| Frekans Bant Genişliği | DC – 1MHz |
| Miller Kondansatörü | 10pF C0G |
| Gain-Bandwidth Product | 40MHz |
| Çıkış Empedansı | > 10kΩ |
| THD Katkısı | < %0.0001 |
| MPSA06 VCEO | 80V |
| MPSA06 IC | 500mA |
| MPSA06 hFE | 30-300 |

#### Devre Şeması

```
                           +35V
                            │
                        ┌───┴───┐
                        │  R1   │ 1kΩ (Collector Load)
                        └───┬───┘
                            │
                    ┌───────┴───────┐
                    │               │
                 ┌──┴──┐         ┌──┴──┐
                 │  Cm │  10pF   │  D1 │  BAT54S (Clamp)
                 │Miller│        └──┬──┘
                 └──┬──┘            │
                    │               │
                    │   Collector   │
                    │               │
                 ┌──┴──┐            │
                 │ Q3  │            │  MPSA06 (NPN)
                 │NPN  │◀───────────┘
                 └──┬──┘
                    │
                 Base ←── From Differential Pair Output
                    │
                 Emitter
                    │
                 ┌──┴──┐
                 │  R2 │  100Ω (Emitter Degeneration)
                 └──┬──┘
                    │
                   -35V
```

#### Miller Compensation Analizi

##### Neden Miller Compensation?

```
Açık devre kazancı (Av) çok yüksek olduğunda,
transistörün iç kapasitansı (Cob) Miller etkisi ile
büyür ve bant genişliğini daraltır.

Miller Kondansatörü (Cm) kontrollü bir şekilde
polarite splitsiyon stabilized ederek,
transistörün DC kazancını yüksek tutar,
AC kazancını ise istenen frecuency'de düşürür.
```

##### Miller Etkisi Formülü

```
AvMiller = Av_open × Cm / (Cm + 1)
AvMiller ≈ Av_open (eğer Cm >> 1)

f-3dB = 1 / (2π × Av × Rc × Cm)

Örnek:
Av = 1000 (60dB)
Rc = 1kΩ
Cm = 10pF

f-3dB = 1 / (2π × 1000 × 1000 × 10×10⁻¹²)
f-3dB = 15.9 kHz (input-referred)
```

##### Dominant Pole

| Parametre | Değer |
|-----------|-------|
| Dominant Pole | 15.9 kHz |
| Second Pole | 1.2 MHz |
| Phase Margin | 65° |
| Gain Margin | 20dB |
| UGF (Unity Gain) | 40MHz |

#### Bileşen Değerleri

| Referans | Değer | Tip | Açıklama |
|----------|-------|-----|----------|
| Q3 | MPSA06 | NPN | Ana VAS transistörü |
| R1 | 1kΩ 1/4W | Metal Film | Collector load |
| R2 | 100Ω 1/4W | Metal Film | Emitter degeneration |
| Cm | 10pF | C0G/NP0 | Miller compensation |
| D1 | BAT54S | Schottky | Output clamp (optional) |

#### Frekans Tepkisi

```
Kazanç (dB)
    │
 60 ┤──────────────────┐
    │                  │
 40 ┤                  │  -20dB/decade
    │                  │
 20 ┤                  │
    │                  │
  0 ┤                  └──────────────────── Frekans
    │
    └───┬────┬────┬────┬────┬────┬────┬───
       10Hz 100Hz 1kHz 10kHz 100kHz 1MHz

    ├─ DC Kazanç: 60dB (1000x)
    ├─ -3dB Noktası: 15.9kHz
    ├─ 0dB Noktası: 40MHz
    └─ Phase Margin: 65°
```

#### Stabilite Analizi

##### Phase Margin Hesabı

```
Dominant pole: fp1 = 15.9kHz
Second pole: fp2 = 1.2MHz

@ UGF (40MHz):
Phase = -90° (fp1) - arctan(40/1200) = -90° - 1.9° = -91.9°
Phase Margin = 180° - 91.9° = 88.1°

Sonuç: Sistem kararlı (PM > 45° gerekli)
```

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Diff Pair | Giriş | Differential pair çıkışı |
| K1 Output Stage | Çıkış | Push-pull output'a sinyal |
| K1 Feedback | Geri besleme | Geri besleme noktası |
| K1 Güç Kaynağı | Alt | ±35V besleme |

#### Durum: Implementasyon

**Durum**: 🟡 Simülasyon Aşamasında

- LTSpice simülasyonu tamamlandı
- AC analysis: 60dB DC kazanç, 40MHz UGF doğrulandı
- Transient analysis: Slew rate > 50V/µs
- DC operating point: IC = 5mA, VCE = 30V
- PCB placement: Short traces, close to differential pair


### 6.3 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/analog-sinyal-yolu.md`


### Analog Sinyal Yolu

#### Genel Bakış

Analog sinyal yolu, COREMUSIC'da DAC çıkışından hoparlörlere kadar olan tüm analog aşama zincirini kapsar. Impedance matching, EMI filtering ve signal integrity bu yolun temel tasarım kriterleridir. Her aşama düşük distorsiyon ve yüksek sinyal/gürültü oranı için optimize edilmiştir.

#### Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Toplam Kazanç | 26dB (20x) |
| Frekans Aralığı | 5Hz – 80kHz (±0.5dB) |
| THD+N | < %0.001 (1kHz, 1W) |
| Sinyal/Gürültü | > 120dB (A-Weighted) |
| Giriş Empedansı | 47kΩ (balanced XLR) |
| Çıkış Empedansı | < 0.1Ω |
| Giriş Hassasiyeti | 1.5Vrms (tam çıkış için) |
| Maks. Giriş | 5Vrms (overload) |
| Maks. Çıkış | 28Vrms (250W @ 8Ω) |

#### Sinyal Yolu Diyagramı

```
┌─────────────────────────────────────────────────────────────────┐
│                   ANALOG SİNYAL YOLU                           │
│                                                                 │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐  │
│  │  XLR     │───▶│  Input   │───▶│ Diff     │───▶│  VAS     │  │
│  │  Input   │    │  Filter  │    │  Pair    │    │  Stage   │  │
│  └──────────┘    └──────────┘    └──────────┘    └─────┬────┘  │
│                                                        │       │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐         │       │
│  │ Speaker  │◀───│  Output  │◀───│  Class   │◀────────┘       │
│  │ Relay    │    │  Filter  │    │  AB Amp  │                  │
│  └────┬─────┘    └──────────┘    └──────────┘                  │
│       │                                                         │
│       ▼                                                         │
│  ┌──────────┐                                                   │
│  │Binding   │                                                   │
│  │Post      │                                                   │
│  └──────────┘                                                   │
└─────────────────────────────────────────────────────────────────┘
```

#### Aşama 1: Giriş Filtresi

##### Differential Input Filter

```
XLR Input
     │
     ├─ Pin 2 (Hot) ──▶ R1 (100Ω) ──▶ C1 (100pF) ──▶ AGND
     │                                    │
     │                                    ▼
     │                              Diff Pair Base(+)
     │
     ├─ Pin 3 (Cold) ──▶ R2 (100Ω) ──▶ C2 (100pF) ──▶ AGND
     │                                    │
     │                                    ▼
     │                              Diff Pair Base(-)
     │
     └─ Pin 1 (GND) ──▶ AGND Plane

Low-pass filter:
f-3dB = 1 / (2π × R × C)
f-3dB = 1 / (2π × 100 × 100×10⁻¹²)
f-3dB = 15.9 MHz

EMI suppression: > 40dB @ 100MHz
```

#### Aşama 2: Differential Pair

```
Diff Pair Gain:
Ad = gm × RC
Ad = 19.2mA/V × 1kΩ
Ad = 19.2 (25.7dB)

Common-mode rejection:
CMRR > 100dB @ 1kHz
```

#### Aşama 3: VAS Stage

```
VAS Gain:
Av = gm × RC
Av = 5mA/V × 10kΩ
Av = 50 (34dB)

Total open-loop gain:
Aol = Ad × Av = 19.2 × 50 = 960 (59.6dB)
```

#### Aşama 4: Output Stage

```
Output Stage:
- Unity voltage gain (emitter follower)
- Current gain: hFE = 100 (Darlington)
- Output impedance: < 0.1Ω
```

#### Aşama 5: Feedback Network

```
Closed-loop gain:
Av(cl) = 1/β = (Rf + Rg) / Rg
Av(cl) = (20kΩ + 1kΩ) / 1kΩ
Av(cl) = 21 (26.4dB)
```

#### Impedance Matching

##### Input Impedance

```
XLR Balanced Input:
- Differential impedance: 47kΩ
- Common-mode impedance: 23.5kΩ per side
- Source impedance (typical): 100Ω
- Mismatch: < 1% (excellent)
```

##### Inter-stage Impedance

```
Diff Pair → VAS:
- Diff pair output Z: ~10kΩ
- VAS input Z: ~100kΩ
- Voltage divider: 100/(100+10) = 0.91 (91% transfer)

VAS → Output Stage:
- VAS output Z: ~10kΩ
- Output stage input Z: ~100kΩ (Darlington)
- Voltage divider: 100/(100+10) = 0.91 (91% transfer)
```

##### Output Impedance

```
Output Stage:
- Zout = R_emit / (1 + hFE)
- Zout = 0.22Ω / 101
- Zout = 2.2mΩ (open loop)
- With feedback: Zout(closed) = Zout / (1 + Aol×β)
- Zout(closed) = 2.2mΩ / 477 = 4.6µΩ (negligible)

Damping Factor:
DF = Zload / Zout = 8Ω / 4.6µΩ = 1,739,130
DF >> 200 (target) ✅
```

#### EMI Filtering

##### Input EMI

```
XLR Input EMI Filter:
- Ferrite bead: BLM18AG601SN1 (600Ω @ 100MHz)
- Filter cap: 100pF C0G
- Attenuation: > 40dB @ 100MHz
```

##### Output EMI

```
Output EMI Filter:
- Ferrite bead: BLM18AG601SN1 (600Ω @ 100MHz)
- Filter cap: 100pF C0G
- Zobel network: 10Ω + 100nF (damping)
```

#### Bileşen Değerleri

| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Input Filter R | 100Ω 0402 | 16 | EMI suppression |
| 2 | Input Filter C | 100pF C0G | 16 | Low-pass filter |
| 3 | Input Ferrite | BLM18AG601SN1 | 16 | EMI filter |
| 4 | Diff Pair R | 100Ω 1/4W | 8 | Emitter degeneration |
| 5 | VAS Load R | 1kΩ 1/4W | 2 | Collector load |
| 6 | Output Emitter R | 0.22Ω 5W | 8 | Current sharing |
| 7 | Feedback Rf | 20kΩ 0.1% | 2 | Gain setting |
| 8 | Feedback Rg | 1kΩ 0.1% | 2 | Gain setting |
| 9 | Zobel R | 10Ω 1/4W | 2 | Output damping |
| 10 | Zobel C | 100nF | 2 | Output damping |

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Konnektörler | Giriş | XLR balanced input |
| K1 Diff Pair | Aşama 1 | Differential input |
| K1 VAS | Aşama 2 | Voltage amplification |
| K1 Output Stage | Aşama 3 | Power amplification |
| K1 Feedback | Geri besleme | Negative feedback |
| K1 Koruma | Çıkış | Speaker relay |
| K1 Hoparlör | Çıkış | Binding posts |

#### Durum: Implementasyon

**Durum**: 🟡 Simülasyon Aşamasında

- THD: < %0.001 (LTSpice verified)
- SNR: > 120dB (calculated)
- Frequency response: 5Hz-80kHz ±0.5dB
- Impedance matching: Verified at all stages
- EMI filtering: Pre-compliance test passed
- PCB routing: Symmetrical layout planned


## 7. Kenar Durumlar

| # | Kenar durum | Etki | Kaynak / Durum |
|---:|---|---|---|
| 1 | CMRR ölçümü vault'ta yok | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 2 | Pair simetrisini sağlayan direnç toleransı (ör. %1) kaynakta belirtilmemiş | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |

## 8. Hata Modları

1. Pair asimetrisi durumunda ortak mod gürültünün diferansiyel sinyale dönüşmesi (kaynak: `diff-pair-input`).
2. Kaynak DC offset'in giriş evresinden geçmesi (kaynak: `vas-stage` ile çapraz referans).

## 9. Bağımlılık Matrisi

| Komşu K | Yön | İlişki | Kanıt |
|---|:---:|---|---|
| `[[../k009-vas-feedback/index]]` | ↓ | Giriş evresi VAS aşamasına besler | `.ai/architecture/k009-vas-feedback/index.md` |
| `[[../k011-guc-koruma-termal/index]]` | ↑ | Koruma devreleri giriş hattını da gözetler | `.ai/architecture/k011-guc-koruma-termal/index.md` |
| `[[../k013-pcb-hoparlor/index]]` | ↓ | Giriş pad'leri ve PCB yerleşim kuralları | `.ai/architecture/k013-pcb-hoparlor/index.md` |
| `[[../k006-dac-adc-zinciri/index]]` | ↑ | ADC girişi bu yolun dijital eşleniği | `.ai/architecture/k006-dac-adc-zinciri/index.md` |

Yerel dosyalar:

- `[[analog-sinyal-yolu]]` — Analog Sinyal Yolu
- `[[diff-pair-giris-asamasi]]` — Diff-Pair Giriş Aşaması

## 10. Kanıt ve Doğrulama

- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/diff-pair-input.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/vas-stage.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/analog-sinyal-yolu.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` bu dosya üretildikten sonra `vault-utf8-writer verify` ile BOM/UTF-8 kontrolü yapılır.
- `⚠️ VERIFICATION REQUIRED` — kaynakta bulunmayan ölçüm/sürüm/ADR değerleri bu dosyaya yazılmamıştır.
- Klasör geneli bağımlılıklar ve okuma sırası: `[[index]]`.

---
*K008 · Diff-Pair Giriş Aşaması — SSOT: `.ai/architecture/k008-analog-giris/`; ham kaynak: `_backup/arch-2026-10-06_1057/architecture/`.*
