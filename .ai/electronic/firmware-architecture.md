---
title: "CoreMusic — Firmware Architecture"
category: electronics
date: 2026-08-09
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Firmware Architecture

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]]

---

## 1. Amaç

CoreMusic ELECTRONICS cihazları için düşük seviye yazılım mimarisi. Firmware katmanları, yaşam döngüsü, RTOS, HAL, driver layer, OTA güncelleme, kurtarma ve firmware güvenliğini kapsar.

---

## 2. Firmware Katmanları

CoreMusic firmware'i beş katmandan oluşur:

| Katman | Görev | Örnekler |
|--------|-------|----------|
| 1. Bootloader | Güvenli önyükleme, firmware doğrulama | U-Boot, custom bootloader |
| 2. Device Init | Cihaz başlatma, sensör algılama | GPIO init, clock config |
| 3. Hardware Config | Perifer konfigürasyonu | SPI/I2C/UART setup |
| 4. Communication | İletişim protokolleri | USB, Ethernet, Wi-Fi, BT |
| 5. Application | Uygulama mantığı | DSP processing, audio routing |

### 2.1 Katmanlı Mimari Diyagramı

```
Katman 5 (Uygulama):    DSP Processing / Audio Routing / Control Logic
Katman 4 (İletişim):    USB Stack / Ethernet / Wi-Fi / Bluetooth
Katman 3 (Donanım):     GPIO / SPI / I2C / UART / I2S(TDM) / EEPROM
Katman 2 (Başlatma):    Device Init / Perifer Algılama / Clock Config
Katman 1 (Bootloader):  Bootloader / Firmware Verify / Recovery Mode
```

---

## 3. Firmware Yaşam Döngüsü

| Aşama | Görev | Çıktı | Süre |
|-------|-------|-------|------|
| 1. Boot | Güç açma → bootloader | Firmware yükleme | <100ms |
| 2. Initialize | Perifer algılama, clock ayarlama | Cihaz haritası | <50ms |
| 3. Configure | GPIO/SPI/I2C/UART yapılandırma | Hazır periferler | <20ms |
| 4. Run | Normal çalışma, DSP processing | Ses çıkışı | Sürekli |
| 5. Update | OTA firmware güncelleme | Yeni firmware | Değişken |
| 6. Recovery | Hatalı güncelleme kurtarma | Eski firmware | <5s |

### 3.1 Yaşam Döngüsü Diyagramı

```
Güç Açma ──▶ Bootloader ──▶ {Firmware Doğrulama}
                                │
                       Başarılı ▼ Başarısız
                    Device Init    Recovery Mode
                         │
                         ▼
                    Hardware Config ──▶ Application Run ──▶ {Güncelleme?}
                                                          │
                                                     Evet ▼ Hayır
                                                  OTA Download  Devam
                                                       │
                                                       ▼
                                              İmza Doğrulama ──▶ Firmware Yazma ──▶ Yazma Doğrulama ──▶ Yeniden Başlat
                                                       │                                        │
                                                  Başarısız                                 Başarısız
                                                       │                                        │
                                                       ▼                                        ▼
                                                 Recovery Mode ◀────────────────────────────────┘
```

---

## 4. RTOS (Real-Time Operating System)

### 4.1 RTOS Kullanım Nedenleri

| Özellik | Açıklama | Gerekçe |
|---------|----------|---------|
| Deterministic | Zaman tahmin edilebilirliği | Ses gecikmesi garantisi |
| Görev Zamanlama | Öncelik tabanlı | DSP task > network task |
| Kesme Yönetimi | Düşük latency | Donanım kesmeleri |
| Bellek Yönetimi | Deterministic allocation | Zero-allocation hedefi |
| Eşzamanlılık | Mutex, semaphore | Kaynak koruma |

### 4.2 Öncelik Sıralaması

| Öncelik | Görev | Deadline | Aksiyon |
|---------|-------|----------|---------|
| 0 (En yüksek) | Audio callback | 10ms | Kesme |
| 1 | DSP processing | 10ms | Zaman kritik |
| 2 | USB isochronous | 1ms | Transfer |
| 3 | Network | 100ms | Normal |
| 4 | OTA update | 1s | Arka plan |
| 5 (En düşük) | Idle | — | Güç tasarrufu |

### 4.3 RTOS Tercihi

