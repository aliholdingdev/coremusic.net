---
type: architecture
category: overview
title: "Architecture Master Metadata — Canonical Counts & Rules"
date: 2026-08-15
updated: 2026-08-15
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/architecture/00-overview/architecture-master.md"
---

# Architecture Master Metadata

**Bu dosya, CoreMusic mimarisindeki tüm sayısal değerlerin TEK KAYNAĞIDIR.**  
Diğer dosyalar bu dosyaya referans verir, kendi başlarına sayısal metadata üretmez.

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[brain.md]] · [[index.md]]

---

## 1. Canonical Counts (Tek Kaynak)

| Metric | Value | Source | Last Verified |
|--------|-------|--------|---------------|
| **BCNF Databases** | 18 | `.sql/mysql/*.sql` | 2026-08-15 |
| **ADR Total** | 87 | ADR-001 through ADR-087 | 2026-08-15 |
| **ADR Frozen** | 37 | ADR-001 through ADR-037 | 2026-08-15 |
| **ADR Active** | 50 | ADR-038 through ADR-087 | 2026-08-15 |
| **Architecture Layers** | 7 | L0 through L6 | 2026-08-15 |
| **Panels** | 10 | music, admin, download, media, auth, home, car, studio, pro, landing | 2026-08-15 |
| **Backend Services** | 7 | Control, Media, Audio, Device, Network Audio, AI, Download | 2026-08-15 |
| **Composer Packages** | 1 | tek shared/ + PSR-4 namespace (ADR-085 v3.0) | 2026-08-15 |
| **Middleware Pipeline** | 10 | OriginCheck→Validation (ADR-010/011/012/013/022) | 2026-08-15 |
| **Agent Count** | 11 | MO + 10 specialist | 2026-08-15 |
| **Template Count** | 25 | .ai/.templates/ | 2026-08-15 |
| **Skill Count** | 10 | .opencode/skills/ | 2026-08-15 |
| **Platform Tiers** | 5 | Windows, Linux, macOS, RPi5, ReactOS | 2026-08-15 |
| **Deployment Modes** | 5 | Home, Car, Studio, NAS, DAC | 2026-08-15 |
| **Audio Divisions** | 5 | Hardware, Software, Studio, Consumer, Research | 2026-08-15 |
| **Hard Guardrails** | 16 | CLAUDE.md §7 | 2026-08-15 |

---

## 2. Architecture Layer Model (L0-L6)

### 2.1 Layer Definitions

| Layer | Name | Kapsam | Teknoloji |
|-------|------|--------|-----------|
| **L0** | Infrastructure | Database, cache, filesystem, IPC, credential vault | PDO MySQL, APCu, Redis, AES-256-GCM |
| **L1** | Security | Middleware pipeline, session, auth, CSRF, CSP | Argon2id, RS256, OWASP Top 10 |
| **L2** | Routing | SPA PageRouter, API Gateway, subdomain routing | PHP 8.4, vanilla router |
| **L3** | Presentation | Frontend, UI, DOM, responsive | Vanilla JS ES6+, ITCSS 9-layer, BEM |
| **L4** | Domain | Business rules, entities, value objects, aggregates | DDD, SOLID, Clean Architecture |
| **L5** | Services | Application services, use cases, CQRS, event bus | PHP 8.4, PSR-14 |
| **L6** | Electronics | Hardware, firmware, driver, DSP, audio engine | C++20, JUCE 9, ASIO, XMOS |

### 2.2 Dependency Rules

```
L6 Electronics → L5 Services → L4 Domain → L3 Presentation → L2 Routing → L1 Security → L0 Infrastructure
```

