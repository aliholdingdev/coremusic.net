---
type: decision
id: "040"
title: "ADR-040: Database Authority (18 BCNF)"
category: "database"
status: "active"
date: "2026-07-25"
updated: "2026-08-15"
authority: "Data Engineer"
governance: "Red Team · Human Mode · Truth Mode"
supersedes: "ADR-003"
version: 2.0.0
tags: [database, bcnf, authority, 18-db, normalization, active]
risk-level: "critical"
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[decisions/accepted/ADR-002-pdo-mandatory-no-orm]]"
  - "[[decisions/accepted/ADR-003-multi-db-9-databases]]"
  - "[[architecture/l0-infrastructure]]"
---

# ADR-040: Database Authority (18 BCNF)

---

## 1. Executive Summary

### 1.1 Kararın Özeti

CoreMusic, **18 izole BCNF veritabanı** ile yönetilir. ADR-003'ü günceller ve 9 DB'den 18 BCNF'ye genişletir. Toplam **156 tablo** bulunur. Her veritabanı BCNF normalized ve bağımsızdır.

### 1.2 18 BCNF Veritabanı

| # | Veritabanı | Tablo | Amaç |
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

### 1.3 Beklenen Sonuçlar

- 18 izole BCNF veritabanı
- 156 tablo
- Cross-db query yasak
- BCNF normalizasyonu zorunlu
- Soft delete zorunlu

---

## 2. Status

| Alan | Değer |
|------|-------|
| **Durum** | active |
| **Versiyon** | 2.0.0 |
| **Oluşturma Tarihi** | 2026-07-25 |
| **Son Güncelleme** | 2026-08-15 |
| **Otorite** | Data Engineer |
| **Risk Seviyesi** | critical |
| **Supersedes** | ADR-003 |

---

## 3. Context

### 3.1 Problem Tanımı

ADR-003 9 veritabanı ile başlamıştı. Ancak sistem büyüdükçe ek domain'ler eklendi:
- Social features (comments, shares, activity)
- AI recommendations
- CMS management
- Download management
- Studio sessions
- i18n support

Bu domain'ler mevcut DB'leri aşırı kalabalıklaştırıyordu.

### 3.2 Genişletme Gerekçesi

| Domain | Mevcut DB | Ek DB | Neden |
|--------|-----------|-------|-------|
| Social | coremusic_social | — | Yeni domain |
| AI | coremusic_ai | — | Yeni domain |
| CMS | coremusic_cms | — | Yeni domain |
| Download | coremusic_download | — | Ayrıştırıldı |
| Neva | coremusic_neva | — | DSP verileri |
| Studio | coremusic_studio | — | Stüdyo verileri |
| Patch | coremusic_patch | — | Migration tracking |

---

## 4. Decision

### 4.1 Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | 18 izole BCNF veritabanı | ✅ Zorunlu |
| 2 | Toplam 156 tablo | ✅ Zorunlu |
| 3 | BCNF normalizasyonu | ✅ Zorunlu |
| 4 | Cross-db query yasak | ❌ Yasak |
| 5 | Soft delete (is_deleted) | ✅ Zorunlu |
| 6 | snake_case naming | ✅ Zorunlu |
| 7 | UUID v7 + INT karisik PK | ✅ Zorunlu |
| 8 | Explicit column list | ✅ Zorunlu |
| 9 | Prepared statement | ✅ Zorunlu |
| 10 | ORM yasak | ❌ Yasak |

### 4.2 Veritabanı Erişim Mimarisi

```
┌─────────────────────────────────────────────────────────────────┐
│                     Database Architecture                        │
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐  │
│  │  Application Layer (PHP 8.4)                              │  │
│  │  • Repository Pattern (Interface + PDO)                   │  │
│  │  • ADR-002: ORM yasak, PDO zorunlu                        │  │
│  └────────────────────────────────────────────────────────────┘  │
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐  │
│  │  Database Connection Layer                                │  │
│  │  • PdoConnectionFactory                                   │  │
│  │  • 18 ayrı PDO connection                                 │  │
│  │  • Prepared statement zorunlu                             │  │
│  └────────────────────────────────────────────────────────────┘  │
│                                                                  │
│  ┌────────────────────────────────────────────────────────────┐  │
│  │  Database Layer (18 BCNF)                                 │  │
│  │                                                            │  │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐    │  │
│  │  │ auth     │ │ user     │ │ musics   │ │ albums   │    │  │
│  │  │ 13 tablo │ │ 7 tablo  │ │ 22 tablo │ │ 5 tablo  │    │  │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘    │  │
│  │                                                            │  │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐    │  │
│  │  │ playlist │ │ catalog  │ │ logs     │ │ media    │    │  │
│  │  │ 5 tablo  │ │ 8 tablo  │ │ 22 tablo │ │ 8 tablo  │    │  │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘    │  │
│  │                                                            │  │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐    │  │
│  │  │ system   │ │ social   │ │ wireless │ │ ai       │    │  │
│  │  │ 17 tablo │ │ 9 tablo  │ │ 5 tablo  │ │ 6 tablo  │    │  │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘    │  │
│  │                                                            │  │
│  │  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐    │  │
│  │  │ api      │ │ cms      │ │ download │ │ neva     │    │  │
│  │  │ 4 tablo  │ │ 8 tablo  │ │ 4 tablo  │ │ 4 tablo  │    │  │
│  │  └──────────┘ └──────────┘ └──────────┘ └──────────┘    │  │
│  │                                                            │  │
│  │  ┌──────────┐ ┌──────────┐                               │  │
│  │  │ studio   │ │ patch    │                               │  │
│  │  │ 6 tablo  │ │ 3 tablo  │                               │  │
│  │  └──────────┘ └──────────┘                               │  │
│  └────────────────────────────────────────────────────────────┘  │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 5. Alternatives Considered

| Alternatif | Neden Reddedildi |
|------------|------------------|
| Tek monolitik DB | Güvenlik, performans |
| MongoDB | NoSQL, BCNF uyumsuz |
| PostgreSQL | MySQL tercih edildi |
| 9 DB (ADR-003) | Yetersiz domain kapsamı |

---

## 6. Consequences

### Olumlu
- Domain izolasyonu
- Performans optimizasyonu
- Bağımsız backup/restore
- BCNF uyumluluğu

### Olumsuz
- Connection yönetimi karmaşıklığı
- Cross-db coordination zorluğu

---

## 7. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Active |

---

*ADR-040: Database Authority (18 BCNF) v2.0.0 — CoreMusic Database*
*Authority: Data Engineer · Last Updated: 2026-08-15*
*Status: Active · Governance: Red Team · Human Mode · Truth Mode*
