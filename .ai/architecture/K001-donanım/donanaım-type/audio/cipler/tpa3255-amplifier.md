---
title: "TPA3255 — 315W Stereo Class-D Amplifier (TI PurePath Ultra-HD) Teknoloji Derin-Dalı [REFERANS — CLASS D YASAK]"
type: architecture-deepdive
node_id: "K001-donanım/donanaım-type/audio/cipler/tpa3255-amplifier"
status: draft
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# TPA3255 — Class-D Amplifier Teknoloji Derin-Dalı (Salt Referans / Alternatif Teknoloji Kaydı)

> ⚠️ **REPO KURALI ÇELİŞKİSİ — EN ÜSTTE (bağlayıcı):**
> CoreMusic amplifikatör topolojisi **Class AB'dir; Class D topoloji YASAKTIR**
> (kök `CLAUDE.md` §Sabitler: "Amplifikatör topolojisi Class AB (Class D yasak)";
> `hardware-electronics` skill §6: H001 — "Class D topoloji → anında reddet";
> ADR-089 §3.1-A1: Class AB-only).
> **Bu dosya TPA3255'i bir "referans / alternatif teknoloji" olarak envantere kaydeder.
> Tasarım kararı VERMEZ, BOM'a ALMAZ, şima üretmez.** Herhangi bir gelecekte Class-D'ye
> reopening talebi **ADR'ye taşınmalı** ve owner onayı gerektirir (Guardrail #14).
> Okunmayan parametre `⚠️ VERIFICATION REQUIRED`dir. Zero-Hallucination bağlayıcıdır.

---

## §0 Künye (Kimlik)

| Alan | Değer | Kaynak |
|---|---|---|
| Üretici | Texas Instruments | ti.com ürün sayfası |
| Part Number | TPA3255 | ti.com |
| Part Status | ACTIVE | ti.com |
| Başlık | "315-W stereo, 600-W mono, 18- to 53.5-V supply, analog input Class-D audio amplifier" | ti.com |
| Mimari | Class-D, AD-mode, PurePath Ultra-HD (integrated feedback + high-speed gate driver error correction) | ti.com + datasheet §3 |
| Analog input | Differential, 2 VRMS arayüz (TI PCM5242 ile uyumlu cümlesi datasheet §3'te) | datasheet §3 |
| MOSFET | Entegre 85 mΩ (RDS(on) typ. 85 mΩ, max 100 mΩ, GVDD=12V) | datasheet §3 + §7.5 |
| Paket | HTSSOP-44 (DDV), PowerPAD, 6.10mm×14.00mm (14×8.1mm) | ti.com + datasheet §6 |
| Çalışma sıcaklığı | 0…70 °C (ti.com product details: "Operating temperature range") | ti.com |
| Datasheet | SLASEA8A — FEBRUARY 2016, REVISED OCTOBER 2016 (Rev. A, Oct 4 2016 tarihli liste) | datasheet header + ti.com |
| Idle losses | <2.5W (4 kanal, PO=0, IPVDD=0; anahtarlanmış) | datasheet §7.6 |
| EMI | "EMI Compliant When Used With Recommended System Design" | ti.com features |

**Uygulama alanları (datasheet §2):** Blu-Ray/DVD Receivers · High End HTiB Systems · AV
Receivers · High End Soundbar · Mini Combo Systems · Active Speakers and Subwoofers.

---

## §1 Blok Diyagramı (ASCII — datasheet §3 "Simplified Schematic" öğeleri)

```text
 Audio Source          TPA3255 (AD-mode Class-D)              LC Filter      LOAD
 (2 VRMS diferansiyel)                                                     
 INPUT_A ──►│Half Bridge A│── OUT_A (pin 39,40) ── LDEM/CDEM ──► 4Ω BTL
 INPUT_B ──►│Half Bridge B│── OUT_B (pin 35)     ── LDEM/CDEM ──►
 INPUT_C ──►│Half Bridge C│── OUT_C (pin 32)     ── LDEM/CDEM ──►
 INPUT_D ──►│Half Bridge D│── OUT_D (pin 27,28)  ── LDEM/CDEM ──►

 PVDD_AB (36,37,38) ──► A+B güç; PVDD_CD (29,30,31) ──► C+D güç (18–53.5V)
 GVDD_AB (1) / GVDD_CD (22) ── gate-drive (0.1µF each)
 BST_A/B/C/D (44,43,24,23) ── bootstrap (0.033µF to OUT_x)
 M1(3), M2(4) ── mod seçimi (stereo/2.1/PBTL/4xSE)
 FREQ_ADJ(8) ── PWM frame rate (R_FREQ_ADJ); OSC_IOM/OSC_IOP(9,10) ── sync
 OC_ADJ(7) ── over-current eşiği (R_OC)
 RESET(18) ── aktif-low reset · FAULT(19) ── shutdown (open-drain) · CLIP_OTW(21) ── clipping+OTW (open-drain)
 C_START(15) ── startup ramp · VBG(20) ── internal reference (1µF) · DVDD(11)/AVDD(14) ── internal regs
```

---

## §2 Pin Konfigürasyonu (HTSSOP-44 DDV — datasheet §6 Pin Functions)

| Pin | Ad | I/O | Açıklama (datasheet) |
|---|---|---|---|
| 1 | GVDD_AB | P | Gate-drive voltage supply; AB-side; 0.1µF to GND |
| 2 | VDD | P | Internal regulator güç; 10µF + 0.1µF to GND decoupling |
| 3 | M1 | I | Mode selection 1 (LSB) |
| 4 | M2 | I | Mode selection 2 (MSB) |
| 5 | INPUT_A | I | Input signal for half bridge A |
| 6 | INPUT_B | I | Input signal for half bridge B |
| 7 | OC_ADJ | I/O | Over-Current threshold programming pin |
| 8 | FREQ_ADJ | O | Oscillator frequency programming pin |
| 9 | OSC_IOM | I/O | Oscillator synchronization interface (kullanılmıyorsa dokunma) |
| 10 | OSC_IOP | O | Oscillator synchronization interface (kullanılmıyorsa dokunma) |
| 11 | DVDD | P | Internal regulator, digital section |
| 12 | GND | P | Ground |
| 13 | GND | P | Ground |
| 14 | AVDD | P | Internal regulator, analog section |
| 15 | C_START | O | Startup ramp; charging capacitor to GND gerektirir |
| 16 | INPUT_C | I | Input signal for half bridge C |
| 17 | INPUT_D | I | Input signal for half bridge D |
| 18 | RESET | I | Device reset input; active low |
| 19 | FAULT | O | Shutdown signal; open drain; active low (kullanılmıyorsa dokunma) |
| 20 | VBG | P | Internal voltage reference; 1µF to GND decoupling |
| 21 | CLIP_OTW | O | Clipping warning + over-temperature warning; open drain; active low |
| 22 | GVDD_CD | P | Gate-drive voltage supply; CD-side; 0.1µF to GND |
| 23 | BST_D | P | HS bootstrap supply; 0.033µF to OUT_D |
| 24 | BST_C | P | HS bootstrap supply; 0.033µF to OUT_C |
| 25 | GND | P | Ground |
| 26 | GND | P | Ground |
| 27 | OUT_D | O | Output, half bridge D |
| 28 | OUT_D | O | Output, half bridge D (çift pin) |
| 29 | PVDD_CD | P | PVDD supply for half-bridge C and D |
| 30 | PVDD_CD | P | PVDD supply for half-bridge C and D |
| 31 | PVDD_CD | P | PVDD supply for half-bridge C and D |
| 32 | OUT_C | O | Output, half bridge C |
| 33 | GND | P | Ground |
| 34 | GND | P | Ground |
| 35 | OUT_B | O | Output, half bridge B |
| 36 | PVDD_AB | P | PVDD supply for half-bridge A and B |
| 37 | PVDD_AB | P | PVDD supply for half-bridge A and B |
| 38 | PVDD_AB | P | PVDD supply for half-bridge A and B |
| 39 | OUT_A | O | Output, half bridge A |
| 40 | OUT_A | O | Output, half bridge A (çift pin) |
| 41 | GND | P | Ground |
| 42 | GND | P | Ground |
| 43 | BST_B | P | HS bootstrap supply; 0.033µF to OUT_B |
| 44 | BST_A | P | HS bootstrap supply; 0.033µF to OUT_A |
| PowerPAD | (epad) | P | Ground; grounded heatsink'e bağlanmalı |

---

## §3 Mod Konfigürasyonu: PBTL / BTL / SE (datasheet §6 Table 1 — Mode Selection Pins)

| M2 | M1 | Input mode | Output configuration | Açıklama |
|---|---|---|---|---|
| 0 | 0 | 2N + 1 | 2 × BTL | Stereo BTL output configuration |
| 0 | 1 | 2N/1N + 1 | 1× BTL + 2× SE | 2.1 modu: Channel AB = BTL, channel C+D = SE |
| 1 | 0 | 2N + 1 | 1 × PBTL | Paralleled BTL (mono); INPUT_C ve INPUT_D to GND'a bağlanır |
| 1 | 0 | 2N + 1 | 1 × BTL (mono) | BTL channel AB aktif; CD switching yapmaz; INPUT_C→DVDD, INPUT_D→GND |
| 1 | 1 | 1N + 1 | 4 × SE | Single-ended output configuration |

- Tablo notu (1): "1" = logic high (DVDD level), "0" = logic low (GND).
- Tablo notu (2): "2N" = differential input signal, "1N" = single-ended input; "+1" = RESET
  logic control pin sayısı.
- PBTL hedefi: 600W mono 2Ω @10% THD+N (1% THD+N: 480W) — ti.com ürün sayfası.
- Cihaz "AD-mode"da çalışır (datasheet §3 Description).

---

## §4 Mutlak Maksimum, ESD, Önerilen Koşullar (datasheet §7.1–§7.3)

**Absolute Maximum Ratings:**

| Parameter | Min | Max | Unit |
|---|---|---|---|
| GVDD_X / BST_X (DC + peak AC, terminalde) | −0.3 | 69 | V |
| VDD to GND | −0.3 | 13.2 | V |
| GVDD_X to GND | −0.3 | 13.2 | V |
| PVDD_X to GND | −0.3 | 69 | V |
| DVDD to GND | −0.3 | 4.2 | V |
| AVDD to GND | −0.3 | 8.5 | V |
| VBG to GND | −0.3 | 4.2 | V |
| OUT_X to GND | −0.3 | 69 | V |
| BST_X to GND | −0.3 | 81.5 | V |
| OC_ADJ, M1, M2, OSC_IOP, OSC_IOM, FREQ_ADJ, C_START to GND | −0.3 | 4.2 | V |
| RESET, FAULT, CLIP_OTW to GND | −0.3 | 4.2 | V |
| INPUT_X to GND | −0.3 | 7 | V |
| Continuous sink current (RESET, FAULT, CLIP_OTW) | — | 9 | mA |
| TJ (operating junction) | 0 | 150 | °C |
| Tstg | −40 | 150 | °C |

**ESD Ratings:** HBM (JESD22-C101, ANSI/ESDA/JEDEC JS-001): ±2000 V · CDM: ±500 V.

**Recommended Operating Conditions:**

| Parameter | Min | Typ | Max | Unit |
|---|---|---|---|---|
| PVDD_x, RL = 4Ω | 18 | 51 | 53.5 | V DC |
| PVDD_x, RL ≥ 6Ω (1) | 18 | 53.5 | 56.5 | V DC |
| GVDD_x (logic reg + gate-drive) | 10.8 | 12 | 13.2 | V DC |
| VDD (digital regulator supply) | 10.8 | 12 | 13.2 | V DC |
| RL (BTL) | 3.4 | — | 4 | Ω |
| RL (SE) | 1.7 | — | 3 | Ω |
| RL (PBTL) | 1.7 | — | 2 | Ω |
| LOUT (BTL/SE/PBTL) — output filter inductance | 5 | — | — | μH (min. değer IOC'de; "within recommended value range") |
| FPWM — Nominal (master) | 430 | 450 | 470 | kHz |
| FPWM — AM1 (R(FREQ_ADJ) 20kΩ ±1%) | 475 | 500 | 525 | kHz |
| FPWM — AM2 (R(FREQ_ADJ) 10kΩ ±1%) | 575 | 600 | 625 | kHz |
| R(FREQ_ADJ) master mode, nominal | 29.7 | 30 | 30.3 | kΩ |
| R(FREQ_ADJ) master, AM1 / AM2 | 19.8 / 9.9 | 20 / 10 | 20.2 / 10.1 | kΩ |
| V(FREQ_ADJ) slave mode | — | — | 3.3 | V |
| R_OC (over-current prog.), 5% tol., RL=4Ω / RL≥6Ω@53.5V | 22 / — | — | 30 / — | kΩ |
| R_OC(LATCHED), 5% tol., RL=4Ω / RL≥6Ω | 47 / — | — | 64 / — | kΩ |
| CPVDD (PVDD yakın decoupling) | 1 | — | — | μF |
| TJ | 0 | — | 125 | °C |

(1) Not: RL≥6Ω ise PVDD artırılabilir; over-current eşiği düşürülerek (datasheet tablo notu).

---

## §5 Termal Bilgiler ve Koruma Eşikleri (datasheet §7.4–§7.5)

**Thermal Information (DDV 44-pin HTSSOP):**

| Metric | JEDEC 4-layer board | Fixed heatsink (85°C) | Unit |
|---|---|---|---|
| RθJA (junction-to-ambient) | 50.7 | 2.4 (2) | °C/W |
| RθJC(top) | 0.36 | 0.3 | °C/W |
| RθJB | 24.4 | n/a | °C/W |
| ψJT | 0.19 | 0.5 | °C/W |
| ψJB | 24.2 | n/a | °C/W |

(2) Sabit heatsink verisi: 85°C heatsink sıcaklığı, 0.7 W/mK ısıl iletkenlikte macun, 2 mil
kalınlık; heatsink sıcaklığı "ambient" kabul edilir ve tek ısı yolu heatsink'tir.
- PowerPAD **top side**'dadır (convenient thermal coupling to heat sink — datasheet §6).

**Koruma / izleme eşikleri (datasheet §7.5):**

| Parameter | Typ / aralık | Unit |
|---|---|---|
| Vuvp (VDD, GVDD_x undervoltage limit) | 8.7 (hyst 0.6) | V |
| Vuvp (PVDD_x undervoltage limit) | 14.5 (hyst 1.4) | V |
| OTW (overtemperature warning, CLIP_OTW) | 110 / 120 / 130 (min/typ/max) | °C |
| OTW hysteresis (CLIP_OTW inaktif olmak için gereken düşüş) | 20 | °C |
| OTE (overtemperature error) | 140 / 150 / 160 (min/typ/max) | °C |
| OTE hysteresis (FAULT release için reset gerekir) | 15 | °C |
| OTE−OTW differential | 30 | °C |
| OLPC (overload protection counter, fPWM=450kHz, 1024 PWM cycle) | 2.3 | ms |
| IOC (resistor-programmable, R_OC=22kΩ / 30kΩ, 1Ω load peak) | 17 / 13 | A |
| IOC(LATCHED) (R_OC=47kΩ / 64kΩ) | 17 / 13 | A |
| IDCspkr (DC speaker protection, BTL current imbalance threshold) | 1.5 | A |
| IOCT (overcurrent response time) | 150 | ns |
| IPD (bootstrap charge pulldown, RESET aktifken, SE modda kullanılmaz) | 3 | mA |

**Digital seviyeler:** VIH 1.9V / VIL 0.8V (M1, M2, OSC_IOP, OSC_IOM, RESET) · Ilkg max
100µA · RINT_PU (FAULT, CLIP_OTW internal pullup to DVDD) 20/26/32 kΩ · VOH 3–3.6V · VOL
≤500mV (4mA) · fanout 30 device.

---

## §6 Ses Performansı (datasheet §7.6 BTL · §7.7 SE; PBTL ti.com ürün sayfası)

**BTL (PVDD=51V, GVDD=VDD=12V, fS=450kHz, ROC=22kΩ, TC=75°C, LDEM=10µH, CDEM=1µF, MODE=00):**

| Parameter | Koşul | Typ | Unit |
|---|---|---|---|
| PO | RL=4Ω, 10% THD+N | 315 | W |
| PO | RL=6Ω, 10% THD+N, PVDD=53.5V | 250 | W |
| PO | RL=8Ω, 10% THD+N, PVDD=53.5V | 195 | W |
| PO | RL=4Ω, 1% THD+N | 255 | W |
| PO | RL=6Ω, 1% THD+N, PVDD=53.5V | 200 | W |
| PO | RL=8Ω, 1% THD+N, PVDD=53.5V | 155 | W |
| THD+N | 1W | 0.006% | — |
| Vn (integrated noise, A-wtd, AES17) | grounded input cap | 85 | µV |
| \|VOS\| (output offset) | inputs AC-coupled to GND | 15 (max 60) | mV |
| SNR | (1% THD+N çıkış seviyesine göre) | 112 | dB |
| DNR | — | 113 | dB |
| Pidle (idle losses, 4 kanal) | — | 2.5 | W |

**SE (RL=2Ω, MODE=11, LDEM=15µH, CDEM=1µF, diğerleri aynı):**

| Parameter | Koşul | Typ | Unit |
|---|---|---|---|
| PO | RL=2Ω, 10% THD+N | 148 | W |
| PO | RL=3Ω, 10% THD+N, PVDD=53.5V | 120 | W |
| PO | RL=4Ω, 10% THD+N, PVDD=53.5V | 95 | W |
| PO | RL=2Ω, 1% THD+N | 120 | W |
| PO | RL=3Ω, 1% THD+N, PVDD=53.5V | 98 | W |
| PO | RL=4Ω, 1% THD+N, PVDD=53.5V | 77 | W |
| THD+N | 1W | 0.04% | — |
| Vn | A-wtd | 160 | µV |
| SNR / DNR | A-wtd | 101 / 101 | dB |
| Pidle | 4 kanal | 2 | W |

**PBTL (ti.com ürün sayfası, datasheet §7.8 bu oturumda tam okunmadı → ⚠️ partial):**

| Koşul | Değer |
|---|---|
| 600W mono into 2Ω @ 10% THD+N | ✅ ti.com features |
| 480W mono into 2Ω @ 1% THD+N | ✅ ti.com features |
| PBTL audio karakteristik tablosunun tamamı (SNR/DNR/Pidle PBTL) | ⚠️ VERIFICATION REQUIRED (datasheet §7.8) |

**Güç/verimlilik iddiaları (ti.com):** 90% efficient Class-D operation (4Ω) · >65 dB PSRR
(BTL, 1kHz, no input) · <85µV (A-wtd) output noise · signal bandwidth up to 100kHz ·
<0.01% THD+N to clipping · idle losses <2.5W.

**Analog girdi evresi (datasheet §7.5):** RIN 20kΩ · VIN max swing 7V p-p · IIN max 1mA ·
G (inverting voltage gain) 21.5 dB.

---

## §7 Oscillator, PWM ve Mod EMI Kontrolü (datasheet §7.3, §7.5)

| Parameter | Nominal | Min–Max | Unit |
|---|---|---|---|
| fOSC(IO+) nominal master | 2.7 | 2.58–2.82 | MHz (=6×FPWM) |
| fOSC(IO+) AM1 master | 3.0 | 2.85–3.15 | MHz |
| fOSC(IO+) AM2 master | 3.6 | 3.45–3.75 | MHz |
| VIH / VIL (osc sync) | — | 1.86 / 1.45 | V |

- FPWM seçimi AM interference avoidance içindir (AM1 500kHz / AM2 600kHz, ±1% resistor tol).
- Slave mode: FREQ_ADJ pinine 3.3V uygulanır; OSC_IO pinleri multi-chip sync için (tek
  kullanılmıyorsa dokunma).
- **EMI notu:** "EMI Compliant When Used With Recommended System Design" (ti.com) — önerilen
  LC output filter (LOUT 5µH min., testte LDEM 10/15µH + CDEM 1µF) ve layout kılavuzu
  (datasheet §12 Layout Guidelines/Examples) şartıyla; EMI ölçümü bu dosyada YAPILMADI.
- İlgili uygulama notları (ti.com literatür listesi): SLAA701 (LC Filter Design, Rev. B,
  Jun 29 2026) · SBAA735 (Speaker/TV/Soundbar Selection Guide, Mar 11 2026) · SLVA824
  (Class-D External Load Diagnostics, Rev. A, Jul 17 2025) · SLAA898 (Constant Voltage
  Audio Systems, Sep 6 2022) · SLAA788 (TPA324x/325x Post-Filter Feedback, Rev. A, Mar 7
  2018) · SLYB233 (High-Power Audio Amplifiers TPA322x/4x/5x, Dec 1 2017) · SLAA787
  (Multi-Device Configuration for TPA32xx, Sep 27 2017) · SLAA719 (TPA32xx Analog Input
  Grounding for SE Inputs, Aug 26 2016) · SLYB229 (High-Power Audio Amplifiers, Jul 14
  2016) · SSZTBG5 (Class-D vs hi-res Class-AB technical article, Apr 1 2016).
- Destek araçları: SLAC733 (TPA3255 Setup Guide & Configuration Tool) · LCFILTER-CALC-TOOL
  (Class-D LC Filter Designer) · PMP10215 (Universal Input→36V 200W PSU reference design
  for Class-D).

---

## §7B Koruma Şemaları ve Hata Raporlama (datasheet §9.4.1 — Feature Description)

**a) Signal clipping / narrow-pulse injection (§9.4.1.2):** Clipping veya CB3C (cycle-by-cycle
control) durumunda PWM daralır ve çıkış PWM sinyalleri durabilir; bunu önlemek için gate
drive'a dar pulse enjekte edilir. Enjeksiyon **her 4. PWM frame'de** yapılır → etkin
anahtarlanma frekansı bu durumda normalin **1/4'üne** düşer. Clipping `CLIP_OTW` ile
işaretlenir ve sinyal seviyesi düşünce self-clearing olur. `CLIP_OTW` darbeleri çıktı
clipping başlangıcında, tip. **~%0.01 THD** seviyesinde, **~500 ns** pulse width ile başlar.

**b) DC Speaker Protection (§9.4.1.4 öncesi):** Hoparlörün bir terminali amplifikatöre
diğeri şasi GND'ye kısa devre olursa hoparlör üzerinde **PVDD/2 DC** oluşur ve yıkıcı akım
mümkündür. Şema, BTL çıkışının çıkış/ giriş akım dengesizliğini (imbalance) algılar; eşik
aşıldığında overload counter maksimuma ulaşana kadar artar ve etkilenen kanal kapanır.
**SE modunda devre dışıdır.**

