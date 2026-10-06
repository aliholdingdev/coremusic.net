---
title: "Vektör İndeks Mimarisi - k108-vector-index"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D03 dilimi (k108-k119)"
updated: 2026-10-06
---

# 108. Vektör İndeks Mimarisi - `k108-vector-index`

> Dilim: D03 (k108-k119) · Klasör no: 108 · Kategori: mimari · Sürüm: 4.0.0 · Tarih: 2026-10-06
> Sorumlu persona: `data-engineer` (şema/indeks), `backend-architect` (API sınırı), `performance-engineer` (latans)
> Kapsadığı MD: 3 (2 içerik + index) · Kaynak kanıtları: satır içinde `Kanıt:` + L aralığı ya da `⚠️ VERIFICATION REQUIRED`

## 1. Genel Bakış

Vektör indeks, yüksek boyutlu embedding kayıtlarının (ses, söz, kapak görseli, profil)
anlamsal sorgu için saklandığı ve k-NN (en yakın komşu) sorgularının karşılandığı
altyapıdır. Bu dosya **mimari tasarım**tır: bileşenler, sınırlar, veri modeli ve
iş kuralları tanımlanır; çalışır bir kurulum **değildir**.

**Katman bağımlılığı:** K5 (veri yönetimi) kalıcılık, K8/K9 (servis/API) erişim
kapısı, K4 yapay zeka (embedding üretimi, bkz. `k107-embedding-models`), K12
(izleme) metrik çıkışı. İndeks kendi başına **kaynak veri değildir**; kaynak kayıt
(system-of-record) K5'te kalır, indeks **türetilmiş** olandır.

Tasarımın temel gerilimi: **tutarlılık gecikmesi ↔ yazma maliyeti**. Kayıt
eklendiğinde indeksin hemen görünür olması (okunabilirlik) ile toplu yazmanın
verimliliği arasındaki denge, bu belgede `E-SYNC-*` kurallarıyla sabitlenir.

Kapsanan üç erişim paterni:
1. **Vektör + filtre** (metadata kısıtı altında en yakın k komşu)
2. **Filtre + vektör** (seçiciliği yüksek kısıt önce uygulanır)
3. **ID ile doğrudan okuma** (tekil kayıt, tombstone sonrası 404)

İndeks türü **boyut-ölçekli** çalışır: kayıt sayısı `N`, embedding boyutu `D`,
yarıçap filtresi `R`. Bellek alt toplamı kabaca `N × D × bayt(boyut) × çarpan`
formülüyle hesaplanır; bu formül §7.4'te ayrıntılandırılır.

## 2. Klasör Özeti (K Tablosu)

| Numara | Ad | Amaç | Bağımlılık | Sorumlu persona | Kanıt | MD |
|---|---|---|---|---|---|---|
| 1 | Vektör İndeks Mimarisi | Bileşenler, sınırlar, veri modeli, iş kuralları | K4 embedding, K5 veri, K8 servis | data-engineer, backend-architect | `⚠️ VERIFICATION REQUIRED: repo'da vektör indeks kurulum kodu görülmedi` | [[vector-index-mimari.md]] |
| 2 | Vektör İndeks Operasyonu | Yazma/güncelleme/silme akışı, ölçüm, arıza kurtarma | K12 izleme, K13 CI/CD, K5 kalıcılık | data-engineer, sre-engineer | `⚠️ VERIFICATION REQUIRED: operasyon betiği repo'da görülmedi` | [[vector-index-operasyon.md]] |

## 3. Kapsam ve Kapsam Dışı

### 3.1 Kapsamda Olanlar

| # | Kapsam | Açıklama |
|---|---|---|
| 1 | Vektör depolama şeması | Alan adları, tipler, boş bırakma kuralları |
| 2 | Yakınlık metriği seçimi | Cosine, L2 (euclidean), dot-product ilişkisi |
| 3 | İndeks yapısı seçimi | Flat (brute-force), IVF, HNSW ve seçim kriterleri |
| 4 | Filtreleme sözdizimi | Metadata kısıtlarının vektör aramasıyla birleşimi |
| 5 | Bölümleme (partition/shard) | Sıcak/sıcak-veri ayrımı, kiracı izolasyonu |
| 6 | Tutarlılık modeli | Write→read gözlemlenebilirliği, yeniden inşa pencereleri |
| 7 | Ölçüm ve SLO | recall@k, p95 latans, indeks boyutu, yeniden inşa süresi |
| 8 | Arıza kurtarma | Shard kaybı, indeks bozulması, yeniden doldurma (reindex) |

### 3.2 Kapsam Dışı Olanlar

| # | Dışarıda | Gerekçe / Nereye Bakılır |
|---|---|---|
| 1 | Embedding modeli eğitimi | `k111-training-pipeline` ve `k107-embedding-models` |
| 2 | Model çıktısının doğruluğu | `k115-model-monitoring` kalite metrikleri |
| 3 | Kaynak kaydın SQL şeması | K5 veri yönetimi, `k120-mysql-18-database` |
| 4 | Anlamsal kimlik üretimi | `k109-semantic-id` |
| 5 | RAG bağlama paketleme | `k110-rag-knowledge` |
| 6 | Deneylerde metrik analizi | `k116-ab-testing` |
| 7 | Güvenlik başlığı/CSRF politikası | K6 güvenlik katmanı (ayrı dilim) |

## 4. Mimari Bileşenler

| # | Bileşen | Sorumluluk | Arayüz | Durum |
|---|---|---|---|---|
| 1 | `EmbeddingProducer` | Ham kayıttan vektör üretir | iç API (toplu/tekil) | tasarlandı |
| 2 | `VectorSchema` | Alan, tip, boyut, kısıt tanımı | veri sözleşmesi | tasarlandı |
| 3 | `IndexWriter` | Upsert / soft-delete / yeniden yazım | idempotent yazma | tasarlandı |
| 4 | `FilterCompiler` | Metadata kısıtını indeks sorgusuna çevirir | derleyici | tasarlandı |
| 5 | `QueryPlanner` | Filtre+vektör sırasını seçer (selectivity tahmini) | planleyici | tasarlandı |
| 6 | `Searcher` | k-NN çalıştırır, sonuç kümesini döndürür | sorgu API | tasarlandı |
| 7 | `Reranker` (ops.) | Aday listesini yeniden sıralar | birincil sonuç | tasarlandı |
| 8 | `SegmentManager` | Sıcak/sıcak segment, birleştirme (merge) | zamanlayıcı | tasarlandı |
| 9 | `TombstoneStore` | Silinen id listesi, yeniden inşa işaretçisi | kalıcı kuyruk | tasarlandı |
| 10 | `Reindexer` | Sıfırdan yeniden doldurma, mavi/yeşil geçiş | toplu iş | tasarlandı |
| 11 | `IndexHealth` | Sağlık sinyali, SLO ihracı | izleme çıkışı | tasarlandı |
| 12 | `AccessGate` | Kimlik doğrulama, kiracı/ayraç kapsaması | API sınırı | tasarlandı |

### 4.1 Bileşen Sınırları (ne yapmaz)

| Bileşen | YAPMAZ |
|---|---|
| `IndexWriter` | Embedding hesaplamaz (vektör hazır gelmek zorundadır) |
| `FilterCompiler` | Serbest metin/LLM sorgusu üretmez |
| `Searcher` | Kayıt kaynağını (K5) güncellemez |
| `Reindexer` | Üretime alınan indeksi **silmeden** yeni indekse yazmaz |
| `AccessGate` | İş kuralı doğrulamaz (yalnız kapsama/izin denetimi) |

## 5. Uçtan Uca Akış

### 5.1 Akış Diyagramı (kayıt yazma ve sorgu)

```
[Kaynak kayıt (K5)]
        |
        v
[Olay/çıpan (insert|update|delete)]
        |
        v
[EmbeddingProducer] -- vektör yoksa hesapla (D boyut) --> [Model (k107/k114)]
        |                                                        |
        | vektör + metadata                                     | imzasız çıktı
        v                                                        v
[IndexWriter] -- idempotent upsert (id, ts) ------------> [TombstoneStore]
        |                                                        |
        | parti (batch)                                          | silme işareti
        v                                                        |
[SegmentManager] -- flush --> [HOT segment]                      |
        |                                                       |
        | birleşim eşiği (N, T dakika)                          |
        v                                                       |
[MERGED segment] <-----------------------------------------------+
        |
        v
[Searcher] <--- [QueryPlanner] <--- [FilterCompiler] <--- [Sorgu (API/K9)]
        |                    ^
        | selectivity        | tahmin (histogram)
        v                    |
   [Aday k sonuç] --> [Reranker?] --> [Yanıt + kaynak id listesi]
```

### 5.2 Adımlar

1. Kaynak kayıtta değişiklik olur; olay `<entity, id, op, version, ts>` biçimindedir.
2. `FilterCompiler` kısıtları sözdizimine çevirir ve geçersiz alanı **en erken** reddeder.
3. `QueryPlanner` filtre seçiciliği (selectivity) tahmin eder; `%<t` ise **önce filtre**.
4. `Searcher` k komşuyu metrik üzerinden çeker; `k × (1 + aday çarpanı)` aday üretir.
5. Zorunlu ise `Reranker` adayları yeniden sıralar; değilse sonuç doğrudan döner.
6. Yazma tarafında `EmbeddingProducer` boyut uyuşmazlığını yazar **öncesi** yakalar.
7. `IndexWriter` aynı `(id, version)` için tekrar çağrıyı **atlar** (idempotent).
8. `SegmentManager` sıcak segmenti boşaltır (flush); arama her iki segmenti kapsar.
9. Birleşim eşiği dolunca segmentler tek segmentte toplanır (merge).
10. `TombstoneStore` silme işareti tutar; yeni yazım bu işareti düşürür.
11. `IndexHealth` sonuç sayısını, latansı ve segment sayısını ihrac eder.
12. Bozulma sezgisinde `Reindexer` yeni indekse yazar, sonra trafik anahtarını çevirir.

### 5.3 Sorgu Öncelik Kararları

| Durum | Karar | Gerekçe |
|---|---|---|
| Filtre seçiciliği tahmini yok | Önce vektör, sonra uygula (post-filter) | Histogram yoksa güvenli varsayım |
| Seçicilik yüksek (kayıt < %1) | Önce filtre (pre-filter) | Aday havuzu küçülür, maliyet düşer |
| `k` > 1000 | Segment süzme + sonradan kesme (two-stage) | Bellek bütçesi aşılmasın |
| Aynı anda `Reranker` varsa | Aday çarpanı 3–5× | Reranker için hammadde gerekir |
| ID ile okuma | k-NN yok, doğrudan nokta okuma | Deterministik ve ucuz |

## 6. İş Kuralları

### 6.1 `V-*` — Vektör Kuralları

- **V-001:** Vektör boyutu (D) koleksiyon başına **sabit**tir; değişimi yeni koleksiyon demektir.
- **V-002:** Kayıtta `null` vektör **saklanmaz**; üretilemiyorsa kayıt `pending_embedding` işaretlenir.
- **V-003:** Vektör `float32` varsayılır; `int8/uint8` yolla yalnız depolama formatında olabilir.
- **V-004:** Birim-vektör (unit) zorunlu ise yazım öncesi normalizasyon zorunlu; normalleştirilmemiş kayıt reddedilir.
- **V-005:** NaN/Inf içeren vektör yazımı **her zaman** reddedilir (sessizce düzeltilmez).

