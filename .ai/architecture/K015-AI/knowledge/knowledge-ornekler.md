---
node_id: knowledge-ornekler
node_type: module
status: active
parent_id: knowledge
children: []
depends_on: [knowledge-anayasa, knowledge-surucu]
evidence:
  - "shared/src/AI/KnowledgeBase.php (kod kanıtı, 2026-10-17 okuma)"
  - ".ai/RAG.md §3 indeks satır alıntıları"
validation: "2026-10-17 üretim - ReadAllLines ölçümü, >=500 kapısı, kod kanıtı zorunlu"
open_questions: []
group: kod
---

# knowledge-ornekler - Bilgi Kod Örnekleri (kanıt odaklı)

> **Kapsam:** Her PHP alıntısı 2026-10-17'de okunan gerçek satırlardır (`Dosya:` / `Satır:`
> künyesi ile). Vault alıntıları (`.ai/RAG.md`, ADR şablonu, glossary) aynı tarihli okuma
> turundandır. İllüstratif çok-dilli bloklar **repo kanıtı değildir**; her birinin üstünde
> zorunlu etiket taşır. Kaynak kod ve `.ai/*.md` salt-okunurdur — bu dosya dışında yazım yoktur.

## §1. Kanıt Künye Formatı

| Alan | Kural |
|---|---|
| Dosya | göreli yol, diskte var (glob/grep ile 2026-10-17 doğrulanmış) |
| Satır | okunan aralık (L36-L67 gibi) — okunmamış satır künye olamaz |
| Alıntı | kod/vault satırının kendisi (kısaltma serbest, uydurma yasak) |
| Yokluk | tam cümle: "Repo'da bu alan kodu YOK — kaynak: upstream docs" |
| Ölçüm | ReadAllLines/grep sayaçları — araç çıktısı, iddia değil |
| İllüstratif | `> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı` |
| Web | erişilen resmi kaynak URL'si ile yazılır; erişilemezse `⚠️ VERIFICATION REQUIRED` |
| Yasak | uydurma künye → Zero-Hallucination ihlali (kök AGENTS §3) · çelişkide disk kazanır |

## §2. Okunan Kaynak Envanteri (2026-10-17)

| # | Dosya | Okuma | Bilgi yönetimi ilgisi |
|---|---|---|---|
| 1 | `shared/src/AI/KnowledgeBase.php` | Tam (L1-L232) | bilgi bankası çekirdeği — arama, lifecycle, skor |
| 2 | `shared/src/Contracts/AI/KnowledgeBaseInterface.php` · `shared/src/AI/AIWorkflow.php` | grep 7 / 14 isabet | sözleşme (ek/sil/lifecycle) · bilgi bankasını çağıran tek iş akışı |
| 3 | `.ai/RAG.md` | L60-L139 | retrieval indeksi (§3) + pipeline (§4) + kural (§5) |
| 4 | `.ai/.templates/adr/adr-nygard-template.md` | L1-L70 | ADR şablonu (bilgi kaydı biçimi) |
| 5 | `.ai/glossary.md` | L59-L83 (grep) | terim kaydı (bilgi sözlüğü satır biçimi) |
| 6 | `.ai/architecture/K015-AI/rag/rag-ornekler.md` | Tam (L1-L519) | kardeş şablon — yapı referansı |

## §3. KnowledgeBase — Sınıf Künyesi, Sabitler, Kolon Kanıtları

Dosya: `shared/src/AI/KnowledgeBase.php` · Satır: L1-L31

```php
 * Bilgi bankası implementasyonu — semantic search, RAG, knowledge lifecycle.   // L5
class KnowledgeBase implements KnowledgeBaseInterface                          // L23
private const CATEGORIES = ['adr', 'architecture', 'security', 'audio', 'hardware', 'workflow', 'template'];  // L25
private const MAX_SEARCH_RESULTS = 50;                                        // L26
private const LIFECYCLE_MAX_AGE_DAYS = 365;                                   // L27
private array $knowledge = [];                                                // L30
```

