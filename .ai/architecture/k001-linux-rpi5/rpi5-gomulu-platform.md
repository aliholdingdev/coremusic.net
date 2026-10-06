---
title: "K001 Raspberry Pi 5 Gömülü Platform — ARM64 Yerleşim ve Donanım Sınırı"
type: architecture-layer
category: d01-os-donanim-surucu
version: 4.0.0
status: active
authority: "WORKFLOW.md §2.1 D01 · K000-K071"
updated: 2026-10-06
---

# K001 — Raspberry Pi 5 Gömülü Platform

> **K numarası:** K001 · **Klasör:** `k001-linux-rpi5` · **Dosya:** `rpi5-gomulu-platform`
> **Sorumlu persona:** `embedded-engineer` (birincil) · `performance-engineer` (ölçüm)

## 1. Kapsam

Bu dosya, Linux çekirdeğinin Raspberry Pi 5 gibi gömülü bir ARM64 platformda nasıl
yerleştiğini ve ses zinciri için hangi donanım sınırlarının geçerli olduğunu tanımlar.
Çekirdeğin genel mimarisi `[[linux-cekirdek-mimari]]` dosyasındadır.

| İlişki | Hedef |
|---|---|
| Kardeş dosya | `[[linux-cekirdek-mimari]]` |
| Klasör dizini | `[[index]]` |
| Donanım katmanı | `[[../k012-dijital-arayuz/index]]` · `[[../k013-pcb-hoparlor/index]]` |
| Sürücü katmanı | `[[../k016-linux-ses/index]]` |

## 2. Gömülü Kaynaklar

| # | Kaynak (salt-okunur) | Satır | Boş olmayan |
|---|----------------------|------:|------------:|
| 1 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | 427 | 338 |
| 2 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 77 | 71 |
| 3 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 38 | 28 |
| 4 | `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` | 22 | 20 |


> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` — satır: 427 (boş olmayan: 338).


## RPi5 Core

### Genel Bakış

RPi5 Core modülü, COREMUSIC'ın Raspberry Pi 5 platformu için temel donanım arayüzü işlevlerini sağlar. Bu modül, GPIO kontrolü, DMA motoru, PWM ses ve I2S arayüzü kullanarak gerçek zamanlı ses işleme ve donanım kontrolü için optimize edilmiş bir altyapı sunar. Yerleşik GPU ve donanım hızlandırmalı ses işleme özelliklerini destekler.

### Teknik Detaylar

#### 1. GPIO Control

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

#### 2. DMA Engine

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

#### 3. PWM Audio

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

#### 4. I2S Interface

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

### API / Arayüz

#### COREMUSIC RPi5 API Başlık Dosyası

```c
#ifndef COREMUSIC_RPI5_H
#define COREMUSIC_RPI5_H

#include <stdint.h>

// GPIO Operations
CM_Status CM_GPIO_Init(void);
CM_Status CM_GPIO_SetMode(uint32_t pin, uint32_t mode);
CM_Status CM_GPIO_Read(uint32_t pin, int *value);
CM_Status CM_GPIO_Write(uint32_t pin, int value);
CM_Status CM_GPIO_SetInterrupt(uint32_t pin, uint32_t edge);

// DMA Operations
CM_Status CM_DMA_Init(void);
CM_Status CM_DMA_Start(int channel, void *cb);
CM_Status CM_DMA_WaitComplete(int channel);
CM_Status CM_DMA_Transfer(uint32_t src, uint32_t dst, size_t length);

// PWM Audio
CM_Status CM_PWM_Init(uint32_t sampleRate);
CM_Status CM_PWM_Write(uint16_t *data, int length);
CM_Status CM_PWM_SetVolume(int volume);
CM_Status CM_PWM_Start(void);
CM_Status CM_PWM_Stop(void);

// I2S Interface
CM_Status CM_I2S_Init(uint32_t sampleRate, int channels);
CM_Status CM_I2S_Write(uint32_t *data, int length);
CM_Status CM_I2S_Read(uint32_t *data, int length);
CM_Status CM_I2S_DMA_Transfer(uint32_t *tx, uint32_t *rx, int length);

// Combined Audio
CM_Status CM_Audio_Output_Start(uint32_t sampleRate, int channels);
CM_Status CM_Audio_Output_Stop(void);
CM_Status CM_Audio_Input_Start(uint32_t sampleRate, int channels);
CM_Status CM_Audio_Input_Stop(void);

