---
title: "IRQ ve Kesinti Yöneticisi - k018-dma-kesinti-yonetimi"
type: architecture
category: mimari
version: 4.0.0
status: active
authority: "Vault (.ai/) SSOT - D01 dilimi (k018-k035)"
updated: 2026-10-06
---

# IRQ ve Kesinti Yöneticisi

> Klasör: `k018-dma-kesinti-yonetimi` · Dilim: D01 (k018–k035) · Dosya: `irq-kesinti-yoneticisi.md`
> Sorumlu persona: `dsp-firmware-engineer` (DSP Firmware Mühendisi) · `audio-hardware-engineer` (Audio Hardware Mühendisi)
> Kaynak: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` · `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` — aktarım bölüm bazında § ve L aralığı ile kanıtlanmıştır.

## Genel Bakış

Kesinti yönetimi, önceliklendirme ve DMA ile etkileşim.

Bu belge; D01 diliminin (DMA ve Kesinti Yönetimi) bir parçasıdır ve tek kaynak doğruluk (SSOT) olarak `.ai/` vault'unda yaşar. İçerik, `_backup` yedeğindeki k0/k1/k2 bölümlerinden verbatim (değişikliksiz) aktarım ve kaynak satırlarına atıf yapan üretim bölümlerinden oluşur.

## Kapsam ve Sınırlar

- **Kapsam:** Kesinti yönetimi, önceliklendirme ve DMA ile etkileşim.
- **Kapsam dışı:** [UNKNOWN] — kaynaklarda bu dosya için açık bir "kapsam dışı" tanımı yok; sınırlar üst merci onayı ile netleştirilmelidir.
- **Bağlı olduğu klasör:** [[index.md]] (DMA ve Kesinti Yönetimi)
- **Çapraz referanslar:** [[../k022-rpi5-core/rpi5-core.md]] · [[../k031-buffer-management/buffer-management.md]] · [[../k030-driver-stack/driver-stack-mimari.md]]

## Kaynak Aktarımı (Verbatim)

> Başlık hiyerarşisi iki kademe aşağı indirildi; `§` ve L aralıkları orijinal kaynak başlığını ve satırlarını gösterir. Aşağıdaki `###`/`####` blokları kaynaktan değiştirilmeden kopyalanmıştır.

### index.md — `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` (91 satır)

#### index.md — başlık ve giriş

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § (giriş) — L1–L9

---
title: "K2 Sürücü Katmanı"
layer: K2
category: "Sürücü"
date: 2026-09-20
---

### K2 Sürücü Katmanı


#### Genel Bakış

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Genel Bakış` — L10–L12


K2 Sürücü Katmanı, COREMUSIC mimarisinin donanım ile yazılım arasındaki köprü katmanıdır. Tüm ses donanımı elemanları (ses kartları, USB DAC'ler, ağı ses cihazları) için platform-bağımsız bir soyutlama sağlar. Bu katman, gerçek zamanlı ses akışı için gereken düşük gecikmeli (low-latency) veri yollarını yönetir.

#### Mimari Konum

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Mimari Konum` — L14–L20


```
K0 (Donanım) → K1 (OS/Core) → K2 (Sürücü) → K3 (Ses Motoru)
```

K2 katmanı, K1'in sağladığı çekirdek hizmetleri (DMA, kesinti, bellek yönetimi) üzerine inşa edilir ve K3'ün DSP zincirine ham ses verisini iletir.

#### Kapsam ve Kategoriler

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Kapsam ve Kategoriler` — L22–L39


##### Platform Sürücüleri
- **ASIO Drivers**: Windows profesyonel ses, Exclusive mode
- **WASAPI**: Windows Audio Session API, modern Windows ses
- **ALSA Native**: Linux çekirdek seviyesi ses
- **PipeWire**: Modern Linux ses yönlendirmesi
- **CoreAudio**: macOS/iOS ses altyapısı

##### Donanım Arabirimleri
- **USB Audio Class 2.0**: USB ses cihazları için evrensel protokol
- **Network Audio**: Dante, AVB, RAVENNA Protokolleri
- **Bluetooth A2DP**: Kablosuz ses iletimi (LDAC, aptX HD, LC3)

##### Mimari Bileşenler
- **Driver Stack**: Çok katmanlı sürücü yığını
- **Buffer Management**: Ring buffer, double buffering, kilit-free kuyruklar
- **Latency Optimization**: Gecikme zincirleri, buffer boyut seçimi

#### Temel İlkeler

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Temel İlkeler` — L41–L53


##### 1. Gerçek Zamanlı Güvenlik (Real-Time Safety)
Tüm K2 kodu, kesme.Context içinde çalışabilir: bellek ayırma yasak, kilitlenme (blocking) yasak, sistem çağrısı yasak.

##### 2. Donanım Bağımsızlığı
Aynı API, ASIO, WASAPI, ALSA ve CoreAudio üzerinde çalışır. Üst katmanlar hangi sürücünün kullanıldığını bilmez.

##### 3. Minimum Gecikme
Hedef: 0.5ms'den az round-trip latency. Buffer boyutları 32-64 sample aralığında.

##### 4. Hata Toleransı
Donanım bağlantı kesilse bile ses motoru (K3) çökmemeli, graceful degradation uygulanmalıdır.

#### Bağımlılıklar

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Bağımlılıklar` — L55–L61


| Katman | İlişki |
|--------|--------|
| K0 | Donanım kaynaklarını kullanır (DMA, IRQ) |
| K1 | İşletim sistemi hizmetlerini çağırır |
| K3 | Ham ses verisini iletir |

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Performans Metrikleri` — L63–L71


