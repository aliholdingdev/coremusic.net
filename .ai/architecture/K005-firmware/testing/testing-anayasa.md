---
node_id: K005-firmware/testing/testing-anayasa
node_type: module
status: draft
parent_id: K005-firmware/testing
children: []
depends_on:
  - K005-firmware/testing
  - K005-firmware
  - K005-firmware/boot
evidence:
  - "K005-firmware/boot/boot-anayasa.md §1-§26 — anayasa şablonu, okundu 2026-10-14"
  - "K005-firmware/boot/boot-surucu.md §2 · §20 — bayrak sözlüğü + test arayüzü şablonu, okundu 2026-10-14"
  - "K005-firmware/testing/index.md (ELI15 · open_questions · §GRUPLAR) — okundu 2026-10-14"
  - "K005-firmware/firmware-test-stratejisi.md §2 · §2.1 · §3 · §4 · §5 · §6 · §7 · §8 · §11 · §14 · §16 · §17 · §20 · §21 · §23 · §25 · §28 · §29 · §30 · §32 — okundu 2026-10-14"
  - "K005-firmware/firmware-dogrulama.md §3 — D1–D10 + geçiş kuralı, okundu 2026-10-14"
  - "K005-firmware/firmware-master.md §2 · §9 · §10 · K-8 — okundu 2026-10-14"
  - "K005-firmware/firmware-kapsam.md §5 · §9.9 (T1–T5) · satır I11 — okundu 2026-10-14"
  - "K005-firmware/firmware-bilesenler.md §4 (F14) · §23 (H17 E_TST_FAIL) — okundu 2026-10-14"
  - "K005-firmware/firmware-bagimliliklar.md §17 (D14) · §20.5 (HIL senaryosu) — okundu 2026-10-14"
  - "K005-firmware/firmware-riskler.md §2 (risk 3 🔴) · §21 (testing satırı) — okundu 2026-10-14"
  - "K005-firmware/firmware-kararlar.md §2 (K-15 · K-16) — okundu 2026-10-14"
  - "K005-firmware/firmware-evrensel-dil.md §2 (terim 24 HIL) · §5 (testing eşlemesi) — okundu 2026-10-14"
  - "K005-firmware/device-control/device-control-anayasa.md §1-§20 — kardeş şablon, okundu 2026-10-14"
  - ".github/workflows/ci.yml — G1–G8 kapıları, okundu 2026-10-14"
  - ".github/workflows/secret-scan.yml — G2 kapısı, okundu 2026-10-14"
validation:
  - "2026-10-14 — FM 10 alan + group + son satır Authority denetimi"
open_questions:
  - "test kapsamı hedefi tanımlı değil ⚠️ (index.md open_question — çözülmedi, taşındı)"
  - "Coverage hedefi ne? — ölçüm yok → hedef yok (test-stratejisi §8 Q2 · §11 M7 = 0) ⚠️ UNKNOWN"
  - "HIL fixture (donanım tezgâhı) tanımı ne zaman? (§8 Q1) ⚠️ UNKNOWN"
  - "G9 firmware test workflow'u ne zaman eklenir? (§8 Q3) ⚠️ VERIFICATION REQUIRED"
  - "G6/G7 soft kapıları ne zaman hard'a döner? (§8 Q5) ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# testing-anayasa — Testing Alt-Modülü Anayasası (K005-firmware)

> **ELI15:** Bu dosya, firmware'in "doğru çalışıyor" iddiasının **kural
> kitabıdır**: neyin test edileceği, neyin "geçti" sayılacağı ve neyin asla
> ölçülmüştür sayılmayacağı burada yazar. Repo'da derlenebilir firmware
> kodu YOK (0 `.c`/`.cpp` dosyası — 2026-10-13 keşfi), HIL tezgâhı yok,
> coverage ölçümü yok: bu yüzden buradaki her şey **mimari taslaktır** ve
> hiçbir kapı bu üretimde **çalıştırılmamıştır**. Kural bu dosyada;
> arayüz/sözleşme `testing-surucu.md`, sınır `testing-kapsam.md`, kalıp
> `testing-ornekler.md`, kanıt `testing-dogrulama.md` içindedir.

## §1 Amaç, Kapsam ve Kapsam Dışı

| Alan | Soru | Cevap | Sahip |
|---|---|---|---|
| Amaç | Bu dosya neyi düzenler? | testing alt-modülünün anayasası (test doktrini + kapılar) | K005-firmware/testing |
| Kapsam içi | Test piramidi L1–L6 katman doktrini | evet — strateji §2 | testing |
| Kapsam içi | CI gate'leri G1–G9 + D-kapılarının test karşılıkları | evet — strateji §3 · §4 | testing |
| Kapsam içi | Ölçüm · kabul · flaky disiplini (M1–M8) | evet — strateji §6 · §11 · §16 | testing |
| Kapsam içi | HIL senaryo tanımı ve dürüst durum etiketi (PLANNED) | evet — strateji §20 | testing |
| Kapsam dışı | Test senaryosunun firmware'de çalıştırılması | hayır → HIL tezgâhı + G9 workflow (ikisi de yok) | ⚠️ dalga-3 |
| Kapsam dışı | DSP/algoritma testinin doğrulama eşiği | hayır → `audio-control` (strateji §19 ölçütleri köprüsü) | K005/audio-control |
| Kapsam dışı | OTA imza/rollback tasarımının kendisi | hayır → `update` (test yalnız HIL provası) | K005/update |
| Kapsam dışı | Host yüzeyi PHP/JS testi yazımı | hayır → mevcut CI işleri (G4/G5) | QA · DevOps |
| Kapsam dışı | Güvenlik açıklığı taraması | hayır → G2 (gitleaks) · security | K005/security |

> **Sınır notu:** testing *testi tanımlar ve kanıtlar*; firmware *üretmez*,
> donanım *kurmaz*. HIL tezgâhı ve G9 workflow'u olmadan "firmware testleri
> geçti" cümlesi bu vaultta **kurulamaz** (strateji §32 Red Team satır 3).

## §2 Test Piramidi Doktrini (L1–L6)

| Katman | Ad | Ne test eder | Ortam | Sorumlu | Durum |
|---|---|---|---|---|---|
| L1 | Unit | Saf fonksiyonlar (checksum · parser · durum makinesi) | host/CI | testing (dalga-3) | strateji hazır |
| L2 | Integration | Bus protokolleri (I²C/SPI/UART çerçeve), kuyruk | simülatör | testing | strateji hazır |
| L3 | System | Açılış→çalışma→kapanma tam akış | HIL tezgâhı | testing | ⚠️ fixture yok |
| L4 | Regression | Daha önce kırılmış senaryolar | HIL + CI | testing | strateji hazır |
| L5 | Negative | Hatalı giriş · bozuk imza · düşük güç | HIL | security + testing | strateji hazır |
| L6 | Performans | Açılış süresi · watchdog penceresi · jitter | HIL/ölçüm | performance | ⚠️ eşik yok |

**Doktrin kuralı (A1):** Piramit altı katmandır, genişlik yukarı doğru daralır:
L1 en çok koşar, L6 en az koşar. L3/L6 satırlarındaki `⚠️` (fixture/eşik)
**eksikliktir, tamamlanmışlık iddiası değildir**; L2'deki "simülatör" henüz
**kavramdır** — implemente edilmemiştir (strateji §2).

**Araç eşlemesi (A2)** — yalnız okunan satırlar:

| Katman/araç | Durum | Kaynak |
|---|---|---|
| L1 (PHP yüzeyi) → PHPUnit `^10.5` | kullanılıyor | strateji §2.1 · `shared/composer.json` (okundu) |
| L1 (JS yüzeyi) → Vitest | kullanılıyor | strateji §2.1 (`npm run test:js`) |
| L2–L6 (firmware yüzeyi) → **tanımlı araç yok** | ⚠️ | strateji §2.1 7. satır |
| Unity / ceedling · cppcheck / Clang-Tidy | planlı (kardeş aktarım) | `device-control-dogrulama.md` §B.1 · `device-control-kapsam.md` §A.3 DC-5 |

> "Tanımlı araç yok" satırı bir eksiklik kaydıdır; araç adı bu dosyada
> **uydurulmaz** — planlı araçlar kardeş dosya aktarımıdır.

## §3 D-Kapıları ve PASS/BLOCK Kuralı

| # | Kriter | Şu an | Test karşılığı |
|---|---|---|---|
| D1 | v2 §2.5 birebirliği | ✅ 2026-10-12 | karşılaştırma testi (elle) |
| D2 | 12 kök MD mevcut | ✅ 2026-10-13 | script assert (dizin sayımı) |
| D3 | Kademe-2 çocuk şeması (6 × 6 MD) | 🔄 | script assert |
| D4 | Frontmatter 9 anahtar, doğru sırada | 🔄 | script assert |
| D5 | `index.md` → `## §GRUPLAR` | ✅ 2026-10-13 | grep assert |
| D6 | Web iddiaları İddia/URL/Tarih/Durum tablosu | ✅ 2026-10-13 | grep assert |
| D7 | Kod örnekleri gerçek dosya/satır künyesi | ✅ 2026-10-13 | dosya okuma assert |
| D8 | Dolgu kelimesi yok | 🔄 | grep (0 eşleşme) |
| D9 | Açık ⚠️/UNKNOWN kalmadı | ❌ | oturum kapanışı |
| D10 | `validation:` listesine `verified` kaydı | ❌ | D1–D9 ✅ sonrası |

