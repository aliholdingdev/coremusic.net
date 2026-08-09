---
type: electronic
category: pcb-design
title: "CoreMusic — PCB Design Guidelines"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — PCB Design Guidelines

**See also:** [[electronic/hardware/index]] · [[ADR-038-8.1-sound-card-chip-selection]]

---

## 1. Amaç

PCB Design Guidelines, CoreMusic ELECTRONICS platformunun PCB tasarım kurallarını tanımlar.

---

## 2. Katman Yapısı

| Katman | Amaç |
|--------|------|
| Top | Sinyal, component placement |
| Inner 1 | Ground plane (analog) |
| Inner 2 | Power plane (±42V, 5V, 3.3V) |
| Bottom | Sinyal, routing |

---

## 3. Analog/Dijital Ayırma

| Kural | Açıklama |
|-------|----------|
| Analog ground | Ayrı plane |
| Digital ground | Ayrı plane |
| Tek nokta birleşme | Power supply girişinde |
| Analog koruma | Dijital sinyallerden uzak |

---

## 4. Trace Kuralları

| Sinyal | Minimum Genişlik | Aralık |
|--------|-------------------|--------|
| Power (±42V) | 50mil | — |
| Analog sinyal | 10mil | 2× trace genişliği |
| Digital (I2S) | 8mil | 3× trace genişliği |
| Clock | 8mil | 5× trace genişliği |

---

## 5. Bileşen Yerleşimi

| Kural | Açıklama |
|-------|----------|
| Decoupling | Her pin yakınına 100nF |
| Bulk | Her güç pinine 10µF |
| High-speed | Clock kaynaklarına yakın |
| Analog | Dijital kaynaklardan uzak |

---

## 6. EMC Kuralları

| Kural | Açıklama |
|-------|----------|
| Ground pour | Tüm katmanlarda |
| Stitching via | Analog/dijital sınırında |
| Filter | Her güç girişinde |
| Shield | Hassas analog bölgeler |

---

## 7. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-038-8.1-sound-card-chip-selection]] | Donanım seçimi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
