---
title: "134 Shard Stratejisi — Mimari ve Tasarim"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 134-shard-strategy · Shard Stratejisi (Mimari)


---

Bu dosya **parçalama (sharding) stratejisi** alanının tasarımını tanımlar: kapsam, akış, kurallar, veri modeli, kenar durumlar, hata modları ve bağımlılık matrisi. Kardeş dosya: operasyon.


---

- **Amaç (A):** Veriyi mantıksal parçalara (shard) bölerken anahtar seçimi, parçalar arası sorgu sınırları, sıcak parça (hot shard) riski ve yeniden dağıtım (rebalance) tasarımını tanımlar.

---

- **Sorumlu persona:** data-engineer · performance-engineer

---

- **Eşleşen klasor:** parçalama anahtarı → olay deposu


---

## Kapsam

### Kapsamda (In)

| # | Kapsam |
|---|---|
| 1 | Parça anahtarı seçimi kuralı |
| 2 | Parça haritası (map) sürümleme |
| 3 | Parçalar arası sorgu yasağı/toleransı |
| 4 | Sıcak parça algılama |

---

### Kapsamdisi (Out)

| # | Kapsamdisi |
|---|---|
| 1 | Bölme (partition) içi indeks tasarımı |
| 2 | Replika topolojisi (k133) |
| 3 | Yedekleme (k5 backup) |
| 4 | Uygulama koddaki sorgular |

---

## Mimari Akış

| Adim | Asama | Aciklama | Kural |
|---|---|---|---|
| 1 | Anahtar | Sorgu/yazma anahtarı çıkarılır | SHD-001 |
| 2 | Harita | Anahtar → parça eşlemesi okunur | SHD-002 |
| 3 | Yönlendirme | Tek parça hedefi seçilir | SHD-003 |
| 4 | Sınır | Parçalar arası sorgu reddedilir ya da birleştirilir | SHD-004 |
| 5 | Ölçüm | Parça yükü ve dağılım K12'ye | SHD-005 |
| 6 | Uyarı | Sıcak parça eşiği aşılırsa olay | SHD-006 |

---

## Kurallar

| Kural | Tanim | Ihlal riski |
|---|---|---|
| `SHD-001` | Parça anahtarı yazımdan önce sabitlenir; anahtar sonradan değiştirilemez. | Yeniden yazım maliyeti |
| `SHD-002` | Parça haritası sürümlüdür (map_version); sürümsüz harita kullanılmaz. | Geçiş tutarsızlığı |
| `SHD-003` | Her istek en fazla tek parçaya gider; çok parçalı istek bilinçli olarak işaretlenir. | Gizli çapraz maliyet |
| `SHD-004` | Parçalar arası bütünlük kısıtı (FK benzeri) veri modeliyle sağlanır, sorguyla değil. | Dağıtık kısıt pahalı |
| `SHD-005` | Parça başına kayıt/yük metrikleri sürekli üretilir. | Sıcak parça kör kalır |
| `SHD-006` | Dağılım eşitsizliği eşiği aşıldıysa rebalance tetiklenir. | Yanlış ölçek |
| `SHD-007` | Parça sayısı ve teknoloji seçimi kanıtsızdır; UNKNOWN işaretlenir. | ZERO-HALLUCINATION |
| `SHD-008` | Parça silme (drop) yalnız yedek doğrulamasından sonra yapılır. | Geri alınamaz kayıp |
| `SHD-009` | Yeni parça açılışı boş parçayla başlar, veri kopyalama ayrı aşamadır. | Kısmi veri yanılgısı |
| `SHD-010` | Parça anahtarı seçimi, seçicilik ölçümüne dayanır; sezgisel seçim yasaktır. | Ölçümsüz dağılım |

---

## Veri Modeli

| Alan | Tip | Aciklama | Kural |
|---|---|---|---|
| `shard_id` | `INT` | Parça numarası | `SHD-002` |
| `map_version` | `INT` | Harita sürümü | `SHD-002` |
| `key_range` | `VARCHAR` | Anahtar aralığı | `SHD-001` |
| `row_count` | `BIGINT` | Kayıt sayısı | `SHD-005` |
| `load_index` | `DECIMAL` | Yük göstergesi | `SHD-006` |
| `state` | `ENUM` | active/draining/migrating/dropped | `SHD-008` |
| `created_at` | `DATETIME` | Açılış anı (UTC) | `SHD-009` |
| `hot_flag` | `TINYINT` | Sıcak parça bayrağı | `SHD-006` |