**PASS/BLOCK kuralı (A3):** D1–D10 **tamamı** ✅ olmadan `validation:`
listesine tarihli `verified` kaydı düşülmez; **kısmi geçiş yoktur**, tek ❌
kapıyı kapalı tutar (`firmware-dogrulama.md` §3 geçiş kuralı). D9/D10'un ❌
olmasının sebebi 6 kırmızı risk (riskler §2) + açık sorulardır — içerik
kapanmadan kapı kapanmaz. Bu üretimde D-kapılarının **hiçbiri
çalıştırılmamıştır**; durumlar kök dosyadan okunarak taşınmıştır.

## §4 CI Gate'leri (G1–G9) — Gerçek Workflow Dosyaları

| Gate | Dosya (okundu 2026-10-14) | Davranış | Sınıf |
|---|---|---|---|
| G1 Test Pipeline | `.github/workflows/ci.yml` | push · pull_request'te işler; başarısızsa birleşmez | hard |
| G2 Secret taraması | `.github/workflows/secret-scan.yml` | gitleaks — sızıntı bulunduysa kapı kapanır | hard |
| G3 Statik analiz | `ci.yml` (`composer stan`) | PHPStan level=5 eşiği geçilmeden birleşmez | hard |
| G4 Unit test (PHP) | `ci.yml` (php-test matrix) | PHPUnit koşar | hard |
| G5 Unit test (JS) | `ci.yml` (js-test) | Vitest koşar | hard |
| G6 JS coverage | `ci.yml` (js-coverage) | `continue-on-error: true` — **soft** | soft |
| G7 JS e2e | `ci.yml` (js-e2e) | `continue-on-error: true` — **soft** | soft |
| G8 Composer audit | `ci.yml` (composer-audit) | bağımlılık güvenlik denetimi | hard |
| G9 Firmware HIL | **workflow YOK** | — | ⚠️ eksik |

**Gate kuralı (A4):** 9 kapının 8'i diskte mevcut, **1'i (G9) yok**; 2'si
(G6 · G7) bilinçli `soft` (A6/A7 kararları). Soft kapı **kapalı kapı
değildir** — çalışır, rapor üretir, yalnız bloklamaz; bu yüzden "CI yeşil" ≠
"testler tamam" (strateji §3 · §7 kural 6 · §28). **CI gate'i olmayan kapı
kapalı kapı sayılmaz** (§7 kural 3) — G9 için pipeline henüz yoktur, adı
uydurulmaz. G6/G7'nin `continue-on-error` olduğu, bu üretimde `ci.yml`
dosyasının kendisinin okunmasıyla teyit edilmiştir.

## §5 Ölçüm, Kabul ve Coverage Yorumu

| # | Ölçüm | Hedef | Son durum |
|---|---|---|---|
| M1 | Piramit katmanı | 6 | ✅ 6 |
| M2 | CI gate (workflow mevcut) | 8 | ✅ 8 |
| M3 | Eksik gate | 1 (G9) | ⚠️ dalga-3 |
| M4 | Senaryo çekirdeği | 10 | ✅ 10 |
| M5 | Senaryo ↔ risk bağlı | 10/10 | ✅ 10/10 |
| M6 | Doğrulanmış kod satırı | ≥1 | ✅ 9 (workflow dosyaları) |
| M7 | Ölçülmüş coverage hedefi | **0 (uydurma yok)** | ✅ 0 |
| M8 | Soft kapı kaydı | 2 | ✅ 2 |

**Coverage kuralı (A5)** — `firmware-test-stratejisi.md` §17 dört maddesi:

1. Coverage oranı **tek başına** kabul kriteri sayılmaz.
2. K005 kabulü M1–M8 ölçümleriyle verilir.
3. Yüzde hedefi, kapı listesindeki G-satırları geçmeden yazılmaz.
4. Eksik gate (G9) kapatılmadan nihai coverage hedefi sabitlenmez.

> **Çözülmemiş açık soru:** `testing/index.md` open_question "test kapsamı
> hedefi tanımlı değil ⚠️" bu dosyada da **açık taşınır**; M7 = 0 ölçümü
> kuralın (A5.4) uygulamasıdır, hedefin kendisi değildir. `%80` gibi bir
> rakam depoda **yoktur ve yazılmaz** (strateji §7 kural 1 · §32 satır 2).

