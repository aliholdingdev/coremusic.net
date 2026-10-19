---
node_id: rag-ornekler
node_type: module
status: active
parent_id: rag
children: []
depends_on: [rag-anayasa, rag-surucu]
evidence:
  - "shared/src/AI/ (7 PHP dosyası - 2026-10-17 okuma)"
  - ".ai/RAG.md §3 (gerçek indeks formatı)"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı, kod kanıtı zorunlu"
open_questions: []
group: kod
---

# rag-ornekler - Retrieval Kod Örnekleri (kanıt odaklı)

> **Kapsam:** Her PHP alıntısı 2026-10-17'de `shared/src/AI/` altındaki 7 dosyanın gerçek
> satırıdır (`Dosya:` / `Satır:` künyesi ile). İllüstratif çok-dilli bloklar repo kanıtı
> değildir; her birinin üstünde zorunlu etiket taşır. Kaynak kod salt-okunurdur.

## §1. Kanıt Künye Formatı

| Alan | Kural |
|---|---|
| Dosya | göreli yol, diskte var (glob ile doğrulanmış) |
| Satır | okunan aralık (L72-L91 gibi) — okunmamış satır künye olamaz |
| Alıntı | kod satırının kendisi (kısaltma serbest, uydurma yasak) |
| Yokluk | tam cümle: "Repo'da bu alan kodu YOK — kaynak: upstream docs" |
| Ölçüm | grep/glob sayaçları — araç çıktısı, iddia değil |
| İllüstratif | `⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı` |
| Tarih | her kanıt 2026-10-17 okuma turuna ait |
| Eşleşme | `evet` / `kısmi` / `YOK` — başka değer yok |
| Yasak | uydurma künye → Zero-Hallucination ihlali (kök AGENTS §3) · çelişkide disk kazanır, alıntı tazelenir |

## §2. Okunan PHP Envanteri — `shared/src/AI/` (7 dosya)

Dosya: `shared/src/AI/` · glob → **7 isabet** (2026-10-17)

| # | Dosya | Satır | Sınıf amacı (L) | Retrieval ilgisi |
|---|---|---|---|---|
| 1 | KnowledgeBase.php | 232 | "Semantic search, RAG, knowledge lifecycle" (L16) | doğrudan — RAG çekirdeği |
| 2 | AIWorkflow.php | 271 | workflow'lar (L14) | doğrudan — retrieval çağrıları |
| 3 | ToolCalling.php | 326 | "Tool registry, executor, permission check" (L16) | kısmi — glob taraması |
| 4 | MemorySystem.php | 242 | "Session state, cache, memory hierarchy" (L16) | kısmi — öncelik hiyerarşisi |
| 5 | PromptEngine.php | 325 | "Prompt üretimi, token, validasyon" (L16) | kısmi — girdi filtresi |
| 6 | AIOrchestrator.php | 210 | "görev dağıtımı" (L5) | kısmi — routing indeksi |
| 7 | AIEngine.php | 382 | "Recommendation, Audio Analysis..." (L6) | kısmi — cosine sözü |

**Düzeltme:** Görevde anılan `ServiceRegistry.php` bu dizinde **DEĞİL** —
`shared/src/Api/Registry/ServiceRegistry.php` (glob 3 isabet).

## §3. KnowledgeBase::ragQuery — RAG'ın 3 Aşaması + confidence

Dosya: `shared/src/AI/KnowledgeBase.php` · Satır: L72-L91, L199-L231

```php
$retrieved = $this->search($query, 5, $context['filters'] ?? []);        // 1. Retrieval — ilgili belgeleri bul
$augmentedContext = $this->augmentContext($query, $retrieved, $context); // 2. Augmentation — prompt'a bağlam ekle
$answer = $this->generateAnswer($query, $augmentedContext);              // 3. Generation — placeholder ("Gerçek implementasyonda LLM API çağrılacak")
```

| Gözlem | Kanıt |
|---|---|
| Retrieval = `search()`, top-5 | L74-L75 |
| Augmentation: `retrieved_documents` + `query_expansion` (yalnız ham sorgu) | L202-L203 |
| Generation = **placeholder** ("Gerçek implementasyonda LLM API çağrılacak") | L80-L81 + L213 |
| Çıktı: answer / sources / confidence · confidence = ort. skor × 1.2 (tavan 1.0; boş → 0.0) · preview kırpma 500 karakter | L83-L90, L225-L230, L55 `mb_substr(...,0,500)` |

