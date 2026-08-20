---
title: "ADR-004: Multi-Domain SPA Architecture"
status: frozen
date: 2026-02-01
tags: [architecture, spa, multi-domain, subdomain, frozen]
---

# ADR-004: Multi-Domain SPA Architecture

---

## 1. Executive Summary

CoreMusic, **multi-subdomain SPA** mimarisi ile Ã§alÄ±ÅŸÄ±r. Her subdomain (music, admin, home, car, studio, pro, media, download) kendi SPA'sÄ±nÄ± Ã§alÄ±ÅŸtÄ±rÄ±r. Subdomain'ler arasÄ± geÃ§iÅŸ client-side routing ile yapÄ±lÄ±r. auth.coremusic.net merkezi auth servisidir.

## 2. Status

| Alan | DeÄŸer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **Risk Seviyesi** | critical |

## 3. Decision

### Subdomain HaritasÄ±

| Subdomain | Port | AmaÃ§ |
|-----------|------|------|
| coremusic.net | 80 | Landing page |
| music.coremusic.net | 81 | Ana medya paneli |
| admin.coremusic.net | 80 | YÃ¶netim paneli |
| auth.coremusic.net | â€” | Merkezi auth |
| home.coremusic.net | 81 | Ev medya merkezi |
| car.coremusic.net | â€” | AraÃ§ iÃ§i |
| studio.coremusic.net | 81 | StÃ¼dyo |
| pro.coremusic.net | 81 | Profesyonel |
| media.coremusic.net | 5000/6000 | Medya processing |
| download.coremusic.net | 3001 | Ä°ndirme servisi |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Her subdomain kendi SPA'sÄ± | âœ… Zorunlu |
| 2 | auth.coremusic.net merkezi | âœ… Zorunlu |
| 3 | Cross-subdomain session | âœ… Zorunlu |
| 4 | Client-side routing | âœ… Zorunlu |
| 5 | History API | âœ… Zorunlu |

---

## 4. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |

---

*ADR-004: Multi-Domain SPA Architecture v2.0.0 â€” CoreMusic Architecture*
*Authority: Backend Architect Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*