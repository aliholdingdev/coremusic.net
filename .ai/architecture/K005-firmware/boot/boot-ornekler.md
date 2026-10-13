---
node_id: K005-firmware/boot/boot-ornekler
node_type: module
status: draft
parent_id: K005-firmware
children: []
depends_on:
  - K005-firmware
evidence:
  - K005-firmware/boot/index.md
  - K005-firmware/boot/boot-kapsam.md
  - K005-firmware/boot/boot-dogrulama.md
  - "repo: shared/src/AI/AIOrchestrator.php satır 36 — 'dsp-fw' agent routing kanıtı (firmware != PHP host)"
validation: []
open_questions:
  - "Hedef MCU ailesi ve flaş bellek kapasitesi kesinleşmedi (UNKNOWN) — linker haritası buna göre seçilecek"
  - "JTAG/SWD fiziksel erişim ve telemetri köprüsünün donanım tasarımı .ai/electronics/ altındadır"
group: kod
---

# Boot Alt-Modülü — Örnekler (K005-firmware/boot/boot-ornekler.md)

> Bu dosya, `K005-firmware/boot/` alt-modülünün **örnekler** dokümanıdır.
> Repo'da derlenebilir firmware kaynak kodu **BULUNMAMAKTADIR** (`.cpp`/`.c`/`.h`
> dosyası yok — 2026-10-13 keşifte 0 dosya) ve hedef MCU bilinmiyor (UNKNOWN).
> Bu nedenle aşağıdaki C/C++ parçaları **taslak referanstır**; her parçanın
> başlığında `Kaynak:` etiketi taşır ve kesin dosya konumu iddia edilmez.
> `Dosya:`/`Satır:` referansı bu dosyada KULLANILMAZ (repo kanıtı yok).

## §A Örnek Yapılandırmalar

### §A.1 Bağlantı haritası (linker) taslağı

Aşağıdaki harita, Cortex-M sınıfı bir MCU için tipik boot yerleşimini
referans gösterir. Adresler **örnektir**; gerçek MCU için datasheet'e göre
değişir (hedef MCU UNKNOWN).

```ld
/* Kaynak: linker haritası taslağı — adresler örnek, hedef MCU UNKNOWN */
MEMORY
{
  FLASH (rx)  : ORIGIN = 0x08000000, LENGTH = 512K
  RAM   (rwx) : ORIGIN = 0x20000000, LENGTH = 128K
}

SECTIONS
{
  .isr_vector :
  {
    KEEP(*(.isr_vector))
  } > FLASH

  .text :
  {
    *(.text*)
    *(.rodata*)
    . = ALIGN(4);
  } > FLASH

  .data : AT (ADDR(.text) + SIZEOF(.text))
  {
    _sdata = .;
    *(.data*)
    . = ALIGN(4);
    _edata = .;
  } > RAM AT > FLASH

  .bss (NOLOAD) :
  {
    _sbss = .;
    *(.bss*)
    *(COMMON)
    . = ALIGN(4);
    _ebss = .;
  } > RAM

  _estack = ORIGIN(RAM) + LENGTH(RAM);
}
```

### §A.2 Vektör tablosu ve reset mantığı (taslak)

```c
/* Kaynak: CMSIS-Core vektör tablosu taslağı — https://developer.arm.com/... yerine
 * https://arm-software.github.io/CMSIS_5/Core/html/index.html (2026-10-13 doğrulandı)
 * Not: hedef MCU UNKNOWN — bu parçacık referans taslaktır. */

#include <stdint.h>

typedef void (*isr_t)(void);

extern uint32_t _estack;
extern uint32_t _sbss, _ebss, _sdata, _edata, _sidata;

extern int main(void);

void reset_handler(void)
{
    uint32_t *src = &_sidata;
    uint32_t *dst = &_sdata;

    while (dst < &_edata)
    {
        *dst++ = *src++;
    }

    for (dst = &_sbss; dst < &_ebss; ++dst)
    {
        *dst = 0U;
    }

    (void)main();
}

__attribute__((section(".isr_vector")))
const isr_t vector_table[] =
{
    (isr_t)(&_estack),
    reset_handler,
};
```

### §A.3 Zephyr dts + Kconfig örneği (48 kHz I2S, 512 tampon)

```dts
/* Kaynak: Zephyr I2S örneği — https://docs.zephyrproject.org/latest/samples/drivers/i2s/README.html
 * (2026-10-13 doğrulandı; düz dizin yolu 404 verir — README.html kullanılır).
 * 48 kHz / 512 örnek: sartname sabitleri. */

/ {
    chosen {
        zephyr,i2s = &i2s;
    };
};

&i2s {
    status = "okay";
    /* Sartname: 48000 Hz, 32-bit float hattı (ADR-017), 512 örnek tampon */
};
```

```kconfig
/* Kaynak: Zephyr Kconfig örneği — https://docs.zephyrproject.org/latest/ (2026-10-13 doğrulandı) */

CONFIG_AUDIO_I2S=y
CONFIG_I2S_TEST=y
CONFIG_MAIN_STACK_SIZE=2048
CONFIG_SYS_CLOCK_TICKS_PER_SEC=1000
```

> **Sartname bağı:** 512 örnek @ 48 kHz = 10,67 ms tampon — doğrulama
> dosyasındaki B-13/B-15 senaryolarının girdisidir (boot-dogrulama.md §A).

### §A.4 Güvenli boot yapılandırma örneği (taslak)

```c
/* Kaynak: NIST SP 800-193 prensipleri —
 * https://nvlpubs.nist.gov/nistpubs/SpecialPublications/NIST.SP.800-193.pdf
 * (2026-10-13 doğrulandı). P1 Protect / P2 Detect / P3 Recover. */

typedef struct
{
    uint32_t stage;
    uint32_t fw_version;
    uint32_t fw_length;
    uint32_t fw_crc32;
    uint32_t signature_len;
    uint8_t  signature[64];
} boot_manifest_t;

_Static_assert(sizeof(boot_manifest_t) % 4 == 0, "manifest 4-byte hizali olmali");
```

## §B Kod Örnekleri

### §B.1 Bootloader ana akışı (taslak kontrol akışı)

```c
/* Kaynak: boot akışı taslağı — boot-kapsam.md §C/boot-dogrulama.md §A (repo kaynak yok). */

typedef enum
{
    BOOT_OK = 0,
    BOOT_ERR_INTEGRITY = -1,
    BOOT_ERR_ROLLBACK = -2,
    BOOT_ERR_HW_INIT = -3
} boot_status_t;

boot_status_t boot_run(void)
{
    boot_status_t st = BOOT_OK;

    /* 1) Erken saat/dma/pinmux kurulumu (boot-kapsam.md §B ögeleri) */
    if (hw_clock_init() != 0)
    {
        return BOOT_ERR_HW_INIT;
    }

    /* 2) Flaş bütünlüğü (CRC) — doğrulama: boot-dogrulama.md B-02 */
    if (flash_crc_verify() != 0)
    {
        st = BOOT_ERR_INTEGRITY;
    }

    /* 3) İmza doğrulama — doğrulama: boot-dogrulama.md B-03 */
    if (image_signature_verify() != 0)
    {
        st = BOOT_ERR_INTEGRITY;
    }

    /* 4) Anti-rollback sürüm kontrolü — boot-dogrulama.md B-04 */
    if (rollback_check() != 0)
    {
        st = BOOT_ERR_ROLLBACK;
    }

    /* 5) Uygulamaya atlama — boot-dogrulama.md B-12 (watchdog reset sonrası) */
    if (st == BOOT_OK)
    {
        app_jump();
    }

    return st;
}
```

### §B.2 Kritik bölüm (critical section) örneği — Zephyr k_spin_lock

```c
/* Kaynak: Zephyr spinlock — https://docs.zephyrproject.org/latest/kernel/services/threads/
 * (2026-10-13 doğrulandı). */

#include <zephyr/kernel.h>

static struct k_spinlock boot_lock;

void boot_critical_enter(struct k_spinlock_key *key)
{
    key->key = k_spin_lock(&boot_lock);
}

void boot_critical_exit(struct k_spinlock_key *key)
{
    k_spin_unlock(&boot_lock, key->key);
}
```

### §B.3 FreeRTOS alternatifi — taskENTER_CRITICAL

```c
/* Kaynak: FreeRTOS-kernel — https://github.com/FreeRTOS/FreeRTOS-Kernel (2026-10-13 doğrulandı). */

void boot_critical_enter_rtos(void)
{
    taskENTER_CRITICAL();
}

void boot_critical_exit_rtos(void)
{
    taskEXIT_CRITICAL();
}
```

### §B.4 Flaş yazma/geri alma (taslak)

```c
/* Kaynak: flash güncelleme taslağı — NIST SP 800-193 P3 Recover ile uyumlu.
 * Doğrulama: boot-dogrulama.md B-05 (tam yazma + yarım yazma + bit-flip). */

int flash_write_image(const uint8_t *src, uint32_t len)
{
    if (flash_erase_app() != 0)
    {
        return -1;
    }

    if (flash_program(src, len) != 0)
    {
        return -1;
    }

    if (flash_crc_verify() != 0)
    {
        return -1;
    }

    return 0;
}
```

### §B.5 Anti-rollback sayacı (taslak)

```c
/* Kaynak: anti-rollback taslağı — boot-dogrulama.md B-04. */

#define FW_VERSION_MIN 1U

int rollback_check(void)
{
    uint32_t stored = flash_read_fw_version();

    if (stored < FW_VERSION_MIN)
    {
        return -1;
    }

    return 0;
}
```

### §B.6 Python doğrulama betiği — offset/CRC hesabı

```python
# Kaynak: boot doğrulama betiği — boot-dogrulama.md §C (Python her yerde çalışır).
import struct
import zlib

def crc32_of(path):
    with open(path, "rb") as fh:
        data = fh.read()
    return zlib.crc32(data) & 0xFFFFFFFF

def parse_vector_table(path):
    with open(path, "rb") as fh:
        raw = fh.read(8)
    sp, reset = struct.unpack("<II", raw)
    return sp, reset

if __name__ == "__main__":
    print(hex(crc32_of("firmware.bin")))
    print(parse_vector_table("firmware.bin"))
```

## §C Kötü Uygulama Örnekleri

| # | Kötü | Neden kötü | İyi |
|---|---|---|---|
| 1 | `flash_read` çağrısını ISR içinde `malloc` ile tampon ayırma | RT ihlali — kesme bağlamında tahsis (K005-firmware kuralı 1) | Baştan ayrılmış sabit tampon kullan |
| 2 | Boot tamponunu 4-byte hizalamadan yazmak | Unaligned erişim Cortex-M'de fault üretir (boot-dogrulama.md B-14) | `. = ALIGN(4)` ile hizala |
| 3 | CRC'yı yanlış uzunlukla (byte değil word) hesaplamak | Sahte "geçti" — B-18 bunu yakalar | CRC uzunluğu flaş boyutuyla eşleşmeli |
| 4 | Bootloader'ı watchdog beslemeden bırakmak | 800 ms sınırı aşılınca reset (B-12) | Boot sırasında periyodik feed |
| 5 | İmza doğrulamasını atlayıp "hızlı boot" yapmak | Güvenli boot P1 ihlali (NIST SP 800-193) | İmza + anti-rollback her boot'ta |
| 6 | Boot günlüğüne hassas anahtar yazmak | Sızıntı (AIOrchestrator.md §4) | Yalnız olay kodu + sürüm |

## §D Karşılaştırmalı Örnekler

| Kriter | Zephyr yolu | FreeRTOS yolu | Boot etkisi |
|---|---|---|---|
| Spinlock API | `k_spin_lock`/`k_spin_unlock` | `taskENTER_CRITICAL`/`taskEXIT_CRITICAL` | Kritik bölüm süresi her ikisinde de ölçülür (B-06) |
| dts/Kconfig | Dahili (dts + Kconfig) | Yok (elle pin/pclk) | Boot yapılandırmasının taşınabilirliği |
| Reset sonrası davranış | `sys_reboot()` (Zephyr reboot docs) | `NVIC_SystemReset()` | B-12 senaryosu |
| Bellek ayırma | Statik (devicetree) | Statik + heap | Tahsis yasağına uyum |
| Sürüm takibi | Manifest + anti-rollback | Manifest + sayaç | B-04 senaryosu |

## §E Web Araştırma Tablosu

| İddia | URL | Tarih | Durum |
|---|---|---|---|
| Vektör tablosu/Cortex-M çekirdek başlangıcı CMSIS-Core'da tanımlanır | https://arm-software.github.io/CMSIS_5/Core/html/index.html | 2026-10-13 | HTTP 200 |
| I2S 48 kHz örneği Zephyr samples'ta bulunur | https://docs.zephyrproject.org/latest/samples/drivers/i2s/README.html | 2026-10-13 | HTTP 200 |
| Kconfig yapılandırma sistemi Zephyr'de anlatılır | https://docs.zephyrproject.org/latest/ | 2026-10-13 | HTTP 200 |
| NIST SP 800-193 platform güvenilirliğini P1/P2/P3 ile tanımlar | https://nvlpubs.nist.gov/nistpubs/SpecialPublications/NIST.SP.800-193.pdf | 2026-10-13 | HTTP 200 (GET; HEAD 404) |
| FreeRTOS kritik bölüm API'si resmi depoda bulunur | https://github.com/FreeRTOS/FreeRTOS-Kernel | 2026-10-13 | HTTP 200 |
| CRC-32/ISO-HDLC IEEE 802.3 uyumludur | https://standards.ieee.org/ieee/802.3/7071/ | 2026-10-13 | HTTP 200 |

## §F Uzatılmış C++ Örneği — Flash Sürücü Sınıfı Taslağı

> Bu sınıf taslağı, boot akışındaki flaş erişimini merkezileştirir.
> Statik bellek kullanır (tahsis yasağına uyum); hedef MCU bilinmediği için
> fonksiyon gövdeleri taslak kontroldür.

```cpp
// Kaynak: flash sürücü sınıfı taslağı — repo'da derlenebilir kod yok.
// Kurallar: statik tampon, tahsis yok, nothrow (K005-firmware kuralı 1/3).

#include <cstdint>
#include <cstddef>

class FlashDriver final
{
public:
    static constexpr uint32_t kSectorSize = 4096U;

    enum class Status : int32_t
    {
        Ok = 0,
        ErrErase = -1,
        ErrProgram = -2,
        ErrVerify = -3,
        ErrRange = -4
    };

    static FlashDriver& instance() noexcept
    {
        static FlashDriver drv;
        return drv;
    }

    Status erase_app() noexcept
    {
        if (hw_flash_erase(kAppBase, kAppSize) != 0)
        {
            return Status::ErrErase;
        }
        return Status::Ok;
    }

    Status program(const uint8_t* src, uint32_t len) noexcept
    {
        if (src == nullptr || len == 0U || len > kAppSize)
        {
            return Status::ErrRange;
        }
        if (hw_flash_program(kAppBase, src, len) != 0)
        {
            return Status::ErrProgram;
        }
        return Status::Ok;
    }

    Status verify_crc(uint32_t expected) noexcept
    {
        uint32_t actual = 0;
        if (hw_flash_crc(kAppBase, kAppSize, &actual) != 0)
        {
            return Status::ErrVerify;
        }
        return (actual == expected) ? Status::Ok : Status::ErrVerify;
    }

private:
    FlashDriver() noexcept = default;

    static constexpr uint32_t kAppBase = 0x08010000U; // örnek — hedef MCU UNKNOWN
    static constexpr uint32_t kAppSize = 0x40000U;    // örnek — 256 KiB

    // hw_flash_* fonksiyonları MCU HAL'ine aittir; implementasyonu
    // hedef MCU kesinleşince yazılacak (⚠️ VERIFICATION REQUIRED).
    static int hw_flash_erase(uint32_t base, uint32_t len) noexcept;
    static int hw_flash_program(uint32_t base, const uint8_t* src, uint32_t len) noexcept;
    static int hw_flash_crc(uint32_t base, uint32_t len, uint32_t* out) noexcept;
};

static_assert(sizeof(FlashDriver) >= 1, "sinif tanimli");
```

## §G Boot Olay Kodları — Doğrulama Köprüsü

Her olay kodu, `boot-dogrulama.md` §A'daki senaryoya bağlanır. Kodlar
taslaktır; kesin değerler firmware implementasyonunda sabitlenecek.

| Olay kodu | Anlamı | Doğrulama senaryosu | Kritiklik |
|---|---|---|---|
| BOOT_E001 | Flaş CRC uyuşmazlığı | B-02 | Kritik — recovery zinciri |
| BOOT_E002 | İmza doğrulama hatası | B-03 | Kritik — güvenlik |
| BOOT_E003 | Anti-rollback ihlali | B-04 | Kritik — güvenlik |
| BOOT_E004 | Flaş yazma yarım kaldı | B-05 | Yüksek — dayanıklılık |
| BOOT_E005 | Erken saat kurulumu başarısız | B-01 | Kritik — donanım |
| BOOT_E006 | Boot süresi eşiği aşıldı (1200 ms) | B-13 | Orta — performans |
| BOOT_E007 | Bit-flip tespit edildi (ECC/parity) | B-07 | Kritik — bütünlük |
| BOOT_E008 | Güvenli başlatma (secure boot) devre dışı | B-08 | Kritik — güvenlik |
| BOOT_E009 | Boot günlüğü sürücü başlatılamadı | B-10 | Orta — telemetri |
| BOOT_E010 | Reset nedeni okunamadı | B-12 | Düşük — teşhis |

## §H Bellek ve Boyut Bütçesi (taslak)

| Bölge | Taslak boyut | Not |
|---|---|---|
| Vektör tablosu | 64–512 bayt | IRQ sayısıyla ölçeklenir |
| Bootloader `.text` | ≤ 32 KiB | Sıkıştırma yoksa doğrulanmalı |
| Boot manifest | 80 bayt | `boot_manifest_t` (§A.4) |
| Boot günlüğü tamponu | 256 bayt statik | Tahsis yasağı — baştan ayrılmış |
| Kritik bölüm maks. süre | ≤ 100 µs (hedef) | `boot-dogrulama.md` B-06 eşiği |
| Boot toplam süre | ≤ 1200 ms | `boot-dogrulama.md` B-13 eşiği |

## §I CI Entegrasyon Notu (repo kanıtı)

Depoda `.github/workflows/ci.yml` mevcuttur (2026-10-13 keşif: `.github/workflows/`
içinde `ci.yml` + `secret-scan.yml`). Firmware build'i bu depoda TANIMLI DEĞİLDİR
(derlenebilir kaynak yok). CI köprüsü kurulacağı zaman:

1. Firmware build adımı `ci.yml`'a bir iş olarak eklenecek (onay + ADR gerektirir).
2. Boot doğrulama betikleri (§B.6) CI adımında çalıştırılacak.
3. `secret-scan.yml` firmware ikili dosyalarında da çalıştırılmalı (anahtar sızıntısı — AIOrchestrator.md §4).

⚠️ Bu bölüm mevcut CI'yı DEĞİŞTİRMEZ; yalnız plan notudur.

## §J Sık Sorulanlar

**S: Bu dosyadaki C kodu derlenebilir mi?**
Hayır. Repo'da derlenebilir firmware kaynağı yok (2026-10-13 keşif: 0 `.c`/`.cpp`/`.h` dosyası) ve hedef MCU UNKNOWN. Parçalar referans taslaktır; `Kaynak:` etiketi taşırlar.

**S: Adresler (0x08000000 gibi) neden sabit?**
Örnektir. Gerçek MCU kesinleşince linker haritası datasheet'e göre güncellenir; sabit adresler burada iddia değil referanstır.

**S: Zephyr mi FreeRTOS mu?**
Henüz karar yok (UNKNOWN). §D karşılaştırması yalnız etkisini gösterir; seçim mimari karardır (ADR gerektirir).

**S: Boot günlüğü nereye yazılır?**
RAM tamponuna; telemetri köprüsü üzerinden okunur (boot-kapsam.md §C). Kalıcı depolama seçimi mimari karardır.

**S: `Kaynak:` neden `Dosya:`/`Satır:` değil?**
Repo'da bu kod karşılığı dosya yoktur. Uydurma dosya yolu/satır numarası yasaktır (zero-hallucination).

## §K Boot Test Matrisi (özet köprü)

| Test | Yöntem | Doğrulama senaryosu |
|---|---|---|
| Statik analiz | cppcheck/Clang-Tidy (boot-dogrulama.md §B.2) | Tümü |
| Birim | Unity/ceedling (boot-dogrulama.md §B.1) | B-02, B-03, B-04, B-08 |
| Entegrasyon | Renode/Zephyr native_sim | B-12, B-13, B-14, B-16 |
| Donanım | SWD + SEGGER J-Link | B-06, B-11, B-17 |
| Fuzz | AFL++ (boot-dogrulama.md §B.3) | B-01, B-19, B-20 |

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
