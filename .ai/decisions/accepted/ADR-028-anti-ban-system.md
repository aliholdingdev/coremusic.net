---
title: "ADR-028: Anti-Ban System"
status: frozen
date: 2026-05-25
tags: [download, anti-ban, rate-limit, proxy, frozen]
---

# ADR-028: Anti-Ban System

---

## 1. Executive Summary

CoreMusic download servisi **anti-ban** sistemi ile korunur. Rate limiting, proxy rotasyonu ve ARL token yÃ¶netimi ile platform ban'larÄ± engellenir.

## 2. Decision

### Anti-Ban Stratejileri

| # | Strateji | Durum |
|---|----------|-------|
| 1 | Rate limiting (platform bazlÄ±) | âœ… Zorunlu |
| 2 | Proxy rotasyonu | âœ… Zorunlu |
| 3 | ARL token rotasyonu | âœ… Zorunlu |
| 4 | User-Agent Ã§eÅŸitliliÄŸi | âœ… Zorunlu |
| 5 | Random delay | âœ… Zorunlu |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Anti-ban aktif | âœ… Zorunlu |
| 2 | Proxy rotation | âœ… Zorunlu |
| 3 | ARL token management | âœ… Zorunlu |
| 4 | Graceful degradation | âœ… Zorunlu |

---

## 3. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |

---

*ADR-028: Anti-Ban System v2.0.0 â€” CoreMusic Download*
*Authority: Backend Architect Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*