---
type: adr
category: database
title: "ADR-072: Social Database Schema"
date: 2026-08-10
updated: 2026-08-10
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-072: Social Database Schema

**Status:** Active (güncellenebilir)
**Kategorisi:** Database
**İlgili Agent:** [[.agents/data-engineer]]
**İlgili Division:** Data Engineering

---

## 1. Amaç

CoreMusic'in sosyal medya özelliklerini destekleyen `coremusic_social` veritabanının şemasını tanımlar. [[ADR-040-database-authority]] ile birlikte çalışır.

---

## 2. Tablolar

| # | Tablo | Amaç | Tahmini Satır |
|---|-------|------|---------------|
| 1 | `comments` | Şarkı/albüm yorumları | 50K |
| 2 | `shares` | Paylaşım kayıtları | 20K |
| 3 | `activity_feed` | Kullanıcı aktivite akışı | 500K |
| 4 | `listening_rooms` | Canlı dinleme odaları | 1K |
| 5 | `room_members` | Oda üyeleri (junction) | 5K |
| 6 | `notifications` | Bildirimler | 1M |

## 3. BCNF Uyumluluğu

| Tablo | Functional Dependency | Candidate Key |
|-------|----------------------|---------------|
| comments | id → {user_id, music_id, content, ...} | id |
| shares | id → {user_id, music_id, platform, ...} | id |
| activity_feed | id → {user_id, activity_type, ...} | id |
| listening_rooms | id → {host_user_id, room_name, ...} | id |
| room_members | id → {room_id, user_id, ...} | (room_id, user_id) UNIQUE |
| notifications | id → {user_id, type, message, ...} | id |

## 4. Cross-DB Referansları

| Kaynak Tablo | Hedef DB | Hedef Tablo |
|--------------|----------|-------------|
| comments.user_id | coremusic_auth | users |
| comments.music_id | coremusic_musics | musics |
| shares.user_id | coremusic_auth | users |
| activity_feed.user_id | coremusic_auth | users |
| listening_rooms.host_user_id | coremusic_auth | users |
| notifications.user_id | coremusic_auth | users |

## 5. OWASP 2025 Uyumluluğu

| OWASP Kuralı | Uygulama |
|--------------|----------|
| A01: Broken Access Control | Yorum/pAYLAŞIM silme: sadece sahibi |
| A03: Injection | Prepared statement zorunlu |
| A04: Insecure Design | Rate limiting: yorum/saat limiti |
| A07: Auth Failures | Session-based auth zorunlu |

---

## 6. İlgili ADR'ler

| ADR | İlişki |
|-----|--------|
| [[ADR-003-multi-db-9-databases]] | DB mimarisi |
| [[ADR-002-pdo-mandatory-no-orm]] | PDO zorunlu |
| [[ADR-040-database-authority]] | DB otoritesi |
| [[ADR-041-database-normalization-supplementary]] | BCNF kuralları |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-10
**Mode:** Red Team · Human Mode · Truth Mode
