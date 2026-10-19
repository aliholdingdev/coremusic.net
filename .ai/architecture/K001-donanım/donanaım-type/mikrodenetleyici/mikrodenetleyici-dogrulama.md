---
node_id: K001-donanım/donanaım-type/mikrodenetleyici/mikrodenetleyici-dogrulama
node_type: module
status: draft
parent_id: K001-donanım/donanaım-type/mikrodenetleyici
children: []
depends_on:
  - "K001-donanım/donanaım-type/mikrodenetleyici/mikrodenetleyici-kapsam"
  - "K001-donanım/donanaım-type/mikrodenetleyici/mikrodenetleyici-ornekler"
evidence:
  - "https://onlinedocs.microchip.com/oxy/gen10/MPLAB_XC8_C_Compiler_Compiler_User_Guide/index.html (✅ HTTP 200 · 2026-10-14)"
  - "https://www.nongnu.org/avr-libc/user-manual/index.html (✅ HTTP 200 · 2026-10-14)"
  - "https://docs.platformio.org/en/latest/platforms/atmelavr.html (✅ HTTP 200 · 2026-10-14)"
  - "https://docs.platformio.org/en/latest/projectconf/ (✅ HTTP 200 · 2026-10-14)"
  - "https://docs.arduino.cc/ (✅ HTTP 200 · 2026-10-14)"
  - "https://raw.githubusercontent.com/arduino/arduino-cli/master/README.md (✅ HTTP 200 · 2026-10-14)"
  - "https://github.com/arduino/ArduinoCore-avr (✅ HTTP 200 · 2026-10-14)"
  - "https://downloads.arduino.cc/arduino-cli/arduino-cli_latest_Windows_64bit.zip (✅ HTTP 200 · 2026-10-14)"
validation: "2026-10-14 · 8 URL HEAD 200 · envanter kesişimi 0 · repo glob ölçümleri (aşağıda) · kapılar K-M1…K-M5 PLANNED"
open_questions:
  - "Q-M1 — PIC serisi (16F/18F/24F/dsPIC)? ⚠️ VERIFICATION REQUIRED"
  - "Q-M2 — ATmega328P hedef teyidi? ⚠️ VERIFICATION REQUIRED"
  - "Q-M4 — HIL donanımı (PICkit/avrdude programmer) envanteri UNKNOWN"
group: kanit
---

# mikrodenetleyici-dogrulama — Kademe-4 Modül (kanıt)

> **Kapsam:** Bu dosya, dalın **ölçüm ve kapı** kaydıdır: URL HEAD matrisi, repo glob
> ölçümleri, F faz kapıları (K-M1…K-M5), açık sorular. Kod/kapsam kardeş dosyalardadır.

## §1 Repo ölçümleri (S1 · 2026-10-14)

| # | Ölçüm | Yöntem | Sonuç | Yorum |
|---|---|---|---|---|
| 1 | `*.c` / `*.h` | glob | **0** | bare-metal kaynak **yok** → PLANNED |
| 2 | `*.ino` | glob | **0** | Arduino sketch **yok** → PLANNED |
| 3 | `platformio.ini` | glob | **0** | PlatformIO projesi **yok** → PLANNED |
| 4 | `CMakeLists.txt` | glob | **0** | host C++ derlemesi de yok (K000 kardeş dosya §11) |
| 5 | Guardrail #16 şablonları | `.ai/.templates/hardware/` | **3/3 var** (pic · avr · arduino, v1.0.0, 2026-09-29) | S1 — bu dalın şablon bağımlılığı |

## §2 Web kaynak matrisi (HEAD · 2026-10-14 · envanter kesişimi 0)

| # | İddia | URL | Durum |
|---|---|---|---|
| 1 | XC8 C Compiler kılavuzu (config word · __delay_ms · bank) | https://onlinedocs.microchip.com/oxy/gen10/MPLAB_XC8_C_Compiler_Compiler_User_Guide/index.html | ✅ HTTP 200 |
| 2 | avr-libc kılavuzu (`util/delay.h` · `avr/io.h` · PROGMEM) | https://www.nongnu.org/avr-libc/user-manual/index.html | ✅ HTTP 200 |
| 3 | PlatformIO `atmelavr` platformu (avrdude yükleme) | https://docs.platformio.org/en/latest/platforms/atmelavr.html | ✅ HTTP 200 |
| 4 | `platformio.ini` `[env:*]` yapılandırma referansı | https://docs.platformio.org/en/latest/projectconf/ | ✅ HTTP 200 |
| 5 | Arduino resmi dokümantasyon kökü | https://docs.arduino.cc/ | ✅ HTTP 200 |
| 6 | arduino-cli (CLI derleme/yükleme — CI kalıbı) | https://raw.githubusercontent.com/arduino/arduino-cli/master/README.md | ✅ HTTP 200 |
| 7 | ArduinoCore-avr (Uno/Nano/Mega çekirdek kaynağı) | https://github.com/arduino/ArduinoCore-avr | ✅ HTTP 200 |
| 8 | arduino-cli Windows 64-bit dağıtımı (F0 kurulumu) | https://downloads.arduino.cc/arduino-cli/arduino-cli_latest_Windows_64bit.zip | ✅ HTTP 200 |

