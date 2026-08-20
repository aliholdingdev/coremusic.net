---
title: "ADR-040: Database Authority (18 BCNF)"
status: active
date: 2026-07-25
tags: [database, bcnf, authority, 18-db, normalization, active]
---

# ADR-040: Database Authority (18 BCNF)

---

## 1. Executive Summary

### 1.1 KararÄ±n Ã–zeti

CoreMusic, **18 izole BCNF veritabanÄ±** ile yÃ¶netilir. ADR-003'Ã¼ gÃ¼nceller ve 9 DB'den 18 BCNF'ye geniÅŸletir. Toplam **156 tablo** bulunur. Her veritabanÄ± BCNF normalized ve baÄŸÄ±msÄ±zdÄ±r.

### 1.2 18 BCNF VeritabanÄ±

| # | VeritabanÄ± | Tablo | AmaÃ§ |
|---|------------|-------|------|
| 1 | coremusic_auth | 13 | Users, roles, sessions, tokens, credential vault |
| 2 | coremusic_user | 7 | Profiles, preferences, history, favorites |
| 3 | coremusic_musics | 22 | Songs, artists, genres, lyrics, podcasts, videos, radio |
| 4 | coremusic_albums | 5 | Album collections, discs, stats |
| 5 | coremusic_playlist | 5 | Playlists, collaborators, followers |
| 6 | coremusic_catalog | 8 | Reference data (genres, roles, instruments) |
| 7 | coremusic_logs | 22 | Audit trail, analytics, performance metrics |
| 8 | coremusic_media | 8 | Device sync, media metadata, access control |
| 9 | coremusic_system | 17 | Settings, config, cache, EQ, notifications, i18n |
| 10 | coremusic_social | 9 | Comments, shares, activity, rooms, notifications |
| 11 | coremusic_wireless | 5 | WiFi + Bluetooth networks |
| 12 | coremusic_ai | 6 | User preferences, recommendations, models |
| 13 | coremusic_api | 4 | API keys, rate limits, webhooks |
| 14 | coremusic_cms | 8 | Pages, blog, tags, FAQs, banners |
| 15 | coremusic_download | 4 | Download queue, history, cache |
| 16 | coremusic_neva | 4 | EQ presets, DSP settings, routing matrix |
| 17 | coremusic_studio | 6 | Studio sessions, tracks, presets, equipment |
| 18 | coremusic_patch | 3 | Schema versions, migration logs |
| | **TOPLAM** | **156** | |

### 1.3 Beklenen SonuÃ§lar

- 18 izole BCNF veritabanÄ±
- 156 tablo
- Cross-db query yasak
- BCNF normalizasyonu zorunlu
- Soft delete zorunlu

---

## 2. Status

| Alan | DeÄŸer |
|------|-------|
| **Durum** | active |
| **Versiyon** | 2.0.0 |
| **OluÅŸturma Tarihi** | 2026-07-25 |
| **Son GÃ¼ncelleme** | 2026-08-15 |
| **Otorite** | Data Engineer |
| **Risk Seviyesi** | critical |
| **Supersedes** | ADR-003 |

---

## 3. Context

### 3.1 Problem TanÄ±mÄ±

ADR-003 9 veritabanÄ± ile baÅŸlamÄ±ÅŸtÄ±. Ancak sistem bÃ¼yÃ¼dÃ¼kÃ§e ek domain'ler eklendi:
- Social features (comments, shares, activity)
- AI recommendations
- CMS management
- Download management
- Studio sessions
- i18n support

Bu domain'ler mevcut DB'leri aÅŸÄ±rÄ± kalabalÄ±klaÅŸtÄ±rÄ±yordu.

### 3.2 GeniÅŸletme GerekÃ§esi

| Domain | Mevcut DB | Ek DB | Neden |
|--------|-----------|-------|-------|
| Social | coremusic_social | â€” | Yeni domain |
| AI | coremusic_ai | â€” | Yeni domain |
| CMS | coremusic_cms | â€” | Yeni domain |
| Download | coremusic_download | â€” | AyrÄ±ÅŸtÄ±rÄ±ldÄ± |
| Neva | coremusic_neva | â€” | DSP verileri |
| Studio | coremusic_studio | â€” | StÃ¼dyo verileri |
| Patch | coremusic_patch | â€” | Migration tracking |

---

## 4. Decision

