---
type: adr
category: database
title: "ADR-076: Video Database Schema"
date: 2026-08-10
updated: 2026-08-10
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-076: Video Database Schema

**Status:** Active (güncellenebilir)
**Kategorisi:** Database
**İlgili Agent:** [[.agents/data-engineer]]

---

## 1. Tablolar

| # | Tablo | Amaç |
|---|-------|------|
| 1 | `music_videos` | Müzik videoları |
| 2 | `video_playback_history` | Video oynatma geçmişi |
| 3 | `video_subtitles` | Altyazı/dil destek |

## 2. BCNF Uyumluluğu

| Tablo | Functional Dependency | Candidate Key |
|-------|----------------------|---------------|
| music_videos | id → {music_id, title, url, ...} | music_id UNIQUE |
| video_playback_history | id → {user_id, video_id, ...} | id |
| video_subtitles | id → {video_id, language, url, ...} | (video_id, language) UNIQUE |

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