## §6 Flaky ve Ölçüm Disiplini

| Adım | Flaky kuralı |
|---|---|
| 1 | Aynı test 2 kez arka arkaya farklı sonuç verirse `flaky` etiketi alır |
| 2 | `flaky` test soft kapıya (G6/G7) dahil edilmez, raporlanır |
| 3 | 3 farklı günde tekrarlanan flaky → gerçek hata kabul edilir, L1'e yükseltilir |
| 4 | Flaky test silinmez; deterministik hâle getirilir (sleep yerine koşul bekleme) |
| 5 | Zaman bağımlı testlerde sabit saat (clock) enjeksiyonu zorunlu |

**Disiplin kuralı (A6):** Ölçülmeden hedef sayı yazılmaz; eşik tanımdır,
ölçüm ayrıdır (`firmware-master.md` §16 disiplini). Ölçüm kalemlerinden
açılış süresi (⚠️ eşik yok), watchdog penceresi (⚠️ tasarım bekliyor),
coverage (⚠️ UNKNOWN) ve flaky geçmişi (⚠️ CI geçmişi okunmadı) bu üretimde
**ölçülmemiştir** — "tuttu/geçti" yazısı yoktur (strateji §6).

## §7 HIL — PLANNED Dürüstüğü

| Kalem | Durum |
|---|---|
| PCM3168A DAC ile gerçek çıkış ölçümü | PLANNED (donanım bekleniyor) |
| Koruma rölesi DC offset testi (>0.5V) | PLANNED |
| ASIO Exclusive Lock çok-uygulama testi | PLANNED (2 uygulama gerekir) |
| 8.1 surround kanal haritası doğrulaması | PLANNED |

**HIL kuralı (A7):** Bu dört satır hiçbir yerde **"geçti"** diye
raporlanamaz; durum sütunu `PLANNED`'dır. HIL senaryosu gerçek donanım
olmadan `✅` sayılmaz (strateji §7 kural 2 · §20). HIL ekipman arayüzü
`testing-surucu.md` §5'te, senaryo kalıpları `testing-ornekler.md`'dedir.

## §8 Anayasal İlkeler (Sert Kurallar)

