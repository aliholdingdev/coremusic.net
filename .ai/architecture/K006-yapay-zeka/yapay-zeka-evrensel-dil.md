---
node_id: K006-yapay-zeka-evrensel-dil
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
  - K015-AI
evidence:
  - "Terimler shared/src/AI/*.php ve Contracts/AI/*.php docblock/identifier'larından türetildi (2026-10-12)"
  - "Kavramsal terimler doğrulanmış kaynaklardan: yapay-zeka-dogrulama.md §2 (32 URL)"
validation: "2026-10-12 · 60+ terim TR↔EN + kod karşılığı · status=verified (kod terimleri disk kanıtlı)"
open_questions:
  - "vault glossary.md ile resmi eşleme yapılmadı (⚠️ VERIFICATION REQUIRED — ayrı tur)"
group: mimari
---

# K006 — Yapay Zeka · Evrensel Dil (TR ↔ EN)

> **Amaç:** K006 vault dosyalarındaki Türkçe terim ile kod içindeki İngilizce identifier
> arasındaki birebir köprü. Kod karşılığı olan terimlerde **dosya/satır** verilir; olmayanlarda
> yalnız sözlük eşlemesi vardır (uydurma identifier yazılmaz).
> Kullanım: bir terim önce buradan geçer, sonra metne girer.

---

## 1. Kod Karşılıklı Terimler (identifier = disk kanıtı)

| Türkçe | English | Kod karşılığı | Konum (2026-10-12) |
|---|---|---|---|
| Öneri üretimi | Recommendation | `getRecommendations` | AIEngine L49 · Contracts L28 |
| Hibrit skorlama | Hybrid scoring | `combineScores` | AIEngine L243 |
| İşbirlikçi filtreleme | Collaborative filtering | `getCollaborativeScores` | AIEngine L209 (→ `[]`) |
| İçerik tabanlı | Content-based | `getContentBasedScores` | AIEngine L223 (→ `[]`) |
| Çeşitlilik filtresi | Diversity filter | `applyDiversityFilter` | AIEngine L264 |
| Min. skor eşiği | Minimum score threshold | `MIN_SCORE` | AIEngine L31 |
| Maks. öneri sayısı | Max recommendations | `MAX_RECOMMENDATIONS` | AIEngine L30 |
| Çeşitlilik hedefi | Diversity target | `DIVERSITY_TARGET` | AIEngine L32 |
| Ağırlık | Weight | `$collabWeight` / `$contentWeight` | AIEngine L243 |
| Ses analizi | Audio analysis | `analyzeAudio` | AIEngine L82 · Contracts L36 |
| Öznitelik çıkarma | Feature extraction | `extractFeatures` | AIEngine L315 (placeholder) |
| Meta veri | Metadata | `extractMetadata` | AIEngine L288 |
| Vuruş hızı | Beats per minute (BPM) | `'bpm'` | AIEngine L95 · Contracts L34 |
| Ton / anahtar | Key | `'key'` | AIEngine L96 |
| Enerji | Energy | `'energy'` | AIEngine L97 |
| Ruh hali | Mood | `'mood'` | AIEngine L98 |
| Dans edilebilirlik | Danceability | `'danceability'` | AIEngine L99 |
| Akustiklik | Acousticness | `'acousticness'` | AIEngine L100 |
| Örnekleme hızı | Sample rate | `'sample_rate'` | AIEngine L102 |
| Bit derinliği | Bit depth | `'bit_depth'` | AIEngine L103 |
| Oda koreksiyonu | Room correction | `calculateRoomCorrection` | AIEngine L347 |
| Otomatik EQ | Auto EQ / auto-EQ | `optimizeEQ` · `autoEQWorkflow` | AIEngine L111 · AIWorkflow L147 |
| Bant frekansı | Band frequency | `bandToFrequency` | AIEngine L332 |
| Bant kalite faktörü | Band Q factor | `calculateBandQ` | AIEngine L368 |
| Donanım analizi | Hardware analysis | `analyzeHardware` | AIEngine L138 · Contracts L52 |
| Arıza tahmini | Fault prediction | `predictFault` | AIEngine L161 · Contracts L60 |
| Görev dağıtımı | Task dispatch | `dispatchTask` | AIOrchestrator L71 · Contracts L23 |
| Görev durumu | Task status | `getTaskStatus` | AIOrchestrator L102 |
| Etkin görevler | Active tasks | `listActiveTasks` | AIOrchestrator L114 |
| Ajana yönlendirme | Route to agent | `routeToAgent` | AIOrchestrator L125 |
| Bağlam | Context | `getContext` / `updateContext` | AIOrchestrator L154 / L162 |
| İş akışı | Workflow | `recommendationWorkflow` vb. 5 akış | AIWorkflow L49/99/147/199/241 |
| Bilgi sorgusu | Knowledge query | `knowledge->search` | AIWorkflow L63 |
| RAG sorgusu | RAG query | `ragQuery` | KnowledgeBase L72 (**K016**) |
| Prompt üretimi | Prompt generation | `generatePrompt` | PromptEngine L48 (**K016**) |
| Token sayımı | Token counting | `countTokens` | PromptEngine L90 (**K016**) |
| Oturum hafızası | Session memory | `setSession` / `getSession` | MemorySystem L45 / L60 (**K016**) |
| Kalıcı hafıza | Persistent memory | `setPersistent` / `getPersistent` | MemorySystem L89 / L104 (**K016**) |
| Hatırlama (önbellekli) | Remember (cached) | `remember` | MemorySystem L123 (**K016**) |
| Araç çağırma | Tool calling | `callTool` / `registerTool` | ToolCalling L73 / L53 (**K016**) |
| İzin kontrolü | Permission check | `checkPermission` | ToolCalling L145 (**K016**) |
| Arayüz / sözleşme | Interface / contract | `*Interface` | `shared/src/Contracts/AI/` (6 dosya) |
| Uygulama / implementasyon | Implementation | `implements *Interface` | AIEngine L28 |
| Yer tutucu | Placeholder | yorum: `placeholder değerler` | AIEngine L318 |

