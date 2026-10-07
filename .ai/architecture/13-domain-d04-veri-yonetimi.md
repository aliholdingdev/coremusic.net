---
title: "13-domain-d04-veri-yonetimi — Mimari Domain Tablosu d04"
type: docs
category: architecture
version: 1.0.0
status: active
authority: "SSOT: .ai/architecture/00-master-index.md (giriş) · bu dosya: d04 katman tablosunun tek kaynağı"
updated: 2026-10-07
tier: 3
domain: architecture-d04
ssot: true
risk: medium
owner: "Vault Steward"
depends-on: ["architecture/00-master-index.md"]
---

# 13-domain-d04-veri-yonetimi — Domain d04: Veri Yönetimi (K150–K199)

> **Kapsam:** 18 coremusic_* BCNF veritabanı (+2 sistem), veri erişim katmanı (PDO/Repository), cache zinciri, şema/migration kararları.
> **Eski dizin karşılığı:** [[architecture/k5-veri-yonetimi]] · **Legacy K5** → bu domain.
> **Gerçeklik notu (2026-10-07):** `.ai/.sql/mysql/` = **20 dosya / 173 CREATE TABLE** (glob+grep). 18'lik BCNF sayımı (156 tablo) `media_catalog` ve `novasearch`'ı KAPSAMAZ (`.ai/CLAUDE.md` §18 notu). Redis adapter **yok** (PLANNED).

## Katman Tablosu (K150–K199 · 50 katman)

| K-id | Katman adı | Durum | Kanıt |
|------|-----------|-------|-------|
| K150 | coremusic_auth (users, roles, sessions, tokens, api_keys) | IMPLEMENTED | .ai/.sql/mysql/coremusic_auth.sql |
| K151 | coremusic_user (profiles, preferences, history) | IMPLEMENTED | .ai/.sql/mysql/coremusic_user.sql |
| K152 | coremusic_musics (songs, artists, genres, lyrics, podcast, video, radio) | IMPLEMENTED | .ai/.sql/mysql/coremusic_musics.sql |
| K153 | coremusic_albums | IMPLEMENTED | .ai/.sql/mysql/coremusic_albums.sql |
| K154 | coremusic_playlist | IMPLEMENTED | .ai/.sql/mysql/coremusic_playlist.sql |
| K155 | coremusic_catalog (reference data) | IMPLEMENTED | .ai/.sql/mysql/coremusic_catalog.sql |
| K156 | coremusic_logs (app log, audit, analytics) | IMPLEMENTED | .ai/.sql/mysql/coremusic_logs.sql |
| K157 | coremusic_media (device sync, file metadata) | IMPLEMENTED | .ai/.sql/mysql/coremusic_media.sql |
| K158 | coremusic_system (settings, config, i18n) | IMPLEMENTED | .ai/.sql/mysql/coremusic_system.sql |
| K159 | coremusic_social (comments, rooms, notifications) | IMPLEMENTED | .ai/.sql/mysql/coremusic_social.sql |
| K160 | coremusic_wireless (WiFi + Bluetooth) | IMPLEMENTED | .ai/.sql/mysql/coremusic_wireless.sql |
| K161 | coremusic_ai (preferences, features, reco) | IMPLEMENTED | .ai/.sql/mysql/coremusic_ai.sql |
| K162 | coremusic_api (keys, rate limits, webhooks) | IMPLEMENTED | .ai/.sql/mysql/coremusic_api.sql |
| K163 | coremusic_cms (pages, blog, banners) | IMPLEMENTED | .ai/.sql/mysql/coremusic_cms.sql |
| K164 | coremusic_download (queue, history, cache) | IMPLEMENTED | .ai/.sql/mysql/coremusic_download.sql |
| K165 | coremusic_neva (EQ presets, routing matrix) | IMPLEMENTED | .ai/.sql/mysql/coremusic_neva.sql |
| K166 | coremusic_studio (sessions, tracks, equipment) | IMPLEMENTED | .ai/.sql/mysql/coremusic_studio.sql |
| K167 | coremusic_patch (schema versions, migrations) | IMPLEMENTED | .ai/.sql/mysql/coremusic_patch.sql |
| K168 | media_catalog (9 tablo — canlıda YOK) | PLANNED | .ai/.sql/mysql/media_catalog.sql · .ai/CLAUDE.md §18 (deploy edilmedi notu) |
| K169 | novasearch (7 tablo, knex — 18'lik sayım dışı) | IMPLEMENTED | .ai/.sql/mysql/novasearch.sql (salt-okunur dump 2026-10-07) |
| K170 | PDO / raw SQL erişimi (ORM yasak) | IMPLEMENTED | ADR-002 · shared/src/Database/ |
| K171 | Database bağlantı/konfigürasyon katmanı | IMPLEMENTED | shared/src/Database/DatabaseManager.php · DatabaseRegistry.php · shared/src/Database/Config/ |
| K172 | Repository katmanı | IMPLEMENTED | shared/src/Repository/ (dizin — 2026-10-07 ls) |
| K173 | APCu cache adapter | IMPLEMENTED | shared/src/Cache/ApcuAdapter.php |
| K174 | CacheManager zinciri (APCu → fallback) | IMPLEMENTED | shared/src/Cache/CacheManager.php · ADR-007 |
| K175 | Page cache adapter | IMPLEMENTED | shared/src/Cache/PageCacheAdapter.php |
| K176 | Memory adapter (fallback) | IMPLEMENTED | shared/src/Cache/MemoryAdapter.php |
| K177 | Cache interface (PSR-6/16 köprüsü) | IMPLEMENTED | shared/src/Cache/CacheInterface.php · shared/composer.json (psr/cache) |
| K178 | Redis cache adapter | PLANNED | ADR-007 · .claude/CLAUDE.md §12 (adapter YOK — grep 0) |
| K179 | BCNF authority (18 DB / 156 tablo) | IMPLEMENTED | ADR-040 · .ai/.sql/mysql/ (20 dosya) |
| K180 | Migration stratejisi (çok-DB) | IMPLEMENTED | ADR-014 · .ai/.sql/mysql/ |
| K181 | SQL normalizasyon stratejisi | IMPLEMENTED | ADR-033 · ADR-041 |
| K182 | Credential vault normalizasyonu | PLANNED | ADR-034 · ⚠️ VERIFICATION REQUIRED (kod kanıtı yok) |
| K183 | Multi-DB sync stratejisi | PLANNED | ADR-050 · ⚠️ VERIFICATION REQUIRED (kod kanıtı yok) |
| K184 | Dual-mode storage stratejisi | PLANNED | ADR-027 · ⚠️ VERIFICATION REQUIRED (kod kanıtı yok) |
| K185 | SQLite (offline queue fallback) | PLANNED | .claude/CLAUDE.md §22 (edge case) · ⚠️ |
| K186 | Restic backup / DR | PLANNED | .claude/CLAUDE.md §5 (K5 satırı) · ⚠️ |
| K187 | Medya dizin ekseni + ULID (dosya arşivi) | PARTIAL | ADR-092 · media.coremusic.net/src/Media/Ulid.php |
| K188 | user_tokens revocation (JWT jti) | IMPLEMENTED | ADR-095 · .ai/.sql/mysql/coremusic_auth.sql (token_type=access) |
| K189 | Rezerve — d04 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K190 | Rezerve — d04 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K191 | Rezerve — d04 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K192 | Rezerve — d04 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K193 | Rezerve — d04 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K194 | Rezerve — d04 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K195 | Rezerve — d04 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K196 | Rezerve — d04 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K197 | Rezerve — d04 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K198 | Rezerve — d04 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |
| K199 | Rezerve — d04 genişletme payı | PLANNED | ⚠️ VERIFICATION REQUIRED |

**Sayım:** 50 satır (K150–K199) · IMPLEMENTED 27 · PARTIAL 1 · PLANNED 22 (2026-10-07 disk ölçümü).

**İlgili ADR:** ADR-002 · ADR-007 · ADR-014 · ADR-027 · ADR-033 · ADR-034 · ADR-040 · ADR-050 · ADR-092 · ADR-095 (`.ai/.decisions/accepted/`)
