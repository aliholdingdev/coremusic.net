---
title: "inference — Kapsam"
type: architecture-scope
node_id: K006-yapay-zeka/inference
node_type: subdomain-scope
status: draft
parent: K006-yapay-zeka
date: 2026-10-12
updated: 2026-10-12
group: mimari
---

# inference — Kademe-2 Kapsam Kaydı

## §1 Rol (ELI15)

`ai-master.md` §1, v2 §2.6 rol cümlesini birebir taşır: **"Şarkı öneren, sesi analiz eden yapay zeka parçaları."**
`ai-master.md` §1 sorumluluk tablosu (satır 37–42) bu adı **"Modeli barındırıp çalıştırmak"** sorumluluğuna bağlar
(`model-management · inference · local-inference` birlikte) — tablo *tasarım yorumudur*.

Basitçe: eğitilmiş modeli **çalıştırıp çıktı üreten** taraf (serving). Repo'da durum net: motor çağrıları
**vardır** (`engine->getRecommendations`) ama bunlar **kural tabanlı PHP** değildir — gerçek bir model
inference runtime'ı (ör. ONNX oturumu) repo'da **görülmedi** (bkz. §3/7).

## §2 Kapsam sınırı (içinde / dışında / komşu)

| # | İçinde (bu dal) | Dışında | Sınır komşusu |
|---|---|---|---|
| 1 | Modelin K006 ürünü olarak çalıştırılması (tek ML çağrı noktası) | Ajan çalışma zamanı / prompt orkestrasyonu | K016-AI (ai-master §2.1 · bagimliliklar §4) |
| 2 | Inference girdi/çıktı sözleşmesi (context → skor/liste) | Girdi verisinin saklanması | K007-data (bagimliliklar §2/1) |
| 3 | Inference'ın çalıştırılacağı süreçle arayüz | Sürecin kendisi | K012-services (bagimliliklar §2/3) |
| 4 | Inference çıktısının UI'a verilmesi (sözleşme) | Kartın çizimi | K011-application (ai-master §2.1) |
| 5 | Inference performans/girdi doğrulaması (sözleşme) | Model kalitesi metrikleri | evaluation (ai-master §3) |
| 6 | Bulut mu yerel mi kararı için kanıt toplama | **Yerel** çalışma ayrıntıları | local-inference (ai-master §3) |

**Sınır kuralı (v2 §2.6 · registry §6):** bu dal yalnız **K006 = ML ürün tarafını** kapsar; K016-AI ajan
altyapısına **taşınmaz** — ajanlar inference'ı **çağırır**.

**Yasak:** tablo **kademe-2 taslak sınır önerisidir** — v2 §2.6 bu çocuğa rol vermez; kilit ADR-097 K4'tedir.

### Alt ayrım noktaları

| # | Ayrım | Durum | Kaynak |
|---|---|---|---|
| A1 | inference ↔ local-inference (aynı işin iki yüzü mü?) | **UNKNOWN** — v2 ayrım vermez | ai-master §3 satır 260/265 |
| A2 | inference ↔ model-management (çalıştıran mı, saklayan mı?) | **UNKNOWN** — v2 ayrım vermez | ai-master §3 satır 259/260 |
| A3 | Gerçek model runtime'ı repo'da yok — ne zaman eklenecek? | ⚠️ IMPLEMENTED değil | `shared/src/AI/` (bu oturum taraması) |
| A4 | Model formatı (ONNX vb.) seçimi | ⚠️ **UNKNOWN** | ai-master §4 |

## §3 Disk kanıtları

| # | İddia | Gerçek dosya yolu | Durum |
|---|---|---|---|
| 1 | Çocuk adı v2 §2.6 satır 260'ta birebir geçer | `.ai/architecture/K006-yapay-zeka/inference/index.md` (evidence) | ✅ dosya okundu |
| 2 | Rol tanımı yok → `⚠️ VERIFICATION REQUIRED` | `inference/index.md` (open_questions satır 13) | ✅ dosya okundu |
| 3 | Inference çağrısının tek noktası workflow → engine | `shared/src/AI/AIWorkflow.php` satır 75 | ✅ dosya okundu |
| 4 | Çıktı sözleşmesi: `array{id, title, artist, score, reason}` | `shared/src/Contracts/AI/AIEngineInterface.php` satır 26–28 | ✅ dosya okundu |
| 5 | Motor 5 modülü PHP içinde implement eder (AIEngine sınıfı) | `shared/src/AI/AIEngine.php` satır 28–32 | ✅ dosya okundu |
| 6 | Task tipi `recommendation` orchestrator'a düşer | `shared/src/AI/AIOrchestrator.php` satır 41 | ✅ dosya okundu |
| 7 | `shared/src/AI/` altında ONNX/TFLite/torch model runtime'ı **görülmedi** (bu oturum grep: `onnx` 0 isabet) | `.ai/…/inference/inference-dogrulama.md` §1/9 | ✅ oturum taraması |
| 8 | K006 model formatı / runtime seçimi | `ai-master.md` §4 | **UNKNOWN** |

### Açık sorular

| # | Soru | İşaret |
|---|---|---|
| R1 | inference ↔ local-inference sınırı ne? | ⚠️ VERIFICATION REQUIRED |
| R2 | Model formatı ve runtime (ONNX · PMML ·pickle/joblib · özel)? | ⚠️ ai-master §4 — **UNKNOWN** |
| R3 | Inference süreci K012'de mi, K006'da mı? | ⚠️ bagimliliklar §2/3 |
| R4 | Mevcut PHP motor mu, harici model mi (geçiş planı)? | ⚠️ **UNKNOWN** — kanıt yok |

