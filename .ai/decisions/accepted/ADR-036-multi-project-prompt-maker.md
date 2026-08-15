---
type: decision
id: "036"
title: "ADR-036: Multi-Project Prompt Maker"
category: "ai"
status: "frozen"
date: "2026-06-30"
updated: "2026-08-15"
authority: "Master Orchestrator"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [ai, prompt, multi-project, frozen]
risk-level: "low"
references:
  - "[[brain.md]]"
  - "[[architecture/ai/prompt-engine]]"
---

# ADR-036: Multi-Project Prompt Maker

---

## 1. Executive Summary

CoreMusic prompt sistemi **çoklu proje** destekler. Her proje için ayrı prompt'lar oluşturulabilir ancak ortak vault referansları paylaşılır.

## 2. Decision

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Multi-project destek | ✅ Zorunlu |
| 2 | Ortak vault referansları | ✅ Zorunlu |
| 3 | Proje-specific prompt | ✅ Zorunlu |
| 4 | Prompt versioning | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-036: Multi-Project Prompt Maker v2.0.0 — CoreMusic AI*
*Authority: Master Orchestrator · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
