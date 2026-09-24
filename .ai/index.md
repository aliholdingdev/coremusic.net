---
title: "CoreMusic Vault - Master Index"
type: system
category: vault-navigation
status: active
authority: SSOT
version: 28.2.0
updated: 2026-09-24
total_files: 538
total_adr: 79
total_adr_disk: 0
---

# CoreMusic Vault — Master Index

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]] · [[.templates/index]] · [[.agents/AGENTS.md]]

**Skills:** `.opencode/skills/` (10 skill — Guardrail #16 zorunlu)

---

## Purpose

### §1 Amaç

Bu dosya, CoreMusic `.ai/` vault'unun ana navigasyon noktasıdır. Tüm vault dosyaları kategorize edilmiş ve hızlı erişilebilir biçimde listelenir.

---

## Scope

### §2 Quick Reference

| İhtiyaç | İlk Adım |
|---------|----------|
| Vault genel bakış | Bu dosya (index.md) |
| Ürün & Ekosistem Vizyonu | [[VISION.md]] |
| Proje Tanımı & Hangi Sorunları Çözer | [[PROJECTS.md]] |
| Teknik Dokümantasyon & Kılavuz | [[TECHNICAL_DOCUMENTATION.md]] |
| Keyword arama | [[keys.md]] |
| Terimler Sözlüğü | [[glossary.md]] |
| Mimari kararlar | [[brain.md]] |
| Ajan yetkileri | [[AGENTS.md]] |
| Süreçler | [[WORKFLOW.md]] |
| Bellek yönetimi | [[MEMORY.md]] |
| Aktivite günlüğü | [[log.md]] |
| ADR kataloğu | § 5 bu dosya |
| Servis haritası | § 6 bu dosya |
| Veritabanı | § 8 bu dosya |
| UI / Mockup / Frontend | [[ui-design/01-mockup-index]] (19 PNG Mockup, C01-C16 Envanteri, 45-Tier Device Matrix) |

---

### §3 SSOT Core Dosyaları (14 Kök Boot + 1 Ek Doküman)

| # | Dosya | Amaç |
|---|-------|------|
| 1 | [[CLAUDE.md]] | Kanonik AI talimatı — boot protokolü, guardrails |
| 2 | [[AGENTS.md]] | Agent kayıt defteri — 11 uzmanlık alanı + MO, koordinasyon |
| 3 | [[WORKFLOW.md]] | Süreçler — vault refactoring, ürün döngüsü |
| 4 | [[index.md]] | Bu dosya — tüm vault dizin yapısı |
| 5 | [[keys.md]] | Anahtar kelime haritası — keyword → dosya yönlendirme |
| 6 | [[brain.md]] | Mimari kararlar — ADR 001-088 (79 karar), L0-L6, engineering brain |
| 7 | [[MEMORY.md]] | Oturum hafızası — persistent state, cache, session lifecycle |
| 8 | [[log.md]] | Aktivite günlüğü — append-only audit trail |
| 9 | [[engine.md]] | Orkestrasyon motoru — agent koordinasyonu, task dispatch |
| 10 | [[VISION.md]] | Ürün ve ekosistem vizyonu — mülkiyet felsefesi, 6 sorun-çözüm, handoff, hibrit omurga |
| 11 | [[PROJECTS.md]] | CoreMusic nedir, 10 temel yetenek, 6 hedef kullanıcı, 9 kullanım senaryosu, sektörel çözümler |
| 12 | [[glossary.md]] | Terimler sözlüğü — Neva Engine, Bit-Perfect, Ambient Aura, Deemix vb. |
| 13 | [[ROLE.md]] | Rol ve sorumluluk tanımı — Senior Software Architect |
| 14 | [[ULTRA-THINKING.md]] | Ultra düşünme protokolü — karar öncesi zorunlu doğrulama |
| 15 | [[TECHNICAL_DOCUMENTATION.md]] | Teknik dokümantasyon, sistem kullanım kılavuzu ve freelancer geliştirici kuralları — ⚠️ VERIFICATION REQUIRED (boot 14 dışı; .ai/ kökünde diskte yok) |


---

## Architecture

### §4 Mimari — L0-L6 Katmanları

*Detaylı metadata için bakınız: [[architecture/index]] §2*

Bağımlılık kuralları: ✅ L6→L5, L5→L4, L4→L3, L3→L2, L2→L1, L1→L0 | ❌ L0→L2/L3, L1→L3, L3→L0

| Katman | Dosya | Kapsam |
|--------|-------|--------|
| L6 Electronics | [[architecture/l6-electronics]] | Hardware, firmware, driver, DSP, audio engine |
| L5 Services | [[architecture/k8-servis]] | Application services, use cases, CQRS, event bus |
| L4 Domain | [[architecture/l4-domain]] | Business rules, entities, value objects, aggregates |
| L3 Presentation | [[architecture/k11-ux]] · [[ui-design/01-mockup-index]] | Frontend, UI, DOM, responsive, 19 PNG mockup, C01-C16 |
| L3 Rehber | [[architecture/l3-presentation/scale-router-css-frontend-guide]] | Scale, Router, CSS & Frontend Entegrasyon Rehberi (adım adım) |
| L2 Routing | [[architecture/k9-api-routing]] | SPA PageRouter, API Gateway, subdomain routing |
| L1 Security | [[architecture/k6-guvenlik]] | Middleware pipeline, session, auth, CSRF, CSP |
| L0 Infrastructure | [[architecture/k0-isletim-sistemi]] | Database, cache, filesystem, IPC, credential vault |

---

### §4A UI Design System & Mockup Otoritesi (Kanonik SSOT)

**Tüm frontend (HTML/CSS/JS/PHP layout) geliştirme görevlerinin tartışmasız başlangıç noktası ve Tek Doğruluk Kaynağıdır (SSOT).**

| Dosya / Dizin | İçerik ve Amaç | Zorunluluk |
|---------------|----------------|------------|
| [[ui-design/01-mockup-index]] | 19 PNG Mockup İndeksi (12 home-1024 + 6 shared-1024) | ✅ Tüm frontend görevlerinde İLK OKUNACAK |
| [[ui-design/02-component-inventory]] | C01–C16 Kanonik Bileşen Envanteri (BEM, ölçüm, token) | ✅ Bileşen kodlarken ZORUNLU |
| [[ui-design/03-implementation-plan]] | 15 Adımlık CSS Uygulama Yol Haritası | ✅ CSS yazarken ZORUNLU |
| [[ui-design/04-accessibility-gaps]] | WCAG 2.2 AA Uyum ve Touch Target Denetimi (min 48px) | ✅ Erişilebilirlik için ZORUNLU |
| [[ui-design/screens/00-ascii-art-index]] | Piksel düzeyinde ASCII Art ekran modelleri (x:0-1024, y:0-600) | ✅ Layout hizalamada ZORUNLU |
| [[ui-design/tokens/design-tokens-master]] | Master CSS Design Tokens (Renk, Boşluk, Tipografi, Cam) | ✅ Token kullanımında ZORUNLU |
| [[ui-design/prompt/00-prompt-index]] | Ekran, Bileşen, Layout ve Sayfa Prompt Şablonları | ✅ Kod üretiminde ZORUNLU |
| `.ai/.png/home-1024/` & `shared-1024/` | 19 Orijinal PNG Mockup Görselleri | ✅ Görsel referans doğrulamada ZORUNLU |
| [[ui-design/reference/01-php-source-architecture]] | PHP backend yapılandırması, DeviceManager entegrasyonu | ✅ Backend entegrasyonunda ZORUNLU |
| [[ui-design/reference/02-text-strings]] | UI text stringleri (80+ Türkçe string) | ✅ Localization'da ZORUNLU |
| [[ui-design/reference/03-icon-asset-catalog]] | İkon envanteri, asset yolları, tier bazlı boyutlar | ✅ İkon entegrasyonunda ZORUNLU |
| [[ui-design/reference/04-verification]] | UI doğrulama protokolü, PNG karşılaştırma | ✅ Her frontend görevinden sonra ZORUNLU |
| [[ui-design/reference/05-backend-reference]] | PHP API endpoint'leri, session/auth | ✅ Backend entegrasyonunda ZORUNLU |
| [[ui-design/reference/06-frontend-reference]] | Vanilla JS + ITCSS 9-layer yapısı, BEM | ✅ Frontend geliştirme ZORUNLU |
| [[ui-design/reference/07-session-notes]] | Geliştirme oturum notları, TODO'lar | ✅ Oturum devamında ZORUNLU |
| [[ui-design/reference/08-css-design-tokens]] | CSS custom properties hızlı referans | ✅ CSS yazarken ZORUNLU |
| [[ui-design/reference/09-interaction-states]] | Bileşen etkileşim durumları, touch vs mouse | ✅ Bileşen geliştirme ZORUNLU |
| [[ui-design/reference/10-device-specific-guidelines]] | 10 cihaz kategorisi için UI kılavuzu | ✅ Tier bazlı geliştirme ZORUNLU |

---

### §4A.1 Device-Aware Rendering Kuralları (v1.0.0)

**Tek bileşen ilkesi + cihaz bazlı CSS override sistemi. Detaylı kurallar: [[brain.md]] §18C**

| Kural | Açıklama | Referans |
|-------|----------|----------|
| Tek HTML yapısı | `home.php`, `header.php`, `footer.php` tek dosya | Guardrail #17 |
| Ayrı dosya yasağı | `home-1024.php`, `home-desktop.html` YASAKTIR | Guardrail #17 |
| Backend sorumluluğu | PHP: davranışsal konfigürasyon (widget count, feature toggle) | [[brain.md]] §18C |
| Frontend sorumluluğu | CSS: sunum kararları (token, media query, grid) | [[brain.md]] §18C |
| Tek bileşen + CSS | Fark CSS media query + CSS variables ile yönetilir | [[ui-design/05-responsive-architecture]] |
| WCAG 2.2 AA | Phone/Embedded: min 48px touch target | [[ui-design/04-accessibility-gaps]] |
| Katman ihlal | PHP'de margin/padding/width/height kodlanamaz | [[brain.md]] §18C |

| Dosya | İçerik | Kullanım |
|-------|--------|----------|
| [[ui-design/05-responsive-architecture]] | 4-Tier Conditional Rendering mimarisi | Cihaz bazlı layout kararları |
| [[architecture/l3-presentation/device-css]] | 7 device CSS + 4 view mode CSS | Behavioral overrides |
| [[brain.md]] §18A | Responsive CSS Architecture Rules | Token tanımları, yasak örüntüler |
| [[brain.md]] §18B | 4-Tier Device Manager Sistemi | DeviceManager karar metotları |
| [[brain.md]] §18C | Device-Aware Rendering Kuralları | Backend/Frontend sorumluluk sınırları |

---

### §6 10 Panel & 7 Backend Servis

### §6.1 Frontend Paneller

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

### §6.2 Backend Servisler

| Servis | Port | Protocol | Stack |
|--------|------|----------|-------|
| Control Service | 81 | HTTP | PHP 8.4 (Auth, Session, RBAC) |
| Media Service | 5000/6000 | HTTP | PHP + FFmpeg (Library, Metadata) |
| Audio Service | 9741/9742 | REST/WS | C++20 JUCE (Player, DSP, Mixer) |
| Device Service | — | BLE/WiFi/USB | C++20 (Bluetooth, WiFi, USB) |
| Network Audio | — | WebRTC/P2P | C++20 (Streaming, Multi-room) |
| AI Service | — | Internal | PHP + Python (Recommendations) |
| Download Service | 3001 | HTTP/WS | Node.js + TypeScript |

---

### §6.3 Port Haritası

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

### §7 Agent Sistemi

| Agent | Domain | Teknoloji |
|-------|--------|-----------|
| Master Orchestrator | Görev dağıtımı, orkestrasyon | Vault System, log.md |
| Backend Architect | PHP 8.4 API, middleware | L2 Routing, controller, repository |
| UI Designer | Vanilla JS, ITCSS, Web Audio | L3 Presentation, responsive, accessibility |
| Security Engineer | OWASP, AES-256-GCM, Argon2id | L1 Security, CSRF, CSP, session |
| Data Engineer | MySQL 18 BCNF, PDO | L0 Infrastructure, schema, migration |
| Embedded Engineer | C++20, JUCE, ASIO | Audio DSP, hardware, ring buffer |
| QA Engineer | PHPUnit, Vitest, Playwright | Testing, coverage, E2E |
| DevOps Engineer | CI/CD, GitHub Actions, GitLeaks | Deployment, pipeline, monitoring |

Agent profile dosyaları: [[.agents/AGENTS.md]], [[.agents/backend-architect]], [[.agents/ui-designer]], [[.agents/security-engineer]], [[.agents/data-engineer]], [[.agents/embedded-engineer]], [[.agents/qa-engineer]], [[.agents/devops-engineer]], [[.agents/master-orchestrator]], [[.agents/audio-hardware-engineer]], [[.agents/dsp-firmware-engineer]], [[.agents/windows-software-engineer]]

---

### §8 Veritabanı (18 BCNF — ADR-040)

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

### §9 Projeler

> **Durum (2026-09-06):** `projects/` klasoru bos oldugu icin §9'daki isimli 20 referans icin STUB dosyalar olusturuldu (VERIFICATION REQUIRED; detay: [[projects/index]]). "EQ alt modülleri (7)" isimsizdir — doğrulanmadan stub üretilmez.

### §9.1 Neva Engine (C++ Audio)

17 modül: [[projects/NevaEngine/overview]], [[projects/NevaEngine/audio-core]], [[projects/NevaEngine/equalizer-system]], [[projects/NevaEngine/midi-system]], [[projects/NevaEngine/routing-matrix]], [[projects/NevaEngine/spatial-audio]], [[projects/NevaEngine/vst3-hosting]], [[projects/NevaEngine/ai-models]], [[projects/NevaEngine/neva-engine-integration]] + EQ alt modülleri (7)

---

### §9.2 Neva Player (Video/Media)

10 modül: [[projects/NevaPlayer/neva-player/overview]], [[projects/NevaPlayer/neva-player/codec-matrix]], [[projects/NevaPlayer/neva-player/ffmpeg-integration]], [[projects/NevaPlayer/neva-player/gpu-acceleration]], [[projects/NevaPlayer/neva-player/video-decoder]], [[projects/NevaPlayer/neva-player/webrtc-streaming]]

---

### §9.3 Diğer Projeler

[[projects/download-service]], [[projects/ipc-contracts]], [[projects/cpp-projects]], [[projects/WirelessConnect/proj-wireless-connect]], [[projects/NevaConnect/proj-neva-connect]]

---

### §10 Donanım & Elektronik

| Kategori | Dosya | İçerik |
|----------|-------|--------|
| **Index & Overview** | [[electronic/index]], [[electronic/core-music-electronics-overview]] | Master index, genel bakış |
| **Design & Roadmap** | [[electronic/hardware-roadmap]], [[electronic/amplifier-design]], [[electronic/audio-interface-design]], [[electronic/xmos-pcm3168a-design]], [[electronic/asio-driver-design]] | 3 faz roadmap, Class AB, XMOS+PCM3168A, ASIO |
| **Test & Measurement** | [[electronic/test-protocols]], [[electronic/frequency-response]], [[electronic/snr-thd-measurement]], [[electronic/thermal-analysis]] | Test protokolleri, SNR/THD, termal |
| **Modül Index'leri** | [[electronic/dsp/index]], [[electronic/drivers/index]], [[electronic/amplifier/index]], [[electronic/hardware/index]], [[electronic/firmware/index]] | DSP(7), Driver(7), Amp(5), HW(5), FW(4) modül |
| **Architecture** | [[electronic/platform-architecture]], [[electronic/device-architecture]], [[electronic/operating-system-architecture]], [[electronic/device-ecosystem]], [[electronic/software-architecture]], [[electronic/service-architecture]], [[electronic/audio-architecture]], [[electronic/dsp-engine-architecture]], [[electronic/driver-framework]], [[electronic/amplifier-architecture]], [[electronic/hardware-design]], [[electronic/firmware-architecture]] | 12 mimari doküman |
| **L6 & Cross-ref** | [[architecture/l6-electronics]], [[architecture/network-architecture]], [[architecture/database-architecture]], [[architecture/security-architecture]] | L6 katmanı, ağ/DB/güvenlik mimarisi |
| **AI & Contracts** | [[architecture/ai/ai-electronics-engine]], [[architecture/ai/ai-workflow-electronics]], [[architecture/03-contracts/development-workflow]], [[architecture/03-contracts/development-standards]], [[architecture/03-contracts/ai-workflow-standards]], [[architecture/03-contracts/diagram-collection]], [[architecture/07-security/electronics-security]], [[architecture/03-contracts/engineering-rules-ssot]], [[architecture/03-contracts/master-implementation-plan]] | AI, geliştirme standartları, master plan |

---

### §11A AI Architecture

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

### §15 Audio Organization

| Division | Sorumluluk |
|----------|------------|
| Hardware Division | Özel audio kartları, DAC/ADC, DSP çipleri, amplifikatör |
| Software Division | C++ Audio Engine, DSP Engine, Mixer, sürücüler |
| Studio Division | ASIO, WASAPI, kayıt, monitoring, routing |
| Consumer Division | Bluetooth, WiFi Audio, müzik oynatma, ev ve araç ses |
| Research Division | AI DSP, yeni codec teknolojileri, geleceğin audio donanımları |

---

## Rules

### §11B Skills (10 Skill — Guardrail #16 Zorunlu)

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

### §12 Vault Altyapısı

> **⚠️ Güncellik Notu (2026-09-06):** Aşağıda referans verilen `sessions/`, `registry/`, `scaffold/`, `knowledge/`, `confidence/`, `research/`, `personas/`, `workflows/`, `testing/`, `projects/` dizinleri vault ağacında mevcut DEĞİLDİR; bu bölümdeki ilgili wiki-linkler kırıktır. Düzeltme seçenekleri (yeniden kurma / referans temizliği) onay listesindedir.

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
| UI-Design | [[ui-design/01-mockup-index]], [[ui-design/02-component-inventory]], [[ui-design/03-implementation-plan]], [[ui-design/04-accessibility-gaps]], [[ui-design/04-vault-registration]] |
| UI-Design Screens | [[ui-design/screens/00-ascii-art-index]], [[ui-design/screens/A-auth/login]], [[ui-design/screens/B-home/dashboard]], [[ui-design/screens/C-music/albums]], [[ui-design/screens/D-player/playlist]], [[ui-design/screens/E-filemanager/disk-browser]], [[ui-design/screens/F-quickpanel/wifi]] |
| PNG Mockups | `.ai/.png/home-1024/` (12 PNG) + `.ai/.png/home-1920/` (1 PNG) + `.ai/.png/shared-1024/` (6 PNG) = 19 PNG |
| UI-Design Prompt | [[ui-design/prompt/00-prompt-index]], [[ui-design/prompt/screen/01-1024-embedded]], [[ui-design/prompt/component/C01-nav-link]], [[ui-design/prompt/layout/01-pattern-standard-60-40]], [[ui-design/prompt/page/01-home]] |
| UI-Design Reference | [[ui-design/tokens/design-tokens-master]], [[ui-design/reference/01-php-source-architecture]], [[ui-design/reference/02-text-strings]], [[ui-design/reference/03-icon-asset-catalog]], [[ui-design/reference/04-verification]] |
| UI-Design Flow | [[ui-design/flow/00-flow-index]], [[ui-design/flow/auth/04-select-gender]] |
| Research | [[research/verified/php84-strict-types]], [[research/verified/argon2id]], [[research/verified/aes-256-gcm]], [[research/verified/pcm3168a]], [[research/verified/asio-sdk]], [[research/verified/juce8]], [[research/verified/xmos-xu316]], [[research/verified/trusted-types-domparser]], [[research/verified/itcss-bemit-layer]], [[research/verified/wcag-22-aa]], [[research/verified/mariadb-1011]] |
| Personas | [[personas/index]], [[personas/methodology]], [[personas/mood-taxonomy]] |
| Templates | [[.templates/index]] — 2026-09-24 sayım: 26 dosya (24 şablon + index.md + CLAUDE.md); eski ad listesi: PHP, JS, CSS, C++, PHPUnit, Vitest, Migration, GitHub Actions, API-doc, Security-audit, ADR, Arduino, AVR, PIC, C, Node.js, ASP.NET, WikiPage, Query, Session) — ⚠️ VERIFICATION REQUIRED: Arduino/AVR/PIC diskte yok, liste sahip onayına açık |
| Workflows | [[../.workflows/adr-creation]], [[workflows/dev-workflow]], [[workflows/code-review]], [[../.workflows/deployment]], [[../.workflows/hallucination-control]], [[../.workflows/security-audit]], [[../.workflows/session-init]], [[workflows/vault-sync-detailed]] |
| Root | [[engine]], [[index-overview]], [[index-services]], [[index-adr]], [[.decisions/index]], [[research/index]] |

