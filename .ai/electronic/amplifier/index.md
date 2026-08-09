---
type: system
category: amplifier-architecture
title: "CoreMusic Electronics — Amplifier Architecture Index"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic Electronics — Amplifier Architecture

**Zorunlu Bağlantılar:** [[electronic/index]] · [[brain.md]] · [[electronic/dsp/index]]

---

## 1. Amaç

Amplifier Architecture, CoreMusic ELECTRONICS platformunun tüm amplifikatör tasarımını, koruma sistemlerini, güç yönetimini ve soğutma altyapısını kapsar.

---

## 2. Amplifier Tipleri

| Tip | Dosya | Güç | Kullanım |
|-----|-------|-----|----------|
| Class AB | [[class-ab]] | 10W-2000W | Ana amplifikatör |
| Class D | [[class-d]] | 10W-2000W | Yüksek verimlilik |
| Class A | Gelecek | Düşük | Referans ses |
| Hybrid | Gelecek | Karışık | Yüksek performans |

---

## 3. Cihaz Amplifier Ailesi

| Cihaz | Kanal | Güç | Ohm | Durum |
|-------|-------|-----|-----|-------|
| 8+1 Amplifier | 8+1 | MAX 2000W | 8Ω | ✅ Varsayılan |
| 5+1 Amplifier | 5+1 | MAX 2000W | 8Ω | ✅ |
| 2+1 Amplifier | 2+1 | MAX 2000W | 8Ω | ✅ |
| 2+1 Amplifier | 2+1 | 35W | 8Ω | ✅ |
| 2+1 Amplifier | 2+1 | 10W | 8Ω | ✅ |
| 2 Kanal Amplifier | 2 | 35W | 8Ω | ✅ |
| 2 Kanal Amplifier | 2 | 10W | 8Ω | ✅ |

**Not:** CoreMusic'in dahili amfisi default 8+1'dir.

---

## 4. 8.1 Surround Kanal Yapısı

```
Front Left      (20Hz - 20kHz)
Front Right     (20Hz - 20kHz)
Center          (100Hz - 8kHz)
Rear Left       (100Hz - 16kHz)
Rear Right      (100Hz - 16kHz)
Side Left       (100Hz - 16kHz)
Side Right      (100Hz - 16kHz)
Height          (200Hz - 16kHz)
Subwoofer LFE   (20Hz - 120Hz)
```

Bass Management: Linkwitz-Riley 4. nesil, crossover 80Hz.

---

## 5. Amplifier Bileşenleri

| Bileşen | Dosya | Kapsam |
|---------|-------|--------|
| Class AB Tasarım | [[class-ab]] | Gain stage, bias, thermal |
| Class D Tasarım | [[class-d]] | PWM, MOSFET, filter |
| Koruma Sistemleri | [[protection]] | Kısa devre, termal, DC offset |
| PSU + Soğutma | [[psu-cooling]] | Güç kaynağı, fan, heatsink |

---

## 6. Koruma Sistemleri

| Koruma | Açıklama | Kritiklik |
|--------|----------|-----------|
| Kısa Devre | Çıkış kısa devresi koruması | CRITICAL |
| Aşırı Akım | Maksimum akım sınırı | CRITICAL |
| Aşırı Gerilim | Maksimum gerilim sınırı | HIGH |
| Ters Polarite | Ters bağlanma koruması | HIGH |
| Termal | Isı sensörü + fan kontrolü | HIGH |
| DC Offset | >0.5V DC offset koruma rölesi | CRITICAL |
| Hoparlör Koruma | Soft start/stop | MEDIUM |

Detay: [[protection]]

---

## 7. Güç Seviyeleri

| Seviye | Kullanım | Ohm |
|--------|----------|-----|
| 10W | Desktop, Nearfield | 8Ω |
| 35W | Small Room | 8Ω |
| 100W | Medium Room | 8Ω |
| 250W | Large Room | 8Ω |
| 500W | Professional | 8Ω |
| 1000W | Concert | 8Ω |
| 2000W | MAX (8+1) | 8Ω |

---

## 8. Analog Amplifier Tasarım Kuralları

| Kural | Açıklama |
|-------|----------|
| Düşük Gürültü | <1mV input noise |
| Yüksek SNR | >100dB |
| Düşük THD+N | <%0.01 |
| Geniş Bant | 20Hz-20kHz ±0.5dB |
| Kararlı | Thermally stable |
| Korumalı | Tüm koruma devreleri |

---

## 9. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | PCM3168A + XMOS XU316 |
| [[ADR-017-dsp-hardware-mode]] | DSP hardware mode |

---

## 10. Çapraz Referanslar

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| Amplifier | [[electronic/dsp/index]] | DSP çıkışı |
| Amplifier | [[electronic/hardware/index]] | PCB tasarımı |
| Amplifier | [[electronic/drivers/index]] | Driver çıkışı |
| Amplifier | [[architecture/07-security/index]] | Koruma sistemleri |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
