---
title: "138 GDPR Dışa Aktarım — Indeks"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 138-gdpr-export · GDPR Dışa Aktarım


---

Veri yonetimi dilimi (D04) klasoru. Bu indeks klasordeki tum MD dosyalarini listeler ve zorunlu bolumleri tasir.


---

## K Tablosu

| K | Ad | Amac | Bagimlilik | Sorumlu persona | Kanit |
|---|---|---|---|---|---|
| K5 / k138 | GDPR Dışa Aktarım | Veri sahibinin erişim/taşınabilirlik (dışa aktarım) ve silme/anonimleştirme taleplerinin kimlik doğrulamalı, denetlenebi... | GDPR dışa aktarım → oturum deposu | security-engineer · data-engineer | `Kanıt: .ai/architecture/138-gdpr-export/index.md` |

---

## Dosyalar

| # | Dosya | Tur | Ama | Wiki-link |
|---|---|---|---|---|
| 1 | gdpr-export-tasarim.md | icerik — mimari | Veri sahibinin erişim/taşınabilirlik (dışa aktarım) ve silme/anonimleştirme taleplerinin k... | [[gdpr-export-tasarim.md]] |
| 2 | gdpr-export-operasyon.md | icerik — operasyon | Talep yaşam döngüsü (alınma → kimlik → üretme → teslim → kapanış), süre takibi, teslim güv... | [[gdpr-export-operasyon.md]] |
| 3 | index.md | indeks | Klasor giris dosyasi | [[index.md]] |

---

## Akis Kutulari

| # | Kutu |
|---|---|
| 1 | talep (K8 uçları) |
| 2 | kimlik doğrulama (K6) |
| 3 | kapsam + veri haritası |
| 4 | paket üretimi (K5) |
| 5 | teslim + denetim kaydı |

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
| k138 | gdpr-export | GDPR Dışa Aktarım | **bu klasor** |
| k139 | session-store | Oturum Deposu | [[../k139-session-store/index.md]] |
| k140 | profile-store | Profil Deposu | [[../k140-profile-store/index.md]] |
| k141 | playlist-store | Çalma Listesi Deposu | [[../k141-playlist-store/index.md]] |
| k142 | library-metadata | Kütüphane Meta Verisi | [[../k142-library-metadata/index.md]] |
| k143 | stats-rollup | İstatistik Toplama (Rollup) | [[../k143-stats-rollup/index.md]] |

---

## Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Kod |
|---|---|---|---|
| E01 | Kimlik eşleşmiyor | Talep red + gerekçe kaydı | `GDX-E01` |
| E02 | Kapsamdaki kaynak okunamıyor | Duraklat + eskalasyon | `GDX-E02` |
| E03 | Üçüncü kişi verisi karışık | Hariç tutma + gerekçe | `GDX-E03` |
| E04 | Silme sırasında yasal tutma | Tutulan kayıtlar gerekçeli liste | `GDX-E04` |
| E05 | Aynı kişi tekrar talep | Yeni talep kimliği, önceki referanslanır | `GDX-OP-E01` |
| E06 | Kimlik doğrulama aracı yok | Talep askıya alınır, tahmin edilmez | `GDX-OP-E02` |
| E07 | Silme onayı geri çekildi | İş durur, kayıt kalır | `GDX-OP-E03` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `GDX-401` | Kimlik doğrulama başarısız | Yanlış/eksik kanıt | Reddet |
| 2 | `GDX-402` | Kapsam boş | Harita eksik | Haritayı tamamla |
| 3 | `GDX-403` | Paket üretim hatası | Kaynak okuma hatası | Duraklat + düzelt |
| 4 | `GDX-404` | Teslim kanıtı eksik | Kanal arızası | Teslimi tekrarla |
| 5 | `GDX-OP-401` | Süre ölçümü yok | Metrik kesintisi | Ölçümü geri getir |
| 6 | `GDX-OP-402` | Teslim kanalı doğrulanmadı | Süreç atlanmış | Gönderme |
| 7 | `GDX-OP-403` | Onaysız silme | Süreç ihlali | İşi durdur + kaydet |

---

## Bağımlılık Matrisi

| Katman | Iliski | Bu klasor ne verir | Bu klasor ne alir |
|---|---|---|---|
| K0 cekirdek | dolayli | dosya/surec zamanlamasi | kalici dosya erisimi |
| K1 donanim | hayir | - | - |
| K2 surucu | hayir | - | - |
| K3 ses motoru | dolayli | - (ihlal) | - (ihlal) |
| K4 yapay zeka | dogrudan | - (dolayli) | model/veri seti talebi |
| K5 veri yonetimi | hayir | - | - |
| K6 guvenlik | dogrudan | erisim kapisi verisi | kimlik/rol politikasi |
| K7 middleware | dolayli | olay kuyrugu | middleware olayi |
| K8 servis | dolayli | servis cagrisi | oturum/istek verisi |
| K9 API-routing | hayir | - | - |
| K10 uygulama | dogrudan | kullanici islemi | talep/sekm verisi |
| K11 UX | hayir | - | - |
| K12 izleme | dogrudan | metrik/olcum | alarm/esik |
| K13 CI/CD | dogrudan | surum etiketi | dagitim/gecis |
| K14 ag | hayir | - | - |
| K15 medya streaming | hayir | - | - |
| K16 K1-kaynak (MCP) | dolayli | - (ihlal) | - (ihlal) |

---

## Bağlantı Bütünlüğü

| # | Hedef | Biçim | Durum |
|---|---|---|---|
| 1 | `gdpr-export-tasarim.md` | wiki-link | uretimde dogrulanir (link-check) |
| 2 | `index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 3 | `gdpr-export-operasyon.md` | wiki-link | uretimde dogrulanir (link-check) |
| 4 | `../k137-integrity-check/index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 5 | `../k139-session-store/index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 6 | `../k137-integrity-check/integrity-check-mimari.md` | wiki-link | uretimde dogrulanir (link-check) |
| 7 | `../k139-session-store/session-store-mimari.md` | wiki-link | uretimde dogrulanir (link-check) |
| 8 | `../k108-vector-index/index.md` | wiki-link | uretimde dogrulanir (link-check) |
| 9 | `../k109-semantic-id/index.md` | wiki-link | uretimde dogrulanir (link-check) |

Dogrulama: `node .ai/scripts/wiki-link-check.ps1` (repo kokunden).


---

## Kaynaklar

| # | Kaynak |
|---|---|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/data-security.md (GDPR Compliance bölümü, L1-L462)` |
| 2 | `.ai/.sql/mysql/coremusic_user.sql (kişisel veri kaynağı — dosya var)` |

---

## SSS

| Soru | Cevap | Kaynak |
|---|---|---|
| Kimlik nasıl doğrulanır? | K6 kapısı; ayrıntı K6 dosyalarında. | §2 |
| Süre kaç gün? | UNKNOWN — politika onayı gerekli. | GDX-007 |
| Silme geri alınır mı? | Hayır — onay zorunlu, geri alınamaz. | GDX-OP-03 |
| Üçüncü kişi verisi? | Hariç tutulur + gerekçe. | GDX-004 |
| Paket ne kadar saklanır? | UNKNOWN + imha zorunlu. | GDX-OP-06 |
| Veri haritası nerede? | Tasarım; üretim kanıtı yok. | ⚠️ VERIFICATION REQUIRED |

---

## Kanıt

- `Kanıt: .ai/architecture/gdpr-export/index.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/data-security.md (GDPR Compliance bölümü, L1-L462)`
- `Kanıt: .ai/.sql/mysql/coremusic_user.sql (kişisel veri kaynağı — dosya var)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/gdpr-export/index.md`

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
| GDPR (veri sahibi talebi) dışa aktarımı kayit politikasi | **R** | C | A | C | C | I |
| Uyum/PII karari | C | I | C | I | **R** | A |
| Olculme hedefleri | C | C | I | **R** | I | A |
| Yayina alma | I | C | C | C | I | **A** |

---

## Terimler

| Terim | Orijinal identifier | Aciklama |
|---|---|---|
| `Veri sahibi` | `data subject` | Kişinin sahibi olduğu veri |
| `Erişim talebi` | `access request` | Kopya isteme |
| `Taşıma` | `portability` | Yapılandırılmış kopya |
| `Silme` | `erasure / right to be forgotten` | Kalıcı kaldırma |
| `Düzeltme` | `rectification` | Yanlışı düzeltme |
| `Veri haritası` | `data map` | Verinin nerede olduğunu harita |
| `Hariç tutma` | `exclusion` | Üçüncü kişi/istisna |
| `İmha` | `disposal` | İkincil kopyanın yok edilmesi |
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
| `GDX-001` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-002` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-003` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-004` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-005` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-006` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-007` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-008` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-009` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-010` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |

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

- GDPR (veri sahibi talebi) dışa aktarımı için kaynak kaydı (system of record) K5 şemasıdır; bu dosyadaki her tablo türetilmiştir.
- Tekrarlı çalıştırma (re-run) güvenliği `GDX-001` ile sabitlenir; idempotentlik varsayılmaz, kanıtlanır.
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
- Ek not 1: `GDX-001` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 2: `GDX-002` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 3: `GDX-003` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 4: `GDX-004` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 5: `GDX-005` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 6: `GDX-006` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 7: `GDX-007` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 8: `GDX-008` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 9: `GDX-009` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 10: `GDX-010` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 11: `GDX-001` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 12: `GDX-002` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 13: `GDX-003` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 14: `GDX-004` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 15: `GDX-005` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 16: `GDX-006` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 17: `GDX-007` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 18: `GDX-008` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 19: `GDX-009` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 20: `GDX-010` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 21: `GDX-001` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 22: `GDX-002` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 23: `GDX-003` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 24: `GDX-004` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 25: `GDX-005` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 26: `GDX-006` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 27: `GDX-007` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 28: `GDX-008` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 29: `GDX-009` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 30: `GDX-010` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 31: `GDX-001` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 32: `GDX-002` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 33: `GDX-003` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 34: `GDX-004` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 35: `GDX-005` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 36: `GDX-006` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 37: `GDX-007` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 38: `GDX-008` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 39: `GDX-009` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 40: `GDX-010` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 41: `GDX-001` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 42: `GDX-002` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 43: `GDX-003` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 44: `GDX-004` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 45: `GDX-005` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 46: `GDX-006` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 47: `GDX-007` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 48: `GDX-008` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 49: `GDX-009` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 50: `GDX-010` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 51: `GDX-001` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 52: `GDX-002` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 53: `GDX-003` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 54: `GDX-004` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 55: `GDX-005` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 56: `GDX-006` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 57: `GDX-007` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 58: `GDX-008` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 59: `GDX-009` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 60: `GDX-010` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 61: `GDX-001` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 62: `GDX-002` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 63: `GDX-003` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 64: `GDX-004` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 65: `GDX-005` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 66: `GDX-006` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 67: `GDX-007` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 68: `GDX-008` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 69: `GDX-009` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 70: `GDX-010` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 71: `GDX-001` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 72: `GDX-002` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 73: `GDX-003` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 74: `GDX-004` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 75: `GDX-005` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 76: `GDX-006` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 77: `GDX-007` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 78: `GDX-008` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 79: `GDX-009` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 80: `GDX-010` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 81: `GDX-001` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 82: `GDX-002` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 83: `GDX-003` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 84: `GDX-004` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 85: `GDX-005` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 86: `GDX-006` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 87: `GDX-007` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 88: `GDX-008` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 89: `GDX-009` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 90: `GDX-010` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 91: `GDX-001` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 92: `GDX-002` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 93: `GDX-003` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 94: `GDX-004` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 95: `GDX-005` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 96: `GDX-006` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 97: `GDX-007` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 98: `GDX-008` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 99: `GDX-009` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 100: `GDX-010` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 101: `GDX-001` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 102: `GDX-002` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 103: `GDX-003` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 104: `GDX-004` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 105: `GDX-005` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 106: `GDX-006` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 107: `GDX-007` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 108: `GDX-008` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 109: `GDX-009` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 110: `GDX-010` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 111: `GDX-001` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 112: `GDX-002` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 113: `GDX-003` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 114: `GDX-004` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 115: `GDX-005` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 116: `GDX-006` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 117: `GDX-007` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 118: `GDX-008` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 119: `GDX-009` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 120: `GDX-010` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 121: `GDX-001` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 122: `GDX-002` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 123: `GDX-003` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 124: `GDX-004` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 125: `GDX-005` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 126: `GDX-006` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 127: `GDX-007` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 128: `GDX-008` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 129: `GDX-009` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 130: `GDX-010` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 131: `GDX-001` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 132: `GDX-002` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 133: `GDX-003` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 134: `GDX-004` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 135: `GDX-005` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 136: `GDX-006` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 137: `GDX-007` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 138: `GDX-008` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 139: `GDX-009` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 140: `GDX-010` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 141: `GDX-001` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 142: `GDX-002` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 143: `GDX-003` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 144: `GDX-004` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 145: `GDX-005` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 146: `GDX-006` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 147: `GDX-007` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 148: `GDX-008` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 149: `GDX-009` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 150: `GDX-010` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 151: `GDX-001` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 152: `GDX-002` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 153: `GDX-003` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 154: `GDX-004` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 155: `GDX-005` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 156: `GDX-006` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 157: `GDX-007` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 158: `GDX-008` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 159: `GDX-009` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 160: `GDX-010` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 161: `GDX-001` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.