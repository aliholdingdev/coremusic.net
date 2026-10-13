---
node_id: K005-firmware/boot/boot-dogrulama
node_type: module
status: draft
parent_id: K005-firmware/boot
children: []
depends_on:
  - K005-firmware
evidence: []
validation: []
open_questions: []
group: kanit
---

# boot — Kanıt Tablosu

> **Kaynak notu:** (a) 2026-10-12 oturumunda exa ile toplanan URL'ler ·
> (b) 2026-10-12 oturumunda yapılan fetch denemeleri · (c) gerçek disk yolları ·
> (d) 2026-10-13 genişletme: PowerShell `Invoke-WebRequest -Method Head`
> ile doğrulanan yeni URL'ler ve repo shell taramaları.
> Yasak: URL, sürüm, register uydurmak; uydurulan satır sayılmaz.

## §A — Doğrulama senaryoları

| # | Senaryo | Ön koşul | Beklenen | Ölçüm yöntemi | Durum |
|---|---|---|---|---|---|
| DV-B1 | Reset → hand-off zinciri kesintisiz | Boot mediya geçerli image içeriyor | S0→S10 tüm durumlar sırayla, T1–T14 geçişleri tutarlı | Boot log adım damgaları (B15) + bütçe sayacı (B16) | Ölçüm PLANNED (donanım yok) |
| DV-B2 | ECC/parity hatası yakalanır | Test belleğinde kasıtlı hata üretilebilir | S4→S9; asla S10 | JTAG bellek yazma + boot log | UNKNOWN (ECC desteği Q5) |
| DV-B3 | Geçersiz imza boot'u reddeder | Bozuk/ imzasız image slot'a yazılmış | S7→S8; uygulama çalışmaz | Test image'ı + boot log | Tasarım seviyesi (kod yok) |
| DV-B4 | Doğrulama çağrısı hatası fail-safe | security çağrısı hata döndürüyor | S7→S9 (asla S10) | Sahte security stub | Tasarım seviyesi |
| DV-B5 | Boot tahmin etmez | Bilinmeyen çip revizyonu okunmuş | S3 en güvenli init dalını seçer | Revizyon kimliği override | Tasarım seviyesi |
| DV-B6 | Watchdog uygulama öncesi kurulur | B12 kritik yolda | S10'dan önce watchdog aktif | Watchdog sayacı okuma | Tasarım seviyesi |
| DV-B7 | Image retry sınırı | Medya okuma hatası tekrarlı | Retry → S8 (sınır aşımında) | Kasıtlı okuma hatası | Sınır tanımsız ⚠️ |
| DV-B8 | Boot bütçesi ölçümü | B16 sayacı işliyor | Reset→hand-off süresi loglanır | SysTick tabanlı sayaç | Ölçüm PLANNED |
| DV-B9 | Reset-cause sınıflandırması | Farklı reset nedenleri üretilebilir (POR/BOR/yazılım) | B6 doğru enum döndürür | Reset devresi + register okuma | UNKNOWN (register aileye bağlı) |
| DV-B10 | Bellek testi kapsamı | §L tahsislerine uygun test genişliği | Bellek hatası boot bütçesine sığar | B16 + hata enjeksiyonu | Ölçüm PLANNED |

### §A.1 Senaryo ↔ durum geçişi eşlemesi

| Senaryo | Tetiklenen geçiş | Doğrulanan koruma |
|---|---|---|
| DV-B1 | T1–T14 tamamı | K1 (kesintisiz zincir) |
| DV-B2 | T12 | K2 (ECC/parity) |
| DV-B3 | T8 (false dalı) | B8/B9 bütünlüğü |
| DV-B4 | T9 | Fail-safe kuralı |
| DV-B5 | T4 (uyuşmazlık dalı) | K5 (tahmin yok) |
| DV-B6 | S10 öncesi | K6 (watchdog) |
| DV-B7 | T7 (retry dalı) | B7 hata modu |
| DV-B8 | Tümü (yan akış) | K4 (bütçe) |
| DV-B9 | T4 | B6 hata modu |
| DV-B10 | T5 | K2 + §L bütçe |

## §B — Test stratejisi

### §B.1 Test piramidi (bu dal)

| Katman | Tip | Bu dalda kapsam | Araç (referans) | Repo durumu |
|---|---|---|---|---|
| L1 | Statik | Frontmatter/şema, bağımlılık grafiği akikliği (§D boot-kapsam) | Vault script'leri (`.ai/scripts/*.ps1`, kökten çalışır) | Mevcut (şema denetimi) |
| L2 | Tasarım incelemesi | Durum matrisi tutarlılığı (boot-kapsam §G), sınır matrisi (§I) | 20 persona turu (AGENTS §6.1) | Prosedürel |
| L3 | SIL (host) | Boot state machine'inin host'ta simülasyonu | C++ host test (fikir) — googletest/cmocka referansları §E | **YOK** — repo'da firmware kodu 0 |
| L4 | HIL | Gerçek çipte reset/verify/mem-test | JTAG/telemetri (OpenOCD/SEGGER referansları §E) | **YOK** — donanım PLANNED |
| L5 | Burn-in | Uzun süreli reset döngüsü tekrarı | Telemetri (diagnostics dalı) | **YOK** |

### §B.2 Test veri gereksinimleri

