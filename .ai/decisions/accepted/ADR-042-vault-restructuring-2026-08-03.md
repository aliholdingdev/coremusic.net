---
type: decision
id: "042"
title: "ADR-042: Vault Restructuring 2026-08-03"
category: "vault"
status: "active"
date: "2026-08-03"
updated: "2026-08-15"
authority: "Master Orchestrator"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [vault, restructuring, ssot, active]
risk-level: "high"
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
---

# ADR-042: Vault Restructuring

---

## 1. Executive Summary

CoreMusic `.ai/` vault'u **SSOT (Single Source of Truth)** olarak yeniden yapılandırılmıştır. 9 zorunlu dosya, 25 template, 11 agent profili tanımlanmıştır.

## 2. Decision

### SSOT Core Dosyaları (9)

| # | Dosya | Amaç |
|---|-------|------|
| 1 | CLAUDE.md | AI anayasası |
| 2 | AGENTS.md | Agent kayıt defteri |
| 3 | WORKFLOW.md | Süreçler |
| 4 | index.md | Master katalog |
| 5 | keys.md | Keyword haritası |
| 6 | brain.md | Mimari kararlar |
| 7 | MEMORY.md | Session hafızası |
| 8 | log.md | Audit trail |
| 9 | engine.md | Orkestrasyon motoru |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | SSOT = .ai/ vault | ✅ Zorunlu |
| 2 | 9 zorunlu dosya | ✅ Zorunlu |
| 3 | Template kullanımı | ✅ Zorunlu |
| 4 | Agent profilleri | ✅ Zorunlu |
| 5 | Vault-sync zorunlu | ✅ Zorunlu |

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-042: Vault Restructuring v2.0.0 — CoreMusic Vault*
*Authority: Master Orchestrator · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
