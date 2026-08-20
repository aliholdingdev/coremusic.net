---
title: "ADR-027: Dual-Mode Storage Strategy"
status: frozen
date: 2026-05-20
tags: [infrastructure, storage, dual-mode, frozen]
---

# ADR-027: Dual-Mode Storage Strategy

---

## 1. Executive Summary

CoreMusic **hibrit depolama** stratejisi kullanÄ±r: Session file-based ile baÅŸlar, DB'ye geÃ§iÅŸ planlanÄ±r. Cache APCu ile baÅŸlar, Redis'e geÃ§iÅŸ planlanÄ±r.

## 2. Decision

### Depolama ModlarÄ±

| Veri | BaÅŸlangÄ±Ã§ | GeÃ§iÅŸ |
|------|-----------|-------|
| Session | File-based | DB (ADR-050) |
| Cache | APCu | Redis |
| Queue | File-based | Redis |
| Storage | Local filesystem | S3/NAS |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | File-based baÅŸlangÄ±Ã§ | âœ… Zorunlu |
| 2 | DB geÃ§iÅŸ planÄ± | âœ… Zorunlu |
| 3 | Cache abstraction | âœ… Zorunlu |
| 4 | Storage abstraction | âœ… Zorunlu |

---

## 3. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |

---

*ADR-027: Dual-Mode Storage Strategy v2.0.0 â€” CoreMusic Infrastructure*
*Authority: Backend Architect Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*