#endif // COREMUSIC_RPI5_H
```

### Bağımlılıklar

#### Gereksinimler
- Raspberry Pi 5 (BCM2712)
- Raspberry Pi OS 64-bit (Bookworm)
- Kernel 6.1 ve üzeri
- libgpiod 1.6 ve üzeri

#### Alt Katmanlar
- ALSA (Advanced Linux Sound Architecture)
- Raspberry Pi firmware
- Device tree overlays

#### Üst Katmanlar
- Cross-Platform API soyutlama katmanı
- K1 Ses Motoru
- K3 Uygulama Katmanı

### Performans Metrikleri

| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| GPIO read/write | < 1μs | Belirlenecek |
| DMA transfer latency | < 10μs | Belirlenecek |
| PWM sample rate | 48kHz+ | Belirlenecek |
| I2S latency | < 1ms | Belirlenecek |
| Audio output latency | < 5ms | Belirlenecek |

### Donanım Notları

1. **PWM Audio**: 16-bit ses verisi, 48kHz sample rate
2. **I2S**: 32-bit ses verisi, high-fidelity dijital ses
3. **DMA**: CPU müdahalesi olmadan yüksek hızlı veri transferi
4. **GPIO**: Düşük gecikme ile donanım kontrolü
5. **Device Tree**: Donanım yapılandırması için overlay desteği

### Durum: Implementasyon

**Mevcut Durum**: Tasarım tamamlandı, implementasyon bekleniyor.

**Öncelik Sırası**:
1. GPIO initialization ve basic control
2. I2S interface implementasyonu
3. DMA engine entegrasyonu
4. PWM audio output
5. Device tree overlay desteği

**Sonraki Adımlar**:
- GPIO pin mapping'inin test edilmesi
- I2S driver implementasyonu
- DMA transfer testlerinin yapılması
- Device tree overlay dosyalarının oluşturulması



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:603-679` — satır: 77 (boş olmayan: 71).

#### K0.8 — Windows API

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K0.8.1** | Windows API (3. katman) | windows-api.md — 15 yaprak |
| K0.8.1.1 | ASIO SDK Entegrasyonu | windows-api.md L25 |
| K0.8.1.2 | WASAPI Entegrasyonu | windows-api.md L91 |
| K0.8.1.3 | Windows Threading | windows-api.md L150 |
| K0.8.1.4 | Bellek Yönetimi | windows-api.md L188 |
| K0.8.1.5 | COM Initialization | windows-api.md L238 |
| K0.8.1.6 | Windows Specific ADR'ler | windows-api.md L253 |
| K0.8.1.7 | ASIO 2.3 SDK Yapısı | windows-api.md L27 |
| K0.8.1.8 | ASIO Callback Protokolü | windows-api.md L50 |
| K0.8.1.9 | ASIO Buffer Konfigürasyonu | windows-api.md L79 |
| K0.8.1.10 | WASAPI Modları | windows-api.md L93 |
| K0.8.1.11 | WASAPI Akışı | windows-api.md L101 |
| K0.8.1.12 | Thread Oluşturma | windows-api.md L152 |
| K0.8.1.13 | Senkronizasyon Primitifleri | windows-api.md L175 |
| K0.8.1.14 | Large Page Allocation | windows-api.md L190 |
| K0.8.1.15 | Memory-Mapped Files | windows-api.md L206 |

#### K0.9 — Dosya Sistemi / Ağ / Zaman

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K0.9.1** | Dosya Sistemi (3. katman) | README.md §7 — 3 yaprak |
| K0.9.1.1 | Dosya Sistemi (K0-07) | README.md L312 |
| K0.9.1.2 | Path Convention | README.md L314 |
| K0.9.1.3 | Dosya Erişim Kuralları | README.md L323 |
| **K0.9.2** | Ağ Stack (3. katman) | README.md §8 — 2 yaprak |
| K0.9.2.1 | Ağ Stack (K0-08) | README.md L334 |
| K0.9.2.2 | Socket Seviyeleri | README.md L336 |
| **K0.9.3** | Zaman Servisi (3. katman) | README.md §9 — 3 yaprak |
| K0.9.3.1 | Zaman Servisi (K0-09) | README.md L347 |
| K0.9.3.2 | Zaman Ölçümleri | README.md L349 |
| K0.9.3.3 | ASIO Zamanlama | README.md L358 |

