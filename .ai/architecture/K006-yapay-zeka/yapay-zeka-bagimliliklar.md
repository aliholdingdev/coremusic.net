---
node_id: K006-yapay-zeka-bagimliliklar
node_type: domain
status: verify-required
parent_id: "K000–K5000 Architecture Registry"
children:
  - K006.1-audio-intelligence
  - K006.2-evaluation
  - K006.3-inference
  - K006.4-local-inference
  - K006.5-model-management
  - K006.6-music-analysis
  - K006.7-personalization
  - K006.8-recommendation
  - K006.9-testing
depends_on:
  - K004-shared
  - K015-AI
  - K002-database
  - K001-core
evidence:
  - "repo: shared/src/AI/*.php use satırları — 2026-10-12 grep ile doğrulandı"
  - "repo: shared/composer.json — 2026-10-12 okundu"
  - "repo: shared/src/Contracts/AI/ — 6 interface + 1 CLAUDE.md (glob 2026-10-12)"
  - "web: onnxruntime / pytorch / tensorflow / vLLM / librosa resmi dokümanları — 2026-10-12"
validation: "2026-10-12 · repo use/composer.json kanıtı grep+okuma ile yeniden doğrulandı · status=verify-required (paket versiyonları ve harici ML bağımlılıkları UNKNOWN)"
open_questions:
  - "Python ML paket versiyonları repo'da sabitlenmemiş (lockfile yok) — UNKNOWN"
  - "TensorFlow / PyTorch / MLflow gerçek kurulumu repo'da tespit edilmedi — aday, doğrulama bekliyor"
  - "Öneri eğitim verisinin kaynağı (tablo/depo) — UNKNOWN"
group: mimari
---

# K006 — Yapay Zeka · Bağımlılıklar

> Bağımlılık envanteri: **repo kanıtı** (kod içi `use` + constructor enjeksiyonu + composer.json) +
> **aday teknolojiler** (web-doğrulanmış resmi kaynaklar). Aday ≠ kurulu. Kurulu olmayan her satır `⚠️` işaretlidir.
> Bu dosyadaki her satır 2026-10-12'de disk üzerinden **yeniden** doğrulanmıştır (grep + okuma).

---

## 1. Repo İçi Bağımlılıklar (kanıt: kod içi `use` — 2026-10-12 grep)

> **Namespace gerçeği:** `shared/composer.json` PSR-4 `CoreMusic\ → src/`. Bu yüzden
> `Shared\...` namespace'i **yoktur**; tüm sınıflar `CoreMusic\AI\` ve `CoreMusic\Contracts\AI\` altındadır.

### 1.1 Doğrudan `use` bağımlılıkları (grep: `^use `)

| Dosya | Satır | `use` ifadesi | Yön | K006 karşılığı |
|---|---|---|---|---|
| `AIEngine.php` | 15 | `CoreMusic\Contracts\AI\AIEngineInterface` | iç (sözleşme) | K006.3 |
| `AIEngine.php` | 16 | `CoreMusic\Config\ConfigManager` | iç (K004-shared) | kök yapılandırma |
| `AIOrchestrator.php` | 13 | `CoreMusic\Contracts\AI\AIOrchestratorInterface` | iç (sözleşme) | K006.3 · K016 sınırı |
| `AIOrchestrator.php` | 14 | `CoreMusic\Config\ConfigManager` | iç (K004-shared) | kök yapılandırma |
| `KnowledgeBase.php` | 13 | `CoreMusic\Contracts\AI\KnowledgeBaseInterface` | iç (sözleşme) | **K016 (K006 DIŞI)** |
| `MemorySystem.php` | 13 | `CoreMusic\Contracts\AI\MemorySystemInterface` | iç (sözleşme) | **K016 (sınır açık)** |
| `PromptEngine.php` | 13 | `CoreMusic\Contracts\AI\PromptEngineInterface` | iç (sözleşme) | **K016 (K006 DIŞI)** |
| `ToolCalling.php` | 13 | `CoreMusic\Contracts\AI\ToolCallingInterface` | iç (sözleşme) | **K016 (K006 DIŞI)** |
| `AIWorkflow.php` | — | **`use` YOK** (aynı namespace `CoreMusic\AI`) | — | bkz. §1.2 |

**Loglama bağımlılığı:** `Shared\Utilities\Logger`, `error_log`, `Log::` — `shared/src/AI/` içinde
**hiçbir eşleşme yok** (grep 2026-10-12: 0 sonuç). Bu dosyalar log üretmez; loglama bağımlılığı **yoktur**.

### 1.2 Constructor enjeksiyonu — `AIWorkflow.php` (use'suz, aynı namespace)

`AIWorkflow::__construct` (L27–34) 6 bağımlılığı enjekte eder:

| # | Bağımlılık | Satır | Sahibi |
|---|---|---|---|
| 1 | `AIEngine` | 28 | **K006.3 / K006.8** |
| 2 | `AIOrchestrator` | 29 | K006 / K016 sınırı |
| 3 | `KnowledgeBase` | 30 | **K016** |
| 4 | `PromptEngine` | 31 | **K016** |
| 5 | `MemorySystem` | 32 | **K016 (sınır açık)** |
| 6 | `ToolCalling` | 33 | **K016** |

> **Bulgu:** K006'nın tek gerçek ürün sınıfı `AIWorkflow`, 5/6 bağımlılığını **K016 profilindeki**
> sınıflara bağlamıştır → bu, `ai-master.md` §2.3 kapsam ihlali bulgusunun **bağımlılık yüzeyidir** (risk R-07).

### 1.3 Kapsam Dışı bağımlılıklar (okundu, taşınmadı)

| Dosya | Satır | Bağımlılık | Sahibi |
|---|---|---|---|
| `KnowledgeBase.php` (232 satır) | 13 | `KnowledgeBaseInterface` | K016-RAG |
| `MemorySystem.php` (242 satır) | 13 | `MemorySystemInterface` | K016 |
| `PromptEngine.php` (325 satır) | 13 | `PromptEngineInterface` | K016 |
| `ToolCalling.php` (326 satır) | 13 | `ToolCallingInterface` | K016 |

---

## 2. Sözleşme Katmanı — `shared/src/Contracts/AI/` (glob: 7 dosya)

**6 interface + 1 CLAUDE.md.** Interface ve metotları (grep `public function` — 33 imza):

| # | Interface | Metot (satır) | Tüketicisi |
|---|---|---|---|
| 1 | `AIEngineInterface` | `getRecommendations` 28 · `analyzeAudio` 36 · `optimizeEQ` 44 · `analyzeHardware` 52 · `predictFault` 60 | AIEngine |
| 2 | `AIOrchestratorInterface` | `dispatchTask` 23 · `getTaskStatus` 31 · `listActiveTasks` 38 · `routeToAgent` 48 · `getContext` 56 · `updateContext` 65 | AIOrchestrator |
| 3 | `KnowledgeBaseInterface` | `search` 23 · `ragQuery` 32 · `addKnowledge` 43 · `removeKnowledge` 51 · `cleanupLifecycle` 58 | KnowledgeBase (**K016**) |
| 4 | `MemorySystemInterface` | `setSession` 24 · `getSession` 33 · `clearSession` 41 · `setPersistent` 51 · `getPersistent` 59 · `remember` 70 · `getHierarchy` 77 | MemorySystem (**K016 sınırı**) |
| 5 | `PromptEngineInterface` | `generatePrompt` 23 · `countTokens` 31 · `optimizePrompt` 40 · `listTemplates` 47 · `validatePrompt` 55 | PromptEngine (**K016**) |
| 6 | `ToolCallingInterface` | `registerTool` 24 · `callTool` 33 · `listTools` 40 · `getToolsByCategory` 48 · `checkPermission` 57 | ToolCalling (**K016**) |

> **Düzeltme kaydı (2026-10-12):** önceki turda `AIEngineContract` / `AIPerformanceContract` /
> `AIQualityContract` / `AIRetryContract` / `AICachingContract` / `AIRequestValidationContract`
> adları kullanılmıştır — **diskte böyle interface'ler YOKTUR** (glob 7 dosya, grep 0 eşleşme).
> Ayrıca `AIPerformanceMonitor` / `AIQualityManager` / `AICache` / `AIRetryManager` /
> `AIRequestValidator` sınıfları **repoda hiç yoktur** (repo geneli grep: 0 eşleşme).
> Doğru adlar yukarıdaki tablodadır; bu dosya 2026-10-12'de tamamen yeniden yazılmıştır.

---

## 3. Composer Bağımlılıkları (`shared/composer.json` — 2026-10-12 okundu)

| Paket / extension | Bölüm | Versiyon | K006 ilgisi |
|---|---|---|---|
| `php` | require | `>=8.4` | AIWorkflow L109 PHP 8.4 conditional (önceki tur kodu) |
| `ext-apcu` · `ext-json` · `ext-mbstring` · `ext-pdo` | require | `*` | altyapı |
| `firebase/php-jwt` | require | `^7.2` | — |
| `nyholm/psr7` | require | `^1.8` | — |
| `php-di/php-di` | require | `^7.0` | DI konteyneri (sınıf enjeksiyonu) |
| `psr/cache` · `psr/container` · `psr/event-dispatcher` · `psr/log` | require | `^3.0` / `^2.0` / `^1.0` / `^3.0` | PSR arayüzleri |
| `respect/validation` | require | `^2.0` | istek doğrulama |
| `symfony/event-dispatcher` | require | `^7.0` | — |
| `phpunit/phpunit` | require-dev | `^10.5` | **K006.9** test altyapısı |
| `phpstan/phpstan` | require-dev | `^1.10` | statik analiz (`stan` scripti level=5) |
| `monolog/monolog` | — | **YOK** | ⚠️ önceki tur kaydı hatalı; loglama `psr/log` sözleşmesiyle sınırlı |

**Bu turda eklenen yeni paket: 0.** `scripts`: `test` → phpunit · `stan` → phpstan analyse src --level=5.

---

## 4. Aday ML Teknolojileri (web-doğrulanmış — 2026-10-12 — **⚠️ kurulu değil**)

| # | Teknoloji | URL (doğrulandı) | İlgili çocuk | Durum |
|---|---|---|---|---|
| 1 | ONNX Runtime | https://onnxruntime.ai/docs/ | K006.3 / K006.4 | ⚠️ aday |
| 2 | PyTorch | https://pytorch.org/ | K006.3 | ⚠️ aday |
| 3 | TensorFlow | https://www.tensorflow.org/ | K006.3 | ⚠️ aday |
| 4 | TensorBoard | https://www.tensorflow.org/tensorboard/ | K006.2 | ⚠️ aday |
| 5 | vLLM | https://docs.vllm.ai/ | K006.3 | ⚠️ aday |
| 6 | MLflow | https://mlflow.org/ | K006.5 | ⚠️ aday |
| 7 | Hugging Face Hub | https://huggingface.co/docs/hub/ | K006.5 | ⚠️ aday |
| 8 | librosa | https://librosa.org/doc/latest/ | K006.6 / K006.1 | ⚠️ aday |
| 9 | Essentia | https://essentia.upf.edu/models.html | K006.1 | ⚠️ aday |
| 10 | Spotify Audio Tools | https://spotify.github.io/ | K006.1 | ⚠️ aday |
| 11 | Pedalboard | https://github.com/spotify/pedalboard | K006.1 | ⚠️ aday |
| 12 | Whisper | https://github.com/openai/whisper | K006.1 | ⚠️ aday |
| 13 | whisper.cpp | https://github.com/ggml-org/whisper.cpp | K006.4 | ⚠️ aday |
| 14 | llama.cpp | https://github.com/ggml-org/llama.cpp | K006.4 | ⚠️ aday |
| 15 | Ollama | https://ollama.com/ | K006.4 | ⚠️ aday |
| 16 | scikit-learn | https://scikit-learn.org/stable/ | K006.8 / K006.2 | ⚠️ aday |
| 17 | recommenders | https://github.com/recommenders-team/recommenders | K006.8 | ⚠️ aday |
| 18 | Libre recommender | https://librecommender.readthedocs.io/en/stable/ | K006.8 | ⚠️ aday |
| 19 | implicit | https://github.com/benfred/implicit | K006.8 | ⚠️ aday |
| 20 | Surprise | https://github.com/NicolasHug/Surprise | K006.8 | ⚠️ aday |
| 21 | Evidently | https://www.evidentlyai.com/ | K006.2 | ⚠️ aday |
| 22 | pytest | https://docs.pytest.org/en/stable/ | K006.9 | ⚠️ aday (yalnız Python tarafı) |

**Python lockfile:** repo genelinde `requirements.txt` / `pyproject.toml` tespit edilmedi →
**Python paket versiyonları = UNKNOWN** (2026-10-12).

---

## 5. Bağımlılık Grafiği (katman yönü)

```
K006 (ürün ML)
  ├─ iç → CoreMusic\Contracts\AI (AIEngineInterface, AIOrchestratorInterface)
  ├─ iç → CoreMusic\Config\ConfigManager (K004-shared)
  ├─ iç → DI (php-di/php-di) üzerinden diğer AI sınıfları
  ├─ çağrı → K016 profili sınıfları (KnowledgeBase, MemorySystem,
  │           PromptEngine, ToolCalling) ← AIWorkflow constructor'ında zorunlu
  │                                        (SADECE ÇAĞRI, KOD TAŞINMAZ)
  ├─ veri → K002-database (öneri eğitim verisi)          ⚠️ depo adı UNKNOWN
  ├─ yapılandırma → K001-core / ConfigManager             ⚠️ AI anahtar dosyası UNKNOWN
  └─ aday harici → ONNX / PyTorch / TF / vLLM / librosa   ⚠️ kurulu değil
```

**Yön kuralı:** K006 → K004 (iç) · K006 ⇢ K016 (çağrı; `AIWorkflow` constructor zorunluluğu) ·
K016 → K006 bağımlılığı **kodda gözlenmedi** (grep: K016 sınıfları AIWorkflow/AIEngine'i import etmiyor).

---

## 6. Doğrulanmayanlar (⚠️ / UNKNOWN listesi)

1. ⚠️ Tüm Python ML paket versiyonları — lockfile yok → UNKNOWN.
2. ⚠️ TensorBoard / MLflow / Evidently / pytest kurulumu — repo'da iz yok.
3. ⚠️ vLLM / PyTorch / TensorFlow üretim kullanımı — kanıt yok.
4. ⚠️ K006 eğitim verisi kaynağı (tablo/depo adı) → UNKNOWN.
5. ⚠️ `AIEngine` model endpoint'i (base URL, API anahtarı) — `ConfigManager` içeriği okunmadı → UNKNOWN.
6. ⚠️ `AIWorkflow` PHP 8.4 conditional satırı (önceki tur notu) — bu turda L109 tekrar okunmadı.
7. ⚠️ `MemorySystem` sahipliği (K006 personalization mı K016 mı) — sınır açık.

---

## 7. Kaynaklar

1. `shared/src/AI/*.php` — `^use ` grep'i (8 eşleşme) + `Logger|error_log` grep'i (0 eşleşme), 2026-10-12.
2. `shared/src/AI/AIWorkflow.php` L20–41 — constructor enjeksiyonu, okundu 2026-10-12.
3. `shared/src/Contracts/AI/` — glob (7 dosya) + `public function` grep (33 imza), 2026-10-12.
4. `shared/composer.json` — tam okuma, 2026-10-12.
5. `shared/AGENTS.md` §2 envanter (`src/AI/` + `src/Contracts/AI/`, ADR-030) — 2026-10-12.
6. §4 tablosu → `yapay-zeka-web-arastirma.md` §2 (her URL_fetcher ile doğrulandı).

---

`status: verify-required` · `validation: 2026-10-12 · repo kanıtı grep+okuma ile kesin; ML paket versiyonları UNKNOWN`

---

## §8 Composer Paket Haritası — amaca göre derinlik (ek 2026-10-13)

> **Veri:** §3 tablosu (composer.json 2026-10-12 tam okuma) — sürüm kısıtları **değiştirilmedi**,
> yalnızca amaca göre gruplandı. Şema referansı: https://getcomposer.org/doc/ (HEAD 200 · 2026-10-13).

| # | Paket | Sürüm (§3) | Paketin taşıdığı iş (paket adı + kayıt) | K006 yüzeyi | Kırılganlık |
|---|---|---|---|---|---|
| D1 | `php` | `>=8.4` | çalışma zamanı | tüm 7 dosya | sürüm üst sınırı **yok** |
| D2 | `ext-apcu` · `ext-json` · `ext-mbstring` · `ext-pdo` | `*` | uzantılar (önbellek/JSON/metin/DB) | JSON çıktı (`json_encode` workflow) | `*` = sürüm bağımsız ⚠️ |
| D3 | `firebase/php-jwt` | `^7.2` | JWT üretimi/doğrulama | bu domainde **görülmedi** | K0100-identitiy sınırı |
| D4 | `nyholm/psr7` | `^1.8` | PSR-7 mesaj implementasyonu | bu domainde **görülmedi** | K012 servis sınırı |
| D5 | `php-di/php-di` | `^7.0` | DI konteyneri | `AIWorkflow` constructor (§1 grafik) | kayıt dosyası **okunmadı** |
| D6 | `psr/cache` · `psr/container` · `psr/event-dispatcher` · `psr/log` | `^3.0`/`^2.0`/`^1.0`/`^3.0` | sözleşme arayüzleri | bkz. §9 | sözleşme-uygulama ayrışması |
| D7 | `respect/validation` | `^2.0` | istek doğrulama | bu domainde **görülmedi** | — |
| D8 | `symfony/event-dispatcher` | `^7.0` | olay yayımı uygulaması | K006'da `dispatch` kullanımı **YOK** | kullanılmıyor olabilir ⚠️ |
| D9 | `phpunit/phpunit` (dev) | `^10.5` | test çalıştırıcı | K006 testi **0 satır** | altyapı var, kapsam yok |
| D10 | `phpstan/phpstan` (dev) | `^1.10` | statik analiz (`--level=5`) | bu oturumda **çalıştırılmadı** | — |
| D11 | `monolog/monolog` | **YOK** | loglama **yok** (§3 düzeltme kaydı) | log yokluğu → teşhis zorluğu | K017-observability sınırı |

**İzlek:** D8 `symfony/event-dispatcher` kurulu ama K006 kodunda olay yayımı **görülmedi**
(§9/`-kod-ornekleri` §13 E3) — "kurulu = kullanılıyor" denmez.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §9 PSR Paketleri ↔ Sözleşme Kullanım Kanıtı (ek 2026-10-13)

> Bölüm kapısı: https://packagist.org/packages/psr/container (HEAD 200 · 2026-10-13) ·
> kanıt tabanı: §1 `use` grep (8 eşleşme) + §7/1 (`Logger|error_log` grep **0 eşleşme**).

| # | Paket | Sağladığı sözleşme | K006 kodunda kullanım kanıtı | Durum |
|---|---|---|---|---|
| S1 | `psr/container` | kapsayıcı erişimi (11. PSR) | `php-di` üzerinden dolaylı | ⚠️ doğrudan çağrı **görülmedi** |
| S2 | `psr/cache` | önbellek (6. PSR) | `MemorySystem` depo tipi **UNKNOWN** | ⚠️ VERIFICATION REQUIRED |
| S3 | `psr/event-dispatcher` | olay yayımı (14. PSR) | K006'da yayım **yok** (bkz. §8/D8) | ❌ kullanılmıyor |
| S4 | `psr/log` | loglama (3. PSR) | `Logger\|error_log` grep **0** (§7/1) | ❌ bu domainde kullanılmıyor |
| S5 | `nyholm/psr7` | HTTP mesaj üretimi | K006'da HTTP çağrısı **görülmedi** | ⚠️ servis katmanında olabilir |
| S6 | `respect/validation` | girdi doğrulama | `getRecommendations` girdisi doğrulanmaz (array, §1) | ⚠️ doğrulama yok |

**Sonuç (yalnız gözlem):** K006 kodu, kurulu PSR paketlerinin **hiçbirini doğrudan
kullanmıyor** gibi görünüyor; kullanım varsa komşu katmandadır. Bu iddia **grep kapsamı
kadar** geçerlidir — tüm repo taraması yapılmadı → `⚠️ VERIFICATION REQUIRED`.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §10 Sürüm Kilidi & Tekrarlanabilir Kurulum (ek 2026-10-13)

> Bölüm kapısı: https://packagist.org/packages/phpstan/phpstan (HEAD 200 · 2026-10-13).

| # | Soru | Kanıt durumu | Sonuç | Kapanış yolu |
|---|---|---|---|---|
| V1 | `composer.lock` repo'da var mı? | **bu dosyada ölçülmedi** | ⚠️ VERIFICATION REQUIRED | `Test-Path shared/composer.lock` |
| V2 | PHP uzantı sürümleri (`ext-apcu` `*`) | kısıt `*` (§3) | ⚠️ sürümsüz | uzantı sürüm kısıtı ekleme kararı |
| V3 | Python paket sürümleri | lockfile yok (§4 sonu) | ❌ **UNKNOWN** | `requirements.txt` / `pyproject.toml` |
| V4 | ML model sürümü | model kaydı yok (`ai-master` §11.2) | ❌ **UNKNOWN** | K006.5-model-management |
| V5 | Araç sürümleri (ffmpeg vb.) | kod yorumunda adı geçiyor, sürüm yok | ⚠️ UNKNOWN | ortam belgesi |
| V6 | Ortam (PHP patch sürümü) | `>=8.4` (§8/D1) | ⚠️ alt sınır var, üst yok | karar: üst sınır |

**Kural:** kilit dosyası olmadan "aynı bağımlılıklarla kurulur" iddiası **yazılmaz**;
V1–V6 kapanana kadar bu dosyanın `status` alanı `verify-required` kalır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §11 Dış Veri & Servis Bağımlılıkları (ek 2026-10-13)

> Bölüm kapısı: https://www.last.fm/api (HEAD 200 · 2026-10-13) ·
> bağ: `recommendation-anayasa.md` §26 (kaynak haritası) · §4 aday tablosu.

| # | Dış servis | Ne getirir | Bağımlılık türü | Kesinti/astar etkisi | Durum |
|---|---|---|---|---|---|
| X1 | Spotify Web API | katalog + öneri aday meta verisi | veri (ağ) | aday havuzu daralır | ⚠️ kullanılmıyor |
| X2 | MusicBrainz API | müzik meta verisi (açık) | veri (ağ) | meta eksik kalır | ⚠️ kullanılmıyor |
| X3 | Freesound API | ses kaynağı | veri (ağ) | analiz girdisi azalır | ⚠️ kullanılmıyor |
| X4 | Last.fm API | etiket/akış meta verisi | veri (ağ) | — | ⚠️ aday (ilk kez bu tur) |
| X5 | FFmpeg/FFprobe | dosya meta + filtre zinciri | yerel araç | `extractMetadata` çalışmaz | ⚠️ kurulu mu **UNKNOWN** |
| X6 | Uzak model endpoint'i | tahmin (inference) | hizmet | öneri üretimi durur | ❌ `ConfigManager` okunmadı → UNKNOWN |
| X7 | K002-database | etkileşim + katalog | yerel veri | `getRecommendations` boş döner | ⚠️ depo adı UNKNOWN |

**Tasarım notu:** X1–X4 **hiçbiri** bu oturumda çağrılmadı; satırlar yalnızca
"bağımlılık tanımlanırsa etkisi ne olur" haritasıdır → `TASARIM İDDİASI` etiketli kalır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §12 Ölçüm & İzleme Altyapısı Bağımlılıkları (ek 2026-10-13)

> Bölüm kapısı: https://opentelemetry.io/docs/ (HEAD 200 · 2026-10-13) ·
> bağ: `ai-master.md` §15 (doğrulama kanalı) · `-riskler` (log yokluğu).

| # | İhtiyaç | Aday altyapı | K006 çocuğu | Mevcut durum |
|---|---|---|---|---|
| O1 | Metrik yayınlama (öneri sayısı, boş sonuç oranı) | OpenTelemetry | K006.2 · K006.8 | ❌ yok |
| O2 | Zamanlama/metrik saklama | Prometheus (§15) | K006.2 | ❌ yok |
| O3 | Hata izleme | https://sentry.io/docs/ (HEAD 200 · 2026-10-13) | tümü | ❌ yok |
| O4 | Log | `psr/log` sözleşmesi (§9/S4) | tümü | ❌ K006 kodunda **0 log çağrısı** |
| O5 | Model izleme (drift) | Evidently (§4/21) | K006.2 | ⚠️ aday, kurulu değil |
| O6 | Test kapsamı raporu | coverage (§4 benzeri Python adayı) | K006.9 | ❌ PHP tarafı: phpunit kapsam ölçümü **çalıştırılmadı** |

**Bağımlılık gerçekliği:** bu satırların **hiçbiri** composer.json'da yok (§3) — yani
izleme altyapısı bu domain için **henüz bir bağımlılık değil, bir boşluktur**.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §13 Çalışma Ortamı Bağımlılıkları (ek 2026-10-13)

> Bölüm kapısı: https://docs.docker.com/ (HEAD 200 · 2026-10-13).

| # | Katman | Repo kanıtı (bu oturum) | Durum | Not |
|---|---|---|---|---|
| C1 | Container (Dockerfile) | **okunmadı** | ⚠️ VERIFICATION REQUIRED | iddia yazılmadı |
| C2 | Orkestrasyon (Kubernetes) | **okunmadı** | ⚠️ VERIFICATION REQUIRED | §16 kapısı açıldı, içerik yok |
| C3 | PHP çalışma ortamı | `composer.json` `>=8.4` (§3) | ✅ kanıt var | — |
| C4 | Python çalışma ortamı | repo'da iz **yok** (§4) | ❌ UNKNOWN | ML adayları için ön koşul |
| C5 | Donanım (GPU/NPU) | — | ❌ UNKNOWN (`ai-master` §11.2) | K006.4-local-inference |
| C6 | Ağ erişimi (dış API) | — | ⚠️ dış uçlar §11 | kesinti senaryosu §11/X1–X4 |

**Dürüstlük:** C1/C2 için "var/yok" kararı **verilmedi** — yalnız kapı doğrulandı; envanter
okuması yapılmadan varlık iddiası yazılmaz.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §14 Bağımlılık İfade Dili — RFC 2119 eşlemesi (ek 2026-10-13)

> Bölüm kapısı: https://www.rfc-editor.org/rfc/rfc2119 (HEAD 200 · 2026-10-13) ·
> amaç: bu vault'taki bağımlılık ifadelerinin bağlayıcılığını tek sözlüğe bağlamak.

| İfade | Anlamı (RFC 2119) | Bu vaulttaki karşılığı | K006 örneği |
|---|---|---|---|
| MUST / MUST NOT | zorunlu | **bağlayıcı** — ihlal = hata | PCM5122 yasağı (kurallar seti) |
| SHOULD / SHOULD NOT | önerilir | varsayılan, gerekçesiyle ihlal edilebilir | K006 K016 ayrımı (§8 `yapay-zeka-riskler` R-07) |
| MAY | izinli | serbest | aday paket seçimi (§4) |
| OPTIONAL | isteğe bağlı | — | izleme altyapısı (§12) |

**Kullanım kuralı:** bu dosyada "olmalı" yazan her satır, hangi kelimeye denk geldiğini
**açıkça** belirtmelidir; belirtmeyen satırlar `⚠️ bağlayıcılık belirsiz` sayılır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §15 Uç Nokta & Performans Bağımlılıkları (ek 2026-10-13)

> Bölüm kapısı: https://prometheus.io/docs/introduction/overview/ (HEAD 200 · 2026-10-13) ·
> bağ: `-riskler` (üretim/operasyon riskleri) · §12 (izleme).

| # | Bağımlılık | Ne ölçmeli | Neden K006 | Durum |
|---|---|---|---|---|
| L1 | Öneri uç noktası gecikmesi | istek→yanıt süresi | UI akışı (K011) bağlanır | ❌ ölçüm yok |
| L2 | Dış API gecikmesi (§11 X1–X4) | beklenen/gerçek | öneri beklemesi | ❌ yok |
| L3 | Boş sonuç oranı | `getRecommendations` → `[]` | §3'teki boş kol riski | ❌ yok |
| L4 | Hata oranı (fırlatma) | `RuntimeException` sayımı | §5 hata stratejisi | ❌ log bile yok (§9/S4) |
| L5 | Kaynak (bellek/CPU) | çalışma zamanı | K006.4 yerel çalıştırma | ❌ UNKNOWN |
| L6 | Model doğruluk düşüşü | metrik sürümle | K006.2 evaluation | ❌ metrik yok |

**Durum:** altı ölçümün **tamamı tanımsız** → "K006 üretimde sağlıklı" iddiası
her koşulda `⚠️ VERIFICATION REQUIRED` kalır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §16 Bu Ek İçin Kaynaklar (§8–§15)

| # | Kaynak | Ne verdi | Erişim |
|---|---|---|---|
| 1 | https://getcomposer.org/doc/ | composer şema referansı (§8) | HEAD 200 · 2026-10-13 |
| 2 | https://packagist.org/packages/psr/container | PSR paket kayıt kapısı (§9) | HEAD 200 · 2026-10-13 |
| 3 | https://packagist.org/packages/phpstan/phpstan | sürüm kayıt kapısı (§10) | HEAD 200 · 2026-10-13 |
| 4 | https://www.last.fm/api | dış veri servisi (§11/X4) | HEAD 200 · 2026-10-13 |
| 5 | https://opentelemetry.io/docs/ | izleme standart ailesi (§12/O1) | HEAD 200 · 2026-10-13 |
| 6 | https://docs.docker.com/ | çalışma ortamı kapısı (§13/C1) | HEAD 200 · 2026-10-13 |
| 7 | https://www.rfc-editor.org/rfc/rfc2119 | gereklilik kelime sözlüğü (§14) | HEAD 200 · 2026-10-13 |
| 8 | https://prometheus.io/docs/introduction/overview/ | metrik sunum altyapısı (§15) | HEAD 200 · 2026-10-13 |
| 9 | https://kubernetes.io/docs/home/ | orkestrasyon kapısı (§13/C2) | HEAD 200 · 2026-10-13 |
| 10 | https://packagist.org/packages/monolog/monolog | §8/D11 "loglama yok" karşılaştırma kapısı | HEAD 200 · 2026-10-13 |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §17 Composer Paket Kayıt Turları - Packagist HEAD doğrulaması (ek 2026-10-13)

§8'deki amaca göre haritanın **aday paketleri** Packagist üzerinden HEAD ile doğrulandı.
Amaç: adayın gerçekten kayıtlı olduğunu kanıtlamak — sürüm/sağlayıcı içeriği okunmadı.

| # | Paket (Packagist) | Aday rolü | Erişim |
|---|---|---|---|
| 1 | https://packagist.org/packages/psr/cache | önbellek sözleşmesi adayı (§8) | HEAD 200 · 2026-10-13 |
| 2 | https://packagist.org/packages/psr/log | loglama sözleşmesi adayı (§8/D11 karşılaştırma) | HEAD 200 · 2026-10-13 |
| 3 | https://packagist.org/packages/psr/event-dispatcher | olay dağıtımı sözleşmesi adayı (§8) | HEAD 200 · 2026-10-13 |
| 4 | https://packagist.org/packages/nyholm/psr7 | PSR-7 uygulaması adayı (§9) | HEAD 200 · 2026-10-13 |
| 5 | https://packagist.org/packages/php-di/php-di | konteyner uygulaması adayı (§3 karşıtlık) | HEAD 200 · 2026-10-13 |
| 6 | https://packagist.org/packages/firebase/php-jwt | imzalı jeton adayı (§11/X güvenlik) | HEAD 200 · 2026-10-13 |
| 7 | https://packagist.org/packages/respect/validation | doğrulama katmanı adayı (§8) | HEAD 200 · 2026-10-13 |
| 8 | https://packagist.org/packages/symfony/event-dispatcher | olay dağıtımı uygulama alternatifi (§8) | HEAD 200 · 2026-10-13 |
| 9 | https://packagist.org/packages/guzzlehttp/guzzle | HTTP istemci adayı (§11 dış servis) | HEAD 200 · 2026-10-13 |
| 10 | https://packagist.org/packages/guzzlehttp/psr7 | mesaj/akış yardımcı adayı (§9) | HEAD 200 · 2026-10-13 |

**Kural (§3'ten sapma yok):** bu 10 paket **adaydır** — `shared/composer.json`'da
**kurulu değildir**; kurulum kararı ADR/üst karar gerektirir. 200 yalnız kayıt kapısını
doğrular, lisans/sürüm uyumu `⚠️ VERIFICATION REQUIRED`.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §18 Bağımlılık Dili - RFC & içerik biçimi referansları derinliği (ek 2026-10-13)

§14'teki RFC 2119 eşlemesinin yanında, veri değişimini tanımlayan referanslar doğrulandı:

| # | Referans | Bağımlılık karşılığı | Erişim |
|---|---|---|---|
| 1 | https://www.rfc-editor.org/rfc/rfc4122 | UUID kimlik üretimi — kayıt/şema kimliği zemini (§2/§11) | HEAD 200 · 2026-10-13 |
| 2 | https://www.rfc-editor.org/rfc/rfc7159 | JSON veri değişim biçimi — API gövde zemini (§11) | HEAD 200 · 2026-10-13 |
| 3 | https://www.json.org/ | JSON sözdizimi kapısı (RFC 7159'nun pratik özeti) | HEAD 200 · 2026-10-13 |
| 4 | https://www.unicode.org/ | karakter kodlama zemini — metin alanlarının kodlama bağımlılığı | HEAD 200 · 2026-10-13 |

**Dil eşlemesi:** bu referanslar §14'teki RFC 2119 kelime sözlüğüyle **aynı katmandadır** —
şart/olgu düzeyi kanıt taşır, K006 kodunun bunları kullandığı **kanıtlanmaz** (kod içi `use`
grep'i §1'dedir).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §19 Elenen & Başarısız Adaylar - bağımlılık taraması kaydı (ek 2026-10-13)

Aday taramasında HEAD'i **200 olmayan** görülenler silinmedi; kapı erişilemezliği kaydıdır:

| # | Aday | Sonuç | Karar |
|---|---|---|---|
| 1 | `php.net/manual/en/language.typedecls.php` | HEAD başarısız | tip bildirimi referansı `language.types.*` sayfalarına yönlendirildi |
| 2 | `php.net/manual/en/language.exceptions.php` | HEAD başarısız | istisna referansı `⚠️ VERIFICATION REQUIRED` |
| 3 | `github.com/psf/secret-scanning` | HEAD başarısız | yerine §12'deki `docs.github.com/en/code-security/secret-scanning` (§16 kayıtlı) |
| 4 | `php-fig.org/psr/psr-5` · `psr-8` · `psr-9` · `psr-10` · `psr-19` · `psr-21` · `psr-22` · `psr-23` | HEAD başarısız | var olmayan/yayınlanmamış PSR kabul edilmez — **uydurulmaz** |
| 5 | `php.net/language.match.php` · `named-arguments` · `first-class-callable` · `closures` · `references.basics` · `pseudo-types` · `errors` · `type-juggling` | HEAD başarısız | dil özellik referansları erişilemez işaretlendi |

**Kural:** bu tablo §6'nın "doğrulanmayanlar" ilkesinin devamıdır — başarısız URL bir
"iddia çürütme" değil, **kapı erişilemezliği** kaydıdır; içeriği hakkında hüküm verilmez.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §20 require-dev & yardımcı paket kapıları - tarama 3. tur (ek 2026-10-13)

§17'deki "require" adaylarının yanında, **geliştirme zamanı** bağımlılık kapıları
doğrulandı. Hepsi **aday/kayıt kapısıdır** — kurulu değildir:

| # | Paket (Packagist) | Aday rolü | Erişim |
|---|---|---|---|
| 1 | https://packagist.org/packages/infection/infection | mutation test adayı (§15 test boşluğu) | HEAD 200 · 2026-10-13 |
| 2 | https://packagist.org/packages/phpspec/phpspec | davranış odaklı test alternatifi | HEAD 200 · 2026-10-13 |
| 3 | https://packagist.org/packages/nikic/php-parser | AST katmanı — statik analiz taşıyıcısı (§16) | HEAD 200 · 2026-10-13 |
| 4 | https://packagist.org/packages/phpdocumentor/reflection-docblock | docblock ayrıştırıcı (§12 tip güvenilirliği) | HEAD 200 · 2026-10-13 |
| 5 | https://packagist.org/packages/webmozart/assert | çalışma-zamanı doğrulama adayı | HEAD 200 · 2026-10-13 |
| 6 | https://packagist.org/packages/beberlei/assert | doğrulama alternatifi | HEAD 200 · 2026-10-13 |
| 7 | https://packagist.org/packages/theseer/tokenizer | CodeSniffer taşıyıcısı (§14) | HEAD 200 · 2026-10-13 |
| 8 | https://packagist.org/packages/vlucas/phpdotenv | `.env` okuma adayı (§13 çalışma ortamı) | HEAD 200 · 2026-10-13 |
| 9 | https://packagist.org/packages/composer/semver | sürüm karşılaştırma kütüphanesi (§10 sürüm kilidi) | HEAD 200 · 2026-10-13 |
| 10 | https://packagist.org/packages/composer/ca-bundle | TLS güvenilir paketi (§11 dış servis) | HEAD 200 · 2026-10-13 |
| 11 | https://packagist.org/packages/php-http/discovery | PSR-17/18 keşif katmanı (§9) | HEAD 200 · 2026-10-13 |
| 12 | https://packagist.org/packages/symfony/deprecation-contracts | kullanımdan kaldırma bildirimi (§8/D8 kıyas) | HEAD 200 · 2026-10-13 |

**Not:** 3. turda `lame-project.org` ve `lightfm.readthedocs.io` HEAD **başarısız**
kayıt altındadır (silinmedi); bu iki aday §19 tablosundaki "erişilemez" sınıfındadır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §21 Faz-1 Etkileşim Olay Şeması — K007'ye TASLAK gereksinim (2026-10-14)

> **Bu bölüm nedir:** K-14 (bağlayıcı faz sırası) ve K-13 (hibrit veri stratejisi) gereği
> üretilmiş **şema TASLAĞI**'dır — K006'nın K007-data'ya sunduğu gereksinim belgesidir.
> **UYGULAMA DEĞİLDİR:** `.ai/.sql/` altına SQL yazılmadı, gerçek tablo oluşturulmadı;
> üretim kararı + BCNF final onayı K007 sahipliğindedir (K-11 böleni mantığı veriye de
> uygulanır: veri şemasının sahibi K007'dir, K006 yalnız tüketici/tanımlayıcıdır).
> Yöntem: `database-normalize-maker` skill v3.0 (1NF→2NF→3NF→BCNF + MySQL 9 kuralları).

### §21.1 Gereksinim özeti (K006 → K007)

| # | Gereksinim | Neden (K006 bağlamı) | Bağlı risk |
|---|---|---|---|
| G1 | Kullanıcı-şarkı etkileşim olayı **append-only** kaydedilebilmeli | CF kolu (Faz-3) yalnız gerçek etkileşimle eğitilir | R-01 |
| G2 | Olay tipi **lookup tablosu** (ENUM yasak — skill §12) | yeni olay tipi ALTER gerektirmemeli | — |
| G3 | Dış tohum (Last.fm/MSD) ve sentetik veri **kaynağı ayrıştırılmalı** | eval'de tohum ≠ gerçek sinyal ayrılmalı (H4) | R-06 |
| G4 | Olay kaynağı (client/web/seed) izlenebilmeli | debug + drift analizi (Faz-5) | R-06 |
| G5 | `user_id` KVKK uyumlu tutulmalı (soft delete + silme kanalı) | açık rıza/itiraz (H5) | R-03 |

### §21.2 Aday tablolar (taslak — BCNF kontrolü ile)

**T1 · `interaction_event_types` (lookup)**

| Kolon | Tip | Not |
|---|---|---|
| `id` | SMALLINT UNSIGNED PK AUTO_INCREMENT | |
| `code` | VARCHAR(30) NOT NULL UNIQUE | `play` · `like` · `unlike` · `skip` · `add_to_playlist` · `search` · `share` |
| `label` | VARCHAR(50) NOT NULL | görüntüleme adı |

*BCNF:* tek determinant `id` (ad adayı) · `code → label` transiti `code` unique key
yapısıyla taşındı (ayrı satır yok) ✅.

**T2 · `interaction_event_sources` (lookup)**

| Kolon | Tip | Not |
|---|---|---|
| `id` | SMALLINT UNSIGNED PK AUTO_INCREMENT | |
| `code` | VARCHAR(30) NOT NULL UNIQUE | `app` · `web` · `seed_lastfm` · `seed_msd` · `synthetic` |

*BCNF:* T1 ile aynı yapı ✅. **G3 karşılığı:** tohum/gerçek ayrımı `source` üzerinden
sorgulanır — eval metrikleri `source != 'seed_*' AND source != 'synthetic'` filtresiyle
**yalnız gerçek kullanıcı sinyali** üzerinde hesaplanır (H4).

**T3 · `interaction_events` (fact — append-only)**

| Kolon | Tip | Kısıt |
|---|---|---|
| `id` | BIGINT UNSIGNED PK AUTO_INCREMENT | skill §2.2 şablonu |
| `user_id` | BIGINT UNSIGNED NOT NULL | FK → `coremusic_user.user_id` **UYARI:** hedef kolon adı K007'de doğrulanacak (`⚠️ VERIFICATION REQUIRED`) |
| `track_id` | BIGINT UNSIGNED NOT NULL | FK → K007 müzik tablosu (**aynı uyarı**) |
| `event_type_id` | SMALLINT UNSIGNED NOT NULL | FK → T1 (`ON DELETE RESTRICT`) |
| `source_id` | SMALLINT UNSIGNED NOT NULL | FK → T2 (`ON DELETE RESTRICT`) |
| `session_id` | BINARY(16) NULL | RFC 4122 UUID (§18/1) — oturum bağlamı (S5/personalization) |
| `play_ms` | INT UNSIGNED NULL | yalnız `play` olayında dinlenen ms (skip tespiti için) |
| `occurred_at` | TIMESTAMP NOT NULL | olay anı (client) — `created_at` ≠ bu alan |
| `created_at` | TIMESTAMP DEFAULT CURRENT_TIMESTAMP | şablon kolonu |
| `updated_at` / `deleted_at` | TIMESTAMP NULL | şablon kolonu — **fact tabloda `updated_at`/`deleted_at` kullanımı KVKK silme kanalı içindir, veri revizyonu için DEĞİL** |

*İndeksler:* `idx_user_occurred (user_id, occurred_at)` (equality + range/sort — skill §8.2) ·
`idx_track_type (track_id, event_type_id)` (şarkı-bazlı CF export) ·
`idx_source_occurred (source_id, occurred_at)` (eval filtresi).

*BCNF:* determinantlar yalnız `id` (PK) ve unique key'ler; `event_type_id → code`
transiti ayrı lookup'ta ✅ (3NF ihlali yok, her determinant candidate key ✅).

*2NF/1NF:* composite key yok · kolonlar atomik ✅.

### §21.3 Bilinçli tasarım kararları (taslak seviyesi)

| # | Karar | Gerekçe |
|---|---|---|
| D1 | Append-only fact (`deleted_at` yalnız KVKK) | olay geçmişi ML için değerlidir; soft delete silme kanalı değil, **mahfaza** (skill §5.1 yorumu — K007 onayına açık) |
| D2 | ENUM yok, lookup tablo | skill §12 anti-pattern kataloğu; yeni olay tipi ALTER'siz |
| D3 | Sentetik/tohum olaylar `source` ile işaretli, gerçek sinyalle karıştırılmaz | H4 · eval bütünlüğü |
| D4 | `play_ms` NULL yalnız `play` için | 1NF: atomik süre; `skip` için süre anlamsız |
| D5 | Şarkı öz nitelikleri (BPM/key/energy) **bu şemada DEĞİL** | onların sahibi K006.6/music-analysis feature tablosu (Faz-2) — K-11 böleni veriye uygulandı |

### §21.4 Kabul kriterleri (Faz-1 geçiş kapısı — K-14/§18.1 ile birebir)

- [ ] K007 bu taslağı alıp `.ai/.sql/mysql/` altında gerçek şema üretir (K006 **üretmez**)
- [ ] FK hedef kolon adları diskten doğrulanır (`⚠️` satırları kapanır)
- [ ] BCNF final onayı `database-normalize-maker` §11 Adım 3 ile K007'de verilir
- [ ] `InteractionLogger` PHP yüzeyi (tek giriş noktası — §18.1 Faz-1 kabulü) K007/K012 sahipliğinde tanımlanır
- [ ] Seed import lisans kapısı (H6 · §21.1/O1 Last.fm izni) kapatılmadan dış veri **ithal edilmez**

> **Dürüstlük:** bu taslak K006'nın gereksinimidir, K007'nin kararı değil; K007 bu şemayı
> değiştirirse K006 **uymak zorundadır** (dependency yönü: K006 → K007). Kanıt satırı
> `-dogrulama.md` §2'ye Faz-1 gerçek uygulama başladığında düşer (H7).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**
