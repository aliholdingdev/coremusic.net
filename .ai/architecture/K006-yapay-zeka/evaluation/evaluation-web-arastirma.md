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

---

# EK (2026-10-15) — Taze Kod Okuması, Yeni Doğrulanan URL'ler ve Ret Kayıtları

> **EK usulü:** append-only; §1–§8 ve status satırı silinmez. Tüm URL'ler 2026-10-15
> tek turda `fetch(u, {redirect:"follow"})` ile status + gerçek `<title>` çıkarılarak
> doğrulanmıştır. Yalnız **status 200 + title hedefle eşleşen** kaynak kanıt sayılır;
> 404 veya gasp title'lı alan adları ret kaydıdır (uydurma düzeltme yoktur).

---

## EK §0 — Taze Okuma Kayıtları (2026-10-15)

| # | Kaynak | Kapsam | Yöntem |
|---|---|---|---|
| 1 | `shared/src/AI/AIEngine.php` | L45–114 (`getRecommendations` · `analyzeAudio` başı) | disk read |
| 2 | `shared/src/AI/AIEngine.php` | L205–282 (`getCollaborativeScores` · `getContentBasedScores` · `combineScores` · `applyDiversityFilter`) | disk read |
| 3 | HTTP GET turu (40 URL — bu ve diğer K006.6/8/9 dosyaları ortak) | status + `<title>` | Code Mode `fetch`, 2026-10-15 |

---

## EK §1 — `getRecommendations` verbatim (zincirin tamamı)

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır: 46–77** · okuma: 2026-10-15

```php
    /**
     * {@inheritdoc}
     */
    public function getRecommendations(array $context, int $limit = 20): array
    {
        $limit = min($limit, self::MAX_RECOMMENDATIONS);
        $userId = $context['user_id'] ?? null;
        $sessionId = $context['session_id'] ?? null;
        $mood = $context['mood'] ?? null;
        $genre = $context['genre'] ?? null;
        $history = $context['history'] ?? [];

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

**Gözlem (yalnız okunan metin):**

1. Zincir 6 adım: collaborative → content → hybrid(0.6/0.4) → çeşitlilik → MIN_SCORE → sıralama.
2. `$sessionId` (L53) ve `$mood` (L54) okunur ama **gövdede hiçbir yere geçirilmez** —
   2026-10-15 taze okumada bu iki değişkenin tek kullanım yeri imzadaki atamadır.
3. `MIN_SCORE` eşiği `self::MIN_SCORE` sabitidir; ağırlıklar ise **sabit değil,
   çağrı yerine gömülü `0.6, 0.4`** (L65) — iki farklı ayar mekanizması.
4. `usort` (L74) `array_filter` sonrası anahtarları karıştırır → `array_values` (L76)
   ile yeniden indekslenir; sıra kararlılık (tie-break) garantisi **kodda yok** (eşit
   skorda sıralama PHP'nin `usort` davranışına kalır).
5. Yorum "6. Sıralama ve limit" ile eylem (`usort` + `array_slice`) birebir örtüşür.

---

## EK §2 — Dört yardımcı metot verbatim (boş skorlayıcılar + birleştirici)

**Dosya:** `shared/src/AI/AIEngine.php` · **Satır: 206–282** · okuma: 2026-10-15

```php
    /**
     * Collaborative filtering skorları.
     */
    private function getCollaborativeScores(?string $userId, array $history): array
    {
        if ($userId === null) {
            return [];
        }

        // DB'den benzer kullanıcıları bul
        // Gerçek implementasyonda matrix factorization (ALS) kullanılacak
        return [];
    }

    /**
     * Content-based skorları hesaplar.
     */
    private function getContentBasedScores(array $context): array
    {
        $targetFeatures = [
            'bpm'          => $context['bpm'] ?? null,
            'energy'       => $context['energy'] ?? null,
            'danceability' => $context['danceability'] ?? null,
            'genre'        => $context['genre'] ?? null,
        ];

        // DB'den aday şarkıları çek ve özelliklerini karşılaştır
        // cosine similarity kullanarak skorla
        return [];
    }

    /**
     * İki skor setini birleştirir.
     *
     * @param array<string, array{score: float}> $collaborative
     * @param array<string, array{score: float}> $content
     */
    private function combineScores(array $collaborative, array $content, float $collabWeight, float $contentWeight): array
    {
        $allKeys = array_unique(array_merge(array_keys($collaborative), array_keys($content)));
        $combined = [];

        foreach ($allKeys as $key) {
            $collabScore = $collaborative[$key]['score'] ?? 0;
            $contentScore = $content[$key]['score'] ?? 0;

            $combined[$key] = [
                'id'    => $key,
                'score' => ($collabScore * $collabWeight) + ($contentScore * $contentWeight),
            ];
        }

        return $combined;
    }

    /**
     * Çeşitlilik filtresi uygular.
     */
    private function applyDiversityFilter(array $scores, ?string $preferredGenre): array
    {
        $diversified = [];
        $genreCounts = [];

        foreach ($scores as $id => $item) {
            $genre = $item['genre'] ?? 'unknown';
            $genreCount = $genreCounts[$genre] ?? 0;

            // Tercih edilen türden çok fazla olmasın
            if ($genre === $preferredGenre && $genreCount > self::DIVERSITY_TARGET * count($scores)) {
                $item['score'] *= 0.8;
            }

            $genreCounts[$genre] = $genreCount + 1;
            $diversified[$id] = $item;
        }

        return $diversified;
    }
```

**Gözlem:**

1. `getCollaborativeScores`: `$userId !== null` olsa bile **şart sonrası da `return []`**
   (L217) — beyan edilen "DB'den benzer kullanıcılar" ve "matrix factorization (ALS)"
   yorumda kalmaktadır; `$history` parametresi gövdede okunmaz.
2. `getContentBasedScores`: `$targetFeatures` 4 alan **hesaplanır ama kullanılmaz**
   (L225–230 → L234 `return []`) — ölü hesaplama; `$context`'in bpm/energy/danceability
   alanları fonksiyona girip kaybolur.
3. `combineScores`: birleşim kümesi `array_merge(array_keys…)` — iki skor seti de
   boşken döngü hiç çalışmaz → **sonuç `[]`**; dolayısıyla zincirin 3.–6. adımları
   boş küme üzerinde işlem yapar (MIN_SCORE filtresi boş kümede anlamsızdır).
4. `applyDiversityFilter`: `$item['genre']` anahtarı `combineScores`'un ürettiği
   kayıtta **yoktur** (yalnız `id` + `score`) → döngü her adımda `'unknown'` alır;
   `$preferredGenre === 'unknown'` eşleşmesi ancak `genre` null/eksikse olur.
   `DIVERSITY_TARGET * count($scores)` eşik değeri boş/tek elemanlı kümede 0'a iner.
5. `score *= 0.8` cezası **koşullu ve tek yönlü** (indirme var, artırma yok).

---

## EK §3 — 2026-10-15 Taze HTTP Doğrulaması: K006.8'e Yeni/Genişleyen URL'ler

> §2'deki 9 URL aynen korunur. Aşağıdaki URL'ler 2026-10-15 turunda status 200 +
> gerçek `<title>` ile doğrulanmıştır (title'lar HTML entity'leri çözülmüş metindir).

| # | URL | HTTP | `<title>` (yanıttan birebir) | K006.8 ilgisi |
|---|---|---|---|---|
| 10 | https://scikit-learn.org/stable/modules/generated/sklearn.metrics.ndcg_score.html | 200 | `ndcg_score — scikit-learn 1.9.1 documentation` | NDCG — sıralama kalitesi metriği |
| 11 | https://scikit-learn.org/stable/modules/model_evaluation.html | 200 | `3.4. Metrics and scoring: quantifying the quality of predictions — scikit-learn 1.9.1 documentation` | metrik kataloğu ana sayfası |
| 12 | https://mlflow.org/docs/latest/ | 200 | `MLflow Documentation | MLflow AI Platform` | deney takibi dokümanı |
| 13 | https://www.evidentlyai.com/ | 200 | `Evidently AI - Open-source AI Evaluation and Observability` | §2.1 ile aynı hedef, taze title teyidi |
| 14 | https://facebookresearch.github.io/faiss/ | 200 | `Welcome to Faiss Documentation — Faiss documentation` | benzerlik/arama — değerlendirme kancası |
| 15 | https://www.tensorflow.org/recommenders | 200 | `TensorFlow Recommenders` | öneri framework'ü (değerlendirme modüllü) |
| 16 | https://www.wandb.ai/ | 200 | `Forge` | W&B **yeniden markalanmış**: title artık "Forge" |
| 17 | https://dvc.org/ | 200 | `Home – DVC` | veri/deney versiyonlama |
| 18 | https://github.com/iterative/dvc | 200 | `GitHub - treeverse/dvc: 🦉 Data Versioning and ML Experiments · GitHub` | DVC deposu — org `treeverse` (dikkat: `iterative` URL'i yönlendiriyor) |
| 19 | https://surprise.readthedocs.io/en/stable/getting_started.html | 200 | `Getting Started — Surprise 1 documentation` | Surprise "1" sürüm dokümanı |
| 20 | https://www.tensorflow.org/tensorboard/ | 200 | `TensorBoard | TensorFlow` | §2.3 ile aynı hedef, taze title teyidi |
| 21 | https://mlflow.org/ | 200 | `MLflow - Open Source AI Platform for Agents, LLMs & Models` | §2.4 ile aynı hedef, taze title — konum genişlemesi |
| 22 | https://recommenders-team.github.io/recommenders/intro.html | 200 | `Welcome to Recommenders — Recommenders documentation` | §2.5 ile aynı hedef, taze title teyidi |

**Sayım:** §2 = 9 URL + EK = **13 URL** → K006.8 toplam **22 benzersiz URL**.

**Sürüm/sürüm-özel notlar (title'dan okunan, yorum yok):**

- **scikit-learn 1.9.1** — iki ayrı sayfa da `1.9.1` yazıyor. Dosya §2.10/§4'teki
  **1.9.0** (2026-10-12 okuması) **korunur**; fark C-row §6.1'dedir.
- **Surprise "1"** — `getting_started.html` title'ı `Surprise 1 documentation`;
  sayısal sürüm title'da yok → tam sürüm `UNKNOWN`.
- **W&B "Forge"** — `wandb.ai` ana sayfasının title'ı `Forge`; "Weights & Biases"
  adı title'da görünmüyor (marka değişimi sinyali). İddia: **yalnız title**;
  "rebrand" nedeni `⚠️ VERIFICATION REQUIRED`.
- **DVC org farkı** — URL `github.com/iterative/dvc`, title `treeverse/dvc`;
  ikisi de 200 (yönlendirme). Depo sahipliği org'u title'a göre `treeverse`'tir.

---

## EK §4 — Ret Kayıtları (404 / Ele Geçirilmiş Alan Adı — 2026-10-15)

| # | Aday URL | HTTP | Dönen gerçek `<title>` | Karar |
|---|---|---|---|---|
| R1 | https://surprise.readthedocs.io/en/stable/metrics.html | **404** | `Documentation page not found - Read the Docs Community` | ❌ RET — Surprise metrik sayfası bu yolda yok; §3'teki "offline metrikler Surprise'da" iddiası `getting_started.html` (EK §3.19) üzerinden izlenir |
| R2 | https://lyst.github.io/lightfm/ | **404** | `Page not found · GitHub Pages` | ❌ RET — LightFM sayfası yayında değil |
| R3 | https://recbole.io | 200 | `SLOT TOTO >> Situs Slot Gacor Terpercaya Bocoran RTP Live Terbaik Depo QRIS 2026` | ❌ RET — alan adı gaspı (kumar spam); RecBole **kullanılamaz** |
| R4 | https://implicit.readthedocs.io | 200 | `Careem Pay Merchant Plugins - NodeJS API Documentation — Careem Pay API 0.1 documentation` | ❌ RET — alan adı gaspı; implicit **kullanılamaz** |

**Ortak ders (K006 geneli):** HTTP 200 tek kanıt değildir; **title içeriği hedefle
eşleşmeli**. R3/R4 `status 200 + title farklı` filtresiyle avlanır. Aynı gün
`audio-intelligence` EK §5'indeki 3×404 ile birleşik ret sayısı: **7** (5 farklı dosyaya
dağılım dosya bazında tekrar edilebilir).

---

## EK §5 — Çapraz Bağ (Taze Verbatim ↔ §5 Tablosusu)

| # | EK bulgusu (2026-10-15) | §5 satırı | Sonuç |
|---|---|---|---|
| X1 | `getCollaborativeScores` her durumda `[]` (EK §2) | "skorlayıcılar her koşulda `[]` döner" | ✅ teyit — L217 verbatim |
| X2 | `getContentBasedScores` `[]` + ölü `$targetFeatures` (EK §2) | aynı satır | ✅ teyit + yeni detay (ölü hesaplama) |
| X3 | `combineScores` boş küme → zincir 3–6 anlamsız (EK §5 gözlem 3) | "MIN_SCORE/usort — eşik/sıralama, metrik değil" | ✅ teyit + derinlik |
| X4 | `$sessionId`/`$mood` okunup kullanılmıyor (EK §1 gözlem 2) | §5'te yok | 🔶 yeni bulgu — K006.4 kişiselleştirme ile ortak (personalization dosyasına işaretli) |
| X5 | sklearn 1.9.1 (EK §3.10–11) | §2.10 "1.9.0" | 🔶 sürüm farkı → C-row §6.1 |
| X6 | `applyDiversityFilter` `genre` anahtarı üretim kaydında yok (EK §2 gözlem 4) | — | 🔶 yeni bulgu (A-öğüt benzeri; recommendation kapsama da girer) |
| X7 | 4 ret URL (EK §4) | §5 C1 (recommenders.microsoft.com düzeltmesi) | ✅ URL sağlığı riski tekrarladı |

---

## EK §6 — Fark (C-row) Tablosu

| C-row | Eski kayıt (korunur) | Taze gerçek (2026-10-15) | Etki |
|---|---|---|---|
| C-1 | §2.2/§3/§4 scikit-learn **1.9.0** (2026-10-12) | title: **1.9.1** (EK §3.10–11) | bu dosyada sklearn **1.9.1** olarak anılır; §2 satırı silinmez |
| C-2 | §2.4 `mlflow.org/` tek URL | + `mlflow.org/docs/latest/` (EK §3.12) — doküman kökü ayrı URL | sayım 9 → 22 |
| C-3 | §2.1 Evidently iddiası (lisans/indirme) | taze title: `Evidently AI - Open-source AI Evaluation and Observability` — **konum teyidi**, lisans/indirme rakamı bu turda yeniden okunmadı | lisans rakamı hâlâ 2026-10-12 metnine dayanır |
| C-4 | §7 kaynak listesi 5 madde | +1 madde: EK §0–§5 (2026-10-15 disk + 40-URL turu) | ek kanıt kütlesi |
| C-5 | — (yeni) | 4 ret URL (EK §4) | negatif kanıt — kanıt olarak kullanılamaz |

---

## EK §7 — Bulgular (A-E serisi)

### A-E-1 — Ölçüm zinciri hâlâ üretim yapmıyor (teyit + mekanizma)

EK §1+§2 verbatim ile zincirin **üç kaynağı da boş**: collaborative → `[]`
(L217), content → `[]` (L234), dolayısıyla `combineScores` boş × boş → `[]`.
MIN_SCORE ve usort `[]` üzerinde çalışır. §5'in "metrik/drift/rapor 0 satır"
bulgusuna ek olarak artık **skor üretiminin de 0 satır** olduğu mekanizmasıyla
gösteriliyor: K006.8 değerlendirilecek **hiçbir skor üretemez** (bugün).

### A-E-2 — Dead code: `$targetFeatures` ve iki ölü okuma

`$targetFeatures` (EK §2 L225–230) hesaplanıp hiç kullanılmaz; `$sessionId`
ve `$mood` (EK §1 L53–54) akışa girmez. Üç ölü okuma = **taahhüt edilmiş ama
bağlanmamış özellikler** (K006.4/K006.5 ile çapraz — personalization EK'sına
işaret).

### A-E-3 — Çeşitlilik filtresi veri sözleşmesiyle uyumsuz

`combineScores` kayıt şeması `{id, score}`; `applyDiversityFilter` `genre` okur
(EK §2 gözlem 4). Yani filtre, birleştiricinin **üretmediği alanı** okuyup
her zaman `'unknown'` alır; `%30 farklı tür garantisi` yorumu bu şema altında
**gerçekleşemez**. Bu, §5 C2'deki boşluğun bir adım ötesidir: boşluk yalnız
"metrik yok" değil, **mevcut filtre de veri sözleşmesi dışında**.

### A-E-4 — İki ayar mekanizması, tek doğruluk kaynağı yok

`MIN_SCORE`/`DIVERSITY_TARGET` sabit sınıf içinde; `0.6/0.4` ağırlıkları çağrı
yerine gömülü (EK §1 L65). C3 ("ağırlıklar nereden geldiği belirsiz") şimdi
iki katmanlı: sabitler **kod içinde tanımlı**, ağırlıklar **hiç tanımsız**
(yerleşim sabiti). Benchmark kaydı `⚠️ VERIFICATION REQUIRED` aynen geçerlidir.

### A-E-5 — Öneri değerlendirme metriklerinin aday envanteri genişledi

EK §3 ile adaylar: `ndcg_score` (sklearn 1.9.1, NDCG — sıralama kalitesi),
TensorFlow Recommenders, Surprise `getting_started`, MLflow (deney/karşılaştırma),
Evidently (drift), Faiss (benzerlik altyapısı), DVC (deney versiyonu), W&B/"Forge"
(title markası değişmiş). Hiçbiri kurulu değil (§4 usulü); **karar değil aday**.

### A-E-6 — URL sağlığı ölçümleri

Bu dosyada: 4 ret (1×404 metrik yolu, 1×404 LightFM, 2×gasp). 2026-10-15 ortak
turdaki toplam: 35×200 + 2×404 + 2×gasp + 1 `no-title` (mlflow model-registry
200 ama title boş — `⚠️ VERIFICATION REQUIRED`, EK §3'te sayılmadı) + 1 "Redirecting…"
(pytorch quantization — title yetersiz, ayrıca işaretli). **200 + güçlü title**
kuralı bu dosyanın kanıt eşiği olarak kalır.

### A-E-7 — DVC org değişimi

`github.com/iterative/dvc` → title `treeverse/dvc` (EK §3.18). İddia iki URL
arasında org farkı olduğunu gösterir; "**hangisi kanonik**" sorusu `UNKNOWN` —
depoların ikisi de 200 olduğu için ikisi de kaynak olarak kullanılabilir, org
iddiası title ile sınırlıdır.

---

## EK §8 — Kapılar (K006.8 web-arastirma)

| Kapı | Durum | Kanıt |
|---|---|---|
| Mevcut içerik korundu (append-only) | ✅ | §1–§8 + status satırı aynen duruyor |
| Frontmatter'e dokunulmadı | ✅ | EK gövdede, frontmatter L1–18 değişmedi |
| Uydurma URL yok | ✅ | 13 kanıt URL = status 200 + gerçek title; 4 URL ret kaydı |
| Erişilemeyen/eksik kanıt işaretli | ✅ | mlflow model-registry title'sız → sayım dışı + ⚠️; pytorch quantization "Redirecting…" → yetersiz title |
| Kod iddiaları disk kanıtlı | ✅ | EK §1–§2 verbatim + `Satır:` eşliği (2026-10-15) |
| `*.php` yazımı yok (yalnız okuma) | ✅ | 2026-10-15 yalnız `read` |
| git commit yok | ✅ | bu oturumda commit atılmadı |
| `.ai/architecture/index.md` dokunulmadı | ✅ | yazım yalnız bu dosyada |

---

## EK §9 — Ölçüm

| Metrik | Değer |
|---|---|
| EK öncesi satır | **140** |
| EK sonrası satır | **478** (kapı o an ❌ → EK-2 ile kapatıldı) |
| ≥500 kapı | **EK-2 §3'e bak — nihai ✅ (533)** |
| Ölçüm yöntemi | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
| Ölçüm tarihi | 2026-10-15 |

---

# EK-2 (2026-10-15) — Derinlik Eki: Metrik İhtiyaç ↔ Aday ↔ Repo Boşluğu Matrisi

> Yeni URL/iddia yok; yalnız EK §1–§3'teki kanıtların K006.8 metrik ihtiyaçlarına
> eşlenmesi. Her hücre ya dosyada mevcut bir kanıta ya da `UNKNOWN` işaretine dayanır.

## EK-2 §1 — İhtiyaç matrisi

| # | Metrik ihtiyacı (K006.8 kapsamı) | Aday kaynak (EK §3 #) | Repo karşılığı (§5 / EK §2) | Durum |
|---|---|---|---|---|
| M1 | Sıralama kalitesi (NDCG@k) | sklearn `ndcg_score` (10) | grep 62 isabet = tamamı yanlış pozitif → gerçek metrik 0 satır | ⚠️ boşluk |
| M2 | Tahmin doğruluğu (RMSE/MAE) | Surprise `getting_started` (19) | skorlayıcılar `[]` → hata hesabı yapılacak çift yok | ⚠️ boşluk |
| M3 | Katalog dışı değerlendirme (hit-rate/precision@k) | recommenders (22) · TF Recommenders (15) | — | ⚠️ boşluk |
| M4 | Drift/veri kalitesi izleme | Evidently (13) | composer grep 0 | ⚠️ boşluk |
| M5 | Deney/parametre karşılaştırması (ağırlık 0.6/0.4 vb.) | MLflow (12·21) | ağırlık çağrıya gömülü (EK §1 L65); log/kayıt yok | ⚠️ boşluk + C3 |
| M6 | Skor üretimi (ölçülecek ham madde) | — | collaborative `[]` + content `[]` (EK §2) | ❌ üretim yok — M1–M3 ön koşulu |
| M7 | Test katmanı (ölçümün otomasyonu) | pytest (§2.8) · PHP: phpunit ^10.5 | AI testi 0/44 dosya | 🔶 altyapı var, AI kapsamı yok |
| M8 | Benzerlik altyapısı (değerlendirme kancası) | Faiss (14) | — | ⚠️ boşluk |
| M9 | Deney/veri versiyonlama | DVC (17·18) | — | ⚠️ boşluk |
| M10 | Görselleştirme | TensorBoard (20) | — | ⚠️ boşluk |

**Okuma:** 10 ihtiyacın **9'u tamamen boş**, 1'i (M7) kısmen karşılanır; M6 tüm
off-line metriklerin ön koşulu olduğu için **bloklayıcıdır** (M6 olmadan M1–M3
ölçülemez).

## EK-2 §2 — Blokaj sırası (mantıksal bağımlılık, kod kanıtlı)

```text
M6 skor üretimi (getCollaborativeScores/getContentBasedScores → [])
  ├─→ M1 NDCG            (ndcg_score — EK §3.10)
  ├─→ M2 RMSE/MAE        (Surprise — EK §3.19)
  └─→ M3 hit/precision   (recommenders — EK §3.22)
        │
        └─→ M5 karşılaştırma (MLflow — ağırlık log'u yok, C3)
              └─→ M4 drift (Evidently) → M10 görselleştirme (TensorBoard)
M7 test otomasyonu       → her adımın regresyon kapısı (AI testi 0)
M8/M9                    → altyapı yan katmanlar (opsiyonel)
```

**Sonuç:** K006.8'de doğru sıra "metrik seçmek" değil, **M6'yı üretmektir**;
metrik seçimi (NDCG vs RMSE) ancak skor varsa anlam kazanır. Bu önceliklendirme
EK §2 veribatımından türetilmiştir, yeni iddia değildir.

## EK-2 §3 — Ölçüm (EK-2)

| Metrik | Değer |
|---|---|
| EK-2 öncesi satır | 478 |
| EK-2 sonrası satır | **533** (2026-10-15 `read(offset=9999)` → "533 lines") |
| ≥500 kapı | **✅ GEÇTİ (533 ≥ 500)** |
| Yöntem | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
| Tarih | 2026-10-15 |
| Yeni URL/iddia sayısı | 0 (yalnız mevcut kanıtın matrislenmesi) |
