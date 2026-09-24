---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K1 Donanım Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-24
last_update_note: "3 turlu agent tartışması"
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K1: Donanım Layer

**Katman:** K1 (Donanım Altyapısı)
**Kapsam:** XMOS, DAC, Amplifikatör, Hoparlör, Güç kaynağı
**Sorumlu Agent:** Audio Hardware Engineer
**Bileşen Sayısı:** 120

---

## 1. Genel Bakış

K1 katmanı, CoreMusic'in fiziksel donanım bileşenlerini içerir. Bu katman, dijital sinyali analog sinyale dönüştüren ve hoparlörlere ileten tüm donanım alt sistemlerini kapsar.

### 1.1 Temel İlkeler

| İlke | Açıklama |
|------|----------|
| **Bit-Perfect** | Sinyal zincirinde kayıp yok |
| **Low THD** | Toplam Harmonik Bozulma <0.005% |
| **High SNR** | Sinyal-Gürültü Oranı >100dB |
| **8.1 Surround** | 8 kanal + 1 LFE |
| **DC-Only** | Güç kaynağı DC Only |

---

## 2. Bileşen Haritası

### 2.1 USB Audio Interface

| Bileşen | Model | Özellik |
|---------|-------|---------|
| USB Audio | XMOS XU316 | USB Audio Class 2.0, 32-bit |
| USB Interface | USB-C | 24-pin, USB 2.0/3.0 |
| Clock | 22.5792 MHz | 44.1kHz family |
| Clock | 24.576 MHz | 48kHz family |

### 2.2 DAC (Digital-to-Analog Converter)

| Bileşen | Model | Kanal | Bit | Sample Rate |
|---------|-------|-------|-----|-------------|
| Ana DAC | PCM3168A | 6-in/8-out | 24-bit | 192kHz |
| Opsiyonel DAC | AK4458 | 8-kanal | 32-bit | 768kHz |
| REDDEDİLMİŞ | PCM5122 | 2-kanal | 32-bit | — |

**⚠️ Uyarı:** PCM5122 8.1 surround için yetersizdir (ADR-038). Sadece 2 kanal destekler.

### 2.3 Amplifikatör

| Bileşen | Model | Topoloji | Güç | THD |
|---------|-------|----------|-----|-----|
| NPN Output | MJL21194 | Class AB Darlington | 50W/kanal | <0.005% |
| PNP Output | MJL21193 | Class AB Darlington | 50W/kanal | <0.005% |

### 2.4 Güç Kaynağı

| Bileşen | Model | Giriş | Çıkış | Verim |
|---------|-------|-------|-------|-------|
| Boost Converter | LM5122 | 22.2V (6S LiPo) | ±35V | %96 |
| Batarya | 6S LiPo | 22.2V nominal | — | — |
| DC Adapter | 19-24V | AC/DC | — | — |

### 2.5 Hoparlör Matrisi (8.1 Surround)

| Kanal | Hoparlör | Frekans | Konum |
|-------|----------|---------|-------|
| CH1 | Front Left | 20Hz-20kHz | Ön sol |
| CH2 | Front Right | 20Hz-20kHz | Ön sağ |
| CH3 | Center | 100Hz-8kHz | Merkez |
| CH4 | LFE (Sub) | 20Hz-120Hz | Subwoofer |
| CH5 | Surround Left | 100Hz-16kHz | Arka sol |
| CH6 | Surround Right | 100Hz-16kHz | Arka sağ |
| CH7 | Rear Left | 100Hz-16kHz | Arka sol |
| CH8 | Rear Right | 100Hz-16kHz | Arka sağ |

---

## 3. PCM3168A DAC Detayı

### 3.1 Pin Out

```
PCM3168A Pin Configuration:
  VDD1: +3.3V (Digital)
  VDD2: +5V (Analog)
  VSS:  -5V (Analog)
  AGND: Analog Ground
  DGND: Digital Ground

  I2S Input:
    BCK:  Bit Clock (64fs)
    LRCK: Left/Right Clock (fs)
    DIN:  Data In
    SCKI: System Clock (256fs or 512fs)

  Analog Output:
    OUTL1-OUTL3: Left channels (3 output)
    OUTR1-OUTR3: Right channels (3 output)
```

### 3.2 I2S Konfigürasyonu

| Parametre | Değer |
|-----------|-------|
| Sample Rate | 48kHz (default) |
| Bit Depth | 24-bit |
| I2S Mode | Standard I2S |
| System Clock | 256fs = 12.288MHz |
| BCK | 64fs = 3.072MHz |
| LRCK | 48kHz |

### 3.3 Analogy Output Devresi

```
PCM3168A OUTL1 → I/V Resistor (1kΩ) → Low-Pass Filter (20kHz) → Differential Driver → Amplifier Input
```

---

## 4. Class AB Amplifikatör Detayı

### 4.1 Tek Kanal Devre Şeması

```
                    +35V (PVDD)
                     │
                ┌────┴────┐
                │  Q15     │ MJL21194 (NPN Output)
                │  NPN     │
     Input ─────┤  Q16     ├──── Output → Hoparlör
     (Diff)     │  BD139   │
                │  VAS     │
                │  Q17     │ MJL21193 (PNP Output)
                │  PNP     │
                └────┬────┘
                     │
                    -35V (PVSS)

  Bias Network:
    Q1 (BC546B): Diferansiyel çift giriş
    Q2 (BC546B): Diferansiyel çift giriş
    Q5 (BC556B): Akım havuzu
    Q9 (KSC3503): VAS (Voltage Amplifier Stage)
    Q10 (BD139): Vbe çarpımı (bias spreader)
```

### 4.2 Bias Ayar Prosedürü

| Adım | İşlem | Değer |
|------|-------|-------|
| 1 | Güç kaynağı ayarla | ±35V DC |
| 2 | Multimetre çıkışa bağla | DC offset ölç |
| 3 | Bias potansiyometresi ayarla | 0V DC offset hedefle |
| 4 | Sıcaklık stabilizasyonu | 5-10 dk bekle |
| 5 | Son kontrol | <0.5V DC offset |

### 4.3 Termal Hesaplama

| Parametre | Değer |
|-----------|-------|
| Güç (kanal başına) | 50W @ 8Ω |
| Verimlilik | ~%65 (Class AB) |
| Isı (kanal başına) | ~17.5W |
| Toplam ısı (8 kanal) | ~140W |
| Heatsink gereksinimi | >140W/C° thermal resistance |
| Fan gereksinimi | 80mm PWM, >50 CFM |

---

## 5. XMOS XU316 Detayı

### 5.1 Blok Diyagramı

```
USB 2.0 ──→ XMOS XU316 ──→ I2S ──→ PCM3168A
              │
              ├→ Clock Generator
              ├→ USB Audio Class 2.0
              ├→ DSP Processing
              └→ Control Interface
```

### 5.2 XMOS Kaynak Kullanımı

