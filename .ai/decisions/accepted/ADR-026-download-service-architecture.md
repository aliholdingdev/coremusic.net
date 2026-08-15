---
type: decision
id: "026"
title: "ADR-026: Download Service Architecture"
category: "architecture"
status: "frozen"
date: "2026-05-15"
updated: "2026-08-15"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [architecture, download, nodejs, frozen]
risk-level: "high"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-028-anti-ban-system]]"
---

# ADR-026: Download Service Architecture

---

## 1. Executive Summary

CoreMusic download servisi **Node.js + TypeScript** ile yazılır. Deezer ve YouTube'dan indirme yapar. Anti-ban sistemi (ADR-028) ile korunur.

## 2. Decision

| # | Kural | Durum |
|---|-------|-------|
| 1 | Node.js + TypeScript | ✅ Zorunlu |
| 2 | Port 3001 | ✅ Zorunlu |
| 3 | WebSocket desteği | ✅ Zorunlu |
| 4 | Anti-ban (ADR-028) | ✅ Zorunlu |
| 5 | FLAC 24/32-bit hedef | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-026: Download Service Architecture v2.0.0 — CoreMusic Architecture*
*Authority: Backend Architect · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
