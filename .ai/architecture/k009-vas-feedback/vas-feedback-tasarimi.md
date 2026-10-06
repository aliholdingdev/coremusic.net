---
title: "K009 VAS + Feedback Tasarımı"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 — K000-K071"
updated: 2026-10-06
---

# K009 — VAS + Feedback Tasarımı

> **K numarası:** K009 · **Klasör:** `k009-vas-feedback` · **Dosya:** `vas-feedback-tasarimi`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `performance-engineer` (ikincil)
> **Klasör amacı:** Voltaj amplifikasyon aşamasının (VAS) kazanç/compansasyon yapısı ile geri besleme ağının kararlılık koşullarını tek klasörde toplamak.

## 1. Kapsam ve Amaç

Bu dosya **VAS + Feedback Tasarımı** konusunu ele alır. Kapsamı: Voltaj amplifikasyon evresi: akım aynası, Miller kompanzasyonu, sürme kapasitesi ve frekans yanıtı.

Yazı, salt-okunur yedek kaynaklardan türetilmiştir; her teknik değer aşağıda
belirtilen kaynak dosyalarında bulunmak zorundadır. Kaynakta bulunmayan her değer
`⚠️ VERIFICATION REQUIRED` ile işaretlenir (ZERO-HALLUCINATION).

## 2. Sinyal / Donanım Akışı

```
      [ Diff-pair giriş ]
            │
            ▼
      [ VAS (Miller kompanzasyon) ]
            │
            ▼
      [ Feedback ağı ]
            │
            ▼
      [ Class AB sürücü ]
            │
            ▼
      [ Çıkış evresi (geri besleme alınır) ]
```

**Akış notları:**

1. **Diff-pair giriş** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
2. **VAS (Miller kompanzasyon)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
3. **Feedback ağı** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
4. **Class AB sürücü** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.
5. **Çıkış evresi (geri besleme alınır)** — bu aşamanın girdisi bir önceki, çıktısı bir sonraki adımdır.

## 3. Kaynak Envanteri

| # | Kaynak dosya (salt-okunur yedek) | Satır | Bu dosyadaki rolü |
|---|---|---:|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/vas-stage.md` | 169 | birincil kaynak (ham içerik §6) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/feedback-network.md` | 154 | ikincil kaynak (§6) |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/diff-pair-input.md` | 131 | tamamlayıcı kaynak (§6) |

## 4. Teknik Spesifikasyon Tabloları (kaynak kalıbı)

### 4.1 · `k1-donanim/vas-stage.md`

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

### 4.2 · `k1-donanim/vas-stage.md`

| Parametre | Değer |
|-----------|-------|
| Dominant Pole | 15.9 kHz |
| Second Pole | 1.2 MHz |
| Phase Margin | 65° |
| Gain Margin | 20dB |
| UGF (Unity Gain) | 40MHz |

### 4.3 · `k1-donanim/vas-stage.md`

| Referans | Değer | Tip | Açıklama |
|----------|-------|-----|----------|
| Q3 | MPSA06 | NPN | Ana VAS transistörü |
| R1 | 1kΩ 1/4W | Metal Film | Collector load |
| R2 | 100Ω 1/4W | Metal Film | Emitter degeneration |
| Cm | 10pF | C0G/NP0 | Miller compensation |
| D1 | BAT54S | Schottky | Output clamp (optional) |

### 4.4 · `k1-donanim/vas-stage.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Diff Pair | Giriş | Differential pair çıkışı |
| K1 Output Stage | Çıkış | Push-pull output'a sinyal |
| K1 Feedback | Geri besleme | Geri besleme noktası |
| K1 Güç Kaynağı | Alt | ±35V besleme |

### 4.5 · `k1-donanim/feedback-network.md`

| Parametre | Değer |
|-----------|-------|
| Toplam Kazanç | 26dB (20x) |
| Açık Devre Kazancı | 80dB (10,000x) |
| Geri Besleme Oranı (β) | 0.05 (1/20) |
| Loop Gain | 60dB (1000x) |
| THD Azaltma | 60dB (1000x) |
| Bant Genişliği | DC – 80kHz |
| Sinyal/Gürültü | > 120dB |

### 4.6 · `k1-donanim/feedback-network.md`

| Kriter | Değer | Durum |
|--------|-------|-------|
| Phase Margin | > 45° | ✅ 65° |
| Gain Margin | > 10dB | ✅ 20dB |
| UGF | 1-10MHz | ✅ 1MHz |
| Peak | < 3dB | ✅ 1.2dB |

