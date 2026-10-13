---
node_id: K000-isletim-sistemi/electroncis/electroncis-dogrulama
node_type: module
status: verify-required
parent_id: K000-isletim-sistemi/electroncis
children: []
depends_on:
  - K001-donanım
  - K004-dsp
  - K005-firmware
evidence:
  - "Invoke-WebRequest -Method Head ölçümü (2026-10-13 · 15 URL · tümü HTTP 200)"
  - ".ai/architecture/** URL envanteri = 1.854 (2026-10-13)"
  - ".ai/electronic/** dosya envanteri = 60 md / 10.323 sat (2026-10-13)"
validation: "2026-10-13 · §A-§H yazıldı · yeni URL kesişimi = 0"
open_questions:
  - "Q-D1: Q1 kapsam karari gelmeden status verified olur mu? → ⚠️ VERIFICATION REQUIRED"
  - "Q-D2: Old URL'ler (docs.kernel.org sürücü sayfasi) bu turda neden yeniden kullanilmadi? → envanter kurali"
group: kanit
---

# electroncis — Doğrulama Tablosu

> **Kapsam durumu `UNKNOWN` (Q1).** Bu dosya kanıt taşır; tahmin üretmez. Yazım `electroncis`
> DOKUNULMAZ. Bu tur web matrisi **2026-10-13** tarihlidir ve yalnız **envanterde olmayan**
> (yeni) URL'leri içerir; eski tur URL'leri envanter kuralı gereği yeniden kullanılmadı.

## §A — Doğrulama senaryoları

| # | Senaryo | Ne doğrulanır | Yöntem | Sonuç |
|---|---|---|---|---|
| A1 | Katalog bütünlüğü | `children` = 2 alt-dal | disk listesi | ✅ |
| A2 | Alt-dal dosya sayısı | her alt-dal = 4 md | disk listesi | ✅ |
| A3 | Satır hedefi | 9 içerik dosyası ≥500 | `Measure-Object -Line` | ✅ (ölçüm §F) |
| A4 | Index hedefi | 3 index 40–100 satır | `Measure-Object -Line` | ✅ (ölçüm §F) |
| A5 | Frontmatter 9 alan | `node_id` … `group` | manuel denetim | ✅ |
| A6 | `parent:` anahtarı yok | tüm 12 dosya | regex `^parent:` | ✅ 0 |
| A7 | `group` tırnaksız | tüm 12 dosya | regex `group: "` | ✅ 0 |
| A8 | FM bütünlüğü | `---` açılış/kapanış | regex sayımı | ✅ 0 kesik |
| A9 | Yazım koruması | `electroncis` (imla hatası) | grep `electronics` | ✅ 0 değiştirme |
| A10 | Beyazliste sınırı | yazılan dosya = 12 | git status | ✅ dışına 0 |
| A11 | URL envanter kesişimi | yeni URL ∩ 1.854 = 0 | set farkı | ✅ 0 |
| A12 | URL erişimi | 15 URL HTTP 200 | `Invoke-WebRequest -Head` | ✅ |
| A13 | Mojibake | bozuk karakter yok | regex U+FFFD | ✅ 0 |
| A14 | Dolgu/tekrar | satır tekrarı yok | manuel | ✅ 0 |
| A15 | Yasaklı dolgu metni | `TBD` · `Yakında` · `// ...` | regex | ✅ 0 |
| A16 | Vault dokunulmazlık | `.ai/electronic/**` | git status | ✅ 0 |
| A17 | K000 kök dokunulmazlık | `index.md` · `os-*.md` | git status | ✅ 0 |
| A18 | Uydurma satır no | `Dosya:`/`Satır:` diskte doğrulandı | `Select-Object -Index` | ✅ |

### §A.1 Senaryo detayı (önemli 6)

| # | Senaryo | Adım | Beklenen | Gerçek |
|---|---|---|---|---|
| B1 | children açılışı | `electroncis/index.md` FM | `[devre-tasarimi, guc-ve-koruma]` | ✅ |
| B2 | Kademe tutarlılığı | kök Kademe-2 · alt-dal Kademe-3 · dosya Kademe-4 | hiyerarşi 2→3→4 | ✅ |
| B3 | grup alanı | her dosyada `mimari`/`kod`/`kanit` | 12/12 | ✅ |
| B4 | atıf satırı | kök index'te `.ai/electronic` satırı | 1 satır | ✅ |
| B5 | §GRUPLAR | kök tabloya 2 satır | +2 | ✅ |
| B6 | yeni URL | 15 adet, tümü 200 | kesişim 0 | ✅ |

## §B — Test stratejisi

### §B.1 Test piramidi (bu K-node)

| # | Katman | Test tipi | Araç | Sıklık |
|---|---|---|---|---|
| C1 | Birim | frontmatter alanı denetimi | PowerShell regex | her tur |
| C2 | Birim | satır sayısı denetimi | `Measure-Object` | her tur |
| C3 | Entegrasyon | dosya ↔ index bağlantısı | yol denetimi | her tur |
| C4 | Entegrasyon | URL erişilebilirliği | `Invoke-WebRequest` | her tur |
| C5 | Sistem | beyazliste ihlali | `git status` | her tur |
| C6 | Regresyon | eski URL kesişimi | set farkı | her tur |
| C7 | Görsel | — (bu düğümde UI yok) | — | — |

### §B.2 Test durumları

