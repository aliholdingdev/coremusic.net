---
title: "CoreMusic — C11 Embedded Driver Template (GCC / ISR / MMIO)"
type: template
category: other
date: 2026-09-23
updated: 2026-09-23
version: 1.0.0
status: active
authority: SSOT
---

# CoreMusic — C11 Embedded Driver Template (GCC / ISR / MMIO)

**Zorunlu Bağlantılar:** [[.ai/.templates/index.md]] · [[.ai/CLAUDE.md]] · [[.ai/brain.md]] · [[.ai/.templates/other/cpp-template.md]] · [[.ai/log.md]]

---

## §1. Amaç

Bu şablon, CoreMusic donanım katmanında **C11 + GCC** ile yazılacak gömülü sürücü kodunu (I2C/SPI register erişimi, ISR, memory-mapped I/O, DMA ring) standartlaştırır.

> **Truth Mode (§1.1):** **Saf C sürücü kodu CoreMusic'te şu an bulunmuyor — bu şablon hazır bulunduruluyor.** Kanıt: `glob('**/*.{c,h}')` → 0 dosya (2026-09-23). Neva Engine'in mevcut ses kodu C++20/JUCE'dir (`[[.ai/.templates/other/cpp-template.md]]`, `glob('**/*.cpp')` → bkz. §2.1). XMOS XU316 firmware'i (ADR-017) için C gerekirse bu şablon devreye girer.

### §1.1 Truth Mode Durum Tablosu

| İddia | Durum | Kanıt |
|---|---|---|
| Repo'da `.c`/`.h` dosyası yok | ✅ doğrulandı | `glob('**/*.{c,h}')` = 0 |
| Ses motoru kodu C++20/JUCE | ✅ | `cpp-template.md` §1 |
| ADR-017 hedefi XMOS XU316 | ✅ | `brain.md` §13.1 (frozen) |
| Bu şablon "hazır bulunduruluyor" | ✅ | bu dosya, `status: active` |

### §1.2 cpp-template'ten Fark Tablosu (Bu Şablonun Özgü §'ları)

| Konu | `cpp-template.md` (C++20) | **bu şablon (C11)** | Neden |
|---|---|---|---|
| Sınıf / RAII | `class`, ctor/dtor, `noexcept` | **YOK** — `struct` + `_init()` | C'de sınıf yok, RAII yok |
| Bellek | zero-allocation (stack/member) | **`malloc`/`free` tamamen yasak** (§4.2) | determinizm |
| Sahiplik | `std::unique_ptr` | açık `deinit()` / statik ömür | C el ile yönetir |
| Lock-free | `std::atomic` | `_Atomic` (C11 atomikleri) | dil farkı |
| ISR | yok (ASIO callback) | **`volatile` + ISR §3.4/§4.1** | donanım kesmesi |
| Register | yok | **memory-mapped I/O §3.3** | MMIO |
| Derleyici | `-Wall -Wextra -Wpedantic -Werror` | aynı + `arm-none-eabi-gcc` | §3.5 |
| Include guard | `#pragma once` | `#ifndef` guard (C'de yaygın) | taşınabilirlik |

---

## §2. Kapsam

| Kapsam | Kapsam Dışı |
|---|---|
| `*.c` / `*.h` gömülü sürücü (I2C, SPI, I2S, GPIO) | C++ audio engine (bkz. `cpp-template.md`) |
| ISR, MMIO register, DMA ring buffer | PHP/JS uygulama kodu |
| C11 `_Atomic`, `volatile` kuralları | Web/CSS frontend |
| GCC/`arm-none-eabi-gcc` build iskeleti | PCB tasarımı (hardware-template) |

- **Dosya tipi:** `*.c` / `*.h` + Markdown şablon dokümanı
- **Teknoloji:** C11, GCC (`arm-none-eabi-gcc` / xcc), Make/CMake
- **Kullanan agent:** Embedded Engineer (birincil), DSP Firmware Engineer (ikincil — XMOS/I2S)
- **Katman:** L0 (donanım) · **Guardrail:** #16 (Template Mandatory)
- **Durum:** 🔵 PLANNED / hazır bulunduruluyor (§1.1)

### §2.1 Donanım Bağları (ADR referanslı)

| bileşen | Rol | ADR | Sürücü etkileşimi |
|---|---|---|---|
| XMOS XU316 | DSP ana | ADR-017 | I2S/TDM, XC/C firmware |
| PCM3168A | DAC/ADC | ADR-017/038 | I2C config (§3.3 MMIO/I2C) |
| 31-band EQ | DSP zinciri | ADR-025 | coefficient tablosu |
| PCM5122 | REDDEDİLMİŞ | ADR-038 | ❌ kullanma |

---

## §3. Mimari

### §3.1 Sürücü İskeleti (C11 — struct + init/deinit, RAII yok)

```c
/* drivers/pcm3168a.h — include guard (C'de #pragma once yerine) */
#ifndef COREMUSIC_PCM3168A_H
#define COREMUSIC_PCM3168A_H

#include <stdint.h>
#include <stddef.h>
#include <stdbool.h>

/* Sürücü durumu — C++'taki sınıf yerine açık struct (RAII YOK) */
typedef struct {
    uint8_t  i2c_addr;      /* 0x40 / 0x41 (AD pin) */
    uint32_t sample_rate;   /* 48000, 96000 */
    uint16_t bit_depth;     /* 16 / 24 */
    bool     initialized;
} pcm3168a_t;

/* Yaşam döngüsü — ctor/dtor yerine açık çağrılar (RAII YOK) */
int  pcm3168a_init(pcm3168a_t *dev, uint8_t addr);   /* -1 = bus hatası */
void pcm3168a_deinit(pcm3168a_t *dev);               /* registerlari resetler */

/* Config — ADR-017 (48k/24b) varsayılanı */
int pcm3168a_set_format(pcm3168a_t *dev, uint32_t sample_rate, uint16_t bit_depth);

#endif /* COREMUSIC_PCM3168A_H */
```

```c
/* drivers/pcm3168a.c */
#include "pcm3168a.h"
#include "mmio.h"        /* §3.3 */
#include "i2c_bus.h"

int pcm3168a_init(pcm3168a_t *dev, uint8_t addr)
{
    if (dev == NULL) return -1;

    dev->i2c_addr   = addr;
    dev->sample_rate = 48000u;    /* ADR-017 varsayılan */
    dev->bit_depth  = 24u;
    dev->initialized = false;

    /* Power-up sequence (datasheet §9) */
    if (i2c_write_reg(dev->addr_dummy(), 0x02, 0x00) != 0) return -1;

    dev->initialized = true;
    return 0;
}

void pcm3168a_deinit(pcm3168a_t *dev)
{
    if (dev == NULL || !dev->initialized) return;
    (void)i2c_write_reg(dev->i2c_addr, 0x02, 0x01);  /* soft mute + powerdown */
    dev->initialized = false;
}
```

### §3.2 Yasaklı Bellek Şablonu (malloc ban → statik havuz)

```c
/* ❌ YASAK — herhangi bir yerde (§4.2) */
/* void *p = malloc(64); free(p); */

/* ✅ DOĞRU — statik, önceden ayrılmış havuz (deterministik) */
#define RING_CAPACITY 1024u   /* güç-of-2 (mask hızı) */

typedef struct {
    _Atomic uint32_t write_head;   /* C11 atomik (C++ std::atomic muadili) */
    _Atomic uint32_t read_head;
    float buffer[RING_CAPACITY];   /* statik depolama — heap YOK */
} audio_ring_t;

static audio_ring_t g_capture_ring;   /* BSS — tek örnek, ömür = program */

bool ring_push(audio_ring_t *r, const float *data, uint32_t n)
{
    uint32_t w = atomic_load_explicit(&r->write_head, memory_order_relaxed);
    uint32_t rd = atomic_load_explicit(&r->read_head, memory_order_acquire);
    if ((RING_CAPACITY - (w - rd)) < n) return false;   /* dolu */

    const uint32_t mask = RING_CAPACITY - 1u;
    for (uint32_t i = 0; i < n; ++i)
        r->buffer[(w + i) & mask] = data[i];

    atomic_store_explicit(&r->write_head, w + n, memory_order_release);
    return true;
}
```

**Bellek Kuralı Eşlemesi:**

| İhtiyaç | C++ (cpp-template) | **C11 (bu şablon)** |
|---|---|---|
| buffer | `alignas(64) float _buf[N]` | `static float g_buf[N] __attribute__((aligned(64)))` |
| ring | `std::atomic` head | `_Atomic uint32_t` head |
| singleton | Meydan okuyan sınıf | `static` dosya-kapsamı |
| dinamik | yasak (audio thread) | **her yerde yasak** (§4.2) |

### §3.3 Memory-Mapped I/O (MMIO)

```c
/* drivers/mmio.h — register erişimi (tek nokta) */
#ifndef COREMUSIC_MMIO_H
#define COREMUSIC_MMIO_H

#include <stdint.h>

/* 32-bit register erişimi — volatile ZORUNLU (§4.1 #3) */
static inline void mmio_write32(uintptr_t addr, uint32_t value)
{
    *(volatile uint32_t *)addr = value;
}

static inline uint32_t mmio_read32(uintptr_t addr)
{
    return *(volatile uint32_t *)addr;
}

/* Read-modify-write (bit alanları için) — atomiklik ISR'ta §3.4'e bak */
static inline void mmio_set_bits(uintptr_t addr, uint32_t mask)
{
    uint32_t v = *(volatile uint32_t *)addr;
    *(volatile uint32_t *)addr = v | mask;
}

#endif /* COREMUSIC_MMIO_H */
```

```c
/* Örnek: I2S control register'ı (adresler board_specific.h'den gelir) */
#include "mmio.h"

#define I2S_CTRL_REG   (*(volatile uint32_t *)0x40012000u)
#define I2S_CTRL_EN    (1u << 0)
#define I2S_CTRL_RST   (1u << 1)

void i2s_start(void)
{
    mmio_set_bits(0x40012000u, I2S_CTRL_EN);
}

bool i2s_is_busy(void)
{
    return (mmio_read32(0x40012000u) & (1u << 8)) != 0u;
}
```

**MMIO Register Tablosu (örnek şablon):**

| Register | Adres (örnek) | Erişim | Amaç | ISR'ta? |
|---|---|---|---|---|
| `I2S_CTRL` | `0x40012000` | R/W | enable/reset | Evet (sadece flag) |
| `I2S_STATUS` | `0x40012004` | R | busy/derr | Evet |
| `DMA_BASE` | `0x40013000` | R/W | ring başlangıcı | Hayır |
| `CODEC_CFG` | I2C `0x40..` | W | PCM3168A config | Hayır |

> ⚠️ Adresler **örnektir**; gerçek adresler datasheet/board dosyasından doğrulanır (`VERIFICATION REQUIRED`, ADR-005).

### §3.4 ISR (Interrupt Service Routine)

```c
/* drivers/isr_audio.c — kesme rutini: kısa, bloklamayan, non-reentrant değil */
#include <stdint.h>
#include "_Atomic_compat.h"   /* C11 atomikler */

/* Paylaşılan durum — ISR ↔ main döngüsü arası */
static _Atomic uint32_t g_xrun_count;          /* taşma sayacı */
static volatile uint32_t g_last_irq_tick;      /* ölçüm için */

void I2S_IRQHandler(void) __attribute__((used));   /* linker sembolü */

void I2S_IRQHandler(void)
{
    /* 1) IRQ kaynağını temizle (dönüşte tekrar tetiklenmesin) */
    mmio_write32(I2S_STATUS_REG, 0x1u);

    /* 2) Flag yükselt — main döngüsü işler (yarım iş kuralı) */
    atomic_store_explicit(&g_irq_pending, 1u, memory_order_release);

    /* 3) Sayaç — ölçüm/log §5 */
    g_last_irq_tick = mmio_read32(SYSTICK_REG);

    /* ⚠️ YASAK: printf, malloc, busy-wait, kilit (§4.2) */
}

/* main döngüsü — ISR'in yarı işini tamamlar */
void audio_task(void)
{
    if (atomic_exchange_explicit(&g_irq_pending, 0u, memory_order_acquire) == 1u)
    {
        /* ring'e kopyala (§3.2), DSP'e bırak (ADR-062 pipeline) */
    }
}
```

**ISR Kural Tablosu:**

| # | Kural | Neden | İhlal |
|---|---|---|---|
| I1 | ISR ≤ birkaç µs, sadece flag/kopya | sistem kilitlenmesi | 🔴 |
| I2 | `printf`/log yasak (lock riski) | deadlock | 🔴 |
| I3 | `volatile`/`_Atomic` paylaşım | optimizasyon kaybı | 🔴 |
| I4 | Kaynak register'ı ilk temizle | tekrar tetikleme | 🔴 |
| I5 | Main döngüsüne iş bırak (yarım iş) | determinizm | 🟠 |
| I6 | `g_xrun_count` gibi sayaçlarla ölç | gözlemlenebilirlik | 🟡 |

### §3.5 Build (Make / arm-none-eabi-gcc)

```make
# Makefile — gömülü sürücü iskeleti
CC      = arm-none-eabi-gcc
CFLAGS  = -std=c11 -Os -g3 \
          -Wall -Wextra -Wpedantic -Werror \
          -ffreestanding -fno-builtin \
          -fno-common -Wshadow -Wconversion \
          -mcpu=cortex-m7 -mthumb
LDFLAGS = -T linker.ld -Wl,--gc-sections

SRCS = drivers/pcm3168a.c drivers/mmio.c drivers/isr_audio.c
OBJS = $(SRCS:.c=.o)

all: firmware.elf

%.o: %.c
	$(CC) $(CFLAGS) -c $< -o $@

firmware.elf: $(OBJS)
	$(CC) $(OBJS) $(LDFLAGS) -o $@

# Bellek denetimi: map dosyasında .bss/.data sınırı
size firmware.elf
```

| Bayrak | Amaç | cpp-template farkı |
|---|---|---|
| `-std=c11` | dil standardı | `-std=c++20` |
| `-ffreestanding` | OS yok (bare-metal) | yok (masaüstü) |
| `-fno-common` | çakışan semboller erken hata | yok |
| `-Wconversion` | gizli daraltma | opsiyonel |
| `size firmware.elf` | SRAM/Flash bütçesi | yok |

### §3.6 Bellek Bütçesi & Linker Haritası (DOMAIN)

| Bölüm | İçerik | Bütçe (örnek 512 KB SRAM) | Denetim | Aşım Eylemi |
|---|---|---|---|---|
| `.text` | kod (Flash) | ≤ 256 KB (Flash 1 MB) | `size firmware.elf` | 🔴 -Os/refactor |
| `.rodata` | sabit tablo (EQ katsayı vb.) | ≤ 32 KB | harita | 🟠 `const` taşı |
| `.data` | başlatılmış statik | ≤ 16 KB | harita | 🟠 küçült |
| `.bss` | başlatılmamış statik (ring dahil) | ≤ 64 KB | harita | 🔴 ring boyutu/4 |
| Stack | ISR + main (toplam) | ≤ 16 KB | `-fstack-usage` | 🔴 statik analiz |
| Heap | **0 (malloc yasak, §4.1)** | **0** | `grep malloc` = 0 | 🔴 ihlal |

```text
$ arm-none-eabi-size firmware.elf
   text    data     bss     dec     hex filename
 184320    2048   49152  235520   39800 firmware.elf
                              ↑
              bütçe: text≤262144 · data≤16384 · bss≤65536 · heap=0

$ arm-none-eabi-objdump -h firmware.elf      # bölüm listesi (denetim)
$ find . -name '*.su' | xargs grep -H 'stack' # -fstack-usage raporu
```

**Statik Analiz / Kuruluş Kontrolleri:**

| Araç | Komut (şablon) | Kabul |
|---|---|---|
| cppcheck | `cppcheck --enable=all --std=c11 drivers/` | 0 error |
| stack-usage | `-fstack-usage` + `.su` toplamı | ≤ 16 KB |
| malloc tarama | `grep -rn "malloc\|free(" drivers/` | **0 sonuç** |
| volatile tarama | `grep -c "(volatile" drivers/mmio.h` | her MMIO'da ≥1 |
| Harita bütçesi | `size` satırları | tümü tabloda |

---

### §3.7 Derleme Bayrakları & Sabitler Tablosu (DOMAIN)

| Bayrak / Sabit | Amaç | Şablon Değeri | Kontrol |
|---|---|---|---|
| `-std=` | C dil sürümü | `c11` | Makefile (§3.5) |
| `-Wall -Wextra -Werror` | uyarı = hata | açık | her derleme |
| `-Os` | boyut optimizasyonu | açık | `size` bütçesi (§3.6) |
| `-ffunction-sections` + `-Wl,--gc-sections` | ölü kod atma | açık | linker haritası (§3.6) |
| `-fstack-usage` | stack ölçümü | açık | `.su` toplamı ≤ {{STACK_MAX}} |
| `-fno-common` | çakışan global tanım | açık | link zamanında hata |
| `-Wshadow` | gölgelenen değişken | açık | okunabilirlik |
| `_Static_assert` | derleme zamanı sözleşmesi | struct boyutu / offset | §3.1, §3.3 |
| `NDEBUG` | assertion kontrolü | üretimde tanımlı | hata yolu §5.1 |

> Yasaklılar §4.2; MMIO `volatile` zorunluluğu §3.3; ISR kuralları §3.4. Bayrak değişikliği ölçüm (µs/mW) gerektirir ve ADR'ye bağlanır. Derleme çıktısı `.ai/log.md`'ye özetlenir (append-only).

## §4. Kurallar

### §4.1 Hard Guardrails (C11 / Gömülü)

| # | Kural | İhlal Sonucu |
|---|---|---|
| 1 | **`malloc`/`free`/`calloc`/`realloc` TAMAMEN yasak** (her fonksiyonda) | fragmentasyon, determinizm kaybı |
| 2 | **`volatile` olmadan MMIO/ISR değişkeni ERİŞİLEMEZ** | yan etki optimizasyonu |
| 3 | ISR kısa olur; `printf`/kilit/malloc yasak | sistem kilidi |
| 4 | Paylaşım `_Atomic` (C11) — `volatile` tek başına yeterli DEĞİL | data race |
| 5 | RAII yok → her `_init()` eşine `_deinit()` | kaynak sızıntısı |
| 6 | Return kodları kontrol edilir (`-Wunused-result` warn) | sessiz bus hatası |
| 7 | `#pragma once` yerine `#ifndef` guard | taşınabilirlik |

### §4.2 Yasaklı Örüntüler

```c
/* ❌ YASAK — dinamik bellek (her yerde) */
void *p = malloc(64);            /* ❌ */
free(p);                         /* ❌ */
char *s = strdup(name);          /* ❌ */

/* ✅ DOĞRU — statik/dosya kapsamlı */
static char s_name_buf[64];

/* ❌ YASAK — volatile'sız MMIO (yan etki silinir) */
uint32_t v = *(uint32_t *)ADDR;          /* ❌ optimizer yok sayabilir */

/* ✅ DOĞRU */
uint32_t v = *(volatile uint32_t *)ADDR; /* ✅ */

/* ❌ YASAK — volatile ile atomik sanmak (dörtgen yan etki yok!) */
volatile int g_flag;                     /* ❌ race */

/* ✅ DOĞRU — C11 atomik */
static _Atomic int g_flag;               /* ✅ */

/* ❌ YASAK — ISR içinde bloklayan çağrı */
void ISR(void) { printf("irq\n"); }      /* ❌ lock + I/O */

/* ✅ DOĞRU — flag yükselt */
void ISR(void) { atomic_store(&g_irq_pending, 1); }
```

Ek kurallar:

- **Zorunlu:** tüm `static` fonksiyonlar `static` anahtarıyla; header'da yalnızca API (`pcm3168a_*`).
- **Zorunlu:** `{{PLACEHOLDER}}` (`{{DATE}}`, `{{BOARD}}`) doldurulmadan commit yasak (Guardrail #16).
- **Yasak:** RAII/metafor taşınması (C++ `class` kalıbı bu şablona kopyalanmaz — §1.2).
- **Standart:** C11 + `-Wall -Wextra -Wpedantic -Werror` (§3.5); derleme hatası → FIX IMMEDIATELY (`AGENTS.md` §24.4).
- **Uyarı:** register adresi/onaylanmamış datasheet → `⚠️ VERIFICATION REQUIRED` (ADR-005).

---

## §5. Workflow

```text
ŞABLONU SEÇ → KOPYALA → {{PLACEHOLDER}} DOLDUR → GUARDRAIL #16 → DERLE (-Werror) → HARİTA (size) → COMMIT
```

1. **ŞABLONU SEÇ:** `.ai/.templates/other/c-template.md` (Guardrail #16).
2. **KOPYALA:** §3.1→`drivers/*.c|*.h`, §3.3→`mmio.h`, §3.4→`isr_audio.c`, §3.5→`Makefile`.
3. **{{PLACEHOLDER}} DOLDUR:** `{{BOARD}}`, `{{DATE}}`.
4. **GUARDRAIL #16 DOĞRULA:** frontmatter 7 alan + §1-§7 + placeholder yok + §4.1/§4.2 ihlali yok.
5. **DERLE:** `make -j` → `-Werror` temiz; `size firmware.elf` SRAM/Flash bütçede.
6. **COMMIT:** ADR-017/ADR-038/ADR-062 ile çelişki yoksa onayla, `log.md`'ye giriş ekle.

### §5.1 Dönüş Kodları / Hata Sınıflandırması

| Kod | Anlam | Örnek (§3.1 sürücü) | Çağrıyan |
|---|---|---|---|
| `0` | başarı | `pcm3168a_init` | uygulama |
| `-1` | bus hatası (I2C NACK) | init/set_format | uygulama → retry ≤3 |
| `-2` | parametre geçersiz | `set_format` (SR desteklenmiyor) | uygulama → §3.11 fallback |
| `-3` | zaman aşımı | DMA kilitlenmesi | uygulama → reset + log |
| `-4` | durum hatası (initialized=false) | çağrı sırası ihlali | program hatası → assert |
| `E_BUSY` | kaynak meşgul | ring dolu (§3.2) | `ring_push` false |

**Hata Yönetim Kuralları:**

| # | Kural | İhlal |
|---|---|---|
| E1 | Her `-1..-4` dönüşü **kontrol edilmeli** (`-Wunused-result`) | sessiz arıza 🔴 |
| E2 | ISR'ta dönüş kodu yok → yalnız flag/sayaç | bloklama 🔴 |
| E3 | Hata kodları `switch` ile tek yerde (log + fallback) | dağınık davranış 🟠 |
| E4 | Retry: max 3, backoff (§3.12 DB benzeri determinizm) | retry storm 🔴 |
| E5 | Kalıcı hata → `.ai/log.md` append (kod + modül) | gözlemlenebilirlik 🟡 |

---

## §6. Doğrulama

- [ ] 7 alanlı frontmatter var (title, type, category, date, updated, version, status, authority)
- [ ] §1-§7 var; §1'de Truth Mode ("şu an yok, hazır bulunduruluyor") mevcut
- [ ] tüm `{{PLACEHOLDER}}`'lar dolduruldu VEYA kayıtlı
- [ ] dosya bu şablona uygun (C11 gömülü sürücü, C++ DEĞİL)
- [ ] §4.1 guardrails + §4.2 yasaklı örüntüler geçti:
      `malloc` yok · MMIO `volatile` · ISR kısa · paylaşım `_Atomic` · `_init/_deinit` çifti var
- [ ] `glob('**/*.{c,h}')` sonucu §1.1 iddiasıyla tutarlı (şu an: 0)

```bash
# §6 denetimi (salt-okunur)
grep -rn "malloc\|free(" drivers/ --include="*.c"   # → 0 sonuç beklenir
grep -rn "(volatile" drivers/mmio.h                 # → her MMIO'da volatile
glob('**/*.{c,h}')                                  # → Truth Mode §1.1
```

**REFACTOR REPORT:** FILE: c-template.md · PURPOSE: C11 Embedded Driver Template (GCC/ISR/MMIO) · VALIDATION: 7 alan + §1-§7 + cpp farkları §1.2 + bilgi korunumu · RELATED: [[.ai/.templates/index.md]] · [[.ai/.templates/other/cpp-template.md]]

---

## §7. Referanslar

- [[.ai/.templates/index.md]] — şablon registry
- [[.ai/CLAUDE.md]] — AI anayasası, Hard Guardrails
- [[.ai/AGENTS.md]] — routing (§6: C++/ASIO/DSP → Embedded; XMOS/I2S → DSP Firmware Engineer)
- [[.ai/.templates/other/cpp-template.md]] — kalıp kaynak + §1.2 fark tablosunun muhatabı
- `.ai/brain.md` — §13 ADR listesi

İlgili ADR'ler:

| ADR | Konu | Bu Şablondaki Etki |
|---|---|---|
| ADR-017 | XMOS XU316 + PCM3168A DSP | §3.1/§3.3 hedef donanım |
| ADR-025 | 31-band parametrik EQ | coefficient tablosu (§3.2 ring'i besler) |
| ADR-038 | PCM3168A (PCM5122 REDDEDİLMİŞ) | §2.1 — PCM5122 kodu yazılmaz |
| ADR-062 | DSP Pipeline Architecture | §3.4 main döngüsü hattı |
| ADR-005 | Zero hallucination | §3.3 register adresi doğrulaması |

> ⚠️ ADR dosyaları diskte ayrı `.md` olarak bulunmamaktadır (bkz. [[.ai/.templates/adr/adr-index.md]] §2.0); numaralar `brain.md` §13 kaynaklıdır.

### §7.1 Şablon Karşılaştırma Tablosu (C ↔ C++ ↔ ASP.NET)

| Kriter | **c-template (C11)** | cpp-template (C++20) | aspnet-template (C# 13) |
|---|---|---|---|
| Soyutlama | `struct` + `_init/_deinit` | `class` + RAII/`noexcept` | `class` + DI/Scoped |
| Bellek | `malloc` tamamen yasak | audio thread'de yasak | GC (havuz/`ArrayPool`) |
| Eşzamanlılık | `_Atomic` + ISR (§3.4) | `std::atomic` lock-free | `async/await` + token |
| Register/Donanım | MMIO `volatile` (§3.3) | ASIO/JUCE soyutlar | — |
| Build | `arm-none-eabi-gcc`/Make (§3.5) | CMake (cpp §3.4) | `dotnet build` |
| Test | static analiz + harita (§3.6) | `-Werror` derleme | xUnit (aspnet §3.6) |
| Durum (Truth) | PLANNED — `.c/.h` = 0 | PLANNED — Neva Engine | PLANNED — `.cs` = 0 |

---

**Template Version:** 1.0.0
**Last Updated:** 2026-09-23