#### K0.10 — Yönetişim & Bileşen Haritası

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K0.10.1** | CLAUDE.md Guardrails (3. katman) | CLAUDE.md — 4 yaprak |
| K0.10.1.1 | Hard Guardrails | CLAUDE.md L16 |
| K0.10.1.2 | Teknoloji Kısıtlamaları | CLAUDE.md L25 |
| K0.10.1.3 | Yasaklı Örüntüler | CLAUDE.md L35 |
| K0.10.1.4 | İlgili ADR'ler | CLAUDE.md L46 |
| **K0.10.2** | index.md (3. katman) | index.md — 8 yaprak |
| K0.10.2.1 | Genel Bakış | index.md L11 |
| K0.10.2.2 | Mimari Diyagram | index.md L15 |
| K0.10.2.3 | Bileşen Listesi | index.md L47 |
| K0.10.2.4 | Bağımlılıklar | index.md L63 |
| K0.10.2.5 | Alt Katmanlar | index.md L65 |
| K0.10.2.6 | Üst Katmanlar | index.md L69 |
| K0.10.2.7 | Temel İlkeler | index.md L74 |
| K0.10.2.8 | Durum: Implementasyon | index.md L82 |
| **K0.10.3** | README Bileşen Haritası (3. katman) | README.md §2 — 20 yaprak |
| K0.10.3.1 | K0-01 Windows API | README.md L56 |
| K0.10.3.2 | K0-02 Linux Kernel | README.md L57 |
| K0.10.3.3 | K0-03 macOS Core | README.md L58 |
| K0.10.3.4 | K0-04 IPC Manager | README.md L59 |
| K0.10.3.5 | K0-05 Memory Manager | README.md L60 |
| K0.10.3.6 | K0-06 Thread Manager | README.md L61 |
| K0.10.3.7 | K0-07 File System | README.md L62 |
| K0.10.3.8 | K0-08 Network Stack | README.md L63 |
| K0.10.3.9 | K0-09 Time Service | README.md L64 |
| K0.10.3.10 | K0-10 Event System | README.md L65 |
| K0.10.3.11 | K0-11 Timer Service | README.md L66 |
| K0.10.3.12 | K0-12 Mutex/Spinlock | README.md L67 |
| K0.10.3.13 | K0-13 Condition Variable | README.md L68 |
| K0.10.3.14 | K0-14 Semaphore | README.md L69 |
| K0.10.3.15 | K0-15 Atomic Operations | README.md L70 |
| K0.10.3.16 | K0-16 Cache Line | README.md L71 |
| K0.10.3.17 | K0-17 CPU Detection | README.md L72 |
| K0.10.3.18 | K0-18 SIMD Support | README.md L73 |
| K0.10.3.19 | K0-19 Power Management | README.md L74 |
| K0.10.3.20 | K0-20 Thermal Management | README.md L75 |



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:319-356` — satır: 38 (boş olmayan: 28).

### 8. Ağ Stack (K0-08)

#### 8.1 Socket Seviyeleri

| Seviye | API | Kullanım |
|--------|-----|----------|
| Raw | BSD Sockets | Low-level network |
| TCP | TCP Sockets | HTTP, WebSocket |
| UDP | UDP Sockets | DNS, mDNS |
| Multicast | IGMP | DLNA, AirPlay |

---

### 9. Zaman Servisi (K0-09)

#### 9.1 Zaman Ölçümleri

| Metot | Çözünürlük | Platform |
|-------|-----------|----------|
| QueryPerformanceCounter | ~100ns | Windows |
| clock_gettime(CLOCK_MONOTONIC) | ~1ns | Linux/macOS |
| mach_absolute_time | ~1ns | macOS |
| rdtsc | ~1ns | Tümü (x86) |

#### 9.2 ASIO Zamanlama

```cpp
// ASIO TimeInfo
struct ASIOTimeInfo {
    double speed;           // Sample rate ratio
    int64_t systemTime;    // Nanoseconds since epoch
    int64_t samplePosition;// Current sample position
    int64_t sampleRate;    // Current sample rate
};
```

---



> **Kanıt (salt-okunur kaynak):** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:581-602` — satır: 22 (boş olmayan: 20).

#### K0.7 — Konteyner Runtime

| Kod | Ad | Kanıt (dosya · satır) |
|-----|----|-----------------------|
| **K0.7.1** | Container Runtime (3. katman) | container-runtime.md — 16 yaprak |
| K0.7.1.1 | Genel Bakış | container-runtime.md L11 |
| K0.7.1.2 | Teknik Detaylar | container-runtime.md L15 |
| K0.7.1.3 | API / Arayüz | container-runtime.md L414 |
| K0.7.1.4 | Bağımlılıklar | container-runtime.md L461 |
| K0.7.1.5 | Performans Metrikleri | container-runtime.md L479 |
| K0.7.1.6 | Güvenlik Notları | container-runtime.md L489 |
| K0.7.1.7 | Durum: Implementasyon | container-runtime.md L497 |
| K0.7.1.8 | Docker Engine Entegrasyonu | container-runtime.md L17 |
| K0.7.1.9 | containerd Entegrasyonu | container-runtime.md L53 |
| K0.7.1.10 | Kubernetes Entegrasyonu | container-runtime.md L113 |
| K0.7.1.11 | Docker Compose | container-runtime.md L245 |
| K0.7.1.12 | Health Check Implementasyonu | container-runtime.md L330 |
| K0.7.1.13 | COREMUSIC Container API Başlık Dosyası | container-runtime.md L416 |
| K0.7.1.14 | Gereksinimler | container-runtime.md L463 |
| K0.7.1.15 | Alt Katmanlar | container-runtime.md L469 |
| K0.7.1.16 | Üst Katmanlar | container-runtime.md L474 |


## 3. Platform Kenar Durumları

| # | Kenar durum | Koşul | Risk | Yaklaşım |
|---|---|---|---|---|
| E1 | Sıcaklık nedeniyle frekans düşürme | uzun süreli yük | periyot aşımı | termal gözlem + periyot bütçesi |
| E2 | SD kart I/O gecikmesi | disk baskısı | dosyadan okuma gecikmesi | bellek içi tamponlama |
| E3 | USB denetleyici paylaşımı | çoklu USB aygıt | gecikme sıçraması | cihaz topolojisi kontrolü |
| E4 | Ağ bağlantısının kopması | uzak ses kullanımı | akış kesintisi | yerel tampon + yeniden bağlanma |
| E5 | Güç kesintisi sonrası zaman kaybı | RTC yok | dosya zaman damgası sapması | ağdan zaman senkronu |

> **Kanıt:** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` ve `README.md` §8-§9.

## 4. Hata Modları

| # | Hata modu | Belirti | Sinyal | Sonuç |
|---|---|---|---|---|
| F1 | Periyot aşımı (xrun) | tıkırtı/kopukluk | xrun sayacı | ses bozulması |
| F2 | Yetersiz güç kaynağı | ani yeniden başlatma | gerilim alarmı | oturum kaybı |
| F3 | Cihaz yolu değişimi | yanlış aygıt | kimlik uyuşmazlığı | yanlış giriş/çıkış |
| F4 | Bellek yetersizliği | süreç ölümü | OOM kaydı | servis düşmesi |
| F5 | Zaman sunucusuna ulaşılamama | saat sürüklenmesi | NTP hata sayacı | dosya sıralama hatası |

> **Kanıt:** `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md` §8 (Ağ Stack), §9 (Zaman Servisi).

## 5. Bağımlılıklar

- **Yukarı:** `[[linux-cekirdek-mimari]]` (çekirdek semantiği) — bu platform onun üzerinde çalışır.
- **Yatay:** `[[../k013-pcb-hoparlor/index]]` (donanım yerleşimi), `[[../k011-guc-koruma-termal/index]]` (besleme/termal).
- **Aşağı:** `[[../k016-linux-ses/index]]` (ALSA/PipeWire), `[[../k014-surucu-yigin/index]]` (buffer/latency).

## 6. Ölçüm ve Persona

| Görev | Persona | Çıktı |
|---|---|---|
| Periyot/aşım ölçümü | `performance-engineer` | metrik tablosu |
| Donanım arayüzü doğrulaması | `embedded-engineer` | kanıt satırı |
| Gömülü ortam paketleme | `devops-engineer` | ortam tarifi |

## 7. Kanıt Kataloğu

1. `Kanıt: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` — gömülü gövde.
2. `Kanıt: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:603-679` — K0.8-K0.10 şemaları.
3. `Kanıt: _backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/README.md:319-356` — ağ ve zaman servisi.
4. `⚠️ VERIFICATION REQUIRED` — RPi5 üzerinde ölçülmüş xrun frekansı (vault'ta ölçüm yok).
5. `⚠️ VERIFICATION REQUIRED` — hedef DAC/ADC kartının platformda tanınma durumu (disk kanıtı yok).

## 8. Doğrulama Durumu

- Tüm iddialar gömülü kaynaklara dayanır; ölçüm içeren iddialar işaretlidir.
- Üst dizin: `[[index]]`