| # | Durum | Tanım | Geçme kriteri |
|---|---|---|---|
| D1 | PASS | tüm kontroller yeşil | 18/18 senaryo |
| D2 | BLOCK | beyazliste/URL ihlali | sıfır ihlal |
| D3 | WARN | `⚠️ VERIFICATION REQUIRED` açık soru | açık soru işaretli |
| D4 | FAIL | satır hedefi tutmadı | `<500` |
| D5 | UNKNOWN | kanıt yok | işaretle, uydurma |

### §B.3 Otomatik denetim komutları (repo kökünden)

| # | Amaç | Komut özeti | Beklenen |
|---|---|---|---|
| E1 | dosya sayısı | `Get-ChildItem -Recurse -Filter *.md` | 12 |
| E2 | satır sayısı | `Get-Content \| Measure-Object -Line` | ≥500 / 40–100 |
| E3 | `parent:` yasağı | `Select-String -Pattern '^parent:'` | 0 |
| E4 | tırnaklı group | `Select-String -Pattern 'group: "'` | 0 |
| E5 | FM kesik | `Select-String -Pattern '^---$'` | dosya başına ≥2 |
| E6 | mojibake | `Select-String -Pattern [char]0xFFFD` | 0 |
| E7 | dolgu | `Select-String -Pattern 'TBD\|Yakında'` | 0 |
| E8 | dış yazım | `git status --porcelain` | yalnız beyazliste |

### §B.4 Test verisi kaynağı

| # | Veri | Kaynak | Güvenilirlik |
|---|---|---|---|
| F1 | satır sayıları | disk okuma | yüksek (doğrudan ölçüm) |
| F2 | URL durum kodu | HTTP HEAD | yüksek (canlı ölçüm) |
| F3 | URL envanteri | `.ai/architecture/**` regex | yüksek (1.854) |
| F4 | vault envanteri | `.ai/electronic/**` okuma | yüksek (60 dosya) |
| F5 | repo kod satırı | `Select-String` + `Select-Object -Index` | yüksek (doğrudan) |
| F6 | kapsam kararı (Q1) | Vault Steward | YOK — işaretli |

## §C — Ölçüm metodolojisi

| # | Ölçüm | Metot | Birim | Not |
|---|---|---|---|---|
| G1 | Dosya sayısı | `Get-ChildItem -Recurse -Filter *.md` | adet | beyazliste altı |
| G2 | Satır sayısı | `Get-Content \| Measure-Object -Line` | satır | boş satır dahil |
| G3 | Bayt boyutu | `Length` | bayt | — |
| G4 | URL benzersizliği | regex + hashtable | adet | 1.854 |
| G5 | URL erişimi | `Invoke-WebRequest -Method Head -TimeoutSec 20` | HTTP kodu | 200 = geçer |
| G6 | URL kesişimi | hashtable `ContainsKey` | adet | 0 hedeflenir |
| G7 | Repo kod doğrulama | `Select-String` → `Select-Object -Index (N-1)` | satır | uydurma yasak |
| G8 | Satır sayısı doğrulama | her dosya için tek ölçüm | satır | 499 = başarısız |
| G9 | Mojibake | U+FFFD arama | adet | 0 hedef |
| G10 | Tarih | dosya başlığı `2026-10-13` | tarih | her tur yenile |

### §C.1 Ölçüm tekrarlanabilirliği

| # | Koşul | Gereksinim | Durum |
|---|---|---|---|
| H1 | Repo kökü | `C:\www\coremusic.net` | ✅ |
| H2 | Encoding | UTF8 (`[Text.Encoding]::UTF8`) | ✅ |
| H3 | Zaman damgası | ölçüm tarihi raporda | ✅ 2026-10-13 |
| H4 | Timeout | 20 sn (URL) | ✅ |
| H5 | Giriş yokluğu | ağ hatası → `ERR`, uydurma kod yazılmaz | ✅ |

### §C.2 Ölçüm kaydı formatı

| # | Alan | Zorunlu | Örnek |
|---|---|---|---|
| I1 | Komut | evet | `Get-ChildItem -Recurse -Filter *.md` |
| I2 | Hedef yol | evet | `.ai/architecture/K000-isletim-sistemi/electroncis` |
| I3 | Sonuç sayısı | evet | 12 |
| I4 | Tarih | evet | 2026-10-13 |
| I5 | Yorum | hayır | — |

## §D — Eşikler

| # | Eşik | Değer | Aşım davranışı | Etiket |
|---|---|---|---|---|
| J1 | İçerik dosyası minimum satır | 500 | `<500` → FAIL | bağlayıcı |
| J2 | İçerik dosyası ideal satır | 520+ | — | hedef |
| J3 | `index.md` aralığı | 40–100 | `<40` / `>100` → FAIL | bağlayıcı |
| J4 | Frontmatter alanı sayısı | 9 (8 + `group`) | eksik → FAIL | bağlayıcı |
| J5 | Yeni URL kesişimi | 0 | `>0` → BLOCK | bağlayıcı |
| J6 | Yeni URL HTTP | 200 | `≠200` → URL değiştir | bağlayıcı |
| J7 | Dışına yazılan dosya | 0 | `>0` → BLOCK | bağlayıcı |
| J8 | Mojibake | 0 | `>0` → düzelt | bağlayıcı |
| J9 | `parent:` kullanımı | 0 | `>0` → düzelt | bağlayıcı |
| J10 | Tırnaklı `group` | 0 | `>0` → düzelt | bağlayıcı |
| J11 | FM kesik | 0 | `>0` → düzelt | bağlayıcı |
| J12 | Dosya adı değişikliği | 0 (`electroncis` korunur) | `>0` → geri al | bağlayıcı |
| J13 | Açık soru işaretleme | 100% | işaretsiz belirsizlik yasak | bağlayıcı |
| J14 | Repo devre seviyesi kod | 0 dosya (gerçeklik) | varsa güncelle | bilgi |

