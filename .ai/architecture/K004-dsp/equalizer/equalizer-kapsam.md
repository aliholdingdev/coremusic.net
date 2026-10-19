---
title: "equalizer — Kapsam"
type: architecture-scope
node_id: K004-dsp/equalizer
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# equalizer — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`dsp-master.md` §3 satır 2 bu dalı **"Frekans bantlarına göre seviye ayarı"**
olarak yazar. Basitçe: sesi frekans bantlarına bölüp her bandı ayrı ayrı
yükseltip/alçaltma — "bass'ı aç, tizi kıs". `index.md` bunu "sesin rengini
değiştiren ana araç" diye tarif eder. EQ tek tek bandın kazancını yönetir;
bantların **içindeki** filtre matematiği filters dalının işidir.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Bant sayısı ve bant frekans yerleşimi (ör. 31-band ISO) | Bant başına biquad/filtre çekirdeği | filters |
| 2 | Bant kazancı (gain) ve Q (genişlik) yönetimi | Filtre topolojisi (Sallen-Key, ladder) | filters |
| 3 | EQ eğrisinin preset'ten yüklenmesi | Preset UI'ı ve seçimi | uygulama katmanı |
| 4 | EQ'nun pipeline'daki yeri (sıra kararı) | Zincirin sahibi olmak | pipeline |
| 5 | Parametrik / grafik / shelving EQ tipi seçimi | Ölçümlü oda düzeltmesi hedefi | measurement |
| — | *(dışında)* bant sayısı/Q varsayılan değerleri | — | **UNKNOWN — R1** |

**Yasak:** filters ile sınır (bant Kazancı EQ'nun, filtre çekirdeği filters'ın);
çakışma olursa sessizce çözülmez, üst kararı (Q3) bekler.

## §3 Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | equalizer ↔ filters sınırı (bant seviyesi ↔ filtre çekirdeği) | ⚠️ VERIFICATION REQUIRED | dsp-master §7 Q3 (komşu) |
| A2 | EQ ↔ measurement (oda düzeltme hedefi kimin?) | **UNKNOWN** | eşik/ölçüm ayrımı Q4 |
| A3 | bant sayısı/Q varsayılanları | **UNKNOWN** — tahmin yasak | equalizer/index.md open_questions |
| A4 | parametrik vs grafik vs shelting tipi | **UNKNOWN** | — |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu / URL | Durum |
|---|---|---|---|
| 1 | Çocuk rolü = "Frekans bantlarına göre seviye ayarı" | `dsp-master.md` §3 satır 2 | ✅ dosya okundu |
| 2 | "bant sayısı/eşik değerleri tanımlı değil" açık soru | `equalizer/index.md` open_questions | ✅ dosya okundu |
| 3 | CoreMusic PHP katmanında 31-band EQ eğrisi üreten bir fonksiyon var | `shared/src/AI/AIEngine.php` satır 117–133 (optimizeEQ) | ✅ dosya okundu |
| 4 | Aynı dosyada 31-band ISO frekans tablosu (20 Hz–20 kHz) | `shared/src/AI/AIEngine.php` satır 334–339 (bandToFrequency) | ✅ dosya okundu |
| 5 | DSPark `Equalizer` + `setBand(index, freq, gain)` sunuyor | https://github.com/CristianMoresi/DSPark | ✅ 2026-10-12 doğrulandı |
| 6 | Patina `EqEngine` (3-band parametric EQ) sunuyor | https://github.com/ShmKnd/Patina | ✅ 2026-10-12 doğrulandı |
| 7 | JUCE `modules/juce_dsp` modülü EQ filtreleri içerir (sınıf adı doğrulanmadı) | https://github.com/juce-framework/JUCE/tree/master/modules | ⚠️ — modül var, içerik UNKNOWN |

> Not: satır 3–4'teki PHP EQ, K004 motorunun kendisi değildir — bir AI/room-correction
> yardımcı hesaplamasıdır; C++ DSP motoru henüz yok (pipeline-ornekler §1).

## §5 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | Varsayılan bant sayısı/Q/eşik değerleri? | **UNKNOWN** |
| R2 | EQ ↔ filters sınırı hangi ölçütle? | ⚠️ VERIFICATION REQUIRED |
| R3 | Oda düzeltme hedefi EQ'da mı measurement'da mı? | ⚠️ VERIFICATION REQUIRED |

**Kapanış:** status: draft kalır. §3'teki UNKNOWN'lar kapanmadan bu dosyada EQ
parametre iddiası bağlayıcı sayılmaz (v2 §3 KURAL 3).

