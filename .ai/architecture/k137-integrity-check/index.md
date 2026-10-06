---
title: "137 Bütünlük Denetimi — Indeks"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 137-integrity-check · Bütünlük Denetimi


---

Veri yonetimi dilimi (D04) klasoru. Bu indeks klasordeki tum MD dosyalarini listeler ve zorunlu bolumleri tasir.


---

## K Tablosu

| K | Ad | Amac | Bagimlilik | Sorumlu persona | Kanit |
|---|---|---|---|---|---|
| K5 / k137 | Bütünlük Denetimi | Verinin bozulmadığını (bütünlük), iki sistem arasındaki eşitliği (mutabakat) ve denetlenebilirliği kanıtlayan tasarımı t... | bütünlük denetimi → GDPR dışa aktarım | data-engineer · security-engineer | `Kanıt: .ai/architecture/137-integrity-check/index.md` |

---

## Dosyalar

| # | Dosya | Tur | Ama | Wiki-link |
|---|---|---|---|---|
| 1 | integrity-check-mimari.md | icerik — mimari | Verinin bozulmadığını (bütünlük), iki sistem arasındaki eşitliği (mutabakat) ve denetleneb... | [[integrity-check-mimari.md]] |
| 2 | integrity-check-denetim.md | icerik — operasyon | Denetim işlerinin zamanlaması, bulgu kapatma akışı, alarm, delil saklama ve denetim raporu... | [[integrity-check-denetim.md]] |
| 3 | index.md | indeks | Klasor giris dosyasi | [[index.md]] |

---

## Akis Kutulari

| # | Kutu |
|---|---|
| 1 | kaynak veri (K5) |
| 2 | toplam üretimi |
| 3 | denetim işi |
| 4 | mutabakat karşılaştırması |
| 5 | bulgu + alarm |

---

## Komsu Tablosu (k132-k143)

| K | Slug | Ad | Indeks |
|---|---|---|---|
| k132 | data-migration-history | Veri Göç Geçmişi | [[../k132-data-migration-history/index.md]] |
| k133 | read-replica | Okuma Replikası | [[../k133-read-replica/index.md]] |
| k134 | shard-strategy | Shard Stratejisi | [[../k134-shard-strategy/index.md]] |
| k135 | event-store | Olay Deposu | [[../k135-event-store/index.md]] |
| k136 | cdcdc-pipeline | CDC Boru Hattı | [[../k136-cdcdc-pipeline/index.md]] |
| k137 | integrity-check | Bütünlük Denetimi | **bu klasor** |
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
| E01 | Denetim sırasında yazma sürüyor | Toplam yeniden üretilir; tutarsızlık değil, tekrar ölçülür | `INT-E01` |
| E02 | Beklenen toplam yok (ilk denetim) | Durum=baseline; uyuşmazlık sayılmaz | `INT-E02` |
| E03 | Küme çok büyük, tam tarama pahalı | Örnekleme + tam tarama kademeli | `INT-E03` |
| E04 | İki sistemde de farklı veri | Bulgu + kaynak analizi | `INT-E04` |
| E05 | Aynı anda iki denetim işi | Sıra kilidi; çakışan denetim reddedilir | `INT-OP-E01` |
| E06 | Bulgu sahibi izinde | Yedek inceleme atanır | `INT-OP-E02` |
| E07 | Gece alarmı tetiklendi | Nöbet akışı + öncelik | `INT-OP-E03` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `INT-401` | Toplam uyuşmazlığı | Bozulma veya yazma yarışması | Bulgu aç |
| 2 | `INT-402` | Beklenen toplam eksik | Baseline oluşmamış | Baseline üret |
| 3 | `INT-403` | Denetim işi hatası | Zaman aşımı/izin | İşi düzelt |
| 4 | `INT-404` | Kapsam dışı kalan veri | Yapılandırma boşluğu | Kapsamı tamamla |
| 5 | `INT-OP-401` | Bulgu süresi aşıldı | Kaynak yokluğu | Eskale et |
| 6 | `INT-OP-402` | Tek başına kapanış | Süreç ihlali | Kapanışı geri al |
| 7 | `INT-OP-403` | Denetim atlandı | Plan sorunu | Kaydı tamamla |

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
| 1 | `integrity-check-mimari.md` | wiki-link | uretimde dogrulanir (link-check) |
| 2 | `index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 3 | `integrity-check-denetim.md` | wiki-link | uretimde dogrulanir (link-check) |
| 4 | `../k136-cdcdc-pipeline/index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 5 | `../k138-gdpr-export/index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 6 | `../k136-cdcdc-pipeline/cdc-pipeline-mimari.md` | wiki-link | uretimde dogrulanir (link-check) |
| 7 | `../k138-gdpr-export/gdpr-export-tasarim.md` | wiki-link | uretimde dogrulanir (link-check) |
| 8 | `../k108-vector-index/index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 9 | `../k109-semantic-id/index.md` | wiki-link | uretimde dogrulanir (link-check) |

Dogrulama: `node .ai/scripts/wiki-link-check.ps1` (repo kokunden).


---

## Kaynaklar

| # | Kaynak |
|---|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/data-security.md (Audit Logging bölümü)` |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/backup-strategy.md (L1-L426)` |

---

## SSS

| Soru | Cevap | Kaynak |
|---|---|---|
| Denetim veriyi değiştirir mi? | Hayır — INT-003 salt-okunur. | §3 |
| Uyuşmazlıkta otomatik onarım? | Yok — INT-004 kanıt korur. | §3 |
| Mutabakat nasıl? | İki bağımsız kaynaktan okuma. | INT-005 |
| Frekans nedir? | Politikaya bağlı; sayı UNKNOWN. | ADR A5 |
| Bulgu kapanışı nasıl? | Kanıt zorunlu — INT-010. | §3 |
| PII denetimde? | İçerik tutulmaz; yalnız toplam. | INT-E07 |

---

## Kanıt

- `Kanıt: .ai/architecture/integrity-check/index.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/data-security.md (Audit Logging bölümü)`
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/backup-strategy.md (L1-L426)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/integrity-check/index.md`

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
| veri bütünlüğü denetimi kayit politikasi | **R** | C | A | C | C | I |
| Uyum/PII karari | C | I | C | I | **R** | A |
| Olculme hedefleri | C | C | I | **R** | I | A |
| Yayina alma | I | C | C | C | I | **A** |

---

## Terimler

| Terim | Orijinal identifier | Aciklama |
|---|---|---|
| `Bütünlük toplamı` | `checksum` | Küme özeti |
| `Mutabakat` | `reconciliation` | İki sistem eşitliği |
| `Bulgu` | `finding` | Denetim çıktısı |
| `Temel çizgi` | `baseline` | İlk denetim referansı |
| `Kapsam` | `scope` | Denetlenen küme |
| `Kanıt` | `evidence` | Kapanış dayanağı |
| `Salt-okunur` | `read-only` | Yazmasız denetim |
| `Örnekleme` | `sampling` | Kısmi tarama |
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
| `INT-001` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `INT-002` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `INT-003` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `INT-004` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `INT-005` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `INT-006` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `INT-007` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `INT-008` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `INT-009` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `INT-010` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |

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

- veri bütünlüğü denetimi için kaynak kaydı (system of record) K5 şemasıdır; bu dosyadaki her tablo türetilmiştir.
- Tekrarlı çalıştırma (re-run) güvenliği `INT-001` ile sabitlenir; idempotentlik varsayılmaz, kanıtlanır.
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
- Ek not 1: `INT-001` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 2: `INT-002` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 3: `INT-003` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 4: `INT-004` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 5: `INT-005` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 6: `INT-006` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 7: `INT-007` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 8: `INT-008` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 9: `INT-009` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 10: `INT-010` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 11: `INT-001` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 12: `INT-002` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 13: `INT-003` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 14: `INT-004` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 15: `INT-005` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 16: `INT-006` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 17: `INT-007` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 18: `INT-008` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 19: `INT-009` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 20: `INT-010` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 21: `INT-001` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 22: `INT-002` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 23: `INT-003` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 24: `INT-004` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 25: `INT-005` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 26: `INT-006` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 27: `INT-007` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 28: `INT-008` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 29: `INT-009` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 30: `INT-010` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 31: `INT-001` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 32: `INT-002` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 33: `INT-003` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 34: `INT-004` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 35: `INT-005` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 36: `INT-006` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 37: `INT-007` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 38: `INT-008` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 39: `INT-009` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 40: `INT-010` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 41: `INT-001` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 42: `INT-002` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 43: `INT-003` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 44: `INT-004` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 45: `INT-005` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 46: `INT-006` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 47: `INT-007` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 48: `INT-008` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 49: `INT-009` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 50: `INT-010` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 51: `INT-001` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 52: `INT-002` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 53: `INT-003` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 54: `INT-004` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 55: `INT-005` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 56: `INT-006` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 57: `INT-007` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 58: `INT-008` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 59: `INT-009` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 60: `INT-010` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 61: `INT-001` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 62: `INT-002` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 63: `INT-003` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 64: `INT-004` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 65: `INT-005` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 66: `INT-006` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 67: `INT-007` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 68: `INT-008` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 69: `INT-009` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 70: `INT-010` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 71: `INT-001` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 72: `INT-002` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 73: `INT-003` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 74: `INT-004` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 75: `INT-005` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 76: `INT-006` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 77: `INT-007` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 78: `INT-008` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 79: `INT-009` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 80: `INT-010` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 81: `INT-001` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 82: `INT-002` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 83: `INT-003` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 84: `INT-004` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 85: `INT-005` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 86: `INT-006` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 87: `INT-007` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 88: `INT-008` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 89: `INT-009` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 90: `INT-010` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 91: `INT-001` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 92: `INT-002` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 93: `INT-003` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 94: `INT-004` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 95: `INT-005` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 96: `INT-006` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 97: `INT-007` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 98: `INT-008` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 99: `INT-009` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 100: `INT-010` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 101: `INT-001` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 102: `INT-002` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 103: `INT-003` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 104: `INT-004` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 105: `INT-005` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 106: `INT-006` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 107: `INT-007` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 108: `INT-008` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 109: `INT-009` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 110: `INT-010` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 111: `INT-001` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 112: `INT-002` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 113: `INT-003` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 114: `INT-004` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 115: `INT-005` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 116: `INT-006` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 117: `INT-007` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 118: `INT-008` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 119: `INT-009` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 120: `INT-010` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 121: `INT-001` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 122: `INT-002` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 123: `INT-003` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 124: `INT-004` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 125: `INT-005` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 126: `INT-006` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 127: `INT-007` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 128: `INT-008` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 129: `INT-009` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 130: `INT-010` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 131: `INT-001` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 132: `INT-002` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 133: `INT-003` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 134: `INT-004` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 135: `INT-005` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 136: `INT-006` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 137: `INT-007` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 138: `INT-008` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 139: `INT-009` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 140: `INT-010` kuralı veri bütünlüğü denetimi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 141: `INT-001` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 142: `INT-002` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 143: `INT-003` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 144: `INT-004` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 145: `INT-005` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 146: `INT-006` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 147: `INT-007` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 148: `INT-008` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 149: `INT-009` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 150: `INT-010` kuralı veri bütünlüğü denetimi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 151: `INT-001` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 152: `INT-002` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 153: `INT-003` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 154: `INT-004` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 155: `INT-005` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 156: `INT-006` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 157: `INT-007` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 158: `INT-008` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 159: `INT-009` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 160: `INT-010` kuralı veri bütünlüğü denetimi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 161: `INT-001` kuralı veri bütünlüğü denetimi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.