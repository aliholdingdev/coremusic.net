---
node_id: K006-yapay-zeka/recommendation/recommendation-anayasa
node_type: module
status: draft
parent_id: K006-yapay-zeka/recommendation
children: []
depends_on: []
evidence: []
validation: []
open_questions: []
group: mimari
---

# recommendation — Anayasa (Kademe-2 İlkeler & Sınırlar)

> Bu dosya `recommendation` alt alanının **anayasasıdır**: bozulmaz ilke, sınır ve
> sorumluluk kümesi. Kod behavior'ı değil, **karar veren kuralları** taşır.
> Operasyonel akış → `recommendation-surucu.md` · gerçek kod → `recommendation-ornekler.md`.

## §1 ELI15 — Bu alan ne, ne DEĞİL?

**Şarkı öneren yapay zeka parçaları.** CoreMusic'te kullanıcıya "ne çalacağını"
puanlayıp sıralayan taraf. Basitçe: aday üretir, puanlar, çeşitlilik uygular,
sıralar ve tek bir öneri listesi döner.

| # | Bu alan (İÇİNDE) | Bu alan (DIŞINDA) | Komşu düğüm |
|---|---|---|---|
| 1 | Aday üretimi (collaborative + content-based hybrid) | Etkileşim/katalog verisinin kalıcı saklanması | K007-data |
| 2 | Skorlama ve hybrid ağırlıklandırma | "beğeni/öneri" iş kuralının sahibi olması | K008-domain |
| 3 | Çeşitlilik (diversity) filtresi ve MIN_SCORE eşiği | Öneri kartının UI'da çizimi | K011-application |
| 4 | Öneri modelinin K006 ürün tarafında çalıştırılması | Ajan çalışma zamanı / prompt yönetimi | K015-AI (**kapsam dışı**) |
| 5 | Öneri kalitesinin ölçülmesi (evaluation ile) | Genel uygulama testi | K18-testing |

**Sınır cümlesi (v2 §2.6 · registry §6):** Bu dal yalnız **K006 = ML ürün tarafını**
kapsar. K015-AI ajan altyapısına öneri yeteneği **taşınamaz**; ajanlar K006'yı **çağırır**.

## §2 Bozulmaz İlkeler (invariants)

> Bunlar ihlal edilemez; ihlal = sistem durur, `log.md` ERROR, MO müdahale (registry §18/1).

| # | İlke | Gerekçe | Kaynak |
|---|---|---|---|
| I1 | Öneri tek ML çağrı noktasından döner | Dağınık skorlama tutarsızlık yaratır | `AIEngine.php` §6 adım |
| I2 | Hybrid skor = `0.6·collaborative + 0.4·content-based` (şu an hardcoded) | Deterministik tekrar | `AIEngine.php` satır 65 |
| I3 | `MIN_SCORE = 0.7` altındaki adaylar elenir | Düşük kaliteli öneri sızmasın | `AIEngine.php` satır 30–32 |
| I4 | `DIVERSITY_TARGET = 0.30` asgari çeşitlilik uygulanır | Tek-taraflı baloncuk önleme | `AIEngine.php` satır 30–32 |
| I5 | `MAX_RECOMMENDATIONS = 20` listeyi aşmaz | Gürültü ve gecikme bütçesi | `AIEngine.php` satır 30–32 |
| I6 | Collaborative skor boşken (`[]`) içerik tabanlı yola düşülür | Fail-safe, boş önerme | `AIEngine.php` satır 209–218 |
| I7 | Öneri sonucu asla `NaN`/`null` karışık dönmez | UI crash önleme | tasarım iddiası — ⚠️ VERIFICATION REQUIRED |

> **Dürüstlük notu:** I2 ve I3–I5 değerleri repo'da **hardcoded**'tır — kalıcı mı,
> öğrenilen mi **UNKNOWN** (`recommendation-kapsam.md` §3 A3). Anayasa bu değerleri
> *değiştirmez*, yalnızca "şu an böyle" olarak kaydeder.

## §3 Öğrenme Türü Anayasası (hangi ML, neden)

| Öğrenme türü | Bu alandaki karşılığı | Durum | Kaynak |
|---|---|---|---|
| Denetimli (supervised) | Content-based: etiket→tür/tarz eşleme | Kısmen IMPLEMENTED | `AIEngine.php` §content |
| Denetimsiz (unsupervised) | Kullanıcı-öğe matrisi → embedding (ALS/k-means) | ⏳ PLANNED (`[]`) | `AIEngine.php` satır 209–218 |
| Pekiştirmeli (RL) | Feedback döngüsüyle ağırlık güncelleme | ⏳ PLANNED (`AIWorkflow` feedback) | `AIWorkflow.php` satır 49–91 |
| Hybrid | collaborative + content birleşimi | IMPLEMENTED (boş collab ile) | `AIEngine.php` satır 65 |

