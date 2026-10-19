---
title: "CoreMusic — Arduino Technology Governance Template"
type: template
category: template
version: 1.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-29
---

# CoreMusic — Arduino Technology Governance Template

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu şablon, CoreMusic ekosistemindeki tüm Arduino ailesi mikrodenetleyici projeleri (donanım denetleyicileri, sensör düğümleri, ekran sürücüleri, IO genişleticileri) için teknoloji yönetişimi kurallarını tanımlar. Eski spesifikasyon dosyası (`arduino-template.md`, 2026-06-24, 1273 satır) yeniden yapılandırılmış; içerik silinmeden Guardrail #16 dış iskeletine (H1 + §1-§7) taşınmıştır.

**Guardrail #16:** CoreMusic vault içinde bir Arduino firmware dokümanı/protokolü üretilirken bu şablon seçilmek ZORUNLUDUR.

| Karar | Dayanak | Şablondaki karşılığı |
|-------|---------|----------------------|
| PlatformIO zorunlu, Arduino IDE yalnız prototipleme | Eski §1.1 kuralları | §4.1 #1 |
| C++17 (`-std=gnu++17`) zorunlu | Eski §2.1 | §4.2 |
| Bloklamayan `millis()` zamanlaması (ISR bayraklı) | Eski §2.3/§7.1 | §4.3 + §5.2 |
| OTA yalnız imzalı (Ed25519/ECDSA) | Eski §3.1 | §4.4 #1 |
| Üretim cihazında watchdog zorunlu | Eski §3.3 | §4.4 #4 |
| CE/FCI/RoHS uyumluluk kanıtı | Eski §19 | §7.4 |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Arduino ailesi MCU firmware projeleri (ESP32, ATmega, Nano, Mega) | Bare-metal AVR (Arduino framework'süz) → `[[hardware/avr-template]]` |
| PlatformIO proje yapısı, kütüphane yönetimi, CI derleme kontrolü | PIC (Microchip) → `[[hardware/pic-template]]` |
| GPIO/I2C/SPI/UART etkileşim kalıpları, EEPROM/LittleFS/SD depolama | Genel donanım tasarım dokümanı → `[[hardware/hardware-template]]` |
| OTA güvenliği, seri/I2C/SPI girdi doğrulama, watchdog | STM32/Cortex-M C++ (RTOS) → `[[../other/cpp-template]]` |
| Test stratejisi (Unity + HITL), performans bütçesi, sorun giderme | C99/C11 sade embede kod → `[[../other/c-template]]` |

- **Dosya tipi:** Markdown teknoloji yönetişim şablonu (`.templates/hardware/` altı).
- **Kullanan agent:** Embedded Engineer (sorumlu), DSP Firmware Engineer / Audio Hardware Engineer (ikincil).
- **Doğrulama zorunluluğu:** sürüm, standart ve kanal/adet iddiaları §7'deki web kaynaklarıyla doğrulanmıştır; doğrulanamayan her iddia `⚠️ VERIFICATION REQUIRED` etiketi taşır.

---

## 3. Mimari

### 3.1 İki Katmanlı Soyutlama Modeli (eski §1.1)

Arduino firmware'i katı 2 katmanlı bir yapı izler; CoreMusic sunucu modelinden (L0-L3) farklıdır:

```
┌────────────────────────────────────────┐
│ L1 — UYGULAMA                          │
│ setup() / loop()                        │
│ Durum makineleri, iş mantığı            │
│ Sensör füzyonu, kontrol algoritmaları   │
├────────────────────────────────────────┤
│ L0 — DONANIM SOYUTLAMA                 │
│ Arduino API (digitalWrite, analogRead)  │
│ Kütüphane sarmalayıcıları (Wire, SPI, SD)│
│ HAL — Hardware Abstraction Layer        │
├────────────────────────────────────────┤
│ MCU — MİKRODENETLEYİCİ                 │
│ Registerlar, kesmeler, zamanlayıcılar   │
│ Bootloader, linker script               │
└────────────────────────────────────────┘
```

**Kurallar:**
- L0, performans kritik olduğunda MCU katmanına doğrudan referans verebilir (register erişimi).
- L1, MCU registerlarına L0'ı atlayarak ERİŞEMEZ — her zaman Arduino API veya kütüphane sarmalayıcısı kullanılır.
- PlatformIO **zorunlu** derleme sistemidir. Arduino IDE yalnız prototipleme içindir; tüm üretim kodu PlatformIO kullanır (`platformio.ini` proje kökünde).
- Bağımlılıklar `platformio.ini` `lib_deps` içinde bildirilir, elle `lib/` klasörüne kopyalanmaz.

### 3.2 Proje Yapısı (PlatformIO — eski §1.2)

```
project-root/
├── platformio.ini              (Derleme yapılandırması, bağımlılıklar, env'ler)
├── src/
│   ├── main.cpp                (setup + loop girişi)
│   ├── application/
│   │   ├── state_machine.cpp/h
│   │   └── control_loop.cpp/h
│   └── hardware/
│       ├── sensor_driver.cpp/h
│       └── actuator_driver.cpp/h
├── lib/
│   └── (özel kütüphaneler — sürümlenmiş)
├── include/
│   └── secrets.h               (WiFi kimlik bilgileri, API anahtarları)
├── test/
│   └── test_main.cpp           (PlatformIO birim testleri)
├── data/
│   └── (SPIFFS/LittleFS dosya sistemi varlıkları)
└── ci/
    └── compile-check.sh        (CI derleme betiği)
```

### 3.3 MCU Seçim Matrisi (eski §10.1)

| Kriter | ESP32 | Mega2560 | Uno R3 |
|--------|-------|----------|--------|
| **CPU** | Çift 240 MHz | 16 MHz | 16 MHz |
| **SRAM** | 520 KB | 8 KB | 2 KB |
| **Flash** | 4-16 MB | 256 KB | 32 KB |
| **WiFi/BT** | Dahili | Kalkan gerekli | Kalkan gerekli |
| **ADC** | 12-bit, 18 kanal | 10-bit, 16 kanal | 10-bit, 6 kanal |
| **DAC** | 2 × 8-bit | Yok | Yok |
| **Maliyet** | $3-8 | $10-15 | $5-8 |
| **En iyi kullanım** | Ağ tabanlı sensör, ses | Çok-sensör, ekran | Basit kontrol, öğrenme |

**Karar Kuralları:**
- WiFi gerekiyor mu? = **ESP32**. İstisnası yok.
- > 4 analog girdi mi? = **ESP32** veya **Mega2560**.
- 4 KB'tan fazla RAM mi gerekiyor? = **ESP32** (veya dikkatli Mega2560).
- Batarya ile mi çalışıyor? = **ESP32** deep sleep (10 µA) veya AVR'de **karşılaştırmalı** uyku.
- Basit buton + LED mi? = **Uno** veya **Nano**.

### 3.4 Kablosuz Protokol Seçimi (eski §10.2)

| Protokol | Maks. Hız | Mesafe | Pin | Kullanım |
|----------|-----------|--------|-----|----------|
| **I2C** | 400 kHz / 3.4 MHz | <1 m | 2 (SDA, SCL) | Sensör, EEPROM, ekran |
| **SPI** | 10-80 MHz | <1 m | 4+ (MOSI, MISO, SCK, CS) | SD kart, yüksek hızlı ADC, TFT |
| **UART** | 2 Mbps'a kadar | <5 m (RS232) | 2 (TX, RX) | GPS, Bluetooth, hata ayıklama |
| **1-Wire** | 16.3 kbps | <100 m | 1 | Sıcaklık sensörleri (DS18B20) |
| **CAN** | 1 Mbps | <40 m | 2 (CANH, CANL) | Otomotiv, endüstriyel |

**Karar Kuralları:**
- Tek sensör = I2C (minimum kablolama).
- Aynı adresli birden çok sensör = I2C multiplexer (TCA9548A) veya SPI'ye geç.
- SD kart = SPI (SD kütüphanesi şartı).
- Uzun mesafe (>5 m) = CAN veya RS485.
- Basit sıcaklık = 1-Wire (DS18B20, uzun mesafe, ortak bus).

### 3.5 MCU Yükseltme Yolu (eski §16.2)

```
Uno (2 KB SRAM)
  │
  ├─ Aynı form faktör → Nano Every (6 KB SRAM, 48 MHz)
  │
  ├─ Daha fazla IO → Mega2560 (8 KB SRAM, 16 kanal ADC)
  │
  └─ Ağ + performans → ESP32 (520 KB SRAM, çift 240 MHz, WiFi/BT)
        │
        ├─ Endüstriyel → ESP32-S3 (512 KB SRAM, PSRAM 8 MB'a kadar)
        │
        └─ Düşük güç → ESP32-C6 (512 KB SRAM, WiFi 6, BLE 5, 802.15.4)
```

| Bileşen | Uno → Mega2560 | Uno → ESP32 | Mega2560 → ESP32 |
|---------|----------------|-------------|------------------|
| **Pinler** | Yeniden kablolama | Yeniden kablolama (3.3V mantık!) | Yeniden kablolama (3.3V mantık!) |
| **I2C** | Aynı (Wire.h) | Farklı pinler (Wire.begin(21,22)) | Farklı pinler |
| **SPI** | Aynı (SS farklı) | VSPI/HSPI seçimi | VSPI/HSPI seçimi |
| **Analog** | 10-bit → aynı | 10-bit → 12-bit (ölçekleme ayarı) | 10-bit → 12-bit |
| **EEPROM** | Aynı API | Önce `EEPROM.begin(size)` | Önce `EEPROM.begin(size)` |
| **Zamanlama** | Aynı | Daha hızlı döngü (gecikmeleri azalt) | Daha hızlı döngü |
| **Güç** | 5V | 3.3V (seviye kaydırıcı gerekli) | 3.3V |

---

## 4. Kurallar

### 4.1 Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | PlatformIO üretim derleme sistemi; Arduino IDE yalnız prototip (eski §1.1) | Tekrarlanamayan derleme |
| 2 | L1 katmanı MCU registerlarına doğrudan erişemez — her zaman L0 üzerinden | Katman ihlali, taşınabilirlik kaybı |
| 3 | Kütüphane bağımlılıkları yalnız `lib_deps`; elle `lib/` kopyası yasak | Sürüm kayması |
| 4 | OTA güncellemeleri Ed25519/ECDSA ile imzalı olmalı; imzasız OTA üretimde YASAK (eski §3.1) | Yetkisiz firmware yükleme |
| 5 | Üretim cihazlarında watchdog `setup()` içinde etkinleştirilmeli (4-8 sn) (eski §3.3) | Askıda kalma tespit edilemez |
| 6 | WiFi/şifre/API anahtarı kaynak dosyaya gömülemez — `secrets.h` + `.gitignore` (eski §3.4) | Sır sızıntısı |
| 7 | Bütün seri girdiler sınırlı tampon + null sonlandırmalı (eski §3.2) | Tampon taşması |
| 8 | CE/FCI/RoHS kanıtı olmadan ürün etiketlenemez (eski §19, §7.4) | Piyasa uyumsuzluğu |

### 4.2 Dil Standardı ve Pin Tanımları (eski §2.1-§2.2)

**C++17** (`-std=gnu++17`, platformio.ini içinde) tüm Arduino projeleri için zorunludur.

```
✅ Doğru:
  constexpr uint8_t LED_PIN = 13;
  enum class State : uint8_t { IDLE, ACTIVE, ERROR };
  unsigned long previousMillis = 0;

❌ Yasak:
  #define LED_PIN 13               (const/constexpr kullan)
  int state = 0;                   (enum class kullan)
  byte data;                       (uint8_t kullan)
```

| Kural | Örnek | Gerekçe |
|-------|-------|---------|
| Pin numaraları `constexpr` | `constexpr uint8_t RELAY_PIN = 4;` | Derleme zamanı sabiti, RAM yok |
| Zamanlama sabitleri `constexpr` | `constexpr unsigned long BLINK_INTERVAL = 500;` | Okunabilirlik |
| Pinleri arayüze göre grupla | `// I2C: SCL=21, SDA=22` | Dokümantasyon |
| Fonksiyon içinde sabitleme yasak | `digitalWrite(4, HIGH)` yerine `digitalWrite(RELAY_PIN, HIGH)` | Bakım kolaylığı |

### 4.3 Zamanlama ve Durum Temsili (eski §2.3-§2.4)

```
✅ Doğru (bloklamayan):
  unsigned long now = millis();
  if (now - previousMillis >= interval) {
      previousMillis = now;
      digitalWrite(LED_PIN, !digitalRead(LED_PIN));
  }

❌ Yanlış (bloklıyor):
  delay(500);
  digitalWrite(LED_PIN, !digitalRead(LED_PIN));
```

**Kurallar:**
- Bütün zamanlamalar `millis()` ile. `delay()` yalnız `setup()` içinde güç açılma stabilizasyonu için.
- Milisaniye altı hassasiyet (encoder, PWM, ultrasonik) için `micros()`.
- Bütün zamanlama değişkenleri `unsigned long` OLMALI (~50 günde taşar, güvenli).
- `millis()` taşması ele alınır: `if (now - previousMillis >= interval)` taşma boyunca doğru çalışır — `now > previousMillis + interval` KARŞILAŞTIRMASI YASAK.

**Durum temsili:** Her zaman açıkça alt tip verilmiş `enum class` (`uint8_t` en küçüğü); durum üye değişkeni veya local static'te, asla globalde.

```cpp
enum class SystemState : uint8_t { INIT, IDLE, ACTIVE, ERROR, SLEEP };
// ❌ int state = 0;   ❌ #define STATE_IDLE 0
```

### 4.4 Güvenlik Kuralları (eski §3)

**OTA (Over-the-Air) Güncellemeleri:**

| Gereklilik | Uygulama |
|------------|----------|
| **İmza zorunlu** | OTA updates kriptografik olarak imzalı OLMALI (Ed25519 veya ECDSA) |
| **İmzasız OTA yok** | İmza doğrulamasız ArduinoOTA üretimde YASAKTIR |
| **Geri alma** | Önceki firmware ayrı flash bölümünde tutulur |
| **Hız sınırı** | Dakikada en fazla 1 OTA denemesi |

**Seri / I2C / SPI Girdisi:**
- **Bütün seri girdiler sınırlanmış olmalı.** 256 baytlık sabit tampon + açık null sonlandırma.
- Uzunluk sınırsız `Serial.readString()` YASAK.
- I2C ve SPI slave cihazlar hareketten önce komut baytlarını doğrulamalı.

```cpp
// ✅ Güvenli seri okuma
#define SERIAL_BUF_SIZE 256
static char buf[SERIAL_BUF_SIZE];
uint8_t idx = 0;
while (Serial.available() && idx < SERIAL_BUF_SIZE - 1) {
    char c = Serial.read();
    if (c == '\n') break;
    buf[idx++] = c;
}
buf[idx] = '\0';  // null sonlandırma
```

**Watchdog Timer:**
- Watchdog üretimde `setup()` içinde etkinleştirilmeli; zaman aşımı 4-8 saniye.
- `wdt_reset()` `loop()`'un **başında** çağrılır (sonunda değil — ortadaki askıdayı korur).

```cpp
#include <avr/wdt.h>      // AVR
// veya
#include <esp_task_wdt.h> // ESP32

void setup() {
    wdt_enable(WDTO_8S);   // 8 saniye zaman aşımı
}
void loop() {
    wdt_reset();           // köpeği besle
    // ... uygulama kodu ...
}
```

**WiFi / Ağ Kimlik Bilgileri:**
- **WiFi SSID/şifresi asla kaynak dosyaya gömülmez.**
- `.gitignore`'a eklenmiş `secrets.h` dosyası kullanılır.
- Üretim WiFi yapılandırması ESP32 NVS (Non-Volatile Storage) içinde tutulur.

```cpp
// include/secrets.h (COMMIT EDİLMEZ — .gitignore içinde)
#pragma once
constexpr char WIFI_SSID[] = "MyNetwork";
constexpr char WIFI_PASS[] = "SecurePassword123";
constexpr char API_KEY[] = "sk_production_abc123";
```

### 4.5 Depolama Kuralları (eski §4 — "Database Conventions")

Arduino hedeflerinde **veritabanı motoru yoktur**. Kalıcı depolama üç mekanizma ile yapılır:

| Mekanizma | Kullanım | Sınır |
|-----------|----------|-------|
| **EEPROM** | Cihaz yapılandırması (512 B), durum kalıcılığı (128 B) | MCU'ya göre (ATmega328P: 1 KB) |
| **LittleFS / SPIFFS** | Sensör günlükleri (CSV), web varlıkları | ESP32 ≈ 1.5 MB |
| **SD kart** | Ses günlüğü, büyük veri, çok saatli toplama | 10 MB/gün dosya döndürme |

```cpp
#include <EEPROM.h>
struct DeviceConfig {
    uint16_t magic = 0xCAFE;         // doğrulama işareti
    uint8_t brightness = 255;
    uint16_t intervalMs = 1000;
    int16_t calibrationOffset = 0;
};
DeviceConfig config;
void saveConfig() { EEPROM.put(0, config); EEPROM.commit(); /* ESP32; AVR otomatik */ }
bool loadConfig() { EEPROM.get(0, config); return config.magic == 0xCAFE; }
```

**Kurallar:**
- SD kart başlatma açılışta kontrol edilir — yoksa log SPIFFS'e yazılır.
- CSV başlığı dosya oluşturmada bir kez yazılır.
- Dosya döndürme: 10 MB veya günlük bölme.
- SQL YASAK — CSV, JSON veya ikili düz dosyalar kullanılır.

### 4.6 En İyi Uygulamalar (eski §7)

| Yöntem | Blokluyor mu? | RAM | CPU | Kullanım |
|--------|---------------|-----|-----|----------|
| `delay()` | EVET | 0 | Döngüleri boşa harcar | Yalnız güç açılma beklemesi |
| `millis()` bloklamayan | HAYIR | 4 bayt | Kullanılabilir | Bütün üretim zamanlaması |
| `Timer1.attachInterrupt` | HAYIR | ISR yığını | Hassas | 1 kHz+ periyodik görev |

**`F()` makrosu (RAM tasarrufu):**
- Her `Serial.print()` / `println()` dizesi, hata/ayıklama menü metinleri `F()` ile flash'ta tutulur.
- Çalışma zamanında değişen dizelerde `F()` KULLANILMAZ (`String` veya `sprintf`).

```cpp
Serial.println("Sensor reading complete");   // ❌ 2 bayt/char RAM
Serial.println(F("Sensor reading complete")); // ✅ 0 bayt RAM
```

**`PROGMEM` arama tabloları için:**
- 64 bayttan büyük arama tabloları, font bit haritaları, kalibrasyon eğrileri, gamma tabloları.
- AVR'de (2 KB RAM) varsayılan; ESP32'de (520 KB RAM) yalnız çok büyük tablolar için.
- 256 girdili 16-bit sinüs tablosu PROGMEM ile SRAM'in %25'ini değil, yalnız flash'ın %2'sini kaplar.

```cpp
const uint16_t sineTable[256] PROGMEM = { 0, 402, 804, 1205, ... };
uint16_t sineValue = pgm_read_word(&sineTable[index]);
```

**Buton Debounce (durum makinesi):** 50 ms gecikmeli durum makinesi; `INPUT_PULLUP` mantığında `PRESSED = LOW`, yükselen kenar algılama döner.

```cpp
class Button {
public:
    Button(uint8_t pin) : pin(pin) { pinMode(pin, INPUT_PULLUP); }
    bool isPressed() {
        bool current = (digitalRead(pin) == LOW);
        unsigned long now = millis();
        if (current != lastState) lastDebounceTime = now;
        if ((now - lastDebounceTime) > DEBOUNCE_DELAY && current != debouncedState) {
            debouncedState = current;
            if (debouncedState == PRESSED) return true;  // yükselen kenar
        }
        lastState = current;
        return false;
    }
private:
    static constexpr unsigned long DEBOUNCE_DELAY = 50;  // ms
    uint8_t pin;
    bool lastState = HIGH, debouncedState = HIGH;
    unsigned long lastDebounceTime = 0;
    static constexpr bool PRESSED = LOW;
};
```

### 4.7 Hata Yönetimi (eski §13)

| Seviye | Aksiyon | Kurtarma |
|--------|---------|----------|
| **INFO** | Seri porta logla | Devam |
| **WARN** + | Seri + LED yanıp sönme deseni | Devam |
| **ERROR** + | Seri + özel hata LED'i | 3× tekrar, sonra başarısız |
| **FATAL** + | Askıda kal + watchdog sıfırlama | Watchdog yeniden başlatma |

- Ayıklama baskıları `DEBUG_ENABLE` ile açılır; sürüm derlemesinde `((void)0)` — sıfır yük.
- Hata kodları LED yanıp sönme sayısıyla kodlanır (`ErrorCode::SENSOR_FAIL = 1`, ... `OTA_FAIL = 5`).
- Watchdog kaynaklı sıfırlama, `MCUSR`/`RCON` okunarak açılışta tespit edilir ve temizlenir.

### 4.8 Bağımlılık / Kütüphane Politikası (eski §14)

| Politika | Kural | Gerekçe |
|----------|-------|---------|
| **Sürüm sabitleme** | Her `lib_deps` girdisi sürüm belirtir | Kazara kırıcı değişiklik önlenir |
| **Dahili tercih** | Üçüncü taraf öncesi Arduino dahili (Wire, SPI, SD) | Bakım, küçük binary |
| **Yıllık denetim** | Tüm lib_deps yılda bir gözden geçirilir | Güvenlik + uyumluluk |
| **Git HEAD yok** | Üretimde `#HEAD` yasak | Kararsız API yüzeyi |

```ini
[env:esp32dev]
lib_deps =
    adafruit/Adafruit SSD1306 @ ^2.5.7
    adafruit/Adafruit GFX Library @ ^1.11.5
    bblanchon/ArduinoJson @ ^7.0.0
    knolleary/PubSubClient @ ^2.8
```

**Yıllık denetim listesi:** sürdürülüyor mu · son sürüm araç zinciriyle uyumlu · CVE var mı · dahiliyle değiştirilebilir mi · kullanılmayan bağımlılık temizlendi mi.

---

## 5. Workflow

### 5.1 Olay Döngüsü: `loop()` bir zamanlayıcı olarak (eski §5.1)

Arduino'da HTTP yönlendiricisi yoktur; bütün yürütme akışını olay驱动 bir zamanlayıcı yönetir.

```cpp
void loop() {
    wdt_reset();

    // Aşama 1: Girdi örnekleme (azami 5 ms)
    sampleSensors();

    // Aşama 2: Durum makinesi güncellemesi (azami 2 ms)
    stateMachine.update(currentMillis);

    // Aşama 3: Kontrol çıkışı (azami 3 ms)
    updateActuators();

    // Aşama 4: İletişim (azami 10 ms)
    handleSerialCommands();
    handleMqttMessages();

    // Aşama 5: Loglama ve teşhis (azami 5 ms)
    logPeriodicData();
}
```

### 5.2 Periyodik Görevler İçin Zamanlayıcı Kesmeleri (eski §5.2)

Kesin aralıklarla ÇALIŞMASI gereken görevler (PID, ses örnekleme, encoder yoklaması) için:

```cpp
#include <TimerOne.h>  // AVR
void setup() {
    Timer1.initialize(1000);       // 1 ms aralık
    Timer1.attachInterrupt(timerISR);
}
volatile bool timerFlag = false;
void timerISR() { timerFlag = true; }   // yalnız bayrak — minimal iş

void loop() {
    if (timerFlag) { timerFlag = false; readEncoder(); }  // hassas 1 kHz
    // ...
}
```

### 5.3 Mod Geçişleri İçin Durum Makinesi (eski §5.3)

```cpp
enum class AppMode : uint8_t { SLEEP, IDLE, ACTIVE, CONFIG, ERROR };

class StateMachine {
public:
    void update(unsigned long now) {
        switch (mode) {
            case AppMode::SLEEP:  if (wakeCondition) transition(AppMode::IDLE); break;
            case AppMode::IDLE:   if (buttonPressed) transition(AppMode::ACTIVE); break;
            case AppMode::ACTIVE:
                if (timeoutReached(now)) transition(AppMode::SLEEP);
                if (errorDetected) transition(AppMode::ERROR);
                break;
            case AppMode::ERROR:  if (recoveryCondition) transition(AppMode::IDLE); break;
        }
    }
private:
    AppMode mode = AppMode::SLEEP;
    void transition(AppMode newMode) { onExit(mode); mode = newMode; onEnter(mode); }
    void onEnter(AppMode m) { /* moda giriş hazırlığı */ }
    void onExit(AppMode m)  { /* modadan çıkış temizliği */ }
};
```

### 5.4 Yükleme ve Dağıtım (eski §11.1)

```bash
# Varsayılan ortama yükle
pio run -t upload
# Belirli ortama yükle
pio run -e esp32dev -t upload
pio run -e uno -t upload
# OTA ile yükle (ESP32)
pio run -e esp32dev -t upload --upload-port 192.168.1.100
# Dosya sistemini yükle (SPIFFS/LittleFS)
pio run -t uploadfs
```

### 5.5 CI: Derleme Kontrolü ve GitHub Actions (eski §11.2-§11.3)

```bash
#!/bin/bash
# ci/compile-check.sh — her commit'te GitHub Actions tarafından çalışır
echo "Running PlatformIO compile check..."
pio check --fail-on-defect=medium --fail-on-violation=high || exit 1
pio run -e native -e esp32dev || exit 1
echo "✅ Compile check passed"
```

```yaml
# .github/workflows/arduino-ci.yml
name: Arduino CI
on: [push, pull_request]
jobs:
  compile:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-python@v5
        with:
          python-version: '3.12'
      - name: Install PlatformIO
        run: pip install platformio
      - name: Compile all targets
        run: pio run -e native -e esp32dev
      - name: Static analysis
        run: pio check --fail-on-defect=medium
```

### 5.6 Arduino IDE → PlatformIO Geçişi (eski §16.1)

| Adım | Aksiyon | Komut / Not |
|------|---------|-------------|
| 1 | PlatformIO kur | `pip install platformio` veya VS Code eklentisi |
| 2 | `platformio.ini` oluştur | §5.8 şablonuna bak |
| 3 | `sketch.ino` → `src/main.cpp` taşı | `#include <Arduino.h>` ekle |
| 4 | Kütüphaneleri `lib_deps`'e taşı | `lib/` klasöründen kaldır |
| 5 | SPIFFS için `data/` klasörünü taşı | Proje köküne yerleştir |
| 6 | Derleme testi | `pio run` |
| 7 | Yükleme | `pio run -t upload` |
| 8 | CI ekle | `.github/workflows/arduino-ci.yml` oluştur |

**Temel farklar:** Arduino IDE örtük include + tek dosya; PlatformIO açık include (`#include <Arduino.h>`, `#include <Wire.h>`).

### 5.7 Governance: Sürüm ve `secrets.h` Yönetimi (eski §9.2-§9.3)

- Firmware sürümü `src/version.h` içinde: `FIRMWARE_MAJOR/MINOR/PATCH` + `FIRMWARE_VERSION "1.3.0"`.
- Git etiketi firmware sürümüyle eşleşir: `git tag -a "v1.3.0" -m "Release 1.3.0"`.
- CI her commit'te `pio check` çalıştırır.
- `.gitignore`'da kesinlikle: `include/secrets.h`.

### 5.8 `platformio.ini` Şablonu (eski §9.1)

```ini
[platformio]
default_envs = esp32dev
src_dir = src
test_dir = test
monitor_speed = 115200

[env]
build_flags =
    -std=gnu++17
    -Wall -Wextra -Werror
    -DARDUINO_USB_CDC_ON_BOOT=1   ; native USB serial
lib_deps =

[env:native]
platform = native
build_type = debug
test_build_project_src = true

[env:esp32dev]
platform = espressif32
board = esp32dev
framework = arduino
board_build.f_cpu = 240000000L
board_build.flash_mode = qio
monitor_filters = time
build_type = release

[env:uno]
platform = atmelavr
board = uno
framework = arduino
build_type = release

[env:mega2560]
platform = atmelavr
board = megaatmega2560
framework = arduino
build_type = release
```

---

## 6. Doğrulama

### 6.1 Birim Testleri (PlatformIO Native — eski §6.1)

```cpp
// test/test_main.cpp
#include <unity.h>

void test_non_blocking_timing(void) {
    unsigned long start = millis();
    unsigned long prev = 0;
    constexpr unsigned long INTERVAL = 100;
    for (int i = 0; i < 10; i++) {
        unsigned long now = millis();
        if (now - prev >= INTERVAL) prev = now;
    }
    TEST_ASSERT_TRUE(millis() - start >= 1000);
}
void setup() { UNITY_BEGIN(); RUN_TEST(test_non_blocking_timing); UNITY_END(); }
void loop() {}
```

```bash
pio test -e native   # host'ta çalışır (hızlı, donanım yok)
pio test -e esp32dev # cihazda çalışır (yavaş, donanım gerekli)
```

### 6.2 HITL (Hardware-in-the-Loop — eski §6.2)

| Test Türü | Araç | Kapsam |
|-----------|------|--------|
| Pin anahtarlama | Osiloskop / lojik analizör | Zamanlama %1 doğrulukta |
| Seri protokol | Python `pyserial` test aracı | Komut/yanıt doğrulama |
| I2C/SPI bus | Bus pirate / lojik analizör | Protokol uyumu |
| Güç tüketimi | DMM / akım probu | Spec ±%10 içinde |

### 6.3 Hata Ayıklama Güvenceleri (eski §6.3)

```cpp
// Yalnız ayıklama baskıları — sürüm derlemesinde -DNDEBUG ile kalkar
#include <assert.h>
void setMotorSpeed(int speed) {
    assert(speed >= 0 && speed <= 255);
    analogWrite(MOTOR_PIN, speed);
}
```

### 6.4 Performans Sınırları (eski §12.1)

| Metrik | Hedef | Uygulama |
|--------|-------|----------|
| **Döngü süresi** | <10 ms (100 Hz) | `micros()` profilleme |
| **ISR süresi** | <50 µs | ISR minimal tutulur (yalnız bayrak) |
| **SRAM kullanımı** | Toplamın %80'i altı | `pio check --section=.bss` |
| **Flash kullanımı** | Toplamın %90'ı altı | `pio size --json` |
| **Watchdog zaman aşımı** | 8 saniye | `wdt_enable(WDTO_8S)` |
| **WiFi yeniden bağlanma** | <5 saniye | Üstel geri çekilme tekrarı |

**SRAM bütçe ölçümü (AVR):**

```cpp
extern int __heap_start, *__brkval;
int freeSRAM() {
    int v;
    return (int)&v - (__brkval == 0 ? (int)&__heap_start : (int)__brkval);
}
```

**Optimizasyon önceliği (etkiye göre):** 1) `String` nesnelerini kaldır (`char[]` + `sprintf`) · 2) dize literallerini `F()` ile flash'a taşı · 3) arama tablolarını `PROGMEM`'e taşı · 4) tampon boyutlarını küçült · 5) izin veriyorsa `int`/`float` yerine `uint8_t`/`int16_t`.

