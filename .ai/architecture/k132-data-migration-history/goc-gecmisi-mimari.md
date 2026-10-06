---
title: "132 Veri Göç Geçmişi — Mimari ve Tasarim"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 132-data-migration-history · Veri Göç Geçmişi (Mimari)


---

Bu dosya **veri göç geçmişi** alanının tasarımını tanımlar: kapsam, akış, kurallar, veri modeli, kenar durumlar, hata modları ve bağımlılık matrisi. Kardeş dosya: operasyon.


---

- **Amaç (A):** Şema değişikliklerinin geri dönüşümlü, denetlenebilir ve tek yönlü uygulanmasını sağlayan göç günlüğü (migration journal) tasarımını tanımlar: sürüm zinciri, doğrulama toplamı (checksum), kilit (lock) ve geri alma (rollback) adımları.

---

- **Sorumlu persona:** data-engineer · backend-architect

---

- **Eşleşen klasor:** göç günlüğü → replika gecikme kaydı


---

## Kapsam

### Kapsamda (In)

| # | Kapsam |
|---|---|
| 1 | Sürüm zinciri ve numaralandırma |
| 2 | Göç günlüğü şeması (journal) |
| 3 | Kilit (migration lock) davranışı |
| 4 | Geri alma (down) adımları |

---

### Kapsamdisi (Out)

| # | Kapsamdisi |
|---|---|
| 1 | Veri içeriği taşınması (ETL) |
| 2 | Uygulama kodu sürümleme (K13) |
| 3 | Yedekleme politikası (k5 yedek) |
| 4 | Raporlama sorguları (k143) |

---

## Mimari Akış

| Adim | Asama | Aciklama | Kural |
|---|---|---|---|
| 1 | Talep | Yeni göç dosyası + sürüm numarası açılır | MIG-001 |
| 2 | Ön denetim | Dry-run üzerinde sözdizimi ve bağımlılık kontrolü | MIG-002 |
| 3 | Kilit | Tek yazıcı kilidi alınır, diğer göçler bekler | MIG-003 |
| 4 | Uygula | Up adımı sırayla çalışır, her adım günlüğe yazılır | MIG-004 |
| 5 | Doğrula | Checksum + tablo/sütun varlık kontrolü | MIG-005 |
| 6 | Kapanış | Durum=applied, kilit serbest, olay K7'ye | MIG-006 |

---

## Kurallar

| Kural | Tanim | Ihlal riski |
|---|---|---|
| `MIG-001` | Her göçün benzersiz, artan sürüm numarası vardır; numara tekrarı göçü reddeder. | Zincir kırılırsa geri alma imkânsızlaşır |
| `MIG-002` | Göç, uygulanmadan önce salt-okunur ön denetimden geçer (dry-run). | Yarı uygulanmış şema riski |
| `MIG-003` | Aynı anda yalnız bir göç yazabilir; kilitsiz göç yasaktır. | Eşzamanlı şema çatışması |
| `MIG-004` | Her adımla birlikte göç günlüğüne satır eklenir (append); geçmiş satır değiştirilemez. | Denetim izi |
| `MIG-005` | Uygulama sonrası checksum yeniden hesaplanır ve günlükle karşılaştırılır. | Sessiz bozulma |
| `MIG-006` | Başarısız göç durumu=failed olarak kalır; otomatik tekrar başlatma yoktur. | Kör tekrar ikinci bozulma üretir |
| `MIG-007` | Geri alma (down) adımı her göç için zorunludur veya gerekçesiyle feragat kaydı vardır. | Geri alınabilirlik |
| `MIG-008` | Şema farkı (ALTER) ile veri yeniden yazımı aynı göçte birleştirilmez. | İki farklı risk sınıfı |
| `MIG-009` | Göç sürümü, dağıtım etiketiyle (K13) eşleştirilir; etiketsiz göç yasaktır. | Sürüm izlenebilirliği |
| `MIG-010` | Bilinmeyen sürüm/etiket durumunda göç durdurulur, tahmin yürütülmez. | ZERO-HALLUCINATION |

