---
type: system
category: amplifier-architecture
title: "CoreMusic Electronics â€” Amplifier Architecture Index"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team Â· Human Mode Â· Truth Mode
---

# CoreMusic Electronics â€” Amplifier Architecture

**Zorunlu BaÄŸlantÄ±lar:** [[electronic/index]] Â· [[brain.md]] Â· [[electronic/dsp/index]]

---

## 1. AmaÃ§

Amplifier Architecture, CoreMusic ELECTRONICS platformunun tÃ¼m amplifikatÃ¶r tasarÄ±mÄ±nÄ±, koruma sistemlerini, gÃ¼Ã§ yÃ¶netimini ve soÄŸutma altyapÄ±sÄ±nÄ± kapsar.

---

## 2. Amplifier Tipleri

| Tip | Dosya | KullanÄ±m |
|-----|-------|----------|
| Class AB | [[class-ab]] | **CoreMusic Ana AmplifikatÃ¶r** â€” high-end ses, stÃ¼dyo |
| Class D | [[class-d]] | Ev cihazÄ± amfi devresi, oda amfi imalatÄ±, test/Ã¶lÃ§Ã¼m |

---

## 3. CoreMusic Amplifier Ailesi

### 3.1 Class AB â€” Ana AmplifikatÃ¶r Serisi (CoreMusic)

| Model | Kanal | GÃ¼Ã§ (8Î©) | Ã‡ip | Fiyat/ch | Stok | KullanÄ±m |
|-------|-------|----------|-----|----------|------|----------|
| CM-71-AB | 7+1 (7.1) | 100W Ã— 7 + 200W Sub | Discrete MJL3281A | ~$15 | âœ… | **CoreMusic Ana Amfi** |
| CM-51-AB | 5+1 (5.1) | 100W Ã— 5 + 200W Sub | Discrete MJL3281A | ~$15 | âœ… | Orta segment |
| CM-21-AB | 2+1 (2.1) | 100W Ã— 2 + 200W Sub | Discrete MJL3281A | ~$15 | âœ… | Stereo + Sub |
| CM-20-AB | 2 (Stereo) | 100W Ã— 2 | Discrete MJL3281A | ~$15 | âœ… | Stereo |
| CM-10-AB | 1 (Mono) | 100W | Discrete MJL3281A | ~$15 | âœ… | Mono test |
| CM-07-AB | 1 (Mono) | 50W | TDA7294 | ~$2.14 | âœ… | Orta gÃ¼Ã§ |
| CM-06-AB | 1 (Mono) | 38W | LM3886 | ~$4.34 | âœ… | Orta gÃ¼Ã§ |
| CM-05-AB | 1 (Mono) | 20W | LM1875 | ~$4.22 | âœ… | DÃ¼ÅŸÃ¼k gÃ¼Ã§ |

### 3.2 Class D â€” Ev CihazÄ± / Oda Ä°malatÄ± / Test Serisi

| Model | Kanal | GÃ¼Ã§ (8Î©) | Ã‡ip | Fiyat/ch | Stok | KullanÄ±m |
|-------|-------|----------|-----|----------|------|----------|
| CM-71-D | 7+1 (7.1) | 50W Ã— 7 + 100W Sub | TPA3255 | ~$4.13 | âš ï¸ | Ev cihazÄ±, oda |
| CM-51-D | 5+1 (5.1) | 50W Ã— 5 + 100W Sub | TPA3255 | ~$4.13 | âš ï¸ | Ev cihazÄ±, oda |
| CM-21-D | 2+1 (2.1) | 50W Ã— 2 + 100W Sub | TPA3255 | ~$4.13 | âš ï¸ | Ev cihazÄ±, test |
| CM-20-D | 2 (Stereo) | 50W Ã— 2 | TPA3255 | ~$4.13 | âš ï¸ | Ev cihazÄ±, test |
| CM-10-D | 1 (Mono) | 50W | TPA3250 | ~$2.35 | âœ… | Tek kanal test |
| CM-05-D | 2 (Stereo) | 30W Ã— 2 | TPA3118D2 | ~$0.60 | âœ… | DÃ¼ÅŸÃ¼k gÃ¼Ã§ ev |
| CM-03-D | 2 (Stereo) | 15W Ã— 2 | TPA3130D2 | ~$0.50 | âœ… | MasaÃ¼stÃ¼, kulaklÄ±k |
| CM-01-D | 1 (Mono) | 5W | TPA3110D2 | ~$0.50 | âœ… | Mini amp |

