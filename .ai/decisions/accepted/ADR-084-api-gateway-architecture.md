---
title: "ADR-084: API Gateway Architecture (API-First, BFF, CQRS)"
status: active
date: 2026-08-12
tags: [architecture, api, gateway, cqrs, bff, event-driven, active]
---

# ADR-084: API Gateway Architecture

---

## 1. Executive Summary

CoreMusic API'leri **API-First** stratejisi ile tasarlanÄ±r. Ã–nce OpenAPI sÃ¶zleÅŸmesi yazÄ±lÄ±r, sonra kod yazÄ±lÄ±r. **BFF (Backend for Frontend)** pattern'i ile her istemci tipi kendi backend'ini alÄ±r. **CQRS** ile yazma/okuma iÅŸlemleri ayrÄ±lÄ±r.

## 2. Decision

### API Gateway

TÃ¼m istemcilerin tek giriÅŸ noktasÄ± `api.coremusic.net`.

### BFF Tablosu

| Ä°stemci | BFF | Response |
|---------|-----|----------|
| SPA | SPA BFF | Tam veri |
| Mobile | Mobile BFF | Minimal |
| Embedded (RPi5) | Embedded BFF | Ultra-minimal |
| Desktop | Desktop BFF | Orta boy |
| Admin | Admin BFF | Full + audit |
| Car | Car BFF | Touch-optimized |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | API-First (OpenAPI) | âœ… Zorunlu |
| 2 | BFF pattern | âœ… Zorunlu |
| 3 | CQRS (yazma/okuma ayrÄ±mÄ±) | âœ… Zorunlu |
| 4 | Contract-first | âœ… Zorunlu |
| 5 | Correlation ID | âœ… Zorunlu |

### CQRS AkÄ±ÅŸÄ±

```
Write: Command â†’ Use Case â†’ Repository â†’ MySQL Master
Read:  Query â†’ Read Model â†’ Cache â†’ Response
```

---

## 3. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-084: API Gateway Architecture v2.0.0 â€” CoreMusic Architecture*
*Authority: Backend Architect Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*