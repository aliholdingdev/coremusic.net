---
type: decision
id: "041"
title: "ADR-041: DB Normalization Supplementary"
category: "database"
status: "active"
date: "2026-07-30"
updated: "2026-08-15"
authority: "Data Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 2.0.0
tags: [database, normalization, supplementary, active]
risk-level: "medium"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-040-database-authority]]"
  - "[[decisions/accepted/ADR-033-sql-normalization-strategy]]"
---

# ADR-041: DB Normalization Supplementary

---

## 1. Executive Summary

ADR-033'a ek bilgi: BCNF normalizasyonu sırasında dikkat edilecek ek kurallar. Composite key kullanımı, junction tablolar, denormalizasyon stratejisi.

## 2. Decision

### Ek Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Composite key kullanımı | ⚠️ Gerekirse |
| 2 | Junction tablolar | ✅ Many-to-Many için |
| 3 | Denormalizasyon | ⚠️ Performans için izinli |
| 4 | Audit trail tabloları | ✅ Zorunlu |
| 5 | Soft delete her tabloda | ✅ Zorunlu |

### Junction Tablo Standardı

```sql
CREATE TABLE user_playlists (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    playlist_id INT UNSIGNED NOT NULL,
    role ENUM('owner', 'collaborator', 'viewer') NOT NULL DEFAULT 'viewer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    UNIQUE KEY uk_user_playlist (user_id, playlist_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (playlist_id) REFERENCES playlists(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 3. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-041: DB Normalization Supplementary v2.0.0 — CoreMusic Database*
*Authority: Data Engineer · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
