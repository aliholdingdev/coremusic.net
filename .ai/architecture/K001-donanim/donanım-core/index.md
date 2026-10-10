---
type: architecture
category: layer-subindex
title: "K001 · donanım-core — Donanım Çekirdeği (Sinyal Zinciri)"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# donanım-core — Donanım Çekirdeği (Sinyal Zinciri)

> Üst indeks: `[[../index]]` · ADR: `ADR-061` (L6 mimari) · `ADR-062` (DSP pipeline) · `ADR-064` (platform L0-L6)

## §1 Kapsam
K001'in çekirdek sinyal zinciri ve platform tanımı — ADR-061'in 3 seviyeli hiyerarşisi:
**Sistem → Kart/Modül → Blok/Devre**.

## §2 Çekirdek Zincir (disk kanıtı YOK — ADR metni SSOT)

```
USB-C → XMOS XU316 → I2S → DAC AK4458 / ADC PCM3168A → diff-pair → VAS → Output → hoparlör
```
(Kaynak: `.ai/.decisions/accepted/ADR-061-electronics-architecture.md` authority satırı)

| Bileşen | Rol | ADR |
|---|---|---|
| XMOS XU316 | USB audio bridge + DSP host | ADR-061 · ADR-038 |
| AK4458 | DAC (çıkış) | ADR-061 |
| PCM3168A | ADC (çıkış — 8 kanal) | ADR-038 |
| Class AB amplifikatör | 8→50W, 120dB+, 12-24V boost, hibrit MCU | ADR-089 |
| Kanal varyantları | mono → 8+1 ürün ailesi | ADR-090 |

## §3 Bağımlılık
- **Alt:** `[[../donanım-kernel]]` (firmware/driver sınırı) · K000.
- **Üst (çağıran):** `[[../donanım-api]]` · K002 · A5 (K016-K020 bileşen grubu bu çekirdeği besler).

## §4 Durum
**PLANNED** — tümü ADR/şartname düzeyi; PCB/BOM/ölçüm kanıtı A5 (K019 PCB, K020 Üretim) katmanlarındadır
ve o katmanlar da PLANNED.

## §5 Risk / Not
- L6↔K eş dönüşümü `adlandirma-kurali.md §7.3` gereği **otomatik yapılmaz** (ADR-061) → bu dosya K uzayında kalır.
- Ölçüm (THD, gürültü bütçesi) verisi YOK → `⚠️ VERIFICATION REQUIRED`.
