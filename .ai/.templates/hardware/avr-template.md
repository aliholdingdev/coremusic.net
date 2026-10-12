---
title: "CoreMusic — AVR Technology Governance Template"
type: template
category: template
version: 1.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-29
---

# CoreMusic — AVR Technology Governance Template

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu şablon, Atmel AVR (ATmega, ATtiny, AT90USB serisi) mikrodenetleyicileri üzerinde Arduino framework'süz (bare-metal) firmware geliştirme kurallarını tanımlar: tek katmanlı kesme驱动 mimari, EEPROM kalıcılık yönetimi, kesme öncelikleri, test stratejisi, performans bütçeleri ve MISRA/IEC uyumluluğu. Eski spesifikasyon dosyası (`avr-template.md`, 2026-06-24, 1119 satır) yeniden yapılandırılmış; içerik silinmeden Guardrail #16 dış iskeletine taşınmıştır.

**Guardrail #16:** CoreMusic vault içinde bare-metal AVR firmware dokümanı üretilirken bu şablon seçilmek ZORUNLUDUR.

| Platform | Değer |
|----------|-------|
| **Platform** | Atmel AVR (ATmega, ATtiny, AT90USB) |
| **Araç zinciri** | avr-gcc 15.1.0 (+avr-libc 2.2.1 + binutils 2.44) — bkz. §7.3 |
| **Standart** | C11 (GNU11) — bare-metal, işletim sistemi yok |
| **Mimari** | 8-bit RISC, 16× register dosyası (r0–r31), Harvard |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Bare-metal AVR firmware (doğrudan register erişimi, ISR tasarımı) | Arduino framework'ü kullanan projeler → `[[hardware/arduino-template]]` |
| avr-gcc/avr-libc/avrdude araç zinciri, Makefile/CI derleme | PIC (Microchip) platformu → `[[hardware/pic-template]]` |
| EEPROM aşınma dengeleme, CRC-16 doğrulama, fuse bit yönetimi | Genel donanım tasarım dokümanı → `[[hardware/hardware-template]]` |
| Simülasyon (simulavr), birim test (Ceedling/Unity), HITL ölçüm | C11 dil standartları (genel) → `[[../other/c-template]]` |
| MISRA C:2025 alt kümesi, IEC 60730 Class B, FCC Part 15 | C++/RTOS projeleri → `[[../other/cpp-template]]` |

- **Dosya tipi:** Markdown teknoloji yönetişim şablonu (`.templates/hardware/` altı).
- **Kullanan agent:** Embedded Engineer (sorumlu), DSP Firmware Engineer (ikincil).
- **Doğrulama zorunluluğu:** araç zinciri sürümleri, MCU parametreleri ve standartlar §7'de web kaynaklarıyla doğrulanmıştır; doğrulanamayan iddia `⚠️ VERIFICATION REQUIRED` taşır.

---

## 3. Mimari

### 3.1 Tek Katmanlı Bare-Metal Modeli (eski §1)

AVR, uyan-olay-anadöngü ile katı tek katmanlı, kesme驱动 bir mimari kullanır. İşletim sistemi yok, RTOS yok, bellek koruma birimi yok — geliştirici her döngüyü ve her baytı yönetir.

**Yürütme modeli:**
```
Reset vector → .init3 (stack init) → main()
  ├── peripherals_init()    — PORT/DDR, timer, ADC, USART registerları
  ├── sei()                 — küresel kesmeleri etkinleştir
  ├── sleep_mode() / idle
  └── [kesme tetiklenir]
       └── ISR(vector)      — SREG kaydet, olayı işle, SREG geri yükle
            └── reti()      — dön, kesmeleri yeniden etkinleştir
```

**Register erişimi — doğrudan, soyutlama katmanı yok:**
```c
// ATmega328P — doğrudan register erişimi
DDRB  |= _BV(PB5);                    // PB5 (Arduino D13) çıkış
PORTB |= _BV(PB5);                    // HIGH yap
TCCR1A = _BV(WGM10) | _BV(WGM11);    // Fast PWM, 10-bit
TCCR1B = _BV(WGM12) | _BV(CS11);     // prescaler /8
OCR1A  = 512;                          // %50 duty cycle
```

