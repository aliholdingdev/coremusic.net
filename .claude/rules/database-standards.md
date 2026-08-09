# Database Standards — CoreMusic

**Authority:** ADR-040, ADR-041
**Last Updated:** 2026-08-06
**Governing Rules:** Red Team • Human Mode • Truth Mode

---

## 1. Architecture: 9 BCNF Databases (ADR-040)

| # | Database | Purpose |
|---|----------|---------|
| 1 | coremusic_auth | Users, roles, sessions, Argon2id hashes |
| 2 | coremusic_user | Profiles, preferences, history |
| 3 | coremusic_musics | Songs, artists, genres, metadata |
| 4 | coremusic_albums | Album collections |
| 5 | coremusic_playlist | User and AI playlists |
| 6 | coremusic_catalog | Download queues, service status |
| 7 | coremusic_logs | Application logs, audit trail |
| 8 | coremusic_media | Media file metadata |
| 9 | coremusic_system | System configuration |

## 2. Mandatory Rules

- **ORM FORBIDDEN** (ADR-002) — raw PDO only
- **SELECT * FORBIDDEN** — explicit column lists required
- **Prepared statements** for all queries
- **BCNF normalization** for all tables (ADR-040)
- **Soft delete** pattern: `is_deleted = 0`
- **Audit trail** with timestamps
- **No hardcoded credentials** — use `.env` or credential vault

## 3. Naming Conventions

| Element | Pattern | Example |
|---------|---------|---------|
| Table names | `coremusic_<domain>` (lowercase, underscore) | `coremusic_musics` |
| Column names | `snake_case` | `created_at` |
| Index names | `idx_<table>_<column>` | `idx_songs_artist_id` |
| Foreign keys | `fk_<table>_<referenced_table>` | `fk_songs_albums` |
| Primary keys | `id` (auto-increment) | `id` |

## 4. Connection

- PDO MySQL driver
- Connection pooling via APCu cache
- Read replicas for heavy read operations
- Transaction isolation level: REPEATABLE READ

## 5. Migration

- Versioned SQL files in `.sql/migrations/`
- Forward-only (no rollback scripts in production)
- Normalization report in `.sql/normalization-report.md`
- ADR-014: Multi-DB migration strategy

## 6. Credential Vault (ADR-022)

- All API keys in `credential_vault` table
- Encrypted with AES-256-GCM (96-bit IV, 16-byte tag)
- Never in code, logs, or vault markdown files
- `[REDACTED]` masking in logs

## 7. Schema Files

| Veritabanı | Dosya |
|------------|-------|
| coremusic_musics | `.sql/coremusic_musics.sql` |
| coremusic_catalog | `.sql/coremusic_catalog.sql` |
| coremusic_user | `.sql/coremusic_user.sql` |
| coremusic_auth | `.sql/coremusic_auth.sql` |
| coremusic_albums | `.sql/coremusic_albums.sql` |
| coremusic_playlist | `.sql/coremusic_playlist.sql` |
| coremusic_media | `.sql/coremusic_media.sql` |
| coremusic_download | `.sql/coremusic_download.sql` |
| coremusic_logs | `.sql/coremusic_logs.sql` |

## 8. Forbidden

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| ORM (Eloquent, Doctrine) | Raw PDO |
| `SELECT *` | Explicit columns |
| Raw SQL concatenation | Prepared statements |
| Hardcoded credentials | `.env` / credential vault |
| Hard delete | Soft delete (`is_deleted = 0`) |
| Auto-increment UUID | Auto-increment `id` |
| Denormalized tables | BCNF normalization |

---

*Database Standards v2.0.0 — CoreMusic Enterprise*
*Last Updated: 2026-08-06*
