---
title: "133 Okuma Replikası — Mimari ve Tasarim"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 133-read-replica · Okuma Replikası (Mimari)


---

Bu dosya **okuma replikası** alanının tasarımını tanımlar: kapsam, akış, kurallar, veri modeli, kenar durumlar, hata modları ve bağımlılık matrisi. Kardeş dosya: operasyon.


---

- **Amaç (A):** Okuma yükünü yazma kaynağından ayıran replika (read replica) yönlendirme tasarımını tanımlar: gecikme (lag) ölçümü, okuma tutarlılığı seçimi, replika sağlık durumları ve yazma sonrası okuma kuralı.

---

- **Sorumlu persona:** data-engineer · performance-engineer

---

- **Eşleşen klasor:** replika gecikmesi → parçalama anahtarı


---

## Kapsam

### Kapsamda (In)

| # | Kapsam |
|---|---|
| 1 | Okuma/yazma yönlendirme kuralı |
| 2 | Gecikme (replication lag) ölçümü |
| 3 | Tutarlılık seviyesi seçimi |
| 4 | Replika sağlık durum makinesi |

---

### Kapsamdisi (Out)

| # | Kapsamdisi |
|---|---|
| 1 | Replikasyon ürünü seçimi |
| 2 | Yedekleme (k5 backup) |
| 3 | Kullanıcı yönetimi (K6) |
| 4 | Sorgu optimizasyonu ayrı dosya |

---

## Mimari Akış

| Adim | Asama | Aciklama | Kural |
|---|---|---|---|
| 1 | Talep | İstemci okuma isteği K9 üzerinden gelir | REPL-001 |
| 2 | Sınıf | Okuma, tutarlılık etiketine göre sınıflanır | REPL-002 |
| 3 | Yönlendirme | Etiket + lag durumuna göre hedef seçilir | REPL-003 |
| 4 | Yedek | Sağlıklı replika yoksa yazma kaynağına düşülür | REPL-004 |
| 5 | Ölçüm | Yanıt süresi + lag K12'ye yazılır | REPL-005 |
| 6 | Kapanış | Eşik aşımında replika devre dışı | REPL-006 |

---

## Kurallar

| Kural | Tanim | Ihlal riski |
|---|---|---|
| `REPL-001` | Okuma isteği bir tutarlılık etiketi taşır; etiketsiz istek varsayılan olarak en taze (fresh) okunur. | Tutarlılık sessizce bozulmasın |
| `REPL-002` | Yazma hemen sonrası okuma (read-your-writes) etiketi zorunludur. | Kullanıcı kendi yazısını görememem |
| `REPL-003` | Yönlendirme kararı lag ölçümüne göre verilir; lag bilinmiyorsa replika kullanılmaz. | Kör yönlendirme bayat veri üretir |
| `REPL-004` | Sağlıklı replika yoksa okuma yazma kaynağına geri döner ve olay kaydedilir. | Kesintisizlik |
| `REPL-005` | Replikadan yalnız okunur; yazma isteği asla replikaya yönlendirilmez. | Yazma kaybı |
| `REPL-006` | Lag eşiği aşıldıysa replika otomatik olarak havuzdan çıkarılır. | Bayat veri yayılımı |
| `REPL-007` | Replika sayısı ve ürün adı seçilmedi olarak işaretlenir; tahmin yazılmaz. | ZERO-HALLUCINATION |
| `REPL-008` | Bağlantı havuzu boyutu okuma yüküne göre ölçülür; varsayılan sayı uydurulmaz. | Ölçüm yoksa eşik yok |
| `REPL-009` | Lag ölçümü olmadan replika sağlıklı sayılmaz. | Eksik metrik ≠ sağlıklı |
| `REPL-010` | Tutarlılık etiketi API sözleşmesinde görünür (header/parametre). | Gizli davranış test edilemez |

---

## Veri Modeli

| Alan | Tip | Aciklama | Kural |
|---|---|---|---|
| `replica_id` | `VARCHAR` | Replika kimliği | `REPL-007` |
| `role` | `ENUM` | primary/replica/unknown | `REPL-005` |
| `lag_ms` | `BIGINT` | Son bilinen gecikme | `REPL-003` |
| `health` | `ENUM` | healthy/degraded/down | `REPL-006` |
| `last_seen_at` | `DATETIME` | Son sağlık bildirimi | `REPL-009` |
| `consistency` | `ENUM` | fresh/bounded/stale | `REPL-001` |
| `pool_size` | `INT` | Açık bağlantı sayısı | `REPL-008` |
| `error_rate` | `DECIMAL` | Hata oranı penceresi | `REPL-006` |

