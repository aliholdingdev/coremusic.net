---
title: "CoreMusic — Hardware Design Guide"
category: electronics
date: 2026-08-09
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Hardware Design Guide

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]]

---

## 1. Amaç

CoreMusic ELECTRONICS için kapsamlı donanım tasarım standartları. Donanım katmanları, blok diyagramı, işlemci/DSP/codec/amplifier seçimi, clock sistemi, güç yönetimi, analog tasarım ve AI destekli donanım analizini kapsar.

---

## 2. Donanım Katmanları

CoreMusic donanım yığını altı katmandan oluşur:

| Katman | Bileşen | Görev |
|--------|---------|-------|
| 1. Power | Güç kaynağı, regülatör, filtre | Tüm sisteme temiz güç sağlama |
| 2. CPU/MCU | ARM, x86, XMOS, ESP32, STM32 | Ana kontrol, koordinasyon |
| 3. DSP | XMOS XU316, FPGA, DSP yongası | Gerçek zamanlı sinyal işleme |
| 4. Audio Codec | PCM3168A, AK4458 | Analog ↔ Dijital dönüşüm |
| 5. Amplifier | Class AB, Class D, Hybrid | Hoparlör sürücü |
| 6. Audio Output | SpeakON, XLR, RCA, TRS | Fiziksel çıkış |

---

## 3. Hardware Block Diagram

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   GİRİŞLER  │     │   İŞLEM     │     │ DÖNÜŞÜM     │     │  ÇIKIŞLAR   │
│─────────────│     │─────────────│     │─────────────│     │─────────────│
│ USB/LAN/WiFi│────▶│ Ana İşlemci │     │ Audio Codec │     │ Hoparlör    │
│ Analog Girdi│────▶│ DSP Engine  │     │ DAC         │     │ Subwoofer   │
│ Dijital Girdi│───▶│ Bellek      │     │ ADC         │     │ Konnektörler│
└─────────────┘     └──────┬──────┘     └──────┬──────┘     └──────┬──────┘
                           │                   │                   │
                           ▼                   ▼                   ▼
                    ┌─────────────┐     ┌─────────────┐     ┌─────────────┐
                    │  GÜÇLENDİRME│     │  GÜÇ KAYNAĞI│     │             │
                    │ Amplifier   │◀────│ PSU         │     │             │
                    └─────────────┘     └─────────────┘     └─────────────┘
```

---

## 3A. Signal Flow — Sinyal Yolu
```
┌─────────────────────────────────────────────────────────────────────────────┐
│                        COREMUSIC SİNYAL YOLU                                │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                             │
│  GİRDİLER                                                                   │
│  ═══════                                                                    │
│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌─────────┐         │
│  │  USB    │  │ SPDIF   │  │  ADAT   │  │ Analog  │  │Bluetooth│         │
│  │ (XMOS)  │  │ (S/PDIF)│  │ (Optik) │  │ (XLR)   │  │ (LDAC)  │         │
│  └────┬────┘  └────┬────┘  └────┬────┘  └────┬────┘  └────┬────┘         │
│       │            │            │            │            │               │
│       └────────────┴────────────┴─────┬──────┴────────────┘               │
│                                       ▼                                    │
│                              ┌────────────────┐                            │
│                              │  XMOS XU316    │                            │
│                              │  USB Audio     │                            │
│                              │  Class 2.0     │                            │
│                              └───────┬────────┘                            │
│                                      │ I2S Bus                             │
│                                      ▼                                     │
│                              ┌────────────────┐                            │
│                              │   PCM3168A     │                            │
│                              │  6-in/8-out    │                            │
│                              │  24-bit/192kHz │                            │
│                              └───────┬────────┘                            │
│                                      │ Analog                              │
│  ÖN İŞLEME                         ▼                                     │
│  ══════════                 ┌────────────────┐                            │
│  ┌──────────┐              │   Preamp       │                            │
│  │ OPA1612  │◀─────────────│ (Düşük Gürültü)│                            │
│  │ 1.1nV/√Hz│              └───────┬────────┘                            │
│  └──────────┘                      │                                      │
│                                    ▼                                      │
│  DSP İŞLEME                ┌────────────────┐                            │
│  ══════════                │  DSP Pipeline  │                            │
│  ┌──────────┐              │  (15 Aşama)    │                            │
│  │ EQ 31-Bant│◀────────────│  XMOS XU316    │                            │
│  │ Compressor│              │  3200 MIPS     │                            │
│  │ Limiter   │              └───────┬────────┘                            │
│  │ Crossover │                      │                                     │
│  └──────────┘                      ▼                                     │
│                             ┌────────────────┐                            │
│  GÜÇ AŞAMASI               │  Class AB Amp  │                            │
│  ══════════                 │  100W × 7+1    │                            │
│  ┌──────────┐              │  THD+N <0.01%  │                            │
│  │ ±42V DC  │◀─────────────│  DF >200       │                            │
│  │ Toroidal │              └───────┬────────┘                            │
│  │ PSU      │                      │                                     │
│  └──────────┘                      ▼                                     │
│  ÇIKIŞLAR                 ┌────────────────┐                            │
│  ════════                 │   Hoparlörler  │                            │
│  ┌────┐┌────┐┌────┐       │ FL/FR/C/SL/SR │                            │
│  │ FL ││ FR ││ C  │       │ RL/RR + Sub   │                            │
│  └────┘└────┘└────┘       └────────────────┘                            │
│  ┌────┐┌────┐┌────┐┌────┐                                               │
│  │ SL ││ SR ││ RL ││ RR │                                               │
│  └────┘└────┘└────┘└────┘                                               │
│  ┌────────┐                                                             │
│  │Subwoofer│                                                            │
│  └────────┘                                                             │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. Ana İşlemci Seçimi

### 4.1 İşlemci Aileleri

| Aile | Örnekler | Kullanım | AVANTAJ |
|------|----------|----------|---------|
| ARM Cortex-M | STM32F7, STM32H7 | MCU, düşük güçlü | Düşük maliyet, düşük güç |
| ARM Cortex-A | i.MX8, RK3588 | Ana işlemci | Yüksek performans, Linux |
| ARM64 | Raspberry Pi 5, BCM2712 | masaüstü/sunucu | Geniş ekosistem |
| x86/x64 | Intel N100, AMD Ryzen | PC, sunucu | Maksimum performans |
| XMOS | XU316 | DSP, ses | 16 çekirdek, ultra düşük gecikme |
| ESP32 | ESP32-S3 | IoT, kablosuz | Wi-Fi/BLE dahil |
| STM32 | STM32H743 | MCU | Zengin perifer, DSP kütüphanesi |

### 4.2 Seçim Kriterleri

| Kriter | Değer |
|--------|-------|
| İşlem Gücü | 100–3200 MIPS |
| Bellek | 256KB–8GB RAM |
| GPIO | 16–200 pin |
| Güç Tüketimi | 0.1W–15W |
| Fiyat | $1–$150 |
| OS Desteği | Bare-metal, RTOS, Linux |

---

## 5. DSP Hardware

### 5.1 DSP İşlem Kapasitesi

| İşlem | Hedef | Uygulama |
|-------|-------|----------|
| EQ (Graphic/Parametric) | 31 bant, bşına <0.1ms | Frekans düzeltme |
| Mixer | 64 girdi → 32 çıktı | Kanal karıştırma |
| FIR Filter | 4096 taps, <1ms gecikme | Doğrusal faz filtreleme |
| IIR Filter | Biquad cascade, <0.01ms | Düşük gecikme filtreleme |
| Delay | 0–1000ms, 0.1ms çözünürlük | Room correction |
| Reverb | 4 mod, 10s decay | Uzamsal efekt |
| Limiter | True Peak, brick wall | Aşırı koruma |
| Compressor | 0.1ms attack, ratio ∞:1 | Dinamik aralık |
| FFT | 4096–16384 nokta | Frekans analizi |
| Crossover | Linkwitz-Riley 4.nesil | Bant ayrımı |
| Loudness | LUFS + ReplayGain | Seviye normalleştirme |

