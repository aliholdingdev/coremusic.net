---
title: "Local Inference — Örnekler"
type: architecture-scope
node_id: K006-yapay-zeka/local-inference
node_type: subdomain-examples
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# Local Inference — Örnekler

Amaç: kapsamı üç örnek somutlaştırmak — gerçek repo kodu (öncelik 1), resmi doküman örneği, uygulama notu.

## §1. Gerçek repo kodu (öncelik 1)

**`shared/src/` içinde yerel inference çağıran kod — yok:**

grep `onnx|onnxruntime|tflite|local_inference` → 0 isabet; repo'da yerel model çalıştırma katmanı henüz yok. En yakın kanıt, `AIEngine`'in çalışma zamanı etiketidir:

```php
// shared/src/AI/AIEngine.php (satır 19)
'runtime' => 'cpu', // yerel veya bulut — mock veri
```

**Yorum (kanıt temelli):** `runtime` alanı mock veride sabit `cpu`; yerel ↔ bulut mod seçimi henüz kodda temsil edilmez (kapsam maddesi 1'in repo karşılığı yok).

## §2. Resmi doküman örneği

ONNX Runtime — execution provider seçimi (resmi doküman, 2026-10-12 doğrulandı):

> "Execution Providers allow ONNX Runtime to take advantage of hardware acceleration … e.g. NVIDIA CUDA, DirectML, Apple CoreML, Intel OpenVINO."
> — https://onnxruntime.ai/docs/execution-providers/

Python — ONNX Runtime yerel inference session (`[ORİJİNAL ÖRNEK · derlenmedi]` · 2026-10-14):

```python
import onnxruntime as ort
import numpy as np
x, y = test_data[0][0], test_data[0][1]
ort_sess = ort.InferenceSession('fashion_mnist_model.onnx')
outputs = ort_sess.run(None, {'input': x.numpy()})
```
— https://onnxruntime.ai/docs/get-started/with-python.html (HTTP 200 · 2026-10-14)

C# — ONNX Runtime yerel session + CUDA execution provider seçimi (`[ORİJİNAL ÖRNEK · derlenmedi]` · 2026-10-14):

```csharp
using Microsoft.ML.OnnxRuntime;
int gpuDeviceId = 0; // The GPU device ID to execute on
using var gpuSessionOptoins = SessionOptions.MakeSessionOptionWithCudaProvider(gpuDeviceId);
using var session = new InferenceSession("model.onnx", gpuSessionOptoins);
```
— https://onnxruntime.ai/docs/get-started/with-csharp.html (HTTP 200 · 2026-10-14)

Java — ONNX Runtime JVM binding: OrtEnvironment + OrtSession (`[ORİJİNAL ÖRNEK · derlenmedi]` · 2026-10-14):

```java
var env = OrtEnvironment.getEnvironment();
var session = env.createSession("model.onnx",new OrtSession.SessionOptions());

OnnxTensor t1,t2;
var inputs = Map.of("name1",t1,"name2",t2);
try (var results = session.run(inputs)) {
    // manipulate the results
}
```
— https://onnxruntime.ai/docs/get-started/with-java.html (HTTP 200 · 2026-10-14)

JavaScript (Node.js) — onnxruntime-node bağlama, resmi quick-start örneğinden parça (`[ORİJİNAL ÖRNEK · derlenmedi]` · 2026-10-14):

```js
const ort = require('onnxruntime-node');
const session = await ort.InferenceSession.create('./model.onnx');

const dataA = Float32Array.from([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);
const dataB = Float32Array.from([10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120]);
const tensorA = new ort.Tensor('float32', dataA, [3, 4]);
const tensorB = new ort.Tensor('float32', dataB, [4, 3]);

const feeds = { a: tensorA, b: tensorB };
const results = await session.run(feeds);
const dataC = results.c.data;
```
— https://github.com/microsoft/onnxruntime-inference-examples/blob/main/js/quick-start_onnxruntime-node/index.js (HTTP 200 · 2026-10-14)

C++ — llama.cpp resmi `examples/simple` örneğinden parça: model yükleme → vocab → tokenize → context oluşturma (`[ORİJİNAL ÖRNEK · derlenmedi]` · 2026-10-14):

```cpp
llama_backend_init();

llama_model_params model_params = llama_model_default_params();
model_params.n_gpu_layers = ngl;

llama_model * model = llama_model_load_from_file(model_path.c_str(), model_params);

const llama_vocab * vocab = llama_model_get_vocab(model);
const int n_prompt = -llama_tokenize(vocab, prompt.c_str(), prompt.size(), NULL, 0, true, true);

llama_context_params ctx_params = llama_context_default_params();
ctx_params.n_ctx = n_prompt + n_predict - 1;
ctx_params.n_batch = n_prompt;

llama_context * ctx = llama_init_from_model(model, ctx_params);
```
— https://github.com/ggml-org/llama.cpp/blob/master/examples/simple/simple.cpp (HTTP 200 · 2026-10-14)

C — ONNX Runtime C API minimal çağrı sırası (resmi doküman; sayfa tam kod bloğu yerine adımları metinle tanımlar — `[ORİJİNAL ÖRNEK · derlenmedi]` · 2026-10-14):

> "1. Include onnxruntime_c_api.h. 2. Call OrtCreateEnv 3. Create Session: OrtCreateSession(env, model_uri, nullptr,…) — Optionally add more execution providers … 4. Create Tensor 1) OrtCreateMemoryInfo 2) OrtCreateTensorWithDataAsOrtValue 5. OrtRun"
> — https://onnxruntime.ai/docs/get-started/with-c.html (HTTP 200 · 2026-10-14)

## §3. Uygulama notu

Hedef donanım (DirectML · CUDA) kesinleşmeden EP seçimi yapılamaz — ai-master.md §7'deki donanım sorusu önceliklidir.

## §4. İlişkili dosyalar

- [[local-inference-kapsam]]
- [[local-inference-dogrulama]]
- [[../index|K006-yapay-zeka index]]

---

## §5 Kapanış Derinliği — Neva Engine lokal inference C++ örnek turu (2026-10-15)

### §5.0 Kanıt disiplini

Repo ölçümü (2026-10-15): `*.cpp` / `*.h` = **0 dosya** → aşağıdaki C++ blokları **vault spesifikasyon alıntılarıdır**, derlenmiş kod değildir; her biri `Dosya: … [Satır: n]` ile işaretlenmiştir.

### §5.1 Örnek LI-1 — Lokal motor sınıfı (ONNXInferenceEngine, tam okuma)

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

**Lokal çıkarımlar:**

1. `env` sınıf üyesi → tek engine örneği tüm process boyunca yaşar; lokal kurulumda EP değişimi (ör. CUDA→CPU) `configure` ile oturum öncesi yapılabilir, canlı EP değişim sözleşmesi yok.
2. Lokal kurulumun son çare yolu `CPU` + INT8'dir (matris [Satır: 50–52]: CPU-only x86 · RPi 4/5/6 satırları).
3. `deviceId` yalnız tek GPU'yu kapsar; çoklu GPU lokal senaryo (cluster, [Satır: 686–690]) ayrı kurulumdur.

### §5.2 Örnek LI-2 — Donanım-özel lokal konfigürasyonlar (tam tablo)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 666–674].

| Donanım | Provider | Precision | Eş Zamanlı Model | Kullanım Senaryosu |
|---|---|---|---|---|
| RTX 4090 (24 GB) | CUDA | FP16 | 4 | Tam kapasite, production |
| RTX 4070 (12 GB) | CUDA | FP16 | 2 | Orta seviye production |
| RTX 3060 (12 GB) | CUDA | FP16/INT8 | 1 | Giriş seviyesi GPU |
| GTX 1660 (6 GB) | CUDA | INT8 | 1 | Eski GPU, sadece hafif modeller |
| M2 Pro (16 GB) | CoreML | FP16 | 2 | macOS production |
| Ryzen 7 (32 GB) | CPU | INT8 | 1 | GPU'suz, hafif modeller |
| Raspberry Pi 5 (8 GB) | CPU | INT8 | 0 | Sadece genre detection |

**Lokal çıkarımlar:**

1. "Eş Zamanlı Model" sütunu lokal kapasite planının çekirdeğidir: model başına VRAM (Dosya: ai-models.md [Satır: 61–72] tablosu) ile çarpıldığında toplam VRAM bütçesi çıkar (ör. RTX 4090: 4 eş zamanlı × ≤ 4 GB FP16 ≈ 16 GB + çalışma payı ≤ 24 GB).
2. RPi5 satırı "0 eş zamanlı" derken Genre CNN tek başına demektir (RPi5 benchmark [Satır: 730]: 0.3s / 200×); AudioSR RPi5'te **çalışmaz** [Satır: 733].
3. GTX 1660 (6 GB) satırı "sadece hafif modeller" der → hafiflik tanımı VRAM ≤ 1.2 GB (DeepFilterNet2 INT8 0.7 GB · DEMUCS Denoiser INT8 0.7 GB) olarak okunur; bu eşik **türetmedir**, spec'te yazılmamıştır.

### §5.3 Örnek LI-3 — Lokal ölçeklendirme senaryoları (tam blok)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 678–690].

```text
Tek PC (Tüm modeller lokal):
RTX 4090 → Tüm modeller FP16, 4 eş zamanlı

PC + RPi (Edge + Cloud):
RTX 4090 → Ağır modeller (AudioSR, Demucs)
Raspberry Pi 5 → Hafif modeller (Genre CNN, DeepFilterNet2 INT8)

Çoklu PC (Cluster):
Machine 1: RTX 4090 → AudioSR + BSRNN
Machine 2: RTX 4090 → Demucs + NuWave2
Machine 3: CPU-only → Batch queue manager
```

**Lokal çıkarımlar:**

1. "PC + RPi" senaryosu iki makine arasında iş bölme iddia eder ama **iletişim protokolü yok** (REST · queue · IPC bilinmiyor) → `⚠️ VERIFICATION REQUIRED`.
2. "Machine 3: CPU-only → Batch queue manager" satırı kuyruk yöneticisinin de lokal bir iş olduğunu, GPU'suz bir makinede çalışabileceğini kanıtlar (kuyruk CPU işidir; GPU yalnız inferans içindir).
3. Cluster senaryosu için ağ bandı gereksinimi (GB/s cinsinden) hesaplanmamıştır → UNKNOWN.

### §5.4 Örnek LI-4 — Model dizin yapısı (lokal saklama, tam blok)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 530–549].

```text
models/
├── audiosr-v2/
│   ├── model_fp16.onnx       (4.0 GB)
│   ├── model_int8.onnx       (2.1 GB)
│   ├── config.json
│   ├── version.txt           ("2.1.0")
│   └── checksums.sha256
├── demucs-ht/
│   ├── model_fp16.onnx
│   ├── model_int8.onnx
│   └── config.json
├── deepfilternet2/
│   ├── model_int8.onnx       (0.3 GB, en küçük)
│   └── config.json
└── genre-cnn/
    ├── model_fp32.onnx        (50 MB, en küçük)
    └── genres.json
```

**Lokal çıkarımlar:**

1. `checksums.sha256` her modelde yok (yalnız audiosr-v2'de görünüyor) → lokal kurulumda bütünlük doğrulaması **model bazında değişken**; K006'da tüm modeller için zorunlu kılınması bir karar maddesidir (`../yapay-zeka-kararlar.md` §H/H2).
2. `genres.json` sınıf etiketlerinin lokal kaynağıdır (audio-intelligence-ornekler §5.3 ile bağ).
3. Boyutlar FP16/INT8 ile VRAM matrisini [Satır: 61–72] doğrular: audiosr-v2 4.0/2.1 GB — tablo ile birebir aynı.

### §5.5 Örnek LI-5 — Lokal sistem yapılandırması (tam blok, ilgili alt küme)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 621–658] — `ai` bloğu:

```json
{
  "ai": {
    "enabled": true,
    "executionProvider": "auto",
    "deviceId": 0,
    "precision": "auto",
    "modelPath": "./models/",
    "maxVRAM": 8192,
    "maxCPUMemory": 4096,
    "jobQueue": {
      "maxWorkers": 4,
      "maxQueueSize": 20,
      "realtimeBudgetPercent": 25
    },
    "updates": {
      "autoCheck": true,
      "checkIntervalHours": 24,
      "autoDownload": false,
      "hfApiUrl": "https://huggingface.co/api/models/neva-engine/"
    }
  }
}
```

**Lokal çıkarımlar:**

1. `executionProvider: "auto"` ve `precision: "auto"` — iki "auto" da çözümleme algoritmasız; lokal kurulumda bunlar **karar verilmemiş** alanlardır (UNKNOWN).
2. `maxVRAM: 8192` (MB) RTX 4090'da 24 GB'ın çok altındadır → varsayılan muhafazakârdır; lokal kullanıcı artırabilir.
3. `autoDownload: false` — otomatik model indirme kapalıdır; lokal kurulum kullanıcı onayı ister (güvenlik davranışı, K006 model-management ile bağ).
4. `hfApiUrl` HuggingFace API adresidir (Dosya: ai-models.md [Satır: 555] `ModelUpdater` içinde de aynı taban).

### §5.6 Örnek LI-6 — Lokal EP düşüş zinciri (failover, türetilmiş)

```text
Tercih (GPU var):
  TensorRT (latency kritik · enum [Satır: 142])
    ↓ başarısız
  CUDA (matris [Satır: 33–47])
    ↓ başarısız (eski driver / VRAM yok)
  DirectML (Windows · enum [Satır: 139])
    ↓ başarısız
  CPU + INT8 (enum [Satır: 137] · matris [Satır: 50–52])
    ↓ INT8 model de yoksa
  AI kapalı · ses zinciri etkilenmez (fail-safe · Dosya: audio-core.md [Satır: 27])
```

Bu zincir **türetmedir**; spec'te otomatik düşüş kuralı yok (`"auto"` için çözümleme tanımsız) → düşüş stratejisi bir K006 kararıdır, uydurma davranış yazılmaz.

### §5.7 Örnek LI-7 — Lokal gate'leri

| # | Gate | Eşik | Kaynak |
|---|---|---|---|
| LI-G1 | Lokal model yükleme ≤ bütçe | model boyutu + %20 pay (türetme) | Dosya: ai-models.md [Satır: 61–72] |
| LI-G2 | CPU INT8 lokal throughput | ≥ 3.3× real-time (DeepFilterNet2 RPi5) | [Satır: 731] |
| LI-G3 | Genre CNN lokal | ≤ 0.3s / 1 dk ses (RPi5) | [Satır: 730] |
| LI-G4 | Bellek tavanı | maxVRAM 8192 MB · maxCPUMemory 4096 MB | [Satır: 629–630] |
| LI-G5 | Bütünlük | checksums.sha256 doğrulaması (model başına) | [Satır: 538] |

Bu turda **hiçbiri çalıştırılmadı** → `../yapay-zeka-dogrulama.md` §7'ye taşındı.

### §5.8 §5 tur kapanış kaydı

- Eklenen: 1 tam sınıf okuması + 2 donanım tablosu + 2 tam blok (ölçeklendirme · dizin · config) + 1 failover zinciri + 5 gate.
- Uydurma identifier: 0 · ölçü uydurma: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 3 (edge↔cluster protokolü · cluster bandı · auto-EP çözümleme).

---

## §6 Kapanış Derinliği — Lokal kurulum bütçe hesabı (türetme, `[TÜRETME]` işaretli)

### §6.1 VRAM bütçe hesabı (model ↔ donanım)

Her donanım satırı için "hangi modeller sığar?" hesabı (VRAM değerleri Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 61–72]; eşzamanlılık [Satır: 666–674]):

| Donanım | Bütçe (FP16) | Sığan FP16 seti (aynı anda) | Sığmayan |
|---|---|---|---|
| RTX 4090 (24 GB) | 24 GB | AudioSR 4.0 + Demucs 2.7 + Resemble 2.0 + DeepFilter 0.6 = 9.3 GB (4 model, 14.7 GB boş pay) | — |
| RTX 4070 (12 GB) | 12 GB | AudioSR 4.0 + Demucs 2.7 + Resemble 2.0 = 8.7 GB (3 model) | NuWave2 ile toplam 11.7 GB → dördü aşar |
| RTX 3060 (12 GB) INT8 | 12 GB | AudioSR INT8 2.1 + Demucs INT8 1.4 + DeepFilter INT8 0.7 = 4.2 GB | FP16 AudioSR ile sığabilir ama spec INT8 der |
| GTX 1660 (6 GB) INT8 | 6 GB | DeepFilter 0.7 + Resemble 1.1 + VoiceFixer 1.0 = 2.8 GB | AudioSR INT8 2.1 tek başına sığar ama "hafif" tanımı belirsiz |
| Ryzen 7 (32 GB) CPU | 32 GB RAM | INT8 seti (4.2 GB) | VRAM yok; RAM yeterli |
| RPi5 (8 GB) CPU | 8 GB RAM | DeepFilterNet2 INT8 0.7 + Genre CNN 0.05 = 0.75 GB | AudioSR **çalışmaz** (VRAM yok · [Satır: 733]) |

**Hesap notları:**

1. Çalışma payı (activations · intermediate buffer) hesaba **dahil edilmedi** → gerçek sürüm payı belirsiz; bu tablo "ağırlık" tabanlı kaba bir üst sınırdır (`[TÜRETME]`).
2. GTX 1660 satırındaki "sadece hafif modeller" tanımı belirsizdir (§5.2 not 3 ile bağ).
3. `maxVRAM: 8192` config değeri [Satır: 629] RTX 4090'ın %34'üdür → varsayılan pay muhafazakârdır; tablodaki 24 GB hesabı bu sınırı **aşar**, yani varsayılan config ile 4 model eşzamanlı **çalışmaz** → iki değer arasındaki gerilim K006 kararıdır (`../yapay-zeka-kararlar.md` §H/H2).

### §6.2 Disk bütçe hesabı (lokal saklama)

Dizin şeması (Dosya: ai-models.md [Satır: 530–549]) + boyutlar [Satır: 61–72]:

| Model | FP16 | INT8 | Toplam (her iki varyant) |
|---|---|---|---|
| audiosr-v2 | 4.0 GB | 2.1 GB | 6.1 GB |
| demucs-ht | 2.7 GB | 1.4 GB | 4.1 GB |
| deepfilternet2 | — | 0.3 GB | 0.3 GB |
| genre-cnn | 0.05 GB (FP32) | — | 0.05 GB |
| **Toplam** | | | **≈ 10.55 GB** |

Not: spec'te NuWave2 · Resemble Enhance · BSRNN · VoiceFixer · HiFi-GAN · WaveGlow · DEMUCS Denoiser için dizin satırı **yok** (yalnız 4 model dizinlenmiştir) → tam set diski UNKNOWN; 10.55 GB yalnız 4 dizinli modelin toplamıdır.

### §6.3 Ağ bütçesi (lokal + cluster)

| Senaryo | Gerekli bant | Kanıt |
|---|---|---|
| Tek PC (tüm modeller lokal) | **0** (ağ yok) | Dosya: ai-models.md [Satır: 679–681] |
| PC + RPi edge | bilinmiyor (protokol yok) | [Satır: 682–685] — `⚠️ VERIFICATION REQUIRED` |
| Cluster 3 makine | bilinmiyor (protokol yok) | [Satır: 686–690] — `⚠️ VERIFICATION REQUIRED` |

### §6.4 §6 tur kapanış kaydı

- Eklenen: 6 satır VRAM hesabı + 4 satır disk hesabı + 3 satır ağ hesabı.
- Uydurma identifier: 0 · ölçü uydurma: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 3 (çalışma payı · tam set diski · protokol).

---

## §7 Kapanış Derinliği — Lokal kurulum kontrol listesi (operasyonel, `[TÜRETME]` işaretli)

> Bu kontrol listesi §5.2 (donanım matrisi) + §5.4 (dizin) + §5.5 (config) + §6 (bütçeler) kanıtlarından
> türetilmiştir; hiçbir adımı bu turda **çalıştırılmadı**. K006 lokal kurulum ekibi için kapı sırasıdır.

### §7.1 Kurulum öncesi kapılar

| # | Kapı | Ölçüt | Kaynak |
|---|---|---|---|
| K-1 | Donanım tespiti | GPU üreticisi · VRAM · CPU · RAM · OS | Dosya: ai-models.md [Satır: 31–52] |
| K-2 | EP seçimi | matristen uygun satır (§5.3) | [Satır: 33–52] |
| K-3 | Varyant seçimi | VRAM/eşik → FP16 · INT8 · FP32 (§6.1) | [Satır: 61–72] |
| K-4 | Disk alanı | seçilen set + %20 pay | [Satır: 530–549] |
| K-5 | Bellek tavanı | `maxVRAM`/`maxCPUMemory` yapılandırması | [Satır: 629–630] |

### §7.2 Kurulum kapıları

| # | Kapı | Ölçüt | Kaynak |
|---|---|---|---|
| K-6 | Model indirme | `autoDownload: false` → kullanıcı onayı | [Satır: 653] |
| K-7 | Bütünlük | `checksums.sha256` doğrulaması | [Satır: 516] · [Satır: 538] |
| K-8 | Yükleme | `loadModel` → `status: READY` (durum makinesi: model-management §5.3) | [Satır: 517] |
| K-9 | Config | `executionProvider`/`precision` "auto" → elle sabitleme önerilir (§5.6 failover) | [Satır: 625–627] |
| K-10 | Kuyruk | `maxWorkers: 4` · `maxQueueSize: 20` · `realtimeBudgetPercent: 25` | [Satır: 631–635] |

### §7.3 Kurulum sonrası kapılar (ölçüm)

| # | Kapı | Ölçüt | Kaynak |
|---|---|---|---|
| K-11 | Sanity | Genre CNN 1 dk ses ≤ 0.3s (RPi5) · ≤ 0.02s (RTX 4090) | [Satır: 730] · [Satır: 719] |
| K-12 | Kalite | PESQ > 3.5 · STOI > 0.85 · SI-SDR > 15 dB | [Satır: 581–588] |
| K-13 | RT | < 5 ms GPU · < 10 ms CPU | [Satır: 428–429] |
| K-14 | Bellek tavanı | ölçüm ≤ `maxVRAM` | [Satır: 629] |
| K-15 | Kuyruk | queue ≤ 20 · worker ≤ 4 | [Satır: 631–635] |

### §7.4 §7 tur kapanış kaydı

- Eklenen: 5+5+5 = 15 kapı (K-1…K-15), her kapı kaynak satırı ile.
- Uydurma identifier: 0 · çalıştırılan kapı: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 0 (kapılar tanımlandı, çalıştırılmadı).

---

## §8 Kapanış Derinliği — Lokal inference gate'leri (ölçüm kapıları)

> **Durum:** Bu kapılar bu turda **çalıştırılmadı** — koşul kodları tasarım
> durumundadır; ölçüm §7'ye (`../yapay-zeka-dogrulama.md`) taşındı ve oradan
> raporlanır. Aşağıdaki eşikler gate tasarımının kendisidir, ölçüm sonucu değildir.

### §8.1 Gate tablosu

| Gate | Eşik | Kapsam | Ölçüm kaynağı |
|---|---|---|---|
| `AI-LOCAL-LOAD-MS` | Model yükleme ≤ 1500 ms (ilk çağrı, disk→RAM) | `ONNXInferenceEngine::loadModel` · Dosya: ai-models.md [Satır: 155] | stopwatch ms |
| `AI-LOCAL-INIT` | Engine kurulum (env + session options) ≤ 500 ms | sınıf üyesi `env` · [Satır: 138–140] | microtime |
| `AI-LOCAL-MEM-MB` | Toplam bellek ≤ `maxVRAM` (8192 MB) / `maxCPUMemory` (4096 MB) | config · [Satır: 629–630] | süreç bellek ölçümü |
| `AI-LOCAL-THREADS` | Kuyruk worker ≤ `maxWorkers: 4` | jobQueue · [Satır: 631] | kuyruk sayacı |
| `AI-LOCAL-QUANT` | INT8 varyantı FP16'ya göre ≥ %45 daha küçük (audiosr-v2: 4.0 → 2.1 GB) | dizin + boyut · [Satır: 530–549], [Satır: 61–72] | `.onnx` boyut farkı |
| `AI-LOCAL-BATCH` | `maxQueueSize` ≤ 20 · aşımda reddet | jobQueue · [Satır: 632] | kuyruk derinliği |
| `AI-LOCAL-LAT` | Genre CNN 1 dk ses ≤ 0.3 s (RPi5) | benchmark · [Satır: 730] | 100 örnek P95 |
| `AI-LOCAL-RT` | RT kuyruk < 5 ms (GPU) · < 10 ms (CPU) | [Satır: 428–429] | `realtimeBudgetPercent: 25` içinde |
| `AI-LOCAL-GRACEFUL` | EP başarısız → düşüş zinciri (§5.6) → AI kapalı, ses zinciri etkilenmez | fail-safe · audio-core.md [Satır: 27] | olay kaydı |
| `AI-LOCAL-INTEGRITY` | `checksums.sha256` doğrulaması başarısızsa model yüklenmez | [Satır: 516], [Satır: 538] | hash karşılaştırma |

### §8.2 Gate koşulunun kod karşılığı (tasarım — yazılmadı, çalıştırılmadı)

> Toplam kod = koşul + durum + ölçüm; kanıt "çalıştır" değil `_cmd/_fields/_phase`
> okumasıdır (bkz. [.ai/k005-gates.md](/ai/k005-gates.md)).

```php
// TASARIM — bu turda yazılmadı; eşikler §8.1'den gelir
public function gateLocalLoadMs(float $ms, int $modelBytes): bool
{
    $limitMs = (int) $this->setting('local.load_ms_limit', 1500);
    $budget  = (int) ($this->setting('local.max_vram_mb', 8192) * 1048576);
    return $ms <= $limitMs && $modelBytes <= $budget;   // ölçüm §7'de
}
```

```cpp
// TASARIM — vault spesifikasyon alıntısıdır, derlenmiş kod değildir
// Dosya: .ai/projects/NevaEngine/legacy-2026-07/ai-models.md [Satır: 144–162]
bool ready(const std::string& modelId) const {
    auto it = sessions.find(modelId);
    return it != sessions.end();            // K-8: status READY karşılığı
}
```

### §8.3 PHP ↔ C++ eşleme (lokal inference yüzeyi)

| K006 kapı | PHP tarafı (repo kanıtı) | C++ tarafı (vault spec) |
|---|---|---|
| K-1/K-2 donanım + EP | yok — `AIEngine.php` yalnız `'runtime' => 'cpu'` mock etiketi [Satır: 19] | `ExecutionProvider` enum · ai-models.md [Satır: 144–151] |
| K-6 model indirme onayı | yok (repo'da model updater yok) | `updates.autoDownload: false` · [Satır: 653] |
| K-8 yükleme durumu | yok | `loadModel` → `sessions` map · [Satır: 155–156] |
| K-10/K-15 kuyruk | yok | `jobQueue` config · [Satır: 631–635] |
| K-11 sanity | yok | benchmark satırları · [Satır: 719], [Satır: 730] |

**Eşleme çıkarımı:** lokal inference yüzeyinin **PHP karşılığı yoktur** (grep `onnx|onnxruntime|tflite|local_inference` → 0 isabet, §1); tüm kapılar C++/spec tarafındadır. PHP tarafı yalnız `runtime` etiketini taşır (§1 kod bloğu).

### §8.4 Ölçüm yöntemleri (gate nasıl hesaplanır)

| Metrik | Hesap | Kaynak |
|---|---|---|
| `AI-LOCAL-LOAD-MS` | `loadModel` çağrısı başlangıç↔bitiş farkı | stopwatch ms |
| `AI-LOCAL-MEM-MB` | süreç WorkingSet / 1MB → karşılaştırma `maxVRAM` | OS süreç ölçümü |
| `AI-LOCAL-QUANT` | `(fp16_bytes − int8_bytes) / fp16_bytes` | [Satır: 61–72] boyutları |
| `AI-LOCAL-LAT` | 100 segment süresi sıralı → 95. indeks | P95 hesabı |
| `AI-LOCAL-GRACEFUL` | düşüş zinciri tetiklenme zamanı (§5.6) | olay kaydı |
| `AI-LOCAL-INTEGRITY` | `checksums.sha256` hash ↔ dosya hash eşleşmesi | SHA-256 |

### §8.5 §8 tur kapanış kaydı

- Eklenen: 10 gate (`AI-LOCAL-*`) + 2 tasarım kod bloğu + 5 satır PHP↔C++ eşleme + 6 satır ölçüm yöntemi.
- Çalıştırılan gate: **0** → ölçüm `../yapay-zeka-dogrulama.md` §7'ye taşındı; bu bölümdeki her sayı **eşik tasarımıdır**, ölçüm sonucu değildir.
- Uydurma identifier: 0 · ölçü uydurma: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 0.

---

## §9 Kapanış Derinliği — Bağlar ve okuma izi

### §9.1 Bu dosyanın bağları

| Bağ | Hedef | İlişki |
|---|---|---|
| Kapsam | `[[local-inference-kapsam]]` | maddeler ↔ örnek |
| Doğrulama | `../yapay-zeka-dogrulama.md` §7 | taşınan gate listesi (§7, §8) |
| Test | `../yapay-zeka-test-stratejisi.md` | K-11…K-13 test senaryosu kaynağı |
| Karar | `../yapay-zeka-kararlar.md` §H | auto-EP çözümleme · checksum zorunluluğu · `maxVRAM` gerilimi |
| Risk | `../yapay-zeka-riskler.md` | EP düşüş zinciri · VRAM aşımı |
| Model yönetimi | `../20-inference/model-management-ornekler.md` §5.3 | `READY` durum makinesi (K-8) |
| Ses zekası | `../05-audio-intelligence/audio-intelligence-ornekler.md` §5.3 | `genres.json` sınıf kaynağı (LI-4 not 2) |

### §9.2 Okunan kaynaklar (bu tur, satır kanıtlı)

| Dosya | Kapsam | Satır |
|---|---|---|
| `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | sınıf · donanım matrisi · dizin · config · benchmark | 31–52 · 61–72 · 129–156 · 428–429 · 516–549 · 555 · 581–588 · 621–658 · 666–690 · 719 · 730–733 |
| `.ai/projects/NevaEngine/legacy-2026-07/audio-core.md` | fail-safe | 27 |
| `shared/src/AI/AIEngine.php` | `runtime` etiketi | 19 |
| onnxruntime.ai / GitHub (llama.cpp · ort-node) | §2 resmi örnekler | HTTP 200 · 2026-10-14 |

Okunmayan/ilgili eşikler bu turda `⚠️ VERIFICATION REQUIRED` olarak işaretlidir (§5.3 · §6.3).

### §9.3 §9 tur kapanış kaydı

- Eklenen: 7 bağ + 4 satır okuma izi.
- Uydurma dosya/yol: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 0.

**Authority:** Master-Orchestrator · **Status:** active · **Updated:** 2026-10-15
