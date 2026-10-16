---
node_id: K001-donanım/donanaım-type/mikrodenetleyici/mikrodenetleyici-kapsam
node_type: module
status: draft
parent_id: K001-donanım/donanaım-type/mikrodenetleyici
children: []
depends_on:
  - "K001-donanım/donanaım-type/mikrodenetleyici/mikrodenetleyici-dogrulama"
  - "K000-isletim-sistemi/isletim-sistemi-cross/derleme-ve-paketleme/derleme-ve-paketleme-cross-platform-plani"
evidence:
  - ".ai/.templates/hardware/pic-template.md v1.0.0 (2026-09-29) — XC8 · bankalı bellek · config word"
  - ".ai/.templates/hardware/avr-template.md v1.0.0 (2026-09-29) — avr-gcc 15.1.0 · avr-libc 2.2.1 · binutils 2.44"
  - ".ai/.templates/hardware/arduino-template.md v1.0.0 (2026-09-29) — PlatformIO zorunlu · Arduino IDE yalnız prototipleme"
  - "https://onlinedocs.microchip.com/oxy/gen10/MPLAB_XC8_C_Compiler_Compiler_User_Guide/index.html (✅ HTTP 200 · 2026-10-14)"
  - "https://www.nongnu.org/avr-libc/user-manual/index.html (✅ HTTP 200 · 2026-10-14)"
  - "https://docs.platformio.org/en/latest/platforms/atmelavr.html (✅ HTTP 200 · 2026-10-14)"
validation: "2026-10-14 · 3 şablon (S1) + 3 URL HEAD 200 · repo glob: .c/.h/.ino/platformio.ini = 0 → içerik PLANNED"
open_questions:
  - "Q-M1 — PIC serisi seçimi (PIC16F/18F/24F/dsPIC) ⚠️ VERIFICATION REQUIRED"
  - "Q-M2 — ATmega328P hedef teyidi bekliyor"
  - "Q-M4 — HIL test donanımı (programmer/UPDI/ICSP) envanteri UNKNOWN"
group: mimari
---

# mikrodenetleyici-kapsam — Kademe-4 Modül (mimari)

> **Kapsam:** PIC · Atmel/ATmega · Arduino entegrasyonlarının **katmanlı mimari
> şeması** ve **proje planı**. Kod blokları kardeş `mikrodenetleyici-ornekler.md`'dedir;
> URL/glob kanıtları kardeş `mikrodenetleyici-dogrulama.md`'dedir. **Kanıt dili:**
> S1 = şablon/repo satırı · S4 = mimari yorum · S7 = bilinmeyen (UNKNOWN).

## §1 Platform matrisi (PIC · AVR · Arduino)

| # | Platform | Aile | Araç zinciri | Yükleme | Şablon (S1) |
|---|---|---|---|---|---|
| 1 | **PIC** | PIC16F · PIC18F · PIC24F/dsPIC | **XC8** (MPLAB X IDE + CLI) | ICSP (PICkit) | `pic-template.md` |
| 2 | **Atmel AVR** | ATmega (328P/4809…) | **avr-gcc 15.1.0** + avr-libc 2.2.1 + binutils 2.44 | ISP / UPDI | `avr-template.md` |
| 3 | **Arduino** | AVR çekirdek (Uno/Nano/Mega) + diğer çekirdekler | **PlatformIO** (IDE yalnız prototipleme) | avrdude (PlatformIO `upload`) | `arduino-template.md` |

> **S1 kural (şablonlar):** Arduino'da **PlatformIO zorunlu**, Arduino IDE yalnız
> prototiplemedir; AVR araç zinciri sürümleri şablon §7.3'te sabittir (Microchip
> resmî + topluluk). PIC'te config word ve bankalı bellek disiplini zorunludur.

### §1.1 Seçim kuralı (S4)

| # | İhtiyaç | Öneri |
|---|---|---|
| 1 | Eğitim/hızlı prototip | Arduino (PlatformIO) |
| 2 | Düşük maliyet · yüksek hacim · 8-bit sade | PIC16F **veya** ATmega328P (Q-M1/Q-M2) |
| 3 | 16-bit DSP/dsensor ağırlıklı | PIC24F/dsPIC (şablon kapsamı) |
| 4 | Modern AVR (UPDI, TCA/TCB) | ATmega4809 (avr-template §migrasyon) |

## §2 Katmanlı mimari (bu dalın çekirdeği)

> Üç platform **aynı üç katmanı** paylaşır; yalnız **Katman-1 (BSP)** platforma özgüdür.

```text
┌─────────────────────────────────────────────────────────┐
│ Katman-3 · Uygulama (app)     — durum makinesi, iş mantığı      │ platform BAĞIMSIZ (C99) │
├─────────────────────────────────────────────────────────┤
│ Katman-2 · HAL / sürücü arayüzü — gpio/uart/timer/adc I/O portları │ platform BAĞIMSIZ (C99) │
├─────────────────────────────────────────────────────────┤
│ Katman-1 · BSP / platform     — XC8 register | avr-libc register │ PLATFORMA ÖZGÜ           │
│                               | Arduino Core (pinMode/digitalWrite)│                        │
└─────────────────────────────────────────────────────────┘
```

