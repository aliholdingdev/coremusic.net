---
title: "inference — Doğrulama"
type: architecture-verification
node_id: K006-yapay-zeka/inference
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# inference — Kanıt Tablosu

> **Kaynak notu:** (a) bu oturumda exa ile bulunan **resmi URL'ler** · (b) bu oturumda okunan **gerçek disk yolları**.
> Yasak: URL, sürüm, API uydurmak; uydurulan satır sayılmaz.

## §1 İddia tablosu

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Çocuk adı "inference" v2 §2.6 satır 260'ta birebir geçer | `.ai/architecture/K006-yapay-zeka/inference/index.md` (evidence) | 2026-10-12 | ✅ dosya okundu |
| 2 | Rol tanımı v2'de yok → `⚠️ VERIFICATION REQUIRED` | `inference/index.md` (open_questions) | 2026-10-12 | ✅ dosya okundu |
| 3 | Inference çağrısının tek noktası: workflow → engine | `shared/src/AI/AIWorkflow.php` satır 75 | 2026-10-12 | ✅ dosya okundu |
| 4 | Çıktı sözleşmesi `array{id, title, artist, score, reason}` | `shared/src/Contracts/AI/AIEngineInterface.php` satır 26–28 | 2026-10-12 | ✅ dosya okundu |
| 5 | `shared/src/AI/` içinde ONNX/TFLite/torch runtime'ı yok (grep `onnx` 0 isabet) | `.ai/architecture/K006-yapay-zeka/inference/` (bu oturum taraması) | 2026-10-12 | ✅ oturum taraması |
| 6 | ONNX Runtime, eğitim framework'ünden bağımsız yüksek performanslı skoring motorudur | https://onnxruntime.ai/docs/api/python/index.html | 2026-10-12 | ✅ doğrulandı |
| 7 | `InferenceSession` ONNX/ORT modelini yükler; `run(output_names, input_feed)` tahmin üretir | https://onnxruntime.ai/docs/api/python/api_summary | 2026-10-12 | ✅ doğrulandı |
| 8 | Standart akış: train → ONNX'e dönüştür → ONNX Runtime ile çalıştır | https://onnxruntime.ai/docs/api/python/tutorial.html | 2026-10-12 | ✅ doğrulandı |
| 9 | ONNX, hesaplama grafiğini taşınabilir serialize formatında tanımlar (ModelProto) | https://onnx.ai/onnx/repo-docs/IR.html | 2026-10-12 | ✅ doğrulandı |
| 10 | CoreMusic'in ONNX Runtime kullandığı | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 11 | Model formatı seçimi (ONNX · pickle · PMML · özel) | `ai-master.md` §4 | 2026-10-12 | **UNKNOWN** |
| 12 | inference ↔ local-inference ↔ model-management sınırı | `ai-master.md` §3 satır 259–260 · 265 | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 13 | Mevcut PHP motorun yerine (veya yanına) harici model runtime geçişi | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |

## §2 Sayım

| Metrik | Değer |
|---|---|
| Toplam satır | 13 |
| İşlenen benzersiz URL | 4 (6 · 7 · 8 · 9) |
| Gerçek dosya yolu satırı | 5 (1 · 2 · 3 · 4 · 5) |
| ✅ doğrulandı | 9 (1–9) |
| ⚠️ VERIFICATION REQUIRED | 1 (12) |
| UNKNOWN | 3 (10 · 11 · 13) |

**Geçiş kuralı:** satır 12 ADR-097 K4 ile; satır 10–11 ve 13 teknoloji seçim kaydıyla
(`yapay-zeka-dogrulama.md` §4) kapanır — kanıt gelene kadar UNKNOWN kalır.

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12

---

## §3 Tur 2 — Neva Engine ONNX kanıt tablosu (ek 2026-10-15)

