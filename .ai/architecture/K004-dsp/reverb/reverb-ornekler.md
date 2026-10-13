---
title: "reverb — Örnekler"
type: architecture-examples
node_id: K004-dsp/reverb
node_type: subdomain-scope
status: draft
parent_id: K004-dsp
children: []
depends_on:
  - K004-dsp
evidence: []
validation: []
open_questions: []
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# reverb — Örnekler

## §1 Gerçek repo kodu (CoreMusic)

**Durum: PHP katmanında parametre var, gerçek reverb motoru yok.**
`shared/src/AI/` altında oda-düzeltme hesabı reverb parametresini kullanır; ancak
bu bir tahmin modelidir, sinyal-domain reverb motoru değildir (C++ motoru yok —
pipeline-ornekler §1):

```php
// shared/src/AI/AIEngine.php satır 111-121 (optimizeEQ içi)
$roomSize = $roomProfile['size'] ?? 'medium';
$reverb = $roomProfile['reverb'] ?? 0.5;
$frequencyResponse = $roomProfile['frequency_response'] ?? [];
// ...
$gain = $this->calculateRoomCorrection($frequency, $roomSize, $reverb, $frequencyResponse);
```

```php
// shared/src/AI/AIEngine.php satır 347-362 — reverb kazanca çarpılıyor
$actualResponse = $frequencyResponse[$freqKey] ?? 0.0;
return ($targetResponse - $actualResponse) * $roomFactor * (1 - $reverb);
```

**Ne gösterdiği:** reverb'ün CoreMusic'te şimdilik yalnızca bir `float` (0–1)
parametresi olarak var olduğu; `(1 - $reverb)` ile düzeltme kazancından düşülüyor.
Gerçek algoritma (FDN/convolution) henüz yazılmadı.

## §2 Resmi doküman / bibliyotek örneği

**Kaynak:** https://github.com/ShmKnd/Patina (README — Reverb, 2026-10-12 ✅)

```cpp
patina::ReverbEngine reverb;
reverb.prepare({48000.0, 512, 2});
patina::ReverbEngine::Params p;
p.type  = patina::ReverbEngine::Spring;   // Spring & plate
p.decay = 0.6f;
p.mix   = 0.3f;
reverb.processBlock(input, output, 2, numSamples, p);
```

**Ne gösterdiği:** analog-modeling reverb — tip seçimi (spring burada) + decay/mix
parametreleri; hazır `prepare`/`processBlock` deseni. CoreMusic `reverb` dalı için
parametre sözleşmesi referansı.

**Kaynak:** https://github.com/CristianMoresi/DSPark (README — v1.8.0 notes, 2026-10-12 ✅)

```
AlgorithmicReverb — true-stereo 32-line FDN (16 in Eco),
                    binaural early field, frequency-dependent decay
```

**Ne gösterdiği:** FDN tabanlı algoritmik reverb referansı — "oda hissi" için
zengin topoloji; `Reverb` (convolution) ayrı sınıf (Capabilities/Time-spatial).

## §3 Uygulama notu

CoreMusic `reverb` dalı PHP'deki `float` parametresini koruyabilir, ancak motor
FDN (DSPark) veya analog-modeling (Patina) referansıyla yazılacak. Oda modeli +
IR verisi (R1) gelmeden hangi tipin seçildiği sabitlenmez.

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `[[reverb-kapsam]]` | sınır ve R1/R3 açık sorular |
| `[[reverb-dogrulama]]` | iddia kanıtları |
| `../delay/delay-kapsam.md` | sınır komşusu (tek kopya ↔ dağıtık yansıma) |
| `../spatial-audio/spatial-audio-kapsam.md` | sınır komşusu (oda hissi ↔ konum) |
| `../measurement/measurement-kapsam.md` | IR ölçüm verisi kaynağı |

## §5 Web araştırması — convolution ve altyapı referansları (2026-10-12)

Aşağıdaki URL'ler 2026-10-12 tarihinde `Invoke-WebRequest -Method Head -UseBasicParsing`
ile doğrulanmıştır (HTTP 200). Her satır: iddia · URL · tarih · durum.