| # | Katman | İçerik | Değişimi tetikleyen |
|---|---|---|---|
| 1 | **K3 Uygulama** | blink/ölçüm/protokol durum makinesi | iş gereksinimi |
| 2 | **K2 HAL** | `gpio_init/gpio_write/uart_send/timer_tick` — **tek** C başlığı | yeni özellik |
| 3 | **K1 BSP** | PIC XC8 · ATmega avr-libc · Arduino Core somutlamaları | **platform değişimi** |

> **Kural (S4):** K3 ve K2'de `#ifdef __PICC__`/`#ifdef __AVR__` **YASAK**; platform
> ayrımı **yalnız K1'de** ve derleme dosya listesinde yapılır. Aynısı host OS tespiti
> için kardeş `derleme-ve-paketleme-ide-platform-tespiti.md` §5 #4 kuralıdır.

## §3 Proje planı (7 faz — sıralı, kapılar bağlayıcı)

| # | Faz | Çıktı | Kapı |
|---|---|---|---|
| F0 | Ortam kurulumu | XC8 · avr-gcc · PlatformIO kurulumu (3 OS'ta — kardeş cross-plani §4) | araç sürümü şablon §7.3 ile eşleşir |
| F1 | Scaffold + HAL arayüzü | `hal/gpio.h` · `hal/uart.h` + 3 BSP klasörü iskeleti | HAL başlığı derlenir (3 platform) |
| F2 | GPIO / blink | 3 platformda LED blink (ornekler §2–§4) | **.hex** üretilir + yükleme doğrulanır |
| F3 | Timer / PWM | `hal/pwm.h` + servis/LED sürücüsü | frekans ölçümü (osiloskop ⚠️ Q-M4) |
| F4 | UART telemetri | `hal/uart.h` tam sürüm + telemetry uygulaması | 115200 8N1 döngü testi |
| F5 | Bus (I2C/SPI) | `hal/i2c.h` · `hal/spi.h` — DAC/codec (K001 `audio/cipler`) bağlantısı | cihaz yanıtı okundu |
| F6 | Test + HIL | birim testi (host `native` PlatformIO) + HIL senaryoları | kapılar K-M1…K-M5 |
| F7 | Paket/dağıtım | `.hex` + sürüm + SHA-256 (cross-plani §6 adlandırma) | release kaydı |

> F0 öncesi: bugün repo'da **0 `.c`/`.ino` dosyası** (S1 glob) — plan tamamı **PLANNED**.

## §4 Paketleme/dağıtım (MCU'ya özel — host değil)

| # | Platform | Artefakt | Yükleyici |
|---|---|---|---|
| 1 | PIC | `.hex` | MPLAB X / PICkit (ICSP) |
| 2 | ATmega | `.hex` (+ bootloader) | avrdude (ISP) |
| 3 | Arduino | `.hex` (PlatformIO `pio run -t upload`) | avrdude |

> Adlandırma kardeş `…/derleme-ve-paketleme-cross-platform-plani.md` §6 ile hizalıdır:
> `{ad}-{sürüm}-{mcu}.{hex}` (ör. `neva-node-1.0.0-atmega328p.hex`).

## §5 Komşu sınırı

| # | Dışarıda olan | Nereye gider |
|---|---|---|
| 1 | Kod blokları (blink/UART/PlatformIO ini) | `mikrodenetleyici-ornekler.md` |
| 2 | URL/glob/kapı kanıtları | `mikrodenetleyici-dogrulama.md` |
| 3 | Bare-metal firmware **kuralları** (bank/config word/PROGMEM) | `.templates/hardware/{pic,avr,arduino}-template.md` (Guardrail #16 SSOT) |
| 4 | Host OS derleme/paketleme (Ninja/CPack/MSIX) | `…/derleme-ve-paketleme-cross-platform-plani.md` |
| 5 | DSP algoritması (XMOS/JUCE) | `K004-dsp` · `K005-firmware` |

## §6 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-M1 | PIC serisi (16F/18F/24F/dsPIC)? | ⚠️ VERIFICATION REQUIRED |
| Q-M2 | ATmega328P hedef teyidi? | ⚠️ VERIFICATION REQUIRED |
| Q-M4 | HIL donanımı (PICkit/avrdude programmer/osiloskop)? | UNKNOWN |
| Q-M5 | İlk uygulama senaryosu (LED/ölçüm/telemetri)? | UNKNOWN |

## §7 Sürüm geçmişi

| # | Sürüm | Tarih | Değişiklik |
|---|---|---|---|
| 1 | v1.0.0 | 2026-10-14 | İlk yazım — 3 Guardrail #16 şablonu (S1) + 3 URL HEAD 200; 3 katmanlı mimari (K3/K2/K1); 7 fazlı proje planı (F0–F7) |

## §8 Bus ve çevre birimi seçimi (kaynak: arduino-template §3.4)

> Bu tablo F5 fazındaki `hal/i2c.h` / `hal/spi.h` kararlarının dayanağıdır. Kaynak:
> `.ai/.templates/hardware/arduino-template.md` v1.0.0 §3.4 (S1 · Guardrail #16 şablonu).

| # | Bus | Maks. hız | Menzil | Pin | Tipik yük | Dal karşılığı |
|---|-----|-----------|--------|-----|----------|---------------|
| 1 | I2C | 400 kHz / 3.4 MHz | <1 m | 2 (SDA, SCL) | sensör, EEPROM, ekran | F5 `hal/i2c.h` |
| 2 | SPI | 10–80 MHz | <1 m | 4+ (MOSI, MISO, SCK, CS) | SD kart, yüksek hızlı ADC, TFT | F5 `hal/spi.h` |
| 3 | UART | 2 Mbps'a kadar | <5 m (RS232) | 2 (TX, RX) | GPS, Bluetooth, hata ayıklama | F4 `hal/uart.h` |
| 4 | 1-Wire | 16.3 kbps | <100 m | 1 | sıcaklık sensörü (DS18B20) | kapsam dışı (Q-M5) |
| 5 | CAN | 1 Mbps | <40 m | 2 (CANH, CANL) | otomotiv, endüstriyel | kapsam dışı |

### §8.1 Bus karar kuralları (S4)

| # | Koşul | Karar |
|---|-------|-------|
| 1 | Tek sensör | I2C — minimum kablolama |
| 2 | Aynı adresli birden çok sensör | I2C multiplexer (TCA9548A) **veya** SPI'ye geç |
| 3 | SD kart / dosya sistemi | SPI (SD kütüphanesi şart) |
| 4 | Uzun mesafe (>5 m) | CAN veya RS485 |
| 5 | Basit sıcaklık, ortak bus | 1-Wire (DS18B20) |
| 6 | DAC/codec (K001 `audio/cipler` ailesi) | SPI veri + I2C kontrol kanalı |
| 7 | Yüksek hızlı ADC/dsensor | SPI (10–80 MHz) — I2C 400 kHz darboğazdır |

> **S4 notu:** F5 kapısında "cihaz yanıtı okunamaz" sonucu çıkarsa ilk şüpheli
> **adres çakışması**dır; ikinci şüpheli SDA/SCL seviye kaydırıcı gereksinimidir
> (3.3 V MCU ↔ 5 V çevre). Bu iki senaryo dal risklerine §20'de kayıtlıdır.

## §9 MCU kaynak bütçesi ve seçim matrisi (kaynak: arduino-template §3.3)

| Kriter | ESP32 | Mega2560 | Uno R3 (ATmega328P) |
|--------|-------|----------|---------------------|
| CPU | Çift 240 MHz | 16 MHz | 16 MHz |
| SRAM | 520 KB | 8 KB | 2 KB |
| Flash | 4–16 MB | 256 KB | 32 KB |
| WiFi/BT | Dahili | kalkan gerekli | kalkan gerekli |
| ADC | 12-bit · 18 kanal | 10-bit · 16 kanal | 10-bit · 6 kanal |
| DAC | 2 × 8-bit | yok | yok |
| Maliyet | $3–8 | $10–15 | $5–8 |
| En iyi kullanım | ağ tabanlı sensör, ses | çok-sensör, ekran | basit kontrol, öğrenme |

> **Dayanak:** tablo `arduino-template.md` §3.3 (S1). Fiyat sütunu **şablon kayıtlıdır**;
> güncel fiyat ⚠️ VERIFICATION REQUIRED (fiyat ölçümle doğrulanmaz, §21 K-M0).

### §9.1 Seçim karar kuralları (S4 — §1.1 tablosunu tamamlar)

| # | Koşul | Karar | Q bağlantısı |
|---|-------|-------|--------------|
| 1 | WiFi gerekiyor | **ESP32** (istisnasız) | — |
| 2 | >4 analog girdi | ESP32 veya Mega2560 | — |
| 3 | >4 KB RAM | ESP32 (veya dikkatli Mega2560) | — |
| 4 | Batarya | ESP32 deep sleep (10 µA) veya AVR'de karşılaştırmalı uyku | güç |
| 5 | Basit buton + LED | Uno veya Nano | Q-M2 |
| 6 | 16-bit DSP/dsensor | PIC24F/dsPIC | Q-M1 |
| 7 | Eğitim/hızlı prototip | Arduino (PlatformIO) | §1.1 |

> **Çelişki uyarısı (dürüstlük):** §1.1 seçim kuralı ile §9.1 #1–#5 aynı soruyu
> farklı granularity'de yanıtlar; **çakışma yoktur**, §9.1 yalnız ESP32/Mega
> kollarını ekler. Çelişki tespit edilirse bu iki tablo birlikte revize edilir.

## §10 Yükseltme ve biçim yolu (kaynak: arduino-template §3.5)

```text
Uno (2 KB SRAM)
  ├─ Aynı form faktör → Nano Every (6 KB SRAM · 48 MHz)
  ├─ Daha fazla IO   → Mega2560 (8 KB SRAM · 16 kanal ADC)
  └─ Ağ + performans → ESP32 (520 KB SRAM · çift 240 MHz · WiFi/BT)
        ├─ Endüstriyel → ESP32-S3 (512 KB SRAM · PSRAM 8 MB'a kadar)
        └─ Düşük güç   → ESP32-C6 (512 KB SRAM · WiFi 6 · BLE 5 · 802.15.4)
```

### §10.1 Biçim değiştirme etki tablosu

| Bileşen | Uno → Mega2560 | Uno → ESP32 | Mega2560 → ESP32 |
|---------|----------------|-------------|------------------|
| Pinler | yeniden kablolama | yeniden kablolama (3.3 V mantık) | yeniden kablolama (3.3 V mantık) |
| I2C | aynı (Wire.h) | farklı pin (`Wire.begin(21,22)`) | farklı pin |
| SPI | aynı (SS farklı) | VSPI/HSPI seçimi | VSPI/HSPI seçimi |
| Analog | 10-bit → aynı | 10-bit → 12-bit (ölçekleme) | 10-bit → 12-bit |
| EEPROM | aynı API | önce `EEPROM.begin(size)` | önce `EEPROM.begin(size)` |
| Zamanlama | aynı | daha hızlı döngü | daha hızlı döngü |
| Güç | 5 V | 3.3 V (seviye kaydırıcı gerekli) | 3.3 V |

> **K001 bağlamı:** dalın F2–F6 fazları ATmega328P (§1 matrisi) üzerinde tanımlıdır;
> biçim değişimi yalnız **Q-M2 kapanmadan** gündeme gelir. ESP32 koluna geçiş
> `.templates/hardware/arduino-template.md` kapsamına girer (bu dal değil).

## §11 Zamanlama ve kesme mimarisi (kaynak: arduino-template §4.3 · §5.1 · §5.2)

| # | Kural | Doğru | Yanlış | Gerekçe |
|---|-------|-------|--------|---------|
| 1 | Tüm zamanlamalar `millis()` | `now - prev >= interval` | `delay(500)` | bloklama yok |
| 2 | Taşma güvenliği | `now - prev >= interval` | `now > prev + interval` | ~50 günde taşma |
| 3 | Zamanlama değişkeni tipi | `unsigned long` | `int`/`long` | taşma güvenliği |
| 4 | Alt µs hassasiyet | `micros()` | `millis()` | encoder/PWM/ultrasonik |
| 5 | `delay()` kullanımı | yalnız `setup()` güç açılma | üretim `loop()` içinde | döngü kaybı |
| 6 | Durum temsili | `enum class X : uint8_t` | `int state = 0` / `#define` | tip güvenliği |
| 7 | Durum değişkeni yeri | member / local static | global | kapsam disiplini |
| 8 | Sabit pin | `constexpr uint8_t` | `#define PIN 13` | derleme zamanı sabiti |

### §11.1 Periyodik görev kalıbı (ISR → bayrak)

| # | Adım | Kural |
|---|------|-------|
| 1 | `Timer1.initialize(1000)` | 1 ms periyot (şablondaki örnek) |
| 2 | `attachInterrupt` ISR gövdesi | **yalnız bayrak yaz** — hesap yok |
| 3 | `loop()` içinde bayrak oku | gerçek iş ana döngüde |
| 4 | ISR bütçesi | <50 µs (şablon §6.4) |
| 5 | Zamanlayıcı çakışması | iki kütüphane aynı timer kullanamaz (§18 T-M9) |

### §11.2 `loop()` aşama bütçesi (kaynak: arduino-template §5.1)

| # | Aşama | Ağırlık üst sınırı |
|---|-------|--------------------|
| 1 | Girdi örnekleme | 5 ms |
| 2 | Durum makinesi güncellemesi | 2 ms |
| 3 | Kontrol çıkışı | 3 ms |
| 4 | İletişim (seri/MQTT) | 10 ms |
| 5 | Loglama/teşhis | 5 ms |
| — | **Toplam** | **25 ms → ≥40 Hz alt döngü** |

> S4: aşama bütçesi toplamı 25 ms'i aşarsa dal `loop()`'u yeniden bölünmelidir;
> F6 (test) kapısında `micros()` profilleme ile ölçülür (§17).

## §12 Güvenlik: OTA · girdi · watchdog · sır (kaynak: arduino-template §4.4)

| # | Kural | Uygulama | İhlal sonucu |
|---|-------|----------|--------------|
| 1 | OTA imzası zorunlu | Ed25519 veya ECDSA | yetkisiz firmware |
| 2 | İmzasız OTA yok | ArduinoOTA doğrulamasız üretimde YASAK | yetkisiz firmware |
| 3 | Geri alma | önceki firmware ayrı flash bölümünde | bricking |
| 4 | OTA hız sınırı | dakikada en fazla 1 deneme | kilitlenme |
| 5 | Seri girdi sınırı | 256 bayt sabit tampon + null sonlandırma | tampon taşması |
| 6 | `Serial.readString()` | sınırsız okuma YASAK | bellek taşması |
| 7 | I2C/SPI slave | komut baytları hareketten önce doğrulanır | yetkisiz erişim |
| 8 | Watchdog | `setup()` içinde 4–8 s etkinleştir | askıda kalma |
| 9 | `wdt_reset()` yeri | `loop()` **başında** (sonunda değil) | ortadaki askı korunmaz |
| 10 | Sır saklama | `secrets.h` + `.gitignore`; kaynak gömme YASAK | sır sızıntısı |
| 11 | Üretim WiFi | ESP32 NVS içinde tutulur | sır sızıntısı |
| 12 | Uyumluluk kanıtı | CE/FCI/RoHS yoksa ürün etiketlenemez | piyasa uyumsuzluğu |

> **Dal bağlantısı:** maddeler 1–4 bu dalın F fazlarına **koşullu** uygulanır —
> yalnız WiFi/OTA içeren senaryolarda (Q-M5 kapanırsa). Madde 5–10 her senaryoda
> geçerlidir. Madde 12 K001 üretim/etiket alanıdır (kardeş `donanım-api`/raporlar).

## §13 Depolama katmanı (kaynak: arduino-template §4.5)

> Arduino hedeflerinde **veritabanı motoru yoktur**; kalıcı depolama üç mekanizmadır.

| # | Mekanizma | Kullanım | Sınır |
|---|-----------|----------|-------|
| 1 | EEPROM | cihaz yapılandırması (512 B), durum kalıcılığı (128 B) | ATmega328P: 1 KB |
| 2 | LittleFS / SPIFFS | sensör günlükleri (CSV), web varlıkları | ESP32 ≈ 1.5 MB |
| 3 | SD kart | ses günlüğü, büyük veri, çok saatli toplama | 10 MB/gün dosya döndürme |

| # | Kural |
|---|-------|
| 1 | SQL **YASAK** — CSV, JSON veya ikili düz dosya |
| 2 | SD kart açılışta kontrol edilir; yoksa log SPIFFS'e yazılır |
| 3 | CSV başlığı yalnız dosya oluşturmada bir kez yazılır |
| 4 | Dosya döndürme: 10 MB veya günlük bölme |
| 5 | `EEPROM.commit()` yalnız yapılandırma değişince (ESP32) |
| 6 | Açılışta doğrulama `magic` işareti ile (örn. `0xCAFE`) |

## §14 Bağımlılık ve kütüphane politikası (kaynak: arduino-template §4.8)

| # | Politika | Kural | Gerekçe |
|---|----------|-------|---------|
| 1 | Sürüm sabitleme | her `lib_deps` girdisi sürüm belirtir | kırıcı değişiklik önlenir |
| 2 | Dahili tercih | üçüncü taraf öncesi Arduino dahili (Wire, SPI, SD) | bakım, küçük binary |
| 3 | Yıllık denetim | tüm `lib_deps` yılda bir gözden geçirilir | güvenlik + uyumluluk |
| 4 | Git HEAD yok | üretimde `#HEAD` yasak | kararsız API yüzeyi |
| 5 | Elle `lib/` kopyası | YASAK — yalnız `lib_deps` | sürüm kayması |
| 6 | C++ standardı | `-std=gnu++17` (Arduino dalı) | şablon §4.2 |
| 7 | Uyarılar | `-Wall -Wextra -Werror` | sessiz hata kalması |

### §14.1 Yıllık denetim listesi

- [ ] `lib_deps` sürdürülüyor mu
- [ ] son sürüm araç zinciriyle uyumlu mu
- [ ] CVE var mı (bilinen güvenlik advisory)
- [ ] dahili kütüphaneyle değiştirilebilir mi
- [ ] kullanılmayan bağımlılık temizlendi mi

## §15 Derleme, yükleme ve CI akışı (kaynak: arduino-template §5.4–§5.7)

| # | Adım | Komut | Kapı |
|---|------|-------|------|
| 1 | Ortam kurulumu | `pip install platformio` | K-M1 |
| 2 | Derleme | `pio run` | K-M2/K-M3 |
| 3 | Ortam bazlı derleme | `pio run -e esp32dev` / `-e uno` | K-M2 |
| 4 | Yükleme | `pio run -t upload` | K-M3 |
| 5 | OTA yükleme (yalnız imzalı) | `pio run --upload-port <ip>` | §12 #1–#4 |
| 6 | Dosya sistemi | `pio run -t uploadfs` | — |
| 7 | Statik analiz | `pio check --fail-on-defect=medium` | K-M5 |
| 8 | Birim testi | `pio test -e native` | K-M5 |
| 9 | Sürüm etiketi | `git tag -a "v1.3.0"` | release kaydı |

### §15.1 Sürüm ve sır yönetimi

| # | Kural |
|---|-------|
| 1 | Firmware sürümü `src/version.h`: `FIRMWARE_MAJOR/MINOR/PATCH` + `FIRMWARE_VERSION "x.y.z"` |
| 2 | Git etiketi firmware sürümüyle eşleşir |
| 3 | CI her commit'te `pio check` çalıştırır |
| 4 | `.gitignore`'da kesinlikle `include/secrets.h` |
| 5 | `.hex` adlandırması kapsam §4 ile hizalı: `{ad}-{sürüm}-{mcu}.hex` |

## §16 Test ve doğrulama mimarisi (kaynak: arduino-template §6.1–§6.3)

| # | Katman | Araç | Ortam | Kapı |
|---|--------|------|-------|------|
| 1 | Birim testi | Unity + PlatformIO `native` | host (hızlı, donanım yok) | K-M5 |
| 2 | Cihaz testi | `pio test -e <env>` | hedef MCU | K-M3/K-M4 |
| 3 | Statik analiz | `pio check` | host | K-M5 |
| 4 | Pin zamanlama | osiloskop / lojik analizör | HIL | ⚠️ Q-M4 |
| 5 | Seri protokol | Python `pyserial` aracı | HIL | K-M4 |
| 6 | I2C/SPI bus | bus pirate / lojik analizör | HIL | F5 |
| 7 | Güç tüketimi | DMM / akım probu | HIL | ⚠️ Q-M4 |
| 8 | Ayıklama | `assert()` + `NDEBUG` | her iki | — |

> **HIL envanteri UNKNOWN (Q-M4):** osiloskop, lojik analizör, programmer
> (PICkit/avrdude), DMM envanteri bu oturumda **ölçülmedi** — iddia üretilmez.

## §17 Performans bütçesi (kaynak: arduino-template §6.4)

| # | Metrik | Hedef | Uygulama |
|---|--------|-------|----------|
| 1 | Döngü süresi | <10 ms (100 Hz) | `micros()` profilleme |
| 2 | ISR süresi | <50 µs | ISR yalnız bayrak |
| 3 | SRAM kullanımı | toplamın %80'i altı | `pio check --section=.bss` |
| 4 | Flash kullanımı | toplamın %90'ı altı | `pio size --json` |
| 5 | Watchdog zaman aşımı | 8 saniye | `wdt_enable(WDTO_8S)` |
| 6 | WiFi yeniden bağlanma | <5 saniye | üstel geri çekilme |

### §17.1 Optimizasyon önceliği (etkiye göre sıralı)

| # | Aksiyon | Etki |
|---|---------|------|
| 1 | `String` nesnelerini kaldır (`char[]` + `sprintf`) | RAM kaçağı kapanır |
| 2 | Dize literallerini `F()` ile flash'a taşı | 2 bayt/char RAM kazancı |
| 3 | Arama tablolarını `PROGMEM`'e taşı | AVR'de flash'a taşınır |
| 4 | Tampon boyutlarını küçült | doğrudan SRAM |
| 5 | İzin veriliyorsa `int`/`float` yerine `uint8_t`/`int16_t` | hem RAM hem CPU |

## §18 Sorun giderme tablosu (kaynak: arduino-template §6.5–§6.6)

| # | Belirti | Olası neden | Kontrol | Düzeltme |
|---|---------|-------------|---------|----------|
| T-M1 | Yükleme başarısız | yanlış board/port | `pio board list` · `pio device list` | `board=` · `upload_port=` |
| T-M2 | Seri bozuk | baud uyumsuzluğu | monitör baud == kod baud | ikisinde de 115200 |
| T-M3 | Brownout | yetersiz güç | VCC'yi ölç | MCU yanına 100 µF, regüle besleme |
| T-M4 | Watchdog döngüsü | döngü 8 sn'den uzun | `micros()` profilleme | `wdt_reset()`'i erkene al |
| T-M5 | WiFi kopması | RF paraziti / menzil | RSSI kontrolü | anten konumu, TX gücü |
| T-M6 | I2C askıda | bus kiliti (SCL alçak) | SDA/SCL lojik analizör | `Wire.setTimeout(50)` + kurtarma darbesi |
| T-M7 | Flash bozulması | yazım sırasında güç kesintisi | açılışta CRC | yalnız değişince `commit()` |
| T-M8 | Bellek sızıntısı | `String` birleştirme | `freeSRAM()` | `char[]` + `sprintf` |
| T-M9 | Zamanlayıcı çakışması | iki kütüphane aynı timer | kütüphane dokümanı | farklı timer (Timer1 vs Timer2) |
| T-M10 | OSC başarısız | yanlış fuse bitleri | avrdude ile fuse oku | `avrdude -p m328p -U lfuse:r:-:h` |

### §18.1 Hata ayıklama araçları

| # | Araç | Ne zaman |
|---|------|----------|
| 1 | Seri monitör | genel ayıklama (`pio device monitor`) |
| 2 | Lojik analizör | I2C/SPI/UART protokol sorunları |
| 3 | Osiloskop | PWM, kesme zamanlaması, brownout (min 100 MHz ⚠️ Q-M4) |
| 4 | Multimetre | gerilim, süreklilik, akım — sorunların %80'i VCC |
| 5 | PlatformIO debugger | `pio debug --interface=gdb` |

## §19 Hata seviye yönetimi (kaynak: arduino-template §4.7)

| # | Seviye | Aksiyon | Kurtarma |
|---|--------|---------|----------|
| 1 | INFO | seri porta logla | devam |
| 2 | WARN | seri + LED yanıp sönme deseni | devam |
| 3 | ERROR | seri + özel hata LED'i | 3× tekrar, sonra başarısız |
| 4 | FATAL | askıda kal + watchdog sıfırlama | watchdog yeniden başlatma |

| # | Kural |
|---|-------|
| 1 | Ayıklama baskıları `DEBUG_ENABLE` ile açılır; sürüm derlemesinde `((void)0)` |
| 2 | Hata kodları LED yanıp sönme sayısıyla kodlanır (örn. `SENSOR_FAIL = 1` … `OTA_FAIL = 5`) |
| 3 | Watchdog kaynaklı sıfırlama açılışta `MCUSR`/`RCON` okunarak tespit edilir ve temizlenir |

## §20 Dal riskleri ve azaltmaları (S4)

| # | Risk | Olasılık | Etki | Azaltma | Etiket |
|---|------|----------|------|---------|--------|
| R-M1 | PIC serisi yanlış seçilmesi | orta | yüksek | Q-M1 kapanana kadar temsilî blok kullan (§4 ornekler) | ⚠️ VERIFICATION REQUIRED |
| R-M2 | ATmega328P hedefi teyitsiz | orta | orta | F2 fazı öncesi Q-M2 teyidi | ⚠️ VERIFICATION REQUIRED |
| R-M3 | I2C adres çakışması / seviye uyumsuzluğu | yüksek | orta | multiplexer (§8.1 #2) + seviye kaydırıcı | S4 |
| R-M4 | HIL envanteri eksik (programmer/osiloskop) | yüksek | yüksek | Q-M4 kapanana kadar kapılar PLANNED kalır | UNKNOWN |
| R-M5 | UBRR hesabı hatalı (osilatör toleransı) | orta | düşük | F4'te gerçek ölçüm; §6 ornekler notu korunur | ornekler §6 |
| R-M6 | Katman ihlali (`#ifdef` K2/K3'e sızmak) | orta | yüksek | kod incelemesi: `#ifdef` yalnız K1 | kapsam §2 kuralı |
| R-M7 | Repo'da 0 kaynak dosyası → plan uygulanamaz durumda | kesin | orta | F0 sıfırdan; plan tamamı PLANNED etiketli | S1 glob |
| R-M8 | `delay()` üretim koduna sızması | orta | orta | §11 #1 kuralı + kod incelemesi | şablon §4.3 |
| R-M9 | Sır kaynağa gömülmesi | düşük | yüksek | `secrets.h` + `.gitignore` denetimi | şablon §4.4 #10 |
| R-M10 | Timer çakışması (Timer1 iki kütüphane) | düşük | orta | §18 T-M9 denetimi | S4 |

## §21 Katılım ve kabul kriterleri (F fazlarıyla hizalı)

| # | Kriter | Ölçüm | Kapı |
|---|--------|-------|------|
| 1 | Araç zinciri kurulu ve sürüm eşleşmeli | `xc8` · `avr-gcc --version` · `pio --version` (şablon §7.3) | K-M1 |
| 2 | HAL başlığı 3 platformda derleniyor | `pio run -e native` + hedef env | K-M2 |
| 3 | `.hex` üretiliyor ve yükleniyor | `pio run -t upload` + LED gözlem | K-M3 |
| 4 | UART 115200 8N1 döngü | loopback seri test | K-M4 |
| 5 | Host birim testi yeşil | `pio test -e native` | K-M5 |
| 6 | Statik analiz eşiği | `pio check --fail-on-defect=medium` | K-M5 |
| 7 | SRAM/Flash bütçe | §17 tablosu içinde | F6 |
| 8 | `.hex` adlandırması + SHA-256 | kapsam §4 | F7 |

> **K-M0 (ön kapı):** fiyat/standart/ölçüm iddiaları yalnız şablon S1 kaynağıyla
> taşınır; bağımsız ölçüm yoksa ⚠️ VERIFICATION REQUIRED olarak kalır.

## §22 Bilinmeyenler ve sınır (bu dalın UNKNOWN kümeleri)

| # | Bilinmeyen | Etiket | Kapanış yolu |
|---|-----------|--------|--------------|
| 1 | PIC serisi (16F/18F/24F/dsPIC) | ⚠️ VERIFICATION REQUIRED | Q-M1 kararı |
| 2 | ATmega328P hedef teyidi | ⚠️ VERIFICATION REQUIRED | Q-M2 teyidi |
| 3 | HIL donanımı envanteri | UNKNOWN | Q-M4 envanter sayımı |
| 4 | İlk uygulama senaryosu | UNKNOWN | Q-M5 kararı |
| 5 | CI'da MCU derlemesi ne zaman girer | UNKNOWN | Q-M5 / DevOps kararı |
| 6 | ESP32 koluna geçiş ihtiyacı | UNKNOWN | §10 biçim kararı |
| 7 | Güncel fiyat/ödül verisi | ⚠️ VERIFICATION REQUIRED | ölçüm/teklif |
| 8 | Repo'da `.c`/`.h`/`.ino` = 0 | S1 (ölçüldü) | F0 başlayınca kapanır |

> **Sınır (komşu dal):** DSP algoritması `K004-dsp` · firmware OTA zinciri
> `K005-firmware` · bare-metal kuralları `.templates/hardware/*-template.md` ·
> host OS derleme `K000 …/derleme-ve-paketleme-cross-platform-plani.md`.

## §23 Faz–dosya–kanıt izleme matrisi (izlenebilirlik)

> Her fazın çıktısı hangi kardeş dosyada, hangi kanıt tipiyle kayıtlıdır —
> "hangi iddia nerede doğrulanır" sorusunun tek cevabı.

| # | Faz | Çıktı | Kapsam kaydı | Doğrulama kaydı | Örnek kaydı | Kanıt tipi |
|---|-----|-------|--------------|-----------------|-------------|------------|
| F0 | Ortam | araç kurulumu | §3 F0 | §3 K-M1 · §21 #1 | — | S1 (sürüm çıktısı) |
| F1 | Scaffold | `hal/*.h` + 3 BSP iskeleti | §3 F1 · §2 | §3 K-M2 · §21 #2 | §1 `hal/gpio.h` | S1 + S4 |
| F2 | Blink | `.hex` ×3 | §3 F2 · §4 | §3 K-M3 · §21 #3 | §2 · §3 · §4 · §5 | S1 (repo) — bugün 0 |
| F3 | Timer/PWM | `hal/pwm.h` | §3 F3 · §11 | §11.1 · §17 #2 | §11.1 (kalıp) | S4 + ölçüm ⚠️ |
| F4 | UART | `hal/uart.h` + telemetri | §3 F4 · §8 #3 | §3 K-M4 · §21 #4 | §6 `uart_telemetry.c` | S4 + HIL ⚠️ |
| F5 | Bus | `hal/i2c.h` · `hal/spi.h` | §3 F5 · §8 · §8.1 | §18 T-M6 | §18.1 (araç) | S1 + cihaz yanıtı |
| F6 | Test+HIL | host testi + HIL senaryoları | §3 F6 · §16 · §17 | §3 K-M5 · §21 #5–#7 | §16 · §17 · §18 | S1 + ölçüm |
| F7 | Paket | `.hex` + sürüm + SHA-256 | §3 F7 · §4 · §15.1 | §21 #8 | §15 (akış) | S1 (release kaydı) |

### §23.1 Kanıt tipi sözlüğü (bu dosyada geçen kısaltmalar)

| # | Tip | Tanım | Kullanım sınırı |
|---|-----|-------|-----------------|
| 1 | **S1** | şablon/repo satırı — gözlenebilir dosya veya glob çıktısı | en güçlü; tarihi ile yazılır |
| 2 | **S4** | mimari yorum — mühendis çıkarımı | kanıt iddiası değildir, gerekçe taşır |
| 3 | **S7** | bilinmeyen | `UNKNOWN` / `⚠️ VERIFICATION REQUIRED` |
| 4 | URL HEAD 200 | erişilebilirlik kanıtı — **içerik doğrulaması değildir** | kardeş `mikrodenetleyici-dogrulama.md` §2 |
| 5 | Ölçüm (ReadAllLines) | dosya/satır sayımı — kapı kontrolü | `Measure-Object -Line` YASAK |

> **Uyarı:** URL HEAD 200 yalnızca kaynağın **ayakta olduğunu** gösterir; belgedeki
> teknik iddiaların doğruluğu ayrı bir okuma gerektirir ve bu oturumda **yapılmamıştır**
> (S7 → kardeş doğrulama dosyası §10).

### §23.2 Bu dosyanın iddia sınırları

| # | İddia sınıfı | Durum |
|---|-------------|-------|
| 1 | Şablon içerikleri (§8–§19) | **aktarılmıştır** — kaynak `.templates/hardware/arduino-template.md` v1.0.0; bağımsız doğrulama yok |
| 2 | Sürüm/standart değerleri (§15.1, §12 #12) | şablon §7.4 kayıtlıdır; bu oturumda yeniden HEAD çekilmedi |
| 3 | Fiyat/ölçüm değerleri (§9, §17) | ⚠️ VERIFICATION REQUIRED — ölçüm yapılmadı |
| 4 | Repo glob (0 `.c`) | S1 · 2026-10-14 ölçümü (dosya FM `validation` satırı) |
| 5 | HIL envanteri | UNKNOWN (Q-M4) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
