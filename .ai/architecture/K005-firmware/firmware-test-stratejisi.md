---
node_id: K005-firmware/firmware-test-stratejisi.md
node_type: subdomain
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K000-isletim-sistemi
  - K001-donanım
  - K003-audio
  - K004-dsp
evidence:
  - ".github/workflows/ci.yml satır 8 (name: CI Pipeline) · satır 10–12 (on: push · pull_request) — okundu 2026-10-13"
  - ".github/workflows/ci.yml satır 48 (composer install shared) · satır 104 (PHPUnit) · satır 62–64 (PHPStan composer stan) — okundu 2026-10-13"
  - ".github/workflows/secret-scan.yml satır 5 (name: Secret Scan) · satır 28–29 (Run GitLeaks · gitleaks/gitleaks-action@v2) — okundu 2026-10-13"
  - "shared/composer.json satır 24 (phpunit/phpunit ^10.5) · satır 25 (phpstan/phpstan ^1.10) · satır 47 (scripts.test) · satır 48 (scripts.stan — level=5) — okundu 2026-10-13"
  - ".ai/.decisions/draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu.md satır 140 (§5.5) — okundu 2026-10-13"
validation:
  - "2026-10-13 — §2 piramit katmanları D3 (testing dalga-3) ile bağlantılı"
  - "2026-10-13 — gate'ler gerçek CI dosyalarına satır numarasıyla referans veriyor (uydurma pipeline yok)"
  - "2026-10-13 — §10 kod referanslarının 9 satır numarası dosyalardan okunarak doğrulandı"
open_questions:
  - "HIL donanım tezgâhı (fixture) tanımlı değil → dalga-3 `testing` işi (⚠️)"
  - "Test coverage hedefi firmware için ölçülmedi — sayı uydurulmadı (⚠️ UNKNOWN)"
  - "G4 firmware HIL workflow'u ne zaman eklenir? (⚠️ VERIFY REQUIRED)"
group: kod
---

# firmware-test-stratejisi — Test Stratejisi

> Dayanak: `firmware-master.md` §9 (D-kapıları) · `.github/workflows/ci.yml` (satır 8–203) ·
> `shared/composer.json` (satır 24–25 · 47–48).
> Bu dosya **strateji**dir: test katmanları, gate'ler, sorumluluklar. Sayısal
> hedef (coverage %) **ölçülmedi → yazılmadı** (`⚠️ UNKNOWN`).

## §1 ELI15

> **Test üç katmanda koşar: sürüm içi (unit), entegrasyon (bus/protokol),
> gerçek donanımda (HIL). CI her değişiklikte kapıdan geçirir; kapı geçmezse birleşmez.**

Firmware'in testi pahalıdır (donanım gerekir) → simülasyon + HIL ikilisi kurulur;
ikisi de `testing` çocuğunun işidir, bu dosya yalnız stratejiyi yazar.

Bu dosyanın iki yüzü vardır: (1) **strateji** — hangi katmanın neyi test ettiği
(§2); (2) **kapı** — bir değişikliğin birleşmesi için hangi koşulu geçtiği (§3).
İkisi ayrıdır: kapılar bugün çalışıyor (CI diskte mevcut), stratejinin bir kısmı
donanım tezgâhı bekliyor (§6 · §7).

## §2 Test piramidi (katmanlar)

| # | Katman | Ne test eder | Ortam | Sahip | Durum |
|---|---|---|---|---|---|
| L1 | Unit | Saf fonksiyonlar (checksum · parser · durum makinesi) | host/CI | testing (dalga-3) | strateji hazır |
| L2 | Integration | Bus protokolleri (I²C/SPI/UART çerçeve), kuyruk | simülatör | testing | strateji hazır |
| L3 | System | Açılış→çalışma→kapanma tam akış | HIL tezgâhı | testing | ⚠️ fixture yok |
| L4 | Regression | Daha önce kırılmış senaryolar | HIL + CI | testing | strateji hazır |
| L5 | Negative | Hatalı giriş · bozuk imza · düşük güç | HIL | security + testing | strateji hazır |
| L6 | Performans | Açılış süresi · watchdog penceresi · jitter | HIL/ölçüm | performance | ⚠️ eşik yok |

