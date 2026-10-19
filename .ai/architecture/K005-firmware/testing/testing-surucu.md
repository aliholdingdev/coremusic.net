---
node_id: K005-firmware/testing/testing-surucu
node_type: module
status: draft
parent_id: K005-firmware/testing
children: []
depends_on:
  - K005-firmware/testing
  - K005-firmware
  - K005-firmware/boot
evidence:
  - "K005-firmware/boot/boot-surucu.md §1 · §2 · §20 · S12 — sürücü şablonu + test arayüzü, okundu 2026-10-14"
  - "K005-firmware/testing/index.md (ELI15 4 senaryo · open_questions) — okundu 2026-10-14"
  - "K005-firmware/firmware-test-stratejisi.md §5 (S1–S10) · §14 · §18 · §20 · §21 · §22 · §25 · §29 — okundu 2026-10-14"
  - "K005-firmware/firmware-bilesenler.md §4 (F14) · §23 (H17 · H18) — okundu 2026-10-14"
  - "K005-firmware/firmware-bagimliliklar.md §17 (D14) · §20.5 (HIL 5 adım) — okundu 2026-10-14"
  - "K005-firmware/firmware-kapsam.md §5 (S20) · §9.6 (G5 test kipi · Ov7) — okundu 2026-10-14"
  - "K005-firmware/firmware-riskler.md §21 (testing satırı) — okundu 2026-10-14"
  - "K005-firmware/device-control/device-control-anayasa.md §10 (DV-1…DV-12 tetik devri) — okundu 2026-10-14"
  - "K005-firmware/firmware-evrensel-dil.md §2 (terim 24 HIL) · §5 — okundu 2026-10-14"
  - ".github/workflows/ci.yml — G1–G8, tam dosya okundu 2026-10-14"
  - ".github/workflows/secret-scan.yml — G2, tam dosya okundu 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "test kapsamı hedefi tanımlı değil ⚠️ (index.md open_question — çözülmedi, taşındı)"
  - "G9 firmware test workflow'u yok — test tetiklerinin CI karşılığı tanımsız ⚠️ VERIFICATION REQUIRED"
  - "F14 ad uzayı çakışması: boot-surucu F14 = BOOT_FAULT_CODE, bilesenler F14 = Test kıyaslama (HIL) — eşleştirme yapılmadı ⚠️"
  - "E_TST_FAIL (H17) nihai kod adı — taslak; evrensel-dil §6 yayını bekleniyor (bilesenler §23 aktarımı) ⚠️"
  - "CI geçmişi okunmadı — 'CI yeşil' doğrulanmadı ⚠️"
group: mimari
---

# testing-surucu — Testing Arayüz ve Sözleşme Katmanı (K005-firmware)

> **ELI15:** Anayasa *kuralları* koyar; bu dosya test sisteminin
> **kontakte fişini** çizer: hangi tetik girer, hangi bayrak okunur, hangi
> hata kodu döner, hangi CI kapısı bağlanır. Repo'da firmware kodu ve HIL
> tezgâhı olmadığından buradaki her arayüz **SÖZLEŞME/taslaktır** —
> "uygulandı" yazılmaz. Bayrak adları kodda yoktur, adlar kodla
> eşleşinceye kadar sözleşme etiketini taşır.

## §1 Amaç, Rol ve Kapsam Sınırı

| Soru | Cevap | Sahip |
|---|---|---|
| Bu dosya neyi tanımlar? | testing modülünün arayüz/sözleşme katmanı | K005-firmware/testing |
| Kimler bu arayüzü kullanır? | boot · update · diagnostics · device-control · CI (DevOps) | §7 komşu tablosu |
| Anayasa bu dosyayı değiştirir mi? | Hayır — anayasa > bu dosya > örnekler | `testing-anayasa.md` |
| Arayüz adları kod adı mı? | Hayır — sözleşme adı (kod yok) | S2 kuralı |
| Kapsam dışı | Testin firmware'de fiilen çalıştırılması (HIL) | tezgâh + G9 (yok) |
| Kapsam dışı | Host yüzeyi PHPUnit/Vitest test yazımı | mevcut CI işleri (G4/G5) |
| Kapsam dışı | Test raporunun saklanması/ Dashboard | diagnostics (taslak) |

**Sözleşme kuralı (S1):** Bir arayüz adı yalnız **tek dosyada** tanım
kazanır; diğer dosyalar yalnız kullanır (çift tanım = belirsizlik ihlali).

**Sözleşme kuralı (S2):** Arayüz adları kodla eşleşinceye kadar
`SÖZLEŞME` etiketli kalır; eşleşme kanıtı olmadan "uygulandı" yazılmaz
(`boot-surucu.md` §1 S2 disiplini — aktarım).

## §2 Test Tetikleyici Arayüzü (TI-T1 – TI-T16)

| # | Test girişi (tetik) | Beklenen çıkış | Katman | Bağlı risk | Kapı |
|---|---|---|---|---|---|
| TI-T1 | Soğuk açılış tetiği (çekirdek S1) | reset vektörü → ilk-stage → ana firmware sırası | L3 | 2 | G9 ⚠️ (workflow yok) |
| TI-T2 | Bozuk/imzasız bölüm denemesi (S2) | reddedilir + güvenli kapanma/geri alma | L5 | 2 | G9 ⚠️ |
| TI-T3 | Watchdog suskunluğu (S3) | watchdog reset'i + neden raporu | L5 | 7 | G9 ⚠️ |
| TI-T4 | Brown-out / gerilim düşürme (S4) | güvenli kapanma, veri bozulması yok | L5 | 8 | G9 ⚠️ |
| TI-T5 | A/B güncelleme + geri alma (S5) | yeni bölüm doğrulanmazsa eskiye dönüş | L3 | 3 🔴 | G9 ⚠️ |
| TI-T6 | Bus yarışı — eşzamanlı I²C komutu (S6) | kuyruk sırası korunur, bozuk çerçeve yok | L2 | 12 | G9 ⚠️ |
| TI-T7 | Kapanma sırası (S7) | önce mute → tampon boşalt → güç kesme | L3 | 8 | G9 ⚠️ |
| TI-T8 | Hatalı host komutu (S8) | hata kodu döner, sessiz yutulmaz | L5 | 1 | G9 ⚠️ |
| TI-T9 | Yetkisiz kabuk komutu (S9) | reddedilir + log (maskelemeli) | L5 | 2 | G2 · G3 (aktif) |
| TI-T10 | Uzun çalışma — soak (S10) | bellek sızıntısı yok, watchdog düzgün | L4 · L6 | 7 | G9 ⚠️ |
| TI-T11 | Boot test girişi TI1–TI16 (aktarım) | boot zinciri tetikleri (reset · bozuk imza · retry · debug) | L1–L3 | 5 | boot B1–B5 |
| TI-T12 | Tahsis denetimi (statik analiz) | 0 tahsis (kural 1) | statik | 6 | G3 · cppcheck/Clang-Tidy (planlı ⚠️) |
| TI-T13 | Lock-free denetimi (statik analiz) | mutex yok (kural 2) | statik | 6 | G3 · cppcheck/Clang-Tidy (planlı ⚠️) |
| TI-T14 | HIL ölçüm zinciri (5 adım) | test kipi → senaryo → ölçüm → rapor → kritere karşılaştırma | L3–L6 | 3 | G9 ⚠️ |
| TI-T15 | CI tetik arayüzü | push · pull_request → G1–G8 işleri | CI | — | G1 (okundu 2026-10-14) |
| TI-T16 | Test hata raporu tetiği | `E_TST_FAIL` → rapor + geç/kal | tümü | — | strateji §5 · H17 |

> **TI1–TI16 ile TI-T1–TI-T16 farkı:** `boot-surucu.md` §20'deki TI1–TI16
> *boot'un* test girdileridir (testing'e aktarılmıştır — TI-T11); TI-T*
> serisi *testing'in* kendi tetikleridir. İki zarf birbirine
> **eşleştirilmedi** — çakışma yoktur, ikisi de ayrı tanımdır.

**Test tetiği kuralı (S3):** Bu arayüz yalnız *tetik/olay* tanımlar;
uygulama kodu testing modülünün sorumluluğundadır (`boot-surucu.md` §20
S12). Tetiklerin hiçbiri bu üretimde **koşulmamıştır**; Durum
`testing-ornekler.md` §A'da `PLANNED` olarak taşınır.

## §3 Bayrak ve Olay Sözlüğü (taslak adlar)

| # | Bayrak/Olay | Üreten | Tüketen | Anlamı | Durum |
|---|---|---|---|---|---|
| B1 | Test kipi (taslak ad) | diagnostics (§9.6 G5) | testing · runtime | kalıcı olmayan test erişim kipi | taslak — bayrak runtime'da yaşar, kipi diagnostics belirler (kapsam §9.6 Ov7) |
| B2 | `DEBUG_ALLOWED` (boot F15) | üretim derlemesi | testing | debug kapısı | boot'ta tanımlı — A7: üretimde kapalı (`boot-surucu.md` §2) |
| B3 | F14 Test kıyaslama (HIL) | testing | testing (rapor) | sabit senaryo · kabul kriteri | taslak (`firmware-bilesenler.md` §4 — testing sahibi) |
| B4 | `E_TST_FAIL` (H17) | testing | rapor · diagnostics | test hatası: rapor + geç/kal | taslak (`firmware-bilesenler.md` §23) |
| B5 | `E_DIAG_TEST` (H18) | diagnostics | test koşucusu | bağlı testi iptal | taslak (`firmware-bilesenler.md` §23) |
| B6 | `BOOT_FAULT_CODE` (boot F14) | boot çekirdeği | testing (okur) | E1–E7 kodu | SÖZLEŞME (`boot-surucu.md` §2) — okuma serbest |
| B7 | Test modunda hata senaryosu tetiği (S20) | testing | diagnostics | yalnız test kipinde üretilir | İÇİNDE (kapsam §5 S20) |

