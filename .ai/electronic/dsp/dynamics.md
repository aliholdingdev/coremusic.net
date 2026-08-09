---
type: electronic
category: dsp-dynamics
title: "CoreMusic — Dynamics Processing"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Dynamics Processing

**See also:** [[electronic/dsp/index]] · [[ADR-017-dsp-hardware-mode]]

---

## 1. Amaç

Dynamics Processing, ses sinyalinin dinamik aralığını yöneten Compressor, Limiter, Gate ve Expander modüllerini tanımlar.

---

## 2. Compressor

Sinyalin dinamik aralığını sıkıştırır.

### Parametreler

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Threshold | -60dB - 0dB | -20dB |
| Ratio | 1:1 - 20:1 | 4:1 |
| Attack | 0.1ms - 100ms | 10ms |
| Release | 10ms - 1000ms | 100ms |
| Knee | 0dB - 30dB | 6dB |
| Makeup Gain | 0dB - +24dB | 0dB |

### Kullanım Senaryoları

| Senaryo | Ratio | Attack | Release |
|---------|-------|--------|---------|
| Soft | 2:1 | 20ms | 200ms |
| Medium | 4:1 | 10ms | 100ms |
| Hard | 8:1 | 5ms | 50ms |
| Brick Wall | ∞:1 | 0.1ms | 10ms |

---

## 3. Limiter

Sinyalin maksimum seviyesini sınırlar.

### Parametreler

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Ceiling | -10dB - 0dB | -0.3dB |
| Release | Auto - 1000ms | Auto |
| Attack | 0.01ms - 10ms | 0.1ms |

### True Peak Limiting

| Özellik | Değer |
|---------|-------|
| Oversampling | 4x |
| True Peak Detection | ITU-R BS.1770-4 |
| Maximum Level | 0 dBTP |

---

## 4. Noise Gate

Düşük seviyeli gürültüyü temizler.

### Parametreler

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Threshold | -80dB - -20dB | -60dB |
| Attack | 0.1ms - 10ms | 1ms |
| Hold | 0ms - 500ms | 50ms |
| Release | 10ms - 1000ms | 100ms |

---

## 5. Expander

Sinyalin dinamik aralığını genişletir.

### Parametreler

| Parametre | Aralığı | Varsayılan |
|-----------|---------|------------|
| Threshold | -60dB - 0dB | -30dB |
| Ratio | 1:1 - 1:10 | 1:2 |
| Attack | 0.1ms - 100ms | 10ms |
| Release | 10ms - 1000ms | 100ms |

---

## 6. Dynamics Pipeline

```
Input Signal
    ↓
Noise Gate (gürültü temizliği)
    ↓
Compressor (dinamik sıkıştırma)
    ↓
Limiter (maksimum sınır)
    ↓
Output Signal
```

---

## 7. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-017-dsp-hardware-mode]] | DSP hardware |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
