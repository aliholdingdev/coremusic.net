---
title: "ADR-074: Radio Database Schema"
status: active
date: 2026-08-10
tags: [database, radio, schema, active]
---

# ADR-074: Radio Database Schema

---

## 1. Executive Summary

Radyo veritabanÄ±, radyo istasyonlarÄ±, programlarÄ± ve currently playing bilgilerini yÃ¶netir.

## 2. Tablolar

| # | Tablo | AmaÃ§ |
|---|-------|------|
| 1 | radio_stations | Radyo istasyonlarÄ± |
| 2 | radio_schedules | Program Ã§izelgeleri |
| 3 | radio_now_playing | Åu an Ã§alan |
| 4 | radio_favorites | Favori istasyonlar |
| 5 | radio_genres | Radyo tÃ¼rleri |

## 3. Decision

| # | Kural | Durum |
|---|-------|-------|
| 1 | BCNF normalization | âœ… Zorunlu |
| 2 | Soft delete | âœ… Zorunlu |
| 3 | Real-time now_playing | âœ… Zorunlu |

---

## 4. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-074: Radio Database Schema v1.0.0 â€” CoreMusic Database*
*Authority: Data Engineer Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*