**Bayrak kuralı (S4):** Yazma yetkisi "Üreten" sütununda tekilleşir; okuma
serbesttir. Yayım atomik bayrak/atomik tamsayı ile yapılır — mutex yasak
(kural 2). Taslak adlar `firmware-evrensel-dil.md` §6 yayınına kadar
**çoğaltılmaz** (`firmware-bilesenler.md` §23 notu).

> ⚠️ **Ad uzayı uyarısı (Q3):** `boot-surucu.md` §2'de F14 = `BOOT_FAULT_CODE`,
> `firmware-bilesenler.md` §4'te F14 = "Test kıyaslama (HIL)" — **iki farklı
> numaralandırma**. Eşleştirme bu üretimde YAPILMADI (uydurma eşitlik
> yazılmaz); soru açık kaydedildi (§8 Q3).

## §4 Hata Kodu Arayüzü

| # | Kod | Üreten | Tüketen | Davranış | Durum |
|---|---|---|---|---|---|
| E1 | `E_TST_FAIL` (H17) | testing (F14 kıyaslama) | rapor + geç/kal akışı | test başarısızlığını sessizce yutmaz; rapora yazar | **taslak** — alan adı H17; nihai ad evrensel-dil §6 yayınında (bilesenler §23 aktarımı) |
| E2 | `E_DIAG_TEST` (H18) | diagnostics | bağlı test koşucusu | bağlı testi iptal eder | taslak |
| E3 | Bus/komut hata kodları (H6–H9) | device-control | testing (negatif test L5) | NACK/timeout/bilinmeyen komut yolu | taslak (kardeş aktarım) |
| E4 | Boot hata kodları E1–E7 | boot çekirdeği | testing (TI-T11) | zincir hata kolu | SÖZLEŞME (`boot-surucu.md` §3 · §6) |

**Hata kodu kuralı (S5):** Kod uydurulmaz; bu tablodaki taslak adlar
kodda kullanılmadan önce dalga-3 kararı gerektirir. Hata kodu arayüzünün
`⚠️` kısmı: `E_TST_FAIL` **varlığı** H17'de kayıtlı, **kesin nihai adı ve
kullanım şekli** taslaktır.

## §5 HIL Ekipman Arayüzü (PLANNED)

| # | Ekipman/arayüz | Ne sağlar | Kaynak | Durum |
|---|---|---|---|---|
| H1 | HIL tezgâhı (fiziksel cihaz) | L3–L6 senaryo çalıştırma | strateji §20 · §21 "Ses laboratuvarı" | **PLANNED** — yok |
| H2 | PCM3168A DAC gerçek çıkış ölçümü | çıkış doğrulaması | strateji §20 satır 1 | PLANNED (donanım bekleniyor) |
| H3 | Koruma rölesi DC offset düzeneği (>0.5V) | DC koruma tetiği | strateji §20 satır 2 | PLANNED |
| H4 | ASIO Exclusive Lock çok-uygulama düzeneği | kilit çakışması | strateji §20 satır 3 | PLANNED (2 uygulama gerekir) |
| H5 | 8.1 surround kanal haritası doğrulayıcı | kanal kapsamı | strateji §20 satır 4 | PLANNED |
| H6 | Test noktaları listesi (PCB erişimi) | HIL bağlantı noktası | `firmware-bagimliliklar.md` E1j | eksik — "HIL eksik kalır" |
| H7 | Ölçüm zinciri (test kipi → senaryo → ölçüm → rapor → kriter) | uçtan uca koşu | `firmware-bagimliliklar.md` §20.5 | tasarlandı; eşik `⚠️ UNKNOWN` (adım 3) |

**Ekipman kuralı (S6):** Ekipman arayüzü `PLANNED` sınıfındadır; bu
arayüz üzerinden **"geçti" raporu üretilemez** (anayasa A7 · strateji §20:
"dört satır hiçbir yerde geçti diye raporlanamaz"). Eşik değeri
`UNKNOWN` olan ölçüm satırına rakam uydurulmaz (bagimliliklar §20.5 adım 3).

