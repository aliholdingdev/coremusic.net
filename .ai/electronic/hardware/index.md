---
type: system
category: hardware-design
title: "CoreMusic Electronics — Hardware Design Index"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic Electronics — Hardware Design

**Zorunlu Bağlantılar:** [[electronic/index]] · [[brain.md]] · [[architecture/l0-infrastructure]]

---

## 1. Amaç

Hardware Design, CoreMusic ELECTRONICS platformunun tüm fiziksel donanım tasarımını, PCB yerleşimini, EMI/EMC uyumluluğunu ve ses yönlendirmesini kapsar.

---

## 2. Donanım Bileşenleri

| Bileşen | Dosya | Kapsam |
|---------|-------|--------|
| PCB Tasarımı | [[pcb-design]] | Modüler PCB, montaj |
| EMI/EMC | [[emi-emc]] | Elektromanyetik uyumluluk |
| Audio Routing | [[audio-routing]] | Analog/digital ses yönlendirme |
| Ground Plane | [[ground-plane]] | Topraklama, güç dağıtımı |

---

## 3. İşlemci Platformları

| Platform | Mimari | Kullanım | Durum |
|----------|--------|----------|-------|
| XMOS XU316 | xcore | USB Audio + DSP | ✅ Ana platform |
| Raspberry Pi | ARM64 | Embedded audio | ✅ |
| STM32 | ARM Cortex-M | MCU tabanlı | ✅ |
| ESP32 | Xtensa | IoT audio | ✅ |
| Intel/AMD | x86/x64 | Desktop/Server | ✅ |

---

## 4. Bellek Yapıları

| Tip | Kullanım |
|-----|----------|
| SRAM | Hızlı erişim |
| DDR4/DDR5 | Ana bellek |
| Flash | Firmware depolama |
| EEPROM | Konfigürasyon |
| eMMC | Gömülü depolama |

---

## 5. Ses Bağlantıları

| Bağlantı | Tip | Kullanım |
|----------|-----|----------|
| RCA | Analog | Ev ses |
| TRS (6.35mm) | Analog | Profesyonel |
| XLR | Analog | Stüdyo |
| Optical (TOSLINK) | Dijital | Ev sinema |
| SPDIF | Dijital | Dijital ses |
| AES/EBU | Dijital | Profesyonel |
| HDMI ARC | Dijital | TV entegrasyonu |
| HDMI eARC | Dijital | Yüksek bant genişliği |
| USB | Dijital | Audio interface |
| I2S | Dijital | Dahili haberleşme |

---

## 6. Haberleşme Arabirimleri

| Arabirim | Hız | Kullanım |
|----------|-----|----------|
| USB 2.0 | 480 Mbps | Ses cihazları |
| USB 3.x | 5-20 Gbps | Yüksek hızlı |
| Ethernet 1Gbps | 1 Gbps | Ağ ses |
| Wi-Fi | 150Mbps-6Gbps | Kablosuz ses |
| Bluetooth/BLE | 1-3 Mbps | Kablosuz kulaklık |
| UART | 115K-4Mbps | Debug, GPIO |
| SPI | 10-50MHz | Yüksek hızlı |
| I2C | 100-400KHz | Düşük hızlı |
| CAN Bus | 125K-1Mbps | Automotive |

---

## 7. Donanım Tasarım İlkeleri

| İlke | Açıklama |
|------|----------|
| Modüler PCB | Her modül bağımsız kart |
| EMI/EMC Uyumlu | CE, RoHS standartları |
| Düşük Gürültü | Low noise design |
| Yüksek Verimlilik | <%10 kayıp |
| Kolay Bakım | Servis edilebilir |
| Genişletilebilir | Yeni modül desteği |
| Firmware Güncellenebilir | OTA + USB |

---

## 8. Güç Yönetimi

| Gerilim | Kullanım |
|---------|----------|
| 3.3V | Dijital lojik |
| 5V | USB, Arduino |
| 12V–24V DC | **Ana güç girişi (DC adaptör/batarya)** |
| ±42V DC | Class AB amfi (Boost converter ile yükseltilir) |
| PoE | Ağ cihazları |
| USB-C PD | Taşınabilir |

---

## 9. Türkiye Tedarik Stratejisi

### 9.1 Türk Tedarikçiler

| # | Tedarikçi | Web | Kapsam | Kargo |
|---|----------|-----|--------|-------|
| 1 | **West-Electronic** | tr.west-electronic.com | LM3886, entegre devreler | DHL/UPS/FedEx |
| 2 | **E-Komponent** | e-komponent.com | DigiKey Türkiye yetkili | Haftalık yükleme |
| 3 | **Fidersan** | fidersan.com | DigiKey + Mouser | 5-8 iş günü |
| 4 | **Ayson Elektronik** | aysonelektronik.com | İstanbul DigiKey | Aynı gün |
| 5 | **Ulutaş Elektronik** | ulutaselektronik.com | IRS2092S, TDA7564 | Yurtiçi |
| 6 | **Park Component** | parkcomponent.com | Genel elektronik | Kapı teslim |

### 9.2 Online Satın Alma

| # | Platform | Kapsam | Kargo | Süre |
|---|----------|--------|-------|------|
| 1 | **AliExpress** (tr.aliexpress.com) | TPA3255, LM3886 board'lar | Ücretsiz kargo | 15-30 gün |
| 2 | **DigiKey** (E-Komponent üzerinden) | Tüm çipler | 5-8 iş günü | Hızlı |
| 3 | **Mouser** (Fidersan üzerinden) | Tüm çipler | 5-8 iş günü | Hızlı |

### 9.3 Satın Alma Stratejisi

| Strateji | Yol | Süre | Maliyet |
|----------|-----|------|---------|
| **En Hızlı** | West-Electronic + Ulutaş | 1-2 gün | Yüksek |
| **En Hızlı (geniş)** | E-Komponent + Fidersan | 5-8 gün | Orta |
| **En Ucuz** | AliExpress | 15-30 gün | Düşük |
| **En Güvenilir** | DigiKey (E-Komponent) | 5-8 gün | Orta-Yüksek |

### 9.4 Bileşen Fiyatları (Türkiye)

| Bileşen | Kaynak | Fiyat (TRY) | Stok |
|---------|--------|-------------|------|
| LM3886TF/NOPB | West-Electronic | ~₺150-200 | ✅ 5173 adet |
| IRS2092S | Ulutaş Elektronik | ~₺100-150 | ✅ |
| TPA3255 Board | AliExpress | ~₺500-1000 | ✅ |
| TPA3118D2 Board | AliExpress | ~₺100-200 | ✅ |
| LM3886 Board | AliExpress | ~₺200-400 | ✅ |
| TDA7294 Board | AliExpress | ~₺200-300 | ✅ |
| XMOS XU316 | DigiKey (E-Komponent) | ~₺800-1200 | ✅ |
| PCM3168A | DigiKey (Fidersan) | ~₺100-150 | ✅ |

### 9.5 Türkiye Tedarik Kuralları

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | **Önce Türkiye** | Türk tedarikçilerden kontrol et |
| 2 | **Orijinal Parça** | DigiKey/Mouser yetkili üzerinden al |
| 3 | **Garanti** | Orijinal parça garantisi zorunlu |
| 4 | **Hızlı Kargo** | Acil durumda West-Electronic/Ulutaş |
| 5 | **Maliyet** | Toplu alımda indirim iste |
| 6 | **Stok Kontrolü** | Sipariş öncesi stok doğrula |

---

## 10. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | PCM3168A, XMOS XU316 |
| [[ADR-017-dsp-hardware-mode]] | DSP hardware |

---

## 10. Çapraz Referanslar

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| Hardware | [[electronic/firmware/index]] | Firmware katmanı |
| Hardware | [[electronic/drivers/index]] | Driver katmanı |
| Hardware | [[electronic/amplifier/index]] | Amplifier tasarımı |
| Hardware | [[electronic/dsp/index]] | DSP donanımı |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