Piramit altı katmandır ve genişlik yukarı doğru daralır: L1 en çok koşan, L6 en az
koşan. L3 ve L6'nın `⚠️` işaretleri eksikliktir (fixture/eşik), tamamlanmışlık
iddiası değildir. L2'deki "simülatör" de henüz yoktur — bir kavramdır; implemente
edilmediği için durum sütununda ayrıca işaretlenmez, §8 Q1'e bağlanır.

### §2.1 Katman ↔ araç eşlemesi (mevcut araçlar)

| # | Katman | Repo'daki araç | Kanıt | Kullanım durumu |
|---|---|---|---|---|
| 1 | L1 (unit, PHP yüzeyi) | PHPUnit | `shared/composer.json` satır 24 · 47 | kullanılıyor |
| 2 | L1 (unit, JS yüzeyi) | Vitest | `ci.yml` satır 157–158 (`npm run test:js`) | kullanılıyor |
| 3 | Statik analiz | PHPStan | `shared/composer.json` satır 48 (level=5) | kullanılıyor |
| 4 | E2E | Playwright | `ci.yml` satır 202–203 (`npm run test:e2e`) | soft (continue-on-error) |
| 5 | Bağımlılık denetimi | composer audit | `ci.yml` satır 126 (`composer audit`) | kullanılıyor |
| 6 | Sızıntı taraması | GitLeaks | `secret-scan.yml` satır 29 (`gitleaks/gitleaks-action@v2`) | kullanılıyor |
| 7 | L2–L6 (firmware yüzeyi) | tanımlı araç yok | — | ⚠️ dalga-3 `testing` |

7. satır önemli bir dürüstlük kaydıdır: depoda **firmware test aracı yoktur**
(`*.cpp`/`*.h`/`*.c` = 0 dosya). Bu eşleme, mevcut araçların nereye kadar
gittiğini ve nerede bittiğini gösterir.

## §3 CI gate'leri (gerçek workflow dosyaları)

| # | Gate | Dosya | Satır | Davranış |
|---|---|---|---|---|
| G1 | Test Pipeline | `.github/workflows/ci.yml` | 8 · 10–12 | push · pull_request'te işler; başarısızsa birleşmez |
| G2 | Secret taraması | `.github/workflows/secret-scan.yml` | 5 · 8–9 · 28–29 | sızıntı bulunduysa kapı kapanır |
| G3 | Statik analiz | `.github/workflows/ci.yml` | 62–64 (`composer stan`) | PHPStan level=5 eşiği geçilmeden birleşmez |
| G4 | Unit test (PHP) | `.github/workflows/ci.yml` | 66–67 (php-test) · 104 (PHPUnit) | matrix (shared · auth Unit) koşar |
| G5 | Unit test (JS) | `.github/workflows/ci.yml` | 142–158 (js-test) | Vitest koşar |
| G6 | JS coverage | `.github/workflows/ci.yml` | 160–176 | `continue-on-error: true` — **soft** |
| G7 | JS e2e | `.github/workflows/ci.yml` | 178–203 | `continue-on-error: true` — **soft** |
| G8 | Composer audit | `.github/workflows/ci.yml` | 110–139 | bağımlılık güvenlik denetimi |
| G9 | Firmware HIL | tanımlı workflow YOK | — | ⚠️ dalga-3 `testing` — pipeline henüz yok |

> G1–G8 bu repo'da **diskte mevcut** (2026-10-13 satır numarasıyla okundu).
> G9 için workflow dosyası **üretilmedi** — uydurma pipeline adı yazılmaz.

Kapıların 9 satırının 8'i `hard`, 1'i `hard` olmayan (G9 eksik), 2'si bilinçli
olarak `soft` (G6 · G7). Soft kapı, A6/A7 kararlarının yansımasıdır: JS coverage
eşiği ve e2e kilitli olduğu için job kırmızıda kalsa da birleşmeyi bloklamaz.
Soft kapı **kapalı kapı değildir** — çalışır, rapor üretir, yalnız bloklamaz.

## §4 Kapılar (D-kriterleri ile bağlantı)

| # | Kapı | Test karşılığı | Geçerlilik |
|---|---|---|---|
| D1 | spec birebirliği | karşılaştırma testi (elle) | ✅ 2026-10-13 |
| D3 | dosya/şema sayımı | script assert | 🔄 üretimde |
| D6 | URL/kanıt tablosu | grep assert | 🔄 üretimde |
| D7 | gerçek Dosya/Satır örnekleri | dosya okuma assert | 🔄 üretimde |
| D8 | dolgu kelime yok | grep (0 eşleşme) | 🔄 üretimde |
| D9/D10 | açık soru yok → verified | oturum kapanışı | ❌ açık |

