---
title: "Vektör İndeks Operasyonu - k108-vector-index"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D03 dilimi (k108-k119)"
updated: 2026-10-06
---

# 108b. Vektör İndeks Operasyonu - `k108-vector-index`

> Dilim: D03 (k108-k119) · Klasör no: 108 · Dosya 2/2 · Sürüm: 4.0.0 · Tarih: 2026-10-06
> Sorumlu persona: `sre-engineer` (çalıştırma), `data-engineer` (veri akışı), `performance-engineer` (ölçüm)
> Kapsadığı MD: 3 (2 içerik + index) · Bu dosya **operasyon/çalıştırma** tasarımıdır.

## 1. Genel Bakış

Bu dosya vektör indeksinin **nasıl işletildiğini** tanımlar: yazma hattı, görevler
(batcher, flush, merge, temizlik), izleme, alarm, olay müdahalesi, kapasite takibi ve
yedek/geri alma. Mimari kararlar kardeş dosyadadır: [[vector-index-mimari.md]].

**Katman bağımlılığı:** K5 (kaynak veri), K7 (kuyruk/olay), K12 (izleme/alarm),
K13 (CI/CD ve iş dağıtım), K14 (ağ/erişim). Çalıştırma birimi (düğüm/süreç adı),
sayısı ve barındırma yeri **UNKNOWN**; bu belge şartları tanımlar, kurulum tarif etmez.

Operasyonun temel ilkesi: **hiçbir operasyon mevcut indeksi geri döndürülemez
şekilde bozmaz.** Her yüksek riskli işlem (reindex, temizlik, yapı değişimi)
`önce ikincil, sonra doğrula, sonra anahtarı çevir` sırasını izler.

## 2. Klasör Özeti (K Tablosu)

| Numara | Ad | Amaç | Bağımlılık | Sorumlu persona | Kanıt | MD |
|---|---|---|---|---|---|---|
| 1 | Vektör İndeks Mimarisi | Bileşen, şema, kural, hata kodları | K4/K5/K8 | data-engineer | `⚠️ VERIFICATION REQUIRED: uygulama kodu yok` | [[vector-index-mimari.md]] |
| 2 | Vektör İndeks Operasyonu | Yazma hattı, görevler, alarm, olay, kapasite | K7/K12/K13 | sre-engineer | `⚠️ VERIFICATION REQUIRED: operasyon betiği yok` | [[vector-index-operasyon.md]] |

## 3. Kapsam ve Kapsam Dışı

### 3.1 Kapsamda

| # | Konu | Çıktı |
|---|---|---|
| 1 | Yazma hattı (olay → embedding → indeks) | Gecikme bütçesi |
| 2 | Arka plan görevleri | Çizelge + tetikleyici |
| 3 | Alarm kuralı seti | Eşik + şiddet + aksiyon |
| 4 | Olay müdahale (incident) | Adım adım runbook |
| 5 | Kapasite takibi | Formül + gözlem tablosu |
| 6 | Yedek ve geri alma | Periyot + doğrulama |
| 7 | Değişim yönetimi | Yapı/parametre değişimi |
| 8 | Yayın sonrası doğrulama | Kapı listesi |

### 3.2 Kapsam Dışı

| # | Dışarıda | Nereye |
|---|---|---|
| 1 | Embedding modeli eğitimi | `k111-training-pipeline` |
| 2 | Model yayını/geri alma kararı | `k113-model-registry` |
| 3 | Uygulama sürüm dağıtımı | K13 CI/CD |
| 4 | Fiziksel sunucu/altyapı | K0/K1 |
| 5 | Kimlik sağlayıcı | K6 güvenlik |
| 6 | A/B deney analizi | `k116-ab-testing` |

## 4. Mimari Bileşenler (operasyon görünümü)

| # | Görev | Sıklık | Sorumluluk | Bağlı kural |
|---|---|---|---|---|
| 1 | `EventConsumer` | sürekli | Olay okur, sıraya alır | E-SYNC-001 |
| 2 | `EmbeddingWorker` | sürekli | Vektör üretir (parti) | V-001..V-005 |
| 3 | `BatchWriter` | parti dolunca | Toplu upsert | E-SYNC-002 |
| 4 | `FlushScheduler` | dakikalık | Bellek → segment | - |
| 5 | `MergeScheduler` | saatlik/şişince | Segment birleşimi | - |
| 6 | `TombstoneCleaner` | günlük | Eski işaret temizliği | E-SYNC-003 |
| 7 | `MutabakatJob` | günlük | Kaynak ↔ indeks eşitliği | - |
| 8 | `HealthProbe` | saniyelik | Sağlık sinyali | - |
| 9 | `MetricExporter` | saniyelik | Metrik ihracı | - |
| 10 | `ReindexOrchestrator` | talep üzerine | İkinci indeks + geçiş | R-001..R-004 |
| 11 | `BackupJob` | günlük | Yedek + doğrulama | - |
| 12 | `CapacityReporter` | günlük | Büyüme raporu | - |

## 5. Uçtan Uca Akış

### 5.1 Yazma hattı (olaydan görününürlüğe)

```
[K5 kayıt değişimi]
   -> [olay yayını: {id,op,version,ts}]
        -> [EventConsumer] --pull--> [kuyruk]
             -> [EmbeddingWorker] --parti (B kaydı)-->
                  -> [boyut+NaN denetimi] --ret--> [DLQ / pending]
                  -> [BatchWriter] --upsert (id,version)-->
                       -> [akım/flush] -> [HOT segment]
                            -> [arama görünümü güncellenir]
                                 -> [MetricExporter: gecikme, hız, hata]
```

### 5.2 Gecikme bütçesi (bileşen bazlı)

