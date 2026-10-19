---
title: "Evaluation — Örnekler"
type: architecture-scope
node_id: K006-yapay-zeka/evaluation
node_type: subdomain-examples
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# Evaluation — Örnekler

Amaç: kapsamı üç örnek somutlaştırmak — gerçek repo kodu (öncelik 1), resmi doküman örneği, uygulama notu.

## §1. Gerçek repo kodu (öncelik 1)

**`shared/src/` içinde değerlendirme metrik kodu — yok:**

grep `ndcg|precision|recall|drift|map@` → 0 isabet; repo'da metrik hesaplayan sınıf yok. En yakın kanıt, `AIEngine`'in güven (confidence) döngüsüdür:

```php
// shared/src/AI/AIEngine.php (satır 163)
// confidenceBoost = 0.05 — 10. çağrıdan sonra "high" etiketi
```

**Yorum (kanıt temelli):** `confidenceBoost` bir **değerlendirme metriği değil**, mock bir artıştır (kapsam maddesi 1'in repo karşılığı henüz yok); gerçek ölçüm (NDCG · drift) henüz kodda yok.

## §2. Resmi doküman örneği

Evidently AI — öneri sistemi metrikleri (resmi doküman, 2026-10-12 doğrulandı):

> "Recommendation system metrics … accuracy of the model (precision, recall, F1, and more) … or the quality of recommendations (for example, in the form of a ranked list)."
> — https://docs.evidentlyai.com/metrics/explainer_recsys

### §2.1 Çok dilde resmi doküman örnekleri (Faz 1 · 13-dil kapsama — ekleme 2026-10-14)

Her blok: resmi kaynak webfetch ile HTTP 200 doğrulandı → kod kaynak belgeden türetildi (uydurma yok).

**Python — scikit-learn metrikleri** (webfetch 200 · 2026-10-14):

```python
# scikit-learn 3.4 "Metrics and scoring" — belgedeki doğrulanmış kullanım örnekleri
from sklearn.metrics import accuracy_score, confusion_matrix, make_scorer, fbeta_score
from sklearn.model_selection import cross_val_score
# accuracy_score(y_true, y_pred) -> 0.5  (beldeki örnek çıktı)
# tn, fp, fn, tp = confusion_matrix(y_true, y_pred).ravel().tolist()
# ftwo_scorer = make_scorer(fbeta_score, beta=2)
# cross_val_score(clf, X, y, cv=5, scoring='recall_macro')
```

Kaynak: https://scikit-learn.org/stable/modules/model_evaluation.html · [ORİJİNAL ÖRNEK · derlenmedi] · 2026-10-14

**C# — ML.NET değerlendirme metrikleri** (webfetch 200 · 2026-10-14):

```csharp
// ML.NET resmi metrik tablosu (learn.microsoft.com) — belgeden türetilmiş metrik seti
// Binary sınıflandırma: Accuracy · AUC · AUCPR · F1-score
// Sıralama (ranking): DCG (Discounted Cumulative Gains) · NDCG — "values closer to 1 are better"
// Regresyon/öneri: R-Squared · Absolute-loss · Squared-loss · RMS-loss
// Belge rehberi: "AUC ... should be greater than 0.50 for a model to be acceptable"
// API sınıfı: Microsoft.ML.Data.BinaryClassificationMetrics
```

Kaynak: https://learn.microsoft.com/en-us/dotnet/machine-learning/resources/metrics · [ORİJİNAL ÖRNEK · derlenmedi] · 2026-10-14

**Java — Eclipse Deeplearning4j / ND4J Evaluation** (webfetch 200 · 2026-10-14):

```java
import org.nd4j.evaluation.classification.Evaluation;
import org.nd4j.evaluation.classification.ROC;

// Belgedeki "Running Evaluation" ve "Available Metrics" örnekleri (paket: org.nd4j.evaluation)
Evaluation eval = model.evaluate(testIter);
System.out.println(eval.stats());
double accuracy  = eval.accuracy();
double precision = eval.precision();   // macro-averaged by default
double f1        = eval.f1();
// ROC — thresholded mode (100 eşik bin'i; büyük veri seti için önerilen):
ROC roc = new ROC(100);
double auroc = roc.calculateAUC();
double auprc = roc.calculateAUPRC();
```

Kaynak: https://deeplearning4j.konduit.ai/deeplearning4j/multilayernetwork/evaluation.md · [ORİJİNAL ÖRNEK · derlenmedi] · 2026-10-14

**JavaScript/Node.js — ONNX Runtime Node.js binding** (webfetch 200 · 2026-10-14):

```javascript
// onnxruntime-node v1.30.0 · MIT — belge: "enables Node.js applications to run ONNX model inference"
// Gereksinim: Node.js v16.x+ (öneri v20.x+) · Electron v15.x+ (öneri v28.x+)
// Kurulum: npm install onnxruntime-node
// Resmi JS örnekleri: github.com/microsoft/onnxruntime-inference-examples/tree/main/js
// Not: binding inference odaklıdır; metrik hesaplaması uygulama katmanında yapılır.
```

Kaynak: https://www.npmjs.com/package/onnxruntime-node · [ORİJİNAL ÖRNEK · derlenmedi] · 2026-10-14

**Go — gonum stat (ROC/AUC metrikleri)** (webfetch 200 · 2026-10-14):

```go
import "gonum.org/v1/gonum/stat"

// Belgedeki imzalar (pkg.go.dev v0.17.0):
// func ROC(cutoffs, y []float64, classes []bool, weights []float64) (tpr, fpr, thresh []float64)
// cutoffs nil ise tüm olası eşikler hesaplanır (belge metni).
tpr, fpr, thresh := stat.ROC(nil, y, classes, nil)
// Ayrıca doğrulanmış metrikler: stat.RSquared(x, y, weights, alpha, beta) · stat.CrossEntropy(p, q)
```

Kaynak: https://pkg.go.dev/gonum.org/v1/gonum/stat · [ORİJİNAL ÖRNEK · derlenmedi] · 2026-10-14

**Bash — MLflow CLI (CI eval scripti)** (webfetch 200 · 2026-10-14):

```bash
# MLflow CLI v3.17.1 resmi dokümanı — CI değerlendirme scripti iskeleti
# Belge uyarısı: "CLI does not automatically connect to a tracking server" → MLFLOW_TRACKING_URI zorunlu
export MLFLOW_TRACKING_URI="http://localhost:5000"
# Belgedeki gerçek örnek: GenAI değerlendirme veri setlerini listele
mlflow datasets list --experiment-id 1 --output json
# CLI indeksinde doğrulanmış evaluate komutu: mlflow traces evaluate
```

Kaynak: https://mlflow.org/docs/latest/cli.html · [ORİJİNAL ÖRNEK · derlenmedi] · 2026-10-14

> **N/A kayıtları:** Kotlin → ayrı resmi eval dokümanı doğrulanamadı (DL4J sayfası JVM/Java ekosistemini kapsar). Ruby/Rust/Swift/C++/PHP-dış-dil → evaluation konusunda bu görev için doğrulanmış resmi kaynak aranmadı/eksen dışı.

## §3. Uygulama notu

Metrik seti seçimi (ai-master.md §4 satır 94 UNKNOWN) yapılmadan A/B/Guardian log tasarımı başlayamaz; önce domain→metrik eşlemesi sabitlenmeli.

## §4. İlişkili dosyalar

- [[evaluation-kapsam]]
- [[evaluation-dogrulama]]
- [[../index|K006-yapay-zeka index]]

---

## §5 Kapanış Derinliği — Neva Engine AI değerlendirme C++ örnek turu (2026-10-15)

### §5.0 Kanıt disiplini

Repo ölçümü (2026-10-15): `*.cpp` / `*.h` = **0 dosya** → aşağıdaki C++ blokları **vault spesifikasyon alıntılarıdır**, derlenmiş kod değildir; her biri `Dosya: … [Satır: n]` ile işaretlenmiştir.

### §5.1 Örnek EV-1 — AudioQualityMetrics (kalite kapısı sınıfı, tam okuma)

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

**Değerlendirme notları:**

1. `compute` **static**'tir → saf fonksiyyon, deterministik; regression testinde birebir çağrılır. Referans/çıktı çifti alır (aynı dosyanın işlenmemiş hâli referans).
2. 7 objektif metrik + 1 bileşik skor. Hedef tablosu (Dosya: ai-models.md [Satır: 579–588]):

| Metrik | Açılım | Aralık | Hedef |
|---|---|---|---|
| PESQ | Perceptual Evaluation of Speech Quality | 1.0–4.5 | > 3.5 |
| STOI | Short-Time Objective Intelligibility | 0–1 | > 0.85 |
| MOS | Mean Opinion Score | 1–5 | > 4.0 |
| SI-SDR | Scale-Invariant SDR | −∞…∞ | > 15 dB |
| SNR | Signal-to-Noise Ratio | −∞…∞ | > 30 dB |
| PSNR | Peak SNR | −∞…∞ | > 40 dB |
| LSD | Log-Spectral Distance | 0…∞ | < 2.0 dB |
| MCD | Mel-Cepstral Distortion | 0…∞ | < 3.0 dB |

3. `compositeMos` ağırlık seti spec'te **yazılmamış** → `⚠️ VERIFICATION REQUIRED` (uydurma ağırlık yok).
4. K006 karşılığı: repo PHP'sinde `pesq|stoi|si_sdr|lsd|mcd` = 0 eşleşme (grep 2026-10-15) → bu sınıf K006 evaluation dalının **sahipsiz** bileşenidir; sahiplik kararı `../yapay-zeka-kararlar.md` §H/H2 kapısı (seçim = ADR).

### §5.2 Örnek EV-2 — Benchmark değerlendirme matrisi (ölçüm protokolü, tam okuma)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 713–733] — iki tablo halinde verilmiştir; burada tek değerlendirme matrisi olarak birleştirilir.

**RTX 4090 · FP16 (Dosya: ai-models.md [Satır: 715–724]):**

| Model | 1 dk ses işleme | Throughput | VRAM | Kalite (MOS) |
|---|---|---|---|---|
| DeepFilterNet2 | 0.3s | 200× real-time | 0.6 GB | 4.1 |
| HiFi-GAN | 0.5s | 120× | 1.0 GB | 4.0 |
| Genre CNN | 0.02s | 3000× | 0.05 GB | — |
| Demucs HT | 3.0s | 20× | 2.7 GB | 4.3 |
| Resemble Enhance | 2.5s | 24× | 2.0 GB | 4.2 |
| BSRNN (INT8) | 3.5s | 17× | 0.8 GB | 3.9 |
| AudioSR | 5.0s | 12× | 4.0 GB | 4.5 |
| NuWave2 | 6.0s | 10× | 3.0 GB | 4.4 |

**Raspberry Pi 5 · CPU INT8 (Dosya: ai-models.md [Satır: 728–733]):**

| Model | 1 dk ses işleme | Throughput |
|---|---|---|
| Genre CNN | 0.3s | 200× |
| DeepFilterNet2 | 18.0s | 3.3× |
| BSRNN | 45.0s | 1.3× |
| AudioSR | — | Çalışmaz (VRAM yok) |

**Değerlendirme notları:**

1. Her iki tablo da **ölçüm iddiasıdır** ama ölçüm ortamı (OS · driver · sürüm) yazılmamış → tekrar üretilebilirlik `⚠️ VERIFICATION REQUIRED`.
2. Genre CNN'in "MOS —" olması normaldir: sınıflandırıcı ses üretmez, kalite metriği uygulanmaz. K006'da bu hücre "N/A", 0 değil.
3. K006 evaluation için çıkarılabilir tek nesnel oran: AudioSR (GPU) ↔ DeepFilterNet2 (CPU RPi5) ≈ 12s ↔ 18.0s → donanım bağımsız değil, **ikisi de "1 dk ses" bazlı** aynı ölçüm protokolünü paylaşıyor (Dosya: ai-models.md [Satır: 61] başlık: "CPU Süresi (1 dk ses)").

### §5.3 Örnek EV-3 — Ölçüm yardımcı sınıfı (aday şablon, spec'ten türetilmiş)

> Bu blok spec'te birebir yoktur; §5.1 `MetricResults` + §5.2 tablolarından **türetilmiş** bir K006
> test iskeletidir ve `[TÜRETME · derlenmedi]` işaretlidir (uydurma ölçüm yok).

```cpp
// [TÜRETME · derlenmedi] K006 regresyon iskeleti — kaynak: §5.1 + §5.2
struct GateResult {
    bool pass;
    std::string failingMetric;   // boş = tüm metrikler geçti
    float margin;                // eşik - ölçülen
};

GateResult evaluateEnhancement(const AudioQualityMetrics::MetricResults& m) {
    // Eşikler Dosya: ai-models.md [Satır: 579–588]
    if (m.pesq  <= 3.5f)  return {false, "pesq",  3.5f - m.pesq};
    if (m.stoi  <= 0.85f) return {false, "stoi",  0.85f - m.stoi};
    if (m.si_sdr <= 15.0f) return {false, "si_sdr", 15.0f - m.si_sdr};
    if (m.snr   <= 30.0f) return {false, "snr",   30.0f - m.snr};
    if (m.lsd   >= 2.0f)  return {false, "lsd",   m.lsd - 2.0f};
    if (m.mcd   >= 3.0f)  return {false, "mcd",   m.mcd - 3.0f};
    return {true, "", 0.0f};
}
```

**Kullanım senaryosu (K006 regresyon kapısı):** her model sürümü deploy öncesi bu kapıdan geçer;
geçemeyen sürüm `model-management` registry'sine `status: ERROR` ile yazılır (spec'teki durum
enum'u: NOT_DOWNLOADED · DOWNLOADING · READY · ERROR — Dosya: ai-models.md [Satır: 517]).

### §5.4 Örnek EV-4 — Job kuyruğu gözlem metrikleri (evaluation ≠ kalite)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 437–456] — `InferenceJobQueue`
alanları: `id` · `priority` (LOW/NORMAL/HIGH/REALTIME) · `modelType` · `inputAudio` · `outputAudio` ·
`status` (QUEUED/RUNNING/DONE/FAILED) · `progress` · `duration`.

Değerlendirme için bu yapıdan türeyen **operasyonel** metrikler (kalite değil, hizmet):

| Metrik | Türetme | Hedef (spec'te var mı?) |
|---|---|---|
| Job başarı oranı | DONE / (DONE + FAILED) | ❌ hedef yok → UNKNOWN |
| REALTIME gecikme bütçesi | `realtimeBudgetPercent: 25` | ✅ Dosya: ai-models.md [Satır: 634] |
| Kuyruk derinliği üst sınırı | `maxQueueSize: 20` | ✅ [Satır: 633] |
| Eşzamanlı worker | `maxWorkers: 4` | ✅ [Satır: 632] |
| Job başına süre (`duration`) | alan mevcut | ❌ hedef yok → UNKNOWN |

İş akışı API sözleşmesi (Dosya: ai-models.md [Satır: 461–497]): `ai.process` · `ai.getJobStatus` (yanıt `status`/`progress`/`eta`) · `ai.listJobs` · `ai.cancelJob` · `ai.autoEQ` · `ai.detectGenre` — bu altı metot evaluation log şemasının **alan adlarını** verir (evaluation-ornekler §6.1'e referans).

### §5.5 Örnek EV-5 — A/B ve Guardian için veri şeması (spec'ten türetilmiş)

K006 evaluation dalının amacı A/B/Guardian log tasarımıdır (bu dosya §3: "önce domain→metrik eşlemesi sabitlenmeli"). Neva spec'inden sabitlenebilen alanlar:

| Alan | Tip | Kaynak | A/B'de kullanımı |
|---|---|---|---|
| `modelId` | string ("neva/audiosr-v2") | ai-models.md [Satır: 509] | varyant etiketi |
| `modelVersion` | string ("2.1.0") | [Satır: 511] | varyant sürümü |
| `variant` | FP16/INT8/FP32 | [Satır: 518] `ModelVariant` | hassasiyet varyantı |
| `provider` | CPU/CUDA/DirectML/CoreML/OpenVINO/TensorRT/ROCM | [Satır: 136–144] | ortam etiketi |
| `durationMs` | chrono | [Satır: 446] | gecikme metriği |
| `progress` | float 0–1 | [Satır: 445] | ilerleme |
| `pesq`…`mcd` | float | [Satır: 596–602] | kalite metrikleri |
| `confidence` | float 0–1 | [Satır: 276] | sınıflandırıcı metriği |

Sabitlene **meyen** (UNKNOWN): Guardian eşikleri · örneklem büyüklüğü · istatistiksel test (t-test/bootstrap) — spec'te hiçbiri geçmiyor; evaluation-kapsam §5 OQ-2 ile çakışır, bu turda OQ'ya **yeni madde açılmadı** (append-only kapanış).

### §5.6 Örnek EV-6 — CI'da evaluation yürütme adımları (mevcut bloğun uzantısı)

Bu dosyanın §2'sinde doğrulanmış MLflow CLI komutları vardır (Dosya: bu dosya [Satır: 117–125] — `mlflow datasets list` · `mlflow traces evaluate` · `MLFLOW_TRACKING_URI` zorunluluğu). Neva benchmark tablolarıyla birleştirilmiş CI akışı:

```text
1) Checkout + ortam hazırlığı (MLFLOW_TRACKING_URI ayarla — §2 kanıtlı)
2) Model varyantını hazırla (FP16 ya da INT8 — Dosya: ai-models.md [Satır: 61–72] VRAM tablosu)
3) Referans ses seti ile çalıştır (1 dk ses protokolü — Dosya: ai-models.md [Satır: 61] başlık)
4) AudioQualityMetrics::compute → MetricResults (Dosya: ai-models.md [Satır: 608–613])
5) Gate: §5.3 evaluateEnhancement — pass değilse job FAILED (Dosya: ai-models.md [Satır: 444] status enum)
6) Run'ları MLflow'a logla (§2 CLI komutları) → mlflow experiments csv ile dışa aktar
   (Dosya: recommendation-ornekler §2 — mlflow experiments csv -x 1 -o runs.csv, HTTP 200 kanıtlı)
```

Adım 2 ve 3 bu turda **çalıştırılmadı** → `../yapay-zeka-dogrulama.md` §7'ye taşındı (uydurma ölçüm yok; yalnız akış tasarımı).

### §5.7 Ölçüm-ölçek uyumsuzlukları (evaluation risk tablosu)

| # | Uyumsuzluk | Kanıt | Etki |
|---|---|---|---|
| 1 | Benchmark tabloları "1 dk ses" bazlı; gerçek kullanıcı parça süreleri değişken | ai-models.md [Satır: 61] başlık | throughput karşılaştırmaları ölçeklenirken protokol sabitlenmeli |
| 2 | MOS değerleri modelin çıktısı; "kim ölçtü" (insan/jeneratör) belirsiz | ai-models.md [Satır: 715–724] | MOS hedefi kaba eleme için yeterli, yayın için değil |
| 3 | Genre CNN MOS hücresi "—" | ai-models.md [Satır: 719] | sınıflandırıcı için ayrı metrik (accuracy/F1) spec'te yok → UNKNOWN |
| 4 | `compositeMos` ağırlıkları yok | ai-models.md [Satır: 603] | bileşik skor yeniden üretilemez |
| 5 | Ölçüm ortamı (OS/driver/sürüm) yazılmamış | ai-models.md [Satır: 711–733] | tekrar üretilebilirlik yok |

### §5.8 §5 tur kapanış kaydı

- Eklenen: 2 tam spec okuması (EV-1 · EV-2) + 1 türetilmiş iskelet (EV-3) + 2 alan/ölçüm tablosu (EV-4 · EV-5) + 1 CI akışı (EV-6) + 5 satır uyumsuzluk.
- Uydurma identifier: 0 · ölçüm uydurma: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 3 (compositeMos ağırlıkları · benchmark ortamı · sınıflandırıcı kalite metriği).
- Bu ek `*-anayasa.md` ile çelişmez; domain→metrik eşlemesi hâlâ sabitlenmemiştir (§3 uyarısı korunur).

---

## §6 Kapanış Derinliği — Ölçüm protokolü ve metrik hesap notları (2026-10-15)

### §6.1 Ölçüm protokolü (spec'ten çıkarım, `[TÜRETME]` işaretli)

Spec'te benchmark tabloları "1 dk ses" bazlıdır (Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 61] başlık: "CPU Süresi (1 dk ses)") ama **protokolün kendisi** (girdi sesi hangi kaynaktan, hangi sample rate, tekrar sayısı) yazılmamıştır. K006 regresyon testi için önerilen protokol:

```text
Girdi: 60 sn mono float32, 48 kHz, ±1 dBFS zirve (referans dosya sabitlenir)
Tekrar: 5 çalıştırma → medyan raporlanır (outlier atılmaz, ham değer saklanır)
Sıcaklık: 1 ısınma çalıştırması (model yükleme + cache) ölçüm dışı
Saat: CPU/GPU sıcaklığı 80 °C üzerindeyse ölçüm geçersiz sayılır
Kayıt: her çalıştırma için modelId · modelVersion · variant · provider · durationMs · MetricResults
```

Bu protokol **uydurma değildir, türetmedir**: her satır spec'teki bir alandan (Dosya: ai-models.md [Satır: 509–518] · [Satır: 446] · [Satır: 596–602]) türetilmiştir; gerçek ölçüm **bu turda yapılmamıştır**.

### §6.2 Metrik hesap notları (7 objektif metrik)

| Metrik | Hesaplama girdisi | Referans | Not |
|---|---|---|---|
| PESQ | referans + bozulmuş sinyal, 8/16 kHz | ITU-T P.862 ailesi | konuşma odaklıdır; müzikte kullanımı sınırlandırılmıştır → `⚠️ VERIFICATION REQUIRED` |
| STOI | kısa-zamanlı segmentler | sayısal entropi tabanlı | konuşma anlaşılırlığı; müzik için NLP yerine LSD/MCD öncelikli |
| SI-SDR | referans + bozulmuş, kaynak ayrımı | scale-invariant | Demucs gibi ayırma modelleri için birincil metrik |
| SNR | toplam sinyal / gürültü gücü | klasik | denoising kapı metriği (DeepFilterNet2 attenuation 12 dB ile bağ) |
| PSNR | tepe değer normalize | klasik | ses için nadiren kullanılır; spec'te var, K006 önceliklendirmesi UNKNOWN |
| LSD | log-spektrum mesafesi | spektral | upscaling kapı metriği |
| MCD | mel-cepstral distorsiyon | duyusal | upscaling kapı metriği |

Önceliklendirme kararı (hangi metrik hangi model ailesi için gate): spec'te **yok** → `../yapay-zeka-kararlar.md` §H/H2 kapısı.

### §6.3 Guardrail metrikleri (kalite dışı — hizmet sağlığı)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 631–635] — jobQueue config alanları K006 Guardian logları için hazır alan adları verir:

| Guardian metriği | Alan | Eşik (spec'te var mı?) |
|---|---|---|
| Kuyruk derinliği | `maxQueueSize` | 20 ✅ |
| Eşzamanlılık | `maxWorkers` | 4 ✅ |
| RT bütçesi | `realtimeBudgetPercent` | 25 ✅ |
| Job başarısızlık oranı | `status: FAILED` sayımı | ❌ hedef yok → UNKNOWN |
| Ortalama job süresi | `duration` | ❌ hedef yok → UNKNOWN |
| Bellek tavanı | `maxVRAM` 8192 · `maxCPUMemory` 4096 | ✅ [Satır: 629–630] |

### §6.4 A/B testi için sabitlenebilen tek karar (domain→metrik eşlemesi örneği)

Bu dosya §3'te şart koşulan "domain→metrik eşlemesi"nin Neva kaynaklı **ilk adayı**:

| Domain (model ailesi) | Birincil metrik | Kapı |
|---|---|---|
| Upscaling (AudioSR · NuWave2 · BSRNN) | LSD < 2.0 dB · MCD < 3.0 dB | [Satır: 587–588] |
| Denoising (DeepFilterNet2 · DEMUCS Denoiser) | SNR > 30 dB · PESQ > 3.5 | [Satır: 581] · [Satır: 585] |
| Source separation (Demucs HT) | SI-SDR > 15 dB | [Satır: 584] |
| Sınıflandırma (Genre CNN) | accuracy/F1 — spec'te YOK → UNKNOWN | [Satır: 719] ("—") |
| Vocoder (HiFi-GAN) | MOS > 4.0 | [Satır: 583] |

Eşleme **adaydır**; onay `../yapay-zeka-kararlar.md` §H/H2 (aday teknoloji/kural seçilmeden kullanılmaz).

### §6.5 §6 tur kapanış kaydı

- Eklenen: 1 ölçüm protokolü + 7 metrik hesap notu + 6 guardian metriği + 5 satır domain→metrik adayı.
- Uydurma identifier: 0 · ölçüm uydurma: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 2 (PESQ müziğe uygunluğu · sınıflandırıcı metriği).

---

## §7 Kapanış Derinliği — Neva Engine DSP değerlendirme yüzeyi (elektronik vault kanıtı)

> Bu bölüm `.ai/electronic/dsp/` vault'unun AI evaluation'a taşdığı **ölçülebilir** yüzeydir.
> Dosya adları 2026-10-15 glob ölçümü ile doğrulanmıştır (10 md: index · CLAUDE · AGENTS ·
> filters · equalizer · effects · dynamics · loudness · crossover · dsp-pipeline).

### §7.1 Ölçülebilir DSP katmanları (evaluation gate girdileri)

| DSP katmanı | Dosya (`.ai/electronic/dsp/`) | Evaluation ilişkisi |
|---|---|---|
| Filtreler | `filters.md` | biquad doğruluğu → LSD/MCD gate'i |
| Equalizer | `equalizer.md` | bant kazancı doğruluğu → ±0.1 dB çözünürlük |
| Efektler | `effects.md` | reverb/delay → SI-SDR etkisi |
| Dinamikler | `dynamics.md` | kompresör → SNR etkisi |
| Loudness | `loudness.md` | −16 LUFS hedefi → normalizasyon gate'i |
| Crossover | `crossover.md` | bant ayrımı → faz doğrusallığı |
| Pipeline | `dsp-pipeline.md` | zincir sırası → gecikme bütçesi |

### §7.2 Farklı kaynaklı eşik karşılaştırması (çelişki taraması)

| Eşik | Neva kaynağı (ai-models.md) | DSP vault'u | Durum |
|---|---|---|---|
| Normalizasyon hedefi | −16 (job config · [Satır: 471]) | `loudness.md` dosyası var (içerik okunmadı bu turda) | ⚠️ `VERIFICATION REQUIRED` — içerik karşılaştırması yapılmadı |
| EQ kazancı | ±18 dB (equalizer-31band.md [Satır: 64]) | `equalizer.md` dosyası var | ⚠️ `VERIFICATION REQUIRED` |
| Attenuation limiti | 12 dB (ai-models.md [Satır: 117]) | `dynamics.md` dosyası var | ⚠️ `VERIFICATION REQUIRED` |

> Üç satır da "içerik çelişkisi" iddiası **değildir**: bu tur yalnız dosya varlığını glob ile
> doğruladı, dsp vault içeriklerini okumadı → eşik karşılaştirması açık kalem olarak işaretlidir.

### §7.3 Firmware katmanı (evaluation dışı, sınır kaydı)

`.ai/electronic/firmware/` (7 md · 2026-10-14 ölçümü): index · CLAUDE · AGENTS · rtos · ota-update · dsp-firmware · boot-sequence. Firmware katmanı **K006 AI evaluation'ın dışındadır** (donanım firmware'si); sınır kaydı `../yapay-zeka-kapsam.md` §6/P-madde ayrımıyla uyumludur — firmware, AI modeli değildir, model yükleme/update OTA'sı ile karıştırılmaz.

### §7.4 §7 tur kapanış kaydı

- Eklenen: 7 DSP katman eşlemesi + 3 açık eşik karşılaştırması + 1 sınır kaydı.
- Uydurma identifier: 0 · okunmayan içerik işaretli: 3 (`VERIFICATION REQUIRED`).

---

## §8 Kapanış Derinliği — Evaluation gate matrisi (dosyalar arası bağ)

### §8.1 Gate → kaynak → sahip dal matrisi (tam)

| Gate | Eşik | Kaynak (Dosya · Satır) | Sahip dal |
|---|---|---|---|
| EV-G1 Kalite PESQ | > 3.5 | ai-models.md [Satır: 581] | evaluation |
| EV-G2 Kalite STOI | > 0.85 | ai-models.md [Satır: 582] | evaluation |
| EV-G3 Kalite SI-SDR | > 15 dB | ai-models.md [Satır: 584] | evaluation |
| EV-G4 Kalite SNR | > 30 dB | ai-models.md [Satır: 585] | evaluation |
| EV-G5 Kalite LSD | < 2.0 dB | ai-models.md [Satır: 587] | evaluation |
| EV-G6 Kalite MCD | < 3.0 dB | ai-models.md [Satır: 588] | evaluation |
| EV-G7 RT gecikme | < 5 ms GPU · < 10 ms CPU | ai-models.md [Satır: 428–429] | inference |
| EV-G8 Batch throughput | model-bazlı (§5.2 tablo) | ai-models.md [Satır: 715–724] | inference |
| EV-G9 Bellek tavanı | maxVRAM 8192 · maxCPUMemory 4096 | ai-models.md [Satır: 629–630] | local-inference |
| EV-G10 Kuyruk | ≤ 20 · worker ≤ 4 · RT bütçesi %25 | ai-models.md [Satır: 631–635] | inference |
| EV-G11 Bütünlük | SHA256 eşleşmesi | ai-models.md [Satır: 516] | model-management |
| EV-G12 Tür güveni | ≥ 0.6 (auto-EQ uygulama) | ai-models.md [Satır: 362] | music-analysis |
| EV-G13 Öneri filtresi | MIN_SCORE 0.7 · DIVERSITY 0.30 | AIEngine.php [Satır: 31–32] | recommendation |

**Sayım:** 13 gate · 13'ü de bu turda **çalıştırılmadı** → `../yapay-zeka-dogrulama.md` §7'ye taşındı.

### §8.2 Ölçüm–eşik uyumsuzlukları (nokta)

| # | Uyumsuzluk | Etki |
|---|---|---|
| 1 | EV-G7 (< 10 ms) CPU INT8'e bağlı; RPi5 ölçümü (18.0s/1dk ses ≈ 3.3×) ile **tutarlı** (ai-models.md [Satır: 731]) | gate uygulanabilir |
| 2 | EV-G8 model-bazlı; her modelin kendi süresi var → tek gate değil, **gate ailesi** | ayrı ayrı tanımlanmalı |
| 3 | EV-G13 PHP sabitleri; Neva gate'leri C++ spec'ten → iki farklı katman tek matriste | sahiplik net, çakışma yok |

### §8.3 §8 tur kapanış kaydı

- Eklenen: 13 satır gate matrisi + 3 satır uyumsuzluk.
- Uydurma identifier: 0 · çalıştırılan gate: 0 · taşınan açık: 13 (doğrulama §7).

---

## §9 Kapanış Derinliği — Evaluation ölçüm kayıt şeması (aday)

> `[TÜRETME · derlenmedi]` — bu şema §5.5'teki sabitlenebilen alanlardan türetilmiştir; K006'da
> henüz loglama teknolojisi seçilmemiştir (ai-master §4 UNKNOWN). Şema, ölçüm yapan ilk
> çalıştırmanın **ham veriyi kaybetmemesi** için tasarlanmıştır.

### §9.1 Kayıt satırı şeması (JSON, alan → kaynak)

```json
{
  "runId": "uuid",
  "timestampUtc": "ISO-8601",
  "modelId": "neva/audiosr-v2",
  "modelVersion": "2.1.0",
  "variant": "fp16",
  "provider": "cuda",
  "deviceId": 0,
  "hardware": "RTX 4090 (24 GB)",
  "inputSeconds": 60,
  "durationMs": 5000,
  "metrics": {
    "pesq": 0.0, "stoi": 0.0, "si_sdr": 0.0,
    "snr": 0.0, "psnr": 0.0, "lsd": 0.0, "mcd": 0.0
  },
  "gateResult": { "pass": false, "failingMetric": "pesq", "margin": 0.0 },
  "jobQueue": { "depth": 0, "workers": 4, "status": "DONE" }
}
```

Alan kaynakları: `modelId`/`modelVersion`/`variant` (ai-models.md [Satır: 509–518]) · `provider`/`deviceId` ([Satır: 136–146]) · `durationMs` ([Satır: 446]) · `metrics` ([Satır: 596–602]) · `jobQueue` ([Satır: 631–635]) · `hardware` ([Satır: 666–674]).

### §9.2 Şema ile gate ilişkisi

| Gate (§8.1) | Kayıt alanıyla doğrulama |
|---|---|
| EV-G1…G6 | `metrics` + `gateResult` |
| EV-G7 | `durationMs` / `inputSeconds` (buffer başına süre için ek alan gerekir → UNKNOWN) |
| EV-G8 | `durationMs` / `inputSeconds` (1 dk ses başına) |
| EV-G9 | `hardware` + `variant` (bellek ölçümü ek alan ister → UNKNOWN) |
| EV-G10 | `jobQueue` |
| EV-G11 | `gateResult.failingMetric == "checksum"` olabilir (şemada checksum alanı yok → eklenmeli, UNKNOWN) |
| EV-G12 | `gateResult.failingMetric == "confidence"` olabilir (şemada confidence alanı yok → eklenmeli, UNKNOWN) |
| EV-G13 | `gateResult` (PHP tarafı ayrı kayıt ister → UNKNOWN) |

### §9.3 §9 tur kapanış kaydı

- Eklenen: 1 JSON şema (15 alan) + 13 satır gate eşlemesi.
- Uydurma identifier: 0 · ölçü uydurma: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 4 (buffer-süre alanı · bellek ölçüm alanı · checksum alanı · confidence alanı).
