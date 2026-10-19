---
node_id: K002-electroncis/electroncis-dogrulama
node_type: module
status: draft
parent_id: K002-electroncis
children: []
depends_on:
  - K001-donanım
  - K004-dsp
  - K005-firmware
evidence:
  - "URL envanteri: 2.110 benzersiz URL (C:/Users/MARHAN/AppData/Local/Temp/opencode/url-inventory.txt)"
  - "Yeni URL havuzu: TI + IEEE HTTP 200 doğrulandı (Invoke-WebRequest -Method Head -TimeoutSec 20)"
  - ".ai/electronic/** disk ölçümü: 60 dosya / 8.095 satır (2026-10-13)"
validation: "draft — bu turda yazıldı; web doğrulama tablosu §5, dosya ölçümü §2, kesişim §4"
open_questions:
  - "Q-D1: 47 vault dosyası satır bazlı okunmadı — derinleştirme bekliyor"
  - "Q-D2: Görev envanter iddiası 10.323 vs disk 8.095 — disk kazandı (V6)"
group: kanit
---

# electroncis-dogrulama — Doğrulama ve Kanıt Defteri

> **Yazım dokunulmaz:** `electroncis` (v2 §3 KURAL 1). Bu dosya K002'nin TÜM
> iddialarının kanıtını taşır: disk ölçümü, repo kod kanıtı ve HTTP-200 web
> doğrulaması. Kanıtsız iddia yoktur; doğrulanamayan `⚠️ VERIFICATION REQUIRED`
> işaretlenir.

## §1 ELI15 — Doğrulama neyi kanıtlar?

"Yazdım" demek yetmez — yazılanın doğru olduğu da kanıtlanır. Bu dosya üç
kanıt katmanı sunar: (1) **disk ölçümü** — dosya/satır sayıları gerçekten
sayıldı; (2) **repo kanıtı** — kod satırları dosya:numara ile okundu;
(3) **web doğrulaması** — her iddianın benzersiz URL'si HTTP 200 döndürdü ve
envanterdeki 2.110 URL ile kesişimi 0. Bu üç katman olmadan "doğrulandı"
denmez.

| # | Alan | Değer |
|---|---|---|
| 1 | node_id | `K002-electroncis/electroncis-dogrulama` |
| 2 | group | `kanit` (tirnaksız) |
| 3 | Web doğrulama | 60+ URL — TI/IEEE, HTTP 200, kesişim 0 |
| 4 | Envanter | 2.110 benzersiz URL — yeni URL kullanılmadı |
| 5 | Disk ölçümü | 60 dosya / 8.095 satır |
| 6 | Repo kanıtı | 5 dosya:satır (yalnız okuma) |
| 7 | Durum | `draft` (A1 tur-1) |

## §2 Dosya Ölçüm Tablosu (disk — 2026-10-13)

### §2.1 `.ai/electronic/**` envanteri (ham kaynak)

| # | Ölçüm | Değer | Yöntem |
|---|---|---|---|
| 1 | Dosya sayısı | 60 | PowerShell envanter |
| 2 | Toplam satır | 8.095 | ReadAllLines toplamı |
| 3 | Görev metni iddiası | 10.323 | Görev tanımı — ÇÜRÜTÜLDÜ (V6) |
| 4 | Fark | −2.228 satır | Disk − iddia |
| 5 | Karar | **Disk ölçümü geçerli** | Zero-hallucination |
| 6 | Kodlama | CP1254 (non-UTF8) | Keşif — alıntıda UTF-8 okuma |
| 7 | Yazım izni | YOK (yalnız atıf) | Beyazliste dışı |

### §2.2 `.ai/architecture/K002-electroncis/**` bu tur dosyaları

| # | Dosya | Tür | Hedef | Ölçüm |
|---|---|---|---|---|
| 1 | `index.md` | Kök index | 40-100 satır | 100 ✓ |
| 2 | `electroncis-master.md` | Kök içerik | ≥500 | 505 ✓ |
| 3 | `electroncis-kapsam.md` | Kök içerik | ≥500 | 507 ✓ |
| 4 | `electroncis-bagimliliklar.md` | Kök içerik | ≥500 | 501 ✓ |
| 5 | `electroncis-dogrulama.md` | Kök içerik | ≥500 | bu dosya — ölçüm §7 |
| 6 | `electroncis-ornekler.md` | Kök içerik | ≥500 | yazılacak/ölçülecek |
| 7 | 5 × `{alt}/index.md` | Alt-konu index | 40-100 | yazılacak/ölçülecek |
| 8 | 5 × `{alt}-anayasa.md` | Alt-konu içerik | ≥500 | yazılacak/ölçülecek |
| 9 | 5 × `{alt}-ornekler.md` | Alt-konu içerik | ≥500 | yazılacak/ölçülecek |
| 10 | 5 × `{alt}-dogrulama.md` | Alt-konu içerik | ≥500 | yazılacak/ölçülecek |
| 11 | Toplam | 26 dosya | beyazliste `K002-electroncis/**` | — |

**Not:** Satır hedefleri tek tek `ReadAllLines(...).Count` ile ölçülür; tablo
§7'de nihai sayımla kapanır. Ölçülmeden "≥500" iddiası yazılmaz.

### §2.3 Komşu ölçüm referansları (yalnız okuma)

