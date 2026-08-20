---
title: "ADR-076: Video Database Schema"
status: active
date: 2026-08-10
tags: [database, video, schema, active]
---

# ADR-076: Video Database Schema

---

## 1. Executive Summary

Video veritabanÄ±, mÃ¼zik videolarÄ±, oynatma geÃ§miÅŸi ve altyazÄ±larÄ± yÃ¶netir.

## 2. Tablolar

| # | Tablo | AmaÃ§ |
|---|-------|------|
| 1 | music_videos | MÃ¼zik videolarÄ± |
| 2 | video_playback | Oynatma geÃ§miÅŸi |
| 3 | video_subtitles | AltyazÄ±lar |
| 4 | video_playlists | Video Ã§alma listeleri |

## 3. Decision

| # | Kural | Durum |
|---|-------|-------|
| 1 | BCNF normalization | âœ… Zorunlu |
| 2 | Soft delete | âœ… Zorunlu |
| 3 | Subtitle multi-language | âœ… Zorunlu |

---

## 4. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-076: Video Database Schema v1.0.0 â€” CoreMusic Database*
*Authority: Data Engineer Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*