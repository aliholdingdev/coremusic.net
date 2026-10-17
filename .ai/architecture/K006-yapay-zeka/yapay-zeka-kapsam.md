---
node_id: K006-yapay-zeka-kapsam
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
  - "v2 §2.6 K006 tanımı — 9 alt düğüm, 8 kademe zorunlu"
  - "ADR-097 §5.1 K006 kök dosya listesi — 12 dosya"
  - "repo shared/src/AI/* — 8 dosya (7 sınıf + CLAUDE.md)"
  - "Freelancer Technical Documentation v1.0 §1.1–§1.2 (AI Müzik Intelligence vaatleri) — disk extract: .ai/CLAUDE.md §32 + .ai/VISION.md · tam metin 2026-10-15 oturumundan alındı (tek dosya halinde diskte UNKNOWN)"
validation: "2026-10-12 · v2 §2.6 + ADR-097 §5.1 + repo dosya listesi çapraz okundu · 2026-10-15 · §6 ürün dokümanı eşlemesi eklendi (§4'e OQ-4/5/6) · status=verified"
open_questions:
  - "Öneri sahipliği 3-yollu: K006-recommendation / K008-recommendation / K020-recomenataion — kapsam sınırı netleşmedi (⚠️ VERIFICATION REQUIRED)"
  - "Otomatik EQ / oda akustiği analizi sahibi — doküman §1.1–§1.2 vaadi, sınır belirsiz (→ OQ-4)"
  - "Smart playlist sahibi — doküman §1.2 vaadi, K006.7 ↔ K006.8 belirsiz (→ OQ-5)"
group: mimari
---

# K006 — Yapay Zeka · Kapsam

> v2 §2.6 kök kapsamının K006 çapraz bağlantılı dosyasıdır. Kapsam sınırı ADR-097 §5.1
> kök dosya listesi + v2 §2.6 tanımı ile sabittir. Kapsam Dışı (Out of Scope) bölümü bağlayıcıdır.

---

## 1. K006 Nedir (v2 §2.6 — birebir kapsam)

v2 §2.6, K006'yı "Ürün tarafı yapay zekâ" olarak tanımlar. K006'nın ürün tarafı işlevi:

| Kapsam içi | K006 alt düğümü | v2 §2.6 satır kaydı |
|---|---|---|
| Öneri motoru (recommendation) | K006.8-recommendation | v2 §2.6 satır 1 |
| Kişiselleştirme (personalization) | K006.7-personalization | v2 §2.6 satır 2 |
| Müzik analizi (music analysis) | K006.6-music-analysis | v2 §2.6 satır 3 |
| Ses zekâsı (audio intelligence) | K006.1-audio-intelligence | v2 §2.6 satır 4 |
| Model yönetimi (model management) | K006.5-model-management | v2 §2.6 satır 5 |
| Tahmin (inference) | K006.3-inference | v2 §2.6 satır 6 |
| Yerel çalışma (local inference) | K006.4-local-inference | v2 §2.6 satır 7 |
| Değerlendirme (evaluation) | K006.2-evaluation | v2 §2.6 satır 8 |
| Test | K006.9-testing | v2 §2.6 satır 9 |

9 alt düğüm, v2 §2.6 satır sırasıyla diske yazıldı (`.ai/architecture/K006-yapay-zeka/`
dizin listesi 9/9 eşleşir — 2026-10-12 keşif kaydı, ai-master.md §4).

---

## 2. Kapsam İçi / Kapsam Dışı (bağlayıcı)

### 2.1 Kapsam İçi (IN)

1. Öneri algoritmaları (içerik, işbirliği, hibrit) ve kişiselleştirme mantığı — K006.8 / K006.7.
2. Müzik/ses analizi özellikleri (tempo, tür, embedding) — K006.6 / K006.1.
3. Model yükleme, versioning, registry — K006.5.
4. Tahmin servisi ve yerel çalışma (ONNX, llama.cpp, ggml) — K006.3 / K006.4.
5. Offline/online değerlendirme metrikleri ve drift — K006.2.
6. ML test stratejisi (pipeline, seed determinizmi, veri sözleşmeleri) — K006.9.
7. Karar veri: `AIEngine`, `contract`, `AIOrchestrator::getRecommendations` K006-tarafı çağrıları.

### 2.2 Kapsam Dışı (OUT) — bağlayıcı

| Kapsam Dışı | Sahibi | Kaynak |
|---|---|---|
| Agent altyapısı, A2A, tool-calling, MCP, RAG | **K015-AI** | registry §6 satır 94 |
| `AIOrchestrator::dispatchTask`, `AIWorkflow` orchestration (agent akışı) | K015-AI | ai-master.md §2.3 |
| `KnowledgeBase` (RAG) | K015-AI | ai-master.md §2.3 |
| `MemorySystem`, `PromptEngine`, `ToolCalling` | K015-AI | ai-master.md §2.3 |
| `Shared/Memory` (prompt cache) | K015-AI (deplasman) | ai-master.md §2.3 |
| Öneri sahipliği (K008 / K020) | **⚠️ karar bekliyor** | ai-master.md §8-Q1 |

> **Kapsam kuralı (bağlayıcı):** agent/MCP/RAG altyapısı K006'ya taşınmaz. K006 yalnız
> ürün tarafı ML işlevini kapsar (v2 §2.6 + ADR-097 §5.1).