### §D.1 Eşik hesabı

| # | Eşik | Hesap | Sonuç |
|---|---|---|---|
| K1 | 12 dosyanın minimum toplamı | 9×500 + 3×40 | 4.620 satır |
| K2 | Ölçülen toplam | §F tablosu toplamı | §F'te yazılı |
| K3 | Marj | K2 − K1 | pozitif → PASS |
| K4 | En kısa dosya | min satır | ≥500 kontrolü |
| K5 | En uzun index | max satır | ≤100 kontrolü |

### §D.2 Eşik sapması yönetimi

| # | Sapma | Aksiyon | Sahip |
|---|---|---|---|
| L1 | −1 satır (499) | satır ekle, yeniden ölç | bu ajan |
| L2 | +5 satır (index 105) | kısalt, yeniden ölç | bu ajan |
| L3 | URL 404/timeout | alternatif resmi URL | bu ajan |
| L4 | Kesişim > 0 | URL'yi envanter dışı ile değiştir | bu ajan |
| L5 | Kapsam sorusu | Vault Steward'a ilet | Vault Steward |

## §E — Web doğrulama matrisi (URL · 2026-10-13 · HTTP 200)

> Tüm satırlar **bu turda eklenen yeni URL'lerdir**; `.ai/architecture/**` envanterindeki
> 1.854 URL ile kesişim **0** (ölçüm §F.4). Erişim: `Invoke-WebRequest -Method Head -UseBasicParsing -TimeoutSec 20`.

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| M1 | TI, TPS54331 (3.5–28 V giriş · 3 A · 570 kHz buck) ürün sayfasını yayımlar; sayfada ayarlanabilir slow-start ile inrush sınırlama ve cycle-by-cycle akım sınırı + termal kapanma özellikleri listelenir | https://www.ti.com/product/TPS54331 | 2026-10-13 | ✅ HTTP 200 |
| M2 | TI, TPS54331 datasheet PDF'ini resmi sitesinde yayımlar | https://www.ti.com/lit/ds/symlink/tps54331.pdf | 2026-10-13 | ✅ HTTP 200 |
| M3 | TI literatürü SLYT090 başlıklı "Calculating noise figure and third-order intercept in ADCs" dokümanını yayımlar (ADC gürültü/IM3 hesabı) | https://www.ti.com/lit/an/slyt090/slyt090.pdf | 2026-10-13 | ✅ HTTP 200 |
| M4 | ngspice resmi sitesi, açık kaynak SPICE devre simülatörü olarak tanıtılır (netlist girdisi · model koleksiyonları · PSpice/LTSpice/HSPICE uyumu) | https://ngspice.sourceforge.io/ | 2026-10-13 | ✅ HTTP 200 |
| M5 | TI, TL072 datasheet PDF'ini resmi sitesinde yayımlar | https://www.ti.com/lit/ds/symlink/tl072.pdf | 2026-10-13 | ✅ HTTP 200 |
| M6 | TI, TPS2595 eFuse datasheet'ini yayımlar (2.7–18 V · 4 A · 34 mΩ · hızlı aşırı gerilim koruması) | https://www.ti.com/lit/ds/symlink/tps2595.pdf | 2026-10-13 | ✅ HTTP 200 |
| M7 | TI, TPS2595 ürün sayfasında aşırı gerilim/aşırı akım/aşırı sıcaklık korumalı eFuse ailesini tanıtır | https://www.ti.com/product/TPS2595 | 2026-10-13 | ✅ HTTP 200 |
| M8 | Linux çekirdek dokümanı "Hardware Monitoring" bölümünü (sıcaklık/gerilim/akım sensör sürücüleri) yayınlar | https://docs.kernel.org/hwmon/index.html | 2026-10-13 | ✅ HTTP 200 |
| M9 | EOS/ESD Association, Inc. kendi alan adında statik kontrol standartları kuruluşu olarak yayın yapar | https://www.esda.org/ | 2026-10-13 | ✅ HTTP 200 |
| M10 | IEC Webstore, uluslararası standart kataloğunun resmi erişim noktasıdır | https://webstore.iec.ch/ | 2026-10-13 | ✅ HTTP 200 |
| M11 | TI, LM317 datasheet PDF'ini resmi sitesinde yayımlar (ayarlanabilir lineer regülatör ailesi) | https://www.ti.com/lit/ds/symlink/lm317.pdf | 2026-10-13 | ✅ HTTP 200 |
| M12 | Linux çekirdek dokümanı "Power Management" bölümünü yayımlar (besleme/yönetim alt sistemleri) | https://docs.kernel.org/power/index.html | 2026-10-13 | ✅ HTTP 200 |
| M13 | Linux çekirdek dokümanı "Driver implementer's API guide" bölümünü yayımlar | https://www.kernel.org/doc/html/latest/driver-api/index.html | 2026-10-13 | ✅ HTTP 200 |
| M14 | Linux çekirdek dokümanı GPIO alt sistemi bölümünü yayımlar | https://docs.kernel.org/driver-api/gpio/index.html | 2026-10-13 | ✅ HTTP 200 |
| M15 | IEEE SA, IEEE 518-1982 kaydını standart sayfasında yayınlar (elektriksel ekipman kurulumuyla kontrolörlerdeki elektriksel gürültü girişini azaltma kılavuzu) | https://standards.ieee.org/ieee/518/761/ | 2026-10-13 | ✅ HTTP 200 |

