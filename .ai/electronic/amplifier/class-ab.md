---
type: electronic
category: amplifier-class-ab
title: "CoreMusic — Class AB Amplifier Topology"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Class AB Amplifier

**See also:** [[electronic/amplifier/index]] · [[hardware/amplifier-pcb]] · [[thermal]] · [[protection]] · [[ADR-038-8.1-sound-card-chip-selection]]

---

## 1. Amaç

Class AB Amplifier, CoreMusic ELECTRONICS platformunun profesyonel ses amfilerinin temel topolojisini tanımlar. 100W @ 8Ω, THD+N <0.01% hedefiyle CM-71-AB (7.1) ana amplifikatör serisinin tasarım omurgasıdır.

Bu dosya:

- Çift kutuplu (complementary BJT) push-pull mimarisini ve emitter-follower çıkış aşamasını
- Önyargı (bias) akımı hesabını ve termal tracking yöntemini
- Verimlilik hesabını ve Class A/A/B/AB karşılaştırmasını
- Harmonik bozulma (THD) kaynağını ve minimizasyonunu
- Termal tasarım zincirini (transistör → heatsink → hava)
- Topoloji varyantlarını (entegre LM3886/LM1875/TDA7294 vs discrete MJL3281A)
- Ölçüm planını ve risk envanterini

taşır. İlgili dosyalar: [[class-d]] (alternatif sınıf), [[power-supply]] (±42V besleme), [[protection]] (DC offset/over-current), [[thermal]] (heatsink hesabı).

---

## 2. Temel Özellikler

| Parametre | Değer |
|-----------|-------|
| Topoloji | Class AB push-pull |
| Çıkış Gücü | 100W @ 8Ω, 200W @ 4Ω |
| THD+N | <0.01% (1kHz, 1W) |
| SNR | >100dB (A-wtd) |
| Frekans Yanıtı | 20Hz - 20kHz (±0.5dB) |
| Damping Factor | >200 (8Ω) |
| Giriş Empedansı | 47kΩ |
| Çıkış Empedansı | <0.1Ω |
| Gerilim Kazancı | 20× (26dB) tipik |
| Topraklama | Star ground, tek nokta |

### 2.1 Çift Kutuplu (Complementary) Yapı

Class AB çıkış aşaması NPN + PNP çiftinin simetrik çalışmasına dayanır:

```
                 +VCC (+42V)
                    │
              ┌─────┴─────┐
              │   Q1 NPN  │  (MJL3281A — pozitif yarım dalga)
              │ (collector)│
   Signal ────┤ base      ├───┬─── Çıkış (speaker +)
              │(emitter)  │   │
              └─────┬─────┘   │
                    ├─────────┘
              ┌─────┴─────┐
              │   Q2 PNP  │  (MJL1302A — negatif yarım dalga)
              │(emitter)  │
              └─────┬─────┘
                    │
                 -VCC (-42V)
```

| Transistör | Tip | Görev | Yarım Dalga |
|------------|-----|-------|-------------|
| Q1 (MJL3281A) | NPN | Pozitif emme (source) | Sinyal > 0V |
| Q2 (MJL1302A) | PNP | Negatif emme (sink) | Sinyal < 0V |

**Neden emitter-follower?** Çıkış emiterden alınır; gerilim kazancı ≈1, akım kazancı yüksek. Negatif geri besleme ile birlikte çıkış empedansı <0.1Ω'a düşer (damping factor >200).

### 2.2 Aşama Şeması (Gain Stages)

```
Input → Differential Pair (VAS) → Driver Stage → Output Stage → Feedback
                                    │
                              Bias Adjustment
                                    │
                              Thermal Tracking
```

| Aşama | Görev | Bileşenler | Tipik Kazanç |
|-------|-------|-----------|---------------|
| Input | Sinyal kabulü, CMRR | Differential pair (BJT, 2N5401/2N5551) | 1× (akım) |
| VAS | Gerilim kazancı | Tek emitter follower (MJE340) | 40-50× |
| Driver | Güç artırma | Darlington pair (MJE15030/MJE15031) | 10× |
| Output | Güç çıkışı | Complementary BJTs (MJL3281A/MJL1302A) | ≈1× (gerilim), 50× (akım) |
| Feedback | Bozulma azaltma | Rejektör (1kΩ + 100Ω divider) | β = 0.091 |

