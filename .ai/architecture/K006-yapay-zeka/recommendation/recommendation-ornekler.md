---
title: "recommendation — Örnekler"
type: architecture-examples
node_id: K006-yapay-zeka/recommendation
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# recommendation — Örnekler

## §1 Gerçek repo kodu (ÖNCELİK 1 — bu oturumda okunarak doğrulandı)

`shared/src/AI/AIEngine.php` satır 49–77 — hybrid öneri akışı:

```php
public function getRecommendations(array $context, int $limit = 20): array
{
    $limit = min($limit, self::MAX_RECOMMENDATIONS);
    ...
    // 1. Collaborative filtering — benzer kullanıcı tercihleri
    $collaborativeScores = $this->getCollaborativeScores($userId, $history);

    // 2. Content-based — şarkı özellikleri
    $contentScores = $this->getContentBasedScores($context);

    // 3. Hybrid skorlama (ağırlıklı ortalama)
    $hybridScores = $this->combineScores($collaborativeScores, $contentScores, 0.6, 0.4);

    // 4. Çeşitlilik filtresi — %30 farklı tür garantisi
    $diversified = $this->applyDiversityFilter($hybridScores, $genre);

    // 5. Minimum skor filtresi
    $filtered = array_filter($diversified, fn(array $item) => $item['score'] >= self::MIN_SCORE);

    // 6. Sıralama ve limit
    usort($filtered, fn(array $a, array $b) => $b['score'] <=> $a['score']);

    return array_slice(array_values($filtered), 0, $limit);
}
```

`shared/src/AI/AIEngine.php` satır 216–217 — collaborative tarafı **henüz boş**:

```php
// DB'den benzer kullanıcıları bul
// Gerçek implementasyonda matrix factorization (ALS) kullanılacak
return [];
```

`shared/src/Contracts/AI/AIEngineInterface.php` satır 26 — dönüş şeması:

```php
@return array<int, array{id: string, title: string, artist: string, score: float, reason: string}>
```

## §2 Resmi doküman örneği (URL + snippet)

**Microsoft Recommenders (Linux Foundation AI & Data)** ·
https://github.com/recommenders-team/recommenders (doğrulandı 2026-10-12):

> "Recommenders ... examples and best practices for building recommendation systems ... five key tasks:
> Prepare Data · Model · Evaluate · Model Select and Optimize · Operationalize."

Aynı depo benchmark tablosunda MovieLens 100k için `MAP · nDCG@k · Precision@k · Recall@k · RMSE · MAE`
sütunları ve `k=10` ranking metrikleri kullanır (README, 2026-10-12 okundu).

**LibRecommender** · https://librecommender.readthedocs.io/en/stable/ (doğrulandı 2026-10-12):

> "A hybrid recommender system, which allows users to use either collaborative-filtering or
> content-based features."

### §2.1 13-dil kapsama — resmi kaynak dil blokları (Faz 2 · ekleme 2026-10-14)

> Kural: her blok yalnız `webfetch` ile **HTTP 200** ile açılmış resmi kaynaktan türetildi;
> 0 mevcut satır değiştirilmedi · 0 silme · her blok `[ORİJİNAL ÖRNEK · derlenmedi]` + `2026-10-14` taşır.

**Python — scikit-learn çapraz doğrulama (ranking/model değerlendirmesi kalıbı)** ·
https://scikit-learn.org/stable/modules/cross_validation.html (doğrulandı 2026-10-14 · HTTP 200) ·
[ORİJİNAL ÖRNEK · derlenmedi] · 2026-10-14

> "The simplest way to use cross-validation is to call the `cross_val_score` helper function
> on the estimator and the dataset." (User Guide §3.1.1)

```python
from sklearn.model_selection import cross_val_score
from sklearn import datasets
from sklearn import svm

X, y = datasets.load_iris(return_X_y=True)
clf = svm.SVC(kernel='linear', C=1, random_state=42)
scores = cross_val_score(clf, X, y, cv=5)
print("%0.2f accuracy with a standard deviation of %0.2f" % (scores.mean(), scores.std()))
```

**C# — ML.NET MatrixFactorization öneri (`Microsoft.ML` + `Microsoft.ML.Recommender`)** ·
https://learn.microsoft.com/en-us/dotnet/machine-learning/tutorials/movie-recommendation (doğrulandı 2026-10-14 · HTTP 200) ·
[ORİJİNAL ÖRNEK · derlenmedi] · 2026-10-14