---

## Workflow

### §13 Deployment Modes

| Mod | Platform | Donanım |
|-----|----------|---------|
| Home Media Center | Windows/Linux/macOS | PC/Laptop |
| Car Audio System | Windows/Android Auto | Raspberry Pi 5 / PCM3168A |
| Professional Studio | Windows (WASAPI/ASIO) | 8.1 Surround + Class AB |
| NAS Audio Server | Linux | Synology/QNAP |
| DAC Control System | Windows/Linux | XMOS XU316 + PCM3168A |

---

### §14 Platform Tiers

| Tier | OS | Durum |
|------|-----|-------|
| Tier 1 (Primary) | Windows (XP–11, Server 2012 R2+) | ✅ Ana geliştirme |
| Tier 2 | Linux (Ubuntu, Debian, Fedora, Arch) | ✅ Destekli |
| Tier 3 | macOS (Monterey–Sonoma) | ✅ Destekli |
| Tier 4 | Raspberry Pi (ARM64, Debian) | ✅ Destekli |
| Tier 5 | ReactOS | ⚠️ Experimental |

---

### §20 Teknoloji Yığını Özeti (Dinamik Stack)

**İlke:** Programlama dili ve teknoloji yığını proje gereksinimlerine göre belirlenir — Node.js · C++ · C# · PHP + proje niteliğinin gerektirdiği diğerleri. Detay: [[engine.md]] §9, [[ROLE.md]] §11.

| Katman | Teknoloji | Durum | Referans |
|--------|-----------|-------|----------|
| PHP servis altyapısı | PHP 8.4 + PSR + php-di | IMPLEMENTED | 4 composer.json |
| Web panel frontend | Vanilla JS + ITCSS + BEM | PLANNED (kod) / IMPLEMENTED (spec) | ADR-001 |
| Audio/embedded | C++20, JUCE, XMOS xcc | PLANNED | electronic/, ADR-017/038 |
| I/O servisi | Node.js 20+ | PLANNED | ADR-026 |
| Windows araçları | C# / WDK | PLANNED | AGENTS.md #11 |
| Veri | MySQL şema (18 BCNF) | IMPLEMENTED (şema) | .ai/.sql/mysql/ |

---

## Validation

### §11 Test & Ekosistem

### §11.1 Test

> **Durum (Faz 0, 2026-09-08):** `.ai/testing/` dizini vault ağacında MEVCUT DEĞİLDİR — aşağıdaki referanslar tarihsel plan kaydıdır; kullanılabilir karşılıklar: `ui-design/03-accessibility-gaps.md` (WCAG denetimi), `.ai/reports/` (3 rapor), `ui-design/screens/` (ekran spec'leri).

| Dosya | Kapsam |
|-------|--------|
| [[testing/strategy]] | Test stratejisi *(dizin yok — plan kaydı)* |
| [[testing/coverage-targets]] | Kapsama hedefleri (≥80% min, ≥90% target) |
| [[testing/e2e-template]] | E2E test şablonu |
| [[testing/persona-test-protocol]] | Persona test protokolü |
| [[testing/test-plan]] | Test planı |
| [[testing/test-scenarios-mapping]] | Test senaryoları eşleme |

---

### §11.2 Ekosistem

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

### §16 Test Coverage Hedefleri

| Modül | Minimum | Hedef |
|-------|---------|-------|
| Backend (PHP) | ≥80% | ≥90% |
| Frontend (JS) | ≥80% | ≥90% |
| Audio Engine (C++) | ≥80% | ≥90% |
| Download Service | ≥80% | ≥90% |

---

### §19 Faz 1 Doğrulama Anlık Görüntüsü (2026-09-08)

### §19.1 Envanter Metrikleri (Faz 0 ölçümü)

| Metrik | Değer | Yöntem |
|--------|-------|--------|
| Vault MD | 741 dosya / 136.261 satır (boş-hariç) | Get-ChildItem + Measure-Object |
| Ortalama satır | 184/dosya | Toplam ÷ dosya |
| Hedef (Faz 1) | ≥500 satır/dosya | Kullanıcı direktifi |
| Kırık referans kümesi | 60+ (5 kategori) | Test-Path taraması |
| Sayım çelişkisi | 8 (hepsi bu revizyonda düzeltildi) | Cross-check |

---

### §19.2 Sayım Düzeltme Kaydı

| İddia | Eski | Yeni | Kanıt |
|--------|------|------|-------|
| PNG mockup | 18 | **19** (12+1+6) | .ai/.png/ sayımı |
| Vault dosya | 726 | **787** | stub'lar dahil sayım |
| ADR kapsamı | 001-087 | **001-088** (79) | decisions sayımı |
| Active ADR | 50 | **30** | §5.2 satır sayımı |
| Kök MD | 11 | **12** | glossary.md dahil |

---

### §19.3 Domain Uygulama Durumu (IMPLEMENTED/PLANNED)

| # | Domain | Teknoloji | Durum | Kanıt |
|---|--------|-----------|-------|-------|
| 1 | shared/ | PHP 8.4 | **IMPLEMENTED** | composer.json (v2.0.0) |
| 2 | packages/shared/ (ADR-085 ile shared/ altına alındı — fiziksel dizin yok) | PHP 8.3 | **FİZİKSEL DEĞİL** | Test-Path: kökte packages/ yok |
| 3 | auth.coremusic.net | PHP 8.4 | **IMPLEMENTED** | composer.json + include/ |
| 4 | home.coremusic.net | PHP 8.4 | **IMPLEMENTED** | composer.json + include/ |
| 5 | assets.coremusic.net | Statik | **IMPLEMENTED** | Css/Fonts/Image/js |
| 6-14 | api, music, admin, car, studio, pro, media, download, landing | PHP/Node.js/C++20 | PLANNED | Test-Path: dizin yok |

---

### §19.4 Kırık Referans Çözüm Durumu

| Küme | Hedef | Çözüm |
|------|-------|-------|
| Arşiv tarihi | prompt*-2026-08-13 (12 link, 5 dosya) | ✅ 2026-09-01'e hizalandı |
| .ai/testing/ | index.md §11, keys.md | ✅ "dizin yok" notu + gerçek karşılıklar |
| .ai/workflows/ | index.md §12 | ✅ uyarı notu mevcut; kök `.workflows/` gerçek konum |
| Electronic kök 8 dosya | keys.md §7, brain.md §21 | ✅ DOĞRULAMA GEREKLİ etiketi + alt klasör yönlendirme |
| models/issues/scripts index | CLAUDE.md §17 | Bekliyor — dizinler yok; oluşturma kararı kullanıcıda |

---

### §19.5 Boot Dosyası Revizyon Durumu

| Dosya | Satır (boş-hariç) | Durum |
|-------|-------------------|-------|
| engine.md | 503 | ✅ |
| glossary.md | ~500 | ✅ (v2.0.0 tam revizyon) |
| ROLE.md | 500 | ✅ (v6.0.0 tam revizyon) |
| ULTRA-THINKING.md | ~510 | ✅ (v2.0.0 tam revizyon) |
| MEMORY.md | ~490 | ✅ (v25.0.0) |
| CLAUDE.md | ~575 | ✅ (düzeltmeler) |
| brain.md | ~745 | ✅ (düzeltmeler) |
| keys.md | ~530 | ✅ (düzeltmeler) |
| WORKFLOW.md | ~590 | ✅ (düzeltmeler) |
| index.md | bu bölümle | ✅ |
| AGENTS.md | ~500 | ✅ (düzeltmeler + §25) |
| log.md | append-only | Faz kapanışında append (✅ 2026-09-08 kaydı düştü) |

---

### §19.6 Yöntem Notu

1. Hedef metrik "boş-hariç satır"tır (`Measure-Object -Line`) — Faz 0 envanteriyle aynı ölçüt.
2. Her genişletme satırı bilgi taşır: kanıt yolu, tablo, ASCII şema veya doğrulama kaydı; doldurma/fluff yasaktır.
3. Frozen ADR metinlerine ve arşiv dosyalarına dokunulmadı; yalnız referanslar doğrulandı.
4. **2026-09-24 taze ölçüm:** `.ai` recursive = 538 `.md` / 583 toplam dosya; `.ai/.templates` = 26 `.md` (`Get-ChildItem -Recurse -Force -Filter *.md`). §18'deki 518 / template 19 değerleri bu disk ölçümüyle düzeltildi (538 / 26 — ölçüm 2026-09-24); sahip yeniden doğrulaması gerekirse §18 satırı üzerinden yapılır.

---

### §21 Kırık Referans Kataloğu (Faz 0 taraması — çözüm durumu)

Bu bölüm, vault genelinde tespit edilen kırık referans kümelerini ve çözüm durumlarını izler. Kural: her çözüm `log.md`'ye kaydedilir; yenisi bulundukça buraya eklenir.

| # | Küme | Etkilenen Dosyalar | Çözüm Durumu |
|---|------|--------------------|--------------|
| 1 | `archives/prompt*-2026-08-13` (12 link) | brain.md §22, WORKFLOW §8.6, ROLE §10, MEMORY §5, keys §3B | ✅ 2026-09-01'e hizalandı |
| 2 | `.ai/testing/` dizini (6 dosya) | index.md §11.1, keys.md §11/§14, ROLE.md, ULTRA-THINKING §3.2 | ✅ "dizin yok" notu + gerçek karşılıklar |
| 3 | `.ai/workflows/` dizini (8 dosya) | index.md §12 | ⚠️ uyarı notu mevcut; gerçek konum kök `.workflows/` |
| 4 | Electronic kök 8 dosya | keys.md §7, brain.md §21, index.md §10 | ✅ DOĞRULAMA GEREKLİ + alt klasör yönlendirme |
| 5 | `electronic/frequency-response` ve `snr-thd` yanlış yol | keys.md §7 | ✅ `electronic/hardware/` altına yönlendirildi |
| 6 | `models/issues/scripts` index yok | CLAUDE.md §17 | ⚠️ Kullanıcı kararı bekliyor (oluştur / referans kaldır) |
| 7 | `subdomains/README` + download index | keys.md §8, index.md §12 | ⚠️ Oluşturma kararı bekliyor |
| 8 | `projects/NevaEngine/eq-dsp-chain` | keys.md §6 | ⚠️ stub kapsamı dışı — index.md §9 notuyla tutarlı |
| 9 | `research/`, `personas/`, `registry/`, `scaffold/`, `knowledge/`, `confidence/`, `sessions/` | index.md §12 | ⚠️ §12 güncellik notu mevcut; yeniden kurma kararı kullanıcıda |
| 10 | `ui-design/reference/02-design-tokens` | keys.md §3A | ✅ `ui-design/tokens/design-tokens-master.md` |
| 11 | l4/l5 flat vs index | (Obsidian fallback) | ✅ flat dosyalar mevcut — sorun değil |
| 12 | `[[reference/yaml-formatter]]` | WORKFLOW §8.8 | ⚠️ DOĞRULANAMADI — test edilmedi |

**Özet:** 6 çözüldü · 6 kullanıcı kararı bekliyor. Bekleyenler kod üretimi gerektirmez; doküman/dizin kararıdır.

---

### §21.1 Metodoloji

1. Referans çıkarma: boot + index dosyalarındaki `[[...]]` ve düz yol pattern'leri toplandı.
2. Doğrulama: her hedef Test-Path ile sınandı (PowerShell 5.1, Faz 0).
3. Kümeler: aynı kök nedenli kırıklar tek küme olarak gruplandı.
4. Çözüm: kanıtlı karşılık varsa yönlendirme; yoksa DOĞRULAMA GEREKLİ/kullanıcı kararı.
5. Bakım: bu katalog her faz kapanışında güncellenir — yeni kırık → yeni satır.

---

### §21.2 Kullanıcı Kararı Bekleyen Özet

| Karar | Seçenekler | Etki |
|-------|------------|------|
| models/issues/scripts index | Oluştur / referans kaldır | CLAUDE.md §17 boot iddiası |
| subdomains README + download index | Oluştur / keys.md satırını sil | §8 keyword yönlendirme |
| research/personas/registry vb. dizinler | Yeniden kur / §12'den sil | 30+ satır katalog temizliği |
| electronic kök 8 dosya | Yeniden üret / DOĞRULAMA GEREKLİ kalıcı | keys.md §7, brain §21 |

---

### §23 Doküman İskeleti (8-Bölüm Uyumu — Vault Refactor Engine 2026-09-23)

> **Not:** v28.0.0 → v28.1.0; eksik frontmatter alanları tamamlandı (category, status, updated). Satır-edit + ekleme (ADR-042), §1-§22 korundu.

### §23.1 İskelet Eşlemesi

| İskelet Bölümü | Karşılık Gelen § |
|----------------|------------------|
| Başlık | H1 + frontmatter (7 zorunlu alan — bu turda category/status/updated eklendi) |
| Amaç | §1 Amaç |
| Kapsam | §2 Quick Reference + §3 SSOT Core Dosyaları (14 Kök Boot + 1 Ek Doküman) |
| Mimari | §4-§4A (L0-L6 + UI Design) + §6-§10 (Panel/Servis/Agent/DB/Projeler/Donanım) + §11A AI Architecture + §15 Audio Org |
| Kurallar | §11B Skills (Guardrail #16) + §12 Vault Altyapısı + [[CLAUDE.md]] §16 |
| Workflow | §13-§14 (Deployment + Tiers) + §20 (stack özeti) |
| Doğrulama | §11 Test & Ekosistem + §16 Test Coverage + §19 Faz 1 Anlık Görüntü + §21 Kırık Referans Kataloğu + bu bölüm §23 |
| Referanslar | §5 ADR Kataloğu + §17 Cross References + §18 Metadata + §22 PDF |

---

### §23.2 Faz 3 Doğrulama (2026-09-23)

- [x] Frontmatter 7 alan TAMAMLANDI (category/status/updated eksikti); version 28.2.0
- [x] `total_files: 850` + `total_adr: 79` → **yeniden sayım Faz 6'ya ertelendi** (ADR aralığı CLAUDE'da 001-088 ile çelişiyor — SSOT conflict defterine işlendi)
- [x] §1-§22 korundu, silme yok; yeni bölüm §23 eklendi

---

### §23.3 İlgili Dosyalar

[[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[brain.md]] · [[keys.md]] · [[glossary.md]] · [[MEMORY.md]] · [[log.md]]

---

### §23.4 Faz 2-3 İskelet Yeniden Düzenleme (2026-09-23)

- [x] 7 İngilizce H2 iskeleti uygulandı: `## Purpose / ## Scope / ## Architecture / ## Rules / ## Workflow / ## Validation / ## References`
- [x] Eski numaralı H2 bölümleri `### §N` H3 başlığına dönüştürüldü; § numaraları harfiyen korundu (dış cross-ref'ler: §4A, §5, §8, §11B, §17)
- [x] İçerik taşındı, silinmedi; §23.1 eşlemesi güncel grubu gösterir
- [x] §4 bağımlılık satırında yasak yönler `✅` → `❌` düzeltildi (L0→L2/L3, L1→L3, L3→L0)
- [x] version 28.1.0 → 28.2.0; updated 2026-09-23; §18 Metadata versiyon satırı güncellendi

---

## References

### §5 Mimari Kararlar (ADR)

Toplam 79 ADR (Frozen: 37, Active: 30, Rejected: 12). Frozen: 001-037 (değiştirilemez). Active: 038-088 (güncellenebilir).

### §5.1 Frozen (001-037)

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

---

### §5.2 Active (038-088)

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
| [[decisions/accepted/ADR-085-modular-composer-packages]] | Shared Library Hybrid (tek shared/ + PSR-4 namespace) | Infrastructure |
| [[decisions/accepted/ADR-086-event-driven-architecture]] | Event Driven Architecture (PSR-14) | Architecture |
| [[decisions/accepted/ADR-087-master-implementation-plan]] | Master Implementation Plan (Sıfırdan Geliştirme Kapsamı) | Architecture |
| [[decisions/accepted/ADR-088-gender-based-social-oauth]] | Gender-Based Social OAuth | Social |

---

### §5.3 Reddedilen Kararlar

| Dosya | Kapsam |
|-------|--------|
| [[.decisions/rejected/index]] | Reddedilen ADR listesi ve gerekçeleri |

---

### §17 Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 3 SSOT | [[CLAUDE.md]] | Ana sözleşme |
| § 4 Mimari | [[architecture/k0-isletim-sistemi]] | L0-L6 katmanları |
| § 5 ADR | [[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]] | Vault standardı |
| § 6 Servisler | [[ecosystem/7-service-integration]] | Servis entegrasyonu |
| § 7 Agentlar | [[AGENTS.md]] | Agent yetkileri |
| § 8 DB | [[architecture/k0-k5-software/k5-data-layer/database_master]] | 18 BCNF şemaları |
| § 9 Projeler | [[projects/NevaEngine/overview]] | C++ ses motoru |
| § 10 Donanım | [[electronic/hardware-roadmap]] | 3 fazlı geliştirme |
| § 11 Test | [[testing/coverage-targets]] | Kapsama hedefleri |
| § 4A UI Design | [[ui-design/01-mockup-index]] | 19 PNG, C01-C16, Mockup SSOT |
| § 4A.1 Device-Aware | [[brain.md]] §18C | Backend/Frontend sorumluluk sınırları, Tek Bileşen İlkesi |

---

### §18 Metadata

- **Toplam dosya:** 538 (ölçüm 2026-09-24 — önceki sahip doğrulaması 518, 2026-09-23; eski Faz 0 değeri 787 güncel değildi)
- **Toplam ADR:** 79 (Frozen: 37, Active: 30, Rejected: 12)
- **Versiyon:** 28.2.0
- **Son Güncelleme:** 2026-09-24 (disk ölçümü: toplam dosya 538, PNG 19, template 26, kök boot 14 — ölçüm 2026-09-24; önceki sahip doğrulaması 518/19, 2026-09-23)
- **Governance:** Red Team · Human Mode · Truth Mode

---

### §22 PDF Dokümantasyon Yapısı (v1.0)

### §01 Giriş
- CoreMusic tanımı ve vizyonu
- 10 temel özellik
- 7 kullanım alanı
- 6 hedef kullanıcı grubu
- 6 sektörel çözüm

### §02 Sistem Mimarisi
- 4 katmanlı basitleştirilmiş mimari (L0-L3)
- 7 katmanlı detaylı mimari (K0-K6)
- Servis diyagramı
- Veri akışı

### İlgili Dosyalar
- [[VISION]] — CoreMusic vizyonu
- [[PROJECTS]] — Proje tanımı
- [[architecture/index]] — Mimari indeks

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-23
**Mode:** Red Team · Human Mode · Truth Mode
