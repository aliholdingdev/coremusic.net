---
title: "Testing — Örnekler"
type: architecture-scope
node_id: K006-yapay-zeka/testing
node_type: subdomain-examples
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# Testing — Örnekler

Amaç: kapsamı üç örnek somutlaştırmak — gerçek repo kodu (öncelik 1), resmi doküman örneği, uygulama notu.

## §1. Gerçek repo kodu (öncelik 1)

**`shared/src/` içinde test edilecek AI kodu mevcut, `shared/tests/` içinde AI testi YOK:**

```php
// shared/src/AI/AIEngine.php (satır 97–115) — test edilecek yüzey
public function planWorkflow(string $input, array $context = []): array
{
    // ... workflow planı üretir (deterministik mock)
    return ['status' => 'ok', 'workflow' => $workflow];
}
```

**Yorum (kanıt temelli):** `planWorkflow` gibi metotlar unit test için doğruday adaydır; ancak `shared/tests/` içinde `AIEngine|AIWorkflow|AIOrchestrator` grep'i **0 isabet** verir — AI testi henüz yazılmamıştır (kapsam maddesi 1–2'nin repo karşılığı yok; negatif kanıt).

## §2. Resmi doküman örneği

scikit-learn — cross-validation (resmi doküman, 2026-10-12 doğrulandı):

> "Learning the parameters of a prediction function and testing it on the same data is a methodological mistake: a model that would just repeat the labels of the samples that it has just seen would have a perfect score but would fail to predict anything useful on yet-unseen data."
> — https://scikit-learn.org/stable/modules/cross_validation.html

### §2.1 Python — pytest (model testi altyapısı)

> **Kaynak:** https://docs.pytest.org/en/stable/getting-started.html · **HTTP 200 · 2026-10-14** · `[ORİJİNAL ÖRNEK · derlenmedi]`

Resmi doküman örneği (birebir alıntı — `test_sample.py`):

```python
# content of test_sample.py
def func(x):
    return x + 1


def test_answer():
    assert func(3) == 5
```

Doküman alıntısı: *"You can use the `assert` statement to verify test expectations."* — floating-point metrik karşılaştırması (kayıp/accuracy eşikleri) için doküman `pytest.approx()` örneğini verir: `assert (0.1 + 0.2) == pytest.approx(0.3)`. K006 bağlamı: model testi katmanı (offline metrik doğrulama) pytest ile yazılır; eşik karşılaştırmaları `approx` ile toleranslı yapılır.

### §2.2 C# — ONNX Runtime (model doğrulama)

> **Kaynak:** https://onnxruntime.ai/docs/get-started/with-csharp.html · **HTTP 200 · 2026-10-14** · `[ORİJİNAL ÖRNEK · derlenmedi]`

Kurulum (doküman birebir):

```text
dotnet add package Microsoft.ML.OnnxRuntime --version 1.16.0
dotnet add package System.Numerics.Tensors --version 0.1.0
```

```csharp
using Microsoft.ML.OnnxRuntime;
using System.Numerics.Tensors;
```

```csharp
// Get path to model to create inference session.
const string modelPath = "./model.onnx";

// Create an InferenceSession from the Model Path.
// Creating and loading sessions are expensive per request.
// They better be cached
using var session = new InferenceSession(modelPath);
```

Doküman alıntısı: *"Creating and loading sessions are expensive per request. They better be cached"* — model doğrulama testinde session'ın fixture/singleton olarak kurulması bu nedenle zorunludur. **Not (dürüstlük):** xUnit sarmalayıcı bu resmi belgede **YOK**; blok yalnız doğrulanmış ONNX Runtime C# införens yüzeyini içerir (xUnit iskeleti uydurulmamıştır).

### §2.3 Java — DL4J (model değerlendirme testi)

> **Kaynak:** https://deeplearning4j.konduit.ai/deeplearning4j/quickstart.md · **HTTP 200 · 2026-10-14** · `[ORİJİNAL ÖRNEK · derlenmedi]`

