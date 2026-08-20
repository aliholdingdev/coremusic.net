---
title: "ADR-075: AI Database Schema"
status: active
date: 2026-08-10
tags: [database, ai, schema, active]
---

# ADR-075: AI Database Schema

---

## 1. Executive Summary

AI veritabanÄ±, kullanÄ±cÄ± tercih profillerini, dinleme Ã¶zelliklerini, Ã¶nerileri ve model verilerini yÃ¶netir.

## 2. Tablolar

| # | Tablo | AmaÃ§ |
|---|-------|------|
| 1 | ai_user_preferences | KullanÄ±cÄ± tercih profilleri |
| 2 | ai_listening_features | Dinleme Ã¶zelliÄŸi Ã§Ä±karÄ±mlarÄ± |
| 3 | ai_recommendations | Ã–neri sonuÃ§larÄ± |
| 4 | ai_models | ML model bilgileri |
| 5 | ai_training_data | EÄŸitim verileri |
| 6 | ai_feedback | KullanÄ±cÄ± geri bildirimi |

## 3. Decision

| # | Kural | Durum |
|---|-------|-------|
| 1 | BCNF normalization | âœ… Zorunlu |
| 2 | Soft delete | âœ… Zorunlu |
| 3 | Feature vector storage | âœ… Zorunlu |

---

## 4. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-075: AI Database Schema v1.0.0 â€” CoreMusic Database*
*Authority: Data Engineer Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*