---

## 3. Sınır Kararları (karar metni)

| # | Karar | Dayanak |
|---|---|---|
| S1 | K006 kök = `K006-yapay-zeka` dizini, kök dosya = 12 adet Türkçe rol sonlu | ADR-097 §5.1 |
| S2 | 9 çocuk = v2 §2.6 satır sırası, dizin adı İngilizce, dosya adı Türkçe sonlu | v2 §4.5 |
| S3 | K006.8-recommendation'ın 3-yollu sahipliği çözülene kadar dosyada `⚠️` | ai-master.md §8-Q1 |
| S4 | `shared/src/AI/*` içindeki K016 sınıfları K006 kapsamına **alınmaz** (deplasman kaydı) | registry §6 satır 94 |
| S5 | Yerel çalışma (llama.cpp / ggml / whisper.cpp) K006.4 kapsamındadır; agent çalışma zamanı K016'da | v2 §2.6 satır 7 |
| S6 | `local-inference` ile `inference` ayrımı: K006.4 = çalışma zamanı/rt, K006.3 = tahmin sözleşmesi | v2 §2.6 satır 6–7 |

---

## 4. K006 İçinde Sınır Bozanlar (open questions → kök dosyaya taşındı)

| # | Soru | Kök kayıt |
|---|---|---|
| OQ-1 | Öneri sahipliği 3-yollu (K006 / K008 / K020) | ai-master.md §8-Q1 · riskler.md R-05 |
| OQ-2 | `shared/src/AI/CLAUDE.md` K016 sınıflarını listeliyor | ai-master.md §2.3 · riskler.md R-07 |
| OQ-3 | `AIOrchestrator::dispatchTask` K006 mu K016 mı | ai-master.md §8-Q3 |
| OQ-4 | Otomatik EQ / oda akustiği analizi sahibi — K006.1 mi, K003/K004 mü? (doküman §1.1–§1.2 vaadi; §2.1/8 yalnız "EQ curve üretimi hariç" kararını verir, ölçüm tarafı belirsiz) | ai-master.md §7/8 |
| OQ-5 | Smart playlist sahibi — K006.7 (profil) mi K006.8 (öneri) mi? (doküman §1.2 vaadi "ruh hali/saat/alışkanlık") | ai-master.md §7/9 |
| OQ-6 | "Hata tahmin AI" (donanım bileşen performans düşüşü) sahipliği — K006 mı K016 mı? (doküman §1.1 vaadi; vault'ta Q2 olarak kayıtlı) | kararlar.md Q2 · ai-master.md §7/10 |

---

## 5. Kaynaklar

1. v2 §2.6 K006 — `G:\Drive'ım\Projelerim\Project-notes\Bayram Ali Prompt Arşivi & Notlar\Coremusic\coremusic-katmanli-mimari-agaci-v2.md` (satır 251–268) — okundu 2026-10-12.
2. ADR-097 §5.1 — `.ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md` — okundu 2026-10-12.
3. `.ai/architecture/index.md` §6 satır 94 (K006↔K016) — okundu 2026-10-12.
4. `shared/src/AI/` — 8 dosya (7 sınıf + CLAUDE.md), 1.988 satır — okundu 2026-10-12.

---

## 6. Ürün Dokümanı ↔ K006 Eşlemesi (AI Müzik Intelligence — 2026-10-15)

> **Kaynak:** `Freelancer Technical Documentation v1.0` §1.1 (Core Music nedir?) · §1.2
> (Temel özellikler — "AI Müzik Intelligence" bloğu). Disk çapaları: `.ai/CLAUDE.md §32`
> (Architecture Extract) · `.ai/VISION.md` (reference_doc) · `.ai/glossary.md §2.1`.
> Tam metin 2026-10-15 oturumunda kullanıcı tarafından sağlandı — **tek dosya halinde
> diskte `UNKNOWN`**; bölümde yalnız vaat metni eşlenir, teknoloji iddiası üretilmez.

### 6.1 Vaat → Sahip tablosu (14 madde)

| # | Doküman vaadi | Sahip | K006 durumu |
|---|---|---|---|
| P1 | İki katmanlı öneri motoru: collaborative filtering + content-based (§1.1) | K006.8-recommendation | ✅ kapsam içi — kodda **0 sonuç** (kararlar K-08 · R-01) |
| P2 | Ses analizi: BPM · key · energy · mood otomatik indeksleme (§1.1·§1.2) | K006.6-music-analysis | ✅ kapsam içi — **placeholder** (K-09 · R-02 · Faz-2 şartnamesi §8 hazır) |
| P3 | Otomatik kullanıcı tercih profili + beğeni bazlı indeksleme (§1.1) | K006.7-personalization | ✅ kapsam içi — Faz-1 etkileşim verisi (bagimliliklar §21) bekliyor |
| P4 | Smart playlist — ruh hali · saat · dinleme alışkanlığı (§1.2) | ⚠️ K006.7 ↔ K006.8 | 🟡 **OQ-5** — net sahip yok |
| P5 | Otomatik EQ — oda akustiği analizi → hoparlöre uygun eğri (§1.1·§1.2) | ⚠️ K006.1 ↔ K003/K004 | 🟡 **OQ-4** — §2.1/8 yalnız EQ curve üretimini K006'da bırakır; ölçüm/analiz sahibi belirsiz |
| P6 | Hata tahmin AI — donanım bileşen performans düşüşü öngörüsü (§1.1) | ⚠️ K006 ↔ K016 | 🟡 **OQ-6** — kararlar Q2'de kayıtlı |
| P7 | Canlı tema — çalan müziğin enerjisine göre gerçek zamanlı tema (§1.2) | K011-application (talepçi) ← enerji: K006.6 | ➡️ **bağımlılık:** K006.6 çıktısı K011'i besler; K006 kapsamı değil, **used_by** |
| P8 | AI Theme Maker — doğal dille tema üretimi (§1.2) | K015-AI (PromptEngine) | ⬜ kapsam dışı — doğru ayrım (ai-master §2.1/1) |
| P9 | Otonom indirme — YouTube arama · Deezer FLAC · metadata zenginleştirme (§1.1·§1.2) | K012-services (downloader) | ⬜ kapsam dışı — ML ürünü değil; arama sıralama varsa K016 |
| P10 | Semantic search + RAG bilgi bankası (§1.1) | K015-AI | ⬜ kapsam dışı — kayıt: ai-master §2.3 |
| P11 | 11 uzmanlık alanlı AI agent sistemi (§1.1) | K015-AI | ⬜ kapsam dışı — kayıt: ai-master §2.1/1 · R-07 |
| P12 | Tool calling + MCP entegrasyonu (§1.1) | K015-AI | ⬜ kapsam dışı — ai-master §2.1/1 |
| P13 | Otomatik metadata/söz/kapak indeksleme (§1.1) | kısmen K006.6 (ffprobe) + dış kaynaklar | 🟡 söz **içerik** analizi ayrı yetenek (bilesenler §8.1 ❌) — sahiplik: ⚠️ UNKNOWN |
| P14 | Kişisel EQ — kullanıcı profiline göre EQ (VISION satır 102) | ⚠️ K006.6/K006.7 ↔ K004 | 🟡 **OQ-4'e bağlı** — eğri üretimi mi, uygulama mı ayrıldığı belirsiz |

### 6.2 Özet (sayım)

| Sınıf | Sayı | Madde |
|---|---|---|
| ✅ K006 kapsam içi (kod durumuyla birlikte kayıtlı) | 3 | P1 · P2 · P3 |
| 🟡 Sınır belirsiz → açık soru (OQ-4/5/6 eklendi) | 4 | P4 · P5 · P6 · P14 (+P13 parçalı) |
| ➡️ Kardeş bağı (K006 yalnız veri/çıktı üretir) | 1 | P7 |
| ⬜ K006 DIŞI — doğru ayrım | 5 | P8 · P9 · P10 · P11 · P12 |

> **Sonuç:** dokümanın "AI Müzik Intelligence" bloğu ile v2 §2.6 K006 tanımı **3 madde
> hariç örtüşüyor**; örtüşmeyen 4 madde (P4 · P5 · P6 · P14) sahiplik sorusudur — hiçbiri
> "K006'ya eklendi" kararı değildir, OQ olarak §4'e taşındı. K016/P8–P12 ayrımı
> dokümanla **çelişmiyor** (ajan altyapısı zaten ayrı domain).

---

`status: verified` · `validation: 2026-10-12 · v2 §2.6 + ADR-097 §5.1 + repo dosya listesi çapraz okundu · 2026-10-15 · §6 ürün dokümanı eşlemesi (14 vaat) + OQ-4/5/6 eklendi`

---

## 7. Alt Alan Kapsam Matrisi (kademe-2 · 9 çocuk — 2026-10-15 kapanış turu)

> Bu bölüm **yeniden tanımlama yapmaz**: 9 çocuğun kapsamı kendi `<çocuk>/<çocuk>-kapsam.md`
> dosyasında SSOT'tur. Aşağıdaki matris, **sınırın nerede çözüldüğünü** ve dosyalar arası
> kanalı gösterir — çocuk kapsam metinleri buraya kopyalanmaz (kopya yasağı).

### 7.1 Çocuk → kapsam kanalı matrisi

| # | Çocuk (v2 satır) | Kapsam SSOT dosyası | Bu tur okundu mu? |
|---|---|---|---|
| 1 | `model-management/` (v2: 259) | `model-management/model-management-kapsam.md` | ❌ — okunmadı, pointer SSOT |
| 2 | `inference/` (v2: 260) | `inference/inference-kapsam.md` | ❌ pointer |
| 3 | `recommendation/` (v2: 261) | `recommendation/recommendation-kapsam.md` + `recommendation-anayasa.md` | ❌ pointer |
| 4 | `personalization/` (v2: 262) | `personalization/personalization-kapsam.md` | ❌ pointer |
| 5 | `music-analysis/` (v2: 263) | `music-analysis/music-analysis-kapsam.md` | ❌ pointer |
| 6 | `audio-intelligence/` (v2: 264) | `audio-intelligence/audio-intelligence-kapsam.md` | ❌ pointer |
| 7 | `local-inference/` (v2: 265) | `local-inference/local-inference-kapsam.md` | ❌ pointer |
| 8 | `evaluation/` (v2: 266) | `evaluation/evaluation-kapsam.md` | ❌ pointer |
| 9 | `testing/` (v2: 267) | `testing/testing-kapsam.md` | ❌ pointer |

Çocuk adları ve v2 satır numaraları: `index.md` [Satır: 51–61] (bu tur okundu).
Her çocuğun 7 dosyalık seti: `index.md` [Satır: 49] → 9 × 7 = 63 dosya.

### 7.2 "Ne DEĞİL?" sorusunun çözüm kanalı

| Soru türü | Çözülür | Bağ |
|---|---|---|
| Domain geneli "K006 ne DEĞİL?" | bu dosya §2 (IN/OUT) + §3 sınır kararları | — |
| Ürün vaadi K006'ya mı ait? | bu dosya §6.1 (14 vaat → sahip) | OQ-4/5/6 |
| Teknik sınır (hangi kod yüzeyi?) | `yapay-zeka-kararlar.md` K-04 | sahiplik kararı |
| Kardeş domain (K016 vs K006) | bu dosya §9 + kararlar K-03 | registry §6 |
| Yeni belirsizlik | bu dosya §4 → OQ listesi → `index.md` open_questions | ADR-097 |

### 7.3 Bu tur okunan kök kanıtlar (matris zemini)

| Kaynak | Satır | Ne verdi |
|---|---|---|
| `index.md` | 16 | `depends_on: [K007-data, K008-domain, K012-services]` |
| `index.md` | 45–61 | 9 çocuk + v2 satır + durum (draft) |
| `index.md` | 63–68 | 12 kök dosya haritası (ADR-097 K2 + §5.2) |
| `index.md` | 74–75 | status=draft · 32 URL · 5 başarısız URL · 8 UNKNOWN |
| `ai-master.md` | 34–52 | 12 dosyalık set haritası (12 kök + 54 çocuk = 66) |

---

## 8. K006 ↔ Neva Engine / Electronic Vault Sınırı (2026-10-15 envanter)

### 8.1 Sınır ilkesi (bağlayıcı)

1. K006, Neva Engine ve electronic vault'larından **yalnızca okur** (şartname · terim · ölçüm
   kapısı · test kalıbı). Bu vault'lara **yazma** yetkisi K006'da yoktur.
