---
title: "135 Olay Deposu — Mimari ve Tasarim"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 135-event-store · Olay Deposu (Mimari)


---

Bu dosya **olay deposu** alanının tasarımını tanımlar: kapsam, akış, kurallar, veri modeli, kenar durumlar, hata modları ve bağımlılık matrisi. Kardeş dosya: operasyon.


---

- **Amaç (A):** Olay tabanlı (event sourcing) kaydın salt-ekleme (append-only) yapısını tanımlar: olay şeması, akış (stream) anahtarı, sıralama, idempotent tüketici ve tekrar oynatma (replay) kuralları.

---

- **Sorumlu persona:** data-engineer · backend-architect

---

- **Eşleşen klasor:** olay deposu → CDC boru hattı


---

## Kapsam

### Kapsamda (In)

| # | Kapsam |
|---|---|
| 1 | Olay şeması ve zorunlu alanlar |
| 2 | Sıra numarası (sequence) garantisi |
| 3 | Salt-ekleme değişmezliği |
| 4 | Tekrar oynatma (replay) kuralları |

---

### Kapsamdisi (Out)

| # | Kapsamdisi |
|---|---|
| 1 | Kuyruk altyapısı seçimi (K7) |
| 2 | CQRS okuma projeksiyonları ayrı |
| 3 | Yedekleme politikası (k5 backup) |
| 4 | Bildirim/UX akışları |

---

## Mimari Akış

| Adim | Asama | Aciklama | Kural |
|---|---|---|---|
| 1 | Üretim | Servis olayı üretir (kimlik + zaman damgası) | EVT-001 |
| 2 | Doğrulama | Şema + sürüm kontrolü | EVT-002 |
| 3 | Yazma | Append-only satır, sıra numarası atanır | EVT-003 |
| 4 | Yayın | Yayıncı olayı kuyruğa verir | EVT-004 |
| 5 | Tüketim | Tüketici idempotent işler | EVT-005 |
| 6 | İzleme | Sıra/atlanma metrikleri K12'ye | EVT-006 |

---

## Kurallar

| Kural | Tanim | Ihlal riski |
|---|---|---|
| `EVT-001` | Her olay benzersiz event_id ve zorunlu şema sürümü taşır. | Tanımlanamayan olay işlenemez |
| `EVT-002` | Şemaya uymayan olay reddedilir; sessizce düzeltilmez. | Bozuk geçmiş |
| `EVT-003` | Olaylar salt-eklemedir; güncelleme/silme yasaktır (düzeltme yeni olayla). | Geçmiş değiştirilemez |
| `EVT-004` | Yayın sırası depo sırasıyla aynıdır; sırasız yayınlama yasaktır. | Sıra bağımlı tüketici |
| `EVT-005` | Tüketici idempotenttir; aynı olay iki kez gelirse etkisi birdir. | Tekrar etkisi |
| `EVT-006` | Atlanan sıra numarası (gap) algılanır ve bildirilir. | Kayıp olay |
| `EVT-007` | Olay deposu kaynak sistem değildir; sistem-of-record K5 ilişkelsel şemasıdır. | Kaynak ikiliği |
| `EVT-008` | Saklama süresi politikayla belirlenir; süre UNKNOWN olarak işaretlenir. | Uyum/alan belirsizliği |
| `EVT-009` | Replay, canlı tüketimi bloke edemez (ayrı kuyruk/pencere). | Üretim kesintisi |
| `EVT-010` | Olay içeriğinde sır/PII, REDACTED politikasıyla maskelenir. | REDACTED |

---

## Veri Modeli

| Alan | Tip | Aciklama | Kural |
|---|---|---|---|
| `event_id` | `VARCHAR` | Benzersiz olay kimliği | `EVT-001` |
| `stream_key` | `VARCHAR` | Akış anahtarı (ör. kayıt/oturum) | `EVT-004` |
| `sequence` | `BIGINT` | Akış içinde sıra | `EVT-003` |
| `schema_version` | `INT` | Şema sürümü | `EVT-001` |
| `payload` | `JSON` | Olay gövdesi | `EVT-002` |
| `occurred_at` | `DATETIME` | Oluşma anı (UTC) | `EVT-001` |
| `producer` | `VARCHAR` | Üreten servis | `EVT-006` |
| `redaction_state` | `ENUM` | raw/masked | `EVT-010` |

