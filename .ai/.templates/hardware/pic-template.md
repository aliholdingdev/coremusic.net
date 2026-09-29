---
title: "CoreMusic — PIC Technology Governance Template"
type: template
category: template
version: 1.0.0
status: active
authority: "Template (Guardrail #16) — Registry: .ai/.templates/index.md"
updated: 2026-09-29
---

# CoreMusic — PIC Technology Governance Template

**Zorunlu Bağlantılar / See also:** [[.templates/index]] · [[../CLAUDE.md]] · [[../AGENTS.md]]

---

## 1. Amaç

Bu şablon, Microchip PIC (PIC16F, PIC18F, PIC24F/dsPIC serileri) mikrodenetleyicileri üzerinde bare-metal firmware geliştirme kurallarını tanımlar: bankalı bellek yönetimi, donanım yığını disiplini, XC8 araç zinciri, config word yapılandırması, EEPROM kalıcılık protokolü ve MISRA/IEC uyumluluğu. Eski spesifikasyon dosyası (`pic-template.md`, 2026-06-24, 1205 satır) yeniden yapılandırılmış; içerik silinmeden Guardrail #16 dış iskeletine taşınmıştır.

**Guardrail #16:** CoreMusic vault içinde PIC firmware dokümanı üretilirken bu şablon seçilmek ZORUNLUDUR.

| Platform | Değer |
|----------|-------|
| **Platform** | Microchip PIC (PIC16F, PIC18F, PIC24F/dsPIC) |
| **Araç zinciri** | XC8 v4.00 (2026) + MPLAB X IDE 6.35 — bkz. §7.3 |
| **Standart** | C99 (XC8 uzantılı) — bare-metal, işletim sistemi yok |
| **Mimari** | 8/16-bit Harvard, bankalı register dosyası, donanım yığını |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Bare-metal PIC firmware (TRIS/LAT/ANSEL, bank geçişi, config word) | AVR bare-metal → `[[hardware/avr-template]]` |
| XC8 CLI/MPLAB X derleme, PICkit/ICD ile programlama | Arduino framework'ü → `[[hardware/arduino-template]]` |
| Data EEPROM erişim protokolü (EECON2 kilitli yazma), wear leveling | Genel donanım tasarım dokümanı → `[[hardware/hardware-template]]` |
| PIC18F high/low kesme öncelikleri, test (MPLAB simülatör, Ceedling) | C99 dil standartları (genel) → `[[../other/c-template]]` |
| MISRA C:2025 alt kümesi, IEC 60730 Class B, FCC Part 15 | PIC24/dsPIC C++ (XC16) → `[[../other/cpp-template]]` ile ilgili |

- **Dosya tipi:** Markdown teknoloji yönetişim şablonu (`.templates/hardware/` altı).
- **Kullanan agent:** Embedded Engineer (sorumlu), DSP Firmware Engineer / Windows Software Engineer (ikincil).
- **Doğrulama zorunluluğu:** sürüm, lisans ve standart iddiaları §7'de web kaynaklarıyla doğrulanmıştır; doğrulanamayan her iddia `⚠️ VERIFICATION REQUIRED` taşır.

---

## 3. Mimari

### 3.1 Tek Katmanlı Bare-Metal Modeli (eski §1)

PIC mikrodenetleyicileri ayrı program ve veri veri yollarına sahip Harvard mimarisi kullanır. AVR'nin düz register dosyasının aksine, PIC16F/PIC18F cihazlarında RAM 128 baytlık (PIC18F için 256 bayt) birden çok banka bölünmüştür; bank sınırında değişken erişirken bank seçimi açıkça yönetilir.

**Yürütme modeli:**
```
Reset vector → Power-up timer → main()
  ├── oscillator_init()       — osilatör modu (HS, XT, INTOSC)
  ├── port_init()             — TRIS, LAT, ANSEL registerları
  ├── peripheral_init()       — TMR0/1, ADC, MSSP (SPI/I2C), EUSART
  ├── global_interrupt_enable() — ei() veya INTCONbits.GIE = 1
  └── [kesme tetiklenir]
       └── interrupt_service_routine()
            ├── context_save()     — WREG, STATUS, BSR (PIC18), FSR
            ├── handle_event()
            ├── context_restore()
            └── retfie()           — dön, kesmeleri yeniden aç
```

**Bank geçişi — PIC mimarisinin tanımlayıcı zorluğu:**

PIC16F'de register dosyası 4 bankaya bölünür (Bank 0-3, 128'er bayt). `BSR` (PIC18) veya `STATUS` içindeki `RP0:RP1` bitleri (PIC16) aktif bankayı seçer. XC8'in çoğunu `banksel` direktifiyle otomatik yapmasına rağmen geliştirici ne zaman gerçekleştiğini anlamalıdır:

```c
// XC8 şu assembly'yi üretir:
//    banksel myVar       ; myVar'ın bankasını BSR'a yükler
//    movff myVar, WREG   ; banka bağlamıyla taşıma
static unsigned char myVar;  // linker GPR bankasına yerleştirir
void example(void) { myVar = 0xFF;  /* XC8 BSR yüklemesini ekler */ }
```

