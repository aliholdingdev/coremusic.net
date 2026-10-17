---
title: "recommendation — Kapsam"
type: architecture-scope
node_id: K006-yapay-zeka/recommendation
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# recommendation — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`ai-master.md` §1, v2 §2.6 rol cümlesini birebir taşır: **"Şarkı öneren, sesi analiz eden yapay zeka parçaları."**
Bu çocuğun adı cümlenin **ilk yarısını** ("şarkı öneren") karşılar; `ai-master.md` §1 sorumluluk tablosu
(satır 37–42) bunu **"Öneri üretmek"** sorumluluğuna bağlar — tablo master'da *tasarım yorumu* olarak işaretlidir.

Basitçe: kullanıcıya ne çalacağını puanlayıp sıralayan taraf. Repo'da karşılığı **vardır**
(`AIEngine::getRecommendations` — hybrid collaborative + content-based), ama bu dalın **resmi kapsam tanımı
v2'de yoktur**; ayrıntı §3'teki R sorularındadır.

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Aday üretimi + hybrid skorlama (collaborative + content-based) | Etkileşim/katalog verisinin saklanması | K007-data (bagimliliklar §2/1) |
| 2 | Çeşitlilik (diversity) filtresi ve minimum skor eşiği | "beğeni/öneri" iş kuralının tanımı | K008-domain (bagimliliklar §2/2) |
| 3 | Öneri sonucunun tek ML çağrı noktası olarak verilmesi | Öneri kartının UI'da çizimi | K011-application (ai-master §2.1) |
| 4 | Öneri modelinin K006'da çalıştırılması | Ajan çalışma zamanı / prompt yönetimi | K015-AI (ai-master §2.1) |
| 5 | Öneri kalitesinin ölçülmesi (evaluation ile birlikte) | Genel uygulama testi | K019-testing (bagimliliklar §3/3) |
| 6 | Öneri akışının arka plan süreciyle ilişkisi (sözleşme) | Sürecin kendisi | K012-services (ai-master §2.1) |

**Sınır kuralı (v2 §2.6 · registry §6):** bu dal yalnız **K006 = ML ürün tarafını** kapsar; K015-AI ajan altyapısına
öneri yeteneği **taşınamaz**, ajanlar K006'yı **çağırır**.

**Yasak:** tablo **kademe-2 taslak sınır önerisidir**. `ai-master.md` §7/2 ve bagimliliklar §5/2'ye göre
öneri sahipliği üç düğüm arasında **açık** (K006 · K008 · K020-recomenataion) — sahiplik ADR'si gelene kadar
bu dal "önerinin sahibi" olduğunu **iddia edemez**.

### Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | K006-recommendation ↔ K008-recommendation ↔ K020-recomenataion | ⚠️ **SINIR AÇIK** | ai-master §7/2 · bagimliliklar §5/2 |
| A2 | Collaborative filtering gerçekten uygulanacak mı (şu an boş)? | ⚠️ taslak — repo kodu `[]` döndürür | `AIEngine.php` satır 209–218 |
| A3 | Skor ağırlıkları (0.6/0.4) sabit mi, öğrenilen mi? | **UNKNOWN** — değer hardcoded | `AIEngine.php` satır 65 |
| A4 | Öneri verisi K007'de mi, önbellekte mi saklanır? | ⚠️ VERIFY REQUIRED | bagimliliklar §4/2 |

## §3 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk adı v2 §2.6 satır 261'de birebir geçer | `.ai/architecture/K006-yapay-zeka/recommendation/index.md` (evidence) | ✅ dosya okundu |
| 2 | Rol tanımı yok → `⚠️ VERIFICATION REQUIRED` | `recommendation/index.md` (open_questions satır 13) | ✅ dosya okundu |
| 3 | Öneri motoru repo'da mevcut: hybrid 6 adım (collab → content → hybrid → diversity → filtre → sıralama) | `shared/src/AI/AIEngine.php` satır 49–77 | ✅ dosya okundu |
| 4 | Hybrid ağırlıkları 0.6 collaborative / 0.4 content-based (hardcoded) | `shared/src/AI/AIEngine.php` satır 65 | ✅ dosya okundu |
| 5 | Collaborative skorlar henüz boş `[]` ("matrix factorization (ALS) kullanılacak" yorumu) | `shared/src/AI/AIEngine.php` satır 209–218 | ✅ dosya okundu |
| 6 | Sabitler: MAX_RECOMMENDATIONS=20 · MIN_SCORE=0.7 · DIVERSITY_TARGET=0.30 | `shared/src/AI/AIEngine.php` satır 30–32 | ✅ dosya okundu |
| 7 | Workflow: session tercihi → dispatch → knowledge search → engine → feedback | `shared/src/AI/AIWorkflow.php` satır 49–91 | ✅ dosya okundu |
| 8 | Öneri sahipliği üç düğüm arasında açık | `ai-master.md` §7/2 | ⚠️ dosya okundu |

### Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | Öneri domain'inin sahibi kim (K006 · K008 · K020)? | ⚠️ SINIR AÇIK — VERIFY REQUIRED |
| R2 | Collaborative filtering / ALS ne zaman uygulanacak? | ⚠️ repo'da boş — IMPLEMENTED değil |
| R3 | Skor ağırlığı 0.6/0.4 kalıcı mı? | ⚠️ **UNKNOWN** |
| R4 | Hangi model formatı/runtime? | ⚠️ ai-master §4 — **UNKNOWN** |

**Kapanış:** status: draft kalır. R1 kapanmadan bu dosyada "öneri sahibi bu düğümüdür" iddiası yazılmaz
(ai-master §7/2 · yapay-zeka-dogrulama §3/2).

## §4 İlişkili dosyalar

- [[recommendation-dogrulama]]
- [[recommendation-ornekler]]
- [[../index|K006-yapay-zeka index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12

---

## EK — 2026-10-15 genişletme eki (append-only · ≥500 kapısı)

> Bu ek dosyanın SONUNA eklenmiştir (append-only). Gövde §1–§4 silinmemiş/değiştirilmemiştir:
> eski içerik, eklenen metnin birebir önekidir. Tüm satır referansları 2026-10-15 taze
> okumadan alınmıştır.

### EK §0 — Usul

| Kural | Uygulama (bu ekte) |
|---|---|
| Append-only | Ekleme dosya sonunda; gövde satırlarına dokunulmadı |
| Ölçüm | `read(offset=9999, limit=1)` hata sayımı (dosya satır sayısı hatada döner) |
| Boş dolgu yasak | TBD · `// ...` · uydurma satır: 0 |
| Zero-hallucination | Her iddia `Dosya: <yol> [Satır: n]` ile kanıtlanır; kanıtsız iddia yazılmaz |
| Kod iddiası | Repo'da `*.cpp/*.h` = 0 (2026-10-15 görev ölçümü) → "repo C++ kodu var" iddiası YOK |
| git commit | YOK — commit orkestratöre aittir |
| Beyazliste | Yalnız bu dosya yazıldı · `*.php` kaynak kod **okundu, yazılmadı** · `index.md` · kardeşler · `.ai/projects` · `.ai/electronic` **yalnız okundu** |

**Önceki sayım (ek öncesi):** `read(offset=9999)` → **85 satır** (T1).

### EK §1 — Taze okuma koşu kayıtları (2026-10-15)

| # | İşlem | Hedef | Sonuç |
|---|---|---|---|
| T1 | offset=9999 sayım | `recommendation-kapsam.md` | **85 satır** (ek öncesi) |
| T2 | tam okuma | `recommendation-kapsam.md` | L1–L85 · frontmatter 11 alan (L1–L11) · gövde §1–§4 · Authority L85 |
| T3 | tam okuma | `shared/src/AI/AIEngine.php` | **382 satır** · sabitler L30–L32 · `getRecommendations` L49–L77 · collab L209–L218 · content L223–L235 · combine L243–L259 · diversity L264–L283 |
| T4 | tam okuma | `shared/src/AI/AIWorkflow.php` | **271 satır** · `recommendationWorkflow` L49–L91 |
| T5 | tam okuma | `shared/src/Contracts/AI/AIEngineInterface.php` | **61 satır** · `getRecommendations` sözleşmesi L21–L28 |
| T6 | glob | `.ai/architecture/K006-yapay-zeka/**/*.md` | 76 dosya · recommendation dalı 7 dosya (index + 6 rol) |

### EK §2 — Verbatim bloklar (birinci el okuma, 2026-10-15)

#### B1 — `AIEngine::getRecommendations` (`shared/src/AI/AIEngine.php` L46–L77, T3)

```php
46:     /**
47:      * {@inheritdoc}
48:      */
49:     public function getRecommendations(array $context, int $limit = 20): array
50:     {
51:         $limit = min($limit, self::MAX_RECOMMENDATIONS);
52:         $userId = $context['user_id'] ?? null;
53:         $sessionId = $context['session_id'] ?? null;
54:         $mood = $context['mood'] ?? null;
55:         $genre = $context['genre'] ?? null;
56:         $history = $context['history'] ?? [];
57: 
58:         // 1. Collaborative filtering — benzer kullanıcı tercihleri
59:         $collaborativeScores = $this->getCollaborativeScores($userId, $history);
60: 
61:         // 2. Content-based — şarkı özellikleri
62:         $contentScores = $this->getContentBasedScores($context);
63: 
64:         // 3. Hybrid skorlama (ağırlıklı ortalama)
65:         $hybridScores = $this->combineScores($collaborativeScores, $contentScores, 0.6, 0.4);
66: 
67:         // 4. Çeşitlilik filtresi — %30 farklı tür garantisi
68:         $diversified = $this->applyDiversityFilter($hybridScores, $genre);
69: 
70:         // 5. Minimum skor filtresi
71:         $filtered = array_filter($diversified, fn(array $item) => $item['score'] >= self::MIN_SCORE);
72: 
73:         // 6. Sıralama ve limit
74:         usort($filtered, fn(array $a, array $b) => $b['score'] <=> $a['score']);
75: 
76:         return array_slice(array_values($filtered), 0, $limit);
77:     }
```

#### B2 — kaynak skorlar boş (`AIEngine.php` L206–L235, T3)

```php
209:     private function getCollaborativeScores(?string $userId, array $history): array
210:     {
211:         if ($userId === null) {
212:             return [];
213:         }
214: 
215:         // DB'den benzer kullanıcıları bul
216:         // Gerçek implementasyonda matrix factorization (ALS) kullanılacak
217:         return [];
218:     }
223:     private function getContentBasedScores(array $context): array
224:     {
225:         $targetFeatures = [
226:             'bpm'          => $context['bpm'] ?? null,
227:             'energy'       => $context['energy'] ?? null,
228:             'danceability' => $context['danceability'] ?? null,
229:             'genre'        => $context['genre'] ?? null,
230:         ];
231: 
232:         // DB'den aday şarkıları çek ve özelliklerini karşılaştır
233:         // cosine similarity kullanarak skorla
234:         return [];
235:     }
```

#### B3 — birleştirme + çeşitlilik (`AIEngine.php` L237–L283, T3)

```php
243:     private function combineScores(array $collaborative, array $content, float $collabWeight, float $contentWeight): array
244:     {
245:         $allKeys = array_unique(array_merge(array_keys($collaborative), array_keys($content)));
246:         $combined = [];
247: 
248:         foreach ($allKeys as $key) {
249:             $collabScore = $collaborative[$key]['score'] ?? 0;
250:             $contentScore = $content[$key]['score'] ?? 0;
251: 
252:             $combined[$key] = [
253:                 'id'    => $key,
254:                 'score' => ($collabScore * $collabWeight) + ($contentScore * $contentWeight),
255:             ];
256:         }
257: 
258:         return $combined;
259:     }
264:     private function applyDiversityFilter(array $scores, ?string $preferredGenre): array
265:     {
266:         $diversified = [];
267:         $genreCounts = [];
268: 
269:         foreach ($scores as $id => $item) {
270:             $genre = $item['genre'] ?? 'unknown';
271:             $genreCount = $genreCounts[$genre] ?? 0;
272: 
273:             // Tercih edilen türden çok fazla olmasın
274:             if ($genre === $preferredGenre && $genreCount > self::DIVERSITY_TARGET * count($scores)) {
275:                 $item['score'] *= 0.8;
276:             }
277: 
278:             $genreCounts[$genre] = $genreCount + 1;
279:             $diversified[$id] = $item;
280:         }
281: 
282:         return $diversified;
283:     }
```

#### B4 — sabitler (`AIEngine.php` L30–L32, T3)

```php
30:     private const MAX_RECOMMENDATIONS = 20;
31:     private const MIN_SCORE = 0.7;
32:     private const DIVERSITY_TARGET = 0.30;
```

#### B5 — `AIWorkflow::recommendationWorkflow` (`shared/src/AI/AIWorkflow.php` L43–L91, T4)

```php
49:     public function recommendationWorkflow(array $request): array
50:     {
51:         $startTime = microtime(true);
52:         $sessionId = $request['session_id'] ?? 'anonymous';
53: 
54:         // 1. Context collect — session'dan tercihleri çek
55:         $context = $this->memory->getSession($sessionId, 'preferences') ?? [];
56:         $context = array_merge($context, $request);
57: 
58:         // 2. Task dispatch
59:         $taskId = $this->orchestrator->dispatchTask('recommendation', $context, 'medium');
60: 
61:         // 3. Knowledge query — benzer önerileri ara
62:         $query = ($context['mood'] ?? '') . ' ' . ($context['genre'] ?? '');
63:         $knowledgeResults = $this->knowledge->search($query, 5);
64: 
65:         // 4. Prompt generate
66:         $promptResult = $this->promptEngine->generatePrompt('recommendation', [
67:             'mood'   => $context['mood'] ?? 'neutral',
68:             'genre'  => $context['genre'] ?? 'any',
69:             'energy' => $context['energy'] ?? 0.5,
70:             'history' => json_encode($context['history'] ?? [], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
71:             'limit'  => $context['limit'] ?? 10,
72:         ], ['knowledge' => $knowledgeResults]);
73: 
74:         // 5. Recommendation engine
75:         $recommendations = $this->engine->getRecommendations($context, $context['limit'] ?? 10);
76: 
77:         // 6. Feedback — sonucu kaydet
78:         $this->memory->setSession($sessionId, 'last_recommendations', $recommendations, 3600);
79: 
80:         $executionTime = microtime(true) - $startTime;
81: 
82:         return [
83:             'recommendations' => $recommendations,
84:             'metadata'        => [
85:                 'task_id'        => $taskId,
86:                 'prompt_tokens'  => $promptResult['tokenCount'],
87:                 'knowledge_hits' => count($knowledgeResults),
88:             ],
89:             'execution_time'  => round($executionTime, 4),
90:         ];
91:     }
```

#### B6 — sözleşme (`AIEngineInterface.php` L21–L28, T5)

```php
21:     /**
22:      * Müzik önerisi üretir.
23:      *
24:      * @param array<string, mixed> $context Kullanıcı bağlamı (tercihler, geçmiş, mood)
25:      * @param int                  $limit   Maksimum öneri sayısı
26:      * @return array<int, array{id: string, title: string, artist: string, score: float, reason: string}>
27:      */
28:     public function getRecommendations(array $context, int $limit = 20): array;
```

### EK §3 — Kod analizi bulguları (her biri verbatim kanıtlı)

#### §3.1 Boru hattı 6 adım — adım durum matrisi

| # | Adım | Konum | Durum | Kanıt |
|---|---|---|---|---|
| 1 | Collaborative skor | `AIEngine.php` L209–L218 | **boş `[]` döner** (her iki yolda da) | B2 L212 · L217 |
| 2 | Content-based skor | `AIEngine.php` L223–L235 | **boş `[]` döner** | B2 L234 |
| 3 | Hybrid birleştirme | `AIEngine.php` L243–L259 | uygulanır ama girdi boşsa sonuç boş | B3 L245–L256 |
| 4 | Çeşitlilik filtresi | `AIEngine.php` L264–L283 | uygulanır ama `genre` anahtarı hiç üretilmez (bkz §3.3) | B3 L270 |
| 5 | MIN_SCORE filtresi | `AIEngine.php` L71 | 0.7 eşiği | B1 L71 · B4 |
| 6 | Sıralama + limit | `AIEngine.php` L74–L76 | azalan sıralama (`$b <=> $a`) | B1 L74–L76 |

#### §3.2 Bulgu R-K1 — motor şu an **her zaman boş liste** döndürür (IMPLEMENTED değil, PLANNED davranış)

Mantıksal türetme (tam okuma kanıtlı):

| # | Adım | Değer | Kanıt |
|---|---|---|---|
| a | `$collaborativeScores` | `[]` — `$userId` null ise L212, doluysa L217; **iki yol da `[]`** | B2 |
| b | `$contentScores` | `[]` — tek dönüş yolu L234 | B2 |
| c | `combineScores([], [], 0.6, 0.4)` | `array_merge([], [])` → `[]` → döngü 0 tur → `[]` | B3 L245–L247 |
| d | `applyDiversityFilter([], …)` | döngü 0 tur → `[]` | B3 L269 |
| e | `array_filter([], …)` | `[]` | B1 L71 |
| f | dönüş | **`[]`** | B1 L76 |

**Sonuç:** `getRecommendations()` bugünkü hâliyle her çağrıda **boş dizi** döndürür — kullanıcıya
hiçbir zaman öneri görünmez (id 1–f). Bu, "öneri motoru repo'da mevcut" (gövde §3/3) ifadesinin
**davranışsal** okumasıdır: boru hattı iskeleti vardır, skorlama **üretilmez**.
Gövde §3/3 ifadesi silinmez; bu ek ona davranış katmanını ekler (append-only).

#### §3.3 Bulgu R-K2 — `genre` anahtarı hiçbir adımda üretilmez → çeşitlilik kuralı asla tetiklenmez

| Zincir | Konum | Gözlem |
|---|---|---|
| `combineScores` yalnız `id` + `score` yazar | B3 L252–L255 | `genre` alanı **yok** |
| `applyDiversityFilter` `$item['genre'] ?? 'unknown'` okur | B3 L270 | her eleman için `'unknown'` |
| Karşılaştırma `$genre === $preferredGenre` | B3 L274 | `'unknown' === 'jazz'` → **false** |

**Sonuç:** `DIVERSITY_TARGET = 0.30` sabiti (B4) ve `*= 0.8` ceza kuralı (B3 L275) kaynak
eksikliği nedeniyle **dead code**dur; yorum satırındaki "%30 farklı tür garantisi" (B1 L67)
üretilen davranışla **eşleşmez**. Düzeltme yasak (append-only) → kapı R-P3.

#### §3.4 Bulgu R-K3 — sözleşme 5 alan ↔ implementasyon 2 alan

| Alan | Sözleşme (B6 L26) | Implementasyon (B3 L252–L255) |
|---|---|---|
| `id` | var | var (L253) |
| `score` | var | var (L254) |
| `title` | var | **yok** |
| `artist` | var | **yok** |
| `reason` | var | **yok** |

Sözleşme `array{id, title, artist, score, reason}` taahhüt eder; birleştirme katmanı yalnız
`{id, score}` üretir. `title/artist/reason` **hiçbir yerde** eklenmez (tam okuma 382 satırda
alan yazımı yalnız L252–L255). Kart gösterimi (K011 sınırı) bu üç alan olmadan çizilemez →
K006↔K011 arayüz riski (kapı R-P4). testing-kapsam §5.1/1 aynı yapıyı "saf fonksiyon" olarak
sınıflandırmıştı; bu ek o sınıflandırmayı **şema uyumsuzluğu** ile tamamlar.

#### §3.5 Bulgu R-K4 — workflow'ta prompt üretilir ama akışa **girmez**

| # | Adım | Konum | Gözlem |
|---|---|---|---|
| 1 | `generatePrompt('recommendation', …)` | T4 L66–L72 | prompt **üretilir** |
| 2 | `$recommendations = $this->engine->getRecommendations(…)` | T4 L75 | motora **promptResult geçirilmez**; yalnız `$context` gider |
| 3 | Yanıtta yalnız `'prompt_tokens' => $promptResult['tokenCount']` | T4 L86 | prompt yalnız **token sayacı** olarak geri döner |

**Sonuç:** LLM/şablon katmanı mevcut (PromptEngine) ama öneri **skoru bu katmandan gelmez**;
workflow'un LLM adımı ölçümlendirme (telemetri) amaçlıdır. R-2 (ai-master L523 Faz-2) bu bulguyla
aynı yere işaret eder: öneri akışının LLM'e gerçek bağımlılığı **kurulmamıştır**.

#### §3.6 Bulgu R-K5 — `sessionId` workflow'ta okunur ama engine'e geçmez (çapraz dal)

| # | Adım | Konum | Gözlem |
|---|---|---|---|
| 1 | `$sessionId = $request['session_id'] ?? 'anonymous'` | T4 L52 | anonim varsayılan |
| 2 | oturumdan tercih çekilir | T4 L55 | `preferences` anahtarı |
| 3 | `$context = array_merge($context, $request)` | T4 L56 | `session_id` `$context`'e **dahildir** (request'te varsa) |
| 4 | engine `$sessionId = $context['session_id'] ?? null` okur | T3 L53 | okunur ama L59/L62'de **kullanılmaz** |

`$sessionId` B3/L53'te okunur; collab (L59 yalnız `$userId`) ve content (L62 yalnız `$context`
feature anahtarları) çağrısında **parametre olarak geçmez** → oturum bazlı kişiselleştirme
(private dal R-2/R-3) için hazır bir kanal **vardır ama boştur**. Çapraz dal: personalization
(müdahale yok — yalnız rapor).

