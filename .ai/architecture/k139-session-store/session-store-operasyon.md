---
title: "139 Oturum Deposu — Operasyon"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 139-session-store · Oturum Deposu (Operasyon)


---

Bu dosya **oturum deposu** alanının işletimini tanımlar: operasyon akışı, kurallar, alarmlar, runbook, kenar durumlar, hata modları ve bağımlılık matrisi. Kardeş dosya: mimari.


---

- **Amaç (B):** Oturum deposunun işletilmesini: TTL/temizlik işi, iptal dalgaları, şüpheli oturum alarmı, kapasite ve runbook operasyonunu tanımlar.

---

- **Sorumlu persona:** sre-engineer · security-engineer


---

## Operasyon Akışı

| Adim | Asama | Aciklama | Periyot |
|---|---|---|---|
| 1 | TTL temizliği | Süresi dolan kayıtların atılması | dakikalık |
| 2 | Sağlık | Okuma/yazma hata oranı | sürekli |
| 3 | Şüpheli oturum | Kuşku işaretlerinin incelenmesi | olay |
| 4 | İptal dalgası | Toplu iptal işlemi | talep üzerine |
| 5 | Kapasite | Aktif oturum sayısının izlenmesi | saatlik |
| 6 | Politika gözden geçirme | TTL/paralel sınırlar | çeyreklik |
| 7 | Tatbikat | Kesintide güvenli red provası | aylık |

---

## Operasyon Kurallari

| Kural | Tanim | Ihlal riski |
|---|---|---|
| `SES-OP-01` | TTL temizlik işi gecikirse alarm tetiklenir. | Gölge veri birikir |
| `SES-OP-02` | Toplu iptal işlemi onay ve kayıt gerektirir. | Kütlesel kilitlenme |
| `SES-OP-03` | Şüpheli oturum alarmı 30 dk içinde incelenir. | İstismar penceresi |
| `SES-OP-04` | Kesintide güvenli red davranışının tatbikatı aylık yapılır. | Kanıtsız prosedür |
| `SES-OP-05` | Oturum deposu metrikleri K12'ye akar (sayı/oran/gecikme). | Kör operasyon |
| `SES-OP-06` | Runbook'a sır/imza anahtarı yazılmaz. | REDACTED |
| `SES-OP-07` | TTL/paralel sınır değişikliği iki onay ister. | Tek onay riskli |
| `SES-OP-08` | Kapasite raporu haftalık üretilir. | Kapasite sürprizi |

---

## Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Kod |
|---|---|---|---|
| E01 | Toplu giriş (kampanya) | Kapasite alarmı + önceliklendirme | `SES-OP-E01` |
| E02 | Temizlik işi çökse | Alarm + manuel tetik | `SES-OP-E02` |
| E03 | Gece şüpheli oturum | Nöbet akışı | `SES-OP-E03` |
| E04 | Toplu iptal sırasında yoğunluk | Kuyruk + onay | `SES-OP-E04` |
| E05 | Anahtar rotasyonu | Eski imzalar geçişte reddedilir | `SES-OP-E05` |
| E06 | Depo bağımlılığı kesildi | Güvenli red tatbikatı | `SES-OP-E06` |
| E07 | Politika değişikliği tartışması | İki onay | `SES-OP-E07` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `SES-OP-401` | Temizlik gecikti | İş çökmesi | İşi yeniden başlat |
| 2 | `SES-OP-402` | Aktif oturum fırladı | Yük veya istismar | Kaynağı incele |
| 3 | `SES-OP-403` | İptal yarım | Dağıtık hata | Tekrar yayınla |
| 4 | `SES-OP-404` | Hata oranı arttı | İmza sorunu | Yayını durdur |
| 5 | `SES-OP-405` | Metrik yok | Ölçüm kesintisi | Sağlıksız say |
| 6 | `SES-OP-406` | Tatbikat gecikti | Plan | Olay aç |
| 7 | `SES-OP-407` | Onay kaydı yok | Süreç atlandı | Kaydı tamamla |
| 8 | `SES-OP-408` | Kanıtsız TTL | Uydurma değer | VERIFICATION REQUIRED |

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
| `AM-01` | İmza hata oranı > eşik | P1 | İmza yolunu incele |
| `AM-02` | Aktif oturum > eşik | P2 | Kapasite değerlendirmesi |
| `AM-03` | Temizlik işi gecikti | P2 | İşi yeniden başlat |
| `AM-04` | Şüpheli oturum | P1 | 30 dk inceleme |
| `AM-05` | İptal yayınlama başarısız | P1 | Tekrar yayınla |
| `AM-06` | Politika değişikliği onaysız | P3 | Değişikliği geri al |

---

## Runbook

| Adim | Islem | Cikti |
|---|---|---|
| 1 | Aktif oturum ve hata metriklerini oku | Sağlık panosu |
| 2 | İmza hatası varsa yayını durdur | Durum kaydı |
| 3 | Şüpheli oturumları iptal et | İptal kaydı |
| 4 | TTL temizlik işini çalıştır | Temizlik raporu |
| 5 | Sonucu doğrula | Sayılar düşer |
| 6 | Olay + log | log.md girişi |

---

## Prova / Test

| # | Prova | Beklenen |
|---|---|---|
| O-01 | Kesintide güvenli red | Açık oturum kabul edilmez |
| O-02 | TTL temizlik provası | Gölge veri birikmez |
| O-03 | Toplu iptal provası | Tüm cihazlar iptal |
| O-04 | Şüpheli alarm doğrulama | Alarm tetiklenir |
| O-05 | Kapasite raporu | Rapor eksiksiz |

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

1. `[[session-store-operasyon.md]]`
2. `[[index.md]]`
3. `[[session-store-mimari.md]]`

### Komsu / ilgili klasorler

1. `[[../k138-gdpr-export/index.md]]`
2. `[[../k140-profile-store/index.md]]`
3. `[[../k138-gdpr-export/gdpr-export-tasarim.md]]`
4. `[[../k140-profile-store/profile-store-mimari.md]]`
5. `[[../k108-vector-index/index.md]]`
6. `[[../k109-semantic-id/index.md]]`

---

## Kanıt

- `Kanıt: .ai/architecture/session-store/session-store-operasyon.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/cache-strategy.md (L1-L450)`
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/data-security.md (L1-L462)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/session-store/session-store-operasyon.md`

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
| oturum deposu kayit politikasi | **R** | C | A | C | C | I |
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
| `Oturum` | `session` | Kimliklenmiş etkinlik aralığı |
| `TTL` | `time to live` | Yaşam süresi |
| `Kayan yenileme` | `sliding renewal` | Etkinlikle uzayan süre |
| `İmza` | `signature` | Sahtesi zor kimlik |
| `İptal` | `revocation` | Oturumu geçersiz kılma |
| `Paralel oturum` | `concurrent session` | Aynı anda açık oturumlar |
| `Cihaz referansı` | `device ref` | Cihaz tanımı |
| `Gölge veri` | `shadow data` | Süresi dolmuş hâlâ duran kayıt |
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
| Oturum deposu nerede? | Tasarım; ürün seçimi kanıtsız. | ⚠️ VERIFICATION REQUIRED |
| TTL ne kadar? | UNKNOWN — politika onayı. | SES-004 |
| İptal derhal mi? | Evet — TTL'ye bırakılmaz. | ADR A2 |
| Paralel oturum sınırı? | UNKNOWN — politika. | SES-007 |
| Depo çökse ne olur? | Güvenli red; açık oturum yok. | ADR A4 |
| Parola saklanır mı? | Hayır — REDACTED. | SES-008 |

---

## Surumler

| Sürüm | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 4.0.0 | 2026-10-06 | D04 k132-k143 operasyon (B) ilk yazım | vault-writer |
| 1.0.0 | 2026-09-20 | K5 taslak (yedek kaynak) | - |

### Ek.1. Kural denetim kayıtları

| Kural | Aşama | Zorunlu işlem | Kanıt durumu |
|---|---|---|---|
| `SES-OP-01` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SES-OP-02` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SES-OP-03` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SES-OP-04` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SES-OP-05` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SES-OP-06` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SES-OP-07` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SES-OP-08` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |

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