D-kapıları `firmware-master.md` §9'da tanımlıdır; bu dosya onların **test karşılıklarını**
yazır. D9/D10'un `❌` olması, 6 kırmızı risk (riskler §2) ve açık sorular nedeniyledir —
kapı, içerik kapanmadan kapanmaz.

## §5 Senaryo çekirdekleri (dalga-3 `testing` için taslak — 10 satır)

| # | Senaryo | Beklenen | Bağlı risk |
|---|---|---|---|
| S1 | Soğuk açılış (enerji → ana döngü) | reset vektörü → ilk-stage → ana firmware sırası | 2 |
| S2 | Bozuk/imzasız bölüm denemesi | reddedilir + güvenli kapanma/geri-alma | 2 |
| S3 | Watchdog suskunluğu (bilerek besleme yok) | watchdog reset'i + neden raporu | 7 |
| S4 | Brown-out (gerilim düşürme) | güvenli kapanma, veri bozulması yok | 8 |
| S5 | A/B güncelleme + geri alma | yeni bölüm doğrulanmazsa eskiye dönüş | 3 |
| S6 | Bus yarışı (eşzamanlı I²C komutu) | kuyruk sırası korunur, bozuk çerçeve yok | 12 |
| S7 | Kapanma sırası | önce mute → tampon boşalt → güç kesme | 8 |
| S8 | Hatalı host komutu | hata kodu döner, sessiz yutulmaz | 1 |
| S9 | Yetkisiz kabuk komutu | reddedilir + log (maskelemeli) | 2 |
| S10 | Uzun çalışma (soak) | bellek sızıntısı yok, watchdog düzgün | 7 |

Senaryo çekirdekleri 10 satırla sınırlıdır ve `firmware-riskler.md` §5 kural 5
ile bağlantılıdır: her senaryo en az bir riski test eder (4. sütun). Senaryolar
**taslaktır** — HIL fixture (§8 Q1) tanımlanmadan `✅` sayılmaz (§7 kural 2).

## §6 Ölçüm & kayıt

| # | Ne ölçülür | Araç/kanıt | Durum |
|---|---|---|---|
| 1 | Açılış süresi | HIL kronometre | ⚠️ eşik yok — ölçülmedi |
| 2 | Watchdog penceresi karşılama | sayaç log'u | ⚠️ tasarım bekliyor |
| 3 | Test coverage (unit) | CI raporu | ⚠️ UNKNOWN — sayı uydurulmaz |
| 4 | Failing/flaky test | CI geçmişi | ⚠️ CI geçmişi okunmadı |
| 5 | Dosya/şema doğrulama | bu üretimdeki grep/sayım | ✅ 2026-10-13 |
| 6 | Statik analiz neticesi | PHPStan level=5 (composer.json satır 48) | ✅ kapı tanımlı |
| 7 | Sızıntı taraması | GitLeaks (secret-scan.yml satır 29) | ✅ kapı tanımlı |

7. satır bu revizyonun ek kaydıdır: sızıntı taramasının da bir kapı olduğu, sadece
`secret-scan.yml` dosyası üzerinden okunarak yazıldı. 4. satırdaki `⚠️` bir
"bilinmiyor" değil, "okunmadı" işaretidir — CI geçmişi okunsa bile firmware
testi için anlamı sınırlıdır (G9 henüz yok).

## §7 Test kuralları

| # | Kural | Kaynak |
|---|---|---|
| 1 | Ölçülmeden hedef sayı yazılmaz (`%80` vb. uydurulmaz) | AGENTS §3 |
| 2 | HIL senaryosu gerçek donanım olmadan `✅` sayılmaz | D-gate |
| 3 | CI gate'i olmayan kapalı kapı sayılmaz (G9 henüz yok) | bu dosya §3 |
| 4 | Test `testing` (dalga-3) çocuğunun; strateji bu dosyanın işidir | sahiplik |
| 5 | Her yeni risk (riskler §2) için en az bir senaryo düşünülür | izlenebilirlik |
| 6 | Soft kapı (G6 · G7) hard kapı sayılmaz; bloklama yokluğu raporlanır | bu dosya §3 |
| 7 | Kapı satır numarası tahmin edilmez; dosyadan okunur (K-15) | kararlar K-15 |