**İlke I8:** Collaborative filtering **henüz uygulanmamıştır** (`[]` döner). Bunu
"var" gibi göstermek **yasaktır** — anayasa bunu `⚠️ PLANNED` olarak kilitler
(`recommendation-kapsam.md` §3 A2/R2).

## §4 Model Ailesi Sınırı

| Aile | Öneri için uygunluk | K006 kararı |
|---|---|---|
| Linear / Matrix Factorization (ALS, SVD) | Kullanıcı-öğe işbirliği için klasik | Aday (PLANNED) |
| Tree (XGBoost/LightGBM) | Özellik zengin skorlamada güçlü | Aday (UNKNOWN — karar yok) |
| Deep / Two-tower / Neural CF | Ölçek + özellik birleştirme | Aday (UNKNOWN) |
| Transformer (BERT4Rec vb.) | Sıra-tabanlı oturum | Aday (UNKNOWN) |

**İlke I9:** K006'da **henüz üretim modeli/runtime seçimi YOK** (`index.md` open_questions).
Model ailesi seçimi `⚠️ VERIFICATION REQUIRED` / `UNKNOWN` olarak taşınır; anayasa
seçim **yapmaz**, yalnız adayları listeler.

## §5 Önyargı & Denklik (Bias & Fairness) Anayasası

| Risk | Öneriye etkisi | Azaltma ilkesi | Durum |
|---|---|---|---|
| Popülerlik yanlılığı (popularity bias) | Az dinlenenler hiç önerilmez | Diversity filtresi (I4) kısmen | Kısmen |
| Soğuk başlangıç (cold-start) | Yeni kullanıcı/şarkı için boş skor | Content-based fallback (I6) | Kısmen |
| Baloncuk (filter bubble) | Kullanıcı tek tarza kilitlenir | DIVERSITY_TARGET (I4) | Kısmen |
| Demografik denklik | Belirli gruba sistemli yanlış önerme | Ölçüm (evaluation) gerekli | ⚠️ VERIFICATION REQUIRED |

**İlke I10:** Fairness metriği (ör. demographic parity, calibration) **henüz
tanımlanmamıştır**. Denklik iddiası yazılmaz; `⚠️ VERIFICATION REQUIRED` ile
`evaluation/` altına görev olarak devredilir.

## §6 Etik & Regülasyon Sınırı

| Düzenleme | İlgili madde | Bu alana etkisi | Durum |
|---|---|---|---|
| EU AI Act | `artificialintelligenceact.eu/article/9/` (risk mgmt) | Öneri "sınırlı risk" olabilir; şeffaflık/gözetim gerekir | ⚠️ VERIFICATION REQUIRED (sınıflandırma net değil) |
| NIST AI RMF | `nist.gov/itl/ai-risk-management-framework` | Govern/Map/Measure/Manage döngüsü | Referans alındı |
| ISO/IEC 42001 (AI MS) | Standart metni | Yönetim sistemi çerçevesi | ⚠️ VERIFICATION REQUIRED (iso.org otomatik istek 403) |

**İlke I11:** Öneri motoru **açıklanabilir (explainable)** sonuç üretmeli
("neden bu şarkı?"). Ancak açıklanabilirlik alanı **henüz tanımsız** →
`⚠️ VERIFICATION REQUIRED`.

## §7 Gizlilik & Veri Anayasası

| # | İlke | Not |
|---|---|---|
| P1 | Kullanıcı etkileşimi K007-data'nın sorumluluğundadır; K006 yalnızca **okur** | bagimliliklar §2/1 |
| P2 | Öneri verisi nerede saklanır (K007 mi, önbellek mi) → **⚠️ VERIFY REQUIRED** | `recommendation-kapsam.md` §3 A4 |
| P3 | Kişisel veri K006'da kalıcı tutulmaz; tercih vektörü referansla taşınır | tasarım iddiası — ⚠️ VERIFICATION REQUIRED |
| P4 | KVKK/GDPR uyumu K008-domain ve security tarafında; K006 yalnız API sözleşmesine uyar | sınır |

## §8 K015-AI Sınırı (KAPSAM DIŞI — bağlayıcı)