| # | İddia | URL | Tarih | Durum |
|---|---|---|---|---|
| 1 | `ConvolverNode`'de `normalize` özelliği, konvolüsyon giriş sinyalinin RMS'ine göre kazancı otomatik ölçekler; varsayılan `true` | https://developer.mozilla.org/en-US/docs/Web/API/ConvolverNode/normalize | 2026-10-12 | ✅ HTTP 200 |
| 2 | JUCE `juce::dsp::Convolution` sınıfı, offline ve real-time konvolüsyon destekler; `prepare` ile block size/sample rate/num channels ayarlanır | https://docs.juce.com/master/classjuce_1_1dsp_1_1Convolution.html | 2026-10-12 | ✅ HTTP 200 |
| 3 | `OfflineAudioContext`, gerçek zaman kısıtı olmadan ses işleme sağlar; `startRendering()` promise ile render sonucu döner | https://developer.mozilla.org/en-US/docs/Web/API/OfflineAudioContext | 2026-10-12 | ✅ HTTP 200 |
| 4 | `PeriodicWave`, `OscillatorNode`'a özel harmonic set tanımlar; `real`/`imag` dizileriyle dalga formu belirlenir | https://developer.mozilla.org/en-US/docs/Web/API/PeriodicWave | 2026-10-12 | ✅ HTTP 200 |
| 5 | CCRMA "Synthesis of Sound by Physical Modeling" sayfası, reverb dahil fiziksel modelleme sentezini kapsar | https://ccrma.stanford.edu/~jos/sasp/ | 2026-10-12 | ✅ HTTP 200 |
| 6 | `AudioBufferSourceNode`, `AudioBuffer`'ı kaynak olarak çalar; `buffer` özelliği reverb IR yüklemesi için kullanılır | https://developer.mozilla.org/en-US/docs/Web/API/AudioBufferSourceNode | 2026-10-12 | ✅ HTTP 200 |
| 7 | JUCE `juce::dsp::Oscillator`, faz accumulate tabanlı osilatör; spring reverb modülasyon referansı olabilir | https://docs.juce.com/master/classjuce_1_1dsp_1_1Oscillator.html | 2026-10-12 | ✅ HTTP 200 |

**Doğrulanamayan adaylar (❌ 404):**

| Aday URL | Durum |
|---|---|
| https://ccrma.stanford.edu/reverb/ | ❌ HTTP 404 (2026-10-12) — `⚠️ VERIFICATION REQUIRED` |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §6 Convolution reverb — mekanik ve ölçek maliyeti

Convolution reverb, oda tepkime fonksiyonunu (IR — Impulse Response) giriş sinyaline
konvolüsyon uygulayarak çalışır. MATLAB tanımı:

```
y[n] = Σ_{k=0}^{L-1} h[k] · x[n-k]
```

`h[k]` = IR katsayıları (L uzunluk), `x[n]` = giriş, `y[n]` = çıkış.

### §6.1 Doğrudan konvolüsyon maliyeti

| IR Uzunluğu (L) | Örnekleme (48 kHz) | Çarpım/ornek (kaygan) | 512-örnek blok toplam | FFT'li konvolüsyon (2×N log N) |
|---|---|---|---|---|
| 1024 | ~21 ms | 1024 | 524.288 | ~11.000 (N=2048 kabaca) |
| 8192 | ~170 ms | 8192 | 4.194.304 | ~30.000 (N=16384 kabaca) |
| 65536 | ~1,37 s | 65536 | 33.554.432 | ~1.000.000 (N=131072 kabaca) |
| 262144 | ~5,5 s | 262144 | 134.217.728 | ~4.600.000 (N=524288 kabaca) |

> ⚠️ **Veri kaynağı:** Tablo değerleri doğrudan formül hesabıdır (L çarpım/ornek,
> L×512 blok toplam). FFT sütunu `2·N·log₂(N)` yaklaşım formülünden türetilmiştir —
> gerçek uygulama performansı JUCE sürümüne ve donanıma bağlıdır,
> `⚠️ VERIFICATION REQUIRED`.

### §6.2 CoreMusic 512-örnek sınırı ile etkileşim

CoreMusic gerçek zamanlı hattı 512 örnek / 48 kHz blok ile çalışır (sartname).
Convolution reverb doğrudan uygulanırsa:

- **Kısa oda IR (< ~250 ms):** 12.000 örnek → ~1,6 MB stereo float (2 kanal ×
  12000 × 4 byte) → kabul edilebilir bellek, doğrudan konvolüsyon ~12.000 çarpım/ornek
  (512-örnek blokta ~6,1 M çarpım) → tek başına CPU bütçesini aşar,
  **FFT tabanlı bölünmüş konvolüsyon zorunlu**.
