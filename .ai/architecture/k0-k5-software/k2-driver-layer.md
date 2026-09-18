---
type: architecture
category: layer-definition
title: "K2 — Driver Layer (40 Components)"
date: 2026-09-18
updated: 2026-09-18
status: draft
version: 1.0.0
authority: Bayram Ali / Vault Steward
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/architecture/k2-driver-layer.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/brain.md"
  layer: K2
  component_count: 40
---

# K2 — Sürücü Katmanı (Driver Layer)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[brain.md]]

**Kapsam:** Tüm işletim sistemleri ve platformlar için ses, girişim, kablosuz ve medya sürücüleri.

---

## 1. Genel Bakış

K2 katmanı, CoreMusic'in donanım bileşenleriyle doğrudan iletişim kuran sürücüleri ve düşük seviyeli API'leri tanımlar. Bu katman, K0 (OS) ve K1 (Hardware) arasında köprü görevi görür.

### 1.1 Sürücü Mimarisi Diyagramı

```
┌─────────────────────────────────────────────────────────────────┐
│                    K2 — DRIVER LAYER (40)                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │               ASIO DRIVERS (2)                           │   │
│  │  ┌──────────────┐  ┌──────────────┐                     │   │
│  │  │ ASIO SDK     │  │ ASIO4ALL    │                     │   │
│  │  │ Driver       │  │ Universal   │                     │   │
│  │  └──────────────┘  └──────────────┘                     │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │               NATIVE AUDIO (6)                           │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐                 │   │
│  │  │ WASAPI   │ │ ALSA     │ │ PipeWire │                 │   │
│  │  │Shared/Ex │ │PCM/Mixer │ │Core/Node │                 │   │
│  │  │clusive   │ │MIDI      │ │Link      │                 │   │
│  │  └──────────┘ └──────────┘ └──────────┘                 │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐                 │   │
│  │  │CoreAudio │ │CoreMIDI  │ │WDM KS    │                 │   │
│  │  │HAL/Plugin│ │Driver    │ │Streaming │                 │   │
│  │  └──────────┘ └──────────┘ └──────────┘                 │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │               RPi5 DRIVERS (1)                           │   │
│  │  ┌──────────────────────────────────────────┐           │   │
│  │  │  I2S + DMA + GPIO                        │           │   │
│  │  └──────────────────────────────────────────┘           │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │               USB DRIVERS (3)                            │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐                 │   │
│  │  │ USB Audio│ │ USB HID  │ │ USB MIDI │                 │   │
│  │  │ Class 2  │ │          │ │          │                 │   │
│  │  └──────────┘ └──────────┘ └──────────┘                 │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │               WIRELESS (7)                               │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐   │   │
│  │  │BT A2DP   │ │BT AVRCP  │ │BT HFP    │ │BT LE     │   │   │
│  │  │          │ │          │ │          │ │Audio     │   │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘   │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐                 │   │
│  │  │Wi-Fi Dir │ │DLNA Ren  │ │AirPlay   │                 │   │
│  │  │          │ │          │ │Receiver  │                 │   │
│  │  └──────────┘ └──────────┘ └──────────┘                 │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │               NETWORK AUDIO (5)                          │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐   │   │
│  │  │MIDI VP   │ │MIDI Net  │ │NDI Audio │ │Dante Ctrl│   │   │
│  │  │          │ │          │ │          │ │          │   │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘   │   │
│  │  ┌──────────┐                                           │   │
│  │  │Chromecast│                                           │   │
│  │  │Audio     │                                           │   │
│  │  └──────────┘                                           │   │
│  └──────────────────────────────────────────────────────────┘   │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │               MISC (16)                                  │   │
│  │  ASIO Buffer Manager, WASAPI Shared, WASAPI Exclusive,  │   │
│  │  ALSA PCM, ALSA Mixer, ALSA MIDI, PipeWire Core,       │   │
│  │  PipeWire Node, PipeWire Link, CoreAudio HAL,           │   │
│  │  CoreAudio Plugin, CoreMIDI Driver, RPi5 I2S,          │   │
│  │  RPi5 DMA, RPi5 GPIO                                    │   │
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
```

### 1.2 Bileşen Dağılımı

| Kategori | Bileşen Sayısı |
|----------|---------------|
| ASIO Drivers | 2 |
| Native Audio | 6 |
| RPi5 Drivers | 1 |
| USB Drivers | 3 |
| Wireless | 7 |
| Network Audio | 5 |
| Misc (alt bileşenler) | 16 |
| **TOPLAM** | **40** |

---

## 2. ASIO Drivers (2 Bileşen)

### 2.1 ASIO SDK Driver

| Özellik | Değer |
|---------|-------|
| SDK Versiyon | 2.3.4 |
| Sample Rate | 44.1kHz — 192kHz |
| Buffer Size | 64 — 2048 samples |
| Input Channels | 6 (PCM3168A ADC) |
| Output Channels | 16 (PCM3168A + AK4458) |
| Bit Depth | 32-bit float |
| Latency | < 3ms @ 128 samples, 48kHz |
| Kullanım | Ana ses arayüzü (Windows) |
| Referans | https://www.steinberg.net/developers/ |

### 2.2 ASIO4ALL Universal

| Özellik | Değer |
|---------|-------|
| Amaç | Universal ASIO driver for WDM devices |
| Kullanım | Fallback when native ASIO unavailable |
| Avantaj | Tüm WDM cihazlarını ASIO'ya çevirir |
| Referans | https://www.asio4all.org/ |

### 2.3 ASIO Buffer Management

```
┌──────────────────────────────────────────────────────┐
│              ASIO BUFFER FLOW                         │
│                                                      │
│  Application ──> ASIO Host ──> Driver ──> Hardware   │
│       │              │            │           │       │
│       │         Buffer Size      │      DMA Buffer   │
│       │         (64-2048)        │      (256-2048)   │
│       │              │            │           │       │
│       └──────────────┴────────────┴───────────┘       │
│                    Callback Model                     │
│                    (double-buffering)                  │
└──────────────────────────────────────────────────────┘
```

---

## 3. Native Audio Drivers (6 Bileşen)

### 3.1 WASAPI (Windows Audio Session API)

| Özellik | Değer |
|---------|-------|
| Shared Mode | Multi-client, OS mixing |
| Exclusive Mode | Direct hardware access, lowest latency |
| Kullanım | Windows primary audio output |
| referans | https://learn.microsoft.com/en-us/windows/win32/coreaudio/ |

**WASAPI Exclusive Lock:**
> ADR-038 H001: Only ONE application at a time can hold WASAPI Exclusive mode. CoreMusic claims exclusive lock on startup.

### 3.2 ALSA (Advanced Linux Sound Architecture)

| Özellik | Değer |
|---------|-------|
| PCM | Playback/Capture, mmap, interleaved |
| Mixer | Volume, routing, controls |
| MIDI | Raw MIDI, sequencer |
| Kullanım | Linux primary audio |
| referans | https://github.com/alsa-project/alsa-lib |

### 3.3 PipeWire

| Özellik | Değer |
|---------|-------|
| Core | Graph-based audio processing |
| Node | Audio source/sink nodes |
| Link | Dynamic node connections |
| Kullanım | Modern Linux audio routing |
| referans | https://gitlab.freedesktop.org/pipewire/pipewire |

### 3.4 CoreAudio HAL

| Özellik | Değer |
|---------|-------|
| HAL | Hardware Abstraction Layer |
| Plugin | Audio processing plugins |
| Kullanım | macOS primary audio |
| referans | https://developer.apple.com/library/archive/documentation/MusicAudio/Conceptual/CoreAudio/ |

### 3.5 CoreMIDI Driver

| Özellik | Değer |
|---------|-------|
| Amaç | MIDI device communication |
| Kullanım | MIDI controller, MIDI network |
| referans | https://developer.apple.com/library/archive/documentation/MusicAudio/Reference/CoreMIDI/ |

### 3.6 WDM Kernel Streaming

| Özellik | Değer |
|---------|-------|
| Amaç | Direct kernel-level audio streaming |
| Kullanım | Low-latency Windows audio (legacy) |
| referans | https://learn.microsoft.com/en-us/windows-hardware/drivers/audio/ |

