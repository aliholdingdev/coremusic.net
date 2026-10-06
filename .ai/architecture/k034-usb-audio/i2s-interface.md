---
title: "I2S Arayüzü - k034-usb-audio"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# I2S Arayüzü

> Klasör: `k034-usb-audio` · Dilim: D01 (k018–k035) · Dosya: `i2s-interface.md`
> Sorumlu persona: `dsp-firmware-engineer` (DSP Firmware Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` · `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

I2S sinyalleri, timing, master/slave ve çoklu kanal yapılandırması.

Bu belge; D01 diliminin (USB Ses) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** I2S sinyalleri, timing, master/slave ve çoklu kanal yapılandırması.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (USB Ses)
- **Çapraz referanslar:** [[../k018-dma-kesinti-yonetimi/dma-yonetimi.md]] · [[../k022-rpi5-core/rpi5-pwm-gpio-audio.md]] · [[../k021-macos-core/core-audio-macos.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

### i2s-interface.md — `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` (186 satır)

#### i2s-interface.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` § (giriş) — L1–L9

---
title: "I2S Bus Protocol"
layer: K1
category: "Dijital Ses Protokolü"
date: 2026-09-20
---

### I2S Bus Protocol


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` § `Genel Bakış` — L10–L12


I2S (Inter-IC Sound), dijital ses verilerini IC'ler arasında aktarmak için geliştirilmiş seri bir haberleşme protokolüdür. Philips Standard formatında çalışır. XMOS XU316'dan DAC (AK4458) ve ADC'ye (PCM3168A) yüksek çözünürlüklü ses verisi taşır.

#### Teknik Spesifikasyonlar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` § `Teknik Spesifikasyonlar` — L14–L26


| Parametre | Değer |
|-----------|-------|
| Standart | Philips I2S (Original) |
| Kanal Sayısı | 8 stereo (16 single) |
| Bit Çözünürlüğü | 32-bit |
| Örnekleme Hızı | 44.1kHz – 192kHz |
| Master Clock | 256fs (11.2896MHz @ 44.1kHz) |
| Bit Clock | 64fs × 32-bit = 2.1168MHz @ 44.1kHz |
| Word Select | fs = 44.1kHz / 48kHz |
| Data Format | MSB First, 2's complement |
| Empedans | 50Ω (source), High-Z (load) |

#### I2S Sinyalleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` § `I2S Sinyalleri` — L28–L47


```
I2S Bus Sinyalleri:

1. SCK (Serial Clock / Bit Clock)
   - Her bit için bir clock pulse
   - Frequency = 2 × channel × bit_depth × fs
   - Example: 2 × 2 × 32 × 44100 = 4.2336MHz

2. WS (Word Select / LR Clock)
   - Sol kanal: WS = 0
   - Sağ kanal: WS = 1
   - Frequency = fs (44.1kHz / 48kHz)

3. SD (Serial Data)
   - MSB First (en yüksek bit önce)
   - 2's complement formatı
   - 32-bit per channel
```

#### I2S Timing Diagram

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` § `I2S Timing Diagram` — L49–L68


```
SCK:  ┌──┐  ┌──┐  ┌──┐  ┌──┐  ┌──┐  ┌──┐  ┌──┐  ┌──┐
      └──┘  └──┘  └──┘  └──┘  └──┘  └──┘  └──┘  └──┘

WS:   ────────────────┐              ┌───────────────────
                      └──────────────┘
      ←── Sol Kanal (WS=0) ──→←── Sağ Kanal (WS=1) ──→

SD:   ──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──
        │B31│B30│B29│...│B1 │B0 │ │B31│B30│...│B1 │B0 │
        └──┴──┴──┴──┴──┴──┴──┘ └──┴──┴──┴──┴──┴──┴──┘
        ←── 32 bit Sol ────────→←── 32 bit Sağ ────────→

Timing:
- Data changes on SCK falling edge
- Data sampled on SCK rising edge
- WS changes 1 SCK cycle before MSB
```

#### Master/Slave Mode

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` § `Master/Slave Mode` — L70–L103


##### XMOS as Master

```
XMOS XU316 (Master)
     │
     ├─ SCK Output ──▶ DAC/ADC SCKI (Input)
     ├─ WS Output ──▶ DAC/ADC LRCK (Input)
     └─ MCLK Output ──▶ DAC/ADC MCLK (Input)

Advantages:
- Single clock source (no sync issues)
- Lower jitter (crystal directly connected)
- Simpler design
```

##### DAC/ADC as Slave

```
DAC (Slave) ← Receives clock from XMOS
     │
     ├─ SCKI ← SCK from XMOS
     ├─ LRCK ← WS from XMOS
     ├─ MCLK ← MCLK from XMOS
     └─ TDMD[0:3] ← SD from XMOS

ADC (Slave) ← Receives clock from XMOS
     │
     ├─ SCKI ← SCK from XMOS
     ├─ LRCK ← WS from XMOS
     ├─ MCLK ← MCLK from XMOS
     └─ DOUTA/B ──▶ SD to XMOS (output)
```

#### Multi-Channel Configuration

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` § `Multi-Channel Configuration` — L105–L133


##### 8-Channel TDM (Time Division Multiplexing)

```
TDM Frame (8 channels × 32 bits = 256 bits per frame):

WS:   ────────────────────────────────────────────────────
      │←─────────────────── WS Period ───────────────────→│

SD0:  ──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──
        │Ch1│Ch2│Ch3│...│Ch32│
        └──┴──┴──┴──┴──┴──┘

SD1:  ──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──
        │Ch33│Ch34│...│Ch64│
        └──┴──┴──┴──┴──┴──┘

SD2:  ──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──
        │Ch65│Ch66│...│Ch96│
        └──┴──┴──┴──┴──┴──┘

SD3:  ──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──┬──
        │Ch97│Ch98│...│Ch128│
        └──┴──┴──┴──┴──┴──┘

Toplam: 4 data line × 32 channels/line = 128 channels
(TDM mode, 32-bit per channel)
```

#### Impedans ve Drive

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` § `Impedans ve Drive` — L135–L157


##### Source Impedans

```
XMOS Output Stage:
- Output impedance: 50Ω (typical)
- Drive capability: ±8mA
- Rise/Fall time: < 5ns

Trace impedance:
- Characteristic impedance: 90Ω differential
- Termination: 100Ω parallel (optional)
```

##### Load Impedans

```
DAC/ADC Input:
- Input impedance: > 100kΩ (digital)
- Input capacitance: 5pF (typical)
- No termination required (high-Z)
```

#### Bileşen Değerleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` § `Bileşen Değerleri` — L159–L166


| # | Bileşen | Model/Değer | Adet | Açıklama |
|---|---------|-------------|------|----------|
| 1 | Ferrite Bead | BLM18AG601SN1 | 16 | I2S hat filtresi |
| 2 | Series Resistor | 22Ω 0402 | 16 | Source damping |
| 3 | Pull-up | 4.7kΩ 0402 | 4 | I2C control |
| 4 | Decoupling | 100nF 0402 | 16 | Per IC |

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` § `Bağımlılıklar` — L168–L175


| Bağımlılık | Yön | Açıklama |
|------------|-----|----------|
| K1 XMOS | Clock/Data | Master clock ve data source |
| K1 DAC | Data | I2S data sink |
| K1 ADC | Data | I2S data source |
| K0 Fiziksel | Alt | PCB trace routing |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` § `Durum: Implementasyon` — L177–L186


**Durum**: 🟢 Hazır

- Protocol: Philips I2S standard
- Timing: All specifications verified in simulation
- PCB routing: Length-matched traces (±1mm)
- Ferrite beads: Selected and verified
- Crystal: 22.5792MHz + 24.576MHz dual
- Multi-channel: TDM mode for 8-channel support

### rpi5-core.md — `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` (434 satır)

#### rpi5-core.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` § (giriş) — L1–L11

---
title: "RPi5 Core - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
platform: "Raspberry Pi 5"
date: 2026-09-20
version: 1.0.1
---

### RPi5 Core


#### Teknik Detaylar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` § `Teknik Detaylar` — L16–L336


##### 1. GPIO Control

GPIO, Raspberry Pi'nin genel amaçlı giriş/çıkış pinlerini kontrol etmek için kullanılır:

```c
#include <stdio.h>
#include <stdlib.h>
#include <fcntl.h>
#include <sys/mman.h>
#include <unistd.h>

#define BCM2712_PERI_BASE 0x1F000000
#define GPIO_BASE (BCM2712_PERI_BASE + 0x200000)
#define GPIO_SIZE 0x1000

volatile unsigned int *gpio;

// GPIO modunu ayarlama
void gpio_set_mode(unsigned int pin, unsigned int mode) {
    unsigned int reg = pin / 10;
    unsigned int shift = (pin % 10) * 3;
    unsigned int value = gpio[reg];

    value &= ~(7 << shift);
    value |= (mode << shift);
    gpio[reg] = value;
}

// GPIO pin değerini okuma
int gpio_read(unsigned int pin) {
    unsigned int reg = pin / 32;
    unsigned int shift = pin % 32;
    return (gpio[reg] >> shift) & 1;
}

// GPIO pin değerini yazma
void gpio_write(unsigned int pin, int value) {
    unsigned int reg = pin / 32;
    unsigned int shift = pin % 32;

    if (value) {
        gpio[7] = (1 << shift);  // SET register
    } else {
        gpio[10] = (1 << shift); // CLEAR register
    }
}

// GPIO pin için kesme (interrupt) yapılandırması
void gpio_setup_interrupt(unsigned int pin, int edge) {
    unsigned int reg = pin / 32;
    unsigned int shift = pin % 32;

    // Kenar algılama modu
    switch (edge) {
        case 0: // Rising edge
            gpio[19] |= (1 << shift);
            gpio[20] &= ~(1 << shift);
            break;
        case 1: // Falling edge
            gpio[19] &= ~(1 << shift);
            gpio[20] |= (1 << shift);
            break;
        case 2: // Both edges
            gpio[19] |= (1 << shift);
            gpio[20] |= (1 << shift);
            break;
    }

    // Pull-up/pull-down direnci
    gpio[37] &= ~(3 << (pin * 2));  // Clear
    gpio[37] |= (2 << (pin * 2));   // Pull-up
}

// GPIO initialization
void gpio_init() {
    int fd = open("/dev/mem", O_RDWR | O_SYNC);
    void *map = mmap(NULL, GPIO_SIZE, PROT_READ | PROT_WRITE,
        MAP_SHARED, fd, GPIO_BASE);
    gpio = (volatile unsigned int *)map;
    close(fd);
}
```

##### 2. DMA Engine

DMA, CPU müdahalesi olmadan bellek transferleri için:

```c
#include <stdint.h>
#include <stdlib.h>

// DMA register yapısı
typedef struct {
    volatile uint32_t info;
    volatile uint32_t src;
    volatile uint32_t dst;
    volatile uint32_t length;
    volatile uint32_t stride;
    volatile uint32_t next;
    volatile uint32_t pad[2];
} DMA_CB;

#define DMA_BASE (BCM2712_PERI_BASE + 0x007000)
#define DMA_CHANNEL_SIZE 0x100

// DMA kanalını başlatma
void dma_start_channel(int channel, DMA_CB *cb) {
    volatile unsigned int *dma = (volatile unsigned int *)
        (DMA_BASE + channel * DMA_CHANNEL_SIZE);

    // Kanalı sıfırla
    dma[0] = 0;  // CS - Control and Status
    dma[0] = (1 << 31);  // Reset

    // DMA control block adresini yükle
    dma[4] = (uint32_t)cb;  // CONBLK_AD

    // Aktif kanal olarak başlat
    dma[0] = (1 << 0) | (1 << 28);  // ACTIVE | END
}

// DMA transferi tamamlanma kontrolü
int dma_transfer_complete(int channel) {
    volatile unsigned int *dma = (volatile unsigned int *)
        (DMA_BASE + channel * DMA_CHANNEL_SIZE);

    return (dma[0] & (1 << 1)) != 0;  // ACTIVE biti temizlendiyse tamamlandı
}

// DMA ile bellekten GPIO'ya ses verisi gönderme
void dma_audio_to_gpio(uint16_t *audioBuffer, int length) {
    DMA_CB *cb = malloc(sizeof(DMA_CB));
    cb->info = (1 << 26) |  // Peri olarak GPIO
               (2 << 20) |  // 2 byte transfer
               (0 << 16) |  // 2 byte source
               (0 << 12);   // 2 byte dest
    cb->src = (uint32_t)audioBuffer;
    cb->dst = (GPIO_BASE + 0x1C);  // GPIO SET register
    cb->length = length * 2;
    cb->stride = 0;
    cb->next = 0;

    dma_start_channel(0, cb);

    // Tamamlanana kadar bekle
    while (!dma_transfer_complete(0)) {
        usleep(100);
    }

    free(cb);
}
```

##### 3. PWM Audio

PWM, Raspberry Pi'de ses üretimi için kullanılır:

```c
#include <stdint.h>

#define PWM_BASE (BCM2712_PERI_BASE + 0x20C000)
#define PWM_SIZE 0x100

volatile unsigned int *pwm;

// PWM modunu ayarlama
void pwm_init(int sampleRate) {
    volatile unsigned int *pwm = (volatile unsigned int *)
        mmap(NULL, PWM_SIZE, PROT_READ | PROT_WRITE,
            MAP_SHARED, fd, PWM_BASE);

    // PWMCTL register'ını yapılandır
    pwm[0] = (1 << 0) |  // PWEN1 - Enable PWM1
             (1 << 1) |  // MODE1 - Serialiser mode
             (1 << 2) |  // RPTL1 - Repeat last data
             (1 << 3) |  // SBIT1 - Silence bit
             (1 << 4);   // POLA1 - Polarity

    // PWMRNG1 - Range register (16-bit ses verisi için)
    pwm[1] = 65536;

    // PWMCLK ayarla
    volatile unsigned int *clk = (volatile unsigned int *)
        mmap(NULL, 0x100, PROT_READ | PROT_WRITE,
            MAP_SHARED, fd, CLK_BASE);

    clk[28] = 0x5A000000 | (1 << 5);  // PCM clock source
    clk[28] = 0x5A000000 | (1 << 5) | (1 << 24);  // MASH
    clk[28] = 0x5A000000 | (1 << 5) | (1 << 9);   // Enable

    // Clock divider hesapla
    uint32_t divider = 19200000 / sampleRate;
    clk[28] = 0x5A000000 | (1 << 5) | divider;
}

// PWM FIFO'ya veri yazma
void pwm_write_fifo(uint16_t *data, int length) {
    for (int i = 0; i < length; i++) {
        // FIFO dolu mu?
        while (pwm[4] & (1 << 1)) {
            usleep(1);
        }
        // FIFO'ya yaz
        pwm[1] = data[i];
    }
}

// PWM kesme (interrupt) ile gerçek zamanlı ses
void pwm_interrupt_handler() {
    if (pwm[4] & (1 << 9)) {  // TX4 Interrupt
        // Bufferdaki bir sonraki veriyi yükle
        if (currentPosition < bufferSize) {
            pwm[1] = audioBuffer[currentPosition++];
        } else {
            // Buffer tükendi
            pwm[0] &= ~(1 << 9);  // TX4 Interrupt'ı devre dışı bırak
        }

        // Interrupt bayrağını temizle
        pwm[4] = (1 << 9);
    }
}
```

##### 4. I2S Interface

I2S, dijital ses cihazları için endüstri standardı arayüzdür:

```c
#include <stdint.h>

#define I2S_BASE (BCM2712_PERI_BASE + 0x203000)
#define I2S_SIZE 0x100

volatile unsigned int *i2s;

// I2S modunu ayarlama
void i2s_init(int sampleRate, int channels) {
    volatile unsigned int *i2s = (volatile unsigned int *)
        mmap(NULL, I2S_SIZE, PROT_READ | PROT_WRITE,
            MAP_SHARED, fd, I2S_BASE);

    // I2SCTL register'ını yapılandır
    i2s[0] = (1 << 24) |  // RXON - RX enabled
             (1 << 25) |  // TXON - TX enabled
             (1 << 26) |  // DLEN - Data length (32-bit)
             (1 << 27) |  // FLEN - Frame length (64-bit)
             (0 << 28) |  // FCLKP - Frame clock polarity
             (1 << 29) |  // FSPOL - Frame sync polarity
             (0 << 30);   // CLKM - Clock mode

    // TXC - TX Configuration
    i2s[2] = (0 << 0) |   // CH1EN - Channel 1 enabled
             (0 << 3) |   // CH1POS - Channel 1 position
             (1 << 4) |   // CH2EN - Channel 2 enabled
             (1 << 7);    // CH2POS - Channel 2 position

    // SAMPLELEN - Sample length
    i2s[3] = 31;  // 32-bit sample

    // FIFO'yu sıfırla
    i2s[5] = (1 << 0) | (1 << 1);  // TXCLR | RXCLR

    // Clock divider hesapla
    uint32_t divider = 19200000 / (sampleRate * 2 * channels);
    i2s[6] = divider;  // CLKDIV
}

// I2S FIFO'ya veri yazma
void i2s_write(uint32_t *data, int length) {
    for (int i = 0; i < length; i++) {
        // FIFO dolu mu?
        while (i2s[5] & (1 << 1)) {
            usleep(1);
        }
        i2s[1] = data[i];  // FIFO'ya yaz
    }
}

// I2S FIFO'dan veri okuma
uint32_t i2s_read() {
    // FIFO boş mu?
    while (i2s[5] & (1 << 0)) {
        usleep(1);
    }
    return i2s[4];  // FIFO'dan oku
}

// I2S DMA entegrasyonu
void i2s_dma_transfer(uint32_t *txBuffer, uint32_t *rxBuffer, int length) {
    DMA_CB *txCb = malloc(sizeof(DMA_CB));
    DMA_CB *rxCb = malloc(sizeof(DMA_CB));

    // TX DMA
    txCb->info = (0 << 26) | (2 << 20) | (0 << 16) | (0 << 12);
    txCb->src = (uint32_t)txBuffer;
    txCb->dst = I2S_BASE + 0x04;  // FIFO TX
    txCb->length = length * 4;
    txCb->next = 0;

    // RX DMA
    rxCb->info = (1 << 26) | (2 << 20) | (0 << 16) | (0 << 12);
    rxCb->src = I2S_BASE + 0x04;  // FIFO RX
    rxCb->dst = (uint32_t)rxBuffer;
    rxCb->length = length * 4;
    rxCb->next = 0;

    dma_start_channel(0, txCb);
    dma_start_channel(1, rxCb);

    // Tamamlanana kadar bekle
    while (!dma_transfer_complete(0) || !dma_transfer_complete(1)) {
        usleep(100);
    }

    free(txCb);
    free(rxCb);
}
```

#### Donanım Notları

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` § `Donanım Notları` — L411–L417


1. **PWM Audio**: 16-bit ses verisi, 48kHz sample rate
2. **I2S**: 32-bit ses verisi, high-fidelity dijital ses
3. **DMA**: CPU müdahalesi olmadan yüksek hızlı veri transferi
4. **GPIO**: Düşük gecikme ile donanım kontrolü
5. **Device Tree**: Donanım yapılandırması için overlay desteği

## Bağımlılık Matrisi

> Bu tablo kaynaklardaki `## Bağımlılıklar` bölümlerinden satır satır derlenmiştir.

| Bağımlılık (kaynak satırı) | Kanıt |
|---|---|
| - Raspberry Pi 5 (BCM2712) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L386 |
| - Raspberry Pi OS 64-bit (Bookworm) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L387 |
| - Kernel 6.1 ve üzeri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L388 |
| - libgpiod 1.6 ve üzeri | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L389 |
| - ALSA (Advanced Linux Sound Architecture) | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L392 |
| - Raspberry Pi firmware | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L393 |
| - Device tree overlays | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L394 |
| - Cross-Platform API soyutlama katmanı | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L397 |
| - K1 Ses Motoru | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L398 |
| - K3 Uygulama Katmanı | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L399 |

## Kenar Durumları

| Senaryo / tetikleyici (kaynak satırı) | Kanıt |
|---|---|
| // Kanalı sıfırla | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L128 |
| // FIFO'yu sıfırla | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L278 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (kenar durumu satırı kaynakta taran… | — |

## Hata Modları

| Tetikleyici / davranış (kaynak satırı) | Kanıt |
|---|---|
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| - Frequency = fs (44.1kHz / 48kHz) | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` L41 |
| - Lower jitter (crystal directly connected) | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` L83 |
| - Rise/Fall time: < 5ns | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` L143 |
| unsigned int shift = (pin % 10) * 3; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L38 |
| unsigned int shift = pin % 32; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L49 |
| unsigned int shift = pin % 32; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L56 |
| unsigned int shift = pin % 32; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L68 |
| DMA, CPU müdahalesi olmadan bellek transferleri için: | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L103 |
| void dma_audio_to_gpio(uint16_t *audioBuffer, int length) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L148 |
| cb->src = (uint32_t)audioBuffer; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L154 |
| // Bufferdaki bir sonraki veriyi yükle | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L228 |
| if (currentPosition < bufferSize) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L229 |
| pwm[1] = audioBuffer[currentPosition++]; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L230 |
| // Buffer tükendi | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L232 |
| void i2s_dma_transfer(uint32_t *txBuffer, uint32_t *rxBuffer, int length) { | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L307 |
| txCb->src = (uint32_t)txBuffer; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L313 |
| rxCb->dst = (uint32_t)rxBuffer; | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L321 |
| 1. **PWM Audio**: 16-bit ses verisi, 48kHz sample rate | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L413 |
| 3. **DMA**: CPU müdahalesi olmadan yüksek hızlı veri transferi | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L415 |
| 4. **GPIO**: Düşük gecikme ile donanım kontrolü | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L416 |

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (güvenlik satırı kaynakta taranmadı) | — |

## Test ve Doğrulama Stratejisi

| Test / doğrulama maddesi (kaynak satırı) | Kanıt |
|---|---|
| - GPIO pin mapping'inin test edilmesi | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L431 |
| - DMA transfer testlerinin yapılması | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L433 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (test maddesi kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (test maddesi kaynakta taranmadı) | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (test maddesi kaynakta taranmadı) | — |

## Riskler ve Belirsizlikler

Kaynaklarda `Belirlenecek` / `UNKNOWN` / `TODO` içeren toplam **5** satır tespit edildi (tüm kaynak dosyalar üzerinde tam tarama).

| Belirsizlik / risk (kaynak satırı) | Kanıt |
|---|---|
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (belirsizlik satırı kaynakta taranm… | — |

## Identifier Envanteri

> Identifier'lar kaynak metinden sayım ile üretilmiştir; ilk geçtiği satır kanıt olarak verilmiştir.

| Identifier | Geçiş sayısı | İlk kanıt |
|---|---|---|
| `I2S` | 49 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` L2 |
| `DMA` | 45 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L14 |
| `firmware` | 1 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` L393 |
| `PCM` | 2 | `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` L12 |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` | ## Teknik Spesifikasyonlar | L14–L26 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` | ## I2S Sinyalleri | L28–L47 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` | ## I2S Timing Diagram | L49–L68 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` | ## Master/Slave Mode | L70–L103 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` | ## Multi-Channel Configuration | L105–L133 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` | ## Impedans ve Drive | L135–L157 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` | ## Bileşen Değerleri | L159–L166 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` | ## Bağımlılıklar | L168–L175 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` | ## Durum: Implementasyon | L177–L186 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | (başlık + giriş) | L1–L11 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Genel Bakış | L12–L14 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Teknik Detaylar | L16–L336 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## API / Arayüz | L338–L381 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Bağımlılıklar | L383–L399 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Performans Metrikleri | L401–L409 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Donanım Notları | L411–L417 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Durum: Implementasyon | L419–L434 | — (keep filtresi dışında) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k1-donanim/i2s-interface.md` § `Durum: Implementasyon` — L177–L186


**Durum**: 🟢 Hazır

- Protocol: Philips I2S standard
- Timing: All specifications verified in simulation
- PCB routing: Length-matched traces (±1mm)
- Ferrite beads: Selected and verified
- Crystal: 22.5792MHz + 24.576MHz dual
- Multi-channel: TDM mode for 8-channel support

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` § `Durum: Implementasyon` — L419–L434


**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. GPIO initialization ve basic control
2. I2S interface implementasyonu
3. DMA engine entegrasyonu
4. PWM audio output
5. Device tree overlay desteği

## Open Sorular

1. ⚠️ VERIFICATION REQUIRED — bu dosyanın hedef metrikleri (sürücü başına gecikme/buffer) kaynakta açıkça sabitlenmemiş ise üst merci onayı gerekir.
2. ⚠️ VERIFICATION REQUIRED — kaynak ile `.ai/CLAUDE.md` katman tanımı arasındaki farklar (varsa) ADR ile çözülmelidir.
kaynakta mevcuttur (yukarıda aktarıldı), canlı kod ile karşılaştırma yapılmamıştır.
4. ⚠️ VERIFICATION REQUIRED — bu belge yalnız vault kanıtına dayanır; disk üzerindeki uygulama kodu ile çapraz doğrulama yapılmamıştır.