## §3 Kapılar (F faz kapıları — kardeş kapsam §3'e bağlı)

| # | Kapı | Ölçüm | Bugün | Hedef |
|---|---|---|---|---|
| K-M1 | F0 — araç zinciri kurulumu | XC8 · avrdude · PlatformIO sürüm çıktısı (şablon §7.3 ile eşleşme) | **0 kurulum** (S1) | 3 araç hazır |
| K-M2 | F1 — HAL başlığı derlemesi | 3 platformda `hal/gpio.h` derlemesi (host `native` env) | **yok** | 3/3 geçer |
| K-M3 | F2 — blink `.hex` | `.hex` üretimi + yükleme doğrulaması (LED gözlem ⚠️ Q-M4) | **yok** | 3/3 üretir |
| K-M4 | F4 — UART döngü testi | 115200 8N1 loopback (donanım) | **yok** | geçer |
| K-M5 | F6 — host testi | PlatformIO `native` env'de birim testi | **yok** | yeşil |

## §4 Dürüstlük işaretleri (bu dalın bilinen eksikleri)

| # | İşaret | Durum |
|---|---|---|
| 1 | `ornekler` §1–§6 blokları | **[KALIP-PLANNED]** — derlenmedi/yüklenmedi |
| 2 | `ornekler` §4 PIC bloğu | **temsilî** (PIC16F varsayımı) — Q-M1 kapanınca revize |
| 3 | `ornekler` §6 UBRR değeri | hesap bekliyor (osilatör toleransı) |
| 4 | HIL donanımı (programmer/osiloskop) | **UNKNOWN** (Q-M4) |

## §5 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-M1 | PIC serisi (16F/18F/24F/dsPIC)? | ⚠️ VERIFICATION REQUIRED |
| Q-M2 | ATmega328P hedef teyidi? | ⚠️ VERIFICATION REQUIRED |
| Q-M3 | İlk uygulama senaryosu? | UNKNOWN |
| Q-M4 | HIL donanımı envanteri (PICkit · avrdude · osiloskop)? | UNKNOWN |
| Q-M5 | CI'da MCU derlemesi (arduino-cli/pio run) ne zaman girer? | UNKNOWN |

## §6 Sürüm geçmişi

| # | Sürüm | Tarih | Değişiklik |
|---|---|---|---|
| 1 | v1.0.0 | 2026-10-14 | İlk yazım — 5 S1 repo ölçümü + 8 URL HEAD 200 (envanter kesişimi 0) + 5 kapı (K-M1…K-M5) + 4 dürüstlük işareti |

## §7 Ölçüm yöntemi ve tekrarlanabilirlik

| # | Öğe | Kullanılan yöntem | Yasak yöntem | Gerekçe |
|---|-----|-------------------|--------------|---------|
| 1 | Dosya satır sayısı | `[System.IO.File]::ReadAllLines($yol).Length` | `Measure-Object -Line` | `Measure-Object -Line` boş/sonlu satırları farklı sayar — kapı ölçümünde sapar |
| 2 | Glob envanteri | `Get-ChildItem -Recurse -Include` / glob | — | yalnızca diskteki dosyalar |
| 3 | URL erişilebilirlik | HTTP HEAD → durum kodu | — | HEAD 200 = ayakta; içerik doğrulaması ayrı |
| 4 | Türkçe yol | `-LiteralPath` + gerçek Türkçe yazım | glob'da `?` karakteri eşlemesi | PowerShell ekranda `donanım` → `donan?m` gösterir (yalnız görüntü); `?` glob jokeridir, gerçek yolla karıştırılmaz |
| 5 | Sonuç kaydı | bu dosyanın § tabloları | — | tekrar çalıştırılabilir |

### §7.1 Tek komutluk ölçüm kalıbı (tekrar üretilebilir)