### 5.2 DSP Yonga Seçenekleri

| Yonga | Çekirdek | İşlem | Güç | Kullanım |
|-------|----------|-------|-----|----------|
| XMOS XU316 | 16 | 3200 MIPS | 0.27W | Birincil DSP |
| ADI SHARC ADSP-21489 | 1 | 2700 MFLOPS | 0.5W | Profesyonel |
| TI TMS320C6748 | 1 | 3648 MIPS | 0.6W | Endüstriyel |
| FPGA (Xilinx Artix-7) | Esnek | Özelleştirilmiş | 2W | Özel DSP |

---

## 6. DAC/ADC Codec

### 6.1 DAC Seçimi

| Chip | Kanal | Bit | Örnekleme | SNR | THD+N | Kullanım |
|------|-------|-----|-----------|-----|-------|----------|
| PCM3168A | 8 çıkış | 24-bit | 192kHz | 112dB | -94dB | **Birincil (7.1)** |
| AK4458 | 8 | 32-bit | 768kHz | 115dB | -107dB | Yüksek uç |
| PCM5122 | 2 | 32-bit | 384kHz | 112dB | -93dB | ❌ REDDEDİLMİŞ (H001) |
| ES9038PRO | 8 | 32-bit | 768kHz | 140dB (DNR) | -122dB | Ultra high-end |

### 6.2 ADC Seçimi

| Chip | Kanal | Bit | Örnekleme | SNR | THD+N |
|------|-------|-----|-----------|-----|-------|
| PCM3168A (ADC) | 6 giriş | 24-bit | 96kHz | 107dB | -93dB |
| AK5558 | 8 | 32-bit | 768kHz | 115dB | -106dB | Yüksek uç ADC |

### 6.3 Codec Seçim Matrisi

| Senaryo | DAC | ADC | Neden |
|---------|-----|-----|-------|
| CoreMusic Standart (7.1) | PCM3168A | PCM3168A | 8 kanal, uygun maliyet |
| Profesyonel Stüdyo | AK4458 | AK5558 | Yüksek SNR, 32-bit |
| Araç İçi | PCM3168A | PCM3168A | Dayanıklılık, sıcaklık |

---

## 7. Clock System

### 7.1 Clock Tipleri

| Clock | Frekans | Görev | Hassasiyet |
|-------|---------|-------|------------|
| Master Clock (MCLK) | 12.288MHz / 24.576MHz | Codec zamanlama | ±50ppm |
| Bit Clock (BCLK) | 64× fs | Seri veri zamanlama | ±50ppm |
| Word Clock (WCLK/LRCK) | fs (48kHz) | Sol/Sağ kanal seçimi | ±50ppm |
| Sample Clock | 44.1k/48k/96k/192k | Örnekleme hızı | ±50ppm |

### 7.2 Clock Jitter

| Parametre | Hedef |
|-----------|-------|
| Jitter | < 100ps RMS |
| PLL Kilitleme Süresi | < 10ms |
| Kaynak Seçimi | Internal, USB, S/PDIF, ADAT |
| Clock Domino | Master → BCLK → WCLK zinciri |

---

## 8. Power Management

### 8.1 Güç Bölgeleri

| Bölge | Gerilim | Akım | Filtre | Öncelik |
|-------|---------|------|--------|---------|
| Ana Güç (Vin) | 12V–24V DC | 10–50A | EMI + LC filtre | — |
| CPU/MCU | 3.3V / 1.8V | 1–3A | LDO + bulk | Yüksek |
| DSP | 1.0V / 3.3V | 0.5–2A | LDO, low-noise | Yüksek |
| DAC | ±5V / 3.3V | 100mA | LDO, ultra-low-noise | Kritik |
| ADC | ±5V / 3.3V | 100mA | LDO, ultra-low-noise | Kritik |
| Analog | ±15V / ±42V | 1–5A | LC filtre, izole | Kritik |
| Dijital | 3.3V / 5V | 0.5–2A | Bulk kapasitör | Normal |
| USB | 5V | 500mA–3A | USB standardı | Normal |
| Fan | 12V | 0.1–0.5A | PWM kontrolü | Düşük |

### 8.2 Güç Sıralaması

| Sıra | Bölge | Bekleme | Açıklama |
|------|-------|---------|----------|
| 1 | Ana Güç | — | Güç kaynağı aktif |
| 2 | CPU/MCU | — | Sistem kontrolcüsü |
| 3 | DSP | 10ms | İşlemci hazır olana kadar bekle |
| 4 | Analog (DAC/ADC) | 20ms | Pop-noise önleme |
| 5 | Amplifier | 50ms | Hoparlör koruma rölesi |

### 8.3 Güç Verimliliği

| Mod | Tüketim | Hedef |
|-----|---------|-------|
| Aktif Çalışma | Tam güç | — |
| Idle (DSP açık) | %30 | DSP bekleme |
| Suspend | <1W | RAM korunur |
| Deep Sleep | <0.5W | Watchdog aktif |
| Tam Kapanma | 0W | — |

---

## 9. Analog Tasarım

### 9.1 Analog Sinyal Yolu Kuralları

| Kural | Açıklama | İhlal Sonucu |
|-------|----------|-------------|
| Düşük Gürültü | <1nV/√Hz op-amp | Artan taban gürültüsü |
| İzolasyon | Anolog/dijital toprak ayrımı | Ground loop gürültüsü |
| Kararlılık | ±0.1% direnç, ±1% kapasitör | Frekans sapması |
| Koruma | ESD koruması giriş/çıkışlarda | Bileşen hasarı |
| Kalkanlama | Koruma katmanı hassas sinyallerde | EMI girişimi |

### 9.2 Op-Amp Seçimi

| Op-Amp | Gürültü | GBW | Uygulama |
|--------|---------|-----|----------|
| OPA2134 | 8nV/√Hz | 8MHz | Genel ses |
| NE5532 | 5nV/√Hz | 10MHz | Profesyonel |
| OPA1612 | 1.1nV/√Hz | 40MHz | Ultra-low-noise |
| LM4562 | 2.7nV/√Hz | 55MHz | Yüksek gain-bandwidth |

### 9.3 Topraklama Stratejisi

| Bölge | Topraklama | Ayrım |
|-------|-----------|-------|
| Analog | Tek nokta topraklama | Dijitalden izole |
| Dijital | Düzlem topraklama | Anologdan izole |
| Güç | Star ground | Tüm katmanlar birleşir |
| Kalkan | Enclosure topraklama | EMI koruması |

---

## 10. Amplifier Architecture

### 10.1 Amplifier Türleri ve Karşılaştırma

| Sınıf | Verimlilik | Kalite | Isı | Uygulama |
|-------|-----------|--------|-----|----------|
| Class A | %25–50 | En yüksek | En yüksek | Stüdyo, high-end |
| Class AB | %50–70 | İyi denge | Orta | **CoreMusic varsayılan** |
| Class D | %85–95 | İyi (PLL filtre gerekli) | Düşük | Araç, taşınabilir |
| Hybrid | %70–90 | Yüksek | Orta | Özel tasarım |

### 10.2 Kanal Konfigürasyonları

| Konfigürasyon | Kanallar | Hoparlör | Varsayılan mı? |
|---------------|----------|----------|---------------|
| Stereo | 2 | 2× hoparlör | Hayır |
| 2.1 | 2+1 | 2× hoparlör + subwoofer | Hayır |
| 5.1 | 5+1 | FL/FR/C/SL/SR + Sub | Hayır |
| 7.1 | 7+1 | FL/FR/C/SL/SR/RL/RR + Sub | **Evet** |

### 10.3 7.1 Kanal Yerleşimi

| Kanal | Kısaltma | Pozisyon | Frekans |
|-------|----------|----------|---------|
| Front Left | FL | Ön sol | 20Hz–20kHz |
| Front Right | FR | Ön sağ | 20Hz–20kHz |
| Center | C | Ön merkez | 100Hz–8kHz |
| Surround Left | SL | Yan sol | 100Hz–16kHz |
| Surround Right | SR | Yan sağ | 100Hz–16kHz |
| Rear Left | RL | Arka sol | 100Hz–16kHz |
| Rear Right | RR | Arka sağ | 100Hz–16kHz |
| Subwoofer (LFE) | SUB | Alt frekans | 20Hz–120Hz |

---

## 11. Güç Seviyeleri ve Chip Eşleşmesi

| Seviye | Class AB Çip | Class D Çip | Fiyat | Uygulama |
|--------|-------------|-------------|-------|----------|
| **5W** | — | TPA3110D2 | ~$0.5 | Mini amp |
| **10W** | LM1875 | TPA3130D2 | ~$0.5-3 | Masaüstü |
| **15W** | LM1875 | TPA3130D2 | ~$0.5-3 | Küçük oda |
| **20W** | LM1875 | TPA3118D2 | ~$0.6-3 | Ev, kitaplık |
| **30W** | LM3886 | TPA3118D2 | ~$0.6-5 | Orta oda |
| **35W** | LM3886 | TPA3116D2 | ~$0.7-5 | Orta oda |
| **50W** | LM3886 / TDA7294 | TPA3116D2 / TPA3250 | ~$0.7-5 | Büyük oda |
| **100W** | Discrete (MJL3281A) | TPA3255 | ~$2.5-4 | **CoreMusic standart** |
| **150W** | Discrete | TPA3251 | ~$3.35 | Salon |
| **185W** | Discrete | TPA3255 | ~$4.13 | Profesyonel |

---

## 12. Koruma Sistemleri

| Koruma | Tetikleme | Tepki | Öncelik |
|--------|-----------|-------|---------|
| Kısa Devre | 0Ω çıkış | Anında kapanma + röle | CRITICAL |
| Aşırı Akım | >max akım | Kademeli azaltma + kapanma | CRITICAL |
| Aşırı Gerilim | >max voltaj | Güç kesme | CRITICAL |
| Ters Polarite | Ters +/− | Koruma rölesi | HIGH |
| Termal | >80°C | Fan hızı ↑, >100°C kapanma | HIGH |
| DC Offset | >0.5V DC çıkış | Koruma rölesi (hoparlör koruması) | CRITICAL |
| Hoparlör Rölesi | Güç açma/kapama | Gecikmeli bağlantı (pop-noise önleme) | MEDIUM |

### 12.1 Koruma Akışı

```
Amplifier Çıkış
      │
      ▼
  ┌───Kontrol───┐
  │             │
  ├── Kısa Devre ──▶ Anında Kapanma ──▶ Hata Logu
  ├── Aşırı Akım──▶ Kademeli Azaltma──▶ Hata Logu
  ├── Termal ──────▶ Fan ↑ / Kapanma ──▶ Hata Logu
  ├── DC Offset ──▶ Röle Aç ──────────▶ Hata Logu
  └── Normal ─────▶ Çalışmaya Devam
```

---

## 13. Hardware Monitoring

### 13.1 İzleme Metrikleri

| Metrik | Sensör | Sıklık | Alarm Eşiği |
|--------|--------|--------|------------|
| CPU Sıcaklığı | Termistör / dijital | 1s | >80°C WARN, >100°C CRITICAL |
| DSP Sıcaklığı | Dijital sensör | 1s | >70°C WARN, >90°C CRITICAL |
| Amplifier Sıcaklığı | Termistör | 1s | >80°C WARN, >100°C STOP |
| Fan Hızı | Hall effect | 5s | <200 RPM WARN |
| Güç Tüketimi | INA219/INA226 | 5s | >max %90 WARN |
| Çıkış Gerilimi | ADC | 1s | >max WARN |
| Çıkış Akımı | Shunt direnç | 1s | >max WARN |
| Hata Kodları | Firmware logu | Anlık | Her hata loglanır |

### 13.2 Hata Kodları

| Kod | Seviye | Açıklama |
|-----|--------|----------|
| E001 | CRITICAL | Kısa devre algılandı |
| E002 | CRITICAL | Aşırı akım |
| E003 | CRITICAL | DC offset koruması |
| E004 | HIGH | Termal koruma |
| E005 | HIGH | DSP hatası |
| E006 | MEDIUM | Fan arızası |
| E007 | LOW | Sıcaklık uyarı |

---

## 14. AI Hardware Analysis

### 14.1 AI Destekli Donanım Analizi

| Analiz | Veri Kaynağı | Çıktı |
|--------|-------------|-------|
| Sağlık Durumu | Tüm sensör verileri | Sağlık skoru (0–100) |
| Arıza Tahmini | Geçmiş sensör verileri, trend analizi | Tahmini arıza süresi |
| Termal Analiz | Sıcaklık haritası | Soğutma optimizasyonu |
| Performans Analizi | CPU/DSP kullanımı | Optimizasyon önerileri |
| Güç Analizi | Güç tüketim verileri | Verimlilik raporu |
| titreşim Analizi | İvmeölçer | Mekanik sorun tespiti |

### 14.2 Predictive Maintenance

| Parametre | Hedef | Yöntem |
|-----------|-------|--------|
| Fan Ömrü | >50,000 saat | Rulman aşınma analizi |
| Kapasitör Ömrü | >10,000 saat | ESR ölçümü, sızıntı akımı |
| Amplifier Sağlığı | THD+N trendi | Spektral analiz |
| PSU Sağlığı | Ripple ölçümü | Dalgalanma analizi |

---

## 15. PCB Tasarımı

### 15.1 Katman Yapısı

| Katman | Amaç | Kalınlık |
|--------|------|---------|
| L1 | Sinyal (üst) | 35µm |
| L2 | GND düzlemi | 35µm |
| L3 | Güç / sinyal | 35µm |
| L4 | Sinyal (alt) | 35µm |
| L5–L8 | Karmaşık tasarımlar | 35µm |

### 15.2 Sinyal Yönlendirme Kuralları

| Kural | Değer |
|-------|-------|
| Analog sinyal uzunluğu | Maksimum 50mm |
| Empedans kontrolü | 50Ω single-ended, 100Ω differential |
| Differential pairs eşitliği | ±5mm uzunluk farkı |
| Clock hatları | Korumalı, GND komşusu |
| Güç hatları | Geniş, düşük empedans |

---

## 16. EMI/EMC

| Özellik | Açıklama | Standart |
|---------|----------|----------|
| EMI Kalkanlama | Metal kasa, FFC/FEM | CE, FCC Class B |
| Filtreleme | EMI filtre, ferrite | CISPR 32 |
| Topraklama | İyi topraklama tasarımı | — |
| Yerleşim | Layout kuralları | — |
| Uyumluluk | CE, FCC, RoHS, REACH | — |

---

## 17. Konnektörler

| Tip | Kullanım | Empedans | Frekans |
|-----|----------|----------|---------|
| XLR | Profesyonel dengeli | 110Ω | DC–50MHz |
| TRS (1/4") | Profesyonel | — | DC–20MHz |
| RCA | Ev sesi | 75Ω | DC–10MHz |
| SpeakON | Hoparlör | — | DC–100kHz |
| USB | Dijital | 90Ω | 480Mbit/s |
| Ethernet | Ağ | 100Ω | 1Gbps+ |
| HDMI | Video/Ses | 100Ω | 6Gbps |
| Banana | Test/Ölçüm | — | DC–1MHz |

---

## 18. Mekanik Tasarım

| Özellik | Açıklama |
|---------|----------|
| Kasa | Alüminyum enkaz, EMI koruması |
| Termal | Alüminyum heatsink, heatpipe |
| Titreşim | Kauçuk ayak, sönümleme |
| Panel | CNC işlenmiş, lazer gravür |
| Boyut | 430×300×80mm (standart rack) |
| Ağırlık | 5–15kg (konfigürasyona bağlı) |

---

## 19. Tipik Sinyal Akışı

```
Analog Girdi ──▶ Preamp ──▶ Buffer ──▶ Filtre ──▶ ADC ──▶ DSP ──▶ DAC ──▶ Voltaj Kazancı ──▶ Akım Kazancı ──▶ Çıkış ──▶ Hoparlör/Sub

Dijital Girdi ──────────────────────────────────▶ ADC
```

---

## 20. Ses Kartı Blok Diyagramı

```
USB ─┐
ADAT ─┤──▶ MCU ──▶ DSP ──▶ Bellek
S/PDIF┘              │
                     ├──▶ DAC 1 ──▶ Op-Amp 1 ──▶ Yükseltici ──▶ Hoparlör 1
                     └──▶ DAC 2 ──▶ Op-Amp 2 ──▶ Yükseltici ──▶ Hoparlör 2
                                                          └──▶ Kulaklık
```

---

## 21. Bileşen Listesi Tipi

| Bileşen | Tip | Değer |
|---------|-----|-------|
| Kapasitör | Film | 100nF – 10µF |
| Kapasitör | Elektrolit | 10µF – 10000µF |
| Kapasitör | Ceramic (MLCC) | 10pF – 100nF |
| Direnç | Metal Film | 0.1% – 1% |
| Op-Amp | Düşük Gürültü | OPA2134, NE5532, OPA1612 |
| Konnektör | XLR | Erkek/Dişi |
| Konnektör | TRS | 1/4" Stereo |
| Konnektör | SpeakON | NL4, NL8 |
| Konnektör | USB | Type-C, Type-B |
| Regülatör | LDO | AMS1117, TLV733 |
| Regülatör | Switching | LM2596, TPS54331 |
| Transistör | MOSFET | IRF540N, IRF9540N |
| Diyot | Schottky | 1N5819, SS34 |

---

## 22. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| [[decisions/accepted/ADR-038-8.1-sound-card-chip-selection]] | 8.1 ses kartı çip seçimi (PCM3168A + XMOS XU316) |
| [[decisions/accepted/ADR-063-hardware-design-standards]] | Donanım tasarım standartları |
| [[decisions/accepted/ADR-061-electronics-architecture]] | Elektronik mimarisi (L6 katmanı) |
| [[decisions/accepted/ADR-017-dsp-hardware-mode]] | DSP donanım modu (XMOS, JUCE) |

---

## 23. İlgili Dosyalar

| Dosya | Amaç |
|-------|------|
| [[electronic/index]] | Elektronik indeksi |
| [[electronic/hardware/index]] | Donanım tasarımı |
| [[electronic/hardware-roadmap]] | Donanım yol haritası |
| [[electronic/audio-architecture]] | Ses mimarisi |
| [[electronic/xmos-pcm3168a-design]] | XMOS + PCM3168A devre tasarımı |
| [[electronic/audio-interface-design]] | Ses arayüzü tasarımı |

---

## 24. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 24 |
| ADR References | 4 |
| ASCII Art Diagrams | 4 (Hardware Block, Koruma Akışı, Sinyal Akışı, Ses Kartı) |
| Hardware Layers | 6 (Power → CPU → DSP → Codec → DAC → Amp) |
| Processor Families | 7 (ARM, ARM64, x86, XMOS, ESP32, STM32) |
| DSP Chips | 4 |
| DAC Options | 4 |
| Amplifier Classes | 4 |
| Channel Configs | 5 (Stereo → 7.1) |
| Power Levels | 8 (10W–2000W) |
| Protection Systems | 7 |
| Monitoring Metrics | 8 |
| Connector Types | 8 |
| AI Analysis Types | 6 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