---

## Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Kod |
|---|---|---|---|
| E01 | Tüm replikalar down | Okuma yazma kaynağına düşer + olay | `REPL-E01` |
| E02 | Lag ölçümü durdu | Replika sağlıksız sayılır | `REPL-E02` |
| E03 | Yazma sonrası anlık okuma | Kaynak üzerinden okunur | `REPL-E03` |
| E04 | Lag eşiği tam sınırda | Sınır üstü kabul edilir (aşım = çıkarma) | `REPL-E04` |
| E05 | Eşzamanlı çok istek | Havuz kuyruğa alınır, bağlantı şişirilmez | `REPL-E05` |
| E06 | Ağ bölünmesi (replikaya erişim yok) | Backoff + kaynak fallback | `REPL-E06` |
| E07 | Bilinmeyen replika durumu | Kullanılmaz, UNKNOWN işaretlenir | `REPL-E07` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `REPL-401` | Lag eşiği aşıldı | Yavaş replikasyon | Replikayı havuzdan çıkar |
| 2 | `REPL-402` | Okuma hatası | Bağlantı/kimlik sorunu | Fallback + olay |
| 3 | `REPL-403` | Havuz tükendi | Bağlantı sızıntısı | Kuyruk + alarm |
| 4 | `REPL-404` | Etiket bilinmiyor | Sözleşme ihlali | Varsayılan uygula + log |
| 5 | `REPL-405` | Yanıt süresi fırladı | Aşırı yük | Backpressure |
| 6 | `REPL-406` | Rol karışıklığı | Yanlış rol bildirimi | Sağlık kontrolünü durdur |
| 7 | `REPL-407` | Okuma/yazma yarışması | Tutarsız okuma | read-your-writes etiketi |
| 8 | `REPL-408` | Bilinmeyen ürün sürümü | Kanıt yok | VERIFICATION REQUIRED |

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
| K10 uygulama | dolayli | kullanici islemi | talep/sekm verisi |
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
| T-01 | Yazma sonrası anlık okuma | Aynı değer görünür |
| T-02 | Lag eşiği aşımı | Replika çıkar (REPL-006) |
| T-03 | Tüm replikalar down | Kaynak fallback |
| T-04 | Lag metriği yok | Replika kullanılmaz |
| T-05 | Etiketsiz istek | fresh varsayılanı |
| T-06 | Havuz tükenmesi | Kuyruk + alarm |
| T-07 | Yanlış rol bildirimi | Okuma durdurulur |
| T-08 | Yalnız yazma trafiği | Replikaya yazma yok |

---

## ADR Adaylari

| # | Karar | Reddedilen | Risk | Durum |
|---|---|---|---|---|
| A1 | Etiketli okuma API'ye açık | Sunucu içi otomatik karar | Davranış test edilebilir olsun | kabul |
| A2 | Lag bilinmiyorsa replika yok | Tahmini tazelik | Sessiz bayat veri | kabul |
| A3 | Ürün/seçim yazılmadı | Spesifik replika teknolojisi adı | Kanıt yok | açık — VERIFICATION REQUIRED |
| A4 | Fallback yazma kaynağı | Hata döndür | Kesintisizlik > gecikme | kabul |
| A5 | Sağlık = metrik + bildirim | Yalnız bağlantı testi | Eksik metrik yanıltır | kabul |

---

## Riskler

| Risk | Etki | Onlem |
|---|---|---|
| Yük altında tek kaynak | Yazma kaynağı şişer | Eşik alarmı + kapasite |
| Lag metriğinin sahte sağlığı | Bayat veri | Çift kaynaklı ölçüm |
| Havuz sızıntısı | Bağlantı tükenmesi | Havuz üst sınırı |
| Etiket ihlali | Tutarlılık kırılması | Sözleşme testi |
| Ürün erken kilitlenmesi | Yeniden iş | Seçim geciktirildi |

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
| `Router` | Okumayı hedefe yönlendirir | Yazmaz |
| `LagProbe` | Gecikmeyi ölçer | Karar vermez |
| `HealthRegistry` | Durum makinesini tutar | Trafiği kesmez |
| `PoolGuard` | Havuz sınırını korur | İş mantığı bilmez |

---

## Bağlantılar

### Kendi dosyalari

1. `[[read-replica-mimari.md]]`
2. `[[index.md]]`
3. `[[read-replica-dengeleme.md]]`

### Komsu / ilgili klasorler

1. `[[../k132-data-migration-history/index.md]]`
2. `[[../k134-shard-strategy/index.md]]`
3. `[[../k132-data-migration-history/goc-gecmisi-mimari.md]]`
4. `[[../k134-shard-strategy/shard-strateji-mimari.md]]`
5. `[[../k108-vector-index/index.md]]`
6. `[[../k109-semantic-id/index.md]]`

---

## Kanıt

- `Kanıt: .ai/architecture/read-replica/read-replica-mimari.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/mysql-18-database.md (Read Replicas bölümü, L1-L500)`
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/connection-pooling.md (L1-L484)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/read-replica/read-replica-mimari.md`

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
| okuma replikası kayit politikasi | **R** | C | A | C | C | I |
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
| `Okuma replikası` | `read replica` | Yazmadan ayrı okuma kopyası |
| `Gecikme` | `replication lag` | Kaynak-replika gecikmesi |
| `Yazma sonrası okuma` | `read-your-writes` | Kendi yazısını görme |
| `Sınırlı tazelik` | `bounded staleness` | İzin verilen gecikme |
| `Yedek düşüşü` | `fallback` | Kaynağa geri dönme |
| `Havuz` | `connection pool` | Paylaşımlı bağlantı kümesi |
| `Sağlık` | `health` | Kullanılabilirlik durumu |
| `Dengeleme` | `load balancing` | Okuma dağıtımı |
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
| `REPL-001` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `REPL-002` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `REPL-003` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `REPL-004` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `REPL-005` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `REPL-006` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `REPL-007` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `REPL-008` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `REPL-009` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `REPL-010` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |

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

- okuma replikası için kaynak kaydı (system of record) K5 şemasıdır; bu dosyadaki her tablo türetilmiştir.
- Tekrarlı çalıştırma (re-run) güvenliği `REPL-001` ile sabitlenir; idempotentlik varsayılmaz, kanıtlanır.
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
- Ek not 1: `REPL-001` kuralı okuma replikası akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 2: `REPL-002` kuralı okuma replikası akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 3: `REPL-003` kuralı okuma replikası akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 4: `REPL-004` kuralı okuma replikası akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 5: `REPL-005` kuralı okuma replikası akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 6: `REPL-006` kuralı okuma replikası akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 7: `REPL-007` kuralı okuma replikası akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 8: `REPL-008` kuralı okuma replikası akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 9: `REPL-009` kuralı okuma replikası akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 10: `REPL-010` kuralı okuma replikası akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 11: `REPL-001` kuralı okuma replikası akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 12: `REPL-002` kuralı okuma replikası akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 13: `REPL-003` kuralı okuma replikası akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 14: `REPL-004` kuralı okuma replikası akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 15: `REPL-005` kuralı okuma replikası akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 16: `REPL-006` kuralı okuma replikası akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 17: `REPL-007` kuralı okuma replikası akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 18: `REPL-008` kuralı okuma replikası akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 19: `REPL-009` kuralı okuma replikası akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 20: `REPL-010` kuralı okuma replikası akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 21: `REPL-001` kuralı okuma replikası akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 22: `REPL-002` kuralı okuma replikası akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 23: `REPL-003` kuralı okuma replikası akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 24: `REPL-004` kuralı okuma replikası akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 25: `REPL-005` kuralı okuma replikası akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 26: `REPL-006` kuralı okuma replikası akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 27: `REPL-007` kuralı okuma replikası akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 28: `REPL-008` kuralı okuma replikası akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 29: `REPL-009` kuralı okuma replikası akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 30: `REPL-010` kuralı okuma replikası akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 31: `REPL-001` kuralı okuma replikası akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 32: `REPL-002` kuralı okuma replikası akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 33: `REPL-003` kuralı okuma replikası akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 34: `REPL-004` kuralı okuma replikası akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 35: `REPL-005` kuralı okuma replikası akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 36: `REPL-006` kuralı okuma replikası akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 37: `REPL-007` kuralı okuma replikası akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 38: `REPL-008` kuralı okuma replikası akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 39: `REPL-009` kuralı okuma replikası akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 40: `REPL-010` kuralı okuma replikası akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 41: `REPL-001` kuralı okuma replikası akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 42: `REPL-002` kuralı okuma replikası akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 43: `REPL-003` kuralı okuma replikası akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 44: `REPL-004` kuralı okuma replikası akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 45: `REPL-005` kuralı okuma replikası akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 46: `REPL-006` kuralı okuma replikası akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 47: `REPL-007` kuralı okuma replikası akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 48: `REPL-008` kuralı okuma replikası akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 49: `REPL-009` kuralı okuma replikası akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.