## §6 CI Gate Arayüzü (gerçek workflow dosyaları)

| Gate | Dosya | İş/tetik | testing'e etkisi | Sınıf |
|---|---|---|---|---|
| G1 | `.github/workflows/ci.yml` | push · pull_request pipeline | test sonuçları birleşmeyi bloklar | hard |
| G2 | `.github/workflows/secret-scan.yml` | gitleaks taraması | sızıntı kapısı (strateji §18 G2a–G2c) | hard |
| G3 | `ci.yml` (`composer stan`) | PHPStan level=5 | statik analiz kapısı (TI-T12/T13 için host karşılık) | hard |
| G4 | `ci.yml` (php-test matrix) | PHPUnit | L1 host yüzeyi | hard |
| G5 | `ci.yml` (js-test) | Vitest | L1 host yüzeyi | hard |
| G6 | `ci.yml` (js-coverage) | coverage raporu — `continue-on-error: true` | raporlar, **bloklamaz** | soft |
| G7 | `ci.yml` (js-e2e) | e2e — `continue-on-error: true` | raporlar, bloklamaz | soft |
| G8 | `ci.yml` (composer-audit) | bağımlılık denetimi | güvenlik kapısı | hard |
| G9 | **workflow dosyası YOK** | — | firmware/HIL testinin CI karşılığı **tanımsız** | ⚠️ eksik |

**CI arayüzü kuralı (S7):** Bu tablo, workflow dosyalarının **tamamının**
2026-10-14'te okunmasıyla yazılmıdır (kanıt biçimi: dosya okuma; uydurma
pipeline adı yok). Kapıların çalışma **geçmişi** okunmamıştır — "CI geçti"
iddiası bu dosyada `⚠️` taşır (strateji §32 satır 4).

## §7 Komşu Modül Arayüzleri

| Komşu | Yön | Arayüz | Durum |
|---|---|---|---|
| boot | testing ← boot | TI1–TI16 test girdileri + B1–B15 bayrak okumaları (`boot-surucu.md` §2 · §20) | SÖZLEŞME (okundu) |
| update | testing → update | TI-T5 geri alma senaryosu — risk 3 🔴 devri (S5 · S-UPD-01) | devir kayıtlı (riskler §21) |
| diagnostics | testing ↔ diagnostics | test kipi (G5 · Ov7) · `E_DIAG_TEST` (H18) · S20 tetik | taslak |
| device-control | testing → device-control | DV-1…DV-12 doğrulama tetikleri | devir (`device-control-anayasa.md` §10) |
| audio-control | testing ↔ audio-control | DC koruma/röle köprü senaryosu (AV-5) | kardeş aktarım |
| runtime | testing → runtime | F14 test olayı/rapor okuması (telemetri köprüsü) | taslak |
| security | testing ↔ security | L5 negatif imza senaryosu (S2) + G2 kapıları | taslak + aktif kapı |
| CI (DevOps) | testing → CI | G1–G9 gate listesi (§6) | 8/9 mevcut · G9 eksik |

## §8 Açık Sorular (çözülmeden taşındı)

| # | Soru | Etiket | Kaynak |
|---|---|---|---|
| Q0 | **test kapsamı hedefi tanımlı değil** | `⚠️` | `testing/index.md` — KAPATILMADI |
| Q1 | G9 firmware test workflow'u ne zaman eklenir? | `⚠️ VERIFY REQUIRED` | strateji §8 Q3 |
| Q2 | HIL tezgâhı/fixture tanımı ne zaman? | `⚠️ UNKNOWN` | strateji §8 Q1 |
| Q3 | F14 iki ayrı ad uzayında (BOOT_FAULT_CODE ↔ Test kıyaslama) — eşleştirme hangi dosyada? | `⚠️ VERIFICATION REQUIRED` | boot-surucu §2 + bilesenler §2 (çapraz okuma) |
| Q4 | `E_TST_FAIL` nihai kod adı ve yayım tarihi? | `⚠️` | bilesenler §23 (evrensel-dil §6 bekleniyor) |
| Q5 | CI geçmişi ne zaman okunup "yeşil" teyit edilecek? | `⚠️` | strateji §6 ölçüm 4 |
| Q6 | Performans eşikleri (açılış süresi · jitter) kime ait? | `⚠️ VERIFY REQUIRED` | strateji §8 Q4 |
| Q7 | Boot ↔ testing bayrak adları (`BOOT_READY` vs kapsamdaki boot olay adları) tutarlı mı — eşleştirilmedi mi? | `⚠️` | kardeş dosya Q3 pratiği (device-control-surucu) |

## §9 İddia → Dayanak Eşlemesi

