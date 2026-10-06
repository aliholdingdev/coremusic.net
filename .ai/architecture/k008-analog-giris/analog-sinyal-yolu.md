---
title: "K008 Analog Sinyal Yolu"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 — K000-K071"
updated: 2026-10-06
---

# K008 — Analog Sinyal Yolu

> **K numarası:** K008 · **Klasör:** `k008-analog-giris` · **Dosya:** `analog-sinyal-yolu`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `performance-engineer` (ikincil)
> **Klasör amacı:** XLR/TRS konnektörden başlayan, koruma/EMI aşamasından geçip diff-pair giriş evresine ulaşan analog giriş yolunu ve empedans/seviye şartlarını tanımlamak.

## 1. Kapsam ve Amaç

Bu dosya **Analog Sinyal Yolu** konusunu ele alır. Kapsamı: Konnektörden diff-pair'e kadar sinyalin fiziksel yolu: filtre, empedans, seviye ve koruma noktaları.

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
| 1 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/analog-sinyal-yolu.md` | 219 | birincil kaynak (ham içerik §6) |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/konnektorler.md` | 194 | ikincil kaynak (§6) |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/diff-pair-input.md` | 131 | tamamlayıcı kaynak (§6) |

## 4. Teknik Spesifikasyon Tabloları (kaynak kalıbı)

### 4.1 · `k1-donanim/analog-sinyal-yolu.md`

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

### 4.2 · `k1-donanim/analog-sinyal-yolu.md`

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

### 4.3 · `k1-donanim/analog-sinyal-yolu.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 Konnektörler | Giriş | XLR balanced input |
| K1 Diff Pair | Aşama 1 | Differential input |
| K1 VAS | Aşama 2 | Voltage amplification |
| K1 Output Stage | Aşama 3 | Power amplification |
| K1 Feedback | Geri besleme | Negative feedback |
| K1 Koruma | Çıkış | Speaker relay |
| K1 Hoparlör | Çıkış | Binding posts |

### 4.4 · `k1-donanim/konnektorler.md`

| Konnektör | Tip | Empedans | Maks. Frekans | Uygulama |
|-----------|-----|----------|---------------|----------|
| XLR | 3-pin balanced | 110Ω | 10MHz | Analog giriş/çıkış |
| RCA | Phono unbalanced | 75Ω | 100MHz | Analog giriş/çıkış |
| USB-C | 16-pin | 90Ω | 480Mbps | Dijital giriş |
| Optical | Toslink | 75Ω | 125Mbps | Dijital giriş |
| HDMI ARC | 19-pin | 100Ω | 3.4Gbps | TV ses çıkışı |
| Binding Post | 4mm banana | - | - | Hoparlör çıkışı |

### 4.5 · `k1-donanim/konnektorler.md`

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | Panel layout, drilling |
| K1 Amplifikatör | Bağlantı | XLR/RCA input |
| K1 Hoparlör | Çıkış | Binding posts |
| K1 USB Audio | Bağlantı | USB-C input |
| K1 Koruma | Bağlantı | Relay switching |

### 4.6 · `k1-donanim/diff-pair-input.md`

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

### 4.7 · `k1-donanim/diff-pair-input.md`

| Parametre | Değer |
|-----------|-------|
| ITail | 1mA (tasarım değeri) |
| IC1 = IC2 | 0.5mA (her biri) |
| VCE | ~35V (her transistör) |
| gm | 19.2 mA/V (IC/VT, VT=26mV) |
| rπ | 5.2kΩ (β/gm, β=100) |
| r0 | 100kΩ (Early voltage consideration) |

### 4.8 · `k1-donanim/diff-pair-input.md`

| Parametre | Değer |
|-----------|-------|
| gm | 19.2 mA/V |
| RE (tail) | 100Ω |
| CMRR (hesaplanan) | 100.8 dB |
| CMRR (hedef) | > 100dB |

### 4.9 · `k1-donanim/diff-pair-input.md`

| Kaynak | Değer | Etki |
|--------|-------|------|
| Thermal (RTail) | 1.29 nV/√Hz | Düşük |
| Shot (IC) | 0.28 nV/√Hz | Düşük |
| Flicker (1/f) | ~5 nV/√Hz @ 10Hz | Orta |
| Toplam Giriş | < 1 nV/√Hz @ 1kHz | Kabul edilebilir |

## 5. Bölüm Yol Haritası (kaynak başlıkları)

