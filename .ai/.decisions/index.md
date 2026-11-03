---
type: index
category: decisions
title: "CoreMusic — Decisions Index"
date: 2026-08-15
updated: 2026-11-01
status: active
version: 1.5.3
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
total-accepted: 85
total-rejected: 10
total-draft: 11
total-frozen: 36
total-active: 50
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

> **Disk ölçümü (2026-11-01 — v1.5.3):** `accepted/` = **85** (2026-10-24 `377224f`: ADR-110-CSP accepted'a girdi; FM v1.5.2 kaydı 84'tü) · `rejected/` = **10 R dosyası** (R-001..R-010; klasörde ayrıca 2 helper dosya `CLAUDE.md`/`index.md` — ham `*.md` sayımı 12 verir, karar kaydı değildirler) · `draft/` = **11** (ADR-103/104/107/108/109/111 + **ADR-112/113/114/115** + **ADR-116**) · kökte **1** (`ADR-091`) → **gerçek toplam 107**.
> Uyarı: 2026-11-01 ham sayımında `rejected=12` çıkmıştı — helper dosyalar karışmıştı; R dosyası 10'dur (disk kazanır kuralı).
> **Numara çakışması (ÇÖZÜLDÜ 2026-11-02):** `accepted/ADR-110-csp-style-src-attr-unsafe-inline-kabul` (2026-10-24) ile çakışan taslak dosya `draft/ADR-116-soft-adr-notlari-…` olarak yeniden numaralandırıldı; çakışma kalmadı.

| Durum | Sayı | Açıklama |
|-------|------|----------|
| **Frozen** | **36** | Değiştirilemez (ADR-001 → ADR-036 — diskte tam) |
| **Active** | **50** | accepted/ içindeki **49** (ADR-037 üzeri; accepted 85 − frozen 36 = 49) + kökteki **ADR-091** (2026-11-01 disk sayımı) |
| **Rejected** | **10** | R-001..R-010 dosyası (R-011/R-012 kayıt YOK); klâsörde 2 helper dosya (CLAUDE.md/index.md) sayılmaz |
| **Draft** | **11** | ADR-103 + ADR-104 + ADR-107 + ADR-108 + ADR-109 + ADR-111 + **ADR-112/113/114/115** + **ADR-116(soft)** — §4A | |
| **Toplam** | **107** | 85 accepted + 10 rejected + 11 draft + 1 kök ADR (2026-11-01 disk ölçümü) | |