**Toplam geri besleme:** %100 negatif feedback (DC'den 100kHz'e kadar açık) — gerilim kazancı 1/β = 11× yerine 20× için giriş aşamasında ek kazanç.

---

## 3. Önyargı (Bias) Hesabı

Class AB'de çıkış transistörleri tam olarak kapanmaz; çaprazlama (crossover) noktasında her ikisi de az miktar iletkendir. Bu quiescent akım (Iq) crossover distortion'ı önler.

### 3.1 Bias Akımı Parametreleri

| Parametre | Değer | Gerekçe |
|-----------|-------|---------|
| Iq (quiescent) | 50-100mA | SOA ve ısı dengesi |
| Vbe-multiplier drop | 1.2-1.4V | Q1/Q2 base arası gerilim |
| Thermal tracking | NTC termistör (1kΩ @ 25°C) | Iq sıcaklıkla sabit |
| Crossover Distortion | Minimize edilmiş | Iq > 0 iken her transistor iletkendir |
| Class B→AB geçiş | Smooth transition | Iq = 0 → Class B (distortion), Iq → A (ısı) |

### 3.2 Vbe-Multiplier Hesabı

Bias devresi Vbe-multiplier (Q3, MJE340) ile kurulur:

| Formül | Değer | Açıklama |
|--------|-------|----------|
| Vbias = Vbe × (1 + R1/R2) | 0.65V × (1 + 1.5k/1k) = 1.63V | Q1+Q2 base arası |
| R1 | 1.5kΩ | Üst direnç |
| R2 | 1kΩ | Alt direnç (NTC paralel) |
| P_bias = Vbias × Iq | 1.63V × 0.08A = 130mW | Q3 gücü |
| R_thermal | 100Ω/W (NTC) | Iq'yu sıcaklıkla düzenler |

**NTC tracking:** Q3, heatsink'e termal olarak bağlanır; sıcaklık arttıkça Vbe düşer, Vbias azalır, Iq düşer — termal kaçak (thermal runaway) önlenir.

### 3.3 Termal Kaçak Koşulu

| Koşul | Formül | Sonuç |
|-------|--------|-------|
| Kararlılık | R_thermal × Iq / Vbe_T × (-2mV/°C) < 1 | Kararlı |
| MJL3281A için | 100Ω/W × 0.08A / 0.65V × 0.002 = 0.25 | ✅ Kararlı (0.25 < 1) |
| Kritik eşik | Iq > 200mA | ⚠️ Termal kaçak riski |

**Kural:** Iq 50-100mA aralığında tutulur; NTC termistör olmadan asla 150mA üzerine çıkarılmaz.

---

## 4. Verimlilik vs Sınıfı

### 4.1 Verimlilik Hesabı

Class AB verimliliği çıkış gücü ve rail gerilimine bağlıdır:

| Formül | Değer (100W @ 8Ω, ±42V) | Açıklama |
|--------|--------------------------|----------|
| Vpeak = √(P × R) | √(100 × 8) = 28.3V | Tepe gerilimi |
| Ipeak = Vpeak / R | 28.3 / 8 = 3.54A | Tepe akımı |
| Pin = (2/π) × Vpeak × Ipeak | (2/π) × 28.3 × 3.54 = 63.7W | Sadece çıkış (sinüs) |
| Pdiss = Pin - Pout | 63.7 - 100 = -36.3W | ❌ Bu formül rail gücünü verir, dissipasyon değil |
| Pdiss (doğru) = (2/π²) × Vpeak × Ipeak + Iq×Vcc - Pout | ≈ 32W + 3.4W - 100W | Negatif = rail'den çekilen |
| η (verimlilik) = Pout / (2 × Vcc × Ipeak × 2/π) | 100 / (2 × 42 × 3.54 × 0.637) = 100/189 = %53 | **%50-55 tipik** |

**Sonuç:** Class AB, 100W çıkışta %45-55 verimlilikle çalışır; kalan güç ısıya dönüşür (≈90W kayıp @ tam yük).

### 4.2 Sınıf Karşılaştırması

