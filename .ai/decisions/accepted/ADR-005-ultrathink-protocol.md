---
title: "ADR-005: Ultrathink Protocol (Zero Hallucination)"
status: frozen
date: 2026-02-05
tags: [architecture, quality, hallucination, verification, frozen]
---

# ADR-005: Ultrathink Protocol (Zero Hallucination)

---

## 1. Executive Summary

CoreMusic'te **Zero Hallucination** prensibi uygulanÄ±r. DoÄŸrulanamayan bilgi `VERIFICATION REQUIRED` olarak iÅŸaretlenir. HiÃ§bir AI ajanÄ± doÄŸrulanmamÄ±ÅŸ bilgiyi kesin gibi sunamaz.

## 2. Decision

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | DoÄŸrulanamayan bilgi â†’ VERIFICATION REQUIRED | âœ… Zorunlu |
| 2 | Uydurma API/endpoint yasak | âŒ Yasak |
| 3 | Uydurma versiyon yasak | âŒ Yasak |
| 4 | Uydurma CVE yasak | âŒ Yasak |
| 5 | Kaynak gÃ¶sterme zorunlu | âœ… Zorunlu |
| 6 | Vault-first okuma | âœ… Zorunlu |

### HallÃ¼sinasyon TÃ¼rleri

| TÃ¼r | Ã–rnek | Ã‡Ã¶zÃ¼m |
|-----|-------|-------|
| API HallÃ¼sinasyonu | Var olmayan endpoint | DoÄŸrula |
| Versiyon HallÃ¼sinasyonu | DoÄŸrulanmamÄ±ÅŸ sÃ¼rÃ¼m | Kaynak bul |
| Performans HallÃ¼sinasyonu | KanÄ±tsÄ±z benchmark | Ã–lÃ§ |
| CVE HallÃ¼sinasyonu | Uydurma gÃ¼venlik aÃ§Ä±ÄŸÄ± | DoÄŸrula |

---

## 3. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |

---

*ADR-005: Ultrathink Protocol v2.0.0 â€” CoreMusic Quality*
*Authority: Master Orchestrator Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*