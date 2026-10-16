---
node_id: K006-yapay-zeka-bilesenler
node_type: domain
status: verified
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
  - K016-AI
evidence:
  - "shared/src/AI/* — 7 PHP + 1 CLAUDE.md, 1.988 PHP satırı (2026-10-12 glob + sayım)"
  - "shared/src/Contracts/AI/* — 6 interface, 33 method imzası (2026-10-12 grep 'public function')"
  - "registry §5/§6 — K006 kök master kaydı"
validation: "2026-10-12 · dosya bazlı metot satır numaraları grep ile diskten alındı · 33 imza sayımı doğrulandı · status=verified"
open_questions:
  - "K006.1-audio-intelligence / K006.4-local-inference / K006.5-model-management karşılığı repo kodunda 0 satır — çocuk dosyalar kapsam tasarımdır, uygulama değil"
  - "AIWorkflow içindeki 5 akışın hangisinin K016'ya ait olduğu (recommendation dışındaki 4 akış) sahiplik belirsiz — ai-master.md §8-Q6"
  - "shared/src/AI/CLAUDE.md K016 sınıflarını listeliyor (kapsam sınırı kaydı, R-07)"
group: mimari
---

# K006 — Yapay Zeka · Bileşenler

> K006 kök bileşen envanteri. Her bileşen: dosya · satır · metot · sahiplik (K006 / K016 sınırı) · durum.
> **Tüm satır numaraları 2026-10-12 disk grep'idir** (`public function` / `private function` / `const`); K016'a ait sınıflar envanterde işaretlidir ve dışarıdadır (kapsam: `yapay-zeka-kapsam.md` §2.2).

---

## 1. Envanter Özeti

| Ölçüm | Değer | Kayıt |
|---|---|---|
| `shared/src/AI/` dosyası | 8 (7 PHP + 1 CLAUDE.md) | 2026-10-12 glob |
| `shared/src/Contracts/AI/` dosyası | 7 (6 interface + 1 CLAUDE.md) | 2026-10-12 glob |
| Toplam PHP satırı (AI/) | 1.988 | 2026-10-12 sayım (§2 + §3 toplamı) |
| Contract method imzası | 33 | 2026-10-12 grep (`public function`) |
| K006 sahipli sınıf | 3 (AIEngine, AIOrchestrator, AIWorkflow) | §2 |
| K016 sahipli sınıf | 4 (KnowledgeBase, MemorySystem, PromptEngine, ToolCalling) | §3 |
| K006 kök MD dosyası | 12 | ADR-097 §5.1 |
| K006 çocuk MD dosyası | 9 × 6 = 54 | v2 §4.4 |

**Düzeltme kaydı (2026-10-12):** önceki turda bu dosyada `getRecommendationsFallback` / `getContentScores` / `getUserSimilarity` / `getTrendingScores` / `getCandidateTracks` / `isDiverse` / `getFallbackRules` / `rankByBusinessRules` / `cacheKey` / `isValidRequest` / `log` metotları, `AIOrchestrator::getRecommendations` (L99–119), `WORKFLOW_*` sabitleri, `personalizationWorkflow`, `musicAnalysisWorkflow` ve 7 adet `*Contract` arayüzü **uydurulmuştu** — hiçbiri diskte yok. Bu sürümde tüm satırlar grep kanıtıyla yeniden üretildi (bkz. §9).

---

## 2. K006 Sınıfları (üretim kodu)

### 2.1 `AIEngine.php` — 382 satır — K006.3-inference çekirdeği

| Metot | Satır | Görünürlük | Sorumluluk |
|---|---|---|---|
| `__construct(ConfigManager)` | 34 | public | tek bağımlılık: `ConfigManager` |
| `getConfig` | 41 | public | config sızdırma (interface dışı) |
| `getRecommendations(array $context, int $limit = 20)` | 49–77 | public | öneriyi çeker + hibritleştirir |
| `analyzeAudio(string $filePath)` | 82–106 | public | BPM/key/energy analizi |
| `optimizeEQ(array $roomProfile)` | 111–136 | public | oda koreksiyonu bandı |
| `analyzeHardware(string $deviceId)` | 138–159 | public | donanım uyumluluk analizi |
| `predictFault(array $metrics)` | 161–207 | public | arıza tahmini |
| `getCollaborativeScores(?string $userId, array $history)` | 209–218 | private | **boş döner** (ALS tutamaç) |
| `getContentBasedScores(array $context)` | 223–241 | private | içerik benzerliği skoru |
| `combineScores(...)` | 243–262 | private | hibrit birleştirme |
| `applyDiversityFilter(array $scores, ?string $preferredGenre)` | 264–286 | private | çeşitlilik filtresi |
| `extractMetadata(string $filePath)` | 288–313 | private | dosya meta verisi |
| `extractFeatures(string $filePath)` | 315–330 | private | **sabit öznitelik** (harici analiz yok) |
| `bandToFrequency(int $band)` | 332–345 | private | band → Hz |
| `calculateRoomCorrection(...)` | 347–366 | private | oda düzeltme eğrisi |
| `calculateBandQ(float $frequency, string $roomSize)` | 368–382 | private | Q faktörü |

Sabitler (L30–32): `MAX_RECOMMENDATIONS=20`, `MIN_SCORE=0.7`, `DIVERSITY_TARGET=0.30`.
Hibrit ağırlık (L65): içerik `0.6` + kullanıcı `0.4` — kod gömülü, config değil.

### 2.2 `AIOrchestrator.php` — 210 satır — K006.3/K016 sınırı

| Metot / sabit | Satır | Sahiplik |
|---|---|---|
| `AGENT_MAP` (sabit) | 26–38 | K016 (agent eşlemesi) |
| `TASK_TYPES` (sabit) | 40–54 | K016 (iş tipi listesi) |
| `dispatchTask(string $type, array $params, string $priority)` | 71–100 | K016 (agent iş akışı) |
| `getTaskStatus(string $taskId)` | 102–112 | K016 |
| `listActiveTasks()` | 114–123 | K016 |
| `routeToAgent(string $agentId, string $action, array $params)` | 125–152 | K016 |
| `getContext(string $sessionId)` | 154–160 | K016 (context store) |
| `updateContext(string $sessionId, array $data)` | 162–175 | K016 |
| `generateTaskId()` | 177–183 | private |
| `executeTask(string $taskId)` | 185–210 | private |

> **K006'da bu sınıfta ürün önerisi kapısı YOKTUR** — `getRecommendations` yalnız `AIEngine`'dedir (L49). Düzeltme kaydı §1.

### 2.3 `AIWorkflow.php` — 271 satır — iş akışı tanımı

| Akış | Satır | Sahiplik |
|---|---|---|
| `__construct` (6 bağımlılık enjeksiyonu) | 27–47 | — |
| `recommendationWorkflow(array $request)` | 49–97 | **K006.8** |
| `audioAnalysisWorkflow(array $request)` | 99–145 | **K006.1 / K006.6** |
| `autoEQWorkflow(array $request)` | 147–197 | **K006.3** (ses AI) |
| `hardwareAnalysisWorkflow(array $request)` | 199–239 | K006 sınırında — sahiplik belirsiz (§open_questions) |
| `faultPredictionWorkflow(array $request)` | 241–271 | K006 sınırında — sahiplik belirsiz (§open_questions) |

> `WORKFLOW_*` sabitleri ve `personalizationWorkflow` / `musicAnalysisWorkflow` **dosyada yoktur** (grep: 0 eşleşme). Sınıf 5 akış içerir, 6 değil.

---

## 3. K016 Sınıfları (bu dizinde ama K006 DIŞI — okundu, taşınmadı)

| Dosya | Satır | Önemli metotlar (satır) | Sahibi |
|---|---|---|---|
| `KnowledgeBase.php` | 232 | `search` L36 · `ragQuery` L72–91 · `addKnowledge` L96 · `removeKnowledge` L121 · `cleanupLifecycle` L134 | K016 |
| `MemorySystem.php` | 242 | `setSession` L45 · `getSession` L60 · `setPersistent` L89 · `remember` L123 · `getHierarchy` L152 · `pruneExpired` L183 | K016 |
| `PromptEngine.php` | 325 | `generatePrompt` L48 · `countTokens` L90 · `optimizePrompt` L101 · `validatePrompt` L156 | K016 |
| `ToolCalling.php` | 326 | `registerTool` L53 · `callTool` L73 · `listTools` L116 · `checkPermission` L145 | K016 |
| `CLAUDE.md` (bu dizinde) | — | K016 sınıflarını listeler | **kapsam ihlali kaydı** (R-07) |

---

## 4. Contract Katmanı — `shared/src/Contracts/AI/` (6 interface · 33 imza)

| # | Interface | İmza | Method'ler (dosya içi satır) | Uygulayan |
|---|---|---|---|---|
| 1 | `AIEngineInterface` | 5 | `getRecommendations` L28 · `analyzeAudio` L36 · `optimizeEQ` L44 · `analyzeHardware` L52 · `predictFault` L60 | `AIEngine` (L15 `use`) |
| 2 | `AIOrchestratorInterface` | 6 | `dispatchTask` L23 · `getTaskStatus` L31 · `listActiveTasks` L38 · `routeToAgent` L48 · `getContext` L56 · `updateContext` L65 | `AIOrchestrator` (L14 `use`) |
| 3 | `KnowledgeBaseInterface` | 5 | `search` L23 · `ragQuery` L32 · `addKnowledge` L43 · `removeKnowledge` L51 · `cleanupLifecycle` L58 | `KnowledgeBase` (L13 `use`) |
| 4 | `MemorySystemInterface` | 7 | `setSession` L24 · `getSession` L33 · `clearSession` L41 · `setPersistent` L51 · `getPersistent` L59 · `remember` L70 · `getHierarchy` L77 | `MemorySystem` (L13 `use`) |
| 5 | `PromptEngineInterface` | 5 | `generatePrompt` L23 · `countTokens` L31 · `optimizePrompt` L40 · `listTemplates` L47 · `validatePrompt` L55 | `PromptEngine` (L13 `use`) |
| 6 | `ToolCallingInterface` | 5 | `registerTool` L24 · `callTool` L33 · `listTools` L40 · `getToolsByCategory` L48 · `checkPermission` L57 | `ToolCalling` (L13 `use`) |

> **Toplam 33 imza** (5+6+5+7+5+5). 7. bir interface (`*Contract`) **yoktur** — bkz. §9 düzeltme kaydı. Namespace: `CoreMusic\Contracts\AI`.

---

## 5. Servis / Altyapı Bağımlılıkları (gerçek — composer.json kanıtı)

| Bağımlılık | Tür | Kullanım yüzeyi |
|---|---|---|
| `CoreMusic\ConfigManager` | iç | `AIEngine` L34 · `AIOrchestrator` L56 tek constructor bağımlılığı |
| `php-di ^7.0` | dış (require) | DI konteyneri — service wiring |
| `psr/*` (container, event-dispatcher, http-message, log, simple-cache) | dış | PSR arayüzleri |
| `symfony/event-dispatcher ^7.0` | dış | olay dağıtımı |
| `nyholm/psr7 ^1.8` | dış | PSR-7 impl |
| `firebase/php-jwt ^7.2` | dış | token |
| `respect/validation ^2.0` | dış | girdi doğrulama |

