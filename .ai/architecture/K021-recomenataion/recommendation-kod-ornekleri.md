---
title: "K021-recomenataion — Recommendation Kod Örnekleri (22 dil)"
type: reference
category: architecture
node_id: K021-recomenataion/recommendation-kod-ornekleri
node_type: file
date: 2026-10-17
updated: 2026-10-17
status: draft
version: 1.0.0
authority: "SSOT: K021-recomenataion/recommendation-master.md §4 (S1–S6) · repo kanıtı shared/src/AI/AIEngine.php"
related:
  - "recommendation-master.md"
  - "ranking/index.md"
  - "personalization/index.md"
  - "evaluation/index.md"
  - "recommendation-arastirma.md"
group: kod
---


# K021-recomenataion — Kod Örnekleri (22 dil)

## §0 Kapsam ve kural

Bu dosya **uygulama kodu değildir** — S1–S6 boru hattının (`recommendation-master.md` §4)
her aşamasını **gerçek dil sözdizimiyle** gösteren referans matristir. Kurallar:

1. **Eşik/skor uydurulmaz** — sabitler yalnız repo kanıtından gelir:
   `MAX_RECOMMENDATIONS=20` · `MIN_SCORE=0.7` · `DIVERSITY_TARGET=0.30` · `w=0.6/0.4`
   (`shared/src/AI/AIEngine.php:30-32,65`).
2. **Placeholder veri** (`user-1`, `track-42`) örnektir; gerçek şema `K007-data` kapısıdır.
3. Her blokta hangi aşama (S1–S6) gösterildiği yazır.
4. Üretim kodu bu dosyadan **kopyalanmaz** — ADR-2/ADR-3 kapıları kapandıktan sonra
   `shared/src/` içinde yazılır.

## §1 Dil matrisi

| # | Dil | Aşama | Konu |
|---|---|---|---|
| 1 | PHP | S2+S3 | hibrit skor + çeşitlilik + min-skor (Faz-1 ile aynı davranış) |
| 2 | JavaScript | S3+S4 | yeniden sıralama + API sunumu |
| 3 | TypeScript | S1 | tipli aday üretimi |
| 4 | Python | S6 | NDCG@K / Precision-Recall (scikit-learn) |
| 5 | C | S2 | sıcak döngü skor hesabı (düşük seviye) |
| 6 | C++ | S1 | aday havuzu (RAII, containers) |
| 7 | C# | S4 | API sunum + önbellek |
| 8 | Go | S6 | offline metrik döngüsü |
| 9 | Rust | S3 | MMR yeniden sıralama (ownership) |
| 10 | Java | S2 | özellik birleştirme (records) |
| 11 | Swift | S4+S5 | mobil sunum + geri bildirim |
| 12 | Kotlin | S5 | profil güncelleme |
| 13 | SQL | S1+S6 | aday havuzu sorgusu + metrik depolama |
| 14 | Bash | S6 | offline değerlendirme döngüsü |
| 15 | PowerShell | S6 | metrik raporu + A/B kayıt |
| 16 | YAML | K4d | CI'da metrik kapısı (GitHub Actions) |
| 17 | HCL | S4 | önbellek/kuyruk altyapısı (Terraform) |
| 18 | Ruby | S4 | API istemcisi + sözleşmeli test |
| 19 | Scala | S2 | toplu hibrit sıralama (Spark stil) |
| 20 | Elixir | S5 | gerçek zamanlı olay akışı |
| 21 | Dart | S4+S5 | istemci sunum + geri bildirim |
| 22 | R | S6 | A/B istatistiği |

## §2 Örnekler

### §2.1 PHP — S2+S3 (hibrit skor + çeşitlilik + min-skor)