| Konu | K006 (ML ürün tarafı) | K015-AI (ajan altyapısı) |
|---|---|---|
| Öneri algoritması & skorlama | ✅ sahip | ❌ yalnız **çağırır** |
| Model eğitimi/dagitimi operational MLOps | ❌ sınır dışı (ürün tarafı minimal) | ✅ sahip (model yaşam döngüsü) |
| Ajan prompting / tool-calling | ❌ | ✅ (`PromptEngine`, `ToolCalling` ajan tarafı) |
| Öneri kalitesi metriği | ✅ (evaluation ile) | ❌ |

**Kural:** K015-AI'ın MLOps/model yaşam döngüsü uygulaması bu dalın **kapsamı
DIŞINDADIR**. Ajanlar K006'nın öneri API'sini çağırır; öneriyi **üretmez**.

## §9 Terim Sözlüğü (TR↔EN)

| TR | EN | Kısa |
|---|---|---|
| Öneri sistemi | Recommendation system | Kullanıcı-öğe eşleştirme |
| İşbirlikli filtreleme | Collaborative filtering | Ortak davranıştan öneri |
| İçerik tabanlı | Content-based | Öznitelikten öneri |
| Hibrit | Hybrid | İki yöntemin birleşimi |
| Çeşitlilik | Diversity | Tek-taraflılığı kırma |
| Soğuk başlangıç | Cold-start | Yeni öğe/kullanıcı problemi |
| Popülerlik yanlılığı | Popularity bias | Meşhuru öne çekme |
| Skorlama | Scoring / ranking | Adayları puanlayıp sıralama |
| Aday üretimi | Candidate generation | Geniş havuzdan eleme |

## §10 Kapsam Dışı (bu anayasanın dışındadır)

| # | Kapsam dışı | Sahibi |
|---|---|---|
| 1 | Model yaşam döngüsü / operational MLOps | K015-AI |
| 2 | Etkileşim verisinin kalıcı saklanması | K007-data |
| 3 | "beğeni" iş kuralının tanımı | K008-domain |
| 4 | Öneri kartı UI çizimi | K011-application |
| 5 | Ajan prompt/tool yönetimi | K015-AI |
| 6 | Genel uygulama testi | K18-testing |

## §11 Açık Sorular (anayasa kilidi)

| # | Soru | İşaret |
|---|---|---|
| Q1 | Öneri domain'inin sahibi kim (K006 · K008 · K020-recomenataon)? | ⚠️ SINIR AÇIK |
| Q2 | Hybrid ağırlık 0.6/0.4 kalıcı mı, öğrenilen mi? | ⚠️ UNKNOWN |
| Q3 | Üretim modeli/runtime seçimi? | ⚠️ UNKNOWN |
| Q4 | Fairness metriği tanımlanacak mı? | ⚠️ VERIFICATION REQUIRED |

## §12 Öğrenme Türü Ayrıntısı (anayasa derinliği)

### §12.1 Denetimli (supervised) — content-based taraf

| Öğrenme problemi | Hedef | Özellikler (feature) | Etiket kaynağı | Durum |
|---|---|---|---|---|
| Tür sınıflandırma | Şarkının türü (pop/rock/...) | MFCC, spectral centroid, tempo | Katalog etiketi (K007) | Kısmen |
| Tarz regresyonu | Yüzbenze skoru | Content embedding vektörü | Kullanıcı beğeni | ⏳ PLANNED |
| Eşleşme skoru | (kullanıcı, şarkı) uyumu | Birleşik vektör | Etkileşim (K007) | ⏳ PLANNED |

**İlke I12:** Denetimli taraf yalnızca **içerik özniteliklerini** kullanır; kullanıcı
gizli davranışını collaborative tarafa bırakır (I1 ile ayrım).

### §12.2 Denetimsiz (unsupervised) — collaborative taraf

| Yöntem | Girdi | Çıktı | K006 durumu |
|---|---|---|---|
| Matrix Factorization (ALS) | Kullanıcı-öğe etkileşim matrisi | Gizli faktör (latent factor) | ⏳ PLANNED — `[]` (`AIEngine.php` 209–218) |
| K-means | Şarkı embedding | Küme etiketi | ⏳ UNKNOWN |
| Nearest Neighbor (item-kNN) | Öğe-öğe benzerlik | Komşu seti | ⏳ UNKNOWN |

**İlke I8 yinelemesi:** ALS "kullanılacak" yorumu kodda mevcut ancak **implemente
değildir**. `recommendation-anayasa` bunu `⚠️ PLANNED` kilitler; "collaborative
filtering çalışıyor" iddiası **yasaktır**.

### §12.3 Pekiştirmeli (RL) — feedback döngüsü

| Bileşen | Karşılığı | Durum |
|---|---|---|
| Ajan (agent) | Öneri motoru | Tasarım |
| Eylem (action) | Bir şarkı önermek | IMPLEMENTED (skor) |
| Ödül (reward) | Dinleme/beğeni sinyali | ⏳ PLANNED (`AIWorkflow` feedback, 49–91) |
| Politika | Skor ağırlıkları / ranking | ⚠️ UNKNOWN (hardcoded) |

**İlke I13:** RL tabanlı ağırlık güncellemesi **henüz yoktur**; feedback toplanır
ancak politikaya **geri beslenmez**. Ödül→model döngüsü `⚠️ VERIFICATION REQUIRED`.

## §13 Model Ailesi Ayrıntısı (karar matrisi)

| Aile | Artı | Eksi | Veri ihtiyacı | Olgunluk (K006) |
|---|---|---|---|---|
| ALS / SVD | Yorumlanabilir, hızlı | Soğuk başlangıç zayıf | Yoğun etkileşim | Düşük (planlı) |
| kNN (item) | Basit, çevrimiçi | Büyük matrisde yavaş | Orta etkileşim | Düşük |
| XGBoost / LightGBM | Özellik zengin, güçlü | Özellik mühendisliği yükü | Etiketli veri | Orta (aday) |
| Two-tower / NCF | Ölçek + öğrenilmiş embedding | Eğitim maliyeti | Bol veri | Düşük |
| Transformer (BERT4Rec) | Oturum sırası | En pahalı, en yavaş | Uzun oturum | Düşük |

**İlke I14:** Model ailesi seçimi için **henüz ADR yok**. Bu matris *aday* listesidir;
seçim `UNKNOWN` olarak taşınır (`index.md` open_questions). Seçim K006 ürün minimal
kalır; operational MLOps K015-AI'ın (kapsam dışı).

## §14 Fairness Ayrıntısı (ölçüm kapıları)

| Metrik | Tanım | Hedef | K006 durumu |
|---|---|---|---|
| Demographic parity | Öneri dağılımı demografiyle bağımsız | Fark ≈ 0 | ⚠️ tanımsız |
| Calibration | Verilen skor ≈ gerçek olasılık | Yakınsama | ⚠️ tanımsız |
| Exposure fairness | Az popülerlerin maruz kalışı | Adil maruziyet | Kısmen (I4 diversity) |
| Serendipity | Beklenmedik ama ilgili keşif | Ölçülür | ⚠️ tanımsız |

**İlke I15:** Fairness metrikleri **tanımlanana kadar** denklik iddiası yazılmaz.
`evaluation/` altına "fairness metriği tanımla" görevi olarak devredilir (Q4).

## §15 Regülasyon Yükümlülükleri (haritalama)

| Yükümlülük | EU AI Act (m.9) | NIST AI RMF | K006 karşılığı |
|---|---|---|---|
| Risk yönetimi döngüsü | Zorunlu | Govern/Map/Measure/Manage | anayasa + surucu |
| Veri yönetimi | Kalite/uygunluk | Map veri | K007'ye devredilir |
| Şeffaflık/açıklanabilirlik | Gerekli (sınırlı risk) | Measure | I11 (⚠️ tanımsız) |
| İnsan gözetimi (human oversight) | Gerekli | Manage | K011/UI tarafı |
| Sağlamlık & güvenlik | Zorunlu | Manage | I6 fail-safe |

**İlke I16:** EU AI Act'te bu motorun **sınıflandırması (risk kategorisi) netleşmemiştir**
→ `⚠️ VERIFICATION REQUIRED`. Yüksek risk olasılığı düşük görünse de resmi
sınıflandırma **yoktur**; iddia yazılmaz.

## §16 Kalite Kapıları (anayasa gate — surucu öncesi)

| # | Kapı | Koşul | İhlal |
|---|---|---|---|
| G1 | Tek çağrı noktası | Tüm skorlar tek `getRecommendations` üzerinden | registry §18/1 |
| G2 | MIN_SCORE | Skor ≥ 0.7 | Aday elenir |
| G3 | Diversity | Çeşitlilik ≥ 0.30 | Denge uygulanır |
| G4 | Liste boyutu | ≤ 20 | Kırpılır |
| G5 | Boş-safe | collab `[]` ise content yolu | Boş önerme yok |
| G6 | Temiz çıktı | `NaN`/`null` karışık yok | ⚠️ I7 VERIFY |

## §17 Karar Önceliği (çatışma çözümü)

