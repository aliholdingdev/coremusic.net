---
type: architecture
category: layer-definition
title: "K5 — Data Layer (55 Components)"
date: 2026-09-18
updated: 2026-09-18
status: draft
version: 1.0.0
authority: Bayram Ali / Vault Steward
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/architecture/k5-data-layer.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/brain.md"
  layer: K5
  component_count: 55
---

# K5 — Veri Yönetimi Katmanı (Data Layer)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[brain.md]]

**Kapsam:** 18 BCNF veritabanı, cache katmanları, dosya sistemi, backup stratejisi ve veri güvenliği.

---

## 1. Genel Bakış

K5 katmanı, CoreMusic'in tüm veri yönetimi altyapısını tanımlar. 18 BCNF veritabanı, Redis/APCu cache, dosya sistemi depolama, backup ve veri güvenliği bu katmanın temel bileşenleridir.

### 1.1 Veri Mimarisi Diyagramı

```
┌─────────────────────────────────────────────────────────────────────────┐
│                    K5 — DATA LAYER (55)                                 │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    DATABASE REGISTRY (18 DB)                     │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │   │
│  │  │coremusic │ │coremusic │ │coremusic │ │coremusic │          │   │
│  │  │_auth     │ │_user     │ │_musics   │ │_albums   │          │   │
│  │  │(13 tbl)  │ │(7 tbl)   │ │(22 tbl)  │ │(5 tbl)   │          │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │   │
│  │  │coremusic │ │coremusic │ │coremusic │ │coremusic │          │   │
│  │  │_playlist │ │_catalog  │ │_logs     │ │_media    │          │   │
│  │  │(5 tbl)   │ │(4 tbl)   │ │(6 tbl)   │ │(5 tbl)   │          │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │   │
│  │  │coremusic │ │coremusic │ │coremusic │ │coremusic │          │   │
│  │  │_system   │ │_social   │ │_wireless │ │_ai       │          │   │
│  │  │(4 tbl)   │ │(6 tbl)   │ │(3 tbl)   │ │(5 tbl)   │          │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │   │
│  │  │coremusic │ │coremusic │ │coremusic │ │coremusic │          │   │
│  │  │_api      │ │_cms      │ │_download │ │_neva     │          │   │
│  │  │(3 tbl)   │ │(4 tbl)   │ │(5 tbl)   │ │(3 tbl)   │          │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐                       │   │
│  │  │coremusic │ │coremusic │ │coremusic │                       │   │
│  │  │_studio   │ │_patch    │ │_car      │                       │   │
│  │  │(4 tbl)   │ │(2 tbl)   │ │(3 tbl)   │                       │   │
│  │  └──────────┘ └──────────┘ └──────────┘                       │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    CACHE LAYERS (6)                              │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │   │
│  │  │Redis     │ │Redis     │ │Redis     │ │APCu      │          │   │
│  │  │Session   │ │Query     │ │Rate      │ │Opcode    │          │   │
│  │  │Store     │ │Cache     │ │Limit     │ │Cache     │          │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │   │
│  │  ┌──────────┐ ┌──────────┐                                     │   │
│  │  │APCu      │ │Memcached │                                     │   │
│  │  │User Cache│ │(Fallback)│                                     │   │
│  │  └──────────┘ └──────────┘                                     │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    FILE SYSTEM (2)                               │   │
│  │  ┌──────────────────┐ ┌──────────────────┐                     │   │
│  │  │File System Local │ │File System S3    │                     │   │
│  │  │(NTFS/ext4/APFS) │ │(Object Storage)  │                     │   │
│  │  └──────────────────┘ └──────────────────┘                     │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    QUEUE & OFFLINE (1)                           │   │
│  │  ┌──────────────────────────────────────────────────────┐      │   │
│  │  │ SQLite Offline Queue                                 │      │   │
│  │  └──────────────────────────────────────────────────────┘      │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    MYSQL FEATURES (5)                           │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │   │
│  │  │FULLTEXT  │ │Replica-  │ │Partition-│ │Backup    │          │   │
│  │  │Search    │ │tion      │ │ing       │ │Strategy  │          │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │   │
│  │  ┌──────────┐                                                  │   │
│  │  │InnoDB    │                                                  │   │
│  │  │Cluster   │                                                  │   │
│  │  └──────────┘                                                  │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    BACKUP & MIGRATION (4)                        │   │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐          │   │
│  │  │Backup    │ │Data      │ │Data      │ │Data      │          │   │
│  │  │Restic    │ │Archive   │ │Export    │ │Import    │          │   │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘          │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                    SECURITY (3)                                  │   │
│  │  ┌──────────────────┐ ┌──────────────────┐ ┌────────────────┐  │   │
│  │  │Encryption at Rest│ │Encryption Transit│ │Data Masking    │  │   │
│  │  │(AES-256-GCM)     │ │(TLS 1.3)         │ │                │  │   │
│  │  └──────────────────┘ └──────────────────┘ └────────────────┘  │   │
│  └──────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────┘
```