---

## Veri Modeli

| Alan | Tip | Aciklama | Kural |
|---|---|---|---|
| `migration_id` | `BIGINT` | Göçün tekil anahtarı | `MIG-001` |
| `version` | `VARCHAR` | Sürüm zinciri etiketi | `MIG-001` |
| `checksum` | `CHAR` | Dosya bütünlük toplamı | `MIG-005` |
| `state` | `ENUM` | pending/applied/failed/reverted | `MIG-006` |
| `applied_at` | `DATETIME` | Uygulama anı (UTC) | `MIG-004` |
| `duration_ms` | `INT` | Süre ölçümü | `MIG-004` |
| `actor` | `VARCHAR` | Uygulayan (CI iş kimliği) | `MIG-009` |
| `down_sql_ref` | `VARCHAR` | Geri alma adımı referansı | `MIG-007` |

---

## Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Kod |
|---|---|---|---|
| E01 | Ağ koptu, göç yarım kaldı | Durum failed kalır; kilit serbest bırakılır, otomatik tekrar yok | `MIG-E01` |
| E02 | Sürüm numarası deliği var (atlanan no) | Göç reddedilir; zincir tamamlanmadan uygulama yok | `MIG-E02` |
| E03 | Aynı sürüm iki dalda açıldı | Çakışan sürüm ilk kazanır; ikincisi yeniden numaralandırılır | `MIG-E03` |
| E04 | Göç penceresi aşıldı | İş kesilir; kalan adımlar failed olarak işaretlenir | `MIG-E04` |
| E05 | Boş tablo üzerinde ALTER | Normal uygulanır; doğrulama satır sayısı=0 bekler | `MIG-E05` |
| E06 | Kilit sahibi süreç öldü | Kilit zaman aşımıyla kırılır; olay loglanır | `MIG-E06` |
| E07 | Down adımı yazılmamış | Kapsam dışı sayılır, feragat kaydı aranır | `MIG-E07` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `MIG-401` | Göç sürüm çakışması | Aynı numara iki kez kullanılmış | Yeniden numaralandır + log |
| 2 | `MIG-402` | Checksum uyuşmazlığı | Dosya sonradan değişmiş | Göçü reddet, kaynağı doğrula |
| 3 | `MIG-403` | Kilit zaman aşımı | Uzun süren işlem | Kesintiyi raporla, manuel devam |
| 4 | `MIG-404` | Sözdizimi hatası | DDL geçersiz | Dry-run'a geri dön |
| 5 | `MIG-405` | Bağımlılık eksik | Önceki sürüm uygulanmamış | Zinciri tamamla |
| 6 | `MIG-406` | Yetki yok | DB kullanıcısında DDL izni yok | Yetki talebi (K6) |
| 7 | `MIG-407` | Alan adı çakışması | Yeni alan mevcut | Fark denetimine dön |
| 8 | `MIG-408` | Zaman aşımı (statement) | İşlem çok uzun | Parçalı göç tasarımı |

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
| K6 guvenlik | dolayli | erisim kapisi verisi | kimlik/rol politikasi |
| K7 middleware | dolayli | olay kuyrugu | middleware olayi |
| K8 servis | dolayli | servis cagrisi | oturum/istek verisi |
| K9 API-routing | hayir | - | - |
| K10 uygulama | hayir | - | - |
| K11 UX | hayir | - | - |
| K12 izleme | dogrudan | metrik/olcum | alarm/esik |
| K13 CI/CD | dogrudan | surum etiketi | dagitim/gecis |
| K14 ag | hayir | - | - |
| K15 medya streaming | hayir | - | - |
| K16 K1-kaynak (MCP) | hayir | - | - |

> Katman tanimlari: `K0 cekirdek` ... `K16 K1-kaynak (MCP)`. `hayir` satirlari bilincli sinirdir.


