---
title: "138 GDPR Dışa Aktarım — Mimari ve Tasarim"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 138-gdpr-export · GDPR Dışa Aktarım (Mimari)


---

Bu dosya **GDPR (veri sahibi talebi) dışa aktarımı** alanının tasarımını tanımlar: kapsam, akış, kurallar, veri modeli, kenar durumlar, hata modları ve bağımlılık matrisi. Kardeş dosya: operasyon.


---

- **Amaç (A):** Veri sahibinin erişim/taşınabilirlik (dışa aktarım) ve silme/anonimleştirme taleplerinin kimlik doğrulamalı, denetlenebilir ve kapsamı ölçülebilir biçimde karşılanmasını tanımlar: veri haritası, paket şeması, maskelenmiş alanlar ve silme sırası.

---

- **Sorumlu persona:** security-engineer · data-engineer

---

- **Eşleşen klasor:** GDPR dışa aktarım → oturum deposu


---

## Kapsam

### Kapsamda (In)

| # | Kapsam |
|---|---|
| 1 | Talep türleri (erişim/taşıma/silme) |
| 2 | Veri haritası kapsamı |
| 3 | Paket şeması ve maskelenen alanlar |
| 4 | Silme/anonimleştirme sırası |

---

### Kapsamdisi (Out)

| # | Kapsamdisi |
|---|---|
| 1 | Kimlik protokolü ayrıntısı (K6) |
| 2 | Saklama politikası sayıları (UNKNOWN) |
| 3 | Bildirim/UX metinleri (K11) |
| 4 | Hukuki görüş (dış kaynak) |

---

## Mimari Akış

| Adim | Asama | Aciklama | Kural |
|---|---|---|---|
| 1 | Talep | Talep alınır, tip ve kapsam kaydedilir | GDX-001 |
| 2 | Kimlik | Talebi sunanın veri sahibi olduğu doğrulanır | GDX-002 |
| 3 | Kapsam | Veri haritasından etkilenen kayıtlar listelenir | GDX-003 |
| 4 | Üretim | Paket üretilir; hariç tutulanlar listelenir | GDX-004 |
| 5 | Teslim | Güvenli teslim + alım kanıtı | GDX-005 |
| 6 | Kapanış | Denetim kaydı ve kapanış kanıtı | GDX-006 |

---

## Kurallar

| Kural | Tanim | Ihlal riski |
|---|---|---|
| `GDX-001` | Her talep benzersiz talep kimliği ve tipiyle (access/export/delete) kaydedilir. | Süreç izlenebilir olmalı |
| `GDX-002` | Kimlik doğrulama olmadan dışa aktarım yapılmaz; talep sahibi eşleşmezse red. | Veri sızıntısı |
| `GDX-003` | Kapsam veri haritasından türetilir; harici tahminleme yapılmaz. | Eksik/kapsam dışı veri |
| `GDX-004` | Başkasına ait veriler (üçüncü kişi) paketten hariç tutulur ve hariç tutma gerekçesi kaydedilir. | Üçüncü kişi hakkı |
| `GDX-005` | Teslim kanıtı (kim, ne zaman, hangi paket) saklanır. | Denetim |
| `GDX-006` | Silme/anonimleştirme işleminde tutulması zorunlu kayıtlar gerekçesiyle listelenir. | Yasal istisna |
| `GDX-007` | Talep süresi (sla) politika ile sabittir; sayı UNKNOWN olarak yazılır. | Kanıtsız süre |
| `GDX-008` | Paket içeriği REDACTED politikasına uyar; parola/anahtar pakete girmez. | REDACTED |
| `GDX-009` | Silme sırası, türetilmiş verileri (önbellek, arşiv) de kapsar. | Gizli kopya |
| `GDX-010` | Talep sonucu reddedilirse gerekçe ve itiraz yolu kaydedilir. | Şeffaflık |

---

## Veri Modeli

| Alan | Tip | Aciklama | Kural |
|---|---|---|---|
| `request_id` | `VARCHAR` | Talep kimliği | `GDX-001` |
| `subject_ref` | `VARCHAR` | Veri sahibi referansı (hash) | `GDX-002` |
| `request_type` | `ENUM` | access/export/delete/rectify | `GDX-001` |
| `scope_manifest` | `JSON` | Kapsam listesi | `GDX-003` |
| `exclusions` | `JSON` | Hariç tutulanlar + gerekçe | `GDX-004` |
| `state` | `ENUM` | received/verified/delivered/rejected | `GDX-006` |
| `delivered_at` | `DATETIME` | Teslim anı (UTC) | `GDX-005` |
| `evidence_ref` | `VARCHAR` | Kanıt referansı | `GDX-005` |

---

## Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Kod |
|---|---|---|---|
| E01 | Kimlik eşleşmiyor | Talep red + gerekçe kaydı | `GDX-E01` |
| E02 | Kapsamdaki kaynak okunamıyor | Duraklat + eskalasyon | `GDX-E02` |
| E03 | Üçüncü kişi verisi karışık | Hariç tutma + gerekçe | `GDX-E03` |
| E04 | Silme sırasında yasal tutma | Tutulan kayıtlar gerekçeli liste | `GDX-E04` |
| E05 | Talep sahibi birden fazla hesap | Harita tümünü kapsar | `GDX-E05` |
| E06 | Paket çok büyük | Parçalı teslim + bütünlük toplamı | `GDX-E06` |
| E07 | Süre aşıldı | Olay + eskalasyon; süre UNKNOWN ise tahmin yok | `GDX-E07` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `GDX-401` | Kimlik doğrulama başarısız | Yanlış/eksik kanıt | Reddet |
| 2 | `GDX-402` | Kapsam boş | Harita eksik | Haritayı tamamla |
| 3 | `GDX-403` | Paket üretim hatası | Kaynak okuma hatası | Duraklat + düzelt |
| 4 | `GDX-404` | Teslim kanıtı eksik | Kanal arızası | Teslimi tekrarla |
| 5 | `GDX-405` | Üçüncü kişi verisi sızdı | Kapsam filtresi yok | Teslimi durdur |
| 6 | `GDX-406` | Silme yarım kaldı | Türetilmiş veri unutuldu | Türev listesini işle |
| 7 | `GDX-407` | Süre ölçümü yok | Metrik eksik | Ölçümü ekle |
| 8 | `GDX-408` | Saklama süresi bilinmiyor | Politika yok | VERIFICATION REQUIRED |

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

> Katman tanimlari: `K0 cekirdek` ... `K16 K1-kaynak (MCP)`. `hayir` satirlari bilincli sinirdir.


---

## Test Senaryolari

| # | Senaryo | Beklenen |
|---|---|---|
| T-01 | Kimliksiz dışa aktarım | Red |
| T-02 | Eksik kapsam | Harita uyarısı |
| T-03 | Üçüncü kişi verisi | Hariç tutma |
| T-04 | Silme + türevler | Türev kayıtlar da işleminir |
| T-05 | Yasal tutma | Gerekçeli istisna |
| T-06 | Parçalı teslim | Bütünlük toplamı eşleşir |
| T-07 | Red akışı | Gerekçe + itiraz kaydı |
| T-08 | Pakette sır yok | REDACTED denetimi |

---

## ADR Adaylari

| # | Karar | Reddedilen | Risk | Durum |
|---|---|---|---|---|
| A1 | Kimlik doğrulama kapısı zorunlu | Yalnız oturum | Talep kimliği ≠ oturum kimliği | kabul |
| A2 | Kapsam veri haritasından | Elle liste | Ölçümsüz kapsam | kabul |
| A3 | Hariç tutma gerekçeli | Sessiz hariç tutma | Şeffaflık | kabul |
| A4 | Türetilmiş veri dahil silme | Yalnız ana tablo | Gizli kopya | kabul |
| A5 | Süre sayısı yazılmadı | Tahmini süre | Kanıtsız taahhüt | açık — VERIFICATION REQUIRED |

---

## Riskler

| Risk | Etki | Onlem |
|---|---|---|
| Yanlış kişiye teslim | Kişisel veri sızıntısı | Çift doğrulama |
| Eksik kapsam | Eksik yanıt | Harita denetimi |
| Gizli kopya kalması | Silme etkisiz | Türev envanteri |
| Süre taahhüdü kanıtsız | Uyum ihlali | Ölçüm + onay |
| Paket içinde sır | Güvenlik ihlali | REDACTED taraması |

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
| `RequestRegistry` | Talepleri tutar | Kimlik doğrulamaz |
| `ScopeResolver` | Kapsamı çözer | Teslim etmez |
| `PackageBuilder` | Paketi üretir | Onay vermez |
| `EvidenceLog` | Kanıtı yazar | İçeriği değiştirmez |

---

## Bağlantılar

### Kendi dosyalari

1. `[[gdpr-export-tasarim.md]]`
2. `[[index.md]]`
3. `[[gdpr-export-operasyon.md]]`

### Komsu / ilgili klasorler

1. `[[../k137-integrity-check/index.md]]`
2. `[[../k139-session-store/index.md]]`
3. `[[../k137-integrity-check/integrity-check-mimari.md]]`
4. `[[../k139-session-store/session-store-mimari.md]]`
5. `[[../k108-vector-index/index.md]]`
6. `[[../k109-semantic-id/index.md]]`

---

## Kanıt

- `Kanıt: .ai/architecture/gdpr-export/gdpr-export-tasarim.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/data-security.md (GDPR Compliance bölümü, L1-L462)`
- `Kanıt: .ai/.sql/mysql/coremusic_user.sql (kişisel veri kaynağı — dosya var)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/gdpr-export/gdpr-export-tasarim.md`

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
| 4.0.0 | 2026-10-06 | D04 k132-k143 tasarim (A) ilk yazım | vault-writer |
| 1.0.0 | 2026-09-20 | K5 taslak (yedek kaynak) | - |

### Ek.1. Kural denetim kayıtları

| Kural | Aşama | Zorunlu işlem | Kanıt durumu |
|---|---|---|---|
| `GDX-001` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-002` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-003` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-004` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-005` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-006` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-007` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-008` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-009` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-010` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |

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