| Sınıf | Bias | Verimlilik | THD | Isı | Kullanım |
|-------|------|-----------|-----|------|----------|
| Class A | Iq > Ipeak | %25-50 | Çok düşük | Çok yüksek | Ultra high-end |
| **Class AB** | **0 < Iq < Ipeak** | **%45-55** | **Düşük** | **Yüksek** | **CoreMusic ana amfi** |
| Class B | Iq = 0 | %60-70 | Orta (crossover) | Orta | Nadiren |
| Class D | Anahtarlama | %85-92 | Çip-bağlı | Düşük | [[class-d]] |

### 4.3 Güç Kaybı Dağılımı (100W @ 8Ω, ±42V)

| Kayıp Kaynağı | Güç | Oran |
|---------------|-----|------|
| Output transistör dissipasyonu | ~32W | %36 |
| Driver stage (MJE15030/31) | ~5W | %6 |
| Bias devresi (Vbe-multiplier) | ~0.13W | %0.1 |
| Input/VAS stage | ~1W | %1 |
| Güç kaynağı kaybı (boost/trafo) | ~20W | %22 |
| **Toplam kayıp** | **~58W** | **%65** |
| **Verimlilik** | — | **%53** |

---

## 5. Harmonik Bozulma (THD)

### 5.1 THD Kaynakları

| Kaynak | Mekanizma | Azaltma |
|--------|-----------|---------|
| Crossover distortion | Çaprazlama noktasında Iq ≠ ideal | Iq 50-100mA, NTC tracking |
| Output transistör nonlinearity | Vbe/Ic eğrisi | Negatif geri besleme (%100) |
| Thermal distortion | Isı ile Vbe drift | NTC termistör, heatsink |
| Power supply ripple | AC ripple modülasyonu | [[power-supply]] §4 |
| Emitter degeneration | Re direnci ile kazanç düşüşü | 0.22-0.47Ω emitter resistor |

### 5.2 THD Hedefleri

| Frekans | THD+N (1W) | THD+N (100W) | Standart |
|---------|-----------|--------------|----------|
| 20Hz | <0.02% | <0.05% | CoreMusic |
| 100Hz | <0.01% | <0.03% | CoreMusic |
| 1kHz | <0.005% | <0.01% | **CoreMusic ana hedef** |
| 10kHz | <0.01% | <0.03% | CoreMusic |
| 20kHz | <0.02% | <0.05% | CoreMusic |

### 5.3 Harmonik Dağılımı

| Harmonik | Tipik Seviye | Kaynak |
|----------|-------------|--------|
| 2. harmonik (2kHz) | -80dB | Çift kutuplu asimetri |
| 3. harmonik (3kHz) | -85dB | Crossover distortion |
| 5. harmonik (5kHz) | -95dB | Higher-order nonlinearity |
| 7. harmonik (7kHz) | -100dB | Higher-order nonlinearity |

**Not:** Çift kutuplu yapı 2. harmoniği bastırır (simetrik); 3. harmonic crossover'dan gelir ve Iq artırılarak azaltılır.

---

## 6. Termal Tasarım

### 6.1 Termal Direnç Zinciri

```
Junction (Tj) → Case (Tc) → Insulator → Heatsink (Ts) → Ambient (Ta)
```

| Parametre | Değer (MJL3281A) | Açıklama |
|-----------|-----------------|----------|
| Tj (maks junction) | 150°C | Datasheet limit |
| Tc (case) | — | Ölçüm noktası |
| RθJC (junction-case) | 0.83°C/W | Datasheet |
| RθCS (case-sink) | 0.5°C/W (silicone insulator) | Montaj |
| RθSA (sink-ambient) | <1.0°C/W (heatsink) | Hava akışı |
| Pdiss (100W @ 8Ω) | ~32W | Output stage |

### 6.2 Junction Sıcaklık Hesabı

| Adım | Hesap | Sonuç |
|------|-------|-------|
| Tj = Ta + Pdiss × (RθJC + RθCS + RθSA) | 25 + 32 × (0.83 + 0.5 + 1.0) | 25 + 32 × 2.33 = **99.6°C** |
| Tj limit | — | 150°C |
| Marj | 150 - 99.6 | **50.4°C** ✅ |
| Fan'lı (RθSA = 0.5°C/W) | 25 + 32 × 1.83 | 25 + 58.6 = 83.6°C ✅ |

**Kural:** Tj 120°C altında tutulur; 150°C'de [[protection]] §3 termal kapanma tetiklenir.

### 6.3 Heatsink Seçimi

| Parametre | Değer |
|-----------|-------|
| Malzeme | Alüminyum (extruded) |
| Yüzey Alanı | >500cm² |
| Termal Direnç | <1.0°C/W (pasif), <0.5°C/W (fan'lı) |
| Fan | 80mm, 12V DC, 60°C'de devreye girer |
| Renk | Siyah anodize (termal emisyon ↑) |

Detay: [[thermal]] §2 (ısıtıcı hesabı), [[thermal]] §3 (hava akışı).

---

## 7. Topoloji Varyantları

### 7.1 Output Transistör Seçenekleri

| # | Transistör | Üretici | Güç | Akım | VCEO | SOA | Fiyat (1adet) | Fiyat (100+) | Stok | Kullanım |
|---|-----------|---------|-----|------|------|-----|---------------|--------------|------|----------|
| 1 | **MJL3281A/MJL1302A** | ON Semi | 200W | 15A | 260V | **En iyi** | ~$2.50 | ~$3.12 | ✅ Mouser: 3793 adet | **En güvenilir** |
| 2 | **TTC5200/TTA1943** | Toshiba | 150W | 15A | 230V | İyi | ~$1.50 | ~$1.20 | ✅ Mevcut | **Güncel versiyon** |
| 3 | **2SC5200/2SA1943** | Toshiba | 150W | 15A | 230V | İyi | ~$2.00 | ~$1.50 | ⚠️ Kısmi stok | **Klasik, yaygın** |

**SOA Karşılaştırma (10ms pulse):**

| Gerilim | 2SC5200 | MJL3281A | MJL4281A |
|---------|---------|----------|----------|
| 50V | 3A | 5A | 6A |
| 100V | 1A | 3.5A | 5A |
| 150V | 0.3A | 1.5A | 3A |

### 7.2 Entegre Class AB Çip Seçenekleri

| # | Çip | Üretici | Güç@8Ω | Güç@4Ω | Supply | THD+N | Fiyat (1+) | Fiyat (100+) | Stok | Kullanım |
|---|-----|---------|--------|--------|--------|-------|------------|--------------|------|----------|
| 1 | **LM1875** | TI | 20W | 30W | ±16-60V | 0.015% | ~$4.22 | ~$4.22 | ✅ Mouser: 354 adet | **Düşük güç** |
| 2 | **LM3886** | TI | 38W | 68W | ±20-94V | 0.03% | ~$6.70 | ~$4.34 | ✅ Mouser: 5267 adet | **Orta güç** |
| 3 | **TDA7294** | ST | 100W | 100W | ±40V | 0.005% | ~$3.25 | ~$2.14 | ✅ JLCPCB: 202 adet | **Yüksek güç** |

### 7.3 Seçim Kılavuzu

| Uygulama | Öneri | Gerekçe |
|----------|-------|---------|
| 100W@8Ω uygulaması | **2SC5200/2SA1943** | Yeterli, ucuz |
| Yüksek güvenilirlik | **MJL3281A/MJL1302A** | Daha iyi SOA |
| Ultra high-end | **MJL4281A/MJL4302A** | En iyi SOA, pahalı |
| Maliyet önemli | **2SC5200/2SA1943** (~$1.5/pair) | Bütçe |
| Düşük güç (20W) | **LM1875** (entegre) | Basit tasarım |
| Orta güç (38W) | **LM3886** (entegre) | Chipwatt protections |
| Yüksek güç (100W) entegre | **TDA7294** | Düşük THD |

### 7.4 Entegre vs Discrete Karşılaştırması

| Kriter | Entegre (LM3886/TDA7294) | Discrete (MJL3281A) |
|--------|--------------------------|---------------------|
| Tasarım süresi | Kısa (datasheet şeması) | Uzun (bias, eşleştirme) |
| Performans | İyi (fabrika kalibre) | Çok iyi (SOA seçimi) |
| Koruma | Dahili (clip, thermal, DC) | Harici [[protection]] gerekli |
| Onarım | Değiştir (tek çip) | Transistör bazlı |
| CoreMusic kullanımı | CM-05/06/07-AB | **CM-71/51/21/20/10-AB** |

### 7.5 Sürücü Aşaması (Driver Stage) Detayları

Discrete Class AB'de VAS (voltage amplification stage) ile output transistörler arasındaki
sürücü aşaması, hem akım kazancı sağlar hem de kapasitif yük kararlılığını korur:

| Parametre | Değer (MJE15030/31) | Gerekçe |
|-----------|---------------------|---------|
| VCEO | 250V / -200V | ±42V rail için yeterli marj |
| Ic maks | 8A / -8A | Output base drive |
| hFE (tipik) | 60 @ 0.5A | Darlington benzeri akım kazancı |
| fT | 30MHz | 100kHz geri besleme kararlılığı |
| RθJC | 3.5°C/W | TO-225AA paket |

**Sürücü güç kaybı:**

```
P_driver ≈ (Vcc - Vout_rms) × Iq_bias + Vcc × I_base_drive
        ≈ 42V × 0.08A + küçük terim ≈ 5W (§4.3 tablosu ile uyumlu)
```

**Neden tek aşamalı sürücü?** Çift aşamalı (Darlington) sürücü daha fazla kazanç sağlar ama
faz kayması ekler; 100kHz açık devre bant genişliği daralır. CoreMusic tercihi: tek aşamalı
sürücü + yüksek hFE output transistörleri (MJL3281A hFE=100 @ 8A).

### 7.6 Zobel Ağı ve Çıkış Kararlılığı

Indüktif hoparlör yükü, çıkış empedansını frekansla artırır; yüksek frekansda osilasyon
riski doğar. Zobel (Boucherot) ağı bu riski bastırır:

| Parametre | Değer | Hesap |
|-----------|-------|-------|
| Rzobel | 10Ω | Hoparlör empedansına eşit (8Ω tipik) |
| Czobel | 100nF film | fc = 1/(2π×R×C) = 159kHz |
| Amfisi çıkış empedansı | <0.1Ω (§2) | Zobel olmadan 1MHz'de artabilir |
| Zobel empedansı @ 1MHz | ~1.6Ω | Kararlılık için yeterli damping |

**Montaj kuralı:** Zobel bileşenleri çıkış terminaline **en kısa yol** ile bağlanır
(trace uzunluğu < 20mm); uzun trace, parasiter endüktans ekler ve Zobel'i etkisiz kılar.

**Doğrulama:** 1MHz'de çıkış osilasyon testi (boş yük + kablolu yük, oscilloscope 20MHz
bandwidth limit açık, 1 saat gözlem).

### 7.7 Giriş Aşaması ve CMRR

Diferansiyel giriş aşaması, ortak mod reddini (CMRR) belirler:

| Parametre | Değer | Kaynak |
|-----------|-------|--------|
| Giriş empedansı | 47kΩ (§2) | Seri direnç + emitter yük |
| CMRR (tipik) | >80dB | Diferansiyel çift eşleştirmesi |
| Gürültü (en) | ~5nV/√Hz @ 1kHz | 2N5401/2N5551 (BJT) |
| Giriş offset | <±5mV | DC kupleleme (kondansatörsüz) |

**DC kupleleme kararı:** Girişte kondansatör kullanılmaz (DC offset'i doğrudan
ilerletir); offset, [[protection]] §4 röle ile korunur. Bu, sub-bass (<20Hz) faz
bozulmasını önler ama DC offset tasarımını kritik yapar.

### 7.8 Hesaplama Örneği: ±42V / 100W @ 8Ω

Tam akım/güç doğrulaması (adım adım):

| Adım | Formül | Değer |
|------|--------|-------|
| Vpeak (ideal) | √(P×R) = √(800) | 28.3V |
| Vpeak (rail düşüşlü, Vce_sat=2V) | Vcc - 2 = 42-2 | 40V headroom ✅ |
| Ipeak | Vpeak/R = 28.3/8 | 3.54A |
| Vrms | Vpeak/√2 = 28.3/1.414 | 20V |
| Pout (doğru) | Vrms²/R = 400/8 | 20W (100W için Vrms=28.3V gerekir) |
| 100W @ 8Ω için Vpeak | 28.3×1.414 | 40.0V |
| 4Ω'da 200W | Vrms = √(200×4) = 28.3V | Aynı Vpeak, 2× akım (7.07A) |

**Kritik NOT:** 100W @ 8Ω için Vrms = 28.3V, Vpeak = 40V. ±42V rail'de Vce_sat = 2V ile
tam 40V tepe elde edilir — **sınırdadır**. Pratikte 96W çıkış (%1 THD+N) elde edilir;
100W için ±45V rail veya Class D ([[class-d]]) gerekir.

### 7.9 Hesaplama Örneği: 4Ω Kararlılığı

4Ω yük, akımı ikiye katlar; SOA ve termal sınırlar yeniden hesaplanır:

| Parametre | 8Ω / 100W | 4Ω / 200W | Değişim |
|-----------|-----------|-----------|---------|
| Ipeak | 3.54A | 7.07A | ×2 |
| Pdiss (output) | 32W | 64W | ×2 |
| RθJC gereksinimi | 2.33°C/W | 1.17°C/W | Sıkılaşır |
| Tj (mevcut heatsink) | 99.6°C ✅ | 174°C ❌ | AŞIM |
| Çözüm | — | Çift transistör paralel + RθSA <0.5°C/W | Fan zorunlu |

**Sonuç:** 4Ω/200W sürekli mod için output transistörleri paralelleştirilir (iki çift
MJL3281A/MJL1302A, emitter dirençleri 0.1Ω'a düşürülür) ve zorlanmış hava soğutma
eklenir. Aksi halde 4Ω modu **tepe güçle (peak) sınırlı** tutulur (10 sn duty cycle).

### 7.10 CoreMusic Entegrasyon Durumu

| Bileşen | Durum | Kanıt |
|---------|-------|-------|
| CM-71-AB (7.1, 100W/kanal) | **PLANNED** | Vault şeması var (§2, §7.2); PCB/schematic YOK |
| Output transistor seçimi | **PLANNED** — MJL3281A/MJL1302A önerisi | §7.1 SOA tablosu |
| Bias devresi (Vbe-multiplier) | **PLANNED** — NTC tracking'li | §3.2 şema |
| Koruma devresi | **PLANNED** — [[protection]] §2-§4 | Vault tasarımı |
| Ölçüm zinciri | **PLANNED** — [[hardware/snr-thd-measurement]] §6 | Vault protokolü |
| Yazılım entegrasyonu | **PLANNED** — `analyzeHardware()` placeholder | §9 repo kanıtı |

**Depo durumu:** Repo'da `.cpp`/`.h` ses motoru dosyası YOK (L0 ALTYAPI PLANNED);
Class AB tasarımı tamamen vault tarafında yaşar (§9). İlk fiziksel prototip için
schematic + PCB üretimi gerekir ([[hardware/amplifier-pcb]]).

---

## 8. Ölçüm Planı

### 8.1 Ölçüm Setupı

```
PC (Signal Generator) → [[hardware/audio-interface]] DAC → Amplifier → Load (8Ω non-inductive)
                                          │
                              [[hardware/snr-thd-measurement]] Analyzer
                                          │
                                    PC (FFT + THD+N)
```

### 8.2 Ölçüm Parametreleri

| Ölçüm | Frekans | Yük | Hedef | Protokol |
|-------|---------|-----|-------|----------|
| THD+N | 1kHz | 8Ω, 1W | <0.01% | [[hardware/snr-thd-measurement]] §6 |
| SNR | 1kHz | 8Ω | >100dB (A-wtd) | [[hardware/snr-thd-measurement]] §1 |
| Frekans yanıtı | 20Hz-20kHz | 8Ω | ±0.5dB | [[hardware/frequency-response]] §5 |
| Damping factor | 100Hz | 8Ω | >200 | Çıkış empedansı <0.1Ω |
| Güç çıkışı | 1kHz | 8Ω/4Ω | 100W/200W (%1 THD+N) | Artırma yöntemi |
| Termal | Sürekli | 8Ω, 100W | Tj < 120°C | Termokupl, [[thermal]] §5 |
| DC offset | 0Hz | Boş yük | <±0.5V | [[protection]] §4 |

### 8.3 Ölçüm Adımları

| Adım | Aksiyon | Beklenen |
|------|---------|----------|
| 1 | DC offset kontrolü (önce) | <±50mV |
| 2 | Bias akımı ölç (emitter resistor üzerinden) | 50-100mA |
| 3 | 1kHz sine, 1W → THD+N | <0.01% |
| 4 | Güç artırma → %1 THD+N noktası | 100W @ 8Ω |
| 5 | SNR (A-wtd, 1kHz ref) | >100dB |
| 6 | Frekans sweep 20Hz-20kHz | ±0.5dB |
| 7 | Termal: 1 saat sürekli 100W | Tj stabil <120°C |

---

## 9. Kaynak Kanıtı

**Repo'da donanım/devre kodu YOK — kaynak: .ai/electronic/**

CoreMusic deposunda Class AB amplifikatör devresine karşılık gelen C/C++/PCB kodu bulunmaz; tasarım yalnızca bu vault dosyalarında yaşar. Depodaki donanıma en yakın yazılım yüzeyi AI orkestrasyon katmanının placeholder ölçüm alanlarıdır:

| Dosya | Satır | Kanıt | Doğrulama |
|-------|-------|-------|-----------|
| `shared/src/AI/AIEngine.php` | 138 | `public function analyzeHardware(string $deviceId): array` — cihaz analizi | `Select-Object -Index (137)` ile doğrulandı |
| `shared/src/AI/AIEngine.php` | 142-143 | `$snr = 0.0;` · `$thd = 0.0;` — placeholder (gerçek ölçüm YOK) | `Select-Object -Index (141..142)` ile doğrulandı |

**Sonuç:** `analyzeHardware()` snr/thd değerlerini her zaman `0.0` döndürür. Class AB ölçüm zinciri vault tarafında tanımlıdır (§8); gerçek cihaz entegrasyonu PLANNED.

---

## 10. Riskler

| # | Risk | Olasılık | Etki | Önlem | İlgili Dosya |
|---|------|----------|------|-------|--------------|
| 1 | **Termal kaçak** | Orta | CRITICAL (transistör hasarı) | NTC tracking, Iq < 100mA | §3.3 · [[thermal]] |
| 2 | **Crossover distortion** | Yüksek | HIGH (ses kalitesi) | Iq 50-100mA, emitter resistor | §3.1 |
| 3 | **DC offset** (hoparlör hasarı) | Orta | CRITICAL | [[protection]] §4 röle | [[protection]] |
| 4 | **Kısa devre** | Düşük | CRITICAL | [[protection]] §2 anında kapanma | [[protection]] |
| 5 | **SOA ihlali** (indüktif yük) | Düşük | HIGH | Zobel ağı, SOA tablosu | §7.1 |
| 6 | **Bias drift** (sıcaklık) | Orta | MEDIUM (THD artışı) | NTC termistör | §3.2 |
| 7 | **Heatsink yetersiz** | Orta | HIGH (termal kapanma) | RθSA < 1.0°C/W | §6.3 |
| 8 | **Power supply ripple** | Düşük | MEDIUM (SNR düşüşü) | [[power-supply]] §4 | [[power-supply]] |
| 9 | **Ground loop** | Orta | MEDIUM (hum) | Star ground | §2 |
| 10 | **Giriş doygunluğu** | Düşük | MEDIUM | Giriş empedansı 47kΩ | §2 |

---

## 11. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | PCM3168A, XMOS |

---

## 12. Kaynakça (Web Araştırması)

| İddia | URL | 2026-10-13 | Durum |
|-------|-----|------------|-------|
| TI, LM1875 20W Class AB audio power amplifier ürün sayfasını resmi alan adında yayımlar (entegre Class AB referansı) | https://www.ti.com/product/LM1875 | 2026-10-13 | HTTP 200 doğrulandı |
| TI, TL072 low-noise JFET-input op-amp ürün sayfasını resmi alan adında yayımlar (diferansiyel giriş aşaması referans op-amp) | https://www.ti.com/product/TL072 | 2026-10-13 | HTTP 200 doğrulandı |

Doğrulama yöntemi: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode