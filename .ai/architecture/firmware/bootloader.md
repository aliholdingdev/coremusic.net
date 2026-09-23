---
title: "Bootloader & DFU Sistemi"
layer: Firmware
category: "Firmware"
date: 2026-09-20
---

# Bootloader & DFU Sistemi

## Genel Bakış

COREMUSIC Bootloader sistemi, firmware güncellemelerini güvenli ve hatasız şekilde gerçekleştirmek için tasarlanmıştır. USB DFU (Device Firmware Upgrade) protokolü ile firmware yükleme, dual-bank koruma mekanizması ile brick riskini önler. XMOS XU316 ve STM32 için ayrı bootloader implementasyonları mevcuttur.

## Firmware Mimarisi

```
┌─────────────────────────────────────────────────────────────────┐
│                  BOOTLOADER MİMARİSİ                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────────────────────────────────────────────┐      │
│  │                USB DFU Interface                      │      │
│  │                (dfu-util compatible)                  │      │
│  └──────────────────┬───────────────────────────────────┘      │
│                     │                                           │
│  ┌──────────────────▼───────────────────────────────────┐      │
│  │              DFU Protocol Handler                    │      │
│  │              - Detach / Download / Upload             │      │
│  │              - Manifest verification                  │      │
│  └──────────────────┬───────────────────────────────────┘      │
│                     │                                           │
│  ┌──────────────────▼───────────────────────────────────┐      │
│  │              Flash Manager                           │      │
│  │              - Dual-bank support                      │      │
│  │              - CRC32 verification                     │      │
│  │              - Erase / Program / Verify               │      │
│  └──────────────────┬───────────────────────────────────┘      │
│                     │                                           │
│  ┌──────────────────▼───────────────────────────────────┐      │
│  │              Boot Manager                            │      │
│  │              - Primary / Backup bank selection        │      │
│  │              - Watchdog timer                         │      │
│  │              - Recovery mode                          │      │
│  └──────────────────────────────────────────────────────┘      │
│                                                                 │
│  ┌──────────────────────────────────────────────────────┐      │
│  │              Flash Memory Layout                     │      │
│  │  ┌──────────┬──────────┬──────────┬──────────┐      │      │
│  │  │ Boot     │ Primary  │ Backup   │ Config   │      │      │
│  │  │ Loader   │ Bank A   │ Bank B   │ Sector   │      │      │
│  │  │ 64KB    │ 512KB    │ 512KB    │ 64KB     │      │      │
│  │  └──────────┴──────────┴──────────┴──────────┘      │      │
│  └──────────────────────────────────────────────────────┘      │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

## Kaynak Kod Yapısı

```
bootloader/
├── xmos_bootloader/
│   ├── src/
│   │   ├── bootloader_main.xc      # Ana bootloader programı
│   │   ├── dfu_handler.xc          # DFU protokol işleyicisi
│   │   ├── flash_manager.xc        # Flash okuma/yazma
│   │   ├── crc32.c                 # CRC32 hesaplama
│   │   └── boot_select.xc          # Boot bank seçimi
│   ├── module_bootloader/          # Bootloader modülü
│   └── Makefile
│
├── stm32_bootloader/
│   ├── Core/Src/
│   │   ├── main.c                  # Ana bootloader
│   │   ├── dfu_core.c             # DFU çekirdek
│   │   ├── flash_driver.c         # Flash sürücü
│   │   └── usb_dfu.c             # USB DFU interface
│   └── Makefile
│
└── tools/
    ├── dfu_util/                   # DFU yükleme aracı
    └── firmware_sign/              # Firmware imzalama
```

## Teknik Detaylar

### Boot Sequence

```
1. Power-On Reset (POR)
2. Watchdog timer başlat
3. Flash'ı oku (boot bank seçimi)
4. CRC32 doğrulama
5. Geçerli firmware varsa:
   → Jump to application
6. Geçerli firmware yoksa:
   → DFU mode'a geç
7. USB DFU interface başlat
8. Firmware yüklemeyi bekle
```

### Dual-Bank Firmware Update

```
Normal Durum:
┌──────────┬──────────┬──────────┐
│ Bootloader│ Bank A   │ Bank B   │
│ (Valid)  │ (Active) │ (Backup) │
└──────────┴──────────┴──────────┘

Update Başlatıldığında:
┌──────────┬──────────┬──────────┐
│ Bootloader│ Bank A   │ Bank B   │
│ (Valid)  │ (Active) │ (Writing)│
└──────────┴──────────┴──────────┘

Update Başarılı:
┌──────────┬──────────┬──────────┐
│ Bootloader│ Bank A   │ Bank B   │
│ (Valid)  │ (Backup) │ (Active) │
└──────────┴──────────┴──────────┘

Update Başarısız (Rollback):
┌──────────┬──────────┬──────────┐
│ Bootloader│ Bank A   │ Bank B   │
│ (Valid)  │ (Active) │ (Invalid)│
└──────────┴──────────┴──────────┘
```

### DFU Protocol

```
USB Setup Packet:
┌───────────────────────────────────────────────┐
│ bmRequestType │ bRequest │ wValue │ wIndex   │
├───────────────┼──────────┼────────┼──────────┤
│ 0x21          │ DFU_DNLOAD│ Block  │ 0        │
│ 0xA1          │ DFU_UPLOAD│ Block  │ 0        │
│ 0x21          │ DFU_CLRSTATUS│ 0   │ 0        │
│ 0xA1          │ DFU_GETSTATUS│ 0   │ 0        │
│ 0x21          │ DFU_DETACH │ 0    │ 0        │
└───────────────────────────────────────────────┘

DFU State Machine:
  appIDLE → appDETACH → dfuIDLE → dfuDNLOAD-SYNC
  → dfuDNLOAD-BUSY → dfuDNLOAD-IDLE → dfuMANIFEST-SYNC
  → dfuMANIFEST → dfuMANIFEST-RESET → dfuIDLE
```

### XMOS Bootloader Implementasyonu

```xc
// XMOS Bootloader - DFU mode kontrolü
// USB DFU protocol handler

// Boot bank seçimi
// Bank A: 0x00000000 - 0x0007FFFF (512KB)
// Bank B: 0x00080000 - 0x000FFFFF (512KB)

#define BANK_A_START    0x00000000
#define BANK_A_SIZE     0x80000
#define BANK_B_START    0x00080000
#define BANK_B_SIZE     0x80000
#define CONFIG_START    0x00100000

// Boot parameter yapısı
typedef struct {
    uint32_t magic;         // 0x434F5245 ("CORE")
    uint32_t active_bank;   // 0 = Bank A, 1 = Bank B
    uint32_t boot_count;    // Başarısız boot sayısı
    uint32_t crc32;         // CRC32 checksum
} boot_params_t;

// Boot sequence
void bootloader_main() {
    boot_params_t params;
    uint32_t valid_bank;

    // Boot parameters oku
    read_boot_params(&params);

    // CRC32 doğrulama
    if (!verify_crc32(&params)) {
        // CRC hatası - DFU mode'a geç
        enter_dfu_mode();
        return;
    }

    // Geçerli bank seçimi
    valid_bank = select_valid_bank(&params);

    if (valid_bank == INVALID_BANK) {
        // Hiçbir bank geçerli değil - DFU mode
        enter_dfu_mode();
        return;
    }

    // Boot count artır
    params.boot_count++;
    if (params.boot_count > MAX_BOOT_ATTEMPTS) {
        // Çok fazla başarısız boot - DFU mode
        enter_dfu_mode();
        return;
    }

    // Jump to application
    jump_to_application(valid_bank);
}
```

### Flash Manager

```c
// Flash management fonksiyonları

// Flash erase - belirli bir bankı temizle
int flash_erase_bank(uint32_t bank_addr, uint32_t size) {
    int ret;

    // Flash erase için unlock
    ret = flash_unlock();
    if (ret != 0) return ret;

    // Sector-by-sector erase
    for (uint32_t addr = bank_addr; addr < bank_addr + size; addr += SECTOR_SIZE) {
        ret = flash_erase_sector(addr);
        if (ret != 0) {
            flash_lock();
            return ret;
        }
    }

    flash_lock();
    return 0;
}

// Flash write - firmware verisini yaz
int flash_write_firmware(uint32_t bank_addr, const uint8_t *data, uint32_t size) {
    int ret;
    uint32_t offset = 0;

    // Flash erase
    ret = flash_erase_bank(bank_addr, size);
    if (ret != 0) return ret;

    // Word-by-word write (32-bit aligned)
    while (offset < size) {
        uint32_t word = 0;
        for (int i = 0; i < 4 && offset < size; i++) {
            word |= data[offset] << (i * 8);
            offset++;
        }

        ret = flash_program_word(bank_addr + offset - 4, word);
        if (ret != 0) return ret;
    }

    return 0;
}

// CRC32 hesaplama
uint32_t calculate_crc32(const uint8_t *data, uint32_t size) {
    uint32_t crc = 0xFFFFFFFF;
    for (uint32_t i = 0; i < size; i++) {
        crc ^= data[i];
        for (int j = 0; j < 8; j++) {
            crc = (crc >> 1) ^ (0xEDB88320 & (-(crc & 1)));
        }
    }
    return ~crc;
}
```

### USB DFU Handler

```c
// USB DFU request handler
// dfu-util ile uyumlu

typedef enum {
    DFU_STATE_appIDLE = 0,
    DFU_STATE_appDETACH,
    DFU_STATE_dfuIDLE,
    DFU_STATE_dfuDNLOAD-SYNC,
    DFU_STATE_dfuDNLOAD-BUSY,
    DFU_STATE_dfuDNLOAD-IDLE,
    DFU_STATE_dfuMANIFEST-SYNC,
    DFU_STATE_dfuMANIFEST,
    DFU_STATE_dfuMANIFEST-RESET
} dfu_state_t;

typedef enum {
    DFU_STATUS_OK = 0,
    DFU_STATUS_errTARGET,
    DFU_STATUS_errFILE,
    DFU_STATUS_errWRITE,
    DFU_STATUS_errERASE,
    DFU_STATUS_errCHECK_ERASED,
    DFU_STATUS_errPROG,
    DFU_STATUS_errVERIFY,
    DFU_STATUS_errADDRESS,
    DFU_STATUS_errNOTDONE,
    DFU_STATUS_errFIRMWARE,
    DFU_STATUS_errVENDOR,
    DFU_STATUS_errUSBR,
    DFU_STATUS_errPOR,
    DFU_STATUS_errUNKNOWN,
    DFU_STATUS_errSTALLEDPKT
} dfu_status_t;

// DFU DNLOAD handler
int handle_dfu_download(uint16_t wValue, const uint8_t *data, uint16_t length) {
    static uint32_t offset = 0;

    // Block 0 = firmware manifest
    if (wValue == 0) {
        // Manifest parse
        manifest_t manifest;
        parse_manifest(data, length, &manifest);

        // Bank seçimi
        uint32_t target_bank = select_target_bank();
        if (target_bank == INVALID_BANK) {
            return DFU_STATUS_errADDRESS;
        }

        offset = 0;
        current_bank = target_bank;
        return DFU_STATUS_OK;
    }

    // Firmware verisi
    if (current_bank == INVALID_BANK) {
        return DFU_STATUS_errUNKNOWN;
    }

    // Flash'a yaz
    int ret = flash_write_chunk(current_bank + offset, data, length);
    if (ret != 0) {
        return DFU_STATUS_errPROG;
    }

    offset += length;
    return DFU_STATUS_OK;
}

// DFU UPLOAD handler
int handle_dfu_upload(uint16_t wValue, uint8_t *data, uint16_t length) {
    // Mevcut firmware'yi oku (backup için)
    uint32_t source_bank = get_active_bank();
    flash_read(source_bank + wValue * length, data, length);
    return DFU_STATUS_OK;
}
```

### Reboot & Recovery

```c
// Sistem yeniden başlatma ve kurtarma
// Watchdog timer ile güvenli reboot

#define MAX_BOOT_ATTEMPTS   3
#define WATCHDOG_TIMEOUT    10000   // 10 saniye

// Reboot fonksiyonu
void system_reboot(int enter_dfu) {
    if (enter_dfu) {
        // DFU mode'a geçerek yeniden başlat
        set_boot_flag(BOOT_FLAG_DFU);
    } else {
        // Normal yeniden başlatma
        clear_boot_flag();
    }

    // Watchdog ile reboot
    watchdog_enable(WATCHDOG_TIMEOUT);
    while (1);  // Watchdog timeout'u tetikler
}

// Kurtarma modu
void enter_recovery_mode() {
    // Minimal firmware yükle
    // USB DFU interface başlat
    // Kullanıcıya "firmware update" modu sun

    led_pattern_set(LED_PATTERN_RECOVERY);  // Kırmızı LED yanıp sönme
    usb_dfu_init();

    while (1) {
        usb_dfu_poll();
    }
}
```

## Derleme & Yükleme

### XMOS Bootloader Derleme

```bash
cd firmware/bootloader/xmos_bootloader
xmake clean
xmake all

# Bootloader'ı flash'a yükle
xflash --target XU316 bootloader.xe \
  --boot-partition-size 0x10000 \
  --data partition.bin
```

### STM32 Bootloader Derleme

```bash
cd firmware/bootloader/stm32_bootloader
make clean
make all

# Bootloader'ı flash'a yükle
openocd -f interface/stlink.cfg -f target/stm32f4x.cfg \
  -c "program build/coremusic_bootloader.elf verify reset exit"
```

### DFU ile Firmware Güncelleme

```bash
# DFU moduna geçiş (çalışan firmware'den)
dfu-util -l
# CoreMusic DFU cihazı listelenmeli

# Firmware yükleme
dfu-util -a firmware -D coremusic_firmware.bin

# Firmware yükleme (bank seçimi ile)
dfu-util -a firmware -D coremusic_firmware.bin \
  -S 0x00080000  # Bank B'ye yaz

# Firmware doğrulama
dfu-util -a firmware -U backup_firmware.bin
md5sum coremusic_firmware.bin backup_firmware.bin
```

### XMOS DFU

```bash
# XMOS DFU ile firmware güncelleme
xflash --target XU316 -o coremusic_v2.bin

# DFU modunda yükleme
xflash --target XU316 --upgrade coremusic_v2.bin

# DFU status kontrolü
xflash --target XU316 --status
```

## Bağımlılıklar

| Bağımdılık | Versiyon | Amaç |
|-----------|----------|------|
| dfu-util | >= 0.10 | USB DFU yükleme aracı |
| libusb | >= 1.0 | USB communication |
| XTC Tools | >= 15.x | XMOS bootloader derleme |
| STM32 HAL | >= 1.25.x | STM32 flash driver |
| CRC32 Library | - | Firmware checksum |
| OpenOCD | >= 0.10 | STM32 yükleme |

## Durum: Implementasyon

| Modül | Durum | Açıklama |
|-------|-------|----------|
| XMOS Bootloader | Planlandı | XU316 DFU bootloader |
| STM32 Bootloader | Planlandı | STM32 DFU bootloader |
| Dual-Bank Manager | Planlandı | A/B partition yönetimi |
| CRC32 Verification | Planlandı | Firmware integrity check |
| Recovery Mode | Planlandı | Kurtarma modu |
| DFU Protocol Handler | Planlandı | USB DFU protokolü |
| Boot Parameters | Planlandı | Persistent boot config |