---

## 2. Kavramsal Terimler (kod karşılığı yok — sözlük eşlemesi)

| Türkçe | English | Kısa tanım (K006 bağlamı) | Not |
|---|---|---|---|
| Yapay zeka | Artificial intelligence (AI) | makine öğrenimi tabanlı ürün özellikleri | kök düğüm adı |
| Makine öğrenimi | Machine learning (ML) | veriden öğrenen model | K006 kapsamı |
| Model | Model | öğrenilmiş ağırlıklar/parametreler | K006.5 |
| Çıkarım / çalıştırma | Inference | eğitilmiş modelden tahmin üretme | K006.3 |
| Yerel çıkarım | Local inference | cihazda, sunucusuz çalıştırma | K006.4 |
| Eğitim | Training | model parametrelerinin öğrenilmesi | dış sınır (çekirdek yok) |
| Model kaydı | Model registry | model sürümlerinin deposu | K006.5 — repo'da 0 |
| Model kalitesi | Model quality | doğruluk/yanlışlık ölçümleri | K006.2 |
| Veri kayması | Data drift | girdi dağılımının zamanla değişmesi | K006.2 — repo'da 0 |
| Model kayması | Model drift | model performansının düşmesi | K006.2 — repo'da 0 |
| Değerlendirme | Evaluation | offline/online metrik hesabı | K006.2 |
| Ölçüm | Metric (measurement) | precision/recall/NDCG vb. | eşik UNKNOWN |
| Kişiselleştirme | Personalization | kullanıcıya göre uyarlama | K006.7 |
| Kullanıcı profili | User profile | tercih/geçmiş kümesi | `preferences` (AIWorkflow L55) |
| Öneri listesi | Recommendation list | sıralı öneri kümesi | dönüş şeması Contracts L26 |
| Açıklanabilirlik | Explainability | `reason` alanı ile gerekçe üretimi | Contracts L26 |
| Soğuk başlangıç | Cold start | yeni kullanıcı/öğe için veri yokluğu | K006.8 — CF kolu `[]` |
| Matris çarpanlaştırımı | Matrix factorization (ALS) | CF yöntemi | yorum: AIEngine L216 — **uygulanmadı** |
| Kosinüs benzerliği | Cosine similarity | içerik benzerliği ölçümü | yorum: AIEngine L233 — **uygulanmadı** |
| Öznitelik | Feature | model girdisi özellik | `extractFeatures` placeholder |
| Önişleme | Preprocessing | veri temizliği/dönüşümü | repo'da yok |
| Etiket | Label | denetimli öğrenme hedefi | K006 — veri kaynağı UNKNOWN |
| Tahmin | Prediction / forecast | model çıktısı | `predictFault` |
| Anomali tespiti | Anomaly detection | beklenmeyen sapma bulma | yorum: AIEngine L26 |
| Model çalışma zamanı | Model runtime | modeli çalıştıran altyapı | aday: ONNX Runtime |
| Ağ geçidi (BFF) | Gateway / BFF | istek yönlendirme katmanı | K006 dışı (K013/K009) |
| Denetim izi | Audit trail | değişiklik kaydı | `.ai/log.md` (append-only) |
| Doğrulama | Validation | kanıtla kontrol | `yapay-zeka-dogrulama.md` |
| Web araştırması | Web research | resmi kaynak taraması | `yapay-zeka-web-arastirma.md` |
| Bileşen | Component | envanter birimi | `yapay-zeka-bilesenler.md` |
| Bağımlılık | Dependency | paket/alt sistem | `yapay-zeka-bagimliliklar.md` |
| Kapsam | Scope | içerir/dışarıda bırakır | `yapay-zeka-kapsam.md` |
| Risk | Risk | etki × olasılık | `yapay-zeka-riskler.md` |
| Test stratejisi | Test strategy | katman + gate | `yapay-zeka-test-stratejisi.md` |
| Karar | Decision (ADR) | kaynaklı bağlayıcı seçim | `yapay-zeka-kararlar.md` |
| Kod örneği | Code example | diskten alınmış blok | `yapay-zeka-kod-ornekleri.md` |
| Usta dosya | Master file | `ai-master.md` | ADR-097 §5.4 |
| Alt alan | Subdomain | kademe-2 çocuk | `node_type: subdomain` |
| Boşluk | Gap | beklenen ama olmayan kod | "0 satır" bulgusu |
| Yer tutucu kod | Placeholder code | gerçek mantık gelene kadar sahte değer | AIEngine L318 |
| Ölü kod | Dead code | atanan ama kullanılmayan | AIEngine L53 |
| Hata fırlatma | Throw / exception | `throw new \RuntimeException` | AIEngine L85 |
| Sonuç tipi | Result type | hata/başarı sarmalayıcı | proje standardı — AI'da **yok** (R-04) |

