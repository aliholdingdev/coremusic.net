---
title: "Anlamlı Kimlik Uyum Stratejisi - k109-semantic-id"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D03 dilimi (k108-k119)"
updated: 2026-10-06
---

# 109b. Anlamlı Kimlik Uyum Stratejisi - `k109-semantic-id`

> Dilim: D03 (k108-k119) · Klasör no: 109 · Dosya 2/2 · Sürüm: 4.0.0 · Tarih: 2026-10-06
> Sorumlu persona: `data-engineer`, `backend-architect`, `sre-engineer`
> Bu dosya **göç/uyum** (migration & compatibility) stratejisini tanımlar; kimlik tasarımı kardeş dosyadır: [[semantic-id-tasarim.md]]

## 1. Genel Bakış

Kimlik şeması değiştiğinde (alfabe, checksum sürümü, namespace, boyut) veya kayıt
kaynağı birleştiğinde (merge) **uyum** gerekir. Bu dosya: göç planı, çift yazma/okuma,
kontrollü geçiş, geri alma, indeks eş güdümü ve kapanış ölçütlerini tanımlar.

**Katman bağımlılığı:** K5 (kaynak + alias), K4 (`k108-vector-index` yeniden eşleme),
K9 (API yanıt biçimi), K12 (ölçüm/alarm), K13 (iş dağıtım). Göç **kod dağıtımı**
değil, **veri + okuma yolu değişimidir**; bu yüzden kapılar (§11) zorunludur.

Üç göç sınıfı ayrılır:
1. **Şema göçü** — kimlik formatı değişir (ör. `id_ver` 1 → 2).
2. **Birleştirme (merge)** — iki kayıt aynı varlığa bağlanır.
3. **Bölme (split)** — tek kayıt ikiye ayrılır (nadir, yüksek risk).

## 2. Klasör Özeti (K Tablosu)

| Numara | Ad | Amaç | Bağımlılık | Sorumlu persona | Kanıt | MD |
|---|---|---|---|---|---|---|
| 1 | Anlamlı Kimlik Tasarımı | Şema, üretim, çakışma, gizlilik | K5, K4, K9 | data-engineer, security | `⚠️ VERIFICATION REQUIRED: kimlik kodu yok` | [[semantic-id-tasarim.md]] |
| 2 | Anlamlı Kimlik Uyum Stratejisi | Göç, çift yazma/okuma, geri alma, kapanış | K5, K4, K12 | data-engineer, sre-engineer | `⚠️ VERIFICATION REQUIRED: göç betiği yok` | [[semantic-id-uyum-stratejisi.md]] |

## 3. Kapsam ve Kapsam Dışı

### 3.1 Kapsamda

| # | Konu | Çıktı |
|---|---|---|
| 1 | Göç fazları ve kapıları | faz listesi |
| 2 | Çift yazma / çift okuma kuralları | kural seti (`SM-*`) |
| 3 | Kontrollü geçiş (canary) | yüzdesel trafik |
| 4 | Geri alma (rollback) | adımlar |
| 5 | İndeks eş güdümü | `k108` reindex tetiği |
| 6 | Mutabakat | günlük denetim |
| 7 | Kapanış ölçütleri | kapı listesi |
| 8 | Olay müdahalesi | runbook |

### 3.2 Kapsam Dışı

| # | Dışarıda | Nereye |
|---|---|---|
| 1 | Kimlik üretim algoritması | [[semantic-id-tasarim.md]] §6 |
| 2 | Vektör indeks iç yapısı | `k108-vector-index` |
| 3 | SQL DDL | K5 / `k120-mysql-18-database` |
| 4 | Uygulama sürüm dağıtımı | K13 |
| 5 | Deney analizi | `k116-ab-testing` |
| 6 | Kimlik doğrulama protokolü | `k144-authentication-jwt` |

## 4. Mimari Bileşenler

| # | Bileşen | Sorumluluk | Faz |
|---|---|---|---|
| 1 | `MigrationPlanner` | Faz + kapsam + iş listesi | plan |
| 2 | `DualWriter` | Eski + yeni kimlik yazımı | 2 |
| 3 | `DualReader` | Okumada eski→yeni çözümleme | 3 |
| 4 | `BackfillJob` | Toplu geçmiş kaydın dönüştürülmesi | 3 |
| 5 | `RefScanner` | Eski kimliğe referans taraması | 5 |
| 6 | `ConsistencyJob` | K5 ↔ indeks ↔ alias mutabakatı | sürekli |
| 7 | `RollbackController` | Ters yönlendirme/geri alma | her an |
| 8 | `ProgressMeter` | `sem_migration_pct` ölçümü | sürekli |
| 9 | `GateKeeper` | Kapı denetimi (§11) | geçiş |
| 10 | `RefSetStore` | Referans seti (regresyon) | sürekli |

## 5. Uçtan Uca Akış (göç fazları)

```
[FAZ 0: hazırlık]
   - referans seti, geri alma provası, ölçüm tanımları
        |
        v
[FAZ 1: şema hazır] --> alias tablosu + id_ver alanı eklendi (eski yazımda)
        |
        v
[FAZ 2: ÇİFT YAZMA] --> her kayıt hem eski hem yeni kimlikle yazılır
        |
        v
[FAZ 3: DOLDURMA + ÇİFT OKUMA] --> BackfillJob + DualReader
        |                              |
        | ilerleme (pct)                | eski referanslar çözülür
        v                              
[FAZ 4: KONTROLLÜ GEÇİŞ] --> %5 -> %25 -> %50 -> %100 (trafik)
        |                        |
        | geri alma tetiği hazır  | metrik kapıları
        v                        
[FAZ 5: REFERANS TARAMASI] --> RefScanner: eski kimlik referansı = 0
        |
        v
[FAZ 6: KAPANIŞ] --> eski okuma kapanır (7 gün arkaik pencere) --> arşiv
        |
        v
[FAZ 7: İZLEME] --> 404/yanlış yönlendirme oranı, mutabakat
```

### 5.1 Adımlar (faz bazlı)

1. Faz 0: referans seti üretilir, geri alma provası yapılır, ölçüm eşikleri onaylanır.
2. Faz 1: `alias` tablosu ve `id_ver` alanı açılır; yazma yolu henüz eskidir.
3. Faz 2: `DualWriter` devreye girer; her yazım iki kimlik üretir.
4. Faz 2 kontrolü: iki yazım eşitliği denetlenir (mutabakat örneği).
5. Faz 3: `BackfillJob` geçmiş kayıtları dönüştürür (checkpoint'li).
6. Faz 3: `DualReader` okumada eski kimliği yeniye çözer.
7. Faz 4: trafik yüzdeleriyle kontrollü geçiş; her adımda kapı (§11) denetlenir.
8. Faz 4: metrik kötüleşmesi → otomatik geri alma (`RollbackController`).
9. Faz 5: `RefScanner` kod/konfigürasyon/veri taraması yapar; eski referans = 0 şart.
10. Faz 6: eski okuma kapatılır (önce 7 gün arkaik pencere, sonra tam kapanış).
11. Faz 7: 404/yanlış yönlendirme oranı ve mutabakat izlenir.
12. Kapanış: olay kaydı + ders kaydı + kapı raporu arşivlenir.

## 6. İş Kuralları (`SM-*`)

- **SM-001:** Göç asla tek adımda (big bang) yapılmaz; fazlar zorunludur.
- **SM-002:** Her fazın geri alma adımı vardır; olmayan faz başlatılamaz.
- **SM-003:** Çift yazma döneminde **iki kimlik de geçerlidir**.
- **SM-004:** Çift yazma hataları (birinin başarısızlığı) P2 alarm üretir.
- **SM-005:** Çift okuma, çözümleme başarısızsa sessizce 404 üretmez; `SEM-105` verir.
- **SM-006:** Backfill işlemi checkpoint'lidir; kesinti sonra aynı yerden devam.
- **SM-007:** Backfill esnasında canlı yazım yapısı korunur (çift yazma).
- **SM-008:** Backfill tekrarı idempotenttir (aynı kayıt iki kez dönüştürülmez).
- **SM-009:** Trafik geçiş adımları: %5 → %25 → %50 → %100 (sıra zorunlu).
- **SM-010:** Her geçiş adımında kapı denetimi yapılır (GateKeeper).
- **SM-011:** Kapı geçemezse geçiş **durur**, geri alma tetiklenir.
- **SM-012:** Geri alma, çift yazmayı kapatıp eski yolu açar (tek işlem).
- **SM-013:** Geri alma sonrası alias kayıtları korunur (ikinci geçiş için).
- **SM-014:** Geri alma sayısı ölçülür; 2'den fazla geri alma → tasarım gözden geçirme.
- **SM-015:** İndeks (`k108`) eş güdümü zorunludur: yeni kimlik indekse de yazılır.
- **SM-016:** İndeks eski kimlikle sorgulanabilir kalmalıdır (pencere içinde).
- **SM-017:** İndeks reindex tetiği göç olayıdır (`SEM-402`).
- **SM-018:** Mutabakat günlük çalışır; fark 0 kabul.
- **SM-019:** Mutabakat farkı > 0 üç gün → P2; > 1000 → P1.
- **SM-020:** Referans taraması yalnız veri değil; kod + konfigürasyon + doküman kapsar.
- **SM-021:** Tarama sonucu 0 değilse kapanış yapılmaz.
- **SM-022:** Kapanış sonrası eski okuma 7 gün açık kalır (arkaik pencere).
- **SM-023:** Arkaik pencere sonunda eski okuma kapatılır (`SEM-103`).
- **SM-024:** Kapanışta alias tablosu **saklanır**, silinmez (geriye dönük iz).
- **SM-025:** Merge (birleştirme) işleminde birincil kimlik seçilir, diğerleri alias olur.
- **SM-026:** Merge'de kaynak kayıtlar silinmez; `retired` işaretlenir.
- **SM-027:** Merge geri alma süresi 7 gündür; sonrasında yalnız elle onay.
- **SM-028:** Split (bölme) yalnız onayla ve manuel planda yapılır.
- **SM-029:** Split'te hangi kaydın hangi kimliği aldığı belgelenir.
- **SM-030:** Göç sırasında performans ölçülür (p95 etkisi).
- **SM-031:** p95 etkisi bütçeyi aşarsa göç yavaşlatılır.
- **SM-032:** Göç işi tek örnektir; eşzamanlılık yasak.
- **SM-033:** Göç işi durdurulabilir; durdurma checkpoint yazar.
- **SM-034:** Göç ilerlemesi `sem_migration_pct` olarak ölçülür.
- **SM-035:** İlerleme 6 saat boyunca artmıyorsa alarm (duraklama).
- **SM-036:** Göç sırasında hata oranı eşik aşarsa otomatik durma.
- **SM-037:** Geri alma tetiği hem otomatik hem elle çalıştırılabilir.
- **SM-038:** Elle geri alma `admin` onayı ister.
- **SM-039:** Her geçiş adımı olay kaydına yazılır (denetim).
- **SM-040:** Göç kapanış raporu üretilir (kapı + metrik + ders).

## 7. Veri Modeli (göç alanları)

### 7.1 Göç durumu tablosu

| Alan | Tip | Açıklama | Zorunlu |
|---|---|---|---|
| `migration_phase` | tinyint | 0..7 faz | evet |
| `migration_started_at` | datetime | başlangıç UTC | evet |
| `migration_pct` | decimal(5,2) | ilerleme | evet |
| `dual_write` | bool | çift yazma bayrağı | evet |
| `dual_read` | bool | çift okuma bayrağı | evet |
| `last_checkpoint` | string | geri devam noktası | evet |
| `rollback_count` | smallint | geri alma sayısı | evet |
| `gate_pass` | bool | son kapı sonucu | evet |

### 7.2 Kayıt düzeyi göç işaretleri

| Alan | Tip | Açıklama |
|---|---|---|
| `id_new` | string(32) | yeni kimlik |
| `id_old` | string(32) | eski kimlik |
| `id_state` | enum | `pending/converted/verified/failed` |
| `converted_at` | datetime | dönüşüm zamanı |
| `verify_hash` | string(64) | iki yazım eşitlik kontrolü |
| `fail_reason` | string | `SEM-*` kodu |

### 7.3 Göç özeti (rapor)

| Sütun | Kaynak | Yorum |
|---|---|---|
| Toplam kayıt | K5 | planlama |
| Dönüştürülen | `id_state=converted` | ilerleme |
| Doğrulanan | `id_state=verified` | kalite |
| Başarısız | `id_state=failed` | hata (SM-005) |
| Bekleyen | `id_state=pending` | kuyruk |
| Eski referans | RefScanner | kapanış kapısı |
| Mutabakat farkı | ConsistencyJob | bütünlük |
| Rollback sayısı | göç durumu | risk |

## 8. Kenar Durumlar

| # | Durum | Tetikleyici | Beklenen | Test |
|---|---|---|---|---|
| 1 | Göç ortasında kesinti | Süreç kapanması | checkpoint'ten devam | M-01 |
| 2 | Çift yazma yarım | Bir hedef hatalı | P2 alarm + telafi | M-02 |
| 3 | Eski referans kod içinde | RefScanner | kapanış bloke | M-03 |
| 4 | Aynı anda merge + göç | Operasyon yarışması | kilit/red | M-04 |
| 5 | Geri alma sonrası tekrar geçiş | İki kez deneme | alias korunur | M-05 |
| 6 | İndeks geride kalır | Eşgüdüm kopması | `SEM-401/402` | M-06 |
| 7 | Backfill + canlı yazım çakışması | Aynı kayıt | çift yazma kazanır | M-07 |
| 8 | Ters alias gerekmesi | Geri alma | tek halka korunur | M-08 |
| 9 | Arkaik pencere içinde istemci | Eski istemci | yönlendirme/uyarı | M-09 |
| 10 | Kapanıştan sonra istemci | Çok eski | `SEM-103` net hata | M-10 |
| 11 | İlerleme takıldı | Bozuk kayıt | 6 sa alarm (SM-035) | M-11 |
| 12 | Mutabakat farkı | Eşleme eksik | P2/P1 (SM-019) | M-12 |
| 13 | Kayıt birleştirme | Aynı varlık | birincil + alias | M-13 |
| 14 | Kayıt bölme | İş kuralı | onay + belge | M-14 |
| 15 | Ölçüm yokluğu | Exporter | "veri yok" alarmı | M-15 |

## 9. Hata Modları

| Kod | Semptom | Kök neden | Aksiyon | Kaçış |
|---|---|---|---|---|
| `SEM-105` | Çift okuma çözümü başarısız | Alias eksik | Tarama + telafi | Manuel eşleme |
| `SEM-403` | Göç takıldı | Bozuk kayıt | Kaydı atla + iş listesi | Sonradan telafi |
| `SEM-404` | İlerleme durdu | Kilit/kayıt | Duraklama alarmı | SM-035 |
| `SEM-405` | Kapı başarısız | Metrik eşiği | Geçiş durdu | Geri alma |
| `SEM-406` | Eşzamanlı göç | İki iş | İkinciyi reddet | Tek örnek |
| `SEM-407` | Checkpoint bozuk | Kesinti | Sıfırdan faz | Yedek checkpoint |
| `SEM-408` | Backfill çakışması | Aynı kayıt | Çift yazma önceliği | Tekrar |
| `SEM-409` | RefScanner hatası | Tarama kapsamı | Kapsam genişlet | Manuel denetim |
| `SEM-410` | Tarama temiz değil | Eski referans | Kapanış reddedilir | Düzelt + tekrar |
| `SEM-411` | Geri alma başarısız | Bozuk alias | P1 olay | Yedekten geri alma |
| `SEM-412` | p95 bozulması | Göç yükü | Yavaşlat | Adım küçültme |
| `SEM-413` | Mutabakat farkı | Eşleme | P2/P1 | Göç tekrarı |
| `SEM-414` | İndeks kaybı | Reindex gecikmesi | `SEM-402` tetiği | `k108` R-001 |
| `SEM-415` | Rapor üretilemedi | Ölçüm | Manuel rapor | Ölçüm onarımı |

## 10. Bağımlılıklar

| Bağımlılık | Tip | Sürüm | Zorunlu | Not |
|---|---|---|---|---|
| K5 (kaynak + alias) | veri | UNKNOWN | evet | göçün taşıyıcısı |
| `k108-vector-index` | servis | UNKNOWN | evet | eşleme/reindex |
| K13 (iş dağıtımı) | süreç | UNKNOWN | evet | Backfill işi |
| K12 (ölçüm) | altyapı | UNKNOWN | evet | kapı metrikleri |
| K9 (API) | servis | UNKNOWN | evet | yanıt `id_source` alanı |
| K6 (denetim) | güvenlik | UNKNOWN | evet | olay kaydı |
| Referans seti | veri | UNKNOWN | evet | regresyon |
| Yedek/depolama | altyapı | UNKNOWN | evet | geri alma |

## 11. Kapılar (geçiş kriterleri)

| # | Kapı | Faz arası | Ölçüt | Durum |
|---|---|---|---|---|
| K1 | Geri alma provası | 0 → 1 | prova başarılı + süre raporu | `⚠️ VERIFICATION REQUIRED` |
| K2 | Çift yazma eşitliği | 2 → 3 | mutabakat farkı 0 | `⚠️ VERIFICATION REQUIRED` |
| K3 | Backfill tamam | 3 → 4 | dönüştürülen = toplam | `⚠️ VERIFICATION REQUIRED` |
| K4 | Hata oranı | 4 (adım) | eşik altı | `⚠️ VERIFICATION REQUIRED` |
| K5 | p95 etkisi | 4 (adım) | bütçe içi | `⚠️ VERIFICATION REQUIRED` |
| K6 | Mutabakat | 4 → 5 | fark 0 | `⚠️ VERIFICATION REQUIRED` |
| K7 | Referans temizliği | 5 → 6 | tarama = 0 | `⚠️ VERIFICATION REQUIRED` |
| K8 | Arkaik pencere | 6 → 7 | 7 gün tamam | `⚠️ VERIFICATION REQUIRED` |
| K9 | 404/yönlendirme | 7 | eşik altı | `⚠️ VERIFICATION REQUIRED` |
| K10 | Kapanış raporu | kapanış | rapor + ders kaydı | zorunlu |

> Tüm kapı eşikleri `⚠️ VERIFICATION REQUIRED`; onay ürün + SRE + veri sahibidir.

## 12. Ölçüm ve Kabul

| # | Metrik | Hedef | Periyot |
|---|---|---|---|
| 1 | `sem_migration_pct` | 100% | saatlik |
| 2 | Mutabakat farkı | 0 | günlük |
| 3 | Eski referans sayısı | 0 | faz 5+ |
| 4 | `SEM-105` sayısı | 0 | günlük |
| 5 | Rollback sayısı | ≤ 2 | göç |
| 6 | p95 etkisi | bütçe içi | faz 4 |
| 7 | Hata oranı | eşik altı | faz 4 |
| 8 | 404 oranı | `⚠️ VERIFICATION REQUIRED` | faz 7 |
| 9 | Duraklama (6 sa) | 0 | sürekli |
| 10 | Kapanış raporu | üretildi | faz 7 |

## 13. Güvenlik ve Uyum

| # | Tehdit | Kontrol | Durum |
|---|---|---|---|
| 1 | Göç sırasında veri sızıntısı | Çift yazma yalnız iç yollar | tasarlandı |
| 2 | Yetkisiz geri alma | `admin` onayı + kayıt | tasarlandı |
| 3 | Denetim izi eksikliği | SM-039 zorunlu olay kaydı | tasarlandı |
| 4 | Alias üzerinden eski veriye erişim | Arkaik pencere + yetki | tasarlandı |
| 5 | Log/rapor sızıntısı | Maskeleme (ID-030) | tasarlandı |
| 6 | Saklama/retention | Politika tanımsız | `⚠️ VERIFICATION REQUIRED` |
| 7 | Uyumluluk gereksinimi | KVKK/SOC2 kapsamı belirsiz | `⚠️ VERIFICATION REQUIRED` |

## 14. Riskler ve Teknik Borç

| # | Risk | Olasılık | Etki | Azaltım |
|---|---|---|---|---|
| 1 | Göç yarım kalması | yüksek | yüksek | faz + checkpoint |
| 2 | Geri alınamama | orta | çok yüksek | Kapı K1 zorunlu |
| 3 | İndeks tutarsızlığı | orta | yüksek | SM-015..017 + mutabakat |
| 4 | Eski referans kalması | yüksek | orta | RefScanner + kapı K7 |
| 5 | Performans bozulması | orta | orta | SM-030/031 + yavaşlatma |
| 6 | Eşzamanlı iş yarışması | orta | yüksek | SM-032 tek örnek |
| 7 | Kapı eşiklerinin onaysızlığı | **yüksek** | yüksek | §11 onay süreci |
| 8 | Arkaik pencere belirsizliği | orta | orta | SM-022 sabit + onay |

## 15. Test Senaryoları

| ID | Senaryo | Adım | Beklenen |
|---|---|---|---|
| M-01 | Kesinti + devam | Backfill'i yarıda kes | checkpoint'ten devam |
| M-02 | Çift yazma hatalı | Bir hedefe yazma | P2 alarm + telafi |
| M-03 | Kodda eski referans | Ekle + tarama | kapanış bloke |
| M-04 | Eşzamanlı iş | İki göç başlat | ikinciyi reddet |
| M-05 | Geri alma + tekrar | Geri al + yeniden geç | alias korunur |
| M-06 | İndeks geride | Eşgüdümü kes | `SEM-401/402` |
| M-07 | Aynı kayıt | Backfill + canlı | çift yazma kazanır |
| M-08 | Ters alias | Geri alma | tek halka |
| M-09 | Eski istemci | Arkaik pencerede istek | yönlendirme |
| M-10 | Kapanış sonrası | Eski id iste | `SEM-103` |
| M-11 | Duraklama | İlerlemeyi dondur | 6 sa alarm |
| M-12 | Mutabakat farkı | Eşlemeyi sil | P2 alarm |
| M-13 | Merge | İki kaydı birleştir | birincil + alias |
| M-14 | Split | Kaydı böl | onay + belge |
| M-15 | Ölçüm yokluğu | Exporter'ı durdur | "veri yok" alarmı |
| M-16 | Kapı reddi | Eşiği ihlal | geçiş durdu |
| M-17 | p95 bozulması | Yük simülasyonu | yavaşlatma |
| M-18 | Kapanış raporu | Faz 7 sonu | rapor + ders |

## 16. Kanıt ve Doğrulama

- `Kanıt: .ai/architecture/k109-semantic-id/semantic-id-uyum-stratejisi.md` (bu dosya)
- `Kanıt: ⚠️ VERIFICATION REQUIRED — göç betiği/plan işi repo'da görülmedi`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — kapı (K1..K10) eşikleri onaylı değildir`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — arkaik pencere süresi (7 gün) tasarımdır`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — retention/uyum gereksinimleri belirsizdir`
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k4-yapay-zeka/ml-infrastructure.md (L1-L493)` — altyapı bağlamı
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/k109-semantic-id/semantic-id-uyum-stratejisi.md`

## 17. Wiki-linkler

- Aynı klasör: [[semantic-id-uyum-stratejisi.md]] · [[semantic-id-tasarim.md]] · [[index.md]]
- `[[../k108-vector-index/vector-index-operasyon.md]]` — reindex/operasyon ortaklığı
- `[[../k108-vector-index/vector-index-mimari.md]]` — eşleme şeması
- `[[../k115-model-monitoring/model-monitoring-metrikleri.md]]` — göç metrikleri
- `[[../k113-model-registry/model-registry-yasam-dongusu.md]]` — sürüm/yayın ortaklığı
- `[[../k118-data-labeling/data-labeling-kalite-kontrol.md]]` — veri doğrulama

## 18. Sürüm Geçmişi

| Sürüm | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 4.0.0 | 2026-10-06 | D03 dilimi uyum stratejisi ilk yazım | vault-writer |
| 1.0.0 | 2026-09-20 | K4 taslak (yedek kaynak) | - |

## 19. Göç Kontrol Listesi (özet)

- **G-01:** Faz planı onaylandı.
- **G-02:** Geri alma provası yapıldı.
- **G-03:** Ölçüm eşikleri onaylandı.
- **G-04:** Alias tablosu açıldı.
- **G-05:** `id_ver` alanı eklendi.
- **G-06:** DualWriter devrede.
- **G-07:** Backfill başladı (checkpoint aktif).
- **G-08:** DualReader devrede.
- **G-09:** Trafik %5.
- **G-10:** Trafik %25.
- **G-11:** Trafik %50.
- **G-12:** Trafik %100.
- **G-13:** Mutabakat farkı 0.
- **G-14:** RefScanner temiz.
- **G-15:** Arkaik pencere başladı.
- **G-16:** Arkaik pencere bitti.
- **G-17:** Eski okuma kapandı.
- **G-18:** Kapanış raporu üretildi.
- **G-19:** Ders kaydı eklendi.
- **G-20:** Olay arşivlendi.

## 20. Ek Göç Kuralları (`SM-041`..`SM-100`)

- **SM-041:** Göç başlamadan önce yedek alınır (faz 0).
- **SM-042:** Yedek alınmadan faz 1 başlamaz.
- **SM-043:** Faz geçişleri olay kaydına yazılır.
- **SM-044:** Faz geri dönüşü (regresyon) onay ister.
- **SM-045:** DualWriter iki hedefe de yazar; biri başarısızsa işlem reddedilir.
- **SM-046:** DualWriter hataları ayrı metrikle izlenir.
- **SM-047:** DualReader çözümlemesi alias tablosundan okur.
- **SM-048:** Alias bulunamazsa `SEM-105` + telafi işi.
- **SM-049:** Backfill kayıtları küçük partiler halinde işlenir.
- **SM-050:** Parti hatası yalnız o kaydı ayırır.
- **SM-051:** Backfill sırasında canlı yazım önceliklidir.
- **SM-052:** Backfill ilerlemesi saatlik raporlanır.
- **SM-053:** Backfill duraklaması 6 sa alarm üretir.
- **SM-054:** Trafik adımları arasında minimum gözlem süresi vardır.
- **SM-055:** Minimum süre `⚠️ VERIFICATION REQUIRED`.
- **SM-056:** Kapı K4-K9 eşikleri onaysız uygulanamaz.
- **SM-057:** Kapı başarısızlık nedeni olay kaydına yazılır.
- **SM-058:** Geri alma sonrası metrikler 24 sa izlenir.
- **SM-059:** İkinci geçiş, ilk geçişten farklı planla yapılır.
- **SM-060:** İkinci geçişte geri alma sayısı başlangıçtan sayılır.
- **SM-061:** İndeks eşlemesi silinirse `SEM-401`.
- **SM-062:** İndeks reindex tetiği `SEM-402`.
- **SM-063:** Mutabakat üç deneme yapar; hepsi başarısızsa P2.
- **SM-064:** RefScanner kod, konfigürasyon, doküman ve veri kapsar.
- **SM-065:** Tarama sonucu kayıt altına alınır (dosya + zaman).
- **SM-066:** Manuel istisna (false positive) gerekçelendirilir.
- **SM-067:** İstisna listesi kapanışta tekrar gözden geçirilir.
- **SM-068:** Arkaik pencerede uyarı başlığı gösterilir (API).
- **SM-069:** Arkaik pencere sonunda uyarı kaldırılır.
- **SM-070:** Eski okuma kapatılınca `SEM-103` üretilir.
- **SM-071:** Alias tablosu kapanışta silinmez.
- **SM-072:** Alias arşivi sorgulanabilir kalır (denetim).
- **SM-073:** Merge'de birincil kimlik seçim kuralı: en eski `created_at`.
- **SM-074:** Merge eşitliğinde `id` alfabetik olarak küçük olan kazanır.
- **SM-075:** Merge sonrası `retired` kayıt silinmez.
- **SM-076:** Merge geri alma 7 gün; sonra elle onay.
- **SM-077:** Split yalnız manuel planda.
- **SM-078:** Split sonrası iki kayıt da referanslanır.
- **SM-079:** Split'te indeks iki kayıt olarak yazılır.
- **SM-080:** Split geri alınamaz (dikkatli tasarım gerekir).
- **SM-081:** Göç işi tek örnektir (SM-032).
- **SM-082:** İkinci iş isteği `SEM-406` ile reddedilir.
- **SM-083:** Durdurma checkpoint yazar (SM-033).
- **SM-084:** Checkpoint bozulursa `SEM-407` + fazdan devam.
- **SM-085:** Göç sırasında hata oranı ölçülür.
- **SM-086:** Hata oranı eşiği `⚠️ VERIFICATION REQUIRED`.
- **SM-087:** Otomatik durma tetiği elle kaldırılabilir.
- **SM-088:** Elle kaldırma `admin` onayı ister.
- **SM-089:** p95 etkisi faz 4'te sürekli ölçülür.
- **SM-090:** p95 bütçesi aşılmazsa adım küçültülür.
- **SM-091:** Göç kapanış raporu kapıları + metrikleri + dersi içerir.
- **SM-092:** Rapor arşivlenir ve olay kaydına bağlanır.
- **SM-093:** Ders kaydı bir sonraki göçe girdi olur.
- **SM-094:** Göç sonrası 30 gün izleme sürer.
- **SM-095:** İzleme süresince 404/yönlendirme oranı izlenir.
- **SM-096:** İzleme sonunda nihai kapanış onayı verilir.
- **SM-097:** Nihai onay ürün + SRE + veri sahibidir.
- **SM-098:** Onay kaydı denetim izidir.
- **SM-099:** Göç tamamlanmazsa teknik borç kaydı açılır.
- **SM-100:** Teknik borç kaydı sahibi ve tarihi tanımlıdır.

## 21. Göç Sırası (özet tablo)

| # | İş | Faz | Kapı | Geri alma |
|---|---|---|---|---|
| 1 | Yedek | 0 | K1 | - |
| 2 | Şema | 1 | K1 | ters ALTER |
| 3 | DualWriter | 2 | K2 | bayrak kapat |
| 4 | Backfill | 3 | K3 | checkpoint |
| 5 | DualReader | 3 | K3 | bayrak kapat |
| 6 | %5 trafik | 4 | K4/K5 | RollbackController |
| 7 | %25 trafik | 4 | K4/K5 | RollbackController |
| 8 | %50 trafik | 4 | K4/K5 | RollbackController |
| 9 | %100 trafik | 4 | K4/K5 | RollbackController |
| 10 | Mutabakat | 4→5 | K6 | göç tekrarı |
| 11 | RefScanner | 5 | K7 | düzeltme |
| 12 | Arkaik pencere | 6 | K8 | geri açma |
| 13 | Eski okuma kapat | 6 | K8 | geri açma |
| 14 | Kapanış raporu | 7 | K10 | - |

## 22. Göç Senaryoları (kısa test listesi)

- **G-01:** Kesinti + checkpoint devam → M-01
- **G-02:** DualWriter kısmi hata → M-02
- **G-03:** Kodda eski referans → M-03
- **G-04:** Eşzamanlı iki göç → M-04
- **G-05:** Geri alma + tekrar → M-05
- **G-06:** İndeks geride kalma → M-06
- **G-07:** Backfill + canlı çakışma → M-07
- **G-08:** Ters alias → M-08
- **G-09:** Eski istemci → M-09
- **G-10:** Kapanış sonrası eski id → M-10
- **G-11:** İlerleme duraklaması → M-11
- **G-12:** Mutabakat farkı → M-12
- **G-13:** Merge → M-13
- **G-14:** Split → M-14
- **G-15:** Ölçüm yokluğu → M-15
- **G-16:** Kapı reddi → M-16
- **G-17:** p95 bozulması → M-17
- **G-18:** Kapanış raporu → M-18

## 23. Operasyon Kartları

| Durum | Ne yap | Ne yapma |
|---|---|---|
| Göç takıldı | checkpoint oku, kaydı atla | sıfırdan başlama |
| Geri alma gerek | RollbackController | alias silme |
| Mutabakat farkı | telafi işi | göz ardı etme |
| Eski referans | düzelt + tekrar tara | kapanış yapma |
| İndeks geride | `SEM-402` tetiği | elle indeks yazma |
| Alarm fırtınası | cooldown | eşikleri aniden değiştirme |
| Ölçüm yok | ölçümü onar | eşik uydurma |
| Kapı reddi | nedeni incele | kapıyı atlama |
| Rapor eksik | manuel üret | kapanış ilan etme |
| Ders yok | toplantı + kayıt | arşivleme |

## 24. Bilinmeyenler (bu dosya)

- Göç süresi tahmini — `⚠️ VERIFICATION REQUIRED`.
- Göç maliyeti — `⚠️ VERIFICATION REQUIRED`.
- Trafik adımı süreleri — `⚠️ VERIFICATION REQUIRED`.
- Hata oranı eşiği — `⚠️ VERIFICATION REQUIRED`.
- p95 bütçesi — `⚠️ VERIFICATION REQUIRED`.
- Arkaik pencere onayı — `⚠️ VERIFICATION REQUIRED`.
- Min. gözlem süresi — `⚠️ VERIFICATION REQUIRED`.
- Retention politikası — `⚠️ VERIFICATION REQUIRED`.
- Uyumluluk kapsamı — `⚠️ VERIFICATION REQUIRED`.
- Göç ortamı — `⚠️ VERIFICATION REQUIRED`.

## 25. Sürüm Geçmişi (bu dosya)

| Sürüm | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 4.0.0 | 2026-10-06 | Göç kuralları + operasyon kartları eklendi | vault-writer |
| 4.0.0 | 2026-10-06 | Uyum stratejisi ilk yazım | vault-writer |
| 1.0.0 | 2026-09-20 | K4 taslak (yedek kaynak) | - |

## 17. Ek — KVKK Uyum Maddeleri ve Karar Matrisi

### 17.1 KVKK maddesi eşlemesi

| Madde | Başlık (kısa) | Bu bölümün karşılığı | Durum |
|---|---|---|---|
| 4 | Genel ilkeler | Amaç sınırlılığı, ölçülülük | Karşılanıyor (tasarım) |
| 5 | İşleme şartları | Açık rıza / sözleşme | Karşılanıyor (tasarım) |
| 6 | Özel nitelikli veri | Kimlik verisi sınıflandırması | Karşılanıyor (tasarım) |
| 9 | Açık rıza | Onay akışı, geri çekme | Karşılanıyor (tasarım) |
| 10 | Veri güvenliği | Hash, rol, imzalama | Karşılanıyor (tasarım) |
| 11 | Yurt dışına aktarma | Aktarım kararı kaydı | Kısmen (aktarım yok) |
| 12 | İlgili kişi hakları | Silme, düzeltme, taşınabilirlik | Karşılanıyor (tasarım) |
| 13 | Aydınlatma | Metin sürümü + zaman damgası | Karşılanıyor (tasarım) |
| 16 | Veri sorumlusu | Sorgu kaydı, denetim izi | Kısmen (uygulama yok) |
| 22 | Yaptırımlar | Kontrol envanteri | `⚠️ VERIFICATION REQUIRED` |

### 17.2 Karar matrisi — onay kapısı seçenekleri

| Ölçüt | A: Sadece metin onayı | B: Metin + checkbox | C: Metin + checkbox + kayıt | D: Zorunlu oturum + onay |
|---|---|---|---|---|
| Hukuki açıklık | Orta | İyi | Çok iyi | Çok iyi |
| Uygulama yükü | Düşük | Orta | Orta-Yüksek | Yüksek |
| Geri çekilebilirlik | Zayıf | Orta | İyi | Çok iyi |
| İlgili kişi denetimi | Zayıf | Orta | İyi | Çok iyi |
| Bot direnci | Düşük | Orta | Orta | Yüksek |
| **Seçim** | Hayır | Hayır | **Evet (varsayılan)** | Opsiyonel |

Seçim gerekçesi: C seçeneği, kanıt üretimi ile uygulama yükü arasında en iyi dengeyi
kurar. D seçeneği hesap gerektirdiği için hesapsız kullanım senaryolarını kırar.

### 17.3 Reddedilen seçenekler ve nedenleri

| Reddedilen | Neden |
|---|---|
| Onayı yalnızca metin ile almak | Geri çekilebilir kanıt üretmez |
| Onayı localStorage'da tutmak | Sunucu tarafı kanıt gerekir |
| Onayı her istekte yeniden sormak | Kullanılabilirliği kırar |
| Onayı süresiz geçerli saymak | Geri çekme hakkı ihlali |
| Onayı sadece çerezle kanıtlamak | Çerez silinebilir |
| Onayı kullanıcı ID'sine gömmek | Geri döndürülebilir bağlantı sızıntısı |

## 18. Ek — Uyum Kontrol Listesi (Son Ek)

| # | Kontrol | Sıklık | Sahip | Kanıt |
|---|---|---|---|---|
| C1 | Aydınlatma metni sürümü eşleşiyor mu? | Her onay | Backend | Onay kaydı |
| C2 | Geri çekme kaydı kuyruğa düştü mü? | Her geri çekme | Backend | Kuyruk kaydı |
| C3 | Silme işi indeksi de temizledi mi? | Her silme | Data | İndeks sayaçları |
| C4 | Taşınabilirlik dosyası alan adları sınırlı mı? | Her dışa aktarma | Data | Dosya ön izleme |
| C5 | Denetim kaydında parola yok mu? | Her kayıt | Security | Kayıt taraması |
| C6 | Onay metni metni ham veriyle eşleşiyor mu? | Metin değişikliğinde | Hukuk | Sürüm diff |
| C7 | Saklama süresi aşıldı mı? | Günlük | Data | Zaman damgası raporu |
| C8 | Eşik sapmaları kaydedildi mi? | Haftalık | QA | `sm-id-fpr` kaydı |
| C9 | Yerel indeks yaşı sınırda mı? | Saatlik | Data | `sm-id-index-staleness` |
| C10 | Cross-issuer kontrolü atlanıyor mu? | Her istek | Security | Kod denetimi |

Tüm kontrollerin otomatikleşmiş karşılığı depoda yoktur:
`⚠️ VERIFICATION REQUIRED`.

## 19. Ek — Uygulama Notu

Bu bölümdeki tüm akışlar tasarım düzeyindedir. Depoda karşılık gelen sınıf,
endpoint veya kuyruk işi bulunmadığı için uygulama notları kanıt beklemektedir.
Kanıt geldiğinde bu belge `status: active` kalır; aksi durumda tasarım olarak
okunur ve `VERIFICATION REQUIRED` işaretleri korunur.

İlgili dosyalar: `[[semantic-id-tasarim.md]]` · `[[index.md]]`.