**c) Pin-to-Pin Short Circuit Protection (PPSC — §9.4.1.4):** OUT_X → GND_X veya OUT_X →
PVDD_X kısa devresini **filtre ÖNCESİ** pinde algılar (OC koruması filtreden sonra algılar).
Algılama **yalnız başlatmada (VDD verildiğinde)** yapılır; startup sonrası kısa devre PPSC'yi
tetiklemez. İki adımlı: önce OUT_X→GND_X, sonra OUT_X→PVDD_X testi. Süre tip. **< 15 ms/µF**
(çıktı LC kapasitesiyle orantılı). Süre boyunca `FAULT` low kalır, RESET değişiklikleri
dikkate alınmaz; kısa devre yoksa `FAULT` release edilir. RESET tekrarı yeni PPSC başlatmaz. BTL ve
PBTL'de etkin, SE'de yapılmaz.

**d) Overtemperature (OTW / OTE — §9.4.1.5):** İki kademeli. TJ > **120 °C (typ)** →
`CLIP_OTW` active-low uyarı; TJ > **155 °C (typ)** → thermal shutdown, tüm half-bridge'ler
Hi-Z, `FAULT` low. **OTE latchedtir**; temizlemek için `RESET` togglenmelidir.
(§7.5 tablosundaki sayısal eşikler: OTW 110/120/130 min/typ/max · OTE 140/150/160.)