**Bellek haritası (PIC18F2520):**
- Program belleği: 32 KB Flash (16-bit komut word'leri, 16.384 komut)
- Veri RAM: 1.536 bayt (bankalı: 12 banka × 128 bayt) + 256 baytlık erişim bankası (Bank 0-1'in alt adresleriyle çakışır)
- Donanım yığını: 31 seviye (PIC18F) — RAM'DE DEĞİL, veri belleğine taşamaz
- EEPROM: 256 bayt (PIC18F2520) — ayrı data EEPROM, RAM'e haritalanmaz

**Erişim bankası** (yalnız PIC18F) kritik bir optimizasyondur: Bank 0'ın en düşük 96 baytı (SFR alanı) + Bank 15'in en yüksek 160 baytı (GPR alanı) bank geçişi olmadan erişilebilir. Sık erişilen değişkenler buraya yerleştirilir:

```c
#pragma udata access
volatile unsigned char system_flags;  // erişim bankası — banksel yükü yok
#pragma udata
```

**SRAM disiplini — yığın (heap) yok:**
```
ERİŞİM BANKASI (0x000-0x05F SFR, 0xF60-0xFFF GPR)
  ├── SFR: PORTA, TRISA, TMR0L, ADCON0, INTCON...
  └── Hızlı GPR: sık kullanılan global, ISR geçicileri

BANK 0-11 (genel amaçlı RAM)
  ├── .udata (XC8 başlatılmamış veri bölümü)
  ├── .idata (başlatılmış veri — crt0 tarafından açılışta Flash'tan kopyalanır)
  └── yığın (PIC18: yazılım yığını ayrılmış banka içinde yüksekten alçağa büyür)
```

**PIC donanım yığını** ayrılmış bellekte 31 girişli bir LIFO'dur — SRAM'DE DEĞİLDIR. Yığın taşması koruması yoktur: 31 seviyenin ötesine itme sessizce sarılır, en derin çağrının dönüş adresini bozar. Config word'deki `STVREN` (Stack Overflow Reset Enable) biti taşmada sıfırlama sağlar ve üretimde ZORUNLUDUR.

### 3.2 Kesme Öncelik Mimarisi (PIC18F — eski §5)

PIC18F iki kesme öncelik seviyesi destekler: yüksek ve düşük. Yüksek öncelikli kesmelerin 0x0008'de, düşük önceliklilerin 0x0018'de ayrı vektörü vardır.

| Öncelik | Vektör | Kaynaklar | Kısıtlar |
|---------|--------|-----------|----------|
| Yüksek (1) | 0x0008 | TMR0 taşması, INT0 pini, PORTB değişimi | Düşük öncelikli ISR tarafından engellenemez. Ses zamanlaması için kullanılır. |
| Düşük (2) | 0x0018 | TMR1, ADC, EUSART RX/TX, MSSP | Yüksek öncelikli ISR tarafından kesilebilir. I/O ve veri aktarımı için. |

**PIC18F yüksek/düşük öncelikli ISR kalıbı:**
```c
#pragma code high_vector = 0x0008
void high_isr(void) { _asm goto high_isr_handler _endasm }
#pragma code

#pragma code low_vector = 0x0018
void low_isr(void) { _asm goto low_isr_handler _endasm }
#pragma code

#pragma interrupt high_isr_handler
void high_isr_handler(void) {
    if (INTCONbits.TMR0IF) { audio_tick(); INTCONbits.TMR0IF = 0; }  // 1 kHz ses tik
}

#pragma interruptlow low_isr_handler
void low_isr_handler(void) {
    if (PIR1bits.ADIF) { adc_flag = 1; PIR1bits.ADIF = 0; }
    if (PIR1bits.RCIF) { uart_receive_byte(); }
}
```

**Ana döngü kalıbı (uyan-olay):**
```c
void main(void) {
    oscillator_init(); ports_init(); peripherals_init();
    interrupts_init(); ei();
    while (1) {
        CLRWDT();
        if (adc_flag) { process_adc(ADRESH, ADRESL); adc_flag = 0; }
        if (uart_rx_count) handle_uart_command();
        SLEEP();  // etkin kesmeyle uyan
    }
}
```

### 3.3 Karar Kayıtları (eski §10)

**MCU Seçimi: PIC18F2520 vs PIC16F886**

| Kriter | PIC18F2520 | PIC16F886 |
|--------|------------|-----------|
| **Flash** | 32 KB (16.384 komut) | 14 KB (8.192 komut) |
| **Veri RAM** | 1.536 bayt (12 banka) | 368 bayt (4 banka) |
| **Donanım yığını** | 31 seviye | 8 seviye |
| **Zamanlayıcı** | 4 (4× 16-bit) | 3 (1× 16-bit + 2× 8-bit) |
| **ADC** | 10-bit, 13 kanal | 10-bit, **11 kanal** |
| **PPS (yeniden eşlenebilir pin)** | Evet (K-serisi) | Hayır |
| **Maliyet** | ~$3.00 (100 adet) ⚠️ VERIFICATION REQUIRED | ~$1.80 (100 adet) ⚠️ VERIFICATION REQUIRED |

> **Düzeltme (2026-09-29):** Eski dokümanda PIC16F886 ADC "14 kanal" olarak geçiyordu; datasheet doğrulaması **11 ADC kanalı** (AN0-AN10) gösterir. PIC18F2520'nin 13 kanal değeri doğrulanmıştır.

**Karar:**
- I2S ses DAC kontrolü (PCM5122): **PIC18F2520** — tamponlar için daha geniş RAM, 31 seviyelik yığın daha derin çağrıya izin verir, PPS PCB yerleşimini kolaylaştırır.
- Basit sensör/buton uygulamaları: **PIC16F886** — daha düşük maliyet, UART + ADC + I2C için yeterli, yönetilecek daha az banka.

**Saat Kaynağı: Dahili OSC vs Harici Kristal**

| Kaynak | Hassasiyet | Başlangıç | Güç | EMI |
|--------|------------|-----------|-----|-----|
| INTOSC (8 MHz, fabrika kalibre) | ±%2 (kalibre) | ~20 µs | Düşük | Düşük |
| HS Kristal (16 MHz) | ±50 ppm | ~4 ms | Orta | Daha yüksek |
| Harici Osilatör (20 MHz) | ±25 ppm | ~2 ms | Yüksek | En düşük |

**Karar:** Ses DAC'ı (PCM5122) hassas I2S bit saati için 16 MHz HS kristal gerektirir; INTOSC ±%2 duyulur perde kayması üretir. Yalnız UART uygulamaları INTOSC kullanabilir: ±%2 9600 baud toleransı içindedir ama 115200 için kritiktir (kalibrasyon veya kristal gerekir).

---

## 4. Kurallar

### 4.1 Hard Guardrails

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | `malloc()/calloc()` YASAK — XC8 reddeder, heap yok | Derleme hatası / tanımsız davranış |
| 2 | Özyineleme YASAK — donanım yığını sınırlı (31/8 seviye) | Dönüş adresi bozulması |
| 3 | Banka adresli fonksiyon işaretçileri YASAK | Pointer boyutu / adres uzayı uyumsuzluğu |
| 4 | `setjmp()/longjmp()` YASAK | Donanım yığını ve BSR bozulur |
| 5 | `alloca()` YASAK — XC8 desteklemez | Derleme hatası |
| 6 | `#pragma config` hedef gerilim/saat/üretim ile eşleşmeli | Cihaz çalışmaz (§6.6) |
| 7 | `STVREN = ON` üretimde zorunlu | Yığın taşması sessizce bozar |
| 8 | EEPROM yazma EECON2 kilit dizisi (0x55→0xAA) tam ve kesintisiz | Yazma gerçekleşmez (§6.6) |
| 9 | `CP = ON` geri alınamaz — toplu silme tüm Flash'ı siler | Kalıcı kod koruma kilidi |

### 4.2 Dil ve Araç Zinciri Standartları (eski §2)

| Gereklilik | Spesifikasyon |
|------------|---------------|
| **Derleyici** | XC8 v4.00 (ücretsiz mod veya PRO) — sürüm bkz. §7.3 |
| **Dil** | XC8 uzantılı C99 (`@` mutlak adres, `#pragma config`, `interrupt` anahtar kelimesi) |
| **Linker** | MPLAB XC8 linker, cihaza özel linker script'leri |
| **Binary** | Intel HEX (.hex) veya .cof (MPLAB COFF hata ayıklama) |
| **Programlama** | PICkit 5 / PICkit 4 / PICkit Basic / ICD 5 / MPLAB Snap / ICE 4 |
| **IDE** | MPLAB X IDE 6.35 (isteğe bağlı; CLI `xc8 --chip=18F2520` ile de derlenir) |

**Zorunlu başlık kalıbı:**
```c
#include <xc.h>              // cihaza özel başlık (SFR bit tanımları dahil)
#include <stdint.h>          // uint8_t, uint16_t, uint32_t
#include <stdbool.h>         // bool, true, false
#include <string.h>          // memcpy (PIC — donanım optimize sürümü)

// Yapılandırma bitleri — main() ÖNCESİNDE ayarlanmalı
#pragma config OSC = HS      // Yüksek hızlı kristal
#pragma config WDT = ON      // Watchdog (üretimde donanım kontrollü)
#pragma config LVP = OFF     // Düşük voltajlı programlama kapalı (RB3 GPIO olur)
#pragma config BOR = ON      // Brown-out reset etkin
#pragma config BORV = 27     // BOD gerilimi 2.7V
#pragma config PBADEN = OFF  // PORTB analog fonksiyonu reset'te kapalı
#pragma config STVREN = ON   // Yığın taşması reset (güvenilirlik için kritik)
```

**Adlandırma:**
```c
LATAbits.LATA0 = 1;     // SFR bit adları: <xc.h>'ten, datasheet ile birebir
TMR0IE = 1;             // Timer0 kesme etkinleştirme
ADCON0bits.GO = 1;      // ADC dönüşümü başlat

void tmr0_init(uint8_t prescaler);   // Fonksiyonlar: snake_case + alt sistem öneki
uint16_t adc_read(uint8_t channel);

#define LED_PIN    LATBbits.LATB7    // Makrolar: UPPER_SNAKE_CASE
#define BUTTON_PIN PORTBbits.RB0
```

### 4.3 Güvenlik Kuralları (eski §3)

**Watchdog Timer (WDT):** Sistem saatinden bağımsız, ~31 kHz'lik ayrı RC osilatörden beslenir; `WDTE = ON` yapılandırma biti ayarlıyken yazılımla kapatılamaz.

```c
#pragma config WDTE = ON      // WDT donanım kontrollü, yazılımsal değil
#pragma config WDTPS = 64     // 1:64 prescaler, ~2.3 sn zaman aşımı (VDD = 5V)

void main(void) {
    CLRWDT();                 // WDT dönemi içinde çağrılmalı
    while (1) { CLRWDT(); process_events(); SLEEP(); }
}
```

**Kritik kural:** `CLRWDT()` WDT dönemi boyunca HER kod yolunda en az bir kez çağrılmalıdır. Koşul bloğu `CLRWDT()` içermiyorsa zaman aşımını tetikleyen bir yol vardır:

```c
// ❌ YANLIŞ — dal CLRWDT()'yi atlar:
if (buffer_ready) { process_buffer(); CLRWDT(); }
else { /* CLRWDT yok — watchdog sıfırlar! */ }

// ✅ DOĞRU — CLRWDT() koşuldan önce:
CLRWDT();
if (buffer_ready) process_buffer();
```

**Brown-Out Reset (BOR):**
```c
#pragma config BOR = ON       // BOR etkin
#pragma config BORV = 27      // 2.7V tetik noktası (beslemeye göre ayarla)
#pragma config PBADEN = OFF   // BOR çıkışında PORTB analog fonksiyonu kapalı

if (RCONbits.BOR) {           // brownout oldu — durumu EEPROM'dan geri yükle
    RCONbits.BOR = 0;         // okuduktan sonra temizle
}
```

**Config Word / Kilit Koruması:**
```c
#pragma config CP = ON        // Kod koruması: Flash harici okumayı engeller
#pragma config CPD = ON       // Data EEPROM kod koruması
#pragma config WRT = OFF      // Yazma koruması kapalı (bootloader bunu kullanır)
#pragma config DEBUG = OFF    // Arka plan hata ayıklayıcı kapalı (üretim)
```
`CP = ON` programlandığında cihaz programlama arayüzünden geri okunamaz; bu toplu silme (tüm Flash'ı siler) dışında **geri alınamaz**.

**SRAM Sınır Kontrolü:** PIC cihazlarında bellek koruma biçimi yoktur; buffer taşmaları komşu bankalı değişkenleri sessizce bozar. Derleme zamanı sınırlaması `@ 0x200` sabit adres + `#define RX_BUFFER_SIZE 32`; ayıklama baskılarında çalışma zamanı kontrolü (`index >= RX_BUFFER_SIZE` → hata göstergesi + dur).

**Yığın Taşması Önleme:** Donanım yığını (PIC18F'de 31 seviye) `STVREN` etkin değilse taşmada sessizce sarılır:
```c
#pragma config STVREN = ON   // Yığın taşması reset (ZORUNLU)
// Derleme zamanı izleme: xc8 --chip=18F2520 main.c -mlong-calls -mstack-usage
// .map dosyası: "Maximum call depth: 12 (safe, limit 31)"
```

### 4.4 Data EEPROM Kuralları (eski §4)

| Parametre | Değer |
|-----------|-------|
| **Ömür** | Hücre başına 1.000.000 yazma çevrimi (PIC18F serisi) |
| **Silme/yazma süresi** | ~4 ms (tek word, dahili zamanlayıcı kontrollü) |
| **Okuma süresi** | Bir komut çevrimi (`EECON1` registerı üzerinden tek bayt) |
| **Toplam boyut** | 256 bayt (PIC18F2520) – 1 KB (PIC18F4550) |

AVR'nin aksine PIC data EEPROM'u bellek haritalı değildir; `EECON1`, `EECON2`, `EEDATA`, `EEADR` registerlarıyla erişilir ve yazma dizisi kilitli açma içerir (0x55 → 0xAA anahtar dizisi, araya NOP/branch GİRMEZ, GIE kapatılır).

**Wear leveling:** 1M çevrim ömrüne rağmen sık yazılan değerler (yapılandırma değişiklikleri, ses seviyesi) aşınma dengeleme gerektirir — 64 baytlık havuz + son EEPROM baytında dönen indeks (eski §4 kodu korunmuştur).

**CRC-16 açılış doğrulama:** EEPROM'da saklanan HER yapılandırma bloğu CRC-16-CCITT (poly `0x1021`, başlangıç `0xFFFF`) ile açılışta doğrulanır; uyuşmazlıkta varsayılanlara dönülür.

### 4.5 Kod Kalitesi Kuralları (eski §7)

**Doğrudan Register Erişimi — soyutlama yok:**
```c
// ✅ DOĞRU — datasheet ile eşleşen bitfield erişimi
LATAbits.LATA0 = 1;
TRISCbits.TRISC7 = 1;    // RC7 giriş (UART RX)

// ❌ YANLIŞ — sihirli sayı
PORTA |= 0x01;           // okuyucu bit 0 anlamını aramak zorunda
```

**`@` Mutlak Adres Direktifi:** XC8, C99'u `@` operatörüyle genişletir; değişkeni sabit adrese yerleştirir. Paylaşımlı bellek kalıpları ve bootloader imza alanları için kritiktir:
```c
volatile unsigned char shared_flag @ 0x200;   // Bank 2, bayt 0
uint8_t boot_signature[4] @ 0x1FF0;
static uint8_t audio_buffer[256] @ 0x300;     // Bank 3
```

**`#pragma udata access` ile banka yerleşimi:** Sık erişilen değişkenler erişim bankasına yerleştirilir; `banksel` bayt başı bir çevrim yükünü önler:
```c
#pragma udata access
volatile uint8_t system_tick;
volatile uint8_t event_flags;
#pragma udata
```

**PPS (Peripheral Pin Select) — geliştirilmiş PIC:** PIC18F K-serisi ve daha yenileri dijital peripheral'leri müsait herhangi bir pine yeniden eşler; pin atamaları değiştiğinde PCB revizyonu önlenir:
```c
void uart_remap(void) {
    RPINR18bits.U1RXR = 0x04;  // U1RX girdisi RP4 (RB4)
    RPOR2bits.RP5R = 0x0002;   // RP5 -> U1TX (RB5)
}
```

**`malloc()` YOK — yalnız Mutlak Yerleşim:** PIC cihazlarında heap ve MMU yoktur; bütün değişkenler linker tarafından statik veya `@` ile mutlak adreste yerleştirilir.

### 4.6 Hata Yönetimi (eski §13)

Hata işaretçileri erişim bankasındaki bitfield yapısında tutulur (hızlı ISR erişimi):
```c
#pragma udata access
volatile struct {
    unsigned WDT_TO : 1;     // Watchdog zaman aşımı
    unsigned BOR_RESET : 1;  // Brown-out sıfırlaması oldu
    unsigned ADC_SAT : 1;    // ADC doygun (Vref+ seviyesi)
    unsigned EEP_CRC  : 1;   // EEPROM CRC uyuşmazlığı
    unsigned UART_OVF : 1;   // UART tampon taşması
    unsigned I2C_NACK : 1;   // I2C NACK alındı
    unsigned TIMER_DRIFT : 1;// Zamanlayıcı son tarihi kaçırdı
    unsigned STACK_ERR : 1;  // Yığın taşması yakını uyarısı
} error_flags;
#pragma udata
```

- **LED blink kodlaması:** Durum LED'i olan cihazlarda en yüksek 4 hata biti blink deseniyle kodlanır (0x01 WDT, 0x02 EEPROM CRC, 0x04 ADC, 0x08 I2C NACK).
- **Watchdog zaman aşımı tespiti:** Açılışta `RCONbits.WDTO` ve `RCONbits.BOR` okunur, işaret temizlenir.

---

## 5. Workflow

### 5.1 Datasheet Bölümleri — Zorunlu Okuma (eski §9)

Her PIC geliştiricisi cihaza ÖZEL datasheet'in ve aile referans kılavuzunun şu bölümlerine başvurur:

| Bölüm | Konu | Kritik Noktalar |
|-------|------|-----------------|
| **1.0** | Cihaz Genel Bakış | Bellek haritaları, pin diyagramları, peripheral listesi |
| **3.0** | Bellek Organizasyonu | Banka haritası, erişim bankası aralığı, donanım yığını derinliği |
| **4.0** | Reset | POR, BOR, WDT reset, MCLR, config word bitleri |
| **9.0** | I/O Portları | TRIS/LAT/PORT davranışı, open-drain, analog seçim (ANSEL) |
| **11.0** | Timer0 | 8/16-bit, prescaler ataması, kenar seçimi |
| **12.0** | Timer1 | 16-bit, gate, osilatör, LP modu |
| **17.0** | MSSP | SPI ve I2C modları, saat hızı hesabı, tampon yönetimi |
| **19.0** | ADC | Kazanç süresi, dönüşüm saati, referans seçimi |
| **21.0** | EUSART | Baud üreteci, otomatik baud, IRDA kodlama |
| **24.0** | Özel Özellikler | Config word'ler, ID konumları, kod koruması |
| **25.0** | Elektriksel Özellikler | DC karakteristikler, zamanlama, ADC hassasiyeti, osilatör toleransı |

### 5.2 Config Word Dokümantasyonu (eski §9)

Her config word bir başlık yorum bloğunda belgelenir:
```c
// CONFIG1H (0x300001) — osilatör ve güç yapılandırması
// Bit 7: OSCSEN = 0 (osilatör anahtarlama kapalı)
// Bit 6-4: FOSC3:FOSC1 = 110 (HS osilatör — 8-16 MHz kristal)
// Bit 3: PLLCFG = 0 (PLL kapalı)
// Bit 2-0: FCMEN = 0 (fail-safe saat izleyici kapalı)

// CONFIG2H (0x300003) — watchdog ve güç açılma
// Bit 7-4: WDTPS3:WDTPS0 = 0101 (1:64 prescaler, ~2.3s)
// Bit 3: WDTEN = 0 (yalnız config biti — yazılım kontrolü yok)

// Üretim hex değerleri:
// CONFIG1 = 0xFF0F, CONFIG2 = 0xFFFF, CONFIG3 = 0xFFFF
```

### 5.3 Kod İncelemesi Zorunluluğu

Bütün PIC firmware'leri gömülü sistemler mühendisi tarafından incelenir. Kontrol listesi:

1. `#pragma config` hedef gerilim, saat kaynağı ve üretim gereksinimleriyle eşleşiyor
2. Bank geçişi yükü değerlendirilmiş — kritik ISRs erişim bankasına yerleştirilmiş
3. Donanım yığını derinliği doğrulanmış (maks. çağrı derinliği < 31)
4. ADC kazanç süresi minimum TAD gereksinimini karşılıyor
5. EEPROM yazma dizisi kilitli açma protokolünü tam izliyor
6. Yüksek/düşük ISR arasında ortak SFR'lerde çift erişim çakışması yok

### 5.4 Derleme Hattı: XC8 CLI + PICkit (eski §11)

```makefile
# PIC18F2520 firmware dağıtım Makefile'ı
CHIP     = 18F2520
TARGET   = firmware
SRC      = main.c timer.c adc.c uart.c i2c.c
CC       = xc8
CFLAGS   = --chip=$(CHIP) --mode=pro -O2 -Wall -Werror
CFLAGS  += --std=c99 --stack=32 --mlong-calls
HEX      = $(TARGET).hex
MAP      = $(TARGET).map

all: $(HEX) size
$(HEX): $(SRC)
	$(CC) $(CFLAGS) $(SRC) --output=+hex -o$(TARGET)
size: $(HEX)
	@echo "=== Memory Usage ==="
	@xc8 --chip=$(CHIP) $(SRC) --summary=memory 2>&1 | tail -20
flash: $(HEX)
	pk4cmd -P$(CHIP) -F$(HEX) -M -R
# PICkit 4/5 + MPLAB X IPE CLI gerektirir
# Kurulum yolu: C:\Program Files\Microchip\MPLABX\v6.35\mplab_ipe\bin
clean:
	rm -f *.hex *.map *.elf *.o *.obj *.d
.PHONY: all size flash clean
```

### 5.5 CI Derleme Kontrolü (GitHub Actions — eski §11)

```yaml
name: pic-compile-check
on: [push]
jobs:
  build:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Install XC8
        run: |
          wget -q https://ww1.microchip.com/downloads/xc8/v4.00/XC8-v4.00-linux-installer.run
          chmod +x XC8-v4.00-linux-installer.run
          sudo ./XC8-v4.00-linux-installer.run --mode unattended --install_dir /opt/microchip/xc8
      - name: Build
        run: |
          export PATH=$PATH:/opt/microchip/xc8/v4.00/bin
          make
      - name: Check flash usage
        run: |
          USAGE=$(grep "Program Memory" firmware.map | awk '{print $5}' | tr -d '%')
          if (( $(echo "$USAGE > 85" | bc -l) )); then
            echo "FAIL: Flash usage $USAGE% exceeds 85% limit"
            exit 1
          fi
```
> ⚠️ VERIFICATION REQUIRED: XC8 v4.00 Linux indirme URL'si yayın anında Microchip sitesinden teyit edilmelidir (eski `XC8-3.00-linux.run` biçiminden güncellendi).

### 5.6 Yükseltme / Geçiş (eski §16)

| Kaynak | Hedef | Gereken Değişiklikler |
|--------|-------|-----------------------|
| PIC16F886 (14 KB Flash, 8 seviye yığın) | PIC18F2520 (32 KB Flash, 31 seviye) | Yeni register adları (`T0IF` → `TMR0IF`), `#pragma config` adresleri, `--chip=` bayrağı, banka→erişim bankası modeli için BSR yönetimi |
| PIC18F2520 | PIC18F46K22 (64 KB Flash, PPS) | PPS yapılandırması eklenir. Register düzeni çoğunlukla uyumlu. Yeni PPS registerları başlatılmalı. |
| PIC18F | PIC24F (16-bit) | Büyük mimari değişim — 16-bit veri yolu, bank geçişi yok, DMA, farklı komut seti. Tam yeniden yazım. Artımlı değil. |

**Geçiş kontrol listesi:** 1) `#pragma config` satırlarını değiştir (her ailenin farklı config adresi var) · 2) register adlarını güncelle (`INTCONbits.T0IF` → `INTCONbits.TMR0IF`) · 3) yığın derinliğini denetle (PIC16 8 seviye — düzleştirme gerekir) · 4) banka yerleşimi (PIC16: 4×128 bayt; PIC18: 12+ banka + erişim bankası) · 5) linker script (XC8 `--chip=` ile otomatik seçer).