| RTOS | Avantaj | Kullanım |
|------|---------|----------|
| FreeRTOS | Yaygın, ücretsiz, küçük | ESP32, STM32 |
| Zephyr | Modern, BLE native, modüler | ARM Cortex-M/A |
| ThreadX (Azure RTOS) | Microsoft desteği | ARM, XTENSA |
| Bare-metal | Maksimum kontrol | XMOS (xcc toolchain) |

**CoreMusic Tercihi:** XMOS XU316 için bare-metal (xcc ile), STM32/ESP32 için FreeRTOS.

---

## 5. HAL (Hardware Abstraction Layer)

### 5.1 HAL Arayüzleri

| Arayüz | Görev | Protokol | Hız |
|--------|-------|----------|-----|
| GPIO | Dijital giriş/çıkış | Register erişimi | ns |
| SPI | Yüksek hız seri | 4 hat, master/slave | 50MHz |
| I2C | Düşük hız seri | 2 hat, address-based | 400kHz |
| UART | Asenkron seri | TX/RX, baud rate | 115200 baud |
| USB | Universal Serial Bus | Isochronous/Bulk | 480Mbit/s |
| I2S/TDM | Ses veri akışı | Bit clock, word clock | 192kHz |
| EEPROM | Kalıcı depolama | I2C/SPI | 1MHz |

### 5.2 HAL Soyutlama

```cpp
// CoreMusic HAL Arayüzü (C++20, noexcept)
class AudioHAL {
public:
    virtual ~AudioHAL() noexcept = default;
    
    virtual bool init(uint32_t sampleRate, uint8_t channels) noexcept = 0;
    virtual bool startStream() noexcept = 0;
    virtual bool stopStream() noexcept = 0;
    virtual int32_t processBlock(float** output, const float** input, 
                                 uint32_t frames) noexcept = 0;
    
    virtual uint32_t getSampleRate() const noexcept = 0;
    virtual uint8_t getChannels() const noexcept = 0;
    virtual uint32_t getLatency() const noexcept = 0;
};
```

---

## 6. Driver Layer

### 6.1 Driver Kategorileri

| Kategori | Örnekler | Görev |
|----------|----------|-------|
| Audio Codec | PCM3168A, AK4458 | DAC/ADC sürücüsü |
| USB Audio | USB Audio Class 2.0 | USB ses transferi |
| DSP | XMOS XU316 loader | Firmware yükleme, parametre |
| I2S/TDM | Bit clock, word clock | Seri ses veri |
| Ethernet | TCP/IP stack | Ağ iletişimi |
| Wi-Fi | WPA2/WPA3 | Kablosuz |
| Bluetooth | A2DP, HFP | Kablosuz ses |
| SPI Flash | W25Q128, MX25L | Firmware depolama |
| EEPROM | 24LC256 | Ayar depolama |

### 6.2 Driver Yaşam Döngüsü

| Aşama | İşlem |
|-------|-------|
| 1. Algılama | Hot-plug, device enumeration |
| 2. Yükleme | Firmware bin yükleme |
| 3. Başlatma | Register yapılandırma |
| 4. Çalıştırma | Veri transferi, processing |
| 5. Güncelleme | Parametre değişikliği |
| 6. Kaldırma | Güvenli kapatma |

---

## 7. OTA (Over-The-Air) Update

### 7.1 OTA Güncelleme Akışı

```
Güncelleme İsteği ──▶ {Versiyon Kontrolü}
                          │
                 Yeni Versiyon ▼ Güncel
                    İndirme      Devam
                       │
                       ▼
                 İmza Doğrulama ──▶ Yedekleme ──▶ Yazma ──▶ Yazma Doğrulama ──▶ Yeniden Başlatma ──▶ Önyükleme Testi ──▶ Tamamlandı
                       │                                              │                                    │
                  Başarısız                                      Başarısız                             Başarısız
                       │                                              │                                    │
                       ▼                                              ▼                                    ▼
                 Hata Logu                                      Geri Alma ◀──────────────────────────────┘
```

### 7.2 OTA Stratejisi

| Özellik | Değer |
|---------|-------|
| Protokol | HTTPS, MQTT |
| Bant genişliği | Düşük öncelikli arka plan |
| Bileşen | Dual-bank (A/B partition) |
| İmza | RSA-2048 veya Ed25519 |
| Sıkıştırma | LZ4, gzip |
| Delta güncelleme | Destekli (differental OTA) |

---

## 8. Recovery (Kurtarma)

### 8.1 Kurtarma Senaryoları

| Senaryo | Tetikleme | Kurtarma |
|---------|-----------|----------|
| Boot başarısız | Bootloader 3 deneme | Recovery partition |
| OTA başarısız | Yazma hatası | Rollback (A→B partition) |
| Watchdog timeout | Cihaz kitlendi | Hardware reset |
| Güç kesintisi | OTA sırasında | Dual-bank koruması |
| Bozulmuş firmware | CRC hatası | Recovery USB |

### 8.2 Kurtarma Prosedürü

| Adım | İşlem | Süre |
|------|-------|------|
| 1 | Recovery partition'dan bootloader | <100ms |
| 2 | Son iyi bilinen firmware yükle | <1s |
| 3 | Hardware test (BIST) | <500ms |
| 4 | Normal çalışmaya geç | Anlık |

### 8.3 Watchdog Timer

| Parametre | Değer |
|-----------|-------|
| Tip | Window watchdog |
| Süre | 100ms window |
| Başarısız | Hardware reset |
| Besleme | Her döngüde feed |

---

## 9. Firmware Bileşenleri

| Bileşen | Görev | Öncelik |
|---------|-------|---------|
| Başlatma (Init) | Cihaz başlatma, sensör algılama | Yüksek |
| Konfigürasyon | Perifer register ayarlama | Yüksek |
| Clock Yönetimi | PLL, MCLK, BCLK, WCLK | Kritik |
| DMA Yönetimi | Bellek transferi (audio buffer) | Yüksek |
| Kesme Yönetimi | IRQ priority, nested interrupts | Kritik |
| Güç Yönetimi | Uyku modları, güç sıralaması | Orta |
| Termal İzleme | Sıcaklık okuma, fan kontrolü | Orta |
| Hata Raporlama | Loglama, teşhis | Düşük |
| EEPROM | Kalıcı ayar depolama | Düşük |
| Watchdog | Sistem sağlığı izleme | Yüksek |

---

## 10. İletişim Firmware'i

### 10.1 Protokol Yığını

| Protokol | Katman | Hız | Kullanım |
|----------|--------|-----|----------|
| USB Audio Class 2.0 | Transport | 480Mbit/s | Ses transferi |
| I2S | Serial | 192kHz×32bit×8ch | DAC/ADC bağlantısı |
| TDM | Serial | 192kHz×32bit×16ch | Çoklu codec |
| SPI | Serial | 50MHz | DSP↔CPU iletişim |
| I2C | Serial | 400kHz | Konfigürasyon |
| Ethernet (TCP/IP) | Ağ | 1Gbps | Uzaktan yönetim |
| Wi-Fi (802.11ac) | Ağ | 1.3Gbps | Kablosuz |
| Bluetooth (BLE 5.3) | Ağ | 2Mbps | Düşük güçlü kablosuz |
| MQTT | Uygulama | Ağ hızı | IoT, sensör verisi |

### 10.2 USB İletişim

| Sınıf | Alt Sınıf | Kullanım |
|-------|-----------|----------|
| Audio (0x01) | AudioStreaming (0x02) | Ses girdi/çıkış |
| Audio (0x01) | MIDI (0x03) | MIDI verisi |
| HID (0x03) | — | Kontrol cihazları |
| CDC (0x02) | ACM (0x02) | Seri port emülasyonu |

---

## 11. DSP Firmware Detayları

### 11.1 XMOS XU316 DSP

| Özellik | Değer |
|---------|-------|
| Yonga | XMOS XU316 |
| Çekirdek | 16 çekirdek, 32-bit |
| İşlem Gücü | 3200 MIPS |
| Bellek | 512KB SRAM + 16MB外部 |
| DSP Pipeline | 7-stage pipeline |
| SIMD | 32-bit multiply-accumulate |
| Katsayı Güncelleme | Runtime coefficient updates |
| Preset | Flash'ta 64 preset depolama |

### 11.2 DSP Firmware Modülleri

| Modül | Görev |
|-------|-------|
| Coefficient Manager | EQ, crossover, filtre katsayıları |
| Preset Manager | Preset yükleme/kaydetme/silme |
| Stream Manager | Ses akışı yönlendirme |
| IRQ Handler | Kesme yönetimi |
| DMA Manager | Bellek transferi |
| Watchdog | DSP sağlık izleme |

---

## 12. Güvenlik

### 12.1 Firmware Güvenlik Önlemleri

| Özellik | Açıklama | Değer |
|---------|----------|-------|
| Güvenli Önyükleme | Root of trust | BootROM → Bootloader → Firmware |
| Dijital İmza | Firmware doğrulama | RSA-2048 veya Ed25519 |
| Rollback Koruması | Eski versiyona geri dönüş | Dual-bank A/B partition |
| Watchdog | Sistem kitlenmesi önleme | 100ms window watchdog |
| Flash Koruması | Yazma koruması | Write-protect register |
| Bellek Koruması | Stack overflow koruması | MPU (Memory Protection Unit) |

### 12.2 Secure Boot Zinciri

```
BootROM ──▶ Bootloader ──▶ Primary Firmware ──▶ Application ──▶ Normal Çalışma
    │             │                │
    │         Başarısız       Başarısız
    │             │                │
    │             ▼                ▼
    │         Recovery Mode   Alternate Partition
    └────── Root of Trust
```

---

## 13. Önyükleme Sırası

```
Güç Açma ──▶ Başlangıç ROM ──▶ {Güvenli Önyükleme}
                                   │
                          Başarılı ▼ Başarısız
                       Bootloader    Kurtarma Modu
                            │
                            ▼
                     Firmware Doğrulama
                            │
                     Başarılı ▼ Başarısız
                  RTOS Başlatma  Kurtarma Modu
                       │
                       ▼
                  HAL Başlatma ──▶ Sürücü Yükleme ──▶ Cihaz Başlatma ──▶ Normal Çalışma
```

---

## 14. Gerçek Zamanlı Ses İşleme

| Özellik | Değer | Açıklama |
|---------|-------|----------|
| Gecikme | Ultra düşük (<1ms) | DSP pipeline optimized |
| Belirleyicilik | Deterministic | Her blok sabit süre |
| Öncelik | TIME_CRITICAL | Audio thread en yüksek |
| Bellek | Zero-allocation | Runtime heap yasak |
| Kilit | Lock-free | Atomic operations |
| SIMD | SSE2/AVX2/NEON | Hızlandırma |

---

## 15. Güç Yönetimi

| Mod | CPU | DSP | Peripheral | Uyanma |
|-----|-----|-----|------------|--------|
| Active | Tam hız | Tam hız | Aktif | — |
| Idle | Düşük hız | Düşük hız | Seçili | Kesme ile |
| Sleep | Kapalı | Kapalı | RAM aktif | Watchdog/RTC |
| Deep Sleep | Kapalı | Kapalı | Sadece GPIO | Düğme/RTC |
| Shutdown | Kapalı | Kapalı | Sıfır | Güç tuşu |

---

## 16. Termal Yönetim

| Sensör | Konum | Sıcaklık | Aksiyon |
|--------|-------|----------|---------|
| TS1 | DSP yongası | 0–125°C | Fan PWM |
| TS2 | Amplifier heatsink | 0–150°C | Kapanma |
| TS3 | PCB orta | -40–85°C | Uyarı |
| TS4 | PSU çıkış | 0–100°C | Fan PWM |

---

## 17. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| [[decisions/accepted/ADR-017-dsp-hardware-mode]] | DSP donanım modu (XMOS, JUCE) |
| [[decisions/accepted/ADR-038-8.1-sound-card-chip-selection]] | 8.1 ses kartı çip seçimi (PCM3168A + XMOS XU316) |
| [[decisions/accepted/ADR-061-electronics-architecture]] | Elektronik mimarisi (L6 katmanı) |

---

## 18. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[electronic/index]] | Elektronik indeksi |
| [[electronic/firmware/index]] | Firmware mimarisi |
| [[electronic/dsp/index]] | DSP motoru |
| [[electronic/drivers/index]] | Sürücü çerçevesi |
| [[electronic/xmos-pcm3168a-design]] | XMOS + PCM3168A tasarımı |

---

## 19. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 19 |
| ADR References | 3 |
| ASCII Art Diagrams | 5 (Katmanlı Mimari, Yaşam Döngüsü, OTA, Secure Boot, Önyükleme) |
| Firmware Layers | 5 (Boot → Init → Config → Comm → App) |
| HAL Interfaces | 7 (GPIO, SPI, I2C, UART, USB, I2S, EEPROM) |
| RTOS Options | 4 (FreeRTOS, Zephyr, ThreadX, Bare-metal) |
| Communication Protocols | 9 |
| Security Features | 6 |
| Power Modes | 5 |
| Thermal Sensors | 4 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
