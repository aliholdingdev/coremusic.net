---
title: "ADR-016: URL Normalization"
status: frozen
date: 2026-04-01
tags: [routing, url, normalization, subdomain, frozen]
---

# ADR-016: URL Normalization

---

## 1. Executive Summary

CoreMusic URL'leri standartlaÅŸtÄ±rÄ±lmÄ±ÅŸtÄ±r. Subdomain routing, case normalization ve trailing slash politikasÄ± tanÄ±mlanmÄ±ÅŸtÄ±r.

## 2. Decision

### URL KurallarÄ±

| # | Kural | Durum |
|---|-------|-------|
| 1 | Subdomain routing | âœ… Zorunlu |
| 2 | Lowercase URL | âœ… Zorunlu |
| 3 | No trailing slash (except root) | âœ… Zorunlu |
| 4 | Hyphen separator | âœ… Zorunlu |
| 5 | UTF-8 encoding | âœ… Zorunlu |

### Subdomain Routing

| Subdomain | Port | AmaÃ§ |
|-----------|------|------|
| music | 81 | Ana medya |
| admin | 80 | YÃ¶netim |
| auth | â€” | Auth |
| home | 81 | Ev |
| car | â€” | AraÃ§ |
| studio | 81 | StÃ¼dyo |
| pro | 81 | Profesyonel |

---

## 3. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |

---

*ADR-016: URL Normalization v2.0.0 â€” CoreMusic Routing*
*Authority: Backend Architect Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*