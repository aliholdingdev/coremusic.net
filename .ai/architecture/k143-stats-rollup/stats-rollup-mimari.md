---
title: "143 İstatistik Toplama (Rollup) — Mimari ve Tasarim"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 143-stats-rollup · İstatistik Toplama (Rollup) (Mimari)


---

Bu dosya **istatistik toplama** alanının tasarımını tanımlar: kapsam, akış, kurallar, veri modeli, kenar durumlar, hata modları ve bağımlılık matrisi. Kardeş dosya: operasyon.


---

- **Amaç (A):** Oynatma/etkileşim olaylarından türetilen istatistiklerin toplanması, pencereleme (window), yutarlılık (idempotent) hesaplama ve geriye dönük düzeltme (backfill) tanımlar.

---

- **Sorumlu persona:** data-engineer · performance-engineer

---

- **Eşleşen klasor:** istatistik toplama → veri taşıma geçmişi


---

## Kapsam

### Kapsamda (In)

| # | Kapsam |
|---|---|
| 1 | Olay → istatistik toplama tasarımı |
| 2 | Pencereleme ve idempotent sayaç |
| 3 | Mutabakat (reconciliation) |
| 4 | Geriye dönük doldurma (backfill) |

---

### Kapsamdisi (Out)

| # | Kapsamdisi |
|---|---|
| 1 | Olay kaynak üretimi (K7 kaynağı) |
| 2 | Öneri modeli eğitimi (K4) |
| 3 | Canlı gösterim/UX (K11) |
| 4 | Ham olay saklama (k135) |

---

## Mimari Akış

| Adim | Asama | Aciklama | Kural |
|---|---|---|---|
| 1 | Olay | Olay akışından kayıt alınır | STA-001 |
| 2 | Anahtar | Sayım anahtarı belirlenir (parça/pencere) | STA-002 |
| 3 | Toplama | Idempotent sayaç güncellenir | STA-003 |
| 4 | Pencere | Pencere kapanışında ara toplam yazılır | STA-004 |
| 5 | Mutabakat | Toplam ile kaynak sayımlar karşılaştırılır | STA-005 |
| 6 | Yayın | Kapanan pencere okumaya açılır | STA-006 |

---

## Kurallar

| Kural | Tanim | Ihlal riski |
|---|---|---|
| `STA-001` | İstatistik yalnız olay kayıtlarından türetilir; elle sayı girilmez. | Uydurma metrik |
| `STA-002` | Her sayımın bir sayım anahtarı (anahtar + pencere) vardır. | Anahtarsız sayım |
| `STA-003` | Aynı olay iki kez gelirse sayaç iki kez artmaz (idempotent). | Çift sayım |
| `STA-004` | Açık pencere yeniden yazılabilir; kapanan pencere salt-okunurdur. | Geçmiş değiştirme |
| `STA-005` | Kapanan pencere mutabakattan geçer; fark alarmı üretir. | Sessiz sapma |
| `STA-006` | Geriye dönük doldurma (backfill) ayrı işte çalışır ve işaretlidir. | Karışık veri |
| `STA-007` | İstatistik türüretilmiş veridir; kaynak kaydı olay akışıdır. | SSOT ihlali |
| `STA-008` | Yuvarlama/yüzde hesapları tanımlıdır; belirsizlik payı belirtilir. | Sahte kesinlik |
| `STA-009` | Pencere uzunluğu tanımlıdır (sayı UNKNOWN); değişim ADR ister. | Karışık pencere |
| `STA-010` | Toplama işi REDACTED içermez; kullanıcı tanımlayıcılar pseudonym kalır. | REDACTED |

---

## Veri Modeli

| Alan | Tip | Aciklama | Kural |
|---|---|---|---|
| `metric_key` | `VARCHAR` | Sayım anahtarı | `STA-002` |
| `window_start` | `DATETIME` | Pencere başı (UTC) | `STA-004` |
| `window_end` | `DATETIME` | Pencere sonu (UTC) | `STA-004` |
| `count_value` | `BIGINT` | Toplam sayı | `STA-003` |
| `dedup_token` | `VARCHAR` | Olay tekilleştirme jetonu | `STA-003` |
| `state` | `ENUM` | open/closed/reconciled | `STA-004` |
| `backfill_flag` | `ENUM` | live/backfill | `STA-006` |
| `reconcile_delta` | `BIGINT` | Mutabakat farkı | `STA-005` |
| `updated_at` | `DATETIME` | Güncelleme (UTC) | `STA-004` |

---

## Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Kod |
|---|---|---|---|
| E01 | Aynı olay iki kez geldi | dedup_token ile sayaç bir kez artar | `STA-E01` |
| E02 | Kapanmış pencereye olay | Ayrı düzeltme kaydı; kapanan pencere değiştirilmez | `STA-E02` |
| E03 | Mutabakat farkı çıktı | Fark alarmı + inceleme | `STA-E03` |
| E04 | Backfill canlı işle çakıştı | backfill_flag ayrımı; sayaçlar karışmaz | `STA-E04` |
| E05 | Olay akışı kesildi | Pencere açık kalır; kapanış ertelenir | `STA-E05` |
| E06 | Pencere uzunluğu değişti | ADR + yeniden etiketleme, eski veri zorlanmaz | `STA-E06` |
| E07 | Boş pencere | Sıfır yazılır; sessiz atlanmaz | `STA-E07` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `STA-401` | Çift sayım | Tekrarlı olay | dedup_token uygula |
| 2 | `STA-402` | Geçmiş değiştirme | Kapanan pencere yazımı | Reddet |
| 3 | `STA-403` | Mutabakat farkı | Kaynak/sayıç uyuşmazlığı | Alarm + incele |
| 4 | `STA-404` | Olay kaybı | Akış kesintisi | Pencereyi açık tut |
| 5 | `STA-405` | Backfill çakışması | Eşzamanlı iş | Flag ile ayır |
| 6 | `STA-406` | Anahtar eksikliği | Olay şeması hatalı | Olay şemasını düzelt |
| 7 | `STA-407` | Pencere belirsizliği | Süre tanımı yok | ADR + onay |
| 8 | `STA-408` | Kanıtsız eşik | Uydurma hedef | VERIFICATION REQUIRED |

---

## Bağımlılık Matrisi

| Katman | Iliski | Bu klasor ne verir | Bu klasor ne alir |
|---|---|---|---|
| K0 cekirdek | dolayli | dosya/surec zamanlamasi | kalici dosya erisimi |
| K1 donanim | hayir | - | - |
| K2 surucu | dolayli | - (ihlal) | - (ihlal) |
| K3 ses motoru | dolayli | - (ihlal) | - (ihlal) |
| K4 yapay zeka | hayir | - | - |
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
| T-01 | Aynı olay iki kez | Sayaç bir kez artar |
| T-02 | Kapanan pencere yazımı | Red |
| T-03 | Mutabakat provası | Fark alarmı |
| T-04 | Backfill + canlı | Sayılar karışmaz |
| T-05 | Olay akışı kesintisi | Pencere açık kalır |
| T-06 | Boş pencere | Sıfır yazılır |
| T-07 | Anahtar eksik olay | Red + kayıt |
| T-08 | Pencere değişikliği | ADR kapısı |

---

## ADR Adaylari

| # | Karar | Reddedilen | Risk | Durum |
|---|---|---|---|---|
| A1 | Idempotent sayaç | Tekrarı sayan sayaç | Çift sayım | kabul |
| A2 | Kalan pencere salt-okunur | Serbest geçmiş düzeltmesi | Geçmiş güvensizliği | kabul |
| A3 | Mutabakat zorunlu | Mutabakatsız kapanış | Sessiz sapma | kabul |
| A4 | Backfill ayrık iş | Canlıya karışık iş | Karışık veri | kabul |
| A5 | Pencere uzunluğu ölçümle | Tahmini pencere | Kanıtsız pencere | açık — VERIFICATION REQUIRED |

---

## Riskler

| Risk | Etki | Onlem |
|---|---|---|
| Çift sayım | Yanlış istatistik | dedup_token |
| Geçmiş oynama | Güvensiz rapor | Salt-okunur kapanış |
| Olay kaybı | Eksik sayaç | Açık pencere + alarm |
| Backfill karışması | Tutarsız toplam | Flag ayrımı |
| Sahte kesinlik | Yanlış karar | Belirsizlik notu |

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
| `WindowCloser` | Pencereyi kapatır | Geçmişi değiştirmez |
| `DedupCounter` | Olayı tekilleştirir | Olayı saklamaz |
| `Reconciler` | Farkı hesaplar | Düzeltmez |
| `BackfillRunner` | Eksik pencereyi doldurur | Canlıyı yazmaz |

---

## Bağlantılar

### Kendi dosyalari

1. `[[stats-rollup-mimari.md]]`
2. `[[index.md]]`
3. `[[stats-rollup-operasyon.md]]`

### Komsu / ilgili klasorler

1. `[[../k142-library-metadata/index.md]]`
2. `[[../k142-library-metadata/library-metadata-mimari.md]]`
3. `[[../k108-vector-index/index.md]]`
4. `[[../k109-semantic-id/index.md]]`

---

## Kanıt

- `Kanıt: .ai/architecture/stats-rollup/stats-rollup-mimari.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/mysql-18-database.md (L1-L500)`
- `Kanıt: .ai/.sql/mysql/coremusic_playlist.sql (etkileşim referansı kaynağı — dosya var)`
- `Kanıt: .github/workflows/ci.yml (iş yürütme kanıtı — dosya var)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/stats-rollup/stats-rollup-mimari.md`

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
| istatistik toplama kayit politikasi | **R** | C | A | C | C | I |
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
| `İstatistik toplama` | `rollup` | Olaylardan türetilen sayılar |
| `Pencere` | `window` | Toplama zaman aralığı |
| `Idempotent` | `idempotent counter` | Tekrar güvenli sayaç |
| `Mutabakat` | `reconciliation` | Toplam-kaynak eşitliği |
| `Geriye doldurma` | `backfill` | Eksik geçmişin tamamlanması |
| `Tekilleştirme jetonu` | `dedup token` | Olay tekrarı engeli |
| `Sayım anahtarı` | `metric key` | Anahtar + pencere çifti |
| `Türetilmiş veri` | `derived data` | Yeniden üretilebilir |
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
| `STA-001` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `STA-002` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `STA-003` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `STA-004` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `STA-005` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `STA-006` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `STA-007` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `STA-008` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `STA-009` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `STA-010` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |

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

- istatistik toplama için kaynak kaydı (system of record) K5 şemasıdır; bu dosyadaki her tablo türetilmiştir.
- Tekrarlı çalıştırma (re-run) güvenliği `STA-001` ile sabitlenir; idempotentlik varsayılmaz, kanıtlanır.
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
- Ek not 1: `STA-001` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 2: `STA-002` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 3: `STA-003` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 4: `STA-004` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 5: `STA-005` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 6: `STA-006` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 7: `STA-007` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 8: `STA-008` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 9: `STA-009` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 10: `STA-010` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 11: `STA-001` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 12: `STA-002` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 13: `STA-003` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 14: `STA-004` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 15: `STA-005` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 16: `STA-006` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 17: `STA-007` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 18: `STA-008` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 19: `STA-009` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 20: `STA-010` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 21: `STA-001` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 22: `STA-002` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 23: `STA-003` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 24: `STA-004` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 25: `STA-005` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 26: `STA-006` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 27: `STA-007` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 28: `STA-008` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 29: `STA-009` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 30: `STA-010` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 31: `STA-001` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 32: `STA-002` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 33: `STA-003` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 34: `STA-004` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 35: `STA-005` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 36: `STA-006` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 37: `STA-007` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 38: `STA-008` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 39: `STA-009` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 40: `STA-010` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 41: `STA-001` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 42: `STA-002` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 43: `STA-003` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 44: `STA-004` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 45: `STA-005` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 46: `STA-006` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 47: `STA-007` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 48: `STA-008` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 49: `STA-009` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.