| Kaynak | Hedef | Etki |
|--------|-------|------|
| XC8 1.x | XC8 4.00 | Ücretsiz mod artık **O2+ tam optimizasyon içerir** (2026-07-08 lisans değişikliği — §7.3). Eski ücretsiz mod O0 ile performans düşüktü. `--chip=` veritabanı yeni PIC ailelerini kapsar. |
| MPLAB X 5.x | MPLAB X 6.35 | IPE CLI komut değişiklikleri (`pk4cmd` sözdizimi). Proje dosyası biçimi uyumlu. |
| PICkit 3 | PICkit 5 / MPLAB Snap / PICkit Basic | Daha hızlı programlama (100 kbps → ~1 Mbps). PICkit 3/ICD 3/REAL ICE **yalnız MPLAB X 6.20'ye kadar** desteklenir; 6.20+ için PICkit 5/ICD 5/ICE 4 önerilir (§7.3). |

---

## 6. Doğrulama

### 6.1 Simülasyon: MPLAB X Simülatörü (eski §6)

MPLAB X, donanım olmadan kırılma noktası, uyaran enjeksiyonu ve zamanlama analizi destekleyen komut-hassas bir simülatör içerir:

```bash
# MPLAB X CLI üzerinden:
mplab_platform/bin/mplab_xc8_sim --chip=18F2520 --pcl=4 \
    --stimulus="file=adc_stimulus.txt:pin=AN0" firmware.hex
```

