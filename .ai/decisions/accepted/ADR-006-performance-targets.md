---
type: decision
id: "006"
title: "ADR-006: Performance Targets"
category: "architecture"
status: "frozen"
date: "2026-02-10"
updated: "2026-08-15"
authority: "Backend Architect"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [architecture, performance, targets, frozen]
risk-level: "high"
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
---

# ADR-006: Performance Targets

---

## 1. Executive Summary

CoreMusic performans hedefleri tanımlanmıştır. Tüm endpoint'ler bu hedeflere uymalıdır.

## 2. Decision

### Performans Hedefleri

| Metrik | Hedef | Kritik Eşik |
|--------|-------|-------------|
| TTFB | < 200ms | > 500ms |
| API Response | < 100ms | > 300ms |
| Page Load | < 2s | > 5s |
| First Paint | < 1s | > 3s |
| Audio Latency (ASIO) | < 10ms | > 20ms |
| Audio Latency (WASAPI) | < 20ms | > 50ms |
| DB Query | < 50ms | > 200ms |
| Cache Hit Rate | > 80% | < 50% |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | TTFB < 200ms | ✅ Zorunlu |
| 2 | API < 100ms | ✅ Zorunlu |
| 3 | Page Load < 2s | ✅ Zorunlu |
| 4 | ASIO latency < 10ms | ✅ Zorunlu |
| 5 | Cache hit > 80% | ✅ Zorunlu |
| 6 | Monitoring zorunlu | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-006: Performance Targets v2.0.0 — CoreMusic Architecture*
*Authority: Backend Architect · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
