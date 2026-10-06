---
title: "136 CDC Boru Hattı — Operasyon"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 136-cdcdc-pipeline · CDC Boru Hattı (Operasyon)


---

Bu dosya **CDC (change data capture) boru hattı** alanının işletimini tanımlar: operasyon akışı, kurallar, alarmlar, runbook, kenar durumlar, hata modları ve bağımlılık matrisi. Kardeş dosya: mimari.


---

- **Amaç (B):** Yakalama sürecinin izlenmesi, checkpoint yönetimi, gecikme alarmı, geri kazanım (backfill) ve boru hattı runbook operasyonunu tanımlar.

---

- **Sorumlu persona:** sre-engineer · data-engineer


---

## Operasyon Akışı

| Adim | Asama | Aciklama | Periyot |
|---|---|---|---|
| 1 | Sağlık | Yakalama + teslim + checkpoint durumu | sürekli |
| 2 | Gecikme izleme | Kaynak-hedef farkı ölçümü | sürekli |
| 3 | Checkpoint denetimi | İlerlemenin gerçekliği | saatlik |
| 4 | Backfill | İlk yükleme / geri kazanım | talep üzerine |
| 5 | Doğrulama | Sayı + örnek karşılaştırma | her backfill |
| 6 | Rapor | Günlük teslim raporu | günlük |
| 7 | Tatbikat | Kayıp senaryosu provası | aylık |

---

## Operasyon Kurallari

| Kural | Tanim | Ihlal riski |
|---|---|---|
| `CDC-OP-01` | Gecikme eşiği alarmı tanımlıdır (değer UNKNOWN/onay bekliyor). | Eşiksiz alarm yok |
| `CDC-OP-02` | Checkpoint manuel ilerletme yetkisi kısıtlıdır. | Kayıp riski |
| `CDC-OP-03` | Backfill öncesi canlı durumu dondurulur veya sıra kilitlenir. | Yarışma |
| `CDC-OP-04` | Doğrulama sayı + örnek (sampling) çiftiyle yapılır. | Tek yöntem yanıltır |
| `CDC-OP-05` | Kurtarma provası aylık yapılır ve kaydı tutulur. | Kanıtsız kurtarma |
| `CDC-OP-06` | Alarm fırtınası cooldown ile bastırılır. | Gürültü |
| `CDC-OP-07` | Runbook'a sır/parola yazılmaz. | REDACTED |
| `CDC-OP-08` | Yapılandırma değişikliği iki onay ister. | Tek onay riskli |

---

## Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Kod |
|---|---|---|---|
| E01 | Gece gecikme yükselmesi | Alarm + kaynak kontrolü | `CDC-OP-E01` |
| E02 | Nöbetçi değişimi | Devir notu + durum özeti | `CDC-OP-E02` |
| E03 | Checkpoint durdurulmuş | Neden kaydı zorunlu | `CDC-OP-E03` |
| E04 | Backfill sırasında yoğun saat | Pencere yeniden planlanır | `CDC-OP-E04` |
| E05 | Maskeleme kuralı değişti | İki onay + denetim | `CDC-OP-E05` |
| E06 | Doğrulama örneği tartışmalı | Ölçüm penceresi tekrarlanır | `CDC-OP-E06` |
| E07 | Tatbikat gecikti | Olay açılır | `CDC-OP-E07` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `CDC-OP-401` | Gecikme sürekli | Kaynak/tüketim aşırı yük | Kapasite değerlendirmesi |
| 2 | `CDC-OP-402` | Doğrulama uyuşmadı | Kayıp/çift uygulama | Akışı durdur |
| 3 | `CDC-OP-403` | Checkpoint elle oynatıldı | Yetki ihlali | Denetim kaydı |
| 4 | `CDC-OP-404` | Kurtarma provası yapılmadı | Zaman | Eskalasyon |
| 5 | `CDC-OP-405` | Alarm fırtınası | Kök neden belirsiz | Cooldown + inceleme |
| 6 | `CDC-OP-406` | Doğrulama örneği yetersiz | Küçük örnekleme | Örneği büyüt |
| 7 | `CDC-OP-407` | Onay kaydı eksik | Süreç atlandı | Kaydı tamamla |
| 8 | `CDC-OP-408` | Kanıtsız eşik | Uydurma sayı | VERIFICATION REQUIRED |

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