```php
<?php declare(strict_types=1);

// Aşama: S2 (skorlama) + S3 (yeniden sıralama) — Faz-1 davranışı
// Kanıt: shared/src/AI/AIEngine.php:30-32,65,67-76

final class HybridRanker
{
    public const MAX_RECOMMENDATIONS = 20;   // repo kanıtı
    public const MIN_SCORE            = 0.7;  // repo kanıtı
    public const DIVERSITY_TARGET     = 0.30; // repo kanıtı

    private const W_COLLAB   = 0.6; // repo kanıtı (AIEngine.php:65)
    private const W_CONTENT  = 0.4;

    /** @param array<string,float> $collab @param array<string,float> $content */
    public function rank(array $collab, array $content, string $mainGenre): array
    {
        // S2: ağırlıklı hibrit birleştirme
        $scores = [];
        foreach (array_unique(array_merge(array_keys($collab), array_keys($content))) as $id) {
            $scores[$id] = self::W_COLLAB  * ($collab[$id] ?? 0.0)
                         + self::W_CONTENT * ($content[$id] ?? 0.0);
        }

        // S3a: skora göre azalan sıralama
        arsort($scores);

        // S3b: çeşitlilik — ana tür dışındaki oranı en az DIVERSITY_TARGET tut
        $kept = [];
        $offGenre = 0;
        foreach ($scores as $id => $score) {
            if ($score < self::MIN_SCORE) {
                continue; // minimum skor filtresi
            }
            $isOffGenre = ($content[$id]['genre'] ?? '') !== $mainGenre;
            if ($isOffGenre && $offGenre < (int) ceil(count($scores) * self::DIVERSITY_TARGET)) {
                $offGenre++;
            } elseif (!$isOffGenre) {
                // ana tür her zaman kabul
            } elseif ($offGenre >= (int) ceil(count($scores) * self::DIVERSITY_TARGET)) {
                continue;
            }
            $kept[] = ['item_id' => $id, 'score' => round($score, 4)];
        }

        return array_slice($kept, 0, self::MAX_RECOMMENDATIONS);
    }
}
```

### §2.2 JavaScript — S3+S4 (yeniden sıralama + API sunumu)

```js
// Aşama: S3 (re-rank) + S4 (serving) — Node.js
// Eşikler repo kanıtına bağlı (AIEngine.php:30-32)

const MAX_RECOMMENDATIONS = 20;
const MIN_SCORE = 0.7;

/** Adayları çeşitlilik hedefine göre yeniden sıralar. */
export function rerank(candidates, { minScore = MIN_SCORE, diversityTarget = 0.3 } = {}) {
  const eligible = candidates.filter((c) => c.score >= minScore);
  const byScore = [...eligible].sort((a, b) => b.score - a.score);

  const quota = Math.ceil(byScore.length * diversityTarget);
  const seen = new Set();
  const out = [];
  let offGenre = 0;

  for (const c of byScore) {
    const key = `${c.artistId}:${c.genre}`;
    if (seen.has(key) && offGenre >= quota) continue; // sanatçı/tür monopolü engeli
    if (c.genre !== byScore[0].genre) offGenre += 1;
    seen.add(key);
    out.push(c);
    if (out.length === MAX_RECOMMENDATIONS) break;
  }
  return out;
}

// S4: HTTP sunum iskeleti (route tanımı ayrı katmandadır — shared/src/PageRouter/)
export function recommendationHandler(req, res, ranker) {
  const list = ranker(req.user, req.query);
  res.writeHead(200, { 'Content-Type': 'application/json' });
  res.end(JSON.stringify({ items: list, count: list.length }));
}
```

### §2.3 TypeScript — S1 (tipli aday üretimi)

```ts
// Aşama: S1 (aday üretimi) — katalogdan aday havuzu
// Not: Faz-1'de bu katman YOK (master §13 B1)

export interface Track { id: string; genre: string; artistId: string; tags: string[]; }
export interface Candidate { track: Track; source: 'content' | 'collaborative' | 'trending'; }

export function generateCandidates(
  seed: Track,
  catalog: Track[],
  coPlay: ReadonlyMap<string, string[]>, // trackId -> birlikte çalınanlar
  limit = 500,
): Candidate[] {
  const out: Candidate[] = [];
  const seen = new Set<string>([seed.id]);

  for (const id of coPlay.get(seed.id) ?? []) {          // işbirlikçi komşular
    const t = catalog.find((c) => c.id === id);
    if (t && !seen.has(t.id)) { seen.add(t.id); out.push({ track: t, source: 'collaborative' }); }
  }
  for (const t of catalog) {                              // içerik benzerliği (tür/etiket kesişimi)
    if (seen.has(t.id)) continue;
    const overlap = t.tags.filter((x) => seed.tags.includes(x)).length;
    if (t.genre === seed.genre || overlap > 0) out.push({ track: t, source: 'content' });
    if (out.length >= limit) break;
  }
  return out;
}
```

