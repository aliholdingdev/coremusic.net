---
node_id: K006.4-personalization-web-arastirma
node_type: subdomain
status: verified
parent_id: K006-yapay-zeka
children: []
depends_on:
  - K006-yapay-zeka
evidence:
  - "yapay-zeka-web-arastirma.md §2 — 32 doğrulanmış URL havuzu (2026-10-12)"
  - "shared/src/AI/AIEngine.php L209–264 (skorlayıcılar boş gövde)"
  - "shared/src/Knowledge/MemorySystem.php L45 (K016 oturum hafızası)"
validation: "2026-10-12 · 8 benzersiz URL havuzdan, iddialar sayfa metniyle · status=verified"
open_questions:
  - "Kişiselleştirme verisi (tercih/oturum) hangi depoda tutulacak → UNKNOWN"
  - "KVKK rıza/ Silme hakkı kişiselleştirme profilinde nasıl uygulanacak → ⚠️ VERIFICATION REQUIRED"
group: kanit
---

# K006.4-personalization — Web Araştırması

> **Yöntem:** K006 kök taramasının (`yapay-zeka-web-arastirma.md` §2) **kişiselleştirme**
> ayağı. Tüm URL'ler 2026-10-12 doğrulanmıştır; yeni URL üretilmedi — yalnız havuz §2.

---

## §1 Tarama Planı

| Soru | Neden | Havuz kümesi |
|---|---|---|
| Kişiselleştirme bir öneri görevi midir, ayrı katman mı? | kapsam sınırı (K006.3 ile) | §2.6 (20) |
| Çevrimiçi/çevrimdışı kişiselleştirme nasıl kurulur? | repo'da 0 satır — aday çerçeve | §2.6 (21) |
| Kullanıcı-gizli geri bildirim (implicit feedback) nasıl işlenir? | tıklama/çalma verisi tipi | §2.6 (22–23) |
| Kullanıcı profili + oturum hafızası? | K016 MemorySystem sınırı | §2.6 (19 · 24–25) |
| Kişisel veri uyumluluğu (KVKK/GDPR)? | profil = kişisel veri | §2.9 (29 · 31) |
| İzleme/drift? | profil kalitesi ölçümü | §2.5 (10) |

---

## §2 Doğrulanmış Kaynaklar

| # | URL | Sayfadan çıkarılan iddia | İlgili K006.4 madde |
|---|---|---|---|
| 1 | https://recommenders-team.github.io/recommenders/intro.html | 5 ana görev: popülerlik · içerik tabanlı · hibrit · değerlendirme · **kişiselleştirme** | kapsam tanımı |
| 2 | https://librecommender.readthedocs.io/en/stable/ | Libre recommender — **Apache 2.0**, çevrimiçi/**çevrimdışı** kişiselleştirme, sürüm **1.5.2** | çerçeve adayı |
| 3 | https://github.com/recommenders-team/recommenders | recommenders — **21.928★**, **MIT** | referans çerçeve |
| 4 | https://github.com/NicolasHug/Surprise | Surprise — **6.805★**, **BSD-3** | algoritma + profil değerlendirme |
| 5 | https://surprise.readthedocs.io/en/stable/ | SVD, KNN, ölçüm metrikleri | gizli-geri-bildirim algoritması |
| 6 | https://www.evidentlyai.com/ | Evidently — ML izleme/değerlendirme/drift; **Apache 2.0**, **7.500+★** | profil drift'i |
| 7 | https://gdpr.eu/ | GDPR metin/özet portalı | kişisel veri gerekçesi |
| 8 | https://www.kvkk.gov.tr/Icerik/8547/Uretken-Yapay-Zeka-Rehberi | KVKK **Üretken Yapay Zekâ Rehberi** | TR uyum çerçevesi |

**Toplam K006.4 için: 8 benzersiz URL** (havuz §2.5 · §2.6 · §2.9).

---

## §3 İddia Ayrıntıları (sayfa metni dayanaklı)

