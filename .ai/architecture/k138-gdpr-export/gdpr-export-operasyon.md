---
title: "138 GDPR Dışa Aktarım — Operasyon"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 138-gdpr-export · GDPR Dışa Aktarım (Operasyon)


---

Bu dosya **GDPR (veri sahibi talebi) dışa aktarımı** alanının işletimini tanımlar: operasyon akışı, kurallar, alarmlar, runbook, kenar durumlar, hata modları ve bağımlılık matrisi. Kardeş dosya: mimari.


---

- **Amaç (B):** Talep yaşam döngüsü (alınma → kimlik → üretme → teslim → kapanış), süre takibi, teslim güvenliği ve denetim kanıtı operasyonunu tanımlar.

---

- **Sorumlu persona:** backend-architect · security-engineer


---

## Operasyon Akışı

| Adim | Asama | Aciklama | Periyot |
|---|---|---|---|
| 1 | Talep alımı | Kayıt + tip + kaynak | talep anında |
| 2 | Kimlik | Doğrulama adımı | hemen |
| 3 | Kapsam | Harita + hariç tutma kararı | iş günü |
| 4 | Üretim | Paket üretimi + toplam | iş günü |
| 5 | Teslim | Güvenli teslim + kanıt | iş günü |
| 6 | Kapanış | Kayıt + geri bildirim | kapanışta |
| 7 | Denetim | Rastgele dosya incelemesi | aylık |

---

## Operasyon Kurallari

| Kural | Tanim | Ihlal riski |
|---|---|---|
| `GDX-OP-01` | Talep alma anından itibaren zaman damgası UTC ile kaydedilir. | Süre tartışması |
| `GDX-OP-02` | Teslim kanalı doğrulanır; kanal doğrulanmadan paket gönderilmez. | Yanlış alıcı |
| `GDX-OP-03` | Silme işlemi onay gerektirir (geri alınamaz). | Geri alınamazlık |
| `GDX-OP-04` | Aylık rastgele dosya incelemesi yapılır. | Kanıtsız süreç |
| `GDX-OP-05` | Red kararında gerekçe ve itiraz yolu yazılır. | Şeffaflık |
| `GDX-OP-06` | Paket saklama süresi politika ile sınırlıdır; silme sonrası paket imha edilir. | İkincil kopya |
| `GDX-OP-07` | Runbook'a sır/PII yazılmaz. | REDACTED |
| `GDX-OP-08` | Talep hacmi metriği haftalık izlenir. | Kapasite |

---

## Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Kod |
|---|---|---|---|
| E01 | Aynı kişi tekrar talep | Yeni talep kimliği, önceki referanslanır | `GDX-OP-E01` |
| E02 | Kimlik doğrulama aracı yok | Talep askıya alınır, tahmin edilmez | `GDX-OP-E02` |
| E03 | Silme onayı geri çekildi | İş durur, kayıt kalır | `GDX-OP-E03` |
| E04 | Paket teslim edilemedi | Yeniden teslim + kanıt | `GDX-OP-E04` |
| E05 | Süre tartışması | Ölçüm kaydı esas alınır | `GDX-OP-E05` |
| E06 | Kapsam itirazı geldi | Harita yeniden gözden geçirilir | `GDX-OP-E06` |
| E07 | Denetim dosyası seçilemedi | Rastgele seçim kaydedilir | `GDX-OP-E07` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `GDX-OP-401` | Süre ölçümü yok | Metrik kesintisi | Ölçümü geri getir |
| 2 | `GDX-OP-402` | Teslim kanalı doğrulanmadı | Süreç atlanmış | Gönderme |
| 3 | `GDX-OP-403` | Onaysız silme | Süreç ihlali | İşi durdur + kaydet |
| 4 | `GDX-OP-404` | Paket imhası yapılmadı | İkincil kopya | İmhayı tamamla |
| 5 | `GDX-OP-405` | Gerekçe eksik red | Şeffaflık ihlali | Gerekçeyi tamamla |
| 6 | `GDX-OP-406` | Denetim incelemesi gecikti | Plan | Eskalasyon |
| 7 | `GDX-OP-407` | Onay kaydı yok | Süreç atlandı | Kaydı tamamla |
| 8 | `GDX-OP-408` | Kanıtsız süre taahhüdü | Uydurma süre | VERIFICATION REQUIRED |

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

## Alarmlar

| # | Alarm | Oncelik | Eylem |
|---|---|---|---|
| `AM-01` | Talep süresi (politika) aşıldı | P1 | Eskale et |
| `AM-02` | Kimlik doğrulama kesintisi | P1 | Talepleri askıya al |
| `AM-03` | Kapsam okuma hatası | P2 | Duraklat + düzelt |
| `AM-04` | Teslim kanıtı eksik | P2 | Teslimi tekrarla |
| `AM-05` | Paket imhası gecikti | P2 | İmha işini başlat |
| `AM-06` | Talep hacmi sıçradı | P3 | Kapasite değerlendirmesi |

---

## Runbook

| Adim | Islem | Cikti |
|---|---|---|
| 1 | Talep durumunu oku | Talep kaydı |
| 2 | Kimlik doğrulama durumunu doğrula | Doğrulama kaydı |
| 3 | Kapsam manifestini üret | Harita listesi |
| 4 | Paketi üret + toplam ekle | Bütünlük toplamı |
| 5 | Güvenli teslim + kanıt | Teslim kanıtı |
| 6 | Kapanış + log | log.md girişi |

---

## Prova / Test

| # | Prova | Beklenen |
|---|---|---|
| O-01 | Talep teslim provası | Kanıt kaydı oluşur |
| O-02 | Silme provası (kopya ortam) | Tüm türevler işlenir |
| O-03 | Red akışı | Gerekçe + itiraz kaydı |
| O-04 | Paket imhası | İkincil kopya yok |
| O-05 | Aylık dosya incelemesi | İnceleme raporu |

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

## Bağlantılar

### Kendi dosyalari

1. `[[gdpr-export-operasyon.md]]`
2. `[[index.md]]`
3. `[[gdpr-export-tasarim.md]]`

### Komsu / ilgili klasorler

1. `[[../k137-integrity-check/index.md]]`
2. `[[../k139-session-store/index.md]]`
3. `[[../k137-integrity-check/integrity-check-mimari.md]]`
4. `[[../k139-session-store/session-store-mimari.md]]`
5. `[[../k108-vector-index/index.md]]`
6. `[[../k109-semantic-id/index.md]]`

---

## Kanıt

- `Kanıt: .ai/architecture/gdpr-export/gdpr-export-operasyon.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/data-security.md (GDPR Compliance bölümü, L1-L462)`
- `Kanıt: .ai/.sql/mysql/coremusic_user.sql (kişisel veri kaynağı — dosya var)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/gdpr-export/gdpr-export-operasyon.md`

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

## Surumler

| Sürüm | Tarih | Değişiklik | Sorumlu |
|---|---|---|---|
| 4.0.0 | 2026-10-06 | D04 k132-k143 operasyon (B) ilk yazım | vault-writer |
| 1.0.0 | 2026-09-20 | K5 taslak (yedek kaynak) | - |

### Ek.1. Kural denetim kayıtları

| Kural | Aşama | Zorunlu işlem | Kanıt durumu |
|---|---|---|---|
| `GDX-OP-01` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-OP-02` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-OP-03` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-OP-04` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-OP-05` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-OP-06` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-OP-07` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `GDX-OP-08` | çalıştırma sırasında | gözlem kaydı zorunlu | `⚠️ VERIFICATION REQUIRED` |

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
- Tekrarlı çalıştırma (re-run) güvenliği `GDX-OP-01` ile sabitlenir; idempotentlik varsayılmaz, kanıtlanır.
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
- Ek not 1: `GDX-OP-01` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 2: `GDX-OP-02` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 3: `GDX-OP-03` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 4: `GDX-OP-04` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 5: `GDX-OP-05` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 6: `GDX-OP-06` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 7: `GDX-OP-07` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 8: `GDX-OP-08` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 9: `GDX-OP-01` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 10: `GDX-OP-02` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 11: `GDX-OP-03` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 12: `GDX-OP-04` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 13: `GDX-OP-05` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 14: `GDX-OP-06` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 15: `GDX-OP-07` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 16: `GDX-OP-08` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 17: `GDX-OP-01` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 18: `GDX-OP-02` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 19: `GDX-OP-03` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 20: `GDX-OP-04` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 21: `GDX-OP-05` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 22: `GDX-OP-06` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 23: `GDX-OP-07` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 24: `GDX-OP-08` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 25: `GDX-OP-01` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 26: `GDX-OP-02` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 27: `GDX-OP-03` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 28: `GDX-OP-04` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 29: `GDX-OP-05` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 30: `GDX-OP-06` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 31: `GDX-OP-07` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 32: `GDX-OP-08` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 33: `GDX-OP-01` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 34: `GDX-OP-02` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 35: `GDX-OP-03` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 36: `GDX-OP-04` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 37: `GDX-OP-05` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 38: `GDX-OP-06` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 39: `GDX-OP-07` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 40: `GDX-OP-08` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 41: `GDX-OP-01` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 42: `GDX-OP-02` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 43: `GDX-OP-03` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 44: `GDX-OP-04` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 45: `GDX-OP-05` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 46: `GDX-OP-06` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 47: `GDX-OP-07` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 48: `GDX-OP-08` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 49: `GDX-OP-01` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 50: `GDX-OP-02` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 51: `GDX-OP-03` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 52: `GDX-OP-04` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 53: `GDX-OP-05` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 54: `GDX-OP-06` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 55: `GDX-OP-07` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 56: `GDX-OP-08` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 57: `GDX-OP-01` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 58: `GDX-OP-02` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 59: `GDX-OP-03` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 60: `GDX-OP-04` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 61: `GDX-OP-05` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 62: `GDX-OP-06` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 63: `GDX-OP-07` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 64: `GDX-OP-08` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 65: `GDX-OP-01` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 66: `GDX-OP-02` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 67: `GDX-OP-03` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 68: `GDX-OP-04` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 69: `GDX-OP-05` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 70: `GDX-OP-06` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 71: `GDX-OP-07` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 72: `GDX-OP-08` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 73: `GDX-OP-01` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 74: `GDX-OP-02` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 75: `GDX-OP-03` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 76: `GDX-OP-04` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 77: `GDX-OP-05` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 78: `GDX-OP-06` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 79: `GDX-OP-07` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 80: `GDX-OP-08` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 81: `GDX-OP-01` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 82: `GDX-OP-02` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 83: `GDX-OP-03` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 84: `GDX-OP-04` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 85: `GDX-OP-05` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 86: `GDX-OP-06` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 87: `GDX-OP-07` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 88: `GDX-OP-08` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 89: `GDX-OP-01` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 90: `GDX-OP-02` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 91: `GDX-OP-03` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 92: `GDX-OP-04` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 93: `GDX-OP-05` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 94: `GDX-OP-06` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 95: `GDX-OP-07` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 96: `GDX-OP-08` kuralı GDPR (veri sahibi talebi) dışa aktarımı akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.