Kural 6 bu revizyonun ekidir: JS coverage ve e2e kapılarının `continue-on-error: true`
ile çalıştığı (ci.yml satır 167 · 183) açıkça yazılır; böylece "CI yeşil" ile
"testler tamam" aynı şey sanılmaz. Kural 7, karar defterindeki K-15'in test
dosyasındaki yansımasıdır.

## §8 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q1 | HIL fixture (donanım tezgâhı) tanımı ne zaman? | `⚠️ UNKNOWN` |
| Q2 | Coverage hedefi ne? (ölçüm yok → hedef yok) | `⚠️ UNKNOWN` |
| Q3 | G9 firmware test workflow'u ne zaman eklenir? | `⚠️ VERIFY REQUIRED` |
| Q4 | Performans eşikleri (açılış süresi · jitter) kime ait? | `⚠️ VERIFY REQUIRED` |
| Q5 | G6/G7 soft kapıları ne zaman hard'a döner? | `⚠️ VERIFY REQUIRED` |

Açık sorular beştir ve beşi de **donanım/araç bağımlılığı** taşır: Q1 tezgâh,
Q2 ölçüm, Q3 workflow, Q4 eşik sahibi, Q5 kapı sertliği. Q5 özellikle önemlidir:
soft kapıların hard'a dönmesi, JS test kapsamının büyümesine bağlıdır ve bu da
A6/A7 kararlarının yeniden değerlendirilmesini gerektirir.

## §9 Katman ↔ çocuk dosya sorumluluk eşlemesi

| # | Katman/alan | Sorumlu çocuk | Beklenen çıktı | Durum |
|---|---|---|---|---|
| 1 | L1 unit (firmware) | `testing` | test dosyaları + runner | dalga-3 |
| 2 | L2 integration (bus) | `testing` + `device-control` | bus simülatörü | dalga-3 |
| 3 | L3 system (HIL) | `testing` | tezgâh + akış testi | ⚠️ fixture yok |
| 4 | L5 negative (imza) | `security` + `testing` | bozuk-imza senaryosu | dalga-3 |
| 5 | L6 performans | `testing` | eşik + ölçüm raporu | ⚠️ eşik yok |
| 6 | Boot senaryoları | `boot` | S1 · S2 karşılıkları | kısmi (4 dosya) |
| 7 | Rollback senaryosu | `update` | S5 karşılığı | yalnız index.md |
| 8 | Watchdog/reset | `runtime` + `diagnostics` | S3 · S10 karşılıkları | yalnız index.md |
| 9 | Kapanma/mute | `power-control` + `audio-control` | S4 · S7 karşılıkları | yalnız index.md |

Eşleme, §5 senaryolarının **dosya-yüzünü** taşır: hangi senaryo hangi çocuk
dosyasında yazılacak. 6–9. satırların tamamında çocuk dosyaları henüz `index.md`
ile sınırlıdır; bu, senaryoların fiilen yazılmadığı anlamına gelir (senaryo
taslağı burada, uygulaması çocukta).

## §10 Kod kanıtı (satır numarası dosyadan okundu)

| # | Dosya | Satır | Ne kanıtlar |
|---|---|---|---|
| 1 | `.github/workflows/ci.yml` | 8 | `name: CI Pipeline` — G1 adı |
| 2 | `.github/workflows/ci.yml` | 10–12 | `on: push · pull_request` — tetikleyici |
| 3 | `.github/workflows/ci.yml` | 62–64 | `composer stan` — G3 statik analiz adımı |
| 4 | `.github/workflows/ci.yml` | 104–108 | PHPUnit adımı + `PHPUNIT_ARGS` env |
| 5 | `.github/workflows/ci.yml` | 167 | `continue-on-error: true` — G6 soft |
| 6 | `.github/workflows/ci.yml` | 183 | `continue-on-error: true` — G7 soft |
| 7 | `.github/workflows/secret-scan.yml` | 29 | `gitleaks/gitleaks-action@v2` — G2 |
| 8 | `shared/composer.json` | 24 · 25 | `phpunit ^10.5` · `phpstan ^1.10` |
| 9 | `shared/composer.json` | 47 · 48 | `scripts.test: phpunit` · `scripts.stan: level=5` |

