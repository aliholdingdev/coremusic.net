---
title: "music-analysis — Örnekler"
type: architecture-examples
node_id: K006-yapay-zeka/music-analysis
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# music-analysis — Örnekler

## §1 Gerçek repo kodu (ÖNCELİK 1 — bu oturumda okunarak doğrulandı)

`shared/src/AI/AIEngine.php` satır 82–106 — analiz çıktısı:

```php
public function analyzeAudio(string $filePath): array
{
    if (!file_exists($filePath)) {
        throw new \RuntimeException("Audio file not found: {$filePath}");
    }

    // FFprobe ile metadata çıkarma
    $metadata = $this->extractMetadata($filePath);

    // DSP pipeline ile özellik çıkarma
    $features = $this->extractFeatures($filePath);

    return [
        'bpm'           => $features['bpm'] ?? 0.0,
        'key'           => $features['key'] ?? 'unknown',
        'energy'        => $features['energy'] ?? 0.0,
        'mood'          => $features['mood'] ?? 'neutral',
        ...
    ];
}
```

`shared/src/AI/AIWorkflow.php` satır 119–125 — analiz sonucunun persist edilmesi:

```php
$bpm    = (float)$analysis['bpm'];
$keySig = (string)$analysis['key'];
$energy = (float)$analysis['energy'];
$this->toolCalling->callTool('db-query', [
    'sql'      => "UPDATE tracks SET bpm = :bpm, key_sig = :key_sig, energy = :energy WHERE id = :id",
    ...
```

## §2 Resmi doküman örneği (URL + snippet)

**librosa — feature extraction (rhythm)** · https://librosa.org/doc/latest/api/feature.html
(doğrulandı 2026-10-12):

```python
tempo = librosa.feature.tempo(onset_envelope=onset_env, sr=sr)
print(f"Estimated tempo: {tempo[0]:.2f} BPM")
```

**ffprobe** · https://www.ffmpeg.org/ffprobe.html (doğrulandı 2026-10-12):

```bash
ffprobe -show_streams -select_streams a INPUT
```

**C++ — Essentia standard mode MFCC extractor** ·
https://essentia.upf.edu/howto_standard_extractor.html
(doğrulandı 2026-10-14 · [ORİJİNAL ÖRNEK · derlenmedi]):
Resmi how-to belgesindeki `standard_mfcc.cpp` örneğinden türetilmiştir —
`essentia::init()` sonrası `AlgorithmFactory` ile `MonoLoader` →
`FrameCreator` → `Windowing` → `Spectrum` → `MFCC` zinciri kurulur,
sonuçlar `Pool`'a yazılır, `PoolAggregator` ile ortalanır ve `YamlOutput`
ile dosyaya dökülür.

```cpp
using namespace essentia::standard;

int main(int argc, char* argv[]) {
  string audioFilename = argv[1];
  string outputFilename = argv[2];

  essentia::init();
  Pool pool;

  int sampleRate = 44100;
  int frameSize = 2048;
  int hopSize = 1024;

  AlgorithmFactory& factory = standard::AlgorithmFactory::instance();

  Algorithm* audio = factory.create("MonoLoader",
                                    "filename", audioFilename,
                                    "sampleRate", sampleRate);
  Algorithm* fc    = factory.create("FrameCreator",
                                    "frameSize", frameSize,
                                    "hopSize", hopSize);
  Algorithm* w     = factory.create("Windowing",
                                    "type", "blackmanharris62");
  Algorithm* spec  = factory.create("Spectrum");
  Algorithm* mfcc  = factory.create("MFCC");

  std::vector<Real> audioBuffer;
  audio->output("audio").set(audioBuffer);
  fc->input("signal").set(audioBuffer);

  std::vector<Real> frame, windowedFrame;
  fc->output("frame").set(frame);
  w->input("signal").set(frame);
  w->output("windowedSignal").set(windowedFrame);
  spec->input("signal").set(windowedFrame);

  std::vector<Real> spectrum, mfccCoeffs, mfccBands;
  spec->output("spectrum").set(spectrum);
  mfcc->input("spectrum").set(spectrum);
  mfcc->output("bands").set(mfccBands);
  mfcc->output("mfcc").set(mfccCoeffs);

  audio->compute();

  while (true) {
    fc->compute();
    if (!frame.size()) {
      break;
    }
    if (isSilent(frame)) continue;
    w->compute();
    spec->compute();
    mfcc->compute();
    pool.add("lowlevel.mfcc", mfccCoeffs);
  }

  Pool aggrPool;
  const char* stats[] = { "mean", "var", "min", "max" };
  Algorithm* aggr = AlgorithmFactory::create("PoolAggregator",
                                             "defaultStats",
                                             arrayToVector<string>(stats));
  aggr->input("input").set(pool);
  aggr->output("output").set(aggrPool);
  aggr->compute();

  Algorithm* output = AlgorithmFactory::create("YamlOutput",
                                               "filename", outputFilename);
  output->input("pool").set(pool);
  output->compute();

  delete audio;
  delete fc;
  delete w;
  delete spec;
  delete mfcc;
  delete output;
  essentia::shutdown();
  return 0;
}
```

