---
type: system
category: electronic-architecture
title: "CoreMusic Electronics â€” Master Index"
date: 2026-08-09
updated: 2026-08-10
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team Â· Human Mode Â· Truth Mode
---

# CoreMusic Electronics â€” Master Index

**Zorunlu BaÄŸlantÄ±lar:** [[CLAUDE.md]] Â· [[AGENTS.md]] Â· [[brain.md]] Â· [[index.md]]

---

## 1. AmaÃ§

Bu dosya, CoreMusic ELECTRONICS platformunun tÃ¼m donanÄ±m, firmware, driver, DSP ve elektronik mimarisinin ana navigasyon noktasÄ±dÄ±r.

---

## 2. Quick Reference

| Ä°htiyaÃ§ | Ä°lk AdÄ±m |
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

## 3. Electronics KatmanlarÄ±

```
CoreMusic ELECTRONICS

â”œâ”€â”€ Hardware Layer        â† [[hardware/index]]
â”œâ”€â”€ Firmware Layer        â† [[firmware/index]]
â”œâ”€â”€ Driver Layer          â† [[drivers/index]]
â”œâ”€â”€ DSP Engine Layer      â† [[dsp/index]]
â”œâ”€â”€ Amplifier Layer       â† [[amplifier/index]]
â”œâ”€â”€ Software Architecture â† [[electronic/software-architecture]]  â† YENÄ° v2.0
â”œâ”€â”€ Service Architecture  â† [[electronic/service-architecture]]   â† YENÄ° v2.0
â”œâ”€â”€ Device Ecosystem      â† [[electronic/device-ecosystem]]       â† YENÄ° v2.0
â””â”€â”€ Middleware Layer      â† architecture/06-audio/
```

---

## 4. Device Families

| Aile | Cihaz SayÄ±sÄ± | Kapsam | Referans |
|------|-------------|--------|----------|
| Home Audio | 7 | Ev ses sistemleri | [[electronic/device-ecosystem]]#3.1 |
| Car Audio | 5 | AraÃ§ iÃ§i ses | [[electronic/device-ecosystem]]#3.2 |
| Professional Audio | 6 | StÃ¼dyo, broadcast, live | [[electronic/device-ecosystem]]#3.3 |
| Embedded Audio | 4 | Raspberry Pi, ARM | [[electronic/device-ecosystem]]#3.4 |
| Development Boards | â€” | GeliÅŸtirme kartlarÄ± | [[hardware/index]]#dev-boards |

**Toplam:** 22 cihaz, 4 aile

---

## 5. DonanÄ±m BileÅŸenleri

| BileÅŸen | Kategori | Referans |
|---------|----------|----------|
| DAC (PCM3168A) | Ses Ã§evirici | [[hardware/index]]#dac |
| ADC | Ses Ã§evirici | [[hardware/index]]#adc |
| DSP (XMOS XU316) | Ä°ÅŸlemci | [[dsp/index]]#dsp-hardware |
| Class AB Amplifier | GÃ¼Ã§lendirici | [[amplifier/index]]#class-ab |
| Class D Amplifier | GÃ¼Ã§lendirici | [[amplifier/index]]#class-d |
| USB Audio Controller | HaberleÅŸme | [[drivers/index]]#usb |
| Virtual Audio Driver | Sanal sÃ¼rÃ¼cÃ¼ | [[drivers/index]]#virtual |

---

## 6. YazÄ±lÄ±m BileÅŸenleri

| BileÅŸen | Katman | Referans |
|---------|--------|----------|
| DSP Pipeline | Ä°ÅŸleme | [[dsp/index]]#pipeline |
| EQ System | Ä°ÅŸleme | [[dsp/index]]#equalizer |
| Crossover Engine | YÃ¶nlendirme | [[dsp/index]]#crossover |
| Driver Framework | SÃ¼rÃ¼cÃ¼ | [[drivers/index]]#framework |
| Firmware Stack | GÃ¶mÃ¼lÃ¼ | [[firmware/index]]#stack |
| Protection System | Koruma | [[amplifier/index]]#protection |
| Software Architecture (5 katman) | Mimari | [[electronic/software-architecture]] |
| Service Architecture (13 servis) | Servis | [[electronic/service-architecture]] |
| Device Ecosystem (22 cihaz) | Ekosistem | [[electronic/device-ecosystem]] |

---

## 7. ADR ReferanslarÄ±

| ADR | Konu | Electronics Ä°liÅŸkisi |
|-----|------|---------------------|
| [[ADR-017-dsp-hardware-mode]] | DSP hardware mode | XMOS, JUCE, ASIO |
| [[ADR-025-professional-eq-system]] | Professional EQ | 31-band EQ |
| [[ADR-038-8.1-sound-card-chip-selection]] | PCM3168A + XMOS XU316 | DAC + DSP seÃ§imi |
| [[ADR-061-electronics-architecture]] | Electronics Architecture | L6 Layer |
| [[ADR-062-dsp-pipeline-architecture]] | DSP Pipeline | 15 aÅŸamalÄ± pipeline |
| [[ADR-063-hardware-design-standards]] | Hardware Design | PCB, EMI/EMC |

---

## 8. Cihaz Destek Matrisi

| Cihaz | HW | FW | Driver | DSP | Amplifier |
|-------|----|----|--------|-----|-----------|
| CM-71-AB (7.1 Class AB) | âœ… | âœ… | âœ… | âœ… | âœ… |
| CM-51-AB (5.1 Class AB) | âœ… | âœ… | âœ… | âœ… | âœ… |
| CM-21-AB (2.1 Class AB) | âœ… | âœ… | âœ… | âœ… | âœ… |
| CM-10-AB (Mono Class AB) | âœ… | âœ… | âœ… | âœ… | âœ… |
| CM-71-D (7.1 Class D) | âœ… | âœ… | âœ… | âœ… | âœ… |
| CM-51-D (5.1 Class D) | âœ… | âœ… | âœ… | âœ… | âœ… |
| CM-21-D (2.1 Class D) | âœ… | âœ… | âœ… | âœ… | âœ… |
| CM-10-D (Mono Class D) | âœ… | âœ… | âœ… | âœ… | âœ… |
| USB Audio Interface | âœ… | âœ… | âœ… | âœ… | âŒ |
| DSP Processor | âœ… | âœ… | âœ… | âœ… | âŒ |
| Raspberry Pi HAT | âœ… | âœ… | âœ… | âœ… | Opsiyonel |

---

## 9. OS Destek Matrisi

| OS | ASIO | WASAPI | WDM | ALSA | CoreAudio | Virtual |
|----|------|--------|-----|------|-----------|---------|
| Windows | âœ… | âœ… | âœ… | âŒ | âŒ | âœ… |
| Linux | âŒ | âŒ | âŒ | âœ… | âŒ | âœ… |
| macOS | âŒ | âŒ | âŒ | âŒ | âœ… | âœ… |
| Raspberry Pi | âŒ | âŒ | âŒ | âœ… | âŒ | âœ… |
| Android | âŒ | âŒ | âŒ | âœ… | âŒ | âœ… |
| iOS | âŒ | âŒ | âŒ | âŒ | âœ… | âœ… |

---

## 10. Ã‡apraz Referanslar

| BÃ¶lÃ¼m | Hedef | Ä°liÅŸki |
|-------|-------|--------|
| DSP | [[architecture/k0-k5-software/k3-audio-engine]] | Audio engine entegrasyonu |
| Drivers | [[architecture/k6-k7-security/k6-security]] | Driver signing, secure boot |
| Hardware | [[architecture/k0-k5-software/k0-os-layer]] | DonanÄ±m altyapÄ±sÄ± |
| Firmware | [[architecture/10-network/index]] | AÄŸ haberleÅŸmesi |
| Amplifier | [[dsp/index]] | DSP-amplifier baÄŸlantÄ±sÄ± |
| Software | [[electronic/software-architecture]] | 5 katmanlÄ± yazÄ±lÄ±m mimarisi |
| Service | [[electronic/service-architecture]] | 13 servis mimarisi |
| Ecosystem | [[electronic/device-ecosystem]] | 22 cihaz ekosistemi |

---

## 11. Metadata

| Metrik | DeÄŸer |
|--------|-------|
| Version | 2.0.0 |
| Status | Active |
| Sub-directories | 5 (dsp, drivers, amplifier, hardware, firmware) |
| Architecture Files | 3 (software-architecture, service-architecture, device-ecosystem) |
| Total Files | ~43 dosya |
| ADR Coverage | 017, 025, 038, 061, 062, 063 |
| Device Families | 4 (Home, Car, Professional, Embedded) |
| Total Devices | 22 |
| Services | 13 |
| Software Layers | 5 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team Â· Human Mode Â· Truth Mode

