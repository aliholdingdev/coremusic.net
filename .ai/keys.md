---
title: "CoreMusic — Vault Keyword Map & Concept Router"
type: system
category: vault-navigation
date: 2026-08-12
updated: 2026-08-13
status: active
version: 25.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/keys.md"
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

# CoreMusic — Vault Keyword Map & Concept Router

**Zorunlu Baglantilar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]] · [[.templates/index]] · [[.agents/AGENTS.md]]

**Skills:** `.opencode/skills/` (10 skill — Guardrail #16 zorunlu)

---

## 1. Amac

Bu dokuman, .ai/ vault icinde aranan kavramlarin aninda tespit edilmesini saglayan Master Kavram ve Dizin Yonlendirme Haritasidir. Ajanlarin ilk basvurduğu referans dosyasidir.

---

## 2. Core Vault Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| index, master, katalog | index.md |
| brain, mimari, dsp, kararlar | brain.md |
| memory, bellek, persistent, session | MEMORY.md |
| log, aktivite, audit, trail | log.md |
| agent, yetki, roller, handover | AGENTS.md |
| workflow, surec, faz, lifecycle | WORKFLOW.md |
| claude, talimat, protokol | CLAUDE.md |
| engine, orkestra, dispatch | engine.md |
| keys, keyword, navigasyon | keys.md |
| architecture-master, canonical count, metadata, ADR count, DB count, layer count | architecture/00-overview/architecture-master.md |

---

## 3. L0-L6 Layer Keywords

### 3.1 L0 Infrastructure

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| L0, altyapi, infrastructure, cache, APCu, Redis | architecture/l0-infrastructure.md |
| db, database, veritabani, PDO | architecture/l0-infrastructure.md |
| filesystem, IPC, shared memory | architecture/l0-infrastructure.md |
| credential vault, secret, key | architecture/l0-infrastructure.md |

### 3.2 L1 Security

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| L1, guvenlik, security, middleware, pipeline | architecture/l1-security.md |
| session, oturum, cookie, CSRF, csrf_token | architecture/l1-security.md |
| CSP, nonce, strict-dynamic, rate limit | architecture/l1-security.md |
| OWASP, zafiyet, tehdit | architecture/l1-security.md |

### 3.3 L2 Routing

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| L2, routing, SPA, single page, router | architecture/l2-routing.md |
| URL, normalization, subdomain | architecture/l2-routing.md |
| PageRouter, PageRouterKernel, HTML shell | architecture/l2-routing/spa-router.md |
| RouteRegistry, SpaRoute, route config | architecture/l2-routing/route-config.md |
| HtmlShellRenderer, CSP nonce, device CSS | architecture/l2-routing/html-shell-renderer.md |
| AuthGuard, AuthUrlBuilder, guard pipeline | architecture/l2-routing/guard-pipeline.md |
| JS Router, Router.js, DomPatcher, GuardPipeline | architecture/l2-routing/js-router.md |
| Middleware pipeline, session, CSRF | architecture/l2-routing/middleware-pipeline.md |
| Subdomain routing, port mapping | architecture/l2-routing/subdomain-routing.md |
| URL normalization, clean URL | architecture/l2-routing/url-normalization.md |
| Service discovery, health check | architecture/l2-routing/service-discovery.md |

### 3.4 L3 Presentation

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| L3, presentation, vanilla JS, framework yasak | architecture/l3-presentation.md |
| ITCSS, BEM, BEMIT, TrustedTypes, DOMParser | architecture/l3-presentation.md |
| Web Audio, ses API | architecture/l3-presentation.md |

### 3A. L4 Domain

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| L4, domain, business rules, entities, aggregates | architecture/l4-domain.md |
| DDD, value object, domain event | architecture/l4-domain.md |
| repository interface, use case interface | architecture/l4-domain.md |

### 3B. L5 Services

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| L5, services, application services, use case | architecture/l5-services.md |
| CQRS, command, query, event bus, PSR-14 | architecture/l5-services.md |
| transaction management, DTO mapping | architecture/l5-services.md |

### 3C. L6 Electronics

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| L6, electronics, hardware, firmware, driver, DSP | architecture/l6-electronics.md |
| XMOS, PCM3168A, Class AB, audio engine | architecture/l6-electronics.md |
| ASIO, WASAPI, JUCE, C++20 | architecture/l6-electronics.md |

### 3D. Skills Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| skill, beceri, agentic, orkestrasyon | .opencode/skills/*/SKILL.md |
| ui-code-generator, ui kod üretimi | .opencode/skills/ui-code-generator/SKILL.md |
| ui-analyzer, ui analiz | .opencode/skills/ui-analyzer/SKILL.md |
| skill-maker, skill oluştur | .opencode/skills/skill-maker/SKILL.md |
| hallucination-control, halüsinasyon | .opencode/skills/hallucination-control/SKILL.md |
| human-mode, insan onayı, HITL | .opencode/skills/human-mode/SKILL.md |
| red-team, truth mode, adversarial | .opencode/skills/red-team-truth-mode/SKILL.md |
| prompt-maker, prompt mühendisliği | .opencode/skills/prompt-maker/SKILL.md |
| agent-orchestrator, görev dağıtımı | .opencode/skills/agent-orchestrator/SKILL.md |
| composer-sync, vendor sync | .opencode/skills/composer-sync/SKILL.md |
| database-normalize, bcnf, normalizasyon | .opencode/skills/database-normalize-maker/SKILL.md |

### 3C. Templates Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| template, şablon, şablon | .ai/.templates/index.md |
| adr template, karar şablonu | .ai/.templates/adr/adr-template.md |
| php template, backend şablonu | .ai/.templates/backend/php-template.md |
| js template, frontend şablonu | .ai/.templates/frontend/js-template.md |
| css template, itcss şablonu | .ai/.templates/frontend/css-template.md |
| phpunit template, test şablonu | .ai/.templates/testing/phpunit-template.md |
| migration template, db migration | .ai/.templates/infrastructure/migration-template.md |
| docker template, container | .ai/.templates/infrastructure/docker-template.md |
| github actions, ci/cd şablonu | .ai/.templates/infrastructure/github-actions-template.md |
| api doc, api dokümantasyonu | .ai/.templates/documentation/api-doc-template.md |
| security audit, güvenlik denetimi | .ai/.templates/documentation/security-audit-template.md |
| c template, embedded şablonu | .ai/.templates/other/c-template.md |
| query template, sql şablonu | .ai/.templates/query/Query-Template.md |

### 3D. Agent Profile Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| agent profile, agent tanımlı | .ai/.agents/AGENTS.md |
| master orchestrator, mo | .ai/.agents/master-orchestrator.md |
| backend architect, php api | .ai/.agents/backend-architect.md |
| ui designer, frontend | .ai/.agents/ui-designer.md |
| security engineer, güvenlik | .ai/.agents/security-engineer.md |
| data engineer, veritabanı | .ai/.agents/data-engineer.md |
| embedded engineer, c++ | .ai/.agents/embedded-engineer.md |
| qa engineer, test | .ai/.agents/qa-engineer.md |
| devops engineer, ci/cd | .ai/.agents/devops-engineer.md |
| audio hardware, dac/adc | .ai/.agents/audio-hardware-engineer.md |
| dsp firmware, xmos | .ai/.agents/dsp-firmware-engineer.md |
| windows software, wasapi | .ai/.agents/windows-software-engineer.md |

### 3A. Frontend & UI Design Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| frontend, css, html, ui, layout, bileşen, ekran, sayfa, tasarım | .ai/ui-design/00-mockup-index.md |
| mockup, görsel, png, screenshot | .ai/ui-design/00-mockup-index.md + .ai/.png/** |
| component, bileşen, C01-C16, BEM | .ai/ui-design/01-component-inventory.md |
| implementation, uygulama, plan, css planı | .ai/ui-design/02-implementation-plan.md |
| accessibility, erişilebilirlik, wcag, touch target | .ai/ui-design/03-accessibility-gaps.md |
| header, footer, nav, navigation | .ai/ui-design/screens/_layout-patterns/ |
| modal, popup, overlay | .ai/ui-design/screens/F-quickpanel/ |
| auth, login, register, gender | .ai/ui-design/screens/A-auth/ |
| home, ana sayfa, dashboard | .ai/ui-design/screens/B-home/ |
| albums, albümler, artists, sanatçılar | .ai/ui-design/screens/C-music/ |
| playlist, player, oynatıcı | .ai/ui-design/screens/D-player/ |
| file manager, dosya yöneticisi, göz at | .ai/ui-design/screens/E-filemanager/ |
| wifi, bluetooth, quick panel | .ai/ui-design/screens/F-quickpanel/ |
| flow, akış, kullanıcı akışı | .ai/ui-design/flow/ |
| prompt, şablon | .ai/ui-design/prompt/ |
| design tokens, token, renk, yazı tipi | .ai/ui-design/reference/02-design-tokens.md |
| ascii art, piksel, ölçü, layout view | .ai/ui-design/screens/00-ascii-art-views.md |
| screen spec, ekran özelliği, pixel exact | .ai/ui-design/screens/ |
| layout pattern, standard 60/40, split home | .ai/ui-design/screens/_layout-patterns/ |
| png mockup, .png dosyası, görsel referans, screenshot | .ai/.png/home-1024/ + .ai/.png/shared-1024/ |
| home-1024, RPi5 mockup, 1024×600 | .ai/.png/home-1024/ |
| shared-1024, auth mockup, login png | .ai/.png/shared-1024/ |
| png mockup index, mockup tablosu | .ai/ui-design/00-mockup-index.md |
| component inventory, bileşen envanteri | .ai/ui-design/01-component-inventory.md |
| implementation plan, uygulama planı | .ai/ui-design/02-implementation-plan.md |
| accessibility gaps, wcag analizi | .ai/ui-design/03-accessibility-gaps.md |
| vault registration, vault kayıt | .ai/ui-design/04-vault-registration.md |

---

## 4. Security Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| CSRF, csrf_token, koruma | [[decisions/accepted/ADR-010-csrf-protection-strategy]] |
| Session, COREMUSIC_SESS, idle | [[decisions/accepted/ADR-011-session-management]] |
| CSP nonce, strict-dynamic | [[decisions/accepted/ADR-012-csp-nonce-strict-dynamic]] |
| Rate Limit, APCu, 60 req/60s | [[decisions/accepted/ADR-013-rate-limiting-apcu]] |
| Argon2id, AES-256-GCM, sifreleme | [[decisions/accepted/ADR-022-database-hardened-security]] |
| credential vault, secret | [[decisions/accepted/ADR-034-credential-vault-normalization]] |
| BypassAuth, test bypass | [[decisions/accepted/ADR-008-bypass-auth-middleware]] |
| auth, kimlik dogrulama | subdomains/auth.coremusic.net/index |
| OWASP, Top 10 | architecture/07-security/security/owasp-compliance |
| encryption | architecture/07-security/encryption |
| API security, token | architecture/07-security/api/api_security_master |
| loglama, logging, PSR-3, Monolog | architecture/07-security/deep-logging-system |
| log_events, log_security, log_performance | architecture/07-security/deep-logging-system |
| log_activity, log_system, redaction | architecture/07-security/deep-logging-system |
| real-time log, dashboard log, monitor | architecture/07-security/deep-logging-system |
| dosya rotasyonu, log rotation, arsiv | architecture/07-security/deep-logging-system |

---

## 5. Database Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| 18 BCNF, normalizasyon | [[decisions/accepted/ADR-040-database-authority]] |
| ORM, SELECT *, PDO | [[decisions/accepted/ADR-002-pdo-mandatory-no-orm]] |
| multi-db, 18 veritabani | [[decisions/accepted/ADR-003-multi-db-9-databases]] |
| migration, schema degisikligi | [[decisions/accepted/ADR-014-multi-db-migration-strategy]] |
| SQL normalization | [[decisions/accepted/ADR-033-sql-normalization-strategy]] |
| DB sync | [[decisions/accepted/ADR-050-multi-db-sync-strategy]] |
| database master | architecture/05-data/database_master.md |
| coremusic_musics | .sql/mysql/coremusic_musics.sql |
| coremusic_auth | .sql/mysql/coremusic_auth.sql |
| coremusic_user | .sql/mysql/coremusic_user.sql |
| coremusic_albums | .sql/mysql/coremusic_albums.sql |
| coremusic_playlist | .sql/mysql/coremusic_playlist.sql |
| coremusic_catalog | .sql/mysql/coremusic_catalog.sql |
| coremusic_logs | .sql/mysql/coremusic_logs.sql |
| coremusic_media | .sql/mysql/coremusic_media.sql |
| coremusic_system | .sql/mysql/coremusic_system.sql |
| coremusic_social | .sql/mysql/coremusic_social.sql |
| coremusic_wireless | .sql/mysql/coremusic_wireless.sql |
| coremusic_download | .sql/mysql/coremusic_download.sql |
| coremusic_ai | .sql/mysql/coremusic_ai.sql |
| coremusic_api | .sql/mysql/coremusic_api.sql |
| coremusic_cms | .sql/mysql/coremusic_cms.sql |
| coremusic_neva | .sql/mysql/coremusic_neva.sql |
| coremusic_studio | .sql/mysql/coremusic_studio.sql |
| coremusic_patch | .sql/mysql/coremusic_patch.sql |

---

## 6. Audio Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| ASIO, ses surucusu, low latency | [[decisions/accepted/ADR-017-dsp-hardware-mode]] |
| Neva Engine, C++, JUCE | projects/NevaEngine/overview |
| equalizer, EQ, 31-band | projects/NevaEngine/equalizer-system |
| DSP chain, routing matrix | projects/NevaEngine/eq-dsp-chain |
| spatial audio, surround | projects/NevaEngine/spatial-audio |
| VST3, plugin, MIDI | projects/NevaEngine/vst3-hosting |
| 32-bit float, ring buffer | architecture/06-audio/audio-pipeline.md |
| 8.1 surround, PCM3168A | [[decisions/accepted/ADR-038-8.1-sound-card-chip-selection]] |
| ASIO/WASAPI/CoreAudio | architecture/06-audio/audio-platform-decision.md |
| audio service | architecture/06-audio/coremusic-audio-service.md |
| device service, BT, WiFi | architecture/06-audio/coremusic-device-service.md |
| network audio, WebRTC | architecture/06-audio/coremusic-network-audio-service.md |
| AI service | architecture/06-audio/coremusic-ai-service.md |
| YouTube, deemix, FLAC | architecture/06-audio/ai-auto-download.md |

---

## 7. Hardware Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| PCM3168A, 8 kanal, DAC | [[decisions/accepted/ADR-038-8.1-sound-card-chip-selection]] |
| PCM5122, REDDED, H001 | [[decisions/accepted/ADR-038-8.1-sound-card-chip-selection]] |
| XMOS XU316, DSP | [[decisions/accepted/ADR-017-dsp-hardware-mode]] |
| AK4458, DAC opsiyonel | electronic/audio-interface-design.md |
| Class AB, amfi, 100W | electronic/amplifier-design.md |
| hardware roadmap, 3 faz | electronic/hardware-roadmap.md |
| audio organization, 5 bolum | electronic/audio-organization.md |
| ASIO driver | electronic/asio-driver-design.md |
| xmos-pcm3168a, devre | electronic/xmos-pcm3168a-design.md |
| audio interface, PCM3168A devre | electronic/audio-interface-design.md |
| frequency response, frekans yaniti | electronic/frequency-response.md |
| SNR, THD, THD+N, olcum | electronic/snr-thd-measurement.md |
| test protokolu, hardware test | electronic/test-protocols.md |
| termal analiz, is sicaklik | electronic/thermal-analysis.md |
| DSP pipeline, equalizer, crossover | electronic/dsp/index.md |
| driver framework, USB, BT, WiFi | electronic/drivers/index.md |
| amplifier architecture, power supply | electronic/amplifier/index.md |
| hardware design, PCB | electronic/hardware/index.md |
| firmware, RTOS, OTA | electronic/firmware/index.md |
| electronics architecture, L6 | [[decisions/accepted/ADR-061-electronics-architecture]] |
| DSP pipeline architecture | [[decisions/accepted/ADR-062-dsp-pipeline-architecture]] |
| hardware design standards | [[decisions/accepted/ADR-063-hardware-design-standards]] |
| CoreMusic Electronics, genel bakis | electronic/core-music-electronics-overview.md |
| platform architecture, 9 katman | electronic/platform-architecture.md |
| device architecture, cihaz aileleri | electronic/device-architecture.md |
| OS architecture, PAL, platform adapter | electronic/operating-system-architecture.md |
| device ecosystem, cihaz ekosistemi | electronic/device-ecosystem.md |
| software architecture, 5 katman | electronic/software-architecture.md |
| service architecture, 13 servis | electronic/service-architecture.md |
| audio architecture, ses pipeline | electronic/audio-architecture.md |
| DSP engine, EQ compressor limiter | electronic/dsp-engine-architecture.md |
| driver framework, ASIO WASAPI ALSA | electronic/driver-framework.md |
| amplifier architecture, 8+1 | electronic/amplifier-architecture.md |
| hardware design, PCB EMI EMC | electronic/hardware-design.md |
| firmware architecture, RTOS HAL OTA | electronic/firmware-architecture.md |
| L6 electronics, layer stack | architecture/l6-electronics.md |
| network architecture, MQTT gRPC IPC | architecture/network-architecture.md |
| database architecture, BCNF SQLite | architecture/database-architecture.md |
| security architecture, OWASP RBAC | architecture/security-architecture.md |
| electronics security, secure boot | architecture/07-security/electronics-security.md |
| engineering rules, SSOT | architecture/03-contracts/engineering-rules-ssot.md |
| diagram collection, mermaid | architecture/03-contracts/diagram-collection.md |
| development workflow, 20 faz | architecture/03-contracts/development-workflow.md |
| development standards, SOLID DDD | architecture/03-contracts/development-standards.md |
| software architecture, 5 katman, DDD, CQRS | electronic/software-architecture.md |
| service architecture, 13 servis, API Gateway | electronic/service-architecture.md |
| device ecosystem, cihaz ekosistemi, OTA | electronic/device-ecosystem.md |

---

## 7A. AI Architecture Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| AI engine, recommendation, music AI | architecture/ai/ai-engine.md |
| AI orchestrator, task dispatch | architecture/ai/ai-orchestrator.md |
| agent system, 11 agents | architecture/ai/agent-system.md |
| knowledge base, semantic search | architecture/ai/knowledge-base.md |
| memory system, session memory | architecture/ai/memory-system.md |
| prompt engine, prompt generation | architecture/ai/prompt-engine.md |
| tool calling, external tools | architecture/ai/tool-calling.md |
| MCP, model context protocol | architecture/ai/mcp-integration.md |
| AI workflow, recommendation engine | architecture/ai/ai-workflow.md |
| AI strategy, prompt engineering | [[decisions/accepted/ADR-030-ai-strategy-core]] |
| system prompt, prompt standards | [[decisions/accepted/ADR-035-system-prompt-engineering]] |
| multi-project prompt | [[decisions/accepted/ADR-036-multi-project-prompt-maker]] |
| startup prompt loader | [[decisions/accepted/ADR-049-startup-prompt-loader]] |
| AI electronics engine | architecture/ai/ai-electronics-engine.md |
| AI workflow electronics | architecture/ai/ai-workflow-electronics.md |
| ai-workflow-standards | architecture/03-contracts/ai-workflow-standards.md |
| AI electronics engine, donanım analizi | architecture/ai/ai-electronics-engine.md |
| AI workflow electronics | architecture/ai/ai-workflow-electronics.md |

---

## 8. Panel & Service Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| 10 panel, Music, Admin, Car | subdomains/README.md |
| music.coremusic.net, port 81 | subdomains/music.coremusic.net/index.md |
| auth.coremusic.net | subdomains/auth.coremusic.net/index.md |
| download.coremusic.net, port 3001 | subdomains/download.coremusic.net/domains/index.md |
| Control Service, port 81 | architecture/06-audio/coremusic-control-service.md |
| Media Service, port 5000/6000 | architecture/06-audio/coremusic-media-service.md |
| Audio Service, port 9741/9742 | architecture/06-audio/coremusic-audio-service.md |
| 7 servis, platform | [[decisions/accepted/ADR-039-7-service-platform-architecture]] |
| servis entegrasyonu | ecosystem/7-service-integration.md |
| health check | ecosystem/service-health-check.md |

---

## 9. Theme & CSS Keywords

| Anahtar Kelime | Hedef Dosya |
|---------------|-------------|
| tema, theme, dinamik tema | [[decisions/accepted/ADR-044-dynamic-user-theme-engine]] |
| cinsiyet, gender, pembe, mavi | [[decisions/accepted/ADR-044-dynamic-user-theme-engine]] |
| data-gender, CSS tokens | [[decisions/accepted/ADR-044-dynamic-user-theme-engine]] |
| multi-domain view mode | [[decisions/accepted/ADR-045-multi-domain-view-mode-architecture]] |
| cross-view state | [[decisions/accepted/ADR-046-cross-view-state-preservation]] |
| View Transition API | [[decisions/accepted/ADR-048-view-transition-api-integration]] |
| device-loader, cihaz algilama | assets.coremusic.net/js/device-loader.js |
| 08_Devices, d-phone, d-tablet | assets.coremusic.net/Css/08_Devices/ |
| 09_ViewModes, v-home, v-pro | assets.coremusic.net/Css/09_ViewModes/ |

---

## 10. ADR Keyword Mapping (001-050)

| ADR | Anahtar Kelimeler | Kategori |
|-----|-------------------|----------|
| ADR-001 | vanilla JS, ITCSS, framework yasak | Frontend |
| ADR-002 | PDO, ORM yasak, SELECT *, prepared | Database |
| ADR-003 | 9 BCNF, multi-db | Database |
| ADR-004 | multi-domain SPA, vault versiyonlama | Architecture |
| ADR-005 | ultrathink, zero hallucination | Quality |
| ADR-006 | performans, hedef, benchmark | Performance |
| ADR-007 | cache namespace, zero code before plan | Infrastructure |
| ADR-008 | bypass auth, test bypass | Security |
| ADR-009 | clean URL, redirect | Routing |
| ADR-010 | CSRF, csrf_token, form | Security |
| ADR-011 | session, COREMUSIC_SESS, idle timeout | Security |
| ADR-012 | CSP, nonce, strict-dynamic | Security |
| ADR-013 | rate limit, APCu | Security |
| ADR-014 | migration, multi-DB | Database |
| ADR-015 | env parser, .env | Infrastructure |
| ADR-016 | URL normalization | Routing |
| ADR-017 | DSP hardware, XMOS, JUCE, ASIO | Audio |
| ADR-018 | footer player, vaporwave | UI |
| ADR-019 | Neva Player, per-OS | Audio |
| ADR-020 | API public, guvenlik | Security |
| ADR-021 | SPA router, immutable contract | Routing |
| ADR-022 | DB hardened, Argon2id, AES-256-GCM | Security |
| ADR-023 | persona-driven, test | Testing |
| ADR-024 | ecosystem, modular docs | Documentation |
| ADR-025 | professional EQ, 31-band | Audio |
| ADR-026 | download service, architecture | Architecture |
| ADR-027 | dual-mode storage | Infrastructure |
| ADR-028 | anti-ban, ARL token | Download |
| ADR-029 | listening rooms, social | Social |
| ADR-030 | AI strategy, core | AI |
| ADR-031 | mobile, PWA, Flutter | Mobile |
| ADR-032 | IPC contract, versioning | Architecture |
| ADR-033 | SQL normalization | Database |
| ADR-034 | credential vault, AES-256-GCM | Security |
| ADR-035 | system prompt, engineering | AI |
| ADR-036 | multi-project, prompt maker | AI |
| ADR-037 | WirelessConnect, WiFi | Integration |
| ADR-038 | PCM3168A, XMOS XU316, 8.1 surround | Audio |
| ADR-039 | 7 servis, platform mimarisi | Architecture |
| ADR-040 | 18 BCNF, DB authority | Database |
| ADR-041 | DB normalization supplementary | Database |
| ADR-042 | vault restructuring, PHP 8.4, port 81 | Vault |
| ADR-043 | auth subdomain, konsolidasyon | Security |
| ADR-044 | dynamic theme, gender, pembe/mavi | UI |
| ADR-045 | multi-domain view mode | UI |
| ADR-046 | cross-view state | UI |
| ADR-047 | login redirect, session bridge | Auth |
| ADR-048 | View Transition API | UI |
| ADR-049 | startup prompt loader | AI |
| ADR-050 | multi-db sync | Database |
| ADR-061 | electronics architecture, L6 layer | Electronics |
| ADR-062 | DSP pipeline architecture | Electronics |
| ADR-063 | hardware design standards | Electronics |
| ADR-064 | electronics platform, L6, 5 cihaz ailesi, 13 servis | Electronics |
| ADR-072 | social database, comments, shares, activity, notifications | Database |
| ADR-073 | podcast database, shows, episodes, transcripts | Database |
| ADR-074 | radio database, stations, schedules, now_playing | Database |
| ADR-075 | ai database, preferences, features, recommendations, models | Database |
| ADR-076 | video database, music_videos, playback, subtitles | Database |
| ADR-077 | studio database, sessions, tracks, presets, equipment | Database |
| ADR-078 | cms database, pages, blog, tags, faqs, banners | Database |
| ADR-079 | i18n database, languages, translations, ui_strings | Database |
| ADR-083 | SPA Router, PHP+JS Hybrid, History API, DOMParser | Routing |
| ADR-084 | API Gateway, API-First, BFF, CQRS, Tek Gateway | Architecture |
| ADR-085 | Shared Library Hybrid, tek shared/ + PSR-4 namespace, circular dependency yasak | Infrastructure |
| ADR-086 | Event Driven, PSR-14, Domain Event, Integration Event | Architecture |
| ADR-087 | Master Implementation Plan, 5 faz, 40 gun, 22 bolum, sirfirdan gelistirme | Architecture |

---

## 11. Decision Tree

```
Istenen Bilgi -> Ilk Kontrol:
|
|-- Mimari/Layer -> architecture/l0-infrastructure/ | l1-security/ | l2-routing/ | l3-presentation/
|-- ADR Karari -> decisions/accepted/ADR-NNN-*.md
|-- Guvenlik -> ADR-010/011/012/013/022 + architecture/07-security/
|-- Veritabani -> ADR-040 + architecture/05-data/database_master.md + .sql/
|-- Ses/Donanim -> ADR-017/038 + electronic/ + projects/NevaEngine/
|-- Panel/Servis -> subdomains/ + architecture/06-audio/
|-- Test -> testing/strategy.md + testing/coverage-targets.md
|-- Vault -> index.md -> keys.md (bu dosya)
```

---

## 12. Navigation Rules (ADR-042 Uyumlu)

### 12.2 Zero Misdirection

| Yanlis | Dogru |
|-----------|----------|
| Tahmin yurutme | keys.md'den keyword ara |
| Recursive glob | Doğrudan glob kullan |
| Web arama | Sadece vault + ADR referanslari |
| Kodu okumadan tahmin | Once kodu oku, sonra ADR |
| Uydurma API/endpoint | // VERIFICATION REQUIRED yaz |

### 12.3 Oncelik Matrisi

```
P0: CLAUDE.md, AGENTS.md, WORKFLOW.md
P1: index.md, keys.md, brain.md, MEMORY.md, log.md
P2: decisions/accepted/ADR-NNN, architecture/L[0-3]/*
P3: testing/*, ui-design/*, personas/*
```

---

## 13. Troubleshooting

| Sorun | Cozum |
|-------|-------|
| Dosya bulunamadi | keys.md veya index.md icinde grep ile ara |
| Token limiti asildi | Buyuk dosyalari parca parca oku (offset/limit) |
| Kirik wiki-link | index.md'de gercek dosya yolunu dogrula |
| Eski ADR referansi | decisions/accepted/ dizininde ADR-NNN ara |
| Bilinmeyen terim | brain.md'de teknik detaylari kontrol et |
| Yanlis port/protokol | Bolum 6'daki port haritasina bak |
| BCNF ihlali | ADR-040 ve architecture/05-data/ kontrol |
| CSRF hatasi | ADR-010 ve architecture/07-security/ kontrol |
| Middleware sirasi | ADR-010/011/012/013/022 -- sira FROZEN |

---

## 14. Quick Reference

| Ihtiyac | Ilk Adim |
|---------|----------|
| Mimari karar | brain.md -> decisions/accepted/ |
| Guvenlik | architecture/l1-security.md -> ADR-010/011/012/013/022 |
| Veritabani | architecture/05-data/database_master.md -> ADR-040 |
| Frontend | architecture/l3-presentation.md -> ADR-001 |
| Backend | architecture/l2-routing.md -> ADR-002 |
| Audio/Donanim | electronic/ -> ADR-017/038 |
| Test | testing/strategy.md -> testing/coverage-targets.md |
| Vault yapisi | index.md -> bu dosya (keys.md) |
| Agent yetkileri | AGENTS.md -> .agents/ |
| Servisler | ecosystem/7-service-integration.md |
| Deploy | architecture/02-deployment/ |
| Tema | ADR-044 -> [[brain#22-prompt-arsivi]] (prompt0-genel içinde tema kuralları) |

### Section 3B: Prompt Archive Keywords

| Keywords | Dosya |
|----------|-------|
| prompt0, genel ana prompt, tüm sistem kuralları, 11 alt domain, 10 panel, 20 analiz görevi | archives/prompt0-genel-ana-prompt-2026-08-13 |
| prompt1, spa router, enterprise router, history api, SOLID, PSR, attribute-based | archives/prompt1-spa-router-2026-08-13 |
| prompt2, auth, merkezi auth, jwt, session, cors, rbac, middleware pipeline | archives/prompt2-auth-2026-08-13 |
| prompt3, api-first, gateway, cqrs, event driven, 14 servis, coremusic-shared | archives/prompt3-api-2026-08-13 |

---

## 15. Critical Warnings

| # | Uyari |
|---|-------|
| 1 | **Rastgele okuma yasak.** Her zaman keys.md kullanin. Token asimina yol acar. |
| 2 | **PCM5122 REDDEDILMISTIR (H001).** 8.1 surround icin yetersiz. Sadece PCM3168A kullanin. |
| 3 | **CSRF Token Key = csrf_token.** _csrf_token 2026-05-30'da kaldirildi. |
| 4 | **Middleware sirasi degistirilemez.** OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf → BypassAuth → Auth → Permission → Validation → Controller |
| 5 | **ORM yasak.** Sadece PDO prepared statement. SELECT * yasak -- acik kolon listesi zorunlu. |

---

## 16. Cross References

| Kaynak | Hedef |
|--------|-------|
| keys.md | [[CLAUDE.md]], [[AGENTS.md]], [[WORKFLOW.md]], [[index.md]], [[brain.md]], [[MEMORY.md]], [[log.md]] |
| keys.md | [[decisions/accepted/ADR-004-multi-domain-spa]], [[decisions/accepted/ADR-005-ultrathink-protocol]] |

---

## 17A. Implementasyon Dosya Haritasi (2026-08-12)

| Keyword | Dosya |
|---------|-------|
| bootstrap, autoload, PROJECT_ROOT | shared/bootstrap.php |
| container, DI, services, binding | shared/config/container.php, shared/config/services.php |
| middleware, pipeline, frozen sıra | shared/config/middleware.php |
| route, CORS, auth config | shared/config/routes.php, shared/config/cors.php, shared/config/auth.php |
| Router, fast-route, group, cache | shared/src/Router/Router.php, RouteDefinition.php, GroupDefinition.php |
| HttpKernel, pipeline, dispatch | shared/src/Http/HttpKernel.php, Response.php |
| User, Role, Session, Token (entity) | shared/src/Auth/Domain/ |
| Email, Password, UserId (VO) | shared/src/Auth/Domain/ValueObjects/ |
| UserRepository, SessionRepository, TokenRepository | shared/src/Auth/Domain/Repository/ |
| LoginRequest, LoginResponse, TokenPair, SessionDTO | shared/src/Auth/Application/DTO/ |
| Argon2id, JwtTokenManager, PDO Repo | shared/src/Auth/Infrastructure/ |
| OriginCheck, Cors, RateLimiter, SecurityHeaders, SessionManager, Csrf, BypassAuth, Auth, Permission, Validation | shared/src/Security/Middleware/ |
| CspNonceGenerator, SecurityHeaderService, RateLimiter | shared/src/Security/Service/ |
| LoginUseCase, LogoutUseCase, RegisterUseCase | shared/src/Auth/Application/ |
| auth.coremusic.net entry | auth.coremusic.net/index.php |
| home.coremusic.net entry | home.coremusic.net/index.php |
| music.coremusic.net entry | music.coremusic.net/index.php |
| admin.coremusic.net entry | admin.coremusic.net/index.php |
| api.coremusic.net entry | api.coremusic.net/index.php |
| landing entry | coremusic.net/index.php |
| ITCSS, CSS, design tokens, BEM | assets.coremusic.net/css/ |
| SPA Router, AuthManager, DomPatcher | assets.coremusic.net/js/ |
| 18 BCNF, 156 tablo | .ai/.sql/mysql/ |
| JWT key pair | shared/config/keys/private.pem, public.pem |

---

## 17. Quality Report

| Metrik | Deger |
|--------|-------|
| Version | 25.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| ADR Coverage | 001-087 (87 ADR keyword mapping) |
| Vault Envanteri | 484+ .md dosyasi, 87 ADR, 18 BCNF DB, 10 panel, 7 servis, shared/ hybrid yapı |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-13
**Mode:** Red Team · Human Mode · Truth Mode