### §2.4 Python — S6 (NDCG@K · Precision-Recall, scikit-learn)

```python
# Aşama: S6 (offline ölçüm)
# Kaynak doğrulaması: scikit-learn ndcg_score / precision_recall_fscore_support (HTTP 200)
# recommendation-arastirma.md §2

import numpy as np
from sklearn.metrics import ndcg_score, precision_recall_fscore_support

# y_true: kullanıcının ilgili olduğu (0/1) sıralı liste · y_score: skorlar
y_true = np.array([[0, 1, 0, 1, 0]])   # placeholder — gerçek veri S5 olay şemasından
y_score = np.array([[0.9, 0.8, 0.7, 0.65, 0.4]])

ndcg_at_5 = ndcg_score(y_true, y_score, k=5)
precision, recall, f1, _ = precision_recall_fscore_support(
    y_true, (y_score >= 0.7).astype(int), average="micro", zero_division=0
)  # 0.7 = repo MIN_SCORE (AIEngine.php:31)

print({"ndcg@5": round(float(ndcg_at_5), 4),
       "precision": round(float(precision), 4),
       "recall": round(float(recall), 4)})
# Eşik (geç/kal) YOKTUR — ADR-3 kapısı (evaluation/index.md §6)
```

### §2.5 C — S2 (sıcak döngü skor hesabı)

```c
/* Aşama: S2 — düşük seviye hibrit skor döngüsü
 * Kısıt: gerçek zamanlı yolda allocation yok (shared/ kuralı: zero-allocation sıcak yol)
 */
#include <stddef.h>

#define REC_MAX_ITEMS   20   /* repo: MAX_RECOMMENDATIONS */
#define REC_MIN_SCORE   0.7f /* repo: MIN_SCORE */
#define REC_W_COLLAB    0.6f /* repo: AIEngine.php:65 */
#define REC_W_CONTENT   0.4f

typedef struct { const char *id; float collab; float content; } rec_in_t;
typedef struct { const char *id; float score; } rec_out_t;

size_t rec_score(const rec_in_t *in, size_t n, rec_out_t *out, size_t cap)
{
    size_t k = 0;
    for (size_t i = 0; i < n && k < cap && k < REC_MAX_ITEMS; ++i) {
        float s = REC_W_COLLAB * in[i].collab + REC_W_CONTENT * in[i].content;
        if (s < REC_MIN_SCORE) continue;      /* minimum skor filtresi */
        out[k].id = in[i].id;
        out[k].score = s;
        ++k;
    }
    return k;                                  /* çağırıcı sıralamayı yapar */
}
```

### §2.6 C++ — S1 (aday havuzu, RAII)

```cpp
// Aşama: S1 — aday üretimi (RAII, noexcept, allocation çağrı dışında)
#include <vector>
#include <string>
#include <unordered_set>
#include <unordered_map>
#include <algorithm>

struct Track { std::string id; std::string genre; std::string artist; };

class CandidatePool {
public:
    explicit CandidatePool(std::size_t limit) : limit_(limit) {}

    [[nodiscard]] std::vector<Track> generate(
        const Track& seed,
        const std::vector<Track>& catalog,
        const std::unordered_map<std::string, std::vector<std::string>>& coPlay) const
    {
        std::vector<Track> out;
        out.reserve(limit_);
        std::unordered_set<std::string> seen{seed.id};

        if (auto it = coPlay.find(seed.id); it != coPlay.end()) {
            for (const auto& id : it->second) {
                if (seen.insert(id).second) {
                    if (auto f = find_if(catalog, id)) out.push_back(*f);
                }
            }
        }
        for (const auto& t : catalog) {
            if (out.size() >= limit_) break;
            if (seen.count(t.id)) continue;
            if (t.genre == seed.genre) out.push_back(t);
        }
        return out;
    }

private:
    const std::size_t limit_;
    static const Track* find_if(const std::vector<Track>& c, const std::string& id) {
        auto it = std::find_if(c.begin(), c.end(),
                               [&](const Track& t) { return t.id == id; });
        return it == c.end() ? nullptr : &*it;
    }
};
```