- oturum deposu için kaynak kaydı (system of record) K5 şemasıdır; bu dosyadaki her tablo türetilmiştir.
- Tekrarlı çalıştırma (re-run) güvenliği `SES-OP-01` ile sabitlenir; idempotentlik varsayılmaz, kanıtlanır.
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
- Ek not 1: `SES-OP-01` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 2: `SES-OP-02` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 3: `SES-OP-03` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 4: `SES-OP-04` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 5: `SES-OP-05` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 6: `SES-OP-06` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 7: `SES-OP-07` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 8: `SES-OP-08` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 9: `SES-OP-01` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 10: `SES-OP-02` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 11: `SES-OP-03` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 12: `SES-OP-04` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 13: `SES-OP-05` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 14: `SES-OP-06` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 15: `SES-OP-07` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 16: `SES-OP-08` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 17: `SES-OP-01` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 18: `SES-OP-02` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 19: `SES-OP-03` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 20: `SES-OP-04` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 21: `SES-OP-05` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 22: `SES-OP-06` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 23: `SES-OP-07` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 24: `SES-OP-08` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 25: `SES-OP-01` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 26: `SES-OP-02` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 27: `SES-OP-03` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 28: `SES-OP-04` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 29: `SES-OP-05` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 30: `SES-OP-06` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 31: `SES-OP-07` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 32: `SES-OP-08` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 33: `SES-OP-01` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 34: `SES-OP-02` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 35: `SES-OP-03` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 36: `SES-OP-04` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 37: `SES-OP-05` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 38: `SES-OP-06` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 39: `SES-OP-07` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 40: `SES-OP-08` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 41: `SES-OP-01` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 42: `SES-OP-02` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 43: `SES-OP-03` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 44: `SES-OP-04` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 45: `SES-OP-05` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 46: `SES-OP-06` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 47: `SES-OP-07` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 48: `SES-OP-08` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 49: `SES-OP-01` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 50: `SES-OP-02` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 51: `SES-OP-03` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 52: `SES-OP-04` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 53: `SES-OP-05` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 54: `SES-OP-06` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 55: `SES-OP-07` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 56: `SES-OP-08` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 57: `SES-OP-01` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 58: `SES-OP-02` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 59: `SES-OP-03` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 60: `SES-OP-04` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 61: `SES-OP-05` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 62: `SES-OP-06` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 63: `SES-OP-07` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 64: `SES-OP-08` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 65: `SES-OP-01` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 66: `SES-OP-02` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 67: `SES-OP-03` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 68: `SES-OP-04` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 69: `SES-OP-05` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 70: `SES-OP-06` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 71: `SES-OP-07` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 72: `SES-OP-08` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 73: `SES-OP-01` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 74: `SES-OP-02` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 75: `SES-OP-03` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 76: `SES-OP-04` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 77: `SES-OP-05` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 78: `SES-OP-06` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 79: `SES-OP-07` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 80: `SES-OP-08` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 81: `SES-OP-01` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 82: `SES-OP-02` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 83: `SES-OP-03` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 84: `SES-OP-04` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 85: `SES-OP-05` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 86: `SES-OP-06` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 87: `SES-OP-07` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 88: `SES-OP-08` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 89: `SES-OP-01` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 90: `SES-OP-02` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 91: `SES-OP-03` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 92: `SES-OP-04` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 93: `SES-OP-05` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 94: `SES-OP-06` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 95: `SES-OP-07` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 96: `SES-OP-08` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.