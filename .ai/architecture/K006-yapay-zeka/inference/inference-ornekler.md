---
title: "inference — Örnekler"
type: architecture-examples
node_id: K006-yapay-zeka/inference
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# inference — Örnekler

## §1 Gerçek repo kodu (ÖNCELİK 1 — bu oturumda okunarak doğrulandı)

`shared/src/AI/AIWorkflow.php` satır 75 — inference çağrısının tek noktası:

```php
// 5. Recommendation engine
$recommendations = $this->engine->getRecommendations($context, $context['limit'] ?? 10);
```

`shared/src/Contracts/AI/AIEngineInterface.php` satır 26–28 — girdi/çıktı sözleşmesi:

```php
@return array<int, array{id: string, title: string, artist: string, score: float, reason: string}>
public function getRecommendations(array $context, int $limit = 20): array;
```

> **Doğruluk notu:** `shared/src/AI/` altında ONNX/TFLite/torch gibi bir **model runtime'ı yok**
> (bu oturum grep: `onnx` 0 isabet). Bu örnekler bir **modelin** değil, PHP kural motorunun
> çağrı noktasıdır — kapsam §4/7 ve dogrulama §1/5.

## §2 Resmi doküman örneği (URL + snippet)

**ONNX Runtime — Python tutorial** · https://onnxruntime.ai/docs/api/python/tutorial.html
(doğrulandı 2026-10-12):

```python
import numpy
import onnxruntime as rt

sess = rt.InferenceSession("logreg_iris.onnx", providers=["CPUExecutionProvider"])
input_name = sess.get_inputs()[0].name
pred_onx = sess.run(None, {input_name: X_test.astype(numpy.float32)})[0]
```

**API özeti** · https://onnxruntime.ai/docs/api/python/api_summary (doğrulandı 2026-10-12):

> "ONNX Runtime loads and runs inference on a model in ONNX graph format, or ORT format
> (for memory and disk constrained environments)."

### §2.1 Çok dilli genişletme (K006 · 13-dil kapsama Faz 1 · ek 2026-10-14)

> Aşağıdaki 6 blok ONNX Runtime'ın **resmi dil binding'lerinden** türetilmiştir; her kaynak
> 2026-10-14'te webfetch ile doğrulanmıştır (HTTP 200). Kod, ilgili belgede/örnek dosyada
> birebir geçen satırlardan derlenmiştir. Bu bloklar **aday** gösterimidir — CoreMusic'in
> bu dillerden birini kullanacağı **UNKNOWN** (bkz. §3 ve ai-master §11.2).

