---
title: "STM32/RPi Support Firmware"
layer: Firmware
category: "Firmware"
date: 2026-09-20
---

# STM32/RPi Support Firmware

## Genel Bakış

STM32 ve Raspberry Pi support firmware, COREMUSIC'ın kontrol katmanını (control plane) yönetir. STM32, XMOS XU316 ile low-level hardware kontrolünü; Raspberry Pi, üst düzey kontrol arayüzlerini (display, network, user interface) sağlar. SPI/I2C comunicación, firmware update orchestration ve system health monitoring bu katmanın temel sorumluluklarıdır.

## Firmware Mimarisi

```
┌─────────────────────────────────────────────────────────────────┐
│              STM32/RPi SUPPORT FIRMWARE                         │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────────────────────────────────────────────┐      │
│  │              Raspberry Pi (Control Host)              │      │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────┐  │      │
│  │  │  Network     │  │  Display     │  │  User    │  │      │
│  │  │  Manager     │  │  Server      │  │  Interface│  │      │
│  │  └──────────────┘  └──────────────┘  └──────────┘  │      │
│  └──────────────────┬───────────────────────────────────┘      │
│                     │ SPI / I2C                                 │
│  ┌──────────────────▼───────────────────────────────────┐      │
│  │              STM32F4 (Bridge Controller)              │      │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────┐  │      │
│  │  │  XMOS        │  │  GPIO        │  │  ADC/DAC │  │      │
│  │  │  Comms       │  │  Control     │  │  Monitor │  │      │
│  │  └──────────────┘  └──────────────┘  └──────────┘  │      │
│  └──────────────────┬───────────────────────────────────┘      │
│                     │ USB / I2S                                 │
│  ┌──────────────────▼───────────────────────────────────┐      │
│  │              XMOS XU316 (Audio Core)                  │      │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────┐  │      │
│  │  │  USB Audio   │  │  I2S         │  │  DSP     │  │      │
│  │  │  Endpoint    │  │  Driver      │  │  Engine  │  │      │
│  │  └──────────────┘  └──────────────┘  └──────────┘  │      │
│  └──────────────────────────────────────────────────────┘      │
│                                                                 │
│  ┌──────────────────────────────────────────────────────┐      │
│  │              Communication Protocols                 │      │
│  │  ┌──────────────┐  ┌──────────────┐  ┌──────────┐  │      │
│  │  │  SPI Master  │  │  I2C Master  │  │  UART    │  │      │
│  │  │  (10 MHz)    │  │  (400 kHz)   │  │  (115200)│  │      │
│  │  └──────────────┘  └──────────────┘  └──────────┘  │      │
│  └──────────────────────────────────────────────────────┘      │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## Kaynak Kod Yapısı

```
mcu_support/
├── stm32/
│   ├── Core/
│   │   ├── Inc/
│   │   │   ├── main.h              # Ana header
│   │   │   ├── stm32f4xx_hal_conf.h # HAL konfigürasyonu
│   │   │   ├── xmos_comm.h         # XMOS iletişim header
│   │   │   ├── spi_driver.h        # SPI driver header
│   │   │   ├── i2c_driver.h        # I2C driver header
│   │   │   ├── gpio_control.h      # GPIO kontrol header
│   │   │   └── system_monitor.h    # System monitoring header
│   │   │
│   │   └── Src/
│   │       ├── main.c              # Ana program
│   │       ├── stm32f4xx_it.c      # Interrupt handler
│   │       ├── xmos_comm.c         # XMOS iletişim
│   │       ├── spi_driver.c        # SPI master driver
│   │       ├── i2c_driver.c        # I2C master driver
│   │       ├── gpio_control.c      # GPIO kontrol
│   │       ├── system_monitor.c    # System health monitoring
│   │       ├── adc_monitor.c       # ADC voltage monitoring
│   │       └── firmware_update.c   # Firmware update orchestration
│   │
│   ├── Drivers/
│   │   └── STM32F4xx_HAL_Driver/   # STM32 HAL library
│   │
│   ├── Makefile                    # STM32 build sistemi
│   └── STM32F4xx.s                 # Startup assembly
│
├── rpi/
│   ├── src/
│   │   ├── main.c                  # Ana program
│   │   ├── xmos_interface.c        # XMOS SPI interface
│   │   ├── display_server.c        # Display server (HDMI/eDP)
│   │   ├── network_manager.c       # Network management
│   │   ├── web_interface.c         # Web-based control UI
│   │   ├── mqtt_client.c           # MQTT communication
│   │   ├── config_manager.c        # Configuration management
│   │   └── ota_update.c            # Over-the-air update
│   │
│   ├── include/
│   │   ├── config.h                # Konfigürasyon
│   │   └── xmos_protocol.h        # XMOS iletişim protokolü
│   │
│   ├── CMakeLists.txt              # RPi build sistemi
│   └── config/
│       └── coremusic.conf          # Varsayılan konfigürasyon
│
└── common/
    ├── xmos_protocol.h             # Ortak XMOS protokolü
    └── command_definitions.h       # Ortak komut tanımları
```

## Teknik Detaylar

### STM32F4 Pin Configuration

```
STM32F407VG Pin Assignment:

┌─────────────────────────────────────────────────────────────┐
│ Pin       │ Port  │ AF    │ Function                      │
├───────────┼───────┼───────┼───────────────────────────────┤
│ PA5       │ SPI1  │ AF5   │ SPI1_SCK  → XMOS SPI Clock   │
│ PA6       │ SPI1  │ AF5   │ SPI1_MISO → XMOS SPI Data In │
│ PA7       │ SPI1  │ AF5   │ SPI1_MOSI → XMOS SPI Data Out│
│ PA4       │ GPIO  │ -     │ SPI1_CS   → XMOS SPI CS       │
│ PB6       │ I2C1  │ AF4   │ I2C1_SCL  → Display/Codec     │
│ PB7       │ I2C1  │ AF4   │ I2C1_SDA  → Display/Codec     │
│ PA9       │ USART1│ AF7   │ USART1_TX → Debug Console     │
│ PA10      │ USART1│ AF7   │ USART1_RX → Debug Console     │
│ PC0       │ ADC1  │ -     │ ADC_CH0   → Voltage Monitor   │
│ PC1       │ ADC1  │ -     │ ADC_CH1   → Temperature       │
│ PC2       │ ADC1  │ -     │ ADC_CH2   → Current Sense     │
│ PD0       │ GPIO  │ -     │ STATUS_LED → Status LED       │
│ PD1       │ GPIO  │ -     │ ERROR_LED  → Error LED        │
│ PD2       │ GPIO  │ -     │ POWER_EN   → Power Enable     │
│ PD3       │ GPIO  │ -     │ RESET_BTN  → Reset Button     │
│ PD4       │ EXTI  │ -     │ XMOS_IRQ   → XMOS Interrupt   │
│ PD5       │ GPIO  │ -     │ XMOS_RST   → XMOS Reset       │
│ PE0       │ TIM1  │ AF1   │ PWM_OUT    → Fan Control      │
└───────────┴───────┴───────┴───────────────────────────────┘
```

### SPI Communication Protocol

```c
// SPI master driver - XMOS ile iletişim
// Mode 0 (CPOL=0, CPHA=0), 10 MHz

#define SPI_SPEED         10000000  // 10 MHz
#define SPI_TIMEOUT       1000      // 1ms timeout

// SPI command format
typedef struct {
    uint8_t command;        // Command byte
    uint8_t address;        // Address byte
    uint8_t length;         // Data length (0-64)
    uint8_t data[64];       // Payload data
    uint16_t crc16;         // CRC16 checksum
} spi_packet_t;

// Command definitions
#define CMD_READ_REGISTER   0x01
#define CMD_WRITE_REGISTER  0x02
#define CMD_READ_AUDIO      0x10
#define CMD_WRITE_AUDIO     0x11
#define CMD_SET_VOLUME      0x20
#define CMD_SET_SAMPLE_RATE 0x21
#define CMD_GET_STATUS      0x30
#define CMD_RESET           0xFF

