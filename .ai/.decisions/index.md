---
type: index
category: decisions
title: "CoreMusic — Decisions Index"
date: 2026-08-15
updated: 2026-10-06
status: active
version: 1.1.5
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
total-accepted: 72
total-rejected: 7
total-frozen: 36
total-active: 36
total-draft: 0
# Control Plane v2 (2026-10-07):
tier: 3
domain: decisions
ssot: true
risk: high
owner: "MO"
depends-on: [".ai/CLAUDE.md"]
---

# CoreMusic — Decisions Index

## 1. Amaç

Tüm Architecture Decision Records (ADR) indeksini sunan, durumlarını ve kategorilerini kataloglayan **ana navigasyon dosyası**dır. **Bağlam** da adr ler bauarda toplanır okunur yazılır.

## 2. Genel Bakış

| Durum | Sayı | Açıklama |
|-------|------|----------|
| **Frozen** | 36 | Değiştirilemez (ADR-001 → ADR-036; ADR-037 debate ✅, frozen YOK) |
| **Active** | 38 | Güncellenebilir (ADR-038 → ADR-095) |
| **Rejected** | 7 | Reddedilen kararlar |
| **Draft** | 0 | Taslak yok (ADR-089 kabule terfi etti, 2026-09-24) |
| **Toplam** | 81 | — |

> Not (2026-10-06 LINT-3): §3 başlığı ve §6 eski hali ADR-037'yi frozen sayıyordu (37/35); §2'deki "ADR-037 debate ✅, frozen YOK" kaydı esas alındı → 36/36. Çelişki Vault Steward onayına açıktır.

## 3. Frozen ADR'ler (001-036)

