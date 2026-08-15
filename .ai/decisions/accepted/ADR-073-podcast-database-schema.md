---
type: decision
id: "073"
title: "ADR-073: Podcast Database Schema"
category: "database"
status: "active"
date: "2026-08-10"
updated: "2026-08-15"
authority: "Data Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [database, podcast, schema, active]
risk-level: "medium"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-040-database-authority]]"
---

# ADR-073: Podcast Database Schema

---

## 1. Executive Summary

Podcast veritabanı, podcast gösterileri, bölümleri, abonelikleri ve transkriptleri yönetir.

## 2. Tablolar

| # | Tablo | Amaç |
|---|-------|------|
| 1 | podcast_shows | Podcast gösterileri |
| 2 | podcast_episodes | Podcast bölümleri |
| 3 | podcast_subscriptions | Abonelikler |
| 4 | podcast_playback | Oynatma geçmişi |
| 5 | podcast_transcripts | Transkriptler |
| 6 | podcast_categories | Kategoriler |
| 7 | podcast_reviews | Yorumlar |
| 8 | podcast_bookmarks | Yer imleri |

## 3. Decision

| # | Kural | Durum |
|---|-------|-------|
| 1 | BCNF normalization | ✅ Zorunlu |
| 2 | Soft delete | ✅ Zorunlu |
| 3 | Full-text search (transcripts) | ✅ Zorunlu |

---

## 4. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-073: Podcast Database Schema v1.0.0 — CoreMusic Database*
*Authority: Data Engineer · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