#### §3.7 Bulgu R-K6 — MIN_SCORE=0.7 sabiti hardcoded ve eşik etkisi ölçülemez

| Sabit | Değer | Konum | Etki |
|---|---|---|---|
| `MAX_RECOMMENDATIONS` | 20 | B4 L30 · uygulama L51 | `min($limit, 20)` |
| `MIN_SCORE` | 0.7 | B4 L31 · uygulama L71 | 0.7 altı elenir |
| `DIVERSITY_TARGET` | 0.30 | B4 L32 · uygulama L274 | dead code (§3.3) |
| hybrid ağırlık | 0.6 / 0.4 | L65 · L243–L244 | hardcoded çağrı argümanı |

Sabitler `private const`; `ConfigManager` constructor'da alınır (T3 L34–L36) ama **hiçbir
sabit config'ten okunmaz** → "0.6/0.4 sabit mi öğrenilen mi" (gövde §2/A3) sorusunun kod
karşılığı: **hardcoded, config'e bağlı değil**. Config erişimi yalnız `getConfig()` (L41–L44)
ile mümkündür; öneri akışında kullanılmaz.

#### §3.8 Bulgu R-K7 — `history` iki yerde iki farklı biçimde işlenir

| # | Yer | Biçim | Kanıt |
|---|---|---|---|
| 1 | workflow prompt | `json_encode($context['history'] ?? [])` | T4 L70 |
| 2 | engine collab | `getCollaborativeScores($userId, $history)` → fonksiyonda **dokunulmaz** | T3 L59 · L209–L218 |

