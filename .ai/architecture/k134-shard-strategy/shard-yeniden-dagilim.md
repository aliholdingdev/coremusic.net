---
title: "134 Shard Stratejisi — Operasyon"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 134-shard-strategy · Shard Stratejisi (Operasyon)


---

Bu dosya **parçalama (sharding) stratejisi** alanının işletimini tanımlar: operasyon akışı, kurallar, alarmlar, runbook, kenar durumlar, hata modları ve bağımlılık matrisi. Kardeş dosya: mimari.


---

- **Amaç (B):** Parça yeniden dağıtımının planlanması, kesinti penceresi, doğrulama ve geri alma operasyonunu; parça metriklerinin izlenmesini tanımlar.

---

- **Sorumlu persona:** sre-engineer · data-engineer


---

## Operasyon Akışı

| Adim | Asama | Aciklama | Periyot |
|---|---|---|---|
| 1 | Ölçüm | Dağılım raporu (parça yükleri) | günlük |
| 2 | Değerlendirme | Sıcak parça kararının verilmesi | haftalık |
| 3 | Plan | Rebalance planı + pencere | talep üzerine |
| 4 | Prova | Kopya ortamda taşıma provası | her plan |
| 5 | Uygulama | Draining → taşıma → doğrulama | pencerede |
| 6 | Kapanış | Harita sürümü + rapor | pencere sonu |
| 7 | Geri alma provası | Eski haritaya dönüş tatbikatı | aylık |

---

## Operasyon Kurallari

| Kural | Tanim | Ihlal riski |
|---|---|---|
| `SHD-OP-01` | Rebalance yalnız onaylı pencerede başlar. | Onaysız geçiş yasak |
| `SHD-OP-02` | Taşıma öncesi hedef parça kapasitesi doğrulanır. | Yarım taşıma |
| `SHD-OP-03` | Taşıma sırasında kaynak parça drain edilir. | Çift yazma |
| `SHD-OP-04` | Her aşamada satır sayısı karşılaştırılır. | Sayısal kanıt |
| `SHD-OP-05` | Geçiş sonrası eski harita en az bir pencere saklanır. | Hızlı geri alma |
| `SHD-OP-06` | Geri alma tatbikatı aylık yapılır. | Kanıtsız prosedür |
| `SHD-OP-07` | Parça metriği olmadan kapasite kararı verilmez. | Ölçümsüz karar |
| `SHD-OP-08` | Eşikler iki onay ister (data + performance). | Tek onay riskli |

---

## Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Kod |
|---|---|---|---|
| E01 | Taşıma sırasında yazma devam ediyor | Draining + delta kuyruğu | `SHD-OP-E01` |
| E02 | Hedef parça dolu çıkarsa | İş durur, geri dönülür | `SHD-OP-E02` |
| E03 | Pencere bitti, taşıma yarım | Geri alma kararı eskalasyonu | `SHD-OP-E03` |
| E04 | Metrik kaybı | Taşıma durdurulur | `SHD-OP-E04` |
| E05 | Ekip çakışması | Sıra kilidi | `SHD-OP-E05` |
| E06 | Geri alma provası gecikti | Olay açılır | `SHD-OP-E06` |
| E07 | Eşik değişikliği | İki onay aranır | `SHD-OP-E07` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `SHD-OP-401` | Sayı uyuşmazlığı | Taşıma kaybı | İşi durdur, geri dön |
| 2 | `SHD-OP-402` | Kapasite yetersiz | Plan hatası | Planı yenile |
| 3 | `SHD-OP-403` | Pencere aşımı | Uzun taşıma | Geri alma kararı |
| 4 | `SHD-OP-404` | Metrik yok | Ölçüm kesintisi | Taşımayı durdur |
| 5 | `SHD-OP-405` | Harita yayında değil | Dağıtım atlandı | Yayını tamamla |
| 6 | `SHD-OP-406` | Onay kaydı yok | Süreç atlanmış | İşi başlatma |
| 7 | `SHD-OP-407` | Geri alma başarısız | Eski harita bozulmuş | Eskale et |
| 8 | `SHD-OP-408` | Kanıtsız eşik | Uydurma sayı | VERIFICATION REQUIRED |

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


---

## Alarmlar

| # | Alarm | Oncelik | Eylem |
|---|---|---|---|
| `AM-01` | Parça yük sapması > eşik | P2 | Rebalance değerlendir |
| `AM-02` | Sıcak parça bayrağı | P2 | Dağılımı incele |
| `AM-03` | Taşıma sayım farkı | P1 | İşi durdur |
| `AM-04` | Parça doluluk > eşik | P2 | Kapasite olayı |
| `AM-05` | Harita sürümü yayında değil | P2 | Yayın takibi |
| `AM-06` | Tatbikat gecikti | P3 | Plan oluştur |

---

## Runbook

| Adim | Islem | Cikti |
|---|---|---|
| 1 | Dağılım raporunu oku | Parça yük tablosu |
| 2 | Sıcak parçayı tespit et | load_index sıralaması |
| 3 | Rebalance planını onayla | Onay kaydı |
| 4 | Draining → taşıma → doğrulama | Sayılar eşit |
| 5 | Harita sürümünü yayınla | map_version arttı |
| 6 | Rapor + log | log.md girişi |

---

## Prova / Test

| # | Prova | Beklenen |
|---|---|---|
| O-01 | Taşıma provası | Sayılar birebir eşit |
| O-02 | Geri alma tatbikatı | Eski harita geri gelir |
| O-03 | Draining yazma redsi | Yeni yazma kabul edilmez |
| O-04 | Pencere aşımına müdahale | Karar akışı çalışır |
| O-05 | Günlük dağılım raporu | Rapor eksiksiz |

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

## Bağlantılar

### Kendi dosyalari

1. `[[shard-yeniden-dagilim.md]]`
2. `[[index.md]]`
3. `[[shard-strateji-mimari.md]]`

### Komsu / ilgili klasorler

1. `[[../k133-read-replica/index.md]]`
2. `[[../k135-event-store/index.md]]`
3. `[[../k133-read-replica/read-replica-mimari.md]]`
4. `[[../k135-event-store/event-store-mimari.md]]`
5. `[[../k108-vector-index/index.md]]`
6. `[[../k109-semantic-id/index.md]]`

---

## Kanıt

- `Kanıt: .ai/architecture/shard-strategy/shard-yeniden-dagilim.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/mysql-18-database.md (Partition Stratejisi bölümü)`
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/database-registry.md (L1-L470)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/shard-strategy/shard-yeniden-dagilim.md`

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

## SSS

| Soru | Cevap | Kaynak |
|---|---|---|
| Parça anahtarı değişir mi? | Hayır — SHD-001 sonradan değiştirilemez. | §3 |
| Kaç parça? | Ölçümle belirlenir; sayı UNKNOWN. | SHD-007 |
| Ürün adı neden yok? | Seçim yapılmadı. | ZERO-HALLUCINATION |
| Çapraz sorgu yasak mı? | Bilinçli modda izinli; gizli çapraz yasak. | SHD-003 |
| Drop nasıl? | Yedek doğrulaması + draining. | SHD-008 |
| Rebalance sıklığı? | Ölçüme bağlı; karar haftalık değerlendirilir. | §B |

---

## Surumler

| Sürüm | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 4.0.0 | 2026-10-06 | D04 k132-k143 operasyon (B) ilk yazım | vault-writer |
| 1.0.0 | 2026-09-20 | K5 taslak (yedek kaynak) | - |

### Ek.1. Kural denetim kayıtları

| Kural | Aşama | Zorunlu işlem | Kanıt durumu |
|---|---|---|---|
| `SHD-OP-01` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-OP-02` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-OP-03` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-OP-04` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-OP-05` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-OP-06` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-OP-07` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-OP-08` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |

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
- Tekrarlı çalıştırma (re-run) güvenliği `SHD-OP-01` ile sabitlenir; idempotentlik varsayılmaz, kanıtlanır.
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
- Ek not 1: `SHD-OP-01` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 2: `SHD-OP-02` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 3: `SHD-OP-03` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 4: `SHD-OP-04` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 5: `SHD-OP-05` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 6: `SHD-OP-06` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 7: `SHD-OP-07` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 8: `SHD-OP-08` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 9: `SHD-OP-01` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 10: `SHD-OP-02` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 11: `SHD-OP-03` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 12: `SHD-OP-04` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 13: `SHD-OP-05` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 14: `SHD-OP-06` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 15: `SHD-OP-07` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 16: `SHD-OP-08` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 17: `SHD-OP-01` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 18: `SHD-OP-02` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 19: `SHD-OP-03` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 20: `SHD-OP-04` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 21: `SHD-OP-05` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 22: `SHD-OP-06` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 23: `SHD-OP-07` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 24: `SHD-OP-08` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 25: `SHD-OP-01` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 26: `SHD-OP-02` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 27: `SHD-OP-03` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 28: `SHD-OP-04` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 29: `SHD-OP-05` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 30: `SHD-OP-06` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 31: `SHD-OP-07` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 32: `SHD-OP-08` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 33: `SHD-OP-01` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 34: `SHD-OP-02` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 35: `SHD-OP-03` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 36: `SHD-OP-04` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 37: `SHD-OP-05` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 38: `SHD-OP-06` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 39: `SHD-OP-07` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 40: `SHD-OP-08` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 41: `SHD-OP-01` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 42: `SHD-OP-02` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 43: `SHD-OP-03` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 44: `SHD-OP-04` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 45: `SHD-OP-05` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 46: `SHD-OP-06` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 47: `SHD-OP-07` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 48: `SHD-OP-08` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 49: `SHD-OP-01` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 50: `SHD-OP-02` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 51: `SHD-OP-03` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 52: `SHD-OP-04` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 53: `SHD-OP-05` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 54: `SHD-OP-06` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 55: `SHD-OP-07` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 56: `SHD-OP-08` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 57: `SHD-OP-01` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 58: `SHD-OP-02` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 59: `SHD-OP-03` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 60: `SHD-OP-04` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 61: `SHD-OP-05` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 62: `SHD-OP-06` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 63: `SHD-OP-07` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 64: `SHD-OP-08` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 65: `SHD-OP-01` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 66: `SHD-OP-02` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 67: `SHD-OP-03` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 68: `SHD-OP-04` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 69: `SHD-OP-05` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 70: `SHD-OP-06` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 71: `SHD-OP-07` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 72: `SHD-OP-08` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 73: `SHD-OP-01` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 74: `SHD-OP-02` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 75: `SHD-OP-03` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 76: `SHD-OP-04` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 77: `SHD-OP-05` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 78: `SHD-OP-06` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 79: `SHD-OP-07` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 80: `SHD-OP-08` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 81: `SHD-OP-01` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 82: `SHD-OP-02` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 83: `SHD-OP-03` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 84: `SHD-OP-04` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 85: `SHD-OP-05` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 86: `SHD-OP-06` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 87: `SHD-OP-07` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 88: `SHD-OP-08` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 89: `SHD-OP-01` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 90: `SHD-OP-02` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 91: `SHD-OP-03` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 92: `SHD-OP-04` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 93: `SHD-OP-05` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 94: `SHD-OP-06` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 95: `SHD-OP-07` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 96: `SHD-OP-08` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.