---
type: electronic
category: boot-sequence
title: "CoreMusic — Embedded Boot Sequence"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Boot Sequence

**See also:** [[electronic/firmware/index]] · [[electronic/hardware/index]]

---

## 1. Amaç

Boot Sequence, CoreMusic ELECTRONICS platformunun gömülü sistemlerdeki (RPi5, XMOS) başlatma dizisini tanımlar.

---

## 2. Boot Dizisi

```
Power On
    ↓
ROM Bootloader
    ↓
XMOS Firmware Load
    ↓
DSP Chain Init
    ↓
I2S Configure
    ↓
PCM3168A Init
    ↓
USB Audio Start
    ↓
System Ready
```

---

## 3. Boot Süreleri

| Aşama | Süre |
|-------|------|
| ROM Boot | 100ms |
| Firmware Load | 500ms |
| DSP Init | 200ms |
| I2S Config | 100ms |
| DAC Init | 200ms |
| USB Start | 300ms |
| **Toplam** | **~1.5s** |

---

## 4. Hata Durumları

| Hata | Aksiyon |
|------|---------|
| XMOS load fail | LED blink, retry |
| DAC init fail | Alternatif DAC dene |
| USB fail | LED error kodu |

---

## 5. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | Donanım seçimi |

---

## 6. Boot Aşamaları (BL1 → BL2 → Kernel)

Bölüm 2'deki boot dizisi, standart gömülü boot hiyerarşisine oturur. Her aşama, bir sonrakinin imzasını doğrular ve devralır (bkz. [[ota-update]] §5 root of trust).

| Aşama | Ad | Sorumlu | Görev |
|-------|-----|---------|-------|
| BL0 | Boot ROM | Donanım (immutable) | İlk CPU reset vektörü, root of trust |
| BL1 | Primary bootloader | Flash | Temel donanım init, BL2 yükle |
| BL2 | Secondary bootloader | Flash | OS/firmware yükle, imzala |
| Kernel / FW | DSP firmware | RAM | Sistem servisleri, DSP döngüsü |
| Userspace | Uygulama | RAM | Servisler, denetim |

### 6.1 Aşama Bazlı Bellek Durumu

| Aşama | Çalışma Belleği | Durum |
|-------|-----------------|-------|
| BL0/BL1 | SRAM minimal | RAM henüz tam hazır değil |
| BL2 | RAM genişliyor | DDR init tamamlandı |
| Kernel/FW | Tam RAM | DMA tamponları allocate (bkz. [[dsp-firmware]] §8) |

### 6.2 Neden Çok Aşamalı Boot

Tek-aşamalı boot, güvenlik ve ölçeklenebilirlikte zayıftır: (1) her aşamanın tek sorumluluğu olur, (2) imza zinciri aşama aşama kurulur ([[ota-update]] §5), (3) BL2 farklı yükleme kaynaklarını (flash, USB, ağ) soyutlar. CoreMusic, XMOS'ta ROM→loader→firmware; RPi5'te bootrom→GPU firmware→kernel hiyerarşisini kullanır.

---

## 7. Platform Boot Varyantları (RPi5 vs XMOS)

Bölüm 1'deki iki hedef platform, farklı boot yolları kullanır.

| Özellik | RPi5 (Linux) | XMOS (bare-metal) |
|---------|--------------|--------------------|
| İlk stage | Boot ROM (SoC) | xCORE boot ROM |
| Bootloader | U-Boot / RPi firmware | Vendor loader |
| OS | Linux (+ Xenomai) | Yok (bare-metal DSP) |
| DSP | Ayrı çekirdek/iş | xCORE içinde |
| Kompleksite | Yüksek (dosya sistemi) | Düşük (immediate) |
| Boot süresi | ~saniyeler | ~100ms–1s |

### 7.1 RPi5 Boot Yolu

```
SoC Boot ROM → EEPROM/bootloader → GPU firmware (start*.elf)
  → Kernel (Image + DTB) → init (Xenomai patch aktif) → servisler
```

### 7.2 XMOS Boot Yolu

```
xCORE boot ROM → loader (flash'tan) → DSP firmware image
  → clock/I2S init → DSP döngüsü (bkz. [[dsp-firmware]] §9)
```