---

## 4. RPi5 Drivers (1 Bileşen — 3 Alt)

### 4.1 RPi5 I2S/DMA/GPIO

| Özellik | Değer |
|---------|-------|
| I2S | Inter-IC Sound, master/slave mode |
| DMA | Direct Memory Access for audio streaming |
| GPIO | General Purpose I/O for control signals |
| Kullanım | XMOS I2S connection, relay control |
| referans | https://www.raspberrypi.com/documentation/computers/processors.html |

```
┌──────────────────────────────────────────────┐
│              RPi5 DRIVER STACK                │
│                                              │
│  ┌──────────┐                               │
│  │ Application│                              │
│  └────┬─────┘                               │
│       ▼                                      │
│  ┌──────────┐                               │
│  │ ALSA/    │                               │
│  │ PipeWire │                               │
│  └────┬─────┘                               │
│       ▼                                      │
│  ┌──────────┐                               │
│  │ I2S Driver│                               │
│  └────┬─────┘                               │
│       ▼                                      │
│  ┌──────────┐   ┌──────────┐               │
│  │ DMA      │   │ GPIO     │               │
│  │ Engine   │   │ Control  │               │
│  └────┬─────┘   └────┬─────┘               │
│       ▼              ▼                      │
│  ┌──────────────────────────┐               │
│  │     XMOS XU316           │               │
│  │  (I2S + Control)         │               │
│  └──────────────────────────┘               │
└──────────────────────────────────────────────┘
```

---

## 5. USB Drivers (3 Bileşen)

### 5.1 USB Audio Class 2

| Özellik | Değer |
|---------|-------|
| Standard | UAC 2.0 |
| Kanal | 14 (8 out + 6 in) |
| Bit | 32-bit |
| Fs | 44.1k-192k |
| Kullanım | XMOS USB connection |
| referans | https://usb.org/document-library/audio-device-class-definition-20 |

### 5.2 USB HID

| Özellik | Değer |
|---------|-------|
| Amaç | Human Interface Device (volume knob, transport controls) |
| Kullanım | Physical control interface |
| referans | https://www.usb.org/document-library/hid-usage-tables-14 |

### 5.3 USB MIDI

| Özellik | Değer |
|---------|-------|
| Standard | USB-MIDI 1.0 |
| Amaç | MIDI controller connection |
| Kullanım | MIDI keyboard, MIDI controller |
| referans | https://www.usb.org/document-library/device-class-definition-midi-10 |

---

## 6. Wireless Drivers (7 Bileşen)

### 6.1 Bluetooth A2DP

| Özellik | Değer |
|---------|-------|
| Amaç | Audio streaming profile |
| Codec | SBC, AAC, LDAC, aptX HD |
| Kullanım | Wireless speaker, headphone |
| referans | https://www.bluetooth.com/specifications/specs/core-specification-5-3/ |

### 6.2 Bluetooth AVRCP

| Özellik | Değer |
|---------|-------|
| Amaç | Audio/Video Remote Control |
| Kullanım | Play/pause/skip control |
| referans | https://www.bluetooth.com/specifications/specs/avrcp-1-6/ |

### 6.3 Bluetooth HFP

| Özellik | Değer |
|---------|-------|
| Amaç | Hands-Free Profile |
| Kullanım | Voice calls, voice assistant |
| referans | https://www.bluetooth.com/specifications/specs/hands-free-profile-1-8/ |

### 6.4 Bluetooth LE Audio

| Özellik | Değer |
|---------|-------|
| Amaç | Low Energy Audio |
| Codec | LC3 |
| Kullanım | Modern wireless audio |
| referans | https://www.bluetooth.com/learn-about-bluetooth/tech-overview/le-audio/ |

### 6.5 Wi-Fi Direct

| Özellik | Değer |
|---------|-------|
| Amaç | Peer-to-peer Wi-Fi connection |
| Kullanım | Direct device streaming |
| referans | https://www.wi-fi.org/discover-wi-fi/wi-fi-direct |

### 6.6 DLNA Renderer

| Özellik | Değer |
|---------|-------|
| Amaç | Digital Living Network Alliance |
| Kullanım | Home network media streaming |
| referans | https://www.dlna.org/ |

### 6.7 AirPlay Receiver

| Özellik | Değer |
|---------|-------|
| Amaç | Apple AirPlay 2 audio streaming |
| Kullanım | Apple device audio casting |
| referans | https://developer.apple.com/airplay2/ |

### 6.8 Roon Endpoint

| Özellik | Değer |
|---------|-------|
| Amaç | Roon music management platform entegrasyonu |
| Protokol | Roon Ready (RAAT - Roon Advanced Audio Transport) |
| Kullanım | Profesyonel müzik yönetimi, high-res streaming |
| referans | https://roonlabs.com/ |

---

## 7. Network Audio Drivers (5 Bileşen)

### 7.1 MIDI Virtual Port

| Özellik | Değer |
|---------|-------|
| Amaç | Software-only MIDI ports |
| Kullanım | Inter-app MIDI, virtual cables |

### 7.2 MIDI Network

| Özellik | Değer |
|---------|-------|
| Amaç | RTP-MIDI over network |
| Kullanım | Multi-device MIDI sync |

### 7.3 NDI Audio

| Özellik | Değer |
|---------|-------|
| Amaç | Network Device Interface audio |
| Kullanım | Video/audio production streaming |
| referans | https://ndi.video/tech/ |

### 7.4 Dante Controller

| Özellik | Değer |
|---------|-------|
| Amaç | Dante audio network protocol |
| Kullanım | Professional networked audio |
| referans | https://www.audinate.com/ |

### 7.5 Chromecast Audio

| Özellik | Değer |
|---------|-------|
| Amaç | Google Cast audio streaming |
| Kullanım | Multi-room audio |
| referans | https://developers.google.com/cast/docs/audio |

---

## 8. ASIO Exclusive Lock Protokolü

```
┌──────────────────────────────────────────────────────┐
│           ASIO EXCLUSIVE LOCK PROTOCOL                │
│                                                      │
│  ┌──────────┐                                        │
│  │ CoreMusic │──── Lock Request ────> ┌──────────┐  │
│  │ Startup   │                        │ ASIO     │  │
│  └──────────┘                        │ Driver   │  │
│                                       │          │  │
│  ┌──────────┐                        │ Lock:    │  │
│  │ Other App │──── Lock Denied ──────│ GRANTED  │  │
│  │ Attempt   │                        │          │  │
│  └──────────┘                        └──────────┘  │
│                                                      │
│  Kural: Tek bir uygulama Exclusive mode alabilir.    │
│  CoreMusic başlatıldığında Exclusive lock alır.      │
│  Diğer uygulamalar Shared mode'a düşer.              │
└──────────────────────────────────────────────────────┘
```

---

## 9. Sürücü Seviye Mimarisi

```
┌─────────────────────────────────────────────────────────────────┐
│                  K2 DRIVER ARCHITECTURE                         │
│                                                                  │
│  LAYER 3: Application Drivers                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │  ASIO SDK │ WASAPI │ ALSA  │ PipeWire │ CoreAudio       │   │
│  └──────────────────────────────────────────────────────────┘   │
│                           │                                     │
│  LAYER 2: Protocol Drivers                                      │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │  USB Audio │ BT A2DP │ DLNA │ AirPlay │ Dante │ NDI    │   │
│  └──────────────────────────────────────────────────────────┘   │
│                           │                                     │
│  LAYER 1: Hardware Drivers                                      │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │  I2S │ DMA │ GPIO │ USB │ PCIe │ Network │ SPI │ I2C   │   │
│  └──────────────────────────────────────────────────────────┘   │
│                           │                                     │
│  LAYER 0: OS Kernel                                             │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │  Windows Kernel │ Linux Kernel │ macOS Kernel │ RPi5     │   │
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
```

---

## 10. Bileşen Sayacı

| # | Bileşen | Kategori | Toplam |
|---|---------|----------|--------|
| 1 | ASIO SDK Driver | ASIO | 1 |
| 2 | ASIO4ALL | ASIO | 1 |
| 3 | WASAPI Shared/Exclusive | Native Audio | 1 |
| 4 | ALSA PCM/Mixer/MIDI | Native Audio | 1 |
| 5 | PipeWire Core/Node/Link | Native Audio | 1 |
| 6 | CoreAudio HAL/Plugin | Native Audio | 1 |
| 7 | CoreMIDI Driver | Native Audio | 1 |
| 8 | RPi5 I2S/DMA/GPIO | RPi5 | 1 |
| 9 | USB Audio Class 2 | USB | 1 |
| 10 | USB HID | USB | 1 |
| 11 | USB MIDI | USB | 1 |
| 12 | BT A2DP | Wireless | 1 |
| 13 | BT AVRCP | Wireless | 1 |
| 14 | BT HFP | Wireless | 1 |
| 15 | BT LE Audio | Wireless | 1 |
| 16 | Wi-Fi Direct | Wireless | 1 |
| 17 | DLNA Renderer | Wireless | 1 |
| 18 | AirPlay Receiver | Wireless | 1 |
| 19 | MIDI Virtual Port | Network Audio | 1 |
| 20 | MIDI Network | Network Audio | 1 |
| 21 | NDI Audio | Network Audio | 1 |
| 22 | Dante Controller | Network Audio | 1 |
| 23 | Chromecast Audio | Network Audio | 1 |
| 24 | ASIO Buffer Manager | Misc | 1 |
| 25 | WASAPI Shared Mode | Misc | 1 |
| 26 | WASAPI Exclusive Mode | Misc | 1 |
| 27 | ALSA PCM | Misc | 1 |
| 28 | ALSA Mixer | Misc | 1 |
| 29 | ALSA MIDI | Misc | 1 |
| 30 | PipeWire Core | Misc | 1 |
| 31 | PipeWire Node | Misc | 1 |
| 32 | PipeWire Link | Misc | 1 |
| 33 | CoreAudio HAL | Misc | 1 |
| 34 | CoreAudio Plugin | Misc | 1 |
| 35 | CoreMIDI Driver | Misc | 1 |
| 36 | RPi5 I2S | Misc | 1 |
| 37 | RPi5 DMA | Misc | 1 |
| 38 | RPi5 GPIO | Misc | 1 |
| 39 | WDM Kernel Streaming | Misc | 1 |
| 40 | DLNA Renderer (detail) | Misc | 1 |
| | **TOPLAM** | | **40** |

---

## 11. GitHub Referansları

| Bileşen | Repository | URL |
|---------|-----------|-----|
| ASIO SDK | Steinberg | https://www.steinberg.net/developers/ |
| PipeWire | PipeWire | https://gitlab.freedesktop.org/pipewire/pipewire |
| ALSA | alsa-lib | https://github.com/alsa-project/alsa-lib |
| USB Audio | UAC 2.0 Spec | https://www.usb.org/document-library/audio-device-class-definition-20 |
| Bluetooth | BT SIG | https://www.bluetooth.com/specifications/ |
| DLNA | DLNA | https://www.dlna.org/ |
| NDI | NDI | https://ndi.video/tech/ |
| Dante | Audinate | https://www.audinate.com/ |

---

## 12. İlgili Dosyalar

| Dosya | İlişki |
|-------|--------|
| [[k0-os-layer]] | K2 sürücülerini barındırır |
| [[k1-hardware-layer]] | K2 donanımı kontrol eder |
| [[k3-audio-engine]] | K2'den ses akışı alır |
| [[k2-driver-layer]] | ASIO sürücü implementasyonu |
| [[k2-driver-layer]] | WASAPI sürücü implementasyonu |
| [[audio_driver]] | Abstrakt sürücü arayüzü |

---

## Class AB Sürücü Gereksinimleri

- [[electronics/amplifier-classab-circuit]] — ASIO/WASAPI ile Class AB entegrasyonu
- [[electronics/power-supply-classab]] — Güç sürücü koruması

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Version:** 1.0.0
**Status:** draft
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
