---
title: "audio-intelligence — Örnekler"
type: architecture-examples
node_id: K006-yapay-zeka/audio-intelligence
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# audio-intelligence — Örnekler

## §1 Gerçek repo kodu (ÖNCELİK 1 — bu oturumda okunarak doğrulandı)

`shared/src/Contracts/AI/AIEngineInterface.php` satır 30–36 — ses analizi sözleşmesi:

```php
/**
 * Ses dosyasını analiz eder (BPM, key, energy, mood).
 *
 * @param string $filePath Ses dosyası yolu
 * @return array{bpm: float, key: string, energy: float, mood: string, danceability: float, acousticness: float}
 */
public function analyzeAudio(string $filePath): array;
```

`shared/src/AI/AIEngine.php` satır 315–326 — özellik çıkarımı **henüz placeholder**:

```php
private function extractFeatures(string $filePath): array
{
    // Gerçek implementasyonda C++ DSP engine ile yapılacak
    // Şimdilik placeholder değerler
    return [
        'bpm'          => 120.0,
        'key'          => 'C',
        'energy'       => 0.7,
        'mood'         => 'happy',
        'danceability' => 0.6,
        'acousticness' => 0.3,
    ];
}
```

`shared/src/AI/AIEngine.php` satır 291 — metadata çıkarma ffprobe ile:

```php
'ffprobe -v quiet -print_format json -show_format -show_streams "%s"',
```

> Örnek 2 ve 3 gerçek koddur; sabit değerli `extractFeatures` bir **eksiklik kanıtıdır**, çalışan özellik
> çıkarımı **yoktur**.

## §2 Resmi doküman örneği (URL + snippet)

**librosa — feature extraction** · https://librosa.org/doc/latest/api/feature.html (doğrulandı 2026-10-12):

```python
# 3. Run the default beat tracker
tempo, beat_frames = librosa.beat.beat_track(y=y, sr=sr)
print('Estimated tempo: {:.2f} beats per minute'.format(tempo))
```

**Essentia (MTG/UPF)** · https://essentia.upf.edu/documentation.html (doğrulandı 2026-10-12):

> "Essentia is an open-source C++ library with Python and JavaScript bindings for audio analysis and
> audio-based music information retrieval."

### §2.1 Dil kapsamı genişlemesi — 13-dil Faz 1 (ek 2026-10-14)

> Her blok: resmi kaynak belge `webfetch` ile açıldı → HTTP 200 → kod belgeden türetildi
> (belgede olmayan satır yazılmadı). Tüm bloklar `[ORİJİNAL ÖRNEK · derlenmedi]` — derleme
> çalıştırılmadı, yalnız resmi doküman alıntısıdır.

**C++ · Essentia — MFCC extractor (standard mode)** · https://essentia.upf.edu/documentation/howto_standard_extractor.html (doğrulandı 2026-10-14) — `[ORİJİNAL ÖRNEK · derlenmedi]`:

```cpp
using namespace essentia::standard;

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
```

**Rust · cpal — düşük seviye çapraz platform ses I/O** · https://docs.rs/cpal (doğrulandı 2026-10-14) — `[ORİJİNAL ÖRNEK · derlenmedi]`:

```rust
use cpal::traits::{DeviceTrait, HostTrait, StreamTrait};

let host = cpal::default_host();
let device = host.default_output_device().expect("no output device available");

let stream = device.build_output_stream(
    config,
    move |data: &mut [f32], _: &cpal::OutputCallbackInfo| {
        // react to stream events and read or write stream data here.
    },
    move |err| {
        // react to errors here.
    },
    None,
);

stream.play().unwrap();
```

**JavaScript · Transformers.js — tarayıcıda Whisper ASR** · https://huggingface.co/docs/transformers.js/api/pipelines (doğrulandı 2026-10-14) — `[ORİJİNAL ÖRNEK · derlenmedi]`:

```javascript
import { pipeline } from '@huggingface/transformers';

const transcriber = await pipeline('automatic-speech-recognition', 'Xenova/whisper-tiny.en');
const url = 'https://huggingface.co/datasets/Xenova/transformers.js-docs/resolve/main/jfk.wav';
const output = await transcriber(url);
// { text: " And so my fellow Americans ask not what your country can do for you, ask what you can do for your country." }
```