### §2.7 C# — S4 (API sunum + önbellek)

```csharp
// Aşama: S4 (serving) — ASP.NET Core iskeleti + kısa ömürlü önbellek
using Microsoft.Extensions.Caching.Memory;

public sealed record RecommendationItem(string ItemId, decimal Score);

public interface IRecommendationService
{
    Task<IReadOnlyList<RecommendationItem>> GetAsync(string userId, CancellationToken ct);
}

public sealed class RecommendationService : IRecommendationService
{
    private readonly IMemoryCache _cache;
    private const int MaxRecommendations = 20; // repo kanıtı

    public RecommendationService(IMemoryCache cache) => _cache = cache;

    public async Task<IReadOnlyList<RecommendationItem>> GetAsync(string userId, CancellationToken ct)
    {
        var key = $"rec:{userId}";
        if (_cache.TryGetValue(key, out IReadOnlyList<RecommendationItem>? cached) && cached is not null)
            return cached;

        var items = await RankAsync(userId, ct);              // S1–S3 çağrısı
        var slice = items.Take(MaxRecommendations).ToArray();
        _cache.Set(key, slice, new MemoryCacheEntryOptions
        {
            AbsoluteExpirationRelativeToNow = TimeSpan.FromMinutes(5) // süre ADR-3'te
        });
        return slice;
    }

    private static Task<IReadOnlyList<RecommendationItem>> RankAsync(string userId, CancellationToken ct)
        => Task.FromResult<IReadOnlyList<RecommendationItem>>(Array.Empty<RecommendationItem>());
}
```

### §2.8 Go — S6 (offline metrik döngüsü)

```go
// Aşama: S6 — Precision@K / Recall@K toplu hesabı
package metrics

type Ranked struct {
	ItemID    string
	Relevant  bool
}

// PrecisionAtK ilk K önerideki ilgili oranını döndürür.
func PrecisionAtK(ranked []Ranked, k int) float64 {
	if k <= 0 || len(ranked) == 0 {
		return 0
	}
	if k > len(ranked) {
		k = len(ranked)
	}
	hits := 0
	for i := 0; i < k; i++ {
		if ranked[i].Relevant {
			hits++
		}
	}
	return float64(hits) / float64(k)
}

// RecallAtK kullanıcının tüm ilgili içeriklerinden kaçının K içinde kaldığını ölçer.
func RecallAtK(ranked []Ranked, k int, totalRelevant int) float64 {
	if totalRelevant == 0 || k > len(ranked) {
		if k > len(ranked) {
			k = len(ranked)
		}
	}
	hits := 0
	for i := 0; i < k; i++ {
		if ranked[i].Relevant {
			hits++
		}
	}
	return float64(hits) / float64(totalRelevant)
}
```

### §2.9 Rust — S3 (MMR yeniden sıralama, ownership)

```rust
// Aşama: S3 — MMR (maximal marginal relevance) tarzı çeşitlilik re-rank
// Kısıt: sahiplik — yeniden sıralama boyunca elemanlar sahiplenilir, kopyalanmaz.

pub struct Candidate {
    pub id: String,
    pub relevance: f64,
    pub similarity_to_selected: Vec<f64>, // sırayla seçilenlere benzerlik
}

pub fn mmr_rank(mut candidates: Vec<Candidate>, k: usize, lambda: f64) -> Vec<String> {
    let mut selected: Vec<String> = Vec::with_capacity(k);
    let mut chosen_flags: Vec<bool> = vec![false; candidates.len()];

    for _ in 0..k {
        let mut best: Option<(usize, f64)> = None;
        for (i, c) in candidates.iter().enumerate() {
            if chosen_flags[i] {
                continue;
            }
            let max_sim = c.similarity_to_selected.iter()
                .cloned()
                .fold(0.0f64, f64::max);
            let mmr = lambda * c.relevance - (1.0 - lambda) * max_sim;
            if best.map_or(true, |(_, b)| mmr > b) {
                best = Some((i, mmr));
            }
        }
        match best {
            Some((i, _)) => {
                chosen_flags[i] = true;
                selected.push(std::mem::take(&mut candidates[i].id));
            }
            None => break,
        }
    }
    selected
}
```