// SPI transaction
int spi_transfer(spi_packet_t *tx, spi_packet_t *rx) {
    uint8_t tx_buf[70];
    uint8_t rx_buf[70];
    int ret;

    // TX packet'i byte dizisine çevir
    tx_buf[0] = tx->command;
    tx_buf[1] = tx->address;
    tx_buf[2] = tx->length;
    for (int i = 0; i < tx->length; i++) {
        tx_buf[3 + i] = tx->data[i];
    }

    // CRC hesapla
    tx->crc16 = crc16(tx_buf, 3 + tx->length);
    tx_buf[3 + tx->length] = (tx->crc16 >> 8) & 0xFF;
    tx_buf[4 + tx->length] = tx->crc16 & 0xFF;

    // SPI transfer
    HAL_GPIO_WritePin(SPI_CS_PORT, SPI_CS_PIN, GPIO_PIN_RESET);
    ret = HAL_SPI_TransmitReceive(&hspi1, tx_buf, rx_buf,
                                   5 + tx->length, SPI_TIMEOUT);
    HAL_GPIO_WritePin(SPI_CS_PORT, SPI_CS_PIN, GPIO_PIN_SET);

    if (ret != HAL_OK) {
        return -1;
    }

    // RX packet parse
    rx->command = rx_buf[0];
    rx->address = rx_buf[1];
    rx->length = rx_buf[2];
    for (int i = 0; i < rx->length; i++) {
        rx->data[i] = rx_buf[3 + i];
    }

    // CRC doğrulama
    uint16_t rx_crc = (rx_buf[3 + rx->length] << 8) | rx_buf[4 + rx->length];
    if (crc16(rx_buf, 3 + rx->length) != rx_crc) {
        return -2;  // CRC error
    }

    return 0;
}

// XMOS ile High-level communication
int xmos_set_volume(uint8_t volume) {
    spi_packet_t tx, rx;
    tx.command = CMD_SET_VOLUME;
    tx.address = 0x00;
    tx.length = 1;
    tx.data[0] = volume;
    return spi_transfer(&tx, &rx);
}

int xmos_set_sample_rate(uint32_t sample_rate) {
    spi_packet_t tx, rx;
    tx.command = CMD_SET_SAMPLE_RATE;
    tx.address = 0x00;
    tx.length = 4;
    tx.data[0] = (sample_rate >> 24) & 0xFF;
    tx.data[1] = (sample_rate >> 16) & 0xFF;
    tx.data[2] = (sample_rate >> 8) & 0xFF;
    tx.data[3] = sample_rate & 0xFF;
    return spi_transfer(&tx, &rx);
}

int xmos_get_status(uint8_t *status) {
    spi_packet_t tx, rx;
    tx.command = CMD_GET_STATUS;
    tx.address = 0x00;
    tx.length = 0;
    int ret = spi_transfer(&tx, &rx);
    if (ret == 0) {
        *status = rx.data[0];
    }
    return ret;
}
```

### System Health Monitoring

```c
// STM32 system health monitoring
// Voltage, temperature, current monitoring

#define ADC_CHANNELS    3
#define VOLTAGE_CHANNEL 0
#define TEMP_CHANNEL    1
#define CURRENT_CHANNEL 2

// ADC okuma fonksiyonu
uint16_t adc_read(uint32_t channel) {
    ADC_ChannelConfTypeDef sConfig = {0};
    sConfig.Channel = channel;
    sConfig.Rank = 1;
    sConfig.SamplingTime = ADC_SAMPLETIME_84CYCLES;
    HAL_ADC_ConfigChannel(&hadc1, &sConfig);

    HAL_ADC_Start(&hadc1);
    HAL_ADC_PollForConversion(&hadc1, 10);
    uint16_t value = HAL_ADC_GetValue(&hadc1);
    HAL_ADC_Stop(&hadc1);

    return value;
}

// Voltage monitoring (0-5V range)
float read_voltage() {
    uint16_t raw = adc_read(VOLTAGE_CHANNEL);
    float voltage = (raw / 4095.0f) * 3.3f * 2.0f;  // Voltage divider
    return voltage;
}

// Temperature monitoring (NTC thermistor)
float read_temperature() {
    uint16_t raw = adc_read(TEMP_CHANNEL);
    float voltage = (raw / 4095.0f) * 3.3f;

    // NTC resistance calculation
    float resistance = 10000.0f * voltage / (3.3f - voltage);

    // Steinhart-Hart equation (simplified)
    float temp_k = 1.0f / (0.0011276f + (0.000234f * logf(resistance)));
    float temp_c = temp_k - 273.15f;

    return temp_c;
}

// Current monitoring
float read_current() {
    uint16_t raw = adc_read(CURRENT_CHANNEL);
    float voltage = (raw / 4095.0f) * 3.3f;

    // INA199 current sense amplifier
    // Gain = 50, Rsense = 0.01Ω
    float current = voltage / (50.0f * 0.01f);

    return current;
}

// System health check
typedef struct {
    float voltage;
    float temperature;
    float current;
    uint32_t uptime;
    uint8_t xmOS_status;
    uint8_t error_flags;
} system_health_t;

void system_health_check(system_health_t *health) {
    health->voltage = read_voltage();
    health->temperature = read_temperature();
    health->current = read_current();
    health->uptime = HAL_GetTick() / 1000;

    // XMOS status oku
    xmos_get_status(&health->xmOS_status);

    // Error flag kontrolü
    health->error_flags = 0;

    // Under-voltage check
    if (health->voltage < 10.0f) {
        health->error_flags |= (1 << 0);
    }

    // Over-temperature check
    if (health->temperature > 80.0f) {
        health->error_flags |= (1 << 1);
    }

    // Over-current check
    if (health->current > 5.0f) {
        health->error_flags |= (1 << 2);
    }
}
```

### Firmware Update Orchestration

```c
// Firmware update orchestration
// STM32 updates XMOS firmware via SPI/DFU

#define FW_UPDATE_IDLE       0
#define FW_UPDATE_ERASE      1
#define FW_UPDATE_WRITE      2
#define FW_UPDATE_VERIFY     3
#define FW_UPDATE_COMPLETE   4
#define FW_UPDATE_ERROR      5

typedef struct {
    uint8_t state;
    uint32_t total_size;
    uint32_t written_size;
    uint32_t crc32;
    uint8_t error_code;
} fw_update_state_t;

// Firmware update başlama
int fw_update_start(fw_update_state_t *state, uint32_t size, uint32_t crc) {
    if (state->state != FW_UPDATE_IDLE) {
        return -1;  // Busy
    }

    state->state = FW_UPDATE_ERASE;
    state->total_size = size;
    state->written_size = 0;
    state->crc32 = crc;
    state->error_code = 0;

    // XMOS'u DFU mode'a al
    xmos_enter_dfu_mode();

    // Flash erase
    int ret = xmos_erase_flash();
    if (ret != 0) {
        state->state = FW_UPDATE_ERROR;
        state->error_code = 1;
        return -1;
    }

    state->state = FW_UPDATE_WRITE;
    return 0;
}

// Firmware verisi yazma
int fw_update_write_chunk(fw_update_state_t *state,
                          const uint8_t *data,
                          uint32_t size) {
    if (state->state != FW_UPDATE_WRITE) {
        return -1;
    }

    // CRC doğrulama (chunk-level)
    // SPI ile XMOS'a gönder
    spi_packet_t tx, rx;
    tx.command = CMD_WRITE_AUDIO;
    tx.address = 0x00;
    tx.length = size;

    for (int i = 0; i < size; i++) {
        tx.data[i] = data[i];
    }

    int ret = spi_transfer(&tx, &rx);
    if (ret != 0) {
        state->state = FW_UPDATE_ERROR;
        state->error_code = 2;
        return -1;
    }

    state->written_size += size;

    // Tüm veri yazıldı mı?
    if (state->written_size >= state->total_size) {
        state->state = FW_UPDATE_VERIFY;
    }

    return 0;
}