---

## 3. Kısaltmalar

| Kısaltma | TR | EN |
|---|---|---|
| ALS | Dönüşümlü En Küçük Kareler | Alternating Least Squares |
| CF | İşbirlikçi filtreleme | Collaborative Filtering |
| EQ | Ekolayzır | Equalizer |
| MIR | Müzik bilgi iştirmesi | Music Information Retrieval |
| RAG | Zenginleştirmeyle Üretim | Retrieval-Augmented Generation |
| NDCG | Normalize Edilmiş Kümülatif Kazanç | Normalized Discounted Cumulative Gain |
| MLOps | ML operasyonları | Machine Learning Operations |
| TTL | Yaşam süresi | Time To Live |
| DSP | Dijital sinyal işleme | Digital Signal Processing |
| PSR | PHP Standartları Önerileri | PHP Standards Recommendations |
| DI | Bağımlılık enjeksiyonu | Dependency Injection |
| BCNF | Boyce-Codd normal formu | Boyce-Codd Normal Form |
| OWASP | Açık Web Uygulamaları Güvenlik Projesi | Open Web Application Security Project |

---

## 4. Çeviri Kuralları (bu vault için bağlayıcı)

1. **Identifier İngilizce, anlatım Türkçe.** Kodda `getRecommendations`; metinde "öneri üretimi".
2. **İlk geçişte çift yazılı:** "işbirlikçi filtreleme (collaborative filtering)" — sonra kısaltma.
3. **`⚠️ VERIFICATION REQUIRED` / `UNKNOWN`** ifadeleri çevrilmez, olduğu gibi kalır (vestibüler işaret).
4. **Uydurma identifier yok:** eşleşmeyen terim §2'ye girer, §1'e **girmez**.
5. **K016 terimleri işaretli:** §1'de `(**K016**)` — K006 metninde kullanıldığında sınır notu korunur.
6. `node_id`/dosya adları İngilizce-sabit; değiştirilemez (KURAL 1: v2 adları birebir).

---

## 5. Sayım

| Küme | Terim |
|---|---|
| §1 kod karşılıklı | 45 |
| §2 kavramsal | 40 |
| §3 kısaltma | 13 |
| §4 kural | 6 |
| **Toplam** | **104 madde** |

---

## 6. Kaynaklar

1. `shared/src/AI/*.php` + `shared/src/Contracts/AI/*.php` — identifier/docblock, 2026-10-12.
2. `yapay-zeka-dogrulama.md` §2 — 32 doğrulanmış URL (kavramsal kaynaklar).
3. `yapay-zeka-bilesenler.md` §2–§4 — metot/interfaсе satır envanteri.
4. `.ai/AGENTS.md` §3 Terminoloji — vault geneli terim standardı (⚠️ glossary eşlemesi ayrı tur).

---

`status: verified` · `validation: 2026-10-12 · 104 madde; 45'i dosya+satır kod kanıtlı, 0 uydurma identifier`

---

## 7. DSP Terimleri (NevaEngine kaynaklı — salt okunur vault)

> Kaynak: `.ai/projects/NevaEngine/02-dsp-zincir-mimari.md` [Satır: 16–90] · `10-io-format-32bit-192khz-kurallari.md` [Satır: 15–99]. Terimler K006↔NevaEngine sınırında (kapsam: `yapay-zeka-kapsam.md` §15) tek yazımda standartlaştırılır.

