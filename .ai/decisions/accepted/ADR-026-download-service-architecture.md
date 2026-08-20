---
title: "ADR-026: Download Service Architecture"
status: frozen
date: 2026-05-15
tags: [architecture, download, nodejs, frozen]
---

# ADR-026: Download Service Architecture

---

## 1. Executive Summary

CoreMusic download servisi **Node.js + TypeScript** ile yazÄ±lÄ±r. Deezer ve YouTube'dan indirme yapar. Anti-ban sistemi (ADR-028) ile korunur.

## 2. Decision

| # | Kural | Durum |
|---|-------|-------|
| 1 | Node.js + TypeScript | âœ… Zorunlu |
| 2 | Port 3001 | âœ… Zorunlu |
| 3 | WebSocket desteÄŸi | âœ… Zorunlu |
| 4 | Anti-ban (ADR-028) | âœ… Zorunlu |
| 5 | FLAC 24/32-bit hedef | âœ… Zorunlu |

---

## 3. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |

---

*ADR-026: Download Service Architecture v2.0.0 â€” CoreMusic Architecture*
*Authority: Backend Architect Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*