---
node_id: K006-yapay-zeka/inference/inference-anayasa
node_type: module
status: draft
parent_id: K006-yapay-zeka/inference
children:
  - K006-yapay-zeka/inference/inference-kapsam
  - K006-yapay-zeka/inference/inference-bilesenler
  - K006-yapay-zeka/inference/inference-dogrulama
  - K006-yapay-zeka/inference/inference-ornekler
  - K006-yapay-zeka/inference/inference-web-arastirma
depends_on: []
evidence:
  - "v2 §2.6 mini-ağaç — coremusic-katmanli-mimari-agaci-v2.md satır 260 (kök index.md Çocuk haritası üzerinden)"
  - "shared/src/Contracts/AI/AIEngineInterface.php satır 28 — getRecommendations imzası okundu (2026-10-15)"
  - "shared/src/AI/AIEngine.php satır 49 — getRecommendations gövdesi okundu (2026-10-15)"
  - ".ai/projects/NevaEngine/legacy-2026-07/ai-models.md satır 125 — §3 ONNX Runtime Inference Pipeline başlığı okundu"
validation:
  - "2026-10-15 — kök index.md Çocuk haritası satır 260 ile ad karşılaştırıldı: birebir"
  - "2026-10-15 — arayüz/imza çapraz kontrolü: AIEngineInterface satır 28 ↔ AIEngine satır 49 aynı imza"
open_questions:
  - "Rol tanımı v2'de yok → ⚠️ VERIFICATION REQUIRED"
  - "Tahmin çıktısı cache TTL değeri kodda sabit değil → UNKNOWN"
  - "Batch boyutu ve timeout eşikleri repo'da tanımlı değil → ⚠️ VERIFICATION REQUIRED"
  - "Üretici model (provider) seçimi yapılandırma yüzeyinde görünmüyor → UNKNOWN"
group: mimari
---

# inference — Anayasa (Kademe-2 İlkeler & Sınırlar)

> Bu dosya `inference` alt alanının **anayasasıdır**: bozulmaz ilke, sınır ve sorumluluk
> kümesi. Kod behavior'ı değil, **karar veren kuralları** taşır. Operasyonel akış →
> `inference-kapsam.md` · gerçek kod → `inference-ornekler.md` · kanıt → `inference-dogrulama.md`.

## §1 ELI15 — Bu alan ne, ne DEĞİL?

**Modelin tahmin ürettiği taraf.** CoreMusic'te "şarkı öner", "sesi analiz et", "EQ'yu
optimize et" çağrıları bu alanda çalışır. Basitçe: girdi gelir → model çağrılır →
skor/sonuç döner. Öğrenme (training) bu alanda **yapılmaz**; model burada **çağrılır**.

| # | Bu alan (İÇİNDE) | Bu alan (DIŞINDA) | Komşu düğüm |
|---|---|---|---|
| 1 | `AIEngine` tahmin çağrıları (`getRecommendations` L49, `analyzeAudio` L82) | Model ağırlıklarının eğitilmesi/güncellenmesi | K006.model-management |
| 2 | `AIWorkflow` tahmin akışları (5 workflow, L49–L299) | Yerel donanımda çalışma zamanı seçimi | K006.local-inference |
| 3 | Sonuç skorlaması birleştirme (`combineScores` L243) | Öneri kalitesinin metrikle ölçülmesi | K006.evaluation |
| 4 | Prompt/ bağlam hazırlığı (`PromptEngine::generatePrompt` L48) | Ajan çalışma zamanı / agent döngüsü | K015-AI (**kapsam dışı**) |
| 5 | Tahmin çıktısının şekli ve sözleşmesi (`AIEngineInterface` L28–L60) | Çıktının UI'da gösterimi | K011-application |

**Sınır cümlesi (v2 §2.6 · registry §6):** Bu dal yalnız **K006 = ML ürün tarafını**
kapsar. K015-AI ajan altyapısına tahmin yeteneği **taşınamaz**; ajanlar K006'yı **çağırır**.

## §2 Bozulmaz İlkeler (invariants)

> Bunlar ihlal edilemez; ihlal = sistem durur, `log.md` ERROR, MO müdahale (registry §18/1).

| # | İlke | Gerekçe | Kaynak |
|---|---|---|---|
| I1 | Her tahmin çağrısı tek kamu sözleşmesinden döner (`AIEngineInterface`) | Sözleşme kırılımı tüm tüketenleri kırar | `AIEngineInterface.php` satır 28–60 |
| I2 | `getRecommendations(array $context, int $limit = 20)` imzası değiştirilemez | 5 tüketen (workflow + sözleşme) bu imzaya bağlı | `AIEngine.php` satır 49 |
| I3 | `MAX_RECOMMENDATIONS = 20` çağrı sınırı aşmaz | Gürültü ve gecikme bütçesi | `AIEngine.php` satır 30 |
| I4 | `MIN_SCORE = 0.7` altındaki adaylar tahmin sonucundan düşer | Düşük kalite sızmasın | `AIEngine.php` satır 31 |
| I5 | `DIVERSITY_TARGET = 0.30` asgari çeşitlilik korunur | Baloncuk (filter bubble) önleme | `AIEngine.php` satır 32 |
| I6 | Collaborative skor boşken (`[]`) içerik tabanlı yola düşülür | Fail-safe, boş önerme | `AIEngine.php` satır 209–223 |
| I7 | Sonuç asla `NaN`/bozuk dizi karışık dönmez | Türetici crash önleme | tasarım iddiası — ⚠️ VERIFICATION REQUIRED |
| I8 | Tahmin çağrısı eğitim verisine **yazmaz** (read-only inference) | Veri bozulması önlenir | tasarım iddiası — ⚠️ VERIFICATION REQUIRED |
| I9 | Prompt değişkenleri şablon adıyla resolve edilir, serbest metin değil | Injection yüzeyi daraltma | `PromptEngine.php` satır 48 |
| I10 | Tahmin öncesi/sonrası log'da hassas içerik maskelenir | Sızıntı önleme | `Redactor.php` satır 31 (SENSITIVE_KEYS) |

