---
type: index
category: decisions
title: "CoreMusic — Decisions Index"
date: 2026-08-15
updated: 2026-09-24
status: active
version: 1.1.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
total-accepted: 68
total-rejected: 12
total-frozen: 37
total-active: 31
total-draft: 0
---

# CoreMusic — Decisions Index

## 1. Amaç

Tüm Architecture Decision Records (ADR) indeksini sunan, durumlarını ve kategorilerini kataloglayan **ana navigasyon dosyası**dır. **Bağlam** da adr ler bauarda toplanır okunur yazılır.

## 2. Genel Bakış

| Durum | Sayı | Açıklama |
|-------|------|----------|
| **Frozen** | 36 | Değiştirilemez (ADR-001 → ADR-036; ADR-037 debate ✅, frozen YOK) |
| **Active** | 31 | Güncellenebilir (ADR-038 → ADR-089) |
| **Rejected** | 12 | Reddedilen kararlar |
| **Draft** | 0 | Taslak yok (ADR-089 kabule terfi etti, 2026-09-24) |
| **Toplam** | 80 | — |

## 3. Frozen ADR'ler (001-037)

| ADR | Başlık | Kategori |
|-----|--------|----------|
| [[ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS, Framework Yasak | Frontend |
| [[ADR-002-pdo-mandatory-no-orm]] | PDO Mandatory, ORM Yasak | Database |
| [[ADR-003-multi-db-bcnf]] | Multi-DB 9 BCNF Veritabanı | Database |
| [[ADR-004-multi-domain-spa]] | Multi-Domain SPA Architecture | Architecture |
| [[ADR-005-ultrathink-protocol]] | Ultrathink Protocol (Zero Hallucination) | Architecture |
| [[ADR-006-performance-targets]] | Performance Targets | Architecture |
| [[ADR-007-cache-namespace]] | Cache Namespace Standard | Architecture |
| [[ADR-008-bypass-auth-middleware]] | Bypass Auth Middleware | Security |
| [[ADR-009-clean-url-redirect]] | Clean URL Redirect | Routing |
| [[ADR-010-csrf-protection-strategy]] | CSRF Protection Strategy | Security |
| [[ADR-011-session-management]] | Session Management | Security |
| [[ADR-012-csp-nonce-strict-dynamic]] | CSP Nonce Strict-Dynamic | Security |
| [[ADR-013-rate-limiting-apcu]] | Rate Limiting APCu | Security |
| [[ADR-014-multi-db-migration-strategy]] | Multi-DB Migration Strategy | Database |
| [[ADR-015-env-parser-strategy]] | Env Parser Strategy | Infrastructure |
| [[ADR-016-url-normalization]] | URL Normalization | Routing |
| [[ADR-017-dsp-hardware-mode]] | DSP Hardware Mode (XMOS, JUCE, ASIO) | Audio |
| [[ADR-018-footer-player-vaporwave]] | Footer Player Vaporwave | Frontend |
| [[ADR-019-per-os-neva-player]] | Per-OS Neva Player | Audio |
| [[ADR-020-api-public-security]] | API Public Security | Security |
| [[ADR-021-spa-router-immutable-contract]] | SPA Router Immutable Contract | Routing |
| [[ADR-022-database-hardened-security]] | Database Hardened Security | Security |
| [[ADR-023-persona-driven-testing]] | Persona-Driven Testing | Testing |
| [[ADR-024-ecosystem-modular-docs]] | Ecosystem Modular Docs | Documentation |
| [[ADR-025-professional-eq-system]] | Professional EQ System (31-band) | Audio |
| [[ADR-026-download-service-architecture]] | Download Service Architecture | Architecture |
| [[ADR-027-dual-mode-storage-strategy]] | Dual-Mode Storage Strategy | Infrastructure |
| [[ADR-028-anti-ban-system]] | Anti-Ban System | Download |
| [[ADR-029-listening-rooms-social]] | Listening Rooms Social | Social |
| [[ADR-030-ai-strategy-core]] | AI Strategy Core | AI |
| [[ADR-031-mobile-strategy-pwa-flutter]] | Mobile Strategy PWA/Flutter | Mobile |
| [[ADR-032-ipc-contract-versioning]] | IPC Contract Versioning | Architecture |
| [[ADR-033-sql-normalization-strategy]] | SQL Normalization Strategy | Database |
| [[ADR-034-credential-vault-normalization]] | Credential Vault Normalization | Security |
| [[ADR-035-system-prompt-engineering]] | System Prompt Engineering | AI |
| [[ADR-036-multi-project-prompt-maker]] | Multi-Project Prompt Maker | AI |
| [[ADR-037-wirelessconnect-integration]] | WirelessConnect Integration | Audio |

## 4. Active ADR'ler (038-089)

| ADR | Başlık | Kategori |
|-----|--------|----------|
| [[accepted/ADR-038-8-1-sound-card-chip-selection]] | 8.1 Sound Card (PCM3168A + XMOS) | Audio |
| [[../brain.md]] ADR-039-7-service-platform-architecture | 7-Service Platform Architecture | Architecture |
| [[accepted/ADR-040-database-authority]] | Database Authority (18 BCNF) | Database |
| [[accepted/ADR-041-database-normalization-supplementary]] | DB Normalization Supplementary | Database |
| [[../CLAUDE.md]] ADR-042-vault-restructuring-2026-08-03 | Vault Restructuring | Vault |
| [[../brain.md]] ADR-043-auth-subdomain-consolidation | Auth Subdomain Consolidation | Security |
| [[../brain.md]] ADR-044-dynamic-user-theme-engine | Dynamic User Theme Engine | Frontend |
| [[../brain.md]] ADR-045-multi-domain-view-mode-architecture | Multi-Domain View Mode | Frontend |
| [[../brain.md]] ADR-046-cross-view-state-preservation | Cross-View State Preservation | Frontend |
| [[../brain.md]] ADR-048-view-transition-api-integration | View Transition API | Frontend |
| [[../brain.md]] ADR-049-startup-prompt-loader | Startup Prompt Loader | AI |
| [[../brain.md]] ADR-050-multi-db-sync-strategy | Multi-DB Sync Strategy | Database |
| [[../brain.md]] ADR-061-electronics-architecture | Electronics Architecture (L6) | Electronics |
| [[../brain.md]] ADR-062-dsp-pipeline-architecture | DSP Pipeline Architecture | Electronics |
| [[../brain.md]] ADR-063-hardware-design-standards | Hardware Design Standards | Electronics |
| [[../brain.md]] ADR-064-electronics-platform-architecture | Electronics Platform Architecture | Electronics |
| [[../brain.md]] ADR-072-social-database-schema | Social DB Schema | Database |
| [[../brain.md]] ADR-073-podcast-database-schema | Podcast DB Schema | Database |
| [[../brain.md]] ADR-074-radio-database-schema | Radio DB Schema | Database |
| [[../brain.md]] ADR-075-ai-database-schema | AI DB Schema | Database |
| [[../brain.md]] ADR-076-video-database-schema | Video DB Schema | Database |
| [[../brain.md]] ADR-077-studio-database-schema | Studio DB Schema | Database |
| [[../brain.md]] ADR-078-cms-database-schema | CMS DB Schema | Database |
| [[../brain.md]] ADR-079-i18n-database-schema | i18n DB Schema | Database |
| [[ADR-081-multi-provider-data-sync]] | Multi-Provider Data Sync (Outbox+WAL) | Database |
| [[../brain.md]] ADR-083-spa-router | SPA Router Architecture | Architecture |
| [[../brain.md]] ADR-084-api-gateway-architecture | API Gateway Architecture | Architecture |
| [[../brain.md]] ADR-085-modular-composer-packages | Shared Library Hybrid (tek shared/ + PSR-4 namespace) | Architecture |
| [[../brain.md]] ADR-086-event-driven-architecture | Event Driven Architecture | Architecture |
| [[../brain.md]] ADR-087-master-implementation-plan | Master Implementation Plan | Architecture |
| [[../brain.md]] ADR-088-gender-based-social-oauth | Gender-Based Social OAuth | Social |
| [[accepted/ADR-089-classab-24v]] | Class AB Amplifikatör + 6S LiPo + ±35V Boost | Electronics |

## 4A. Draft ADR'ler

| ADR | Başlık | Kategori |
|-----|--------|----------|
| — | Taslak yok — ADR-089 kabule terfi etti (2026-09-24, §4) | — |

## 5. Reddedilen ADR'ler

| ADR | Başlık | Red Nedeni |
|-----|--------|------------|
| [[R-001-redux-style-state-management]] <!-- dead-link: R-001-redux-style-state-management no source 2026-09-24 --> | Redux-Style State | Framework yasağı |
| [[R-002-mongodb-document-store]] <!-- dead-link: R-002-mongodb-document-store no source 2026-09-24 --> | MongoDB | BCNF uyumsuz |
| [[R-003-jquery-ui-framework]] <!-- dead-link: R-003-jquery-ui-framework no source 2026-09-24 --> | jQuery | Framework yasağı |
| [[R-004-webpack-bundle-system]] <!-- dead-link: R-004-webpack-bundle-system no source 2026-09-24 --> | Webpack | Over-engineering |
| [[R-005-rest-only-api]] <!-- dead-link: R-005-rest-only-api no source 2026-09-24 --> | REST-Only | WebSocket gerekli |
| [[R-006-laravel-eloquent-orm]] <!-- dead-link: R-006-laravel-eloquent-orm no source 2026-09-24 --> | Eloquent ORM | ORM yasak |
| [[R-007-firebase-authentication]] <!-- dead-link: R-007-firebase-authentication no source 2026-09-24 --> | Firebase Auth | Harici bağımlılık |
| [[R-008-mysql-myisam-engine]] <!-- dead-link: R-008-mysql-myisam-engine no source 2026-09-24 --> | MyISAM | Transaction eksik |
| [[R-009-single-database-architecture]] <!-- dead-link: R-009-single-database-architecture no source 2026-09-24 --> | Single DB | Güvenlik/performans |
| [[R-010-nodejs-backend-fullstack]] <!-- dead-link: R-010-nodejs-backend-fullstack no source 2026-09-24 --> | Node.js Full Stack | PHP zorunlu |
| [[R-011-graphql-api]] <!-- dead-link: R-011-graphql-api no source 2026-09-24 --> | GraphQL | Over-engineering |
| [[R-012-microservices-architecture]] <!-- dead-link: R-012-microservices-architecture no source 2026-09-24 --> | Microservices | Erken optimizasyon |

## 6. Kategori Haritası

| Kategori | Frozen | Active | Toplam |
|----------|--------|--------|--------|
| Security | 8 | 1 | 9 |
| Database | 4 | 11 | 15 |
| Architecture | 5 | 7 | 12 |
| Frontend | 2 | 4 | 6 |
| Audio | 5 | 4 | 9 |
| Routing | 3 | 0 | 3 |
| Infrastructure | 2 | 0 | 2 |
| AI | 3 | 1 | 4 |
| Testing | 1 | 0 | 1 |
| Documentation | 1 | 0 | 1 |
| Download | 1 | 0 | 1 |
| Social | 1 | 1 | 2 |
| Mobile | 1 | 0 | 1 |
| Vault | 0 | 1 | 1 |
| Electronics | 0 | 5 | 5 |
| **TOPLAM** | **37** | **31** | **68** |

---

*Decisions Index v1.1.1 — CoreMusic Vault*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-24*
*Mode: Red Team · Human Mode · Truth Mode*
