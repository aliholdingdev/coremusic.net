---
type: electronic
category: dsp-effects
title: "CoreMusic — Audio Effects"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Audio Effects

**See also:** [[electronic/dsp/index]] · [[ADR-017-dsp-hardware-mode]]

---

## 1. Amaç

Audio Effects, CoreMusic DSP Engine'deki mekânsal efektleri (reverb, delay, echo) ve oda düzeltme mekanizmalarını tanımlar.

---

## 2. Reverb

Mekânsal yankı efekti.

### Reverb Modları

| Mod | Kullanım | Room Size |
|-----|----------|-----------|
| Geniş Konser | Büyük mekan | 5000m³ |
| Düğün Salonu | Orta mekan | 2000m³ |
| Oda | Küçük mekan | 100m³ |
| Stüdyo | Profesyonel | 50m³ |
| Custom | Özelleştirilebilir | Ayarlanabilir |

### Reverb Parametreleri

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Room Size | 0 - 100% | 30% |
| Decay Time | 0.1s - 10s | 2.0s |
| Pre-delay | 0ms - 100ms | 20ms |
| Damping | 0 - 100% | 50% |
| Mix (Wet/Dry) | 0% - 100% | 20% |

---

## 3. Delay

Gecikme efekti.

### Delay Parametreleri

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Delay Time | 1ms - 2000ms | 250ms |
| Feedback | 0% - 95% | 40% |
| Mix | 0% - 100% | 20% |
| Ping-Pong | On/Off | Off |
| Sync | BPM-sync | Off |

---

## 4. Echo

Tekrarlayan yankı efekti.

### Echo Parametreleri

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Echo Time | 50ms - 500ms | 200ms |
| Decay | 1 - 10 | 3 |
| Mix | 0% - 100% | 15% |

---

## 5. Stereo Width

Stereo genişletme/sıkıştırma.

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Width | 0% (mono) - 200% (ultra-wide) | 100% |
| Center | 0% - 100% | 50% |

---

## 6. Room Correction

Oda akustik düzeltmesi.

### Room Correction Parametreleri

| Parametre | Kullanım |
|-----------|----------|
| Measurement Mic | Oda ölçümü |
| Frequency Response | Frekans yanıtı |
| Phase Response | Faz yanıtı |
| Correction Filter | FIR tabanlı düzeltme |
| Target Curve | Hedef eğri |

---

## 7. FX Pipeline

```
Input Signal
    ↓
Reverb (mekânsal)
    ↓
Delay (gecikme)
    ↓
Echo (yankı)
    ↓
Stereo Width (genişlik)
    ↓
Room Correction (oda düzeltme)
    ↓
Output Signal
```

---

## 7. AUX/USB Çıkışından FX

### 7.1 Çıkış Tipleri

| Çıkış | Protokol | FX Desteği | Kullanım |
|-------|----------|------------|----------|
| **Hoparlör (ASIO)** | ASIO/WASAPI | Tam FX | Ana ses çıkışı |
| **AUX Output** | Analog (3.5mm/6.3mm) | Reverb, Delay, Chorus | Harici amfi/speaker |
| **USB Output** | USB Audio Class 2.0 | Tam FX | USB DAC, harici cihaz |
| **Bluetooth** | BLE/A2DP | Sınırlı FX | Kablosuz hoparlör |
| **WiFi** | mDNS/DLNA | Tam FX | Multi-room |

### 7.2 FX Routing

```
DSP Chain Output
  → Mixer (Volume, Pan)
    → FX Bus (Reverb, Delay, Chorus)
      → AUX Output (analog)
      → USB Output (dijital)
      → Bluetooth (kablosuz)
      → WiFi (multi-room)
```

### 7.3 Samsung Galaxy J7 Tarzı Geniş Konser Efekti

CoreMusic, Samsung Galaxy J7 2016'daki "Geniş Konser Salonu" efektine benzer mekânsal ses deneyimi sunar.

| Efekt | Samsung J7 | CoreMusic |
|-------|------------|-----------|
| **Geniş Konser** | 3.0s decay | 3.0s decay, 5000m³ room |
| **Düğün Salonu** | 2.0s decay | 2.0s decay, 2000m³ room |
| **Oda** | 0.8s decay | 0.8s decay, 100m³ room |
| **Stüdyo** | 0.5s decay | 0.5s decay, 50m³ room |

**Kullanım Senaryoları:**
- Müzik dinlerken konser salonu atmosferi hissetme
- Düğün salonu gibi mekanların akustiğini yeniden oluşturma
- Stüdyo kayıtlarında profesyonel reverb ekleme
- AUX/USB çıkışından efektli ses aktarımı

---

## 8. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-017-dsp-hardware-mode]] | DSP hardware |
| [[ADR-025-professional-eq-system]] | EQ sistemi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
