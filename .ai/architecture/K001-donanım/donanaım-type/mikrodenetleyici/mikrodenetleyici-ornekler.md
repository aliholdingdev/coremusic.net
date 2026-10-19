---
node_id: K001-donanım/donanaım-type/mikrodenetleyici/mikrodenetleyici-ornekler
node_type: module
status: draft
parent_id: K001-donanım/donanaım-type/mikrodenetleyici
children: []
depends_on:
  - "K001-donanım/donanaım-type/mikrodenetleyici/mikrodenetleyici-kapsam"
evidence:
  - ".ai/.templates/hardware/avr-template.md v1.0.0 — avr/io.h · avr/interrupt.h · avr/pgmspace.h · F_CPU"
  - ".ai/.templates/hardware/pic-template.md v1.0.0 — XC8 · config word · bankalı bellek · __delay_ms/_XTAL_FREQ"
  - ".ai/.templates/hardware/arduino-template.md v1.0.0 — PlatformIO zorunlu · setup()/loop()"
  - "https://www.nongnu.org/avr-libc/user-manual/index.html (✅ HTTP 200 · 2026-10-14)"
  - "https://docs.platformio.org/en/latest/projectconf/ (✅ HTTP 200 · 2026-10-14)"
  - "https://raw.githubusercontent.com/arduino/arduino-cli/master/README.md (✅ HTTP 200 · 2026-10-14)"
validation: "2026-10-14 · 3 şablon (S1) + 3 URL HEAD 200 · repo glob .c/.h/.ino = 0 → TÜM BLOKLAR [KALIP-PLANNED] · derlenmedi/yüklenmedi"
open_questions:
  - "Q-M1 — PIC serisi (blok §4 PIC16F877A **temsilî** — Q-M1 kapanınca güncellenir)"
  - "Q-M2 — ATmega328P teyidi bekliyor (blok §3 bu hedefe göre yazıldı)"
group: kod
---

# mikrodenetleyici-ornekler — Kademe-4 Modül (kod)

> ⚠️ **Dürüstlük kapısı:** Repo'da `.c` · `.h` · `.ino` · `platformio.ini` **0**
> (S1 glob, 2026-10-14). Bu dosyadaki **her blok `[KALIP-PLANNED]`'dır** — derlenmedi,
> yüklenmedi, ölçülmüdü. Katman şeması kardeş `mikrodenetleyici-kapsam.md` §2'dedir.
> Sürümler (avr-gcc 15.1.0 · avr-libc 2.2.1 · XC8) Guardrail #16 şablonlarından (S1).

## §0 Katman haritası (bu dosyadaki bloklar → katman)