### 3.3 Class D Chip SeÃ§im Matrisi

| Ã‡ip | GÃ¼Ã§@8Î© | GÃ¼Ã§@4Î© | Supply | Fiyat (1ku) | Stok | KullanÄ±m |
|-----|--------|--------|--------|-------------|------|----------|
| **TPA3255** | 185W | 315W | 18-53.5V | ~$4.13 | âš ï¸ | Profesyonel |
| **TPA3251** | 150W | 175W | 12-38V | ~$3.35 | âœ… | Orta gÃ¼Ã§ |
| **TPA3250** | 70W | 130W | 12-36V | ~$2.35 | âœ… | BÃ¼tÃ§e |
| **TPA3116D2** | â€” | 50W | 4.5-26V | ~$0.70 | âœ… | 35-50W ev |
| **TPA3118D2** | 30W | 60W | 4.5-26V | ~$0.60 | âœ… | 15-30W ev |
| **TPA3130D2** | 15W | 30W | 4.5-26V | ~$0.50 | âœ… | 5-15W masaÃ¼stÃ¼ |
| **TPA3110D2** | 10W | 15W | 4.5-26V | ~$0.50 | âœ… | Mini amp |

### 3.4 Class AB Chip SeÃ§im Matrisi

| Ã‡ip | GÃ¼Ã§@8Î© | GÃ¼Ã§@4Î© | Supply | Fiyat (1ku) | Stok | KullanÄ±m |
|-----|--------|--------|--------|-------------|------|----------|
| **LM3886** | 38W | 68W | Â±20-94V | ~$3-5 | âœ… | 35-50W high-end |
| **LM1875** | 20W | 30W | Â±16-60V | ~$2-3 | âœ… | 10-20W ev |
| **TDA7294** | 100W | 100W | Â±40V | ~$3-5 | âœ… | 50-100W ev |
| **MJL3281A/MJL1302A** | 100W+ | 200W+ | Â±42V | ~$2.5/adet | âœ… | Discrete, high-end |

**Not:** CoreMusic'in ana amfisi Class AB CM-71-AB (7.1) modelidir. Class D modelleri ev cihazÄ± devrelerinde, oda amfi imalatÄ±nda ve test/Ã¶lÃ§Ã¼m amaÃ§lÄ± kullanÄ±lÄ±r.

---

## 4. 7.1 Surround Kanal YapÄ±sÄ±

```
Front Left      (20Hz - 20kHz)
Front Right     (20Hz - 20kHz)
Center          (100Hz - 8kHz)
Surround Left   (100Hz - 16kHz)
Surround Right  (100Hz - 16kHz)
Rear Left       (100Hz - 16kHz)
Rear Right      (100Hz - 16kHz)
Subwoofer LFE   (20Hz - 120Hz)
```

Bass Management: Linkwitz-Riley 4. nesil, crossover 80Hz.

---

## 5. Amplifier BileÅŸenleri

| BileÅŸen | Dosya | Kapsam |
|---------|-------|--------|
| Class AB TasarÄ±m | [[class-ab]] | Gain stage, bias, thermal |
| Class D TasarÄ±m | [[class-d]] | PWM, MOSFET, filter |
| Koruma Sistemleri | [[protection]] | KÄ±sa devre, termal, DC offset |
| PSU + SoÄŸutma | [[psu-cooling]] | GÃ¼Ã§ kaynaÄŸÄ±, fan, heatsink |

