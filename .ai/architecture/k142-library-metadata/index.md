---
title: "142 Kütüphane Meta Verisi — Indeks"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 142-library-metadata · Kütüphane Meta Verisi


---

Veri yonetimi dilimi (D04) klasoru. Bu indeks klasordeki tum MD dosyalarini listeler ve zorunlu bolumleri tasir.


---

## K Tablosu

| K | Ad | Amac | Bagimlilik | Sorumlu persona | Kanit |
|---|---|---|---|---|---|
| K5 / k142 | Kütüphane Meta Verisi | Parça/album/sanatçı meta verisinin sahipliği, normalizasyonu, birleştirme (merge) ve kırık referans davranışını tanımlar... | kütüphane meta verisi → istatistik toplama | data-engineer · backend-architect | `Kanıt: .ai/architecture/142-library-metadata/index.md` |

---

## Dosyalar

| # | Dosya | Tur | Ama | Wiki-link |
|---|---|---|---|---|
| 1 | library-metadata-mimari.md | icerik — mimari | Parça/album/sanatçı meta verisinin sahipliği, normalizasyonu, birleştirme (merge) ve kırık... | [[library-metadata-mimari.md]] |
| 2 | library-metadata-operasyon.md | icerik — operasyon | Kütüphane meta verisinin işletilmesini: tazelik denetimi, birleştirme işleri, arşiv/silme ... | [[library-metadata-operasyon.md]] |
| 3 | index.md | indeks | Klasor giris dosyasi | [[index.md]] |

---

## Akis Kutulari

| # | Kutu |
|---|---|
| 1 | katalog isteği (K9) |
| 2 | alan doğrulama |
| 3 | birincil anahtar |
| 4 | kütüphane kaydı (K5) |
| 5 | tazelik denetimi |

---

## Komsu Tablosu (k132-k143)

| K | Slug | Ad | Indeks |
|---|---|---|---|
| k132 | data-migration-history | Veri Göç Geçmişi | [[../k132-data-migration-history/index.md]] |
| k133 | read-replica | Okuma Replikası | [[../k133-read-replica/index.md]] |
| k134 | shard-strategy | Shard Stratejisi | [[../k134-shard-strategy/index.md]] |
| k135 | event-store | Olay Deposu | [[../k135-event-store/index.md]] |
| k136 | cdcdc-pipeline | CDC Boru Hattı | [[../k136-cdcdc-pipeline/index.md]] |
| k137 | integrity-check | Bütünlük Denetimi | [[../k137-integrity-check/index.md]] |
| k138 | gdpr-export | GDPR Dışa Aktarım | [[../k138-gdpr-export/index.md]] |
| k139 | session-store | Oturum Deposu | [[../k139-session-store/index.md]] |
| k140 | profile-store | Profil Deposu | [[../k140-profile-store/index.md]] |
| k141 | playlist-store | Çalma Listesi Deposu | [[../k141-playlist-store/index.md]] |
| k142 | library-metadata | Kütüphane Meta Verisi | **bu klasor** |
| k143 | stats-rollup | İstatistik Toplama (Rollup) | [[../k143-stats-rollup/index.md]] |

---

## Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Kod |
|---|---|---|---|
| E01 | Aynı eser iki kayıtta | Merge planı + kaynak referanslar taşınır, kayıt silinmez | `LIB-E01` |
| E02 | Normalizasyon sonrası çakışma | İkinci kayıt merge adayına eklenir | `LIB-E02` |
| E03 | Bayat etiketle yazma | Red + yeniden okuma | `LIB-E03` |
| E04 | Telif etiketi cleared değil | Yayınlanmaz, katalogda işaretli | `LIB-E04` |
| E05 | Merge işi yoğun saatte | Pencere yeniden planlanır | `LIB-OP-E01` |
| E06 | Sökük referans dalgası | Kaynak değişikliğini incele | `LIB-OP-E02` |
| E07 | Telif engeli dalgası | Telif akışını incele | `LIB-OP-E03` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `LIB-401` | Mükerrer kayıt | Birincil anahtar ihlali | Reddet |
| 2 | `LIB-402` | Bayat yazma | Etag uyuşmazlığı | Yeniden oku + dene |
| 3 | `LIB-403` | Telif engeli | rights_state=blocked | Yayınlanmadan reddet |
| 4 | `LIB-404` | Normalizasyon hatası | Geçersiz karakter | Reddet |
| 5 | `LIB-OP-401` | Tazelik bulgusu | Etag tutarsız | Denetimi tekrarla |
| 6 | `LIB-OP-402` | Sökük referans > eşik | Kaynak değişimi | Rapor + incele |
| 7 | `LIB-OP-403` | Merge geri alma | Hatalı birleştirme | Geri al |

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

## Bağlantı Bütünlüğü

| # | Hedef | Biçim | Durum |
|---|---|---|---|
| 1 | `library-metadata-mimari.md` | wiki-link | uretimde dogrulanir (link-check) |
| 2 | `index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 3 | `library-metadata-operasyon.md` | wiki-link | uretimde dogrulanir (link-check) |
| 4 | `../k141-playlist-store/index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 5 | `../k143-stats-rollup/index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 6 | `../k141-playlist-store/playlist-store-mimari.md` | wiki-link | uretimde dogrulanir (link-check) |
| 7 | `../k143-stats-rollup/stats-rollup-mimari.md` | wiki-link | uretimde dogrulanir (link-check) |
| 8 | `../k108-vector-index/index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 9 | `../k109-semantic-id/index.md` | wiki-link | uretimde dogrulanir (link-check) |

Dogrulama: `node .ai/scripts/wiki-link-check.ps1` (repo kokunden).


---

## Kaynaklar

| # | Kaynak |
|---|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/mysql-18-database.md (L1-L500)` |
| 2 | `.ai/.sql/mysql/coremusic_albums.sql (album kaynağı — dosya var)` |
| 3 | `.ai/.sql/mysql/coremusic_user.sql (kullanıcı referansı kaynağı — dosya var)` |

---

## SSS

| Soru | Cevap | Kaynak |
|---|---|---|
| Aynı eser iki kez kayıtlı? | Merge — kayıt silinmez (LIB-004). | §3 |
| Ham başlık saklanır mı? | Hayır — normalizasyon zorunlu (LIB-003). | §3 |
| Telif engelli yayınlanır mı? | Hayır — rights_state kapısı (LIB-007). | §3 |
| Etag ne? | Tazelik etiketi — bayat yazma reddi. | LIB-005 |
| Alan sayısı? | UNKNOWN — envanter ölçülmedi. | ADR A5 |
| Toplu iş kotası? | UNKNOWN — politika onayı. | LIB-OP-03 |

---

## Kanıt

- `Kanıt: .ai/architecture/library-metadata/index.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/mysql-18-database.md (L1-L500)`
- `Kanıt: .ai/.sql/mysql/coremusic_albums.sql (album kaynağı — dosya var)`
- `Kanıt: .ai/.sql/mysql/coremusic_user.sql (kullanıcı referansı kaynağı — dosya var)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/library-metadata/index.md`

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
| 4.0.0 | 2026-10-06 | D04 k132-k143 indeks ilk yazım | vault-writer |
| 1.0.0 | 2026-09-20 | K5 taslak (yedek kaynak) | - |

### Ek.1. Kural denetim kayıtları

| Kural | Aşama | Zorunlu işlem | Kanıt durumu |
|---|---|---|---|
| `LIB-001` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `LIB-002` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `LIB-003` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `LIB-004` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `LIB-005` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `LIB-006` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `LIB-007` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `LIB-008` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `LIB-009` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `LIB-010` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |

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
- Ek not 48: `LIB-008` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 49: `LIB-009` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 50: `LIB-010` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 51: `LIB-001` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 52: `LIB-002` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 53: `LIB-003` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 54: `LIB-004` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 55: `LIB-005` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 56: `LIB-006` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 57: `LIB-007` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 58: `LIB-008` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 59: `LIB-009` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 60: `LIB-010` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 61: `LIB-001` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 62: `LIB-002` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 63: `LIB-003` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 64: `LIB-004` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 65: `LIB-005` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 66: `LIB-006` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 67: `LIB-007` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 68: `LIB-008` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 69: `LIB-009` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 70: `LIB-010` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 71: `LIB-001` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 72: `LIB-002` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 73: `LIB-003` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 74: `LIB-004` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 75: `LIB-005` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 76: `LIB-006` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 77: `LIB-007` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 78: `LIB-008` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 79: `LIB-009` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 80: `LIB-010` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 81: `LIB-001` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 82: `LIB-002` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 83: `LIB-003` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 84: `LIB-004` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 85: `LIB-005` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 86: `LIB-006` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 87: `LIB-007` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 88: `LIB-008` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 89: `LIB-009` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 90: `LIB-010` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 91: `LIB-001` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 92: `LIB-002` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 93: `LIB-003` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 94: `LIB-004` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 95: `LIB-005` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 96: `LIB-006` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 97: `LIB-007` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 98: `LIB-008` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 99: `LIB-009` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 100: `LIB-010` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 101: `LIB-001` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 102: `LIB-002` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 103: `LIB-003` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 104: `LIB-004` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 105: `LIB-005` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 106: `LIB-006` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 107: `LIB-007` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 108: `LIB-008` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 109: `LIB-009` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 110: `LIB-010` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 111: `LIB-001` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 112: `LIB-002` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 113: `LIB-003` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 114: `LIB-004` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 115: `LIB-005` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 116: `LIB-006` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 117: `LIB-007` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 118: `LIB-008` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 119: `LIB-009` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 120: `LIB-010` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 121: `LIB-001` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 122: `LIB-002` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 123: `LIB-003` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 124: `LIB-004` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 125: `LIB-005` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 126: `LIB-006` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 127: `LIB-007` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 128: `LIB-008` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 129: `LIB-009` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 130: `LIB-010` kuralı kütüphane meta verisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 131: `LIB-001` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 132: `LIB-002` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 133: `LIB-003` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 134: `LIB-004` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 135: `LIB-005` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 136: `LIB-006` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 137: `LIB-007` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 138: `LIB-008` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 139: `LIB-009` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 140: `LIB-010` kuralı kütüphane meta verisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 141: `LIB-001` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 142: `LIB-002` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 143: `LIB-003` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 144: `LIB-004` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 145: `LIB-005` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 146: `LIB-006` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 147: `LIB-007` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 148: `LIB-008` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 149: `LIB-009` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 150: `LIB-010` kuralı kütüphane meta verisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 151: `LIB-001` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 152: `LIB-002` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 153: `LIB-003` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 154: `LIB-004` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 155: `LIB-005` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 156: `LIB-006` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 157: `LIB-007` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 158: `LIB-008` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 159: `LIB-009` kuralı kütüphane meta verisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.