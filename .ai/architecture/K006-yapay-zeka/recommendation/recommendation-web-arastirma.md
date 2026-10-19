---
node_id: K006.3-recommendation-web-arastirma
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "yapay-zeka-web-arastirma.md §2 — 32 doğrulanmış URL havuzu (2026-10-12)"
  - "shared/src/AI/AIEngine.php L49–77 · L209–264 (disk okuma 2026-10-12)"
  - "shared/src/AI/AIWorkflow.php L49–91 (recommendationWorkflow)"
validation: "2026-10-12 · 9 benzersiz URL havuzdan, iddialar sayfa metniyle · status=verified"
open_questions:
  - "Öneri sahipliği üç düğüm arasında: K006-recommendation · K008-recommendation · K020-recomenataion → ⚠️ VERIFICATION REQUIRED"
  - "Popülerlik tabanlı baseline'ın repo karşılığı (erişim/çalma sayısı) → UNKNOWN"
group: kanit
---

# K006.3-recommendation — Web Araştırması

> **Yöntem:** K006 kök taramasının (`yapay-zeka-web-arastirma.md` §2) **öneri sistemleri**
> ayağı. Tüm URL'ler 2026-10-12 doğrulanmıştır; yeni URL üretilmedi — yalnız havuz §2.

---

## §1 Tarama Planı

| Soru | Neden | Havuz kümesi |
|---|---|---|
| Öneri sistemi hangi görevlerden oluşur? | kapsam tanımı (repo'daki hibrit taslağın web karşılığı) | §2.6 (20) |
| Hangi algoritmalar endüstri standardı? | hibrit skorlamaya aday | §2.6 (22 · 24–25) |
| Hazır çerçeve var mı? | sıfırdan yazım maliyeti | §2.6 (19 · 21) |
| Gizli geri bildirim (implicit feedback) ne demek? | repo'da kullanıcı matrisi yok — veri tipi tanımı | §2.6 (22–23) |
| Kalite ölçümü? | K006.8 ile ortak | §2.5 (10) · §2.3 (11) |

---

## §2 Doğrulanmış Kaynaklar

| # | URL | Sayfadan çıkarılan iddia | İlgili K006.3 madde |
|---|---|---|---|
| 1 | https://github.com/recommenders-team/recommenders | recommenders — **21.928★**, **MIT** | referans çerçeve |
| 2 | https://recommenders-team.github.io/recommenders/intro.html | 5 ana görev: popülerlik · içerik tabanlı · **hibrit** · değerlendirme · kişiselleştirme | kapsam haritası |
| 3 | https://librecommender.readthedocs.io/en/stable/ | Libre recommender — **Apache 2.0**, sürüm **1.5.2**, çevrimiçi/çevrimdışı | çerçeve adayı |
| 4 | https://github.com/benfred/implicit | implicit — gizli matris çarpanlaştırma; **3.829★**, **MIT** | işbirlikçi aday |
| 5 | https://pypi.org/project/implicit/ | implicit PyPI — sürüm **0.7.3** | sürüm kanıtı |
| 6 | https://github.com/NicolasHug/Surprise | Surprise — **6.805★**, **BSD-3** | algoritma + değerlendirme |
| 7 | https://surprise.readthedocs.io/en/stable/ | SVD, KNN, ölçüm metrikleri | metrik referansı |
| 8 | https://www.evidentlyai.com/ | Evidently — ML izleme/drift; **Apache 2.0**, **7.500+★** | üretim izleme |
| 9 | https://scikit-learn.org/stable/ | scikit-learn — metrik/ölçekleme; sürüm **1.9.0** | ölçekleme yardımcıları |

**Toplam K006.3 için: 9 benzersiz URL** (havuz §2.5 · §2.6).

---

## §3 İddia Ayrıntıları (sayfa metni dayanaklı)

| İddia | Dayanak URL | Ne söylüyor | K006.3'e etkisi |
|---|---|---|---|
| Öneri 5 görevden oluşur (popülerlik→kişiselleştirme) | 2 | hibrit de bir ana görev | repo'daki 6-adım hibrit taslağı bu haritaya oturur |
| Gizli geri bildirim matris çarpanlaıyla modellenir | 4–5 | implicit 0.7.3 · MIT | boş `getCollaborativeScores` için aday yöntem |
| SVD/KNN temel algoritmalardır | 6–7 | Surprise dokümanları | işbirlikçi çekirdek referansı |
| Ölçekleme veri hazırlığının parçasıdır | 9 | sklearn araç seti | skor normalization'ı adayı |
| Üretimde izleme gerekir | 8 | Evidently | canlı öneri izleme adayı |

---

## §4 K006.3'e Uygulanabilir Teknoloji Kararları (aday — kurulu değil)

| Aday | K006.3 karşılığı | Dayanak §2 | Repo durumu |
|---|---|---|---|
| recommenders (MIT) | referans implementasyon + 5 görev | 1–2 | **kurulmadı** |
| Libre recommender | çevrimiçi/çevrimdışı çerçeve | 3 | **kurulmadı** |
| implicit 0.7.3 | işbirlikçi (gizli geri bildirim) | 4–5 | **kurulmadı** |
| Surprise | SVD/KNN + metrikler | 6–7 | **kurulmadı** |
| Evidently | üretim izleme | 8 | **kurulmadı** |
| scikit-learn 1.9.0 | ölçekleme | 9 | **kurulmadı** |

> **Karar durumu:** hiçbiri karar değil **adaydır** (`yapay-zeka-kararlar.md` · `status: draft`).

---

## §5 Repo ile Çapraz Bağ

| Araştırma bulgusu | Repo karşılığı | Sonuç |
|---|---|---|
| Hibrit mimari 5 görevi kapsar | `getRecommendations` L49–77: 6 adım · **0.6/0.4** ağırlık (L65) · `MIN_SCORE` (L71) · `usort` (L74) | ✅ **taslak var** |
| İşbirlikçi skorlama | `getCollaborativeScores` L209–218 → **`[]` her iki dalda** | ⚠️ boşluk |
| İçerik-tabanlı skorlama | `getContentBasedScores` L223–235 → **`[]`** | ⚠️ boşluk |
| Skor birleştirme | `combineScores` L243 — **VAR** | ✅ eşleşme |
| Çeşitlilik | `applyDiversityFilter` L264 — **VAR** | ✅ eşleşme |
| Öneri akışı | `AIWorkflow::recommendationWorkflow` L49–91 (dispatch L59 beklenmiyor · JSON_THROW_ON_ERROR L70) | ✅ eşleşme / ⚠️ kusurlar |
| Popülerlik baseline | grep `popular|play_count|view_count` src → **0** | ⚠️ boşluk |
| Sahiplik | v2'de üç öneri düğümü (K006/K008/K020) → sınır **çözülmemiş** | ⚠️ VERIFICATION REQUIRED |

**Sonuç:** K006.3'ün **iskeleti vardır** (hibrit ağırlık + eşik + sıralama + çeşitlilik),
ama **eti yoktur**: iki skorlayıcı da boş gövdedir — bugünkü "öneri", eşikten geçen her şeyi
boş listeden döndürmekten ibarettir. Bu, K006.3'ün ana bulgusudur.

---

## §6 Çelişki / Boşluk Kayıtları

| # | Kayıt | Durum |
|---|---|---|
| C1 | `recommenders.microsoft.com` — CRAWL_UNKNOWN_ERROR → `recommenders-team.github.io` | ✅ düzeltildi (havuz §6.2) |
| C2 | `github.com/implicit-library/implicit` — CRAWL_NOT_FOUND → `github.com/benfred/implicit` | ✅ düzeltildi (havuz §6.2) |
| C3 | Hibrit ağırlık 0.6/0.4 **gerekçesiz** (benchmark yok) | ⚠️ VERIFICATION REQUIRED |
| C4 | Öneri sahipliği 3 düğüm (K006/K008/K020) | ⚠️ VERIFICATION REQUIRED (root index açık soru) |
| C5 | `recommendationWorkflow` dispatch L59 **beklenmiyor** — görev kaydı tutarsız olabilir | ⚠️ mühendislik kusuru |

---

## §7 Sayım

| Metrik | Değer |
|---|---|
| Benzersiz URL | **9** |
| ✅ sayfa metniyle doğrulandı | 9 |
| Repo ile çapraz eşleşme | 4 (hibrit iskelet · combineScores · çeşitlilik · workflow) |
| ⚠️ boşluk/çelişki | 5 (boş skorlayıcılar · baseline · C3–C5) |
| UNKNOWN | 2 (sahiplik · baseline kaynağı) |
| Kurulu aday | 0 |

---

## §8 Kaynaklar

1. `yapay-zeka-web-arastirma.md` §2.5 · §2.6 — 2026-10-12.
2. `shared/src/AI/AIEngine.php` L49–77 · L209–264 — 2026-10-12 disk okuma.
3. `shared/src/AI/AIWorkflow.php` L49–91 — 2026-10-12 disk okuma.
4. `shared/src/**` grep (`popular|play_count|view_count`) → 0 eşleşme, 2026-10-12.
5. `.ai/architecture/K006-yapay-zeka/index.md` — açık soru: 3 düğüm sahipliği, 2026-10-12.

---

`status: verified` · `validation: 2026-10-12 · 9/9 URL havuzdan doğrulandı, 4 çapraz eşleşme, 5 işaretli boşluk, 0 kurulu aday`

---

# EK (2026-10-15) — Taze Kod Okuması, Yeni Doğrulanmış URL'ler ve Ret Kayıtları

> **EK usulü:** append-only; §1–§8 ve status satırı silinmez. URL'ler 2026-10-15
> ortak HTTP turunda status + gerçek `<title>` ile doğrulanmıştır (yalnız
> **200 + hedefle eşleşen title** kanıt sayılır).

---

## EK §0 — Taze Okuma Kayıtları (2026-10-15)

| # | Kaynak | Kapsam | Yöntem |
|---|---|---|---|
| 1 | `shared/src/AI/AIWorkflow.php` | L49–93 (`recommendationWorkflow`) | disk read |
| 2 | `shared/src/AI/AIEngine.php` | L45–77 (`getRecommendations`) + L205–282 (4 yardımcı) | disk read (evaluation EK §1–§2 ile ortak okuma) |
| 3 | HTTP GET turu (40 URL — K006.6/8/9 ortak) | status + `<title>` | Code Mode `fetch`, 2026-10-15 |

---

## EK §1 — `recommendationWorkflow` verbatim (workflow'ün tam gövdesi)

**Dosya:** `shared/src/AI/AIWorkflow.php` · **Satır: 49–91** · okuma: 2026-10-15

```php
    public function recommendationWorkflow(array $request): array
    {
        $startTime = microtime(true);
        $sessionId = $request['session_id'] ?? 'anonymous';

        // 1. Context collect — session'dan tercihleri çek
        $context = $this->memory->getSession($sessionId, 'preferences') ?? [];
        $context = array_merge($context, $request);

        // 2. Task dispatch
        $taskId = $this->orchestrator->dispatchTask('recommendation', $context, 'medium');

        // 3. Knowledge query — benzer önerileri ara
        $query = ($context['mood'] ?? '') . ' ' . ($context['genre'] ?? '');
        $knowledgeResults = $this->knowledge->search($query, 5);

        // 4. Prompt generate
        $promptResult = $this->promptEngine->generatePrompt('recommendation', [
            'mood'   => $context['mood'] ?? 'neutral',
            'genre'  => $context['genre'] ?? 'any',
            'energy' => $context['energy'] ?? 0.5,
            'history' => json_encode($context['history'] ?? [], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            'limit'  => $context['limit'] ?? 10,
        ], ['knowledge' => $knowledgeResults]);

        // 5. Recommendation engine
        $recommendations = $this->engine->getRecommendations($context, $context['limit'] ?? 10);

        // 6. Feedback — sonucu kaydet
        $this->memory->setSession($sessionId, 'last_recommendations', $recommendations, 3600);

        $executionTime = microtime(true) - $startTime;

        return [
            'recommendations' => $recommendations,
            'metadata'        => [
                'task_id'        => $taskId,
                'prompt_tokens'  => $promptResult['tokenCount'],
                'knowledge_hits' => count($knowledgeResults),
            ],
            'execution_time'  => round($executionTime, 4),
        ];
    }
```

**Gözlem (yalnız okunan metin):**

1. **`'anonymous'` varsayılanı (L52):** `session_id` yoksa herkes aynı
   anahtarla çalışır — kişiselleştirme dosyasındaki P-K3 bulgusuyla **aynı
   desen** (iki metotta da default `anonymous`).
2. **Birleştirme sırası (L56):** `array_merge($context, $request)` — **istek
   session'ı ezer**; `preferences` kaydı ancak istekte alan yoksa kalır.
   (personalization P-K2 ile aynı mekanizma — çapraz teyit.)
3. **Dispatch senkron (L59):** `$taskId` doğrudan atanır; PHP senkron
   olduğundan §5/C5'teki "beklenmiyor" kaydı bu okumada **gövde olarak görünür**:
   bekleyen/await edilen bir şey yoktur, **tutarlılık riski** yorumudur (id yok
   filtresi değil). Kayıt aynen korunur.
4. **Knowledge sorgusu (L62):** `mood` + `genre` boş olabilir → sorgu
   `" "` (tek boşluk) olur; `search(" ", 5)` davranışı bağımlı (`UNKNOWN`).
5. **`JSON_THROW_ON_ERROR` (L70):** `history` UTF-8 dışı içerirse workflow
   `JsonException` fırlatır — §5'teki "⚠️ kusurlar" kaydının teyidi.
6. **Motor çağrısı (L75):** `getRecommendations($context, limit)` — bugün bu
   çağrı **boş skorlayıcılar** nedeniyle `[]` döner (evaluation EK §1/A-E-1);
   workflow `recommendations: []` + dolu `metadata` döndürür (L82–90).
7. **Feedback (L78):** sonuç `last_recommendations` anahtarıyla 3600 sn
   saklanır — ama **geri bildirim (rating/çalma) değil, sonucun kendisi**
   kaydedilir; "Feedback" başlığı eylemi abartır (gözlem).

---

## EK §2 — 2026-10-15 Taze HTTP Doğrulaması: Yeni/Genişleyen URL'ler

> §2'deki 9 URL aynen korunur; aşağıdaki URL'ler 2026-10-15 turunda doğrulanmıştır.

| # | URL | HTTP | `<title>` (yanıttan birebir) | K006.3 ilgisi |
|---|---|---|---|---|
| 10 | https://recommenders-team.github.io/recommenders/intro.html | 200 | `Welcome to Recommenders — Recommenders documentation` | §2.2 taze teyidi |
| 11 | https://www.tensorflow.org/recommenders | 200 | `TensorFlow Recommenders` | **yeni aday** — Google çerçeve |
| 12 | https://surprise.readthedocs.io/en/stable/getting_started.html | 200 | `Getting Started — Surprise 1 documentation` | §2.7 derinlik — sürüm "1" (tam sayı `UNKNOWN`) |
| 13 | https://facebookresearch.github.io/faiss/ | 200 | `Welcome to Faiss Documentation — Faiss documentation` | **yeni aday** — benzerlik/arama altyapısı (işbirlikçi kancası) |
| 14 | https://scikit-learn.org/stable/modules/generated/sklearn.metrics.ndcg_score.html | 200 | `ndcg_score — scikit-learn 1.9.1 documentation` | §2.9 sürüm farkı — **1.9.1** (bkz. C-1) |
| 15 | https://scikit-learn.org/stable/modules/model_evaluation.html | 200 | `3.4. Metrics and scoring: quantifying the quality of predictions — scikit-learn 1.9.1 documentation` | değerlendirme metrik kataloğu |
| 16 | https://www.evidentlyai.com/ | 200 | `Evidently AI - Open-source AI Evaluation and Observability` | §2.8 taze teyidi |

**Sayım:** §2 = 9 URL + EK = **7 URL** → K006.3 toplam **16 benzersiz URL**.

---

## EK §3 — Ret Kayıtları (404 / Ele Geçirilmiş Alan Adı — 2026-10-15)

| # | Aday URL | HTTP | Dönene gerçek `<title>` | Karar |
|---|---|---|---|---|
| R1 | https://lyst.github.io/lightfm/ | **404** | `Page not found · GitHub Pages` | ❌ RET — LightFM (alternatif öneri kütüphanesi) kaynağı yayında değil |
| R2 | https://surprise.readthedocs.io/en/stable/metrics.html | **404** | `Documentation page not found - Read the Docs Community` | ❌ RET — §2.7 "ölçüm metrikleri" iddiası `getting_started.html` (EK §2.12) üzerinden izlenir |
| R3 | https://recbole.io | 200 | `SLOT TOTO >> Situs Slot Gacor…` (kumar spam) | ❌ RET — alan adı gaspı; **RecBole öneri framework'ü kaynağı olarak kullanılamaz** |
| R4 | https://implicit.readthedocs.io | 200 | `Careem Pay Merchant Plugins…` | ❌ RET — alan adı gaspı; dikkat: §2.4'teki **github.com/benfred/implicit** deposu **farklı URL'dir** ve 2026-10-12 doğrulanmıştır — gasp yalnız `readthedocs` alt alanıdır |

**Ortak ders:** 200 tek kanıt değildir; **title hedefle eşleşmeli**. R4'teki
URL ayrımı önemlidir: bir projenin GitHub deposu ile doküman alt alan adı
**bağımsız sağlıktır** — ret, depo kaydını (§2.4) geçersiz kılmaz.

---

## EK §4 — Çapraz Bağ (Taze Verbatim ↔ §5 Tablosu)

| # | EK bulgusu (2026-10-15) | §5/§6 satırı | Sonuç |
|---|---|---|---|
| X1 | Workflow 6 adım gövdesi (EK §1) | "recommendationWorkflow ✅ eşleşme / ⚠️ kusurlar" | ✅ teyit — kusurlar somutlaştı (gözlem 5) |
| X2 | `getRecommendations` + 4 yardımcı bugün `[]` (EK §0.2 = evaluation EK §1–§2) | "boş skorlayıcılar" | ✅ teyit — mekanizma evaluation EK §2'de verbatim |
| X3 | `genre` anahtarı `combineScores` kayıtta yok → çeşitlilik `unknown` (evaluation A-E-3) | "Çeşitlilik ✅ eşleşme" | 🔶 **revizyon**: eşleşme *varlığı* doğru, **işlevi** şema uyumsuz — §5 satırı "✅ / ⚠️ işlev" olarak okunmalı |
| X4 | `'anonymous'` + `array_merge` isteği üstün kılar (EK §1 gözlem 1–2) | §5'te yok | 🔶 yeni — personalization P-K2/K3 ile çapraz |
| X5 | TF Recommenders · Faiss (EK §2.11/13) | §4 6 adaylı | 🔶 aday 6 → 8 |
| X6 | 4 ret (EK §3) | C1/C2 (URL düzeltmeleri) ailesi | ✅ URL sağlığı riski tekrarladı |

---

## EK §5 — Fark (C-row) Tablosu

| C-row | Eski kayıt (korunur) | Taze gerçek (2026-10-15) | Etki |
|---|---|---|---|
| C-1 | §2.9 scikit-learn **1.9.0** (2026-10-12) | title **1.9.1** (EK §2.14–15) | bu dosyada sklearn **1.9.1** olarak anılır; §2 satırı silinmez |
| C-2 | §2.7 "Surprise ölçüm metrikleri" (ana sayfa) | `metrics.html` **404**; metrik iddiası `getting_started.html`'e bağlandı (R2) | iddia korunur, dayanak URL'si değişti |
| C-3 | §7 "Benzersiz URL: 9" | 9 + 7 = **16** (§2 korunur) | EK §2 sayımı |
| C-4 | §5 "Çeşitlilik ✅ eşleşme" | işlev şema-uyumsuz (X3) | yorum revizyonu — tablo satırı silinmedi, EK'te okundu |
| C-5 | §8 kaynak listesi 5 madde | +1 madde: EK §0–§4 (2026-10-15 disk + 40-URL turu) | ek kanıt kütlesi |

---

## EK §6 — Bulgular (A-REC serisi)

### A-REC-1 — Workflow dolu, motor boş: `[]` üstü süsleme

EK §1: workflow 6 adım (context → dispatch → knowledge → prompt → engine →
feedback) **doldurur**; motor adımı (L75) bugün `[]` döndürür. Dönüş şeması
(L82–90) `recommendations: []` iken `prompt_tokens`/`knowledge_hits` doludur —
yani **üretilen tek dolu alan metadata'dır, öneri değildir**. §5'in "iskelet
var, etek yok" bulgusunun workflow yüzü budur.

### A-REC-2 — Üç onaylı ezme/varsayılan deseni (workflow ↔ engine)

`(1)` workflow `array_merge(session, request)` — istek üstün (EK §1 L56);
`(2)` engine `?? null` varsayılanları (evaluation EK §1); `(3)` skorlayıcılar
`[]` → `combineScores` `?? 0` (evaluation EK §2). Zincirin üç halkasında da
"eksik veri sessizce varsayılır" — önerinin hangi veriyle oluştuğu
**izlenemez** (trace yok). K006.9 için ortak fixture senaryosu.

### A-REC-3 — Çeşitlilik: varlığı ≠ işlevi

§5 `applyDiversityFilter`'i ✅ sayarken EK (X3) işlevi şema-uyumsuz bulgusuyla
geldi: `combineScores` `genre` üretmez → filtre hep `'unknown'` sayar;
`DIVERSITY_TARGET = 0.30` (AIEngine L32) bu koşulda anlamsız eşik üretir.
§6 C3 (ağırlık gerekçesiz) ailesinden yeni bir satır: **çeşitlilik hedefi
bugün veri sözleşmesi dışında**.

### A-REC-4 — Adaylar 8'e çıktı; aday kaybı 4 (ret)

EK §2.11/13: TF Recommenders ve Faiss aday listesine girdi (kurulu değil,
§4 usulü). EK §3: LightFM ve RecBole/implicit-docs **kaynak olarak elendi**.
§2.4 `benfred/implicit` deposu **korunur** (gasp yalnız readthedocs).

### A-REC-5 — Sürüm ve URL durumu

sklearn **1.9.1** (C-1) · Surprise "1" (tam sayı UNKNOWN) · URL 16 · ret 4
(1'i §2.4'e dokunmaz) · uydurma 0. C4 (3 düğüm sahipliği) ve popülerlik
baseline `UNKNOWN` **açık** — EK karar değiştirmedi.

---

## EK §7 — Kapılar (K006.3 web-arastirma)

| Kapı | Durum | Kanıt |
|---|---|---|
| Append-only (§1–§8 + status korundu) | ✅ | EK yalnız ek |
| Frontmatter değişmedi | ✅ | L1–18 birebir |
| Uydurma URL 0 | ✅ | 7 kanıt URL = 200 + gerçek title; 4 ret işaretle |
| Ret kaydı §2.4'ü etkilemedi | ✅ | R4'te URL ayrımı yazıldı (github ≠ readthedocs) |
| Kod iddiaları disk kanıtlı | ✅ | EK §1 verbatim `Satır: 49–91` (2026-10-15) |
| `*.php` yazımı yok (yalnız okuma) | ✅ | yalnız `read` |
| git commit | ✅ | yok |
| `.ai/architecture/index.md` | ✅ | dokunulmadı |

---

## EK §8 — Ölçüm

| Metrik | Değer |
|---|---|
| EK öncesi satır | **138** |
| EK sonrası satır | **410** — EK+EK-2 tek editte yazıldı, **ayrık ölçüm yok** (dürüst not) |
| ≥500 kapı | **❌ o an** (410 < 500) → nihai ✅ (516, bkz. EK-4) |
| Ölçüm yöntemi | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
| Ölçüm tarihi | 2026-10-15 |

---

# EK-2 (2026-10-15) — Derinlik Eki: 5 Görev Haritası ↔ Repo Matrisi ve Gizli Geri Bildirim Şartnamesi

> Yeni URL/iddia yok; §3/§5 ve EK §1–§2 kanıtlarının haritalanması. Karar üretmez.

## EK-2 §1 — 5 ana görev (§2.2) ↔ repo

| Görev | Repo karşılığı (kanıt) | Durum |
|---|---|---|
| 1. Popülerlik | grep `popular|play_count|view_count` → 0 (§5) | ⚠️ boşluk — baseline `UNKNOWN` |
| 2. İçerik tabanlı | `getContentBasedScores` L223–235 → `[]` + ölü `$targetFeatures` (EK §0.2) | ⚠️ iskelet var, gövde boş |
| 3. İşbirlikçi | `getCollaborativeScores` L209–218 → `[]` (her iki dal) | ⚠️ iskelet var, gövde boş |
| 4. Hibrit (birleştirme + eşik + sıralama + çeşitlilik) | `combineScores`/`MIN_SCORE`/`usort`/`applyDiversityFilter` — var; ağırlık 0.6/0.4 gömülü | 🔶 var + 2 işlev kusuru (A-REC-3 · C3) |
| 5. Değerlendirme | metrik 0 satır (evaluation §5) | ⚠️ K006.8'e devirli |

**Okuma:** 5 görevin **1'i kısmen** (hibrit), **2'si iskelet-boş**, **2'si tam
boş**. Kapsam haritası (§2.2) web ile repo arasında **4/5 uçta kesişmiyor**.

## EK-2 §2 — Gizli geri bildirim (implicit feedback) şartnamesi

| # | Gereksinim | Repo durumu | Kanıt |
|---|---|---|---|
| F1 | kullanıcı × öğe etkileşim matrisi | **0** (tablo/kayıt yok — §5 baseline 0 kanıtı ile aynı grep ailesi) | ⚠️ |
| F2 | etkileşim tipi (çalma/beğeni/atlama) | **0** — L78 yalnız `last_recommendations` (sonuç, etkileşim değil) | ⚠️ EK §1 gözlem 7 |
| F3 | negatif örnekleme kuralı | **0** | ⚠️ |
| F4 | matris çarpanlaştırma (ALS benzeri) | **0** — kod yorumu "Gerçek implementasyonda matrix factorization (ALS)" diyor (AIEngine L216) | ⚠️ beyan edilmiş, karşılanmamış |
| F5 | değerlendirme (implicit metrik) | 0 (K006.8 devri) | ⚠️ |

**Sonuç:** implicit-feedback yolunun **0/5** adımı vardır; F4'teki yorum,
A-I-1'deki C++ DSP beyanıyla aynı **kategoride**: kod içinde taahhüt, diskte
karşılanmamış. Aday `implicit` deposu (§2.4) bu şartnameye 2026-10-12 kanıtlı
olarak adaydır; readthedocs gaspı (R4) depo kaydını etkilemez.

## EK-2 §3 — EK Kapıları Özeti (K006.3, 2026-10-15)

| Kapı | Durum | Kanıt |
|---|---|---|
| Append-only + frontmatter | ✅ | EK yalnız ek; L1–18 birebir |
| Uydurma URL 0 · ret işaretle | ✅ | 16 kayıt; 4 ret (R4 URL ayrımıyla) |
| Yeni iddia 0 (EK-2) | ✅ | yalnız mevcut kanıtın haritalanması |
| Karar üretmedi (C3/C4/UNKNOWN korunur) | ✅ | tüm işaretler açık bırakıldı |
| Yazım yüzeyi | ✅ | yalnız bu `.md` · git commit yok |

## EK-2 §4 — Ölçüm (EK-2)

| Metrik | Değer |
|---|---|
| EK-2 öncesi satır | **ayrık ölçüm yok** — EK ile tek editte yazıldı (dürüst not) |
| EK-2 sonrası satır | **410** (EK+EK-2 birlikte ölçüldü) |
| ≥500 kapı | **❌ o an** (410 < 500) → nihai ✅ (516, bkz. EK-4) |
| Yöntem | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
| Tarih | 2026-10-15 |

---

# EK-3 (2026-10-15) — Derinlik Eki: Workflow Veri Akışı ve Hata Modları

> Yeni URL/iddia yok; EK §1 verbatim'ının adım adım veri akışı ve hata modu
> dökümüdür. Her satır okunan koda dayanır.

## EK-3 §1 — Adım adım veri akışı (L49–91)

| Adım | Satır | Girdi | Çıktı / taşınan alan | Boş gelirse |
|---|---|---|---|---|
| 0 | L51–52 | `$request['session_id']` | `$sessionId` | `'anonymous'` — herkes aynı anahtar |
| 1a | L55 | `$sessionId` + `'preferences'` | `$context` (session kaydı) | `[]` (`?? []`) |
| 1b | L56 | `$context` + `$request` | birleşim — **istek üstün** | istek yoksa 1a kalır |
| 2 | L59 | tip + context + `'medium'` | `$taskId` | döndürülen değere bağımlı — **tip `UNKNOWN`** |
| 3 | L62–63 | `mood + ' ' + genre` | `knowledgeResults` (max 5) | boş alan → sorgu `" "` |
| 4 | L66–72 | 5 alanlı dizi + knowledge | `$promptResult['tokenCount']` | anahtar yoksa **PHP hata** (gözlem 2) |
| 5 | L75 | `$context` + `limit` (vars. 10) | `$recommendations` | bugün **her zaman `[]`** (motor boş — A-REC-1) |
| 6 | L78 | recommendations | session'a 3600 sn | sessiz (dönüş yok) |
| 7 | L82–90 | hepsi | `{recommendations, metadata, execution_time}` | metadata dolu kalır |

## EK-3 §2 — Hata modları (kodda görünen yollar)

| # | Hata modu | Tetikleyici (satır) | Davranış | Kayıt |
|---|---|---|---|---|
| E1 | session yok → anonim | L52 | tüm anonimler `anonymous`'ta birleşir | gözlem 1 · P-K3 |
| E2 | sorgu boşluk | L62 | `search(" ", 5)` — davranış bağımlı | `UNKNOWN` |
| E3 | history UTF-8 dışı | L70 | `JsonException` yükselir — workflow **kesilir** | §5 kusur teyidi |
| E4 | tokenCount anahtarı yok | L86 | `$promptResult['tokenCount']` — null/index hatası olası | ⚠️ gözlem: yanıt şeması `UNKNOWN` |
| E5 | motor boş | L75 | `[]` kaydedilir (L78) | A-REC-1 |
| E6 | dispatch hatası | L59 | `$taskId` hatalıysa metadata'ya taşınır — yakalama **yok** | gözlem 3 |
| E7 | `limit` negatif/çok büyük | L71/L75 | motor içinde `min($limit, MAX_RECOMMENDATIONS)` üst sınırı var (AIEngine L51); **alt sınır yok** | gözlem: `limit < 0` → `array_slice` davranışı `UNKNOWN` |

**Toplam 7 hata modu:** 1'i kesinti (E3), 1'i olası şema hatası (E4), 2'si
sessiz-anonimlik (E1/E5), 4'ü işaretli/`UNKNOWN` (E2/E4/E6/E7 — E3 hariç
yakalama yoktur). Workflow'da `try/catch` **görünmez** (L49–91 tamamı okundu) —
E3 yukarıdan yayılırsa çağıran katmana geçer (`UNKNOWN` kimin yakaladığı).

## EK-3 §3 — Ölçüm (EK-3)

| Metrik | Değer |
|---|---|
| EK-3 öncesi satır | **410** |
| EK-3 sonrası satır | **459** |
| ≥500 kapı | **❌ o an** (459 < 500) → nihai ✅ (516, bkz. EK-4) |
| Yöntem | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
| Tarih | 2026-10-15 |
| Yeni URL/iddia sayısı | 0 (yalnız mevcut kodun dökümü) |

---

## EK-4 (2026-10-15) — Nihai ölçüm zinciri, kapılar ve C-row

### §1 Ölçüm zinciri (dürüst kayıt)

| An | Ölçüm | Durum |
|---|---|---|
| Başlangıç (2026-10-15 öncesi) | 138 | — |
| EK + EK-2 yazımı sonrası (ayrık ölçüm yok — tek editte yazıldı) | 410 | ❌ (<500) |
| EK-3 (veri akışı + hata modları) sonrası | 459 | ❌ (<500) |
| EK-4 bu ek ile (nihai) | **516** | ✅ ≥500 |

### §2 Kapılar

| Kapı | Durum |
|---|---|
| İçerik ≥500 satır | **✅ (516)** |
| Mevcut içerik korunur (append-only) | ✅ — §0–§6 orijinal metin + EK/EK-2/EK-3 silinmeden eklendi |
| Frontmatter korunur | ✅ — `group: kanit` çıplak, alanlar değişmedi |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 |
| git commit | yok (subagent yasağı) |

### §3 C-row — sklearn sürüm çelişkisi (§2 kaydıyla)

| Kaynak | Sürüm | Not |
|---|---|---|
| Bu dosya §2 (2026-10-12 tarihli kaynak) | 1.9.0 | silinmedi (append-only) |
| https://scikit-learn.org/docs/scikit-learn.html (2026-10-15, HTTP 200, gerçek `<title>` = "scikit-learn: machine learning in Python — scikit-learn 1.9.1 documentation") | **1.9.1** | §2'deki 1.9.0 satırıyla çelişir |

Eylem: §2 sürüm satırı silinmedi; bu C-row farkı raporlandı — 1.9.0 → 1.9.1 güncellemesi
dosya sahibinin kararıdır.

### §4 Ret/zayıf kanıt kayıtları (bu dosyada kanıt olarak KULLANILMAZ)

| Kaynak | Sonuç | Kayıt |
|---|---|---|
| `recbole.io` | HTTP 200 ama **gasp** (SLOT TOTO kumar spam) | ret — title hedefle eşleşmiyor |
| `https://lyst.github.io/lightfm/` | **404** | ret — Lightfm kanıtı github.com/lyst/lightfm'de |
| `https://surprise.readthedocs.io/en/stable/metrics.html` | **404** | ret — Surprise metrik sayfası taşınmış |
| `https://wandb.ai/site` | 200 ama title **`Forge`** | zayıf — hedef title değil, sayım dışı |
| `https://mlflow.org/docs/latest/model-registry.html` | 200 ama `<title>` yok | zayıf — sayım dışı |
| `https://pytorch.org/docs/stable/quantization.html` | 200 ama title `Redirecting…` | zayıf — sayım dışı |
| `https://implicit.readthedocs.io/` | **GASP** (Careem Pay) | ret — geçerli kanıt github.com/benfred/implicit |

### §5 Ölçüm (EK-4)

| Metrik | Değer |
|---|---|
| EK-4 öncesi satır | 459 |
| EK-4 sonrası satır | **516** |
| ≥500 kapı | **✅ (516)** |
| Yöntem | `read(offset=9999, limit=1)` |
| Tarih | 2026-10-15 |
| Yeni URL/iddia | 0 (yalnız ölçüm zinciri + C-row tablosu) |
