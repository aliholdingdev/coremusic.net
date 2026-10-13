---
type: electronic
category: dsp-filters
title: "CoreMusic — Filter Systems (FIR, IIR, FFT)"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.1.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Filter Systems

**See also:** [[electronic/dsp/index]] · [[ADR-017-dsp-hardware-mode]]

---

## 1. Amaç

Filter Systems, CoreMusic DSP Engine'deki FIR, IIR ve FFT tabanlı filtreleme
mekanizmalarını tanımlar. Bu dosya; transferans fonksiyonu matematiği, biquad
katsayı üretimi, bilinear transform, kutup-sıfır yerleşimi ve çalışma zamanı
davranışını tek yerde toplar.

**Kapsam dışı:** crossover topolojisi `[[crossover]]`, EQ bant planı
`[[equalizer]]` (ADR-025), filtre zinciri sıralaması `[[dsp-pipeline]]`.

**Dürüstlük notu:** Repo'da C/C++ filtre kodu YOK — kaynak: `.ai/electronic/**`
DSP belgeleri; formül düzeyindeki iddialar standart literatür (RBJ Audio EQ
Cookbook, bilinear transform) ile çapraz doğrulanmıştır.

---

## 2. High Pass Filter

Düşük frekansları keser.

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Cutoff Frequency | 20Hz - 200Hz | 80Hz |
| Order | 1 - 8 | 2 |
| Slope | 6dB/oct - 48dB/oct | 12dB/oct |
| Type | Butterworth / Linkwitz-Riley | Butterworth |

### 2.1 Kullanım Yeri (Neva Engine)

- **80 Hz HPF**: subwoofer çıkış koruması (DC/yüksek alt-bas yükü) —
  `[[../amplifier/index|amplifier]]` güç sınırlamasıyla eşleşir
- **20 Hz HPF**: kaynak taşınır (vinyl rumble / DC offset) — ADR-038 H001
  koruma rölesi zincirinin öncesinde dijital temizlik

### 2.2 Bitirme Davranışı

| Bölge | Butterworth (N=2) | Linkwitz-Riley (2N=4) |
|---|---|---|
| @fc genlik | −3.01 dB | −6.02 dB |
| Faz @fc | −90° (N=2) | −180° (N=4, çift birebir) |
| grup gecikmesi | tepe cutoff yakını | daha geniş, daha düzgün |

> LR'de iki birebir Butterworth cascadesi: kutuplar aynı, sıfırlar fc'de
> 2× → −6 dB/oct × 2 = −24 dB/oct ve faz iki kez −90°... hayır: LR-4'ün
> kutup-sıfır iptali fazı düzleştirir (bkz §7).

---

## 3. Low Pass Filter

Yüksek frekansları keser.

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Cutoff Frequency | 2kHz - 20kHz | 18kHz |
| Order | 1 - 8 | 2 |
| Slope | 6dB/oct - 48dB/oct | 12dB/oct |
| Type | Butterworth / Linkwitz-Riley | Butterworth |

### 3.1 Anti-Alias & DAC Öncesi

- PCM3168A (ADR-038) öncesi 18 kHz LPF → 48 kHz örneklemeorde Nyquist
  (24 kHz) payını korur; gözenek (imaging) bileşeni ayrıdır
- Nyquist hesap: `fs/2 = 48000/2 = 24000 Hz` → geçiş bandı 18–24 kHz = 6 kHz

### 3.2 Yüksek Order Uyarısı

N ≥ 6 iken kutuplar (bilinear sonrası) fs/4 civarında toplanır; katsayı
yuvarlaması (float32) kaynaklı **limit cycle** riski doğar → §9.4.

---

## 4. FIR Filter (Finite Impulse Response)

| Özellik | Değer |
|---------|-------|
| Taps | 64 - 4096 |
| Phase | Linear phase |
| Kullanım | High-precision EQ, room correction |
| CPU | Yüksek (convolution) |
| Latency | Taps/2 samples |

### 4.1 Gecikme Matematiği (faz-doğrusal)

Simetrik FIR'da grup gecikmesi sabittir:

```text
N = taps (tek sayı, N = 2M+1)
group delay = M = (N-1)/2 samples
@48 kHz: 64 tap ≈ 63/2 = 31.5 örnek ≈ 0.656 ms
         512 tap ≈ 255.5 örnek ≈ 5.32 ms
```