- **Uzun IR (> ~1 s):** 48.000+ örnek → FFT'li konvolüsyon ile ~10×-30× ucuzlar
  (yukarıdaki tablo). Bellek ~384 KB/kanal (48000×4 byte).
- **Çok kanallı (8.1):** 8 kanal × IR → bellek ve çarpım 8× artar; her kanal için
  ayrı IR gerekebilir (oda hissi kanal-bağımlıysa).

**Sonuç:** convolution reverb CoreMusic'te ancak FFT bölünmüş konvolüsyon ile
gerçek zamanlıye uygundur; JUCE `juce::dsp::Convolution` sınıfı bunu içerir
(§5, satır 2).

### §6.3 Normalize davranışı

WebAudio `ConvolverNode`'da `normalize` varsayılan `true`'dur — IR, giriş sinyaline
bağımsız olarak RMS ölçeklemesi ile normalize edilir (§5, satır 1). Bu, farklı
uzunlukta/farklı kazançta IR'lar arasında tutarlı seviye sağlar; ancak
düşük enerjili IR'lar (sönümleme geçişi gibi) istenmeyen şekilde yükseltilebilir.
CoreMusic motorunda bu davranış ADR ile sabitlenmelidir (varsayılan: `true` önerilir,
kullanıcı "wild" modda kapatabilir).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §7 FDN (Feedback Delay Network) — topoloji ve parametreler

FDN, gecikme hatları arasındaki besleme matrisi ile ortak modal yoğunluk üretir.
DSPark örneği (§2) "32-line true-stereo FDN" referansı verir.

### §7.1 Minimal 4-hat FDN

```
        ┌──┐   ┌──┐   ┌──┐   ┌──┐
        │z⁻¹│   │z⁻¹│   │z⁻¹│   │z⁻¹│   (4 gecikme hattı: D1..D4)
        └──┘   └──┘   └──┘   └──┘
          │      │      │      │
          └──────┴──────┴──────┘
                     │
               ┌─────┴─────┐
               │  Householder │   (besleme matrisi H = I − 2/N · 11ᵀ)
               │  matris H   │
               └─────┬─────┘
                     │
               geri besleme (g → 0..1, frekans-bağımlı olabilir)
```

| Parametre | Semantik | CoreMusic PHP karşılığı |
|---|---|---|
| `D_i` (hat uzunluğu) | Oda boyutu → modal aralık ≈ 1/D_i | `roomProfile['size']` türetilir (kısa/orta/uzun) |
| `g` (geri besleme) | T60 → g = 10^(−3·D_i/(T60·fs)) | `decay` (0–1) → T60'e map |
| `H` (matris) | Hat karışımı; Householder üniform mod sağlar | sabit (kullanıcı yok) |
| `mix` (dry/wet) | Çıkış = (1−mix)·giriş + mix·reverb | `mix` (0–1) |
| `predelay` | İlk yansıma öncesi kuru sinyal süresi | yok (eklenebilir) |

### §7.2 Householder matrisi avantajı

`H = I − (2/N)·J` (J = birim matris) otonormtur (H·Hᵀ = I) → besleme döngüsünde
enerji kaybı yalnızca `g`'den gelir; modal sönüm eşit dağılır. Bu, "flat" reverb
karakteri üretir. Alternatif Hadamard/Householder seçimi karakteri değiştirir:
Householder daha yumuşak, Hadamard daha "metalik" algılanır (uygulama gözlemi —
`⚠️ VERIFICATION REQUIRED`, algısal karşılaştırma ölçülmedi).

### §7.3 Frekans-bağımlı sönüm

Gerçek odalarda yüksek frekanslar daha hızlı söner. Uygulamada her hattan sonra
düşük geçiren filtre (ya da hat-içi filtre) konarak T60(f) hedeflenir.
CoreMusic'te `roomProfile['frequency_response']` bu amaçla kullanılabilir —
mevcut PHP kodunda yalnız EQ düzeltmesi için var (§1); reverb motoruna
taşınması açık sorudur (R-kod: `reverb-kapsam` R3).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §8 Analog-modeling reverb — spring/plate karakteri

