---
title: "CoreMusic — Engineering Brain (Enterprise SSOT)"
type: brain
category: architecture-decisions
date: 2026-08-08
updated: 2026-08-13
status: active
version: 23.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/brain.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/WORKFLOW.md · .ai/brain.md · .ai/index.md"
---

# CoreMusic — Engineering Brain (Enterprise SSOT)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[MEMORY.md]] · [[log.md]] · [[.templates/index]] · [[.agents/AGENTS.md]]

**Skills:** `.opencode/skills/` (10 skill — Guardrail #16 zorunlu)

---

## 1. Amaç

CoreMusic platformunun tüm mühendisleri ve AI ajanları için mimari kararların, donanım/yazılım kısıtlamalarının ve ses işleme spesifikasyonlarının tutulduğu Ana Mühendislik Hafızasıdır (SSOT). `index.md` harita ise, bu belge mühendislik defteridir.

---

## 2. Scope

C++ Audio DSP (ASIO, WASAPI, ring buffer, zero-allocation, 32-bit float PCM), 8+1 Surround (Class AB, XMOS XU316, PCM3168A/AK4458), PHP Middleware Pipeline (SessionManager→Csrf), 18 BCNF DB, AES-256-GCM Credential Vault, 10 panel mimarisi, AI Auto-Download (YouTube→deemix→FLAC), 3 fazlı geliştirme, 5 deployment modu, 5 audio division.

---

## 3. Core Principles

| Prensipl | Açıklama |
|----------|----------|
| SOLID | Tek Sorumluluk, Açık Kapalılık, Yerine Koyma, Arayüz Ayrımı, Bağımlılık Tersi |
| Clean Architecture (L0-L6) | Infrastructure → Security → Routing → Presentation → Domain → Services → Electronics |
| Hexagonal Architecture | Adapter/Port pattern ile bağımsızlık |
| DRY | Tekrarlanan kod yasağı |
| YAGNI | Gereksiz özellik ekleme yasağı |
| Real-Time Thread Model | Audio thread'de blocking operations yasak |
| Zero Code Before Plan | Plan onayı olmadan kod yazma yasağı (ADR-007) |

---

## 4. Tech Stack

| Katman | Teknoloji | Versiyon |
|--------|-----------|----------|
| Backend | PHP (strict_types=1) | 8.4+ |
| Frontend | Vanilla JS ES6+ (framework YASAK) | ES2022 |
| CSS | ITCSS + BEM | 7-layer |
| Database | MySQL / MariaDB (PDO, ORM YASAK) | 18 BCNF |
| Audio Engine | C++20, JUCE 9, ASIO SDK 2.3.4 | — |
| Hardware | XMOS XU316, PCM3168A | PCM5122 REDDEDİLMİŞ |
| Rate Limiting | APCu | 60 req/60s |
| Encryption | AES-256-GCM, Argon2id | NIST SP 800-38D |

**ASIO SDK Download:** https://www.steinberg.net/developers/asiosdk-open/

### 4A. Enterprise Composer Stack (prompt0 + prompt2 — "Build Business Logic, Not Infrastructure")

Temel ilke: **Önce standart çözüm, sonra Composer paketi, en son özel implementasyon.**

#### Çekirdek Paketler (Minimum Enterprise Stack)

| Kategori | Paket | PSR | Amaç |
|----------|-------|-----|------|
| **DI** | `php-di/php-di` | PSR-11 | Dependency Injection Container |
| **Router** | `nikic/fast-route` | — | Enterprise Router |
| **HTTP Message** | `nyholm/psr7` | PSR-7 | HTTP Message Implementation |
| **HTTP Emitter** | `laminas/laminas-httphandlerrunner` | — | Response Emitter |
| **JWT** | `lcobucci/jwt` | — | JWT Token Yönetimi (RS256) |
| **UUID** | `ramsey/uuid` | — | UUID Üretimi |
| **Logger** | `monolog/monolog` | PSR-3 | Structured Logging |
| **Env** | `vlucas/phpdotenv` | — | Environment Variables |
| **Validation** | `respect/validation` | — | Request/DTO Validation |
| **CSRF** | `symfony/security-csrf` | — | CSRF Koruması |
| **Cache** | `symfony/cache` | PSR-6 | Cache (Redis, APCu, File) |
| **Redis** | `predis/predis` | — | Redis Client |
| **Event** | `symfony/event-dispatcher` | PSR-14 | Event Dispatcher |
| **Rate Limit** | `symfony/rate-limiter` | — | Rate Limiting |
| **Filesystem** | `league/flysystem` | — | Dosya Sistemi Abstraction |
| **HTTP Client** | `guzzlehttp/guzzle` | PSR-18 | HTTP Client |
| **Encryption** | `paragonie/halite` | — | AES-256-GCM Encryption |
| **HTML Sanitizer** | `ezyang/htmlpurifier` | — | XSS Koruması |
| **Device** | `mobiledetect/mobiledetectlib` | — | Cihaz Algılama |
| **Scheduler** | `dragonmantank/cron-expression` | — | Cron Expression |
| **Migration** | `robmorgan/phinx` | — | DB Migration |
| **DBAL** | `doctrine/dbal` | — | Database Abstraction (PDO üstü) |
| **Serializer** | `symfony/serializer` | — | DTO/JSON/XML Dönüşümleri |
| **Mailer** | `symfony/mailer` | — | Mail Gönderimi |
| **Console** | `symfony/console` | — | CLI Komutları |
| **Lock** | `symfony/lock` | — | Distributed Lock |

#### PSR Standartları

```
psr/log, psr/container, psr/http-message, psr/http-server-handler,
psr/http-server-middleware, psr/http-factory, psr/http-client,
psr/event-dispatcher, psr/cache, psr/simple-cache
```

#### Auth & Security Paketleri

| Paket | Amaç |
|-------|------|
| `lcobucci/jwt` | JWT token yönetimi (RS256) |
| `paragonie/sodium_compat` | Libsodium wrapper |
| `paragonie/constant_time_encoding` | Timing attack koruması |
| `symfony/password-hasher` | Şifre hashleme wrapper |
| `pragmarx/google2fa` | MFA/2FA (TOTP) |
| `endroid/qr-code` | QR kod üretimi |
| `league/oauth2-server` | OAuth2 Server |

#### Geliştirme Araçları

| Paket | Amaç |
|-------|------|
| `phpunit/phpunit` | Unit Test |
| `pestphp/pest` | Modern Test Framework |
| `phpstan/phpstan` | Static Analysis (level 8) |
| `friendsofphp/php-cs-fixer` | Kod Standartları (PSR-12) |
| `rector/rector` | Automated Refactoring |
| `roave/security-advisories` | Güvenlik Danışmanı |
| `deptrac/deptrac` | Katman Bağımlılık Analizi |

#### Yasaklı Paketler

| Yasaklı | Neden | Doğru |
|---------|-------|-------|
| Doctrine ORM | ORM yasak (ADR-002) | PDO + Doctrine DBAL |
| Laravel Eloquent | ORM yasak (ADR-002) | PDO |
| Propel | ORM yasak (ADR-002) | PDO |
| `firebase/php-jwt` | Yasaklı — RS256 için `lcobucci/jwt` kullanılır | `lcobucci/jwt` |
| `mysql_*` fonksiyonları | Deprecated | PDO |
| MD5/SHA1 | Güvensiz hash | Argon2id |
| mcrypt | Deprecated | paragonie/halite |

### 4B. API Architecture (prompt3 — API-First, Gateway, BFF, CQRS)

CoreMusic API tek bir büyük API değil, servis bazlıdır. Tüm istemciler API Gateway üzerinden bağlanır.

#### Temel Prensip: Contract First

```
OpenAPI Spec → DTO → Contract → Validation → Use Case → Kod
```

**Kod hiçbir zaman sözleşmeden önce yazılmaz.**

#### API Gateway

Tüm istemcilerin tek giriş noktası `api.coremusic.net`'tir. Gateway; routing, auth, rate limit, validation, logging, correlation ID görevini üstlenir.

#### BFF (Backend for Frontend)

Her istemci tipi kendi BFF'sini kullanır:

| İstemci | BFF | Response |
|---------|-----|----------|
| SPA | SPA BFF | Tam veri |
| Mobile | Mobile BFF | Minimal |
| Embedded (RPi5) | Embedded BFF | Ultra-minimal, gzip |
| Desktop | Desktop BFF | Orta boy |
| Admin | Admin BFF | Full + audit |
| Car | Car BFF | Touch-optimized |

#### CQRS

Yazma ve okuma işlemleri tamamen ayrılır:

```
Write: Command → Use Case → Repository → MySQL Master
Read:  Query → Read Model → Cache → Response
```

#### Event Driven (ADR-086)

Servisler birbirini doğrudan çağırmaz, event yayınlar:

```
Service A → Event Bus (PSR-14) → Service B, C, D
```

#### SPA → ApiClient Kuralı

```
SPA → ApiClient → HTTP → Gateway → Middleware → Use Case → Domain → Repository → Infrastructure
```

SPA **asla** PDO, MySQL, Repository, Entity, Infrastructure, Filesystem, FFmpeg, Redis, Cache veya SQL **görmez.**

---

## 5. Architecture Layers L0-L6

*Detaylı metadata için bakınız: [[architecture/00-overview/architecture-master]] §2*

| Katman | İçerik | Teknoloji |
|--------|--------|-----------|
| L6 Electronics | Hardware, firmware, driver, DSP, audio engine | C++20, JUCE 9, ASIO, XMOS |
| L5 Services | Application services, use cases, CQRS, event bus | PHP 8.4, PSR-14 |
| L4 Domain | Business rules, entities, value objects, aggregates | DDD, SOLID, Clean Architecture |
| L3 Presentation | Frontend, UI, DOM | Vanilla JS, ITCSS, TrustedTypes |
| L2 Routing | Router, middleware, dispatch | PHP 8.4 PageRouter |
| L1 Security | Session, Auth, CSRF, CSP | Middleware Pipeline |
| L0 Infrastructure | Database, cache, fs | PDO, APCu, Redis |

Bağımlılık: ✅ L6→L5, L5→L4, L4→L3, L3→L2, L2→L1, L1→L0 | ❌ L0→L2/L3, L1→L3, L3→L0. Layer Violation → derhal revert.

---

## 6. Middleware Pipeline (Sıra Değişmez — ADR-010/011/012/013/022)

```
1. OriginCheckMiddleware()      — Köken doğrulama (whitelist CORS)
2. CorsMiddleware()             — CORS header'ları (whitelist only)
3. RateLimiterMiddleware()      — APCu: 60 req/60s
4. SecurityHeadersMiddleware()  — CSP nonce üret, strict-dynamic, HSTS, X-Frame
5. SessionManagerMiddleware()   — Session başlat, CSP nonce'u session'a kaydet
6. CsrfMiddleware()             — csrf_token doğrulama (POST/PUT/DELETE)
7. BypassAuthMiddleware()       — Test bypass (production'da devre dışı)
8. AuthMiddleware()             — Auth bilgisi inject (session'dan okur)
9. PermissionMiddleware()       — RBAC yetki kontrolü (regular/premium/studio/car/admin/system)
10. ValidationMiddleware()      — Request/DTO validasyonu
→ Controller
```

CSP nonce üretimi SecurityHeaders (#4) içindedir. SessionManager (#5) bu nonce'u session'a kaydeder. Sıra değiştirilirse CSP bozulur.

---

## 7. C++ Audio Rules

### 7.1 Zero-Allocation Kuralı

Real-time audio callback içerisinde ❌ yasak: `malloc()`, `free()`, `new`, `delete`, `std::make_shared`, `std::vector` push_back, I/O blocking, `throw`. ✅ İzin: Stack tahsisi, `std::atomic`, SIMD (SSE2/AVX2/NEON), `constexpr`, member değişkenler, `alignas(64)`.

### 7.2 ASIO Callback

```cpp
void processAudioBlock(float** output, const float** input,
                       int channels, int samples) noexcept {
    for (int i = 0; i < samples; ++i)
        for (int ch = 0; ch < channels; ++ch) {
            float s = input[ch][i];
            s = dspChain[ch].processEQ(s);
            s = dspChain[ch].processCompressor(s);
            s = dspChain[ch].processLimiter(s);
            output[ch][i] = s;
        }
}
```

### 7.3 Thread & Cache

- Audio thread: `THREAD_PRIORITY_TIME_CRITICAL`. Normal: `THREAD_PRIORITY_NORMAL`.
- writeHead/readHead: `alignas(64) std::atomic<size_t>` (false sharing önleme).

---

## 8. Hardware

| Bileşen | Özellik |
|---------|---------|
| XMOS XU316 | USB Audio Class 2.0, zero-latency DSP |
| PCM3168A | 6-in/8-out codec, 24-bit, DAC 192kHz, ADC 96kHz, SNR 112dB (DAC) |
| AK4458 (opsiyonel) | 8-kanal high-end DAC, 32-bit, 768kHz |
| PCM5122 | ❌ REDDEDİLMİŞ — Sadece 2 kanal, 8.1 için yetersiz (H001) |
| Class AB Amp | 100W @ 8Ω, THD+N <0.01%, SNR >100dB, ±42V DC |

ASIO Buffer: 512 sample varsayılan (64-1024), 48kHz, 32-bit float, ~10.67ms gecikme.

---

## 9. 8.1 Surround

8 kanal + 1 LFE subwoofer. Kanallar: Front L/R (20Hz–20kHz), Center (100Hz–8kHz), Surround L/R (100Hz–16kHz), Rear L/R (100Hz–16kHz), Height L/R (200Hz–16kHz), Subwoofer LFE (20Hz–120Hz). Bass management: Linkwitz-Riley 4. nesil, crossover 80Hz.

---

## 10. PHP Security

| Parametre | Değer |
|-----------|-------|
| AES-256-GCM IV | 96-bit (12 byte) |
| AES-256-GCM Tag | 16 byte |
| AES-256-GCM Key | 256-bit (32 byte) |
| Argon2id Memory | 64MB |
| Argon2id Time | 4 iterations |
| Argon2id Threads | 2 |
| CSRF Token Key | `csrf_token` (NOT `_csrf_token`) |
| CSRF Doğrulama | `hash_equals()` (timing-safe) |
| CSP Nonce | `base64_encode(random_bytes(32))` |

PDO: Prepared statement zorunlu, SELECT * yasak, explicit column list.

---

## 11. 18 BCNF Databases (ADR-040)

*Detaylı metadata için bakınız: [[architecture/00-overview/architecture-master]] §3*

| # | Veritabanı | Amaç | Tablo Sayısı |
|---|------------|------|-------------|
| 1 | coremusic_auth | Kullanıcılar, roller, session, token, credential vault, API key | 13 |
| 2 | coremusic_user | Profiller, tercihler, geçmiş, favoriler | 7 |
| 3 | coremusic_musics | Şarkılar, sanatçılar, türler, sözler, dosyalar, podcast, video, radyo | 22 |
| 4 | coremusic_albums | Albüm koleksiyonları, diskler, istatistikler | 5 |
| 5 | coremusic_playlist | Kullanıcı ve AI çalma listeleri, işbirlikçiler, takipçiler | 5 |
| 6 | coremusic_catalog | Referans verileri (tür listesi, sanatçı rolleri, enstrümanlar, ruh halleri) | 8 |
| 7 | coremusic_logs | Audit trail, analitik, hata logları, performans metrikleri | 22 |
| 8 | coremusic_media | Cihaz senkronizasyonu, medya metadata, erişim kontrolü | 8 |
| 9 | coremusic_system | Ayarlar, config, cache, EQ, dosya yöneticisi, bildirimler, i18n | 17 |
| 10 | coremusic_social | Yorumlar, paylaşımlar, aktivite, dinleme odaları, bildirimler | 9 |
| 11 | coremusic_wireless | WiFi + Bluetooth ağları | 5 |
| 12 | coremusic_ai | Kullanıcı tercih profilleri, dinleme özellikleri, öneriler | 6 |
| 13 | coremusic_api | API anahtarları, rate limit, API çağrı logları, webhook'lar | 4 |
| 14 | coremusic_cms | Sayfalar, blog, etiketler, medya varlıkları, SSS, banner'lar | 8 |
| 15 | coremusic_download | İndirme kuyruğu, geçmiş, önbellek, kaynak API'leri | 4 |
| 16 | coremusic_neva | EQ preset'leri, DSP ayarları, yönlendirme matrisi, spektrum analizi | 4 |
| 17 | coremusic_studio | Stüdyo oturumları, parçalar, preset'ler, ekipman | 6 |
| 18 | coremusic_patch | Şema sürümleri, migration logları, yamalar | 3 |
| | **TOPLAM** | | **156** |

Kurallar: ORM yasak, SELECT * yasak, BCNF zorunlu, soft delete (`is_deleted = 0`), prepared statement, snake_case naming.

---

## 12. AI Auto-Download Pipeline

```
YouTube URL → nova-search-engine → deemix PHP port (Deezer FLAC) → 24/32-bit FLAC → coremusic_musics DB metadata
```

Anti-ban: Rate limiting, ARL token rotasyonu, proxy rotasyonu, User-Agent çeşitliliği. Kalite: FLAC 24/32-bit, MP3 320kbps fallback.

---

## 13. ADR Summary

### 13.1 Frozen (001-037)

| ADR | Konu |
|-----|------|
| ADR-001 | Vanilla JS + ITCSS, framework yasak |
| ADR-002 | PDO mandatory, ORM yasak |
| ADR-003 | 9 BCNF izole veritabanı |
| ADR-004 | Multi-domain SPA mimarisi |
| ADR-005 | Zero hallucination, VERIFICATION REQUIRED |
| ADR-006 | <200ms TTFB, <100ms API |
| ADR-007 | Cache namespace, Zero Code Before Plan |
| ADR-008 | Test bypass middleware |
| ADR-009 | Clean URL redirect |
| ADR-010 | csrf_token key zorunlu |
| ADR-011 | COREMUSIC_SESS, 3600s idle timeout |
| ADR-012 | strict-dynamic, nonce-based CSP |
| ADR-013 | APCu, 60 req/60s |
| ADR-014 | Forward-only, versioned migration |
| ADR-015 | .env dosya okuma stratejisi |
| ADR-016 | Subdomain routing |
| ADR-017 | XMOS XU316 + PCM3168A DSP |
| ADR-018 | Footer player vaporwave |
| ADR-019 | Per-OS Neva Player |
| ADR-020 | API güvenlik stratejisi |
| ADR-021 | SPA router immutable contract |
| ADR-022 | AES-256-GCM, Argon2id |
| ADR-023 | Persona bazlı test |
| ADR-024 | Modüler dokümantasyon |
| ADR-025 | 31-band parametrik EQ |
| ADR-026 | Node.js indirme servisi |
| ADR-027 | Hibrit depolama |
| ADR-028 | Rate limiting + proxy rotasyonu |
| ADR-029 | Sosyal dinleme odaları |
| ADR-030 | AI öneri motoru |
| ADR-031 | PWA + Flutter |
| ADR-032 | Versiyonlu IPC sözleşmeleri |
| ADR-033 | BCNF normalizasyon |
| ADR-034 | AES-256-GCM credential vault |
| ADR-035 | Prompt engineering standartları |
| ADR-036 | Çoklu proje prompt üretimi |
| ADR-037 | Kablosuz ağ entegrasyonu |

### 13.2 Active (038-087)

| ADR | Konu |
|-----|------|
| ADR-038 | XMOS XU316 + PCM3168A (PCM5122 REDDEDİLMİŞ) |
| ADR-039 | 7-servis platform mimarisi |
| ADR-040 | 18 BCNF veritabanı otoritesi |
| ADR-041 | DB normalizasyon ek bilgi |
| ADR-043 | Auth subdomain konsolidasyonu |
| ADR-044 | Cinsiyet bazlı dinamik tema |
| ADR-045 | Multi-domain view mode |
| ADR-046 | Cross-view state koruma |
| ADR-048 | View Transition API entegrasyonu |
| ADR-049 | Startup prompt loader |
| ADR-050 | Multi-DB sync stratejisi |
| ADR-061 | Electronics Architecture (L6 Layer) |
| ADR-062 | DSP Pipeline Architecture |
| ADR-063 | Hardware Design Standards |
| ADR-064 | Electronics Platform Architecture (L0-L6, 5 cihaz, 13 servis) |
| ADR-072 | Social DB Schema (comments, shares, activity, rooms, notifications) |
| ADR-073 | Podcast DB Schema (shows, episodes, subscriptions, transcripts) |
| ADR-074 | Radio DB Schema (stations, schedules, now_playing) |
| ADR-075 | AI DB Schema (preferences, features, recommendations, models) |
| ADR-076 | Video DB Schema (music_videos, playback, subtitles) |
| ADR-077 | Studio DB Schema (sessions, tracks, presets, equipment) |
| ADR-078 | CMS DB Schema (pages, blog, tags, media, FAQs, banners) |
| ADR-079 | i18n DB Schema (languages, translations, ui_strings, locale) |
| ADR-083 | SPA Router Architecture (PHP+JS Hybrid) |
| ADR-084 | API Gateway Architecture (API-First, BFF, CQRS) |
| ADR-085 | Shared Library Hybrid (tek shared/ + PSR-4 namespace) |
| ADR-086 | Event Driven Architecture (PSR-14) |
| ADR-087 | Master Implementation Plan (Sıfırdan Geliştirme Kapsamı) |

---

## 14. Development Strategy

| Faz | Hedef | Donanım | Süre |
|-----|-------|---------|------|
| Faz 1 — MVP | Mevcut PC/laptop'da temel platform | Mevcut ses kartları (WASAPI/ASIO) | 6–12 ay |
| Faz 2 — Premium | CoreMusic Audio donanım entegrasyonu | PCM3168A, AK4458, XMOS XU316, Class AB | 12–24 ay |
| Faz 3 — Professional | Tam entegre stüdyo ve araç içi | 8.1 surround, multi-room, NAS | 24–36 ay |

---

## 15. Platform Tiers

| Tier | OS | Durum |
|------|-----|-------|
| Tier 1 (Primary) | Windows (XP–11, Server 2012 R2+) | ✅ Ana geliştirme |
| Tier 2 | Linux (Ubuntu, Debian, Fedora, Arch) | ✅ Destekli |
| Tier 3 | macOS (Monterey–Sonoma) | ✅ Destekli |
| Tier 4 | Raspberry Pi (ARM64, Debian) | ✅ Destekli |
| Tier 5 | ReactOS | ⚠️ Experimental |

---

## 16. Audio Organization

| Division | Sorumluluk |
|----------|------------|
| Hardware Division | Özel audio kartları, DAC/ADC, DSP çipleri, amplifikatör |
| Software Division | C++ Audio Engine, DSP Engine, Mixer, sürücüler |
| Studio Division | ASIO, WASAPI, kayıt, monitoring, routing |
| Consumer Division | Bluetooth, WiFi Audio, müzik oynatma, ev ve araç ses |
| Research Division | AI DSP, yeni codec teknolojileri |

---

## 17. Hard Guardrails (14 Kural)

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Zero-Allocation: Audio thread'de heap allocation yasak | Ses takılması / crash |
| 2 | Lock-Free: Audio thread'de mutex yasak | Deadlock |
| 3 | Layer Violation: L0 → L3 import yasak | Derhal revert |
| 4 | SELECT *: Açık sütun listesi zorunlu | SQL injection riski |
| 5 | Hardcoded Secret: API key/log'da yasak | Güvenlik ihlali |
| 6 | csrf_token: Key ismi değişmez (ADR-010) | CSRF bozulması |
| 7 | Zero Code Before Plan: Plan onayı olmadan kod yok | Mimari bozulma |
| 9 | In-Place Refactoring: Dosya adı/konumu değişmez | Link kırılması |
| 10 | ORM Yasak: Sadece PDO prepared (ADR-002) | SQL injection |
| 11 | Framework Yasak: Sadece Vanilla JS (ADR-001) | Bağımlılık artışı |
| 12 | Middleware Sırası: Değişmez (ADR-010/011/012/013/022) | CSP/CSRF bozulması |
| 13 | Port 81: music.coremusic.net PHP 8.4 | Servis çökmesi |
| 14 | PCM5122 Yasak: 8.1 surround için yetersiz (H001) | Yanlış donanım |

---

## 18. Coding Standards

| Dil | Kritik Kurallar |
|-----|-----------------|
| PHP | `declare(strict_types=1)`, PSR-12, constructor injection, PHP 8.4+ |
| JavaScript | Vanilla ES6+ (framework yasak), `const`/`let`, async/await, AbortController, `#` private, DOMParser+TrustedTypes, innerHTML yasak |
| C++ | C++20, noexcept (ASIO callback), constexpr (buffer), alignas(64), [[nodiscard]] |
| CSS | ITCSS 9-layer, BEM+BEMIT, custom properties, main.css sadece 01-07 |

---

## 18A. Responsive CSS Architecture (a-layout-tokens.css v2.0.0)

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| Token Konsolidasyonu | Tek dosyada (`a-layout-tokens.css`) tüm responsive breakpoint'ler | [[architecture/l3-presentation]] |
| Default Viewport | 1024×600 (RPi5 embedded, mockup reference) | [[ui-design/00-mockup-index]] |
| Media Query Breakpoints | 4 adet: tablet (768-1024), mobile (≤767), desktop (≥1920), 4K TV (≥3840) | — |
| Token Kategorileri | Header/Footer heights, spacing, font scale, touch targets, glass blur, z-index | — |
| Device CSS Dönüşümü | `d-embedded.css`, `d-desktop.css`, `d-tablet.css` → sadece behavioral overrides (hover, touch, scrollbar) | — |

### Responsive CSS Mimarisi Kuralı (Zorunlu — Guardrail #17)

**1024×600 PNG mockup = Design Reference**
- Pixel reference: Tüm ölçüler PNG'den çıkarılır
- Layout authority: Layout kararı PNG mockup'a göredir
- Component measurement source: Bileşen boyutları PNG piksel ölçümü

**Frontend Implementasyon Kuralları:**
1. TEK component sistemi + responsive CSS
2. Ayrı HTML oluşturma → YASAK (Kod revert edilir)
3. Ayrı frontend branch oluşturma → YASAK
4. Hardcoded resolution lock → YASAK
5. CSS variables + media queries ile breakpoint yönetimi
6. Device CSS dosyaları = sadece behavioral overrides (hover, touch, scrollbar)

**CSS Token Sistemi:**
- `:root` = 1024px default (mockup reference)
- `@media (min-width: 1920px)` = Desktop override
- `@media (min-width: 3840px)` = 4K TV override
- `@media (max-width: 767px)` = Mobile override
- `@media (min-width: 768px) and (max-width: 1024px)` = Tablet override

**Yasak Örüntüleri:**
| ❌ Yasak | ✅ Doğru |
|----------|----------|
| home-1024.html, home-desktop.html | Tek HTML + responsive CSS |
| `if (screenWidth === 1024) { separate code }` | CSS media query + var() |
| device-loader.js ile tam CSS swap | CSS media query ile token override |
| Hardcoded `height: 90px` | `height: var(--footer-h)` |
| Hardcoded `width: 280px` | `width: var(--sidebar-w)` |

**Dosya Yapısı:**
```
01_Abstracts/a-layout-tokens.css  → Tüm token tanımları + media query overrides
08_Devices/d-embedded.css         → Sadece behavioral overrides (hover, touch, scrollbar)
08_Devices/d-desktop.css          → Sadece behavioral overrides
03_Layout/_header.css             → `var(--header-h)` kullanır
03_Layout/_footer.css             → `var(--footer-h)` kullanır
04_Components/*.css               → `var(--token)` kullanır
```

---

## 19. Edge Cases

| Edge Case | Tetikleyici | Çözüm | ADR |
|-----------|-------------|-------|-----|
| ASIO Device Loss | USB kopması | WASAPI fallback → Null Output | [[ADR-017-dsp-hardware-mode]] |
| Cache Stampede | Yüksek load | Mutex ile single load | [[architecture/l0-infrastructure]] |
| Multi-Tab CSRF | Birden fazla sekme | Token session-bound sabit | [[ADR-010-csrf-protection-strategy]] |
| Layer Violation | L0 → L3 import | Derhal revert | [[CLAUDE.md]] |
| PCM5122 Kullanımı | 8.1 surround denemesi | PCM3168A veya AK4458 | [[ADR-038-8.1-sound-card-chip-selection]] |
| Network Outage | İnternet kopması | Offline-First + SQLite queue | [[architecture/00-overview/architecture-master]] |
| BCNF Violation | Yeni tablo | 3NF → BCNF audit | [[ADR-040-database-authority]] |
| Buffer Underrun | CPU %100 | Fade-out → 50ms sessizlik → restart | [[engine.md]] |
| Session Timeout | 3600s idle | Otomatik yeniden auth | [[ADR-011-session-management]] |

---

## 20. Warnings

| # | Uyarı |
|---|-------|
| 1 | **H001:** PCM5122 ile 8.1 surround YAPILAMAZ. Sadece 2 kanal destekler. PCM3168A veya AK4458 kullanın. |
| 2 | **Middleware:** Sıra değiştirilmez. CSP nonce SecurityHeaders'da üretilir, SessionManager session'a kaydeder. |
| 3 | **SELECT * Yasak:** SQL injection riski. Her zaman açık sütun listesi. |
| 4 | **Düz Metin Secret:** API key, password, JWT secret ASLA kodda veya log'da düz metin. `[REDACTED]` kullanın. |
| 5 | **Zero-Allocation:** Audio thread'de `malloc()` ses takılmasına veya çökmesine yol açar. |
| 6 | **ASIO Exclusive Lock:** Aynı anda sadece tek uygulama. Çoklu deneme sürücü çökmesi. |
| 7 | **DC Offset Riski:** Class AB amfide >0.5V DC offset koruma rölesi tetiklenmeli. |

---

## 21. Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § Amaç | [[CLAUDE.md]] | Ana sözleşme |
| § Mimari | [[architecture/00-overview/architecture-master]] | L0-L6 |
| § C++ Audio | [[ADR-017-dsp-hardware-mode]] | XMOS, JUCE |
| § 8.1 Surround | [[ADR-038-8.1-sound-card-chip-selection]] | PCM3168A, H001 |
| § PHP Middleware | [[ADR-010-csrf-protection-strategy]] | csrf_token |
| § Cache/Vault | [[ADR-022-database-hardened-security]] | AES-256-GCM |
| § 18 BCNF DB | [[ADR-040-database-authority]] | 18 DB |
| § Audio Org | [[electronic/audio-organization]] | 5 bölüm |
| § Hardware | [[electronic/hardware-roadmap]] | 3 fazlı yol haritası |
| § 22 (Prompt Arsivi) | [[architecture/ai/prompt-engine]] | Prompt üretim motoru |
| § 22 (Prompt Arsivi) | [[CLAUDE#26-prompt-entegrasyonu]] | Boot protokolünde prompt entegrasyonu |
| § UI Design | [[ui-design/00-mockup-index]] | Mockup indeksi — 18 PNG |
| § Mockup PNG'ler | `.ai/.png/home-1024/` (12) + `.ai/.png/shared-1024/` (6) | RPi5 1024×600 mockup'lar |

---

## 22. Prompt Arşivi

Archives dizinindeki 4 ana prompt dosyası. Bu dosyalar vault'un parçasıdır ve her oturumun başında okunmalıdır.

| Prompt | Amaç | Kullanım | Konum |
|--------|------|----------|-------|
| `prompt0-genel-ana-prompt` | Ana genel prompt: 11 alt domain, 10 panel, 20 analiz görevi, zorunlu kurallar | Tüm agentlar, her analiz görevinde | [[archives/prompt0-genel-ana-prompt-2026-08-13]] |
| `prompt1-spa-router` | SPA Router: Enterprise router, SOLID, PSR, attribute-based, DI, route-cache, subdomain-aware | Backend Architect, UI Designer | [[archives/prompt1-spa-router-2026-08-13]] |
| `prompt2-auth` | Auth: Merkezi auth.coremusic.net, hybrid JWT+session, RBAC, middleware pipeline, CORS | Security Engineer, Backend Architect | [[archives/prompt2-auth-2026-08-13]] |
| `prompt3-api` | API: API-First, Gateway, CQRS, Event Driven, 14 servis, coremusic-shared | Backend Architect, DevOps Engineer | [[archives/prompt3-api-2026-08-13]] |

### 22.1 Prompt-Article Eşleşme Tablosu

| Prompt İçeriği | Vault'daki Karşılığı | ADR |
|----------------|----------------------|-----|
| prompt0: 11 alt domain | brain.md § 4A (Composer Stack) | ADR-087 |
| prompt0: 10 panel | brain.md § 9 (Paneller) | ADR-039 |
| prompt0: 20 analiz görevi | WORKFLOW.md genişletilmiş prompt bölümü | ADR-042 |
| prompt0: Zorunlu Kurallar | CLAUDE.md § 7 (Hard Guardrails) | ADR-007 |
| prompt1: Enterprise Router | architecture/l2-routing/spa-router.md § 1A | ADR-083 |
| prompt2: Central Auth | architecture/08-auth/auth-cross-domain.md, ADR-043 | ADR-043 |
| prompt2: Middleware Pipeline | brain.md § 6 | ADR-010/011/012/013/022 |
| prompt3: API Gateway | ADR-084 (API Gateway Architecture) | ADR-084 |
| prompt3: CQRS | brain.md § 4B (API Architecture) | ADR-086 |
| prompt3: Event Driven | ADR-086 (Event Driven Architecture) | ADR-086 |

---

## 23. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 23.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| ADR Coverage | 001–087 (87 ADR) |
| Panel Count | 10 |
| Service Count | 7 |
| DB Count | 18 BCNF |
| Audio Channels | 8+1 Surround |
| EQ Bands | 31 |
| Hardware Phases | 3 (MVP → Premium → Professional) |
| Platform Tiers | 5 |
| Hard Guardrails | 14 |
| Edge Cases | 10 |
| Warnings | 7 |
| Implementation Plan | 5 faz, 40 gün, 22 bölüm (ADR-087) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-13
**Mode:** Red Team · Human Mode · Truth Mode