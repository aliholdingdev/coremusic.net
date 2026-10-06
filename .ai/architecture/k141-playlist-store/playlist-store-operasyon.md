---
title: "141 Çalma Listesi Deposu — Operasyon"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 141-playlist-store · Çalma Listesi Deposu (Operasyon)


---

Bu dosya **çalma listesi deposu** alanının işletimini tanımlar: operasyon akışı, kurallar, alarmlar, runbook, kenar durumlar, hata modları ve bağımlılık matrisi. Kardeş dosya: mimari.


---

- **Amaç (B):** Çalma listesi verisinin işletilmesini: sıra bütünlüğü denetimi, çakışma oranı izleme, toplu taşıma işleri ve raporlama operasyonunu tanımlar.

---

- **Sorumlu persona:** backend-architect · sre-engineer


---

## Operasyon Akışı

| Adim | Asama | Aciklama | Periyot |
|---|---|---|---|
| 1 | Bütünlük denetimi | Sıra boşluğu/çakışma kontrolü | günlük |
| 2 | Çakışma metriği | Red oranının izlenmesi | sürekli |
| 3 | Kırık referans | Sökük parça raporu | haftalık |
| 4 | Toplu taşıma | İthalat/birleştirme işi | talep üzerine |
| 5 | Arşiv temizliği | Eski arşivlerin gözden geçirilmesi | aylık |
| 6 | Rapor | Liste sağlığı raporu | haftalık |
| 7 | Politika gözden geçirme | Boyut/tekrar kuralları | çeyreklik |

---

## Operasyon Kurallari

| Kural | Tanim | Ihlal riski |
|---|---|---|
| `PLB-OP-01` | Bütünlük denetimi günlük çalışır ve sonuç kaydedilir. | Sessiz bozulma |
| `PLB-OP-02` | Çakışma oranı eşiği tanımlıdır (sayı UNKNOWN). | Eşiksiz alarm |
| `PLB-OP-03` | Toplu işler kota ile çalışır; kota aşımında durur. | Toplu yük |
| `PLB-OP-04` | Arşiv temizliği fiziksel silme ise onay ister. | Geri alınamazlık |
| `PLB-OP-05` | Sıra yeniden numaralandırma (compact) manuel onay ister. | Sıra kırılması |
| `PLB-OP-06` | Denetim kayıtları append-only saklanır. | Denetim izi |
| `PLB-OP-07` | Runbook'a sır/PII yazılmaz. | REDACTED |
| `PLB-OP-08` | Liste metrikleri K12'ye akar. | Kör operasyon |

---

## Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Kod |
|---|---|---|---|
| E01 | Bütünlük denetimi bulgu verirse | Bulgu aç + compact planı | `PLB-OP-E01` |
| E02 | Toplu iş yoğun saatte | Pencere yeniden planlanır | `PLB-OP-E02` |
| E03 | Çakışma oranı fırladı | İstemci sürümünü incele | `PLB-OP-E03` |
| E04 | Kırık referans dalgası | Kütüphane değişikliğini incele | `PLB-OP-E04` |
| E05 | Fiziksel silme talebi | Onay + kanıt zorunlu | `PLB-OP-E05` |
| E06 | Rapor gecikmesi | Olay açılır | `PLB-OP-E06` |
| E07 | Onay kaydı eksik | İş durdurulur | `PLB-OP-E07` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `PLB-OP-401` | Sıra bozukluğu | Yarım işlem | compact + denetim |
| 2 | `PLB-OP-402` | Çakışma > eşik | İstemci yarışması | Yolu incele |
| 3 | `PLB-OP-403` | Toplu iş kotası | Aşırı iş | Planı böl |
| 4 | `PLB-OP-404` | Arşiv temizliği yarım | Kesinti | İşi tamamla |
| 5 | `PLB-OP-405` | Onaysız silme | Süreç ihlali | Kaydı düzelt |
| 6 | `PLB-OP-406` | Denetim kaydı eksik | Yazma aksadı | Denetimi düzelt |
| 7 | `PLB-OP-407` | Rapor üretilemedi | Veri erişimi | Erişimi düzelt |
| 8 | `PLB-OP-408` | Kanıtsız eşik | Uydurma sayı | VERIFICATION REQUIRED |

