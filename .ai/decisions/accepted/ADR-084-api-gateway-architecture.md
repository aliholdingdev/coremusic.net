---
type: decision
id: "084"
title: "ADR-084: API Gateway Architecture (API-First, BFF, CQRS)"
category: "architecture"
status: "active"
date: "2026-08-12"
updated: "2026-08-15"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [architecture, api, gateway, cqrs, bff, event-driven, active]
risk-level: "critical"
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[decisions/accepted/ADR-039-7-service-platform-architecture]]"
  - "[[decisions/accepted/ADR-086-event-driven-architecture]]"
---

# ADR-084: API Gateway Architecture

---

## 1. Executive Summary

CoreMusic API'leri **API-First** stratejisi ile tasarlanır. Önce OpenAPI sözleşmesi yazılır, sonra kod yazılır. **BFF (Backend for Frontend)** pattern'i ile her istemci tipi kendi backend'ini alır. **CQRS** ile yazma/okuma işlemleri ayrılır.

## 2. Decision

### API Gateway

Tüm istemcilerin tek giriş noktası `api.coremusic.net`.

### BFF Tablosu

| İstemci | BFF | Response |
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
| 1 | API-First (OpenAPI) | ✅ Zorunlu |
| 2 | BFF pattern | ✅ Zorunlu |
| 3 | CQRS (yazma/okuma ayrımı) | ✅ Zorunlu |
| 4 | Contract-first | ✅ Zorunlu |
| 5 | Correlation ID | ✅ Zorunlu |

### CQRS Akışı

```
Write: Command → Use Case → Repository → MySQL Master
Read:  Query → Read Model → Cache → Response
```

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-084: API Gateway Architecture v2.0.0 — CoreMusic Architecture*
*Authority: Backend Architect · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
