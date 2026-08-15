---
title: "CoreMusic Vault — Master Index"
type: system
category: vault-navigation
date: 2026-08-13
updated: 2026-08-13
status: active
version: 26.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
total_files: 493
total_adr: 87
reference:
  authority: ".ai/index.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/WORKFLOW.md"
    - ".ai/brain.md"
    - ".ai/index.md"
    - ".ai/keys.md"
    - ".ai/MEMORY.md"
    - ".ai/log.md"
    - ".ai/engine.md"
  architecture:
    - ".ai/ADR/"
    - "Existing project architecture"
    - "Existing codebase patterns"
  project_structure:
    - "coremusic.net/"
    - "shared/"
    - "api.coremusic.net/"
    - "auth.coremusic.net/"
    - "music.coremusic.net/"
    - "admin.coremusic.net/"
    - "home.coremusic.net/"
    - "car.coremusic.net/"
    - "studio.coremusic.net/"
    - "pro.coremusic.net/"
    - "media.coremusic.net/"
    - "download.coremusic.net/"
  decision_priority:
    - "ADR decisions"
    - "Architecture documentation"
    - "Security requirements"
    - "Existing implementation"
    - "User requirements"
  update_policy:
    preserve_existing_structure: true
    require_approval_for:
      - "file rename"
      - "directory move"
      - "architecture change"
      - "database schema change"
      - "security policy change"
  skills:
    - path: ".opencode/skills/ui-code-generator/SKILL.md"
      purpose: "UI/CSS kod üretimi, responsive tasarım, WCAG erişilebilirlik"
    - path: ".opencode/skills/ui-analyzer/SKILL.md"
      purpose: "UI analizi, mevcut tasarım değerlendirme"
    - path: ".opencode/skills/skill-maker/SKILL.md"
      purpose: "Yeni skill oluşturma, skill template sistemi"
    - path: ".opencode/skills/red-team-truth-mode/SKILL.md"
      purpose: "Güvenlik testi, truth mode, adversarial analiz"
    - path: ".opencode/skills/prompt-maker/SKILL.md"
      purpose: "Prompt mühendisliği, AI talimat tasarımı"
    - path: ".opencode/skills/composer-sync/SKILL.md"
      purpose: "Composer dependency yönetimi, vendor senkronizasyonu"
    - path: ".opencode/skills/agent-orchestrator/SKILL.md"
      purpose: "Agent görev dağıtımı, multi-agent koordinasyonu"
    - path: ".opencode/skills/human-mode/SKILL.md"
      purpose: "İnsan modu iletişimi, onay süreçleri"
    - path: ".opencode/skills/hallucination-control/SKILL.md"
      purpose: "Halüsinasyon kontrolü, doğrulama protokolleri"
    - path: ".opencode/skills/database-normalize-maker/SKILL.md"
      purpose: "BCNF normalizasyonu, şema tasarımı"
  templates:
    adr:
      - path: ".ai/.templates/adr/adr-template.md"
        purpose: "ADR şablonu"
      - path: ".ai/.templates/adr/adr-frontend-template.md"
        purpose: "Frontend ADR şablonu"
      - path: ".ai/.templates/adr/adr-database-template.md"
        purpose: "Database ADR şablonu"
      - path: ".ai/.templates/adr/adr-security-template.md"
        purpose: "Security ADR şablonu"
      - path: ".ai/.templates/adr/adr-audio-template.md"
        purpose: "Audio/Hardware ADR şablonu"
      - path: ".ai/.templates/adr/adr-index.md"
        purpose: "ADR navigasyon rehberi"
    backend:
      - path: ".ai/.templates/backend/php-template.md"
        purpose: "PHP 8.4 backend geliştirme şablonu"
      - path: ".ai/.templates/backend/nodejs-template.md"
        purpose: "Node.js 20+ backend geliştirme şablonu"
    frontend:
      - path: ".ai/.templates/frontend/js-template.md"
        purpose: "Vanilla JS ES6+ frontend geliştirme şablonu"
      - path: ".ai/.templates/frontend/css-template.md"
        purpose: "ITCSS 9-layer, BEM CSS şablonu"
    testing:
      - path: ".ai/.templates/testing/phpunit-template.md"
        purpose: "PHPUnit 10+ test şablonu"
      - path: ".ai/.templates/testing/vitest-template.md"
        purpose: "Vitest JS/TS test şablonu"
    infrastructure:
      - path: ".ai/.templates/infrastructure/migration-template.md"
        purpose: "MySQL 9 BCNF migration şablonu"
      - path: ".ai/.templates/infrastructure/docker-template.md"
        purpose: "Docker 24+ Compose v2 şablonu"
      - path: ".ai/.templates/infrastructure/github-actions-template.md"
        purpose: "GitHub Actions CI/CD şablonu"
    documentation:
      - path: ".ai/.templates/documentation/api-doc-template.md"
        purpose: "API dokümantasyon şablonu"
      - path: ".ai/.templates/documentation/security-audit-template.md"
        purpose: "Güvenlik denetimi şablonu"
      - path: ".ai/.templates/documentation/WikiPage-Template.md"
        purpose: "Wiki sayfası şablonu"
    hardware:
      - path: ".ai/.templates/hardware/arduino-template.md"
        purpose: "Arduino/IoT prototipleme şablonu"
      - path: ".ai/.templates/hardware/avr-template.md"
        purpose: "AVR mikrodenetleyici şablonu"
      - path: ".ai/.templates/hardware/pic-template.md"
        purpose: "PIC mikrodenetleyici şablonu"
    query:
      - path: ".ai/.templates/query/Query-Template.md"
        purpose: "SQL sorgu şablonu"
    other:
      - path: ".ai/.templates/other/c-template.md"
        purpose: "C11 GCC embedded/driver şablonu"
      - path: ".ai/.templates/cpp-template.md"
        purpose: "C++20 JUCE/ASIO şablonu"
---

# CoreMusic Vault — Master Index

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]] · [[.templates/index]] · [[.agents/AGENTS.md]]

**Skills:** `.opencode/skills/` (10 skill — Guardrail #16 zorunlu)

---

## 1. Amaç

Bu dosya, CoreMusic `.ai/` vault'unun ana navigasyon noktasıdır. Tüm vault dosyaları kategorize edilmiş ve hızlı erişilebilir biçimde listelenir.

---

## 2. Quick Reference

| İhtiyaç | İlk Adım |
|---------|----------|
| Vault genel bakış | Bu dosya (index.md) |
| Keyword arama | [[keys.md]] |
| Mimari kararlar | [[brain.md]] |
| Ajan yetkileri | [[AGENTS.md]] |
| Süreçler | [[WORKFLOW.md]] |
| Bellek yönetimi | [[MEMORY.md]] |
| Aktivite günlüğü | [[log.md]] |
| ADR kataloğu | § 5 bu dosya |
| Servis haritası | § 6 bu dosya |
| Veritabanı | § 8 bu dosya |

---

## 3. SSOT Core Dosyaları (9 Zorunlu)

| # | Dosya | Amaç |
|---|-------|------|
| 1 | [[CLAUDE.md]] | Kanonik AI talimatı — boot protokolü, guardrails |
| 2 | [[AGENTS.md]] | Agent kayıt defteri — 7 agent + MO, handover |
| 3 | [[WORKFLOW.md]] | Süreçler — vault refactoring, ürün döngüsü |
| 4 | [[index.md]] | Bu dosya — tüm vault dizin yapısı |
| 5 | [[keys.md]] | Anahtar kelime haritası — keyword → dosya yönlendirme |
| 6 | [[brain.md]] | Mimari kararlar — ADR 001-087, L0-L6, engineering brain |
| 7 | [[MEMORY.md]] | Oturum hafızası — persistent state, cache, session lifecycle |
| 8 | [[log.md]] | Aktivite günlüğü — append-only audit trail |
| 9 | [[engine.md]] | Orkestrasyon motoru — agent koordinasyonu, task dispatch |

---

## 4. Mimari — L0-L6 Katmanları

*Detaylı metadata için bakınız: [[architecture/00-overview/architecture-master]] §2*

Bağımlılık kuralları: ✅ L6→L5, L5→L4, L4→L3, L3→L2, L2→L1, L1→L0 | ❌ L0→L2/L3, L1→L3, L3→L0

| Katman | Dosya | Kapsam |
|--------|-------|--------|
| L6 Electronics | [[architecture/l6-electronics]] | Hardware, firmware, driver, DSP, audio engine |
| L5 Services | [[architecture/l5-services]] | Application services, use cases, CQRS, event bus |
| L4 Domain | [[architecture/l4-domain]] | Business rules, entities, value objects, aggregates |
| L3 Presentation | [[architecture/l3-presentation]] | Frontend, UI, DOM, responsive |
| L2 Routing | [[architecture/l2-routing]] | SPA PageRouter, API Gateway, subdomain routing |
| L1 Security | [[architecture/l1-security]] | Middleware pipeline, session, auth, CSRF, CSP |
| L0 Infrastructure | [[architecture/l0-infrastructure]] | Database, cache, filesystem, IPC, credential vault |

---

## 5. Mimari Kararlar (ADR)

Toplam 87 ADR. Frozen: 001-037 (değiştirilemez). Active: 038-087 (güncellenebilir).

### 5.1 Frozen (001-037)

| ADR | Konu | Kategori |
|-----|------|----------|
| [[decisions/accepted/ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS, framework yasak | Frontend |
| [[decisions/accepted/ADR-002-pdo-mandatory-no-orm]] | PDO mandatory, ORM yasak | Database |
| [[decisions/accepted/ADR-003-multi-db-9-databases]] | 9 BCNF veritabanı | Database |
| [[decisions/accepted/ADR-004-multi-domain-spa]] | Multi-domain SPA mimarisi | Architecture |
| [[decisions/accepted/ADR-005-ultrathink-protocol]] | Zero hallucination protocol | Quality |
| [[decisions/accepted/ADR-006-performance-targets]] | Performans hedefleri | Performance |
| [[decisions/accepted/ADR-007-cache-namespace]] | Cache namespace standardı | Infrastructure |
| [[decisions/accepted/ADR-008-bypass-auth-middleware]] | Auth bypass middleware | Security |
| [[decisions/accepted/ADR-009-clean-url-redirect]] | Clean URL redirect | Routing |
| [[decisions/accepted/ADR-010-csrf-protection-strategy]] | CSRF koruma stratejisi | Security |
| [[decisions/accepted/ADR-011-session-management]] | Session yönetimi | Security |
| [[decisions/accepted/ADR-012-csp-nonce-strict-dynamic]] | CSP nonce + strict-dynamic | Security |
| [[decisions/accepted/ADR-013-rate-limiting-apcu]] | APCu rate limiting | Security |
| [[decisions/accepted/ADR-014-multi-db-migration-strategy]] | Multi-DB migration | Database |
| [[decisions/accepted/ADR-015-env-parser-strategy]] | Env parser stratejisi | Infrastructure |
| [[decisions/accepted/ADR-016-url-normalization]] | URL normalization | Routing |
| [[decisions/accepted/ADR-017-dsp-hardware-mode]] | DSP hardware mode (XMOS, JUCE) | Audio |
| [[decisions/accepted/ADR-018-footer-player-vaporwave]] | Footer player vaporwave | UI |
| [[decisions/accepted/ADR-019-per-os-neva-player]] | Per-OS Neva Player | Audio |
| [[decisions/accepted/ADR-020-api-public-security]] | API public security | Security |
| [[decisions/accepted/ADR-021-spa-router-immutable-contract]] | SPA router contract | Routing |
| [[decisions/accepted/ADR-022-database-hardened-security]] | DB hardened security | Security |
| [[decisions/accepted/ADR-023-persona-driven-testing]] | Persona-driven testing | Testing |
| [[decisions/accepted/ADR-024-ecosystem-modular-docs]] | Ecosystem modular docs | Documentation |
| [[decisions/accepted/ADR-025-professional-eq-system]] | Professional EQ system | Audio |
| [[decisions/accepted/ADR-026-download-service-architecture]] | Download service arch | Architecture |
| [[decisions/accepted/ADR-027-dual-mode-storage-strategy]] | Dual-mode storage | Infrastructure |
| [[decisions/accepted/ADR-028-anti-ban-system]] | Anti-ban system | Download |
| [[decisions/accepted/ADR-029-listening-rooms-social]] | Listening rooms social | Social |
| [[decisions/accepted/ADR-030-ai-strategy-core]] | AI strategy core | AI |
| [[decisions/accepted/ADR-031-mobile-strategy-pwa-flutter]] | Mobile strategy PWA/Flutter | Mobile |
| [[decisions/accepted/ADR-032-ipc-contract-versioning]] | IPC contract versioning | Architecture |
| [[decisions/accepted/ADR-033-sql-normalization-strategy]] | SQL normalization | Database |
| [[decisions/accepted/ADR-034-credential-vault-normalization]] | Credential vault normalization | Security |
| [[decisions/accepted/ADR-035-system-prompt-engineering]] | System prompt engineering | AI |
| [[decisions/accepted/ADR-036-multi-project-prompt-maker]] | Multi-project prompt maker | AI |
| [[decisions/accepted/ADR-037-wirelessconnect-integration]] | WirelessConnect integration | Integration |

### 5.2 Active (038-064)

| ADR | Konu | Kategori |
|-----|------|----------|
| [[decisions/accepted/ADR-038-8.1-sound-card-chip-selection]] | 8.1 ses donanımı (PCM3168A + XMOS XU316) | Audio |
| [[decisions/accepted/ADR-039-7-service-platform-architecture]] | 7-servis platform mimarisi | Architecture |
| [[decisions/accepted/ADR-040-database-authority]] | 18 BCNF DB otoritesi | Database |
| [[decisions/accepted/ADR-041-database-normalization-supplementary]] | DB normalizasyon ekı | Database |
| [[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]] | Vault yeniden yapılandırma | Vault |
| [[decisions/accepted/ADR-043-auth-subdomain-consolidation]] | Auth subdomain konsolidasyonu | Security |
| [[decisions/accepted/ADR-044-dynamic-user-theme-engine]] | Dynamic theme engine | UI |
| [[decisions/accepted/ADR-045-multi-domain-view-mode-architecture]] | Multi-domain view mode | UI |
| [[decisions/accepted/ADR-046-cross-view-state-preservation]] | Cross-view state koruma | UI |
| [[decisions/accepted/ADR-047-login-redirect-session-bridge]] | Login redirect session bridge | Auth |
| [[decisions/accepted/ADR-048-view-transition-api-integration]] | View Transition API entegrasyonu | UI |
| [[decisions/accepted/ADR-049-startup-prompt-loader]] | Startup prompt loader | AI |
| [[decisions/accepted/ADR-050-multi-db-sync-strategy]] | Multi-DB sync stratejisi | Database |
| [[decisions/accepted/ADR-061-electronics-architecture]] | Electronics Architecture (L6 Layer) | Electronics |
| [[decisions/accepted/ADR-062-dsp-pipeline-architecture]] | DSP Pipeline Architecture | Electronics |
| [[decisions/accepted/ADR-063-hardware-design-standards]] | Hardware Design Standards | Electronics |
| [[decisions/accepted/ADR-064-electronics-platform-architecture]] | Electronics Platform Architecture (L0-L6, 5 cihaz, 13 servis) | Electronics |
| [[decisions/accepted/ADR-072-social-database-schema]] | Social DB Schema (comments, shares, activity, rooms, notifications) | Database |
| [[decisions/accepted/ADR-073-podcast-database-schema]] | Podcast DB Schema (shows, episodes, subscriptions, transcripts) | Database |
| [[decisions/accepted/ADR-074-radio-database-schema]] | Radio DB Schema (stations, schedules, now_playing) | Database |
| [[decisions/accepted/ADR-075-ai-database-schema]] | AI DB Schema (preferences, features, recommendations, models) | Database |
| [[decisions/accepted/ADR-076-video-database-schema]] | Video DB Schema (music_videos, playback, subtitles) | Database |
| [[decisions/accepted/ADR-077-studio-database-schema]] | Studio DB Schema (sessions, tracks, presets, equipment) | Database |
| [[decisions/accepted/ADR-078-cms-database-schema]] | CMS DB Schema (pages, blog, tags, media, FAQs, banners) | Database |
| [[decisions/accepted/ADR-079-i18n-database-schema]] | i18n DB Schema (languages, translations, ui_strings, locale) | Database |
| [[decisions/accepted/ADR-083-spa-router]] | SPA Router Architecture (PHP+JS Hybrid) | Routing |
| [[decisions/accepted/ADR-084-api-gateway-architecture]] | API Gateway Architecture (API-First, BFF, CQRS) | Architecture |
| [[decisions/accepted/ADR-085-modular-composer-packages]] | Modular Composer Packages (coremusic/*) | Infrastructure |
| [[decisions/accepted/ADR-086-event-driven-architecture]] | Event Driven Architecture (PSR-14) | Architecture |
| [[decisions/accepted/ADR-087-master-implementation-plan]] | Master Implementation Plan (Sıfırdan Geliştirme Kapsamı) | Architecture |

### 5.3 Reddedilen Kararlar

| Dosya | Kapsam |
|-------|--------|
| [[decisions/rejected/README]] | Reddedilen ADR listesi ve gerekçeleri |

---

## 6. 10 Panel & 7 Backend Servis

### 6.1 Frontend Paneller

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

### 6.2 Backend Servisler

| Servis | Port | Protocol | Stack |
|--------|------|----------|-------|
| Control Service | 81 | HTTP | PHP 8.4 (Auth, Session, RBAC) |
| Media Service | 5000/6000 | HTTP | PHP + FFmpeg (Library, Metadata) |
| Audio Service | 9741/9742 | REST/WS | C++20 JUCE (Player, DSP, Mixer) |
| Device Service | — | BLE/WiFi/USB | C++20 (Bluetooth, WiFi, USB) |
| Network Audio | — | WebRTC/P2P | C++20 (Streaming, Multi-room) |
| AI Service | — | Internal | PHP + Python (Recommendations) |
| Download Service | 3001 | HTTP/WS | Node.js + TypeScript |

### 6.3 Port Haritası

| Port | Servis | Protokol |
|------|--------|----------|
| 80 | admin.coremusic.net | HTTP |
| 81 | music.coremusic.net (Control Service) | HTTP |
| 3001 | download.coremusic.net | HTTP/WS |
| 3306 | MySQL 18 BCNF DB | TCP |
| 5000/6000 | media.coremusic.net | HTTP |
| 9741 | Audio Service (REST) | HTTP |
| 9742 | Audio Service (WebSocket) | WS |
| 9743 | Neva Player | WS |
| 49152-65535 | WebRTC | UDP |

---

## 7. Agent Sistemi

| Agent | Domain | Teknoloji |
|-------|--------|-----------|
| Master Orchestrator | Görev dağıtımı, orkestrasyon | Vault System, log.md |
| Backend Architect | PHP 8.4 API, middleware | L2 Routing, controller, repository |
| UI Designer | Vanilla JS, ITCSS, Web Audio | L3 Presentation, responsive, accessibility |
| Security Engineer | OWASP, AES-256-GCM, Argon2id | L1 Security, CSRF, CSP, session |
| Data Engineer | MySQL 18 BCNF, PDO | L0 Infrastructure, schema, migration |
| Embedded Engineer | C++20, JUCE, ASIO | Audio DSP, hardware, ring buffer |
| QA Engineer | PHPUnit, Vitest, Playwright | Testing, coverage, E2E |
| DevOps Engineer | CI/CD, Docker, GitLeaks | Deployment, pipeline, monitoring |

Agent profile dosyaları: [[.agents/AGENTS.md]], [[.agents/backend-architect]], [[.agents/ui-designer]], [[.agents/security-engineer]], [[.agents/data-engineer]], [[.agents/embedded-engineer]], [[.agents/qa-engineer]], [[.agents/devops-engineer]], [[.agents/master-orchestrator]], [[.agents/audio-hardware-engineer]], [[.agents/dsp-firmware-engineer]], [[.agents/windows-software-engineer]]

---

## 8. Veritabanı (18 BCNF — ADR-040)

| # | Veritabanı | Dosya | Amaç | Tablo Sayısı |
|---|------------|-------|------|-------------|
| 1 | coremusic_auth | `.sql/mysql/coremusic_auth.sql` | Users, roles, sessions, tokens, credential vault, API keys | 13 |
| 2 | coremusic_user | `.sql/mysql/coremusic_user.sql` | Profiles, preferences, history, favorites | 7 |
| 3 | coremusic_musics | `.sql/mysql/coremusic_musics.sql` | Songs, artists, genres, lyrics, files, podcasts, videos, radio | 22 |
| 4 | coremusic_albums | `.sql/mysql/coremusic_albums.sql` | Album collections, discs, stats | 5 |
| 5 | coremusic_playlist | `.sql/mysql/coremusic_playlist.sql` | User and AI playlists, collaborators, followers | 5 |
| 6 | coremusic_catalog | `.sql/mysql/coremusic_catalog.sql` | Reference data (genres, artist roles, instruments, moods) | 8 |
| 7 | coremusic_logs | `.sql/mysql/coremusic_logs.sql` | Audit trail, analytics, error logs, performance metrics | 22 |
| 8 | coremusic_media | `.sql/mysql/coremusic_media.sql` | Device sync, media metadata, access control | 8 |
| 9 | coremusic_system | `.sql/mysql/coremusic_system.sql` | Settings, config, cache, EQ, file manager, notifications, i18n | 17 |
| 10 | coremusic_social | `.sql/mysql/coremusic_social.sql` | Comments, shares, activity, listening rooms | 9 |
| 11 | coremusic_wireless | `.sql/mysql/coremusic_wireless.sql` | WiFi + Bluetooth networks | 5 |
| 12 | coremusic_ai | `.sql/mysql/coremusic_ai.sql` | User preference profiles, listening features, recommendations | 6 |
| 13 | coremusic_api | `.sql/mysql/coremusic_api.sql` | API keys, rate limits, API call logs, webhooks | 4 |
| 14 | coremusic_cms | `.sql/mysql/coremusic_cms.sql` | Pages, blog, tags, media assets, FAQs, banners | 8 |
| 15 | coremusic_download | `.sql/mysql/coremusic_download.sql` | Download queue, history, cache, source APIs | 4 |
| 16 | coremusic_neva | `.sql/mysql/coremusic_neva.sql` | EQ presets, DSP settings, routing matrix, spectrum analysis | 4 |
| 17 | coremusic_studio | `.sql/mysql/coremusic_studio.sql` | Studio sessions, tracks, presets, equipment | 6 |
| 18 | coremusic_patch | `.sql/mysql/coremusic_patch.sql` | Schema versions, migration logs, patches | 3 |
| | **TOPLAM** | | | **156** |

---

## 9. Projeler

### 9.1 Neva Engine (C++ Audio)

[[projects/NevaEngine/overview]], [[projects/NevaEngine/audio-core]], [[projects/NevaEngine/equalizer-system]], [[projects/NevaEngine/equalizer-31band]], [[projects/NevaEngine/eq-dsp-chain]], [[projects/NevaEngine/eq-theme-system]], [[projects/NevaEngine/eq-ai-auto]], [[projects/NevaEngine/eq-plugin-hosting]], [[projects/NevaEngine/eq-presets]], [[projects/NevaEngine/eq-user-modes]], [[projects/NevaEngine/eq-cross-platform]], [[projects/NevaEngine/midi-system]], [[projects/NevaEngine/routing-matrix]], [[projects/NevaEngine/spatial-audio]], [[projects/NevaEngine/vst3-hosting]], [[projects/NevaEngine/ai-models]], [[projects/NevaEngine/neva-engine-integration]]

### 9.2 Neva Player (Video/Media)

[[projects/NevaPlayer/neva-player/overview]], [[projects/NevaPlayer/neva-player/codec-matrix]], [[projects/NevaPlayer/neva-player/color-pipeline]], [[projects/NevaPlayer/neva-player/ffmpeg-integration]], [[projects/NevaPlayer/neva-player/gpu-acceleration]], [[projects/NevaPlayer/neva-player/video-decoder]], [[projects/NevaPlayer/neva-player/webrtc-streaming]], [[projects/NevaPlayer/neva-player/windows-optimization]], [[projects/NevaPlayer/neva-player/rpi5-arm64]], [[projects/NevaPlayer/neva-player/resolution-adaptation]]

### 9.3 Diğer Projeler

[[projects/download-service]], [[projects/ipc-contracts]], [[projects/cpp-projects]], [[projects/WirelessConnect/proj-wireless-connect]], [[projects/NevaConnect/proj-neva-connect]], [[projects/README]]

---

## 10. Donanım & Elektronik

| Dosya | Kapsam |
|-------|--------|
| [[electronic/index]] | Master Electronics Index |
| [[electronic/audio-organization]] | 5 bölüm organizasyonu |
| [[electronic/hardware-roadmap]] | 3 fazlı hardware geliştirme |
| [[electronic/amplifier-design]] | Class AB 100W amfi tasarımı |
| [[electronic/audio-interface-design]] | XMOS XU316 + PCM3168A audio interface |
| [[electronic/xmos-pcm3168a-design]] | XMOS + PCM3168A devre tasarımı |
| [[electronic/asio-driver-design]] | ASIO sürücü tasarımı |
| [[electronic/test-protocols]] | Donanım test protokolleri |
| [[electronic/frequency-response]] | Frekans yanıtı ölçüm protokolü |
| [[electronic/snr-thd-measurement]] | SNR & THD ölçüm protokolü |
| [[electronic/thermal-analysis]] | Termal analiz |
| [[electronic/dsp/index]] | DSP Engine (7 modül) |
| [[electronic/drivers/index]] | Driver Framework (7 modül) |
| [[electronic/amplifier/index]] | Amplifier Architecture (5 modül) |
| [[electronic/hardware/index]] | Hardware Design (5 modül) |
| [[electronic/firmware/index]] | Firmware Architecture (4 modül) |
| [[electronic/core-music-electronics-overview]] | CoreMusic Electronics genel bakış |
| [[electronic/platform-architecture]] | Platform mimarisi (9 katman) |
| [[electronic/device-architecture]] | Cihaz mimarisi ve aileleri |
| [[electronic/operating-system-architecture]] | OS desteği (8 platform, PAL) |
| [[electronic/device-ecosystem]] | Cihaz ekosistemi ve iletişim |
| [[electronic/software-architecture]] | Yazılım mimarisi (5 katman) |
| [[electronic/service-architecture]] | Servis mimarisi (13 servis) |
| [[electronic/audio-architecture]] | Ses mimarisi (input→output pipeline) |
| [[electronic/dsp-engine-architecture]] | DSP motoru (EQ, compressor, limiter, crossover) |
| [[electronic/driver-framework]] | Sürücü çerçevesi (ASIO/WASAPI/ALSA/CoreAudio) |
| [[electronic/amplifier-architecture]] | Amfi mimarisi (8+1 varsayılan, 7 cihaz) |
| [[electronic/hardware-design]] | Donanım tasarım rehberi (PCB, EMI/EMC) |
| [[electronic/firmware-architecture]] | Firmware mimarisi (RTOS, HAL, OTA) |
| [[architecture/l6-electronics]] | L6 Electronics katmanı (L0-L6 stack) |
| [[architecture/network-architecture]] | Ağ mimarisi (HTTP/MQTT/gRPC/IPC) |
| [[architecture/database-architecture]] | Veritabanı mimarisi (18 BCNF, SQLite) |
| [[architecture/security-architecture]] | Güvenlik mimarisi (OWASP 2025, RBAC) |
| [[architecture/ai/ai-electronics-engine]] | AI Electronics Engine |
| [[architecture/ai/ai-workflow-electronics]] | AI Electronics workflow |
| [[architecture/03-contracts/development-workflow]] | 20 fazlı geliştirme süreci |
| [[architecture/03-contracts/development-standards]] | Geliştirme standartları (SOLID, DDD, CQRS) |
| [[architecture/03-contracts/ai-workflow-standards]] | AI workflow standartları |
| [[architecture/03-contracts/diagram-collection]] | Mermaid diyagram koleksiyonu |
| [[architecture/07-security/electronics-security]] | Elektronik güvenlik (Secure Boot, FW signing) |
| [[architecture/03-contracts/engineering-rules-ssot]] | Mühendislik kuralları SSOT |
| [[architecture/03-contracts/master-implementation-plan]] | Master Implementation Plan (5 faz, 40 gün, 22 bölüm) |

---

## 11. Test & Ekosistem

### 11.1 Test

| Dosya | Kapsam |
|-------|--------|
| [[testing/strategy]] | Test stratejisi |
| [[testing/coverage-targets]] | Kapsama hedefleri (≥80% min, ≥90% target) |
| [[testing/e2e-template]] | E2E test şablonu |
| [[testing/persona-test-protocol]] | Persona test protokolü |
| [[testing/test-plan]] | Test planı |
| [[testing/test-scenarios-mapping]] | Test senaryoları eşleme |

### 11.2 Ekosistem

| Dosya | Kapsam |
|-------|--------|
| [[ecosystem/7-service-integration]] | 7-servis entegrasyonu |
| [[ecosystem/service-health-check]] | Health check endpoint'leri |
| [[ecosystem/service-communication]] | Servis iletişim kalıpları |
| [[ecosystem/panel-integration]] | 10-panel entegrasyonu |
| [[ecosystem/error-recovery]] | Hata kurtarma stratejileri |
| [[ecosystem/network-architecture]] | Ağ mimarisi |
| [[ecosystem/state-machines]] | State machine'ler |

---

## 11A. AI Architecture

| Dosya | Kapsam |
|-------|--------|
| [[architecture/ai/index]] | AI Architecture index |
| [[architecture/ai/ai-engine]] | AI Engine — müzik önerileri, ses analizi, otomatik EQ |
| [[architecture/ai/ai-orchestrator]] | AI Orchestrator — görev dağıtımı, context yönetimi |
| [[architecture/ai/agent-system]] | Agent System — 11 ajanlı agent sistemi |
| [[architecture/ai/knowledge-base]] | Knowledge Base — bilgi bankası, semantic search |
| [[architecture/ai/memory-system]] | Memory System — session hafızası, persistence |
| [[architecture/ai/prompt-engine]] | Prompt Engine — prompt üretimi, token management |
| [[architecture/ai/tool-calling]] | Tool Calling — dış servis çağrısı |
| [[architecture/ai/mcp-integration]] | MCP Integration — Model Context Protocol |
| [[architecture/ai/ai-workflow]] | AI Workflow — recommendation, analysis, optimization |

---

## 11A. Skills (10 Skill — Guardrail #16 Zorunlu)

| # | Skill | Amaç | Konum |
|---|-------|------|-------|
| 1 | `ui-code-generator` | UI/CSS kod üretimi, responsive tasarım | `.opencode/skills/ui-code-generator/SKILL.md` |
| 2 | `ui-analyzer` | UI analizi, mevcut tasarım değerlendirme | `.opencode/skills/ui-analyzer/SKILL.md` |
| 3 | `skill-maker` | Yeni skill oluşturma, template sistemi | `.opencode/skills/skill-maker/SKILL.md` |
| 4 | `hallucination-control` | Halüsinasyon kontrolü, doğrulama | `.opencode/skills/hallucination-control/SKILL.md` |
| 5 | `human-mode` | İnsan modu iletişimi, onay süreçleri | `.opencode/skills/human-mode/SKILL.md` |
| 6 | `red-team-truth-mode` | Güvenlik testi, adversarial analiz | `.opencode/skills/red-team-truth-mode/SKILL.md` |
| 7 | `prompt-maker` | Prompt mühendisliği, AI talimat tasarımı | `.opencode/skills/prompt-maker/SKILL.md` |
| 8 | `agent-orchestrator` | Agent görev dağıtımı, multi-agent koordinasyonu | `.opencode/skills/agent-orchestrator/SKILL.md` |
| 9 | `composer-sync` | Composer dependency yönetimi | `.opencode/skills/composer-sync/SKILL.md` |
| 10 | `database-normalize-maker` | BCNF normalizasyonu, şema tasarımı | `.opencode/skills/database-normalize-maker/SKILL.md` |

**Detay:** Her skill dosyası YAML frontmatter'da `reference:` bloğu ile vault'a bağlıdır.

---

## 12. Vault Altyapısı

| Kategori | Dosyalar |
|----------|----------|
| Session | [[sessions/index]] |
| Registry | [[registry/dashboard]], [[registry/lifecycle]], [[registry/projects.csv]] |
| Scaffold | [[scaffold/checklist]], [[scaffold/rules]] |
| Reports | [[reports/session-summary-template]] |
| Prompt Archives | [[brain#22-prompt-arsivi]] | 4 ana prompt: genel, SPA router, auth, API |
| Prompt Engine | [[architecture/ai/prompt-engine]] | Prompt üretim ve yönetim motoru |
| Knowledge | [[knowledge/verified]], [[knowledge/unverified]], [[knowledge/rejected]], [[confidence/README]] |
| Subdomains | [[subdomains/README]], [[subdomains/auth.coremusic.net/index]], [[subdomains/music.coremusic.net/index]], [[subdomains/download.coremusic.net/domains/index]] |
| UI-Design | [[ui-design/00-mockup-index]], [[ui-design/01-component-inventory]], [[ui-design/02-implementation-plan]], [[ui-design/03-accessibility-gaps]], [[ui-design/04-vault-registration]] |
| UI-Design Screens | [[ui-design/screens/00-ascii-art-views]], [[ui-design/screens/A-auth/login]], [[ui-design/screens/B-home/dashboard]], [[ui-design/screens/C-music/albums]], [[ui-design/screens/D-player/playlist]], [[ui-design/screens/E-filemanager/disk-browser]], [[ui-design/screens/F-quickpanel/wifi]] |
| PNG Mockups | `.ai/.png/home-1024/` (12 PNG) + `.ai/.png/shared-1024/` (6 PNG) = 18 PNG |
| UI-Design Prompt | [[ui-design/prompt/00-prompt-index]], [[ui-design/prompt/screen/01-1024-embedded]], [[ui-design/prompt/component/C01-nav-link]], [[ui-design/prompt/layout/01-pattern-standard-60-40]], [[ui-design/prompt/page/01-home]] |
| UI-Design Reference | [[ui-design/reference/01-design-tokens]], [[ui-design/reference/02-php-source-architecture]], [[ui-design/reference/03-text-strings]], [[ui-design/reference/04-icon-asset-catalog]], [[ui-design/reference/05-verification]] |
| UI-Design Flow | [[ui-design/flow/00-flow-index]], [[ui-design/flow/auth/04-select-gender]] |
| Research | [[research/verified/php84-strict-types]], [[research/verified/argon2id]], [[research/verified/aes-256-gcm]], [[research/verified/pcm3168a]], [[research/verified/asio-sdk]], [[research/verified/juce8]], [[research/verified/xmos-xu316]], [[research/verified/trusted-types-domparser]], [[research/verified/itcss-bemit-layer]], [[research/verified/wcag-22-aa]], [[research/verified/mariadb-1011]] |
| Personas | [[personas/index]], [[personas/methodology]], [[personas/mood-taxonomy]] |
| Templates | [[.templates/index]] — 25 template (PHP, JS, CSS, C++, PHPUnit, Vitest, Migration, Docker, GitHub Actions, API-doc, Security-audit, ADR, Arduino, AVR, PIC, C, Node.js, ASP.NET, WikiPage, Query) |
| Workflows | [[workflows/adr-creation]], [[workflows/dev-workflow]], [[workflows/code-review]], [[workflows/deployment]], [[workflows/hallucination-control]], [[workflows/security-audit]], [[workflows/session-init]], [[workflows/vault-sync-detailed]] |
| Root | [[engine]], [[index-overview]], [[index-services]], [[index-adr]], [[decisions/index]], [[research/index]] |

---

## 13. Deployment Modes

| Mod | Platform | Donanım |
|-----|----------|---------|
| Home Media Center | Windows/Linux/macOS | PC/Laptop |
| Car Audio System | Windows/Android Auto | Raspberry Pi 5 / PCM3168A |
| Professional Studio | Windows (WASAPI/ASIO) | 8.1 Surround + Class AB |
| NAS Audio Server | Linux (Docker) | Synology/QNAP |
| DAC Control System | Windows/Linux | XMOS XU316 + PCM3168A |

---

## 14. Platform Tiers

| Tier | OS | Durum |
|------|-----|-------|
| Tier 1 (Primary) | Windows (XP–11, Server 2012 R2+) | ✅ Ana geliştirme |
| Tier 2 | Linux (Ubuntu, Debian, Fedora, Arch) | ✅ Destekli |
| Tier 3 | macOS (Monterey–Sonoma) | ✅ Destekli |
| Tier 4 | Raspberry Pi (ARM64, Debian) | ✅ Destekli |
| Tier 5 | ReactOS | ⚠️ Experimental |

---

## 15. Audio Organization

| Division | Sorumluluk |
|----------|------------|
| Hardware Division | Özel audio kartları, DAC/ADC, DSP çipleri, amplifikatör |
| Software Division | C++ Audio Engine, DSP Engine, Mixer, sürücüler |
| Studio Division | ASIO, WASAPI, kayıt, monitoring, routing |
| Consumer Division | Bluetooth, WiFi Audio, müzik oynatma, ev ve araç ses |
| Research Division | AI DSP, yeni codec teknolojileri, geleceğin audio donanımları |

---

## 16. Test Coverage Hedefleri

| Modül | Minimum | Hedef |
|-------|---------|-------|
| Backend (PHP) | ≥80% | ≥90% |
| Frontend (JS) | ≥80% | ≥90% |
| Audio Engine (C++) | ≥80% | ≥90% |
| Download Service | ≥80% | ≥90% |

---

## 17. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 SSOT | [[CLAUDE.md]] | Ana sözleşme |
| § 4 Mimari | [[architecture/l0-infrastructure]] | L0-L6 katmanları |
| § 5 ADR | [[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]] | Vault standardı |
| § 6 Servisler | [[ecosystem/7-service-integration]] | Servis entegrasyonu |
| § 7 Agentlar | [[AGENTS.md]] | Agent yetkileri |
| § 8 DB | [[architecture/05-data/database_master]] | 18 BCNF şemaları |
| § 9 Projeler | [[projects/NevaEngine/overview]] | C++ ses motoru |
| § 10 Donanım | [[electronic/hardware-roadmap]] | 3 fazlı geliştirme |
| § 11 Test | [[testing/coverage-targets]] | Kapsama hedefleri |

---

## 18. Metadata

- **Toplam dosya:** 493
- **Toplam ADR:** 78 (Frozen: 37, Active: 41)
- **Versiyon:** 26.0.0
- **Governance:** Red Team · Human Mode · Truth Mode

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-13
**Mode:** Red Team · Human Mode · Truth Mode