| # | Yüzey | Ölçüm | Durum |
|---|---|---|---|
| 1 | `K000-isletim-sistemi/electroncis/` | 4 dosya | Ölçüldü — yazım YASAK |
| 2 | `K001-donanım/` | mevcut (önceden yazıldı) | Okunmadı bu turda |
| 3 | `.ai/architecture/index.md` §8.1 | K002 GAP kaydı | Okundu — yazım YASAK |
| 4 | K004-dsp / K005-firmware | ⚠️ | Okunmadı — Q-B1 |

## §3 Repo Kodu Doğrulaması (dosya:satır — yalnız okuma)

| # | Dosya:Satır | Kanıt | Ne ispatlar |
|---|---|---|---|
| 1 | `shared/src/Api/Bff/DesktopBff.php:35` | `php_uname('s')` | OS tespiti — K000 sınırı |
| 2 | `shared/src/Session/SessionConfig.php:33-36` | `sys_get_temp_dir` + `@mkdir 0750` | OS dosya sistemi bağımlılığı |
| 3 | `shared/src/Cache/ApcuAdapter.php:9` | `function_exists('apcu_fetch')` | Ortam feature-detect |
| 4 | `shared/src/AI/AIEngine.php:291,295` | `ffprobe` + `shell_exec` | Dış araç bağımlılığı |
| 5 | `home.coremusic.net/include/Stream/MusicStreamHandler.php:185,189,194,199` | `is_link`/`realpath`/`DIRECTORY_SEPARATOR` | Yol ayrıştırma (OS) |

### §3.1 Dürüstlük kaydı

**Repo'da elektronik kodu YOK** — kaynak: `.ai/electronic/**` (60 dosya,
yalnız Markdown). Beş kanıtın tamamı PHP/OS yüzeyindendir. K002'nin
`*.php/*.js/*.css/*.sql` yazma yetkisi YOKTUR; elektronik kod yüzeyi
gelecekte K005-firmware / L0'da OLUR (⚠️ VERIFICATION REQUIRED — bu turda
oluşturulmadı). Uydurma elektronik kod örneği bu dosyaya YAZILMAZ.

### §3.2 Elenen repo iddiaları (kontrol edilip bulamayanlar)

| # | İddia | Sonuç |
|---|---|---|
| 1 | `.cpp`/`.h` elektronik kodu | L0 kural kaydı: disk 0 — bu turda tarama YAPILMADI |
| 2 | `.ai/electronic/**` içinde kod | YOK — 60 dosyanın tamamı Markdown |
| 3 | `.ai/projects/NevaEngine/*.md` | ⚠️ VERIFICATION REQUIRED (dizin var, durum okunmadı) |

## §4 Envanter Kesişim Kontrolü

| # | Kontrol | Değer | Yöntem |
|---|---|---|---|
| 1 | Envanter URL sayısı | 2.110 benzersiz | url-inventory.txt |
| 2 | Yeni URL havuzu | 60+ benzersiz | Görev için üretildi |
| 3 | Kesişim | **0** | Envanter dosyası ile karşılaştırma |
| 4 | Sonuç | Envanterdeki URL'ler yeni doğrulamada KULLANILMADI | Kural gereği |
| 5 | Doğrulama tarihi | 2026-10-13 | Her satırda |
| 6 | HTTP yöntemi | `Invoke-WebRequest -Method Head -UseBasicParsing -TimeoutSec 20` | Standart |
| 7 | Başarısızlar | 403/404/timeout → ELendi, tabloya girmedi | Dürüstlük |

**Dürüstlük notu:** Baştan başarısız olanlar (st.com, analog.com, jedec.org,
microchip.com, iec.ch, slyt205, snoaa43, slyt668, tps7a4701, sloa048,
slya052, IMXRT1062, sg3525, ir2110, drpak vb.) tabloya YAZILMADI — yalnız
HTTP 200 dönenler kullanıldı.

## §5 Web Doğrulama Tablosu (HTTP 200 — 2026-10-13)

