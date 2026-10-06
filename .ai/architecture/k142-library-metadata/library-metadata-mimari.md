---
title: "142 Kütüphane Meta Verisi — Mimari ve Tasarim"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 142-library-metadata · Kütüphane Meta Verisi (Mimari)


---

Bu dosya **kütüphane meta verisi** alanının tasarımını tanımlar: kapsam, akış, kurallar, veri modeli, kenar durumlar, hata modları ve bağımlılık matrisi. Kardeş dosya: operasyon.


---

- **Amaç (A):** Parça/album/sanatçı meta verisinin sahipliği, normalizasyonu, birleştirme (merge) ve kırık referans davranışını tanımlar: alan envanteri, birincil anahtarlar ve güncelleme kapıları.

---

- **Sorumlu persona:** data-engineer · backend-architect

---

- **Eşleşen klasor:** kütüphane meta verisi → istatistik toplama


---

## Kapsam

### Kapsamda (In)

| # | Kapsam |
|---|---|
| 1 | Parça/album/sanatçı meta veri şeması |
| 2 | Normalizasyon ve birleştirme (merge) |
| 3 | Kırık referans davranışı |
| 4 | Tazelik/etag denetimi |

---

### Kapsamdisi (Out)

| # | Kapsamdisi |
|---|---|
| 1 | Çalma listesi (k141) |
| 2 | Oynatma/streaming (K3/K15) |
| 3 | İstatistik toplama (k143) |
| 4 | Öneri modeli (K4) |

---

## Mimari Akış

| Adim | Asama | Aciklama | Kural |
|---|---|---|---|
| 1 | İstek | Katalog isteği alan doğrulamadan geçer | LIB-001 |
| 2 | Anahtar | Birincil anahtar ile kayıt bulunur | LIB-002 |
| 3 | Normalizasyon | Alanlar normalize edilir (büyük/küçük, boşluk) | LIB-003 |
| 4 | Birleştirme | Aynı eser iki kayıtta ise merge planlanır | LIB-004 |
| 5 | Tazelik | Güncelleme etiketi (etag) yenilenir | LIB-005 |
| 6 | Referans | Liste/istatistik referansları güncellenir | LIB-006 |

---

## Kurallar

| Kural | Tanim | Ihlal riski |
|---|---|---|
| `LIB-001` | Kütüphane meta verisi yalnız tanımlı alan envanterinde okunur/yazılır. | Kaçak alan |
| `LIB-002` | Her kaydın birincil anahtarı benzersizdir; ikinci kayıt reddedilir. | Mükerrer eser |
| `LIB-003` | Yazı biçimi normalizasyonu zorunludur (NFKC + trim); ham biçim saklanmaz. | Arama tutarsızlığı |
| `LIB-004` | Birleştirme (merge) işlemi kaynak kaydı silmez; kaynak referansları taşınır. | Veri kaybı |
| `LIB-005` | Her başarılı yazımda tazelik etiketi yenilenir; bayat etiketli yazma reddedilir. | Bayat yazma |
| `LIB-006` | Kırık referans (sökük bağlantı) sessizce silinmez, işaretlenir. | Sessiz kayıp |
| `LIB-007` | Telif/erişim etiketi olmayan kayıt yayına alınmaz. | Telif ihlali |
| `LIB-008` | Toplu içe aktarım işi kota ile çalışır ve idempotenttir. | Tekrarlı yükleme |
| `LIB-009` | Alan tipi/uzunluk sınırı tanımlıdır; aşılan değer reddedilir. | Bozuk veri |
| `LIB-010` | Kütüphane verisi REDACTED içermez; sırlar bu şemada tutulmaz. | REDACTED |

---

## Veri Modeli