---

## Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Kod |
|---|---|---|---|
| E01 | Tek parça aşırı dolu | Sıcak parça alarmı + rebalance önerisi | `SHD-E01` |
| E02 | Boş parça oluştu | Normal durum, metrikte görünür | `SHD-E02` |
| E03 | Harita sürümü değişirken istek | Eski sürümle tutarlı yanıt, sonra geçiş | `SHD-E03` |
| E04 | Parçalar arası sorgu geldi | Bilinçli modda birleştirme, değilse red | `SHD-E04` |
| E05 | Anahtar seçiciliği düşük | Yeniden ölçüm + tasarım kaydı | `SHD-E05` |
| E06 | Parça düşürme sırasında erişim | Draining: yeni yazma yok, okuma devam | `SHD-E06` |
| E07 | Parça sayısı bilinmiyor | UNKNOWN yazılır, tahmin edilmez | `SHD-E07` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `SHD-401` | Harita bulunamadı | Sürüm eşleşmesi yok | İsteği durdur |
| 2 | `SHD-402` | Anahtar dağıtılamıyor | Geçersiz anahtar | Girdiyi doğrula |
| 3 | `SHD-403` | Çapraz sorgu zaman aşımı | Birleştirme çok pahalı | Tasarımı gözden geçir |
| 4 | `SHD-404` | Parça dolu | Kapasite | Rebalance başlat |
| 5 | `SHD-405` | Harita çakışması | Aynı aralık iki parçada | Geçmişi durdur |
| 6 | `SHD-406` | Drop sonrası erişim | Eski sürüm istemci | Sürüm kapı kontrolü |
| 7 | `SHD-407` | Dağılım metriği yok | Ölçüm aksadı | Sağlıksız say |
| 8 | `SHD-408` | Parça sayısı kanıtsız | Seçim yok | VERIFICATION REQUIRED |

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
| K10 uygulama | hayir | - | - |
| K11 UX | hayir | - | - |
| K12 izleme | dogrudan | metrik/olcum | alarm/esik |
| K13 CI/CD | dogrudan | surum etiketi | dagitim/gecis |
| K14 ag | hayir | - | - |
| K15 medya streaming | dolayli | - (ihlal) | - (ihlal) |
| K16 K1-kaynak (MCP) | hayir | - | - |

> Katman tanimlari: `K0 cekirdek` ... `K16 K1-kaynak (MCP)`. `hayir` satirlari bilincli sinirdir.


---

## Test Senaryolari

| # | Senaryo | Beklenen |
|---|---|---|
| T-01 | Eşit dağılım | Yük sapması eşik içinde |
| T-02 | Sıcak parça senaryosu | Alarm + öner |
| T-03 | Harita sürüm geçişi | Tutarsızlık yok |
| T-04 | Çapraz sorgu | Red/birleşim kuralı |
| T-05 | Parça drop | Draining davranışı |
| T-06 | Boş anahtar | Erken red |
| T-07 | Parça sayısında artış | Harita sürümü artar |
| T-08 | Eski sürüm istemci | Uyumluluk reddi |

---

## ADR Adaylari

| # | Karar | Reddedilen | Risk | Durum |
|---|---|---|---|---|
| A1 | Anahtar sabit + harita sürümlü | Çalışma zamanında anahtar değişimi | Veri yeniden yazımı riski | kabul |
| A2 | Çok parçalı istek açık işaretli | Şeffaf birleştirme | Gizli maliyet | kabul |
| A3 | Drop yedeğe bağlı | Anında silme | Geri alınamazlık | kabul |
| A4 | Parça sayısı ölçümle belirlenir | Sabit öngörü | Ölçümsüz kapasite | kabul |
| A5 | Ürün seçimi yok | Spesifik sharding ürünü | Kanıt yok | açık — VERIFICATION REQUIRED |

---

## Riskler

