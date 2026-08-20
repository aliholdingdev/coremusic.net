---
title: "ADR-041: DB Normalization Supplementary"
status: active
date: 2026-07-30
tags: [database, normalization, supplementary, active]
---

# ADR-041: DB Normalization Supplementary

---

## 1. Executive Summary

ADR-033'a ek bilgi: BCNF normalizasyonu sÄ±rasÄ±nda dikkat edilecek ek kurallar. Composite key kullanÄ±mÄ±, junction tablolar, denormalizasyon stratejisi.

## 2. Decision

### Ek Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | Composite key kullanÄ±mÄ± | âš ï¸ Gerekirse |
| 2 | Junction tablolar | âœ… Many-to-Many iÃ§in |
| 3 | Denormalizasyon | âš ï¸ Performans iÃ§in izinli |
| 4 | Audit trail tablolarÄ± | âœ… Zorunlu |
| 5 | Soft delete her tabloda | âœ… Zorunlu |

### Junction Tablo StandardÄ±

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

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-041: DB Normalization Supplementary v2.0.0 â€” CoreMusic Database*
*Authority: Data Engineer Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*