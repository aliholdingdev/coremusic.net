---
type: adr
category: database
title: "ADR-074: Radio Database Schema"
date: 2026-08-10
updated: 2026-08-10
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-074: Radio Database Schema

**Status:** Active (güncellenebilir)
**Kategorisi:** Database
**İlgili Agent:** [[.agents/data-engineer]]

---

## 1. Tablolar

| # | Tablo | Amaç |
|---|-------|------|
| 1 | `radio_stations` | Radyo istasyonları |
| 2 | `radio_schedules` | Yayın programları |
| 3 | `radio_now_playing` | Anlık yayın durumu |

## 2. BCNF Uyumluluğu

| Tablo | Functional Dependency | Candidate Key |
|-------|----------------------|---------------|
| radio_stations | id → {name, genre, frequency, ...} | id |
| radio_schedules | id → {station_id, day_of_week, ...} | (station_id, day_of_week, start_time) UNIQUE |
| radio_now_playing | id → {station_id, track_title, ...} | station_id UNIQUE |

---

## 3. İlgili ADR'ler

| ADR | İlişki |
|-----|--------|
| [[ADR-003-multi-db-9-databases]] | DB mimarisi |
| [[ADR-040-database-authority]] | DB otoritesi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team · Human Mode · Truth Mode