**Uyanaran dosyaları** belirli komut sayılarında ADC değerleri, UART baytları ve pin geçişleri enjekte eder:
```
# adc_stimulus.txt — 1000. komutta AN0'a 512 mV enjekte et
1000 A  0.512   2.5 255
2000 A  0.618   2.5 255
3000 A  0.450   2.5 255
```

### 6.2 Birim Testleri: Ceedling + CMock (eski §6)

XC8 ile derlenen kod bank seçim talimatları ve mimariye özgü SFR bit yapıları içerir; birim testler host gcc ile sahtelenmiş donanım registerlarında çalışır:

```c
// test/test_timer.c
#include "unity.h"
#include "mock_pic_hardware.h"

void setUp(void) { INTCON = 0; TMR0IF = 0; }

void test_timer_overflow_sets_flag(void) {
    INTCON = 0x20;      // TMR0IF = 1 (INTCON'da 0x20)
    TMR0IF = 1;         // CMock enjeksiyona göre bunu ayarlar
    timer_tick_handler();
    TEST_ASSERT_EQUAL(1, timer_tick_count);
}
```

### 6.3 HITL: PICkit 4 Lojik + Osiloskop (eski §6)

PICkit 4 temel lojik analizör modu içerir (en fazla 4 kanal, 20 MHz örnek hızı):
```bash
# MPLAB X IPE üzerinden lojik analizör yakalama
# PORTA bit 0 yükselen kenarında tetikle
pk4cmd -T -P18F2520 -L -TRIG PORTA0:RISE -S 1000000 -C 4
```

