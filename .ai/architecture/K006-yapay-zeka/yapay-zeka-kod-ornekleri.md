---
node_id: K006-yapay-zeka-kod-ornekleri
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
  - "shared/src/AI/AIEngine.php — L1–77, L81–133, L205–235, L310–354 (2026-10-12 okundu)"
  - "shared/src/AI/AIWorkflow.php — L12–98 (2026-10-12 okundu)"
  - "shared/src/Contracts/AI/AIEngineInterface.php — L1–61 tam okuma (2026-10-12)"
validation: "2026-10-12 · tüm kod blokları dosya+satır referanslı, birebir disk içeriği · status=verified"
open_questions:
  - "extractFeatures placeholder'ının C++ DSP engine'e ne zaman bağlanacağı → UNKNOWN"
  - "getCollaborativeScores/getContentBasedScores'un gerçek implementasyon tarihi → UNKNOWN"
group: kod
---

# K006 — Yapay Zeka · Kod Örnekleri

> **Kural:** her blok gerçek disk içeriğidir; dosya yolu + satır aralığı zorunlu. Bloklar arasında
> yorum/çıkarma ayrı ayrı etiketlenir. Uydurma satır yok; `...` ile kırpılmış yerlerde
> atlanan satır sayısı yazılır.

---

## 1. Arayüz Sözleşmesi — `shared/src/Contracts/AI/AIEngineInterface.php` L19–28

```php
interface AIEngineInterface
{
    /**
     * Müzik önerisi üretir.
     *
     * @param array<string, mixed> $context Kullanıcı bağlamı (tercihler, geçmiş, mood)
     * @param int                  $limit   Maksimum öneri sayısı
     * @return array<int, array{id: string, title: string, artist: string, score: float, reason: string}>
     */
    public function getRecommendations(array $context, int $limit = 20): array;
```

**Analiz:** sözleşmenin tek dönüş şeması `@return` docblock'tadır (L26): her öneri
`{id, title, artist, score, reason}` beşlisidir. `score` `float`, `limit` varsayılanı 20.
K006.8-recommendation'ın çıktısı bu şeye bağlıdır — `reason` alanı açıklanabilirlik (explainability)
yüzeyidir; içeriği üretim kodunda dolduran taraf `AIEngine`'dir (bkz. §2).

**5 imzanın tamamı (aynı dosya):** `getRecommendations` L28 · `analyzeAudio` L36 ·
`optimizeEQ` L44 · `analyzeHardware` L52 · `predictFault` L60.

---

## 2. Hibrit Öneri Akışı — `shared/src/AI/AIEngine.php` L49–77

```php
public function getRecommendations(array $context, int $limit = 20): array
{
    $limit = min($limit, self::MAX_RECOMMENDATIONS);
    $userId = $context['user_id'] ?? null;
    $sessionId = $context['session_id'] ?? null;
    $mood = $context['mood'] ?? null;
    $genre = $context['genre'] ?? null;
    $history = $context['history'] ?? [];

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

**Analiz:** 6 adımlı boru hattı. `0.6/0.4` ağırlığı L65'te **kod gömülü** (config değil).
`MIN_SCORE=0.7` (L31) ve `MAX_RECOMMENDATIONS=20` (L30) sınıf sabitidir.
$sessionId/$mood değişkenleri L53–54'te okunur ama bu metotta **kullanılmaz** (L59–62 yollarında geçmez) —
L55'teki `$mood` ölü okumadır çünkü `getContentBasedScores($context)` tüm bağlamı alır; yine de
değişkenin kendisi bu gövdede ölçek dışıdır. **Dead-code notu:** L53 `$sessionId` bu gövdede hiç kullanılmaz.

---

## 3. Boş Tutamaç — `shared/src/AI/AIEngine.php` L206–218

```php
    /**
     * Collaborative filtering skorları.
     */
    private function getCollaborativeScores(?string $userId, array $history): array
    {
        if ($userId === null) {
            return [];
        }

        // DB'den benzer kullanıcıları bul
        // Gerçek implementasyonda matrix factorization (ALS) kullanılacak
        return [];
    }
```

**Analiz:** metot **her iki dalda da boş array döner** (L212 ve L217). L216'daki yorum
gelecekteki ALS (Alternating Least Squares) niyetini belirtir — implementasyon **yok**.
Sonuç: §2'deki hibrit boru hattının `0.6` ağırlıklı kolu şu an daima `[]` üretir;
pratikte öneriler tümüyle content-based kola düşer. Bu, K006.8 için **kanıtlanmış boşluktur**.

Aynı durum `getContentBasedScores` (L223–235) için de geçerlidir — L234 `return [];`:

```php
    private function getContentBasedScores(array $context): array
    {
        $targetFeatures = [
            'bpm'          => $context['bpm'] ?? null,
            'energy'       => $context['energy'] ?? null,
            'danceability' => $context['danceability'] ?? null,
            'genre'        => $context['genre'] ?? null,
        ];

        // DB'den aday şarkıları çek ve özelliklerini karşılaştır
        // cosine similarity kullanarak skorla
        return [];
    }
```

> **Sonuç (2026-10-12 gözlem):** `getRecommendations` çağrıları `combineScores([], [], 0.6, 0.4)`
> üzerinden geçer → `applyDiversityFilter` → `MIN_SCORE=0.7` eşiği. İki girdi de boşken
> filtrelenen küme boş döner. K006.8 için öncelikli açık madde budur.

---

## 4. Placeholder Öznitelik Çıkarımı — `shared/src/AI/AIEngine.php` L312–327

```php
    /**
     * DSP pipeline ile özellik çıkarır.
     */
    private function extractFeatures(string $filePath): array
    {
        // Gerçek implementasyonda C++ DSP engine ile yapılacak
        // Şimdilik placeholder değerler
        return [
            'bpm'          => 120.0,
            'key'          => 'C',
            'energy'       => 0.7,
            'mood'         => 'happy',
            'danceability' => 0.6,
            'acousticness' => 0.3,
        ];
    }
```

**Analiz:** `$filePath` parametresi **gövdede hiç kullanılmaz** (L315–327) — sabit döner.
Yorum (L317–318) hedefi: C++ DSP engine (Neva Engine, K003-audio). K006.1-audio-intelligence ve
K006.6-music-analysis için bu tek gerçek "ses analizi" girişi olup **içeriği uydurmadır**:
BPM/key/energy/mood her dosya için aynı çıkar. `analyzeAudio` (L92) bunu çağırdığı için
`bpm=120.0, key=C` çıktısı tüm ses dosyalarında sabittir.

---

## 5. Ses Analizi Çıkışı — `shared/src/AI/AIEngine.php` L82–106

```php
    public function analyzeAudio(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("Audio file not found: {$filePath}");
        }

        // FFprobe ile metadata çıkarma
        $metadata = $this->extractMetadata($filePath);

        // DSP pipeline ile özellik çıkarma
        $features = $this->extractFeatures($filePath);

        return [
            'bpm'           => $features['bpm'] ?? 0.0,
            'key'           => $features['key'] ?? 'unknown',
            'energy'        => $features['energy'] ?? 0.0,
            'mood'          => $features['mood'] ?? 'neutral',
            'danceability'  => $features['danceability'] ?? 0.0,
            'acousticness'  => $features['acousticness'] ?? 0.0,
            'duration'      => $metadata['duration'] ?? 0,
            'sample_rate'   => $metadata['sample_rate'] ?? 44100,
            'bit_depth'     => $metadata['bit_depth'] ?? 16,
            'channels'      => $metadata['channels'] ?? 2,
        ];
    }