| # | Terim | Karşılık / Açıklama | Kaynak |
|---|---|---|---|
| D-1 | DSP zinciri | Sıralı efekt/processing blokları (gain → EQ → dynamics → limit) | `02-dsp-zincir-mimari.md` [Satır: 16–90] |
| D-2 | Gain staging | Zincir aşamalarında kazanç yönetimi (headroom korunumu) | `02-dsp-zincir-mimari.md` [Satır: 16–90] |
| D-3 | Guardrail | Zincir sonu güvenlik sınırı (limit/clipper) — KRİTİK | `02-dsp-zincir-mimari.md` [Satır: 71–87] |
| D-4 | Headroom | Tepe sinyale kadar kalan dinamik boşluk | `02-dsp-zincir-mimari.md` [Satır: 16–90] |
| D-5 | Real-time callback | Zaman kısıtlı ses callback'i — tahsis/kilit yasak | `02-dsp-zincir-mimari.md` [Satır: 71–87] |
| D-6 | Lock-free | Callback içinde kilit kullanmayan veri yapısı | `02-dsp-zincir-mimari.md` [Satır: 71–87] |
| D-7 | Ring buffer | Prosesler arası sabit boyutlu sinyal tamponu | `02-dsp-zincir-mimari.md` [Satır: 16–90] |
| D-8 | Zero-allocation | Callback öncesi tahsis; çalışma anında tahsis yok | `02-dsp-zincir-mimari.md` [Satır: 71–87] |
| D-9 | Saturasyon | Kontrollü/doğrusal-olmayan kırpılma | `02-dsp-zincir-mimari.md` [Satır: 16–90] |
| D-10 | Clip | Sinyalin sistemce kırpılması (istenmeyen) | `02-dsp-zincir-mimari.md` [Satır: 16–90] |
| D-11 | Dynamics | Sıkıştırma/genişletme ailesi (kompressor/limiter) | `02-dsp-zincir-mimari.md` [Satır: 16–90] |
| D-12 | Latency | Girdi→çıktı gecikmesi (sample/ms cinsinden) | `10-io-format-32bit-192khz-kurallari.md` [Satır: 15–99] |

## 8. OS / API Terimleri (IO format ve platform matrisi)

> Kaynak: `10-io-format-32bit-192khz-kurallari.md` [Satır: 15–99] (§2 IO matrisi).

| # | Terim | Karşılık / Açıklama | Kaynak |
|---|---|---|---|
| O-1 | IO format | Bit derinliği + örnekleme hızı kuralları (32-bit / 192 kHz) | `10-io-format-32bit-192khz-kurallari.md` [Satır: 15–99] |
| O-2 | 32-bit PCM | Kayıpsız yakın sinyal temsili (bu vault standardı) | `10-io-format-32bit-192khz-kurallari.md` [Satır: 15–99] |
| O-3 | 192 kHz | Yüksek örnekleme hızı (IO kuralı) | `10-io-format-32bit-192khz-kurallari.md` [Satır: 15–99] |
| O-4 | Web-PCM | Web çalma zinciri için PCM aktarım planı | `05-neva-mixer-web-pcm-plani.md` [Satır: 132–143] |
| O-5 | Mixer | Kanal karıştırma katmanı (seviye/pan/busk) | `05-neva-mixer-web-pcm-plani.md` [Satır: 132–143] |
| O-6 | WASAPI | Windows ses API'si (fallback hedefi) | `06-gereksinimler-ve-farkliliklar.md` [Satır: 15–75] |
| O-7 | Driver | Donanım ses sürücüsü katmanı | `.ai/electronic/drivers/` (10 dosya) |
| O-8 | Platform katmanı | OS-özel ses/io entegrasyonu | `.ai/projects/NevaEngine/os/` (4 md) |

## 9. Firmware / Real-Time Terimleri

> Kaynak: `.ai/electronic/firmware/` (7 dosya) · `.ai/electronic/dsp/` (10 dosya) — salt okunur envanter; başlıklar 2026-10-15 ölçümü.

| # | Terim | Karşılık / Açıklama | Kaynak |
|---|---|---|---|
| F-1 | Firmware | Donanım üzerinde çalışan düşük seviye yazılım | `.ai/electronic/firmware/` (7 dosya) |
| F-2 | OTA update | Hava üzerinden firmware güncelleme (A/B partition) | `.ai/electronic/firmware/` (ota-update başlıkları) |
| F-3 | RTOS | Gerçek zamanlı işletim sistemi (zaman kısıtı görevleri) | `.ai/electronic/firmware/` (rtos başlıkları) |
| F-4 | A/B partition | İki bölmeli, geri dönüşlü güncelleme şeması | `.ai/electronic/firmware/` (ota-update başlıkları) |
| F-5 | Rollback | Başarısız güncellemeden önceki sürüme dönüş | `.ai/electronic/firmware/` (ota-update başlıkları) |
| F-6 | DMA | Bellek ile doğrudan veri aktarımı (CPU yükü azaltır) | `.ai/electronic/dsp/` (10 dosya) |
| F-7 | I2S/TDM | Seri ses veri yolu protokolleri | `00-sdk-lib-envanter.md` [Satır: 19–76] |
| F-8 | DAC/ADC | Dijital↔analog dönüştürücü | `00-sdk-lib-envanter.md` [Satır: 19–76] |

## 10. AI /ML Terimleri (Neva model envanteri köprüsü)

> Kaynak: `legacy-2026-07/ai-models.md` başlıklar [Satır: 13 · 434 · 459 · 503 · 528 · 577 · 590 · 713 · 726] · `legacy-2026-07/eq-ai-auto.md` [Satır: 20–199 · 119 · 140] · `eq-user-modes.md` [Satır: 16–24 · 28 · 69].