**e) UVP ve POR (§9.4.1.6):** GVDD_X ve VDD bağımsız izlenir; herhangi birinde UVP eşiğinin
altına düşüş → tüm half-bridge'ler anında Hi-Z, `FAULT` low. Gerilim eşiğin üstüne çıkınca
cihaz **otomatik** devreye girer. POR, GVDD_X/VDD Electrical Characteristics değerlerine
ulaşılınca tüm devrelerin çalışır olduğunu garanti eder.

**f) Fault Handling (§9.4.1.7):** **Global fault** → tüm PWM kapanır, `FAULT` low, **latching**
(temizlik = RESET toggle). **Channel fault** → yalnız etkilenen kanalın PWM'i kapanır.
`RESET` lowken `FAULT` high zorlanır (fault olsun olmasın). Datasheet uyarısı: `RESET`'in
serbest bırakılması mikrokontrolcü ile izlenmeli; yalnız `CLIP_OTW` high (temiz) iken
`RESET` high yapılmalı.

**Table 5 — Error Reporting (datasheet §9.4.1.7):**

| Fault/Event | Açıklama | Global/Channel | Raporlama | Latched? | Temizlik | Çıkış FET'leri |
|---|---|---|---|---|---|---|
| PVDD_X / VDD / AVDD UVP | Voltage fault | Global | FAULT pin | Self clearing | İlgili supply yükselt | Hi-Z |
| POR (DVDD UVP) | Power-on reset | Global | FAULT pin | Self clearing | DVDD yükselmesini bekle | Hi-Z |
| BST_X UVP | Bootstrap voltage fault | Channel (half-bridge) | Yok | Self clearing | BST recharge (low-side ON, VDD 12V) | Cap to high-side off |
| OTW | Thermal warning | Global | OTW (`CLIP_OTW`) pin | Self clearing | Eşik altına soğut | Normal çalışma |
| OTE (OLP, CB3C > 1.7 ms) | Thermal shutdown | Global | FAULT pin | **Latched** | `RESET` toggle | Hi-Z |
| OC shutdown (47kΩ < ROC_ADJ < 68kΩ) | Overcurrent shutdown | Channel | FAULT pin | **Latched** | `RESET` toggle | Hi-Z |
| OC limiting (22kΩ < ROC_ADJ < 30kΩ) | Overcurrent limiting | Channel | Yok | Self clearing | Sinyal seviyesini düşür / kısa kaldır | Flip state, cycle-by-cycle @ fs/3 |
| Stuck fault (Slave modda OSC_IO aktivitesi yok) | Oscillator stuck | Global | Yok | Self clearing | OSC_IO aktivitesini sürdür | Hi-Z |