### 6.4 Kapsam Hedefleri

| Katman | Host Derlemeli | Hedefde | Sıklık |
|--------|----------------|---------|--------|
| Uygulama mantığı | %90+ (Unity) | — | Her commit |
| SFR register yazımları | %70+ (mock) | %100 scope yakalama | Her sürüm |
| ISR işleyicileri | %60+ (simülasyon) | %100 zamanlama doğrulandı | Her sürüm |
| EEPROM aşınma dengeleme | %95+ (Unity) | — | Her commit |

### 6.5 Performans Bütçeleri (eski §12)

| Metrik | Hedef | Yöntem |
|--------|-------|--------|
| **ISR gecikmesi (yüksek öncelik)** | <650 ns (bayttan ilk komuta) | INT0 pininde scope → ISR'de PORTB anahtarlaması |
| **ISR titremesi** | 16 MHz'de <30 ns | ISR başlangıç sinyalinin ardışık yükselen kenarları |
| **Ana döngü iterasyonu** | <5 ms (bütün bloklamayan yollar) | Döngü girişinde RA4'i anahtarla, lojik analizörle yakala |
| **ADC dönüşüm süresi** | <125 µs (14 TAD, 1.6 µs/TAD) | GO→!GO geçişini ölç |
| **Timer0 çözünürlüğü** | 1 µs (Fosc/4 = 4 MHz, 1:1) | TMR0L taşma değeri |
| **I2C saati (SSP)** | 100 kHz (standart) / 400 kHz (hızlı) | 16 MHz'de 100 kHz için SSPADD = 99 |
| **EUSART baud hata oranı** | <%2 (mutlak tolerans) | 16 MHz'de 9600 için UBRG = 25 (−%1,36 hata) |
| **Data EEPROM yazma** | bayt başına ~4 ms | Kendi kendine zamanlı, WR biti yoklamasıyla |