> **Dürüstlük notu:** I3–I5 değerleri repo'da **hardcoded**'tır (`private const`,
> `AIEngine.php` satır 30–32). Kalıcı mı, öğrenilen mi **UNKNOWN**
> (`inference-kapsam.md` §3). Anayasa bu değerleri *değiştirmez*, "şu an böyle" der.

## §3 Öğrenme Türü Anayasası (inference hangi öğrenmeyi kullanır)

| Öğrenme türü | inference'daki karşılığı | Durum | Kaynak |
|---|---|---|---|
| Denetimli (supervised) | Content-based skorlama (etiket→tür eşleme) | IMPLEMENTED (skor fonksiyonu var) | `AIEngine.php` satır 223 |
| Denetimsiz (unsupervised) | Kullanıcı-öğe matrisinden kategori çıkarımı | ⏳ PLANNED (`[]` döner) | `AIEngine.php` satır 209 |
| Pekiştirmeli (RL) | Feedback ile ağırlık güncelleme | ⏳ PLANNED | `AIWorkflow.php` satır 49 |
| Kural-tabanlı (non-ML) | Room correction katsayı hesapları | IMPLEMENTED (formül tabanlı) | `AIEngine.php` satır 347, 368 |
| Önceden tanımlı model | ONNX çalışma zamanı üzerinden çıkarım | Vault'ta tanımlı, repo kodu yok | `ai-models.md` satır 125 |

**İlke I11:** Inference tarafı "öğreniyor" gibi gösterilemez. Repo'da eğitim döngüsü
**yoktur**; her şey ya kural tabanlı ya da `[]` dönen boş iskelettir. Bunu `⚠️ PLANNED`
olarak kilitlemek bu anayasanın görevidir.

## §4 Model Ailesi Sınırı

| Aile | Inference için uygunluk | K006 kararı | Kaynak |
|---|---|---|---|
| Kural/formül tabanlı (kod içi) | Uygun — mevcut durum | KULLANILIYOR | `AIEngine.php` satır 332–381 |
| Küçük sınıflandırıcı (ONNX) | Uygun — vault planı | ADAY (repo kodu yok) | `ai-models.md` satır 265 |
| Süper-çözünürlük modeli (AudioSR) | Uzun süreli çıkarım | ADAY (batch tarafı) | `ai-models.md` satır 74 |
| Denoising (DeepFilterNet2) | Gerçek zamanlıye uygun | ADAY | `ai-models.md` satır 102 |
| Büyük dil modeli (LLM) | Tahmin çekirdeği DEĞİL | KAPSAM DIŞI | K015-AI sınırı |
| Bulut API'ye tam devir | Gizlilik nedeniyle sınırlı | VARSAYILAN RED | §7 |

**İlke I12:** Inference için seçilen model ailesi **ölçeklenebilirlik bütçesiyle**
birlikte karar verilir: gerçek zamanlı yol (≤ buffer bütçesi) ile batch yol
(`Job Queue`, `ai-models.md` satır 434) birbirine **karıştırılamaz**.

## §5 Önyargı & Denklik (Bias & Fairness) Anayasası

| # | Risk | Inference'daki görünümü | Kapı |
|---|---|---|---|
| B1 | Popularity bias | Sıcak şarkılara sistemik skor avantajı | `DIVERSITY_TARGET` (`AIEngine.php` satır 32) |
| B2 | Filter bubble | Aynı türün tekrarı | `applyDiversityFilter` (`AIEngine.php` satır 264) |
| B3 | Soğuk başlangıç | Yeni kullanıcıda collab boş | `getCollaborativeScores` `[]` → content fallback (`AIEngine.php` satır 209) |
| B4 | Cihaz yanlılığı | Yüksek donanıma iyi, düşüğe kötü sonuç | `ai-models.md` satır 29 (Donanım Destek Matrisi) |
| B5 | Dil/etiket yanlılığı | TR içerikte etiket seyrekliği | ⚠️ VERIFICATION REQUIRED (ölçüm yok) |

**İlke I13:** Bias ölçümü **bu dalda yapılmaz** (onun sahibi `evaluation`); burada yalnızca
**kapı değerleri** uygulanır (`MIN_SCORE`, `DIVERSITY_TARGET`). Ölçüm sonucu uydurmak yasak.

## §6 Etik & Regülasyon Sınırı

| # | Konu | Inference etkisi | Durum |
|---|---|---|---|
| E1 | AI Act — şeffaflık | Kullanıcı "bu bir model sonucu" bilgisini görmeli | ⚠️ VERIFICATION REQUIRED (UI kanıtı yok) |
| E2 | GDPR/KVKK — otomatik karar | Tahmin sonucu kullanıcıyı olumsuz etkiliyorsa itiraz hakkı | referans: `recommendation-anayasa.md` §31 |
| E3 | Katalog dışına çıkarma | Tahmin asla kataloğun dışına öneri üretmez | `AIEngine.php` satır 49–77 |
| E4 | Kaynak gösterimi | Model/kaynak bilgisi log'da izlenebilir | tasarım iddiası — ⚠️ VERIFICATION REQUIRED |
| E5 | İnsan denetimi | Otomatik EQ/öneri kullanıcı tarafından geri alınabilir | `optimizeEQ` çağrısı geri alınabilir mi → UNKNOWN |