// Firmware doğrulama
int fw_update_verify(fw_update_state_t *state) {
    if (state->state != FW_UPDATE_VERIFY) {
        return -1;
    }

    // CRC32 doğrulama
    uint32_t actual_crc;
    int ret = xmos_read_crc(&actual_crc);
    if (ret != 0 || actual_crc != state->crc32) {
        state->state = FW_UPDATE_ERROR;
        state->error_code = 3;
        return -1;
    }

    // Başarılı - normal mode'a dön
    xmos_exit_dfu_mode();

    state->state = FW_UPDATE_COMPLETE;
    return 0;
}

// Hata durumunda rollback
int fw_update_rollback(fw_update_state_t *state) {
    // Backup bank'a dön
    xmos_exit_dfu_mode();
    xmos_select_backup_bank();

    state->state = FW_UPDATE_IDLE;
    state->error_code = 0;
    return 0;
}
```

### Raspberry Pi Control Interface

```c
// Raspberry Pi - XMOS control interface
// SPI master olarak STM32 ile iletişim

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <fcntl.h>
#include <sys/ioctl.h>
#include <linux/spi/spidev.h>

#define SPI_DEVICE      "/dev/spidev0.0"
#define SPI_SPEED       10000000  // 10 MHz
#define SPI_MODE        SPI_MODE_0

// SPI file descriptor
int spi_fd;

// SPI başlatma
int spi_init() {
    spi_fd = open(SPI_DEVICE, O_RDWR);
    if (spi_fd < 0) {
        perror("SPI open failed");
        return -1;
    }

    // SPI mode ayarla
    uint8_t mode = SPI_MODE;
    ioctl(spi_fd, SPI_IOC_WR_MODE, &mode);

    // Bits per word
    uint8_t bits = 8;
    ioctl(spi_fd, SPI_IOC_WR_BITS_PER_WORD, &bits);

    // Max speed
    uint32_t speed = SPI_SPEED;
    ioctl(spi_fd, SPI_IOC_WR_MAX_SPEED_HZ, &speed);

    return 0;
}

// SPI transfer
int spi_transfer_data(uint8_t *tx, uint8_t *rx, int length) {
    struct spi_ioc_transfer tr = {
        .tx_buf = (unsigned long)tx,
        .rx_buf = (unsigned long)rx,
        .len = length,
        .speed_hz = SPI_SPEED,
        .bits_per_word = 8,
        .delay_usecs = 0,
    };

    int ret = ioctl(spi_fd, SPI_IOC_MESSAGE(1), &tr);
    return (ret < 0) ? -1 : 0;
}

// XMOS komut gönderme
int xmos_send_command(uint8_t cmd, uint8_t *data, int data_len) {
    uint8_t tx[70];
    uint8_t rx[70];

    tx[0] = cmd;
    tx[1] = 0x00;  // Address
    tx[2] = data_len;
    for (int i = 0; i < data_len; i++) {
        tx[3 + i] = data[i];
    }

    // CRC ekle
    uint16_t crc = crc16(tx, 3 + data_len);
    tx[3 + data_len] = (crc >> 8) & 0xFF;
    tx[4 + data_len] = crc & 0xFF;

    int ret = spi_transfer_data(tx, rx, 5 + data_len);
    if (ret != 0) return -1;

    // Response parse
    if (rx[0] != cmd) return -2;  // Yanlış yanıt
    return 0;
}

// Web interface server
// Flask/Express benzeri basit HTTP server
void start_web_server(int port) {
    int server_fd, client_fd;
    struct sockaddr_in address;

    server_fd = socket(AF_INET, SOCK_STREAM, 0);
    address.sin_family = AF_INET;
    address.sin_addr.s_addr = INADDR_ANY;
    address.sin_port = htons(port);

    bind(server_fd, (struct sockaddr *)&address, sizeof(address));
    listen(server_fd, 5);

    printf("Web server started on port %d\n", port);

    while (1) {
        client_fd = accept(server_fd, NULL, NULL);
        // HTTP request handler
        handle_http_request(client_fd);
        close(client_fd);
    }
}