| Durum | Karar | Kaynak |
|---|---|---|
| K006 ↔ K015-AI çatışması | K016 sınır dışı; K006 ürün kalır | §8 |
| K006 ↔ K008 sahiplik | **AÇIK** — iddia yok | Q1 · kapsam §2 |
| Hardcoded vs öğrenilen | Şu an hardcoded; "öğrenildi" denmez | I2 · Q2 |
| Anayasa ↔ kod | Anayasa kazanır (bozulmaz ilke) | §2 |

## §18 Sürüm & Değişiklik Kilidi

| Alan | Değer |
|---|---|
| status | draft (Q1–Q4 açık) |
| Bozulmaz ilke | §2 I1–I11 |
| Kilitli UNKNOWN | Q2 (ağırlık), Q3 (model), Q4 (fairness) |
| K016 sınırı | §8 — bağlayıcı |

## §19 Risk Kaydı (anayasa)

| # | Risk | Etki | Olasılık | Azaltma |
|---|---|---|---|---|
| R1 | Collab boş kalırsa zayıf öneri | Yüksek | Yüksek | I6 content fallback |
| R2 | Hardcoded ağırlık yanlış | Orta | Orta | Q2 ADR beklemede |
| R3 | Popülerlik baloncuk | Orta | Yüksek | I4 diversity |
| R4 | Fairness iddiası kanıtsız | Yüksek | Orta | I15/Q4 kilit |
| R5 | K016 sınır ihlali (ajan öneriyi üretir) | Yüksek | Düşük | §8 bağlayıcı |
| R6 | Model seçimi olmadan ölçek | Orta | Orta | Q3 UNKNOWN |

## §20 ADR & Vault Referansları

| Referans | Ne verir |
|---|---|
| ADR-097 K2/K4 | Kademe-3 (bounded-context) yapısı — kilitlenmedi |
| ADR-097 §5.2 | Dosya rol & bütçe şeması |
| ADR-097 §5.5 | İki-ekseni gruplama (group alanı) |
| v2 §2.6 (satır 261) | Çocuk adı kaynağı — birebir |
| `../ai-master.md` §7/2 | Öneri sahipliği üç düğüm — açık |
| `../yapay-zeka-bagimliliklar.md` §5/2 | K006↔K020-recomenataon sınırı |

## §21 Çapraz Bütünlük (bu dosyanın tutarlılık kontrolü)

| Kontrol | Beklenen | Durum |
|---|---|---|
| K016 sınırı her ilgili dosyada mı? | evet (§8) | ✅ |
| Öneri sahipliği iddia edildi mi? | hayır (Q1 açık) | ✅ |
| Hardcoded değer "öğrenildi" mi dendi? | hayır (I2/Q2) | ✅ |
| collab `[]` "var" mı dendi? | hayır (I8) | ✅ |
| Fairness iddiası kanıtsız mı yazıldı? | hayır (I15 kilit) | ✅ |

## §22 Uzantı Sözlüğü (alan-özel TR↔EN)

| TR | EN | Not |
|---|---|---|
| Aday üretimi | Candidate generation | Geniş havuzdan eleme |
| Gizli faktör | Latent factor | MF çıktısı |
| Öğe-kNN | Item-based kNN | Komşu benzerliği |
| İki-kule | Two-tower | Retrieval modeli |
| Maruziyet | Exposure | Öneri görünürlüğü |
| Serendipity | Serendipity | Beklenmedik keşif |
| Kalibrasyon | Calibration | Skor≈olasılık |
| Politika | Policy | RL seçim kuralı |

## §23 Sürüm Kilidi Özeti

| Alan | Değer | Değişim |
|---|---|---|
| Bozulmaz ilke | I1–I16 | Yalnız ADR ile |
| Kilitli UNKNOWN | Q2 · Q3 · Q4 | Kanıt gelince |
| K016 sınırı | §8 | Bağlayıcı |
| Kod reference | `AIEngine.php` satır 30–32/65/209–218 | salt-okunur |
| status | draft | Q1–Q4 kapanınca verified |

## §24 İlişkili dosyalar

- `recommendation-surucu.md` (operasyonel akış)
- `recommendation-ornekler.md` (gerçek kod)
- `recommendation-kapsam.md` · `recommendation-dogrulama.md`
- `../ai-master.md` · `../yapay-zeka-kapsam.md` · `../index.md`

---

## §25 Ölçüm Referansları — değerlendirme metrikleri resmi kaynağı (ek 2026-10-13)

> **Kaynak:** https://scikit-learn.org/stable/modules/model_evaluation.html (HEAD 200 · 2026-10-13) ·
> bağ: §14 (fairness ölçümleri) · §16 (kapılar) · `../yapay-zeka-test-stratejisi.md` §3.

| # | Metrik | Anayasadaki karşılığı | Tam tanım nerede | K006 durumu |
|---|---|---|---|---|
| M1 | Precision@k | §16 G2/G3 kalite kapısı | resmi ölçüme sayfası (URL üstte) | ❌ hesaplayan kod **YOK** |
| M2 | Recall@k | öneri kapsaması (§3 içerik kolu) | aynı | ❌ yok |
| M3 | nDCG@k | sıralama kalitesi (§16 G1 sıralı çıktı) | aynı | ❌ yok |
| M4 | MAP | aday listesi geneli | aynı | ❌ yok |
| M5 | MRR | ilk doğru önerinin sırası | aynı | ❌ yok |
| M6 | Demographic parity (§14) | denklik kapısı | resmi sayfa **kapsam dışı** → ayrı kaynak gerekli | ⚠️ tanımsız (Q4) |
| M7 | Calibration (§14) | skor≈olasılık | aynı | ⚠️ tanımsız |
| M8 | Serendipity (§14) | beklenmedik keşif | **standart sayfa yok** → `⚠️ VERIFICATION REQUIRED` | ⚠️ tanımsız |

**İlke I17 (yeni):** metrik **adı** yazmak ölçüm değildir. Bu anayasada bir metrik ancak
(1) resmi tanımı, (2) hesaplayan kod, (3) eşik değeri — üçü birden varsa **kapı** sayılır;
ikisi eksikse `⚠️ tanımsız` kalır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §26 Öneri Veri Kaynakları Anayasası (ek 2026-10-13)

> **Kural:** eğitim/aday verisi kaynağını bu anayasa **seçmez** — yalnızca hangi kaynağın
> kişisel veri taşıdığını ve hangi çocuğu bağladığını haritalar.
> Doğrulanan resmi kapılar (HEAD 200 · 2026-10-13): https://musicbrainz.org/doc/MusicBrainz_API ·
> https://developer.spotify.com/documentation/web-api · https://freesound.org/docs/api/

