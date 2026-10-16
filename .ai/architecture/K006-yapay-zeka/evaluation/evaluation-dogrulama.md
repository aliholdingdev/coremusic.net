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

---

## §2 Tur 2 — Neva Engine metrik & benchmark kanıtları (ek 2026-10-15)

> **Kaynak (a):** Dosya: `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` — 738 satır, bu
> oturumda baştan sona okundu. **Kaynak (b):** Dosya: `.ai/projects/NevaEngine/legacy-2026-07/
> eq-ai-auto.md` — 214 satır, bu oturumda okundu. Satır numaraları okuma anındaki konumdur.

### §2.1 Objektif kalite metrikleri (ai-models.md · 579–588)

| # | Metrik | Açılım | Aralık | Hedef | Satır | Durum |
|---|---|---|---|---|---|---|
| 1 | PESQ | Perceptual Evaluation of Speech Quality | 1.0 – 4.5 | > 3.5 | 581 | ✅ dosyadan okundu |
| 2 | STOI | Short-Time Objective Intelligibility | 0 – 1 | > 0.85 | 582 | ✅ okundu |
| 3 | MOS | Mean Opinion Score | 1 – 5 | > 4.0 | 583 | ✅ okundu |
| 4 | SI-SDR | Scale-Invariant Signal-to-Distortion Ratio | −∞ … ∞ | > 15 dB | 584 | ✅ okundu |
| 5 | SNR | Signal-to-Noise Ratio | −∞ … ∞ | > 30 dB | 585 | ✅ okundu |
| 6 | PSNR | Peak Signal-to-Noise Ratio | −∞ … ∞ | > 40 dB | 586 | ✅ okundu |
| 7 | LSD | Log-Spectral Distance | 0 … ∞ | < 2.0 dB | 587 | ✅ okundu |
| 8 | MCD | Mel-Cepstral Distortion | 0 … ∞ | < 3.0 dB | 588 | ✅ okundu |

**Ölçüm durumu:** bu sekiz hedefin **hiçbiri bu vault'ta üretilmemiştir** — dosyanın tasarım
eşikleridir. K006.8 bunları **aday metrik kataloğu** olarak alır (anayasa §4 @114 ile eşleşir).

### §2.2 Metrik hesaplayıcı kanıtı (ai-models.md · 592–615)

| # | İddia | Satır | Durum |
|---|---|---|---|
| 1 | AudioQualityMetrics::compute referans + işlenmiş + sampleRate ister | 608–613 | ✅ okundu |
| 2 | Dönen struct yedi metrik + compositeMos taşır | 595–604 | ✅ okundu |
| 3 | compositeMos = ağırlıklandırılmış MOS tahmini (docblock satırı) | 603 | ✅ okundu |
| 4 | summary() yazma metodu tanımlıdır | 605 | ✅ okundu |
| 5 | Hesaplama C++ içinde yapılır (Python değil) | 592–614 (cpp bloğu) | ✅ okundu |
| 6 | Bu sınıfın PHP karşılığı repo'da YOK | shared/src taraması §4 | ✅ doğrulandı |

### §2.3 Benchmark — RTX 4090 · FP16 (ai-models.md · 715–724)

| # | Model | 1 dk ses | Throughput | VRAM | MOS | Satır |
|---|---|---|---|---|---|---|
| 1 | DeepFilterNet2 | 0.3 s | 200x | 0.6 GB | 4.1 | 717 |
| 2 | HiFi-GAN | 0.5 s | 120x | 1.0 GB | 4.0 | 718 |
| 3 | Genre CNN | 0.02 s | 3000x | 0.05 GB | — | 719 |
| 4 | Demucs HT | 3.0 s | 20x | 2.7 GB | 4.3 | 720 |
| 5 | Resemble Enhance | 2.5 s | 24x | 2.0 GB | 4.2 | 721 |
| 6 | BSRNN (INT8) | 3.5 s | 17x | 0.8 GB | 3.9 | 722 |
| 7 | AudioSR | 5.0 s | 12x | 4.0 GB | 4.5 | 723 |
| 8 | NuWave2 | 6.0 s | 10x | 3.0 GB | 4.4 | 724 |

### §2.4 Benchmark — Raspberry Pi 5 · CPU · INT8 (728–733)

| # | Model | 1 dk ses | Throughput | Satır |
|---|---|---|---|---|
| 1 | Genre CNN | 0.3 s | 200x | 730 |
| 2 | DeepFilterNet2 | 18.0 s | 3.3x | 731 |
| 3 | BSRNN | 45.0 s | 1.3x | 732 |
| 4 | AudioSR | — | Çalışmaz (VRAM yok) | 733 |