### 6.2 `F-*` — Filtre Kuralları

- **F-001:** Yalnızca şemada tanımlı alanlarda filtre yapılabilir; bilinmeyen alan = 400.
- **F-002:** `AND`/`OR`/`NOT` ve aralık (`range`) desteklenir; iç içe derinlik sınırı uygulanır (aşırı iç içe reddedilir).
- **F-003:** Metin `contains` filtresi vektör indeksine **delegasyon edilmez**; tam metin motoruna gider.
- **F-004:** Filtre, boş sonuç üretiyorsa sorgu **erişim hatası değil**, boş küme olarak döner.

### 6.3 `S-*` — Sorgu Kuralları

- **S-001:** `k` pozitif tam sayı; üst sınır sunucu tarafından bağlanır (aşırı `k` kırpılır).
- **S-002:** Aynı `request_id` ile tekrarlanan sorgu önbellekten dönebilir; önbellek TTL kısa tutulur.
- **S-003:** Sonuçlarda her zaman kayıt `id` + `score` + `version` döner (kaynak izlenebilirliği).
- **S-004:** `score` yorumu metriğe bağlıdır: cosine yüksek = yakın; L2 düşük = yakın. Yanıtta metrik adı da taşınır.

### 6.4 `E-SYNC-*` — Tutarlılık Kuralları

- **E-SYNC-001:** Varsayılan: **yazılabilir (read-your-writes)** — yazma onayı sonrası aynı kökte okunabilir.
- **E-SYNC-002:** Toplu yüklemelerde gecikme gözlenebilir; bu durum yanıtta `sync_state=async` bildirilir.
- **E-SYNC-003:** Silme işlemleri `tombstone` üzerinden yürür; okuma yolu tombstone'u uygular.
- **E-SYNC-004:** Kaynak kayıt geri alınırsa (rollback) indeks de geri alınır; çift sistem kuralı ihlali hata kodudur.
- **E-SYNC-005:** `version` geriye giden yazım (eski sürümün gelmesi) **sessizce düşer** ve olay kaydına yazılır.

### 6.5 `R-*` — Yeniden İnşa Kuralları

- **R-001:** Yeniden inşa her zaman **ikinci** indekste yapılır; mevcut indeks servis dışı bırakılmaz.
- **R-002:** Geçiş (cutover) yalnız sağlık sinyali eşik değerini geçtiğinde yapılır.
- **R-003:** Geri alma (rollback) tek adımda mümkündür; bayrak geri çevrilir, veri kopyalanmaz.
- **R-004:** Yeniden inşa işi kesilirse konumdan devam eder (checkpoint); sıfırdan başlamaz.

## 7. Veri Modeli

### 7.1 Koleksiyon (şema) Tablosu

| Alan | Tip | Boş/null | Kısıt | İndeks |
|---|---|---|---|---|
| `id` | string(64) | hayır | benzersiz | primary |
| `tenant` | string(32) | hayır | kiracı ayrımı | btree |
| `kind` | enum | hayır | ses/söz/görsel/profil | btree |
| `embedding` | float32[D] | hayır | boyut sabit | vektör |
| `metric` | enum | hayır | cosine/l2/dot | - |
| `norm` | float32 | evet | 0..1 | - |
| `payload.*` | json | evet | filtre alanı | btree (içinde) |
| `lang` | string(8) | evet | ISO benzeri | btree |
| `created_at` | datetime | hayır | UTC | btree |
| `updated_at` | datetime | hayır | UTC | btree |
| `version` | int64 | hayır | ≥1 | - |
| `state` | enum | hayır | active/tombstone/pending | btree |
| `source_ref` | string | hayır | K5 kayıt anahtarı | btree |

### 7.2 Sorgu Şeması

| Alan | Tip | Zorunlu | Varsayılan | Not |
|---|---|---|---|---|
| `vector` | float32[D] | evet | - | hazır embedding |
| `top_k` | int | hayır | 10 | sunucu üst sınırı var |
| `filter` | object | hayır | boş | F-001 kapsamı |
| `with_payload` | bool | hayır | true | false ise sadece id+score |
| `with_vector` | bool | hayır | false | vektör geri dönüşü ayrı izin |
| `request_id` | string | hayır | üretilir | önbellek/izleme anahtarı |
| `sort_by` | enum | hayır | score | yalnız desteklenen alanlar |

### 7.3 İndeks Yapılandırma Varsayılanları

| Ayar | Düşük maliyet | Dengeli | Yüksek recall | Etkisi |
|---|---|---|---|---|
| İndeks türü | IVF | HNSW | HNSW | yapı → doğruluk/maliyet |
| Aday (ef) | 64 | 128 | 256 | arama kalitesi ↔ hız |
| bağlantı (M) | 8 | 16 | 32 | yapı boyutu ↔ kalite |
| ölçümdeki örnek | %2 | %5 | %10 | inşa süresi |
| aday çarpanı | 1× | 2× | 3× | sonradan kesme maliyeti |

### 7.4 Bellek ve Kapasite Formülü

```
B_ısıci(N,D)   = N × D × 4 bayt                  # float32 ham
B_yapı(N,M)    = N × M × 2 × 8 bayt              # graf kenar örtüsü (yaklaşık)
B_segment(N)    = N × (D × q + 64) bayt           # q = bayt/boyut (float32: 4, int8: 1)
T_birim         = (B_ısıci + B_yapı) × 1.30      # %30 çalışma payı (headroom)
```