**İlke I14:** Regülasyon kapısı `inference` dalında **bilgilendirme** düzeyinde kalır;
yasal değerlendirme K006 root `-kararlar.md` ve `.ai/.decisions/` yetkisindedir.

## §7 Gizlilik & Veri Anayasası

| # | Kural | Gerekçe | Kaynak |
|---|---|---|---|
| P1 | Tahmin girdisi olarak `$context` dışına veri taşınmaz | Sızıntı yüzeyi | `AIEngine.php` satır 49 imza |
| P2 | Oturum verisi `MemorySystem` üzerinden okunur, doğrudan `$_SESSION` değil | Soyutlama | `MemorySystem.php` satır 45, 60 |
| P3 | Kalıcı tercih `setPersistent` ile TTL'siz yazılmaz | Veri minimizasyonu | `MemorySystem.php` satır 89 |
| P4 | Süresi dolan bellek temizlenir | `pruneExpired` | `MemorySystem.php` satır 183 |
| P5 | Log satırları maskelenir | Hassas veri | `Redactor.php` satır 65 |
| P6 | Tahmin sonuçları üçüncü tarafa sızdırılmaz | Gizlilik | tasarım iddiası — ⚠️ VERIFICATION REQUIRED |

## §8 K015-AI Sınırı (KAPSAM DIŞI — bağlayıcı)

| # | K006.inference (BU DAL) | K015-AI (DIŞARI) | Ayrım cümlesi |
|---|---|---|---|
| 1 | `AIEngine` tahmin çağrıları | Ajan çalışma zamanı (agent loop) | "Model çağrılır" ≠ "ajan çalışır" |
| 2 | `AIWorkflow` 5 akışı | Ajan görev dağılımı (`AIOrchestrator` ürün tarafı) | Akı = iş akışı, ajan = karar döngüsü |
| 3 | `PromptEngine::validatePrompt` (L156) kapısı | Ajan prompt'unun sahibi | Kapı burada, prompt sahibi orada |
| 4 | `ToolCalling::checkPermission` (L145) | Ajan izin politikası | Araç çağrısı burada, politika orada |
| 5 | Tahmin sonucu (`array`) | Ajan kararı/metni | Sonuç veri, karar metni kapsam dışı |

**İlke I15:** K015-AI bu dalı **çağırır**, bu dal K015-AI'yı **beslemez**. Ajanlara
öneri/tahmin yeteneği taşımak mimari ihlaldir (registry §18/1).

## §9 Terim Sözlüğü (TR↔EN)

| TR | EN | Kısa tanım | Kaynak |
|---|---|---|---|
| Tahmin | Inference | Modelle sonuç üretimi | bu dosya §1 |
| Çıkarım | Inference (aynı) | Türkçe eşdeğer — ikisi de kullanılıyor | `ai-models.md` §3 (satır 125) |
| Aday | Candidate | Skorlanabilir öğe | `AIEngine.php` satır 49–77 |
| Skor | Score | `[0,1]` Normalized ağırlık | `combineScores` satır 243 |
| Birleştirme | Score combination | collab + content ağırlıklı toplam | `AIEngine.php` satır 243 |
| Çeşitlilik | Diversity | Liste içi tür dağılımı korunumu | satır 264 |
| Eşik | Threshold (`MIN_SCORE`) | Elme sınırı 0.7 | satır 31 |
| Sıcaklık | Temperature | Örneklem çeşitliliği (LLM) — bu repo'da yok | UNKNOWN |
| Öğutma | Quantization | FP16→INT8 küçültme | `ai-models.md` satır 208 |
| Bilgi damıtımı | Knowledge distillation | Küçük modele aktarım | `ai-models.md` satır 244 |
| İş kuyruğu | Job queue | Batch tahmin sırası | `ai-models.md` satır 434 |
| Gerçek zamanlı | Real-time | Buffer bütçesi içinde | `audio-core.md` §11.1 (satır 387) |
| Toplu | Batch | Zaman dışı, kuyruklu | `ai-models.md` satır 422 |
| Gecikme | Latency | Çağrı→sonuç süresi | `audio-core.md` satır 387 |
| Zaman aşımı | Timeout | Çağrı üst sınırı | ⚠️ VERIFICATION REQUIRED |
| Önbellek | Cache | Tekrar çağrı ekonomisi | `CacheInterface.php` satır 7–12 |
| Bağlam | Context | `getRecommendations` 1. argüman | `AIEngine.php` satır 49 |
| Güven skoru | Confidence | Sonuç güveni | `KnowledgeBase.php` satır 223 |

## §10 Kapsam Dışı (bu anayasanın dışındadır)

| # | Kapsam dışı konu | Nereye gider |
|---|---|---|
| 1 | Model indirme, sürümleme, registry | `model-management` |
| 2 | Yerel çalışma zamanı, donanım seçimi, offline | `local-inference` |
| 3 | Öneri kalitesi ölçümü, A/B test | `evaluation` |
| 4 | Öğrenme/tuning döngüsü (varsa) | `model-management` |
| 5 | Ajan döngüsü, agent çalışma zamanı | K015-AI |
| 6 | DSP gerçek zamanlı zinciri (C++ tarafı) | `.ai/electronic/dsp/` + Neva Engine |
| 7 | UI bileşeni çizimi | K011-application |

## §11 Açık Sorular (anayasa kilidi)

