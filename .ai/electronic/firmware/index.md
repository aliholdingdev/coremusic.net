---
type: system
category: firmware-architecture
title: "CoreMusic Electronics — Firmware Architecture Index"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic Electronics — Firmware Architecture

**Zorunlu Bağlantılar:** [[electronic/index]] · [[brain.md]] · [[electronic/hardware/index]]

## 1. Amaç

Firmware Architecture, CoreMusic ELECTRONICS platformunun tüm gömülü sistemlerin düşük seviyeli yazılımını, boot süreçlerini, RTOS yapısını, HAL katmanını ve güncelleme mekanizmalarını kapsar.

## 2. Dosya Envanteri (Disk Ölçümü — 2026-10-13)

Klasörde 6 markdown dosyası bulunur; 4'ü içerik, 2'si agent talimatıdır (AGENTS.md · CLAUDE.md — dokunulmaz, salt-okunur).

| # | Dosya | Kapsam |
|---|-------|--------|
| 1 | `boot-sequence.md` | Bootloader + RTOS başlatma, zamanlama |
| 2 | `rtos.md` | RTOS seçimi, zamanlama, bellek (heap yasak) |
| 3 | `dsp-firmware.md` | DSP firmware katmanı (HAL + driver soyutlama) |
| 4 | `ota-update.md` | OTA güncelleme, imza/CRC, rollback |

**Düzeltme (2026-10-13):** Eski `[[bootloader-rtos]]`, `[[hal-driver]]`, `[[update-recovery]]` wikilink'leri diskte YOKtu (envanter ile uyuşmuyordu) — gerçek dosya adlarıyla değiştirildi.

## 3. Firmware Stack (katman sırası — üstten alta)

Application Layer → Middleware (REST API, IPC) → DSP Engine → Driver Layer → HAL (Hardware Abstraction Layer) → RTOS (Real-Time Operating System) → Bootloader → Hardware.

## 4. Bootloader — Görevleri

Donanımı başlatır · EEPROM'dan konfigürasyon okur · Firmware doğrulama (imza kontrolü) · RTOS'u yükler · Hata durumunda recovery modu. Detay: [[boot-sequence]].

## 5. RTOS (Real-Time Operating System)

Zamanlama: preemptive priority-based · Görev sayısı: max 32 · Kesme: IRQ priority management · Bellek: **static allocation (heap yasak)** · Watchdog: hardware watchdog zorunlu. Seçenekler: FreeRTOS (ARM Cortex-M) · Zephyr (ARM, x86) · ThreadX (Azure RTOS) · bare-metal (basit sistemler). Detay: [[rtos]].

## 6. HAL (Hardware Abstraction Layer)

HAL, donanımdan bağımsız kod yazmayı sağlar: Application Code → HAL Interface → Platform-Specific HAL → Hardware. Arayüzleri: Audio HAL (I2S, TDM, SPDIF) · GPIO · SPI · I2C · UART · USB · Timer · DMA. Detay: [[dsp-firmware]].

## 7. Firmware Güncelleme

| Yöntem | Güvenlik | Kullanım |
|--------|----------|----------|
| OTA (Wi-Fi/Ethernet) | TLS + imza | Uzaktan güncelleme |
| USB DFU | İmza | Yerel güncelleme |
| Serial | Yok | Debug/prototip |
| Recovery Mode | Backup firmware | Kurtarma |

Akış: yeni firmware indir → **imza doğrulama** → CRC kontrolü → backup mevcut firmware → flash yeni firmware → reboot → doğrulama → başarısızsa **rollback**. Detay: [[ota-update]].

## 8. Firmware Güvenliği

Secure Boot (imzalı bootloader) · Firmware Signing (RSA/ECDSA) · Anti-Rollback (eski sürüme geçiş engeli) · Encrypted Flash (şifreli firmware depolama) · Tamper Detection (yetkisiz erişim algılama). OTA güvenlik akışı → `../architecture/07-security` ile uyumlu olmalıdır (firmware AGENTS.md §3.1).

## 9. Cihaz Bazlı Firmware

| Cihaz | İşlemci | RTOS | HAL |
|-------|---------|------|-----|
| 7.1 Amp (Class AB) | XMOS XU316 | Bare-metal | XMOS HAL |
| USB Audio | XMOS XU316 | Bare-metal | XMOS HAL |
| Raspberry Pi HAT | BCM2711 | Linux | ALSA |
| DSP Processor | STM32 | FreeRTOS | STM32 HAL |
| IoT Gateway | ESP32 | FreeRTOS | ESP-IDF |

## 10. ADR Referansları

[[ADR-017-dsp-hardware-mode]] (XMOS, JUCE) · [[ADR-038-8.1-sound-card-chip-selection]] (XMOS XU316).

## 11. Çapraz Referanslar

[[electronic/hardware/index]] (donanım katmanı) · [[electronic/drivers/index]] (driver güncellemesi) · [[electronic/dsp/index]] (DSP engine) · [[architecture/07-security/index]] (secure boot).

**Sıkıştırma notu (2026-10-13):** 196 satırlık dizin 40–100 bandına sıkıştırıldı; ASCII katman diyagramları (§3, §6) ve güncelleme akış şeması (§7) düz metne indirildi — bilgi alt içerik dosyalarında (`boot-sequence.md`, `rtos.md`, `dsp-firmware.md`, `ota-update.md`) mevcuttur.

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