Resmi quickstart (DL4J 1.0.0-M2.1) — epoch sonu değerlendirme bloğu (birebir alıntı):

```java
            // Evaluate on the test set at the end of each epoch
            Evaluation eval = model.evaluate(testIter);
            testIter.reset();

            log.info("--- Epoch {} ---", epoch);
            log.info("Accuracy:  {}", eval.accuracy());
            log.info("Precision: {}", eval.precision());
            log.info("Recall:    {}", eval.recall());
            log.info("F1 Score:  {}", eval.f1());
```

Doküman alıntısı: *"A well-configured MLP on MNIST reaches 97-98% accuracy within five epochs on the CPU."* — değerlendirme metrikleri (accuracy/precision/recall/F1) K006 T3 offline metrik setiyle (Precision@k · Recall@k · nDCG@k · MAP — `ai-master.md` §10) aynı ailededir. **Not (dürüstlük):** JUnit sarmalayıcı bu belgede **YOK**; blok `Evaluation` API'siyle sınırlıdır.

### §2.4 JavaScript/Node.js — onnxruntime-node (smoke test zemini)

> **Kaynak:** https://onnxruntime.ai/docs/get-started/with-javascript/node.html · **HTTP 200 · 2026-10-14** · `[ORİJİNAL ÖRNEK · derlenmedi]`

Kurulum + import (doküman birebir):

```bash
# install latest release version
npm install onnxruntime-node
```

```javascript
// use ES6 style import syntax (recommended)
import * as ort from 'onnxruntime-node';
```