| # | Kaynak | Seviye | Başlık |
|---:|---|---:|---|
| 1 | `k1-donanim/analog-sinyal-yolu.md` | H1 | Analog Sinyal Yolu |
| 2 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Genel Bakış |
| 3 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Teknik Spesifikasyonlar |
| 4 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Sinyal Yolu Diyagramı |
| 5 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Aşama 1: Giriş Filtresi |
| 6 | `k1-donanim/analog-sinyal-yolu.md` | H3 | Differential Input Filter |
| 7 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Aşama 2: Differential Pair |
| 8 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Aşama 3: VAS Stage |
| 9 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Aşama 4: Output Stage |
| 10 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Aşama 5: Feedback Network |
| 11 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Impedance Matching |
| 12 | `k1-donanim/analog-sinyal-yolu.md` | H3 | Input Impedance |
| 13 | `k1-donanim/analog-sinyal-yolu.md` | H3 | Inter-stage Impedance |
| 14 | `k1-donanim/analog-sinyal-yolu.md` | H3 | Output Impedance |
| 15 | `k1-donanim/analog-sinyal-yolu.md` | H2 | EMI Filtering |
| 16 | `k1-donanim/analog-sinyal-yolu.md` | H3 | Input EMI |
| 17 | `k1-donanim/analog-sinyal-yolu.md` | H3 | Output EMI |
| 18 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Bileşen Değerleri |
| 19 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Bağımlılıklar |
| 20 | `k1-donanim/analog-sinyal-yolu.md` | H2 | Durum: Implementasyon |
| 21 | `k1-donanim/konnektorler.md` | H1 | Konnektörler - XLR, RCA, USB-C, Optical, HDMI ARC |
| 22 | `k1-donanim/konnektorler.md` | H2 | Genel Bakış |
| 23 | `k1-donanim/konnektorler.md` | H2 | Teknik Spesifikasyonlar |
| 24 | `k1-donanim/konnektorler.md` | H2 | XLR Konnektörü |
| 25 | `k1-donanim/konnektorler.md` | H3 | Pin Konfigürasyonu |
| 26 | `k1-donanim/konnektorler.md` | H3 | XLR Devre Bağlantısı |
| 27 | `k1-donanim/konnektorler.md` | H2 | RCA Konnektörü |
| 28 | `k1-donanim/konnektorler.md` | H3 | Pin Konfigürasyonu |
| 29 | `k1-donanim/konnektorler.md` | H2 | USB-C Konnektörü |
| 30 | `k1-donanim/konnektorler.md` | H3 | Pin Konfigürasyonu |
| 31 | `k1-donanim/konnektorler.md` | H2 | Optical (Toslink) |
| 32 | `k1-donanim/konnektorler.md` | H3 | Pin Konfigürasyonu |
| 33 | `k1-donanim/konnektorler.md` | H2 | HDMI ARC |
| 34 | `k1-donanim/konnektorler.md` | H3 | Pin Konfigürasyonu |
| 35 | `k1-donanim/konnektorler.md` | H2 | Binding Posts (Hoparlör Çıkışları) |
| 36 | `k1-donanim/konnektorler.md` | H3 | Pin Konfigürasyonu |
| 37 | `k1-donanim/konnektorler.md` | H2 | Panel Düzeni |
| 38 | `k1-donanim/konnektorler.md` | H2 | Bağımlılıklar |
| 39 | `k1-donanim/konnektorler.md` | H2 | Durum: Implementasyon |
| 40 | `k1-donanim/diff-pair-input.md` | H1 | Differential Pair Input Stage |
| 41 | `k1-donanim/diff-pair-input.md` | H2 | Genel Bakış |
| 42 | `k1-donanim/diff-pair-input.md` | H2 | Teknik Spesifikasyonlar |
| 43 | `k1-donanim/diff-pair-input.md` | H2 | Devre Şeması |
| 44 | `k1-donanim/diff-pair-input.md` | H2 | Bias Current Hesaplaması |
| 45 | `k1-donanim/diff-pair-input.md` | H3 | Tail Current |
| 46 | `k1-donanim/diff-pair-input.md` | H3 | Operating Point |
| 47 | `k1-donanim/diff-pair-input.md` | H2 | CMRR Analizi |
| 48 | `k1-donanim/diff-pair-input.md` | H3 | CMRR Formülü |
| 49 | `k1-donanim/diff-pair-input.md` | H3 | hesaplama |
| 50 | `k1-donanim/diff-pair-input.md` | H2 | Gürültü Analizi |
| 51 | `k1-donanim/diff-pair-input.md` | H3 | Giriş Gürültüsü Kaynakları |
| 52 | `k1-donanim/diff-pair-input.md` | H2 | Bağımlılıklar |
| 53 | `k1-donanim/diff-pair-input.md` | H2 | Durum: Implementasyon |

## 6. Kaynak İçerik (ham, satır bozulmadan)

