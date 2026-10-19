---
type: technical-spec
project: Neva Engine
module: ai-models
version: 1.0.0
date: 2026-06-02
status: draft
language: tr-TR
---

# Neva Engine — AI Ses İşleme Modelleri

## 1. Genel Bakış

Neva Engine AI modülü, derin öğrenme tabanlı ses iyileştirme, müzik türü
sınıflandırma, otomatik EQ ve ses upscaling işlemlerini gerçekleştiren,
ONNX Runtime üzerinde çalışan, platformlar arası bir inference altyapısıdır.

### Tasarım Hedefleri

| Hedef               | Açıklama                                    |
| ------------------- | ------------------------------------------- |
| Platform bağımsız   | ONNX Runtime, CUDA/DirectML/CoreML/CPU      |
| Gerçek zamanlı      | Hafif modeller (< 10 ms inference)          |
| Batch processing    | Ağır modeller arka planda                   |
| Otomatik güncelleme | HuggingFace Hub'dan model indirme           |
| Ölçeklenebilir      | PC (RTX 4090) → RPi 5 (CPU INT8) →  Mac OS) |

### Donanım Destek Matrisi

| Platform                  | GPU          | CPU Optimizasyonu | Hassasiyet |
| ------------------------- | ------------ | ----------------- | ---------- |
| Windows (RTX 5000 series) | CUDA 13.x    |                   |            |
| Windows (RTX 4000 series) | CUDA 13.x    |                   |            |
| Windows (RTX 3000 series) | CUDA 13.x    |                   |            |
| Windows (RTX 2000 series) | CUDA 12.x    |                   |            |
| Windows (GTX 1000 series) | CUDA 10.x    |                   |            |
| macOS (M series base)     | CoreML / MPS |                   |            |
| macOS (M series Pro)      | CoreML / MPS |                   |            |
| macOS (M series Max)      | CoreML / MPS |                   |            |
| macOS (M series Ultra)    | CoreML / MPS |                   |            |
| macOS (İntel series)      |              |                   |            |
| Linux (RTX 5000 series)   | CUDA 13.x    |                   |            |
| Linux (RTX 4000 series)   | CUDA 13.x    |                   |            |
| Linux (RTX 3000 series)   | CUDA 13.x    |                   |            |
| Linux (RTX 2000 series)   | CUDA 12.x    |                   |            |
| Linux (GTX 1000 series)   | CUDA 10.x    |                   |            |
| Intel Arc                 |              |                   |            |
| Raspberry Pi 6            |              |                   |            |
| Raspberry Pi 5            |              |                   |            |
| Raspberry Pi 4            |              |                   |            |
| CPU-only (x86)            |              |                   |            |


---

## 2. Ses Upscaling Modelleri

### 2.1 Model Karşılaştırma Tablosu

| Model            | VRAM (FP16) | VRAM (INT8) | CPU Süresi (1 dk ses) | GPU Süresi | Açıklama                              |
| ---------------- | ----------- | ----------- | --------------------- | ---------- | ------------------------------------- |
| AudioSR          | 4.0 GB      | 2.1 GB      | —                     | 12s        | Ses süper çözünürlük, 24kHz→48kHz     |
| Demucs HT        | 2.7 GB      | 1.4 GB      | 45s                   | 8s         | Kaynak ayırma (vokal/davul/bas/diğer) |
| Resemble Enhance | 2.0 GB      | 1.1 GB      | 30s                   | 5s         | Konuşma iyileştirme, denoising        |
| BSRNN            | 1.5 GB      | 0.8 GB      | 25s                   | 4s         | Bant-genişlik genişletme (8kHz→48kHz) |
| NuWave2          | 3.0 GB      | 1.6 GB      | 50s                   | 9s         | Ses süper çözünürlük                  |
| HiFi-GAN         | 1.0 GB      | 0.5 GB      | 8s                    | 1.5s       | Vocoder, en hızlı                     |
| WaveGlow         | 2.5 GB      | 1.3 GB      | 40s                   | 7s         | Akış tabanlı vocoder                  |
| VoiceFixer       | 1.8 GB      | 1.0 GB      | 20s                   | 4s         | Konuşma restorasyonu                  |
| DEMUCS Denoiser  | 1.2 GB      | 0.7 GB      | 18s                   | 3s         | Gürültü temizleme (hafif)             |
| DeepFilterNet2   | 0.6 GB      | 0.3 GB      | 5s                    | —          | Gerçek zamanlı denoising, en hafif    |

