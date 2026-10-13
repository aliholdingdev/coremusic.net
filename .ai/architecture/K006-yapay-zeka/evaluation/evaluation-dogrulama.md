---
title: "Evaluation — Doğrulama"
type: architecture-scope
node_id: K006-yapay-zeka/evaluation
node_type: subdomain-verification
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: kanit
---

# Evaluation — Doğrulama

Amaç: `evaluation-kapsam.md` içindeki iddiaların kaynaklarını listelemek. Her satır bir iddia + kaynak + tarih + durum. Durum: ✅ doğrulandı · ⚠️ kısmi · UNKNOWN doğrulanamadı.

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | Evidently AI, öneri sistemleri metriklerini (RankSys/NDCG ağırlıklı recall benzeri metrikler) açıklayan resmi explainer dokümanı yayınlar | https://docs.evidentlyai.com/metrics/explainer_recsys | 2026-10-12 | ✅ |
| 2 | Evidently AI, drift (data/concept drift) ölçümü için resmi explainer dokümanı yayınlar | https://docs.evidentlyai.com/metrics/explainer_drift | 2026-10-12 | ✅ |
| 3 | Evidently AI genel bakış (neden ML monitoring · model kalitesi izleme) resmi dokümanda tanımlanır | https://docs.evidentlyai.com/introduction | 2026-10-12 | ✅ |
| 4 | scikit-learn cross-validation, hold-out/değerlendirme veri bölme ve sızıntı önleme (leakage) resmi belgelerde anlatılır | https://scikit-learn.org/stable/modules/cross_validation.html | 2026-10-12 | ✅ |
| 5 | Recommenders (Microsoft), öneri sistemi değerlendirme notbook'ları (precision@k · ndcg@k · map@k · recall@k) içeren resmi repo'dur | https://github.com/recommenders-team/recommenders | 2026-10-12 | ✅ |
| 6 | K006 evaluation v2 kapsamı: "A/B test framework · Guard · Guardian log" | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\v2.md` §2.4-8 (satır 246) | 2026-10-12 | ✅ |
| 7 | ai-master.md §3'te evaluation çıktısı "Metrik · drift · A/B ölçümü" olarak kayıtlıdır | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\ai-master.md` §3 (satır 71) | 2026-10-12 | ✅ |
| 8 | ai-master.md §4'te "Model değerlendirme yöntemi: UNKNOWN" kayıtlıdır | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\ai-master.md` §4 (satır 94) | 2026-10-12 | ✅ |
| 9 | ai-master.md §7'de model değerlendirme sorusu açık soru listesinde (satır 117) | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\ai-master.md` §7 (satır 117) | 2026-10-12 | ✅ |
| 10 | K006 sınırı: K006 = ML ürün tarafı, K016-AI = ajan altyapısı (bu alt alan K006'da tanımlı) | `C:\www\coremusic.net\.ai\architecture\K006-yapay-zeka\v2.md §2.6` (satır 159) | 2026-10-12 | ✅ |
| 11 | CoreMusic'te model/öneri metrik hesabı yapan kod bulunamadı (precision/recall/NDCG/drift çağıran sınıf yok) | `C:\www\coremusic.net\shared\src` — grep `ndcg\|precision\|recall\|drift\|map@` (0 isabet) | 2026-10-12 | ⚠️ |
| 12 | A/B test altyapısı (trafik bölme · anlamlılık testi) tanımlı değildir | UNKNOWN | 2026-10-12 | UNKNOWN |
| 13 | Drift eşiği ve otomatik tepki kuralı (geri alma · uyarı) netleşmemiştir | UNKNOWN | 2026-10-12 | UNKNOWN |

| 14 | Surprise `cross_validate(algo, data, measures=['rmse','mae'], cv=5)` ile 5-fold çapraz doğrulama çalıştırır | https://surprise.readthedocs.io/en/stable/getting_started.html | 2026-10-12 | ✅ |
| 15 | Surprise CV iterator'ları: KFold · LeaveOneOut · ShuffleSplit; scikit-learn API'sinden ilhamlı | https://surprise.readthedocs.io/en/stable/getting_started.html | 2026-10-12 | ✅ |
| 16 | Surprise GridSearchCV hiperparametre arama + hold-out ile unbiased değerlendirme | https://surprise.readthedocs.io/en/stable/FAQ.html | 2026-10-12 | ✅ |
| 17 | surpriselib.com benchmark: SVD ml-100k 5-fold RMSE 0.934 · MAE 0.737 | https://surpriselib.com/ | 2026-10-12 | ✅ |
| 18 | Evidently Test'ler Pass/Fail koşul tabanlıdır: `eq · gt · gte · lt · lte · is_in · not_in` | https://docs.evidentlyai.com/docs/library/tests | 2026-10-12 | ✅ |
| 19 | Evidently RecSys Test Preset: TestMAPK · TestNovelty · TestPersonalization · TestSerendipity · TestDiversity · TestCoverage | https://docs-old.evidentlyai.com/reference/all-tests | 2026-10-12 | ✅ |
| 20 | Evidently drift testleri `TestShareOfDriftedColumns` · `TestColumnDrift` dataset/column düzeyinde | https://docs-old.evidentlyai.com/user-guide/tests-and-reports/run-tests | 2026-10-12 | ✅ |
| 21 | Report preset'e `include_tests=True` ile otomatik test seti üretilir | https://docs.evidentlyai.com/docs/library/tests | 2026-10-12 | ✅ |
| 22 | sklearn `cross_validate` çoklu metrik + çoklu CV iterator sonucu döndürür | https://scikit-learn.org/stable/modules/generated/sklearn.model_selection.cross_validate.html | 2026-10-12 | ✅ |
| 23 | AWS Personalize: yeni etkileşim verisi anında öneri sonuçlarını etkiler | https://docs.aws.amazon.com/personalize/latest/dg/how-new-data-influences-recommendations.html | 2026-10-12 | ✅ |
| 24 | Drift ölçümünde yaygın metrikler: Hellinger · Jensen-Shannon · Kullback-Leibler · Wasserstein · PSI | https://docs.evidentlyai.com/metrics/explainer_drift | 2026-10-12 | ✅ |
| 25 | Model performans drift'i gerçek değerlere göre ölçülür (MAE · RMSE · accuracy — target drift) | https://docs.evidentlyai.com/metrics/explainer_drift | 2026-10-12 | ✅ |
| 26 | Amazon Personalize Real-Time, canlı kullanıcı bağlamına göre anında öneri üretir | https://docs.aws.amazon.com/personalize/latest/dg/personalize-real-time.html | 2026-10-12 | ✅ |
| 27 | AWS Personalize event tracker, uygulama etkileşim verisini alır ve indeksler | https://docs.aws.amazon.com/personalize/latest/dg/data-ingestion.html | 2026-10-12 | ✅ |
| 28 | Metrik hesaplama maliyeti/SLA'sı (gerçek zamanlı vs batch) netleşmedi | — | 2026-10-12 | UNKNOWN |
| 29 | Hype döngüsü doğrulama tekrar sıklığı (otomatik/manuel) belirsiz | — | 2026-10-12 | UNKNOWN |
| 30 | Değerlendirme veri seti bölme stratejisi (hold-out penceresi) sabitlenmedi | — | 2026-10-12 | UNKNOWN |
| 31 | evaluation kademe-2/3 kapsamı resmileşmedi | ADR-097 K4 (referans) | 2026-10-12 | ⚠️ BEKLİYOR |

**Sayım:** ✅24 · ⚠️2 · UNKNOWN5 · Toplam31
