---
title: "ADR-050: Multi-DB Sync Strategy"
status: active
date: 2026-08-08
tags: [database, sync, multi-db, active]
---

# ADR-050: Multi-DB Sync Strategy

---

## 1. Executive Summary

18 BCNF veritabanÄ± arasÄ±ndaki senkronizasyon **event-driven** strateji ile yÃ¶netilir. Cross-db query yasak olduÄŸu iÃ§in veri paylaÅŸÄ±mÄ± **integration events** ile yapÄ±lÄ±r.

## 2. Decision

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Cross-db query yasak | âŒ Yasak |
| 2 | Event-driven sync | âœ… Zorunlu |
| 3 | Integration events | âœ… Zorunlu |
| 4 | eventual consistency | âœ… Kabul |
| 5 | Event log | âœ… Zorunlu |

### Sync AkÄ±ÅŸÄ±

```
Service A (coremusic_auth)
    â”‚
    â”œâ”€â”€â–º UserCreatedEvent
    â”‚
    â””â”€â”€â–º Event Bus (PSR-14)
            â”‚
            â”œâ”€â”€â–º coremusic_user (profile oluÅŸtur)
            â”œâ”€â”€â–º coremusic_social (social profile oluÅŸtur)
            â””â”€â”€â–º coremusic_ai (AI profil oluÅŸtur)
```

---

## 3. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-050: Multi-DB Sync Strategy v2.0.0 â€” CoreMusic Database*
*Authority: Data Engineer Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*