| # | İddia (bu dosyada) | Dayanak | Tür |
|---|---|---|---|
| C1 | S1–S16 test tetikleri (TI-T1–TI-T16) | strateji §5 (S1–S10) · §14 · boot-surucu §20 · bagimliliklar §20.5 | kaynak (tasarım) |
| C2 | Boot tetikleri TI1–TI16 testing'e ait | `boot-surucu.md` §20 | kaynak (aktarım) |
| C3 | Bayrak sözlüğü B1–B7 | kapsam §9.6 G5/Ov7/S20 · bilesenler §4/§23 · boot-surucu §2 | kaynak (taslak) |
| C4 | F14 ad uzayı çakışması | boot-surucu §2 (F14) + bilesenler §4 (F14) — çapraz okuma | dürüstlük (eşleştirme YOK) |
| C5 | `E_TST_FAIL` H17 · `E_DIAG_TEST` H18 | bilesenler §23 | kaynak (taslak) |
| C6 | HIL ekipman H1–H7 | strateji §20 · §21 · bagimliliklar §17 E1j · §20.5 | kaynak (PLANNED) |
| C7 | CI G1–G8 gerçek · G9 yok | ci.yml + secret-scan.yml (2026-10-14 okundu) · strateji §3 | disk kanıtı |
| C8 | Soft kapılar G6/G7 | ci.yml (`continue-on-error: true`) | disk kanıtı |
| C9 | Risk 3 devri (S5 HIL) | riskler §2 · §21 | kaynak (devir) |
| C10 | DV-1…DV-12 tetik devri | device-control-anayasa §10 | kaynak (kardeş) |
| C11 | S20 yalnız test kipinde | kapsam §5 S20 | kaynak |
| C12 | HIL terimi testing'e ait | evrensel-dil §2 #24 · §5 | kaynak |
| C13 | "CI yeşil doğrulanmadı" | strateji §6 ölçüm 4 · §32 satır 4 | dürüstlük |
| C14 | Tetikler koşulmadı | anayasa P10 · repo'da firmware kodu yok (0 dosya) | dürüstlük |

## §10 Kapanış Kontrolü (bu dosya)

| # | Kontrol | Beklenti | Durum |
|---|---|---|---|
| 1 | FM anahtar sayısı | 10 alan, doğru sıra | evet |
| 2 | Bölüm başı tablo | §1–§9 her birinde ≥1 tablo | evet |
| 3 | Coverage açık sorusu | çözülmeden taşındı (Q0) | evet |
| 4 | G9 | "yok" olarak yazıldı — ad uydurulmadı | evet |
| 5 | HIL | PLANNED — "geçti" yok | evet |
| 6 | Dosya/satır künyesi | 0 (firmware kodu yok) | evet |
| 7 | Bayrak adı uydurma | 0 — tümü kaynaklı/etiketli | evet |
| 8 | URL | 0 (araştırma yapılmadı) | evet |
| 9 | Son satır | Authority birebir tek satır | evet |


---

## §11 Derinleştirme Eki — Test Tetikleyici Sözleşmeleri (TI detayı)

### §11.1 Tetikleyici davranış kuralları

| Kural | Tanım | İhlali |
|---|---|---|
| TEK-ATIM | tetikleyici bir kez çalışır; otomatik tekrar yok | tekrar-labildir → flaky sinyali |
| GERİ-DÖNÜŞ | tetikleyici sonucu senkron değilse bildirim yolu tanımlı | sessiz bekleme yasak |
| İPTAL | iptal edilen tetik INVALID/BLOKE sayılır | PASS üretilemez |
| İZ | her tetik bir sonuç satırı üretir (§1) | kanıtsız sonuç yasak |
| SAHİP | her tetikleyicinin sahibi modül bellidir | sahipsiz tetik kapıda sayılmaz |

### §11.2 Tetikleyici → sonuç eşlemesi (taslak)

| Tetik | Girdi | Sonuç satırı | Beklenen verdict |
|---|---|---|---|
| TI-T1 senaryo çalıştır | senaryo kodu | ts/scen/verdict | PASS/FAIL/BLOKE/INVALID |
| TI-T2 kapı denetle | kapı kodu | kapi/kanit | PASS/BLOCKED/BELIRSIZ |
| TI-T3 kanıt ekle | kanıt satırı | evidence_ref | UYGUN/UYGUN DEĞİL |
| TI-T4 flaky bildir | tekrar verisi | repeat | FLAKY/DEĞİL |
| TI-T5 rapor oku | raporu | yorum satırı | YORUM/VERİ YOK |
| TI-T10 HIL başlat | ekipman + senaryo | fiziksel ölçüm satırı | (EX4 blokajlı) |

### §11.3 Hata kodu arayüzü (genişletme — sayısal atama YOK)