**Döngü titremesi ölçümü:** `micros()` ile her döngü süresi ölçülür, en kötü değer her 1000 döngüde raporlanır (eski §12.3 kod bloğu korunmuştur — kaynak dosyada `maxLoopTime` profilleme kalıbı).

### 6.5 Sorun Giderme Tablosu (eski §15.1)

| Belirti | Olası Neden | Kontrol | Düzeltme |
|---------|-------------|---------|----------|
| **Yükleme başarısız** | Yanlış board/port | `pio board list`, `pio device list` | `board = esp32dev`, `upload_port = COM3` |
| **Seri bozuk** | Baud uyumsuzluğu | Monitör baud == kod baud | Her ikisinde `monitor_speed = 115200` |
| **Brownout** | Yetersiz güç kaynağı | VCC'yi multimetre ile ölç | MCU yanına 100 µF kapasitör, regüle besleme |
| **Watchdog döngüsü** | Döngü 8 saniyeden uzun | `micros()` profilleme | Her aşamayı profille, `wdt_reset()`'i erkene al |
| **WiFi kopması** | RF paraziti / menzil | WiFi RSSI kontrolü | Anteni taşı, TX gücünü azalt |
| **I2C askıda** | Bus kiliti (SCL alçak takılı) | SDA/SCL lojik analizör | `Wire.setTimeout(50)`; bus kurtarma darbeleri |
| **Flash bozulması** | Yazma sırasında güç kesintisi | Açılışta CRC | Yalnız yapılandırma değişince `EEPROM.commit()` |
| **Bellek sızıntısı** | `String` birleştirme | `freeSRAM()` kontrolü | `String` → `char[]` + `sprintf` |
| **Zamanlayıcı çakışması** | İki kütüphane aynı timer | Kütüphane dokümanı | Farklı timer (Timer1 vs Timer2) |
| **OSC başarısız** | AVR'de yanlış fuse bitleri | avrdude ile fuse oku | `avrdude -p m328p -U lfuse:r:-:h` |