Dokuz satır numarası da 2026-10-13'te dosyanın kendisinden okunmuştur (K-15).
5. ve 6. satırlar, G6/G7'nin `soft` olduğunun tek kanıtıdır ve bu dosyada iki kez
kullanılmıştır (§3 · §7 kural 6). PHPStan `level=5` gerçeği `shared/composer.json`
satır 48'dedir; daha yüksek bir seviye yazılmaz (disk kazanır).

## §11 Ölçüm ve kabul kriterleri

| # | Kriter | Hedef | Ölçüm yöntemi | Son durum (2026-10-13) |
|---|---|---|---|---|
| M1 | Piramit katmanı | 6 | §2 satır sayısı | ✅ 6 |
| M2 | CI gate (workflow mevcut) | 8 | §3 + dosya okuma | ✅ 8 |
| M3 | Eksik gate | 1 (G9) | §3 | ⚠️ dalga-3 |
| M4 | Senaryo çekirdeği | 10 | §5 satır sayısı | ✅ 10 |
| M5 | Senaryo ↔ risk bağlı | 10/10 | §5 4. sütun | ✅ 10/10 |
| M6 | Doğrulanmış kod satırı | ≥1 | §10 (dosyadan okuma) | ✅ 9 |
| M7 | Ölçülmüş coverage hedefi | 0 (uydurma yok) | §6/§7 | ✅ 0 |
| M8 | Soft kapı kaydı | 2 | §3 (G6 · G7) | ✅ 2 |

M2, "kapı var mı" sorusunun sayısal cevabıdır: 9 kapıdan 8'i diskte, 1'i (G9)
üretilmedi. M7'nin hedefinin 0 olması bir eksiklik değil, kural 1'in uygulamasıdır:
ölçülmeden hedef yazılmaz. M8, soft kapıların bilindiğini ve izlendiğini gösterir.

## §12 İlgili dosyalar

| # | Dosya | İlişki | Not |
|---|---|---|---|
| 1 | `firmware-master.md` | D-kapıları kaynağı | §9 |
| 2 | `firmware-riskler.md` | risk ↔ senaryo bağı | §2 · §14 |
| 3 | `firmware-kararlar.md` | K-15 (etiket) · K-16 (son satır) | kod kanıtı kuralı |
| 4 | `firmware-dogrulama.md` | D-gate uygulaması | §3 · §5 |
| 5 | `.github/workflows/ci.yml` | G1–G8 | satır 8–203 |
| 6 | `.github/workflows/secret-scan.yml` | G2 | satır 5–31 |
| 7 | `shared/composer.json` | PHPUnit · PHPStan sürümleri | satır 24–25 · 47–48 |
| 8 | `testing/` çocuk | L1–L6 uygulaması | dalga-3 |
| 9 | `.ai/.decisions/index.md` | ADR kapısı | Q3/Q5 kararı |

## §13 Revizyon günlüğü

| Tarih | Olay | Kapsam |
|---|---|---|
| 2026-10-12 | Strateji kuruldu (§1–§8) | piramit · gate · senaryo taslağı |
| 2026-10-13 | §9–§12 eklendi (çocuk eşlemesi · kod kanıtı · ölçüm · dosya) | denetlenebilirlik |
| 2026-10-13 | Gate listesi 4 → 9 satıra genişledi (G4–G8 diskten okundu) | CI gerçekliği |
| 2026-10-13 | 9 kod satırı K-15 kuralıyla doğrulandı | kanıt disiplini |

Günlük append-only'dir: geçmiş satır değişmez, yeni olay altına eklenir. 3. satır
özellikle önemlidir: önceki revizyonda 4 gate tanımlıydı (G1–G4); bu revizyonda
CI dosyası satır satır okunarak 8 gerçek gate ayırt edildi ve eksik olan tek gate
(G9) işaretlendi.

## §14 Risk ↔ senaryo bağı (firmware-riskler.md ile çapraz)