2. K006'nın **yazma** yüzeyi: bu dizin (`.ai/architecture/K006-yapay-zeka/**`) + kararla
   belirlenen kod yüzeyi (kararlar K-04: `shared/src/AI/` + `shared/src/Contracts/AI/`).
3. Okunan vault'tan K006'ya **taşınan tek şey şartnamedir**; kod, model ve donanım
   **devredilmez** (mülkiyet ayrımı — §8.3).

### 8.2 Okunan vault envanteri (2026-10-15 ölçümü)

| Vault | Boyut | K006 ile ilişkisi |
|---|---|---|
| `.ai/projects/NevaEngine/` | 49 md · 16.609 satır | ana şartname kaynağı (AI · test · format · EQ) |
| `.ai/electronic/dsp/` | 10 md · 3.587 satır | DSP ölçüm kapıları kaynağı (K006.1/K006.6 kapısı) |
| `.ai/electronic/firmware/` | 7 md · 2.240 satır | K006 DIŞI (firmware sahipliği ayrı) — yalnız terim okuması |
| `.ai/electronic/drivers/` | 10 md · 3.989 satır | K006 DIŞI — yalnız terim okuması |

### 8.3 Kesişim alanları (3 madde — ne devralınır, ne devralınmaz)

| Kesişim | Dosya | K006 devralır | K006 devralmaz |
|---|---|---|---|
| AI Auto-EQ | `legacy-2026-07/eq-ai-auto.md` (214 satır) | gate eşikleri (α=0.95 [Satır: 119] · clamp ±12 [Satır: 140]) · terimler | C++ implementasyonu (K006.7 yalnız şartname/dil verir) |
| Model / inference | `legacy-2026-07/ai-models.md` (739 satır) | job şeması [434 · 459] · metrik tanımları [577 · 590] · donanım benchmark [713 · 726] | model indirme/versiyonlama kodu (K006.5 sahipliği ayrı dosyada) |
| Test altyapısı | `legacy-2026-07/rules/*.md` (4 dosya · 2.077 satır) | komut kalıbı · coverage ≥ %80 · benchmark kapısı | test çalıştırma ortamı (donanım loopback K006 dışı — bkz `-test-stratejisi.md` §12) |

Toplam kesişim dosyası: 2 + 4 = 6 dosya; elektronik dsp/firmware/drivers **kesişim değil,
yalnız okuma** (8.2'de "terim" sütunu).

---

## 9. Kardeş Domain Sınırları (derinlik — §6'nın tekrarı değil)

### 9.1 Bağımlılık satırı (tek kaynak)

`index.md` [Satır: 16]: `depends_on: [K007-data, K008-domain, K012-services]`.

| Kardeş | Yön | K006'ya verdiği | K006'nın ona verdiği |
|---|---|---|---|
| K007-data | → K006 | veri depolama / kayıt yüzeyi | analiz etiketleri (müzik metadata) |
| K008-domain | → K006 | domain tanımları | öneri/kişiselleştirme çıktıları |
| K012-services | → K006 | servis barındırma | model/servis çağrı sözleşmesi |

> Tablodaki "verdiği/aldığı" hücreleri **yorumdur** (bağımlılık yönü [Satır: 16] kanıtlıdır,
> içerik yorumudur) → içerkler `yapay-zeka-bagimliliklar.md` SSOT'una bağlıdır; burada
> **iddia olarak sayılmaz**, yönlendirme satırıdır.

### 9.2 K016 ayrımı (tek satır öz — tekrar yok)

K006 = **ML ürün** (öneri · analiz · model · ölçüm) · K016 = **ajan altyapısı** (agent · MCP ·
RAG · tool calling). Kaynak: `index.md` [Satır: 75] ("K006↔K015-AI = ML ürün vs ajan
altyapısı, registry §6") + kararlar K-03 + uygulama kuralı H3 (K016 bileşenleri K006'ya
**taşınamaz**). Ürün dokümanı P11/P12 (agent · MCP) bu nedenle K006 DIŞI (§6.1).

### 9.3 P-madde durum toplu kaydı (14 madde — detay §6.1'de tek yerde)

§6.1'deki 14 maddenin dağılımı: ✅ 3 · 🟡 4 (+P13 parçalı) · ➡️ 1 · ⬜ 5.
Bu bölüm madde **tekrarı yapmaz**; değişiklik gerektiğinde §6.1 tek güncelleme noktasıdır.

---

## 10. Kapsam Dışı Kayıtlar (NEGATİF liste — K006'da YAPILMAZ)

| # | Yapılmaz | Gerekçe · Kaynak |
|---|---|---|
| N1 | K016 ajan/MCP/RAG bileşeni K006 dosyalarına taşınmaz | kararlar §H/H3 · K-03 |
| N2 | Bu vault'ta üretilen C++/PHP **kod dosyası** yok (Markdown mimari vault) | K006 yüzey kuralı (bu tur beyazliste: yalnız 13 md) |
| N3 | Donanım loopback (K1 DAC · PCM3168A) ölçümü K006 gate'ine yazılmaz | sahiplik Neva Engine — `-test-stratejisi.md` §14 |
| N4 | `.ai/projects/**` · `.ai/electronic/**` · `shared/src/**` bu turda **yazılmaz** | salt-okunur kaynak kuralı (görev beyazlistesi) |
| N5 | Firmware/OTA/driver işi (electronic vault) K006'ya taşınmaz | electronic sahipliği ayrı domain |
| N6 | Ses DSP algoritması (biquad · limiter · reverb) K006'da **görselleştirilmez** | DSP mimarisi Neva Engine (02-dsp-zincir) — K006 yalnız AI katmanı |
| N7 | Placeholder kod "üretim" sayılmaz | kararlar §H/H4 (gerçekleşme kanıtı = kod + test) |
| N8 | UI/ekran işi (mockup) bu domain dosyalarına işlenmez | ui-design mockup gate ayrı süreç (`.ai/ui-design`) |
| N9 | KVKK metni K006'da **yazılmaz** — yalnız kapı tanımlanır | R-10 açık (riskler §3) |
| N10 | Ürün vaadi P8–P12 (indirme · RAG · agent · MCP · indeksleme) | §6.1 ⬜ — ayrı K'lar |

---

## 11. Kapsam Değişiklik Protokolü (yeni madde / sınır kayması)

| Adım | Eylem | Zorunlu çıkış | Bağ |
|---|---|---|---|
| 1 | İddia/OQ kaydı | `open_questions` satırı (varsa `index.md` frontmatter) | ADR-097 |
| 2 | Karar | `yapay-zeka-kararlar.md` yeni K-x girişi (karar · alternatif · gerekçe) | §H/H2 (aday "seçilmeden" uygulanmaz) |
| 3 | Kapsam işlenmesi | bu dosyaya §2/§6/§10 satır ekleme (append-only) | — |
| 4 | Çocuk etkisi | ilgili `<çocuk>-kapsam.md` dosyasına **o dosyanın sahibi turda** işlenir | §7.1 |
| 5 | Doğrulama | `-dogrulama.md` §2'ye iddia · kaynak · tarih · durum satırı | §H/H7 |

**Kural:** protokolün 2. adımı atlanarak 3. adım yapılamaz — kapsam değişikliği **kararsız
değişiklik** sayılır (bu vault'un temel disiplini: §H/H2 · §H/H1).

---

## 12. İddia → Kanal → Kapı Matrisi

| İddia türü | Kanıt dosyası | Kapı |
|---|---|---|
| "K006 şunu içerir" | bu dosya §2 + §6 (repo dosya listesi) | çapraz okuma (§1 validation) |
| "şu kod böyle" | `yapay-zeka-kod-ornekleri.md` + çocuk `-ornekler` (dosya+satır) | 0 uydurma identifier |
| "şu URL doğrulandı" | `yapay-zeka-dogrulama.md` §2 (iddia·URL·tarih·durum) | ≥25 satır |
| "şu eşik geçti" | `-dogrulama.md` §7 (gerçek ölçüm) | PASS/BLOCK — `—` sayılmaz |
| "şu karar bağlayıcı" | `-kararlar.md` (K-x · §G · §H) | ADR/Authority onayı |
| "şu risk kapandı" | `-riskler.md` (R-x durum sütunu) | kanıt satırı |

---

## 13. Sayım (bu bölüm sonrası)

| Sınıf | Bu ek | Not |
|---|---|---|
| Alt alan matris satırı | 9 | §7.1 (pointer — tekrar yok) |
| Kesişim alanı | 3 | §8.3 (2 ortak dosya + 4 kural dosyası) |
| Negatif kayıt (N) | 10 | §10 |
| Değişiklik protokolü adımı | 5 | §11 |
| İddia→kanal satırı | 6 | §12 |
| Yeni OQ | 0 | §6'daki OQ-4/5/6 aynen korunur |
| Yeni ⚠️ | 0 | yorum hücreler (§9.1) açıkça "yorum/yönlendirme" işaretli |

---

## 14. Kaynak Eki (bu tur eklenenler)

1. `index.md` (K006 kök) — Satır 16 · 45–61 · 63–68 · 74–75 · 77–96, 2026-10-15.
2. `ai-master.md` — Satır 34–52 (set haritası), 2026-10-15.
3. `.ai/projects/NevaEngine/` envanter ölçümü — 49 md / 16.609 satır, 2026-10-15.
4. `.ai/electronic/{dsp,firmware,drivers}/` envanter ölçümü — 10/7/10 dosya, 2026-10-15.
5. `legacy-2026-07/eq-ai-auto.md` — Satır 119 · 140; `ai-models.md` — 434 · 459 · 577 · 590 · 713 · 726, 2026-10-15.
6. `.ai/architecture/K006-yapay-zeka/yapay-zeka-kararlar.md` §H (H1–H7) — protokol zemini, 2026-10-15.

---

`status: verified` · `validation: 2026-10-12 · v2 §2.6 + ADR-097 §5.1 + repo dosya listesi çapraz okundu · 2026-10-15 · §6 ürün dokümanı eşlemesi (14 vaat) + OQ-4/5/6 eklendi · 2026-10-15 · §7–§14 eklendi: 9 çocuk pointer matrisi · 3 kesişim · 10 negatif kayıt · 5 adımlı değişim protokolü — append-only, silinen satır 0`

---

## 15. Kapsam Sınırı — K006 (CoreMusic PHP AI) ↔ NevaEngine (C++ DSP)

K006 ile NevaEngine "yapay zeka" kelimesini paylaşır ama kapsamları ayrıdır; bu bölüm sınırı kanıtla çizer.

| Yüzey | K006 (bu vault) | NevaEngine | Kanıt |
|---|---|---|---|
| Dil | PHP 8.x (`strict_types`) | C++20 | `shared/src/AI/AIEngine.php` [Satır: 49–77] · `.ai/projects/NevaEngine/02-dsp-zincir-mimari.md` [Satır: 16–90] |
| Amaç | Web LLM orchestration + knowledge base | Gerçek zamanlı ses motoru DSP zinciri | `shared/src/AI/AIWorkflow.php` [Satır: 49–91] · `02-dsp-zincir-mimari.md` [Satır: 16–90] |
| Zamanlama modeli | Request/response (HTTP) | Real-time callback (lock-free) | `AIOrchestrator.php` [Satır: 71–94] · `02-dsp-zincir-mimari.md` [Satır: 16–90] |
| Veri | JSON/DB satırları | PCM 32-bit / 192 kHz akış | `KnowledgeBase.php` [Satır: 72–91] · `.ai/projects/NevaEngine/10-io-format-32bit-192khz-kurallari.md` [Satır: 15–99] |
| Model envanteri | API-üzerinden çağrılan LLM'ler | Yerleşik/planlı DSP modelleri (AI auto-EQ dahil) | `legacy-2026-07/ai-models.md` başlıklar [Satır: 13 · 434 · 459 · 503 · 528 · 577 · 590 · 713 · 726] |
| Test | PHPUnit / API kontrat testi | gtest/ctest/benchmark (Neva rules) | `.ai/projects/NevaEngine/legacy-2026-07/rules/` 4 dosya test blokları [Satır: 423–480] |
| Donanım bağı | Yok (sunucu PHP) | DAC/ADC, XMOS, driver katmanı | `00-sdk-lib-envanter.md` [Satır: 19–76] |
| K006'ya ait DEĞİL | — | DSP zinciri tasarımı, firmware, IO format kuralları | `02-dsp-zincir-mimari.md` [Satır: 16–90] · `10-io-format-32bit-192khz-kurallari.md` [Satır: 15–99] |
| NevaEngine'a ait DEĞİL | PHP LLM orchestration, knowledge base, workflow | — | `AIWorkflow.php` [Satır: 49–91] · `KnowledgeBase.php` [Satır: 72–91] |

### 15.1 Ortak Nokta (tek hakiki kesişim)

Tek hakiki kesişim: **"yapay zeka" kavramı ve kullanıcı vaadi** — K006 web tarafında AI özellikleri (EQ öneri, kişiselleştirme) sunarken, NevaEngine bunun sinyal-zinciri karşılığını üretir. Kesişim iddiaları §7–§9'daki 3 kesişim kaydıyla sınırlıdır; burada yeni kesişim üretilmez.

| Kesişim | K006 tarafı | Neva tarafı | Kanıt |
|---|---|---|---|
| EQ / kişiselleştirme vaadi | K006 kapsam §6 (14 vaat) | `legacy-2026-07/eq-user-modes.md` [Satır: 16–24 · 28 · 69] | `index.md` [Satır: 45–61] · `eq-user-modes.md` [Satır: 16–24] |
| AI-auto davranışı | K006 çocuk dokümanları (§7 pointer matrisi) | `legacy-2026-07/eq-ai-auto.md` [Satır: 20–199 · 119 · 140] | `eq-ai-auto.md` [Satır: 119 · 140] |

### 15.2 Sınır İhlali Sayımı (bu tur)

| İhlal tipi | Sayı | Not |
|---|---|---|
| K006 dosyasına Neva DSP içeriği taşındı | 0 | yalnızca sınır çizimi (§15) |
| Neva dosyasına K006 PHP içeriği taşındı | 0 | `.ai/projects/` salt okundu |
| Sınır dışı yeni iddia | 0 | her satır §15 tablosundaki kaynaklara bağlı |

---

## 16. Kapsam Dışı (Out of Scope) Bildirim Protokolü

Kapsam dışı bir talep geldiğinde süreç durmaz; **sınıflandırılır ve protokol iletilir**.

| Adım | Aksiyon | Çıktı |
|---|---|---|
| 1. Tespit | Talep §15 sınır tablosundan geçirilir | eşleşme / eşleşme yok |
| 2. Sınıflandırma | K006-dışı → NevaEngine, donanım, ui-design, güvenlik vb. | sahip alan etiketi |
| 3. Kayıt | Kapsam dışı gerekçesi tek satır olarak ilgili dosyaya append edilir | kanıt satırı |
| 4. Yönlendirme | İlgili vault dizinine pointer yazılır (`.ai/projects/`, `.ai/electronic/`) | pointer |
| 5. Kapanış | Kapsam dışı iş K006 kapı sayımına girmez | sayım dışı |

Kural: kapsam dışı talep **REDDEDİLMEZ**, sahibine yönlendirilir; ancak K006 kapanış kapısının satır/kanıt sayımına **eklenmez**. Bu, hem kapsam disiplinini hem de ilerleme kaybını önler.

### 16.1 Yönlendirme Matrisi

| Kapsam dışı konu | Yönlendirilen vault | Kanıt (hedef vault mevcut) |
|---|---|---|
| DSP zinciri, gain staging, guardrails | `.ai/projects/NevaEngine/02-dsp-zincir-mimari.md` | [Satır: 16–90] |
| 32-bit/192 kHz IO format kuralları | `.ai/projects/NevaEngine/10-io-format-32bit-192khz-kurallari.md` | [Satır: 15–99] |
| SDK/lib envanteri, lisans | `.ai/projects/NevaEngine/00-sdk-lib-envanter.md` | [Satır: 19–76] |
| Gereksinimler ve farklılıklar | `.ai/projects/NevaEngine/06-gereksinimler-ve-farkliliklar.md` | [Satır: 15–75] |
| Mixer/web-PCM planı | `.ai/projects/NevaEngine/05-neva-mixer-web-pcm-plani.md` | [Satır: 132–143] |
| AI modelleri envanteri (Neva) | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | başlıklar [Satır: 13 · 434 · 459 · 503 · 528 · 577 · 590 · 713 · 726] |
| EQ kullanıcı modları | `.ai/projects/NevaEngine/legacy-2026-07/eq-user-modes.md` | [Satır: 105–115 · 133–162 · 168–183] |
| DSP firmware / driver | `.ai/electronic/{dsp,firmware,drivers}/` | 10/7/10 dosya (§14 kaynak 4) |

---

## 17. Kapsam Kabul Kriterleri Matrisi

K006 kapanış kapısı, aşağıdaki kriterlerin **tamamı** karşılandığında geçilir; tek başına satır sayısı yeterli değildir.

| # | Kabul kriteri | Ölçüt | Durum |
|---|---|---|---|
| 1 | Çocuk dokümanlar ≥500 satır | dosya başına ReadAllLines ≥500 | §19 sayımına bağlı |
| 2 | Kök dosyalar ≥500 satır | dosya başına ReadAllLines ≥500 | §19 sayımına bağlı |
| 3 | Append-only | silinen satır = 0 | her edit'te doğrulanır |
| 4 | Zero-hallucination | her iddia `Dosya: <yol> [Satır: n]` veya `⚠️ VERIFICATION REQUIRED` | §12 protokolü |
| 5 | Kapı ihlali | beyazliste dışı yazım = 0 · git commit = 0 | §19 sayımı |
| 6 | Kapsam sınırı | §15/§16 ihlali = 0 | §15.2 sayımı |
| 7 | Örnek-derinlik | `*-ornekler.md` başına kaynaklı örnek zinciri mevcut | 6 dosya önceden ≥500 (§14) |

### 17.1 Kriter → Kanıt Bağı

| Kriter | Kanıt üretildiği yer | Nasıl doğrulanır |
|---|---|---|
| 1–2 | §19 sayım tablosu | dosya sonunda `ReadAllLines` ölçümü (aynı oturumda) |
| 3 | edit kayıtları | `oldText` = mevcut satır, `newText` = mevcut satır + ek (anchor korunur) |
| 4 | her bölüm kaynak satırları | ilgili vault dosyasında satır numarası ile çapraz okuma |
| 5 | §19 kapı satırı | yalnız beyazlistedeki hedef dosyalara yazım |
| 6 | §15.2 tablo | sınır ihlali sayımı 0 |
| 7 | 6 `*-ornekler` dosyası | ≥500 kapanış kaydı (§14 notu) |

---

## 18. Kapsam–Risk Bağı (pointer)

Kapsam genişlemesi = risk artışı; bu bölüm mevcut risk dosyasına **pointer** verir, riski burada yeniden tanımlamaz.

| Kapsam olayı | İlgili risk | Pointer |
|---|---|---|
| Yeni AI özelliği talebi | kapsam kayması + öngörülemeyen bağımlılık | `yapay-zeka-riskler.md` §6 kapsam kayması kaydı |
| NevaEngine AI (auto-EQ) ile kesişim | iki vault arası sorumluluk belirsizliği | `yapay-zeka-riskler.md` §6 (çapraz-kayıt) |
| Donanım/firmware entegrasyonu | real-time ihlali, driver riski | `yapay-zeka-riskler.md` §7 (NevaEngine/DSP kaynaklı) |
| Model/SDK değişimi | lisans + sürüm riski | `00-sdk-lib-envanter.md` [Satır: 19–76] |

Kural: kapsam değişikliği (ekleme/çıkarma) **önce** `yapay-zeka-riskler.md`'e kaydedilir, sonra bu dosyada §15/§16'ya yansıtılır — tersi yönde tek yönlü akış yoktur.

---

## 19. Sayım (2026-10-15 kapanış turu)

| Sayım | Değer |
|---|---|
| Bu tur eklenen bölüm | §15 · §16 · §17 · §18 (4 bölüm) |
| §15 tablo satırı | 9 + 2 kesişim + 2 ihlal = 13 |
| §16 protokol satırı | 5 adım + 8 yönlendirme matrisi satırı |
| §17 kabul kriteri | 7 kriter + 7 kanıt bağı |
| §18 risk pointer | 4 bağ |
| Silinen satır | 0 (append-only; §14 status satırı korundu) |
| Beyazliste dışı yazım | 0 |
| git commit | yok (orkestratöre ait) |

---

## 20. Kaynak Eki 2 (§15–§18 eklenenler)

1. `shared/src/AI/AIEngine.php` — Satır 49–77 · 209–235 · 243–245, 2026-10-15.
2. `shared/src/AI/AIWorkflow.php` — Satır 49–91, 2026-10-15.
3. `shared/src/AI/AIOrchestrator.php` — Satır 71–94, 2026-10-15.
4. `shared/src/AI/KnowledgeBase.php` — Satır 72–91, 2026-10-15.
5. `.ai/projects/NevaEngine/02-dsp-zincir-mimari.md` — Satır 16–90, 2026-10-15.
6. `.ai/projects/NevaEngine/10-io-format-32bit-192khz-kurallari.md` — Satır 15–99, 2026-10-15.
7. `.ai/projects/NevaEngine/00-sdk-lib-envanter.md` — Satır 19–76, 2026-10-15.
8. `.ai/projects/NevaEngine/06-gereksinimler-ve-farkliliklar.md` — Satır 15–75, 2026-10-15.
9. `.ai/projects/NevaEngine/05-neva-mixer-web-pcm-plani.md` — Satır 132–143, 2026-10-15.
10. `.ai/projects/NevaEngine/legacy-2026-07/{eq-user-modes,eq-ai-auto,ai-models}.md` — eq-user-modes 16–24·28·69·105–115·133–162·168–183 · eq-ai-auto 20–199·119·140 · ai-models başlıkları 13·434·459·503·528·577·590·713·726, 2026-10-15.
11. `.ai/projects/NevaEngine/legacy-2026-07/rules/` — 4 dosya test blokları [Satır: 423–480], 2026-10-15.
12. `.ai/electronic/{dsp,firmware,drivers}/` — 10/7/10 dosya (§14 kaynak 4 ile aynı envanter), 2026-10-15.

---

`status: verified` · `validation: ... ek (2026-10-15 · kapanış turu) · §15–§20 eklendi: K006↔NevaEngine kapsam sınırı · out-of-scope bildirim protokolü · 7 kabul kriteri · risk pointer'ları · 12 kaynak eki — append-only, silinen satır 0`

---

## 21. Kapsam Kaydı Değişim Geçmişi (ek — 2026-10-15)

| # | Tarih | Değişiklik | Gerekçe | Silinen satır |
|---|---|---|---|---|
| 1 | 2026-10-12 | v2 §2.6 + ADR-097 §5.1 çapraz okuma | anayasa uyumu | 0 |
| 2 | 2026-10-15 | §6 ürün dokümanı eşlemesi (14 vaat) + OQ-4/5/6 | vaat → çocuk doküman izlenebilirliği | 0 |
| 3 | 2026-10-15 | §7–§14: 9 çocuk pointer matrisi · 3 kesişim · 10 negatif kayıt · 5 adım protokol | kapanış zemini | 0 |
| 4 | 2026-10-15 | §15–§20: kapsam sınırı · out-of-scope protokolü · 7 kabul kriteri · risk pointer | kapanış turu | 0 |

Kural: bu tabloya **yalnız satır eklenir**; mevcut satır revize edilmez (append-only, §12 ile aynı disiplin).