## §4. KnowledgeBase::search — İndeks Taraması + Kategori + Lifecycle

Dosya: `shared/src/AI/KnowledgeBase.php` · Satır: L25-L27, L36-L67, L96-L116, L134-L156

```php
$queryLower = strtolower($query);
foreach ($this->knowledge as $id => $item) {
    if (!empty($filters['category']) && $item['category'] !== $filters['category']) continue;
    // Basit keyword matching (gerçek implementasyonda vector cosine similarity)
    $score = $this->calculateRelevanceScore($queryLower, $item);
}
```

| Gözlem | Kanıt |
|---|---|
| O(n) tek tek tarama + sert kategori elemesi | L42, L44-L46 |
| Eşik: skor > 0 · sıralama: azalan · kesme: top-N · üst sınır 50 (`MAX_SEARCH_RESULTS`) | L51, L64, L66, L26 |
| 7 kategori; bilinmeyen kategori → exception · kayıt anahtarı `kb_N`, depolama bellek-içi (disk/DB yazımı yok) | L25, L98-L100, L102, L29-L31 |
| 365 günden eskisi lifecycle ile silinir | L27, L139-L145 |

## §5. calculateRelevanceScore — Ağırlıklı Keyword Skoru

Dosya: `shared/src/AI/KnowledgeBase.php` · Satır: L163-L194

```php
if (str_contains($titleLower, $query))   { $score += 0.6; }   // L171-L173
if (str_contains($contentLower, $query)) { $score += 0.3; }   // L176-L178
$queryWords = explode(' ', $query);                           // L181
foreach ($queryWords as $word) { if (strlen($word) < 3) continue;   // L184
    if (str_contains($titleLower, $word) || str_contains($contentLower, $word)) $matchCount++; }
$score += ($matchCount / count($queryWords)) * 0.1;           // L191
```

| Bileşen | Ağırlık | Satır |
|---|---|---|
| Tam query başlıkta / içerikte | 0.6 / 0.3 | L171-L178 |
| Kelime kesişimi (min 3 harf) | 0.1 × oran | L181-L191 |
| Toplam üst sınır | 1.0 | 0.6+0.3+0.1 |

## §6. AIWorkflow — Üç Retrieval Çağrı Noktası

Dosya: `shared/src/AI/AIWorkflow.php` · Satır: L61-L63, L72, L87, L111-L115, L218-L220

```php
// 3. Knowledge query — benzer önerileri ara
$query = ($context['mood'] ?? '') . ' ' . ($context['genre'] ?? '');
$knowledgeResults = $this->knowledge->search($query, 5);
```

| Çağrı | Sorgu biçimi | Top-N | Satır |
|---|---|---|---|
| Recommendation | `mood + genre` | 5 | L62-L63 |
| Prompt bağlamı + metadata sayacı | `['knowledge' => ...]` / `'knowledge_hits' => count(...)` | — | L72, L87 |
| Audio analysis | `key + mood + genre` (genre hep boş — L112 notu) | 3 | L114-L115 |
| Hardware | `status + hardware + deviceId` | 3 | L219-L220 |

**Kavram karşılığı:** "soru üret → indekste ara → prompt'a koy" zinciri repo'da **vardır**; sorgu üretimi sözlüktür (alan birleşimi), embedding **değildir**.

## §7. ToolCalling::file-search — Glob ile İndeks Taraması + İzin

Dosya: `shared/src/AI/ToolCalling.php` · Satır: L28-L40, L145-L166, L206-L222

```php
$this->tools['file-search'] = [
    'name'        => 'file-search',
    'description' => 'Dosya arar',
    'parameters'  => [['name' => 'pattern', 'type' => 'string', 'required' => true,
                       'description' => 'Glob pattern']],
    'executor'    => function(array $params): array {
        $path = $params['path'] ?? '.';
        $files = glob($path . '/' . $params['pattern']);
        return ['files' => $files, 'count' => count($files)];
    },
    'category'    => 'file',
];
```

| Gözlem | Kanıt |
|---|---|
| Dosya adı tabanlı retrieval — vault §3 kural 1 "Test-Path / glob" ile aynı araç | L215 + RAG.md §3.2 #1 |
| İçerik/semantik arama **değildir** (executor yalnız yol döner) | L213-L220 |
| 11 agent × kategori izin listesi; izin yoksa `allowed: false` (fail-safe) · önekten kategori türetme (`file-` → `file`) | L28-L40, L158-L163, L291-L298 |