| Kaynak | Kullanım |
|--------|----------|
| Logical Cores | 8 (4 x 2 tile) |
| MIPS | ~2000 (toplam) |
| RAM | 512KB (tile 0+1) |
| Flash | 16MB (external) |
| USB PHY | High-speed 480Mbps |

---

## 6. PCB Tasarım Kuralları

| Parametre | Değer |
|-----------|-------|
| Layer | 6-layer stackup |
| Copper (top/bottom) | 2oz |
| Copper (inner) | 1oz |
| Finish | ENIG |
| Min trace | 4mil |
| Min via | 8mil drill, 16mil pad |
| USB Impedans | 90Ω differential |
| I2S Impedans | 50Ω single-ended |
| Ground | Star ground topology |
| Thermal | Thermal vias under power components |

### 6.1 Stackup

```
Layer 1: Signal (top) — Components, traces
Layer 2: Ground — Continuous ground plane
Layer 3: Signal — I2S, control signals
Layer 4: Power — +35V, -35V, +3.3V, +5V
Layer 5: Ground — Continuous ground plane
Layer 6: Signal (bottom) — Components, traces
```

---

## 7. BOM Maliyet Analizi

| Kategori | Bileşen Sayısı | Toplam Maliyet |
|----------|---------------|---------------|
| USB Audio (XMOS) | 3 | $15.00 |
| DAC (PCM3168A) | 15 | $25.00 |
| Amplifikatör (8 kanal) | 120 | $85.00 |
| Güç Kaynağı | 35 | $45.00 |
| Pasif Bileşenler | 800+ | $180.00 |
| Konnektörler | 25 | $30.00 |
| PCB (6-layer) | 1 | $50.00 |
| **TOPLAM** | **~1,000** | **~$430** |

---

## 8. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| `architecture/k1-donanim/README.md` | Bu dosya |
| `architecture/k1-donanim/xmos-xu316.md` | XMOS detayı |
| `architecture/k1-donanim/pcm3168a.md` | DAC detayı |
| `architecture/k1-donanim/ak4458.md` | High-end DAC |
| `architecture/k1-donanim/class-ab-amplifier.md` | Amplifikatör devresi |
| `architecture/k1-donanim/speaker-matrix.md` | Hoparlör konfigürasyonu |
| `architecture/k1-donanim/bom-cost.md` | BOM maliyet |
| `architecture/k16-class-ab/README.md` | K16 Class AB |
| `architecture/k17-guc-kaynagi/README.md` | K17 Güç kaynağı |

---

## 9. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-038 | PCM3168A (PCM5122 REDDEDİLMİŞ) |
| ADR-089 | Class AB Amplifikatör + 6S LiPo + ±35V Boost |

---

## Alt Katman Şeması (K1.a.b.c)

> **Şema kuralları (2026-09-24 · 3 turlu agent tartışması):** `K1` → `K1.a` (2. katman, 12 düğüm) → `K1.a.b` (3. katman, her `K1.a` için zorunlu) → `K1.a.b.c` (4. katman — yalnızca diskte kanıtla: MD başlık satırı; uydurma yok). **Kapsam kanıtı:** 19 içerik MD + `firmware/` 8 MD (**K1.f alt katmanı**) + README bileşen envanteri. Bilinçli çıkarım: "Bağımlılıklar"/"Durum: Implementasyon" başlıkları ve README §8-§9 (57 başlık) yaprak sayılmadı — kanıt dizininde görünür. Hedef: 2. katman **12** · 3. katman **≥7** (fiilen 31) · 4. katman **360** kanıtlı yaprak (fiilen 360).

### K1 Şema Özeti

| 2. Katman | Ad | 3. Katman | 4. Kanıtlı Yaprak | Birincil Kanıt |
|-----------|----|-----------|-------------------|----------------|
| K1.1 | DAC/ADC Zinciri | 3 | 23 | ak4458-dac.md, pcm3168a-dac-adc.md, dac-adc-zinciri.md |
| K1.2 | Amplifikatör Aşamaları | 6 | 66 | diff-pair/vas/class-ab/output/feedback/mjle md |
| K1.3 | Analog Sinyal Yolu | 1 | 17 | analog-sinyal-yolu.md |
| K1.4 | Güç Kaynağı & Koruma | 2 | 23 | guc-kaynagi-analog.md, koruma-devreleri.md |
| K1.5 | Dijital Arayüzler | 3 | 32 | i2s-interface.md, usb-audio.md, xmos-xu316.md |
| K1.6 | PCB & Termal | 2 | 21 | pcb-tasarim.md, termal-yonetim.md |
| K1.7 | Konnektör & Hoparlör | 2 | 24 | konnektorler.md, hoparlor-dizilimi.md |
| K1.8 | Firmware (K1.f alt katmanı) | 8 | 123 | firmware/ — 8 MD |
| K1.9 | Bileşen Haritası & BOM | 1 | 20 | README.md §2-§7 |
| K1.10 | index.md | 1 | 5 | index.md |
| K1.11 | CLAUDE.md Guardrails | 1 | 4 | CLAUDE.md |
| K1.12 | README Genel Bakış | 1 | 2 | README.md §1 |
| **TOPLAM** | | **31** | **360** | |

### K1.1 — DAC/ADC Zinciri

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K1.1.1** | AK4458 DAC (3. katman) | ak4458-dac.md — 7 yaprak |
| K1.1.1.1 | Genel Bakış | ak4458-dac.md L10 |
| K1.1.1.2 | Teknik Spesifikasyonlar | ak4458-dac.md L14 |
| K1.1.1.3 | DSD Modu Destekleri | ak4458-dac.md L29 |
| K1.1.1.4 | Devre Tasarımı | ak4458-dac.md L39 |
| K1.1.1.5 | Pin Konfigürasyonu (Önemli Pinler) | ak4458-dac.md L80 |
| K1.1.1.6 | Temel Bağlantılar | ak4458-dac.md L41 |
| K1.1.1.7 | Kondansatör ve Direnç Değerleri | ak4458-dac.md L68 |
| **K1.1.2** | PCM3168A DAC/ADC (3. katman) | pcm3168a-dac-adc.md — 6 yaprak |
| K1.1.2.1 | Genel Bakış | pcm3168a-dac-adc.md L10 |
| K1.1.2.2 | Teknik Spesifikasyonlar | pcm3168a-dac-adc.md L14 |
| K1.1.2.3 | Devre Tasarımı | pcm3168a-dac-adc.md L29 |
| K1.1.2.4 | Pin Konfigürasyonu | pcm3168a-dac-adc.md L68 |
| K1.1.2.5 | Temel Bağlantılar | pcm3168a-dac-adc.md L31 |
| K1.1.2.6 | Filtre ve Kondansatörler | pcm3168a-dac-adc.md L57 |
| **K1.1.3** | DAC-ADC Zinciri (3. katman) | dac-adc-zinciri.md — 10 yaprak |
| K1.1.3.1 | Genel Bakış | dac-adc-zinciri.md L10 |
| K1.1.3.2 | Teknik Spesifikasyonlar | dac-adc-zinciri.md L14 |
| K1.1.3.3 | Sinyal Yolu Diyagramı | dac-adc-zinciri.md L27 |
| K1.1.3.4 | Clock Synchronization | dac-adc-zinciri.md L56 |
| K1.1.3.5 | EMI Filtreleme | dac-adc-zinciri.md L109 |
| K1.1.3.6 | Empedans Eşleşme | dac-adc-zinciri.md L123 |
| K1.1.3.7 | Bileşen Değerleri | dac-adc-zinciri.md L131 |
| K1.1.3.8 | Master/Slave Konfigürasyonu | dac-adc-zinciri.md L58 |
| K1.1.3.9 | Clock Accuracy | dac-adc-zinciri.md L101 |
| K1.1.3.10 | I2S Hat Filtresi | dac-adc-zinciri.md L111 |

