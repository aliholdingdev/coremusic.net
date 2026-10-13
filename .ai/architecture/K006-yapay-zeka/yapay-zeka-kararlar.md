---
node_id: K006-yapay-zeka-kararlar
node_type: domain
status: verified
parent_id: "K000–K5000 Architecture Registry"
children:
  - K006.1-audio-intelligence
  - K006.2-evaluation
  - K006.3-inference
  - K006.4-local-inference
  - K006.5-model-management
  - K006.6-music-analysis
  - K006.7-personalization
  - K006.8-recommendation
  - K006.9-testing
depends_on:
  - K004-shared
  - K016-AI
evidence:
  - "ADR-097 §5 (isimlendirme) — 2026-10-12 okundu"
  - ".ai/architecture/index.md §5 satır 6 · §6 satır 94 (K006↔K016) — 2026-10-12"
  - "v2 §2.6 satır 251–268 (9 çocuk) · §2.16 (K016 sınırı) — 2026-10-12"
validation: "2026-10-12 · 12 karar, her biri kaynak+gerekçe ile · açık kararlar açık olarak işaretli · status=verified"
open_questions:
  - "Öneri sahipliği 3-way (K006 · K008 · K020) — ADR bekliyor, kesin karar YOK"
  - "AIWorkflow içindeki hardwareAnalysis/faultPrediction akışlarının sahibi (K006 mı K016 mı) — belirsiz"
  - "K006.2-evaluation için izleme altyapısı seçimi (Evidently adayı) — kurulmadı, karar değil aday"
group: mimari
---

# K006 — Yapay Zeka · Kararlar

> **Bu dosya ne değildir:** yeni ADR üretmez. Buradaki kararlar ya (a) **kabul edilmiş** kaynaklardan
> (ADR-097, v2 ağaç, registry) türetilmiştir, ya (b) **disk gözlemi** ile sabitlenmiştir, ya da
> (c) **açık** olarak işaretlenmiştir. Açık karar, karar gibi sunulmaz.

---

## §A — Kabul Edilmiş Kararlar (kaynaklı)

### K-01 · Node adı ve dosya şeması
- **Karar:** kök düğüm `K006-yapay-zeka`; master dosya `ai-master.md`; 12 kök MD + her çocukta 6 MD.
- **Kaynak:** ADR-097 §5.1 (Türkçe rol eki adlandırması) · §5.4 (`ai-master.md` adı korunur) ·
  `.ai/architecture/index.md` §5 satır 6 (master = `ai-master.md`).
- **Gerekçe:** registry ile dosya adı eşleşmezse §6 satır kırılır; ADR-097 donmuş karardır.
- **Durum:** ✅ kabul (2026-10-12).

### K-02 · 9 çocuk v2'den birebir
- **Karar:** `audio-intelligence` · `evaluation` · `inference` · `local-inference` ·
  `model-management` · `music-analysis` · `personalization` · `recommendation` · `testing`.
- **Kaynak:** v2 §2.6 satır 251–268 (mini-ağaç satır 255–268).
- **Gerekçe:** yazım hatası dahil adlar değiştirilemez; yazım hatası `recommendation` değil
  **K020** tarafındaki `recomenataion`'dadır (registry §6) — K006'daki ad düzdür.
- **Durum:** ✅ kabul. Yeni çocuk/dizin ekleme **yasak**.

### K-03 · K006 ↔ K016 kapsam ayrımı
- **Karar:** K006 = **ML ürün yüzeyi** (öneri, ses analizi, tahmin, kişiselleştirme). K016 = **ajan/MCP/
  RAG/prompt/altyapı**. `KnowledgeBase` · `MemorySystem` · `PromptEngine` · `ToolCalling` K016'ya ait;
  `AIWorkflow` onları **çağırır ama sahibi değildir**.
- **Kaynak:** v2 §2.6 ve §2.16 · registry §6 satır 94 (`K006-yapay-zeka (ML ürün) ↔ K016-AI (ajan
  altyapısı) — ikisi de meşru, ayrı domain`).
- **Disk kanıtı:** 4 sınıf `use CoreMusic\Contracts\AI\{KnowledgeBaseInterface|MemorySystemInterface|
  PromptEngineInterface|ToolCallingInterface}` (her dosya L13).
- **Gerekçe:** iki domain'i birleştirmek registry §6 satırını kırar; ikisi de "meşru" olduğu için
  biri diğerine tahsis edilemez.
- **Durum:** ✅ kabul (2026-10-12).

### K-04 · Kod yüzeyi yalnız `shared/src/AI/` + `shared/src/Contracts/AI/`
- **Karar:** K006'nın gerçek kod karşılığı bu iki dizindir (7 PHP + 1 CLAUDE.md · 6 interface).
- **Kaynak:** disk glob, 2026-10-12 · `shared/AGENTS.md` §2 envanter satırı
  (`src/AI/` + `src/Contracts/AI/` — ADR-030).