**Kapanış:** status: draft kalır. R1–R4 kapanmadan bu dosyada **runtime/model seçimi** iddiası yazılmaz
(ai-master §4.1 · yapay-zeka-dogrulama §5).

## §4 İlişkili dosyalar

- [[inference-dogrulama]]
- [[inference-ornekler]]
- [[../index|K006-yapay-zeka index]]

---

**Authority:** Bayram Ali / Vault Steward · 2026-10-12

---

# EK — 2026-10-15 Derinleştirme Eki (append-only)

## EK §0 — Eklenti notu

Bu ek, 2026-10-12 tarihli inference kapsam kaydının **sonuna** eklenmiştir; L1–83 içeriği
değişmemiştir (append-only · `.ai/AGENTS.md` §25.3 madde 1–2). Eski metin bu ekin tam önekidir.

| Kural | Bu ek için durum |
|---|---|
| Append-only (eski satır silme/değiştirme) | ✅ uygulandı |
| Yeni URL yasak — yalnız 2026-10-12 kayıtlarının taşınması | ✅ §14: `inference/inference-web-arastirma.md` L42–49 · `inference/inference-dogrulama.md` L27–30 |
| `index.md` · `-anayasa.md` · `-dogrulama.md` · `-ornekler.md` · `.ai/*` boot dosyaları · `*.php/js/css/sql` | ❌ dokunulmadı (yalnız okundu) |
| `git commit` | ❌ atılmadı |
| `// ...` · `TBD` · doldurma satırı | ❌ yok — alıntılardaki `//` kaynak kodun kendi satırıdır |
| Satır ölçümü | `read` aralık testi (`shell` bu oturumda izin dışı) |

**Durum notu (dürüstlük):** eski envanterler bu dosyayı "84 satır" diye saymıştı; 2026-10-15
`read` ölçümü **83 satır** verir (bkz. C11). Fark eski metinde düzeltilmedi, buraya kaydedildi.

## EK §1 — Taze tarama (2026-10-15)

| # | Ne tarandı | Dosya | Satır | Bulgu |
|---|---|---|---|---|
| T1 | Çocuk satırı (2. sıra) | `K006-yapay-zeka/ai-master.md` | 149 | `| 2 | inference/ | 260 | Uzak/merkezi model çalıştırma — istek başına tahmin | ✅ 6/6 | draft |` |
| T2 | Komşu çocuk satırları | `ai-master.md` | 148, 154 | `model-management/` → v2 **259** · `local-inference/` → v2 **265** |
| T3 | Sorumluluk satırı S3 | `ai-master.md` | 184 | `S3 | Modeli barındırmak & çağırmak | model-management · inference · local-inference | — | AIOrchestrator::dispatchTask L71–100 | ⚠️ kısmi` |
| T4 | Ayrım §2.1 (K016) | `ai-master.md` | 101 | "LLM/ajan altyapısı ... → K016-AI · ✅ kapsam ayrımı" |
| T5 | Ayrım §2.1 (K012) | `ai-master.md` | 105 | "Öneriyi yürüten arka plan süreci → K012-services · **TASARIM İDDIASI**" |
| T6 | Ayrım §2.1 (K011) | `ai-master.md` | 107 | "UI'da öneri kartlarının çizimi → K011-application · TASARIM İDDIASI" |
| T7 | Açık soru #4 (§7) | `ai-master.md` | 256 | "Üretim modeli/runtime seçimi (hangi model, hangi format) → **UNKNOWN** · teknoloji karar kaydı · `-dogrulama` §7/4" |
| T8 | Aday envanter §11.1 | `ai-master.md` | 318–347 | "seçim YAPILMADI" — ONNX Runtime (325) · PyTorch (326) · TensorFlow (327) · vLLM (340) · HF Hub (342) |
| T9 | UNKNOWN listesi §11.2 | `ai-master.md` | 352–357 | model formatı · eğitim çatısı · boyut · donanım · SLO · lisans = **6 UNKNOWN** |
| T10 | Inference çağrısı | `shared/src/AI/AIWorkflow.php` | 75 | `$recommendations = $this->engine->getRecommendations($context, $context['limit'] ?? 10);` |
| T11 | Prompt adımı (kullanımı) | `shared/src/AI/AIWorkflow.php` | 66 | `$this->promptEngine->generatePrompt('recommendation', ...)` — **K016'ya çağrı** (bağımlılık envanteri L57: PromptEngine = K016 DIŞI) |
| T12 | Task tipleri | `shared/src/AI/AIOrchestrator.php` | 40–49 | `TASK_TYPES` = 8 kayıt; `recommendation` → agent `backend`, priority `medium` (L41) |
| T13 | Model runtime grep | `shared/src/**` (`onnx\|tflite\|torch\|pmml\|joblib`) | — | **0 isabet** (T13a: `shared/src/AI/` içinde de 0) → §3/7 iddiası genişletilerek doğrulandı |
| T14 | Dal indeksi | `inference/index.md` | 6–12, 18–21 | children = 6 dosya · open_questions **L18–21** (eski iddia "satır 13" — C4) |
| T15 | Anayasa başlıkları | `inference/inference-anayasa.md` | 29–511 | **33 başlık** (§1–§32 + §25/§26/§27–§32 ek usul) |
| T16 | Doğrulama matrisi | `inference/inference-dogrulama.md` | 27–30 | ONNX 4 satır ✅ (2026-10-12) — §14'te taşındı |

## EK §2 — Alıntı blokları (birebir)

### EK §2.1 Inference çağrısı + komşu adımlar — Dosya: `shared/src/AI/AIWorkflow.php` [Satır: 65–78]

```php
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
```

