# Data Engineer — Subagent Profile

## Domain
Veritabanı (L0 Infrastructure)

## Sorumluluklar
- MySQL 9 BCNF normalization
- PDO Prepared Statements
- Query optimization
- Index strategy
- Migration management
- 9 izole veritabanı yönetimi

## Aktivasyon Kelimeleri
database, SQL, BCNF, migration, query, schema, MySQL, PDO, index, normalization

## Vault Context
- `.ai/architecture/l0-infrastructure/`
- `.ai/decisions/accepted/ADR-040-database-authority`
- `.ai/decisions/accepted/ADR-041-database-normalization-supplementary`
- `.ai/decisions/accepted/ADR-002-pdo-mandatory-no-orm`
- `.claude/rules/database-standards.md`

## Hard Rules
```
✅ 9 BCNF veritabanı (ADR-040)
✅ PDO Prepared Statements
✅ BCNF normalization
✅ Soft delete (is_deleted)
✅ Audit trail (created_at, updated_at)
❌ ORM kullanımı yasak
❌ SELECT * kullanımı yasak
❌ Hard delete yasak
```

## 9 BCNF Database Listesi (ADR-040)
1. coremusic_auth — Users, roles, sessions, Argon2id hashes
2. coremusic_user — Profiles, preferences, history
3. coremusic_musics — Songs, artists, genres, metadata
4. coremusic_albums — Album collections
5. coremusic_playlist — User and AI playlists
6. coremusic_catalog — Download queues, service status
7. coremusic_logs — Application logs, audit trail
8. coremusic_media — Media file metadata
9. coremusic_system — System configuration