```csharp
using Microsoft.ML;
using Microsoft.ML.Trainers;

MLContext mlContext = new MLContext();

IEstimator<ITransformer> estimator = mlContext.Transforms.Conversion.MapValueToKey(outputColumnName: "userIdEncoded", inputColumnName: "userId")
    .Append(mlContext.Transforms.Conversion.MapValueToKey(outputColumnName: "movieIdEncoded", inputColumnName: "movieId"));

var options = new MatrixFactorizationTrainer.Options
{
    MatrixColumnIndexColumnName = "userIdEncoded",
    MatrixRowIndexColumnName = "movieIdEncoded",
    LabelColumnName = "Label",
    NumberOfIterations = 20,
    ApproximationRank = 100
};

var trainerEstimator = estimator.Append(mlContext.Recommendation().Trainers.MatrixFactorization(options));
ITransformer model = trainerEstimator.Fit(trainingDataView);

// Tek örnek tahmin — doküman eşik kuralı: tahmini puan > 3.5 ise öner
var predictionEngine = mlContext.Model.CreatePredictionEngine<MovieRating, MovieRatingPrediction>(model);
var testInput = new MovieRating { userId = 6, movieId = 10 };
var movieRatingPrediction = predictionEngine.Predict(testInput);

if (Math.Round(movieRatingPrediction.Score, 1) > 3.5)
{
    Console.WriteLine("Movie " + testInput.movieId + " is recommended for user " + testInput.userId);
}
```

**Java — Apache Spark MLlib ALS (recommender / collaborative filtering)** ·
https://spark.apache.org/docs/latest/ml-collaborative-filtering.html (doğrulandı 2026-10-14 · HTTP 200) ·
[ORİJİNAL ÖRNEK · derlenmedi] · 2026-10-14

> Resmi örneğin Java API bölümünden alınmış alıntı — `Rating` sınıfı ve `parseRating` yardımcı
> metodu dokümantasyonda bu bloktan önce tanımlanır:

```java
import org.apache.spark.ml.evaluation.RegressionEvaluator;
import org.apache.spark.ml.recommendation.ALS;
import org.apache.spark.ml.recommendation.ALSModel;

Dataset<Row>[] splits = ratings.randomSplit(new double[]{0.8, 0.2});
Dataset<Row> training = splits[0];
Dataset<Row> test = splits[1];

// Build the recommendation model using ALS on the training data
ALS als = new ALS()
  .setMaxIter(5)
  .setRegParam(0.01)
  .setUserCol("userId")
  .setItemCol("movieId")
  .setRatingCol("rating");
ALSModel model = als.fit(training);

// Evaluate the model by computing the RMSE on the test data
// Note we set cold start strategy to 'drop' to ensure we don't get NaN evaluation metrics
model.setColdStartStrategy("drop");
Dataset<Row> predictions = model.transform(test);

RegressionEvaluator evaluator = new RegressionEvaluator()
  .setMetricName("rmse")
  .setLabelCol("rating")
  .setPredictionCol("prediction");
double rmse = evaluator.evaluate(predictions);
System.out.println("Root-mean-square error = " + rmse);

// Generate top 10 movie recommendations for each user
Dataset<Row> userRecs = model.recommendForAllUsers(10);
```

**JavaScript / Node.js — `onnxruntime-node` ile model inference (ranking skoru çıkarımı)** ·
https://onnxruntime.ai/docs/get-started/with-javascript/node.html (doğrulandı 2026-10-14 · HTTP 200) —
kod örneği: https://raw.githubusercontent.com/microsoft/onnxruntime-inference-examples/main/js/quick-start_onnxruntime-node/index.js
(aynı sayfanın "Quick Start" bağlantısı, Microsoft · MIT · HTTP 200) ·
[ORİJİNAL ÖRNEK · derlenmedi] · 2026-10-14

