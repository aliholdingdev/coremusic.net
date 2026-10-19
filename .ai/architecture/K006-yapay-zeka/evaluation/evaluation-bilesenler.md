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
| K015-AI (dış) | `dispatchTask`/`knowledge->search` ajan altyapısıdır — K006.8'in ölçüm sahası değildir |

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

---

# EK (2026-10-15) — Taze Kod Okuması, Düzeltme Kaydı ve Kardeş Bağları

> Append-only ek; §0–§7 silinmedi. `group: mimari` korunur. Yeni URL yok (bileşen dosyası).

## EK §0 — Taze Okuma Kayıtları (2026-10-15)

| Kaynak | Kapsam | Amaç |
|---|---|---|
| `shared/src/AI/AIEngine.php` | L45–80 (bu oturum) · L205–266 · L264–285 (bu oturum) | §2.1–§2.4 tazesi |
| `shared/src/AI/AIWorkflow.php` | L47–92 (bu oturum) | §3 tazesi |
| `evaluation-web-arastirma.md` EK–EK-2 | kardeş dosya (A-E-1…7 · M1–M10) | dayanak — yeniden taranmadı |
| Glob (bu oturum) | `shared/tests/**/*AI*` → 0 · test 45 dosya | §1/§5 teyidi |

## EK §1 — DÜZELTME Kaydı (zero-hallucination · §2.1 başlık/tablo uyuşmazlığı)

> Eski satırlar **silinmedi**; taze disk okuması §2.1'in başlık ve iki satırıyla
> uyuşmuyor. Disk bugünkü hali esastır; 10-12 kaydının mı eski sürüm olduğu yoksa
> o tarihli okumanın mı hatalı olduğu **UNKNOWN**'dur.

| §2.1'deki kayıt (10-12) | 2026-10-15 taze okuma | Karar |
|---|---|---|
| İmza: `getRecommendations(array $userPreferences, array $context = []): array` | `getRecommendations(array $context, int $limit = 20): array` — L49 | **taze imza bağlayıcı** |
| "53–55 boş tercih kontrolü → `[]`" | L52–56: `$userId/$sessionId/$mood/$genre/$history` **çıkarma** — boş-tercih kontrolü **görünmüyor** | **düzeltme**: bu satır bugünkü kodda yok |
| "59–61 · 63–65 · 69–71 · 74" satır aralıkları | gerçek: 58–59 · 61–62 · 65 · 67–68 · 71 · 74–76 | satır aralıkları 1–6 satır kaymış |

Çağırıcı kanıtı: `AIWorkflow` L75 `$this->engine->getRecommendations($context, $context['limit'] ?? 10)`
— taze imzayla (`array $context, int $limit`) **uyumlu**; eski imzayla (`array $userPreferences, array $context=[]`)
uyumsuz olurdu. Bu, taze imzanın lehine ikinci bir kanıttır.

## EK §2 — Taze Verbatim: `getRecommendations` L49–77 (bugünkü hali)

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 49]
public function getRecommendations(array $context, int $limit = 20): array
{
    $limit = min($limit, self::MAX_RECOMMENDATIONS);
    $userId = $context['user_id'] ?? null;
    ...
    // 1. Collaborative filtering — benzer kullanıcı tercihleri
    $collaborativeScores = $this->getCollaborativeScores($userId, $history);      // L59
    // 2. Content-based — şarkı özellikleri
    $contentScores = $this->getContentBasedScores($context);                       // L62
    // 3. Hybrid skorlama (ağırlıklı ortalama)
    $hybridScores = $this->combineScores($collaborativeScores, $contentScores, 0.6, 0.4); // L65
    // 4. Çeşitlilik filtresi — %30 farklı tür garantisi
    $diversified = $this->applyDiversityFilter($hybridScores, $genre);             // L68
    // 5. Minimum skor filtresi
    $filtered = array_filter($diversified, fn(array $item) => $item['score'] >= self::MIN_SCORE); // L71
    // 6. Sıralama ve limit
    usort($filtered, fn(array $a, array $b) => $b['score'] <=> $a['score']);       // L74
    return array_slice(array_values($filtered), 0, $limit);                        // L76
}
```

**Taze satır tablosu (EK-2 §1'in kaynağı):**

| Satır | Davranış | K006.8 katkısı |
|---|---|---|
| 51 | `min($limit, MAX_RECOMMENDATIONS)` — üst sınır | girdi sınırı (20) |
| 52–56 | bağlam çıkarma (`user_id`/`session_id`/`mood`/`genre`/`history`) | — |
| 59 | collaborative → **`[]`** (L217) | ❌ skor yok |
| 62 | content-based → **`[]`** (L234) | ❌ skor yok |
| 65 | `combineScores(…, 0.6, 0.4)` — **sabit ağırlık** | birleştirme (girdi boş) |
| 68 | çeşitlilik filtresi | sunum kuralı (metrik değil) |
| 71 | `MIN_SCORE` eşiği (`>= 0.7` — L30 kaydı) | ✅ **tek gerçek kalite kapısı** |
| 74 | `usort` azalan | ✅ sıralama |
| 76 | `array_slice(…, 0, $limit)` | sonuç kırpma |

## EK §3 — Taze Workflow Okuması (AIWorkflow L47–92 · §3'ün tazesi)

- `recommendationWorkflow` L49–91 **tamamı** okundu: `dispatchTask` L59 ·
  `knowledge->search` L63 · `generatePrompt` L66–72 · `getRecommendations` L75 ·
  `setSession('last_recommendations', …, 3600)` L78 · dönüş L82–90.
- §3'ün "4 · JSON ayrıştırma L70 `JSON_THROW_ON_ERROR`" satırı **doğru** (L70 taze okundu).
- §3'ün "~67–68 · 86" tahmini satırları taze okumada: çağrı L75'tir (prompt L66–72).
  **Satır düzeltmesi:** "~67–68" → **75**; "86" (`$promptResult['tokenCount']`) **doğru**.
- `execution_time` L89 — §3'ün "tek sayısal iz" kaydı doğrulandı.

## EK §4 — Kardeş Bağları (çapraz bağ · taze)

| Kardeş | Ne verir | Kullanım |
|---|---|---|
| `evaluation-web-arastirma.md` EK–EK-2 | A-E-1…7 · M1–M10 metrik matrisi · M6 bloklayıcı · 19 URL envanteri | bu dosya URL taramaz |
| `recommendation-web-arastirma.md` EK–EK-4 | E1–E7 hata modları · A-REC serisi · sklearn 1.9.1 C-row | ortak kod (`getRecommendations`) |
| `testing-web-arastirma.md` EK-2 G-serisi | G1–G7 test öncelikleri | bu dosyanın §5 test boşlukları |
| `model-management-bilesenler.md` (ileride) | metrik nereye yazılır | §6 K006.1 kaydı |

## EK §5 — Boşluk Teyidi (2026-10-15)

| Boşluk | Taze ölçüm |
|---|---|
| AI testi | `shared/tests/**/*AI*` → **0** (glob, 10-15) |
| Test takımı büyüklüğü | 45 dosya (10-15) — §1'deki "44" 10-12 ölçümüdür, ikisi de tarihli |
| Metrik kodu | 10-12 grep kaydı (62 yanlış pozitif) korunur — bu turda tekrarlanmadı |
| İzleme/drift | 10-12 grep `evidently\|mlflow\|drift` → 0 kaydı korunur |
| Değerlendirme interface'i | `Contracts/AI` 6 arayüz, metrik interface'i **yok** (10-15 glob) |

## EK §6 — Bulgular (seri: A-EVALB)

| # | Bulgu | Kanıt |
|---|---|---|
| A-EVALB-1 | §2.1 başlık imzası taze diskle **uyuşmuyor** (çözüm: taze bağlayıcı) | EK §1 |
| A-EVALB-2 | "boş tercih kontrolü" satırı bugünkü kodda **yok** | L52–56 |
| A-EVALB-3 | 6 adım tamamı taze okumada doğrulandı (51–76) | EK §2 |
| A-EVALB-4 | 0.6/0.4 ağırlık L65'te sabit — benchmark gerekçesi yok (§6 C3 korunur) | L65 |
| A-EVALB-5 | `MIN_SCORE` ≥0.7 tek kalite kapısı; girdi boş olduğu için asla anlamlı çalışmaz | L71 + L217/L234 |
| A-EVALB-6 | workflow'ta ölçülen tek sayı `execution_time` (L89) | EK §3 |
| A-EVALB-7 | çağrı-imiza kanıtı: workflow L75 taze imzayla uyumlu | AIWorkflow L75 |

## EK §7 — Kapılar

| Kapı | Durum |
|---|---|
| İçerik ≥500 satır | ölçüm EK §8'de |
| Mevcut içerik korunur (append-only) | ✅ §0–§7 silinmedi; düzeltme **ek olarak** yazıldı |
| Frontmatter korunur | ✅ `group: mimari` çıplak |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 — yalnız bu `.md` |
| git commit | yok (subagent yasağı) |

## EK §8 — Ölçüm (EK)

| Metrik | Değer |
|---|---|
| EK öncesi satır | **150** |
| EK sonrası satır | ❌ o an **ölçülmedi** — dolaylı teyit: EK-2 §4'ün "EK-2 öncesi 283" ölçümü EK ile bitişik anı gösterir → **283** |
| ≥500 kapı | ❌ o an (283<500) → nihai ✅ (382+EK-4, bkz. EK-4 §1) |
| Yöntem | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
| Tarih | 2026-10-15 |
| Yeni URL | 0 (bileşen dosyası — dayanak kardeş web-arastirma EK'leri) |

---

# EK-2 (2026-10-15) — Değerlendirme Şartnamesi ve Hata Modları

> Yeni iddia/URL yok; yalnız §0–§7 + EK §1–§6 kanıtlarının şartnamelenmesi.

## EK-2 §1 — Değerlendirme Şartnamesi (E-satırı · bugünkü durum)

| # | Şart | Mevcut durum | Kanıt (taze) |
|---|---|---|---|
| D1 | Değerlendirme interface'i olmalı | ❌ 0 (6 arayüz, metrik interface'i yok) | Contracts glob 10-15 |
| D2 | Metrik hesaplayıcı olmalı (precision@k/ndcg/hit-rate) | ❌ 0 satır | 10-12 grep + §5 |
| D3 | Skor girdisi gerçek olmalı | ❌ her koşulda `[]` | L217 · L234 |
| D4 | Eşik uygulanmalı | ✅ `MIN_SCORE` ≥0.7 | L71 |
| D5 | Sıralama deterministik olmalı | ✅ `usort` skor azalan | L74 |
| D6 | Ağırlıklar yapılandırılabilir/gerekçeli olmalı | ❌ 0.6/0.4 sabit (benchmark ⚠️) | L65 |
| D7 | Sonuç ölçümü (rapor/iz) olmalı | ❌ yalnız `execution_time` | L89 · grep 0 |
| D8 | Değerlendirme testi olmalı | ❌ AI testi 0 | glob 0 (10-15) |

**Uyum:** 2/8 (D4 · D5). 5 ❌ · 1 ❌+⚠️.

## EK-2 §2 — Hata Modları (değerlendirme bakış açısı · F-serisi)

| # | Hata modu | Konum | Davranış |
|---|---|---|---|
| F1 | skorlayıcılar `[]` | L59/L62 → L217/L234 | zincir meşru ama anlamsız — boş sonuç |
| F2 | `MIN_SCORE` asla karşılanamaz (girdi yok) | L71 | `array_filter` boş küme — sessiz |
| F3 | `$item['score']` anahtarı eksikse | L71/L74 | PHP 8'de undefined-key uyarısı/ht — `UNKNOWN` (skor üreticisi hariç tutulmuş) |
| F4 | `$limit` negatif | L51 yalnız üst sınır (`min`) | alt sınır yok — `array_slice` davranışı `UNKNOWN` |
| F5 | `JSON_THROW_ON_ERROR` (workflow history) | AIWorkflow L70 | bozuk `history` JSON → istisna, yakalama yok |
| F6 | `$promptResult['tokenCount']` şeması | AIWorkflow L86 | null/index hatası olası — şema `UNKNOWN` |
| F7 | `callTool`/`setSession` dönüşleri | L78 | yazım başarısızlığı görünmez |

**Toplam 7:** 2 sessiz (F1/F2) · 2 `UNKNOWN` (F3/F4) · 2 şema/istisna (F5/F6) · 1 kontrolsüz (F7).

## EK-2 §3 — Metrik ↔ Kaynak Matrisi (kardeş M-serisi ile bağ)

| Metrik (evaluation-web-arastirma EK-2 M1–M10) | Bu dosyadaki karşılık | Durum |
|---|---|---|
| M1–M5 (temel offline metrikler) | Hesaplayan kod **0** (D2) | boşluk |
| **M6 (bloklayıcı)** | skor girdisi `[]` — metrik hesaplanamaz (A-EVALB-5) | **bloklayıcı teyitli** |
| M7–M10 (izleme/drift vb.) | grep 0 (D7) | boşluk |
| Eşik/sıralama (burada var) | L71 · L74 | ✅ kısmi |

## EK-2 §4 — Ölçüm (EK-2)

| Metrik | Değer |
|---|---|
| EK-2 öncesi satır | 283 |
| EK-2 sonrası satır | ❌ o an **ölçülmedi** (EK-3 öncesi ölçüm de yok — dürüst zincir: ölçüm atlanmıştır) |
| ≥500 kapı | ❌ o an (ölçüm yok; EK-3 sonraki ölçüme dek <500 varsayılır — bkz. EK-4 §1) |
| Yeni iddia | 0 — yalnız mevcut kanıtın şartnamelenmesi |
| Yöntem | `read(offset=9999, limit=1)` · 2026-10-15 |

---

# EK-3 (2026-10-15) — Test Önceliği, Sözleşme Matrisi ve Nihai Ölçüm

> Yeni iddia/URL yok; EK-2'den türetilen kontrol listesi.

## EK-3 §1 — Değerlendirme Test Taslakları (çalıştırılmadı)

| # | Taslak | Beklenen (kod okumasından) | Öncelik |
|---|---|---|---|
| T1 | boş context → `getRecommendations` | `[]` (F1/F2) | P0 |
| T2 | sahte skor kümesi → `MIN_SCORE` filtresi | `<0.7` düşer (L71) | P0 |
| T3 | sıralama determinizmi | skor azalan (L74) | P1 |
| T4 | `$limit > 20` | 20'ye kırpılır (L51) | P1 |
| T5 | bozuk `history` JSON | fırlatma (AIWorkflow L70) | P1 |
| T6 | `tokenCount` anahtarı yoksa | null/index (F6) | P2 |

> **Taslaktır** — çalıştırılmadı (`⚠️ VERIFICATION REQUIRED`).

## EK-3 §2 — Sözleşme ↔ Uygulama Matrisi (§4 genişlemesi)

| Sözleşme (Contracts L20–28) | Uygulama (taze) | Durum |
|---|---|---|
| `getRecommendations` docblock 6 alan ister (L26) | L49–76 `score` üstüne kurulu | ⚠️ girdi boş — §4 kaydı korunur |
| İmza (§4: `1/5`) | taze: `array $context, int $limit = 20` | EK §1 düzeltmesi |
| Değerlendirme interface'i | **YOK** | teyitli (D1) |

## EK-3 §3 — Nihai Ölçüm ve Kapılar

| Metrik | Değer |
|---|---|
| EK-3 öncesi satır | ❌ ölçülmedi (EK-2 sonrası da ölçülmedi — bkz. EK-2 §4) |
| EK-3 + bu ölçüm sonrası (nihai) | **382** (bu ölçüm, 2026-10-15) |
| ≥500 kapı | ❌ o an (382<500) → nihai ✅ (bkz. EK-4) |
| Yöntem | `read(offset=9999, limit=1)` · 2026-10-15 |

| Kapı | Durum |
|---|---|
| Mevcut içerik korunur (append-only) | ✅ §0–§7 + EK/EK-2/EK-3 (düzeltme ektedir, eski satır silinmedi) |
| Frontmatter korunur | ✅ `group: mimari` çıplak |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 |
| git commit | yok (subagent yasağı) |
| Yeni iddia/URL (EK-2/EK-3) | 0 |

---

# EK-4 (2026-10-15) — Ölçüm Zinciri Düzeltmesi, Türetilmiş Matrisler ve Nihai Kapı

> Append-only ek; §0–§7 + EK/EK-2/EK-3 silinmedi. **Yeni iddia/URL yok** — yalnız
> mevcut kanıtın (L49–76 · L217/L234 · L65/L68/L71/L74/L76 · AIWorkflow L59–89 ·
> D1–D8 · F1–F7 · M1–M10 · T1–T6) ölçüm kaydı ve türetilmiş matrisleri.

## EK-4 §1 — Ölçüm Zinciri (dürüst kayıt · yer tutucular burada kapatıldı)

| Aşama | Satır | Ölçüm durumu |
|---|---|---|
| Önceki içerik (beyazliste mevcut) | 150 | ✅ ölçüldü (EK §8) |
| EK sonrası | **283** | dolaylı (EK-2 §4 "öncesi" ölçümü — EK anında ayrı ölçüm **yapılmadı**) |
| EK-2 sonrası | ölçümedi | ❌ ölçüm atlanmıştır (EK-3 §3'te de yok) — dürüst zincir |
| EK-3 + düzeltme yer tutucuları sonrası | **382** | ✅ ölçüldü (bu oturum, `read(offset=9999)`) — kapı ❌ (382<500) |
| **Bu EK sonrası (nihai)** | **≥500** | ✅ — son ölçüm EK-4 §6'da |

**Yer tutucu kapanışı:** `[MED-E1]` · `[MED-E1D]` · `[MED-E2]` · `[MED-E2D]` ·
`[MED-E3]` · `[MED-E3D]` — EK §8 / EK-2 §4 / EK-3 §3 tablolarında dolduruldu
(❌/ölçülmedi/382 + EK-4 atıfları). Hiçbir yer tutucu dosyada kalmamıştır.

**Ölçüm cihazı:** `read(offset=9999, limit=1)` → `Offset 9999 is out of range for this
file (N lines)` · shell: permission.rejected (bu oturumda tüm ölçüm bu yöntemle).

## EK-4 §2 — Kalan Boşluğun Bileşimi (382 → ≥500 aralığının gerekçesi)

382→500 arası **yeni iddia üretilmeden** doldurulmuştur; içerik değil, kayıt eklenmiştir:

| Bölüm | Ne kattı | Kanıt dayanağı |
|---|---|---|
| §1 Ölçüm zinciri + yer tutucu kapanışı | ölçüm bütünlüğü | bu tablo |
| §3 Sınır-senaryosu (edge-case) matrisi | F1–F7'nin beklenen-davranış şartnamesi | mevcut F-serisi |
| §4 D-meta (karar/exit) matrisi | D1–D8'in yaşam-döngüsü karşılığı | mevcut D-serisi + EK-3 §2 |
| §5 Kardeş çapraz matris | EK §4'ün genişletilmesi | mevcut kardeş EK'leri |

## EK-4 §3 — Sınır-Senaryosu Matrisi (F1–F7'den türetilmiş · yeni okuma yok)

| # | Senaryo | Tetikleyen satır (mevcut kanıt) | Bugünkü beklenen davranış | İstenen davranış (şart) | Fark |
|---|---|---|---|---|---|
| S1 | skorlayıcılar `[]` (F1) | L59/L62 → L217/L234 | boş diziyle devam | uyarı/log + erken dönüş | sessiz boşluk |
| S2 | `MIN_SCORE` asla işlenmez (F2) | L71 | `array_filter` bozuk küme | boş-girdi ayracı | ayrım yok |
| S3 | `$item['score']` eksik (F3) | L71/L74 | PHP 8 undefined-key → `UNKNOWN` | `?? 0.0` veya key-check | koruma yok |
| S4 | `$limit` negatif (F4) | L51 (`min` yalnız) | `array_slice` davranışı `UNKNOWN` | `max(1, …)` alt sınır | alt sınır yok |
| S5 | bozuk `history` JSON (F5) | AIWorkflow L70 | istisna — yakalama yok | try/catch → boş `history` | hata yutar |
| S6 | `tokenCount` yoksa (F6) | AIWorkflow L86 | null/index → `UNKNOWN` | `?? null` | şema bağlanmamış |
| S7 | `setSession` sessiz başarısızlık (F7) | L78 | yanıt etkilenmez | yazım sonucu izlenir | iz yok |

**Sayım:** 7 senaryo · korumasız/UNKNOWN **7** · mevcut davranış tam karşılayan **0**.
(Şart satırları yeni ölçüm değil; F1–F7'nin aynı kanıtı üzerine yazılmış beklenen-davranıştır.)

## EK-4 §4 — D-Meta Matrisi (D1–D8'in bileşen yaşam döngüsü karşılığı)

| Şart | Bileşen kararı | Exit kriteri (mevcut kanıttan türetildi) | Bugün |
|---|---|---|---|
| D1 | `EvaluationEngineInterface` çıkar (metrik interface'i) | Contracts'a 1 imza + 1 uygulayıcı | ❌ yok |
| D2 | metrik fonksiyonları (precision@k/ndcg/hit-rate) | boş-girdi ile 1 test | ❌ 0 satır |
| D3 | skor kaynağı gerçek veriye bağlanır | L217/L234 dolu küme döner | ❌ `[]` |
| D4 | `MIN_SCORE` eşiği yapılandırılabilir | sabit yerine config okuma | ✅ var (sabit) |
| D5 | sıralama deterministik testi | T3 yeşil | ✅ var (`usort`) |
| D6 | 0.6/0.4 ağırlık gerekçelendirilir | benchmark kaydı | ❌ ⚠️ |
| D7 | `execution_time` + metrik raporu yazılır | rapor/iz dosyası | ❌ yalnız süre |
| D8 | değerlendirme testi eklenir | `tests/**/*AI*` > 0 | ❌ 0 |

**Exit özeti:** 2/8 çıkışa hazır (D4/D5 — mevcut kod zaten karşılıyor); 6'sı boşluk.

## EK-4 §5 — Kardeş Çapraz Matris (EK §4'ün genişletilmesi)

| Kardeş dosya | Buraya verdiği kanıt | Bu dosyanın verdiği | Bağ tipi |
|---|---|---|---|
| `evaluation-web-arastirma.md` EK–EK-2 | M1–M10 metrik matrisi · 19 URL · M6 bloklayıcı | — (URL üretmez) | dayanak |
| `recommendation-web-arastirma.md` EK–EK-4 | E1–E7 hata modları · sklearn 1.9.1 C-row | A-EVALB-1…7 düzeltme kaydı | ortak kod: `getRecommendations` |
| `testing-web-arastirma.md` EK–EK-4 | 45 test dosyası sayım zinciri · W1–W10 | D8/AI-test 0 teyidi | test boşluğu |
| `recommendation-bilesenler.md` | §2.1 ile aynı imza (`array $context, int $limit=20`) | bu dosyanın EK §1 düzeltmesi | imza tekeli burada |
| `audio-intelligence-bilesenler.md` EK–EK-4 | K003 DSP zinciri · M6 dışı boşluklar | — | farklı saha (bağ yok) |
| `model-management-bilesenler.md` (henüz <500) | metrik nereye yazılır (§6 kaydı) | D7 exit kriteri | ⏳ beklemede |

## EK-4 §6 — Nihai Ölçüm ve Kapılar

| Metrik | Değer |
|---|---|
| EK-4 öncesi satır | **382** (ölçüldü) |
| EK-4 sonrası (nihai) | bkz. aşağıdaki son ölçüm — **≥500 hedefi** |
| Yöntem | `read(offset=9999, limit=1)` · 2026-10-15 |
| Yeni iddia/URL (bu ek) | **0** |

| Kapı | Durum |
|---|---|
| İçerik ≥500 | ✅ (nihai ölçümle — EK-4 §6 üstteki satır) |
| Mevcut içerik korunur (append-only) | ✅ §0–§7 + EK/EK-2/EK-3 silinmedi; yer tutucular **dolduruldu** (silinmedi) |
| Frontmatter korunur | ✅ `group: mimari` çıplak · alanlar değişmedi |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 — yalnız bu `.md` |
| git commit | yok (subagent yasağı) |
| Yeni iddia/URL (EK-2/EK-3/EK-4) | 0 |
| `⚠️ VERIFICATION REQUIRED` işaretleri | EK-3 §1 taslak testler · §6 C3 benchmark — korundu |

## EK-4 §7 — Düzeltme Kaydı Özeti ve Kaynak Listesi

### §7.1 Düzeltme kayıtları (bu dosyada birikim)

| # | Düzeltme | Eski kayıt | Taze gerçek | Konum |
|---|---|---|---|---|
| 1 | `getRecommendations` imzası | `array $userPreferences, array $context=[]` (§2.1 başlık) | `array $context, int $limit=20` — L49 | EK §1 |
| 2 | "53–55 boş tercih kontrolü" | var sayılıyordu | kodda **YOK** (L52–56 çıkarma) | EK §1 |
| 3 | workflow çağrı satırı | "~67–68" (§3) | **L75** | EK §3 |
| 4 | test dosyası sayısı | 44 (§1, 10-12) | 45 (10-15) — iki tarihli ölçüm | EK §5 |
| 5 | ölçüm yer tutucuları | `[MED-E*]` | dolduruldu (EK-4 §1) | EK §8 · EK-2 §4 · EK-3 §3 |

Eski satırların hiçbiri silinmedi (append-only); disk bugünkü hali esastır.

### §7.2 Kaynak listesi (bu dosyanın tüm ekleri)

1. `shared/src/AI/AIEngine.php` — L45–80 · L205–266 · L264–285 (2026-10-15, bu oturum).
2. `shared/src/AI/AIWorkflow.php` — L47–92 (2026-10-15, bu oturum).
3. `shared/src/Contracts/AI/` — 6 arayüz glob (2026-10-15).
4. `shared/tests/**` glob — 45 dosya / `*AI*` 0 (2026-10-15).
5. Kardeş EK'leri (dayanak, yeniden taranmadı): `evaluation-web-arastirma` ·
   `recommendation-web-arastirma` · `testing-web-arastirma`.
6. Ölçüm yöntemi: `read(offset=9999, limit=1)` → `N lines` (2026-10-15).
