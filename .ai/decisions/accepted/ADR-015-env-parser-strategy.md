---
title: "ADR-015: Env Parser Strategy"
status: frozen
date: 2026-03-30
tags: [infrastructure, env, config, frozen]
---

# ADR-015: Env Parser Strategy

---

## 1. Executive Summary

CoreMusic ortam deÄŸiÅŸkenleri `.env` dosyasÄ±ndan okunur. `vlucas/phpdotenv` kullanÄ±lÄ±r. Hassas bilgiler credential vault'ta AES-256-GCM ile ÅŸifrelenir.

## 2. Decision

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | .env dosyasÄ± | âœ… Zorunlu |
| 2 | phpdotenv kullanÄ±mÄ± | âœ… Zorunlu |
| 3 | Hassas bilgi credential vault | âœ… Zorunlu |
| 4 | .gitignore'da .env | âœ… Zorunlu |
| 5 | Environment-specific configs | âœ… Zorunlu |

---

## 3. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |

---

*ADR-015: Env Parser Strategy v2.0.0 â€” CoreMusic Infrastructure*
*Authority: Backend Architect Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*