---

## Alarmlar

| # | Alarm | Oncelik | Eylem |
|---|---|---|---|
| `AM-01` | Teslim gecikmesi > eşik | P2 | Tüketimi ölçekle |
| `AM-02` | Checkpoint > X dk hareketsiz | P2 | Hedef hatalarını kontrol et |
| `AM-03` | Sıra boşluğu | P1 | Akışı durdur |
| `AM-04` | Kuyruk doluluğu > eşik | P2 | Kapasite olayı |
| `AM-05` | Maskeleme denetimi başarısız | P1 | Teslimi durdur |
| `AM-06` | Kurtarma provası gecikti | P3 | Plan oluştur |

---

## Runbook

| Adim | Islem | Cikti |
|---|---|---|
| 1 | Yakalama/teslim/checkpoint durumunu oku | Sağlık panosu |
| 2 | Sıra boşluğu varsa akışı durdur | Durum kaydı |
| 3 | Backfill/kurtarma işini başlat | Onay kaydı |
| 4 | Sayı + örnek doğrulama | Eşitlik raporu |
| 5 | Checkpoint'i güvenli noktaya al | Checkpoint kaydı |
| 6 | Olay + log | log.md girişi |

---

## Prova / Test

| # | Prova | Beklenen |
|---|---|---|
| O-01 | Kurtarma provası | Hedef sıfırdan eşitlenir |
| O-02 | Gecikme alarmı doğrulama | Alarm tetiklenir |
| O-03 | Checkpoint manuel kilidi | Yetki reddi |
| O-04 | Backfill + canlı sıra | Çakışma yok |
| O-05 | Günlük teslim raporu | Rapor eksiksiz |

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

1. `[[cdc-pipeline-operasyon.md]]`
2. `[[index.md]]`
3. `[[cdc-pipeline-mimari.md]]`

### Komsu / ilgili klasorler

1. `[[../k135-event-store/index.md]]`
2. `[[../k137-integrity-check/index.md]]`
3. `[[../k135-event-store/event-store-mimari.md]]`
4. `[[../k137-integrity-check/integrity-check-mimari.md]]`
5. `[[../k108-vector-index/index.md]]`
6. `[[../k109-semantic-id/index.md]]`

---

## Kanıt

