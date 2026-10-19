---
type: system
category: driver-framework
title: "CoreMusic Electronics — Driver Framework Index"
date: 2026-08-09
updated: 2026-10-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic Electronics — Driver Framework

**Zorunlu Bağlantılar:** [[electronic/index]] · [[brain.md]] · [[architecture/07-security/index]]

## 1. Amaç

Driver Framework, CoreMusic ELECTRONICS platformunun tüm işletim sistemlerinde ses donanımıyla iletişim kuran sürücü altyapısını yönetir: ses akışı → [[audio-drivers]] · USB enumeration → [[usb-drivers]] · kablosuz → [[wifi-drivers]] ve [[bluetooth-drivers]] · Ethernet/NIC → [[network-drivers]] · gömülü MCU çevre birimleri → [[embedded-drivers]] · hotplug/udev → [[device-integration]].

Kapsam dışı: donanım şeması/PCB → [[electronic/hardware/index]] · firmware OTA/boot → [[electronic/firmware/index]] · amplifikatör güç katı → [[electronic/amplifier/index]] · DSP algoritması → [[electronic/dsp/index]]. Sürücü katmanı yalnızca işletim sistemi ile donanım arasındaki taşıma ve cihaz yaşam döngüsünden sorumludur; sinyal işleme DSP katmanında kalır. Her içerik dosyası 2026-10-13 derinleştirme turunda ≥500 tam satıra genişletilmiştir (ortak omurga: katman konumu · teknik matris · hata modları · ölçüm protokolü · HTTP 200 doğrulanmış kaynakça).

## 2. Dosya Envanteri

| # | Dosya | Kapsam |
|---|-------|--------|
| 1 | [[audio-drivers]] | ASIO, WASAPI, ALSA, CoreAudio — kernel↔HAL, DMA, buffer/period (§1-§9) |
| 2 | [[usb-drivers]] | USB yığını, isochronous/bulk, UAC/HID, enumeration (§1-§7) |
| 3 | [[wifi-drivers]] | İstemci/denetleyici, tarama/bağlanma, WPA, güç yönetimi (§1-§7) |
| 4 | [[bluetooth-drivers]] | HCI/L2CAP, eşleştirme, A2DP/SCO, BLE (§1-§11) |
| 5 | [[network-drivers]] | NIC katmanı, ring buffer/TSO, VLAN/QoS, PTP (§1-§11) |
| 6 | [[embedded-drivers]] | MCU çevre birimleri (SPI/I2C/UART), kesme/DMA, RTOS (§1-§10) |
| 7 | [[device-integration]] | Cihaz keşfi, probing/hotplug, udev/libudev (§1-§10) |

## 3. Katman / Komşu Sınırlar

Dikey yığın: Application → CoreMusic API → Platform Abstraction Layer (PAL) → Driver Layer (ASIO/WASAPI/ALSA/CoreAudio · USB/WiFi/BT/NIC · Embedded Peripheral · Virtual Audio Driver) → Kernel Driver → Hardware. Komşu sınır ve geçiş noktaları: [[electronic/firmware/index]] (MCU/SoC kalıcı kod — firmware sürümü uyumluluk matrisine girer) · [[electronic/hardware/index]] (DAC/ADC/amfi/PCB — chip-id ve pin atamaları probing verisidir) · [[electronic/dsp/index]] (DSP zinciri sürücünün buffer'ına yazar) · [[electronic/amplifier/index]] (DC offset koruması sürücü gözetiminde tetiklenir) · [[architecture/07-security/index]] (driver signing, Secure Boot).

- **Sürücü ↔ DSP sınırı:** sürücü ham PCM tamponunu (32-bit float, 48 kHz — ADR-017) DSP'ye devreder; DSP parametrelerine (EQ bantları, compressor eşiği) dokunmaz.
- **DC offset gözetimi:** DC offset >0.5 V ise donanım koruma rölesi açılır (ADR-038 H001); sürücü katmanı bu eşiği çiğneyen akışı mute eder — röle tek savunma hattı değildir.
- **Signing zinciri:** Windows WHQL/EV imzası + Secure Boot · Linux module signing · macOS DriverKit notarization — [[architecture/07-security/driver-signing]].

## 4. OS Ses Stack'leri ve Öncelik

- **Windows:** CoreMusic API → WASAPI → ASIO → Kernel Streaming → WDM → Hardware. Öncelik: 1. ASIO (en düşük gecikme) · 2. WASAPI Exclusive · 3. WASAPI Shared · 4. WDM.
- **Linux:** CoreMusic API → PipeWire → PulseAudio → ALSA → Kernel → Hardware. Öncelik: 1. ALSA (hw) · 2. PipeWire · 3. JACK · 4. PulseAudio.
- **macOS:** CoreMusic API → CoreAudio → IOKit → Hardware. Öncelik: 1. CoreAudio · 2. AudioUnit.

ASIO Exclusive Lock: yalnızca **bir** uygulama cihazı aynı anda ele geçirebilir (kural 9); ikinci istek ret edilir, sessizce paylaşıma düşürülmez. Sanal ses sürücüsü (routing/virtual mic/speaker/streaming/broadcast) fiziksel cihazla aynı hotplug olayları girer — [[audio-drivers]] §5 · [[device-integration]] §2. Cihaz keşif zinciri: tara → sürücü kontrol → uyumluluk doğrula → varsayılan seç → engine başlat; hotplug sürümü aynı doğrulama zincirinden kernel olay bildirimiyle geçer.

## 5. OS Bazında Sürücü Karşılaştırması

### 5.1 Exclusive/Paylaşımlı Erişim Matrisi

| OS | Exclusive mod | Paylaşımlı mod | Kilitleme kuralı |
|----|---------------|----------------|------------------|
| Windows | ASIO Exclusive, WASAPI Exclusive | WASAPI Shared, WDM | Kural 9 — tek uygulama |
| Linux | ALSA doğrudan (hw:), JACK | dmix, PulseAudio, PipeWire | İlk açan (device node) |
| macOS | CoreAudio (aggregat dışı) | CoreAudio aggregate device | Tek render (çoklu aggregate) |

### 5.2 Gecikme Bütçesi (aynı donanım, 512/48kHz — ADR-017)

| OS | En düşük gecikme sürücüsü | Tipik toplam | Sınırlayıcı katman |
|----|---------------------------|--------------|---------------------|
| Windows | ASIO | Buffer + ~1 ms | Kernel Streaming |
| Linux | ALSA (hw) | Buffer + ~1 ms | Kernel ALSA PCM |
| macOS | CoreAudio | Buffer + ~1–2 ms | IOKit/CoreAudio |

Buffer süresi (T) = period_size × n_period / sample_rate → 512 sample × 2 / 48000 = 21.33 ms (çift tampon) · tek tampon 10.67 ms. Buffer süresi her OS'ta baskın maliyettir; OS katmanı yalnız ~1–2 ms ekler. Periyot küçüldükçe gecikme ↓, xrun (underrun) riski ↑ — [[audio-drivers]] §9.2: 128 → 5.33 ms (DJ/monitor) · 256 → 10.67 ms (dengeli) · 512 → 21.33 ms (**ADR-017 varsayılanı**) · 1024 → 42.67 ms (mixing).

Bağlantı × OS: USB DAC (UAC2), WiFi (AirPlay/Roon), Bluetooth (A2DP/LE), Ethernet (Roon) üç OS'ta da ✓ ([[usb-drivers]] · [[wifi-drivers]] · [[bluetooth-drivers]] · [[network-drivers]]); gömülü I2S (RPi5) yalnız Linux'ta ✓ ([[embedded-drivers]]).

## 6. Repo Kod Durumu

CoreMusic deposunda (2026-10-13 ölçümü) `shared/src` ve `home.coremusic.net` altında sürücü/DSP çekirdek kodu içeren `.cpp`/`.c`/`.h` dosyası **YOK**; ilk 40 depo kaynak dosyasının tamamı PHP'dir. Neva Engine `.cpp` yüzeyi PLANNED'dır — bu klasördeki tüm teknik içerik **spesifikasyon** niteliğindedir. **Repo'da sürücü/DSP kodu YOK — kaynak: .ai/electronic/**

## 7. Web Araştırma Kayıtları

| İddia | URL | Tarih | Durum |
|-------|-----|-------|-------|
| Linux sürücü modeli ve driver-API çekirdek dokümantasyonu §3 katman tanımının dayanağı | https://www.kernel.org/doc/html/latest/driver-api/basics.html | 2026-10-13 | HTTP 200 doğrulandı |
| Linux ağ yığını dokümantasyonu network-drivers sınırının resmi referansı | https://www.kernel.org/doc/html/latest/networking/index.html | 2026-10-13 | HTTP 200 doğrulandı |
| Linux ALSA/ses altyapısı dokümantasyonu §4 Linux kolunun resmi kaynağı | https://www.kernel.org/doc/html/latest/sound/index.html | 2026-10-13 | HTTP 200 doğrulandı |

## 8. ADR Referansları ve Kural 12

ADR'ler: [[ADR-017-dsp-hardware-mode]] (XMOS, JUCE, ASIO) · [[ADR-019-per-os-neva-player]] (Per-OS player) · [[ADR-022-database-hardened-security]] (driver güvenliği) · [[ADR-038-8.1-sound-card-chip-selection]] (PCM3168A · ADR-038 H001).

**Kural 12 hatırlatma:** "JUCE 9" / "ASIO SDK 2.3.4" sürüm kanıtı depoda yok → [[audio-drivers]] §8.3'te `⚠️ VERIFICATION REQUIRED` olarak işaretlidir. **Sıkıştırma notu (2026-10-13):** 358 satırlık indeks 40–100 bandına indirildi; silinen sürücü sınıfları sözlüğü ve derinleştirme tablosu bilgi olarak yedi içerik dosyasının §1'lerinde mevcuttur.

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
**Mode:** Red Team · Human Mode · Truth Mode
