---
node_id: K006.3-recommendation-bilesenler
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "shared/src/AI/AIEngine.php L49–77 · L209–264 (2026-10-12 tam okuma)"
  - "shared/src/AI/AIWorkflow.php L49–91 (2026-10-12 tam okuma)"
  - "shared/src/Contracts/AI/AIEngineInterface.php L20–28 (getRecommendations sözleşmesi)"
  - "shared/src/AI/AIOrchestrator.php L26 · L40 (görev tipi 'recommendation')"
validation: "2026-10-12 · tüm metot/satır grep ile doğrulandı; skorlayıcıların 2/2'si boş gövde · status=verified"
open_questions:
  - "Öneri sahipliği: K006 vs K008 vs K020 → ⚠️ VERIFICATION REQUIRED"
  - "0.6/0.4 ağırlıklarının gerekçesi (benchmark) → ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# K006.3-recommendation — Bileşenler

> K006.3'ün **gerçek** bileşen envanteri. Kural: var olan kod yazılır, olmayan "0 satır" olarak
> boşluk sayılır. Öneri **iskeleti** (ağırlık/eşik/sıralama/çeşitlilik) vardır; skor **üreten**
> iki motor **boş gövdedir**.

---

## §1 Envanter Özeti

| Ölçüm | Değer | Kayıt |
|---|---|---|
| K006.3'e atfedilen ayrı sınıf/dosya | **0** | glob `shared/src/AI/*` — 7 PHP, "recommendation" adında dosya yok |
| Öneri pipeline metotları | 4 (`getRecommendations` L49 · `combineScores` L243 · `applyDiversityFilter` L264 · boş skorlayıcılar L209/L223) | §2 |
| Boş skorlayıcı | **2/2** (`getCollaborativeScores` · `getContentBasedScores` → `[]`) | §2.2 |
| Öneri workflow'u | ✅ **var** (`recommendationWorkflow` L49–91) | §3 |
| Öneri sözleşmesi | 1 imza (`AIEngineInterface::getRecommendations` L28) | §4 |
| Göverit tipi kaydı | `AIOrchestrator::TASK_TYPES` L40 içinde 'recommendation' | §5 |
| Popülerlik baseline | **0 satır** | grep → 0 (2026-10-12) |

---

## §2 Gerçek Kod Bileşenleri

### §2.1 `AIEngine::getRecommendations(array $userPreferences, array $context = []): array` — L49–77 (public)

| Adım | Satır | Davranış |
|---|---|---|
| 1 | 53–55 | boş tercih kontrolü → `[]` |
| 2 | 59–61 | `getCollaborativeScores` → **`[]`** |
| 3 | 63–65 | `getContentBasedScores` → **`[]`** + sabit ağırlık **0.6/0.4** (L65) |
| 4 | 69–71 | `combineScores` + `MIN_SCORE` eşiği ile filtre |
| 5 | 74 | `usort` — `score` göre azalan sıralama |

### §2.2 Boş skorlayıcılar (private)

| Metot | Konum | Davranış |
|---|---|---|
| `getCollaborativeScores` | L209–218 | iki dalda `return [];` — kullanıcı matrisi yok |
| `getContentBasedScores` | L223–235 | `return [];` — özellik/etiket kaynağı yok |

**Bulgu:** L65'teki 0.6/0.4 ağırlık + `combineScores` **boş kümeler üzerinde çalışır** —
bugünkü öneri listesi her koşulda **boştur** (eşikten geçecek skor yoktur).

### §2.3 `AIEngine::combineScores` — L243 · `applyDiversityFilter` — L264 (private)

| Metot | Davranış |
|---|---|
| `combineScores` L243 | iki (bugün boş) kümenin ağırlıklı birleşimi |
| `applyDiversityFilter` L264 | sonucun çeşitliliği — sunum kuralı |

---

## §3 Workflow Bileşeni — `AIWorkflow::recommendationWorkflow(array $request)` L49–91