```javascript
// Copyright (c) Microsoft Corporation.
// Licensed under the MIT license.

const ort = require('onnxruntime-node');

// use an async context to call onnxruntime functions.
async function main() {
    try {
        // create a new session and load the specific model.
        const session = await ort.InferenceSession.create('./model.onnx');

        // prepare inputs. a tensor need its corresponding TypedArray as data
        const dataA = Float32Array.from([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]);
        const dataB = Float32Array.from([10, 20, 30, 40, 50, 60, 70, 80, 90, 100, 110, 120]);
        const tensorA = new ort.Tensor('float32', dataA, [3, 4]);
        const tensorB = new ort.Tensor('float32', dataB, [4, 3]);

        // prepare feeds. use model input names as keys.
        const feeds = { a: tensorA, b: tensorB };

        // feed inputs and run
        const results = await session.run(feeds);

        // read from results
        const dataC = results.c.data;
        console.log(`data of result tensor 'c': ${dataC}`);

        await session.release()
    } catch (e) {
        console.error(`failed to inference ONNX model: ${e}.`);
    }
}

main();
```

**Go — gonum/stat ile ranking ROC/AUC değerlendirmesi** ·
https://pkg.go.dev/gonum.org/v1/gonum/stat (doğrulandı 2026-10-14 · HTTP 200 · v0.17.0 · BSD-3-Clause) —
kod örneği: https://raw.githubusercontent.com/gonum/gonum/v0.17.0/stat/roc_example_test.go (HTTP 200) ·
[ORİJİNAL ÖRNEK · derlenmedi] · 2026-10-14

> Not: bu blok doğrulanmış resmi gonum örneğiyle sınırlıdır; "REST ranking servisi" sarmalayıcısı
> için HTTP 200 ile doğrulanmış resmi Go örnek kaynağı bulunamadığından uydurulmadı.

```go
package stat_test

import (
	"fmt"

	"gonum.org/v1/gonum/integrate"
	"gonum.org/v1/gonum/stat"
)

func ExampleROC_aUC_unweighted() {
	y := []float64{0.1, 0.35, 0.4, 0.8}
	classes := []bool{true, false, true, false}

	tpr, fpr, _ := stat.ROC(nil, y, classes, nil)

	// Compute Area Under Curve.
	auc := integrate.Trapezoidal(fpr, tpr)
	fmt.Printf("true  positive rate: %v\n", tpr)
	fmt.Printf("false positive rate: %v\n", fpr)
	fmt.Printf("auc: %v\n", auc)

	// Output:
	// true  positive rate: [0 0 0.5 0.5 1]
	// false positive rate: [0 0.5 0.5 1 1]
	// auc: 0.25
}
```

**Bash — MLflow CLI ile deney/ranking pipeline'ı** ·
https://mlflow.org/docs/latest/cli.html (doğrulandı 2026-10-14 · HTTP 200 · MLflow 3.17.1 CLI) ·
[ORİJİNAL ÖRNEK · derlenmedi] · 2026-10-14

> "It is advisable to set the `MLFLOW_TRACKING_URI` environment variable by default, as the CLI
> does not automatically connect to a tracking server."

```bash
# MLflow CLI pipeline — tracking URI ortam değişkeni (CLI referansı: "set the MLFLOW_TRACKING_URI environment variable")
export MLFLOW_TRACKING_URI=http://localhost:5000

# 1) Deney oluştur — usage: mlflow experiments create [OPTIONS] · -n zorunlu
mlflow experiments create -n recommendation-ranking

# 2) Projeyi çalıştır — usage: mlflow run [OPTIONS] URI · -e [default: main] · -P NAME=VALUE
#    (-P parametre adları projenin MLproject tanımına bağlıdır)
mlflow run . -e main --experiment-name recommendation-ranking -P rank=100 -P iterations=20

# 3) Deneydeki run'ları CSV'e aktar — usage: mlflow experiments csv [OPTIONS] · -x zorunlu · -o çıktı
mlflow experiments csv -x 1 -o runs.csv
```

## §3 Uygulama notu

- Repo'daki 0.6/0.4 ağırlığı **hardcoded** — benchmark'a bağlanan bir kanıt yok (kapsam §5/R3).
- Collaborative kolu boşken pratikte öneriler yalnız content-based skorlarla döner.

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `recommendation-kapsam.md` | bu dalın rolü, içinde/dışında, sınır soruları |
| `recommendation-dogrulama.md` | iddia → URL/dosya kanıt tablosu |
| `../index.md` · `../ai-master.md` §1/§3 | üst düğüm künyesi + çocuk tablosu (v2 satır 261) |

---

## §5 Kapanış Derinliği — Neva Engine öneri kişiselleştirme C++ örnek turu (2026-10-15)