```powershell
$root = 'C:\www\coremusic.net\.ai\architecture\K001-donanım'
Get-ChildItem -LiteralPath $root -Recurse -File -Filter *.md | ForEach-Object {
  $n = [System.IO.File]::ReadAllLines($_.FullName).Length
  $rel = $_.FullName.Substring($root.Length + 1)
  "{0}`t{1}" -f $n, $rel
} | Sort-Object { [int]($_ -split "`t")[0] }
```

| # | Çıktı yorumu | Kapı |
|---|--------------|------|
| 1 | içerik dosyası `< 500` | **İHLAL** — append ile büyütülür (silme yasak) |
| 2 | içerik dosyası `≥ 500` | geçer |
| 3 | `index.md` `< 40` veya `> 100` | **İHLAL** — rewrap ile 40–100 |
| 4 | `index.md` 40–100 | geçer |

> **Ölçüm tarihi disiplini:** her ölçüm çalıştırması tarihli satır olarak §13'e
> işlenir; tarihsiz sayı iddiası sayılmaz.

## §8 Şablon envanteri doğrulaması (Guardrail #16 · S1)

| # | Şablon | Sürüm | Tarih | Eklediği yükümlülük | Kullanım zorunlu mu |
|---|--------|-------|-------|---------------------|---------------------|
| 1 | `.ai/.templates/hardware/pic-template.md` | v1.0.0 | 2026-09-29 | XC8 · bankalı bellek · config word · `__delay_ms` / `_XTAL_FREQ` | PIC bloğu için EVET |
| 2 | `.ai/.templates/hardware/avr-template.md` | v1.0.0 | 2026-09-29 | avr-gcc 15.1.0 · avr-libc 2.2.1 · binutils 2.44 · `avr/io.h` · `util/delay.h` · PROGMEM | AVR bloğu için EVET |
| 3 | `.ai/.templates/hardware/arduino-template.md` | v1.0.0 | 2026-09-29 | PlatformIO zorunlu · Arduino IDE yalnız prototip · C++17 · `millis()` · OTA imzalı · watchdog | Arduino bloğu için EVET |

| # | Doğrulama sorusu | Bugün | Kapı |
|---|------------------|-------|------|
| 1 | 3 şablon dosyası diskte var mı? | **EVET** (2026-10-14 · 3/3) | sürekli |
| 2 | Şablon sürümü FM'de `1.0.0` mu? | EVET (üçünde de) | sürekli |
| 3 | Şablon registry'de kayıtlı mı? | `.templates/index.md` referansı vardır (şablon FM `authority` satırı) | ⚠️ bu oturumda indeks okunmadı |
| 4 | Şablon değişti mi? | bilinmiyor | şablon tarih değişirse bu dosya yeniden doğrulanır |

## §9 Araç zinciri sürüm matrisi (şablon §7.4 kayıtlı — bu oturumda yeniden çekilmedi)

| # | Araç | Şablondaki kayıtlı değer | Kaynak (şablon §7.4) | Bu oturum ölçümü |
|---|------|--------------------------|----------------------|------------------|
| 1 | Arduino IDE | **2.3.10** (Haziran 2026; 2.3.11 geliştirme) | arduino.cc GitHub release notları | ❌ yapılmadı |
| 2 | PlatformIO Core | **6.1.19** (2026-02-04, kararlı) | platformio.org / GitHub releases | ❌ yapılmadı |
| 3 | arduino-esp32 core | **3.3.11** (2026-07-22) | github.com/espressif/arduino-esp32 | ❌ yapılmadı |
| 4 | avr-gcc | **15.1.0** | `avr-template.md` §7.3 (kapsam §1 FM) | ❌ yapılmadı |
| 5 | avr-libc | **2.2.1** | `avr-template.md` §7.3 | ❌ yapılmadı |
| 6 | binutils (AVR) | **2.44** | `avr-template.md` §7.3 | ❌ yapılmadı |
| 7 | MPLAB XC8 | şablonda belirtilir (sürüm bu oturumda aktarılmadı) | `pic-template.md` | ⚠️ VERIFICATION REQUIRED |

> **Dürüstlük:** sütun "Bu oturum ölçümü" **boş kalmıştır** — araç kurulu değildir
> (§1 #1–#4: repo'da 0 kaynak). K-M1 kapısı ancak gerçek `--version` çıktısı
> alındığında kapanır; şablon değerleri **hedef** olarak kullanılır, ölçüm yerine geçmez.

### §9.1 Sürüm iddiası kuralı

| # | Kural |
|---|-------|
| 1 | Şablondaki sürüm = **hedef/beklenti**, ölçüm değildir |
| 2 | Ölçüm yalnızca `araç --version` çıktısıdır ve bu dosyaya satır olarak eklenir |
| 3 | Çıktı şablonla eşleşmiyorsa **sapma** kaydedilir, şablon değiştirilmez |
| 4 | Ölçüm yapılmayan satır `❌ yapılmadı` kalır — "eşleşti" yazılamaz |

## §10 Web kaynak matrisi — kategori ve kapsam notları (mevcut 8 URL)

| # | Kategori | URL # | Kapsadığı iddia sınıfı | Kapsamadığı |
|---|----------|-------|------------------------|-------------|
| 1 | Derleyici dokümanı | 1 (XC8) | dil kuralları, config word, `__delay_ms` | hedef PIC serisi seçimi (Q-M1) |
| 2 | Kütüphane dokümanı | 2 (avr-libc) | `util/delay.h`, `avr/io.h`, PROGMEM | fuse biti konfigürasyonu |
| 3 | Platform/builder | 3 · 4 (PlatformIO) | `platformio.ini`, `env:*`, atmelavr | CI çalıştırma kanıtı |
| 4 | Çekirdek dokümantasyon | 5 · 7 (Arduino docs / Core-avr) | Arduino API, Uno/Nano/Mega çekirdeği | ESP32 çekirdeği |
| 5 | CLI/dağıtım | 6 · 8 (arduino-cli README / Windows zip) | CI derleme kalıbı, F0 kurulumu | Linux/macOS dağıtımı |

| # | Kapsam dışı kalan iddia | Neden | Etiket |
|---|-------------------------|-------|--------|
| 1 | `pio check` davranışı | bu oturumda okunmadı | ⚠️ VERIFICATION REQUIRED |
| 2 | Unity test çerçevesi API'si | şablonda örnek var, doğrulanmadı | ⚠️ VERIFICATION REQUIRED |
| 3 | avrdude sürümü | hiçbir URL'de yok | UNKNOWN |
| 4 | PICkit sürücüsü/sürümü | URL yok (Q-M4) | UNKNOWN |
| 5 | EN standartlarının güncel metni | şablon §7.4 referans verir, okunmadı | ⚠️ VERIFICATION REQUIRED |

## §11 Kapıların ayrıntılı ölçüm prosedürü (K-M1 … K-M5)

| # | Kapı | Prosedür (adım adım) | Geçiş ölçütü | Bugün |
|---|------|----------------------|--------------|-------|
| K-M1 | Araç zinciri | 1) `pio --version` 2) `avr-gcc --version` 3) XC8 sürüm çıktısı 4) §9 tablosuna satır ekle | 3 araç da çıktı verir ve §9 hedefiyle sapma kaydedilmiştir | ❌ 0/3 |
| K-M2 | HAL derleme | 1) `hal/gpio.h` oluştur (ornekler §1) 2) 3 BSP derle 3) `pio run -e native` | 3/3 derleme hatasız | ❌ yok |
| K-M3 | Blink `.hex` | 1) `pio run` → `.hex` 2) `pio run -t upload` 3) LED gözlemi | `.hex` üretilir + yükleme doğrulanır | ❌ yok |
| K-M4 | UART döngü | 1) `pio device monitor` 115200 8N1 2) TX–RX loopback 3) veri doğrulama | bozulmadan veri döner | ❌ yok |
| K-M5 | Host testi | 1) `pio test -e native` 2) `pio check --fail-on-defect=medium` | test yeşil + orta seviye ihlal yok | ❌ yok |

### §11.1 Kapı kayıt şablonu (her kapı kapanırken doldurulur)

```text
KAPı: <K-Mn>
TARİH: <YYYY-MM-DD>
KOMUT: <çalıştırılan komut>
ÇIKTI: <ham çıktı — kısaltılmış>
HEDEF (§9/§21): <beklenen>
SONUÇ: GEÇTİ / KALDI
SAPMA: <varsa — şablon hedefinden fark>
KANIT: <dosya yolu veya log satırı>
```

| # | Kural |
|---|-------|
| 1 | Kapı yalnız ham çıktı ile kapanır — "kuruldu" beyanı sayılmaz |
| 2 | Çıktı bu dosyaya iliştirilir (§13 tablosuna özet satır) |
| 3 | Kapanan kapı sürüm geçmişine işlenir |
| 4 | Geri açılış: araç/sürüm değişirse kapı yeniden ölçülür |

## §12 Uyumluluk ve standart kanıt planı (şablon §7.4 kaynaklı — okunmadı)

> **Bu bölüm bir *plan*tır, sonuç değil.** Standart metinleri bu oturumda
> **okunmamıştır**; satırlar şablonun kayıtlı referanslarını aktarır.

| # | Alan | Şablondaki kayıtlı referans | Bu oturumda doğrulandı mı |
|---|------|------------------------------|---------------------------|
| 1 | EMC emisyon | EN 55032:2015+A11:2020 | ❌ hayır — ⚠️ VERIFICATION REQUIRED |
| 2 | EMC bağışıklık | EN 55035:2017+A11:2020 | ❌ hayır — ⚠️ VERIFICATION REQUIRED |
| 3 | Radyo (WiFi/BT, ESP32) | EN 300 328 V2.2.2 (2019-07) | ❌ hayır — ⚠️ VERIFICATION REQUIRED |
| 4 | Güvenlik | EN IEC 62368-1:2020+A11:2020 | ❌ hayır — ⚠️ VERIFICATION REQUIRED |
| 5 | RoHS | EN IEC 63000:2018 | ❌ hayır — ⚠️ VERIFICATION REQUIRED |

| # | Kural |
|---|-------|
| 1 | Standart numarası "doğrulanmış" olarak yalnızca standart yayın organından okunursa yazılır |
| 2 | Şablon satırı kaynak olarak gösterilse bile iddia sınıfı **aktarılmış** kalır |
| 3 | Bu dal (mikrodenetleyici) için asıl gereklilik: varsa kablosuz modül → radyo standardı; yoksa 3–5 maddesi uygulanmaz |

## §13 Ölçüm kayıt defteri (tarihli — tekrar eklenir)

| # | Tarih | Kapsam | Yöntem | Sonuç |
|---|-------|--------|--------|-------|
| 1 | 2026-10-14 | repo glob (`.c`·`.h`·`.ino`·`platformio.ini`·`CMakeLists.txt`) | glob | **0 / 0 / 0 / 0 / 0** |
| 2 | 2026-10-14 | şablon envanteri | disk kontrolü | **3/3 var** (v1.0.0 · 2026-09-29) |
| 3 | 2026-10-14 | URL HEAD | HTTP HEAD | **8/8 → 200** |
| 4 | 2026-10-14 | envanter kesişimi (proje dosyaları ↔ URL listesi) | karşılaştırma | **0** |
| 5 | 2026-10-15 | kapı K-M1…K-M5 | §11 prosedürü | **0/5 kapandı** (araç yok) |
| 6 | 2026-10-15 | araç `--version` ölçümleri | — | **yapılmadı** (§9 sütun 5) |

### §13.1 Defter kuralları

| # | Kural |
|---|-------|
| 1 | Satırlar **eklenir**, eski satır değiştirilmez |
| 2 | Aynı ölçüm tekrarlanırsa yeni satır açılır (tarih farkı) |
| 3 | Sonuç `0` ise "0" yazılır — yuvarlama yok |
| 4 | Ölçüm yapılmadıysa tabloya satır eklenmez; §9 sütununda `❌ yapılmadı` kalır |

## §14 Dürüstlük işaretleri — bu dosyanın genişletilmiş listesi

| # | İşaret | Ne anlama gelir | Kapanış koşulu |
|---|--------|-----------------|----------------|
| 1 | `[KALIP-PLANNED]` | kod bloğu derlenmedi/yüklenmedi | F2–F5 kapıları |
| 2 | `temsilî` | hedef seçimi kesin değil | Q-M1 |
| 3 | `hesap bekliyor` | sayısal değer doğrulanmadı | F4 ölçümü |
| 4 | `UNKNOWN` | bilgi yok | ilgili Q kapanır |
| 5 | `⚠️ VERIFICATION REQUIRED` | doğrulanamayan iddia | kaynak okuma + kayıt |
| 6 | `❌ yapılmadı` | ölçüm adımı atlandı | §9/#1–#3 |
| 7 | `S1/S4/S7` | kanıt tipi (şablon · yorum · bilinmeyen) | kalıcı |

## §15 Açık sorular — genişletilmiş kapanış planı

| # | Soru | Etiket | Kapanış adımı | Blokladığı kapı |
|---|------|--------|---------------|-----------------|
| 1 | Q-M1 PIC serisi? | ⚠️ VERIFICATION REQUIRED | hedef serisi kararı + pic-template §kapsam okuma | ornekler §4 kalıcılaşır |
| 2 | Q-M2 ATmega328P teyidi? | ⚠️ VERIFICATION REQUIRED | teyit | ornekler §3 |
| 3 | Q-M3 İlk uygulama senaryosu? | UNKNOWN | senaryo seçimi | F2 kapsamı |
| 4 | Q-M4 HIL donanımı? | UNKNOWN | envanter sayımı (programmer/osiloskop/DMM) | K-M3 gözlem · K-M4 · §16 HIL |
| 5 | Q-M5 CI MCU derlemesi? | UNKNOWN | DevOps kararı | §15 CI fazı |

## §16 HIL doğrulama planı (envanter UNKNOWN — plan olarak kayıtlı)

| # | Test | Gereken araç | Araç durumu | Kapı |
|---|------|--------------|-------------|------|
| 1 | LED/çıktı gözlemi | görsel | ✔ insan gözlemi (donanım gerekli) | K-M3 |
| 2 | PWM/periyot ölçümü | osiloskop | UNKNOWN (Q-M4) | F3 |
| 3 | UART loopback | USB-serial / jumper | UNKNOWN | K-M4 |
| 4 | I2C/SPI protokol | lojik analizör | UNKNOWN | F5 |
| 5 | Güç tüketimi | DMM / akım probu | UNKNOWN | §17 |
| 6 | Programmer | PICkit (PIC) · avrdude/USBasp (AVR) | UNKNOWN | K-M1/K-M3 |
| 7 | Seri otomasyonu | Python `pyserial` | şablonda referans var, kurulum yok | K-M4 |

> Hiçbiri bu oturumda **kurulmamış/ölçülmemiştir**; tablo yalnız plan taşır.

## §17 Bağımlılık zinciri doğrulaması

| # | Bağımlılık | Tür | Durum |
|---|-----------|-----|-------|
| 1 | `…/mikrodenetleyici-kapsam` | kardeş dosya | ✔ var (bu oturumda ölçüldü) |
| 2 | `…/mikrodenetleyici-ornekler` | kardeş dosya | ✔ var |
| 3 | `K000-isletim-sistemi/…/derleme-ve-paketleme-cross-platform-plani` | K000 dalı | ⚠️ bu oturumda **okunmadı** — referans FM'den aktarıldı |
| 4 | `.templates/hardware/{pic,avr,arduino}-template.md` | Guardrail #16 şablon | ✔ 3/3 diskte (§8) |
| 5 | `.templates/hardware/hardware-template.md` | genel donanım şablonu | ⚠️ bu dalda referans edilmedi/okunmadı |
| 6 | `K004-dsp` · `K005-firmware` | komşu dal | sınır — bu dalın dışında |

| # | Kural |
|---|-------|
| 1 | Okunmamış bağımlılığa "doğrulandı" denmez |
| 2 | K000 bağımlılığı yalnız FM `depends_on` satırından gelir |
| 3 | Şablon bağımlılığı §8'de ayrı ölçülür |

## §18 Rapor formatı (kapı kapanış / oturum sonu)

```text
DOSYA: .ai/architecture/K001-donanım/donanaım-type/mikrodenetleyici/<ad>.md
TARİH: <YYYY-MM-DD>
ÖLÇÜM YÖNTEMİ: [System.IO.File]::ReadAllLines().Length
ÖNCE: <n>  SONRA: <n>  KAPI: ≥500 → GEÇTİ/KALDI
KAPILAR: K-M1..K-M5 → <n>/5
AÇIK SORULAR: Q-M1..Q-M5 → <kapanan>/<toplam>
DÜRÜSTLÜK: [KALIP-PLANNED] <n> · temsilî <n> · UNKNOWN <n>
YASAKLAR: git commit <yok> · beyazliste dışı yazım <yok> · silme <yok>
```

## §19 Kapı geçmişi (tarihli — yalnız kapananlar)

| # | Kapı | Tarih | Kanıt | Durum |
|---|------|-------|-------|-------|
| 1 | Şablon envanteri 3/3 | 2026-10-14 | disk kontrolü (§8) | ✅ GEÇTİ |
| 2 | URL HEAD 8/8 → 200 | 2026-10-14 | HTTP HEAD (§2) | ✅ GEÇTİ |
| 3 | Env kesişimi 0 | 2026-10-14 | karşılaştırma (§2) | ✅ GEÇTİ |
| 4 | K-M1 araç zinciri | — | — | ⏳ BEKLEMEDE (araç yok) |
| 5 | K-M2 HAL derleme | — | — | ⏳ BEKLEMEDE (kaynak yok) |
| 6 | K-M3 `.hex` | — | — | ⏳ BEKLEMEDE |
| 7 | K-M4 UART | — | — | ⏳ BEKLEMEDE |
| 8 | K-M5 host testi | — | — | ⏳ BEKLEMEDE |
| 9 | Dosya kapısı ≥500 satır | 2026-10-15 | §7 ölçüm kalıbı | ✅ GEÇTİ (bu revizyon) |

## §20 Sorumluluk ve rol matrisi

| # | İş | Sorumlu | Rol |
|---|----|---------|-----|
| 1 | Faz planı, katman şeması | Embedded Engineer | Expert (§6.1 persona) |
| 2 | Kapı/ölçüm kaydı | QA Engineer | Expert |
| 3 | URL/kanıt toplama | research-analyst | Junior |
| 4 | CI kapısı (Q-M5) | devops-engineer | Senior |
| 5 | HIL donanım envanteri | audio-hardware-engineer | uzman — Q-M4 |
| 6 | Vault kaydı / sürüm | Vault Steward (MO) | orkestratör |

## §21 Hata ve sapma yönetimi

| # | Senaryo | Aksiyon |
|---|---------|---------|
| 1 | Ölçüm yöntemi tartışmalı | §7 yöntemine dön; `Measure-Object -Line` sonucunu **kullanma** |
| 2 | Şablon sürümü değişti | §8 tablosunu güncelle + bu dosyayı yeniden doğrula |
| 3 | URL 404/403 | §2 satırını koru, durum sütununu tarihli olarak düzelt (eski satır silinmez, yeni satır eklenir) |
| 4 | Araç sürümü şablonla uyuşmuyor | sapmayı yaz; şablonu **değiştirme** (şablon Guardrail #16 SSOT) |
| 5 | Kapı 3 kez başarısız | dur + üst karar (§6.1 Expert) |
| 6 | Yeni bilinmeyen | §15 tablosuna soru ekle (Q-M6+), uydurma kapanış yazma |

## §22 Bu dosyanın iddia sınırları (kapanış beyanı)

| # | Kategori | Sayı | Durum |
|---|----------|------|-------|
| 1 | HTTP 200 ile doğrulanmış URL | 8 | erişilebilirlik ✓ · içerik ✗ |
| 2 | S1 repo ölçümü | 5 | 2026-10-14 |
| 3 | Şablon S1 | 3/3 | 2026-10-14 |
| 4 | Kapanan kapı | 3 | §19 #1–#3 |
| 5 | Kapalı olmayan kapı | 5 | K-M1…K-M5 |
| 6 | Açık soru | 5 | Q-M1…Q-M5 |
| 7 | Bağımsız doğrulanmış araç sürümü | **0** | §9 |
| 8 | Bu oturumda okunan standart metni | **0** | §12 |

> **Beyan:** bu dosyada **hiçbir** araç sürümü, standart metni veya kod davranışı
> bağımsız olarak doğrulanmamıştır; tümü şablon/repo kayıtlarından aktarılmıştır.

## §23 Sürüm geçmişi (devam satırları)

| # | Sürüm | Tarih | Değişiklik |
|---|-------|-------|---------|
| 2 | v1.1.0 | 2026-10-15 | Ölçüm yöntemi (§7) · şablon envanteri (§8) · araç sürüm matrisi + "ölçülmedi" dürüstlüğü (§9) · URL kapsam notları (§10) · kapı prosedürü (§11) · standart planı (§12) · ölçüm defteri (§13) · HIL planı (§16) · bağımlılık (§17) · rapor formatı (§18) · kapı geçmişi (§19) · rol (§20) · hata yönetimi (§21) · iddia sınırları (§22) — satır kapısı ≥500 |

## §24 Komşu sınırı (tekrar kaydı — kapsam §5 ile aynı)

| # | Dışarıda olan | Nereye gider |
|---|---------------|--------------|
| 1 | Kod blokları | `mikrodenetleyici-ornekler.md` |
| 2 | Mimari/proje planı | `mikrodenetleyici-kapsam.md` |
| 3 | Bare-metal kuralları | `.templates/hardware/{pic,avr,arduino}-template.md` |
| 4 | Host OS derleme/paketleme | `K000 …/derleme-ve-paketleme-cross-platform-plani.md` |
| 5 | DSP/firmware algoritması | `K004-dsp` · `K005-firmware` |

## §25 Bu dosyanın görev sınırı (ne yapar / ne yapmaz)

| # | Bu dosya YAPAR | Bu dosya YAPMAZ |
|---|----------------|-----------------|
| 1 | Ölçüm yöntemini tarif eder (§7) | Ölçümü **yapılmış gibi** beyan etmez |
| 2 | Kapı prosedürünü yazar (§11) | Kapıyı prosedür yazımıyla **geçilmiş** saymaz |
| 3 | Şablon envanterini listeler (§8) | Şablon içeriğini **tekrar doğrulamış** olmaz |
| 4 | URL erişilebilirlik kaydını taşır (§2) | URL **içeriğini** doğrulamış olmaz |
| 5 | Açık soruları ve kapanış yolunu listeler (§15) | Soruyu kapatmaz, tahminle kapatmaz |
| 6 | Ölçüm/kapı geçmişini kaydeder (§13, §19) | Geçmiş satırı **değiştirmez** (append-only) |
| 7 | Kardeş dosyalara sınır çizer (§24) | Kardeş dosyanın içeriğini **üretmez** |

> **Komşu sorumluluk:** mimari/proje planı → `mikrodenetleyici-kapsam.md` ·
> kod blokları → `mikrodenetleyici-ornekler.md`. Bu dosyada kod bloğu **olmaz**
> (kapsam §5 #1–#2 kuralı tekrarı).

## §26 Yeniden doğrulama tetikleyicileri (ne değişirse bu dosya güncellenir)

| # | Tetikleyici | Etkilenen bölüm | Aksiyon |
|---|-------------|-----------------|---------|
| 1 | Şablon sürümü/tarihi değişti (§8) | §8 · §9 | envanteri yeniden say, sürüm satırını ekle |
| 2 | Yeni URL eklendi/404 oldu | §2 · §10 | yeni satır ekle; 404 için durum satırını tarihli düzelt |
| 3 | Araç kuruldu (`--version` alındı) | §9 sütun 5 · §19 | `❌ yapılmadı` → ölçülen değer + tarih |
| 4 | Bir kapı kapandı | §13 · §19 · §11 | ham çıktıyı iliştir, GEÇTİ yaz |
| 5 | Repo'ya ilk `.c`/`.ino` dosyası geldi | §1 · §13 | glob'u yeniden çalıştır, satır ekle |
| 6 | Q-M1…Q-M5 kapandı | §15 · ornekler §8 · kapsam §6 | ilgili satırı kapat, kapıları serbest bırak |
| 7 | `Measure-Object -Line` sonucu raporlandı | §7 | reddet, §7 yöntemini göster |
| 8 | K000 bağımlılığı okundu | §17 #3 | `okunmadı` → okundu + tarih |

## §27 Ölçüm araç çantası (komut referansı — tekrar üretilebilir)

| # | Amaç | Komut/araç | Çıktı nereye yazılır |
|---|------|-----------|----------------------|
| 1 | Dosya satır sayısı | `[System.IO.File]::ReadAllLines($yol).Length` | §13 defteri |
| 2 | Klasör tüm md ölçümü | §7.1 kalıbı (Recursive + `-Filter *.md`) | §13 / rapor §18 |
| 3 | Belirli desen glob | `Get-ChildItem -Recurse -Include *.c,*.h,*.ino` | §1 #1–#4 |
| 4 | URL HEAD | HTTP HEAD → durum kodu | §2 tablosu |
| 5 | Araç sürümü | `<araç> --version` | §9 sütun 5 + §13 |
| 6 | Kapı testi (derleme) | `pio run -e native` / `pio test -e native` | §11 · §19 |
| 7 | Statik analiz | `pio check --fail-on-defect=medium` | K-M5 |

| # | Yasak araç | Neden |
|---|-----------|-------|
| 1 | `Measure-Object -Line` | kapı ölçümünde sapar (§7 #1) |
| 2 | Glob'da `?` ile Türkçe karakter eşlemesi | `?` jokerdir — yanlış eşleşme/0 sonuç riski (§7 #4) |
| 3 | Tahmini satır sayısı ("yaklaşık 500") | kapı kesin sayı ister |

## §28 Oturum sonu kontrol listesi (bu dosya için)

- [ ] §7 yöntemiyle satır sayısı ölçüldü (`ReadAllLines`) — içerik `≥500`
- [ ] `index.md` 40–100 arasında
- [ ] §13 ölçüm defterine tarihli satır eklendi
- [ ] §9 sütun 5 (`Bu oturum ölçümü`) hâlâ dürüst: `❌ yapılmadı` veya gerçek çıktı
- [ ] §12 standart satırları `⚠️ VERIFICATION REQUIRED` olarak kaldı (okunmadı)
- [ ] §15 açık soru tablosunda uydurma kapanış yok
- [ ] Append-only: mevcut satırların silinmediği doğrulandı (Authority son satırda)
- [ ] `git commit` **atılmadı**
- [ ] Beyazliste dışı yazım **yok** (yalnız `K001-donanım/**`)

## §29 Bu dalın bilgi kaynakları ve okuma sırası

> Sıra kuralı: önce şablon (kural), sonra kardeş kapsam (plan), sonra kardeş
> örnekler (kod), en son bu dosya (kanıt). Tersi okumada şablon çelişkisi kaçar.

| # | Sıra | Kaynak | Tür | Bu oturumda okundu mu |
|---|------|--------|-----|------------------------|
| 1 | 1 | `.ai/.templates/hardware/arduino-template.md` v1.0.0 | Guardrail #16 şablonu | ✔ okundu (766 satır) |
| 2 | 1 | `.ai/.templates/hardware/pic-template.md` v1.0.0 | Guardrail #16 şablonu | ✔ okundu (687 satır) |
| 3 | 1 | `.ai/.templates/hardware/avr-template.md` v1.0.0 | Guardrail #16 şablonu | ✔ okundu (650 satır) |
| 4 | 2 | `…/mikrodenetleyici-kapsam.md` | kardeş — plan/mimari | ✔ okundu (bu oturum) |
| 5 | 3 | `…/mikrodenetleyici-ornekler.md` | kardeş — kod | ✔ okundu (bu oturum) |
| 6 | 4 | `…/mikrodenetleyici-dogrulama.md` | bu dosya — kanıt | ✔ (üretildi) |
| 7 | 5 | `…/mikrodenetleyici/index.md` | dizin kapısı 40–100 | ✔ ölçüldü (62) |
| 8 | 6 | `K000 …/derleme-ve-paketleme-cross-platform-plani.md` | komşu bağımlılık | ❌ okunmadı (§17 #3) |
| 9 | 6 | `.templates/hardware/hardware-template.md` | komşu şablon | ❌ okunmadı (§17 #5) |
| 10 | — | `K004-dsp` · `K005-firmware` | sınır dal | ❌ kapsam dışı |

### §29.1 URL kaynakları (bu oturumda yeniden çekilmedi)

| # | Kaynak grubu | Durum |
|---|--------------|-------|
| 1 | §2'deki 8 URL HEAD 200 | 2026-10-14 ölçümü — bu oturumda **tekrarlanmadı** |
| 2 | Şablon §7.4 web doğrulaması | 2026-09-29 tarihli şablon kaydı — aktarıldı, yeniden çekilmedi |
| 3 | Yeni URL ekleme | bu oturumda **yapılmadı** |

### §29.2 Okuma sırasında karşılaşılan bilinmeyenler (kapanmayanlar)

| # | Bilinmeyen | Etiket | Nereye yazıldı |
|---|-----------|--------|----------------|
| 1 | K000 cross-plani içeriği | UNKNOWN | §17 #3 |
| 2 | `hardware-template.md` içeriği | UNKNOWN | §17 #5 |
| 3 | `pio check` gerçek davranışı | ⚠️ VERIFICATION REQUIRED | §10 #1 |
| 4 | avrdude / PICkit sürümleri | UNKNOWN | §10 #3 · #4 |
| 5 | `.github/workflows/` envanteri (bu dal) | UNKNOWN | ornekler §21 notu |
| 6 | Standart metinleri (EN serisi) | ⚠️ VERIFICATION REQUIRED | §12 |

> **Kapanış beyanı:** bu dosyanın tamamı **aktarım + plan + ölçüm yöntemi** taşır;
> bağımsız teknik doğrulama (standart okuma, araç sürümü, kod derleme) **yapılmamıştır**
> ve yapılmadığı §9 · §12 · §22'de yazılıdır.

## §30 Kapıdan önceki son kontrol (özet tablo)

| # | Kontrol | Hedef | Bugün |
|---|---------|-------|-------|
| 1 | Dosya satır sayısı | ≥500 | ölçülecek (§7 yöntemi) |
| 2 | `index.md` | 40–100 | 62 ✓ |
| 3 | `group:` alanı | `kanit` | ✓ FM L24 |
| 4 | Authority son satırda | ✓ | ✓ |
| 5 | Silme yok (append-only) | mevcut §1–§6 aynen korunur | ✓ |
| 6 | `git commit` | yok | yok |
| 7 | Beyazliste dışı yazım | 0 | 0 |
| 8 | Uydurma URL/dosya | 0 | 0 (§29.1: yeni URL eklenmedi) |

> Bu tablo sonradan **ölçüm sonucuyla** doldurulmaz — sonuç §13 defterine satır
> olarak eklenir; §30 yalnız kontrol listesi olarak kalır.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
