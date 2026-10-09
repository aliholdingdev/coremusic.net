---
title: "Mimari"
type: system
category: vault-navigation
status: active
authority: SSOT
version: 28.4.4
updated: 2026-10-06
total_files: 720
total_adr: 80
total_adr_disk: 60
# Control Plane v2 (Q7 geniş şema — 2026-10-07):
tier: 5
domain: navigation
ssot: false
risk: low
owner: "MO"
depends-on: []
---# Mimari## Architecture

### §4 Mimari — L0-L6 Katmanları

*Detaylı metadata için bakınız: [[architecture/index]] §2*

Bağımlılık kuralları: ✅ L6→L5, L5→L4, L4→L3, L3→L2, L2→L1, L1→L0 | ❌ L0→L2/L3, L1→L3, L3→L0

| Katman | Dosya | Kapsam |
|--------|-------|--------|
| L6 Electronics | [[architecture/k1-donanim]] · [[architecture/firmware]] | Hardware, firmware, driver, DSP, audio engine |
| L5 Services | [[architecture/k8-servis]] | Application services, use cases, CQRS, event bus |
| L4 Domain | [[architecture/k10-uygulama]] | Business rules, entities, value objects, aggregates |
| L3 Presentation | [[architecture/k11-ux]] · [[ui-design/01-mockup-index]] | Frontend, UI, DOM, responsive, 19 PNG mockup, C01-C16 |
| L3 Rehber | — *(eski hedef `l3-presentation/scale-router-css-frontend-guide` kaldırıldı 2026-10-07 — rehber dosyası yok; eşdeğer: [[ui-design/05-responsive-architecture]] + [[architecture/k11-ux]])* | Scale, Router CSS rehberi (kapsam boşluğu — içerik Faz 1+) |
| L2 Routing | [[architecture/k9-api-routing]] | SPA PageRouter, API Gateway, subdomain routing |
| L1 Security | [[architecture/k6-guvenlik]] | Middleware pipeline, session, auth, CSRF, CSP |
| L0 Infrastructure | [[architecture/k0-isletim-sistemi]] | Database, cache, filesystem, IPC, credential vault |

---

### §4A UI Design System & Mockup Otoritesi (Kanonik SSOT)

**Tüm frontend (HTML/CSS/JS/PHP layout) geliştirme görevlerinin tartışmasız başlangıç noktası ve Tek Doğruluk Kaynağıdır (SSOT).**

| Dosya / Dizin | İçerik ve Amaç | Zorunluluk |
|---------------|----------------|------------|
| [[ui-design/01-mockup-index]] | 19 PNG Mockup İndeksi (12 home-1024 + 1 home-1920 + 6 shared-1024) | ✅ Tüm frontend görevlerinde İLK OKUNACAK |
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
| [[.templates/frontend/css-device-template]] | Device/Cihaz CSS behavioral override şablonu (eski 7-CSS envanteri 2026-10-07'de kaldırıldı) | Behavioral overrides |
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

[[architecture/k8-servis/download-service]], [[projects/ipc-contracts]], [[projects/cpp-projects]], [[projects/WirelessConnect/proj-wireless-connect]], [[projects/NevaConnect/proj-neva-connect]]

---

### §10 Donanım & Elektronik

| Kategori | Dosya | İçerik |
|----------|-------|--------|
| **Index & Overview** | [[electronic/index]], [[electronic/core-music-electronics-overview]] | Master index, genel bakış |
| **Design & Roadmap** | [[electronic/hardware-roadmap]], [[electronic/amplifier-design]], [[electronic/audio-interface-design]], [[electronic/xmos-pcm3168a-design]], [[electronic/asio-driver-design]] | 3 faz roadmap, Class AB, XMOS+PCM3168A, ASIO |
| **Test & Measurement** | [[electronic/test-protocols]], [[electronic/frequency-response]], [[electronic/snr-thd-measurement]], [[electronic/thermal-analysis]] | Test protokolleri, SNR/THD, termal |
| **Modül Index'leri** | [[electronic/dsp/index]], [[electronic/drivers/index]], [[electronic/amplifier/index]], [[electronic/hardware/index]], [[electronic/firmware/index]] | DSP(7), Driver(7), Amp(5), HW(5), FW(4) modül |
| **Architecture** | [[electronic/platform-architecture]], [[electronic/device-architecture]], [[electronic/operating-system-architecture]], [[electronic/device-ecosystem]], [[electronic/software-architecture]], [[electronic/service-architecture]], [[electronic/audio-architecture]], [[electronic/dsp-engine-architecture]], [[electronic/driver-framework]], [[electronic/amplifier-architecture]], [[electronic/hardware-design]], [[electronic/firmware-architecture]] | 12 mimari doküman |
| **L6 & Cross-ref** | [[architecture/k1-donanim]], [[architecture/network-architecture]], [[architecture/database-architecture]], [[architecture/security-architecture]] | L6 katmanı, ağ/DB/güvenlik mimarisi |
| **AI & Contracts** | [[architecture/ai/ai-electronics-engine]], [[architecture/ai/ai-workflow-electronics]], [[architecture/03-contracts/development-workflow]], [[architecture/03-contracts/development-standards]], [[architecture/03-contracts/ai-workflow-standards]], [[architecture/03-contracts/diagram-collection]], [[architecture/03-contracts/engineering-rules-ssot]], [[architecture/03-contracts/master-implementation-plan]] | AI, geliştirme standartları, master plan |

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

