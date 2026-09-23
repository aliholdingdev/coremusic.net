---
title: "RPi5 Core - İşletim Sistemi Katmanı"
layer: K0
category: "İşletim Sistemi"
platform: "Raspberry Pi 5"
date: 2026-09-20
version: 1.0.0
---

# RPi5 Core

## Genel Bakış

RPi5 Core modülü, COREMUSIC'ın Raspberry Pi 5 platformu için temel donanım arayüzü işlevlerini sağlar. Bu modül, GPIO kontrolü, DMA motoru, PWM ses ve I2S arayüzü kullanarak gerçek zamanlı ses işleme ve donanım kontrolü için optimize edilmiş bir altyapı sunar. Yerleşik GPU ve donanım hızlandırmalı ses işleme özelliklerini destekler.

## Teknik Detaylar

### 1. GPIO Control

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

### 2. DMA Engine

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

### 3. PWM Audio

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

### 4. I2S Interface

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

## API / Arayüz

### COREMUSIC RPi5 API Başlık Dosyası

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

## Bağımlılıklar

### Gereksinimler
- Raspberry Pi 5 (BCM2712)
- Raspberry Pi OS 64-bit (Bookworm)
- Kernel 6.1 ve üzeri
- libgpiod 1.6 ve üzeri

### Alt Katmanlar
- ALSA (Advanced Linux Sound Architecture)
- Raspberry Pi firmware
- Device tree overlays

### Üst Katmanlar
- Cross-Platform API soyutlama katmanı
- K1 Ses Motoru
- K3 Uygulama Katmanı

## Performans Metrikleri

| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| GPIO read/write | < 1μs | Belirlenecek |
| DMA transfer latency | < 10μs | Belirlenecek |
| PWM sample rate | 48kHz+ | Belirlenecek |
| I2S latency | < 1ms | Belirlenecek |
| Audio output latency | < 5ms | Belirlenecek |

## Donanım Notları

1. **PWM Audio**: 16-bit ses verisi, 48kHz sample rate
2. **I2S**: 32-bit ses verisi, high-fidelity dijital ses
3. **DMA**: CPU müdahalesi olmadan yüksek hızlı veri transferi
4. **GPIO**: Düşük gecikme ile donanım kontrolü
5. **Device Tree**: Donanım yapılandırması için overlay desteği

## Durum: Implementasyon

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
