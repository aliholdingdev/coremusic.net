---
type: decision
id: "074"
title: "ADR-074: Radio Database Schema"
category: "database"
status: "active"
date: "2026-08-10"
updated: "2026-08-15"
authority: "Data Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [database, radio, schema, active]
risk-level: "medium"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-040-database-authority]]"
---

# ADR-074: Radio Database Schema

---

## 1. Executive Summary

Radyo veritabanı, radyo istasyonları, programları ve currently playing bilgilerini yönetir.

## 2. Tablolar

| # | Tablo | Amaç |
|---|-------|------|
| 1 | radio_stations | Radyo istasyonları |
| 2 | radio_schedules | Program çizelgeleri |
| 3 | radio_now_playing | Şu an çalan |
| 4 | radio_favorites | Favori istasyonlar |
| 5 | radio_genres | Radyo türleri |

## 3. Decision

| # | Kural | Durum |
|---|-------|-------|
| 1 | BCNF normalization | ✅ Zorunlu |
| 2 | Soft delete | ✅ Zorunlu |
| 3 | Real-time now_playing | ✅ Zorunlu |

---

## 4. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-074: Radio Database Schema v1.0.0 — CoreMusic Database*
*Authority: Data Engineer · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