`$history` collab fonksiyonuna girer ama L209–L218'de hiçbir okuma/isempty yoktur (yalnız
`$userId === null` kontrolü) → geçmiş verisi **hiç değerlendirilmez**. Kapsam §2/1'deki
"aday üretimi + hybrid skorlama" maddesi için veri akışı **kurulmamıştır**.

#### §3.9 Workflow metadata sözleşmesi (T4 L82–L90)

| Alan | Kaynak | Değer |
|---|---|---|
| `recommendations` | engine dönüşü | her zaman `[]` (§3.2) |
| `metadata.task_id` | `dispatchTask` | string task-id |
| `metadata.prompt_tokens` | `$promptResult['tokenCount']` | PromptEngine token sayımı |
| `metadata.knowledge_hits` | `count($knowledgeResults)` | `search($query, 5)` → 0–5 |
| `execution_time` | `microtime` farkı | 4 haneye yuvarlanmış |

`knowledge` araması yalnız `mood + genre` dizesiyle yapılır (T4 L62–L63); bu sonuç da
**öneri skorlamasına girmez** (yalnız `knowledge_hits` sayılır) → R-K4 ile aynı aile:
zenginleştirme adımları telemetridir.

### EK §4 — Fark tablosu (C-row'lar)

| # | Gövde/kayıt iddiası | Taze gerçek (2026-10-15) | Karar |
|---|---|---|---|
| R-C1 | §3/3: "Öneri motoru repo'da mevcut: hybrid 6 adım (satır 49–77)" | B1 birebir L49–L77 | ✅ **teyit** (iskelet) |
| R-C2 | §3/4: "Hybrid ağırlıkları 0.6/0.4 hardcoded (satır 65)" | B1 L65 birebir | ✅ teyit |
| R-C3 | §3/5: "Collaborative skorlar boş `[]` (satır 209–218)" | B2 birebir | ✅ teyit + content-based da boş (L234) → §3.2 |
| R-C4 | §3/6: "MAX_RECOMMENDATIONS=20 · MIN_SCORE=0.7 · DIVERSITY_TARGET=0.30 (satır 30–32)" | B4 birebir | ✅ teyit |
| R-C5 | §3/7: "Workflow 6 adım (satır 49–91)" | T4 birebir L49–L91 | ✅ teyit |
| R-C6 | §1 "satır 37–42 sorumluluk tablosu" | (bkz. music-analysis EK MA-C2 — aynı master referans hatası ailesi) | 🔄 P-1 ile aynı kapı ailesi |
| R-C7 | §2/A2 "repo kodu `[]` döndürür (209–218)" | yalnız collab için okunmuştu; content-based L234 de `[]` | ✅ **genişledi** (yeni bilgi, eski satır silinmedi) |
| R-C8 | §2/A3 "0.6/0.4 hardcoded — UNKNOWN kalıcı mı?" | hardcoded + config'ten okunmaz (§3.7) | 🔄 P-3 bekler; kod düzeyi kesinleşti |
| R-C9 | §3/8 "öneri sahipliği üç düğüm arasında açık" | kod yalnız K006 tarafını gösterir; sahiplik kararı kanıtlanamaz | ✅ korunur — sahiplik ADR dışı |
| R-C10 | — (gövdede yok) | R-K1: motor her zaman `[]` döner | **yeni bulgu** → kapı R-P2 |
| R-C11 | — (gövdede yok) | R-K3: sözleşme 5 alan ↔ impl 2 alan | **yeni bulgu** → kapı R-P4 |
| R-C12 | — (gövdede yok) | R-K4: prompt akışa girmez | **yeni bulgu** → kapı R-P5 |

### EK §5 — Kapılar (R-P-1 … R-P-9)

| # | Kapı | Sahip | Durum |
|---|---|---|---|
| R-P-1 | Master "satır 37–42" referansı (R-C6 · MA-C2 ailesi) | vault-steward | 🔄 kayıt — gövde revizyonu ADR gerektirir |
| R-P-2 | R-K1: boş-liste davranışı — Faz-2'de collab/content gerçekleşmesi gelene kadar "motor mevcut" ifadesi davranışsal olarak **eksiktir** | architect | ⏳ ai-master L523 Faz-2 (R-02) |
| R-P-3 | R-K2: `genre` anahtarı üretilmediği için çeşitlilik kuralı dead code | architect + backend | ⏳ skor şemasına `genre` eklenmesi kararı |
| R-P-4 | R-K3: sözleşme 5 alan ↔ impl 2 alan (`title/artist/reason` yok) | qa-engineer | ⏳ şema uyumu kararı · testing-kapsam P-9 ile aynı kapı ailesi |
| R-P-5 | R-K4: workflow prompt'u akışa sokmaz (yalnız token telemetrisi) | architect | ⏳ LLM entegrasyon kararı (ai-master §18.1 Faz-3/4) |
| R-P-6 | R-K6: 0.6/0.4 · MIN_SCORE 0.7 hardcoded, ConfigManager'a bağlı değil | architect | ⏳ config'e taşınma kararı |
| R-P-7 | R1: öneri domain sahipliği (K006 · K008 · K020) | architect | ⏳ sahiplik ADR'si (gövde §2/A1 — dokunulmadı) |
| R-P-8 | R2: collab/ALS ne zaman uygulanacak? | architect | ⏳ ai-master L523 Faz-2 |
| R-P-9 | R3/R4: ağırlık kalıcılığı + model/runtime seçimi | architect | ⏳ ai-master §11.1 (seçim YOK) |

### EK §6 — Kardeş dal kesişimleri (bu ek bulguları ışığında)

| Kardeş dal | Kesişim | Bu ekten çıkan işaretleme |
|---|---|---|
| personalization | R-K5: `session_id` okunur ama skorlamaya girmez | personalization R2/R3 ile aynı kanal — rapor, müdahale yok |
| testing | R-K3: sözleşme/impl alan farkı → şema testi gereği | testing-kapsam §5.1/1 + P-9 ile aynı kapı |
| evaluation | motor `[]` döndürdükçe NDCG/ölçüm **anlamsız** — ground-truth ile skorlanacak çıktı yok | evaluation T4 (ai-master L305) ile aynı blokaj |
| music-analysis | content-based feature'lar (bpm/energy/danceability — B2 L226–L228) `analyzeAudio` alanlarıyla aynı adları taşır | feature sözleşmesi uyumu **tesadüfi** — R-2 Faz-2'de sabitlenmeli |
| inference | workflow `getRecommendations` çağrısı canlı uç | inference-dogrulama (≥500 ✅) |
| local-inference | offline uçta öneri skorlaması | local-inference-dogrulama (≥501 ✅) |
| model-management | öneri modeli kaydı/sürümü | S3 (ai-master L184) |
| audio-intelligence | knowledge araması (T4 L63) ortak zemin | — |
| kök anayasa | `yapay-zeka-dogrulama.md` §3/2 sahiplik iddiası | bu ek R1'i kapatmaz |