| Risk | Etki | Onlem |
|---|---|---|
| Sıcak parça | Yerel şişme | Ölçüm + rebalance planı |
| Harita tutarsızlığı | Yanlış hedef | Sürüm + kapı kontrolü |
| Çapraz sorgu yayılması | Gecikme artışı | Bilinçli mod zorunlu |
| Erken ürün kilidi | Yeniden iş | Seçim ertelendi |
| Drop hatası | Veri kaybı | Yedek doğrulaması |

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
| `KeyExtractor` | Anahtarı çıkarır | Sorguyu değiştirmez |
| `ShardMap` | Haritayı sürümle tutar | Veri taşımaz |
| `Rebalancer` | Taşımayı planlar | Onay vermez |
| `HotSpotWatcher` | Dağılımı izler | Karar vermez |

---

## Bağlantılar

### Kendi dosyalari

1. `[[shard-strateji-mimari.md]]`
2. `[[index.md]]`
3. `[[shard-yeniden-dagilim.md]]`

### Komsu / ilgili klasorler

1. `[[../k133-read-replica/index.md]]`
2. `[[../k135-event-store/index.md]]`
3. `[[../k133-read-replica/read-replica-mimari.md]]`
4. `[[../k135-event-store/event-store-mimari.md]]`
5. `[[../k108-vector-index/index.md]]`
6. `[[../k109-semantic-id/index.md]]`

---

## Kanıt

- `Kanıt: .ai/architecture/shard-strategy/shard-strateji-mimari.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/mysql-18-database.md (Partition Stratejisi bölümü)`
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/database-registry.md (L1-L470)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/shard-strategy/shard-strateji-mimari.md`

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
| parçalama (sharding) stratejisi kayit politikasi | **R** | C | A | C | C | I |
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
| `Parça` | `shard / partition` | Fiziksel mantıksal bölüm |
| `Parça anahtarı` | `shard key` | Parçayı belirleyen alan |
| `Harita` | `shard map` | Anahtar-parça eşlemesi |
| `Sıcak parça` | `hot shard` | Aşırı yükli parça |
| `Yeniden dağıtım` | `rebalance` | Veriyi yeniden dengeleme |
| `Boşaltma` | `draining` | Yazmayı kesme aşaması |
| `Çapraz sorgu` | `cross-shard query` | Birden çok parçayı kapsayan |
| `Seçicilik` | `cardinality/selectivity` | Anahtarın ayırma gücü |
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
| `SHD-001` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-002` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-003` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-004` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-005` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-006` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-007` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-008` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-009` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-010` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |

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

- parçalama (sharding) stratejisi için kaynak kaydı (system of record) K5 şemasıdır; bu dosyadaki her tablo türetilmiştir.
- Tekrarlı çalıştırma (re-run) güvenliği `SHD-001` ile sabitlenir; idempotentlik varsayılmaz, kanıtlanır.
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
- Ek not 1: `SHD-001` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 2: `SHD-002` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 3: `SHD-003` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 4: `SHD-004` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 5: `SHD-005` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 6: `SHD-006` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 7: `SHD-007` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 8: `SHD-008` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 9: `SHD-009` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 10: `SHD-010` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 11: `SHD-001` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 12: `SHD-002` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 13: `SHD-003` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 14: `SHD-004` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 15: `SHD-005` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 16: `SHD-006` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 17: `SHD-007` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 18: `SHD-008` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 19: `SHD-009` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 20: `SHD-010` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 21: `SHD-001` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 22: `SHD-002` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 23: `SHD-003` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 24: `SHD-004` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 25: `SHD-005` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 26: `SHD-006` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 27: `SHD-007` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 28: `SHD-008` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 29: `SHD-009` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 30: `SHD-010` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 31: `SHD-001` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 32: `SHD-002` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 33: `SHD-003` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 34: `SHD-004` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 35: `SHD-005` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 36: `SHD-006` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 37: `SHD-007` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 38: `SHD-008` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 39: `SHD-009` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 40: `SHD-010` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 41: `SHD-001` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 42: `SHD-002` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 43: `SHD-003` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 44: `SHD-004` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 45: `SHD-005` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 46: `SHD-006` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 47: `SHD-007` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 48: `SHD-008` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 49: `SHD-009` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.