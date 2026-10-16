---
node_id: K006.3-recommendation-bilesenler
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "shared/src/AI/AIEngine.php L49–77 · L209–264 (2026-10-12 tam okuma)"
  - "shared/src/AI/AIWorkflow.php L49–91 (2026-10-12 tam okuma)"
  - "shared/src/Contracts/AI/AIEngineInterface.php L20–28 (getRecommendations sözleşmesi)"
  - "shared/src/AI/AIOrchestrator.php L26 · L40 (görev tipi 'recommendation')"
validation: "2026-10-12 · tüm metot/satır grep ile doğrulandı; skorlayıcıların 2/2'si boş gövde · status=verified"
open_questions:
  - "Öneri sahipliği: K006 vs K008 vs K020 → ⚠️ VERIFICATION REQUIRED"
  - "0.6/0.4 ağırlıklarının gerekçesi (benchmark) → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K006.3-recommendation — Bileşenler

> K006.3'ün **gerçek** bileşen envanteri. Kural: var olan kod yazılır, olmayan "0 satır" olarak
> boşluk sayılır. Öneri **iskeleti** (ağırlık/eşik/sıralama/çeşitlilik) vardır; skor **üreten**
> iki motor **boş gövdedir**.

---

## §1 Envanter Özeti

| Ölçüm | Değer | Kayıt |
|---|---|---|
| K006.3'e atfedilen ayrı sınıf/dosya | **0** | glob `shared/src/AI/*` — 7 PHP, "recommendation" adında dosya yok |
| Öneri pipeline metotları | 4 (`getRecommendations` L49 · `combineScores` L243 · `applyDiversityFilter` L264 · boş skorlayıcılar L209/L223) | §2 |
| Boş skorlayıcı | **2/2** (`getCollaborativeScores` · `getContentBasedScores` → `[]`) | §2.2 |
| Öneri workflow'u | ✅ **var** (`recommendationWorkflow` L49–91) | §3 |
| Öneri sözleşmesi | 1 imza (`AIEngineInterface::getRecommendations` L28) | §4 |
| Göverit tipi kaydı | `AIOrchestrator::TASK_TYPES` L40 içinde 'recommendation' | §5 |
| Popülerlik baseline | **0 satır** | grep → 0 (2026-10-12) |

---

## §2 Gerçek Kod Bileşenleri

### §2.1 `AIEngine::getRecommendations(array $userPreferences, array $context = []): array` — L49–77 (public)

| Adım | Satır | Davranış |
|---|---|---|
| 1 | 53–55 | boş tercih kontrolü → `[]` |
| 2 | 59–61 | `getCollaborativeScores` → **`[]`** |
| 3 | 63–65 | `getContentBasedScores` → **`[]`** + sabit ağırlık **0.6/0.4** (L65) |
| 4 | 69–71 | `combineScores` + `MIN_SCORE` eşiği ile filtre |
| 5 | 74 | `usort` — `score` göre azalan sıralama |

### §2.2 Boş skorlayıcılar (private)

| Metot | Konum | Davranış |
|---|---|---|
| `getCollaborativeScores` | L209–218 | iki dalda `return [];` — kullanıcı matrisi yok |
| `getContentBasedScores` | L223–235 | `return [];` — özellik/etiket kaynağı yok |

**Bulgu:** L65'teki 0.6/0.4 ağırlık + `combineScores` **boş kümeler üzerinde çalışır** —
bugünkü öneri listesi her koşulda **boştur** (eşikten geçecek skor yoktur).

### §2.3 `AIEngine::combineScores` — L243 · `applyDiversityFilter` — L264 (private)

| Metot | Davranış |
|---|---|
| `combineScores` L243 | iki (bugün boş) kümenin ağırlıklı birleşimi |
| `applyDiversityFilter` L264 | sonucun çeşitliliği — sunum kuralı |

---

## §3 Workflow Bileşeni — `AIWorkflow::recommendationWorkflow(array $request)` L49–91

| Adım | Satır | Çağrı | Kusur/kayıt |
|---|---|---|---|
| 1 · Task dispatch | 59 | `orchestrator->dispatchTask(...)` | **beklenmiyor** (C5) |
| 2 · Bilgi zenginleştirme | 63 | `knowledge->search($query, …)` | K016 yüzeyi |
| 3 · Öneri | ~67–68 | `engine->getRecommendations(...)` | §2.1 |
| 4 · JSON ayrıştırma | 70 | `json_decode(..., JSON_THROW_ON_ERROR)` | fırlatır (R-12) |
| 5 · Prompt | 86 | prompt **yalnız metadatayı** besler — öneri içeriği değil | kalite değil |
| çıkış | 88–90 (yaklaşık bölge) | sonuç + `execution_time` | süre izi |

---

## §4 Sözleşme Eşlemesi

| Sözleşme | İmza | Konum | Uygulayan |
|---|---|---|---|
| `AIEngineInterface::getRecommendations(...)` | 1/5 | Contracts L28 · docblock L20–26 | `AIEngine` L49 |
| (öneri için ayrı interface) | — | **YOK** | — |

`@return` docblock (Contracts L26) 6 alan ister: `id, title, artist, score, genre, reason` —
alanlar tanımlı, **skor kaynağı boş** (§2.2).

---

## §5 Görev Kaydı — `AIOrchestrator`

| Bileşen | Konum | K006.3 ilişkisi |
|---|---|---|
| `AGENT_MAP` | L26 | ajan eşleme — 'recommendation' görevi dağıtılabilir |
| `TASK_TYPES` | L40 | görev tipleri içinde 'recommendation' |
| `dispatchTask` | L71–100 | workflow'un L59'da çağırdığı dağıtım |

**Kayıt:** önerinin **görev tipi** tanımlıdır; ancak iş mantığı K006.3'te değil, K016
orkestrasyonu üzerinden akar (sınır kaydı).

---

## §6 Boşluk Tablosu (beklenen bileşen → repo durumu)