**ONNX Runtime — C (düşük seviye C API)** ·
https://github.com/microsoft/onnxruntime-inference-examples/blob/main/c_cxx/fns_candy_style_transfer/fns_candy_style_transfer.c
(doğrulandı 2026-10-14 · `[ORİJİNAL ÖRNEK · derlenmedi]` · get-started: https://onnxruntime.ai/docs/get-started/with-c.html):

```c
#include "onnxruntime_c_api.h"

const OrtApi* g_ort = NULL;

#define ORT_ABORT_ON_ERROR(expr)                             \
  do {                                                       \
    OrtStatus* onnx_status = (expr);                         \
    if (onnx_status != NULL) {                               \
      const char* msg = g_ort->GetErrorMessage(onnx_status); \
      fprintf(stderr, "%s\n", msg);                          \
      g_ort->ReleaseStatus(onnx_status);                     \
      abort();                                               \
    }                                                        \
  } while (0);

/* main(): API giriş noktası + env + session */
g_ort = OrtGetApiBase()->GetApi(ORT_API_VERSION);
OrtEnv* env;
ORT_ABORT_ON_ERROR(g_ort->CreateEnv(ORT_LOGGING_LEVEL_WARNING, "test", &env));
OrtSessionOptions* session_options;
ORT_ABORT_ON_ERROR(g_ort->CreateSessionOptions(&session_options));
OrtSession* session;
ORT_ABORT_ON_ERROR(g_ort->CreateSession(env, model_path, session_options, &session));

/* run_inference(): tensör oluştur + çalıştır */
OrtMemoryInfo* memory_info;
ORT_ABORT_ON_ERROR(g_ort->CreateCpuMemoryInfo(OrtArenaAllocator, OrtMemTypeDefault, &memory_info));
const int64_t input_shape[] = {1, 3, 720, 720};
const size_t input_shape_len = sizeof(input_shape) / sizeof(input_shape[0]);
const size_t model_input_len = model_input_ele_count * sizeof(float);

OrtValue* input_tensor = NULL;
ORT_ABORT_ON_ERROR(g_ort->CreateTensorWithDataAsOrtValue(memory_info, model_input, model_input_len, input_shape,
                                                         input_shape_len, ONNX_TENSOR_ELEMENT_DATA_TYPE_FLOAT,
                                                         &input_tensor));
const char* input_names[] = {"inputImage"};
const char* output_names[] = {"outputImage"};
OrtValue* output_tensor = NULL;
ORT_ABORT_ON_ERROR(g_ort->Run(session, NULL, input_names, (const OrtValue* const*)&input_tensor, 1, output_names, 1,
                              &output_tensor));
float* output_tensor_data = NULL;
ORT_ABORT_ON_ERROR(g_ort->GetTensorMutableData(output_tensor, (void**)&output_tensor_data));

g_ort->ReleaseValue(output_tensor);
g_ort->ReleaseValue(input_tensor);
g_ort->ReleaseSession(session);
g_ort->ReleaseSessionOptions(session_options);
g_ort->ReleaseEnv(env);
```

**ONNX Runtime — C++ (C API üzerine header-only RAII wrapper)** ·
https://github.com/microsoft/onnxruntime-inference-examples/blob/main/c_cxx/squeezenet/main.cpp
(doğrulandı 2026-10-14 · `[ORİJİNAL ÖRNEK · derlenmedi]` · get-started: https://onnxruntime.ai/docs/get-started/with-cpp.html —
"C++ API is a thin wrapper of the C API" · TensorRT-EP satırları bu örnekte kısaltıldı, CPU yolu korundu):

```cpp
#include <onnxruntime_cxx_api.h>

Ort::Env env(ORT_LOGGING_LEVEL_WARNING, "test");

Ort::SessionOptions session_options;
session_options.SetIntraOpNumThreads(1);
session_options.SetGraphOptimizationLevel(GraphOptimizationLevel::ORT_ENABLE_EXTENDED);

#ifdef _WIN32
const wchar_t* model_path = L"squeezenet.onnx";
#else
const char* model_path = "squeezenet.onnx";
#endif

Ort::Session session(env, model_path, session_options);

/* model girdi düğümlerini incele */
Ort::AllocatorWithDefaultOptions allocator;
const size_t num_input_nodes = session.GetInputCount();
std::vector<Ort::AllocatedStringPtr> input_names_ptr;
std::vector<const char*> input_node_names;
std::vector<int64_t> input_node_dims;
for (size_t i = 0; i < num_input_nodes; i++) {
  auto input_name = session.GetInputNameAllocated(i, allocator);
  input_node_names.push_back(input_name.get());
  input_names_ptr.push_back(std::move(input_name));
  auto type_info = session.GetInputTypeInfo(i);
  auto tensor_info = type_info.GetTensorTypeAndShapeInfo();
  input_node_dims = tensor_info.GetShape();
}

constexpr size_t input_tensor_size = 224 * 224 * 3;
std::vector<float> input_tensor_values(input_tensor_size);
std::vector<const char*> output_node_names = {"softmaxout_1"};

auto memory_info = Ort::MemoryInfo::CreateCpu(OrtArenaAllocator, OrtMemTypeDefault);
auto input_tensor = Ort::Value::CreateTensor<float>(memory_info, input_tensor_values.data(), input_tensor_size,
                                                    input_node_dims.data(), 4);

auto output_tensors =
    session.Run(Ort::RunOptions{nullptr}, input_node_names.data(), &input_tensor, 1, output_node_names.data(), 1);

float* floatarr = output_tensors.front().GetTensorMutableData<float>();
```

**ONNX Runtime — C# (.NET · Microsoft.ML.OnnxRuntime)** ·
https://onnxruntime.ai/docs/get-started/with-csharp.html
(doğrulandı 2026-10-14 · `[ORİJİNAL ÖRNEK · derlenmedi]`):

```csharp
using Microsoft.ML.OnnxRuntime;

const string modelAPath = "./modelA.onnx";
const string modelBPath = "./modelB.onnx";
using InferenceSession session1 = new InferenceSession(modelAPath);
using InferenceSession session2 = new InferenceSession(modelBPath);

// Illustration only
float[] inputData = { 1, 2, 3, 4 };
long[] inputShape = { 1, 4 };

using var inputOrtValue = OrtValue.CreateTensorValueFromMemory(inputData, inputShape);

var inputs1 = new Dictionary<string, OrtValue>
{
    { "input", inputOrtValue }
};

using var runOptions = new RunOptions();

// session1 inference
using (var outputs1 = session1.Run(runOptions, inputs1, session1.OutputNames))
{
    var outputToFeed = outputs1.First();
    var inputs2 = new Dictionary<string, OrtValue>
    {
        { "inputNameForModelB", outputToFeed }
    };
    using (var results = session2.Run(runOptions, inputs2, session2.OutputNames))
    {
        // manipulate the results
    }
}

// Running on GPU (CUDA) — GPU paketi varsa:
int gpuDeviceId = 0;
using var gpuSessionOptions = SessionOptions.MakeSessionOptionWithCudaProvider(gpuDeviceId);
using var gpuSession = new InferenceSession("model.onnx", gpuSessionOptions);
```

**ONNX Runtime — Java (JVM · com.microsoft.onnxruntime:onnxruntime)** ·
https://onnxruntime.ai/docs/get-started/with-java.html
(doğrulandı 2026-10-14 · `[ORİJİNAL ÖRNEK · derlenmedi]` · "Java 8 or newer"):

```java
var env = OrtEnvironment.getEnvironment();
var session = env.createSession("model.onnx", new OrtSession.SessionOptions());

OnnxTensor t1, t2;
var inputs = Map.of("name1", t1, "name2", t2);
try (var results = session.run(inputs)) {
    // manipulate the results
}

// girdi verisini yükleme — en verimli yol java.nio.Buffer:
FloatBuffer sourceData;  // assume your data is loaded into a FloatBuffer
long[] dimensions;       // and the dimensions of the input are stored here
var tensorFromBuffer = OnnxTensor.createTensor(env, sourceData, dimensions);

float[][] sourceArray = new float[28][28];  // assume your data is loaded into a float array
var tensorFromArray = OnnxTensor.createTensor(env, sourceArray);

// Run on a GPU (CUDA) execution provider:
int gpuDeviceId = 0;
var sessionOptions = new OrtSession.SessionOptions();
sessionOptions.addCUDA(gpuDeviceId);
var gpuSession = env.createSession("model.onnx", sessionOptions);
```

**ONNX Runtime — JavaScript / Node.js (onnxruntime-node)** ·
https://github.com/microsoft/onnxruntime-inference-examples/blob/main/js/quick-start_onnxruntime-node/index.js
(doğrulandı 2026-10-14 · `[ORİJİNAL ÖRNEK · derlenmedi]` · kurulum: `npm install onnxruntime-node` —
https://onnxruntime.ai/docs/get-started/with-javascript/node.html · MIT lisanslı resmi Microsoft örneği):

```js
const ort = require('onnxruntime-node');

// use an async context to call onnxruntime functions.
async function main() {
    try {
        // the model in this example contains a single MatMul node
        // it has 2 inputs: 'a'(float32, 3x4) and 'b'(float32, 4x3)
        // it has 1 output: 'c'(float32, 3x3)
        const session = await ort.InferenceSession.create('./model.onnx');

        const dataA = Float32Array.from([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);
        const dataB = Float32Array.from([10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120]);
        const tensorA = new ort.Tensor('float32', dataA, [3, 4]);
        const tensorB = new ort.Tensor('float32', dataB, [4, 3]);

        const feeds = { a: tensorA, b: tensorB };
        const results = await session.run(feeds);

        const dataC = results.c.data;
        console.log(`data of result tensor 'c': ${dataC}`);

        await session.release()
    } catch (e) {
        console.error(`failed to inference ONNX model: ${e}.`);
    }
}

main();
```

**ONNX Runtime — Rust (topluluk binding'i `ort` — resmi API dizininde listeleniyor)** ·
https://ort.pyke.io/ (doğrulandı 2026-10-14 · `[ORİJİNAL ÖRNEK · derlenmedi]` ·
2.0.0-rc.14 · API referansı https://docs.rs/ort/latest/ort/ ·
ONNX Runtime resmi dizini "Rust API (Community)" olarak listeliyor: https://onnxruntime.ai/docs/api/ ·
ayrıca https://onnxruntime.ai/docs/get-started/community-projects.html):

```toml
[dependencies]
ort = "=2.0.0-rc.14"
```

```rust
use ort::session::{builder::GraphOptimizationLevel, Session};

let env = ort::init().build()?;
let mut model = Session::builder(&env)?
    .with_optimization_level(GraphOptimizationLevel::Level3)?
    .with_intra_threads(4)?
    .commit_from_file("yolov8m.onnx")?;

let outputs = model.run(ort::inputs!["image" => image])?;
let predictions = outputs["output0"].try_extract_array::<f32>()?;
```

> **Dil kapsamı notu (Faz 1):** Aday listedeki Go · Zig · Swift · PowerShell için 2026-10-14'te
> doğrulanabilir resmi ONNX Runtime kaynağı **bulunamadı** (onnxruntime.ai/docs/api/ resmi dil
> dizini Python · Java · C# · C/C++ · WinRT · Objective-C · JavaScript · topluluk Ruby/Julia/Rust
> içerir; Go/Zig/Swift/PowerShell yok) → bu diller için blok yazılmadı. Kotlin için de
> Kotlin'e özgü resmi ORT örneği/dokümanı bulunamadı (yalnız Java/JVM binding'i resmi).

## §3 Uygulama notu

- Örnek 2 bir **aday** mimaridir; CoreMusic'in ONNX kullanıp kullanmadığı **UNKNOWN** (kapsam §5/R2).
- Kademe-3'te "PHP motor ↔ model runtime" geçiş kararı ai-master §4/k4 kapsamında alınmalıdır.

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `inference-kapsam.md` | bu dalın rolü, içinde/dışında, sınır soruları |
| `inference-dogrulama.md` | iddia → URL/dosya kanıt tablosu |
| `../index.md` · `../ai-master.md` §1/§3 | üst düğüm künyesi + çocuk tablosu (v2 satır 260) |

---

## §5 Kapanış Derinliği — Neva Engine inference C++ örnek turu (2026-10-15)

### §5.0 Kanıt disiplini

Repo ölçümü (2026-10-15): `*.cpp` / `*.h` = **0 dosya** → aşağıdaki C++ blokları **vault spesifikasyon alıntılarıdır**, derlenmiş kod değildir; her biri `Dosya: … [Satır: n]` ile işaretlenmiştir.

### §5.1 Örnek INF-1 — ONNXInferenceEngine (motor sınıfı, tam okuma)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 129–156].

```cpp
class ONNXInferenceEngine {
    Ort::Env env{ORT_LOGGING_LEVEL_WARNING, "NevaAI"};
    Ort::SessionOptions sessionOptions;
    std::unordered_map<std::string, Ort::Session> sessions;

public:
    enum class ExecutionProvider {
        CPU,
        CUDA,           // NVIDIA GPU
        DirectML,       // Windows GPU (DX12)
        CoreML,         // Apple Neural Engine
        OpenVINO,       // Intel CPU/iGPU
        TensorRT,       // NVIDIA (en hızlı)
        ROCM,           // AMD GPU
    };

    void configure(ExecutionProvider provider, int deviceId = 0);
    Ort::Session& loadModel(const std::string& modelPath);
    void unloadModel(const std::string& modelId);

    // FP16 → INT8 quantization
    void quantizeModel(const std::string& srcPath, const std::string& dstPath);

    // Model optimizasyonu (graph fusion, constant folding)
    void optimizeModel(const std::string& srcPath, const std::string& dstPath);
};
```

**Okuma notları:**

1. 7 EP enum'u Donanım Destek Matrisi'yle (Dosya: ai-models.md [Satır: 31–52]) birebir eşleşmez: matriste Intel Arc ve Raspberry Pi satırları var, enum'da bunlara karşılık gelen ayrı EP yok (Intel Arc → OpenVINO varsayımı **uydurulamaz**, `⚠️ VERIFICATION REQUIRED`).
2. `loadModel` `Ort::Session&` döndürür → çağırıcı referansı harici tutabilir; session map'ten silinirse **dangling referans** riski doğar. K006 çıkarımı: referans yalnız oturum sahipliği sürerken geçerlidir (bkz. audio-intelligence-ornekler §7.1).
3. `quantizeModel`/`optimizeModel` dosyadan dosyaya çalışır → batch (dizin geneli) dönüşümü yok; K006 model-management dalı bunu isterse ek API gerekir.

### §5.2 Örnek INF-2 — Tensor hazırlama ve Run (düşük seviye akış, tam okuma)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 184–202].

```cpp
// Input tensor: [batch, channels, time]
std::vector<int64_t> inputShape = {1, 1, 65536};

Ort::Value inputTensor = Ort::Value::CreateTensor<float>(
    memoryInfo,
    inputData.data(),
    inputData.size(),
    inputShape.data(),
    inputShape.size()
);

// Output tensor: aynı shape
auto outputTensors = session.Run(
    Ort::RunOptions{nullptr},
    inputNames.data(), &inputTensor, 1,
    outputNames.data(), 1
);
```

**Okuma notları:**

1. Şema sabittir: `[batch=1, channels=1, time=65536]` → mono, tek parça, 2^16. `chunkSize` (Dosya: ai-models.md [Satır: 87]) ile aynı değer; şema↔config tutarlılığı **kanıtlanmış** durumdadır.
2. `session.Run` senkron çağrıdır; çoklu çıktı (`outputNames`, 1 adet) — ikinci çıktı varsa `outputNames` uzunluğu 2 olmalıdır; spec'te 1 yazılmıştır.
3. Tam akış (Dosya: ai-models.md [Satır: 160–180]): Model Yükle → Input Tensor Hazırlama (Mel-Spectrogram/STFT · Normalizasyon · Padding · Ort::Value) → Run → Output (Denormalizasyon · Overlap-add · Float32 buffer). Bu akıştaki **normalizasyon mean/std değerleri** spec'te yok → `⚠️ VERIFICATION REQUIRED`.

### §5.3 Örnek INF-3 — ExecutionProvider seçimi (karar matrisi, spec'ten)

| EP | Platform | Spec kanıtı | Seçim kuralı (türetilmiş) |
|---|---|---|---|
| CUDA | Win/Linux NVIDIA | matris [Satır: 33–47] (CUDA 10.x–13.x GPU nesillerine göre) | GPU varsa ve VRAM ≥ model INT8 boyutu |
| TensorRT | Win/Linux NVIDIA | enum [Satır: 142] "en hızlı" | latency kritikse CUDA yerine |
| DirectML | Windows (DX12) | enum [Satır: 139] | NVIDIA olmayan Windows GPU |
| CoreML | macOS (M serisi) | matris [Satır: 38–41] | Apple Neural Engine |
| OpenVINO | Intel CPU/iGPU | enum [Satır: 141] | Intel donanım |
| ROCM | AMD GPU | enum [Satır: 143] | AMD GPU |
| CPU | her yer | enum [Satır: 137] | son çare / INT8 hafif modeller |

Varsayılan `executionProvider: "auto"` (Dosya: ai-models.md [Satır: 625]) — "auto" çözümleme algoritması spec'te **yok** → K006'da EP seçimi kararı `../yapay-zeka-kararlar.md` §H/H2 kapısına tabidir (aday "seçildi" olmadan kodda kullanılmaz).

### §5.4 Örnek INF-4 — Real-time vs batch sınıflandırması (tam tablo)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 426–432].

| Kategori | Modeller | Inference Süresi | Kullanım |
|---|---|---|---|
| Real-Time (GPU) | DeepFilterNet2, HiFi-GAN | < 5 ms | canlı denoising, vocoder |
| Real-time (CPU) | DeepFilterNet2 (INT8), Genre CNN | < 10 ms | canlı auto-EQ |
| Fast Batch (GPU) | Demucs, Resemble Enhance | 5–15s / dakika | upload processing |
| Slow Batch (GPU) | AudioSR, NuWave2, BSRNN | 10–40s / dakika | offline upscaling |
| Heavy Batch | Custom large models | 1–10 dk | arka plan işleme |

**Değerlendirme notları:**

1. Tablo "1 dk ses" başına süreyi verir; "Inference Süresi" sütunu ise **buffer başına** bütçedir (< 5 ms · < 10 ms) — iki farklı ölçek, aynı tabloda; karıştırılmamalı.
2. Real-time kategori asla job kuyruğuna girmez (bkz. §5.5); batch kuyruğa girer. Bu ayrım K006 inference mimarisinin iki kanalıdır.

### §5.5 Örnek INF-5 — Job kuyruğu (batch kanalı, tam okuma)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 436–457].

```cpp
class InferenceJobQueue {
    struct Job {
        std::string id;                 // UUID
        JobPriority priority;           // LOW, NORMAL, HIGH, REALTIME
        ModelType modelType;
        std::vector<float> inputAudio;
        std::vector<float> outputAudio;
        JobStatus status;               // QUEUED, RUNNING, DONE, FAILED
        float progress;                 // 0.0 - 1.0
        std::chrono::milliseconds duration;
    };

    std::priority_queue<Job, std::vector<Job>, JobComparator> queue;
    std::vector<std::thread> workers;   // 4 worker thread

public:
    std::string submitJob(const Job& job);
    JobStatus getJobStatus(const std::string& jobId);
    void cancelJob(const std::string& jobId);
};
```

**Okuma notları:**

1. `Job::inputAudio`/`outputAudio` vektörleri ses kopyası taşır → kuyrukta tutulan iş bellek maliyetlidir; `maxQueueSize: 20` (Dosya: ai-models.md [Satır: 633]) üst sınırı bu maliyeti sınırlar.
2. `priority_queue` + `JobComparator` — karşılaştırma **DESC** (yüksek öncelik önce) varsayımı dayatılır ama comparator gövdesi spec'te yok → `⚠️ VERIFICATION REQUIRED`.
3. `cancelJob` RUNNING işi iptal edebilir mi? spec'te belirsiz → ortalama iptal semantiği UNKNOWN.
4. Config ile uyum: `maxWorkers: 4` (Dosya: ai-models.md [Satır: 632]) — `workers` vektöründeki `// 4 worker thread` yorumuyla [Satır: 450] birebir tutarlı.

### §5.6 Örnek INF-6 — Quantization merdiveni (hassasiyet değerlendirme)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 210–225].

| Kademe | Byte/tensor | Kalite | Hız | VRAM | Yöntem |
|---|---|---|---|---|---|
| FP32 | 4 | referans | 1× | 4× | — |
| FP16 | 2 | %98 | 2× | 2× | — |
| INT8 | 1 | %95 | 4× | 4× | PTQ static / PTQ dynamic / QAT |
| INT4 | 0.5 | %85 | 8× | 8× | deneysel |

**Değerlendirme notları:**

1. "Kalite %" değerlerinin referansı (hangi model · hangi metrik) yazılmamış → bu yüzdeler **kaba yönlendirme**, gate olarak kullanılamaz; gate §5.7'deki gerçek metriklerdir.
2. PTQ static "calibration dataset gerekli" notu [Satır: 222] → K006 calibration verisi kaynağı UNKNOWN (kök dosya §6.3 kaydıyla aynı madde, tekrar değil bağ).

### §5.7 Örnek INF-7 — Inference gate (Neva metrikleriyle, türetilmiş)

```text
Gate INF-A: Gerçek zamanlı kanal — çıktı bütçesi < 10 ms (CPU) / < 5 ms (GPU)
            Kaynak: Dosya: ai-models.md [Satır: 428–429]
Gate INF-B: Batch kanalı — 1 dk ses ≤ hedef süre (model bazlı, §5.2 tablosu)
            Kaynak: Dosya: ai-models.md [Satır: 715–724]
Gate INF-C: Kalite — PESQ > 3.5 · STOI > 0.85 · SI-SDR > 15 dB · LSD < 2.0 dB
            Kaynak: Dosya: ai-models.md [Satır: 579–588]
Gate INF-D: Bellek — tahsis edilen VRAM/RAM üst sınırı aşılmaz
            Kaynak: Dosya: ai-models.md [Satır: 629–630] (maxVRAM 8192 · maxCPUMemory 4096)
Gate INF-E: Kuyruk — queue ≤ 20 · worker ≤ 4 · realtime bütçesi %25
            Kaynak: Dosya: ai-models.md [Satır: 631–635]
```

Bu turda **hiçbiri çalıştırılmadı** → `../yapay-zeka-dogrulama.md` §7'ye taşındı.

### §5.8 §5 tur kapanış kaydı

- Eklenen: 2 tam spec okuması (INF-1 · INF-2) + 1 EP karar matrisi + 1 sınıflandırma tablosu + 1 kuyruk okuması + 1 quantization tablosu + 5 gate.
- Uydurma identifier: 0 · ölçü uydurma: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 4 (auto-EP çözümleme · normalizasyon mean/std · comparator · cancel semantiği).

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