### 6.6 Hata Ayıklama Araçları (eski §15.2)

| Araç | Ne zaman | Komut / Kurulum |
|------|----------|-----------------|
| **Seri monitör** | Genel hata ayıklama | `pio device monitor` |
| **Lojik analizör** | I2C/SPI/UART protokol sorunları | Saleae Logic 2 (2 kanal için ücretsiz) |
| **Osiloskop** | PWM, kesme zamanlaması, brownout | Minimum 100 MHz bant genişliği |
| **Multimetre** | Gerilim, süreklilik, akım | Önce VCC ölç — sorunların %80'i |
| **PlatformIO debugger** | Adım adım hata ayıklama | `pio debug --interface=gdb` (ESP32) |

### 6.7 Kabul Kontrol Listesi

- [ ] Frontmatter 7 alan tam (title, type, category, version, status, authority, updated)
- [ ] H1 + §1-§7 sırası sabit, bölüm adları değiştirilmedi
- [ ] `{{PLACEHOLDER}}` kalmadı
- [ ] Bütün wiki-link'ler `.md` uzantısız ve geçerli yol formatında
- [ ] Sürüm/standart iddiaları §7 kaynaklarıyla eşleşiyor; doğrulanamayanlar `⚠️ VERIFICATION REQUIRED`
- [ ] Kaynak dosya (eski şablon) değiştirilmedi/silinmedi