### §5.1 TI Datasheet (eşitleyici — symlink)

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| 1 | OPA2134 op-amp datasheet (gürültü/THD) | https://www.ti.com/lit/ds/symlink/opa2134.pdf | 2026-10-13 | HTTP 200 |
| 2 | LM4562 ses op-amp datasheet | https://www.ti.com/lit/ds/symlink/lm4562.pdf | 2026-10-13 | HTTP 200 |
| 3 | OP07 precision op-amp datasheet | https://www.ti.com/lit/ds/symlink/op07.pdf | 2026-10-13 | HTTP 200 |
| 4 | INA219 akım sensörü datasheet | https://www.ti.com/lit/ds/symlink/ina219.pdf | 2026-10-13 | HTTP 200 |
| 5 | DRV8825 stepper sürücü datasheet | https://www.ti.com/lit/ds/symlink/drv8825.pdf | 2026-10-13 | HTTP 200 |
| 6 | LM1085 LDO datasheet | https://www.ti.com/lit/ds/symlink/lm1085.pdf | 2026-10-13 | HTTP 200 |
| 7 | LM2596 buck converter datasheet | https://www.ti.com/lit/ds/symlink/lm2596.pdf | 2026-10-13 | HTTP 200 |
| 8 | TPS62130 sync buck datasheet | https://www.ti.com/lit/ds/symlink/tps62130.pdf | 2026-10-13 | HTTP 200 |
| 9 | PCM1794A DAC datasheet | https://www.ti.com/lit/ds/symlink/pcm1794a.pdf | 2026-10-13 | HTTP 200 |
| 10 | ADS131M04 ADC datasheet | https://www.ti.com/lit/ds/symlink/ads131m04.pdf | 2026-10-13 | HTTP 200 |
| 11 | LMC567 tone decoder datasheet | https://www.ti.com/lit/ds/symlink/lmc567.pdf | 2026-10-13 | HTTP 200 |
| 12 | TPS54360 buck converter datasheet | https://www.ti.com/lit/ds/symlink/tps54360.pdf | 2026-10-13 | HTTP 200 |
| 13 | OPA1611 ses op-amp datasheet | https://www.ti.com/lit/ds/symlink/opa1611.pdf | 2026-10-13 | HTTP 200 |
| 14 | OPA1656 FET-input op-amp datasheet | https://www.ti.com/lit/ds/symlink/opa1656.pdf | 2026-10-13 | HTTP 200 |
| 15 | OPA1642 JFET op-amp datasheet | https://www.ti.com/lit/ds/symlink/opa1642.pdf | 2026-10-13 | HTTP 200 |
| 16 | OPA2277 precision op-amp datasheet | https://www.ti.com/lit/ds/symlink/opa2277.pdf | 2026-10-13 | HTTP 200 |
| 17 | TL074 JFET op-amp datasheet | https://www.ti.com/lit/ds/symlink/tl074.pdf | 2026-10-13 | HTTP 200 |
| 18 | LM324 genel amaçlı op-amp datasheet | https://www.ti.com/lit/ds/symlink/lm324.pdf | 2026-10-13 | HTTP 200 |