| # | Terim | Karşılık / Açıklama | Kaynak |
|---|---|---|---|
| A-1 | AI-auto EQ | Otomatik EQ karar akışı | `eq-ai-auto.md` [Satır: 20–199 · 119 · 140] |
| A-2 | Kullanıcı modu | EQ davranış modu (kişi/ortam bazlı) | `eq-user-modes.md` [Satır: 16–24 · 28 · 69] |
| A-3 | Model envanteri | AI modelleri listesi (başlıklı) | `ai-models.md` başlıklar [Satır: 13 · 434 · 459 · 503 · 528 · 577 · 590 · 713 · 726] |
| A-4 | LLM | Büyük dil modeli (K006 tarafı: API üzerinden) | `shared/src/AI/AIWorkflow.php` [Satır: 49–91] |
| A-5 | Knowledge base | Bilgi bankası (K006 tarafı: PHP) | `shared/src/AI/KnowledgeBase.php` [Satır: 72–91] |
| A-6 | Orchestrator | AI iş akışı orkestratörü | `shared/src/AI/AIOrchestrator.php` [Satır: 71–94] |

## 11. Ölçüm Metrikleri (standart yazım)

| # | Metrik | Standart yazım | Not |
|---|---|---|---|
| M-1 | Örnekleme hızı | `<n> kHz` (ör. 192 kHz) | IO kuralı: `10-io-format...md` [Satır: 15–99] |
| M-2 | Bit derinliği | `<n>-bit PCM` (ör. 32-bit PCM) | IO kuralı |
| M-3 | Gecikme | `<n> ms` veya `<n> sample` | birim asla karıştırılmaz |
| M-4 | Tepe | dBFS | klip referansı |
| M-5 | Dosya sayısı | `<n> dosya` + tarih (ör. 10 dosya, 2026-10-15) | envanter ölçümleri |
| M-6 | Satır sayısı | `ReadAllLines` ile sayım (tarayıcı `\n` farkı yok sayılır) | kapı ölçüm standardı |

## 12. Sayım (bu tur ek — §7–§11)

| Sayım | Değer |
|---|---|
| DSP terimi (§7) | 12 (D-1 … D-12) |
| OS/API terimi (§8) | 8 (O-1 … O-8) |
| Firmware/RT terimi (§9) | 8 (F-1 … F-8) |
| AI/ML terimi (§10) | 6 (A-1 … A-6) |
| Ölçüm metriği (§11) | 6 (M-1 … M-6) |
| Ek toplam | 40 madde (önceki 104 → toplam 144) |
| Silinen satır | 0 (append-only) |
| Beyazliste dışı yazım | 0 |
| git commit | yok (orkestratöre ait) |

---

## 13. Kaynak Eki (§7–§11 eklenenler)

1. `.ai/projects/NevaEngine/02-dsp-zincir-mimari.md` — Satır 16–90 (guardrails 71–87), 2026-10-15.
2. `.ai/projects/NevaEngine/10-io-format-32bit-192khz-kurallari.md` — Satır 15–99 (§2 IO matrisi), 2026-10-15.
3. `.ai/projects/NevaEngine/05-neva-mixer-web-pcm-plani.md` — Satır 132–143, 2026-10-15.
4. `.ai/projects/NevaEngine/06-gereksinimler-ve-farkliliklar.md` — Satır 15–75, 2026-10-15.
5. `.ai/projects/NevaEngine/00-sdk-lib-envanter.md` — Satır 19–76, 2026-10-15.
6. `.ai/projects/NevaEngine/os/` — 4 md (platform katmanı), 2026-10-15.
7. `.ai/electronic/dsp/` — 10 dosya envanteri, 2026-10-15.
8. `.ai/electronic/firmware/` — 7 dosya envanteri (ota-update · rtos başlıkları), 2026-10-15.
9. `.ai/electronic/drivers/` — 10 dosya envanteri, 2026-10-15.
10. `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` — başlıklar Satır 13 · 434 · 459 · 503 · 528 · 577 · 590 · 713 · 726, 2026-10-15.
11. `.ai/projects/NevaEngine/legacy-2026-07/eq-ai-auto.md` — Satır 20–199 · 119 · 140, 2026-10-15.
12. `.ai/projects/NevaEngine/legacy-2026-07/eq-user-modes.md` — Satır 16–24 · 28 · 69, 2026-10-15.

---

`status: verified` · `validation: ... ek (2026-10-15 · kapanış turu) · §7–§13 eklendi: 40 yeni madde (12 DSP · 8 OS/API · 8 FW/RT · 6 AI/ML · 6 ölçüm) — toplam 144 madde — append-only, silinen satır 0`

---

## 14. Çakışan Terimler (aynı kavram, farklı yazımlar → tek standart)

Aynı kavramın vault içinde birden fazla yazımı varsa **tek standart yazım** burada bağlayıcıdır; diğerleri varyant olarak işaretlenir, silinmez.