### 2.2 AudioSR (Ses Süper Çözünürlük)

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

**AudioSR Kullanım Senaryoları:**
- Eski kayıtların (24 kHz) modern kaliteye (48 kHz) yükseltilmesi
- Düşük kaliteli MP3'lerin frekans spektrumunun genişletilmesi
- Telefon kayıtlarının (8 kHz) müzik kalitesine yaklaştırılması

### 2.3 DeepFilterNet2 (Gerçek Zamanlı Denoising)

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

---

## 3. ONNX Runtime Inference Pipeline

### 3.1 ONNX Runtime Yapılandırması

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

### 3.2 ONNX Inference Akışı

```
Model Yükleme
    │
    ▼
Input Tensor Hazırlama
    │
    ├── Ses → Mel-Spectrogram / STFT
    ├── Normalizasyon (mean/std)
    ├── Padding (chunk size'a tamamla)
    └── Ort::Value oluştur
    │
    ▼
Ort::Session::Run()
    │
    ▼
Output Tensor İşleme
    │
    ├── Denormalizasyon
    ├── Overlap-add (chunk birleştirme)
    └── Float32 buffer'a dönüştürme
```

### 3.3 Tensor Formatları

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

---

## 4. Model Optimizasyonu

### 4.1 Quantization (FP16 → INT8)

```
Hassasiyet Azaltma Stratejisi:

FP32 (4 byte/tensor)    → Referans kalite, en yavaş
    ↓
FP16 (2 byte/tensor)    → %98 kalite, 2x hız, 2x az VRAM
    ↓
INT8 (1 byte/tensor)    → %95 kalite, 4x hız, 4x az VRAM
    ↓
INT4 (0.5 byte/tensor)  → %85 kalite, 8x hız (deneysel)

Quantization Yöntemleri:
- Post-Training Static Quantization (calibration dataset gerekli)
- Post-Training Dynamic Quantization (calibration gerekmez, daha hızlı)
- Quantization-Aware Training (QAT, en yüksek kalite)
```

### 4.2 Pruning (Ağırlık Budama)

```cpp
struct PruningConfig {
    float sparsityTarget = 0.5;     // %50 budama hedefi
    PruningMethod method = PruningMethod::MAGNITUDE;
    int fineTuneEpochs = 5;
};

enum class PruningMethod {
    MAGNITUDE,      // En küçük ağırlıkları buda
    GRADIENT,       // Gradient tabanlı
    STRUCTURED,     // Kanal/filtre seviyesinde
    UNSTRUCTURED,   // Bireysel ağırlık seviyesinde
};
```

### 4.3 Knowledge Distillation

```
Teacher Model (Büyük, yavaş, yüksek kalite)
    │
    │  Soft labels (olasılık dağılımı)
    ▼
Student Model (Küçük, hızlı, optimize)
    │
    ▼
ONNX export → INT8 quantize → deploy

Örnek:
Teacher: AudioSR (4 GB VRAM)
Student: AudioSR-Tiny (1 GB VRAM, %90 kalite)
```

---

## 5. AI Auto-EQ

### 5.1 Müzik Türü Sınıflandırma

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

### 5.2 Genre Detection Mimarisi

```
Ses Sinyali (3 sn)
    │
    ▼
┌──────────────────┐
│ Mel-Spectrogram  │  (128 mel band, 256 FFT, 10ms hop)
│ (128 × 300)      │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ CNN Encoder      │
│  4× Conv2D       │  (3×3 kernel, BatchNorm, ReLU)
│  2× MaxPool      │
│  GlobalAvgPool   │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ Feature Vector   │  (512 boyutlu embedding)
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ Classifier Head  │  (Dense 512 → 256 → 12)
│  Softmax         │
└────────┬─────────┘
         │
         ▼
Genre + Confidence (örn: Arabesk, 0.87)
```

### 5.3 Auto-EQ Tam Akış

```
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

### 5.4 Auto-EQ Yapılandırması

```json
{
  "autoEQ": {
    "enabled": true,
    "defaultReference": "genre_specific",
    "aggressiveness": 0.7,
    "maxGainPerBand": 6.0,
    "minConfidenceForApply": 0.6,
    "analysisWindowSeconds": 5.0,
    "updateIntervalSeconds": 30,
    "smoothingMs": 1000,
    "genreOverrides": {
      "arabesk": {
        "aggressiveness": 0.8,
        "maxGainPerBand": 8.0
      },
      "klasik": {
        "aggressiveness": 0.3,
        "maxGainPerBand": 3.0
      }
    }
  }
}
```

---

## 6. Ses Ön-İşleme (Upload Pipeline)

### 6.1 Upload İşleme Akışı

```
Kullanıcı dosya yükleme (CoreMusic Web UI)
    │
    ▼
┌─────────────────────────────────────┐
│  Ses Ön-İşleme Pipeline (Batch)     │
│                                     │
│  1. Format algılama (FFmpeg probe) │
│  2. Format dönüşümü (→ WAV 48kHz) │
│  3. Gürültü analizi                │
│  4. AI Denoising (DeepFilterNet2)  │
│  5. Loudness normalizasyonu (EBU)  │
│  6. AI Upscaling (gerekirse)       │
│  7. Metadata çıkarma               │
│  8. DB'ye kaydetme                 │
│  9. Orijinal + işlenmiş saklama    │
└─────────────────────────────────────┘
    │
    ▼
Kullanıcıya bildirim: "Dosya işlendi"
```

### 6.2 İşleme Stratejisi

| Dosya Durumu | Aksiyon |
|-------------|---------|
| 48 kHz FLAC/WAV | Sadece loudness normalize |
| 44.1 kHz FLAC | 48 kHz upsample (libsamplerate) |
| 320 kbps MP3 | Decode → loudness normalize |
| 128 kbps MP3 | Decode → AI upscale (AudioSR) → normalize |
| 8 kHz mono (telefon) | AI upscale (BSRNN) → stereo genişletme → normalize |
| Gürültülü kayıt | AI denoising (DeepFilterNet2) → normalize |
| Clipping/bozuk | AI restorasyon (VoiceFixer) → normalize |

---

## 7. Real-Time vs Batch Stratejisi

### 7.1 Model Kategorileri

| Kategori | Modeller | Inference Süresi | Kullanım |
|----------|----------|-----------------|----------|
| Real-Time (GPU) | DeepFilterNet2, HiFi-GAN | < 5 ms | Canlı denoising, vocoder |
| Real-Time (CPU) | DeepFilterNet2 (INT8), Genre CNN | < 10 ms | Canlı auto-EQ |
| Fast Batch (GPU) | Demucs, Resemble Enhance | 5-15s / dakika | Upload processing |
| Slow Batch (GPU) | AudioSR, NuWave2, BSRNN | 10-40s / dakika | Offline upscaling |
| Heavy Batch | Custom large models | 1-10 dk | Arka plan işleme |

### 7.2 Job Queue Sistemi

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

### 7.3 Job API

```json
// AI işleme başlat (batch)
{"method": "ai.process", "params": {
    "model": "audiosr",
    "inputFile": "/media/upload/track_123.mp3",
    "outputFile": "/media/processed/track_123_enhanced.wav",
    "priority": "normal",
    "config": {
        "targetSampleRate": 48000,
        "denoiseEnabled": true,
        "normalizeEnabled": true,
        "normalizeTarget": -16
    }
}, "id": 1}

// İş durumu sorgulama
{"method": "ai.getJobStatus", "params": {"jobId": "uuid-123"}, "id": 2}
// Yanıt: {"result": {"status": "running", "progress": 0.65, "eta": 8}}

// Tüm işleri listele
{"method": "ai.listJobs", "params": {}, "id": 3}

// İş iptal et
{"method": "ai.cancelJob", "params": {"jobId": "uuid-123"}, "id": 4}

// Auto-EQ başlat
{"method": "ai.autoEQ", "params": {
    "mode": "continuous",
    "reference": "genre_specific",
    "aggressiveness": 0.7
}, "id": 5}

// Genre detection (tek seferlik)
{"method": "ai.detectGenre", "params": {
    "file": "/media/track_123.wav"
}, "id": 6}
```

---

## 8. Model Yönetimi

### 8.1 HuggingFace Entegrasyonu

```cpp
class ModelRegistry {
public:
    struct ModelInfo {
        std::string id;                 // "neva/audiosr-v2"
        std::string name;               // "AudioSR v2"
        std::string version;            // "2.1.0"
        std::string hfRepo;             // "neva-engine/audiosr-v2"
        std::string hfFile;             // "model.onnx"
        std::string localPath;          // "./models/audiosr-v2/"
        uint64_t sizeBytes;
        std::string checksumSHA256;
        ModelStatus status;             // NOT_DOWNLOADED, DOWNLOADING, READY, ERROR
        std::vector<ModelVariant> variants;  // FP16, INT8, FP32
    };

    std::vector<ModelInfo> listAvailable();
    void downloadModel(const std::string& modelId, ModelVariant variant);
    void deleteModel(const std::string& modelId);
    void checkForUpdates();
};
```

### 8.2 Model Versiyonlama

```
Model Dizin Yapısı:
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

### 8.3 Otomatik Güncelleme

```cpp
class ModelUpdater {
    std::string hfApiUrl = "https://huggingface.co/api/models/";
    std::chrono::hours checkInterval{24};

public:
    struct UpdateInfo {
        std::string modelId;
        std::string currentVersion;
        std::string latestVersion;
        bool updateAvailable;
        std::string changelog;
    };

    std::vector<UpdateInfo> checkForUpdates();
    void updateModel(const std::string& modelId, ProgressCallback callback);
    void updateAll(ProgressCallback callback);
};
```

---

## 9. Ses Kalitesi Metrikleri

### 9.1 Objektif Metrikler

| Metrik | Açılım | Aralık | Hedef | Açıklama |
|--------|--------|--------|-------|----------|
| PESQ | Perceptual Evaluation of Speech Quality | 1.0 - 4.5 | > 3.5 | Konuşma kalitesi |
| STOI | Short-Time Objective Intelligibility | 0 - 1 | > 0.85 | Konuşma anlaşılırlığı |
| MOS | Mean Opinion Score | 1 - 5 | > 4.0 | Genel kalite |
| SI-SDR | Scale-Invariant Signal-to-Distortion Ratio | -∞ ... ∞ | > 15 dB | Kaynak ayırma |
| SNR | Signal-to-Noise Ratio | -∞ ... ∞ | > 30 dB | Gürültü seviyesi |
| PSNR | Peak Signal-to-Noise Ratio | -∞ ... ∞ | > 40 dB | Sinyal kalitesi |
| LSD | Log-Spectral Distance | 0 ... ∞ | < 2.0 dB | Spektral benzerlik |
| MCD | Mel-Cepstral Distortion | 0 ... ∞ | < 3.0 dB | Ses benzerliği |

### 9.2 Metrik Hesaplama

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

---

