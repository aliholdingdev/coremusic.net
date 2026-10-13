---
title: "recommendation — Örnekler"
type: architecture-examples
node_id: K006-yapay-zeka/recommendation
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kod
---

# recommendation — Örnekler

## §1 Gerçek repo kodu (ÖNCELİK 1 — bu oturumda okunarak doğrulandı)

`shared/src/AI/AIEngine.php` satır 49–77 — hybrid öneri akışı:

```php
public function getRecommendations(array $context, int $limit = 20): array
{
    $limit = min($limit, self::MAX_RECOMMENDATIONS);
    ...
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

`shared/src/AI/AIEngine.php` satır 216–217 — collaborative tarafı **henüz boş**:

```php
// DB'den benzer kullanıcıları bul
// Gerçek implementasyonda matrix factorization (ALS) kullanılacak
return [];
```

`shared/src/Contracts/AI/AIEngineInterface.php` satır 26 — dönüş şeması:

```php
@return array<int, array{id: string, title: string, artist: string, score: float, reason: string}>
```

## §2 Resmi doküman örneği (URL + snippet)

**Microsoft Recommenders (Linux Foundation AI & Data)** ·
https://github.com/recommenders-team/recommenders (doğrulandı 2026-10-12):

> "Recommenders ... examples and best practices for building recommendation systems ... five key tasks:
> Prepare Data · Model · Evaluate · Model Select and Optimize · Operationalize."

Aynı depo benchmark tablosunda MovieLens 100k için `MAP · nDCG@k · Precision@k · Recall@k · RMSE · MAE`
sütunları ve `k=10` ranking metrikleri kullanır (README, 2026-10-12 okundu).

**LibRecommender** · https://librecommender.readthedocs.io/en/stable/ (doğrulandı 2026-10-12):

> "A hybrid recommender system, which allows users to use either collaborative-filtering or
> content-based features."

## §3 Uygulama notu

- Repo'daki 0.6/0.4 ağırlığı **hardcoded** — benchmark'a bağlanan bir kanıt yok (kapsam §5/R3).
- Collaborative kolu boşken pratikte öneriler yalnız content-based skorlarla döner.

## §4 İlişkili dosyalar

| Dosya | İlişki |
|---|---|
| `recommendation-kapsam.md` | bu dalın rolü, içinde/dışında, sınır soruları |
| `recommendation-dogrulama.md` | iddia → URL/dosya kanıt tablosu |
| `../index.md` · `../ai-master.md` §1/§3 | üst düğüm künyesi + çocuk tablosu (v2 satır 261) |

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12