### K1.2 — Amplifikatör Aşamaları

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K1.2.1** | Diff Pair Input (3. katman) | diff-pair-input.md — 11 yaprak |
| K1.2.1.1 | Genel Bakış | diff-pair-input.md L10 |
| K1.2.1.2 | Teknik Spesifikasyonlar | diff-pair-input.md L14 |
| K1.2.1.3 | Devre Şeması | diff-pair-input.md L28 |
| K1.2.1.4 | Bias Current Hesaplaması | diff-pair-input.md L59 |
| K1.2.1.5 | CMRR Analizi | diff-pair-input.md L80 |
| K1.2.1.6 | Gürültü Analizi | diff-pair-input.md L102 |
| K1.2.1.7 | Tail Current | diff-pair-input.md L61 |
| K1.2.1.8 | Operating Point | diff-pair-input.md L69 |
| K1.2.1.9 | CMRR Formülü | diff-pair-input.md L82 |
| K1.2.1.10 | hesaplama | diff-pair-input.md L93 |
| K1.2.1.11 | Giriş Gürültüsü Kaynakları | diff-pair-input.md L104 |
| **K1.2.2** | VAS Stage (3. katman) | vas-stage.md — 11 yaprak |
| K1.2.2.1 | Genel Bakış | vas-stage.md L10 |
| K1.2.2.2 | Teknik Spesifikasyonlar | vas-stage.md L14 |
| K1.2.2.3 | Devre Şeması | vas-stage.md L28 |
| K1.2.2.4 | Miller Compensation Analizi | vas-stage.md L62 |
| K1.2.2.5 | Bileşen Değerleri | vas-stage.md L104 |
| K1.2.2.6 | Frekans Tepkisi | vas-stage.md L114 |
| K1.2.2.7 | Stabilite Analizi | vas-stage.md L136 |
| K1.2.2.8 | Neden Miller Compensation? | vas-stage.md L64 |
| K1.2.2.9 | Miller Etkisi Formülü | vas-stage.md L77 |
| K1.2.2.10 | Dominant Pole | vas-stage.md L94 |
| K1.2.2.11 | Phase Margin Hesabı | vas-stage.md L138 |
| **K1.2.3** | Class-AB Amplifikatör (3. katman) | class-ab-amplifikator.md — 8 yaprak |
| K1.2.3.1 | Genel Bakış | class-ab-amplifikator.md L10 |
| K1.2.3.2 | Teknik Spesifikasyonlar | class-ab-amplifikator.md L14 |
| K1.2.3.3 | Devre Şeması — Genel Görünüm | class-ab-amplifikator.md L28 |
| K1.2.3.4 | Devre Tasarımı — Detaylı | class-ab-amplifikator.md L50 |
| K1.2.3.5 | Bileşen Listesi | class-ab-amplifikator.md L129 |
| K1.2.3.6 | Differential Pair Input Stage | class-ab-amplifikator.md L52 |
| K1.2.3.7 | Voltage Amplification Stage (VAS) | class-ab-amplifikator.md L78 |
| K1.2.3.8 | Push-Pull Output Stage | class-ab-amplifikator.md L101 |
| **K1.2.4** | Output Stage (3. katman) | output-stage.md — 15 yaprak |
| K1.2.4.1 | Genel Bakış | output-stage.md L10 |
| K1.2.4.2 | Teknik Spesifikasyonlar | output-stage.md L14 |
| K1.2.4.3 | Push-Pull Konfigürasyon | output-stage.md L26 |
| K1.2.4.4 | Darlington Configuration | output-stage.md L65 |
| K1.2.4.5 | Thermal Tracking | output-stage.md L108 |
| K1.2.4.6 | Bileşen Değerleri | output-stage.md L146 |
| K1.2.4.7 | Akım Yolu Analizi | output-stage.md L159 |
| K1.2.4.8 | Tek transistor (Single-ended output) | output-stage.md L28 |
| K1.2.4.9 | Push-Pull Output | output-stage.md L35 |
| K1.2.4.10 | Neden Darlington? | output-stage.md L67 |
| K1.2.4.11 | Darlington Emitters Follower | output-stage.md L82 |
| K1.2.4.12 | Crossover Distortion Sorunu | output-stage.md L110 |
| K1.2.4.13 | Çözüm: Thermal Tracking | output-stage.md L120 |
| K1.2.4.14 | Pozitif Yarım Döngü (MJL21194 Active) | output-stage.md L161 |
| K1.2.4.15 | Negatif Yarım Döngü (MJL21193 Active) | output-stage.md L170 |
| **K1.2.5** | Feedback Network (3. katman) | feedback-network.md — 11 yaprak |
| K1.2.5.1 | Genel Bakış | feedback-network.md L10 |
| K1.2.5.2 | Teknik Spesifikasyonlar | feedback-network.md L14 |
| K1.2.5.3 | Devre Şeması | feedback-network.md L26 |
| K1.2.5.4 | Kazanç Hesaplaması | feedback-network.md L58 |
| K1.2.5.5 | Frequency Compensation | feedback-network.md L82 |
| K1.2.5.6 | Bileşen Değerleri | feedback-network.md L117 |
| K1.2.5.7 | Empedans Eşleşme | feedback-network.md L126 |
| K1.2.5.8 | Kapalı Devre Kazancı | feedback-network.md L60 |
| K1.2.5.9 | Distorsiyon Azaltma | feedback-network.md L73 |
| K1.2.5.10 | Bode Plot | feedback-network.md L84 |
| K1.2.5.11 | Stabilite Kriterleri | feedback-network.md L108 |
| **K1.2.6** | MJL21194/93 Output Transistör (3. katman) | mjle21194-93.md — 10 yaprak |
| K1.2.6.1 | Genel Bakış | mjle21194-93.md L10 |
| K1.2.6.2 | Teknik Spesifikasyonlar | mjle21194-93.md L14 |
| K1.2.6.3 | DC Karakteristikleri (Tipik @ 25°C) | mjle21194-93.md L32 |
| K1.2.6.4 | AC Karakteristikleri (Tipik @ 25°C) | mjle21194-93.md L42 |
| K1.2.6.5 | Devre Bağlantıları | mjle21194-93.md L51 |
| K1.2.6.6 | Termal Hesaplamalar | mjle21194-93.md L115 |
| K1.2.6.7 | Push-Pull Konfigürasyon | mjle21194-93.md L53 |
| K1.2.6.8 | Thermal Tracking (Bias Transistörleri) | mjle21194-93.md L93 |
| K1.2.6.9 | Güç Tüketimi (Tipik Usage) | mjle21194-93.md L117 |
| K1.2.6.10 | Soğutucu Gereksinimi | mjle21194-93.md L127 |

### K1.3 — Analog Sinyal Yolu

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K1.3.1** | Analog Sinyal Yolu (3. katman) | analog-sinyal-yolu.md — 17 yaprak |
| K1.3.1.1 | Genel Bakış | analog-sinyal-yolu.md L10 |
| K1.3.1.2 | Teknik Spesifikasyonlar | analog-sinyal-yolu.md L14 |
| K1.3.1.3 | Sinyal Yolu Diyagramı | analog-sinyal-yolu.md L28 |
| K1.3.1.4 | Aşama 1: Giriş Filtresi | analog-sinyal-yolu.md L52 |
| K1.3.1.5 | Aşama 2: Differential Pair | analog-sinyal-yolu.md L79 |
| K1.3.1.6 | Aşama 3: VAS Stage | analog-sinyal-yolu.md L91 |
| K1.3.1.7 | Aşama 4: Output Stage | analog-sinyal-yolu.md L103 |
| K1.3.1.8 | Aşama 5: Feedback Network | analog-sinyal-yolu.md L112 |
| K1.3.1.9 | Impedance Matching | analog-sinyal-yolu.md L121 |
| K1.3.1.10 | EMI Filtering | analog-sinyal-yolu.md L162 |
| K1.3.1.11 | Bileşen Değerleri | analog-sinyal-yolu.md L182 |
| K1.3.1.12 | Differential Input Filter | analog-sinyal-yolu.md L54 |
| K1.3.1.13 | Input Impedance | analog-sinyal-yolu.md L123 |
| K1.3.1.14 | Inter-stage Impedance | analog-sinyal-yolu.md L133 |
| K1.3.1.15 | Output Impedance | analog-sinyal-yolu.md L147 |
| K1.3.1.16 | Input EMI | analog-sinyal-yolu.md L164 |
| K1.3.1.17 | Output EMI | analog-sinyal-yolu.md L173 |

### K1.4 — Güç Kaynağı & Koruma

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K1.4.1** | Analog Güç Kaynağı (3. katman) | guc-kaynagi-analog.md — 9 yaprak |
| K1.4.1.1 | Genel Bakış | guc-kaynagi-analog.md L10 |
| K1.4.1.2 | Teknik Spesifikasyonlar | guc-kaynagi-analog.md L14 |
| K1.4.1.3 | Güç Topolojisi | guc-kaynagi-analog.md L28 |
| K1.4.1.4 | Devre Tasarımı | guc-kaynagi-analog.md L71 |
| K1.4.1.5 | Bileşen Listesi | guc-kaynagi-analog.md L110 |
| K1.4.1.6 | Ripple Analizi | guc-kaynagi-analog.md L123 |
| K1.4.1.7 | Koruma Devreleri | guc-kaynagi-analog.md L138 |
| K1.4.1.8 | LM5122 Dual Boost Converter | guc-kaynagi-analog.md L73 |
| K1.4.1.9 | Voltaj Ayarı | guc-kaynagi-analog.md L95 |
| **K1.4.2** | Koruma Devreleri (3. katman) | koruma-devreleri.md — 14 yaprak |
| K1.4.2.1 | Genel Bakış | koruma-devreleri.md L10 |
| K1.4.2.2 | Teknik Spesifikasyonlar | koruma-devreleri.md L14 |
| K1.4.2.3 | DC Offset Koruması | koruma-devreleri.md L25 |
| K1.4.2.4 | Overcurrent Koruması | koruma-devreleri.md L69 |
| K1.4.2.5 | Thermal Shutdown | koruma-devreleri.md L107 |
| K1.4.2.6 | Short Circuit Koruması | koruma-devreleri.md L141 |
| K1.4.2.7 | Bileşen Değerleri | koruma-devreleri.md L157 |
| K1.4.2.8 | Devre Şeması (DC Offset) | koruma-devreleri.md L27 |
| K1.4.2.9 | Çalışma Prensibi | koruma-devreleri.md L58 |
| K1.4.2.10 | Devre Şeması (Overcurrent) | koruma-devreleri.md L71 |
| K1.4.2.11 | Current Limiting Profile | koruma-devreleri.md L98 |
| K1.4.2.12 | Devre Şeması (Thermal) | koruma-devreleri.md L109 |
| K1.4.2.13 | Thermal Profile | koruma-devreleri.md L131 |
| K1.4.2.14 | Çift Koruma | koruma-devreleri.md L143 |

### K1.5 — Dijital Arayüzler

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K1.5.1** | I2S Interface (3. katman) | i2s-interface.md — 13 yaprak |
| K1.5.1.1 | Genel Bakış | i2s-interface.md L10 |
| K1.5.1.2 | Teknik Spesifikasyonlar | i2s-interface.md L14 |
| K1.5.1.3 | I2S Sinyalleri | i2s-interface.md L28 |
| K1.5.1.4 | I2S Timing Diagram | i2s-interface.md L49 |
| K1.5.1.5 | Master/Slave Mode | i2s-interface.md L70 |
| K1.5.1.6 | Multi-Channel Configuration | i2s-interface.md L105 |
| K1.5.1.7 | Impedans ve Drive | i2s-interface.md L135 |
| K1.5.1.8 | Bileşen Değerleri | i2s-interface.md L159 |
| K1.5.1.9 | XMOS as Master | i2s-interface.md L72 |
| K1.5.1.10 | DAC/ADC as Slave | i2s-interface.md L87 |
| K1.5.1.11 | 8-Channel TDM (Time Division Multiplexing) | i2s-interface.md L107 |
| K1.5.1.12 | Source Impedans | i2s-interface.md L137 |
| K1.5.1.13 | Load Impedans | i2s-interface.md L150 |
| **K1.5.2** | USB Audio (3. katman) | usb-audio.md — 13 yaprak |
| K1.5.2.1 | Genel Bakış | usb-audio.md L10 |
| K1.5.2.2 | Teknik Spesifikasyonlar | usb-audio.md L14 |
| K1.5.2.3 | USB Descriptor Hierarchy | usb-audio.md L29 |
| K1.5.2.4 | Isochronous Transfer | usb-audio.md L63 |
| K1.5.2.5 | Sample Rate Support | usb-audio.md L90 |
| K1.5.2.6 | DSD (DoP) Support | usb-audio.md L103 |
| K1.5.2.7 | USB-C Connection | usb-audio.md L129 |
| K1.5.2.8 | Driver Status | usb-audio.md L150 |
| K1.5.2.9 | Bileşen Değerleri | usb-audio.md L160 |
| K1.5.2.10 | Neden Isochronous? | usb-audio.md L65 |
| K1.5.2.11 | Transfer Parameters | usb-audio.md L79 |
| K1.5.2.12 | DoP (DSD over PCM) | usb-audio.md L105 |
| K1.5.2.13 | Pin Mapping | usb-audio.md L131 |
| **K1.5.3** | XMOS XU316 (3. katman) | xmos-xu316.md — 6 yaprak |
| K1.5.3.1 | Genel Bakış | xmos-xu316.md L10 |
| K1.5.3.2 | Teknik Spesifikasyonlar | xmos-xu316.md L14 |
| K1.5.3.3 | Devre Tasarımı | xmos-xu316.md L29 |
| K1.5.3.4 | Pin Konfigürasyonu | xmos-xu316.md L64 |
| K1.5.3.5 | Temel Bağlantılar | xmos-xu316.md L31 |
| K1.5.3.6 | Kondansatör Değerleri | xmos-xu316.md L55 |

### K1.6 — PCB & Termal

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K1.6.1** | PCB Tasarım (3. katman) | pcb-tasarim.md — 8 yaprak |
| K1.6.1.1 | Genel Bakış | pcb-tasarim.md L10 |
| K1.6.1.2 | Teknik Spesifikasyonlar | pcb-tasarim.md L14 |
| K1.6.1.3 | Katman Stackup | pcb-tasarim.md L30 |
| K1.6.1.4 | Star Grounding Sistemi | pcb-tasarim.md L76 |
| K1.6.1.5 | Controlled Impedance | pcb-tasarim.md L101 |
| K1.6.1.6 | Bileşen Yerleşimi | pcb-tasarim.md L142 |
| K1.6.1.7 | Impedans Hesaplaması | pcb-tasarim.md L103 |
| K1.6.1.8 | Differential Pair (I2S, USB) | pcb-tasarim.md L123 |
| **K1.6.2** | Termal Yönetim (3. katman) | termal-yonetim.md — 13 yaprak |
| K1.6.2.1 | Genel Bakış | termal-yonetim.md L10 |
| K1.6.2.2 | Teknik Spesifikasyonlar | termal-yonetim.md L14 |
| K1.6.2.3 | Isı Dağılım Analizi | termal-yonetim.md L26 |
| K1.6.2.4 | Soğutucu Seçimi | termal-yonetim.md L46 |
| K1.6.2.5 | Sıcaklık İzleme | termal-yonetim.md L71 |
| K1.6.2.6 | Fan Kontrolü | termal-yonetim.md L102 |
| K1.6.2.7 | Termal Pad Uygulaması | termal-yonetim.md L127 |
| K1.6.2.8 | Toplam Isı (8 Kanal) | termal-yonetim.md L38 |
| K1.6.2.9 | Alüminyum Ekstrüzyon Soğutucu | termal-yonetim.md L48 |
| K1.6.2.10 | Termal Ped | termal-yonetim.md L61 |
| K1.6.2.11 | NTC Sensör Devresi | termal-yonetim.md L73 |
| K1.6.2.12 | Sıcaklık-Hassasiyet Tablosu | termal-yonetim.md L92 |
| K1.6.2.13 | PWM Fan Driver | termal-yonetim.md L104 |

### K1.7 — Konnektör & Hoparlör

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K1.7.1** | Konnektörler (3. katman) | konnektorler.md — 16 yaprak |
| K1.7.1.1 | Genel Bakış | konnektorler.md L10 |
| K1.7.1.2 | Teknik Spesifikasyonlar | konnektorler.md L14 |
| K1.7.1.3 | XLR Konnektörü | konnektorler.md L25 |
| K1.7.1.4 | RCA Konnektörü | konnektorler.md L61 |
| K1.7.1.5 | USB-C Konnektörü | konnektorler.md L80 |
| K1.7.1.6 | Optical (Toslink) | konnektorler.md L97 |
| K1.7.1.7 | HDMI ARC | konnektorler.md L111 |
| K1.7.1.8 | Binding Posts (Hoparlör Çıkışları) | konnektorler.md L136 |
| K1.7.1.9 | Panel Düzeni | konnektorler.md L151 |
| K1.7.1.10 | Pin Konfigürasyonu (XLR) | konnektorler.md L27 |
| K1.7.1.11 | XLR Devre Bağlantısı | konnektorler.md L45 |
| K1.7.1.12 | Pin Konfigürasyonu (RCA) | konnektorler.md L63 |
| K1.7.1.13 | Pin Konfigürasyonu (USB-C) | konnektorler.md L82 |
| K1.7.1.14 | Pin Konfigürasyonu (Optical) | konnektorler.md L99 |
| K1.7.1.15 | Pin Konfigürasyonu (HDMI ARC) | konnektorler.md L113 |
| K1.7.1.16 | Pin Konfigürasyonu (Binding Posts) | konnektorler.md L138 |
| **K1.7.2** | Hoparlör Dizilimi (3. katman) | hoparlor-dizilimi.md — 8 yaprak |
| K1.7.2.1 | Genel Bakış | hoparlor-dizilimi.md L10 |
| K1.7.2.2 | Teknik Spesifikasyonlar | hoparlor-dizilimi.md L14 |
| K1.7.2.3 | Hoparlör Konumları | hoparlor-dizilimi.md L27 |
| K1.7.2.4 | Kanal Haritası | hoparlor-dizilimi.md L68 |
| K1.7.2.5 | Hoparlör Özellikleri | hoparlor-dizilimi.md L81 |
| K1.7.2.6 | Kablo ve Bağlantılar | hoparlor-dizilimi.md L106 |
| K1.7.2.7 | Full-Range Hoparlörler (FL, C, FR, SL, SR, SBL, SBR) | hoparlor-dizilimi.md L83 |
| K1.7.2.8 | Subwoofer (LFE) | hoparlor-dizilimi.md L95 |

### K1.8 — Firmware (K1.f Alt Katmanı)

> **Kural:** `firmware/` klasörü K1'in alt katmanıdır (**K1.f**); 8 MD'nin tamamı K1.f.1..K1.f.8 olarak 3. katmanı oluşturur, her birinin başlıkları 4. katman yapraklarıdır.

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K1.f.1** | Bootloader (3. katman) | firmware/bootloader.md — 16 yaprak |
| K1.f.1.1 | Genel Bakış | firmware/bootloader.md L10 |
| K1.f.1.2 | Firmware Mimarisi | firmware/bootloader.md L14 |
| K1.f.1.3 | Kaynak Kod Yapısı | firmware/bootloader.md L58 |
| K1.f.1.4 | Teknik Detaylar | firmware/bootloader.md L85 |
| K1.f.1.5 | Derleme & Yükleme | firmware/bootloader.md L395 |
| K1.f.1.6 | Boot Sequence | firmware/bootloader.md L87 |
| K1.f.1.7 | Dual-Bank Firmware Update | firmware/bootloader.md L102 |
| K1.f.1.8 | DFU Protocol | firmware/bootloader.md L130 |
| K1.f.1.9 | XMOS Bootloader Implementasyonu | firmware/bootloader.md L150 |
| K1.f.1.10 | Flash Manager | firmware/bootloader.md L211 |
| K1.f.1.11 | USB DFU Handler | firmware/bootloader.md L274 |
| K1.f.1.12 | Reboot & Recovery | firmware/bootloader.md L356 |
| K1.f.1.13 | XMOS Bootloader Derleme | firmware/bootloader.md L397 |
| K1.f.1.14 | STM32 Bootloader Derleme | firmware/bootloader.md L410 |
| K1.f.1.15 | DFU ile Firmware Güncelleme | firmware/bootloader.md L422 |
| K1.f.1.16 | XMOS DFU | firmware/bootloader.md L441 |
| **K1.f.2** | XMOS Firmware (3. katman) | firmware/xmos-firmware.md — 18 yaprak |
| K1.f.2.1 | Genel Bakış | firmware/xmos-firmware.md L10 |
| K1.f.2.2 | Firmware Mimarisi | firmware/xmos-firmware.md L14 |
| K1.f.2.3 | Kaynak Kod Yapısı | firmware/xmos-firmware.md L41 |
| K1.f.2.4 | Teknik Detaylar | firmware/xmos-firmware.md L75 |
| K1.f.2.5 | Derleme & Yükleme | firmware/xmos-firmware.md L262 |
| K1.f.2.6 | XC Dil Özellikleri | firmware/xmos-firmware.md L77 |
| K1.f.2.7 | XMOS XU316 Özellikleri | firmware/xmos-firmware.md L96 |
| K1.f.2.8 | Thread Zamanlama | firmware/xmos-firmware.md L110 |
| K1.f.2.9 | Memory Map | firmware/xmos-firmware.md L121 |
| K1.f.2.10 | USB Audio Pipeline | firmware/xmos-firmware.md L132 |
| K1.f.2.11 | Clock Recovery | firmware/xmos-firmware.md L142 |
| K1.f.2.12 | DSP Processing Chain | firmware/xmos-firmware.md L171 |
| K1.f.2.13 | USB Audio Class 2.0 Entegrasyonu | firmware/xmos-firmware.md L201 |
| K1.f.2.14 | Error Handling & Recovery | firmware/xmos-firmware.md L231 |
| K1.f.2.15 | Ortam Kurulumu | firmware/xmos-firmware.md L264 |
| K1.f.2.16 | Firmware Derleme | firmware/xmos-firmware.md L276 |
| K1.f.2.17 | Firmware Yükleme | firmware/xmos-firmware.md L294 |
| K1.f.2.18 | Debug & Trace | firmware/xmos-firmware.md L307 |
| **K1.f.3** | USB Audio Firmware (3. katman) | firmware/usb-audio-firmware.md — 15 yaprak |
| K1.f.3.1 | Genel Bakış | firmware/usb-audio-firmware.md L10 |
| K1.f.3.2 | Firmware Mimarisi | firmware/usb-audio-firmware.md L14 |
| K1.f.3.3 | Kaynak Kod Yapısı | firmware/usb-audio-firmware.md L62 |
| K1.f.3.4 | Teknik Detaylar | firmware/usb-audio-firmware.md L89 |
| K1.f.3.5 | Derleme & Yükleme | firmware/usb-audio-firmware.md L432 |
| K1.f.3.6 | USB Audio Class 2.0 Descriptor Hiyerarşisi | firmware/usb-audio-firmware.md L91 |
| K1.f.3.7 | Isochronous Transfer Mekanizması | firmware/usb-audio-firmware.md L129 |
| K1.f.3.8 | Clock Recovery & Sync | firmware/usb-audio-firmware.md L187 |
| K1.f.3.9 | Ring Buffer Implementasyonu | firmware/usb-audio-firmware.md L265 |
| K1.f.3.10 | Audio Format Conversion | firmware/usb-audio-firmware.md L319 |
| K1.f.3.11 | USB Audio Control Requests | firmware/usb-audio-firmware.md L354 |
| K1.f.3.12 | Latency Optimization | firmware/usb-audio-firmware.md L408 |
| K1.f.3.13 | USB Audio Firmware Derleme | firmware/usb-audio-firmware.md L434 |
| K1.f.3.14 | USB Descriptor Doğrulama | firmware/usb-audio-firmware.md L453 |
| K1.f.3.15 | USB Audio Test | firmware/usb-audio-firmware.md L467 |
| **K1.f.4** | MCU Support (3. katman) | firmware/mcu-support.md — 18 yaprak |
| K1.f.4.1 | Genel Bakış | firmware/mcu-support.md L10 |
| K1.f.4.2 | Firmware Mimarisi | firmware/mcu-support.md L14 |
| K1.f.4.3 | Kaynak Kod Yapısı | firmware/mcu-support.md L56 |
| K1.f.4.4 | Teknik Detaylar | firmware/mcu-support.md L112 |
| K1.f.4.5 | Derleme & Yükleme | firmware/mcu-support.md L877 |
| K1.f.4.6 | STM32F4 Pin Configuration | firmware/mcu-support.md L114 |
| K1.f.4.7 | SPI Communication Protocol | firmware/mcu-support.md L143 |
| K1.f.4.8 | System Health Monitoring | firmware/mcu-support.md L252 |
| K1.f.4.9 | Firmware Update Orchestration | firmware/mcu-support.md L352 |
| K1.f.4.10 | Raspberry Pi Control Interface | firmware/mcu-support.md L470 |
| K1.f.4.11 | Network Manager | firmware/mcu-support.md L619 |
| K1.f.4.12 | Configuration Management | firmware/mcu-support.md L684 |
| K1.f.4.13 | UART Debug Console | firmware/mcu-support.md L783 |
| K1.f.4.14 | STM32 Firmware Derleme | firmware/mcu-support.md L879 |
| K1.f.4.15 | STM32 Firmware Yükleme | firmware/mcu-support.md L897 |
| K1.f.4.16 | Raspberry Pi Firmware Derleme | firmware/mcu-support.md L911 |
| K1.f.4.17 | Raspberry Pi Kurulum | firmware/mcu-support.md L931 |
| K1.f.4.18 | Entegrasyon Testi | firmware/mcu-support.md L953 |
| **K1.f.5** | DSP Firmware (3. katman) | firmware/dsp-firmware.md — 15 yaprak |
| K1.f.5.1 | Genel Bakış | firmware/dsp-firmware.md L10 |
| K1.f.5.2 | Firmware Mimarisi | firmware/dsp-firmware.md L14 |
| K1.f.5.3 | Kaynak Kod Yapısı | firmware/dsp-firmware.md L72 |
| K1.f.5.4 | Teknik Detaylar | firmware/dsp-firmware.md L103 |
| K1.f.5.5 | Derleme & Yükleme | firmware/dsp-firmware.md L547 |
| K1.f.5.6 | DSP Processing Block Structure | firmware/dsp-firmware.md L105 |
| K1.f.5.7 | IIR Biquad Filter | firmware/dsp-firmware.md L146 |
| K1.f.5.8 | Parametric EQ | firmware/dsp-firmware.md L216 |
| K1.f.5.9 | Dynamics Compressor | firmware/dsp-firmware.md L282 |
| K1.f.5.10 | Peak Limiter | firmware/dsp-firmware.md L345 |
| K1.f.5.11 | Noise Gate | firmware/dsp-firmware.md L402 |
| K1.f.5.12 | Stereo Crossfeed (Headphone) | firmware/dsp-firmware.md L457 |
| K1.f.5.13 | DSP Performance Optimization | firmware/dsp-firmware.md L509 |
| K1.f.5.14 | DSP Firmware Derleme | firmware/dsp-firmware.md L549 |
| K1.f.5.15 | DSP Test | firmware/dsp-firmware.md L568 |
| **K1.f.6** | GPIO Control (3. katman) | firmware/gpio-control.md — 14 yaprak |
| K1.f.6.1 | Genel Bakış | firmware/gpio-control.md L10 |
| K1.f.6.2 | Firmware Mimarisi | firmware/gpio-control.md L14 |
| K1.f.6.3 | Kaynak Kod Yapısı | firmware/gpio-control.md L61 |
| K1.f.6.4 | Teknik Detaylar | firmware/gpio-control.md L90 |
| K1.f.6.5 | Derleme & Yükleme | firmware/gpio-control.md L716 |
| K1.f.6.6 | GPIO Pin Haritası (XMOS XU316) | firmware/gpio-control.md L92 |
| K1.f.6.7 | Button Handler | firmware/gpio-control.md L119 |
| K1.f.6.8 | LED Controller | firmware/gpio-control.md L233 |
| K1.f.6.9 | Rotary Encoder | firmware/gpio-control.md L399 |
| K1.f.6.10 | Display Driver (OLED SSD1306) | firmware/gpio-control.md L493 |
| K1.f.6.11 | Event Queue | firmware/gpio-control.md L607 |
| K1.f.6.12 | GPIO Interrupt Handler | firmware/gpio-control.md L671 |
| K1.f.6.13 | GPIO Firmware Derleme | firmware/gpio-control.md L718 |
| K1.f.6.14 | GPIO Test | firmware/gpio-control.md L734 |
| **K1.f.7** | I2S Driver (3. katman) | firmware/i2s-driver.md — 15 yaprak |
| K1.f.7.1 | Genel Bakış | firmware/i2s-driver.md L10 |
| K1.f.7.2 | Firmware Mimarisi | firmware/i2s-driver.md L14 |
| K1.f.7.3 | Kaynak Kod Yapısı | firmware/i2s-driver.md L63 |
| K1.f.7.4 | Teknik Detaylar | firmware/i2s-driver.md L84 |
| K1.f.7.5 | Derleme & Yükleme | firmware/i2s-driver.md L426 |
| K1.f.7.6 | I2S Protocol Overview | firmware/i2s-driver.md L86 |
| K1.f.7.7 | Clock Configuration | firmware/i2s-driver.md L109 |
| K1.f.7.8 | I2S Master Driver | firmware/i2s-driver.md L161 |
| K1.f.7.9 | Multi-Channel I2S | firmware/i2s-driver.md L286 |
| K1.f.7.10 | Sample Rate Conversion | firmware/i2s-driver.md L326 |
| K1.f.7.11 | Clock Recovery (Slave Mode) | firmware/i2s-driver.md L367 |
| K1.f.7.12 | Audio Quality Metrics | firmware/i2s-driver.md L407 |
| K1.f.7.13 | I2S Driver Derleme | firmware/i2s-driver.md L428 |
| K1.f.7.14 | I2S Test | firmware/i2s-driver.md L447 |
| K1.f.7.15 | DAC/ADC Konfigürasyonu | firmware/i2s-driver.md L460 |
| **K1.f.8** | Firmware index (3. katman) | firmware/index.md — 12 yaprak |
| K1.f.8.1 | Genel Bakış | firmware/index.md L10 |
| K1.f.8.2 | Firmware Mimarisi | firmware/index.md L14 |
| K1.f.8.3 | Kaynak Kod Yapısı | firmware/index.md L49 |
| K1.f.8.4 | Teknik Detaylar | firmware/index.md L88 |
| K1.f.8.5 | Derleme & Yükleme | firmware/index.md L134 |
| K1.f.8.6 | XMOS XU316 Seçim Gerekçesi | firmware/index.md L90 |
| K1.f.8.7 | Firmware Katmanları | firmware/index.md L99 |
| K1.f.8.8 | Real-Time Gereksinimleri | firmware/index.md L110 |
| K1.f.8.9 | Thread Kullanımı (XMOS) | firmware/index.md L121 |
| K1.f.8.10 | XMOS Firmware Derleme | firmware/index.md L136 |
| K1.f.8.11 | STM32 Firmware Derleme | firmware/index.md L151 |
| K1.f.8.12 | DFU ile Güncelleme | firmware/index.md L164 |

### K1.9 — Bileşen Haritası & BOM

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K1.9.1** | README §2-§7 (3. katman) | README.md — 20 yaprak |
| K1.9.1.1 | 2. Bileşen Haritası | README.md L42 |
| K1.9.1.2 | 3. PCM3168A DAC Detayı | README.md L93 |
| K1.9.1.3 | 4. Class AB Amplifikatör Detayı | README.md L135 |
| K1.9.1.4 | 5. XMOS XU316 Detayı | README.md L185 |
| K1.9.1.5 | 6. PCB Tasarım Kuralları | README.md L210 |
| K1.9.1.6 | 7. BOM Maliyet Analizi | README.md L238 |
| K1.9.1.7 | 2.1 USB Audio Interface | README.md L44 |
| K1.9.1.8 | 2.2 DAC (Digital-to-Analog Converter) | README.md L53 |
| K1.9.1.9 | 2.3 Amplifikatör | README.md L63 |
| K1.9.1.10 | 2.4 Güç Kaynağı | README.md L70 |
| K1.9.1.11 | 2.5 Hoparlör Matrisi (8.1 Surround) | README.md L78 |
| K1.9.1.12 | 3.1 Pin Out | README.md L95 |
| K1.9.1.13 | 3.2 I2S Konfigürasyonu | README.md L116 |
| K1.9.1.14 | 3.3 Analogy Output Devresi | README.md L127 |
| K1.9.1.15 | 4.1 Tek Kanal Devre Şeması | README.md L137 |
| K1.9.1.16 | 4.2 Bias Ayar Prosedürü | README.md L162 |
| K1.9.1.17 | 4.3 Termal Hesaplama | README.md L172 |
| K1.9.1.18 | 5.1 Blok Diyagramı | README.md L187 |
| K1.9.1.19 | 5.2 XMOS Kaynak Kullanımı | README.md L198 |
| K1.9.1.20 | 6.1 Stackup | README.md L225 |