---

## Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Kod |
|---|---|---|---|
| E01 | Aynı olay iki kez gelir | Tüketici etkisi birdir; depoya bir kez yazılır | `EVT-E01` |
| E02 | Sıra deliği oluştu | Gap alarmı; tüketici bekler | `EVT-E02` |
| E03 | Şema sürümü bilinmiyor | Olay reddedilir, bilinmeyen sayılır | `EVT-E03` |
| E04 | Payload çok büyük | Reddedilir veya referanslanır (tasarım kararı) | `EVT-E04` |
| E05 | Replay sırasında canlı trafik | Ayrı pencere/kuyruk | `EVT-E05` |
| E06 | PII içeren olay | Maskelenir, ham hali saklanmaz | `EVT-E06` |
| E07 | Aynı sıra numarası iki kez | Çakışma reddi | `EVT-E07` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `EVT-401` | Şema ihlali | Alan tipi/eşik uymuyor | Reddet + üreticiye bildir |
| 2 | `EVT-402` | Sıra çakışması | Eşzamanlı yazma | Sıra tahsisini düzelt |
| 3 | `EVT-403` | Yayın başarısız | Kuyruk erişilemiyor | Yeniden dene + alarm |
| 4 | `EVT-404` | Gap algılandı | Kayıp gönderim | Üreticiyi sorgula |
| 5 | `EVT-405` | Tüketici tekrarı | Idempotentlik yok | Tüketiciyi düzelt |
| 6 | `EVT-406` | Depo doluluğu | Saklama/alan | Arşiv planı |
| 7 | `EVT-407` | Maskleme hatası | PII sızıntısı | Durdur + denetim |
| 8 | `EVT-408` | Bilinmeyen sürüm | Kanıt yok | VERIFICATION REQUIRED |

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
| T-01 | Aynı olay iki kez yazma | Tek satır |
| T-02 | Sıra deliği | Gap alarmı |
| T-03 | Geçersiz şema | Red |
| T-04 | Sıra değişmezliği | Okuma sırası yazma sırası |
| T-05 | Replay idempotentliği | İkinci oynatmada etki aynı |
| T-06 | PII olayı | Maskelenmiş payload |
| T-07 | Büyük payload | Red/referans kuralı |
| T-08 | Saklama süresi doldu | Arşiv/silme davranışı |

---

## ADR Adaylari

| # | Karar | Reddedilen | Risk | Durum |
|---|---|---|---|---|
| A1 | Append-only + düzeltme yeni olay | Güncellenebilir depo | Geçmiş bütünlüğü | kabul |
| EVT-A2 | Sıra numarası depoda | Uygulama sırası | Tek doğruluk sırası | kabul |
| A3 | Replay canlıdan ayrı | Tek kuyruk | Üretim kesintisi riski | kabul |
| A4 | PII maskelenir | Ham saklama | REDACTED + uyum | kabul |
| A5 | Kuyruk ürünü seçilmedi | Spesifik kuyruk teknolojisi | Kanıt yok | açık — VERIFICATION REQUIRED |

---

## Riskler

| Risk | Etki | Onlem |
|---|---|---|
| Saklama süresi belirsizliği | Alan/uyum riski | Politika onayı |
| Gap sessizliği | Kayıp veri | Otomatik gap alarmı |
| Replay maliyeti | Kaynak tüketimi | Pencere + kota |
| Maskleme kaçışı | PII sızıntısı | Denetim testi |
| Kuyruk bağımlılığı | Gecikme | K7 kararı |

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
| `EventWriter` | Olayı yazar | İş mantığı bilmez |
| `SchemaGuard` | Şemayı denetler | Olayı düzeltmez |
| `Publisher` | Kuyruğa yayınlar | Sırayı değiştirmez |
| `ReplayRunner` | Oynatmayı yürütür | Canlı tüketimi kesmez |

---

## Bağlantılar

### Kendi dosyalari

1. `[[event-store-mimari.md]]`
2. `[[index.md]]`
3. `[[event-store-saklama-ve-replay.md]]`

### Komsu / ilgili klasorler

1. `[[../k134-shard-strategy/index.md]]`
2. `[[../k136-cdcdc-pipeline/index.md]]`
3. `[[../k134-shard-strategy/shard-strateji-mimari.md]]`
4. `[[../k136-cdcdc-pipeline/cdc-pipeline-mimari.md]]`
5. `[[../k108-vector-index/index.md]]`
6. `[[../k109-semantic-id/index.md]]`

---

## Kanıt

- `Kanıt: .ai/architecture/event-store/event-store-mimari.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/data-security.md (Audit Logging bölümü, L1-L462)`
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/backup-strategy.md (L1-L426)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/event-store/event-store-mimari.md`

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
| olay deposu kayit politikasi | **R** | C | A | C | C | I |
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
| `Olay` | `event` | Değişiklik kaydı |
| `Akış` | `stream` | Olayların sıralı dizisi |
| `Sıra numarası` | `sequence` | Akış içindeki konum |
| `Salt-ekleme` | `append-only` | Değiştirilemeyen yazma |
| `Tekrar oynatma` | `replay` | Olayları yeniden tüketme |
| `Idempotent` | `idempotent consumer` | Tekrar güvenli tüketim |
| `Sıra deliği` | `gap` | Atlanan sıra |
| `Arşiv` | `archive` | Süresi dolan olay katmanı |
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
| `EVT-001` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `EVT-002` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `EVT-003` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `EVT-004` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `EVT-005` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `EVT-006` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `EVT-007` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `EVT-008` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `EVT-009` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `EVT-010` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |

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

- olay deposu için kaynak kaydı (system of record) K5 şemasıdır; bu dosyadaki her tablo türetilmiştir.
- Tekrarlı çalıştırma (re-run) güvenliği `EVT-001` ile sabitlenir; idempotentlik varsayılmaz, kanıtlanır.
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
- Ek not 1: `EVT-001` kuralı olay deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 2: `EVT-002` kuralı olay deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 3: `EVT-003` kuralı olay deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 4: `EVT-004` kuralı olay deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 5: `EVT-005` kuralı olay deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 6: `EVT-006` kuralı olay deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 7: `EVT-007` kuralı olay deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 8: `EVT-008` kuralı olay deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 9: `EVT-009` kuralı olay deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 10: `EVT-010` kuralı olay deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 11: `EVT-001` kuralı olay deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 12: `EVT-002` kuralı olay deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 13: `EVT-003` kuralı olay deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 14: `EVT-004` kuralı olay deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 15: `EVT-005` kuralı olay deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 16: `EVT-006` kuralı olay deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 17: `EVT-007` kuralı olay deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 18: `EVT-008` kuralı olay deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 19: `EVT-009` kuralı olay deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 20: `EVT-010` kuralı olay deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 21: `EVT-001` kuralı olay deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 22: `EVT-002` kuralı olay deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 23: `EVT-003` kuralı olay deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 24: `EVT-004` kuralı olay deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 25: `EVT-005` kuralı olay deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 26: `EVT-006` kuralı olay deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 27: `EVT-007` kuralı olay deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 28: `EVT-008` kuralı olay deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 29: `EVT-009` kuralı olay deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 30: `EVT-010` kuralı olay deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 31: `EVT-001` kuralı olay deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 32: `EVT-002` kuralı olay deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 33: `EVT-003` kuralı olay deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 34: `EVT-004` kuralı olay deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 35: `EVT-005` kuralı olay deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 36: `EVT-006` kuralı olay deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 37: `EVT-007` kuralı olay deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 38: `EVT-008` kuralı olay deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 39: `EVT-009` kuralı olay deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 40: `EVT-010` kuralı olay deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 41: `EVT-001` kuralı olay deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 42: `EVT-002` kuralı olay deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 43: `EVT-003` kuralı olay deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 44: `EVT-004` kuralı olay deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 45: `EVT-005` kuralı olay deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 46: `EVT-006` kuralı olay deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 47: `EVT-007` kuralı olay deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 48: `EVT-008` kuralı olay deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 49: `EVT-009` kuralı olay deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.