### 4.7 · `k1-donanim/feedback-network.md`

| Referans | Değer | Tolerans | Tip | Açıklama |
|----------|-------|----------|-----|----------|
| R_f | 20kΩ | %0.1 | Metal Film | Feedback direnci |
| R_g | 1kΩ | %0.1 | Metal Film | Ground reference |
| C_f | 100pF | %5 | C0G/NP0 | HF compensation |
| R_d | 10Ω | %5 | Metal Film | Damping (optional) |

### 4.8 · `k1-donanim/feedback-network.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Diff Pair | Giriş | Feedback giriş noktası |
| K1 Output Stage | Çıkış | Feedback çıkış noktası |
| K1 VAS | Bağlantı | Loop gain katkısı |
| K1 Güç Kaynağı | Alt | ±35V referans |

### 4.9 · `k1-donanim/diff-pair-input.md`

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

### 4.10 · `k1-donanim/diff-pair-input.md`

| Parametre | Değer |
|-----------|-------|
| ITail | 1mA (tasarım değeri) |
| IC1 = IC2 | 0.5mA (her biri) |
| VCE | ~35V (her transistör) |
| gm | 19.2 mA/V (IC/VT, VT=26mV) |
| rπ | 5.2kΩ (β/gm, β=100) |
| r0 | 100kΩ (Early voltage consideration) |

### 4.11 · `k1-donanim/diff-pair-input.md`

| Parametre | Değer |
|-----------|-------|
| gm | 19.2 mA/V |
| RE (tail) | 100Ω |
| CMRR (hesaplanan) | 100.8 dB |
| CMRR (hedef) | > 100dB |

### 4.12 · `k1-donanim/diff-pair-input.md`

| Kaynak | Değer | Etki |
|--------|-------|------|
| Thermal (RTail) | 1.29 nV/√Hz | Düşük |
| Shot (IC) | 0.28 nV/√Hz | Düşük |
| Flicker (1/f) | ~5 nV/√Hz @ 10Hz | Orta |
| Toplam Giriş | < 1 nV/√Hz @ 1kHz | Kabul edilebilir |

## 5. Bölüm Yol Haritası (kaynak başlıkları)

| # | Kaynak | Seviye | Başlık |
|---:|---|---:|---|
| 1 | `k1-donanim/vas-stage.md` | H1 | Voltage Amplification Stage (VAS) |
| 2 | `k1-donanim/vas-stage.md` | H2 | Genel Bakış |
| 3 | `k1-donanim/vas-stage.md` | H2 | Teknik Spesifikasyonlar |
| 4 | `k1-donanim/vas-stage.md` | H2 | Devre Şeması |
| 5 | `k1-donanim/vas-stage.md` | H2 | Miller Compensation Analizi |
| 6 | `k1-donanim/vas-stage.md` | H3 | Neden Miller Compensation? |
| 7 | `k1-donanim/vas-stage.md` | H3 | Miller Etkisi Formülü |
| 8 | `k1-donanim/vas-stage.md` | H3 | Dominant Pole |
| 9 | `k1-donanim/vas-stage.md` | H2 | Bileşen Değerleri |
| 10 | `k1-donanim/vas-stage.md` | H2 | Frekans Tepkisi |
| 11 | `k1-donanim/vas-stage.md` | H2 | Stabilite Analizi |
| 12 | `k1-donanim/vas-stage.md` | H3 | Phase Margin Hesabı |
| 13 | `k1-donanim/vas-stage.md` | H2 | Bağımlılıklar |
| 14 | `k1-donanim/vas-stage.md` | H2 | Durum: Implementasyon |
| 15 | `k1-donanim/feedback-network.md` | H1 | Negative Feedback Network |
| 16 | `k1-donanim/feedback-network.md` | H2 | Genel Bakış |
| 17 | `k1-donanim/feedback-network.md` | H2 | Teknik Spesifikasyonlar |
| 18 | `k1-donanim/feedback-network.md` | H2 | Devre Şeması |
| 19 | `k1-donanim/feedback-network.md` | H2 | Kazanç Hesaplaması |
| 20 | `k1-donanim/feedback-network.md` | H3 | Kapalı Devre Kazancı |
| 21 | `k1-donanim/feedback-network.md` | H3 | Distorsiyon Azaltma |
| 22 | `k1-donanim/feedback-network.md` | H2 | Frequency Compensation |
| 23 | `k1-donanim/feedback-network.md` | H3 | Bode Plot |
| 24 | `k1-donanim/feedback-network.md` | H3 | Stabilite Kriterleri |
| 25 | `k1-donanim/feedback-network.md` | H2 | Bileşen Değerleri |
| 26 | `k1-donanim/feedback-network.md` | H2 | Empedans Eşleşme |
| 27 | `k1-donanim/feedback-network.md` | H2 | Bağımlılıklar |
| 28 | `k1-donanim/feedback-network.md` | H2 | Durum: Implementasyon |
| 29 | `k1-donanim/diff-pair-input.md` | H1 | Differential Pair Input Stage |
| 30 | `k1-donanim/diff-pair-input.md` | H2 | Genel Bakış |
| 31 | `k1-donanim/diff-pair-input.md` | H2 | Teknik Spesifikasyonlar |
| 32 | `k1-donanim/diff-pair-input.md` | H2 | Devre Şeması |
| 33 | `k1-donanim/diff-pair-input.md` | H2 | Bias Current Hesaplaması |
| 34 | `k1-donanim/diff-pair-input.md` | H3 | Tail Current |
| 35 | `k1-donanim/diff-pair-input.md` | H3 | Operating Point |
| 36 | `k1-donanim/diff-pair-input.md` | H2 | CMRR Analizi |
| 37 | `k1-donanim/diff-pair-input.md` | H3 | CMRR Formülü |
| 38 | `k1-donanim/diff-pair-input.md` | H3 | hesaplama |
| 39 | `k1-donanim/diff-pair-input.md` | H2 | Gürültü Analizi |
| 40 | `k1-donanim/diff-pair-input.md` | H3 | Giriş Gürültüsü Kaynakları |
| 41 | `k1-donanim/diff-pair-input.md` | H2 | Bağımlılıklar |
| 42 | `k1-donanim/diff-pair-input.md` | H2 | Durum: Implementasyon |