| Adım | Satır | Çağrı | Kusur/kayıt |
|---|---|---|---|
| 1 · Task dispatch | 59 | `orchestrator->dispatchTask(...)` | **beklenmiyor** (C5) |
| 2 · Bilgi zenginleştirme | 63 | `knowledge->search($query, …)` | K016 yüzeyi |
| 3 · Öneri | ~67–68 | `engine->getRecommendations(...)` | §2.1 |
| 4 · JSON ayrıştırma | 70 | `json_decode(..., JSON_THROW_ON_ERROR)` | fırlatır (R-12) |
| 5 · Prompt | 86 | prompt **yalnız metadatayı** besler — öneri içeriği değil | kalite değil |
| çıkış | 88–90 (yaklaşık bölge) | sonuç + `execution_time` | süre izi |

---

## §4 Sözleşme Eşlemesi

| Sözleşme | İmza | Konum | Uygulayan |
|---|---|---|---|
| `AIEngineInterface::getRecommendations(...)` | 1/5 | Contracts L28 · docblock L20–26 | `AIEngine` L49 |
| (öneri için ayrı interface) | — | **YOK** | — |

`@return` docblock (Contracts L26) 6 alan ister: `id, title, artist, score, genre, reason` —
alanlar tanımlı, **skor kaynağı boş** (§2.2).

---

## §5 Görev Kaydı — `AIOrchestrator`

| Bileşen | Konum | K006.3 ilişkisi |
|---|---|---|
| `AGENT_MAP` | L26 | ajan eşleme — 'recommendation' görevi dağıtılabilir |
| `TASK_TYPES` | L40 | görev tipleri içinde 'recommendation' |
| `dispatchTask` | L71–100 | workflow'un L59'da çağırdığı dağıtım |

**Kayıt:** önerinin **görev tipi** tanımlıdır; ancak iş mantığı K006.3'te değil, K016
orkestrasyonu üzerinden akar (sınır kaydı).

---

## §6 Boşluk Tablosu (beklenen bileşen → repo durumu)

| Beklenen bileşen (K006.3 kapsamı) | Repo durumu | Kanıt |
|---|---|---|
| İşbirlikçi skor motoru | **0 satır** (boş gövde) | L209–218 |
| İçerik-tabanlı skor motoru | **0 satır** (boş gövde) | L223–235 |
| Popülerlik baseline | **0 satır** | grep → 0 |
| Önbellek (sıcak öneri) | **0 satır** | composer.json cache paketi yok |
| Ağırlık yapılandırması/benchmark | **0 satır** (0.6/0.4 sabit) | L65 |
| Öneri sözleşmesi (alanlar) | ✅ **var** | Contracts L26 |
| Hibrit iskelet (eşik/sıralama/çeşitlilik) | ✅ **var** | L71 · L74 · L264 |
| Öneri workflow'u | ✅ **var** (kusurlu dispatch) | L49–91 |

---

## §7 Kardeş Eşlemesi

| Bileşen | İlişki |
|---|---|
| K006.4-personalization | profil→skor girdisi; bugün boş skorlayıcılar ortak kırılma noktası |
| K006.5-music-analysis | katalog öznitelikleri içerik-tabanlı skorlamayı beslemeli — placeholder (L315–330) kırıyor |
| K006.8-evaluation | ağırlık (0.6/0.4) kalitesi ölçülmedikçe iyileştirilemez |
| K008/K020 (dış) | **sahiplik 3-way açık soru** — kod bu düğümlerden hiçbirine yazılmamıştır |

---

## §8 Kaynaklar

1. `shared/src/AI/AIEngine.php` — L49–77 · L209–264 tam okuma, 2026-10-12.
2. `shared/src/AI/AIWorkflow.php` — L49–91 tam okuma, 2026-10-12.
3. `shared/src/AI/AIOrchestrator.php` — L26 · L40 · L71–100, 2026-10-12.
4. `shared/src/Contracts/AI/AIEngineInterface.php` — L20–28, 2026-10-12.
5. `recommendation-web-arastirma.md` §5 — çapraz bağ.

---

`status: verified` · `validation: 2026-10-12 · 5 gerçek metot + 1 workflow + 2 orkestratör sabiti doğrulandı, 8 beklenen bileşenden 5'i 0 satır`
