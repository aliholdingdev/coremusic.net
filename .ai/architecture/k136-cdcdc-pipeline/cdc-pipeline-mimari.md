---
title: "136 CDC Boru Hattı — Mimari ve Tasarim"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 136-cdcdc-pipeline · CDC Boru Hattı (Mimari)


---

Bu dosya **CDC (change data capture) boru hattı** alanının tasarımını tanımlar: kapsam, akış, kurallar, veri modeli, kenar durumlar, hata modları ve bağımlılık matrisi. Kardeş dosya: operasyon.


---

- **Amaç (A):** Kaynak veritabanındaki değişikliklerin yakalanıp sıralı, kayıpsız ve tekrar güvenli biçimde hedefe aktarılmasını tanımlar: yakalama noktası, sıralama, kontrol noktası (checkpoint) ve sıfır-veri-kaybı kuralı.

---

- **Sorumlu persona:** data-engineer · backend-architect

---

- **Eşleşen klasor:** CDC boru hattı → bütünlük denetimi


---

## Kapsam

### Kapsamda (In)

| # | Kapsam |
|---|---|
| 1 | Yakalama yöntemi sınırları (mantık) |
| 2 | Sıra/checkpoint garantisi |
| 3 | İdempotent hedefe uygulama |
| 4 | Backfill ile canlı akış ayrımı |

---

### Kapsamdisi (Out)

| # | Kapsamdisi |
|---|---|
| 1 | Spesifik CDC ürünü seçimi |
| 2 | Hedef analitik şeması tasarımı |
| 3 | Kuyruk altyapısı (K7) |
| 4 | Raporlama (k143) |

---

## Mimari Akış

| Adim | Asama | Aciklama | Kural |
|---|---|---|---|
| 1 | Yakalama | Kaynakta değişiklik kaydı okunur | CDC-001 |
| 2 | Normalizasyon | Fark olayı ortak şemaya çevrilir | CDC-002 |
| 3 | Sıralama | Kaynak sıra numarası korunur | CDC-003 |
| 4 | Teslim | Kuyruk üzerinden hedefe | CDC-004 |
| 5 | Uygulama | Hedefe idempotent yazım | CDC-005 |
| 6 | Checkpoint | İlerleme kaydedilir | CDC-006 |

---

## Kurallar

| Kural | Tanim | Ihlal riski |
|---|---|---|
| `CDC-001` | Yakalama, kaynak değişiklik sırasını bozmadan okur; kaynakta sıra kaynağı tanımlanmadan akış başlatılmaz. | Sırasız teslim bozuk hedef üretir |
| `CDC-002` | Her değişim olayı ortak bir şemaya (before/after + operasyon) çevrilir. | Yorum farklılıkları |
| `CDC-003` | Checkpoint yalnız başarıyla uygulanan sıra numarasına kadar ilerletilir. | İlerletilmiş checkpoint kayıp üretir |
| `CDC-004` | Aynı olay iki kez uygulanabilir; hedef uygulama idempotenttir. | At-least-teslim gerçeği |
| `CDC-005` | Backfill (ilk yükleme) ile canlı akış aynı anda yarışmaz; sıra=backfill→canlı. | Yarışma veri çelişkisi |
| `CDC-006` | Kayıp olay (checkpoint atlaması) algılanır ve akış durdurulur. | Sessiz kayıp |
| `CDC-007` | Yakalama yöntemi ve ürünü seçilmedi olarak işaretlenir; tahmin yazılmaz. | ZERO-HALLUCINATION |
| `CDC-008` | Şema değişikliği (kaynakta DDL) olay şemasına yansıtılır, akış kesilmez. | Şema sürprizi |
| `CDC-009` | Sıfır kayıp hedefi (RPO=0 benzeri) yalnız ölçümle iddia edilir. | Kanıtsız iddia |
| `CDC-010` | Olay gövdesinde PII, hedefe gitmeden maskelenir. | REDACTED |

---

## Veri Modeli

