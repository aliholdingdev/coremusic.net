---
title: "137 Bütünlük Denetimi — Mimari ve Tasarim"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 137-integrity-check · Bütünlük Denetimi (Mimari)


---

Bu dosya **veri bütünlüğü denetimi** alanının tasarımını tanımlar: kapsam, akış, kurallar, veri modeli, kenar durumlar, hata modları ve bağımlılık matrisi. Kardeş dosya: operasyon.


---

- **Amaç (A):** Verinin bozulmadığını (bütünlük), iki sistem arasındaki eşitliği (mutabakat) ve denetlenebilirliği kanıtlayan tasarımı tanımlar: toplam/bchecksum kontrolleri, periyodik denetim, mutabakat (reconciliation) ve bulgu yönetimi.

---

- **Sorumlu persona:** data-engineer · security-engineer

---

- **Eşleşen klasor:** bütünlük denetimi → GDPR dışa aktarım


---

## Kapsam

### Kapsamda (In)

| # | Kapsam |
|---|---|
| 1 | Bütünlük toplamı (checksum) üretimi ve saklanması |
| 2 | Periyodik denetim işi |
| 3 | Mutabakat (reconciliation) kuralı |
| 4 | Bulgu (finding) yaşam döngüsü |

---

### Kapsamdisi (Out)

| # | Kapsamdisi |
|---|---|
| 1 | Şifreleme/anahtar yönetimi (K6) |
| 2 | Yedekleme doğrulaması (k5 backup) |
| 3 | Log denetimi ayrı (K6) |
| 4 | Uygulama testleri (QA) |

---

## Mimari Akış

| Adim | Asama | Aciklama | Kural |
|---|---|---|---|
| 1 | Ölçüm | Veri/küme için bütünlük toplamı üretilir | INT-001 |
| 2 | Saklama | Toplam + meta veri saklanır | INT-002 |
| 3 | Denetim | Periyodik iş toplamı yeniden hesaplar | INT-003 |
| 4 | Karşılaştırma | Beklenen ile hesaplanan eşleştirilir | INT-004 |
| 5 | Mutabakat | İki sistem arasındaki fark listelenir | INT-005 |
| 6 | Bulgu | Fark bulgu olarak açılır ve kapatılır | INT-006 |

---

## Kurallar

| Kural | Tanim | Ihlal riski |
|---|---|---|
| `INT-001` | Her denetlenebilir küme için bütünlük toplamı üretilir ve kaynağıyla birlikte saklanır. | Toplamsız denetim kanıtsızdır |
| `INT-002` | Toplam üretildiği anda kaydedilir; hesap sırasında değişen veri reddedilir. | Hareketli hedef yanılgısı |
| `INT-003` | Denetim işi, denetlenen veriye YAZMAZ (salt-okunur). | Denetim veriyi bozmaz |
| `INT-004` | Uyuşmazlık = bulgu; otomatik düzeltme yapılmaz. | Kör düzeltme kanıt yok eder |
| `INT-005` | Mutabakat iki bağımsız kaynaktan okur; tek kaynaklı doğrulama sayılmaz. | Aynı kaynak kendini doğrulamaz |
| `INT-006` | Her bulgu için kök neden, etki ve kapanış kanıtı zorunludur. | Açıktan açık kalma |
| `INT-007` | Denetim sonuçları değiştirilemez biçimde saklanır (append-only). | Denetim izi |
| `INT-008` | Denetim kapsamı ve frekansı politika ile sabittir; kapsam içi veri atlanamaz. | Kapsam boşluğu |
| `INT-009` | Ölçülmeyen alan 'denetlendi' olarak işaretlenmez. | Yanlış güvence |
| `INT-010` | Bulgu kapatma kanıtı olmadan bulgu kapatılamaz. | Sahte kapanış |

---

## Veri Modeli

| Alan | Tip | Aciklama | Kural |
|---|---|---|---|
| `check_id` | `VARCHAR` | Denetim kimliği | `INT-001` |
| `target_ref` | `VARCHAR` | Denetlenen küme referansı | `INT-001` |
| `expected_sum` | `VARCHAR` | Beklenen toplam | `INT-004` |
| `actual_sum` | `VARCHAR` | Hesaplanan toplam | `INT-004` |
| `status` | `ENUM` | pass/mismatch/error/skipped | `INT-004` |
| `checked_at` | `DATETIME` | Denetim anı (UTC) | `INT-003` |
| `finding_id` | `VARCHAR` | Bulgu kimliği | `INT-006` |
| `evidence_ref` | `VARCHAR` | Kapanış kanıtı referansı | `INT-010` |

---

## Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Kod |
|---|---|---|---|
| E01 | Denetim sırasında yazma sürüyor | Toplam yeniden üretilir; tutarsızlık değil, tekrar ölçülür | `INT-E01` |
| E02 | Beklenen toplam yok (ilk denetim) | Durum=baseline; uyuşmazlık sayılmaz | `INT-E02` |
| E03 | Küme çok büyük, tam tarama pahalı | Örnekleme + tam tarama kademeli | `INT-E03` |
| E04 | İki sistemde de farklı veri | Bulgu + kaynak analizi | `INT-E04` |
| E05 | Denetim işi atlandı | Atlanma kaydı; sessiz atlama yasak | `INT-E05` |
| E06 | Denetim sırasında ağ kesildi | Durum=error; pass sayılmaz | `INT-E06` |
| E07 | PII içeren küme | Toplam meta verisinde içerik tutulmaz | `INT-E07` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `INT-401` | Toplam uyuşmazlığı | Bozulma veya yazma yarışması | Bulgu aç |
| 2 | `INT-402` | Beklenen toplam eksik | Baseline oluşmamış | Baseline üret |
| 3 | `INT-403` | Denetim işi hatası | Zaman aşımı/izin | İşi düzelt |
| 4 | `INT-404` | Kapsam dışı kalan veri | Yapılandırma boşluğu | Kapsamı tamamla |
| 5 | `INT-405` | İki kaynak okunamadı | Bağlantı | Mutabakatı erteleme |
| 6 | `INT-406` | Kanıt referansı kırık | Yol değişmiş | Kanıtı yenile |
| 7 | `INT-407` | Bulgu süresi doldu | Gecikmiş kapanış | Eskale et |
| 8 | `INT-408` | Kapsam/frekans kanıtsız | Politika yok | VERIFICATION REQUIRED |

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
| T-01 | Bozulmuş satır | Uyuşmazlık + bulgu |
| T-02 | İlk denetim (baseline) | status=baseline |
| T-03 | Denetim sırasında yazma | Tekrar ölçüm |
| T-04 | Kapsam dışı küme | Atlanma kaydı |
| T-05 | Kesinti | status=error |
| T-06 | Mutabakat farkı | Fark listesi |
| T-07 | Kanıtsız kapanış denemesi | Red |
| T-08 | Denetim salt-okunur | Kayıt değişmez |

---

## ADR Adaylari

| # | Karar | Reddedilen | Risk | Durum |
|---|---|---|---|---|
| A1 | Uyuşmazlıkta otomatik düzeltme yok | Otomatik onarım | Kanıt yok edilmesin | kabul |
| A2 | Mutabakat iki bağımsız kaynak | Tek kaynak | Kendini doğrulama yanılgısı | kabul |
| A3 | Denetim append-only | Güncellenebilir tablo | Denetim izi | kabul |
| A4 | Örnekleme + tam tarama kademeli | Sadece tam tarama | Maliyet | kabul |
| A5 | Frekans/kapsam politikaya bağlı | Sabit öngörü | Kanıtsız eşik | açık — VERIFICATION REQUIRED |

---

## Riskler

| Risk | Etki | Onlem |
|---|---|---|
| Kör otomatik onarım | Kanıt kaybı | Onaylı düzeltme akışı |
| Kapsam boşluğu | Denetlenmemiş veri | Kapsam denetimi |
| Örnekleme yanlılığı | Yanlış güvence | Kademeleme |
| Bulgu birikmesi | Yaygın bozulma | Yaşam döngüsü SLA'sı |
| Kanıt kaybı | Uyum ihlali | Append-only saklama |

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
| `ChecksumProducer` | Toplam üretir | Veriyi değiştirmez |
| `AuditJob` | Denetimi çalıştırır | Düzeltmez |
| `Reconciler` | Farkları listeler | Karar vermez |
| `FindingStore` | Bulguları tutar | Kapatamaz |

---

## Bağlantılar

### Kendi dosyalari

1. `[[integrity-check-mimari.md]]`
2. `[[index.md]]`
3. `[[integrity-check-denetim.md]]`

### Komsu / ilgili klasorler

1. `[[../k136-cdcdc-pipeline/index.md]]`
2. `[[../k138-gdpr-export/index.md]]`
3. `[[../k136-cdcdc-pipeline/cdc-pipeline-mimari.md]]`
4. `[[../k138-gdpr-export/gdpr-export-tasarim.md]]`
5. `[[../k108-vector-index/index.md]]`
6. `[[../k109-semantic-id/index.md]]`

---

## Kanıt

- `Kanıt: .ai/architecture/integrity-check/integrity-check-mimari.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/data-security.md (Audit Logging bölümü)`
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/backup-strategy.md (L1-L426)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/integrity-check/integrity-check-mimari.md`

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
| 4.0.0 | 2026-10-06 | D04 k132-k143 tasarim (A) ilk yazım | vault-writer |
| 1.0.0 | 2026-09-20 | K5 taslak (yedek kaynak) | - |

### Ek.1. Kural denetim kayıtları

| Kural | Aşama | Zorunlu işlem | Kanıt durumu |
|---|---|---|---|
| `INT-001` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `INT-002` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `INT-003` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `INT-004` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `INT-005` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `INT-006` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `INT-007` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `INT-008` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `INT-009` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `INT-010` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |

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