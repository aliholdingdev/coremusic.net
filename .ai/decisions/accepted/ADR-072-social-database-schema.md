---
type: decision
id: "072"
title: "ADR-072: Social Database Schema"
category: "database"
status: "active"
date: "2026-08-10"
updated: "2026-08-15"
authority: "Data Engineer"
governance: "Red Team · Human Mode · Truth Mode"
version: 1.0.0
tags: [database, social, schema, active]
risk-level: "medium"
references:
  - "[[brain.md]]"
  - "[[decisions/accepted/ADR-040-database-authority]]"
---

# ADR-072: Social Database Schema

---

## 1. Executive Summary

coremusic_social veritabanı, yorumlar, paylaşımlar, aktivite akışları, dinleme odaları ve bildirimleri yönetir. 9 tablo BCNF normalized.

## 2. Tablolar

| # | Tablo | Amaç |
|---|-------|------|
| 1 | comments | Yorumlar (music, album, playlist) |
| 2 | comment_likes | Yorum beğenileri |
| 3 | shares | Paylaşımlar |
| 4 | activity_feed | Kullanıcı aktivite akışı |
| 5 | listening_rooms | Dinleme odaları |
| 6 | room_participants | Oda katılımcıları |
| 7 | notifications | Bildirimler |
| 8 | notification_preferences | Bildirim tercihleri |
| 9 | user_follows | Kullanıcı takipleri |

## 3. Decision

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | BCNF normalization | ✅ Zorunlu |
| 2 | Soft delete her tabloda | ✅ Zorunlu |
| 3 | Timestamp zorunlu | ✅ Zorunlu |
| 4 | Foreign key constraints | ✅ Zorunlu |
| 5 | Index optimization | ✅ Zorunlu |

### Şema Örneği

```sql
CREATE TABLE comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    commentable_type ENUM('music', 'album', 'playlist', 'podcast') NOT NULL,
    commentable_id BIGINT UNSIGNED NOT NULL,
    parent_id BIGINT UNSIGNED DEFAULT NULL,
    content TEXT NOT NULL,
    likes_count INT UNSIGNED DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_deleted TINYINT(1) NOT NULL DEFAULT 0,
    INDEX idx_commentable (commentable_type, commentable_id),
    INDEX idx_user (user_id),
    INDEX idx_parent (parent_id),
    FOREIGN KEY (user_id) REFERENCES coremusic_user.users(id),
    FOREIGN KEY (parent_id) REFERENCES comments(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 4. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-072: Social Database Schema v1.0.0 — CoreMusic Database*
*Authority: Data Engineer · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
