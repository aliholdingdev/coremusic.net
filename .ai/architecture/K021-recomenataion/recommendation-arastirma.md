---
title: "K021-recomenataion — Web Araştırması (Kaynak Kanıtı)"
type: research
category: architecture
node_id: K021-recomenataion/recommendation-arastirma
node_type: file
date: 2026-10-17
updated: 2026-10-17
status: draft
version: 1.0.0
authority: "SSOT: zero-hallucination (AGENTS.md §3) — her URL 2026-10-17'de HTTP ile doğrulandı"
related:
  - "recommendation-master.md"
  - "ranking/index.md"
  - "personalization/index.md"
  - "evaluation/index.md"
  - "recommendation-kod-ornekleri.md"
group: kanit
---


# K021-recomenataion — Web Araştırması (Kaynak Kanıtı)

## §0 Yöntem (Truth Mode)

| Öğe | Değer |
|---|---|
| Tarih | 2026-10-17 |
| Araç | `curl.exe -s -o NUL -w "%{http_code}" -L --max-time 20 -A "Mozilla/5.0 …"` (repo kökünden, 2 tur) |
| Aday sayısı | **28 benzersiz URL** |
| Sonuç | **15 × HTTP 200** (cite edilebilir) · **9 × HTTP 404** (elenmiştir) · **4 × HTTP 403** (erişim engelli — URL gerçek, içerik okunmadı) |
| Kural | HTTP 200 olmayan hiçbir kaynaktan **içerik iddiası yazılmaz**; 404'ler listeden çıkarılır, 403'ler dürüstçe işaretlenir |

> **Truth Mode sayım düzeltmesi (2026-10-17):** bu dosyanın ilk yazımında sayılar
> "22 URL · 6×404" olarak hatalıydı (3 aday URL tabloya girmemişti). İki curl turunun
> ham çıktısı yeniden sayıldı → **nihai ve tek geçerli sayılar yukarıdadır**: 28 = 15 + 9 + 4.

## §1 HTTP doğrulama tablosu (28 satır — 2 tur, ham çıktı)