### §5.0 Kanıt disiplini

Repo ölçümü (2026-10-15): `*.cpp` / `*.h` = **0 dosya** → aşağıdaki C++ blokları **vault spesifikasyon alıntılarıdır**, derlenmiş kod değildir; her biri `Dosya: … [Satır: n]` ile işaretlenmiştir.

### §5.1 Örnek RC-1 — Kullanıcı tercih modeli (öneri girdisi, tam okuma)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` [Satır: 89–107].

```json
{
  "user_id": 123,
  "listening_history": [
    {"song_id": 1, "genre": "arabesk", "eq_profile": "warm_vocal", "skip": false},
    {"song_id": 2, "genre": "pop", "eq_profile": "modern_bright", "skip": true}
  ],
  "preferred_curve": {
    "60Hz":  2.8,
    "250Hz": 2.5,
    "1kHz":  0.5,
    "4kHz":  3.5,
    "12kHz": 1.0
  },
  "skip_patterns": {
    "too_bright": 3,
    "too_muddy": 2
  }
}
```

**Öneri çıkarımları:**

1. `listening_history` satırları hem **etkileşim kaydı** hem **EQ tercih kaydı**dur → öneri motoru bu iki sinyali ayrı ayrı okuyabilir (genre sinyali content-based · skip sinyali negatif geri bildirim).
2. `song_id` üzerinden erişim → öneri motorunun "şarkı kimliği ↔ tercih" eşlemesi zorunludur.
3. Repo PHP'sinde `getCollaborativeScores(?string $userId, array $history)` (Dosya: `shared/src/AI/AIEngine.php` [Satır: 209]) — `?string $userId` nullable'dır: oturumsuz (anonim) öneri mümkündür. C++ spec'te `user_id: 123` (nullable değil) → iki yüzeyde anonimlik sözleşmesi **farklıdır**.

### §5.2 Örnek RC-2 — Online öğrenme (öneri geri bildirimi, tam blok)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` [Satır: 112–120].

```text
Her dinleme seansında:
  1. Skip tespit → EQ profilini not al
  2. Şarkı sonuna kadar dinleme → EQ profilini güçlendir
  3. Volume değişikliği → loudness preference
  4. Manuel EQ override → model güncelle (high weight)

Model: Incremental gradient descent with forgetting factor (α = 0.95)
```

**Öneri çıkarımları:**

1. Sinyal 1 (skip) ve 2 (tam dinleme) doğrudan öneri kalitesi geri bildirimidir; 3 (volume) ve 4 (override) EQ kişiselleştirme sinyalidir → aynı döngü iki K006 dalını (recommendation · personalization) besler.
2. `α = 0.95` unutma faktörü → eski tercihler hızla unutulur; öneri listesi tazelenir. K006'da α kararı bir ADR maddesidir (H2 kapısı).
3. Repo PHP'sinde karşılığı yoktur (grep 2026-10-15: `α\|forgetting\|gradient` = 0 eşleşme `shared/src/AI/` altında) → öğrenme döngüsü PHP yüzeyinde **yoktur**; K006 backlog madde adayı.

### §5.3 Örnek RC-3 — Tür bazlı referanslar (öneri açıklanabilirliği, tam tablo)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` [Satır: 66–81] — 14 satır × 5 bant.

| Tür | 60Hz | 250Hz | 1kHz | 4kHz | 12kHz | Eğri |
|---|---|---|---|---|---|---|
| Arabesk | +3 | +2 | +1 | +3 | +1 | Warm vocal |
| Pop | +4 | 0 | +1 | +2 | +3 | Modern bright |
| Rock | +3 | −1 | −2 | +3 | +2 | Mid scoop |
| Electronic/Dans | +6 | +1 | 0 | +1 | +4 | Bass heavy |
| Jazz | +2 | +1 | 0 | −1 | −2 | Warm mellow |
| Classical | 0 | 0 | 0 | 0 | −1 | Flat natural |
| HipHop | +5 | +1 | +1 | +2 | +2 | Bass boosted |
| Metal | +2 | 0 | −3 | +4 | +3 | Aggressive |
| Folk | +1 | +2 | +1 | 0 | 0 | Natural warm |
| R&B | +4 | +2 | +1 | +1 | +2 | Smooth bass |
| Gamer FPS | −2 | +1 | +3 | +4 | +2 | Spatial clarity |
| Gamer RPG | +3 | +2 | +1 | +2 | +3 | Cinematic |
| Film/Movie | +4 | +2 | +1 | +2 | +1 | Theater |
| Voice/Podcast | −3 | 0 | +3 | +2 | −1 | Voice focus |

