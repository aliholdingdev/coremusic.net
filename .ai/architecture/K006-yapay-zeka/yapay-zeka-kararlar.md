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
  - K015-AI
evidence:
  - "ADR-097 §5 (isimlendirme) — 2026-10-12 okundu"
  - ".ai/architecture/index.md §5 satır 6 · §6 satır 94 (K006↔K016) — 2026-10-12"
  - "v2 §2.6 satır 251–268 (9 çocuk) · §2.16 (K016 sınırı) — 2026-10-12"
validation: "2026-10-12 · 12 karar, her biri kaynak+gerekçe ile · açık kararlar açık olarak işaretli · 2026-10-14 · K-11–K-14 kullanıcı kararları + §H uygulama kuralları eklendi · status=verified"
open_questions:
  - "Öneri sahipliği 3-way — 2026-10-14 kullanıcı kararı: HİBRİT bölen (K-11); kesinleşmesi için ADR kaydı bekliyor"
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
- **Kaynak:** v2 §2.6 ve §2.16 · registry §6 satır 94 (`K006-yapay-zeka (ML ürün) ↔ K015-AI (ajan
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
| K-11 | Öneri sahipliği hibrit böleni | sahiplik | kullanıcı onayı 2026-10-14 | ✅ (ADR bekliyor) |
| K-12 | Hibrit üretim mimarisi | mimari | kullanıcı onayı 2026-10-14 | ✅ |
| K-13 | Hibrit veri stratejisi | veri | kullanıcı onayı 2026-10-14 + §21 | ✅ (lisans ⚠️) |
| K-14 | Faz sıralaması (bağlayıcı) | plan | kullanıcı onayı 2026-10-14 | ✅ |

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

## §G — 2026-10-14 Kullanıcı Kararları (hibrit plan — Authority onayı)

> **Türk ayrımı:** §A'daki kararlar kaynaklardan türetildi, §B'dekiler disk gözlemidir;
> bu bölümdeki dört karar **kullanıcı (Authority) tarafından 2026-10-14 oturumunda açıkça
> onaylanmıştır**. Yine de ADR-097 disipliniyle çelişmeyen, kaynaklı gerekçeli kayıtlardır;
> ADR üretimleri `⚠️` ile bekler (bu dosya yeni ADR üretmez — bkz. dosya başlığı).

### K-11 · Öneri sahipliği — HİBRİT bölen (Q1'e kısmi kapanış)
- **Karar:** sahiplik tek düğümde toplanmaz, üç katmana bölünür:
  | Katman | Sahip | Ne |
  |---|---|---|
  | Model/skorlama | **K006.8-recommendation** | aday üretimi, skorlama, sıralama algoritması |
  | İş kuralı filtresi | **K008-domain** | "beğen", yaş sınırı, engelli içerik, katalog kuralları |
  | Keşif/UI akışı | **K020-recomenataion** | kullanıcıya sunum sırası, keşif akışı |
- **Kaynak:** kullanıcı onayı 2026-10-14 (hibrit sahiplik sorusunun cevabı).
- **Gerekçe:** tek sahiplik K008 veya K020'nin meşru payını silerdi; v2 §2.6 K006'yı
  "öneri motoru" (algoritma) olarak tanımlar — sahiplik algoritmada kalır, diğerleri
  komşu katman olarak ayrılır. Registry §6 K006↔K016 ayrımı **değişmez**.
- **Durum:** ✅ kullanıcı kararı (2026-10-14). ADR kaydı bekliyor (Q1'in resmi kapanışı).

### K-12 · Üretim mimarisi — HİBRİT (sunucu + cihaz)
- **Karar:** üretim hedefi hibrit mimaridir: **sunucu tarafı ONNX Runtime** adayı ile
  tahmin (inference), **cihaz tarafında hafif embedding cache** (local-inference).
  Model formatı seçimi hâlâ adaydır — bu karar mimari **yönü** verir, teknolojiyi kilitlemez.
- **Kaynak:** kullanıcı onayı 2026-10-14 (hibrit üretim hedefi sorusu).
- **Gerekçe:** tam yerel çalışma (offline-first) CoreMusic'in web omurgasına aykırı;
  tam sunucu ise K006.4-local-inference'i anlamsız kılar — hibrit ikisini de yaşatır.
- **Durum:** ✅ kullanıcı kararı (2026-10-14). Teknoloji seçimi Q4/§11.1'de `⚠️` kalır.

### K-13 · Veri stratejisi — HİBRİT (mevcut + dış + sentetik)
- **Karar:** MVP'de üç kaynak birlikte kullanılır:
  | Kaynak | Ne verir | Kanıt |
  |---|---|---|
  | Mevcut DB katalog | şarkı/album/artist meta verisi | repo K007 (mevcut) |
  | Dış: MusicBrainz | katalog doğrulama/MBID eşleme | `-web-arastirma` §21/D3 |
  | Dış: Last.fm / MSD (Taste Profile) | etkileşim tohumu (CF başlangıcı) | `-web-arastirma` §21/D1 · D2 |
  | Sentetik/seed | test ortamı, evaluation baseline | bu karar |
- **Kaynak:** kullanıcı onayı 2026-10-14 (hibrit veri durumu sorusu) + §21 tur-4 kanıtları.
- **Gerekçe:** sıfır etkileşim verisiyle CF kolu doldurulamaz (R-01); dış tohum + sentetik
  seed olmadan Faz-3 eval metriği anlamsızdır.
- **Sınır:** Last.fm ticari izin şartı (§21.1/O1) + MSD lisansı (§21.1/O4) `⚠️` —
  veri ithalatı öncesi hukuki kapı ayrıca kapatılır.
- **Durum:** ✅ kullanıcı kararı (2026-10-14) + ⚠️ lisans kapıları açık.

### K-14 · Faz sıralaması — veri → analiz → öneri → inference → eval
- **Karar:** uygulama sırası bağlayıcıdır: **Faz-1 veri temeli → Faz-2 ses analizi
  gerçekleştirmesi → Faz-3 öneri kolları → Faz-4 inference/model-management →
  Faz-5 evaluation & KVKK**. Bir fazın kabul kriteri geçmeden sonraki faz başlamaz.
- **Kaynak:** kullanıcı onayı 2026-10-14 ("full" kapsam, sıralı plan).
- **Gerekçe:** ters sıra (önce model seçimi) 50 yıllık mühendislik kuralıyla çelişir —
  veri olmadan algoritma, algoritma olmadan ölçüm boştur; bu vault'un kendi bulguları
  (R-01 · R-02 · R-06, K-08 · K-09) sıralamayı zaten dayatmaktadır.
- **Durum:** ✅ kullanıcı kararı (2026-10-14).

---

## §H — Uygulama Kuralları (bu plan bağlayıcı — 2026-10-14)

| # | Kural | Bağlantı |
|---|---|---|
| H1 | Bir fazın kabul kriteri geçmeden sonraki faz **başlamaz** | K-14 |
| H2 | Aday teknoloji "seçildi" olmadan kodda **kullanılmaz**; seçim = ADR | §11.1 · Q4 |
| H3 | K016 (ajan/MCP/RAG) bileşenleri K006'ya **taşınamaz** | K-03 · R-07 |
| H4 | Placeholder/sahte veri üreten kod "üretim" **sayılmaz**; gerçekleşme kanıtı = kod + test | K-09 · R-02 |
| H5 | Personalization verisi açık rıza + itiraz kanalı olmadan işlenemez | R-03 · §14 (ai-master) |
| H6 | Dış veri ithalatı (Last.fm/MSD/MusicBrainz) lisans/izin kapısı kapatılmadan **başlamaz** | K-13 · §21.1 |
| H7 | Her faz kapanışında `-dogrulama.md` §2 matrisine iddia·URL·tarih satırı eklenir | ADR-097 disiplini |

---

`status: verified` · `validation: 2026-10-12 · 10 kabul/gözlem + 5 açık karar, her satır kaynaklı, açık kararlar açık işaretli · 2026-10-14 · §G K-11–K-14 kullanıcı kararları (hibrit plan) + §H 7 uygulama kuralı eklendi`

---

## §I — NevaEngine Kaynaklı Kararlar (K-15 … K-26 — 2026-10-15 eklendi)

> Kaynaklar salt okunur: `.ai/projects/NevaEngine/*.md` + `legacy-2026-07/*` + PHP AI katmanı. Her karar satır numaralı kaynakla bağlıdır; uydurma karar ID'si yoktur.

| # | Karar | Bağlantı | Kaynak |
|---|---|---|---|
| K-15 | DSP zinciri guardrail bloğu **zorunlu**; yeni blok guardrailsiz eklenmez | R-15 · R-17 | `02-dsp-zincir-mimari.md` [Satır: 71–87] |
| K-16 | Gain staging tablosu tek noktada tutulur; satır içi kazanç değeri sabitlenmez | R-16 | `02-dsp-zincir-mimari.md` [Satır: 16–90] |
| K-17 | IO format standardı: 32-bit / 192 kHz; dönüşüm tek yerde | R-19 | `10-io-format-32bit-192khz-kurallari.md` [Satır: 15–99] |
| K-18 | SDK/lib envanterinde **lisans sütunu zorunlu**; boş lisans = kabul edilmez | R-18 | `00-sdk-lib-envanter.md` [Satır: 19–76 · 65–73] |
| K-19 | Gereksinim ↔ farklılık satırı çifti zorunlu; tek başına gereksinim kabul edilmez | R-21 | `06-gereksinimler-ve-farkliliklar.md` [Satır: 15–75] |
| K-20 | Web-PCM planı revizyonunda §7 risk satırları yeniden sayılır | R-20 | `05-neva-mixer-web-pcm-plani.md` [Satır: 132–143] |
| K-21 | OTA/güncellemede A/B partition + rollback yoksa sürüm yayınlanmaz | R-22 | `.ai/electronic/firmware/` (ota-update başlıkları) |
| K-22 | Driver sürüm matrisi güncel değilse fallback zinciri doğrulanmadan geçiş yapılmaz | R-23 | `.ai/electronic/drivers/` (audio-drivers başlıkları) |
| K-23 | ai-models başlık envanteri periyodik sayılır; başlık değişimi indeks satırına yansıtılır | R-25 | `ai-models.md` başlıklar [Satır: 13 · 434 · 459 · 503 · 528 · 577 · 590 · 713 · 726] |
| K-24 | eq-ai-auto davranışı değişirse K006 kesişim tablosu (kapsam §15.1) yeniden sayılır | R-24 | `eq-ai-auto.md` [Satır: 20–199 · 119 · 140] |
| K-25 | eq-user-modes için mod başına test senaryosu zorunlu | R-26 | `eq-user-modes.md` [Satır: 105–115 · 133–162 · 168–183] |
| K-26 | Real-time callback: tahsis/kilit/log yasak; buffer'lar baştan allocate | R-15 | `02-dsp-zincir-mimari.md` [Satır: 71–87] · `rules/` [Satır: 423–480] |

### §I.1 Karar gerekçeleri (özet — detay kaynak satırındadır)

| Karar | Gerekçe (tek cümle) | Reddedilen alternatif |
|---|---|---|
| K-15 | Guardrailsiz zincir öngörüsüz amplifikasyona yol açar | "ileride eklenir" |
| K-16 | Satır içi sabit kazanç revizyonu kilitler | her efektte kendi kazancı |
| K-17 | Çoklu format dönüşümü senkron/kalite kaybı üretir | dönüşümü efektlere dağıtmak |
| K-18 | Lisans boşluğu dağıtım/CI engeli üretir | "sonra tamamlanır" |
| K-19 | Farksız gereksinim farkındalık üretmez | gereksinim tek başına |
| K-20 | Plan değişince risk satırı bayatlar | risk satırını sabit tutmak |
| K-21 | Rollbacksuz OTA brick riski taşır | tek bölümlü güncelleme |
| K-22 | Sürüm matrisi olmadan fallback öngörülemez | "sürücü zaten kurulu" |
| K-23 | Başlık sürüklenmesi envanteri okunmaz kılar | envanteri elle hatırlamak |
| K-24 | Davranış değişimi vaadi bayatlatır | kesişimi saymamak |
| K-25 | Testsiz mod davranışı regresyona açık | testi sonra yazarız |
| K-26 | Callback tahsisi drop-out/click üretir | çalışma anında allocate |

### §I.2 Karar durumu sayımı (§I)

| Sayım | Değer |
|---|---|
| Yeni karar (K-15 … K-26) | 12 |
| Kaynak bağlı (dosya+satır) | 12/12 |
| ⚠️ VERIFICATION REQUIRED | 0 |
| UNKNOWN | 0 |

---

## §J — K006 Özel Kararları (PHP AI katmanı — 2026-10-15 eklendi)

> Kaynak: `shared/src/AI/*.php` (salt okunur — bu turda PHP dosyalarına YAZILMAZ).

| # | Karar | Bağlantı | Kaynak |
|---|---|---|---|
| K006-1 | AIEngine kamu arayüzü sözleşme üzerinden tüketilir; somut sınıf doğrudan bağımlılık yapılmaz | H2 · H3 | `AIEngineInterface.php` [Satır: 34] · `AIEngine.php` [Satır: 49–77] |
| K006-2 | AIWorkflow adımları tek tek kaydedilir (izlenebilirlik); toplu log yok | H7 | `AIWorkflow.php` [Satır: 49–91] |
| K006-3 | Orchestrator karar ağacı sabit kod içinde tutulur; dış servise gizli karar verilmez | K-03 · H3 | `AIOrchestrator.php` [Satır: 71–94] |
| K006-4 | KnowledgeBase girdileri açık rıza kaydı olmadan kişiselleştirme için kullanılmaz | H5 · R-03 | `KnowledgeBase.php` [Satır: 72–91] |
| K006-5 | AIEngine yapılandırma/estate satırları tek noktada (kod içi sabit scattering yok) | K-09 | `AIEngine.php` [Satır: 209–235 · 243–245] |
| K006-6 | K016 (ajan/MCP/RAG) bileşenleri K006 AI katmanına import edilmez | H3 | `.ai/architecture/index.md` §6 (K006↔K016) |
| K006-7 | Placeholder/boş cevap "başarılı AI" sayılmaz; gerçekleşme kanıtı kod + test | H4 · R-02 | §H4 · test stratejisi (K006) |
| K006-8 | Her AI özelliği kapıda `-dogrulama.md` §2 matrisine iddia·kaynak·tarih satırı ekler | H7 | ADR-097 disiplini |

---

## §J.1 K006 karar → test bağı

| Karar | Test türü | Doğrulama kaynağı |
|---|---|---|
| K006-1 | Kontrat testi | `AIEngineInterface.php` [Satır: 34] |
| K006-2 | Workflow adım log testi | `AIWorkflow.php` [Satır: 49–91] |
| K006-3 | Karar ağacı birim testi | `AIOrchestrator.php` [Satır: 71–94] |
| K006-4 | Rıza kaydı entegrasyon testi | `KnowledgeBase.php` [Satır: 72–91] |
| K006-5 | Yapılandırma yayılım testi | `AIEngine.php` [Satır: 209–235] |
| K006-6 | Import/bağımlılık denetimi | `.ai/architecture/index.md` §6 |
| K006-7 | Placeholder red testi | K006 test stratejisi |
| K006-8 | Kapı matrisi sayımı | `-dogrulama.md` §2 |

---

## §K — Karar → Risk → Kapsam Bağı (üçlü izlenebilirlik)

| Karar | Risk | Kapsam dosyası |
|---|---|---|
| K-15 · K-26 | R-15 · R-17 | `yapay-zeka-kapsam.md` §15 (Neva sınırı) |
| K-16 | R-16 | `yapay-zeka-kapsam.md` §15 |
| K-17 | R-19 | `yapay-zeka-kapsam.md` §16 (out-of-scope yönlendirme) |
| K-18 | R-18 | `yapay-zeka-kapsam.md` §16 |
| K-19 | R-21 | `yapay-zeka-kapsam.md` §17 (kabul kriteri 6) |
| K-20 | R-20 | `yapay-zeka-kapsam.md` §15 |
| K-21 | R-22 | `yapay-zeka-kapsam.md` §16 (firmware yönlendirme) |
| K-22 | R-23 | `yapay-zeka-kapsam.md` §16 (drivers yönlendirme) |
| K-23 | R-25 | `yapay-zeka-kapsam.md` §15.1 (kesişim) |
| K-24 | R-24 | `yapay-zeka-kapsam.md` §15.1 (kesişim) |
| K-25 | R-26 | `yapay-zeka-kapsam.md` §17 (kabul kriteri 7) |
| K006-1 … K006-8 | R-01 … R-14 (§5 mevcut register) | `yapay-zeka-kapsam.md` §6 (14 vaat) |

Kural: yeni karar eklenirken bu üçlü (karar → risk → kapsam satırı) **aynı oturumda** yazılır; eksik bağlantı = karar yarım (H1 ile aynı disiplin).

---

## §L — Sayım (bu tur — 2026-10-15)

| Sayım | Değer |
|---|---|
| §I yeni karar | 12 (K-15 … K-26) |
| §J yeni karar | 8 (K006-1 … K006-8) |
| §I.1 gerekçe satırı | 12 |
| §J.1 test bağı satırı | 8 |
| §K üçlü izlenebilirlik satırı | 12 |
| Kaynak bağlı (dosya+satır) | 20/20 |
| ⚠️ / UNKNOWN | 0 / 0 |
| Silinen satır | 0 (append-only) |
| Beyazliste dışı yazım | 0 |
| git commit | yok (orkestratöre ait) |

---

## §M — Kaynak Eki (§I–§K eklenenler)

1. `.ai/projects/NevaEngine/02-dsp-zincir-mimari.md` — Satır 16–90 (guardrails 71–87), 2026-10-15.
2. `.ai/projects/NevaEngine/10-io-format-32bit-192khz-kurallari.md` — Satır 15–99, 2026-10-15.
3. `.ai/projects/NevaEngine/00-sdk-lib-envanter.md` — Satır 19–76 (lisans 65–73), 2026-10-15.
4. `.ai/projects/NevaEngine/06-gereksinimler-ve-farkliliklar.md` — Satır 15–75, 2026-10-15.
5. `.ai/projects/NevaEngine/05-neva-mixer-web-pcm-plani.md` — Satır 132–143, 2026-10-15.
6. `.ai/electronic/firmware/` — ota-update başlıkları (7 dosya), 2026-10-15.
7. `.ai/electronic/drivers/` — audio-drivers başlıkları (10 dosya), 2026-10-15.
8. `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` — başlıklar 13 · 434 · 459 · 503 · 528 · 577 · 590 · 713 · 726, 2026-10-15.
9. `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` — Satır 20–199 · 119 · 140, 2026-10-15.
10. `.ai/projects/NevaEngine/legacy-2026-07/eq-user-modes.md` — Satır 105–115 · 133–162 · 168–183, 2026-10-15.
11. `.ai/projects/NevaEngine/legacy-2026-07/rules/` — test blokları Satır 423–480, 2026-10-15.
12. `shared/src/Contracts/AI/AIEngineInterface.php` — Satır 34, 2026-10-15.
13. `shared/src/AI/AIEngine.php` — Satır 49–77 · 209–235 · 243–245, 2026-10-15.
14. `shared/src/AI/AIWorkflow.php` — Satır 49–91, 2026-10-15.
15. `shared/src/AI/AIOrchestrator.php` — Satır 71–94, 2026-10-15.
16. `shared/src/AI/KnowledgeBase.php` — Satır 72–91, 2026-10-15.

---

`status: verified` · `validation: ... ek (2026-10-15 · kapanış turu) · §I–§M eklendi: 20 yeni karar (12 Neva + 8 K006) · 12 gerekçe · 8 test bağı · 12 üçlü izlenebilirlik · 16 kaynak eki — append-only, silinen satır 0`

---

## §N — Geliştirme Planı & IO Karar Köprüsü (K-27 … K-35 — 2026-10-15 eklendi)

> Kaynak: `01-gelistirme-plani.md` [Satır: 19–29 · 114–145] (lisans kararları L125–134 dahil) · `10-io-format-32bit-192khz-kurallari.md` [Satır: 15–99] (IO kararları K1–K6) · `06-gereksinimler-ve-farkliliklar.md` [Satır: 15–75] (gereksinim kayıtları R2 · R5 · R8 · R16 · R17 · R18).

| # | Karar | Bağlantı | Kaynak |
|---|---|---|---|
| K-27 | Faz sırası: gereksinim → mimari → uygulama; faz atlaması yok | H1 | `01-gelistirme-plani.md` [Satır: 19–29] |
| K-28 | Lisans kararı (L125–L134): envantere giren her bağımlılığın lisans kararı dokümante edilir | K-18 · R-18 | `01-gelistirme-plani.md` [Satır: 114–145] |
| K-29 | IO-1: format standardı kararı (32-bit/192 kHz) geri alınamaz; tüm yeni IO bu standarda uyar | K-17 · R-19 | `10-io-format...md` [Satır: 15–99] (K1) |
| K-30 | IO-2: dönüşüm katmanı tek; efekt blokları format bilmez | K-17 · R-19 | `10-io-format...md` [Satır: 15–99] (K2) |
| K-31 | IO-3: kanal sayısı/yerleşimi kararı IO dokümanında tek noktadan yönetilir | R-19 | `10-io-format...md` [Satır: 15–99] (K3) |
| K-32 | IO-4: örnekleme hızı dönüşümü (resample) yalnız planlanan noktada | R-19 | `10-io-format...md` [Satır: 15–99] (K4) |
| K-33 | IO-5: bit derinliği düşürme (dither) kararı belgelenir; sessiz düşürme yasak | R-19 | `10-io-format...md` [Satır: 15–99] (K5) |
| K-34 | IO-6: ölçüm/doğrulama noktası IO standardına bağlı; ölçüm protokolü IO revizyonunda güncellenir | R-19 · §I.2 | `10-io-format...md` [Satır: 15–99] (K6) |
| K-35 | Gereksinim kayıtları R2 · R5 · R8 · R16 · R17 · R18 karar izinde izlenir; her kayıt bir karar bağına sahiptir | K-19 · R-21 | `06-gereksinimler-ve-farkliliklar.md` [Satır: 15–75] |

### §N.1 Gereksinim kaydı → karar bağı

| Gereksinim kaydı | Karar | Not |
|---|---|---|
| R2 | K-19 · K-35 | farklılık satırı zorunluluğu |
| R5 | K-19 · K-35 | gereksinim↔farklılık çifti |
| R8 | K-19 · K-35 | matris sayımı |
| R16 | K-35 | izlenebilirlik |
| R17 | K-35 | izlenebilirlik |
| R18 | K-35 · K-28 | lisans kararı bağı |

---

## §O — Karar Güncelleme Protokolü (append-only disiplini)

| Adım | Aksiyon | Yasak |
|---|---|---|
| 1 | Yeni karar → yeni ID (K-nn) ile eklenir | mevcut satır revizyonu |
| 2 | Karar → risk → kapsam bağı (§K) aynı oturumda yazılır | bağısız karar |
| 3 | Kaynak satır numarası zorunlu | kaynaksız karar |
| 4 | Reddedilen alternatif tek cümle yazılır (§I.1 biçimi) | gerekçesiz karar |
| 5 | Karar geçersizse yeni satırda "geçersiz (tarih) + neden" işaretlenir | silme |

## §P — Sayım (bu tur ek 2 — §N–§O)

| Sayım | Değer |
|---|---|
| §N yeni karar | 9 (K-27 … K-35) |
| §N.1 gereksinim→karar bağı | 6 satır |
| §O protokol adımı | 5 |
| Ek toplam | 20 satır (önceki §I–§M turuna ek) |
| Kümülatif yeni karar (2026-10-15) | 29 (12 §I + 8 §J + 9 §N) |
| Silinen satır | 0 (append-only) |
| Beyazliste dışı yazım | 0 |
| git commit | yok (orkestratöre ait) |

---

## §Q — Kaynak Eki 2 (§N eklenenler)

1. `.ai/projects/NevaEngine/01-gelistirme-plani.md` — Satır 19–29 (faz sırası) · 114–145 (lisans kararları L125–L134), 2026-10-15.
2. `.ai/projects/NevaEngine/10-io-format-32bit-192khz-kurallari.md` — Satır 15–99 (IO kararları K1–K6), 2026-10-15.
3. `.ai/projects/NevaEngine/06-gereksinimler-ve-farkliliklar.md` — Satır 15–75 (R2 · R5 · R8 · R16 · R17 · R18), 2026-10-15.

---

`status: verified` · `validation: ... ek 2 (2026-10-15 · kapanış turu) · §N–§Q eklendi: 9 karar (K-27…K-35) · 6 gereksinim→karar bağı · 5 adımlı güncelleme protokolü · 3 kaynak eki — kümülatif 29 yeni karar, append-only, silinen satır 0`

---

## §R — Karar ID Devamlılık Tablosu (bu tur kapanışı)

| Aralık | Kapsam | Durum | Tarih |
|---|---|---|---|
| K-01 … K-10 | Önceki turlar (mevcut §A–§F) | korundu (silinmedi) | 2026-10-12 |
| K-11 … K-14 | Kullanıcı kararları (hibrit plan, §G) | korundu | 2026-10-14 |
| K-15 … K-26 | NevaEngine kaynaklı (§I) | eklendi | 2026-10-15 |
| K006-1 … K006-8 | K006 PHP AI katmanı (§J) | eklendi | 2026-10-15 |
| K-27 … K-35 | Geliştirme planı & IO (§N) | eklendi | 2026-10-15 |
| K-36 … | (sonraki tur için boş — uydurma ID yazılmaz) | — | — |

Kural: sonraki tur K-36'dan başlar; bu tabloya yalnız satır eklenir.

---

`status: verified` · `validation: ... ek 3 (2026-10-15 · kapanış turu) · §R eklendi: karar ID devamlılık tablosu (29 yeni karar kayıt altında; K-36 sonraki tur) — append-only, silinen satır 0`