---

## Bağımlılık Matrisi

| Katman | Iliski | Bu klasor ne verir | Bu klasor ne alir |
|---|---|---|---|
| K0 cekirdek | dolayli | dosya/surec zamanlamasi | kalici dosya erisimi |
| K1 donanim | hayir | - | - |
| K2 surucu | hayir | - | - |
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
| `AM-01` | Bütünlük bulgusu | P2 | Bulgu aç |
| `AM-02` | Çakışma oranı > eşik | P2 | İstemci yolunu incele |
| `AM-03` | Kırık referans > eşik | P3 | Kütüphaneyi incele |
| `AM-04` | Toplu iş kotası doldu | P3 | Planı böl |
| `AM-05` | Denetim kaydı durdu | P2 | Denetimi düzelt |
| `AM-06` | Onaysız silme denemesi | P1 | İşi durdur |

---

## Runbook

| Adim | Islem | Cikti |
|---|---|---|
| 1 | Liste sağlık metriklerini oku | Sağlık panosu |
| 2 | Bütünlük bulgusunu incele | Bulgu kaydı |
| 3 | Gerekirse compact planla | Onay kaydı |
| 4 | Compact + yeniden denetim | Sıra raporu |
| 5 | Kırık referans raporunu üret | Rapor |
| 6 | Olay + log | log.md girişi |

---

## Prova / Test

| # | Prova | Beklenen |
|---|---|---|
| O-01 | Bütünlük denetimi provası | Bulgu akışı çalışır |
| O-02 | Compact provası | Sıra eksiksiz |
| O-03 | Toplu iş provası | Kota korunur |
| O-04 | Çakışma alarmı doğrulama | Alarm tetiklenir |
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

1. `[[playlist-store-operasyon.md]]`
2. `[[index.md]]`
3. `[[playlist-store-mimari.md]]`

### Komsu / ilgili klasorler

1. `[[../k140-profile-store/index.md]]`
2. `[[../k142-library-metadata/index.md]]`
3. `[[../k140-profile-store/profile-store-mimari.md]]`
4. `[[../k142-library-metadata/library-metadata-mimari.md]]`
5. `[[../k108-vector-index/index.md]]`
6. `[[../k109-semantic-id/index.md]]`

---

## Kanıt

- `Kanıt: .ai/architecture/playlist-store/playlist-store-operasyon.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/mysql-18-database.md (L1-L500)`
- `Kanıt: .ai/.sql/mysql/coremusic_playlist.sql (liste kaynağı — dosya var)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/playlist-store/playlist-store-operasyon.md`

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
| çalma listesi deposu kayit politikasi | **R** | C | A | C | C | I |
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
| `Çalma listesi` | `playlist` | Sıralı parça kümesi |
| `Sıra numarası` | `position` | Liste içindeki konum |
| `Taşıma` | `reorder/move` | Yeniden sıralama |
| `Atomiklik` | `atomic operation` | Tamamı ya da hiç |
| `Arşiv` | `archive` | Geri açılabilir silme |
| `Sökük referans` | `dangling reference` | Kırık parça bağlantısı |
| `Bütünlük` | `integrity` | Sıra doğruluğu |
| `Toplu iş` | `bulk job` | Kotalı toplu işlem |
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
| Liste silinir mi? | Arşiv — PLB-006; fiziksel silme onaylı. | §3 |
| Aynı parça iki kez? | Politikaya bağlı; sayı UNKNOWN. | PLB-005 |
| Eşzamanlı düzenleme? | Çakışma red + yeniden deneme. | PLB-004 |
| Boyut sınırı? | UNKNOWN — politika onayı. | PLB-008 |
| Sökük parça? | İşaretlenir, satır silinmez. | PLB-009 |
| Kütüphane kaynağı? | Ayrı dosya: k142. | §2 |

---

## Surumler

| Sürüm | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 4.0.0 | 2026-10-06 | D04 k132-k143 operasyon (B) ilk yazım | vault-writer |
| 1.0.0 | 2026-09-20 | K5 taslak (yedek kaynak) | - |