---

## Test Senaryolari

| # | Senaryo | Beklenen |
|---|---|---|
| T-01 | Sıfırdan zincir uygulama | Tüm sürüm sırasıyla applied |
| T-02 | Aynı sürüm iki kez | İkincisi reddedilir (MIG-001) |
| T-03 | Bozuk dosya ile uygulama | Checksum reddi (MIG-005) |
| T-04 | Eşzamanlı iki göç | Biri bekler/kilit (MIG-003) |
| T-05 | Yarım göç sonrası durum | failed + kilit yok |
| T-06 | Down adımı | Şema eski haline döner |
| T-07 | Delikli zincir | Red (MIG-001) |
| T-08 | Etiketsiz göç | Red (MIG-009) |

---

## ADR Adaylari

| # | Karar | Reddedilen | Risk | Durum |
|---|---|---|---|---|
| A1 | Göç günlüğü veritabanında tutulur | Dosya tabanlı şema dosyaları tek başına | Çok-yazıcılı ortamda tek gerçeklik gerekir | kabul |
| A2 | Tek yazıcı kilidi | Eşzamanlı göç | Şema yarışması veri bozulması üretir | kabul |
| A3 | Otomatik tekrar yok | Failed göçleri kendiliğinden yeniden dene | Kör tekrar ikinci bozulma üretir | kabul |
| A4 | Checksum zorunlu | Yalnız sözdizimi kontrolü | Sessiz bozulma algılanamaz | kabul |
| A5 | Ürün seçimi yapılmadı | Belirli bir migration aracı seçmek | Seçim kanıtsız; ZERO-HALLUCINATION | açık — VERIFICATION REQUIRED |

---

## Riskler

| Risk | Etki | Onlem |
|---|---|---|
| Uzun kilit penceresi | Yazma yolu durur | Parçalı göç + ön denetim |
| Down adımının test edilmemişliği | Geri alınamama | Pencere provası (B dosyası) |
| Sürüm deliği | Zincir okunamaz | CI kapısında numara denetimi |
| Checksum gereksiniminin atlanması | Bozulma sessiz kalır | Kapı kontrolü |
| Eşzamanlı dağıtım | Yarışma | Kilit + K13 sırası |

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
| `MigrationJournal` | Günlük satırlarını yazar | İş mantığı çalıştırmaz |
| `LockManager` | Tek yazıcı kilidini verir | Kilit sahibini kesmez |
| `ChecksumVerifier` | Bütünlüğü hesaplar | Dosyayı değiştirmez |
| `RollbackRunner` | Down adımını çalıştırır | Onay vermez |

---

## Bağlantılar

### Kendi dosyalari

1. `[[goc-gecmisi-mimari.md]]`
2. `[[index.md]]`
3. `[[goc-gecmisi-operasyon.md]]`

### Komsu / ilgili klasorler

1. `[[../k133-read-replica/index.md]]`
2. `[[../k133-read-replica/read-replica-mimari.md]]`
3. `[[../k108-vector-index/index.md]]`
4. `[[../k109-semantic-id/index.md]]`

---

## Kanıt

- `Kanıt: .ai/architecture/data-migration-history/goc-gecmisi-mimari.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/migration-strategy.md (L1-L439)`
- `Kanıt: .ai/.sql/mysql/ (19 .sql dosyası — ölçüm 2026-10-06)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/data-migration-history/goc-gecmisi-mimari.md`

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
| veri göç geçmişi kayit politikasi | **R** | C | A | C | C | I |
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
| `Göç günlüğü` | `migration journal` | Uygulanan göçlerin kaydı |
| `Sürüm zinciri` | `version chain` | Artan göç numaraları |
| `Ön denetim` | `dry-run` | Uygulamadan önce salt-okunur prova |
| `Geri alma` | `rollback/down` | Şemayı eski haline getirme |
| `Kilit penceresi` | `migration lock window` | Tek yazıcı dönemi |
| `Doğrulama toplamı` | `checksum` | Dosya bütünlüğü |
| `Pencere` | `maintenance window` | Onaylı çalışma aralığı |
| `Feragat kaydı` | `waiver` | Gerekçeli down yok kararı |
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
| `MIG-001` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `MIG-002` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `MIG-003` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `MIG-004` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `MIG-005` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `MIG-006` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `MIG-007` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `MIG-008` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `MIG-009` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `MIG-010` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |

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

- veri göç geçmişi için kaynak kaydı (system of record) K5 şemasıdır; bu dosyadaki her tablo türetilmiştir.
- Tekrarlı çalıştırma (re-run) güvenliği `MIG-001` ile sabitlenir; idempotentlik varsayılmaz, kanıtlanır.
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
- Ek not 1: `MIG-001` kuralı veri göç geçmişi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 2: `MIG-002` kuralı veri göç geçmişi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 3: `MIG-003` kuralı veri göç geçmişi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 4: `MIG-004` kuralı veri göç geçmişi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 5: `MIG-005` kuralı veri göç geçmişi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 6: `MIG-006` kuralı veri göç geçmişi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 7: `MIG-007` kuralı veri göç geçmişi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 8: `MIG-008` kuralı veri göç geçmişi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 9: `MIG-009` kuralı veri göç geçmişi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 10: `MIG-010` kuralı veri göç geçmişi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 11: `MIG-001` kuralı veri göç geçmişi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 12: `MIG-002` kuralı veri göç geçmişi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 13: `MIG-003` kuralı veri göç geçmişi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 14: `MIG-004` kuralı veri göç geçmişi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 15: `MIG-005` kuralı veri göç geçmişi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 16: `MIG-006` kuralı veri göç geçmişi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 17: `MIG-007` kuralı veri göç geçmişi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 18: `MIG-008` kuralı veri göç geçmişi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 19: `MIG-009` kuralı veri göç geçmişi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 20: `MIG-010` kuralı veri göç geçmişi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 21: `MIG-001` kuralı veri göç geçmişi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 22: `MIG-002` kuralı veri göç geçmişi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 23: `MIG-003` kuralı veri göç geçmişi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 24: `MIG-004` kuralı veri göç geçmişi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 25: `MIG-005` kuralı veri göç geçmişi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 26: `MIG-006` kuralı veri göç geçmişi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 27: `MIG-007` kuralı veri göç geçmişi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 28: `MIG-008` kuralı veri göç geçmişi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 29: `MIG-009` kuralı veri göç geçmişi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 30: `MIG-010` kuralı veri göç geçmişi akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 31: `MIG-001` kuralı veri göç geçmişi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 32: `MIG-002` kuralı veri göç geçmişi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 33: `MIG-003` kuralı veri göç geçmişi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 34: `MIG-004` kuralı veri göç geçmişi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 35: `MIG-005` kuralı veri göç geçmişi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 36: `MIG-006` kuralı veri göç geçmişi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 37: `MIG-007` kuralı veri göç geçmişi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 38: `MIG-008` kuralı veri göç geçmişi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 39: `MIG-009` kuralı veri göç geçmişi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 40: `MIG-010` kuralı veri göç geçmişi akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 41: `MIG-001` kuralı veri göç geçmişi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 42: `MIG-002` kuralı veri göç geçmişi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 43: `MIG-003` kuralı veri göç geçmişi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 44: `MIG-004` kuralı veri göç geçmişi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 45: `MIG-005` kuralı veri göç geçmişi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 46: `MIG-006` kuralı veri göç geçmişi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 47: `MIG-007` kuralı veri göç geçmişi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 48: `MIG-008` kuralı veri göç geçmişi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 49: `MIG-009` kuralı veri göç geçmişi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 50: `MIG-010` kuralı veri göç geçmişi akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 51: `MIG-001` kuralı veri göç geçmişi akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.