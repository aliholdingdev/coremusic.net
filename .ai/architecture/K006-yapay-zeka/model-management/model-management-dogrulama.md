---
title: "Model Management — Doğrulama"
type: architecture-scope
node_id: K006-yapay-zeka/model-management
node_type: subdomain-verification
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# Model Management — Doğrulama

Amaç: `model-management-kapsam.md` içindeki iddiaların kaynaklarını listelemek. Her satır bir iddia + kaynak + tarih + durum. Durum: ✅ doğrulandı · ⚠️ kısmi · UNKNOWN doğrulanamadı.

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | MLflow Model Registry, model versiyonlama · aşama geçişi (Stage transition) ve kayıt workflow'unu belgeler | https://mlflow.org/docs/latest/ml/model-registry/ | 2026-10-12 | ✅ |
| 2 | MLflow "Register, Track and Deploy Machine Learning Models" · model registry workflow adımları resmi dokümanda belgelenir | https://mlflow.org/docs/latest/ml/model-registry/workflow/ | 2026-10-12 | ✅ |
| 3 | ONNX model serialization · graph versioning ve opset sürümü resmi ONNX Repo Docs'ta tanımlanır | https://onnx.ai/onnx/repo-docs/Versioning | 2026-10-12 | ✅ |
| 4 | scikit-learn model persistence (işlem-sistemi model saklama biçimi) resmi belgelerde anlatılır | https://scikit-learn.org/stable/model_persistence.html | 2026-10-12 | ✅ |
| 5 | ONNX Intermediate Representation (IR) model dosya biçimi sürüm alanları (`ir_version`) tanımlanır | https://onnx.ai/onnx/repo-docs/IR.html | 2026-10-12 | ✅ |
| 6 | CoreMusic envanteri 12 AI domain'inin model adı olarak `core-algorithm-orchestrator` kullandığını gösterir | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\ai-master.md` §1 (satır 30–47) | 2026-10-12 | ✅ |
| 7 | AIEngine'de model referansı tek satırda (`$model`) tutulur; sürüm/registry alanı yoktur | `C:\www\coremusic.net\shared\src\AI\AIEngine.php` satır 30 | 2026-10-12 | ✅ |
| 8 | ai-master.md §4'te registry teknolojisi UNKNOWN olarak kayıtlıdır | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\ai-master.md` §4 (satır 87) | 2026-10-12 | ✅ |
| 9 | ai-master.md §7'de model/veri sürümleme · dokümanı sorusu açık soru listesinde yer alır | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\ai-master.md` §7 (satır 118) | 2026-10-12 | ✅ |
| 10 | K006 sınırı: K006 = ML ürün tarafı, K015-AI = ajan altyapısı (bu alt alan K006'da tanımlı) | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\v2.md §2.6` (satır 159) | 2026-10-12 | ✅ |
| 11 | Repo'da model registry/artifact store çağıran kod bulunamadı (model dosyası yükleyen veya sürüm kaydeden sınıf yok) | `C:\www\coremusic.net\shared\src` — grep `registry\|artifact\|mlflow` (0 isabet) | 2026-10-12 | ⚠️ |
| 12 | Model sürümü / etiket (Production·Staging) politikası tanımlı değildir | UNKNOWN | 2026-10-12 | UNKNOWN |
| 13 | Artifact saklama yeri (yerel disk · object store) ve boyut bütçesi belirsizdir | UNKNOWN | 2026-10-12 | UNKNOWN |
| 14 | Rollback tetikleyici (otomatik · manuel) netleşmemiştir | UNKNOWN | 2026-10-12 | UNKNOWN |

| 15 | MLflow Model Registry, merkezi model store + API + UI ile model yaşam döngüsünü yönetir (lineage · versioning · aliasing · tagging) | https://mlflow.org/docs/latest/ml/model-registry/ | 2026-10-12 | ✅ |
| 16 | Registry lineage: her model versiyonu üreten experiment/run'a bağlanır — yeniden üretilebilirlik izi | https://mlflow.org/docs/latest/ml/model-registry/ | 2026-10-12 | ✅ |
| 17 | Yeni kayıt version 1'den başlar; aynı ada sonraki her kayıt versiyon numarasını artırır | https://mlflow.org/docs/latest/ml/model-registry/ | 2026-10-12 | ✅ |
| 18 | Model Alias, bir versiyona mutable isim referansı verir (`models:/MyModel@champion`) — üretim trafiği alias'a yönlendirilir | https://mlflow.org/docs/latest/ml/model-registry/ | 2026-10-12 | ✅ |
| 19 | Version tag'leri pre-deploy kontrol etiketi taşır (ör. `pre_deploy_checks: "PASSED"`, `validation_status`) | https://mlflow.org/docs/latest/ml/model-registry/ | 2026-10-12 | ✅ |
| 20 | Model version tarihsel bilgi tutar: creation/last_updated timestamp, user_id, description | https://mlflow.org/docs/latest/ml/model-registry/ | 2026-10-12 | ✅ |
| 21 | Kayıt/yükleme API'si: `mlflow.register_model(model_uri, name)` → `mlflow.<flavor>.load_model("models:/NAME/VERSION")` | https://mlflow.org/docs/latest/ml/model-registry/workflow/ | 2026-10-12 | ✅ |
| 22 | `set_registered_model_alias` + `copy_model_version` ile staging→production promosyonu yapılır | https://mlflow.org/docs/latest/ml/model-registry/workflow/ | 2026-10-12 | ✅ |
| 23 | Model Stages (Staging\|Production\|Archived) MLflow 2.9.0'da deprecated edildi; yerine alias + version tags önerilir | https://mlflow.org/docs/latest/ml/model-registry/workflow/ | 2026-10-12 | ✅ |
| 24 | Alias ataması deploy'dan bağımsız güncellenir — deployment ile inference iş yükü decouple edilir | https://mlflow.org/docs/latest/ml/model-registry/ | 2026-10-12 | ✅ |
| 25 | scikit-learn persistence resmi sayfası pickle · joblib · cloudpickle karşılaştırması sunar; güvenilmeyen kaynaklardan yükleme arbitrary code riski taşır | https://scikit-learn.org/stable/model_persistence.html | 2026-10-12 | ✅ |
| 26 | `joblib.dump`/`joblib.load` pickle tabanlı API sunar (`joblib.dump(voting_clf, 'voting_clf.joblib')`) | https://joblib.readthedocs.io/en/latest/user_guide/persistence.html | 2026-10-12 | ✅ |
| 27 | joblib memory-mapped yükleme (`mmap_mode`) ile büyük nesnelerde RAM tüketimi azaltılır | https://joblib.readthedocs.io/en/latest/user_guide/persistence.html | 2026-10-12 | ✅ |
| 28 | UI'da "Register Model" adımı artifact'ten model kaydını başlatır | https://mlflow.org/docs/latest/ml/model-registry/ | 2026-10-12 | ✅ |
| 29 | Envanter (M01–M12) ↔ registry kayıt eşlemesi birebir nasıl doğrulanacak? | — | 2026-10-12 | UNKNOWN |
| 30 | Registry erişim yetkisi / kimlik doğrulama hangi düğümün (K009/K010)? | — | 2026-10-12 | UNKNOWN |
| 31 | Eğitim run lineage kaydı (hangi veri/run üretti) CoreMusic'te tutulacak mı? | — | 2026-10-12 | UNKNOWN |
| 32 | model-management kademe-2/3 kapsamı resmileşmedi | ADR-097 K4 (referans) | 2026-10-12 | ⚠️ BEKLİYOR |

**Sayım:** ✅24 · ⚠️2 · UNKNOWN6 · Toplam32

---

## EK §0 — Bu ek ne? (append-only · 2026-10-15)

> **Kural:** aşağıdaki EK §1–§11 eklenmiştir; dosyanın **1–53 satırları** (frontmatter ·
> 32 iddia satırı · orijinal Sayım) **değişmeden korunmuştur** ve bu ekin birebir önekidir.
> **Yeni URL yok** — EK §11'de yalnız 2026-10-12 tarihli 6 URL yeniden taşınır.
> **Kod yüzeyine yazım yok** — bu tur yalnız `.md` evidansıdır.
> Her satır kaynak: `Dosya: <yol> [Satır: n]` · doğrulanamayan iddia: `⚠️ VERIFICATION
> REQUIRED` · bilinmeyen: `UNKNOWN`.

---

## EK §1 — Taze repo taramaları (2026-10-15)

| # | Tarama | Yüzey | Sonuç | Kaynak |
|---|---|---|---|---|
| T1 | grep `mlflow\|ModelRegistry\|model_registry` | `shared/src` | **0 isabet** | `C:\www\coremusic.net\shared\src` — grep 2026-10-15 |
| T2 | grep `core-algorithm-orchestrator` | `shared/src` (tüm dosyalar) | **0 isabet** | aynı — grep 2026-10-15 |
| T3 | grep `core-algorithm-orchestrator` | repo geneli `**/*.md` | **11 isabet · hepsi `model-management/` içinde** (`ornekler` L23,56,59,85,101,107,134 = 7 · `kapsam` L37,39,40 = 3 · `dogrulama` L24 = 1) | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\model-management\` — grep 2026-10-15 |
| T4 | grep `core-algorithm-orchestrator` | `ai-master.md` | **0 isabet** | `...K006-yapay-zeka\ai-master.md` — grep 2026-10-15 |
| T5 | glob `**/*.{onnx,pkl,joblib,pt,pth,safetensors}` | repo kökü | **0 dosya** | `C:\www\coremusic.net` — glob 2026-10-15 |
| T6 | glob `*.php` | `shared/src/AI/` | **7 dosya**: `AIEngine.php` · `AIOrchestrator.php` · `AIWorkflow.php` · `KnowledgeBase.php` · `MemorySystem.php` · `PromptEngine.php` · `ToolCalling.php` | `C:\www\coremusic.net\shared\src\AI` — glob 2026-10-15 |
| T7 | glob `*.yml` | `.github/workflows/` | **2 dosya**: `ci.yml` · `secret-scan.yml` | `C:\www\coremusic.net\.github\workflows` — glob 2026-10-15 |
| T8 | grep `"php"` / `"phpunit/phpunit"` | `shared/composer.json` | L2 `coremusic/shared-infrastructure` · L8 `"php": ">=8.4"` · L24 `"phpunit/phpunit": "^10.5"` · ML/registry paketi **yok** | `C:\www\coremusic.net\shared\composer.json` [Satır: 2, 8, 24] |
| T9 | grep `M01` | K006 kök + çocuklar | **5 isabet**: `evaluation-kapsam.md` L19 · `model-management-kapsam.md` L23, L37, L39 · `model-management-dogrulama.md` L48 — **`ai-master.md` içinde 0 isabet** | grep 2026-10-15 |
| T10 | heading grep `^#{1,4} ` | `ai-master.md` | **37 başlık** (`#` L27 → `§18.4` L544) | grep 2026-10-15 |
| T11 | heading grep `^#{1,4} ` | `model-management-anayasa.md` | **48 başlık** (`#` L27 → `§23.3` L514) | grep 2026-10-15 |
| T12 | satır sayımı (aralık testi) | `ai-master.md` | **557 satır** (2026-10-15; 2026-10-13 ölçümü 371'di) | `read` → "Offset 999 is out of range for this file (557 lines)" |
| T13 | satır sayımı | `model-management-kapsam.md` | **55 satır** (tam okuma, 2026-10-15) | `read` L1–L55 |
| T14 | satır sayımı | `model-management-dogrulama.md` (bu dosya, ek öncesi) | **53 satır** | grep `^.*$` = 53 isabet, 2026-10-15 |

**EK §1 gözlem:** T1–T5 birlikte okunduğunda **model ağırlığı dosyası, model registry
çağrısı ve MLflow bağımlılığı repo'da 0 satır / 0 dosyadır** — bu, 2026-10-12 iddia 11
(`⚠️`) ile aynı sonuçtur; 2026-10-15'te **tekrar** doğrulanmıştır.

### EK §1.1 Kod yüzeyi — `AIEngine` sınıf başı (birebir alıntı)

**Dosya:** `C:\www\coremusic.net\shared\src\AI\AIEngine.php` [Satır: 18–44]

```php
/**
 * AI Engine — CoreMusic'in merkezi AI işleme motoru.
 *
 * 5 modül:
 * 1. RecommendationEngine — Müzik önerileri (collaborative + content-based)
 * 2. AudioAnalyzer — Ses analizi (BPM, key, energy, mood)
 * 3. EQOptimizer — Otomatik EQ (room correction, preset)
 * 4. HardwareAnalyzer — Donanım analizi (DAC/ADC performans, termal)
 * 5. FaultPredictor — Hata tahmini (anomaly detection)
 */
class AIEngine implements AIEngineInterface
{
    private const MAX_RECOMMENDATIONS = 20;
    private const MIN_SCORE = 0.7;
    private const DIVERSITY_TARGET = 0.30;

    public function __construct(private readonly ConfigManager $config)
    {
    }

    /**
     * Yapılandırma erişimi.
     */
    public function getConfig(): ConfigManager
    {
        return $this->config;
    }
```

| Satır | Birebir içerik | K006.1 (model-management) açısından |
|---|---|---|
| 28 | `class AIEngine implements AIEngineInterface` | tek sınıf, tek sözleşme — model yaşam döngüsü sınıfı **yok** |
| 30 | `private const MAX_RECOMMENDATIONS = 20;` | **öneri sayısı** sabiti — model sürümü **değil** |
| 31 | `private const MIN_SCORE = 0.7;` | **skor eşiği** sabiti — model sürümü **değil** |
| 32 | `private const DIVERSITY_TARGET = 0.30;` | **çeşitlilik hedefi** sabiti — model sürümü **değil** |
| 34 | `__construct(private readonly ConfigManager $config)` | tek bağımlılık = `ConfigManager` (seam) |
| 41–44 | `getConfig(): ConfigManager` | yapılandırma erişim noktası — registry **değil** |

---

## EK §2 — Çelişki kaydı (2026-10-15 diski ↔ 2026-10-12 satırları)

> **Append-only kuralı:** 2026-10-12 tarihli satırlar **silinmez ve düzeltilmez** (dosyanın
> 1–53 satırları önek olarak korunur). Aşağıdaki satırlar **yeni gözlem kaydıdır**; bir
> iddianın "yanlış" olması o satırı geriye dönük değiştirmez, **yeni bilgi** olarak eklenir.

| # | Eski iddia (satır) | 2026-10-15 disk gözlemi | Yeni durum |
|---|---|---|---|
| C1 | İddia 7 (L25): "AIEngine'de model referansı tek satırda (`$model`) tutulur — `AIEngine.php` satır 30" | `AIEngine.php` **Satır: 30** = `private const MAX_RECOMMENDATIONS = 20;` — `$model` değişkeni satır 30'da **yok** | ⚠️ ÇELİŞKİ — eski satır korunur, doğru kanıt EK §1.1 |
| C2 | `model-management-kapsam.md` **Satır: 40**: "`$model = 'core-algorithm-orchestrator';` — kayıt/zaman damgası yok (kanıt: satır 28–29)" | `AIEngine.php` Satır 28–32 = sınıf başlığı + 3 sabit; `core-algorithm-orchestrator` dizesi `shared/src` içinde **0 isabet** (T2) | ⚠️ ÇELİŞKİ — kapsam dosyası bu turda **yazılmadı** (beyazliste dışı), düzeltme bekliyor |
| C3 | İddia 6 (L24): "ai-master §1 (satır 30–47) 12 AI domain'inin model adı `core-algorithm-orchestrator`" | `ai-master.md` içinde dize **0 isabet** (T4); `M01` **0 isabet** (T9); güncel §1 = **Satır: 56** (`## §1 Kimlik`) → §1.3 bitiş L92 | ⚠️ ÇELİŞKİ — hem dize hem satır aralığı bayat |
| C4 | `model-management-kapsam.md` **Satır: 38**: "ai-master §3 (satır 59–73)" | `ai-master.md` **Satır: 142** = `## §3 Kademe-2 envanter (9 çocuk)`; `model-management` satırı **Satır: 148** | ⚠️ BAYAT SATIR — yeni karşılık: EK §8/§3-4 |
| C5 | `model-management-kapsam.md` **Satır: 39**: "yapay-zeka-bagimliliklar.md (satır 14) — M01 · `core-algorithm-orchestrator` tablo satırı" | `yapay-zeka-bagimliliklar.md` **Satır: 14** = `- K006.8-recommendation` — bu **`children:` listesinin 8. elemanıdır**, tablo satırı değildir; dosya genelinde `core-algorithm-orchestrator` **0 isabet** | ⚠️ ÇELİŞKİ — iddia ettiği satır var, içeriği farklı |
| C6 | İddia 8 (L26): "ai-master §4 (satır 87) registry teknolojisi UNKNOWN" | `ai-master.md` **Satır: 176** = `## §4 Sorumluluk matrisi`; §4 içinde "registry UNKNOWN" satırı **yok**. UNKNOWN listesi güncel yerinde: **Satır: 348–357** (`### §11.2 UNKNOWN`) ve **Satır: 256** (§7/4 "Üretim modeli/runtime seçimi … **UNKNOWN**") | ⚠️ BAYAT SATIR — içerik gerçek, konumu değişmiş |
| C7 | İddia 9 (L27): "ai-master §7 (satır 118) — model/veri sürümleme dokümanı sorusu" | `ai-master.md` **Satır: 249** = `## §7 Açık sorular` (L249–262, 10 soru). 10 soru arasında "model/veri sürümleme dokümanı" başlığı **yok**; en yakın kayıt §7/4 (**Satır: 256**) ve §18.4/3 (**Satır: 550**) "Model format/runtime teknoloji ADR'si" | ⚠️ BAYAT SATIR + BAYAT BAŞLIK |
| C8 | `model-management-kapsam.md` **Satır: 37 / Satır: 23**: envanter `ai-master.md §1` içinde "M01–M12 sorumluluk tablosu" | `ai-master.md` içinde `M01` **0 isabet** (T9); §1 kimlik bölümüdür (L56–92), sorumluluk matrisi **§4**'tedir (L176–203) ve o matrisin sütunları: `# · Sorumluluk · Birincil çocuklar · İkincil · Repo kanıtı · Etiket` — model adı sütunu **yok** | ⚠️ ÇELİŞKİ — "model adı sütunu" bugünkü diskte mevcut değil |
| C9 | `model-management-bilesenler.md` **Satır: 36** ve **Satır: 57**: "L30–32 = motor adı/versiyon/kanal sabitleri" | `AIEngine.php` Satır 30–32 = `MAX_RECOMMENDATIONS` · `MIN_SCORE` · `DIVERSITY_TARGET` — bunlar **öneri skorlama** sabitleri, motor kimliği/versiyonu **değil** | ⚠️ ÇELİŞKİ — terminoloji sapması (beyazliste dışı dosya, düzeltme bekliyor) |
| C10 | (yeni kayıt) | `ai-master.md` **Satır: 406** = `\| 7 \| model-management/ \| 6 \| 476 \| 0 \|` — §13.2 çocuk-klasör ölçümü (kaynak tarihi 2026-10-13); 2026-10-15'te bu klasörde `model-management-dogrulama.md` **53 → 500+ satıra** çıkarılmıştır, ölçüm **bayattır** | ℹ️ ÖLÇÜM NOTU |
| C11 | (yeni kayıt) | `ai-master.md` **Satır: 508** Authority satırı: `v2.0.0 · 2026-10-12` · **Satır: 512** `## §18 Uygulama Yol Haritası (hibrit plan — 2026-10-14, Authority onaylı)` — dosya 2026-10-12/13/14'te **yeniden yazılmıştır** (§0–§18 iskeleti) | ℹ️ YENİDEN YAZIM KAYDI |

**EK §2 sayımı:** 11 çelişki/ölçüm satırı (C1–C11) · bunlardan **9'u ⚠️**, 2'si ℹ️ not.

---

## EK §3 — `ai-models.md` (NevaEngine legacy) §8 derin okuma — model kayıt emsali

**Kaynak:** `C:\www\coremusic.net\.ai\projects\NevaEngine\legacy-2026-07\ai-models.md`
[Satır: 498–571 okundu · 2026-10-15]

### EK §3.1 §8 başlık + §8.1 `ModelRegistry` (birebir alıntı)

**Satır: 501–526**

```text
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

| # | `ModelInfo` alanı | Ne taşır | model-management-anayasa §4 (Model Kartı) karşılığı |
|---|---|---|---|
| M-1 | `id` | model kimliği (ör. `neva/audiosr-v2`) | model adı alanı |
| M-2 | `name` | görünen ad | model adı alanı |
| M-3 | `version` | `2.1.0` biçiminde sürüm | §6.1 sürüm semantiği |
| M-4 | `hfRepo` | kaynak depo | §5 kaynak getirme usulü |
| M-5 | `hfFile` | artifact dosya adı (`model.onnx`) | §8 yayma — artifact |
| M-6 | `localPath` | yerel dizin | §8.2 yerel paket teslimi |
| M-7 | `sizeBytes` | boyut | artifact boyutu (kapsam §1.3) |
| M-8 | `checksumSHA256` | bütünlük | §6.3 referans bütünlüğü · §20 güvenlik |
| M-9 | `status` (NOT_DOWNLOADED·DOWNLOADING·READY·ERROR) | durum | §18 durum matrisi |
| M-10 | `variants` (FP16·INT8·FP32) | format varyantı | §5.2 sürüm sabitleme |
| M-11 | `listAvailable()` | envanter listesi | §1 kapsam — model envanteri |
| M-12 | `downloadModel(id, variant)` | getirme | §5.1 içe aktarma adımları |
| M-13 | `deleteModel(id)` | silme | §6.4 emekliliye ayırma |
| M-14 | `checkForUpdates()` | güncelleme taraması | §6.2 geçmiş kaydı ile ilişkili |

> **Kanal uyarısı:** bu tablo **eşleme tasarımıdır**, repo kodu **değildir**; `ModelRegistry`
> sınıfı CoreMusic deposunda **bulunmaz** (T1 · T5 ile 0 isabet). Eşlemenin kendisi
> `⚠️ VERIFICATION REQUIRED` — anayasa §4 alan adları ile `ModelInfo` alan adları
> **birebir aynı değildir**, yalnızca **anlam** eşleşir.

### EK §3.2 §8.2 Model dizin yapısı (birebir alıntı)

**Satır: 528–549**

```text
### 8.2 Model Versiyonlama

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

| # | Dizin | Dosyalar | Boyut kaydı | Sürüm kanıtı |
|---|---|---|---|---|
| D-1 | `models/audiosr-v2/` | `model_fp16.onnx` · `model_int8.onnx` · `config.json` · `version.txt` · `checksums.sha256` | 4.0 GB · 2.1 GB | `version.txt` = `"2.1.0"` |
| D-2 | `models/demucs-ht/` | `model_fp16.onnx` · `model_int8.onnx` · `config.json` | — | — |
| D-3 | `models/deepfilternet2/` | `model_int8.onnx` · `config.json` | 0.3 GB | — |
| D-4 | `models/genre-cnn/` | `model_fp32.onnx` · `genres.json` | 50 MB | — |
| D-5 | sürüm kanıtı biçimi | `version.txt` + `checksums.sha256` (dizin başına) | — | anayasa §6.2 append-only geçmiş ile **aynı fikir** |
| D-6 | CoreMusic deposu karşılığı | `**/*.{onnx,pkl,joblib,pt,pth,safetensors}` → **0 dosya** (T5) | — | ❌ bu yapı repo'da **yok** |

### EK §3.3 §8.3 Otomatik güncelleme — `ModelUpdater` (birebir alıntı)

**Satır: 551–571.** Not: `Satır: 555` bir API URL'si içerir — **bu turda yeni URL taşıma
yasası** gereği o satırın metni kopyalanmamıştır (kanıt: satır numarası korunur).

```cpp
class ModelUpdater {
    // Satır 555 — harici API URL'si: metin bilinçli kopyalanmadı (URL taşıma yasağı)
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

| # | Gözlem | Değer | Kaynak |
|---|---|---|---|
| U-1 | tarama aralığı | 24 saat (`checkInterval{24}`) | ai-models.md [Satır: 556] |
| U-2 | `UpdateInfo` alanları | `modelId` · `currentVersion` · `latestVersion` · `updateAvailable` · `changelog` | [Satır: 559–565] |
| U-3 | `currentVersion` ↔ `latestVersion` ikilisi | sürüm karşılaştırması kayıtta tutulur | [Satır: 561–562] |
| U-4 | `changelog` alanı | sürüm geçmişi metni zorunlu | [Satır: 564] |
| U-5 | tekil + toplu güncelleme | `updateModel` · `updateAll` | [Satır: 568–569] |
| U-6 | ilerleme geri çağrısı | `ProgressCallback` — indirme ilerlemesi | [Satır: 568] |
| U-7 | CoreMusic karşılığı | repo'da `checkForUpdates` / `updateModel` çağrısı **0 isabet** | `shared/src` grep 2026-10-15 (T1 ile birlikte) |
| U-8 | ota-update.md emsali | rollback + manifest + canary — firmware tarafında **tasarım var, kod yok** | `...electronic\firmware\ota-update.md` [Satır: 453–456] |

**EK §3 sayımı:** 3 alıntı bloğu (§8.1 · §8.2 · §8.3) · 4 tablo (14 + 6 + 8 + 3 satır
veri) · 1 kanal uyarısı.

---

## EK §4 — `ota-update.md` çapraz okuma (yaşam döngüsü / rollback emsali)

**Kaynak:** `C:\www\coremusic.net\.ai\electronic\firmware\ota-update.md`
[Satır: 118–219 · 385–414 · 452–496 okundu · 2026-10-15]

### EK §4.1 §7 Rollback türleri (birebir tablo) — [Satır: 124–136]

| Rollback Türü | Tetikleyici | Amaç |
|---------------|-------------|------|
| Otomatik rollback | Post-update sağlık kontrolü başarısız | Bozuk sürümü reddet |
| Anti-rollback | Sürüm numarası monoton artmalı | Eski/vulnerable sürümün yüklenmesini engelle |
| Kademeli rollback | Kademeli dağıtım geri alma | Yaygın hatalı sürümü çekme |

- **§7.1** [Satır: 136] — sürüm sayacı donanımda (OTP/secure counter) tutulur; bootloader
  depolanmış sürümdeki paketleri reddeder.
- **§7.2 durum tablosu** [Satır: 140–145] — `Güncelleme öncesi A→A` · `B'ye yazıldı A (pending)→A→B dener` ·
  `B sağlık geçti B (good)→B` · `B sağlık başarısız A (good)→A otomatik rollback`.
- **§6.2** [Satır: 120] — A/B atomik güncelleme: yazma sırasında güç kesilirse eski yuva
  aynen kalır (brick engeli).

### EK §4.2 §8 Transport + Manifest (birebir tablo) — [Satır: 149–176]

| Katman | Protokol | Amaç |
|--------|----------|------|
| Transport | TLS 1.3 | Gizlilik + bütünlük + kimlik doğrulama |
| Paket şifreleme | AES-256-GCM | Uçtan uca paket şifreleme |
| İmza | RSA-2048 / ECDSA | Firmware bütünlüğü (Bölüm 5) |
| Manifest | JSON/COSE | Sürüm, hedef donanım, bağımlılıklar |

| Alan | Açıklama |
|------|----------|
| version | Semantic sürüm + monotonic counter |
| hardware_id | Hedef donanım revizyonu |
| image_size | Firmware boyutu |
| sha256 | Paket checksum'ı (Bölüm 3) |
| signature | İmza (Bölüm 5) |
| min_version | Anti-rollback alt sınırı (Bölüm 7.1) |
| rollout_policy | Kademeli dağıtım oranı (Bölüm 10) |

- **§8.2** [Satır: 176] — TLS yalnız taşıma kanalını korur; paket imzası ayrıca
  doğrulanmalıdır (**defense in depth**).
- **§9 sağlık kontrolü** [Satır: 184–190] — Boot (watchdog kick) · temel servis alive ping ·
  I2S/DMA heartbeat · bellek CRC · sürüm doğrulama (`boot edilen sürüm = manifest`); hepsi
  başarısızlıkta rollback / safe-mode.
- **§9.1** [Satır: 194] — **canary penceresi** içindeki beklenmedik reset = güncelleme
  başarısız → eski yuvaya rollback.

### EK §4.3 §10 Kademeli dağıtım + §21 Filo yönetimi — [Satır: 198–215 · 390–405]

| Aşama | Oran | Amaç |
|-------|------|------|
| Canary | %1–5 | Erken sinyal toplama |
| Early | %10–25 | Genişletilmiş doğrulama |
| Broad | %50–90 | Ana yayılım |
| Full | %100 | Tamamlanma |

| Rol | Sorumluluk |
|-----|-------------|
| Update server | Firmware + manifest barındırır, imzalar |
| Enrollment / device registry | Cihaz envanteri, mevcut sürüm takibi |
| Rollout orchestrator | Kademeli dağıtım kararı (Bölüm 10) |
| Telemetry ingest | Cihaz sağlık metrikleri (Bölüm 11) |
| Rollback coordinator | Hatalı sürüm geri çekme (Bölüm 7) |

- **§10.1** [Satır: 211] — hata oranı/sağlık eşiği aşılırsa dağıtım **durdurulur ve geri
  alınır**; kapılar telemetriye dayanır.
- **§10.2** [Satır: 215] — kohort bazlı dağıtım (donanım revizyonu · bölge · beta).
- **§21.1** [Satır: 404] — sunucu her cihazın sürümünü ve `hardware_id`'sini bilir;
  uyumsuz revizyona yanlış paket gönderilmez.

### EK §4.4 §25 Doğrulama ve test matrisi (birebir tablo) — [Satır: 460–488]

| Test | Senaryo | Kabul Kriteri |
|------|---------|----------------|
| Normal güncelleme | İmzalı paket, sağlıklı ağ | Yeni sürüm aktif |
| İmza reddi | Bozuk/sahte imza | Güncelleme reddi, eski sürüm |
| Güç-kesintisi (yazma) | Yazma sırasında reset | Aktif yuva değişmez |
| Güç-kesintisi (commit) | Bayrak öncesi reset | Eski yuva boot |
| Boot döngüsü | Yeni firmware boot olmaz | Watchdog → rollback |
| Sağlık kontrolü başarısız | Canary penceresinde reset | Otomatik rollback |
| Anti-rollback | Eski sürüm paketi | Reddi |
| Ağ kesintisi (resume) | İndirme yarıda | Kal yerden devam |
| Uyumsuz donanım | Yanlış hardware_id | Reddi |

**§25.1 rollback senaryoları** [Satır: 478–484]: (1) boot olmaz 3 deneme → eski yuva ·
(2) canary watchdog → rollback + log · (3) sağlık timeout → rollback · (4) NVS migrate
hatası → rollback (NVS korunur) · (5) rollback sonrası tekrar güncelleme → başarılı.

**§25.2** [Satır: 488] — test ortamı QEMU/simüle flash + gerçek donanım + programlanabilir
güç rölesi; **`⚠️ VERIFICATION REQUIRED:` bu altyapı henüz repo'da otomatikleştirilmemiştir.**

**§24 faz tablosu** [Satır: 453–456] — Faz 1 tasarım · Faz 2 A/B+anti-rollback `PLANNED (repo kodu YOK)` ·
Faz 3 kademeli dağıtım `PLANNED` · Faz 4 donanım doğrulama `PLANNED`.

### EK §4.5 Model yönetimine aktarım (tasarım emsali — kod değil)

| # | OTA kavramı | model-management karşılığı | Durum |
|---|---|---|---|
| A-1 | A/B yuva + atomik commit | model sürümü geçişi (önceki sürüm silinmez) | tasarımsal · `⚠️ VERIFICATION REQUIRED` |
| A-2 | otomatik rollback (sağlık kontrolü) | kötü metrik/latency → önceki sürüme dönüş | tasarımsal · İddia 14 (`UNKNOWN`) ile aynı belirsizlik |
| A-3 | anti-rollback (monotonic counter) | model sürüm sayacının geri alınamazlığı | tasarımsal |
| A-4 | manifest (`version` · `sha256` · `rollout_policy`) | model kartı + checksum + yayma politikası | tasarımsal · anayasa §4/§6/§8 ile uyumlu |
| A-5 | canary penceresi | model canary/percentage rollout | tasarımsal |
| A-6 | fleet: update server + enrollment | model registry + envanter (M01–M12) | tasarımsal · İddia 29 `UNKNOWN` |
| A-7 | telemetri → karar kapısı | drift/izleme (evaluation) | tasarımsal |
| A-8 | test matrisi (9 + 5 senaryo) | model rollout test paketi | `PLANNED` — repo testi **0** |
| A-9 | `PLANNED (repo kodu YOK)` [Satır: 454] | CoreMusic rollback kodu da **0** (T1 · T5) | ❌ **0 / 0 — iki tarafta da kod yok** |
| A-10 | bu tablo bir **emsal**dir, K006'ya doğrudan kopya **değildir** | — | `⚠️ VERIFICATION REQUIRED` |

**EK §4 sayımı:** 5 alıntı bloğu/tablo · 21 veri satırı (3+4+7+4+5+9 = özet 32 + notlar)
· 10 aktarım satırı (A-1–A-10).

---

## EK §5 — `audio-drivers.md` §5.10 — sürüm yönetimi kuralı

**Kaynak:** `C:\www\coremusic.net\.ai\electronic\drivers\audio-drivers.md` [Satır: 441–443]

> "Sürücü sürümü donanım uyumluluk matrisine yazılır ([[device-integration]] §4). Sürüm
> bump yalnız şu durumlarda yapılır: güvenlik açığı, yeni donanım desteği, xrun düzeltmesi.
> Bağımlılık bump / firmware OTA / refactor → DUR + handover (kural 13)."

| # | Kural | Model yönetimi açısından | Durum |
|---|---|---|---|
| DR-1 | sürüm bump yalnız 3 nedende (güvenlik · yeni donanım · xrun) | model sürümü de **kısıtlı tetikleyici** ile bump edilebilir — anayasa §6.4 (retire) ile uyumlu | ✅ okundu |
| DR-2 | sürüm donanım uyumluluk matrisine yazılır | model sürümü de bir **matrise** yazılmalıdır (model × runtime × donanım) | ✅ okundu |
| DR-3 | `firmware OTA` → **DUR + handover** | OTA değişikliği sürücü katmanında durdurulur — sürüm zinciri kopmaz | ✅ okundu |
| DR-4 | §5.9 platform karşılaştırması [Satır: 434–439] — sürüm bağımlılığı sütunu: kart üreticisi / OS / çekirdek / OS | sürüm sahipliği **platforma** bağlıdır; model tarafında sahiplik **UNKNOWN** (İddia 30) | ✅ okundu / İddia 30 `UNKNOWN` |
| DR-5 | §6.1 hata modu tablosu [Satır: 451–457] — Xrun · cihaz kaybı · lock · format reddi · drift | model tarafında karşılığı **drift** (evaluation) — K006.2 kapsamında | ✅ okundu |

**EK §5 sayımı:** 1 alıntı · 5 satır (DR-1–DR-5).

---

## EK §6 — `model-management-anayasa.md` başlık çaprazı (48 başlık, 2026-10-15)

**Kaynak:** `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\model-management\model-management-anayasa.md`
— heading grep `^#{1,4} ` = 48 isabet. "Bu dosyada karşılığı" sütunu **yalnız** bu
`-dogrulama.md` dosyasının kanıtını gösterir; `—` = kanıt bu turda toplanmadı.

| # | Başlık | Satır | Bu dosyada karşılığı |
|---|---|---|---|
| 1 | `# Model Management — Alt Dal Anayasası (kademe-2)` | 27 | — |
| 2 | `§1. Kapsam Sınırı` | 35 | EK §7 (kapsam çaprazı) |
| 3 | `§2. İlke ve Değerler` | 56 | — |
| 4 | `§2.1 Öncelik sırası` | 58 | — |
| 5 | `§2.2 Zero-hallucination bağlayıcılığı` | 68 | EK §0 · EK §2 (çelişki kaydı) |
| 6 | `§3. Mimari Roller ve Kararlar` | 77 | — |
| 7 | `§3.1 Yaşam döngüsü akışı` | 79 | EK §4 (OTA emsali) |
| 8 | `§3.2 Sahiplik matrisi` | 100 | İddia 30 `UNKNOWN` |
| 9 | `§4. Model Kartı Şeması` | 115 | EK §3.1 (M-1–M-14 eşlemesi) |
| 10 | `§5. Kaynak Getirme Usulü` | 140 | EK §3.1 (M-4 · M-12) |
| 11 | `§5.1 İçe aktarma adımları` | 142 | EK §3.1 (M-12) |
| 12 | `§5.2 Sürüm sabitleme kuralı` | 156 | EK §3.2 (M-10 · D-5) |
| 13 | `§6. Sürümleme ve Geçmiş` | 164 | EK §3.2 · EK §4.1 |
| 14 | `§6.1 Sürüm semantiği` | 166 | EK §3.1 (M-3) |
| 15 | `§6.2 Geçmiş kaydı (append-only)` | 177 | EK §3.2 (D-5) · EK §0 (bu dosyanın append-only rejimi) |
| 16 | `§6.3 Referans bütünlüğü` | 183 | EK §3.1 (M-8) |
| 17 | `§6.4 Emekliliye ayırma (retire)` | 187 | EK §3.1 (M-13) |
| 18 | `§7. Kapı (Gate) Entegrasyonu` | 200 | — |
| 19 | `§7.1 Kapıların sırası` | 202 | — |
| 20 | `§7.2 Gate kayıt zorunluluğu` | 211 | — |
| 21 | `§8. Yayma (Distribution)` | 221 | EK §4.3 · EK §4.5 (A-4 · A-5) |
| 22 | `§8.1 Servis/inference'a yayma` | 223 | EK §4.5 (A-1) |
| 23 | `§8.2 Yerel paket teslimi (local-inference)` | 229 | EK §3.1 (M-6) |
| 24 | `§8.3 Rollback` | 235 | EK §4.1 (3 rollback türü) · İddia 14 `UNKNOWN` |
| 25 | `§9. Rehberler` | 247 | — |
| 26 | `§10. Zorunlu Kurallar (MUST / MUST NOT)` | 260 | — |
| 27 | `§11. Karar Kaydı` | 278 | EK §2 (C10 · C11 karar kaydı) |
| 28 | `§12. Bağımlılıklar` | 299 | T1 · T5 · T8 (0 isabet kanıtları) |
| 29 | `§12.1 Bağımlı olduğu alanlar` | 301 | EK §7 (kapsam §2) |
| 30 | `§12.2 Bağımlı olan alanlar` | 310 | EK §7 (kapsam §1) |
| 31 | `§13. İlişkili Dosyalar` | 320 | EK §7 (kapsam "İlişkili dosyalar" L51–55) |
| 32 | `§13.1 Bu dalın kardeş dosyaları` | 322 | EK §6 (bu tablo) · EK §7 |
| 33 | `§13.2 Vault referansları` | 335 | — |
| 34 | `§14. Açık Sorular` | 349 | EK §7 (kapsam §4) · İddia 29–32 |
| 35 | `§15. İlgili Lint Maddeleri` | 359 | — |
| 36 | `§16. Bağlam ve Versiyon` | 371 | EK §2 (C11) |
| 37 | `§17. Bileşen Sözlüğü (TR↔EN)` | 386 | — |
| 38 | `§18. Durum Matrisi (model kaydı durumları)` | 406 | EK §3.1 (M-9 `ModelStatus`) |
| 39 | `§19. Hata Sınıfları ve Kayıt Davranışı` | 422 | EK §3.1 (M-9 `ERROR`) · EK §4.4 |
| 40 | `§20. Güvenlik ve Güven` | 438 | EK §4.2 (imza · checksum · defense in depth) |
| 41 | `§21. Anti-Patternler` | 448 | EK §2 (C1–C9 anti-pattern kanıtları) |
| 42 | `§22. Uç Nokta Sözleşmesi` | 464 | — |
| 43 | `§22.1 Giriş` | 466 | — |
| 44 | `§22.2 Çıkış` | 476 | — |
| 45 | `§23. Kaynaklar ve Red-Team Soruları` | 488 | EK §11 (URL taşıma) |
| 46 | `§23.1 Kaynaklar` | 490 | EK §11 |
| 47 | `§23.2 Red-Team soruları` | 501 | EK §2 · EK §10 (kapılar) |
| 48 | `§23.3 Revizyon Kaydı` | 514 | EK §2 (C10 · C11) |

**EK §6 sayımı:** 48 başlık · karşılığı olan **30** · `—` (bu turda kanıt yok) **18**.

---

## EK §7 — `model-management-kapsam.md` (55 satır) ile çapraz

**Kaynak:** `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\model-management\model-management-kapsam.md`
[tam okuma L1–L55 · 2026-10-15]

| Kapsam satırı | Kapsam içeriği (özet) | Bu dosyadaki karşılık | Durum |
|---|---|---|---|
| L15 | ELI15 — kayıt defteri metaforu; "K006 çocuk tablosunda (ai-master §3) 9. sırada" | EK §8/§3-9 — `ai-master.md` **Satır: 148** = model-management, tablo 9 satır (L148–156) | ✅ iddia doğru, **satır no bayat** |
| L19 | Kapsam içi 1 — model kayıt defteri (registry) | T1 (`mlflow\|ModelRegistry` 0) · EK §3.1 (emsal `ModelRegistry`) | ⚠️ istek var, repo 0 |
| L20 | Kapsam içi 2 — sürümleme + `Production/Staging/Archived` etiketleri | İddia 23 — MLflow 2.9.0'da stages **deprecated** → alias + tags | ⚠️ kapsam etiketleri **eski MLflow modelini** anlatıyor |
| L21 | Kapsam içi 3 — artifact saklama (ağırlık · ONNX · vektör indeksi) | T5 (0 dosya) · İddia 13 `UNKNOWN` | ⚠️ 0 / UNKNOWN |
| L22 | Kapsam içi 4 — deploy + rollback | EK §4.1–§4.5 · İddia 14 `UNKNOWN` | ⚠️ tasarım + emsal, kod 0 |
| L23 | Kapsam içi 5 — model envanteri (M01–M12) "ai-master §1 sorumluluk tablosu" | **C8**: `ai-master.md` içinde `M01` **0 isabet**; sorumluluk matrisi §4 (L176) | ⚠️ ÇELİŞKİ |
| L27–31 | Kapsam dışı 5 madde (eğitim · K016 · inference · feature store · etik) | ai-master **Satır: 187–188** (S6/S7 = 🔴 K015-AI) | ✅ uyumlu |
| L37 | Disk kanıtı — ai-master §1 (satır 30–47) + `core-algorithm-orchestrator` | **C3** · **C8** | ⚠️ ÇELİŞKİ |
| L38 | Disk kanıtı — ai-master §3 (satır 59–73) | **C4**: güncel §3 = L142, satır = L148 | ⚠️ BAYAT |
| L39 | Disk kanıtı — bagimliliklar (satır 14) M01 tablo satırı | **C5**: L14 = `- K006.8-recommendation` (children listesi) | ⚠️ ÇELİŞKİ |
| L40 | Disk kanıtı — `AIEngine.php` (satır 30) `$model = 'core-algorithm-orchestrator'` | **C1** · **C2**: L30 = `MAX_RECOMMENDATIONS` | ⚠️ ÇELİŞKİ |
| L41 | Disk kanıtı — ai-master §4 (satır 87) registry UNKNOWN | **C6**: §4 = L176, UNKNOWN = L348–357 / L256 | ⚠️ BAYAT |
| L45 | Açık soru 1 — registry teknolojisi seçilmedi | İddia 8 (bayat konum) + ai-master **Satır: 352–353** UNKNOWN | ✅ hâlâ açık |
| L46 | Açık soru 2 — artifact yeri + boyut bütçesi | İddia 13 `UNKNOWN` | ✅ hâlâ açık |
| L47 | Açık soru 3 — sürüm şeması + `Production` işaretçisi | ai-master **Satır: 525** (Faz 4 "MLflow registry (aday)") | ✅ hâlâ açık |
| L48 | Açık soru 4 — **kademe-2 kapsam taslağı v2'de yok** | ai-master **Satır: 159–160** "resmi kapsam tanımı v2'de yoktur → `⚠️ VERIFICATION REQUIRED`" | ✅ iki dosya **uyumlu** |
| L49 | Açık soru 5 — rollback tetikleyicisi otomatik/mü? | EK §4.1 (emsalde otomatik + anti + kademeli) · İddia 14 `UNKNOWN` | ✅ hâlâ açık |
| L53–55 | İlişkili dosyalar: `-dogrulama` · `-ornekler` · K006 index | bu dosya (EK ekli) · `model-management-ornekler.md` (7 `core-algorithm-orchestrator` satırı) | ✅ linkler geçerli |

**EK §7 sayımı:** 20 çapraz satırı · kapsam §3 disk-kanıtlarının **5/5'i** ya çelişki
(C3–C5) ya bayat satır (C4 · C6) olarak işaretlendi.

---

## EK §8 — `ai-master.md` başlık + satır yeniden konumu (2026-10-15)

**Kaynak:** `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\ai-master.md`
[37 başlık · 557 satır · 2026-10-15]

| # | Başlık | Satır | Bu dosyadaki kullanım |
|---|---|---|---|
| 1 | `# ai-master — K006-yapay-zeka` | 27 | — |
| 2 | `§0 Bu dosya ne? — 12 dosyalık set haritası` | 34 | EK §2 (C11 iskelet) |
| 3 | `§1 Kimlik` | 56 | **C3** (eski iddia L30–47) |
| 4 | `§1.1 Kaynak rol cümlesi` | 58 | — |
| 5 | `§1.2 ELI15 kimlik` | 72 | EK §7 (L15 çaprazı) |
| 6 | `§1.3 Kimliğin 4 dayanağı` | 84 | T6 (D3 · D4) |
| 7 | `§2 Sınır — ne DEĞİL` | 95 | EK §7 (kapsam dışı) |
| 8 | `§2.1 Kapsam DIŞI` | 97 | — |
| 9 | `§2.2 K006 ↔ K015-AI` | 112 | İddia 10 ✅ |
| 10 | `§2.3 Depodaki gerçek: shared/src/AI/CLAUDE.md ihlali` | 125 | T6 (7 PHP) |
| 11 | `§3 Kademe-2 envanter (9 çocuk)` | 142 | **C4** (eski iddia L59–73) |
| 12 | `§3.1 Kademe-2 sayım özeti` | 162 | EK §10 (ölçüm) |
| 13 | `§4 Sorumluluk matrisi` | 176 | **C6** · **C8** (eski iddia L87) |
| 14 | `§4.1 Sorumluluk × çocuk kapsama` | 190 | EK §7 (kapsam dışı 5 madde) |
| 15 | `§5 Bağımlılık özeti` | 207 | T1 · T5 |
| 16 | `§6 Doğrulama özeti` | 226 | — |
| 17 | `§7 Açık sorular` | 249 | **C7** (eski iddia L118) |
| 18 | `§8 Karar özeti` | 266 | — |
| 19 | `§9 Risk özeti` | 280 | — |
| 20 | `§10 Test stratejisi özeti` | 296 | EK §4.4 (test matrisi emsal) |
| 21 | `§11 Teknoloji / ekosistem` | 313 | EK §11 (URL taşıma) |
| 22 | `§11.1 Doğrulanmış aday envanteri` | 318 | — |
| 23 | `§11.2 UNKNOWN (kanıt yok — uydurulmaz)` | 348 | **C6** — 6 UNKNOWN satırı (L352–357) |
| 24 | `§12 Kaynaklar` | 361 | — |
| 25 | `§13 Set derinlik ölçümü` | 374 | **C10** |
| 26 | `§13.1 Kök 12 dosya — satır derinliği` | 379 | EK §10 |
| 27 | `§13.2 Çocuk klasörler — satır toplamı` | 396 | **C10** (L406 = 476 satır, 2026-10-13) |
| 28 | `§13.3 Sürüm / durum alanları` | 414 | — |
| 29 | `§14 Uyum & veri koruma haritası` | 429 | — |
| 30 | `§15 Doğrulama kanalı & CI yüzeyi` | 453 | T7 (2 workflow) |
| 31 | `§16 Aday teknoloji genişlemesi — 2. tur` | 471 | EK §11 |
| 32 | `§17 Ek kaynaklar` | 495 | EK §11 |
| 33 | `§18 Uygulama Yol Haritası (2026-10-14)` | 512 | **C11** |
| 34 | `§18.1 Faz tablosu` | 518 | Satır 525 = Faz 4 "MLflow registry (aday)" |
| 35 | `§18.2 Kurallar (H1–H7)` | 528 | EK §0 (H7: her fazda `-dogrulama.md`'ye satır) |
| 36 | `§18.3 Kapsam notları` | 534 | — |
| 37 | `§18.4 Açık kapılar` | 544 | Satır 550 = "Model format/runtime teknoloji ADR'si → UNKNOWN→ADR" |

**EK §8 sayımı:** 37 başlık · 11'i bu dosyada çapraz/karşıt kullanıldı · toplam **557 satır**
(T12).

---

## EK §9 — Korunan maddeler ve geçiş (append-only taahhüt)

| # | Taahhüt | Kanıt |
|---|---|---|
| P-1 | Dosyanın **1–53** satırı birebir önektir; silme · taşıma · yeniden yazma **yoktur** | ek öncesi sayım 53 (T14) + ek sonrası sayım EK §10 |
| P-2 | Frontmatter (L1–11) **değişmemiştir** — `group: kanit` korunur | L10 |
| P-3 | Orijinal **Sayım** satırı (L53) korunur; bu ek ayrı bir sayımla kapanır | L53 + EK §10 |
| P-4 | 2026-10-12 tarihli 32 iddia satırından **hiçbiri düzeltilemedi** — çelişkiler EK §2'de **yeni satır** olarak kaydedildi | EK §2 (C1–C11) |
| P-5 | **Yeni URL eklenmedi**; yalnız 6 URL (2026-10-12) yeniden taşındı | EK §11 |
| P-6 | Yazılan tek dosya bu dosyadır; `index.md` · `-kapsam.md` · `*.php/js/css/sql` · `.ai/AGENTS.md` · `.ai/CLAUDE.md` **dokunulmadı** | beyazliste kuralı |
| P-7 | `git commit` **atılmadı** — commit yetkisi orkestratöre aittir | kural |
| P-8 | `// ...` · `TBD` · doldurma satırı **yok**; her satır kaynak taşır | EK §0 |
| P-9 | Mojibake **yok** — UTF-8 Türkçe metin | gözlem |
| P-10 | `shell` bu oturumda izin dışı olduğundan PowerShell `ReadAllLines` **kullanılmadı**; satır sayısı `grep ^.*$` isabet sayısı ve `read` aralık testi ile alındı | T12 · T14 |

**EK §9 sayımı:** 10 taahhüt satırı.

---

## EK §10 — Ölçüm, kapılar ve bu ek sayımı

### EK §10.1 Dosya satır sayısı

| Aşama | Yöntem | Sonuç |
|---|---|---|
| Ek öncesi (2026-10-15) | `grep ^.*$` → 53 isabet | **53** |
| Ek sonrası (2026-10-15) | `read` aralık testi → toplam satır | **EK §10.3'te** |
| Eşik | ≥500 | **EK §10.3'te** |

### EK §10.2 Kapılar (bu tur)

| Kapı | Durum |
|---|---|
| Kapsam dışı dosya yazımı (beyazliste dışı) | **0** |
| `.ai/architecture/index.md` değişikliği | **0** |
| `*.php` / `*.js` / `*.css` / `*.sql` değişikliği | **0** |
| `.ai/AGENTS.md` · `.ai/CLAUDE.md` değişikliği | **0** |
| Yeni URL (2026-10-12 dışı) | **0** |
| `git commit` | **yok** |
| Frontmatter değişikliği | **0** |
| Silme /overwrite (eski satır) | **0** |
| Uydurma iddia / `// ...` doldurma | **0** |

### EK §10.3 Ek sayımı (yazım sonrası ölçüldü — 2026-10-15)

| Aşama | Yöntem | Sonuç |
|---|---|---|
| Ek öncesi | `grep ^.*$` → 53 isabet | **53** |
| Ek sonrası | `read` aralık testi → "Offset 9999 is out of range for this file (**644** lines)" | **644** |
| Ek satırı (644 − 53) | hesap | **591** |
| Eşik ≥500 | karşılaştırma | **✅ GEÇTİ** (644 ≥ 500) |

**Sonuç:** `model-management-dogrulama.md` → **644 satır · ≥500 ✅**.

---

## EK §11 — URL taşıma (yalnız 2026-10-12 kayıtları — yeni URL yok)

| # | URL | Taşıdığı iddia | İlk kayıt |
|---|---|---|---|
| 1 | https://mlflow.org/docs/latest/ml/model-registry/ | registry · lineage · alias · version tag · aşama geçişi | 2026-10-12 (İddia 1, 15–20, 24, 28) |
| 2 | https://mlflow.org/docs/latest/ml/model-registry/workflow/ | kayıt akışı · `register_model` · alias promosyonu · stages deprecated | 2026-10-12 (İddia 2, 21–23) |
| 3 | https://onnx.ai/onnx/repo-docs/Versioning | graph versioning · opset sürümü | 2026-10-12 (İddia 3) |
| 4 | https://onnx.ai/onnx/repo-docs/IR.html | `ir_version` — model dosyası biçim sürümü | 2026-10-12 (İddia 5) |
| 5 | https://scikit-learn.org/stable/model_persistence.html | pickle · joblib · cloudpickle + arbitrary code uyarısı | 2026-10-12 (İddia 4, 25) |
| 6 | https://joblib.readthedocs.io/en/latest/user_guide/persistence.html | `joblib.dump/load` · `mmap_mode` | 2026-10-12 (İddia 26–27) |

**EK §11 sayımı:** 6 URL · 0 yeni · 0 doğrulanmamış.

---

**Ek Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode ·
**Tarih:** 2026-10-15 · **Eklenti:** EK §0–§11 (append-only · 2026-10-12 tabanı korunmuştur)