| Kaynak → Hedef | İzinli mi? | Açıklama |
|-----------------|------------|----------|
| L6 → L5 | ✅ | Electronics services'i kullanır |
| L5 → L4 | ✅ | Services domain'i kullanır |
| L4 → L3 | ✅ | Domain presentation'dan bağımsız (interface) |
| L3 → L2 | ✅ | UI routing'i çağırır |
| L2 → L1 | ✅ | Routing security'yi çağırır |
| L1 → L0 | ✅ | Security infrastructure'ı çağırır |
| L0 → L2/L3 | ❌ | Infrastructure asla UI'ı doğrudan çağırmaz |
| L1 → L3 | ❌ | Security asla UI'ı doğrudan çağırmaz |
| L3 → L0 | ❌ | UI asla DB'ye doğrudan erişemez |
| L0 → L4 | ❌ | Infrastructure domain'e bağımlı olamaz |
| L3 → L4 | ❌ | Presentation domain'i doğrudan çağıramaz |

**Layer Violation İhlali:** Tespit edilirse derhal revert + log CRITICAL.

---

## 3. Database Registry (18 BCNF)

| # | Database | Amaç | Tablo Sayısı | SQL File |
|---|----------|------|-------------|----------|
| 1 | `coremusic_auth` | Users, roles, sessions, tokens, credential vault, API keys | 13 | `.sql/mysql/coremusic_auth.sql` |
| 2 | `coremusic_user` | Profiles, preferences, history, favorites | 7 | `.sql/mysql/coremusic_user.sql` |
| 3 | `coremusic_musics` | Songs, artists, genres, lyrics, files, podcasts, videos, radio | 22 | `.sql/mysql/coremusic_musics.sql` |
| 4 | `coremusic_albums` | Album collections, discs, stats | 5 | `.sql/mysql/coremusic_albums.sql` |
| 5 | `coremusic_playlist` | User and AI playlists, collaborators, followers | 5 | `.sql/mysql/coremusic_playlist.sql` |
| 6 | `coremusic_catalog` | Reference data (genres, artist roles, instruments, moods) | 8 | `.sql/mysql/coremusic_catalog.sql` |
| 7 | `coremusic_logs` | Audit trail, analytics, error logs, performance metrics | 22 | `.sql/mysql/coremusic_logs.sql` |
| 8 | `coremusic_media` | Device sync, media metadata, access control | 8 | `.sql/mysql/coremusic_media.sql` |
| 9 | `coremusic_system` | Settings, config, cache, EQ, file manager, notifications, i18n | 17 | `.sql/mysql/coremusic_system.sql` |
| 10 | `coremusic_social` | Comments, shares, activity, listening rooms, notifications | 9 | `.sql/mysql/coremusic_social.sql` |
| 11 | `coremusic_wireless` | WiFi + Bluetooth networks | 5 | `.sql/mysql/coremusic_wireless.sql` |
| 12 | `coremusic_ai` | User preference profiles, listening features, recommendations | 6 | `.sql/mysql/coremusic_ai.sql` |
| 13 | `coremusic_api` | API keys, rate limits, API call logs, webhooks | 4 | `.sql/mysql/coremusic_api.sql` |
| 14 | `coremusic_cms` | Pages, blog, tags, media assets, FAQs, banners | 8 | `.sql/mysql/coremusic_cms.sql` |
| 15 | `coremusic_download` | Download queue, history, cache, source APIs | 4 | `.sql/mysql/coremusic_download.sql` |
| 16 | `coremusic_neva` | EQ presets, DSP settings, routing matrix, spectrum analysis | 4 | `.sql/mysql/coremusic_neva.sql` |
| 17 | `coremusic_studio` | Studio sessions, tracks, presets, equipment | 6 | `.sql/mysql/coremusic_studio.sql` |
| 18 | `coremusic_patch` | Schema versions, migration logs, patches | 3 | `.sql/mysql/coremusic_patch.sql` |
| | **TOPLAM** | | **156** | |

**Kurallar:** ORM yasak (ADR-002), SELECT * yasak, BCNF zorunlu, prepared statement.

---

## 4. ADR Registry

### 4.1 Frozen (001-037) — Değiştirilemez

| ADR | Konu | Kategori |
|-----|------|----------|
| ADR-001 | Vanilla JS + ITCSS, framework yasak | Frontend |
| ADR-002 | PDO mandatory, ORM yasak | Database |
| ADR-003 | 9 BCNF izole veritabanı | Database |
| ADR-004 | Multi-domain SPA mimarisi | Architecture |
| ADR-005 | Zero hallucination, VERIFICATION REQUIRED | Quality |
| ADR-006 | <200ms TTFB, <100ms API | Performance |
| ADR-007 | Cache namespace, Zero Code Before Plan | Infrastructure |
| ADR-008 | Test bypass middleware | Security |
| ADR-009 | Clean URL redirect | Routing |
| ADR-010 | csrf_token key zorunlu | Security |
| ADR-011 | COREMUSIC_SESS, 3600s idle timeout | Security |
| ADR-012 | strict-dynamic, nonce-based CSP | Security |
| ADR-013 | APCu, 60 req/60s | Security |
| ADR-014 | Forward-only, versioned migration | Database |
| ADR-015 | .env dosya okuma stratejisi | Infrastructure |
| ADR-016 | Subdomain routing | Routing |
| ADR-017 | XMOS XU316 + PCM3168A DSP | Audio |
| ADR-018 | Footer player vaporwave | UI |
| ADR-019 | Per-OS Neva Player | Audio |
| ADR-020 | API güvenlik stratejisi | Security |
| ADR-021 | SPA router immutable contract | Routing |
| ADR-022 | AES-256-GCM, Argon2id | Security |
| ADR-023 | Persona bazlı test | Testing |
| ADR-024 | Modüler dokümantasyon | Documentation |
| ADR-025 | 31-band parametrik EQ | Audio |
| ADR-026 | Node.js indirme servisi | Architecture |
| ADR-027 | Hibrit depolama | Infrastructure |
| ADR-028 | Rate limiting + proxy rotasyonu | Download |
| ADR-029 | Sosyal dinleme odaları | Social |
| ADR-030 | AI öneri motoru | AI |
| ADR-031 | PWA + Flutter | Mobile |
| ADR-032 | Versiyonlu IPC sözleşmeleri | Architecture |
| ADR-033 | BCNF normalizasyon | Database |
| ADR-034 | AES-256-GCM credential vault | Security |
| ADR-035 | Prompt engineering standartları | AI |
| ADR-036 | Çoklu proje prompt üretimi | AI |
| ADR-037 | Kablosuz ağ entegrasyonu | Integration |

### 4.2 Active (038-087) — Güncellenebilir

| ADR | Konu | Kategori |
|-----|------|----------|
| ADR-038 | XMOS XU316 + PCM3168A (PCM5122 REDDEDİLMİŞ) | Audio |
| ADR-039 | 7-servis platform mimarisi | Architecture |
| ADR-040 | 18 BCNF veritabanı otoritesi | Database |
| ADR-041 | DB normalizasyon ek bilgi | Database |
| ADR-042 | Vault yeniden yapılandırma | Vault |
| ADR-043 | Auth subdomain konsolidasyonu | Security |
| ADR-044 | Cinsiyet bazlı dinamik tema | UI |
| ADR-045 | Multi-domain view mode | UI |
| ADR-046 | Cross-view state koruma | UI |
| ADR-047 | Login redirect session bridge | Auth |
| ADR-048 | View Transition API entegrasyonu | UI |
| ADR-049 | Startup prompt loader | AI |
| ADR-050 | Multi-DB sync stratejisi | Database |
| ADR-061 | Electronics Architecture (L6 Layer) | Electronics |
| ADR-062 | DSP Pipeline Architecture | Electronics |
| ADR-063 | Hardware Design Standards | Electronics |
| ADR-064 | Electronics Platform Architecture (L0-L6, 5 cihaz, 13 servis) | Electronics |
| ADR-072 | Social DB Schema | Database |
| ADR-073 | Podcast DB Schema | Database |
| ADR-074 | Radio DB Schema | Database |
| ADR-075 | AI DB Schema | Database |
| ADR-076 | Video DB Schema | Database |
| ADR-077 | Studio DB Schema | Database |
| ADR-078 | CMS DB Schema | Database |
| ADR-079 | i18n DB Schema | Database |
| ADR-080 | Electronics Development Workflow | Electronics |
| ADR-083 | SPA Router Architecture (PHP+JS Hybrid) | Routing |
| ADR-084 | API Gateway Architecture (API-First, BFF, CQRS) | Architecture |
| ADR-085 | Shared Library Hybrid (tek shared/ + PSR-4 namespace) | Infrastructure |
| ADR-086 | Event Driven Architecture (PSR-14) | Architecture |
| ADR-087 | Master Implementation Plan | Architecture |