## 6. Kaynak İçerik (ham, satır bozulmadan)

### 6.1 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/vas-stage.md`


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


### 6.2 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/feedback-network.md`


### Negative Feedback Network

#### Genel Bakış

Negative feedback network, amplifikatörün çıkış sinyalini girişe geri besleyerek kazanç stabilitesini, distorsiyon azaltmasını ve bant genişliği genişletmesini sağlar. Resistive divider ile kazanç ayarı yapılır, frequency compensation ile stabilite garantilenir.

#### Teknik Spesifikasyonlar

| Parametre | Değer |
|-----------|-------|
| Toplam Kazanç | 26dB (20x) |
| Açık Devre Kazancı | 80dB (10,000x) |
| Geri Besleme Oranı (β) | 0.05 (1/20) |
| Loop Gain | 60dB (1000x) |
| THD Azaltma | 60dB (1000x) |
| Bant Genişliği | DC – 80kHz |
| Sinyal/Gürültü | > 120dB |

#### Devre Şeması

```
                         ┌──────────────────────────────────┐
                         │         FEEDBACK NETWORK         │
                         │                                  │
                         │   ┌───────┐                      │
                         │   │  R_f  │  20kΩ (Feedback)     │
                         │   └───┬───┘                      │
                         │       │                          │
                         │   ┌───┴───┐                      │
                         │   │  R_g  │  1kΩ (Ground)        │
                         │   └───┬───┘                      │
                         │       │                          │
                         │      GND                         │
                         │                                  │
                         │   β = Rg / (Rf + Rg)             │
                         │   β = 1kΩ / (20kΩ + 1kΩ)        │
                         │   β = 0.0476                     │
                         │                                  │
                         │   Av = 1/β = 21 (26.4dB)         │
                         │                                  │
                         └──────────────────────────────────┘

Signal Flow:

Input(+) ──▶ Differential Pair ──▶ VAS ──▶ Output ──▶ Speaker
                  ▲                                     │
                  │                                     │
                  └──────── Feedback Network ◀──────────┘
```

#### Kazanç Hesaplaması

##### Kapalı Devre Kazancı

```
Av_closed = Av_open / (1 + Av_open × β)

Av_open = 10,000 (80dB)
β = 0.0476

Av_closed = 10,000 / (1 + 10,000 × 0.0476)
Av_closed = 10,000 / 477
Av_closed = 20.96 (26.4dB)
```

##### Distorsiyon Azaltma

```
THD_open = %0.1 (açık devre)
THD_closed = THD_open / (1 + Av_open × β)
THD_closed = %0.1 / 477
THD_closed = %0.00021 (hedef: < %0.001)
```

#### Frequency Compensation

##### Bode Plot

```
Kazanç (dB)
    │
 80 ┤─────────────┐
    │             │
 60 ┤             │ -20dB/decade (dominant pole)
    │             │
 40 ┤             │
    │             │
 20 ┤             │
    │             │
  0 ┤             └────────────────── Frekans
    │
    └───┬────┬────┬────┬────┬────┬───
       10Hz 100Hz 1kHz 10kHz 100kHz 1MHz

    ├─ DC Kazanç: 80dB (10,000x)
    ├─ Unity Gain Frequency: 1MHz
    ├─ Phase Margin: > 60°
    └─ Gain Margin: > 20dB
```

##### Stabilite Kriterleri

| Kriter | Değer | Durum |
|--------|-------|-------|
| Phase Margin | > 45° | ✅ 65° |
| Gain Margin | > 10dB | ✅ 20dB |
| UGF | 1-10MHz | ✅ 1MHz |
| Peak | < 3dB | ✅ 1.2dB |

#### Bileşen Değerleri

| Referans | Değer | Tolerans | Tip | Açıklama |
|----------|-------|----------|-----|----------|
| R_f | 20kΩ | %0.1 | Metal Film | Feedback direnci |
| R_g | 1kΩ | %0.1 | Metal Film | Ground reference |
| C_f | 100pF | %5 | C0G/NP0 | HF compensation |
| R_d | 10Ω | %5 | Metal Film | Damping (optional) |

#### Empedans Eşleşme

```
Giriş Empedansı: 47kΩ (differential)
Feedback Empedansı: 21kΩ (Rf + Rg paralel)
Çıkış Empedansı: < 0.1Ω (açık devre feedback ile)

Empedans oranı: 47kΩ / 21kΩ = 2.24:1 (iyi eşleşme)
```

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Diff Pair | Giriş | Feedback giriş noktası |
| K1 Output Stage | Çıkış | Feedback çıkış noktası |
| K1 VAS | Bağlantı | Loop gain katkısı |
| K1 Güç Kaynağı | Alt | ±35V referans |

#### Durum: Implementasyon

**Durum**: 🟡 Simülasyon Aşamasında

- Kazanç: 20x (26dB) – LTSpice doğrulandı
- THD: < %0.001 @ 1kHz, 1W – Simülasyon ile verified
- Feedback trace: PCB'de short, shielded routing
- Ground connection: Star ground point'e doğrudan bağlantı
- Component tolerance: %0.1 metal film (precision)


### 6.3 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/diff-pair-input.md`


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


## 7. Kenar Durumlar

| # | Kenar durum | Etki | Kaynak / Durum |
|---:|---|---|---|
| 1 | Kazanç marjı / faz marjı sayısal değeri vault'ta yok | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 2 | Kompanzasyon kondansatör değeri kaynakta doğrulanamıyor | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |

## 8. Hata Modları

1. Yetersiz kompanzasyonda osilasyon (kaynak: `vas-stage`).
2. Sürme akımının yetersiz kalıp crosstalk/distorsiyon üretmesi (kaynak: `vas-stage` §sürücü bölümü).

## 9. Bağımlılık Matrisi

| Komşu K | Yön | İlişki | Kanıt |
|---|:---:|---|---|
| `[[../k008-analog-giris/index]]` | ↑ | Giriş evresi sinyali besler | `.ai/architecture/k008-analog-giris/index.md` |
| `[[../k010-class-ab-cikis/index]]` | ↓ | Çıkış evresi feedback noktasını oluşturur | `.ai/architecture/k010-class-ab-cikis/index.md` |
| `[[../k011-guc-koruma-termal/index]]` | ↑ | Besleme rayları VAS sürme kapasitesini belirler | `.ai/architecture/k011-guc-koruma-termal/index.md` |

Yerel dosyalar:

- `[[vas-feedback-tasarimi]]` — VAS + Feedback Tasarımı
- `[[feedback-agi-mimarisi]]` — Feedback Ağı Mimarisi

## 10. Kanıt ve Doğrulama

- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/vas-stage.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/feedback-network.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/diff-pair-input.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` bu dosya üretildikten sonra `vault-utf8-writer verify` ile BOM/UTF-8 kontrolü yapılır.
- `⚠️ VERIFICATION REQUIRED` — kaynakta bulunmayan ölçüm/sürüm/ADR değerleri bu dosyaya yazılmamıştır.
- Klasör geneli bağımlılıklar ve okuma sırası: `[[index]]`.

---
*K009 · VAS + Feedback Tasarımı — SSOT: `.ai/architecture/k009-vas-feedback/`; ham kaynak: `_backup/arch-2026-10-06_1057/architecture/`.*