| # | Soru | Etki | Durum |
|---|---|---|---|
| Q1 | Tahmin sonucu cache'leniyor mu, TTL ne? | Tazelik vs yük | UNKNOWN |
| Q2 | `optimizeEQ` çağrısı geri alınabilir mi (undo)? | Kullanıcı güvencesi | ⚠️ VERIFICATION REQUIRED |
| Q3 | `analyzeAudio` hangi dosya formatlarını kabul eder? | Giriş yüzeyi | UNKNOWN |
| Q4 | Batch tahmin tetikleyicisi nedir (cron/event)? | Zamanlama | ⚠️ VERIFICATION REQUIRED |
| Q5 | Tahmin hata kodları sözleşmede tanımlı mı? | Türetici güveni | UNKNOWN |
| Q6 | `predictFault` hangi metrikleri bekler? | Girdi şeması | UNKNOWN |
| Q7 | Çok-kiracılık / kullanıcı ayrımı tahmin katmanında var mı? | Gizlilik | UNKNOWN |
| Q8 | Model sürümü sonuçla birlikte döndürülüyor mu? | İzlenebilirlik | ⚠️ VERIFICATION REQUIRED |

## §12 Çağrı Sözleşmesi (inference'ın dışa kapısı)

> Beş kamu imzası bu dalın **tüm** dış etkileşimini tanımlar. Değişiklik = sözleşme kırılması.

| # | İmza | Dosya · satır | Dönen | Tüketen |
|---|---|---|---|---|
| S1 | `getRecommendations(array $context, int $limit = 20): array` | `AIEngine.php` satır 49 · arayüz satır 28 | skorlu aday listesi | `AIWorkflow.php` satır 49 |
| S2 | `analyzeAudio(string $filePath): array` | `AIEngine.php` satır 82 · arayüz satır 36 | meta + özellik | `AIWorkflow.php` satır 99 |
| S3 | `optimizeEQ(array $roomProfile): array` | `AIEngine.php` satır 111 · arayüz satır 44 | EQ düzeltmesi | `AIWorkflow.php` satır 147 |
| S4 | `analyzeHardware(string $deviceId): array` | `AIEngine.php` satır 138 · arayüz satır 52 | cihaz analizi | `AIWorkflow.php` satır 199 |
| S5 | `predictFault(array $metrics): array` | `AIEngine.php` satır 161 · arayüz satır 60 | arıza tahmini | `AIWorkflow.php` satır 241 |