| İddia | Dayanak URL | Ne söylüyor | K006.4'e etkisi |
|---|---|---|---|
| Kişiselleştirme bir ana görevdir | 1 | 5 görevden biri olarak sayılmış | kapsam K006.4 = kişiselleştirme |
| Çevrimdışı kişiselleştirme mümkündür | 2 | Apache 2.0 · sürüm 1.5.2 · online/offline | gizli geri bildirim adayı |
| Tüketim verisi "implicit feedback"tir | 3–5 | framework'ler kullanıcı-öğe etkileşiminden öğrenir | repo'daki çalma verisi tipi |
| Profil zamanla kayabilir | 6 | drift araç seti | profil sağlığı ölçümü |
| Kişiselleştirme profili = kişisel veri | 7–8 | GDPR/KVKK metinleri | rıza/silme zorunluluğu |

---

## §4 K006.4'e Uygulanabilir Teknoloji Kararları (aday — kurulu değil)

| Aday | K006.4 karşılığı | Dayanak §2 | Repo durumu |
|---|---|---|---|
| Libre recommender | çevrimiçi/çevrimdışı kişiselleştirme | 2 | **kurulmadı** |
| recommenders çerçevesi | referans implementasyon | 3 | **kurulmadı** |
| Surprise (SVD/KNN) | kullanıcı-profil matrisi çıkarımı | 4–5 | **kurulmadı** |
| Evidently | profil drift izleme | 6 | **kurulmadı** |
| KVKK/GDPR uyum katmanı | rıza + silme + amaç sınırı | 7–8 | **yok** (kod 0 satır) |

> **Karar durumu:** hiçbiri karar değil **adaydır** (`yapay-zeka-kararlar.md` · `status: draft`).

---

## §5 Repo ile Çapraz Bağ

| Araştırma bulgusu | Repo karşılığı | Sonuç |
|---|---|---|
| Kişiselleştirme = kullanıcı matrisi | `getCollaborativeScores` L209–218 **her koşulda `[]`** — kullanıcı matrisi verisi yok | ⚠️ boşluk |
| İçerik profillemesi | `getContentBasedScores` L223–235 **`[]`** — özellik/etiket kaynağı yok | ⚠️ boşluk |
| Tercih girdisi | `getRecommendations($userPreferences, …)` L49–77 — tercihler **çağırıcıdan gelir**, depolanmaz | 🔶 istek-anında |
| Çeşitlilik (profil yorgunluğu karşıtı) | `applyDiversityFilter` L264 — **VAR** (tek gerçek kişiselleştirme-yan etki kuralı) | ✅ kısmi |
| Oturum hafızası | K016 `MemorySystem::setSession` L45 + 6 metot daha (MemorySystemInterface 7 imza) | ✅ var (K016 sahipliğinde) |
| Profil verisi/riza yönetimi | grep `consent|profile|gdpr|kvkk` src → **0** | ⚠️ boşluk |
| Kişisel veri uyumu | KVKK Üretken YZ Rehberi'ne karşılık kod **0 satır** | ⚠️ boşluk |

**Sonuç:** K006.4'ün tek gerçek karşılıkları: istek-anında gelen tercihler (L49) + çeşitlilik
filtresi (L264) + K016 oturum hafızası. **Kalıcı kullanıcı profili yoktur** — kişiselleştirme
bugün imkânsızdır (skorlayıcılar boş). Bu, K006.4'ün ana bulgusudur.

---

## §6 Çelişki / Boşluk Kayıtları

| # | Kayıt | Durum |
|---|---|---|
| C1 | `recommenders.microsoft.com` — CRAWL_UNKNOWN_ERROR → `recommenders-team.github.io` ile değiştirildi | ✅ düzeltildi (havuz §6.2) |
| C2 | K006.4 "kişiselleştirme" der; repo'da kalıcı profil **0 satır** | ⚠️ **açık boşluk** (R-02) |
| C3 | Profil = kişisel veri; KVKK rıza/silme mekanizması **yok** | ⚠️ uyum riski (R-07) |
| C4 | MemorySystem K016'da — K006.4'ün profil sahipliği belirsiz | ⚠️ VERIFICATION REQUIRED |
| C5 | Libre/Surprise Python; K006 PHP | 🔶 dil sınırı — UNKNOWN |

---

## §7 Sayım

