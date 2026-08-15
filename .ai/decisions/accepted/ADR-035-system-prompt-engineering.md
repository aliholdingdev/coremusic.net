---
type: decision
id: "035"
title: "ADR-035: System Prompt Engineering"
category: "ai"
status: "frozen"
date: "2026-06-25"
updated: "2026-08-15"
authority: "Master Orchestrator"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [ai, prompt, engineering, frozen]
risk-level: "low"
references:
  - "[[brain.md]]"
  - "[[architecture/ai/prompt-engine]]"
---

# ADR-035: System Prompt Engineering

---

## 1. Executive Summary

CoreMusic AI prompt'ları **standart format** ile yazılır. Her prompt vault referansları, token limitleri ve çıktı formatı içerir.

## 2. Decision

### Prompt Formatı

| Alan | Zorunlu mu? |
|------|-------------|
| Title | ✅ |
| Vault references | ✅ |
| Token limit | ✅ |
| Output format | ✅ |
| Guardrails | ✅ |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Vault-first prompt | ✅ Zorunlu |
| 2 | Token limit belirleme | ✅ Zorunlu |
| 3 | Output format tanımla | ✅ Zorunlu |
| 4 | Guardrails ekle | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-035: System Prompt Engineering v2.0.0 — CoreMusic AI*
*Authority: Master Orchestrator · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