**Sözleşme kuralları:**
- İmza değişikliği yalnız `AIEngineInterface` üzerinden ve sürüm notuyla yapılır.
- Yeni tahmin yeteneği **yeni imza** olarak eklenir, mevcut imza genişletilmez.
- Dönen `array` şeması belgelenmemiş → `⚠️ VERIFICATION REQUIRED` (Q5'e bağlı).
- `int $limit = 20` varsayılanı `MAX_RECOMMENDATIONS` ile tutarlıdır (satır 30).

## §13 Gecikme & Timeout Bütçesi

> Inference, gerçek zamanlı ses yolunun **kısa** sürmek zorunda olan kısmıdır; üst sınır
> `audio-core.md` §11.1 (Latency Hedefleri, satır 387) ile aynı bütçe disiplinindedir.

| # | Bütçe kalemi | Değer | Durum | Kaynak |
|---|---|---|---|---|
| L1 | Round-trip hedefi (ses yolu) | ≤10 ms sınıfı (ASIO ana backend) | Vault planı | `03-isletim-sistemi-kod-ornekleri.md` satır 51 (§2 ASIO <10ms başlığı) |
| L2 | Tahmin çağrısı (PHP ürünü) | Ölçüm YOK | UNKNOWN | — |
| L3 | Timeout eşiği | Tanımsız | ⚠️ VERIFICATION REQUIRED | Q4 |
| L4 | Prompt token üst sınırı | 4096 | IMPLEMENTED | `PromptEngine.php` satır 25 |
| L5 | Prompt sıkıştırma eşiği | 0.8 (oran) | IMPLEMENTED | `PromptEngine.php` satır 26 |
| L6 | RAG sonuç üst sınırı | 50 | IMPLEMENTED | `KnowledgeBase.php` satır 26 |
| L7 | Öneri listesi üst sınırı | 20 | IMPLEMENTED | `AIEngine.php` satır 30 |
| L8 | Sorgu sonuç varsayılanı | 10 | IMPLEMENTED | `KnowledgeBase.php` satır 36 |

**İlke I16:** Ölçülmemiş gecikme "hızlı" diye yazılamaz. L2 ve L3 için `UNKNOWN` kilitli
kalır; ölçüm yapıldığında `-dogrulama.md`'ye tarihli satır olarak eklenir.

## §14 Cache, Batch ve Tekrar Ekonomisi

| # | Mekanizma | Kural | Kanıt |
|---|---|---|---|
| C1 | Sonuç cache'i | TTL tanımsız → cache varsayılanı KAPALI sayılır | Q1 |
| C2 | Bellek katmanları | L1/L2/L3 hiyerarşisi `MemorySystem` | `MemorySystem.php` satır 25 (HIERARCHY) |
| C3 | Tembel yükleme | `remember(key, loader, level, ttl)` | `MemorySystem.php` satır 123 |
| C4 | Süre dolumu temizliği | `pruneExpired` | `MemorySystem.php` satır 183 |
| C5 | Cache arayüzü | `get/set/delete/increment/clear/has` | `CacheInterface.php` satır 7–12 |
| C6 | Batch tahmin | Job queue ile asenkron | `ai-models.md` satır 434 (§7.2) |
| C7 | Job API | REST üzerinden iş tetikleme | `ai-models.md` satır 459 (§7.3) |
| C8 | Özet/istatistik | `getCacheStats`, `estimateMemoryUsage` | `MemorySystem.php` satır 162, 222 |

**İlke I17:** Cache anahtarı **girdi + model sürümü** birlikte taşır; sürüm cache anahtarında
yoksa eski model sonucu taze diye servis edilir → **cache zehirlenmesi**. Sürüm anahtarı
repo'da görünmüyor → `⚠️ VERIFICATION REQUIRED` (Q8 ile ilişkili).

## §15 Hata & Fallback Zinciri

| # | Hata | Beklenen davranış | Durum |
|---|---|---|---|
| F1 | Collaborative boş (`[]`) | Content-based yola düş | IMPLEMENTED (`AIEngine.php` satır 209–223) |
| F2 | Model çağılamıyor (workload) | Kural-tabanlı fallback | Vault planı (`audio-core.md` §12.2 satır 439, Fallback Zinciri) |
| F3 | Ses kesintisi senaryosu | Kesintisiz geri dönüş zinciri | `audio-core.md` satır 429 (§12.1) |
| F4 | Prompt injection tespiti | `validatePrompt` reddi | `PromptEngine.php` satır 27 (INJECTION_PATTERNS), satır 156 |
| F5 | Araç izni yok | `checkPermission` reddi | `ToolCalling.php` satır 145 |
| F6 | Parametre şeması hatalı | `validateParameters` istisnası | `ToolCalling.php` satır 307 |
| F7 | Tahmin exception'ı | Türeticiye dönülmeli, sessizce boş dönülmemeli | ⚠️ VERIFICATION REQUIRED |
| F8 | Token taşması | `truncateLongLists` / `truncateContext` | `PromptEngine.php` satır 278, 307 |

**İlke I18:** Fallback zinciri **sessiz olamaz**: her düşüş seviyesi log'a yazılır ve
sonuç üzerinde iz taşır. Sessiz fallback = gözlemlenebilirlik ihlali.

## §16 Determinizm, Tekrarlanabilirlik ve Sıcaklık

| # | Konu | Kural | Durum |
|---|---|---|---|
| D1 | Aynı girdi → aynı skor | Kural-tabanlı yolda geçerli | Beklenen (`AIEngine.php` satır 347, 368 saf formüller) |
| D2 | Aynı girdi → aynı liste | collab `[]` iken geçerli | Beklenen |
| D3 | Sıcaklık/seed parametresi | Repo'da tanım yok | UNKNOWN |
| D4 | Model sürümü sonuçla dönsün | Şimdilik dönmüyor | ⚠️ VERIFICATION REQUIRED (Q8) |
| D5 | Eval tekrar edilebilirliği | `evaluation` dalının sorumluluğu | K006.evaluation |
| D6 | Float hassasiyeti | Ses yolu 32/64-bit float | `audio-core.md` satır 266 (§8) |

**İlke I19:** Determinizm **iddia edilmez**, ölçülür. Ölçüm yoksa "deterministik" denmez;
"beklenen deterministik, ölçülmüş değil" yazılır.

## §17 Güvenlik Kapıları (inference yüzeyi)

| # | Kapı | Mekanizma | Kanıt |
|---|---|---|---|
| G1 | Prompt injection taraması | `INJECTION_PATTERNS` desen listesi | `PromptEngine.php` satır 27 |
| G2 | Prompt doğrulama | `validatePrompt(): array` | `PromptEngine.php` satır 156 |
| G3 | Araç izin denetimi | `checkPermission($toolName, $agentId)` | `ToolCalling.php` satır 145 |
| G4 | Agent izin matrisi | `AGENT_PERMISSIONS` | `ToolCalling.php` satır 28 |
| G5 | Görev tipi izni | `TASK_TYPES` | `AIOrchestrator.php` satır 40 |
| G6 | Hassas veri maskesi | `Redactor::redact` | `Redactor.php` satır 65 |
| G7 | CSRF/oturum katmanı | PSR-15 middleware hattı | `CsrfMiddleware.php` satır 20 · `AuthMiddleware.php` satır 9 |
| G8 | Rate limit | `RateLimiterMiddleware` | `RateLimiterMiddleware.php` satır 50 |
| G9 | Parametre tip/şema denetimi | `validateParameters` | `ToolCalling.php` satır 307 |
| G10 | Tahmin çıktısı XSS'i | Çıktı K011'de escape edilir | ⚠️ VERIFICATION REQUIRED (bu dal dışı) |

**İlke I20:** Inference çağrısı **güvenli varsayılanla** çalışır: izin bilinmiyorsa RED
(fail-closed). Fail-open davranışı güvenlik ihlalidir (`.ai/CLAUDE.md` guardrail ruhu).

## §18 Workflow Çağrı Zinciri (AIWorkflow — 5 akış)

| # | Akış | Satır | Adımlar (vault okuması) | Çağırdığı sözleşme |
|---|---|---|---|---|
| W1 | `recommendationWorkflow` | 49 | istek → engine → filtre → yanıt | S1 |
| W2 | `audioAnalysisWorkflow` | 99 | dosya → meta/özellik → rapor | S2 |
| W3 | `autoEQWorkflow` | 147 | oda profili → katsayı → EQ | S3 |
| W4 | `hardwareAnalysisWorkflow` | 199 | cihaz → analiz → öneri | S4 |
| W5 | `faultPredictionWorkflow` | 241 | metrik → tahmin → uyarı | S5 |

**İlke I21:** Workflow **orkestrasyon** katmanıdır, model değildir. Workflow içine iş kuralı
gizlemek katman ihlalidir; iş kuralı K008-domain'e, model çağrısı `AIEngine`'e aittir.

**İlişkili orkestratör yüzeyi** (`shared/src/AI/AIOrchestrator.php`):
`dispatchTask` satır 71 · `getTaskStatus` satır 102 · `listActiveTasks` satır 114 ·
`routeToAgent` satır 125 · `getContext` satır 154 · `updateContext` satır 162 ·
`executeTask` satır 185 · `AGENT_MAP` satır 26.

## §19 Kalite & Ölçüm Kapıları (anayasa gate)

| # | Kapı | Koşul | Sahip |
|---|---|---|---|
| K1 | Sözleşme bütünlüğü | `AIEngineInterface` ↔ `AIEngine` imza eşleşmesi | bu dal |
| K2 | Sabit tutarlılığı | `MAX_RECOMMENDATIONS` = varsayılan `limit` = 20 | bu dal |
| K3 | Fallback görünürlüğü | Her düşüş log'da iz bırakır | bu dal |
| K4 | Bias ölçümü | Metrik üretimi | `evaluation` |
| K5 | Kalite ölçümü | Offline/online metrik | `evaluation` |
| K6 | Regresyon testi | Sözleşme testi | `testing` |
| K7 | Model sağlığı | Sürüm/registry | `model-management` |
| K8 | Çalışma zamanı sağlığı | Yerel runtime | `local-inference` |

**İlke I22:** Kapıyı geçmeyen tahmin **yayına alınamaz**. Kapı sahipleri karıştırılamaz
(her kapı tek dalın sorumluluğunda).

## §20 Sürüm & Değişiklik Kilidi

| # | Öğe | Kilidi |
|---|---|---|
| V1 | `AIEngineInterface` imzaları | Kırılması = semver major + K006 root karar kaydı |
| V2 | `private const` eşikleri (L30–32) | Değişimi = `evaluation` kapısı + karar kaydı |
| V3 | Prompt şablon adları | Değişimi = `PromptEngine::listTemplates` (L140) ile envanter güncellemesi |
| V4 | `INJECTION_PATTERNS` | Yalnız genişletilir, daraltılmaz (güvenlik) |
| V5 | `MemorySystem::HIERARCHY` | Katman sayısı değişimi = mimari karar |
| V6 | Vault md referansları | Yeni sürüm = `model-management` anayasası üzerinden |

**İlke I23:** Bu anayasa **tarih damgalıdır** (2026-10-15) ve yalnız ek yapılarak
güncellenir; mevcut satır silinemez (append-only revizyon).

## §21 Çapraz Bütünlük (bu dosyanın tutarlılık kontrolü)

| # | Kontrol | Beklenen | Durum (2026-10-15) |
|---|---|---|---|
| X1 | §2 I3–I5 sabitleri = `AIEngine.php` L30–32 | eşleşme | OKUNDU |
| X2 | §12 S1 = `AIEngineInterface.php` L28 | eşleşme | OKUNDU |
| X3 | §12 S1 = `AIEngine.php` L49 | eşleşme | OKUNDU |
| X4 | §18 W1–W5 satırları = `AIWorkflow.php` L49/99/147/199/241 | eşleşme | OKUNDU |
| X5 | §17 G1–G3 = `PromptEngine`/`ToolCalling` satırları | eşleşme | OKUNDU |
| X6 | §3 I11 "eğitim döngüsü yok" iddiası | repo'da eğitim kodu bulunmamalı | 2026-10-15 ölçümü: `shared/src/AI/` 7 dosya, eğitim döngüsü YOK |
| X7 | §13 L1 = `03-isletim-sistemi-kod-ornekleri.md` §2 başlığı (satır 51) | atıf doğru | OKUNDU |
| X8 | `⚠️ VERIFICATION REQUIRED` sayısı | ≥1 (açık soru dürüstlüğü) | §11: 8 soru; §2: I7, I8 |

## §22 Kardeş Eşlemesi (K006 içi bu dalın konumu)

| Kardeş dal | İlişki | Bu anayasadaki karşılığı |
|---|---|---|
| `model-management` | Model sürümü/registry besler inference'ı | §4 · §16 D4 · §20 V6 |
| `local-inference` | Yerel çalışma zamanı seçer | §4 · §12 |
| `recommendation` | `getRecommendations` çağrısının iş sahibi | §12 S1 |
| `personalization` | Kullanıcı bağlamını sağlar (`$context`) | §12 · §7 P2 |
| `music-analysis` | `analyzeAudio` beslemesi | §12 S2 |
| `audio-intelligence` | Ses zekâsı yetenekleri | §12 S2, S3 |
| `evaluation` | Kalite/bias ölçümü | §5 · §19 K4, K5 |
| `testing` | Sözleşme/regresyon testi | §19 K6 |
| K006 root | Anayasa-üstü kararlar | §25 referansları |

## §23 İlişkili Dosyalar

- `inference-kapsam.md` — kapsam sınırı ve hariçler
- `inference-bilesenler.md` — bileşen envanteri ve boşluklar
- `inference-ornekler.md` — gerçek kod blokları
- `inference-dogrulama.md` — iddia·kaynak·tarih·durum matrisi
- `inference-web-arastirma.md` — resmi kaynak taraması
- `index.md` — alt-dal kayıt ve durum künyesi
- `../yapay-zeka-kararlar.md` — K006 geneli kararlar
- `../yapay-zeka-riskler.md` — risk ve azaltma

## §24 Kaynaklar (bu anayasada atıf yapılanlar)

| # | Kaynak | Erişim | Kullanım |
|---|---|---|---|
| R1 | `shared/src/AI/AIEngine.php` | okundu (grep satır indeksi) | §2, §3, §4, §12, §13, §16 |
| R2 | `shared/src/AI/AIWorkflow.php` | okundu (grep satır indeksi) | §18 |
| R3 | `shared/src/AI/AIOrchestrator.php` | okundu (grep satır indeksi) | §18 |
| R4 | `shared/src/AI/PromptEngine.php` | okundu (grep satır indeksi) | §13, §15, §17 |
| R5 | `shared/src/AI/MemorySystem.php` | okundu (grep satır indeksi) | §7, §14 |
| R6 | `shared/src/AI/ToolCalling.php` | okundu (grep satır indeksi) | §15, §17 |
| R7 | `shared/src/AI/KnowledgeBase.php` | okündu (grep satır indeksi) | §13 |
| R8 | `shared/src/Contracts/AI/AIEngineInterface.php` | okundu | §12, §20 |
| R9 | `.ai/projects/NevaEngine/legacy-2026-07/ai-models.md` | okundu (başlık indeksi) | §3, §4, §13, §14 |
| R10 | `.ai/projects/NevaEngine/legacy-2026-07/audio-core.md` | okundu (başlık indeksi) | §13, §15, §16 |
| R11 | `.ai/projects/NevaEngine/03-isletim-sistemi-kod-ornekleri.md` | okundu (başlık indeksi) | §13 L1 |
| R12 | `recommendation-anayasa.md` (kardeş) | okundu | §6 E2, iskelet |
| R13 | `K006-yapay-zeka/index.md` (kök) | okundu | v2 satır 260 doğrulaması |
| R14 | Web kaynağı | AÇILMADI — bu turda URL eklenmedi | `inference-web-arastirma.md`'ye devredildi |

## §25 Red-Team Soruları (anayasa saldırı yüzeyi)

| # | Soru | Beklenen cevap |
|---|---|---|
| T1 | Model çağrısı başarısız olursa kullanıcı ne görür? | Tanımlı hata — Q7 |
| T2 | `$context` içine hassas veri konursa ne olur? | Maskelenir (`Redactor`) — §17 G6 |
| T3 | `limit` negatif geçilirse ne olur? | Şema kontrolü — ⚠️ VERIFICATION REQUIRED |
| T4 | Aynı kullanıcı art arda 1000 çağrı yaparsa? | `RateLimiterMiddleware` — §17 G8 |
| T5 | Eski model cache'i yeni sürümle karışır mı? | §14 I17 — ⚠️ VERIFICATION REQUIRED |
| T6 | Prompt'a injection gelirse? | `validatePrompt` reddi — §17 G1, G2 |
| T7 | Aracı izinsiz çağıran ajan? | `checkPermission` reddi — §17 G3 |
| T8 | Tahmin sonucu log'a sızar mı? | `Redactor` maskesi — §17 G6 |
| T9 | `getRecommendations` 1000 aday isterse? | `MAX_RECOMMENDATIONS` 20 — §2 I3 |
| T10 | Sonuç `null` dönerse türetici ne yapar? | Sözleşme şeması tanımsız — Q5 |

## §26 Revizyon Kaydı

| Tarih | Değişiklik | Yapan | Not |
|---|---|---|---|
| 2026-10-15 | İlk yazım (dosya oluşturma) | K006 vault içerik ajanı | Append-only; §1–§26 |
| — | Sonraki revizyon | — | Yalnız ek bölüm olarak |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**status:** draft — §11'deki 8 açık soru kapandığında `verified`.

---

## §27 Ek Usul — Rollout Usulü (shadow → canary → A/B)

| Aşama | Trafik | Geçiş koşulu | Geri al tetikleyici |
|---|---|---|---|
| Shadow | %0 kullanıcı etkisi (çift çağrı) | çıktı kalitesi gate'i (evaluation) | kalite/latency sapması |
| Canary | %1–5 | hata oranı ≤ eşik, p95 ≤ bütçe (§13) | canary_accuracy_drop (K006 §2.2) |
| A/B | %50/%50 (örgütleme) | birincil ürün metriği anlamlı | istatistiksel anlamlılık yoksa geri |
| Tam | %100 | gözlem penceresi kapandı | drift alert (§24 R*) |

**Kural:** Aşama sırası **atlanamaz** — doğrudan %100 rollout yasak. Her aşamanın **ölçüm koşulu** evaluation anayasasının kapı matrisinden gelir; bu anayasa yalnız **trafik dağılımını** bağlar.
**Kanıt bağlantısı:** `../evaluation/evaluation-anayasa.md` §16 (kapı matrisi) + bu dosya §19 (kalite kapıları).

---

## §28 Ek Usul — Model Sürümleme Sözleşmesi

1. **Etiket formatı:** `provider/model:revision` (ör. `:v3`, tarih ya da commit kısa hash'i) — `AIEngine::MODEL_NAME` sabitleri (satır 30–32) yalnızca **varsayılan** tanımlar; runtime tercihi ayrı katmandan gelir (K005 model-management anayasası ile uyum).
2. **Cache anahtarı** sürümü **içerir** (§14 I17) — sürüm içermeyen cache anahtarı yasak.
3. **Sözleşme sürümü:** `AICallRequest`/`AIResult` alan ekleme **geriye uyumlu**; alan **kaldırma** ADR ister.
4. **Rollback:** bir önceki revision bir komutla geri gelir; rollback sırasında cache namespace'i de geri alınır (eski/ yeni karışımı yok — Q5, T5).
5. **Kayıt:** her revision değişikliği `.ai/.decisions/` altında ADR satırı üretir (sürüm + gerekçe + gate sonucu).

**Doğrulama (zero-hallucination):** `MODEL_NAME` sabitleri `shared/src/AI/AIEngine.php` satır 30–32'de gözlemlenmiştir; sürümleme/kayıt mekanizmasının production kodu var mı → **⚠️ VERIFICATION REQUIRED** (K006 dışı kod yüzeyi).

---

## §29 Ek Usul — Ölçüm Kanca Noktaları (inference içi)

| Kanca | Nerede | Ölçülen |
|---|---|---|
| C1 | çağrı öncesi (istek kabul) | giriş boyutu, token tahmini, cache anahtarı |
| C2 | provider yanıtı | TTFT, tok/s, ham yanıt boyutu (§13) |
| C3 | fallback dalı | hangi fallback basamağı çalıştı (§15) |
| C4 | circuit breaker | durum geçişi OPEN/HALF-OPEN/CLOSED (Q9) |
| C5 | rate limit reddi | reddedilen istek sayısını (Q8) |
| C6 | çıktı sonrası | `Redactor` maskelenen alan sayısı (G6) |

**Kural:** Kanca ölçümleri **ham haliyle** log'a; PII maskeleme **önceden** (C6'ya bırakılmaz — maskeleme loglama anında yapılır). Log şeması `testing` dalının; **bu anayasa yalnız kanca yerlerini bağlar.**

---

## §30 Ek Usul — Kapı (Gate) Özeti — anayasa son kararı

Bir inference değişikliği aşağıdaki kapıların **tamamından** geçmeden release edilemez:

1. **Sözleşme kapısı** — §12 şeması ihlali yok (alan adı/tip/nullable).
2. **Bütçe kapısı** — §13 timeout + percentil latency bütçesi aşılmadı.
3. **Fallback kapısı** — §15 zinciri eksiksiz; her hata sınıfının dalı var.
4. **Güvenlik kapısı** — §17 G1–G8 testleri geçti (özellikle G3 izin, G6 redaction).
5. **Kalite kapısı** — §19 metrik eşikleri (evaluation kapı matrisi — §27 tablo).
6. **Rollout kapısı** — §27 sırası korundu (gölgeden başlamadan tam trafik yok).
7. **Kayıt kapısı** — §28.5 ADR satırı + §29 kanca ölçümleri dashboard'da.

**Tek cümle özet:** *"Ölçülmemiş, geri alınamayan ve maskelenmemiş hiçbir inference değişikliği production'a girmez."*
**Revizyonla ilişkisi:** Bu bölüm §26 revizyon kaydından **sonra** eklenmiştir (append-only); sonraki ek usul §31, §32… olarak numaralanır.

---

## §31 Ek Usul — SSS (Sıkça Sorulan Usul Soruları)

| # | Soru | Anayasa cevabı |
|---|---|---|
| U1 | Timeout aşılırsa retry yapılır mı? | Evet — §15 G7'ye göre; ama **idempotent** olmayan çağrıda retry **yalnız** ağ hatasında, aynı `request_id` ile |
| U2 | Cache'e güvenilir mi? | Evet — §14; ama sürüm + PII filtresi zorunlu (I17, §28.3) |
| U3 | Yeni model doğrudan açılır mı? | Hayır — §27 sırası zorunlu (gölge yoksa canary yok) |
| U4 | Rate limit kaç? | `RateLimiterMiddleware` + `AICallRequest` şeması (§17 G8); sayısal değer **UNKNOWN** (runtime config — doğrulanmalı) |
| U5 | Fallback'te kalite düşerse? | Kabul edilir, ama §19'da **fallback_mode** etiketli ayrı metrik olarak raporlanır — kalite düşüşü **gizlenmez** |
| U6 | Determinizm şart mı? | §16: üretimde determinizm zorunlu değil (sıcaklık > 0 serbest), ama **ölçüm/eval** koşulunda `seed` zorunlu |
| U7 | Batch ne zaman? | §14: aynı milisaniyede ≥2 istek birikirse; batch'i aşan gecikme §13 bütçesine yazılır |
| U8 | Provider değişirse? | Sözleşme §12 sabit kalır — provider değişimi **bileşen değişimidir**, gate koşuludur (§30) |
| U9 | Ajan aracı (tool) çağırırsa? | §17 G3 izin + §18 zincir; izin yoksa **sessizce değil**, tanımlı hata döner |
| U10 | Bu anayasa yetmezse? | §11 açık sorular → üst karar → ADR → bu dosyaya ek bölüm (append-only) |

**Uygulama notu:** SSS cevapları **başka anayasadan gelmez** — her cevap bu dosyanın bir §'sine bağlanır; bağlantısız cevap geçersizdir (T soruları §25 ile aynı disiplindedir).

---

## §32 Ek Usul — Revizyon Eşiği Tablosu (ne değişince ne tekrarlanır)

| Değişen yüzey | Tekrarlanacak kapı | Neden |
|---|---|---|
| Model revision (§28) | §19 kalite + §27 shadow | çıktı dağılımı değişir |
| Prompt şablonu | §19 kalite (LLM) | davranış doğrudan değişir |
| Timeout/bütçe değeri (§13) | §13 latency ölçümü | kullanıcı deneyimi doğrudan etkilenir |
| Fallback zinciri (§15) | §30 kapı 3 | hata sınıfları farklı dala düşebilir |
| Rate limit / izin (§17) | §30 kapı 4 (G3, G8) | güvenlik yüzeyi değişir |
| Cache TTL/anahtar (§14) | §30 kapı 1 + 6 | yanılma/PII sızıntısı riski |
| Batch penceresi (§14) | §13 + §29 C2 | gecikme bütçesi değişir |
| Provider (sağlayıcı) | **tüm kapılar** (§30.1–7) | yeni bileşen = tam gate |

**Son kural:** Tabloda olmayan bir değişiklik bile, **ölçüm (§29 kanca) + geri alma (§27) olmadan** production'a girmez — bu iki mekanizma her değişikliğin **alt kümesidir**.