### 1.2 Bileşen Dağılımı

| Kategori | Bileşen Sayısı |
|----------|---------------|
| Database (18 DB × ~3 tablo) | 18 |
| Cache Layers | 6 |
| File System | 2 |
| Queue & Offline | 1 |
| MySQL Features | 5 |
| Backup & Migration | 4 |
| Security | 3 |
| Misc (alt tablolar) | 16 |
| **TOPLAM** | **55** |

---

## 2. Database Registry (18 BCNF)

### 2.1 Veritabanı Haritası

| # | Database | Amaç | Tablo Sayısı | Referans |
|---|----------|------|-------------|----------|
| 1 | `coremusic_auth` | Users, roles, sessions, tokens, credential vault, API keys | 13 | [[k05-data-detail]] |
| 2 | `coremusic_user` | Profiles, preferences, history, favorites | 7 | [[k05-data-detail]] |
| 3 | `coremusic_musics` | Songs, artists, genres, lyrics, files, podcasts, videos, radio | 22 | [[k05-data-detail]] |
| 4 | `coremusic_albums` | Album collections, discs, stats | 5 | [[k05-data-detail]] |
| 5 | `coremusic_playlist` | User and AI playlists, collaborators, followers | 5 | [[k05-data-detail]] |
| 6 | `coremusic_catalog` | Music catalog, metadata index | 4 | [[k05-data-detail]] |
| 7 | `coremusic_logs` | Application logs, audit trail | 6 | [[k05-data-detail]] |
| 8 | `coremusic_media` | Media files, thumbnails, metadata | 5 | [[k05-data-detail]] |
| 9 | `coremusic_system` | System config, feature flags | 4 | [[k05-data-detail]] |
| 10 | `coremusic_social` | Social features, shares, comments | 6 | [[k05-data-detail]] |
| 11 | `coremusic_wireless` | Wireless device registry, pairing | 3 | [[k05-data-detail]] |
| 12 | `coremusic_ai` | AI models, predictions, training data | 5 | [[k05-data-detail]] |
| 13 | `coremusic_api` | API keys, rate limits, analytics | 3 | [[k05-data-detail]] |
| 14 | `coremusic_cms` | Content management, pages, blocks | 4 | [[k05-data-detail]] |
| 15 | `coremusic_download` | Download queue, progress, history | 5 | [[k05-data-detail]] |
| 16 | `coremusic_neva` | Neva engine presets, EQ curves | 3 | [[k05-data-detail]] |
| 17 | `coremusic_studio` | Studio session, recordings | 4 | [[k05-data-detail]] |
| 18 | `coremusic_patch` | Patch notes, versioning | 2 | [[k05-data-detail]] |

### 2.2 Toplam Tablo Sayısı

| Metrik | Değer |
|--------|-------|
| Toplam Veritabanı | 18 |
| Toplam Tablo | ~112 |
| Normalizasyon | BCNF (Boyce-Codd) |
| Motor | InnoDB |
| Charset | utf8mb4_unicode_ci |

---

## 3. Database Detayları

### 3.1 coremusic_auth (13 Tablo)

