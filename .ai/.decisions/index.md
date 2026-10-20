---
type: index
category: decisions
title: "CoreMusic — Decisions Index"
date: 2026-08-15
updated: 2026-10-20
status: active
version: 1.5.1
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
total-accepted: 80
total-rejected: 10
total-draft: 1
total-frozen: 36
total-active: 45
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

Tüm Architecture Decision Records (ADR) indeksini sunan, durumlarını ve kategorilerini kataloglayan **ana navigasyon dosyası**dır. **Bağlam** da ADR'ler burada toplanır, okunur, yazılır.

## 2. Genel Bakış

> **Disk ölçümü (2026-10-12 · güncelleme 2026-10-17):** `accepted/` = **80** ADR dosyası · `rejected/` = **10** R dosyası · `draft/` = **3** (ADR-097, 2026-10-12 + ADR-102, 2026-10-13 + **ADR-103** medya-sağlayıcı auth gate, 2026-10-17 eklendi) · kökte **1** ADR (`ADR-091-template-engine-no-eval.md` — yanlış konumda, §4'te bağlantılandı) → **gerçek toplam 94**.
> Önceki 2026-10-10 sayımı (accepted 83 / rejected 12 / draft 1 / toplam 96) disk gerçeğiyle **çelişiyordu** ve §2/§6 tabloları 78-96 arasında değişiyordu; sayılar bugün dosya sayısından yeniden yazıldı.

| Durum | Sayı | Açıklama |
|-------|------|----------|
| **Frozen** | **36** | Değiştirilemez (ADR-001 → ADR-036 — diskte tam) |
| **Active** | **45** | `accepted/` içindeki **44** (ADR-037 üzeri) + kökteki **ADR-091** |
| **Rejected** | **10** | `rejected/` 10 dosya (R-011/R-012'de dosya YOK — §5'te işaretli) |
| **Draft** | **3** | `draft/ADR-097-k-space-v3-…` + `draft/ADR-102-tur-taksonomi-genislemesi-5-dal` (2026-10-13) + `draft/ADR-103-media-provider-auth-gate` (2026-10-17) — §4A | |
| **Toplam** | **94** | 80 accepted + 10 rejected + 3 draft + 1 kök ADR |

> **Not (2026-10-12):** **ADR-061 (Electronics Architecture L6 — K uzayı katman tanımı)**, K-space katman mimarisi (K000–K020 / A0–A5 / `.ai/architecture/`) ile birlikte **kaldırıldı** — dosya silindi, yedek `C:\temp\opencode\vault-backup-2026-10-12\decisions-adr061\`.
> Not (2026-10-06 LINT-3): §3 başlığı ve §6 eski hali ADR-037'yi frozen sayıyordu (37/35); §2'deki "ADR-037 debate ✅, frozen YOK" kaydı esas alındı → 36.
> Not (2026-10-09): ADR-098..101 eklendi (50 yıllık mimari ilkeler araştırması → mimari plan §8 kararları).
> Not (2026-10-10): ADR-096/097 silinmişti (K-Space V2 rejimi).
> Not (2026-10-12): **ADR-097 yeniden yazıldı** (K-Space V3 — sıfırdan kurulum, çoklu-MD şema, isimlendirme, Band-2..7 rezervasyonu) → `draft/` (onay bekliyor, §4A).
> Not (2026-10-17): **ADR-103 eklendi** (medya-sağlayıcı auth gate) → `draft/` (onay bekliyor, §4A) — K020-discovery tur-2 keşfinden devredildi; kanıt: `.ai/architecture/K020-discovery/discovery-web-arastirma.md` (12 servis · 18 URL · 0 hata).

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

## 4. Active ADR'ler (037-101 — diskte dosyası olanlar)

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
| [[accepted/ADR-084-api-gateway-architecture]] | API Gateway Architecture (Gateway/BFF×4 IMPLEMENTED; CQRS + OpenAPI PLANNED — 2026-10-07 dolduruldu) | Architecture |
| [[accepted/ADR-085-modular-composer-packages]] | Shared Library Hybrid (tek shared/ + PSR-4 namespace — diskte implement, 2026-10-07 dolduruldu) | Architecture |
| [[accepted/ADR-086-event-driven-architecture]] | Event Driven Architecture (PSR-14 altyapı implement; production wiring PLANNED — 2026-10-07 dolduruldu) | Architecture |
| ADR-087-master-implementation-plan | Master Implementation Plan | Architecture <!-- NO FILE on disk 2026-10-06 — brain.md only --> |
| ADR-088-gender-based-social-oauth | Gender-Based Social OAuth | Social <!-- NO FILE on disk 2026-10-06 — brain.md only --> |
| [[accepted/ADR-089-classab-24v]] | Class AB Amplifikatör + 6S LiPo + ±35V Boost | Electronics |
| [[accepted/ADR-090-channel-variant-product-family]] | Kanal Varyant Ürün Ailesi (mono → 8+1 SKU) | Electronics |
| [[ADR-091-template-engine-no-eval]] | TemplateEngine eval() Kaldırımı (Guardrail #21) | Security |
| [[accepted/ADR-092-media-dizin-ekseni-ve-ulid]] | Medya Arşivi Dizin Ekseni ve ULID Kimliği | Infrastructure |
| [[accepted/ADR-093-view-modes-single-load-path]] | 09_ViewModes v-*.css için tek yükleme yolu: <link id="cm-view-css"> kanoniktir, cihaz @import zinciri deferred | Frontend |
| [[accepted/ADR-094-api-pipeline-origin-csrf]] | API pipeline'ına OriginCheck + koşullu CSRF eklendi (ADR-020 sıra genişletmesi; B-F-02/B-F-03) | Security |
| [[accepted/ADR-095-hybrid-jwt-rs256-access-token]] | Hybrid JWT (RS256): issue/validate/revocation — firebase/php-jwt, jti→user_tokens, 0 migration | Security |
| [[accepted/ADR-098-modul-siniri-gizlenecek-karar]] | Modül Sınırı = Gizlenecek Karar (Parnas 1972) | Architecture |
| [[accepted/ADR-099-tek-deploy-modular-monolith]] | Tek Deploy + Zorlanabilir Modül Sınırları (Modular Monolith) | Architecture |
| [[accepted/ADR-100-cross-db-outbox-cqrss-semi]] | Cross-DB Yazmada Outbox Zorunlu; CQRS Yalnız Okuma Sorgusu Düzeyinde | Database |
| [[accepted/ADR-101-guvenlik-kanonik-sirasi-uzak-cagri]] | Güvenlik Kanonik Sırası + Uzak Çağrı Sınırları (Timeout/Retry/Fallback) | Security |
| [[accepted/ADR-0105-neva-player-media-stack]] | Neva Player Media Stack: Açık Kaynak Decode (dr_libs+libdsd+ffmpeg-gateway) + WASAPI Exclusive Bit-Perfect + libplacebo 4K/HDR + 31-Band Parametrik EQ; 3-tur debate 4/4 koşullu-kabul → v1.1 kabul (2026-10-19) | Audio |
| [[accepted/ADR-106-internet-radio-and-selectable-band-eq]] | Neva Player Internet Radio (ICY/Shoutcast özel tuner + HLS FFmpeg gateway) + Kullanıcı-Ayarlanabilir Bant EQ (10/15/20/31, maks 31); 4 persona debate 4/4 koşullu-kabul → 27 şart → v1.1 kabul (2026-10-20); v1.1.1: VLC dump 62.5/`63`=0 teyidi işlendi | Audio |

## 4A. Draft ADR'ler

| ADR | Başlık | Kategori |
|-----|--------|----------|
| [[draft/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu]] | K-Space V3: Sıfırdan Kurulum, Çoklu-MD Düğüm Şeması, İsimlendirme Spesifikasyonu ve Band-2..7 Rezervasyonu | Architecture |
| [[draft/ADR-102-tur-taksonomi-genislemesi-5-dal]] | K001 donanaım-type Tür Taksonomisi Genişlemesi: 3 → 9 Bağlam (display · power · cables · network · memory · cpu) | Architecture |
| [[draft/ADR-103-media-provider-auth-gate]] | Medya-Sağlayıcı Auth Gate: Harici Servis Kimlik Şeması Kapısı ve Üretim Entegrasyonu Ön Koşulları (K020-discovery tur-2 devri) | Security |

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

> **Yeniden ölçüm (2026-10-12 — dosya sayımı, §6 eski 78 satırı geçersizdir).** Yalnız **diskte dosyası olan** ADR'ler sayıldı (`accepted/` 80 + kökteki ADR-091 = 81); `<!-- NO FILE -->` işaretli ADR-083/087/088 bu tabloya **alınmadı**. Rejected (10) §5'tedir, bu tabloda değildir. **Artış (2026-10-20):** ADR-106 accepted'a eklendi → `accepted/` 81 + ADR-091 = **82** (Audio Active 2→3).

| Kategori | Frozen | Active | Toplam |
|----------|--------|--------|--------|
| Security | 8 | 10 | 18 |
| Database | 4 | 13 | 17 |
| Architecture | 6 | 6 | 12 |
| Frontend | 2 | 5 | 7 |
| Electronics | 0 | 5 | 5 |
| Audio | 3 | 3 | 6 |
| AI | 3 | 1 | 4 |
| Infrastructure | 2 | 2 | 4 |
| Routing | 3 | 0 | 3 |
| Documentation | 1 | 0 | 1 |
| Download | 1 | 0 | 1 |
| Mobile | 1 | 0 | 1 |
| Social | 1 | 0 | 1 |
| Testing | 1 | 0 | 1 |
| Vault | 0 | 1 | 1 |
| **TOPLAM** | **36** | **46** | **82** |

---

*Decisions Index v1.5.0 — CoreMusic Vault*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-10-20*
*Mode: Red Team · Human Mode · Truth Mode*