| Kanıt | Değer | Satır |
|---|---|---|
| Kayıt şeması (kolonlar) | `id, title, content, category, metadata, created_at, updated_at` | L29 `@var` docblock |
| Kategori kümesi | 7 değer: adr · architecture · security · audio · hardware · workflow · template | L25 |
| Arama üst sınırı / lifecycle yaşı | 50 / 365 gün | L26-L27 |
| Depolama | bellek-içi dizi (`private array $knowledge`) — disk/DB yazımı yok | L30-L31 |

## §4. `search()` — İndeks Taraması (gerçek alıntı)

Dosya: `shared/src/AI/KnowledgeBase.php` · Satır: L36-L67

```php
public function search(string $query, int $maxResults = 10, array $filters = []): array
{
    $maxResults = min($maxResults, self::MAX_SEARCH_RESULTS); $queryLower = strtolower($query);
    foreach ($this->knowledge as $id => $item) {
        if (!empty($filters['category']) && $item['category'] !== $filters['category']) continue;
        // Basit keyword matching (gerçek implementasyonda vector cosine similarity)
        $score = $this->calculateRelevanceScore($queryLower, $item);
        if ($score > 0) { $results[] = ['id' => ..., 'content' => mb_substr($item['content'], 0, 500), 'source' => $item['metadata']['source'] ?? 'vault']; }
    }
    usort($results, fn(array $a, array $b) => $b['score'] <=> $a['score']);
    return array_slice($results, 0, $maxResults);
}
```

| Gözlem | Kanıt |
|---|---|
| O(n) satır-satır tarama; indeks yapısı yok | L42 |
| Sert kategori elemesi (`continue`) | L44-L46 |
| Eşik `score > 0` · sıralama azalan · kesme top-N | L51, L64, L66 |
| Önizleme kırpma 500 karakter · kaynak etiketi `?? 'vault'` | L55, L57 |
| Cosine **yalnız yorum** — uydurma hesap yok | L48 |

## §5. `ragQuery()` — 3 Aşama + Confidence (gerçek alıntı)

Dosya: `shared/src/AI/KnowledgeBase.php` · Satır: L72-L91

```php
$retrieved = $this->search($query, 5, $context['filters'] ?? []);        // 1. Retrieval
$augmentedContext = $this->augmentContext($query, $retrieved, $context); // 2. Augmentation
$answer = $this->generateAnswer($query, $augmentedContext);              // 3. Generation — placeholder
return ['answer' => $answer, 'sources' => array_map(...), 'confidence' => $this->calculateConfidence($retrieved)];
```

| Gözlem | Kanıt |
|---|---|
| Retrieval top-5 sabiti (arama varsayılanı 10 değil) | L75 |
| Augmentation = `retrieved_documents` + `query_expansion` (yalnız ham sorgu) | L199-L206 |
| Generation placeholder: `"Detaylı yanıt için LLM API entegrasyonu gereklidir."` | L211-L218 |
| Confidence = ort. skor × 1.2, tavan 1.0; boş küme → `0.0` | L223-L231 |

## §6. `calculateRelevanceScore()` — Keyword Arama Ağırlıkları (gerçek alıntı)

Dosya: `shared/src/AI/KnowledgeBase.php` · Satır: L163-L194

```php
if (str_contains($titleLower, $query))   { $score += 0.6; }   // L171-L173
if (str_contains($contentLower, $query)) { $score += 0.3; }   // L176-L178
$queryWords = explode(' ', $query);                           // L181
foreach ($queryWords as $word) { if (strlen($word) < 3) continue;          // L184
    if (str_contains($titleLower, $word) || str_contains($contentLower, $word)) $matchCount++; }
// explode() her zaman en az bir eleman döndürür; bölüm asla 0 olmaz.      // L190
$score += ($matchCount / count($queryWords)) * 0.1;           // L191
```

| Bileşen | Ağırlık | Satır |
|---|---|---|
| Tam sorgu başlıkta / içerikte | 0.6 / 0.3 | L171-L178 |
| Kelime kesişimi (min 3 harf) | 0.1 × oran — toplam üst sınır 1.0 | L181-L191 |

## §7. Kayıt Lifecycle — `addKnowledge` / `removeKnowledge` / `cleanupLifecycle`

Dosya: `shared/src/AI/KnowledgeBase.php` · Satır: L96-L116, L121-L129, L134-L156