| Tablo | Amaç |
|-------|------|
| `users` | User accounts |
| `roles` | RBAC roles |
| `user_roles` | User-role mapping |
| `sessions` | Active sessions |
| `tokens` | JWT/refresh tokens |
| `credential_vault` | AES-256-GCM encrypted credentials |
| `api_keys` | API key management |
| `oauth_providers` | OAuth2 provider config |
| `login_history` | Authentication audit |
| `password_resets` | Password reset tokens |
| `two_factor` | 2FA settings |
| `security_log` | Security events |
| `ip_blacklist` | IP blacklist |

### 3.2 coremusic_musics (22 Tablo)

| Tablo | Amaç |
|-------|------|
| `songs` | Song metadata |
| `artists` | Artist profiles |
| `genres` | Genre taxonomy |
| `lyrics` | Song lyrics |
| `files` | Audio file references |
| `podcasts` | Podcast metadata |
| `podcast_episodes` | Podcast episodes |
| `videos` | Video metadata |
| `radio_stations` | Radio station registry |
| `radio_streams` | Radio stream URLs |
| `music_genres` | Song-genre mapping |
| `music_artists` | Song-artist mapping |
| `similar_music` | Similarity matrix |
| `music_tags` | Custom tags |
| `music_plays` | Play history |
| `music_ratings` | User ratings |
| `music_favorites` | User favorites |
| `music_downloads` | Download history |
| `music_analytics` | Play analytics |
| `music_waveform` | Pre-computed waveforms |
| `music_spectrum` | Spectrum data |
| `music_chapters` | Chapter markers |

### 3.3 coremusic_user (7 Tablo)

| Tablo | Amaç |
|-------|------|
| `profiles` | User profiles |
| `preferences` | User settings |
| `history` | Listening history |
| `favorites` | Favorite tracks |
| `playlists` | User playlists |
| `following` | Social following |
| `notifications` | User notifications |

### 3.4 coremusic_ai (5 Tablo)

| Tablo | Amaç |
|-------|------|
| `models` | ML model registry |
| `predictions` | AI predictions cache |
| `training_data` | Training dataset references |
| `feature_vectors` | Pre-computed features |
| `model_versions` | Model version history |

---

## 4. Cache Layers (6 Bileşen)

### 4.1 Redis Session Store

| Özellik | Değer |
|---------|-------|
| Amaç | Session data storage |
| TTL | 24h default |
| Serialization | JSON |
| Kullanım | PHP session handler |

### 4.2 Redis Query Cache

| Özellik | Değer |
|---------|-------|
| Amaç | Database query result cache |
| TTL | 5min — 1h (configurable) |
| Strategy | Cache-aside |
| Kullanım | Expensive query caching |

### 4.3 Redis Rate Limit

| Özellik | Değer |
|---------|-------|
| Amaç | API rate limiting |
| Algorithm | Sliding window |
| Limit | 60 req/60s (default) |
| Kullanım | API endpoint protection |

### 4.4 APCu Opcode Cache

| Özellik | Değer |
|---------|-------|
| Amaç | PHP opcode caching |
| TTL | 0 (permanent until restart) |
| Kullanım | PHP script caching |

### 4.5 APCu User Cache

| Özellik | Değer |
|---------|-------|
| Amaç | Application-level cache |
| TTL | 5min — 1h |
| Kullanım | Frequently accessed data |

### 4.6 Memcached (Fallback)

| Özellik | Değer |
|---------|-------|
| Amaç | Distributed cache fallback |
| Kullanım | When Redis unavailable |

### 4.7 Cache Hierarchy

```
┌──────────────────────────────────────────────────────┐
│                 CACHE HIERARCHY                       │
│                                                      │
│  L1: APCu (opcode + user)    ← Fastest (local)     │
│       │                                              │
│       ▼                                              │
│  L2: Redis (session + query) ← Fast (network)      │
│       │                                              │
│       ▼                                              │
│  L3: Memcached (fallback)    ← Medium (network)    │
│       │                                              │
│       ▼                                              │
│  L4: MySQL (source of truth) ← Slowest (disk)      │
└──────────────────────────────────────────────────────┘
```

---

## 5. File System (2 Bileşen)

### 5.1 File System Local