**Bütçe etkisi (ADR-017 512-örnek blok):** 512-tap FIR tek başına blok
süresinin ~%50'sini harcar → gerçek sistemde partitioned convolution gerekir
(§5.3).

### 4.2 Pencereleme

| Pencere | Yan lob (dB) | Bant geçiş genişliği | Not |
|---|---|---|---|
| Rectangular | −13 | dar | Gibbs, ripple max |
| Hanning | −31 | orta | genel amaçlı |
| Hamming | −43 | orta | yan lob dengesi |
| Blackman | −58 | geniş | en düşük yan lob |

### 4.3 Tasarım Yöntemleri

1. **Windowed-sinc**: `h[n] = sinc(2·fc·(n−M)) · w[n]` — basit, yeterli
2. **Parks-McClellan (equiripple)**: minimax optimizasyonu — en dar geçiş
   bandı, iteratif (derleme/planlama maliyeti)
3. **Least-squares**: hedef manyetik ile en küçük kare sapma

### 4.4 Neden Genel Zaman Çizgisinde FIR Yok

CPU maliyeti O(N) örnek başına → 48 kHz × 4096 tap ≈ 196.6 M MAC/s/kanal;
Cortex-M7 (STM32H743) bütçesinin tek kanalda bile büyük kısmını alır.
**Karar:** FIR yalnız konvolüsyon reverb'ünde (effects §5) ve offline
room-correction planında (PLANNED) — gerçek zamanlı EQ IIR (biquad) ile.

---

## 5. IIR Filter (Infinite Impulse Response)

| Özellik | Değer |
|---------|-------|
| Order | 1 - 8 |
| Phase | Non-linear |
| Kullanım | Real-time EQ, crossover |
| CPU | Düşük |
| Latency | Minimum |

### 5.1 Biquad Transferansı (2. derece)

```text
         b0 + b1·z⁻¹ + b2·z⁻²
H(z) = ─────────────────────────
         1  + a1·z⁻¹ + a2·z⁻²

Diferansiyel denklem (DF1):
y[n] = b0·x[n] + b1·x[n−1] + b2·x[n−2] − a1·y[n−1] − a2·y[n−2]
```

- 5 katsayı (b0, b1, b2, a1, a2) — normalizasyonda `a0 = 1`
- 2 kutup + 2 sıfır → order-2; N sıralı filtre = ⌈N/2⌉ biquad cascadesi
- **Gecikme:** grup gecikmesi fs'e göre küçük (blok içi), genellikle < 1 örnek
  @fc civarı — FIR'a göre dramatik avantaj

### 5.2 Bilinear Transform

s-düzlemi (analog) → z-düzlemi (dijital):

```text
        1 − z⁻¹
s = K · ──────── ,   K = 2·fs (veya π·fs tanımına göre)
        1 + z⁻¹
```

**Frequency warping:** analog cutoff `Ωc`, dijital `ωc` karşılığı:

```text
ωc = 2·arctan(Ωc / (2·fs))    (K = 2·fs seçimiyle)
```

- Warping nedeniyle **high-frequency compress**: fs/2'ye yaklaştıkça tüm
  bantlar sıkışır → cutoff'lar daima dijital düzlemde ayarlanır
- Ters warping: `Ωc = 2·fs·tan(ωc/2)` — analog tasarım parametresi
  dijitalden türetilirken kullanılır

### 5.3 Uygulama Biçimleri (C++20, zero-allocation)

| Yapı | MAC/örnek | Not | state |
|---|---|---|---|
| Direct Form 1 (DF1) | 5 mul + 4 add | giriş/çıkış ayrı delay | x[2], y[2] |
| Direct Form 2 (DF2) | 5 mul + 4 add | tek delay zinciri | s[2] |
| **Transposed DF2** | 5 mul + 4 add | kayan nokta underflow'a dayanıklı | s[2] |

**Kural (AGENTS.md §zero-allocation):** her biquad'ın 2 durum değişkeni
init'te allocate edilir; ses callback'inde `new`/`malloc` YASAK. Katsayı
güncelleme (EQ param değişimi) audio thread dışında hesaplanır, atomik
pointer/flag ile publish edilir (`[[dsp-pipeline]]` §zero-alloc).

