# search-algorithms.md — Arama Algoritmaları

> Kaynak: `packages/search` + `packages/ai/result-enricher.ts`.

## 1. Sorgu analizi (heuristic + AI)

- Tokenizasyon (2+ harf), stopword (tr/en)
- İpucu tespiti: canlı/resmi/cover/remix, yıl (2010-2015), dil (tr/en)
- AI genişletme: 3-5 varyant (deep search tohumları); AI yoksa heuristic

## 2. Aday seçimi — Search Index (MSA düzeni)

- Ters indeks: `terim → {youtubeId}` (bellekte, artımlı güncellenir)
- Önek eşleşmesi (3+ harf, 500 tavan)
- Eşitlikte popülerlik kırılımı; `limit` = MSA bütçesi

## 3. Skorlama (0..1, deterministik)

```
score = 0.45·term + 0.25·log10(views+1)/7 + 0.15·recency(2y yarı-ömür)
        + 0.15·quality + başlık bonusu(0.15) + sinyaller(±0.1)
```

## 4. Süzme / sıralama / sayfalama

`applyFilters` (views/duration/score/quality) → `sortVideos`
(relevance|view_count|upload_date|rating) → `paginate` (page/limit)

## 5. Açıklanabilirlik

`buildReasons()`: başlık/açıklama eşleşmesi, popülerlik, güncellik,
canlı/uyum sinyalleri — max 4, kullanıcıya gösterilir.

## V2

Semantic arama: embedding + kosinüs benzerliği; hibrit skor =
0.7·lexical + 0.3·semantic (pgvector / bellek-içi).