| # | Kaynak türü | Kişisel veri içerir mi | Hangi çocuk | Anayasa bağlantısı | Erişim koşulu |
|---|---|---|---|---|---|
| D1 | MusicBrainz (açık müzik veritabanı API'si) | Hayır — müzik meta verisi | K006.6-music-analysis | §3 içerik kolu | ⚠️ kullanım koşulları **okunmadı** |
| D2 | Spotify Web API (katalog + kullanıcı etkileşimi) | **Evet** (kullanıcı tarafı uçlar) | K006.7 · K006.8 | §7 gizlilik + §15 regülasyon | ⚠️ VERIFICATION REQUIRED (onay/izin modeli okunmadı) |
| D3 | Freesound (ses parçası API'si) | Hayır — kaynak ses | K006.1 · K006.6 | §5 öznitelik girdisi | ⚠️ API anahtarı modeli okunmadı |
| D4 | K007-katalog (repo içi) | Kısmen (beğeni = kullanıcı) | K006.8 | §7 · Q1 | ⚠️ tablo adı **UNKNOWN** |
| D5 | `MemorySystem::setSession` oturum anahtarı | **Evet** | K006.7 | §7 (R-03) | ⚠️ alt depo **UNKNOWN** |
| D6 | Dış model eğitimi (hazır model) | Veri seti belirsiz | K006.5-model-management | §13 model ailesi | ⚠️ VERIFICATION REQUIRED |

**İlke I18 (yeni):** Kişisel veri **taşıyan** bir kaynak, bu anayasada `§7` (gizlilik) ve
`§15` (regülasyon) kapılarını **otomatik** tetikler; kaynak satırı `⚠️` işaretliyken
"kullanıldı" iddiası yazılmaz.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §27 Girdi Ses Biçimi Sınırı — içerik kolunun beslendiği formatlar (ek 2026-10-13)

> **Bağ:** §3 (content-based), §5 (öznitelik), `../yapay-zeka-kod-ornekleri.md` §4
> (`extractFeatures` placeholder). Doğrulanmış resmi kapılar (HEAD 200 · 2026-10-13):
> https://xiph.org/flac/ · https://opus-codec.org/ · https://ffmpeg.org/ffmpeg-filters.html

| # | Format/katman | Resmi kapı | Öznitelik çıkarımına etkisi | K006 durumu |
|---|---|---|---|---|
| F1 | FLAC (kayıpsız kodlama) | xiph.org/flac | kayıpsız girdi → BPM/ton hataları azalır (tasarım beklentisi) | ⚠️ ölçüm **YOK** |
| F2 | Opus (kayıplı/gerçek zamanlı) | opus-codec.org | agresif kodlama spektral bilgiyi kısabilir | ⚠️ ölçüm **YOK** |
| F3 | FFmpeg filtre zinciri (features) | ffmpeg.org/ffmpeg-filters | `extractMetadata` yorumunun işaret ettiği dış araç (`AIEngine.php` L88) | ⚠️ entegrasyon **YOK** |
| F4 | Kaynak dosya bulunamadığı durum | — | `analyzeAudio` fırlatma yolları (`AIEngine.php` L85) | ✅ kod kanıtı §5 |
| F5 | Desteklenmeyen format | UNKNOWN | — | ⚠️ beyaz liste **tanımlı değil** |

**İlke I19 (yeni):** Bu anayasa **codec seçmez**. Yalnızca şunu kilitler: içerik kolu
(BPM/ton/enerji) girdisi ile ilgili **ölçüm yokken**, bir format için "doğru çıkarım
yapılıyor" iddiası yazılmaz.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §28 Tekrarlanabilirlik & Veri Sürümleme (ek 2026-10-13)

> **Bağ:** §12.2 (ALS girdisi = etkileşim matrisi), §13 (model ailesi), §16 G5 (boş-safe).
> Doğrulanmış resmi kapılar (HEAD 200 · 2026-10-13): https://dvc.org/doc ·
> https://mlcommons.org/ · https://docs.pydantic.dev/

| # | Gereklilik | Neden K006 için | Aday kapı | Durum |
|---|---|---|---|---|
| R1 | Eğitim verisi sürümü sabitlenmeli | aynı model farklı veriyle doğrulanamaz | DVC | ⚠️ kurulmadı |
| R2 | Girdi/çıktı şeması doğrulamalı | `getRecommendations` dönüşü docblock'ta (§1) | Pydantic (Python tarafı) | ⚠️ PHP tarafında eşdeğer **UNKNOWN** |
| R3 | Ölçüm tekrarlanabilir olmalı | §25 M1–M5 metrikleri aynı veriyle yeniden üretilmeli | MLCommons referans yöntemi | ⚠️ VERIFICATION REQUIRED |
| R4 | Random seed sabitlenmeli | ALS/k-means rastgeleliği (§12.2) | **seçim YOK** | ⚠️ UNKNOWN |
| R5 | Etiket/denetim kaydı | model sürümü ↔ karar izi | `model-management` | ⚠️ çocukta 0 satır kod |

**İlke I20 (yeni):** Tekrarlanabilirlik, "aynı sonucu üretmek" değil, "aynı veri +
aynı sürüm + aynı tohum ile yeniden üretilebilmek" demektir; bu üçünün de kaydı olmadan
hiçbir öneri metriği **kanıt sayılmaz**.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §29 Anayasa Kaynak Turları (2026-10-13 HEAD doğrulaması)

| # | URL | Bölüm | HTTP | Not |
|---|---|---|---|---|
| 1 | https://scikit-learn.org/stable/modules/model_evaluation.html | §25 | 200 | ölçüm referansı |
| 2 | https://musicbrainz.org/doc/MusicBrainz_API | §26 | 200 | açık müzik API'si |
| 3 | https://developer.spotify.com/documentation/web-api | §26 | 200 | katalog + kullanıcı API'si |
| 4 | https://freesound.org/docs/api/ | §26 | 200 | ses kaynağı API'si |
| 5 | https://xiph.org/flac/ | §27 | 200 | kayıpsız format |
| 6 | https://opus-codec.org/ | §27 | 200 | düşük gecikmeli format |
| 7 | https://ffmpeg.org/ffmpeg-filters.html | §27 | 200 | öznitelik/filtre zinciri |
| 8 | https://dvc.org/doc | §28 | 200 | veri sürümleme |
| 9 | https://mlcommons.org/ | §28 | 200 | ölçüm referans projeleri |
| 10 | https://docs.pydantic.dev/ | §28 | 200 | şema doğrulama |

**Başarısız deneme (silinmedi):** `tech.ebu.ch/publications/r128` → HTTP 403 ·
`www.recsys.org` → TLS hatası · `www.aes.org` → HTTP 403. Üçü de **bu anayasada
kullanılmadı**; iddia uydurulmadı, başarısızlık kayda geçirildi.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §30 Ek Kaynaklar (yalnız §25–§29)

| # | Kaynak | Ne verdi | Tarih |
|---|---|---|---|
| 1 | https://scikit-learn.org/stable/modules/model_evaluation.html | metrik referansı (§25) | 2026-10-13 HEAD 200 |
| 2 | https://musicbrainz.org/doc/MusicBrainz_API · https://developer.spotify.com/documentation/web-api · https://freesound.org/docs/api/ | veri kaynağı kapıları (§26) | 2026-10-13 HEAD 200 |
| 3 | https://xiph.org/flac/ · https://opus-codec.org/ · https://ffmpeg.org/ffmpeg-filters.html | format katmanı (§27) | 2026-10-13 HEAD 200 |
| 4 | https://dvc.org/doc · https://mlcommons.org/ · https://docs.pydantic.dev/ | tekrarlanabilirlik (§28) | 2026-10-13 HEAD 200 |
| 5 | `../yapay-zeka-kod-ornekleri.md` §4 · §5 | placeholder + hata yolu kod kanıtı | 2026-10-12 disk |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §31 Düzenleyici Bağlantı Noktaları - AI Act & GDPR resmi kapıları (ek 2026-10-13)

§15'teki regülasyon haritasının dayandığı **kapı adresleri** HEAD ile doğrulandı. Bu bölüm
§15'in yükümlülük listesini **değiştirmez**; yalnızca kapıların erişilebilirliğini kanıtlar.

| # | Kapı (URL) | Haritadaki karşılığı | Erişim |
|---|---|---|---|
| 1 | https://digital-strategy.ec.europa.eu/en/policies/regulatory-framework-ai | §15/R1 AI Act politika sayfası (AB Komisyonu) | HEAD 200 · 2026-10-13 |
| 2 | https://gdpr-info.eu/ | §15/R2 GDPR md metni kapısı (eur-lex erişilemez §14 kaydı) | HEAD 200 · 2026-10-13 |
| 3 | https://www.nist.gov/itl/ai-risk-management-framework | §19 risk kaydı için gönüllü çerçeve kapısı | HEAD 200 · 2026-10-13 |
| 4 | https://www.rfc-editor.org/rfc/rfc4122 | §28 veri kimliği (UUID) referans kapısı | HEAD 200 · 2026-10-13 |
| 5 | https://www.rfc-editor.org/rfc/rfc7159 | §26/§28 JSON değişim biçimi referans kapısı | HEAD 200 · 2026-10-13 |
| 6 | https://www.json.org/ | §28 serileştirme sözdizimi kapısı | HEAD 200 · 2026-10-13 |
| 7 | https://www.unicode.org/ | §9 terim sözlüğü karakter zemini | HEAD 200 · 2026-10-13 |

**Kapsam sınırı (bağlayıcı):** yalnızca `HEAD 200` doğrulanmıştır — sayfaların **metni,
yürürlük tarihi ve madde numaraları okunmamıştır**. §15'teki madde bazlı eşlemeler
`⚠️ VERIFICATION REQUIRED` statüsündedir; kapı 200 döndü diye yükümlülük içeriği
doğrulanmış sayılmaz. Yürürlük tarihi ve bağlayıcılık için bir sonraki turda sayfa
içeriği okunmalı, o tarihe kadar bu bölüm **yalnız erişim kanıtı** taşır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

## §32 Metrik Hesap Zemini - Python ölçüm kütüphaneleri kapıları (ek 2026-10-13)

§25'teki değerlendirme metriklerinin **hesaplandığı** ekosistem kapıları doğrulandı;
scikit-learn (§25 kayıtlı) dışında kalan iki kapı bu bölümde:

| # | Kapı | §25'teki karşılığı | Erişim |
|---|---|---|---|
| 1 | https://www.statsmodels.org/ | istatistiksel test/zaman serisi kapısı — güven aralığı iddiaları bu kapıya dayanabilir | HEAD 200 · 2026-10-13 |
| 2 | https://yellowbrick.readthedocs.io/ | görsel model denetimi kapısı — §16 kalite gate görselleştirme adayı | HEAD 200 · 2026-10-13 |
| 3 | https://docs.python.org/3/library/statistics.html | standart kütüphane istatistik modülü — bağımlılıksız temel hesap | HEAD 200 · 2026-10-13 |

**Sınır:** bu kapılar K006'da **kurulu değildir** ve metrik değerleri **hiçbiri
bu kapılar üzerinden hesaplanmamıştır** — §25'teki metrik tanımları hâlâ tanım düzeyidir,
sayısal eşikler `UNKNOWN` olarak kalır.

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