### EK §7 — Dal dosya envanteri (recommendation · 2026-10-15)

| Dosya | Görev başı kayıt | Bu tur durum | Not |
|---|---|---|---|
| `index.md` | 48 (tahmini — ölçülmedi) | dokunulmadı | beyazliste dışı |
| `recommendation-kapsam.md` | 85 | **ölçüm EK §9** | bu ek |
| `recommendation-dogrulama.md` | ≥500 ✅ (önceki tur) | dokunulmadı | — |
| `recommendation-anayasa.md` | ≥500 ✅ (önceki tur) | dokunulmadı | — |
| `recommendation-bilesenler.md` | 148 | bu tur C grubu | — |
| `recommendation-ornekler.md` | 381 (tahmini) | dokunulmadı | beyazliste dışı |
| `recommendation-web-arastirma.md` | 138 | bu tur B grubu | — |

> Not: tahmini satır sayıları ölçülmemiştir; kesin sayı yalnız bu dosya için EK §9'dadır.

### EK §8 — Usul kararları

| # | Karar | Gerekçe |
|---|---|---|
| U-1 | PHP kaynak kod dosyaları bu turda **yalnız okundu** | beyazliste: yalnız `*.md` mimari vault |
| U-2 | Gövde §1–§4'teki "satır 37–42" gibi hatalı referanslar **düzeltmedi** | append-only; düzeltme ADR + vault-steward işidir (P-1) |
| U-3 | R-K1 (her zaman boş liste) iddiası **mantıksal türetme** olarak etiketlendi | doğrudan gözlem değil, kod akışından çıkarım; her adımın kanıtı §3.2 tablosunda |
| U-4 | Yeni URL eklenmedi | bu turda erişilebilirlik doğrulaması yapılmadı → uydurma URL yasak |
| U-5 | `.ai/projects/NevaEngine` ve `.ai/electronic` bu dal için **okunmadı** | konu PHP öneri motoru; NevaEngine kanıtı audio-intelligence/music-analysis bilesenler eklerinde kullanılacak |

### EK §9 — NIHAÎ ÖLÇÜM

| Yöntem | Sonuç | Tarih |
|---|---|---|
| `read(offset=9999)` hata sayımı | **501 satır** | 2026-10-15 |
| Eksik (<500) mi? | **HAYIR — 501 ≥ 500 ✅** | 2026-10-15 |

**Ek Authority:**
EK §0–§9 bu oturumda (2026-10-15) taze okumalarla (T1–T6) üretildi; gövde §1–§4 önceden
mekvcuttu ve silinmedi. git commit YOK · beyazliste dışı dosyaya yazım YOK · `index.md` /
`ai-master.md` / kardeş dosyalara DOKUNULMADI.