### 6.1 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/analog-sinyal-yolu.md`


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


### 6.2 · `_backup/arch-2026-10-06_1057/architecture/k1-donanim/konnektorler.md`


### Konnektörler - XLR, RCA, USB-C, Optical, HDMI ARC

#### Genel Bakış

COREMUSIC konnektör sistemi, çeşitli ses ve kontrol arabirimleri için endüstri standartlarında bağlantı noktaları sağlar. Balanced XLR, unbalanced RCA, dijital USB-C, Optical (Toslink) ve HDMI ARC konnektörleri kullanılır.

#### Teknik Spesifikasyonlar

| Konnektör | Tip | Empedans | Maks. Frekans | Uygulama |
|-----------|-----|----------|---------------|----------|
| XLR | 3-pin balanced | 110Ω | 10MHz | Analog giriş/çıkış |
| RCA | Phono unbalanced | 75Ω | 100MHz | Analog giriş/çıkış |
| USB-C | 16-pin | 90Ω | 480Mbps | Dijital giriş |
| Optical | Toslink | 75Ω | 125Mbps | Dijital giriş |
| HDMI ARC | 19-pin | 100Ω | 3.4Gbps | TV ses çıkışı |
| Binding Post | 4mm banana | - | - | Hoparlör çıkışı |

#### XLR Konnektörü

##### Pin Konfigürasyonu

```
    ┌─────────────────┐
    │    XLR Male     │
    │   (Panel Mount) │
    │                 │
    │     ┌───┐       │
    │     │ 1 │       │  Pin 1: Ground (Shield)
    │    /│   │\      │  Pin 2: Hot (+, Non-inverting)
    │   / │ 2 │ \     │  Pin 3: Cold (-, Inverting)
    │  /  │   │  \    │
    │ / 3 │   │   \   │
    │/    └───┘    \  │
    │               │ │
    └─────────────────┘
```

##### XLR Devre Bağlantısı

```
XLR Input (Balanced)
     │
     ├─ Pin 1 (GND) ──▶ AGND Plane
     ├─ Pin 2 (Hot) ──▶ 100Ω ──▶ ADC AINL+ (PCM3168A)
     └─ Pin 3 (Cold) ─▶ 100Ω ──▶ ADC AINL- (PCM3168A)

XLR Output (Balanced)
     │
     ├─ Pin 1 (GND) ──▶ AGND Plane
     ├─ Pin 2 (Hot) ──◀ DAC OUTL+ (AK4458)
     └─ Pin 3 (Cold) ─◀ DAC OUTL- (AK4458)
```

#### RCA Konnektörü

##### Pin Konfigürasyonu

```
    ┌─────────────────┐
    │    RCA Male     │
    │   (Panel Mount) │
    │                 │
    │     ┌───┐       │
    │     │ + │       │  Center Pin: Signal (+)
    │     └───┘       │  Outer Ring: Ground (Shield)
    │    ╱     ╲      │
    │   ╱       ╲     │
    │  ╱─────────╲    │
    │                 │
    └─────────────────┘
```

#### USB-C Konnektörü

##### Pin Konfigürasyonu

```
USB-C 16-Pin Connector
     │
     ├─ VBUS (A4, A9, B4, B9) ──▶ +5V (VBUS)
     ├─ GND (A1, A12, B1, B12) ──▶ DGND
     ├─ D+ (A6) ──▶ XU316 USB_DP
     ├─ D- (A7) ──▶ XU316 USB_DM
     ├─ CC1 (A5) ──▶ 5.1kΩ (Sink)
     ├─ CC2 (B5) ──▶ 5.1kΩ (Sink)
     ├─ SBU1 (A8) ──▶ NC (not used)
     └─ SBU2 (B8) ──▶ NC (not used)
```

#### Optical (Toslink)

##### Pin Konfigürasyonu

```
Toslink Connector
     │
     ├─ TX ──▶ LED Transmitter (TOS1103)
     ├─ RX ──▶ Photodiode Receiver
     └─ GND ──▶ Shield

Not: Optik izolasyon sağlar, ground loop engeller.
```

#### HDMI ARC

##### Pin Konfigürasyonu

```
HDMI ARC (Audio Return Channel)
     │
     ├─ TMDS Data0+ ──▶ LVDS Receiver
     ├─ TMDS Data0- ──▶ LVDS Receiver
     ├─ TMDS Data1+ ──▶ LVDS Receiver
     ├─ TMDS Data1- ──▶ LVDS Receiver
     ├─ TMDS Data2+ ──▶ LVDS Receiver
     ├─ TMDS Data2- ──▶ LVDS Receiver
     ├─ TMDS Clock+ ──▶ LVDS Receiver
     ├─ TMDS Clock- ──▶ LVDS Receiver
     ├─ CEC ──▶ CEC Controller (I2C)
     ├─ SCL ──▶ EDID I2C Clock
     ├─ SDA ──▶ EDID I2C Data
     ├─ +5V ──▶ Power
     ├─ HPD ──▶ Hot Plug Detect
     └─ GND ──▶ Shield

ARC Modu: TV'den ses sinyalini HDMI kablo üzerinden alır.
```

