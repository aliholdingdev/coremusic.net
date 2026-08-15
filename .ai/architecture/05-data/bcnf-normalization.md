---
title: "CoreMusic — BCNF Normalization"
type: architecture
category: database
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — BCNF Normalization

**Zorunlu Bağlantılar:** [[index]] · [[ADR-040-database-authority]] · [[ADR-033-sql-normalization-strategy]]

---

## 1. Amaç

18 BCNF veritabanının normalizasyon kurallarını ve uygulama standartlarını tanımlar.

---

## 2. 18 BCNF Veritabanı

| # | Veritabanı | Amaç | Tablo Sayısı |
|---|------------|------|--------------|
| 1 | coremusic_auth | Users, roles, sessions | 5 |
| 2 | coremusic_user | Profiles, preferences | 4 |
| 3 | coremusic_musics | Songs, artists, genres | 6 |
| 4 | coremusic_albums | Album collections | 3 |
| 5 | coremusic_playlist | User and AI playlists | 4 |
| 6 | coremusic_catalog | Download queues, status | 5 |
| 7 | coremusic_logs | Application logs | 3 |
| 8 | coremusic_media | Media file metadata | 4 |
| 9 | coremusic_system | System configuration | 3 |

---

## 3. BCNF Kuralları

| Kural | Açıklama |
|-------|----------|
| 1NF | Atomic values, no repeating groups |
| 2NF | No partial dependencies |
| 3NF | No transitive dependencies |
| BCNF | Every determinant is a candidate key |

---

## 4. Normalizasyon Standartları

| Standart | Kural |
|----------|-------|
| Primary Key | `id` (BIGINT UNSIGNED AUTO_INCREMENT) |
| Foreign Key | Explicit constraint |
| Naming | snake_case |
| Soft Delete | `is_deleted = 0` |
| Timestamps | `created_at`, `updated_at` |
| Audit | `created_by`, `updated_by` |

---

## 5. Example Schema (coremusic_auth)

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role_id INT UNSIGNED NOT NULL,
    is_deleted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;
```

---

## 6. Query Rules

| Kural | Yasak | Doğru |
|-------|-------|-------|
| SELECT | `SELECT *` | Explicit column list |
| ORM | Eloquent, Doctrine | Raw PDO |
| Injection | String concatenation | Prepared statement |
| Transaction | Auto-commit | Explicit transaction |

---

## 7. Migration Strategy

| Kural | Açıklama |
|-------|----------|
| Forward-only | Geri alım yok |
| Versioned | Her migration versiyonlu |
| Reversible | Down migration zorunlu |
| Tested | Test ortamında önce çalıştır |

---

## 8. Performance

| Optimizasyon | Açıklama |
|--------------|----------|
| Index | WHERE clause columns |
| Composite | Multi-column queries |
| Covering | SELECT column indexes |
| Query plan | EXPLAIN analysis |

---

## 9. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 2 DB | [[ADR-040-database-authority]] | 18 BCNF authority |
| § 5 Schema | [[ADR-033-sql-normalization-strategy]] | SQL normalization |
| § 6 Rules | [[ADR-002-pdo-mandatory-no-orm]] | No ORM rule |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
