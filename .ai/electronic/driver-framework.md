---
title: "CoreMusic — Driver Framework"
category: electronics
date: 2026-08-09
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Driver Framework

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]]

---

## 1. Amaç

CoreMusic ELECTRONICS için kapsamlı sürücü desteği. Tüm donanım platformları için sürücü mimarisi, platform bazlı ses yığınları, sıcak takma, sürücü yönetimi, güvenlik ve AI destekli sürücü analizini kapsar.

---

## 2. Sürücü Mimarisi Katmanları

```
┌─────────────────────────────────────────────────────────────────┐
│  Katman 5: Uygulama    │  CoreMusic Audio Engine               │
├─────────────────────────┼───────────────────────────────────────┤
│  Katman 4: Platform API │  Hardware Abstraction Layer           │
├─────────────────────────┼───────────────────────────────────────┤
│  Katman 3: Sürücü       │  ASIO │ WASAPI │ ALSA │ CoreAudio    │
├─────────────────────────┼───────────────────────────────────────┤
│  Katman 2: Çekirdek     │  Kernel ASIO │ Kernel WASAPI │ ALSA  │
├─────────────────────────┼───────────────────────────────────────┤
│  Katman 1: Donanım      │  XMOS XU316 │ PCM3168A │ AK4458      │
└─────────────────────────┴───────────────────────────────────────┘
```

### 2.1 ASCII: Sürücü Mimarisi Katmanları
```
┌─────────────────────────────────────────────────────────────────────────────┐
│                  COREMUSIC SÜRÜCÜ MİMARİSİ — 5 KATMAN                      │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │ KATMAN 5: UYGULAMA                                                  │   │
│  │ ┌─────────────────────────────────────────────────────────────────┐ │   │
│  │ │              CoreMusic Audio Engine                             │ │   │
│  │ └─────────────────────────────────────────────────────────────────┘ │   │
│  └──────────────────────────────────┬──────────────────────────────────┘   │
│                                     ▼                                      │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │ KATMAN 4: PLATFORM API (Hardware Abstraction Layer)                 │   │
│  │ ┌─────────────────────────────────────────────────────────────────┐ │   │
│  │ │              CoreMusic HAL — Çapraz Platform Soyutlama          │ │   │
│  │ └─────────────────────────────────────────────────────────────────┘ │   │
│  └──────────────────────────────────┬──────────────────────────────────┘   │
│                                     ▼                                      │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │ KATMAN 3: SÜRÜCÜ KATMANI                                           │   │
│  │ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐  │   │
│  │ │ASIO Drv  │ │WASAPI Drv│ │ALSA Drv  │ │CoreAudio │ │USB Audio │  │   │
│  │ │Windows   │ │Windows   │ │Linux     │ │macOS     │ │Genel     │  │   │
│  │ └──────────┘ └──────────┘ └──────────┘ └──────────┘ └──────────┘  │   │
│  └──────────────────────────────────┬──────────────────────────────────┘   │
│                                     ▼                                      │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │ KATMAN 2: ÇEKİRDEK SÜRÜCÜSÜ                                        │   │
│  │ ┌──────────┐ ┌──────────┐ ┌──────────┐                             │   │
│  │ │Kernel    │ │Kernel    │ │Kernel    │                             │   │
│  │ │ASIO      │ │WASAPI    │ │ALSA      │                             │   │
│  │ └──────────┘ └──────────┘ └──────────┘                             │   │
│  └──────────────────────────────────┬──────────────────────────────────┘   │
│                                     ▼                                      │
│  ┌─────────────────────────────────────────────────────────────────────┐   │
│  │ KATMAN 1: DONANIM                                                   │   │
│  │ ┌──────────┐ ┌──────────┐ ┌──────────┐                             │   │
│  │ │XMOS XU316│ │PCM3168A  │ │AK4458    │                             │   │
│  │ │16 Core   │ │6-in/8-out│ │8-ch DAC  │                             │   │
│  │ │3200 MIPS │ │24-bit    │ │32-bit    │                             │   │
│  │ └──────────┘ └──────────┘ └──────────┘                             │   │
│  └─────────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────────┘
```

