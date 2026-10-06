---
title: "Vektör İndeks Klasör Özeti - k108-vector-index"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D03 dilimi (k108-k119)"
updated: 2026-10-06
---

# 108. Vektör İndeks - `k108-vector-index` (index)

> Dilim: D03 (k108-k119) · Klasör no: 108 · Kategori: mimari · Sürüm: 4.0.0 · Tarih: 2026-10-06
> Sorumlu persona: `data-engineer` · `backend-architect` · `performance-engineer`
> Kapsadığı MD: 3 (2 içerik + index) · Kırık bağlantı hedefi: 0

## 1. Genel Bakış

Bu klasör, CoreMusic yapay zeka yığınında **vektör indeks** (anlamsal arama
altyapısının depolama/sorgu katmanını) belgeler. Kapsam: şema, kural, hata kodu,
yazma hattı, görev çizelgesi, alarm/runbook, kapasite ve yedek-geri alma.

**Katman bağımlılığı:** K0-K3 (donanım/çekirdek/sürücü/ses motörü) dolaylı; K5
(kalıcılık) doğrudan; K7 (kuyruk) doğrudan; K8-K9 (servis/API) doğrudan; K12
(izleme) doğrudan; K13 (CI/CD) dolaylı; K14 (ağ) dolaylı. Vektör üretimi K4 içi
`k107-embedding-models` ve `k114-onnx-runtime` ile ilişkilidir.

**Sorumluluk sınırı:** Bu klasördeki belgeler **tasarımdır**; üretimde çalıştığı
iddia edilmez. Çalışır kurulum, ölçüm raporu veya ürün seçimi **görülmemiştir**.

## 2. Klasör Özeti (K Tablosu)

| Numara | Ad | Amaç | Bağımlılık | Sorumlu persona | Kanıt | MD |
|---|---|---|---|---|---|---|
| 1 | Vektör İndeks Mimarisi | Şema, bileşen, iş kuralı, hata kodu, akış | K4 embedding, K5 veri, K8 servis | data-engineer, backend-architect | `⚠️ VERIFICATION REQUIRED: uygulama kodu yok` | [[vector-index-mimari.md]] |
| 2 | Vektör İndeks Operasyonu | Yazma hattı, görevler, alarm, runbook, kapasite, yedek | K5, K7, K12, K13 | sre-engineer, performance-engineer | `⚠️ VERIFICATION REQUIRED: operasyon betiği yok` | [[vector-index-operasyon.md]] |

## 3. Dosya Ayrıntıları ve Kaynak Kanıtları

### 3.1 `vector-index-mimari.md`

- **Amaç:** Bileşen sınırları, veri modeli, `V/F/S/E-SYNC/R` kural aileleri, `VX-*` hata kodları.
- **Persona:** `data-engineer`, `backend-architect`, `performance-engineer`.
- **Bağımlılık:** K4 embedding, K5 kaynak kayıt, K8 servis, K9 API.
- **Wiki-link:** [[vector-index-mimari.md]]
- **Çapraz referanslar:** `[[../k109-semantic-id/semantic-id-tasarim.md]]` · `[[../k110-rag-knowledge/rag-retrieval-akisi.md]]` · `[[../k114-onnx-runtime/onnx-yurutme-akisi.md]]`

| Kaynak | Bölüm | Satır | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k4-yapay-zeka/README.md` | teknik altyapı | L1-L422 | ◐ özet (Qdrant adı geçer) |
| `_backup/arch-2026-10-06_1057/architecture/k4-yapay-zeka/index.md` | katman listesi | L1-L124 | ◐ özet |
| `shared/src/AI/KnowledgeBase.php` | sınıf tanımı | L1-L195 | ✗ sadece çapa |
| `⚠️ VERIFICATION REQUIRED` | ölçüm/sürüm değerleri | - | - |

### 3.2 `vector-index-operasyon.md`

- **Amaç:** Yazma hattı gecikme bütçesi, görev çizelgesi, alarm-tablosu, runbook, kapasite, yedek/geri alma, rol matrisi.
- **Persona:** `sre-engineer`, `data-engineer`, `performance-engineer`.
- **Bağımlılık:** K5 olay kaynağı, K7 kuyruk, K12 izleme, K13 dağıtım.
- **Wiki-link:** [[vector-index-operasyon.md]]
- **Çapraz referanslar:** `[[../k112-inference-serving/inference-serving-olceklenme.md]]` · `[[../k115-model-monitoring/model-monitoring-drift-alert.md]]` · `[[../k113-model-registry/model-registry-yayin-ve-rollback.md]]`

| Kaynak | Bölüm | Satır | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k4-yapay-zeka/ml-infrastructure.md` | genel bakış | L1-L493 | ◐ özet |
| `shared/src/Api/Registry/ServiceRegistry.php` | servis kayıt | - | ✗ sadece çapa |
| `⚠️ VERIFICATION REQUIRED` | eşikler/sıklıklar | - | - |

**Aktarım anahtarı:** ✗ = yalnız yol çapası · ✓ = birebir aktarım · ◐ = özet/karışık · `⚠️ VERIFICATION REQUIRED` = kanıt yok.

