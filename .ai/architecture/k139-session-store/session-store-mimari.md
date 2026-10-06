---
title: "139 Oturum Deposu — Mimari ve Tasarim"
type: architecture
category: veri-yonetimi
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D04 dilimi (k132-k143)"
updated: 2026-10-06
---


---

# 139-session-store · Oturum Deposu (Mimari)


---

Bu dosya **oturum deposu** alanının tasarımını tanımlar: kapsam, akış, kurallar, veri modeli, kenar durumlar, hata modları ve bağımlılık matrisi. Kardeş dosya: operasyon.


---

- **Amaç (A):** Oturum kayıtlarının saklanması, süre (TTL) yönetimi, yenileme, cihaz takibi ve iptal davranışını tanımlar: oturum anahtarı imzası, oturum veri şeması, paralel oturum kuralları ve sona erme durumları.

---

- **Sorumlu persona:** backend-architect · security-engineer

---

- **Eşleşen klasor:** oturum deposu → profil deposu


---

## Kapsam

### Kapsamda (In)

| # | Kapsam |
|---|---|
| 1 | Oturum veri şeması ve imzası |
| 2 | TTL ve yenileme kuralı |
| 3 | İptal (logout / revoke) davranışı |
| 4 | Paralel oturum politikası |

---

### Kapsamdisi (Out)

| # | Kapsamdisi |
|---|---|
| 1 | Kimlik doğrulama protokolü (K6) |
| 2 | Parola hashing (K6) |
| 3 | CSRF ayrı dosya (K6) |
| 4 | Uygulama ekranları (K11) |

---

## Mimari Akış

| Adim | Asama | Aciklama | Kural |
|---|---|---|---|
| 1 | Oluşturma | Kimlik doğrulaması sonrası oturum açılır | SES-001 |
| 2 | İmza | Oturum anahtarı imzalanır/dağıtılır | SES-002 |
| 3 | Doğrulama | Her istekte imza + süre kontrolü | SES-003 |
| 4 | Yenileme | Etkin oturumda TTL uzatılır | SES-004 |
| 5 | İptal | Çıkış/kuşku halinde kayıt iptal edilir | SES-005 |
| 6 | Temizlik | Süresi dolan kayıtlar atılır | SES-006 |

---

## Kurallar

| Kural | Tanim | Ihlal riski |
|---|---|---|
| `SES-001` | Oturum kaydı yalnız kimlik doğrulamasından sonra açılır. | Sahte oturum |
| `SES-002` | Oturum kimliği tahmin edilemez ve imzalıdır; imzasız oturum reddedilir. | Oturum taklidi |
| `SES-003` | Her istekte süre (TTL) ve imza birlikte doğrulanır. | Süresi dolmuş erişim |
| `SES-004` | Yenileme (sliding) yalnız etkin oturumda yapılır; yenileme üst sınırı tanımlanır. | Sonsuz oturum |
| `SES-005` | Çıkış, parola değişikliği ve kuşku halinde tüm oturumlar iptal edilir. | Çalıntı oturum |
| `SES-006` | Süresi dolan oturum verisi saklama süresi dolmadan okunamaz. | Gölge veri |
| `SES-007` | Paralel oturum üst sınırı politika ile tanımlıdır (sayı UNKNOWN). | Kaynak/istismar |
| `SES-008` | Oturum deposunda parola/anahtar tutulmaz (REDACTED). | Sızıntı etkisi |
| `SES-009` | İptal işlemi yayılımı (tüm cihazlar) zorunlu bir seçenektir. | Kurtarılamayan cihaz |
| `SES-010` | Oturum kayıtları salt yetkili bileşenlerce okunur. | Yetkisiz okuma |

---

## Veri Modeli

| Alan | Tip | Aciklama | Kural |
|---|---|---|---|
| `session_id` | `VARCHAR` | İmzalı oturum kimliği | `SES-002` |
| `subject_ref` | `VARCHAR` | Kullanıcı referansı | `SES-001` |
| `issued_at` | `DATETIME` | Açılış (UTC) | `SES-001` |
| `expires_at` | `DATETIME` | Bitiş (UTC) | `SES-003` |
| `last_seen_at` | `DATETIME` | Son etkinlik | `SES-004` |
| `device_ref` | `VARCHAR` | Cihaz referansı | `SES-009` |
| `state` | `ENUM` | active/revoked/expired | `SES-005` |
| `revoked_at` | `DATETIME` | İptal anı | `SES-005` |

