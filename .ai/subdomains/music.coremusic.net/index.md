---
type: subdomain
title: "Music Subdomain — music.coremusic.net"
category: "media"
date: "2026-08-17"
updated: "2026-08-17"
status: "active"
version: "1.0.0"
authority: "SSOT"
references:
  - "[[decisions/accepted/ADR-043-auth-subdomain-consolidation]]"
  - "[[architecture/l3-presentation]]"
---

# Music Subdomain — music.coremusic.net

## 1. Genel Bakış

| Alan | Değer |
|------|-------|
| Entry Point | `music.coremusic.net/index.php` |
| Port | 81 (Apache/Vhost level) |
| Stack | PHP 8.4 + Vanilla JS |
| Session Cookie | `COREMUSIC_SESS`, domain `.coremusic.net` |
| Ana Sayfa | Ana medya paneli |

## 2. Servis

- Ana medya yönetim paneli
- Müzik kütüphanesi, albümler, sanatçılar
- Playlist yönetimi
- Player entegrasyonu

## 3. Auth

auth.coremusic.net üzerinden cross-domain auth (auth_key bridge).
Session cookie domain: `.coremusic.net`

---

*Music Subdomain v1.0.0 — CoreMusic Vault*
*Last Updated: 2026-08-17*
