---
title: "ADR-063: Hardware Design Standards"
status: active
date: 2026-08-09
tags: [electronics, hardware, design, standards, active]
---

# ADR-063: Hardware Design Standards

---

## 1. Executive Summary

CoreMusic donanÄ±m tasarÄ±m standartlarÄ± tanÄ±mlanmÄ±ÅŸtÄ±r. PCB katman sayÄ±sÄ±, bileÅŸen seÃ§imi ve test protokolleri belirlenmiÅŸtir.

## 2. Decision

### PCB StandartlarÄ±

| Ã–zellik | DeÄŸer |
|---------|-------|
| Katman sayÄ±sÄ± | 4 (Ã¶nerilen) |
| Min track width | 6mil |
| Min drill size | 0.3mm |
| Impedance control | 50ohm single-ended |
| EMI/EMC | IEC 61000 |

### BileÅŸen SeÃ§im KurallarÄ±

| # | Kural | Durum |
|---|-------|-------|
| 1 | 4 katman PCB | âœ… Ã–nerilen |
| 2 | Impedance control | âœ… Zorunlu |
| 3 | EMI shielding | âœ… Zorunlu |
| 4 | Thermal management | âœ… Zorunlu |
| 5 | Test points | âœ… Zorunlu |

---

## 3. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-063: Hardware Design Standards v1.0.0 â€” CoreMusic Electronics*
*Authority: Audio Hardware Engineer Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*