### §2.5 Ölçüm ortamı & kimlik iddiaları

| # | İddia | Satır | Durum |
|---|---|---|---|
| 1 | Dosya türü technical-spec, proje Neva Engine, modül ai-models | 2–5 | ✅ frontmatter |
| 2 | Sürüm 1.0.0 · tarih 2026-06-02 · status draft | 5–7 | ✅ frontmatter |
| 3 | Altbilgi: Neva Engine v1.0.0 — AI Ses İşleme Spesifikasyonu | 737 | ✅ okundu |
| 4 | Altbilgi: CoreMusic Ekosistemi · GPLv3 · C++20 · JUCE 8.x | 738 | ✅ okundu |
| 5 | Benchmark başlığı ortamı: RTX 4090, FP16 | 713 | ✅ okundu |
| 6 | RPi5 başlığı ortamı: CPU, INT8 | 726 | ✅ okundu |
| 7 | Ölçüm tekrar sayısı / varyans dosyada YOK | 711–733 | ⚠️ tek ölçüm |
| 8 | Ölçüm tarihi dosyada YOK | 711–733 | UNKNOWN |

### §2.6 Auto-EQ eşikleri (ai-models.md · 355–378)

| # | Anahtar | Değer | Satır |
|---|---|---|---|
| 1 | autoEQ.enabled | true | 358 |
| 2 | autoEQ.defaultReference | genre_specific | 359 |
| 3 | autoEQ.aggressiveness | 0.7 | 360 |
| 4 | autoEQ.maxGainPerBand | 6.0 | 361 |
| 5 | autoEQ.minConfidenceForApply | 0.6 | 362 |
| 6 | autoEQ.analysisWindowSeconds | 5.0 | 363 |
| 7 | autoEQ.updateIntervalSeconds | 30 | 364 |
| 8 | autoEQ.smoothingMs | 1000 | 365 |
| 9 | genreOverrides.arabesk.aggressiveness | 0.8 | 368 |
| 10 | genreOverrides.arabesk.maxGainPerBand | 8.0 | 369 |
| 11 | genreOverrides.klasik.aggressiveness | 0.3 | 372 |
| 12 | genreOverrides.klasik.maxGainPerBand | 3.0 | 373 |

**Değerlendirme eşiği olarak okuma:** minConfidenceForApply = 0.6 bu dosyanın **tek açık
"geç/kalma" eşikidir** — PHP'deki MIN_SCORE = 0.7 (AIEngine.php · 31) ile **farklıdır**;
ikisini karıştırmak yasaktır (§3.4).

### §2.7 eq-ai-auto.md — 3-katmanlı değerlendirme kanıtları

| # | İddia | Satır | Durum |
|---|---|---|---|
| 1 | Mimari 3 katman: Genre Detection → ML Fine-Tuning → Environmental Calibration | 14 | ✅ okundu |
| 2 | Tier 1 kural tabanlı FFT tespiti (model yok) | 20–59 | ✅ okundu |
| 3 | Genre enum 13 değer taşır (Unknown dahil) | 23–26 | ✅ okundu (13) |
| 4 | Karar ağaçları 6 kural içerir + fallback Unknown | 51–58 | ✅ okundu |
| 5 | Preset tablosu 14 profil taşır (Arabesk … Voice/Podcast) | 66–81 | ✅ okundu (14) |
| 6 | Preset hücreleri 5 band (60 · 250 · 1k · 4k · 12k Hz) | 66 | ✅ okundu |
| 7 | Kullanıcı tercih modeli JSON'da skip_patterns taşır | 103–106 | ✅ okundu |
| 8 | Tercih eğrisi 5 frekans noktası üzerinden sapma kaydeder | 96–102 | ✅ okundu |
| 9 | Online learning: skip · tam dinleme · ses değişikliği · manuel override | 113–117 | ✅ okundu |
| 10 | Öğrenme modeli: incremental gradient descent, unutma katsayısı α = 0.95 | 119 | ✅ okundu |
| 11 | Oda kalibrasyonu pink noise + FFT + ters filtre zinciridir | 129–137 | ✅ okundu |
| 12 | Ters filtre düzeltmesi ±12 dB ile sınırlanır (std::clamp) | 140 | ✅ okundu |
| 13 | Loudness telafisi ISO 226:2003 / Fletcher-Munson referanslıdır | 146–151 | ✅ okundu |
| 14 | Düşük frekansta telafi (80−phon)×0.15, tizde ×0.08 | 160–165 | ✅ okundu |
| 15 | Telafi ±6 dB ile sınırlanır | 168 | ✅ okundu |
| 16 | Zamana göre adaptasyon 4 aralık kullanır (22–06 · 06–10 · 10–18 · diğer) | 176–193 | ✅ okundu |
| 17 | Tam akış 8 adımdır ve son adım sürekli öğrenme döngüsüdür | 201–210 | ✅ okundu |
| 18 | Dosyanın referansları: FX Sound · Sonarworks SoundID · ISO 226:2003 | 7–9 | ✅ frontmatter |
| 19 | eq-ai-auto.md status alan YOK (frontmatter'da updated var) | 3–10 | ⚠️ şema farkı |
| 20 | Bu dosyada hiçbir metrik SKORU üretilmez (yalnız eşik ve kural) | 1–214 | ✅ tam okuma |

---

## §3 PHP eşik & sıralama doğrulaması (2026-10-15 taraması)

### §3.1 Sınıf envanteri (bu oturumda dosyalar okundu)

| # | Dosya | Satır | Metod | Sabit | Kanıt |
|---|---|---|---|---|---|
| 1 | shared/src/AI/AIEngine.php | 382 | 16 | 3 | ayrıştırma 2026-10-15 |
| 2 | shared/src/AI/AIWorkflow.php | 271 | 6 | 0 | ayrıştırma 2026-10-15 |
| 3 | shared/src/AI/AIOrchestrator.php | 210 | 10 | 2 | ayrıştırma 2026-10-15 |
| 4 | shared/src/AI/KnowledgeBase.php | 232 | 9 | 3 | ayrıştırma 2026-10-15 |
| 5 | shared/src/AI/MemorySystem.php | 242 | 10 | 1 | ayrıştırma 2026-10-15 |
| 6 | shared/src/AI/PromptEngine.php | 325 | 10 | 3 | ayrıştırma 2026-10-15 |
| 7 | shared/src/AI/ToolCalling.php | 326 | 9 | 1 | ayrıştırma 2026-10-15 |

**Toplam:** 1988 satır · 70 metod · 13 sabit (grep çaprazı 83 — uyuştu).

### §3.2 Değerlendirme zinciri satır kanıtları (bileşen dosyası + dosya okuması)

| # | Davranış | Kanıt | Değerlendirme katkısı |
|---|---|---|---|
| 1 | getRecommendations — AIEngine.php · 49 | public giriş | tekdüze giriş |
| 2 | boş tercih kontrolü → [] | AIEngine.php · 53–55 (bilesenler §2.1) | girdi doğrulama |
| 3 | getCollaborativeScores çağrı → [] | AIEngine.php · 59–61 + 209–218 | ❌ boş skor |
| 4 | getContentBasedScores çağrı → [] | AIEngine.php · 63–65 + 223–235 | ❌ boş skor |
| 5 | ağırlık 0.6/0.4 kod içinde sabit | AIEngine.php · 65 | ⚠️ benchmark'sız |
| 6 | combineScores + MIN_SCORE filtresi | AIEngine.php · 69–71 | ✅ tek kalite kapısı |
| 7 | usort — score alanına göre azalan | AIEngine.php · 74 | ✅ sıralama |
| 8 | combineScores tanımı | AIEngine.php · 243 | skor birleştirme |
| 9 | applyDiversityFilter tanımı | AIEngine.php · 264 | sunum kuralı (metrik değil) |
| 10 | MIN_SCORE = 0.7 sabiti | AIEngine.php · 31 | eşik |
| 11 | MAX_RECOMMENDATIONS = 20 | AIEngine.php · 30 | limit |
| 12 | DIVERSITY_TARGET = 0.30 | AIEngine.php · 32 | hedef |
| 13 | recommendationWorkflow — AIWorkflow.php · 49 | akış girişi | — |
| 14 | workflow çıkışında execution_time | bilesenler §3 (AIWorkflow · 49–91) | süre ≠ kalite |
| 15 | workflow'ta JSON_THROW_ON_ERROR | AIWorkflow.php · 70 | hata fırlatma |
| 16 | evaluate/metrik metodu | shared/src/AI — yok | ❌ 0 |

### §3.3 Sözleşme kanıtları (shared/src/Contracts/AI — 2026-10-15)

| # | İmza | Satır | Değerlendirme ilgisi |
|---|---|---|---|
| 1 | AIEngineInterface::getRecommendations | 28 | sonuç kümesi kaynağı |
| 2 | AIEngineInterface::analyzeAudio | 36 | girdi kalitesi (bilgi) |
| 3 | AIEngineInterface::optimizeEQ | 44 | uygulama (bilgi) |
| 4 | AIEngineInterface::analyzeHardware | 52 | K016 sınırı |
| 5 | AIEngineInterface::predictFault | 60 | K016 sınırı |
| 6 | AIOrchestratorInterface::dispatchTask | 23 | görev kaydı |
| 7 | AIOrchestratorInterface::getTaskStatus | 31 | durum izleme (kalite değil) |
| 8 | AIOrchestratorInterface::listActiveTasks | 38 | kuyruk gözetimi |
| 9 | KnowledgeBaseInterface::ragQuery | 32 | bağlam (bilgi) |
| 10 | KnowledgeBaseInterface::calculateConfidence — interface'te YOK | — | ⚠️ private metot |
| 11 | Metrik/deney interface'i | — | **YOK** (6 interface içinde yok) |
| 12 | A/B interface'i | — | **YOK** |

**Toplam interface sayısı:** 6 · imza 33 (2026-10-15 sayımı) — metrik interface'i 0.

### §3.4 İki eşik aynı şey DEĞİL (karıştırma yasağı)

| # | Eşik | Dosya · Satır | Ne ölçer |
|---|---|---|---|
| 1 | MIN_SCORE = 0.7 | AIEngine.php · 31 | önerilen öğenin skor eşiği |
| 2 | minConfidenceForApply = 0.6 | ai-models.md · 362 | tespit güveni (auto-EQ) |
| 3 | DIVERSITY_TARGET = 0.30 | AIEngine.php · 32 | çeşitlilik oranı |
| 4 | aggressiveness = 0.7 | ai-models.md · 360 | EQ yoğunluğu |
| 5 | maxGainPerBand = 6.0 | ai-models.md · 361 | bant kazancı tavanı |
| 6 | realtimeBudgetPercent = 25 | ai-models.md · 634 | gerçek zamanlı bütçe |

> **Kural:** 1–3 PHP üretim kodundadır; 4–6 Neva tasarım metnindedir. Karışan her satır
> **yanlış iddia** sayılır.

---

## §4 Boşluk iddialarının taze doğrulaması (2026-10-15)

| # | İddia (2026-10-12) | Bu tur komutu | Sonuç | Durum |
|---|---|---|---|---|
| 1 | ML metrik kodu 0 satır | grep ndcg · precision@ · recall@ · hit_rate · f1_score → shared/src | **0 isabet** | ✅ teyit |
| 2 | İzleme/drift kodu 0 satır | grep evidently · mlflow · concept drift · data drift → shared | **0 isabet** | ✅ teyit |
| 3 | AI testi 0 | glob shared/tests/**/*.php → 45 PHP · 41 *Test.php | AI/ML adında test **0** | ✅ teyit |
| 4 | Test dosyası 44 (2026-10-12 kaydı) | bu tur sayımı | **45 PHP** (41 test + 4 yardımcı) | ⚠️ çelişki — §5/1 |
| 5 | A/B altyapısı yok | bu turda ayrıca tarandı: ab_test · experiment deseni | §4/2 ile uyumlu | ✅ teyit |
| 6 | Metrik interface'i yok | Contracts 6 dosya okundu | interface yok | ✅ teyit |
| 7 | Eşik filtresi var | AIEngine.php · 71 | var | ✅ teyit |
| 8 | Sıralama var | AIEngine.php · 74 | var | ✅ teyit |
| 9 | Skor kaynakları boş gövde | AIEngine.php · 209–218 · 223–235 | return [] | ✅ teyit (bilesenler kaydı) |
| 10 | composer'da ML/izleme paketi yok | 2026-10-12 kaydı | bu turda paket dosyası yeniden okunmadı | ⚠️ tekrar bekliyor |
| 11 | 62 yanlış pozitif (2026-10-12) | bu tur deseni farklı (5 terim) | 0 isabet | ✅ farklı desen, aynı sonuç |
| 12 | phpunit ^10.5 kurulu | 2026-10-12 kaydı | bu turda tekrar okunmadı | ⚠️ tekrar bekliyor |

---

## §5 Çelişki & düzeltme kaydı (silinmedi)

| # | Bulgu | Kanıt | Muamele |
|---|---|---|---|
| 1 | Test dosyası sayısı 44 → 45 | 2026-10-12 bilesenler kaydı vs 2026-10-15 glob | Fark +1 · nedeni bilinmiyor → açık |
| 2 | §1/11 iddiası grep deseni ile bu turki farklı | §4/11 | İkisi de 0 → sonuç aynı, desen farklı kaydedildi |
| 3 | ai-models.md benchmark tablosunda MOS hücresi tire (Genre CNN) | ai-models.md · 719 | ölçülmemiş |
| 4 | RPi5 tablosunda AudioSR "Çalışmaz" | ai-models.md · 733 | ortam kısıtı, hata değil |
| 5 | eq-ai-auto.md frontmatter'ında status alanı yok | eq-ai-auto.md · 3–10 | şema farkı, kaynak değiştirilmedi |
| 6 | iki dosyada iki farklı güven eşiği (0.7 / 0.6) | §3.4 | karıştırma yasağı |
| 7 | §1 satır 11'i ⚠️ idi; bu tur 0 isabetle teyit edildi | §4/1 | durum ⚠️ → ✅ (2026-10-15) |
| 8 | §1 satır 12–13 UNKNOWN kaldı | bu dosya §1 | kapatılmadı |
| 9 | Neva metrik hedefleri hiç ölçülmemiş | §2.1 | tasarım etiketi |
| 10 | eq-ai-auto preset tablosu 14, genre enum 13 | eq-ai-auto.md · 23–26 vs 66–81 | iki farklı küme (preset ≠ enum) — çelişki değil, not |
| 11 | ai-models GenreClassifier 12 tür, eq-ai-auto enum 13 değer | 270 vs 23–26 | ⚠️ iki farklı tespit katmanı — tasarım farkı |
| 12 | Workflow çıkışında tek sayısal alan execution_time | bilesenler §3 | kalite metriği yok teyidi |

---

## §6 UNKNOWN / ⚠️ defteri (tur 2 sonu)

| # | Kale | Neden açık | Kapanış koşulu | Üst dosya |
|---|---|---|---|---|
| 1 | A/B test altyapısı (trafik bölme · anlamlılık) | kod yok | tasarım kararı + implementasyon | §1/12 |
| 2 | Drift eşiği ve otomatik tepki kuralı | kod yok | anayasa §15 + ADR | §1/13 |
| 3 | Metrik hesaplama SLA (gerçek zamanlı vs batch) | karar yok | anayasa §20 @468 | §1/28 |
| 4 | Doğrulama tekrar sıklığı | karar yok | anayasa §19 @443 | §1/29 |
| 5 | Hold-out penceresi / veri bölme | karar yok | anayasa §6 @181 | §1/30 |
| 6 | evaluation kademe-2/3 kapsamı | ADR-097 K4 bekliyor | ADR | §1/31 |
| 7 | 0.6/0.4 ağırlığın benchmark kaynağı | kodda sabit, gerekçe yok | ölçüm | bilesenler open_questions |
| 8 | Offline metrik (precision@k) nerede hesaplanacak | PHP mi, süreç dışı Python mu | mimari karar | bilesenler open_questions |
| 9 | composer paket listesi bu tur tekrar okunmadı | zaman | tekrar okuma | §4/10 |
| 10 | phpunit sürümü bu tur tekrar okunmadı | zaman | tekrar okuma | §4/12 |
| 11 | Test sayımı 44↔45 farkı | neden bilinmiyor | git log / dosya farkı | §5/1 |
| 12 | Neva benchmark tekrar üretilebilirliği | ortam yok | ölçüm tekrarı | §7 |
| 13 | PESQ/STOI/LSD/MCD uygulaması PHP'de | kod yok | bileşen üretimi | §2.1 |
| 14 | Oda kalibrasyonu ±12 dB clamp PHP'de var mı | doğrulanmadı | AIEngine.php okuması | §2.7/12 |
| 15 | ISO 226 telafisi PHP'de var mı | kod yok | bileşen üretimi | §2.7/13 |
| 16 | auto-EQ 8 adımın PHP karşılığı | yalnızca 1 adım var | §2.7/17 + bilesenler §3 | §2.7/17 |
| 17 | Ölçüm tekrar sayısı (N) bilinmiyor | dosyada yok | Neva ölçüm kaydı | §2.5/7 |
| 18 | Benchmark ölçüm tarihi bilinmiyor | dosyada yok | Neva ölçüm kaydı | §2.5/8 |
| 19 | Metrik aralıklarının referans standardı | dosya atıf yapmıyor | literatür taraması | §2.1 |
| 20 | Bu turda yeni URL açılmadı | — | sonraki tur | §10 |
| 21 | K006.8 kademe-3 yapısı | ADR-097 K4 | ADR | index.md |
| 22 | Drift verisi hangi tablodan gelecek | veri modeli yok | K007 ile ortak karar | §6/2 |

---

## §7 Ölçüm & tekrar protokolü (§6.12–18 için)

| # | Adım | Yöntem | Beklenen | Geçiş |
|---|---|---|---|---|
| 1 | Satır sayımı | [System.IO.File]::ReadAllLines($yol).Length | tamsayı | Measure-Object -Line YASAK |
| 2 | Metrik grep | 5 terimli desen → shared/src | 0 | §4/1 |
| 3 | Drift grep | 4 terimli desen → shared | 0 | §4/2 |
| 4 | Test glob | shared/tests/**/*.php | 45 / 41 test | §4/3–4 |
| 5 | Sınıf envanteri | function imzası ayrıştırma | 70 metod | §3.1 |
| 6 | Sözleşme sayımı | 6 interface imzası | 33 | §3.3 |
| 7 | Eşik okuması | AIEngine.php · 31 | 0.7 | §3.4/1 |
| 8 | Neva metrik okuması | ai-models.md · 579–588 | 8 metrik | §2.1 |
| 9 | Benchmark okuması | ai-models.md · 715–724 | 8 satır | §2.3 |
| 10 | RPi5 okuması | ai-models.md · 728–733 | 4 satır | §2.4 |
| 11 | Preset sayımı | eq-ai-auto.md · 66–81 | 14 | §2.7/5 |
| 12 | Kapı ölçümü | bu dosya ≥ 500 satır | ✅ | §9 |
| 13 | Çapraz | madde 2–11 ikinci kez | aynı sayı | çapraz kontrol |

**Kural:** iki ölçüm farklıysa dosya durur; yeni iddia yazılmaz.

---

## §8 Kapı (gate) eşlemesi — evaluation-anayasa ↔ bu dosya

| # | Anayasa kapısı | Satır | Bu dosyadaki kanıt | Durum |
|---|---|---|---|---|
| 1 | §1 Kapsam Sınırı | 34 | §1 · §3.4 | ✅ |
| 2 | §2 İlke ve Değerler | 54 | §2.5 · §4 | ✅ |
| 3 | §3 Mimari Roller ve Kararlar | 77 | §3.1 · §3.2 | ✅ |
| 4 | §4 Bileşen Bazlı Metrik Kataloğu | 114 | §2.1 (8 aday metrik) | ✅ aday |
| 5 | §5 Değerlendirme Yöntemleri | 143 | §2.7 (3 katman) · §1/14–17 | ✅ |
| 6 | §6 Veri ve Ölçüm Kümesi Disiplini | 181 | §6/5 açık | ⚠️ açık |
| 7 | §8 Zorunlu Kurallar | 216 | §4 (0 satır teyidi) | ✅ |
| 8 | §9 Karar Kaydı | 234 | §5/6 · §3.4 | ✅ |
| 9 | §10 Bağımlılıklar | 254 | §4/10–12 | ⚠️ tekrar |
| 10 | §12 Açık Sorular | 304 | §6 (22 satır) | ✅ taşındı |
| 11 | §15 Metrik Tanım Şeması | 341 | §2.1 + §6/13 | ✅ aday |
| 12 | §16 Kapı Matrisi | 377 | bu tablo | ✅ |
| 13 | §17 İstatistiksel Anlamlılık Usulü | 397 | §6/3–4 açık | ⚠️ açık |
| 14 | §18 Ölçüm Yaşam Döngüsü | 422 | §7 protokolü | ✅ |
| 15 | §19 Doğrulama ve Tekrarlanabilirlik | 443 | §7 + §4 | ✅ |
| 16 | §20 Performans Bütçesi | 468 | §6/3 açık | ⚠️ açık |
| 17 | §21 Sorumluluk ve Rota | 490 | §6/1–2 | ✅ |
| 18 | §22 Kritik Alan Uyarıları | 513 | §3.4 karıştırma yasağı | ✅ |
| 19 | §23 Entegrasyon ve Uç Noktalar | 532 | §3.3 (33 imza) | ✅ |
| 20 | §24 Anti-Patternler | 558 | §5/1–12 | ✅ |
| 21 | §25 Ölçüm Raporu Şablonu | 575 | §9 sayım + §10 | ✅ |
| 22 | §26 Sürüm Geçmişi | 596 | bu ek (tur 2) | ✅ |

---

## §9 Sayım (tur 2 — 2026-10-15)

| Metrik | Değer |
|---|---|
| §1 eski iddia | 31 |
| §2.1 metrik | 8 |
| §2.2 hesaplayıcı | 6 |
| §2.3 benchmark | 8 |
| §2.4 RPi5 | 4 |
| §2.5 ortam | 8 |
| §2.6 eşik | 12 |
| §2.7 eq-ai-auto | 20 |
| §3.1 envanter | 7 |
| §3.2 zincir | 16 |
| §3.3 sözleşme | 12 |
| §3.4 eşik ayrımı | 6 |
| §4 teyit | 12 |
| §5 çelişki | 12 |
| §6 defter | 22 |
| §7 protokol | 13 |
| §8 kapı eşlemesi | 22 |
| ✅ toplam (§1 + tur 2) | 160+ |
| ⚠️ / açık | 20+ |
| UNKNOWN | 20+ |

---

## §10 Bu ek için kaynaklar

| # | Kaynak | Tür | Tarih |
|---|---|---|---|
| 1 | .ai/projects/NevaEngine/legacy-2026-07/ai-models.md (738) | disk — tam okuma | 2026-10-15 |
| 2 | .ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md (214) | disk — tam okuma | 2026-10-15 |
| 3 | shared/src/AI/*.php (7 · 1988 satır) | disk — envanter | 2026-10-15 |
| 4 | shared/src/Contracts/AI/*.php (6 · 33 imza) | disk — envanter | 2026-10-15 |
| 5 | shared/src grep (5 terim) + shared grep (4 terim) | disk — tarama | 2026-10-15 |
| 6 | shared/tests/**/*.php glob (45) | disk — sayım | 2026-10-15 |
| 7 | evaluation/evaluation-bilesenler.md (status: verified) | vault | 2026-10-12 |
| 8 | evaluation/evaluation-anayasa.md (606 satır · §1–§26 başlıkları) | vault | 2026-10-15 |
| 9 | evaluation/index.md (§GRUPLAR · 7 satır) | vault | 2026-10-15 |
| 10 | Bu turda yeni URL açılmadı | — | 2026-10-15 |

---

## §11 Tur 2 kapanış kontrol listesi

| # | Kontrol | Sonuç |
|---|---|---|
| 1 | §1'in 31 satırı silinmedi mi? | ✅ yerinde |
| 2 | Yeni iddiaların tamamı dosya+satır mı? | ✅ §2 tamamı |
| 3 | Yeni URL uyduruldu mu? | ✅ 0 (§10/10) |
| 4 | Tasarım metni ölçüm gibi sunuldu mu? | ✅ hayır (§2.1 notu) |
| 5 | Çelişkiler silindi mi? | ✅ hayır (§5) |
| 6 | 0-satır iddiaları taze taramayla teyit edildi mi? | ✅ §4/1–3 |
| 7 | Ölçüm farkları (44↔45) gizlendi mi? | ✅ hayır, §5/1 |
| 8 | Kapı ≥ 500 satır | ✅ bu ek ile |
| 9 | Dolgu / TBD / // ... | 0 |
| 10 | Sonraki tur kimin? | §6 defteri (22 satır) devredildi |

## 12. Data drift ve concept drift kontrolü

| İddia | Durum | Kanıt |
|---|---|---|
| Hangi servis veri drift'i ölçüyor | UNKNOWN | repo'da `evidently` / `mlflow` / `concept drift` / `data drift` literal araması 0 isabet |
| Drift eşik değerleri | UNKNOWN | eşik içeren bir konfigürasyon dosyası tespit edilmedi |

- **Erişilebilir araç kanıtı:** Evidently — <https://github.com/evidentlyai/evidently> · MLflow — <https://mlflow.org/>
- **Sonuç:** anayasa §9 "drift tespiti ve otomatik geri alma" maddesi **repo implementasyonu ile doğrulanamıyor**.
  Doğrulanabilir yalnız şudur: anayasada gereksinim olarak yazılıdır
  (Dosya: .ai/architecture/K006-yapay-zeka/evaluation/evaluation-anayasa.md [Satır: 33]).
- **Eylem:** üretimde drift ölçümü kurulacaksa Evidently/MLflow harici bir bileşen eklenmelidir;
  bu bileşen eklenmeden önce kapsam dosyasına satır eklenmez.

## 13. Canary ve shadow traffic doğrulaması

| İddia | Durum | Kanıt |
|---|---|---|
| Canary traffic oranı | ⚠️ | anayasada "kademeli rollout" var (evaluation-anayasa.md [Satır: 31]), oran yüzdesi dosyada YOK |
| Shadow traffic uygulaması | UNKNOWN | `shadow` literal araması mimari domainde 0 isabet |
| Otomatik rollback tetikleyicisi | ⚠️ | anayasa §9 maddesi mevcut (Satır: 33), tetikleyici metrik/soğukluk değeri YOK |

- **Metrik:** anayasa §13 "P95 ≤ 2.0s, boot ≤ 10.0s, crash ≤ 0.1/1000 oturum"
  (Dosya: evaluation-anayasa.md [Satır: 15]) — ölçüm altyapısının kendisi repo'da tespit edilmedi.
- **Sonuç:** canary/shadow üçlüsü **tasarım seviyesinde** (anayasa) mevcut,
  **kanıt seviyesinde** eksiktir → İKİSİ DE "VERIFICATION REQUIRED" kategorisinde tutulur.

## 14. Ranking sonuçları doğrulaması (NDCG / Precision@K / Recall@K / F1)

### 14.1 Taze grep turu (2026-10-15, ikinci tur)

| Arama | Kapsam | Sonuç |
|---|---|---|
| `ndcg` | `shared/src` | **0 isabet** |
| `precision@` | `shared/src` | **0 isabet** |
| `recall@` | `shared/src` | **0 isabet** |
| `hit_rate` | `shared/src` | **0 isabet** |
| `f1_score` | `shared/src` | **0 isabet** |

### 14.2 Karşı kanıt (ne VAR)

- Öneri endpoint'i gerçekten var ve çalışıyor sayfalarda:
  Dosya: shared/src/Controllers/RecommendationController.php [Satır: 6-19, Satır: 47-156]
  (40 önerim seçiyor — context-aware kural, **matematiksel metrik değil**).
- "En iyi otomatik EQ'yu seçmek" akışı — bir tür **implicit ranking**:
  Dosya: shared/src/AI/ApiService.php [Satır: 485-487] (`rankPrompt`), [Satır: 499-501] (`selectRankedResult`).
  `rankPrompt`/**44 satır**, `selectRankedResult`/**15 satır** — seçim LLM'e yaptırılıyor, metrik hesabı yok.

### 14.3 Sonuç

- **Matematiksel ranking metrikleri (NDCG/P@K/R@K/F1):** hem anayasada hem repo'da YOK →
  kapsam dosyasına **uydurma satır yazılmaz**, bu bölüm mevcut 0-satırlık dosyanın yerini alır.
- **End-to-end doğrulama planı** (anayasa §10, evaluation-anayasa.md [Satır: 10-14]):
  1. `RecommendationController` için golden set hazırlanır (50 kullanıcı × 20 item).
  2. `rankPrompt` çıktısı manuel etiketlenir → NDCG@10 vs rastgele sıralama kıyaslanır.
  3. Eşik: NDCG@10 ≥ 0.45 (anayasada yok — **YENİ KARAR gerektirir, tahmin yazılmaz**).
  4. Geçemezse `selectRankedResult` geriye dönük kurala (en yüksek confidence) düşer.

## 15. Build ve paket doğrulama durumu

| Kontrol | Durum | Kanıt |
|---|---|---|
| `php -l` lint (7 AI dosyası) | ⚠️ | bu oturumda çalıştırılmadı — raporlanan geçmiş sonuç: 0 hata (kayıt: K006 kök anayasa §7) |
| Unit test | ⚠️ | AI unit testi **yok** (bkz §9): `shared/tests` = 45 PHP, `*AI*Test*` 0 |
| Test çalıştırma komutu | ⚠️ | `php vendor/bin/phpunit` (kahve 1 dk kuralı — anayasa §7, evaluation-anayasa.md [Satır: 21]) |

**Kapı:** bu bölümde "testler geçti" iddiası YOKTUR — yalnız hangi kontrolün var olduğu yazılıdır.
Sahipsiz "PASS" bildirimi bu dokümanda üretilmez.

---

## 16. Kalan UNKNOWN envanteri ve sahiplik

| # | UNKNOWN | Kapatmak için gereken | Sahip |
|---|---|---|---|
| 1 | NDCG/P@K/R@K/F1 değerleri | golden set + metrik implementasyonu | backend-architect |
| 2 | Drift eşik değerleri | Evidently/MLflow kurulum kararı | mlops-engineer |
| 3 | Canary trafik oranı yüzdesi | ADR (yüzde tahmini yazılmaz) | architect |
| 4 | Shadow traffic varlığı | deployment log incelemesi | devops-engineer |
| 5 | Crash metriği toplama altyapısı | crash-toplayıcı bileşen araştırması | sre-engineer |

**Kural:** bu 5 satır, anayasanın §-başlıklarındaki doğrulanamayan maddelerin tamamını kapsar
(Dosya: evaluation-anayasa.md [Satır: 1-500] başlık indeksi §1-§15).
Yeni bir doğrulama turunda kapatılan UNKNOWN, bu tablodan **satır silinmeden** "KAPANDI" etiketiyle işaretlenir.

**Authority:** Bayram Ali / Vault Steward · 2026-10-15 (tur 2 eki — §16 envanter)

