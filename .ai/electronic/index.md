---
type: system
category: electronic-architecture
title: "CoreMusic Electronics — Master Index"
date: 2026-08-09
updated: 2026-08-10
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic Electronics — Master Index

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[brain.md]] · [[index.md]]

---

## 1. Amaç

Bu dosya, CoreMusic ELECTRONICS platformunun tüm donanım, firmware, driver, DSP ve elektronik mimarisinin ana navigasyon noktasıdır.

---

## 2. Quick Reference

| İhtiyaç | İlk Adım |
|---------|----------|
| DSP Engine | [[dsp/index]] |
| Driver Framework | [[drivers/index]] |
| Amplifier | [[amplifier/index]] |
| Hardware Design | [[hardware/index]] |
| Firmware | [[firmware/index]] |
| Software Architecture | [[electronic/software-architecture]] |
| Service Architecture | [[electronic/service-architecture]] |
| Device Ecosystem | [[electronic/device-ecosystem]] |
| Development Workflow | [[electronic/development-workflow]] |
| Electronics Diagrams | [[diagrams/electronics-diagrams]] |

---

## 3. Electronics Katmanları

```
CoreMusic ELECTRONICS

├── Hardware Layer        ← [[hardware/index]]
├── Firmware Layer        ← [[firmware/index]]
├── Driver Layer          ← [[drivers/index]]
├── DSP Engine Layer      ← [[dsp/index]]
├── Amplifier Layer       ← [[amplifier/index]]
├── Software Architecture ← [[electronic/software-architecture]]  ← YENİ v2.0
├── Service Architecture  ← [[electronic/service-architecture]]   ← YENİ v2.0
├── Device Ecosystem      ← [[electronic/device-ecosystem]]       ← YENİ v2.0
└── Middleware Layer      ← architecture/06-audio/
```

---

## 4. Device Families

| Aile | Cihaz Sayısı | Kapsam | Referans |
|------|-------------|--------|----------|
| Home Audio | 7 | Ev ses sistemleri | [[electronic/device-ecosystem]]#3.1 |
| Car Audio | 5 | Araç içi ses | [[electronic/device-ecosystem]]#3.2 |
| Professional Audio | 6 | Stüdyo, broadcast, live | [[electronic/device-ecosystem]]#3.3 |
| Embedded Audio | 4 | Raspberry Pi, ARM | [[electronic/device-ecosystem]]#3.4 |
| Development Boards | — | Geliştirme kartları | [[hardware/index]]#dev-boards |

**Toplam:** 22 cihaz, 4 aile

---

## 5. Donanım Bileşenleri

| Bileşen | Kategori | Referans |
|---------|----------|----------|
| DAC (PCM3168A) | Ses çevirici | [[hardware/index]]#dac |
| ADC | Ses çevirici | [[hardware/index]]#adc |
| DSP (XMOS XU316) | İşlemci | [[dsp/index]]#dsp-hardware |
| Class AB Amplifier | Güçlendirici | [[amplifier/index]]#class-ab |
| Class D Amplifier | Güçlendirici | [[amplifier/index]]#class-d |
| USB Audio Controller | Haberleşme | [[drivers/index]]#usb |
| Virtual Audio Driver | Sanal sürücü | [[drivers/index]]#virtual |

---

## 6. Yazılım Bileşenleri

| Bileşen | Katman | Referans |
|---------|--------|----------|
| DSP Pipeline | İşleme | [[dsp/index]]#pipeline |
| EQ System | İşleme | [[dsp/index]]#equalizer |
| Crossover Engine | Yönlendirme | [[dsp/index]]#crossover |
| Driver Framework | Sürücü | [[drivers/index]]#framework |
| Firmware Stack | Gömülü | [[firmware/index]]#stack |
| Protection System | Koruma | [[amplifier/index]]#protection |
| Software Architecture (5 katman) | Mimari | [[electronic/software-architecture]] |
| Service Architecture (13 servis) | Servis | [[electronic/service-architecture]] |
| Device Ecosystem (22 cihaz) | Ekosistem | [[electronic/device-ecosystem]] |

---

## 7. ADR Referansları

| ADR | Konu | Electronics İlişkisi |
|-----|------|---------------------|
| [[ADR-017-dsp-hardware-mode]] | DSP hardware mode | XMOS, JUCE, ASIO |
| [[ADR-025-professional-eq-system]] | Professional EQ | 31-band EQ |
| [[ADR-038-8.1-sound-card-chip-selection]] | PCM3168A + XMOS XU316 | DAC + DSP seçimi |
| [[ADR-051-platform-rewrite-from-scratch]] | Sıfırdan platform | Electronics mimarisi |
| [[ADR-061-electronics-architecture]] | Electronics Architecture | L6 Layer |
| [[ADR-062-dsp-pipeline-architecture]] | DSP Pipeline | 15 aşamalı pipeline |
| [[ADR-063-hardware-design-standards]] | Hardware Design | PCB, EMI/EMC |

---

## 8. Cihaz Destek Matrisi

| Cihaz | HW | FW | Driver | DSP | Amplifier |
|-------|----|----|--------|-----|-----------|
| 8+1 Amp (2000W) | ✅ | ✅ | ✅ | ✅ | ✅ |
| 5+1 Amp (2000W) | ✅ | ✅ | ✅ | ✅ | ✅ |
| 2+1 Amp (2000W) | ✅ | ✅ | ✅ | ✅ | ✅ |
| 2+1 Amp (35W) | ✅ | ✅ | ✅ | ✅ | ✅ |
| 2+1 Amp (10W) | ✅ | ✅ | ✅ | ✅ | ✅ |
| 2 Kanal Amp (35W) | ✅ | ✅ | ✅ | ✅ | ✅ |
| 2 Kanal Amp (10W) | ✅ | ✅ | ✅ | ✅ | ✅ |
| USB Audio Interface | ✅ | ✅ | ✅ | ✅ | ❌ |
| DSP Processor | ✅ | ✅ | ✅ | ✅ | ❌ |
| Raspberry Pi HAT | ✅ | ✅ | ✅ | ✅ | Opsiyonel |

---

## 9. OS Destek Matrisi

| OS | ASIO | WASAPI | WDM | ALSA | CoreAudio | Virtual |
|----|------|--------|-----|------|-----------|---------|
| Windows | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ |
| Linux | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ |
| macOS | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ |
| Raspberry Pi | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ |
| Android | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ |
| iOS | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ |

---

## 10. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| DSP | [[architecture/06-audio/index]] | Audio engine entegrasyonu |
| Drivers | [[architecture/07-security/index]] | Driver signing, secure boot |
| Hardware | [[architecture/l0-infrastructure]] | Donanım altyapısı |
| Firmware | [[architecture/10-network/index]] | Ağ haberleşmesi |
| Amplifier | [[dsp/index]] | DSP-amplifier bağlantısı |
| Software | [[electronic/software-architecture]] | 5 katmanlı yazılım mimarisi |
| Service | [[electronic/service-architecture]] | 13 servis mimarisi |
| Ecosystem | [[electronic/device-ecosystem]] | 22 cihaz ekosistemi |

---

## 11. Metadata

| Metrik | Değer |
|--------|-------|
| Version | 2.0.0 |
| Status | Active |
| Sub-directories | 5 (dsp, drivers, amplifier, hardware, firmware) |
| Architecture Files | 3 (software-architecture, service-architecture, device-ecosystem) |
| Total Files | ~43 dosya |
| ADR Coverage | 017, 025, 038, 051, 061, 062, 063 |
| Device Families | 4 (Home, Car, Professional, Embedded) |
| Total Devices | 22 |
| Services | 13 |
| Software Layers | 5 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team · Human Mode · Truth Mode
