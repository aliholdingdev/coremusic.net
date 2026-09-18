---
type: system
category: hardware-design
title: "CoreMusic Electronics â€” Hardware Design Index"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team Â· Human Mode Â· Truth Mode
---

# CoreMusic Electronics â€” Hardware Design

**Zorunlu BaÄŸlantÄ±lar:** [[electronic/index]] Â· [[brain.md]] Â· [[architecture/k0-k5-software/k0-os-layer]]

---

## 1. AmaÃ§

Hardware Design, CoreMusic ELECTRONICS platformunun tÃ¼m fiziksel donanÄ±m tasarÄ±mÄ±nÄ±, PCB yerleÅŸimini, EMI/EMC uyumluluÄŸunu ve ses yÃ¶nlendirmesini kapsar.

---

## 2. DonanÄ±m BileÅŸenleri

| BileÅŸen | Dosya | Kapsam |
|---------|-------|--------|
| PCB TasarÄ±mÄ± | [[pcb-design]] | ModÃ¼ler PCB, montaj |
| EMI/EMC | [[emi-emc]] | Elektromanyetik uyumluluk |
| Audio Routing | [[audio-routing]] | Analog/digital ses yÃ¶nlendirme |
| Ground Plane | [[ground-plane]] | Topraklama, gÃ¼Ã§ daÄŸÄ±tÄ±mÄ± |

---

## 3. Ä°ÅŸlemci PlatformlarÄ±

| Platform | Mimari | KullanÄ±m | Durum |
|----------|--------|----------|-------|
| XMOS XU316 | xcore | USB Audio + DSP | âœ… Ana platform |
| Raspberry Pi | ARM64 | Embedded audio | âœ… |
| STM32 | ARM Cortex-M | MCU tabanlÄ± | âœ… |
| ESP32 | Xtensa | IoT audio | âœ… |
| Intel/AMD | x86/x64 | Desktop/Server | âœ… |

---

## 4. Bellek YapÄ±larÄ±

| Tip | KullanÄ±m |
|-----|----------|
| SRAM | HÄ±zlÄ± eriÅŸim |
| DDR4/DDR5 | Ana bellek |
| Flash | Firmware depolama |
| EEPROM | KonfigÃ¼rasyon |
| eMMC | GÃ¶mÃ¼lÃ¼ depolama |

---

## 5. Ses BaÄŸlantÄ±larÄ±

| BaÄŸlantÄ± | Tip | KullanÄ±m |
|----------|-----|----------|
| RCA | Analog | Ev ses |
| TRS (6.35mm) | Analog | Profesyonel |
| XLR | Analog | StÃ¼dyo |
| Optical (TOSLINK) | Dijital | Ev sinema |
| SPDIF | Dijital | Dijital ses |
| AES/EBU | Dijital | Profesyonel |
| HDMI ARC | Dijital | TV entegrasyonu |
| HDMI eARC | Dijital | YÃ¼ksek bant geniÅŸliÄŸi |
| USB | Dijital | Audio interface |
| I2S | Dijital | Dahili haberleÅŸme |

---

## 6. HaberleÅŸme Arabirimleri

| Arabirim | HÄ±z | KullanÄ±m |
|----------|-----|----------|
| USB 2.0 | 480 Mbps | Ses cihazlarÄ± |
| USB 3.x | 5-20 Gbps | YÃ¼ksek hÄ±zlÄ± |
| Ethernet 1Gbps | 1 Gbps | AÄŸ ses |
| Wi-Fi | 150Mbps-6Gbps | Kablosuz ses |
| Bluetooth/BLE | 1-3 Mbps | Kablosuz kulaklÄ±k |
| UART | 115K-4Mbps | Debug, GPIO |
| SPI | 10-50MHz | YÃ¼ksek hÄ±zlÄ± |
| I2C | 100-400KHz | DÃ¼ÅŸÃ¼k hÄ±zlÄ± |
| CAN Bus | 125K-1Mbps | Automotive |

---

## 7. DonanÄ±m TasarÄ±m Ä°lkeleri

| Ä°lke | AÃ§Ä±klama |
|------|----------|
| ModÃ¼ler PCB | Her modÃ¼l baÄŸÄ±msÄ±z kart |
| EMI/EMC Uyumlu | CE, RoHS standartlarÄ± |
| DÃ¼ÅŸÃ¼k GÃ¼rÃ¼ltÃ¼ | Low noise design |
| YÃ¼ksek Verimlilik | <%10 kayÄ±p |
| Kolay BakÄ±m | Servis edilebilir |
| GeniÅŸletilebilir | Yeni modÃ¼l desteÄŸi |
| Firmware GÃ¼ncellenebilir | OTA + USB |

---

## 8. GÃ¼Ã§ YÃ¶netimi

| Gerilim | KullanÄ±m |
|---------|----------|
| 3.3V | Dijital lojik |
| 5V | USB, Arduino |
| 12Vâ€“24V DC | **Ana gÃ¼Ã§ giriÅŸi (DC adaptÃ¶r/batarya)** |
| Â±42V DC | Class AB amfi (Boost converter ile yÃ¼kseltilir) |
| PoE | AÄŸ cihazlarÄ± |
| USB-C PD | TaÅŸÄ±nabilir |

---

## 9. TÃ¼rkiye Tedarik Stratejisi

### 9.1 TÃ¼rk TedarikÃ§iler

| # | TedarikÃ§i | Web | Kapsam | Kargo |
|---|----------|-----|--------|-------|
| 1 | **West-Electronic** | tr.west-electronic.com | LM3886, entegre devreler | DHL/UPS/FedEx |
| 2 | **E-Komponent** | e-komponent.com | DigiKey TÃ¼rkiye yetkili | HaftalÄ±k yÃ¼kleme |
| 3 | **Fidersan** | fidersan.com | DigiKey + Mouser | 5-8 iÅŸ gÃ¼nÃ¼ |
| 4 | **Ayson Elektronik** | aysonelektronik.com | Ä°stanbul DigiKey | AynÄ± gÃ¼n |
| 5 | **UlutaÅŸ Elektronik** | ulutaselektronik.com | IRS2092S, TDA7564 | YurtiÃ§i |
| 6 | **Park Component** | parkcomponent.com | Genel elektronik | KapÄ± teslim |

### 9.2 Online SatÄ±n Alma

| # | Platform | Kapsam | Kargo | SÃ¼re |
|---|----------|--------|-------|------|
| 1 | **AliExpress** (tr.aliexpress.com) | TPA3255, LM3886 board'lar | Ãœcretsiz kargo | 15-30 gÃ¼n |
| 2 | **DigiKey** (E-Komponent Ã¼zerinden) | TÃ¼m Ã§ipler | 5-8 iÅŸ gÃ¼nÃ¼ | HÄ±zlÄ± |
| 3 | **Mouser** (Fidersan Ã¼zerinden) | TÃ¼m Ã§ipler | 5-8 iÅŸ gÃ¼nÃ¼ | HÄ±zlÄ± |

### 9.3 SatÄ±n Alma Stratejisi

| Strateji | Yol | SÃ¼re | Maliyet |
|----------|-----|------|---------|
| **En HÄ±zlÄ±** | West-Electronic + UlutaÅŸ | 1-2 gÃ¼n | YÃ¼ksek |
| **En HÄ±zlÄ± (geniÅŸ)** | E-Komponent + Fidersan | 5-8 gÃ¼n | Orta |
| **En Ucuz** | AliExpress | 15-30 gÃ¼n | DÃ¼ÅŸÃ¼k |
| **En GÃ¼venilir** | DigiKey (E-Komponent) | 5-8 gÃ¼n | Orta-YÃ¼ksek |

### 9.4 BileÅŸen FiyatlarÄ± (TÃ¼rkiye)

| BileÅŸen | Kaynak | Fiyat (TRY) | Stok |
|---------|--------|-------------|------|
| LM3886TF/NOPB | West-Electronic | ~â‚º150-200 | âœ… 5173 adet |
| IRS2092S | UlutaÅŸ Elektronik | ~â‚º100-150 | âœ… |
| TPA3255 Board | AliExpress | ~â‚º500-1000 | âœ… |
| TPA3118D2 Board | AliExpress | ~â‚º100-200 | âœ… |
| LM3886 Board | AliExpress | ~â‚º200-400 | âœ… |
| TDA7294 Board | AliExpress | ~â‚º200-300 | âœ… |
| XMOS XU316 | DigiKey (E-Komponent) | ~â‚º800-1200 | âœ… |
| PCM3168A | DigiKey (Fidersan) | ~â‚º100-150 | âœ… |

### 9.5 TÃ¼rkiye Tedarik KurallarÄ±

| # | Kural | AÃ§Ä±klama |
|---|-------|----------|
| 1 | **Ã–nce TÃ¼rkiye** | TÃ¼rk tedarikÃ§ilerden kontrol et |
| 2 | **Orijinal ParÃ§a** | DigiKey/Mouser yetkili Ã¼zerinden al |
| 3 | **Garanti** | Orijinal parÃ§a garantisi zorunlu |
| 4 | **HÄ±zlÄ± Kargo** | Acil durumda West-Electronic/UlutaÅŸ |
| 5 | **Maliyet** | Toplu alÄ±mda indirim iste |
| 6 | **Stok KontrolÃ¼** | SipariÅŸ Ã¶ncesi stok doÄŸrula |

---

## 10. ADR ReferanslarÄ±

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | PCM3168A, XMOS XU316 |
| [[ADR-017-dsp-hardware-mode]] | DSP hardware |

---

## 10. Ã‡apraz Referanslar

| Kaynak | Hedef | Ä°liÅŸki |
|--------|-------|--------|
| Hardware | [[electronic/firmware/index]] | Firmware katmanÄ± |
| Hardware | [[electronic/drivers/index]] | Driver katmanÄ± |
| Hardware | [[electronic/amplifier/index]] | Amplifier tasarÄ±mÄ± |
| Hardware | [[electronic/dsp/index]] | DSP donanÄ±mÄ± |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team Â· Human Mode Â· Truth Mode

