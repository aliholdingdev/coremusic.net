---
node_id: K021-recomenataion-web-arastirma
node_type: subdomain
status: draft
parent_id: K021-recomenataion
children: []
depends_on: [K006-yapay-zeka, K009-security, K020-discovery]
evidence:
  - ".ai/architecture/K021-recomenataion/recommendation-arastirma.md §0-§1 (28 URL = 15×200 · 9×404 · 4×403 — curl+UA, 2026-10-17)"
  - "2026-10-18 taze doğrulama: evidentlyai ×2 · weaviate · cran recometrics = HTTP 200 (fetch)"
  - "2026-10-18 düzeltme: cran 'recomputetrics' 404 → doğru paket adı 'recometrics'"
validation: "≥5 gerçek URL; her biri doğrulanmış (erişim tarihi ile); ulaşılamayan '⚠️'/'404'/'403' etiketiyle."
open_questions:
  - "⚠️ dl.acm.org 3 DOI + netflixtechblog = 403 (bot koruması) — URL gerçek, içerik okunamadı, içerik iddiası yazılmaz"
  - "⚠️ Springer (10.1007/s13042-017-0762-9) = bot challenge · ScienceDirect (S0950705116302763) = HTTP 400 — erişim engelli sayıldı"
group: kanit
---

# Recommendation — Web Araştırması

> K021 araştırma kanıtı: **önce doğrulama, sonra yazım** (brief kuralı). Ana küme
> `recommendation-arastirma.md` (28 URL — 2026-10-17 curl); bu dosya 2026-10-18
> taze turu + ölü/engelli kayıtları taşır.

## §1. Birincil kaynaklar — 2026-10-18 doğrulandı (fetch HTTP 200)

| # | URL | Ne verir | Erişim |
|---|---|---|---|
| 1 | https://evidentlyai.com/ranking-metrics/evaluating-recommender-systems | 10 metrik çerçevesi: predictive (Precision/Recall@K) · ranking (NDCG/MAP/MRR/Hit Rate) · behavioral (diversity/novelty/serendipity/popularity bias) · business (CTR/ARPU) — offline/online ayrımı | 2026-10-18 ✓ |
| 2 | https://evidentlyai.com/ranking-metrics/precision-recall-at-k | Precision/Recall@K tanımı + K parametresi + F-beta; **rank-aware olmadığı** uyarısı (NDCG/MAP önerisi) | 2026-10-18 ✓ |
| 3 | https://weaviate.io/blog/retrieval-evaluation-metrics | search/öneri ortak metrikleri: precision@K · recall@K · MRR · MAP · NDCG (+ pytrec_eval kod örneği) — "MAP öneride, NDCG retrieval'da yaygın" | 2026-10-18 ✓ |
| 4 | https://cran.r-project.org/package=recometrics | R paketi: P@K · R@K · AP@K · NDCG@K · Hit@K · RR@K · ROC-AUC · PR-AUC (implicit-feedback — v0.1.6-3) | 2026-10-18 ✓ |

## §2. Doğrulanmış ana küme — arastirma.md (curl+UA, 2026-10-17 — HTTP 200)

| # | URL | Ne verir |
|---|---|---|
| 1 | https://developers.google.com/machine-learning/recommendation | iki aşamalı mimari (retrieval → ranking) endüstri standardı |
| 2 | https://developers.google.com/machine-learning/recommendation/overview | retrieval/ranking ayrımı |
| 3 | https://www.tensorflow.org/recommenders | TF Recommenders (two-tower) |
| 4 | https://github.com/recommenders-team/recommenders | Microsoft açık kaynak öner repo |
| 5 | https://scikit-learn.org/stable/modules/generated/sklearn.metrics.ndcg_score.html | NDCG uygulaması |
| 6 | https://scikit-learn.org/stable/modules/generated/sklearn.metrics.precision_recall_fscore_support.html | Precision/Recall/F1 |
| 7 | https://www.evanmiller.org/ab-testing/ | A/B istatistiği (online deney) |
| 8 | https://cloud.google.com/vertex-ai-generative-ai/docs/discover/evaluations | değerlendirme deseni |
| 9 | https://cloud.google.com/vertex-ai-generative-ai/docs/model-monitoring/evaluating-model-performance | üretim izleme/sürüklenme deseni |
| 10 | https://www.rfc-editor.org/rfc/rfc9110.html | HTTP semantiği (403 dahil — API hata kodları) |
| 11 | https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/403 | 403 erişim reddi semantiği |
| 12 | https://www.kvkk.gov.tr/ | KVKK resmi kaynak (K1 kapısı) |
| 13 | https://docs.anthropic.com/en/docs/build-with-claude/evals | LLM-evals deseni (gerekçe üretimi eklenirse) |
| 14 | https://en.wikipedia.org/wiki/Recommender_system | genel çerçeve/taksonomi |
| 15 | https://docs.python.org/3/library/http.server.html | basit sunum iskeleti |

## §3. Kapsama etkisi (hangi karar bu kaynaklara dayanıyor)

| Bulgu | Kaynak | K021 etkisi |
|---|---|---|
| İki aşamalı mimari endüstri standardı | §2/1-3 | S1 (aday/aday-havuzu) ayrı aşama olarak kanonik (master §4) |
| Offline metrikler uygulanabilir (NDCG/Precision) | §1/1-4 · §2/5-6 | test-stratejisi §4 REC-T20; **eşik yok — ADR-3** |
| K parametresi kesim noktasıdır (K020'ye K karışmasın) | §1/2 | `MAX_RECOMMENDATIONS=20` ölçüm K'sından farklı — sözlükte ayrıştırıldı |
| MAP öneride, NDCG retrieval'da yaygın | §1/3 | evaluation/ metrik seti için öncelik önerisi (karar değil) |
| Online A/B + üretim izleme deseni | §2/7-9 | test-stratejisi §4 REC-T22/23 |
| 403 = erişim reddi (RFC 9110) | §2/10-11 | API sözleşmesi hata semantiği (master §18) |

## §4. Ölü / erişim engelli kayıtlar (cite EDİLMEZ)

### §4.1 404 — elendi (9, arastirma.md #16-24)

| URL | Not |
|---|---|
| `https://www.tensorflow.org/recommenders/examples` | taşınmış |
| `https://learn.microsoft.com/en-us/dotnet/api/microsoft.ai.evaluation` | API adı mevcut değil (**uydurma API adı kullanılmaz**) |
| `https://microsoft.github.io/recommenders/` | GitHub'a taşınmış (→ §2/4) |
| `https://gdpr.eu/article-6-lawful-basis/` | GDPR hukuki dayanak yorumu bu vaultun kapsamı değil |
| `https://developers.google.com/machine-learning/recommendation/content-based` | 404 |
| + 4 blog/eğitim URL'si | arastirma.md #18-21 |
| `https://cran.r-project.org/package=recomputetrics` | **404 (2026-10-18)** — yanlış paket adı → doğru: `recometrics` (§1/4) |

### §4.2 Erişim engelli — URL gerçek, içerik okunamadı (⚠️)

| URL | Durum | Kural |
|---|---|---|
| `https://dl.acm.org/doi/10.1145/3477495` · `10.1145/3523227` · `10.1145/1721837.1721840` | 403 (bot koruması — 2026-10-17) | içerik iddiası yazılmaz |
| `https://netflixtechblog.com/netflix-recommendations-beyond-the-5-stars-part-1-55f6c46c875` | 403 (Medium) | içerik iddiası yazılmaz |
| `https://link.springer.com/article/10.1007/s13042-017-0762-9` | bot challenge (2026-10-18) | ⚠️ içerik okunamadı — yalnız bibliyografik atıf |
| `https://www.sciencedirect.com/science/article/pii/S0950705116302763` | HTTP 400 (2026-10-18) | ⚠️ içerik doğrulanamadı — atıf yapılmaz |

**Sayım düzeltmesi (Truth Mode):** ilk yazım "17 URL / 6×404" yanlıştı →
**28 URL = 15 + 9 + 4** (arastirma.md §0-§1); bu dosya 2026-10-18 turuyla
`recomputetrics` 404'ünü ve 2 engelli erişimi ekler.

## §5. Yöntem

Sıra: konu → arama → **fetch ile doğrulama** → eşleme → yazım. Doğrulama iki tur:
(1) 2026-10-17 curl+UA (28 URL), (2) 2026-10-18 fetch (4 yeni URL + 3 engelli).
Kural: erişilemeyen URL'e `⚠️`/`404`/`403` etiketi konur, **silinmez ve içerik
iddiası üretilmez**; yanlış ad (`recomputetrics`) düzeltmesi kayda geçer.

---

**Authority:** Bayram Ali / Vault Steward