**Not:** ADR-051-058, ADR-060, ADR-065-071, ADR-081-082 henüz oluşturulmamıştır.

---

## 5. Service Registry

### 5.1 Backend Services (7)

| Service | Port | Protocol | Stack |
|---------|------|----------|-------|
| Control Service | 81 | HTTP | PHP 8.4 (Auth, Session, RBAC) |
| Media Service | 5000/6000 | HTTP | PHP + FFmpeg (Library, Metadata) |
| Audio Service | 9741/9742 | REST/WS | C++20 JUCE (Player, DSP, Mixer) |
| Device Service | — | BLE/WiFi/USB | C++20 (Bluetooth, WiFi, USB) |
| Network Audio | — | WebRTC/P2P | C++20 (Streaming, Multi-room) |
| AI Service | — | Internal | PHP + Python (Recommendations) |
| Download Service | 3001 | HTTP/WS | Node.js + TypeScript |

### 5.2 Frontend Panels (10)

| Panel | Port | Stack |
|-------|------|-------|
| music.coremusic.net | 81 | PHP 8.4 + Vanilla JS |
| admin.coremusic.net | 80 | PHP 8.4 |
| download.coremusic.net | 3001 | Node.js + TypeScript |
| media.coremusic.net | 5000/6000 | PHP + FFmpeg |
| auth.coremusic.net | — | PHP 8.4 |
| home.coremusic.net | — | Vanilla JS |
| car.coremusic.net | — | Vanilla JS |
| studio.coremusic.net | — | Vanilla JS |
| pro.coremusic.net | — | Vanilla JS |
| coremusic.net | — | Vanilla JS |

---

## 6. Port Registry

| Port | Service | Protocol |
|------|---------|----------|
| 80 | admin.coremusic.net | HTTP |
| 81 | music.coremusic.net (Control Service) | HTTP |
| 3001 | download.coremusic.net | HTTP/WS |
| 3306 | MySQL 9 (18 BCNF DB) | TCP |
| 5000/6000 | media.coremusic.net | HTTP |
| 9741 | Audio Service (REST) | HTTP |
| 9742 | Audio Service (WebSocket) | WS |

---

## 7. Cross References

| Kaynak | Hedef | İlişki |
|--------|-------|--------|
| architecture-master.md | [[CLAUDE.md]] | Ana sözleşme |
| architecture-master.md | [[brain.md]] | Mimari kararlar |
| architecture-master.md | [[index.md]] | Vault indeksi |
| architecture-master.md | [[architecture/l0-infrastructure]] | L0 detay |
| architecture-master.md | [[architecture/l1-security]] | L1 detay |
| architecture-master.md | [[architecture/l2-routing]] | L2 detay |
| architecture-master.md | [[architecture/l3-presentation]] | L3 detay |
| architecture-master.md | [[architecture/l4-domain]] | L4 detay |
| architecture-master.md | [[architecture/l5-services]] | L5 detay |
| architecture-master.md | [[architecture/l6-electronics]] | L6 detay |

---

**Authority:** Bayram Ali / Vault Steward  
**Last Updated:** 2026-08-15  
**Mode:** Red Team · Human Mode · Truth Mode