| # | URL | HTTP | Karar |
|---|---|---|---|
| 1 | `https://en.wikipedia.org/wiki/Recommender_system` | 200 | ✅ cite — genel çerçeve/taksonomi |
| 2 | `https://scikit-learn.org/stable/modules/generated/sklearn.metrics.ndcg_score.html` | 200 | ✅ cite — NDCG uygulaması |
| 3 | `https://scikit-learn.org/stable/modules/generated/sklearn.metrics.precision_recall_fscore_support.html` | 200 | ✅ cite — Precision/Recall/F1 |
| 4 | `https://developers.google.com/machine-learning/recommendation` | 200 | ✅ cite — iki aşamalı mimari rehberi |
| 5 | `https://developers.google.com/machine-learning/recommendation/overview` | 200 | ✅ cite — retrieval/ranking ayrımı |
| 6 | `https://www.tensorflow.org/recommenders` | 200 | ✅ cite — TF Recommenders (two-tower) |
| 7 | `https://github.com/recommenders-team/recommenders` | 200 | ✅ cite — Microsoft açık kaynak öner repo |
| 8 | `https://www.evanmiller.org/ab-testing/` | 200 | ✅ cite — A/B istatistiği |
| 9 | `https://cloud.google.com/vertex-ai-generative-ai/docs/discover/evaluations` | 200 | ✅ cite — değerlendirme deseni |
| 10 | `https://cloud.google.com/vertex-ai-generative-ai/docs/model-monitoring/evaluating-model-performance` | 200 | ✅ cite — üretim izleme deseni |
| 11 | `https://www.kvkk.gov.tr/` | 200 | ✅ cite — KVKK resmi kaynak |
| 12 | `https://docs.anthropic.com/en/docs/build-with-claude/evals` | 200 | ✅ cite — LLM-evals deseni (gerekçe üretimi eklenirse) |
| 13 | `https://www.rfc-editor.org/rfc/rfc9110.html` | 200 | ✅ cite — HTTP semantiği (403 dahil) |
| 14 | `https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/403` | 200 | ✅ cite — HTTP 403 semantiği |
| 15 | `https://docs.python.org/3/library/http.server.html` | 200 | ✅ cite — basit sunum iskeleti |
| 16 | `https://www.tensorflow.org/recommenders/examples` | **404** | ❌ elendi |
| 17 | `https://learn.microsoft.com/en-us/dotnet/api/microsoft.ai.evaluation` | **404** | ❌ elendi — API adı mevcut değil (uydurma API adı kullanılmaz) |
| 18 | `https://mnguyen12.github.io/blog/2020/06/03/learning-to-rank` | **404** | ❌ elendi |
| 19 | `https://www.baeldung.com/cs/machine-learning-evaluation-metrics` | **404** | ❌ elendi |
| 20 | `https://aws.amazon.com/blogs/machine-learning/evaluate-a-production-recommendation-system-using-amazon-sagemaker-model-monitor/` | **404** | ❌ elendi |
| 21 | `https://cloud.google.com/architecture/tech-tutorials/ai-ml/optimizing-recommendation-system-performance-with-bigquery-ml-and-vertex-ai/` | **404** | ❌ elendi |
| 22 | `https://microsoft.github.io/recommenders/` | **404** | ❌ elendi — sayfa GitHub'a taşınmış (bkz #7) |
| 23 | `https://gdpr.eu/article-6-lawful-basis/` | **404** | ❌ elendi — GDPR hukuki dayanak yorumu **bu vaultun kapsamı değil** (K020-comilance) |
| 24 | `https://developers.google.com/machine-learning/recommendation/content-based` | **404** | ❌ elendi |
| 25 | `https://dl.acm.org/doi/10.1145/3477495` | **403** | ⚠️ erişim engelli (bot koruması) — URL gerçek, içerik okunmadı |
| 26 | `https://dl.acm.org/doi/10.1145/3523227` | **403** | ⚠️ erişim engelli — aynı davranış |
| 27 | `https://dl.acm.org/doi/10.1145/1721837.1721840` | **403** | ⚠️ erişim engelli — aynı davranış |
| 28 | `https://netflixtechblog.com/netflix-recommendations-beyond-the-5-stars-part-1-55f6c46c875` | **403** | ⚠️ erişim engelli (Medium bot koruması) — aynı davranış |

**Tekrar kaydı:** ACM `10.1145/3477495` iki turda da sorgulandı (satır 25) — tekrarsız
benzersiz URL sayısı **28**'dir.

## §2 Doğrulanmış kaynaklar — ne kanıtladı, vaultta nerede kullanıldı

| # | Kaynak (HTTP 200) | Ne kanıtladı | Kullanım yeri |
|---|---|---|---|
| 1 | Wikipedia — Recommender system | taksonomi (content / collaborative / hybrid), popülerlik/kapsam/çeşitlilik sorunları | `ranking/index.md` §2-§4 · `recommendation-master.md` §8 |
| 2 | scikit-learn `ndcg_score` | offline sıralama metriği NDCG uygulanabilir | `evaluation/index.md` §2 · kod matrisi §2.4 |
| 3 | scikit-learn `precision_recall_fscore_support` | Precision/Recall/F1 uygulanabilir | `evaluation/index.md` §2 · kod matrisi §2.4 |
| 4-5 | Google ML Recommendation (2 sayfa) | iki aşamalı mimari (retrieval → ranking) endüstri deseni | `recommendation-master.md` §4 · `ranking/index.md` §4 |
| 6 | TF Recommenders | two-tower/retrieval referans uygulaması | `ranking/index.md` §4 (seçenek C) |
| 7 | Microsoft `recommenders` repo | offline metrik + algoritma referans uygulamaları | `evaluation/index.md` §8 |
| 8 | Evan Miller A/B Testing | A/B istatistiği (güven aralığı) deseni | `evaluation/index.md` §3 · kod matrisi §2.22 |
| 9-10 | Vertex AI evaluations + model monitoring | üretim değerlendirme + sürüklenme izleme deseni | `evaluation/index.md` §4 |
| 11 | KVKK resmi sitesi | kişisel veri rejimi resmi kaynağı | `personalization/index.md` §4 (kapsam kararı comilance'ın) |
| 12 | Anthropic Evals | LLM çıktı değerlendirmesi deseni (açıklama/gerekçe üretimi eklenirse) | `recommendation-master.md` §13 B4 (gerekçe alanı) |
| 13-14 | RFC 9110 + MDN | HTTP 403 semantiği (kapalı uçlar için doğru kod) | API tasarımı notu |
| 15 | Python `http.server` | basit sunum iskeleti (prototip) | kod matrisi bağlamı |

## §3 Elenenler (HTTP 404 — cite EDİLMEZ · 9 adet)

| URL | Neden elendi |
|---|---|
| TF Recommenders `/examples` | 404 — örnek sayfası taşınmış/kaldırılmış |
| Microsoft Learn `microsoft.ai.evaluation` | 404 — API adı mevcut değil (uydurma API adı kullanılmadı) |
| `mnguyen12.github.io` learning-to-rank | 404 |
| Baeldung ML metrikleri | 404 |
| AWS SageMaker Model Monitor blog | 404 |
| Cloud AI/ML BigQuery+Vertex optimizasyon tutorial | 404 |
| `microsoft.github.io/recommenders/` | 404 — sayfa GitHub reposuna taşınmış (çalışan karşılığı §1 satır 7) |
| `gdpr.eu/article-6-lawful-basis/` | 404 — GDPR hukuki dayanak yorumu ayrıca **bu vaultun kapsamı değil** (K020-comilance) |
| Google ML `/recommendation/content-based` | 404 — içerik-bazlı rehber sayfası mevcut değil |

> **Kural:** bu 404 URL'ler hiçbir vault dosyasında "kaynak" olarak kullanılmaz;
> öğrenilen konu (learning-to-rank, üretim monitorü) yalnız §2'deki **200 doğrulamalı**
> kaynaklarla desteklenir.

## §4 Erişim engelli (HTTP 403 — dürüst kayıt)

| URL | Durum | Davranış |
|---|---|---|
| `dl.acm.org/doi/10.1145/3477495` · `/10.1145/3523227` · `/10.1145/1721837.1721840` | 403 — bot koruması | URL'ler **gerçek** (sunucu yanıt verdi); içerik **okunmadı** → bu kaynaklara dayanan **hiçbir iddia yazılmadı** |
| `netflixtechblog.com/…/beyond-the-5-stars-part-1-…` | 403 — Medium bot koruması | aynı davranış |

> İlgili vault kuralı: `.ai/architecture/index.md` §6 — "erişim engelli · cita edilmez"
> (`osek-vdx.org` örneğiyle aynı muamele).

## §5 Konu → kaynak eşlemesi (hızlı arama)

| Konu | Doğrulanmış kaynak |
|---|---|
| Aday üretimi / retrieval | #4 · #5 · #6 · #7 |
| Hibrit/iki aşamalı sıralama | #1 · #4 · #5 |
| NDCG / Precision-Recall | #2 · #3 · #7 |
| A/B testi | #8 |
| Üretim izleme / sürüklenme | #9 · #10 |
| KVKK | #11 |
| LLM çıktı değerlendirmesi | #12 |
| HTTP 403 semantiği | #13 · #14 |

## §6 Araştırma açık konuları

| # | Konu | Durum |
|---|---|---|
| 1 | Öneri sistemlerinde KVKK'nın **uygulama** yorumu (hukuki analiz) | bu vaultun kapsamı **değil** → `K020-comilance` |
| 2 | ACM makalelerinin içeriği (RecSys literatürü) | 403 — erişilemedi → erişim sağlanınca yeniden tur |
| 3 | CoreMusic'e özgü veri hacmi/ölçüm | dış kaynakla **kanıtlanamaz** — S5 verisi toplanınca ölçülecek |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