| Beklenen bileşen (K006.3 kapsamı) | Repo durumu | Kanıt |
|---|---|---|
| İşbirlikçi skor motoru | **0 satır** (boş gövde) | L209–218 |
| İçerik-tabanlı skor motoru | **0 satır** (boş gövde) | L223–235 |
| Popülerlik baseline | **0 satır** | grep → 0 |
| Önbellek (sıcak öneri) | **0 satır** | composer.json cache paketi yok |
| Ağırlık yapılandırması/benchmark | **0 satır** (0.6/0.4 sabit) | L65 |
| Öneri sözleşmesi (alanlar) | ✅ **var** | Contracts L26 |
| Hibrit iskelet (eşik/sıralama/çeşitlilik) | ✅ **var** | L71 · L74 · L264 |
| Öneri workflow'u | ✅ **var** (kusurlu dispatch) | L49–91 |

---

## §7 Kardeş Eşlemesi

| Bileşen | İlişki |
|---|---|
| K006.4-personalization | profil→skor girdisi; bugün boş skorlayıcılar ortak kırılma noktası |
| K006.5-music-analysis | katalog öznitelikleri içerik-tabanlı skorlamayı beslemeli — placeholder (L315–330) kırıyor |
| K006.8-evaluation | ağırlık (0.6/0.4) kalitesi ölçülmedikçe iyileştirilemez |
| K008/K020 (dış) | **sahiplik 3-way açık soru** — kod bu düğümlerden hiçbirine yazılmamıştır |

---

## §8 Kaynaklar

1. `shared/src/AI/AIEngine.php` — L49–77 · L209–264 tam okuma, 2026-10-12.
2. `shared/src/AI/AIWorkflow.php` — L49–91 tam okuma, 2026-10-12.
3. `shared/src/AI/AIOrchestrator.php` — L26 · L40 · L71–100, 2026-10-12.
4. `shared/src/Contracts/AI/AIEngineInterface.php` — L20–28, 2026-10-12.
5. `recommendation-web-arastirma.md` §5 — çapraz bağ.

---

`status: verified` · `validation: 2026-10-12 · 5 gerçek metot + 1 workflow + 2 orkestratör sabiti doğrulandı, 8 beklenen bileşenden 5'i 0 satır`

---

# EK (2026-10-15)

> **§0 Taze okuma:** dosya başlangıçta **148 satır** (`read(offset=9999, limit=1)`).
> Bu EK'in tüm kanıtları bugün doğrudan okumalardandır (offset/limit ile):
> `AIEngine.php` L45-84 · L205-313 · L314-353 · `AIWorkflow.php` L1-94 ·
> `AIEngineInterface.php` (tam) · `MemorySystemInterface.php` (tam) · `AIOrchestrator.php` L20-104 ·
> `MemorySystem.php` L36-75 · glob `shared/src/AI/*` · glob `shared/src/Knowledge/*` ·
> grep `popular|Popüler` (shared/src) · grep `cache` (composer.json + shared/src/AI).
> Kod dosyalarına yazım yok · URL yok (dayanak kardeş `recommendation-web-arastirma.md`) ·
> append-only · git commit yok.

## EK-1 · Düzeltme Kayıtları (eski satırlar silinmedi — üstteki § referansları tarihseldir)

**[R-D1] §2.1 imzası YANLIŞ.** Eski: "`getRecommendations(array $userPreferences, array $context = [])`".
Taze: `public function getRecommendations(array $context, int $limit = 20): array`
(`Dosya: shared/src/AI/AIEngine.php [Satır: 49]`). `array $userPreferences` **yoktur**; `int $limit = 20`
vardır. (Bu düzeltmenin ilk kaydı kardeş `evaluation-bilesenler.md` EK §1'dedir; dosya-kendi-uyarlaması.)

**[R-D2] §2.1 adım tablosu 1. satır KODDA YOK.** Eski: "53–55 · boş tercih kontrolü → `[]`".
Bugünkü tam okuma L49-77'de **hiçbir erken dönüş/boş-tercih kontrolü yoktur** — L50 `{`,
L51 `$limit = min($limit, self::MAX_RECOMMENDATIONS);`, L52-56 bağlam çıkarımı
(`Dosya: shared/src/AI/AIEngine.php [Satır: 50-56]`). Doğru sonuç kardeş `evaluation` EK §1 ile aynıdır:
"bu satır bugünkü kodda yok". Boş `$context` yine `[]` döner ama **pipeline sonunda** (L59·L62·L65·L68·L71·L74·L76 —
bkz. EK-3 §2 zinciri; kişisel düzeltme kaydı `personalization-bilesenler.md` EK-6/[P-Y1]).

**[R-D3] §2.1 adım tablosu satır sınırları ✗ (içeriklerin çoğu ✓).** Taze karşılık:

| Eski satır | Eski aralık | Taze gerçek | Karar |
|---|---|---|---|
| 1 · boş tercih | 53–55 | **yok** ([R-D2]) | ✗ |
| 2 · collaborative | 59–61 | yorum L58 · çağrı **L59** (`getCollaborativeScores($userId, $history)`) | ≈✓ (L61 içerik yorumudur) |
| 3 · content + 0.6/0.4 | 63–65 | yorum L61 · çağrı **L62** (`getContentBasedScores($context)`) · ağırlık **L65** ✓ (`combineScores(..., 0.6, 0.4)`) | içerik ✓, çağrı satırı ✗ |
| 4 · combine + MIN_SCORE | 69–71 | combine **L65** (satır 3'te) · çeşitlilik çağrısı **L68** · `MIN_SCORE` filtresi **L71** | ✗ (combine tekrarı + L68 atlanmış) |
| 5 · usort | 74 | **L74** ✓ (`usort($filtered, fn($a,$b) => $b['score'] <=> $a['score'])`) | ✓ |
| (eksik) | — | **L51** `min($limit, MAX_RECOMMENDATIONS)` · **L52-56** bağlam çıkarımı · **L76** `array_slice(array_values($filtered), 0, $limit)` | tamamlama |

**[R-D4] §3 workflow tablosu 3/6 satır ✗ (taze tam okuma L49-91):**

| Eski iddia | Taze gerçek | Karar |
|---|---|---|
| "3 · Öneri · ~67–68 · `engine->getRecommendations`" | yorum **L74** · çağrı **L75** (`$this->engine->getRecommendations($context, $context['limit'] ?? 10)`) | ✗ satır |
| "4 · JSON ayrıştırma · **70** · `json_decode(..., JSON_THROW_ON_ERROR)`" | L70 **`json_encode`** (prompt parametresi `history`: `json_encode($context['history'] ?? [], JSON_THROW_ON_ERROR \| JSON_UNESCAPED_UNICODE)`) — **decode değil, encode**; ayrıştırma yok | ✗ hem yön hem ad |
| "5 · Prompt · **86** · prompt yalnız metadatayı besler" | prompt üretimi **L66-72** (`generatePrompt('recommendation', [...])`); **L86** yalnız `'prompt_tokens' => $promptResult['tokenCount']` **metadata okumasıdır** | ✗ satır (metadata ✓) |
| "çıkış · 88–90 (yaklaşık bölge)" | `return` bloğu **L82-90**; `'task_id'` L85 · `'prompt_tokens'` L86 · `'knowledge_hits'` L87 · `'execution_time'` L89 | ≈✓ (tam aralık) |
| "1 · dispatch · 59 · **beklenmiyor (C5)**" | `dispatchTask('recommendation', $context, 'medium')` **L59'da ÇAĞRILIR** ✓ (eski "beklenmiyor" yorumu bu dosyanın kendi C5 iddiasıdır — kod çağrıyı doğrular, yorumu **bu EK'te yeniden değerlendirilmedi** → `⚠️`) | çağrı ✓, yorum ⚠️ |
| "2 · bilgi zenginleştirme · 63" | `$query = ($context['mood'] ?? '') . ' ' . ($context['genre'] ?? '')` **L62** · `$this->knowledge->search($query, 5)` **L63** ✓ | ✓ |

**[R-D5] §4 docblock aralığı ve alan sayısı ✗.** Eski: "docblock L20–26 · `@return` (L26) **6 alan**:
id, title, artist, score, **genre**, reason".
Taze: docblock **L21-27** · `@return` **L26** = `array<int, array{id: string, title: string, artist: string, score: float, reason: string}>`
→ **5 alan; `genre` YOKTUR** (`Dosya: shared/src/Contracts/AI/AIEngineInterface.php [Satır: 21-28]`).
İmza satırı **L28** ✓ ve implementasyonla **birebir aynı** (AIEngine L49 ile parametre-yönden hizalı: `array $context, int $limit = 20`).

**[R-D6] §5 `dispatchTask` aralığı ✗.** Eski: "L71–100". Taze: `public function dispatchTask(string $type, array $params, string $priority = 'medium'): string` **L71** · bilinmeyen tip `InvalidArgumentException` **L73-75** · `generateTaskId()` L77 · görev kaydı **L80-91** · `executeTask($taskId)` **L94** (anında) · `return $taskId` **L96-97** · `getTaskStatus` **L99+** (`Dosya: shared/src/AI/AIOrchestrator.php [Satır: 71-97]`).

**[R-D7] §6 önbellek satırının composer iddiası ✗.** Eski: "Önbellek (sıcak öneri) · 0 satır ·
**composer.json cache paketi yok**". Taze: `shared/composer.json` **L16 `psr/cache: ^3.0`** vardır
(ayrıca L3 description "…Cache…" geçer) → "cache paketi yok" **yanlış**. Doğru kalan kısım:
**öneri-özel önbellek gerçekten 0** — `recommendationWorkflow` tam okumasında (L49-91) cache/remember
çağrısı **yoktur**; `shared/src/AI` genelinde `cache` isabetleri yalnızca `MemorySystem` (genel
`$cache` L40, `getCacheStats` L162, `remember` L188+) ve `AIWorkflow` **EQ** workflow'unda
(L172-174: `eq_{sessionId}_…` + `remember(..., 'l2', 3600)`) → **öneri akışında önbellek yok** ✓
(`Dosya: shared/src/AI/AIWorkflow.php [Satır: 172-174]` + tam okuma L49-91).

**[R-D8] §7 `extractFeatures` aralığı ✗ (içerik ✓).** Eski: "placeholder (L315–330)".
Taze: `private function extractFeatures(string $filePath): array` **L315-327** — yorumlar
L317-318 ("Gerçek implementasyonda C++ DSP engine ile yapılacak" / "Şimdilik placeholder değerler") ve
6 sabit: `'bpm' => 120.0` · `'key' => 'C'` · `'energy' => 0.7` · `'mood' => 'happy'` ·
`'danceability' => 0.6` · `'acousticness' => 0.3` (L319-326). L329-330 **başka metodun** docblock'udur
(`Dosya: shared/src/AI/AIEngine.php [Satır: 315-327]`).

**[R-D9] §3 "json_decode … fırlatır (R-12)" iddiası ✗** — L70 `json_encode` olduğundan (bkz. [R-D4])
atıf edilen davranış yönü yanlıştır. `JSON_THROW_ON_ERROR` **encode** tarafındadır; geçersiz UTF-8
girdide `json_encode` fırlatır (`Dosya: shared/src/AI/AIWorkflow.php [Satır: 70]`). "R-12" claim-id
kardeş `recommendation-web-arastirma.md`'dedir ve bu EK'te **tekrar okunmadı** → `⚠️`
(bağ korunur, içerik tekrarlanmaz).

**[R-D10] §2.3 "çeşitlilik = sunum kuralı" kaydı YETERSİZ — taze şartname EK-2.2'dedir.**
Kardeş `personalization-bilesenler.md` EK-7 [Y5]/[Y6] bu dosyanın da paylaştığı `AIEngine.php`
üzerinde ilk düzeltmeyi yaptı (ceza **yalnız tercih edilen türe**, `===`, `%30` aşımda `*= 0.8`;
hibrit çıktısında `genre` anahtarı olmadığından pratikte **hiç tetiklenmez**). Tekrar yok —
burada yalnız **kendi satır kanıtımla** şartname EK-2.2'de verilir.

## EK-1b · Taze Verbatim Kanıtlar (bugün, birebir)

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 49-77] (davranış adımları özeti — tam metin okundu)
public function getRecommendations(array $context, int $limit = 20): array
{
    $limit = min($limit, self::MAX_RECOMMENDATIONS);              // L51
    $userId = $context['user_id'] ?? null;                        // L52
    $sessionId = $context['session_id'] ?? null;                  // L53  → L49-77'de okunmaz (ölü)
    $mood = $context['mood'] ?? null;                             // L54  → L49-77'de okunmaz (ölü)
    $genre = $context['genre'] ?? null;                           // L55
    $history = $context['history'] ?? [];                         // L56
    $collaborativeScores = $this->getCollaborativeScores($userId, $history);  // L59
    $contentScores = $this->getContentBasedScores($context);                  // L62
    $hybridScores = $this->combineScores($collaborativeScores, $contentScores, 0.6, 0.4); // L65
    $diversified = $this->applyDiversityFilter($hybridScores, $genre);        // L68
    $filtered = array_filter($diversified, fn(array $item) => $item['score'] >= self::MIN_SCORE); // L71
    usort($filtered, fn(array $a, array $b) => $b['score'] <=> $a['score']);  // L74
    return array_slice(array_values($filtered), 0, $limit);                    // L76
}
```

> Not: L58/L61/L64/L67/L70/L73 yorum satırlarıdır (`// 1. Collaborative filtering …` vb.) —
> gövde satırları yukarıdaki gibidir (`Dosya: shared/src/AI/AIEngine.php [Satır: 58, 61, 64, 67, 70, 73]`).

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 209-218] — private, $history hiç okunmaz
private function getCollaborativeScores(?string $userId, array $history): array
{
    if ($userId === null) { return []; }        // L211-213 (tek dal — empty($history) YOK)
    // DB'den benzer kullanıcıları bul
    // Gerçek implementasyonda matrix factorization (ALS) kullanılacak
    return [];                                   // L217
}
```

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 223-235] — private, $targetFeatures kurulur ama okunmaz
private function getContentBasedScores(array $context): array
{
    $targetFeatures = [
        'bpm'          => $context['bpm'] ?? null,           // L226
        'energy'       => $context['energy'] ?? null,        // L227
        'danceability' => $context['danceability'] ?? null,  // L228
        'genre'        => $context['genre'] ?? null,         // L229
    ];
    // DB'den aday şarkıları çek ve özelliklerini karşılaştır   // L232
    // cosine similarity kullanarak skorla                       // L233
    return [];                                                  // L234
}
```

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 243-259] — tek satır imza; çıktı YALNIZ id+score
private function combineScores(array $collaborative, array $content, float $collabWeight, float $contentWeight): array
{
    $allKeys = array_unique(array_merge(array_keys($collaborative), array_keys($content))); // L245
    $combined = [];
    foreach ($allKeys as $key) {
        $collabScore = $collaborative[$key]['score'] ?? 0;     // L249 — ?? 0 guard'ı
        $contentScore = $content[$key]['score'] ?? 0;          // L250
        $combined[$key] = ['id' => $key, 'score' => ($collabScore * $collabWeight) + ($contentScore * $contentWeight)]; // L252-255
    }
    return $combined;                                          // L258
}
```
> Docblock: `@param array<string, array{score: float}>` ×2 (**L240-241**; docblock L237-242).

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 264-283] — ceza yalnız tercih edilen türe
private function applyDiversityFilter(array $scores, ?string $preferredGenre): array
{
    $diversified = [];                                         // L266
    $genreCounts = [];                                         // L267
    foreach ($scores as $id => $item) {                        // L269
        $genre = $item['genre'] ?? 'unknown';                  // L270
        $genreCount = $genreCounts[$genre] ?? 0;               // L271
        // Tercih edilen türden çok fazla olmasın               // L273
        if ($genre === $preferredGenre && $genreCount > self::DIVERSITY_TARGET * count($scores)) { // L274
            $item['score'] *= 0.8;                             // L275
        }
        $genreCounts[$genre] = $genreCount + 1;                // L278
        $diversified[$id] = $item;                             // L279
    }
    return $diversified;                                       // L282
}
```