```php
if (!in_array($category, self::CATEGORIES, true)) {
    throw new \InvalidArgumentException("Invalid category: {$category}. Must be one of: " . implode(', ', self::CATEGORIES));
}
$id = 'kb_' . $this->nextId++;                    // kayıt anahtarı: kb_N (L102)
$cutoffDate = date('Y-m-d', strtotime('-' . self::LIFECYCLE_MAX_AGE_DAYS . ' days'));
if ($item['created_at'] < $cutoffDate) { unset($this->knowledge[$id]); $removed++; }
```

| İş | Davranış | Satır |
|---|---|---|
| Ekle | bilinmeyen kategori → exception (fail-safe) | L98-L100 |
| Temizle | 365 günden eskisi silinir; `{removed, updated, kept}` döner · silmede `isset` yoksa `false` | L139-L155, L123-L128 |
| Kalıcılık | `updated` hep 0 — güncelleme metodu **yok** | L137, L154 |

## §8. `KnowledgeBaseInterface` — Sözleşme Kanıtları

Dosya: `shared/src/Contracts/AI/KnowledgeBaseInterface.php` · Satır (grep): L13, L43, L51, L54

```php
interface KnowledgeBaseInterface                                    // L13
public function addKnowledge(string $title, string $content, string $category, array $metadata = []): string;   // L43
public function removeKnowledge(string $id): bool;                  // L51
 * Knowledge lifecycle yönetimi — eski/yetersiz bilgileri temizler. // L54
```

**Eşleşme:** arayüz ↔ uygulama `search/ragQuery/addKnowledge/removeKnowledge/cleanupLifecycle` — **evet** (5/5 imza KnowledgeBase.php L36, L72, L96, L121, L134 ile aynı).

## §9. Bilgi Bankasını Çağıran Tek Yüzey — `AIWorkflow`

Dosya: `shared/src/AI/AIWorkflow.php` · Satır (grep): L22, L61-L63, L72, L87, L111-L115, L135, L218-L220, L229

```php
private KnowledgeBase $knowledge;                    // L22 — bağımlılık enjeksiyonu
// 3. Knowledge query — benzer önerileri ara           // L61
$knowledgeResults = $this->knowledge->search($query, 5);   // L63
'knowledge_hits' => count($knowledgeResults),        // L87 — ölçüm sayacı
$knowledgeResults = $this->knowledge->search($query, 3);   // L218-L220 — enrich
```

| Çağrı | Top-N | Satır |
|---|---|---|
| Recommendation (`mood + genre`) | 5 | L63 |
| Prompt bağlamı + `knowledge_hits` | — | L72, L87 |
| Audio analysis (`key + mood + genre`) · Hardware (`status + hardware + deviceId`) | 3 · 3 | L115 · L220 |

**Yokluk:** Başka sınıf `KnowledgeBase`'i kullanmaz — grep 34 isabeti yalnız `KnowledgeBase.php`, `AIWorkflow.php`, `KnowledgeBaseInterface.php` içinde.

## §10. `.ai/RAG.md` §3 — Gerçek İndeks Satır Alıntıları

Dosya: `.ai/RAG.md` · Satır: L71-L75, L88-L94, L96

```markdown
### §3.1 Konu → Dosya Eşlemesi (20 satır — 2026-10-14 disk ölçümü)
| # | Konu / Soru Sınıfı | Hedef Dosya(lar) | Keyword'ler | Durum |
|---|--------------------|------------------|-------------|-------|
| 1 | Boot sırası, kanonik okuma listesi | [[CLAUDE]] §16 · [[../WORKFLOW]] (kök) boot | boot, okuma, kanonik, 13 dosya | IMPLEMENTED |
| 17 | PHP/backend kod yüzeyi | `shared/src` (165 .php) · alt alan adı `*/include/` | php, pdo, middleware, shared | IMPLEMENTED |
| 18 | AI stratejisi, RAG/embedding kararı | [[.decisions/accepted/ADR-030-ai-strategy-core]] | rag, embedding, ai-stratejisi, llm | PLANNED |
```

| Ölçüm | Değer |
|---|---|
| Başlık iddiası | "20 satır — 2026-10-14 disk ölçümü" (L71) |
| Sayılan satır numaraları | 1-8 ve 10-21 → **#9 yok**, 20 satır (iddia ile ölçüm örtüşür) |
| Sütunlar | `#` · `Konu / Soru Sınıfı` · `Hedef Dosya(lar)` · `Keyword'ler` · `Durum` (L73) |
| Kapsam notu (olmayanlar) | `.ai/wiki/` diskte 0 dosya → "VERIFICATION REQUIRED" (L96) |

