---
type: adr
category: database
title: "ADR-073: Podcast Database Schema"
date: 2026-08-10
updated: 2026-08-10
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-073: Podcast Database Schema

**Status:** Active (güncellenebilir)
**Kategorisi:** Database
**İlgili Agent:** [[.agents/data-engineer]]

---

## 1. Tablolar

| # | Tablo | Amaç |
|---|-------|------|
| 1 | `podcast_shows` | Podcast gösterileri |
| 2 | `podcast_episodes` | Bölümler |
| 3 | `podcast_subscriptions` | Kullanıcı abonelikleri |
| 4 | `podcast_transcripts` | Otomatik transkripsiyon (AI Service) |

## 2. BCNF Uyumluluğu

| Tablo | Functional Dependency | Candidate Key |
|-------|----------------------|---------------|
| podcast_shows | id → {title, author, category, ...} | id |
| podcast_episodes | id → {show_id, title, duration, ...} | id |
| podcast_subscriptions | id → {user_id, show_id, ...} | (user_id, show_id) UNIQUE |
| podcast_transcripts | id → {episode_id, language, content, ...} | (episode_id, language) UNIQUE |

## 3. Cross-DB Referansları

| Kaynak | Hedef DB | Hedef Tablo |
|--------|----------|-------------|
| podcast_subscriptions.user_id | coremusic_auth | users |

---

## 4. İlgili ADR'ler

| ADR | İlişki |
|-----|--------|
| [[ADR-003-multi-db-9-databases]] | DB mimarisi |
| [[ADR-040-database-authority]] | DB otoritesi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team · Human Mode · Truth Mode