**C — libsndfile ile WAV okuma (dosya bilgisi + ham örnekler)** ·
https://libsndfile.github.io/libsndfile/api.html
(doğrulandı 2026-10-14 · [ORİJİNAL ÖRNEK · derlenmedi]):
libsndfile API referansındaki `sf_open` / `SF_INFO` / `sf_readf_float`
imzalarından türetilmiştir — okuma modunda `format` alanı sıfırlanır,
diğer alanlar kütüphane tarafından doldurulur; frame-count okuma tamponu
`frames * channels` öğe için ayırır.

```c
#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include <sndfile.h>

int main(int argc, char** argv) {
  if (argc < 2) {
    fprintf(stderr, "usage: %s <in.wav>\n", argv[0]);
    return 1;
  }

  SF_INFO info;
  info.format = 0; /* okuma modunda sf_open öncesi sıfırlanır */
  SNDFILE* file = sf_open(argv[1], SFM_READ, &info);
  if (file == NULL) {
    fprintf(stderr, "error: %s\n", sf_strerror(NULL));
    return 1;
  }
  printf("samplerate: %d  channels: %d  frames: %ld\n",
         info.samplerate, info.channels, (long)info.frames);

  sf_count_t want = info.samplerate > 0 ? info.samplerate : 44100;
  float* data = (float*)malloc((size_t)want * info.channels * sizeof(float));
  if (data == NULL) {
    sf_close(file);
    return 1;
  }

  /* frames cinsinden okuma: tampon (frames * channels) öğe tutmalı */
  sf_count_t got = sf_readf_float(file, data, want);
  if (got > 0) {
    double sumsq = 0.0;
    sf_count_t i;
    for (i = 0; i < got * info.channels; i++)
      sumsq += (double)data[i] * (double)data[i];
    printf("read %ld frames, rms %.6f\n",
           (long)got, sqrt(sumsq / (double)(got * info.channels)));
  }

  free(data);
  sf_close(file);
  return 0;
}
```

**Rust — spectrum-analyzer ile Hann pencere + FFT spektrumu** ·
https://docs.rs/spectrum-analyzer/latest/spectrum_analyzer/
(doğrulandı 2026-10-14 · [ORİJİNAL ÖRNEK · derlenmedi]):
Crate dokümantasyonundaki "Getting started" örneğinin aynısı — örnek uzunluğu
2'nin kuvveti olmalı; `hann_window` → `samples_fft_to_spectrum` (44100 Hz,
`divide_by_N` ölçekleme) → `spectrum.max()` ile en yüksek genlikli frekans.

```rust
use spectrum_analyzer::scaling::divide_by_N;
use spectrum_analyzer::windows::hann_window;
use spectrum_analyzer::{FrequencyLimit, samples_fft_to_spectrum};

// your samples; the length must be a power of two
let samples = vec![0.0; 2048];

let windowed = hann_window(&samples);
let spectrum = samples_fft_to_spectrum(
    &windowed,
    44100,
    FrequencyLimit::All,
    Some(&divide_by_N),
)
.unwrap();

// the loudest frequency in the block
let (frequency, value) = spectrum.max();
```

**JavaScript (tarayıcı) — Web Audio API AnalyserNode ile spektrum** ·
https://developer.mozilla.org/en-US/docs/Web/API/Web_Audio_API/Visualizations_with_Web_Audio_API
(doğrulandı 2026-10-14 · [ORİJİNAL ÖRNEK · derlenmedi]):
MDN "Visualizations with Web Audio API" rehberindeki resmi parçalardan
türetilmiştir — `AnalyserNode.fftSize` (varsayılan 2048), `frequencyBinCount`
fft'in yarısı, veri `Uint8Array`'a kopyalanır; dalga formu için
`getByteTimeDomainData`, frekans barları için `getByteFrequencyData` kullanılır.

