---
type: architecture
category: layer-subindex
title: "K001 · donanım-api — Donanım Arayüzü (Spec Düzeyi)"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# donanım-api — Donanım Arayüzü

> Üst indeks: `[[../index]]` · ADR: `ADR-061-electronics-architecture` · `ADR-038-8-1-sound-card-chip-selection`

## §1 Kapsam
K001'in **üst katmanlara (K002 Sürücü) açtığı** donanım arayüzü: kayıt/çıkış kanalı,
I2S/TDM pin sözleşmesi, cihaz kimlikleri. Spec düzeyinde — kod/gerçekleşim bu katmanda değil.

## §2 Arayüz Öğeleri (ADR düzeyi — disk kanıtı YOK)

| Arayüz | Sözleşme | Kaynak |
|---|---|---|
| Dijital ses | **I2S / TDM** — XMOS ↔ DAC/ADC arası veri yolu | ADR-061 (USB-C → XMOS XU316 → I2S → DAC AK4458 / ADC PCM3168A) |
| Analog giriş/çıkış | diff-pair → VAS → Output → hoparlör | ADR-061 blok hiyerarşisi |
| Çip seçimi | DAC **AK4458** · ADC **PCM3168A** · USB bridge **XMOS XU316** | ADR-061 · ADR-038 (8.1 ses kartı) |
| Yalnız referans | PCM5122 **yasak** → PCM3168A/AK4458 öner (AGENTS §17-8) | `.ai/AGENTS.md` §17 |

## §3 Bağımlılık
- **Alt:** `[[../donanım-core]]` · K000 (zemin).
- **Üst (çağıran):** K002 (Sürücü — donanımı programatik kullanır) · K003 (DSP).

## §4 Durum
**PLANNED** — ADR metinleri diskte (`accepted/ADR-061`, `accepted/ADR-038`); pin/seviye/protokol
detayları için ADR-063 (Hardware Design Standards) geçerli; **ölçüm/gerçekleşim kanıtı YOK**.

## §5 Risk / Not
- API imzaları (K002'nin göreceği) yazılmadı → **UNKNOWN** (sürücü katmanı K002'nin işi).
