---
title: COREMUSIC DEEP RULES — HYBRID SPA + 7-SERVICE ECOSYSTEM
version: 7.2.0
updated: 2026-06-11
quality-score: "98.7%"
---

# COREMUSIC DEEP RULES — HYBRID SPA + 7-SERVICE ECOSYSTEM
# Prompt Maker v7.2.0 | 2026-06-11

## 4-Layer Architecture (L0-L3)

```
L3 Presentation: main.js, AuthHandler.js, HomeHandler.js, pages/*.php
L2 Router: Router.js, PageRouter.php, RouteRegistry.php
L1 Security: GuardPipeline.js, Middleware Pipeline (7 middleware)
L0 Infrastructure: FetchWrapper, CacheLayer, Logger, Database, Cache
```

**Rules:** L3→L2→L1→L0 (no upward refs, L0 cannot import L2)

## SPA Router Specifics

**FetchWrapper:**
- AbortController for every request
- Retry on 5xx (max 2 attempts, 500ms backoff)
- X-CSRF-Token, X-Requested-With headers

**CacheLayer:**
- static: 3600s (albums, tracks, artists)
- user: 60s (home, dashboard, playlists)
- dynamic: 30s (search results)
- LRU eviction (MAX_ENTRIES=50)
- Memory check: navCount % 10 → heapMB > 100 → evictHalf()

**CSRF Protection:**
- Key: `csrf_token` (NOT `_csrf_token`)
- Validation: `hash_equals()` timing-safe
- Update timing: AFTER DOM patch (form in new DOM)

## PHP Runtime Rules

- `declare(strict_types=1)` EVERY FILE
- Namespace: `CoreMusic\System\*`
- Constructor DI mandatory
- PDO + prepared statements MANDATORY
- Password: Argon2id (memory_cost=65536, time_cost=4, threads=2)
- Session security: regenerate_id(true) after login

## Middleware Pipeline (FIXED ORDER)

1. SessionManager (session_start, CMM_UserID)
2. BypassAuth (dev mode, bypass routes)
3. RateLimiter (60 req/60s per IP, APCu)
4. AuthMiddleware (verify $_SESSION['MM_UserID'])
5. SecurityHeaders (CSP nonce, X-Frame-Options, HSTS)
6. CsrfMiddleware (hash_equals, validate token)

**Critical:** SessionManager BEFORE SecurityHeaders (nonce injection)

## 7-Service Ecosystem (ADR 030-032)

| Service | Port | Tech | Role |
|---------|------|------|------|
| CoreMusic PHP | 81 | PHP 8.4 | Main SPA runtime |
| Assets JS | — | Vanilla JS | Router + CSS |
| Download Svc | 3001 | Node.js/TS | Music downloader |
| Neva Engine | 9741-9742 | C++ DSP | Audio processor |
| Neva Player | 9743 | C++ media | Video player |
| API | future | — | API endpoint |
| Media | — | PHP | Upload service |

## Service Discovery (ADR 031)

**Windows Named Objects:**
- `Global\NevaEngineStatus` → {restPort: 9741, wsPort: 9742, state, pid}
- `Global\NevaAudioRingBuffer` → float32 PCM ring buffer
- `Global\NevaAudioRingMutex` → sync primitive

**Fallback:** `~/.coremusic/engine.status` (JSON file, Linux/macOS)

## IPC Protocol (ADR 032)

**WebSocket Handshake (9742, 9743, 3001):**
```json
Client → Server:
{
  "type": "handshake",
  "apiVersion": 2,
  "clientType": "spa|download|player",
  "clientId": "uuid-v4"
}

Server → Client:
{
  "type": "handshake_ack",
  "apiVersion": 2,
  "capabilities": ["spectrum", "eq31band", "presets"],
  "deprecated": []
}
```

**Control Command (versioned):**
```json
{
  "cmd": "setEQ",
  "apiVersion": 2,
  "bands": [1.5, 0.8, ...],
  "type": "31band"
}
```

## Health Checks (Readiness + Liveness)

```bash
# Readiness: ready to serve requests
curl http://localhost:9741/health → {status: "ok", uptime: ...}

# Liveness: container still running (k8s)
GET /health?liveness → {status: "ok"}
```

## Startup Sequence (CRITICAL ORDER)

1. Neva Engine (ASIO + ring buffer)
2. Neva Player (GPU + discover Engine)
3. Download Service (Node express)
4. CoreMusic PHP (routes + discover Engine)
5. Browser (load SPA)

**Failure modes:**
- Engine down → Player standby, CoreMusic degrades
- Player down → video unavailable, audio pipeline fails
- Download down → queue disabled but SPA works
- CoreMusic down → entire app unavailable

## CSS Architecture (ITCSS, ADR 002)

```
01_Abstracts: tokens, mixins (8 files)
02_Base: HTML element styles (2 files)
03_Layout: footer, header, grid (2 files)
04_Components: seekbar, volume, scrollbar (3 files)
05_Pages: home, login, gender (4 files)
06_Utilities: margins, colors, display (1 file)
07_Vendors: Bootstrap (1 file)
```

**Variables:** --color-primary, --color-bg, --color-text (dark/light mode)

## Database Schemas (9 instances)

- coremusic_core: users, sessions, auth
- coremusic_auth: credentials (AES-256-GCM)
- coremusic_albums: album metadata
- coremusic_musics: track info, codec, bitrate
- coremusic_playlists: user playlists, tracks
- coremusic_catalog: search index
- coremusic_media: uploads
- coremusic_logs: audit trail
- coremusic_system: config

## Gender Selection & RPi5 Hardware Mode

- 3 genders: male, female, neutral (for homepage background)
- HW Mode: `COREMUSIC_HW_MODE=true` env var
- RPi5: PCM5122 I2C audio, 32-bit DAC
- HardwareBypassMiddleware: skip certain checks on RPi5

---

Quality Score (2026-06-11): 98.7%