**Bellek haritası (ATmega328P):**
- Flash: 32 KB (RWW + NRWW bölümleri, 10k silme çevrimi)
- SRAM: 2 KB (0x0100'de başlar, yığın aşağı doğru büyür)
- EEPROM: 1 KB (100k yazma çevrimi, ayrı I2C uzayı — EECR/EEDR/EEAR üzerinden)
- Register dosyası: 0x0000–0x001F (r0–r31), I/O: 0x0020–0x005F, Ext I/O: 0x0060–0x00FF

**Kritik kural:** ISRs mümkünse 128 bayt üretilmiş kodun altında kalmalıdır. Uzun işlemler (ADC dönüşümü, EEPROM yazma) ISR'den kurulan durum makinesi bayrağıyla ana döngüde yoklanır. ISR'den asla `printf()` çağrılmaz — 2 KB+ stdio çeker ve kesmeleri yüzlerce mikrosaniye kapatır.

**SRAM yerleşim disiplini:**
```
0x0100  — .data (başlangıçta global, crt1 ile flash'tan kopyalanır)
0x0100+ — .bss  (sıfırlanmış global)
         — heap  (kaçının: bare-metal'de brk() yok, malloc 400+ bayt çeker)
         ← yığın (RAMEND'den aşağı büyür, SP registerı ile izlenir)
```

### 3.2 Kesme Öncelik Mimarisi (eski §5.1)

AVR sabit donanım önceliğini vektör adresiyle kullanır — düşük adres = yüksek öncelik. Kod buna göre düzenlenir:

| Öncelik | Vektör | Amaç | Maks. Gecikme |
|---------|--------|------|----------------|
| 1 (yüksek) | RESET | Güç açılma / watchdog sıfırlama | N/A |
| 2 | INT0 | Harici pin (acil durdurma) | <1 µs |
| 3 | PCINT0 | Pin değişimi (buton matrisi) | <2 µs |
| 4 | TIMER1_COMPA | Ses örnek saati | <5 µs |
| 5 | TIMER0_OVF | Sistem tik (1 ms) | <5 µs |
| 6 | ADC | Dönüşüm tamamlandı | <3 µs |
| 7 | USART_RX | Seri alım | <10 µs |
| 8 (düşük) | SPI_STC / TWI | Peripheral aktarımı | <20 µs |

**Altın kural:** Ses için kullanılan zamanlayıcı (TIMER1), seri I/O'dan daha yüksek öncelikte olmalıdır; UART kesmesi tetiklendiğinde ses kırılması önlenir. İkisinin birlikte yaşaması gerekirse seri ISR içinde yalnız kritik register erişimi sırasında küresel kesmeler kapatılır (`UDR` okuması öncesi `cli()`, sonrası `sei()`).

### 3.3 Ana Döngü Kalıbı — Uyan-Olay (eski §5.2)

```c
int main(void) {
    peripherals_init();
    watchdog_init();
    sei();                     // küresel kesmeleri etkinleştir
    event_flags_t events = 0;  // ISR ile paylaşılır, atomik değişir

    while (1) {
        wdt_reset();
        events = atomic_load();   // kesme bayraklarını oku

        if (events & FLAG_ADC_READY) {
            uint16_t sample = adc_get_last();
            process_audio(sample);
            atomic_clear(FLAG_ADC_READY);
        }
        if (events & FLAG_BUTTON) handle_button(button_get());

        sleep_mode();            // SE biti, kesmeyle uyan
    }
}
```

Kesme-ana döngü iletişimi volatile byte bayraklarıyla: ISR içinde tek baytlık yazma (`event_flags |= FLAG_AUDIO;`) içerî olarak atomiktir.

### 3.4 Karar Kayıtları (eski §10)

**MCU Seçimi: ATmega328P vs ATtiny85**

| Kriter | ATmega328P | ATtiny85 |
|--------|------------|----------|
| **Flash** | 32 KB | 8 KB |
| **SRAM** | 2 KB | 512 B |
| **EEPROM** | 1 KB | 512 B |
| **Pin** | 28 (23 GPIO) | 8 (6 GPIO) |
| **Zamanlayıcı** | 3 (1× 16-bit, 2× 8-bit) | 2 (8-bit) |
| **ADC** | 10-bit, 6 kanal (TQFP-32: 8 kanal) | 10-bit, **4 kanal** |
| **Paket** | DIP-28 / TQFP-32 | DIP-8 / SOIC-8 |
| **Ömür** | 10k flash / 100k EEPROM çevrimi | 10k flash / 100k EEPROM çevrimi |
| **Araç zinciri** | avr-gcc + avrdude | avr-gcc + avrdude |

> **Düzeltme (2026-09-29):** Eski dokümanda ATtiny85 ADC "3 kanal" ve pin "5 GPIO" olarak geçiyordu; datasheet doğrulaması **4 ADC kanalı / 6 GPIO** (PC5 dahil) gösterir. *(ATtiny85'in ADC0-ADC3 girdileri vardır; PC5/ADC3.)* ⚠️ VERIFICATION REQUIRED: ATtiny85'in ADC3/PC5 pin eşlemesi pakete göre teyit edilmelidir.

**Karar:**
- DSP ses işleme (PCM5122 DAC, 48 kHz I2S) için: **ATmega328P** — ses tamponları için daha fazla SRAM, hassas örnek hızı için 16-bit zamanlayıcı.
- Basit sensör okuma (buton matrisi, LED kontrolü) için: **ATtiny85** — daha düşük maliyet, daha küçük ayak izi.

**Saat Kaynağı: Dahili vs Harici Kristal**

| Kaynak | Hassasiyet | Başlangıç | BOM Maliyeti | EMI |
|--------|------------|-----------|--------------|-----|
| Dahili RC (8 MHz) | ±%3 (kalibre) | Anında | $0 | Düşük |
| Harici Kristal (16 MHz) | ±50 ppm | ~4 ms | $0.15 + kapasitör | Daha yüksek |
| Harici Osilatör | ±25 ppm | ~2 ms | $0.50 | En düşük |

**Karar:** Hassas I2S bit saati gerektiren ses uygulamaları harici 16 MHz kristal kullanmalıdır. Dahili RC sıcaklıkla kayar, DAC çıkışında duyulur perde varyasyonu üretir. Yalnız UART uygulamalarında dahili RC ±%3, 115200 baud toleransı içindedir.

---

## 4. Kurallar

### 4.1 Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | `malloc()/calloc()/realloc()/free()` YASAK — yalnız statik ayırma | Bellek parçalanması, geri alınamaz çökme |
| 2 | Sınırsız `printf()/sprintf()` YASAK (2 KB+ stdio çeker) | SRAM taşması |
| 3 | Özyineleme ve `alloca()` YASAK | Öngörülemeyen yığın derinliği, SRAM sarması |
| 4 | `setjmp()/longjmp()` YASAK | SREG bozulur, ISR dönüşü çöker |
| 5 | Register yazımlarında `_BV()` zorunlu — sihirli sayı yasak | Okunamayan, hatalı bit yerleşimi |
| 6 | Bütün sabit tablolar `PROGMEM` ile flash'ta | 512 B tablo SRAM'in %25'ini yer |
| 7 | Üretim cihazlarında watchdog + BOD fuse zorunlu | Askıda kalma / belirsiz davranış |
| 8 | Fuse yapılandırması belgelenmeden üretime programlanamaz | Brick riski (§5.5) |

### 4.2 Dil ve Araç Zinciri Standartları (eski §2)

| Gereklilik | Spesifikasyon |
|------------|---------------|
| **Derleyici** | avr-gcc 15.x (GNU11 / `-std=gnu11`) — sürüm bkz. §7.3 |
| **Kütüphane** | avr-libc 2.2.1 (`avr/io.h`, `avr/interrupt.h`, `avr/pgmspace.h`) |
| **Linker** | avr-ld, avr-libc linker script'leri |
| **Binary biçimi** | Flash için Intel HEX (.hex), EEPROM için .eep |
| **Programlama** | avrdude 8.2 (AVRISP mkII, USBasp, Atmel-ICE) |

**Zorunlu başlık kalıbı:**
```c
#include <avr/io.h>          // register tanımları (avr/sfr_defs.h dahil)
#include <avr/interrupt.h>   // ISR() makrosu, sei(), cli()
#include <avr/pgmspace.h>    // PROGMEM flash sabitleri
#include <avr/wdt.h>         // watchdog timer
#include <util/delay.h>      // _delay_ms(), _delay_us() (satır içi, çevrim sayım)
#include <stdint.h>          // uint8_t, uint16_t, uint32_t
#include <stdbool.h>         // bool, true, false
// Bellek ayırca veya FILE* akışı kullanan stdio fonksiyonları YOK
// sprintf_P(küçük_tampon, PSTR("val=%d"), x) sınırlı tamponla uygundur
```

**Adlandırma:**
```c
#define F_CPU 16000000UL              // Peripheral registerları: UPPER_SNAKE_CASE
void timer1_init(void);               // Fonksiyonlar: snake_case, fiil_nesne
uint16_t adc_read(uint8_t channel);
ISR(TIMER1_COMPA_vect) { ... }        // ISR adları: avr/io.h'daki vektör adıyla
ISR(INT0_vect) { ... }
#define LED_PIN PB5                   // Makrolar: UPPER_SNAKE_CASE + açıklayıcı önek
#define ADC_REF DEFAULT
```

### 4.3 Güvenlik Kuralları (eski §3)

**Watchdog Timer (zorunlu):** `main()`'in ilk 10 komutu içinde etkinleştirilir; ana döngü zaman aşımından uzun sürerse MCU sıfırlanır.

```c
void watchdog_init(void) { wdt_enable(WDTO_2S); }  // ATmega328P için 2 sn
void main_loop(void) {
    while (1) {
        wdt_reset();      // 2 sn içinde servis edilmeli
        process_events();
        sleep_mode();
    }
}
```
- ISR watchdog zaman aşımından uzun sürerse MCU sıfırlanır; bütün ISRs <100 µs'de bitmeli, `wdt_reset()` yalnız ana döngüde çağrılır.

**Brown-Out Detection (BOD):** Fuse bitleriyle etkinleştirilir, VCC çalışma geriliminin altına düştüğünde tanımsız davranışı önler:
```
BODLEVEL = 2.7V (ATmega328P: uzatılmış fuse'da BODLEVEL=101)
BOD eylemi = BOD_ACT_HYST (histerezis — eşikte osilasyonu önler)
```

**Lock Bitleri ve Bellek Koruması (üretim için öneriliyor):**
```
LB2:LB1 = 10     — bellek kilidi, flash/EEPROM harici okuma yok
BLB12:BLB11 = 11 — boot kilidi, boot bölümüne yazma yok (bootloader bozulması önlenir)
```

**SRAM Sınır Kontrolü (MMU/MPU yok):**
1. **Stack canary:** SRAM başına (SP başlangıcının hemen altı) bilinen desen yerleştirilir, periyodik doğrulanır.
2. **Derleme zamanı:** avr-gcc'de `-Warray-bounds -Wstack-usage=128`.
3. **Çalışma zamanı:** Her dizi erişimi `#define MAX_LEN` sabitine karşı kontrol edilir.

```c
#if defined(DEBUG) && DEBUG
static inline void check_stack(void) {
    extern uint8_t __stack;      // linker semboli: .bss sonu
    if (__stack < 0x100 + 512) { // %50 SRAM eşiği
        PORTB |= _BV(PB0);       // hata LED'ini yak
    }
}
#endif
```

**Buffer Taşması Yok:** AVR'de `strncpy_s` yoktur — eşdeğeri elle uygulanır; her dize/dizi işlemi sınırlı varyantlarla, her zaman null sonlandırmalıdır.

### 4.4 EEPROM Kalıcılık Kuralları (eski §4)

| Parametre | Değer |
|-----------|-------|
| **Ömür** | Hücre başına 100.000 yazma çevrimi (ATmega serisi) |
| **Silme/yazma süresi** | 3.3 ms tipik (8-16 MHz) |
| **Okuma süresi** | Bir çevrim (CPU saatiyle senkron) |
| **Toplam boyut** | 512 B (ATtiny) – 4 KB (ATmega2560) |

**Aşınma dengeleme:** Aynı EEPROM hücresi yüksek frekansla tekrar tekrar yazılmaz. 64 yuvalı round-robin şema kullanılır (eski §4 kodu korunmuştur): her yapılandırma bloğu `version + veri + crc16` şeklindedir; en yüksek `version`'lı yuva açılışta yüklenir, kayıt bir sonraki yuvaya yazılır.

**CRC-16 yapılandırma doğrulama:** EEPROM'da saklanan HER yapılandırma bloğu açılışta CRC-16 ile doğrulanır (poly `0xA001`, başlangıç `0xFFFF`); uyuşmazlıkta varsayılanlara dönülür.

### 4.5 Kod Kalitesi Kuralları (eski §7)

**Doğrudan Register Erişimi (HAL yok):**
```c
// ❌ YANLIŞ: soyutlama zamanlama kritik register sırasını gizler
void pwm_set_duty(uint8_t duty) { analogWrite(PIN_D9, duty); }  // wiring_analog.c çeker

// ✅ DOĞRU: doğrudan register erişimi, bilinen zamanlama
void pwm1_set_duty(uint16_t duty) { OCR1A = duty; }  // tek çevrimlik yazma
```

**`_BV()` makrosu:** `(1 << bit)`'e açılır. Tek bit ayarlarken her zaman kullanılır; sihirli sayı yasak:
```c
DDRB  |= _BV(PB5) | _BV(PB3) | _BV(PB2);   // ✅
TCCR1A = _BV(WGM10) | _BV(COM1A1);          // ✅
// ❌ DDRB |= 0b00101100;  — okuyucu bit konumlarını çözmek zorunda
```

**`PROGMEM` flash sabitleri için:** AVR Flash 32-256 KB, SRAM 2-8 KB. Bütün sabit tablolar (arama tabloları, dizeler, kalibrasyon verisi) flash'ta `PROGMEM` ile oturur; 512 baytlık tablo PROGMEM ile SRAM'in %25'ini değil, flash'ın %2'sini kaplar ve sıfır SRAM kullanır.

**`malloc()` YOK — yalnız Statik Ayırma:**
```c
#define AUDIO_BUFFER_SIZE 256
static int16_t audio_buffer[AUDIO_BUFFER_SIZE];  // ✅ .bss'ta 512 bayt
// ❌ int16_t* audio_buffer = (int16_t*)malloc(512);  — parçalanma riski
```
Değişken uzunluklu tampon gerçekten gerekirse (8-bit embede'de nadir) sabit maksimum kullanılır: `#define MAX_PACKET_SIZE 64` + `rx_length <= MAX_PACKET_SIZE`.

### 4.6 Hata Yönetimi (eski §13)

AVR'de heap olmadığından dinamik hata nesneleri imkânsızdır; hatalar tek `uint8_t` içinde bitmask ile iletilir:

```c
typedef enum {
    ERR_NONE          = 0,
    ERR_WDT_TIMEOUT   = _BV(0),  // watchdog sıfırlaması oldu
    ERR_ADC_SATURATE  = _BV(1),  // ADC girdi aralık dışı
    ERR_EEPROM_CRC    = _BV(2),  // yapılandırma CRC uyuşmazlığı
    ERR_UART_OVERFLOW = _BV(3),  // UART RX tamponu doldu
    ERR_SPI_TIMEOUT   = _BV(4),  // SPI aktarımı kilitlendi
    ERR_I2C_NACK      = _BV(5),  // TWI slave'den NAK
    ERR_STACK_LOW     = _BV(6),  // yığın SRAM sınırına yaklaşıyor
    ERR_RESERVED      = _BV(7),  // ayrılmış
} error_flags_t;

volatile uint8_t global_error_flags = 0;
#define error_set(flag)   global_error_flags |= (flag)    // tek baytlık atomik
#define error_clear(flag) global_error_flags &= ~(flag)
```

**LED yanıp sönme desenleri:** Durum LED'i olan cihazlarda hata durumu açılışta blink deseniyle kodlanır (`0b11` = EEPROM hatası, `0b1` = WDT sıfırlaması; temiz açılışta yanıp sönme yok).

**Watchdog Sıfırlama Kaynağı:**
```c
void check_reset_source(void) {
    if (MCUSR & _BV(WDRF)) { error_set(ERR_WDT_TIMEOUT); MCUSR &= ~_BV(WDRF); }
    if (MCUSR & _BV(BORF)) { MCUSR &= ~_BV(BORF); }  // brownout — hata değil
}
```

---

## 5. Workflow

### 5.1 Datasheet Bölümleri — Zorunlu Okuma (eski §9)

| Bölüm | Konu | Kritik Noktalar |
|-------|------|-----------------|
| **7. I/O-Ports** | Port yapılandırması, pull-up, alternatif fonksiyonlar | `PORTxn` vs `DDRxn` sırası, MCUCR'daki `PUD` biti |
| **11. TC1 — 16-bit Timer/Counter** | Timer modları, PWM, giriş yakalama | `ICR1` TOP vs `OCR1A` TOP, `TCCR1A/B` bit düzeni |
| **17. 2-wire Serial (TWI)** | I2C bus master/slave | TWBR hesabı, TWPS prescaler, TWINT temizleme |
| **20. ADC** | Dönüşüm, gürültü bastırıcı, serbest koşu | Prescaler <200 kHz, ilk dönüşüm daha uzun |
| **22. EEPROM** | Okuma/yazma zamanlaması, kendi kendine programlama | Kendi kendine zamanlı silme/yazma, hazır bayrakları |
| **24. USART0** | Seri, çerçeve biçimleri, baud | UBRR = F_CPU/16/BAUD-1, çift hız U2X0 |
| **28. Fuse Bits** | BOD, saat kaynağı, boot boyutu | Lock bitleri, uzatılmış fuse'lar, fabrika varsayılanları |
| **29. Elektriksel Özellikler** | Zamanlama, eşikler, güç | BOD eşikleri, akım çekimi, pin kapasitansı |

### 5.2 Fuse Bit Yapılandırması (eski §9 — brick riski)

Fuse bitleri sınırlı ölçüde tek seferlik programlanabilir; yanlış ayar MCU'yu brick edebilir (yüksek voltajlı programlama gerekir). HER üretim fuse yapılandırması bir başlıkta belgelenir:

```c
// fuse.conf — ATmega328P üretim ayarları
// Programlama: avrdude -p m328p -U lfuse:w:0xFF:m -U hfuse:w:0xDE:m -U efuse:w:0xFD:m
// Low Fuse (0xFF): Harici kristal 8-16 MHz, başlangıç 14CK + 65ms
// High Fuse (0xDE): Brown-out 2.7V, bootloader yok, EEPROM korunur
// Extended Fuse (0xFD): Brown-out etkin, OCD kapalı (üretimde debugWire kapalı)
```

### 5.3 Kod İncelemesi Zorunluluğu

Bütün AVR firmware'leri programlamadan önce gömülü sistemler mühendisi tarafından incelenir. İnceleme kontrolü:

1. Register bit desenleri datasheet ile eşleşiyor (çevrilmiş nibble yok)
2. Fuse bitleri hedef MCU gerilimi ve saatiyle uyumlu
3. ISR gecikme bütçesi zamanlama gereksinimini karşılıyor
4. I2C tamponlama kaynaklı yığın taşması riski yok
5. Herhangi bir bloklama işleminden önce watchdog etkin

### 5.4 Derleme Hattı: avr-gcc + avrdude (eski §11)

```makefile
# ATmega328P firmware dağıtım Makefile'ı
MCU      = atmega328p
F_CPU    = 16000000UL
TARGET   = firmware
SRC      = main.c timer.c adc.c uart.c
CC       = avr-gcc
OBJCOPY  = avr-objcopy
SIZE     = avr-size
AVRDUDE  = avrdude
PORT     = /dev/ttyUSB0  # Linux; Windows'ta COM3

CFLAGS   = -mmcu=$(MCU) -DF_CPU=$(F_CPU) -std=gnu11 -Os -Wall -Wextra
CFLAGS  += -Werror -Warray-bounds -Wstack-usage=128 -flto
CFLAGS  += -fstack-usage  # yığın analizi için .su dosyaları üretir
LDFLAGS  = -mmcu=$(MCU) -Wl,-gc-sections -flto

all: $(TARGET).hex $(TARGET).eep size
$(TARGET).elf: $(SRC)
	$(CC) $(CFLAGS) $(SRC) $(LDFLAGS) -o $@
$(TARGET).hex: $(TARGET).elf
	$(OBJCOPY) -O ihex -R .eeprom $< $@
$(TARGET).eep: $(TARGET).elf
	$(OBJCOPY) -O ihex -j .eeprom $< $@
flash: $(TARGET).hex
	$(AVRDUDE) -p $(MCU) -c usbasp -P $(PORT) -U flash:w:$<:i
fuses:
	$(AVRDUDE) -p $(MCU) -c usbasp -U lfuse:w:0xFF:m -U hfuse:w:0xDE:m -U efuse:w:0xFD:m
size: $(TARGET).elf
	$(SIZE) --mcu=$(MCU) -C $<
clean:
	rm -f *.elf *.hex *.eep *.o *.su
.PHONY: all flash fuses size clean
```

### 5.5 CI Derleme Kontrolü (GitHub Actions — eski §11)

```yaml
name: avr-compile-check
on: [push]
jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Install avr-gcc
        run: |
          sudo apt-get update
          sudo apt-get install -y gcc-avr avr-libc avrdude
      - name: Build
        run: make
      - name: Check size
        run: |
          make size
          # Flash kullanımı %90'ı aşarsa başarısız
          SIZE_OUTPUT=$(make size 2>&1)
          PERCENT=$(echo "$SIZE_OUTPUT" | grep -oP '(\d+\.?\d*)%' | head -1)
          if (( $(echo "$PERCENT > 90" | bc -l) )); then
            echo "FAIL: Flash usage $PERCENT exceeds 90% limit"
            exit 1
          fi
```

### 5.6 Yükseltme / Geçiş (eski §16)

| Kaynak | Hedef | Gereken Değişiklikler |
|--------|-------|-----------------------|
| ATmega328P (32 KB Flash, 2 KB SRAM) | ATmega2560 (256 KB, 8 KB SRAM) | `-mmcu` bayrağı, timer register adları, fuse adresleri, linker script. SRAM dostu kod daha büyük tamponlardan yararlanır. |
| ATtiny85 | ATtiny84 | Farklı pinout, aynı çekirdek. Küçük kod değişiklikleri: bazı Tiny parçalarda TIMSK → TIMSK1. |
| ATmega328P | ATmega4809 (AVR enhanced, 48 KB) | Yeni peripheral register haritası (TCB, TCA, ZCD). Bütün TCCR/OCR referansları güncellenir. avr-libc 2.2+ 0-serisini destekler. |

**Geçiş kontrol listesi:** register uyumluluğu (datasheet haritaları karşılaştırılır) · kesme vektör adları (`TIMER1_COMPA_vect` → 0-serisinde `TCB0_INT_vect`) · fuse byte eşlemesi (ATmega/ATtiny aileleri farklı) · linker script · `<avr/io.h>` `-mmcu` bayrağından otomatik tanır.

| Kaynak | Hedef | Etki |
|--------|-------|------|
| avr-gcc 7.x | avr-gcc 15.x | Yeni builtin'ler (`__builtin_avr_delay_cycles`), gelişmiş LTO, daha iyi PROGMEM işleme. `-Os` optimizasyonu daha agresif — zamanlama için test edin. |
| avr-libc 1.8.x | avr-libc 2.2.x | Yeni başlık `avr/cpufunc.h` (NOP/sleep). EEPROM fonksiyon imzaları değişmedi. |
| avrdude 6.x | avrdude 8.2 | USBasp desteği gelişti, Atmel-ICE protokol değişiklikleri, yeni `-x` uzatılmış parametreleri. |

---

## 6. Doğrulama

### 6.1 Simülasyon: SimulAVR (eski §6)

simulavr, ATmega ve ATtiny cihazlarının çevrim-hassas simülasyonunu sağlar; derlenmiş .elf dosyasını simüle edilmiş donanım modeline bağlar.

```bash
# ATmega328P simülasyonu, 16 MHz, UART stdout'a bağlı
simulavr -d atmega328p -f firmware.elf -c vcd:trace.vcd -m 16000000

# GDB ile kırılma noktası hata ayıklama
simulavr -d atmega328p -f firmware.elf -g
avr-gdb firmware.elf
(gdb) target remote localhost:1212
(gdb) break adc_read
(gdb) continue
```

### 6.2 Birim Testleri: Ceedling + Unity (eski §6)

Ceedling (Ruby tabanlı test çalıştırıcı) + Unity, AVR simülasyonu kapsamlı birim testler için çok yavaş olduğundan test kodunu avr-gcc ile değil host gcc ile derler. Donanım bağımlı fonksiyonlar sahtelenir.

```c
// test/test_adc.c
#include "unity.h"
#include "mock_adc_hardware.h"  // otomatik üretilmiş mock

void setUp(void) { adc_mock_Init(); }

void test_adc_read_returns_value_in_range(void) {
    adc_mock_setValue(512);
    uint16_t result = adc_read(0);
    TEST_ASSERT_UINT16_WITHIN(5, 512, result);
}
```
```bash
ceedling test:adc       # host gcc ile derle, geliştirme makinesinde çalıştır
ceedling coverage:all   # host derlemeli testlerde gcov
```

### 6.3 HITL: Lojik Analizör (eski §6)

| Sinyal | Pin | Amaç |
|--------|-----|------|
| CLKOUT (PB0) | D8 | Titreşim ölçümü için 1 MHz saat çıktısı |
| ISR_START (PD2) | D2 | ISR girişinde HIGH, çıkışında LOW (ISR gecikmesi) |
| AUDIO_FS (PD3) | D3 | Çerçeve senkron darbesi, periyot kararlılığını ölç |

```bash
sigrok-cli -i capture.srzip -P i2s -A i2s=mclk:SCLK,ws:LRCK,sd:SDATA
```

### 6.4 Test Kapsam Hedefleri

| Katman | Host Derlemeli | Hedefde | Sıklık |
|--------|----------------|---------|--------|
| Uygulama mantığı | %90+ (Unity) | — | Her commit |
| Donanım sürücüleri | %70+ (mock) | %100 açılış testi | Her sürüm |
| ISR işleyicileri | %50+ (simülasyon) | %100 scope yakalama | Her sürüm |
| EEPROM aşınma dengeleme | %95+ (Unity) | — | Her commit |

### 6.5 Performans Bütçeleri (eski §12)

| Metrik | Hedef | Yöntem |
|--------|-------|--------|
| **ISR gecikmesi** | <2 µs (bayttan ilk komuta) | ISR_START pinini scope ile ölç |
| **ISR titremesi** | 16 MHz'de <50 ns | Ardışık ISR_START kenarlarını ölç |
| **Ana döngü iterasyonu** | <5 ms (bütün bloklamayan yollar) | LOOP pinini anahtarla, lojik analizörle yakala |
| **ADC örnek hızı** | 15.6 kHz (10-bit, prescaler /128 maks.) | ADCSRA serbest koşu yapılandırması |
| **Timer1 çözünürlüğü** | 4 µs (16 MHz'de 1:1 prescaler) | OCR1A TOP değeri |
| **I2S bit saati** | 3.072 MHz (48 kHz × 64 bit/çerçeve) | SPI master F_CPU/4 = 4 MHz (örneklenmemiş) |
| **Flash okuma (tek word)** | 2 çevrim (boru hattı, prefetch sonrası bir/tur) | `pgm_read_word()` satır içi |
| **SRAM erişimi** | 2 çevrim (registerlar için tek çevrim) | Load/store komutları |

**Kesme gecikmesi dökümü (16 MHz = 62.5 ns/çevrim):** mevcut komutu tamamla (1-4 çevrim) → PC it (2) → SREG it (1) → vektör adresini yükle (1) → ISR'a atla (2) = **donanım gecikmesi 7-10 çevrim = 437-625 ns**. ISR 16 komutta tutulursa (bayrak kurma tipik) toplam ISR süresi ~2 µs.

**Bellek ayak izi hedefleri:**

| Bileşen | Flash | SRAM |
|---------|-------|------|
| Ana döngü + init | 2 KB | — |
| Timer ISR (1 kHz tik) | 64 B | 4 B (tay sayacı) |
| ADC + tampon (serbest koşu) | 512 B | 512 B (tampon) |
| UART TX/RX ring tamponları | 1.5 KB | 256 B |
| I2C TWI master | 1 KB | 32 B |
| EEPROM aşınma dengeleme | 2 KB | 16 B |
| **Toplam (bütçe)** | **7 KB (%22)** | **820 B (%40)** |

### 6.6 Sorun Giderme (eski §15)

**Fuse Brick (MCU yanıt vermiyor):** Fuse programladıktan sonra MCU avrdude'ya yanıt vermiyorsa cihaz ölü değildir — saat kaynağı fuse'u kristal bağlıyken harici kaynağa ayarlanmış olabilir. XTAL1 pinine (DIP-28'de PB6) 1-8 MHz saat sinyali uygulanır, sonra fuse doğru değere programlanır:
```bash
# XTAL1'e harici saat uygula, sonra:
avrdude -p m328p -c usbasp -U lfuse:w:0xFF:m
```
Lock bitleri ayarlıysa yüksek voltajlı programlama (HVPP/HVSP, Atmel-ICE HV adaptörü) gerekir.

**Saat Uyumsuzluğu — Yanlış Baud:** UART çıktısı bozuksa neden `F_CPU`'nun gerçek frekansla eşleşmemesidir. Teşhis: sistemi CLKO pinine (PB0) yansıt ve osiloskopla ölç. Düzeltme: `F_CPU` tanımını veya fuse'ları donanımla eşleştir.

**Watchdog Döngüsü:** MCU sürekli sıfırlanıyorsa ana döngü `wdt_reset()` çağırmadan zaman aşımını aşıyordur. Açılışta `MCUSR` okunur; `wdt_reset()` her döngü başında çağrılır ve hiçbir tek işlem WDT zaman aşımının %50'sini (WDTO_2S için 1 sn) aşmaz.

**BOD Brownout — Sahte Sıfırlamalar:** Yüksek akımlı yükler açıldığında MCU sıfırlanıyorsa VCC BOD eşiğinin altına düşüyordur. 1) Yük anahtarlamasında VCC dalgalanmasını osiloskopla ölç · 2) yük sürücüsüne 10-100 µF baypas ekle · 3) VCC 4.0V'a düşüyorsa BOD'ı 4.0V değil 2.7V yap (`BODLEVEL` fuse) · 4) histerezisi etkinleştir.

### 6.7 Kabul Kontrol Listesi

- [ ] Frontmatter 7 alan tam
- [ ] H1 + §1-§7 sırası sabit
- [ ] `{{PLACEHOLDER}}` kalmadı
- [ ] Wiki-link'ler `.md` uzantısız, düzeltilmiş yollarla (`[[hardware/arduino-template]]` vb.)
- [ ] Araç zinciri sürümleri §7.3 ile eşleşiyor
- [ ] Kaynak dosya değiştirilmedi/silinmedi

---

## 7. Referanslar

### 7.1 Vault Bağlantıları

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Template registry | [[.templates/index]] | Envanter (DRY), §3.2 iskelet |
| Vault anayasası | [[../CLAUDE.md]] | Hard Guardrails (16) |
| Agent registry | [[../AGENTS.md]] | §6 routing (embedded) |
| Hardware şablonu | [[hardware/hardware-template]] | Genel donanım tasarım dokümanı |
| Arduino şablonu | [[hardware/arduino-template]] | Arduino framework'lü projeler |
| PIC şablonu | [[hardware/pic-template]] | Alternatif MCU platformu (XC8, banked bellek) |
| C şablonu | [[../other/c-template]] | AVR'de kullanılan C11 dil standartları |
| C++ şablonu | [[../other/cpp-template]] | Neva Engine C++20 (ARM/x86) — AVR'de kullanılmaz |
| ADR Audio | [[../adr/adr-audio-template]] | Ses donanımı mimari kararları |

### 7.2 AVR vs PIC Seçimi (eski §18)

- **AVR** ses DSP için tercih edilir (daha iyi avr-gcc optimizasyonu, açık kaynak ses kütüphaneleri ekosistemi).
- **PIC** ultra düşük güç batarya uygulamaları için tercih edilir (nanoWatt XLP teknolojisi, daha derin uyku modları).
- İkisi de I2C sensör arayüzü ve UART tabanlı kontrol için uygundur.

### 7.3 Doğrulanmış Araç Zinciri Sürümleri (web doğrulaması — 2026-09-29)

| Bileşen | Doğrulanmış sürüm | Kaynak |
|---------|-------------------|--------|
| **avr-gcc (Microchip resmî)** | GCC **15.1.0** + avr-libc **2.2.1** + binutils **2.44** | Microchip AVR-GCC toolchain release notes |
| **avr-gcc (topluluk — ZakKemble/avr-gcc)** | GCC **16.1.0** + avr-libc **2.3.2** + binutils **2.46.1** | github.com/ZakKemble/avr-gcc releases |
| **avrdude** | **v8.2** | github.com/avrdude/avrdude releases |
| **avr-gdb** | GCC ailesiyle eşleşen 15.x | GNU GDB AVR hedefi |
| **simulavr** | 1.2+ (paket depoları) | ⚠️ VERIFICATION REQUIRED (kesin sürüm) |

> Eski dokümandaki `avr-gcc 14.x`, `avr-libc 2.2`, `avr-binutils 2.42`, `avrdude 8.x` değerleri bu tabloyla güncellenmiştir.

**Proje başına sürüm sabitleme:** Her proje `toolchain-version.txt` dosyasında sürümü sabitler:
```
avr-gcc:      15.1.0 (Microchip resmî paket)
avr-libc:     2.2.1
avr-binutils: 2.44
avrdude:      8.2
```

### 7.4 Doğrulanmış Standartlar (web doğrulaması — 2026-09-29)

| Standart | Doğrulanmış durum | Kaynak |
|----------|-------------------|--------|
| **MISRA C:2025** | Mart 2025'te yayımlandı — **225 aktif kural** (MISRA C:2023: Nisan 2023, 221 kural; C:2012 + AMD3 Kasım 2022 / AMD4 Mart 2023) | misra.org.uk |
| **IEC 60730 Class B** | Gömülü ev aleti güvenlik testleri — AVR/PIC firmware uygulaması §6'da | IEC 60730-1 |
| **FCC Part 15** | 1,7 MHz üzeri saat üreten cihazlar (16 MHz MCU dahil) | eCFR 47 CFR Part 15 |

**MISRA C:2025 alt kümesi (eski §19 — AVR uygulaması):**

| Kural | Açıklama | AVR Uygulaması |
|-------|----------|----------------|
| **Dir 4.1** | Çalışma zamanı hataları en azaltılmalı | Watchdog, BOD, açılışta CRC doğrulama zorunlu |
| **Rule 8.2** | Fonksiyon tipleri prototip kapsamında olmalı | Her fonksiyon kullanımdan önce prototiplenir; avr-gcc `-Wimplicit-function-declaration` bunu zorlar |
| **Rule 12.1** | Dinamik yığın ayırma yok | `malloc()` yasak; `avr-nm firmware.elf \| grep malloc` boş dönmeli |
| **Rule 13.5** | Boolean ifadelerde yan etki yok | ISR bayrakları `volatile uint8_t` — bir kez oku, açıkça temizle; geçici değişken kullan |
| **Rule 14.3** | Kontrol ifadeleri Boolean olmalı | `<stdbool.h>`'dan `bool`, tamsayı koşul değil |
| **Rule 17.2** | Özyineleme yok | Fonksiyon çağrı grafiği döngüsüz; `avr-nm -u --size-sort` ile denetlenir |
| **Rule 21.7** | `setjmp`/`longjmp` yok | ISR sınırında register durumunu bozar — yasak |
| **Rule 22.1** | Bütün bellek erişimi sınırlanmış | Her tampon erişimi derleme zamanı `#define` sınırları kullanır; ayıklama baskılarında çalışma zamanı kontrolü |

**IEC 60730 Class B uyumluluğu (güvenlik kritik firmware):** 1) CPU register testi (bilinen desen yaz-oku-doğrula) · 2) SRAM testi (açılışta March C: `0x55`, `0xAA`, `0x00`, `0xFF`) · 3) Flash CRC (açılışta tüm uygulamanın CRC-16'sı) · 4) Saat arızası tespiti (CLKOUT'u ikinci zamanlayıcıyla izle) · 5) Kesme kırılması (TIMER1_COMPA son tarihini kaçırırsa hata bayrağı).

**FCC Part 15 (16 MHz'den yüksek saat üreten cihazlar):** 1) Üretiliyorsa gölgeli muhafaza · 2) Yayılım spektrumu (AVR'de SSCG yok — harici SSCG osilatörü) · 3) Baypas: her VCC pinine 100 nF seramik + güç girişine 10 µF · 4) PCB yerleşimi: yüksek frekanslı izlerin (SCK, MOSI, MISO) halka alanını minimize et, kenarlardan uzak tut.

### 7.5 Sürüm Kaydı

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 1.0 | 2026-06-24 | İlk AVR şablonu (eski spec, 1119 satır, `.templates/avr-template.md`) |
| 1.0.0 | 2026-09-29 | Guardrail #16 iskeletine yeniden düzenlendi; araç zinciri web ile doğrulandı (avr-gcc 15.1.0/16.1.0, avr-libc 2.2.1/2.3.2, binutils 2.44/2.46.1, avrdude 8.2); ATtiny85 ADC 4 kanal olarak düzeltildi; MISRA C:2025 doğrulandı (Mart 2025, 225 kural); wiki-link yolları düzeltildi |

---

**Template Version:** 1.0.0
**Last Updated:** 2026-09-29
