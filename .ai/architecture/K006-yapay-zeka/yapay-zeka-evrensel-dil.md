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
  - K004-shared
  - K016-AI
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
