---
title: "ADR-042: Vault Restructuring 2026-08-03"
status: active
date: 2026-08-03
tags: [vault, restructuring, ssot, active]
---

# ADR-042: Vault Restructuring

---

## 1. Executive Summary

CoreMusic `.ai/` vault'u **SSOT (Single Source of Truth)** olarak yeniden yapÄ±landÄ±rÄ±lmÄ±ÅŸtÄ±r. 9 zorunlu dosya, 25 template, 11 agent profili tanÄ±mlanmÄ±ÅŸtÄ±r.

## 2. Decision

### SSOT Core DosyalarÄ± (9)

| # | Dosya | AmaÃ§ |
|---|-------|------|
| 1 | CLAUDE.md | AI anayasasÄ± |
| 2 | AGENTS.md | Agent kayÄ±t defteri |
| 3 | WORKFLOW.md | SÃ¼reÃ§ler |
| 4 | index.md | Master katalog |
| 5 | keys.md | Keyword haritasÄ± |
| 6 | brain.md | Mimari kararlar |
| 7 | MEMORY.md | Session hafÄ±zasÄ± |
| 8 | log.md | Audit trail |
| 9 | engine.md | Orkestrasyon motoru |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | SSOT = .ai/ vault | âœ… Zorunlu |
| 2 | 9 zorunlu dosya | âœ… Zorunlu |
| 3 | Template kullanÄ±mÄ± | âœ… Zorunlu |
| 4 | Agent profilleri | âœ… Zorunlu |
| 5 | Vault-sync zorunlu | âœ… Zorunlu |

---

## 3. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-042: Vault Restructuring v2.0.0 â€” CoreMusic Vault*
*Authority: Master Orchestrator Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*