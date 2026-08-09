---
type: adr
category: database
title: "ADR-075: AI Database Schema"
date: 2026-08-10
updated: 2026-08-10
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-075: AI Database Schema

**Status:** Active (güncellenebilir)
**Kategorisi:** Database
**İlgili Agent:** [[.agents/data-engineer]]

---

## 1. Tablolar

| # | Tablo | Amaç |
|---|-------|------|
| 1 | `user_preference_profiles` | Kullanıcı tercih profilleri |
| 2 | `listening_features` | Dinleme özellikleri (ML features) |
| 3 | `recommendation_history` | Öneri geçmişi |
| 4 | `audio_features` | Ses analiz özellikleri |
| 5 | `model_versions` | AI model versiyonları |
| 6 | `training_jobs` | Eğitim işleri |

## 2. BCNF Uyumluluğu

| Tablo | Functional Dependency | Candidate Key |
|-------|----------------------|---------------|
| user_preference_profiles | id → {user_id, genre_weights, ...} | user_id UNIQUE |
| listening_features | id → {user_id, music_id, features, ...} | (user_id, music_id) UNIQUE |
| recommendation_history | id → {user_id, music_id, algorithm, ...} | id |
| audio_features | id → {music_id, bpm, key, loudness, ...} | music_id UNIQUE |
| model_versions | id → {model_name, version, accuracy, ...} | (model_name, version) UNIQUE |
| training_jobs | id → {model_version_id, status, ...} | id |

## 3. Cross-DB Referansları

| Kaynak | Hedef DB | Hedef Tablo |
|--------|----------|-------------|
| user_preference_profiles.user_id | coremusic_auth | users |
| listening_features.user_id | coremusic_auth | users |
| listening_features.music_id | coremusic_musics | musics |
| recommendation_history.user_id | coremusic_auth | users |
| audio_features.music_id | coremusic_musics | musics |

---

## 4. İlgili ADR'ler

| ADR | İlişki |
|-----|--------|
| [[ADR-003-multi-db-9-databases]] | DB mimarisi |
| [[ADR-040-database-authority]] | DB otoritesi |
| [[ADR-030-ai-strategy-core]] | AI stratejisi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team · Human Mode · Truth Mode
