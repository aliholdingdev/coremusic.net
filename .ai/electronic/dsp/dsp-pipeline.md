---
type: electronic
category: dsp-pipeline
title: "CoreMusic — DSP Processing Pipeline"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — DSP Processing Pipeline

**See also:** [[electronic/dsp/index]] · [[ADR-017-dsp-hardware-mode]] · [[ADR-025-professional-eq-system]]

---

## 1. Amaç

DSP Processing Pipeline, ses sinyalinin girişten çıkışa kadar tüm işleme adımlarını tanımlar. Her aşama bağımsız bir modül olarak geliştirilir.

---

## 2. Pipeline Akışı

```
Audio Input (Raw PCM)
    │
    ▼
┌─────────────────┐
│  Input Gain     │  Sinyal seviyesi ayarı
└────────┬────────┘
         ▼
┌─────────────────┐
│  Noise Gate     │  Düşük seviyeli gürültü temizliği
└────────┬────────┘
         ▼
┌─────────────────┐
│  High Pass      │  Düşük frekans kesme
│  Filter         │  (20-200Hz ayarlanabilir)
└────────┬────────┘
         ▼
┌─────────────────┐
│  Low Pass       │  Yüksek frekans kesme
│  Filter         │  (2kHz-20kHz ayarlanabilir)
└────────┬────────┘
         ▼
┌─────────────────┐
│  Parametric EQ  │  Bant bazlı frekans ayarı
│  (31 Band)      │  Frekans + Gain + Q
└────────┬────────┘
         ▼
┌─────────────────┐
│  Graphic EQ     │  Bant bazlı frekans ayarı
│  (31 Band)      │  Sadece Gain
└────────┬────────┘
         ▼
┌─────────────────┐
│  Compressor     │  Dinamik aralık sıkıştırma
└────────┬────────┘
         ▼
┌─────────────────┐
│  Limiter        │  Maksimum seviye sınırı
└────────┬────────┘
         ▼
┌─────────────────┐
│  Loudness       │  perceived loudness
└────────┬────────┘
         ▼
┌─────────────────┐
│  Crossover      │  Frekans dağıtımı
│  (Bass Mgmt)    │  (80Hz Linkwitz-Riley)
└────────┬────────┘
         ▼
┌─────────────────┐
│  Delay          │  Gecikme (kanal senkron)
└────────┬────────┘
         ▼
┌─────────────────┐
│  Reverb         │  Mekânsal efekt
└────────┬────────┘
         ▼
┌─────────────────┐
│  Output Gain    │  Çıkış seviyesi ayarı
└────────┬────────┘
         ▼
┌─────────────────┐
│  Output Routing │  Kanal yönlendirme
│  (7.1 Surround) │  Front/Surround/Rear/Sub
└─────────────────┘
         │
         ▼
Audio Output (Processed PCM)
```

---

## 3. İşleme Sırası

| Sıra | Modül | Parametre | Min | Max | Varsayılan |
|------|-------|-----------|-----|-----|------------|
| 1 | Input Gain | Level | -60dB | +12dB | 0dB |
| 2 | Noise Gate | Threshold | -80dB | -20dB | -60dB |
| 3 | High Pass | Frequency | 20Hz | 200Hz | 80Hz |
| 4 | Low Pass | Frequency | 2kHz | 20kHz | 18kHz |
| 5 | Parametric EQ | Bands | 0 | 31 | 0 |
| 6 | Graphic EQ | Bands | 0 | 31 | 0 |
| 7 | Compressor | Ratio | 1:1 | 20:1 | 4:1 |
| 8 | Limiter | Ceiling | -10dB | 0dB | -0.3dB |
| 9 | Loudness | Target | -24LUFS | -14LUFS | -23LUFS |
| 10 | Crossover | Frequency | 60Hz | 120Hz | 80Hz |
| 11 | Delay | Time | 0ms | 50ms | 0ms |
| 12 | Reverb | Mix | 0% | 100% | 0% |
| 13 | Output Gain | Level | -60dB | +12dB | 0dB |

---

## 4. Zero-Allocation Kuralları

Audio thread'de ❌ yasak:
- `malloc()`, `free()`, `new`, `delete`
- `std::make_shared`, `std::vector` push_back
- I/O blocking
- `throw`, `catch`

✅ İzin:
- Stack tahsisi
- `std::atomic`
- SIMD (SSE2/AVX2/NEON)
- `constexpr`
- `alignas(64)`

---

## 5. Performans Hedefleri

| Metrik | Hedef |
|--------|-------|
| Latency (ASIO) | <10ms |
| Latency (WASAPI) | <20ms |
| CPU (8+1 kanal) | <%15 |
| Bellek | <%50MB |
| Örnekleme | 48kHz (standart) |
| Bit Derinliği | 32-bit float |

---

## 6. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-017-dsp-hardware-mode]] | XMOS, JUCE, ASIO |
| [[ADR-025-professional-eq-system]] | 31-band EQ |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