> **Not (2026-10-23 — Prompt Arşivi Taraması Revizyonu):** Disk sayımı index ile hizalandı:
> `accepted/` 83 → **84** (**ADR-097 K-Space V3 `draft/` → `accepted/`'e terfi** — üretim diskte tam, kullanıcı onayı 2026-10-23) ·
> `draft/` **6'da kaldı** (ADR-097 terfi −1, ADR-110 soft-ADR ekleme +1 — 2026-10-23 disk sayımı: 103/104/107/108/109/110) · **numara çakışması giderildi**: `draft/ADR-098-k-space-kimlik-…` → **ADR-108**,
> `draft/ADR-102-tur-taksonomi-…` → **ADR-109** (accepted ADR-098-Parnas ve ADR-102-admin-theme korundu) ·
> §4A'ya **ADR-104** ve **ADR-107** ilk kez eklendi (önceki index'te hiç geçmiyorlardı).

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
| [[accepted/ADR-097-k-space-v3-sifirdan-kurulum-coklu-md-ve-bant-rezervasyonu]] | K-Space V3: Sıfırdan Kurulum, Çoklu-MD Düğüm Şeması, İsimlendirme ve Band-2..7 Rezervasyonu (draft → accepted terfi 2026-10-23) | Architecture |
| [[accepted/ADR-098-modul-siniri-gizlenecek-karar]] | Modül Sınırı = Gizlenecek Karar (Parnas 1972) | Architecture |
| [[accepted/ADR-099-tek-deploy-modular-monolith]] | Tek Deploy + Zorlanabilir Modül Sınırları (Modular Monolith) | Architecture |
| [[accepted/ADR-100-cross-db-outbox-cqrss-semi]] | Cross-DB Yazmada Outbox Zorunlu; CQRS Yalnız Okuma Sorgusu Düzeyinde | Database |
| [[accepted/ADR-101-guvenlik-kanonik-sirasi-uzak-cagri]] | Güvenlik Kanonik Sırası + Uzak Çağrı Sınırları (Timeout/Retry/Fallback) | Security |
| [[accepted/ADR-102-admin-theme-manager-architecture]] | Admin Theme Manager Mimarisi & Pembe Işıl-Peri Token Adaptasyonu | Frontend |
| [[accepted/ADR-0105-neva-player-media-stack]] | Neva Player Media Stack: Açık Kaynak Decode (dr_libs+libdsd+ffmpeg-gateway) + WASAPI Exclusive Bit-Perfect + libplacebo 4K/HDR + 31-Band Parametrik EQ; 3-tur debate 4/4 koşullu-kabul → v1.1 kabul (2026-10-19) · **v1.1.1 (2026-10-20): libmpv "hibrit fallback" seçeneği REDDEDİLDİ (mpv GPL-2.0 copyleft + black-box — gerekçe §3); Ghidra decompile dump referansı eklendi** · **v1.2.0 (2026-10-20): 112 kaynaklı web doğrulama — soxr LGPL-2.1 düzeltmesi, ASIO dual-license, libdsd UNKNOWN, miniaudio distortion riski** · **v1.2.1 (2026-10-20): VRAM boşluğu kapatıldı — P010 12,4→24,9 MB/kare düzeltmesi + §2.8 bütçe dökümü (~400-700 MB, derived) + V0-V5 tükenme merdiveni + §5.6 VRAM peak ölçüm kapısı** · **v1.3.0 (2026-10-20): kriter↔kapı boşlukları — §2.9 sistem RAM bütçesi (≤400 MB) + §2.10 gecikme bütçesi (<20 ms, blok ≥1024@48k yasak, L1-L4 merdiven) + kapı 5.10 gecikme ölçümü + kapı 5.11 A/V sync eşiği (P95 ≤20 ms)** · **v1.3.1 (2026-10-20): VRAM ikinci denetim — ölçüm ortamı zorunlu alanları (GPU/sürücü, bench `UNKNOWN`) + merdiven V0-V5 doğrulama testi (§5.6) + AK-4 VRAM ölçütü (kapsam v0.2.2)** · **v1.3.2 (2026-10-20): VRAM üçüncü denetim — bench atandı `coremusic-dev` (GTX 1660 · 6144 MB · sürücü 610.62 · Win10 19045, 3 kaynak teyitli) + masaüstü baseline (1511 MiB / 1706 MB) + bench≠hedef-sınıf notu (1060 3 GB'ta 600 MB = %19,5); §7.5 — NevaPlayer henüz derlenmediği için peak ölçümü açık kalır** · **v1.4.0 (2026-10-20): kalan boşluklar 4-5 — kapı 5.12 gapless format vektörü (AK-5 8 format: MP3 LAME/Xing delay+padding, FLAC seektable, Opus granule, DoP/DSF marker continuity; dosya→dosya + radyo akış→akış; DAC loopback ±5 ms korelasyon, >20 ms FAIL — derived) + kapı 5.13 hostile input fuzzing (format başına ≥1000 sentetik corpus — ≥8000 derived; 0 ASan/UBSan crash, 0 OOB, CI 10 dk; OSS-Fuzz atfı VERIFICATION REQUIRED); kapsam v0.2.3: AK-5 vektör genişletmesi + AK-11 hostile corpus** | Audio |
| [[accepted/ADR-106-internet-radio-and-selectable-band-eq]] | Neva Player Internet Radio (ICY/Shoutcast özel tuner + HLS FFmpeg gateway) + Kullanıcı-Ayarlanabilir Bant EQ (10/15/20/31, maks 31); 4 persona debate 4/4 koşullu-kabul → 27 şart → v1.1 kabul (2026-10-20); v1.1.1: VLC dump 62.5/`63`=0 teyidi işlendi | Audio |
| [[accepted/ADR-110-csp-style-src-attr-unsafe-inline-kabul]] | CSP style-src-attr unsafe-inline (kabul edilmiş risk, SEC-08) | Security |

## 4A. Draft ADR'ler

> **2026-10-23 notu:** ADR-097 accepted'a terfi etti; ADR-098-draft → **ADR-108**, ADR-102-draft → **ADR-109** olarak yeniden numaralandırıldı (numara çakışması giderildi). ADR-104/ADR-107 bu tabloya ilk kez eklendi.
> **2026-11-01 notu:** ADR-112/113/114/115 bu oturumda draft yazıldı (tümü kullanıcı kararı ALINDI — accepted geçiş şartı ilgili ADR §6'da).
> **2026-11-02 notu:** Draft **ADR-110 (soft-ADR)** numarası `accepted/ADR-110-csp` (2026-10-24) ile çakıştığı için kullanıcı onayıyla **ADR-116** olarak yeniden numaralandırıldı; numara çakışması tamamen çözüldü.

| ADR | Başlık | Kategori |
|-----|--------|----------|
| [[draft/ADR-103-media-provider-auth-gate]] | Medya-Sağlayıcı Auth Gate: Harici Servis Kimlik Şeması Kapısı ve Üretim Entegrasyonu Ön Koşulları (K020-discovery tur-2 devri) | Security |
| [[draft/ADR-104-page-router-kernel-god-class-bolme-plani]] | PageRouter Kernel God-Class Bölme Planı | Architecture |
| [[draft/ADR-107-neva-engine-platform-ve-paketleme]] | Neva Engine Platform ve Paketleme | Audio |
| [[draft/ADR-108-k-space-kimlik-duzeltmesi-disk-karsilastirmasi-ve-yeniden-numaralandirma]] | K-Space Kimlik Düzeltmesi, Disk Karşılaştırması ve Yeniden Numaralandırma (eski adı draft/ADR-098) | Architecture |
| [[draft/ADR-109-tur-taksonomi-genislemesi-5-dal]] | K001 donanım-type Tür Taksonomisi Genişlemesi: 3 → 9 Bağlam (eski adı draft/ADR-102) | Architecture |
| [[draft/ADR-116-soft-adr-notlari-select-star-samesite-position-itcss]] | Soft-ADR Notları: SELECT * İstisnası (admin/konsol), SameSite=Lax, Position Mekanizması Serbestliği, ITCSS 11-Katman SSOT (eski adı draft/ADR-110) | Security + CSS |
| [[draft/ADR-111-amfi-urun-ailesi-power-governor-18650-bypass]] | Amfi Ürün Ailesi (9 SKU, azami 8 kanal/500 W) + Power Governor (empedans doğrulamalı watt kilidi) + 18650 Adaptör-Bypass Politikası | Electronics | Embedded + Audio HW + MO |
| [[draft/ADR-112-media-soft-delete-canli-dump-hizalama]] | media_access/media_audit Soft-Delete Kolonları — Canlı Şema Dump SSOT'a Hizalanır (kullanıcı kararı 2026-11-01, seçenek A) | Data |
| [[draft/ADR-113-api-v2-paralel-kurumsallasma]] | API Sürüm Stratejisi — V1/V2 Paralel Kurumsallaşma (kullanıcı kararı 2026-11-01, seçenek C) | Architecture |
| [[draft/ADR-114-repository-pattern-standarti-yeni-kod-kurali]] | Repository Pattern Standardı — Yeni Kod İçin Kural (Kod Donduruldu; mevcut repo'lar dokunulmaz) | Architecture |
| [[draft/ADR-115-media-yonetimi-adminde-faz3-gui-iptal]] | Media Yönetimi Admin Panelde — media Faz 3 Web Paneli İPTAL (faz3-gui-spec superseded 0.1.1) | Architecture |

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
| [[rejected/R-008-mysql-myisam-engine]] | MyISAM | Transaction eksik <!-- dosya VAR — 2026-11-01 disk ölçümü --> |
| [[rejected/R-009-single-database-architecture]] | Single DB | Güvenlik/performans <!-- dosya VAR — 2026-11-01 disk ölçümü --> |
| [[rejected/R-010-nodejs-backend-fullstack]] | Node.js Full Stack | PHP zorunlu <!-- dosya VAR — 2026-11-01 disk ölçümü --> |
| R-011-graphql-api | GraphQL | Over-engineering <!-- NO FILE on disk 2026-11-01 --> |
| R-012-microservices-architecture | Microservices | Erken optimizasyon <!-- NO FILE on disk 2026-11-01 --> |

## 6. Kategori Haritası

> **Yeniden ölçüm (2026-10-12 — dosya sayımı, §6 eski 78 satırı geçersizdir).** Yalnız **diskte dosyası olan** ADR'ler sayıldı (`accepted/` 80 + kökteki ADR-091 = 81); `<!-- NO FILE -->` işaretli ADR-083/087/088 bu tabloya **alınmadı**. Rejected (10) §5'tedir, bu tabloda değildir. **Artış (2026-10-20):** ADR-106 accepted'a eklendi → `accepted/` 81 + ADR-091 = **82** (Audio Active 2→3). **Artış (2026-10-24):** ADR-110-CSP accepted (`377224f`) → accepted 85 + ADR-091 = **86**. TOPLAM satırı 36+49=85, 2026-11-01 accepted ölçümüyle kazara eşleşir; kategori satırları ayrı ölçümde yeniden sayılır.

| Kategori | Frozen | Active | Toplam |
|----------|--------|--------|--------|
| Security | 8 | 10 | 18 |
| Database | 4 | 13 | 17 |
| Architecture | 6 | 7 | 13 |
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
| **TOPLAM** | **36** | **49** | **85** |

---

*Decisions Index v1.5.3 — CoreMusic Vault*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-11-01*
*Mode: Red Team · Human Mode · Truth Mode*