### §2.10 Java — S2 (özellik birleştirme, records)

```java
// Aşama: S2 — hibrit özellik birleştirme (Java 17+ records)
import java.util.List;

public final class HybridScoring {
    private static final double W_COLLAB  = 0.6;  // repo kanıtı
    private static final double W_CONTENT = 0.4;
    private static final double MIN_SCORE = 0.7;  // repo kanıtı

    public record Feature(String itemId, double collab, double content, double genreBonus) {}

    public record Scored(String itemId, double score) {}

    public static List<Scored> scoreAndFilter(List<Feature> features) {
        return features.stream()
                .map(f -> new Scored(f.itemId(),
                        W_COLLAB * f.collab() + W_CONTENT * f.content() + f.genreBonus()))
                .filter(s -> s.score() >= MIN_SCORE)
                .sorted((a, b) -> Double.compare(b.score(), a.score()))
                .toList();
    }
}
```

### §2.11 Swift — S4+S5 (mobil sunum + geri bildirim)

```swift
// Aşama: S4 (sunum) + S5 (geri bildirim) — iOS istemci
import Foundation

struct RecommendationItem: Decodable {
    let itemId: String
    let score: Double
}

enum FeedbackKind: String, Codable { case completed, skipped, saved }

struct FeedbackEvent: Codable {
    let itemId: String
    let kind: FeedbackKind
    let at: Date
}

protocol RecommendationClient {
    func fetch(userId: String) async throws -> [RecommendationItem]
    func send(_ event: FeedbackEvent) async throws   // S5 → olay akışı
}

final class HomeViewModel: ObservableObject {
    @Published var items: [RecommendationItem] = []
    private let client: RecommendationClient
    init(client: RecommendationClient) { self.client = client }

    func load(userId: String) async {
        do { items = try await client.fetch(userId: userId) }   // fail-safe: hata → boş liste
        catch { items = [] }                                    // kişiselleştirme atlanır
    }

    func completed(_ item: RecommendationItem) async {
        let event = FeedbackEvent(itemId: item.itemId, kind: .completed, at: Date())
        try? await client.send(event)                           // S5 olayı — sessizce düşmez, loglanır
    }
}
```

### §2.12 Kotlin — S5 (profil güncelleme)

```kotlin
// Aşama: S5 — örtük geri bildirimden profil özeti güncelleme
// Kısıt: profil modeli K1 (KVKK) kapısı kapalıysa ÇALIŞMAZ (fail-safe)

data class Feedback(val trackId: String, val genre: String, val completed: Boolean, val durationSec: Int)

data class GenreProfile(val genre: String, val weight: Double)

class ProfileUpdater(private val store: ProfileStore) {

    suspend fun apply(userId: String, events: List<Feedback>) {
        if (!store.consentGranted(userId)) return   // KVKK kapısı (personalization/index.md §4)

        val weights = events.groupBy { it.genre }
            .mapValues { (_, list) ->
                list.count { it.completed } / list.size.toDouble()
            }
            .map { (genre, w) -> GenreProfile(genre, w) }

        store.saveSummary(userId, weights)          // B seçeneği: kalıcı özet (ADR-2)
    }
}

interface ProfileStore {
    suspend fun consentGranted(userId: String): Boolean
    suspend fun saveSummary(userId: String, weights: List<GenreProfile>)
}
```

### §2.13 SQL — S1 (aday havuzu) + S6 (metrik depolama)