**Java · ONNX Runtime JVM binding — oturum + çalıştırma** · https://onnxruntime.ai/docs/get-started/with-java.html (doğrulandı 2026-10-14) — `[ORİJİNAL ÖRNEK · derlenmedi]`:

```java
var env = OrtEnvironment.getEnvironment();
var session = env.createSession("model.onnx", new OrtSession.SessionOptions());

OnnxTensor t1, t2;
var inputs = Map.of("name1", t1, "name2", t2);
try (var results = session.run(inputs)) {
    // manipulate the results
}
```

**C# · ONNX Runtime — OrtValue tabanlı çıkarım** · https://onnxruntime.ai/docs/tutorials/csharp/basic_csharp.html (doğrulandı 2026-10-14) — `[ORİJİNAL ÖRNEK · derlenmedi]`:

```csharp
using var session = new InferenceSession("model.onnx");

float[] sourceData;  // assume your data is loaded into a flat float array
long[] dimensions;   // and the dimensions of the input is stored here

// Create a OrtValue on top of the sourceData array
using var inputOrtValue = OrtValue.CreateTensorValueFromMemory(sourceData, dimensions);

var inputs = new Dictionary<string, OrtValue> {
    { "name1",  inputOrtValue }
};

using var runOptions = new RunOptions();

// Pass inputs and request the first output
using var output = session.Run(runOptions, inputs, session.OutputNames[0]);

var output_0 = output[0];

// Returns Span<float> which points directly to native memory.
var outputData = output_0.GetTensorDataAsSpan<float>();
```

**Swift · AVAudioEngine — Apple ses motoru grafiği** · https://developer.apple.com/documentation/avfaudio/avaudioengine (doğrulandı 2026-10-14; HTML yerine aynı sayfanın resmi DocC JSON endpoint'i `developer.apple.com/tutorials/data/documentation/avfaudio/avaudioengine.json` üzerinden — HTTP 200) — `[ORİJİNAL ÖRNEK · derlenmedi]`:

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
```

**C · ONNX Runtime C API — çağrı sırası (belgedeki adımlar)** · https://onnxruntime.ai/docs/get-started/with-c.html (doğrulandı 2026-10-14) — `[ORİJİNAL ÖRNEK · derlenmedi]`:

```c
/* onnxruntime_c_api.h — resmi belgedeki API çağrı sırası:
 * 1. Include onnxruntime_c_api.h
 * 2. Call OrtCreateEnv
 * 3. Create Session: OrtCreateSession(env, model_uri, nullptr, ...)
 * 4. Create Tensor: 1) OrtCreateMemoryInfo 2) OrtCreateTensorWithDataAsOrtValue
 * 5. OrtRun
 */
