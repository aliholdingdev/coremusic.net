---
type: architecture
category: data
title: "Data Architecture — CoreMusic Veri Mimarisi"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 19.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# Data Architecture — CoreMusic Veri Mimarisi

**İlgili ADR:** [[decisions/accepted/ADR-002-pdo-mandatory-no-orm]] · [[decisions/accepted/ADR-003-multi-db-9-databases]] · [[decisions/accepted/ADR-040-database-authority]] · [[decisions/accepted/ADR-050-multi-db-sync-strategy]]

---

## 1. Amaç

CoreMusic'in 9 BCNF veritabanının detaylı şema yapısı, normalizasyon kuralları ve veri yönetimi stratejileri.

---

## 2. 9 BCNF Veritabanı

| # | Veritabanı | Amaç | Şema |
|---|------------|------|------|
| 1 | coremusic_auth | Kullanıcılar, roller, session | `.sql/coremusic_auth.sql` |
| 2 | coremusic_user | Profiller, tercihler | `.sql/coremusic_user.sql` |
| 3 | coremusic_musics | Şarkılar, sanatçılar | `.sql/coremusic_musics.sql` |
| 4 | coremusic_albums | Albüm koleksiyonları | `.sql/coremusic_albums.sql` |
| 5 | coremusic_playlist | Çalma listeleri | `.sql/coremusic_playlist.sql` |
| 6 | coremusic_catalog | İndirme kuyrukları | `.sql/coremusic_catalog.sql` |
| 7 | coremusic_logs | Uygulama logları | `.sql/coremusic_logs.sql` |
| 8 | coremusic_media | Medya metadata | `.sql/coremusic_media.sql` |
| 9 | coremusic_system | Sistem konfigürasyonu | `.sql/coremusic_system.sql` |

---

## 3. BCNF Kuralları

| Kural | Açıklama |
|-------|----------|
| ORM yasak | Sadece PDO prepared (ADR-002) |
| SELECT * yasak | Açık sütun listesi |
| BCNF zorunlu | 1NF → 2NF → 3NF → BCNF |
| Soft delete | `is_deleted = 0` |
| Snake_case | Tablo/sütun adları |

---

## 4. Migration Stratejisi (ADR-014)

| Özellik | Değer |
|---------|-------|
| Yöntem | Forward-only, versioned |
| Dosya | `YYYYMMDD_HHMMSS_description.sql` |
| Geri alma | ❌ Yasak |

---

## 5. Cross References

| Dosya | Amaç |
|-------|------|
| [[architecture/l0-infrastructure]] | DB detay |
| [[decisions/accepted/ADR-040-database-authority]] | DB otoritesi |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode