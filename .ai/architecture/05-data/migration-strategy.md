---
type: architecture
category: data-migration
title: "CoreMusic — Migration Strategy"
date: 2026-08-09
updated: 2026-08-09
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Migration Strategy

**See also:** [[architecture/05-data/database_master]] · [[ADR-014-multi-db-migration-strategy]] · [[ADR-033-sql-normalization-strategy]]

---

## 1. Amaç

Migration Strategy, CoreMusic platformunun 18 BCNF veritabanı için şema değişikliklerini versiyonlu, geri alınabilir ve güveli bir şekilde yönetme stratejisini tanımlar.

---

## 2. Migration Kuralları

| Kural | Açıklama | ADR |
|-------|----------|-----|
| Forward-only | Geri migration yasak | [[ADR-014]] |
| Versioned | Her migration bir versiyon | [[ADR-014]] |
| Atomic | Her migration tek transaction | — |
| Tested | Migration test edilmeli | — |
| Documented | Her migration açıklamalı | — |

---

## 3. Migration Dosya Yapısı

```
.sql/
├── migrations/
│   ├── 001_create_users_table.sql
│   ├── 002_add_email_index.sql
│   ├── 003_create_devices_table.sql
│   └── ...
└── seeds/
    ├── 001_seed_roles.sql
    └── 002_seed_admin.sql
```

---

## 4. Migration Format

```sql
-- Migration: 003_create_devices_table
-- Date: 2026-08-09
-- Author: backend-architect
-- Description: Create device management tables

BEGIN TRANSACTION;

CREATE TABLE IF NOT EXISTS devices (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    device_type VARCHAR(50) NOT NULL,
    device_name VARCHAR(100) NOT NULL,
    serial_number VARCHAR(100) UNIQUE,
    firmware_version VARCHAR(20) NOT NULL,
    driver_version VARCHAR(20) NOT NULL,
    status ENUM('online', 'offline', 'error') DEFAULT 'offline',
    last_seen_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) DEFAULT 0,
    INDEX idx_user_id (user_id),
    INDEX idx_device_type (device_type),
    INDEX idx_status (status),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;
```

---

## 5. 9 Database Migration Sırası

| Sıra | Veritabanı | Migration |
|------|------------|-----------|
| 1 | coremusic_auth | Users, roles, sessions |
| 2 | coremusic_user | Profiles, preferences |
| 3 | coremusic_musics | Songs, artists, genres |
| 4 | coremusic_albums | Albums |
| 5 | coremusic_playlist | Playlists |
| 6 | coremusic_catalog | Download queues |
| 7 | coremusic_logs | Audit logs |
| 8 | coremusic_media | Media metadata |
| 9 | coremusic_system | Configuration |

---

## 6. Migration Komutları

```bash
# Tüm pending migration'ları çalıştır
php bin/migrate.php migrate

# Belirli bir migration'ı çalıştır
php bin/migrate.php migrate --target=003

# Son durumu göster
php bin/migrate.php status

# Migration logunu göster
php bin/migrate.php log
```

---

## 7. Rollback Stratejisi

| Durum | Aksiyon |
|-------|---------|
| Migration başarısız | Transaction rollback |
| Yeni migration hatalı | Yeni migration ile düzelt (forward-only) |
| Veri kaybı riski | Backup'tan geri yükle |
| Production hata | Hotfix migration |

**Kural:** Geri migration yasak. Sorun varsa yeni migration ile düzeltilir.

---

## 8. Backup Strategy

| Frekans | Kapsam | Saklama |
|---------|--------|---------|
| Her migration öncesi | Tam DB dump | 7 gün |
| Günlük | Incremental | 30 gün |
| Haftalık | Tam backup | 3 ay |
| Aylık | Tam backup | 1 yıl |

---

## 9. Seed Data

```sql
-- Seed: Roles
INSERT INTO roles (name, description) VALUES
('admin', 'Administrator'),
('user', 'Regular User'),
('premium', 'Premium User'),
('device_owner', 'Device Owner'),
('studio', 'Studio User'),
('car', 'Car Audio User'),
('embedded', 'Embedded Device User');
```

---

## 10. ADR Referansları

| ADR | Konu |
|-----|------|
| [[ADR-014-multi-db-migration-strategy]] | Forward-only migration |
| [[ADR-033-sql-normalization-strategy]] | SQL normalizasyon |
| [[ADR-040-database-authority]] | 18 BCNF DB |
| [[ADR-050-multi-db-sync-strategy]] | Multi-DB sync |

---

## 11. Çapraz Referanslar

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| Migration | [[architecture/05-data/database_master]] | DB yapısı |
| Migration | [[architecture/l0-infrastructure]] | Infrastructure katmanı |
| Migration | [[electronic/index]] | Electronics DB şeması |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-09
**Mode:** Red Team · Human Mode · Truth Mode