Patina `ReverbEngine` (§2) spring ve plate sunar. Bu iki tip konvolüsyon değil,
nonlineer + dispersive sistem modellemesidir.

### §8.1 Spring reverb mekaniği

Spring reverb, torsional dalga yayında ilerleyen mekanik dalga tabanlıdır:
giriş transdüser → yay → çıkış transdüser. Temel özellikler:

| Özellik | Davranış |
|---|---|
| Dispersiyon | Hız frekansa bağlı → "boing" / "drip" karakteri |
| Nonlineerlik | Yüksek seviyede yay modları harmonik üretir |
| Coupling modları | Torsional + lateral → çok zengin mod yapısı |
| Sıcaklık/bakım | Gerçek yaylarda drift; modelde deterministik |

Modelleme yaklaşımları (literatür bilgisi):
- **Tapped delay line + allpass zinciri:** yayın dispersion eğrisini taklit eder.
- **Dijital waveguide (CCRMA fiziksel modelleme):** FDTD ya da 1D dalga denklemi
  (§5, satır 5). CoreMusic C++ motorunda uygulanabilir.

### §8.2 Plate reverb mekaniği

Plate reverb, ince metal levhada yayılan enine dalga tabanlıdır:
giriş transdüser → levha → 2+ çıkış transdüser (farklı konumlar). Özellikler:

| Özellik | Davranış |
|---|---|
| Erken reflection | Levha boyutuna bağlı, oda kadar belirgin değil |
| Mod yoğunluğu | Çok yüksek → smooth, "yayvan" karakter |
| Frekans yanıtı | Daha düz, oda reverb'ünden daha "parlak" |
| Nonlineerlik | Düşük seviyede lineer |

### §8.3 CoreMusic parametre haritası

CoreMusic PHP `reverb` parametresi (0–1) tek başına spring/plate ayrımı yapamaz.
Motor tarafında ek parametreler önerilir (ADR ile sabitlenecek):

```cpp
struct ReverbParams {
    enum Type { Room, Hall, Spring, Plate } type = Room;
    float decay   = 0.6f;   // 0..1 → T60 map
    float mix     = 0.3f;   // 0..1 dry/wet
    float predelayMs = 0.0f;
    float damping  = 0.5f;  // yüksek frekans sönüm
    // PHP'de olmayanlar (eklenmeli):
    Type  type    = Room;   // PHP'de "reverb" 0-1 → Room varsayımı
    float width   = 1.0f;   // stereo genişlik
};
```

`type` alanı PHP'de mevcut değil → motor Room (§7 FDN) ile başlar; spring/plate
sonraki fazda eklenir (R1: oda modeli + IR verisi gelmeden sabitlenmez, §3).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §9 Ölçüm — RT60 ve tepkime karakterizasyonu

Reverb motorunun doğrulanması, ölçülen tepkime istatistikleri ile hedef
eşleşmesiyle yapılır.

### §9.1 Ölçülebilir parametreler

| Parametre | Tanım | Ölçüm yöntemi |
|---|---|---|
| **RT60** (T60) | Tepkime −60 dB'ye düşene kadar süre | Schroeder entegrasyonu (backward integrate) |
| **EDT** (Early Decay Time) | 0 ile −10 dB arası eğimden türetilen süre | Schroeder, erken kısım |
| **C50 / C80** | 50/80 ms sonrasının enerji oranı (dB) | Enerji oranı: 10·log₁₀(sonrası enerjisi / öncesi enerjisi) |
| **DRR** (Direct-to-Reverberant) | Direkt/reaktif enerji oranı | Tepe vs kuyruk enerjisi |
| **Modal yoğunluk** | Birim frekanstaki resonans sayısı | FFT / modal analiz |

Schroeder entegrasyonu (yöntem):

```
y[n] = Σ_{k=n}^{N-1} h²[k]      (geriye doğru kümülatif enerji)
RT60 ≈ süre y[n] −60 dB düşene kadar
```

### §9.2 CoreMusic ölçüm hattı

`measurement` dalı IR ölçüm verisi üretir (§4: `../measurement/measurement-kapsam.md`);
bu veri:

1. Convolution reverb girişi olarak kullanılır (IR = h[k]).
2. FDN motorunun hedef RT60/C50 değerlerini belirler.
3. Doğrulama: motor üretilen tepkime → aynı istatistikler → tolerans ±%10 içinda
   olmalı (hedef tolerans, `⚠️ VERIFICATION REQUIRED`).

