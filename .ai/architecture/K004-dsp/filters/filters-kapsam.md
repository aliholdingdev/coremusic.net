---
title: "filters — Kapsam"
type: architecture-scope
node_id: K004-dsp/filters
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
tags: []
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# filters — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`dsp-master.md` §3 satır 3 bu dalı **"Alçak/yüksek/geçiren bant filtreleri"**
olarak yazar. Basitçe: belirli frekansları geçirip diğerlerini engelleyen temel
yapı taşları — alçak geçiren (bass), yüksek geçiren (tiz), bant geçiren. `index.md`
bunları "EQ'nun ve crossover'ın malzemesi" diye çağırır: filters, biquad gibi
çekirdek matematiği sahiplenir.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Alçak/yüksek/bant geçiren çekirdek (biquad, SVF, ladder) | Bant seviyesi/Q yönetimi (kullanıcı ayarı) | equalizer |
| 2 | Filtre topolojisi/sırası (Butterworth vb. — tip kararı) | Bölünmüş frekansların hoparlöre dağıtımı | crossover |
| 3 | Filtre katsayıları (coefficients) hesabı | Ölçümlü düzeltme hedefi | measurement |
| 4 | Crossover'ın filtre malzemesi (besleme) | Crossover ağzının kendisi | crossover |
| — | *(dışında)* filtre tipi/sırası kararı (Butterworth…) | — | **UNKNOWN — R1** |

**Yasak:** equalizer ve crossover ile sınır; filters her iki tüketicinin de
malzemesidir ama kendi başına preset/ hoparlör bilmez.

## §3 Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | filters ↔ equalizer (çekirdek ↔ bant seviyesi) | ⚠️ VERIFICATION REQUIRED | dsp-master §7 Q3 komşu |
| A2 | filters ↔ crossover (malzeme ↔ dağıtım) | ⚠️ VERIFICATION REQUIRED | — |
| A3 | filre tipi/sırası kararı (Butterworth, Linkwitz-Riley…) | **UNKNOWN** — tahmin yasak | filters/index.md open_questions |
| A4 | minimum-phase vs linear-phase | **UNKNOWN** | — |

## §4 Disk kanıtları

| # | İddia | Gerçek dosya yolu / URL | Durum |
|---|---|---|---|
| 1 | Çocuk rolü = "Alçak/yüksek/geçiren bant filtreleri" | `dsp-master.md` §3 satır 3 | ✅ dosya okundu |
| 2 | "filtre tipi/sırası kararı (Butterworth vb.) kanıtsız" açık soru | `filters/index.md` open_questions | ✅ dosya okundu |
| 3 | CoreMusic `shared/src/` altında C++/PHP biquad/filtre çekirdeği yok | `shared/src/` taraması (grep) — negatif | ✅ 2026-10-12 doğrulandı |
| 4 | Signalsmith-Audio/dsp `filters.h` başlığı sunuyor | https://github.com/Signalsmith-Audio/dsp | ✅ 2026-10-12 doğrulandı |
| 5 | DSPark `Biquad`, `StateVariableFilter`, `LadderFilter`, `FilterEngine`, `CrossoverFilter` sunuyor | https://github.com/CristianMoresi/DSPark | ✅ 2026-10-12 doğrulandı |
| 6 | Patina ZDF/TPT ladder + Sallen-Key filtre devreleri sunuyor | https://github.com/ShmKnd/Patina | ✅ 2026-10-12 doğrulandı |
| 7 | JUCE `juce_dsp` filtre sınıfları içerir (sınıf adı doğrulanmadı) | https://github.com/juce-framework/JUCE/tree/master/modules | ⚠️ — modül ✅, içerik UNKNOWN |

## §5 Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | Filtre tipi ve sırası (Butterworth / Linkwitz-Riley / başka)? | **UNKNOWN** |
| R2 | filters ↔ equalizer sınırı hangi ölçütle? | ⚠️ VERIFICATION REQUIRED |
| R3 | Crossover bu dalın filtresini mi kullanacak, ayrı mı olacak? | ⚠️ VERIFICATION REQUIRED |

**Kapanış:** status: draft kalır. Filtre tipi kararı (R1) verilmeden topoloji
iddiası bağlayıcı sayılmaz (v2 §3 KURAL 3).

