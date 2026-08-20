---
title: "ADR-046: Cross-View State Preservation"
status: active
date: 2026-08-08
tags: [frontend, state, preservation, cross-view, active]
---

# ADR-046: Cross-View State Preservation

---

## 1. Executive Summary

View mode'lar arasÄ± geÃ§iÅŸlerde **state korunur**. KullanÄ±cÄ± pro modundan home moduna geÃ§tiÄŸinde, scroll pozisyonu, seÃ§imler ve geÃ§ici veriler korunur.

## 2. Decision

### State Koruma AlanlarÄ±

| Alan | Korunma | YÃ¶ntem |
|------|---------|--------|
| Scroll pozisyonu | âœ… | sessionStorage |
| SeÃ§ili Ã¶ÄŸe | âœ… | Client-side state |
| Arama filtresi | âœ… | URL params |
| Player durumu | âœ… | Global state |
| Form verileri | âœ… | sessionStorage |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Scroll state korunur | âœ… Zorunlu |
| 2 | Player state korunur | âœ… Zorunlu |
| 3 | URL params ile state | âœ… Zorunlu |
| 4 | sessionStorage kullanÄ±mÄ± | âœ… Zorunlu |

---

## 3. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-046: Cross-View State Preservation v2.0.0 â€” CoreMusic Frontend*
*Authority: UI Designer Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*