Doküman tablosu: CPU backend Windows x64/arm64 · Linux x64/arm64 · macOS x64/arm64 ✔️ (Windows'ta DirectML ✔️). **Not (dürüstlük):** bu resmi sayfa yalnız kurulum + import + platform tablosunu içerir; tam införens (`InferenceSession`/`run`) örneği bu sayfada **YOK** — införens kodu uydurulmamış, blok kurulum+import smoke doğrulamasıyla sınırlıdır.

### §2.5 Go — gonum/stat (istatistik doğrulama testi)

> **Kaynak:** https://pkg.go.dev/gonum.org/v1/gonum/stat · **HTTP 200 · 2026-10-14** · `[ORİJİNAL ÖRNEK · derlenmedi]`

Doküman imzaları (v0.17.0 · BSD-3-Clause · pkg.go.dev kaydı): `func Mean(x, weights []float64) float64` · `func Variance(x, weights []float64) float64` — *"Variance computes the unbiased weighted sample variance"*. Test, bu imzalardan ve dokümanentaki formülden türetildi:

```go
import (
	"math"
	"testing"

	"gonum.org/v1/gonum/stat"
)

func TestVariance(t *testing.T) {
	x := []float64{2, 4, 4, 4, 5, 5, 7, 9}
	got := stat.Variance(x, nil) // weights nil => tüm ağırlıklar 1 (doküman)
	want := 32.0 / 7.0           // (9+1+1+1+0+0+4+16) / (8-1)
	if math.Abs(got-want) > 1e-9 {
		t.Fatalf("Variance = %v, want %v", got, want)
	}
}
```

K006 bağlamı: öneri/metrik dağılım doğrulaması (rokun/nDCG sapması) Go tarafında `gonum/stat` ile deterministik test edilebilir.

### §2.6 Bash (CI) — GitHub Actions test adımı

> **Kaynak:** https://docs.github.com/en/actions/writing-workflows/quickstart · **HTTP 200 · 2026-10-14** · `[ORİJİNAL ÖRNEK · derlenmedi]`

Doküman alıntısı: *"GitHub Actions is a continuous integration and continuous delivery (CI/CD) platform that allows you to automate your build, test, and deployment pipeline. You can create workflows that run tests whenever you push a change to your repository."* — quickstart yaml'ının iskelet alıntısı (korunan adımlar birebir; yalnız echo satırları sadeleştirildi):

```yaml
name: GitHub Actions Demo
on: [push]
jobs:
  Explore-GitHub-Actions:
    runs-on: ubuntu-latest
    steps:
      - name: Check out repository code
        uses: actions/checkout@v6
      - name: List files in the repository
        run: |
          ls ${{ github.workspace }}
```

Test komutları (ör. `pytest`, `dotnet test`, `mvn test`, `go test`) `run:` adımlarına yazılır; repo'daki mevcut CI yüzeyi `ai-master.md` §15 V1–V2 satırlarıdır (`ci.yml` · `secret-scan.yml` — ⚠️ ölçüm bayat, yeniden sayım gerekli).

## §3. Uygulama notu

İlk test dalgası: `AIEngine::planWorkflow` + `AIWorkflow` deterministik akış (unit), ardından orchestrator→engine integration; AI test kütüphanesi kararı (UNKNOWN) öncelenmeli.

## §4. İlişkili dosyalar

- [[testing-kapsam]]
- [[testing-dogrulama]]
- [[../index|K006-yapay-zeka index]]

---

## §5 Kapanış Derinliği — Neva Engine AI test C++ örnek turu (2026-10-15)

### §5.0 Kanıt disiplini

Repo ölçümü (2026-10-15): `*.cpp` / `*.h` = **0 dosya** → aşağıdaki C++ blokları **vault spesifikasyon alıntılarıdır**, derlenmiş kod değildir; her biri `Dosya: … [Satır: n]` ile işaretlenmiştir.

### §5.1 Örnek TS-1 — Kalite metrik testi (AudioQualityMetrics gate, tam okuma)

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

**Test notları:**

1. `compute` static → saf fonksiyon, unit test için ideal; aynı girdi çifti her çalıştırışta aynı çıktıyı üretir (determinizm).
2. Gate eşikleri (Dosya: ai-models.md [Satır: 579–588]): PESQ > 3.5 · STOI > 0.85 · SI-SDR > 15 dB · SNR > 30 dB · PSNR > 40 dB · LSD < 2.0 dB · MCD < 3.0 dB.
3. `compositeMos` ağırlıkları spec'te **yok** → bu alan unit test'te **assert edilemez** (uydurma ağırlık yok); gate yalnız 7 objektif metriğe dayanır.

### §5.2 Örnek TS-2 — Regresyon testi akışı (CI, tam blok)

```text
Girdi: referans ses dosyası (1 dk) + işlenmiş çıktı
1) AudioQualityMetrics::compute(reference, processed, N, sr)
2) MetricResults → gate (§5.1 eşikleri)
3) pass → CI yeşil · fail → CI kırmızı + failingMetric raporlanır
4) Sonuçları MLflow'a logla (§2 doğrulanmış CLI komutları)
```

Bu akış `ai-models.md` [Satır: 579–615] + bu dosya [Satır: 117–125] (MLflow CLI) + `../yapay-zeka-test-stratejisi.md` §5 gate tanımı ile birleştirilmiştir; **bu turda çalıştırılmadı**.

### §5.3 Örnek TS-3 — Gerçek zamanlı gecikme bütçesi testi (gate)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 426–432].

| Kategori | Hedef | Test yöntemi |
|---|---|---|
| Real-Time (GPU) | < 5 ms | buffer başına süre ölçümü |
| Real-Time (CPU) | < 10 ms | buffer başına süre ölçümü |
| Batch (GPU) | 5–40s / dk ses | toplam süre ölçümü |

Ölçüm protokolü: 1000 buffer tekrarı → medyan + p99 (spec'te protokol yok → bu K006 **türetmesidir**, `[TÜRETME]` işaretli; gerçek ölçüm yapılmadı).

### §5.4 Örnek TS-4 — Job kuyruğu unit test senaryoları (spec'ten)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 437–456] — `InferenceJobQueue` alanları.

| # | Test | Beklenen | Kanıt |
|---|---|---|---|
| T-1 | submitJob → getJobStatus | QUEUED | [Satır: 453–454] |
| T-2 | cancelJob(QUEUED) → durum | FAILED/iptal (semantik UNKNOWN) | [Satır: 455] |
| T-3 | maxQueueSize doldu → submit | RED (queue kapasitesi 20) | [Satır: 633] |
| T-4 | 4 worker eşzamanlı | 4 iş paralel | [Satır: 450] · [Satır: 632] |
| T-5 | progress 0→1 monoton | monoton artan | [Satır: 445] |

T-2'de beklenen **semantik belirsizdir** → `⚠️ VERIFICATION REQUIRED` (iptal semantiği spec'te tanımsız).

### §5.5 Örnek TS-5 — EP (execution provider) düşüş testi (gate)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 136–144].

| # | Test | Beklenen | Kanıt |
|---|---|---|---|
| T-6 | CUDA yok → configure(CUDA) | başarısız → CPU'ya düş | [Satır: 137] |
| T-7 | DirectML yok → configure(DirectML) | başarısız → CPU'ya düş | [Satır: 139] |
| T-8 | CPU INT8 | her yerde çalışır (fallback) | [Satır: 137] |

Düşüş zinciri `local-inference-ornekler.md` §5.6'da tanımlanmıştır; bu turda test edilmedi (repo'da C++ kodu yok).

