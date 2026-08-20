---
title: "ADR-072: Social Database Schema"
status: active
date: 2026-08-10
tags: [database, social, schema, active]
---

# ADR-072: Social Database Schema

---

## 1. Executive Summary

coremusic_social veritabanÄ±, yorumlar, paylaÅŸÄ±mlar, aktivite akÄ±ÅŸlarÄ±, dinleme odalarÄ± ve bildirimleri yÃ¶netir. 9 tablo BCNF normalized.

## 2. Tablolar

| # | Tablo | AmaÃ§ |
|---|-------|------|
| 1 | comments | Yorumlar (music, album, playlist) |
| 2 | comment_likes | Yorum beÄŸenileri |
| 3 | shares | PaylaÅŸÄ±mlar |
| 4 | activity_feed | KullanÄ±cÄ± aktivite akÄ±ÅŸÄ± |
| 5 | listening_rooms | Dinleme odalarÄ± |
| 6 | room_participants | Oda katÄ±lÄ±mcÄ±larÄ± |
| 7 | notifications | Bildirimler |
| 8 | notification_preferences | Bildirim tercihleri |
| 9 | user_follows | KullanÄ±cÄ± takipleri |

## 3. Decision

### Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | BCNF normalization | âœ… Zorunlu |
| 2 | Soft delete her tabloda | âœ… Zorunlu |
| 3 | Timestamp zorunlu | âœ… Zorunlu |
| 4 | Foreign key constraints | âœ… Zorunlu |
| 5 | Index optimization | âœ… Zorunlu |

### Åema Ã–rneÄŸi

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

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 1.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-072: Social Database Schema v1.0.0 â€” CoreMusic Database*
*Authority: Data Engineer Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*