---

## 6. Koruma Sistemleri

| Koruma | AÃ§Ä±klama | Kritiklik |
|--------|----------|-----------|
| KÄ±sa Devre | Ã‡Ä±kÄ±ÅŸ kÄ±sa devresi korumasÄ± | CRITICAL |
| AÅŸÄ±rÄ± AkÄ±m | Maksimum akÄ±m sÄ±nÄ±rÄ± | CRITICAL |
| AÅŸÄ±rÄ± Gerilim | Maksimum gerilim sÄ±nÄ±rÄ± | HIGH |
| Ters Polarite | Ters baÄŸlanma korumasÄ± | HIGH |
| Termal | IsÄ± sensÃ¶rÃ¼ + fan kontrolÃ¼ | HIGH |
| DC Offset | >0.5V DC offset koruma rÃ¶lesi | CRITICAL |
| HoparlÃ¶r Koruma | Soft start/stop | MEDIUM |

Detay: [[protection]]

---

## 7. GÃ¼Ã§ Seviyeleri ve Chip EÅŸleÅŸmesi

| Seviye | Class AB Ã‡ip | Class D Ã‡ip | KullanÄ±m | Ohm |
|--------|-------------|-------------|----------|-----|
| **5W** | â€” | TPA3110D2 | Mini amp, masaÃ¼stÃ¼ | 8Î© |
| **10W** | LM1875 (30W@4Î©) | TPA3130D2 | MasaÃ¼stÃ¼, kulaklÄ±k | 8Î© |
| **15W** | LM1875 | TPA3130D2 | KÃ¼Ã§Ã¼k oda | 8Î© |
| **20W** | LM1875 | TPA3118D2 | Ev, kitaplÄ±k | 8Î© |
| **30W** | LM3886 (68W@4Î©) | TPA3118D2 | Orta oda | 8Î© |
| **35W** | LM3886 | TPA3116D2 | Orta oda | 8Î© |
| **50W** | LM3886 / TDA7294 | TPA3116D2 / TPA3250 | BÃ¼yÃ¼k oda | 8Î© |
| **100W** | Discrete (MJL3281A) | TPA3255 | **CoreMusic standart** | 8Î© |
| **150W** | Discrete | TPA3251 | Salon | 8Î© |
| **185W** | Discrete | TPA3255 | Profesyonel | 8Î© |
| **250W** | Discrete (paralel) | TPA3255 (BTL) | BÃ¼yÃ¼k salon | 4Î© |
| **500W** | Discrete (multi-pair) | TPA3255 (PBTL) | Konser | 2Î© |

---

## 8. Amplifier TasarÄ±m KurallarÄ±

### 8.1 Genel Kurallar

| # | Kural | AÃ§Ä±klama | Ä°hlal Sonucu |
|---|-------|----------|-------------|
| 1 | **GÃ¼Ã§ SÄ±ralamasÄ±** | Ã–nce DSP, sonra analog, en son amplifier | Pop-noise, hasar |
| 2 | **GÃ¼Ã§ GiriÅŸ** | 12V-24V DC + Boost Converter â†’ Â±42V | DÃ¼ÅŸÃ¼k Ã§Ä±kÄ±ÅŸ gÃ¼cÃ¼ |
| 3 | **PCB Katman** | Minimum 4 katman (Class D iÃ§in zorunlu) | EMI, gÃ¼rÃ¼ltÃ¼ |
| 4 | **Topraklama** | Star ground, analog/dijital ayrÄ±m | Ground loop |
| 5 | **Koruma** | DC offset, over-current, termal zorunlu | HoparlÃ¶r hasarÄ± |
| 6 | **SoÄŸutma** | Heatsink zorunlu (Class AB), opsiyonel (Class D) | Termal kapanma |
| 7 | **GiriÅŸ EmpedansÄ±** | 10kÎ©-47kÎ© arasÄ± | Empedans uyumsuzluÄŸu |
| 8 | **Ã‡Ä±kÄ±ÅŸ EmpedansÄ±** | <0.1Î© (Class AB), <0.01Î© (Class D) | DÃ¼ÅŸÃ¼k damping factor |
| 9 | **Filtre** | Class D iÃ§in LC Ã§Ä±kÄ±ÅŸ filtresi zorunlu | EMI, high-frequency gÃ¼rÃ¼ltÃ¼ |
| 10 | **Decoupling** | Her Ã§ip yakÄ±nÄ±na 100nF + 10ÂµF | Besleme gÃ¼rÃ¼ltÃ¼sÃ¼ |

### 8.2 Class AB KurallarÄ±

| # | Kural | AÃ§Ä±klama |
|---|-------|----------|
| 1 | **Bias AyarÄ±** | Quiescent akÄ±m 50-100mA, NTC termistÃ¶r ile termal tracking |
| 2 | **Output TransistÃ¶r** | MJL3281A/MJL1302A (en gÃ¼venilir) veya 2SC5200/2SA1943 (en ucuz) |
| 3 | **Driver TransistÃ¶r** | MJE15030/MJE15031 (uyumlu gain linearity) |
| 4 | **Negatif Geri Besleme** | %100 negatif feedback, dÃ¼ÅŸÃ¼k THD |
| 5 | **Heatsink** | Minimum 500cmÂ², <1Â°C/W termal direnÃ§ |
| 6 | **GÃ¼Ã§ TransistÃ¶r SayÄ±sÄ±** | 100W@8Î© iÃ§in minimum 2 pair (NPN+PNP) |

### 8.3 Class D KurallarÄ±

| # | Kural | AÃ§Ä±klama |
|---|-------|----------|
| 1 | **Ã‡Ä±kÄ±ÅŸ Filtresi** | 2. basamak LC, 50kHz kesme, 10ÂµH indÃ¼ktÃ¶r |
| 2 | **Diferansiyel GiriÅŸ** | TPA32xx serisi iÃ§in zorunlu |
| 3 | **PCB YerleÅŸimi** | GÃ¼Ã§ yolu kÄ±sa, sinyal yolu uzun |
| 4 | **Bootstrap** | Her kanal iÃ§in 220nF bootstrap kapasitÃ¶rÃ¼ |
| 5 | **Heat Pad** | TPA3255 iÃ§in PowerPAD termal baÄŸ |
| 6 | **AM Avoidance** | Switching frequency ayarÄ± ile AM interference Ã¶nleme |

### 8.4 GÃ¼Ã§ Besleme KurallarÄ±

| # | Kural | AÃ§Ä±klama |
|---|-------|----------|
| 1 | **GiriÅŸ VoltajÄ±** | 12V-24V DC (adaptÃ¶r veya batarya) |
| 2 | **Boost Converter** | LTC3862 (en iyi, %95) veya LM5122 (en ucuz, %94) |
| 3 | **Ripple** | <5mV RMS (LC filtre ile) |
| 4 | **KapasitÃ¶r** | Minimum 10,000ÂµF per rail |
| 5 | **Koruma** | Over-voltage, under-voltage, over-current zorunlu |
| 6 | **Soft-Start** | 2 saniye gecikme, rush current sÄ±nÄ±rlama |
| 7 | **Toroidal** | AC mains gerektiÄŸinde tercih edilir (500VA, %95 verimlilik) |

### 8.5 Koruma KurallarÄ±

