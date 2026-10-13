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