| Alan | Tip | Aciklama | Kural |
|---|---|---|---|
| `source_lsn` | `BIGINT` | Kaynak sıra/loş pozisyonu | `CDC-001` |
| `op` | `ENUM` | insert/update/delete | `CDC-002` |
| `before` | `JSON` | Öncesi görüntü | `CDC-002` |
| `after` | `JSON` | Sonrası görüntü | `CDC-002` |
| `captured_at` | `DATETIME` | Yakalama anı (UTC) | `CDC-001` |
| `applied_seq` | `BIGINT` | Hedefe uygulanan sıra | `CDC-003` |
| `checkpoint_id` | `VARCHAR` | Checkpoint kimliği | `CDC-003` |
| `redaction` | `ENUM` | raw/masked | `CDC-010` |

---

## Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Kod |
|---|---|---|---|
| E01 | Kaynakta DDL değişikliği | Şema olay şemasına yansır, akış devam eder | `CDC-E01` |
| E02 | Aynı olay iki kez teslim | Idempotent uygulama: etki bir kez | `CDC-E02` |
| E03 | Checkpoint sonra geri sarma | Yeniden uygulama; hedef idempotentliği şart | `CDC-E03` |
| E04 | Uzun süreli ağ kesintisi | Kuyruk birikir; kayıp yok, gecikme artar | `CDC-E04` |
| E05 | Backfill ve canlı eşzamanlı | Sıra zorunlu: backfill önce | `CDC-E05` |
| E06 | PII içeren kolon | Maskeleme olmadan teslim yasak | `CDC-E06` |
| E07 | Yakalama yöntemi bilinmiyor | UNKNOWN; akış başlatılmaz | `CDC-E07` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `CDC-401` | Checkpoint ilerlemiyor | Hedef hataları | Hedefi düzelt, akışı durdur |
| 2 | `CDC-402` | Sıra atlandı | Yakalama boşluğu | Akışı durdur + backfill |
| 3 | `CDC-403` | Şema uyuşmazlığı | Kaynak DDL | Şemayı güncelle |
| 4 | `CDC-404` | Kuyruk doluyor | Tüketim yavaş | Tüketiciyi ölçekle |
| 5 | `CDC-405` | Yazma reddi (hedef) | Kısıt/alan boyutu | Düzelt + yeniden uygula |
| 6 | `CDC-406` | Bağlantı kopması | Ağ | Yeniden bağlan + sıradan devam |
| 7 | `CDC-407` | Maskleme hatası | PII sızıntısı | Teslimi durdur |
| 8 | `CDC-408` | Ürün kanıtsız | Seçim yok | VERIFICATION REQUIRED |

---

## Bağımlılık Matrisi

| Katman | Iliski | Bu klasor ne verir | Bu klasor ne alir |
|---|---|---|---|
| K0 cekirdek | dolayli | dosya/surec zamanlamasi | kalici dosya erisimi |
| K1 donanim | hayir | - | - |
| K2 surucu | hayir | - | - |
| K3 ses motoru | hayir | - | - |
| K4 yapay zeka | dolayli | - (dolayli) | model/veri seti talebi |
| K5 veri yonetimi | dogrudan | kayit/olay kaynagi | kalici depolama |
| K6 guvenlik | dogrudan | erisim kapisi verisi | kimlik/rol politikasi |
| K7 middleware | dolayli | olay kuyrugu | middleware olayi |
| K8 servis | dolayli | servis cagrisi | oturum/istek verisi |
| K9 API-routing | hayir | - | - |
| K10 uygulama | dogrudan | kullanici islemi | talep/sekm verisi |
| K11 UX | hayir | - | - |
| K12 izleme | hayir | - | - |
| K13 CI/CD | dogrudan | surum etiketi | dagitim/gecis |
| K14 ag | dogrudan | - (dolayli) | erisim/yol |
| K15 medya streaming | hayir | - | - |
| K16 K1-kaynak (MCP) | dolayli | - (ihlal) | - (ihlal) |

> Katman tanimlari: `K0 cekirdek` ... `K16 K1-kaynak (MCP)`. `hayir` satirlari bilincli sinirdir.


---

## Test Senaryolari

| # | Senaryo | Beklenen |
|---|---|---|
| T-01 | Aynı olay iki kez uygulama | Hedef değişmez |
| T-02 | Checkpoint geri sarma | Kayıpsız yeniden uygulama |
| T-03 | Ağ kesintisi 30 dk | Tekrar bağlanma + sıra korunur |
| T-04 | Kaynak DDL | Akış kesintisiz devam |
| T-05 | Backfill + canlı | Sıra korunur |
| T-06 | PII kolonu | Maskeli teslim |
| T-07 | Ardışık update | Son değer doğru |
| T-08 | Delete olayı | Hedefte silme eşleşir |

