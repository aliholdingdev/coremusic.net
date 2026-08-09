---
type: system
category: driver-framework
title: "CoreMusic Electronics — Driver Framework Index"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic Electronics — Driver Framework

**Zorunlu Bağlantılar:** [[electronic/index]] · [[brain.md]] · [[architecture/07-security/index]]

---

## 1. Amaç

Driver Framework, CoreMusic ELECTRONICS platformunun tüm işletim sistemlerinde ses donanımıyla iletişim kuran sürücü altyapısını yönetir.

---

## 2. Driver Katmanları

```
Application
    ↓
CoreMusic API
    ↓
Platform Abstraction Layer (PAL)
    ↓
Driver Layer
    ├── ASIO Driver
    ├── WASAPI Driver
    ├── WDM Driver
    ├── ALSA Driver
    ├── CoreAudio Driver
    ├── Virtual Audio Driver
    └── Hardware Driver
    ↓
Kernel Driver
    ↓
Hardware
```

---

## 3. Driver Listesi

| Driver | Dosya | OS | Kullanım |
|--------|-------|-----|----------|
| ASIO | [[asio-driver]] | Windows | Profesyonel ses |
| WASAPI/WDM | [[wasapi-wdm]] | Windows | Genel ses |
| ALSA | [[alsa-coreaudio]] | Linux | Linux ses |
| CoreAudio | [[alsa-coreaudio]] | macOS | macOS ses |
| Virtual | [[virtual-audio]] | Tümü | Sanal ses |
| Hardware | [[hardware-driver]] | Tümü | Fiziksel donanım |

---

## 4. OS Ses Stack'leri

### Windows Audio Stack
```
Application → CoreMusic API → WASAPI → ASIO → Kernel Streaming → WDM → Hardware
```

### Linux Audio Stack
```
Application → CoreMusic API → PipeWire → PulseAudio → ALSA → Kernel → Hardware
```

### macOS Audio Stack
```
Application → CoreMusic API → CoreAudio → IOKit → Hardware
```

Detay: [[driver-stack-diagrams]]

---

## 5. Driver Öncelik Sırası

### Windows
1. ASIO (en düşük gecikme)
2. WASAPI Exclusive
3. WASAPI Shared
4. WDM

### Linux
1. ALSA (en düşük gecikme)
2. PipeWire
3. JACK
4. PulseAudio

### macOS
1. CoreAudio
2. AudioUnit

---

## 6. Virtual Audio Driver

Sanal ses sürücüsü gerçek donanım olmadan ses cihazı oluşturur.

Kullanım alanları:
- Audio Routing
- Virtual Microphone
- Virtual Speaker
- Streaming
- Screen Recording
- Broadcast

Detay: [[virtual-audio]]

---

## 7. Hot Plug Desteği

Sistem cihazların çalışma sırasında bağlanmasını/çıkarılmasını destekler:

- USB ses kartı takılması
- Bluetooth kulaklık bağlanması
- HDMI monitör değiştirilmesi
- Harici DAC eklenmesi

Audio Engine yeniden başlatılmadan cihaz değişimi mümkün olmalıdır.

---

## 8. Driver Güvenliği

| Kural | Açıklama | ADR |
|-------|----------|-----|
| Dijital İmza | Tüm driver'lar imzalı olmalı | [[ADR-022-database-hardened-security]] |
| Secure Boot | Firmware imzalı açılmalı | [[ADR-022-database-hardened-security]] |
| Bellek Koruma | Taşma/taşırma engeli | — |
| Yetki Doğrulama | Yetkisiz erişim engeli | — |

Detay: [[architecture/07-security/driver-signing]]

---

## 9. Device Discovery Akışı

```
Sistem Başlat
    ↓
Donanımı Tara
    ↓
Ses Cihazlarını Tara
    ↓
Sürücüleri Kontrol Et
    ↓
Uyumluluğu Doğrula
    ↓
Varsayılan Cihazı Seç
    ↓
Audio Engine'i Başlat
```

---

## 10. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-017-dsp-hardware-mode]] | XMOS, JUCE, ASIO |
| [[ADR-019-per-os-neva-player]] | Per-OS player |
| [[ADR-022-database-hardened-security]] | Driver güvenliği |

---

## 11. Çapraz Referanslar

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| Drivers | [[electronic/dsp/index]] | DSP-engine bağlantısı |
| Drivers | [[electronic/firmware/index]] | Firmware güncellemesi |
| Drivers | [[electronic/hardware/index]] | Donanım erişimi |
| Drivers | [[architecture/07-security/index]] | Driver signing |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