| # | Blok | Katman | Platform |
|---|---|---|---|
| 1 | §1 `hal/gpio.h` | K2 HAL (platform bağımsız arayüz) | üçü de |
| 2 | §2 `bsp/avr_gpio.c` | K1 BSP | ATmega328P (avr-gcc) |
| 3 | §3 `main.c` blink + UART | K3 Uygulama (+ K1 çağrısı) | ATmega328P |
| 4 | §4 `pic_blink.c` | K3+K1 (XC8, tek dosya — bank/config zorunlu) | PIC16F (temsilî) |
| 5 | §5 `ino_blink.ino` + `platformio.ini` | K3 (Arduino Core K1'i üstlenir) | Arduino Uno |
| 6 | §6 `uart_telemetry.c` | K3 Uygulama | ATmega328P |

## §1 HAL arayüzü — `hal/gpio.h` [KALIP-PLANNED] · K2

```c
/* K2 HAL — platform BAĞIMSIZ. #ifdef YASAK (kapsam §2 kuralı). */
#ifndef HAL_GPIO_H
#define HAL_GPIO_H
#include <stdint.h>
typedef enum { HAL_PIN_A0 = 0, HAL_PIN_LED = 13 } hal_pin_t;   /* Arduino-uyumlu numaralandırma */
typedef enum { HAL_DIR_IN = 0, HAL_DIR_OUT = 1 } hal_dir_t;
void hal_gpio_init(hal_pin_t pin, hal_dir_t dir);
void hal_gpio_write(hal_pin_t pin, uint8_t level);
uint8_t hal_gpio_read(hal_pin_t pin);
void hal_delay_ms(uint16_t ms);
#endif
```

## §2 ATmega328P BSP — `bsp/avr_gpio.c` [KALIP-PLANNED] · K1

```c
/* K1 BSP (ATmega328P · avr-gcc) — register erişimi YALNIZ burada. */
#include "hal/gpio.h"
#include <avr/io.h>
#include <util/delay.h>                    /* avr-libc — F_CPU derleme bayrağıyla çalışır */
#define AVR_LED_PIN 5                      /* PB5 — Arduino Uno D13 karşılığı */

void hal_gpio_init(hal_pin_t pin, hal_dir_t dir) {
    if (pin == HAL_PIN_LED) {
        if (dir == HAL_DIR_OUT) DDRB |= (1u << AVR_LED_PIN);
        else                   DDRB &= ~(1u << AVR_LED_PIN);
    }
}
void hal_gpio_write(hal_pin_t pin, uint8_t level) {
    if (pin == HAL_PIN_LED) {
        if (level) PORTB |=  (1u << AVR_LED_PIN);
        else       PORTB &= ~(1u << AVR_LED_PIN);
    }
}
uint8_t hal_gpio_read(hal_pin_t pin) {
    return (pin == HAL_PIN_LED) ? (PINB >> AVR_LED_PIN) & 1u : 0u;
}
void hal_delay_ms(uint16_t ms) { while (ms--) _delay_ms(1); }   /* _delay_ms <~ 13 ms/bound — avr-libc kuralı */
```

> Derleme bayrağı: `-DF_CPU=16000000UL` (Uno 16 MHz — şablon §7.3 sürüm matrisiyle
> birlikte sabitlenir). Sürücü: `avr-gcc` → `avr-objcopy` → `.hex`.

## §3 ATmega328P uygulama — `main.c` blink + UART [KALIP-PLANNED] · K3

```c
/* K3 Uygulama — platform bağımsız; yalnız hal/gpio.h çağırır. */
#include "hal/gpio.h"
int main(void) {
    hal_gpio_init(HAL_PIN_LED, HAL_DIR_OUT);
    for (;;) { hal_gpio_write(HAL_PIN_LED, 1); hal_delay_ms(500);
               hal_gpio_write(HAL_PIN_LED, 0); hal_delay_ms(500); }
}
```

## §4 PIC (XC8) — `pic_blink.c` [KALIP-PLANNED] · K3+K1 · ⚠️ temsilî (Q-M1)

```c
/* XC8 · PIC16F — TEMSILÎ KALIP: config word + _XTAL_FREQ zorunlu (pic-template §kural). */
#include <xc.h>
#define _XTAL_FREQ 4000000UL               /* 4 MHz osilatör varsayımı — ⚠️ Q-M1 */
#pragma config FOSC = INTRC_IO, WDTE = OFF, CP = OFF, LVP = OFF   /* gerçek proje: LVP/koruma kararı */
void main(void) {
    TRISBbits.TRISB0 = 0;                  /* RB0 çıkış */
    while (1) { LATBbits.LATB0 = 1; __delay_ms(500);
                LATBbits.LATB0 = 0; __delay_ms(500); }
}
```

> Bankalı bellek/koruma bitleri şablon (`pic-template.md`) hükümleridir; gerçek hedef
> serisi Q-M1 kapanınca güncellenir.

## §5 Arduino — `blink.ino` + `platformio.ini` [KALIP-PLANNED] · K3

```cpp
/* Arduino çekirdeği K1'i üstlenir (pinMode/digitalWrite) — PlatformIO zorunlu (şablon §1.1). */
#define LED_PIN 13
void setup() { pinMode(LED_PIN, OUTPUT); }
void loop()  { digitalWrite(LED_PIN, HIGH); delay(500);
               digitalWrite(LED_PIN, LOW);  delay(500); }
```

```ini
; platformio.ini — çok-env (kardeş cross-plani §4 sürüm-sabitleme kuralı ile hizalı)
[env:uno]
platform  = atmelavr          ; docs.platformio.org atmelavr
board     = uno
framework = arduino
monitor_speed = 115200
```

## §6 UART telemetri — `uart_telemetry.c` (115200 8N1) [KALIP-PLANNED] · K3

```c
/* F4 fazı çıktısı — ATmega328P USART0; HAL_uart_* arayüzü kardeş dosya §1 kalıbıyla. */
#include <avr/io.h>
void uart_init(void) {
    UBRR0 = 16;                            /* 16 MHz / 16 / 115200 - 1 ≈ 7 (≈%2.1 hata) — hesaplanacak */
    UCSR0B = (1 << TXEN0);                 /* yalnız TX — F4 kapsamı */
    UCSR0C = (1 << UCSZ01) | (1 << UCSZ00);/* 8N1 */
}
void uart_send(char c) { while (!(UCSR0A & (1 << UDRE0))) { } UDR0 = c; }
```

> ⚠️ UBRR değeri **hesaplanacak** (Q: osilatör ±% tolerans); blok kalıp,
> `uart_send` blocking'tir — F4 kapısı ring-buffer'a taşınabilir (K005 §ring).

## §7 Proje planı bağlantısı

Fazlar F0–F7 kardeş `mikrodenetleyici-kapsam.md` §3'tedir; bu dosyadaki bloklar
**F1–F4** çıktısıdır. Dağıtım `.hex` adlandırması: kapsam §6.

## §8 Açık sorular

| # | Soru | Etiket |
|---|---|---|
| Q-M1 | PIC serisi (§4 temsilî)? | ⚠️ VERIFICATION REQUIRED |
| Q-M2 | ATmega328P hedef? | ⚠️ VERIFICATION REQUIRED |
| Q-M4 | HIL donanımı (yükleme doğrulaması için) | UNKNOWN |

## §9 Sürüm geçmişi

| # | Sürüm | Tarih | Değişiklik |
|---|---|---|---|
| 1 | v1.0.0 | 2026-10-14 | İlk yazım — 6 [KALIP-PLANNED] blok (HAL·BSP·app·PIC·Arduino·UART) + platformio.ini; 3 şablon (S1) + 3 URL HEAD 200; repo .c/.ino = 0 dürüstlük kapısı işaretli |

## §10 Bloklamayan blink — `blink_nb.cpp` [KALIP-PLANNED] · K3

```cpp
/* Şablon §4.3 kuralı: tüm zamanlamalar millis(); delay() üretim loop'unda YASAK. */
#include <Arduino.h>
constexpr uint8_t LED_PIN = 13;
constexpr unsigned long BLINK_INTERVAL = 500;
unsigned long previousMillis = 0;

void setup() { pinMode(LED_PIN, OUTPUT); }
void loop() {
    const unsigned long now = millis();
    if (now - previousMillis >= BLINK_INTERVAL) {   /* taşmaya güvenli karşılaştırma */
        previousMillis = now;
        digitalWrite(LED_PIN, !digitalRead(LED_PIN));
    }
}
```

> Yanlış kalıp (YASAK): `delay(500); digitalWrite(...)` — §11 #1 ihlali.

## §11 Buton debounce — `Button` sınıfı [KALIP-PLANNED] · K3

```cpp
/* 50 ms durum-makinesi debounce; INPUT_PULLUP: PRESSED = LOW, yükselen kenar döner. */
class Button {
public:
    explicit Button(uint8_t pin) : pin_(pin) { pinMode(pin_, INPUT_PULLUP); }
    bool isPressed() {
        const bool current = (digitalRead(pin_) == LOW);
        const unsigned long now = millis();
        if (current != lastState_) lastDebounceTime_ = now;
        if ((now - lastDebounceTime_) > DEBOUNCE_DELAY && current != debouncedState_) {
            debouncedState_ = current;
            if (debouncedState_ == PRESSED) return true;    /* yalnız kenarda döner */
        }
        lastState_ = current;
        return false;
    }
private:
    static constexpr unsigned long DEBOUNCE_DELAY = 50;     /* ms */
    static constexpr bool PRESSED = LOW;
    uint8_t pin_;
    bool lastState_ = HIGH, debouncedState_ = HIGH;
    unsigned long lastDebounceTime_ = 0;
};
```

## §12 Zamanlayıcı kesmesi — ISR bayrak kalıbı [KALIP-PLANNED] · K2/K3

```cpp
/* Şablon §5.2: hassas periyotlu görevler için ISR yalnız bayrak yazar. */
#include <TimerOne.h>
volatile bool timerFlag = false;

void timerISR() { timerFlag = true; }            /* hesap YOK — bütçe <50 µs */

void setup() {
    Timer1.initialize(1000);                     /* 1 ms periyot */
    Timer1.attachInterrupt(timerISR);
}
void loop() {
    if (timerFlag) { timerFlag = false; readEncoder(); }
    /* ... §11.2 aşama bütçesi ... */
}
```

> Kural: `volatile` zorunlu; ISR içinde `Serial.print`/`delay`/`malloc` **YASAK**.
> Timer çakışması → §18 T-M9.

## §13 Durum makinesi — `StateMachine` [KALIP-PLANNED] · K3

```cpp
/* Şablon §5.3: mod geçişleri onEnter/onExit ile merkezîleştirilir. */
enum class AppMode : uint8_t { SLEEP, IDLE, ACTIVE, CONFIG, ERROR };

class StateMachine {
public:
    void update(unsigned long now) {
        switch (mode_) {
            case AppMode::SLEEP:  if (wakeCondition)    transition(AppMode::IDLE);   break;
            case AppMode::IDLE:   if (buttonPressed)    transition(AppMode::ACTIVE); break;
            case AppMode::ACTIVE:
                if (timeoutReached(now)) transition(AppMode::SLEEP);
                if (errorDetected)       transition(AppMode::ERROR);
                break;
            case AppMode::ERROR:  if (recoveryCondition) transition(AppMode::IDLE);  break;
            case AppMode::CONFIG: break;
        }
    }
private:
    AppMode mode_ = AppMode::SLEEP;
    void transition(AppMode n) { onExit(mode_); mode_ = n; onEnter(mode_); }
    void onEnter(AppMode) { /* moda giriş hazırlığı */ }
    void onExit(AppMode)  { /* modadan çıkış temizliği */ }

    /* Girdi/joystick koşulları — KALIP: F2'de gerçek sinyallerle bağlanacak */
    bool wakeCondition      = false;
    bool buttonPressed      = false;
    bool errorDetected      = false;
    bool recoveryCondition  = false;
    bool timeoutReached(unsigned long) { return false; }
};
```

> ⚠️ **Dürüstlük:** blok derlenmedi — üye ifadeleri kalıp gösterimi; F1/F2'de
> gerçek üyelere çevrilecek `[KALIP-PLANNED]`.

## §14 EEPROM yapılandırma — `config_store` [KALIP-PLANNED] · K3

```cpp
/* Şablon §4.5: veritabanı yok — EEPROM / LittleFS / SD. */
#include <EEPROM.h>
struct DeviceConfig {
    uint16_t magic = 0xCAFE;        /* doğrulama işareti */
    uint8_t  brightness = 255;
    uint16_t intervalMs = 1000;
    int16_t  calibrationOffset = 0;
};
DeviceConfig config;

void saveConfig() { EEPROM.put(0, config); EEPROM.commit(); /* ESP32; AVR otomatik */ }
bool loadConfig() { EEPROM.get(0, config); return config.magic == 0xCAFE; }
```

| # | Kural |
|---|-------|
| 1 | `magic` uyuşmuyorsa fabrika değeri (varsayılan) kullanılır |
| 2 | `commit()` yalnız yapılandırma **değişince** (§13 #5 · T-M7) |
| 3 | ATmega328P EEPROM sınırı **1 KB** (§13 tablosu) |
| 4 | SQL YASAK; kalıcı veri CSV/JSON/ikili düz dosya |

## §15 PROGMEM arama tablosu [KALIP-PLANNED] · K3

```cpp
/* Şablon §4.6: 256 girdili 16-bit sinüs — SRAM'in %25'i değil, flash'ın %2'si. */
#include <avr/pgmspace.h>
const uint16_t sineTable[256] PROGMEM = { 0, 402, 804, 1205 /* ... kalıp */ };
uint16_t sineValue = pgm_read_word(&sineTable[index]);
```

| # | Kural |
|---|-------|
| 1 | 64 bayttan büyük arama tabloları/font/kalibrasyon eğrisi → `PROGMEM` |
| 2 | AVR (2 KB RAM) için varsayılan; ESP32 (520 KB) yalnız çok büyük tablolar |
| 3 | Çalışma zamanında değişen dize `PROGMEM`'e alınmaz |

## §16 Watchdog [KALIP-PLANNED] · K3

```cpp
/* Şablon §4.4 #8–#9: üretimde setup() içinde etkinleştir; wdt_reset() loop BAŞINDA. */
#include <avr/wdt.h>          /* AVR — ESP32: <esp_task_wdt.h> */

void setup() { wdt_enable(WDTO_8S); }      /* 4–8 s aralığı (§12 #8) */
void loop()  { wdt_reset();                /* başta — ortadaki askıyı korur */
               /* ... uygulama ... */ }
```

| # | Kural |
|---|-------|
| 1 | `wdt_reset()` sonunda DEĞİL başında |
| 2 | Watchdog kaynaklı sıfırlama açılışta `MCUSR`/`RCON` ile tespit+temizle (§19 #3) |
| 3 | Aşama bütçesi 25 ms (§11.2) → 8 s watchdog için cömert marj |

## §17 Güvenli seri okuma tamponu [KALIP-PLANNED] · K3

```cpp
/* Şablon §4.4 #5–#6: sınırsız Serial.readString() YASAK; 256 bayt + null. */
static constexpr uint8_t SERIAL_BUF_SIZE = 256;
static char buf[SERIAL_BUF_SIZE];
static uint8_t idx = 0;

while (Serial.available() && idx < SERIAL_BUF_SIZE - 1) {
    const char c = Serial.read();
    if (c == '\n') break;
    buf[idx++] = c;
}
buf[idx] = '\0';              /* zorunlu null sonlandırma */
idx = 0;
```

## §18 HAL I2C/SPI arayüzü — `hal/i2c.h` + `hal/spi.h` [KALIP-PLANNED] · K2

```c
/* K2 HAL — platform bağımsız (kapsam §2 kuralı: #ifdef YASAK). */
#ifndef HAL_I2C_H
#define HAL_I2C_H
#include <stdint.h>
typedef enum { HAL_OK = 0, HAL_ERR_BUS = 1, HAL_ERR_ADDR = 2, HAL_ERR_TIMEOUT = 3 } hal_status_t;
hal_status_t hal_i2c_init(uint32_t hz);                       /* 100k / 400k (§8) */
hal_status_t hal_i2c_write(uint8_t addr7, const uint8_t* d, uint16_t n);
hal_status_t hal_i2c_read (uint8_t addr7, uint8_t* d, uint16_t n);
#endif
```

```c
#ifndef HAL_SPI_H
#define HAL_SPI_H
#include <stdint.h>
typedef enum { HAL_SPI_1MHZ = 0, HAL_SPI_10MHZ = 1, HAL_SPI_40MHZ = 2 } hal_spi_hz_t;
void     hal_spi_init(hal_spi_hz_t hz);
void     hal_spi_select(uint8_t cs, uint8_t assert);          /* CS manuel tutulur */
uint8_t  hal_spi_transfer(uint8_t out);
#endif
```

> **F5 kapısı:** cihaz yanıtı okunamazsa §8.1 #2 (multiplexer) ve seviye kaydırıcı
> (3.3 V ↔ 5 V) denetlenir — §20 R-M3.

## §19 PIC bank/kesme kalıbı — `pic_isr.c` [KALIP-PLANNED] · K1 · ⚠️ temsilî (Q-M1)

```c
/* XC8 · PIC16F TEMSILÎ — bank erişimi ve config word şablon (pic-template) hükümleridir. */
#include <xc.h>
#define _XTAL_FREQ 4000000UL
#pragma config FOSC = INTRC_IO, WDTE = OFF, CP = OFF, LVP = OFF

volatile uint8_t tick = 0;
void __interrupt() isr(void) {
    if (PIE1bits.TMR2IF && PIR1bits.TMR2IF) {
        PIR1bits.TMR2IF = 0;      /* bayrak temizle — ISR minimal (§11.1 #4) */
        tick++;
    }
}
```

> ⚠️ **temsilî** — hedef seri Q-M1 kapanınca revize edilir (kapsam §6 · ornekler §4 notu).

## §20 PlatformIO çok-env yapılandırması [KALIP-PLANNED] · F0/F1

```ini
; platformio.ini — şablon §5.8 kalıbı (sürüm-sabitleme: cross-plani §4)
[platformio]
default_envs = uno
src_dir = src
test_dir = test
monitor_speed = 115200

[env]
build_flags =
    -std=gnu++17
    -Wall -Wextra -Werror
lib_deps =

[env:native]
platform = native
build_type = debug
test_build_project_src = true

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

| # | Kural | Kapı |
|---|-------|------|
| 1 | `lib_deps` her girdide sürüm belirtir (§14 #1) | K-M1 |
| 2 | `#HEAD` yasak (§14 #4) | K-M1 |
| 3 | `-Wall -Wextra -Werror` (§14 #7) | K-M2 |
| 4 | Elle `lib/` kopyası yasak (§14 #5) | kod incelemesi |

## §21 CI derleme betiği + workflow [KALIP-PLANNED] · Q-M5

```bash
#!/bin/bash
# ci/compile-check.sh — GitHub Actions her commit'te çalıştırır (şablon §5.5)
echo "Running PlatformIO compile check..."
pio check --fail-on-defect=medium --fail-on-violation=high || exit 1
pio run -e native -e uno || exit 1
echo "Compile check passed"
```

```yaml
# .github/workflows/arduino-ci.yml — KALIP (repo'da bu dosya YOK)
name: Arduino CI
on: [push, pull_request]
jobs:
  compile:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-python@v5
        with: { python-version: '3.12' }
      - name: Install PlatformIO
        run: pip install platformio
      - name: Compile all targets
        run: pio run -e native -e uno
      - name: Static analysis
        run: pio check --fail-on-defect=medium
```

> ⚠️ Repo `.github/workflows/` envanteri bu oturumda **ölçülmedi** → dosya kalıp,
> "mevcut" denmez (Q-M5 · §17 bağımlılık #6 sınırı).

## §22 Birim testi — `test/test_main.cpp` [KALIP-PLANNED] · K-M5

```cpp
/* Şablon §6.1 — host'ta çalışır (native env), donanım gerekmez. */
#include <unity.h>

void test_non_blocking_timing(void) {
    unsigned long prev = 0;
    constexpr unsigned long INTERVAL = 100;
    const unsigned long start = millis();
    for (int i = 0; i < 10; i++) {
        const unsigned long now = millis();
        if (now - prev >= INTERVAL) prev = now;
    }
    TEST_ASSERT_TRUE(millis() - start >= 1000);
}
void setup() { UNITY_BEGIN(); RUN_TEST(test_non_blocking_timing); UNITY_END(); }
void loop() {}
```

```bash
pio test -e native    # host — hızlı, donanım yok
pio test -e uno       # cihaz — yavaş, donanım gerekli (⚠️ Q-M4)
```

## §23 SRAM bütçe ölçümü + optimizasyon sırası [KALIP-PLANNED] · K-M5

```cpp
/* AVR serbest SRAM ölçümü (şablon §6.4) — hedef: toplamın %80'i altında. */
extern int __heap_start, *__brkval;
int freeSRAM() {
    int v;
    return (int)&v - (__brkval == 0 ? (int)&__heap_start : (int)__brkval);
}
```

| # | Optimizasyon önceliği (etkiye göre) | Kazanç |
|---|--------------------------------------|--------|
| 1 | `String` → `char[]` + `sprintf` | RAM kaçağı kapanır (T-M8) |
| 2 | Dize literalleri `F()` ile flash'a | 2 bayt/char RAM |
| 3 | Arama tabloları `PROGMEM` | SRAM → flash (§15) |
| 4 | Tampon boyutlarını küçült | doğrudan SRAM |
| 5 | `int`/`float` → `uint8_t`/`int16_t` | RAM + CPU |

| # | Bütçe hedefi | Ölçüm |
|---|--------------|-------|
| 1 | SRAM < %80 | `pio check --section=.bss` |
| 2 | Flash < %90 | `pio size --json` |
| 3 | Döngü < 10 ms | `micros()` profilleme (§11.2) |
| 4 | ISR < 50 µs | §11.1 #4 |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