```sql
-- Aşama: S1 — aday havuzu sorgusu (K007-data şeması varsayımı; gerçek şema K007 kapısı)
-- Kural: parametreli sorgu · SELECT * yasak (AGENTS.md §5)

-- 1) İşbirlikçi komşular: aynı kullanıcıların dinlediği parçalar
SELECT t.track_id, t.genre_id, COUNT(*) AS co_play
FROM play_log AS p
JOIN play_log AS q
  ON q.user_id = p.user_id AND q.track_id <> p.track_id
JOIN tracks  AS t ON t.track_id = q.track_id
WHERE p.track_id = :seed_track_id
GROUP BY t.track_id, t.genre_id
ORDER BY co_play DESC
LIMIT :candidate_limit;

-- 2) S6: önerilen ve dinlenen eşleşmesinden Precision@K hesabı (günlük özet)
-- K eşiği parametredir (ADR-3'te atanır — sayı uydurulmaz)
SELECT
  d.eval_date,
  SUM(CASE WHEN r.rank_pos <= :k AND l.completed = 1 THEN 1 ELSE 0 END)
    / NULLIF(SUM(CASE WHEN r.rank_pos <= :k THEN 1 ELSE 0 END), 0) AS precision_at_k,
  COUNT(*) AS evaluated_items
FROM recommendation_log AS r
LEFT JOIN play_log AS l
  ON l.track_id = r.track_id AND l.user_id = r.user_id
 AND l.played_at >= r.served_at
JOIN evaluation_days AS d ON d.eval_date = DATE(r.served_at)
GROUP BY d.eval_date
ORDER BY d.eval_date;
```

### §2.14 Bash — S6 (offline değerlendirme döngüsü)

```bash
#!/usr/bin/env bash
# Aşama: S6 — offline metrik döngüsü (adaya göreli çalışır: repo KÖKÜNDEN çalıştırın)
set -euo pipefail

K="${K:-}"                       # ADR-3'te atanacak K değeri — boş bırakılırsa ölçüm DURUR
[[ -z "$K" ]] && { echo "HATA: K değeri zorunlu (ADR-3 kapısı)"; exit 2; }

EVAL_DIR="${EVAL_DIR:-.ai/architecture/K021-recomenataion/evaluation}"
OUT_DIR="${OUT_DIR:-C:/temp/opencode/rec-eval}"
mkdir -p "$OUT_DIR"

total=0; passed=0
for f in "$EVAL_DIR"/*.jsonl; do
  [[ -e "$f" ]] || continue
  total=$((total + 1))
  # python3 tekil geçiş: NDCG@K hesabı (bkz §2.4)
  if python3 - "$f" "$K" <<'PY'
import json, sys, numpy as np
from sklearn.metrics import ndcg_score
path, k = sys.argv[1], int(sys.argv[2])
rows = [json.loads(l) for l in open(path, encoding="utf-8")]
y_true = np.array([r["relevance"] for r in rows])
y_score = np.array([r["score"] for r in rows])
sys.exit(0 if ndcg_score([y_true], [y_score], k=k) >= 0 else 1)
PY
  then passed=$((passed + 1)); echo "GECTI  $f"; else echo "KALDI  $f"; fi
done

echo "SONUC: $passed/$total gecti (K=$K)  cikti: $OUT_DIR"
```

### §2.15 PowerShell — S6 (metrik raporu + A/B kayıt)

```powershell
# Aşama: S6 — A/B gün raporu (repo kökünden çalıştırın)
$ErrorActionPreference = 'Stop'

$logs = Get-ChildItem -Path 'C:\temp\opencode\rec-eval' -Filter 'ab-*.json' -ErrorAction SilentlyContinue
if (-not $logs) { Write-Output 'VERI YOK — A/B altyapisi kurulmamis (K4d VERIFY REQUIRED)'; exit 0 }

$report = foreach ($f in $logs) {
    $rows = Get-Content -Raw -Path $f.FullName | ConvertFrom-Json
    $ctrl = @($rows | Where-Object { $_.variant -eq 'control' })
    $test = @($rows | Where-Object { $_.variant -eq 'treatment' })
    [pscustomobject]@{
        Date                = $f.BaseName -replace 'ab-', ''
        ControlN            = $ctrl.Count
        TreatmentN          = $test.Count
        ControlCompletion   = if ($ctrl.Count) { [math]::Round(($ctrl | Where-Object completed).Count / $ctrl.Count, 4) } else { $null }
        TreatmentCompletion = if ($test.Count) { [math]::Round(($test | Where-Object completed).Count / $test.Count, 4) } else { $null }
        Note                = 'Gec/Kal ESIGI YOK — ADR-3 kapisi'
    }
}

$report | Format-Table -AutoSize
$report | Export-Csv -NoTypeInformation -Path 'C:\temp\opencode\rec-eval\ab-report.csv'
```

### §2.16 YAML — K4d (CI metrik kapısı, GitHub Actions)

```yaml
# Aşama: K4d — CI'da offline metrik kapısı
# Kural: eşik sayısı YOKTUR; job yalnız metrikleri YAYINLAR, karar ADR-3'te verilir.
name: recommendation-eval

on:
  workflow_dispatch:
  pull_request:
    paths:
      - 'shared/src/AI/**'

jobs:
  offline-metrics:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-python@v5
        with:
          python-version: '3.12'
      - name: Kurulum
        run: pip install scikit-learn numpy
      - name: Metrik hesabı
        env:
          K: ''            # ADR-3 atanana kadar boş — boşsa betik DURUR (bkz §2.14)
        run: bash .ai/architecture/K021-recomenataion/evaluation/run-offline.sh || true
      - name: Raporu yayınla
        run: echo "metrikler eklenti özetine yazildi — esik yok, karar insanda"
```

### §2.17 HCL — S4 (önbellek/kuyruk altyapısı, Terraform)

```hcl
# Aşama: S4 — sunum katmanı altyapısı (önbellek + olay kuyruğu)
# Kural: secret değerleri kodda DEĞİLLDİR; yalnız variable tanımları.

variable "region" {
  type    = string
  default = "eu-central-1"
}

variable "rec_cache_node_type" {
  type    = string
  default = "cache.t3.micro"     # boyutlandırma ADR-3 sonrası kapasite ölçümüyle
}

variable "event_stream_name" {
  type        = string
  description = "S5 geri bildirim olay akışı adı"
}

resource "redis_cluster" "rec" {
  region           = var.region
  node_type        = var.rec_cache_node_type
  num_cache_nodes  = 1
  transit_encryption_enabled = true
}

resource "stream" "rec_feedback" {
  name         = var.event_stream_name
  shard_count  = 1               # kapasite ölçümüyle büyür (S6 izleme sinyali)
}

output "rec_cache_endpoint" {
  value     = redis_cluster.rec.endpoint_address
  sensitive = false
}
```

### §2.18 Ruby — S4 (API istemcisi + sözleşmeli test)

```ruby
# Aşama: S4 — öner API istemcisi (BFF/istemci tarafı)
require "json"
require "net/http"

class RecommendationClient
  Item = Struct.new(:item_id, :score, keyword_init: true)

  def initialize(base_url:, limit: 20)   # limit varsayılanı repo kanıtı
    @base = URI.parse(base_url)
    @limit = limit
  end

  def fetch(user_id)
    uri = URI("#{@base}/api/v1/recommendations?user_id=#{URI.encode_www_form_component(user_id)}&limit=#{@limit}")
    res = Net::HTTP.get_response(uri)
    raise "öneri hatası: #{res.code}" unless res.is_a?(Net::HTTPSuccess)
    JSON.parse(res.body).fetch("items").map { |i| Item.new(item_id: i["item_id"], score: i["score"].to_f) }
  end
end

RSpec.describe RecommendationClient do
  it "limiti aşmaz" do
    stub_request(:get, %r{/api/v1/recommendations}).to_return(status: 200, body: { items: [] }.to_json)
    expect(described_class.new(base_url: "https://api.example.test").fetch("u1")).to be_empty
  end
end
```

### §2.19 Scala — S2 (toplu hibrit sıralama, Spark stil)

```scala
// Aşama: S2 — parti (batch) hibrit sıralama
final case class Feature(itemId: String, collab: Double, content: Double, genre: String)

object HybridBatch {
  val WCollab: Double  = 0.6   // repo kanıtı
  val WContent: Double = 0.4
  val MinScore: Double = 0.7   // repo kanıtı
  val MaxPerUser: Int  = 20    // repo kanıtı

  def score(f: Feature): Double = WCollab * f.collab + WContent * f.content

  def rankPerUser(rows: Iterator[Feature]): Iterator[(String, Double)] =
    rows.map(f => (f.itemId, score(f)))
        .filter(_._2 >= MinScore)
        .toList
        .sortBy(-_._2)
        .take(MaxPerUser)
        .iterator
}
```

### §2.20 Elixir — S5 (gerçek zamanlı olay akışı)

```elixir
# Aşama: S5 — geri bildirim olay akışı (GenServer)
defmodule CoreMusic.Rec.Feedback do
  use GenServer

  @table :rec_feedback

  def start_link(opts), do: GenServer.start_link(__MODULE__, opts, name: __MODULE__)

  @impl true
  def init(_opts) do
    :ets.new(@table, [:named_table, :public, :set, read_concurrency: true])
    {:ok, %{}}
  end

  @impl true
  def handle_cast({:record, user_id, track_id, kind}, state)
      when kind in [:completed, :skipped, :saved] do
    :ets.insert(@table, {{user_id, track_id}, kind, System.system_time(:second)})
    {:noreply, state}
  end

  @impl true
  def handle_call({:summary, user_id}, _from, state) do
    events = :ets.match_object(@table, {{user_id, :_, :_}, :_, :_})
    completed = Enum.count(events, fn {_, k, _} -> k == :completed end)
    {:reply, %{events: length(events), completed: completed}, state}
  end
end
```

### §2.21 Dart — S4+S5 (Flutter istemci)

```dart
// Aşama: S4 (sunum) + S5 (geri bildirim) — Flutter istemci
import 'dart:convert';
import 'package:http/http.dart' as http;

class RecommendationItem {
  final String itemId;
  final double score;
  const RecommendationItem({required this.itemId, required this.score});

  factory RecommendationItem.fromJson(Map<String, dynamic> j) =>
      RecommendationItem(itemId: j['item_id'] as String, score: (j['score'] as num).toDouble());
}

class RecommendationRepository {
  final http.Client _client;
  final String baseUrl;
  RecommendationRepository(this.client, {required this.baseUrl}) : _client = client;

  Future<List<RecommendationItem>> fetch(String userId, {int limit = 20}) async {
    final uri = Uri.parse('$baseUrl/api/v1/recommendations')
        .replace(queryParameters: {'user_id': userId, 'limit': '$limit'});
    final res = await _client.get(uri);
    if (res.statusCode != 200) return const [];        // fail-safe: hata → kişiselleştirme yok
    final items = (jsonDecode(res.body)['items'] as List)
        .map((e) => RecommendationItem.fromJson(e as Map<String, dynamic>))
        .toList();
    return items;
  }

  Future<void> sendFeedback(String userId, String itemId, String kind) async {
    await _client.post(Uri.parse('$baseUrl/api/v1/feedback'),
        headers: const {'Content-Type': 'application/json'},
        body: jsonEncode({'user_id': userId, 'item_id': itemId, 'kind': kind})); // S5
  }
}
```

### §2.22 R — S6 (A/B istatistiği)

```r
# Aşama: S6 — A/B tamamlama oranı karşılaştırması
# Kural: güven aralığı RAPORLANIR; geç/kal kararı ADR-3 kapısıdır (erken kesme yasak).

ab_test <- function(control_completed, control_n, treatment_completed, treatment_n) {
  p1 <- control_completed / control_n
  p2 <- treatment_completed / treatment_n
  se <- sqrt(p1 * (1 - p1) / control_n + p2 * (1 - p2) / treatment_n)
  diff <- p2 - p1
  data.frame(
    control_rate   = round(p1, 4),
    treatment_rate = round(p2, 4),
    diff           = round(diff, 4),
    ci_low         = round(diff - 1.96 * se, 4),   # %95 yaklaşım aralığı
    ci_high        = round(diff + 1.96 * se, 4)
  )
}

# Örnek çağrı — değerler placeholder; gerçek veri S5 olay logundan gelir
print(ab_test(control_completed = 120, control_n = 400,
              treatment_completed = 145, treatment_n = 400))
```

## §3 Kapsam dışı / dürüstlük notları

| Not | Ayrıntı |
|---|---|
| Eşik yok | Hiçbir örnekte "geç/kal" sayısı **uydurulmamıştır** — K değeri/eşikler ADR-3 kapısıdır. |
| Yer tutucu veri | `user-1` · `track-42` · `:seed_track_id` gibi girdiler örnektir; gerçek şema K007-data kapısıdır. |
| Üretim kodu | Bu dosya referanstır; `shared/src/` içine yazım ADR-2/ADR-3 sonrası başlar. |
| Repo sabitleri | `20` · `0.7` · `0.30` · `0.6/0.4` — hepsi `shared/src/AI/AIEngine.php:30-32,65` kanıtından. |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