### §5.6 Örnek TS-6 — Ölçüm doğruluğu testi (bayrak / sanity)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 713–733] — benchmark tabloları.

Sanity gate'leri (ölçüm doğruluğu kontrolü, gerçek ölçüm değil):

| # | Sanity | Beklenen | Kanıt |
|---|---|---|---|
| S-1 | GPU throughput > CPU throughput (aynı model) | ✅ | [Satır: 717] vs [Satır: 731] |
| S-2 | INT8 VRAM < FP16 VRAM | ✅ | [Satır: 63] (AudioSR 2.1 < 4.0) |
| S-3 | Genre CNN en hızlı | ✅ | [Satır: 719] (0.02s) |
| S-4 | AudioSR RPi5'te çalışmaz | ✅ | [Satır: 733] |

S-1…S-4 bu turda **yalnızca tablo okumasıdır**, ölçüm yapılmadı.

### §5.7 Örnek TS-7 — Test gate özeti (K006 için)

| # | Gate | Eşik | Çalıştırıldı mı? |
|---|---|---|---|
| TS-G1 | Kalite metrikleri | PESQ>3.5 · STOI>0.85 · SI-SDR>15 · SNR>30 · PSNR>40 · LSD<2.0 · MCD<3.0 | ❌ hayır |
| TS-G2 | RT gecikme | < 5ms GPU · < 10ms CPU | ❌ hayır |
| TS-G3 | Batch throughput | 1 dk ses ≤ hedef süre | ❌ hayır |
| TS-G4 | Kuyruk | ≤ 20 · worker ≤ 4 · REALTIME bütçesi %25 | ❌ hayır |
| TS-G5 | EP düşüş | CPU'ya düşüş | ❌ hayır |
| TS-G6 | Ölçüm sanity | S-1…S-4 | ❌ hayır |

> Bu tur **test tasarımı** turudur; ölçüm **yapılmadı** → `../yapay-zeka-dogrulama.md` §7'ye taşındı.

### §5.8 §5 tur kapanış kaydı

- Eklenen: 1 tam okuma (TS-1) + 1 CI akışı + 1 RT tablosu + 5 test senaryosu + 3 EP düşüş senaryosu + 4 sanity + 6 gate.
- Uydurma identifier: 0 · ölçü uydurma: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 2 (cancel semantiği · RT ölçüm protokolü).

---

## §6 Kapanış Derinliği — Neva Engine test altyapısı tam okuma (2026-10-15 kapanış turu)

### §6.0 Kanıt disiplini (bu bölüm için)

- Tüm alıntılar `.ai/projects/NevaEngine/legacy-2026-07/rules/` altındaki kural
  dosyalarından **satır numarasıyla** alınmıştır.
- Repo'da `*.cpp` / `*.h` = **0 dosya** (2026-10-15 ölçümü) → aşağıdaki C++ blokları
  **vault kural dosyalarındaki örnek testlerdir**, bu repo'nun derlenen testleri değildir.