- **Durum:** ✅ kabul.

### K-05 · Sözleşme katmanı arayüz adları
- **Karar:** tek gerçek arayüz seti `AIEngineInterface` · `AIOrchestratorInterface` ·
  `KnowledgeBaseInterface` · `MemorySystemInterface` · `PromptEngineInterface` ·
  `ToolCallingInterface` (6 arayüz · 33 imza · namespace `CoreMusic\Contracts\AI`).
- **Kaynak:** disk grep, 2026-10-12 (bkz. `yapay-zeka-bilesenler.md` §4).
- **Gerekçe:** `*Contract` adlandırması önceki turda uydurulmuştu; diskte yok (§9 düzeltme kaydı).
- **Durum:** ✅ kabul; uydurma adlar **kullanılamaz**.

### K-06 · Kök dosya adlarının Türkçe rol eki şeması
- **Karar:** `yapay-zeka-{kapsam|bagimliliklar|dogrulama|web-arastirma|bilesenler|kod-ornekleri|
  kararlar|riskler|test-stratejisi|evrensel-dil}.md` + `index.md` + `ai-master.md` = 12.
- **Kaynak:** ADR-097 §5.1 · görev manifesti (bu revizyon).
- **Durum:** ✅ kabul (2026-10-12 tamamlandı: 12/12).

---

## §B — Disk Gözlemiyle Sabitlenen Kararlar (kod gerçeği)

### K-07 · Motor tek sınıf, soyutlanmamış
- **Gerçek:** tahmin/öneri/analiz motoru `AIEngine` tek sınıf (382 satır); tek bağımlılık
  `ConfigManager` (L34). Motor arayüzü/interface swap **yok** (`AIEngineInterface` var ama
  alternatif implementasyon tek).
- **Karar etkisi:** K006.3-inference için "motor değiştirilebilirliği" mevcut değil — yeni motor
  `AIEngineInterface` implementasyonu olarak girer, K006.3 tasarımının varsayımı budur.
- **Durum:** ✅ gözlem (2026-10-12).

### K-08 · Öneri hattı fiilen content-based'e düşüyor
- **Gerçek:** `getCollaborativeScores` L209–218 → `[]` · `getContentBasedScores` L223–235 → `[]` ·
  hibrit ağırlık 0.6/0.4 (L65) · `MIN_SCORE=0.7` (L31).
- **Karar etkisi:** K006.8 öncelikli açığı CF kolunun doldurulmasıdır; ağırlıkların revizyonu
  **tasarım onayı** gerektirir (kod gömülü, config değil).
- **Durum:** ✅ gözlem (2026-10-12).

### K-09 · Ses analizi placeholder
- **Gerçek:** `extractFeatures` L315–330 sabit değer döner; `$filePath` kullanılmaz.
- **Karar etkisi:** K006.1 / K006.6 "gerçek analiz" iddiası **üretemez**; hedef C++ DSP engine
  (yorum L317). Bu çocuklar kapsam olarak tasarlanır, var olan kod gibi sunulmaz.
- **Durum:** ✅ gözlem (2026-10-12).

### K-10 · Workflow kontrat dışı
- **Gerçek:** `AIWorkflow`'un interface'i yok (L18); 6 somut bağımlılık (L20–41); K016 sınıflarını
  doğrudan çağır (ör. `knowledge->search` L63).
- **Karar etkisi:** arayüz üzerinden soyutlanmadığı için test edilemezlik K006.9'un açıdır; K016 çağrısı
  sınır ihlali değil, **kayıtlı bağımlılık** (K-03).
- **Durum:** ✅ gözlem (2026-10-12).

---

## §C — Açık Kararlar (kesin değil — ADR/üzeri karar bekliyor)

### Q1 · Öneri sahipliği 3-way — **AÇIK**
- **Durum:** K006-recommendation ↔ K008-recommendation ↔ K020-recomenataion üçlüsünde
  **hiçbiri kesin sahip değil** (registry §6 · `ai-master.md` §7/2). Bu dosyaların hiçbiri
  "sahibi K006'dır" **demek zorunda değildir** ve dememiştir.
- **Kapanış koşulu:** ADR ile sahiplik ataması. Kanıt gelene kadar `⚠️ VERIFICATION REQUIRED`.
- **Durum:** 🔴 açık (2026-10-12).

### Q2 · `hardwareAnalysisWorkflow` / `faultPredictionWorkflow` sahibi — **AÇIK**
- **Durum:** 5 akışın 2'si (L199–239 · L241–271) ne K006 ne K016'da açıkça tanımlı;
  `ai-master.md` §8-Q6'ya işaretli.
- **Kapanış koşulu:** akış bazlı sahiplik kararı.

### Q3 · İzlence (monitoring) altyapısı — **KARAR DEĞİL, ADAY**
- **Durum:** Evidently (`https://www.evidentlyai.com/`) doğrulanmış **adaydır**; repo'da kurulum
  **yok** (`AIPerformanceMonitor`/`AIQualityManager` grep: 0). "Kullanıyoruz" denemez;
  "aday" denir.
- **Kapanış koşulu:** K006.2 tasarımda seçim + kurulum kanıtı.

### Q4 · Sürümlü AI bağımlılığı — **AÇIK**
- **Durum:** `shared/composer.json`'da ML/öneri paketi **yok**; Python bağımlılığı için lockfile
  da yok → versiyonlar `UNKNOWN`. Adaylar `yapay-zeka-bagimliliklar.md` §3'te "kurulmadı" etiketiyle.
- **Kapanış koşulu:** bağımlılık ekleme kararı (ADR) + sürüm kanıtı.

### Q5 · `AIOrchestrator` K006 yüzeyi — **KISMEN AÇIK**
- **Durum:** `dispatchTask('recommendation', ...)` (AIWorkflow L59) K006 verisini K016'ya taşır;
  task sonucu beklemez (asenkron). K006'nın K016'dan **okuma** sözleşmesi tanımsız.
- **Kapanış koşulu:** K006↔K016 veri sözleşmesi ADR'si.

---

## §D — Karar Kaydı Matrisi

| # | Karar | Tür | Kaynak | Durum |
|---|---|---|---|---|
| K-01 | `ai-master.md` + 12/6 şeması | isim | ADR-097 §5.1/§5.4 · registry §5/6 | ✅ |
| K-02 | 9 çocuk birebir | kapsam | v2 §2.6 L251–268 | ✅ |
| K-03 | K006↔K016 ayrımı | kapsam | v2 §2.6/§2.16 · registry §6/94 | ✅ |
| K-04 | Kod yüzeyi 2 dizin | envanter | disk glob 2026-10-12 | ✅ |
| K-05 | 6 arayüz/33 imza | sözleşme | disk grep 2026-10-12 | ✅ |
| K-06 | Türkçe ek adlandırma | isim | ADR-097 §5.1 | ✅ |
| K-07 | Tek motor, soyutsuz | kod | AIEngine L34 | ✅ gözlem |
| K-08 | CF kolu boş | kod | AIEngine L209–218 | ✅ gözlem |
| K-09 | Ses analizi placeholder | kod | AIEngine L315–330 | ✅ gözlem |
| K-10 | Workflow kontratsız | kod | AIWorkflow L18/L20–41 | ✅ gözlem |
| Q1 | Öneri sahipliği | sahiplik | registry §6 | 🔴 açık |
| Q2 | 2 akış sahibi | sahiplik | ai-master §8-Q6 | 🔴 açık |
| Q3 | İzleme altyapısı | teknoloji | aday only | 🔴 açık |
| Q4 | AI paket sürümleri | bağımlılık | composer.json (yok) | 🔴 açık |
| Q5 | K006↔K016 veri sözleşmesi | sözleşme | AIWorkflow L59 | 🔴 açık |

---

## §E — Yapılmayanlar (negatif kararlar)

| Karar | Neden yapılmadı |
|---|---|
| Yeni çocuk dizini açmak | v2 §2.6 sabit; ekleme yasak |
| `*Contract` adlarını kullanmak | diskte yok (uydurma) |
| Evidently'i "kurulu" saymak | repo'da 0 satır |
| Öneri sahibini K006 ilan etmek | Q1 açık, ADR yok |
| Python paket versiyonu yazmak | lockfile yok → UNKNOWN |
| K016 sınıflarını K006'ya taşımak | registry §6/94 ihlali |
| `git commit` atmak | subagent yetkisi dışı (§14.9) |

---

## §F · Kaynaklar

1. `ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md` §5 — 2026-10-12 okundu.
2. `.ai/architecture/index.md` §5 satır 6 · §6 satır 94 — 2026-10-12.
3. `coremusic-katmanli-mimari-agaci-v2.md` §2.6 (L251–268) · §2.16 — 2026-10-12.
4. `shared/src/AI/*.php` — 7 dosya grep/okuma, 2026-10-12.
5. `yapay-zeka-bilesenler.md` §4/§9 — sözleşmeler + düzeltme kaydı.

---

`status: verified` · `validation: 2026-10-12 · 10 kabul/gözlem + 5 açık karar, her satır kaynaklı, açık kararlar açık işaretli`