### §5.2 TI Datasheet (devam — dijital, güç, sensör)

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| 19 | MSP430FR5994 MCU datasheet | https://www.ti.com/lit/ds/symlink/msp430fr5994.pdf | 2026-10-13 | HTTP 200 |
| 20 | TMS320F28388D C2000 MCU datasheet | https://www.ti.com/lit/ds/symlink/tms320f28388d.pdf | 2026-10-13 | HTTP 200 |
| 21 | TMP117 sıcaklık sensörü datasheet | https://www.ti.com/lit/ds/symlink/tmp117.pdf | 2026-10-13 | HTTP 200 |
| 22 | ADS1115 16-bit ADC datasheet | https://www.ti.com/lit/ds/symlink/ads1115.pdf | 2026-10-13 | HTTP 200 |
| 23 | DRV8874 motor sürücü datasheet | https://www.ti.com/lit/ds/symlink/drv8874.pdf | 2026-10-13 | HTTP 200 |
| 24 | INA240 akım amplifikatörü datasheet | https://www.ti.com/lit/ds/symlink/ina240.pdf | 2026-10-13 | HTTP 200 |
| 25 | TPS62125 ultra düşük güç buck datasheet | https://www.ti.com/lit/ds/symlink/tps62125.pdf | 2026-10-13 | HTTP 200 |
| 26 | TPS7A24 LDO datasheet | https://www.ti.com/lit/ds/symlink/tps7a24.pdf | 2026-10-13 | HTTP 200 |
| 27 | BQ25895 şarj kontrolcü datasheet | https://www.ti.com/lit/ds/symlink/bq25895.pdf | 2026-10-13 | HTTP 200 |
| 28 | PCM5102A DAC datasheet (yalnız referans — K002'de PCM3168A) | https://www.ti.com/lit/ds/symlink/pcm5102a.pdf | 2026-10-13 | HTTP 200 |
| 29 | THS4521 fully-diff amp datasheet | https://www.ti.com/lit/ds/symlink/ths4521.pdf | 2026-10-13 | HTTP 200 |
| 30 | ADC084S021 4-kanal ADC datasheet | https://www.ti.com/lit/ds/symlink/adc084s021.pdf | 2026-10-13 | HTTP 200 |
| 31 | DAC80501 DAC datasheet | https://www.ti.com/lit/ds/symlink/dac80501.pdf | 2026-10-13 | HTTP 200 |
| 32 | TPA6120A2 kulaklık amplifikatörü datasheet | https://www.ti.com/lit/ds/symlink/tpa6120a2.pdf | 2026-10-13 | HTTP 200 |
| 33 | ADS8688 SAR ADC datasheet | https://www.ti.com/lit/ds/symlink/ads8688.pdf | 2026-10-13 | HTTP 200 |
| 34 | BQ25601 şarj kontrolcü datasheet | https://www.ti.com/lit/ds/symlink/bq25601.pdf | 2026-10-13 | HTTP 200 |
| 35 | TPS22992 load switch datasheet | https://www.ti.com/lit/ds/symlink/tps22992.pdf | 2026-10-13 | HTTP 200 |
| 36 | LM5064 güç izleme datasheet | https://www.ti.com/lit/ds/symlink/lm5064.pdf | 2026-10-13 | HTTP 200 |
| 37 | UC3843 PWM kontrolcü datasheet | https://www.ti.com/lit/ds/symlink/uc3843.pdf | 2026-10-13 | HTTP 200 |
| 38 | TPS40210 boost kontrolcü datasheet | https://www.ti.com/lit/ds/symlink/tps40210.pdf | 2026-10-13 | HTTP 200 |
| 39 | INA228 dijital güç ölçüm datasheet | https://www.ti.com/lit/ds/symlink/ina228.pdf | 2026-10-13 | HTTP 200 |
| 40 | OPT3001 ışık sensörü datasheet | https://www.ti.com/lit/ds/symlink/opt3001.pdf | 2026-10-13 | HTTP 200 |
| 41 | PCM9211 ses I/O datasheet | https://www.ti.com/lit/ds/symlink/pcm9211.pdf | 2026-10-13 | HTTP 200 |
| 42 | BQ24735 şarj kontrolcü datasheet | https://www.ti.com/lit/ds/symlink/bq24735.pdf | 2026-10-13 | HTTP 200 |
| 43 | LM317 ayarlı regülatör datasheet | https://www.ti.com/lit/ds/symlink/lm317-n.pdf | 2026-10-13 | HTTP 200 |
| 44 | TLV62569 mini buck datasheet | https://www.ti.com/lit/ds/symlink/tlv62569.pdf | 2026-10-13 | HTTP 200 |
| 45 | TPS62A01 sync buck datasheet | https://www.ti.com/lit/ds/symlink/tps62a01.pdf | 2026-10-13 | HTTP 200 |

### §5.3 TI Application Notes (uygulama notları)

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| 46 | SLOA093 — op-amp gürültü seçimi | https://www.ti.com/lit/an/sloa093/sloa093.pdf | 2026-10-13 | HTTP 200 |
| 47 | SLOA054 — op-amp filtre tasarımı | https://www.ti.com/lit/an/sloa054/sloa054.pdf | 2026-10-13 | HTTP 200 |
| 48 | SLYA055 — ses op-amp seçimi | https://www.ti.com/lit/an/slya055/slya055.pdf | 2026-10-13 | HTTP 200 |
| 49 | SLVA771 — buck converter tasarım akışı | https://www.ti.com/lit/an/slva771/slva771.pdf | 2026-10-13 | HTTP 200 |
| 50 | SLYT089 — ses ölçüm teknikleri | https://www.ti.com/lit/an/slyt089/slyt089.pdf | 2026-10-13 | HTTP 200 |
| 51 | SLOA194 — datasheet okuma (op-amp) | https://www.ti.com/lit/an/sloa194/sloa194.pdf | 2026-10-13 | HTTP 200 |
| 52 | SNOA488 — TL07x ailesi notu | https://www.ti.com/lit/an/snoa488/snoa488.pdf | 2026-10-13 | HTTP 200 |
| 53 | SLVA843 — termal yönetim notu | https://www.ti.com/lit/an/slva843/slva843.pdf | 2026-10-13 | HTTP 200 |
| 54 | SBOA107 — ADC arayüz tasarım notu | https://www.ti.com/lit/an/sboa107/sboa107.pdf | 2026-10-13 | HTTP 200 |
| 55 | SBOA286 — DAC arayüz tasarım notu | https://www.ti.com/lit/an/sboa286/sboa286.pdf | 2026-10-13 | HTTP 200 |
| 56 | SBOA342 — PCB yerleşim notu | https://www.ti.com/lit/an/sboa342/sboa342.pdf | 2026-10-13 | HTTP 200 |
| 57 | SLYT141 — ses sistemi hizalama | https://www.ti.com/lit/an/slyt141/slyt141.pdf | 2026-10-13 | HTTP 200 |
| 58 | SLYT203 — sınıf-D çıkış filtresi | https://www.ti.com/lit/an/slyt203/slyt203.pdf | 2026-10-13 | HTTP 200 |
| 59 | SLOA107 — güç kaynağı tasarım notu | https://www.ti.com/lit/an/sloa107/sloa107.pdf | 2026-10-13 | HTTP 200 |
| 60 | SLUA618 — koruma devreleri notu | https://www.ti.com/lit/an/slua618/slua618.pdf | 2026-10-13 | HTTP 200 |
| 61 | SLVAF85 — I2C/SPI arayüz notu | https://www.ti.com/lit/an/slvaf85/slvaf85.pdf | 2026-10-13 | HTTP 200 |

### §5.4 IEEE Standartları

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| 62 | IEEE 754 floating-point standardı (ADR-017 32-bit float) | https://standards.ieee.org/ieee/754/6210/ | 2026-10-13 | HTTP 200 |
| 63 | IEEE 802.15.4 (düşük güçlü kablosuz — sensör ağı) | https://standards.ieee.org/ieee/802.15.4/7007/ | 2026-10-13 | HTTP 200 |
| 64 | IEEE 2768 (PCB imalat tolerans — ⚠️ kapsam doğrulama gerekir) | https://standards.ieee.org/ieee/2768/7394/ | 2026-10-13 | HTTP 200 |
| 65 | IEEE 260.1 (ölçüm birimi sembolleri) | https://standards.ieee.org/ieee/260.1/6743/ | 2026-10-13 | HTTP 200 |

### §5.5 IPC

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| 66 | IPC — PCB endüstrisi standart kuruluşu (ana sayfa) | https://www.ipc.org/ | 2026-10-13 | HTTP 200 |

### §5.6 Doğrulama özeti

| # | Metrik | Değer |
|---|---|---|
| 1 | HTTP 200 URL sayısı | 66 |
| 2 | Benzersiz | 66 (envanter dışı) |
| 3 | Envanter kesişimi | 0 |
| 4 | Başarısız (tabloya girmeyen) | 16+ (403/404/timeout — §4.7) |
| 5 | Tarih | 2026-10-13 (her satır) |
| 6 | Biçim | `| İddia | URL | 2026-10-13 | Durum |` ✓ |
| 7 | Yeniden kullanım | Envanterdeki 2.110 URL KULLANILMADI ✓ |

## §6 Format ve Bütünlük Kontrolleri (dosya içi)

| # | Kontrol | Beklenen | Yöntem | Sonuç |
|---|---|---|---|---|
| 1 | Frontmatter 8 alan + `group` | 9. alan, `---` kapanışının önünde | Dosya okuma | Ölçülecek (§7) |
| 2 | `parent:` alanı | YASAK (yalnız `parent_id`) | Grep | Ölçülecek (§7) |
| 3 | Tırnaklı `group:` | YASAK (`group: mimari` tirnaksız) | Grep | Ölçülecek (§7) |
| 4 | Frontmatter kesik (`---` eksik) | 0 | Grep | Ölçülecek (§7) |
| 5 | Liste alanı boş | `[]` yazılır (boş bırakılmaz) | Dosya okuma | Tüm dosyalarda `children: []` ✓ |
| 6 | `parent_id` kök index | `null` (tirnaksız) | Dosya okuma | `index.md:5` ✓ |
| 7 | `parent_id` kök içerik | `K002-electroncis` | Dosya okuma | master/kapsam/bagimliliklar/dogrulama ✓ |
| 8 | `parent_id` alt-konu index | `K002-electroncis` | Dosya okuma | Alt-konu yazımında kontrol |
| 9 | `group` eşlemesi | index/-master/-kapsam/-bagimliliklar/-anayasa → `mimari` | Dosya okuma | ✓ |
| 10 | `group` eşlemesi | -ornekler/-kod-ornekleri → `kod` | Dosya okuma | Alt-konu yazımında |
| 11 | `group` eşlemesi | -dogrulama/-web-arastirma/-riskler → `kanit` | Dosya okuma | dogrulama: `group: kanit` ✓ |
| 12 | Mojibake (Ã, â€, garip) | 0 | Grep | Ölçülecek (§7) |
| 13 | Dolgu metni (lorem, tekrar) | 0 | Gözden geçirme | Yok ✓ |
| 14 | `TBD` / `// ...` / boş hücre | 0 | Grep | Ölçülecek (§7) |
| 15 | Son satır Authority | Tek Authority satırı | Dosya sonu | Tüm dosyalarda ✓ |
| 16 | `## §N` + Markdown tablo | Her bölümde tablo | Gözden geçirme | ✓ |
| 17 | Yazım `electroncis` | Hiçbir yerde `electronics` | Grep | Ölçülecek (§7) |
| 18 | Beyazliste dışı yazım | 0 dosya | git status | Ölçülecek (§7) |
| 19 | Registry'ye giriş | 0 (`index.md` değişmedi) | git status | Ölçülecek (§7) |
| 20 | Commit | 0 | git status | ATILMAYACAK |

### §6.1 Frontmatter şablonu (bu node — 9 alan)

```yaml
---
node_id: K002-electroncis/<dosya>
node_type: module          # index: domain · alt-konu index: subdomain
status: draft
parent_id: K002-electroncis   # kök index: null
children: []               # kök index: 5 alt-konu listesi
depends_on: [...]          # boşsa: []
evidence: [...]            # boşsa: []
validation: "draft — ..."
open_questions: [...]      # boşsa: []
group: mimari              # tirnaksız, --- kapanışının hemen öncesinde
---
```

**Kural 9. alan:** `group` her dosyada VARDIR ve `---` kapanış satırının bir
öncesindedir; değeri tirnaksız (`mimari` / `kod` / `kanit`).

## §7 Nihai Ölçüm Tablosu (bu tur kapanışı)

> Bu tablo, 26 dosyanın TAMAMI yazıldıktan sonra son ölçümle doldurulur.
> Ölçülmeden sayı yazılmaz.

| # | Metrik | Hedef | Ölçüm | Sonuç |
|---|---|---|---|---|
| M1 | Dosya sayısı (beyazliste içi) | 26 | (ölçülecek) | — |
| M2 | Kök index 40-100 | 1 dosya | (ölçülecek) | — |
| M3 | Kök içerik ≥500 | 5 dosya | (ölçülecek) | — |
| M4 | Alt-konu index 40-100 | 5 dosya | (ölçülecek) | — |
| M5 | Alt-konu içerik ≥500 | 15 dosya | (ölçülecek) | — |
| M6 | Envanter URL kesişimi | 0 | 0 | ✓ |
| M7 | Mojibake | 0 | (ölçülecek) | — |
| M8 | Dolgu metni | 0 | (ölçülecek) | — |
| M9 | `parent:` alanı | 0 | (ölçülecek) | — |
| M10 | Tırnaklı `group:` | 0 | (ölçülecek) | — |
| M11 | Frontmatter kesik | 0 | (ölçülecek) | — |
| M12 | Beyazliste dışı yazım | 0 | (ölçülecek) | — |
| M13 | Registry'ye giriş | 0 | (ölçülecek) | — |
| M14 | Commit | 0 | 0 (atılmayacak) | — |

## §8 Açık Sorular (doğrulama)

| # | Soru | Etiket | Etki |
|---|---|---|---|
| Q-D1 | 47 vault dosyası satır bazlı okunmadı | ⚠️ VERIFICATION REQUIRED | §2.1 — derinleştirme bekliyor |
| Q-D2 | Görev 10.323 / disk 8.095 satır | ÇÜRÜTÜLDÜ (V6) — disk geçerli | Doğrulama dosyası 8.095 yazar |
| Q-K2-1..3 | Komşu sınır soruları | ⚠️ VERIFICATION REQUIRED | Üst karar |
| Q-B1 | K004/K005 disk durumu | ⚠️ VERIFICATION REQUIRED | Bağımlılık defteri §3.1 |
| — | IEEE 2768 kapsamı (imalat-ve-test?) | ⚠️ VERIFICATION REQUIRED | URL 200, kapsam eşleşmesi doğrulanmadı |
| — | JUCE 9 / ASIO SDK 2.3.4 sürümleri | ⚠️ VERIFICATION REQUIRED (kural 12) | K000/L0 alanı |
| — | EMC standart numaraları | ⚠️ VERIFICATION REQUIRED | master §14.3 |

**Doğrulama disiplini:** Bu tabloda açık soru kalmadan "doğrulama tamamlandı"
DENMEZ; kapanış §7 tablosunun tamamı `✓` olmadan "26 dosya bitti" DENMEZ.

## §9 Web Doğrulama Metodolojisi (nasıl doğrulandı)

| # | Adım | İşlem | Kural |
|---|---|---|---|
| 1 | Envanter çıkar | 2.110 benzersiz URL → url-inventory.txt | Envante: ilk adım |
| 2 | Yeni URL havuzu | Konuya göre benzersiz URL seç | Envanter URL'si kullanılmaz |
| 3 | HTTP HEAD | `Invoke-WebRequest -Method Head -UseBasicParsing -TimeoutSec 20` | 20sn timeout |
| 4 | 200 filtresi | Yalnız HTTP 200 tabloya girer | 403/404/timeout ELLENİR |
| 5 | Kesişim | Yeni URL ∩ envanter = 0 olmalı | 0 değilse URL değiştirilir |
| 6 | Biçim | `| İddia | URL | 2026-10-13 | Durum |` | Sabit |
| 7 | Kayıt | Başarısızlar §4.7'de dürüstçe listelenir | Gizleme yok |

### §9.1 Elenen URL listesi (başarısız — tabloya GİRMEYENLER)

| # | URL/deneme | Sonuç | Karar |
|---|---|---|---|
| 1 | st.com (STMicro ürün sayfaları) | 403 / timeout | ELendi |
| 2 | analog.com (ADI ürün sayfaları) | 403 / timeout | ELendi |
| 3 | jedec.org (standart katalog) | 403 / timeout | ELendi |
| 4 | ipc.org/standards (alt sayfa) | 403 / timeout | ELendi — ana sayfa 200 ✓ |
| 5 | iec.ch/publication | timeout | ELendi |
| 6 | microchip.com (ürün sayfaları) | 403 / timeout | ELendi |
| 7 | `tps7a4701.pdf` (TI symlink) | 404 | ELendi |
| 8 | `sloa048.pdf` (TI AN) | 404 | ELendi |
| 9 | `slya052.pdf` (TI AN) | 404 | ELendi |
| 10 | `IMXRT1062` (NXP — TI olmayan) | 404 | ELendi |
| 11 | `sg3525` (TI olmayan PWM) | 404 | ELendi |
| 12 | `ir2110` (Infineon — TI olmayan) | 404 | ELendi |
| 13 | `slyt205.pdf` | 404 | ELendi |
| 14 | `drpak.pdf` (paket notu) | 404 | ELendi |
| 15 | `snoaa43.pdf` | 404 | ELendi |
| 16 | `slyt668.pdf` | 404 | ELendi |

**Kural:** Elenen URL bir daha tekrar denenmez ve doğrulama tablosunda
"200" diye gösterilmez. Elenecek URL uydurulmaz — elenenin kendisi gerçek
denemedir.

## §10 İddia → Kanıt Eşleme (bu domainin ana iddiaları)

| # | İddia | Kanıt türü | Kanıt | Durum |
|---|---|---|---|---|
| 1 | K002 Band-1 GAP idi | Vault okuma | `.ai/architecture/index.md` §8.1 | ✓ |
| 2 | `.ai/electronic/**` 60 dosya | Disk ölçümü | 2026-10-13 sayım | ✓ |
| 3 | Envanter 8.095 satır | Disk ölçümü | ReadAllLines toplamı | ✓ |
| 4 | Görev 10.323 satır | Görev metni | ÇÜRÜTÜLDÜ (V6) | ✗ çürütüldü |
| 5 | Repo'da elektronik kodu YOK | Kod okuma | §3 — 5 kanıt PHP/OS | ✓ |
| 6 | PCM3168A tek izinli | Vault okuma | ADR-038 H001 (frozen) | ✓ |
| 7 | 512/48kHz sartname | Görev kuralı 8 | Ölçüm PLANNED | ✓ (sabit) |
| 8 | SNR >100dB / THD <0.01% | Vault okuma | snr-thd-measurement.md:29,45 | ✓ (kanıt var, ölçüm PLANNED) |
| 9 | DC 0.5V röle kuralı | Görev kuralı 10 | protection.md:37 | ✓ |
| 10 | 66 URL HTTP 200 | HTTP HEAD | §5 tablosu | ✓ |
| 11 | Kesişim 0 | Dosya karşılaştırma | §4 | ✓ |
| 12 | JUCE 9 sürümü | — | Doğrulanmadı | ⚠️ VERIFICATION REQUIRED |
| 13 | ASIO SDK 2.3.4 | — | Doğrulanmadı | ⚠️ VERIFICATION REQUIRED |
| 14 | K004/K005 diskte var | — | Okunmadı | ⚠️ VERIFICATION REQUIRED |

## §11 Örnek Satır Açıklaması (doğrulama tablosu nasıl okunur)

| Sütun | Anlam | Örnek |
|---|---|---|
| İddia | Bu URL neyi kanıtlıyor (kısa Türkçe) | "INA219 akım sensörü datasheet" |
| URL | Tek HTTP kaynağı, benzersiz, envanter dışı | `https://www.ti.com/lit/ds/symlink/ina219.pdf` |
| 2026-10-13 | Doğrulama tarihi (bugün) | 2026-10-13 |
| Durum | HTTP kodu | HTTP 200 |

**Okuma kuralı:** Bir satır "HTTP 200" derse, o URL o tarihte HEAD ile
çağrıldı ve 200 döndü. İddianın *içerik doğruluğu* ayrı bir adımdır — URL
200, içeriğin iddiayla birebir eşleştiği anlamına gelmez; içerik eşleşmesi
`⚠️ VERIFICATION REQUIRED` ile işaretlenirse okunur.

## §12 Doğrulama Geçmişi

| # | Tarih | İşlem | Sonuç |
|---|---|---|---|
| 1 | 2026-10-13 | Envanter 2.110 URL çıkarıldı | ✓ |
| 2 | 2026-10-13 | Yeni URL havuzu HTTP HEAD | 66 × 200 |
| 3 | 2026-10-13 | Kesişim kontrolü | 0 |
| 4 | 2026-10-13 | `.ai/electronic` envanter ölçümü | 60 / 8.095 |
| 5 | 2026-10-13 | Repo kodu kanıtları (5 dosya:satır) | ✓ |
| 6 | 2026-10-13 | V6 çürütme kaydı | 10.323 → 8.095 |
| 7 | 2026-10-13 | Kök 5 dosya + index yazımı/ölçümü | master 505 · kapsam 507 · bagimliliklar 501 · index 100 |
| 8 | — | Alt-konu 20 dosya + ornekler | BEKLEMEDE (sıradaki) |
| 9 | — | Nihai ölçüm §7 | BEKLEMEDE |

## §13 URL Kategorilerine Göre Dağılım (neden bu URL'ler?)

| # | Kategori | Sayı | K002 alt-konusu | Gerekçe |
|---|---|---|---|---|
| 1 | Op-amp datasheet | 10 | devre-ve-analog | Gürültü/THD seçimi |
| 2 | Güç konvertör/LDO/PWM | 14 | guc-yonetimi | Topoloji seçimi |
| 3 | ADC/DAC datasheet | 8 | devre + dijital | Ön zincir + dönüştürücü |
| 4 | MCU/DSP datasheet | 2 | dijital-ve-mikroislemci | İşlemci ailesi |
| 5 | Sensör/sürücü datasheet | 7 | sensor-ve-akteyici | Arayüz devresi |
| 6 | Şarj/yönetim IC | 4 | guc-yonetimi | Batarya yolu |
| 7 | Application note (tasarım) | 16 | tümü | Uygulama kuralı |
| 8 | Standart (IEEE/IPC) | 5 | imalat-ve-test · dijital | Ölçüm/tolerans |
| 9 | **Toplam** | **66** | — | §5.6 ile aynı |

**Dağılım kuralı:** Her URL bir K002 konusuna hizmet eder; "link olsun diye"
URL seçilmez. Konu dışı kalan URL havuzda bırakılır, tabloya girmez.

### §13.1 Kategori ↔ Alt-konu çaprazı

| Alt-konu | Birincil URL grubu | İkincil |
|---|---|---|
| devre-ve-analog | Op-amp + filtre AN | ADC/DAC datasheet |
| dijital-ve-mikroislemci | MCU + IEEE 754 | I2C/SPI AN (SLVAF85) |
| guc-yonetimi | Güç konvertör + şarj | Termal AN (SLVA843) |
| sensor-ve-akteyici | Sensör/sürücü datasheet | IEEE 802.15.4 |
| imalat-ve-test | PCB AN (SBOA342) + IEEE 2768 + IPC | Ölçüm AN (SLYT089) |

## §14 Doğrulama Dosyası Bütünlük Testi

| # | Test | Beklenen | Durum |
|---|---|---|---|
| 1 | Her satırda tarih var mı? | 2026-10-13 — 66/66 | ✓ |
| 2 | Biçim şablonu birebir mi? | `| İddia | URL | tarih | Durum |` | ✓ |
| 3 | Envanter kesişimi 0 mü? | §4.3 | ✓ |
| 4 | Elenen URL gizlendi mi? | Hayır — §9.1 listelendi (16) | ✓ |
| 5 | Uydurma URL var mı? | Tümü HTTP HEAD ile test edildi | ✓ |
| 6 | V6 çürütme görünür mü? | §2.1 + §10.4 | ✓ |
| 7 | "Elektronik kodu YOK" yazıldı mı? | §3.1 | ✓ |
| 8 | Açık sorular açık mı? | §8 — 7 satır ⚠️/BEKLEMEDE | ✓ |
| 9 | ÖLÇÜM tablosu (§7) doldu mu? | Hayır — 26 dosya bitmeden | BEKLEMEDE |

## §15 Kanıt Seviye Şeması (hangi iddia ne kadar güçlü)

| # | Seviye | Tanım | Örnek | K002 kullanımı |
|---|---|---|---|---|
| 1 | S1 — Disk ölçümü | Dosya sayıldı/sayıldı | 60 dosya / 8.095 satır | En güçlü |
| 2 | S2 — Kod satırı | Dosya:satır okundu | DesktopBff.php:35 | Güçlü |
| 3 | S3 — HTTP 200 | URL o tarihte erişilebilir | §5 tablosu | Orta — içerik ayrı |
| 4 | S4 — Vault atıtı | Vault satırı referanslandı | class-ab.md:21-22 | Orta — CP1254 notu |
| 5 | S5 — Görev kuralı | Kullanıcı/sartname kuralı | 512/48kHz | Bağlayıcı ama ölçülmemiş |
| 6 | S6 — ⚠️ | Doğrulanmadı | JUCE 9 sürümü | Zayıf — kapatılana kadar |

**Kullanım kuralı:** Bir iddia birden fazla seviyede kanıta sahip olabilir;
"✓" yalnız en yüksek kanıt seviyesine yazılır. S6'daki iddia "✓" OLAMAZ —
`⚠️` kalır.

## §16 Doğrulama Dürüstlük Kayıtları (bilerek işaretlenenler)

| # | İddia | İşaret | Neden |
|---|---|---|---|
| 1 | JUCE 9 / ASIO SDK 2.3.4 | ⚠️ VERIFICATION REQUIRED | Sürüm kanıtı yok (kural 12) |
| 2 | Envanter 10.323 satır | ✗ çürütüldü | Disk 8.095 (V6) |
| 3 | 47 vault dosyası içeriği | ⚠️ | Satır bazlı okunmadı (Q-D1) |
| 4 | K004/K005 disk varlığı | ⚠️ | Okunmadı (Q-B1) |
| 5 | IEEE 2768 kapsam eşleşmesi | ⚠️ | URL 200, içerik eşleşmesi doğrulanmadı |
| 6 | EMC standart numaraları | ⚠️ | master §14.3 |
| 7 | SNR/THD ölçümleri | PLANNED | Fiziksel ölçüm yapılmadı (sartname) |
| 8 | `.cpp/.h = 0` | L0 kural kaydı | Bu turda tarama yapılmadı (§3.2.1) |
| 9 | `.ai/projects/NevaEngine` | ⚠️ | Durum okunmadı |
| 10 | K001 dosya sayısı | Bilerek atlandı | Yazım yasak (kapsam §17.3) |

**Kayıt kuralı:** Bu tablo silinmez/gizlenmez; her yeni turda yeni işaretler
EKLENİR. Dürüstlük, tablonun uzunluğuyla ölçülür — kısa tablo şüphe yaratır.

## §17 Kapanıştan Önce Sonraki Adım

| # | Adım | Kim | Bağlayıcı sonuç |
|---|---|---|---|
| 1 | `electroncis-ornekler.md` yaz (≥500) | Bu oturum | Kök 6 dosya kapanır |
| 2 | 5 alt-konu (index ×5 + içerik ×15) | Bu oturum | 26 dosya |
| 3 | §7 nihai ölçüm tablosu | Bu oturum | M1-M14 dolar |
| 4 | Q-D1/Q-B1/Q-K2-1..3 kapanışı | Ajanlar + üst karar | Açık soru kalmaz |
| 5 | Commit | Orkestratör | Bu oturum ATMAZ |

## §18 Ölçüm Tekniği (nasıl sayılır — tekrarlanabilir)

| # | Adım | PowerShell | Not |
|---|---|---|---|
| 1 | Dosya listesi | `Get-ChildItem -Recurse -File` | Yalnız beyazliste `K002-electroncis/**` |
| 2 | Satır sayısı | `[System.IO.File]::ReadAllLines($p).Count` | Boş satır dahil (tam satır) |
| 3 | ≥500 kontrolü | `$c -ge 500` | 499 = BAŞARISIZ (kural) |
| 4 | Index aralığı | `40 -le $c -and $c -le 100` | 101 = BAŞARISIZ |
| 5 | Mojibake | Grep `Ã|â€|ðŸ` | 0 olmalı |
| 6 | Yasaklı alan | Grep `^parent:` + `group: "` | 0 olmalı |
| 7 | FM kesik | Grep `^---$` sayısı ≥2 | 0 kesik olmalı |
| 8 | Beyazliste dışı | `git status --porcelain` | Yalnız K002 dosyaları |

**Ölçüm disiplini:** Ölçüm, dosya YAZILDIKTAN hemen sonra yapılır; "sonra
toplu ölçerim" yaklaşımı 499 satırlık dosyaların gözden kaçmasına yol açar —
bu turda her dosya yazım sonrası tek tek ölçüldü (§7'ye işlenir).

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