- Çalıştırılan test/benchmark: **0** → `../yapay-zeka-dogrulama.md` §7'ye taşındı.

### §6.1 Örnek TS-8 — Unit test kalıbı (tam alıntı)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/rules/cpp-global-CLAUDE.md` [Satır: 423–447]

```cpp
// tests/NevaEngine/EqualizerTest.cpp
#include <gtest/gtest.h>
#include "neva-engine/Equalizer.hpp"

class EqualizerTest : public ::testing::Test {
protected:
  void SetUp() override {
    eq = std::make_unique<NevaEngine::Equalizer>(48000, 512);
  }
  std::unique_ptr<NevaEngine::Equalizer> eq;
};

TEST_F(EqualizerTest, AppliesGain) {
  eq->setGain(1.0f, 6.0f);  // 1kHz band, +6 dB
  std::vector<float> input(512, 1.0f);
  std::vector<float> output(512);
  eq->process(input.data(), output.data(), 512);
  EXPECT_GT(output[0], 1.5f);  // Expect gain applied
}
```

**K006.9 çıkarımı:** fixture deseni (`SetUp` + 48000/512) AI blokları için de geçerlidir —
`RecommendationEngine` / `FeatureExtractor` testleri aynı `TEST_F` kalıbında kurulur;
eşik assertion'ı (`EXPECT_GT`) K006 gate'lerine (`TS-G1…G7`) birebir taşınır.

### §6.2 Örnek TS-9 — Integration test kalıbı (tam alıntı)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/rules/cpp-global-CLAUDE.md` [Satır: 449–468]

```cpp
// tests/integration/EnginePlayerIntegration.cpp
TEST(EnginePlayerIntegration, RingBufferSharedMemory) {
  // Start Engine (creates NevaAudioRingBuffer)
  auto engine = NevaEngine::start();
  ASSERT_TRUE(engine.ringBuffer != nullptr);

  // Start Player (attaches to ring buffer)
  auto player = NevaPlayer::start();
  EXPECT_EQ(player.ringBuffer.capacity(), engine.ringBuffer.capacity());

  // Write PCM → Read FFT
  std::vector<float> pcm(512);
  engine.ringBuffer.write(pcm.data(), 512);
  auto spectrum = player.getFFTSpectrum();
  EXPECT_EQ(spectrum.size(), 256);
}
```

**Çıkarım:** K006 için eşdeğer entegrasyon testi = "PHP `getRecommendations` → C++ motor
köprüsü → dönen liste boyutu/alanları" (sözleşme testi, kök `-test-stratejisi.md` §3.3).

### §6.3 Örnek TS-10 — Çalıştırma komutları (ctest + benchmark, tam alıntı)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/rules/neva-engine-CLAUDE.md` [Satır: 430–443 · 458–469]

```bash
cd build
ctest -R "NevaEngine::" --output-on-failure

# Per-component
ctest -R "Equalizer::"        # 31-band filtering
ctest -R "Compressor::"       # Dynamics processing
ctest -R "Spatializer::"      # Convolver & HRTF
ctest -R "VST3::"             # Plugin hosting
ctest -R "AIAutoEQ::"         # ONNX inference
ctest -R "FFTAnalyzer::"      # Spectrum analysis
```

```bash
# Audio processing speed
./tests/NevaEngine/EqualizerBench --benchmark_filter="31Band"
./tests/NevaEngine/CompressorBench

# Plugin load performance
./tests/NevaEngine/VST3LoadBench

# FFT spectrum generation
./tests/NevaEngine/FFTAnalyzerBench
```

Aynı dosyada benchmark filtreli çalıştırma `.ai/.../cpp-global-CLAUDE.md` [Satır: 472–480] içinde
de tekrarlanır (`EqualizerBench --benchmark_filter=".*31Band.*"` · `CodecBench` · `perf record`).
**K006 için hazır zemin:** `ctest -R "AIAutoEQ::"` satırı [Satır: 441] ONNX inference testinin
**zaten adlandırıldığını** kanıtlar — K006.4/K006.9 bu cihazı devralır.

### §6.4 Örnek TS-11 — Test gereksinimleri (bağlayıcı maddeler)

| Kaynak | Satır | Madde |
|---|---|---|
| `rules/cpp-global-AGENTS.md` | 359–364 | Unit: public API coverage **≥ 80%** · Integration: IPC + shared memory tüm platformlarda · Benchmark: latency/CPU hedefleri **merge öncesi** doğrulanır · Cross-platform: Windows + 1 diğer platform |
| `rules/neva-engine-AGENTS.md` | 324–329 | Tüm public DSP fonksiyonları için unit (hedef %80) · ASIO callback + ring buffer entegrasyonu · FFT/kompresör/plugin-load benchmark · **ses kalite testleri (THD · SNR ölçümleri)** |
| `01-gelistirme-plani.md` | 29 | Guardrail satırı: `Zero-allocation · lock-free · noexcept · 64-byte align · Google Test ≥80%` |
| `01-gelistirme-plani.md` | 114–118 | Faz 4 (sürekli): her DSP bloğu unit + **impulse/THD testi** · gerçek DAC loopback THD+N/SNR (K1 donanım + PCM3168A) · **golden reference WAV** regression (Airwindows karşılaştırmalı) |

### §6.5 Örnek TS-12 — Ölçüm kapıları (tam tablo okuması)

Dosya: `.ai/projects/NevaEngine/02-dsp-zincir-mimari.md` [Satır: 79–87]

| Metrik | Hedef | Yöntem |
|---|---|---|
| THD+N | < 0.005% @ 1 kHz | Loopback ölçüm (K1 DAC) + Google Test sine analiz |
| SNR | > 105 dB | -60 dBFS dither testi |
| True Peak overshoot | 0 | BS.1770-4 4x TP ölçüm (libebur128) |
| Gecikme | ASIO < 10 ms · WASAPI < 20 ms | Roundtrip loopback |
| Coverage | ≥ 80% min / ≥ 90% hedef | Google Test |

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/rules/neva-engine-CLAUDE.md` [Satır: 420–424]

| Performans | Hedef | Ölçüm |
|---|---|---|
| CPU (31-band EQ) | < %10 @ 44.1 kHz | `/health` → cpuUsage |
| Bellek izi | < 300 MB | `rss` |
| FFT hesabı (2048pt) | < 10 ms | Benchmark suite |
| Plugin yükleme | < 500 ms/plugin | PluginManager |
| WebSocket kare hızı | 30 FPS spektrum | `/fft` < 33 ms |

### §6.6 Örnek TS-13 — AI model testi için hazır zemin (K006.2/K006.4/K006.9)

| Zemin | Kaynak | Satır | K006 kullanımı |
|---|---|---|---|
| `ctest -R "AIAutoEQ::"` ONNX inference testi | rules/neva-engine-CLAUDE.md | 441 | K006.4 yerel çıkarım doğrulaması |
| Objektif kalite metrikleri bölümü | `legacy-2026-07/ai-models.md` | 577 (`### 9.1 Objektif Metrikler`) | K006.2 değerlendirme gate kaynağı |
| Metrik hesaplama bölümü | `legacy-2026-07/ai-models.md` | 590 (`### 9.2 Metrik Hesaplama`) | ölçüm uygulaması |
| Benchmark sonuçları (RTX 4090 · RPi5) | `legacy-2026-07/ai-models.md` | 713 · 726 | donanım-bazlı referans değerler |
| Job Queue sistemi | `legacy-2026-07/ai-models.md` | 434 (`### 7.2 Job Queue Sistemi`) | kuyruk testi senaryosu kaynağı |

> Metrik **değerleri** bu bölümde tekrar yazılmadı (kopya yasağı) → eşikler
> `testing-ornekler.md` §5.1 (TS-G1) ve `ai-models.md` §9'da tek kaynaktır.

