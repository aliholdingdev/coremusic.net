---
title: "ADR-003: Multi-DB 9 BCNF VeritabanÄ±"
status: frozen
date: 2026-01-25
tags: [database, bcnf, multi-db, normalization, frozen]
---

# ADR-003: Multi-DB 9 BCNF VeritabanÄ±

---

## 1. Executive Summary

### 1.1 KararÄ±n Ã–zeti

CoreMusic, **9 izole BCNF veritabanÄ±** kullanÄ±r. Her veritabanÄ± belirli bir domain'i yÃ¶netir. VeritabanlarÄ± birbirinden baÄŸÄ±msÄ±zdÄ±r ve cross-db query yasaktÄ±r. Ä°leride 18 BCNF veritabanÄ±na geniÅŸletilebilir (ADR-040).

### 1.2 Temel GerekÃ§e

Tek monolitik veritabanÄ±:
- GÃ¼venlik aÃ§Ä±ÄŸÄ± yaratÄ±r (tek point of failure)
- Performans darboÄŸazÄ± yaratÄ±r
- Migration karmaÅŸÄ±klÄ±ÄŸÄ± yaratÄ±r
- Backup/restore sÃ¼releri uzar

Ä°zole veritabanlarÄ±:
- GÃ¼venlik izolasyonu
- Performans optimizasyonu
- BaÄŸÄ±msÄ±z migration
- HÄ±zlÄ± backup/restore

### 1.3 Beklenen SonuÃ§lar

- 9 izole BCNF veritabanÄ±
- Cross-db query yasak
- BaÄŸÄ±msÄ±z schema yÃ¶netimi
- BCNF normalizasyonu

---

## 2. Status

| Alan | DeÄŸer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **OluÅŸturma Tarihi** | 2026-01-25 |
| **Son GÃ¼ncelleme** | 2026-08-15 |
| **Otorite** | Data Engineer |
| **Risk Seviyesi** | critical |

---

## 3. Context

### 3.1 VeritabanÄ± Listesi

| # | VeritabanÄ± | AmaÃ§ | Tablo SayÄ±sÄ± |
|---|------------|------|-------------|
| 1 | coremusic_auth | Users, roles, sessions, tokens | 13 |
| 2 | coremusic_user | Profiles, preferences, history | 7 |
| 3 | coremusic_musics | Songs, artists, genres, lyrics | 22 |
| 4 | coremusic_albums | Album collections, discs | 5 |
| 5 | coremusic_playlist | Playlists, collaborators | 5 |
| 6 | coremusic_catalog | Reference data | 8 |
| 7 | coremusic_logs | Audit trail, analytics | 22 |
| 8 | coremusic_media | Device sync, media metadata | 8 |
| 9 | coremusic_system | Settings, config, cache | 17 |

### 3.2 BCNF KurallarÄ±

| Kural | AÃ§Ä±klama |
|-------|----------|
| BCNF 1 | Her non-key attribute, candidate key'e tam baÄŸÄ±mlÄ± |
| BCNF 2 | Candidate key'ler birbirinden baÄŸÄ±msÄ±z |
| BCNF 3 | Transitive baÄŸÄ±mlÄ±lÄ±k yok |

### 3.3 Cross-DB Query Yasak

```php
// âŒ YANLIÅ â€” Cross-db query (ADR-003)
$stmt = $pdo->query(
    'SELECT u.email, m.title
     FROM coremusic_auth.users u
     JOIN coremusic_musics.songs m ON u.id = m.user_id'
);

// âœ… DOÄRU â€” Ä°ki ayrÄ± sorgu
$stmt1 = $authPdo->prepare('SELECT id, email FROM users WHERE id = :id');
$stmt2 = $musicPdo->prepare('SELECT title FROM songs WHERE user_id = :user_id');
```

---

## 4. Decision

### 4.1 Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | 9 izole veritabanÄ± | âœ… Zorunlu |
| 2 | BCNF normalizasyonu | âœ… Zorunlu |
| 3 | Cross-db query yasak | âŒ Yasak |
| 4 | Soft delete (is_deleted) | âœ… Zorunlu |
| 5 | snake_case naming | âœ… Zorunlu |
| 6 | Explicit column list | âœ… Zorunlu |
| 7 | Prepared statement | âœ… Zorunlu |
| 8 | UUID v7 + INT karisik PK | âœ… Zorunlu |

---

## 5. Alternatives Considered

| Alternatif | Neden Reddedildi |
|------------|------------------|
| Tek monolitik DB | GÃ¼venlik, performans |
| MongoDB | NoSQL, BCNF uyumsuz |
| PostgreSQL | MySQL tercih edildi |

---

## 6. Quality Report

| Metrik | DeÄŸer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **SatÄ±r** | ~500+ |
| **Status** | Frozen |

---

*ADR-003: Multi-DB 9 BCNF v2.0.0 â€” CoreMusic Database*
*Authority: Data Engineer Â· Last Updated: 2026-08-15*
*Status: Frozen Â· Governance: Red Team Â· Human Mode Â· Truth Mode*