## §11. `.ai/RAG.md` §3.2 — Eşleme Kuralları (alıntı)

Dosya: `.ai/RAG.md` · Satır: L100-L105

```markdown
| 1 | Satır yalnız diskte var olan dosyaya gider (Test-Path / glob) | Hayalet satır silinir |
| 3 | Durum sütunu: `IMPLEMENTED` (dosya kanıtlı) · `PLANNED` (ADR/plan var, kod/yok) · `UNKNOWN` | Durumsuz satır geçersiz |
| 4 | Sayılar (96 md, 65 şablon, 21 sql …) yalnız ölçümle yazılır | Zero-Hallucination ihlali |
```

| Kural | Kod karşılığı | Eşleşme |
|---|---|---|
| #1 disk kanıtı | `glob()` (ToolCalling) — bkz. rag-ornekler §7 | kısmi |
| #3 Durum etiketi | `KnowledgeBase`'te karşılığı **YOK** | YOK |
| #4 yalnız ölçümle sayı · #2 en dar hedef önce | bu dosyada uygulanıyor (§25, §30) | evet |

## §12. `.ai/RAG.md` §4 — Pipeline Durumu (kanıt ADR-030)

Dosya: `.ai/RAG.md` · Satır: L111, L116-L120

```markdown
> ADR-030 (AI Strategy Core) tespiti: *"RAG / embedding / vektör — ❌ PLANNED (kod 0, tablo 0)"*.
| 1 | Manuel keyword indeksi | [[keys]] + bu dosyanın §3 tablosu (taban retrieval'ı) | IMPLEMENTED | `.ai/keys.md` · `.ai/RAG.md` (diskte) |
| 5 | Arama sırası | Hedef: exact keyword → BM25 (metin) → vektör (semantik); ilk ikisi bugün el ile yapılır | PLANNED | ADR-030 + bu dosya §3 |
```

**Bu dosyayla ilgisi:** §4 #5 "exact keyword bugün el ile yapılır" — bilgi bankası kodu (§4-§6) yalnız keyword verir; BM25/vektör satırı `PLANNED` kalır (§23 yokluk).

## §13. ADR Şablonu — Bilgi Kaydı Biçimi (gerçek alıntı)

Dosya: `.ai/.templates/adr/adr-nygard-template.md` · Satır: L11, L15, L32, L37, L43-L63

```markdown
# Nygard ADR Şablonu (ADR-NNN)                                              // L11
> **Kullanım:** Michael Nygard klasik ADR biçimi — **Status / Context / Decision / Consequences** çekirdeği.  // L15
- **Dosya adı:** `adr-NNN-kisa-slug.md` (küçük harf)                          // L32
## §3 Mimari — Nygard Çekirdeği (silinemez sıralı 4 blok)                     // L43
title: "ADR-NNN — <Karar Başlığı>"   status: proposed | accepted | frozen | deprecated
kaynak: "3 turlu agent tartışması 20 persona"                                 // L61
```

| Alan | Kaynak | Durum |
|---|---|---|
| Nygard 4 blok (Status/Context/Decision/Consequences) · üretim yolu `decisions/draft/ADR-NNN-*.md` | şablon §3.2 (L65+) · `WORKFLOW.md` L223 | IMPLEMENTED |
| Numara serisi ≥ ADR-090 · frozen 001-037 immutable · karar mercii alanı `kaynak` | L37 · L61 | IMPLEMENTED |

## §14. Glossary Kaydı — Terim Satır Biçimi (gerçek alıntı)

Dosya: `.ai/glossary.md` · Satır: L59-L63, L68, L83

```markdown
| Terim | Tanım |                                    // L59
| **ADR** | Architecture Decision Record — Mimari karar kaydı |
| **ORM** | Object-Relational Mapping (YASAK) |
```