**g) Stuck Fault / Reset Timing (§9.4.1.8):** OSC_IO frekansı minimumun altına düşerse cihaz
Hi-Z'ye geçer. Reset behavior: RESET low, BTL/PBTL weak pulldown'lar half-bridge input'larında
yükselen kenar geçişine izin verir; **reset'ten önceki ilk fault sinyali `FAULT`'ta gösterilir
ve `FAULT`'ın falling edge'inden en az 4 ms sonra RESET low'dan önce gelen fault'un temizliği
mümkün değildir** (datasheet metni regular'tır; hassas zamanlama ⚠️ kısmi — uygulamada EVM
kullanım kılavuzuna bakılmalı).

---

## §7C Tipik Uygulamalar (datasheet §10.2 — Design Requirements tabloları)

Cihaz 4 konfigürasyonda yapılandırılabilir: **stereo BTL · 4-kanal SE · mono PBTL · 2.1
(1×BTL + 2×SE)**. Üç tipik uygulamanın design requirement tabloları:

| Design parameter | BTL (Table 6) | SE (Table 9) | PBTL (Table 11) |
|---|---|---|---|
| Low power (pull-up) supply | 3.3 V | 3.3 V | 3.3 V |
| Mid power supply 12 V | 12 V | 12 V | 12 V |
| High power supply | 18–51 V | 18–51 V | 18–51 V |
| Mode selection | M2=L, M1=L | M2=H, M1=H | M2=H, M1=L |
| Analog inputs (INPUT_A/B/C/D) | ±3.9 V peak max (hepsi) | ±3.9 V peak max (hepsi) | A/B = ±3.9 V peak max · C/D = Grounded |
| Output filter (L + C) | 10 µH + 1 µF | 15 µH + 680 nF | 10 µH + 1 µF |
| Speaker impedance | 3–8 Ω | 2–8 Ω | 2–4 Ω |

**Decoupling kapasitör önerileri (§10.2.1.2.1–2):**
- Kaliteli, **X7R** seramik kullanılmalı.
- PVDD close-decoupling (1 µF/full-bridge) pinlere **en yakın** yerleştirilmeli; ground yolu
  geniş ve iyi referanslı olmalı → PVDD ringling minimum.
