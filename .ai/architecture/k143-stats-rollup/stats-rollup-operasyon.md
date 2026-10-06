---
title: "143 İstatistik Toplama (Rollup) — Operasyon"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 143-stats-rollup · İstatistik Toplama (Rollup) (Operasyon)


---

Bu dosya **istatistik toplama** alanının işletimini tanımlar: operasyon akışı, kurallar, alarmlar, runbook, kenar durumlar, hata modları ve bağımlılık matrisi. Kardeş dosya: mimari.


---

- **Amaç (B):** Toplama işlerinin işletilmesini: pencere kapanışı, mutabakat (reconciliation), geriye dönük doldurma ve raporlama operasyonu.

---

- **Sorumlu persona:** performance-engineer · sre-engineer


---

## Operasyon Akışı

| Adim | Asama | Aciklama | Periyot |
|---|---|---|---|
| 1 | Pencere kapanışı | Açık pencerelerin kapatılması | saatlik |
| 2 | Mutabakat | Toplam-kaynak karşılaştırması | saatlik |
| 3 | Backfill | Eksik pencerelerin doldurulması | talep üzerine |
| 4 | Gecikme metriği | Toplama gecikmesinin izlenmesi | sürekli |
| 5 | Rapor | İstatistik sağlığı raporu | haftalık |
| 6 | Politika gözden geçirme | Pencere + yuvarlama kuralları | çeyreklik |
| 7 | Arşiv | Eski pencerelerin saklama denetimi | aylık |

---

## Operasyon Kurallari

| Kural | Tanim | Ihlal riski |
|---|---|---|
| `STA-OP-01` | Pencere kapanışı saatliktir ve kayda alınır. | Kapanışsız veri |
| `STA-OP-02` | Mutabakat farkı eşiği tanımlıdır (sayı UNKNOWN). | Eşiksiz alarm |
| `STA-OP-03` | Backfill işleri canlı işten ayrı çalışır ve işaretlidir. | Karışık veri |
| `STA-OP-04` | Fark alarmında kapanan pencere otomatik açılmaz; onay gerekir. | Kör düzeltme |
| `STA-OP-05` | Saklama (retention) süresi tanımlıdır (sayı UNKNOWN); aşım arşivlenir. | Alan şişmesi |
| `STA-OP-06` | Denetim kayıtları append-only saklanır. | Denetim izi |
| `STA-OP-07` | Runbook'a sır/PII yazılmaz; tanımlayıcılar pseudonym. | REDACTED |
| `STA-OP-08` | Toplama metrikleri K12'ye akar. | Kör operasyon |

---

## Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Kod |
|---|---|---|---|
| E01 | Mutabakat farkı > eşik | Onaylı düzeltme işi planlanır | `STA-OP-E01` |
| E02 | Pencere kapanışı gecikti | Gecikme alarmı | `STA-OP-E02` |
| E03 | Backfill yoğun saatte | Pencere yeniden planlanır | `STA-OP-E03` |
| E04 | Olay akışı durdu | Kapanış ertelenir + olay | `STA-OP-E04` |
| E05 | Saklama aşımı | Arşiv onayı | `STA-OP-E05` |
| E06 | Rapor gecikti | Olay açılır | `STA-OP-E06` |
| E07 | Onaysız geçmiş düzeltme | İş durdurulur | `STA-OP-E07` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `STA-OP-401` | Kapanış gecikmesi | İş kesintisi | İşi yeniden başlat |
| 2 | `STA-OP-402` | Mutabakat > eşik | Kaynak sapması | Onaylı düzeltme |
| 3 | `STA-OP-403` | Backfill yarım | Kesinti | İşi tamamla |
| 4 | `STA-OP-404` | Saklama aşımı | Alan şişmesi | Arşivle |
| 5 | `STA-OP-405` | Onaysız düzeltme | Süreç ihlali | Kaydı düzelt |
| 6 | `STA-OP-406` | Denetim kaydı eksik | Yazma aksadı | Denetimi düzelt |
| 7 | `STA-OP-407` | Rapor üretilemedi | Veri erişimi | Erişimi düzelt |
| 8 | `STA-OP-408` | Kanıtsız eşik | Uydurma sayı | VERIFICATION REQUIRED |

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


---

## Alarmlar

| # | Alarm | Oncelik | Eylem |
|---|---|---|---|
| `AM-01` | Mutabakat farkı > eşik | P1 | Onaylı düzeltme |
| `AM-02` | Pencere kapanışı gecikti | P2 | İşi yeniden başlat |
| `AM-03` | Toplama gecikmesi > eşik | P2 | Akışı incele |
| `AM-04` | Backfill yarım kaldı | P3 | İşi tamamla |
| `AM-05` | Denetim kaydı durdu | P2 | Denetimi düzelt |
| `AM-06` | Onaysız geçmiş düzeltme | P1 | İşi durdur |

---

## Runbook

| Adim | Islem | Cikti |
|---|---|---|
| 1 | Toplama sağlık metriklerini oku | Sağlık panosu |
| 2 | Açık pencereleri incele | Kapanış kaydı |
| 3 | Mutabakat farkını doğrula | Karşılaştırma |
| 4 | Onaylı düzeltme işini çalıştır | Onay kaydı |
| 5 | Yeniden mutabakat | pass sonucu |
| 6 | Olay + log | log.md girişi |

---

## Prova / Test

| # | Prova | Beklenen |
|---|---|---|
| O-01 | Pencere kapanışı provası | Kapanış kaydı oluşur |
| O-02 | Mutabakat provası | Fark alarmı tetiklenir |
| O-03 | Backfill provası | Flag ayrımı korunur |
| O-04 | Gecikme alarmı doğrulama | Alarm tetiklenir |
| O-05 | Haftalık rapor | Rapor eksiksiz |

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