## §8. MemorySystem — Öncelik Hiyerarşisi (retrieval sırası analogu)

Dosya: `shared/src/AI/MemorySystem.php` · Satır: L18-L22, L25-L31, L123-L147

```php
 * - L1 (Hot): SSOT dosyaları (CLAUDE, AGENTS, WORKFLOW) — Oturum sonu
 * - L2 (Warm): Görev dosyaları (ADR, architecture) — Görev sonu
 * - L3 (Cool): Referans dosyaları (testing, ui-design) — İsteğe bağlı
private const HIERARCHY = ['brain' => 5, 'index' => 4, 'agents' => 4, 'memory' => 3, 'log' => 1];
```

| Kavram | Karşılığı | Eşleşme |
|---|---|---|
| Vault okuma öncelik sırası | `HIERARCHY` skorları 5→1 | kısmi — sabit tablo |
| Cache-aside okuma | `remember()` L123-L147 | evet |
| TTL ile bayatlık önleme | `expires_at` kontrolü L129 | evet |

## §9. PromptEngine — Prompt Injection Filtresi (retrieval girdi kapısı)

Dosya: `shared/src/AI/PromptEngine.php` · Satır: L27-L35, L156-L186, L90-L96

```php
private const INJECTION_PATTERNS = [
    '/ignore\s+previous/i', '/you\s+are\s+now/i', '/system\s*:\s*/i', '/act\s+as/i',
    '/pretend\s+you/i', '/disregard/i', '/override/i',
];
if (preg_match($pattern, $prompt)) {
    $risks[] = "Potential prompt injection detected: {$pattern}";
    $sanitized = preg_replace($pattern, '[FILTERED]', $sanitized);
}
```

| Kontrol | Satır | Sonuç |
|---|---|---|
| 7 injection deseni | L27-L35 | risk + `[FILTERED]` |
| Token bütçesi 4096 aşımı · token ≈ karakter/3.5 (tahmin) | L25, L170-L174, L92-L95 | risk kaydı; gerçek tokenizer YOK (L92 yorumu) |
| Hassas veri deseni (`password\|secret\|api[_-]?key\|token`) | L177-L179 | risk kaydı |

## §10. AIOrchestrator — Routing İndeksi (soru → agent analoğu)

Dosya: `shared/src/AI/AIOrchestrator.php` · Satır: L26-L49, L73-L75, L125-L149

```php
private const AGENT_MAP = [
    'mo'      => ['domain' => 'coordination', 'layer' => 'coordination'],
    'backend' => ['domain' => 'php-api', 'layer' => 'l2-routing'],
    // ... 11 anahtar (L26-L38)
];
private const TASK_TYPES = [
    'recommendation' => ['agent' => 'backend', 'priority' => 'medium'],
    // ... 8 tip (L40-L49)
];
```

| Gözlem | Satır |
|---|---|
| 11 anahtarlı routing tablosu | L26-L38 |
| 8 görev tipi → agent + priority | L40-L49 |
| Bilinmeyen tip → exception; bilinmeyen agent → `success: false` | L73-L75, L127-L133 |

> **Eşleştirme:** `.ai/RAG.md` §3 "konu → hedef dosya" tablosu ile bu kod aynı işlevi yapar (tablodan eşleme); alan farklıdır: o dosya, bu agent.

## §11. AIEngine — Cosine Similarity: SÖZ VAR, KOD YOK

Dosya: `shared/src/AI/AIEngine.php` · Satır: L215-L217, L232-L234

```php
// DB'den aday şarkıları çek ve özelliklerini karşılaştır
// cosine similarity kullanarak skorla
return [];
```

| İddia | Kanıt | Değerlendirme |
|---|---|---|
| "cosine similarity kullanarak skorla" | L233 yorum satırı | yalnız yorum |
| Dönüş değeri | L234 `return [];` | hesaplama yok |
| Collaborative tarafı (ALS) | L216 yorum + L217 `return [];` | yalnız yorum |

**Sonuç:** Repo'da vektör benzerliği hesabı **YOK** — cos benzerliği iki yorum satırında anılır (KnowledgeBase L48, AIEngine L233). Uydurma hesap kodu yazılmaz.

## §12. KOD YOK Alanlar (yokluk cümleleri — tahmin yasak)

| Alan | Durum (2026-10-17) |
|---|---|
| Embedding üretimi (metin → vektör) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Vektör store / ANN (HNSW, ivf, pgvector) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Chunking (§/token bazlı bölme) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| BM25 / ters indeks / terim frekansı | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Gerçek tokenizer | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| `.ai/**/*.md` okuyup indeksleyen loader | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Retrieval değerlendirme / ranking metric | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Kalıcı indeks yazımı (disk/DB tablosu) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Query expansion / reformulation | Repo'da bu alan kodu YOK — kaynak: upstream docs (L203 kopyalar) |
| Reranking (çift aşamalı sıralama) | Repo'da bu alan kodu YOK — kaynak: upstream docs |

## §13. Negatif Ölçüm — Grep Kanıtları (araç çıktısı)

| Arama (`*.php`, repo kökü) | Sonuç | Tarih |
|---|---|---|
| `embedding\|Embedding` | **0 isabet** | 2026-10-17 |
| `vector_search\|VectorStore\|hnsw\|faiss\|pgvector` | **0 isabet** | 2026-10-17 |
| `BM25` (shared/src) | **0 isabet** | 2026-10-17 |
| `chunk\|Chunk` (shared/src) | 0 RAG isabeti — tek eşleşme ses byte chunk'ı: `home.coremusic.net/include/Stream/MusicStreamHandler.php` L344-L349 | 2026-10-17 |
| `cosine` (shared/src) | 2 isabet, **ikisi de yorum**: KnowledgeBase L48 · AIEngine L233 | 2026-10-17 |
| `vector` · `tokenizer` (shared/src) | 1+1 yorum: KnowledgeBase L19 `SemanticSearch (vector DB)` · PromptEngine L92 | 2026-10-17 |

## §14. `.ai/RAG.md` §3 — Gerçek İndeks Satırı Formatı + Eşleme Kuralları

Dosya: `.ai/RAG.md` · Satır: L71-L75, L90-L91, L100-L105

```markdown
| # | Konu / Soru Sınıfı | Hedef Dosya(lar) | Keyword'ler | Durum |
| 1 | Boot sırası, kanonik okuma listesi | [[CLAUDE]] §16 · [[../WORKFLOW]] (kök) boot | boot, okuma, kanonik, 13 dosya | IMPLEMENTED |
| 17 | PHP/backend kod yüzeyi | `shared/src` (165 .php) · alt alan adı `*/include/` | php, pdo, middleware, shared | IMPLEMENTED |
| 18 | AI stratejisi, RAG/embedding kararı | [[.decisions/accepted/ADR-030-ai-strategy-core]] | rag, embedding, ai-stratejisi, llm | PLANNED |
```

| Sütun / Kural | Anlamı | Kod karşılığı |
|---|---|---|
| `#` + Konu / Soru Sınıfı | sorgu sınıfı | `search($query)` girdisi |
| Hedef Dosya(lar) | retrieval sonucu | `results[]['source']` (L57) |
| Keyword'ler | indeks terimleri | kelimeler (L181) |
| Durum (IMPLEMENTED/PLANNED/UNKNOWN) | kanıt etiketi | **YOK** (kodda karşılığı yok) |
| §3.2 #1 "yalnız diskte var olan dosya (Test-Path / glob)" | hayalet satır silinir | `glob()` (ToolCalling L215) |
| §3.2 #4 "sayılar yalnız ölçümle yazılır" | Zero-Hallucination | bu dosyada uygulanıyor |

§3.1 başlık (L71): `### §3.1 Konu → Dosya Eşlemesi (20 satır — 2026-10-14 disk ölçümü)`

## §15. `.ai/RAG.md` §4 — Pipeline Durumu (alıntı: ADR-030)

Dosya: `.ai/RAG.md` · Satır: L111, L116-L122

> ADR-030 (AI Strategy Core) tespiti: *"RAG / embedding / vektör — ❌ PLANNED (kod 0, tablo 0)"*.