| Metrik | Hedef |
|--------|-------|
| Round-trip Latency | < 0.5ms (ASIO Exclusive) |
| Buffer Boyutu | 32-64 sample @ 96kHz |
| CPU Kullanımı | < 5% (boşta) |
| Maksimum Kanal Sayısı | 128 giriş + 128 çıkış |
| Desteklenen Örnekleme Hızları | 44.1k, 48k, 88.2k, 96k, 176.4k, 192k, 352.8k, 384k |

#### Dosya Haritası

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Dosya Haritası` — L73–L87


| Dosya | İçerik |
|-------|--------|
| asio-drivers.md | ASIO Exclusive mode, low latency, buffer yönetimi |
| wasapi-exclusive.md | WASAPI Exclusive/Shared, Windows audio session |
| alsa-native.md | ALSA native audio, PCM aygıtları |
| pipewire-modern.md | PipeWire modern Linux ses, SPA pluginleri |
| core-audio-macos.md | CoreAudio macOS, AudioUnits, HAL |
| usb-audio-class.md | USB Audio Class 2.0, isochronous mode |
| network-audio-drivers.md | Dante, AVB, RAVENNA ağ sesi |
| bluetooth-a2dp.md | Bluetooth A2DP, LDAC, aptX HD, LC3 |
| driver-stack-mimari.md | Çok katmanlı sürücü yığını, HAL soyutlama |
| buffer-management.md | Ring buffer, double buffering, lock-free queues |
| latency-optimization.md | Latency zincirleri, buffer seçimi, RT planlama |

#### Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Durum: Implementasyon` — L89–L91


K2 Katmanı, K1'in stabilitesi doğrulandıktan sonra implemente edilecektir. Önce ALSA ve PipeWire (Linux) sürücüleri, ardından WASAPI ve ASIO (Windows), en sonda CoreAudio (macOS) sürücüleri yazılacaktır.

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

#### Performans Metrikleri

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` § `Performans Metrikleri` — L401–L409


| Metrik | Hedef | Mevcut |
|--------|-------|--------|
| GPIO read/write | < 1μs | Belirlenecek |
| DMA transfer latency | < 10μs | Belirlenecek |
| PWM sample rate | 48kHz+ | Belirlenecek |
| I2S latency | < 1ms | Belirlenecek |
| Audio output latency | < 5ms | Belirlenecek |

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
| ### 4. Hata Toleransı | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L52 |
| Donanım bağlantı kesilse bile ses motoru (K3) çökmemeli, graceful degradation uygulanmalıdır. | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L53 |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |
| ⚠️ VERIFICATION REQUIRED — kaynakta bu başlıkta yeterli kanıt yok, üst merci incelemesi beklenmektedir. (hata modu satırı kaynakta taranmad… | — |

## Performans ve Gereksinimler

| Metrik / gereksinim (kaynak satırı) | Kanıt |
|---|---|
| K2 Sürücü Katmanı, COREMUSIC mimarisinin donanım ile yazılım arasındaki köprü katmanıdır. Tüm ses donanımı elemanları (ses kartları, USB DA… | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L12 |
| - **Buffer Management**: Ring buffer, double buffering, kilit-free kuyruklar | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L38 |
| - **Latency Optimization**: Gecikme zincirleri, buffer boyut seçimi | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L39 |
| ### 3. Minimum Gecikme | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L49 |
| Hedef: 0.5ms'den az round-trip latency. Buffer boyutları 32-64 sample aralığında. | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L50 |
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

## Güvenlik Notları

| Güvenlik önlemi / tehdit (kaynak satırı) | Kanıt |
|---|---|
| ### 1. Gerçek Zamanlı Güvenlik (Real-Time Safety) | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L43 |
| K2 Katmanı, K1'in stabilitesi doğrulandıktan sonra implemente edilecektir. Önce ALSA ve PipeWire (Linux) sürücüleri, ardından WASAPI ve ASI… | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L91 |
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
| `IRQ` | 1 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L59 |
| `DMA` | 47 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L20 |
| `kesinti` | 1 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L20 |
| `driver-stack` | 1 | `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` L85 |

## Kaynak Bölüm Envanteri

| Kaynak dosya | § Bölüm | Satır aralığı | Aktarım |
|---|---|---|---|
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | (başlık + giriş) | L1–L9 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Genel Bakış | L10–L12 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Mimari Konum | L14–L20 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Kapsam ve Kategoriler | L22–L39 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Temel İlkeler | L41–L53 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Bağımlılıklar | L55–L61 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Performans Metrikleri | L63–L71 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Dosya Haritası | L73–L87 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` | ## Durum: Implementasyon | L89–L91 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | (başlık + giriş) | L1–L11 | ✓ |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Genel Bakış | L12–L14 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Teknik Detaylar | L16–L336 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## API / Arayüz | L338–L381 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Bağımlılıklar | L383–L399 | — (keep filtresi dışında) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Performans Metrikleri | L401–L409 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Donanım Notları | L411–L417 | ✓ (verbatim) |
| `_backup/arch-2026-10-06_1057/architecture/k0-isletim-sistemi/rpi5-core.md` | ## Durum: Implementasyon | L419–L434 | — (keep filtresi dışında) |

## Durum: Implementasyon

> Aktarım: `_backup/arch-2026-10-06_1057/architecture/k2-surucu/index.md` § `Durum: Implementasyon` — L89–L91


K2 Katmanı, K1'in stabilitesi doğrulandıktan sonra implemente edilecektir. Önce ALSA ve PipeWire (Linux) sürücüleri, ardından WASAPI ve ASIO (Windows), en sonda CoreAudio (macOS) sürücüleri yazılacaktır.

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
