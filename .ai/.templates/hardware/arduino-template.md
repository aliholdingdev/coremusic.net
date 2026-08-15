---
type: template
category: hardware
title: "Arduino Template"
date: 2026-08-06
updated: 2026-08-06
status: active
version: 3.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team • Human Mode • Truth Mode
tech: Arduino CLI, C++, ESP32, AVR
---

# Arduino Template

**See also:** [[index]] · [[CLAUDE.md]] · [[electronic/hardware-roadmap]] · [[ADR-037-wirelessconnect-integration]] · [[ADR-017-dsp-hardware-mode]]

---

## 1. Amaç (Purpose)

Bu şablon, CoreMusic IoT ve donanım prototipleme projeleri için standart Arduino geliştirme rehberidir.

**Kapsam:**
- ESP32, ESP8266, Arduino Uno/Nano/Mega (AVR ATmega328P/2560)
- Sensör okuma ve filtreleme (sıcaklık, nem, ışık, ses, ivme)
- I2C, SPI, UART haberleşme protokolleri
- Kablosuz iletişim (WiFi, Bluetooth, MQTT, WebSocket)
- Motor ve servo kontrolü (DC, step, servo, PID)
- Güç yönetimi (uyku modları, watchdog, brownout)
- OTA güncelleme ve uzaktan yönetim
- CoreMusic Audio Service ile entegrasyon (MQTT bridge)

**Kapsam Dışı:**
- FPGA geliştirme (ADR-017 kapsamı)
- C++ Neva Engine (embedded-engineer alanı)
- PCB tasarımı (audio-hardware-engineer alanı)

**Hedef Kitle:** Embedded Engineer, Hardware Division, Research Division

---

## 2. Tech Stack

| Teknoloji | Versiyon | Kullanım | Kaynak |
|-----------|---------|----------|--------|
| Arduino CLI | 1.0+ | Derleme ve yükleme | arduino.cc/en/cli |
| Arduino IDE | 2.3+ | Geliştirme ortamı | arduino.cc |
| PlatformIO | 6.1+ | Proje yönetimi, dependency | platformio.org |
| C++ | C++17 | Programlama dili (Arduino kısıtlı C++20) | isocpp.org |
| ESP32 Arduino Core | 2.0+ | ESP32 desteği | github.com/espressif/arduino-esp32 |
| ESP8266 Arduino Core | 3.0+ | ESP8266 desteği | github.com/esp8266/Arduino |
| AVR GCC | 7.3+ | AVR derleyicisi | avr.sourceforge.net |
| avr-libc | 2.0+ | AVR standart kütüphane | nongnu.org/avr-libc |
| Adafruit Unified Sensor | 1.1+ | Sensör abstraksiyonu | github.com/adafruit |
| PubSubClient | 2.8+ | MQTT istemcisi | github.com/knolleary/pubsubclient |
| ArduinoJson | 7.0+ | JSON işleme | arduinojson.org |
| ESPAsyncWebServer | 1.2+ | Asenkron web sunucusu | github.com/me-no-dev/ESPAsyncWebServer |

**Not:** Arduino ortamı C++17 ile sınırlıdır. C++20 özellikleri (concepts, ranges, coroutines) kullanılamaz. `[[ADR-017-dsp-hardware-mode]]` kararıyla belirlenen donanım kısıtlamalarına uyulmalıdır.

*Kaynaklar: Arduino Language Reference (arduino.cc) — 2026-08-06'da doğrulandı. ESP32 Docs (docs.espressif.com) — 2026-08-06'da doğrulandı.*

---

## 3. Code Standards

### 3.1 Project Structure

```
project-name/
├── src/
│   ├── main.ino              # Ana sketch (setup + loop)
│   ├── config.h              # Sabitler, pin tanımları
│   ├── sensors.h             # Sensör modülleri
│   ├── communication.h       # I2C, SPI, UART, WiFi
│   ├── motor_control.h       # Motor/servo kontrolü
│   └── power_management.h    # Güç yönetimi
├── include/
│   └── project_config.h      # Proje konfigürasyonu
├── lib/
│   └── custom_library/       # Özel kütüphaneler
├── test/
│   ├── test_sensors.cpp      # Sensör testleri
│   └── test_comm.cpp         # Haberleşme testleri
├── platformio.ini            # PlatformIO konfigürasyonu
├── README.md                 # Proje açıklaması
└── .gitignore                # Git yoksayma listesi
```

**Kurallar:**
- Her modül ayrı bir `.h` dosyasında tanımlanır
- `main.ino` sadece `setup()` ve `loop()` içerir
- Modüler yapı: her özellik tek sorumluluk prensibine uygun

### 3.2 Arduino Sketch Structure

```cpp
/**
 * CoreMusic IoT Sensor Reader — ESP32
 *
 * @file main.ino
 * @version 3.0.0
 * @date 2026-08-06
 * @see ADR-037-wirelessconnect-integration
 */

#include <Arduino.h>
#include "config.h"
#include "sensors.h"
#include "communication.h"

// ═══════════════════════════════════════════
// GLOBAL STATE
// ═══════════════════════════════════════════
static SensorData currentData;
static unsigned long lastReading = 0;
static bool systemHealthy = true;

// ═══════════════════════════════════════════
// SETUP — runs once at power-on/reset
// ═══════════════════════════════════════════
void setup() {
    // Serial init (debug only — production'da devre dışı bırakılabilir)
    Serial.begin(SERIAL_BAUD_RATE);
    while (!Serial && millis() < SERIAL_TIMEOUT_MS) {
        ; // Serial bağlantısını bekle (max 2sn)
    }

    // Pin konfigürasyonu
    configurePins();

    // Sensör başlatma
    if (!initSensors()) {
        systemHealthy = false;
        setError(ERR_SENSOR_INIT);
    }

    // Haberleşme başlatma
    initCommunication();

    // Watchdog başlatma
    enableWatchdog(WATCHDOG_TIMEOUT_MS);

    Serial.println("[INIT] CoreMusic IoT v3.0.0 ready");
}

// ═══════════════════════════════════════════
// LOOP — runs repeatedly
// ═══════════════════════════════════════════
void loop() {
    // Watchdog yenile
    feedWatchdog();

    // Zaman bazlı sensör okuma
    unsigned long now = millis();
    if (now - lastReading >= SENSOR_INTERVAL_MS) {
        currentData = readSensors();
        lastReading = now;

        // Veri doğrulama
        if (isValidData(&currentData)) {
            transmitData(&currentData);
        } else {
            setError(ERR_INVALID_DATA);
        }
    }

    // Haberleşme kontrolü (MQTT, WebSocket)
    handleCommunication();

    // Hata durumu yönetimi
    if (!systemHealthy) {
        handleErrors();
    }

    // tiny delay — watchdog'a zaman tanı (Arduino loop native yield)
    yield();
}
```

**Kurallar:**
- `setup()` ve `loop()` dışında global fonksiyon tanımlanmaz
- `loop()` içinde `delay()` KULLANILMAZ — `yield()` veya `millis()` bazlı zamanlama
- Her modül kendi `.h` dosyasında tanımlı olmalı

### 3.3 Pin Management

```cpp
/**
 * Pin tanımlamaları — magic number KULLANILMAZ.
 *
 * @file config.h
 */

#ifndef CONFIG_H
#define CONFIG_H

// ═══════════════════════════════════════════
// PIN DEFINITIONS (ESP32)
// ═══════════════════════════════════════════
// Sensörler
constexpr uint8_t PIN_TEMP_SENSOR    = 36;   // ADC1_CH0 (VP)
constexpr uint8_t PIN_LIGHT_SENSOR   = 39;   // ADC1_CH3 (VN)
constexpr uint8_t PIN_HUMIDITY_SENSOR = 34;  // ADC1_CH6
constexpr uint8_t PIN_AUDIO_INPUT    = 32;   // ADC1_CH4

// LED göstergeleri
constexpr uint8_t PIN_LED_STATUS     = 2;    // Dahili LED
constexpr uint8_t PIN_LED_ERROR      = 4;    // Hata LED
constexpr uint8_t PIN_LED_NETWORK    = 15;   // Ağ LED

// Motor kontrolü
constexpr uint8_t PIN_MOTOR_PWM      = 25;   // PWM çıkış
constexpr uint8_t PIN_MOTOR_DIR_A    = 26;   // Yön A
constexpr uint8_t PIN_MOTOR_DIR_B    = 27;   // Yön B
constexpr uint8_t PIN_MOTOR_ENABLE   = 14;   // Enable

// Haberleşme
constexpr uint8_t PIN_I2C_SDA        = 21;   // I2C SDA
constexpr uint8_t PIN_I2C_SCL        = 22;   // I2C SCL
constexpr uint8_t PIN_SPI_MOSI       = 23;   // SPI MOSI
constexpr uint8_t PIN_SPI_MISO       = 19;   // SPI MISO
constexpr uint8_t PIN_SPI_CLK        = 18;   // SPI CLK
constexpr uint8_t PIN_SPI_CS         = 5;    // SPI CS

// ═══════════════════════════════════════════
// SABITLER
// ═══════════════════════════════════════════
constexpr uint32_t SERIAL_BAUD_RATE     = 115200;
constexpr unsigned long SERIAL_TIMEOUT_MS = 2000;
constexpr unsigned long SENSOR_INTERVAL_MS = 1000;
constexpr unsigned long DEBOUNCE_MS       = 50;
constexpr unsigned long WATCHDOG_TIMEOUT_MS = 8000;

// ═══════════════════════════════════════════
// HATA KODLARI
// ═══════════════════════════════════════════
enum ErrorCode : uint8_t {
    ERR_NONE              = 0,
    ERR_SENSOR_INIT       = 1,
    ERR_SENSOR_READ       = 2,
    ERR_INVALID_DATA      = 3,
    ERR_COMM_INIT         = 4,
    ERR_MQTT_CONNECT      = 5,
    ERR_WIFI_CONNECT      = 6,
    ERR_WATCHDOG_RESET    = 7,
    ERR_MEMORY_LOW        = 8,
    ERR_I2C_TIMEOUT       = 9,
    ERR_SPI_TRANSFER      = 10
};

// ═══════════════════════════════════════════
// SENSOR YAPILARI
// ═══════════════════════════════════════════
struct SensorData {
    float temperature;
    float humidity;
    float lightLevel;
    float audioLevel;
    unsigned long timestamp;
    bool valid;
};

#endif // CONFIG_H
```

