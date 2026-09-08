---
title: "CoreMusic — .ai/.sql/mysql Agent Talimatları"
type: agent-registry
folder: ".ai/.sql/mysql"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
---

# .ai/.sql/mysql — AGENTS.md

**Zorunlu Bağlantılar:** [[../AGENTS.md]] *(üretilecek)*

## 1. Amaç
Canlı MySQL şema dump'ları (18 BCNF veritabanı, ADR-040): auth, catalog, musics, albums, media, api, cms, download, logs, ai, vb.

## 2. İçerik Envanteri
18 SQL dosyası: `coremusic_auth.sql`, `coremusic_catalog.sql`, `coremusic_musics.sql`, `coremusic_albums.sql`, `coremusic_media.sql`, `coremusic_api.sql`, `coremusic_cms.sql`, `coremusic_download.sql`, `coremusic_logs.sql`, `coremusic_ai.sql` + 8 ek DB

## 3. Kurallar
1. Dump'lar `mysqldump` çıktısıdır; elle düzenleme yasak — şema değişikliği migration ile, dump yenilenir
2. Dump içinde gerçek kullanıcı verisi/şifre hash'i görülürse DUR + bildir (seed/gdpr sorusu)
3. Şema ↔ `05-data/database_master.md` senkronu korunur

## 5. İlgili Kaynaklar
[[../../architecture/05-data/AGENTS.md]] · [[../../architecture/sql/AGENTS.md]] · [[../../decisions/accepted/ADR-040-database-authority.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06