| # | Bileşen (başlık alıntısı) | Durum | Bu dosyadaki kanıt |
|---|---|---|---|
| 1 | Manuel keyword indeksi (`[[keys]]` + §3) | IMPLEMENTED | §14 |
| 2 | Chunk stratejisi "§-bazlı, hedef ~500 token" | PLANNED | §12 |
| 3-4 | Embedding adayı + vektör store "Seçim YAPILMADI" | PLANNED | §13 |
| 5 | Arama sırası "exact keyword → BM25 → vektör" | PLANNED | §4 (keyword var), BM25/vektör YOK |
| 6 | Güncelleme tetikleyicisi `vault-sync-post` | PLANNED | skill var, PHP kodu yok |
| 7 | Kaynak kapsamı `.ai/**/*.md` + boot dosyaları | PLANNED | §12 loader YOK |

## §16. İllüstratif — Python: Cosine Similarity

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
import math

def cosine_similarity(a: list[float], b: list[float]) -> float:
    dot = sum(x * y for x, y in zip(a, b))
    norm = math.sqrt(sum(x * x for x in a)) * math.sqrt(sum(y * y for y in b))
    return 0.0 if norm == 0.0 else dot / norm
```

**Kapsam:** embedding benzerliği — repo karşılığı YOK (§11) · O(d).

## §17. İllüstratif — Python: §-Bazlı Chunking

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
import re

def chunk_by_section(text: str) -> list[dict]:
    # RAG.md §4 #2 tasarımına karşılık gelen §-bazlı bölme
    parts = re.split(r"(?m)^(##\s+.+)$", text)
    chunks, cur = [], {"heading": "(preamble)", "body": ""}
    for part in parts:
        if re.match(r"^##\s+.+", part):
            if cur["body"].strip(): chunks.append(cur)
            cur = {"heading": part.strip(), "body": ""}
        else: cur["body"] += part
    if cur["body"].strip(): chunks.append(cur)
    return chunks   # metadata: başlık + (üretimde) dosya yolu
```

**Kapsam:** chunk stratejisi — vault'ta **PLANNED** (§15 #2) · repo grep 0 (§13).

## §18. İllüstratif — TypeScript: Client-Side Retrieval

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
type IndexRow = { topic: string; target: string; keywords: string[]; status: string };
// topic/target/keywords/status = RAG.md §3 sütunları

export function retrieve(rows: IndexRow[], query: string, topK = 3) {
  const q = query.toLocaleLowerCase("tr");
  return rows
    .map((r) => ({ row: r, score: r.keywords.filter((k) => q.includes(k)).length }))
    .filter((x) => x.score > 0)
    .sort((a, b) => b.score - a.score)
    .slice(0, topK);
}
```

**Kapsam:** istemci tarafı anahtar-eşleşmeli retrieval — §3 satır alanlarına birebir. Repo durumu: bu eşleştirme kodu JS tarafında da **YOK**.

## §19. İllüstratif — Go: Index Scan (lineer tarama + skor)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
// KnowledgeBase::search (PHP, L42-L66) ile aynı O(n) semantik; ağırlıklar §5 ile aynı.
func Scan(docs []Doc, query, category string, topK int) []Hit {
    q := strings.ToLower(query)
    var hits []Hit
    for _, d := range docs {
        if category != "" && d.Category != category { continue }
        score := 0.0
        if strings.Contains(strings.ToLower(d.Title), q) { score += 0.6 }
        if strings.Contains(strings.ToLower(d.Content), q) { score += 0.3 }
        if score > 0 { hits = append(hits, Hit{Doc: d, Score: score}) }
    }
    sort.Slice(hits, func(i, j int) bool { return hits[i].Score > hits[j].Score })
    if len(hits) > topK { hits = hits[:topK] }
    return hits
}
```

**Kapsam:** indeks taraması — ağırlıklar PHP ile aynı (0.6/0.3, §5) · O(n) + O(n log n).

## §20. İllüstratif — C#: Semantic Search (embed + top-K)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```csharp
public sealed record SemanticHit(string Id, string Title, double Score);

public interface IEmbedder { ValueTask<float[]> EmbedAsync(string text, CancellationToken ct); }

public sealed class SemanticSearch(IEmbedder embedder, IReadOnlyList<Doc> corpus)
{
    public async Task<IReadOnlyList<SemanticHit>> SearchAsync(string query, int topK, CancellationToken ct)
    {
        var q = await embedder.EmbedAsync(query, ct);
        var scored = new List<SemanticHit>(corpus.Count);
        foreach (var doc in corpus) {
            var v = await embedder.EmbedAsync(doc.Title, ct);
            scored.Add(new SemanticHit(doc.Id, doc.Title, Cosine(q, v)));   // Cosine: §16'daki ile aynı
        }
        return scored.OrderByDescending(h => h.Score).Take(topK).ToArray();
    }
}
```

**Kapsam:** semantik arama — `IEmbedder` soyutlaması repo'da **YOK** (§12). Karşılığı: KnowledgeBase L19 "SemanticSearch (vector DB)" — yalnız isim.

## §21. İllüstratif — Rust: BM25 Skoru + Basit Tokenizer

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```rust
pub struct Bm25 { pub k1: f32, pub b: f32 }   // typical: 1.2 / 0.75 · vault §4 #5 PLANNED
impl Bm25 {
    pub fn score(&self, tf: u32, doc_len: u32, avg_len: f32, idf: f32) -> f32 {
        let norm = 1.0 - self.b + self.b * (doc_len as f32 / avg_len.max(1.0));
        idf * (tf as f32 * (self.k1 + 1.0)) / (tf as f32 + self.k1 * norm.max(1e-6))
    }
}

pub fn tokenize(text: &str) -> Vec<String> {   // eşik 3: PHP strlen < 3 (L184) ile aynı
    text.to_lowercase().split(|c: char| !c.is_alphanumeric())
        .filter(|t| t.len() >= 3).map(|t| t.to_string()).collect()
}
```

**Kapsam:** BM25 + tokenizer — vault §4 #5 **PLANNED**, repo grep 0 (§13).

## §22. İllüstratif — C++: Vector Cosine

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```cpp
#include <cmath>

