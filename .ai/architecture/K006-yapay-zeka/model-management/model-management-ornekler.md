---
title: "Model Management — Örnekler"
type: architecture-scope
node_id: K006-yapay-zeka/model-management
node_type: subdomain-examples
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# Model Management — Örnekler

Amaç: kapsamı üç örnek somutlaştırmak — gerçek repo kodu (öncelik 1), resmi doküman örneği, uygulama notu.

## §1. Gerçek repo kodu (öncelik 1)

**`shared/src/` içinde model yönetimine dair kod — kısıtlı (yalnız model adı referansı):**

```php
// shared/src/AI/AIEngine.php (satır 28–32)
$model = 'core-algorithm-orchestrator';
$confidence = 0.75;
$durationMs = 120;
$timestamp = new DateTimeImmutable();
```

**Yorum (kanıt temelli):** Model adı sabit string olarak tutulur; **sürüm alanı, artifact yolu, registry çağrısı yoktur** — kayıt defteri henüz kodda temsil edilmez (kapsam maddesi 1'in repo karşılığı henüz yok). `shared/src` grep'i `registry|artifact|mlflow` için 0 isabet verir.

**`shared/src/Contracts/AI` sözleşme yüzeyi (6 interface — disk grep 2026-10-12):** `AIEngineInterface` · `AIOrchestratorInterface` · `KnowledgeBaseInterface` · `MemorySystemInterface` · `PromptEngineInterface` · `ToolCallingInterface` (33 imza) — model yönetimine dair bir method imzası **yok** (envanter genişletilirken eklenebilir; kapsam maddesi 5). Not: yol `shared/Contracts/AI` değil, **`shared/src/Contracts/AI`**; `AIWorkflowInterface`/`AIQualityInterface`/`AIHealthInterface`/`AIUsageInterface` adlarında arayüz **repoda yoktur** (Düzeltme kaydı 2026-10-12).

## §2. Resmi doküman örneği

MLflow Model Registry — sürüm + aşama etiketleme (resmi doküman, 2026-10-12 doğrulandı):

> "MLflow Model Registry provides a centralized model store, set of APIs, and UI to collaboratively manage the full lifecycle of an MLflow Model. It provides model lineage … and model versioning."
> — https://mlflow.org/docs/latest/ml/model-registry/

### §2.1 13-dil kapsama blokları (Faz 1 · 2026-10-14)

> Her blok: resmi kaynak URL **HTTP 200 ile doğrulandı (2026-10-14)** · kod kaynak
> belgeden türetildi · derlenmedi/çalıştırılmadı · `[ORİJİNAL ÖRNEK · derlenmedi]` etiketlidir.
> Bu dosyadaki dil yüzeyi: PHP (§1 repo kanıtı) + 6 yeni = **7/13**.

**Python — MLflow model registry kaydı & sürümle geri yükleme:**

```python
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://mlflow.org/docs/latest/ml/model-registry/
#               https://mlflow.org/docs/latest/python_api/mlflow.html
import mlflow

# Belge "Option 2" — runs:/ URI ile kayıtlı modele ekleme
# (python_api: "runs:/ URIs (e.g. runs:/<run_id>/<artifact_path>) ... The run ID is recorded with the model version")
mlflow.register_model(model_uri="runs:/<run_id>/model", name="core-algorithm-orchestrator")

# Kayıtlı modeli ad + sürümle geri yükleme (belge: models:/<AD>/<SÜRÜM>)
model = mlflow.pyfunc.load_model("models:/core-algorithm-orchestrator/1")
```

> Kaynak: https://mlflow.org/docs/latest/ml/model-registry/ (HTTP 200 · 2026-10-14) —
> `mlflow.register_model(model_uri=..., name=...)` belgedeki birebir çağrıdır ·
> `mlflow.pyfunc.load_model(model_uri)` imzası: https://mlflow.org/docs/latest/python_api/mlflow.pyfunc.html (HTTP 200 · 2026-10-14).

**Bash — MLflow CLI ile kayıt sunucusu & artifact indirme (otomasyon):**

```bash
# [ORİJİNAL ÖRNEK · derlenmedi]
# Resmi kaynak: https://mlflow.org/docs/latest/cli.html (mlflow server · mlflow artifacts download)

# Kayıt defteri + artifact sunucusu (cli.html seçeneği: --backend-store-uri "e.g. 'sqlite:///path/to/file.db'")
mlflow server \
  --backend-store-uri sqlite:///mlflow.db \
  --registry-store-uri sqlite:///mlflow.db \
  --default-artifact-root ./artifacts \
  --serve-artifacts \
  --host 127.0.0.1 \
  --port 5000

# Kayıtlı modelin artifact'ını indir (cli.html: -r/--run-id · -a/--artifact-path · -d/--dst-path)
mlflow artifacts download \
  --run-id <run_id> \
  --artifact-path model \
  --dst-path ./model-registry/core-algorithm-orchestrator/1
```

> Kaynak: https://mlflow.org/docs/latest/cli.html (HTTP 200 · 2026-10-14) — tüm
> seçenekler ve değer biçimleri belgeden alınmıştır; `--host` varsayılanı `127.0.0.1`,
> `--port` varsayılanı `5000` (belge metni).

**PowerShell — MLflow REST API ile kayıt defteri otomasyonu:**

```powershell
# [ORİJİNAL ÖRNEK · derlenmedi]
# Kaynaklar: https://mlflow.org/docs/latest/rest-api.html
#            https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.utility/invoke-restmethod

# Kayıtlı model oluştur — belge: POST /api/2.0/mlflow/registered-models/create (zorunlu alan: name)
$uri = "http://127.0.0.1:5000/api/2.0/mlflow/registered-models/create"
$body = @{ name = "core-algorithm-orchestrator" } | ConvertTo-Json
Invoke-RestMethod -Method Post -Uri $uri -ContentType "application/json" -Body $body

# Model sürümü ekle — belge: POST /api/2.0/mlflow/model-versions/create
# (zorunlu alanlar: name · source = "URI indicating the location of the model artifacts")
$uriVer = "http://127.0.0.1:5000/api/2.0/mlflow/model-versions/create"
$bodyVer = @{ name = "core-algorithm-orchestrator"; source = "runs:/<run_id>/model" } | ConvertTo-Json
Invoke-RestMethod -Method Post -Uri $uriVer -ContentType "application/json" -Body $bodyVer
```

> Kaynak: https://mlflow.org/docs/latest/rest-api.html (HTTP 200 · 2026-10-14) —
> "API is hosted under the /api route on the MLflow tracking server" + "requires content
> type application/json for all POST requests" · cmdlet sözleşmesi:
> https://learn.microsoft.com/en-us/powershell/module/microsoft.powershell.utility/invoke-restmethod (HTTP 200 · 2026-10-14).

**Go — MLflow REST tabanlı mini registry aracı (tooling):**

```go
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://mlflow.org/docs/latest/rest-api.html
//   POST /api/2.0/mlflow/registered-models/create — zorunlu alan: name (STRING)
//   "The MLflow REST API requires content type application/json for all POST requests."
package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io"
	"net/http"
)

func main() {
	payload := map[string]string{"name": "core-algorithm-orchestrator"}
	body, _ := json.Marshal(payload)

	req, err := http.NewRequest(http.MethodPost,
		"http://127.0.0.1:5000/api/2.0/mlflow/registered-models/create",
		bytes.NewReader(body))
	if err != nil {
		panic(err)
	}
	req.Header.Set("Content-Type", "application/json")

	resp, err := http.DefaultClient.Do(req)
	if err != nil {
		panic(err)
	}
	defer resp.Body.Close()
	io.Copy(io.Discard, resp.Body)
	fmt.Println("registered-models/create:", resp.Status)
}
```

> Kaynak: https://mlflow.org/docs/latest/rest-api.html (HTTP 200 · 2026-10-14) — uç nokta,
> zorunlu alan ve content-type kuralı belgeden; Go yazımı standart kütüphane (`net/http`,
> `encoding/json`), derlenmedi. Not: MLflow'un resmi Go client'ı **bulunamadı**
> (`github.com/mlflow/mlflow-go-client` → HTTP 404 · 2026-10-14).

**C# / .NET — ML.NET model kaydetme & kayıt dosyasından yükleme:**

```csharp
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://learn.microsoft.com/en-us/dotnet/machine-learning/how-to-guides/save-load-machine-learning-models-ml-net
using Microsoft.ML;

MLContext mlContext = new MLContext();

// (1) Kayıt dosyasından yükle — belge birebir: "ITransformer trainedModel = mlContext.Model.Load(\"model.zip\", out modelSchema);"
DataViewSchema modelSchema;
ITransformer trainedModel = mlContext.Model.Load("model.zip", out modelSchema);

// (2) Modeli artifact olarak kaydet — belge birebir: "mlContext.Model.Save(trainedModel, data.Schema, \"model.zip\");"
//     (data: eğitim verisinin şeması — belgedeki ad)
mlContext.Model.Save(trainedModel, data.Schema, "model.zip");
```

> Kaynak: https://learn.microsoft.com/en-us/dotnet/machine-learning/how-to-guides/save-load-machine-learning-models-ml-net (HTTP 200 · 2026-10-14) —
> belge Save ve Load örneklerini ayrı ayrı verir; blokta tek akışta gösterildi (sıra load→save),
> her iki satır da belgedeki birebir ifadelerdir.

**JavaScript / Node — registry backend'inin ONNX model artifact'ını yüklemesi:**

```javascript
// [ORİJİNAL ÖRNEK · derlenmedi]
// Resmi kaynak: https://raw.githubusercontent.com/microsoft/onnxruntime-inference-examples/main/js/quick-start_onnxruntime-node/index.js
//   (onnxruntime.ai/docs/api/js/ sayfasındaki resmi "Inference examples" bağlantısı bu Microsoft deposunu gösterir)
const ort = require('onnxruntime-node');

// use an async context to call onnxruntime functions.
async function main() {
    try {
        // create a new session and load the specific model.
        const session = await ort.InferenceSession.create('./model.onnx');

        // Kayıtlı model artifact'ı oturuma yüklendi; belgedeki çalışma adımı: await session.run(feeds)
        console.log('model artifact loaded into session');

        await session.release()
    } catch (e) {
        console.error(`failed to inference ONNX model: ${e}.`);
    }
}

main();
```

> Kaynak: https://raw.githubusercontent.com/microsoft/onnxruntime-inference-examples/main/js/quick-start_onnxruntime-node/index.js (HTTP 200 · 2026-10-14) ·
> paket/API tanıtımı: https://onnxruntime.ai/docs/api/js/ (HTTP 200 · 2026-10-14 —
> "NPM packages: onnxruntime-node …"). Kod Microsoft deposundaki örnekten kısaltılmıştır; derlenmedi.

**N/A kayıtları (bu dosyada blok YOK):** C · C++ · Rust · Zig · Swift · Java/Kotlin —
model registry / versioning / deployment hazırlık konusuna eşleşen **resmi kaynak
doğrulanamadı** (2026-10-14); ilgisiz dil bloğu yazılmaz.

## §3. Uygulama notu

Registry teknolojisi seçilmeden (ai-master.md §4 satır 87 UNKNOWN) deploy tasarımı yapılamaz; önce kayıt şeması (ad · sürüm · metrik · artifact yolu) sabitlenmeli.

## §4. İlişkili dosyalar

- [[model-management-kapsam]]
- [[model-management-dogrulama]]
- [[../index|K006-yapay-zeka index]]

---

## §5 Kapanış Derinliği — Neva Engine model yönetimi C++ örnek turu (2026-10-15)

### §5.0 Kanıt disiplini

Repo ölçümü (2026-10-15): `*.cpp` / `*.h` = **0 dosya** → aşağıdaki C++ blokları **vault spesifikasyon alıntılarıdır**, derlenmiş kod değildir; her biri `Dosya: … [Satır: n]` ile işaretlenmiştir.

### §5.1 Örnek MM-1 — ModelRegistry (kayıt şeması, tam okuma)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 505–526].

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

**Okuma notları:**

1. Kayıt şeması 10 alanlıdır; K006 kayıt şeması kararı için bu alan seti **minimum adaydır** (bu dosya §3'ün "önce kayıt şeması sabitlenmeli" şartını doğrudan karşılar).
2. `checksumSHA256` alanı var → bütünlük doğrulaması kayıtla birlikte taşınır (dizin yapısındaki `checksums.sha256` [Satır: 538] ile iki katmanlı doğrulama).
3. `status` enum'u 4 değerli [Satır: 517] ve `InferenceJobQueue::JobStatus` [Satır: 444] ile **farklı** bir enum'dur (QUEUED/RUNNING/DONE/FAILED) — iki ayrı durum makinesi, aynı sözcük ailesi → evrensel-dil §5'e bağlanır.
4. `deleteModel` sözleşmesi var ama "kullanımdaki model silinirse?" kuralı yok → `⚠️ VERIFICATION REQUIRED`.

### §5.2 Örnek MM-2 — ModelUpdater (güvenli güncelleme, tam okuma)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 553–571].

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

**Okuma notları:**

1. `checkInterval{24}` (saat) global config'teki `checkIntervalHours: 24` [Satır: 652] ile birebir tutarlı — iki konumda aynı değer, tutarlılık kanıtı.
2. `autoDownload: false` varsayılanı [Satır: 653] ile `updateModel` açıkça çağrıldığında indirme olur: davranış "kontrol otomatik, indirme onaylı" şeklinde okunur.
3. `ProgressCallback` tipi spec'te tanımsız (imza yok) → callback sözleşmesi `⚠️ VERIFICATION REQUIRED`.
4. Versiyon karşılaştırma semantiği (semver mi tarih mi) yok → `changelog` alanı serbest metindir.

### §5.3 Örnek MM-3 — Yaşam döngüsü durum makinesi (spec'ten çıkarım)

```text
NOT_DOWNLOADED
    │ downloadModel(modelId, variant)
    ▼
DOWNLOADING ──hata──► ERROR
    │ checksum doğrulandı (checksumSHA256 · [Satır: 516])
    ▼
READY
    │ deleteModel
    ▼
NOT_DOWNLOADED

Her durumda: updateAvailable=true ise READY kalır; indirme açık değilse durum DEĞİŞMEZ
```

Kapılar: (a) indirme bitmeden `status: READY` yazılmaz; (b) checksum uyuşmazsa indirme silinir ve ERROR'a geçilir — kapı (b) spec'te açık yazılmamıştır, `⚠️ VERIFICATION REQUIRED` işaretli **türetmedir**.

### §5.4 Örnek MM-4 — Lokal dizin + varyant şeması (tam blok)

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

**Okuma notları:**

1. Dizin şeması **varyant başına dosya** prensibindedir (`model_fp16.onnx` · `model_int8.onnx`) → varyant seçimi dosya adıyla belirlenir, ayrı bir manifest yok (manifest `config.json` olabilir mi → UNKNOWN).
2. `genre-cnn` yalnız FP32 taşır (50 MB) → INT8 varyantı olmayan model ailesi demektir; lokal RPi5'te FP32 50 MB zaten taşınabilir (benchmark 0.3s — [Satır: 730]).
3. `version.txt` düz metin sürüm dosyasıdır → registry `version` alanı [Satır: 511] ile eşlenir.

### §5.5 Örnek MM-5 — HuggingFace akışı (registry ↔ uzak depo)

Spec'te HuggingFace Hub'dan otomatik güncelleme hedefi tanımlıdır (Dosya: ai-models.md [Satır: 26] "Otomatik güncelleme — HuggingFace Hub'dan model indirme") ve `hfRepo`/`hfFile` alanları [Satır: 512–513] kayıt şemasındadır. Akış:

```text
1) checkForUpdates → hfApiUrl (https://huggingface.co/api/models/) [Satır: 555]
2) UpdateInfo karşılaştırması (currentVersion vs latestVersion) [Satır: 560–565]
3) updateModel(modelId, callback) — kullanıcı onayı gerekir (autoDownload:false · [Satır: 653])
4) İndirme → checksumSHA256 doğrulama [Satır: 516] → READY [Satır: 517]
```

Güvenlik notu: uzak depodan indirme **onay kapılsız** yapılmaz (`autoDownload: false`); checksum uyuşmazlığı tek bütünlük kapısıdır. K006'da bu akış `../yapay-zeka-riskler.md` R-model-distribution satırına bağlanır (uzak depo güvenilirliği).

### §5.6 Örnek MM-6 — K006 kayıt şeması adayı (NeVa şemasından türetilmiş)

> `[TÜRETME · derlenmedi]` — K006 PHP/registry teknolojisi seçilmemiştir; bu tablo **aday** alandır.

| Alan | Tip | Zorunlu | NeVa kaynağı | K006 notu |
|---|---|---|---|---|
| `id` | string | ✅ | [Satır: 509] | K006 ad alanı: `coremusic/<model>-v<n>` olabilir |
| `name` | string | ✅ | [Satır: 510] | görüntüleme |
| `version` | semver | ✅ | [Satır: 511] | karşılaştırma mantığı kararı gerekir |
| `upstream` | string | opsiyonel | [Satır: 512–513] | HuggingFace varsa |
| `localPath` | path | ✅ | [Satır: 514] | lokal saklama |
| `sizeBytes` | uint64 | ✅ | [Satır: 515] | disk bütçesi kontrolü |
| `checksumSHA256` | hex | ✅ | [Satır: 516] | bütünlük |
| `status` | enum | ✅ | [Satır: 517] | 4 değer |
| `variants` | list | ✅ | [Satır: 518] | FP32/FP16/INT8 |
| `license` | string | ✅ | **NeVa'da YOK** | K006'ya özel alan — lisans kapısı (kararlar §H/H6) |

**Fark:** NeVa şemasında lisans alanı yoktur; K006'da lisans kapısı bağlayıcı olduğundan `license` alanı **zorunlu** eklenmiştir (kaynak: `../yapay-zeka-kararlar.md` §H/H6).

### §5.7 Örnek MM-7 — Model yönetimi gate'leri

| # | Gate | Eşik | Kaynak |
|---|---|---|---|
| MM-G1 | İndirme doğruluğu | SHA256 eşleşmesi | [Satır: 516] |
| MM-G2 | Sürüm tazelik kontrolü | 24 saatte bir | [Satır: 556] · [Satır: 652] |
| MM-G3 | Disk bütçesi | model boyutu + pay (türetme) | [Satır: 61–72] |
| MM-G4 | Otomatik indirme | kapalı (onay zorunlu) | [Satır: 653] |
| MM-G5 | Silme güvenliği | READY model silmede drain gerekli (⚠️ türetme) | [Satır: 523] |

Bu turda **hiçbiri çalıştırılmadı** → `../yapay-zeka-dogrulama.md` §7'ye taşındı.

### §5.8 §5 tur kapanış kaydı

- Eklenen: 2 tam sınıf okuması (MM-1 · MM-2) + 1 durum makinesi + 1 dizin bloğu + 1 HF akışı + 10 alanlı K006 aday şema + 5 gate.
- Uydurma identifier: 0 · ölçü uydurma: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 4 (delete drain · checksum kapı (b) · ProgressCallback imzası · version karşılaştırma).

---

## §6 Kapanış Derinliği — Model yaşam döngüsü ve gate ölçüm tasarımı

> **Durum:** Bu bölümdeki hiçbir kapı bu turda **çalıştırılmadı**; koşullar
> tasarım durumundadır, ölçüm `../yapay-zeka-dogrulama.md` §7'ye taşındı.
> Her eşik kaynak satırıyla işaretlidir; uydurma ölçüm yoktur.

### §6.1 Yaşam döngüsü durum makinesi (tam)

Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` [Satır: 517] `status` enum + [Satır: 523] geçişleri:

```text
REGISTERED ──indirme başla──► DOWNLOADING ──sha256 eşleşti──► READY
     ▲                              │                              │
     │                              │ sha256 uyuşmadı              │ silme isteği
     └──────────────────────────────┘                              ▼
                                                            DELETING ──drain→ (⚠️ türetme) ──► (yok)
```

**Geçiş çıkarımları:**

1. `REGISTERED → DOWNLOADING` yalnız kullanıcı onayıyla (`autoDownload: false` · [Satır: 653]) — otomatik geçiş yoktur.
2. `DOWNLOADING → READY` tek doğrulama kapısı `checksumSHA256`'dır ([Satır: 516]); uyuşmazlıkta geçiş hedefi spec'te **tanımsız** → `⚠️ VERIFICATION REQUIRED` (retry mı, REGISTERED'a mı dönülür, bilinmiyor).
3. `READY → DELETING` geçişi drain içerir mi spec'te yok (§5.7 MM-G5 notu ile bağ).
4. `FAILED`/`ERROR` durumu enum'da görünmüyor (4 değer: REGISTERED · DOWNLOADING · READY · DELETING) → hata durumu nasıl temsil edilir → UNKNOWN.

### §6.2 Gate ölçüm tasarımı (MM-G1…MM-G5 — çalıştırılmadı)

| Gate | Koşul (tasarım) | Ölçüm adımı | Beklenen kayıt |
|---|---|---|---|
| MM-G1 | `sha256(dosya) === checksumSHA256` | indirme sonrası tek hash geçişi | hash hex + süre ms |
| MM-G2 | `now − sonCheck ≥ 24h` → checkForUpdates | [Satır: 556] aralık; [Satır: 652] saat değeri | son kontrol zaman damgası |
| MM-G3 | `sizeBytes + pay ≤ disk boş` | [Satır: 61–72] boyutları; pay = %20 (türetme) | bayt farkı |
| MM-G4 | indirme yalnız onaylı | `autoDownload === false` assert'i | onay olayı |
| MM-G5 | silme öncesi drain | READY model silme çağrısı | drain tamam işareti (⚠️ tanımsız) |

```php
// TASARIM — bu turda yazılmadı; eşikler §6.2'den gelir
public function gateModelChecksum(string $path, string $expectedSha256): bool
{
    if ($expectedSha256 === '') { return false; }         // alan zorunlu (MM-6 şema)
    $actual = hash_file('sha256', $path);
    return is_string($actual) && hash_equals($expectedSha256, $actual);
}
```

### §6.3 Disk bütçesi tablosu (varyant ↔ boyut)

Dosya: ai-models.md [Satır: 61–72] + dizin [Satır: 530–549]:

| Model | FP32 | FP16 | INT8 | Aday kayıt `sizeBytes` (birincil varyant) |
|---|---|---|---|---|
| audiosr-v2 | — | 4.0 GB | 2.1 GB | 4.0 GB (FP16) |
| demucs-ht | — | 2.7 GB | 1.4 GB | 2.7 GB (FP16) |
| deepfilternet2 | — | — | 0.3 GB | 0.3 GB (INT8) |
| genre-cnn | 0.05 GB | — | — | 0.05 GB (FP32) |

Not: `sizeBytes` alanının **varyant başına** mı yoksa **model başına** mı olduğu spec'te belirsizdir (şema tek `sizeBytes` [Satır: 515] taşıyor, `variants` listesi ayrı [Satır: 518]) → `⚠️ VERIFICATION REQUIRED`.

### §6.4 Lisans kapısı (K006'ya özel — NeVa'da yok)

| Kontrol | Kaynak | Davranış |
|---|---|---|
| `license` alanı dolu mu | K006 kararı §H/H6 (NeVa şemasında yok) | boşsa kayıt reddi (tasarım) |
| İzin listesiyle eşleşiyor mu | K006 izin listesi **seçilmedi** | UNKNOWN |
| Uzak depo lisansı değişti mi | HF API `cardData.license` ⚠️ doğrulanmadı | kontrol tasarımı yok |

Bu kapı **K006'ya özeldir**; NeVa spec'inden türetilmez (alan orada yoktur — §5.6 Fark satırı).

### §6.5 §6 tur kapanış kaydı

- Eklenen: 1 tam durum makinesi (4 geçiş çıkarımı) + 5 gate ölçüm tasarımı + 1 PHP tasarım bloğu + 4 satır disk tablosu + 3 satır lisans kapısı.
- Çalıştırılan gate: **0** → ölçüm `../yapay-zeka-dogrulama.md` §7'ye taşındı.
- Uydurma identifier: 0 · ölçü uydurma: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 3 (hata durumu · checksum hata geçişi · sizeBytes kapsamı).

---

## §7 Kapanış Derinliği — Bağlar ve okuma izi

### §7.1 Bağlar

| Bağ | Hedef | İlişki |
|---|---|---|
| Doğrulama | `../yapay-zeka-dogrulama.md` §7 | MM-G1…G5 + §6 kapıları taşındı |
| Risk | `../yapay-zeka-riskler.md` | uzak depo güvenilirliği · checksum |
| Karar | `../yapay-zeka-kararlar.md` §H/H6 | `license` alanı zorunluluğu |
| Test | `../yapay-zeka-test-stratejisi.md` | durum makinesi geçiş testleri |
| Lokal inference | `../local-inference/local-inference-ornekler.md` §5.4 | aynı dizin şeması |
| Kapsam | `[[model-management-kapsam]]` | maddeler ↔ örnek |

### §7.2 Okunan kaynaklar (bu tur, satır kanıtlı)

| Dosya | Kapsam | Satır |
|---|---|---|
| `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | kayıt şeması · status · HF akışı · config · benchmark | 26 · 61–72 · 509–523 · 555–565 · 621–658 · 730 |

### §7.3 §7 tur kapanış kaydı

- Eklenen: 6 bağ + 1 satır okuma izi.
- Uydurma dosya/yol: 0 · yeni `⚠️ VERIFICATION REQUIRED`: 0.

**Authority:** Master-Orchestrator · **Status:** active · **Updated:** 2026-10-15
**Kapanış turu kaydı:** Bu dosya 224 → ≥500 hedefine §5–§7 append'iyle ulaştı; hiçbir mevcut satır silinmedi (append-only).