Örnek hesaplama (tasarım senaryosu, **ölçüm değildir**): `N=10.000.000`,
`D=768`, `float32` → `B_ısıci = 30.7 GB`, `M=16` → `B_yapı ≈ 24.6 GB`,
toplam ≈ `55.3 GB × 1.30 ≈ 71.9 GB`. Sonuç `⚠️ VERIFICATION REQUIRED: gerçek
kayıt sayısı ve donanım ölçümü yapılmadı`.

## 8. Kenar Durumlar

| # | Kenar durum | Tetikleyici | Beklenen davranış | Doğrulama |
|---|---|---|---|---|
| 1 | Boş sonuç kümesi | Filtre hiçbir kayıt tutmazsa | `200` + `hits: []`, hata değil | T-07 |
| 2 | Tek kayıt koleksiyonu | Test kurulumu | k=10 istenirse 1 döner | T-04 |
| 3 | Aşırı yüksek seçicilik | Nadir filtre | Önce filtre planına düşer | T-09 |
| 4 | Tam tersi: seçicilik düşük | Yaygın filtre | Önce vektör planı korunur | T-09 |
| 5 | Aynı id iki kez upsert | Yeniden gönderim | Son `version` kazanır, tek kayıt | T-05 |
| 6 | Eski sürümün gelmesi | Gecikmiş olay | Düşülür + olay kaydı | T-06 |
| 7 | Silinen id sorgusu | Tombstone | 404 / sonuç yok | T-08 |
| 8 | Boyut uyuşmazlığı | Farklı model çıktısı | Yazım öncesi 422, indeks bozulmaz | T-03 |
| 9 | Vektör tamamen aynı | Kopya içerik | Aynı skor; eşitlik bozma `id` ile deterministik | T-11 |
| 10 | Sorgu sırasında merge | Zamanlayıcı | Sonuç kümesi değişmez (atomik görünüm) | T-10 |
| 11 | Yanıt gövdesi taşması | Çok büyük `k` | Kırpma + `truncated=true` | T-12 |
| 12 | Kiracı sınırı aşımı | Yanlış `tenant` | Kapsama dışına erişim = boş/403 | T-13 |
| 13 | Saat kayması | düğüm saatleri | `updated_at` UTC + version devreye girer | T-14 |
| 14 | Sıfır bayt vektör | Bozuk kayıt yazımı | Yazar reddedilir (V-005) | T-15 |
| 15 | Filtre içine serbest metin | İstemci hatası | F-003: ayrı motora yönlendirme/400 | T-16 |

## 9. Hata Modları

| Kod | Semptom | Kök neden | Aksiyon | Kaçış / kurtarma |
|---|---|---|---|---|
| `VX-101` | 422 boyut uyuşmazlığı | Model çıktısı D farklı | İsteği reddet | Kaydı `pending_embedding` al |
| `VX-102` | 422 NaN/Inf | Normalizasyon hatası | Kaydı reddet | Embedding yeniden üret |
| `VX-103` | 400 bilinmeyen alan | F-001 ihlali | Şema hatası döndür | İstemci sürümünü güncelle |
| `VX-104` | 400 filtre derinliği | Aşırı iç içe | Derinlik sınırı bildir | Filtre sadeleştir |
| `VX-105` | `k` sınırı aşımı | Aşırı `k` | Kırp + uyar | Parametreyi düzelt |
| `VX-201` | İndeks yok/erişilemiyor | Kurulum ya da yetki | Sağlık alarmı P1 | Yeniden inşa bayrağı |
| `VX-202` | Shard eksik | Düğüm kaybı | Trafik dışındaki shard | Replica'dan doldur |
| `VX-203` | Zaman aşımı | Kilit/merge beklemesi | Zaman aşımı hatası | Merge'i iptal, tekrarla |
| `VX-301` | recall düşüşü | Yapı parametresi/hat | Metrik alarm | Parametre geri alma |
| `VX-302` | Bellek aşımı | N×D büyümesi | Segment baskı | Segment/bölüm ayarı |
| `VX-303` | İndeks bozulması | Kapanma anında yazım | Sağlık sinyali düşer | R-001 ile yeniden inşa |
| `VX-401` | Kiracı sızıntısı | Kapsama eksikliği | Güvenlik olayı | Filtre zorunlu kılınır |
| `VX-402` | Yetki reddi | Erişim kapısı | 403 | Kapsam tanımı düzelt |
| `VX-501` | Önbellek tutarsızlığı | TTL/iptal gecikmesi | İptal yayını | Kısa TTL, iptal akışı |
| `VX-502` | `sync_state` süresi dolmuş | Olay kuyruğu geride | Uyarı metriği | Kuyruk derinliği takibi |

## 10. Bağımlılıklar

| Bağımlılık | Tip | Sürüm | Zorunlu | Sağlayıcı | Not |
|---|---|---|---|---|---|
| Embedding üretimi | iç modül | UNKNOWN | evet | `k107-embedding-models` | Boyut sabitinin kaynağı |
| Kaynak kayıt (K5) | veri | UNKNOWN | evet | MySQL 18 DB | System-of-record |
| API kapısı | servis | UNKNOWN | evet | K9 routing | İstek doğrulama |
| Vektör depo yazılımı | üçüncü taraf | `⚠️ VERIFICATION REQUIRED` | evet | seçilmedi | Ürün adı yazılmaz |
| Ölçüm/izleme | iç | UNKNOWN | evet | K12 | recall/latans ihracı |
| Kuyruk/olay | altyapı | UNKNOWN | opsiyonel | K7 middleware | Senkronizasyon |
| Depolama (segment) | disk | UNKNOWN | evet | altyapı | Kapasite §7.4 |
| CPU (SIMD) | donanım | `⚠️ VERIFICATION REQUIRED` | opsiyonel | donanım | Avantaj, zorunlu değil |

## 11. Ölçüm ve Kabul Kriterleri

| # | Metrik | Hedef | Ölçüm noktası | Kabul |
|---|---|---|---|---|
| 1 | recall@10 (referans sorgu seti) | `⚠️ VERIFICATION REQUIRED` | puanlama işi | eşik kullanıcıca onaylı |
| 2 | p95 sorgu latansı | `⚠️ VERIFICATION REQUIRED` | API kapı logu | bütçe onaylı değil |
| 3 | p99 yazma onay latansı | `⚠️ VERIFICATION REQUIRED` | yazma yolu | - |
| 4 | indeks yeniden inşa süresi | `⚠️ VERIFICATION REQUIRED` | Reindexer | R-002 koşulu |
| 5 | boş sonuç oranı | düşük tutulur | sorgu metriği | Anomali alarm eşiği |
| 6 | `pending_embedding` yaşı | `⚠️ VERIFICATION REQUIRED` | kuyruk | Tükenme hedefi yok |
| 7 | tombstone temizlik gecikmesi | `⚠️ VERIFICATION REQUIRED` | temizlik işi | - |
| 8 | bellek kullanımı / eşik | ≤ %80 sürekli | süreç metriği | 30 dk penceresi |
| 9 | sağlıksız düğüm süresi | `⚠️ VERIFICATION REQUIRED` | sağlık sinyali | SLO tanımı yok |
| 10 | kayıt/vektör tutarsızlığı | 0 | mutabakat işi | Günlük denetim |

## 12. Güvenlik ve Uyum

| # | Tehdit | Kontrol | Durum |
|---|---|---|---|
| 1 | Kiracılar arası veri sızıntısı | Zorunlu `tenant` kapsamı + kapsama denetimi | tasarlandı |
| 2 | Sorgu üzerinden bilgi sızması | Yanıtta yalnız izinli alanlar (`with_payload`) | tasarlandı |
| 3 | Prompt/veri enjeksiyonu | Serbest metin filtresi F-003, JSON sınırları | tasarlandı |
| 4 | Aşırı kullanım (DoS) | `k`/gövde boyutu/filtre derinliği sınırları | tasarlandı |
| 5 | Vektörlerle PII çıkarımı | Ham vektör dışa aktarımı ayrı yetkiye bağlı | tasarlandı |
| 6 | Denetim izi eksikliği | Sorgu/yazma olayı kayıt (`request_id`) | tasarlandı |
| 7 | Yetkisiz yeniden inşa | `Reindexer` rol ayrımı | tasarlandı |
| 8 | Geri döndürülebilirlik | Yedek/geri alma planı **tanımsız** | `⚠️ VERIFICATION REQUIRED` |

## 13. Riskler ve Teknik Borç

