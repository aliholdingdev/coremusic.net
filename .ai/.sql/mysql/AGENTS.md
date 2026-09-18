---
title: "CoreMusic â€” .ai/.sql/mysql Agent TalimatlarÄ±"
type: agent-registry
folder: ".ai/.sql/mysql"
category: vault
date: 2026-09-06
status: active
version: 1.0.0
authority: SSOT
---

# .ai/.sql/mysql â€” AGENTS.md

**Zorunlu BaÄŸlantÄ±lar:** [[../AGENTS.md]] *(Ã¼retilecek)*

## 1. AmaÃ§
CanlÄ± MySQL ÅŸema dump'larÄ± (18 BCNF veritabanÄ±, ADR-040): auth, catalog, musics, albums, media, api, cms, download, logs, ai, vb.

## 2. Ä°Ã§erik Envanteri
18 SQL dosyasÄ±: `coremusic_auth.sql`, `coremusic_catalog.sql`, `coremusic_musics.sql`, `coremusic_albums.sql`, `coremusic_media.sql`, `coremusic_api.sql`, `coremusic_cms.sql`, `coremusic_download.sql`, `coremusic_logs.sql`, `coremusic_ai.sql` + 8 ek DB

## 3. Kurallar
1. Dump'lar `mysqldump` Ã§Ä±ktÄ±sÄ±dÄ±r; elle dÃ¼zenleme yasak â€” ÅŸema deÄŸiÅŸikliÄŸi migration ile, dump yenilenir
2. Dump iÃ§inde gerÃ§ek kullanÄ±cÄ± verisi/ÅŸifre hash'i gÃ¶rÃ¼lÃ¼rse DUR + bildir (seed/gdpr sorusu)
3. Åema â†” `05-data/database_master.md` senkronu korunur

## 5. Ä°lgili Kaynaklar
[[../../architecture/k0-k5-software/k5-data-layer/AGENTS.md]] Â· [[../../architecture/sql/AGENTS.md]] Â· [[../../decisions/accepted/ADR-040-database-authority.md]]

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-06