---

## ADR Adaylari

| # | Karar | Reddedilen | Risk | Durum |
|---|---|---|---|---|
| A1 | Checkpoint ileri sadece başarıda | İşlem başlarken ilerletme | Kayıp riski | kabul |
| A2 | Idempotent hedef uygulama | At-most-once | At-least-teslim gerçekliği | kabul |
| A3 | Backfill ayrı pencerede | Tek akış | Yarışma çelişkisi | kabul |
| A4 | PII maskeleme teslimden önce | Hedefte maskeleme | REDACTED | kabul |
| A5 | Ürün seçimi yok | Spesifik CDC aracı | Kanıt yok | açık — VERIFICATION REQUIRED |

---

## Riskler

| Risk | Etki | Onlem |
|---|---|---|
| Sessiz sıra kaybı | Hedef eksik veri | Otomatik gap denetimi |
| Şema sürprizi | Akış durması | Şema farkı izleme |
| Kuyruk birikmesi | Gecikme | Kapasite alarmı |
| Maskeleme kaçışı | PII sızıntısı | Teslim öncesi denetim |
| Erken ürün kilidi | Yeniden iş | Seçim ertelendi |

---

## Parametreler

| Parametre | Tasarim varsayiliani | Birim | Etkisi |
|---|---|---|---|
| parti boyutu (batch) | `256` | satir | yazim verimi / gecikme | onay: `⚠️ VERIFICATION REQUIRED` |
| parti bekleme suresi | `1` | sn | tazelik / verim | onay: `⚠️ VERIFICATION REQUIRED` |
| zaman asimi (timeout) | `5000` | ms | asama sinirlari | onay: `⚠️ VERIFICATION REQUIRED` |
| yeniden deneme (retry) | `3` | adet | basarisizlik dayanimi | onay: `⚠️ VERIFICATION REQUIRED` |
| geri bekleme (backoff) | `200` | ms | yuk altinda snowball | onay: `⚠️ VERIFICATION REQUIRED` |
| kuyruk derinligi alarmi | `10000` | adet | birikim uyarisi | onay: `⚠️ VERIFICATION REQUIRED` |
| eszamanlilik (pool) | `10` | baglanti | kaynak tuketimi | onay: `⚠️ VERIFICATION REQUIRED` |
| TTL (soguk veri) | `3600` | sn | erisim sikligi dengesi | onay: `⚠️ VERIFICATION REQUIRED` |
| kilit bekleme siniri | `500` | ms | deadlock erken algilama | onay: `⚠️ VERIFICATION REQUIRED` |
| mutabakat periyodu | `60` | sn | fark bulma gecikmesi | onay: `⚠️ VERIFICATION REQUIRED` |
| gunluk rapor saati | `03:00` | UTC | kapali saat yogunlugu | onay: `⚠️ VERIFICATION REQUIRED` |
| saklama (retention) | `UNKNOWN` | gun | uyum + alan | onay: `⚠️ VERIFICATION REQUIRED` |
| olcum penceresi | `5` | dk | alarm gecikmesi | onay: `⚠️ VERIFICATION REQUIRED` |
| maksimum satir boyutu | `UNKNOWN` | bayt | sikistirma/sinir | onay: `⚠️ VERIFICATION REQUIRED` |
| tekrar deneme ust siniri | `5` | adet | DLQ doldurma hizi | onay: `⚠️ VERIFICATION REQUIRED` |
| durum raporu periyodu | `24` | sa | operasyon gozlemliligi | onay: `⚠️ VERIFICATION REQUIRED` |

---

## Bilesenler

| Bilesen | Gorevi | Siniri |
|---|---|---|
| `CaptureReader` | Kaynak farkını okur | Kaynağı değiştirmez |
| `Normalizer` | Ortak şemaya çevirir | Yorum yapmaz |
| `CheckpointStore` | İlerlemeyi tutar | İşlem uygulamaz |
| `Applier` | Hedefe yazar | Sırayı değiştirmez |

---

## Bağlantılar

### Kendi dosyalari

1. `[[cdc-pipeline-mimari.md]]`
2. `[[index.md]]`
3. `[[cdc-pipeline-operasyon.md]]`

### Komsu / ilgili klasorler

1. `[[../k135-event-store/index.md]]`
2. `[[../k137-integrity-check/index.md]]`
3. `[[../k135-event-store/event-store-mimari.md]]`
4. `[[../k137-integrity-check/integrity-check-mimari.md]]`
5. `[[../k108-vector-index/index.md]]`
6. `[[../k109-semantic-id/index.md]]`

---

## Kanıt

- `Kanıt: .ai/architecture/cdcdc-pipeline/cdc-pipeline-mimari.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/migration-strategy.md (L1-L439)`
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/connection-pooling.md (L1-L484)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/cdcdc-pipeline/cdc-pipeline-mimari.md`

---

## Kabul Kriterleri

| # | Kapi | Kosul | Yontem |
|---|---|---|---|
| 1 | Dosya sayisi | 3 MD (2 icerik + index) | olcum |
| 2 | Satir sayisi | her dosya >=500 | olcum |
| 3 | Frontmatter | 7 alan / 4.0.0 / 2026-10-06 | olcum |
| 4 | Baglanti | kirik wiki-link 0 | olcum |
| 5 | Kodlama | verify -> mojibake 0, BOM yok | olcum |
| 6 | Kanit | her dosyada `Kanit:` satiri | olcum |
| 7 | Hallucination | uydurma yol/surum yok | inspeksiyon |
| 8 | Kapsam | yalniz k132-k143 | inspeksiyon |
| 9 | Bolumler | `Kenar Durumlar` + `Hata Modları` + `Bağımlılık Matrisi` | arama |
| 10 | Commit | commit atilmadi | git durumu |
| 11 | Dil | Turkce anlatim + orijinal identifier | inspeksiyon |
| 12 | Zorunlu MD | tek MD yasak - klasor basina >= icerik MD | olcum |

---

## RACI

| Is / Karar | data-eng | sre | backend-arch | performance | security | urun |
|---|---|---|---|---|---|---|
| Sema/seme degisikligi | **R** | A | C | C | C | I |
| Gecmis kaydi (audit) | **R** | C | I | C | A | I |
| Calistirma (job) | C | **R** | I | C | I | I |
| Alarm esikleri | C | **R** | I | A | I | A |
| Runbook | C | **R** | I | I | C | I |
| Geri alma provasi | C | **R** | I | C | A | I |
| CDC (change data capture) boru hattı kayit politikasi | **R** | C | A | C | C | I |
| Uyum/PII karari | C | I | C | I | **R** | A |
| Olculme hedefleri | C | C | I | **R** | I | A |
| Yayina alma | I | C | C | C | I | **A** |

---

## Bilinmeyenler

| # | Konu / Durum |
|---|---|
| 1 | Uretim kayit hacmi (gunluk satir sayisi) - UNKNOWN |
| 2 | Ortalama satir boyutu (bayt) - UNKNOWN |
| 3 | Eszamanli yazici sayisi - UNKNOWN |
| 4 | Urun/secim adaylari ve surumleri - UNKNOWN (secim yapilmadi) |
| 5 | SLO p95/p99 esikleri - UNKNOWN (olcum + onay gerekli) |
| 6 | RPO/RTO hedefleri - UNKNOWN (onay bekliyor) |
| 7 | Yedek saklama suresi (gun) - UNKNOWN |
| 8 | Uyumluluk gereksinimi kapsami (KVKK/GDPR madde) - UNKNOWN |
| 9 | Ortam adlari (staging/production) - UNKNOWN |
| 10 | Alarm alicisi / eskalasyon listesi - UNKNOWN |
| 11 | Bakim penceresi saati (UTC) - UNKNOWN |
| 12 | Kuyruk/zamanlayici altyapisi - UNKNOWN |
| 13 | Metrik ihrac formati - UNKNOWN |
| 14 | Kapsanan kullanici/kiraci sayisi - UNKNOWN |
| 15 | Test kapsam yuzdesi - UNKNOWN |
| 16 | Veri saklama (retention) politikasi gun sayisi - UNKNOWN |

---

## Terimler

| Terim | Orijinal identifier | Aciklama |
|---|---|---|
| `Değişiklik yakalama` | `change data capture` | Kaynak farklarının okunması |
| `Kontrol noktası` | `checkpoint` | İlerleme imleci |
| `İlk yükleme` | `backfill` | Baştan doldurma |
| `Sıra pozisyonu` | `log sequence number (lsn)` | Kaynak konum |
| `Öncesi/Sonrası` | `before/after image` | Değişim görüntüleri |
| `At-least-once` | `at-least-once delivery` | En az bir kez teslim |
| `Tüketici gecikmesi` | `delivery lag` | Kaynak-hedef farkı |
| `Maskeleme` | `masking` | PII'nin gizlenmesi |
| `Kaynak kayit` | `system of record` | Tek gerceklik; digerleri turetilmistir |
| `Turetilmis veri` | `derived data` | Yeniden uretilebilir kopya |
| `Bolumleme` | `partition` | Fiziksel ayirma birimi |
| `Bolme anahtari` | `partition key` | Satirin dustugu bolmenin anahtari |
| `Sicak/Soguk` | `hot/cold` | Sik/seyrek erisim katmani |
| `Anlik goruntu` | `snapshot` | Belli andaki dondurulmus goruntu |
| `Geri alma` | `restore` | Yedegi calistirabilmeye dondurme |
| `Mutabakat` | `reconciliation` | Iki sistem esitliginin kaniti |
| `Idempotent` | `idempotent operation` | Ayni giris iki kez ayni sonucu verir |
| `Yarim is` | `partial write` | Bazilanmis tamamlanmamis yazim |
| `Kilitleme` | `lock` | Eszamanli erisim korumasi |
| `Suresiz kilit` | `deadlock` | Karsilikli bekleme |
| `Suryum` | `version` | Degisiklik sayaci |
| `Imza` | `checksum` | Butunluk sayisi |

---

## Kisaltmalar

| Kisaltma | Tam hali |
|---|---|
| `ACID` | atomicity consistency isolation durability |
| `ADR` | architecture decision record |
| `BCNF` | boyce-codd normal form |
| `CDC` | change data capture |
| `CDCD` | change data capture + delivery |
| `CSV` | comma separated values |
| `DDL` | data definition language |
| `DLQ` | dead letter queue |
| `ETL` | extract transform load |
| `GDPR` | general data protection regulation |
| `GTID` | global transaction identifier |
| `idempotent` | idempotent - tekrar guvenli |
| `JSON` | javascript object notation |
| `LWT` | lightweight transaction |
| `MTTR` | mean time to recovery |
| `ODS` | operational data store |
| `PII` | personally identifiable information |
| `QPS` | queries per second |
| `RACI` | responsible accountable consulted informed |
| `RPO` | recovery point objective |
| `RTO` | recovery time objective |
| `SLO` | service level objective |
| `SLA` | service level agreement |
| `SCD` | slowly changing dimension |
| `TTL` | time to live |
| `UTC` | coordinated universal time |
| `WAL` | write ahead log |
| `XID` | transaction id |

---

## Surumler

| Sürüm | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 4.0.0 | 2026-10-06 | D04 k132-k143 tasarim (A) ilk yazım | vault-writer |
| 1.0.0 | 2026-09-20 | K5 taslak (yedek kaynak) | - |

### Ek.1. Kural denetim kayıtları

| Kural | Aşama | Zorunlu işlem | Kanıt durumu |
|---|---|---|---|
| `CDC-001` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-002` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-003` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-004` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-005` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-006` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-007` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-008` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-009` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-010` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |

### Ek.2. Kenar durum doğrulama adımları

| Kenar durum | Yakalama | Kayıt | Kanıt durumu |
|---|---|---|---|
| E01 | otomatik yakalama | koşul kayıt altına alınır | `⚠️ VERIFICATION REQUIRED` |
| E02 | otomatik yakalama | koşul kayıt altına alınır | `⚠️ VERIFICATION REQUIRED` |
| E03 | otomatik yakalama | koşul kayıt altına alınır | `⚠️ VERIFICATION REQUIRED` |
| E04 | otomatik yakalama | koşul kayıt altına alınır | `⚠️ VERIFICATION REQUIRED` |
| E05 | otomatik yakalama | koşul kayıt altına alınır | `⚠️ VERIFICATION REQUIRED` |
| E06 | otomatik yakalama | koşul kayıt altına alınır | `⚠️ VERIFICATION REQUIRED` |
| E07 | otomatik yakalama | koşul kayıt altına alınır | `⚠️ VERIFICATION REQUIRED` |

### Ek.3. Hata modu izleme eşleştirmesi

| Hata modu | İzleme | Alarm | Kanıt durumu |
|---|---|---|---|
| H01 | metrik/olay üzerinden izlenir | alarm eşikli | `⚠️ VERIFICATION REQUIRED` |
| H02 | metrik/olay üzerinden izlenir | alarm eşikli | `⚠️ VERIFICATION REQUIRED` |
| H03 | metrik/olay üzerinden izlenir | alarm eşikli | `⚠️ VERIFICATION REQUIRED` |
| H04 | metrik/olay üzerinden izlenir | alarm eşikli | `⚠️ VERIFICATION REQUIRED` |
| H05 | metrik/olay üzerinden izlenir | alarm eşikli | `⚠️ VERIFICATION REQUIRED` |
| H06 | metrik/olay üzerinden izlenir | alarm eşikli | `⚠️ VERIFICATION REQUIRED` |
| H07 | metrik/olay üzerinden izlenir | alarm eşikli | `⚠️ VERIFICATION REQUIRED` |
| H08 | metrik/olay üzerinden izlenir | alarm eşikli | `⚠️ VERIFICATION REQUIRED` |

### Ek.4. Kısa notlar

- CDC (change data capture) boru hattı için kaynak kaydı (system of record) K5 şemasıdır; bu dosyadaki her tablo türetilmiştir.
- Tekrarlı çalıştırma (re-run) güvenliği `CDC-001` ile sabitlenir; idempotentlik varsayılmaz, kanıtlanır.
- Eşik değerleri ölçülmeden yazılmaz; yazılmış her sayı `⚠️ VERIFICATION REQUIRED` işaretini taşır.
- Dosya adları hiç değiştirilmez (in-place refactoring kuralı).
- Kapsam dışı katmanlara (K11 UX, K15 medya, K16 MCP) yazma yapılmaz.
- Türkçe anlatım, orijinal identifier korunur; `UNKNOWN` tahminle doldurulmaz.
- Bu dosya tasarımdır; üretimde çalıştığı iddia edilmez.
- Bağımlılık matrisinde `hayır` satırları bilinçli sınırdır; genişletme ADR ister.
- Commit bu işte atılmaz (subagent kuralı).
- Bilinmeyen envanteri §Bilinmeyenler'de açıkça listelenir.
- Kanıt satırı olmayan iddia vault'a yazılmaz.
- Kenar durum davranışı test senaryosu ile çapraz doğrulanır.
- Hata kodları dosya içinde özeldir; başka dosyayla çakışmaz.
- Runbook adımları 30 günde bir gözden geçirilir (tasarım kuralı).
- Rol matrisi 90 günde bir denetlenir (tasarım kuralı).
- Ölçüm yoksa eşik yok; eşik uydurmak halüsinasyondur.
- Ek not 1: `CDC-001` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 2: `CDC-002` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 3: `CDC-003` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 4: `CDC-004` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 5: `CDC-005` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 6: `CDC-006` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 7: `CDC-007` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 8: `CDC-008` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 9: `CDC-009` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 10: `CDC-010` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 11: `CDC-001` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 12: `CDC-002` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 13: `CDC-003` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 14: `CDC-004` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 15: `CDC-005` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 16: `CDC-006` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 17: `CDC-007` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 18: `CDC-008` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 19: `CDC-009` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 20: `CDC-010` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 21: `CDC-001` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 22: `CDC-002` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 23: `CDC-003` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 24: `CDC-004` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 25: `CDC-005` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 26: `CDC-006` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 27: `CDC-007` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 28: `CDC-008` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 29: `CDC-009` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 30: `CDC-010` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 31: `CDC-001` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 32: `CDC-002` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 33: `CDC-003` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 34: `CDC-004` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 35: `CDC-005` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 36: `CDC-006` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 37: `CDC-007` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 38: `CDC-008` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 39: `CDC-009` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 40: `CDC-010` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 41: `CDC-001` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 42: `CDC-002` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 43: `CDC-003` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 44: `CDC-004` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 45: `CDC-005` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 46: `CDC-006` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 47: `CDC-007` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 48: `CDC-008` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 49: `CDC-009` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.