// Basit, optimize edilmemiş cosine — öğretim amaçlı; üretimde SoA + SIMD gerekir.
double cosine_similarity(const float* a, const float* b, int n) noexcept {
    double dot = 0.0, na = 0.0, nb = 0.0;
    for (int i = 0; i < n; ++i) {
        dot += static_cast<double>(a[i]) * static_cast<double>(b[i]);
        na  += static_cast<double>(a[i]) * static_cast<double>(a[i]);
        nb  += static_cast<double>(b[i]) * static_cast<double>(b[i]);
    }
    return (na == 0.0 || nb == 0.0) ? 0.0 : dot / (std::sqrt(na) * std::sqrt(nb));
}
```

**Kapsam:** vektör çarpımı — RAG vektör katmanının matematik çekirdeği. Repo durumu: `shared/src` içinde C++ yok (C++ yüzeyi Neva Engine / PLANNED).

## §23. Kavram ↔ Eşleşme Tablosu (vault kavramı → kod karşılığı)

| Vault kavramı (RAG.md) | Repo kod karşılığı | Eşleşme |
|---|---|---|
| Retrieval (belge bulma) | `KnowledgeBase::search` L36-L67 | **evet** |
| RAG pipeline (R→A→G) | `KnowledgeBase::ragQuery` L72-L91 | **kısmi** (G placeholder) |
| Relevance skoru | `calculateRelevanceScore` L163-L194 | **evet** (keyword, BM25 değil) |
| Chunk stratejisi (§-bazlı) | — | **YOK** |
| Embedding | — (yalnız yorum L48/L233) | **YOK** |
| Vektör store | — | **YOK** |
| BM25 | — | **YOK** |
| Manuel keyword indeksi | dosya var, indeksleyen kod yok | **kısmi** |
| Soru → dosya eşleme | `AIWorkflow` sorgu kurulumu L62/L114/L219 | **kısmi** |
| İndeks taraması (glob) | `file-search` → `glob()` L215 | **evet** |
| Okuma öncelik sırası | `MemorySystem::HIERARCHY` L25-L31 | **kısmi** |
| Durum etiketi | — | **YOK** |
| Güncelleme tetikleyicisi | `vault-sync-post` skill (PHP kodu yok) | **kısmi** |
| Retrieval güven skoru | `calculateConfidence` L223-L231 | **evet** |
| Prompt injection kapısı | `PromptEngine::validatePrompt` L156-L186 | **evet** |

## §24. Soru → Dosya Eşleme Örnekleri (RAG.md §3 üzerinden)

| # | Örnek soru | §3 satırı (başlangıç alıntısı) | Hedef | Durum |
|---|---|---|---|---|
| 1 | "RAG/embedding kararı neydi?" | `| 18 | AI stratejisi, RAG/embedding kararı |` | ADR-030 | PLANNED |
| 2 | "PHP backend kodu nerede?" | `| 17 | PHP/backend kod yüzeyi |` | `shared/src` (165 .php) | IMPLEMENTED |
| 3 | "Agent routing nasıl?" | `| 3 | Agent routing, handover, persona hiyerarşisi |` | AGENTS §4/§6/§15 | IMPLEMENTED |
| 4 | "Şablon seçimi hangi dosya?" | `| 10 | Şablon seçimi (Guardrail #16) |` | `.templates/index` (65 dosya) | IMPLEMENTED |

**Kod tarafı notu:** örnek 2'nin anahtar kelimeleri (`php`, `pdo`) `KnowledgeBase::search` gelse 0.3 içerik skoru alır (L176-L178) — repo indeksi **dosya düzeyi eşleme yapmaz**; eşleştirme bugün yalnız `.ai/RAG.md` §3 tablosu üzerinden elle yapılır.

## §25. Güvenlik Notları (kanıt ile)

| Not | Kanıt |
|---|---|
| Injection filtresi 7 desen + `[FILTERED]` | PromptEngine L27-L35, L162-L167 |
| `db-query` yalnız SELECT · izin yoksa `allowed: false` | ToolCalling L233-L237, L158-L163 |
| Retrieval girdisi güvenilmez metindir — doğrudan LLM'e verilmez; sırlar bu dosyaya yazılmaz | `validatePrompt` L156-L186 · bu dosya |

## §26. Yasaklar (bu dosya ve bu alt-dal)

| Yasak | Kural kaynağı |
|---|---|
| `git commit` atmak | AGENTS.md §16.5 — commit orkestratöre aittir |
| `shared/src/**` yazımı ve bu dosya dışına yazım (salt-okunur) | görev manifesti |
| Uydurma `Dosya:`/`Satır:` künyesi · illüstratif bloğa repo kanıtı süsü vermek | Zero-Hallucination (AGENTS §3) · bu dosya §1 |
| Beklemede kalan çift süslü parantez placeholder'ı · ölçülmemiş sayıya "ölçüldü" demek | RAG.md §6 #6 · RAG.md §3.2 kural 4 |

## §27. Yeniden Ölçüm Komutu

| Amaç | Komut (repo kökünden) |
|---|---|
| Satır sayısı | `[IO.File]::ReadAllLines($hedef,[Text.Encoding]::UTF8).Count` |
| Kapı | sonuç `>= 500` (band 500-520) → PASS |
| Frontmatter 10 alan · Authority son satır | ilk 14 satırda 10 alan · `$lines[-1]` birebir eşleşme |
| Mojibake · PHP kanıtı tazeliği | `ReadAllText` içinde U+FFFD ara → 0 · grep → satır kayması varsa künye güncellenir |
| İndeks formatı | `.ai/RAG.md` §3 satır sayısı yeniden sayılır |

## §28. Doğrulama Kapıları

| # | Kapı | Kriter | Durum |
|---|---|---|---|
| 1 | Satır sayısı | `>= 500` (band 500-520) | §27 ölçümü |
| 2 | Frontmatter + Authority | 10 alan; `title`/`parent` yok · son satır birebir | gözden geçirme |
| 3 | PHP kanıtı | her alıntı gerçek `Dosya:`/`Satır:` | 2026-10-17 okuma |
| 4 | Yokluk cümleleri | 10 satır "KOD YOK" | §12 |
| 5 | İllüstratif etiket | 6 dilde blok + üstünde etiket | §16-§22 |
| 6 | Placeholder + Mojibake | üretimde bekleyen yer tutucu yok · U+FFFD 0 | grep · §27 |
| 7 | Section sayısı | en az 25 `## §` | grep |

## §29. Gate Özeti

§1-§29: künye formatı 9 kural · envanter 7 PHP dosyası · 9 PHP kanıt bölümü (KnowledgeBase ×3, AIWorkflow, ToolCalling, MemorySystem, PromptEngine, AIOrchestrator, AIEngine) · KOD YOK 10 satır · grep negatif 6 satır · RAG.md alıntı 2 blok (§3 format + §4 ADR-030) · illüstratif 7 blok / 6 dil · eşleme 15 satır · soru→dosya 4 örnek · güvenlik 3 · yasak 4 · ölçüm 5 · kapı 7 → kapılar: `>=500` · FM 10 alan · Authority son satır.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