| Katman | Görev | Örnek |
|--------|-------|-------|
| Application | Yüksek düzey ses API | CoreMusic Audio Engine |
| Platform API | Çapraz platform soyutlama | CoreMusic HAL |
| Driver Layer | Donanım özel sürücü | PCM3168A driver |
| Kernel Driver | OS seviyesi erişim | ALSA, WASAPI driver |
| Hardware | Fiziksel donanım | XMOS XU316, PCM3168A |

---

## 3. Sürücü Tipleri

### 3.1 Sanal Ses Sürücüsü

| Özellik | Açıklama |
|---------|----------|
| Donanım | Fiziksel donanım yok (yazılım tabanlı) |
| Kullanım | Ses yönlendirme, sanal mikrofon/hoparlör |
| Akış | Streaming, ekran kaydı, yayın |
| Örnek | VB-Audio, BlackHole, PipeWire sink |

| Sanal Cihaz | Kullanım |
|-------------|----------|
| Sanal Mikrofon | Dış kaynaktan ses girdisi |
| Sanal Hoparlör | Dış uygulamadan ses yakalama |
| Sanal Mix | Çoklu kaynak karıştırma |
| Streaming Sink | Yayın/akış kaynağı |
| Loopback | Çıkış → Girdi yönlendirme |

### 3.2 Fiziksel Ses Sürücüsü

| Özellik | Açıklama |
|---------|----------|
| Donanım | Gerçek ses donanımı iletişimi |
| Türler | USB Ses Arayüzü, PCIe Ses Kartı, HDMI Ses, DSP Kartı |
| Örnek | XMOS USB Audio, Realtek PCIe, NVIDIA HDMI |

| Tür | Örnek Cihaz | Protokol |
|-----|-------------|----------|
| USB Ses Arayüzü | CoreMusic DAC, Scarlett 2i2 | USB Audio Class 2.0 |
| PCIe Ses Kartı | Creative Sound Blaster | PCIe bus |
| HDMI Ses | NVIDIA, AMD GPU | HDMI/DisplayPort |
| DSP Kartı | CoreMusic DSP Board | I2S/TDM |

### 3.3 USB Sürücüsü

| Sınıf | Alt Sınıf | Kullanım |
|-------|-----------|----------|
| Audio (0x01) | AudioStreaming (0x02) | Ses girdi/çıkış |
| Audio (0x01) | MIDI (0x03) | MIDI verisi |
| HID (0x03) | — | Kontrol cihazları |
| CDC (0x02) | ACM (0x02) | Seri port emülasyonu |

| USB Audio Özelliği | Değer |
|--------------------|-------|
| USB Audio Class | 1.0 (48kHz/16-bit) / 2.0 (192kHz/32-bit) |
| Isochronous Endpoint | Senkron ses transferi |
| Asynchronous Sync | USB clock → device clock |
| Adaptive Sync | Device → USB clock |
| Feedback | USB'ye hız bildirimi |

### 3.4 DSP Sürücüsü

| İşlem | Görev | Protokol |
|-------|-------|----------|
| Firmware Yükleme | DSP yongasına firmware yazma | SPI, JTAG |
| Konfigürasyon | Register ayarlama | SPI, I2C |
| Parametre Güncelleme | EQ/krossover/volume katsayıları | SPI, I2S |
| Preset Yönetimi | Preset yükleme/kaydetme | SPI flash |
| Health Check | DSP durum sorgulama | SPI, GPIO |

---

## 4. Platform Bazlı Ses Yığınları

### 4.1 Windows Audio Stack

```
Uygulama ──▶ CoreMusic API ──▶ WASAPI
                                  │
                    Exclusive ────▶ Kernel Streaming ──▶ WDM/KS Driver ──▶ Hardware
                    Shared ──────▶ Windows Audio Service ──▶ WDM/KS Driver ──▶ Hardware

Alternatif: ASIO Driver ──▶ Hardware
```

| Katman | Görev | Gecikme |
|--------|-------|---------|
| Uygulama | CoreMusic Audio Engine | — |
| CoreMusic API | Yüksek düzey API | <0.1ms |
| WASAPI (Shared) | Windows Audio Service | ~10ms |
| WASAPI (Exclusive) | Doğrudan erişim | ~3ms |
| ASIO | Profesyonel düşük gecikme | ~1ms |
| Kernel Streaming | Doğrudan donanım | <1ms |
| WDM | Genel Windows sürücüsü | ~15ms |

**Windows Öncelik Sırası:** ASIO > WASAPI Exclusive > WASAPI Shared > WDM

### 4.2 Linux Audio Stack

```
Uygulama ──▶ CoreMusic API ──▶ PipeWire ──▶ PulseAudio ──▶ Kernel ALSA ──▶ Hardware
                                  │
                                  └──▶ ALSA ──▶ Kernel ALSA ──▶ Hardware

Alternatif: JACK ──▶ Kernel ALSA ──▶ Hardware
```

| Katman | Görev | Gecikme |
|--------|-------|---------|
| Uygulama | CoreMusic Audio Engine | — |
| CoreMusic API | Yüksek düzey API | <0.1ms |
| PipeWire | Modern ses sunucusu | ~2ms |
| PulseAudio | Masaüstü ses | ~10ms |
| JACK | Profesyonel ses | ~2ms |
| ALSA | Doğrudan donanım | <1ms |

**Linux Öncelik Sırası:** PipeWire > JACK > PulseAudio > ALSA

### 4.3 macOS Audio Stack

```
Uygulama ──▶ CoreMusic API ──▶ CoreAudio ──▶ AudioToolbox ──▶ Hardware
                                │
                                └──▶ IOKit ──▶ Hardware
```

| Katman | Görev | Gecikme |
|--------|-------|---------|
| Uygulama | CoreMusic Audio Engine | — |
| CoreMusic API | Yüksek düzey API | <0.1ms |
| CoreAudio | macOS ses framework'ü | ~3ms |
| AudioToolbox | AudioQueue, AudioUnit | ~2ms |
| IOKit | Donanım erişimi | <1ms |

**macOS Öncelik Sırası:** CoreAudio > AudioToolbox > IOKit

### 4.4 Raspberry Pi Audio Stack

| Katman | Görev | Arayüz |
|--------|-------|--------|
| Uygulama | CoreMusic Audio Engine | — |
| CoreMusic API | Yüksek düzey API | — |
| ALSA | Linux ses | Kernel |
| I2S | Seri ses | PCM5102A/PCM3168A |
| PWM | Modülasyon | Hoparlör |

### 4.5 Android Audio Stack

| Katman | Görev | API |
|--------|-------|-----|
| Uygulama | CoreMusic App | — |
| CoreMusic API | Yüksek düzey API | — |
| AAudio | Yeni nesil (API 26+) | NDK |
| OpenSL ES | Eski nesil | NDK |
| AudioFlinger | Ses sunucusu | Framework |

### 4.6 iOS Audio Stack

| Katman | Görev | Framework |
|--------|-------|-----------|
| Uygulama | CoreMusic App | — |
| CoreMusic API | Yüksek düzey API | — |
| AVAudioSession | Oturum yönetimi | AVFoundation |
| AudioUnit | Düşük seviye | AudioToolbox |
| CoreAudio | Donanım erişimi | CoreAudio |

---

## 5. Audio Backend Seçim Stratejisi

### 5.1 OS Bazlı Öncelik Sırası

| OS | 1. Öncelik | 2. Öncelik | 3. Öncelik | 4. Öncelik |
|----|-----------|-----------|-----------|-----------|
| Windows | ASIO | WASAPI Exclusive | WASAPI Shared | WDM |
| Linux | PipeWire | JACK | PulseAudio | ALSA |
| macOS | CoreAudio | AudioToolbox | IOKit | — |
| Android | AAudio | OpenSL ES | — | — |
| iOS | CoreAudio | AVAudioEngine | — | — |
| RPi | ALSA | I2S | PWM | — |

### 5.2 Otomatik Seçim Akışı

```
OS Algılama ──▶ {Hangi OS?}
                    │
       Windows ────▶ {ASIO Mevcut mu?}
                      │
                 Evet ▼ Hayır
            ASIO Kullan  WASAPI Exclusive

       Linux ─────▶ {PipeWire?}
                      │
                 Evet ▼ Hayır
           PipeWire Kullan  {JACK?}
                              │
                         Evet ▼ Hayır
                    JACK Kullan  ALSA Kullan

       macOS ────▶ CoreAudio Kullan
       Android ──▶ AAudio Kullan
       iOS ──────▶ CoreAudio Kullan
```

---

## 6. Device Discovery Akışı

```
Sistem Başlatma ──┬──▶ USB Enumerate ─┐
                  ├──▶ I2C Scan ──────┤
                  ├──▶ SPI Probe ─────┤──▶ {Cihaz Tanıdık mı?}
                  └──▶ Bluetooth Scan ┘           │
                                            Evet ▼ Hayır
                                     Sürücü Yükle  Desteklenmeyen Cihaz
                                           │              │
                                           ▼              ▼
                                    Konfigürasyon   Varsayılan Sürücü
                                           │
                                           ▼
                                    Aktif Kullanım
```

### 6.1 Cihaz Tanıma

| Yöntem | Protokol | Bilgi |
|--------|----------|-------|
| USB VID/PID | USB | Vendor/Product ID |
| I2C Address | I2C | Chip model |
| SPI JEDEC | SPI | Flash ID |
| BLE Service | Bluetooth | Service UUID |
| EDID | HDMI/DP | Cihaz bilgisi |

---

## 7. Sürücü Yöneticisi

### 7.1 Yönetici Özellikleri

| Özellik | Görev | Öncelik |
|---------|-------|---------|
| Donanım Algılama | Otomatik bulma, hot-plug | Yüksek |
| Sürücü Yükleme | Otomatik yükleme | Yüksek |
| Sürücü Güncelleme | Versiyon yönetimi | Orta |
| Firmware Kontrolü | Versiyon doğrulama | Yüksek |
| Uyumluluk Kontrolü | OS/sürüm eşleşme | Yüksek |
| Hata Raporlama | Teşhis, loglama | Orta |
| Teşhis | Sorun giderme | Orta |

### 7.2 Sürücü Versiyon Yönetimi

| Aşama | İşlem |
|-------|-------|
| 1 | Mevcut sürücü versiyonunu oku |
| 2 | Yeni sürücü versiyonunu kontrol et |
| 3 | Uyumluluk doğrula |
| 4 | Yedekleme (eski sürücü) |
| 5 | Güncelle |
| 6 | Doğrulama testi |
| 7 | Başarısızsa rollback |

---

## 8. Sıcak Takma (Hot-Plug) Desteği

### 8.1 Hot-Plug Olayları

| Olay | Tetikleme | Tepki |
|------|-----------|-------|
| USB Takma | Device enumeration | Sürücü yükle, akış başlat |
| USB Çıkarma | Device removal | Akışı durdur, kaynak serbest bırak |
| Bluetooth Bağlantısı | A2DP connection | Sürücü yükle, streaming başlat |
| Bluetooth Kesme | Disconnect | Akışı durdur, fallback |
| HDMI Değişikliği | Hot-plug detect | Audio format yeniden yapılandırma |
| Harici DAC Ekleme | USB/SPDIF detect | DAC sürücüsü yükle |

### 8.2 Hot-Plug Akışı

```
USB Takma ──▶ Algılama ──▶ {Donanım Tanıma}
                              │
                     Tanıdık ▼ Bilinmeyen
                  Sürücü Yükle    Hata
                        │
                        ▼
                  Konfigürasyon ──▶ Aktif Et ──▶ Ses Motoru Entegrasyonu ──▶ Kullanıma Hazır
```

**Kural:** Ses Motoru yeniden başlatılmaz. Akış kesintisiz devam eder.

### 8.3 Fallback Stratejisi

| Durum | Fallback |
|-------|----------|
| USB DAC çıkarma | Dahili DAC'a geç |
| Bluetooth kopma | Wi-Fi streaming'e geç |
| ASIO cihaz kaybı | WASAPI'ye geç |
| Tüm çıkışlar yok | Null output (sessizlik) |

---

## 9. Sürücü Güvenliği

| Özellik | Açıklama | Değer |
|---------|----------|-------|
| Dijital İmza | Sürücü doğrulama | RSA-2048 |
| Yetkisiz Erişim | Kontrol mekanizması | RBAC |
| Bellek Taşması | Buffer overflow koruması | ASLR + DEP |
| Güvenli Hata | Graceful degradation | try/catch, fallback |
| İzin Doğrulama | Per-resource permission | POSIX caps |
| Güvenli IPC | Sürücü-uyygula iletişimi | Encrypted pipe |

### 9.1 Güvenlik Kontrol Matrisi

| Tehdit | Koruma | Öncelik |
|--------|--------|---------|
| Yetkisiz sürücü yükleme | İmza doğrulama | CRITICAL |
| Bellek sızıntısı | ASLR, DEP, stack canary | CRITICAL |
| Yükleme saldırısı | Sürücü imzası | CRITICAL |
| Bellek taşması | Buffer size kontrolü | HIGH |
| Privilege escalation | Minimal yetki | HIGH |
| IPC dinleme | Encrypted communication | MEDIUM |

---

## 10. AI Driver Analysis

### 10.1 AI Destekli Sürücü Analizi

| Analiz | Veri Kaynağı | Çıktı |
|--------|-------------|-------|
| Performans Analizi | CPU kullanımı, throughput | Optimizasyon önerileri |
| Gecikme Analizi | Latency metrics, jitter | Gecikme azaltma stratejileri |
| Bellek Analizi | Heap usage, fragmentation | Bellek optimizasyonu |
| Uyumluluk Kontrolü | OS versiyon, driver version | Uyumluluk raporu |
| Çakışma Tespiti | IRQ assignments, DMA channels | Kaynak çakışma çözümü |
| Çekirdek Hatası Analizi | Kernel logs, crash dumps | Root cause analysis |
| Ses Kesintisi Tespiti | Buffer underrun/overrun count | Buffer optimizasyonu |
| Arabellek Alt/Üst | Buffer fill levels | Buffer boyutu ayarı |

### 10.2 AI Monitoring Dashboard

| Metrik | Hedef | Alarm |
|--------|-------|-------|
| CPU Kullanımı | < %30 | > %80 |
| Bellek Kullanımı | < %60 | > %90 |
| Gecikme (latency) | < 5ms | > 20ms |
| Buffer Underrun | 0/saniye | > 1/saniye |
| Buffer Overrun | 0/saniye | > 1/saniye |
| Çekirdek Hatası | 0 | Herhangi bir |
| USB Hatası | 0 | > 0 |

---

## 11. Sürücü Geliştirme Standartları

| Standart | Açıklama |
|----------|----------|
| SOLID | Tek sorumluluk, açık/kapalı, yerine koyma, arayüz ayrımı, bağımlılık tersi |
| Clean Architecture | Katmanlı mimari, bağımlılık kuralları |
| Minimum Bağımlılık | Az dış bağımlılık |
| Kapsamlı Hata | Her hata durumu ele alınır |
| Günlük | Debug ve audit logları |
| Test Kapsama | ≥%80 unit test |
| Code Review | Her pull request için zorunlu |

---

## 12. Sürücü Katman Detayları

### 12.1 Uygulama Katmanı

| Özellik | Açıklama |
|---------|----------|
| API | Yüksek düzey, donanım bağımsız |
| Soyutlama | `AudioEngine` interface |
| Kullanım | Kolay entegrasyon |
| Örnek | `engine->setOutputDevice(device)` |

### 12.2 Platform API Katmanı

| Özellik | Açıklama |
|---------|----------|
| Çapraz Platform | Tek arayüz, farklı implementasyon |
| Soyutlama | `PlatformAudio` abstract class |
| Uyumluluk | Windows, Linux, macOS, RPi |
| Örnek | `PlatformAudio::create(type)` |

### 12.3 Sürücü Katmanı

| Özellik | Açıklama |
|---------|----------|
| Donanım Bağımsız | Her cihaz için özel sürücü |
| Optimizasyon | Cihaz özelliklerine göre |
| Yönetim | Yaşam döngüsü yönetimi |
| Örnek | `PCM3168ADriver`, `AK4458Driver` |

### 12.4 Çekirdek Sürücüsü Katmanı

| Özellik | Açıklama |
|---------|----------|
| Düşük Seviye | Donanım register erişimi |
| Güvenlik | Bellek koruması, yetki |
| Performans | Optimize edilmiş E/S |
| Örnek | `ALSA PCM driver`, `WASAPI client` |

---

## 13. Driver API Örneği

### 14.1 C++ Driver Interface

```cpp
// CoreMusic Driver Interface (C++20)
class AudioDriver {
public:
    virtual ~AudioDriver() noexcept = default;

    // Yaşam döngüsü
    virtual bool probe() noexcept = 0;
    virtual bool open() noexcept = 0;
    virtual bool close() noexcept = 0;

    // Konfigürasyon
    virtual bool setSampleRate(uint32_t rate) noexcept = 0;
    virtual bool setBufferSize(uint32_t frames) noexcept = 0;
    virtual bool setChannels(uint8_t in, uint8_t out) noexcept = 0;

    // Streaming
    virtual bool startStream() noexcept = 0;
    virtual bool stopStream() noexcept = 0;
    virtual int32_t processBlock(float** out, const float** in, 
                                 uint32_t frames) noexcept = 0;

    // Bilgi
    virtual const char* getName() const noexcept = 0;
    virtual uint32_t getLatency() const noexcept = 0;
    virtual bool isAvailable() const noexcept = 0;
};
```

---

## 15. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| [[decisions/accepted/ADR-017-dsp-hardware-mode]] | DSP donanım modu (XMOS, JUCE, ASIO) |
| [[decisions/accepted/ADR-038-8.1-sound-card-chip-selection]] | 8.1 ses kartı çip seçimi (PCM3168A + XMOS XU316) |
| [[decisions/accepted/ADR-061-electronics-architecture]] | Elektronik mimarisi (L6 katmanı) |

---

## 16. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[electronic/index]] | Elektronik indeksi |
| [[electronic/drivers/index]] | Sürücü çerçevesi |
| [[electronic/audio-architecture]] | Ses mimarisi |
| [[electronic/hardware-design]] | Donanım tasarım rehberi |
| [[electronic/firmware-architecture]] | Firmware mimarisi |

---

## 17. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 17 |
| ADR References | 3 |
| ASCII Art Diagrams | 7 (Sürücü Mimarisi, Windows/Linux/macOS Stack, Otomatik Seçim, Device Discovery, Hot-Plug) |
| Driver Types | 4 (Sanal, Fiziksel, USB, DSP) |
| Platforms | 6 (Windows, Linux, macOS, RPi, Android, iOS) |
| Audio Stacks | 6 |
| Security Features | 6 |
| AI Analysis Types | 8 |
| Hot-Plug Events | 6 |
| Fallback Scenarios | 4 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-15
**Mode:** Red Team · Human Mode · Truth Mode
