---
type: system
category: hardware-design
title: "CoreMusic Electronics — Hardware Design Index"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic Electronics — Hardware Design

**See also:** [[electronic/index]] · [[electronic/amplifier/index]] — **Zorunlu Bağlantılar:** [[electronic/hardware/pcb-design]] · [[electronic/hardware/amplifier-pcb]] · [[electronic/hardware/audio-interface]] · [[electronic/hardware/frequency-response]] · [[electronic/hardware/snr-thd-measurement]]

## 1. Amaç

Hardware Design, CoreMusic ELECTRONICS platformunun tüm fiziksel donanım tasarımını, PCB yerleşimini, EMI/EMC uyumluluğunu ve ses yönlendirmesini kapsar. Bu indeks klasörün SSOT kataloğudur: hangi alt dosya neyi kapsar, PCB/amfi PCB/audio interface birbirine nasıl bağlanır, frekans yanıtı ve SNR/THD ölçüm protokolleri nerede tanımlıdır ve hangi ADR'ler donanım seçimini bağlar (ADR-038, ADR-063).

## 2. Dosya Envanteri (Disk Ölçümü — 2026-10-13)

Klasörde 8 markdown dosyası bulunur; 6'sı içerik, 2'si agent talimatıdır (AGENTS.md · CLAUDE.md — dokunulmaz, salt-okunur).

| # | Dosya | Kapsam |
|---|-------|--------|
| 1 | `pcb-design.md` | Katman, trace, analog/dijital ayırma, EMC (§6) |
| 2 | `amplifier-pcb.md` | Amfi güç yolu, termal, star ground |
| 3 | `audio-interface.md` | XMOS XU316 + PCM3168A USB ses arayüzü (ADR-038) |
| 4 | `frequency-response.md` | Sweep/ESS protokolü, kabul testi §17.2 |
| 5 | `snr-thd-measurement.md` | SNR >100dB / THD+N <0.01% ölçüm planı |

**Kural:** İçerik dosyaları bu klasörün üretim yüzeyidir; AGENTS/CLAUDE dosyalarına yalnız orkestratör dokunur. EMI/EMC kuralları ayrı dosyada değil, [[pcb-design]] §6 içindedir (2026-10-13 envanter düzeltmesi — eski `[[emi-emc]]`, `[[ground-plane]]`, `[[audio-routing]]` linkleri diskte YOKtu, kaldırıldı).

## 3. Donanım Bileşenleri

[[pcb-design]] (layout kuralları: modüler PCB, katman, montaj) · [[amplifier-pcb]] (amfi kart yerleşimi: güç yolu, termal, star ground) · [[audio-interface]] (8.1 sinyal zinciri USB→I2S→DAC) · [[frequency-response]] (ölçüm raporu, 20Hz-20kHz ±0.5dB hedef) · [[snr-thd-measurement]] (SNR/THD performans raporu).

## 4. Platform, Bağlantı, İlkeler (özet)

**Ana platform XMOS XU316'dır** (USB Audio + DSP, ADR-038); Raspberry Pi (ARM64), STM32 (Cortex-M), ESP32 (Xtensa) gömülü kontrol/IoT katmanı, Intel/AMD x86 ASIO host'tur — ses zincirinin kritik yolunda yalnız XU316 yer alır. Bellek yapıları: XU316 dahili SRAM (hızlı DSP erişimi) · DDR4/DDR5 (host) · Flash (firmware) · EEPROM (konfigürasyon).

Ses bağlantıları: RCA/TRS/XLR (analog) · TOSLINK/SPDIF/AES-EBU/HDMI ARC-eARC (dijital) · USB (XU316 audio interface) · I2S (XU316↔PCM3168A dahili) — 8.1 surround 8 kanal DAC (PCM3168A) ile kurulur, bkz. [[electronic/hardware/audio-interface]] §3. Haberleşme: USB 2.0 480 Mbps (XU316 High-Speed) · Ethernet 1Gbps · Wi-Fi · Bluetooth/BLE · UART/SPI/I2C (DAC konfigürasyonu SPI 10-50MHz) · CAN (automotive).

Tasarım ilkeleri: modüler PCB · EMI/EMC uyumlu (CE, RoHS) · düşük gürültü (analog ayrım) · yüksek verimlilik (<%10 kayıp) · kolay bakım · genişletilebilir · firmware OTA + USB ile güncellenebilir.

## 5. Güç Yönetimi

3.3V (dijital lojik — XU316, PCM3168A dijital) · 5V (USB, PCM3168A analog) · **12V–24V DC ana güç girişi** (adaptör/batarya) → boost converter ile **±42V DC** (Class AB amfi) · PoE (ağ cihazları) · USB-C PD (taşınabilir). Güç zinciri detayı (boost hesapları, regülatör seçimi, ripple): [[electronic/amplifier/power-supply]] §2-§4.

## 6. Türkiye Tedarik Stratejisi

> ⚠️ Fiyat/stok verileri 2026-08-09 tarihli ilk taslaktan mirastır; 2026-10-13 revizyonunda canlı doğrulanmadı (bkz. §9-1).

Türk tedarikçiler: West-Electronic (LM3886, entegre) · E-Komponent (DigiKey Türkiye yetkili) · Fidersan (DigiKey + Mouser) · Ayson Elektronik (İstanbul DigiKey) · Ulutaş Elektronik (IRS2092S, TDA7564) · Park Component. Online: AliExpress (TPA3255/LM3886 board'lar, 15-30 gün) · DigiKey/Mouser (E-Komponent/Fidersan üzerinden, 5-8 iş günü). Strateji: en hızlı West-Electronic+Ulutaş (1-2 gün, yüksek maliyet) · en ucuz AliExpress (15-30 gün) · en güvenilir DigiKey yetkili kanal. Kurallar: önce Türkiye · orijinal parça garantisi zorunlu · sipariş öncesi stok doğrula · toplu alımda indirim iste. bileşen fiyat aralıkları (miras, ⚠️ doğrulanmadı): LM3886TF ~₺150-200 · IRS2092S ~₺100-150 · TPA3255 Board ~₺500-1000 · XMOS XU316 ~₺800-1200 · PCM3168A ~₺100-150.

## 7. Ölçüm & Doğrulama Protokolleri (Özet)

Frekans yanıtı [[frequency-response]] → 20Hz-20kHz ±0.5dB (100Hz-10kHz; kabul §17.2) · SNR [[snr-thd-measurement]] → >100dB A-weighted (tekrarlanabilirlik) · THD+N [[snr-thd-measurement]] → <0.01% @1kHz (tam yük, 8Ω dummy load). Ölçüm zinciri donanımı: [[electronic/hardware/audio-interface]] (PCM3168A ADC/DAC) — ölçüm arayüzü olarak da kullanılır.

## 8. Risk Matrisi (Klasör Geneli)

| # | Risk | Önlem |
|---|------|-------|
| 1 | Sahte/dangling wiki-link (dosya taşınması) | Envanter §2 disk ölçümüne bağlı |
| 2 | Tedarikçi fiyat/stok bayatlaması | §6 uyarı işareti |
| 3 | Ölçüm protokolü uygulanmazlığı (ekipman yok) | Ekipman listeleri dosya içi |
| 4 | ADR-038 sapması (yanlış DAC seçimi) | Yalnız PCM3168A zorunlu, §10 kontrol |
| 5 | Frekans yanıtı ölçümünde oda etkisi | near-field/kturnstile (frequency-response §6.3) |

## 9. Web Kaynakları ve Açık Konular

Aşağıdaki URL'ler 2026-10-13 tarihinde `Invoke-WebRequest -Method Head -UseBasicParsing -Uri $u -TimeoutSec 20` ile doğrulanmıştır; her URL bu dosyada tektir.

| İddia | URL | Durum |
|-------|-----|-------|
| TI, TPS62150 buck converter datasheet'ini resmi alan adında yayımlar (güç ağacı rail regülatörü referansı) | https://www.ti.com/lit/ds/symlink/tps62150.pdf | HTTP 200 |
| TI, TLV755P LDO datasheet'ini resmi alan adında yayımlar (düşük gürültülü lojik rail referansı) | https://www.ti.com/lit/ds/symlink/tlv755p.pdf | HTTP 200 |

Truth Mode gereği doğrulanamayan iddialar silinmez; açık madde olarak kaydedilir:

| # | Konu | Durum |
|---|------|-------|
| 1 | Tedarikçi fiyat/stok §6 tablosu 2026-08-09 mirası; canlı doğrulanmadı | ⚠️ VERIFICATION REQUIRED — sipariş öncesi canlı doğrula |
| 2 | DAC seçimi kontrolü: bu klasörde yalnız PCM3168A referans edilir (ADR-038 H001, kontrol edildi ✅) — ancak üretim PCB'sinde denetim gate'i tanımsız | ⚠️ VERIFICATION REQUIRED — BOM denetim kuralı ADR'ye bağlanmalı |
| 3 | XU316 sürüm kanıtı: iddia ADR-038'ten gelir; repo'da Neva Engine PLANNED (C++ kodu YOK) | ⚠️ VERIFICATION REQUIRED — firmware katmanı [[electronic/firmware/index]] |
| 4 | Ekipman listeleri ([[frequency-response]] §8 · [[snr-thd-measurement]]) satın alma kanıtı olmadan yazıldı | ⚠️ VERIFICATION REQUIRED |

## 10. ADR ve Çapraz Referanslar

ADR-038 (PCM3168A, XMOS XU316 — H001) · ADR-017 (DSP hardware, 32-bit float) · ADR-063 (Hardware Design Standards — `../../decisions/accepted/ADR-063-hardware-design-standards.md`). Çapraz: [[electronic/amplifier/index]] (güç stage, koruma) · [[electronic/amplifier/power-supply]] (±42V/PVDD zinciri) · [[electronic/firmware/index]] · [[electronic/drivers/index]] · [[electronic/dsp/index]].

**Sıkıştırma notu (2026-10-13):** 301 satırlık indeks 40–100 bandına indirildi; silinen tedarikçi/fiyat tabloları ve sözlük satırları bilgi olarak §6 özetinde ve alt içerik dosyalarında mevcuttur.

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