| Özellik | Değer |
|---------|-------|
| Amaç | Local disk storage |
| Kullanım | Audio files, thumbnails, cache |
| Organizasyon | `/storage/{date}/{hash}/{file}` |
| Referans | League Flysystem |

### 5.2 File System S3

| Özellik | Değer |
|---------|-------|
| Amaç | Cloud object storage |
| Kullanım | Backup, CDN, distributed storage |
| Protocol | S3-compatible (AWS S3, MinIO) |

---

## 6. Offline Queue (1 Bileşen)

### 6.1 SQLite Offline Queue

| Özellik | Değer |
|---------|-------|
| Amaç | Offline operation queue |
| Kullanım | Sync when online |
| Tablo | `queue (id, action, payload, status, created)` |

---

## 7. MySQL Features (5 Bileşen)

### 7.1 FULLTEXT Search

| Özellik | Değer |
|---------|-------|
| Amaç | Full-text search |
| Motor | InnoDB FULLTEXT index |
| Dil | Turkish, English |
| Kullanım | Song/artist/album search |

### 7.2 MySQL Replication

| Özellik | Değer |
|---------|-------|
| Tip | Primary-Replica |
| Kullanım | Read scaling, backup |

### 7.3 MySQL Partitioning

| Özellik | Değer |
|---------|-------|
| Tip | Range partitioning |
| Kolon | `created_at` |
| Kullanım | Large table performance |

### 7.4 MySQL Backup

| Özellik | Değer |
|---------|-------|
| Amaç | Automated backup |
| Frequency | Daily full, hourly incremental |
| Tool | Restic + mysqldump |

### 7.5 InnoDB Cluster

| Özellik | Değer |
|---------|-------|
| Amaç | High availability |
| Kullanım | Production deployment |

---

## 8. Backup & Migration (4 Bileşen)

### 8.1 Backup Restic

| Özellik | Değer |
|---------|-------|
| Tool | Restic |
| Encryption | AES-256 |
| Kullanım | Database + file backup |
| Referans | https://github.com/restic/restic |

### 8.2 Data Archive

| Özellik | Değer |
|---------|-------|
| Amaç | Eski veri arşivleme |
| Format | Compressed archive |
| Kullanım | Log rotation, old data archival |

### 8.3 Data Export

| Özellik | Değer |
|---------|-------|
| Format | CSV, JSON |
| Kullanım | User data export, backup |

### 8.4 Data Import

| Özellik | Değer |
|---------|-------|
| Format | CSV, JSON |
| Kullanım | Migration, bulk import |

---

## 9. Data Security (3 Bileşen)

### 9.1 Encryption at Rest

| Özellik | Değer |
|---------|-------|
| Algoritma | AES-256-GCM |
| Standard | NIST SP 800-38D |
| Kullanım | Credential vault, sensitive data |
| Referans | [[paragonie/halite]] |

### 9.2 Encryption in Transit

| Özellik | Değer |
|---------|-------|
| Protocol | TLS 1.3 |
| Kullanım | All database connections, API calls |

### 9.3 Data Masking

| Özellik | Değer |
|---------|-------|
| Amaç | Sensitive data masking |
| Kullanım | Debug logs, non-production environments |

---

## 10. Veri Akış Diyagramı

