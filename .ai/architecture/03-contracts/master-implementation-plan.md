---
type: architecture
category: contracts
title: "CoreMusic — Master Implementation Plan"
date: 2026-08-13
updated: 2026-08-13
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
total_sections: 13
target_lines: 2000+
---

# CoreMusic — Master Implementation Plan

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]]

---

## 1. Introduction & Scope

### 1.1 Purpose

This document is the **single source of truth** for implementing the CoreMusic enterprise media platform from scratch. It defines every layer, every service, every database, every package, every security control, and every deployment target — in one place, in dependency order, with acceptance criteria.

**No code is written until this plan is approved.** This is a Hard Gate per ADR-007 (Zero Code Before Plan).

### 1.2 What CoreMusic Is

CoreMusic is a multi-service, multi-subdomain digital media management platform — NOT a music player. It manages:

- Music library (metadata, files, playlists, albums, artists, genres)
- Media vault (storage, streaming, transcoding)
- Download service (Deezer FLAC via deemix, YouTube via yt-dlp)
- Audio engine (C++ Neva Engine, DSP, ASIO/WASAPI, 8.1 surround)
- Device management (home, pro, studio, car — Raspberry Pi 5 embedded)
- User management (roles, permissions, sessions, MFA)
- Admin panel (user management, system config, audit)
- API gateway (central nerve system for all clients)
- Real-time streaming (WebSocket, WebRTC)

### 1.3 What CoreMusic Is NOT

- NOT a single-page application with a monolithic backend
- NOT a Laravel/Symfony application
- NOT a music streaming service (no Spotify-like licensing)
- NOT a social media platform (listening rooms are scope-limited)

### 1.4 Reference Architecture

All architectural decisions are in `.ai/decisions/accepted/` (ADR-001 through ADR-050). Key references:

| ADR | Subject | Status |
|-----|---------|--------|
| ADR-001 | Vanilla JS + ITCSS (no frameworks) | Frozen |
| ADR-002 | PDO mandatory, no ORM | Frozen |
| ADR-003 | 9 BCNF databases | Frozen |
| ADR-004 | Multi-domain SPA architecture | Frozen |
| ADR-010 | CSRF protection strategy | Frozen |
| ADR-011 | Session management | Frozen |
| ADR-012 | CSP nonce + strict-dynamic | Frozen |
| ADR-013 | Rate limiting (APCu) | Frozen |
| ADR-022 | Database hardened security | Frozen |
| ADR-038 | 8.1 sound card chip selection | Active |
| ADR-039 | 7-service platform architecture | Active |
| ADR-040 | 18 BCNF databases | Active |
| ADR-042 | Vault restructuring | Active |
| ADR-044 | Dynamic user theme engine | Active |


### 1.5 Implementation Philosophy

**Reference architecture only.** The old codebase at `C:\www\coremusic.net.old.ref` is read-only for understanding behaviors. No code is copied. All implementations follow:

1. Clean Architecture (L0-L6 layers)
2. SOLID principles
3. DDD (Domain-Driven Design)
4. CQRS (Command Query Responsibility Segregation)
5. Event Driven Architecture
6. Hexagonal Architecture (Ports & Adapters)

### 1.6 Success Criteria

| Criterion | Target |
|-----------|--------|
| Architecture compliance | All layers respect dependency rules |
| Security | OWASP Top 10:2025 compliant |
| Performance | TTFB < 200ms, API < 100ms, audio < 10ms |
| Test coverage | ≥ 80% backend, ≥ 80% frontend |
| Documentation | ADR for every significant decision |
| Deployment | Docker Compose, zero-downtime capable |

---

## 2. Implementation Phases

### 2.1 Phase Overview

```
Phase 1: Foundation (Weeks 1-4)
  └── Shared packages, auth service, API gateway, DB schema

Phase 2: Core Services (Weeks 5-10)
  └── Media service, download service, audio engine stubs

Phase 3: Frontend (Weeks 11-16)
  └── SPA router, auth pages, music pages, admin pages

Phase 4: Integration (Weeks 17-22)
  └── Cross-service communication, WebSocket, real-time

Phase 5: Production (Weeks 23-26)
  └── Docker, CI/CD, monitoring, security audit, launch
```

### 2.2 Phase 1: Foundation (Weeks 1-4)

**Goal:** Establish the infrastructure that all other phases depend on.

#### 2.2.1 Week 1: Project Structure & Shared Library