| Veri | Amaç | Üretim | Durum |
|---|---|---|---|
| Geçerli test image'ı | DV-B1正常 akış | Üretim script'i (update dalı) | Tasarım seviyesi |
| Bozuk magic image'ı | DV-B3, DV-B7 | Hex edit + yeniden imzalama | Tasarım seviyesi |
| İmzasız image | DV-B3 | İmzalama atlanarak paketleme | Tasarım seviyesi |
| Bellek hata paterni | DV-B2, DV-B10 | JTAG write (HIL) | UNKNOWN (donanım) |
| Kasıtlı clock sapması | DV-B1 clock doğrulama | Clock devresi manipülasyonu | UNKNOWN (donanım) |
| Farklı reset nedeni katalogu | DV-B9 | Reset devresi (K001) | UNKNOWN (donanım) |

### §B.3 Pass/BLOCK kapıları

| Kapı | Kriter | Karar |
|---|---|---|
| G-1 | Frontmatter şeması 10 alan + group | Şablon dışı → BLOCK |
| G-2 | Durum matrisi (§G) tutarsız geçiş içermez | Tutarlılık ihlali → BLOCK |
| G-3 | Hata kodu sözlüğü diagnostics ile çakışmıyor | Çakışma → BLOCK (numara alanı) |
| G-4 | Uydurma register adresi/URL/satır numarası yok | Tespit → BLOCK (zero-hallucination) |
| G-5 | Ölçüm iddiası ölçüm kanıtıyla | Ölçüm kanıtsız → iddia "PLANNED" olarak kalır |

## §C — Ölçüm metodolojisi

### §C.1 Boot zaman ölçümü (K4, DV-B8)

| Adım | Yöntem | Kesinlik | Kaynak |
|---|---|---|---|
| 1 | B16 sayacı SysTick'e bağlanır (boot başında enable) | Tick çözünürlüğü | CMSIS `SysTick` sembolleri (§E satır E1) |
| 2 | Her durum geçişinde (T1–T14) sayaç okunur | Adım başına | Boot log (B15) |
| 3 | Reset→hand-off toplamı = son okuma − ilk okuma | Toplam süre | Boot log |
| 4 | Sonuç sartname penceresi (10.67 ms) ile karşılaştırılır | Bütçe uyumu | Sartname (olcum PLANNED) |

**Not:** 512 sample / 48 kHz = 10.666… ms — bu pencere *ses tamponu*
penceresidir; boot süresinin bu pencereye sığması zorunlu değildir ama
**ölçülmesi** zorunludur (K4). Aşım kalıcıysa §L boot-kapsam öncelik sırası.

### §C.2 Bellek sağlığı ölçümü (DV-B2, DV-B10)

| Adım | Yöntem | Sınır | Kaynak |
|---|---|---|---|
| 1 | Bellek test algoritması seçimi (March vs pattern) | Seçim yapılmadı ⚠️ | Literatür referansı — somut algoritma kararı yok |
| 2 | Test genişliği tahsis edilen bütçeye sığar | §L boot-kapsam | Boot bütçesi |
| 3 | Kasıtlı hata enjeksiyonu (HIL, JTAG) | Donanım gerekli | OpenOCD/SEGGER (§E) |
| 4 | ECC/parity exception sayacı | Donanım destekli | UNKNOWN (Q5) |

### §C.3 İmza doğrulama ölçümü (DV-B3, DV-B4)

| Adım | Yöntem | Beklenen | Kaynak |
|---|---|---|---|
| 1 | Geçerli image → doğrulama | true | security dalı arayüzü |
| 2 | Bozuk hash → doğrulama | false | Hash: SHA-2 ailesi (RFC 6234, §E satır E4) |
| 3 | İmzasız image → doğrulama | false | Algoritma seçimi security dalının |
| 4 | Çağrının kendisi hata → fail-safe | S9 (asla S10) | Boot-kapsam §G T9 |

### §C.4 Log/telemetri doğrulaması (DV-B1, DV-B8)

| Adım | Yöntem | Beklenen | Kaynak |
|---|---|---|---|
| 1 | Boot log kanalı hazır (JTAG/UART) | Adım kayıtları okunur | OpenOCD/SEGGER (§E) |
| 2 | Her T-geçişi için bir kayıt | 14 geçiş × kayıt | Boot-kapsam §G.2 |
| 3 | Bütçe sayacı son okuması | Reset→hand-off süresi | §C.1 |
| 4 | Log yoksa | Teşhis kör → kapı G-4 ihlali değil ama K4 kapanamaz | Boot-kapsam RSK-8 |

## §D — Referans değerler ve eşikler