**Gözlem A1-1:** adım 5 = bu dalın **tek çağrı noktası** (eski §3/3, L56 — satır 75 ✅).
**A1-2:** adım 4 ve 6 `promptEngine` / `memory` üzerinden **K016'ya** gider → bu dal tek başına
çalışamaz; bağımlılık yönü "K006 ⇢ K016 çağrısı" (bağımlılık envanteri L180).
**A1-3:** adım 6 sonucu 3600 sn TTL ile oturum deposuna yazar — inference çıktısının **saklanması**
için ayrı sahip gerekir (K007/K016 sınırı, C5'e bkz.).

### EK §2.2 Çıktı sözleşmesi — Dosya: `shared/src/Contracts/AI/AIEngineInterface.php` [Satır: 21–28]

```php
    /**
     * Müzik önerisi üretir.
     *
     * @param array<string, mixed> $context Kullanıcı bağlamı (tercihler, geçmiş, mood)
     * @param int                  $limit   Maksimum öneri sayısı
     * @return array<int, array{id: string, title: string, artist: string, score: float, reason: string}>
     */
    public function getRecommendations(array $context, int $limit = 20): array;
```

**Gözlem A2-1:** eski §3/4 (L57) bu satırları "26–28" diye sayar; `@return` satırı **L26**,
imza **L28** ✅ — aralık dar ama doğru. **A2-2:** 5 alanlı şema (`id · title · artist · score ·
reason`) UI sözleşmesinin (K011) de tek dayanağıdır; test-stratejisi bunu "schema doğrulayıcı test"
olarak ister (`yapay-zeka-test-stratejisi.md` [Satır: 97–98]).

### EK §2.3 Task tipleri — Dosya: `shared/src/AI/AIOrchestrator.php` [Satır: 40–49]

```php
    private const TASK_TYPES = [
        'recommendation'    => ['agent' => 'backend', 'priority' => 'medium'],
        'analysis'          => ['agent' => 'embedded', 'priority' => 'high'],
        'eq-optimization'   => ['agent' => 'embedded', 'priority' => 'medium'],
        'hardware'          => ['agent' => 'audio-hw', 'priority' => 'high'],
        'fault'             => ['agent' => 'dsp-fw',   'priority' => 'critical'],
        'security-audit'    => ['agent' => 'security', 'priority' => 'critical'],
        'documentation'     => ['agent' => 'mo',       'priority' => 'low'],
        'testing'           => ['agent' => 'qa',       'priority' => 'medium'],
    ];
```

**Gözlem A3-1:** eski §3/6 (L59) "`recommendation` → satır 41" ✅ birebir doğrulandı.
**A3-2:** bu tablo **agent = K016 persona adları** taşır (backend · embedded · audio-hw · dsp-fw ·
security · mo · qa — `.ai/AGENTS.md` §4 persona listesiyle aynı adlar) → orchestrator, inference
isteklerini K016'ya devreder; inference'ın kendi "süreci" (eski §2/3 sınırı) fiilen K016'dadır.

### EK §2.4 Envanter satırları — Dosya: `ai-master.md` [Satır: 148–149, 154]

```text
| 1 | `model-management/` | 259 | Model kaydı · sürüm · ağırlık saklama · hangi modelin ürettiği | ✅ 6/6 | draft |
| 2 | `inference/` | 260 | Uzak/merkezi model çalıştırma — istek başına tahmin | ✅ 6/6 | draft |
| 7 | `local-inference/` | 265 | Cihaz üzerinde (offline) çalıştırma — yerel donanım | ✅ 6/6 | draft |
```

**Gözlem A4-1:** üç rol cümlesi de **tasarım yorumudur** (`ai-master.md` [Satır: 178] §4 önsözü
ve [Satır: 158–160] ad kuralı: "resmi kapsam tanımı v2'de yoktur → hepsi ⚠️").
**A4-2:** "Uzak/merkezi" ↔ "Cihaz üzerinde (offline)" ayrımı, eski §2/6 ve A1'in (L45) tek yazılı
dayanağıdır — **ölçüt değil, iki cümle** → A1 hâlâ UNKNOWN.

### EK §2.5 Aday envanter önsözü — Dosya: `ai-master.md` [Satır: 318–321]

```text
### §11.1 Doğrulanmış aday envanteri (seçim YAPILMADI)

Bu tablo **"CoreMusic şunu kullanacak" demez** — yalnızca 2026-10-12'de resmi kaynağı açılıp
doğuğrulanmış adayları listeler. Karar onayı yok → `status: draft` kalır.
```

**Gözlem A5-1:** bu dalın R2'si ("format/runtime seçimi") için bağlayıcı metin budur: aday
**doğrulanmıştır**, seçim **yapılmamıştır**. (Kaynak metindeki "doğuğrulanmış" yazım hatası
kaynakta olduğu gibi taşındı — düzeltilmedi.)

## EK §3 — Çelişki kaydı (eski satırlar silinmedi)

| # | Eski iddia (dosya·satır) | 2026-10-15 kanıtı | Karar |
|---|---|---|---|
| C1 | L18: "`ai-master.md` §1 sorumluluk tablosu (**satır 37–42**)" | ilgili tablo **§4** = L176–203; S3 satırı **L184**; L37–47 aralığı §0 set haritasıdır | ⚠️ BAYAT SATIR + BAYAT BÖLÜM |
| C2 | L45/L46: "ai-master §3 **satır 260/265**" ve "**259/260**" | bu sayılar **v2 mini-ağaç satırlarıdır**; ai-master'daki karşılıkları L149 (260) · L154 (265) · L148 (259) | ⚠️ KARIŞIK REFERANS (v2 ↔ ai-master) |
| C3 | L58: "`AIEngine.php` satır **28–32** = 5 modül docblock" | sınıf docblock **L18–27**; L28 `class AIEngine ...`; L30–32 sabitler (`MAX_RECOMMENDATIONS` vb.) | ⚠️ KAPSAM DIŞI ARALIK — içerik doğru, satır 4 aşağıda |
| C4 | L55: "`inference/index.md` (open_questions **satır 13**)" | `inference/index.md` open_questions bloğu **L18–21** (ilk madde L19) | ⚠️ BAYAT SATIR |
| C5 | L31: "Inference çıktısının saklanması → K007 (bagimliliklar §2/1)" | bağımlılık envanterinde çıktı saklama **doğrudan geçmiyor**; en yakın: `AIWorkflow` L78 `memory->setSession(...)` (MemorySystem → sahiplik **açık**, bagimliliklar L193) | ⚠️ ATIF ZAYIF — yön doğru, kanıt başka yerde |
| C6 | L48 / R2: "Model formatı (ONNX vb.) seçimi → `ai-master.md` **§4**" | §4 = sorumluluk matrisi (L176–203); format/runtime UNKNOWN'u **§7/4** = **L256** + §11.2 L352 | ⚠️ BAYAT BÖLÜM |
| C7 | L60: "`shared/src/AI/` altında ONNX/TFLite/torch **görülmedi** (grep `onnx` 0)" | 2026-10-15 genişletilmiş grep: `shared/src/**` içinde `onnx\|tflite\|torch\|pmml\|joblib` = **0 isabet** (T13) | ✅ DOĞRULANDI (kapsam genişledi, sonuç aynı) |
| C8 | L21–23: "gerçek bir model inference runtime'ı repo'da **görülmedi**" | T13 ile desteklendi; ayrıca `shared/src/AI/` 7 PHP dosyası arasında runtime sınıfı yok (`ai-master` D3, L90) | ✅ DOĞRULANDI |
| C9 | L68 (R2): "ai-master §4 — **UNKNOWN**" | C6 ile aynı: bölüm §4 değil, §7/§11.2 | ⚠️ BAYAT BÖLÜM |
| C10 | L61 (İddia 8): "model formatı / runtime seçimi → `ai-master.md` §4 → UNKNOWN" | §11.1 (L318) aday listesi **var** (ONNX · PyTorch · TF · vLLM · HF Hub) + §7/4 (L256) seçim **UNKNOWN** | ℹ️ ŞEKİL: "aday var, seçim yok" (L61'in "UNKNOWN"u seçim için doğru) |
| C11 | (envanter) "inference-kapsam = 84 satır" | `read`: **83 satır** (ek öncesi) | ℹ️ ÖLÇÜM DÜZELTMESİ |

## EK §4 — Kapsam sınırının (§2) derinleştirilmiş karşılığı

| # | Sınır (L29–34) | 2026-10-15 kanıt | Sonuç |
|---|---|---|---|
| 1 | Tek ML çağrı noktası · K016 | `AIWorkflow` L75 tek çağrı (T10) · orchestrator TASK_TYPES L40–49 agent adları K016 persona adları (A3-2) · bagimliliklar L181: "K016 → K006 bağımlılığı kodda gözlenmedi" | ✅ yön net: K006 ⇢ K016 |
| 2 | Girdi/çıktı sözleşmesi · K007 | çıktı şeması L26 (`@return` 5 alan) ✅ · girdinin saklanması için K006'da ayrı tablo **yok**; bagimliliklar §21 taslağı özellik alanlarını K006.6'ya verir (L568) | ✅ sözleşme evi doğru |
| 3 | Süreçle arayüz · K012 | `ai-master.md` L105: K012 satırı **"TASARIM İDDIASI"** etiketli (kaynak sütunu `—`) | ⚠️ komşu bile iddia düzeyinde |
| 4 | UI'a verilme · K011 | `ai-master.md` L107: K011 da **"TASARIM İDDIASI"** | ⚠️ aynı |
| 5 | Performans/girdi doğrulaması · evaluation | anayasa §13 "Gecikme & Timeout Bütçesi" (L218) + §29 "Ölçüm Kanca Noktaları" (L460) bu dalın **içinde**; kalite metriği evaluation'da (ai-master S4, L185) | ✅ ayrım tutarlı |
| 6 | Bulut ↔ yerel kanıt toplama · local-inference | `ai-master.md` L154 (local-inference = "cihaz üzerinde offline") + §11.2 L355: donanım eşiği **UNKNOWN** | ⚠️ ayrım için kanıt var, eşik yok |
| — | K016 dışı: prompt/orkestrasyon | `AIWorkflow` L66 (generatePrompt) + L106 (dispatchTask) = K016 çağrıları | ✅ taşınmadı, çağrılıyor |

## EK §5 — Alt ayrım noktaları (A1–A4 + yeni A5–A7)

| # | Ayrım | 2026-10-15 durumu | Kanıt |
|---|---|---|---|
| A1 | inference ↔ local-inference | **UNKNOWN** — iki cümle var (A4-2), ölçüt yok; v2'de ayrım yok | ai-master L149 ↔ L154 |
| A2 | inference ↔ model-management | **UNKNOWN** — "çalıştırma" vs "saklama" ayrımı yalnız tasarım yorumu | ai-master L148 ↔ L149 |
| A3 | Gerçek runtime ne zaman eklenecek? | ⚠️ IMPLEMENTED değil — grep 0 (C7) · yol haritasında ilgili kapı: ai-master §18 Faz 4 (L525: "Sunucu ONNX Runtime (aday) + cihaz embedding cache") | T13 · ai-master L525 |
| A4 | Model formatı seçimi | ⚠️ **UNKNOWN** (aday var, seçim yok — C10) | ai-master L256 · L318–347 · L352 |
| **A5 (yeni)** | Inference **sürecinin** sahibi: K006 mı K012 mi? | ⚠️ yeni soru — TASK_TYPES agent eşlemesi fiilen K016'ya devrediyor; K012 satırı "tasarım iddiası" | AIOrchestrator L40–49 · ai-master L105 |
| **A6 (yeni)** | `JSON_THROW_ON_ERROR` (workflow L70) + `memory->setSession` (L78) — hata/TTL davranışı kime ait? | ⚠️ yeni soru — anayasa §15 "Hata & Fallback Zinciri" (L254) bunu tanımlamalı | AIWorkflow L70, L78 |
| **A7 (yeni)** | Öneri limiti sözleşmesi: interface `int $limit = 20` (L28) ↔ workflow `?? 10` (L75) ↔ sabit `MAX_RECOMMENDATIONS = 20` (AIEngine L30) | ⚠️ üç farklı varsayılan (20/10/20) — hangisi bağlayıcı? | AIEngineInterface L28 · AIWorkflow L75 · AIEngine L30, L51 |

## EK §6 — Eski §3 "Disk kanıtları" yeniden doğrulaması

| İddia (L54–61) | 2026-10-15 kontrol | Sonuç |
|---|---|---|
| 1 · v2 satır 260 | `inference/index.md` L15, L32, L51 birebir ✅ | ✅ |
| 2 · open_questions satır 13 | L18–21 (C4) | ⚠️ konum yanlış, içerik doğru |
| 3 · workflow satır 75 | T10 ✅ | ✅ |
| 4 · sözleşme satır 26–28 | T: `AIEngineInterface` L26 `@return`, L28 imza ✅ | ✅ |
| 5 · AIEngine satır 28–32 | docblock 18–27 (C3) | ⚠️ konum yanlış |
| 6 · AIOrchestrator satır 41 | T12 ✅ | ✅ |
| 7 · runtime grep 0 | T13 ✅ (kapsam `shared/src/**`'a genişletildi) | ✅ |
| 8 · format seçimi UNKNOWN | C10 — aday var, seçim UNKNOWN | ℹ️ şekil |

**Toplam:** 5 ✅ · 3 ⚠️ konum/şekil · **yanlış yön 0**.

## EK §7 — Açık sorular (R1–R4 + yeni R5–R9)

| # | Soru | Durum | Kanıt |
|---|---|---|---|
| R1 | inference ↔ local-inference sınırı | ⚠️ VERIFICATION REQUIRED | A1 |
| R2 | Model formatı ve runtime | ⚠️ UNKNOWN (aday var, seçim yok) | C10 · T8 |
| R3 | Inference süreci K012'de mi K006'da mı? | ⚠️ VERIFICATION REQUIRED | A5 |
| R4 | Mevcut PHP motor mu harici model mi (geçiş planı)? | ⚠️ UNKNOWN | C8 + ai-master L525 (Faz 4 hâlâ ⏳/kural düzeyi) |
| **R5 (yeni)** | Inference çıktısının saklanması (workflow L78) hangi K'ya ait — C5 | ⚠️ VERIFICATION REQUIRED | bagimliliklar L193 |
| **R6 (yeni)** | Limit varsayılanı 20 mi 10 mu (A7)? | ⚠️ VERIFICATION REQUIRED | üç kaynak |
| **R7 (yeni)** | K012/K011 "tasarım iddiası" etiketleri ne zaman kanıta dönüşecek? | ⚠️ üst karar | ai-master L105, L107 |
| **R8 (yeni)** | Anayasa §12 çağrı sözleşmesi (L200) ile interface `@return` şeması (L26) birebir mi? | ⚠️ çapraz okuma gerek | anayasa L200 |
| **R9 (yeni)** | Shadow → canary → A/B rollout usulü (anayasa §27, L434) hangi altyapıda çalışacak? | ⚠️ VERIFICATION REQUIRED | anayasa L434 · evaluation altyapısı da yok (EK evaluation §15.1 G4) |

**Kapanış (genişletildi):** R1–R9 kapanmadan bu dosyada **runtime/model seçimi iddiası yazılmaz** —
eski kapanış cümlesi (L72–73) aynen bağlayıcıdır; bu ek yalnız numaralandırmayı genişletti.

## EK §8 — Kardeş dosya ve indeks cross'ları

### EK §8.1 `inference/index.md` [Satır: 6–12, 18–21, 47–54]

| Kontrol | Değer |
|---|---|
| children | 6 dosya: anayasa · bilesenler · kapsam · ornekler · dogrulama · web-arastirma |
| evidence | "v2 §2.6 mini-ağaç ... satır **260**" ✅ ai-master L149 ile birebir |
| validation | "2026-10-12 — satır 260 ile karşılaştırıldı: ad birebir" ✅ |
| open_questions | Rol tanımı yok · kademe-3 yok · `depends_on` boş — üçü de hâlâ geçerli |
| Durum tablosu | "adı geçiyor ✅ (260) · rol ❌ · kademe-3 ⏳ ADR-097 K4 · status draft" ✅ |
| `»` kusuru | L37, L43–45 — diğer dal indeksleriyle aynı bilinen kusur, düzeltilmedi |
| group | `group: mimari` (L22) = bu dosyanın frontmatter'ı (L10) |

### EK §8.2 Kardeş dosya ilişkileri (yalnız referans — dokunulmadı)

| Dosya | Bu ekle çapraz |
|---|---|
| `inference-anayasa.md` | 33 başlık (T15) — §12 Çağrı Sözleşmesi (L200) · §13 Gecikme Bütçesi (L218) · §15 Hata & Fallback (L254) · §18 Workflow Zinciri (L302) · §27 Rollout (L434) · §30 Kapı Özeti (L475) |
| `inference-dogrulama.md` | §14 URL kaynağı L27–30 |
| `inference-web-arastirma.md` | §14 URL kaynağı L42–49 |
| `inference-ornekler.md` | ONNX dil örnekleri (L37–281 arası) — 2026-10-14 tarihli, bu eke **kopyalanmadı** |
| `inference-bilesenler.md` | dokunulmadı |

## EK §9 — `ai-master.md` inference cross'u

| Satır | İçerik | Not |
|---|---|---|
| 78 | "3. **Çalıştırır** — modeli barındırıp sonucu üretir (`model-management` · `inference` · `local-inference`)" | rol fiili |
| 149 | çocuk satırı (v2 260) | ana kayıt |
| 184 | S3 birincil · repo kanıtı `AIOrchestrator::dispatchTask L71–100` · etiket **⚠️ kısmi** | kısmi kanıt: dispatch var, model runtime yok |
| 194–196, 200 | kapsama: model-management/inference/local-inference → S3 | S3 üçlüsü |
| 101 | K016 sınırı ✅ | §2 |
| 105, 107 | K012/K011 = TASARIM İDDIASI | A5/R7 |
| 256 | format/runtime UNKNOWN · `-dogrulama` §7/4 | C6 |
| 318–347 | §11.1 aday envanter (ONNX L325 vb.) "seçim YAPILMADI" | R2 |
| 352–357 | §11.2 6 UNKNOWN | R2 |
| 525 | §18.1 Faz 4: "Sunucu ONNX Runtime (aday) + cihaz embedding cache (K-12) · MLflow registry (aday) — üretim model/format/runtime UNKNOWN'ları ADR ile dolar · R-04 · ⏳" | yol haritası |
| 404 (§13.2) | `inference/` 2026-10-13 toplamı — bkz. EK §13 | bayatlaştı |

## EK §10 — `yapay-zeka-bagimliliklar.md` inference cross'u

| Satır | İçerik | Bu dal etkisi |
|---|---|---|
| 9 | `K006.3-inference` | node kimliği |
| 51 | `AIEngine.php` L15 → `AIEngineInterface` → "iç (sözleşme) → K006.3" | sözleşme evi |
| 53 | `AIOrchestrator.php` L13 → **"K006 · K016 sınırı"** | A5'in dayanağı |
| 70–71 | `AIEngine` → "K006.3 / K006.8" · `AIOrchestrator` → "K006 / K016 sınırı" | ikili aidiyet |
| 77 | "Bulgu: K006'nın tek gerçek ürün sınıfı `AIWorkflow`, 5/6 bağımlılığını K016 profilindeki ..." | A1-2'nin dayanağı |
| 137–141 | adaylar: ONNX Runtime · PyTorch · TensorFlow · TensorBoard · vLLM → hepsi "⚠️ aday" | §14 |
| 149–151 | whisper.cpp · llama.cpp · Ollama → **K006.4-local-inference** | komşu |
| 190 | eğitim verisi kaynağı UNKNOWN | R2 |
| 269 | "V4 ML model sürümü → model kaydı yok → ❌ UNKNOWN → K006.5" | inference öncesi koşul |
| 333 | "C5 Donanım (GPU/NPU) → ❌ UNKNOWN → K006.4" | A1 |

## EK §11 — Anayasa başlık cross'u (33 başlık, 2026-10-15 `grep`)

| Başlık | Satır | Çapraz |
|---|---|---|
| §1 ELI15 — Bu alan ne, ne DEĞİL? | 35 | bu dosya §1–§2 |
| §2 Bozulmaz İlkeler (invariants) | 52 | — |
| §3 Öğrenme Türü Anayasası | 73 | R2 |
| §4 Model Ailesi Sınırı | 87 | R2 · C10 |
| §5 Önyargı & Denklik | 102 | — |
| §6 Etik & Regülasyon Sınırı | 115 | — |
| §7 Gizlilik & Veri Anayasası | 128 | C5 |
| §8 K016-AI Sınırı (bağlayıcı) | 139 | EK §4/1 |
| §9 Terim Sözlüğü | 152 | — |
| §10 Kapsam Dışı | 175 | EK §4 |
| §11 Açık Sorular (anayasa kilidi) | 187 | EK §7 |
| §12 Çağrı Sözleşmesi (dışa kapısı) | 200 | A2-2 · R8 |
| §13 Gecikme & Timeout Bütçesi | 218 | EK §4/5 · R9 |
| §14 Cache, Batch ve Tekrar Ekonomisi | 237 | A7 |
| §15 Hata & Fallback Zinciri | 254 | A6 |
| §16 Determinizm, Tekrarlanabilirlik, Sıcaklık | 270 | R2 |
| §17 Güvenlik Kapıları | 284 | — |
| §18 Workflow Çağrı Zinciri (5 akış) | 302 | A1-1 · T10 |
| §19 Kalite & Ölçüm Kapıları (anayasa gate) | 320 | R9 |
| §20 Sürüm & Değişiklik Kilidi | 336 | R2 |
| §21 Çapraz Bütünlük | 350 | — |
| §22 Kardeş Eşlemesi | 363 | EK §8.2 |
| §23 İlişkili Dosyalar | 377 | EK §8 |
| §24 Kaynaklar | 388 | §14 |
| §25 Red-Team Soruları | 407 | EK §7 |
| §26 Revizyon Kaydı | 422 | bu ek |
| §27 Ek Usul — Rollout (shadow → canary → A/B) | 434 | R9 |
| §28 Ek Usul — Model Sürümleme Sözleşmesi | 448 | R2 |
| §29 Ek Usul — Ölçüm Kanca Noktaları | 460 | EK §4/5 |
| §30 Ek Usul — Kapı (Gate) Özeti | 475 | R9 |
| §31 Ek Usul — SSS | 492 | — |
| §32 Ek Usul — Revizyon Eşiği Tablosu | 511 | bu ek |

## EK §12 — Korunan maddeler (kapı listesi)

| # | Madde | Durum |
|---|---|---|
| P-1 | Eski içerik L1–83 tam önek olarak korundu | ✅ |
| P-2 | Frontmatter (L1–11, `group: mimari`) değişmedi | ✅ |
| P-3 | `inference/index.md` yazılmadı | ✅ |
| P-4 | Diğer 5 kardeş `-*.md` yazılmadı | ✅ |
| P-5 | `.ai/AGENTS.md` · `.ai/CLAUDE.md` · `.ai/architecture/index.md` yazılmadı | ✅ |
| P-6 | `*.php/js/css/sql` yazılmadı (AIWorkflow · AIEngine · AIEngineInterface · AIOrchestrator okundu) | ✅ |
| P-7 | `git commit` atılmadı | ✅ |
| P-8 | Yeni URL yok (§14 yalnız 2026-10-12 kayıtları) | ✅ |
| P-9 | `// ...` · `TBD` · doldurma satırı yok; alıntılardaki `//` kaynak kodun kendi satırıdır | ✅ |
| P-10 | Mojibake yok · her iddia dosya+satır ya da taşınan URL ile kanıtlandı | ✅ |

## EK §13 — Ölçüm, sayım ve sonuç

| Aşama | Yöntem | Sonuç |
|---|---|---|
| Ek öncesi (2026-10-15) | `read` tam okuma (L1–83) | **83 satır** |
| Ek sonrası (2026-10-15) | `read` offset 9999 → out-of-range mesajı | **520 satır** |
| Eşik | ≥500 | **✅ GEÇTİ (+20)** |

### EK §13.1 Bu ek sayımı

| Metrik | Değer |
|---|---|
| EK bölüm sayısı | 15 (EK §0–§15) |
| Alıntı bloğu | 6 (3 PHP + 2 metin + 1 önsöz) |
| Tarama satırı | T1–T22 |
| Çelişki kaydı | 13 (C1–C13) |
| Açık soru | 4 eski + 5 yeni = 9 (R1–R9) |
| Taşınan URL | 12 (0 yeni) |
| Kapı ihlali | 0 |
| Beyazliste dışı yazım | 0 |
| `git commit` | yok |

## EK §14 — Taşınan URL'ler (2026-10-12 — 0 yeni URL)

Kaynak: `inference/inference-web-arastirma.md` [Satır: 42–49] · `inference/inference-dogrulama.md` [Satır: 27–30].

| # | URL (taşındı) | Kaynak satır | Rol |
|---|---|---|---|
| 1 | https://onnxruntime.ai/docs/ | web-arastirma 42 · dogrulama (aila) | taşınabilir çıkarım adayı |
| 2 | https://pytorch.org/ | web-arastirma 43 | framework adayı |
| 3 | https://www.tensorflow.org/ | web-arastirma 44 | framework adayı |
| 4 | https://vllm.ai/ | web-arastirma 45 | LLM sunum adayı |
| 5 | https://docs.vllm.ai/ | web-arastirma 46 | sürüm kanıtı |
| 6 | https://github.com/openai/whisper | web-arastirma 47 | ses modeli çıkarım adayı (110.308★ · MIT) |
| 7 | https://huggingface.co/docs/hub/ | web-arastirma 48 | model kaynağı (paketleme/dağıtım/sürümleme) |
| 8 | https://github.com/ggml-org/llama.cpp | web-arastirma 49 | yerel/ONNX-dışı çıkarım adayı (130.787★ · MIT) |
| 9 | https://onnxruntime.ai/docs/api/python/index.html | dogrulama 27 | "framework'ten bağımsız skoring motoru" |
| 10 | https://onnxruntime.ai/docs/api/python/api_summary | dogrulama 28 | `InferenceSession` · `run(output_names, input_feed)` |
| 11 | https://onnxruntime.ai/docs/api/python/tutorial.html | dogrulama 29 | train → ONNX → runtime akışı |
| 12 | https://onnx.ai/onnx/repo-docs/IR.html | dogrulama 30 | ModelProto — taşınabilir serialize formatı |

**Kopyalanmayanlar:** `inference-ornekler.md` içindeki 2026-10-14 tarihli örnek URL'leri
(L37, L49, L62–63, L117–118, L168, L213, L242–244, L278–281) ve ai-master §16 genişleme
kaynakları bu listeye **alınmadı** — 2026-10-12 havuzu dışındadır; yalnız dosya·satır
referansı verildi.

## EK §15 — Uygulama yüzeyi: workflow ve yol haritası kesişimi (ek kanıt)

> Bu bölüm EK §13 ölçümünden ÖNCE eklenen tamamlayıcı kanıttır; iddiaların
> tamamı taze okumadan gelir (2026-10-15).

### EK §15.1 Ek tarama

| # | Dosya | Satır | Bulgu |
|---|---|---|---|
| T17 | `.ai/architecture/K006-yapay-zeka/ai-master.md` | 525 | §18.1 Faz 4: "Sunucu ONNX Runtime (aday) + cihaz embedding cache (K-12) · MLflow registry (aday)" — durum **⏳**, kabul: "üretim model/format/runtime UNKNOWN'ları ADR ile dolar" |
| T18 | `.ai/architecture/K006-yapay-zeka/ai-master.md` | 530–532 | §18.2 H1–H7: H2 "seçim = ADR, aday kodda yok" · H4 "placeholder üretim sayılmaz" · H7 her faz kapanışında `-dogrulama.md`'ye iddia·URL·tarih satırı |
| T19 | `shared/src/AI/AIWorkflow.php` | 99–139 | `audioAnalysisWorkflow`: `dispatchTask('analysis', …, 'high')` → `engine->analyzeAudio()` → `knowledge->search()` → `toolCalling->callTool('db-query', …)` (prepared statement `:bpm :key_sig :energy :id`) |
| T20 | `shared/src/AI/AIWorkflow.php` | 112–114 | Kaynak kod yorumu: "analyzeAudio() 'genre' anahtarını döndürmez → tür bilgisi her zaman boş"; sorgu `"{key} {mood} {genre}"` formülüyle yine de kurulur |
| T21 | `shared/src/AI/AIWorkflow.php` | 147–158 | `autoEQWorkflow`: `dispatchTask('eq-optimization', …, 'medium')` — `AIOrchestrator` TASK_TYPES (L40–49) ile aynı sözlük |
| T22 | `shared/src/AI/AIWorkflow.php` | 82–91 | `recommendationWorkflow` dönüşü: `metadata` = `task_id` · `prompt_tokens` · `knowledge_hits`, ayrıca `execution_time` |

### EK §15.2 Alıntı — `AIWorkflow::audioAnalysisWorkflow` (verbatim)

**Dosya:** `shared/src/AI/AIWorkflow.php` [Satır: 105–127]

```php
        // 1. Task dispatch
        $taskId = $this->orchestrator->dispatchTask('analysis', $request, 'high');

        // 2. Audio analysis
        $analysis = $this->engine->analyzeAudio($filePath);

        // 3. Knowledge enrich — benzer şarkıları bul
        // analyzeAudio() 'genre' anahtarını döndürmez -> tür bilgisi her zaman boş.
        $genre = '';
        $query = "{$analysis['key']} {$analysis['mood']} {$genre}";
        $knowledgeResults = $this->knowledge->search($query, 3);

        // 4. DB'ye kaydet (track_id varsa) — prepared statement ile SQL injection koruması
        if ($trackId !== null) {
            $bpm    = (float)$analysis['bpm'];
            $keySig = (string)$analysis['key'];
            $energy = (float)$analysis['energy'];
            $this->toolCalling->callTool('db-query', [
                'sql'      => "UPDATE tracks SET bpm = :bpm, key_sig = :key_sig, energy = :energy WHERE id = :id",
                'params'   => ['bpm' => $bpm, 'key_sig' => $keySig, 'energy' => $energy, 'id' => (int)$trackId],
                'database' => 'coremusic_musics',
            ]);
        }
```

### EK §15.3 Çelişki kaydı (devam)

| # | İddia / kaynak | Disk kanıtı | Karar |
|---|---|---|---|
| C12 | `AIWorkflow.php` L156 kaynak yorumu `// 2. Room profile分析` — Çince karakter karışımı (mojibake benzeri) | `shared/src/AI/AIWorkflow.php` [Satır: 156] | PHP dosyası bu görevde **salt-okunur** (beyazliste dışı) → düzeltilmedi, yalnızca kaydedildi |
| C13 | `ai-master.md` §18.3 "K020-recomenataion" yazımı | `ai-master.md` [Satır: 540] | Vault yazım hatası; append-only kural gereği düzeltilmedi, satır referansıyla kaydedildi |

### EK §15.4 Yol haritası kesişimi (ai-master §18.1 ↔ bu kapsam)

| Faz | Inference rolü | Durum (kaynak) |
|---|---|---|
| 2 — Ses analizi gerçekleştirmesi | `extractFeatures()` placeholder'ı → gerçek pipeline; inference katmanına kalite verisi besler | 🔄 şartname hazır (ai-master L523) |
| 3 — Öneri kolları | Çevrimdışı metrik (Precision@k · nDCG@k) inference çıktısını değerlendirir | ⏳ (ai-master L524) |
| 4 — Hibrit inference & model-management | **Bu dosyanın ana konusu:** ONNX Runtime (aday) + cihaz embedding cache + MLflow registry (aday) | ⏳ · ADR bekliyor (ai-master L525) |
| 5 — Evaluation & KVKK | Drift izleme + rıza kanalı inference logging'ini şart koşar | ⏳ (ai-master L526) |

> **H2 bağlayıcılığı (ai-master L531):** ONNX Runtime bu dosyada da yalnız
> **aday**dır; `shared/src/**` içinde `onnx|tflite|torch|pmml|joblib` isabeti 0'dır
> (EK §2 T-taraması) — "seçilmiş teknoloji" iddiası yazılamaz.

### EK §15.5 Ek kapılar

| # | Kapı | Durum |
|---|---|---|
| P-11 | `AIWorkflow.php` yalnız okundu; PHP'ye yazım yok | ✅ |
| P-12 | §18 yol haritası "plan" olarak taşındı; "seçilmiş" dili yok (H2) | ✅ |
| P-13 | EK §15 de yalnız 2026-10-15 taze okuma kanıtı içerir; yeni URL yok | ✅ |

---

**Ek Authority:** Bayram Ali / Vault Steward · **Tarih:** 2026-10-15 ·
**Eklenti:** EK §0–§15 (append-only · 2026-10-12 tabanı korunmuştur · 0 yeni URL)