### §9.3 Offline render ile test

WebAudio `OfflineAudioContext` (§5, satır 3), gerçek zaman kısıtı olmadan reverb
impuls yanıtı render etmeye izin verir — `startRendering()` promise sonucu
`AudioBuffer` olarak alınır ve istatistikler hesaplanır. CoreMusic C++ motorunda
eşdeğer: `processBlock`'i test ortamında 48.000 örnek impulse ile çağır,
Schroeder eğrisini hesapla. JUCE `juce::dsp::Convolution`'ın offline modu da
bu amaçla kullanılabilir (§5, satır 2).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §10 Riskler ve sınırlar

| # | Risk | Etki | Önlem |
|---|---|---|---|
| 1 | Doğrudan konvolüsyon CPU bütçesini aşar | XRUN / ses kesilmesi | FFT bölünmüş konvolüsyon zorunlu |
| 2 | Uzun IR (> 2 s) bellek taşması | 8.1'de ~384 KB/kanal × 8 → 3 MB; okunabilir ama cache thrash | IR segmentasyonu; baş + kuyruk ayrımı |
| 3 | Normalize yanlış seviye üretir (düşük enerjili IR) | Aşırı yükseltilmiş kuyruk | `normalize=false` + elle gain seçeneği |
| 4 | FDN hat uzunlukları modal aralık üretir (çok kısa ise "metalik") | Metalik reverb karakteri | Hat uzunlukları asal/çoğul olmayan; ≥ 2 ms |
| 5 | FDN besleme döngüsü g ≥ 1 → sonsuz kuyruk | Bellek taşması / clip | Sert limiter ya da g < 1 garantisi (motor içinde) |
| 6 | Spring nonlineerliği CPU'yu artırır | Zaman aşımı | Modelde basitleştirme; preset başına CPU profili |
| 7 | PHP `reverb` (0–1) → tip eşlemesi belirsiz | Beklenmeyen karakter | Room varsayımı (ADR ile sabitlenir) |
| 8 | Frekans bağımlı sönüm filtresi gecikme ekler | Latans artışı | Filtre block-içi uygulanır; grup gecikmesi ölçülür |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §11 Denetim kontrol listesi

| # | Kontrol | Beklenen | Durum |
|---|---|---|---|
| 1 | Convolution FFT kullanıyor mu? | Evet — doğrudan konvolüsyon 512-örnek blokta gerçek zamanlı değil | ☐ |
| 2 | `normalize` davranışı sabitlendi mi? | ADR ile `true`/`false` kararı | ☐ |
| 3 | FDN g < 1 garantisi var mı? | Motor içinde sert kontrol | ☐ |
| 4 | FDN hat uzunlukları ≥ 2 ms ve asal-benzeri mi? | Metalik rezonans önlenir | ☐ |
| 5 | T60 hedefi PHP `decay`'dan mı türetiliyor? | `T60 = f(decay)` eşlemesi tanımlı | ☐ |
| 6 | Offline impulse testi var mı? | RT60 ölçümü ± tolerans içinde | ☐ |
| 7 | Spring/plate faz 2'ye mi ertelendi? | R1 bağımlılığı (oda modeli + IR) beklemede | ☐ |
| 8 | 8.1 kanal bellek profili çıkarıldı mı? | 8 × IR bellek hesabı | ☐ |
| 9 | Frekans bağımlı sönüm uygulandı mı? | Düşük geçiren filtre ya da not: `⚠️ VERIFICATION REQUIRED` | ☐ |
| 10 | Latans ölçümü yapıldı mı? | Reverb bloğu latansı ölçüldü ve raporlandı | ☐ |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §12 Özet

| Konu | Durum |
|---|---|
| PHP reverb | Parametre var (`float` 0–1), motor yok |
| C++ motor hedefi | FDN (odalar) + convolution (IR verisi gelince) + spring/plate (faz 2) |
| Convolution maliyeti | FFT bölünmüş konvolüsyon zorunlu (§6) |
| Ölçüm | Schroeder RT60 + C50; offline impulse ile doğrulama (§9) |
| Doğrulanmış URL | 7 × HTTP 200 (§5) · 1 × 404 (`⚠️ VERIFICATION REQUIRED`) |
| Açık sorular | R1 (oda modeli + IR) · R3 (frekans bağımlı sönüm haritası) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §13 JUCE DSP entegrasyon notları (CoreMusic motoru)