| # | Değer | Sayı | Kaynak | Durum |
|---|---|---|---|---|
| 1 | Ses tamponu | 512 sample | Sartname | ✅ sabit |
| 2 | Örnekleme hızı | 48 kHz | Sartname | ✅ sabit |
| 3 | Tampon penceresi | 512/48000 = 10.67 ms (10.666… ms) | Hesap (1 ve 2'den) | ✅ türetilmiş |
| 4 | Veri biçimi | 32-bit float | ADR-017 | ✅ sabit |
| 5 | DAC | PCM3168A (PCM5122 yasak) | ADR-038 H001 | ✅ sabit |
| 6 | Boot bütçesi | ⚠️ tanımsız (ölçüm PLANNED) | — | UNKNOWN |
| 7 | Bellek test süresi tahsisi | ⚠️ §L taslak; somut değer yok | — | UNKNOWN (ölçüm) |
| 8 | Watchdog timeout | ⚠️ tanımsız | — | UNKNOWN |
| 9 | Retry sınırı | ⚠️ tanımsız | — | UNKNOWN |
| 10 | Hash algoritması (boot) | Öneri: SHA-256; seçim security dalının | RFC 6234 (§E E4) | ⚠️ seçim bekliyor |
| 11 | İmza algoritması | ⚠️ RSA-2048 / ECDSA-P256 karşılaştırması yapılmadı | OpenSSL man sayfaları (§E) | UNKNOWN (seçim) |
| 12 | Reset-cause enum değer seti | ⚠️ çip ailesine bağlı | — | UNKNOWN (Q5) |

### §D.1 Eşik tablosu (kapılar)

| Eşik | Değer | Aşım davranışı | Kayıt |
|---|---|---|---|
| Boot bütçe (log eşiği) | 10.67 ms (tampon penceresi) | Loglanır, durdurulmaz | K4 |
| Bellek testi hatası | 1 hata bile | S9 (asla S10) | K2 |
| İmza doğrulama | bool eşik (true/false) | false → S8 | K3 |
| Doğrulama çağrısı hatası | Herhangi bir hata | S9 (fail-safe) | §G T9 |
| Mojibake/dolgu | 0 | BLOCK (görev kapısı G-4) | Zero-hallucination |

## §E — Web araştırma tablosu

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| Zephyr toolchains kılavuzu, cross-compile araç zinciri yapılandırmasını belgeler — boot derleme/araç zinciri doğrulaması referansı | https://docs.zephyrproject.org/latest/develop/toolchains/index.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| Zephyr Nucleo-F401RE board dokümanı, bir ARM Cortex-M4 board'ının boot/yükleme yapısını örnekler — HIL doğrulama referansı | https://docs.zephyrproject.org/latest/boards/st/nucleo_f401re/doc/index.html | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| IETF RFC 1071, internet protokollerinde 16-bit birim tümleyen (checksum) hesap yöntemini tanımlar — boot bütünlük kontrollerinde checksum referansı | https://www.rfc-editor.org/rfc/rfc1071 | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| SEGGER J-Link sayfası, JTAG/SWD debug probe teknolojisini belgeler — DV-B2/HIL bellek yazma-doğrulama aracı referansı | https://www.segger.com/products/debug-probes/j-link/ | 2026-10-13 | ✅ HTTP 200 (HEAD) |
| IETF RFC 1321, MD5 özet algoritmasının spesifikasyonudur — bu dosyada MD5 *yalnızca tarihsel referans* olarak listelenir; CoreMusic'te MD5/SHA-1/DES kullanımı yasaktır (global mühendislik standardı) | https://www.rfc-editor.org/rfc/rfc1321 | 2026-10-13 | ✅ HTTP 200 (HEAD) — yasak algoritma referansı, kullanım değil |

## §F — İddia tablosu (2026-10-12 kaydı — korunmuştur)

| # | İddia | URL / Dosya | Tarih | Durum |
|---|---|---|---|---|
| 1 | XU316 datasheet'i: 8 KB OTP "application boot code" için, JTAG programlama/yükleme ve cycle-accurate XTC simülatörü belgelenmiş | https://www.xmos.com/download/XU316-1024-xcore_ai-Datasheet(2_0_0).pdf/ | 2026-10-12 | ✅ doğrulandı |
| 2 | XU316-1024 serisi xcore.ai sayfasında dört package (QF60A/B, FB265, TQ128) ve datasheet listesi var | https://www.xmos.com/xcore-ai | 2026-10-12 | ✅ doğrulandı |
| 3 | Görev manifesti URL'si: `https://www.xmos.com/processors/xu316` | https://www.xmos.com/processors/xu316 | 2026-10-12 | ⚠️ bu oturumda **HTTP 406** (exa + webfetch); ikincil kanıt: CLAUDE.md §Web Verification Status (2026-10-07 ✅) |
| 4 | Repo web doğrulama kaydı: "XMOS XU316 · USB Audio SoC · ✅ xmos.com" | `CLAUDE.md` §Web Verification Status (2026-10-07) | 2026-10-12 | ✅ dosya okundu |
| 5 | MCUboot, primary/secondary slot ile boot eder; test-swap → "OK" işaretlenmezse sonraki boot'ta **revert** (geri dönme) yapar | https://docs.mcuboot.com/design.html | 2026-10-12 | ✅ doğrulandı |
| 6 | MCUboot image doğrulaması: magic, `image_tlv_info`, SHA256 TLV eşleşmesi ve opsiyonel imza TLV kontrolü | https://docs.mcuboot.com/design.html | 2026-10-12 | ✅ doğrulandı |
| 7 | USB DFU 1.1 spesifikasyonu firmware yükseltmesi için **4 faz** tanımlar; DFU sırasında cihaz normal işini durdurur | https://usb.org/sites/default/files/DFU_1.1.pdf | 2026-10-12 | ✅ doğrulandı |
| 8 | NIST SP 800-193: boot/critical data için Protection · Detection · Recovery üçlüsü; RTU (Root of Trust for Update) boot zincirinin parçasıdır | https://csrc.nist.gov/pubs/sp/800/193/final | 2026-10-12 | ✅ doğrulandı |
| 9 | Çocuk rolü = "İlk kod, kendini tanıma ve yükleme sırası" | `firmware-master.md` §3 satır 57 | 2026-10-12 | ✅ dosya okundu |
| 10 | boot ↔ runtime ↔ device-control sınırı açık soru Q3 | `firmware-master.md` §7 Q3 · `boot/index.md` open_questions | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 11 | boot ↔ update sınırı (DFU/recovery) vault'ta yazılmamış | `firmware-bagimliliklar.md` §3 · §4/3 (Q4) | 2026-10-12 | ⚠️ VERIFICATION REQUIRED |
| 12 | CoreMusic K005 boot firmware kodu repo'da mevcut | `shared/src/**` shell taraması | 2026-10-12 | **UNKNOWN** — 0 dosya (kanıt yok) |
| 13 | CoreMusic'in boot mediya'sı (flash/OTP) ve yükleme aracı | — | 2026-10-12 | **UNKNOWN** (Q5 · T9) |
| 14 | CoreMusic boot sırasının süre/adım tanımı (timing budget) | — | 2026-10-12 | **UNKNOWN** (kanıt yok) |
| 15 | MCUboot/XU316'nın CoreMusic'te kullanıldığı | — | 2026-10-12 | **UNKNOWN** — örnek referanstır, seçim değil |

> Not (2026-10-13): §F satırları envanterdeki (1441 URL) kaynaklardır; bu
> dosyanın **yeni** §E satırları envanterle kesişmez (kesişim = 0).

## §G — Repo kanıt tablosu (2026-10-13 shell taraması)

| # | İddia | Gerçek dosya | Kanıt metodu | Durum |
|---|---|---|---|---|
| 1 | Repo'da firmware/boot C/C++ kodu yok (`.cpp/.h/.hpp/.c/.ino` = 0; vendor/node_modules/third_party hariç) | `C:\www\coremusic.net` recursive tarama | `Get-ChildItem -Recurse -Include` | ✅ ölçüldü — **kod kanıtı 0** |
| 2 | Firmware agent routing satırı mevcut: `'dsp-fw' => ['domain' => 'firmware', 'layer' => 'fw']` | `shared/src/AI/AIOrchestrator.php` satır 36 | `Select-Object -Index (36-1)` | ✅ doğrulandı |
| 3 | Fault önceliği kritik → `dsp-fw` agent: `'fault' => ['agent' => 'dsp-fw', 'priority' => 'critical']` | `shared/src/AI/AIOrchestrator.php` satır 45 | `Select-Object -Index (45-1)` | ✅ doğrulandı |
| 4 | `RuntimeBootstrap.php` mevcut (21 satır) — adı "bootstrap" olsa da PHP host başlatmasıdır, firmware boot'u değil | `shared/src/Bootstrap/RuntimeBootstrap.php` | Tam dosya okuma (21 satır) | ✅ doğrulandı — sınır notu korunur |
| 5 | CI workflow dosyaları mevcut (2 dosya: ci.yml, secret-scan.yml) | `.github/workflows/` | Dizin listeleme | ✅ ölçüldü |
| 6 | `shared/tests` altında 44 PHP test dosyası var (PHP test altyapısı mevcut, firmware test yok) | `shared/tests/**` | Recursive `-Filter *.php` sayımı | ✅ ölçüldü |
| 7 | ci.yml yorum satırları PHP/Node test adımlarını belgeler (test, stan, js-test, js-coverage, js-e2e) | `.github/workflows/ci.yml` satır 1–12 | `-TotalCount 12` okuma | ✅ doğrulandı |

## §H — Sayım

| Metrik | Değer |
|---|---|
| §F toplam satır (2026-10-12 kaydı) | 15 |
| §F URL satırı / benzersiz URL | 7 / 6 (5 ve 6 aynı URL'yi paylaşır) |
| §F gerçek dosya yolu satırı | 5 (4 · 9 · 10 · 11 · 12) |
| §F ✅ doğrulandı | 8 (1 · 2 · 4 · 5 · 6 · 7 · 8 · 9) |
| §F ⚠️ VERIFICATION REQUIRED | 3 (3 · 10 · 11) |
| §F UNKNOWN | 4 (12 · 13 · 14 · 15) |
| §E yeni URL (2026-10-13) | 5 benzersiz, HTTP 200 HEAD, envanter kesişimi 0 |
| §A doğrulama senaryosu | 10 (DV-B1…DV-B10) |
| §G repo kanıtı | 7 satır (4 ✅ doğrulama + 2 ölçüm + 1 okuma) |
| §D referans değer | 12 (4 sabit · 1 türetilmiş · 7 UNKNOWN/⚠️) |

**Geçiş kuralı:** §F satır 10–11 üst kararla (Q3 · Q4), 13–14 donanım hedefi
kararıyla (Q5 · T9) kapanır; 15, K005 seçim kararı verilmeden ✅ olmaz.
§G satır 1 (kod kanıtı 0), ilk firmware commit'inde güncellenir.

## §I — Geçiş ve kapanış koşulları

| Kapanacak | Koşul | Sahip | Bağımlı senaryo |
|---|---|---|---|
| DV-B1 ölçüm | Donanım + boot log kanalı | embedded-engineer | K4 |
| DV-B2 | ECC destekli çip kararı (Q5) | architect | K2 |
| DV-B7 sınırı | Retry sınırı tasarım kararı | architect | RSK-7 |
| §D 6–9 (UNKNOWN) | Ölçüm / tasarım kararı | performance-engineer | §L boot-kapsam |
| §D 10–11 (algoritma) | security dalı kararı | security-engineer | DV-B3 |
| §D 12 (reset-cause) | Çip ailesi (Q5) | embedded-engineer | DV-B9 |

## §J — İleri doğrulama senaryoları (2026-10-13 genişletme)

§A'daki 10 senaryonun üzerine, boot zincirinin keşfedilmemiş yüzlerini
kapsayan 8 ek senaryo. Hepsi **tasarım seviyesindedir** — repo'da
firmware kodu 0 olduğu için (§G satır 1) hiçbiri çalıştırılamaz;
senaryolar donanım ve kod geldiğinde §B.1 piramidinin L3/L4 katmanında
işletilecektir.

| # | Senaryo | Ön koşul | Beklenen | Ölçüm yöntemi | Durum |
|---|---|---|---|---|---|
| DV-B11 | Power-fail anında boot yazma | Boot sırasında güç kesintisi üretilebilir | S5→S9; eski image kalır (A/B slot) | Güç kesme devresi + tekrar-boot logu | Tasarım seviyesi |
| DV-B12 | Rollback (geri alma) | İki image slotu (A/B) mevcut | Yeni image başlatılamazsa eski image'a dön | Sahte bozuk image + boot log | Tasarım seviyesi |
| DV-B13 | Rollback bayrağı | Test-swap sonrası "OK" yazılmazsa | Sonraki boot'ta revert (MCUboot dokümanı §F satır 5) | Bayrak okuma + boot log | Tasarım seviyesi |
| DV-B14 | Boot-timeclock doğrulama | Saat kaynağı boot'ta hazır değilse | Oscillator stabilini bekleme döngüsü; erken geçiş yasak | Saat-sapma ölçümü (JTAG) | UNKNOWN (donanım) |
| DV-B15 | Yığın/IRQ önceliği boot'ta | IRQ priority boot sırasında değişebiliyor | Boot sırasında kritik IRQ en yüksek öncelikte | IRQ priority register okuma | Tasarım seviyesi |
| DV-B16 | OTP/boot-lock | Boot-lock yazıldıktan sonra | Boot yeniden yazılamaz (salt-okunur) | Yeniden-yazma denemesi + OTP lock bit | Tasarım seviyesi |
| DV-B17 | Boot sırasında sürücü bağımlılığı | Boot'un dış sürücüye bağımlılığı varsa | Bağımlılık boot öncesi çözülür; boot içinde bekleme yok | Bağımlılık grafiği incelemesi | Tasarım seviyesi |
| DV-B18 | Boot sonrası bellek sızıntısı | Boot belleği runtime'a devrediyor | Boot'ta tahsis edilen her blok runtime'da serbest bırakılabilir | Bellek-sızıntısı taraması (HIL) | UNKNOWN (donanım) |

### §J.1 Senaryo ↔ durum geçişi eşlemesi (devamı)

| Senaryo | Tetiklenen geçiş | Doğrulanan koruma |
|---|---|---|
| DV-B11 | T6 (image doğrulama) | K3 (bütünlük) + A/B slot |
| DV-B12 | T8 (retry → fallback) | K3 (rollback) |
| DV-B13 | T11 (test-swap) | K3 (bayrak) |
| DV-B14 | T2 (clock init) | K1 (clock zinciri) |
| DV-B15 | T3 (IRQ init) | K6 (watchdog + IRQ) |
| DV-B16 | T10 (boot-lock) | K2 (OTP) |
| DV-B17 | T1 (bağımlılık çözümü) | K1 (bağımsız zincir) |
| DV-B18 | S10 (runtime'a hand-off) | K7 (bellek sahipliği) |

## §K — Test kapsamı genişletme

### §K.1 Test senaryosu ↔ ekipman eşlemesi

| Senaryo grubu | Ekipman | Maliyet | Öncelik |
|---|---|---|---|
| DV-B1, B8 (boot zaman) | UART + osiloskop | Düşük | Yüksek |
| DV-B2, B10 (bellek) | JTAG + test çipi | Orta | Yüksek |
| DV-B3, B4 (imza) | Test image'ı + hash aracı | Düşük | Orta |
| DV-B11, B12, B13 (güç/rollback) | Güç kesme devresi + A/B slot | Orta | Yüksek |
| DV-B14 (clock) | Saat analizörü | Orta | Düşük |
| DV-B15, B16 (IRQ/OTP) | JTAG + register okuma | Düşük | Orta |
| DV-B17 (bağımlılık) | Statik analiz (bağımlılık grafiği) | Düşük | Yüksek |
| DV-B18 (bellek sızıntısı) | HIL + bellek-tarama aracı | Yüksek | Orta |

### §K.2 Test verisi genişletme

| Veri | Senaryo | Üretim yöntemi | Durum |
|---|---|---|---|
| A/B slot test image'ları | DV-B12, DV-B13 | İki ayrı image + slot switch | Tasarım seviyesi |
| Güç-kesme testi | DV-B11 | Güç kesme devresi + tekrar-boot | Tasarım seviyesi |
| Clock-sapma paterni | DV-B14 | Saat manipülasyonu | UNKNOWN (donanım) |
| IRQ priority okuma | DV-B15 | JTAG register dump | Tasarım seviyesi |
| Boot-lock bit okuma | DV-B16 | OTP lock + register okuma | Tasarım seviyesi |
| Bağımlılık grafiği | DV-B17 | Statik analiz (dependency graph) | Tasarım seviyesi |
| Bellek-sızıntısı raporu | DV-B18 | HIL + valgrind-benzeri (embedded) | UNKNOWN (donanım) |

## §L — Boot-kapsam referans genişletme

boot-dogrulama.md bu bölümde boot-kapsam.md'deki §L bölümünün kapılarını
tek tabloda toplar:

| Kapı | §L kaynağı | Kapanış biçimi | Kapı durumu |
|---|---|---|---|
| Boot bütçesi | boot-kapsam §L | Donanım ölçümü (DV-B8) | AÇIK |
| Bellek testi kapsamı | boot-kapsam §L | Donanım testi (DV-B10) | AÇIK |
| İmza doğrulama | boot-kapsam §L | İmza testi (DV-B3, B4) | AÇIK |
| Rollback kapısı | boot-kapsam §L | A/B slot testi (DV-B12, B13) | AÇIK |
| Clock bağımsızlığı | boot-kapsam §L | Clock testi (DV-B14) | AÇIK |
| Boot-lock | boot-kapsam §L | OTP testi (DV-B16) | AÇIK |

**Kapı kuralı:** altı kapı da AÇIK'tır — bu bölüm kapıları yalnız tarif
eder; kapanış §K'daki ekipman ve test verisiyle olur.

## §M — Ölçüm metodolojisi genişletme

### §M.1 Boot-timeclock ölçümü (DV-B14)

| Adım | Yöntem | Sınır | Kaynak |
|---|---|---|---|
| 1 | Clock stabilini bekleme süresi ölçülür | Datasheet'e göre | Çip datasheet'i (UNKNOWN) |
| 2 | Clock init geçişi (T2) zaman damgası | Boot log (B15) | Boot log |
| 3 | Erken geçiş tespiti | Clock hazır olmadan geçiş varsa H1 | Boot log |
| 4 | Toplam clock-init süresi | Boot bütçesine sığar | §C.1 |

### §M.2 IRQ/OTP doğrulaması (DV-B15, DV-B16)

| Adım | Yöntem | Beklenen | Kaynak |
|---|---|---|---|
| 1 | IRQ priority register okuma (boot sırasında) | Kritik IRQ en yüksek | JTAG register okuma |
| 2 | Boot-lock bit okuma | Boot-lock yazıldıktan sonra | OTP register |
| 3 | Boot-lock sonrası yeniden-yazma denemesi | Yazma başarısız (salt-okunur) | Boot-lock bit |
| 4 | Boot-lock boot'tan sonra mı yazıldı? | Evet — boot'tan önce değil | Boot log + OTP bit |

### §M.3 Bellek-sızıntısı ölçümü (DV-B18)

| Adım | Yöntem | Sınır | Kaynak |
|---|---|---|---|
| 1 | Boot'ta tahsis edilen blok sayısı | 0 sızıntı | HIL + bellek-tarama |
| 2 | Runtime'a devredilen blok sayısı | Blok sayısı eşleşir | Bellek-sahiplik tablosu |
| 3 | Runtime'da serbest bırakılan blok | 0 sızıntı | Bellek-tarama |
| 4 | Boot'ta kalan blok | Boot sonrası 0 kalmalı | Bellek-tarama |

## §N — Doğrulama kapıları genişletme

### §N.1 Test kapısı genişletme

| Kapı | Kriter | Karar |
|---|---|---|
| G-6 | DV-B11-B18 senaryoları §B.1 piramidine uygun | Piramit dışı → BLOCK |
| G-7 | Test verisi §K.2'de eksiksiz | Veri eksik → BLOCK |
| G-8 | Ekipman §K.1'de tanımlı | Ekipman tanımsız → BLOCK |
| G-9 | Boot-timeclock ölçüm adımları §M.1'de | Adım eksik → BLOCK |

### §N.2 Kapı ↔ senaryo eşlemesi

| Kapı | Doğrulanan senaryo |
|---|---|
| G-6 | §J tamamı (DV-B11-B18) |
| G-7 | §K.2 (test verisi) |
| G-8 | §K.1 (ekipman) |
| G-9 | §M.1 (clock ölçümü) |

## §O — Sayım genişletme (2026-10-13)

| Metrik | Değer |
|---|---|
| §J ileri senaryo | 8 (DV-B11…DV-B18) |
| §J↔§G eşleme | 8 satır |
| §K.1 test↔ekipman | 8 satır |
| §K.2 test↔veri | 7 satır |
| §L boot-kapsam kapısı | 6 kapı (tamamı AÇIK) |
| §M.1 clock ölçümü | 4 adım |
| §M.2 IRQ/OTP ölçümü | 4 adım |
| §M.3 bellek-sızıntısı ölçümü | 4 adım |
| §N.1 test kapısı | 4 kapı (G-6…G-9) |
| §N.2 kapı↔senaryo | 4 satır |

**Geçiş kuralı (§O):** §J'deki 8 senaryonun tamamı tasarım seviyesindedir;
repo'da firmware kodu 0 olduğu için (§G satır 1) hiçbiri çalıştırılamaz.
§M'deki 12 adımın hiçbiri donanım olmadan ölçülemez; adımlar yalnız
tariftir ve donanım geldiğinde işletilir. §N'deki 4 kapı da AÇIK'tır —
kapılar boot-dogrulama'nın iç kapısıdır, boot-kapsam §L kapılarını
ezmez (§L kapıları bağımsızdır).

## §P — Dürüstlük bloğu (derinleştirme eki 2)

Bu ek (§J-§P) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 URL, 0 repo iddiası, 0 ölçüm sonucu, 0 sürüm sayısı
eklenmiştir; yalnız ileri doğrulama senaryoları (§J), test kapsamı
(§K), boot-kapsam referans genişletmesi (§L), ölçüm adımları (§M),
doğrulama kapıları (§N) ve sayım genişletmesi (§O) eklenmiştir.
§A-§I'daki mevcut senaryolar, test stratejisi, ölçüm metodolojisi ve
sayım aynen korunur. §F'deki 15 satırın 4'ü UNKNOWN olarak kalır
(§F satır 12-15 — firmware kodu, boot mediyası, süre/adım tanımı,
MCUboot/XU316 seçimi); bu ek UNKNOWN'ları kapatmaz. §G'deki 7
repo kanıtı satırı aynen korunur ve firmware kodu gelmeden güncellenmez.
§H'deki sayım aynen korunur — bu ek §O'yu ekler, §H'yi değiştirmez.

## §Q — Boot-dogrulama ↔ boot-anayasa referans genişletme

boot-anayasa.md'deki kapılar bu bölümde tek tabloda toplanır:

| Kapı | boot-anayasa kaynağı | Kapanış biçimi | Kapı durumu |
|---|---|---|---|
| Boot-zinciri kesintisizliği | boot-anayasa §5 | DV-B1 (donanım) | AÇIK |
| Reset-cause doğru okuma | boot-anayasa §6 | DV-B9 (donanım) | AÇIK |
| Boot bütçesi | boot-anayasa §7 | DV-B8 (donanım) | AÇIK |
| Bellek testi bütçesi | boot-anayasa §8 | DV-B10 (donanım) | AÇIK |
| İmza doğrulama kapısı | boot-anayasa §9 | DV-B3, B4 (tasarım) | AÇIK |
| Rollback kapısı | boot-anayasa §10 | DV-B12, B13 (tasarım) | AÇIK |
| Boot-lock | boot-anayasa §11 | DV-B16 (tasarım) | AÇIK |

**Referans kuralı:** yedi kapı da AÇIK'tır; bu tablo boot-anayasa'nın
kapılarını tarif eder, ekleme yapmaz — kapılar boot-anayasa'da tanımlıdır
ve bu dosyanın iç kapısı değildir (§N.1'deki G-6…G-9 ise bu dosyanın iç
kapısıdır; ikisi farklı katmandadır).

## §R — Boot-dogrulama ↔ runtime-surucu referans genişletme

runtime-surucu.md'deki gözlem kapıları bu bölümde toplanır:

| Kapı | runtime-surucu kaynağı | Kapanış biçimi | Kapı durumu |
|---|---|---|---|
| Görev süresi bütçesi | runtime-surucu §15.1 | Donanım ölçümü | AÇIK |
| Kesme oranı bütçesi | runtime-surucu §15.2 | Donanım ölçümü | AÇIK |
| Yığın tepe-noktası | runtime-surucu §15.3 | Boot+çalışma ölçümü | AÇIK |
| Köprü-sayaç doğruluğu | runtime-surucu §19.2 | Çift-alan ölçümü | AÇIK |
| Güç geçiş kapıları | runtime-surucu §14 | Cihaz-testi | AÇIK |
| Kurtarma izi | runtime-surucu §20 | Watchdog tetikleme testi | AÇIK |

**Referans kuralı:** altı kapı da AÇIK'tır; bu tablo runtime-surucu'nun
kapılarını tarif eder, ekleme yapmaz. runtime-surucu bu kapıların
tarifini taşır; ölçüm sonucu buraya değil boot-dogrulama §G/§H'ye
yazılır (iki-dosya kuralı: tarif runtime-surucu'da, ölçüm burada).

## §S — Geçiş ve kapanış koşulları genişletme (2026-10-13)

§I'daki 6 kapanış koşulunun üzerine 8 ek koşul:

| Kapanacak | Koşul | Sahip | Bağımlı senaryo |
|---|---|---|---|
| DV-B11 (güç-kesme) | A/B slot tasarımı + güç kesme devresi | embedded-engineer | K3 (bütünlük) |
| DV-B12 (rollback) | İki image slotu (A/B) mevcut | embedded-engineer | K3 (rollback) |
| DV-B13 (bayrak) | Test-swap bayrağı tasarımı | embedded-engineer | K3 (bayrak) |
| DV-B14 (clock) | Clock-init bekleme döngüsü tasarımı | embedded-engineer | K1 (clock) |
| DV-B15 (IRQ) | IRQ priority register okuması | embedded-engineer | K6 (watchdog) |
| DV-B16 (OTP) | Boot-lock bit tasarımı | security-engineer | K2 (OTP) |
| DV-B17 (bağımlılık) | Bağımlılık grafiği | architect | K1 (bağımsız zincir) |
| DV-B18 (bellek-sızıntısı) | Bellek-sahiplik tablosu | embedded-engineer | K7 (bellek) |

**Kapanış kuralı:** sekiz koşul da AÇIK'tır — bu ek koşulları yalnız
tarif eder; kapanış donanım ve kod geldiğinde olur (§G satır 1 güncellenir).

## §T — Dürüstlük bloğu (derinleştirme eki 3)

Bu ek (§Q-§T) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 URL, 0 repo iddiası, 0 ölçüm sonucu, 0 sürüm sayısı
eklenmiştir; yalnız boot-anayasa kapıları (§Q), runtime-surucu kapıları
(§R) ve geçiş koşulları genişletmesi (§S) eklenmiştir. §A-§P'deki
mevcut içerik aynen korunur. §Q'daki 7 kapı boot-anayasa'da tanımlıdır
ve bu dosyanın iç kapısı değildir; §R'deki 6 kapı runtime-surucu'da
tanımlıdır ve ölçüm sonucu buraya değil boot-dogrulama §G/§H'ye yazılır
(iki-dosya kuralı). §N.1'deki G-6…G-9 ise bu dosyanın iç kapısıdır —
üç farklı katman (boot-anayasa / runtime-surucu / boot-dogrulama iç)
birbirine karıştırılmaz.

## §U — Boot-dogrulama ↔ update-surucu referans genişletme

update-surucu.md'deki güncelleme kapıları bu bölümde toplanır:

| Kapı | update-surucu kaynağı | Kapanış biçimi | Kapı durumu |
|---|---|---|---|
| Image yazma bütçesi | update-surucu §6 | Donanım ölçümü | AÇIK |
| İmza doğrulama | update-surucu §7 | İmza testi (DV-B3) | AÇIK |
| Rollback tetikleme | update-surucu §8 | A/B slot testi (DV-B12) | AÇIK |
| Boot-lock yazma | update-surucu §9 | OTP testi (DV-B16) | AÇIK |
| DFU 4-faz akışı | update-surucu §10 | DFU testi (USB DFU 1.1) | AÇIK |

**Referans kuralı:** beş kapı da AÇIK'tır; bu tablo update-surucu'nun
kapılarını tarif eder, ekleme yapmaz. DFU 4-faz akışı USB DFU 1.1
spesifikasyonuna bağlıdır (§F satır 7 — ✅ doğrulandı); DFU sırasında
cihaz normal işini durdurur ve bu durma boot-dogrulama'da da
kaydedilir (S10→S8→S9 geçişleri).

## §V — Geçiş ve kapanış koşulları — update-surucu genişletme (2026-10-13)

| Kapanacak | Koşul | Sahip | Bağımlı senaryo |
|---|---|---|---|
| update-surucu §6 (image yazma) | Donanım + image yazma arayüzü | embedded-engineer | K3 (bütünlük) |
| update-surucu §7 (imza) | security dalı kararı | security-engineer | DV-B3 |
| update-surucu §8 (rollback) | A/B slot tasarımı | embedded-engineer | DV-B12 |
| update-surucu §9 (boot-lock) | Boot-lock bit tasarımı | security-engineer | DV-B16 |
| update-surucu §10 (DFU) | USB DFU test ekipmanı | devops-engineer | DV-B1 (USB DFU) |

**Kapanış kuralı:** beş koşul da AÇIK'tır — bu ek koşulları yalnız
tarif eder; kapanış donanım ve kod geldiğinde olur.

## §W — Sayım genişletme 2 (2026-10-13)

| Metrik | Değer |
|---|---|
| §Q boot-anayasa kapısı | 7 kapı (tamamı AÇIK) |
| §R runtime-surucu kapısı | 6 kapı (tamamı AÇIK) |
| §S geçiş koşulu (genişletme) | 8 satır (tamamı AÇIK) |
| §U update-surucu kapısı | 5 kapı (tamamı AÇIK) |
| §V update-surucu geçiş koşulu | 5 satır (tamamı AÇIK) |

**Geçiş kuralı (§W):** §Q-§V'deki 31 satırın (7+6+8+5+5) tamamı
tasarım seviyesindedir; repo'da firmware kodu 0 olduğu için (§G satır 1)
hiçbiri çalıştırılamaz. Kapıların kapanışı donanım ve kod geldiğinde
olur; §H'deki sayım aynen korunur — bu ek §W'yu ekler, §H'yi değiştirmez.

## §X — Dürüstlük bloğu (derinleştirme eki 4)

Bu ek (§U-§X) kanıt oranını DEĞİŞTİRmez — yeni içerik yeni kanıt
değildir: 0 URL, 0 repo iddiası, 0 ölçüm sonucu, 0 sürüm sayısı
eklenmiştir; yalnız update-surucu kapıları (§U), update-surucu geçiş
koşulları (§V) ve sayım genişletmesi 2 (§W) eklenmiştir. §A-§T'deki
mevcut içerik aynen korunur. §U'daki 5 kapı update-surucu'da tanımlıdır
ve bu dosyanın iç kapısı değildir. §F satır 7'deki USB DFU 1.1 ✅
doğrulanmıştır (2026-10-12) — bu ek DFU iddiasını değiştirmez, yalnız
DFU'nun update-surucu ile ilişkisini tarif eder.

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