## 10. Yapılandırma

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
    "autoEQ": {
      "enabled": false,
      "defaultReference": "genre_specific",
      "aggressiveness": 0.7,
      "analysisWindowSeconds": 5.0
    },
    "uploadProcessing": {
      "enabled": true,
      "denoiseEnabled": true,
      "upscaleEnabled": false,
      "normalizeTarget": -16,
      "normalizeEnabled": true,
      "formats": ["flac", "wav", "mp3", "aac", "ogg"]
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

---

## 11. Deployment Stratejisi

### 11.1 Donanım-Özel Konfigürasyonlar

| Donanım               | Provider | Precision | Eş Zamanlı Model | Kullanım Senaryosu              |
| --------------------- | -------- | --------- | ---------------- | ------------------------------- |
| RTX 4090 (24 GB)      | CUDA     | FP16      | 4                | Tam kapasite, production        |
| RTX 4070 (12 GB)      | CUDA     | FP16      | 2                | Orta seviye production          |
| RTX 3060 (12 GB)      | CUDA     | FP16/INT8 | 1                | Giriş seviyesi GPU              |
| GTX 1660 (6 GB)       | CUDA     | INT8      | 1                | Eski GPU, sadece hafif modeller |
| M2 Pro (16 GB)        | CoreML   | FP16      | 2                | macOS production                |
| Ryzen 7 (32 GB)       | CPU      | INT8      | 1                | GPU'suz, hafif modeller         |
| Raspberry Pi 5 (8 GB) | CPU      | INT8      | 0                | Sadece genre detection          |

### 11.2 Ölçeklendirme Stratejisi

```
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

---

## 12. Gelecek Modeller ve Genişletmeler

| Model | Tip | Tahmini VRAM | Açıklama |
|-------|-----|-------------|----------|
| MusicGen | Müzik üretimi | 8 GB | Meta'nın müzik üretim modeli |
| AudioLDM 2 | Text-to-audio | 6 GB | Metinden ses üretimi |
| Stable Audio | Müzik üretimi | 12 GB | Yüksek kalite müzik sentezi |
| EnCodec | Ses codec | 2 GB | Neural audio codec |
| Spleeter | Kaynak ayırma | 1 GB | Deezer kaynak ayırıcı |
| CLAP | Ses-metin embedding | 3 GB | Multimodal ses anlama |
| WavJourney | Ses hikaye üretimi | 8 GB | Metinden ses senaryosu |
| VALL-E | Ses klonlama | 6 GB | 3 sn'den ses klonlama |
| RAVE | Gerçek zamanlı sentez | 2 GB | Neural synthesizer |
| AudioMAE | Ses temsili | 1 GB | Self-supervised audio |

---

## 13. Performans Ölçümleri

### 13.1 Benchmark Sonuçları (RTX 4090, FP16)

| Model | 1 dk ses işleme | Throughput | VRAM | Kalite (MOS) |
|-------|----------------|-----------|------|-------------|
| DeepFilterNet2 | 0.3s | 200x real-time | 0.6 GB | 4.1 |
| HiFi-GAN | 0.5s | 120x real-time | 1.0 GB | 4.0 |
| Genre CNN | 0.02s | 3000x | 0.05 GB | — |
| Demucs HT | 3.0s | 20x real-time | 2.7 GB | 4.3 |
| Resemble Enhance | 2.5s | 24x real-time | 2.0 GB | 4.2 |
| BSRNN (INT8) | 3.5s | 17x real-time | 0.8 GB | 3.9 |
| AudioSR | 5.0s | 12x real-time | 4.0 GB | 4.5 |
| NuWave2 | 6.0s | 10x real-time | 3.0 GB | 4.4 |

### 13.2 Raspberry Pi 5 Benchmark (CPU, INT8)

| Model | 1 dk ses işleme | Throughput |
|-------|----------------|-----------|
| Genre CNN | 0.3s | 200x |
| DeepFilterNet2 | 18.0s | 3.3x |
| BSRNN | 45.0s | 1.3x |
| AudioSR | — | Çalışmaz (VRAM yok) |

---

*Neva Engine v1.0.0 — AI Ses İşleme Spesifikasyonu*
*CoreMusic Ekosistemi · GPLv3 · C++20 · JUCE 8.x*