```

## §3 Uygulama notu

- Her iki kütüphane de **adaydır**; CoreMusic'in hangisini kullanacağı `UNKNOWN` (kapsam §5/R3).
- Repo'da çalışan özellik çıkarımı yok → kademe-3'te önce `extractFeatures` sahiplenilmelidir.

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `audio-intelligence-kapsam.md` | bu dalın rolü, içinde/dışında, açık sorular |
| `audio-intelligence-dogrulama.md` | iddia → URL/dosya kanıt tablosu |
| `../index.md` · `../ai-master.md` §1/§3 | üst düğüm künyesi + çocuk tablosu (v2 satır 264) |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12

---

## §5 Kapanış Derinliği — Neva Engine AI ses zekâsı C++ örnek turu (2026-10-15)

### §5.0 Kanıt disiplini (bu bölüm için bağlayıcı)

- Bu bölümdeki tüm C++ blokları **vault spesifikasyon alıntılarıdır**, derlenmiş kod değildir.
- Repo ölçümü (2026-10-15, bu tur): depoda `*.cpp` / `*.h` = **0 dosya** → "repo'da çalışan C++ AI kodu var" iddiası **yasaktır**; her örneğin kaynağı bir `.ai/projects/NevaEngine/**` md satırıdır ve `Dosya: … [Satır: n]` ile işaretlenir.
- Örnekler `*-bilesenler.md` (repo PHP bileşenleri) ile **karşılaştırma katmanı**dır: Neva spec'inin C++ yüzü ↔ CoreMusic'in PHP yüzü.

### §5.1 Örnek AI-1 — AudioSRProcessor (batch upscaling sınıflandırıcısı, tam okuma)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 76–95] — sınıf orada birebir tanımlıdır.

```cpp
class AudioSRProcessor {
    Ort::Session session;               // ONNX session
    Ort::MemoryInfo memoryInfo;
    std::vector<Ort::Value> inputTensors;
    std::vector<Ort::Value> outputTensors;

public:
    struct Config {
        int inputSampleRate = 24000;
        int outputSampleRate = 48000;
        int chunkSize = 65536;          // 2^16 sample parça
        int overlapSize = 8192;         // %12.5 overlap
    };

    bool initialize(const std::string& modelPath, const Config& config);
    std::vector<float> upscale(const float* audio, int numSamples);
    float getProgress() const;          // 0.0 - 1.0
};
```

**Okuma notları (ses mühendisi perspektifi):**

1. `chunkSize = 65536` sabiti anlamlıdır: 48 kHz'te ≈ 1.36 sn ses; modelin eğitim penceresiyle eşleşmek zorundadır. `Config` alanları `initialize` sonrası **değiştirilemez** varsayılır (değişirse tensor shape kırılır — bkz. §5.6 risk tablosu).
2. `overlapSize = 8192` (%12.5) **overlap-add** zorunluluğunun kanıtıdır: aynı kaynak dosyanın §3.2 akış diyagramında "Overlap-add (chunk birleştirme)" adımı [Satır: 178] ve §2.1 karşılaştırma tablosunda CPU süresi "1 dk ses" bazında verilmiştir [Satır: 61–72].
3. `getProgress()` ara döndürme sözleşmesidir → K006 karşılığı PHP'de job durumudur: `shared/src/AI/AIWorkflow.php` [Satır: 49 `recommendationWorkflow` … Satır: 241 `faultPredictionWorkflow`] arasında eşzamanlı bir upsert/progress API'si **yoktur** (grep 2026-10-12: 5 workflow metodu). Fark `⚠️ VERIFICATION REQUIRED` olarak kayıt altındadır.
4. VRAM/Kalite beklentisi spesifikte tablolanmıştır: AudioSR FP16 4.0 GB · INT8 2.1 GB · GPU 12s/1dk ses · MOS 4.5 (Dosya: ai-models.md [Satır: 63] ve [Satır: 723]).

### §5.2 Örnek AI-2 — DeepFilterNet2Processor (gerçek zamanlı denoise, tam okuma)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 104–121].

```cpp
class DeepFilterNet2Processor {
    Ort::Session session;
    std::vector<float> stateBuffer;     // RNN state buffer

public:
    // Gerçek zamanlı işleme (10 ms buffer'lar halinde)
    float processSample(float inputSample);

    // Batch işleme
    void processBlock(const float* input, float* output, int numSamples);

    struct Config {
        float attenuationLimit = 12.0;  // dB, maksimum gürültü azaltma
        bool preserveSpeech = true;     // Konuşma frekanslarını koru
    };
};
```

**Okuma notları:**

1. `stateBuffer` alanı, modelin **rekürsif (RNN) durum** taşıdığını kanıtlar → bu buffer audio callback dışında, prepare-to-play evresinde allocate edilmelidir. CoreMusic'in lock-free prensibiyle uyumlu okuma: Dosya: `.ai/projects/NevaEngine/legacy-2026-07/audio-core.md` [Satır: 21–27] (prensip tablosu: Lock-free · Pre-allocation · No exception).
2. İki API yüzeyi (`processSample` 10 ms · `processBlock`) sınıflandırma tablosundaki "Real-Time (CPU) < 10 ms" satırıyla tutarlıdır (Dosya: ai-models.md [Satır: 429]).
3. `attenuationLimit = 12.0 dB` K006 doğrulama testinde **ölçülebilir tekil** bir eşiktir: bir kaydın işlenmiş çıktısında bant-noise azaltması 12 dB'i aşamaz; test-stratejisi §5'e taşınan somut kabul kriteri (bkz. `../yapay-zeka-test-stratejisi.md` §8).

### §5.3 Örnek AI-3 — GenreClassifier (12 sınıf · CNN · tam okuma)

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

**Okuma notları:**

1. Sınıf sayısı sabittir (`NUM_GENRES = 12`) ve sınıf etiketleri yorum satırında **açıkça listelenmiştir** — etiket seti uydurulmaz, alınır. Sınıf sırası modelin `genres.json` dosyasıyla eşleşmek zorundadır (dosya ağacı kanıtı: ai-models.md [Satır: 546–548] — `genre-cnn/model_fp32.onnx (50 MB)` + `genres.json`).
2. Girdi penceresi 3 sn'dir; mimari diyagram 128 mel band · 256 FFT · 10 ms hop · 4×Conv2D · 512-dim embedding · Dense 512→256→12 · Softmax (Dosya: ai-models.md [Satır: 290–320]).
3. Çıktı `confidence` alanı K006'daki `MIN_SCORE` eşiğiyle doğrudan ilişkilidir: repo PHP'sinde `private const MIN_SCORE = 0.7` (Dosya: `shared/src/AI/AIEngine.php` [Satır: 31]) — Neva spec'inde Auto-EQ için eşik `minConfidenceForApply: 0.6` (Dosya: ai-models.md [Satır: 362]). İki eşik **farklıdır ve çelişmez** (biri öneri filtresi, diğeri EQ uygulama eşiği); K006 evrensel-dil'e iki ayrı terim olarak kayıtlıdır.
4. Rule-based alternatif (CNN'siz Tier-1): 6 öznitelik (centroid · bassRatio · midRatio · highRatio · bpm · regularity) + karar ağacı — Dosya: `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` [Satır: 22–59]. Hibrit mimari gereği CNN düşük güvenliyken (`confidence < 0.6`) karar ağacına düşer.

### §5.4 Örnek AI-4 — AudioQualityMetrics (kalite kapısı, tam okuma)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 592–615].

```cpp
class AudioQualityMetrics {
public:
    struct MetricResults {
        float pesq;
        float stoi;
        float si_sdr;
        float snr;
        float psnr;
        float lsd;
        float mcd;
        float compositeMos;     // Ağırlıklandırılmış MOS tahmini

        std::string summary();
    };

    static MetricResults compute(
        const float* reference,
        const float* processed,
        int numSamples,
        int sampleRate
    );
};
```

**Okuma notları:**

1. `compute` **static** ve referans/çıktı çifti alır → regresyon testinde "aynı girdi, iki model çıktısı" karşılaştırmasına doğrudan oturur. Metrik hedefleri tabloda sabittir (Dosya: ai-models.md [Satır: 579–588]): PESQ > 3.5 · STOI > 0.85 · SI-SDR > 15 dB · SNR > 30 dB · LSD < 2.0 dB · MCD < 3.0 dB.
2. `compositeMos` ağırlıklandırma formülü spesifikte **yazılmamıştır** → ağırlıklar `⚠️ VERIFICATION REQUIRED` (uydurma yok).
3. Bu sınıf K006 evaluation dalının C++ ayağıdır; PHP ayağında karşılığı repo'da **yoktur** (grep 2026-10-12: `shared/src/AI/` içinde pesq/stoi/si_sdr = 0 eşleşme) → sahiplik: evaluation dalı, madde E-kapatış.

### §5.5 Örnek AI-5 — Upload ön-işleme karar tablosu (kural matrisi)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 408–418] — 7 satırlık dosya-durum → aksiyon matrisi:

| Dosya Durumu | Aksiyon |
|---|---|
| 48 kHz FLAC/WAV | Sadece loudness normalize |
| 44.1 kHz FLAC | 48 kHz upsample (libsamplerate) |
| 320 kbps MP3 | Decode → loudness normalize |
| 128 kbps MP3 | Decode → AI upscale (AudioSR) → normalize |
| 8 kHz mono (telefon) | AI upscale (BSRNN) → stereo genişletme → normalize |
| Gürültülü kayıt | AI denoising (DeepFilterNet2) → normalize |
| Clipping/bozuk | AI restorasyon (VoiceFixer) → normalize |

**Okuma notları:**

1. Tablo **deterministiktir** — tahmin içermez; K006'da "audio-intelligence ne zaman devreye girer" sorusunun tek kaynaklı cevabıdır (kapsam §5/R4 ile aynı soru).
2. Normalizasyon hedefi `-16 LUFS` olarak job konfigürasyonunda geçer (Dosya: ai-models.md [Satır: 471] `normalizeTarget: -16`) ve genel config'te tekrarlanır [Satır: 646] → iki yerde aynı değer, tutarlılık kanıtı.
3. `libsamplerate` adı geçen tek açık-kaynak bağımlılıktır; lisansı GPL'dir (Neva Engine GPLv3 ile uyumlu — Dosya: ai-models.md [Satır: 738]). K006'da ithalat öncesi lisans kapısı: `../yapay-zeka-kararlar.md` §H/H6.

### §5.6 Örnek AI-6 — Sınıf-tasarım risk okuması (kod inceleme turu)

| # | Gözlem | Kanıt | K006 karşılığı |
|---|---|---|---|
| 1 | `ONNXInferenceEngine::sessions` map'i `std::unordered_map<std::string, Ort::Session>` — `Ort::Session` move-only'dir; map değer olarak tutulması **yeniden yerleştirme** gerektirir | ai-models.md [Satır: 133] + [Satır: 147–148] | model-management-ornekler §4 (registry yaşam döngüsü) |
| 2 | `unloadModel(modelId)` sözleşmesi var ama kilit/atomik yok → çok thread'li kullanımda veri yarışı riski | ai-models.md [Satır: 148] | riskler R-yeni (concurrency) |
| 3 | `quantizeModel` / `optimizeModel` tek dosya → tek dosya sözleşmesi; batch dönüşüm API'si yok | ai-models.md [Satır: 150–154] | model-management §3 |
| 4 | `InferenceJobQueue::workers` vektörde 4 worker; join/detach görünmüyor → yaşam döngüsü belirsiz | ai-models.md [Satır: 450] | test-stratejisi §8 G-kapanış |
| 5 | `GenreClassifier::classify` thread-dışı çağrılmış varsayımı (state yok) ama `DeepFilterNet2Processor` state taşır → iki modelin eşzamanlılık sözleşmesi **farklıdır** | ai-models.md [Satır: 269–279] vs [Satır: 106–113] | evrensel-dil §5 (aynı terim farklı semantik uyarısı) |

> Hiçbir gözlem "repo kodu bozuk" iddiası değildir: bunlar **spesifikasyon metni** içindir ve K006 kademe-3 tasarım girdisidir.

### §5.7 Ölçüm referansları (bu dalın benchmark tabanı)

| Model | 1 dk ses (RTX 4090 FP16) | Throughput | VRAM | MOS | Kaynak |
|---|---|---|---|---|---|
| DeepFilterNet2 | 0.3s | 200× real-time | 0.6 GB | 4.1 | ai-models.md [Satır: 717] |
| HiFi-GAN | 0.5s | 120× | 1.0 GB | 4.0 | [Satır: 718] |
| Genre CNN | 0.02s | 3000× | 0.05 GB | — | [Satır: 719] |
| Demucs HT | 3.0s | 20× | 2.7 GB | 4.3 | [Satır: 720] |
| Resemble Enhance | 2.5s | 24× | 2.0 GB | 4.2 | [Satır: 721] |
| BSRNN (INT8) | 3.5s | 17× | 0.8 GB | 3.9 | [Satır: 722] |
| AudioSR | 5.0s | 12× | 4.0 GB | 4.5 | [Satır: 723] |
| NuWave2 | 6.0s | 10× | 3.0 GB | 4.4 | [Satır: 724] |

Raspberry Pi 5 (CPU INT8) karşılaştırması: Genre CNN 0.3s/200× · DeepFilterNet2 18.0s/3.3× · BSRNN 45.0s/1.3× · AudioSR **çalışmaz** (Dosya: ai-models.md [Satır: 728–733]). Bu satır, "model ↔ donanım uygunluk matrisi"nin kanıtıdır; K006 lokal-inference dalı bunu deployment kapısına bağlar.

### §5.8 §5 tur kapanış kaydı

- Eklenen örnek: 6 sınıf-tam okuma + 1 karar tablosu + 5 satır risk okuması + 2 benchmark tablosu.
- Uydurma identifier: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 2 (compositeMos ağırlıkları · progress API'si).
- Bu ek bölüm `*-anayasa.md` §2 (ilkeler) ile çelişmez; `*-bilesenler.md` §4/§9 (PHP envanteri) tamamlar — ikisi ayrı katmandır (C++ spec ↔ PHP uygulama).

---

## §6 Karşılaştırma Katmanı — PHP repo yüzü ↔ Neva C++ spec yüzü (2026-10-15)

> Bu bölüm `kod` grubu dosyanın ikinci ayağıdır: Neva spec'indeki AI ses zekâsı sözleşmelerinin,
> CoreMusic repo'sunda **gerçekten var olan** PHP karşılıklarını satır kanıtıyla eşler. Eşleşmeyen
> her satır "eksik" değil, **farklı katman** olarak işaretlenir (spec ≠ uygulama).

### §6.1 Eşleme matrisi (Dosya · Satır ↔ sözleşme)

| Sözleşme (Neva spec) | Spec kanıtı | PHP karşılığı | PHP kanıtı | Durum |
|---|---|---|---|---|
| Sınıflandırma sonucu üretimi (`GenreResult`) | ai-models.md [Satır: 273–277] | `getRecommendations(array $context, int $limit = 20): array` | AIEngine.php [Satır: 49] | ⚠️ farklı katman (öneri ≠ tür) |
| Kalite metriği (`AudioQualityMetrics::compute`) | ai-models.md [Satır: 608–613] | — | grep 2026-10-15: `shared/src/AI/` içinde `pesq\|stoi\|si_sdr\|lsd\|mcd` = 0 eşleşme | ❌ PHP'de yok (tasarım boşluğu) |
| Ses analizi girişi | ai-models.md [Satır: 386–406] upload pipeline | `analyzeAudio(string $filePath): array` | AIEngine.php [Satır: 82] | ✅ imza var; içerik `UNKNOWN` (metadata-only) |
| EQ bandı frekansı | equalizer-31band.md [Satır: 32–41] ISO 1/3 oktav | `bandToFrequency(int $band): float` | AIEngine.php [Satır: 332] | ✅ bant eşlemesi iki katmanda da var |
| Oda düzeltmesi | eq-ai-auto.md [Satır: 128–143] inverse filter | `calculateRoomCorrection(float $frequency, string $roomSize, float $reverb, array $frequencyResponse): float` | AIEngine.php [Satır: 347] | ✅ karşılık var (farklı girdi seti) |
| Bant Q faktörü | equalizer-31band.md [Satır: 71–78] Q tablosu | `calculateBandQ(float $frequency, string $roomSize): float` | AIEngine.php [Satır: 368] | ✅ karşılık var |
| Model registry | ai-models.md [Satır: 506–525] `ModelRegistry` | — | grep: `shared/src/AI/` içinde `registry\|onnx` = 0 eşleşme | ❌ PHP'de yok (K006.5 sahipliğinde) |
| Job kuyruğu | ai-models.md [Satır: 437–456] `InferenceJobQueue` | — | grep: `queue\|job` = 0 eşleşme (AI/ altında) | ❌ PHP'de yok (planlama gerektirir) |
| Batch çıktı normalizasyonu | ai-models.md [Satır: 471] `normalizeTarget: -16` | — | grep: `loudness\|lufs\|normalize` = 0 eşleşme | ❌ PHP'de yok |

**Sayım:** 9 satır · ✅ 4 · ❌ 4 · ⚠️ 1. ❌ satırlar K006 backlog'una **yeni madde** olarak girer (kapsam §6'da P-madde numarası atanması gerekir — `⚠️ VERIFICATION REQUIRED`: bu tur atama yapmadı).

### §6.2 PHP yüzeyinin AI ses zekâsı sözleşmesi (tam envanter, grep kanıtlı)

`shared/src/AI/AIEngine.php` — ses/zekâ ile ilgili 11 public/private metot (2026-10-15 grep):

| # | İmza | Satır | Ses zekâsı ilişkisi |
|---|---|---|---|
| 1 | `getRecommendations(array $context, int $limit = 20): array` | 49 | dolaylı (dinleme bağlamı) |
| 2 | `analyzeAudio(string $filePath): array` | 82 | **doğrudan** |
| 3 | `optimizeEQ(array $roomProfile): array` | 111 | **doğrudan** |
| 4 | `analyzeHardware(string $deviceId): array` | 138 | sınır (donanım ≠ ses) |
| 5 | `predictFault(array $metrics): array` | 161 | sınır (donanım sağlığı) |
| 6 | `getCollaborativeScores(?string $userId, array $history): array` | 209 | ses değil |
| 7 | `getContentBasedScores(array $context): array` | 223 | dolaylı |
| 8 | `combineScores(...)` | 243 | ses değil |
| 9 | `applyDiversityFilter(array $scores, ?string $preferredGenre): array` | 264 | **tür bilgisi taşır** |
| 10 | `extractMetadata(string $filePath): array` | 288 | **doğrudan** (metadata) |
| 11 | `extractFeatures(string $filePath): array` | 315 | **doğrudan** — placeholder (bkz. §6.3) |

`shared/src/AI/AIWorkflow.php` — 5 workflow (grep 2026-10-15): `recommendationWorkflow` [Satır: 49] · `audioAnalysisWorkflow` [Satır: 99] · `autoEQWorkflow` [Satır: 147] · `hardwareAnalysisWorkflow` [Satır: 199] · `faultPredictionWorkflow` [Satır: 241].

### §6.3 Placeholder gerçeği (tek nokta — tekrar değil, derinlik)

`extractFeatures` (AIEngine.php [Satır: 315]) K006'nın en kritik tek satırıdır:
Neva spec'i feature çıkarımını tanımlar (mel-spec · centroid · band enerjisi — ai-models.md [Satır: 290–320] ve eq-ai-auto.md [Satır: 28–48]),
PHP yüzeyinde ise **gerçekleştirilmemiştir**. Sonuç: `getCollaborativeScores`/`getContentBasedScores`
[Satır: 209 · 223] dolaylı olarak bu placeholder'a bağımlıysa, tüm öneri kalitesi üstünde
`⚠️ VERIFICATION REQUIRED` kalır. Kapanış kararı bu dosyanın konusu değil → `../yapay-zeka-kararlar.md`
§H/H4 kuralı ("placeholder kod üretim sayılmaz") devreye girer.

### §6.4 C++ spec'ten türetilmiş kabul kriterleri (audio-intelligence için)

| # | Kriter | Ölçüm | Kanıt kökü |
|---|---|---|---|
| AK-1 | Genre CNN tahmini 3 sn pencerede RTX'te ≤ 20 ms | 1 dk / 0.02s ÷ 60 | ai-models.md [Satır: 719] |
| AK-2 | Genre CNN RPi5 (INT8) ≤ 300 ms | 0.3s ölçümü | ai-models.md [Satır: 730] |
| AK-3 | Auto-EQ uygulama eşiği `confidence ≥ 0.6` | config | ai-models.md [Satır: 362] |
| AK-4 | Denoise azaltma üst sınırı 12 dB (aşım = kalite bozulması) | `attenuationLimit` | ai-models.md [Satır: 117] |
| AK-5 | Normalizasyon hedefi −16 LUFS (tutarlı, iki konumda aynı) | job + global config | ai-models.md [Satır: 471] · [Satır: 646] |
| AK-6 | Chunk 65536 / overlap 8192 → sınırda artefakt yok (smearing testi) | `Config` | ai-models.md [Satır: 87–88] |

> AK-1…AK-6 bu turda **sadece tanımlandı; çalıştırılmadı** → `../yapay-zeka-dogrulama.md` §7'ye
> "çalıştırılmadı" statüsüyle taşındı (uydurma ölçüm yok).

---

## §7 Nesne yaşam döngüsü okuması — Ort::Session sahipliği (2026-10-15)

### §7.1 Sahiplik modeli (spec metninden çıkarım, atıfsız iddia yok)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 130–155] — `ONNXInferenceEngine`
sınıfının alanları: `Ort::Env env` [Satır: 131] · `Ort::SessionOptions sessionOptions` [Satır: 132] ·
`std::unordered_map<std::string, Ort::Session> sessions` [Satır: 133].

`Ort::Session` ONNX Runtime C++ API'de move-only bir sarmalayıcıdır (kopyalama yok) — bu, spesifikte
`unordered_map` değer tipi olarak kullanılmasının **move-assign** gerektirdiği anlamına gelir.
Spec metni `loadModel`/`unloadModel` [Satır: 147–148] için kilit kelime içermiyor → eşzamanlılık
sözleşmesi belirsizdir. K006 çıkarımı:

1. `loadModel` yalnızca tek yönetim (control) thread'inde çağrılabilir; audio callback'ten asla.
2. `unloadModel` çağrısından önce ilgili oturumun tüm işlerinin bitmesi gerekir (drain); drain mekanizması spec'te tanımlı değil → `⚠️ VERIFICATION REQUIRED`.
3. Session map'i hazırlandıktan sonra **salt-okunur** varsayımı en güvenli okumadır; runtime'da model değişimi = oturum değiştirme + atomik gösterge (atomic pointer swap) ister.

### §7.2 Katman sınırı tablosu (ses callback ↔ AI katmanı)

| Katman | Sahip | Thread | AI'ye erişim |
|---|---|---|---|
| ASIO/WASAPI callback | audio-core.md [Satır: 106–128] | gerçek zamanlı | **yok** (yalnız atomik sayaç/peak) |
| DSP zinciri (EQ/comp) | equalizer-31band.md [Satır: 88–115] | gerçek zamanlı | batch sonucu okur (atomic swap) |
| AI batch motoru (ONNX) | ai-models.md [Satır: 130–155] | worker (4 thread) | queue üzerinden job alır |
| Job kuyruğu | ai-models.md [Satır: 437–456] | yönetimsel | — |

Audio callback'te `Ort::Session::Run` çağrısı bu tabloya göre **yasaktır** (RT ihlali); yalnız
`< 10 ms` sınıfı modeller (DeepFilterNet2 INT8 · Genre CNN) ayrı bir "realtime yardımcı" thread'inde
çalışıp sonucu atomik paylaşabilir — bu okuma spesifikteki kategori tablosuyla [Satır: 426–432] tutarlıdır.

### §7.3 Hata modu tablosu (ses zekâsı bileşenleri)

| Bileşen | Hata modu | Kullanıcı etkisi | Azaltma |
|---|---|---|---|
| ONNX EP seçimi başarısız | sessiyon kurulamaz | AI kapalı, ses normal akar | CPU'ya düş (spec: `ExecutionProvider::CPU` [Satır: 137]) |
| VRAM yetersiz | yükleme hatası | batch işler kuyrukta kalır | INT8 varyant (tablo [Satır: 61–72]) |
| Model dosyası bozuk (checksum) | initialize false | iş RED | SHA256 doğrulama (registry [Satır: 516]) |
| Chunk sınırında artefakt | click/smearing | kalite şikayeti | overlap-add (akış [Satır: 178]) |
| Düşük genre confidence | yanlış EQ profili | hatalı ses karakteri | `minConfidenceForApply` kapısı [Satır: 362] |
| Kuyruk taşması | iş reddedildi | beklenmedik RED | `maxQueueSize: 20` [Satır: 633] |

### §7.4 Kapanış beyanı (bu dosya için)

- Eklenen derinlik: §5 (6 örnek tam okuma) · §6 (PHP↔C++ eşleme + 11 metot envanteri + 6 kabul kriteri) · §7 (sahiplik · katman sınırı · hata modu).
- Toplam yeni madde: 6 C++ sınıf okuması · 9 eşleme satırı · 11 PHP imzası · 6 AK · 6 hata modu · 5 katman satırı.
- Uydurma: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 3 (drain mekanizması · compositeMos ağırlıkları · progress API eşlemesi).