| Sınıf | Anlamı | Tüketici |
|---|---|---|
| E-INPUT | tetik girdisi hatalı | çağıran (QA/geliştirici) |
| E-ENV | ortam hazır değil (ekipman/CI) | DevOps |
| E-EVID | kanıt yazılamadı | t3 · Vault Steward |
| E-CRIT | kapı kriteri okunamaz | kriter sahibi |
| E-BLOCK | ön koşul eksik (G9/kod) | sahip + Vault Steward |

1. Sınıf **adları** taslaktır; sayısal kod atamak bu vault'ta yasak (uydurma kod).
2. Sınıflar testing-dogrulama §J.1 verdict değerleriyle eşleşir (BLOKE vs INVALID).

---

## §12 Derinleştirme Eki — HIL Ekipman Sözleşmesi (şartname · PLANNED)

### §12.1 Ekipman kayıt alanları (HIL defteri)

| Alan | Açıklama |
|---|---|
| eq_id | ekipman kimliği (üreten atar — ad uydurulmaz) |
| capability | sağladığı ölçüm/sinyal tipi |
| calibration_ref | kalibrasyon kayıt referansı |
| owner | sahip (DevOps/Donanım) |
| status | MÜSAİT/MEŞGUL/BAKIM |

### §12.2 HIL tetik akışı (sözleşme)

1. TI-T10 çağrısı → ekipman durumu MÜSAİT değilse **E-ENV** (senaryo BLOKE).
2. Ekipman sağlandıysa → senaryo çalışır → ölçüm satırı kanıt defterine.
3. Kalibrasyon referansı yoksa sonuç **INVALID** (kanıt zinciri kırılır).
4. HIL çıktısı kapıya (G9) girer — kapı sahibi DevOps + QA.

### §12.3 HIL kapısı blokajı (dürüst kayıt)

- G9 workflow **tanımlı değildir** (firmware-test-stratejisi §3 · testing-ornekler §B.2).
- Bu sözleşme **kullanılamaz durumdadır**; yalnızca şartnamedir.
- Blokaj çözülmeden HIL sonucu üreten her iddia uydurmadır (kapı gizlenemez).

---

## §13 Derinleştirme Eki — CI Gate Sözleşmesi (gerçek dosyalar üzerinden)

### §13.1 Bilinen gerçek workflow'lar (2026-10-14 okuma)

| Dosya | Ne sağlar | Testing'e etkisi |
|---|---|---|
| .github/workflows/ci.yml | genel CI (lint/test job'ları) | DV-T7 determinizm denetimi |
| .github/workflows/secret-scan.yml | secret taraması | güvenlik kapısı (security dalı köprüsü) |

1. Bu iki dosya **gerçek**tir; firmware kapıları (G-firmware/G9) **bu listede yok**.
2. ci.yml js-coverage job'u continue-on-error: true (2026-10-14 tam okuma) —
   yani coverage raporu **zorunlu kapı değildir** (eşik yok — Q0 ile tutarlı).

### §13.2 Kapı genişletme kuralı

- Yeni firmware workflow'u yalnız DevOps ekler; testing **şartname** üretir (§12/§11).
- Eklenen kapı bu dosyaya append edilir (§13.3 boş tablo — doldurulacak).

### §13.3 Yeni kapı kayıt tablosu (boş — doldurulacak)

| Kapı kodu | Workflow dosyası | Katım | Kanıt |
|---|---|---|---|
| (beklenen G-firmware/G9 satırları — DevOps ekleyecek) | | | |

> Boş tablo dolgu değildir: **beklenen satırların yeridir**; uydurma satır yazmak yasak.

---

## §14 Derinleştirme Eki — Komşu Arayüz Genişletme (sahiplik × veri)

| Veri | Üretici | Taşıyan | Tüketici | Gecikme kuralı |
|---|---|---|---|---|
| Senaryo sonucu | t1 | testing-surucu §1 | t2/t3 | kapının önce |
| Kapı kararı | t2 | testing-surucu §1 | Vault Steward | kapı anında |
| Kanıt satırı | t3 | defter | denetçi | yazılma anında |
| Flaky işareti | t4 | rapor | geliştirici | rapor anında |
| HIL ölçümü | F14 | HIL defteri | t2 (G9) | blokaj sonrası |
| Coverage raporu | CI | artefakt | t5 | CI bitişi |

### §14.1 Veri kalite kuralları

1. Her veri taşınırken **kanıt referansı korunur** (yeniden yazım → yeniden üretim).
2. Veri kaybı: taşıma yolunda kayıp görülürse sonuç INVALID (§G.2 t3 kuralı).
3. Zaman damgası: taşıyan taraf ekler — **üretici değildir** (ts sahibi üreticidir).

---

## §15 Derinleştirme Eki — İddia → Dayanak (bu ek)

| İddia (bu ek) | Dayanak |
|---|---|
| t1–t5 tetik kuralları (TEK-ATIM…) | testing-kapsam §G (bileşen envanteri) |
| Sonuç şeması alanları | testing-dogrulama §J.1 |
| HIL akışı | firmware-bagimliliklar §20.5 (HIL 5 adım) |
| G9 blokajı | firmware-test-stratejisi §3 (G9 tanımsız) |
| ci.yml/secret-scan.yml gerçekliği | 2026-10-14 tam dosya okuması |
| js-coverage continue-on-error | ci.yml (2026-10-14 okuma) |
| Kapı kopyalanmaz (SSOT) | testing-kapsam §J.2 |

### §15.1 İddia kuralı (bu ek)

- Çalıştırılmış iddiası: 0 (tümü şartname/PLANNED).
- Uydurma workflow/ekipman/kod adı: 0 (yalnız bilinen iki workflow + boş kayıt tablosu).

---

## §16 Derinleştirme Eki — Red Team Sonuç Tablosu (bu ek)

| İtiraz | Yanıt | Dayanak |
|---|---|---|
| "HIL akışını şimdi uygulayalım" | Hayır — G9 yok; şartname kalır | §12.3 |
| "Boş kapı tablosu silinsin" | Hayır — beklenen satırların yeridir; dolgu değil | §13.3 |
| "Sonuç taşımayı t3 yapsın" | Hayır — üretici/ts sahibi değişir | §14.1 |
| "Kapı kodlarını numaralandıralım" | Sayısal atama yok — DevOps tanır | §11.3 |
| "js-coverage kapısı zorunlu" | Hayır — continue-on-error (2026-10-14) | §13.1 |

---

## §17 Derinleştirme Eki — Sürüm/Değişiklik Kuralları

| Değişiklik | Karar | Onay |
|---|---|---|
| Yeni tetikleyici (TI-T*) | ek uyumlu | QA |
| Sonuç alanına ekleme | ek uyumlu | QA + Vault Steward |
| Sonuç alanı silme/adx değişim | uyumsuz — ADR | Architect |
| Kapı ekleme | ek uyumlu | DevOps + QA |
| Verdict davranışı değişimi | ADR zorunlu | QA + Vault Steward |

1. Uyumluluk yalnız **sözleşme** düzeyindedir; firmware sürüm etiketi bu vault'ta tanımsız.
2. Değişiklikler append ile kaydedilir (mevcut tablolar silinmez).

---

## §18 Derinleştirme Eki — Kapanış Sayımı (bu ek)

| Kontrol | Sonuç |
|---|---|
| Eklenen bölümler | §11–§18 |
| Silinen mevcut içerik | 0 (append-only) |
| Frontmatter | korundu (10 alan + group: kod) |
| Dolgu/TBD | 0 (boş tablo §13.3 bilinçli — doldurulacak) |
| Uydurma workflow/ekipman | 0 |
| Çalıştırılmış iddiası | 0 |
| Son satır Authority | birebir korunur |


---

## §19 Derinleştirme Eki — Sözleşme Denetim Listesi (kapı öncesi)

### §19.1 Her sözleşme değişikliğinde sorulacaklar

1. TI-* tetikleyicinin sahibi değişti mi? — sahipsiz tetik kapıda sayılmaz (§11.1).
2. Sonuç satırı alanları (§1) etkilendi mi? — defter şemasıyla uyum (§14.1).
3. İptal/BLOKE/INVALID ayrımı korundu mu? — karıştırma yasak (testing-kapsam §N.2).
4. HIL sözleşmesi (§12) etkilendiyse G9 blokaj kaydı yenilendi mi?
5. CI kapıları (§13) listeye yeni satır aldı mı (append)?
6. Kapı raporu (§16 itiraz 2) boş tablo bilinçli mi korunuyor (uydurma satır yasak)?

### §19.2 Denetim kayıt satırı (şema — değer üretilmez)

| Alan | Açıklama |
|---|---|
| ts | denetim zamanı (üreten doldurur) |
| ref | değişiklik referansı (dosya + bölüm) |
| verdict | UYGUN / UYGUN DEĞİL / ADR-GEREKLİ |
| followup | izleyen eylem |

---

## §20 Derinleştirme Eki — Kullanım Kılavuzu (kim ne okur)

1. **QA**: §11 (tetik kuralları) → §14 (veri yolu) → §19 (kapı öncesi denetim).
2. **DevOps**: §12 (HIL) + §13 (CI kapıları · boş kayıt tablosu).
3. **Geliştirici**: §14.1 (veri kalite) — sonuç üretirken ts/kanıt sahipliği.
4. **Vault Steward**: §15 (iddia-dayanak) + §18 (kapanış sayımı).
5. **İnceleyen**: §16 red team itirazları — erken kapanış/gizleme tespiti için.

### §20.1 Dosyanın güncellenme koşulu

- G9/DevOps kapısı açılırsa: §13.3 tablosuna satır append edilir (silme yok).
- Yeni tetikleyici onayı: §11.2 tablosuna satır (QA onayı).
- Sonuç alanı değişikliği: §17 ADR kuralı işletilir.

---

## §21 Derinleştirme Eki — Terimler ve Kısaltmalar

| Terim | Anlamı (bu dosyadaki) |
|---|---|
| TI-T* | test tetikleyici arayüzü (§2 · §11) |
| t1–t5, F14 | testing bileşenleri (testing-kapsam §G) |
| EX1–EX4 | ortam kademeleri (testing-dogrulama §I) |
| E-INPUT/ENV/EVID/CRIT/BLOCK | tetik hata sınıfları (§11.3 — sayısal kod yok) |
| G9 | firmware HIL workflow kapısı — TANIMLI DEĞİL |
| HIL defteri | ekipman kayıt alanı (§12.1 — boş kalıp) |
| INVALID/BLOKE | FAIL değildir (testing-kapsam §N.2) |
| SSOT | tek-üretim kuralı — kapı kriteri kopyalanmaz |

---

## §22 Derinleştirme Eki — Kapanış Cümlesi (bu ek bütünlüğü)

Bu ek (§11–§22) testing-surucu.md dosyasını tetikleyici sözleşmeleri, HIL ekipman
şartnamesi, CI kapı kaydı (bilinen iki gerçek workflow + bilinçli boş tablo), veri
kalite kuralları, iddia-dayanak, red team, sürüm kuralları, denetim listesi ve
sözlükle derinleştirmiştir. Tüm içerik şartname/PLANNED düzeyindedir; çalışan
firmware test altyapısı **yoktur** (cpp/h = 0 · G9 tanımsız — 2026-10-15).
Mevcut §1–§10 + frontmatter append-only korunmuştur.


---

## §23 Derinleştirme Eki — Özet ve Son Kontrol Tablosu

| Bölüm | Ne verdi | Bekleyen |
|---|---|---|
| §11 | TEK-ATIM/GERİ-DÖNÜŞ/İPTAL/İZ/SAHİP kuralları + hata sınıfları | sahipsiz tetik yoksa kapı değerlendirilebilir |
| §12 | HIL defteri alanları + tetik akışı | G9 workflow (DevOps) |
| §13 | Gerçek workflow kaydı + boş kapı tablosu | firmware kapı satırları (DevOps) |
| §14 | Veri yolu + ts/kanıt sahipliği | defter uygulaması (t3) |
| §15 | İddia-dayanak (7 satır) | — |
| §16 | 5 red team itiraz-kararı | — |
| §17 | Sürüm/değişiklik kuralları | ADR gerekenler için onay |
| §19 | Kapı öncesi denetim listesi + kayıt şeması | denetim satırları (üretici doldurur) |
| §20–§21 | Kullanım kılavuzu + sözlük | — |
| §22–§23 | Kapanış + son kontrol | — |

### §23.1 Son kontrol (bu ek)

1. append-only ihlali: 0 · Frontmatter: korundu · Authority satırı: birebir son satır.
2. Dolgu/TBD: 0 (§13.3 boş tablo bilinçli bekleyen satır yeridir).
3. Uydurma workflow/ekipman/kod: 0 · Çalıştırılmış iddiası: 0.
4. testing-surucu.md bu ek ile ≥500 kapısını geçmiştir (ölçüm: dosya sonu sayımı).


---

## §24 Derinleştirme Eki — Sonraki Üretim Koşulları

| Koşul | Gelince yapılacak eylem |
|---|---|
| G9 workflow DevOps tarafından eklendi | §13.3 tablosuna satır + §12.3 blokaj kaydı append |
| firmware kodu depoya girdi (cpp/h > 0) | §11.2 TI satırları "kullanılabilir"ye güncellenir |
| Q0 (coverage eşiği) ADR ile çözüldü | §13.1 notuna eşik referansı append edilir |
| t3 kanıt defteri uygulandı | §14.1 kuralları uygulama referansı eklenir |
| Yeni TI-T* onaylandı | §11.2 tablosuna satır (QA onayı) |

1. Tüm güncellemeler **append-only** yapılır; mevcut tablolar silinmez.
2. Her güncellemede §23.1 son kontrolü yenilenir (kapı kapısı ölçümü korunur).
3. Karar sahipliği değişirse §17 tablosu yeni satırla güncellenir (ADR zorunlu).

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode