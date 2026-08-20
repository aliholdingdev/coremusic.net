---
title: "ADR-073: Podcast Database Schema"
status: active
date: 2026-08-10
tags: [database, podcast, schema, active]
---

# ADR-073: Podcast Database Schema

---

## 1. Executive Summary

Podcast veritabanÄ±, podcast gÃ¶sterileri, bÃ¶lÃ¼mleri, abonelikleri ve transkriptleri yÃ¶netir.

## 2. Tablolar

| # | Tablo | AmaÃ§ |
|---|-------|------|
| 1 | podcast_shows | Podcast gÃ¶sterileri |
| 2 | podcast_episodes | Podcast bÃ¶lÃ¼mleri |
| 3 | podcast_subscriptions | Abonelikler |
| 4 | podcast_playback | Oynatma geÃ§miÅŸi |
| 5 | podcast_transcripts | Transkriptler |
| 6 | podcast_categories | Kategoriler |
| 7 | podcast_reviews | Yorumlar |
| 8 | podcast_bookmarks | Yer imleri |

## 3. Decision

| # | Kural | Durum |
|---|-------|-------|
| 1 | BCNF normalization | âœ… Zorunlu |
| 2 | Soft delete | âœ… Zorunlu |
| 3 | Full-text search (transcripts) | âœ… Zorunlu |

---

## 4. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-073: Podcast Database Schema v1.0.0 â€” CoreMusic Database*
*Authority: Data Engineer Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*