- 1 µF kapasitör PWM voltaj aşırı voltajı, yüksek güçte ısı ve ripple akımına dayanmalı;
  **51 V supply ile min. 100 V gerilim rating** şart.
- Bulk PVDD kapasitörleri: iyi tasarlanmış supply ile çoğu uygulamayı **1000 µF / 80 V**
  destekler; **low ESR** tip (yüksek hızlı switching ile çalıştığı için).

**PCB malzeme (§10.2.1.2.3):** **FR-4 glass epoxy, 2 oz (70 µm) bakır** önerilir → daha yüksek güç,
gelişmiş termal performans, daha iyi EMI marjı (düşük trace inductance).

**Oscillator / master-slave (§10.2.1.2.4):**
- Frekans `FREQ_ADJ` → GND direnci ile trim edilir; direnç değişikliği **RESET lowken**
  yapılmalı.
- AM bandı radio alıcılarıyla çakışmayı azaltmak için nominal ↔ alternatif frekans seçimi
  (AM1/AM2) yapılır.
- **Slave mode:** `FREQ_ADJ` → DVDD'e çekilerek osilatör kapatılır; OSC_I/O pinleri harici
  diferansiyel clock'ın input'u olur. Master/Slave sistemde kanallar arası **inter-channel
  delay** otomatik kurulur (boş kanallar aynı anda anahtarlanmaz → power supply üzerinden
  gürültü bağlantısı azalır).