// HTTP request handler
void handle_http_request(int client_fd) {
    char buffer[1024];
    read(client_fd, buffer, sizeof(buffer));

    // Parse request
    if (strstr(buffer, "GET /api/status")) {
        // System status JSON
        system_health_t health;
        system_health_check(&health);

        char json[256];
        snprintf(json, sizeof(json),
                 "{\"voltage\":%.2f,\"temperature\":%.1f,"
                 "\"current\":%.2f,\"uptime\":%lu}",
                 health.voltage, health.temperature,
                 health.current, health.uptime);

        // HTTP response
        char response[512];
        snprintf(response, sizeof(response),
                 "HTTP/1.1 200 OK\r\n"
                 "Content-Type: application/json\r\n"
                 "Content-Length: %d\r\n"
                 "\r\n%s", strlen(json), json);

        write(client_fd, response, strlen(response));
    }
    else if (strstr(buffer, "GET /api/volume")) {
        // Volume ayarlama
        // Query string'den volume değerini al
        // xmos_send_command ile XMOS'a gönder
    }
    else if (strstr(buffer, "POST /api/update")) {
        // Firmware update endpoint
        // Multipart form data parse
        // fw_update_start ile güncelleme başlat
    }
}
```

### Network Manager

```c
// Network management - WiFi/Ethernet
// mDNS discovery, DHCP, static IP

#include <net/if.h>
#include <ifaddrs.h>

// Network interface enum
typedef enum {
    NET_ETH0,
    NET_WLAN0,
    NET_USB0
} net_interface_t;

// Network status
typedef struct {
    char ip_address[16];
    char mac_address[18];
    int is_connected;
    net_interface_t active_interface;
} network_status_t;

// IP address okuma
int get_ip_address(net_interface_t iface, char *ip, int ip_len) {
    struct ifaddrs *ifaddr;
    struct ifaddrs *ifa;

    getifaddrs(&ifaddr);

    for (ifa = ifaddr; ifa != NULL; ifa = ifa->ifa_next) {
        if (ifa->ifa_addr == NULL) continue;

        if (ifa->ifa_addr->sa_family == AF_INET) {
            if ((iface == NET_ETH0 && strcmp(ifa->ifa_name, "eth0") == 0) ||
                (iface == NET_WLAN0 && strcmp(ifa->ifa_name, "wlan0") == 0)) {
                struct sockaddr_in *addr = (struct sockaddr_in *)ifa->ifa_addr;
                inet_ntop(AF_INET, &addr->sin_addr, ip, ip_len);
                freeifaddrs(ifaddr);
                return 0;
            }
        }
    }

    freeifaddrs(ifaddr);
    return -1;
}

// mDNS service registration
// coremusic.local ile erişim
void register_mdns_service() {
    // avahi-daemon veya mDNSResponder
    // _http._tcp service registration
    // coremusic.local hostname
}

// DHCP client management
void manage_dhcp() {
    // dhcpcd servisi
    // IP lease takibi
    // Fallback static IP
}
```

### Configuration Management

```c
// Konfigürasyon yönetimi
// JSON-based configuration dosyası

#define CONFIG_FILE     "/etc/coremusic/config.json"
#define CONFIG_MAX_SIZE 4096

// Config yapısı
typedef struct {
    char device_name[32];
    int sample_rate;
    int bit_depth;
    int volume;
    int brightness;
    char wifi_ssid[32];
    char wifi_password[64];
    char static_ip[16];
    int dhcp_enabled;
    int auto_update;
} coremusic_config_t;

// Config okuma
int config_load(coremusic_config_t *config) {
    FILE *f = fopen(CONFIG_FILE, "r");
    if (!f) {
        // Varsayılan config
        config_defaults(config);
        return -1;
    }

    char buffer[CONFIG_MAX_SIZE];
    fread(buffer, 1, CONFIG_MAX_SIZE, f);
    fclose(f);

    // JSON parse (cJSON library)
    cJSON *json = cJSON_Parse(buffer);
    if (!json) {
        config_defaults(config);
        return -1;
    }

    // Field'ları oku
    cJSON *item;
    item = cJSON_GetObjectItem(json, "device_name");
    if (item) strncpy(config->device_name, item->valuestring, 31);

    item = cJSON_GetObjectItem(json, "sample_rate");
    if (item) config->sample_rate = item->valueint;

    item = cJSON_GetObjectItem(json, "volume");
    if (item) config->volume = item->valueint;

    // ... diğer field'lar

    cJSON_Delete(json);
    return 0;
}

// Config yazma
int config_save(coremusic_config_t *config) {
    cJSON *json = cJSON_CreateObject();

    cJSON_AddStringToObject(json, "device_name", config->device_name);
    cJSON_AddNumberToObject(json, "sample_rate", config->sample_rate);
    cJSON_AddNumberToObject(json, "volume", config->volume);
    cJSON_AddNumberToObject(json, "brightness", config->brightness);
    cJSON_AddStringToObject(json, "wifi_ssid", config->wifi_ssid);
    cJSON_AddNumberToObject(json, "dhcp_enabled", config->dhcp_enabled);
    cJSON_AddNumberToObject(json, "auto_update", config->auto_update);

    char *json_str = cJSON_Print(json);
    cJSON_Delete(json);

    FILE *f = fopen(CONFIG_FILE, "w");
    if (!f) return -1;
    fwrite(json_str, 1, strlen(json_str), f);
    fclose(f);

    free(json_str);
    return 0;
}

// Varsayılan config
void config_defaults(coremusic_config_t *config) {
    strcpy(config->device_name, "COREMUSIC");
    config->sample_rate = 48000;
    config->bit_depth = 24;
    config->volume = 80;
    config->brightness = 100;
    strcpy(config->wifi_ssid, "");
    strcpy(config->wifi_password, "");
    strcpy(config->static_ip, "192.168.1.100");
    config->dhcp_enabled = 1;
    config->auto_update = 1;
}
```

### UART Debug Console

```c
// UART debug console
// STM32 USART1 - 115200 baud

#define UART_BAUDRATE    115200
#define UART_BUFFER_SIZE 256

// UART RX buffer
char uart_rx_buffer[UART_BUFFER_SIZE];
int uart_rx_head = 0;
int uart_rx_tail = 0;

// UART RX interrupt handler
void USART1_IRQHandler(void) {
    if (__HAL_UART_GET_FLAG(&huart1, UART_FLAG_RXNE)) {
        char c = huart1.Instance->DR;
        uart_rx_buffer[uart_rx_head] = c;
        uart_rx_head = (uart_rx_head + 1) % UART_BUFFER_SIZE;
    }
}

// UART okuma (non-blocking)
int uart_read_char(char *c) {
    if (uart_rx_head == uart_rx_tail) {
        return -1;  // No data
    }
    *c = uart_rx_buffer[uart_rx_tail];
    uart_rx_tail = (uart_rx_tail + 1) % UART_BUFFER_SIZE;
    return 0;
}

// Debug output
void debug_printf(const char *format, ...) {
    char buffer[256];
    va_list args;
    va_start(args, format);
    vsnprintf(buffer, sizeof(buffer), format, args);
    va_end(args);

    HAL_UART_Transmit(&huart1, (uint8_t *)buffer, strlen(buffer), 100);
}

// Debug komut处理
void debug_process_commands() {
    char cmd[64];
    int cmd_len = 0;

    while (1) {
        char c;
        if (uart_read_char(&c) == 0) {
            if (c == '\n' || c == '\r') {
                cmd[cmd_len] = '\0';
                debug_handle_command(cmd);
                cmd_len = 0;
            } else if (cmd_len < 63) {
                cmd[cmd_len++] = c;
            }
        }
    }
}

// Debug komut handlers
void debug_handle_command(const char *cmd) {
    if (strcmp(cmd, "status") == 0) {
        system_health_t health;
        system_health_check(&health);
        debug_printf("Voltage: %.2fV\n", health.voltage);
        debug_printf("Temperature: %.1f°C\n", health.temperature);
        debug_printf("Current: %.2fA\n", health.current);
        debug_printf("Uptime: %lu sec\n", health.uptime);
    }
    else if (strcmp(cmd, "reset") == 0) {
        debug_printf("System reset...\n");
        NVIC_SystemReset();
    }
    else if (strncmp(cmd, "vol ", 4) == 0) {
        int vol = atoi(cmd + 4);
        xmos_set_volume(vol);
        debug_printf("Volume set to %d\n", vol);
    }
    else if (strncmp(cmd, "rate ", 5) == 0) {
        int rate = atoi(cmd + 5);
        xmos_set_sample_rate(rate);
        debug_printf("Sample rate set to %d\n", rate);
    }
    else {
        debug_printf("Unknown command: %s\n", cmd);
        debug_printf("Available: status, reset, vol <0-100>, rate <44100|48000|96000|192000>\n");
    }
}
```

## Derleme & Yükleme

### STM32 Firmware Derleme

```bash
cd firmware/mcu_support/stm32

# ARM GCC toolchain ile derleme
make clean
make all

# Veya STM32CubeIDE ile
# Project → Build All

# Derleme çıktısı
# build/coremusic_stm32.elf
# build/coremusic_stm32.bin
# build/coremusic_stm32.hex
```

### STM32 Firmware Yükleme

```bash
# ST-Link ile yükleme
openocd -f interface/stlink.cfg -f target/stm32f4x.cfg \
  -c "program build/coremusic_stm32.elf verify reset exit"

# dfu-util ile yükleme (DFU modunda)
dfu-util -a 0 -D build/coremusic_stm32.bin

# STM32CubeProgrammer GUI
# firmware选择 → Download → Start
```

### Raspberry Pi Firmware Derleme

```bash
cd firmware/mcu_support/rpi

# CMake ile derleme
mkdir build && cd build
cmake ..
make -j$(nproc)

# Veya make ile
make clean
make all

# Kurulum
sudo make install
sudo systemctl enable coremusic
sudo systemctl start coremusic
```

### Raspberry Pi Kurulum

```bash
# Bağımlılıklar
sudo apt update
sudo apt install -y build-essential cmake libjson-c-dev \
  libwebsockets-dev libavahi-compat-libdnssd-dev

# SPI enable
sudo raspi-config
# Interface Options → SPI → Enable

# I2C enable
sudo raspi-config
# Interface Options → I2C → Enable

# coremusic servisi
sudo cp config/coremusic.conf /etc/coremusic/
sudo cp build/coremusic /usr/local/bin/
sudo systemctl daemon-reload
```

### Entegrasyon Testi

```bash
# STM32-XMOS SPI communication test
# Logic analyzer ile SPI trafiği monitoring

# RPi-STM32 SPI test
# /dev/spidev0.0 üzerinden test

# End-to-end test
# USB input → DSP → I2S output loopback test
```

## Bağımlılıklar

| Bağımlılık | Versiyon | Amaç |
|-----------|----------|------|
| STM32 HAL | >= 1.25.x | STM32 donanım soyutlama |
| FreeRTOS | >= 10.x | STM32 gerçek zamanlı OS |
| cJSON | >= 1.7.x | JSON parsing (RPi) |
| libwebsockets | >= 4.x | WebSocket support (RPi) |
| Avahi | >= 0.8.x | mDNS service (RPi) |
| OpenSSL | >= 1.1.x | TLS support (RPi) |
| GCC ARM | >= 10.x | STM32 cross-compiler |
| GCC | >= 10.x | RPi native compiler |
| CMake | >= 3.15 | Build sistemi |

## Durum: Implementasyon

| Modül | Durum | Açıklama |
|-------|-------|----------|
| STM32 SPI Driver | Planlandı | XMOS SPI communication |
| STM32 GPIO Control | Planlandı | Hardware GPIO management |
| STM32 ADC Monitor | Planlandı | Voltage/current/temp |
| STM32 Firmware Update | Planlandı | DFU orchestration |
| RPi XMOS Interface | Planlandı | SPI control interface |
| RPi Web Server | Planlandı | HTTP control API |
| RPi Display Server | Planlandı | HDMI/eDP display |
| RPi Network Manager | Planlandı | WiFi/Ethernet management |
| RPi MQTT Client | Planlandı | IoT communication |
| RPi Config Manager | Planlandı | JSON configuration |
| UART Debug Console | Planlandı | Debug output |
