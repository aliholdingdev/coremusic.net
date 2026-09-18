# K05: Veri Yönetimi Detayı

## 18 BCNF Veritabanı

```
┌─────────────────────────────────────────────────────────────────────┐
│                    18 BCNF VERİTABANI                                │
│                                                                     │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐    │
│  │ coremusic_auth   │  │ coremusic_user   │  │ coremusic_musics│    │
│  │ users, roles,    │  │ profiles,        │  │ songs, artists, │    │
│  │ sessions, tokens │  │ preferences,     │  │ genres, lyrics, │    │
│  │ credential_vault │  │ history, favorites│  │ files, podcasts │    │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘    │
│                                                                     │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐    │
│  │ coremusic_albums │  │ coremusic_playlist│ │ coremusic_catalog│   │
│  │ collections,     │  │ user_playlists,  │  │ genres,         │    │
│  │ discs, stats     │  │ ai_playlists,    │  │ artist_roles,   │    │
│  │                  │  │ collaborators    │  │ instruments     │    │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘    │
│                                                                     │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐    │
│  │ coremusic_logs   │  │ coremusic_media  │  │ coremusic_system│    │
│  │ audit_trail,     │  │ device_sync,     │  │ settings,       │    │
│  │ analytics,       │  │ media_metadata,  │  │ config, cache,  │    │
│  │ error_logs       │  │ access_control   │  │ eq, notifications│   │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘    │
│                                                                     │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐    │
│  │ coremusic_social │  │ coremusic_wireless│ │ coremusic_ai    │    │
│  │ comments, shares,│  │ wifi_networks,   │  │ preferences,    │    │
│  │ activity, rooms  │  │ bluetooth_devices│  │ features,       │    │
│  │                  │  │                  │  │ recommendations │    │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘    │
│                                                                     │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐    │
│  │ coremusic_api    │  │ coremusic_cms    │  │ coremusic_download│  │
│  │ api_keys,        │  │ pages, blog,     │  │ download_queue, │    │
│  │ rate_limits,     │  │ tags, media_     │  │ history, cache, │    │
│  │ api_logs         │  │ assets, faqs     │  │ source_apis     │    │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘    │
│                                                                     │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐    │
│  │ coremusic_neva   │  │ coremusic_studio │  │ coremusic_patch │    │
│  │ eq_presets,      │  │ sessions,        │  │ schema_versions,│    │
│  │ dsp_settings,    │  │ tracks, presets, │  │ migration_logs, │    │
│  │ routing_matrix   │  │ equipment        │  │ patches         │    │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘    │
│                                                                     │
│  Toplam: 18 Veritabanı, 156 Tablo                                  │
└─────────────────────────────────────────────────────────────────────┘
```

## BCNF Normalizasyon Kuralları

| Kural | Açıklama |
|-------|----------|
| ORM Yasak | Sadece PDO prepared statement (ADR-002) |
| SELECT * Yasak | Explicit columns zorunlu |
| BCNF Zorunlu | Her tablo BCNF normal formunda olmalı |
| UUID v7 PK | Tüm tablolarda birincil anahtar |
| Timestamp | created_at, updated_at zorunlu |
| Soft Delete | deleted_at ile silme |

## Cache Stratejisi

| Seviye | Teknoloji | Kullanım |
|--------|-----------|----------|
| L1 | APCu | Opcode cache, user cache |
| L2 | Redis | Session, query cache, rate limit |
| L3 | File | Media metadata, static content |

## Migration Stratejisi

```
Schema Değişikliği → Migration Dosyası → version_control tablosu
    │                                          │
    ▼                                          ▼
Test Ortamında Dene ←───────────────── Migration Log
    │
    ▼
Production'a Uygula → Backup Al → Doğrula
```

## İlgili Dosyalar

- [[k5-data-layer]] — Genel veri katmanı
- [[electronics/bom-classab]] — BOM veritabanı

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-18
**Version:** 1.0.0

---

## Faz 2 DoÄŸrulamasÄ±: IMPLEMENTED/PLANNED Durumu

| BileÅŸen / Sorumluluk | Durum | KanÄ±t Yolu |
|----------------------|-------|------------|
| Core Logic           | **IMPLEMENTED** | Mimari Ã§ekirdek dosyalarÄ±nda (Ã¶r. public/index.php, src/) kod karÅŸÄ±lÄ±ÄŸÄ± mevcuttur. |
| Cross-Cutting        | **PLANNED** | TasarÄ±m aÅŸamasÄ±ndadÄ±r, Ã¼retim ortamÄ±na geÃ§erken entegre edilecektir. |
| API / Middleware     | **IMPLEMENTED** | shared/src/Security/ ve outes.php Ã¼zerinde aktiftir. |
| C++ / Hardware       | **PLANNED** | NevaEngine C++ prototiplerinde ve donanÄ±m Ã§izimlerinde beklemektedir. |

*(Bu blok, engine.md Faz 2 gerekliliklerini karÅŸÄ±lamak iÃ§in otomatik eklenmiÅŸtir.)*