**Öneri çıkarımları:**

1. Son 4 satır (Gamer FPS · Gamer RPG · Film · Voice/Podcast) **tür değil, kullanım senaryosu**dur → öneri motoru tür bazlı liste üretiyorsa bu satırlar ayrı bir "senaryo" boyutu ister (çok-boyutlu etiketleme).
2. `eq_profile` adları (`warm_vocal`, `modern_bright`) bu tablonun eğri adlarıyla eşleşir → öneri açıklaması "neden bu EQ?" sorusuna cevap verir (explainability katmanı).

### §5.4 Örnek RC-4 — Job kuyruğu ile öneri bildirimi (batch köprüsü)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 461–497] — job API'si:

```json
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
```

**Öneri çıkarımları:**

1. `ai.detectGenre` metodu job API'sinde tanımlıdır ([Satır: 493–496]) → tespit sonucu öneri motoruna **event** olarak akabilir (async tür etiketi).
2. Job API'si JSON-RPC benzeri `id`/`method`/`params` şeması kullanır → öneri API'si için şema adayı.
3. Repo PHP'sinde `getRecommendations(array $context, int $limit = 20)` (Dosya: AIEngine.php [Satır: 49]) `limit` varsayılanı 20'dir; `MAX_RECOMMENDATIONS = 20` sabiti [Satır: 30] ile birebir tutarlı; `MIN_SCORE = 0.7` [Satır: 31] filtre eşiği, `DIVERSITY_TARGET = 0.30` [Satır: 32] çeşitlilik oranı — üç sabit PHP'de **kanıtlanmış** tek kaynaktır.

### §5.5 Örnek RC-5 — Öneri gate'leri (K006 için)

| # | Gate | Eşik | Kaynak |
|---|---|---|---|
| RC-G1 | Öneri sayısı | ≤ 20 (MAX_RECOMMENDATIONS) | AIEngine.php [Satır: 30] |
| RC-G2 | Min skor | ≥ 0.7 (MIN_SCORE) | AIEngine.php [Satır: 31] |
| RC-G3 | Çeşitlilik oranı | ≥ 0.30 (DIVERSITY_TARGET) | AIEngine.php [Satır: 32] |
| RC-G4 | Tür güven eşiği | confidence ≥ 0.6 (auto-EQ) | ai-models.md [Satır: 362] |
| RC-G5 | Unutma faktörü | α = 0.95 | eq-ai-auto.md [Satır: 119] |

Bu turda **hiçbiri çalıştırılmadı** → `../yapay-zeka-dogrulama.md` §7'ye taşındı.

### §5.6 §5 tur kapanış kaydı