| Aşama | Bütçe (tasarım) | Ölçüm | Aşım davranışı |
|---|---|---|---|
| Olay yayını | `⚠️ VERIFICATION REQUIRED` | olay zaman damgası farkı | kuyruk derinliği alarmı |
| Kuyrukta bekleme | `⚠️ VERIFICATION REQUIRED` | kuyruk yaşı | tüketici çoğaltma |
| Embedding üretimi | `⚠️ VERIFICATION REQUIRED` | işçi süresi | işçi artırma |
| İndeks yazımı | `⚠️ VERIFICATION REQUIRED` | upsert süresi | parti boyutu ayarı |
| Akım/flush | `⚠️ VERIFICATION REQUIRED` | flush süresi | segment baskısı |
| **Toplam (yaz-oku)** | `⚠️ VERIFICATION REQUIRED` | uçtan uca test | olay + uyarı |

### 5.3 Adım adımlar

1. Olay gelir; `version` alanı zorunlu (E-SYNC-005).
2. Aynı `(id,version)` daha işlenmişse **atlanır** (tekrar güvenli).
3. Embedding üretimi başarısızsa kayıt `pending_embedding` alınır; asla `null` yazılmaz.
4. Boyut uyuşmazlığı/NaN varsa `VX-101/VX-102` ile **DLQ**'ya gider, kuyruk tıkanmaz.
5. BatchWriter `B` kayıt ya da `T` saniye bekler; ilk koşul dolunca yazar.
6. Yazım onayı sonrası `sync_state=read_your_writes` bildirilir.
7. FlushScheduler bellekteki değişimi segmente boşaltır; arama iki segmenti tarar.
8. MergeScheduler birleşim başlatır; arama atomik görünümü korur (T-10).
9. TombstoneCleaner yalnız `epoch` eskimiş işaretleri siler.
10. MutabakatJob örneklemler; tutarsızlık varsa reindex tetiği düşünülür.
11. MetricExporter latans/hız/hata/yaş metriklerini K12'ye ihrac eder.
12. Alarm eşikleri aşılırsa runbook (§7) devreye girer.

## 6. İş Kuralları

### 6.1 Parti (batch) kuralları

- **B-001:** Parti boyutu `B` ve bekleme `T` birlikte tanımlıdır; ilk dolan kazanır.
- **B-002:** Aynı id içindeki son sürüm kazanır (parti içi normalizasyon).
- **B-003:** Parti hatasında **tamamı** değil, yalnız hatalı kayıt ayrılır (DLQ).
- **B-004:** Parti boyutu bellek sınırını aşarsa yarıya bölünür.
- **B-005:** Sıra kaybı (out-of-order) `version` ile engellenir; sıra zorunlu değildir.

### 6.2 Görev çizelgesi

| Görev | Tetik | Sıklık (varsayılan) | Eşzamanlılık | Kilit |
|---|---|---|---|---|
| `EmbeddingWorker` | kuyruk | sürekli | N işçi | yok (bölünmüş kuyruk) |
| `BatchWriter` | B/T | 1 sn / 200 kayıt | 1 | indeks yazma kilidi |
| `FlushScheduler` | zaman | 60 sn | 1 | segment kilidi |
| `MergeScheduler` | zaman + eşiği | 30 dk | 1 | indeks yazma kilidi |
| `TombstoneCleaner` | zaman | 24 sa | 1 | epoch kilidi |
| `MutabakatJob` | zaman | 24 sa | 1 | yalnız okuma |
| `BackupJob` | zaman | 24 sa | 1 | anlık görüntü |
| `CapacityReporter` | zaman | 24 sa | 1 | yalnız okuma |
| `HealthProbe` | zaman | 5 sn | çoklu | yok |
| `MetricExporter` | zaman | 10 sn | çoklu | yok |

> Çizelgedeki sıklıklar **tasarım varsayılanıdır**; `⚠️ VERIFICATION REQUIRED`
> ile işaretlidir ve üretim eşiği onayı gerektirir.

### 6.3 Değişim kuralları

- **C-001:** Yapı/parametre değişikliği önce ikincil indekste denenir.
- **C-002:** Geri alma adımı her değişimin zorunlu parçasıdır (deneme olmadan geçiş yok).
- **C-003:** Eşzamanlı iki operasyon (merge + reindex + temizlik) **yasak**; kilit sırası vardır.
- **C-004:** Kilit sırası: `reindex > merge > flush > writer > cleaner`. Öncelik çöküşü = olay.
- **C-005:** Kilit bekleme sınırı aşılırsa işlem vazgeçer (fail-fast), kilitmez.

## 7. Alarm Kuralları (runbook)

| # | Alarm | Koşul | Şiddet | İlk aksiyon | Runbook adımı |
|---|---|---|---|---|---|
| 1 | `vx_write_lag` | yaz-oku gecikmesi eşiği aşarsa | P2 | kuyruk/istenci incele | RB-01 |
| 2 | `vx_queue_age` | kuyruk yaşı eşiği aşarsa | P2 | işçi sayısını artır | RB-01 |
| 3 | `vx_dlq_growth` | DLQ boyutu artıyor | P1 | hata örneği incele | RB-02 |
| 4 | `vx_mem_high` | bellek > eşik 30 dk | P1 | segment baskısı/kuantizasyon | RB-03 |
| 5 | `vx_health_fail` | sağlık sinyali düşüyor | P1 | düğüm/replica kontrolü | RB-04 |
| 6 | `vx_recall_drop` | recall eşiği altına | P1 | yapı parametresi değişikliği | RB-05 |
| 7 | `vx_mismatch` | mutabakat tutarsızlığı | P1 | kaynak-olay hattı | RB-06 |
| 8 | `vx_merge_stuck` | birleşim > süre sınırı | P3 | kilit bekleyicileri | RB-07 |
| 9 | `vx_cache_stale` | iptal yayını gecikmesi | P3 | TTL/iptal akışı | RB-08 |
| 10 | `vx_backup_fail` | yedek başarısız | P2 | depolama/disk | RB-09 |

