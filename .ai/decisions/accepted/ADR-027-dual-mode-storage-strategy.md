---
type: decision
id: "027"
title: "ADR-027: Dual-Mode Storage Strategy"
category: "infrastructure"
status: "frozen"
date: "2026-05-20"
updated: "2026-08-15"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [infrastructure, storage, dual-mode, frozen]
risk-level: "medium"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-040-database-authority]]"
---

# ADR-027: Dual-Mode Storage Strategy

---

## 1. Executive Summary

CoreMusic **hibrit depolama** stratejisi kullanır: Session file-based ile başlar, DB'ye geçiş planlanır. Cache APCu ile başlar, Redis'e geçiş planlanır.

## 2. Decision

### Depolama Modları

| Veri | Başlangıç | Geçiş |
|------|-----------|-------|
| Session | File-based | DB (ADR-050) |
| Cache | APCu | Redis |
| Queue | File-based | Redis |
| Storage | Local filesystem | S3/NAS |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | File-based başlangıç | ✅ Zorunlu |
| 2 | DB geçiş planı | ✅ Zorunlu |
| 3 | Cache abstraction | ✅ Zorunlu |
| 4 | Storage abstraction | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-027: Dual-Mode Storage Strategy v2.0.0 — CoreMusic Infrastructure*
*Authority: Backend Architect · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