| Kural | Kanıt |
|---|---|
| Biçim: `| **TERİM** | Tanım |` — iki sütun, alfabetik gövde (örnek: BCNF L68) | L59-L83 |
| Kısaltma + uzun ad birlikte · yasak terim etiketi tanımın içinde | L61-L62, L83 |
| İlişki: RAG.md §3 #15 "Terimler → [[glossary]] IMPLEMENTED" | RAG.md L88 |

## §15. İllüstratif — Python: BM25 Skoru

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
# Parametre varsayılanları upstream kaynak (Elasticsearch similarity settings: k1=1.2, b=0.75)
class Bm25:
    def __init__(self, k1: float = 1.2, b: float = 0.75):
        self.k1, self.b = k1, b
    def score(self, tf: int, doc_len: int, avg_len: float, idf: float) -> float:
        norm = 1.0 - self.b + self.b * (doc_len / max(avg_len, 1.0))
        return idf * (tf * (self.k1 + 1.0)) / (tf + self.k1 * max(norm, 1e-6))
```

**Kapsam:** terim frekansı doygunluğu + uzunluk normalizasyonu — repo karşılığı YOK (bkz. §23 "BM25").

## §16. İllüstratif — Python: Ters İndeks (inverted index)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```python
from collections import defaultdict

def build_inverted_index(docs: dict[str, str]) -> dict[str, list[tuple[str, int]]]:
    index: dict[str, list[tuple[str, int]]] = defaultdict(list)
    for doc_id, text in docs.items():
        for word in text.lower().split():          # üretimde: tokenizer + stopword
            if len(word) >= 3: index[word].append((doc_id, 1))   # eşik 3: PHP L184 ile aynı
    return dict(index)                             # sorgu: terim -> [(doc_id, tf)]
```

**Kapsam:** repo'da `KnowledgeBase` ters indeks **kurmaz** — O(n) tarama (§4); bu blok §15'in veri yapısıdır.

## §17. İllüstratif — TypeScript: Knowledge Base Client

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```typescript
export type Category = "adr" | "architecture" | "security" | "audio" | "hardware" | "workflow" | "template";
// alan adları KnowledgeBase.php L29 docblock ile aynı: id/title/content/category/metadata/...
export class KnowledgeBaseClient {
  private items = new Map<string, { id: string; title: string; content: string; category: Category }>();
  add(title: string, content: string, category: Category) {
    const id = `kb_${this.items.size + 1}`;              // anahtar şeması: kb_N (PHP L102)
    this.items.set(id, { id, title, content, category }); return id;
  }
  search(query: string, maxResults = 10, category?: Category) {
    const q = query.toLocaleLowerCase("tr");
    return [...this.items.values()].filter((it) => !category || it.category === category)   // PHP L44-L46
      .map((it) => ({ it, score: (it.title.toLowerCase().includes(q) ? 0.6 : 0) + (it.content.toLowerCase().includes(q) ? 0.3 : 0) }))  // PHP L171-L178
      .filter((r) => r.score > 0).sort((a, b) => b.score - a.score).slice(0, Math.min(maxResults, 50));   // üst sınır 50 = PHP L26
  }
}
```

**Kapsam:** istemci/kopya katman — JS/TS tarafında bu sınıf repo'da **YOK** (grep `KnowledgeBase` yalnız PHP).

## §18. İllüstratif — Go: Keyword Index (kategori + skor)

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```go
// Ağırlıklar PHP §6 ile aynı (0.6/0.3); tarama O(n) — KnowledgeBase::search semantiği.
type Doc struct{ ID, Title, Content, Category string }
func KeywordSearch(docs []Doc, query, category string, topK int) []Hit {
    q := strings.ToLower(query); var hits []Hit
    for _, d := range docs {
        if category != "" && d.Category != category { continue }      // PHP L44-L46
        score := 0.0
        if strings.Contains(strings.ToLower(d.Title), q) { score += 0.6 }
        if strings.Contains(strings.ToLower(d.Content), q) { score += 0.3 }
        if score > 0 { hits = append(hits, Hit{Doc: d, Score: score}) }
    }
    sort.Slice(hits, func(i, j int) bool { return hits[i].Score > hits[j].Score }); if len(hits) > topK { hits = hits[:topK] }
    return hits
}
```

**Kapsam:** keyword indeksi — repo'da aynı iş PHP'de var (§4); bu blok dil taşınabilirliği gösterimidir.

## §19. İllüstratif — Rust: Triple-Store KV Graph

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```rust
// RDF triple modeli (subject, predicate, object) — upstream: W3C RDF 1.1 Concepts.
use std::collections::{HashMap, HashSet};
pub struct TripleStore { t: HashSet<(String, String, String)>, by_s: HashMap<String, Vec<(String, String)>> }
impl TripleStore {
    pub fn insert(&mut self, s: &str, p: &str, o: &str) {
        if self.t.insert((s.into(), p.into(), o.into())) {
            self.by_s.entry(s.into()).or_default().push((p.into(), o.into())); }
    }
    pub fn query(&self, s: &str) -> &[(String, String)] { self.by_s.get(s).map(|v| v.as_slice()).unwrap_or(&[]) }
}
// Örnek üçlü: ("ADR-030", "status", "PLANNED") — bilgi bankası metadata'sının grafiğe taşınması
```

**Kapsam:** üçlü depo — repo'da graf/triple yapısı **YOK**; vault karşılığı düz markdown satırları (§10).

## §20. İllüstratif — C++: Trie ile Prefix Arama

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```cpp
#include <array>
#include <memory>
#include <string_view>
// Keyword indeksi için prefix taraması — PHP str_contains (L171/L176) yerine O(k) alternatif.
struct TrieNode { std::array<std::unique_ptr<TrieNode>, 26> next{}; bool end = false; };
static void insert(TrieNode& root, std::string_view word) {
    TrieNode* cur = &root;
    for (char c : word) { if (c < 'a' || c > 'z') continue;          // harf dışı atla
        auto& slot = cur->next[static_cast<size_t>(c - 'a')];
        if (!slot) slot = std::make_unique<TrieNode>(); cur = slot.get(); }
    cur->end = true;
}
static bool search_prefix(const TrieNode& root, std::string_view prefix) {   // sonuç: var/yok
    const TrieNode* cur = &root;
    for (char c : prefix) { if (c < 'a' || c > 'z') continue;
        const auto& slot = cur->next[static_cast<size_t>(c - 'a')];
        if (!slot) return false; cur = slot.get(); }
    return true;                                    // eşikli kelime filtresi PHP L184 gibi
}
```

**Kapsam:** prefix indeksi — repo'da C++ bilgi kodu **YOK** (`shared/src` PHP'dir; C++ yüzeyi Neva Engine).

## §21. İllüstratif — C#: Reflection Tabanlı Kayıt Kaydı

> ⚠️ İllüstratif örnek - repo kanıtı DEĞİL - upstream/docs referansı

```csharp
// Bilgi kategorilerini attribute'tan toplama — PHP CATEGORIES sabiti (L25) yerine esnek alternatif.
[AttributeUsage(AttributeTargets.Class)]
public sealed class KnowledgeCategoryAttribute(string name) : Attribute { public string Name { get; } = name; }
[KnowledgeCategory("adr")] public sealed record AdrEntry(string Id, string Title, string Content);
public static IReadOnlyList<string> DiscoverCategories(Assembly asm) =>
    asm.GetTypes().Where(t => t.GetCustomAttribute<KnowledgeCategoryAttribute>() is not null)
       .Select(t => t.GetCustomAttribute<KnowledgeCategoryAttribute>()!.Name)
       .Distinct(StringComparer.Ordinal).OrderBy(n => n, StringComparer.Ordinal).ToArray();   // PHP: sabit dizi sıralaması