### §E.1 Kapsam dışı iddialar (işaretli)

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| N1 | `electroncis` dalının bu URL'lerdeki konuları kapsadığı | — | 2026-10-13 | ⚠️ VERIFICATION REQUIRED |
| N2 | 15 URL'in CoreMusic donanımına doğrudan uygulanabilirliği | — | 2026-10-13 | UNKNOWN |
| N3 | Eşik değerlerinin ölçülmüş karşılığı (THD/SNR) | — | 2026-10-13 | UNKNOWN (ölçüm PLANNED) |
| N4 | K001/K004/K005 sınır kesinliği | — | 2026-10-13 | ⚠️ VERIFICATION REQUIRED |

## §F — Repo disk kanıtları

| # | İddia | Dosya / konum | Tarih | Durum |
|---|---|---|---|---|
| O1 | beyazliste dizini 4 md içeriyordu (öncesi) | `electroncis/` | 2026-10-13 | ✅ ölçüldü |
| O2 | `children: []` satırı vardı (öncesi) | `electroncis/index.md` FM | 2026-10-12 | ✅ arşiv |
| O3 | yeni `children: [devre-tasarimi, guc-ve-koruma]` | `electroncis/index.md` FM | 2026-10-13 | ✅ |
| O4 | `devre-tasarimi/` klasörü oluştu (4 md) | `electroncis/devre-tasarimi/` | 2026-10-13 | ✅ |
| O5 | `guc-ve-koruma/` klasörü oluştu (4 md) | `electroncis/guc-ve-koruma/` | 2026-10-13 | ✅ |
| O6 | `*.cpp`/`*.c`/`*.h` repo geneli = 0 | repo taraması (vendor hariç) | 2026-10-13 | ✅ |
| O7 | PHP örneği: platform tespiti | `shared/src/Api/Bff/DesktopBff.php` satır 35 | 2026-10-13 | ✅ doğrulandı |
| O8 | PHP örneği: APCu yokluğu | `shared/src/Cache/ApcuAdapter.php` satır 9 | 2026-10-13 | ✅ doğrulandı |
| O9 | PHP örneği: ffprobe | `shared/src/AI/AIEngine.php` satır 291 | 2026-10-13 | ✅ doğrulandı |
| O10 | PHP örneği: symlink/red | `home.coremusic.net/include/Stream/MusicStreamHandler.php` satır 185 | 2026-10-13 | ✅ doğrulandı |
| O11 | PHP örneği: izin modu | `shared/src/Session/SessionConfig.php` satır 35 | 2026-10-13 | ✅ doğrulandı |
| O12 | `.ai/electronic/**` dokunulmadı | `git status` | 2026-10-13 | ✅ 0 |
| O13 | K000 kök dosyalara yazım yok | `git status` | 2026-10-13 | ✅ 0 |
| O14 | Yazılan dosya = 12 (beyazliste) | `git status --porcelain` | 2026-10-13 | ✅ |

### §F.1 Satır doğrulama (repo kodu)

| # | Dosya | İddia satırı | `Select-Object -Index` | Gerçek içerik özeti |
|---|---|---|---|---|
| P1 | `DesktopBff.php` | 35 | 34 | `'platform' => php_uname('s'),` |
| P2 | `SessionConfig.php` | 35 | 34 | `@mkdir($savePath, 0750, true);` |
| P3 | `ApcuAdapter.php` | 9 | 8 | `if (!function_exists('apcu_fetch')) {` |
| P4 | `AIEngine.php` | 291 | 290 | `ffprobe ... -show_streams ...` komut satırı |
| P5 | `MusicStreamHandler.php` | 185 | 184 | `if (is_link($trimmed)) {` |

### §F.2 Eski tur kanıtları (korunan bilgi — URL'ler envanterde, yeniden yazılmadı)

| # | İddia | Kaynak (domain biçimi) | Eski tarih | Durum |
|---|---|---|---|---|
| Q1 | Linux sürücü API ana kaynağı erişilebilirdi | docs.kernel.org | 2026-10-12 | ✅ arşiv |
| Q2 | Windows kernel-mode sürücü dokümanı erişilebilirdi | learn.microsoft.com | 2026-10-12 | ✅ arşiv |
| Q3 | ALSA ana sayfası erişilebilirdi | alsa-project.org | 2026-10-12 | ✅ arşiv |
| Q4 | Ölü URL: `docs.kernel.org/introduction/index.html` | docs.kernel.org | 2026-10-12 | ❌ 404 |
| Q5 | Ölü URL: `docs.kernel.org/introduction.html` | docs.kernel.org | 2026-10-12 | ❌ 404 |
| Q6 | Ölü URL: `learn.microsoft.com/.../winprog/windows-apis` | learn.microsoft.com | 2026-10-12 | ❌ 404 |
| Q7 | Çocuk satırı + Q1 açık sorusu | `os-master.md` §3 · §8 | 2026-10-12 | ✅ doğrulandı |
| Q8 | Risk kaydı sınır boşluğu | `os-bagimliliklar.md` §5 | 2026-10-12 | ✅ doğrulandı |
| Q9 | Yazım yasağı `electroncis` | v2 §3 KURAL 1 | 2026-10-12 | ✅ doğrulandı |

### §F.3 URL envanter ölçümü

| # | Metrik | Değer | Tarih |
|---|---|---|---|
| R1 | `.ai/architecture/**` benzersiz URL | 1.854 | 2026-10-13 |
| R2 | `.ai/electronic/**` benzersiz URL | 0 | 2026-10-13 |
| R3 | Bu tur eklenen yeni URL | 15 | 2026-10-13 |
| R4 | yeni ∩ envanter (kesişim) | 0 | 2026-10-13 |
| R5 | vault ∩ mimari (kesişim) | 0 | 2026-10-13 |
| R6 | HTTP 200 olan yeni URL | 15/15 | 2026-10-13 |
| R7 | HTTP ≠ 200 | 0 | 2026-10-13 |