| # | Kural | Tetikleme | Aksiyon | SÃ¼re |
|---|-------|-----------|---------|------|
| 1 | **DC Offset** | >Â±0.5V DC | RÃ¶le aÃ§ (hoparlÃ¶r ayÄ±r) | <10ms |
| 2 | **Over-Current** | >max akÄ±m | GÃ¼Ã§ azalt | <100Âµs |
| 3 | **Short-Circuit** | 0Î© Ã§Ä±kÄ±ÅŸ | AnÄ±nda kapanma | <1Âµs |
| 4 | **Termal** | >80Â°C | Fan hÄ±zÄ± â†‘, >100Â°C kapanma | SÃ¼rekli |
| 5 | **Over-Voltage** | >Â±48V DC | Boost kapat | AnÄ±nda |
| 6 | **Under-Voltage** | <10V DC | Amplifier durdur | AnÄ±nda |

### 8.6 Termal YÃ¶netim KurallarÄ±

| # | Kural | AÃ§Ä±klama |
|---|-------|----------|
| 1 | **Heatsink** | Class AB iÃ§in zorunlu, Class D iÃ§in opsiyonel |
| 2 | **Fan EÅŸiÄŸi** | 60Â°C'de fan baÅŸlar |
| 3 | **Kapanma** | >100Â°C'de otomatik kapanma |
| 4 | **Termal SensÃ¶r** | En sÄ±cak noktaya yerleÅŸtir |
| 5 | **Hava AkÄ±ÅŸÄ±** | SÄ±cak â†’ soÄŸuk yÃ¶nÃ¼nde akÄ±ÅŸ |

### 8.7 Test KurallarÄ±

| # | Kural | AÃ§Ä±klama |
|---|-------|----------|
| 1 | **THD+N Ã–lÃ§Ã¼mÃ¼** | 1kHz, 1W, 8Î© yÃ¼k |
| 2 | **SNR Ã–lÃ§Ã¼mÃ¼** | A-waged, 1kHz referans |
| 3 | **Frekans YanÄ±tÄ±** | 20Hz-20kHz, Â±0.5dB tolerans |
| 4 | **Damping Factor** | 100Hz, 8Î© yÃ¼k |
| 5 | **GÃ¼Ã§ Ã‡Ä±kÄ±ÅŸÄ±** | THD+N=%1 noktasÄ±nda |
| 6 | **Termal Test** | 1 saat sÃ¼rekli Ã§alÄ±ÅŸma |

---

## 9. PCB Katman Ã–nerisi

| Katman | KullanÄ±m | Ã–neri |
|--------|----------|-------|
| 2 | DÃ¼ÅŸÃ¼k-orta gÃ¼Ã§ Class AB | âŒ Yetersiz |
| **4** | **Class D + orta gÃ¼Ã§** | âœ… **En iyi seÃ§im (TI Ã¶nerisi)** |
| 6 | KarmaÅŸÄ±k mixed-signal | âœ… Profesyonel |
| 8 | Ultra high-end | âš ï¸ Gereksiz pahalÄ± |

**4 Katman Stack:**
```
Layer 1: Signal routing (top)
Layer 2: Ground plane (analog)
Layer 3: Power plane (Â±42V, 5V, 3.3V)
Layer 4: Signal routing (bottom)
```

**Neden 4 katman?**
- TI'Ä±n Class D iÃ§in Ã¶nerisi
- Analog/dijital ayrÄ±m iÃ§in yeterli
- EMI containment iyi
- Maliyet uygun (6 katmana gÃ¶re %40 daha ucuz)

---

## 10. ADR ReferanslarÄ±

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | PCM3168A + XMOS XU316 |
| [[ADR-017-dsp-hardware-mode]] | DSP hardware mode |

---

## 10. Ã‡apraz Referanslar

| Kaynak | Hedef | Ä°liÅŸki |
|--------|-------|--------|
| Amplifier | [[electronic/dsp/index]] | DSP Ã§Ä±kÄ±ÅŸÄ± |
| Amplifier | [[electronic/hardware/index]] | PCB tasarÄ±mÄ± |
| Amplifier | [[electronic/drivers/index]] | Driver Ã§Ä±kÄ±ÅŸÄ± |
| Amplifier | [[architecture/k6-k7-security/k6-security]] | Koruma sistemleri |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team Â· Human Mode Â· Truth Mode

