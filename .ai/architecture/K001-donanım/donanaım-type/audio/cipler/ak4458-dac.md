---
title: "AK4458 — 32-bit 8-kanal Premium DAC (AKM VELVET SOUND) Teknoloji Derin-Dalı"
type: architecture-deepdive
node_id: "K001-donanım/donanaım-type/audio/cipler/ak4458-dac"
status: draft
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# AK4458 — 32-bit 8ch Premium DAC Teknoloji Derin-Dalı

> **Kapsam:** Bu dosya bir **teknoloji derin-dalı**dır (donanım çipi envanteri). Tasarım kararı,
> BOM satırı veya şema üretmez; her rakam okunan resmi kaynak (ürün sayfası veya datasheet) ile
> işaretlenmiştir. Okunmayan hiçbir parametre yazılmamıştır → eksikler `⚠️ VERIFICATION REQUIRED`
> olarak işaretlidir. Zero-Hallucination (kök `AGENTS.md` §3) bu dosyaya bağlayıcıdır.

---

## §0 Künye (Kimlik)

| Alan | Değer | Kaynak |
|---|---|---|
| Üretici | AKM (Asahi Kasei Microdevices) | akm.com ürün sayfası |
| Part Number | AK4458VN | akm.com ürün sayfası |
| Part Status | MP (Mass Production) | akm.com ürün sayfası |
| Başlık / Slogan | "115dB 768kHz / 32-bit 8 ch Premium DAC" | akm.com ürün sayfası |
| Mimari | VELVET SOUND (AKM premium ses mimarisi) | akm.com ürün sayfası |
| Kanal sayısı | 8 (4 diferansiyel çift × L/R: DAC1..DAC4 blokları) | datasheet §1, §4 |
| Çıkış tipi | Diferansiyel (AOUTxP / AOUTxN) | akm.com + datasheet §5 |
| Çözünürlük | 32 bit | datasheet §2(5) |
| Paket | 48-pin QFN (0.5mm pitch), HVQFN | datasheet §5 + akm.com |
| Sıcaklık | −40…+105 °C (exposed pad toprağa bağlı) · −40…+85 °C (pad açık) | datasheet §5 Ordering Guide |
| MSL / Ambalaj | MSL2 · TAPE · Pb Free: Yes · Halogen Free: Yes | akm.com ürün sayfası |
| MOQ | 1000 adet | akm.com ürün sayfası |
| Datasheet revizyonu | 014011794-E-02 (2017/07), 84+ sayfa | datasheet header |
| İlgili op-amp | AK4911ET / AK4912ET (AKM premium audio op-amp ailesi, sayfa önerisi) | akm.com ürün sayfası |

**Uygulama alanları (datasheet §1 + ürün sayfası):** AV Receivers · CD/SACD Players · Network
Audio · USB DACs · USB Headphones · Sound Plate/Bars · Audio Interfaces · Car Audio · External
Amplifiers for Car Audio · Measurement Equipment and Control Systems.

---

## §1 Blok Diyagramı ve Sinyal Akışı (ASCII)

```text
 MCLK ──► Clock Divider ◄── fs auto-detect (PCM mode)
                                  │
 BICK/DCLK ─┐                    │
 LRCK/DSDL1 ─┤ PCM Data      DSD Data
 SDTI1..4 ────► Interface      Interface ──► (DSDL1-4, DSDR1-4, DCLK)
                                  │
                    ┌─────────────┴─────────────┐
                    ▼                           ▼
        De-emphasis (32/44.1/48k)     DATT + Soft Mute (255 kademe, 0.5dB adım)
                    │                           │
                    └─────────────┬─────────────┘
                                  ▼
                 4 × 8x Interpolator (FIR, 1fs → 8fs)
                                  │
                 4 × ΔΣ Modulator (3. sıra, multi-bit)
                                  │
                 4 × Noise Rejection Filter (out-of-band bastırma)
                                  │
                 4 × SCF (switched-capacitor filter → analog)
                                  │
   ┌──────────┬──────────┬──────────┬──────────┐
   ▼          ▼          ▼          ▼          ▼
 DAC1      DAC2      DAC3      DAC4      Vref Bias
 AOUTL1P/N AOUTL2P/N AOUTL3P/N AOUTL4P/N
 AOUTR1P/N AOUTR2P/N AOUTR3P/N AOUTR4P/N
   ▲          ▲          ▲          ▲
 VREFH1/L1 VREFH2/L2 VREFH3/L3 VREFH4/L4   (harici referans pinleri)

 Kontrol yolları: I2C Bus · 3-wire Serial · Parallel (pin strap)
 Daisy Chain: SDTI3/TDMO1, SDTI4/TDMO2 (TDM data out)
 LDO (VDD18, 1.8V tip.) → dahili dijital çekirdek beslemesi
```

Kaynak: datasheet §4 "Block Diagram and Functions" (Figure 1) + §9 Functional Descriptions.

---

## §2 Pin Konfigürasyonu (48-pin QFN — datasheet §5 Pin Functions)