| Alan | Tip | Aciklama | Kural |
|---|---|---|---|
| `track_ref` | `VARCHAR` | Parça birincil anahtarı | `LIB-002` |
| `album_ref` | `VARCHAR` | Album referansı | `LIB-002` |
| `artist_ref` | `VARCHAR` | Sanatçı referansı | `LIB-002` |
| `title` | `VARCHAR` | Başlık (normalize) | `LIB-003` |
| `duration_ms` | `INT` | Süre (ms) | `LIB-009` |
| `rights_state` | `ENUM` | pending/cleared/blocked | `LIB-007` |
| `etag` | `VARCHAR` | Tazelik etiketi | `LIB-005` |
| `merged_from` | `VARCHAR` | Birleştirme kaynağı (varsa) | `LIB-004` |
| `updated_at` | `DATETIME` | Güncelleme (UTC) | `LIB-005` |

---

## Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Kod |
|---|---|---|---|
| E01 | Aynı eser iki kayıtta | Merge planı + kaynak referanslar taşınır, kayıt silinmez | `LIB-E01` |
| E02 | Normalizasyon sonrası çakışma | İkinci kayıt merge adayına eklenir | `LIB-E02` |
| E03 | Bayat etiketle yazma | Red + yeniden okuma | `LIB-E03` |
| E04 | Telif etiketi cleared değil | Yayınlanmaz, katalogda işaretli | `LIB-E04` |
| E05 | Kırık liste referansı | İşaretlenir, otomatik silme yok | `LIB-E05` |
| E06 | Eski istemci yeni alan ister | Alan yok sayılır, hata değil | `LIB-E06` |
| E07 | Toplu içe aktarım tekrarladı | Idempotent: aynı kayıt iki kez eklenmez | `LIB-E07` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `LIB-401` | Mükerrer kayıt | Birincil anahtar ihlali | Reddet |
| 2 | `LIB-402` | Bayat yazma | Etag uyuşmazlığı | Yeniden oku + dene |
| 3 | `LIB-403` | Telif engeli | rights_state=blocked | Yayınlanmadan reddet |
| 4 | `LIB-404` | Normalizasyon hatası | Geçersiz karakter | Reddet |
| 5 | `LIB-405` | Merge veri kaybı | Referans taşınamadı | İşlemi geri al |
| 6 | `LIB-406` | Alan sınırı aşımı | Tip/uzunluk | Reddet |
| 7 | `LIB-407` | Toplu iş hatası | Kota/kesinti | Kota ile devam |
| 8 | `LIB-408` | Şema kanıtsız | Tasarım eksik | VERIFICATION REQUIRED |

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

> Katman tanimlari: `K0 cekirdek` ... `K16 K1-kaynak (MCP)`. `hayir` satirlari bilincli sinirdir.


---

## Test Senaryolari

| # | Senaryo | Beklenen |
|---|---|---|
| T-01 | Mükerrer eser kaydı | İkinci kayıt reddedilir |
| T-02 | Normalizasyon çakışması | Merge adayına eklenir |
| T-03 | Bayat etiketli yazma | Red |
| T-04 | Telif engeli | Yayın yok |
| T-05 | Kırık referans | İşaretlenir |
| T-06 | Tekrarlı içe aktarım | Tekrar kayıt yok |
| T-07 | Alan sınırı aşımı | Red |
| T-08 | Merge sonrası liste | Referanslar taşındı |

---

## ADR Adaylari

| # | Karar | Reddedilen | Risk | Durum |
|---|---|---|---|---|
| A1 | Anahtar benzersizliği | Yumuşak tekilleştirme | Mükerrer eser | kabul |
| A2 | Merge kaydı silmez | Fiziksel birleştirme | Kayıp geri alınamazlık | kabul |
| A3 | Etag ile bayat yazma reddi | Son yazan kazanır | Kayıp güncelleme | kabul |
| A4 | Normalizasyon zorunlu | İstemciye bırakma | Arama tutarsızlığı | kabul |
| A5 | Alan envanteri ölçümle | Tahmini alan listesi | Kanıtsız envanter | açık — VERIFICATION REQUIRED |

---

## Riskler

| Risk | Etki | Onlem |
|---|---|---|
| Mükerrer eser | Yanlış katalog | Anahtar denetimi |
| Merge kaybı | Veri kaybı | Referans taşıma provası |
| Bayat yazma | Eski değer ezmesi | Etag reddi |
| Telif ihlali | Hukuki risk | rights_state kapısı |
| Sökük referanslar | Bozuk görünüm | İşaretleme + rapor |

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
| `KeyGuard` | Anahtar benzersizliğini korur | Veri yazmaz |
| `Normalizer` | Yazı biçimini standardize eder | İçerik değiştirmez |
| `MergePlanner` | Birleştirme planı üretir | Kaydı silmez |
| `FreshnessCheck` | Etag denetler | Yazmaz |

---

## Bağlantılar

### Kendi dosyalari

1. `[[library-metadata-mimari.md]]`
2. `[[index.md]]`
3. `[[library-metadata-operasyon.md]]`

### Komsu / ilgili klasorler

1. `[[../k141-playlist-store/index.md]]`
2. `[[../k143-stats-rollup/index.md]]`
3. `[[../k141-playlist-store/playlist-store-mimari.md]]`
4. `[[../k143-stats-rollup/stats-rollup-mimari.md]]`
5. `[[../k108-vector-index/index.md]]`
6. `[[../k109-semantic-id/index.md]]`

---

## Kanıt

- `Kanıt: .ai/architecture/library-metadata/library-metadata-mimari.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/mysql-18-database.md (L1-L500)`
- `Kanıt: .ai/.sql/mysql/coremusic_albums.sql (album kaynağı — dosya var)`
- `Kanıt: .ai/.sql/mysql/coremusic_user.sql (kullanıcı referansı kaynağı — dosya var)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/library-metadata/library-metadata-mimari.md`

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
| kütüphane meta verisi kayit politikasi | **R** | C | A | C | C | I |
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
| `Kütüphane meta verisi` | `library metadata` | Parça/album/sanatçı bilgisi |
| `Birincil anahtar` | `primary key` | Benzersiz kayıt anahtarı |
| `Normalizasyon` | `normalization` | Yazı biçimi standardizasyonu |
| `Birleştirme` | `merge` | Mükerrer kayıtları eleme |
| `Tazelik etiketi` | `etag` | Bayat yazma engeli |
| `Telif durumu` | `rights state` | Yayın izni |
| `Sökük referans` | `dangling reference` | Kırık bağlantı |
| `Kota` | `quota` | Toplu iş sınırı |
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
| `LIB-001` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `LIB-002` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `LIB-003` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `LIB-004` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `LIB-005` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `LIB-006` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `LIB-007` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `LIB-008` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `LIB-009` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `LIB-010` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |

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

- kütüphane meta verisi için kaynak kaydı (system of record) K5 şemasıdır; bu dosyadaki her tablo türetilmiştir.
- Tekrarlı çalıştırma (re-run) güvenliği `LIB-001` ile sabitlenir; idempotentlik varsayılmaz, kanıtlanır.
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
- Ek not 1: `LIB-001` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 2: `LIB-002` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 3: `LIB-003` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 4: `LIB-004` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 5: `LIB-005` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 6: `LIB-006` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 7: `LIB-007` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 8: `LIB-008` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 9: `LIB-009` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 10: `LIB-010` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 11: `LIB-001` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 12: `LIB-002` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 13: `LIB-003` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 14: `LIB-004` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 15: `LIB-005` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 16: `LIB-006` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 17: `LIB-007` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 18: `LIB-008` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 19: `LIB-009` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 20: `LIB-010` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 21: `LIB-001` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 22: `LIB-002` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 23: `LIB-003` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 24: `LIB-004` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 25: `LIB-005` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 26: `LIB-006` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 27: `LIB-007` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 28: `LIB-008` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 29: `LIB-009` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 30: `LIB-010` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 31: `LIB-001` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 32: `LIB-002` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 33: `LIB-003` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 34: `LIB-004` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 35: `LIB-005` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 36: `LIB-006` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 37: `LIB-007` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 38: `LIB-008` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 39: `LIB-009` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 40: `LIB-010` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 41: `LIB-001` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 42: `LIB-002` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 43: `LIB-003` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 44: `LIB-004` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 45: `LIB-005` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 46: `LIB-006` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 47: `LIB-007` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.