### K1.10 — index.md

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K1.10.1** | K1 İndeksi (3. katman) | index.md — 5 yaprak |
| K1.10.1.1 | Genel Bakış | index.md L10 |
| K1.10.1.2 | Blok Diyagramı | index.md L14 |
| K1.10.1.3 | Bileşen Listesi | index.md L50 |
| K1.10.1.4 | Katman Bağımlılıkları | index.md L65 |
| K1.10.1.5 | Teknik Özet | index.md L73 |

### K1.11 — CLAUDE.md Guardrails

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K1.11.1** | K1 Guardrails (3. katman) | CLAUDE.md — 4 yaprak |
| K1.11.1.1 | Hard Guardrails | CLAUDE.md L16 |
| K1.11.1.2 | Bileşen Seçim Kısıtları | CLAUDE.md L26 |
| K1.11.1.3 | Yasaklı Bileşenler | CLAUDE.md L36 |
| K1.11.1.4 | İlgili ADR'ler | CLAUDE.md L44 |

### K1.12 — README Genel Bakış

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K1.12.1** | README §1 (3. katman) | README.md — 2 yaprak |
| K1.12.1.1 | 1. Genel Bakış | README.md L26 |
| K1.12.1.2 | 1.1 Temel İlkeler | README.md L30 |

## Kanıt Kataloğu (K1)

> Bu dizin, K1 şemasındaki 360 yaprağın dosya bazlı kaynağını verir. "Kapsanan" = şemaya giren yaprak; ham H2+H3 ile fark, aşağıdaki Bilinçli Çıkarım notunda açıklanır. Satır aralığı = dosyadaki ilk–son başlık satırıdır.

| # | Dosya | Rol | H2 | H3 | Kapsanan | Satır Aralığı |
|---|-------|-----|----|----|----------|---------------|
| 1 | README.md | Katman özeti, bileşen haritası, BOM | 9 | 15 | 22 | L26–L269 |
| 2 | CLAUDE.md | Hard guardrails | 4 | 0 | 4 | L16–L44 |
| 3 | index.md | Mimari indeks | 6 | 0 | 5 | L10–L83 |
| 4 | ak4458-dac.md | AK4458 DAC | 7 | 2 | 7 | L10–L114 |
| 5 | analog-sinyal-yolu.md | 5 aşamalı analog yol | 13 | 6 | 17 | L10–L209 |
| 6 | class-ab-amplifikator.md | Class-AB devresi | 7 | 3 | 8 | L10–L153 |
| 7 | dac-adc-zinciri.md | DAC-ADC zinciri | 9 | 3 | 10 | L10–L151 |
| 8 | diff-pair-input.md | Differential giriş | 8 | 5 | 11 | L10–L122 |
| 9 | feedback-network.md | Geri besleme ağı | 9 | 4 | 11 | L10–L145 |
| 10 | guc-kaynagi-analog.md | LM5122 boost PSU | 9 | 2 | 9 | L10–L156 |
| 11 | hoparlor-dizilimi.md | 8.1 hoparlör matrisi | 8 | 2 | 8 | L10–L125 |
| 12 | i2s-interface.md | I2S/TDM arayüzü | 10 | 5 | 13 | L10–L177 |
| 13 | koruma-devreleri.md | DC/OC/thermal/short | 9 | 7 | 14 | L10–L180 |
| 14 | konnektorler.md | XLR/RCA/USB-C/HDMI | 11 | 7 | 16 | L10–L194 |
| 15 | mjle21194-93.md | Output transistörler | 8 | 4 | 10 | L10–L148 |
| 16 | output-stage.md | Darlington output | 9 | 8 | 15 | L10–L190 |
| 17 | pcm3168a-dac-adc.md | Ana DAC/ADC | 6 | 2 | 6 | L10–L109 |
| 18 | pcb-tasarim.md | 6-layer PCB | 8 | 2 | 8 | L10–L177 |
| 19 | termal-yonetim.md | Soğutma/fan/NTC | 9 | 6 | 13 | L10–L160 |
| 20 | usb-audio.md | USB Audio Class | 11 | 4 | 13 | L10–L181 |
| 21 | vas-stage.md | VAS + Miller | 9 | 4 | 11 | L10–L160 |
| 22 | xmos-xu316.md | XMOS XU316 devresi | 6 | 2 | 6 | L10–L92 |
| 23 | firmware/bootloader.md | K1.f — bootloader/DFU | 7 | 11 | 16 | L10–L465 |
| 24 | firmware/xmos-firmware.md | K1.f — XMOS XC | 7 | 13 | 18 | L10–L336 |
| 25 | firmware/usb-audio-firmware.md | K1.f — UAC2 firmware | 7 | 10 | 15 | L10–L494 |
| 26 | firmware/mcu-support.md | K1.f — STM32/RPi | 7 | 13 | 18 | L10–L980 |
| 27 | firmware/dsp-firmware.md | K1.f — DSP blokları | 7 | 10 | 15 | L10–L592 |
| 28 | firmware/gpio-control.md | K1.f — GPIO/LED/display | 7 | 9 | 14 | L10–L758 |
| 29 | firmware/i2s-driver.md | K1.f — I2S master | 7 | 10 | 15 | L10–L485 |
| 30 | firmware/index.md | K1.f — indeks | 7 | 7 | 12 | L10–L189 |
| | **TOPLAM** | 30 dosya | **241** | **176** | **360** | |

**Katalog notları:**

1. **Bilinçli çıkarım (57 başlık):** 27 "Bağımlılıklar" + 28 "Durum: Implementasyon" + README §8 İlgili Dosyalar (L253) + §9 İlgili ADR'ler (L269). Ham toplam 417 − 57 = **360** yaprak.
2. **Satır bazlı ek kanıt (şemaya sayılmadı):** README §2 bileşen tabloları 20 satır (L48-89), §7 BOM 8 satır (L242-249 — **Amplifikatör 8 kanal = 120 bileşen**, L244), §1 ilkeler 5 satır (L34-38).
3. **K1.f:** firmware/ 8 MD'nin tamamı K1.f.1..K1.f.8 olarak 3. katmanda; 139 firmware yaprağı K1.f alt katmanına aittir.
4. **Kapsam:** 19 içerik MD + README + CLAUDE + index = 22 k1-donanim dosyası + 8 firmware dosyası = 30 kanıt dosyası (spec: 19 + 8 ✓).
5. **Adlandırma:** klasör/dosya adları lowercase-hyphen (k1-donanim, firmware, mjle21194-93, …), belgelerde K1 numarası taşınır.

---

*K1 Donanım Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-24 — genişletme: 3 turlu agent tartışması*
*Mode: Red Team · Human Mode · Truth Mode*
