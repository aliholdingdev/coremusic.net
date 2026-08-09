---
type: adr
category: database
title: "ADR-077: Studio Database Schema"
date: 2026-08-10
updated: 2026-08-10
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-077: Studio Database Schema

**Status:** Active (güncellenebilir)
**Kategorisi:** Database
**İlgili Agent:** [[.agents/data-engineer]]

---

## 1. Tablolar

| # | Tablo | Amaç |
|---|-------|------|
| 1 | `studio_sessions` | Kayıt oturumları |
| 2 | `studio_tracks` | Parçalar |
| 3 | `studio_presets` | Ses preset'leri |
| 4 | `studio_equipment` | Ekipman envanteri |
| 5 | `session_equipment` | Oturum-ekipman eşleştirme (junction) |
| 6 | `studio_collaborators` | İşbirlikçi yönetimi |

## 2. BCNF Uyumluluğu

| Tablo | Functional Dependency | Candidate Key |
|-------|----------------------|---------------|
| studio_sessions | id → {host_user_id, name, ...} | id |
| studio_tracks | id → {session_id, name, instrument, ...} | id |
| studio_presets | id → {user_id, name, settings, ...} | (user_id, name) UNIQUE |
| studio_equipment | id → {brand, model, type, ...} | id |
| session_equipment | id → {session_id, equipment_id, ...} | (session_id, equipment_id) UNIQUE |
| studio_collaborators | id → {session_id, user_id, role, ...} | (session_id, user_id) UNIQUE |

---

## 3. İlgili ADR'ler

| ADR | İlişki |
|-----|--------|
| [[ADR-003-multi-db-9-databases]] | DB mimarisi |
| [[ADR-040-database-authority]] | DB otoritesi |
| [[ADR-038-8.1-sound-card-chip-selection]] | Donanım entegrasyonu |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team · Human Mode · Truth Mode
