---
title: "ADR-049: Startup Prompt Loader"
status: active
date: 2026-08-08
tags: [ai, prompt, startup, loader, active]
---

# ADR-049: Startup Prompt Loader

---

## 1. Executive Summary

CoreMusic'te her AI oturum baÅŸlangÄ±cÄ±nda **4 ana prompt** (prompt0-3) sÄ±rasÄ±yla yÃ¼klenir. Prompt'lar vault'a iÅŸlenmiÅŸtir ve referans olarak kullanÄ±lÄ±r.

## 2. Decision

### Prompt SÄ±rasÄ±

| SÄ±ra | Prompt | Dosya | Max SÃ¼re |
|------|--------|-------|----------|
| 1 | prompt0 (Genel Ana) | archives/prompt0-genel-ana-prompt | 5s |
| 2 | prompt1 (SPA Router) | archives/prompt1-spa-router | 3s |
| 3 | prompt2 (Auth) | archives/prompt2-auth | 3s |
| 4 | prompt3 (API) | archives/prompt3-api | 3s |

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | 4 prompt sÄ±ralÄ± yÃ¼kleme | âœ… Zorunlu |
| 2 | Max 14s toplam sÃ¼re | âœ… Zorunlu |
| 3 | Vault'a iÅŸlenmiÅŸ prompt | âœ… Zorunlu |
| 4 | Domain bazlÄ± prompt seÃ§imi | âœ… Zorunlu |

---

## 3. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-049: Startup Prompt Loader v2.0.0 â€” CoreMusic AI*
*Authority: Master Orchestrator Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*