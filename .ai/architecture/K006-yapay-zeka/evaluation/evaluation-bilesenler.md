---
node_id: K006.8-evaluation-bilesenler
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "shared/src/AI/AIEngine.php L49–77 · L209–264 (2026-10-12 tam okuma)"
  - "shared/src/AI/AIWorkflow.php L49–91 (2026-10-12 tam okuma)"
  - "shared/src/Contracts/AI/AIEngineInterface.php L28 (getRecommendations sözleşmesi)"
validation: "2026-10-12 · tüm metot/satır grep ile doğrulandı; metrik/izleme servisi = 0 satır · status=verified"
open_questions:
  - "0.6/0.4 hibrit ağırlık hangi benchmark'tan geldi → ⚠️ VERIFICATION REQUIRED"
  - "Offline metrik (precision@k vb.) hesabı PHP'de mi, süreç dışındaki Python'da mı yapılacak → UNKNOWN"
group: mimari
---

# K006.8-evaluation — Bileşenler

> K006.8'in **gerçek** bileşen envanteri. Kural: var olan kod yazılır, olmayan "0 satır" olarak
> boşluk sayılır. Değerlendirme bugün **yalnızca eşik/sıralama** seviyesindedir — metrik,
> drift veya rapor bileşeni repoda **yoktur**.

---

## §1 Envanter Özeti

| Ölçüm | Değer | Kayıt |
|---|---|---|
| K006.8'e atfedilen ayrı sınıf/dosya | **0** | glob `shared/src/AI/*` — 7 PHP, hiçbiri "evaluation" adında değil |
| Değerlendirme işlevine katkı sağlayan metotlar | 4 (`getRecommendations` · `combineScores` · `applyDiversityFilter` · `getCollaborativeScores`/`getContentBasedScores` — son ikisi boş) | §2 |
| Metrik hesaplayan kod | **0 satır** | grep `ndcg|precision|recall|hit_rate|accuracy` → 62 isabet, hepsi yanlış pozitif (`$coreCalled`, `Arbitrary Precision`) |
| İzleme/drift kodu | **0 satır** | grep `evidently|mlflow|drift` → 0 (2026-10-12) |
| Sözleşme karşılığı | 1 imza (`AIEngineInterface::getRecommendations` L28) | §4 |
| Test altyapısı | phpunit ^10.5 + phpstan ^1.10 (**kurulu**) · AI testi **0** | composer.json · `shared/tests` glob 44 dosya |

---

## §2 Gerçek Kod Bileşenleri

### §2.1 `AIEngine::getRecommendations(array $userPreferences, array $context = []): array` — L49–77 (public)

| Satır | Davranış | "Değerlendirme" katkısı |
|---|---|---|
| 53–55 | boş tercih kontrolü → `[]` | girdi doğrulama |
| 59–61 | `getCollaborativeScores` → **`[]` döner** (L209–218) | ❌ skor yok |
| 63–65 | `getContentBasedScores` → **`[]` döner** (L223–235) + 0.6/0.4 ağırlık | ❌ skor yok |
| 69–71 | `combineScores` + `MIN_SCORE` eşiği ile filtreleme | ✅ **eşik kontrolü** (tek gerçek "kalite kapısı") |
| 74 | `usort` — `score` alanına göre azalan sıralama | ✅ sıralama doğruluğu |

**Sorumluluk:** K006.8'in tek gerçek kapısı. Amaç "kalite ölçmek" değil **eşik uygulamak**tır —
ölçüm (metrik) yoktur.

### §2.2 `AIEngine::combineScores(array $collaborative, array $contentBased): array` — L243 (private)

| Satır | Davranış |
|---|---|
| 245–260 (yaklaşık bölge) | iki skor kümesini birleştirir; ağırlıklandırma getRecommendations içinde sabit (L65) |

**Kayıt:** ağırlık (0.6/0.4) **kod içinde sabittir**; dışarıdan yapılandırılmaz, benchmark ile
gerekçelendirilmemiştir (C3 · açık soru).

### §2.3 `AIEngine::getCollaborativeScores` L209–218 · `getContentBasedScores` L223–235 — **boş gövde**

| Metot | Davranış |
|---|---|
| `getCollaborativeScores` | iki dalda da `return [];` — kullanıcı-matris verisi yok |
| `getContentBasedScores` | `return [];` — özellik/etiket veri kaynağı yok |

**Bulgu:** değerlendirme zincirinin girdisi (skorlar) **her koşulda boştur**; bu nedenle
`combineScores`/`MIN_SCORE` bugün asla anlamlı bir küme üzerinde çalışmaz (karakteristik test
önceliği — K006.9).

### §2.4 `AIEngine::applyDiversityFilter(array $recommendations): array` — L264 (private)

| Satır | Davranış |
|---|---|
| 266–285 (yaklaşık bölge) | listeye çeşitlilik uygular — **kalite metriği değil**, sunum kuralı |

---

## §3 Workflow Bileşeni — `AIWorkflow::recommendationWorkflow(array $request)` L49–91

| Adım | Satır | Çağrı | K006.8 ilgisi |
|---|---|---|---|
| 1 · Task dispatch | 59 | `orchestrator->dispatchTask(...)` (beklenmiyor) | görev kaydı — **kalite kontrolü değil** |
| 2 · Bilgi zenginleştirme | 63 | `knowledge->search($query, …)` | bağlam — ölçüm yok |
| 3 · Öneri | ~67–68 | `engine->getRecommendations(...)` | §2.1 |
| 4 · JSON ayrıştırma | 70 | `json_decode(..., JSON_THROW_ON_ERROR)` | **hata fırlatır** (R-12 ailesi) |
| 5 · Prompt | 86 | prompt yalnızca metadatayı besler | — |
| çıkış | — | sonuç + `execution_time` | süre var, **kalite metriği yok** |

**İzlek:** workflow'ta "quality check" adı verilen, **sonucu ölçen** hiçbir adım yoktur;
`execution_time` tek sayısal izdir (performans, kalite değil).

---

## §4 Sözleşme Eşlemesi

| Sözleşme | İmza | Konum | Uygulayan |
|---|---|---|---|
| `AIEngineInterface::getRecommendations(...)` | 1/5 | Contracts L28 · docblock L20–26 | `AIEngine` L49 |
| (değerlendirme/metrik için ayrı interface) | — | **YOK** | — |

`@return` docblock (Contracts L26) 6 alan ister; implementasyon `score` alanı üzerine kuruludur —
ancak skorlar bugün hep boş kümeden gelir (§2.3).

---

## §5 Boşluk Tablosu (beklenen bileşen → repo durumu)

| Beklenen bileşen (K006.8 kapsamı) | Repo durumu | Kanıt |
|---|---|---|
| Metrik hesaplayıcı (precision@k, ndcg, hit-rate…) | **0 satır** | metrik grep → yalnızca yanlış pozitifler |
| Drift/veri kalitesi izleme | **0 satır** | grep `drift|evidently` → 0 |
| Deney/parametre takibi (ağırlık karşılaştırma) | **0 satır** | grep `mlflow` → 0 · 0.6/0.4 sabit (L65) |
| Değerlendirme raporu/panosu | **0 satır** | glob `shared/src/AI/*` — ilgili dosya yok |
| A/B veya çevrimiçi ölçüm | **0 satır** | grep `ab_test|experiment` → 0 |
| AI kalite testi | **0 satır** | `shared/tests/**/*.php` → 44 dosya, AI 0 |
| Eşik filtresi (MIN_SCORE) | ✅ **var** | L71 |
| Sıralama (usort) | ✅ **var** | L74 |
| Skor birleştirme | ✅ **var** (boş girdilerle) | L243 + §2.3 |

---

## §6 Kardeş Eşlemesi

| Bileşen | İlişki |
|---|---|
| K006.3-recommendation | değerlendirme, önerinin **ölçüm katmanıdır**; skor üreten `combineScores`/`getRecommendations` orada tanımlı — burada yalnızca kalite yorumu |
| K006.9-testing | §2.3'ün boş gövdeleri + §3'teki `JSON_THROW_ON_ERROR` → karakteristik test önceliği |
| K006.1-model-management | metrik/izleme çıksa nereye yazılır → model/çalışma kaydı (henüz yok) |
| K016-AI (dış) | `dispatchTask`/`knowledge->search` ajan altyapısıdır — K006.8'in ölçüm sahası değildir |

---

## §7 Kaynaklar

1. `shared/src/AI/AIEngine.php` — L49–77 · L209–264 tam okuma, 2026-10-12.
2. `shared/src/AI/AIWorkflow.php` — L49–91 tam okuma, 2026-10-12.
3. `shared/src/Contracts/AI/AIEngineInterface.php` — L20–28, 2026-10-12.
4. `shared/src/**` ML metrik grep → gerçek eşleşme 0 (62 yanlış pozitif listelendi), 2026-10-12.
5. `shared/composer.json` — phpunit ^10.5 · phpstan ^1.10 · ML/izleme paketi yok, 2026-10-12.
6. `evaluation-web-arastirma.md` §5 — çapraz bağ.

---

`status: verified` · `validation: 2026-10-12 · 4 gerçek metot + 1 workflow satır aralıklarıyla doğrulandı, 9 beklenen bileşenden 6'sı 0 satır`