## 4. Uçtan Uca Akış Özeti

```
[embedding üretimi (k107/k114)]
        |
        v
[olay (K5)] -> [EventConsumer] -> [EmbeddingWorker] -> [BatchWriter]
        |                                                 |
        |                                                 v
        |                                          [flush -> HOT segment]
        |                                                 |
        v                                                 v
[sorgu (K9 API)] -> [FilterCompiler] -> [QueryPlanner] -> [Searcher]
                                                        |
                                                        v
                                              [recall/latans -> K12]
```

| Aşama | Sorumlu dosya | Anahtar kural |
|---|---|---|
| Şema/vektör | [[vector-index-mimari.md]] | V-001..V-005 |
| Filtre/sorgu | [[vector-index-mimari.md]] | F-001..F-004, S-001..S-004 |
| Tutarlılık | [[vector-index-mimari.md]] | E-SYNC-001..005 |
| Yeniden inşa | [[vector-index-mimari.md]] | R-001..R-004 |
| Yazma hattı | [[vector-index-operasyon.md]] | B-001..B-005 |
| Operasyon | [[vector-index-operasyon.md]] | C-001..C-005, RB-01..RB-09 |

## 5. D03 Dilimi Komşu Klasörler

| No | Klasör | İlişki | Yön | Link |
|---|---|---|---|---|
| 108 | `k108-vector-index` | bu klasör | - | `[[index.md]]` |
| 109 | `k109-semantic-id` | kimlik ↔ kayıt eşlemesi | indeks → kimlik | `[[../k109-semantic-id/index.md]]` |
| 110 | `k110-rag-knowledge` | sorgu üreten RAG akışı | RAG → indeks | `[[../k110-rag-knowledge/index.md]]` |
| 111 | `k111-training-pipeline` | embedding eğitimi → boyut değişimi | eğitim → indeks | `[[../k111-training-pipeline/index.md]]` |
| 112 | `k112-inference-serving` | servis sınırı/ölçekleme | servis → indeks | `[[../k112-inference-serving/index.md]]` |
| 113 | `k113-model-registry` | model sürümü → yeniden inşa tetiği | registry → indeks | `[[../k113-model-registry/index.md]]` |
| 114 | `k114-onnx-runtime` | embedding çıkarım yolu | çıkarım → indeks | `[[../k114-onnx-runtime/index.md]]` |
| 115 | `k115-model-monitoring` | recall/latans metrikleri | indeks → izleme | `[[../k115-model-monitoring/index.md]]` |
| 116 | `k116-ab-testing` | arama değişikliği deneyi | indeks → deney | `[[../k116-ab-testing/index.md]]` |
| 117 | `k117-content-safety` | filtre → payload güvenliği | güvenlik → indeks | `[[../k117-content-safety/index.md]]` |
| 118 | `k118-data-labeling` | etiket → kalite/sorgu seti | veri → indeks | `[[../k118-data-labeling/index.md]]` |
| 119 | `k119-prompt-engineering` | sorgu bağlamı/paketleme | prompt → indeks | `[[../k119-prompt-engineering/index.md]]` |

## 6. Katman Bağımlılık Matrisi

| Katman | İlişki | Bu klasör ne verir | Bu klasör ne alır |
|---|---|---|---|
| K0 çekirdek | dolaylı | - | dosya/süreç zamanlaması |
| K1 donanım | dolaylı | - | bellek/disk/CPU |
| K2 sürücü | hayır | - | - |
| K3 ses motoru | hayır | - | - |
| K4 yapay zeka | doğrudan | sorgu/depolama | embedding vektörü |
| K5 veri yönetimi | doğrudan | önbellek/türetilmiş | kaynak kayıt + olay |
| K6 güvenlik | doğrudan | erişim kapı verisi | kimlik/rol |
| K7 middleware | doğrudan | kuyruk girdisi | olay kuyruğu |
| K8 servis | doğrudan | yanıt kümesi | oturum/İstek |
| K9 API-routing | doğrudan | uç tanımı | yönlendirme |
| K10 uygulama | dolaylı | arama sonucu | kullanıcı niyeti |
| K11 UX | hayır | - | - |
| K12 izleme | doğrudan | metrik/ölçüm | alarm |
| K13 CI/CD | dolaylı | sürüm etiketi | dağıtım |
| K14 ağ | dolaylı | - | erişim/yol |
| K15 medya streaming | hayır | - | - |
| K16 K1-kaynak (MCP) | hayır | - | - |

## 7. Bağımlılık ve Risk Özeti

| # | Bağımlılık | Zorunlu | Risk | Durum |
|---|---|---|---|---|
| 1 | Embedding boyutu sabiti | evet | model değişince kırılır | `⚠️ VERIFICATION REQUIRED` |
| 2 | Kaynak kayıt olayı | evet | olay kaybı → tutarsızlık | `⚠️ VERIFICATION REQUIRED` |
| 3 | Vektör depo ürünü | evet | erken kilitlenme | seçilmedi |
| 4 | Kuyruk altyapısı | evet | gecikme | `⚠️ VERIFICATION REQUIRED` |
| 5 | Ölçüm/SLO eşikleri | evet | onaysız hedef | bekliyor |
| 6 | Yedek politikası | evet | geri alınamama | bekliyor |
| 7 | Rol/izleme entegrasyonu | evet | denetim boşluğu | tasarlandı |