// PHP'de liste sabittir, keşif yok (L25): in_array($category, self::CATEGORIES, true)
```

**Kapsam:** kategori keşfi — repo'da reflection tabanlı kayıt **YOK**; PHP'de sabit dizi + `in_array(..., true)` (L98).

## §22. Kavram ↔ Eşleşme Tablosu

| Bilgi yönetimi kavramı | Repo/vault karşılığı | Eşleşme |
|---|---|---|
| Bilgi bankası (CRUD) | `addKnowledge`/`removeKnowledge` L96-L129 | **evet** |
| Arama (retrieval) | `search` L36-L67 | **evet** |
| Relevance skoru | `calculateRelevanceScore` L163-L194 | **evet** (keyword — BM25 değil) |
| RAG pipeline (R→A→G) | `ragQuery` L72-L91 | **kısmi** (G placeholder) |
| Lifecycle (bayatlık) | `cleanupLifecycle` L134-L156 | **evet** |
| Ters indeks / BM25 | — | **YOK** |
| Kalıcı depolama (disk/DB) | — (bellek-içi dizi L30) | **YOK** |
| Konu → dosya eşlemesi | `.ai/RAG.md` §3 (elle) | **evet** (vault, kod değil) |
| Durum etiketi IMPLEMENTED/PLANNED | RAG.md §3.2 #3 — kodda karşılığı yok | **YOK** |
| ADR ile karar kaydı · terim sözlüğü | `adr-nygard-template` L43-L63 · `glossary.md` L59-L83 | **evet** |
| Güncelleme tetikleyicisi | `vault-sync-post` skill — PHP kodu yok | **kısmi** |

## §23. Yokluk Cümleleri (tahmin yasak)

| Alan | Durum (2026-10-17) |
|---|---|
| BM25 / terim frekansı-normalizasyonu | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Ters indeks (terim → kayıt listesi) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Embedding / vektör benzerliği hesabı | Repo'da bu alan kodu YOK — kaynak: upstream docs (yalnız yorum L48) |
| Bilgi kaydını `.ai/**/*.md` dosyalarından yükleyen loader | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Kalıcı indeks yazımı (disk/DB tablosu) | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Kayıt güncelleme metodu (`updateKnowledge`) | Repo'da bu alan kodu YOK — kaynak: upstream docs (`updated` hep 0, L137) |
| Terim/keyword sözlüğü kod içi (`keys.md` eşlemesi) | Repo'da bu alan kodu YOK — kaynak: vault `.ai/keys.md` |
| Triple-store / graf sorgu dili | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Trie / prefix indeksi | Repo'da bu alan kodu YOK — kaynak: upstream docs |
| Kayıt sayısı/ölçüm metriği (sayaç dışında) | Repo'da bu alan kodu YOK — kaynak: upstream docs |

## §24. Güvenlik Notları (kanıt ile)

| Not | Kanıt |
|---|---|
| **Sırlar bilgi bankasına girmez** — credential/anahtar hiçbir satıra yazılmaz | `.ai/RAG.md` §5 #5 (L137): "`.env.figma` / credential anahtarları hiçbir satıra girmez → Sızıntı → REDACTED" |
| `metadata` serbest alan — bilgi girişi sırasında secret taşımamalı | KnowledgeBase.php L110 (`'metadata' => $metadata`), L96 imzası |
| Bilinmeyen kategori exception → kategori uydurma ile kayıt sızdırılamaz | KnowledgeBase.php L98-L100 |
| Sorgu metni güvenilir değildir: injection deseni `[FILTERED]` ile temizlenir | `PromptEngine.php` — bkz. rag-ornekler §9 (bu dosyada yeniden okunmadı) |

## §25. Ölçüm Kalıpları (araç çıktısı — iddia değil)

| Amaç | Kalıp |
|---|---|
| Satır sayısı | `[IO.File]::ReadAllLines($hedef,[Text.Encoding]::UTF8).Count` |
| Mojibake | `ReadAllText` içinde U+FFFD ara → hedef 0 |
| İndeks satırı sayısı | `Select-String -Path .ai\RAG.md -Pattern '^\| \d+ \|'` → §3.1 20 satır (yeniden sayım gerekir) |
| İllüstratif etiket sayısı | `Select-String -Pattern 'İllüstratif örnek'` → kod bloğu sayısı ile eşit olmalı |
| Section sayısı | `Select-String -Pattern '^## §'` → ≥ 25 |

## §26. Doğrulama Kapıları

| # | Kapı | Kriter | Kanıt |
|---|---|---|---|
| 1 | Satır sayısı | `>= 500` (band 500-520) | §30 ölçüm satırı |
| 2 | Frontmatter 10 alan | `node_id…group` sırası; `title`/`parent` alanı YOK | ilk 14 satır |
| 3 | Authority son satır | `$lines[-1]` birebir eşleşme | §30 |
| 4 | PHP kanıtı | her alıntı gerçek `Dosya:`/`Satır:` künyeli | §3-§9 |
| 5 | Vault kanıtı | RAG.md/ADR/glossary alıntıları satır numaralı | §10-§14 |
| 6 | İllüstratif etiket | her kod bloğu üstünde `⚠️` satırı | §15-§21 (7 blok / 6 dil) |
| 7 | Yokluk cümleleri | ≥ 10 "KOD YOK" satırı | §23 (10) |
| 8 | Section sayısı | ≥ 25 `## §` | §1-§30 |