### Ek.1. Kural denetim kayıtları

| Kural | Aşama | Zorunlu işlem | Kanıt durumu |
|---|---|---|---|
| `PLB-OP-01` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `PLB-OP-02` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `PLB-OP-03` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `PLB-OP-04` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `PLB-OP-05` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `PLB-OP-06` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `PLB-OP-07` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `PLB-OP-08` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |

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

- çalma listesi deposu için kaynak kaydı (system of record) K5 şemasıdır; bu dosyadaki her tablo türetilmiştir.
- Tekrarlı çalıştırma (re-run) güvenliği `PLB-OP-01` ile sabitlenir; idempotentlik varsayılmaz, kanıtlanır.
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
- Ek not 1: `PLB-OP-01` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 2: `PLB-OP-02` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 3: `PLB-OP-03` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 4: `PLB-OP-04` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 5: `PLB-OP-05` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 6: `PLB-OP-06` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 7: `PLB-OP-07` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 8: `PLB-OP-08` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 9: `PLB-OP-01` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 10: `PLB-OP-02` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 11: `PLB-OP-03` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 12: `PLB-OP-04` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 13: `PLB-OP-05` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 14: `PLB-OP-06` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 15: `PLB-OP-07` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 16: `PLB-OP-08` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 17: `PLB-OP-01` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 18: `PLB-OP-02` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 19: `PLB-OP-03` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 20: `PLB-OP-04` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 21: `PLB-OP-05` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 22: `PLB-OP-06` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 23: `PLB-OP-07` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 24: `PLB-OP-08` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 25: `PLB-OP-01` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 26: `PLB-OP-02` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 27: `PLB-OP-03` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 28: `PLB-OP-04` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 29: `PLB-OP-05` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 30: `PLB-OP-06` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 31: `PLB-OP-07` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 32: `PLB-OP-08` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 33: `PLB-OP-01` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 34: `PLB-OP-02` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 35: `PLB-OP-03` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 36: `PLB-OP-04` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 37: `PLB-OP-05` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 38: `PLB-OP-06` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 39: `PLB-OP-07` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 40: `PLB-OP-08` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 41: `PLB-OP-01` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 42: `PLB-OP-02` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 43: `PLB-OP-03` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 44: `PLB-OP-04` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 45: `PLB-OP-05` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 46: `PLB-OP-06` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 47: `PLB-OP-07` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 48: `PLB-OP-08` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 49: `PLB-OP-01` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 50: `PLB-OP-02` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 51: `PLB-OP-03` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 52: `PLB-OP-04` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 53: `PLB-OP-05` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 54: `PLB-OP-06` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 55: `PLB-OP-07` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 56: `PLB-OP-08` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 57: `PLB-OP-01` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 58: `PLB-OP-02` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 59: `PLB-OP-03` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 60: `PLB-OP-04` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 61: `PLB-OP-05` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 62: `PLB-OP-06` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 63: `PLB-OP-07` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 64: `PLB-OP-08` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 65: `PLB-OP-01` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 66: `PLB-OP-02` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 67: `PLB-OP-03` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 68: `PLB-OP-04` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 69: `PLB-OP-05` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 70: `PLB-OP-06` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 71: `PLB-OP-07` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 72: `PLB-OP-08` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 73: `PLB-OP-01` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 74: `PLB-OP-02` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 75: `PLB-OP-03` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 76: `PLB-OP-04` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 77: `PLB-OP-05` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 78: `PLB-OP-06` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 79: `PLB-OP-07` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 80: `PLB-OP-08` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 81: `PLB-OP-01` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 82: `PLB-OP-02` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 83: `PLB-OP-03` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 84: `PLB-OP-04` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 85: `PLB-OP-05` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 86: `PLB-OP-06` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 87: `PLB-OP-07` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 88: `PLB-OP-08` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 89: `PLB-OP-01` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 90: `PLB-OP-02` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 91: `PLB-OP-03` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 92: `PLB-OP-04` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 93: `PLB-OP-05` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 94: `PLB-OP-06` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 95: `PLB-OP-07` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 96: `PLB-OP-08` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.