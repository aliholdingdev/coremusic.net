---
type: electronic
category: dsp-equalizer
title: "CoreMusic — Equalizer System"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.1.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Equalizer System

**See also:** [[electronic/dsp/index]] · [[ADR-025-professional-eq-system]]

---

## 1. Amaç

Equalizer System, CoreMusic ELECTRONICS platformunun grafik ve parametrik EQ
sistemini tanımlar. 31 bant'a kadar destek sunar.

**Kapsam dışı:** biquad matematiği ve kararlılık `[[filters]]` · zincir
sırası/görev `[[dsp-pipeline]]` (ADR-062).

**Dürüstlük notu:** Repo'da EQ C++ kodu YOK — kaynak: `.ai/electronic/**`
(ADR-025 dahil). Bu dosya yalnız donanım-bağımsız bant/parametre/ölçüm
disiplinini taşır.

> **ADR-025 bağlayıcılığı (düzeltme 2026-10-13):** ADR-025 `status: accepted`
> olup kendisi **frozen değildir** (`ADR-025 §16: "frozen YOK"`); ancak
> `.ai/electronic/dsp/AGENTS.md` kural 1 gereği bu klasörde ADR-025'in
> **parametre kararları değiştirilemez** — değişiklik yeni ADR açmayı
> gerektirir. Bağlayıcı kararlar (ADR-025 §2): **çift EQ** (31-band grafik +
> ≥2 bant parametrik), sıra **grafik → parametrik** (seri), grafik ±12 dB /
> sabit Q ≈4.3–4.4 / aktif bant 2–31, parametrik ±12 dB / Q 0.3–10.

---

## 2. EQ Tipleri

### Graphic Equalizer

| Bant Sayısı | Frekans Aralığı | Kullanım |
|-------------|------------------|----------|
| 2 Band | Low/High | Basit ton |
| 3 Band | Low/Mid/High | Standart |
| 5 Band | 5 frekans | Ev ses |
| 8 Band | 8 frekans | Orta seviye |
| 10 Band | 10 frekans | Profesyonel |
| 15 Band | 15 frekans | Yüksek |
| 31 Band | 31 frekans | Stüdyo |

**Mimari not:** grafik EQ, sabit merkez frekanslı peaking bantların
kısmi-overlap'idir; her slider 1 biquad → 31 slider = 31 biquad/kanal.

### Parametric Equalizer

| Bant Sayısı | Parametreler | Kullanım |
|-------------|-------------|----------|
| 2 Band | Freq, Gain, Q | Basit |
| 5 Band | Freq, Gain, Q | Orta |
| 10 Band | Freq, Gain, Q | Yüksek |
| 31 Band | Freq, Gain, Q | Stüdyo |

**Grafik vs parametrik:** grafik = sabit fc, serbest gain; parametrik = serbest
fc/gain/Q. 31-band grafik, pratikte parametrik motorun sabit fc modudur →
tek kod yolu (biquad) yeterli.

---

## 3. 31-Band Graphic EQ Frekansları

```
31Hz    63Hz    125Hz   250Hz   500Hz
1kHz    2kHz    4kHz    8kHz    16kHz
+ ara frekanslar...
```

| Bant | Frekans | Octave |
|------|---------|--------|
| 1 | 20Hz | — |
| 2 | 25Hz | — |
| 3 | 31.5Hz | 1/3 octave |
| 4 | 40Hz | — |
| 5 | 50Hz | — |
| 6 | 63Hz | — |
| 7 | 80Hz | — |
| 8 | 100Hz | — |
| 9 | 125Hz | — |
| 10 | 160Hz | — |
| 11 | 200Hz | — |
| 12 | 250Hz | — |
| 13 | 315Hz | — |
| 14 | 400Hz | — |
| 15 | 500Hz | — |
| 16 | 630Hz | — |
| 17 | 800Hz | — |
| 18 | 1kHz | — |
| 19 | 1.25kHz | — |
| 20 | 1.6kHz | — |
| 21 | 2kHz | — |
| 22 | 2.5kHz | — |
| 23 | 3.15kHz | — |
| 24 | 4kHz | — |
| 25 | 5kHz | — |
| 26 | 6.3kHz | — |
| 27 | 8kHz | — |
| 28 | 10kHz | — |
| 29 | 12.5kHz | — |
| 30 | 16kHz | — |
| 31 | 20kHz | — |

### 3.1 1/3-Oktav Matematiği

- Komşu bant oranı: `2^(1/3) ≈ 1.2599` → 20 × 1.2599 ≈ 25.2 Hz (25 Hz),
  25.2 × 1.2599 ≈ 31.7 Hz (31.5 Hz) — tablo ISO 26:10 yuvarlamasıyla uyumlu
- 31 bant × 1/3 oktav ≈ 10.3 oktav → 20 Hz–20 kHz (≈ 9.97 oktav) tam kapsar
- Grafik EQ'da komşu bant overlap'i: Q ≈ 4.3 (1/3 oktav bandı) için −3 dB
  noktaları komşu fc'de kesişir → **toplam eğrisi düz değil, ±1–2 dB
  ripple** olur (ölçüm §9, kabul ±0.5 dB sadece parametrik modda)

### 3.2 ADR-025 Kapsamı

ADR-025 bu 31 bant planını **bağlayıcı (accepted, frozen YOK)** olarak sabitler; frekansların
yeniden dağıtımı (örn. 1/6 oktav veya log dışı) yasaktır. Mevcut bant
listesi = SSOT (bu bölüm).

---

## 4. Parametric EQ Parametreleri (ADR-025-c)

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Bant sayısı | **≥ 2** (üst sınır konfigürasyon, önerilen 8) | 2 |
| Frequency | 20Hz - 20kHz (log-uzay serbest) | 500Hz |
| Gain | -12dB - +12dB | 0dB |
| Q Factor | **0.3 - 10** | 1.0 |
| Filter Type | Peak (varsayılan), LowShelf, HighShelf, Notch | Peak |

> **ADR-025 §2.2b** ile birebir (`adr-audio-template.md:177-179` kaynaklı).
> Sapma kaydı: `eq-parametric.md:30` "Q 0.5-10" der — ADR-025 **0.3–10**
> kabul eder (ADR-025 §2.2b sapma notu). Grafik EQ'un Q'su bu tabloya
> girmez: **sabit ≈4.3–4.4** (ADR-025 §2b).

### 4.1 Çift EQ Yapısı (ADR-025-a/b)

| EQ | Bant | Q | Kazanç | Aktif bant |
|---|---|---|---|---|
| **Grafik** | 31 (ISO 1/3-oktav ızgara) | sabit ≈4.3–4.4 | ±12 dB | **2–31 seçilebilir** |
| **Parametrik** | ≥ 2 (serbest) | 0.3–10 | ±12 dB | hepsi |

- İkisi **ayrı bloklar**, tek sınıfta birleşmez; ikisi de tek başına "EQ"
  diye adlandırılmaz (ADR-025-a)
- **Sıra grafik → parametrik (seri)** — paralel şube yok (ADR-025-d +
  ADR-062 §2(a)/1); ters sıra (`parametric>graphic`) izinli ama preset'te
  `chainOrder` alanı + `*-rev` adı zorunlu (ADR-025 §2.2c)
- Aktif olmayan grafik bant **işlenmez** (sıfır CPU) ve arayüzde pasif
  gösterilir (ADR-025-b)

### 4.1 Peaking (RBJ) — Katsayı Üretimi

```text
A  = 10^(gain_dB / 40)          (amplitude, peaking için)
w0 = 2·π·fc / fs
alpha = sin(w0) / (2·Q)

b0 =   1 + alpha·A
b1 =  -2·cos(w0)
b2 =   1 - alpha·A
a0 =   1 + alpha / A
a1 =  -2·cos(w0)
a2 =   1 - alpha / A
→ tümü a0'a bölünür
```

- `A = 10^(gain/40)`: +12 dB → A ≈ 1.995; 0 dB → A = 1 (birim, tüm katsayılar
  `b=a` → passthrough)
- Q = 1.0, fc = 1 kHz, gain = 0 dB → `b == a` → filtre kimlik (bypass ile
  bitişik)

### 4.2 Shelf (RBJ)

```text
A = 10^(gain/40)
alpha = sin(w0)/2 · √( (A + 1/A)·(1/S − 1) + 2 )   ; S = shelf slope
(S ile: S=1 maksimum eğim ≈ Q=0.707 karşılığı)
```

- **LowShelf**: 1/3-oktav planın 20–100 Hz bloğunu toplu yönetmek için
- **HighShelf**: 10–20 kHz air band
- ADR-025 uç bantlar shelf olabilir; mid bantlar peaking

### 4.3 Q ↔ Bant Genişliği İlişkisi

```text
BW (oktav) = (2/Q) · asinh( 1 / (2·sin(w0)/?) )  — basitleştirilmiş:
en pratik yaklaşıklık:
BW_oct ≈ (2 / Q) · asinh(1 / (2·k))   ... k=1 için:
Q = 1.414 → ~1 oct   (−3 dB tek genişlik)
Q = 2.83  → ~0.5 oct (1/6 oktav)
Q = 4.32  → ~0.33 oct(1/3 oktav) ← grafik EQ bandı
```

**Q aralıkları (ADR-025):** parametrik **0.3–10** → Q=10 dar (~0.2 oct,
tek frekans izolasyonu) … Q=0.3 çok geniş (~13 oct, tüm bant eğilimi);
grafik EQ **sabit Q ≈4.3–4.4** (~1/3 oktav — üstteki 4.32 satırı).
Grafik EQ'da kullanıcı Q **değiştiremez** (yalnız kazanç); ince ayar
parametrik EQ'nun işidir (ADR-025 §2.1).

### 4.4 Faz Etkisi

- Peaking bant, faz tepkisini de değiştirir; toplam 31 bant faz kayması
  group delay şeklinde ortaya çıkar
- Uçlarda (20 Hz / 20 kHz) ADR-062 zinciri payını bozmamak için uç bantlar
  dar Q ile değil shelf ile yönetilir (genlik-odaklı, faz daha yumuşak)
- Faz ölçümü `[[measurements]]`; kabul: negatif group delay YOK (fiziksel
  olmayan sonuç = katsayı hatası)

---

## 5. EQ Preset Sistemi

| Preset | Kullanım |
|--------|----------|
| Flat | Tüm bantlar 0dB |
| Bass Boost | Düşük frekans güçlendirme |
| Treble Boost | Yüksek frekans güçlendirme |
| Vocal | Orta frekans vurgu |
| Rock | Bass + Treble boost |
| Jazz | yumuşak ton |
| Classical | Denge |
| Custom | Kullanıcı tanımlı |

### 5.1 Preset Kuralları

1. Preset yalnız **parametre setidir** (fc, gain, Q dizisi) — yeni tip
  /katsayı yolu üretmez; tümü §4 katsayı formülüne iner
2. Flat = tüm gain 0 → kimlik katsayı (bypass ile bitişik, ölçüm §9.1)
3. Preset uygulama anında 31 biquad'ın katsayıları **toplu hesap → atomik
   yayılım** (click yok, §9.4); hesap ses thread'inde DEĞİL
4. Kullanıcı preset'i ADR-025'in **bağlayıcı kararlarını** (bant ızgarası,
   ±12 dB, Q sınırları, grafik→parametrik sıra) değiştiremez → kullanıcı
   yalnız **içerik** (kazanç/frekans izinli aralıkta) seçer; grafik bant
   merkezleri ISO ızgarasında sabit (§3/§4.1)

### 5.2 Kanal Topolojisi (8.1 — ADR-062)

- Master EQ: 31 bant × 1 (tüm kanallar öncesi/sonrası — pipeline'daki
  yerleşim `[[dsp-pipeline]]`)
- Aux send EQ: ayrı 31 bant (PLANNED — hafıza/canal hesabı ADR-017
  512-örnek bütçesiyle birlikte ölçülür)
- Uyarı: 31 bant × 9 kanal = 279 biquad ≈ 279 × 5 MAC/örnek × 48000
  ≈ 67.0 M MAC/s — STM32H743 (Cortex-M7, `[[../hardware/index|hardware]]`)
  için **ölçülmeden iddia edilmez** (⚠️ VERIFICATION REQUIRED — gerçek
  yürütme profili `[[measurements]]`/soak ile doğrulanacak)

---

## 6. Uygulama Disiplini (Zero-Allocation)

1. 31 biquad durum dizisi (2×31 float × kanal) **init'te allocate**
   (`alignas(64)`); ses callback'inde `new`/`vector::push_back` YASAK
2. Katsayı güncelleme: control thread hesaplar → `std::atomic` publish →
   ses thread A/B crossfade (5–10 ms) (bkz `[[filters]]` §9.5)
3. Callback `noexcept`; NaN/finite guard `[[dsp-pipeline]]` §guard
4. Gain sweep ölçümü (§9) bypass modu ile karşılaştırılır

---

## 7. Ölçüm Protokolü (EQ Kabul)

| # | Test | Yöntem | Kabul |
|---|---|---|---|
| 1 | Flat doğruluğu | sweep, tüm gain 0 | ±0.1 dB (kimlik) |
| 2 | Tek bant peaking | fc'ye 1 ton | kazanç ±12 ±0.3 dB |
| 3 | Q doğruluğu | −3 dB noktaları | BW ≈ tablo (§4.3) ±%5 |
| 4 | 31 bant sweep | log sweep | ripple ≤ ±0.5 dB (parametrik) |
| 5 | Shelf kenar | 20 Hz / 20 kHz | tepe/kırpma yok (uç bant clamp) |
| 6 | Preset geçişi | Flat → Rock canlı | click yok, katsayı yayılım ≤ 10 ms |

---

## 8. Hata Modları

| # | Hata | Sonuç | Önlem |
|---|---|---|---|
| 1 | ±12 dB klips aşımı | headroom kaybı, THD | §4 klips + toplam gain monitor |
| 2 | Katsayı anlık değişimi | click | §6 A/B crossfade |
| 3 | 31 bant toplam tepe | dijital saturasyon | zincir sonu limiter `[[dynamics]]` |
| 4 | Preset ≠ ADR-025 bant planı (500 Hz default dahil) | ADR-025 ihlali | §5.1 kural 4 |
| 5 | fs değişimi (48k dışı) | katsayı bayat | tüm biquad yeniden üretim (init) |

---

## 9. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-025-professional-eq-system]] | Çift EQ (**bağlayıcı, frozen YOK** — grafik ±12 dB sabit Q ≈4.3–4.4 + parametrik Q 0.3–10) |
| [[ADR-017-dsp-hardware-mode]] | 512 / 48 kHz / float32 — blok & bütçe |
| ADR-062 | Pipeline sırası — EQ grafik (aşama 3) → parametrik (aşama 4); crossover zincirde ayrı aşama değil ⚠️ |

---

## 10. Çapraz Referanslar

- `[[electronic/dsp/index]]` · `[[filters]]` · `[[crossover]]` ·
  `[[dsp-pipeline]]` · `[[dynamics]]` · `[[measurements]]`
- `.ai/.decisions/accepted/ADR-025-professional-eq-system.md` (bağlayıcı karar metni, frozen YOK)

---

## 11. Derinleştirme: Katsayı ve Frekans Hesapları (2026-10-13)

> §2–§7'nin formüllerini sabit örneklerle pekiştirir. Repo'da EQ kodu YOK
> (PLANNED) — sayısal örnekler RBJ/ADR-025 uygulamasıdır, doğrulama §7'dedir.

### 11.1 ISO 1/3-Oktav Frekans Listesi (ADR-025-a Izgarası)

31 bant, 20 Hz–20 kHz (her frekans ×2^(1/3) ≈ ×1.2599 ile bir sonrakine geçer):

```text
20 · 25 · 31.5 · 40 · 50 · 63 · 80 · 100 · 125 · 160 · 200 · 250 · 315 ·
400 · 500 · 630 · 800 · 1000 · 1250 · 1600 · 2000 · 2500 · 3150 · 4000 ·
5000 · 6300 · 8000 · 10000 · 12500 · 16000 · 20000
```

- Sayım: **31** (80 Hz–20 kHz arası 25 bant + 20/25/31.5/40/50/63 altı bant
  = 31) — bu liste ADR-025'in "eq-parametric.md:117-122 ile aynı"
  frekans kümesidir.
- **500 Hz** ADR-025'in parametrik **default** bant merkezidir (§4.1).
- Aktif bant seçimi: **2–31** (ADR-025-a); 10 bant (TV/K10) = bu ızgaranın
  oktav alt kümesi (ör. 100 · 200 · 400 · 800 · 1.6k · 3.15k · 6.3k ızgaraya
  oturur — kesin alt küme seçimi tasarım aşamasında, PLANNED).

### 11.2 RBJ Peaking Katsayı Sayısal Örneği

**Girdi:** f0 = 1000 Hz · fs = 48000 · gain = +6 dB · Q = 1

| Değer | Formül | Sonuç |
|---|---|---|
| A | 10^(6/40) | 1.41254 |
| ω0 | 2π·1000/48000 | 0.130900 rad |
| cos ω0 | — | 0.991445 |
| sin ω0 | — | 0.130526 |
| α | sin ω0/(2·Q) | 0.065263 |
| b0 (normalize öncesi) | 1 + α·A | 1.092180 |
| b1 | −2·cos ω0 | −1.982889 |
| b2 | 1 − α·A | 0.907820 |
| a0 | 1 + α/A | 1.046201 |
| a1 | −2·cos ω0 | −1.982889 |
| a2 | 1 − α/A | 0.953799 |

Normalize (`b/a0, a1/a0, a2/a0`):

```text
b0' = 1.044  b1' = −1.895  b2' = 0.868
a1' = −1.895 a2' = 0.912
```

**Kontrol (DC):** (b0+b1+b2)/(a0+a1+a2) = 0.01711/0.01711 = **1.000** ✓
(peaking DC'de nötr — doğruluk kapısı §7/3). f0'da kazanç **+6 dB**
(üretim doğrulaması sweep ile §7/1).

### 11.3 Q ↔ Bant Genişliği Dönüşümü

`BW_oktav = 2·asinh(1/(2Q)) / ln 2`:

| Q | BW (oktav) | Karşılığı |
|---|---|---|
| 0.3 | ≈ 4.74 | çok geniş (dip/tepe düzeltmesi) |
| 0.707 | ≈ 1.94 | Butterworth genişliği (≈2 oktav) |
| 1.0 | ≈ 1.39 | standart geniş |
| 4.3–4.4 (grafik sabit) | ≈ 0.34 | **≈1/3 oktav** — ızgara ile tutarlı ✓ |
| 10 | ≈ 0.14 | dar (notch benzeri) |

- **Tutarlılık kontrolü:** grafik EQ'nun sabit Q'su (≈4.3–4.4) 1/3-oktav
  ızgaranın tam genişliğidir → bitişik bantların kesişimi tasarımı gereğidir
  (ADR-025-a). Bu, Q aralığı 0.3–10 (parametrik) ile **çelişmez** — iki farklı
  EQ'dur (§4 tablosu).
- Ters dönüşüm: `Q = 1 / (2·sinh(BW·ln2/2))`.

### 11.4 Gain Staging Hesabı (§6 headroom)

Senaryo: 31 grafik bantının **tamamı +12 dB** (ADR-025 sınırı), parametrik
3 bant **+6 dB**:

| Katkı | Hesap | Sonuç |
|---|---|---|
| Tekil üst sınır (grafik) | ADR-025-a | +12 dB |
| Bitişik 3 bant birlikte (≈ kesişen +Q) | 12+ kaba üstel toplam değil — **ölçülür** | ⚠️ ölçüm §7/1 |
| Parametrik toplam | 3 × +6 dB | ⚠️ üstel toplam ≈ +11 dB mertebe (yaklaşık) |
| Koruma | zincir sonu TP + limiter (ADR-025-d/e) | TP ≤ −0.3/−1 dBTP |

> Kural: **"bant başına +12 dB" ≠ "toplam +12 dB"**. Toplam boost için
> zincir sonu true-peak kontrolü **zorunludur** (ADR-025 §2.2d headroom);
> toplam sayılar ölçüm olmadan **yazılmaz** (§7 kural 3).

### 11.5 Preset Kayıt Şeması (ADR-025 §2.2c hizası)

```text
{
  "name": "Rock",
  "chainOrder": "graphic>parametric",   // ters sıra: "parametric>graphic" + "Rock-rev"
  "graphic":   { "activeBands": 31, "gainsDb": [ ... 31 değer ... ] },
  "parametric":[ { "f":500, "q":1.0, "gDb":0.0 }, ... ≥2 bant ],
  "bypass": false
}
```

- `chainOrder` alanı **her preset'te** bulunur (ters sıra `*-rev` ile §5.1
  kural 4'ün karşılığı).
- Preset ≠ ADR-025 bant planı hatası §8/4'tedir.

### 11.6 Ölçüm Veri Şablonu (Kayıt Zorunlu)

| Alan | Değer (boş bırakılmaz) |
|---|---|
| Tarih / fs | YYYY-MM-DD / 48000 |
| Topoloji | grafik + parametrik (chainOrder) |
| Sweep ripple | dB — kabul ±0.5 (§7/1) |
| DC doğrulama | 1.000 ± 0.01 (§7/3) |
| Toplam boost TP | dBTP (4× OS) ≤ limit |
| CPU (bant başına) | ⚠️ ölçülmeden "GERÇEK" sayı yazılmaz (ADR-025-e) |

### 11.7 ADR Çapraz Referans Özeti

| Kural | Kaynak | Bu bölümdeki karşılık |
|---|---|---|
| Çift EQ, Q aralıkları, aktif bant 2–31 | [[ADR-025-professional-eq-system]] §2.1-2.2 | §11.1–11.3 |
| Grafik→parametrik sıra + ters sıra izni | ADR-025 §2.2c + ADR-062 §2(a) | §11.5 |
| Zero-alloc / DF2T / FTZ | [[ADR-017-dsp-hardware-mode]] + ADR-025-e | §6, §11.2 (katsayı blok sınırında) |
| 9 aşama (EQ 3→4) | ADR-062 | §9 tablosu |
| PCM3168A (DAC) | ADR-038 H001 | §7 ölçüm donanımı |

---

## 12. EQ Kabul Kontrol Listesi (Uygulama Öncesi)

EQ zinciri (grafik → parametrik, ADR-062 aşama 3 → 4) devreye alınmadan önce adımlar sırayla tamamlanır; atlanan adım varsa **kabul edilmez**.

| # | Adım | Referans | Çıktı (kayıt zorunlu) |
|---|------|----------|------------------------|
| 1 | Çift EQ kurgusunu doğrula (grafik → parametrik seri, paralel yok) | §4.1 · ADR-025 §2.2c | zincir şeması |
| 2 | Aktif bandı doğrula (2–31, ±12 dB kazanç sınırı) | §3.2 · §4 | bant listesi |
| 3 | Katsayıları init'te hesapla (DF2T, FTZ, blok sınırında) | §6 · §11.2 | katsayı tablosu |
| 4 | Q ↔ bant genişliği dönüşümünü doğrula | §4.3 · §11.3 | dönüşüm tablosu |
| 5 | Preset kaydını ADR-025 §2.2c şemasına göre yaz | §5.1 · §11.5 | preset kaydı |
| 6 | Sweep ölçümü yap (20 Hz–20 kHz) | §7.1 | ripple dB |
| 7 | DC doğrulaması yap (1.000 ± 0.01) | §7.3 | DC değeri |
| 8 | Toplam boost için TP ölçümü (4× OS) | §7 · §11.6 | dBTP |
| 9 | CPU/bant başı ölç | ADR-025-e | µs (ölçülmeden "gerçek" sayılmaz) |

### 12.1 Sık Yapılan EQ Hataları

| # | Hata | Sonuç | Önlem |
|---|------|-------|-------|
| 1 | Katsayıların runtime'da hesaplanması | Audio callback'inde tahsis → kural 1 ihlali | §6 — init'te hesapla |
| 2 | Paralel EQ şubesi açılması | ADR-062/ADR-025 sırası ihlali | §4.1 — seri zincir |
| 3 | Denormal değer (FTZ kapalı) | CPU spike, bütçe aşımı | §6 · ADR-025-e |
| 4 | Kazanç > ±12 dB uygulanması | TP/kırpma riski | §4 · ADR-025 §2.1 |
| 5 | Aktif bandın 1'e veya 32'ye taşınması | Izgara ihlali (2–31) | §3.1 · §3.2 |
| 6 | Ölçümsüz "EQ uygun" kararı | Tekrarlanamayan iddia | §11.6 şablonu zorunlu |
| 7 | Ters sıra (parametrik → grafik) izinsiz uygulama | ADR-025 §2.2c ihlali | Yalnız ADR'de izinli |

### 12.2 EQ DUR Kapıları

| Kapı | Koşul | Sonuç |
|---|---|---|
| Sıra kapısı | grafik → parametrik zinciri bozuldu | DUR — yeni ADR |
| Kazanç kapısı | bant kazanç ±12 dB dışında | DUR — ADR-025 ihlali |
| Ölçüm kapısı | sweep ripple ölçülmedi | `⚠️ VERIFICATION REQUIRED` |
| Tahsis kapısı | init dışında katsayı üretimi | DUR — kural 1 ihlali |
| DAC kapısı | PCM3168A değilse | DUR — ADR-038 H001 |

**Not:** Bu bölümdeki değerler (±12 dB · aktif bant 2–31 · ripple ±0.5 dB · DC 1.000 ± 0.01) mevcut §3/§4/§7 kapılarıyla **aynı** değerlerdir; yeni tolerans **tanımlanmaz**.

### 12.3 EQ Ölçüm Ortamı ve Tekrarlanabilirlik

Tekrarlanabilir bir EQ ölçümü için ön koşullar (§7 ölçüm protokolünün tamamlayıcısı):

| # | Koşul | Gerekçe | Kaynak |
|---|---|---|---|
| 1 | fs = 48 kHz / 512 blok / float32 sabit | Zincir ADR-017 ile aynı yapıda | [[ADR-017-dsp-hardware-mode]] |
| 2 | Sweep kaynağı ve seviyesi kayıtta belirtilir | Aynı sweep ≠ aynı ripple | §7.1 · §11.6 |
| 3 | EQ bypass↔aktif sırayla ölçülür | Baseline olmadan sapma ölçülemez | §7.1 |
| 4 | Tüm bantlar tek oturumda ölçülür | Oturumlar arası ekipman farkı | §7 |
| 5 | Ölçüm donanımı PCM3168A üzerinden | ADR-038 H001 | §7 · ADR-038 |

**Tekrar kuralı:** Aynı sweep + aynı bypass durumu → ripple ±0.5 dB içinde tekrarlanır; aşarsa oturum geçersizdir ve §11.6 şablonuyla yeniden ölçülür. Kayıtsız ölçüm kanıt sayılmaz.

**Kapsam notu:** Bu bölüm yalnız *doğrulama* adımlarını taşır; EQ parametreleri ADR-025'te **frozen**dır ve burada değiştirilemez (dsp AGENTS.md §3.1). Parametre değişikliği yalnız ADR-025 üzerinden yapılır.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