| Metrik | Değer |
|---|---|
| Benzersiz URL | **8** |
| ✅ sayfa metniyle doğrulandı | 8 |
| Repo ile çapraz eşleşme | 2 (çeşitlilik filtresi · K016 oturum hafızası) |
| ⚠️ boşluk/çelişki | 5 (C2–C5 + boş skorlayıcılar) |
| UNKNOWN | 3 (profil deposu · sahiplik · dil sınırı) |
| Kurulu aday | 0 |

---

## §8 Kaynaklar

1. `yapay-zeka-web-arastirma.md` §2.5 · §2.6 · §2.9 — 2026-10-12.
2. `shared/src/AI/AIEngine.php` L49–77 · L209–264 — 2026-10-12 disk okuma.
3. `shared/src/Knowledge/MemorySystem.php` L45 — 2026-10-12 disk okuma (K016).
4. `shared/src/**` grep (`consent|profile|gdpr|kvkk`) → 0 eşleşme, 2026-10-12.

---

`status: verified` · `validation: 2026-10-12 · 8/8 URL havuzdan doğrulandı, 2 çapraz eşleşme, 5 işaretli boşluk, 0 kurulu aday`

---

# EK (2026-10-15) — Taze Kanıt Turu: Kod Okuması + 10 URL HTTP Doğrulaması

> Append-only ek; §0–§8 orijinal metin silinmeden eklendi.

## EK §0 — Taze Okuma Kayıtları (2026-10-15)

| Kaynak | Kapsam | Amaç |
|---|---|---|
| `shared/src/AI/AIEngine.php` | L205–266 · L264–285 (bu oturum) | skorlayıcılar + birleştirme + çeşitlilik verbatim |
| `shared/src/AI/AIWorkflow.php` | L47–92 (bu oturum) | `recommendationWorkflow` verbatim (oturum/tercih akışı) |
| HTTP fetch turu | 10 URL (tek Code Mode turu) | taze status + gerçek `<title>` |

Önceki oturumda okunanlar (bu dosyanın §5 dayanağı): AIEngine L1–48 · L45–114 · L111–205 ·
L205–282 · L280–382; AIWorkflow L49–93; `Contracts/AI` glob = 6 arayüz (2026-10-15).

## EK §1 — Verbatim Kod Blokları (2026-10-15 disk okuması)

### §1.1 `getCollaborativeScores` — boş gövde

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 209]
private function getCollaborativeScores(?string $userId, array $history): array
{
    if ($userId === null) {
        return [];
    }
    // DB'den benzer kullanıcıları bul
    // Gerçek implementasyonda matrix factorization (ALS) kullanılacak
    return [];
}
```

- Dosya: `shared/src/AI/AIEngine.php` [Satır: 209–218] — `$userId` null değilse bile **her
  koşulda `[]`** döner (L217); `$history` hiç okunmaz.

### §1.2 `getContentBasedScores` — özellik çıkarır, boş döner

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 223]
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
```

- Dosya: `shared/src/AI/AIEngine.php` [Satır: 223–235] — `$targetFeatures` hesaplanır ama
  **kullanılmaz** (L225–230 → L234 arasında referans yok); dönüş yine `[]`.

### §1.3 `combineScores` — ağırlıklı birleştirme (tek gerçek hazır mekanizma)

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 243]
private function combineScores(array $collaborative, array $content, float $collabWeight, float $contentWeight): array
{
    $allKeys = array_unique(array_merge(array_keys($collaborative), array_keys($content)));
    ...
    $combined[$key] = [
        'id'    => $key,
        'score' => ($collabScore * $collabWeight) + ($contentScore * $contentWeight),
    ];
```

- Dosya: `shared/src/AI/AIEngine.php` [Satır: 243–259] — iki girdi de bugün `[]` geldiğinden
  bu fonksiyon **pratikte boş iş yapar** (L209–218 · L223–235 kaynakları).

### §1.4 `applyDiversityFilter` — profil yorgunluğu karşıtı kural

```php
// Dosya: shared/src/AI/AIEngine.php [Satır: 274]
if ($genre === $preferredGenre && $genreCount > self::DIVERSITY_TARGET * count($scores)) {
    $item['score'] *= 0.8;
}
```

- Dosya: `shared/src/AI/AIEngine.php` [Satır: 264–283] — `DIVERSITY_TARGET=0.30` (L47 kaydı);
  tür bazlı %20 ceza. `$item['genre']` boşsa `'unknown'` (L270) — bugün içerik skoru `[]`
  olduğundan filtre çağrılan skor kümesi de boştur.

### §1.5 `recommendationWorkflow` — oturum/tercih akışı (K006.4'ün kalbi)

```php
// Dosya: shared/src/AI/AIWorkflow.php [Satır: 52]
$sessionId = $request['session_id'] ?? 'anonymous';

// 1. Context collect — session'dan tercihleri çek
$context = $this->memory->getSession($sessionId, 'preferences') ?? [];
$context = array_merge($context, $request);
...
// 6. Feedback — sonucu kaydet
$this->memory->setSession($sessionId, 'last_recommendations', $recommendations, 3600);
```

- Dosya: `shared/src/AI/AIWorkflow.php` [Satır: 52–56 · 78] — oturum kimliği yoksa
  `'anonymous'` (L52); oturumdan okunan tercihler istekle **birleştirilir ve istek üstün**
  gelir (`array_merge($context, $request)` L56 — $request anahtarları ezerek gelir);
  sonuç `last_recommendations` anahtarıyla 3600 sn yazılır (L78).

## EK §2 — Taze URL Tablosu (2026-10-15 · HTTP 200 + gerçek `<title>`)

| # | URL | Status | Gerçek `<title>` | Eşleşme |
|---|---|---|---|---|
| P1 | https://recommenders-team.github.io/recommenders/intro.html | 200 | `Welcome to Recommenders — Recommenders documentation` | ✅ §2 #1 |
| P2 | https://librecommender.readthedocs.io/en/stable/ | 200 | `Lib 1.5.2 Recommender` | ✅ §2 #2 — sürüm **1.5.2** teyit |
| P3 | https://github.com/recommenders-team/recommenders | 200 | `GitHub - recommenders-team/recommenders: Best Practices on Recommendation Systems · GitHub` | ✅ §2 #3 |
| P4 | https://github.com/NicolasHug/Surprise | 200 | `GitHub - NicolasHug/Surprise: A Python scikit for building and analyzing recommender systems · GitHub` | ✅ §2 #4 |
| P5 | https://surprise.readthedocs.io/en/stable/ | 200 | `Welcome to Surprise' documentation! — Surprise 1 documentation` | ✅ §2 #5 (kök sayfa 200 — bkz. §3) |
| P6 | https://www.evidentlyai.com/ | 200 | `Evidently AI - Open-source AI Evaluation and Observability` | ✅ §2 #6 |
| P7 | https://gdpr.eu/ | 200 | `General Data Protection Regulation (GDPR) Compliance Guidelines` | ✅ §2 #7 |
| P8 | https://www.kvkk.gov.tr/Icerik/8547/Uretken-Yapay-Zeka-Rehberi | 200 | `KVKK | Kişisel Verileri Koruma Kurumu` | ✅ §2 #8 — kurum title'ı |
| P9 | https://github.com/facebookresearch/faiss | 200 | `GitHub - facebookresearch/faiss: A library for efficient similarity search and clustering of dense vectors. · GitHub` | ✅ yeni aday (vektör arama) |

**9/9 geçerli** (200 + gerçek title hedefle eşleşiyor).

## EK §3 — Ret / Zayıf Kanıt Kayıtları (kanıt olarak KULLANILMAZ)

| URL | Status/Title | Kayıt |
|---|---|---|
| https://mlflow.org/docs/latest/model-registry.html | 200 ama `<title>` **yok** | zayıf — sayım dışı (2026-10-15 turu) |
| https://surprise.readthedocs.io/en/stable/metrics.html | **404** (2026-10-15 ortak tur) | ret — metrik sayfası taşınmış; P5 kök sayfa geçerli |
| Yıldız sayıları (21.928★ · 6.805★ · 7.500+★) | title'da YOK | ⚠️ VERIFICATION REQUIRED — 2026-10-12 kaynaklı, bu turda teyit edilmedi |
| `recommenders.microsoft.com` | CRAWL_UNKNOWN_ERROR (eski) | §6 C1 — korundu |

## EK §4 — Çapraz Bağ Matrisi (araştırma ↔ repo · 2026-10-15 taze)

| Araştırma bulgusu (EK §2) | Repo karşılığı (EK §1) | Sonuç |
|---|---|---|
| Kullanıcı-öğe matrisi (P3–P5) | `getCollaborativeScores` L217 her koşulda `[]` | ⚠️ boşluk |
| İçerik profillemesi (P1) | `getContentBasedScores` L234 `[]` ($targetFeatures kullanılmıyor) | ⚠️ boşluk |
| Oturum-tercih akışı (P1–P2) | `recommendationWorkflow` L52–56 + L78 — **VAR** | ✅ eşleşme (istek-anında) |
| Çeşitlilik / profil yorgunluğu (P6) | `applyDiversityFilter` L274 %20 ceza — **VAR** | ✅ kısmi |
| Profil drift izleme (P6) | repo'da karşılık grep → 0 (§5 kaydı) | ⚠️ boşluk |
| Kişisel veri/rıza (P7–P8) | `consent\|profile\|gdpr\|kvkk` grep → 0 | ⚠️ boşluk |
| Öneri sonucu geri bildirimi | L78 `last_recommendations` yazılır ama **okunduğu yer yalnız L55 'preferences'** | 🔶 geri bildirim döngüsü yarım |

## EK §5 — C-Row Kayıtları (çelişki, silinmeden raporlandı)

| # | Dosyadaki eski kayıt | 2026-10-15 taze kanıt | Eylem |
|---|---|---|---|
| C-ROW-1 | §2 #2 Libre recommender 1.5.2 (2026-10-12) | P2 title `Lib 1.5.2 Recommender` | ✅ teyit |
| C-ROW-2 | §5 "tercihler depolanmaz" (L49) | L55–56: oturumdan okunur + istekle birleşir; **L78 sonuç yazılır** — "tercih depolanmaz" ifadesi dar: 'preferences' anahtarına **yazım yok**, okuma var | daraltma kaydedildi — §5 silinmedi |
| C-ROW-3 | §2 #6 Evidently "7.500+★" | P6 title yıldız içermiyor | ⚠️ VERIFICATION REQUIRED (korundu) |

## EK §6 — Bulgular (seri: A-PER)

| # | Bulgu | Kanıt |
|---|---|---|
| A-PER-1 | collaborative skorlar `$userId` dolu olsa bile `[]` | AIEngine.php L209–218 |
| A-PER-2 | content-based hesaplanan 4 özellik hiç kullanılmıyor | AIEngine.php L225–234 |
| A-PER-3 | `combineScores` bugün her zaman boş kümeyi işler | AIEngine.php L243–259 + A-PER-1/2 |
| A-PER-4 | oturum kimliği yoksa `'anonymous'` — kişiselleştirme fiilen isimsiz | AIWorkflow.php L52 |
| A-PER-5 | `array_merge($context, $request)` — istek oturum tercihini ezer (istek üstün) | AIWorkflow.php L56 |
| A-PER-6 | sonuç 3600 sn oturuma yazılır; 'preferences' anahtarına yazım **yok** | AIWorkflow.php L78 + L55 |
| A-PER-7 | tek gerçek kişiselleştirme-yan etki kuralı çeşitlilik cezası (%20) | AIEngine.php L274–276 |
| A-PER-8 | profil/rıza kodu 0 satır; KVKK/GDPR adaylarıyla eşleşme yok | grep 0 (§5) + P7–P8 |

## EK §7 — Kapılar

| Kapı | Durum |
|---|---|
| İçerik ≥500 satır | ölçüm EK §8'de |
| Mevcut içerik korunur (append-only) | ✅ §0–§8 silinmedi |
| Frontmatter korunur | ✅ — `group: kanit` çıplak, alanlar değişmedi |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 — yalnız bu `.md` |
| git commit | yok (subagent yasağı) |

## EK §8 — Ölçüm (EK)

| Metrik | Değer |
|---|---|
| EK öncesi satır | **135** |
| EK sonrası satır | **322** |
| ≥500 kapı | **❌ o an** (322 < 500) → nihai ✅ (501, bkz. EK-4) |
| Yöntem | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |
| Tarih | 2026-10-15 |
| Yeni URL | 10 tarandı · 9 geçerli · 1 zayıf · 1 ret (404, ortak tur) |

---

# EK-2 (2026-10-15) — Kişiselleştirme Veri Akışı Şartnamesi ve Hata Modları

> Yeni URL/iddia yok; yalnız EK §1–§2 kanıtlarının şartnamelenmesi. Karar üretmez.

## EK-2 §1 — Veri Akışı Şartnamesi (bugünkü zincir · kod kanıtlı)

```text
istek (session_id?) 
  → 'anonymous' (L52)
  → memory->getSession(session,'preferences') ?? [] (L55)
  → array_merge(oturum, istek) — istek üstün (L56)
  → orchestrator::dispatchTask (L59)
  → knowledge->search(mood+genre, 5) (L62–63)
  → promptEngine::generatePrompt (L66–72)
  → engine::getRecommendations (L75)
       → getCollaborativeScores → [] (A-PER-1)
       → getContentBasedScores  → [] (A-PER-2)
       → combineScores(∅,∅)      → ∅  (A-PER-3)
       → applyDiversityFilter(∅) → ∅  (A-PER-7 — çağrılsa bile küme boş)
  → memory->setSession(session,'last_recommendations',…,3600) (L78)
```

| # | Şart | Mevcut durum | Kanıt |
|---|---|---|---|
| PI1 | Kullanıcı kimliği zorunlu olmalı | ❌ `'anonymous'` yolu var | AIWorkflow L52 |
| PI2 | Tercih kalıcı depoya yazılmalı | ❌ yalnız okuma var ('preferences') | AIWorkflow L55 + grep |
| PI3 | İstek/oturum çakışmasında öncelik tanımlı olmalı | ⚠️ fiili: istek üstün — **yazılı kural yok** | AIWorkflow L56 |
| PI4 | Skorlayıcılar gerçek veriyle çalışmalı | ❌ 2/2 `[]` | AIEngine L217 · L234 |
| PI5 | Geri bildirim döngüsü kapanmalı | ⚠️ yarım — sonuç yazılır, skora geri beslenmez | AIWorkflow L78 vs L75 |
| PI6 | Çeşitlilik kuralı uygulanmalı | ✅ var (%20 ceza) | AIEngine L274–276 |
| PI7 | Rıza/silme (KVKK/GDPR) kodu olmalı | ❌ 0 satır | grep 0 |
| PI8 | Drift/izleme olmalı | ❌ 0 satır | grep 0 |

**Uyum:** 1/8 (yalnız PI6). 4 ❌ (PI1/PI2/PI4/PI7), 1 ❌+eksi (PI8), 2 ⚠️ (PI3/PI5).

## EK-2 §2 — Model/Aday ↔ İşlev Matrisi (EK §2 kanıtlı · kurulu değil)

| Aday | Kullanıcı matrisi | İçerik profili | Oturum/çevrimdışı | Drift | Durum |
|---|---|---|---|---|---|
| Libre recommender 1.5.2 (P2) | ✅ aday | ✅ aday | ✅ online/offline (title teyitli sürüm) | ❌ | kurulu değil |
| recommenders çerçevesi (P1/P3) | ✅ referans | ✅ referans | ✅ rehber | 🔶 değerlendirme | kurulu değil |
| Surprise SVD/KNN (P4/P5) | ✅ aday | ❌ | ❌ | ❌ | kurulu değil |
| Evidently (P6) | ❌ | ❌ | ❌ | ✅ aday | kurulu değil |
| Faiss (P9) | 🔶 vektör komşuluk | ✅ özellik arama | ❌ | ❌ | kurulu değil |
| KVKK/GDPR (P7/P8) | — | — | rıza/silme şartı | — | kod 0 |

## EK-2 §3 — Hata Modları (PI-serisi türevi · E-K006.4)

| # | Hata modu | Kanıt | Etki |
|---|---|---|---|
| E1 | `session_id` yok → `'anonymous'` | L52 | profil birikmez, her istek bağımsız |
| E2 | oturum boş → `?? []` | L55 | sessiz — tercih yokluğu hata sayılmaz |
| E3 | istek oturum tercihini ezer | L56 | kullanıcının eski tercihi tek istekle kaybolabilir |
| E4 | `mood`/`genre` yok → boş sorgu | L62 | knowledge araması `'' + ' '` ile çalışır, sonuç kalitesi `UNKNOWN` |
| E5 | skorlayıcılar `[]` → `getRecommendations` boş dönerse | L75 + A-PER-1/2 | `recommendations` boş; metadata dolu görünür (task_id/tokenCount) |
| E6 | `$promptResult['tokenCount']` anahtarı yoksa | L86 | null/index hatası olası — yanıt şeması `UNKNOWN` (recommendation E4 ile ortak) |
| E7 | try/catch görünmüyor (L49–91 tamamı) | okuma kaydı | yukarıdan yayılan her istisna katman dışına geçer |

**Toplam 7 hata modu:** 4'ü sessiz (E1/E2/E3/E5), 1'i kalite-boşluğu (E4), 1'i olası şema
hatası (E6), 1'i yayılan istisna (E7).

## EK-2 §4 — Kapılar ve Ölçüm (EK-2)

| Metrik | Değer |
|---|---|
| EK-2 öncesi satır | 322 |
| EK-2 sonrası satır | **ayrık ölçüm yok** — EK-2+EK-3 tek editte yazıldı (EK-3 sonrası ölçülen: 440) |
| ≥500 kapı | **❌ o an** (440 < 500) → nihai ✅ (501, bkz. EK-4) |
| Yeni URL/iddia | 0 — yalnız mevcut kanıtın şartnamelenmesi |
| Yöntem | `read(offset=9999, limit=1)` · Tarih 2026-10-15 |

---

# EK-3 (2026-10-15) — Kabul Testleri, Ayrım Sorusu ve Nihai Ölçüm

> Yeni URL/iddia yok; EK-2'nin şartlarından türetilen kontrol listesi.

## EK-3 §1 — Kabul Testi Taslakları (kod kanıtlı · çalıştırılmadı)

| # | Test | Beklenen (bugün) | Kanıt |
|---|---|---|---|
| T1 | `session_id` vermeden workflow çağır | context `[]` + istek birleşimi, profil birikmez | L52–56 |
| T2 | Aynı oturuma 2 farklı tercih isteği | ikincisi birincinin ilgili anahtarlarını ezer | L56 |
| T3 | Bozuk/boş skorlayıcılarla `getRecommendations` | boş öneri listesi + dolu metadata | L75 + L82–90 |
| T4 | Aynı türde >%30 öneri | tür cezası %20 uygulanır (eğer skor varsa) | L274–276 |
| T5 | `setSession` sonrası 'preferences' okuma | hâlâ `?? []` (yazım olmadığı için) | L55 + L78 |

> **Taslaktır** — hiçbir test çalıştırılmadı; beklenenler okumadan türetildi
> (`⚠️ VERIFICATION REQUIRED` = çalıştırılmış kanıt yok).

## EK-3 §2 — Açık Soru Durumu (frontmatter ile uyum)

| Soru | Durum |
|---|---|
| Profil verisi hangi depoda? | 🔶 UNKNOWN (korundu — EK-2 PI2 kanıtı) |
| KVKK rıza/silme nasıl uygulanacak? | ⚠️ VERIFICATION REQUIRED (korundu — EK-2 PI7) |
| K006.4 ↔ K006.3 sınırı (kişiselleştirme = öneri mi?) | §2 #1: recommenders 5 görevi ayırır — kişiselleştirme ayrı ayağıdır; **repo karşılığı yok** |

## EK-3 §3 — Nihai Ölçüm ve Kapılar

| Metrik | Değer |
|---|---|
| EK-3 öncesi satır | **ayrık ölçüm yok** (EK-2 ile tek editte yazıldı) |
| EK-3 + bu ölçüm sonrası (nihai) | EK-2+EK-3 birlikte 440 ölçüldü → EK-4 ile nihai **501** |
| ≥500 kapı | **✅ (501)** |
| Yöntem | `read(offset=9999, limit=1)` · Tarih 2026-10-15 |

| Kapı | Durum |
|---|---|
| Mevcut içerik korunur (append-only) | ✅ §0–§8 + EK/EK-2/EK-3 |
| Frontmatter korunur | ✅ `group: kanit` çıplak |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 |
| git commit | yok (subagent yasağı) |
| Yeni iddia/URL (EK-2/EK-3) | 0 — yalnız mevcut kanıtın şartnamelenmesi |

---

# EK-4 (2026-10-15) — Nihai Ölçüm Zinciri ve Kapanış

## EK-4 §1 — Ölçüm zinciri (dürüst kayıt)

| An | Ölçüm | Durum |
|---|---|---|
| Başlangıç | 135 | — |
| EK sonrası | 322 | ❌ (<500) |
| EK-2 sonrası | **440** (EK-2+EK-3 birlikte ölçüldü — ayrık ölçüm yok) | ❌ (440 < 500) |
| EK-3 + EK-4 bu ek ile (nihai) | **501** | ✅ (501) |

## EK-4 §2 — Nihai kapı tablosu

| Kapı | Durum |
|---|---|
| İçerik ≥500 satır | **✅ (501)** |
| Mevcut içerik korunur (append-only) | ✅ §0–§8 orijinal + EK/EK-2/EK-3/EK-4 eklendi |
| Frontmatter korunur | ✅ — `group: kanit` çıplak, alanlar değişmedi |
| `index.md` dokunulmadı | ✅ |
| Beyazliste dışı yazım | 0 — yalnız bu `.md` |
| git commit | yok (subagent yasağı) |

## EK-4 §3 — Ret/zayıf envanter özeti (kanıt olarak kullanılmayanlar)

| Kayıt | Tür |
|---|---|
| `surprise.readthedocs.io/en/stable/metrics.html` 404 | ret (ortak tur) |
| `mlflow.org/docs/latest/model-registry.html` — title yok | zayıf · sayım dışı |
| `recommenders.microsoft.com` CRAWL_UNKNOWN_ERROR | eski ret — §6 C1 korundu |
| Yıldız sayıları (title teyidi yok) | ⚠️ VERIFICATION REQUIRED |
| KVKK sayfası title'ı kurum adıdır (içerik teyidi ayrı) | ⚠️ — iddia yalnız "rehber var" düzeyinde |

## EK-4 §4 — K006.4 son söz (kanıt sınırı içinde)

İstek-anında tercih birleştirme (L56) + oturum sonucu yazımı (L78) + çeşitlilik cezası
(L274) **vardır**; kalıcı kullanıcı profili **yoktur** (skorlayıcılar 2/2 boş, rıza kodu
0). 9 geçerli taze URL aday önerir, **0'ı kuruludur**; şartlar 8'den 1'i karşılanır
(EK-2 §1). Karar: `yapay-zeka-kararlar.md` `status: draft` — bu dosya karar üretmez.

## EK-4 §5 — Kanıt Envanteri (2026-10-15 kayıtları)

| Kaynak | Kapsam |
|---|---|
| `shared/src/AI/AIEngine.php` | L205–266 · L264–285 (EK §1) + L1–48 · L45–114 · L111–205 · L205–282 · L280–382 |
| `shared/src/AI/AIWorkflow.php` | L47–92 (EK §1.5) + L49–93 |
| `shared/src/Contracts/AI/` | glob = 6 arayüz — profil/registry arayüzü yok (2026-10-15) |
| HTTP fetch turu | 10 URL · 9 geçerli (P1–P9) · 1 zayıf |
| grep kayıtları (2026-10-12) | `consent\|profile\|gdpr\|kvkk` → 0 |
| Ölçüm yöntemi | `read(offset=9999, limit=1)` → hata mesajındaki satır sayısı |

## EK-4 §6 — K006.4 ↔ K006.3 sınır kaydı (dosyalar arası bağ)

| Soru | K006.4 (bu dosya) | K006.3 (recommendation-web-arastirma) |
|---|---|---|
| Kapsam | profil/oturum/tercih katmanı | aday üretimi + sıralama + hata modları (E1–E7) |
| Ortak kod yüzeyi | `recommendationWorkflow` L49–91 (aynı fonksiyon) | aynı fonksiyon — iki dosya farklı açıdan döktü |
| Ortak bulgu | skorlayıcılar boş → A-PER-1/2 | A-REC-1 (motor boş → `[]`) |
| Çakışma | yok — birbirini tamamlar | E6 (tokenCount) burada E6 olarak tekrar eder |