```php
// Dosya: shared/src/AI/AIWorkflow.php [Satır: 49-91] — recommendationWorkflow adım haritası
public function recommendationWorkflow(array $request): array   // L49
{
    $startTime = microtime(true);                               // L51
    $sessionId = $request['session_id'] ?? 'anonymous';         // L52
    $context = $this->memory->getSession($sessionId, 'preferences') ?? [];  // L55 (2 argüman ✓)
    $context = array_merge($context, $request);                 // L56
    $taskId = $this->orchestrator->dispatchTask('recommendation', $context, 'medium'); // L59
    $query = ($context['mood'] ?? '') . ' ' . ($context['genre'] ?? '');  // L62
    $knowledgeResults = $this->knowledge->search($query, 5);    // L63
    $promptResult = $this->promptEngine->generatePrompt('recommendation', [  // L66
        'mood' => ..., 'genre' => ..., 'energy' => ...,         // L67-69
        'history' => json_encode($context['history'] ?? [], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE), // L70
        'limit'  => $context['limit'] ?? 10,                    // L71
    ], ['knowledge' => $knowledgeResults]);                     // L72
    $recommendations = $this->engine->getRecommendations($context, $context['limit'] ?? 10); // L75
    $this->memory->setSession($sessionId, 'last_recommendations', $recommendations, 3600);   // L78 (4 argüman ✓)
    $executionTime = microtime(true) - $startTime;              // L80
    return ['recommendations' => ..., 'metadata' => [...], 'execution_time' => round($executionTime, 4)]; // L82-90
}
```