| ADR | Başlık | Kategori |
|-----|--------|----------|
| [[accepted/ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS, Framework Yasak | Frontend |
| [[accepted/ADR-002-pdo-mandatory-no-orm]] | PDO Mandatory, ORM Yasak | Database |
| [[accepted/ADR-003-multi-db-bcnf]] | Multi-DB 9 BCNF Veritabanı | Database |
| [[accepted/ADR-004-multi-domain-spa]] | Multi-Domain SPA Architecture | Architecture |
| [[accepted/ADR-005-ultrathink-protocol]] | Ultrathink Protocol (Zero Hallucination) | Architecture |
| [[accepted/ADR-006-performance-targets]] | Performance Targets | Architecture |
| [[accepted/ADR-007-cache-namespace]] | Cache Namespace Standard | Architecture |
| [[accepted/ADR-008-bypass-auth-middleware]] | Bypass Auth Middleware | Security |
| [[accepted/ADR-009-clean-url-redirect]] | Clean URL Redirect | Routing |
| [[accepted/ADR-010-csrf-protection-strategy]] | CSRF Protection Strategy | Security |
| [[accepted/ADR-011-session-management]] | Session Management | Security |
| [[accepted/ADR-012-csp-nonce-strict-dynamic]] | CSP Nonce Strict-Dynamic | Security |
| [[accepted/ADR-013-rate-limiting-apcu]] | Rate Limiting APCu | Security |
| [[accepted/ADR-014-multi-db-migration-strategy]] | Multi-DB Migration Strategy | Database |
| [[accepted/ADR-015-env-parser-strategy]] | Env Parser Strategy | Infrastructure |
| [[accepted/ADR-016-url-normalization]] | URL Normalization | Routing |
| [[accepted/ADR-017-dsp-hardware-mode]] | DSP Hardware Mode (XMOS, JUCE, ASIO) | Audio |
| [[accepted/ADR-018-footer-player-vaporwave]] | Footer Player Vaporwave | Frontend |
| [[accepted/ADR-019-per-os-neva-player]] | Per-OS Neva Player | Audio |
| [[accepted/ADR-020-api-public-security]] | API Public Security | Security |
| [[accepted/ADR-021-spa-router-immutable-contract]] | SPA Router Immutable Contract | Routing |
| [[accepted/ADR-022-database-hardened-security]] | Database Hardened Security | Security |
| [[accepted/ADR-023-persona-driven-testing]] | Persona-Driven Testing | Testing |
| [[accepted/ADR-024-ecosystem-modular-docs]] | Ecosystem Modular Docs | Documentation |
| [[accepted/ADR-025-professional-eq-system]] | Professional EQ System (31-band) | Audio |
| [[accepted/ADR-026-download-service-architecture]] | Download Service Architecture | Architecture |
| [[accepted/ADR-027-dual-mode-storage-strategy]] | Dual-Mode Storage Strategy | Infrastructure |
| [[accepted/ADR-028-anti-ban-system]] | Anti-Ban System | Download |
| [[accepted/ADR-029-listening-rooms-social]] | Listening Rooms Social | Social |
| [[accepted/ADR-030-ai-strategy-core]] | AI Strategy Core | AI |
| [[accepted/ADR-031-mobile-strategy-pwa-flutter]] | Mobile Strategy PWA/Flutter | Mobile |
| [[accepted/ADR-032-ipc-contract-versioning]] | IPC Contract Versioning | Architecture |
| [[accepted/ADR-033-sql-normalization-strategy]] | SQL Normalization Strategy | Database |
| [[accepted/ADR-034-credential-vault-normalization]] | Credential Vault Normalization | Security |
| [[accepted/ADR-035-system-prompt-engineering]] | System Prompt Engineering | AI |
| [[accepted/ADR-036-multi-project-prompt-maker]] | Multi-Project Prompt Maker | AI |

## 4. Active ADR'ler (037-093 — diskte dosyası olanlar)

| ADR | Başlık | Kategori |
|-----|--------|----------|
| [[accepted/ADR-037-wirelessconnect-integration]] | WirelessConnect Integration | Audio |
| [[accepted/ADR-038-8-1-sound-card-chip-selection]] | 8.1 Sound Card (PCM3168A + XMOS) | Audio |
| [[accepted/ADR-039-7-service-platform-architecture]] | 7-Service Platform Architecture | Architecture |
| [[accepted/ADR-040-database-authority]] | Database Authority (18 BCNF) | Database |
| [[accepted/ADR-041-database-normalization-supplementary]] | DB Normalization Supplementary | Database |
| [[accepted/ADR-042-vault-restructuring-2026-08-03]] | Vault Restructuring | Vault |
| [[accepted/ADR-043-auth-subdomain-consolidation]] | Auth Subdomain Consolidation | Security |
| [[accepted/ADR-044-dynamic-user-theme-engine]] | Dynamic User Theme Engine | Frontend |
| [[accepted/ADR-045-multi-domain-view-mode-architecture]] | Multi-Domain View Mode | Frontend |
| [[accepted/ADR-046-cross-view-state-preservation]] | Cross-View State Preservation | Frontend |
| [[accepted/ADR-047-login-redirect-session-bridge]] | Login Redirect & Session Bridge | Security |
| [[accepted/ADR-048-view-transition-api-integration]] | View Transition API | Frontend |
| [[accepted/ADR-049-startup-prompt-loader]] | Startup Prompt Loader | AI |
| [[accepted/ADR-050-multi-db-sync-strategy]] | Multi-DB Sync Strategy | Database |
| [[accepted/ADR-052-hybrid-auth-session-jwt]] | Hybrid Auth Session + JWT | Security |
| [[accepted/ADR-056-auth-module-implementation]] | Auth Modülü Uygulaması (RBAC + Permission middleware) | Security |
| [[accepted/ADR-058-centralized-auth-service]] | Merkezi Auth Servisi | Security |
| [[accepted/ADR-059-jwt-library-and-mfa]] | JWT Kütüphanesi ve MFA TOTP | Security |
| [[accepted/ADR-061-electronics-architecture]] | Electronics Architecture (L6) | Electronics |
| [[accepted/ADR-062-dsp-pipeline-architecture]] | DSP Pipeline Architecture | Electronics |
| [[accepted/ADR-063-hardware-design-standards]] | Hardware Design Standards | Electronics |
| [[accepted/ADR-064-electronics-platform-architecture]] | Electronics Platform Architecture | Electronics |
| [[accepted/ADR-072-social-database-schema]] | Social DB Schema | Database |
| [[accepted/ADR-073-podcast-database-schema]] | Podcast DB Schema | Database |
| [[accepted/ADR-074-radio-database-schema]] | Radio DB Schema | Database |
| [[accepted/ADR-075-ai-database-schema]] | AI DB Schema | Database |
| [[accepted/ADR-076-video-database-schema]] | Video DB Schema | Database |
| [[accepted/ADR-077-studio-database-schema]] | Studio DB Schema | Database |
| [[accepted/ADR-078-cms-database-schema]] | CMS DB Schema | Database |
| [[accepted/ADR-079-i18n-database-schema]] | i18n DB Schema | Database |
| [[accepted/ADR-081-multi-provider-data-sync]] | Multi-Provider Data Sync (Outbox+WAL) | Database |
| [[accepted/ADR-082-dev-environment]] | Dev/Staging Environment Architecture | Infrastructure |
| ADR-083-spa-router | SPA Router Architecture | Architecture <!-- NO FILE on disk 2026-10-06 — brain.md only --> |
| ADR-084-api-gateway-architecture | API Gateway Architecture | Architecture <!-- NO FILE on disk 2026-10-06 — brain.md only --> |
| ADR-085-modular-composer-packages | Shared Library Hybrid (tek shared/ + PSR-4 namespace) | Architecture <!-- NO FILE on disk 2026-10-06 — brain.md only --> |
| ADR-086-event-driven-architecture | Event Driven Architecture | Architecture <!-- NO FILE on disk 2026-10-06 — brain.md only --> |
| ADR-087-master-implementation-plan | Master Implementation Plan | Architecture <!-- NO FILE on disk 2026-10-06 — brain.md only --> |
| ADR-088-gender-based-social-oauth | Gender-Based Social OAuth | Social <!-- NO FILE on disk 2026-10-06 — brain.md only --> |
| [[accepted/ADR-089-classab-24v]] | Class AB Amplifikatör + 6S LiPo + ±35V Boost | Electronics |
| [[accepted/ADR-090-channel-variant-product-family]] | Kanal Varyant Ürün Ailesi (mono → 8+1 SKU) | Electronics |
| ADR-091-template-engine-no-eval | TemplateEngine eval() Kaldırımı (Guardrail #21) | Security <!-- NO FILE on disk 2026-10-06 — brain.md only --> |
| [[accepted/ADR-092-media-dizin-ekseni-ve-ulid]] | Medya Arşivi Dizin Ekseni ve ULID Kimliği | Infrastructure |
| [[accepted/ADR-093-view-modes-single-load-path]] | 09_ViewModes v-*.css için tek yükleme yolu: <link id="cm-view-css"> kanoniktir, cihaz @import zinciri deferred | Frontend |
| [[accepted/ADR-094-api-pipeline-origin-csrf]] | API pipeline'ına OriginCheck + koşullu CSRF eklendi (ADR-020 sıra genişletmesi; B-F-02/B-F-03) | Security |
| [[accepted/ADR-095-hybrid-jwt-rs256-access-token]] | Hybrid JWT (RS256): issue/validate/revocation — firebase/php-jwt, jti→user_tokens, 0 migration | Security |

## 4A. Draft ADR'ler

| ADR | Başlık | Kategori |
|-----|--------|----------|
| — | Taslak yok — ADR-089 kabule terfi etti (2026-09-24, §4) | — |

## 5. Reddedilen ADR'ler

| ADR | Başlık | Red Nedeni |
|-----|--------|------------|
| [[rejected/R-001-redux-style-state-management]] | Redux-Style State | Framework yasağı |
| [[rejected/R-002-mongodb-document-store]] | MongoDB | BCNF uyumsuz |
| [[rejected/R-003-jquery-ui-framework]] | jQuery | Framework yasağı |
| [[rejected/R-004-webpack-bundle-system]] | Webpack | Over-engineering |
| [[rejected/R-005-rest-only-api]] | REST-Only | WebSocket gerekli |
| [[rejected/R-006-laravel-eloquent-orm]] | Eloquent ORM | ORM yasak |
| [[rejected/R-007-firebase-authentication]] | Firebase Auth | Harici bağımlılık |
| R-008-mysql-myisam-engine | MyISAM | Transaction eksik <!-- NO FILE on disk 2026-10-06 --> |
| R-009-single-database-architecture | Single DB | Güvenlik/performans <!-- NO FILE on disk 2026-10-06 --> |
| R-010-nodejs-backend-fullstack | Node.js Full Stack | PHP zorunlu <!-- NO FILE on disk 2026-10-06 --> |
| R-011-graphql-api | GraphQL | Over-engineering <!-- NO FILE on disk 2026-10-06 --> |
| R-012-microservices-architecture | Microservices | Erken optimizasyon <!-- NO FILE on disk 2026-10-06 --> |

## 6. Kategori Haritası

| Kategori | Frozen | Active | Toplam |
|----------|--------|--------|--------|
| Security | 8 | 6 | 14 |
| Database | 4 | 12 | 16 |
| Architecture | 6 | 1 | 7 |
| Frontend | 2 | 5 | 7 |
| Audio | 3 | 2 | 5 |
| Routing | 3 | 0 | 3 |
| Infrastructure | 2 | 2 | 4 |
| AI | 3 | 1 | 4 |
| Testing | 1 | 0 | 1 |
| Documentation | 1 | 0 | 1 |
| Download | 1 | 0 | 1 |
| Social | 1 | 0 | 1 |
| Mobile | 1 | 0 | 1 |
| Vault | 0 | 1 | 1 |
| Electronics | 0 | 6 | 6 |
| **TOPLAM** | **36** | **36** | **72** |

> Not: Bu harita yalnızca accepted ADR'leri kapsar (72). Rejected (7) + Draft (0) hariçtir.

---

*Decisions Index v1.1.5 — CoreMusic Vault*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-29*
*Mode: Red Team · Human Mode · Truth Mode*
