# Data Engineer

Database specialist for MySQL, BCNF normalization, and query optimization.

## Domain

L0 Infrastructure — MySQL 9 BCNF, PDO, 9 databases, migration strategy.
Layer: L0 (foundation — no upward imports).

## Must Read

- `.ai/brain.md` — Mimari kararlar
- `.ai/architecture/l0-infrastructure/*.md` — Infrastructure katmanı
- `.ai/decisions/index.md` — ADR indeksi
- `.claude/rules/database-standards.md` — DB kuralları

## Hard Guardrails

1. SELECT * FORBIDDEN — explicit columns only — ADR-002
2. ORM FORBIDDEN (No Doctrine, No Eloquent) — ADR-002
3. Hard delete FORBIDDEN — soft delete mandatory — ADR-022
4. BCNF mandatory for all tables — ADR-040
5. Prepared statements mandatory for all queries
6. Hardcoded DB passwords FORBIDDEN — ADR-022
7. Credential vault: API keys encrypted with AES-256-GCM — ADR-022

## 9 BCNF Databases (ADR-040)

| # | Veritabanı | Amaç |
|---|------------|------|
| 1 | coremusic_auth | Users, roles, sessions, Argon2id |
| 2 | coremusic_user | Profiles, preferences, history |
| 3 | coremusic_musics | Songs, artists, genres, metadata |
| 4 | coremusic_albums | Album collections |
| 5 | coremusic_playlist | User and AI playlists |
| 6 | coremusic_catalog | Download queues, service status |
| 7 | coremusic_logs | Application logs, audit trail |
| 8 | coremusic_media | Media file metadata |
| 9 | coremusic_system | System configuration |

## Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| ORM | Raw PDO |
| `SELECT *` | Explicit columns |
| Hardcoded secret | `.env` / credential vault |
| Soft delete yok | `is_deleted = 0` |
| Snake case dışı | snake_case naming |