- Slave polaritesi: **Slave 1** = normal (master+ → slave+, master− → slave−) · **Slave 2** =
  ters (master+ → slave−). Table 7 inter-channel delay değerleri (PWM frame'e göre derece):

| | 2×BTL (M2=0,M1=0) | 1×BTL+2×SE (M2=0,M1=1) | 1×PBTL (M2=1,M1=0) | 4×SE (M2=1,M1=1) |
|---|---|---|---|---|
| Master: OUT_A/B/C/D | 0° / 180° / 60° / 240° | 0° / 180° / 60° / 120° | 0° / 180° / 0° / 180° | 0° / 60° / 0° / 60° |
| Slave 1: OUT_A/B/C/D | 60° / 240° / 120° / 300° | 60° / 240° / 120° / 180° | 60° / 240° / 60° / 240° | 60° / 120° / 60° / 120° |
| Slave 2: OUT_A/B/C/D | 30° / 210° / 90° / 270° | 30° / 210° / 90° / 150° | 30° / 210° / 30° / 210° | 30° / 90° / 30° / 90° |

**Diğer uygulama notları (§10.2.1.2):**
- Cihaz ses sinyalini **girişten çıkışa invert eder**.
- `CLIP` sinyali ses seviyesini düşürmek veya **akıllı power supply'ı** kontrol etmek için
  kullanılabilir (düşük rail'de çalışan, ihtiyaca göre yükselen).
- `DVDD` ve `AVDD` pinleri **harici devreye besleme kaynağı olarak önerilmez** (iç devre
  akımı için yeterlidir; ek yük → voltaj sag + gürültü enjeksiyonu).

---

## §7D Güç Kaynağı, Açılış/Kapanış ve Termal Tasarım (datasheet §11)

**İki external supply gerekli (§11.1):**
1. **PVDD** — yüksek gerilimli supply; output stage'i ve load akımını besler (18–53.5 V).
2. **GVDD_X ve VDD** — orta gerilimli supply; gate-drive + internal digital/analog bölümler
   (10.8/12/13.2 V).

Her iki supply da Electrical Characteristics'teki akımdan **fazlasını** sağlayabilmeli.

**VDD Supply (§11.1.1):** VDD, internal LDO'ları (DVDD = 3.3 V tip · AVDD = 7.75 V tip)
besler; LDO çıktıları AVDD/DVDD pinlerinde dış bypass kapasitörüne çıkış yapar. Bu LDO'lar
**yalnız internal devrenin akım ihtiyacını** desteklemek için tasarlanmıştır — harici devre
beslenmemeli. Bağlantı/routing/decoupling, **TPA3255D2EVM User's Guide (SLOU441)** ile
Application/Layout bölümlerine sadık kalınmalı; sapma → performans kaybı, anormallik, hatta
cihaz hasarı.

**GVDD_X Supply (§11.1.2):** Output H-bridge gate-drive'ını besler; aynı SLOU441 kuralları
geçerli.

**PVDD Supply (§11.1.3):** Output stage load akımını sağlar. Yüksek gerilimli switching
nedeniyle **decoupling özellikle kritiktir**; uygunsuz decoupling → voltaj spike'ları →
cihaz hasarı / kötü ses / shutdown fault'ları.

**Powering Up (§11.2):** Power-up **sırası zorunlu değildir**; ancak PVDD açıldıktan sonra
`RESET` **en az 250 ms low tutulmalı**. H-bridge çıkışları, GVDD_X ve VDD UVP eşiğinin
üzerine çıkana kadar **yüksek empedans (Hi-Z)** kalır. RESET serbest bırakılınca: `FAULT`
low'laşır, AVDD regulator'ı enable olur; AVDD UVP eşiğine ulaşana kadar `FAULT` low kalır.
Giriş AC-koplama kapasitörlerinin DC voltajı stabilize olmak için **precharge** süresi, sonra
**controlled ramp-up** başlar (Figure 32 — tPrecharge/tStartup ramp sayısal değerleri ⚠️
karakter grafiğinden okundu, tipik figure değerleri; kesin değer EVM kılavuzunda doğrulanmalı).

**Powering Down (§11.3):** Power-down sırası da **zorunlu değildir**; cihaz GVDD_X/VDD UVP
eşiğinin üzerinde kaldığı sürece tamamen çalışır. Yine de iyi uygulama: kapanışta `RESET`
low tutulur → controlled ramp-down ile **pop/click** (işitsel artefakt) önlenir.

**Thermal Design (§11.4):**
- Termal performans **heatsink tasarımına ve sistem kabına** (kapalı kutu / fanlı) bağlıdır;
  maksimum sürekli çıkış gücü bunu etkiler.
- Sürekli yüksek güçte termal sınırlamayı azaltmak için: **soğutma kapasitesini artırın veya
  PBTL modunda çalıştırın**.
- Cihazın **OTW eşiğinin altında** çalıştırılması önerilir.
- Düşük ses frekanslarında, çıktı MOSFET'lerinin ısınması arttığı için sürekli güçte termal
  sınırlama olabilir. Figure 33: **TC = 75 °C heatsink, BTL 4 Ω, max %10 THD** sürekli çıkış
  gücü frekansla grafiği (20–150 Hz).
- Non-sürekli (burst) performans: Figure 36/37 — **TC = 75 °C, 4 Ω BTL, güç oranı 1:16 ve
  1:8**, burst ratio (High:Low) 2:1 / 2:2 / 2:4 / 2:8 / 1:1 / 1:4 / 1:8 eksenli, 20–150 Hz
  aralığında maks burst çıkış gücü.
- 8 Ω load'ta cihaz termal olarak sınırlı **değildir**; daha düşük load empedansında
  switching frekansına ve ortalama/maks güç oranına bağlı sınırlama olabilir.

---

## §7E Layout Kuralları (datasheet §12.1–§12.2)

**Layout Guidelines (§12.1):**
1. Kesintisiz (unbroken) **ground plane** kullan → güç ve ses sinyalleri için düşük empedans/
   inductance dönüş yolu.
2. Ground pin'lerinden cihazı çevreleyen PCB alanına **sürekli ground plane** koru (ground
   pin'leri paketin ana ısı iletkenleridir).
3. PCB layout ↔ ses performansı ↔ EMI birbirine sıkı bağlıdır.
4. Ses girişi routing'i kısa tutulmalı, eşlik eden ses kaynağı ground'ıyla birlikte gitmeli.
5. PVDD üzerindeki küçük bypass kapasitörleri **PVDD pinlerine en yakın**.
6. Cihaz altındaki local ground alanı **sağlam** tutulmalı → ground bounce minimize.
7. Pasif bileşenlerin **dar ucu TPA3255'e** bakacak şekilde yerleştir (iki pad arası yeterince
   geniş değilse bakır padlerin arasına akmasın).
8. TPA3255 yakınına **diğer ısı üreten bileşen/ yapı yok**.
9. Cihazdan çevreleyen ground alanlarına **ısı akışını kesen trace/via dizileri özellikle
   output tarafında** kaçınılmalı.

**Layout Examples — ortak notlar (§12.2.1–3, Figure 38/39/40 — BTL/SE/PBTL composite):**
- **T1:** PVDD bulk decoupling kapasitörleri PVDD/GND_X pinlerine **en yakın** (heatsink
  mesafeyi belirler); geniş trace'ler top layer'da, pinlere **doğrudan, via'sız**; akım
  yolunu blocklayan via/trace yok.
- **T2:** PVDD close decoupling, **düşük empedanslı X7R** seramik, heatsink **altında** ve
  pinlere yakın.
- **T3:** Heatsink, PCB ground'una **iyi bağlantı** sağlamalı.
- Kullanılmayan tüm PCB alanı **GND bakır dökümü** olmalı. (PBTL örneğinde kullanılmayan
  giriş pinleri ground'a bağlanır — Figure 40 "Grounded for PBTL" notu.)

---

## §7F Paket, Sipariş ve EMC/ESD Kullanım Notu (datasheet §13–§14)

**Orderable table (PACKAGE OPTION ADDENDUM, www.ti.com 10-Nov-2025 tarihli):**

| Orderable PN | Status | Package | Pins | Package qty | Carrier | RoHS | Op temp | Lead finish | MSL / Peak reflow | Part marking |
|---|---|---|---|---|---|---|---|---|---|---|
| TPA3255DDV | Active (Production) | HTSSOP (DDV) | 44 | 35 | TUBE | Yes | 0…70 °C | NIPDAU | Level-3-260C-168 HR | 3255 |
| TPA3255DDV.B | Active (Production) | HTSSOP (DDV) | 44 | 35 | TUBE | Yes | 0…70 °C | NIPDAU | Level-3-260C-168 HR | 3255 |
| TPA3255DDVR | Active (Production) | HTSSOP (DDV) | 44 | 2000 | LARGE T&R | Yes | 0…70 °C | NIPDAU | Level-3-260C-168 HR | 3255 |
| TPA3255DDVR.B | Active (Production) | HTSSOP (DDV) | 44 | 2000 | LARGE T&R | Yes | 0…70 °C | NIPDAU | Level-3-260C-168 HR | 3255 |

**Doküman desteği (§13.1):** TPA3255D2EVM User's Guide = **SLOU441**.
**ESD dikkat (§13.5):** Cihazın dahili ESD koruması **sınırlıdır**; depolama/taşıma sırasında
pin'ler birbirine kısa yapılmalı veya iletken köpüğe konulmalı (MOS gate'lerini korumak için).
**Community (§13.3):** TI E2E Online Community (e2e.ti.com) · TI Design Support.

---

## §8 Aile Karşılaştırması (datasheet §5 Device Comparison)

| Device | Açıklama |
|---|---|
| TPA3244 | 40-W Stereo, 100-W peak PurePath Ultra-HD Pad Down Class-D Amplifier |
| TPA3245 | 100-W Stereo, 200-W Mono PurePath Ultra-HD Analog-Input Class-D Amplifier |
| TPA3250 | 70-W Stereo, 130-W peak PurePath Ultra-HD Pad Down Class-D Amplifier |
| TPA3251 | 175-W Stereo, 350-W Mono PurePath Ultra-HD Analog-Input Class-D Amplifier |
| **TPA3255** | **315-W Stereo, 600-W Mono PurePath Ultra-HD Analog-Input (bu dosya)** |

---

## §9 CoreMusic Çelişki Kaydı ve Rolü (bağlayıcı — karar YOK)

| Konu | Durum |
|---|---|
| **Topoloji durumu** | **Class D = YASAK** (kök `CLAUDE.md` §Sabitler; ADR-089 §3.1-A1; skill §6 H001). CoreMusic amfi topolojisi **Class AB** (K16: Darlington MJL21194/93, 50W/kanal @8Ω). |
| **Bu dosyanın statüsü** | Salt **referans/alternatif teknoloji envanteri**. Tasarım kararı VERİLMEDİ · BOM'a ALINMADI · şeçim YAPILMADI. |
| **Çelişki işareti** | ⚠️ Manifestteki "24-52V PVDD" ifadesi datasheet ile **örtüşmüyor**: datasheet Rec. Op. PVDD = **18–53.5V (RL=4Ω)**, 18–56.5V (RL≥6Ω). Doğru değer datasheet'tekidir; manifest değeri ⚠️ işaretlidir. |
| **ADR taşıma notu** | Sınıf D'ye herhangi bir reopening talebi → **ADR** (`.ai/brain.md` veya `.ai/architecture/adr/`) + owner onayı (Guardrail #14) ile yapılır; bu dosya tek başına yeterli gerekçe DEĞİLDİR. |
| **Ölçüm kapısı** | Class-D ile ilgili herhangi bir gelecekteki karar için ölçüm kanıtı (THD+N/SNR/EMI/termal) zorunludur → şu an **⚠️ VERIFICATION REQUIRED** (prototip yok). |
| **Vault tutarlılığı** | Kök `CLAUDE.md` §Web Verification tablosunda TPA3255 **listelenmemiştir** (AK4458, PCM3168A, XU316, LM5122, MJL21194/93 var) — bu dosya envanter dışı referans olarak işaretlenmiştir. |
| **Karşılaştırma (bilgi amaçlı)** | K16 hedefi: 50W/kanal @8Ω, THD <0.005% @1W, SNR >105dB (Class AB). TPA3255 datasheet: 0.006% THD+N @1W (BTL), SNR 112dB — rakamlar sınıf karşılaştırmasıdır, **seçim ölçütü değildir** (topoloji yasağı önceliklidir). |

**Ölçüm durumu:** Prototip yok → tüm hedefler **PLANNED**; APPROVE kararı YOK.

---

## §10 Doğrulama Tablosu (id · URL · tarih · durum)

| §id | Kaynak URL | Tarih | Durum |
|---|---|---|---|
| §0, §6–§8 | https://www.ti.com/product/TPA3255 | 2026-10-12 erişim | ✅ DOĞRULANDI (ACTIVE, özet, literatür listesi) |
| §1–§9 | https://www.ti.com/lit/ds/symlink/tpa3255.pdf (SLASEA8A, Rev. A) | 2026-10-12 erişim | ✅ DOĞRULANDI (pin/mode/karakteristik tabloları okundu) |
| §6 PBTL tablosu | datasheet §7.8 (Rev. A) | 2026-10-12 | ⚠️ VERIFICATION REQUIRED (PBTL audio tablosu tam okunmadı; güç rakamları ti.com features'tan) |
| §9 kural | kök `CLAUDE.md` (disk) §Sabitler · `.claude/skills/hardware-electronics/SKILL.md` §6 H001 · ADR-089 | 2026-10-12 okundu | ✅ DOĞRULANDI (disk kanıtı; çelişki işareti ACTIVE) |
| §7 EMI | "EMI Compliant When Used With Recommended System Design" (ti.com features) | 2026-10-12 | ✅ DOĞRULANDI (id dia; ölçüm YOK → uygulama şartı doğrulanmadı) |

---

## §11 Açık Sorular

1. **Sahip kararı (kritik):** Class-D yasağı (ADR-089) bu çip için kalıcı mı, yoksa ölçüm
   kanıtlı bir reopening senaryosu tanımlanacak mı? → owner + ADR gerekir.
2. **PVDD aralığı:** Manifestteki 24-52V ifadesi nereden geldi (sistemin gerçek rail'i ±35V
   K17 boost mu, ayrı 48V mu)? K17 ±35V bu çipin 18V min.PVDD ile uyumu ölçülmeli.
3. **LC filter:** Hangi LDEM/CDEM ile EMI hedefi tutturulacak (SLAA701 design tool girdileri)?
4. **Koruma entegrasyonu:** FAULT/CLIP_OTW open-drain çıkışları CoreMusic koruma mimarisine
   (KSD301 termal kesme, DC offset rölesi — skill §6) nasıl entegre edilir? (Class AB'de
   bu pinler YOK.)
5. **Ölçüm protokolü:** AES17 + AUX-0025 measurement filter koşulları laboratuvar
   protokolüne (K19-testing / audio-hardware-engineer §2 Ölçüm) nasıl yazılır?

---

## §12 Authority

*Authority: kök `CLAUDE.md` §Sabitler (Class AB-only, Class D yasak — **öncelikli otorite**)
· ADR-089 (`.ai/brain.md`) · skill: `hardware-electronics` (v3.0, §6 H001) · TI datasheet
SLASEA8A (Rev. A). Otorite sırası: `.ai/CLAUDE.md` > `.ai/AGENTS.md` > `.ai/WORKFLOW.md` >
`.ai/brain.md`. Bu dosya **referans/alternatif teknoloji kaydı (draft)**tır; tasarım kararı
üretemez; herhangi bir Class-D kararı ADR + owner onayı gerektirir. node_id:
`K001-donanım/donanaım-type/audio/cipler/tpa3255-amplifier` · 2026-10-12.*
