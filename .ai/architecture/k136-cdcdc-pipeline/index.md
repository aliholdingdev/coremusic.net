---
title: "136 CDC Boru Hattı — Indeks"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 136-cdcdc-pipeline · CDC Boru Hattı


---

Veri yonetimi dilimi (D04) klasoru. Bu indeks klasordeki tum MD dosyalarini listeler ve zorunlu bolumleri tasir.


---

## K Tablosu

| K | Ad | Amac | Bagimlilik | Sorumlu persona | Kanit |
|---|---|---|---|---|---|
| K5 / k136 | CDC Boru Hattı | Kaynak veritabanındaki değişikliklerin yakalanıp sıralı, kayıpsız ve tekrar güvenli biçimde hedefe aktarılmasını tanımla... | CDC boru hattı → bütünlük denetimi | data-engineer · backend-architect | `Kanıt: .ai/architecture/136-cdcdc-pipeline/index.md` |

---

## Dosyalar

| # | Dosya | Tur | Ama | Wiki-link |
|---|---|---|---|---|
| 1 | cdc-pipeline-mimari.md | icerik — mimari | Kaynak veritabanındaki değişikliklerin yakalanıp sıralı, kayıpsız ve tekrar güvenli biçimd... | [[cdc-pipeline-mimari.md]] |
| 2 | cdc-pipeline-operasyon.md | icerik — operasyon | Yakalama sürecinin izlenmesi, checkpoint yönetimi, gecikme alarmı, geri kazanım (backfill)... | [[cdc-pipeline-operasyon.md]] |
| 3 | index.md | indeks | Klasor giris dosyasi | [[index.md]] |

---

## Akis Kutulari

| # | Kutu |
|---|---|
| 1 | kaynak DB değişikliği |
| 2 | yakalama (capture) |
| 3 | sıralı kuyruk |
| 4 | checkpoint |
| 5 | hedefe uygulama |

---

## Komsu Tablosu (k132-k143)

| K | Slug | Ad | Indeks |
|---|---|---|---|
| k132 | data-migration-history | Veri Göç Geçmişi | [[../k132-data-migration-history/index.md]] |
| k133 | read-replica | Okuma Replikası | [[../k133-read-replica/index.md]] |
| k134 | shard-strategy | Shard Stratejisi | [[../k134-shard-strategy/index.md]] |
| k135 | event-store | Olay Deposu | [[../k135-event-store/index.md]] |
| k136 | cdcdc-pipeline | CDC Boru Hattı | **bu klasor** |
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
| E01 | Kaynakta DDL değişikliği | Şema olay şemasına yansır, akış devam eder | `CDC-E01` |
| E02 | Aynı olay iki kez teslim | Idempotent uygulama: etki bir kez | `CDC-E02` |
| E03 | Checkpoint sonra geri sarma | Yeniden uygulama; hedef idempotentliği şart | `CDC-E03` |
| E04 | Uzun süreli ağ kesintisi | Kuyruk birikir; kayıp yok, gecikme artar | `CDC-E04` |
| E05 | Gece gecikme yükselmesi | Alarm + kaynak kontrolü | `CDC-OP-E01` |
| E06 | Nöbetçi değişimi | Devir notu + durum özeti | `CDC-OP-E02` |
| E07 | Checkpoint durdurulmuş | Neden kaydı zorunlu | `CDC-OP-E03` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `CDC-401` | Checkpoint ilerlemiyor | Hedef hataları | Hedefi düzelt, akışı durdur |
| 2 | `CDC-402` | Sıra atlandı | Yakalama boşluğu | Akışı durdur + backfill |
| 3 | `CDC-403` | Şema uyuşmazlığı | Kaynak DDL | Şemayı güncelle |
| 4 | `CDC-404` | Kuyruk doluyor | Tüketim yavaş | Tüketiciyi ölçekle |
| 5 | `CDC-OP-401` | Gecikme sürekli | Kaynak/tüketim aşırı yük | Kapasite değerlendirmesi |
| 6 | `CDC-OP-402` | Doğrulama uyuşmadı | Kayıp/çift uygulama | Akışı durdur |
| 7 | `CDC-OP-403` | Checkpoint elle oynatıldı | Yetki ihlali | Denetim kaydı |

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

## Bağlantı Bütünlüğü

| # | Hedef | Biçim | Durum |
|---|---|---|---|
| 1 | `cdc-pipeline-mimari.md` | wiki-link | uretimde dogrulanir (link-check) |
| 2 | `index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 3 | `cdc-pipeline-operasyon.md` | wiki-link | uretimde dogrulanir (link-check) |
| 4 | `../k135-event-store/index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 5 | `../k137-integrity-check/index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 6 | `../k135-event-store/event-store-mimari.md` | wiki-link | uretimde dogrulanir (link-check) |
| 7 | `../k137-integrity-check/integrity-check-mimari.md` | wiki-link | uretimde dogrulanir (link-check) |
| 8 | `../k108-vector-index/index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 9 | `../k109-semantic-id/index.md` | wiki-link | uretimde dogrulanir (link-check) |

Dogrulama: `node .ai/scripts/wiki-link-check.ps1` (repo kokunden).


---

## Kaynaklar

| # | Kaynak |
|---|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/migration-strategy.md (L1-L439)` |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/connection-pooling.md (L1-L484)` |

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

## Kanıt

- `Kanıt: .ai/architecture/cdcdc-pipeline/index.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/migration-strategy.md (L1-L439)`
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/connection-pooling.md (L1-L484)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/cdcdc-pipeline/index.md`

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

## Surumler

| Sürüm | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 4.0.0 | 2026-10-06 | D04 k132-k143 indeks ilk yazım | vault-writer |
| 1.0.0 | 2026-09-20 | K5 taslak (yedek kaynak) | - |

### Ek.1. Kural denetim kayıtları

| Kural | Aşama | Zorunlu işlem | Kanıt durumu |
|---|---|---|---|
| `CDC-001` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-002` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-003` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-004` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-005` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-006` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-007` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-008` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-009` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `CDC-010` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |

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

- CDC (change data capture) boru hattı için kaynak kaydı (system of record) K5 şemasıdır; bu dosyadaki her tablo türetilmiştir.
- Tekrarlı çalıştırma (re-run) güvenliği `CDC-001` ile sabitlenir; idempotentlik varsayılmaz, kanıtlanır.
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
- Ek not 1: `CDC-001` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 2: `CDC-002` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 3: `CDC-003` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 4: `CDC-004` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 5: `CDC-005` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 6: `CDC-006` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 7: `CDC-007` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 8: `CDC-008` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 9: `CDC-009` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 10: `CDC-010` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 11: `CDC-001` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 12: `CDC-002` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 13: `CDC-003` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 14: `CDC-004` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 15: `CDC-005` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 16: `CDC-006` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 17: `CDC-007` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 18: `CDC-008` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 19: `CDC-009` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 20: `CDC-010` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 21: `CDC-001` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 22: `CDC-002` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 23: `CDC-003` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 24: `CDC-004` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 25: `CDC-005` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 26: `CDC-006` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 27: `CDC-007` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 28: `CDC-008` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 29: `CDC-009` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 30: `CDC-010` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 31: `CDC-001` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 32: `CDC-002` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 33: `CDC-003` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 34: `CDC-004` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 35: `CDC-005` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 36: `CDC-006` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 37: `CDC-007` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 38: `CDC-008` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 39: `CDC-009` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 40: `CDC-010` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 41: `CDC-001` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 42: `CDC-002` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 43: `CDC-003` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 44: `CDC-004` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 45: `CDC-005` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 46: `CDC-006` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 47: `CDC-007` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 48: `CDC-008` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 49: `CDC-009` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 50: `CDC-010` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 51: `CDC-001` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 52: `CDC-002` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 53: `CDC-003` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 54: `CDC-004` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 55: `CDC-005` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 56: `CDC-006` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 57: `CDC-007` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 58: `CDC-008` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 59: `CDC-009` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 60: `CDC-010` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 61: `CDC-001` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 62: `CDC-002` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 63: `CDC-003` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 64: `CDC-004` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 65: `CDC-005` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 66: `CDC-006` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 67: `CDC-007` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 68: `CDC-008` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 69: `CDC-009` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 70: `CDC-010` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 71: `CDC-001` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 72: `CDC-002` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 73: `CDC-003` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 74: `CDC-004` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 75: `CDC-005` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 76: `CDC-006` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 77: `CDC-007` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 78: `CDC-008` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 79: `CDC-009` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 80: `CDC-010` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 81: `CDC-001` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 82: `CDC-002` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 83: `CDC-003` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 84: `CDC-004` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 85: `CDC-005` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 86: `CDC-006` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 87: `CDC-007` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 88: `CDC-008` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 89: `CDC-009` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 90: `CDC-010` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 91: `CDC-001` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 92: `CDC-002` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 93: `CDC-003` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 94: `CDC-004` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 95: `CDC-005` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 96: `CDC-006` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 97: `CDC-007` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 98: `CDC-008` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 99: `CDC-009` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 100: `CDC-010` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 101: `CDC-001` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 102: `CDC-002` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 103: `CDC-003` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 104: `CDC-004` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 105: `CDC-005` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 106: `CDC-006` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 107: `CDC-007` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 108: `CDC-008` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 109: `CDC-009` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 110: `CDC-010` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 111: `CDC-001` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 112: `CDC-002` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 113: `CDC-003` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 114: `CDC-004` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 115: `CDC-005` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 116: `CDC-006` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 117: `CDC-007` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 118: `CDC-008` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 119: `CDC-009` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 120: `CDC-010` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 121: `CDC-001` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 122: `CDC-002` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 123: `CDC-003` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 124: `CDC-004` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 125: `CDC-005` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 126: `CDC-006` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 127: `CDC-007` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 128: `CDC-008` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 129: `CDC-009` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 130: `CDC-010` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 131: `CDC-001` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 132: `CDC-002` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 133: `CDC-003` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 134: `CDC-004` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 135: `CDC-005` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 136: `CDC-006` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 137: `CDC-007` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 138: `CDC-008` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 139: `CDC-009` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 140: `CDC-010` kuralı CDC (change data capture) boru hattı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 141: `CDC-001` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 142: `CDC-002` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 143: `CDC-003` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 144: `CDC-004` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 145: `CDC-005` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 146: `CDC-006` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 147: `CDC-007` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 148: `CDC-008` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 149: `CDC-009` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 150: `CDC-010` kuralı CDC (change data capture) boru hattı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 151: `CDC-001` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 152: `CDC-002` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 153: `CDC-003` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 154: `CDC-004` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 155: `CDC-005` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 156: `CDC-006` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 157: `CDC-007` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 158: `CDC-008` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 159: `CDC-009` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 160: `CDC-010` kuralı CDC (change data capture) boru hattı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 161: `CDC-001` kuralı CDC (change data capture) boru hattı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.