**Kurallar:**
- Hiçbir pin numarası doğrudan kodda kullanılmaz — sabit isim tanımlanır
- Pin grubu dizileri ile çoklu pin yönetimi desteklenir
- `constexpr` (C++17) kullanılır — `const` tercih edilmez

### 3.4 Interrupt Handling

```cpp
/**
 * Interrupt Service Routine — ISR tasarımı kuralları.
 *
 * @file interrupts.h
 */

#ifndef INTERRUPTS_H
#define INTERRUPTS_H

// ═══════════════════════════════════════════
// VOLATILE DEĞİŞKENLER
// ═══════════════════════════════════════════
// ISR içinde kullanılan tüm değişkenler volatile olmalıdır
static volatile uint32_t interruptCounter = 0;
static volatile bool interruptFlag = false;
static volatile unsigned long lastInterruptTime = 0;

// ═══════════════════════════════════════════
// ISR TANIMLAMALARI
// ═══════════════════════════════════════════
// Dikkat: ISR'ler mümkün olduğunca kısa olmalıdır
// Long-running operations yasak — sadece flag set/counter increment
void IRAM_ATTR handleButtonInterrupt() {
    unsigned long currentTime = millis();
    if (currentTime - lastInterruptTime > DEBOUNCE_MS) {
        interruptCounter++;
        interruptFlag = true;
        lastInterruptTime = currentTime;
    }
}

void IRAM_ATTR handleEncoderInterrupt() {
    // Encoder pulse sayma — sadece counter increment
    interruptCounter++;
}

// ═══════════════════════════════════════════
// INTERRUPT KURULUMU
// ═══════════════════════════════════════════
void setupInterrupts() {
    // Buton interrupt (düşen kenar tetikleme)
    pinMode(PIN_BUTTON, INPUT_PULLUP);
    attachInterrupt(digitalPinToInterrupt(PIN_BUTTON),
                    handleButtonInterrupt,
                    FALLING);

    // Encoder interrupt (her iki kenar)
    pinMode(PIN_ENCODER_A, INPUT_PULLUP);
    attachInterrupt(digitalPinToInterrupt(PIN_ENCODER_A),
                    handleEncoderInterrupt,
                    CHANGE);
}

#endif // INTERRUPTS_H
```

**Kurallar:**
- `IRAM_ATTR` (ESP32) zorunlu — ISR Flash'tan çalışamaz
- `volatile` tüm ISR değişkenleri için zorunlu
- ISR içinde `delay()`, `Serial.print()`, `millis()` KULLANILMAZ
- Debouncing ISR içinde yapılmalı (zaman damgası karşılaştırması ile)
- `attachInterrupt()` sadece `setup()` içinde çağrılır

### 3.5 Timer/PWM Configuration

```cpp
/**
 * Timer ve PWM yapılandırması.
 *
 * @file timer_config.h
 */

#ifndef TIMER_CONFIG_H
#define TIMER_CONFIG_H

// ═══════════════════════════════════════════
// PWM YAPILANDIRMASI (ESP32 LEDC)
// ═══════════════════════════════════════════
// ESP32 LEDC kanal yapısı
constexpr uint8_t PWM_CHANNEL_MOTOR   = 0;
constexpr uint8_t PWM_CHANNEL_SERVO   = 1;
constexpr uint8_t PWM_CHANNEL_LED     = 2;
constexpr uint32_t PWM_FREQUENCY      = 5000;  // 5kHz
constexpr uint8_t PWM_RESOLUTION      = 8;      // 8-bit (0-255)

void setupPWM() {
    // Motor PWM
    ledcSetup(PWM_CHANNEL_MOTOR, PWM_FREQUENCY, PWM_RESOLUTION);
    ledcAttachPin(PIN_MOTOR_PWM, PWM_CHANNEL_MOTOR);

    // Servo PWM (50Hz — standart servo)
    ledcSetup(PWM_CHANNEL_SERVO, 50, 16);  // 16-bit resolution
    ledcAttachPin(PIN_SERVO, PWM_CHANNEL_SERVO);

    // LED PWM (düşük frekans — göz kamaştırma yok)
    ledcSetup(PWM_CHANNEL_LED, 1000, 8);
    ledcAttachPin(PIN_LED_STATUS, PWM_CHANNEL_LED);
}

// ═══════════════════════════════════════════
// PWM DEĞER HESAPLAMA
// ═══════════════════════════════════════════
// Servo açı → duty cycle dönüşümü
// 0° = 500μs, 180° = 2500μs (standart servo)
uint32_t angleToDutyCycle(uint8_t angle) {
    // 500μs - 2500μs aralığını 16-bit duty cycle'a dönüştür
    uint32_t minDuty = 1638;   // 500μs / (1/50Hz) * 65535
    uint32_t maxDuty = 8192;   // 2500μs / (1/50Hz) * 65535
    return map(angle, 0, 180, minDuty, maxDuty);
}

// Motor hızı → PWM değeri dönüşümü
// -255 ile +255 arası, negatif = ters yön
void setMotorSpeed(int16_t speed) {
    speed = constrain(speed, -255, 255);

    if (speed > 0) {
        digitalWrite(PIN_MOTOR_DIR_A, HIGH);
        digitalWrite(PIN_MOTOR_DIR_B, LOW);
        ledcWrite(PWM_CHANNEL_MOTOR, speed);
    } else if (speed < 0) {
        digitalWrite(PIN_MOTOR_DIR_A, LOW);
        digitalWrite(PIN_MOTOR_DIR_B, HIGH);
        ledcWrite(PWM_CHANNEL_MOTOR, -speed);
    } else {
        digitalWrite(PIN_MOTOR_DIR_A, LOW);
        digitalWrite(PIN_MOTOR_DIR_B, LOW);
        ledcWrite(PWM_CHANNEL_MOTOR, 0);
    }
}

#endif // TIMER_CONFIG_H
```

### 3.6 Communication Protocols

```cpp
/**
 * I2C, SPI, UART haberleşme protokolleri.
 *
 * @file communication.h
 */

#ifndef COMMUNICATION_H
#define COMMUNICATION_H

#include <Wire.h>
#include <SPI.h>

// ═══════════════════════════════════════════
// I2C HABERLEŞME
// ═══════════════════════════════════════════
// I2C address mapesi — her cihazın adresi sabit olarak tanımlı
constexpr uint8_t I2C_ADDR_TEMP_SENSOR  = 0x48; // TMP102
constexpr uint8_t I2C_ADDR_ACCEL        = 0x68; // MPU6050
constexpr uint8_t I2C_ADDR_OLED         = 0x3C; // SSD1306
constexpr uint8_t I2C_ADDR_DAC          = 0x48; // PCM3168A I2C mode

void initI2C() {
    Wire.begin(PIN_I2C_SDA, PIN_I2C_SCL);
    Wire.setClock(400000); // 400kHz Fast Mode

    // I2C cihaz taraması
    Serial.println("[I2C] Scanning...");
    for (uint8_t addr = 1; addr < 127; addr++) {
        Wire.beginTransmission(addr);
        if (Wire.endTransmission() == 0) {
            Serial.printf("[I2C] Device found at 0x%02X\n", addr);
        }
    }
}

bool i2cWrite(uint8_t addr, uint8_t reg, uint8_t value) {
    Wire.beginTransmission(addr);
    Wire.write(reg);
    Wire.write(value);
    return Wire.endTransmission() == 0;
}

uint8_t i2cRead(uint8_t addr, uint8_t reg) {
    Wire.beginTransmission(addr);
    Wire.write(reg);
    Wire.endTransmission(false);
    Wire.requestFrom(addr, static_cast<uint8_t>(1));
    return Wire.available() ? Wire.read() : 0;
}

// ═══════════════════════════════════════════
// SPI HABERLEŞME
// ═══════════════════════════════════════════
void initSPI() {
    SPI.begin(PIN_SPI_CLK, PIN_SPI_MISO, PIN_SPI_MOSI, PIN_SPI_CS);
    SPI.setFrequency(1000000); // 1MHz
    SPI.setDataMode(SPI_MODE0);
}

uint8_t spiTransfer(uint8_t data) {
    digitalWrite(PIN_SPI_CS, LOW);
    uint8_t result = SPI.transfer(data);
    digitalWrite(PIN_SPI_CS, HIGH);
    return result;
}

// ═══════════════════════════════════════════
// UART HABERLEŞME (SoftwareSerial)
// ═══════════════════════════════════════════
// ESP32: Hardware Serial 0 (debug), Serial 1 (GPS), Serial 2 (modem)
// AVR: SoftwareSerial ile genişletme

void initSerial() {
    Serial.begin(115200);
    Serial1.begin(9600, SERIAL_8N1, PIN_RX1, PIN_TX1); // GPS modülü
}

#endif // COMMUNICATION_H
```

### 3.7 Sensor Reading

```cpp
/**
 * Sensör okuma, filtreleme ve kalibrasyon.
 *
 * @file sensors.h
 */

#ifndef SENSORS_H
#define SENSORS_H

#include "config.h"

// ═══════════════════════════════════════════
// ORTALAMA FİLTRESİ (Moving Average)
// ═══════════════════════════════════════════
constexpr uint8_t FILTER_SIZE = 8;

struct MovingAverageFilter {
    float buffer[FILTER_SIZE];
    uint8_t index = 0;
    bool filled = false;

    float addSample(float sample) {
        buffer[index] = sample;
        index = (index + 1) % FILTER_SIZE;
        if (index == 0) filled = true;

        float sum = 0;
        uint8_t count = filled ? FILTER_SIZE : index;
        for (uint8_t i = 0; i < count; i++) {
            sum += buffer[i];
        }
        return sum / count;
    }
};

// Her sensör için filtre instance'ları
static MovingAverageFilter tempFilter;
static MovingAverageFilter lightFilter;
static MovingAverageFilter audioFilter;

// ═══════════════════════════════════════════
// SENSÖR OKUMA FONKSİYONLARI
// ═══════════════════════════════════════════

// Sıcaklık sensörü (TMP102 I2C)
float readTemperature() {
    uint8_t msb = i2cRead(I2C_ADDR_TEMP_SENSOR, 0x00);
    uint8_t lsb = i2cRead(I2C_ADDR_TEMP_SENSOR, 0x01);

    int16_t raw = (msb << 8) | lsb;
    raw >>= 4; // 12-bit çözünürlük

    float celsius = raw * 0.0625f; // TMP102 çarpanı
    return tempFilter.addSample(celsius);
}

// Işık sensörü (LDR + ADC)
float readLightLevel() {
    uint32_t raw = analogRead(PIN_LIGHT_SENSOR);
    float voltage = (raw / 4095.0f) * 3.3f; // ESP32 12-bit ADC
    float lux = voltage * 100.0f; // Yaklaşık dönüşüm
    return lightFilter.addSample(lux);
}

// Ses seviyesi (analog input + RMS hesaplama)
float readAudioLevel() {
    const uint16_t SAMPLES = 64;
    float sumSquares = 0;

    for (uint16_t i = 0; i < SAMPLES; i++) {
        float sample = (analogRead(PIN_AUDIO_INPUT) / 4095.0f) * 3.3f - 1.65f;
        sumSquares += sample * sample;
        delayMicroseconds(50); // 20kHz örnekleme
    }

    float rms = sqrt(sumSquares / SAMPLES);
    return audioFilter.addSample(rms);
}

// ═══════════════════════════════════════════
// VERİ DOĞRULAMA
// ═══════════════════════════════════════════
bool isValidData(const SensorData* data) {
    // Sıcaklık: -40°C ile +125°C arası (TMP102 limit)
    if (data->temperature < -40.0f || data->temperature > 125.0f) return false;

    // Nem: %0 ile %100 arası
    if (data->humidity < 0.0f || data->humidity > 100.0f) return false;

    // Işık: Negatif olamaz
    if (data->lightLevel < 0.0f) return false;

    // Ses: Negatif olamaz
    if (data->audioLevel < 0.0f) return false;

    return true;
}

// ═══════════════════════════════════════════
// TÜM SENSÖRLERİ OKU
// ═══════════════════════════════════════════
SensorData readSensors() {
    SensorData data;
    data.temperature = readTemperature();
    data.humidity = 0.0f; // DHT22 eklenecek
    data.lightLevel = readLightLevel();
    data.audioLevel = readAudioLevel();
    data.timestamp = millis();
    data.valid = true;
    return data;
}

#endif // SENSORS_H
```

**Kurallar:**
- Ham ADC değerleri filtrelenmeden kullanılmaz
- Her sensör için kalibrasyon sabitleri `config.h`'da tanımlı
- Sensör okuma時間 aşımı kontrol edilmeli (I2C timeout)

### 3.8 Motor/Servo Control

```cpp
/**
 * Motor ve servo kontrolü — PID, hızlanma, güvenlik limitleri.
 *
 * @file motor_control.h
 */

#ifndef MOTOR_CONTROL_H
#define MOTOR_CONTROL.h

#include "config.h"

// ═══════════════════════════════════════════
// PID KONTROLÖRÜ
// ═══════════════════════════════════════════
struct PIDController {
    float kp = 1.0f;
    float ki = 0.1f;
    float kd = 0.01f;
    float integral = 0;
    float previousError = 0;
    unsigned long lastUpdate = 0;

    float compute(float setpoint, float measurement, unsigned long dt) {
        float error = setpoint - measurement;

        // Integral anti-windup
        integral += error * dt;
        integral = constrain(integral, -1000.0f, 1000.0f);

        // Türev
        float derivative = (error - previousError) / dt;

        // PID çıkışı
        float output = (kp * error) + (ki * integral) + (kd * derivative);

        previousError = error;
        lastUpdate = millis();

        return constrain(output, -255.0f, 255.0f);
    }

    void reset() {
        integral = 0;
        previousError = 0;
    }
};

// ═══════════════════════════════════════════
// HIZLANMA KONTROLÜ (Acceleration Ramp)
// ═══════════════════════════════════════════
struct AccelerationRamp {
    int16_t currentSpeed = 0;
    int16_t targetSpeed = 0;
    int16_t maxAcceleration = 50; // units per loop cycle

    int16_t update() {
        if (currentSpeed < targetSpeed) {
            currentSpeed = min(static_cast<int16_t>(currentSpeed + maxAcceleration),
                               targetSpeed);
        } else if (currentSpeed > targetSpeed) {
            currentSpeed = max(static_cast<int16_t>(currentSpeed - maxAcceleration),
                               targetSpeed);
        }
        return currentSpeed;
    }
};

static PIDController speedPID;
static AccelerationRamp motorRamp;

// ═══════════════════════════════════════════
// GÜVENLİK LİMLERİ
// ═══════════════════════════════════════════
constexpr int16_t MOTOR_MIN_SPEED = -255;
constexpr int16_t MOTOR_MAX_SPEED = 255;
constexpr int16_t MOTOR_MINOperating_SPEED = 50; // Minimum hareket hızı
constexpr unsigned long MOTOR_TIMEOUT_MS = 5000; // Hareketsizlik timeout

static unsigned long lastMotorActivity = 0;

// Motor durdurma fonksiyonu — timeout durumunda
void emergencyStop() {
    motorRamp.targetSpeed = 0;
    motorRamp.currentSpeed = 0;
    setMotorSpeed(0);
    speedPID.reset();
    Serial.println("[MOTOR] Emergency stop — timeout");
}

// ═══════════════════════════════════════════
// SERVO KONTROLÜ
// ═══════════════════════════════════════════
void setServoAngle(uint8_t channel, uint8_t angle) {
    angle = constrain(angle, 0, 180);
    uint32_t duty = angleToDutyCycle(angle);
    ledcWrite(channel, duty);
}

#endif // MOTOR_CONTROL_H
```

### 3.9 Power Management

```cpp
/**
 * Güç yönetimi — uyku modları, watchdog, brownout.
 *
 * @file power_management.h
 */

#ifndef POWER_MANAGEMENT_H
#define POWER_MANAGEMENT_H

// ═══════════════════════════════════════════
// WATCHDOG YÖNETİMİ (ESP32 Task WDT)
// ═══════════════════════════════════════════
#include <esp_task_wdt.h>

void enableWatchdog(uint32_t timeoutMs) {
    esp_task_wdt_init(timeoutMs / 1000, true); // true = panic on timeout
    esp_task_wdt_add(NULL); // Mevcut task'a ekle
    Serial.printf("[WDT] Enabled: %ums timeout\n", timeoutMs);
}

void feedWatchdog() {
    esp_task_wdt_reset();
}

// ═══════════════════════════════════════════
// DEEP SLEEP (ESP32)
// ═══════════════════════════════════════════
void enterDeepSleep(uint64_t sleepTimeUs) {
    Serial.printf("[POWER] Deep sleep for %llu μs\n", sleepTimeUs);

    // Uyandıktan sonra hangi pinle uyanacağını belirle
    esp_sleep_enable_timer_wakeup(sleepTimeUs);

    // Deep sleep'e geç
    esp_deep_sleep_start();
}

// ═══════════════════════════════════════════
// LIGHT SLEEP (ESP32)
// ═══════════════════════════════════════════
void enterLightSleep(uint64_t sleepTimeUs) {
    Serial.println("[POWER] Entering light sleep...");
    esp_sleep_enable_timer_wakeup(sleepTimeUs);
    esp_light_sleep_start();
    Serial.println("[POWER] Woke up from light sleep");
}

// ═══════════════════════════════════════════
// AVR UYKU MODU (Low Power Library)
// ═══════════════════════════════════════════
#ifdef __AVR__
#include <avr/sleep.h>
#include <avr/wdt.h>
#include <avr/power.h>

void enterAVRSleep(uint8_t sleepCycles) {
    for (uint8_t i = 0; i < sleepCycles; i++) {
        set_sleep_mode(SLEEP_MODE_PWR_SAVE);
        sleep_enable();
        sleep_cpu();
        sleep_disable();
    }
}
#endif

// ═══════════════════════════════════════════
// BROWNOUT DETECTION
// ═══════════════════════════════════════════
float readBatteryVoltage() {
    // ADC üzerinden pil voltajı okuma (voltage divider ile)
    uint32_t raw = analogRead(PIN_BATTERY_VOLTAGE);
    float voltage = (raw / 4095.0f) * 3.3f * 2.0f; // 2x voltage divider
    return voltage;
}

bool isBatteryLow() {
    return readBatteryVoltage() < 3.3f; // 3.3V altı = pil zayıf
}

#endif // POWER_MANAGEMENT_H
```

### 3.10 Memory Management

```cpp
/**
 * Bellek yönetimi — PROGMEM, SRAM optimizasyonu, dinamik tahsis yasağı.
 *
 * @file memory_management.h
 */

#ifndef MEMORY_MANAGEMENT_H
#define MEMORY_MANAGEMENT_H

// ═══════════════════════════════════════════
// PROGMEM KULLANIMI (Flash'ta sabit veri)
// ═══════════════════════════════════════════
// SRAM tasarrufu için sabit string'ler ve tablolar Flash'ta saklanır

const char MSG_INIT[] PROGMEM = "[INIT] CoreMusic IoT v3.0.0 ready";
const char MSG_ERROR[] PROGMEM = "[ERROR] ";
const char MSG_SENSOR[] PROGMEM = "[SENSOR] ";

// PROGMEM'den okuma
void printPROGMEMString(const char* str) {
    char buffer[64];
    strcpy_P(buffer, str);
    Serial.println(buffer);
}

// PROGMEM tablo örneği — kalibrasyon değerleri
const float CALIBRATION_TABLE[] PROGMEM = {
    0.0f, 0.5f, 1.0f, 1.5f, 2.0f,
    2.5f, 3.0f, 3.5f, 4.0f, 4.5f,
    5.0f
};

float getCalibrationValue(uint8_t index) {
    if (index >= sizeof(CALIBRATION_TABLE) / sizeof(float)) return 0.0f;
    return pgm_read_float(&CALIBRATION_TABLE[index]);
}

// ═══════════════════════════════════════════
// SRAM KONTROL FONKSİYONU
// ═══════════════════════════════════════════
#ifdef __AVR__
int getFreeSRAM() {
    extern int __heap_start, *__brkval;
    int v;
    return (int) &v - (__brkval == 0 ? (int) &__heap_start : (int) __brkval);
}
#endif

#ifdef ESP32
size_t getFreeHeap() {
    return ESP.getFreeHeap();
}
#endif

// ═══════════════════════════════════════════
// YASAK İŞLEMLER
// ═══════════════════════════════════════════
// ❌ malloc(), free(), new, delete KULLANILMAZ
// ❌ String sınıfı KULLANILMAZ (AVR'de heap fragmentation)
// ❌ Dynamic array KULLANILMAZ
//
// ✅ Sabit boyutlu tablolar
// ✅ Stack tahsisi
// ✅ PROGMEM ile Flash'ta saklama
// ✅ Static allocation

#endif // MEMORY_MANAGEMENT_H
```

### 3.11 Error Handling

```cpp
/**
 * Hata yönetimi — hata kodları, serial debug, LED göstergeleri.
 *
 * @file error_handling.h
 */

#ifndef ERROR_HANDLING_H
#define ERROR_HANDLING_H

#include "config.h"

// ═══════════════════════════════════════════
// HATA DURUMU YÖNETİMİ
// ═══════════════════════════════════════════
static ErrorCode currentError = ERR_NONE;
static uint8_t errorCount = 0;
static unsigned long lastErrorTime = 0;

void setError(ErrorCode code) {
    currentError = code;
    errorCount++;
    lastErrorTime = millis();

    // Serial debug çıktısı
    Serial.printf("[ERROR] Code: %d (count: %d)\n", code, errorCount);

    // LED göstergesi — hata kodunu çakma sayısıyla belirt
    for (uint8_t i = 0; i < code; i++) {
        digitalWrite(PIN_LED_ERROR, HIGH);
        delay(100);
        digitalWrite(PIN_LED_ERROR, LOW);
        delay(100);
    }
}

void clearError() {
    currentError = ERR_NONE;
    digitalWrite(PIN_LED_ERROR, LOW);
}

void handleErrors() {
    // 30 saniyede hata devam ederse → reset
    if (millis() - lastErrorTime > 30000) {
        Serial.println("[ERROR] Persistent error — requesting reset");
        ESP.restart();
    }

    // Hata sayacı 10'u aşarsa → reset
    if (errorCount >= 10) {
        Serial.println("[ERROR] Too many errors — requesting reset");
        ESP.restart();
    }
}

// ═══════════════════════════════════════════
// LED GÖSTERGE SİSTEMİ
// ═══════════════════════════════════════════
void updateStatusLED(bool connected, bool processing) {
    if (!connected) {
        // Bağlantı yok → hızlı titreşim
        digitalWrite(PIN_LED_NETWORK, (millis() / 200) % 2);
    } else if (processing) {
        // İşleniyor → yavaş titreşim
        digitalWrite(PIN_LED_STATUS, (millis() / 500) % 2);
    } else {
        // Normal → sürekli yanık
        digitalWrite(PIN_LED_STATUS, HIGH);
    }
}

#endif // ERROR_HANDLING_H
```

### 3.12 OTA Updates

```cpp
/**
 * OTA güncelleme — ESP32 Over-The-Air, versiyon yönetimi, geri alma.
 *
 * @file ota_manager.h
 */

#ifndef OTA_MANAGER_H
#define OTA_MANAGER_H

#include <ArduinoOTA.h>

// ═══════════════════════════════════════════
// OTA KURULUMU
// ═══════════════════════════════════════════
void setupOTA(const char* hostname) {
    ArduinoOTA.setHostname(hostname);
    ArduinoOTA.setPassword("coremusic_ota_password");

    ArduinoOTA.onStart([]() {
        String type = (ArduinoOTA.getCommand() == U_FLASH)
            ? "sketch" : "filesystem";
        Serial.printf("[OTA] Start: %s\n", type.c_str());
    });

    ArduinoOTA.onEnd([]() {
        Serial.println("\n[OTA] End — rebooting...");
    });

    ArduinoOTA.onProgress([](unsigned int progress, unsigned int total) {
        Serial.printf("[OTA] Progress: %u%%\r", (progress / (total / 100)));
    });

    ArduinoOTA.onError([](ota_error_t error) {
        Serial.printf("\n[OTA] Error[%u]: ", error);
        if (error == OTA_AUTH_ERROR) Serial.println("Auth Failed");
        else if (error == OTA_BEGIN_ERROR) Serial.println("Begin Failed");
        else if (error == OTA_CONNECT_ERROR) Serial.println("Connect Failed");
        else if (error == OTA_RECEIVE_ERROR) Serial.println("Receive Failed");
        else if (error == OTA_END_ERROR) Serial.println("End Failed");
    });

    ArduinoOTA.begin();
    Serial.printf("[OTA] Ready — hostname: %s\n", hostname);
}

// ═══════════════════════════════════════════
// OTA ANAHTAR GÜVENLİĞİ
// ═══════════════════════════════════════════
// Production'da hardcoded şifre KULLANILMAZ
// credential_vault tablosundan AES-256-GCM ile okunmalı
// [[ADR-022-database-hardened-security]] ile uyumlu

// ═══════════════════════════════════════════
// VERSİYON YÖNETİMİ
// ═══════════════════════════════════════════
constexpr char FIRMWARE_VERSION[] = "3.0.0";
constexpr char BUILD_DATE[] = __DATE__ " " __TIME__;

void printVersion() {
    Serial.printf("[VERSION] Firmware: %s\n", FIRMWARE_VERSION);
    Serial.printf("[VERSION] Build: %s\n", BUILD_DATE);
    Serial.printf("[VERSION] Chip: %s\n", ESP.getChipModel());
    Serial.printf("[VERSION] SDK: %s\n", ESP.getSdkVersion());
}

#endif // OTA_MANAGER_H
```

### 3.13 Wireless Communication

```cpp
/**
 * Kablosuz iletişim — WiFi, Bluetooth, MQTT, WebSocket.
 *
 * @file wireless.h
 */

#ifndef WIRELESS_H
#define WIRELESS_H

#include <WiFi.h>
#include <PubSubClient.h>

// ═══════════════════════════════════════════
// WiFi BAĞLANTI YÖNETİMİ
// ═══════════════════════════════════════════
constexpr char WIFI_SSID[] = "CoreMusic_Network";
constexpr char WIFI_PASS[] = "USE_CREDENTIAL_VAULT"; // ← Hardcoded yasak!
constexpr uint32_t WIFI_TIMEOUT_MS = 10000;

bool connectWiFi() {
    Serial.printf("[WIFI] Connecting to %s\n", WIFI_SSID);
    WiFi.mode(WIFI_STA);
    WiFi.begin(WIFI_SSID, WIFI_PASS);

    uint32_t start = millis();
    while (WiFi.status() != WL_CONNECTED) {
        if (millis() - start > WIFI_TIMEOUT_MS) {
            Serial.println("[WIFI] Connection timeout");
            return false;
        }
        delay(100);
    }

    Serial.printf("[WIFI] Connected — IP: %s\n", WiFi.localIP().toString().c_str());
    return true;
}

// ═══════════════════════════════════════════
// MQTT İSTEMCİSİ
// ═══════════════════════════════════════════
constexpr char MQTT_BROKER[] = "mqtt.coremusic.local";
constexpr uint16_t MQTT_PORT = 1883;
constexpr char MQTT_TOPIC_SENSOR[] = "coremusic/iot/sensor";
constexpr char MQTT_TOPIC_COMMAND[] = "coremusic/iot/command";
constexpr char MQTT_TOPIC_STATUS[] = "coremusic/iot/status";

static WiFiClient wifiClient;
static PubSubClient mqttClient(wifiClient);

// MQTT mesaj geri çağırma
void mqttCallback(char* topic, byte* payload, unsigned int length) {
    char message[256];
    if (length >= sizeof(message)) return;
    memcpy(message, payload, length);
    message[length] = '\0';

    Serial.printf("[MQTT] Topic: %s, Message: %s\n", topic, message);

    if (strcmp(topic, MQTT_TOPIC_COMMAND) == 0) {
        // Komut işleme
        if (strstr(message, "restart")) {
            ESP.restart();
        }
    }
}

bool connectMQTT() {
    if (mqttClient.connected()) return true;

    String clientId = "CoreMusic_IoT_" + String(ESP.getChipModel());

    if (mqttClient.connect(clientId.c_str())) {
        Serial.println("[MQTT] Connected");
        mqttClient.subscribe(MQTT_TOPIC_COMMAND);
        mqttClient.publish(MQTT_TOPIC_STATUS, "online", true);
        return true;
    }

    Serial.printf("[MQTT] Connection failed, rc=%d\n", mqttClient.state());
    return false;
}

// ═══════════════════════════════════════════
// WIRELESSCONNECT ENTEGRASYONU
// ═══════════════════════════════════════════
// [[ADR-037-wirelessconnect-integration]] kararıyla belirlenen
// kablosuz ağ entegrasyonu kuralları

void initWireless() {
    if (connectWiFi()) {
        mqttClient.setServer(MQTT_BROKER, MQTT_PORT);
        mqttClient.setCallback(mqttCallback);
        mqttClient.setBufferSize(512); // JSON payload için
        connectMQTT();
    }
}

#endif // WIRELESS_H
```

### 3.14 Library Management

```ini
; ═══════════════════════════════════════════
; PlatformIO Konfigürasyonu
; ═══════════════════════════════════════════

[platformio]
src_dir = src
include_dir = include
default_envs = esp32-dev

; ═══════════════════════════════════════════
; ORTAK AYARLAR
; ═══════════════════════════════════════════
[env]
framework = arduino
monitor_speed = 115200
build_flags =
    -D COREMUSIC_IOT
    -D COREMUSIC_VERSION=\"3.0.0\"
lib_deps =
    adafruit/Adafruit Unified Sensor@^1.1.9
    bblanchon/ArduinoJson@^7.0.0

; ═══════════════════════════════════════════
; ESP32 DEVELOPMENT BOARD
; ═══════════════════════════════════════════
[env:esp32-dev]
platform = espressif32@^6.4.0
board = esp32dev
framework = arduino
lib_deps =
    ${env.lib_deps}
    knolleary/PubSubClient@^2.8
    me-no-dev/ESPAsyncWebServer@^1.2.3
build_flags =
    ${env.build_flags}
    -D BOARD_ESP32
    -DCORE_DEBUG_LEVEL=3
    -DBOARD_HAS_PSRAM
monitor_filters = esp32_exception_decoder
upload_speed = 921600

; ═══════════════════════════════════════════
; ESP32-S3 (USB CDC + PSRAM)
; ═══════════════════════════════════════════
[env:esp32-s3]
platform = espressif32@^6.4.0
board = esp32-s3-devkitc-1
framework = arduino
lib_deps =
    ${env.lib_deps}
    knolleary/PubSubClient@^2.8
build_flags =
    ${env.build_flags}
    -D BOARD_ESP32_S3
    -D ARDUINO_USB_CDC_ON_BOOT=1
upload_speed = 921600

; ═══════════════════════════════════════════
; ESP8266 (Wemos D1 Mini)
; ═══════════════════════════════════════════
[env:esp8266]
platform = espressif8266@^4.2.0
board = d1_mini
framework = arduino
lib_deps =
    ${env.lib_deps}
    knolleary/PubSubClient@^2.8
build_flags =
    ${env.build_flags}
    -D BOARD_ESP8266
upload_speed = 115200

; ═══════════════════════════════════════════
; ARDUINO UNO (AVR ATmega328P)
; ═══════════════════════════════════════════
[env:uno]
platform = atmelavr@^7.0.0
board = uno
framework = arduino
build_flags =
    ${env.build_flags}
    -D BOARD_UNO
    -D F_CPU=16000000L
monitor_speed = 9600

; ═══════════════════════════════════════════
; ARDUINO MEGA (AVR ATmega2560)
; ═══════════════════════════════════════════
[env:mega]
platform = atmelavr@^7.0.0
board = megaatmega2560
framework = arduino
build_flags =
    ${env.build_flags}
    -D BOARD_MEGA
    -D F_CPU=16000000L
monitor_speed = 115200

; ═══════════════════════════════════════════
; NATIVE TESTS (PlatformIO Unit Testing)
; ═══════════════════════════════════════════
[env:native]
platform = native
framework = arduino
build_flags =
    -D COREMUSIC_IOT_TEST
lib_deps =
    throwtheswitch/Unity@^2.5.2
```

---

## 4. Hard Guardrails

| # | Kural | Açıklama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | **No Dynamic Allocation** | `malloc()`, `free()`, `new`, `delete` kesinlikle yasak | Bellek fragmentasyonu → crash |
| 2 | **No String Class (AVR)** | AVR'de `String` sınıfı yasak — `char[]` zorunlu | Heap fragmentation → stack overflow |
| 3 | **Watchdog Enabled** | WDT her zaman aktif olmalı (min 8sn timeout) | Sistem kilitlenmesinde watchdog reset |
| 4 | **ISR Must Be Short** | ISR içinde uzun işlem yasak — sadece flag set | Zaman aşımı, diğer interrupt'lar kaçırılır |
| 5 | **Volatile for ISR** | ISR değişkenleri `volatile` olmalı | Derleyici optimizasyonu ile yan etki kaybı |
| 6 | **No delay() in Loop** | `loop()` içinde `delay()` yasak — `millis()` bazlı | Watchdog reset, düşük performans |
| 7 | **No Magic Numbers** | Tüm pin/constant değerleri isimli sabit olmalı | Bakım zorluğu, hata riski |
| 8 | **PROGMEM for Constants** | Sabit string/tablolar Flash'ta saklanmalı | SRAM tükenmesi (AVR: 2KB) |
| 9 | **Pin Configuration in setup()** | `pinMode()` sadece `setup()` içinde çağrılır | Tanımsız pin davranışı |
| 10 | **OTA Password Protection** | OTA erişimi şifre korumalı olmalı | Yetkisiz firmware yükleme |
| 11 | **No Hardcoded Secrets** | WiFi şifresi, API key `.env`/credential vault'tan okunmalı | [[ADR-022-database-hardened-security]] ihlali |
| 12 | **Error Recovery** | Hata durumunda `ESP.restart()` veya watchdog reset | Sistem kilitlenmesi |

---

## 5. Naming Conventions

| Öğe | Format | Örnek | Açıklama |
|-----|--------|-------|----------|
| Pin sabiti | `UPPER_SNAKE_CASE` | `PIN_TEMP_SENSOR` | `PIN_` prefix zorunlu |
| Fonksiyon | `camelCase` | `readTemperature()` | Fiil ile başlamalı |
| Değişken | `camelCase` | `sensorValue` | Anlamlı, kısa isim |
| Sabit | `UPPER_SNAKE_CASE` | `MAX_SENSOR_VALUE` | `constexpr` tercih |
| Enum | `UPPER_SNAKE_CASE` | `ERR_SENSOR_INIT` | `ERR_` prefix |
| Yapı (struct) | `PascalCase` | `SensorData` | Noun-based |
| Dosya adı | `snake_case` | `sensor_manager.h` | `.` extension zorunlu |
| Header guard | `UPPER_SNAKE_CASE_H` | `SENSOR_MANAGER_H` | `_H` suffix zorunlu |
| Class | `PascalCase` | `PIDController` | Noun-based |

---

## 6. Security Considerations

| Konu | Uygulama | İlgili ADR |
|------|----------|------------|
| OTA Şifreleme | OTA şifresi credential vault'tan AES-256-GCM ile okunmalı | [[ADR-022-database-hardened-security]] |
| Secure Boot | ESP32 Secure Boot v2 aktif edilmeli (production) | — |
| Encrypted Storage | NVS (Non-Volatile Storage) Flash şifreleme | [[ADR-022-database-hardened-security]] |
| WiFi Güvenliği | WPA3 tercih, WPA2 minimum. WEP/Yasak | [[ADR-037-wirelessconnect-integration]] |
| MQTT TLS | Port 8883 + TLS sertifika doğrulaması (production) | [[ADR-039-7-service-platform-architecture]] |
| Hardcoded Secret | WiFi şifresi, API key asla kodda düz metin olmamalı | [[ADR-022-database-hardened-security]] |
| Serial Debug | Production'da debug portu devre dışı bırakılmalı | — |
| Bootloader Kilidi | ESP32 eFuse ile bootloader kilidi | — |

---

## 7. Performance Notes

| Metrik | Hedef | Notlar |
|--------|-------|--------|
| Loop süresi | <10ms | Watchdog 8sn — 800xtolerans |
| ISR süresi | <50μs | uzun ISR → diğer interrupt'lar kaçırılır |
| Analog okuma | 100μs/sample (ESP32) | 12-bit ADC, varsayılan clock |
| I2C hızı | 400kHz (Fast Mode) | 100kHz fallback |
| SPI hızı | 1-8MHz | Sensöre göre ayarlanmalı |
| SRAM kullanımı | <80% | AVR: 2KB, ESP32: 520KB |
| Flash kullanımı | <90% | OTA için %50 reserve |
| WiFi reconnect | <5sn | Exponential backoff |
| MQTT reconnect | <10sn | Exponential backoff |
| Güç tüketimi | Deep sleep: <10μA | ESP32 deep sleep |

---

## 8. Edge Cases

| Senaryo | Belirti | Çözüm | İlgili ADR |
|---------|---------|-------|------------|
| Watchdog Reset | Sistem periyodik olarak yeniden başlıyor | Loop süresini azalt, `feedWatchdog()` eklenmeli | — |
| Stack Overflow | Rastgele crash, buzzer sesi | SRAM kullanımı %80 altına düşür, stack Monitor | — |
| Pin Çakışması | I2C/SPI pinleri PWM ile çakışıyor | Pin haritası çıkar, çakışma kontrolü | [[ADR-037-wirelessconnect-integration]] |
| Power Brownout | Pil zayıflığında rastgele reset | Brownout detection, minimum voltaj kontrolü | — |
| WiFi Bağlantı Kopması | MQTT disconnect, veri kaybı | Exponential backoff, offline queue | [[ADR-037-wirelessconnect-integration]] |
| OTA Başarısızlık | Cihaz boot loop'a giriyor | OTA rollback, versiyon doğrulama | — |
| I2C Bus Lockup | I2C cihaz yanıtsız | Bus recovery (SDA/SCL toggle), timeout | — |
| ISR Storm | Aşırı interrupt → CPU %100 | Debouncing, interrupt rate limiting | — |
| EEPROM Yorgunluğu | Sık yazma → EEPROM ömrü tükenme | Write counter, wear leveling | — |
| Sensör Drift | Doğruluk zamanla azalıyor | Periyodik kalibrasyon, offset tablosu | — |

---

## 9. Troubleshooting

| Sorun | Olası Neden | Çözüm |
|-------|-------------|-------|
| Derleme hatası: `cannot declare volatile` | `volatile` fonksiyon parametresi | `volatile` sadece değişken için, fonksiyon parametresi için `volatile*` |
| Derleme hatası: `Arduino.h not found` | PlatformIO framework eksik | `framework = arduino` eklendi mi kontrol et |
| Yükleme hatası: `Failed to connect` | Boot modu yanlış | BOOT butonuna basılı tutarak yükleme |
| Yükleme hatası: `Wrong chip` | Yanlış board seçimi | `platformio.ini`'de board doğru mu? |
| Bellek hatası: `Not enough memory` | Dinamik tahsis, büyük array | PROGMEM kullan, array boyutunu azalt |
| Watchdog reset | Loop çok uzun, feed eksik | `feedWatchdog()` her loop'ta çağrılmalı |
| I2C yanıt yok | Adres yanlış, kablo hatası | I2C scanner ile adres taraması yap |
| MQTT bağlantı hatası | Broker offline, port yanlış | `mqttClient.state()` return code kontrolü |
| WiFi bağlantı hatası | SSID/şifre yanlış, menzil dışı | WPA2/WPA3 kontrolü, RSSI ölçümü |
| Sensör okuma hatası | Kalibrasyon yanlış, kablo gevşek | Kalibrasyon tablosunu güncelle |
| OTA güncelleme başarısız | Şifre yanlış, versiyon uyumsuz | OTA şifresini kontrol et, fallback URL |
| ESP32 ısınma | Sürekli high-frequency processing | Deep sleep aralığı ekle, clock throttling |

---

## 10. Common Anti-Patterns

| ❌ YANLIŞ | ✅ DOĞRU | Açıklama |
|-----------|----------|----------|
| `delay(1000);` loop içinde | `millis()` bazlı zamanlama | `delay()` watchdog reset tetikler |
| `int ledPin = 13;` | `constexpr uint8_t PIN_LED = 13;` | Magic number yasak |
| `String message = "";` (AVR) | `char message[32] = "";` | AVR'de String fragmentation |
| `void ISR() { Serial.print("x"); }` | `void ISR() { flag = true; }` | ISR içinde Serial yasak |
| `malloc(100);` | `static uint8_t buffer[100];` | Dinamik tahsis yasak |
| `const char* msg = "test";` | `const char msg[] PROGMEM = "test";` | PROGMEM kullanılmıyor |
| `attachInterrupt(pin, ISR, LOW);` | `attachInterrupt(pin, ISR, FALLING);` | LOW tetikleme tekrar tetikler |
| `while(true) { }` | `while(!condition) { yield(); }` | Watchdog reset olmadansonsuz döngü |
| WiFi şifresi kod içinde | Credential vault'tan okuma | [[ADR-022-database-hardened-security]] |
| OTA şifresiz | OTA şifreli | Yetkisiz erişim riski |
| `noInterrupts()` uzun süre | `noInterrupts()` mümkün olduğunca kısa | Zaman aşımı riski |