---

## 7. Referanslar

### 7.1 Vault Bağlantıları

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Template registry | [[.templates/index]] | Envanter (DRY), §3.2 iskelet |
| Vault anayasası | [[../CLAUDE.md]] | Hard Guardrails (16) |
| Agent registry | [[../AGENTS.md]] | §6 routing (embedded → C++/DSP) |
| Hardware şablonu | [[hardware/hardware-template]] | Genel donanım tasarım dokümanı |
| AVR şablonu | [[hardware/avr-template]] | Bare-metal AVR (framework'süz) |
| PIC şablonu | [[hardware/pic-template]] | Microchip PIC platformu |
| C şablonu | [[../other/c-template]] | C99/C11 non-Arduino embede (STM32, PIC) |
| C++ şablonu | [[../other/cpp-template]] | Cortex-M/RISC-V için tam C++17/RTOS |
| ADR Audio | [[../adr/adr-audio-template]] | Ses donanımı mimari kararları |

### 7.2 Şablon Seçim Tablosu (eski §18.1)

| Kullanım Senaryosu | Şablon | Gerekçe |
|--------------------|--------|---------|
| Hızlı prototipleme, sensör, ekran | **Arduino** (bu şablon) | Hızlı geliştirme, geniş kütüphane ekosistemi |
| Ultra düşük gecikme (<1 ms ISR) | **AVR** | Framework yükü yok, doğrudan register erişimi |
| STM32, PIC veya bare-metal | **C** | C99 yönetilen kod, HAL-only, deterministik |
| RTOS, çok iş parçacıklı, büyük proje | **C++** | FreeRTOS, tam OOP, STL konteynerleri |

### 7.3 Çapraz Teknoloji İlişkileri (eski §18)

| Şablon | İlişki |
|--------|--------|
| `[[hardware/avr-template]]` | Bare-metal AVR (Arduino framework'süz) — register düzeyi kontrol, daha küçük binary, daha düşük gecikme |
| `[[../other/c-template]]` | Non-Arduino embede (STM32, PIC) için C99/C11 — yalnız HAL, C++ çalışma zamanı yok |
| `[[../other/cpp-template]]` | Cortex-M/RISC-V için tam C++17 — RTOS desteği, STL alt kümesi, Arduino yükü yok |

### 7.4 Doğrulanmış Sürüm ve Standart Kaynakları (web doğrulaması — 2026-09-29)

| Öğe | Doğrulanmış değer | Kaynak |
|-----|-------------------|--------|
| Arduino IDE | **2.3.10** (Haziran 2026, güncel sürüm; 2.3.11 geliştirme) | arduino.cc GitHub release notları |
| PlatformIO Core | **6.1.19** (2026-02-04, kararlı; 6.2.0 yayımlanmadı) | platformio.org / GitHub releases |
| arduino-esp32 core | **3.3.11** (2026-07-22) | github.com/espressif/arduino-esp32 releases |
| EMC emisyon | **EN 55032:2015+A11:2020** | CENELEC standart indeksi |
| EMC bağışıklık | **EN 55035:2017+A11:2020** | CENELEC standart indeksi |
| Radyo (WiFi/BT, ESP32) | **EN 300 328 V2.2.2 (2019-07)** — 2021-08-06'dan beri zorunlu | ETSI standart sayfası |
| Güvenlik | **EN IEC 62368-1:2020+A11:2020** (OJEU harmonize; son sürüm 2024+A11:2024; EN 62368-1:2014 2026-01-07'de geri çekildi) | AB Uyumlu Standartlar Listesi |
| RoHS | **EN IEC 63000:2018** (EN 50581:2012 2021-11-18'de geri çekildi) | AB Uyumlu Standartlar Listesi |
| ATmega328P / ATtiny85 / ESP32 pin-sayıları | Eski dokümandaki değerler doğrulandı (ATtiny85 ADC **4 kanal**) | Üretici datasheet'leri |

- Eski dokümandaki `EN 55032:2015`, `EN 55035:2017`, `EN 62368-1` ve `EN 50581:2012` referansları §7.4'teki güncel sürümlerle **düzeltilmiştir**.
- Fiyat, tolerans ve ölçümlere dayanmayan her iddia `⚠️ VERIFICATION REQUIRED` taşır (ör. güç tüketimi/batarya ömrü hesapları — ölçümle doğrulanacak).

### 7.5 Sürüm Kaydı

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 1.0 | 2026-06-24 | İlk Arduino şablonu (eski spec, 1273 satır, `.templates/arduino-template.md`) |
| 1.0.0 | 2026-09-29 | Guardrail #16 7-alanlı frontmatter + H1 + §1-§7 iskeletine yeniden düzenlendi; web doğrulaması (IDE 2.3.10, PlatformIO 6.1.19, esp32 core 3.3.11, EN standart güncellemeleri) uygulandı; wiki-link yolları düzeltildi |

---

**Template Version:** 1.0.0
**Last Updated:** 2026-09-29
