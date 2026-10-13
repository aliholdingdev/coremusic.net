---
node_id: K006.8-evaluation-web-arastirma
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "yapay-zeka-web-arastirma.md §2 — 32 doğrulanmış URL havuzu (2026-10-12)"
  - "shared/src/AI/AIEngine.php L49–77 · L209–264 (disk okuma 2026-10-12)"
  - "shared/composer.json (grep evidently|mlflow|pytest → 0 eşleşme, 2026-10-12)"
validation: "2026-10-12 · 9 benzersiz URL havuzdan, iddialar sayfa metniyle · status=verified"
open_questions:
  - "scikit-learn 1.9.0 sürümü doc sayfasından okundu; Python ortamı repo'da yok → kurulum kararı UNKNOWN"
  - "Evidently'in drift/eval panelleri PHP tarafına nasıl taşınır → süreç/dil sınırı ⚠️ VERIFICATION REQUIRED"
group: kanit
---

# K006.8-evaluation — Web Araştırması

> **Yöntem:** K006 kök taramasının (`yapay-zeka-web-arastirma.md` §2) **değerlendirme/metrik**
> ayağı. Tüm URL'ler 2026-10-12 kök taramasında doğrulanmıştır; yeni URL üretilmedi —
> yalnızca havuz §2 kullanıldı.

---

## §1 Tarama Planı

| Soru | Neden | Havuz kümesi |
|---|---|---|
| ML değerlendirme/drift izleme araçları hangileri? | K006.8 kapsamı "önerinin gerçekten iyi olup olmadığını ölçmek" — repo'da karşılığı yok, aday gerekiyor | §2.5 (10) |
| Öneri sistemi değerlendirme metrikleri neler? | hybrid skorlamada (0.6/0.4) kalite ölçütü | §2.6 (20 · 24 · 25) |
| Model/metrik izleme altyapısı var mı? | eğitim/izleme katmanı adayı | §2.2 (7 · 9) |
| Değerlendirme testi nasıl yazılır? | K006.9 ile ortak | §2.8 (32) · §2.3 (11) · §2.1 (1) |

---

## §2 Doğrulanmış Kaynaklar

| # | URL | Sayfadan çıkarılan iddia | İlgili K006.8 madde |
|---|---|---|---|
| 1 | https://www.evidentlyai.com/ | Evidently — ML izleme, değerlendirme, drift; **Apache 2.0**, **7.500+★**, **40m+ indirme** (sayfa metni) | drift/eval izleme adayı |
| 2 | https://scikit-learn.org/stable/ | scikit-learn — metrik, ölçekleme, veri ayrımı araçları; sürüm **1.9.0** (doc sayfası) | metrik kütüphanesi adayı |
| 3 | https://www.tensorflow.org/tensorboard/ | TensorBoard — eğitim/metrik görselleştirme | metrik görselleştirme |
| 4 | https://mlflow.org/ | MLflow — ML deneyi/yaşam döngüsü takibi | deney takibi |
| 5 | https://recommenders-team.github.io/recommenders/intro.html | 5 ana görev: popülerlik · içerik tabanlı · hibrit · **değerlendirme** · kişiselleştirme | değerlendirme çerçevesi |
| 6 | https://surprise.readthedocs.io/en/stable/ | Surprise dokümanları — SVD, KNN, **ölçüm metrikleri** | öneri metrikleri |
| 7 | https://github.com/NicolasHug/Surprise | Surprise — öneri algoritmaları + değerlendirme; **6.805★**, **BSD-3** | lisans/yıldız |
| 8 | https://docs.pytest.org/en/stable/ | pytest — Python test çerçevesi dokümanları | aday test altyapısı |
| 9 | https://onnxruntime.ai/docs/ | ONNX Runtime dokümanları — model çalışma zamanı ailesi | çıkarım sonrası doğrulama |

**Toplam K006.8 için: 9 benzersiz URL** (havuz §2.5 · §2.6 · §2.2 · §2.8 · §2.3 · §2.1).

---

## §3 İddia Ayrıntıları (sayfa metni dayanaklı)

| İddia | Dayanak URL | Ne söylüyor | K006.8'e etkisi |
|---|---|---|---|
| Drift/eval izleme Apache 2.0 ile ücretsiz | 1 | sayfa metni: lisans + indirme sayısı | lisans riski düşük — aday |
| Metrik üretimi sklearn ile yapılır | 2 | precision/recall benzeri sınıflandırma metrikleri sklearn araç setinde | etiketli veri varsa aday |
| Deney karşılaştırması MLflow ile yapılır | 4 | run takibi + metrik karşılaştırma | "hangi skor ağırlığı daha iyi" sorusunun aracı |
| Öneri değerlendirmesi ayrı bir görevdir | 5 | recommenders framework'ünde değerlendirme 5 ana görevden biri | K006.8'in kapsam tanımı web ile uyumlu |
| Offline metrikler (RMSE/MAE vb.) Surprise'da tanımlı | 6–7 | tahmin doğruluğu ölçümü kütüphane içinde | boş skorlayıcıların (L209–235) ölçüm adayı |
| Test katmanı pytest ile kurulur | 8 | Python tarafı test çalıştırıcısı | PHP'de karşılığı PHPUnit (repo kanıtı) |

---

## §4 K006.8'e Uygulanabilir Teknoloji Kararları (aday — kurulu değil)

| Aday | K006.8 karşılığı | Dayanak §2 | Repo durumu |
|---|---|---|---|
| Evidently | drift/eval raporu, veri kalitesi panosu | 1 | **kurulmadı** (composer.json grep → 0) |
| scikit-learn 1.9.0 | metrik hesaplama (precision/recall benzeri) | 2 | **kurulmadı** |
| TensorBoard | metrik görselleştirme | 3 | **kurulmadı** |
| MLflow | deney/parametre karşılaştırma | 4 | **kurulmadı** |
| recommenders değerlendirme | offline/online öneri metrikleri | 5 | **kurulmadı** |
| Surprise metrikleri | RMSE/MAE/precision@k | 6–7 | **kurulmadı** |
| pytest | Python testi | 8 | **kurulmadı** · PHP'de PHPUnit ^10.5 var |
| ONNX Runtime | çıkarım çıktısı doğrulama | 9 | **kurulmadı** |

> **Karar durumu:** hiçbiri karar değil **adaydır** (`yapay-zeka-kararlar.md` · `status: draft`).

---

## §5 Repo ile Çapraz Bağ

| Araştırma bulgusu | Repo karşılığı | Sonuç |
|---|---|---|
| sklearn metrik üretir | repo'da `ndcg|precision|recall|hit_rate|accuracy` grep'i → 62 isabet, **tamamı yanlış pozitif** (`$coreCalled` değişkeni, `Arbitrary Precision` bcmath metni) — gerçek ML metriği **0 satır** | ⚠️ eşleşme yok |
| Evidently drift ölçer | composer.json'da evidently **yok**; `shared/src/**` grep → 0 | ⚠️ boşluk |
| MLflow deney takibi yapar | composer.json'da mlflow **yok** | ⚠️ boşluk |
| Öneri kalitesi ölçülür | `getRecommendations` L49–77'de **MIN_SCORE filtresi (L71)** ve **usort (L74)** var — bu bir "eşik/sıralama" kontrolüdür, **metrik değildir** | 🔶 kısmi |
| Skor ağırlığı (hibrit) | `AIEngine` içinde **0.6/0.4** ağırlığı (L65) ve `combineScores` (L243) — ayarlanabilir ama **ölçülmemiş** | 🔶 kısmi |
| Skorlayıcılar | `getCollaborativeScores` L209–218 ve `getContentBasedScores` L223–235 **her koşulda `[]` döner** → değerlendirilecek skor üretimi bugün **imkânsız** | ⚠️ boşluk |
| Test altyapısı | composer.json **phpunit ^10.5** + **phpstan ^1.10**; `shared/tests/**/*.php` glob → **44 dosya**, AI ile ilgili **0** | ✅ altyapı var / ❌ AI testi yok |

**Sonuç:** K006.8'in repo karşılığı yalnızca **eşik/sıralama** seviyesindedir (MIN_SCORE · usort ·
sabit ağırlık). Ölçüm (metrik), izleme (drift) ve raporlama katmanı **tamamen 0 satırdır** —
bu K006.8'in ana bulgusudur.

---

## §6 Çelişki / Boşluk Kayıtları

| # | Kayıt | Durum |
|---|---|---|
| C1 | `recommenders.microsoft.com` — CRAWL_UNKNOWN_ERROR → `recommenders-team.github.io` ile değiştirildi | ✅ düzeltildi (havuz §6.2) |
| C2 | Kök kapsam "önerinin gerçekten iyi olup olmadığını ölçer" der; repo'da metrik kodu **0 satır** | ⚠️ **açık boşluk** (riskler R-03) |
| C3 | 0.6/0.4 ağırlıkları **nereyden geldiği belirsiz** (kanıt/benchmark yok) | ⚠️ VERIFICATION REQUIRED |
| C4 | sklearn/pytest Python; K006 PHP | 🔶 süreç/dil sınırı — UNKNOWN |
| C5 | TensorBoard/MLflow eğitim zincirini varsayar; repo'da eğitim (training) kodu **0 satır** | ⚠️ kapsam dışı zincir |

---

## §7 Sayım

| Metrik | Değer |
|---|---|
| Benzersiz URL | **9** |
| ✅ sayfa metniyle doğrulandı | 9 |
| Repo ile çapraz eşleşme | 2 (MIN_SCORE/usort · composer test altyapısı) |
| ⚠️ boşluk/çelişki | 5 (C2–C5 + §5 boş satırlar) |
| UNKNOWN | 2 (dil sınırı · ağırlık kaynağı) |
| Kurulu aday | 0 |

---

## §8 Kaynaklar

1. `yapay-zeka-web-arastirma.md` §2.1 · §2.2 · §2.3 · §2.5 · §2.6 · §2.8 — 2026-10-12.
2. `shared/src/AI/AIEngine.php` L49–77 · L209–264 — 2026-10-12 disk okuma.
3. `shared/composer.json` — grep (`evidently|mlflow|pytest|monolog`) → 0 eşleşme, 2026-10-12.
4. `shared/tests/**/*.php` glob → 44 dosya (AI testi 0), 2026-10-12.
5. `shared/src/**` ML metrik grep → yalnızca yanlış pozitifler, 2026-10-12.

---

`status: verified` · `validation: 2026-10-12 · 9/9 URL havuzdan doğrulandı, 2 çapraz eşleşme, 5 işaretli boşluk, 0 kurulu aday`