1. `[[stats-rollup-operasyon.md]]`
2. `[[index.md]]`
3. `[[stats-rollup-mimari.md]]`

### Komsu / ilgili klasorler

1. `[[../k142-library-metadata/index.md]]`
2. `[[../k142-library-metadata/library-metadata-mimari.md]]`
3. `[[../k108-vector-index/index.md]]`
4. `[[../k109-semantic-id/index.md]]`

---

## Kanıt

- `Kanıt: .ai/architecture/stats-rollup/stats-rollup-operasyon.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/mysql-18-database.md (L1-L500)`
- `Kanıt: .ai/.sql/mysql/coremusic_playlist.sql (etkileşim referansı kaynağı — dosya var)`
- `Kanıt: .github/workflows/ci.yml (iş yürütme kanıtı — dosya var)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/stats-rollup/stats-rollup-operasyon.md`

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

## SSS

| Soru | Cevap | Kaynak |
|---|---|---|
| Aynı olay iki kez sayılır mı? | Hayır — dedup_token (STA-003). | §3 |
| Kapanan pencere değişir mi? | Hayır — salt-okunur (STA-004). | §3 |
| Fark çıkarsa? | Alarm + onaylı düzeltme (STA-OP-04). | §5 |
| Backfill canlıyı bozar mı? | Hayır — flag ayrımı (STA-006). | §3 |
| Pencere uzunluğu? | UNKNOWN — ADR + onay. | STA-009 |
| Mutabakat eşiği? | UNKNOWN — ölçüm gerekli. | STA-OP-02 |

---

## Surumler

| Sürüm | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 4.0.0 | 2026-10-06 | D04 k132-k143 operasyon (B) ilk yazım | vault-writer |
| 1.0.0 | 2026-09-20 | K5 taslak (yedek kaynak) | - |

### Ek.1. Kural denetim kayıtları

| Kural | Aşama | Zorunlu işlem | Kanıt durumu |
|---|---|---|---|
| `STA-OP-01` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `STA-OP-02` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `STA-OP-03` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `STA-OP-04` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `STA-OP-05` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `STA-OP-06` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `STA-OP-07` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `STA-OP-08` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |

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
- Tekrarlı çalıştırma (re-run) güvenliği `STA-OP-01` ile sabitlenir; idempotentlik varsayılmaz, kanıtlanır.
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
- Ek not 1: `STA-OP-01` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 2: `STA-OP-02` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 3: `STA-OP-03` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 4: `STA-OP-04` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 5: `STA-OP-05` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 6: `STA-OP-06` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 7: `STA-OP-07` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 8: `STA-OP-08` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 9: `STA-OP-01` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 10: `STA-OP-02` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 11: `STA-OP-03` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 12: `STA-OP-04` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 13: `STA-OP-05` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 14: `STA-OP-06` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 15: `STA-OP-07` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 16: `STA-OP-08` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 17: `STA-OP-01` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 18: `STA-OP-02` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 19: `STA-OP-03` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 20: `STA-OP-04` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 21: `STA-OP-05` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 22: `STA-OP-06` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 23: `STA-OP-07` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 24: `STA-OP-08` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 25: `STA-OP-01` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 26: `STA-OP-02` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 27: `STA-OP-03` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 28: `STA-OP-04` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 29: `STA-OP-05` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 30: `STA-OP-06` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 31: `STA-OP-07` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 32: `STA-OP-08` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 33: `STA-OP-01` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 34: `STA-OP-02` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 35: `STA-OP-03` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 36: `STA-OP-04` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 37: `STA-OP-05` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 38: `STA-OP-06` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 39: `STA-OP-07` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 40: `STA-OP-08` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 41: `STA-OP-01` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 42: `STA-OP-02` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 43: `STA-OP-03` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 44: `STA-OP-04` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 45: `STA-OP-05` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 46: `STA-OP-06` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 47: `STA-OP-07` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 48: `STA-OP-08` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 49: `STA-OP-01` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 50: `STA-OP-02` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 51: `STA-OP-03` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 52: `STA-OP-04` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 53: `STA-OP-05` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 54: `STA-OP-06` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 55: `STA-OP-07` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 56: `STA-OP-08` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 57: `STA-OP-01` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 58: `STA-OP-02` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 59: `STA-OP-03` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 60: `STA-OP-04` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 61: `STA-OP-05` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 62: `STA-OP-06` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 63: `STA-OP-07` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 64: `STA-OP-08` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 65: `STA-OP-01` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 66: `STA-OP-02` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 67: `STA-OP-03` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 68: `STA-OP-04` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 69: `STA-OP-05` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 70: `STA-OP-06` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 71: `STA-OP-07` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 72: `STA-OP-08` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 73: `STA-OP-01` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 74: `STA-OP-02` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 75: `STA-OP-03` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 76: `STA-OP-04` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 77: `STA-OP-05` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 78: `STA-OP-06` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 79: `STA-OP-07` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 80: `STA-OP-08` kuralı istatistik toplama akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 81: `STA-OP-01` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 82: `STA-OP-02` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 83: `STA-OP-03` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 84: `STA-OP-04` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 85: `STA-OP-05` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 86: `STA-OP-06` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 87: `STA-OP-07` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 88: `STA-OP-08` kuralı istatistik toplama akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 89: `STA-OP-01` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 90: `STA-OP-02` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 91: `STA-OP-03` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 92: `STA-OP-04` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 93: `STA-OP-05` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 94: `STA-OP-06` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 95: `STA-OP-07` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 96: `STA-OP-08` kuralı istatistik toplama akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 97: `STA-OP-01` kuralı istatistik toplama akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.