---
type: decision
id: "076"
title: "ADR-076: Video Database Schema"
category: "database"
status: "active"
date: "2026-08-10"
updated: "2026-08-15"
authority: "Data Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [database, video, schema, active]
risk-level: "medium"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-040-database-authority]]"
---

# ADR-076: Video Database Schema

---

## 1. Executive Summary

Video veritabanı, müzik videoları, oynatma geçmişi ve altyazıları yönetir.

## 2. Tablolar

| # | Tablo | Amaç |
|---|-------|------|
| 1 | music_videos | Müzik videoları |
| 2 | video_playback | Oynatma geçmişi |
| 3 | video_subtitles | Altyazılar |
| 4 | video_playlists | Video çalma listeleri |

## 3. Decision

| # | Kural | Durum |
|---|-------|-------|
| 1 | BCNF normalization | ✅ Zorunlu |
| 2 | Soft delete | ✅ Zorunlu |
| 3 | Subtitle multi-language | ✅ Zorunlu |

---

## 4. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-076: Video Database Schema v1.0.0 — CoreMusic Database*
*Authority: Data Engineer · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