| # | Risk | Olasılık | Etki | Azaltım |
|---|---|---|---|---|
| 1 | recall maliyeti yükseldikçe latans | orta | yüksek | iki aşamalı arama, aday çarpanı |
| 2 | Bellek maliyeti N ile doğrusal | yüksek | yüksek | kuantizasyon, segment baskısı |
| 3 | Embedding modeli değişimi → kırılma | orta | çok yüksek | yeni koleksiyon + R-001 reindex |
| 4 | Filtre/vektör sırası yanlış tahmin | orta | orta | histogram/ölçüm tabanlı plan |
| 5 | Silme tutarsızlığı (tombstone unutma) | düşük | yüksek | mutabakat işi (ölçüm #10) |
| 6 | Ürün seçimi erken kilitlenmesi | orta | orta | `VX-*` şemasının üründen bağımsızlığı |
| 7 | Önbellek bayat veri | orta | orta | kısa TTL + iptal yayını |
| 8 | Ölçüm hedeflerinin yazılmamış olması | **yüksek** | yüksek | Bölüm 11 eşiklerinin onayı |

## 14. Test Senaryoları

| ID | Senaryo | Ön koşul | Adım | Beklenen |
|---|---|---|---|---|
| T-01 | Tekil yazma → okuma | Boş koleksiyon | upsert 1 kayıt, k=1 sorgu | 1 hit, `version=1` |
| T-02 | Toplu yazma | 1000 kayıt | parti 100 | 1000 kayıt görünür, `sync_state` bildirimi |
| T-03 | Boyut uyuşmazlığı | D sabit | farklı D gönder | `VX-101`, indeks bozulmaz |
| T-04 | k > N | 3 kayıt | `top_k=10` | 3 hit döner |
| T-05 | Idempotent tekrar | Aynı `(id, version)` | iki kez gönder | tek kayıt, ikinci yanıt "atlandı" |
| T-06 | Eski sürüm engeli | `version=5` | `version=3` gönder | düşürülür, olay kaydı |
| T-07 | Boş sonuç | Kısıt tutmaz | sorgu | `200`, `hits: []` |
| T-08 | Silme sonrası okuma | tombstone | sorgu | sonuç yok |
| T-09 | Plan seçimi | Histogram | yüksek/düşük seçicilik | §5.3'e uygun plan |
| T-10 | Merge sırasında tutarlılık | merge kuyruktayken | 20 sorgu | sonuç sayısı değişmez |
| T-11 | Eşit skor determinizmi | kopya vektörler | k=5 | sıralama `id` ile sabit |
| T-12 | Yanıt taşması | büyük `k` | istek gönder | kırpma + bayrak |
| T-13 | Kiracı izolasyonu | 2 kiracı | A'dan B sorgusu | B verisi görünmez |
| T-14 | Saat kayması simülasyonu | düğüm saati ±5 dk | yaz-oku | `version` ile tutarlı |
| T-15 | Bozuk vektör reddi | NaN içeren | gönder | `VX-102` |
| T-16 | Serbest metin filtresi | `filter.text` | gönder | F-003 reddi |
| T-17 | Yeniden inşa kesintisi | iş yarıda | iptal + devam | checkpoint'ten sürer |
| T-18 | Sağlıksız düğüm | düğüm kapalı | sorgu | replica'ya yönlenir/boş |

## 15. Kanıt ve Doğrulama Listesi

- `Kanıt: .ai/architecture/k108-vector-index/vector-index-mimari.md` (bu dosya; tasarım belgesi)
- `Kanıt: ⚠️ VERIFICATION REQUIRED — repo'da vektör indeks uygulama kodu, indeks kurulum betiği ya da ölçüm raporu bulunmadığı doğrulanamadı (tarama: `shared/src/**`, sonuç yok)`.
- `Kanıt: ⚠️ VERIFICATION REQUIRED — Bölüm 11'deki SLO eşikleri onaylı değildir; hedefler kullanıcı kararı gerektirir`.
- `Kanıt: ⚠️ VERIFICATION REQUIRED — Bölüm 10'daki vektör depo yazılımı seçimi yapılmamıştır (ürün/sürüm adı yazılmaz)`.
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k4-yapay-zeka/README.md (L1-L422)` — katman genel bakışı; vektör DB olarak **Qdrant** adı geçmektedir.
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k4-yapay-zeka/index.md (L1-L124)` — K4 katman bileşen listesi.
- `Kanıt: shared/src/AI/KnowledgeBase.php (L1-L195, class KnowledgeBase)` — gerçek kod çapası; bilgi tabanı erişim katmanı.
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/k108-vector-index/vector-index-mimari.md`

Doğrulama listesi (kapılar):
1. Frontmatter 7 alan + `version: 4.0.0` + `updated: 2026-10-06`.
2. ≥1 ASCII akış, ≥6 tablo, kenar durum, hata modu, bağımlılık, `Kanıt:`.
3. Wiki-linkler yalnız k108-k119 klasör listesinden.
4. `verify` → `mojibake: 0`, `hasBom: false`.
5. Satır sayısı ≥500 (boş satırlar hariç).

## 16. Wiki-linkler ve Çapraz Referanslar

- Aynı klasör: [[vector-index-mimari.md]] · [[vector-index-operasyon.md]] · [[index.md]]
- `[[../k107-embedding-models/]]` — vektörün üretildiği katman (**klasör seviyesi referans**)
- `[[../k109-semantic-id/semantic-id-tasarim.md]]` — kimlik ↔ indeks eşlemesi
- `[[../k110-rag-knowledge/rag-retrieval-akisi.md]]` — sorgu çağıran RAG akışı
- `[[../k114-onnx-runtime/onnx-yurutme-akisi.md]]` — embedding çıkarım yolu
- `[[../k115-model-monitoring/model-monitoring-metrikleri.md]]` — recall/latans metrikleri
- `[[../k112-inference-serving/inference-serving-mimari.md]]` — servis sınırı

## 17. Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 4.0.0 | 2026-10-06 | D03 dilimi ilk yazım; şema, kurallar, hata kodları | vault-writer |
| 1.0.0 | 2026-09-20 | K4 taslak başlangıcı (yedek kaynak) | - |

> **Not:** Bu belge tasarım kararıdır; üretimde çalıştığı iddia edilmez. Eşik/sürüm
> değerleri `⚠️ VERIFICATION REQUIRED` ile işaretlidir.

## 18. Örnek İstek / Yanıt Şemaları

### 18.1 Vektör + filtre sorgusu

```json
{
  "vector": [0.0123, -0.4471, 0.9987],
  "top_k": 10,
  "filter": {
    "must": [
      { "field": "kind", "op": "eq", "value": "ses" },
      { "field": "lang", "op": "in", "value": ["tr", "en"] },
      { "field": "payload.year", "op": "range", "gte": 2015, "lte": 2026 }
    ],
    "must_not": [ { "field": "state", "op": "eq", "value": "tombstone" } ]
  },
  "with_payload": true,
  "with_vector": false,
  "request_id": "req-7f3c91"
}
```

### 18.2 Başarılı yanıt

```json
{
  "hits": [
    { "id": "trk_01H8X", "score": 0.9312, "version": 4,
      "payload": { "title": "örnek", "year": 2021, "kind": "ses" } },
    { "id": "trk_01H9Q", "score": 0.9187, "version": 2,
      "payload": { "title": "örnek 2", "year": 2019, "kind": "ses" } }
  ],
  "metric": "cosine",
  "truncated": false,
  "sync_state": "read_your_writes",
  "request_id": "req-7f3c91",
  "took_ms": 7
}
```

### 18.3 Hata yanıtı (boyut uyuşmazlığı)

```json
{
  "error": {
    "code": "VX-101",
    "message": "embedding boyutu koleksiyon boyutuyla uyuşmuyor",
    "expected_dim": 768,
    "received_dim": 1024,
    "field": "vector"
  },
  "request_id": "req-7f3c92"
}
```

### 18.4 Şema doğrulama kuralları (alan bazlı)

| Alan | Doğrulama | Hata kodu |
|---|---|---|
| `vector` | varlık, boyut, NaN/Inf yokluğu | `VX-101` / `VX-102` |
| `top_k` | tam sayı > 0, sunucu üst sınırı | `VX-105` |
| `filter.must[]` | alan varlığı (şema) | `VX-103` |
| `filter` derinliği | ≤ izinli derinlik | `VX-104` |
| `with_vector` | yalnız izinli roller | `VX-402` |
| `request_id` | uzunluk sınırı, karakter kümesi | `VX-103` |
| `tenant` | oturumla eşleşme | `VX-401` |

## 19. Kapasite Senaryoları

> Aşağıdaki sayılar **tasarım senaryosudur**, ölçüm değildir. Gerçek değerler
> `⚠️ VERIFICATION REQUIRED` ile işaretlidir.

| Senaryo | N | D | Bütçe (yaklaşık) | Yapı | Beklenen darboğaz |
|---|---|---|---|---|---|
| S1 – POC | 100.000 | 384 | < 1 GB | flat | CPU (brute-force) |
| S2 – Erken | 1.000.000 | 512 | ~2.5 GB + yapı | IVF | ölçüm örneği |
| S3 – Büyüme | 10.000.000 | 768 | ~55 GB + yapı | HNSW | bellek |
| S4 – Ölçek | 100.000.000 | 768 | ~553 GB + yapı | IVF + bölüm | disk/bölümleme |
| S5 – Yoğun yazma | 10.000.000 | 768 | +yazma kuyruğu | HNSW + parti | flush gecikmesi |

| Darboğaz | Belirti | Çözüm sırası |
|---|---|---|
| Bellek | Sayfa/vm baskısı, swap yoklaması | kuantizasyon → bölüm → disk yapısı |
| CPU (arama) | p95 yükselir, QPS sabit | aday çarpanı ↓, ef ↓, önbellek |
| Disk I/O | flush süresi uzar | parti boyutu ↑, segment frekansı ↓ |
| Yazma kuyruğu | `sync_state` gecikmesi | paralel yazıcı, arka plan |
| Ağ | yanıt gövdesi büyük | `with_payload=false`, k ↓ |

## 20. Bileşen Şartları (ayrıntılı)

### 20.1 `IndexWriter`

- Girdi: `(id, version, vector, payload, tenant, kind)`.
- Çıktı: onay `(id, version, accepted, reason)`.
- Sözleşme: aynı `(id, version)` tekrarı idempotenttir (S-005 varsayımı).
- Sınıf: id yoklama (lookup) → `version` karşılaştırması → kabul/ret.
- Hata: `VX-101`, `VX-102`, `VX-401`.

### 20.2 `FilterCompiler`

- Sözdizimi: `must`, `must_not`, `should`, `range`, `in`, `exists`.
- Reddedilen: bilinmeyen alan, serbest metin (`contains`), aşırı derinlik.
- Çıktı: indeks-dilli plan nesnesi (üründen bağımsız ara form).
- Garanti: derleme hatası isteği **erken** reddeder (arama başlamadan).

### 20.3 `QueryPlanner`

- Sinyal: filtre seçicilik tahmini (kayıt histogramı) + `k` + `with_vector`.
- Karar: §5.3 tablosu; belirsizlikte güvenli varsayılan = post-filter.
- Çıktı: `(plan, est_candidates, est_cost)`.
- İzleme: plan dağılımı metriği (önce filtre oranı).

### 20.4 `SegmentManager`

- Sıcak segment: son yazım yapılan, küçük, bellekte.
- Sıcak veri: birleşmiş, büyük, okumaya hazır.
- Birleşim tetikleyicisi: segment sayısı `> S` **veya** yaşı `> T`.
- Atomiklik: arama, birleşim sırasında eski+nova birleşimini geçici görünümde okur.

### 20.5 `TombstoneStore`

- Kayıt: `(id, deleted_at, epoch)`.
- Okuma yolu: eşleşen id'yi düşürür.
- Temizlik: `epoch` eski kayıtlar toplu silinir (yalnız yeniden inşa tamamlandıysa).

### 20.6 `Reindexer`

- Girdi: kaynak olay akışı + başlangıç `checkpoint`.
- Adım: `create(new)` → `backfill` → `catchup` → `verify` → `cutover` → `retire(old)`.
- Doğrulama: mutabakat örneği (§11 ölçüm #10) ve sağlık eşiği.
- Geri alma: `cutover` öncesi her an; sonrası için ters bayrak.

## 21. Sözlük

| Terim | Orijinal identifier | Açıklama |
|---|---|---|
| Vektör | embedding vector | Yüksek boyutlu sayı dizisi |
| Boyut | dimension (D) | Vektör uzunluğu |
| Metrik | metric | Yakınlık ölçütü (cosine/l2/dot) |
| K-NN | k-nearest neighbors | En yakın k komşu araması |
| Tombstone | tombstone | Silinmiş kayıt işareti |
| Segment | segment | İndeks veri parçası |
| Birleşim | merge | Segmentleri tek parçada toplama |
| Akış (flush) | flush | Bellek içi veriyi diske yazma |
| Filtre | filter | Metadata kısıtı |
| Seçicilik | selectivity | Filtrenin tuttuğu kayıt oranı |
| Aday çarpanı | over-fetch ratio | Kesmeden önce çekilen aday katı |
| Yeniden inşa | reindex | Sıfırdan ikinci indeks oluşturma |
| Geçiş | cutover | Trafik anahtarını çevirme |
| Önbellek | cache | Kısa vadeli tekrar yanıtı |
| SLO | service level objective | Hizmet seviyesi hedefi |
| recall@k | recall@k | İlk k içinde doğru oranı |
| Kiracı | tenant | Veri sahibi birim |
| Yaz-oku | read-your-writes | Kendi yazısını okuyabilme |
| Mutabakat | reconciliation | İki sistem eşitliğinin denetimi |
| Kuyruk | queue | Sıraya alınmış işler |
| checkpoint | checkpoint | Kesintiden devam noktası |

## 22. Karar Kayıtları (ADR özeti)

| # | Karar | Alternatif | Gerekçe | Durum |
|---|---|---|---|---|
| A1 | Şema üründen bağımsız (`VX-*`) | Ürüne özel alan adları | Değişim maliyeti | kabul edildi |
| A2 | `float32` varsayılan | Sadece int8 | Doğruluk güvencesi | kabul edildi |
| A3 | Tombstone'lu silme | Fiziksel anlık silme | Tutarlılık + geri alma | kabul edildi |
| A4 | Çift indeksli reindex | Çevrimiçi tek indeks | Risk sınırı (R-001) | kabul edildi |
| A5 | Ölçüm hedefleri yazılmadı | Erken sabitleme | Uydurma hedef riski | bekliyor (`⚠️ VERIFICATION REQUIRED`) |
| A6 | Ürün seçimi yapılmadı | Hemen seçim | Kanıt yokluğu | bekliyor (`⚠️ VERIFICATION REQUIRED`) |

## 23. Uygulama Notları (şema dışı ayrıntılar)

### 23.1 Puan (score) yorumlama tablosu

| Metrik | Puan aralığı | Yorumlama | Dönüşüm gerekli mi? |
|---|---|---|---|
| cosine | -1..1 | 1'e yakın = benzer | hayır (doğrudan sunulabilir) |
| cosine (normalize) | 0..1 | yüksek = benzer | hayır |
| L2 (euclidean) | 0..∞ | düşük = benzer | **evet** (skor yönü ters) |
| dot product | -∞..∞ | yüksek = benzer | hayır ama boyut etkiler |

**Kural:** API yanıtındaki `metric` alanı olmadan skor **anlamsızdır**; istemci
skoru yorumlarken metriği okumak zorundadır. Bu kural `VX-*` sözleşmesine sabitlenmiştir.

### 23.2 Eşik değerleri (tasarım varsayılanı, onay bekliyor)

| Eşik | Varsayılan | Neden bu değer | Durum |
|---|---|---|---|
| `top_k` üst sınırı | 100 | Yanıt boyutu + CPU bütçesi | `⚠️ VERIFICATION REQUIRED` |
| Filtre derinliği | 8 | Derin iç içe planlama maliyeti | `⚠️ VERIFICATION REQUIRED` |
| Aday çarpanı (dengeli) | 2× | recall/latans denemesi | `⚠️ VERIFICATION REQUIRED` |
| Merge segment sayısı | 8 | Yazma/okuma dengesi | `⚠️ VERIFICATION REQUIRED` |
| Merge yaşı | 30 dk | Küçük segment birikimi | `⚠️ VERIFICATION REQUIRED` |
| Önbellek TTL | 5 sn | Bayatlık riski düşük, tekrar yüksek | `⚠️ VERIFICATION REQUIRED` |
| Bellek uyarı eşiği | %80 sürekli | 30 dk pencere | `⚠️ VERIFICATION REQUIRED` |
| `pending_embedding` yaşı | 15 dk | Arka plan üretimi | `⚠️ VERIFICATION REQUIRED` |

### 23.3 İzinli roller

| Rol | Sorgu | Yazma | Vektör geri okuma | Reindex | Denetim okuma |
|---|---|---|---|---|---|
| `reader` | evet | hayır | hayır | hayır | hayır |
| `writer` | evet | evet | hayır | hayır | hayır |
| `indexer` | hayır | evet | evet | evet | hayır |
| `auditor` | evet | hayır | hayır | hayır | evet |
| `admin` | evet | evet | evet | evet | evet |

### 23.4 Bağlantı bütünlüğü (bu dosyadan çıkan linkler)

| Hedef | Biçim | Var mı |
|---|---|---|
| `[[vector-index-mimari.md]]` | aynı klasör | evet (bu dosya) |
| `[[vector-index-operasyon.md]]` | aynı klasör | evet |
| `[[index.md]]` | aynı klasör | evet |
| `[[../k109-semantic-id/semantic-id-tasarim.md]]` | çapraz | evet (D03 planı) |
| `[[../k110-rag-knowledge/rag-retrieval-akisi.md]]` | çapraz | evet (D03 planı) |
| `[[../k114-onnx-runtime/onnx-yurutme-akisi.md]]` | çapraz | evet (D03 planı) |
| `[[../k115-model-monitoring/model-monitoring-metrikleri.md]]` | çapraz | evet (D03 planı) |
| `[[../k112-inference-serving/inference-serving-mimari.md]]` | çapraz | evet (D03 planı) |

> `k107-embedding-models` linki klasör seviyesindedir ve D03 (k108-k119) dışıdır;
> dosya adı uydurulmamıştır. Kırık dosya-linki **0** hedeflenir.

### 23.5 Bilinmeyenler (açıkça yazılmış)

| Konu | Durum | Ne gerekli |
|---|---|---|
| Vektör depo ürün seçimi | UNKNOWN | Kriter seti + deneme |
| recall/latans hedefleri | UNKNOWN | Ölçüm + iş hedefi onayı |
| Gerçek N ve D | UNKNOWN | Kaynak kayıt sayımı |
| Eğitim verisi sürümü | UNKNOWN | `k111-training-pipeline` çıktısı |
| Kiracı sayısı | UNKNOWN | Ürün gereksinimi |
| Kuyruk altyapısı | UNKNOWN | K7 kararı |
| Yedekleme politikası | UNKNOWN | K5/K12 politikası |