| Task | Files | Acceptance Criteria |
|------|-------|---------------------|
| Root directory structure | `C:\www\coremusic.net\` | All subdirectories created |
| Shared library scaffolding | `shared/composer.json` | PSR-4 autoloading configured (ADR-085 v3.0) |
| Router contracts | `shared/src/Router/Contracts/` | RouterInterface, RouteDefinitionInterface |
| Security middleware | `shared/src/Security/Middleware/` | 10 middleware (frozen pipeline) |
| Auth domain | `shared/src/Auth/Domain/` | User, Email, UserId, UserRole value objects |
| Bootstrap file | `shared/bootstrap.php` | Autoloader, config loader, error handler |

#### 2.2.2 Week 2: Auth Service Core

| Task | Files | Acceptance Criteria |
|------|-------|---------------------|
| Auth domain entities | `shared/src/Auth/Domain/Entity/` | User entity (immutable, readonly) |
| Auth repository interface | `shared/src/Auth/Domain/Repository/` | UserRepositoryInterface |
| Auth PDO repository | `shared/src/Auth/Infrastructure/Repository/` | PdoUserRepository (no ORM) |
| Password service | `shared/src/Security/Service/` | Argon2id (64MB, t=4, p=2) |
| JWT service | `shared/src/Auth/Infrastructure/Service/` | RS256 encode/decode, key rotation |
| Session manager | `packages/security/src/Middleware/` | COREMUSIC_SESS cookie, 3600s idle |
| Auth API endpoints | `auth.coremusic.net/api/` | login, logout, register, refresh |

#### 2.2.3 Week 3: Security Middleware Pipeline

| Task | Files | Acceptance Criteria |
|------|-------|---------------------|
| Middleware pipeline | `packages/security/src/Middleware/` | 10-layer frozen order |
| CSRF middleware | `packages/security/src/Middleware/CsrfMiddleware.php` | `csrf_token` key, hash_equals |
| Rate limiter | `packages/security/src/Middleware/RateLimiterMiddleware.php` | APCu, 60 req/60s |
| Security headers | `packages/security/src/Middleware/SecurityHeadersMiddleware.php` | CSP nonce, HSTS, X-Frame-Options |
| BypassAuth middleware | `packages/security/src/Middleware/BypassAuthMiddleware.php` | Dev only, `?_bypass=1` |
| CORS configuration | `shared/config/cors.php` | Whitelist: *.coremusic.net |

#### 2.2.4 Week 4: Database & API Gateway

| Task | Files | Acceptance Criteria |
|------|-------|---------------------|
| MySQL schema: coremusic_auth | `.sql/mysql/coremusic_auth.sql` | 12 tables, BCNF compliant |
| MySQL schema: coremusic_user | `.sql/mysql/coremusic_user.sql` | 7 tables, BCNF compliant |
| API gateway setup | `api.coremusic.net/` | Single entry point, middleware pipeline |
| Route definitions | `shared/config/routes.php` | Auth routes registered |
| DI container | `shared/config/container.php` | php-di configured |
| Environment config | `shared/.env.example` | All secrets documented (no real values) |

### 2.3 Phase 2: Core Services (Weeks 5-10)

**Goal:** Build the 7 backend services that power the platform.

#### 2.3.1 Weeks 5-6: Media Service

| Task | Files | Acceptance Criteria |
|------|-------|---------------------|
| Media domain entities | `packages/media/src/Domain/` | MediaFile, Metadata, CoverArt |
| Media repository | `packages/media/src/Infrastructure/` | PdoMediaRepository |
| File storage service | `packages/storage/` | Flysystem abstraction, local + S3 |
| Transcoding service | `media.coremusic.net/` | FFmpeg wrapper, FLAC→MP3, AAC→FLAC |
| Metadata extractor | `media.coremusic.net/` | getID3 integration, ID3/Vorbis parsing |
| Media API endpoints | `media.coremusic.net/api/` | upload, stream, metadata, search |
| Streaming endpoint | `media.coremusic.net/stream/` | Range request support, auth-gated |

#### 2.3.2 Weeks 7-8: Download Service

| Task | Files | Acceptance Criteria |
|------|-------|---------------------|
| Download domain entities | `packages/download/src/Domain/` | DownloadJob, Source, Status |
| Download queue | `packages/queue/` | Redis queue, priority support |
| deemix integration | `download.coremusic.net/` | Deezer FLAC download, ARL token |
| yt-dlp integration | `download.coremusic.net/` | YouTube audio extraction |
| Anti-ban system | `download.coremusic.net/` | Rate limiting, proxy rotation |
| Download API endpoints | `download.coremusic.net/api/` | start, status, cancel, history |
| WebSocket progress | `download.coremusic.net/ws/` | Real-time download progress |

#### 2.3.3 Weeks 9-10: Audio Engine Stubs & Remaining Services

| Task | Files | Acceptance Criteria |
|------|-------|---------------------|
| Audio service stub | `audio.coremusic.net/` | REST API on port 9741, WS on 9742 |
| Device service stub | `device.coremusic.net/` | BLE/WiFi/USB device management |
| Network audio stub | `network.coremusic.net/` | WebRTC/P2P streaming |
| AI service stub | `ai.coremusic.net/` | Recommendation engine interface |
| Health check endpoints | All services | `/health` returns 200 OK |

### 2.4 Phase 3: Frontend (Weeks 11-16)

**Goal:** Build the SPA frontend with Vanilla JS, ITCSS, and responsive design.

#### 2.4.1 Weeks 11-12: SPA Router & Auth Pages

| Task | Files | Acceptance Criteria |
|------|-------|---------------------|
| SPA Router | `assets.coremusic.net/js/Router.js` | History API, pushState, lazy-load |
| Guard pipeline | `assets.coremusic.net/js/guards/` | AuthGuard, RoleGuard, PermissionGuard |
| DOM patcher | `assets.coremusic.net/js/DomPatcher.js` | DOMParser + replaceChildren (no innerHTML) |
| CSRF manager | `assets.coremusic.net/js/CsrfManager.js` | Meta tag + header, post-DOM-patch |
| Login page | `auth.coremusic.net/` | Email + password form, CSRF token |
| Register page | `auth.coremusic.net/` | Registration form with validation |
| Forgot password | `auth.coremusic.net/` | Password reset flow |

#### 2.4.2 Weeks 13-14: Music & Library Pages

| Task | Files | Acceptance Criteria |
|------|-------|---------------------|
| Dashboard page | `music.coremusic.net/` | Recently played, recommendations |
| Albums page | `music.coremusic.net/` | Album grid, cover art, pagination |
| Artists page | `music.coremusic.net/` | Artist list, bio, discography |
| Playlist page | `music.coremusic.net/` | Playlist management, drag-and-drop |
| Player component | `assets.coremusic.net/js/player/` | Play/pause, seek, volume, queue |
| Search component | `assets.coremusic.net/js/search/` | Global search, filters, results |

#### 2.4.3 Weeks 15-16: Admin & Settings Pages

| Task | Files | Acceptance Criteria |
|------|-------|---------------------|
| Admin dashboard | `admin.coremusic.net/` | System stats, user count, storage |
| User management | `admin.coremusic.net/` | List, edit, disable, role assignment |
| System settings | `admin.coremusic.net/` | Config editor, feature flags |
| Download management | `admin.coremusic.net/` | Queue view, retry, cancel |
| Theme engine | `assets.coremusic.net/js/ThemeManager.js` | Gender-based CSS custom properties |
| Device loader | `assets.coremusic.net/js/device-loader.js` | Viewport-based CSS loading |

### 2.5 Phase 4: Integration (Weeks 17-22)

**Goal:** Connect all services, implement real-time features, and ensure cross-domain auth works.

#### 2.5.1 Weeks 17-18: Cross-Domain Auth & SSO

| Task | Files | Acceptance Criteria |
|------|-------|---------------------|
| Cookie-based SSO | All subdomains | COREMUSIC_SESS shared across *.coremusic.net |
| Session validation API | `auth.coremusic.net/api/session` | Check session validity, return user info |
| Device registration | `auth.coremusic.net/api/device` | Register, authenticate, rotate keys |
| CORS configuration | All services | Proper origin whitelist per service |

#### 2.5.2 Weeks 19-20: WebSocket & Real-Time

| Task | Files | Acceptance Criteria |
|------|-------|---------------------|
| WebSocket server | `packages/websocket/` | PSR-7 compatible, channel-based |
| Player state sync | `audio.coremusic.net/ws/` | Real-time playback state |
| Download progress | `download.coremusic.net/ws/` | Progress updates per job |
| Notification system | `packages/notifications/` | Push notifications, in-app |

#### 2.5.3 Weeks 21-22: Event System & CQRS

| Task | Files | Acceptance Criteria |
|------|-------|---------------------|
| Event dispatcher | `packages/events/` | PSR-14 compatible |
| Domain events | All domains | UserRegistered, SongAdded, etc. |
| Command/Query handlers | All services | CQRS pattern implemented |
| Event store (optional) | `packages/events/` | Event sourcing for audit |

### 2.6 Phase 5: Production (Weeks 23-26)

**Goal:** Deploy to production, ensure reliability, security, and performance.

#### 2.6.1 Weeks 23-24: Docker & Deployment

| Task | Files | Acceptance Criteria |
|------|-------|---------------------|
| Dockerfiles | All services | Multi-stage builds, non-root |
| docker-compose.yml | Root | All services orchestrated |
| .dockerignore | All services | Vendor, tests, .env excluded |
| Health checks | All services | `/health` endpoint, proper intervals |
| Nginx/Apache config | All services | Reverse proxy, SSL termination |

#### 2.6.2 Weeks 25-26: Monitoring, Testing & Launch

| Task | Files | Acceptance Criteria |
|------|-------|---------------------|
| Monitoring stack | `packages/monitoring/` | Prometheus metrics, health dashboard |
| Logging infrastructure | `packages/logger/` | PSR-3 Monolog, structured JSON |
| CI/CD pipeline | `.github/workflows/` | Lint, test, security scan, deploy |
| Security audit | `security-audit.md` | OWASP checklist completed |
| Performance testing | Load test results | TTFB < 200ms, API < 100ms |
| Documentation | README.md, CONTRIBUTING.md | Complete setup instructions |

---

## 3. Technical Specifications

### 3.1 Authentication Architecture

#### 3.1.1 Hybrid Auth

```
Browser
    │
    ▼
HttpOnly Secure Session Cookie (COREMUSIC_SESS)
    │
    ▼
Access JWT Token (15min, RS256)
    │
    ▼
Refresh JWT Token (7 days, RS256)
    │
    ▼
auth.coremusic.net
    │
    ▼
Protected Services
```

#### 3.1.2 Session Configuration

| Property | Value |
|----------|-------|
| Name | `COREMUSIC_SESS` |
| HttpOnly | true |
| Secure | true (production) |
| SameSite | Strict |
| Domain | `.coremusic.net` |
| Path | `/` |
| Max-Age | 86400 (24 hours) |
| Idle Timeout | 3600 (1 hour) |
| Absolute Timeout | 86400 (24 hours) |

#### 3.1.3 JWT Configuration

| Property | Access Token | Refresh Token |
|----------|-------------|---------------|
| Algorithm | RS256 | RS256 |
| TTL | 15 minutes | 7 days |
| Issuer | `auth.coremusic.net` | `auth.coremusic.net` |
| Audience | `*.coremusic.net` | `auth.coremusic.net` |
| Claims | sub, iss, exp, iat, jti, roles | sub, iss, exp, iat, jti |
| Key Rotation | 90 days | 90 days |
| Blacklist | Redis/APCu | Redis/APCu |

#### 3.1.4 Password Hashing

| Parameter | Value |
|-----------|-------|
| Algorithm | Argon2id |
| Memory | 64 MB |
| Time Cost | 4 iterations |
| Threads | 2 |
| Pepper | `.env` (AUTH_PEPPER) |

#### 3.1.5 Auth Endpoints

| Method | Endpoint | Rate Limit | Description |
|--------|----------|------------|-------------|
| POST | `/api/login` | 5/60s | Email + password login |
| POST | `/api/logout` | — | Invalidate session + tokens |
| POST | `/api/register` | 3/300s | Create new account |
| POST | `/api/refresh` | 10/60s | Refresh access token |
| POST | `/api/forgot-password` | 3/300s | Send reset email |
| POST | `/api/reset-password` | 3/300s | Reset with token |
| GET | `/api/session-check` | — | Validate current session |
| POST | `/api/device/register` | 10/60s | Register new device |
| POST | `/api/device/auth` | 10/60s | Authenticate device |

#### 3.1.6 RBAC Roles

| Role | music.read | music.write | media.read | media.write | admin.* | system.* |
|------|-----------|-------------|-----------|-------------|---------|----------|
| guest | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| user | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ |
| premium | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| studio | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| car | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ |
| admin | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| system | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |

### 3.2 API Architecture

#### 3.2.1 API Gateway (Central Nerve System)

The API Gateway at `api.coremusic.net` is the single entry point for all API clients. It handles:

- Routing to backend services
- JWT authentication validation
- RBAC authorization enforcement
- Rate limiting (per-user, per-IP)
- Request validation (schema)
- Response normalization
- Correlation ID generation
- Audit logging
- Circuit breaker
- Caching (GET requests)

#### 3.2.2 Contract-First Development

```
OpenAPI Spec → DTO → Contract → Validation → Use Case → Tests → Code
```

**No code is written before the contract is defined.** Every API endpoint must have:
1. OpenAPI 3.1 specification
2. Request DTO (immutable)
3. Response DTO (immutable)
4. Repository interface (port)
5. Use case handler
6. Unit test
7. Integration test

#### 3.2.3 Standard Response Format

**Success:**
```json
{
  "success": true,
  "data": {},
  "meta": {
    "timestamp": "2026-08-13T12:00:00Z",
    "request_id": "req-abc-123",
    "service": "music-api",
    "version": "1.0.0"
  }
}
```

**Error:**
```json
{
  "success": false,
  "error": {
    "code": "MUSIC_SONG_NOT_FOUND",
    "message": "Song not found",
    "details": []
  },
  "meta": {
    "timestamp": "2026-08-13T12:00:00Z",
    "request_id": "req-abc-123",
    "service": "music-api",
    "version": "1.0.0"
  }
}
```

#### 3.2.4 URL Conventions

| Rule | Correct | Wrong |
|------|---------|-------|
| Lowercase | `/api/v1/songs` | `/api/v1/Songs` |
| Plural nouns | `/api/v1/songs` | `/api/v1/song` |
| kebab-case | `/api/v1/songs/{id}/cover-art` | `/api/v1/songs/{id}/coverArt` |
| No verbs | `GET /api/v1/songs` | `POST /api/v1/getSongs` |
| Max nesting | 2 levels | 3+ levels |
| Version prefix | `/api/v1/songs` | `/songs` |

#### 3.2.5 Error Codes

Format: `{SERVICE}_{RESOURCE}_{ERROR_TYPE}`

| Service | Error Code | HTTP Status |
|---------|------------|-------------|
| Auth | `AUTH_USER_NOT_FOUND` | 404 |
| Auth | `AUTH_INVALID_CREDENTIALS` | 401 |
| Auth | `AUTH_RATE_LIMIT_EXCEEDED` | 429 |
| Music | `MUSIC_SONG_NOT_FOUND` | 404 |
| Music | `MUSIC_SONG_INVALID_TITLE` | 422 |
| Media | `MEDIA_FILE_TOO_LARGE` | 413 |
| Download | `DOWNLOAD_QUEUE_FULL` | 503 |
| Playlist | `PLAYLIST_NOT_FOUND` | 404 |
| System | `SYSTEM_SERVICE_UNAVAILABLE` | 503 |

#### 3.2.6 Rate Limits

| Endpoint | Limit | Window |
|----------|-------|--------|
| Login | 5 | 60s |
| Register | 3 | 300s |
| Password reset | 3 | 300s |
| Token refresh | 10 | 60s |
| API (auth required) | 120 | 60s |
| Default | 60 | 60s |

### 3.3 Database Architecture

#### 3.3.1 18 BCNF Databases (ADR-040)

| # | Database | Tables | Purpose |
|---|----------|--------|---------|
| 1 | coremusic_auth | 12 | Users, roles, sessions, tokens, credential vault, API keys |
| 2 | coremusic_user | 7 | Profiles, preferences, history, favorites |
| 3 | coremusic_musics | 12 | Songs, artists, genres, lyrics, files, tags, stats |
| 4 | coremusic_albums | 5 | Album collections, tracks |
| 5 | coremusic_playlist | 5 | User and AI playlists, items, collaborators |
| 6 | coremusic_catalog | 8 | Reference data (genres, roles, instruments) |
| 7 | coremusic_logs | 13 | Audit trail, analytics, error logs |
| 8 | coremusic_media | 8 | Device sync, media metadata, file references |
| 9 | coremusic_system | 13 | Settings, config, cache, EQ, notifications |
| 10 | coremusic_social | 9 | Comments, shares, activity feed, listening rooms |
| 11 | coremusic_wireless | 5 | WiFi + Bluetooth networks |

**Total:** 18 BCNF databases, ~156 tables

#### 3.3.2 Database Rules

| Rule | Enforcement |
|------|-------------|
| ORM FORBIDDEN | Raw PDO only (ADR-002) |
| SELECT * FORBIDDEN | Explicit column lists required |
| Soft delete mandatory | `is_deleted = 0` pattern |
| BCNF mandatory | All tables in Boyce-Codd Normal Form |
| Prepared statements | All queries use parameterized binding |
| snake_case naming | Tables, columns, indexes |
| Auto-increment IDs | Integer primary keys |

#### 3.3.3 Connection Management

- PDO MySQL driver
- Connection pooling via APCu cache
- Transaction isolation: REPEATABLE READ
- Read replicas for heavy read operations
- Connection timeout: 5 seconds
- Max connections per service: 20

#### 3.3.4 Migration Strategy

- Forward-only (no rollback in production)
- Versioned SQL files in `.sql/migrations/`
- Transaction-wrapped (atomic)
- Naming: `YYYYMMDD_HHMMSS_description.sql`
- Seed files in `.sql/seeds/`

### 3.4 Security Architecture

#### 3.4.1 OWASP Top 10:2025 Compliance

| OWASP | Category | Mitigation |
|-------|----------|------------|
| A01 | Broken Access Control (SSRF dahil) | RBAC + Permission Guard + Middleware + URL Allowlist |
| A02 | Security Misconfiguration | CSP strict-dynamic + SecurityHeaders + hardening |
| A03 | Software Supply Chain Failures | Composer audit + dependency scanning + GitLeaks |
| A04 | Cryptographic Failures | AES-256-GCM + Argon2id + RS256 + TLS 1.3 |
| A05 | Injection | Prepared Statements + DOMParser + TrustedTypes |
| A06 | Insecure Design | Clean Architecture + DDD + CQRS + threat modeling |
| A07 | Authentication Failures | Hybrid Auth + MFA + Rate Limit + account lockout |
| A08 | Software/Data Integrity Failures | CSRF + JWT signature + validation + CI integrity |
| A09 | Security Logging & Alerting Failures | PSR-3 structured logging + audit trail + alerting |
| A10 | Mishandling of Exceptional Conditions | Error hierarchy + graceful degradation + circuit breaker |

#### 3.4.2 Encryption Standards

| Component | Algorithm | Key Size | IV Size | Tag Size |
|-----------|-----------|----------|---------|----------|
| Credential vault | AES-256-GCM | 256-bit | 96-bit | 128-bit |
| Password hash | Argon2id | — | — | — |
| JWT signing | RS256 | RSA-2048 | — | — |
| CSRF token | random_bytes | 256-bit | — | — |
| CSP nonce | random_bytes | 256-bit | — | — |
| Session ID | random_bytes | 256-bit | — | — |

#### 3.4.3 Security Headers

| Header | Value |
|--------|-------|
| Content-Security-Policy | `default-src 'self'; script-src 'nonce-{random}' 'strict-dynamic'` |
| Strict-Transport-Security | `max-age=31536000; includeSubDomains; preload` |
| X-Content-Type-Options | `nosniff` |
| X-Frame-Options | `DENY` |
| X-XSS-Protection | `0` |
| Referrer-Policy | `strict-origin-when-cross-origin` |
| Permissions-Policy | `camera=(), microphone=(), geolocation=()` |

#### 3.4.4 Middleware Pipeline (Frozen Order)

```
1. SessionManagerMiddleware()    — Session start, CSP nonce, CSRF token
2. BypassAuthMiddleware()        — Dev bypass (prod disabled)
3. RateLimiterMiddleware()       — APCu: 60 req/60s
4. AuthMiddleware()              — Session/JWT auth info inject
5. SecurityHeadersMiddleware()   — CSP, HSTS, X-Frame-Options
6. CsrfMiddleware()              — csrf_token validation (POST/PUT/DELETE)
```

**Order is FROZEN.** Changing order breaks CSP nonce generation.

#### 3.4.5 Secrets Management

| Rule | Enforcement |
|------|-------------|
| Secrets in `.env` | Never in code, logs, or vault markdown |
| Credential vault | AES-256-GCM encrypted in MySQL |
| Log masking | `[REDACTED]` for all sensitive data |
| `.env` in `.gitignore` | Never committed to version control |
| Key rotation | 90 days for JWT keys |

---

## 4. Directory Structure

### 4.1 Root Directory

```
C:\www\coremusic.net\
├── .ai/                          ← Vault (SSOT)
├── .claude/                      ← Agent configs, rules, skills
├── .opencode/                    ← OpenCode configs, skills
├── .github/                      ← CI/CD workflows
├── shared/                       ← Shared PHP infrastructure
│   ├── composer.json
│   ├── src/
│   │   ├── Auth/
│   │   ├── Security/
│   │   ├── Http/
│   │   ├── Router/
│   │   ├── Container/
│   │   └── Event/
│   ├── config/
│   │   ├── services.php
│   │   ├── middleware.php
│   │   ├── cors.php
│   │   └── routes.php
│   └── bootstrap.php
├── packages/                     ← coremusic/* Composer packages
│   ├── contracts/
│   ├── support/
│   ├── http/
│   ├── auth/
│   ├── security/
│   ├── cache/
│   ├── events/
│   ├── validation/
│   ├── storage/
│   ├── logger/
│   ├── openapi/
│   ├── sdk/
│   ├── api-client/
│   ├── queue/
│   ├── config/
│   ├── monitoring/
│   ├── testing/
│   ├── websocket/
│   ├── observability/
│   ├── mfa/
│   ├── i18n/
│   └── device/
├── auth.coremusic.net/           ← Central identity provider
│   ├── index.php
│   ├── composer.json
│   ├── include/Controller/
│   ├── config/
│   └── pages/
├── music.coremusic.net/          ← Main media panel (port 81)
├── admin.coremusic.net/          ← Administration panel
├── api.coremusic.net/            ← API gateway
├── media.coremusic.net/          ← Media vault (port 5000/6000)
├── download.coremusic.net/       ← Download service (port 3001)
├── home.coremusic.net/           ← Home media center (RPi5)
├── pro.coremusic.net/            ← Professional panel (RPi5)
├── studio.coremusic.net/         ← Studio panel (RPi5)
├── car.coremusic.net/            ← Car infotainment
├── coremusic.net/                ← Landing page
├── assets.coremusic.net/         ← Shared static assets
│   ├── css/
│   │   ├── main.css
│   │   ├── 01_Abstracts/
│   │   ├── 02_Base/
│   │   ├── 03_Layout/
│   │   ├── 04_Components/
│   │   ├── 05_Pages/
│   │   ├── 06_Utilities/
│   │   ├── 07_Vendors/
│   │   ├── 08_Devices/
│   │   └── 09_ViewModes/
│   ├── js/
│   │   ├── Router.js
│   │   ├── DomPatcher.js
│   │   ├── CsrfManager.js
│   │   ├── ThemeManager.js
│   │   ├── device-loader.js
│   │   ├── player/
│   │   ├── search/
│   │   └── guards/
│   ├── fonts/
│   └── images/
├── .sql/                         ← Database schemas
│   ├── mysql/
│   │   ├── coremusic_auth.sql
│   │   ├── coremusic_user.sql
│   │   ├── coremusic_musics.sql
│   │   ├── coremusic_albums.sql
│   │   ├── coremusic_playlist.sql
│   │   ├── coremusic_catalog.sql
│   │   ├── coremusic_logs.sql
│   │   ├── coremusic_media.sql
│   │   └── coremusic_system.sql
│   └── migrations/
├── docker/                       ← Docker configurations
│   ├── nginx/
│   ├── php/
│   └── mysql/
├── composer.json                 ← Root workspace (NO application code)
├── phpunit.xml                   ← Root test config
├── phpstan.neon                  ← Static analysis config
└── README.md
```

### 4.2 Shared Package Structure

```
shared/
├── composer.json
├── bootstrap.php
├── config/
│   ├── services.php
│   ├── middleware.php
│   ├── cors.php
│   ├── routes.php
│   └── auth.php
└── src/
    ├── Auth/
    │   ├── Domain/
    │   │   ├── Entity/
    │   │   │   └── User.php
    │   │   ├── Repository/
    │   │   │   └── UserRepositoryInterface.php
    │   │   ├── ValueObject/
    │   │   │   ├── Email.php
    │   │   │   ├── Password.php
    │   │   │   └── UserId.php
    │   │   └── Enum/
    │   │       └── Role.php
    │   ├── Application/
    │   │   ├── UseCase/
    │   │   │   ├── LoginUseCase.php
    │   │   │   ├── RegisterUseCase.php
    │   │   │   └── LogoutUseCase.php
    │   │   └── DTO/
    │   │       ├── LoginRequest.php
    │   │       └── LoginResponse.php
    │   └── Infrastructure/
    │       ├── Repository/
    │       │   └── PdoUserRepository.php
    │       └── Service/
    │           ├── JwtService.php
    │           └── PasswordService.php
    ├── Security/
    │   ├── Middleware/
    │   │   ├── SessionManagerMiddleware.php
    │   │   ├── BypassAuthMiddleware.php
    │   │   ├── RateLimiterMiddleware.php
    │   │   ├── AuthMiddleware.php
    │   │   ├── SecurityHeadersMiddleware.php
    │   │   └── CsrfMiddleware.php
    │   └── Service/
    │       ├── CsrfService.php
    │       ├── RateLimiterService.php
    │       └── SecurityHeaderService.php
    ├── Http/
    │   ├── Kernel.php
    │   ├── Request/
    │   │   └── RequestFactory.php
    │   └── Response/
    │       └── ResponseEmitter.php
    ├── Router/
    │   ├── Router.php
    │   ├── RouteCollector.php
    │   ├── RouteDispatcher.php
    │   └── Attributes/
    │       ├── Route.php
    │       ├── Middleware.php
    │       └── Group.php
    ├── Container/
    │   └── ContainerFactory.php
    └── Event/
        └── EventDispatcherFactory.php
```

### 4.3 Subdomain Structure

Each subdomain is an independent project:

```
{subdomain}.coremusic.net/
├── index.php              ← Entry point
├── .htaccess              ← Apache rewrite
├── composer.json          ← Dependencies + shared path repository
├── vendor/                ← Project-specific vendor
├── include/
│   └── Controller/
│       └── *Controller.php
├── config/
│   ├── routes.php         ← Route definitions
│   ├── config.php         ← Project configuration
│   └── container.php      ← DI container definition
├── pages/
│   └── *.php              ← PHP templates
├── assets/
│   ├── css/
│   └── js/
└── tests/
    ├── Unit/
    └── Integration/
```

### 4.4 Composer Path Repository

Each subdomain references shared library:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../shared",
            "options": { "symlink": true }
        }
    ],
    "require": {
        "coremusic/shared-infrastructure": "^1.0"
    }
}
```

---

## 5. Implementation Order

### 5.1 Dependency Graph

```
Layer 0: Infrastructure
  ├── MySQL 18 BCNF databases (18 schemas)
  ├── Redis (cache, queue, sessions)
  ├── APCu (rate limiting, route cache)
  └── Filesystem (media storage)

Layer 1: Shared Library (ADR-085 v3.0 — tek shared/ + PSR-4 namespace)
  ├── shared/src/Router/        (L2: SPA Router — Contracts, Attributes, Cache)
  ├── shared/src/Security/      (L1: Middleware Pipeline — 10 middleware)
  ├── shared/src/Auth/          (L1/L4: Auth Domain — Entity, VO, Repository, Event)
  ├── shared/src/Http/          (PSR-7/17)
  ├── shared/src/Cache/         (PSR-6)
  ├── shared/src/Events/        (PSR-14)
  ├── shared/src/Validation/    (Request validation)
  └── shared/src/Logger/        (PSR-3)

Layer 2: Subdomain Entry Points (shared/'e bağımlı)
  ├── auth.coremusic.net/index.php
  ├── music.coremusic.net/index.php
  ├── api.coremusic.net/index.php
  └── admin.coremusic.net/index.php
  ├── coremusic/auth (depends on http, contracts, support)
  └── coremusic/mfa (depends on auth, contracts)

Layer 3: Service Packages
  ├── coremusic/storage (depends on contracts)
  ├── coremusic/queue (depends on events, contracts)
  ├── coremusic/sdk (depends on http, auth, contracts)
  ├── coremusic/api-client (depends on http, contracts)
  └── coremusic/websocket (depends on http, contracts)

Layer 4: Application Services
  ├── auth.coremusic.net (depends on shared, security, auth)
  ├── music.coremusic.net (depends on shared, auth)
  ├── admin.coremusic.net (depends on shared, auth)
  ├── api.coremusic.net (depends on shared, all packages)
  └── media.coremusic.net (depends on shared, storage)

Layer 5: Frontend
  ├── assets.coremusic.net (CSS, JS — no PHP deps)
  ├── SPA Router (depends on assets)
  ├── Player (depends on Web Audio API)
  └── Theme Engine (depends on CSS custom properties)

Layer 6: Integration
  ├── Cross-domain auth (cookie SSO)
  ├── WebSocket connections
  ├── Event system (PSR-14)
  └── CQRS command/query handlers
```

### 5.2 Critical Path

```
Week 1: Project structure + shared packages
    ↓
Week 2: Auth domain + repository + password + JWT
    ↓
Week 3: Security middleware pipeline (6 layers)
    ↓
Week 4: Database schemas + API gateway
    ↓
Weeks 5-6: Media service
    ↓
Weeks 7-8: Download service
    ↓
Weeks 9-10: Audio engine stubs + remaining services
    ↓
Weeks 11-12: SPA Router + auth pages
    ↓
Weeks 13-14: Music & library pages
    ↓
Weeks 15-16: Admin & settings pages
    ↓
Weeks 17-18: Cross-domain auth + SSO
    ↓
Weeks 19-20: WebSocket + real-time
    ↓
Weeks 21-22: Event system + CQRS
    ↓
Weeks 23-24: Docker + deployment
    ↓
Weeks 25-26: Monitoring + testing + launch
```

### 5.3 Parallel Work Opportunities

| Weeks | Track A (Backend) | Track B (Frontend) | Track C (DevOps) |
|-------|-------------------|-------------------|------------------|
| 1-4 | Shared packages, auth, security | Asset structure, ITCSS setup | Docker base images |
| 5-10 | Media, download, audio stubs | SPA Router, auth pages | CI/CD pipeline |
| 11-16 | API gateway, WebSocket | Music, admin pages | Monitoring setup |
| 17-22 | Event system, CQRS | Integration testing | Security audit |
| 23-26 | Performance optimization | E2E testing | Production deploy |

---

## 6. Risk Analysis

### 6.1 High Risk

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| ASIO driver compatibility | Audio engine failure | Medium | Extensive hardware testing, fallback to WASAPI |
| Cross-domain cookie issues | Auth breaks across subdomains | Medium | Test with all 10 subdomains, proper domain config |
| Database migration errors | Data loss | Low | Forward-only, transaction-wrapped, backups |
| Security vulnerability | Data breach | Low | OWASP checklist, penetration testing, code review |
| Performance bottleneck | Slow response times | Medium | Load testing, caching strategy, CDN |

### 6.2 Medium Risk

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| Circular dependency | Build failure | Medium | Enforce dependency rules, CI checks |
| Memory leak in audio | System crash | Medium | Valgrind testing, ASAN, strict allocation rules |
| CSRF token timing | Security hole | Low | Post-DOM-patch update, session-bound tokens |
| Rate limit bypass | Abuse | Low | APCu fallback, multiple tracking layers |
| WebSocket scaling | Connection limits | Medium | Connection pooling, horizontal scaling |

### 6.3 Low Risk

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| CSS specificity conflicts | UI bugs | Low | ITCSS layer ordering, BEM namespace |
| Font loading delay | Flash of unstyled text | Low | font-display: swap, preload |
| Image optimization | Slow page load | Low | Lazy loading, WebP conversion |
| Mobile responsiveness | Poor UX on small screens | Low | Device CSS, viewport testing |
| Browser compatibility | Feature not working | Low | Progressive enhancement, polyfills |

---

## 7. Quality Gates

### 7.1 Code Quality

| Gate | Minimum | Tool |
|------|---------|------|
| PHPStan level | 8 | `phpstan analyse --level=8` |
| Code style | PSR-12 | `php-cs-fixer fix --dry-run` |
| No deprecated | 0 warnings | PHPStan + custom rules |
| No TODO/FIXME | 0 in production | `grep -r "TODO\|FIXME"` |

### 7.2 Test Coverage

| Module | Minimum | Target | Framework |
|--------|---------|--------|-----------|
| Backend (PHP) | ≥80% | ≥90% | PHPUnit 11 |
| Frontend (JS) | ≥80% | ≥90% | Vitest |
| Audio Engine (C++) | ≥80% | ≥90% | Google Test |
| Download Service | ≥80% | ≥90% | Vitest |

### 7.3 Security

| Gate | Check | Tool |
|------|-------|------|
| No known CVE | 0 vulnerability | `composer audit` |
| No hardcoded secrets | 0 match | `grep` patterns |
| No eval/assert | 0 usage | `grep` patterns |
| OWASP compliance | All 10 categories | Security audit checklist |

### 7.4 Performance

| Metric | Target | Measurement |
|--------|--------|-------------|
| TTFB | < 200ms | Lighthouse |
| API response | < 100ms | Load testing |
| Audio latency | < 10ms | ASIO buffer measurement |
| Page load | < 2s | Lighthouse |
| Database query | < 50ms | Query log analysis |

### 7.5 Documentation

| Gate | Requirement |
|------|-------------|
| README.md | Complete setup instructions |
| API docs | OpenAPI 3.1 for all endpoints |
| ADR | Every significant decision documented |
| CHANGELOG | Updated for every release |
| CONTRIBUTING | Developer guide |

---

## 8. Appendix

### 8.1 Glossary

| Term | Definition |
|------|------------|
| ADR | Architecture Decision Record |
| BCNF | Boyce-Codd Normal Form |
| BFF | Backend for Frontend |
| CORS | Cross-Origin Resource Sharing |
| CQRS | Command Query Responsibility Segregation |
| CSP | Content Security Policy |
| CSRF | Cross-Site Request Forgery |
| DDD | Domain-Driven Design |
| DTO | Data Transfer Object |
| HSTS | HTTP Strict Transport Security |
| ITCSS | It's Time to Create Scaleable Stylesheets |
| JWT | JSON Web Token |
| MFA | Multi-Factor Authentication |
| OWASP | Open Web Application Security Project |
| PSR | PHP Standards Recommendation |
| RBAC | Role-Based Access Control |
| RS256 | RSA Signature with SHA-256 |
| SSO | Single Sign-On |
| TTFB | Time To First Byte |

### 8.2 Technology Versions

| Technology | Version | Source |
|------------|---------|--------|
| PHP | 8.4+ | ADR-042 |
| MySQL | 9.x | ADR-040 |
| Redis | 7.x | Infrastructure |
| Node.js | LTS | Download service |
| C++ | 20 | Audio engine |
| JUCE | 9 | Audio framework |
| ASIO SDK | 2.3.4 | Audio driver |
| Docker | Latest | Deployment |
| Composer | Latest | PHP packages |
| npm | Latest | JS packages |

### 8.3 Port Registry

| Port | Service | Protocol |
|------|---------|----------|
| 80 | admin.coremusic.net, coremusic.net | HTTP |
| 81 | music.coremusic.net, home/pro/studio | HTTP |
| 443 | All (production) | HTTPS |
| 3001 | download.coremusic.net | HTTP/WS |
| 3306 | MySQL 9 | TCP |
| 5000/6000 | media.coremusic.net | HTTP |
| 6379 | Redis | TCP |
| 9741 | Audio Service (REST) | HTTP |
| 9742 | Audio Service (WebSocket) | WS |

### 8.4 Database Table Counts

| Database | Tables | Key Entities |
|----------|--------|--------------|
| coremusic_auth | 12 | users, roles, sessions, refresh_tokens, mfa_secrets, credential_vault, api_keys |
| coremusic_user | 7 | profiles, preferences, history, favorites |
| coremusic_musics | 12 | songs, artists, genres, lyrics, files, tags, stats, audio_features |
| coremusic_albums | 5 | albums, album_tracks, album_art |
| coremusic_playlist | 5 | playlists, playlist_items, collaborators |
| coremusic_catalog | 8 | genres, roles, instruments, reference_data |
| coremusic_logs | 13 | audit_trail, analytics, error_logs, performance_logs |
| coremusic_media | 8 | media_files, device_sync, streaming_sessions |
| coremusic_system | 13 | settings, config, cache, eq_presets, notifications |
| coremusic_social | 9 | comments, shares, activity_feed, listening_rooms |
| coremusic_wireless | 5 | wifi_networks, bluetooth_devices, wireless_settings |
| **Total** | **97** | |

### 8.5 Forbidden Patterns Summary

| ❌ Forbidden | ✅ Correct | ADR |
|-------------|-----------|-----|
| ORM (Eloquent, Doctrine) | Raw PDO | ADR-002 |
| `SELECT *` | Explicit columns | ADR-002 |
| `innerHTML` | DOMParser + replaceChildren | ADR-001 |
| React/Vue/Angular | Vanilla JS | ADR-001 |
| `eval()` / `Function()` | Safe alternatives | Security |
| `localStorage` for auth | Session-based auth | ADR-011 |
| `_csrf_token` | `csrf_token` | ADR-010 |
| Custom JWT | lcobucci/jwt (firebase/php-jkt yasaklı) | Security |
| Custom crypto | paragonie/halite | Security |
| `mysql_*` functions | PDO | Deprecated |
| MD5/SHA1 for passwords | Argon2id | Security |
| Hardcoded secrets | `.env` / credential vault | ADR-022 |
| `require_once` in classes | PSR-4 autoloading | Architecture |
| `var_dump()` / `print_r()` | PSR-3 logging | Security |
| Root-level `composer.json` | Per-subdomain | — |

### 8.6 Middleware Implementation Reference

```php
<?php

declare(strict_types=1);

// Frozen pipeline order (ADR-010/011/012/013/022):
$middlewares = [
    new SessionManagerMiddleware(),    // 1. Session start, CSP nonce
    new BypassAuthMiddleware(),         // 2. Dev bypass (prod disabled)
    new RateLimiterMiddleware(),        // 3. APCu: 60 req/60s
    new AuthMiddleware(),               // 4. Auth info inject
    new SecurityHeadersMiddleware(),    // 5. CSP, HSTS, X-Frame-Options
    new CsrfMiddleware(),              // 6. csrf_token validation
];

// Execute in reverse (innermost to outermost)
foreach (array_reverse($middlewares) as $middleware) {
    $response = $middleware->process($request, $handler);
}
```

### 8.7 Auth Flow Complete

```
1. User visits any subdomain (.coremusic.net)
   │
   ▼
2. Subdomain checks session cookie (COREMUSIC_SESS)
   │
   ├── Cookie exists → Validate via auth.coremusic.net/session-check API
   │   ├── Valid → User proceeds
   │   └── Invalid → Redirect to auth.coremusic.net/login
   │
   └── No cookie → Redirect to auth.coremusic.net/login
   │
   ▼
3. Login page (auth.coremusic.net/login)
   │
   ├── Email + password form
   │
   ▼
4. POST /api/auth/login
   │
   ├── Rate limit check (5 attempts/60s)
   │
   ├── Argon2id password verify
   │
   ├── Session create (COREMUSIC_SESS cookie)
   │   ├── session_regenerate_id(true)
   │   ├── $_SESSION['user_id'] = $user->id
   │   ├── $_SESSION['role'] = $user->role
   │   └── Cookie: COREMUSIC_SESS (HttpOnly, Secure, SameSite=Strict, Domain=.coremusic.net)
   │
   ├── JWT Access Token (15min, RS256)
   │
   ├── JWT Refresh Token (7 days, RS256)
   │
   └── 302 Redirect → origin subdomain
   │
   ▼
5. User is now authenticated on origin subdomain
```

### 8.8 CQRS Pattern Reference

**Command (Write):**
```php
final class CreateSongCommand
{
    public function __construct(
        public readonly string $title,
        public readonly string $artistId,
        public readonly int $durationSeconds,
        public readonly int $bitrate,
        public readonly string $genre
    ) {}
}
```

**Query (Read):**
```php
final class GetSongByIdQuery
{
    public function __construct(
        public readonly string $songId,
        public readonly bool $includeMetadata = false
    ) {}
}
```

**Handler:**
```php
class CreateSongHandler
{
    public function __construct(
        private SongRepository $songRepo,
        private EventDispatcher $eventDispatcher
    ) {}

    public function handle(CreateSongCommand $command): SongId
    {
        $song = Song::create(
            new Title($command->title),
            new ArtistId($command->artistId),
            new Duration($command->durationSeconds),
            new Bitrate($command->bitrate),
            new Genre($command->genre)
        );

        $this->songRepo->save($song);

        foreach ($song->collectDomainEvents() as $event) {
            $this->eventDispatcher->dispatch($event);
        }

        return $song->id();
    }
}
```

### 8.9 Event System Reference

**Domain Event:**
```php
final class SongCreatedEvent
{
    public const string VERSION = '1.0';

    public function __construct(
        public readonly string $songId,
        public readonly string $title,
        public readonly string $artistId,
        public readonly DateTimeImmutable $occurredAt = new DateTimeImmutable()
    ) {}
}
```

**Integration Event:**
```php
final class SongDownloadCompletedEvent
{
    public function __construct(
        public readonly string $correlationId,
        public readonly string $downloadId,
        public readonly string $userId,
        public readonly string $filePath,
        public readonly int $fileSize,
        public readonly DateTimeImmutable $occurredAt = new DateTimeImmutable()
    ) {}
}
```

### 8.10 Docker Compose Reference

```yaml
version: '3.8'

services:
  nginx:
    image: nginx:alpine
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - ./docker/nginx:/etc/nginx/conf.d
      - ./auth.coremusic.net:/var/www/auth
      - ./music.coremusic.net:/var/www/music
      - ./admin.coremusic.net:/var/www/admin
      - ./api.coremusic.net:/var/www/api
      - ./media.coremusic.net:/var/www/media
      - ./assets.coremusic.net:/var/www/assets
    depends_on:
      - php

  php:
    build:
      context: ./docker/php
      dockerfile: Dockerfile
    volumes:
      - .:/var/www/html
    environment:
      - APP_ENV=production
      - DB_HOST=mysql
      - REDIS_HOST=redis
    depends_on:
      - mysql
      - redis

  mysql:
    image: mysql:9.0
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
      MYSQL_DATABASE: coremusic_auth
    volumes:
      - mysql_data:/var/lib/mysql
      - ./.sql:/docker-entrypoint-initdb.d
    ports:
      - "3306:3306"

  redis:
    image: redis:7-alpine
    ports:
      - "6379:6379"
    volumes:
      - redis_data:/data

volumes:
  mysql_data:
  redis_data:
```

### 8.11 CI/CD Pipeline Reference

```yaml
name: CI/CD Pipeline

on: [push, pull_request]

jobs:
  lint:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP 8.4
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
      - name: Install dependencies
        run: composer install --no-interaction
      - name: PHPStan
        run: vendor/bin/phpstan analyse --level=8
      - name: CS Fixer
        run: vendor/bin/php-cs-fixer fix --dry-run --diff

  test:
    runs-on: ubuntu-latest
    needs: lint
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP 8.4
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
      - name: Install dependencies
        run: composer install --no-interaction
      - name: PHPUnit
        run: vendor/bin/phpunit --coverage-clover=coverage.xml
      - name: Check coverage
        run: |
          COVERAGE=$(php -r "echo (simplexml_load_file('coverage.xml')->project['metrics']['files-covered'] / simplexml_load_file('coverage.xml')->project['metrics']['files']) * 100;")
          if (( $(echo "$COVERAGE < 80" | bc -l) )); then
            echo "Coverage $COVERAGE% is below 80% threshold"
            exit 1
          fi

  security:
    runs-on: ubuntu-latest
    needs: lint
    steps:
      - uses: actions/checkout@v4
      - name: Setup PHP 8.4
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.4'
      - name: Install dependencies
        run: composer install --no-interaction
      - name: Security audit
        run: composer audit

  deploy:
    runs-on: ubuntu-latest
    needs: [test, security]
    if: github.ref == 'refs/heads/main'
    steps:
      - uses: actions/checkout@v4
      - name: Deploy to production
        run: |
          # Deployment steps here
          echo "Deploying to production..."
```

### 8.12 Monitoring Dashboard Reference

| Metric | Type | Description |
|--------|------|-------------|
| `http_requests_total` | Counter | Total requests |
| `http_request_duration_seconds` | Histogram | Request duration |
| `http_requests_by_status` | Counter | Requests by status code |
| `db_connection_pool_active` | Gauge | Active DB connections |
| `cache_hit_ratio` | Gauge | Cache hit ratio |
| `queue_depth` | Gauge | Queue depth |
| `memory_usage_bytes` | Gauge | Memory usage |
| `cpu_usage_percent` | Gauge | CPU usage |
| `active_sessions` | Gauge | Active sessions |
| `websocket_connections` | Gauge | Active WebSocket connections |

### 8.13 Deployment Modes

| Mode | Platform | Hardware |
|------|----------|----------|
| Home Media Center | Windows/Linux/macOS | PC/Laptop |
| Car Audio System | Windows/Android Auto | Raspberry Pi 5 / PCM3168A |
| Professional Studio | Windows (WASAPI/ASIO) | 8.1 Surround + Class AB |
| NAS Audio Server | Linux (Docker) | Synology/QNAP |
| DAC Control System | Windows/Linux | XMOS XU316 + PCM3168A |

### 8.14 Platform Tiers

| Tier | OS | Status |
|------|-----|--------|
| Tier 1 (Primary) | Windows (XP–11, Server 2012 R2+) | ✅ Primary development |
| Tier 2 | Linux (Ubuntu, Debian, Fedora, Arch) | ✅ Supported |
| Tier 3 | macOS (Monterey–Sonoma) | ✅ Supported |
| Tier 4 | Raspberry Pi (ARM64, Debian) | ✅ Supported |
| Tier 5 | ReactOS | ⚠️ Experimental |

### 8.15 Audio Specifications

| Feature | Value |
|---------|-------|
| Sample Format | Float32 (32-bit) |
| Sample Rate | 48/96/192 kHz |
| Channels | 2-8 (stereo to 8.1 surround) |
| I/O | 8x8 |
| Latency | <10ms (ASIO), <20ms (WASAPI) |
| Buffer Size | 256-1024 samples |
| DSP Effects | EQ, Reverb, Compressor, Limiter |
| EQ Bands | 31 parametric |
| Drivers | ASIO, WASAPI, ALSA, CoreAudio, PipeWire, I2S |

### 8.16 Service Architecture

| Service | Port | Protocol | Stack |
|---------|------|----------|-------|
| Control | 81 | HTTP | PHP 8.4 (Auth, Session, RBAC) |
| Media | 5000/6000 | HTTP | PHP + FFmpeg (Library, Metadata) |
| Audio | 9741/9742 | REST/WS | C++20 JUCE (Player, DSP, Mixer) |
| Device | — | BLE/WiFi/USB | C++20 (Bluetooth, WiFi, USB) |
| Network Audio | — | WebRTC/P2P | C++20 (Streaming, Multi-room) |
| AI | — | Internal | PHP + Python (Recommendations) |
| Download | 3001 | HTTP/WS | Node.js + TypeScript |

### 8.17 Frontend Technology

| Component | Technology |
|-----------|-----------|
| Language | Vanilla JavaScript ES6+ |
| Module System | ES6 modules (import/export) |
| DOM Safety | DOMParser + TrustedTypes (no innerHTML) |
| CSS Architecture | ITCSS 9-layer + BEM |
| Theme Engine | CSS custom properties (data-gender attribute) |
| Responsive | Device CSS (d-*.css) loaded by device-loader.js |
| SPA Router | History API pushState (no hash routing) |
| State Management | Simple Store pattern (no framework) |
| Testing | Vitest |

### 8.18 Security Checklist

- [ ] CSRF token in all forms (key: `csrf_token`)
- [ ] CSP header on all responses (nonce-based)
- [ ] Prepared statements for all SQL
- [ ] No secrets in code or logs
- [ ] Rate limiting active
- [ ] Session cookie: HttpOnly, Secure, SameSite=Strict
- [ ] Error responses don't leak sensitive info
- [ ] Input validation on all user input
- [ ] RBAC checks on all protected endpoints
- [ ] OWASP Top 10:2025 compliant

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-13
**Mode:** Red Team · Human Mode · Truth Mode
**Version:** 1.0.0
