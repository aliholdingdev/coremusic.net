---
title: "CoreMusic Vault — Master Index"
type: system
category: vault-navigation
updated: 2026-08-08
status: active
version: 19.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
total_files: 404
total_adr: 50
---

# CoreMusic Vault — Master Index

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]]

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
| 6 | [[brain.md]] | Mimari kararlar — ADR 001-050, L0-L3, engineering brain |
| 7 | [[MEMORY.md]] | Oturum hafızası — persistent state, cache, session lifecycle |
| 8 | [[log.md]] | Aktivite günlüğü — append-only audit trail |
| 9 | [[engine.md]] | Orkestrasyon motoru — agent koordinasyonu, task dispatch |

---

## 4. Mimari — L0-L3 Katmanları

Bağımlılık kuralları: ✅ L3→L2, L2→L1, L1→L0 | ❌ L0→L2/L3, L1→L3, L3→L0

| Katman | Dosya | Kapsam |
|--------|-------|--------|
| L0 Infrastructure | [[architecture/l0-infrastructure]] | DB, cache, filesystem, IPC, credential vault |
| L1 Security | [[architecture/l1-security]] | Middleware pipeline, session, CSRF, CSP, rate limit |
| L2 Routing | [[architecture/l2-routing]] | SPA PageRouter, URL normalization, subdomain routing |
| L3 Presentation | [[architecture/l3-presentation]] | Vanilla JS, ITCSS 7-layer, TrustedTypes, Web Audio |

---

## 5. Mimari Kararlar (ADR)

Toplam 50 ADR. Frozen: 001-037 (değiştirilemez). Active: 038-050 (güncellenebilir).

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

### 5.2 Active (038-050)

| ADR | Konu | Kategori |
|-----|------|----------|
| [[decisions/accepted/ADR-038-8.1-sound-card-chip-selection]] | 8.1 ses donanımı (PCM3168A + XMOS XU316) | Audio |
| [[decisions/accepted/ADR-039-7-service-platform-architecture]] | 7-servis platform mimarisi | Architecture |
| [[decisions/accepted/ADR-040-database-authority]] | 9 BCNF DB otoritesi | Database |
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
| 3306 | MySQL 9 BCNF DB | TCP |
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
| Data Engineer | MySQL 9 BCNF, PDO | L0 Infrastructure, schema, migration |
| Embedded Engineer | C++20, JUCE, ASIO | Audio DSP, hardware, ring buffer |
| QA Engineer | PHPUnit, Vitest, Playwright | Testing, coverage, E2E |
| DevOps Engineer | CI/CD, Docker, GitLeaks | Deployment, pipeline, monitoring |

Agent profile dosyaları: [[.agents/AGENTS.md]], [[.agents/backend-architect]], [[.agents/ui-designer]], [[.agents/security-engineer]], [[.agents/data-engineer]], [[.agents/embedded-engineer]], [[.agents/qa-engineer]], [[.agents/devops-engineer]], [[.agents/master-orchestrator]], [[.agents/audio-hardware-engineer]], [[.agents/dsp-firmware-engineer]], [[.agents/windows-software-engineer]]

---

## 8. Veritabanı (9 BCNF — ADR-040)

| # | Veritabanı | Dosya | Amaç |
|---|------------|-------|------|
| 1 | coremusic_auth | `.sql/coremusic_auth.sql` | Users, roles, sessions, Argon2id hashes |
| 2 | coremusic_user | `.sql/coremusic_user.sql` | Profiles, preferences, history |
| 3 | coremusic_musics | `.sql/coremusic_musics.sql` | Songs, artists, genres, metadata |
| 4 | coremusic_albums | `.sql/coremusic_albums.sql` | Album collections |
| 5 | coremusic_playlist | `.sql/coremusic_playlist.sql` | User and AI playlists |
| 6 | coremusic_catalog | `.sql/coremusic_catalog.sql` | Download queues, service status |
| 7 | coremusic_logs | `.sql/coremusic_logs.sql` | Application logs, audit trail |
| 8 | coremusic_media | `.sql/coremusic_media.sql` | Media file metadata |
| 9 | coremusic_system | `.sql/coremusic_system.sql` | System configuration |

Ek SQL: `.sql/coremusic_analytics.sql`, `.sql/coremusic_api.sql`, `.sql/coremusic_credential.sql`, `.sql/coremusic_download.sql`, `.sql/coremusic_neva.sql`, `.sql/coremusic_patch.sql`, `.sql/coremusic_wireless.sql`, `.sql/core-music-db.sql`

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

## 12. Vault Altyapısı

| Kategori | Dosyalar |
|----------|----------|
| Session | [[sessions/index]] |
| Registry | [[registry/dashboard]], [[registry/lifecycle]], [[registry/projects.csv]] |
| Scaffold | [[scaffold/checklist]], [[scaffold/rules]] |
| Reports | [[reports/session-summary-template]] |
| Prompt System | [[prompt-system/coremusic-theme-prompt]] |
| Knowledge | [[knowledge/verified]], [[knowledge/unverified]], [[knowledge/rejected]], [[confidence/README]] |
| Subdomains | [[subdomains/README]], [[subdomains/auth.coremusic.net/index]], [[subdomains/music.coremusic.net/index]], [[subdomains/download.coremusic.net/domains/index]] |
| UI-Design | [[ui-design/00-index]], [[ui-design/02-design-tokens]], [[ui-design/04-design-system]], [[ui-design/backend-reference]] |
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
| § 4 Mimari | [[architecture/l0-infrastructure]] | L0-L3 katmanları |
| § 5 ADR | [[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]] | Vault standardı |
| § 6 Servisler | [[ecosystem/7-service-integration]] | Servis entegrasyonu |
| § 7 Agentlar | [[AGENTS.md]] | Agent yetkileri |
| § 8 DB | [[architecture/05-data/database_master]] | 9 BCNF şemaları |
| § 9 Projeler | [[projects/NevaEngine/overview]] | C++ ses motoru |
| § 10 Donanım | [[electronic/hardware-roadmap]] | 3 fazlı geliştirme |
| § 11 Test | [[testing/coverage-targets]] | Kapsama hedefleri |

---

## 18. Metadata

- **Toplam dosya:** 404
- **Toplam ADR:** 50 (Frozen: 37, Active: 13)
- **Versiyon:** 19.0.0
- **Governance:** Red Team · Human Mode · Truth Mode

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode