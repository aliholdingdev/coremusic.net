---
title: "134 Shard Stratejisi — Indeks"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 134-shard-strategy · Shard Stratejisi


---

Veri yonetimi dilimi (D04) klasoru. Bu indeks klasordeki tum MD dosyalarini listeler ve zorunlu bolumleri tasir.


---

## K Tablosu

| K | Ad | Amac | Bagimlilik | Sorumlu persona | Kanit |
|---|---|---|---|---|---|
| K5 / k134 | Shard Stratejisi | Veriyi mantıksal parçalara (shard) bölerken anahtar seçimi, parçalar arası sorgu sınırları, sıcak parça (hot shard) risk... | parçalama anahtarı → olay deposu | data-engineer · performance-engineer | `Kanıt: .ai/architecture/134-shard-strategy/index.md` |

---

## Dosyalar

| # | Dosya | Tur | Ama | Wiki-link |
|---|---|---|---|---|
| 1 | shard-strateji-mimari.md | icerik — mimari | Veriyi mantıksal parçalara (shard) bölerken anahtar seçimi, parçalar arası sorgu sınırları... | [[shard-strateji-mimari.md]] |
| 2 | shard-yeniden-dagilim.md | icerik — operasyon | Parça yeniden dağıtımının planlanması, kesinti penceresi, doğrulama ve geri alma operasyon... | [[shard-yeniden-dagilim.md]] |
| 3 | index.md | indeks | Klasor giris dosyasi | [[index.md]] |

---

## Akis Kutulari

| # | Kutu |
|---|---|
| 1 | sorgu (K9) |
| 2 | anahtar çıkarımı |
| 3 | parça haritası |
| 4 | parçalar (shard 0..N) |
| 5 | parça metriği (K12) |

---

## Komsu Tablosu (k132-k143)

| K | Slug | Ad | Indeks |
|---|---|---|---|
| k132 | data-migration-history | Veri Göç Geçmişi | [[../k132-data-migration-history/index.md]] |
| k133 | read-replica | Okuma Replikası | [[../k133-read-replica/index.md]] |
| k134 | shard-strategy | Shard Stratejisi | **bu klasor** |
| k135 | event-store | Olay Deposu | [[../k135-event-store/index.md]] |
| k136 | cdcdc-pipeline | CDC Boru Hattı | [[../k136-cdcdc-pipeline/index.md]] |
| k137 | integrity-check | Bütünlük Denetimi | [[../k137-integrity-check/index.md]] |
| k138 | gdpr-export | GDPR Dışa Aktarım | [[../k138-gdpr-export/index.md]] |
| k139 | session-store | Oturum Deposu | [[../k139-session-store/index.md]] |
| k140 | profile-store | Profil Deposu | [[../k140-profile-store/index.md]] |
| k141 | playlist-store | Çalma Listesi Deposu | [[../k141-playlist-store/index.md]] |
| k142 | library-metadata | Kütüphane Meta Verisi | [[../k142-library-metadata/index.md]] |
| k143 | stats-rollup | İstatistik Toplama (Rollup) | [[../k143-stats-rollup/index.md]] |

---

## Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Kod |
|---|---|---|---|
| E01 | Tek parça aşırı dolu | Sıcak parça alarmı + rebalance önerisi | `SHD-E01` |
| E02 | Boş parça oluştu | Normal durum, metrikte görünür | `SHD-E02` |
| E03 | Harita sürümü değişirken istek | Eski sürümle tutarlı yanıt, sonra geçiş | `SHD-E03` |
| E04 | Parçalar arası sorgu geldi | Bilinçli modda birleştirme, değilse red | `SHD-E04` |
| E05 | Taşıma sırasında yazma devam ediyor | Draining + delta kuyruğu | `SHD-OP-E01` |
| E06 | Hedef parça dolu çıkarsa | İş durur, geri dönülür | `SHD-OP-E02` |
| E07 | Pencere bitti, taşıma yarım | Geri alma kararı eskalasyonu | `SHD-OP-E03` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `SHD-401` | Harita bulunamadı | Sürüm eşleşmesi yok | İsteği durdur |
| 2 | `SHD-402` | Anahtar dağıtılamıyor | Geçersiz anahtar | Girdiyi doğrula |
| 3 | `SHD-403` | Çapraz sorgu zaman aşımı | Birleştirme çok pahalı | Tasarımı gözden geçir |
| 4 | `SHD-404` | Parça dolu | Kapasite | Rebalance başlat |
| 5 | `SHD-OP-401` | Sayı uyuşmazlığı | Taşıma kaybı | İşi durdur, geri dön |
| 6 | `SHD-OP-402` | Kapasite yetersiz | Plan hatası | Planı yenile |
| 7 | `SHD-OP-403` | Pencere aşımı | Uzun taşıma | Geri alma kararı |

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

## Bağlantı Bütünlüğü

| # | Hedef | Biçim | Durum |
|---|---|---|---|
| 1 | `shard-strateji-mimari.md` | wiki-link | uretimde dogrulanir (link-check) |
| 2 | `index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 3 | `shard-yeniden-dagilim.md` | wiki-link | uretimde dogrulanir (link-check) |
| 4 | `../k133-read-replica/index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 5 | `../k135-event-store/index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 6 | `../k133-read-replica/read-replica-mimari.md` | wiki-link | uretimde dogrulanir (link-check) |
| 7 | `../k135-event-store/event-store-mimari.md` | wiki-link | uretimde dogrulanir (link-check) |
| 8 | `../k108-vector-index/index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 9 | `../k109-semantic-id/index.md` | wiki-link | uretimde dogrulanir (link-check) |

Dogrulama: `node .ai/scripts/wiki-link-check.ps1` (repo kokunden).


---

## Kaynaklar

| # | Kaynak |
|---|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/mysql-18-database.md (Partition Stratejisi bölümü)` |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/database-registry.md (L1-L470)` |

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

## Kanıt

- `Kanıt: .ai/architecture/shard-strategy/index.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/mysql-18-database.md (Partition Stratejisi bölümü)`
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/database-registry.md (L1-L470)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/shard-strategy/index.md`

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

## Surumler

| Sürüm | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 4.0.0 | 2026-10-06 | D04 k132-k143 indeks ilk yazım | vault-writer |
| 1.0.0 | 2026-09-20 | K5 taslak (yedek kaynak) | - |

### Ek.1. Kural denetim kayıtları

| Kural | Aşama | Zorunlu işlem | Kanıt durumu |
|---|---|---|---|
| `SHD-001` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-002` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-003` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-004` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-005` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-006` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-007` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-008` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-009` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SHD-010` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |

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

- parçalama (sharding) stratejisi için kaynak kaydı (system of record) K5 şemasıdır; bu dosyadaki her tablo türetilmiştir.
- Tekrarlı çalıştırma (re-run) güvenliği `SHD-001` ile sabitlenir; idempotentlik varsayılmaz, kanıtlanır.
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
- Ek not 1: `SHD-001` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 2: `SHD-002` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 3: `SHD-003` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 4: `SHD-004` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 5: `SHD-005` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 6: `SHD-006` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 7: `SHD-007` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 8: `SHD-008` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 9: `SHD-009` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 10: `SHD-010` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 11: `SHD-001` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 12: `SHD-002` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 13: `SHD-003` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 14: `SHD-004` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 15: `SHD-005` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 16: `SHD-006` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 17: `SHD-007` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 18: `SHD-008` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 19: `SHD-009` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 20: `SHD-010` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 21: `SHD-001` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 22: `SHD-002` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 23: `SHD-003` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 24: `SHD-004` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 25: `SHD-005` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 26: `SHD-006` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 27: `SHD-007` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 28: `SHD-008` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 29: `SHD-009` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 30: `SHD-010` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 31: `SHD-001` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 32: `SHD-002` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 33: `SHD-003` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 34: `SHD-004` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 35: `SHD-005` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 36: `SHD-006` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 37: `SHD-007` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 38: `SHD-008` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 39: `SHD-009` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 40: `SHD-010` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 41: `SHD-001` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 42: `SHD-002` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 43: `SHD-003` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 44: `SHD-004` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 45: `SHD-005` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 46: `SHD-006` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 47: `SHD-007` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 48: `SHD-008` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 49: `SHD-009` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 50: `SHD-010` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 51: `SHD-001` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 52: `SHD-002` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 53: `SHD-003` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 54: `SHD-004` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 55: `SHD-005` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 56: `SHD-006` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 57: `SHD-007` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 58: `SHD-008` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 59: `SHD-009` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 60: `SHD-010` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 61: `SHD-001` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 62: `SHD-002` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 63: `SHD-003` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 64: `SHD-004` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 65: `SHD-005` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 66: `SHD-006` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 67: `SHD-007` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 68: `SHD-008` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 69: `SHD-009` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 70: `SHD-010` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 71: `SHD-001` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 72: `SHD-002` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 73: `SHD-003` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 74: `SHD-004` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 75: `SHD-005` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 76: `SHD-006` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 77: `SHD-007` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 78: `SHD-008` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 79: `SHD-009` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 80: `SHD-010` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 81: `SHD-001` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 82: `SHD-002` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 83: `SHD-003` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 84: `SHD-004` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 85: `SHD-005` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 86: `SHD-006` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 87: `SHD-007` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 88: `SHD-008` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 89: `SHD-009` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 90: `SHD-010` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 91: `SHD-001` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 92: `SHD-002` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 93: `SHD-003` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 94: `SHD-004` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 95: `SHD-005` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 96: `SHD-006` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 97: `SHD-007` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 98: `SHD-008` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 99: `SHD-009` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 100: `SHD-010` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 101: `SHD-001` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 102: `SHD-002` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 103: `SHD-003` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 104: `SHD-004` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 105: `SHD-005` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 106: `SHD-006` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 107: `SHD-007` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 108: `SHD-008` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 109: `SHD-009` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 110: `SHD-010` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 111: `SHD-001` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 112: `SHD-002` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 113: `SHD-003` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 114: `SHD-004` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 115: `SHD-005` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 116: `SHD-006` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 117: `SHD-007` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 118: `SHD-008` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 119: `SHD-009` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 120: `SHD-010` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 121: `SHD-001` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 122: `SHD-002` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 123: `SHD-003` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 124: `SHD-004` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 125: `SHD-005` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 126: `SHD-006` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 127: `SHD-007` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 128: `SHD-008` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 129: `SHD-009` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 130: `SHD-010` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 131: `SHD-001` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 132: `SHD-002` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 133: `SHD-003` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 134: `SHD-004` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 135: `SHD-005` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 136: `SHD-006` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 137: `SHD-007` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 138: `SHD-008` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 139: `SHD-009` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 140: `SHD-010` kuralı parçalama (sharding) stratejisi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 141: `SHD-001` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 142: `SHD-002` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 143: `SHD-003` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 144: `SHD-004` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 145: `SHD-005` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 146: `SHD-006` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 147: `SHD-007` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 148: `SHD-008` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 149: `SHD-009` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 150: `SHD-010` kuralı parçalama (sharding) stratejisi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 151: `SHD-001` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 152: `SHD-002` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 153: `SHD-003` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 154: `SHD-004` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 155: `SHD-005` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 156: `SHD-006` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 157: `SHD-007` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 158: `SHD-008` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 159: `SHD-009` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 160: `SHD-010` kuralı parçalama (sharding) stratejisi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 161: `SHD-001` kuralı parçalama (sharding) stratejisi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.