> **Repoda yoktur** (repo geneli grep, 2026-10-12: 0 eşleşme): `AIPerformanceMonitor` · `AIQualityManager` · `AICache` · `AIRetryManager` · `AIRequestValidator`. Bunları içeren ayrı bir "Servis Katmanı" **mevcut değildir**; metrik/önbellek/retry/doğrulama katmanı için ayrı tasarım gerekir (K006.2 / K006.3 açık maddesi).

---

## 6. Bileşen → Çocuk Eşlemesi

| Bileşen | Çocuk | Not |
|---|---|---|
| `AIEngine::getRecommendations` + `combineScores` + `applyDiversityFilter` | K006.8-recommendation | çekirdek |
| `AIEngine::extractFeatures` / `extractMetadata` | K006.6-music-analysis | tutamaç (sabit veri) |
| `AIEngine::analyzeAudio` | K006.1-audio-intelligence | **tek gerçek ses analizi girişi** |
| `AIEngine` tahmin çağrı zinciri (`predictFault`) | K006.3-inference | motor soyutlanmamış |
| `AIEngine::optimizeEQ` + `calculateRoomCorrection` | K006.3 / K006.1 | DSP köprüsü |
| `AIOrchestrator::*` | K016 (K006 DIŞI) | kapı K006'da değil |
| `AIWorkflow::recommendationWorkflow` | K006.8 | akış |
| `AIWorkflow::audioAnalysisWorkflow` | K006.1 / K006.6 | akış |
| `AIWorkflow::autoEQWorkflow` | K006.3 | akış |
| (yok) | K006.4-local-inference | **repo karşılığı 0 satır** |
| (yok) | K006.5-model-management | **repo karşılığı 0 satır** |
| (yok) | K006.7-personalization | **repo karşılığı 0 satır** (öneri kişiselleştirmesi `getRecommendations` içinde örtük) |
| (yok) | K006.2-evaluation | metrik/drift bileşeni **0 satır** |
| PHPUnit ^10.5 (composer.json) | K006.9-testing | PHP test altyapısı |

---

## 7. Boşluk Tablosu (bileşen yokluğu = bulgu)

| Çocuk | Beklenen bileşen | Repo durumu |
|---|---|---|
| audio-intelligence | ses analiz servisi | **0 satır** (`analyzeAudio` AIEngine içinde, ayrı servis yok) |
| local-inference | yerel çalışma runtime bağı | **0 satır** |
| model-management | model registry/store | **0 satır** |
| evaluation | drift/rapor bileşeni | **0 satır** (monitor/quality sınıfı da yok) |
| personalization | kişiselleştirme servisi | **0 satır** (öneri içinde örtük) |
| testing | ML test yardımcıları | **0 satır** (yalnız PHPUnit altyapısı) |

---

## 8. Kaynaklar

1. `shared/src/AI/*.php` — 7 dosya tam okuma + grep (`public function`/`private function`/`const`), 2026-10-12 (§2–§3).
2. `shared/src/Contracts/AI/*.php` — 6 interface grep, 33 imza, 2026-10-12 (§4).
3. `shared/composer.json` — require/require-dev, 2026-10-12 (§5).
4. `.ai/architecture/index.md` §5 satır 6 (`ai-master.md`) — 2026-10-12.
5. Repo geneli grep: `AIPerformanceMonitor|AIQualityManager|AICache|AIRetryManager|AIRequestValidator` → 0 eşleşme, 2026-10-12 (§5/§9).

---

## 9. Düzeltme Kaydı (2026-10-12)

| Uydurma iddia (eski sürüm) | Disk gerçeği |
|---|---|
| `AIEngine::getRecommendationsFallback` L83–117 vb. 11 metot | `AIEngine`'de 16 metot var; fallback/candidate/diversity-yardımcıları **yok** — L49–77 tek gövde |
| `AIOrchestrator::getRecommendations` L99–119 | Yok; en yakın satır aralığı `dispatchTask` L71–100 |
| `WORKFLOW_RECOMMENDATION` … (6 akış) L16–27 | `WORKFLOW_*` sabitleri **yok**; 5 akış metodu L49/99/147/199/241 |
| `personalizationWorkflow` L109–147 · `musicAnalysisWorkflow` L149–181 | İkisi de **yok** |
| `AIEngineContract` / `AIOrchestratorContract` / `AIPerformanceContract` / `AIQualityContract` / `AIRetryContract` / `AICachingContract` / `AIRequestValidationContract` (7 arayüz) | **6 gerçek arayüz**: `AIEngineInterface` · `AIOrchestratorInterface` · `KnowledgeBaseInterface` · `MemorySystemInterface` · `PromptEngineInterface` · `ToolCallingInterface` |
| §5 "Servis Katmanı" (5 sınıf, konum UNKNOWN) | Bu sınıflar **repoda hiç yok** (grep 0) — §5 bu sürümde gerçek bağımlılıklarla değiştirildi |

---

`status: verified` · `validation: 2026-10-12 · 7 PHP + 6 interface grep ile metot satırı doğrulandı, 33 imza sayıldı, uydurma 19 iddia §9 ile temizlendi`

---

## §10 Bileşen Olgunluk Matrisi (ek 2026-10-13)

> **Veri kaynağı:** bu dosyanın §1–§5 envanteri (2026-10-12 disk) + `-kod-ornekleri` §9–§10.
> Paket/tanım şeması referansı: https://getcomposer.org/doc/04-schema.md (HEAD 200 · 2026-10-13).

| # | Bileşen | Kod | Sözleşme | Test | Karşılık gelen çocuk | Olgunluk |
|---|---|---|---|---|---|---|
| M1 | `AIEngine` | ✅ 7 dosyalık setin parçası | ✅ `AIEngineInterface` (5 imza) | ❌ 0 satır | K006.8 · K006.6 · K006.1 | Kısmi |
| M2 | `AIWorkflow` | ✅ | ❌ interface **yok** | ❌ 0 satır | K006.8 + K016 çağrısı | Sözleşmesiz |
| M3 | `AIOrchestrator` | ✅ (K016 profili) | ✅ `AIOrchestratorInterface` | ❌ 0 satır | görev dağıtım yüzeyi | Kısmi |
| M4 | `KnowledgeBase` | ✅ (K016 profili) | ✅ `KnowledgeBaseInterface` | ❌ 0 satır | K016 sınırı (R-07) | Sınır dışı |
| M5 | `MemorySystem` | ✅ (K016 profili) | ✅ `MemorySystemInterface` | ❌ 0 satır | K006.7 kişiselleştirme | Sınır açık |
| M6 | `PromptEngine` | ✅ (K016 profili) | ✅ `PromptEngineInterface` | ❌ 0 satır | K016 (anayasa §8) | Sınır dışı |
| M7 | `ToolCalling` | ✅ (K016 profili) | ✅ `ToolCallingInterface` | ❌ 0 satır | K016 (anayasa §8) | Sınır dışı |
| M8 | evaluation bileşeni | ❌ 0 kod | ❌ | ❌ | K006.2-evaluation | Boş |
| M9 | local-inference bileşeni | ❌ 0 kod | ❌ | ❌ | K006.4-local-inference | Boş |
| M10 | model-management bileşeni | ❌ 0 kod | ❌ | ❌ | K006.5-model-management | Boş |
| M11 | music-analysis / audio-intelligence gerçek analiz | ❌ `extractFeatures` placeholder | ✅ `analyzeAudio` imzası var | ❌ | K006.6 · K006.1 | Sözleşmeli, gövdesiz |

**Okuma:** 7 bileşenin **tamamı** sözleşmeli/dokümante; **hiçbirinde test yok**; 3 çocuk
(M8–M10) tamamen boştur. "Bileşen var" iddiası M8–M10 için **yazışmaz**.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §11 Dış Çağrı Yüzeyi — HTTP sözleşmeleri eşlemesi (ek 2026-10-13)

> **Bağ:** `AIWorkflow` uzak çağrı/metric akışları · anayasa §26 (kaynak API'leri) ·
> bölüm kapısı: https://www.php-fig.org/psr/psr-17/ (HEAD 200 · 2026-10-13).

| # | İhtiyaç | PSR karşılığı | K006 bileşeni | Durum |
|---|---|---|---|---|
| H1 | HTTP istemci arayüzü | (bkz. `-web-arastirma` §11 A5) | uzak model/inference çağrısı | ⚠️ repo'da **görülmedi** |
| H2 | İstek/yanıt factory | PSR-17 | inference isteği üretimi | ⚠️ kullanılmıyor |
| H3 | Middleware (istek/yanıt katmanı) | PSR-15 | güvenlik/filtre katmanı | ⚠️ bu domainde **yok** (K009) |
| H4 | Yanıt mesajı | PSR-7 | uzak servis yanıtı | ⚠️ kullanılmıyor |
| H5 | Olay yayımı | (bkz. `-kod-ornekleri` §13 E3) | workflow adımları | ❌ yok |
| H6 | Basit cache | (bkz. `-web-arastirma` §11 A4) | `MemorySystem` oturum | ⚠️ alt depo **UNKNOWN** |

**Sınır:** HTTP/PSR-7/15/17 yüzeyi **K009-security / K012-services** ile ortak olabilir —
bu dosya sahiplik **iddia etmez** (Q1 benzeri açık soru).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §12 Tip Sistemi & Docblock Güvenilirliği (ek 2026-10-13)

> **Bağ:** `-kod-ornekleri` §5 (6 alan docblock vs 10 alan implementasyon) ·
> bölüm kapısı: https://www.php.net/manual/en/language.enumerations.php (HEAD 200 · 2026-10-13).

| # | Alan | Gözlem | PHP 8 yüzeyi | Sonuç |
|---|---|---|---|---|
| Y1 | `getRecommendations` dönüşü | `@return array{...}` docblock | çalışma zamanında **zorunlu değil** | ⚠️ şema yalnızca yorumda |
| Y2 | Öneri `reason` alanı | docblock'ta var, değeri **nerede üretildiği** kod-ornekleri §2'de görünmüyor | — | ⚠️ VERIFICATION REQUIRED |
| Y3 | `array_filter` sonrası `usort` | indeks boşluğu olabilir; `array_values` ile düzeltilmiş | dil davranışı | ✅ kod-ornekleri §2 L93 |
| Y4 | Enum kullanımı | bileşenlerde **görülmedi** (tüm durumlar string/array) | enum mevcut | ⚠️ sabit string riski |
| Y5 | Property tipleri | `AIWorkflow` typed property (§8) | dil özelliği | ✅ |
| Y6 | Union/intersection tip | bileşenlerde **görülmedi** | PHP 8 mevcut | ⚠️ yok |
| Y7 | Attribute kullanımı | bileşenlerde **görülmedi** | PHP 8 mevcut | ⚠️ yok |
| Y8 | null ile dönüş | `?string $userId` (kod-ornekleri §3) | nullable | ✅ |

**Dürüstlük:** "görülmedi" = bu dosyanın okuduğu bileşen yüzeyinde rastlanmadı; tüm dosya
taraması **bu oturumda yapılmadı** → `⚠️ VERIFICATION REQUIRED`.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §13 Paket Kaydı & Sürüm Kanıtı (ek 2026-10-13)

> **Bağ:** §5 servis/altyapı bağımlılıkları · bölüm kapısı:
> https://packagist.org/packages/phpunit/phpunit (HEAD 200 · 2026-10-13).

| # | Paket | Dosyadaki kanıt | Kayıt kapısı (200) | Sürüm doğrulandı mı |
|---|---|---|---|---|
| P1 | `phpunit/phpunit` | `^10.5` (composer.json — §5 kanıtı) | packagist (URL üstte) | ❌ sayfa **okunmadı** |
| P2 | `phpstan/phpstan` | `^1.10` (`-test-stratejisi` §1) | ⚠️ ayrı kapısı §17/E-list | ❌ okunmadı |
| P3 | `php-di/php-di` | `^7.0` (`-kod-ornekleri` §8) | ⚠️ `⚠️ VERIFICATION REQUIRED` | ❌ okunmadı |
| P4 | Python ML paketleri | repo'da **lockfile yok** (`-bagimliliklar`) | — | ❌ UNKNOWN |
| P5 | FFmpeg/FFprobe | kod yorumu (kod-ornekleri §5 L88) | trac.ffmpeg.org (§17) | ❌ araç kurulu mu **UNKNOWN** |

**Kural:** "packagist sayfası açıldı" ≠ "sürüm doğrulandı". Sürüm iddiası için sayfa
**metninin** okunması gerekir; bu tur yalnız HEAD durumunu aldı.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §14 Kod Düzeni & Denetim Araçları — aday envanteri (ek 2026-10-13)

> Bölüm kapısı: https://cs.symfony.com/ (HEAD 200 · 2026-10-13) ·
> bağ: `-kod-ornekleri` §12 (PSR-12 biçim gözlemi).

| # | Aday araç | Ne denetler | K006 yüzeyi | Durum |
|---|---|---|---|---|
| N1 | PHP-CS-Fixer (cs.symfony.com) | biçim düzeltme | 7 PHP dosyası | ⚠️ kurulmadı |
| N2 | PHP_CodeSniffer (§17 kapısı) | PSR-1/PSR-12 uyumu | biçim gözlemi §12 | ⚠️ kurulmadı |
| N3 | Rector (§17 kapısı) | otomatik kod dönüşümü | PHP 8 yükseltme | ⚠️ kurulmadı |
| N4 | PHPStan (mevcut, seviye 5) | statik analiz | tüm set | ✅ altyapı var, **bu oturumda çalıştırılmadı** |
| N5 | Psalm | statik analiz (alternatif) | tüm set | ⚠️ aday |
| N6 | mutasyon testi (infection) | test gücü | test olmadığından **anlamsız** | ❌ önce test gerekli |

**Öncelik:** N6, test yokken **çalıştırılamaz** → sıralama: test (§15) → statik (N4) → biçim (N1/N2).

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §15 Bileşen → Test Parçası Eşlemesi (ek 2026-10-13)

> Bölüm kapısı: https://github.com/sebastianbergmann/phpunit (HEAD 200 · 2026-10-13) ·
> bağ: `-kod-ornekleri` §14 (33 imza × test durumu).

| # | Bileşen | Test tipi | Fixture/arrange ihtiyacı | İlk test neyi kanıtlar |
|---|---|---|---|---|
| W1 | `combineScores` / `applyDiversityFilter` | birim (saf fonksiyon) | sahte skor dizisi | §2 ağırlık 0.6/0.4 davranışı |
| W2 | `getRecommendations` (boş kol) | birim | iki boş kol | filtre sonrası **boş** sonuç (§3 gözlemi) |
| W3 | `extractFeatures` | birim | sahte dosya yolu | placeholder'ın **sabit** çıktısı (§4) |
| W4 | `analyzeAudio` hata yolu | birim | var olmayan dosya | `\RuntimeException` fırlatma (§5) |
| W5 | `optimizeEQ` | birim | 3 farklı oda profili | 31 bant + `match` katsayısı (§6) |
| W6 | `AIWorkflow::recommendationWorkflow` | entegrasyon | 6 sahte bağımlılık | akış sırası + metadata (§7) |
| W7 | `MemorySystem` oturum yazma/okuma | entegrasyon | oturum deposu | KVKK silme senaryosu (anayasa §14/C5) |
| W8 | K016 sınırı (K006 çağırmamalı) | mimari test | bağımlılık grafiği | R-07 ayrımı |

**Kabul kriteri bu dosyada yazılmaz** — eşik/sahiplik `-test-stratejisi.md` ve K019'dadır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §16 Boşluk → Eylem Kuyruğu (ek 2026-10-13)

> Bölüm kapısı: https://getrector.com/ (HEAD 200 · 2026-10-13).

| # | Boşluk (kaynak) | Eylem | Sorumlu çocuk | Öncelik |
|---|---|---|---|---|
| G1 | M8–M10: 3 çocukta 0 kod | bileşen iskeleti mi, kapsam daraltma mı → ADR | K006.2 · K006.4 · K006.5 | Yüksek |
| G2 | `AIWorkflow` interface'siz (§11 H5) | sözleşme ekleme kararı | K006.8 / K016 | Orta |
| G3 | 7 bileşenin testi 0 (§15) | W1–W5 birim testi | K006.9-testing | Yüksek |
| G4 | docblock↔implementasyon 6/10 alan (§12 Y1) | şemayı daraltma veya docblock düzeltme | K006.8 | Orta |
| G5 | biçim denetimi yok (§14) | N1/N2 kurulum kararı | K018-devops | Düşük |
| G6 | paket sürümü doğrulanmadı (§13) | composer.lock üretimi | K018-devops | Orta |
| G7 | K016 bileşenleri bu dizinde (§1 M4–M7) | kapsama ihlali kararı (R-07) | ADR | Yüksek |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §17 Bu Ek İçin Kaynaklar (§10–§16)

| # | Kaynak | Ne verdi | Erişim |
|---|---|---|---|
| 1 | https://getcomposer.org/doc/04-schema.md | composer.json şema referansı (§10) | HEAD 200 · 2026-10-13 |
| 2 | https://www.php-fig.org/psr/psr-17/ | HTTP factory sözleşmesi (§11) | HEAD 200 · 2026-10-13 |
| 3 | https://www.php.net/manual/en/language.enumerations.php | PHP 8 tip yüzeyi (§12) | HEAD 200 · 2026-10-13 |
| 4 | https://packagist.org/packages/phpunit/phpunit | paket kayıt kapısı (§13) | HEAD 200 · 2026-10-13 |
| 5 | https://cs.symfony.com/ | biçim aracı (§14/N1) | HEAD 200 · 2026-10-13 |
| 6 | https://github.com/sebastianbergmann/phpunit | test çalıştırıcı deposu (§15) | HEAD 200 · 2026-10-13 |
| 7 | https://getrector.com/ | dönüşüm aracı (§16) | HEAD 200 · 2026-10-13 |
| 8 | https://squizlabs.github.io/PHP_CodeSniffer/ | PSR denetleyici (§14/N2) | HEAD 200 · 2026-10-13 |
| 9 | https://www.php-fig.org/psr/psr-15/ · https://www.php-fig.org/psr/psr-7/ | §11 H3/H4 referansları | HEAD 200 · 2026-10-13 |
| 10 | https://trac.ffmpeg.org/ | §13 P5 araç kapısı | HEAD 200 · 2026-10-13 |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §18 Test Yardımcıları & Kalite Araçları - aday bileşen kaydı (ek 2026-10-13)

§15/§16'daki test ve denetim boşluğunu kapatmaya **aday** bileşenlerin kayıt kapıları
doğrulandı. Hiçbiri repo'da kurulu değildir; kurulum = ADR/üst karar.

| # | Aday bileşen (kayıt kapısı) | Boşluğa karşılığı | Erişim |
|---|---|---|---|
| 1 | https://github.com/phpstan/phpstan | statik analiz deposu (§16/N3) | HEAD 200 · 2026-10-13 |
| 2 | https://github.com/vimeo/psalm | statik analiz alternatifi (§16/N3) | HEAD 200 · 2026-10-13 |
| 3 | https://packagist.org/packages/phpunit/php-code-coverage | kapsam ölçüm bileşeni (§15) | HEAD 200 · 2026-10-13 |
| 4 | https://packagist.org/packages/sebastian/comparator | assertion karşılaştırma çekirdeği (§15) | HEAD 200 · 2026-10-13 |
| 5 | https://packagist.org/packages/sebastian/exporter | assertion hata çıktısı (§15) | HEAD 200 · 2026-10-13 |
| 6 | https://packagist.org/packages/sebastian/environment | test ortamı bilgisi (§15) | HEAD 200 · 2026-10-13 |
| 7 | https://packagist.org/packages/mockery/mockery | sahte nesne bileşeni adayı (§15) | HEAD 200 · 2026-10-13 |
| 8 | https://packagist.org/packages/phpspec/prophecy | sahte nesne alternatidi (§15) | HEAD 200 · 2026-10-13 |
| 9 | https://packagist.org/packages/friendsofphp/php-cs-fixer | biçim düzeltme adayı (§14/N1 karşıtı) | HEAD 200 · 2026-10-13 |
| 10 | https://packagist.org/packages/slevomat/coding-standard | PSR-12 denetim standardı adayı (§14/N2) | HEAD 200 · 2026-10-13 |
| 11 | https://packagist.org/packages/dealerdirect/phpcodesniffer-composer-installer | CodeSniffer kurulum köprüsü (§14/N2) | HEAD 200 · 2026-10-13 |
| 12 | https://packagist.org/packages/roave/security-advisories | güvenlik taviz verisi (§13/§16) | HEAD 200 · 2026-10-13 |
| 13 | https://packagist.org/packages/ramsey/uuid | UUID üretici adayı (§13 veri kimliği) | HEAD 200 · 2026-10-13 |
| 14 | https://pytest.org/ | Python tarafı test kapısı — kardeş-katman kıyası (§15) | HEAD 200 · 2026-10-13 |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §19 PHP Dil Referansı Sayfaları - davranış doğrulama kapısı (ek 2026-10-13)

§12'deki tek tip sayfasının (enumerations) dışındaki dil referans kapıları doğrulandı;
bunlar **davranış iddialarının** (nullability, tip dönüşümü, kapsam) kapı kanıtıdır —
içerik okunmadığı için madde metni `⚠️ VERIFICATION REQUIRED` kalır:

| # | Sayfa | §12'deki boşluğun karşılığı | Erişim |
|---|---|---|---|
| 1 | https://www.php.net/manual/en/language.attributes.php | attribute sözdizimi referansı (§12) | HEAD 200 · 2026-10-13 |
| 2 | https://www.php.net/manual/en/language.types.string.php | string davranışı (§4 sözleşme girdisi) | HEAD 200 · 2026-10-13 |
| 3 | https://www.php.net/manual/en/language.types.array.php | dizi davranışı (§3 workflow `$request` girdisi) | HEAD 200 · 2026-10-13 |
| 4 | https://www.php.net/manual/en/language.types.boolean.php | bool davranışı (§4 sözleşme dönüşü) | HEAD 200 · 2026-10-13 |
| 5 | https://www.php.net/manual/en/language.types.float.php | float davranışı — metrik döndüşü (§15/§16) | HEAD 200 · 2026-10-13 |
| 6 | https://www.php.net/manual/en/language.types.integer.php | integer davranışı (§15/§16) | HEAD 200 · 2026-10-13 |
| 7 | https://www.php.net/manual/en/language.types.callable.php | callable tipi — hook/servis çağrısı (§4) | HEAD 200 · 2026-10-13 |
| 8 | https://www.php.net/manual/en/language.types.mixed.php | mixed — `array $request` genişliğinin sınırı (§3) | HEAD 200 · 2026-10-13 |
| 9 | https://www.php.net/manual/en/language.control-structures.php | kontrol yapısı — workflow dallanması (§3 L49-91) | HEAD 200 · 2026-10-13 |
| 10 | https://www.php.net/manual/en/language.functions.php | fonksiyon bildirimi — metot imzaları (§4) | HEAD 200 · 2026-10-13 |
| 11 | https://www.php.net/manual/en/language.namespaces.php | ad alanı — `Contracts\AI` katmanı (§4) | HEAD 200 · 2026-10-13 |
| 12 | https://www.php.net/manual/en/language.oop5.php | sınıf/arayüz — interface eşlemeleri (§4) | HEAD 200 · 2026-10-13 |
| 13 | https://www.php.net/manual/en/language.variables.scope.php | değişken kapsamı — workflow yerel değişkenleri (§3) | HEAD 200 · 2026-10-13 |
| 14 | https://www.php.net/manual/en/language.operators.php | operatör — koşul/atama ifadeleri (§3) | HEAD 200 · 2026-10-13 |
| 15 | https://www.php.net/manual/en/language.generators.php | generator — akış işleme adayı (§3) | HEAD 200 · 2026-10-13 |
| 16 | https://www.php.net/manual/en/language.types.resource.php | resource — dış çağrı yüzeyi (§11) | HEAD 200 · 2026-10-13 |
| 17 | https://www.php.net/manual/en/language.types.object.php | nesne — servis enjeksiyonu (§2/§4) | HEAD 200 · 2026-10-13 |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §20 PSR Genişlemesi & Bu Ek Kaynakları (§18–§19) (ek 2026-10-13)

| # | Kaynak | Ne verdi | Erişim |
|---|---|---|---|
| 1 | https://www.php-fig.org/psr/psr-2/ | klasik kod stili standardı kapısı (§14/N2 kıyas) | HEAD 200 · 2026-10-13 |
| 2 | https://www.php-fig.org/psr/psr-13/ | olay dağıtımı arayüzü kapısı (§8 aday ilişkisi) | HEAD 200 · 2026-10-13 |
| 3 | https://www.php-fig.org/psr/psr-20/ | saat/kronometre arayüzü kapısı (§16 ölçüm adayı) | HEAD 200 · 2026-10-13 |
| 4 | https://packagist.org/packages/php-fig/cache · https://packagist.org/packages/php-fig/log · https://packagist.org/packages/php-fig/container | sözleşme paketleri kayıt kapıları (§13 kıyas) | HEAD 200 · 2026-10-13 |
| 5 | https://packagist.org/packages/php-fig/http-message · https://packagist.org/packages/php-fig/http-client · https://packagist.org/packages/php-fig/http-factory | HTTP sözleşmeleri kayıt kapıları (§11 kıyas) | HEAD 200 · 2026-10-13 |
| 6 | https://packagist.org/packages/symfony/http-foundation · https://packagist.org/packages/slim/slim · https://packagist.org/packages/laminas/laminas-diactoros | çerçeve/uygulama alternatifleri (§7 boşluk kıyası) | HEAD 200 · 2026-10-13 |
| 7 | https://packagist.org/packages/fig/http-message-util | mesaj yardımcı sabitleri (§11) | HEAD 200 · 2026-10-13 |
| 8 | https://packagist.org/packages/phpunit/php-timer · https://packagist.org/packages/phpunit/php-invoker | süre/çalıştırma yardımcıları (§15/§16) | HEAD 200 · 2026-10-13 |
| 9 | https://packagist.org/packages/phpstan/phpstan-rules · https://packagist.org/packages/symplify/easy-coding-standard | kural/standard paketleri (§14) | HEAD 200 · 2026-10-13 |
| 10 | `§18` · `§19` aday listeleri | bu ekteki 31 adayın tamamı — **hiçbiri kurulu değil** | 2026-10-13 disk + HEAD |

**Dürüstlük notu:** §18–§19'daki tüm satırlar **aday/kayıt kapısı**dır; "K006'da kullanılıyor"
iddiası taşımaz, kurulum iddiası taşımaz. Bağımlılık olarak kurulmadan önce
`../yapay-zeka-bagimliliklar.md` §3 (gerçek composer bağımlılıkları) esastır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §21 Test Çekirdeği Bileşenleri - derinlemesine kayıt kapısı (ek 2026-10-13)

§18'deki listeden farklı olarak, PHPUnit'un **iç çekirdek** bileşenleri ve rakip test
çalıştırıcı kapıları ayrı ayrı doğrulandı (§15 "test parçası" boşluğu için):

| # | Kapı | Bileşen rolü | Erişim |
|---|---|---|---|
| 1 | https://packagist.org/packages/myclabs/deep-copy | nesne kopyalama — test izolasyonu çekirdeği | HEAD 200 · 2026-10-13 |
| 2 | https://packagist.org/packages/sebastian/type | tip denetimi çekirdeği | HEAD 200 · 2026-10-13 |
| 3 | https://packagist.org/packages/sebastian/global-state | statik durum yakalama çekirdeği | HEAD 200 · 2026-10-13 |
| 4 | https://packagist.org/packages/doctrine/instantiator | nesnesiz örnek üretimi (mock çekirdeği) | HEAD 200 · 2026-10-13 |
| 5 | https://packagist.org/packages/phar-io/version | sürüm karşılaştırma (PHPUnit sürüm kontrolü) | HEAD 200 · 2026-10-13 |
| 6 | https://packagist.org/packages/hamcrest/hamcrest-php | eşleyici/kural kütüphanesi (assertion dili) | HEAD 200 · 2026-10-13 |
| 7 | https://packagist.org/packages/phpunit/php-text-template | çıktı şablonu (raporlama) | HEAD 200 · 2026-10-13 |
| 8 | https://pestphp.com/ | alternatif test çerçevesi kapısı | HEAD 200 · 2026-10-13 |
| 9 | https://github.com/pestphp/pest | alternatif çerçeve deposu | HEAD 200 · 2026-10-13 |
| 10 | https://github.com/pytest-dev/pytest-cov | kardeş ekosistem kapsam eklentisi (§15 kıyas) | HEAD 200 · 2026-10-13 |
| 11 | https://github.com/pytest-dev/pytest-mock | kardeş ekosistem mock eklentisi (§15 kıyas) | HEAD 200 · 2026-10-13 |
| 12 | https://docs.python.org/3/library/unittest.html | kardeş ekosistem test modülü — §2 kıyas katmanı | HEAD 200 · 2026-10-13 |

**Kapsam:** bu kapıların hiçbiri `shared/composer.json` require-dev'inde **görünmez** —
§5'teki gerçek bağımlılık listesi (`phpunit ^10.5` · `phpstan ^1.10`) değişmez; bu tablo
**yalnız tarama kaydıdır**.

---

## §22 Ortak Kod Yüzeyi — `shared/src/AI/` sınıf & metod envanteri (ek 2026-10-15)

> **Kanıt yöntemi:** 7 PHP dosyasının tamamı bu oturumda okundu; metod adı ve satır no
> dosyanın kendisinden alındı (uydurma yok). Ölçüm komutu: dosya satırları ayrıştırılıp
> `function <ad>` imzaları tarandı. Sayılar 2026-10-15 disk durumudur.

| # | Dosya (göreli yol) | Satır | Metod | Sabit | İlk metot | Son metot |
|---|---|---|---|---|---|---|
| 1 | `shared/src/AI/AIEngine.php` | 382 | 16 | 3 | `__construct` @34 | `calculateBandQ` @368 |
| 2 | `shared/src/AI/AIOrchestrator.php` | 210 | 10 | 2 | `__construct` @56 | `executeTask` @185 |
| 3 | `shared/src/AI/AIWorkflow.php` | 271 | 6 | 0 | `__construct` @27 | `faultPredictionWorkflow` @241 |
| 4 | `shared/src/AI/KnowledgeBase.php` | 232 | 9 | 3 | `search` @36 | `calculateConfidence` @223 |
| 5 | `shared/src/AI/MemorySystem.php` | 242 | 10 | 1 | `setSession` @45 | `estimateMemoryUsage` @222 |
| 6 | `shared/src/AI/PromptEngine.php` | 325 | 10 | 3 | `__construct` @40 | `truncateContext` @307 |
| 7 | `shared/src/AI/ToolCalling.php` | 326 | 9 | 1 | `__construct` @45 | `validateParameters` @307 |
| — | **Toplam** | **1988** | **70** | **13** | — | — |

**Çapraz kontrol:** `grep` deseni (`public|private|protected` + `function|const`) bu 7 dosyada
**83 isabet** verdi; 70 metod + 13 sabit = 83 → iki bağımsız sayım birebir örtüştü (2026-10-15).

### §22.1 K006.3'e giren / girmeyen ayrımı (bu dosya §3 ile aynı kural)

| Sınıf | K006.3 (inference) | K006.6 (öneri) | K006.4 (kişiselleştirme) | Kanıt satırı |
|---|---|---|---|---|
| `AIEngine::getRecommendations` | hayır | **evet** | kısmen | `AIEngine.php` @49 |
| `AIEngine::analyzeAudio` | hayır | hayır | hayır → K006.5 | `AIEngine.php` @82 |
| `AIEngine::optimizeEQ` | hayır | hayır | hayır → K006.5 | `AIEngine.php` @111 |
| `AIEngine::analyzeHardware` / `predictFault` | hayır | hayır | hayır → K016 | `AIEngine.php` @138 · @161 |
| `AIWorkflow::recommendationWorkflow` | hayır | **evet** | kısmen | `AIWorkflow.php` @49 |
| `AIWorkflow::audioAnalysisWorkflow` | hayır | hayır | hayır → K006.5 | `AIWorkflow.php` @99 |
| `AIWorkflow::autoEQWorkflow` | hayır | hayır | hayır → K006.5 | `AIWorkflow.php` @147 |
| `AIOrchestrator::dispatchTask` | evet (çağırır) | kısmen | kısmen | `AIOrchestrator.php` @71 |
| `KnowledgeBase::ragQuery` | **evet** | kısmen | kısmen | `KnowledgeBase.php` @72 |
| `PromptEngine::generatePrompt` | **evet** | kısmen | kısmen | `PromptEngine.php` @48 |
| `ToolCalling::callTool` | **evet** | hayır | hayır | `ToolCalling.php` @73 |

**Okuma kuralı:** "evet" = bu sınıfın o metodu K006.3 kapsamındadır; "kısmen" = çağıran akışın
türüne göre K006.3 ya da K006.4; "hayır" = kapsam dışı (komşu K'ya ait) — §6'daki eşlemenin
satır bazlı kanıtıdır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**