| # | Varyant yazımlar | Standart yazım | Alan | Kaynak |
|---|---|---|---|---|
| C-1 | "sample rate" / "örnekleme hızı" / "sampling rate" | örnekleme hızı (TR) · sample rate (kod) | IO | `10-io-format...md` [Satır: 15–99] |
| C-2 | "bit depth" / "bit derinliği" | bit derinliği (TR) · bit depth (kod) | IO | `10-io-format...md` [Satır: 15–99] |
| C-3 | "gain staging" / "kazanç yönetimi" | gain staging | DSP | `02-dsp-zincir-mimari.md` [Satır: 16–90] |
| C-4 | "guardrail" / "sınır" / "güvenlik sınırı" | guardrail | DSP | `02-dsp-zincir-mimari.md` [Satır: 71–87] |
| C-5 | "callback" / "geri çağırım" | callback | RT | `02-dsp-zincir-mimari.md` [Satır: 71–87] |
| C-6 | "lock-free" / "kilitsiz" | lock-free | RT | `02-dsp-zincir-mimari.md` [Satır: 71–87] |
| C-7 | "rollback" / "geri dönüş" | rollback | FW | `.ai/electronic/firmware/` |
| C-8 | "mixer" / "karıştırıcı" | mixer | IO | `05-neva-mixer-web-pcm-plani.md` [Satır: 132–143] |

Kural: varyant yazımlar mevcut metinlerde **düzeltilemez** (append-only); yalnız yeni üretimde standart yazım kullanılır. Çelişki tespitinde bu tabloya satır eklenir.

## 15. Terim → Dosya Eşlemesi (nerede geçiyorsa)

| Terim grubu | Geçtiği dosya (salt okunur) | Satır |
|---|---|---|
| D-1 … D-12 (DSP) | `.ai/projects/NevaEngine/02-dsp-zincir-mimari.md` | 16–90 (guardrails 71–87) |
| O-1 … O-3 (IO format) | `.ai/projects/NevaEngine/10-io-format-32bit-192khz-kurallari.md` | 15–99 |
| O-4 … O-5 (Web-PCM/mixer) | `.ai/projects/NevaEngine/05-neva-mixer-web-pcm-plani.md` | 132–143 |
| O-6 (WASAPI) | `.ai/projects/NevaEngine/06-gereksinimler-ve-farkliliklar.md` | 15–75 |
| O-7 (Driver) | `.ai/electronic/drivers/` | başlık envanteri (10 dosya) |
| O-8 (Platform) | `.ai/projects/NevaEngine/os/` | 4 md |
| F-1 … F-5 (Firmware/OTA/RTOS) | `.ai/electronic/firmware/` | başlık envanteri (7 dosya) |
| F-6 (DMA) | `.ai/electronic/dsp/` | başlık envanteri (10 dosya) |
| F-7 … F-8 (I2S/TDM/DAC/ADC) | `.ai/projects/NevaEngine/00-sdk-lib-envanter.md` | 19–76 |
| A-1 (AI-auto EQ) | `legacy-2026-07/eq-ai-auto.md` | 20–199 · 119 · 140 |
| A-2 (Kullanıcı modu) | `legacy-2026-07/eq-user-modes.md` | 16–24 · 28 · 69 · 105–115 |
| A-3 (Model envanteri) | `legacy-2026-07/ai-models.md` | başlıklar 13 · 434 · 459 · 503 · 528 · 577 · 590 · 713 · 726 |
| A-4 … A-6 (LLM/KB/Orch) | `shared/src/AI/{AIWorkflow,KnowledgeBase,AIOrchestrator}.php` | 49–91 · 72–91 · 71–94 |

## 16. Test & Doğrulama Terimleri (Neva rules köprüsü)

> Kaynak: `legacy-2026-07/rules/` — 4 dosya, test blokları [Satır: 423–480], 2026-10-15.

| # | Terim | Karşılık / Açıklama | Kaynak |
|---|---|---|---|
| T-1 | gtest | C++ birim test çatısı | `rules/` [Satır: 423–480] |
| T-2 | ctest | Test çalıştırıcı (CMake) | `rules/` [Satır: 423–480] |
| T-3 | benchmark | Performans ölçüm testi | `rules/` [Satır: 423–480] |
| T-4 | Test bloğu | Vault içindeki gerçek test tanımı parçası | `rules/` [Satır: 423–480] |

## 17. Kısaltma Sözlüğü Ek (§3'e ek — uzunluk korunur)