### §6.7 Örnek TS-14 — Platform test matrisi

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/rules/cpp-global-CLAUDE.md` [Satır: 695–697]

| Platform | Bilinen test yükü | Test yaklaşımı |
|---|---|---|
| Windows | ASIO sürücü sorunları | birden çok ASIO cihazı ile test (Dante · RME vb.) |
| Linux | dağıtıma göre ALSA yapılandırması | Ubuntu 22.04 · Fedora 38 · Raspberry Pi OS |

`rules/cpp-global-AGENTS.md` [Satır: 357]: platform desteği "Windows/macOS/Linux/ARM64 (RPi5) —
**all paths tested**" · [Satır: 364]: derleme + çalıştırma Windows + 1 diğer platform zorunlu.

### §6.8 K006.9 gate'leri ile eşleme (§5.7'ye ek)

| Yeni | Ne ölçer | Bağlı mevcut gate |
|---|---|---|
| TS-G8 | coverage ≥ 80% (public API) | TS-G1 kalite gate'ini tamamlar (kod yüzeyi) |
| TS-G9 | `ctest -R "AIAutoEQ::"` yeşil | TS-G5 EP düşüş testini çalıştırma zemini |
| TS-G10 | golden WAV regresyonu (Faz 4) | TS-G1 metrik kapılarının zamana dayanıklılığı |
| TS-G11 | merge öncesi benchmark (latency/CPU) | TS-G2 RT gecikme bütçesi |

Bu turda **hiçbiri çalıştırılmadı** → `../yapay-zeka-dogrulama.md` §7'ye taşındı.

### §6.9 §6 tur kapanış kaydı

- Eklenen: 2 tam C++ test alıntısı (TS-8 · TS-9) + 2 bash bloğu (TS-10) + 4 kaynaklı
  gereksinim satırı (TS-11) + 2 ölçüm tablosu (TS-12) + 5 satır AI zemini (TS-13) +
  2 satır platform matrisi (TS-14) + 4 eşleme satırı (TS-G8…G11).
- Uydurma identifier: 0 · ölçü uydurma: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 0.
- Alıntı kodlar vault kural dosyalarından birebir; repo `*.cpp` = 0 olduğu için
  "repo test kodu" iddiası **yapılmamıştır**.

---

**Authority:** Master-Orchestrator kapanış turu · **Status:** active · **Updated:** 2026-10-15

---

## §7 Kapanış Derinliği — Test çalışma sırası, görev kalıbı ve kapanış kontrol listesi (2026-10-15)

### §7.1 Örnek TS-15 — Tüm projeleri test etme komutu (tam alıntı)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/rules/cpp-global-CLAUDE.md` [Satır: 113–124]

```bash
# Run unit tests (Google Test framework)
cd build
ctest --output-on-failure -j 8

# Per-project tests
ctest -R NevaEngine::Equalizer
ctest -R NevaPlayer::Codec
ctest -R WirelessConnect::WiFi
ctest -R NevaConnect::I2C
```

**K006.9 çıkarımı:** paralel `-j 8` + `--output-on-failure` K006 test komutunun da
varsayılanıdır; alt alan adları (`recommendation` · `music-analysis` · `inference` …)
`-R` filtresiyle aynı kalıpta adlandırılır.

### §7.2 Örnek TS-16 — Yeni efekt/AI bloğu ekleme görev kalıbı (7 adım)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/rules/cpp-global-AGENTS.md` [Satır: 91–97]

| Adım | Kalıp (kural dosyası) | K006 karşılığı |
|---|---|---|
| 1 | İlgili vault dosyasını oku (`CLAUDE.md` → `[[equalizer-system]]` …) | `anayasa` → `kapsam` okuması |
| 2 | Header (`include/dsp/MyEffect.hpp`) | motor arayüzü (`Contracts/AI/*.php` karşılığı) |
| 3 | Implementasyon (`src/dsp/MyEffect.cpp`) | uygulama kodu |
| 4 | **Unit test ekle** (`tests/NevaEngine/MyEffectTest.cpp`, [Satır: 94]) | K006 test dosyası |
| 5 | Zincire bağla (`L1_DSP::SignalChain`, [Satır: 95]) | workflow'a ekle (AIWorkflow L49 şeması) |
| 6 | `cmake --build build && ctest -R MyEffect` ([Satır: 96]) | `vendor/bin/phpunit --filter` + `ctest` |
| 7 | Vault güncelle (`.ai/log.md`, [Satır: 97]) | `-dogrulama.md` §2'ye iddia satırı |