### 7.1 Runbook adımları

- **RB-01 (yazma gecikmesi):** 1) kuyruk derinliği/yaşı → 2) işçi hata oranı →
  3) indeks yazma latansı → 4) partiyi `B` yarıya indir → 5) gerekiyorsa tüketici ekle →
  6) kapanışta gecikme 5 dk altına inmeli.
- **RB-02 (DLQ büyümesi):** 1) son 100 DLQ kaydını sınıflandır (boyut/NaN/yetki) →
  2) üretici tarafında düzelt (V kuralları) → 3) DLQ'yu yeniden işle → 4) tekrar oranı ölç.
- **RB-03 (bellek):** 1) segment/sayaç oku → 2) `MergeScheduler`'ı hızlandır →
  3) kuantizasyon düşüncesini aç (bkz. `k114-onnx-runtime` ile karışmasın: burada indeks
  depolama biçimi) → 4) bölümlere ayırma planı → 5) eşik normale dönene kadar izle.
- **RB-04 (sağlık):** 1) düğüm durumu → 2) replica var mı → 3) trafikten çek →
  4) yeniden inşa gerekli mi (R-001) → 5) kapandıysa olay kaydı aç.
- **RB-05 (recall):** 1) referans sorgu seti çalıştır → 2) son yapı değişimi (§8) →
  3) parametreyi geri al → 4) model sürümü değişti mi (`k113-model-registry`) → 5) kapanış.
- **RB-06 (mutabakat):** 1) örnek farkları listele → 2) eksik olay mı, fazla silme mi →
  3) reindex tetiği (R-001) → 4) doğrula → 5) fark 0'a inince kapat.
- **RB-07 (merge takıldı):** 1) kilit bekleyicileri → 2) merge işlemini iptal →
  3) checkpoint'ten tekrarla → 4) tekrarlarsa eşikleri (§6.2) gözden geçir.
- **RB-08 (önbellek bayatlığı):** 1) TTL değeri → 2) iptal yayını çalışıyor mu →
  3) TTL'i kısalt → 4) etkilenen uçları listele.
- **RB-09 (yedek):** 1) depolama alanı → 2) son başarılı yedek zamanı → 3) geri alma
  provası (§9) yap → 4) çözülene kadar günlük tekrar.

## 8. Kapasite ve Büyüme

| Gözlem | Hesap | Uyarı eşiği | Aksiyon |
|---|---|---|---|
| Kayıt hızı | kayıt/saat | `⚠️ VERIFICATION REQUIRED` | plan revizyonu |
| İndeks boyutu büyümesi | GB/gün | `⚠️ VERIFICATION REQUIRED` | bölüm/kompresyon |
| Bellek kullanımı | % | %80 sürekli (30 dk) | RB-03 |
| Disk kullanımı | % | `⚠️ VERIFICATION REQUIRED` | segment baskısı |
| Segment sayısı | adet | > birleşim eşiği | merge önceliği |
| Sorgu QPS | istek/s | `⚠️ VERIFICATION REQUIRED` | okuyucu artırma |
| p95/p99 latans | ms | bütçe yok → **onay bekliyor** | - |
| Yeniden inşa süresi | dk | `⚠️ VERIFICATION REQUIRED` | bölümleme |
| Mutabakat farkı | adet | 0 | RB-06 |

**Büyüme formülü (tasarım):** `GB/gün = (kayıt/gün × (D×bayt + yapı payı)) / 10⁹`.
`kayıt/gün`, `D` ve bayt değeri **UNKNOWN** → sonuç `⚠️ VERIFICATION REQUIRED`.

## 9. Yedek ve Geri Alma

| # | Adım | Detay | Kabul |
|---|---|---|---|
| 1 | Anlık görüntü | Segment + yapı + tombstone | Sağlam imza |
| 2 | Katalog | Şema, sürüm, epoch | Okunabilir |
| 3 | Kopya | Ayrı depolama alanı | Uzak kopya |
| 4 | Sıklık | 24 saat (varsayılan) | `⚠️ VERIFICATION REQUIRED` |
| 5 | Saklama | `⚠️ VERIFICATION REQUIRED` | Politika onayı |
| 6 | Geri alma provası | Ayda bir kurgusal | Süre raporu |
| 7 | Doğrulama | Mutabakat örneği | Fark 0 |
| 8 | Kapsama | Kapsam dışı kalan olaylar | Sonradan işlenir |

**Geri alma sırası:** bakım penceresi → trafiği durdur → anlık görüntüyü yükle →
mutabakat → sağlık → trafiği aç → olay kaydı. **Ters sırada gitmek yasaktır.**

## 10. Kenar Durumlar (operasyon)

| # | Durum | Tetikleyici | Beklenen | Test |
|---|---|---|---|---|
| 1 | Olay tekrarı | Yeniden yayınlama | Idempotent atlanır | O-02 |
| 2 | Kuyruk boşalması | Bağlantı kopması | Kaldığı yerden devam | O-03 |
| 3 | Aynı anda merge + flush | Zamanlayıcı çakışması | Kilit sırası uygulanır | O-04 |
| 4 | Reindex sırasında yazma | Operasyon devrede | İki indekse birden yazar | O-05 |
| 5 | Yedek sırasında flush | Zamanlama | Atomik anlık görüntü | O-06 |
| 6 | Saat kayması | NTP sorunu | `version` korur, ts'ye güvenmez | O-07 |
| 7 | DLQ taşması | Üretici hatası | Sınırlı bellek, disk uyarısı | O-08 |
| 8 | Alarm fırtınası | Eşik çok hassas | Hysteresis + cooldown | O-09 |
| 9 | Eksik metrik | Exporter hatası | "veri yok" alarmı (sağlık değil) | O-10 |
| 10 | Bakım penceresi | Planlı iş | Pencere dışı otomatik durdurma | O-11 |