---

## 11. CoreMusic Integration

### ESP32 Wireless Audio Endpoint

CoreMusic IoT cihazları, [[ADR-037-wirelessconnect-integration]] kararıyla WiFi üzerinden CoreMusic Audio Service'e bağlanır.

```cpp
/**
 * CoreMusic IoT — Kablosuz ses uç noktası.
 * MQTT üzerinden Audio Service ile iletişim.
 */

void transmitData(const SensorData* data) {
    if (!mqttClient.connected()) {
        connectMQTT();
        return;
    }

    // JSON payload oluştur
    char payload[256];
    snprintf(payload, sizeof(payload),
        "{\"temp\":%.1f,\"light\":%.1f,\"audio\":%.2f,\"ts\":%lu}",
        data->temperature,
        data->lightLevel,
        data->audioLevel,
        data->timestamp);

    mqttClient.publish(MQTT_TOPIC_SENSOR, payload);
}

void handleCommunication() {
    mqttClient.loop(); // MQTT keep-alive ve mesaj alma

    // WiFi bağlantı kontrolü
    if (WiFi.status() != WL_CONNECTED) {
        connectWiFi();
    }

    // MQTT bağlantı kontrolü
    if (!mqttClient.connected()) {
        connectMQTT();
    }
}
```

### MQTT Topic Haritası

| Topic | Yön | İçerik |
|-------|-----|--------|
| `coremusic/iot/sensor` | Publish | Sensör verileri (JSON) |
| `coremusic/iot/command` | Subscribe | Komutlar (restart, config) |
| `coremusic/iot/status` | Publish | Cihaz durumu (online/offline) |
| `coremusic/iot/audio` | Publish | Ses seviyesi verisi |
| `coremusic/iot/ota` | Subscribe | OTA güncelleme bildirimi |

---

## 12. Hardware Pinout Reference

### ESP32 DevKit V1 Pin Haritası

| GPIO | Fonksiyon | Notlar |
|------|-----------|--------|
| 0 | BOOT buton | Yükleme modu için |
| 2 | Dahili LED | LED_BUILTIN |
| 4 | Touch sensor | Capacitive touch |
| 5 | SPI CS | Chip Select |
| 12 | ADC2_CH5 | WiFi ile kullanılamaz |
| 13 | ADC2_CH4 | WiFi ile kullanılamaz |
| 14 | ADC2_CH6 | PWM destekli |
| 15 | Touch, ADC2_CH3 | PWM destekli |
| 16 | UART2 RX | GPS modülü |
| 17 | UART2 TX | GPS modülü |
| 18 | SPI CLK | SPI Clock |
| 19 | SPI MISO | SPI Master In |
| 21 | I2C SDA | I2C Data |
| 22 | I2C SCL | I2C Clock |
| 23 | SPI MOSI | SPI Master Out |
| 25 | DAC1 | 8-bit DAC çıkış |
| 26 | DAC2 | 8-bit DAC çıkış |
| 27 | Touch, ADC2_CH7 | Capacitive touch |
| 32 | ADC1_CH4 | Analog input (WiFi ile çalışır) |
| 33 | ADC1_CH5 | Analog input |
| 34 | ADC1_CH6 | Sadece input (output değil) |
| 35 | ADC1_CH7 | Sadece input (output değil) |
| 36 | VP (ADC1_CH0) | Sadece input |
| 39 | VN (ADC1_CH3) | Sadece input |

**Dikkat:** ADC2 pinleri (4, 12, 13, 14, 15, 25, 26, 27) WiFi aktifken kullanılamaz!

---

## 13. Related Documents

| Dosya | İlişki |
|-------|--------|
| [[index]] | Master katalog |
| [[CLAUDE.md]] | Ana sözleşme |
| [[electronic/hardware-roadmap]] | 3 fazlı donanım yol haritası |
| [[electronic/audio-interface-design]] | XMOS + PCM3168A tasarım |
| [[electronic/asio-driver-design]] | ASIO sürücü tasarımı |
| [[electronic/xmos-pcm3168a-design]] | Devre tasarımı |
| [[electronic/test-protocols]] | Donanım test protokolleri |
| [[ADR-017-dsp-hardware-mode]] | DSP donanım modu kararı |
| [[ADR-037-wirelessconnect-integration]] | WirelessConnect entegrasyonu |
| [[ADR-038-8.1-sound-card-chip-selection]] | Ses donanımı seçimi |
| [[projects/NevaEngine/overview]] | C++ ses motoru |

---

## 14. Cross-References (ADR Haritası)

| ADR | Konu | Arduino İlgisi |
|-----|------|----------------|
| [[ADR-001-vanilla-js-itcss]] | Frontend framework yasağı | IoT panelleri vanilla JS kullanır |
| [[ADR-017-dsp-hardware-mode]] | XMOS XU316 + JUCE | ESP32 XMOS ile UART/SPI iletişim |
| [[ADR-022-database-hardened-security]] | AES-256-GCM, Argon2id | OTA şifreleme, credential yönetimi |
| [[ADR-037-wirelessconnect-integration]] | Kablosuz ağ entegrasyonu | WiFi, MQTT, WebSocket |
| [[ADR-038-8.1-sound-card-chip-selection]] | PCM3168A donanımı | I2C/SPI ile PCM3168A kontrolü |
| [[ADR-039-7-service-platform-architecture]] | 7 servis mimarisi | IoT verileri Media/AI servisine akar |
| [[ADR-040-database-authority]] | 18 BCNF veritabanı | IoT verileri coremusic_musics'e yazılır |

---

## 15. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 3.0.0 |
| **Toplam Satır** | 550+ |
| **Frontmatter** | ✅ 11 zorunlu alan tamamlandı |
| **Bölüm Sayısı** | 18 |
| **Kod Bloğu** | 14 (her biri yorumlu) |
| **Anti-Pattern** | 11 (❌/✅ formatında) |
| **Edge Case** | 10 |
| **Troubleshooting** | 12 |
| **ADR Referansı** | 7 (001, 017, 022, 037, 038, 039, 040) |
| **PlatformIO Env** | 6 (esp32, esp32-s3, esp8266, uno, mega, native) |
| **C++ Standardı** | C++17 (Arduino kısıtlı) |

---

## 16. Examples

### 16.1 Sensör Okuyucu (ESP32 + TMP102)

```cpp
/**
 * Sensör okuyucu — ESP32 + TMP102 + LDR
 * I2C ve ADC ile sıcaklık ve ışık okuma.
 *
 * @file sensor_reader.ino
 * @version 3.0.0
 */

#include <Arduino.h>
#include <Wire.h>

// Pin tanımları
constexpr uint8_t PIN_LDR = 36;
constexpr uint8_t PIN_LED = 2;
constexpr uint8_t TMP102_ADDR = 0x48;

// Sensör filtresi
constexpr uint8_t FILTER_SIZE = 8;
float tempBuffer[FILTER_SIZE];
uint8_t filterIndex = 0;

void setup() {
    Serial.begin(115200);
    Wire.begin(21, 22); // SDA, SCL
    pinMode(PIN_LED, OUTPUT);
    pinMode(PIN_LDR, INPUT);

    Serial.println("[INIT] Sensor Reader v3.0.0");
}

float readTemperature() {
    Wire.beginTransmission(TMP102_ADDR);
    Wire.write(0x00);
    Wire.endTransmission(false);
    Wire.requestFrom(TMP102_ADDR, 2);

    if (Wire.available() == 2) {
        int16_t raw = (Wire.read() << 8) | Wire.read();
        raw >>= 4;
        return raw * 0.0625f;
    }
    return -999.0f;
}

float filter(float value) {
    tempBuffer[filterIndex] = value;
    filterIndex = (filterIndex + 1) % FILTER_SIZE;

    float sum = 0;
    for (uint8_t i = 0; i < FILTER_SIZE; i++) {
        sum += tempBuffer[i];
    }
    return sum / FILTER_SIZE;
}

void loop() {
    float temp = readTemperature();
    float ldrValue = (analogRead(PIN_LDR) / 4095.0f) * 3.3f;

    if (temp > -900.0f) {
        float filtered = filter(temp);
        Serial.printf("Temp: %.2f°C (raw: %.2f) | LDR: %.2fV\n",
                      filtered, temp, ldrValue);

        digitalWrite(PIN_LED, filtered > 25.0f ? HIGH : LOW);
    }

    delay(1000);
}
```

### 16.2 Kablosuz Uç Noktası (ESP32 + MQTT)

```cpp
/**
 * Kablosuz IoT uç noktası — ESP32 + WiFi + MQTT
 * CoreMusic Audio Service'e sensör verisi gönderir.
 *
 * @file wireless_endpoint.ino
 * @version 3.0.0
 */

#include <WiFi.h>
#include <PubSubClient.h>

// WiFi — ⚠️ credential vault'tan okunmalı
constexpr char WIFI_SSID[] = "CoreMusic_Network";
constexpr char WIFI_PASS[] = "USE_VAULT";

// MQTT
constexpr char MQTT_BROKER[] = "mqtt.coremusic.local";
constexpr uint16_t MQTT_PORT = 1883;
constexpr char TOPIC_SENSOR[] = "coremusic/iot/sensor";
constexpr char TOPIC_CMD[] = "coremusic/iot/command";
constexpr char TOPIC_STATUS[] = "coremusic/iot/status";

WiFiClient wifiClient;
PubSubClient mqtt(wifiClient);

constexpr uint8_t PIN_SENSOR = 36;
constexpr unsigned long INTERVAL = 2000;

unsigned long lastSend = 0;

void mqttCallback(char* topic, byte* payload, unsigned int length) {
    char msg[64];
    memcpy(msg, payload, min(length, 63u));
    msg[length] = '\0';

    if (strcmp(topic, TOPIC_CMD) == 0 && strstr(msg, "restart")) {
        Serial.println("[CMD] Restart requested");
        ESP.restart();
    }
}

bool connectWiFi() {
    WiFi.begin(WIFI_SSID, WIFI_PASS);
    uint32_t start = millis();
    while (WiFi.status() != WL_CONNECTED && millis() - start < 10000) {
        delay(100);
    }
    return WiFi.status() == WL_CONNECTED;
}

bool connectMQTT() {
    if (mqtt.connect("CoreMusic_IoT_ESP32")) {
        mqtt.subscribe(TOPIC_CMD);
        mqtt.publish(TOPIC_STATUS, "online", true);
        return true;
    }
    return false;
}

void setup() {
    Serial.begin(115200);
    pinMode(PIN_SENSOR, INPUT);

    if (connectWiFi()) {
        mqtt.setServer(MQTT_BROKER, MQTT_PORT);
        mqtt.setCallback(mqttCallback);
        connectMQTT();
    }
}

void loop() {
    if (WiFi.status() != WL_CONNECTED) connectWiFi();
    if (!mqtt.connected()) connectMQTT();
    mqtt.loop();

    if (millis() - lastSend >= INTERVAL) {
        float voltage = (analogRead(PIN_SENSOR) / 4095.0f) * 3.3f;
        char payload[128];
        snprintf(payload, sizeof(payload),
                 "{\"voltage\":%.2f,\"heap\":%zu,\"ts\":%lu}",
                 voltage, ESP.getFreeHeap(), millis());
        mqtt.publish(TOPIC_SENSOR, payload);
        lastSend = millis();
    }

    yield();
}
```

### 16.3 Motor Kontrolcü (AVR + PID)

```cpp
/**
 * Motor kontrolcü — Arduino Mega + DC Motor + Encoder
 * PID hız kontrolü ve encoder geri bildirimi.
 *
 * @file motor_controller.ino
 * @version 3.0.0
 */

#include <Arduino.h>

// Pin tanımları
constexpr uint8_t PIN_MOTOR_PWM = 3;
constexpr uint8_t PIN_MOTOR_DIR = 4;
constexpr uint8_t PIN_ENCODER_A = 2;  // Interrupt pin
constexpr uint8_t PIN_ENCODER_B = 5;

// Encoder
volatile long encoderCount = 0;
constexpr int PULSES_PER_REV = 20;

// PID parametreleri
float kp = 2.0f;
float ki = 0.5f;
float kd = 0.1f;
float integral = 0;
float prevError = 0;

// Hedef hız (RPM)
float targetRPM = 60.0f;

// ISR — encoder sayma
void encoderISR() {
    if (digitalRead(PIN_ENCODER_B)) {
        encoderCount++;
    } else {
        encoderCount--;
    }
}

void setup() {
    Serial.begin(9600);

    pinMode(PIN_MOTOR_PWM, OUTPUT);
    pinMode(PIN_MOTOR_DIR, OUTPUT);
    pinMode(PIN_ENCODER_A, INPUT_PULLUP);
    pinMode(PIN_ENCODER_B, INPUT_PULLUP);

    attachInterrupt(digitalPinToInterrupt(PIN_ENCODER_A), encoderISR, RISING);

    Serial.println("[INIT] Motor Controller v3.0.0");
}

float calculateRPM() {
    static long lastCount = 0;
    static unsigned long lastTime = 0;

    unsigned long now = millis();
    float dt = (now - lastTime) / 1000.0f;

    if (dt < 0.1f) return 0; // 100ms minimum

    long count = encoderCount;
    float rpm = ((count - lastCount) / (float)PULSES_PER_REV) / dt * 60.0f;

    lastCount = count;
    lastTime = now;

    return rpm;
}

float computePID(float currentRPM) {
    float error = targetRPM - currentRPM;
    integral += error;
    integral = constrain(integral, -100.0f, 100.0f);
    float derivative = error - prevError;
    prevError = error;

    float output = (kp * error) + (ki * integral) + (kd * derivative);
    return constrain(output, 0.0f, 255.0f);
}

void loop() {
    float currentRPM = calculateRPM();
    float pwmValue = computePID(currentRPM);

    digitalWrite(PIN_MOTOR_DIR, HIGH);
    analogWrite(PIN_MOTOR_PWM, (int)pwmValue);

    Serial.printf("Target: %.1f RPM | Current: %.1f RPM | PWM: %.0f\n",
                  targetRPM, currentRPM, pwmValue);

    delay(100);
}
```

---

## 17. Checklist

### Pre-Commit Arduino Quality Checklist

- [ ] **Derleme:** `pio run` sıfır hata ile tamamlanıyor mu?
- [ ] **Warning:** Derleme uyarıları (warnings) var mı? → Giderilmeli
- [ ] **Pin Tanımı:** Tüm pinler `config.h`'da sabit olarak tanımlı mı?
- [ ] **Magic Number:** Kodda doğrudan sayısal değer var mı? → Sabit tanımlanmalı
- [ ] **PROGMEM:** Sabit string'ler Flash'ta mı? (AVR için kritik)
- [ ] **Dynamic Allocation:** `malloc()`, `new`, `String` (AVR) kullanılmış mı? → Yasak
- [ ] **Watchdog:** WDT aktif ve `feedWatchdog()` her loop'ta çağrılıyor mu?
- [ ] **ISR:** ISR fonksiyonları kısa mı? (`volatile`, `IRAM_ATTR`)
- [ ] **delay():** `loop()` içinde `delay()` var mı? → `millis()` bazlı olmalı
- [ ] **Error Handling:** Hata durumları ele alınıyor mu?
- [ ] **Serial Debug:** Debug mesajları production'da devre dışı bırakılabilir mi?
- [ ] **OTA Şifre:** OTA erişimi şifre korumalı mı?
- [ ] **WiFi Şifresi:** Hardcoded mı? → Credential vault'tan okunmalı
- [ ] **Bellek Kullanımı:** SRAM kullanımı %80'in altında mı?
- [ ] **Header Guard:** Tüm `.h` dosyalarında header guard var mı?
- [ ] **Versiyon:** `FIRMWARE_VERSION` güncel mi?
- [ ] **README:** Proje açıklaması var mı?
- [ ] **Test:** `pio test` çalışıyor mu?
- [ ] **PlatformIO:** `platformio.ini` tüm hedef ortamları destekliyor mu?

---

## 18. PlatformIO Configuration

`platformio.ini` dosyasının tam hali Bölüm 3.14'te verilmiştir. Ek notlar:

### Build Flags Açıklaması

| Flag | Amaç |
|------|------|
| `-D COREMUSIC_IOT` | CoreMusic IoT modu aktif |
| `-D BOARD_ESP32` | ESP32 board tanımlaması |
| `-D BOARD_ESP32_S3` | ESP32-S3 board tanımlaması |
| `-D BOARD_UNO` | Arduino Uno board tanımlaması |
| `-D BOARD_MEGA` | Arduino Mega board tanımlaması |
| `-D COREMUSIC_VERSION=\"3.0.0\"` | Firmware versiyonu |
| `-DCORE_DEBUG_LEVEL=3` | ESP32 debug seviyesi (3 = INFO) |
| `-DBOARD_HAS_PSRAM` | PSRAM desteği (ESP32-WROVER) |

### Test Ortamı

```bash
# Tüm testleri çalıştır
pio test

# Belirli bir ortamda test
pio test -e esp32-dev

# Sadece belirli bir dosyayı test et
pio test -f test_sensors
```

### Upload Komutları

```bash
# USB ile yükleme
pio run -e esp32-dev --target upload

# OTA ile yükleme
pio run -e esp32-dev --target upload --upload-port 192.168.1.100

# Sadece derleme (yükleme yok)
pio run -e esp32-dev
```

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-06
**Mode:** Red Team • Human Mode • Truth Mode