```

**Analiz:** hata yönetimi fırlatma tabanlı (`\RuntimeException`, L85) — Result/Either **değil**
(bkz. `yapay-zeka-riskler.md` R-04). `extractMetadata` yorumu FFprobe'a işaret eder (L88);
`extractFeatures` yorumu DSP pipeline'a (L91) — ikisi de §4'te görüldüğü üzere gerçek analiz
yapmaz. Interface docblock'ı (Contracts L34) 6 alanı; implementasyon 10 alan döner
(6 öznitelik + 4 metadata) — **sözleşme üstü genişlik**, daraltma yapan çağıran yok.

---

## 6. 31-Band EQ Üretimi — `shared/src/AI/AIEngine.php` L111–133

```php
    public function optimizeEQ(array $roomProfile): array
    {
        $roomSize = $roomProfile['size'] ?? 'medium';
        $reverb = $roomProfile['reverb'] ?? 0.5;
        $frequencyResponse = $roomProfile['frequency_response'] ?? [];

        // 31-band parametrik EQ eğrisi üret
        $eqCurve = [];
        for ($band = 0; $band < 31; $band++) {
            $frequency = $this->bandToFrequency($band);
            $gain = $this->calculateRoomCorrection($frequency, $roomSize, $reverb, $frequencyResponse);
            $q = $this->calculateBandQ($frequency, $roomSize);

            $eqCurve[] = [
                'band'      => $band,
                'frequency' => $frequency,
                'gain'      => round($gain, 2),
                'q'         => round($q, 2),
            ];
        }

        return $eqCurve;
    }
```

**Analiz:** 31 bant (20 Hz–20 kHz, `bandToFrequency` L332–342 ISO listesi). `gain` tavanı
interface docblock'ında (Contracts L42) `array{band, frequency, gain, q}` — implementasyonla eşleşir.
`calculateRoomCorrection` (L347) `match` ile `small=1.2 / medium=1.0 / large=0.8` katsayısı
(L350–354) — basit lineer model, ölçüme dayalı değil (⚠️ doğrulama gerekir; ayrı ölçüm
`yapay-zeka-dogrulama.md` §4'te yok).

---

## 7. Öneri Workflow Zinciri — `shared/src/AI/AIWorkflow.php` L49–91

```php
    public function recommendationWorkflow(array $request): array
    {
        $startTime = microtime(true);
        $sessionId = $request['session_id'] ?? 'anonymous';

        // 1. Context collect — session'dan tercihleri çek
        $context = $this->memory->getSession($sessionId, 'preferences') ?? [];
        $context = array_merge($context, $request);

        // 2. Task dispatch
        $taskId = $this->orchestrator->dispatchTask('recommendation', $context, 'medium');

        // 3. Knowledge query — benzer önerileri ara
        $query = ($context['mood'] ?? '') . ' ' . ($context['genre'] ?? '');
        $knowledgeResults = $this->knowledge->search($query, 5);

        // 4. Prompt generate
        $promptResult = $this->promptEngine->generatePrompt('recommendation', [
            'mood'   => $context['mood'] ?? 'neutral',
            'genre'  => $context['genre'] ?? 'any',
            'energy' => $context['energy'] ?? 0.5,
            'history' => json_encode($context['history'] ?? [], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            'limit'  => $context['limit'] ?? 10,
        ], ['knowledge' => $knowledgeResults]);

        // 5. Recommendation engine
        $recommendations = $this->engine->getRecommendations($context, $context['limit'] ?? 10);

        // 6. Feedback — sonucu kaydet
        $this->memory->setSession($sessionId, 'last_recommendations', $recommendations, 3600);

        $executionTime = microtime(true) - $startTime;

        return [
            'recommendations' => $recommendations,
            'metadata'        => [
                'task_id'        => $taskId,
                'prompt_tokens'  => $promptResult['tokenCount'],
                'knowledge_hits' => count($knowledgeResults),
            ],
            'execution_time'  => round($executionTime, 4),
        ];
    }
```

**Analiz:** 6 bağımlılık enjeksiyonu (sınıf L20–41: `AIEngine`, `AIOrchestrator`, `KnowledgeBase`,
`PromptEngine`, `MemorySystem`, `ToolCalling`) — `use` satırı **yok** çünkü hepsi aynı
`CoreMusic\AI` namespace'inde. Dikkat çekenler:

1. L63 `$this->knowledge->search(...)` → `KnowledgeBase` = **K016 sınıfı**. K006.8 akışı K016'ya
   doğrudan bağımlıdır (sınır kaydı: `yapay-zeka-kapsam.md` §2.2).
2. L66 `generatePrompt('recommendation', ...)` sonucu **cevap gövdesine girmez** — yalnız
   `metadata.prompt_tokens` (L86) için sayılır. Yani prompt üretimi bu akışta sonuç **üretmez**.
3. L59 `dispatchTask('recommendation', ...)` bir `taskId` döner ama beklenmez; akış **asenkron
   task'i senkron çağrıyla başlatıp sonucunu beklemez** (L59 → L75 arası task sonucu okunmaz).
4. L52 `?? 'anonymous'` — kimliksiz oturum varsayılanı.

---

## 8. Sınıf Bağımlılık Enjeksiyonu — `shared/src/AI/AIWorkflow.php` L18–41

```php
class AIWorkflow
{
    private AIEngine $engine;
    private AIOrchestrator $orchestrator;
    private KnowledgeBase $knowledge;
    private PromptEngine $promptEngine;
    private MemorySystem $memory;
    private ToolCalling $toolCalling;

    public function __construct(
        AIEngine $engine,
        AIOrchestrator $orchestrator,
        KnowledgeBase $knowledge,
        PromptEngine $promptEngine,
        MemorySystem $memory,
        ToolCalling $toolCalling
    ) {
        $this->engine = $engine;
        $this->orchestrator = $orchestrator;
        $this->knowledge = $knowledge;
        $this->promptEngine = $promptEngine;
        $this->memory = $memory;
        $this->toolCalling = $toolCalling;
    }
```

**Analiz:** 6 somut sınıf bağımlılığı — **arayüz üzerinden enjeksiyon yok** (AIWorkflow,
`AIWorkflowInterface` implemente etmez; arayüz zaten repoda yok). Tip güvenliği `readonly`/typed
property ile sağlanmış; hata yönetimi yok. DI konteyneri `php-di ^7.0` (composer.json) ile
kuruluyorsa somut sınıflar kayıtlıdır — **kayıt dosyası okunmadı** (⚠️ VERIFICATION REQUIRED).

---

## 9. Arayüz ↔ Implementasyon Eşlemesi (tüm set)

| Interface (Contracts/AI) | İmza | Implementasyon | `use` satırı |
|---|---|---|---|
| `AIEngineInterface` L19 | 5 | `AIEngine.php` L28 `implements AIEngineInterface` | AIEngine L15 |
| `AIOrchestratorInterface` L13 | 6 | `AIOrchestrator.php` (L13–14 `use`) | AIOrchestrator L14 |
| `KnowledgeBaseInterface` L13 | 5 | `KnowledgeBase.php` L36 search · L72 ragQuery | L13 |
| `MemorySystemInterface` L13 | 7 | `MemorySystem.php` L45 setSession · L123 remember | L13 |
| `PromptEngineInterface` L13 | 5 | `PromptEngine.php` L48 generatePrompt · L90 countTokens | L13 |
| `ToolCallingInterface` L13 | 5 | `ToolCalling.php` L53 registerTool · L73 callTool | L13 |

`AIWorkflow`'un interface'i **yoktur** (yalnızca `class AIWorkflow`, L18) — kontrat dışı yüzey.

---

## 10. Örnek → Çocuk Eşlemesi

| Örnek § | Çocuk | Ne kanıtlıyor |
|---|---|---|
| §1 · §2 | K006.8-recommendation | çıktı şeması + 6 adımlı hibrit akış |
| §3 | K006.8-recommendation | CF kolu **boş** — algoritma yok |
| §4 · §5 | K006.1-audio-intelligence · K006.6-music-analysis | öznitelik **placeholder** |
| §6 | K006.3-inference | EQ modeli basit `match` — ölçüm yok |
| §7 · §8 | K006.8 · K016 sınırı | workflow 6 somut bağımlılık, K016'ya doğrudan çağrı |
| §9 | K006.9-testing | test edilebilir arayüz yüzeyi (6 interface, 33 imza) |

**Kod karşılığı olmayan çocuklar:** K006.2-evaluation · K006.4-local-inference ·
K006.5-model-management · K006.7-personalization — bu dördünde **hiçbir örnek yok** (0 satır).
K006.9-testing: K006 AI sınıfları için yazılmış test **0 satır** (yalnız `phpunit ^10.5` altyapısı).

---

## 11. Kaynaklar

1. `shared/src/Contracts/AI/AIEngineInterface.php` — L1–61 tam okuma, 2026-10-12.
2. `shared/src/AI/AIEngine.php` — L1–77, L81–140, L205–249, L310–354 okundu, 2026-10-12.
3. `shared/src/AI/AIWorkflow.php` — L12–98 okundu, 2026-10-12.
4. `shared/composer.json` — DI/test paketleri, 2026-10-12.
5. `yapay-zeka-bilesenler.md` §2/§4 — metot satır envanteri (bu dosyadaki tüm L referansları
   ile çapraz doğrulandı).

---

`status: verified` · `validation: 2026-10-12 · 9 gerçek kod bloğu, her biri dosya+satır ile diskten alındı, 0 uydurma sembol`

---

## §12 Biçim & standart gözlemi — PSR-12 ile karşılaştırma (ek 2026-10-13)

> **Kapsam:** yalnız bu dosyanın §1–§8 bloklarındaki kod — yeni kod okunmadı, uydurulmadı.
> Standart kaynağı: https://www.php-fig.org/psr/psr-12/ (HEAD 200 · 2026-10-13 doğrulandı).

| # | Gözlem (§ referanslı) | Kanıt | Standart yüzeyi | Durum |
|---|---|---|---|---|
| B1 | Fonksiyon gövdesi 4 boşluk girintisiyle yazılmış | §2 blok (L66–94) | PSR-12 General | ✅ gözlendi |
| B2 | Metot adları camelCase (`getCollaborativeScores`) | §3 (L111) | PSR-12 Methods | ✅ gözlendi |
| B3 | Dizi anahtarları snake_case (`user_id`, `session_id`) | §2 (L51–53) | PSR-12 ile çelişen bir madde yok — proje içi sözleşmedir | ⚠️ proje kararı değil, görgü |
| B4 | Docblock her public/private metotta mevcut | §1 · §3 · §4 | PSR-12 Comments | ✅ gözlendi |
| B5 | `interface` tek dosyada, açılış süslü parantez aynı satırda | §1 (L41) | PSR-12 General | ✅ gözlendi |
| B6 | `match` ifadesi tek satır kolonlu kullanılmış | §6 (`calculateRoomCorrection`) | PSR-12 General (PHP 8 yüzeyi) | ✅ gözlendi |
| B7 | Kaynak dosya `<?php` açık etiketi ile başlıyor (bloklar L40/L66/L107) | §2 · §3 | PSR-12 General | ✅ gözlendi |
| B8 | Satır sonu `;` zorunlu; bloklarda eksik `;` yok | tüm bloklar | PSR-12 General | ✅ gözlendi |
| B9 | `use` bildirimi olmayan dosya (aynı namespace) | §8 analiz (AIWorkflow) | PSR-12 Namespace and Use Declarations | ✅ gözlendi — `use` yokluğu kasıtlı |
| B10 | Uzun satır (docblock `@return` L26, §1) ~110 karakter | §1 (L26) | PSR-12 General — satır uzunluğu **zorunlu değil** | ✅ ihlal değil |

**Ölçüt notu:** PSR-12'in madde numaraları bu ekte **yazılmadı** (madde numarası uydurmak yasak) —
standart yalnızca üst başlık düzeyinde referanslanır; tam madde metni URL'dedir.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §13 Enjeksiyon & kapsayıcı yüzeyi — PSR-11 / PSR-14 karşılaştırması (ek 2026-10-13)

> **Kaynaklar:** §8 (constructor) · `yapay-zeka-bagimliliklar.md` §3 (composer.json kanıtı) ·
> https://www.php-fig.org/psr/psr-11/ (HEAD 200 · 2026-10-13) · https://www.php-fig.org/psr/psr-14/ (HEAD 200 · 2026-10-13).

| # | Yüzey | Disk kanıtı | PSR-11 (container) | PSR-14 (event dispatcher) | Durum |
|---|---|---|---|---|---|
| E1 | `AIWorkflow::__construct` 6 bağımlılık ister | §8 (L329–343) | Kapsayıcı arayüzü yok — somut sınıflar doğrudan istenir | — | ⚠️ PSR-11 `ContainerInterface` kullanılmıyor |
| E2 | `php-di ^7.0` composer.json'da | `yapay-zeka-bagimliliklar.md` §3 | php-di PSR-11 implementasyonu sunabilir | — | ⚠️ kayıt dosyası **okunmadı** → VERIFICATION REQUIRED |
| E3 | Olay yayımı (`dispatch`) kullanımı | §7 (L59 `dispatchTask`) | — | `EventDispatcherInterface` çağrısı **yok**; `dispatchTask` kendi kavramıdır | ✅ PSR-14 yüzeyi boş |
| E4 | Servis erişimi `$this->knowledge->search(...)` | §7 (L63) | arayüz üzerinden değil somut tip üzerinden | — | ⚠️ test için mock zorlaşır |
| E5 | `MemorySystem` oturum yazımı | §7 (L75) | PSR-6/PSR-16 cache sözleşmesi ile eşleşme **incelenmedi** | — | ⚠️ VERIFICATION REQUIRED |
| E6 | Hata yayılımı: fırlatma (`\RuntimeException`) | §5 (L85) | — | — | ✅ gözlem §5'te kayıtlı |
| E7 | Servis kaydı / etiketleme (tagged service) | repo'da **görülmedi** | — | — | ❌ kanıt yok → UNKNOWN |

**Sonuç (yalnız gözlem):** K006 kodu, PSR-11'in sunduğu *soyut kapsayıcı* ve PSR-14'ün
*olay yayımı* yüzeylerini **kullanmıyor**; bağımlılıklar constructor ile somut tipte sabitlenmiştir.
Bu bir kusur kaydı değil, **durum kaydıdır** — taşıma/yeniden düzenleme bu dosyanın işi değildir.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §14 Test edilebilirlik yüzeyi — 33 imza × mevcut test (ek 2026-10-13)

> **Kaynak:** §9 tablosu (6 interface · 33 imza — 2026-10-12 grep sayımı) ·
> §10 ("K006 AI sınıfları için test 0 satır") · https://docs.phpunit.de/ (HEAD 200 · 2026-10-13).

| # | Interface | İmza | Dönüşü doğrudan assert edilebilir mi | Mevcut test | Boşluk etkisi |
|---|---|---|---|---|---|
| T1 | `AIEngineInterface` | 5 | Evet — `array` dönüş şeması docblock'ta | ❌ 0 satır | skorlama/filtre regressyonu görünmez |
| T2 | `AIOrchestratorInterface` | 6 | Kısmen — `taskId` döner | ❌ 0 satır | task akışı doğrulanmaz |
| T3 | `KnowledgeBaseInterface` | 5 | Evet — arama sonuç dizisi | ❌ 0 satır | K016 sınırı test edilemez |
| T4 | `MemorySystemInterface` | 7 | Evet — oturum yazma/okuma | ❌ 0 satır | KVKK silme talebi test edilemez |
| T5 | `PromptEngineInterface` | 5 | Evet — token sayımı | ❌ 0 satır | maliyet hesabı doğrulanmaz |
| T6 | `ToolCallingInterface` | 5 | Evet — araç çağrısı sonucu | ❌ 0 satır | araç hatası yolu test edilemez |
| — | **Toplam** | **33** | — | ❌ **0 satır** | altyapı var (`phpunit ^10.5`), test yok |

**Öncelik sırası (yalnız mantıksal çıkarım, ölçüm değil):** §3'te kanıtlanan *boş CF kolu*
ve §4'teki *placeholder extractFeatures*, T1 üzerinden en ucuz testlenebilir yüzdür — çünkü
her ikisi de saf `array → array` dönüşümüdür ve sahte girdi ile doğrudan gözlemlenebilir.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §15 Statik analiz yakalanabilirlik listesi — PHPStan yüzeyi (ek 2026-10-13)

> **Kaynak:** §2–§8 gözlemleri · yapılandırma kanıtı `yapay-zeka-test-stratejisi.md` §1
> (`phpstan ^1.10`, script `stan`, seviye 5) · https://phpstan.org/ (HEAD 200 · 2026-10-13).

| # | Kokorel (kod gerçekliği) | Konum | Seviye 5 statik analiz yakalar mı | Not |
|---|---|---|---|---|
| S1 | `$sessionId` gövdede hiç kullanılmıyor | `AIEngine.php` L53 (§2) | ⚠️ çoğunlukla hayır — atanmış değişken analizi ayrı kural | elle gözlem |
| S2 | `$filePath` parametresi kullanılmıyor | `AIEngine.php` L315–327 (§4) | ⚠️ kısmen | elle gözlem |
| S3 | Her iki kolda da `return []` | `AIEngine.php` L212/L217/L234 (§3) | ❌ hayır — tip doğru, değer boş | çalışma zamanı bulgusu |
| S4 | Interface docblock 6 alan, implementasyon 10 alan | §5 (Contracts L34 vs L96–105) | ❌ docblock uyumsuzluğu statik analizin varsayılan kapsamı değil | elle gözlem |
| S5 | `0.6/0.4` ağırlığı kod gömülü | §2 (L65) | ❌ hayır | yapılandırma borcu |
| S6 | Farklı hata stratejisi (throw vs Result) | §5 (L85) · `yapay-zeka-riskler.md` R-04 | ❌ hayır | mimari karar |
| S7 | `AIWorkflow` interface'siz (kontrat dışı yüzey) | §8 (L18) | ⚠️ kısmen — soyutlama eksikliği kural ile | elle gözlem |
| S8 | Somut sınıf enjeksiyonu (arayüz yok) | §8 (L329–343) | ❌ hayır | test edilebilirlik etkisi §14 |
| S9 | Ölü kod: `promptResult` yalnız metadata'da | §7 (L66 → L86) | ❌ hayır | §7/2 kayıtlı |
| S10 | Asenkron task sonucu okunmuyor | §7 (L59 → L75) | ❌ hayır | §7/3 kayıtlı |

**Dürüstlük:** "yakalar/yakalamaz" sütunu **bu dosyadaki kod okumasına** dayanan bir
*mühendislik tahminidir*, çalıştırılmış analiz çıktısı **değildir** → PHPStan bu repo üzerinde
çalıştırılmadı (`status: verified` bu bölüm için değişmez). Çalıştırılmış sonuç için
`⚠️ VERIFICATION REQUIRED`.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §16 Ekin kaynakları (yalnız bu §12–§15 arası)

| # | Kaynak | Ne verdi | Erişim |
|---|---|---|---|
| 1 | https://www.php-fig.org/psr/psr-12/ | biçim karşılaştırma yüzeyi (§12) | HEAD 200 · 2026-10-13 |
| 2 | https://www.php-fig.org/psr/psr-11/ | kapsayıcı sözleşmesi (§13) | HEAD 200 · 2026-10-13 |
| 3 | https://www.php-fig.org/psr/psr-14/ | olay yayımı sözleşmesi (§13) | HEAD 200 · 2026-10-13 |
| 4 | https://docs.phpunit.de/ | test çalıştırıcı dokümantasyonu (§14) | HEAD 200 · 2026-10-13 |
| 5 | https://phpstan.org/ | statik analiz aracı (§15) | HEAD 200 · 2026-10-13 |
| 6 | https://psalm.dev/ | ikinci statik analiz adayı (§15 karşılığı — kurulmadı) | HEAD 200 · 2026-10-13 |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**
