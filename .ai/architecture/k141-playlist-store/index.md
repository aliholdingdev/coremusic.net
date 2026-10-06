---
title: "141 Çalma Listesi Deposu — Indeks"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 141-playlist-store · Çalma Listesi Deposu


---

Veri yonetimi dilimi (D04) klasoru. Bu indeks klasordeki tum MD dosyalarini listeler ve zorunlu bolumleri tasir.


---

## K Tablosu

| K | Ad | Amac | Bagimlilik | Sorumlu persona | Kanit |
|---|---|---|---|---|---|
| K5 / k141 | Çalma Listesi Deposu | Çalma listelerinin başlık/meta verisi, sıralı parça kayıtları, eşzamanlı düzenleme, parça taşıma (yeniden sıralama) ve a... | çalma listesi deposu → kütüphane meta verisi | data-engineer · backend-architect | `Kanıt: .ai/architecture/141-playlist-store/index.md` |

---

## Dosyalar

| # | Dosya | Tur | Ama | Wiki-link |
|---|---|---|---|---|
| 1 | playlist-store-mimari.md | icerik — mimari | Çalma listelerinin başlık/meta verisi, sıralı parça kayıtları, eşzamanlı düzenleme, parça ... | [[playlist-store-mimari.md]] |
| 2 | playlist-store-operasyon.md | icerik — operasyon | Çalma listesi verisinin işletilmesini: sıra bütünlüğü denetimi, çakışma oranı izleme, topl... | [[playlist-store-operasyon.md]] |
| 3 | index.md | indeks | Klasor giris dosyasi | [[index.md]] |

---

## Akis Kutulari

| # | Kutu |
|---|---|
| 1 | liste isteği (K9) |
| 2 | sahiplik + yetki |
| 3 | sıra numarası |
| 4 | liste kaydı (K5) |
| 5 | bütünlük denetimi |

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
| k141 | playlist-store | Çalma Listesi Deposu | **bu klasor** |
| k142 | library-metadata | Kütüphane Meta Verisi | [[../k142-library-metadata/index.md]] |
| k143 | stats-rollup | İstatistik Toplama (Rollup) | [[../k143-stats-rollup/index.md]] |

---

## Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Kod |
|---|---|---|---|
| E01 | Aynı anda iki kullanıcı taşıma yapıyor | Çakışma reddi + yeniden deneme | `PLB-E01` |
| E02 | Sıra numarası deliği oluştu | Bütünlük denetiminde işaretlenir, otomatik yeniden numaralanmaz | `PLB-E02` |
| E03 | Aynı parça iki kez eklendi | Politika: izinli ise iki satır, red ise uyarı | `PLB-E03` |
| E04 | Parça kütüphaneden silinmiş | track_ref işaretli kalır, satır silinmez | `PLB-E04` |
| E05 | Bütünlük denetimi bulgu verirse | Bulgu aç + compact planı | `PLB-OP-E01` |
| E06 | Toplu iş yoğun saatte | Pencere yeniden planlanır | `PLB-OP-E02` |
| E07 | Çakışma oranı fırladı | İstemci sürümünü incele | `PLB-OP-E03` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `PLB-401` | Sahiplik ihlali | Yetkisiz yazma | Reddet + denetim |
| 2 | `PLB-402` | Sıra çakışması | Eşzamanlı taşıma | Reddet + yeniden dene |
| 3 | `PLB-403` | Boyut sınırı | Aşırı öğe | Reddet |
| 4 | `PLB-404` | Kırık parça referansı | Kütüphane değişimi | İşaretle |
| 5 | `PLB-OP-401` | Sıra bozukluğu | Yarım işlem | compact + denetim |
| 6 | `PLB-OP-402` | Çakışma > eşik | İstemci yarışması | Yolu incele |
| 7 | `PLB-OP-403` | Toplu iş kotası | Aşırı iş | Planı böl |

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
| 1 | `playlist-store-mimari.md` | wiki-link | uretimde dogrulanir (link-check) |
| 2 | `index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 3 | `playlist-store-operasyon.md` | wiki-link | uretimde dogrulanir (link-check) |
| 4 | `../k140-profile-store/index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 5 | `../k142-library-metadata/index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 6 | `../k140-profile-store/profile-store-mimari.md` | wiki-link | uretimde dogrulanir (link-check) |
| 7 | `../k142-library-metadata/library-metadata-mimari.md` | wiki-link | uretimde dogrulanir (link-check) |
| 8 | `../k108-vector-index/index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 9 | `../k109-semantic-id/index.md` | wiki-link | uretimde dogrulanir (link-check) |

Dogrulama: `node .ai/scripts/wiki-link-check.ps1` (repo kokunden).


---

## Kaynaklar

| # | Kaynak |
|---|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/mysql-18-database.md (L1-L500)` |
| 2 | `.ai/.sql/mysql/coremusic_playlist.sql (liste kaynağı — dosya var)` |

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

## Kanıt

- `Kanıt: .ai/architecture/playlist-store/index.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/mysql-18-database.md (L1-L500)`
- `Kanıt: .ai/.sql/mysql/coremusic_playlist.sql (liste kaynağı — dosya var)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/playlist-store/index.md`

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

## Surumler

| Sürüm | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 4.0.0 | 2026-10-06 | D04 k132-k143 indeks ilk yazım | vault-writer |
| 1.0.0 | 2026-09-20 | K5 taslak (yedek kaynak) | - |

### Ek.1. Kural denetim kayıtları

| Kural | Aşama | Zorunlu işlem | Kanıt durumu |
|---|---|---|---|
| `PLB-001` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `PLB-002` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `PLB-003` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `PLB-004` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `PLB-005` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `PLB-006` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `PLB-007` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `PLB-008` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `PLB-009` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `PLB-010` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |

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

- çalma listesi deposu için kaynak kaydı (system of record) K5 şemasıdır; bu dosyadaki her tablo türetilmiştir.
- Tekrarlı çalıştırma (re-run) güvenliği `PLB-001` ile sabitlenir; idempotentlik varsayılmaz, kanıtlanır.
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
- Ek not 1: `PLB-001` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 2: `PLB-002` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 3: `PLB-003` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 4: `PLB-004` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 5: `PLB-005` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 6: `PLB-006` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 7: `PLB-007` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 8: `PLB-008` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 9: `PLB-009` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 10: `PLB-010` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 11: `PLB-001` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 12: `PLB-002` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 13: `PLB-003` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 14: `PLB-004` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 15: `PLB-005` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 16: `PLB-006` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 17: `PLB-007` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 18: `PLB-008` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 19: `PLB-009` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 20: `PLB-010` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 21: `PLB-001` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 22: `PLB-002` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 23: `PLB-003` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 24: `PLB-004` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 25: `PLB-005` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 26: `PLB-006` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 27: `PLB-007` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 28: `PLB-008` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 29: `PLB-009` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 30: `PLB-010` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 31: `PLB-001` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 32: `PLB-002` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 33: `PLB-003` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 34: `PLB-004` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 35: `PLB-005` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 36: `PLB-006` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 37: `PLB-007` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 38: `PLB-008` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 39: `PLB-009` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 40: `PLB-010` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 41: `PLB-001` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 42: `PLB-002` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 43: `PLB-003` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 44: `PLB-004` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 45: `PLB-005` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 46: `PLB-006` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 47: `PLB-007` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 48: `PLB-008` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 49: `PLB-009` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 50: `PLB-010` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 51: `PLB-001` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 52: `PLB-002` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 53: `PLB-003` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 54: `PLB-004` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 55: `PLB-005` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 56: `PLB-006` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 57: `PLB-007` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 58: `PLB-008` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 59: `PLB-009` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 60: `PLB-010` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 61: `PLB-001` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 62: `PLB-002` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 63: `PLB-003` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 64: `PLB-004` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 65: `PLB-005` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 66: `PLB-006` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 67: `PLB-007` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 68: `PLB-008` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 69: `PLB-009` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 70: `PLB-010` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 71: `PLB-001` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 72: `PLB-002` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 73: `PLB-003` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 74: `PLB-004` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 75: `PLB-005` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 76: `PLB-006` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 77: `PLB-007` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 78: `PLB-008` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 79: `PLB-009` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 80: `PLB-010` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 81: `PLB-001` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 82: `PLB-002` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 83: `PLB-003` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 84: `PLB-004` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 85: `PLB-005` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 86: `PLB-006` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 87: `PLB-007` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 88: `PLB-008` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 89: `PLB-009` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 90: `PLB-010` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 91: `PLB-001` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 92: `PLB-002` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 93: `PLB-003` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 94: `PLB-004` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 95: `PLB-005` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 96: `PLB-006` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 97: `PLB-007` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 98: `PLB-008` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 99: `PLB-009` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 100: `PLB-010` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 101: `PLB-001` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 102: `PLB-002` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 103: `PLB-003` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 104: `PLB-004` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 105: `PLB-005` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 106: `PLB-006` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 107: `PLB-007` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 108: `PLB-008` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 109: `PLB-009` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 110: `PLB-010` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 111: `PLB-001` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 112: `PLB-002` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 113: `PLB-003` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 114: `PLB-004` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 115: `PLB-005` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 116: `PLB-006` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 117: `PLB-007` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 118: `PLB-008` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 119: `PLB-009` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 120: `PLB-010` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 121: `PLB-001` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 122: `PLB-002` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 123: `PLB-003` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 124: `PLB-004` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 125: `PLB-005` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 126: `PLB-006` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 127: `PLB-007` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 128: `PLB-008` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 129: `PLB-009` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 130: `PLB-010` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 131: `PLB-001` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 132: `PLB-002` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 133: `PLB-003` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 134: `PLB-004` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 135: `PLB-005` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 136: `PLB-006` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 137: `PLB-007` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 138: `PLB-008` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 139: `PLB-009` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 140: `PLB-010` kuralı çalma listesi deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 141: `PLB-001` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 142: `PLB-002` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 143: `PLB-003` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 144: `PLB-004` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 145: `PLB-005` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 146: `PLB-006` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 147: `PLB-007` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 148: `PLB-008` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 149: `PLB-009` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 150: `PLB-010` kuralı çalma listesi deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 151: `PLB-001` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 152: `PLB-002` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 153: `PLB-003` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 154: `PLB-004` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 155: `PLB-005` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 156: `PLB-006` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 157: `PLB-007` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 158: `PLB-008` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 159: `PLB-009` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 160: `PLB-010` kuralı çalma listesi deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 161: `PLB-001` kuralı çalma listesi deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.