```php
// Dosya: shared/src/Contracts/AI/AIEngineInterface.php [Satır: 21-28] — docblock + imza
/**
 * Müzik önerisi üretir.
 *
 * @param array<string, mixed> $context Kullanıcı bağlamı (tercihler, geçmiş, mood)   // L24
 * @param int                  $limit   Maksimum öneri sayısı                         // L25
 * @return array<int, array{id: string, title: string, artist: string, score: float, reason: string}> // L26 — 5 alan
 */
public function getRecommendations(array $context, int $limit = 20): array;          // L28
```

**Taze grep/glob kanıtları (bugün):**

- `glob shared/src/AI/*` → **7 PHP** (`AIEngine · AIWorkflow · AIOrchestrator · KnowledgeBase · MemorySystem · PromptEngine · ToolCalling`) + `CLAUDE.md` → §1 "7 PHP" ✓ (**taze teyit**).
- `glob shared/src/Knowledge/*` → **0 dosya** → eski `Knowledge/MemorySystem.php` yolu **ölü** (kardeş personalization EK-7 ile hizalı; bu dosyanın kendi §8/§6 atıflarına da uygulanır).
- grep `popular|Popüler` (shared/src) → **0 eşleşme** → §1 "Popülerlik baseline 0 satır" ✓ **taze teyit**.
- `shared/composer.json` grep `cache|redis|memcache` → L3 (description) · **L16 `psr/cache: ^3.0`** → [R-D7].
- `AIOrchestrator` **L41** `'recommendation' => ['agent' => 'backend', 'priority' => 'medium']` (TASK_TYPES L40-49) · `AGENT_MAP` **L26-38** (11 ajan) → §5 ✓ taze teyit.

## EK-1c · Kardeş Bağ (tekrar yasağı)

- [R-D1] ilk kayıt: `../evaluation/evaluation-bilesenler.md` EK §1 · kişisel uyarlama `../personalization-bilesenler.md` EK-1 [P-D5] + EK-6.
- Çeşitlilik mantığı düzeltmesinin ilk kaydı: `../personalization-bilesenler.md` EK-7 [Y5]/[Y6] → bu dosyada **tekrar edilmez**, şartname EK-2.2'ye bağlanır.
- `optimizeEQ` D1/D2 · inference 3 düzeltme · `A-MM-1` → **bu dosyada yok** (aynı `AIEngine.php`'ye taze okuma yapılsa da her dosya kendi satır düzeltmesini kendi EK'inde yazar).
- URL havuzu: `../recommendation-web-arastirma.md` EK'leri — bu EK'te **yeni URL 0**.
- Test dayanağı: `../testing-web-arastirma.md` + `../evaluation/evaluation-bilesenler.md` EK-4.

## EK-1d · Taze Bulgular (seri R-S)

- **[R-S1]** `getRecommendations` **pipeline'da erken kapı yok** — her adım zorunlu akar; boş girdi
  dahil tüm sonuçlar L76 `array_slice`'tan `[]` olarak çıkar (L51-76, [R-D2]/[R-D3]).
- **[R-S2]** `$limit` **ölü değil**: `min($limit, MAX_RECOMMENDATIONS=20)` (L51) + `array_slice(...,0,$limit)` (L76) →
  `getRecommendations($ctx, 3)` en fazla 3 döner (`Dosya: shared/src/AI/AIEngine.php [Satır: 51, 76]`).
  (Kardeş personalization EK-7 [Y9] ile hizalı; FP-2 kapanışı orada.)
- **[R-S3]** İki skorlayıcı da **private ve boş**: `getCollaborativeScores` yalnız `$userId === null`
  dalında `[]` (L211-213) + L217 `return []`; `$history` parametresi **hiç okunmaz** ·
  `getContentBasedScores` `$targetFeatures` kurar (L225-230) ama **okumaz**, L234 `return []` →
  **öneri listesi bugünkü kodda koşul ne olursa olsun boştur** (eski §2.1 "Bulgu" satırı ✓ taze teyit).
- **[R-S4]** `combineScores` çıktısı **yalnız `id` + `score`** (L252-255) → `genre` yok →
  `applyDiversityFilter` içinde her öge `'unknown'` (L270) → ceza koşulu `'unknown' === $preferredGenre`
  (L274) yalnız `$context['genre'] === 'unknown'` iken %30 aşımda tetiklenebilir →
  **pratikte çeşitlilik indirimi etkisizdir** (L55·L68·L270·L274 — kardeş [Y6] ile hizalı, kendi satır kanıtıyla).
- **[R-S5]** `MIN_SCORE` filtresi **L71**'dedir (`$item['score'] >= self::MIN_SCORE`, sabit `MIN_SCORE = 0.7` L32) —
  eski §6'daki "eşik L71" ✓ taze teyit; eşiğin uygulanabilir olması için skor üretimi gerekir ([R-S3] nedeniyle **şu an boş küme üzerinde etkisiz**).
- **[R-S6]** Workflow **öneri geri bildirimini oturuma yazar**: `setSession($sessionId, 'last_recommendations', $recommendations, 3600)` (L78) —
  imza `MemorySystem::setSession(string,string,mixed,int=0):bool` (MemorySystem L45) ile **birebir uyumlu**; TTL 3600 sn.
- **[R-S7]** Workflow `AIWorkflow::$memory` **somut `MemorySystem`**'dir (L24·L32 — interface değil);
  `getSession($sessionId,'preferences') ?? []` (L55) TTL dolumunda `null` → `[]` fallback'i çalışır
  (`Dosya: shared/src/AI/MemorySystem.php [Satır: 60-75]`).
- **[R-S8]** Prompt adımı (L66-72) ile motor adımı (L75) **aynı `$context`'i** kullanır; prompt'a giden
  `history` `json_encode` (L70) olur ve **motor tarafından okunmaz** (motor L56'daki ham diziyi okur) →
  prompt ile sonuç ayrı yollar, eski §3 "prompt yalnız metadatayı besler" kaydı ✓ davranış olarak doğru
  (satır düzeltmesi [R-D4]).
- **[R-S9]** `dispatchTask('recommendation', …, 'medium')` (L59) → TASK_TYPES L41: `agent=backend`,
  `priority=medium` ✓ · anında `executeTask` (L94) → **asenkron kuyruk yok**, senkron çağrı
  (`Dosya: shared/src/AI/AIOrchestrator.php [Satır: 94]`).
- **[R-S10]** Boş-`$context`-benzeri workflow senaryosunda `$query = ' '` (L62, iki boş string + boşluk)
  → `knowledge->search(' ', 5)` çağrısı **boş/tek-boşluk sorguyla** gider (`Dosya: shared/src/AI/AIWorkflow.php [Satır: 62-63]`).
- **[R-S11]** `extractFeatures` 6 sabit placeholder (L319-326) — music-analysis beslemesi **gerçek
  özellik üretmez** ([R-D8]; §7 kardeş bağı K006.5 ✓ davranışsal olarak korunur).
- **[R-S12]** Öneri-özel önbellek **0** · genel `MemorySystem` cache'i ve EQ cache'i var ama
  `recommendationWorkflow` akışında **kullanılmaz** ([R-D7]).

## EK-1e · EK-1 Kapı

- Düzeltme: **10** ([R-D1]–[R-D10]) · yeni URL: **0** · silme: **0** · kod yazımı: **0** · git commit: **yok**.
- Tüm yeni iddialar `Dosya:` + `Satır:` taşır (grep/glob iddiaları araç + hedefle).
- Ölçüm: **ölçülmedi** (EK-1..4 tek akışta — zincir EK-4).

---

## EK-2 · Şartname ve Hata Modları (taze; dayanak kardeş, URL 0)

### EK-2.1 · Arayüz Şartnamesi

| # | Sözleşme | İmza (taze) | Konum | Not / boşluk |
|---|---|---|---|---|
| C-R1 | `getRecommendations` | `(array $context, int $limit = 20): array` **public** | AIEngine L49 · Interface L28 | iki taraf birebir aynı ✓ · erken kapı yok [R-S1] |
| C-R2 | `getCollaborativeScores` | `(?string $userId, array $history): array` **private** | L209 | `$history` ölü parametre · daima `[]` [R-S3] |
| C-R3 | `getContentBasedScores` | `(array $context): array` **private** | L223 | özellikler kurulur-okunmaz · daima `[]` [R-S3] |
| C-R4 | `combineScores` | `(array, array, float, float): array` **private** | L243 | docblock `array<string, array{score: float}>` L240-241 · `?? 0` guard L249-250 · çıktı `id+score` L252-255 |
| C-R5 | `applyDiversityFilter` | `(array $scores, ?string $preferredGenre): array` **private** | L264 | ceza yalnız tercih+`%30` aşımda L274-275 · filtreleme **yok** (tüm ögeler döner L279) |
| C-R6 | `recommendationWorkflow` | `(array $request): array` | AIWorkflow L49 | `@return` docblock L47: `{recommendations, metadata, execution_time}` ✓ (L82-90) |
| C-R7 | Interface `@return` öğesi | `{id, title, artist, score, reason}` — **5 alan** | Contracts L26 | `genre` **yok** [R-D5] |
| C-R8 | `dispatchTask` | `(string $type, array $params, string $priority='medium'): string` | AIOrchestrator L71 | bilinmeyen tip → `InvalidArgumentException` L73-75 · senkron `executeTask` L94 |
| C-R9 | `MemorySystem::setSession` | `(string $sessionId, string $key, mixed $value, int $ttl=0): bool` | MemorySystem L45 · Interface L24 | workflow L78 ile 4 argüman ✓ |
| C-R10 | `MemorySystem::getSession` | `(string $sessionId, string $key): mixed` | MemorySystem L60 · Interface L33 | TTL dolumu → `null` (L69-72) · workflow L55 `?? []` ✓ |

### EK-2.2 · Davranış Şartnamesi (koşul → sonuç)

1. **Limit:** `$limit = min($limit, 20)` (L51) · sonuç en fazla `$limit` öğe (L76).
2. **Boş/başarısız skor:** iki skorlayıcı `[]` → `combineScores([], [], .6, .4)` → `[]` (L245-258 küme
   birleşimi boş) → `applyDiversityFilter([], …)` → `[]` (L269 döngü hiç çalışmaz) → `array_filter([])` →
   `usort([])` → `array_slice([])` = **`[]`** → EK-3 §2 zinciri.
3. **Çeşitlilik:** girdi `hybridScores` (id+score) olduğundan `$genre` daima `'unknown'` (L270);
   `$preferredGenre = $context['genre'] ?? null` (L55). Ceza **yalnız** `'unknown' === preferred` **ve**
   aynı türün sayacı `> 0.30 × n` iken `score *= 0.8` (L274-275). `null`/farklı tercihte ceza **hiç yok**.
   Sayılar her öge için `+1` (L278); **eleme yoktur** (L279 her ögeyi geçirir).
4. **Eşik:** yalnız `score >= 0.7` kalanlar (L71) — bugünkü boş kümede etkisiz.
5. **Sıralama:** `score` azalan (L74) · `array_values` + `array_slice` (L76) — `array_filter` anahtar
   boşluklarını temizler.
6. **Workflow:** `preferences` oturum-okuma (L55) → `array_merge` (L56) → dispatch (L59, senkron) →
   `search(' {boşluk}', 5)` riski ([R-S10]) → prompt (L66-72, ayrı yol) → motor (L75) →
   `last_recommendations` yazımı 3600 sn (L78) → `{recommendations, metadata, execution_time}` (L82-90).
7. **Geri bildirim:** `setSession` `true` döner (L54); başarısızlık yolu **yoktur** (koşul yok) →
   yazım her zaman `true` (kod kanıtı L47-54).

### EK-2.3 · Hata Modları (RF serisi — bu dosyaya özel; kardeş FP'lerle çakışmaz)

| Kod | Hata modu | Tetik | Etki | Kanıt |
|---|---|---|---|---|
| **RF-1** | **Boş-öneri döngüsü (kök kusur)** | L217 + L234 | tüm pipeline `[]` üzerinden çalışır; eşik/sıralama/limit fiilen **ölü kod** | AIEngine L217·L234·L71·L76 |
| **RF-2** | Ölü bağlam değişkenleri | L53-54 | `$sessionId`/`$mood` okunmaz → ilgili bağlam taşınmışsa **sessiz kayıp** (mood yalnız `$context` üzerinden L62'ye girer) | L53-54·L62 |
| **RF-3** | Tek-boşluk bilgi sorgusu | workflow L62-63 | mood/genre yokken `search(' ', 5)` → alakasız/bol isabet riski | AIWorkflow L62-63 |
| **RF-4** | Senkron dispatch | L59+L94 | `executeTask` çağrı zincirinde; timeout/wf gecikmesi doğrudan kullanıcıya yansır (asenkron değil) | AIOrchestrator L94 |
| **RF-5** | `json_encode` fırlatması | L70 `JSON_THROW_ON_ERROR` | `history` geçersiz UTF-8 ise **workflow tamamen hata verir** (öneri üretilmez) | AIWorkflow L70 |
| **RF-6** | Çeşitlilik fiilen etkisiz | L252-255 + L270 + L274 | `genre` anahtarı yokluğunda tür-dışı dengeleme **çalışmaz** (%30 garantisi uygulanamaz) | L252-255·L270·L274 |
| **RF-7** | `genre` alanı sözleşme-dışı | Interface L26 (5 alan, `genre` yok) vs `applyDiversityFilter` L270 (`$item['genre']`) | motorun kendi ürettiği sözleşmede `genre` **olmadığından** L270 hep `'unknown'` — tasarım-uyumsuzluk | Contracts L26 + AIEngine L270 |
| **RF-8** | TTL sessiz silme | MemorySystem L69-72 | `preferences` anahtarı dolduğunda `getSession` → `null` → workflow `[]` fallback'i **sessiz veri kaybı** (uyarı yok) | MemorySystem L69-72 · AIWorkflow L55 |
| **RF-9** | Önbellek yokluğu | workflow L49-91 | her çağrı tam pipeline (dispatch + search + prompt + motor); sıcak öneri önbelleği **0** ([R-D7]) | tam okuma L49-91 |
| **RF-10** | `$history` ölü parametre | L209·L217 | geçmiş olay `/ müzik geçmişi taşınsa bile işbirlikçi skora **girmez** (kodda okunmuyor) | L209·L217 |

### EK-2.4 · Boşluklar (beklenen bileşen → bugün)

| Boşluk | Durum | Kanıt |
|---|---|---|
| İşbirlikçi skor motoru | **0 satır** (yorum + `return []`) | L215-217 |
| İçerik-tabanlı skor motoru | **0 satır** (yorum + `return []`) | L232-234 |
| Popülerlik baseline | **0** | grep `popular` → 0 (bugün) |
| Öneri önbelleği | **0** (`psr/cache` var ama akışta kullanılmıyor) | [R-D7] |
| Ağırlık benchmark'ı | **0** — 0.6/0.4 sabit (L65), gerekçe `⚠️ VERIFICATION REQUIRED` (frontmatter açık sorusu korunur) | L65 · frontmatter |
| Tür alanı (`genre`) | sözleşmede yok, filtrede okunuyor → **tasarım boşluğu** | RF-7 |
| Test | bu EK'te tarama **yapılmadı** (yapısal yasağın gereği) → dayanak kardeş `testing-web-arastirma.md` + `evaluation` EK-4 | §1c |
| KVKK/edep-tuşu | bu EK'te taze kanıt **yok** → `⚠️`, dayanak `../personalization-web-arastirma.md` EK-3 | §1c |

### EK-2.5 · EK-2 Kapı

- Yeni URL: **0** · yeni iddialar: yalnız okunan kod satırları · `⚠️ VERIFICATION REQUIRED`: 3
  (R-12 claim içeriği · C5 "beklenmiyor" yorumu · benchmark gerekçesi + KVKK dayanağı kardeşte).
- Ölçüm: **ölçülmedi** (tek akış — EK-4 §7).

---

## EK-3 · Test Taslakları (çalışan test yok — salt şartname; kod dosyasına yazım 0)

### EK-3.1 · Senaryolar (RT serisi)

| # | Senaryo | Arrange | Act | Assert (taslak) | Bağ |
|---|---|---|---|---|---|
| RT-1 | Boş bağlam | `[]` | `getRecommendations([])` | `=== []` (L76 üzerinden; **erken kapı iddiası yok**) | [R-S1] |
| RT-2 | Limit kırpma | dolu `$context` (gerçekçi: skor yoksa `[]` — aşağıda RT-14) | `getRecommendations($ctx, 3)` | `count ≤ 3` (L51 `min(3,20)=3` + L76) | [R-S2] |
| RT-3 | Null kullanıcı | `['user_id'=>null, 'history'=>[1]]` | `getCollaborativeScores(null, [1])` (Reflection) | `=== []` (L211-213) | C-R2 |
| RT-4 | **`$history` ölü** | `['user_id'=>'u1','history'=>[1,2,3]]` | `getCollaborativeScores('u1',[1,2,3])` | `=== []` (L217) — history **etkisiz** | RF-10 |
| RT-5 | Content kurulum-okuma | `['bpm'=>100,'genre'=>'rock']` | `getContentBasedScores(...)` | `=== []` (L234); features okunmadan | C-R3 |
| RT-6 | Combine ağırlık | `['a'=>['score'=>1.0]]`, `['b'=>['score'=>1.0]]` | Reflection `combineScores(..., 0.6, 0.4)` | `['a']['score']=0.6` · `['b']['score']=0.4` · anahtar `id+score` (L252-255) | C-R4 |
| RT-7 | **`?? 0` guard** | `['a'=>[]]` (score yok) + `[]` | `combineScores(['a'=>[]], [], 0.6, 0.4)` | `['a']['score']=0.0` — **hata YOK** (RF-5 düzeltmesi/FP-5 geri çekimi) | L249 |
| RT-8 | Çeşitlilik: ceza tetiği | 10 öge, `genre='rock'`, `preferred='rock'` | `applyDiversityFilter($s,'rock')` | ilk 3 rock `×1.0` (0,1,2 ≤ %30·10=3 → `>` koşulu), 4. ve sonrası `×0.8` (L274-275: `count > 3`) | C-R5 |
| RT-9 | Çeşitlilik: null tercih | aynı 10 öge, `preferred=null` | `applyDiversityFilter($s,null)` | **hiçbir** `×0.8` yok (L274 `=== null` asla) | EK-2.2-3 |
| RT-10 | Çeşitlilik: eleme yok | 10 öge | dönüş | `count === 10` (L279 her ögeyi geçirir) | C-R5 |
| RT-11 | `'unknown'` normalizasyonu | öge `genre` anahtarsız, `preferred='unknown'` | `applyDiversityFilter` | `$genre='unknown'` (L270) → eşleşme + %30 aşımında `×0.8` | L270·L274 |
| RT-12 | Hibritte `genre` yokluğu (**RF-6/RF-7**) | `combineScores` çıktısı | `applyDiversityFilter(hybrid, 'rock')` | `'unknown'==='rock'` → **ceza yok** → kırık tasarım **teyitli** | [R-S4] |
| RT-13 | `setSession`/`getSession` TTL | `MemorySystem` | `setSession('s1','k','v',1)` → hemen `getSession('s1','k')` | `=== 'v'` (L47-54) | C-R9/C-R10 |
| RT-14 | **Tam akış (boş-öneri)** | boş bağlam | `recommendationWorkflow([])` | `recommendations === []` · `metadata.task_id` string · `execution_time > 0` (L82-90) — **RF-1 kanıtı** | C-R6 |
| RT-15 | Dispatch bilinmeyen tip | `AIOrchestrator` | `dispatchTask('nope', [])` | `InvalidArgumentException` (L73-75) | C-R8 |
| RT-16 | `search` tek-boşluk | mood/genre yok | workflow L62 sorgusu | `$query === ' '` — **RF-3** (sorgu kalitesi kardeşe bağlı) | L62 |

### EK-3.2 · Kapsam Dışı / Bloklular

- KVKK/edep-tuşu/profil-persist testleri → `⚠️` (dayanak `../personalization-web-arastirma.md` EK-3).
- `R-12` senaryosu → kardeş `recommendation-web-arastirma.md` (bu EK'te tekrar okunmadı).
- Çalışan test iddiası: **0** — hiçbir test bu EK'te çalıştırılmadı (uydurulan sayı yok).
- Yazım: hiçbir RT `*.php`'ye yazılmaz (beyazliste gate · §16.4).

### EK-3.3 · EK-3 Kapı

- Yeni URL: **0** · yeni iddia: yok (tümü EK-1/EK-2 satır atıfları) · kod yazımı: **0**.
- Ölçüm: **ölçülmedi** (tek akış — EK-4 §7).

---

## EK-4 · Ölçüm Zinciri ve Nihai Kapı

### § Ölçüm (dürüst — `read(offset=9999, limit=1)`; shell reddedildi)

| Adım | Zaman | Sonuç |
|---|---|---|
| Başlangıç (§0) | 2026-10-15 | **148** satır |
| EK-1..EK-3 (tek akışta yazıldı) | — | **ölçülmedi** (ara ölçüm yapılmadı) |
| Nihai (bu satır öncesi son read) | 2026-10-15 | **558** satır |

### § Düzeltme Özeti

| Kod | Konu | Sonuç |
|---|---|---|
| R-D1 | `getRecommendations` imzası | `(array $context, int $limit=20)` ✓ L49 |
| R-D2 | "53–55 boş tercih" | **kodda yok** ✓ (pipeline sonu L76) |
| R-D3 | adım satırları | L51·L52-56·L59·L62·L65·L68·L71·L74·L76 ✓ |
| R-D4 | workflow satırları | L75 (öneri) · L70 encode · L66-72 prompt · L82-90 çıkış ✓ |
| R-D5 | `@return` alan sayısı | **5** (genre yok) ✓ L26 |
| R-D6 | `dispatchTask` aralığı | L71-97 ✓ |
| R-D7 | önbellek/composer | `psr/cache` L16 var · öneri-özel 0 ✓ |
| R-D8 | `extractFeatures` | L315-327 ✓ (6 sabit) |
| R-D9 | `json_decode` iddiası | `json_encode` L70 ✓ |
| R-D10 | çeşitlilik şartnamesi | EK-2.2 ✓ (ilk kayıt kardeş EK-7 [Y5]) |

### § Nihai Kapı

- **≥500 satır:** **558 ≥ 500** ✅ · frontmatter korundu ✓ · append-only (silme 0) ✓ · `index.md` dokunulmadı ✓ ·
  beyazliste dışı yazım: **0** · kod (`*.php/js/css/sql`) yazımı: **0** · git commit: **yok** ·
  yeni URL: **0** · uydurma ölçü/iddea: **0** (yanlış iddialar EK-1'de düzeltme kaydıyla giderildi).
- Kardeş tekrarı: optimizeEQ D1/D2 ✗ yok · inference 3 ✗ yok · A-MM-1 ✗ yok · çeşitlilik [Y5] tekrar
  edilmedi (EK-2.2'ye bağlandı) · `getRecommendations` taze imzayla yazıldı ✓.