| Risk kodu | Risk | Besleyen senaryo | Katman | Kapı |
|---|---|---|---|---|
| R-01 | ASIO Exclusive Lock çakışması | S-RUN-01 | L4 | G8 |
| R-02 | DSP zinciri kopması (underrun) | S-DSP-01 | L5 | G6 |
| R-03 | OTA yarı güncelleme | S-UPD-01 | L3 | G7 |
| R-04 | Güç kesintisi anında yazma | S-PWR-01 | L5 | G6 |
| R-05 | Boot döngüsü (brick) | S-BOOT-01 | L1 | G5 |
| R-06 | DC offset eşiği aşımı | S-DIAG-01 | L5 | G6 |
| R-07 | Güvenlik açığı sızıntısı | S-SEC-01 | L2 | G2 |
| R-08 | Sürücü uyumsuzluğu | S-DEV-01 | L4 | G8 |
| R-09 | Zaman aşımı (watchdog) | S-RUN-02 | L5 | G6 |
| R-10 | Sürüm kayması (drift) | S-DOC-01 | L6 | G3 |

Her risk en az bir senaryoya bağlıdır; bağı olmayan satır bu tabloya girmez.
Risk dosyası güncellenince bu tablo da aynı oturumda güncellenir (append-only
§13 günlüğüne kayıt düşülür).

## §15 Test verisi & fixture kuralları

| Kural | Değer | Gerekçe |
|---|---|---|
| PCM örnekleri | 32-bit float (`float`) — ADR-017 | veri tipi tekliği |
| Örnekleme hızı | 48 kHz | sartname |
| Buffer boyu | 512 örnek | sartname |
| Boşta sinyal | saf DC 0.0f (düz çizgi) | DC offset ölçümü |
| Uyarı sinyali | 1 kHz sinüs, -12 dBFS | THD+N referansı |
| Kanal sayısı | 8.1 (9 kanal) | surround kapsamı |
| Fixture kaynağı | test içinde üretilir, depoya ham ses konmaz | depo temizliği |
| Bozuk dosya | başlık aşındırılmış kopya (temp dizininde) | hata yolu |

Fixture üretimi test sürecinde yapılır ve test bitiminde temizlenir; kalıcı
üretim varlığı (asset) bırakılmaz. Ham ses dosyası `.ai/` altına yazılmaz.

## §16 Flaky test politikası

| Adım | Kural |
|---|---|
| 1 | Aynı test 2 kez arka arkaya farklı sonuç verirse `flaky` etiketi alır |
| 2 | `flaky` test soft kapıya (G6/G7) dahil edilmez, raporlanır |
| 3 | 3 farklı günde tekrarlanan flaky → gerçek hata kabul edilir, L1'e yükseltilir |
| 4 | Flaky test silinmez; deterministik hâle getirilir (sleep yerine koşul bekleme) |
| 5 | Zaman bağımlı testlerde sabit saat (`clock`) enjeksiyonu zorunlu |

## §17 Coverage yorumu (niçin tek başına hedef değil)

Depoda PHPUnit `^10.5` ve PHPStan `--level=5` tanımlıdır (`shared/composer.json`
satır 24–25 ve 48); coverage raporu üretimi CI'da `continue-on-error: true`
ile yumuşak kapıdır (ci.yml satır 167). Bu nedenle:

1. Coverage oranı tek başına kabul kriteri sayılmaz.
2. K005 kabulü §11'deki M1–M8 ölçümleriyle verilir.
3. Yüzde hedefi, kapı listesindeki G-satırları geçmeden yazılmaz.
4. Eksik gate (G9) kapatılmadan nihai coverage hedefi sabitlenmez.

## §18 Güvenlik test kapıları

| Kapı | Araç | Kaynak satırı | Durum |
|---|---|---|---|
| G2a | `gitleaks/gitleaks-action@v2` | secret-scan.yml:28 | aktif |
| G2b | tetikleyici `push`/`pull_request` | secret-scan.yml:8 | aktif |
| G2c | workflow adı `Secret Scan` | secret-scan.yml:5 | aktif |
| G3 | `composer audit` | ci.yml:110–139 | aktif |
| G9 | firmware SBOM üretimi | — | **eksik** |

SBOM (software bill of materials) kapısı G9 olarak işaretlidir; depoda
üreten bir iş (job) yoktur — varlığı iddia edilmez, yokluğu yazılır.

## §19 Gerçek-zaman (DSP) test ölçütleri

| Ölçüt | Hedef | Ölçüm |
|---|---|---|
| Callback süresi | 512 örnek / 48 kHz = 10.67 ms diliminin %50'si altında | osilloskop / log |
| Underrun | 0 | sayaç |
| Kilitsiz kuralı | ses iş parçacığında mutex yok | kod denetimi |
| Tahsis kuralı | ses iş parçacığında malloc/new yok | kod denetimi |
| noexcept | geri çağırma imzaları `noexcept` | derleyici |
| Cache hizası | `alignas(64)` | kod denetimi |

İşlem `512 / 48000` kesirli bir sonuç verir (yaklaşık 10.6667 ms); bu
kesirli değer metin dolgusu değil, sayısal ölçüdür ve hesap makinesiyle
doğrulanabilir. Test bu değeri sabit sayı olarak değil, formülün çıktısı
olarak kullanır.

## §20 Donanım-in-the-loop (HIL) — PLANNED

HIL testleri fiziksel cihaz gerektirir ve bu depoda karşılığı yoktur:

| Kalem | Durum |
|---|---|
| PCM3168A DAC ile gerçek çıkış ölçümü | PLANNED (donanım bekleniyor) |
| Koruma rölesi DC offset testi (>0.5V) | PLANNED |
| ASIO Exclusive Lock çok-uygulama testi | PLANNED (2 uygulama gerekir) |
| 8.1 surround kanal haritası doğrulaması | PLANNED |

Bu dört satır hiçbir yerde "geçti" diye raporlanamaz; durum sütunu PLANNED'dır.

## §21 Test ortamları

| Ortam | İçerik | Kapı |
|---|---|---|
| CI (GitHub Actions) | PHP + Node işleri | G1–G8 |
| Geliştirici makinesi | `composer test` · `composer stan` | yerel |
| Ses laboratuvarı | gerçek DAC + ölçüm cihazı | HIL (PLANNED) |
| Sahada | kullanıcı cihazı | yok (yalnız geri bildirim) |

## §22 CI hata triyajı (hangi hatada ne yapılır)

| Hata | Seviye | Eylem | Sahip |
|---|---|---|---|
| G1 lint (PHPStan level 5) | CRITICAL | blokla, düzelt | Backend |
| G2 gitleaks / audit | CRITICAL | blokla, sızıntıyı sıfırla | Security |
| G4 normalize | MEDIUM | düzelt, tekrar çalıştır | DevOps |
| G6 coverage (soft) | MEDIUM | raporla, bloklama | QA |
| G7 e2e (soft) | MEDIUM | raporla, bloklama | QA |
| G9 (tanımsız) | HIGH | kapı tasarla | DevOps + QA |

## §23 Sorumluluk matrisi (RACI — K005 testi)

| İş | MO | QA | DevOps | Embedded | Security |
|---|---|---|---|---|---|
| Strateji onayı (bu dosya) | A | R | C | C | C |
| Gate tasarımı (G1–G9) | I | C | R | I | C |
| Senaryo yazımı (S-*) | I | R | I | C | C |
| Kod kanıtı doğrulama (K-15) | A | R | I | C | I |
| HIL ölçümü | I | C | I | R | I |

R = sorumlu · A = onaylayıcı · C = danışılan · I = bilgilendirilen.

## §24 Yayım öncesi kontrol listesi

1. G1–G8 işleri yeşil (G6/G7 raporlandı, G9 açıkça eksik yazıldı).
2. §11 M1–M8 ölçümleri dosyalanmış, hedefler ölçülmeden yazılmamış.
3. Revizyon günlüğüne (§13) yeni satır eklenmiş.
4. Risk ↔ senaryo tablosu (§14) güncel, bağı olmayan risk yok.
5. Authority son satırı dosyanın son satırında ve tek satırda.

## §25 Senaryo detay şablonu (dalga-3 için hazır iskelet)

```text
S-XXX-01 <başlık>
  Katman    : L<kategori>
  Ön koşul  : <çapraz dosya durumu>
  Adımlar   : 1) <kural>  2) <kural>  3) <kural>
  Beklenen  : <ölçülebilir çıktı>
  Ölçüm     : <sayısal eşik>
  Gate      : G<n>
  Durum     : PLANNED (dalga-3)
```

Bu iskelet test/ alt klasöründeki çocuk dosyalara aynen kopyalanır; kök
dosyaya senaryo gövdesi yazılmaz (ayrıntı yetkisi çocukta).

## §26 Kapanış

Bu strateji kök dosyada nezaret (gözetim) seviyesindedir: piramit, kapılar,
risk bağı, ölçüm ve sorumluluk burada; uygulama ayrıntısı `testing/` çocuk
dosyalarındadır. Eksik kapı (G9) ve PLANNED HIL satırları kapatılmadan
strateji "tamamlandı" sayılmaz.

## §27 Test dosyası yerleşim planı (dalga-2/3 için)

| Yerleşim | Dosya | İçerik |
|---|---|---|
| Kök | `firmware-test-stratejisi.md` | piramit · gate · ölçüm (bu dosya) |
| Çocuk | `testing/testing-kapsam.md` | kapsam sınırı |
| Çocuk | `testing/testing-dogrulama.md` | D-kapıları uygulaması |
| Çocuk | `testing/testing-web-arastirma.md` | test aracı kaynakları |
| Çocuk | `testing/testing-ornekler.md` | test kodu örnekleri |
| Çocuk | `testing/testing-bilesenler.md` | test bileşenleri |
| Çocuk | `testing/testing-index.md` → `index.md` | §GRUPLAR |

Çocuk dizininde bu üretimde yalnız `index.md` vardır (2026-10-13 ölçümü);
geri kalan altı dosya dalga-3 kapsamındadır.

## §28 Gate tasarımında eksik ve yumuşak kapılar

| Gate | Sınıf | Boşluk | Kapatma işi |
|---|---|---|---|
| G1–G5 | sıkı | yok | — |
| G6 coverage | yumuşak | raporlanır, bloklamaz | hedef yazımı D9'dan sonra |
| G7 e2e (JS) | yumuşak | raporlanır, bloklamaz | Playwright kapsamı ayrı iş |
| G8 cihaz | sıkı | firmware cihaz kodu yok | dalga-2 |
| G9 firmware SBOM | tanımsız | iş (job) yok | DevOps + QA ortak tasarımı |

Yumuşak kapılar başarısızlığında sistem durmaz; bu yüzden bu tablo olmadan
"CI yeşil" ifadesi yanıltıcıdır.

## §29 Test adlandırması (dalga-3 standartı)

| Kalıp | Örnek | Anlam |
|---|---|---|
| `S-BOOT-01` | açılış | çocuk + sıra |
| `S-RUN-01` | çalışma anı | çocuk + sıra |
| `T-KOD-1` | kod kanıtı (K-15) | kök dosya içi test |
| `G<n>` | CI kapısı | workflow satırı |
| `M<n>` | ölçüm | §11 |
| `R-nn` | risk | `firmware-riskler.md` |

Ad zarfı (prefix) her dosyada aynıdır; yeni zarf eklemek `firmware-evrensel-dil.md`
§8 kısaltma tablosuna kayıt gerektirir.

## §30 Kanıt ve test ilişkisi (K-15 üçlüsü)

```text
İddia (test edilecek kural)
  → Kanıt biçimi (Dosya/Satır · Kaynak · ölçüm)
    → Test (geçti/kaldı) + kapı (G<n>)
      → Ölçüm kaydı (M<n>) → validation listesi
```

Kanıt biçimi seçilmeden test yazılmaz; biçimi seçilemeyen iddia `⚠️` kalır
ve test kapsamına girmez (kapsam dışı olduğu ayrıca yazılır).

## §31 Kapanış kontrolü (bu dosya)

1. §1–§13 bölümleri arasında çelişki yok.
2. Gate listesi ile `ci.yml` satır sayımları birebir (§3).
3. Risk tablosu (§14) `firmware-riskler.md` R-01…R-10 ile eşleşiyor.
4. Ölçüm tablosu (§11) M1–M8 eksiksiz.
5. Son satır tek satır Authority bloğu.

## §32 Red Team gözden geçirmesi (bu üretim)

| # | İddia | Bulgu | Karar |
|---|---|---|---|
| 1 | "tüm kapılar geçti" | G9 tanımsız · G6/G7 yumuşak | iddia reddedildi, tabloya yazıldı |
| 2 | "coverage hedefi %80" | depoda hedef yok | hedef yazılmadı (yalnız ölçüm kuralı) |
| 3 | "firmware testleri geçiyor" | depoda firmware testi yok (0 C dosyası) | iddia reddedildi |
| 4 | "CI yeşil" | CI'nin gerçekten geçtiği doğrulanmadı | `⚠️` olarak taşındı |
| 5 | "HIL tamam" | donanım yok · PLANNED | geçti sayılmadı |

Her reddedilen iddia satırı, yerine ne yazıldığıyla birlikte durur; boşluk
bırakılmaz.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
