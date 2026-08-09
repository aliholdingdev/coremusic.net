# Core Rules — CoreMusic

**Authority:** ADR-001, ADR-002, ADR-005, ADR-039, ADR-040, ADR-042, ADR-044
**Last Updated:** 2026-08-09
**Governing Rules:** Red Team · Human Mode · Truth Mode

---

## 1. Identity

CoreMusic is a **multi-service media management platform** (NOT a music player). Designed for:
- Individual users, professional music producers, studios
- Car infotainment systems
- Home media centers
- NAS storage solutions
- Multi-device ecosystems

## 2. Architecture: 4-Layer Strict

```
L3 — Presentation  (Frontend, UI, DOM)          ← Vanilla JS, ITCSS
L2 — Routing       (Router, middleware, dispatch) ← PHP 8.4 PageRouter
L1 — Security      (Session, Auth, CSRF, CSP)   ← Middleware Pipeline
L0 — Infrastructure (Database, cache, fs)        ← PDO, APCu, Redis
```

**Dependency Rules:**
- ✅ L3 → L2, L2 → L1, L1 → L0: Allowed
- ❌ L0 → L2/L3, L1 → L3, L3 → L0: FORBIDDEN

## 3. Middleware Pipeline (Immutable Order)

```
SessionManager → BypassAuth → RateLimiter → Auth → SecurityHeaders → Csrf
```

**Order is FROZEN** (ADR-010/011/012/013/022). Never change.

## 4. Hard Guardrails

| Rule | Enforcement |
|------|-------------|
| Zero Code Before Plan | No code without approved design |
| No Hallucination | Unverified data → `VERIFICATION REQUIRED` |
| MSA Limit = 15 files | Max 15 files per task (ADR-042/C5) |
| In-Place Refactoring | No file rename/move without approval |
| Single Source of Truth | All info from `.ai/` vault only |
| CSRF Token = `csrf_token` | NOT `_csrf_token` (removed 2026-05-30) |
| Middleware Order Immutable | SessionManager → ... → Csrf |
| Port 81 = music.coremusic.net | PHP 8.4 |
| No ORM | Raw PDO only (ADR-002) |
| No SELECT * | Explicit column lists required |
| No Frameworks | Vanilla JS + ITCSS (ADR-001) |

## 5. Tech Stack

| Layer | Technology | Version |
|-------|-----------|---------|
| Backend | PHP | 8.4+ (strict_types=1) |
| Frontend | Vanilla JS ES6+ | ES2022 |
| CSS | ITCSS + BEM | 7-layer |
| Database | MySQL | 9 BCNF-isolated databases |
| Audio | C++20, JUCE 9, ASIO SDK 2.3.4 | — |
| Hardware | XMOS XU316, PCM3168A | PCM5122 REJECTED (H001) |
| ORM | FORBIDDEN | Raw PDO only |
| Frameworks | FORBIDDEN | Vanilla only |

## 6. Service Map

### 6.1 10 Panel (Frontend)

| Panel | Port | Stack | Durum |
|-------|------|-------|-------|
| music.coremusic.net | 81 | PHP 8.4 + Vanilla JS | ✅ Ana medya paneli |
| admin.coremusic.net | 80 | PHP 8.4 | ✅ Yönetim paneli |
| download.coremusic.net | 3001 | Node.js + TypeScript | ✅ İndirme servisi |
| media.coremusic.net | 5000/6000 | PHP + FFmpeg | ✅ Medya depolama |
| auth.coremusic.net | — | PHP 8.4 | ✅ Kimlik doğrulama |
| home.coremusic.net | — | Vanilla JS | ✅ Ev medya merkezi |
| car.coremusic.net | — | Vanilla JS | ✅ Araç içi bilgi-eğlence |
| studio.coremusic.net | — | Vanilla JS | ✅ Profesyonel stüdyo |
| pro.coremusic.net | — | Vanilla JS | ✅ Profesyonel panel |
| coremusic.net | — | Vanilla JS | ✅ Landing page |

### 6.2 7 Backend Service (Runtime)

| Service | Port | Protocol | Stack |
|---------|------|----------|-------|
| Control Service | 81 | HTTP | PHP 8.4 (Auth, Session, RBAC) |
| Media Service | 5000/6000 | HTTP | PHP + FFmpeg (Library, Metadata) |
| Audio Service | 9741/9742 | REST/WS | C++20 JUCE (Player, DSP, Mixer) |
| Device Service | — | BLE/WiFi/USB | C++20 (Bluetooth, WiFi, USB) |
| Network Audio | — | WebRTC/P2P | C++20 (Streaming, Multi-room) |
| AI Service | — | Internal | PHP + Python (Recommendations, Auto-download) |
| Download Service | 3001 | HTTP/WS | Node.js + TypeScript |

## 7. Theme Engine (ADR-044)

- **Gender-based:** female→pink, male→blue, neutral→default
- **PHP:** `ThemeEngine.php` — resolves theme from DB + user gender
- **JS:** `ThemeManager.js` — applies theme via CSS custom properties (no reload)
- **DB:** `user_preferences` table — `user_id`, `device_type`, `theme_gender`
- **CSS:** `data-gender` attribute on `<html>`, `<body>`, containers
- **Hardcoded theme YASAK:** `if female show pink.png` forbidden
- **Admin panel:** Separate theme system (independent from user themes)

## 8. Audio Organization (5 Divisions)

| Division | Sorumluluk |
|----------|------------|
| Hardware Division | Özel audio kartları, DAC/ADC, DSP çipleri, amplifikatör |
| Software Division | C++ Audio Engine, DSP Engine, Mixer, sürücüler |
| Studio Division | ASIO, WASAPI, kayıt, monitoring, routing |
| Consumer Division | Bluetooth, WiFi Audio, müzik oynatma, ev ve araç ses |
| Research Division | AI DSP, yeni codec teknolojileri, geleceğin audio donanımları |

## 9. Deployment Modes

| Mod | Platform | Donanım |
|-----|----------|---------|
| 🏠 Home Media Center | Windows/Linux/macOS | PC/Laptop |
| 🚗 Car Audio System | Windows/Android Auto | Raspberry Pi 5 / PCM3168A |
| 🎛️ Professional Studio | Windows (WASAPI/ASIO) | 8.1 Surround + Class AB |
| 📦 NAS Audio Server | Linux (Docker) | Synology/QNAP |
| 🎵 DAC Control System | Windows/Linux | XMOS XU316 + PCM3168A |

## 10. Electronics Architecture (ADR-061–ADR-063, ADR-080)

CoreMusic Electronics, 5 Division + 3 Cihaz Ailesi + 15 Device OS + 12 Division yapısıyla donanım/yazılım entegrasyonunu yönetir.

### 10.1 L0-L6 Layer Stack

| Katman | Kapsam | Teknoloji |
|--------|--------|-----------|
| L0 | Infrastructure | MySQL, Redis, PDO, APCu |
| L1 | Security | OWASP 2025, Argon2id, AES-256-GCM |
| L2 | Routing | PHP 8.4 PageRouter, SPA |
| L3 | Presentation | Vanilla JS, ITCSS |
| L4 | Domain | Entity, Value Object, Repository |
| L5 | Services | API Gateway, Service Mesh |
| L6 | Electronics | C++20, XMOS, JUCE, ASIO |

### 10.2 3 Device Families

| Aile | Güç | Hoparlör | Kullanım |
|------|-----|----------|----------|
| 8+1 Amp | 2000W @ 8Ω | 8 kanal + 1 LFE | Stüdyo, Pro, Araç |
| 5+1 Amp | — | 5 kanal + 1 LFE | Orta segment |
| 2+1 Amp | 10W/35W/2000W | 2 kanal + 1 LFE | Entry level |

### 10.3 Electronics Standartları (Web Doğrulanmış)

| Standart | Versiyon | Kaynak |
|----------|----------|--------|
| XMOS lib_i2s | v6.0.1 | xmos.com (2024/11/12) |
| XMOS lib_xua | v5.5.0 | xmos.com |
| JUCE | 9.0.0 | juce.com (Jul 21 2026) |
| ASIO SDK | v2.3.4 | steinberg.net (GPLv3 open-source) |
| OWASP | Top 10:2025 | owasp.org (Aug 2026) |

### 10.4 Hard Guardrails (Electronics)

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Zero-Allocation: Audio thread'de heap allocation yasak | Ses takılması / crash |
| 2 | Lock-Free: Audio thread'de mutex yasak | Deadlock |
| 3 | PCM5122 Yasak: 8.1 surround için yetersiz (H001) | Yanlış donanım |
| 4 | ASIO Exclusive Lock: Aynı anda sadece tek uygulama | Sürücü çökmesi |
| 5 | DC Offset Riski: >0.5V DC offset koruma rölesi | Amfi hasarı |

## 11. Platform Tier Hierarchy

| Tier | OS | Durum |
|------|-----|-------|
| Tier 1 (Primary) | Windows (XP–11, Server 2012 R2+) | ✅ Ana geliştirme |
| Tier 2 | Linux (Ubuntu, Debian, Fedora, Arch) | ✅ Destekli |
| Tier 3 | macOS (Monterey–Sonoma) | ✅ Destekli |
| Tier 4 | Raspberry Pi (ARM64, Debian) | ✅ Destekli |
| Tier 5 | ReactOS | ⚠️ Experimental |

## 12. Test Coverage Targets

| Module | Minimum | Target |
|--------|---------|--------|
| Backend (PHP) | ≥80% | ≥90% |
| Frontend (JS) | ≥80% | ≥90% |
| Audio Engine (C++) | ≥80% | ≥90% |
| Download Service | ≥80% | ≥90% |

## 13. Zero Hallucination Policy (ADR-005)

- NEVER fabricate API endpoints, classes, or database tables
- Unverified data MUST be marked: `⚠️ VERIFICATION REQUIRED`
- Always verify against vault documentation before coding
- When uncertain, ask the user

### 13.1 Pre-Commit Checklist

1. ✅ Read actual source code (not memory)
2. ✅ Verify against ADR documents
3. ✅ Check `.ai/` vault for canonical references
4. ✅ Confirm function signatures from source
5. ✅ Validate database schema from `.sql/` files

### 13.2 Common Hallucination Patterns

| Pattern | Prevention |
|---------|------------|
| Invented API endpoints | Check route files directly |
| Wrong function signatures | Read class definitions |
| Outdated patterns | Web search for latest docs |
| Assumed behavior | Read actual implementation |
| Made-up ADR numbers | Check `decisions/accepted/` |

## 14. Human Mode (ADR-042)

### 14.1 Output Standards

- Lead with the answer or next action
- Number multi-step work
- One bounded action per step
- End with one next action doable in under 2 minutes
- Finish current issue before raising a new one
- Restate progress each turn
- Give time estimates in concrete units
- After a change, show what now works
- Errors: state location, cause, and fix (no drama)
- Cap lists at 5 items
- No preamble, no recaps, no closers

### 14.2 Decision Transparency

- Explain reasoning behind choices
- Reference specific ADR numbers
- Show alternatives considered
- State assumptions explicitly

### 14.3 Escalation Protocol

When uncertain:
1. State what is known
2. State what is uncertain
3. Ask one specific question
4. Wait for clarification before proceeding

## 15. Red Team Protocol

Every agent output must undergo adversarial review:

### 15.1 Self-Review Checklist

- [ ] Are all claims backed by source code or ADR?
- [ ] Are there any invented APIs, classes, or endpoints?
- [ ] Is the code complete (not pseudocode)?
- [ ] Are error cases handled?
- [ ] Are security implications considered?
- [ ] Is the implementation following all hard guardrails?

### 15.2 Verification Commands

```bash
# PHP syntax check
php -l file.php

# Run tests
vendor/bin/phpunit

# Check ADR compliance
grep -r "ADR-NNN" .ai/decisions/accepted/

# Verify no hallucinated classes
grep -r "class.*{" src/ --include="*.php"
```

---

*Core Rules v3.1.0 — CoreMusic Enterprise*
*Last Updated: 2026-08-10*