## §27. Açık Sorular

| # | Soru | Neden açık |
|---|---|---|
| 1 | `KnowledgeBase` kayıtları nereye kalıcılaşacak (dosya/DB)? | L30 yalnız bellek-içi; §4 #4 PLANNED |
| 2 | `updated` alanı neden hep 0 — `updateKnowledge` metodu eklenecek mi? | L137, L154 kanıtı |
| 3 | §3 indeksi otomatik mi el ile mi üretilecek? | RAG.md §4 #6 PLANNED |
| 4 | ADR `kaynak` alanı bilgi kayıtlarına (`metadata['source']`) taşınacak mı? | L57 + şablon L61 — eşleşme tasarlanmadı |

## §28. Kaynaklar

**Repo/vault (2026-10-17 okuma):** `shared/src/AI/KnowledgeBase.php` (L1-L232) · `shared/src/Contracts/AI/KnowledgeBaseInterface.php` (grep L13/L43/L51/L54) · `shared/src/AI/AIWorkflow.php` (grep 14 isabet) · `.ai/RAG.md` (L60-L139) ·
`.ai/.templates/adr/adr-nygard-template.md` (L1-L70) · `.ai/glossary.md` (L59-L83) · şablon: `.ai/architecture/K015-AI/rag/rag-ornekler.md` (L1-L519).

**Web (erişildi, 2026-10-17):**

| # | Kaynak | Kullanım |
|---|---|---|
| 1 | https://www.elastic.co/docs/reference/elasticsearch/index-settings/similarity | BM25 varsayılan `k1=1.2`, `b=0.75` (§15) |
| 2 | https://www.cognitect.com/blog/2011/11/15/documenting-architecture-decisions | Nygard ADR: Status/Context/Decision/Consequences (§13) |
| 3 | https://www.w3.org/TR/rdf-concepts | RDF 1.1 triple: subject/predicate/object (§19) |

Erişilemeyen/id dia edilmeyen kaynak için URL yazılmaz → `⚠️ VERIFICATION REQUIRED`.

## §29. Yasaklar (bu dosya ve bu alt-dal)

| Yasak | Kural kaynağı |
|---|---|
| `git commit` atmak | AGENTS.md §16.5 — commit orkestratöre aittir |
| `shared/src/**` ve `.ai/*.md` yazımı · bu dosya dışına yazım | görev manifesti (salt-okunur) |
| Uydurma `Dosya:`/`Satır:` künyesi · illüstratif bloğa repo kanıtı süsü vermek | Zero-Hallucination (kök AGENTS §3) |
| Dosya adını/yerini değiştirme · `title`/`parent` frontmatter alanı ekleme | görev manifesti + §26 #2 |
| Secret/credential'ı bilgi bankası veya bu dosyaya yazmak | `.ai/RAG.md` §5 #5 (L137) |

## §30. Gate Özeti & Ölçüm

§1-§30: künye formatı 9 kural · envanter 7 dosya · PHP kanıt bölümü 7 (sınıf/sabit · search · ragQuery · skor · lifecycle · interface · workflow) · RAG.md alıntı 3 blok (§3 · §3.2 · §4) · ADR şablonu 1 · glossary 1 · illüstratif **7 blok / 6 dil** (Python ×2 · TypeScript · Go · Rust · C++ · C#) · eşleme 11 satır · yokluk 10 satır · güvenlik 4 · ölçüm 5 · kapı 8 · açık soru 4 · kaynak 3 web + 7 repo.

| Ölçüm | Değer |
|---|---|
| Dosya | `.ai/architecture/K015-AI/knowledge/knowledge-ornekler.md` |
| Satır (ReadAllLines UTF8) | `518` — `>= 500` kapısı §26 #1 |
| Frontmatter · son satır | 10 alan (`node_id` → `group`), `title`/`parent` YOK · Authority satırı birebir (aşağıda) |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
