---
type: decision
id: "003"
title: "ADR-003: Multi-DB 9 BCNF Veritabanı"
category: "database"
status: "frozen"
date: "2026-01-25"
updated: "2026-08-15"
authority: "Data Engineer"
governance: "Red Team · Human Mode · Truth Mode"
supersedes: null
version: 2.0.0
tags: [database, bcnf, multi-db, normalization, frozen]
risk-level: "critical"
references:
  - "[[brain.md]]"
  - "[[CLAUDE.md]]"
  - "[[decisions/accepted/ADR-002-pdo-mandatory-no-orm]]"
  - "[[decisions/accepted/ADR-040-database-authority]]"
  - "[[architecture/l0-infrastructure]]"
---

# ADR-003: Multi-DB 9 BCNF Veritabanı

---

## 1. Executive Summary

### 1.1 Kararın Özeti

CoreMusic, **9 izole BCNF veritabanı** kullanır. Her veritabanı belirli bir domain'i yönetir. Veritabanları birbirinden bağımsızdır ve cross-db query yasaktır. İleride 18 BCNF veritabanına genişletilebilir (ADR-040).

### 1.2 Temel Gerekçe

Tek monolitik veritabanı:
- Güvenlik açığı yaratır (tek point of failure)
- Performans darboğazı yaratır
- Migration karmaşıklığı yaratır
- Backup/restore süreleri uzar

İzole veritabanları:
- Güvenlik izolasyonu
- Performans optimizasyonu
- Bağımsız migration
- Hızlı backup/restore

### 1.3 Beklenen Sonuçlar

- 9 izole BCNF veritabanı
- Cross-db query yasak
- Bağımsız schema yönetimi
- BCNF normalizasyonu

---

## 2. Status

| Alan | Değer |
|------|-------|
| **Durum** | frozen |
| **Versiyon** | 2.0.0 |
| **Oluşturma Tarihi** | 2026-01-25 |
| **Son Güncelleme** | 2026-08-15 |
| **Otorite** | Data Engineer |
| **Risk Seviyesi** | critical |

---

## 3. Context

### 3.1 Veritabanı Listesi

| # | Veritabanı | Amaç | Tablo Sayısı |
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

### 3.2 BCNF Kuralları

| Kural | Açıklama |
|-------|----------|
| BCNF 1 | Her non-key attribute, candidate key'e tam bağımlı |
| BCNF 2 | Candidate key'ler birbirinden bağımsız |
| BCNF 3 | Transitive bağımlılık yok |

### 3.3 Cross-DB Query Yasak

```php
// ❌ YANLIŞ — Cross-db query (ADR-003)
$stmt = $pdo->query(
    'SELECT u.email, m.title
     FROM coremusic_auth.users u
     JOIN coremusic_musics.songs m ON u.id = m.user_id'
);

// ✅ DOĞRU — İki ayrı sorgu
$stmt1 = $authPdo->prepare('SELECT id, email FROM users WHERE id = :id');
$stmt2 = $musicPdo->prepare('SELECT title FROM songs WHERE user_id = :user_id');
```

---

## 4. Decision

### 4.1 Kesin Kurallar

| # | Kural | Durum |
|---|-------|-------|
| 1 | 9 izole veritabanı | ✅ Zorunlu |
| 2 | BCNF normalizasyonu | ✅ Zorunlu |
| 3 | Cross-db query yasak | ❌ Yasak |
| 4 | Soft delete (is_deleted) | ✅ Zorunlu |
| 5 | snake_case naming | ✅ Zorunlu |
| 6 | Explicit column list | ✅ Zorunlu |
| 7 | Prepared statement | ✅ Zorunlu |
| 8 | UUID v7 + INT karisik PK | ✅ Zorunlu |

---

## 5. Alternatives Considered

| Alternatif | Neden Reddedildi |
|------------|------------------|
| Tek monolitik DB | Güvenlik, performans |
| MongoDB | NoSQL, BCNF uyumsuz |
| PostgreSQL | MySQL tercih edildi |

---

## 6. Quality Report

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır** | ~500+ |
| **Status** | Frozen |

---

*ADR-003: Multi-DB 9 BCNF v2.0.0 — CoreMusic Database*
*Authority: Data Engineer · Last Updated: 2026-08-15*
*Status: Frozen · Governance: Red Team · Human Mode · Truth Mode*