| # | Kural | Kaynak | Sonuç (ihlalde) |
|---|---|---|---|
| P1 | Ölçülmeden hedef sayı yazılmaz (`%80` vb. uydurulmaz) | strateji §7 kural 1 · AGENTS §3 | ret |
| P2 | HIL senaryosu gerçek donanım olmadan `✅` sayılmaz | strateji §7 kural 2 | ret |
| P3 | CI gate'i olmayan kapı kapalı sayılmaz (G9 henüz yok) | strateji §7 kural 3 | ret |
| P4 | Soft kapı (G6 · G7) hard kapı sayılmaz; bloklama yokluğu raporlanır | strateji §7 kural 6 | ret |
| P5 | Her yeni risk için en az bir senaryo düşünülür | strateji §7 kural 5 · riskler §5 kural 5 | ret — izlenebilirlik kopar |
| P6 | Kapı/tetik referansı tahmin edilmez; dosyadan okunur (K-15) | kararlar K-15 | ret |
| P7 | Bilinmeyen değer `UNKNOWN`, doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` | AGENTS §3 · kararlar K-7 | ret |
| P8 | Kod alıntısı `Taslak:`/`Kaynak:` etiketiyle; firmware kodu olmadığından dosya/satır künyesi hiç kullanılmaz | kararlar K-15 | ret |
| P9 | Flaky test silinmez, deterministik hâle getirilir | strateji §16 kural 4 | ret |
| P10 | Bu üretimde hiçbir kapı **çalıştırılmamıştır** — çalıştırılmış gibi yazılmaz | dürüstlük (§10 satır 4) | ret |

## §9 İddia ↔ Kanıt ↔ Test ↔ Kapı Zinciri (K-15 üçlüsü)

| # | Zincir halkası | Soru | Red Team sonucu |
|---|---|---|---|
| 1 | İddia | Test edilecek kural nedir? | kanıt biçimi seçilmeden test yazılmaz |
| 2 | Kanıt biçimi | Dosya/satır · kaynak · ölçüm hangisi? | biçimi seçilemeyen iddia `⚠️` kalır ve test kapsamına **girmez** |
| 3 | Test + kapı | Geçti/kaldı + hangi G<n>? | kapı yoksa sonuç bloklanmaz, yalnız raporlanır |
| 4 | Ölçüm kaydı | M<n> → `validation:` listesi | D10 kapısı |

**Zincir kuralı (A9):** "Kanıt biçimi seçilmeden test yazılmaz" — bu
depoda firmware kodu olmadığı için firmware iddialarının kanıt biçimi
**donanım/standart referansıdır** (ör. 1 tampon = 10,67 ms), dosya/satır
künyesi değil (`firmware-test-stratejisi.md` §30).

## §10 Red Team İtiraz Kayıtları (iddia → karar)

| # | Öne sürülen iddia | Karar | Yerine yazılan |
|---|---|---|---|
| T1 | "tüm kapılar geçti" | reddedildi | G9 tanımsız · G6/G7 yumuşak |
| T2 | "coverage hedefi %80" | reddedildi | depoda hedef yok — yalnız ölçüm kuralı (M7 = 0) |
| T3 | "firmware testleri geçiyor" | reddedildi | depoda firmware testi yok (0 C dosyası) |
| T4 | "CI yeşil" | reddedildi | CI'nin gerçekten geçtiği **doğrulanmadı** → `⚠️` |
| T5 | "HIL tamam" | reddedildi | donanım yok · PLANNED |

> Bu tablo `firmware-test-stratejisi.md` §32'den aktarılmıştır; her reddedilen
> iddia, yerine ne yazıldığıyla birlikte durur (boşluk sessiz kapatılmaz).

## §11 Sorumluluk, Devir ve Çocuk Eşlemesi

| İş | MO | QA | DevOps | Embedded | Security |
|---|---|---|---|---|---|
| Strateji onayı | A | R | C | C | C |
| Gate tasarımı (G1–G9) | I | C | R | I | C |
| Senaryo yazımı (S-*) | I | R | I | C | C |
| Kod kanıtı doğrulama (K-15) | A | R | I | C | I |
| HIL ölçümü | I | C | I | R | I |

(R = sorumlu · A = onaylayıcı · C = danışılan · I = bilgilendirilen —
strateji §23 RACI.)

| Devir | Yön | Ne taşındı | Kanıt |
|---|---|---|---|
| Risk 3 (rollback 🔴, skor 15) | update → testing | HIL senaryosu (S5 · S-UPD-01) | `firmware-riskler.md` §2 · §21 |
| T1–T5 test kalemleri | kapsam → testing | unit · integration · HIL · kabul kriteri · örtü/rapor | `firmware-kapsam.md` §9.9 (hepsi dalga-3) |
| F14 Test kıyaslama (HIL) | bilesenler → testing | sabit senaryo · kabul kriteri | `firmware-bilesenler.md` §4 |
| E_TST_FAIL (H17) | bilesenler → testing | hata kodu arayüzü (taslak) | `firmware-bilesenler.md` §23 |
| D14 / F14 bağımlılığı | bagimliliklar → testing | gerçek donanım toplama bağımlılığı | `firmware-bagimliliklar.md` §17 · §20.5 |
| Terim 24 "HIL" | evrensel-dil → testing | bağlayıcı terim ataması | `firmware-evrensel-dil.md` §2 · §5 |

> **Devir boşluğu (raporlanır):** `firmware-riskler.md` §21 testing satırı
> hâlâ "yalnız index.md" der — bu üretim bu satırı **düzeltmez** (başka
> dosyaya dokunma yasağı); çelişki rapor listesindedir.

## §12 Açık Sorular (çözülmeden taşındı)

| # | Soru | Etiket | Kaynak |
|---|---|---|---|
| Q0 | **test kapsamı hedefi tanımlı değil** | `⚠️` | `testing/index.md` open_questions — KAPATILMADI, taşındı |
| Q1 | HIL fixture (donanım tezgâhı) tanımı ne zaman? | `⚠️ UNKNOWN` | strateji §8 Q1 |
| Q2 | Coverage hedefi ne? (ölçüm yok → hedef yok) | `⚠️ UNKNOWN` | strateji §8 Q2 · §11 M7 |
| Q3 | G9 firmware test workflow'u ne zaman eklenir? | `⚠️ VERIFY REQUIRED` | strateji §8 Q3 |
| Q4 | Performans eşikleri (açılış süresi · jitter) kime ait? | `⚠️ VERIFY REQUIRED` | strateji §8 Q4 |
| Q5 | G6/G7 soft kapıları ne zaman hard'a döner? | `⚠️ VERIFY REQUIRED` | strateji §8 Q5 |
| Q6 | Dalga-3 3 çocuk ne zaman yazılacak (diagnostics · security · testing)? | `⚠️ VERIFY REQUIRED` | `firmware-master.md` §10 Q1 · K-8 |

## §13 Kaynak ve Dayanak Kanıtı

| Kaynak | Bölüm | Bu dosyada kullanıldığı yer |
|---|---|---|
| `K005-firmware/boot/boot-anayasa.md` | §1–§26 (şablon) | iskelet · ELI15 · Authority satırı |
| `K005-firmware/boot/boot-surucu.md` | §2 · §20 | §10 (bayrak/test arayüzü disiplini) |
| `testing/index.md` | ELI15 · open_questions · §GRUPLAR | ELI15 · Q0 · frontmatter `group` |
| `firmware-test-stratejisi.md` | §2 · §2.1 · §3 · §4 · §5 · §6 · §7 · §8 · §11 · §14 · §16 · §17 · §20 · §21 · §23 · §25 · §28 · §29 · §30 · §32 | §2–§11 (birincil kaynak) |
| `firmware-dogrulama.md` | §3 | D1–D10 + geçiş kuralı (§3) |
| `firmware-master.md` | §2 · §9 · §10 · K-8 | §1 · §12 Q6 |
| `firmware-kapsam.md` | §5 · §9.9 · satır I11 | §1 (I11) · §11 (T1–T5) |
| `firmware-bilesenler.md` | §4 (F14) · §23 (H17) | §11 |
| `firmware-bagimliliklar.md` | §17 (D14) · §20.5 | §11 |
| `firmware-riskler.md` | §2 · §21 | §1 · §11 devir |
| `firmware-kararlar.md` | §2 (K-7 · K-15 · K-16) | §8 · §9 |
| `firmware-evrensel-dil.md` | §2 (terim 24) · §5 | §11 |
| `device-control/device-control-anayasa.md` | §1–§20 (kardeş şablon) | H1 · bölüm düzeni |
| `.github/workflows/ci.yml` · `secret-scan.yml` | tamamı (okundu 2026-10-14) | §4 G1–G8 |

> **Uydurma yok:** bu dosyada web araştırması yapılmadı — URL sayısı 0;
> repo'da firmware kodu olmadığından test kodu iddiası da yoktur.

## §14 Revizyon ve Kapanış Kontrolü

| # | Kayıt | Değer |
|---|---|---|
| V1 | Dosya | `testing/testing-anayasa.md` (K005-firmware) |
| V2 | node_id | K005-firmware/testing/testing-anayasa |
| V3 | node_type · status · children | module · draft · [] |
| V4 | group | mimari (tirnaksız) |
| V5 | parent_id | K005-firmware/testing |
| V6 | Üretim tarihi | 2026-10-14 |
| V7 | Okunan kaynak dosya sayısı | 14 (12 K005 MD + 2 workflow dosyası) |
| V8 | Web URL sayısı | 0 (araştırma yapılmadı — URL uydurulmadı) |
| V9 | Bölüm sayısı | §1 → §14 |
| V10 | Çalıştırılan kapı | 0 — hiçbir D/G kapısı bu üretimde çalıştırılmadı |

| Kontrol | Beklenti | Durum |
|---|---|---|
| FM anahtar sayısı | 10 (node_id · node_type · status · parent_id · children · depends_on · evidence · validation · open_questions · group) | evet |
| `parent:` anahtarı | bulunmamalı (yalnız `parent_id`) | evet |
| `group` değeri | tirnaksız `mimari` | evet |
| Bölüm başı tablo | her bölümde ≥1 tablo | evet |
| Coverage açık sorusu | çözülmeden taşındı (Q0 · §5) | evet |
| "geçti/uydurma hedef" iddiası | 0 | evet |
| Dosya/satır künyesi | 0 (firmware kodu yok) | evet |
| Bölüm tekrarı / dolgu | 0 | evet |
| Son satır | Authority satırı birebir tek satır | evet |


---

## §15 Piramit Doktrini — Derinletme (L1–L6 kural seti)

### §15.1 Katman başına bağlayıcı kurallar (P-L1…P-L6)

| Kural | Katman | Bağlayıcı madde |
|---|---|---|
| P-L1 | birim | her karar dalı en az bir kez yürütülür (senaryo şartnamedir — eşik yok) |
| P-L2 | bileşen | arayüz hata sınıfları (E-*) tek tek denenir |
| P-L3 | bütünleşik | modüller arası veri yolu (ts/kanıt sahipliği) korunur |
| P-L4 | sistem | zamanlama/bütçe ölçümü **ancak** ölçümlü ortamda iddia edilir |
| P-L5 | HIL | fiziksel ölçüm ekipman kaydı olmadan **geçti** yazılmaz |
| P-L6 | filo/canary | yalnız update dalının kademeli dağıtım kapısı üzerinden |

### §15.2 Piramit ihlalleri (yasak davranışlar)

1. L1 sonucunu L5 yerine koymak (yanlış katmanda ölçüm) — **yasak**.
2. L5 sonucunu L1'siz üretmek (temelsiz zirve) — kapıda **sayılmaz**.
3. Eksik katmanı gizlemek (BLOKE'u PASS göstermek) — anayasa ihlali (§13 red team).
4. Kapsam dışı katmanı ölçmek (L4'te insan faktörü) — sonuç geçersiz.

### §15.3 Katman geçiş kanıtı

- Her katman geçişi kanıt satırı üretir (dosya + satır) — testing-surucu §1.
- Geçiş kanıtsızsa üst katman **BELIRSIZ** yazılır (kapı gizlenemez).

---

## §16 D-Kapıları — Derinletme (kapı davranışı tablosu)

### §16.1 Kapı durumları (dürüstlük dili)

| Durum | Anlamı | Raporlama |
|---|---|---|
| PASS | kriter karşılandı + kanıt var | normal satır |
| FAIL | kriter karşılanmadı | normal satır + kök neden alanı |
| BLOKE | ön koşul yok (ekipman/kod/workflow) | ayrı satır — FAIL değil |
| BELIRSIZ | kriter okunamaz/kanıt eksik | ayrı satır — PASS üretilemez |
| FLAKY-PASS | tekrar-labilen geçiş | ayrı işaret — normal PASS ile karıştırılmaz |

### §16.2 Kapı değerlendirme sırası (bağlayıcı)

1. BLOKE kontrolü önce (ön koşul) → sonra kriter → sonra kanıt.
2. Kanıt yoksa sonuç **geçersiz** (BELIRSIZ) — kriter karşılasa bile.
3. Flaky sinyali varsa sonuç işaretlenir; kapı kararı bunu **ayrı** iletir.
4. Kapı sahibi olmayan ajan "geçti" **bilemez** (yalnız kanıt üretir).

### §16.3 Kapı ↔ anayasa madde eşlemesi

| Kapı durumu | Anayasa maddesi | Ek kaynak |
|---|---|---|
| BLOKE | dürüst boşluk (§13) | testing-kapsam §N.2 |
| BELIRSIZ | kanıtsız iddia yasak (§9) | testing-surucu §1 |
| FLAKY-PASS | flaky disiplini (§6) | testing-dogrulama §H.3 |
| PASS | kanıt zinciri (§9 K-15) | testing-surucu §14.1 |

---

## §17 CI Gate Doktrini — Derinletme (gerçek dosya bağlamı)

### §17.1 Bilinen kapı envanteri (2026-10-14 okuma — gerçektir)

| Workflow | Kapı işi | Durum |
|---|---|---|
| ci.yml | genel lint/test job'ları | çalışıyor (bu depoda) |
| ci.yml (js-coverage job'u) | coverage raporu | continue-on-error — zorunlu kapı değil |
| secret-scan.yml | secret taraması | çalışıyor |

### §17.2 Tanımsız firmware kapıları (dürüst kayıt)

| Kapı | Durum | Etki |
|---|---|---|
| G-firmware | workflow YOK | firmware kapısı kapalı — sonuç BELIRSIZ |
| G9 (HIL) | workflow YOK | L5 kapalı — BLOKE satırı |

1. Tanımsız kapı **gizlenmez**; raporlarda açık satır taşır.
2. Kapı tanımı yalnız firmware-test-stratejisi §3/§18'de değişir (SSOT).
3. Workflow eklenmesi DevOps işidir; testing yalnız şartname üretir (surucu §13).

---

## §18 Ölçüm Disiplini — Derinletme (ölçülmemiş hedef yasağı)

### §18.1 Ölçüm kuralları (O1–O5)

| Kural | Metin |
|---|---|
| O1 | Sayısal hedef/ölçüt yalnız ölçüm sonrası ve ADR ile sabitlenir |
| O2 | Ölçülmüş değer tahminle değiştirilemez (tahmin = ölçüm değildir) |
| O3 | Ölçüm ortamı kayıt satırında görünür (EX1–EX4 / DX1–DX4) |
| O4 | Ölçüm tekrar sayısı sabitlenmez — tekrar verisi flaky işaretine girer |
| O5 | Ölçüm yoksa kapı BELIRSIZ (PASS/FAIL yazılmaz) |

### §18.2 Ölçüm → hedef akışı (tek yönlü)

1. ölçüm (çalıştırma + kanıt) → 2. dağılım gözlemi → 3. ADR tartışması →
   4. hedef sabitleme → 5. kapı kriterine yazım (firmware-test-stratejisi).
2. Ters akış (hedef → ölçüm beklentisi) **yasak değildir** ama hedef o an
   "taslak" damgası taşır; kapı kriterine girmez.

---

## §19 HIL Doktrini — Derinletme (PLANNED dürüstlüğü)

### §19.1 HIL'in bu projedeki durumu

| Konu | Durum |
|---|---|
| G9 workflow | TANIMLI DEĞİL (firmware-test-stratejisi §3) |
| Ekipman listesi | YOK (ad uydurmak yasak) |
| HIL 5 adım planı | firmware-bagimliliklar §20.5 (şartname var, uygulama yok) |
| L5 sonucu üreten iddia | 0 (üretilemez) |

### §19.2 HIL açılış sırası (şartname — DevOps sahipliğinde)

1. Ekipman şartnamesi (testing-surucu §12.1 alanları).
2. G9 workflow tasarımı + onay (DevOps + QA).
3. HIL defteri doldurma (eq_id/capability/calibration_ref).
4. DV-T10/S-PWR-01 gibi L5 kartlarının HAZIR duruma geçişi.
5. İlk kapı değerlendirmesi (kapı BELIRSIZ'den çıkar).

---

## §20 Anayasal İlkeler — Ek Maddeler (P16–P21)

| Madde | Metin |
|---|---|
| P16 | BLOKE, FAIL değildir; ikisini karıştıran rapor **yanlış rapordur** |
| P17 | Boş bekleyen tablo (doldurulacak satır) dolgu değildir; **uydurma satır** dolgudur |
| P18 | Taslak damgasız betik/kart, uydurmadır (depoda altyapı yokken) |
| P19 | Kapı sonucu yalnız kapı sahibi tarafından verilir; agent kanıt üretir |
| P20 | Ölçüm ortamı bilinmeyen sonuç, sonuç sayılmaz (BELIRSIZ) |
| P21 | Kapıyı gizlemek (BLOKE'u saklamak) anayasa ihlalidir |

### §20.1 İlkelerin uygulama yeri

- P16/P21 → raporlama (testing-surucu §16 red team).
- P17/P18 → kart/betik üretimi (testing-ornekler §P notu).
- P19/P20 → kapı değerlendirmesi (bu dosya §16.2).

---

## §21 K-15 Zinciri — Derinletme (iddia ↔ kapı ↔ test ↔ kanıt)

### §21.1 Zincir kırılma noktaları ve tepkileri

| Kırılma | Tepki | Kapıya etkisi |
|---|---|---|
| iddia ↔ kapı eşleşmiyor | kapıda sayılır (kapsam testi yok) | FAIL/BELIRSIZ |
| kapı ↔ test eşleşmiyor | test geçersiz (kapsamsız test) | sonuç iptal |
| test ↔ kanıt eşleşmiyor | sonuç geçersiz (kanıtsız) | BELIRSIZ |
| kanıt ↔ iddia eşleşmiyor | iddia geri çekilir (append kaydı) | kapı yeniden |

### §21.2 Zincir denetimi (her üretim turunda)

1. Yeni iddia → hangi kapı? → hangi test? → hangi kanıt? (dörtlü soru).
2. Dördünden biri boşsa iddia **"taslak"** damgası taşır (kapıya girmez).
3. Denetim satırı Vault Steward defterine append edilir (kapı defteri).

---

## §22 Kapanış — Bu Ek Özeti (§15–§21)

| Ek bölüm | Ne getirdi |
|---|---|
| §15 | P-L1…P-L6 katman kuralları + ihlal listesi + geçiş kanıtı |
| §16 | Kapı durumları (5 değer) + değerlendirme sırası + madde eşlemesi |
| §17 | Gerçek workflow envanteri + tanımsız firmware kapıları kaydı |
| §18 | O1–O5 ölçüm kuralları + ölçüm→hedef akışı |
| §19 | HIL durumu + açılış sırası (PLANNED dürüstlüğü) |
| §20 | P16–P21 anayasal maddeler |
| §21 | K-15 zincir kırılma tepkileri + dörtlü denetim |

### §22.1 Son kontrol

- Mevcut §1–§14 append-only korundu · Frontmatter korundu · Dolgu/TBD: 0 ·
  Çalıştırılmış iddiası: 0 · Uydurma kapı/ad: 0 · Son satır Authority: birebir.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode