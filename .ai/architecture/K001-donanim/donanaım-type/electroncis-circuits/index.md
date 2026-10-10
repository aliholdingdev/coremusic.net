---
type: architecture
category: layer-subindex
title: "K001 · donanaım-type/electroncis-circuits — Devre Tipleri"
date: 2026-10-10
status: active
version: 1.0.0
authority: SSOT
---

# electroncis-circuits — Devre Tipleri

> Üst indeks: `[[../index]]` · K001: `[[../../index]]` · ADR: `ADR-061` (Blok/Devre 3. seviye) · `ADR-063` (PCB kural seti)

## §1 Kapsam
ADR-061'in 3 seviyeli hiyerarşisinin 3. kademesi (**Sistem → Kart/Modül → Blok/Devre**):
tipik ses-yolu devre blokları. Tek alt dal: `[[amfiliper/index|amfiliper]]` (yazım mevcut yapı — değiştirilmedi).

## §2 Devre Blokları (ADR-061 blok diyagramı — spec düzeyi)

| Blok | Sinyal yolu içindeki yeri |
|---|---|
| USB-C girişi | sistem girişi |
| XMOS XU316 | dijital köprü |
| I2S bağlantısı | dijital ses veri yolu |
| DAC AK4458 / ADC PCM3168A | dönüşüm |
| diff-pair | diferansiyel analog |
| VAS (voltage amplifier stage) | öncül amplifikasyon |
| Output → hoparlör | güç çıkışı |

## §3 Bağımlılık
- **Alt:** K000 (zemin) — matris §2'de K001'in tek bağımlılığı.
- **Üst:** `[[../index]]` · `[[../../donanım-core]]` · K019 (PCB) / K016 (Amplifikatör) A5 grupları.

## §4 Durum
**PLANNED (spec)** — devre tanımı yalnız ADR metninde; PCB layout/gerçekleşim K019 katmanında (o da PLANNED).

## §5 Risk / Not
- Bu dosya devre **içeriği üretmez** (ikinci yazım çelişkisi riski) — yetkili metin ADR-061/063'tür.
- ADR-063'ün kural sayıları (EMC/AES17 test maddeleri) bu görevde okunmadı → `UNKNOWN`.