## 11. Hata Modları (operasyon)

| Kod | Semptom | Kök neden | Aksiyon | Kaçış |
|---|---|---|---|---|
| `VX-OP-01` | Yazma gecikmesi | İşçi/parti | RB-01 | Kuyruk kapasitesi |
| `VX-OP-02` | DLQ büyümesi | Kayıt hataları | RB-02 | Üretici düzeltmesi |
| `VX-OP-03` | Bellek baskısı | Büyüme | RB-03 | Bölüm/kuantizasyon |
| `VX-OP-04` | Sağlık düşüşü | Düğüm/replica | RB-04 | Trafikten çekme |
| `VX-OP-05` | recall düşüşü | Yapı/model | RB-05 | Parametre geri alma |
| `VX-OP-06` | Mutabakat farkı | Olay hattı | RB-06 | Reindex |
| `VX-OP-07` | Merge takıldı | Kilit | RB-07 | İptal + tekrar |
| `VX-OP-08` | Bayat önbellek | TTL/iptal | RB-08 | TTL kısaltma |
| `VX-OP-09` | Yedek başarısız | Depolama | RB-09 | Alternatif hedef |
| `VX-OP-10` | Kilit çöküşü | Öncelik ihlali | C-004 denetimi | Fail-fast yeniden deneme |
| `VX-OP-11` | Çift reindex | Eşzamanlı tetik | ReindexOrchestrator kilidi | İkinci işi iptal |
| `VX-OP-12` | Epoch uyuşmazlığı | Temizlik yarım | Temizliği durdur | Epoch yeniden senkron |

## 12. Bağımlılıklar

| Bağımlılık | Tip | Sürüm | Zorunlu | Not |
|---|---|---|---|---|
| Olay kaynağı (K5) | veri | UNKNOWN | evet | Olay şeması sözleşmesi |
| Kuyruk (K7) | altyapı | UNKNOWN | evet | Sürüm `⚠️ VERIFICATION REQUIRED` |
| Zamanlayıcı (cron benzeri) | altyapı | UNKNOWN | evet | Çizelge §6.2 |
| Depolama (yedek) | altyapı | UNKNOWN | evet | §9 |
| İzleme (K12) | servis | UNKNOWN | evet | Alarm aktarımı |
| CI/CD (K13) | süreç | UNKNOWN | hayır | Görev dağıtım yolu |
| Kimlik/rol (K6) | güvenlik | UNKNOWN | evet | §14 rolleri |
| Ölçüm aracı | dış | `⚠️ VERIFICATION REQUIRED` | opsiyonel | Ürün adı yazılmaz |

## 13. Ölçüm ve Kabul

| # | Kontrol | Kabul | Kapı |
|---|---|---|---|
| 1 | Yerleşim provası (staging) | S-01..S-05 geçti | yayın öncesi |
| 2 | Alarm üretimi provası | Sahte olay → alarm geldi | yayın öncesi |
| 3 | Geri alma provası | Süre + doğrulama raporu | yayın öncesi |
| 4 | Yedek doğrulama | Son 7 gün başarılı | günlük |
| 5 | Mutabakat | Fark 0 | günlük |
| 6 | Kapasite raporu | Büyüme tahmini güncel | günlük |
| 7 | Runbook geçerliliği | Adımlar uygulanabilir | 30 günde bir |
| 8 | Rol denetimi | Yetki matrisi güncel | 90 günde bir |

## 14. Güvenlik ve Uyum

| # | Tehdit | Kontrol | Durum |
|---|---|---|---|
| 1 | Yetkisiz reindex | Rol ayrımı (§15) + kilit | tasarlandı |
| 2 | Yedek açıkta kalması | Ayrı alan + erişim denetimi | tasarlandı |
| 3 | DLQ içinde hassas veri | DLQ erişimi kısıtlı, süreli saklama | tasarlandı |
| 4 | Denetim izi yokluğu | Operasyon olay kaydı | tasarlandı |
| 5 | Yetkisiz parametre değişimi | Değişim onayı + kayıt (C-001) | tasarlandı |
| 6 | Runbook'ta sır/anahtar | REDACTED politikası | kural |
| 7 | Saklama süresi belirsizliği | Politika onayı bekliyor | `⚠️ VERIFICATION REQUIRED` |

**Rol matrisi:**

| İşlem | operatör | sre | data-eng | admin |
|---|---|---|---|---|
| Sorgu | evet | evet | evet | evet |
| Elle flush/merge | hayır | evet | evet | evet |
| Reindex tetikleme | hayır | hayır | evet | evet |
| Yapı değişimi | hayır | hayır | evet | evet |
| Yedek geri alma | hayır | evet | hayır | evet |
| Rol yönetimi | hayır | hayır | hayır | evet |

## 15. Test Senaryoları (operasyon)