| No. | Pin Adı | I/O | İşlev (datasheet) | PD State (power-down) |
|---|---|---|---|---|
| 1 | MCLK | I | External Master Clock Input Pin | Hi-Z |
| 2 | BICK / DCLK | I / I | PCM: Audio Serial Data Clock · DSD: DSD Clock Pin | Hi-Z |
| 3 | LRCK / DSDL1 | I / I | PCM: Input Channel Clock · DSD: Audio Serial Data Input | Hi-Z |
| 4 | SDTI1 / DSDR1 | I / I | PCM: Audio Serial Data Input · DSD: Audio Serial Data Input | Hi-Z |
| 5 | SDTI2 / DSDL2 | I / I | PCM: Audio Serial Data Input · DSD: Audio Serial Data Input | Hi-Z |
| 6 | SDTI3 / DSDR2 / TDMO1 | I / I / O | PCM data in · DSD data in · Daisy Chain mode data out | 100kΩ pull-down |
| 7 | SDTI4 / DSDL3 / TDMO2 | I / I / O | PCM data in · DSD data in · Daisy Chain mode data out | 100kΩ pull-down |
| 8 | DSDR3 | I | DSD mode Audio Serial Data Input | Hi-Z |
| 9 | DSDL4 | I | DSD mode Audio Serial Data Input | Hi-Z |
| 10 | DSDR4 | I | DSD mode Audio Serial Data Input | Hi-Z |
| 11 | DZF / SMUTE | O / I | O: Zero Input Detect · I (parallel mode): soft mute init (H→mute, L→release) | 100kΩ pull-down |
| 12 | CAD1 / DCHAIN | I / I | I2C/3-wire: Chip Address 1 · Parallel: Daisy Chain Mode select | Hi-Z |
| 13 | SDA / CDTI / TDM0 | I/O / I / I | I2C data · 3-wire serial data in · Parallel: TDM mode select | Hi-Z |
| 14 | SCL / CCLK / TDM1 | I / I / I | I2C clock · 3-wire serial clock · Parallel: TDM mode select | Hi-Z |
| 15 | CAD0_I2C / CSN / DIF | I / I / I | I2C: Chip Address 0 · 3-wire: chip select · Parallel: format select ("L": 32-bit MSB, "H": 32-bit I2S) | Hi-Z |
| 16 | PS / CAD0_SPI | I / I | I2C="H": PS control mode select ("L": I2C bus, "H": parallel) · I2C="L": 3-wire chip address 0 | Hi-Z |
| 17 | I2C | I | Control mode select: "L": 3-wire serial · "H": I2C bus veya parallel | Hi-Z |
| 18 | AOUTL1P | O | Lch Positive Analog Output 1 Pin | Hi-Z |
| 19 | AOUTL1N | O | Lch Negative Analog Output 1 Pin | Hi-Z |
| 20 | VREFL1 | I | Negative Voltage Reference Input (AVSS) | Hi-Z |
| 21 | VREFH1 | I | Positive Voltage Reference Input (AVDD) | Hi-Z |
| 22 | AOUTR1N | O | Rch Negative Analog Output 1 Pin | Hi-Z |
| 23 | AOUTR1P | O | Rch Positive Analog Output 1 Pin | Hi-Z |
| 24 | AOUTL2P | O | Lch Positive Analog Output 2 Pin | Hi-Z |
| 25 | AOUTL2N | O | Lch Negative Analog Output 2 Pin | Hi-Z |
| 26 | VREFL2 | I | Negative Voltage Reference Input (AVSS) | Hi-Z |
| 27 | VREFH2 | I | Positive Voltage Reference Input (AVDD) | Hi-Z |
| 28 | AOUTR2N | O | Rch Negative Analog Output 2 Pin | Hi-Z |
| 29 | AOUTR2P | O | Rch Positive Analog Output 2 Pin | Hi-Z |
| 30 | AVSS | — | Analog Ground Pin | — |
| 31 | AVDD | — | Analog Power Supply Pin, 3.0V–5.5V | — |
| 32 | AOUTL3P | O | Lch Positive Analog Output 3 Pin | Hi-Z |
| 33 | AOUTL3N | O | Lch Negative Analog Output 3 Pin | Hi-Z |
| 34 | VREFH3 | I | Positive Voltage Reference Input (AVDD) | Hi-Z |
| 35 | VREFL3 | I | Negative Voltage Reference Input (AVSS) | Hi-Z |
| 36 | AOUTR3N | O | Rch Negative Analog Output 3 Pin | Hi-Z |
| 37 | AOUTR3P | O | Rch Positive Analog Output 3 Pin | Hi-Z |
| 38 | AOUTL4P | O | Lch Positive Analog Output 4 Pin | Hi-Z |
| 39 | AOUTL4N | O | Lch Negative Analog Output 4 Pin | Hi-Z |
| 40 | VREFH4 | I | Positive Voltage Reference Input (AVDD) | Hi-Z |
| 41 | VREFL4 | I | Negative Voltage Reference Input (AVSS) | Hi-Z |
| 42 | AOUTR4N | O | Rch Negative Analog Output 4 Pin | Hi-Z |
| 43 | AOUTR4P | O | Rch Positive Analog Output 4 Pin | Hi-Z |
| 44 | LDOE | I | Internal LDO Enable: "L": Disable, "H": Enable | Hi-Z |
| 45 | TVDD | — | Digital Power Supply Pin, 3.0V–3.6V (recomm. tablosunda LDOE="L" iken 1.7–3.6V) | — |
| 46 | DVSS | — | Digital Ground Pin | — |
| 47 | VDD18 | O / I | LDOE="H": LDO output (1.0µF ile DVSS'e bağlanmalı) · LDOE="L": 1.8V harici güç girişi | (Note 4) |
| 48 | PDN | I | Power-Down & Reset Pin ("L": power-down + registerlar default'a reset) | Hi-Z |
| Back pad | (exposed pad) | — | Açık bırakılmalı veya board ground'una bağlanmalı (datasheet Note 1) | — |

**Datasheet notları (§5):**
- Note 2: Internal pull-up/down pinleri hariç tüm input pinleri floating bırakılmamalı.
- Note 3: PCM/DSD modu register ile; Daisy Chain modu hem register hem pin ile kontrol edilir.
- Note 4: LDOE="H" iken VDD18 DVSS çıkış verir; LDOE="L" iken Hi-Z.

**Kullanılmayan pin işleme (datasheet §5 "Handling of Unused Pin"):**

| Sınıf | Pinler | Ayar |
|---|---|---|
| Analog | AOUTL1..4P/N, AOUTR1..4P/N | Bu pinler **open** bırakılmalı |
| Digital | DZF | open bırakılmalı |
| Digital | SDTI1-4, DSDR3, DSDL4, DSDR4 | DVSS'e bağlanmalı |

---

## §3 Mutlak Maksimum ve Önerilen Çalışma Koşulları (datasheet §6–§7)

**Mutlak Maksimum Ratings (AVSS=DVSS=0V):**

| Parameter | Symbol | Min | Max | Unit |
|---|---|---|---|---|
| Analog güç | AVDD | −0.3 | 6.0 | V |
| Digital I/O güç | TVDD | −0.3 | 4.0 | V |
| Digital core güç | VDD18 | −0.3 | 2.5 | V |
| AVSS/DVSS farkı | ΔGND | — | 0.3 | V |
| Pin başına akım (güç hariç) | IIN | — | ±10 | mA |
| Dijital input voltajı | VIND | −0.3 | TVDD+0.3 | V |
| Ortam sıcaklığı (pad toprağa bağlı) | Ta | −40 | 105 | °C |
| Ortam sıcaklığı (pad açık) | Ta | −40 | 85 | °C |
| Depolama sıcaklığı | Tstg | −65 | 150 | °C |

**Recommended Operation Conditions:**

| Parameter | Symbol | Min | Typ | Max | Unit |
|---|---|---|---|---|---|
| Analog güç (AVDD) | AVDD | 3.0 | 5.0 | 5.5 | V |
| Digital I/O (LDOE="L") | TVDD | 1.8 | — | 3.6 | V |
| Digital core (LDOE="H", internal LDO) | VDD18 | 1.7 | 1.8 | 1.98 | V |
| Digital I/O (LDOE="H") | TVDD | 3.0 | 3.3 | 3.6 | V |
| Voltage reference "H" | VREFH1-4 | AVDD−0.5 | AVDD | — | V |
| Voltage reference "L" | VREFL1-4 | — | AVSS | — | V |

**Güç sıralaması notları (datasheet Note 7–8):**
- LDOE="L" iken VDD18, TVDD ile aynı anda veya sonrasında güçlenmeli.
- AVDD↔TVDD veya AVDD↔VDD18 sıralaması kritik değildir.
- LDOE="H" iken dahili LDO 1.8V (tip) üretir; sıralama kritik değildir.

---

## §4 Analog Performans (datasheet §8 — iki besleme senaryosu)

**Koşul (1): AVDD=5.0V, TVDD=3.3V, fs=44.1kHz, BICK=64fs, 1kHz, 24-bit, RL≥2kΩ, BW 20Hz–20kHz:**

| Parameter | Min | Typ | Max | Unit |
|---|---|---|---|---|
| Resolution | — | — | 32 | bit |
| THD+N, fs=44.1kHz 0dBFS (BW=20kHz) | — | −107 | −100 | dB |
| THD+N, fs=44.1kHz −60dBFS | — | −52 | — | dB |
| THD+N, fs=96kHz 0dBFS (BW=40kHz) | — | −104 | — | dB |
| THD+N, fs=96kHz −60dBFS | — | −48 | — | dB |
| THD+N, fs=192kHz 0dBFS (BW=40kHz) | — | −104 | — | dB |
| THD+N, fs=192kHz −60dBFS (BW=40kHz) | — | −48 | — | dB |
| THD+N, fs=192kHz −60dBFS (BW=80kHz) | — | −44 | — | dB |
| Dynamic Range (−60dBFS, A-weighted) | 110 | 115 | — | dB |
| S/N (A-weighted) | 110 | 115 | — | dB |
| Interchannel Isolation (1kHz) | 100 | 110 | — | dB |
| Interchannel Gain Mismatch | — | 0 | 0.3 | dB |
| Gain Drift | — | 20 | — | ppm/°C |
| Output Voltage (Note 13) | ±2.65 | ±2.8 | ±2.95 | Vpp |
| Load Resistance | 2 | — | — | kΩ |
| Load Capacitance | — | — | 30 | pF |
| AVDD akımı (normal, PDN="H") | — | 31 | 41 | mA |
| TVDD akımı fs=44.1kHz | — | 8 | 11 | mA |
| TVDD akımı fs=96kHz | — | 13 | 17 | mA |
| TVDD akımı fs=192kHz | — | 20 | 26 | mA |
| Power-down akımı (PDN="L", AVDD+TVDD) | — | 1 | 100 | µA |

**Koşul (2): AVDD=3.3V (aynı diğer koşullar):**

| Parameter | Min | Typ | Max | Unit |
|---|---|---|---|---|
| THD+N, fs=44.1kHz 0dBFS (BW=20kHz) | — | −93 | −86 | dB |
| THD+N, fs=44.1kHz −60dBFS | — | −48 | — | dB |
| THD+N, fs=96kHz 0dBFS (BW=40kHz) | — | −92 | — | dB |
| THD+N, fs=192kHz 0dBFS (BW=40kHz) | — | −92 | — | dB |
| Dynamic Range (−60dBFS, A-weighted) | 106 | 111 | — | dB |
| S/N (A-weighted) | 106 | 111 | — | dB |
| Output Voltage | ±1.66 | ±1.85 | ±2.04 | Vpp |
| AVDD akımı (normal, faz opposed L/R) | — | 24 | — | mA |
| TVDD akımı fs=44.1kHz | — | 8 | — | mA |

**Ölçüm notları (datasheet §8 Note 9–15):**
- Note 9: Audio Precision System Two, averaging mode ile ölçülmüştür.
- Note 13: Full scale, PCM'de 1kHz 0dB sine; DSD'de 1kHz %25–75 duty sine. Çıkış
  (VREFH−VREFL) voltajıyla orantılıdır: `AOUT = ±2.8Vpp × (VREFHn − VREFLn)/5`.
- Note 14: AC yük DC kesme kapasitörüyle 2kΩ (min); DC kesme yoksa DC yük 3.5kΩ (min).
  Kapasitif yük minimize edilmelidir (analog karakteristikler kapasitife duyarlı).
- Typical connection diagram notu (datasheet §10): BICK=64fs, LRCK=fs; AVDD/TVDD
  ayrı LDO'dan dağıtılmalı; mümkün değilse VREFL1-4 ↔ VREFH1-4 arasına 10Ω + 220µF LPF;
  DVSS ile AVSS aynı potansiyelde olmalı; tüm digital input pinleri floating bırakılmaz.

---

## §5 Dijital Filtre Modları (datasheet §2(5) + §8 filtre tabloları)

**Seçilebilir 5 filtre tipi (datasheet §2(5)):**

| # | Filtre tipi | GD (grup gecikmesi) | Durum |
|---|---|---|---|
| 1 | Sharp Roll-Off Filter | 26.8/fs (fs=44.1k) | §8 tablosu okundu (aşağıda) |
| 2 | Slow Roll-Off Filter | — | ⚠️ VERIFICATION REQUIRED (§8 tablosu bu oturumda okunmadı) |
| 3 | Short Delay Sharp Roll-Off Filter | 5.8/fs | §8 tablosu okundu (fs=192k) |
| 4 | Short Delay Slow Roll-Off Filter | 4.8/fs | §8 tabloları okundu (44.1k/96k/192k) |
| 5 | Super Slow Roll-Off Filter | — | ⚠️ VERIFICATION REQUIRED (§8 tablosu bu oturumda okunmadı) |

**Sharp Roll-Off (fs=44.1kHz, Normal Speed, DEM=OFF, SLOW="0", SD="0"):**

| Parameter | Symbol | Min | Typ | Max | Unit |
|---|---|---|---|---|---|
| Pass band (±0.05dB) | PB | 0 | — | 20.0 | kHz |
| Pass band (−3.0dB) | PB | — | 21.5 | — | kHz |
| Pass band Ripple | PR | −0.0032 | — | 0.0032 | dB |
| Stop band | SB | 24.1 | — | — | kHz |
| Stop band Attenuation | SA | 80 | — | — | dB |
| Group Delay | GD | — | 26.8 | — | 1/fs |

**Short Delay Sharp Roll-Off (fs=192kHz, Quad Speed, SD="1"):**

| Parameter | Symbol | Min | Typ | Max | Unit |
|---|---|---|---|---|---|
| Pass band (±0.05dB) | PB | 0 | — | 87.0 | kHz |
| Pass band (−3.0dB) | PB | — | 93.6 | — | kHz |
| Pass band Ripple | PR | −0.0031 | — | 0.0031 | dB |
| Stop band | SB | 105 | — | — | kHz |
| Stop band Attenuation | SA | 80 | — | — | dB |
| Group Delay | GD | — | 5.8 | — | 1/fs |
| Freq. Response (DF+SCF) 0–80.0kHz | — | −1 | — | 0.1 | dB |

**Short Delay Slow Roll-Off — üç hız (SLOW="1", SD="1"):**

| fs | PB (±0.05dB) | PB (−3.0dB) | PR | SB | SA | GD | DF+SCF FR bandı |
|---|---|---|---|---|---|---|---|
| 44.1 kHz | 0–11.1 kHz | 19.4 kHz | ±0.05 dB | 38.1 kHz | 82 dB | 4.8/fs | 0–20.0kHz, −5…+0.1dB |
| 96 kHz | 0–24.2 kHz | 42.1 kHz | ±0.05 dB | 83.0 kHz | 82 dB | 4.8/fs | 0–40.0kHz, −5…+0.1dB |
| 192 kHz | 0–48.4 kHz | 84.3 kHz | ±0.05 dB | 165.9 kHz | 82 dB | 4.8/fs | 0–80.0kHz, −5…+0.1dB |

- Note 20: PB ve SB fs ile ölçeklenir (örn. PB=0.252×fs, SB=0.864×fs).
- Feature list §2(5): Sharp filtrede ripple ±0.0032dB, attenuation 80dB (256x oversampling,
  "OSR-Doubler" teknolojisi).

---

## §6 DSD Modu (datasheet §8 DSD Mode Characteristics)

- Çip DSD girişi destekler: maks. **11.2 MHz DSD** (datasheet §1/§2).
- DSD pinleri: DCLK (pin 2), DSDL1-4, DSDR1-4 (pinler 3-10, multiplexed).
- DSDF bit = "0" (fs=44.1kHz, D/P="1") frekans tepkisi:

| DSDSEL[1:0] | 20kHz | 50kHz (veya ölçekli) | 100kHz (veya ölçekli) |
|---|---|---|---|
| "00" | −0.8 dB | −5.5 dB | −19.9 dB |
| "01" | −0.8 dB @40kHz | −5.5 dB @200kHz | −19.9 dB @400kHz |
| "10" | −0.8 dB @80kHz | −5.5 dB @400kHz | −19.9 dB @800kHz |

- DSDF bit = "1" iken aynı DSDSEL kodları için: −0.2 / −6.3 / −23.7 dB (aynı frekans noktaları).
- Note 21: DSD sinyal seviyesi %25–75 duty aralığında (SACD Scarlet Book).
- DSD timing: 64/128/256 modları (DSDSEL="00"/"01"/"10") — DCLK periyodu 1/64fs, 1/128fs,
  1/256fs; pulse width 144ns / 72ns / 36ns; DCLK edge→DSDL/R ±20ns / ±10ns / ±5ns (Note 28).

---

## §7 Ses Arayüzü: I2S / TDM / Daisy Chain (datasheet §8 Switching Characteristics)

**TDM modları (TDM1-0 paralel kontrol pinleri, pin 13/14):**

| TDM1-0 | Mod | LRCK davranışı | BICK |
|---|---|---|---|
| "00" | Normal | fsn 8–54kHz · fsd 54–108kHz · fsq 108–216kHz · fso 384kHz · fsh 768kHz; duty 45–55% | Normal: 1/256fsn · Double: 1/128fsd · Quad: 1/64fsq · Oct: 1/64fso · Hex: 1/64fsh |
| "01" | TDM128 | fsn 8–54 · fsd 54–108 · fsq 108–216 kHz; tLRH/tLRL = 1/128fs | Normal 1/128fsn · Double 1/128fsd · Quad 1/128fsq |
| "10" | TDM256 | fsn 8–54 · fsd 54–108 kHz; tLRH/tLRL = 1/256fs | Normal 1/256fsn · Double 1/256fsd |
| "11" | TDM512 | fsn 8–54 kHz; tLRH/tLRL = 1/512fs | Normal 1/512fsn |

**Ortak timing kısıtları:**
- BICK pulse width low/high: Normal modda min 9ns; TDM modlarında min 14ns.
- BICK "↑" → LRCK edge (tBLR) ve LRCK edge → BICK "↑" (tLRB): min 5ns.
- SDTI hold/setup (tSDH/tSDS): min 5ns.
- TDMO1/2 setup/hold (TDM256/512): tBSS min 5ns, tBSH min 5ns (TVDD<2.6V ve LDOE="L" ise 4ns).
- Note 24: BICK rising edge, LRCK edge ile aynı anda OLAMAZ.
- Note 25/26: Daisy Chain modunda TVDD<3.0V ise fsd max 96kHz (TDM256) / 48kHz (TDM512).

**I/F format desteği (datasheet §2(13)):** 24/32bit MSB justified · 16/20/24/32bit LSB
justified · I2S · DSD · TDM. DIF pin (15, parallel mode): "L"=32-bit MSB, "H"=32-bit I2S.

**Daisy Chain (datasheet §2(9) + §5):** İkinci AK4458'ye TDMO1 üzerinden 8ch kaydırılarak
data aktarılabilir (datasheet Figure 38 TDM512 örneği: SDTI1 = L5-8/R5-8 → ikinci çipin DAC
girişi, ikinci çip TDMO1'den çıktı verir).

---

## §8 Master Clock İlişkisi (datasheet §2(15) + §9 System Clock)

**MCLK fs aralıkları (datasheet §2(15)) — iki artı işaretli uç değer çifti:**

| fs aralığı | İzinli MCLK oranı |
|---|---|
| 30kHz–32kHz | 1152fs |
| 30kHz–54kHz | 512fs veya 768fs |
| 30kHz–108kHz | 256fs veya 384fs |
| 108kHz–216kHz | 128fs veya 192fs |
| ~384kHz | 64fs veya 128fs |
| ~768kHz | 64fs |

**MCLK frekans sınırı (switching characteristics):** fCLK min 2.048 MHz, max 49.152 MHz;
duty cycle 40–60%; min pulse width tCLKH/tCLKL 9.155ns.

**İki kablo modu (datasheet §9 "System Clock [1] PCM Mode"):**
- **Manual Setting Mode (ACKS="0", default):** sampling speed DFS2-0 bitleriyle seçilir;
  MCLK frekansı harici sağlanır. Reset çıkarken (PDN "↑") çip bu moddadır. DFS2-0
  değişince RSTN bitiyle reset gerekir.
- **Auto Setting Mode (ACKS="1"):** MCLK frekansı otomatik algılanır; DFS bitleri gerekmez.
- MCLK, LRCK ile senkron olmalı fakat fazı kritik değildir; MCLK dijital interpolation
  filter ve delta-sigma modulator'ü sürer.
- DSD modunda master clock frekansı DCKS biti ile seçilir (datasheet §4 Clock Divider).
- Note 23: 1152fs/512fs/768fs ↔ 256fs/384fs ↔ 128fs/192fs geçişlerinde PDN veya RSTN ile
  reset şarttır.

---

## §9 Kontrol Arayüzü ve Konfigürasyon Bitleri (datasheet §9)

**Üç kontrol yolu:** I2C Bus · 3-wire Serial · Parallel (pin strap). Mod pinleri: I2C (17),
PS (16), CAD0_I2C/CSN/DIF (15), CAD1/DCHAIN (12), TDM1/TDM0 (14/13), DIF (15).

**3-wire serial timing:** CCLK periyodu min 200ns; pulse width low/high min 80ns; CDTI
setup/hold min 40ns; CSN "H" time min 150ns; tCSS/tCSH min 50ns.

**I2C timing:** SCL max 400kHz; tBUF min 1.3µs; tHD:STA min 0.6µs; tLOW min 1.3µs; tHIGH
min 0.6µs; tSU:STA min 0.6µs; tHD:DAT min 0; tSU:DAT min 0.1µs; tR max 1.0µs; tF max 0.3µs;
tSU:STO min 0.6µs; spike filter tSP max 50ns; bus kapasitansı Cb max 400pF.

**Power-down & reset timing:** PDN accept pulse width min 150ns; reject pulse width max 30ns
(<30ns "L" reset üretmez).

**Datasheet metninde geçen konfigürasyon bitleri (§8–§9 bağlamından, register adları):**
DP (PCM/DSD mode) · ACKS (auto clock setting) · DFS2-0 (sampling speed) · SLOW (slow roll-off
seçimi) · SD (short delay seçimi) · DEM1-0 (de-emphasis 32/44.1/48kHz) · DSDSEL1-0 (DSD clock
modu) · DSDF (DSD filter) · DCKB (DSD clock polarity) · DCKS (DSD master clock) · SELLR
(DSD L/R data select) · MONO (DAC mono mod) · RSTN (soft reset) · DCHAIN (daisy chain).

> ⚠️ **VERIFICATION REQUIRED:** Register Map (datasheet §9 "Register Map", s.74) ve Register
> Definitions (s.75) bölümlerinin tam register adres/bit içeriği bu oturumda okunmadı; register
> adresleri bu dosyaya YAZILMAMIŞTIR. NXP MCUXpresso SDK API enumları (kaynak: mcuxpresso.nxp.com
> Ak4458 API doc) bu bit adlarını bağımsız olarak doğrular (kAK4458_Normal/TDM128/TDM256/TDM512,
> DCKS, DSDSEL, DEM, SELLR, MONO).

**Digital De-emphasis (datasheet §2(10)):** 32, 44.1 ve 48kHz sampling için (PCM mode).

---

## §10 DC Karakteristikler ve Zamanlama Şemaları (datasheet §8)

**DC Characteristics (Ta=−40…105°C, AVDD=3.0–5.5V, TVDD=1.7–3.6V):**

| Parameter | Symbol | Koşul | Min | Typ | Max | Unit |
|---|---|---|---|---|---|---|
| High-Level Input Voltage | VIH | TVDD=1.7–3.0V | 80%TVDD | — | — | V |
| Low-Level Input Voltage | VIL | TVDD=1.7–3.0V | — | — | 20%TVDD | V |
| High-Level Input Voltage | VIH | TVDD=3.0–3.6V | 70%TVDD | — | — | V |
| Low-Level Input Voltage | VIL | TVDD=3.0–3.6V | — | — | 30%TVDD | V |
| High-Level Output Voltage | VOH | LRCK/BICK, Iout=−100µA | TVDD−0.5 | — | — | V |
| Low-Level Output Voltage | VOL | SDA hariç, Iout=100µA | — | — | 0.5 | V |
| Low-Level Output Voltage | VOL | SDA, 2.0V≤TVDD≤3.6V, Iout=3mA | — | — | 0.4 | V |
| Low-Level Output Voltage | VOL | SDA, 1.7V≤TVDD≤2.0V, Iout=3mA | — | — | 0.5 | V |
| Input Leakage Current | Iin | — | — | — | ±10 | µA |

**Zamanlama diyagramı indeksi (datasheet §8 Figures 10–17):**

| Fig. | İçerik | Kullandığı parametreler |
|---|---|---|
| Figure 10 | Clock Timing (MCLK/LRCK/BICK) | 1/fCLK, tCLKH, tCLKL, dCLK, tLRH, tLRL, tBCK, tBCKL, tBCKH |
| Figure 11 | Audio Interface Timing (PCM mode) | tBLR, tLRB, tSDH, tSDS, tBSS, tBSH (TDMO) |
| Figure 12 | Audio Serial Interface (DSD Normal, DCKB="0") | tDCK, tDCKL, tDCKH, tDDD |
| Figure 13 | Audio Serial Interface (DSD Phase Modulation, DCKB="0") | tDDD (çift kenar) |
| Figure 14 | WRITE Command Input Timing (3-wire serial) | tCSS, tCCKL, tCCKH, tCDS, tCDH; çerçeve: C1 C0 R/W A4 |
| Figure 15 | WRITE Data Input Timing (3-wire serial) | tCSW, tCSH; veri D3 D2 D1 D0 |
| Figure 16 | I2C Bus Timing | tBUF, tHD:STA, tLOW, tHIGH, tR, tF, tSU:STA, tHD:DAT, tSU:DAT, tSU:STO, tSP |
| Figure 17 | Power-down & Reset Timing (PDN + 3-wire) | tAPD, tRPD, tCSS |

- Clock tanımı: dCLK = tCLKH×fCLK ve tCLKL×fCLK (duty); Duty = tLRH×fs, tLRL×fs.
- 3-wire WRITE çerçevesi: C1 C0 R/W A4 (komut), ardından D3–D0 (veri) — datasheet Figure 14/15.
- PDN reset: güç-up'ta PDN "L"→"H" ile reset serbest bırakılır; "L" süresi >150ns (tAPD),
  <30ns (tRPD) reset sayılmaz.

---

## §11 Sipariş, Paket, İlgili Belgeler ve Aile Karşılaştırması

**Ordering Guide (datasheet §5):**

| Part / Kit | Ayrıntı |
|---|---|
| AK4458VN | −40…+105°C, exposed pad toprağa bağlı · 48-pin QFN (0.5mm pitch) |
| (ayrı kayıt) | −40…+85°C varyantı: exposed pad açık — datasheet metni iki sıcaklık/pad durumunu listeler |
| AKD4458 | AK4458 için Evaluation Board (AKM referans kartı) |

**İlgili belgeler (akm.com ürün sayfası — "Related Documents"):**

| Belge | URL (akm.com) |
|---|---|
| Datasheet (EN) | /content/dam/documents/products/audio/audio-dac/ak4458vn/ak4458vn-en-datasheet.pdf |
| Evaluation Board Manual (EN) | /content/dam/documents/products/audio/audio-dac/ak4458vn/ak4458vn-en-evaluationmanual.pdf |
| Quality Specifications | /content/dam/documents/quality/specifications/ak4xxx/ak4458vn-en-qualityspecifications.pdf |

**Ürün sayfası spesifikasyon tablosu (ek satırlar):**

| Alan | Değer |
|---|---|
| Analog Output Type | Differential |
| S/N [dB] | 115 / 111 (AVDD=5V / 3.3V karşılığı — datasheet §8 ile tutarlı) |
| THD+N [dB] | −107 |
| Power Supply (MIN/TYP/MAX) [V] | 3.0/3.0 · 5.0/3.3 · 5.5/3.6 (analog / digital sütunları) |
| 2Vrms Line Driver | − (yok) |
| Headphone Amplifier | − (yok) |
| Other | VELVETSOUND, Automotive grade |

**AKM premium DAC aile karşılaştırması (ikincil kaynak: datasheet4u.com/octopart listesi,
2026-10-12 erişim):**

| Part | Kanal | Başlık |
|---|---|---|
| AK4452 | 2 ch | 115dB 768kHz 32-bit 2ch Premium DAC |
| AK4454 | — | Premium DAC (kanal sayısı ⚠️ VERIFICATION REQUIRED) |
| AK4456 | 6 ch | 115dB 768kHz 32-bit 6ch Premium DAC |
| AK4458 | 8 ch | 115dB 768kHz 32-bit 8ch Premium DAC (bu dosya) |

**Typical Connection diyagram notları (datasheet §10, Figure 81/82/83):**
- Figure 81: AVDD=5V, TVDD=3.3V; Chip Address="00"; BICK=64fs, LRCK=fs.
- Güç hatları AVDD ve VREFH1-4, LDO vb. ile düşük empedansta dağıtılmalı; mümkün değilse
  VREFL1-4↔VREFH1-4 arası 10Ω + 220µF LPF.
- Figure 82 varyantı: LDOE="L" + I2C bus control mode (I2C pin="H").
- Çıkış DC kesme kapasitörü 0.1µF; çıkış başına LPF (L2ch) uygulanır (Figure 81 çizimi).

---

## §12 CoreMusic Rolü (Bu Çip Sisteminde Nerede Durur)

| Konu | CoreMusic bağlamı | Kaynak |
|---|---|---|
| Rol | Çok-kanallı analog çıkış DAC'ı (çok-kanallı çıkış hedefi) | Görev manifesti 2026-10-12 |
| Vault konumu | ADR-038 (brain.md): DAC/USB çekirdeği = PCM3168A + XMOS XU316; **PCM5122 yasak** | `.claude/skills/hardware-electronics/SKILL.md` §7 |
| Yasak etkileşimi | AK4458, PCM5122 yasağı kapsamında bir **alternatif** olarak envanterdedir; BOM kararı bu dosyada VERİLMEDİ | skill §6 H001 |
| Amfi topolojisi | Amplifikatör topolojisi **Class AB** (K16); AK4458 diferansiyel çıkışı Class AB giriş zincirine sürücü olarak değerlendirilir | kök CLAUDE.md §Sabitler; `.ai/CLAUDE.md` §5 K16 |
| Kanal mimarisi | K16: 1–8 modüler kanal; AK4458'nin 8 kanalı (4× diferansiyel çift) bu modüler kanal konseptiyle sayısal uyum sağlar (kanal başına 1 çift) | skill §1 tablosu |
| Arayüz | AK4458 I2S/TDM girişi → K19 PCB kuralı: 50Ω I2S empedans çizgisi | skill §1 (K19) |
| Seviye planı | AK4458 diferansiyel çıkış ±2.8Vpp tip. (AVDD=5V) → K16 Class AB girişi için seviye/empedans eşleştirmesi ayrı bir `audio-hardware-engineer` tasarımıdır (bu dosya karar vermez) | datasheet §8 Note 13 + skill §2 |
| Ölçüm | SNR 115dB (datasheet) vs K16 hedef SNR >105dB → DAC, amfi zinciri için SNR marjı sağlar (marj = 115−105 = 10dB, tip. değerler üzerinden) | datasheet §8 + skill §1 |
| Önerilen op-amp | AKM sayfası AK4911ET/AK4912ET önerir (LPF/buffer aşaması için); BOM'a alma kararı bu dosyada YOK | akm.com ürün sayfası |

**Ölçüm durumu:** Prototip yok → tüm CoreMusic hedefleri **PLANNED** (§2 Ölçüm standardı:
"ölçüm yoksa PLANNED"). Bu dosya APPROVE/REVISE kararı üretmez; status=draft.

---

## §13 Doğrulama Tablosu (id · URL · tarih · durum)

| §id | Kaynak URL | Tarih | Durum |
|---|---|---|---|
| §0-§2 | https://www.akm.com/us/en/products/audio/audio-dac/ak4458vn/ | 2026-10-12 erişim | ✅ DOĞRULANDI (ürün sayfası live) |
| §1-§9 | https://www.akm.com/content/dam/documents/products/audio/audio-dac/ak4458vn/ak4458vn-en-datasheet.pdf (mirror: https://static.datasheets.com/doc/26692303-asahi-kasei-micro-ak4458vn-ds.pdf) | Datasheet rev 014011794-E-02 (2017/07); erişim 2026-10-12 | ✅ DOĞRULANDI (pin/filtre/timing tabloları okundu) |
| §9 register map | Aynı datasheet §9 "Register Map" s.74–75 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED (içerik okunmadı) |
| §5 Slow/Super Slow filtre | Aynı datasheet §8 filtre tabloları | 2026-10-12 | ⚠️ VERIFICATION REQUIRED (tablo değerleri okunmadı) |
| §9 bit adları bağımsız doğrulama | https://mcuxpresso.nxp.com/api_doc/dev/4621/a00011.html | 2026-10-12 erişim | ✅ DOĞRULANDI (SDK enum adları) |
| §0 datasheet indeks | https://www.alldatasheet.com/datasheet-pdf/pdf/917537/AKM/AK4458.html | 2026-10-12 erişim | ✅ DOĞRULANDI (84 sayfa, "115dB 768kHz 32-bit 8ch Premium DAC") |
| §12 vault atıfları | `.claude/skills/hardware-electronics/SKILL.md` (disk) · kök `CLAUDE.md` §Sabitler (disk) | 2026-10-12 okundu | ✅ DOĞRULANDI (disk kanıtı) |
| §11 aile karşılaştırması | https://datasheet4u.com/datasheets/AKM/AK4458/1039769 | 2026-10-12 erişim | ✅ DOĞRULANDI (ikincil kaynak; AK4454 kanal sayısı ⚠️) |
| §11 belge linkleri | akm.com "Related Documents" bölümü | 2026-10-12 erişim | ✅ DOĞRULANDI (URL yolları ürün sayfasından) |

---

## §14 Açık Sorular

1. **Kanal sayısı ihtiyacı:** CoreMusic K16 1–8 modüler kanal destekler; AK4458'nin 8 kanalı
   tamamı mı kullanılacak, yoksa 2 kanal (1 çift) mı? → owner kararı (ADR-090 varyant SKU ile
   ilişkili).
2. **Besleme senaryosu:** AVDD=5.0V (THD+N −107dB) mı, AVDD=3.3V (−93dB) mı? 5V senaryosu
   K17 ±35V güç ağacından nasıl türetilecek (düşük gürültülü LDO seçimi) → K17 kapsamı.
3. **Filtre seçimi:** Sharp (GD 26.8/fs) mi, Short Delay Slow (GD 4.8/fs) mı? Ölçüm hedefi
   (THD vs group delay) yok → VERIFICATION REQUIRED.
4. **Clock kaynağı:** MCLK kimden gelecek (XMOS XU316 mı, ayrı crystal mı)? Auto Setting
   Mode (ACKS="1") kullanılırsa DFS strap'leri gerekmez ama hangi fs aralığı hedefleniyor?
5. **Daisy Chain:** 8+ kanal gerekecekse iki AK4458 zinciri mi, tek PCM3168A mı? (ADR-038
   PCM3168A der; AK4458 alternatif olarak bekliyor.)

---

## §15 Authority

*Authority: `.ai/CLAUDE.md` §5 (K16-K20) · kök `CLAUDE.md` §Sabitler (Class AB, PCM5122 yasak)
· ADR-038 (`.ai/brain.md`) · ADR-089 · skill: `hardware-electronics` (v3.0). Otorite sırası:
`.ai/CLAUDE.md` > `.ai/AGENTS.md` > `.ai/WORKFLOW.md` > `.ai/brain.md`. Bu dosya
**teknoloji derin-dalı (draft)**tır; tasarım kararı, BOM satırı veya şema üretmez. Değişiklik
yetkisi: `audio-hardware-engineer` (§9 çıktı standardı) — `.ai/electronic/**` yazımı owner
onayı gerektirir. node_id: `K001-donanım/donanaım-type/audio/cipler/ak4458-dac` · 2026-10-12.*