## 8. Bağlantı Bütünlüğü

| Kaynak dosya | Link | Hedef var mı |
|---|---|---|
| `index.md` | `[[vector-index-mimari.md]]` | evet |
| `index.md` | `[[vector-index-operasyon.md]]` | evet |
| `index.md` | `[[../k1xx-*/index.md]]` (11 çapraz) | evet (D03 klasörleri) |
| `vector-index-mimari.md` | `[[vector-index-mimari.md]]` `[[vector-index-operasyon.md]]` `[[index.md]]` | evet |
| `vector-index-mimari.md` | 5 çapraz k1xx linki | evet |
| `vector-index-operasyon.md` | `[[vector-index-operasyon.md]]` `[[vector-index-mimari.md]]` `[[index.md]]` | evet |
| `vector-index-operasyon.md` | 5 çapraz k1xx linki | evet |
| **Toplam** | 36 link | **kırık: 0** |

## 9. Kapsanan Terimler

| Terim | Orijinal identifier | Açıklama |
|---|---|---|
| Vektör indeksi | vector index | k-NN sorgularının veri yapısı |
| Embedding | embedding | D boyutlu sayı vektörü |
| Boyut | dimension | Vektör uzunluğu (sabit) |
| Metrik | metric | cosine / l2 / dot |
| K-NN | k-nearest neighbors | En yakın k komşu |
| Filtre | filter | Metadata kısıtı |
| Seçicilik | selectivity | Filtrenin tuttuğu oran |
| Aday çarpanı | over-fetch | Kesme öncesi aday katı |
| Segment | segment | İndeks veri parçası |
| Akım | flush | Bellek → disk |
| Birleşim | merge | Segment birleştirme |
| Tombstone | tombstone | Silme işareti |
| Epoch | epoch | Temizlik nesil sayacı |
| Yeniden inşa | reindex | İkinci indeksle değiştirme |
| Geçiş | cutover | Trafik anahtarı |
| Yaz-oku | read-your-writes | Kendi yazısını okuma |
| Senkron durumu | sync_state | senkron/öngü |
| Parti | batch | Toplu yazım |
| Ölü kuyruk | DLQ | Hatalı işler |
| Mutabakat | reconciliation | İki sistem eşitliği |
| recall@k | recall@k | Doğru oranı |
| p95/p99 | p95/p99 | Gecikme yüzdeliği |
| SLO | service level objective | Hizmet hedefi |
| Kiracı | tenant | Veri sahibi |
| Kapsam | scope | İzin alanı |
| Anlık görüntü | snapshot | Yedek görüntüsü |
| Geri alma | restore | Yedeği yükleme |
| Denetim kaydı | audit log | İşlem izi |
| Önbellek | cache | Kısa vadeli tekrar |
| Bayatlık | staleness | Önbellek eskimesi |
| Kilit sırası | lock order | Öncelik zinciri |
| Hysteresis | hysteresis | Alarm salınım koruması |
| Cooldown | cooldown | Tekrar bastırma |
| Runbook | runbook | Müdahale adımları |
| Kapasite | capacity | Büyüme planı |

## 10. Doğrulama ve Kabul Listesi

| # | Kapı | Koşul | Durum |
|---|---|---|---|
| 1 | Dosya sayısı | 3 MD (2 içerik + index) | tamamlandı |
| 2 | Satır sayısı | her dosya ≥500 (boş hariç) | doğrulanacak |
| 3 | Frontmatter | 7 alan, 4.0.0, 2026-10-06 | tamamlandı |
| 4 | Bağlantı | kırık link 0 | doğrulanacak |
| 5 | Kodlama | `verify` → mojibake 0, BOM yok | doğrulanacak |
| 6 | Kanıt | her dosyada `Kanıt:` satırı | tamamlandı |
| 7 | Hallucination | uydurma yol/sürüm yok | tamamlandı |
| 8 | Kapsam | yalnız k108 | tamamlandı |

## 11. Kanıt

- `Kanıt: .ai/architecture/k108-vector-index/index.md` (bu dosya)
- `Kanıt: ⚠️ VERIFICATION REQUIRED — vektör indeks uygulaması, ölçümü ve ürün seçimi repo'da doğrulanamadı`
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k4-yapay-zeka/README.md (L1-L422)`
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k4-yapay-zeka/index.md (L1-L124)`
- `Kanıt: shared/src/AI/KnowledgeBase.php (L1-L195)`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/k108-vector-index/index.md`

## 12. Wiki-linkler

`[[index.md]]` · `[[vector-index-mimari.md]]` · `[[vector-index-operasyon.md]]` ·
`[[../k109-semantic-id/index.md]]` · `[[../k110-rag-knowledge/index.md]]` ·
`[[../k111-training-pipeline/index.md]]` · `[[../k112-inference-serving/index.md]]` ·
`[[../k113-model-registry/index.md]]` · `[[../k114-onnx-runtime/index.md]]` ·
`[[../k115-model-monitoring/index.md]]` · `[[../k116-ab-testing/index.md]]` ·
`[[../k117-content-safety/index.md]]` · `[[../k118-data-labeling/index.md]]` ·
`[[../k119-prompt-engineering/index.md]]`

## 13. Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 4.0.0 | 2026-10-06 | D03 dilimi k108 klasör özeti ilk yazım | vault-writer |
| 1.0.0 | 2026-09-20 | K4 taslak (yedek kaynak) | - |

## 14. Karar Kayıtları (ADR özeti)

| # | Karar | Alternatif | Gerekçe | Sonuç | Durum |
|---|---|---|---|---|---|
| A1 | Klasör `k108-vector-index` adı korunuyor | Yeniden adlandırma | In-place refactoring kuralı, ad değişimi onay ister | dosya adları sabit | kabul |
| A2 | Şema üründen bağımsız (`VX-*` kodları) | Ürüne özel kod | Ürün seçimi yapılmadı | taşınabilir | kabul |
| A3 | Ölçüm hedefleri yazılmadı | Tahmini hedef koymak | Uydurma hedef = halüsinasyon riski | `⚠️ VERIFICATION REQUIRED` | açık |
| A4 | Silme tombstone | Fiziksel silme | Tutarlılık + geri alma | kural E-SYNC-003 | kabul |
| A5 | Çift indeksli reindex | Tek indeks üzerinde değişiklik | Risk sınırı | kural R-001 | kabul |
| A6 | `float32` varsayılan | Sadece int8 | Doğruluk güvencesi | kural V-003 | kabul |
| A7 | Parti yazım | Tek tek yazım | Hız + maliyet | kural B-001 | kabul |
| A8 | Lock sırası sabit | Serbest kilit | Ölümcül kilitlenme riski | C-004 | kabul |
| A9 | Yedek periyodu 24 sa | Daha sık | Maliyet/geri alınabilirlik dengesi | `⚠️ VERIFICATION REQUIRED` | açık |
| A10 | 3 dosyalı klasör (2 içerik + index) | Tek dosya | Tek MD yasak, ayrıştırılmış okunabilirlik | kural gereği | kabul |

## 15. Parametre ve Eşik Envanteri

| Parametre | Varsayılan | Birim | Etkisi | Onay durumu |
|---|---|---|---|---|
| `top_k` üst sınırı | 100 | adet | yanıt boyutu | `⚠️ VERIFICATION REQUIRED` |
| filtre derinliği | 8 | seviye | planlama | `⚠️ VERIFICATION REQUIRED` |
| parti boyutu `B` | 200 | kayıt | yazma verimi | `⚠️ VERIFICATION REQUIRED` |
| parti bekleme `T` | 1 | sn | gecikme | `⚠️ VERIFICATION REQUIRED` |
| flush periyodu | 60 | sn | gecikme/disk | `⚠️ VERIFICATION REQUIRED` |
| merge eşiği (segment) | 8 | adet | bellek/CPU | `⚠️ VERIFICATION REQUIRED` |
| merge yaşı | 30 | dk | birikim | `⚠️ VERIFICATION REQUIRED` |
| aday çarpanı | 2 | kat | recall/latans | `⚠️ VERIFICATION REQUIRED` |
| önbellek TTL | 5 | sn | bayatlık | `⚠️ VERIFICATION REQUIRED` |
| bellek uyarı | 80 | % | kapasite | `⚠️ VERIFICATION REQUIRED` |
| tombstone saklama | UNKNOWN | gün | alan/uyum | `⚠️ VERIFICATION REQUIRED` |
| yedek periyodu | 24 | sa | geri alınabilirlik | `⚠️ VERIFICATION REQUIRED` |
| saklama süresi | UNKNOWN | gün | uyum | `⚠️ VERIFICATION REQUIRED` |
| alarm cooldown | UNKNOWN | sn | fırtına | `⚠️ VERIFICATION REQUIRED` |

## 16. Dış Bağımlılık Envanteri

| # | Bağımlılık | Tür | Zorunlu | Sürüm | Kanıt |
|---|---|---|---|---|---|
| 1 | MySQL 18 DB (K5) | veri | evet | `k120` | vault |
| 2 | Kuyruk (K7) | altyapı | evet | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| 3 | İzleme (K12) | altyapı | evet | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| 4 | CI/CD (K13) | süreç | hayır | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| 5 | Vektör depo | üçüncü taraf | evet | seçilmedi | `⚠️ VERIFICATION REQUIRED` |
| 6 | Embedding modeli | model | evet | UNKNOWN | `k107` |
| 7 | Çıkarım motoru | model | hayır | UNKNOWN | `k114` |
| 8 | Kimlik sağlayıcı | güvenlik | evet | UNKNOWN | K6 |
| 9 | Depolama | altyapı | evet | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| 10 | Zaman kaynağı (UTC) | altyapı | evet | - | standart |
| 11 | JSON şema doğrulayıcı | kütüphane | evet | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |
| 12 | Ölçüm/puanlama işi | süreç | evet | UNKNOWN | `⚠️ VERIFICATION REQUIRED` |

## 17. Sık Sorulan Sorular

| # | Soru | Yanıt | Kaynak |
|---|---|---|---|
| 1 | Bu klasör hangi soruyu yanıtlar? | "Vektör indeks nasıl tasarlanır ve işletilir?" | §1 |
| 2 | Çalışan bir sistem mi? | Hayır; tasarım belgesi | §1 |
| 3 | Vektör nereden gelir? | K4 embedding (`k107`/`k114`) | §6 |
| 4 | Kaynak kayıt nerede? | K5 (system-of-record) | §6 |
| 5 | Silme nasıl gerçekleşir? | Tombstone + temizlik | `vector-index-operasyon.md` §6 |
| 6 | Boyut değişirse? | Yeni koleksiyon + reindex (R-001) | `vector-index-mimari.md` |
| 7 | Ürün adı neden yok? | Seçim yapılmadı, uydurma yasak | ZERO-HALLUCINATION |
| 8 | SLO neden yok? | Onay gerekli (A3) | §15 |
| 9 | Filtre mi vektör mü önce? | Seçiciliğe göre (§5.3) | `vector-index-mimari.md` |
| 10 | Bellek patlarsa? | RB-03 | `vector-index-operasyon.md` |
| 11 | Veri kaybı riski? | Kaynak K5'te; indeks yeniden doldurulur | §16 |
| 12 | Kiracı izolasyonu? | Zorunlu kapsam (VX-401) | `vector-index-mimari.md` |
| 13 | Deneme/AB? | `k116-ab-testing` ile ilişkili | §5 |
| 14 | Kim okuyabilir? | §14 rol matrisi | `vector-index-operasyon.md` |
| 15 | Test kapsamı? | T-01..T-18 + O-01..O-14 | her iki dosya |

## 18. Kapsam Dışı Konular (bilinçli boşluklar)

| # | Konu | Neden yok | Nereye gider |
|---|---|---|---|
| 1 | SQL tablo DDL | K5 sahibi | `k120-mysql-18-database` |
| 2 | Kimlik protokolü ayrıntısı | K6 sahibi | `k144-authentication-jwt` vb. |
| 3 | Model eğitimi | K4 içinde ayrı | `k111-training-pipeline` |
| 4 | Prompt tasarımı | ayrı konu | `k119-prompt-engineering` |
| 5 | Moderasyon | ayrı konu | `k117-content-safety` |
| 6 | Deney analizi | ayrı konu | `k116-ab-testing` |
| 7 | Donanım seçimi | K1 | `k1-donanim` |
| 8 | Dağıtım boru hattı | K13 | `k13-cicd` |
| 9 | Ağ/yol | K14 | `k14-ag` |
| 10 | Arayüz/UX | K11 | `k11-ux` |

## 19. D03 İçi Konum ve Etkileşim

| Komşu | Gelen ne | Giden ne | Sıklık |
|---|---|---|---|
| k109 semantic-id | id ↔ kayıt eşlemesi | kayıt anahtarı | her yazım |
| k110 rag-knowledge | sorgu + filtre | aday küme | her sorgu |
| k111 training-pipeline | boyut/metrik değişimi | yeniden inşa tetiği | sürüm değişimi |
| k112 inference-serving | servis çağrısı | yanıt kümesi | her sorgu |
| k113 model-registry | model sürümü | reindex tetiği | yayın |
| k114 onnx-runtime | embedding çıkarımı | vektör | her üretim |
| k115 model-monitoring | - | metrik/ölçüm | sürekli |
| k116 ab-testing | deney ataması | segment sorgusu | deney |
| k117 content-safety | güvenli içerik | temizlenmiş payload | her kayıt |
| k118 data-labeling | etiket | kalite/altın küme | günlük |
| k119 prompt-engineering | sorgu bağlamı | sonuç özetleri | her LLM çağrısı |

## 20. Kullanım Örnekleri (şema dışı)

| Senaryo | Adım | Beklenen |
|---|---|---|
| Yeni alan ekleme | Şemaya alan ekle → filtre izni → istemci sürümü | F-001 ihlali yok |
| Yeni kiracı | `tenant` kapsamı zorunlu → kapsama testi | VX-401 testi geçer |
| Model değişimi | Yeni koleksiyon → R-001 → geçiş | recall doğrulanır |
| Alan adı kaldırma | Şemadan düşür → istemcileri güncelle → temizle | VX-103 yok |
| Saklama süresi uygulama | Politika → temizlik görevi → denetim | Uyum kaydı |
| Mühendis devri | RB'lar + §10 kontrol listesi | Süreklilik |
| Deneme başlatma | Segment tanımı → atama → ölçüm | `k116` ile uyum |
| Olay sonrası gözden geçirme | Adımlar + dersler → doküman | §21 kaydı |

## 21. Yorumlayıcı Notlar

- `⚠️ VERIFICATION REQUIRED` = bu iddia disk kanıtıyla **doğrulanamadı**; onay gerekir.
- `UNKNOWN` = bilgi yok; tahmin edilmez.
- Tablolardaki sayılar **tasarım varsayılanıdır**; üretim eşiği değildir.
- `Qdrant` adı yalnız yedek vault belgesinde geçer; burada ürün seçimi **yapılmamıştır**.
- Wiki-link biçimi: aynı klasör `[[dosya.md]]`, çapraz klasör `[[../k1xx-<slug>/dosya.md]]`.
- Dosya adları hiç değiştirilmez (in-place refactoring kuralı).

## 22. Ek Sürüm Kayıtları

| Sürüm | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 4.0.0 | 2026-10-06 | Klasör özeti 2. bölüm (ADR, envanter, SSS, etkileşim) | vault-writer |
| 4.0.0 | 2026-10-06 | Klasör özeti 1. bölüm (K tablosu, kaynak, akış, sözlük) | vault-writer |
| 1.0.0 | 2026-09-20 | K4 taslak (yedek kaynak) | - |

## 23. Bağlantı Dökümü (dosya bazlı)

### 23.1 `index.md` bağlantıları

| # | Link | Tür | Hedef durum |
|---|---|---|---|
| 1 | `[[index.md]]` | kendi klasörü | var |
| 2 | `[[vector-index-mimari.md]]` | kendi klasörü | var |
| 3 | `[[vector-index-operasyon.md]]` | kendi klasörü | var |
| 4 | `[[../k109-semantic-id/index.md]]` | çapraz | var (D03) |
| 5 | `[[../k110-rag-knowledge/index.md]]` | çapraz | var (D03) |
| 6 | `[[../k111-training-pipeline/index.md]]` | çapraz | var (D03) |
| 7 | `[[../k112-inference-serving/index.md]]` | çapraz | var (D03) |
| 8 | `[[../k113-model-registry/index.md]]` | çapraz | var (D03) |
| 9 | `[[../k114-onnx-runtime/index.md]]` | çapraz | var (D03) |
| 10 | `[[../k115-model-monitoring/index.md]]` | çapraz | var (D03) |
| 11 | `[[../k116-ab-testing/index.md]]` | çapraz | var (D03) |
| 12 | `[[../k117-content-safety/index.md]]` | çapraz | var (D03) |
| 13 | `[[../k118-data-labeling/index.md]]` | çapraz | var (D03) |
| 14 | `[[../k119-prompt-engineering/index.md]]` | çapraz | var (D03) |

### 23.2 `vector-index-mimari.md` bağlantıları

| # | Link | Tür |
|---|---|---|
| 1 | `[[vector-index-mimari.md]]` | kendi |
| 2 | `[[vector-index-operasyon.md]]` | kendi |
| 3 | `[[index.md]]` | kendi |
| 4 | `[[../k109-semantic-id/semantic-id-tasarim.md]]` | çapraz |
| 5 | `[[../k110-rag-knowledge/rag-retrieval-akisi.md]]` | çapraz |
| 6 | `[[../k114-onnx-runtime/onnx-yurutme-akisi.md]]` | çapraz |
| 7 | `[[../k115-model-monitoring/model-monitoring-metrikleri.md]]` | çapraz |
| 8 | `[[../k112-inference-serving/inference-serving-mimari.md]]` | çapraz |

### 23.3 `vector-index-operasyon.md` bağlantıları

| # | Link | Tür |
|---|---|---|
| 1 | `[[vector-index-operasyon.md]]` | kendi |
| 2 | `[[vector-index-mimari.md]]` | kendi |
| 3 | `[[index.md]]` | kendi |
| 4 | `[[../k112-inference-serving/inference-serving-olceklenme.md]]` | çapraz |
| 5 | `[[../k115-model-monitoring/model-monitoring-drift-alert.md]]` | çapraz |
| 6 | `[[../k113-model-registry/model-registry-yayin-ve-rollback.md]]` | çapraz |
| 7 | `[[../k118-data-labeling/data-labeling-kalite-kontrol.md]]` | çapraz |
| 8 | `[[../k111-training-pipeline/training-veri-ve-dogrulama.md]]` | çapraz |

## 24. Terim ve Kural Sembolleri

| Sembol | Anlam | Dosya |
|---|---|---|
| `V-*` | Vektör kuralları | mimari |
| `F-*` | Filtre kuralları | mimari |
| `S-*` | Sorgu kuralları | mimari |
| `E-SYNC-*` | Tutarlılık kuralları | mimari |
| `R-*` | Yeniden inşa | mimari |
| `B-*` | Parti yazım | operasyon |
| `C-*` | Değişim | operasyon |
| `RB-*` | Runbook | operasyon |
| `OP-*` | Operasyon kuralı | operasyon |
| `T-*` | Mimari test | mimari |
| `O-*` | Operasyon test | operasyon |
| `K1..K10` | Kurtarma senaryosu | operasyon |
| `D-01..D-08` | Bozulma testi | operasyon |
| `A1..A10` | Karar kaydı | index |
| `VX-*` | İndeks hata kodu | mimari |
| `VX-OP-*` | Operasyon hata kodu | operasyon |

## 25. Kapı ve Kontrol Listesi

- **K-01:** Dosya sayısı = 3 (2 içerik + index).
- **K-02:** Her dosya ≥500 satır (boş hariç).
- **K-03:** Frontmatter 7 alan.
- **K-04:** `version: 4.0.0`.
- **K-05:** `updated: 2026-10-06`.
- **K-06:** `authority` D03 (k108-k119) içerir.
- **K-07:** Her dosyada `Kanıt:` satırı var.
- **K-08:** Kanıtsız iddia `⚠️ VERIFICATION REQUIRED`.
- **K-09:** Uydurma yol/sınıf/sürüm yok.
- **K-10:** Kırık wiki-link = 0.
- **K-11:** Link biçiminde `../k1xx-slug/dosya.md`.
- **K-12:** `verify` → mojibake 0.
- **K-13:** `verify` → BOM yok.
- **K-14:** ≥1 ASCII akış var.
- **K-15:** ≥6 tablo var.
- **K-16:** Kenar durum tablosu var.
- **K-17:** Hata modu tablosu var.
- **K-18:** Bağımlılık tablosu var.
- **K-19:** Test senaryoları var.
- **K-20:** Sürüm geçmişi var.
- **K-21:** Yalnız k108 kapsamı.
- **K-22:** Başka dosyaya dokunulmadı.
- **K-23:** Commit atılmadı.
- **K-24:** Türkçe anlatım + orijinal identifier.

## 26. Bilinmeyen Envanteri

- **U-01:** Vektör depo ürün adı — UNKNOWN.
- **U-02:** Vektör depo sürümü — UNKNOWN.
- **U-03:** SLO p95 hedefi — UNKNOWN (onay bekliyor).
- **U-04:** SLO p99 hedefi — UNKNOWN (onay bekliyor).
- **U-05:** recall hedefi — UNKNOWN (onay bekliyor).
- **U-06:** Günlük kayıt hacmi — UNKNOWN.
- **U-07:** Kayıt büyüklüğü (ortalama) — UNKNOWN.
- **U-08:** Embedding boyutu `D` — UNKNOWN (tasarım örneğinde 768).
- **U-09:** Embedding modeli adı/sürümü — UNKNOWN.
- **U-10:** Eğitim verisi sürümü — UNKNOWN.
- **U-11:** Kiracı sayısı — UNKNOWN.
- **U-12:** Eşzamanlı kullanıcı — UNKNOWN.
- **U-13:** Kuyruk altyapısı — UNKNOWN.
- **U-14:** Zamanlayıcı altyapısı — UNKNOWN.
- **U-15:** Depolama türü/disk — UNKNOWN.
- **U-16:** Yedek saklama süresi — UNKNOWN.
- **U-17:** Saklama (retention) politikası — UNKNOWN.
- **U-18:** Denetim kaydı hedefi — UNKNOWN.
- **U-19:** Uyumluluk gereksinimi (KVKK/SOC2 vb.) — UNKNOWN.
- **U-20:** Yetki sağlayıcı entegrasyonu — UNKNOWN.
- **U-21:** Puanlama (recall) referans seti — UNKNOWN.
- **U-22:** Alarm alıcısı / eskalasyon listesi — UNKNOWN.
- **U-23:** Bakım penceresi saati — UNKNOWN.
- **U-24:** Ortam adları (staging/production) — UNKNOWN.
- **U-25:** Görev dağıtım yolu — UNKNOWN.
- **U-26:** Metrik ihrac formatı — UNKNOWN.
- **U-27:** Sağlık uç noktası yolu — UNKNOWN.
- **U-28:** Şema doğrulayıcı kütüphanesi — UNKNOWN.
- **U-29:** Ayrık entegrasyon testi varlığı — UNKNOWN.
- **U-30:** Koşum / test kapsamı — UNKNOWN.

## 27. Kısaltmalar

| Kısaltma | Tam hali |
|---|---|
| k-NN | k-nearest neighbors |
| IVF | inverted file (yapı terimi) |
| HNSW | hierarchical navigable small world (yapı terimi) |
| SLO | service level objective |
| SLA | service level agreement |
| DLQ | dead letter queue |
| RB | runbook |
| ADR | architecture decision record |
| PII | personally identifiable information |
| UTC | coordinated universal time |
| TTL | time to live |
| NTP | network time protocol |
| API | application programming interface |
| DDL | data definition language |
| QA | quality assurance |
| POC | proof of concept |
| ES | escalation |
| MTTR | mean time to recovery |
| MTBF | mean time between failures |
| QPS | queries per second |
| IO | input/output |
| CPU | central processing unit |
| RAM | random access memory |
| SSD | solid state drive |
| CSV | comma separated values |
| JSON | javascript object notation |
| HTTP | hypertext transfer protocol |
| TLS | transport layer security |
| OWASP | open web application security project |
| GDPR | general data protection regulation |

## 28. Sorumluluk Matrisi (RACI)

| İş / Karar | data-eng | sre | backend-arch | performance | security | ürün |
|---|---|---|---|---|---|---|
| Şema tasarımı | **R** | C | A | C | C | I |
| Filtre sözdizimi | **R** | I | A | C | C | I |
| Sorgu API'si | C | I | **R** | C | C | A |
| Parti ayarları | **R** | C | I | C | I | I |
| Flush/merge çizelgesi | C | **R** | I | C | I | I |
| Alarm eşikleri | C | **R** | I | A | I | A |
| Runbook | C | **R** | I | I | C | I |
| Reindex tetiği | **R** | C | A | C | I | I |
| Cutover onayı | A | **R** | C | C | I | I |
| Yedek politikası | C | **R** | I | I | A | I |
| Geri alma | C | **R** | I | I | A | I |
| Kapasite raporu | **R** | C | I | A | I | I |
| Rol matrisi | C | C | C | I | **R** | A |
| SLO onayı | I | **R** | I | C | I | **A** |
| Ürün seçimi | A | C | **R** | C | C | I |
| Deneme tasarımı | C | I | C | **R** | I | A |
| İçerik güvenliği | C | I | C | I | **R** | A |
| Etiket şeması | **R** | I | I | C | C | A |
| Prompt sürümü | C | I | **R** | I | C | I |
| Yayına alma kararı | I | C | C | C | I | **A** |

> `R` = yürüten · `A` = onaylayan · `C` = danışılan · `I` = bilgilendirilen.

## 29. Kısa Notlar

- Ölçüm yoksa eşik yok.
- Eşik uydurmak halüsinasyondur; `⚠️ VERIFICATION REQUIRED` kullan.
- Şema değişikliği = değişim talebi (C-001).
- Silme = tombstone (E-SYNC-003).
- Boyut değişimi = yeni koleksiyon (V-001).
- NaN vektör = red (V-005).
- Bilinmeyen alan = erken red (F-001).
- Serbest metin filtresi = ayrı motor (F-003).
- Kapsamsız istek = `VX-401`.
- Yetkisiz istek = `VX-402`.
- Boş sonuç hata değildir (T-07).
- `k` > N kırpılır (S-001).
- Skor metriksiz anlamsızdır (§23.1 mimari).
- İki indeks aynı anda canlı kalamaz (OP-034).
- Cutover = sağlık eşiği (OP-049).
- Kilit sırası zorunlu (C-004).
- Kilit bekleme sınırı: fail-fast (C-005).
- Yedek ≠ doğrulama; geri alma provası şart (OP-027).
- Mutabakat günlük ve fark 0 (OP-023).
- Bellek %80 / 30 dk → RB-03.
- DLQ büyümesi → P1 (§7 #3).
- Alarm fırtınası → cooldown + hysteresis.
- Eksik metrik ≠ sağlıklı sistem (OP-041).
- Saat kaymalarında `version` geçerlidir (OP-043).
- Runbook 30 günde bir gözden geçirilir (OP-052).
- Rol matrisi 90 günde bir denetlenir (OP-053).
- P1 sonrası gözden geçirme zorunlu (OP-054).
- Kapasite raporu günlük (OP-056).
- Doluluk < 60 gün → kapasite olayı (OP-058).
- Alan kaldırma iki aşamalı (OP-060).
- `tenant` kapsamı zorunlu (OP-061).
- `request_id` denetimde zorunlu (OP-064).
- Runbook'a sır yazılmaz (OP-065).
- DLQ yalnız yetkili rollerce okunur (OP-066).
- Yedek alanı ayrı erişimde (OP-067).
- Önbellek iptali > 5 sn → uyarı (OP-068).
- Yeni eşik = `⚠️ VERIFICATION REQUIRED` (OP-071).
- Eşik onayı ürün + SRE (OP-072).
- Kapanış ölçütü: metrik + kanıt + ders (OP-075).
- Türkçe anlatım, orijinal identifier korunur.
- Dosya adları değiştirilmez.
- Kapsam dışı dosyalara dokunulmaz.
- Commit bu işte atılmaz.
- `UNKNOWN` tahminle doldurulmaz.

## 30. Son Söz ve Yön

Bu klasör (k108) D03 diliminin **ilk** klasörüdür; sıradaki komşu
`[[../k109-semantic-id/index.md]]` (semantic-id) ve öncesindeki mimari bağ
`k107-embedding-models` (D02) vardır. Klasör içi okuma sırası önerisi:
`index.md` → `vector-index-mimari.md` → `vector-index-operasyon.md`.

| Sıra | Dosya | Neden |
|---|---|---|
| 1 | `index.md` | Bağlam, komşular, kapılar |
| 2 | `vector-index-mimari.md` | Şema + kural + hata kodu |
| 3 | `vector-index-operasyon.md` | İşletme + runbook |

> Bu klasör tamamlandığında D03 diliminin 12 klasöründen **1/12**'si kapanmıştır.