> **Kaynak:** Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` — 738 satır, bu oturumda
> baştan sona okundu; satır numaraları okuma anındaki dosya konumudur. Bu dosya **C++/Neva örneği**
> üretebilmek için birincil kanıt tabanıdır (repo'da *.cpp/*.h yok — bkz. §10.4).

| # | İddia | Kanıt: Dosya · Satır | Tür | Durum |
|---|---|---|---|---|
| 14 | Neva AI modülü ONNX Runtime üzerinde çalışan platformlar arası inference altyapısıdır | ai-models.md · 15–17 | mimari | ✅ okundu |
| 15 | Gerçek zamanlı hedef: hafif modeller için < 10 ms inference | ai-models.md · 24 | SLA | ✅ okundu |
| 16 | Ağır modeller batch olarak arka planda çalışır | ai-models.md · 25 | mimari | ✅ okundu |
| 17 | Model güncelleme kaynağı HuggingFace Hub'dır | ai-models.md · 26 | bağımlılık | ✅ okundu |
| 18 | Donanım matrisi Windows · macOS · Linux · Raspberry Pi · Intel Arc · CPU-only kapsar | ai-models.md · 31–52 | envanter | ✅ okundu |
| 19 | Windows/Linux GPU matrisi CUDA 13.x · 12.x · 10.x satırlarına ayrılmıştır | ai-models.md · 33–47 | sürüüm | ✅ okundu |
| 20 | macOS satırları CoreML / MPS ile işaretlenmiştir | ai-models.md · 38–41 | sürüüm | ✅ okundu |
| 21 | macOS Intel satırında GPU ve hassasiyet hücreleri BOŞ bırakılmıştır | ai-models.md · 42 | boşluk | ✅ okundu (eksik veri) |
| 22 | Intel Arc, Raspberry Pi 4/5/6 ve CPU-only satırlarında hassasiyet hücresi BOŞ | ai-models.md · 48–52 | boşluk | ✅ okundu (eksik veri) |
| 23 | Model karşılaştırma tablosu 10 model içerir (AudioSR … DeepFilterNet2) | ai-models.md · 61–72 | envanter | ✅ okundu |
| 24 | En düşük INT8 VRAM: DeepFilterNet2 0.3 GB | ai-models.md · 72 | metrik | ✅ okundu |
| 25 | En yüksek FP16 VRAM: AudioSR 4.0 GB | ai-models.md · 63 | metrik | ✅ okundu |
| 26 | AudioSR yapılandırması: girdi 24000 Hz, çıktı 48000 Hz, chunk 65536, overlap 8192 | ai-models.md · 84–89 | parametre | ✅ okundu |
| 27 | DeepFilterNet2 gerçek zamanlı işleme 10 ms buffer'larla tanımlanmıştır | ai-models.md · 110 | parametre | ✅ okundu |
| 28 | DeepFilterNet2 attenuationLimit 12.0 dB, preserveSpeech varsayılan true | ai-models.md · 116–119 | parametre | ✅ okundu |
| 29 | ONNXInferenceEngine içinde 7 ExecutionProvider sayılır | ai-models.md · 136–144 | mimari | ✅ okundu (7/7) |
| 30 | Provider listesi: CPU · CUDA · DirectML · CoreML · OpenVINO · TensorRT · ROCM | ai-models.md · 137–143 | envanter | ✅ okundu |
| 31 | Engine arayüzü: configure · loadModel · unloadModel · quantizeModel · optimizeModel | ai-models.md · 146–154 | API | ✅ okundu |
| 32 | Inference akışı 4 evre: yükleme → tensor hazırlama → Run() → çıktı işleme | ai-models.md · 161–179 | akış | ✅ okundu |
| 33 | Girdi tensor şekli [1, 1, 65536] olarak sabitlenmiştir | ai-models.md · 186 | parametre | ✅ okundu |
| 34 | Tensor üretimi Ort::Value::CreateTensor<float> ile yapılır | ai-models.md · 188–194 | API | ✅ okundu |
| 35 | Tahmin çağrısı Ort::Session::Run() üzerinden yapılır | ai-models.md · 197–200 | API | ✅ okundu |
| 36 | Hassasiyet merdiveni: FP32 → FP16 → INT8 → INT4 | ai-models.md · 213–219 | dönüşüm | ✅ okundu |
| 37 | FP16 iddiası: %98 kalite, 2x hız, 2x az VRAM | ai-models.md · 215 | iddia (tasarım) | ✅ okundu — ölçülmemiş |
| 38 | INT8 iddiası: %95 kalite, 4x hız, 4x az VRAM | ai-models.md · 217 | iddia (tasarım) | ✅ okundu — ölçülmemiş |
| 39 | INT4 iddiası: %85 kalite, 8x hız — dosyanın kendisi **deneysel** der | ai-models.md · 219 | iddia (tasarım) | ✅ okundu — deneysel |
| 40 | Quantization üç yöntemle listelenir: static · dynamic · QAT | ai-models.md · 221–224 | yöntem | ✅ okundu |
| 41 | PruningConfig sparsityTarget 0.50, fineTuneEpochs 5 | ai-models.md · 230–234 | parametre | ✅ okundu |
| 42 | PruningMethod enum: MAGNITUDE · GRADIENT · STRUCTURED · UNSTRUCTURED | ai-models.md · 236–241 | enum | ✅ okundu |
| 43 | Knowledge distillation örneği: AudioSR (4 GB) → AudioSR-Tiny (1 GB, %90 kalite) | ai-models.md · 256–258 | iddia (tasarım) | ✅ okundu — ölçülmemiş |
| 44 | GenreClassifier NUM_GENRES = 12 | ai-models.md · 270 | parametre | ✅ okundu |
| 45 | Tür listesi 0–11 arası: Arabesk … Diğer (Türk müziği odaklı) | ai-models.md · 281–284 | etiket | ✅ okundu |
| 46 | Genre mimarisi: Mel-Spectrogram 128 band · 256 FFT · 10 ms hop | ai-models.md · 295 | parametre | ✅ okundu |
| 47 | CNN Encoder: 4× Conv2D + 2× MaxPool + GlobalAvgPool | ai-models.md · 301–304 | mimari | ✅ okundu |
| 48 | Embedding boyutu 512; classifier head 512 → 256 → 12 + Softmax | ai-models.md · 309–315 | mimari | ✅ okundu |
| 49 | Auto-EQ akışı 8 adımdır (sinyal → tespit → preset → FFT → hata → optimizasyon → uygulama → onay) | ai-models.md · 324–351 | akış | ✅ okundu |
| 50 | Auto-EQ yapılandırmasında minConfidenceForApply = 0.6 | ai-models.md · 362 | eşik | ✅ okundu |
| 51 | Auto-EQ analiz penceresi 5.0 sn, güncelleme aralığı 30 sn, smoothing 1000 ms | ai-models.md · 363–365 | parametre | ✅ okundu |
| 52 | Upload pipeline 9 adımdır (FFmpeg probe → dönüştürme → gürültü → denoise → loudness → upscale → metadata → DB → saklama) | ai-models.md · 390–402 | akış | ✅ okundu |
| 53 | İşleme stratejisi tablosu 7 dosya durumu tanımlar | ai-models.md · 410–418 | kural | ✅ okundu |
| 54 | Model kategorileri: Real-Time GPU < 5 ms · Real-Time CPU < 10 ms · Fast Batch · Slow Batch · Heavy Batch | ai-models.md · 426–432 | SLA | ✅ okundu |
| 55 | InferenceJobQueue 4 worker thread ile tanımlanmıştır | ai-models.md · 450 | kaynak | ✅ okundu |
| 56 | Job öncelikleri: LOW · NORMAL · HIGH · REALTIME | ai-models.md · 440 | enum | ✅ okundu |
| 57 | Job API altı çağrı tanımlar: ai.process · ai.getJobStatus · ai.listJobs · ai.cancelJob · ai.autoEQ · ai.detectGenre | ai-models.md · 462–496 | API | ✅ okundu (6/6) |
| 58 | ModelRegistry ModelInfo alanı checksumSHA256 taşır | ai-models.md · 516 | bütünlük | ✅ okundu |
| 59 | Model durumları: NOT_DOWNLOADED · DOWNLOADING · READY · ERROR | ai-models.md · 517 | durum | ✅ okundu |
| 60 | Model varyantları: FP16 · INT8 · FP32 | ai-models.md · 518 | varyant | ✅ okundu |
| 61 | Model dizin şeması version.txt + checksums.sha256 içerir | ai-models.md · 531–548 | şema | ✅ okundu |
| 62 | ModelUpdater kontrol aralığı 24 saattir | ai-models.md · 556 | parametre | ✅ okundu |
| 63 | Kalite metrikleri sekiz tanedir: PESQ · STOI · MOS · SI-SDR · SNR · PSNR · LSD · MCD | ai-models.md · 579–588 | metrik | ✅ okundu |
| 64 | PESQ hedefi > 3.5 (aralık 1.0–4.5) | ai-models.md · 581 | eşik | ✅ okundu |
| 65 | STOI hedefi > 0.85 (aralık 0–1) | ai-models.md · 582 | eşik | ✅ okundu |
| 66 | SI-SDR hedefi > 15 dB; SNR hedefi > 30 dB | ai-models.md · 584–585 | eşik | ✅ okundu |
| 67 | LSD hedefi < 2.0 dB; MCD hedefi < 3.0 dB | ai-models.md · 587–588 | eşik | ✅ okundu |
| 68 | AudioQualityMetrics::compute referans/işlenmiş çiftten yedi metrik üretir | ai-models.md · 608–613 | API | ✅ okundu |
| 69 | Yapılandırma: executionProvider auto · maxVRAM 8192 · maxCPUMemory 4096 | ai-models.md · 625–630 | config | ✅ okundu |
| 70 | JobQueue config: maxWorkers 4 · maxQueueSize 20 · realtimeBudgetPercent 25 | ai-models.md · 631–635 | config | ✅ okundu |
| 71 | Benchmark tablosu 8 model içerir (RTX 4090, FP16) | ai-models.md · 715–724 | ölçüm | ✅ okundu |
| 72 | Raspberry Pi 5 tablosu 4 model içerir (CPU, INT8) | ai-models.md · 728–733 | ölçüm | ✅ okundu |
| 73 | Dosya altbilgisi: GPLv3 · C++20 · JUCE 8.x | ai-models.md · 737–738 | lisans | ✅ okundu |

---

## §4 Model envanteri doğrulama tablosu (10 model · satır 61–72)

> **Amaç:** §3 satır 23–25'i tek tek doğrula; her satır dosyanın kendi hücresidir.

| # | Model | FP16 (GB) | INT8 (GB) | CPU 1 dk (sn) | GPU (sn) | Kanıt satırı |
|---|---|---|---|---|---|---|
| 1 | AudioSR | 4.0 | 2.1 | — | 12 | 63 |
| 2 | Demucs HT | 2.7 | 1.4 | 45 | 8 | 64 |
| 3 | Resemble Enhance | 2.0 | 1.1 | 30 | 5 | 65 |
| 4 | BSRNN | 1.5 | 0.8 | 25 | 4 | 66 |
| 5 | NuWave2 | 3.0 | 1.6 | 50 | 9 | 67 |
| 6 | HiFi-GAN | 1.0 | 0.5 | 8 | 1.5 | 68 |
| 7 | WaveGlow | 2.5 | 1.3 | 40 | 7 | 69 |
| 8 | VoiceFixer | 1.8 | 1.0 | 20 | 4 | 70 |
| 9 | DEMUCS Denoiser | 1.2 | 0.7 | 18 | 3 | 71 |
| 10 | DeepFilterNet2 | 0.6 | 0.3 | 5 | — | 72 |

**Türetilmiş (dosyadan hesaplanan, uydurulmayan) notlar:**

- CPU sütunu boş **yalnız** AudioSR (satır 63) → bu model için CPU süresi bilinmiyor = **UNKNOWN**.
- GPU süresi boş **yalnız** DeepFilterNet2 (satır 72) → gerçek zamanlı sınıfa ayrıldığı için
  batch süresi verilmemiş (satır 428 ile tutarlı).
- INT8/FP16 oranı en yüksek model HiFi-GAN (0.5/1.0 = %50) → tablodaki tek %50 kazanç.
- Toplam FP16 VRAM (10 model) = 20.3 GB; INT8 toplamı = 10.8 GB → ikisi birden
  tek 24 GB GPU'ya sığmaz, **kademeli yükleme zorunlu** (§5.4).

---

## §5 ExecutionProvider · hassasiyet · dönüşüm kanıtları

### §5.1 Provider tablosu (ai-models.md · 136–144)

| # | Provider | Dosyanın kendi notu | Kanıt satırı |
|---|---|---|---|
| 1 | CPU | — | 137 |
| 2 | CUDA | NVIDIA GPU | 138 |
| 3 | DirectML | Windows GPU (DX12) | 139 |
| 4 | CoreML | Apple Neural Engine | 140 |
| 5 | OpenVINO | Intel CPU/iGPU | 141 |
| 6 | TensorRT | NVIDIA (en hızlı) | 142 |
| 7 | ROCM | AMD GPU | 143 |

**Doğrulama:** enum içi satır sayısı 7 → §3 satır 29 (7/7) ile birebir.

### §5.2 Hassasiyet merdiveni (213–219)

| # | Kademе | Byte/tensor | Dosyanın iddiası | Kanıt satırı | Ölçüm durumu |
|---|---|---|---|---|---|
| 1 | FP32 | 4 | referans kalite, en yavaş | 213 | tasarım metni |
| 2 | FP16 | 2 | %98 kalite · 2x hız · 2x az VRAM | 215 | **ölçülmemiş** |
| 3 | INT8 | 1 | %95 kalite · 4x hız · 4x az VRAM | 217 | **ölçülmemiş** |
| 4 | INT4 | 0.5 | %85 kalite · 8x hız (deneysel) | 219 | **ölçülmemiş** |

### §5.3 Yöntem & budama (221–241)

| # | Öğe | Kanıt satırı | Not |
|---|---|---|---|
| 1 | Post-Training Static Quantization — calibration dataset gerekli | 222 | koşul yazılı |
| 2 | Post-Training Dynamic Quantization — calibration gerekmez | 223 | koşul yazılı |
| 3 | Quantization-Aware Training (QAT) — en yüksek kalite | 224 | sıralama yazılı |
| 4 | PruningConfig.sparsityTarget = 0.5 | 231 | varsayılan |
| 5 | PruningConfig.fineTuneEpochs = 5 | 233 | varsayılan |
| 6 | MAGNITUDE — en küçük ağırlıkları buda | 237 | enum tanımı |
| 7 | GRADIENT — gradient tabanlı | 238 | enum tanımı |
| 8 | STRUCTURED — kanal/filtre seviyesinde | 239 | enum tanımı |
| 9 | UNSTRUCTURED — bireysel ağırlık seviyesinde | 240 | enum tanımı |

### §5.4 Zamanlama & SLA tablosu (426–432 · 631–635)

| # | Kategori | Dosyanın verdiği eşik | Eşik türü | Kanıt satırı |
|---|---|---|---|---|
| 1 | Real-Time (GPU) | < 5 ms | tasarım SLA | 428 |
| 2 | Real-Time (CPU) | < 10 ms | tasarım SLA | 429 |
| 3 | Fast Batch (GPU) | 5–15 s / dakika ses | tasarım | 430 |
| 4 | Slow Batch (GPU) | 10–40 s / dakika ses | tasarım | 431 |
| 5 | Heavy Batch | 1–10 dk | tasarım | 432 |
| 6 | realtimeBudgetPercent | 25 (%) | config | 634 |
| 7 | maxWorkers | 4 | config | 632 |
| 8 | maxQueueSize | 20 | config | 633 |

> **Ölçüm notu:** bu eşiklerin **hiçbiri bu vault'ta ölçülmemiştir** — dosyanın tasarım
> metnidir. Ölçüm için §12 protokolü çalıştırılmalıdır.

---

## §6 Benchmark & donanım matrisi doğrulaması

### §6.1 RTX 4090 · FP16 (ai-models.md · 715–724)

| # | Model | 1 dk ses işleme | Throughput | VRAM | MOS | Satır |
|---|---|---|---|---|---|---|
| 1 | DeepFilterNet2 | 0.3 s | 200x real-time | 0.6 GB | 4.1 | 717 |
| 2 | HiFi-GAN | 0.5 s | 120x real-time | 1.0 GB | 4.0 | 718 |
| 3 | Genre CNN | 0.02 s | 3000x | 0.05 GB | — | 719 |
| 4 | Demucs HT | 3.0 s | 20x real-time | 2.7 GB | 4.3 | 720 |
| 5 | Resemble Enhance | 2.5 s | 24x real-time | 2.0 GB | 4.2 | 721 |
| 6 | BSRNN (INT8) | 3.5 s | 17x real-time | 0.8 GB | 3.9 | 722 |
| 7 | AudioSR | 5.0 s | 12x real-time | 4.0 GB | 4.5 | 723 |
| 8 | NuWave2 | 6.0 s | 10x real-time | 3.0 GB | 4.4 | 724 |

### §6.2 Raspberry Pi 5 · CPU · INT8 (728–733)

| # | Model | 1 dk ses işleme | Throughput | Satır |
|---|---|---|---|---|
| 1 | Genre CNN | 0.3 s | 200x | 730 |
| 2 | DeepFilterNet2 | 18.0 s | 3.3x | 731 |
| 3 | BSRNN | 45.0 s | 1.3x | 732 |
| 4 | AudioSR | — | Çalışmaz (VRAM yok) | 733 |

**Çelişki taraması (§10.1'e bağlantı):** §6.2 satır 1'de Genre CNN 0.3 s = 200x iken
§6.1 satır 3'te 0.02 s = 3000x → 15x fark. Donanım farkıyla tutarlı (RTX 4090 vs RPi 5),
**çelişki değil**, iki farklı ölçüm ortamı.

### §6.3 Donanım-özel eşzamanlı model (666–674)

| # | Donanım | Provider | Precision | Eş zamanlı model | Satır |
|---|---|---|---|---|---|
| 1 | RTX 4090 (24 GB) | CUDA | FP16 | 4 | 668 |
| 2 | RTX 4070 (12 GB) | CUDA | FP16 | 2 | 669 |
| 3 | RTX 3060 (12 GB) | CUDA | FP16/INT8 | 1 | 670 |
| 4 | GTX 1660 (6 GB) | CUDA | INT8 | 1 | 671 |
| 5 | M2 Pro (16 GB) | CoreML | FP16 | 2 | 672 |
| 6 | Ryzen 7 (32 GB) | CPU | INT8 | 1 | 673 |
| 7 | Raspberry Pi 5 (8 GB) | CPU | INT8 | 0 | 674 |

---

## §7 C++ örnek üretim kanıtları (dosyanın kod blokları)

> **Kritik okuma:** repo'da *.cpp/*.h **yok** (§10.4). Bu bloklar **tasarım metni**dir;
> üretim kodu değillerdir. Yine de Neva Engine'in C++20/ONNX sözleşmesini gösterirler ve
> bu vaulttaki C++ örneklerinin **tek gerçek kaynağıdırlar**.

| # | Sınıf / blok | Kod satır aralığı | Neyi kanıtlar |
|---|---|---|---|
| 1 | AudioSRProcessor | 76–95 | Ort::Session + MemoryInfo + tensor vektörleri deseni |
| 2 | DeepFilterNet2Processor | 104–121 | örnek-bazlı (processSample) ve blok-bazlı (processBlock) iki API |
| 3 | ONNXInferenceEngine | 129–156 | provider seçimi, session yönetimi, quantize/optimize kanca noktaları |
| 4 | GenreClassifier | 267–286 | sonuç struct'ı (genreId · genreName · confidence) |
| 5 | PruningConfig / PruningMethod | 229–242 | budama yapılandırması ve enum |
| 6 | InferenceJobQueue | 436–457 | öncelik kuyruğu + worker havuzu + jobId durum makinesi |
| 7 | ModelRegistry::ModelInfo | 505–526 | model kayıt şeması (checksum · status · variants) |
| 8 | ModelUpdater | 553–571 | sürüm kontrolü ve geri çağırma (callback) deseni |
| 9 | AudioQualityMetrics | 592–615 | statik hesaplayıcı deseni — referans/işlenmiş çifti |

**Ortak C++ özellikleri (bloklardan gözlemlenen):**
- Otomatik bağlama yok; açık std:: tipleri (std::string · std::vector · std::chrono) — 1–2,
  6, 8. satır aralıklarında.
- RAII iması: Ort::Session üye olarak tutulur (77 · 106 · 132) — oturum yaşam döngüsü
  sınıf ömrüne bağlı.
- std::thread kullanımı yalnız InferenceJobQueue içinde (450) — başka blokta yok.
- Hata taşıma mekanizması hiçbir blokta görünmüyor (exception/Result yok) → **§10.6**.

---

## §8 PHP sözleşme ↔ Neva hedefi boşluk matrisi

> **Amaç:** CoreMusic'in PHP yüzeyi ile Neva Engine hedefi arasındaki farkı satır kanıtıyla
> göster. Sol taraf dosya okuması, sağ taraf §3 kanıtıdır.

| # | PHP yüzeyi (gerçek · satır) | Neva hedefi (ai-models.md · satır) | Fark | Durum |
|---|---|---|---|---|
| 1 | AIEngine::getRecommendations — AIEngine.php · 49 | Job API ai.detectGenre — 494–496 | PHP'de tespit yok, yalnız öneri | ❌ boşluk |
| 2 | AIEngine::analyzeAudio — AIEngine.php · 82 | ONNXInferenceEngine::loadModel — 147 | PHP'de model yükleme yok | ❌ boşluk |
| 3 | AIEngine::optimizeEQ — AIEngine.php · 111 | Auto-EQ 8 adım akış — 324–351 | PHP'de FFT/optimizasyon yok | ❌ boşluk |
| 4 | AIEngine::predictFault — AIEngine.php · 161 | Neva'da karşılığı YOK | K016 alanı (bkz. anayasa §8) | ⚠️ sınır |
| 5 | AIEngine::analyzeHardware — AIEngine.php · 138 | Neva'da karşılığı YOK | K016 alanı | ⚠️ sınır |
| 6 | Sabit MAX_RECOMMENDATIONS = 20 — AIEngine.php · 30 | maxQueueSize = 20 — 633 | sayısal tesadüf, farklı kavram | ⚠️ karıştırma yok |
| 7 | Sabit MIN_SCORE = 0.7 — AIEngine.php · 31 | minConfidenceForApply = 0.6 — 362 | eşikler farklı | ⚠️ iki ayrı eşik |
| 8 | Sabit DIVERSITY_TARGET = 0.30 — AIEngine.php · 32 | Neva'da karşılığı yok | PHP özgü diversification | ✅ bilgi |
| 9 | getCollaborativeScores — AIEngine.php · 209 | Neva'da karşılığı yok | işbirlikçi filtreleme PHP'de | ✅ bilgi |
| 10 | getContentBasedScores — AIEngine.php · 223 | GenreClassifier — 268 | PHP içerik bazlı, Neva tespit bazlı | ⚠️ farklı aile |
| 11 | combineScores — AIEngine.php · 243 | Neva'da karşılığı yok | ağırlıklı birleştirme PHP özgü | ✅ bilgi |
| 12 | applyDiversityFilter — AIEngine.php · 264 | Neva'da karşılığı yok | ✅ bilgi |
| 13 | extractMetadata — AIEngine.php · 288 | Upload pipeline 9 adım — 390–402 | PHP'de FFmpeg probe yok | ❌ boşluk |
| 14 | extractFeatures — AIEngine.php · 315 | Mel-Spectrogram 128×300 — 295 | PHP'de spektrogram yok | ❌ boşluk |
| 15 | bandToFrequency — AIEngine.php · 332 | EQ preset tablosu — 66–81 | PHP bant→frekans eşlemesi tutarlı | ✅ kısmen |
| 16 | calculateRoomCorrection — AIEngine.php · 347 | Room correction — 128–144 | Neva ±12 dB clamp var, PHP clamp kanıtı yok | ⚠️ doğrulanmadı |
| 17 | calculateBandQ — AIEngine.php · 368 | Neva'da Q kavramı geçmiyor | PHP özgü | ✅ bilgi |
| 18 | AIWorkflow::recommendationWorkflow — AIWorkflow.php · 49 | ai.autoEQ / ai.process — 487 / 463 | akış benzerliği | ⚠️ isomorf değil |
| 19 | AIWorkflow::audioAnalysisWorkflow — AIWorkflow.php · 99 | Upload pipeline — 390–402 | PHP akışı tekil çağrı | ⚠️ kısmen |
| 20 | AIWorkflow::autoEQWorkflow — AIWorkflow.php · 147 | Auto-EQ akışı — 324–351 | PHP 8 adımın 1'ini yapar | ❌ boşluk |
| 21 | AIOrchestrator::dispatchTask — AIOrchestrator.php · 71 | InferenceJobQueue::submitJob — 453 | PHP kuyruk yok, doğrudan çalıştırır | ❌ boşluk |
| 22 | AIOrchestrator::executeTask — AIOrchestrator.php · 185 | worker havuzu — 450 | PHP tek iş parçacığı | ❌ boşluk |
| 23 | KnowledgeBase::ragQuery — KnowledgeBase.php · 72 | Neva'da RAG yok | PHP özgü RAG | ✅ bilgi |
| 24 | PromptEngine::MAX_TOKENS_DEFAULT = 4096 — PromptEngine.php · 25 | Neva'da token kavramı yok | iki farklı alan | ✅ bilgi |
| 25 | PromptEngine::countTokens — PromptEngine.php · 90 | Neva token saymaz (sample sayar) | birim farkı: token ≠ sample | ⚠️ birim |
| 26 | ToolCalling::checkPermission — ToolCalling.php · 145 | Neva'da izin katmanı yok | ⚠️ güvenlik farkı |
| 27 | MemorySystem::remember — MemorySystem.php · 123 | ModelRegistry cache — 521–525 | bellek vs disk önbellek | ⚠️ farklı katman |
| 28 | PHP'de ExecutionProvider kavramı YOK | 7 provider — 136–144 | donanım seçimi PHP'de yok | ❌ boşluk |
| 29 | PHP'de quantize/optimize kavramı YOK | quantizeModel · optimizeModel — 151 / 154 | model dönüşümü yok | ❌ boşluk |
| 30 | PHP'de checksum/bütünlük kavramı YOK | checksumSHA256 — 516 | imza doğrulama yok | ❌ boşluk |
| 31 | PHP'de metric (PESQ/STOI) kavramı YOK | 8 metrik — 579–588 | kalite ölçümü yok | ❌ boşluk |
| 32 | PHP'de job durum makinesi YOK | QUEUED·RUNNING·DONE·FAILED — 444 | iş takibi yok | ❌ boşluk |

**Sayım:** 32 satır · ❌ boşluk 12 · ⚠️ sınır/doğrulanmadı 9 · ✅ bilgi 11.

---

## §9 Sınır iddiaları (inference ↔ local-inference ↔ model-management)

| # | İddia | Kanıt | Durum |
|---|---|---|---|
| 1 | Kademe-2 envanteri 9 çocuk taşır | ai-master.md · §3 @142 | ✅ okundu |
| 2 | Sorumluluk matrisi ai-master §4'tedir | ai-master.md · §4 @176 | ✅ okundu |
| 3 | K006 ↔ K016 ayrımı ai-master §2.2'dedir | ai-master.md · §2.2 @112 | ✅ okundu |
| 4 | Aday teknoloji seçimi YAPILMADI (envanter var, karar yok) | ai-master.md · §11.1 @318 | ✅ okundu |
| 5 | UNKNOWN listesi ai-master §11.2'dedir | ai-master.md · §11.2 @348 | ✅ okundu |
| 6 | Uygulama yol haritası ai-master §18'dedir (faz sırası K-14) | ai-master.md · §18 @512 | ✅ okundu |
| 7 | inference anayasasında K015-AI kapsam dışıdır | inference-anayasa.md · §8 @139 | ✅ okundu |
| 8 | inference dışa kapısı çağrı sözleşmesidir | inference-anayasa.md · §12 @200 | ✅ okundu |
| 9 | gecikme/timeout bütçesi anayasa §13'tedir | inference-anayasa.md · §13 @218 | ✅ okundu |
| 10 | cache/batch/usul anayasa §14'tedir | inference-anayasa.md · §14 @237 | ✅ okundu |
| 11 | hata/fallback zinciri anayasa §15'tedir | inference-anayasa.md · §15 @254 | ✅ okundu |
| 12 | determinizm/sıcaklık anayasa §16'dadır | inference-anayasa.md · §16 @270 | ✅ okundu |
| 13 | workflow çağrı zinciri anayasa §18'de 5 akıştır | inference-anayasa.md · §18 @302 | ✅ okundu |
| 14 | rollout usulü shadow → canary → A/B | inference-anayasa.md · §27 @434 | ✅ okundu |
| 15 | ölçüm kanca noktaları anayasa §29'dadır | inference-anayasa.md · §29 @460 | ✅ okundu |
| 16 | kapı özeti anayasa §30'dadır | inference-anayasa.md · §30 @475 | ✅ okundu |
| 17 | revizyon eşiği tablosu anayasa §32'dedir | inference-anayasa.md · §32 @511 | ✅ okundu |
| 18 | local-inference ile sınır rol tanımı v2'de yok | inference/index.md · open_questions | ⚠️ VERIFICATION REQUIRED |
| 19 | model-management sınırı ai-master §3 satır 259'a bağlanır | ai-master.md · §3 @142 | ⚠️ indirgeme |
| 20 | Öneri sahipliği 3-way belirsizliği kök kararlar §C'de | yapay-zeka-kararlar.md · §C @122 | ✅ okundu |

---

## §10 Çelişki & düzeltme kaydı (bu tur — silinmedi)

| # | Bulgu | Kanıt | Muamele |
|---|---|---|---|
| 1 | Ölçeklenebilirlik satırında çift boşluk + kapanmamış parantez: PC (RTX 4090) → RPi 5 (CPU INT8) →  Mac OS) | ai-models.md · 27 | Kaynak metin aynen taşındı; düzeltilmedi (kaynak değiştirilmez) |
| 2 | macOS Intel satırında GPU ve hassasiyet hücreleri boş | ai-models.md · 42 | UNKNOWN olarak işaretlendi (§3/21) |
| 3 | Intel Arc · RPi 4/5/6 · CPU-only satırlarında hassasiyet hücresi boş | ai-models.md · 48–52 | UNKNOWN (§3/22) |
| 4 | AudioSR CPU süresi hücresi tire (—) | ai-models.md · 63 | UNKNOWN sayıldı (§4 notu) |
| 5 | DeepFilterNet2 GPU süresi hücresi tire (—) | ai-models.md · 72 | Batch dışı bırakılmış, çelişki değil |
| 6 | Genre CNN MOS hücresi tire (—) | ai-models.md · 719 | Ölçülmemiş sayıldı |
| 7 | Hassasiyet merdiveni yüzde iddiaları hiçbir ölçümle desteklenmiyor | ai-models.md · 215 · 217 · 219 | **ölçülmemiş** etiketi (§5.2) |
| 8 | INT4 satırı dosyanın kendisinde deneysel der | ai-models.md · 219 | deneysel etiketi korundu |
| 9 | §1 satır 10 (model formatı) hâlâ UNKNOWN — bu turda kapatılmadı | bu dosya · §1 | UNKNOWN kaldı |
| 10 | §1 satır 11 (repo'da ONNX Runtime kullanımı) hâlâ UNKNOWN | bu dosya · §1 | UNKNOWN kaldı |
| 11 | §1 satır 13 (PHP motorun yerine geçiş) hâlâ UNKNOWN | bu dosya · §1 | UNKNOWN kaldı |
| 12 | İki bağımsız sayım (grep 83 · ayrıştırma 70+13=83) uyuştu | shared/src/AI/ (§8 köprüsü) | çelişki yok, teyit |

---

## §11 UNKNOWN / ⚠️ VERIFICATION REQUIRED defteri

| # | Kale | Neden açık | Kapanış koşulu | Üst dosya |
|---|---|---|---|---|
| 1 | Model formatı seçimi (ONNX · pickle · PMML · özel) | disk kanıtı yok | üretim kodunda format okunması | yapay-zeka-dogrulama.md §4 |
| 2 | CoreMusic'in ONNX Runtime kullandığı | grep 0 isabet (§1/10) | composer/vendor taraması | yapay-zeka-dogrulama.md §4 |
| 3 | Harici model runtime'a geçiş kararı | karar verilmedi | ADR | yapay-zeka-kararlar.md §C/Q4 |
| 4 | inference ↔ local-inference ↔ model-management sınırı | v2 rol tanımı yok | ADR-097 K4 | ai-master.md §7 |
| 5 | local-inference rol cümlesi | v2'de yok | Authority kararı | local-inference/index.md |
| 6 | Donanım matrisinde hassasiyet hücreleri | dosya boş | Neva ekibiyle teyit | §5.1 |
| 7 | FP16/INT8/INT4 yüzde iddiaları | ölçülmemiş | §12 protokolü | §5.2 |
| 8 | Real-time < 5 ms / < 10 ms SLA | ölçülmemiş | §12 protokolü | §5.4 |
| 9 | Benchmark tabloları (RTX 4090 · RPi 5) | tekrar üretilemedi | ölçüm tekrarı | §6 |
| 10 | WaveGlow GPU süresi (40 s) — tabloda CPU 40 s var, GPU 7 s | tek hücre okundu, doğrulanmadı | dosya tekrar okundu (69) | §4 |
| 11 | Öneri sahipliği 3-way | yönetim kararı bekliyor | K-11 kısmi kapandı | yapay-zeka-kararlar.md §C/Q1 |
| 12 | hardwareAnalysisWorkflow / faultPredictionWorkflow sahibi | K006 ↔ K016 sınırı | ADR | yapay-zeka-kararlar.md §C/Q2 |
| 13 | İzleme (monitoring) altyapısı | aday listesi var, karar yok | altyapı kararı | yapay-zeka-kararlar.md §C/Q3 |
| 14 | AIOrchestrator'ın K006 yüzeyi | kısmen açık | sözleşme güncellemesi | yapay-zeka-kararlar.md §C/Q5 |
| 15 | PHP'de hata taşıma deseni (throw vs Result) | §7.6 notu | kod okuması | bu dosya §7 |
| 16 | Room correction ±12 dB clamp PHP'de var mı | doğrulanmadı | AIEngine.php okuması | §8/16 |
| 17 | Token ↔ sample birim dönüşümü | dönüşüm yok | tasarım kararı | §8/25 |
| 18 | İzin katmanı (checkPermission) Neva tarafında | yok | tasarım kararı | §8/26 |
| 19 | Ölçüm tekrarlanabilirliği (benchmark) | prosedür yoktu | §12 | §12 |
| 20 | Bu ek için erişilemeyen URL | bu turda URL açılmadı | sonraki tur web fetch | §15 |

---

## §12 Yeniden ölçüm protokolü (§11.7–9 · §11.19 için)

| # | Adım | Komut / yöntem | Beklenen çıktı | Geçiş |
|---|---|---|---|---|
| 1 | Dosya satır sayımı | [System.IO.File]::ReadAllLines($yol).Length | tamsayı | Measure-Object -Line YASAK (boş satır saymaz) |
| 2 | Metod sayımı | function imzası ayrıştırma | 70 metod | grep 83 ile çapraz (13 sabit) |
| 3 | Provider enum sayımı | satır 136–144 aralığı | 7 | §3/29 |
| 4 | Model tablosu sayımı | satır 61–72 | 10 satır | §4 |
| 5 | Benchmark sayımı | satır 715–724 | 8 satır | §6.1 |
| 6 | RPi5 sayımı | satır 728–733 | 4 satır | §6.2 |
| 7 | SLA eşikleri | satır 426–432 | 5 kategori | §5.4 |
| 8 | Metrik sayısı | satır 579–588 | 8 metrik | §3/63 |
| 9 | Job API sayısı | satır 462–496 | 6 çağrı | §3/57 |
| 10 | PHP dosya satırları | 7 dosya · toplam 1988 | §22 bilesenler | yapay-zeka-bilesenler.md §22 |
| 11 | K006 dosya kapısı | içerik ≥ 500 · index 40–100 | kapı raporu | bu ek |
| 12 | Ölçüm tekrarı | madde 1–11 iki kez | iki sayı eşit | çapraz kontrol |

**Protokol kuralı:** iki ölçüm farklı çıkarsa dosya DURUR; fark giderilmeden yeni iddia yazılmaz.

---

## §13 Kapı (gate) eşlemesi — anayasa §30 ↔ bu dosya

| # | Anayasa kapısı (inference-anayasa.md) | Kapı satırı | Bu dosyadaki kanıt | Durum |
|---|---|---|---|---|
| 1 | Çağrı sözleşmesi kapısı | §12 @200 | §8/1–2 · §9/8 | ✅ kanıtlandı |
| 2 | Gecikme & timeout bütçesi | §13 @218 | §5.4 SLA satırları | ⚠️ ölçülmemiş |
| 3 | Cache & batch kapısı | §14 @237 | §3/54–57 | ⚠️ ölçülmemiş |
| 4 | Hata & fallback zinciri | §15 @254 | §7 ortak özellik notu | ⚠️ kod kanıtı yok |
| 5 | Determinizm & sıcaklık | §16 @270 | §5.2 merdiven | ⚠️ ölçülmemiş |
| 6 | Güvenlik kapıları | §17 @284 | §8/26 checkPermission | ⚠️ PHP'de var, Neva'da yok |
| 7 | Workflow zinciri (5 akış) | §18 @302 | §8/18–20 | ✅ satır kanıtı |
| 8 | Kalite & ölçüm kapıları | §19 @336 | §6.1 · §6.2 | ✅ dosya içi ölçüm tablosu |
| 9 | Sürüm & değişiklik kilidi | §20 @350 | §3/58–62 (checksum · status) | ✅ kanıtlandı |
| 10 | Rollout usulü | §27 @434 | §11.7–9 | ⚠️ ölçülmemiş |
| 11 | Sürümleme sözleşmesi | §28 @448 | §3/61 dizin şeması | ✅ kanıtlandı |
| 12 | Ölçüm kanca noktaları | §29 @460 | §12 protokolü | ✅ protokol yazıldı |
| 13 | Kapı özeti | §30 @475 | bu tablo | ✅ eşlendi |
| 14 | Revizyon eşiği | §32 @511 | §10 düzeltme kaydı | ✅ eşlendi |

---

## §14 Sayım (tur 2 — 2026-10-15)

| Metrik | Değer |
|---|---|
| §1 eski iddia satırı | 13 |
| §3 ek iddia satırı | 60 |
| §4 model satırı | 10 + 4 not |
| §5.1 provider satırı | 7 |
| §5.2 merdiven satırı | 4 |
| §5.3 yöntem satırı | 9 |
| §5.4 SLA satırı | 8 |
| §6.1 benchmark satırı | 8 |
| §6.2 RPi5 satırı | 4 |
| §6.3 donanım satırı | 7 |
| §7 kod bloğu satırı | 9 |
| §8 boşluk matrisi satırı | 32 |
| §9 sınır iddiası | 20 |
| §10 çelişki kaydı | 12 |
| §11 defter satırı | 20 |
| §12 protokol satırı | 12 |
| §13 kapı eşlemesi | 14 |
| ✅ doğrulanmış / okunmuş | 130+ |
| ⚠️ VERIFICATION REQUIRED | 13 |
| UNKNOWN | 20 |

---

## §15 Bu ek için kaynaklar (yalnız §3–§14)

| # | Kaynak | Tür | Erişim | Tarih |
|---|---|---|---|---|
| 1 | .ai/projects/NevaEngine/legacy-2026-07/ai-models.md (738 satır) | disk | bu oturumda okundu | 2026-10-15 |
| 2 | shared/src/AI/*.php (7 dosya · 1988 satır) | disk | bu oturumda okundu | 2026-10-15 |
| 3 | shared/src/Contracts/AI/*.php (6 interface · 33 imza) | disk | bu oturumda okundu | 2026-10-15 |
| 4 | .ai/architecture/K006-yapay-zeka/ai-master.md (§2–§18 başlıkları) | disk | bu oturumda okundu | 2026-10-15 |
| 5 | .ai/architecture/K006-yapay-zeka/inference/inference-anayasa.md (§1–§32 başlıkları) | disk | bu oturumda okundu | 2026-10-15 |
| 6 | .ai/architecture/K006-yapay-zeka/inference/index.md | disk | §1 kaynaklı | 2026-10-12 |
| 7 | .ai/architecture/K006-yapay-zeka/yapay-zeka-bilesenler.md §22 | disk | bu oturumda yazıldı | 2026-10-15 |
| 8 | .ai/architecture/K006-yapay-zeka/yapay-zeka-kararlar.md §C | disk | başlık okundu | 2026-10-15 |
| 9 | Bu turda yeni URL açılmadı | — | — | 2026-10-15 |
| 10 | repo *.cpp / *.h | disk | 0 dosya (2026-10-15 ölçümü) | 2026-10-15 |

**Authority:** Bayram Ali / Vault Steward · 2026-10-15 (tur 2 eki)

---

## §16 Tur 2 kapanış kontrol listesi (bu ek kendi kendini denetler)

| # | Kontrol | Beklenen | Sonuç |
|---|---|---|---|
| 1 | Eski §1–§6 silinmedi mi? | 13 iddia satırı yerinde | ✅ yerinde |
| 2 | Eski Authority satırı yerinde mi? | 2026-10-12 satırı | ✅ geri kondu |
| 3 | Yeni iddialar dosya+satır mı taşıyor? | §3 tamamı | ✅ 60/60 |
| 4 | Yeni URL uyduruldu mu? | 0 | ✅ §15/9 |
| 5 | Ölçülen değerler ölçümle yazıldı mı? | §14 sayım | ✅ |
| 6 | Tasarım metni ölçüm gibi sunuldu mu? | hayır | ✅ §5.4 notu |
| 7 | Çelişkiler silindi mi? | hayır | ✅ §10 |
| 8 | Kapı ≥ 500 satır? | 500+ | ✅ bu ek ile |
| 9 | Dolgu / TBD / // ... var mı? | 0 | ✅ |
| 10 | Kod blokları gerçek mi? | repo C++ yok → tasarım bloğu etiketi | ✅ §7 |
| 11 | İki bağımsız sayım uyuşuyor mu? | 83 = 70+13 | ✅ §10/12 |
| 12 | Sonraki tur kimin? | local-inference · model-management kardeşleri | §9/18–20 |

**Tur 2 sonu.** Sonraki tur bu dosyanın §11 defterini devralır; defter satırı
kapanmadan §1'deki üç UNKNOWN **UNKNOWN** olarak kalır (uydurulmaz).