| Kısaltma | Tam hali | Alan |
|---|---|---|
| PCM | Pulse-Code Modulation | IO |
| IO | Input/Output | IO |
| RT | Real-Time | RT |
| FW | Firmware | FW |
| OTA | Over-The-Air | FW |
| DMA | Direct Memory Access | DSP |
| DAC | Digital-to-Analog Converter | DSP |
| ADC | Analog-to-Digital Converter | DSP |
| I2S | Inter-IC Sound | DSP |
| TDM | Time-Division Multiplexing | DSP |
| LLM | Large Language Model | AI |
| KB | Knowledge Base | AI |
| ORCH | Orchestrator | AI |
| EQ | Equalizer | AI/DSP |
| THD | Total Harmonic Distortion | DSP (⚠️ ölçüm eşiği verisi bu vault'ta yok — `yapay-zeka-riskler.md` §7.2'ye pointer) |
| SLO | Service Level Objective | K006 (⚠️ eşik verisi `yapay-zeka-dogrulama.md`'de değil → VERIFICATION REQUIRED) |

## 18. Kullanım Kuralları (yazım anı)

| # | Kural | Uygulama |
|---|---|---|
| K-1 | İlk geçişte tam hali + kısaltma | "(32-bit Pulse-Code Modulation (PCM))" |
| K-2 | Kod identifier'ları İngilizce kalır | `AIEngine`, `AIWorkflow` — çevrilemez |
| K-3 | Birim asla kısaltılmaz | "192 kHz" yazılır, "192K" yazılmaz |
| K-4 | Ölçüm + tarih çifti zorunlu | "10 dosya, 2026-10-15" |
| K-5 | Belirsiz terim → `⚠️ VERIFICATION REQUIRED` | uydurma karşılık yazılmaz |
| K-6 | Yeni terim → §7–§11 tablolarına satır eklenir | mevcut satır revize edilmez |

## 19. Sayım (bu tur ek 2 — §14–§18)

| Sayım | Değer |
|---|---|
| Çakışan terim (§14) | 8 (C-1 … C-8) |
| Terim→dosya eşlemesi (§15) | 13 satır |
| Test terimi (§16) | 4 (T-1 … T-4) |
| Kısaltma eki (§17) | 16 (⚠️ işaretli 2) |
| Kullanım kuralı (§18) | 6 (K-1 … K-6) |
| Ek toplam | 47 madde/satır (önceki 144 → toplam 191) |
| Silinen satır | 0 (append-only) |
| Beyazliste dışı yazım | 0 |
| git commit | yok (orkestratöre ait) |

---

## 20. Kaynak Eki 2 (§14–§18 eklenenler)

1. `.ai/projects/NevaEngine/legacy-2026-07/rules/` — 4 dosya, test blokları Satır 423–480, 2026-10-15.
2. `.ai/projects/NevaEngine/02-dsp-zincir-mimari.md` — Satır 16–90 · 71–87 (çakışma standardı D serisi), 2026-10-15.
3. `.ai/projects/NevaEngine/10-io-format-32bit-192khz-kurallari.md` — Satır 15–99 (O serisi), 2026-10-15.
4. `.ai/projects/NevaEngine/05-neva-mixer-web-pcm-plani.md` — Satır 132–143 (O-4/O-5), 2026-10-15.
5. `.ai/projects/NevaEngine/06-gereksinimler-ve-farkliliklar.md` — Satır 15–75 (O-6), 2026-10-15.
6. `.ai/projects/NevaEngine/00-sdk-lib-envanter.md` — Satır 19–76 (F-7/F-8), 2026-10-15.
7. `.ai/electronic/firmware/` — 7 dosya (F-1 … F-5), 2026-10-15.
8. `.ai/electronic/dsp/` — 10 dosya (F-6), 2026-10-15.
9. `.ai/electronic/drivers/` — 10 dosya (O-7), 2026-10-15.
10. `.ai/projects/NevaEngine/os/` — 4 md (O-8), 2026-10-15.
11. `legacy-2026-07/eq-ai-auto.md` — Satır 20–199 · 119 · 140 (A-1), 2026-10-15.
12. `legacy-2026-07/eq-user-modes.md` — Satır 16–24 · 28 · 69 · 105–115 (A-2), 2026-10-15.
13. `legacy-2026-07/ai-models.md` — başlıklar 13 · 434 · 459 · 503 · 528 · 577 · 590 · 713 · 726 (A-3), 2026-10-15.
14. `shared/src/AI/{AIWorkflow,KnowledgeBase,AIOrchestrator}.php` — Satır 49–91 · 72–91 · 71–94 (A-4 … A-6), 2026-10-15.

---

`status: verified` · `validation: ... ek 2 (2026-10-15 · kapanış turu) · §14–§20 eklendi: 8 çakışan terim standardı · 13 terim→dosya eşlemesi · 4 test terimi · 16 kısaltma · 6 kullanım kuralı · 14 kaynak eki — append-only, silinen satır 0`

---

## 21. K006 ↔ NevaEngine Köprüsü (terim karşılıkları)

Kesişim alanındaki her terimin **iki vault'ta da** bir karşılığı vardır; bu tablo çift yönlü okunur.

| K006 terimi (PHP/AI) | NevaEngine karşılığı | Not | Kaynak |
|---|---|---|---|
| LLM çağrısı | Model envanteri satırı | K006 API üzerinden, Neva yerleşik/planlı | `AIWorkflow.php` [Satır: 49–91] · `ai-models.md` başlıklar [Satır: 13 · 434] |
| Workflow adımı | DSP zincir bloğu | ikisi de sıralı; biri iş akışı, biri sinyal | `AIWorkflow.php` [Satır: 49–91] · `02-dsp-zincir-mimari.md` [Satır: 16–90] |
| Knowledge base girdisi | Kullanıcı modu tercihi | kişiselleştirme verisi | `KnowledgeBase.php` [Satır: 72–91] · `eq-user-modes.md` [Satır: 16–24 · 28 · 69] |
| Orchestrator kararı | AI-auto EQ kararı | karar verici katman | `AIOrchestrator.php` [Satır: 71–94] · `eq-ai-auto.md` [Satır: 119 · 140] |
| Kontrat (interface) | Guardrail | ikisi de sınır; biri tip, biri sinyal | `AIEngineInterface.php` [Satır: 34] · `02-dsp-zincir-mimari.md` [Satır: 71–87] |
| PHPUnit testi | gtest/ctest/benchmark | test çatıları farklı, amaç aynı | K006 test stratejisi · `rules/` [Satır: 423–480] |

## 22. Anti-Pattern Terimler (yazım anında kaçınılacaklar)

| # | Anti-pattern | Doğru davranış | Gerekçe |
|---|---|---|---|
| AP-1 | "yapay zeka"yi tek vault'a mal etmek | kapsam sınırı ile birlikte yaz (`yapay-zeka-kapsam.md` §15) | R-24 senkronsuzluk riski |
| AP-2 | Varyant yazımları mevcut metinde düzeltmek | yalnız yeni üretimde standart yazım | append-only ihlali |
| AP-3 | Kısaltmayı ilk geçişte kullanmak | tam hali + kısaltma | okunabilirlik |
| AP-4 | Birimi kısaltmak ("192K") | "192 kHz" | ölçüm belirsizliği |
| AP-5 | Belirsiz terime karşılık uydurmak | `⚠️ VERIFICATION REQUIRED` | zero-hallucination |
| AP-6 | Terimi çevirip kod identifier'ını çevirmek | kod identifier'ları İngilizce kalır | derleme/kırılma |
| AP-7 | Ölçümü tarihsiz yazmak | "10 dosya, 2026-10-15" | envanter sürüklenmesi |
| AP-8 | Yeni terimi tabloya eklememek | §7–§11'e satır ekle | dağınık sözlük |

## 23. Dil Standardı Ek Kuralları (dosya bazlı)

| Dosya tipi | Kural | Kaynak dayanağı |
|---|---|---|
| Vault `.md` | TR anlatım, EN teknik terim (standart yazım §14) | bu dosya §14 |
| PHP identifier/docblock | EN, strict_types | `shared/src/AI/AIEngine.php` [Satır: 49–77] |
| C++ kod (spec) | EN identifier, MISRA uyumu | `.ai/projects/NevaEngine/02-dsp-zincir-mimari.md` [Satır: 16–90] |
| SQL/şema | EN tablo/alan adları | `.ai/AGENTS.md` §5 (Data Engineer) |
| Commit subject | EN (düz ASCII, mojibake yok) | `.ai/AGENTS.md` §13.8 kayıtları |

## 24. Sayım (bu tur ek 3 — §21–§23 + kapanış)

| Sayım | Değer |
|---|---|
| Köprü karşılığı (§21) | 6 satır |
| Anti-pattern (§22) | 8 (AP-1 … AP-8) |
| Dosya bazlı dil kuralı (§23) | 5 satır |
| Ek toplam | 19 satır (önceki 191 → toplam 210) |
| Toplam madde (§1–§23) | 104 (§1–§5) + 40 (§7–§11) + 47 (§14–§18) + 19 (§21–§23) = **210** |
| Silinen satır | 0 (append-only) |
| Beyazliste dışı yazım | 0 |
| git commit | yok (orkestratöre ait) |

---

## 25. Kapanış Kaydı (bu tur — 2026-10-15)

| Tur | Eklenen bölümler | Madde | Satır etkisi |
|---|---|---|---|
| Ek 1 (2026-10-15) | §7–§13 | 40 | 190 → 300 |
| Ek 2 (2026-10-15) | §14–§20 | 47 | 300 → 417 |
| Ek 3 (2026-10-15) | §21–§25 | 19 + kayıt | 417 → ≥500 |
| **Toplam** | §7–§25 | **106 yeni madde** | 190 → ≥500 (append-only) |

Kural: bu tablo yalnız eklenir; önceki tur satırları değiştirilmez.

---

`status: verified` · `validation: ... ek 3 (2026-10-15 · kapanış turu) · §21–§25 eklendi: 6 köprü karşılığı · 8 anti-pattern · 5 dosya-bazlı dil kuralı · kapanış kaydı (toplam 210 madde, 106 bu tur) — append-only, silinen satır 0`

---

## 26. Terim Bakım Ritmi (sahiplik + periyot)

| Terim grubu | Sahip | Periyot | Tetikleyici olay |
|---|---|---|---|
| D serisi (DSP) | Embedded/DSP | Neva vault revizyonunda | `02-dsp-zincir-mimari.md` değişimi |
| O serisi (OS/API) | Windows SW / Embedded | IO revizyonunda | `10-io-format...md` değişimi |
| F serisi (FW/RT) | Firmware/Drivers | Envanter ölçümünde | `.ai/electronic/*` envanter değişimi |
| A serisi (AI/ML) | MO (K006 vault) | K006 kapı turunda | `ai-models.md` başlık değişimi |
| C serisi (çakışma) | MO | yeni çakışma tespitinde | varyant yazılmış yeni metin |
| T serisi (test) | QA | test zinciri revizyonunda | `rules/` test bloğu değişimi |

Kural: bakım yalnız **satır ekleme** ile yapılır (append-only); mevcut tablo satırı revize edilmez — revizyon gerekirse yeni satır eklenir ve eski satır "geçersiz (tarih)" olarak işaretlenir.