### 4.1 Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | 18 izole BCNF veritabanÄ± | âœ… Zorunlu |
| 2 | Toplam 156 tablo | âœ… Zorunlu |
| 3 | BCNF normalizasyonu | âœ… Zorunlu |
| 4 | Cross-db query yasak | âŒ Yasak |
| 5 | Soft delete (is_deleted) | âœ… Zorunlu |
| 6 | snake_case naming | âœ… Zorunlu |
| 7 | UUID v7 + INT karisik PK | âœ… Zorunlu |
| 8 | Explicit column list | âœ… Zorunlu |
| 9 | Prepared statement | âœ… Zorunlu |
| 10 | ORM yasak | âŒ Yasak |

### 4.2 VeritabanÄ± EriÅŸim Mimarisi

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚                     Database Architecture                        â”‚
â”‚                                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Application Layer (PHP 8.4)                              â”‚  â”‚
â”‚  â”‚  â€¢ Repository Pattern (Interface + PDO)                   â”‚  â”‚
â”‚  â”‚  â€¢ ADR-002: ORM yasak, PDO zorunlu                        â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Database Connection Layer                                â”‚  â”‚
â”‚  â”‚  â€¢ PdoConnectionFactory                                   â”‚  â”‚
â”‚  â”‚  â€¢ 18 ayrÄ± PDO connection                                 â”‚  â”‚
â”‚  â”‚  â€¢ Prepared statement zorunlu                             â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                                  â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚  Database Layer (18 BCNF)                                 â”‚  â”‚
â”‚  â”‚                                                            â”‚  â”‚
â”‚  â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”    â”‚  â”‚
â”‚  â”‚  â”‚ auth     â”‚ â”‚ user     â”‚ â”‚ musics   â”‚ â”‚ albums   â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ 13 tablo â”‚ â”‚ 7 tablo  â”‚ â”‚ 22 tablo â”‚ â”‚ 5 tablo  â”‚    â”‚  â”‚
â”‚  â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜    â”‚  â”‚
â”‚  â”‚                                                            â”‚  â”‚
â”‚  â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”    â”‚  â”‚
â”‚  â”‚  â”‚ playlist â”‚ â”‚ catalog  â”‚ â”‚ logs     â”‚ â”‚ media    â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ 5 tablo  â”‚ â”‚ 8 tablo  â”‚ â”‚ 22 tablo â”‚ â”‚ 8 tablo  â”‚    â”‚  â”‚
â”‚  â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜    â”‚  â”‚
â”‚  â”‚                                                            â”‚  â”‚
â”‚  â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”    â”‚  â”‚
â”‚  â”‚  â”‚ system   â”‚ â”‚ social   â”‚ â”‚ wireless â”‚ â”‚ ai       â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ 17 tablo â”‚ â”‚ 9 tablo  â”‚ â”‚ 5 tablo  â”‚ â”‚ 6 tablo  â”‚    â”‚  â”‚
â”‚  â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜    â”‚  â”‚
â”‚  â”‚                                                            â”‚  â”‚
â”‚  â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”    â”‚  â”‚
â”‚  â”‚  â”‚ api      â”‚ â”‚ cms      â”‚ â”‚ download â”‚ â”‚ neva     â”‚    â”‚  â”‚
â”‚  â”‚  â”‚ 4 tablo  â”‚ â”‚ 8 tablo  â”‚ â”‚ 4 tablo  â”‚ â”‚ 4 tablo  â”‚    â”‚  â”‚
â”‚  â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜    â”‚  â”‚
â”‚  â”‚                                                            â”‚  â”‚
â”‚  â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â” â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”                               â”‚  â”‚
â”‚  â”‚  â”‚ studio   â”‚ â”‚ patch    â”‚                               â”‚  â”‚
â”‚  â”‚  â”‚ 6 tablo  â”‚ â”‚ 3 tablo  â”‚                               â”‚  â”‚
â”‚  â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜ â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜                               â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                                                  â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

---

## 5. Alternatives Considered

| Alternatif | Neden Reddedildi |
|------------|------------------|
| Tek monolitik DB | GÃ¼venlik, performans |
| MongoDB | NoSQL, BCNF uyumsuz |
| PostgreSQL | MySQL tercih edildi |
| 9 DB (ADR-003) | Yetersiz domain kapsamÄ± |

---

## 6. Consequences

### Olumlu
- Domain izolasyonu
- Performans optimizasyonu
- BaÄŸÄ±msÄ±z backup/restore
- BCNF uyumluluÄŸu

### Olumsuz
- Connection yÃ¶netimi karmaÅŸÄ±klÄ±ÄŸÄ±
- Cross-db coordination zorluÄŸu

---

## 7. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Active |

---

*ADR-040: Database Authority (18 BCNF) v2.0.0 â€” CoreMusic Database*
*Authority: Data Engineer Â· Last Updated: 2026-08-15*
*Status: Active Â· Governance: Red Team Â· Human Mode Â· Truth Mode*