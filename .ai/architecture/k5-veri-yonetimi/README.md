---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K5 Veri Yönetimi Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-20
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K5: Veri Yönetimi Layer

**Katman:** K5 (Veri Yönetimi)
**Kapsam:** MySQL 18 DB, Redis, APCu, File System, SQLite, Backup
**Sorumlu Agent:** Data Engineer
**Bileşen Sayısı:** 55

---

## 1. Genel Bakış

K5 katmanı, CoreMusic'in tüm verilerini yöneten veri katmanını içerir. 18 BCNF veritabanı, cache sistemleri ve dosya depolama stratejilerini kapsar.

### 1.1 18 BCNF Veritabanı (ADR-040)

| # | Veritabanı | Amaç | Tablo Sayısı |
|---|------------|------|-------------|
| 1 | coremusic_auth | Users, roles, sessions, tokens | 13 |
| 2 | coremusic_user | Profiles, preferences, history | 7 |
| 3 | coremusic_musics | Songs, artists, genres, lyrics | 22 |
| 4 | coremusic_albums | Album collections, discs, stats | 5 |
| 5 | coremusic_playlist | Playlists, collaborators | 5 |
| 6 | coremusic_catalog | Reference data | 8 |
| 7 | coremusic_logs | Audit trail, analytics | 22 |
| 8 | coremusic_media | Device sync, media metadata | 8 |
| 9 | coremusic_system | Settings, config, cache | 17 |
| 10 | coremusic_social | Comments, shares, activity | 9 |
| 11 | coremusic_wireless | WiFi + Bluetooth networks | 5 |
| 12 | coremusic_ai | Preferences, recommendations | 6 |
| 13 | coremusic_api | API keys, rate limits | 4 |
| 14 | coremusic_cms | Pages, blog, tags | 8 |
| 15 | coremusic_download | Download queue, history | 4 |
| 16 | coremusic_neva | EQ presets, DSP settings | 4 |
| 17 | coremusic_studio | Studio sessions, tracks | 6 |
| 18 | coremusic_patch | Schema versions, migrations | 3 |
| | **TOPLAM** | | **156** |

---

## 2. BCNF Kuralları

| Kural | Açıklama |
|-------|----------|
| BCNF zorunlu | Her tablo BCNF formunda olmalı |
| Soft delete | `is_deleted = 0` koşulu her sorguda |
| Snake_case | Tablo ve sütun isimleri |
| Timestamp | `created_at`, `updated_at`, `deleted_at` |
| Prepared statement | PDO prepared zorunlu |
| No ORM | Doctrine DBAL veya raw PDO |
| No `SELECT *` | Açık sütun listesi |

---

## 3. Cache Stratejisi

### 3.1 Multi-Tier Cache

```
L1: APCu (in-process, 60s TTL)
    ↓
L2: Redis (shared, 5min TTL)
    ↓
L3: File System (disk, 1hour TTL)
    ↓
L4: MySQL (persistent)
```

### 3.2 Cache Kullanım Alanları

| Veri | Cache Seviyesi | TTL |
|------|---------------|-----|
| Session | APCu | 3600s |
| User profile | Redis | 300s |
| EQ presets | APCu | 600s |
| Music metadata | Redis | 600s |
| API rate limit | APCu | 60s |
| Search results | Redis | 120s |

---

## 4. SQLite Offline-First

### 4.1 Offline Veri Modeli

```
Online (MySQL) ←→ Sync Engine ←→ Offline (SQLite)

Sync Protocol:
  1. Pull changes from server
  2. Push local changes
  3. Conflict resolution (last-write-wins)
  4. Merge and reconcile
```

---

## 5. Backup Stratejisi

### 5.1 Backup Planı

| Tip | Sıklık | Tutma | Yöntem |
|-----|--------|-------|--------|
| Full | Haftalık | 4 hafta | mysqldump |
| Incremental | Günlük | 7 gün | binlog |
| Real-time | Sürekli | 24 saat | Replication |
| File | Günlük | 30 gün | Restic |

---

## 6. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-003 | 9 BCNF izole veritabanı |
| ADR-040 | 18 BCNF veritabanı otoritesi |
| ADR-027 | Hibrit depolama |

---

*K5 Veri Yönetimi Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-20*
*Mode: Red Team · Human Mode · Truth Mode*