#### Binding Posts (Hoparlör Çıkışları)

##### Pin Konfigürasyonu

```
Binding Post (4mm Banana Compatible)
     │
     ├─ Red (+) ──▶ Amplifikatör Output (+)
     ├─ Black (-) ──▶ Amplifikatör Output (-)
     └─ Banana Plug / Spade / Bare Wire uyumlu

Torque: 1.5 Nm (tightening)
Maks. Kablo Kesiti: 4 AWG (21.2mm²)
```

#### Panel Düzeni

```
┌─────────────────────────────────────────────────────────┐
│                     ARKA PANEL                          │
│                                                         │
│  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐      │
│  │XLR 1│ │XLR 2│ │XLR 3│ │XLR 4│ │XLR 5│ │XLR 6│      │
│  │FL   │ │FR   │ │C    │ │SL   │ │SR   │ │SBL  │      │
│  └─────┘ └─────┘ └─────┘ └─────┘ └─────┘ └─────┘      │
│                                                         │
│  ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐ ┌─────┐      │
│  │XLR 7│ │RCA 1│ │RCA 2│ │USB-C│ │TosLk│ │HDMI │      │
│  │SBR  │ │L    │ │R    │ │In   │ │In   │ │ARC  │      │
│  └─────┘ └─────┘ └─────┘ └─────┘ └─────┘ └─────┘      │
│                                                         │
│  ┌─────────────────────────────────────────────────┐    │
│  │              BINDING POSTS                       │    │
│  │  (+FL) (-FL) (+FR) (-FR) (+C) (-C) (+SL) (-SL) │    │
│  └─────────────────────────────────────────────────┘    │
│                                                         │
│  ┌─────────────────────────────────────────────────┐    │
│  │              BINDING POSTS (Devam)               │    │
│  │  (+SR) (-SR) (+SBL)(-SBL)(+SBR)(-SBR)(+LFE)(-LFE)│   │
│  └─────────────────────────────────────────────────┘    │
│                                                         │
│  ┌─────┐ ┌─────┐ ┌─────┐                               │
│  │IEC │ │Fuse │ │SWITCH│                               │
│  │Inlet│ │     │ │     │                               │
│  └─────┘ └─────┘ └─────┘                               │
└─────────────────────────────────────────────────────────┘
```

#### Bağımlılıklar

| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K0 Fiziksel | Alt | Panel layout, drilling |
| K1 Amplifikatör | Bağlantı | XLR/RCA input |
| K1 Hoparlör | Çıkış | Binding posts |
| K1 USB Audio | Bağlantı | USB-C input |
| K1 Koruma | Bağlantı | Relay switching |

#### Durum: Implementasyon

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
| 1 | Giriş empedansı/kazanç değeri kaynak tablosunda tam sayısal olarak doğrulanamıyor | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |
| 2 | Konnektör pin eşlemesi (XLR 1/2/3) vault'ta açıkça yazılmamış | ⚠️ VERIFICATION REQUIRED | ⚠️ VERIFICATION REQUIRED |

## 8. Hata Modları

1. EMI filtresi bulunmayan hatta yüksek frekans gürültünün girmesi (kaynak: `analog-sinyal-yolu`).
2. Empedans eşleşmediğinde yansıma/bozulma (kaynak: `dac-adc-zinciri` §Empedans Eşleme ile çapraz referans).

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

- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/analog-sinyal-yolu.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/konnektorler.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` `_backup/arch-2026-10-06_1057/architecture/k1-donanim/diff-pair-input.md` (salt-okunur yedek, satır sayısı §3).
- `Kanıt:` bu dosya üretildikten sonra `vault-utf8-writer verify` ile BOM/UTF-8 kontrolü yapılır.
- `⚠️ VERIFICATION REQUIRED` — kaynakta bulunmayan ölçüm/sürüm/ADR değerleri bu dosyaya yazılmamıştır.
- Klasör geneli bağımlılıklar ve okuma sırası: `[[index]]`.

---
*K008 · Analog Sinyal Yolu — SSOT: `.ai/architecture/k008-analog-giris/`; ham kaynak: `_backup/arch-2026-10-06_1057/architecture/`.*