---

## §6 Teknik derinlik: filtre çekirdeği matematiği

> Literatür referansı; CoreMusic'te filtre çekirdeği YOKTUR (§4/3 — grep negatif).
> Formüller §11'deki doğrulanmış kaynaklardan alınmıştır.

### §6.1 Biquad — fark denklemleri ve form varyantları

```
Direct Form I (DF1):
  y[n] = b0·x[n] + b1·x[n-1] + b2·x[n-2] - a1·y[n-1] - a2·y[n-2]

Direct Form II (DF2):
  v[n] = x[n] - a1·v[n-1] - a2·v[n-2]
  y[n] = b0·v[n] + b1·v[n-1] + b2·v[n-2]

Transposed DF2 (TDF2):
  y[n] = b0·x[n] + z1
  z1   = b1·x[n] - a1·y[n] + z2
  z2   = b2·x[n] - a2·y[n]
```

| Form | Durum değişkeni | Sayısal stabilite | SIMD/öneri |
|---|---|---|---|
| DF1 | 4 kayıt (x1,x2,y1,y2) | iyi (geniş dinamik) | klasik, okunaklı |
| DF2 | 2 kayıt | zayıf (kutup-sıfır iptali hassas) | önerilmez |
| TDF2 | 2 kayıt | DF1 kadar iyi, gecikme 1 örnek | blok işlemede yaygına ⚠️ tercih = tasarım |
| SVF (aşağı §6.4) | 2–3 kayıt | çok iyi (parazitik mod yok) | aday |

Seçim ADR'ye bağlıdır (**UNKNOWN** — bu dosya karar vermez).

### §6.2 Butterworth — genlik ve derece

```
|H(jΩ)|² = 1 / (1 + (Ω/Ωc)^(2n))     , n = derece
n = 2 için:  Q = 1/√2 ≈ 0.7071        (maximally-flat, tepe yok)
n = 4 için:  2× (n=2) kaskad, her biri Q = 0.7071
-kB dB kesim frekansı: Ωc·(10^(kB/10) - 1)^(1/2n)
```

- Butterworth: bant içinde düz (0.9999…), kütükte yavaş kesim.
- Kararlılık: tüm kökler solda (s-dünya) / birim çember içi (z-dünya).
- ⚠️ "derece seçimi" CoreMusic'te **UNKNOWN** (A3/R1) — bu tablo yalnız
  literatür optionsıdır.

### §6.3 RBJ Cookbook katsayıları (alçak/yüksek/bant/notch)

```
w0 = 2π·f0/fs , alpha = sin(w0)/(2Q)   (Q formu; BW formu: alpha = sin(w0)·sinh(ln2/2·BW·w0/sin w0))

LPF:  b0 = (1-cos w0)/2      b1 = 1-cos w0      b2 = (1-cos w0)/2
HPF:  b0 = (1+cos w0)/2      b1 = -(1+cos w0)   b2 = (1+cos w0)/2
BPF0: b0 = alpha             b1 = 0             b2 = -alpha      (0 dB tepe)
BPF1: b0 = sin w0/2          b1 = 0             b2 = -sin w0/2   (Q kazançlı)
NOTCH:b0 = 1                 b1 = -2·cos w0     b2 = 1
APF:  b0 = 1-alpha           b1 = -2·cos w0     b2 = 1+alpha
hepsi: a0 = 1+alpha , a1 = -2·cos w0 , a2 = 1-alpha   → /a0 normalize
```

Notch ile BPF tamamlayıcıdır: `NOTCH(z) = 1 - BPF(z)` (aynı alpha).

### §6.4 State-Variable Filter (SVF, Chamberlin biçimi) — aday topoloji

```
f = 2·sin(π·fc/fs)      q = 1/Q
low  ← low  + f·band
high  = x - low - q·band
band ← band + f·high
çıkış: low / band (BPF) / high (HPF) / notch = high + low
```

- Tek girdiden üç çıkış aynı anda (LP+BP+HP) → crossover maliyeti düşer
  (komşu: `../crossover/`).
- `f` sınırı: fc ≲ fs/6'da stabil (kutuk kayması) — kırpma/limit önerilir ⚠️
  (spesifik sınır için kaynak §11/4, spesifik sayfa doğrulanmadı →
  **VERIFICATION REQUIRED**).