Aynı dosyanın Task-3 kalıbı benchmark'ı **zorunlu adım** yapar: benchmark yaz ([Satır: 116]) →
profil al (117) → optimize et (118) → doğrula: "zero data loss, latency < 5 ms ASIO target"
([Satır: 119]) → `ctest -R RingBuffer && ./tests/RingBufferBench` ([Satır: 120]) →
**"Commit with performance numbers in message"** ([Satır: 121]).

### §7.3 Örnek TS-17 — AI Auto-EQ test görevi (K006 ile doğrudan kesişen tek görev)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/rules/neva-engine-AGENTS.md` [Satır: 63–71]

| Adım | Kural dosyası satırı | İçerik |
|---|---|---|
| 1 | [Satır: 67] | `[[projects/neva-engine/eq-ai-auto]]` model şartnamesini oku |
| 2 | [Satır: 68] | ONNX Runtime'ı `CMakeLists.txt`'e ekle |
| 3 | [Satır: 69] | `AIAutoEQProcessor::infer(audioBuffer)` → EQ kazançlarını döndür |
| 4 | [Satır: 70] | **Test: `ctest -R AIAutoEQ::Inference`** |
| 5 | [Satır: 71] | WebSocket doğrulama: `{"cmd": "aiAutoEq", "genre": "rock"}` |

Aynı dosyanın Task-1 ([Satır: 42]: `ctest -R Equalizer::SetGain`) ve Task-3
([Satır: 58–60]: FFT benchmark → hedef < 10 ms) kalıpları, AI testi ile DSP testini
**aynı adım düzenine** sokar: implement → `ctest -R <Bileşen>::<Metrik>` → canlı doğrulama.

### §7.4 Örnek TS-18 — Her commit sonrası kapanış kontrol listesi (tam okuma)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/rules/neva-engine-AGENTS.md` [Satır: 253–263]

| # | Kural satırı | Kontrol |
|---|---|---|
| 1 | [Satır: 255] | tüm testler geçer: `ctest --output-on-failure` |
| 2 | [Satır: 256] | audio callback ihlali yok (`grep -R "(new\|malloc)" src/asio/`) |
| 3 | [Satır: 257] | CPU < %30 (benchmark sonucu) |
| 4 | [Satır: 258] | ASIO gecikme < 5 ms (`/health`) |
| 5 | [Satır: 259] | FFT 30 FPS güncelleniyor |
| 6 | [Satır: 260] | VST3 pluginleri doğru yükleniyor (değiştiyse) |
| 7 | [Satır: 261] | ring buffer'da veri bozulması yok (stress test) |
| 8 | [Satır: 262] | `.ai/log.md` tek-satır kaydı |
| 9 | [Satır: 263] | mimari değişiklik → `-decisions/` ADR |

**K006 uygulaması:** madde 1–7 K006.9 gate'leriyle (TS-G1…G11) eşleştirilir; madde 8–9
bu vault'un kendi disiplinidir (`-dogrulama.md` §2 + `-kararlar.md` ADR girişi).

### §7.5 §7 tur kapanış kaydı

- Eklenen: 1 bash bloğu (TS-15) + 7 satır görev kalıbı (TS-16) + 5 satır AI test görevi
  (TS-17) + 9 satır kapanış listesi (TS-18).
- Uydurma identifier: 0 · ölçü uydurma: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 0.
- Hepsi bu turda okunan kural dosyalarından satır numarasıyla alındı.

---

**Authority:** Master-Orchestrator kapanış turu · **Status:** active · **Updated:** 2026-10-15
**Kapanış turu kaydı:** Bu dosya 305 → ≥500 hedefine §6–§7 append'iyle ulaştı; hiçbir mevcut satır silinmedi (append-only).
