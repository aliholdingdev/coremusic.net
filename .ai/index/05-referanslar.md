---
title: "Referanslar"
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
---# Referanslar## References

### §5 Mimari Kararlar (ADR)

Toplam 80 ADR (Frozen: 37, Active: 31, Rejected: 12). Frozen: 001-037 (değiştirilemez). Active: 038-089 (güncellenebilir).

### §5.1 Frozen (001-037)

| ADR | Konu | Kategori |
|-----|------|----------|
| [[.decisions/accepted/ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS, framework yasak | Frontend |
| [[.decisions/accepted/ADR-002-pdo-mandatory-no-orm]] | PDO mandatory, ORM yasak | Database |
| [[.decisions/accepted/ADR-003-multi-db-bcnf]] | 9 BCNF veritabanı | Database |
| [[.decisions/accepted/ADR-004-multi-domain-spa]] | Multi-domain SPA mimarisi | Architecture |
| [[.decisions/accepted/ADR-005-ultrathink-protocol]] | Zero hallucination protocol | Quality |
| [[.decisions/accepted/ADR-006-performance-targets]] | Performans hedefleri | Performance |
| [[.decisions/accepted/ADR-007-cache-namespace]] | Cache namespace standardı | Infrastructure |
| [[.decisions/accepted/ADR-008-bypass-auth-middleware]] | Auth bypass middleware | Security |
| [[.decisions/accepted/ADR-009-clean-url-redirect]] | Clean URL redirect | Routing |
| [[.decisions/accepted/ADR-010-csrf-protection-strategy]] | CSRF koruma stratejisi | Security |
| [[.decisions/accepted/ADR-011-session-management]] | Session yönetimi | Security |
| [[.decisions/accepted/ADR-012-csp-nonce-strict-dynamic]] | CSP nonce + strict-dynamic | Security |
| [[.decisions/accepted/ADR-013-rate-limiting-apcu]] | APCu rate limiting | Security |
| [[.decisions/accepted/ADR-014-multi-db-migration-strategy]] | Multi-DB migration | Database |
| [[.decisions/accepted/ADR-015-env-parser-strategy]] | Env parser stratejisi | Infrastructure |
| [[.decisions/accepted/ADR-016-url-normalization]] | URL normalization | Routing |
| [[.decisions/accepted/ADR-017-dsp-hardware-mode]] | DSP hardware mode (XMOS, JUCE) | Audio |
| [[.decisions/accepted/ADR-018-footer-player-vaporwave]] | Footer player vaporwave | UI |
| [[.decisions/accepted/ADR-019-per-os-neva-player]] | Per-OS Neva Player | Audio |
| [[.decisions/accepted/ADR-020-api-public-security]] | API public security | Security |
| [[.decisions/accepted/ADR-021-spa-router-immutable-contract]] | SPA router contract | Routing |
| [[.decisions/accepted/ADR-022-database-hardened-security]] | DB hardened security | Security |
| [[.decisions/accepted/ADR-023-persona-driven-testing]] | Persona-driven testing | Testing |
| [[.decisions/accepted/ADR-024-ecosystem-modular-docs]] | Ecosystem modular docs | Documentation |
| [[.decisions/accepted/ADR-025-professional-eq-system]] | Professional EQ system | Audio |
| [[.decisions/accepted/ADR-026-download-service-architecture]] | Download service arch | Architecture |
| [[.decisions/accepted/ADR-027-dual-mode-storage-strategy]] | Dual-mode storage | Infrastructure |
| [[.decisions/accepted/ADR-028-anti-ban-system]] | Anti-ban system | Download |
| [[.decisions/accepted/ADR-029-listening-rooms-social]] | Listening rooms social | Social |
| [[.decisions/accepted/ADR-030-ai-strategy-core]] | AI strategy core | AI |
| [[.decisions/accepted/ADR-031-mobile-strategy-pwa-flutter]] | Mobile strategy PWA/Flutter | Mobile |
| [[.decisions/accepted/ADR-032-ipc-contract-versioning]] | IPC contract versioning | Architecture |
| [[.decisions/accepted/ADR-033-sql-normalization-strategy]] | SQL normalization | Database |
| [[.decisions/accepted/ADR-034-credential-vault-normalization]] | Credential vault normalization | Security |
| [[.decisions/accepted/ADR-035-system-prompt-engineering]] | System prompt engineering | AI |
| [[.decisions/accepted/ADR-036-multi-project-prompt-maker]] | Multi-project prompt maker | AI |
| [[.decisions/accepted/ADR-037-wirelessconnect-integration]] | WirelessConnect integration | Integration |

---

### §5.2 Active (038-089)

| ADR | Konu | Kategori |
|-----|------|----------|
| [[brain.md]] ADR-038-8.1-sound-card-chip-selection | 8.1 ses donanımı (PCM3168A + XMOS XU316) | Audio |
| [[brain.md]] ADR-039-7-service-platform-architecture | 7-servis platform mimarisi | Architecture |
| [[brain.md]] ADR-040-database-authority | 18 BCNF DB otoritesi | Database |
| [[brain.md]] ADR-041-database-normalization-supplementary | DB normalizasyon ekı | Database |
| [[CLAUDE.md]] ADR-042-vault-restructuring-2026-08-03 | Vault yeniden yapılandırma | Vault |
| [[brain.md]] ADR-043-auth-subdomain-consolidation | Auth subdomain konsolidasyonu | Security |
| [[brain.md]] ADR-044-dynamic-user-theme-engine | Dynamic theme engine | UI |
| [[brain.md]] ADR-045-multi-domain-view-mode-architecture | Multi-domain view mode | UI |
| [[brain.md]] ADR-046-cross-view-state-preservation | Cross-view state koruma | UI |
| [[brain.md]] ADR-048-view-transition-api-integration | View Transition API entegrasyonu | UI |
| [[brain.md]] ADR-049-startup-prompt-loader | Startup prompt loader | AI |
| [[brain.md]] ADR-050-multi-db-sync-strategy | Multi-DB sync stratejisi | Database |
| [[brain.md]] ADR-061-electronics-architecture | Electronics Architecture (L6 Layer) | Electronics |
| [[brain.md]] ADR-062-dsp-pipeline-architecture | DSP Pipeline Architecture | Electronics |
| [[brain.md]] ADR-063-hardware-design-standards | Hardware Design Standards | Electronics |
| [[brain.md]] ADR-064-electronics-platform-architecture | Electronics Platform Architecture (L0-L6, 5 cihaz, 13 servis) | Electronics |
| [[brain.md]] ADR-072-social-database-schema | Social DB Schema (comments, shares, activity, rooms, notifications) | Database |
| [[brain.md]] ADR-073-podcast-database-schema | Podcast DB Schema (shows, episodes, subscriptions, transcripts) | Database |
| [[brain.md]] ADR-074-radio-database-schema | Radio DB Schema (stations, schedules, now_playing) | Database |
| [[brain.md]] ADR-075-ai-database-schema | AI DB Schema (preferences, features, recommendations, models) | Database |
| [[brain.md]] ADR-076-video-database-schema | Video DB Schema (music_videos, playback, subtitles) | Database |
| [[brain.md]] ADR-077-studio-database-schema | Studio DB Schema (sessions, tracks, presets, equipment) | Database |
| [[brain.md]] ADR-078-cms-database-schema | CMS DB Schema (pages, blog, tags, media, FAQs, banners) | Database |
| [[brain.md]] ADR-079-i18n-database-schema | i18n DB Schema (languages, translations, ui_strings, locale) | Database |
| [[brain.md]] ADR-083-spa-router | SPA Router Architecture (PHP+JS Hybrid) | Routing |
| [[brain.md]] ADR-084-api-gateway-architecture | API Gateway Architecture (API-First, BFF, CQRS) | Architecture |
| [[brain.md]] ADR-085-modular-composer-packages | Shared Library Hybrid (tek shared/ + PSR-4 namespace) | Infrastructure |
| [[brain.md]] ADR-086-event-driven-architecture | Event Driven Architecture (PSR-14) | Architecture |
| [[brain.md]] ADR-087-master-implementation-plan | Master Implementation Plan (Sıfırdan Geliştirme Kapsamı) | Architecture |
| [[brain.md]] ADR-088-gender-based-social-oauth | Gender-Based Social OAuth | Social |
| [[.decisions/accepted/ADR-089-classab-24v]] | Class AB Amplifikatör + 6S LiPo + ±35V Boost | Electronics |

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
| § 5 ADR | [[CLAUDE.md]] ADR-042-vault-restructuring-2026-08-03 | Vault standardı |
| § 6 Servisler | [[ecosystem/service-integration]] | Servis entegrasyonu *(yol düzeltildi 2026-10-07)* |
| § 7 Agentlar | [[AGENTS.md]] | Agent yetkileri |
| § 8 DB | [[.sql/mysql]] | 18 BCNF şemaları |
| § 9 Projeler | [[projects/NevaEngine/overview]] | C++ ses motoru |
| § 10 Donanım | [[electronic/hardware-roadmap]] | 3 fazlı geliştirme |
| § 11 Test | [[testing/coverage-targets]] | Kapsama hedefleri |
| § 4A UI Design | [[ui-design/01-mockup-index]] | 19 PNG, C01-C16, Mockup SSOT |
| § 4A.1 Device-Aware | [[brain.md]] §18C | Backend/Frontend sorumluluk sınırları, Tek Bileşen İlkesi |

---

### §24 Referans Linkleri (Dış Kaynak — 2026-10-01)

**awesome-opencode**
- https://github.com/NacioFelix/awesome-opencode
- https://github.com/weisser-dev/awesome-opencode
- https://github.com/jamait/awesome-opencode-skills

**agent skills**
- https://github.com/agentskills/agentskills
- https://github.com/j4flmao/agent-skills
- https://github.com/JazzaAI/agent-skills
- https://github.com/iannil/skills
- https://github.com/luckys/agent-skills
- https://github.com/tars-agentic/agent-skills

**dotnet**
- https://github.com/dotnet/skills
- https://github.com/DevExpress/agent-skills

**agent engineering / practices**
- https://github.com/DenisSergeevitch/agents-best-practices
- https://github.com/obra/superpowers

**frontend**
- https://github.com/vercel-labs/agent-skills
- https://github.com/lumpinif/frontend-ui-engineering
- https://github.com/hueyexe/frontend-agent-skills
- https://github.com/Junaid-PK/frontend-design-skill

**claude code**
- https://github.com/shanraisshan/claude-code-best-practice
- https://github.com/davila7/claude-code-templates
- https://github.com/ykdojo/claude-code-tips
- https://github.com/subinium/awesome-claude-code
- https://github.com/dazuiba/awesome-claude-code-1
- https://github.com/jqueryscript/awesome-claude-code
- https://github.com/itgoyo/awesome-claude-code

**mcp / agent platform**
- https://github.com/mctrinh/awesome-mcp-servers
- https://github.com/modelcontextprotocol/servers
- https://github.com/vakra-dev/awesome-ai-agents
- https://github.com/hammond01/CleanArchitecture

*Not:* harici kaynaklar vault'un (`.ai/`) altındadır; kural/çelişki durumunda SSOT kazanır. Kopya liste: kök `README.md` § "AI Agent & Skill Referansları".

### §18 Metadata

- **Toplam dosya:** 720 (.md — `.ai` recursive, 2026-09-30 Get-ChildItem sayımı; önceki kayıt 587 @ 2026-09-24 → **fark +133**, vault/envanter genişlemesi) · *tarihsel: 518 (2026-09-23 sahip doğrulaması), 787 (eski Faz 0 — güncel değil)*
- **Toplam ADR:** 80 karar iddiası (Frozen: 37, Active: 31, Rejected: 12) — **fiziksel ADR dosyası (disk): 60** (`Get-ChildItem .ai/.decisions -Recurse -Filter ADR-*.md`, 2026-09-30: `accepted/` 59 + kök 1 (`ADR-091-template-engine-no-eval.md`) + `draft/` 0 + `rejected/` 0) → **fark: 20 karar** (044-080 ve 082-088 arası) yalnızca `brain.md` metninde/karar appendix'inde — **VERIFICATION REQUIRED** (dosyalaştırılmadı); `total_adr: 80` = karar sayısı, `total_adr_disk: 60` = dosya sayısı — ikisi aynı şey değildir
- **Versiyon:** 28.4.3
- **Son Güncelleme:** 2026-09-29 (Faz 8b PNG envanter gerçeği §19.5 + §18 changelog; aynı gün önceki: Faz 7 guardrail hizalaması — ui-design disk gerçekliği §19.5, kırık ekran/prompt yolları düzeltildi, PNG kırılımı 12+1+6; önceki: 2026-09-27 disk ölçümü 587/PNG 19/template 36)
  - **28.4.3 — Faz 8b (2026-09-29):** PNG envanter gerçeği: 151 hedef · 136 indirilen · 15 gizli node visible:false → API NULL · 13 legacy · toplam 149; "149/151 = 2 eksik" iddiası geçersiz.
  - **28.4.2 — Faz 7 (2026-09-29):** §19.5 disk gerçekliği (kök 6 · screens 21 · flow 21 · prompt 51 · reference 17 · tokens 4+7 · PNG 19 · figma png 151 hedef / 136 indirilen · raw 19 JSON/79.7 MB) eklendi; §4A/§11'de olmayan ekran ve prompt yolları diskteki gerçek adlarla değiştirildi (B-home, E-filemanager, F-quickpanel, 04-vault-registration, 01-1024-embedded, 01-pattern-standard-60-40 → mevcut dosyalar); mockup-index kırılımı 12+1+6'ya tamamlandı.
  - **28.4.0 — Faz 2 (2026-09-27):** .personas 68/68 persona yeniden yazıldı (35.026 satır, ≥500 oranı %100), 4 dosya yeniden adlandırıldı, kırık wiki-link 7→0, personas/index.md sayaçları disk gerçeğiyle hizalandı, templates registry #38.
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
**Last Updated:** 2026-10-06
**Mode:** Red Team · Human Mode · Truth Mode