**Kesme gecikmesi dökümü (PIC18F):** mevcut komutu tamamla (çoğu 1, dallanma 2 çevrim) → donanım dönüş adresini yığına kaydet (1) → 0x0008/0x0018 vektörüne dallan (donanım jumper'ı, sıfır çevrim) → vektörde GOTO (2) → bağlam kaydetmeye başla: WREG gölge registera (PIC18F donanım — sıfır çevrim, eski PIC'lerin aksine) → BSR, STATUS, FSR yazılımda (4-8 çevrim) = **toplam 8-14 çevrim = 40 MHz'de 200-350 ns** (kullanıcı ISR kodundan önce). AVR'nin aksine PIC18F donanımı WREG'ı otomatik gölge registera kaydeder.

**Bellek ayak izi hedefleri:**

| Bileşen | Flash | Veri RAM |
|---------|-------|----------|
| Ana döngü + init | 2.5 KB | — |
| Timer0 ISR (1 ms tik) | 128 B | 4 B (taşma sayacı) |
| ADC + çok kanal | 1 KB | 8 B (sonuç tamponu) |
| UART TX/RX ring tamponları | 1.5 KB | 192 B (64 baytlık ring) |
| I2C MSSP master | 1.2 KB | 16 B |
| EEPROM aşınma dengeleme | 1.8 KB | 12 B |
| **Toplam (bütçe)** | **8.1 KB (%25)** | **232 B (%15)** |