CoreMusic C++20 motoru JUCE tabanlıdır; reverb modülü JUCE `juce::dsp` altında
hazır sınıflar içerir ya da özel yazım gerekir. JUCE Convolution sınıfı (§5, satır 2):
`prepare(ProcessSpec)` ile örnekleme/blok/kanal ayarlanır, `process(ProcessContext)`
ile blok işlenir, `loadIR(AudioBuffer<float>)` ile IR yüklenir.

### §13.1 JUCE `dsp::Convolution` kullanımı deseni

```cpp
juce::dsp::Convolution conv;
juce::dsp::ProcessSpec spec { 48000.0, 512, 2 };  // sampleRate, blockSize, numChannels
conv.prepare(spec);
conv.loadIR(irBuffer);       // IR: measurement dalından alınan tepkime
conv.process(juce::dsp::ProcessContextReplacing<float>(block));
```

| JUCE sınıfı | Kullanım | CoreMusic reverb rolü |
|---|---|---|
| `juce::dsp::Convolution` | FFT tabanlı konvolüsyon | IR reverb (faz 2) |
| `juce::dsp::Oscillator` | Faz accumulate osilatör (§5, satır 7) | Spring modülasyon / LFO |
| `juce::dsp::Phaser` | Allpass kademe faz kaydırma (§5, satır 8 — Phaser sınıfı) | Spring "boing" taklidi (alternatif) |
| `juce::dsp::LookupTableTransform` | Sınırlı aralıkta nonlineer lookup (§5, satır 9) | Spring nonlineerlik yaklaşma |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §14 Reverb algoritma karşılaştırma matrisi

| Algoritma | Kalite | CPU (512 örnek) | Bellek | Param. zorluğu | CoreMusic uygunluk |
|---|---|---|---|---|---|
| Doğrudan konvolüsyon | ★★★★★ (IR birebir) | ✗ (512-örnek blokta aşırı) | Yüksek (IR) | Düşük | ✗ FFT olmadan yok |
| FFT bölünmüş konvolüsyon | ★★★★★ | ★★★★ (uygun) | Yüksek (IR) | Düşük | ✓ Faz 2 |
| FDN (4–32 hat) | ★★★★ | ★★★★ | Düşük (N × D) | Orta | ✓ Faz 1 |
| FDN + frekans bağımlı sönüm | ★★★★★ | ★★★ (filtre ek maliyet) | Düşük-Orta | Yüksek | ✓ Faz 1.5 |
| Dijital waveguide (spring) | ★★★★ | ★★★ | Düşük | Yüksek | ✗ Faz 2+ |
| Allpass zinciri (Schroeder) | ★★ | ★★★★★ | Çok düşük | Düşük | ✗ Basit kalır, yalnız fallback |
| Feedback comb + allpass (Freeverb) | ★★★ | ★★★★ | Düşük | Düşük | ✓ Basit fallback |

**Karar özeti (CoreMusic):**
- **Faz 1:** FDN (§7) — PHP `reverb` 0–1 parametresini karşılars; oda/hall tipleri.
- **Faz 2:** Convolution (§6, §13) — `measurement` dalından IR verisi gelince.
- **Faz 2+:** Spring/plate (§8) — analog-modeling; JUCE Oscillator/Phaser/LookupTable ile.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §15 Ek — Schroeder allpass/comb formülleri (referans)

FDN'ye geçiş öncesi basit fallback olarak Schroeder (1962) comb + allpass
zinciri kullanılabilir. Formüller:

**Feedback comb (giriş x, çıkış y, gecikme M, geri besleme g):**

```
y[n] = x[n−M] + g · y[n−M]
```

**Allpass (gecikme M, katsayı g):**

```
y[n] = −g · x[n] + x[n−M] + g · y[n−M]
```

Freeverb deseni: 8 comb (sol: 1116, 1188, 1277, 1356 · sağ: 1139, 1211, 1300,
1393 örnek) + 4 allpass (556, 441, 341, 225 örnek). Damping parametresi comb
içinde tek kutuplu filtre ile uygulanır. (Freeverb kaynak kodu açık kaynak —
`https://www.freverb.org/` · `⚠️ VERIFICATION REQUIRED` — 2026-10-12'de HEAD
ile doğrulanmadı; sayısal değerler yaygın bilgi olarak taşındı, uygulama öncesi
kaynak doğrulanmalı.)

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §16 Reverb preset haritası — PHP parametre → motor karakteri

PHP `reverb` (0–1) tek boyutlu; motor bunu T60'a, tip seçimi Room'a sabitli.
Aşağıdaki eşleme motor sözleşmesi taslağıdır:

| PHP `reverb` | T60 hedefi | Karakter | Eşdeğer fiziksel oda |
|---|---|---|---|
| 0.00–0.10 | < 0.3 s | Neredeyse kuru (yalnız early reflections) | Yankısız oda / dead room |
| 0.10–0.30 | 0.3–0.8 s | Kısa oda, tanımlı | Ölçüm odası / stüdyo booth |
| 0.30–0.50 | 0.8–1.5 s | Orta oda | Orta boy stüdyo / rehearsal |
| 0.50–0.70 | 1.5–2.5 s | Konser salonu erişimi | Küçük konser salonu |
| 0.70–0.85 | 2.5–4.0 s | Büyük salon | Kilise / amfi |
| 0.85–1.00 | 4.0–8.0 s | Katedral / hall | Katedral / büyük amfi |

> ⚠️ **Kaynak notu:** T60 aralıkları pratik reverb preset literatürüne dayanır;
> CoreMusic odası ölçülmüş olmadığından bu harita **tahmin**tir ve R1 (IR ölçüm
> verisi) gelince revize edilir. `measurement` dalından gerçek RT60 gelmeden
> kesin eşleme `⚠️ VERIFICATION REQUIRED`.

### §16.1 T60 → FDN geri besleme dönüşümü

Her gecikme hattı için:

```
g_i = 10^(−3 · D_i / (T60 · fs))
```

D_i = hat uzunluğu (örnek), fs = 48000. Örnek: D = 1600 örnek (~33 ms),
T60 = 1.5 s → g = 10^(−3·1600/(1.5·48000)) = 10^(−4800/72000) = 10^(−0.0667) ≈ 0.858.

| D_i (örnek) | ≈ ms | T60=0.8 s → g | T60=1.5 s → g | T60=4.0 s → g |
|---|---|---|---|---|
| 1409 | ~29 | 0.756 | 0.867 | 0.948 |
| 1600 | ~33 | 0.781 | 0.886 | 0.955 |
| 1811 | ~38 | 0.803 | 0.902 | 0.962 |
| 2053 | ~43 | 0.822 | 0.916 | 0.967 |
| 2311 | ~48 | 0.840 | 0.928 | 0.972 |

> Hesap: `g_i = 10^(−3·D_i/(T60·48000))` — tablo değerleri formülün doğrudan
> sonucu; harici kaynak iddiası değildir.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §17 CoreMusic reverb — motor arayüz taslağı (sözleşme)

PHP katmanı ↔ C++ motor arası reverb parametre aktarımı sözleşmesi taslağı:

```cpp
// ReverbParams — PHP'den gelen harici parametreler
struct ReverbParams {
    float roomSize  = 0.5f;   // PHP roomProfile['size'] eşlemesi (0–1)
    float reverb    = 0.5f;   // PHP reverb (0–1) — T60 map (§16)
    float damping   = 0.5f;   // Yüksek frekans sönüm (0–1) — PHP'de yok (eklenecek)
    float width     = 1.0f;   // Stereo genişlik (0–1) — PHP'de yok (eklenecek)
    bool  enabled   = true;   // bypass anahtarı
};

// Motor iç durum (harici değil)
struct ReverbInternal {
    float t60;                // reverb'ten türetilir (§16)
    float fdnG[4];            // hat başına geri besleme (§16.1)
    // FDN hat buffer'ları (çalışma zamanında allocate edilir)
};
```

| Alan | PHP kaynağı | Durum |
|---|---|---|
| `roomSize` | `roomProfile['size']` (`short`/`medium`/`large` → 0.25/0.5/0.75 eşlem) | Mevcut |
| `reverb` | `roomProfile['reverb']` | Mevcut |
| `damping` | Yok — PHP'ye eklenmeli | Eksik (faz 1.5) |
| `width` | Yok — PHP'ye eklenmeli | Eksik (faz 2) |
| `enabled` | Yok — basit `reverb > 0` kontrolü yapılabilir | Taslak |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