| ID | Senaryo | Adım | Beklenen |
|---|---|---|---|
| O-01 | Kesintiden devam | Görevi yarıda kes, başlat | checkpoint'ten devam |
| O-02 | Olay tekrarı | Aynı olayı 3× yayınla | tek yazım |
| O-03 | Kuyruk kopması | Bağlantıyı düşür | kuyruk sıfırlanmaz, yaş korunur |
| O-04 | Kilit yarışması | merge + flush eşzamanlı | kilit sırası (C-004) uygulanır |
| O-05 | Reindex sırasında yazma | reindex devrede yaz | iki indeks de güncellenir |
| O-06 | Yedek atomikliği | Yedek sırasında flush | atomik görüntü |
| O-07 | Saat kayması | ts'yi ±5 dk oynat | `version` korur |
| O-08 | DLQ baskısı | 1000 hatalı kayıt | bellek sınırlı, disk uyarısı |
| O-09 | Alarm fırtınası | 1 dk 50 ihlal | tek alarm + cooldown |
| O-10 | Eksik metrik | Exporter'ı durdur | "veri yok" alarmı |
| O-11 | Bakım penceresi | Pencere dışı tetik | otomatik red |
| O-12 | Geri alma provası | Kurgusal anlık görüntü yükle | sağlık + mutabakat geçer |
| O-13 | Rollback | Bayrağı geri çevir | trafiğe eski indeks döner |
| O-14 | Kapasite uyarısı | Simüle %85 bellek | RB-03 alarmı gelir |

## 16. Bağımlılık ve Risk Özeti

| # | Risk | Olasılık | Etki | Azaltım |
|---|---|---|---|---|
| 1 | Yazma hattı tıkanması | orta | yüksek | B-001..B-005 + işçi ölçekleme |
| 2 | DLQ sessiz büyümesi | orta | yüksek | P1 alarm (§7 #3) |
| 3 | Bellek taşması | yüksek | çok yüksek | RB-03 + bölüm |
| 4 | Reindex operasyon hatası | düşük | çok yüksek | R-001 + kapılar |
| 5 | Yedek alınamazlık | orta | yüksek | RB-09 + provası |
| 6 | Alarm yorgunluğu | orta | orta | hysteresis + şiddet hiyerarşisi |
| 7 | Eşiklerin onaysızlığı | **yüksek** | orta | Bölüm 13 kapıları |

## 17. Kanıt ve Doğrulama

- `Kanıt: .ai/architecture/k108-vector-index/vector-index-operasyon.md` (bu dosya)
- `Kanıt: ⚠️ VERIFICATION REQUIRED — repo'da bu görevlerin (batcher/merge/cleaner/reindex) çalışan betiği görülmedi`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — alarm eşikleri, SLO ve yedek periyodu onaylı değildir`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — kuyruk/planlayıcı altyapısı seçimi yapılmamıştır`
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k4-yapay-zeka/ml-infrastructure.md (L1-L493)` — ML altyapısı genel bakışı
- `Kanıt: shared/src/Api/Registry/ServiceRegistry.php` — gerçek servis kayıt sınıfı (`ServiceRegistry`)
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/k108-vector-index/vector-index-operasyon.md`

## 18. Wiki-linkler

- Aynı klasör: [[vector-index-operasyon.md]] · [[vector-index-mimari.md]] · [[index.md]]
- `[[../k112-inference-serving/inference-serving-olceklenme.md]]` — ölçekleme karşılığı
- `[[../k115-model-monitoring/model-monitoring-drift-alert.md]]` — alarm/drift köprüsü
- `[[../k113-model-registry/model-registry-yayin-ve-rollback.md]]` — geri alma ortaklığı
- `[[../k118-data-labeling/data-labeling-kalite-kontrol.md]]` — veri kalitesi girdisi
- `[[../k111-training-pipeline/training-veri-ve-dogrulama.md]]` — yeniden eğitim tetiği

## 19. Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 4.0.0 | 2026-10-06 | D03 dilimi operasyon belgesi ilk yazım | vault-writer |
| 1.0.0 | 2026-09-20 | K4 taslak (yedek kaynak) | - |

## 20. Olay ve Görev Şemaları

### 20.1 Olay kaydı (alan sözdizimi)

| Alan | Tip | Zorunlu | Açıklama |
|---|---|---|---|
| `id` | string(64) | evet | Kayıt anahtarı |
| `op` | enum | evet | `insert` / `update` / `delete` |
| `version` | int64 | evet | Artan sürüm (E-SYNC-005) |
| `ts` | datetime UTC | evet | Kaynak zaman damgası |
| `tenant` | string(32) | evet | Kiracı |
| `kind` | enum | evet | ses/söz/görsel/profil |
| `source` | string | evet | Olay kaynağı |
| `payload_hash` | string | hayır | Bütünlük kontrolü |
| `attempt` | int | hayır | Yeniden deneme sayacı |

### 20.2 Görev sağlık kontrolü

| Görev | Başarı ölçütü | Başarısızlık belirtisi | Aksiyon |
|---|---|---|---|
| `EventConsumer` | ilerleme kaydediliyor | duraklama | RB-01 |
| `EmbeddingWorker` | işlenen/oran yüksek | hata oranı artışı | RB-02 |
| `BatchWriter` | yazım onayı | reddetme artışı | RB-02 |
| `FlushScheduler` | bellek düşüşü | segment büyümesi | RB-03 |
| `MergeScheduler` | segment sayısı ↓ | artan bellek | RB-03/RB-07 |
| `TombstoneCleaner` | işaret yaşı ↓ | büyüyen tablo | RB-07 |
| `MutabakatJob` | fark 0 | fark > 0 | RB-06 |
| `HealthProbe` | sinyal taze | bayat sinyal | RB-04 |
| `BackupJob` | son yedek < 24 sa | başarısız | RB-09 |
| `CapacityReporter` | rapor üretimi | eksik rapor | manuel kontrol |

### 20.3 Günlük operasyon kontrol listesi

1. Son 24 saatte `vx_*` alarmı var mı? (varsa hangi RB çalıştı?)
2. Yaz-oku gecikmesi hedefin (onaylıysa) içinde mi?
3. DLQ boyutu ve yaşı nedir, trend nedir?
4. Bellek kullanımı 30 dakikalık pencerede %80 altında mı?
5. Son başarılı yedek zamanı 24 saatten yakın mı?
6. Mutabakat farkı sıfır mı?
7. Segment sayısı birleşim eşiğinin üzerinde bekliyor mu?
8. `pending_embedding` kuyruğu yaşı kabul edilebilir mi?
9. Kapasite raporunda öngörülen doluluk tarihi nedir?
10. Bekleyen değişim (C-001) var mı, penceresi nedir?

## 21. Eskalasyon ve İletişim

| Seviye | Koşul | Süre | Sorumlu | Eylem |
|---|---|---|---|---|
| L1 | Tek görev hatası, otomatik deneme başarılı | - | sistem | kayıt |
| L2 | Tekrarlayan hata / P3 alarm | 30 dk | operatör | RB uygula |
| L3 | P2 alarm / yazma gecikmesi | 15 dk | sre-engineer | RB + kayıt |
| L4 | P1 alarm / sağlık yok / mutabakat | 5 dk | sre + data-eng | müdahale + bildirim |
| L5 | Veri kaybı şüphesi | anlık | yetkili | geri alma (§9) + olay |

**Kayıt zorunluluğu:** L3 ve üzeri her müdahalede `olay kaydı` açılır; alanlar:
`baslangic, tespit, etki, etkilenen aralik, adimlar, sonuc, ders, sorumlu`.
Kapanış koşulu: alarm normale döndü + kanıt (metrik görseli/log) eklendi.

## 22. Metrik Sözlüğü (operasyon)

| Metrik | Birim | Kaynak | Periyot | Yorum |
|---|---|---|---|---|
| `vx_write_lag_ms` | ms | uçtan uca test | 1 dk | yaz-oku gecikmesi |
| `vx_queue_age_s` | sn | kuyruk | 1 dk | geride kalma |
| `vx_queue_depth` | adet | kuyruk | 1 dk | birikme |
| `vx_dlq_size` | adet | DLQ | 5 dk | hata birikimi |
| `vx_write_rate` | kayıt/s | yazıcı | 1 dk | yük |
| `vx_error_rate` | % | yazıcı | 1 dk | hata oranı |
| `vx_flush_s` | sn | flush | 5 dk | akım süresi |
| `vx_merge_s` | sn | merge | 5 dk | birleşim süresi |
| `vx_segment_count` | adet | indeks | 5 dk | birleşim baskısı |
| `vx_mem_pct` | % | süreç | 1 dk | bellek |
| `vx_disk_pct` | % | disk | 5 dk | doluluk |
| `vx_search_p95_ms` | ms | sorgu | 1 dk | latans |
| `vx_recall` | 0..1 | puanlama işi | günlük | kalite |
| `vx_reconcile_diff` | adet | mutabakat | günlük | tutarlılık |
| `vx_backup_age_h` | sa | yedek | 1 saat | tazelik |
| `vx_reindex_pct` | % | reindex | 5 dk | ilerleme |
| `vx_cache_hit` | % | önbellek | 1 dk | tekrar |
| `vx_pending_age_s` | sn | pending | 5 dk | bekleyen embedding |

## 23. Bilinmeyenler ve Açık Sorular

| # | Soru | Neden önemli | Kime sorulur |
|---|---|---|---|
| 1 | Hangi vektör depo ürünü kullanılacak? | Şema ve kapasite bağlanır | mimari karar |
| 2 | Yaz-oku gecikme hedefi nedir? | Alarm eşiği | iş + SRE |
| 3 | Kayıt/gün hacmi nedir? | Kapasite formülü | veri sahibi |
| 4 | Kiracı modeli var mı? | Kapsam tasarımı | ürün |
| 5 | Yedek saklama süresi? | Uyum/maliyet | güvenlik + iş |
| 6 | Embedding modeli ne sıklıkla değişir? | Reindex sıklığı | `k107` sahibi |
| 7 | Kuyruk altyapısı seçildi mi? | Gecikme bütçesi | K7 kararı |
| 8 | Denetim kaydı nereye yazılacak? | Uyum | K6/K12 |
| 9 | Onaylı SLO var mı? | Kapı #5 | ürün + SRE |
| 10 | Eski veri ne kadar saklanacak? | Bellek | iş politikası |

## 24. Sürüm Geçmişi (bu dosya)

| Sürüm | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 4.0.0 | 2026-10-06 | D03 dilimi operasyon belgesi (2. bölüm eklendi) | vault-writer |
| 1.0.0 | 2026-09-20 | K4 taslak (yedek kaynak) | - |

> **Uyarı:** Bu dosyadaki tüm sayısal eşikler tasarımdır; `⚠️ VERIFICATION REQUIRED`
> ile işaretlidir ve üretimde onaylanmadan kullanılmaz. Ölçüm yoksa hedef yoktur.

## 25. Bozulma ve Kurtarma Senaryoları

### 25.1 Senaryo tablosusu

| # | Senaryo | Belirti | Tespit | Kurtarma adımı | Tahmini süre |
|---|---|---|---|---|---|
| K1 | Düğüm kaybı | Sağlık sinyali yok | HealthProbe | Trafikten çek + replica devreye | `⚠️ VERIFICATION REQUIRED` |
| K2 | Segment bozulması | Okuma hatası | Sağlık + okuma testi | Anlık görüntüden geri alma (§9) | `⚠️ VERIFICATION REQUIRED` |
| K3 | Kuyruk veri kaybı | Olay boşluğu | Mutabakat farkı | Olay kaynağından yeniden oynat | `⚠️ VERIFICATION REQUIRED` |
| K4 | Bellek tükenmesi | Süreç kapanması | Bellek alarmı | Bölümleme + kuantizasyon | `⚠️ VERIFICATION REQUIRED` |
| K5 | Disk dolması | Yazım reddi | Disk alarmı | Segment baskısı + alan açma | `⚠️ VERIFICATION REQUIRED` |
| K6 | Çift indeks | İki indeks aynı anda canlı | ReindexOrchestrator | Trafik anahtarını tek indekse al | dakikalar |
| K7 | Epoch kayması | Temizlik yarım | Epoch denetimi | Temizliği durdur + epoch yeniden senkron | `⚠️ VERIFICATION REQUIRED` |
| K8 | Kilit çöküşü | İşler askıda | Kilit bekleme metriği | Fail-fast + otomatik tekrar | saniyeler |
| K9 | Alarm fırtınası | Yüzlerce bildirim | Alarm hacmi | Cooldown + hysteresis | dakikalar |
| K10 | Yedek bozuk | Geri alma provası hata | Aylık prova | Alternatif yedeğe geç | `⚠️ VERIFICATION REQUIRED` |

### 25.2 Kurtarma öncelik sırası

1. **Okunabilirlik** — sorgu yolu ayakta kalsın (bozuk düğüm trafikten çekilir).
2. **Doğruluk** — mutabakat farkı büyürse yazma hattı durdurulabilir (kaynak K5'te durur).
3. **Bütünlük** — geri alma, atomik anlık görüntü üzerinden yapılır.
4. **Performans** — yalnız ayakta kalan üç önceki kapandıktan sonra ele alınır.
5. **Kapasite** — büyüme işi en son öncelenir; kesinti yaratmaz.

### 25.3 Bozulma testleri (kabul için)

| ID | Test | Yöntem | Beklenen |
|---|---|---|---|
| D-01 | Düğümü kapat | Süreci durdur | Trafik replica'ya, veri kaybı yok |
| D-02 | Diski doldur | Kaba dosya | Yazım reddi + alarm, okuma sürer |
| D-03 | Kuyruğu kes | Bağlantı yok | Yaş artışı, kuyruk kaybı yok |
| D-04 | Belleği kıs | Sınır uygula | RB-03 alarmı, kontrollü baskı |
| D-05 | Yedeği boz | Anlık görüntüyü sil | Sonraki yedek + alarm |
| D-06 | Kilit sıkıştır | Eşzamanlı operasyon | C-004 ihlali tespiti |
| D-07 | Saati kaydır | Zaman atlatma | `version` koruması çalışır |
| D-08 | Eşiği kır | İhlal simülasyonu | Alarm + runbook gelir |

## 26. Bilgi Topluluğu (bu klasörün sorularına yanıtlar)

| Soru | Yanıt | Kaynak |
|---|---|---|
| İndeks kaynak veri midir? | Hayır; türetilmiştir | [[vector-index-mimari.md]] §1 |
| Silme nasıl yapılır? | Tombstone + temizlik görevi | [[vector-index-mimari.md]] §6.4 |
| Boyut değişince ne olur? | Yeni koleksiyon + reindex | [[vector-index-mimari.md]] R-001 |
| Filtre önce mi vektör mü? | Seçiciliğe göre (§5.3) | [[vector-index-mimari.md]] §5.3 |
| Yazma hattı gecikir ise? | RB-01 | bu dosya §7.1 |
| Bellek artarsa? | RB-03 → bölüm/kuantizasyon | bu dosya §7.1 |
| Yedek nasıl doğrulanır? | Mutabakat örneği | bu dosya §9 |
| Ürün seçimi ne zaman? | Kanıt ve deneme sonrası | `⚠️ VERIFICATION REQUIRED` |
| SLO hedefleri? | Onay bekliyor | bu dosya §13 |
| Kimler reindex tetikler? | `data-eng` / `admin` | bu dosya §14 |

## 27. Operasyon Kuralları (`OP-*` — kısa liste)

- **OP-001:** Bakım penceresi dışında otomatik işler durdurulamaz.
- **OP-002:** Manuel flush yalnız `sre`/`data-eng` rolüyle çalıştırılır.
- **OP-003:** Manuel merge işlemi kilit sırasına uyar (C-004).
- **OP-004:** Bellek %80 üstünde 30 dk kalırsa RB-03 otomatik çağrılır.
- **OP-005:** DLQ boyutu her saate raporlanır.
- **OP-006:** DLQ kayıtları 7 günden eskiyse arşivlenir.
- **OP-007:** Yeniden işleme (retry) en fazla 3 kez denenir.
- **OP-008:** Üçüncü deneme başarısızsa kayıt DLQ'ya gider.
- **OP-009:** Olay tekrarı güvenlidir (idempotent).
- **OP-010:** Olay sırası zorunlu değildir; `version` korur.
- **OP-011:** `version` geri gelirse yazım düşer ve olaya yazılır.
- **OP-012:** Silme olayı yalnız `delete` op ile taşınır.
- **OP-013:** `pending_embedding` kayıtları 15 dk sonra uyarır.
- **OP-014:** `pending_embedding` 60 dk kalırsa P3 alarm.
- **OP-015:** Boyut uyuşmazlığı üreten taraf düzeltilmeden tekrar denenmez.
- **OP-016:** NaN/Inf üreten taraf düzeltilmeden tekrar denenmez.
- **OP-017:** Parti yarıya bölme bellek uyarısında tetiklenir.
- **OP-018:** Flush süresi 3× normalse alarm verilir.
- **OP-019:** Merge süresi sınırı aşılırsa RB-07.
- **OP-020:** Segment sayısı 16'ya ulaşırsa merge önceliği yükselir.
- **OP-021:** Tombstone temizliği yalnız tam epoch'ta çalışır.
- **OP-022:** Epoch değişimi reindex tamamlanmadan yapılmaz.
- **OP-023:** Mutabakat farkı > 0 ise yazma hattı incelenir.
- **OP-024:** Mutabakat farkı 3 gün art arda > 0 ise P2.
- **OP-025:** Mutabakat farkı 1000'i geçerse P1.
- **OP-026:** Yedek 24 saatten eskiyse P2 alarm.
- **OP-027:** Yedek geri alma provası ayda bir yapılır.
- **OP-028:** Geri alma provası başarısızsa P1.
- **OP-029:** Anlık görüntü alınırken yazma durdurulmaz.
- **OP-030:** Anlık görüntü atomik alınır.
- **OP-031:** Yapı değişimi kayıt altına alınır (C-001).
- **OP-032:** Yapı değişimi ikincilde denenmeden canlıya geçmez.
- **OP-033:** Yapı değişimi geri alma adımı içermiyse reddedilir.
- **OP-034:** İki reindex eşzamanlı olamaz (OP-035 istisnası hariç).
- **OP-035:** Acil durumda ikinci reindex yalnız `admin` onayıyla açılır.
- **OP-036:** Kilit bekleme 30 sn'yi aşarsa işlem vazgeçer.
- **OP-037:** Vazgeçen iş kilit kırma yapmaz.
- **OP-038:** Alarm cooldown süresince tekrar bildirim yapılmaz.
- **OP-039:** Hysteresis alt/üst eşikleri ayrı tanımlıdır.
- **OP-040:** Şiddet yükseltmesi (P3→P1) cooldown'ı sıfırlar.
- **OP-041:** Eksik metrik "sağlık yok" alarmı üretir.
- **OP-042:** Metrik sağlığı alarmı ile veri sağlığı alarmı ayrıdır.
- **OP-043:** Saat kaymasında ts'ye değil `version`'a bakılır.
- **OP-044:** Sağlıksız düğüm 2 kez üst üste başarısızsa trafikten çekilir.
- **OP-045:** Trafikten çekme otomatik, geri ekleme elle onayla olur.
- **OP-046:** Geri ekleme öncesi sağlık sinyali taze olmalıdır.
- **OP-047:** Reindex ilerlemesi %10 ara raporlar üretir.
- **OP-048:** Reindex doğrulama mutabakat örneğiyle yapılır.
- **OP-049:** Cutover öncesi sağlık eşiği aşılmış olmalıdır.
- **OP-050:** Cutover sonrası eski indeks 24 sa saklanır.
- **OP-051:** Eski indeks 24 sa sonra yalnız `admin` onayıyla silinir.
- **OP-052:** Runbook her 30 günde bir gözden geçirilir.
- **OP-053:** Rol matrisi 90 günde bir denetlenir.
- **OP-054:** Her P1 olaydan sonra gözden geçirme toplantısı yapılır.
- **OP-055:** Gözden geçirme çıktıları §21 biçiminde kaydedilir.
- **OP-056:** Kapasite raporu günlük üretilir.
- **OP-057:** Kapasite raporu 30 gün öngörü içerir.
- **OP-058:** Öngörü doluluk 60 gün altındaysa kapasite olayı açılır.
- **OP-059:** Yeni alan ekleme şema değişimidir (C-001).
- **OP-060:** Alan kaldırma iki aşamalıdır: önce pasif, sonra temizleme.
- **OP-061:** `tenant` kapsamı tüm sorgularda zorunludur.
- **OP-062:** Kapsam atlanan istek `VX-401` ile reddedilir.
- **OP-063:** Yetki matrisi dışı istek `VX-402` ile reddedilir.
- **OP-064:** Denetim kaydında `request_id` zorunludur.
- **OP-065:** Hassas veri (anahtar/sır) runbook'a yazılmaz (REDACTED).
- **OP-066:** DLQ içerikleri yalnız yetkili rollerce okunur.
- **OP-067:** Yedek alanı ayrı erişim kontrolüne tabidir.
- **OP-068:** Önbellek iptali yayını 5 sn'yi aşarsa uyarı.
- **OP-069:** Bayat önbellek bulgusu TTL'i kısaltır.
- **OP-070:** Ölçüm yoksa eşik yok; eşik varsayımı yasaktır.
- **OP-071:** Her yeni eşik `⚠️ VERIFICATION REQUIRED` işaretini taşır.
- **OP-072:** Eşik onayı ürün + SRE ortak kararıdır.
- **OP-073:** Runbook adımları değiştirilirse test edilir (§15).
- **OP-074:** Olay kaydı kapanmadan alarm kabul edilmez.
- **OP-075:** Çıkış (kapanış) ölçütü: metrik + kanıt + ders kaydı.

## 28. Hızlı Referans Kartı

| İhtiyaç | Komut/adım | Kaynak |
|---|---|---|
| Sağlık sor | HealthProbe çıktısı | §4 |
| Metrik oku | `vx_*` metrikleri | §22 |
| Alarm listesi | §7 tablosu | bu dosya |
| RB uygula | §7.1 | bu dosya |
| Yedek geri al | §9 sırası | bu dosya |
| Reindex | `ReindexOrchestrator` + OP-047..051 | §4 |
| Rol sor | §14 matrisi | bu dosya |
| Kapasite | §8 formülü | bu dosya |
| Değişiklik yap | C-001..C-003 | §6.3 |
| Olay kaydı | §21 alanları | bu dosya |