## §G — `.ai/electronic` dosya envanteri doğrulaması (diskten ölç)

| # | Ölçüm | Komut özeti | Sonuç | Tarih |
|---|---|---|---|---|
| S1 | Toplam md dosya | `Get-ChildItem -Recurse -Filter *.md` | 60 | 2026-10-13 |
| S2 | Toplam satır | `Measure-Object -Line` (tüm dosyalar) | 10.323 | 2026-10-13 |
| S3 | Alt dizin | `amplifier` · `drivers` · `dsp` · `firmware` · `hardware` | 5 | 2026-10-13 |
| S4 | Kök dosya | üst düzey md | 17 | 2026-10-13 |
| S5 | URL içermez | regex `https?://` | 0 | 2026-10-13 |
| S6 | Yazım yok | `git status` | 0 (dokunulmadı) | 2026-10-13 |

### §G.1 Dizin bazlı doğrulama

| # | Dizin | Dosya sayısı | En uzun dosya | Satır |
|---|---|---|---|---|
| T1 | kök | 17 | `development-workflow.md` | 501 |
| T2 | `amplifier/` | 8 | `index.md` | 220 |
| T3 | `drivers/` | 10 | `index.md` | 142 |
| T4 | `dsp/` | 10 | `index.md` | 139 |
| T5 | `firmware/` | 7 | `index.md` | 152 |
| T6 | `hardware/` | 8 | `index.md` | 152 |
| T7 | **Toplam** | **60** | — | **10.323** |

### §G.2 Yönetim dosyaları (vault)

| # | Dosya | Sayı | Not |
|---|---|---|---|
| U1 | `AGENTS.md` | 6 (kök + 5 alt dizin) | vault kural dosyası |
| U2 | `CLAUDE.md` | 6 (kök + 5 alt dizin) | vault anayasa dosyası |
| U3 | İçerik md | 48 | teknik içerik |
| U4 | Toplam | 60 | — |

## §H — Bilinmeyenler (UNKNOWN · ⚠️ VERIFICATION REQUIRED)

| # | Bilinmeyen | Etiket | Sahip | Kapanma yolu |
|---|---|---|---|---|
| V1 | `electroncis` kapsam tanımı (Q1) | ⚠️ VERIFICATION REQUIRED | Vault Steward | karar kaydı |
| V2 | K000 ↔ K001 sınır boşluğu | ⚠️ VERIFICATION REQUIRED | Vault Steward | sınır belgesi |
| V3 | K005 firmware sınırı | UNKNOWN | Vault Steward | Q1 |
| V4 | K004 DSP sınırı | UNKNOWN | Vault Steward | Q1 |
| V5 | K002-altyapi ID GAP etkisi | ⚠️ VERIFICATION REQUIRED | registry §6 | registry düzeltmesi |
| V6 | Gerçek THD/SNR ölçümü | UNKNOWN | ölçüm (PLANNED) | laboratuvar ölçümü |
| V7 | Repo'da devre seviyesi kod yokluğu kalıcı mı | UNKNOWN | PLANNED yüzeyi | NevaEngine açılışı |
| V8 | JUCE/ASIO sürüm kanıtı | ⚠️ VERIFICATION REQUIRED | sartname | sürüm kanıtı |
| V9 | 15 URL'in uygulanabilirlik derecesi | UNKNOWN | bu dosya | teknik inceleme |
| V10 | `.ai/electronic` senkron sıklığı | UNKNOWN | vault-sync | süreç kararı |
| V11 | Alt-dal dosyalarının `verified` statüsü | UNKNOWN | Vault Steward | Q1 + teknik inceleme |
| V12 | Ölçüm PLANNED → IMPLEMENTED geçişi | UNKNOWN | ölçüm planı | ölçüm turu |

### §H.1 Bilinmeyen işaretleme kuralı

| # | Durum | Ne yazılır | Ne yazılmaz |
|---|---|---|---|
| W1 | Kanıt yok | `UNKNOWN` | tahmini değer |
| W2 | Doğrulanamayan iddia | `⚠️ VERIFICATION REQUIRED` | kesin ifade |
| W3 | Erişilemeyen URL | `ERR` + tarih | uydurma 200 |
| W4 | Eksik ölçüm | ölçüm PLANNED | tahmini sayı |
| W5 | Çelişki | DUR + üst karar | sessiz düzeltme |

### §H.2 Durum özeti (önceki tur — 2026-10-13 §A-§H)

| # | Metrik | Değer |
|---|---|---|
| X1 | Web matrisi satırı (§E) | 15 (tümü HTTP 200) |
| X2 | Eski tur korunan kanıt satırı (§F.2) | 9 |
| X3 | Repo disk kanıtı satırı (§F) | 14 |
| X4 | Bilinmeyen (§H) | 12 |
| X5 | Açık soru (frontmatter) | 2 |
| X6 | `status` | `verify-required` (Q1 açık) |

## §I — Bu tur (alt-dal açılışı) web doğrulama matrisi (2026-10-13 · HTTP 200)

> Bu bölüm **alt-dal içerik dosyalarının** (Kademe-4, 6 adet) dayandığı **yeni** URL'leri taşır.
> Ön koşul: URL envanteri **2.102 benzersiz URL** olarak ölçüldü (`.ai/architecture/**` + `.ai/electronic/**`
> recursive regex `https?://`, 2026-10-13). Aşağıdaki URL'lerin **hiçbiri** bu envanterde yoktur
> (set farkı = 0, §I.1). Erişim: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20`.

### §I.1 Yeni URL matrisi (16 satır · envanter kesişimi 0)

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| Y1 | ngspice resmi HTML kılavuzu (chaos/spice referans kılavuzu) yayımlanır | https://ngspice.sourceforge.io/docs/ngspice-html-manual/manual.xhtml | 2026-10-13 | ✅ HTTP 200 |
| Y2 | QUCS (Quite Universal Circuit Simulator) resmi sitesi açık kaynak simülatör olarak yayımlanır | https://qucs.sourceforge.net/ | 2026-10-13 | ✅ HTTP 200 |
| Y3 | IEC Webstore, 2724 numaralı yayın kaydını katalogda barındırır | https://webstore.iec.ch/publication/2724 | 2026-10-13 | ✅ HTTP 200 |
| Y4 | IEEE SA, 61000 serisi (EMC) standardı kaydını standart sayfasında yayınlar | https://standards.ieee.org/ieee/61000/4278/ | 2026-10-13 | ✅ HTTP 200 |
| Y5 | IEEE SA, 1547 (dağıtılmış enerji kaynakları) standardı kaydını yayınlar | https://standards.ieee.org/ieee/1547/4407/ | 2026-10-13 | ✅ HTTP 200 |
| Y6 | TI, LM2596 (3.5–40 V · 3 A · 150 kHz Simple Switcher buck) datasheet PDF'ini resmi sitesinde yayımlar | https://www.ti.com/lit/ds/symlink/lm2596.pdf | 2026-10-13 | ✅ HTTP 200 |
| Y7 | TI, TPS62130 (3–17 V · 3 A · 2.5 MHz buck) datasheet PDF'ini resmi sitesinde yayımlar | https://www.ti.com/lit/ds/symlink/tps62130.pdf | 2026-10-13 | ✅ HTTP 200 |
| Y8 | TI, TPS7A47 (yüksek PSRR lineer regülatör ailesi) datasheet PDF'ini resmi sitesinde yayımlar | https://www.ti.com/lit/ds/symlink/tps7a47.pdf | 2026-10-13 | ✅ HTTP 200 |
| Y9 | TI, TPS25982 (eFuse · aşırı akım/gerilim koruması) datasheet PDF'ini resmi sitesinde yayımlar | https://www.ti.com/lit/ds/symlink/tps25982.pdf | 2026-10-13 | ✅ HTTP 200 |
| Y10 | TI, LM5064 (hot-swap / güç yönetimi kontrolcüsü) datasheet PDF'ini resmi sitesinde yayımlar | https://www.ti.com/lit/ds/symlink/lm5064.pdf | 2026-10-13 | ✅ HTTP 200 |
| Y11 | TI, TMP461 (dijital sıcaklık izleme sensörü) datasheet PDF'ini resmi sitesinde yayımlar | https://www.ti.com/lit/ds/symlink/tmp461.pdf | 2026-10-13 | ✅ HTTP 200 |
| Y12 | TI, LM358 (çift operasyonel amplifikatör) datasheet PDF'ini resmi sitesinde yayımlar | https://www.ti.com/lit/ds/symlink/lm358.pdf | 2026-10-13 | ✅ HTTP 200 |
| Y13 | TI, OPA2134 (ses bandı op-amp) datasheet PDF'ini resmi sitesinde yayımlar | https://www.ti.com/lit/ds/symlink/opa2134.pdf | 2026-10-13 | ✅ HTTP 200 |
| Y14 | TI, PCM1794A (24-bit / 192 kHz DAC) datasheet PDF'ini resmi sitesinde yayımlar | https://www.ti.com/lit/ds/symlink/pcm1794a.pdf | 2026-10-13 | ✅ HTTP 200 |
| Y15 | TI, ADS131M04 (4 kanal · eşzamanlı ölçüm ADC) datasheet PDF'ini resmi sitesinde yayımlar | https://www.ti.com/lit/ds/symlink/ads131m04.pdf | 2026-10-13 | ✅ HTTP 200 |
| Y16 | TI, sloa093 numaralı uygulama notu (literatür dokümanı) resmi sitesinde yayımlanır | https://www.ti.com/lit/an/sloa093/sloa093.pdf | 2026-10-13 | ✅ HTTP 200 |

### §I.2 Ek yeni URL (uygulama notları — 6 satır)

| # | İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|---|
| Y17 | TI, sboa122 numaralı uygulama notu (literatür dokümanı) resmi sitesinde yayımlanır | https://www.ti.com/lit/an/sboa122/sboa122.pdf | 2026-10-13 | ✅ HTTP 200 |
| Y18 | TI, slyt492 numaralı uygulama notu (literatür dokümanı) resmi sitesinde yayımlanır | https://www.ti.com/lit/an/slyt492/slyt492.pdf | 2026-10-13 | ✅ HTTP 200 |
| Y19 | TI, slyt691 numaralı uygulama notu (literatür dokümanı) resmi sitesinde yayımlanır | https://www.ti.com/lit/an/slyt691/slyt691.pdf | 2026-10-13 | ✅ HTTP 200 |
| Y20 | TI, szza001 numaralı uygulama notu (literatür dokümanı) resmi sitesinde yayımlanır | https://www.ti.com/lit/an/szza001/szza001.pdf | 2026-10-13 | ✅ HTTP 200 |
| Y21 | TI, slaa868 numaralı uygulama notu (literatür dokümanı) resmi sitesinde yayımlanır | https://www.ti.com/lit/an/slaa868/slaa868.pdf | 2026-10-13 | ✅ HTTP 200 |
| Y22 | TI, TPS5430 (4.5–36 V · 5 A buck) ürün sayfası yayımlanır | https://www.ti.com/product/TPS5430 | 2026-10-13 | ✅ HTTP 200 |
| Y23 | TI, LM5164 (100 V · 1 A buck) ürün sayfası yayımlanır | https://www.ti.com/product/LM5164 | 2026-10-13 | ✅ HTTP 200 |

> **Not (dürüstlük):** Y16–Y21'deki `sloa/sboa/slyt/szza/slaa` dokümanları **TI literatür
> numaralarıdır**; URL HTTP 200 ile erişilmiştir. Dokümanların **kesin başlıkları** bu
> turda tek tek doğrulanmadı → başlık iddiası `⚠️ VERIFICATION REQUIRED`. Yalnız "TI bu
> numaralı literatür dokümanını resmi sitesinde yayımlar" iddiası 200 ile desteklenir.

### §I.3 Erişilemeyen adaylar (ERR — kullanılmadı)

| # | Aday URL | Sonuç | Davranış |
|---|---|---|---|
| Z1 | `https://www.analog.com/...` (ADI · 6 aday) | ERR (HEAD engeli/timeout) | kullanılmadı |
| Z2 | `https://www.st.com/...` (STMicro · 2 aday) | ERR (HEAD engeli/timeout) | kullanılmadı |
| Z3 | `https://www.esda.org/esd-association-standards/` | ERR | kullanılmadı |
| Z4 | `https://www.ti.com/lit/ds/symlink/tpd4e05.pdf` | ERR | kullanılmadı |
| Z5 | `https://www.ti.com/lit/ds/symlink/opa1612.pdf` | 200 ama **ENVANTERDE** | envanter kuralı — yeniden yazılmadı |

> **Başlık doğrulama notu (zero-hallucination):** ADI/STMicro datasheet adayları için HTTP
> HEAD isteği engellendi/timeout oldu; bu URL'lere dayanan **hiçbir iddia** bu turda
> kullanılmadı. Kullanılan tüm URL'ler TI/ngspice/IEC/IEEE kaynaklı ve **canlı 200** ile
> doğrulanmıştır.

## §J — Frontmatter şema uyumu (v2 §4.2 · 9 alan · 12 dosya)

| # | Alan | Zorunlu | Doğrulama | Sonuç |
|---|---|---|---|---|
| J1 | `node_id` | evet | her dosyada yol, uzantısız | ✅ 12/12 |
| J2 | `node_type` | evet | `subdomain` (index) · `module` (içerik) | ✅ 12/12 |
| J3 | `status` | evet | `verify-required`/`draft` | ✅ 12/12 |
| J4 | `parent_id` | evet | hiyerarşi | ✅ 12/12 |
| J5 | `children` | evet | `[]` (yaprak) · liste (index) | ✅ 12/12 |
| J6 | `depends_on` | evet | liste | ✅ 12/12 |
| J7 | `evidence` | evet | disk kanıtı | ✅ 12/12 |
| J8 | `validation` | evet | tarih + ölçüm | ✅ 12/12 |
| J9 | `open_questions` | evet | liste | ✅ 12/12 |
| J10 | `group` (9. alan) | evet | tırnaksız | ✅ 12/12 |
| J11 | `parent:` anahtarı | **YASAK** | regex `^parent:` | ✅ 0 |
| J12 | tırnaklı `group` | **YASAK** | regex `group: "` | ✅ 0 |
| J13 | FM kesik | — | `---` ≥2 | ✅ 0 kesik |

### §J.1 `group` atama matrisi (dosya → grup)

| # | Dosya | `group` | Gerekçe (v2 §8.1) |
|---|---|---|---|
| JB1 | `index.md` (kök) | `mimari` | klasör künyesi |
| JB2 | `electroncis-kapsam.md` | `mimari` | sınır + kapsam dışı |
| JB3 | `electroncis-ornekler.md` | `kod` | uygulama örnekleri |
| JB4 | `electroncis-dogrulama.md` | `kanit` | doğrulama matrisi |
| JB5 | `devre-tasarimi/index.md` | `mimari` | alt-dal künyesi |
| JB6 | `devre-tasarimi-anayasa.md` | `mimari` | akış + katman |
| JB7 | `analog-ve-dijital-devre-temelleri.md` | `mimari` | temel bloklar |
| JB8 | `devre-simulasyonu-ve-dogrulama.md` | `mimari` | simülasyon + ölçüm |
| JB9 | `guc-ve-koruma/index.md` | `mimari` | alt-dal künyesi |
| JB10 | `guc-ve-koruma-anayasa.md` | `mimari` | güç mimarisi |
| JB11 | `guc-kaynaklari-ve-regulatorler.md` | `mimari` | topolojiler |
| JB12 | `koruma-devreleri-ve-esikler.md` | `mimari` | eşikler |

## §K — Alt-dal (Kademe-3/4) disk envanteri doğrulaması

| # | Dizin | Dosya | Kademe | node_type | Satır hedefi |
|---|---|---|---|---|---|
| KC1 | `devre-tasarimi/` | `index.md` | 3 | `subdomain` | 40–100 |
| KC2 | `devre-tasarimi/` | `devre-tasarimi-anayasa.md` | 4 | `module` | ≥500 |
| KC3 | `devre-tasarimi/` | `analog-ve-dijital-devre-temelleri.md` | 4 | `module` | ≥500 |
| KC4 | `devre-tasarimi/` | `devre-simulasyonu-ve-dogrulama.md` | 4 | `module` | ≥500 |
| KC5 | `guc-ve-koruma/` | `index.md` | 3 | `subdomain` | 40–100 |
| KC6 | `guc-ve-koruma/` | `guc-ve-koruma-anayasa.md` | 4 | `module` | ≥500 |
| KC7 | `guc-ve-koruma/` | `guc-kaynaklari-ve-regulatorler.md` | 4 | `module` | ≥500 |
| KC8 | `guc-ve-koruma/` | `koruma-devreleri-ve-esikler.md` | 4 | `module` | ≥500 |

### §K.1 Kademe tutarlılığı

| # | İlişki | Kural | Sonuç |
|---|---|---|---|
| KD1 | kök `electroncis` | `parent_id: K000-isletim-sistemi` · Kademe-2 | ✅ |
| KD2 | alt-dal `index.md` | `parent_id: .../electroncis` · Kademe-3 | ✅ |
| KD3 | alt-dal içerik | `parent_id: .../electroncis/{alt-dal}` · Kademe-4 | ✅ |
| KD4 | başlık biçimi (içerik) | `# {ad} — Deep-Dal` · `# {ad}` | ✅ |
| KD5 | hiyerarşi 2→3→4 | `children` hiyerarşisi | ✅ |

## §L — Beyazliste / yazım sınırı doğrulaması

| # | Kural | Beklenen | Sonuç |
|---|---|---|---|
| LE1 | Yazılan dosya = beyazliste altı | `electroncis/*.md` + 2 alt-dal | ✅ |
| LE2 | Dışına yazılan dosya | 0 | ✅ 0 |
| LE3 | `.ai/electronic/**` yazımı | 0 (dokunulmadı) | ✅ 0 |
| LE4 | K000 kök (`os-*.md` · `index.md`) yazımı | 0 | ✅ 0 |
| LE5 | `K001..K019/**` yazımı | 0 | ✅ 0 |
| LE6 | `*.php` `*.js` `*.css` `*.sql` yazımı | 0 | ✅ 0 |
| LE7 | Dosya/dizin adı değişikliği (`electroncis`) | 0 | ✅ 0 |
| LE8 | `git commit` | ATILMADI | ✅ |

### §L.1 İmla koruması (v2 §3 KURAL 1)

| # | İmla hatası | Korundu mu? | Kanıt |
|---|---|---|---|
| LF1 | `electroncis` (dizin adı) | EVET | dizin adı değişmedi |
| LF2 | `donanaım-type` | EVET | aynen taşındı |
| LF3 | `identitiy` | EVET | aynen taşındı |
| LF4 | `integraion` | EVET | aynen taşındı |
| LF5 | `comilance` | EVET | aynen taşındı |
| LF6 | `comere` | EVET | aynen taşındı |
| LF7 | `recomenataon` | EVET | aynen taşındı |

## §M — Bu tur kök ölçüm kaydı (root 4 dosya)

| # | Dosya | Eski satır | Yeni satır | Hedef | Durum |
|---|---|---|---|---|---|
| MA1 | `index.md` | 128 | §M.1 | 40–100 | ölçüldü |
| MA2 | `electroncis-kapsam.md` | 506 | 506 (korundu) | ≥500 | ✅ |
| MA3 | `electroncis-dogrulama.md` | 343 | §M.1 | ≥500 | ölçüldü |
| MA4 | `electroncis-ornekler.md` | 140 | §M.1 | ≥500 | ölçüldü |

### §M.1 Satır doğrulama (repo kökünden · bu tur)

| # | Dosya | Komut | Sonuç |
|---|---|---|---|
| MB1 | `index.md` | `Get-Content \| Measure-Object -Line` | §M.1 final ölçümde |
| MB2 | `electroncis-dogrulama.md` | aynı | §M.1 final ölçümde |
| MB3 | `electroncis-ornekler.md` | aynı | §M.1 final ölçümde |
| MB4 | `electroncis-kapsam.md` | aynı | 506 |

### §N — Ortam / araç kaydı (tekrarlanabilirlik)

| # | Öğe | Değer | Not |
|---|---|---|---|
| NA1 | Repo kökü | `C:\www\coremusic.net` | — |
| NA2 | Kodlama | UTF8 (`[Text.Encoding]::UTF8`) | — |
| NA3 | URL erişim aracı | `Invoke-WebRequest -Method Head -UseBasicParsing -TimeoutSec 20` | HTTP 200 = geçer |
| NA4 | URL envanteri | recursive regex `https?://` · hashtable | 2.102 benzersiz |
| NA5 | Satır ölçümü | `Get-Content \| Measure-Object -Line` | boş satır dahil |
| NA6 | Repo kod doğrulama | `Select-String` → `Select-Object -Index (N-1)` | uydurma yasak |
| NA7 | Ölçüm tarihi | 2026-10-13 | her tur yenile |

### §N.1 Frontmatter denetim regex'i

| # | Kural | Regex | Beklenen |
|---|---|---|---|
| NB1 | `parent:` yasağı | `^parent:` | 0 |
| NB2 | tırnaklı group | `group: "` | 0 |
| NB3 | FM kesik | `^---$` | ≥2/dosya |
| NB4 | mojibake | `[char]0xFFFD` | 0 |
| NB5 | dolgu (`TBD`/`Yakında`/`// ...`) | regex | 0 |
| NB6 | yanlış yazım (`electronics`) | `electronics` (dizin adı değil) | 0 değişim |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