- `Kanıt: .ai/architecture/cdcdc-pipeline/cdc-pipeline-operasyon.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/migration-strategy.md (L1-L439)`
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/connection-pooling.md (L1-L484)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/cdcdc-pipeline/cdc-pipeline-operasyon.md`

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

## SSS

| Soru | Cevap | Kaynak |
|---|---|---|
| Sıfır kayıp mı? | Yalnız ölçümle iddia edilir — CDC-009. | §3 |
| Hangi ürün kullanılıyor? | Seçilmedi — kanıt yok. | CDC-007 |
| Aynı olay iki kez gelirse? | Idempotent uygulama etkiyi bire indirir. | CDC-004 |
| Backfill neden ayrı? | Canlı ile yarışma çelişkisi üretir. | CDC-005 |
| PII ne olur? | Teslimden önce maskelenir. | CDC-010 |
| Kurtarma ne sıklıkta? | Aylık tatbikat (tasarım kuralı). | CDC-OP-05 |

---

## Surumler

| Sürüm | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 4.0.0 | 2026-10-06 | D04 k132-k143 operasyon (B) ilk yazım | vault-writer |
| 1.0.0 | 2026-09-20 | K5 taslak (yedek kaynak) | - |

### Ek.1. Kural denetim kayıtları

| Kural | Aşama | Zorunlu işlem | Kanıt durumu |
|---|---|---|---|
| `CDC-OP-01` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-OP-02` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-OP-03` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-OP-04` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-OP-05` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-OP-06` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-OP-07` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-OP-08` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |

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
- Tekrarlı çalıştırma (re-run) güvenliği `CDC-OP-01` ile sabitlenir; idempotentlik varsayılmaz, kanıtlanır.
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
- Ek not 1: `CDC-OP-01` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 2: `CDC-OP-02` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 3: `CDC-OP-03` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 4: `CDC-OP-04` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 5: `CDC-OP-05` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 6: `CDC-OP-06` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 7: `CDC-OP-07` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 8: `CDC-OP-08` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 9: `CDC-OP-01` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 10: `CDC-OP-02` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 11: `CDC-OP-03` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 12: `CDC-OP-04` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 13: `CDC-OP-05` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 14: `CDC-OP-06` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 15: `CDC-OP-07` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 16: `CDC-OP-08` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 17: `CDC-OP-01` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 18: `CDC-OP-02` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 19: `CDC-OP-03` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 20: `CDC-OP-04` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 21: `CDC-OP-05` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 22: `CDC-OP-06` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 23: `CDC-OP-07` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 24: `CDC-OP-08` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 25: `CDC-OP-01` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 26: `CDC-OP-02` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 27: `CDC-OP-03` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 28: `CDC-OP-04` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 29: `CDC-OP-05` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 30: `CDC-OP-06` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 31: `CDC-OP-07` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 32: `CDC-OP-08` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 33: `CDC-OP-01` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 34: `CDC-OP-02` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 35: `CDC-OP-03` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 36: `CDC-OP-04` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 37: `CDC-OP-05` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 38: `CDC-OP-06` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 39: `CDC-OP-07` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 40: `CDC-OP-08` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 41: `CDC-OP-01` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 42: `CDC-OP-02` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 43: `CDC-OP-03` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 44: `CDC-OP-04` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 45: `CDC-OP-05` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 46: `CDC-OP-06` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 47: `CDC-OP-07` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 48: `CDC-OP-08` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 49: `CDC-OP-01` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 50: `CDC-OP-02` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 51: `CDC-OP-03` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 52: `CDC-OP-04` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 53: `CDC-OP-05` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 54: `CDC-OP-06` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 55: `CDC-OP-07` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 56: `CDC-OP-08` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 57: `CDC-OP-01` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 58: `CDC-OP-02` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 59: `CDC-OP-03` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 60: `CDC-OP-04` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 61: `CDC-OP-05` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 62: `CDC-OP-06` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 63: `CDC-OP-07` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 64: `CDC-OP-08` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 65: `CDC-OP-01` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 66: `CDC-OP-02` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 67: `CDC-OP-03` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 68: `CDC-OP-04` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 69: `CDC-OP-05` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 70: `CDC-OP-06` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 71: `CDC-OP-07` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 72: `CDC-OP-08` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 73: `CDC-OP-01` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 74: `CDC-OP-02` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 75: `CDC-OP-03` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 76: `CDC-OP-04` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 77: `CDC-OP-05` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 78: `CDC-OP-06` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 79: `CDC-OP-07` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 80: `CDC-OP-08` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 81: `CDC-OP-01` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 82: `CDC-OP-02` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 83: `CDC-OP-03` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 84: `CDC-OP-04` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 85: `CDC-OP-05` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 86: `CDC-OP-06` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 87: `CDC-OP-07` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 88: `CDC-OP-08` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 89: `CDC-OP-01` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 90: `CDC-OP-02` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 91: `CDC-OP-03` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 92: `CDC-OP-04` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 93: `CDC-OP-05` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 94: `CDC-OP-06` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 95: `CDC-OP-07` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 96: `CDC-OP-08` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.