---

## Kenar Durumlar

| # | Kenar durum | Beklenen davranış | Kod |
|---|---|---|---|
| E01 | Süre doldu, istek geldi | Red + yeniden girişe yönlendirme | `SES-E01` |
| E02 | İmza uyuşmuyor | Red + olay kaydı | `SES-E02` |
| E03 | Aynı anda 10 cihaz | Paralel üst sınır uygulanır (sayı UNKNOWN) | `SES-E03` |
| E04 | Parola değişti | Tüm oturumlar iptal | `SES-E04` |
| E05 | Saat kayması (UTC dışı) | UTC damgaları esas alınır | `SES-E05` |
| E06 | Depo geçici olarak erişilemez | Güvenli red; açık oturum kabul edilmez | `SES-E06` |
| E07 | İptal yayılımı yarım kaldı | İptal + kalan cihaz listesi raporlanır | `SES-E07` |

---

## Hata Modları

| # | Hata kodu | Belirti | Kök neden | Eylem |
|---|---|---|---|---|
| 1 | `SES-401` | İmza geçersiz | Sahte/bozuk çerez | Reddet + olay |
| 2 | `SES-402` | Süre dolmuş | TTL aşılmış | Yeniden giriş |
| 3 | `SES-403` | Depo erişilemez | Altyapı | Güvenli red |
| 4 | `SES-404` | Paralel sınır aşıldı | Politika ihlali | Eski oturumu kapat |
| 5 | `SES-405` | İptal yayılmadı | Dağıtık iptal hatası | Tekrar yayınla |
| 6 | `SES-406` | Oturum kaydı çakışması | Kimlik üretimi | Kimliği yenile |
| 7 | `SES-407` | Yetkisiz okuma denemesi | Erişim ihlali | Reddet + denetim |
| 8 | `SES-408` | TTL/sınır kanıtsız | Politika yok | VERIFICATION REQUIRED |

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

> Katman tanimlari: `K0 cekirdek` ... `K16 K1-kaynak (MCP)`. `hayir` satirlari bilincli sinirdir.


---

## Test Senaryolari

| # | Senaryo | Beklenen |
|---|---|---|
| T-01 | Süresi dolan oturum | Red |
| T-02 | Sahte imza | Red + olay |
| T-03 | Çıkış sonrası istek | Red |
| T-04 | Parola değişimi | Tüm oturumlar iptal |
| T-05 | Sliding yenileme | expires_at uzar |
| T-06 | Paralel üst sınır | En eski kapanır |
| T-07 | Depo kesintisi | Güvenli red |
| T-08 | Yetkisiz okuma | Red |

---

## ADR Adaylari

| # | Karar | Reddedilen | Risk | Durum |
|---|---|---|---|---|
| A1 | İmza zorunlu | İmzasız kimlik | Oturum taklidi | kabul |
| A2 | İptal = derhal okunamaz | TTL'ye bırakma | Çalıntı oturum riski | kabul |
| A3 | Türetilmiş oturum verisinde PII asgari | Tüm profil verisi | Sızıntı etkisi | kabul |
| A4 | Depo erişilemezse red | Açık oturum kabul | Güvenlik > kesinti | kabul |
| A5 | TTL/paralel sayı politikaya bağlı | Tahmini sayı | Kanıtsız eşik | açık — VERIFICATION REQUIRED |

---

## Riskler

| Risk | Etki | Onlem |
|---|---|---|
| Sonsuz oturum | Kalıcı erişim | Yenileme üst sınırı |
| İptal yayılım gecikmesi | Kurtarılamayan cihaz | Yayın + doğrulama |
| Oturum deposu sızıntısı | Toplu ele geçirme | Asgari veri + imza |
| Saat kayması | Erken/geç kapanış | UTC |
| Politika kanıtsızlığı | Uyum ihlali | Ölçüm + onay |

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
| `SessionWriter` | Oturumu yazar | Kimliği doğrulamaz |
| `SignatureVerifier` | İmzayı doğrular | Oturumu silmez |
| `TtlJanitor` | Süresi dolanı atar | Aktif oturuma dokunmaz |
| `RevocationBus` | İptali yayınlar | Karar vermez |

---

## Bağlantılar

### Kendi dosyalari

1. `[[session-store-mimari.md]]`
2. `[[index.md]]`
3. `[[session-store-operasyon.md]]`

### Komsu / ilgili klasorler

1. `[[../k138-gdpr-export/index.md]]`
2. `[[../k140-profile-store/index.md]]`
3. `[[../k138-gdpr-export/gdpr-export-tasarim.md]]`
4. `[[../k140-profile-store/profile-store-mimari.md]]`
5. `[[../k108-vector-index/index.md]]`
6. `[[../k109-semantic-id/index.md]]`

---

## Kanıt

- `Kanıt: .ai/architecture/session-store/session-store-mimari.md` (bu dosya)
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/cache-strategy.md (L1-L450)`
- `Kanıt: _backup/arch-2026-10-06_1057/architecture/k5-veri-yonetimi/data-security.md (L1-L462)`
- `Kanıt: ⚠️ VERIFICATION REQUIRED — üretimde çalışan kurulum, ölçüm raporu ve ürün seçimi repo'da doğrulanamadı`
- Doğrulama: `node .ai/scripts/vault-utf8-writer.mjs verify --file .ai/architecture/session-store/session-store-mimari.md`

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
| oturum deposu kayit politikasi | **R** | C | A | C | C | I |
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
| `Oturum` | `session` | Kimliklenmiş etkinlik aralığı |
| `TTL` | `time to live` | Yaşam süresi |
| `Kayan yenileme` | `sliding renewal` | Etkinlikle uzayan süre |
| `İmza` | `signature` | Sahtesi zor kimlik |
| `İptal` | `revocation` | Oturumu geçersiz kılma |
| `Paralel oturum` | `concurrent session` | Aynı anda açık oturumlar |
| `Cihaz referansı` | `device ref` | Cihaz tanımı |
| `Gölge veri` | `shadow data` | Süresi dolmuş hâlâ duran kayıt |
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
| `SES-001` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SES-002` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SES-003` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SES-004` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SES-005` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SES-006` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SES-007` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SES-008` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SES-009` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |
| `SES-010` | tasarım aşamasında | şema/kural denetimi zorunlu | `⚠️ VERIFICATION REQUIRED` |

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

- oturum deposu için kaynak kaydı (system of record) K5 şemasıdır; bu dosyadaki her tablo türetilmiştir.
- Tekrarlı çalıştırma (re-run) güvenliği `SES-001` ile sabitlenir; idempotentlik varsayılmaz, kanıtlanır.
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
- Ek not 1: `SES-001` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 2: `SES-002` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 3: `SES-003` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 4: `SES-004` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 5: `SES-005` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 6: `SES-006` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 7: `SES-007` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 8: `SES-008` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 9: `SES-009` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 10: `SES-010` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 11: `SES-001` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 12: `SES-002` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 13: `SES-003` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 14: `SES-004` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 15: `SES-005` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 16: `SES-006` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 17: `SES-007` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 18: `SES-008` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 19: `SES-009` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 20: `SES-010` kuralı oturum deposu akışında kayıt aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 21: `SES-001` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 22: `SES-002` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 23: `SES-003` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 24: `SES-004` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 25: `SES-005` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 26: `SES-006` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 27: `SES-007` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 28: `SES-008` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 29: `SES-009` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 30: `SES-010` kuralı oturum deposu akışında gözlem aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 31: `SES-001` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 32: `SES-002` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 33: `SES-003` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 34: `SES-004` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 35: `SES-005` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 36: `SES-006` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 37: `SES-007` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 38: `SES-008` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 39: `SES-009` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 40: `SES-010` kuralı oturum deposu akışında geri alma aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 41: `SES-001` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 42: `SES-002` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 43: `SES-003` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 44: `SES-004` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 45: `SES-005` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 46: `SES-006` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 47: `SES-007` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 48: `SES-008` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.
- Ek not 49: `SES-009` kuralı oturum deposu akışında şema aşamasında denetlenir — kanıt: `⚠️ VERIFICATION REQUIRED`.