- Dikey-updating SVF, jitter/rezonans davranışında ladder'a benzer.

### §6.5 Ladder (4-pole) — analog-modeling referansı

- 4 × birinci derece kütük, kütük başına kazanç g; rezonans R döngüye geri
  besler → self-oscillation eşiği (R ≈ 1).
- Dijitalde ZDF/TPT (topolojik-sabit-gecikme) çözücü ile stabil edilir
  (Patina `LadderFilter` — §4/6 ✅ doğrulanmış kütüphane).
- K004 kapsamı: ladder üst-seviye bir topoloji adayıdır; CoreMusic
  uygulayacak mı **UNKNOWN** (A3).

### §6.6 Sıra (cascade) ve birleşim kuralları

| Kural | İfade |
|---|---|
| LTI kaskad | H_toplam = ∏ H_i (sıra matematiksel olarak önemsiz) |
| float32 sıra etkisi | yuvarlama toplam gürültüsü değişir → sıra testi gerekir |
| Kazanç yerleşimi | yüksek kazançlı bant önce → SNR erken bozulur; düşük kazanç önce → geç gürültü ⚠️ tercih = tasarım |
| Nyquist | fs/2'ye yakın biquad'lar fs'e duyarlı — fs değişince yeniden hesap |
| Aşırı-uyarımda | Q yüksekse sıralı değil eşzamanlı çözüm (TDF2/SVF) düşünülür |
| DC zinciri | her biquad'ın DC kazancı = H(1); toplam DC = çarpım |

### §6.7 Çözünürlük/yuvarlama (ADR-017 float32)

- Katsayılar float32'te ~7 ondalık güvenilir basamak; fs=48 kHz'te yüksek-Q
  biquad'lar float32 ile **genellikle** yeterlidir ⚠️ (kanıt: test — §8 T7).
- Referans üretim float64'te yapılp float32'ye yuvarlanır (double→float
  tek geçiş) — katsayı seri güncellenmez (drift önlenir).
- Negatif kanal: float32 katsayıyla kök-margini daralan yüksek-Q durumları
  olasıdır → §8 T7 taraması kapısı.

### §6.8 Birinci derece (tek kutuplu) alçak/yüksek geçiren

```
LPF: y[n] = y[n-1] + α·(x[n] - y[n-1]) ,  α = 1 - exp(-2π·fc/fs)
HPF: y[n] = α·(y[n-1] + x[n] - x[n-1])  (aynı α)
Grup gecikmesi (DC): τ ≈ 1/(2π·fc)
```

DC-offset temizliği, sub-sonik kırma ve zarf-yumuşatıcı (dynamics dalı) için
en ucuz yapı taşıdır (dinamik zarf: `../dynamics/`).

## §7 Worked example: LPF, fc = 2 kHz, Q = 0.7071 @ fs = 48 kHz

```
w0    = 2π·2000/48000      = 0.2617994
cos w0= 0.9659258          sin w0 = 0.2588190
alpha = sin w0/(2·0.7071)  = 0.2588190/1.4142136 = 0.1830127

b0 = (1-cos w0)/2   = (1-0.9659258)/2 = 0.0170371
b1 = 1-cos w0       = 0.0340742
b2 = b0             = 0.0170371
a0 = 1+alpha        = 1.1830127
a1 = -2·cos w0      = -1.9318517
a2 = 1-alpha        = 0.8169873

normalize (/a0):
b0' = 0.014401   b1' = 0.028802   b2' = 0.014401
a1' = -1.632983  a2' = 0.690599
```

Kontrol: kökler `z² + a1'·z + a2' = 0` → |kök| ≈ √0.690599 ≈ 0.831 < 1 ✅
(iki eşit kök: -a1'/2 = 0.81649; |0.81649| = 0.816 < 1 — tekdüze-kutuplu
Butterworth davranışı). DC kazancı: H(1) = (0.014401+0.028802+0.014401) /
(1 - (-1.632983)... hesap: pay = 0.057604; payda = 1 + 1.632983 + 0.690599
= 3.323582 → H(1) = 0.017332... ⚠️ **Bu sonuç LPF için DC≈1 beklentisine
AYKIRI görünüyor — demek ki normalize işareti/kontrol hatası var**: gerçek
kontrolde pay = b0'+b1'+b2' = 0.057604 ve payda = 1 - a1' - a2'? Hayır:
H(1) = (Σb)/(Σa) ve Σa = 1 + a1' + a2' = 1 - 1.632983 + 0.690599 = 0.057616
→ H(1) = 0.057604/0.057616 ≈ **0.9998** ✅ (±0.0002 — yuvarlama).*

> Ders (Truth Mode): LPF DC kontrolü `Σb / Σa` formuyla yapılır; işaret
> karıştırılırsa yanlış "aykırılık" görülür. §8 T1 bu formüle sabitlenir.

## §8 Doğrulama senaryoları (çekirdek implementasyonu için)

| # | Senaryo | Girdi | Beklenen | Durum |
|---|---|---|---|---|
| T1 | DC kazancı (Σb/Σa) | DC | LPF: ≈1; HPF: ≈0; notch: ≈1 | PLANNED |
| T2 | Nyquist kazancı | (-1)^n | LPF: ≈0; HPF: ≈1 | PLANNED |
| T3 | -3 dB noktası (LPF n=2) | sinus taraması | fc'de ≈ -3.01 dB | PLANNED |
| T4 | Butterworth düzlüğü | 0.1…0.8·fc | tepe yok (ripple ≈ 0) | PLANNED |
| T5 | Kararlılık ızgarası | fs × fc × Q × derece | \|kök\| < 1 | PLANNED |
| T6 | Türev-süreklilik (blok sınırları) | 1/64/512 blok | bit-birebir | PLANNED |
| T7 | float32 yuvarlama (§6.7) | double referans | genlik farkı ≤ eşik ⚠️ (tasarım) | PLANNED |
| T8 | Notch derinliği | f0 | ≈ -∞ dB (sınırlı Q) | PLANNED |
| T9 | SVF kütük kayması (§6.4) | fc/fs taraması | kayma toleransı ⚠️ | PLANNED |
| T10 | Denormal | uzun HPF kuyruğu | FTZ etkin | PLANNED |
| T11 | Faz/grup gecikmesi | analitik ↔ ölçüm | örtüşme ⚠️ tolerans | PLANNED |
| T12 | Cascade sıra etkisi | 4 bant reorder | ≤ tolerans (float32) | PLANNED |

## §9 Realtime kısıtları (bağlayıcı — AGENTS.md §1)

| # | Kural | Çekirdeğe yansıma |
|---|---|---|
| 1 | tahsis yasağı | durum dizileri prepare'da; runtime'da yalnız çarpım/toplama |
| 2 | lock-free | parametre/katsayı takası blok sınırında atomik |
| 3 | noexcept | katsayı hesaplaması RT dışında; process RT içinde |
| 4 | alignas(64) | durum blokları cache hattı hizalı |
| 5 | float32 (ADR-017) | tek tip veri zinciri; §6.7 doğruluk testi |
| 6 | 512/48k (sartname) | blok boyutu-özel kod yok; her blokta aynı sonuç |
| 7 | fail-safe | NaN katsayıda biquad'ı bypass/sıfırla (ses kesilmez) |

## §10 Komşu dal etkileşimi

| Komşu | Soru | Durum |
|---|---|---|
| equalizer | çekirdek mi, bant mı sahiplenir (A1/R2) | ⚠️ Q3 |
| crossover | hangi topoloji (LR/Butterworth) (A3/R1) | **UNKNOWN** |
| pipeline | sıra (filters önce mi?) | ⚠️ [TASARIM] Q3 |
| measurement | doğrulama ölçümü kim (A2) | ⚠️ Q4 |
| dynamics | zarf LPF'ü mü paylaşır | **UNKNOWN** |
| K003 output | filtrelenmiş sinyal çıkışa | `../../K003-audio/output/output-kapsam.md` |
| K001 | DAC'ın iç filtresi ayrı katman (PCM3168A) | `../../K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec.md` |
| K002 | analog filtre/devre | `../../K002-electroncis/devre-ve-analog/devre-ve-analog-anayasa.md` |

## §11 Kaynak doğrulama (2026-10-13, HEAD → HTTP 200)

| # | Kaynak | URL | HEAD | Kullanım |
|---|---|---|---|---|
| 1 | Audio EQ Cookbook (W3C/RBJ) | https://www.w3.org/2011/audio/audio-eq-cookbook.html | ✅ 200 | §6.3 |
| 2 | The Scientist and Engineer's Guide to DSP | https://www.dspguide.com/ | ✅ 200 | §6.1–§6.2 |
| 3 | Julius O. Smith III — filters | https://ccrma.stanford.edu/~jos/filters/ | ✅ 200 | §6.1 formlar |
| 4 | musicdsp.org (SVF referansı — spesifik sayfa ⚠️) | https://www.musicdsp.org/ | ✅ 200 | §6.4 (sayfa içeriği doğrulanmadı → ⚠️) |
| 5 | CCRMA reverb (FDN arka planı) | https://ccrma.stanford.edu/~jos/reverb/ | ✅ 200 | komşu reverb |
| 6 | Wikipedia — Butterworth | https://en.wikipedia.org/wiki/Butterworth_filter | ✅ 200 | §6.2 çapraz kontrol |
| 7 | Wikipedia — IIR / FIR | https://en.wikipedia.org/wiki/Infinite_impulse_response · https://en.wikipedia.org/wiki/Finite_impulse_response | ✅ 200/200 | §6.1 sınıflandırma |
| 8 | Wikipedia — Linkwitz–Riley | https://en.wikipedia.org/wiki/Linkwitz%E2%80%93Riley_filter | ✅ 200 | crossover komşusu |
| 9 | Signalsmith-Audio/dsp (`filters.h`) | https://github.com/Signalsmith-Audio/dsp | ✅ 200 | §4/4 |
| 10 | DSPark · Patina | https://github.com/CristianMoresi/DSPark · https://github.com/ShmKnd/Patina | ✅ 200 | §4/5–6 |

## §12 Wiki-link köprüsü

| Hedef | Bağlantı |
|---|---|
| K004 kök | [[dsp-master]] · [[dsp-bilesenler]] · [[dsp-dogrulama]] · [[dsp-bagimliliklar]] |
| Eş dal | `filters-dogrulama.md` · `filters-ornekler.md` |
| Komşu | `../equalizer/equalizer-kapsam.md` · `../crossover/crossover-kapsam.md` · `../pipeline/pipeline-kapsam.md` · `../measurement/measurement-kapsam.md` |
| K003 | `../../K003-audio/output/output-kapsam.md` |
| K001 | `../../K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec.md` · `ak4458-dac.md` · `tpa3255-amplifier.md` |
| K002 | `../../K002-electroncis/devre-ve-analog/devre-ve-analog-anayasa.md` |

## §13 Yol haritası (PLANNED — bağlayıcı değil)

| # | Adım | Kapı | Durum |
|---|---|---|---|
| W1 | Topoloji kararı (Butterworth/LR/SVF/ladder — A3/R1) | ADR | ⏳ |
| W2 | Biquad çekirdeği + test paketi (§8) | W1 | PLANNED |
| W3 | Eq/crossover tüketicilerine malzeme sağlama | W2 | PLANNED |
| W4 | float32 doğruluk kampanyası (§6.7/T7) | W2 | PLANNED |
| W5 | Ölçümle kanıt (measurement) | W4 | PLANNED |

## §14 Anti-pattern tablosu

| # | Hata | Sonuç |
|---|---|---|
| 1 | DF2 ile yüksek-Q üretim | kutup-sıfır iptali → gürültü patlaması |
| 2 | Her örnekte `pow/sin` | jitter (kural ihlali riski) |
| 3 | Katsayıları blok içi normalize etmek | israf + drift |
| 4 | fs değişince katsayı güncellememek | yanlış kesim frekansı |
| 5 | SVF'de fc → fs/4 üstü | rezonans kayması/instabilite (⚠️ sınır kaynağı §11/4) |
| 6 | NaN katsayı | tüm zincir NaN → fail-safe şart |
| 7 | Sıra değişince test etmemek (float32) | sessiz genlik sapması |
| 8 | DC'yi HPF'siz geçirmek | K001 donanım koruma riski (katman ayrı) |

## §15 Terim sözlüğü

| Terim | Tanım |
|---|---|
| **biquad** | 2. derece IIR |
| **DF1/DF2/TDF2** | biquad durum-topolojileri (§6.1) |
| **SVF** | state-variable filter (§6.4) |
| **ZDF/TPT** | zero-delay feedback / topolojik-sabit-gecikme (ladder stabilizasyonu) |
| **Butterworth** | maksimally-flat genlik tepkisi |
| **Linkwitz-Riley** | 2× Butterworth kaskad (crossover) |
| **derece (n)** | kutup sayısı |
| **Q** | kalite faktörü (bant genişliği/tepe) |
| **alpha** | RBJ'de bant genişliği terimi |
| **cascade** | seri kaskad |
| **kutup/sıfır** | H(z) payda/pay polinomu kökleri |
| **FTZ** | flush-to-zero (denormal önlemi) |
| **ADR-017** | float32 ses kararı |
| **sartname** | 512/48k blok |
| **malzeme** | filters'ın eq/crossover'a tedariki (kapsam §2) |
| **notch** | dar bant eleme |
| **APF** | all-pass filtre (faz) |
| **grup gecikmesi** | -dφ/dω |
| **ripple** | bant içi dalgalanma |
| **self-oscillation** | ladder'in rezonans eşiği |

## §16 Red-Team notları

1. §2/§6.2/§6.3'teki formüller **literatür**tür; CoreMusic topoloji tercihi
   hâlâ A3/R1'de **UNKNOWN** — bu bölüm kararı **temsil etmez**.
2. §4'teki kütüphane iddiaları önceki turlarda URL ile doğrulanmıştır; bu
   tur yalnız HEAD tazelemesi + literatür kaynağı eklenmiştir.
3. §7 örnek sayıları bu dosyada hesaplandı; "bağlayıcı eşik" değildir.
4. §11/4 musicdsp.org sayfa-içeriği doğrulanmadı → SVF sınır iddiaları ⚠️.
5. Kanıt oranı değişmedi: §1 11 satır · §2 sayımları aynen geçerli.

## §17 Kod şablonu: biquad çekirdeği (TASARIM ESKİZİ — repo'da YOK, kanıt değil)

```cpp
// K004-dsp filters çekirdeği — PLANNED iskelet (kanıt DEĞİL)
// Formüller: RBJ Cookbook (§11/1) · DSP guide (§11/2)
enum class FiltreTipi { LPF, HPF, BPF, Notch, APF };   // çekirdek kapsamı (§2/1)

struct Katsayi { float b0, b1, b2, a1, a2; };           // a0 = 1 (normalize)
struct Durum   { float x1 = 0.f, x2 = 0.f, y1 = 0.f, y2 = 0.f; };

// prepare() — RT DIŞI: tahsis/burada hesap serbest (sözleşme: §24 DSPark deseni)
Katsayi hesapla(float fs, FiltreTipi t, float f0, float q) noexcept {
    constexpr float PI = 3.14159265358979f;
    const float w0    = 2.0f * PI * f0 / fs;
    const float cw    = std::cos(w0);
    const float sw    = std::sin(w0);
    const float alpha = sw / (2.0f * q);
    float b0, b1, b2;
    switch (t) {
        case FiltreTipi::LPF:  b0 = (1-cw)/2;      b1 = 1-cw;         b2 = (1-cw)/2;   break;
        case FiltreTipi::HPF:  b0 = (1+cw)/2;      b1 = -(1+cw);      b2 = (1+cw)/2;   break;
        case FiltreTipi::BPF:  b0 = alpha;         b1 = 0.0f;         b2 = -alpha;     break;
        case FiltreTipi::Notch: b0 = 1.0f;         b1 = -2.0f * cw;   b2 = 1.0f;       break;
        default: /* APF */     b0 = 1.0f - alpha;  b1 = -2.0f * cw;   b2 = 1.0f + alpha; break;
    }
    const float a0 = 1.0f + alpha;
    const float a1 = -2.0f * cw;
    const float a2 = 1.0f - alpha;
    Katsayi k { b0 / a0, b1 / a0, b2 / a0, a1 / a0, a2 / a0 };
    if (!std::isfinite(k.b0) || !std::isfinite(k.a2))   // fail-safe (§9/7)
        return Katsayi { 1.0f, 0.0f, 0.0f, 0.0f, 0.0f }; // bypass kimliği
    return k;                                            // H(z) = 1
}

// processBlock — RT: tahsis yok, lock yok, noexcept (§9)
void isle(const Katsayi& k, Durum& d, float* buf, int n) noexcept {
    for (int i = 0; i < n; ++i) {
        const float x = buf[i];
        const float y = k.b0*x + k.b1*d.x1 + k.b2*d.x2 - k.a1*d.y1 - k.a2*d.y2;
        d.x2 = d.x1; d.x1 = x; d.y2 = d.y1; d.y1 = y;
        buf[i] = y;
    }
}

// kararlılık kontrolü (test/test-only): |kök| < 1
// z² + a1·z + a2 = 0 → kökler: (-a1 ± sqrt(a1² - 4a2)) / 2
bool kararli(const Katsayi& k) noexcept {
    const float disc = k.a1 * k.a1 - 4.0f * k.a2;
    if (disc >= 0.0f) { const float r = std::sqrt(disc);
        const float m1 = std::abs((-k.a1 + r) / 2.0f);
        const float m2 = std::abs((-k.a1 - r) / 2.0f);
        return m1 < 1.0f && m2 < 1.0f; }
    return std::abs(k.a2) < 1.0f;   // eş-pole durumu: |kök|² = a2
}
```

**Bu blok kanıt değildir** — §13'teki W2 adımının hedef tarifidir; gerçek
kod yazıldığında §8 testleri çalıştırılacak ve sonuç `filters-dogrulama.md`'ye
satır olarak girecektir.

## §18 Soru–cevap matrisi (açık kapıların dili)

| # | Soru | Cevap | Kapı |
|---|---|---|---|
| Q1 | Filtre tipi ne olacak (Butterworth/LR/SVF/ladder)? | bilinmiyor | A3/R1 |
| Q2 | Sıra kararı kimin (filters mi crossover mı)? | belirsiz | A2/R3 |
| Q3 | equalizer ile sınır nasıl çizilir? | belirsiz | A1/R2 (Q3) |
| Q4 | minimum-phase mi linear-phase mi? | bilinmiyor | A4 |
| Q5 | Kaç derece (n) seçilecek? | bilinmiyor | R1 |
| Q6 | float32 yüksek-Q'da yeterli mi? | test PLANNED | §8 T5/T7 |
| Q7 | SVF kütük sınırı tam olarak nedir? | ⚠️ (kaynak sayfası okunmadı) | §11/4 |
| Q8 | Hangi kütüphane (Signalsmith/DSPark/Patina/JUCE)? | bilinmiyor | bağımlılık kapısı |
| Q9 | juce_dsp filtre sınıfları neler? | ⚠️ içerik UNKNOWN | doğrulama turu |
| Q10 | Sıra-değişimi float32 toleransı? | tasarım kararı | §8 T12 |
| Q11 | Kazanç yerleşimi (gain staging) kuralı? | tasarım | §6.6 |
| Q12 | DC zinciri kim temizler (HPF)? | belirsiz | pipeline/dynamics |
| Q13 | CPU maliyeti/blok? | ölçülmedi → UNKNOWN | measurement |
| Q14 | Test kapsamı hedefi? | bilinmiyor | testing R1 |
| Q15 | Crossover kendi filtresini mi yazar? | belirsiz | A2/R3 |
| Q16 | fs değişince kim yeniden hesaplar? | tasarım | pipeline/K003 |
| Q17 | Ladder kullanılacak mı? | bilinmiyor | A3 |
| Q18 | Eşik/Q gibi sabitler nerede tutulur? | bilinmiyor | R1 (presets?) |
| Q19 | Ölçüm aracı ne? | aday REW (erişimi ✅) | R2/Q4 |
| Q20 | Kim onaylar? | Vault Steward (ADR) | süreç |

**Hiçbir soruya tahmini sayısal cevap yazılmamıştır.**

## §19 Uygulama yol haritası (PLANNED — bağlayıcı değil)

| # | Adım | Çıktı | Kapı | Durum |
|---|---|---|---|---|
| W1 | Topoloji/derece kararı (A3/R1) | ADR | Vault Steward | ⏳ |
| W2 | Biquad çekirdeği (§17) | sınıf + sözleşmesi | W1 | PLANNED |
| W3 | Kararlılık + doğruluk test paketi (§8) | test raporu | W2 | PLANNED |
| W4 | eq/crossover'a malzeme sunma | çekirdek API | W2 + Q3 | PLANNED |
| W5 | Ölçümle kanıt (measurement) | eğri raporu | W3 | PLANNED |
| W6 | CI'e DSP testi ekleme (testing R3) | pipeline çıktısı | testing altyapısı | PLANNED |

## §20 Ek eş-zamanlı okuma listesi

| Sıra | Dosya | Ne verir | Ne zaman |
|---|---|---|---|
| 1 | `[[dsp-master]]` §3 satır 3 | çocuk rolü | her zaman |
| 2 | `[[dsp-master]]` §7 Q3 | sınır kapısı | A1/A2 tartışmasında |
| 3 | `[[dsp-dogrulama]]` | kanıt protokolü | etiket değiştirmeden önce |
| 4 | `[[dsp-bagimliliklar]]` §5.1 | bağımlılık kapısı | kütüphane kararında |
| 5 | `filters/index.md` | R1 açık soruları | karar arifesinde |
| 6 | `../equalizer/equalizer-kapsam.md` | bant seviyesi sınırı | A1'de |
| 7 | `../crossover/crossover-kapsam.md` | dağıtım sınırı | A2'de |
| 8 | `../pipeline/pipeline-kapsam.md` | sıra/latency | yerleşimde |
| 9 | `../measurement/measurement-kapsam.md` | ölçüm kapısı | kanıt aşamasında |
| 10 | `../../K003-audio/output/output-kapsam.md` | çıkış zinciri | entegrasyonda |
| 11 | `../../K001-donanım/donanaım-type/audio/cipler/pcm3168a-codec.md` | DAC iç filtresi | katman sınırında |
| 12 | `../../K002-electroncis/devre-ve-analog/devre-ve-analog-anayasa.md` | analog filtre | katman sınırında |

**Okuma kuralı:** toplu okuma yasak; tablo yalnız ihtiyaç anında çağrılır.

## §21 Terim sözlüğü (ek — bu dosyaya özgü)

| Terim | Tanım |
|---|---|
| **normalize (a0)** | katsayıların a0'a bölünmesi |
| **kök marjini** | 1 - max\|kök\| |
| **Σb/Σa** | DC kazancı formülü (§7 dersi) |
| **bypass kimliği** | H(z)=1 katsayıları |
| **fail-safe** | hatalı katsayıda kimliğe düşme |
| **BW (oktav)** | bant genişliği (RBJ bandwidth formu) |
| **ripple** | bant içi genlik dalgalanması |
| **kutuk kayması** | SVF'de fc/fs ile bozulan kesim |
| **TDF2** | transposed direct form II |
| **ZDF** | zero-delay feedback (analog-modeling) |
| **derece (n)** | kutup sayısı |
| **kaskad** | seri filtre dizisi |
| **kazanç yerleşimi** | bloklar arası seviye düzeni |
| **malzeme sözleşmesi** | filters → eq/crossover tedariki |
| **çekirdek** | katsayı + fark denklemi (bu dal) |
| **topoloji** | devre/çözücü yapısı |
| **denormal** | alt-NaN float; CPU tuzağı |
| **FTZ** | flush-to-zero |
| **prepare** | RT-dışı hazırlık aşaması |
| **processBlock** | RT işleme sözleşmesi |

## §22 Red-Team notları

1. §6–§8 literatür ve hesaptır; CoreMusic topoloji/derece tercihi **UNKNOWN**
   kalır (A3/R1) — bu bölüm karar **temsil etmez**.
2. §17 kodu kanıt değildir, eskizdir; "repo'da var" denmez.
3. §11/4 musicdsp.org spesifik sayfası okunmadı → SVF sınır iddiaları ⚠️.
4. §18'de hiçbir soruya tahmini sayı yazılmamıştır (Truth Mode).
5. Kanıt oranı değişmedi: §1 11 satır · §2 sayımları aynen geçerli.

> Bu bölüm kanıt oranını DEĞİŞTİRmez — eklenen bölümler doğrulanmış kaynaklar ve
> mevcut dosya referansları içerir; doğrulanamayan iddialar ⚠️ VERIFICATION
> REQUIRED ile işaretlidir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode (tek Authority).