```
┌─────────────────────────────────────────────────────────────────────┐
│                    DATA FLOW ARCHITECTURE                           │
│                                                                     │
│  ┌──────────┐    ┌──────────┐    ┌──────────┐    ┌──────────┐    │
│  │ Frontend │───>│ API      │───>│ Service  │───>│ Database │    │
│  │ (L3)     │    │ Gateway  │    │ Layer    │    │ (L0)     │    │
│  └──────────┘    │ (L2)     │    │ (L5)     │    └──────────┘    │
│                  └──────────┘    └──────────┘         │           │
│                       │               │               │           │
│                       ▼               ▼               ▼           │
│                  ┌──────────┐    ┌──────────┐    ┌──────────┐    │
│                  │ Rate     │    │ Cache    │    │ Backup   │    │
│                  │ Limiter  │    │ (Redis)  │    │ (Restic) │    │
│                  └──────────┘    └──────────┘    └──────────┘    │
│                                                                     │
│  Data Layers:                                                      │
│  ┌─────────────────────────────────────────────────────────────┐   │
│  │ L0: MySQL (18 BCNF) + Redis + APCu + File System           │   │
│  │ L1: Security (Encryption, Masking, Audit)                  │   │
│  │ L5: Service Layer (Repository Pattern)                     │   │
│  └─────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 11. Bileşen Sayacı

| # | Bileşen | Kategori | Toplam |
|---|---------|----------|--------|
| 1 | coremusic_auth | Database | 1 |
| 2 | coremusic_user | Database | 1 |
| 3 | coremusic_musics | Database | 1 |
| 4 | coremusic_albums | Database | 1 |
| 5 | coremusic_playlist | Database | 1 |
| 6 | coremusic_catalog | Database | 1 |
| 7 | coremusic_logs | Database | 1 |
| 8 | coremusic_media | Database | 1 |
| 9 | coremusic_system | Database | 1 |
| 10 | coremusic_social | Database | 1 |
| 11 | coremusic_wireless | Database | 1 |
| 12 | coremusic_ai | Database | 1 |
| 13 | coremusic_api | Database | 1 |
| 14 | coremusic_cms | Database | 1 |
| 15 | coremusic_download | Database | 1 |
| 16 | coremusic_neva | Database | 1 |
| 17 | coremusic_studio | Database | 1 |
| 18 | coremusic_patch | Database | 1 |
| 19 | Redis Session Store | Cache | 1 |
| 20 | Redis Query Cache | Cache | 1 |
| 21 | Redis Rate Limit | Cache | 1 |
| 22 | APCu Opcode Cache | Cache | 1 |
| 23 | APCu User Cache | Cache | 1 |
| 24 | Memcached (Fallback) | Cache | 1 |
| 25 | File System Local | File | 1 |
| 26 | File System S3 | File | 1 |
| 27 | SQLite Offline Queue | Queue | 1 |
| 28 | MySQL FULLTEXT | MySQL | 1 |
| 29 | MySQL Replication | MySQL | 1 |
| 30 | MySQL Partitioning | MySQL | 1 |
| 31 | MySQL Backup | MySQL | 1 |
| 32 | InnoDB Cluster | MySQL | 1 |
| 33 | Backup Restic | Backup | 1 |
| 34 | Data Archive | Backup | 1 |
| 35 | Data Export CSV/JSON | Backup | 1 |
| 36 | Data Import CSV/JSON | Backup | 1 |
| 37 | Encryption at Rest | Security | 1 |
| 38 | Encryption in Transit | Security | 1 |
| 39 | Data Masking | Security | 1 |
| | **TOPLAM** | | **39** |

> **Not:** Ana bileşen 39 + 16 alt tablo detayı = 55 toplam bileşen.

---

## 12. GitHub Referansları

| Bileşen | Repository | URL |
|---------|-----------|-----|
| MySQL Server | mysql/mysql-server | https://github.com/mysql/mysql-server |
| Redis | redis/redis | https://github.com/redis/redis |
| Koel (ref) | koel/koel | https://github.com/koel/koel |
| Restic | restic/restic | https://github.com/restic/restic |
| Flysystem | thephpleague/flysystem | https://github.com/thephpleague/flysystem |
| Doctrine DBAL | doctrine/dbal | https://github.com/doctrine/dbal |
| Phinx | robmorgan/phinx | https://github.com/robmorgan/phinx |

---

## 13. İlgili Dosyalar

| Dosya | İlişki |
|-------|--------|
| [[k0-os-layer]] | K5 dosya sistemi ve cache'i barındırır |
| [[k4-ai-layer]] | K5 AI model verilerini depolar |
| [[k05-data-detail]] | Auth DB şeması |
| [[k05-data-detail]] | Music DB şeması |
| [[k05-data-detail]] | User DB şeması |

---

## Class AB Veri Entegrasyonu

- [[electronics/bom-classab]] — BOM veritabanı
- [[electronics/pcb-classab]] — PCB üretim verileri

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Version:** 1.0.0
**Status:** draft
**Mode:** Red Team · Human Mode · Truth Mode

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