### 6.6 Sorun Giderme (eski §15)

**Config Word Yanlış — Cihaz Başlamıyor:** PICkit cihazı tanıyor ama kod çalışmıyorsa `#pragma config` uyumsuzdur — osilatör XT/HS'e ayarlı ama kristal bağlı değil; veya `MCLRE` kapalıyken MCLR pini alçakta tutuluyor. Teşhis: 1) OSCON kaydını oku (osilatör stabil mi) · 2) OSC1 pinini osiloskopla ölç (sine = kristal, kare = harici saat) · 3) config word'ü geri oku: `pk4cmd -P18F2520 -R CONFIG`. Düzeltme (kristalsiz): `#pragma config FOSC = INTIO7` + `IESO = OFF`.

**Bank Geçişi Performans Sorunları:** Kod derleniyor ama beklenenden yavaş, kritik ISR zamanlayıcıları aşıyorsa XC8 sık erişilen değişkeni erişim bankasına yerleştirmemiştir (her erişim `banksel` + `movff` = 2 ek çevrim). Teşhis: `xc8 --chip=18F2520 main.c --ASMLIST -o main.lst` + `grep -c "banksel" main.lst`. Düzeltme: `#pragma udata access` ile kritik değişkenleri erişim bankasına zorla.

**EEPROM Yazması Kalıcı Olmuyor:** EECON2 anahtar dizisi (0x55, 0xAA) doğru yürütülmediyse veya yazma penceresinde kesmeler açma sırasını bozduysa veri kaybolur. Düzeltme: kesmeler yazma sırasında kapalı, 0x55 → 0xAA arka arkaya (araya NOP/dallanma yok):
```c
void eeprom_safe_write(uint8_t addr, uint8_t data) {
    while (EECON1bits.WR);
    EEADR = addr; EEDATA = data;
    EECON1bits.EEPGD = 0; EECON1bits.CFGS = 0; EECON1bits.WREN = 1;
    INTCONbits.GIE = 0;          // anahtarlı yazma sırasında kesme yok
    EECON2 = 0x55;               // arka arkaya olmalı
    EECON2 = 0xAA;
    EECON1bits.WR = 1;           // yazmayı başlat (donanım biti temizler)
    INTCONbits.GIE = 1;
    EECON1bits.WREN = 0;
}
```

**Donanım Yığını Taşması:** `STVREN` etkinken cihaz tahminsiz sıfırlanıyorsa veya CALL/RETURN çiftleri bozulmuşsa çağrı derinliği 31'ı (PIC18F) veya 8'i (PIC16F) aşıyordur — özyineleme veya derin iç içe ISR. Teşhis: `xc8 --chip=18F2520 --stack-usage main.c` + `.map` dosyasında "Maximum". Düzeltme: derin çağrı ağaçlarını düzleştir. Yalnız 8 seviyesi olan PIC16F'de ISR'lar yalnız bir seviye derinliğinde CALL yapabilir.

### 6.7 Kabul Kontrol Listesi

- [ ] Frontmatter 7 alan tam
- [ ] H1 + §1-§7 sırası sabit
- [ ] `{{PLACEHOLDER}}` kalmadı
- [ ] Wiki-link'ler `.md` uzantısız, düzeltilmiş yollarla
- [ ] Araç zinciri sürümleri §7.3 ile eşleşiyor (XC8 4.00, MPLAB X 6.35)
- [ ] ADC kanal sayısı 11 (PIC16F886) olarak düzeltildi
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
| AVR şablonu | [[hardware/avr-template]] | Alternatif MCU platformu (avr-gcc, düz register dosyası) |
| Arduino şablonu | [[hardware/arduino-template]] | Arduino framework'ü (AVR tabanlı) |
| C şablonu | [[../other/c-template]] | Temel C99 dil standartları |
| C++ şablonu | [[../other/cpp-template]] | 8-bit PIC'te C++ yok; PIC24/dsPIC XC16 için referans |
| ADR Audio | [[../adr/adr-audio-template]] | Ses donanımı mimari kararları |

### 7.2 PIC vs AVR Seçimi (eski §18)

- **PIC** ultra düşük güç batarya uygulamaları için tercih edilir (nanoWatt XLP teknolojisi, <50 nA uyku akımı).
- **AVR** ses DSP için tercih edilir (daha iyi avr-gcc optimizasyonu, daha büyük açık kaynak kütüphane ekosistemi).
- İkisi de I2C sensör ağları ve UART kontrol için uygundur; karar mevcut ekip araç zinciri deneyimine ve özel peripheral gereksinimlerine göre verilir (yeni PIC'te PPS, AVR'de olay sistemi).

### 7.3 Doğrulanmış Araç Zinciri ve Donanım Sürümleri (web doğrulaması — 2026-09-29)

| Bileşen | Doğrulanmış değer | Kaynak |
|---------|-------------------|--------|
| **MPLAB XC8** | **v4.00 (2026)** | microchip.com/en-us/tools-tools/compilers/mplab-xc8 |
| **MPLAB X IDE** | **6.35 (24 Temmuz 2026)** | microchip.com/en-us/tools-tools/ides-and-ide-add-ons/mplab-x-ide |
| **XC8 lisansı** | **2026-07-08'den itibaren BÜTÜN MPLAB XC kompilatör lisansları ücretsiz** (PRO optimizasyonları dahil; ücretsiz mod artık O2+ sağlar) | Microchip lisans duyurusu |
| **Desteklenen programlayıcılar** | Önerilen: **PICkit 5, ICD 5, ICE 4**; güncel: PICkit 4, MPLAB Snap, PICkit Basic | Microchip developer docs |
| **Eski programlayıcılar** | PICkit 3 / ICD 3 / REAL ICE yalnız **MPLAB X 6.20'ye kadar** desteklenir (6.20 son sürüm) | Microchip release notes |
| **PIC18F2520** | 32 KB Flash / 1.536 bayt RAM / 256 bayt EEPROM / 31 seviye yığın / 13 kanal ADC | Üretici datasheet |
| **PIC16F886** | 14 KB Flash / 368 bayt RAM / 256 bayt EEPROM / 3 zamanlayıcı / **11 kanal ADC** / 8 seviye yığın | Üretici datasheet |

> Eski dokümandaki `XC8 3.x`, `MPLAB X 6.x (6.20)`, `Pickit 4/5`, `PIC16F886 14 kanal ADC` değerleri bu tabloyla güncellenmiştir. Eski "PRO modu lisans gerektirir" ifadesi 2026-07-08 lisans değişikliğinden önce geçerliydi — artık tüm XC kompilatör lisansları ücretsizdir.

### 7.4 Doğrulanmış Standartlar (web doğrulaması — 2026-09-29)

| Standart | Doğrulanmış durum | Kaynak |
|----------|-------------------|--------|
| **MISRA C:2025** | Mart 2025'te yayımlandı — **225 aktif kural** (MISRA C:2023: Nisan 2023, 221 kural) | misra.org.uk |
| **IEC 60730 Class B** | Gömülü ev aleti güvenlik testleri | IEC 60730-1 |
| **FCC Part 15** | 1,7 MHz üzeri saat üreten cihazlar | eCFR 47 CFR Part 15 |

**MISRA C:2025 alt kümesi (PIC uygulaması — eski §19):**

| Kural | Açıklama | PIC Uygulaması |
|-------|----------|----------------|
| **Dir 4.1** | Çalışma zamanı hataları en azaltılmalı | Watchdog, BOR, STVREN, açılışta CRC doğrulama — hepsi zorunlu |
| **Rule 8.2** | Fonksiyon tipleri prototip kapsamında | Bütün fonksiyonlar kullanımdan önce prototiplenmeli; XC8 `-Wall -Werror` zorlar |
| **Rule 12.1** | Dinamik yığın ayırma yok | XC8 `malloc()` desteklemez; `nm firmware.elf \| grep malloc` boş döner |
| **Rule 14.3** | Kontrol ifadeleri özünde Boolean | `<stdbool.h>`'dan `bool`; `if (flags)` yerine `if (flags != 0)` |
| **Rule 16.7** | Pointer parametresi null olabilir | Sürücü fonksiyonlardaki tüm pointer parametreleri `NULL` kontrolünden geçer; PIC'te null erişim 0x0000'ı (SFR alanı) okur |
| **Rule 17.2** | Özyineleme yok | Çağrı grafiği döngüsüz; PIC16F 8 seviyelik yığını özyinelemeyi felç eder |
| **Rule 18.4** | Union ile temsil yeniden yorumlanmaz | Tür cezbesi yok; uç dönüşüm için açık cast veya `memcpy` |
| **Rule 21.7** | `setjmp`/`longjmp` yok | Donanım dönüş yığınını bozar — yedek mekanizma yok |

**IEC 60730 Class B uyumluluğu:** 1) CPU register testi (WREG'a 0x55/0xAA yaz, geri oku; STATUS ve BSR) · 2) SRAM testi (soğuk açılışta tüm dolu bankalarda March C) · 3) Flash CRC (açılışta tüm Flash üzerinde CRC-16, config word'de saklanan değerle karşılaştırma) · 4) Saat arızası tespiti (destekliyorsa FCMEN etkinleştir — OSC1'i izler, arızada INTOSC'a geçer) · 5) Kesme izleme (TMR0 kesmesi beklenen pencerede yoksa hata bayrağı; 1,5× periyot) · 6) Yığın testi (PIC18F'de 30 seviye iten sahte fonksiyon, STVREN tetiklememeli).

**FCC Part 15 (1,7 MHz üzeri saat):** 1) Yayılım spektrumu — PIC18F'de SSCG yok, harici SSCG osilatörü kullan · 2) Baypas: her VDD/VSS çiftine 100 nF seramik (PIC18F2520'de min 2 kapasitör) + güç girişine 10 µF; kapasitörler pinlerden <5 mm · 3) PCB: yüksek frekanslı izler (OSC1/OSC2, SCL, SDA, TX) 30 mm altında, osilatör devresi altında toprak düzlemi · 4) Yükseliş süresi kontrolü: `SLRCONbits.SLRCONB = 0xFF;` (PORTB yavaş kayma).

### 7.5 Sürüm Kaydı

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 1.0 | 2026-06-24 | İlk PIC şablonu (eski spec, 1205 satır, `.templates/pic-template.md`) |
| 1.0.0 | 2026-09-29 | Guardrail #16 iskeletine yeniden düzenlendi; web doğrulaması (XC8 v4.00, MPLAB X 6.35, ücretsiz XC lisansları 2026-07-08, PICkit 5/ICD 5/ICE 4 önerisi, PICkit 3 son MPLAB X 6.20); PIC16F886 ADC 11 kanal olarak düzeltildi; wiki-link yolları düzeltildi |

---

**Template Version:** 1.0.0
**Last Updated:** 2026-09-29