### 5.4 Kararlılık Koşulları

```text
|z_kutup| < 1  →  kararlı
a2 > −1 ∧ a2 < 1 ∧ a1 < 1 + a2 ∧ a1 > a2 − 1   (Jury / bilinen şartlar)
```

- Kararsız biquad → genlik sonsuza gider → DAC'a NaN/inf gitmesin: §9.3
- float32 katsayı doğrulaması init ve param change anında (audio thread
  dışında) yapılır

---

## 6. FFT (Fast Fourier Transform)

| Özellik | Değer |
|---------|-------|
| Size | 512 - 8192 |
| Window | Hanning, Hamming, Blackman |
| Overlap | 50% - 75% |
| Kullanım | Frequency analysis, spectral EQ |

### 6.1 Çözünürlük

```text
Δf = fs / N
@48 kHz: N=512 → Δf = 93.75 Hz
         N=4096 → Δf = 11.72 Hz
```

- **Spektral EQ** için 11.7 Hz çözünürlük gerekir → N ≥ 4096
- **Ölçüm/scope** için 512 yeterli (gözlem, `[[measurements]]`)
- Bin frekansı: `f_k = k · fs / N`, k = 0…N−1

### 6.2 Zaman Frekansı Çarpımı (Heisenberg)

```text
Δt = N / fs        (zaman penceresi)
Δf = fs / N        (frekans çözünürlüğü)
Δt · Δf = 1        (sabit ürün)
```

N=4096 @48k: Δt = 85.3 ms → hızlı zarflar (transient) kaçırılır; N=512:
Δt = 10.7 ms (bir blok) → çözünürlük 93.75 Hz. **Seçim = ödünleşme.**

### 6.3 Konvolüsyonlu Reverb ile İlişki

FFT tabanlı konvolüsyon (overlap-add / overlap-save) impulse-response
uzunluğunu O(N log N)'e indirir → FIR §4.4 darboğazını kırar; uygulama
`[[effects]]` §5.

---

## 7. Crossover Filters

| Özellik | Değer |
|---------|-------|
| Type | Linkwitz-Riley 4th order |
| Slope | 24dB/octave |
| Crossover Point | 80Hz (varsayılan) |
| Phase | Coherent (sum flat) |

### 7.1 Linkwitz-Riley = İki Cascaded Butterworth

```text
LR-4 (24 dB/oct) = Butterworth N=2 × Butterworth N=2 (aynı fc)
LR-8 (48 dB/oct) = Butterworth N=4 × Butterworth N=4
```

- Butterworth N=2'nin faz kaybı @fc = −90°; iki birebir cascade → −180°
- **Ama** LP+HP çiftinde faz kayıpları birbirini telafi eder → LP+HP toplamı
  **0° faz, düz genlik** (allpass karakteri) → hoparlör sum'ı bozulmaz
- Butterworth N=2'nin karşılıklı kutup-sıfır yapısı bu iptali sağlar:
  HP'nin fc'deki sıfırı, LP'nin fc'deki kutup/yapısal zayıflamasını dengeler

### 7.2 Örnek Katsayılar (Bilinear, fs=48 kHz, fc=80 Hz)

⚠️ VERIFICATION REQUIRED: Aşağıdaki katsayılar bilinen RBJ formüllerinin
48 kHz/80 Hz yerine konmasıyla türetilmiştir; **repo'da doğrulayan kod
YOK** — üretim öncesi `[[measurements]]` sweep ile doğrulanmalıdır.

```text
fc = 80 Hz, fs = 48000 Hz
K = tan(π·fc/fs) = tan(π·80/48000) ≈ 0.005236
Q = 0.7071 (Butterworth N=2)

Butterworth LP (RBJ):
b0 = K²/(1+K·√2+K²) ≈ 1.3695e-05·... (normalizasyon sonrası)
→ pratikte katsayılar double'da hesaplanır, float32'ye publish edilir
```

**Kural:** katsayı üretimi **init/param-change** (control thread); ses
callback'i yalnız `y = b·x` zincirini çalıştırır.

### 7.3 3-Way ve 4-Way Allpass Gereği

- 3-way (80 Hz + 3500 Hz): orta band allpass telafisi olmadan uçlarda faz
  kayması → sum düz değildir. Allpass, LR-4 fazını %50 duty gibi değil,
  **kutup-sıfır çifti** ile telafi eder
- Detay ve ölçüm: `[[crossover]]` §faz hizalama

### 7.4 Sıralama Kuralı (EQ ↔ Crossover)

EQ **önce**, crossover **sonra** (`[[dsp-pipeline]]` §2-3). Ters sırada EQ,
bantlara ayrılmış önceki sinyali manipüle eder; crossover bölgeleri kayar.
⚠️ Bu sıralama **ADR-062'nin 9 aşaması değildir** — crossover zincirde ayrı
aşama olarak geçmez; aşamaya bindirmek = sıra değişimi = **yeni ADR**
(ADR-062 §2(b)).

---

## 8. Karşılaştırma Özeti

| Ölçüt | FIR | IIR (biquad) | FFT |
|---|---|---|---|
| Gecikme | (N−1)/2 örnek | ~< 1 örnek | N (blok) |
| Faz | doğrusal (imetrik) | non-lineer | — |
| CPU/örnek | O(N) | O(1) (5 MAC) | O(log N) + O(N) |
| Kararlılık | daima kararlı | kutup kontrolü gerekir | — |
| Zero-alloc uyumu | buffer init'te | durum init'te | plan init'te |
| Tipik kullanım | reverb/room | EQ/crossover | ölçüm, spektral |

---

## 9. Hata Modları & Koruma

### 9.1 Frequans Warping Hatası

- **Belirti:** yüksek bant cutoff'ı beklendiğinde kayar
- **Neden:** analog-tasarım parametresi fs varsayımıyla dijitalde uygulanmış
- **Düzeltme:** §5.2 ters warping; cutoff'ları dijital tanım (fs bilinerek)

### 9.2 Q ↑ → Gain Tepe Riski

Narrow peaking bantta Q > 5 iken ±12 dB (ADR-025 sınırı) bile anlık tepe
yaratır; toplam zincir headroom'u §9.3 ile korunur.

### 9.3 NaN / Inf Yayılımı

- Kaynak bozuksa (x[n] = NaN) tüm zincir NaN üretir → DAC'a gitmeden
  **clamp + finite check** (son aşamada, `[[dsp-pipeline]]` §guard)
- Biquad kararsızsa (§5.4) → watchdog: genlik eşiği aşılırsa biquad bypass
  + `⚠️ VERIFICATION REQUIRED` log bayrağı (audio thread'de yalnız atomik
  bayrak, string log YASAK)

### 9.4 Limit Cycle (Yüksek Order, float32)

- Koşul: N ≥ 6 LP + fs/4 yakını kutup + katsayı yuvarlama → sıfır girdiyle
  küçük salınım
- Önlem: transposed DF2 (§5.3), katsayı üretiminde **double** intermediate,
  DC yakınında peek filtresi bypass

### 9.5 Param-Change Click

- Biquad katsayısı anlık değişince durum (s[n]) ile yeni katsayı uyuşmaz →
  click
- Önlem: katsayı yayılımında **crossfade** (5–10 ms) veya state yeniden
  init (rampeli) — audio thread'de atomik çift-katsayı seti (A/B)

---

## 10. Ölçüm Protokolü (Kabul)

| # | Test | Yöntem | Kabul |
|---|---|---|---|
| 1 | HPF/LPF sweep | sine 20 Hz–20 kHz, log sweep | ripple ≤ ±0.5 dB, stopband ≤ hedef |
| 2 | Butterworth @fc | tek ton fc | −3.01 ±0.3 dB |
| 3 | LR sum (LP+HP) | paralel giriş, toplam çıkış | faz ≈ 0°, genlik düz (bkz §7.1) |
| 4 | FIR grup gecikmesi | impulse → tepe konumu | (N−1)/2 ± 1 örnek |
| 5 | Kararlılık soak | sıfır/DC girdi 60 dk | salınım yok, xrun 0 |
| 6 | Param-change | EQ kaydırma canlı | click yok (THD spuru ≤ −80 dB) |

Araç: `[[measurements]]` (sweep/THD) · Donanım DAC çıkışı PCM3168A (ADR-038).

---

## 11. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-017-dsp-hardware-mode]] | DSP hardware (512 / 48 kHz / float32) |
| [[ADR-025-professional-eq-system]] | Çift EQ (31-band grafik + ≥2 parametrik, frozen YOK) |
| ADR-038 H001 | PCM5122 yasak → PCM3168A |
| ADR-062 | Pipeline 9 aşama (crossover ayrı aşama değil — `dsp-pipeline` §2) |

---

## 12. Çapraz Referanslar

- `[[electronic/dsp/index]]` · `[[dsp-pipeline]]` · `[[crossover]]` ·
  `[[equalizer]]` · `[[effects]]` · `[[measurements]]`
- `[[../hardware/index|hardware]]` (PCM3168A) · `[[../amplifier/index|amplifier]]`

---

## 13. Derinleştirme: Katsayı ve Pencere Tabloları (2026-10-13)

> §2–§7'nin formüllerini sabit tablolarla pekiştirir. Repo'da filtre kodu YOK
> (PLANNED) — doğrulama §10 kapısındadır.

### 13.1 Butterworth 2. Derece Katsayıları (Q Değerleri)

Normalize analog kesim polinomu `s² + α·s + 1` — α = 1/Q:

| Derece | α (kaskat LP) | Not |
|---|---|---|
| 2 (BW-2) | √2 ≈ 1.4142 | −3 dB @fc, tek kademe |
| 4 (BW-4) | 2. kök: α₁ = 1.0824, α₂ = 1.9319 (kaskad BW-2² değil — **kutup açıları 22.5°/67.5°**) | sum'da tepe → crossover **kullanmaz** (§7.1) |
| 6 | α = 0.5176, 1.4142, 1.9319 (kaskad BW-2³) | — |

- **LR-4 = BW-2 kaskadı²** (α = √2 ×2): §7.1'in kaskad dayanağı; BW-4 ile
  **karıştırılmaz** (crossover §7/1 FMEA'sı).
- Q'lu biquad üretimi §5 (RBJ); α sin(w0)/(2Q) ile bağlıdır — §11.2'deki
  sayısal örnek bu bağı gösterir.

### 13.2 Bilinear Warping — Sayısal Örnek (§5.2)

Dijital karşılık: `f_analog → f_dijital = (fs/π) · arctan( (π·f_analog)/fs )`:

| fs | f_analog | f_dijital | Sapma |
|---|---|---|---|
| 48000 | 1000 Hz | ≈ 1000.1 Hz | ≈ %0.01 — ihmal |
| 48000 | 10000 Hz | ≈ 10772 Hz | **≈ +7.7%** — gerçek kayma |
| 48000 | 20000 Hz | ≈ 24000 (Nyquist'e oturur) | cutoff'a asla ulaşılmaz |

- **Kural:** cutoff'lar analog-tasarım olarak verilmişse ters warping ile
  ön-düzeltme (§5.2) veya dijital tanım (fs bilinerek) — §9.1 hata modu bu
  sayısal sapmadan doğar.
- fs = 96000'te 10 kHz sapması ≈ %1.9'a düşer (fs ↑ → warp ↓) — fs değişimi
  katsayı üretimini **zorunlu** kılar (crossover §7/6).

### 13.3 Pencere Karşılaştırması (§4)

| Pencere | Mainlobe (oktav) | Yan yaprak | Not |
|---|---|---|---|
| Rectangular | ≈ 0.86 | −13 dB (ilk) | en dar lobe, en kötü sızıntı |
| Hann | ≈ 1.71 | −31 dB | genel amaçlı |
| Hamming | ≈ 1.36 | −43 dB | yan yaprak bastırma |
| Blackman | ≈ 2.02 | −58 dB | en sakin, en geniş lobe |

- FIR crossover tasarımında tercih: sızıntı → **linear faz** avantajıyla
  takas edilir; tap sayısı arttıkça lobe daralır (grup gecikmesi (N−1)/2
  büyür — §4/§8 dengesi).
- Bu tablo **literatür tipik değerleridir**; kesin yan-yaprak deseni
  ölçümlerle doğrulanır (§10/4) — "ölçüldü" denmez.

### 13.4 Ölçüm Veri Şablonu (Kayıt Zorunlu)

| Alan | Değer (boş bırakılmaz) |
|---|---|
| Tarih / fs | YYYY-MM-DD / 48000 |
| Sıra | BW / LR / allpass var-yok |
| Ripple (sweep) | dB ≤ ±0.5 (§10/1) |
| −3 dB kontrolü | 3.01 ± 0.3 (§10/2) |
| FIR GD | (N−1)/2 ± 1 örnek (§10/4) |
| Kararlılık soak | 60 dk, salınım yok (§10/5) |
| xrun | 0 / sayı |

### 13.5 ADR Çapraz Referans Özeti

| Kural | Kaynak | Bu bölümdeki karşılık |
|---|---|---|
| 512/48k/float32 + zero-alloc | [[ADR-017-dsp-hardware-mode]] | §4/§5 init kuralları |
| EQ kaskad DF2T/FTZ | [[ADR-025-professional-eq-system]] §2.2b/d | §5, §11.2 |
| 9 aşama (EQ 3→4, crossover aşama değil) | ADR-062 | §7.4 |
| PCM5122 yasak → PCM3168A | ADR-038 H001 | §10 |

---

## 14. Tasarım ve Uygulama Kontrol Listesi (Uygulama Öncesi)

Her filtre bloğu kodlanmadan önce aşağıdaki adımlar sırayla tamamlanır; adımlardan atlanan varsa blok **kabul edilmez**.

| # | Adım | Referans | Çıktı (kayıt zorunlu) |
|---|------|----------|------------------------|
| 1 | fs ve blok boyutunu sabitle (48 kHz / 512 örnek) | §4 · [[ADR-017-dsp-hardware-mode]] | init bütçesi |
| 2 | Topoloji seç (biquad DF2T, FIR pencereleme) | §5, §13.3 | kaskad düzeni |
| 3 | Katsayıları hesapla (Q, cutoff, shelf) | §5 · [[ADR-025-professional-eq-system]] | katsayı tablosu (init'te) |
| 4 | Ölçeklendirme/FTZ kontrolü (denormals) | §5 · [[ADR-025-professional-eq-system]] §2.2b | test çıktısı |
| 5 | Frekans yanıtı üret (sweep 20 Hz–20 kHz) | §10/1 | ripple dB |
| 6 | −3 dB noktası ve faz/grup gecikmesi doğrula | §10/2, §10/4 | ölçülmüş değer |
| 7 | Kararlılık soak (60 dk, salınım yok) | §10/5 | soak kaydı |
| 8 | xrun = 0 ve callback bütçesi ≤ %80 | [[electronic/dsp/index]] §6 | soak raporu |

### 14.1 Sık Yapılan Tasarım Hataları

| # | Hata | Sonuç | Önlem |
|---|------|-------|-------|
| 1 | Katsayıların runtime'da hesaplanması | Audio callback'inde tahsis → kural 1 ihlali | Tüm katsayılar init'te (§4/§5) |
| 2 | Denormal değerlerin göz ardı edilmesi | CPU spike, callback bütçesi aşımı | FTZ/DAZ etkinleştir (§5) |
| 3 | Cutoff'un fs/2'ye çok yakın seçilmesi | Kararsız biquad, ripple aşımı | Nyquist'ten ≥ %40 uzak (§5) |
| 4 | FIR tap sayısının ölçüsüz artırılması | Gecikme bütçesi ihlali | (N−1)/2 grup gecikmesi §10/4 ile ölç |
| 5 | Pencere seçiminin ölçimsiz yapılması | Yan yaprak sızıntısı beklenenden kötü | §13.3 tablosu + §10/4 ölçümü |
| 6 | Ölçüm kaydı yapılmadan "uygun" kararı | Tekrarlanamayan iddia | §13.4 şablonu doldurulmadan kabul yok |

**Not:** Bu bölümdeki toleranslar (ripple ≤ ±0.5 dB · −3 dB = 3.01 ± 0.3 · xrun 0) mevcut §10 ölçüm kapılarıyla **aynı** değerlerdir; yeni tolerans **tanımlanmaz**, yalnız mevcut kapılar listelenir. Ölçümü olmayan satır `⚠️ VERIFICATION REQUIRED` işaretlenir.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