---

## §6 Teknik derinlik: EQ çekirdek matematiği (RBJ referansı)

> Bu bölüm tasarım referansıdır; CoreMusic'te henüz EQ biquad kodu YOKTUR
> (§4/§5 — `shared/src/` taraması negatif). Formüller literatürden alınmıştır,
> kaynak doğrulaması §11'dedir.

### §6.1 Biquad transfer fonksiyonu ve fark denklemi

Z düzleminde ikinci derece (biquad) filtre:

```
        b0 + b1·z⁻¹ + b2·z⁻²
H(z) = ─────────────────────────
        1  + a1·z⁻¹ + a2·z⁻²
```

Birim örnekleme uzayında fark denklemi (Direct Form I):

```
y[n] = b0·x[n] + b1·x[n-1] + b2·x[n-2] - a1·y[n-1] - a2·y[n-2]
```

Katsayılar normalize edilir (a0'a bölünür) → a0 = 1. Neden önemli:

- Her EQ bandı = 1 biquad; 31 band = 31 biquad = 62 kutup (2'ser).
- Zincirleme (cascade) sırası matematiksel olarak değişmez (LTI), ama float32
  (ADR-017) katsayı yuvarlaması nedeniyle sıra ≠0 etkisizdir: sinyal-zarflı
  gürültü (quantization noise) toplamı değişir → sıra kararı test edilir
  (⚠️ CoreMusic sıra tercihi **UNKNOWN** — R2/Q3 ile bağlı).

### §6.2 Peaking EQ (tepe) katsayıları — RBJ Audio EQ Cookbook

Girdiler: `f0` (tepe frekansı, Hz), `Q`, `Gain` (dB), `fs` (örnekleme).

```
A     = 10^(Gain/40)              ← genlik oranı (dB/40 = yarı-log)
w0    = 2π·f0/fs                  ← normalize açısal frekans
alpha = sin(w0)/(2·Q)             ← bant genişliği terimi

b0 =  1 + alpha·A        b1 = -2·cos(w0)       b2 =  1 - alpha·A
a0 =  1 + alpha/A        a1 = -2·cos(w0)       a2 =  1 - alpha/A
→ tüm katsayılar a0'a bölünür
```

Kontrol noktaları (doğrulama testleri için):

| Nokta | Beklenen |
|---|---|
| f = f0 | |H| = A (yani Gain dB) |
| f → 0 ve f → fs/2 | |H| → 1 (0 dB — tepe EQ uçlarda düz) |
| Gain = 0 dB | H(z) = 1 (bypass, katsayılar ±0'a oturur) |
| Q ↑ | tepe daralır, alan (dB·oktav) yaklaşık sabit kalır |

### §6.3 Shelving katsayıları (low-shelf / high-shelf)

```
alpha = sin(w0)/2 · sqrt( (A + 1/A)·(1/S - 1) + 2 )   ; S = shelf slope

Low-shelf:
b0 =    A·[(A+1) - (A-1)·cos(w0) + 2·sqrt(A)·alpha]
b1 =  2·A·[(A-1) - (A+1)·cos(w0)]
b2 =    A·[(A+1) - (A-1)·cos(w0) - 2·sqrt(A)·alpha]
a0 =        (A+1) + (A-1)·cos(w0) + 2·sqrt(A)·alpha
a1 =   -2·[(A-1) + (A+1)·cos(w0)]
a2 =        (A+1) + (A-1)·cos(w0) - 2·sqrt(A)·alpha
```

High-shelf: `+` işaretli terimler `(A-1)` ile yer değiştirir (kosinüs terimi
değişir). Shelf slope `S` ile Q ilişkisi: `S = 1` en dik izinli shelf
(RBJ tanımı); `S > 1` aşırı-uyarım (peak/ripple) üretir → **S ≤ 1** tutulur.

### §6.4 Alçak/yüksek geçiren ve notch

```
Lowpass:  b0 = (1-cos w0)/2,  b1 = 1-cos w0,  b2 = (1-cos w0)/2
          a0 = 1+alpha,       a1 = -2·cos w0, a2 = 1-alpha

Highpass: b0 = (1+cos w0)/2,  b1 = -(1+cos w0), b2 = (1+cos w0)/2
          a0 = 1+alpha,       a1 = -2·cos w0,   a2 = 1-alpha

Notch:    b0 =  1,  b1 = -2·cos w0,  b2 =  1
          a0 = 1+alpha, a1 = -2·cos w0, a2 = 1-alpha
```

`alpha` için Q veya bandwidth (BW, oktav) yazımı:

```
Q formu:        alpha = sin(w0)/(2·Q)
bandwidth formu: alpha = sin(w0) · sinh( ln2/2 · BW · w0/sin(w0) )
```

### §6.5 Q ↔ bant genişliği ↔ tepe genişliği ilişkileri

| İlişki | İfade | Not |
|---|---|---|
| Bant genişliği (yaklaşık, yüksek Q) | BW[Hz] ≈ f0/Q | dar-Q için kabul edilebilir |
| Bant genişliği (açık, genel) | BW[oktav] = (2/π)·arctan(1/(2Q))... ⚠️ | genel kapalı form **VERIFICATION REQUIRED** |
| -3 dB bant (2. derece LPF) | f⁻³ᵈᴮ = f_c · sqrt(2^(1/2) - 1) ≈ 0.6436·f_c | Butterworth (n=2) |
| Butterworth Q (2. derece) | Q = 1/√2 ≈ 0.7071 | maximally-flat |
| Linkwitz-Riley (kütük) | 2× Butterworth kaskad → Q_LR = 0.7071 × 2 kaskad | crossover dalının konusu: [[crossover-kapsam]] (varsa) / ../crossover/ |
| Tepe EQ'da Q daraldıkça | kazanç sabit, bant daralır | §6.2 kontrol tablosu |

> ⚠️ "BW genel kapalı formu" satırındaki formül doğrulanmadı; yalnız Q ≈
> f0/BW(hepsi f0'da) ve Butterworth sabitleri (dizinde doğrulanmış kaynaklara
> bakılacaktır) kullanılır.

### §6.6 Faz tepkisi ve grup gecikmesi

```
Faz:            φ(ω) = ∠H(e^{jω})
Grup gecikmesi: τg(ω) = -dφ/dω   [radyan/sn → saniye: τg = -(1/2π)·dφ/df]
```

Minimum-phase biquad'larda faz kayması kaçınılmazdır:

- Tepe EQ, tepe frekansında fazı sıfırdan geçirir; yan bantlarda ±kadar
  kayma üretir (Q arttıkça kayma artar).
- Grup gecikmesi f0 yakınında tepe yapar → yüksek-Q dar bant, dar
  frekans aralığında grup-gecikme distorsiyonu (smearing) yaratır.
- Ölçüm: biquad'ın faz tepkisi, katsayılardan analitik olarak hesaplanır
  (z- dönüşümü); doğrulama testi §9'da.

Toplam zincir gecikmesi pipeline'ın latency bütçesine yazılır
([[pipeline-kapsam]] §3/A4 — T8 **UNKNOWN**, sayı uydurulmaz).

### §6.7 Minimum-phase vs linear-phase EQ (seçim kriterleri)

| Kriter | Minimum-phase (biquad kaskad) | Linear-phase (FIR) |
|---|---|---|
| Ek gecikme | yok (katsayı-warm, blok gecikmesi kadar) | (N-1)/2 örnek — örn. 513 tap → 256 örnek ≈ 5.33 ms @48 kHz |
| Faz | kayar (minimum-phase) | sıfır faz (genlik yalnız) |
| Bedeli | faz kayması | RAM (tap), kuyruk (tail), boost'ta pre-ring |
| Realtime uyum | ✅ native (sıfır tahsis kuralıyla) | ⚠️ FIR konvolüsyon blok başına hesaplanmalı |
| CoreMusic durumu | **UNKNOWN** (A4 — tercih belirsiz) | **UNKNOWN** |

**A4 bu dosyada çözülmez** (§3/§5 — tahmin yasak). Tablo yalnız kriter
setidir; tercih kararı tasarım aşamasında ADR ile alınır.

### §6.8 Float32 (ADR-017) altında katsayı doğrulama testleri

| # | Test | Kabul | Durum |
|---|---|---|---|
| 1 | Kararlılık: kökler \|z\| < 1 | her fs ve parametre taramasında | tasarım testi — PLANNED |
| 2 | DC kazancı: H(1) = beklenen dB | ±0.05 dB (float32) | ⚠️ eşik değeri = tasarım kararı |
| 3 | Nyquist kazancı: H(-1) | peaking/shelf'te 0 dB | tasarımsal |
| 4 | Bypass: Gain=0 → \|H\|-1 ≈ 0 | ≤ 1e-6 mutlak | tasarımsal |
| 5 | Denormal: katsayılar + durum | flush-to-zero etkinse sorun yok | runtime ortamına bağlı ⚠️ |
| 6 | Blok-boyutu bağımsızlığı (1, 64, 512 örnek) | bit-birebir aynı çıkış | realtime sözleşmesi (DSPark prepare/processBlock deseni) |

## §7 Bant yerleşimi: 31 bantlık 1/3 oktav serisi

Kod kanıtı: `shared/src/AI/AIEngine.php` `bandToFrequency()` 31 band döndürür
(§4/4). Standart literatür serisi (1/3 oktav nominal merkez frekansları):

```
20   25   31.5 40   50   63   80   100
125  160  200  250  315  400  500  630
800  1000 1250 1600 2000 2500 3150 4000
5000 6300 8000 10000 12500 16000 20000     → 31 bant
```

- Her bandın alt/üst kenarı: `f_merkez · 2^(±1/6)` (1/3 oktav genişlik).
- Seri 20 Hz–20 kHz'i kapsar; 20 kHz bandı Nyquist'in (24 kHz @48 kHz)
  altındadır → biquad tasarımı fs=48 kHz için güvenlidir.
- Seri kaynağı: 1/3 oktav band tanımı (§11 kaynak tablosu — Octave band
  sayfası HEAD 200). Serinin standart numarası (IEC 61260 vb.) ⚠️ **VERIFICATION
  REQUIRED** — standart metni bu turda okunmadı.
- `optimizeEQ()` (AIEngine satır 111–133) bu frekanslarda PHP eğrisi üretir;
  bu eğri K004'ün EQ'su değildir (§4 not).

## §8 Worked example: 1 kHz, +6 dB, Q = 1.0 peaking @ fs = 48 kHz

Adım-adım (literatür formülü §6.2, sayılar bu dosyada hesaplandı):

```
w0    = 2π·1000/48000        = 0.1308997
cos w0= 0.9914449            sin w0 = 0.1305262
A     = 10^(6/40)            = 1.4125375
alpha = sin w0/(2·1.0)       = 0.0652631

b0 = 1 + alpha·A     = 1.092184     a0 = 1 + alpha/A = 1.046195
b1 = -2·cos w0       = -1.9828897   a1 = -2·cos w0   = -1.9828897
b2 = 1 - alpha·A     = 0.907816     a2 = 1 - alpha/A = 0.953805

normalize (/a0):
b0' = 1.043960   b1' = -1.9828897/1.046195 = -1.895337   b2' = 0.867735
a1' = -1.895337   a2' = 0.911693
```

Kontrol (§6.2 tablosu): f = 1000 Hz'de |H| ≈ 10^(6/20) ≈ 1.9953 (+6 dB) ·
f → 0 ve 24 kHz'te |H| → 1. Sayılar yaklaşık (6 hane); doğrulama testi
float32 toleransında çalışır (§6.8/2: ±0.05 dB — eşik **tasarım kararı**).

## §9 Realtime uygulama kısıtları (bağlayıcı kurallar)

| Kural | EQ'ya yansıması | Kaynak |
|---|---|---|
| Sıfır tahsis (audio thread'de malloc/new yok) | katsayı ve durum dizileri `prepare` anında allocate; runtime'da yalnız okuma/yazma | AGENTS.md §1 (embedded kuralları) |
| Lock-free | preset geçişi atomik yayın (ör. çift-tampon/atomic pointer); EQ katsayıları blok sınırında takas | aynı |
| `noexcept` callback | katsayı hesaplaması blok içinde değil, parametre değişikliğinde (non-RT thread) yapılır | aynı |
| `alignas(64)` | biquad durum dizileri cache hattı hizalı (31 bant × {s1,s2}) | aynı |
| float32 veri (ADR-017) | katsayılar float32; doğruluk yeterliliği §6.8 testleriyle kanıtlanır | ADR-017 |
| 512 örnek / 48 kHz (sartname) | blok işleme birimi; EQ blok-boyutundan bağımsız olmalı (§6.8/6) | sartname — ölçüm PLANNED |

## §10 Komşu dal etkileşimi (sınır → teknik sonuç)

| Komşu | Kesişen teknik soru | Bu dosyadaki durum |
|---|---|---|
| filters | biquad çekirdeği kimin? (A1/R2) | ⚠️ VERIFICATION REQUIRED — EQ bant seviyesi, filters çekirdek |
| pipeline | EQ'nun sırası (giriş-gain sonrası mı, dynamics sonrası mı?) | ⚠️ [TASARIM] — Q3 bağlı |
| measurement | düzeltme hedefi (EQ eğrisi) kimden gelir? (A2/R3) | ⚠️ VERIFICATION REQUIRED — Q4 |
| crossover | shelving/LPF ile crossover LPF'ü aynı matematiği paylaşır mı? | **UNKNOWN** — crossover dalı kardeş |
| K003 output | EQ sonrası sinyal K003'ün zincirine girer | `../../K003-audio/output/output-kapsam.md` |
| K001 çip | nihai çıkış PCM3168A/AK4458'e gider — EQ burada **DSP alanındadır**, DAC filter'inde değil | `../../K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec.md` · `ak4458-dac.md` |

## §11 Kaynaklar (tur-2 doğrulama — 2026-10-13, `Invoke-WebRequest -Method Head`)

| # | Kaynak | URL | HEAD | Kullanım |
|---|---|---|---|---|
| 1 | Robert Bristow-Johnson — Audio EQ Cookbook (W3C host) | https://www.w3.org/2011/audio/audio-eq-cookbook.html | ✅ 200 | §6.2–§6.4 katsayı formülleri |
| 2 | The Scientist and Engineer's Guide to DSP (D. Smith) | https://www.dspguide.com/ | ✅ 200 | §6.1 biquad/IIR arka planı |
| 3 | Room EQ Wizard (ölçüm aracı) | https://www.roomeqwizard.com/ | ✅ 200 | §10 measurement komşusu (aday araç) |
| 4 | Julius O. Smith III — CCRMA filters kitabı | https://ccrma.stanford.edu/~jos/filters/ | ✅ 200 | §6.6 faz/grup gecikmesi arka planı |
| 5 | Wikipedia — Biquad filter | https://en.wikipedia.org/wiki/Biquad_filter | ✅ 200 | §6.1 çapraz kontrol |
| 6 | Wikipedia — Butterworth filter | https://en.wikipedia.org/wiki/Butterworth_filter | ✅ 200 | §6.5 Butterworth Q |
| 7 | Wikipedia — Octave band | https://en.wikipedia.org/wiki/Octave_band | ✅ 200 | §7 1/3 oktav serisi |

Aday (doğrulandı, bu bölümde kullanılmadı): https://www.dsprelated.com/ ✅200 ·
https://www.musicdsp.org/ ✅200 · https://www.juce.com/ ✅200 (JUCE içerik-sınıf
iddiaları ayrı turda — `juce_dsp` içerik kanıtı **UNKNOWN**, bkz §4/7).

## §12 Wiki-link köprüsü

| Hedef | Bağlantı |
|---|---|
| K004 kök — ana plan | [[dsp-master]] |
| K004 kök — bileşenler | [[dsp-bilesenler]] |
| K004 kök — doğrulama | [[dsp-dogrulama]] |
| K004 kök — bağımlılıklar | [[dsp-bagimliliklar]] |
| Kardeş dal — filtre çekirdeği | `filters/filters-kapsam.md` |
| Kardeş dal — sıralama | `pipeline/pipeline-kapsam.md` |
| Kardeş dal — ölçüm | `measurement/measurement-kapsam.md` |
| K003 çıkış zinciri | `../../K003-audio/output/output-kapsam.md` |
| K001 DAC çipi | `../../K001-donanım/donanaım-type/audio/cipler/ak4458-dac.md` |
| K002 analog/devre | `../../K002-electroncis/devre-ve-analog/devre-ve-analog-anayasa.md` |

---

## §13 EQ tipi tam tablosu (kapsam içi referans)

| # | EQ tipi | Biquad? | Tipik kullanım | Karşılık (RBJ) | CoreMusic durumu |
|---|---|---|---|---|---|
| 1 | Parametrik peaking | ✅ tek biquad | dar düzeltme (yanıp-sönen frekans) | `peakingEQ` | **UNKNOWN** (R1/R2) |
| 2 | Grafik EQ | ✅ bant başına sabit-Q peaking | kullanıcı kumandası (31 bant) | `peakingEQ` ×31 | PHP eğrisi var (§4/3–4), motor YOK |
| 3 | Low-shelf | ✅ tek biquad | bass genel seviyesi | `lowShelf` | **UNKNOWN** |
| 4 | High-shelf | ✅ tek biquad | tiz genel seviyesi | `highShelf` | **UNKNOWN** |
| 5 | Alçak geçiren (LPF) | ✅ | anti-alias / tiz kırma | `lowPass` | filters ile sınır (A1) |
| 6 | Yüksek geçiren (HPF) | ✅ | DC / sub-sonik temizliği | `highPass` | filters ile sınır (A1) |
| 7 | Bant geçiren (BPF) | ✅ | tek bant izolasyon | `bandPass` | **UNKNOWN** |
| 8 | Notch (bant eleden) | ✅ | spesifik gürültü (ör. 50/60 Hz) | `notch` | **UNKNOWN** |
| 9 | All-pass | ✅ | faz şekillendirme (crossover fazı) | `allPass` | crossover komşusu |
| 10 | Tilt EQ | ✅ (shelf kombinasyonu) | genel eğim dengesi | shelf bileşimi | **UNKNOWN** |
| 11 | Linear-phase EQ | FIR (biquad değil) | faz nötr düzeltme | — (FIR) | **UNKNOWN** (§6.7) |
| 12 | Dynamic EQ | peaking + zarf | frekans-bağımlı dinamik | — | dynamics dalı sınırı |

**Sınır notu:** 5–9 numaralı tipler `filters` dalının malzemesidir (A1/R2);
bunların EQ'ya ait olup olmadığı Q3 kararıdır. 12 numara dynamics dalıyla
(`../dynamics/dynamics-kapsam.md`) kesişir.

## §14 Shelf worked example: low-shelf, f0 = 100 Hz, +4 dB, S = 1.0 @ 48 kHz

§6.3 formülünün adım-adım değerlendirmesi (bu dosyada hesaplandı; yaklaşık):

```
A     = 10^(4/40)               = 1.2589254
w0    = 2π·100/48000            = 0.01308997
cos w0= 0.9999143        sin w0 = 0.01308971
S     = 1.0  →  alpha = sin(w0)/2 · sqrt( (A+1/A)·(1/S - 1) + 2 )
       (1/S - 1) = 0  →  sqrt term = sqrt(2) = 1.4142136
alpha = 0.01308971/2 · 1.4142136 = 0.00654486 · 1.4142136 = 0.00925522

b0 = A·[(A+1) - (A-1)·cos w0 + 2·sqrt(A)·alpha]
   = 1.2589254·[2.2589254 - 0.2589254·0.9999143 + 2·1.1220183·0.00925522]
   = 1.2589254·[2.2589254 - 0.2589034 + 0.0207763]
   = 1.2589254 · 2.0207983 = 2.5440
b1 = 2A·[(A-1) - (A+1)·cos w0]
   = 2·1.2589254·[0.2589254 - 2.2589254·0.9999143]
   = 2.5178508·[0.2589254 - 2.2587311] = 2.5178508·(-1.9998057) = -5.0352
b2 = A·[(A+1) - (A-1)·cos w0 - 2·sqrt(A)·alpha]
   = 1.2589254·[2.2589254 - 0.2589034 - 0.0207763] = 1.2589254 · 1.9792457 = 2.4918
a0 = (A+1) + (A-1)·cos w0 + 2·sqrt(A)·alpha
   = 2.2589254 + 0.2589034 + 0.0207763 = 2.5386
a1 = -2·[(A-1) + (A+1)·cos w0] = -2·[0.2589254 + 2.2587311] = -5.0353
a2 = (A+1) + (A-1)·cos w0 - 2·sqrt(A)·alpha = 2.2589254 + 0.2589034 - 0.0207763 = 2.4971

normalize (/a0 ≈ 2.5386):
b0' ≈ 1.0021   b1' ≈ -1.9834   b2' ≈ 0.9816
a1' ≈ -1.9834   a2' ≈ 0.9837
```

**Kontrol:** f → 0'da H(1) ≈ 10^(4/20) ≈ 1.5849 (+4 dB shelf) ·
f → fs/2'de H(-1) ≈ 1. · Kararlılık: a2' ≈ 0.9837 < 1 → kökler birim çember
içinde (yaklaşık kontrol; tam kök testi §6.8/1).

> Sayılar 4 haneli yaklaşıktır; doğrulama testi gerçek katsayıları float64'te
> üretip float32'ye yuvarlar (ADR-017) ve §6.8 toleranslarıyla karşılaştırır.

## §15 Ölçüm–doğrulama senaryoları (EQ implementasyonu için)

| # | Senaryo | Girdi | Beklenen | Eşik | Durum |
|---|---|---|---|---|---|
| T1 | DC kazancı | sabit 1.0 (DC) | H(1) = shelf hedefi | ±0.05 dB | PLANNED |
| T2 | Nyquist | (-1)^n | peaking/shelf: 0 dB | ±0.05 dB | PLANNED |
| T3 | Peaking tepe | swept sinus f0±10% | +G dB | ±0.1 dB | PLANNED |
| T4 | Bypass | Gain=0 | \|H-1\| ≈ 0 | ≤1e-6 | PLANNED |
| T5 | Blok-bağımsızlık | blok 1/64/512 | bit-birebir | = | PLANNED |
| T6 | Sıra-değişimi | bant 1↔2 swap | genlik toplamı eşit (±tol) | ±0.01 dB | PLANNED |
| T7 | Kararlılık taraması | fs × f0 × Q × Gain ızgarası | \|kök\|<1 | <1 | PLANNED |
| T8 | Denormal | 20 Hz kuyruk testi | durum sıfırlanır/FTZ | CPU patlaması yok | PLANNED |
| T9 | Preset geçişi | anlık katsayı takası | pop yok (crossfade) | işitsel kanıt | PLANNED |
| T10 | 31-band toplam | ±6 dB jenerik eğri | toplam genlik ≤ 36 dB (kazanç bütçesi) | ⚠️ bütçe = tasarım | **UNKNOWN** |
| T11 | Faz/grup-gecikme | analitik vs ölçüm | örtüşme | ±5° ⚠️ eşik | PLANNED |
| T12 | float32 yuvarlama | double→float katsayı | genlik sapması | ±0.01 dB ⚠️ | PLANNED |

**Not:** T7/T8/T10 eşikleri sayısal değer içerir; bunlar literatür
başlangıç noktasıdır, **bağlayıcı değildir** — ADR ile sabitlenir (R1 ile
birlikte).

## §16 Anti-pattern tablosu (realtime EQ hataları)

| # | Anti-pattern | Sonuç | Doğrusu |
|---|---|---|---|
| 1 | Katsayı hesabını audio callback içinde `pow/log` ile yapmak | jitter, tahsis riski | hazırlık anında hesapla, blokta yalnız uygula |
| 2 | `std::vector` push_back ile bant ekleme | tahsis (kural ihlali) | prepare'da sabit dizi (alignas(64)) |
| 3 | Katsayıları her örnek için yeniden normalize | CPU israfı, drift | blok sınırında tek normalize |
| 4 | Preset değişiminde sert katsayı takası | click/pop | katsayı crossfade (5–20 ms ⚠️ süre = tasarım) |
| 5 | float64 durum + float32 katsayı karışımı | hizalama/sarıf belirsizliği | ADR-017: tek tip float32 zinciri |
| 6 | Nullptr/nan katsayı kontrolü yok | NaN tüm zincire yayılır | katsayı doğrulama + fail-safe (sıfırla) |
| 7 | DC offset'i EQ HPF'siz geçirmek | donanım koruması riski | K001 koruma sınırı: `../../K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec.md` (donanım katmanı ayrı) |
| 8 | Her blok `reset()` | filtre hiç oturmaz | yalnız mod değişiminde reset |

## §17 Terim sözlüğü (bu dosyaya özgü)

| Terim | Tanım |
|---|---|
| **biquad** | 2. derece IIR filtre (4 normalize katsayı) |
| **RBJ Cookbook** | Robert Bristow-Johnson'ın biquad katsayı formülü seti (§11/1) |
| **Gain (dB)** | tepe/kazanç farkı; A = 10^(G/40) genlik oranı |
| **Q** | kalite faktörü; daraldıkça bant daralır |
| **BW** | bant genişliği (oktav veya Hz) |
| **shelf slope (S)** | shelf'in dikliği; S ≤ 1 güvenli bölge |
| **min-phase** | en küçük faz kayması sağlayan fiziksel-uygulama |
| **linear-phase** | FIR ile sıfır faz; bedeli gecikme |
| **grup gecikmesi** | fazın frekans türevi (τg = -dφ/dω) |
| **cascade** | filtrelerin seri dizilimi |
| **normalize** | a0'a bölme (a0=1) |
| **DC kazancı** | f=0'daki genlik (H(1)) |
| **bypass** | Gain=0'da sinyali değiştirmez davranış |
| **denormal** | çok küçük float altı değer; CPU tuzağı |
| **crossfade** | iki katsayı kümesi arası yumuşak geçiş |
| **1/3 oktav** | f_merkez·2^(±1/6) bant genişliği |
| **Nyquist** | fs/2 (24 kHz @ 48 kHz) |
| **ADR-017** | 32-bit float ses verisi kararı |
| **sartname** | 512 örnek / 48 kHz tampon spesifikasyonu |
| **Q3/Q4** | dsp-master §7 açık karar kapıları |

## §18 Red-Team notları (Truth Mode)

1. Bu dosyanın **hiçbir** yeni satırı CoreMusic'e özgü EQ parametresi
   (bant sayısı, Q, eşik) iddia etmez — hepsi ya literatür (§6–§17) ya da
   açık kapıya (§8 R4, §5 R1–R3) bağlıdır.
2. §14/§15'teki sayılar **hesaplanmıştır**, uydurulmamıştır; yine de
   "bağlayıcı eşik" değildir — ADR bekler.
3. §7 serisi 31 banttır; repo `bandToFrequency()` ile eşleşiyor mu
   **okunarak** doğrulanmalı (satır 4 ✅ okundu; serinin standart aidiyeti
   ⚠️ — R4).
4. juce_dsp içerik iddiaları hâlâ ⚠️ (§1/10) — bu bölüm onları kapatmaz.
5. Kanıt oranı değişmedi: §1 12 satır, §2 sayımları aynen geçerli.

## §19 Uygulama yol haritası (PLANNED — bağlayıcı değil)

Aşağıdaki sıra yalnızca iş-tasarımı önerisidir; her adımın kapısı ayrıdır.
Hiçbir adım bu dosyadaki UNKNOWN'ları kapatmaz.

| # | Adım | Çıktı | Kapı (önce ne kapanmalı) | Durum |
|---|---|---|---|---|
| W1 | Karar ADR'si: bant sayısı, Q, eşik (R1) | sabitlenmiş parametre seti | Vault Steward onayı | ⏳ |
| W2 | Sınır kararı (R2/Q3): EQ ↔ filters | ölçütlü kapsam satırı | dsp-master §7 Q3 | ⏳ |
| W3 | Biquad çekirdeği (RBJ katsayıları) | `prepare/processBlock` sözleşmeli sınıf | W1 | PLANNED |
| W4 | 31-band grafik EQ katmanı | katsayı tablosu + preset yükleyici | W1 + W3 | PLANNED |
| W5 | Ölçüm–doğrulama paketi (§15 T1–T12) | test raporu | W3 | PLANNED |
| W6 | Realtime sertleştirme (§16 anti-pattern denetimi) | kod denetim listesi | W3 | PLANNED |
| W7 | Pipeline'a yerleşim | sıra kararı (Q3) | W2 | PLANNED |
| W8 | Ölçümle doğrulama (magnitude/phase çizimi) | kanıt dosyası | W5 | PLANNED |

**Sıra mantığı:** karar → çekirdek → katman → test → yerleşim. Ters sıra
(önce kod, sonra karar) yasaktır (zero-hallucination + §5/§6.7).

**Blok başına maliyet tahmini (yok — sayı uydurulmaz):** CoreMusic CPU profili
ölçülmedi → **UNKNOWN**. Ölçüm `measurement` dalının işidir (A3).

## §21 Ek eş-zamanlı okuma listesi (bu dalı besleyen dosyalar)

| Sıra | Dosya | Ne verir | Ne zaman okunur |
|---|---|---|---|
| 1 | `[[dsp-master]]` §3 satır 2 | çocuk rolü tanımı | her zaman |
| 2 | `[[dsp-master]]` §7 Q3 | sınır kapısı | A1/R2 tartışmasında |
| 3 | `[[dsp-bilesenler]]` | bileşen envanteri | bileşen eklemeden önce |
| 4 | `[[dsp-dogrulama]]` | kanıt protokolü | etiket değiştirmeden önce |
| 5 | `[[dsp-bagimliliklar]]` §5.1 | kütüphane bağımlılık kapısı | aday kütüphane için |
| 6 | `equalizer/index.md` | açık sorular (R1) | karar arifesinde |
| 7 | `../filters/filters-kapsam.md` | çekirdek sınırı (R2) | sınır tartışmasında |
| 8 | `../pipeline/pipeline-kapsam.md` | sıra (Q3) | yerleşim kararında |
| 9 | `../measurement/measurement-kapsam.md` | ölçüm sınırı (R3) | hedef eğri tartışmasında |
| 10 | `../../K003-audio/output/output-kapsam.md` | çıkış zinciri | entegrasyon öncesi |
| 11 | `../../K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec.md` | DAC katmanı sınırı | donanım-DSP sınırında |
| 12 | `../../K002-electroncis/devre-ve-analog/devre-ve-analog-anayasa.md` | analog katman | katman sınırında |

**Okuma kuralı:** toplu okuma yasak (boot kuralı); tablo yalnız ihtiyaç
anında çağrılır.

> Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler doğrulanmış kaynaklar ve
> mevcut dosya referansları içerir; doğrulanamayan iddialar ⚠️ VERIFICATION
> REQUIRED ile işaretlidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode (tek Authority).
