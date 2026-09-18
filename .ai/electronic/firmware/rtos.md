---
type: electronic
category: rtos
title: "CoreMusic — RTOS Integration"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — RTOS Integration

**See also:** [[electronic/firmware/index]] · [[ADR-017-dsp-hardware-mode]]

---

## 1. Amaç

RTOS Integration, CoreMusic ELECTRONICS platformunun gerçek zamanlı işletim sistemi entegrasyonunu tanımlar.

---

## 2. RTOS Seçenekleri Karşılaştırma Tablosu (Faz 6 Doğrulaması)

Bu tablo, CoreMusic platformunda RTOS/bare-metal katmanları arasındaki kullanım senaryolarını detaylandırır:

| RTOS | Platform | Kullanım | Avantaj/Dezavantaj |
|------|----------|----------|--------------------|
| FreeRTOS | RPi5 | Embedded Linux | Geniş destek, ancak interrupt latency dalgalı. |
| Xenomai | Linux | Real-time patch | Linux üstü hard-RT desteği. |
| bare-metal | XMOS | Zero-overhead | DSP için en iyi düşük gecikme, ancak yüksek karmaşıklık. |

---

## 3. Görev Öncelikleri

| Görev | Öncelik | Periyot |
|-------|---------|---------|
| Audio DSP | En yüksek | 10µs |
| I2S Transfer | Yüksek | 20µs |
| USB Audio | Yüksek | 100µs |
| Control | Orta | 1ms |
| Monitoring | Düşük | 100ms |

---

## 4. Real-Time Kısıtları

| Kural | Açıklama |
|-------|----------|
| No blocking | Audio thread'de I/O yasak |
| No malloc | Heap allocation yasak |
| Lock-free | Atomic operations |
| Priority inversion | Priority ceiling |

---

## 5. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-017-dsp-hardware-mode]] | DSP hardware |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