### 7.3 Ortak Soyutlama

Her iki platformda da, DSP firmware yükleme ve clock-init adımları mantıksal olarak aynıdır; yalnız ilk-stage bootloader farklıdır. Bu soyutlama, [[electronic/firmware/index]] üzerindeki platform-bağımsız firmware sözleşmesini korur.

---

## 8. A/B Yuva ve Dual-Slot Boot

Bölüm 6'daki BL2, Bölüm 6.1'deki yuva seçimiyle hangi firmware'in boot edileceğine karar verir (bkz. [[ota-update]] §6 A/B).

| Yuva Bayrağı | Anlam | Boot Davranışı |
|---------------|-------|----------------|
| good | Sağlıklı firmware | Boot edilir |
| pending | Yeni firmware (denenecek) | Dener, contador++ |
| bad | Başarısız | Boot edilmez |
| recovery | Kurtarma image | Son çare |

### 8.1 Boot Yuva Seçim Algoritması

```
bootloader başlangıcı
  → active/pending bayrağını oku
  → pending ise → yeni yuvayı dene (boot contador++)
  → contador eşiği (ör. 3) aşılırsa → good yuvasına dön
  → hiç good yoksa → recovery image
```

İlgili: [[ota-update]] §7 (anti-rollback), §13 (boot döngüsü).

---

## 9. Loader → Kernel / Firmware Geçişi (Handoff)

BL2, yüklediği görüntüyü başlatırken, kontrollü bir devir (handoff) yapar; boot parametreleri ve bellek haritası aktarılır.

| Handoff Parametresi | Açıklama |
|---------------------|----------|
| Bellek haritası | RAM bölgeleri, DMA tampon adresleri |
| Boot argümanları | Kernel cmdline / yapılandırma |
| Device tree (DTB) | Donanım tanımı (RPi5) |
| Clock/PLL durumu | Devralınan clock (bkz. [[dsp-firmware]] §6) |
| Watchdog durumu | Devredilen watchdog (bkz. [[rtos]] §21) |

### 9.1 Handoff Temizlik Kuralı

Devir öncesi, bootloader'ın kullandığı geçici bellek ve kesmeler temizlenir; kernel/firmware temiz bir durumdan başlar. Aksi halde bayat (stale) DMA/kesme durumu belirsizliğe yol açar.

---

## 10. Hızlı Başlatma (Fast Boot)

Bölüm 3'teki ~1.5s hedefi, hızlı başlatma teknikleriyle düşürülebilir.

| Teknik | Etki | Kısım |
|--------|------|-------|
| Paralel init | DSP + DAC + USB eşzamanlı | §2'deki seri adımlar |
| Skip self-test | Test atlanır (güvenilir ortam) | BL1 |
| Pre-loaded firmware | Flash'tan hızlı yükleme | BL2 |
| Minimal DDR init | Hızlı bellek | BL1 |
| Warm reset reuse | Sıcak reset'te clock korunur | BL2 |

### 10.1 Boot Süresi Bütçesi (Hedef)

| Aşama | Normal | Hedef (fast) |
|-------|--------|--------------|
| ROM/BL1 | 100ms | 50ms |
| Firmware load | 500ms | 200ms |
| DSP init | 200ms | 100ms |
| I2S/DAC | 300ms | 100ms |
| USB start | 300ms | 100ms |
| **Toplam** | **~1.5s** | **~0.6s** |

> ⚠️ VERIFICATION REQUIRED: Fast-boot hedefleri tasarım amaçlarıdır; gerçek donanım üzerinde ölçülmüş boot süresi henüz yoktur (bkz. Bölüm 12).

---

## 11. Hata Kurtarma ve Watchdog

Bölüm 4'teki hata durumları, boot seviyesinde de geçerlidir. Boot döngüsü ve kurtarma hiyerarşisi:

| Hata | Tespit | Kurtarma |
|------|--------|----------|
| Firmware load fail | BL2 checksum/imza | Retry → recovery (bkz. Bölüm 8) |
| Boot döngüsü | Boot contador | Eski (good) yuva |
| Clock init fail | PLL unlock | Retry → safe-mode |
| DAC init fail | I2C/alt bus hata | Alternatif (bkz. Bölüm 4) |
| DDR init fail | BL1 test | Reset / safe-mode |
| Watchdog timeout | Kick yok | Sistem reset (bkz. [[rtos]] §21) |

### 11.1 Boot Watchdog Hiyerarşisi

Boot sırasında da watchdog aktiftir; bootloader her aşamada watchdog'ı "kick" eder. Aşama takılırsa (ör. clock lock alınamazsa) watchdog reset eder ve bootloader contador'ı bir sonraki yuveyi dener. Bu, ölü firmware'in sonsuz boot döngüsüne girmesini engeller.

---

## 12. Boot Süresi Ölçümü

Boot süresi, iddia edilen bir değer değil, ölçülen bir değerdir (bkz. Bölüm 3).

| Ölçüm Noktası | Zaman Damgası | Amaç |
|---------------|---------------|------|
| Reset vektörü | BL0 giriş | Referans 0 |
| BL1 sonu | DDR ready | İlk aşama |
| BL2 sonu | Firmware yüklendi | Yükleme süresi |
| DSP döngü başlangıcı | İlk frame | Audio hazır |
| USB enumerate | Host görünür | Sistem hazır |

### 12.1 Ölçüm Yöntemi

Boot süresi, reset çıkışından (power-on) sistem-hazır girişine kadar GPIO zaman damgasıyla (ya da seri log timestamp) ölçülür. Her aşama için ayrı damga, §10.1'deki bütçenin aşılıp aşılmadığını gösterir. ⚠️ VERIFICATION REQUIRED: Ölçüm altyapısı henüz repo'da yoktur (bkz. Bölüm 15 repo kanıtı).

---

## 13. Secure Boot ve İmza Zinciri

Boot güvenliği, [[ota-update]] §5'teki root of trust ve imza zincirine dayanır; her aşama bir sonrakini doğrular.

| Aşama | Doğrulanan | Doğrulayan |
|-------|------------|------------|
| BL1 | BL2 imzası | Boot ROM / BL0 |
| BL2 | Firmware imzası | BL1 |
| Firmware | (isteğe bağlı) uygulama | BL2 |

### 13.1 Anti-Rollback Boot Kontrolü

BL2, yalnız donanımsal sürüm sayacından (OTP) yüksek sürümleri boot eder; eski/zayıf firmware'in geri yüklenmesi engellenir (bkz. [[ota-update]] §7.1).

---

## 14. Repo Kanıtı

Repo'da boot/firmware (C++/C) kodu YOK — 2026-10-13 ölçümü: `0` `.cpp/.h/.c`, `0` `CMakeLists.txt`, `NevaEngine` yok. Bu dosya salt spesifikasyon; kaynak `.ai/electronic/**`. Boot-süresi ölçüm altyapısı da mevcut değil. Uydurma `Dosya:/Satır:` referansı yazılmaz.

---

## 15. Web Araştırması

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| U-Boot, gömülü sistemler için boot loader (BL2 benzeri yükleme/soyutlama) sağlar | https://www.denx.de/projects/u-boot/ | 2026-10-13 | HTTP 200 doğrulandı |
| MCUboot, imza doğrulamalı secure boot ve A/B firmware image yönetimi sağlar | https://docs.mcuboot.com/ | 2026-10-13 | HTTP 200 doğrulandı |

---

## 16. Kaynakça

1. U-Boot — embedded boot loader: https://www.denx.de/projects/u-boot/
2. MCUboot — secure boot loader / image upgrade: https://docs.mcuboot.com/
3. CoreMusic Vault — `.ai/electronic/firmware/index.md`, `.ai/electronic/firmware/dsp-firmware.md`, `.ai/electronic/firmware/ota-update.md`
4. İlgili: [[rtos]] · [[dsp-firmware]] · [[ota-update]] · [[electronic/hardware/index]]

---

## 17. Terimler ve Kısaltmalar

| Terim | Açıklama |
|-------|----------|
| BL0/BL1/BL2 | Bootloader aşamaları (ROM/primary/secondary) |
| Boot ROM | Donanımda salt-okunur ilk boot kodu |
| Root of trust | Güven zincirinin donanımdaki kökü |
| DTB | Device Tree Blob (donanım tanımı) |
| Handoff | Bootloader'dan OS/firmware'e devir |
| A/B yuva | Dual-bank firmware slotu |
| Boot contador | Boot deneme sayacı (döngü engelleme) |
| Recovery | Kurtarma image/mod |

---

## 18. Cross-Referanslar ve Yol Haritası

| Konu | İlgili Dosya |
|------|--------------|
| Firmware genel bakış | [[electronic/firmware/index]] |
| DSP firmware / clock | [[dsp-firmware]] |
| OTA / A/B / secure boot | [[ota-update]] |
| RTOS / watchdog | [[rtos]] |
| Donanım / DAC | [[electronic/hardware/index]] · [[ADR-038-8.1-sound-card-chip-selection]] |

### 18.1 Yol Haritası

| Aşama | İçerik | Durum |
|-------|--------|-------|
| Faz 1 | Boot hiyerarşisi + A/B + fast-boot tasarımı (bu dosya) | Tasarım |
| Faz 2 | Bootloader entegrasyonu (imza + yuva) | PLANNED (repo kodu YOK) |
| Faz 3 | Boot-süresi ölçüm altyapısı | PLANNED (ölçüm) |
| Faz 4 | Donanım üzerinde doğrulama | PLANNED |

---

## 19. Güç Sıralaması (Power Sequencing)

Boot, güç doğru şekilde ayağa kalkmadan başlamaz; PMIC (power management IC) sıralaması boot'un öncüsüdür.

| Sıra | Rail | Durum | Boot Etkisi |
|------|------|-------|-------------|
| 1 | Main input (VBAT/VIN) | Stabil | CPU reset serbest |
| 2 | Core rail (DVDD) | Stabil | CPU çalışır |
| 3 | I/O rail (AVDD/IOVDD) | Stabil | Bus çalışır |
| 4 | Analog rail (sesso analog) | Stabil | DAC/ADC temiz |
| 5 | Reset çıkış | Deassert | BL0 başlar |

### 19.1 Power-Good (PGOOD) Beklemesi

Boot ROM/BL1, ilgili rail'lerin power-good sinyallerini bekler; erken başlama kararsızlık üretir. PGOOD timeout → safe-mode/reset. Bu sıralama, ses analog rail'inin temiz kalkmasını sağlar (pop/noise önleme, bkz. [[dsp-firmware]] §21.1).

### 19.2 Koruma Rölesi ile Etkileşim

Elektronik kural 10 (DC offset > 0.5V → koruma rölesi), boot sırasında da geçerlidir: DAC init tamamlanmadan ve clock stabil olmadan koruma rölesi hoparlöre bağlanmaz. Boot tamamlandıktan ve DC-offset kontrolü temizlendikten sonra rölesi kapatılır.

---

## 20. Reset Kaynakları ve Tipleri

Boot'un nasıl tetiklendiği, hangi aşamanın tekrarlanacağını belirler.

| Reset Kaynağı | Tetik | Boot Noktası |
|---------------|-------|--------------|
| Power-on (POR) | İlk güç | BL0 (tam) |
| Warm reset | Yazılım/watchdog | BL1 (DDR korunabilir) |
| Software reset | Kontrollü yeniden başlatma | BL1 |
| Watchdog reset | Kick yok (bkz. [[rtos]] §21) | BL1 + contador++ |
| Brown-out | Düşük voltaj | POR (tam) |
| External reset | Pin/manuel | BL0/BL1 |

### 20.1 Reset Türü ve A/B Etkisi

Warm reset, DDR içeriğini koruyabildiğinde firmware daha hızlı devralır (fast boot, Bölüm 10). Watchdog reset, boot contador'ı artırır (Bölüm 8.1) — böylece tekrarlanan başarısız boot, iki yuva arasında geçişe zorlanır.

---

## 21. Boot Yapılandırması ve OTP

Bazı boot kararları, kalıcı (salt-okunur) yapılandırmaya bağlıdır.

| Yapılandırma | Depolama | Boot Etkisi |
|---------------|----------|-------------|
| Boot kaynağı | OTP / strap pin | Flash vs USB vs ağ |
| Anti-rollback counter | OTP / secure counter | Eski sürüm reddi (Bölüm 13.1) |
| Secure-boot enable | OTP | İmza zorunlu (Bölüm 13) |
| Yuva tercihi | NVS / flag | A/B seçimi (Bölüm 8) |
| Debug lock | OTP / eFuse | JTAG kilit (Bölüm 23) |

### 21.1 Strap Pin ve Boot Modu

Bazı platformlar, boot modunu ayarlamak için power-on strap pinleri kullanır (ör. normal boot vs recovery vs USB-load). Bu pinler, OTP kadar kalıcı olmasa da ilk boot kararını belirler.

---

## 22. Platform Firmware Detayları (RPi5)

RPi5, x86'a özgü olmayan bir GPU-firmware tabanlı boot kullanır; Linux kernel'inden önce GPU firmware yüklenir.

| Bileşen | Rol |
|---------|-----|
| SoC Boot ROM | İlk stage |
| Bootloader / EEPROM | Firmware yönlendirme |
| start*.elf (GPU firmware) | GPU + temel sistem init |
| Kernel Image | Linux (+ Xenomai patch) |
| Device Tree (DTB) | Donanım tanımı |
| initramfs / rootfs | Kök dosya sistemi |

### 22.1 Xenomai Entegrasyon Noktası

Xenomai dual-kernel patch'i, Linux kernel boot sırasında yüklenir (bkz. [[rtos]] §6); boot sırası, Xenomai'nin cobalt çekirdeğini devreye almasını gerektirir. Boot akışı, real-time katmanın hazır olmasını garanti eder.

---

## 23. Debug ve Kurtarma Arayüzleri

Boot sorunlarını teşhis ve kurtarmak için arayüzler:

| Arayüz | Kullanım | Erişim |
|--------|----------|--------|
| Serial/UART log | Boot log akışı | Başlangıçtan itibaren |
| JTAG/SWD | Düşük seviye debug | Boot kilidi açıkken |
| USB recovery | Firmware yeniden yükleme | Recovery mod |
| LED hata kodu | Görsel teşhis (bkz. Bölüm 4) | Her zaman |
| Boot contador | Boot deneme sayacı | NVS/OTP okuma |

### 23.1 Debug Kilidi (Secure Debug)

Üretim cihazlarında JTAG/SWD, güvenlik için kilitlenir (Bölüm 21); kurtarma yalnız imzalı USB recovery ile yapılır. Bu, yetkisiz firmware yüklemesini engeller (Bölüm 13 secure boot ile uyumlu).

---

## 24. Özet ve Anahtar Kararlar

| # | Karar | Dayanak |
|---|-------|---------|
| 1 | Çok aşamalı boot (BL0→BL2) | Güvenlik + soyutlama (Bölüm 6) |
| 2 | A/B yuva + boot contador | Boot döngüsü engelleme (Bölüm 8) |
| 3 | İmza zinciri + anti-rollback | Secure boot (Bölüm 13) |
| 4 | Güç sıralaması + PGOOD | Temiz kalkış (Bölüm 19) |
| 5 | Reset-tipi aware contador | Watchdog → yuva geçişi (Bölüm 20.1) |
| 6 | Fast-boot hedefi ~0.6s | UX (Bölüm 10) |
| 7 | Ölçülen boot süresi | İddia değil, ölçüm (Bölüm 12) |

---

## 25. LED Hata Kodları (Görsel Teşhis)

Bölüm 4'teki "LED error kodu"nun detaylı haritası. Ekran/seri log olmadan teşhis için blink desenleri kullanılır.

| Kod | Blink | Anlam | Aksiyon |
|-----|-------|-------|---------|
| E1 | 1× kısa | Firmware load fail | Retry → recovery |
| E2 | 2× kısa | DAC/I2C init fail | Alternatif DAC |
| E3 | 3× kısa | USB enumerate fail | Yeniden dene |
| E4 | 4× kısa | Clock/PLL unlock | Yeniden kilit |
| E5 | 5× kısa | DDR/BL1 fail | Reset |
| W1 | Sürekli yavaş | Watchdog recovery | Yuva geçişi |
| OK | Tek kısa | Sistem hazır | — |

### 25.1 LED Kodu Prensibi

LED kodları, boot'un hangi aşamada takıldığını gösterir (Bölüm 6.1'e göre). Bu, saha desteğinde seri log olmadan teşhisi mümkün kılar; kod haritası firmware'e gömülüdür.

---

## 26. Çok-Çekirdekli (XMOS) Boot

XMOS xCORE çok-çekirdeklidir; her çekirdek boot sırasında başlatılır ve DSP döngüsüne katılır.

| Adım | Aksiyon |
|------|---------|
| 1 | Boot ROM ana çekirdeği başlatır |
| 2 | Loader firmware image'ı çeker (Bölüm 6) |
| 3 | Her çekirdeğe kod dağıtılır (tile/core) |
| 4 | Clock/I2S init (ana çekirdek, bkz. [[dsp-firmware]] §6) |
| 5 | Çekirdeklerarası kanallar kurulur (bkz. [[dsp-firmware]] §12.1) |
| 6 | DSP döngüsü tüm çekirdeklerde eşzamanlı başlar |

### 26.1 Çekirdek Senkronizasyon Barrier

Tüm çekirdekler, DSP döngüsü başlamadan önce bir barrier (senkronizasyon noktası) ile hizalanır; erken başlayan çekirdek, hazır olmayan diğerlerine veri göndermez. Bu, Bölüm 9'daki döngü bütçesinin bozulmasını önler.

---

## 27. Boot + DSP/DAC Init Entegrasyon Sırası

Bölüm 2'deki "DSP Chain Init → I2S Configure → PCM3168A Init" adımları, clock ve koruma sıralamasıyla birleşir.

```
Clock stabil (PLL lock, bkz. [[dsp-firmware]] §6)
  → DAC reset (I2C)
  → DAC format/rate yapılandır
  → I2S başlat (DMA hazır)
  → DSP katsayı yükle (bkz. [[dsp-firmware]] §10)
  → DSP döngüsü başlat
  → DC-offset kontrolü temiz → koruma rölesi kapat (elektronik kural 10)
  → mute kaldır → sistem hazır (Bölüm 3)
```

### 27.1 Sıralama Kuralı

Bu sıra ters çevrilemez: DAC, clock stabil olmadan init edilemez; DSP döngüsü, DMA hazır olmadan başlamaz; koruma rölesi, DC-offset temiz olmadan kapatılmaz. Her adım, bir öncekinin başarısını şart koşar.

---

## 28. Bağlantılar (Summary Links)

`[[electronic/firmware/index]]` · `[[dsp-firmware]]` · `[[rtos]]` · `[[ota-update]]` · `[[electronic/hardware/index]]` · `[[electronic/dsp/index]]` · `[[ADR-038-8.1-sound-card-chip-selection]]`

---

## 29. Boot Sırası Doğrulama Kapıları (Dur-Kararları)

Boot sırası değiştirilemez içerik olduğundan, her revizyon önce aşağıdaki kapılardan geçer:

| # | Kapı | Koşul | Durum |
|---|------|-------|-------|
| 1 | Clock kapısı | PLL lock alınmadan DAC/I2S init yok (§27) | DUR |
| 2 | DMA kapısı | DMA hazır olmadan DSP döngüsü başlamaz (§27) | DUR |
| 3 | DC-offset kapısı | DC-offset temiz değilse koruma rölesi açık kalır (§27, elektronik kural 10) | DUR |
| 4 | İmza kapısı | Firmware imza/CRC doğrulaması başarısızsa rollback (§7, [[ota-update]]) | DUR |
| 5 | Barrier kapısı | Çekirdek barrier'ı tamamlanmadan DSP döngüsü start yok (§26.1) | DUR |
| 6 | Test kapısı | Boot davranışı değiştiyse test senaryosu şart (firmware AGENTS.md §3.2) | DUR |

**Ölçüm kanıtı yoksa iddia yazılmaz:** bu bölüm boot sırasının *doğrulama kurallarını* taşır; gerçek boot süresi/latency ölçümü repo'da yoktur → `⚠️ VERIFICATION REQUIRED`.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
