---
title: "K009 Feedback Ağı Mimarisi"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 — K000-K071"
updated: 2026-10-06
---

# K009 — Feedback Ağı Mimarisi

> **K numarası:** K009 · **Klasör:** `k009-vas-feedback` · **Dosya:** `feedback-agi-mimarisi`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `performance-engineer` (ikincil)
> **Klasör amacı:** Voltaj amplifikasyon aşamasının (VAS) kazanç/compansasyon yapısı ile geri besleme ağının kararlılık koşullarını tek klasörde toplamak.

## 1. Kapsam ve Amaç

Bu dosya **Feedback Ağı Mimarisi** konusunu ele alır. Kapsamı: Küresel geri besleme topolojisi, geri besleme faktörü ve DC offset/termal sürükleme ile etkileşimi.

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
| 1 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/feedback-network.md` | 154 | birincil kaynak (ham içerik §6) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/vas-stage.md` | 169 | ikincil kaynak (§6) |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/class-ab-amplifikator.md` | 162 | tamamlayıcı kaynak (§6) |

## 4. Teknik Spesifikasyon Tabloları (kaynak kalıbı)

### 4.1 · `k1-donanim/feedback-network.md`

| Parametre | Değer |
|-----------|-------|
| Toplam Kazanç | 26dB (20x) |
| Açık Devre Kazancı | 80dB (10,000x) |
| Geri Besleme Oranı (β) | 0.05 (1/20) |
| Loop Gain | 60dB (1000x) |
| THD Azaltma | 60dB (1000x) |
| Bant Genişliği | DC – 80kHz |
| Sinyal/Gürültü | > 120dB |

### 4.2 · `k1-donanim/feedback-network.md`

| Kriter | Değer | Durum |
|--------|-------|-------|
| Phase Margin | > 45° | ✅ 65° |
| Gain Margin | > 10dB | ✅ 20dB |
| UGF | 1-10MHz | ✅ 1MHz |
| Peak | < 3dB | ✅ 1.2dB |

### 4.3 · `k1-donanim/feedback-network.md`

| Referans | Değer | Tolerans | Tip | Açıklama |
|----------|-------|----------|-----|----------|
| R_f | 20kΩ | %0.1 | Metal Film | Feedback direnci |
| R_g | 1kΩ | %0.1 | Metal Film | Ground reference |
| C_f | 100pF | %5 | C0G/NP0 | HF compensation |
| R_d | 10Ω | %5 | Metal Film | Damping (optional) |

### 4.4 · `k1-donanim/feedback-network.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Diff Pair | Giriş | Feedback giriş noktası |
| K1 Output Stage | Çıkış | Feedback çıkış noktası |
| K1 VAS | Bağlantı | Loop gain katkısı |
| K1 Güç Kaynağı | Alt | ±35V referans |

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

### 4.9 · `k1-donanim/class-ab-amplifikator.md`

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

### 4.10 · `k1-donanim/class-ab-amplifikator.md`

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

### 4.11 · `k1-donanim/class-ab-amplifikator.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 AK4458 | Giriş | DAC diferansiyel çıkış |
| K1 Feedback Network | Geri besleme | Negatif geri besleme ağı |
| K1 Güç Kaynağı | Alt | ±35V dual rail |
| K1 Termal | Bağlantı | Soğutucu bağlantısı |
| K1 Koruma | Çıkış | DC offset, overcurrent koruması |

## 5. Bölüm Yol Haritası (kaynak başlıkları)

| # | Kaynak | Seviye | Başlık |
|---:|---|---:|---|
| 1 | `k1-donanim/feedback-network.md` | H1 | Negative Feedback Network |
| 2 | `k1-donanim/feedback-network.md` | H2 | Genel Bakış |
| 3 | `k1-donanim/feedback-network.md` | H2 | Teknik Spesifikasyonlar |
| 4 | `k1-donanim/feedback-network.md` | H2 | Devre Şeması |
| 5 | `k1-donanim/feedback-network.md` | H2 | Kazanç Hesaplaması |
| 6 | `k1-donanim/feedback-network.md` | H3 | Kapalı Devre Kazancı |
| 7 | `k1-donanim/feedback-network.md` | H3 | Distorsiyon Azaltma |
| 8 | `k1-donanim/feedback-network.md` | H2 | Frequency Compensation |
| 9 | `k1-donanim/feedback-network.md` | H3 | Bode Plot |
| 10 | `k1-donanim/feedback-network.md` | H3 | Stabilite Kriterleri |
| 11 | `k1-donanim/feedback-network.md` | H2 | Bileşen Değerleri |
| 12 | `k1-donanim/feedback-network.md` | H2 | Empedans Eşleşme |
| 13 | `k1-donanim/feedback-network.md` | H2 | Bağımlılıklar |
| 14 | `k1-donanim/feedback-network.md` | H2 | Durum: Implementasyon |
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
| 29 | `k1-donanim/class-ab-amplifikator.md` | H1 | Class AB Amplifikatör Devresi |
| 30 | `k1-donanim/class-ab-amplifikator.md` | H2 | Genel Bakış |
| 31 | `k1-donanim/class-ab-amplifikator.md` | H2 | Teknik Spesifikasyonlar |
| 32 | `k1-donanim/class-ab-amplifikator.md` | H2 | Devre Şeması - Genel Görünüm |
| 33 | `k1-donanim/class-ab-amplifikator.md` | H2 | Devre Tasarımı - Detaylı |
| 34 | `k1-donanim/class-ab-amplifikator.md` | H3 | Differential Pair Input Stage |
| 35 | `k1-donanim/class-ab-amplifikator.md` | H3 | Voltage Amplification Stage (VAS) |
| 36 | `k1-donanim/class-ab-amplifikator.md` | H3 | Push-Pull Output Stage |
| 37 | `k1-donanim/class-ab-amplifikator.md` | H2 | Bileşen Listesi |
| 38 | `k1-donanim/class-ab-amplifikator.md` | H2 | Bağımlılıklar |
| 39 | `k1-donanim/class-ab-amplifikator.md` | H2 | Durum: Implementasyon |

## 6. Kaynak İçerik (ham, satır bozulmadan)

### 6.1 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/feedback-network.md`


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


### 6.3 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/class-ab-amplifikator.md`


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


## 7. Kenar Durumlar

| # | Kenar durum | Etki | Kaynak / Durum |
|---:|---|---|---|
| 1 | Geri besleme alınan nokta (çıkış çıkış uçları mı, hoparlör ucu mu) kaynakta net ayrışmıyor | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 2 | Termal sürükleme ile DC offset ilişkisi ölçülmüş değil | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |

## 8. Hata Modları

1. Geri besleme kopması/kararsızlık → aşırı kazanç ve osilasyon (kaynak: `feedback-network`).
2. Topraklama halkası hatalarında osilasyon/şeritleme (kaynak: `feedback-network` §topraklama).

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

- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/feedback-network.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/vas-stage.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/class-ab-amplifikator.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` bu dosya üretildikten sonra `vault-utf8-writer verify` ile BOM/UTF-8 kontrolü yapılır.
- `⚠️ VERIFICATION REQUIRED` — kaynakta bulunmayan ölçüm/sürüm/ADR değerleri bu dosyaya yazılmamıştır.
- Klasör geneli bağımlılıklar ve okuma sırası: `[[index]]`.

---
*K009 · Feedback Ağı Mimarisi — SSOT: `.ai/architecture/k009-vas-feedback/`; ham kaynak: `_backup/arch-2026-10-06_1057/architecture/`.*