```js
const audioCtx = new AudioContext();
const analyser = audioCtx.createAnalyser();

analyser.fftSize = 2048;
const bufferLength = analyser.frequencyBinCount;
const dataArray = new Uint8Array(bufferLength);

// dalga formu (osiloskop) — zaman alanı
analyser.getByteTimeDomainData(dataArray);
canvasCtx.clearRect(0, 0, WIDTH, HEIGHT);
canvasCtx.lineWidth = 2;
canvasCtx.strokeStyle = "rgb(0 0 0)";
canvasCtx.beginPath();
const sliceWidth = WIDTH / bufferLength;
let x = 0;
for (let i = 0; i < bufferLength; i++) {
  const v = dataArray[i] / 128.0;
  const y = v * (HEIGHT / 2);
  if (i === 0) canvasCtx.moveTo(x, y);
  else canvasCtx.lineTo(x, y);
  x += sliceWidth;
}
canvasCtx.lineTo(WIDTH, HEIGHT / 2);
canvasCtx.stroke();

// frekans bar grafiği — frekans alanı
function draw() {
  requestAnimationFrame(draw);
  analyser.getByteFrequencyData(dataArray);
  canvasCtx.fillStyle = "rgb(0 0 0)";
  canvasCtx.fillRect(0, 0, WIDTH, HEIGHT);
  const barWidth = (WIDTH / bufferLength) * 2.5;
  let bx = 0;
  for (let i = 0; i < bufferLength; i++) {
    const barHeight = dataArray[i] / 2;
    canvasCtx.fillStyle = `rgb(${barHeight + 100} 50 50)`;
    canvasCtx.fillRect(bx, HEIGHT - barHeight / 2, barWidth, barHeight);
    bx += barWidth + 1;
  }
}
draw();
```

**Swift — AVAudioEngine ile dosyadan oynatma zinciri** ·
https://developer.apple.com/documentation/avfaudio/avaudioengine.md
(doğrulandı 2026-10-14 · [ORİJİNAL ÖRNEK · derlenmedi]):
Apple resmi dokümantasyonunun `AVAudioEngine` sayfasındaki "Create an engine
for audio file playback" örneğinin aynısı — `AVAudioFile` → `AVAudioPlayerNode`
`attach`/`connect` → `scheduleFile` → `start()`/`play()` akışı.

```swift
let audioFile = /* An AVAudioFile instance that points to file that's open for reading. */
let audioEngine = AVAudioEngine()
let playerNode = AVAudioPlayerNode()

// Attach the player node to the audio engine.
audioEngine.attach(playerNode)

// Connect the player node to the output node.
audioEngine.connect(playerNode,
                    to: audioEngine.outputNode,
                    format: audioFile.processingFormat)

playerNode.scheduleFile(audioFile,
                        at: nil,
                        completionCallbackType: .dataPlayedBack) { _ in
    /* Handle any work that's necessary after playback. */
}

do {
    try audioEngine.start()
    playerNode.play()
} catch {
    /* Handle the error. */
}

playerNode.stop()
audioEngine.stop()
```

**C# — ONNX Runtime C# API ile model çıkarımı** ·
https://onnxruntime.ai/docs/api/csharp/api/Microsoft.ML.OnnxRuntime.InferenceSession.html
(doğrulandı 2026-10-14 · [ORİJİNAL ÖRNEK · derlenmedi]):
`InferenceSession(string modelPath)` kurucusu, `Run(IReadOnlyCollection<NamedOnnxValue>)`
imzası ve `NamedOnnxValue.CreateFromTensor<T>(string, Tensor<T>)` fabrika
yönteminden türetilmiştir — çıktı koleksiyonu `DisposableNamedOnnxValue`
döndürür ve `IDisposable`'dır.

```csharp
using Microsoft.ML.OnnxRuntime;
using Microsoft.ML.OnnxRuntime.Tensors;

using var session = new InferenceSession("model.onnx");

// girdi adı modelin InputNames/InputMetadata alanından okunur
float[] pcm = new float[15600]; // 0.975 sn @ 16 kHz mono örnekleme
var tensor = new DenseTensor<float>(pcm, new[] { 1, 15600 });
var inputs = new List<NamedOnnxValue> {
    NamedOnnxValue.CreateFromTensor("input", tensor)
};

using IDisposableReadOnlyCollection<DisposableNamedOnnxValue> results =
    session.Run(inputs);

foreach (var r in results) {
    var scores = r.AsTensor<float>().ToArray();
    Console.WriteLine($"{r.Name}: {scores.Length} skor, ilk={scores[0]:F4}");
}
```

**Go — go-dsp fft paketi ile gerçek sinyalin FFT'si** ·
https://pkg.go.dev/github.com/mjibson/go-dsp/fft
(doğrulandı 2026-10-14 · [ORİJİNAL ÖRNEK · derlenmedi]):
pkg.go.dev paket dokümantasyonundaki `FFTReal` imzası
(`func FFTReal(x []float64) []complex128` — "returns the forward FFT of the
real-valued slice") ve paket overview'ünden türetilmiştir.

```go
package main

import (
	"fmt"

	"github.com/mjibson/go-dsp/fft"
)

// FFTReal, gerçek değerli dilimin ileri FFT'sini döndürür.
func main() {
	samples := []float64{1, 2, 3, 4, 5, 6, 7, 8}
	spectrum := fft.FFTReal(samples)
	fmt.Println(len(spectrum), spectrum[0])
}
```

## §3 Uygulama notu

- Repo'daki `extractMetadata` bu komutu birebir kullanır; `extractFeatures` ise **placeholder**dır —
  BPM/key değeri gerçeği yansıtmaz.
- Kademe-3'te metrik çıkarımı için araç seçimi ai-master §4 kararı gerektirir (kapsam §5/R2).

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `music-analysis-kapsam.md` | bu dalın rolü, içinde/dışında, açık sorular |
| `music-analysis-dogrulama.md` | iddia → URL/dosya kanıt tablosu |
| `../index.md` · `../ai-master.md` §1/§3 | üst düğüm künyesi + çocuk tablosu (v2 satır 263) |

---

## §5 Kapanış Derinliği — Neva Engine müzik analizi C++ örnek turu (2026-10-15)

### §5.0 Kanıt disiplini

Repo ölçümü (2026-10-15): `*.cpp` / `*.h` = **0 dosya** → aşağıdaki C++ blokları **vault spesifikasyon alıntılarıdır**, derlenmiş kod değildir; her biri `Dosya: … [Satır: n]` ile işaretlenmiştir.

### §5.1 Örnek MA-1 — GenreClassifier (CNN analizcisi, tam okuma)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 267–286].

```cpp
class GenreClassifier {
    Ort::Session session;
    static constexpr int NUM_GENRES = 12;

public:
    struct GenreResult {
        int genreId;
        std::string genreName;
        float confidence;           // 0.0 - 1.0
    };

    std::vector<GenreResult> classify(const float* audio, int numSamples);

    // Desteklenen Türk müzik türleri:
    // 0: Arabesk, 1: Türkü, 2: Pop, 3: Rock, 4: Rap/Hip-Hop
    // 5: Türk Sanat Müziği, 6: Fantezi, 7: Özgün
    // 8: Elektronik/Dans, 9: Caz/Blues, 10: Klasik, 11: Diğer
};
```

**Analiz notları:**

1. Girdi 3 sn sinyal; pipeline: 128 mel band · 256 FFT · 10 ms hop → 128×300 mel-spec → 4×Conv2D → 512-dim embedding → Dense 512→256→12 → Softmax (Dosya: ai-models.md [Satır: 290–320]).
2. Sınıf seti **Türk müziği odaklıdır** (Arabesk · Türkü · TSM · Fantezi · Özgün) — K006 CoreMusic hedef kitlesiyle örtüşür; etiket seti `genres.json` dosyasıyla taşınır [Satır: 548].
3. `classify` birden fazla `GenreResult` döndürür (top-k okunabilir) → eşiğin altında kalan sonuçlar elenmez, çağıran karar verir.

### §5.2 Örnek MA-2 — Rule-based tespit (CNN'siz Tier-1, tam okuma)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` [Satır: 22–59].

```cpp
enum class Genre {
    Arabesk, Pop, Rock, Electronic, Jazz, Classical,
    HipHop, Metal, Folk, Latin, RNB, Ambient, Unknown
};

Genre detectGenre(const float* spectrum, int bins, float sampleRate) {
    // Spectral centroid → brightness
    float centroid = computeSpectralCentroid(spectrum, bins);

    // Bass energy ratio (20-250Hz / total)
    float bassRatio = energyInRange(spectrum, 20, 250, sampleRate)
                    / energyInRange(spectrum, 20, 20000, sampleRate);

    // Mid energy (500Hz-2kHz)
    float midRatio = energyInRange(spectrum, 500, 2000, sampleRate)
                   / energyInRange(spectrum, 20, 20000, sampleRate);

    // High energy (8kHz-16kHz)
    float highRatio = energyInRange(spectrum, 8000, 16000, sampleRate)
                    / energyInRange(spectrum, 20, 20000, sampleRate);

    // Tempo / BPM detection (onset detection)
    float bpm = detectBPM(spectrum, sampleRate);

    // Rhythm regularity (electronic vs live)
    float regularity = computeRhythmRegularity(spectrum);

    // Decision tree
    if (bassRatio > 0.4f && bpm < 100 && midRatio > 0.3f)   return Genre::Arabesk;
    if (bassRatio > 0.35f && bpm > 120 && highRatio > 0.15f) return Genre::Electronic;
    if (midRatio > 0.35f && regularity < 0.3f)                return Genre::Classical;
    if (bpm > 120 && highRatio > 0.1f && bassRatio > 0.3f)   return Genre::Pop;
    if (midRatio < 0.25f && bassRatio > 0.3f && highRatio > 0.1f) return Genre::Rock;
    if (bpm > 80 && bpm < 110 && regularity > 0.5f)           return Genre::HipHop;

    return Genre::Unknown;
}
```

**Analiz notları:**

1. İki sınıf seti **farklıdır**: CNN 12 sınıf [Satır: 270–284], rule-based 13 enum (12 + Unknown) [Satır: 23–26] — kümeler uyuşmaz (rule-based'te Metal var, CNN'de ayrı "Metal" yok; CNN'de "Türkü/Fantezi/Özgün" var, rule-based'te yok). K006 evrensel-dil §5'e "aynı terim, iki küme" uyarısı olarak kayıtlıdır.
2. `detectBPM(spectrum, sampleRate)` girdisi spektrumdur — BPM tipik olarak zaman alanı/spectrogram ister; spec'te imza bu şekilde yazılmıştır, daraltma **yorumlanmaz** (alındığı gibi).
3. `centroid` hesaplanır ama karar ağacında **kullanılmaz** → hesaplanmış, kullanılmamış öznitelik (ölü hesaplama olabilir; K006'da `extractFeatures` implementasyonu için not).

### §5.3 Örnek MA-3 — Tür bazlı EQ referans eğrileri (analiz çıktısı → aksiyon)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/eq-presets.md` [Satır: 14–31] — 16 tür × 7 bant (60Hz · 150Hz · 400Hz · 1kHz · 2.4kHz · 6kHz · 15kHz):

| Tür | 60 | 150 | 400 | 1k | 2.4k | 6k | 15k | Eğri |
|---|---|---|---|---|---|---|---|---|
| Arabesk | +2 | +3 | +1 | +2 | +4 | +2 | +1 | Warm vocal forward |
| Arabesk Slow | +3 | +2 | +2 | +1 | +3 | +1 | 0 | Deep emotional |
| Pop Turkish | +3 | +1 | 0 | +1 | +3 | +3 | +3 | Modern bright |
| Pop Dance | +5 | +2 | 0 | +1 | +2 | +3 | +4 | Bass heavy bright |
| Rock | +2 | 0 | −1 | −2 | +3 | +3 | +2 | Mid scoop |
| Metal | +2 | 0 | −2 | −3 | +4 | +4 | +3 | Aggressive |
| Electronic | +5 | +2 | +1 | 0 | +2 | +3 | +4 | Club |
| HipHop | +5 | +3 | +1 | +1 | +2 | +2 | +2 | Bass power |
| Jazz | +1 | +2 | +1 | 0 | −1 | −1 | −2 | Warm mellow |
| Classical | 0 | 0 | 0 | 0 | 0 | 0 | −1 | Natural flat |
| Folk | +1 | +2 | +1 | +1 | 0 | −1 | 0 | Natural warm |
| R&B | +4 | +2 | +1 | +1 | +1 | +2 | +2 | Smooth |
| Latin | +3 | +2 | +1 | +2 | +2 | +2 | +2 | Rhythmic |
| Reggae | +4 | +2 | +1 | +1 | 0 | +1 | +1 | Bass-line |
| Blues | +2 | +2 | +1 | +1 | 0 | −1 | −1 | Vintage warm |
| Ambient | +2 | +1 | 0 | 0 | −1 | 0 | +1 | Atmospheric |

**Analiz notları:**

1. Bu tablo `detectGenre` çıktısı ile doğrudan eşleşir (Tür enum'u ↔ tablo satırı) — analiz → aksiyon zincirinin tek kaynaklı kanıtı.
2. 5-band genişletilmiş EQ'da (eq-ai-auto.md [Satır: 66–81]) 14 satır × 5 bantlık **farklı** bir tablo daha vardır — iki tablo farklı çözünürlükte aynı amaçtır; K006'da hangisinin referans alınacağı kararı `../yapay-zeka-kararlar.md` §H/H2 kapısındadır.
3. Değerler dB ve ±6 dB aralığındadır; 31-bant EQ'nun ±18 dB aralığıyla (equalizer-31band.md [Satır: 64]) uyumludur (refererans eğrisi ±6'yı aşmaz).

### §5.4 Örnek MA-4 — Auto-EQ tam akış (analiz → uygulama, tam blok)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 324–351].

```text
[1] Ses Sinyali (5 sn analiz penceresi)
    │
    ▼
[2] Genre Classification (CNN) → "Arabesk" (%87)
    │
    ▼
[3] Genre-Specific Reference Curve Yükle
    Arabesk Hedef: +4dB @ 200Hz, -2dB @ 500Hz, +3dB @ 3kHz
    │
    ▼
[4] FFT Analizi (4096-point, Hann, %50 overlap)
    │
    ▼
[5] Spektrum Karşılaştırma
    error(f) = target(f) - current(f)
    │
    ▼
[6] EQ Bant Optimizasyonu
    Her bant için: optimal gain = minimize(error at band freq)
    │
    ▼
[7] Bant Değerlerini Uygula (EQ zincirine yaz)
    │
    ▼
[8] Kullanıcı Onayı (opsiyonel)
    "Auto-EQ: Arabesk profili uygulandı. Onaylıyor musunuz?"
```

**Analiz notları:**

1. Analiz penceresi 5 sn'dir [Satır: 325]; CNN girdisi 3 sn'dir (§5.1) → iç içe iki pencere, farklıdır; karıştırılmamalı.
2. FFT parametresi sabittir: 4096-point · Hann · %50 overlap [Satır: 335] — bu parametreler K006 `extractFeatures` tasarımının doğrudan girdisidir (repo'daki placeholder'ın yerine geçecek somut şartname).
3. Adım [8] "opsiyonel" kullanıcı onayı — `minConfidenceForApply: 0.6` [Satır: 362] eşiğiyle birlikte okunur: eşik altı uygulama yok, üstünde onay politikası yapılandırma ile belirlenir.

### §5.5 Örnek MA-5 — Analiz gate'leri (K006 için)

| # | Gate | Eşik | Kaynak |
|---|---|---|---|
| MA-G1 | Genre tahmini CPU RPi5 | ≤ 0.3s (1 dk ses) | ai-models.md [Satır: 730] |
| MA-G2 | Genre tahmini GPU | ≤ 0.02s | [Satır: 719] |
| MA-G3 | Uygulama eşiği | confidence ≥ 0.6 | [Satır: 362] |
| MA-G4 | Analiz penceresi | 5 sn (auto-EQ) / 3 sn (CNN) | [Satır: 325] · [Satır: 291] |
| MA-G5 | FFT sabiti | 4096 · Hann · %50 overlap | [Satır: 335] |

Bu turda **hiçbiri çalıştırılmadı** → `../yapay-zeka-dogrulama.md` §7'ye taşındı.

### §5.6 §5 tur kapanış kaydı

- Eklenen: 2 tam analizcisi okuması (MA-1 · MA-2) + 16 satır EQ referans tablosu + 1 tam akış bloğu + 5 gate.
- Uydurma identifier: 0 · ölçü uydurma: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 1 (rule-based vs CNN sınıf kümesi uyuşmazlığı — iki referans tablo kararı).
- Bu ek `*-anayasa.md` §2 (ilkeler) ile çelişmez; `extractFeatures` placeholder gerçeği §3'te korunur.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