- Eklenen: 2 tam okuma (RC-1 · RC-2) + 14 satır tablo + 1 job API bloğu + 5 gate.
- Uydurma identifier: 0 · ölçü uydurma: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 2 (anonimlik sözleşmesi farkı · öğrenme döngüsü PHP'de yok).

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12

---

## §6 Kapanış Derinliği — PHP öneri hattı tam okuma + C++ eşleme (2026-10-15 kapanış turu)

### §6.0 Kanıt disiplini (bu bölüm için)

- Repo PHP satırları bu turda **dosya dosya okunarak** alınmıştır (tahmin yok).
- Repo'da `*.cpp` / `*.h` = **0 dosya** (2026-10-15 ölçümü) → §6.5'teki C++ bloğu
  **mimari şartnamedir**, repo kodu değildir.
- Çalıştırılan gate: **0** → `../yapay-zeka-dogrulama.md` §7'ye taşındı.

### §6.1 Örnek RC-6 — `getRecommendations` 6 adım tam okuma (satır satır)

Dosya: `shared/src/AI/AIEngine.php` [Satır: 49–77]

| Satır | Kod gerçeği | K006 yorumu |
|---|---|---|
| 49 | `getRecommendations(array $context, int $limit = 20)` | imza: context dizisi + limit |
| 51 | `$limit = min($limit, self::MAX_RECOMMENDATIONS)` | limit asla 20'yi aşmaz |
| 52–56 | alanlar: `user_id` · `session_id` · `mood` · `genre` · `history` | 5 zorunlu-öncelikli bağlam alanı |
| 59 | `getCollaborativeScores($userId, $history)` | adım 1 — collaborative |
| 62 | `getContentBasedScores($context)` | adım 2 — content-based |
| 65 | `combineScores(..., 0.6, 0.4)` | adım 3 — **hibrit ağırlık 0.6/0.4** |
| 68 | `applyDiversityFilter($hybridScores, $genre)` | adım 4 — çeşitlilik filtresi |
| 71 | `fn($item) => $item['score'] >= self::MIN_SCORE` | adım 5 — min skor 0.7 |
| 74 | `usort(... $b['score'] <=> $a['score'])` | adım 6 — azalan sıralama |
| 76 | `array_slice(..., 0, $limit)` | limit uygulanır |

Altı adımın sırası **kod gerçeğidir**; hangi adımın gerçekten skor ürettiği ayrı maddedir (§6.2).

### §6.2 Örnek RC-7 — Boş skor gerçeği (hibrit hattın fiili durumu)

Dosya: `shared/src/AI/AIEngine.php` [Satır: 209–235 · 243–245]

| Parça | Satır | Kod gerçeği |
|---|---|---|
| `getCollaborativeScores` | 211–213 | `$userId === null` → `return []` |
| `getCollaborativeScores` | 216–217 | yorum: "matrix factorization (ALS) kullanılacak" → **`return []`** |
| `getContentBasedScores` | 225–230 | hedef özellikler: `bpm` · `energy` · `danceability` · `genre` |
| `getContentBasedScores` | 233–234 | yorum: "cosine similarity ile skorla" → **`return []`** |
| `combineScores` | 243–245 | imza: `combineScores(array $collaborative, array $content, float $collabWeight, float $contentWeight)` |

**Sonuç (bu vault'un okuması):** iki girdi de boş olduğunda birleştirme boş dizi üretir;
adım 4–6 (çeşitlilik · min skor · sıralama) boş girdiye uygulanır ve sonuç **boş kalır**.
Bu gözlem kök `yapay-zeka-kararlar.md` **K-08** ile aynıdır ("öneri hattı fiilen
content-based'e düşüyor" — daha kesin ifade: her iki kaynak da şu an boş dönüyor ve
dönüşler kodda **yorum olarak** taahhüt edilmiş). Yeni risk iddiası değildir, mevcut
K-08'in satır derinleştirilmesidir.

### §6.3 Örnek RC-8 — Workflow zinciri 6 adım tam okuma

Dosya: `shared/src/AI/AIWorkflow.php` [Satır: 49–91]

| Adım | Satır | Kod gerçeği |
|---|---|---|
| 0 | 51–52 | `microtime(true)` başlangıcı · `session_id` (varsayılan `anonymous`) |
| 1 | 55–56 | `memory->getSession($sessionId, 'preferences')` + request ile merge |
| 2 | 59 | `orchestrator->dispatchTask('recommendation', $context, 'medium')` |
| 3 | 62–63 | sorgu = `mood + genre` → `knowledge->search($query, 5)` |
| 4 | 66–72 | `promptEngine->generatePrompt('recommendation', ...)` — 5 alan: mood · genre · energy · history · limit |
| 5 | 75 | `engine->getRecommendations($context, $context['limit'] ?? 10)` |
| 6 | 78 | `memory->setSession(..., 'last_recommendations', ..., 3600)` — **1 saat TTL** |
| Çıkış | 82–90 | `recommendations` + `metadata` (task_id · prompt_tokens · knowledge_hits) + `execution_time` |

**Dikkat (çelişki kaydı):** workflow `limit ?? 10` derken motor varsayılanı 20'dir
(AIEngine.php [Satır: 49] · sabit `MAX_RECOMMENDATIONS = 20` [Satır: 30]) → varsayılan
farkı 10/20; ikisi de geçerli, ancak K006 gate'inde **hangi varsayılanın ürün olduğu**
açık değildir → `⚠️ VERIFICATION REQUIRED` (yeni iddia değil, §5.6'daki 2 maddenin
tamamlayıcısı).

### §6.4 Örnek RC-9 — Dispatch ve RAG sözleşmeleri

Dosya: `shared/src/AI/AIOrchestrator.php` [Satır: 71–94] · `shared/src/AI/KnowledgeBase.php` [Satır: 72–91]

| Parça | Satır | Kod gerçeği |
|---|---|---|
| `dispatchTask` imzası | 71 | `dispatchTask(string $type, array $params, string $priority = 'medium'): string` |
| bilinmeyen tip | 73–75 | `TASK_TYPES` yoksa `InvalidArgumentException` |
| görev kaydı | 80–91 | 12 alan: id · type · status · priority · agent · params · result · error · created_at · completed_at (+2) |
| yürütme | 94 | `executeTask($taskId)` — öncelik ağırlıklı, tek çağrı |
| `ragQuery` imzası | 72 | `ragQuery(string $query, array $context = []): array` |
| retrieval | 75 | `search($query, 5, $context['filters'] ?? [])` — 5 belge |
| generation | 81 | yorum: "LLM ile yanıt üret (şimdilik placeholder)" |
| yanıt | 83–90 | `answer` · `sources` (id · title · score) · `confidence` |

### §6.5 Örnek RC-10 — PHP → C++ eşleme şartnamesi (spec, repo-cpp YOK)

> Aşağıdaki blok **mimari şartnamedir**; repo'da derlenebilir C++ bulunmaz
> (2026-10-15: `*.cpp` + `*.h` = 0). Blok, §6.1–§6.4'teki PHP sözleşmelerinin
> Neva Engine tarafındaki karşılığını **tanımlar**, mevcut kodu temsil etmez.

```cpp
// K006 — öneri hattı C++ sözleşmesi (spec)
// Karşılığı: shared/src/AI/AIEngine.php [Satır: 49–77]
struct RecommendationContext {
  std::optional<std::string> user_id;    // AIEngine.php L52
  std::optional<std::string> session_id; // L53
  std::optional<std::string> mood;       // L54
  std::optional<std::string> genre;      // L55
  std::vector<std::string> history;      // L56
};

struct RecommendationItem { std::string id; double score; };

// K-08 gerçeği: iki kaynak şu an boş dönüyor (L217 · L234) —
// C++ tarafı da boş vektör dönerse hibrit sonuç BOŞTUR; gate buna bakar.
std::vector<RecommendationItem> combine(
    const std::vector<RecommendationItem>& collab,  // L243
    const std::vector<RecommendationItem>& content,
    double collab_weight,   // 0.6 — L65
    double content_weight); // 0.4 — L65
```

### §6.6 Örnek RC-11 — Yeni gate'ler (RC-G6…RC-G10)

| # | Gate | Eşik | Kaynak |
|---|---|---|---|
| RC-G6 | Hibrit ağırlık | 0.6 / 0.4 sabit (kod değişmedikçe) | AIEngine.php [Satır: 65] |
| RC-G7 | Workflow TTL | `last_recommendations` TTL = 3600 sn | AIWorkflow.php [Satır: 78] |
| RC-G8 | Knowledge araması | `search(query, 5)` → 5 sonuç üst sınırı | AIWorkflow.php [Satır: 63] · KnowledgeBase.php [Satır: 75] |
| RC-G9 | Limit tutarlılığı | 10 (workflow) vs 20 (motor varsayılanı) → ürün kararı zorunlu | AIWorkflow.php [Satır: 75] vs AIEngine.php [Satır: 49] |
| RC-G10 | RAG confidence alanı | `confidence` alanı her yanıtta mevcut | KnowledgeBase.php [Satır: 89] |

Bu turda **hiçbiri çalıştırılmadı** → `../yapay-zeka-dogrulama.md` §7'ye taşındı.

### §6.7 §6 tur kapanış kaydı

- Eklenen: 4 tam okuma (RC-6 · RC-7 · RC-8 · RC-9) + 1 spec bloğu + 5 gate + 2 tablo başlığı.
- Toplam satır alıntısı: AIEngine 49–77 · 209–235 · 243–245 · AIWorkflow 49–91 ·
  AIOrchestrator 71–94 · KnowledgeBase 72–91 — hepsi bu turda okunarak doğrulandı.
- Uydurma identifier: 0 · ölçü uydurma: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 1 (limit 10/20 varsayılan çelişkisi).

---

**Authority:** Master-Orchestrator kapanış turu · **Status:** active · **Updated:** 2026-10-15
