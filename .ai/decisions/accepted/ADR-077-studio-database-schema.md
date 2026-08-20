---
title: "ADR-077: Studio Database Schema"
status: active
date: 2026-08-10
tags: [database, studio, schema, active]
---

# ADR-077: Studio Database Schema

---

## 1. Executive Summary

StÃ¼dyo veritabanÄ±, stÃ¼dyo oturumlarÄ±nÄ±, parÃ§alarÄ±, preset'leri ve ekipman bilgilerini yÃ¶netir.

## 2. Tablolar

| # | Tablo | AmaÃ§ |
|---|-------|------|
| 1 | studio_sessions | StÃ¼dyo oturumlarÄ± |
| 2 | studio_tracks | KayÄ±t parÃ§alarÄ± |
| 3 | studio_presets | Ses preset'leri |
| 4 | studio_equipment | Ekipman bilgileri |
| 5 | studio_templates | Åablonlar |
| 6 | studio_collaborators | Ä°ÅŸbirlikÃ§iler |

## 3. Decision

| # | Kural | Durum |
|---|-------|-------|
| 1 | BCNF normalization | âœ… Zorunlu |
| 2 | Soft delete | âœ… Zorunlu |
| 3 | Equipment compatibility | âœ… Zorunlu |

---

## 4. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-077: Studio Database Schema v1.0.0 â€” CoreMusic Database*
*Authority: Data Engineer Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*