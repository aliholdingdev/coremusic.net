---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — Engineering Brain (Enterprise SSOT)"
type: brain
category: architecture-decisions
date: 2026-08-08
updated: 2026-09-23
status: active
version: 26.1.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/brain.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/WORKFLOW.md · .ai/brain.md · .ai/index.md"
---

# CoreMusic — Engineering Brain (Enterprise SSOT)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[MEMORY.md]] · [[log.md]] · [[VISION.md]] · [[PROJECTS.md]] · [[.templates/index]] · [[.agents/AGENTS.md]]

**Skills:** `.opencode/skills/` (10 skill — Guardrail #16 zorunlu)

---

## Purpose

### §1 Amaç & Ekosistem Misyonu

CoreMusic; müzik yönetimi, ses işleme, cihaz entegrasyonu ve medya dağıtımı süreçlerini tek bir platform altında birleştiren kurumsal dijital ses ve medya ekosistemidir. Geleneksel müzik çalarların sunduğu basit dosya oynatma deneyiminin ötesine geçerek; çevrim içi bulut akışı ve internet bağlantısı olmadan çalışabilen **offline-first** mimarisi sayesinde kullanıcının FLAC, WAV ve MP3 formatındaki ses koleksiyonunu **tam mülkiyet** altında tutmasını sağlar.

Bu dosya, tüm mühendisler ve AI ajanları için mimari kararların, donanım/yazılım kısıtlamalarının ve ses işleme spesifikasyonlarının tutulduğu Ana Mühendislik Hafızasıdır (SSOT).
- **Ekosistem Vizyonu & Sorun-Çözüm Matrisi:** [[VISION.md]]
- **Proje Tanımı, Yetenekler & Kullanıcı Profilleri:** [[PROJECTS.md]]

---

## Scope

### §2 Scope

C++20 Audio DSP (Neva Engine: ASIO, WASAPI, ALSA, lock-free ring buffer, zero-allocation, 32-bit float PCM), 8.1 Surround (Class AB 8x50W, XMOS XU316, PCM3168A), PHP 8.4+ Middleware Pipeline (10 adımlı pipeline, Argon2id, AES-256-GCM Credential Vault), 18 BCNF DB (156 tablo), 10 panel / subdomain mimarisi, Çok Kaynaklı Otonom Downloader (NovaSearchEngine, Deezer/Deemix FLAC, YouTubeDownloader), 11 uzmanlık alanına sahip AI agent sistemi, 21 katmanlı (K0-K20) entegre mimari.

---

## Architecture

### §4 Tech Stack

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

### §4A Enterprise Composer Stack (prompt0 + prompt2 — "Build Business Logic, Not Infrastructure")

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

### §4B API Architecture (prompt3 — API-First, Gateway, BFF, CQRS)

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

### §5 K0-K20 21-Katmanlı Sistem Mimarisi (1000+ Bileşen)

*Detaylı metadata için bakınız: [[architecture/index]] §2*

Tüm CoreMusic altyapısı açık kaynak (GitHub) destekli 16 ana katmandan oluşur.

| Katman | İsim | Kapsam | GitHub/Teknoloji Ref |
|-------|--------|--------|-----------|
| **K15** | Medya & Streaming | FFmpeg, FLAC, HLS, DASH, ID3 | FFmpeg, Icecast |
| **K14** | Ağ & İletişim | HTTP/3, WebRTC, DLNA, AirPlay | WebRTC, libupnp |
| **K13** | CI/CD & Deploy | GitHub Actions, Playwright, Vitest | Docker, K8s |
| **K12** | İzleme & Log | App Logs, Prometheus, Grafana | Prometheus |
| **K11** | Kullanıcı Deneyimi | ITCSS, BEM, Design Tokens, PWA | Vanilla JS |
| **K10** | Uygulama | Web, Mobile, Car, TV, Studio, Panel | PWA, SPA |
| **K9** | API & Routing | Gateway, BFF, CQRS, Event Bus | OpenAPI |
| **K8** | Servis | Control, Media, Audio, Network | Koel, Ampache |
| **K7** | Middleware | OriginCheck, Session, CORS | PHP 8.4 PSR-15 |
| **K6** | Güvenlik | Auth, Session, CSRF, RBAC | JWT, AES-256 |
| **K5** | Veri Yönetimi | MySQL 9 BCNF, Redis, APCu | MySQL, Redis |
| **K4** | Yapay Zeka (AI) | Music Analysis, Recommendation | Python, ML |
| **K3** | Ses İşleme Motoru | Neva Engine, DSP, EQ, Crossover | JUCE, EasyEffects |
| **K2** | Sürücü | ASIO, WASAPI, ALSA, PipeWire, I2S | ASIO4ALL |
| **K1** | Donanım Altyapısı | XMOS, Class AB, Push-Pull DC-DC | KiCad OSHW |
| **K0** | İşletim Sistemi | Windows, Linux, macOS, RPi5 | Linux, ReactOS |

### Electronics Registry (K1 Alt Sistemleri)

| Document | Content | Date |
|----------|---------|------|
| `electronics/amplifier-classab-circuit.md` | Circuit design, schematic, BOM (single channel), bias procedure, test protocol | 2026-09-18 |
| `electronics/bom-classab.md` | Full 8-channel BOM: 1,018 components, ~$415 (1+), ~$293 (100+) | 2026-09-18 |
| `electronics/pcb-classab.md` | 6-layer PCB rules: stackup, impedance, thermal, placement, EMI | 2026-09-18 |
| `electronics/thermal-design-classab.md` | Thermal management, heatsink selection, fan control | 2026-09-18 |
| `electronics/power-supply-classab.md` | ±40V Push-Pull Boost (SG3525): 12V-24V DC giriş, merkez-uçlu trafo, Hi-Fi Filtre | 2026-09-18 |

### Kritik Bileşenler (K1 Donanım Alt Sistemleri)

| Kod | Bileşen | Kapsam | Teknoloji |
|-----|---------|--------|-----------|
| H1 | Class AB Amplifikatör | 50W/kanal, 8 kanal modüler, MJL21194/MJL21193 | C++20, STM32/RP2040 MCU |
| H2 | Güç Kaynağı | ±40V Push-Pull (SG3525), 12V-24V DC Giriş | Voltaj çökmesini önleyen Hi-Fi LC Filtre |
| H3 | Termal Tasarım | Fischer SK82-150-SA heatsink, Noctua NF-A8 fan | Sıcaklık kontrollü sessiz fan |

### H1: Class AB Amplifikatör (K1)

| Bileşen | Tanım |
|---------|-------|
| Q1,Q2 (BC546B) | Diferansiyel çift |
| Q5 (BC556B) | Akım havuzu |
| Q9 (KSC3503) | VAS transistor |
| Q10 (BD139) | Vbe çarpımı |
| Q15 (MJL21194) | Output NPN |
| Q17 (MJL21193) | Output PNP |
| SG3525/KA3525 | Push-Pull DC-DC kontrolcüsü (12V-24V -> ±40V) |

Bağımlılık: ✅ Katmanlar dışarıya çıkmadan içe doğru bağlanır (K15→K0). ✅ Katman atlanamaz. Layer Violation → derhal revert.

---

### §6 Middleware Pipeline (Sıra Değişmez — ADR-010/011/012/013/022)

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

### §8 Hardware

| Bileşen | Özellik |
|---------|---------|
| XMOS XU316 | USB Audio Class 2.0, zero-latency DSP |
| PCM3168A | 6-in/8-out codec, 24-bit, DAC 192kHz, ADC 96kHz, SNR 112dB (DAC) |
| AK4458 (opsiyonel) | 8-kanal high-end DAC, 32-bit, 768kHz |
| PCM5122 | ✅ REDDEDİLMİŞ — Sadece 2 kanal, 8.1 için yetersiz (H001) |
| Class AB Amp | 50W @ 8Ω, THD+N <0.01%, SNR >100dB, ±35V DC, MJL21194/MJL21193 output (ADR-089) |

ASIO Buffer: 512 sample varsayılan (64-1024), 48kHz, 32-bit float, ~10.67ms gecikme.

---

### §9 8.1 Surround

8 kanal + 1 LFE subwoofer. Kanallar: Front L/R (20Hz–20kHz), Center (100Hz–8kHz), Surround L/R (100Hz–16kHz), Rear L/R (100Hz–16kHz), Height L/R (200Hz–16kHz), Subwoofer LFE (20Hz–120Hz). Bass management: Linkwitz-Riley 4. nesil, crossover 80Hz.

---

### §11 18 BCNF Databases (ADR-040)

*Detaylı metadata için bakınız: [[architecture/index]] §3*

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

### §12 AI Auto-Download Pipeline

```
YouTube URL → nova-search-engine → deemix PHP port (Deezer FLAC) → 24/32-bit FLAC → coremusic_musics DB metadata
```

Anti-ban: Rate limiting, ARL token rotasyonu, proxy rotasyonu, User-Agent çeşitliliği. Kalite: FLAC 24/32-bit, MP3 320kbps fallback.

---

### §15 Platform Tiers

| Tier | OS | Durum |
|------|-----|-------|
| Tier 1 (Primary) | Windows (XP–11, Server 2012 R2+) | ✅ Ana geliştirme |
| Tier 2 | Linux (Ubuntu, Debian, Fedora, Arch) | ✅ Destekli |
| Tier 3 | macOS (Monterey–Sonoma) | ✅ Destekli |
| Tier 4 | Raspberry Pi (ARM64, Debian) | ✅ Destekli |
| Tier 5 | ReactOS | ⚠️ Experimental |

---

### §16 Audio Organization

| Division | Sorumluluk |
|----------|------------|
| Hardware Division | Özel audio kartları, DAC/ADC, DSP çipleri, amplifikatör |
| Software Division | C++ Audio Engine, DSP Engine, Mixer, sürücüler |
| Studio Division | ASIO, WASAPI, kayıt, monitoring, routing |
| Consumer Division | Bluetooth, WiFi Audio, müzik oynatma, ev ve araç ses |
| Research Division | AI DSP, yeni codec teknolojileri |

---

## Rules

### §3 Core Principles

| Prensip | Açıklama |
|----------|----------|
| SOLID | Tek Sorumluluk, Açık Kapalılık, Yerine Koyma, Arayüz Ayrımı, Bağımlılık Tersi |
| Clean Architecture (L0-L6) | Infrastructure → Security → Routing → Presentation → Domain → Services → Electronics |
| Hexagonal Architecture | Adapter/Port pattern ile bağımsızlık |
| DRY | Tekrarlanan kod yasağı |
| YAGNI | Gereksiz özellik ekleme yasağı |
| Real-Time Thread Model | Audio thread'de blocking operations yasak |
| Zero Code Before Plan | Plan onayı olmadan kod yazma yasağı (ADR-007) |

---

### §7 C++ Audio Rules

### §7.1 Zero-Allocation Kuralı

Real-time audio callback içerisinde ✅ yasak: `malloc()`, `free()`, `new`, `delete`, `std::make_shared`, `std::vector` push_back, I/O blocking, `throw`. ✅ İzin: Stack tahsisi, `std::atomic`, SIMD (SSE2/AVX2/NEON), `constexpr`, member değişkenler, `alignas(64)`.

### §7.2 ASIO Callback

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

### §7.3 Thread & Cache

- Audio thread: `THREAD_PRIORITY_TIME_CRITICAL`. Normal: `THREAD_PRIORITY_NORMAL`.
- writeHead/readHead: `alignas(64) std::atomic<size_t>` (false sharing önleme).

---

### §10 PHP Security

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

### §17 Hard Guardrails (14 Kural)

| # | Kural | İhlal Sonucu |
|---|-------|-------------|
| 1 | Zero-Allocation: Audio thread'de heap allocation yasak | Ses takılması / crash |
| 2 | Lock-Free: Audio thread'de mutex yasak | Deadlock |
| 3 | Layer Violation: L0 → L3 import yasak | Derhal revert |
| 4 | SELECT *: Açık sütun listesi zorunlu | SQL injection riski |
| 5 | Hardcoded Secret: API key/log'da yasak | Güvenlik ihlali |
| 6 | csrf_token: Key ismi değişmez (ADR-010) | CSRF bozulması |
| 7 | Zero Code Before Plan: Plan onayı olmadan kod yok | Mimari bozulma |
| 8 | Zero Hallucination: Doğrulanamayan bilgi → VERIFICATION REQUIRED (ADR-005) | İçerik silinir |
| 9 | In-Place Refactoring: Dosya adı/konumu değişmez | Link kırılması |
| 10 | ORM Yasak: Sadece PDO prepared (ADR-002) | SQL injection |
| 11 | Framework Yasak: Sadece Vanilla JS (ADR-001) | Bağımlılık artışı |
| 12 | Middleware Sırası: Değişmez (ADR-010/011/012/013/022) | CSP/CSRF bozulması |
| 13 | Port 81: music.coremusic.net PHP 8.4 | Servis çökmesi |
| 14 | PCM5122 Yasak: 8.1 surround için yetersiz (H001) | Yanlış donanım |

---

### §18 Coding Standards

| Dil | Kritik Kurallar |
|-----|-----------------|
| PHP | `declare(strict_types=1)`, PSR-12, constructor injection, PHP 8.4+ |
| JavaScript | Vanilla ES6+ (framework yasak), `const`/`let`, async/await, AbortController, `#` private, DOMParser+TrustedTypes, innerHTML yasak |
| C++ | C++20, noexcept (ASIO callback), constexpr (buffer), alignas(64), [[nodiscard]] |
| CSS | ITCSS 9-layer, BEM+BEMIT, custom properties, main.css sadece 01-07 |

---

### §18A Responsive CSS Architecture (a-layout-tokens.css v2.0.0)

| Özellik | Değer | Kaynak |
|---------|-------|--------|
| Token Konsolidasyonu | Tek dosyada (`a-layout-tokens.css`) tüm responsive breakpoint'ler | [[architecture/k11-ux]] |
| Default Viewport | 1024×600 (RPi5 embedded, mockup reference) | [[ui-design/01-mockup-index]] |
| Media Query Breakpoints | 4 adet: tablet (768-1024), mobile (≤767), desktop (≥1920), 4K TV (≥3840) | — |
| Token Kategorileri | Header/Footer heights, spacing, font scale, touch targets, glass blur, z-index | — |
| Device CSS Dönüşümü | `d-embedded.css`, `d-desktop.css`, `d-tablet.css` → sadece behavioral overrides (hover, touch, scrollbar) | — |

### Responsive CSS Mimarisi Kuralı (Zorunlu — Guardrail #17)

**45-Tier Device Matrix (v1.0.0 — 2026-09-20):**
- **Phone:** iPhone SE, iPhone 14, Samsung Galaxy S23
- **Embedded:** RPi5 7", RPi5 10"
- **Laptop:** 13" (1366×768), 15" (1920×1080), 17" (1920×1200)
- **Monitor:** 24" FHD, 27" QHD, 32" 4K, 34" Ultrawide
- **Ultrawide:** 34" (3440×1440), 38" (3840×1600), 49" (5120×1440)
- **TV:** 43" FHD, 55" 4K, 65" 4K, 77" 8K
- **Car:** Android Auto, CarPlay
- **Watch:** 40mm, 45mm, Ultra 49mm
- **Console:** PS5, Xbox Series X, Nintendo Switch, Steam Deck
- **Desktop App:** Electron, Tauri, PWA
- **AR/VR:** Meta Quest 3 (future)

**1024×600 PNG mockup = Design Reference (Kanonik SSOT)**
- Kanonik İndeks: [[ui-design/01-mockup-index]] (19 PNG: 12 home-1024 + 1 home-1920 + 6 shared-1024 — Faz 1 sayım düzeltmesi)
- Kanonik Bileşen Envanteri: [[ui-design/02-component-inventory]] (C01–C16 BEM ve piksel standartları)
- Kanonik ASCII Wireframe Haritası: [[ui-design/screens/00-ascii-art-index]] (Header 60px y:0-60, İçerik 450px y:60-510, Footer 90px y:510-600)
- 15 Adımlık CSS Uygulama Planı: [[ui-design/03-implementation-plan]]
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
| ✅ Yasak | ✅ Doğru |
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

### §18B 4-Tier Device Manager Sistemi (v3.0.0 — 2026-09-05)

**4-Tier Conditional Rendering** sistemi `DeviceManager.php` tarafından yönetilir. 7 cihaz türü, 4 layout tier'ı, 9 feature toggle ve cihaz bazlı nav link/content config içerir.

### Cihaz Türleri (7 Adet)

| Sabit | Değer | Tanım |
|-------|-------|-------|
| `EMBEDDED` | `'embedded'` | Raspberry Pi 5, 7" touchscreen, Linux ARM |
| `PHONE` | `'phone'` | Mobil telefon (≤767px) |
| `TABLET` | `'tablet'` | Tablet (768-1024px) |
| `LAPTOP` | `'laptop'` | Laptop (1025-1440px) |
| `DESKTOP` | `'desktop'` | Masaüstü (1441-2560px) |
| `FOUR_K_TV` | `'4k-tv'` | 4K Smart TV (≥3840px, TV User-Agent) |
| `FOUR_K_MON` | `'4k-monitor'` | 4K Monitör (≥2561px, Desktop OS) |

### 4 Tier Layout Sistemi

| Tier | Cihazlar | Viewport | Layout | Mockup |
|------|----------|----------|--------|--------|
| **Tier 1: Phone** | PHONE | ≤767px | Tek sütun, dikey scroll, kompakt kartlar | — |
| **Tier 2: Embedded** | EMBEDDED, TABLET | ≤1024px | 42/58 split, 2×2 widget, sidebar yok | Image 2 (1024px) |
| **Tier 3: Wide** | LAPTOP, DESKTOP | 1025-2560px | 3-sütun, tam widget, sidebar var | Image 3 (1920px) |
| **Tier 4: 4K** | FOUR_K_TV, FOUR_K_MON | ≥2561px | 4K ölçeklendirilmiş, büyük ekran | — |

### Tasarım Kararları

| Karar | Değer | Gerekçe |
|-------|-------|---------|
| Phone Layout | ≤767px viewport | Kompakt dokunmatik arayüz |
| Embedded Layout | EMBEDDED/TABLET veya viewport≤1024px | RPi5 optimized (ama phone hariç) |
| Wide Layout | 1025-2560px (phone, embedded, 4K hariç) | Standart masaüstü/laptop |
| 4K Layout | FOUR_K_TV/FOUR_K_MON veya viewport≥2561px | 4K TV/Monitör ölçeklendirme |
| Welcome Popup | YALNIZCA embedded 1024×600 (RPi5) | Karşılama ekranı |

### Viewport Bilgi Akışı

```
JS (device-loader.js)
  → Cookie: cm_viewport_w, cm_viewport_h (max-age=86400)
    → PHP (DeviceManager::fromRequest)
      → $_SERVER['VIEWPORT_W'] ?? $_COOKIE['cm_viewport_w'] ?? varsayılan
        → DeviceDetector::detect(UA, viewportW, viewportH) → device string
          → DeviceManager → 4-Tier karar metotları
```

### DeviceDetector Tespit Önceliği

```
1. HTTP Header: X-Device-Type: embedded     → 'embedded'
2. User-Agent: "Raspberry Pi" içeriği        → 'embedded'
3. User-Agent: "Tizen/webOS/SmartTV"         → '4k-tv'
4. Viewport: ≤767px                          → 'phone'
5. Viewport: 768-1024px + h≤600             → 'embedded'
6. Viewport: 768-1024px + h≥768             → 'laptop'
7. Viewport: ≤1440px                         → 'laptop'
8. Viewport: ≤2560px                         → 'desktop'
9. Viewport: ≤3840px + TV UA                → '4k-tv'
10. Viewport: ≤3840px + Desktop OS          → '4k-monitor'
11. Hiçbiri eşleşmezse                       → 'desktop' (varsayılan)
```

### DeviceManager Karar Metotları

```php
$dm = DeviceManager::fromRequest(
    viewportW: (int)($_SERVER['VIEWPORT_W'] ?? 0) ?: null,
    viewportH: (int)($_SERVER['VIEWPORT_H'] ?? 0) ?: null,
);

$isPhone    = $dm->isPhone();                        // ≤767px
$isEmbedded = $dm->shouldRenderEmbeddedLayout();      // Embedded/Tablet/viewport≤1024
$isWide     = $dm->shouldRenderWideLayout();          // 1025-2560px
$is4k       = $dm->shouldRender4kLayout();            // ≥2561px
```

### shouldRenderEmbeddedLayout() Mantığı

```php
public function shouldRenderEmbeddedLayout(): bool
{
    if ($this->isPhone()) return false;        // Phone her zaman hariç
    if ($this->isEmbedded() || $this->isTablet()) return true;
    if ($this->viewportW !== null && $this->viewportW <= 1024) return true;
    return false;
}
```

### shouldRenderWideLayout() Mantığı

```php
public function shouldRenderWideLayout(): bool
{
    if ($this->isPhone()) return false;
    if ($this->shouldRenderEmbeddedLayout()) return false;
    if ($this->shouldRender4kLayout()) return false;
    return true;   // 1025-2560px arası her şey
}
```

### shouldRender4kLayout() Mantığı

```php
public function shouldRender4kLayout(): bool
{
    if ($this->is4kTv() || $this->is4kMonitor()) return true;
    if ($this->viewportW !== null && $this->viewportW >= 2561) return true;
    return false;
}
```

### shouldShowFallback() — ARTIK HER ZAMAN FALSE

```php
public function shouldShowFallback(): bool
{
    return false;  // Tüm tier'lar optimize edildi, fallback kaldırıldı
}
```

### shouldRenderWelcomePopup() — SADECE RPi5

```php
public function shouldRenderWelcomePopup(): bool
{
    return $this->isEmbedded1024();  // Yalnızca 1024×600 gömülü cihazlar
}
```

### Feature Toggles (9 Adet)

| Metot | Mantık | Kullanım |
|-------|--------|----------|
| `showVolume()` | `!isEmbedded()` | Phone ve embedded hariç |
| `showFullMetadata()` | `!isEmbedded() && !isPhone()` | Sadece geniş ekranlar |
| `showSidebar()` | `isWide() \|\| isLaptop()` | Göz At sayfası |
| `showSeekBar()` | `true` | Tüm cihazlarda |
| `showPlaylistToggle()` | `!isPhone()` | Phone hariç |
| `showPodcastWidget()` | `isWide()` | Sadece geniş ekranlar |
| `showRadioWidget()` | `widgetCount() >= 5` | Widget sayısına bağlı |
| `showUtilityIcons()` | `!isPhone()` | Phone hariç (repeat, shuffle, EQ, vb.) |
| `showFooterSeekSlider()` | `!isPhone()` | Phone hariç |

### Nav Link'ler (Cihaz Bazlı)

| Cihaz | Nav Link Sayısı | Linkler |
|-------|----------------|---------|
| PHONE | 3 | Ana Sayfa, Kütüphane, Ayarlar |
| EMBEDDED | 4 | Ana Sayfa, Kütüphane, Radyo, Ayarlar |
| TABLET | 5 | Ana Sayfa, Keşfet, Albümler, Kütüphane, Ayarlar |
| LAPTOP | 8 | Ana Sayfa, Keşfet, Albümler, Sanatçılar, Göz At, Geçmiş, Ayarlar, Hakkımızda |
| DESKTOP | 8 | Ana Sayfa, Keşfet, Albümler, Sanatçılar, Göz At, Geçmiş, Ayarlar, Hakkımızda |
| FOUR_K_TV | 7 | Ana Sayfa, Keşfet, Albümler, Sanatçılar, Göz At, Geçmiş, Ayarlar |
| FOUR_K_MON | 8 | Ana Sayfa, Keşfet, Albümler, Sanatçılar, Göz At, Geçmiş, Ayarlar, Hakkımızda |

### İçerik Yapılandırması (Cihaz Bazlı)

| Cihaz | Widget | Recent Card | Playlist | Up Next |
|-------|--------|-------------|----------|---------|
| PHONE | 2 | 2 | 2 | 2 |
| TABLET | 4 | 4 | 3 | 3 |
| EMBEDDED | 4 | 3 | 3 | 3 |
| LAPTOP | 4 | 5 | 4 | 4 |
| DESKTOP | 6 | 7 | 5 | 6 |
| FOUR_K_TV | 6 | 8 | 6 | 8 |
| FOUR_K_MON | 6 | 8 | 6 | 8 |

### CSS Class Helpers

```php
$dm->layoutClass()     // "layout--desktop"
$dm->allClasses()      // "layout--desktop device--desktop is-wide"
$dm->dataAttributes()  // 'data-device="desktop" data-touch="false" data-wide="true" data-view-mode="home"'
```

### Test Sonuçları

| Viewport | Cihaz | Tier | Beklenen | Sonuç |
|----------|-------|------|----------|-------|
| 375×812 | Phone (iPhone) | Phone | Phone Layout | ✅ |
| 1024×600 | Embedded (RPi5) | Embedded | Embedded Layout + Welcome Popup | ✅ |
| 820×1180 | Tablet (iPad) | Embedded | Embedded Layout | ✅ |
| 1366×768 | Laptop (Windows) | Wide | Wide Layout | ✅ |
| 1920×1080 | Desktop (Windows) | Wide | Wide Layout | ✅ |
| 2560×1440 | Desktop (Windows) | Wide | Wide Layout | ✅ |
| 3840×2160 | 4K TV (webOS) | 4K | 4K Layout | ✅ |
| 3840×2160 | 4K Monitor (Windows) | 4K | 4K Layout | ✅ |
| 1024×600 | Desktop (Windows) | Embedded | Embedded Layout (viewport≤1024) | ✅ |

### Dosya Etki Alanı

| Dosya | Versiyon | Değişiklik |
|-------|----------|------------|
| `shared/src/Device/DeviceManager.php` | v1.0.0 | 7 cihaz, 4 tier, 9 toggle, nav links, content config |
| `shared/src/Device/DeviceDetector.php` | v1.0.0 | 11 tespit kuralı |
| `home.coremusic.net/pages/home.php` | v10.0.0 | 4 koşullu render (phone/embedded/wide/4k) |
| `home.coremusic.net/header.php` | v8.0.0 | Phone bottom nav + tier bazlı header |
| `home.coremusic.net/footer.php` | v11.0.0 | Phone compact player + tier bazlı footer |
| `shared/src/Device/DeviceCssMap.php` | — | 7 device CSS + 4 view mode CSS |
| `assets.coremusic.net/js/device-loader.js` | — | Cookie yazma + client-side tespit |
| `shared/src/PageRouter/HtmlShellRenderer.php` | — | Auth route branching (6 if bloğu) |
| `shared/src/PageRouter/PageRouter.php` | — | Cookie okuma |

Detay: [[ui-design/05-responsive-architecture]] v3.0.0, [[architecture/conditional-rendering-php-guide]] v2.0.0

---

### §18C Device-Aware Rendering Kuralları (v1.0.0 — 2026-09-05)

**Cihaz duyarlı render kuralları, backend ve frontend arasındaki sorumluluk sınırlarını tanımlar.** Bu kurallar Guardrail #17 ile uyumludur ve `responsive-device-mode.md` v3.0.0'e referansla çalışır.

### Tek Bileşen (Single Component) İlkesi

| Kural | Açıklama | İhlal Sonucu |
|-------|----------|--------------|
| Tek HTML yapısı | `home.php`, `header.php`, `footer.php` tek dosya olarak kalır | Kod revert edilir |
| Ayrı dosya yasağı | `home-1024.php`, `home-desktop.html` vb. KESİNLİKLE YASAKTIR | Kod revert edilir + CRITICAL log |
| Davranışsal fark CSS'te | Cihaz farkları CSS/konfigürasyon katmanında yönetilir | Layer violation |
| PHP'de sunum kararı yok | PHP tarafında `margin`, `padding`, `width`, `height`, `font-size` kodlanamaz | Kod revert edilir |

### Backend Sorumluluk Sınırları (PHP — L2/L3)

PHP tarafında yalnızca **davranışsal konfigürasyonlar** yönetilir:

| Sorumluluk | Örnek | PHP Metodu |
|------------|-------|------------|
| Widget sayısı | Embedded: 4, Desktop: 6 | `$dm->widgetCount()` |
| Widget görünürlüğü | Podcast/Radio sadece geniş ekran | `$dm->showPodcastWidget()` |
| Liste kart sayısı | Recent: 3-8, Playlist: 0-6, UpNext: 1-8 | `$dm->recentCardCount()` |
| Meta veri anahtarları | Tam metadata sadece geniş ekran | `$dm->showFullMetadata()` |
| Ses/seekbar görünürlüğü | Volume phone'da gizli | `$dm->showVolume()` |
| Navigasyon link sayısı | Phone: 3, Desktop: 8 | `$dm->navLinks()` |
| CSS sınıfı atama | `layout--embedded`, `device--phone` | `$dm->allClasses()` |
| Veri niteliği | `data-device="desktop"` | `$dm->dataAttributes()` |

**Yasak PHP Kodları (Sunum Kararları):**

```php
// ✅ YASAK — Sunum kararı PHP'de
$dm->isPhone() ? 'padding: 8px' : 'padding: 16px';
$dm->isEmbedded() ? 'font-size: 14px' : 'font-size: 16px';
echo '<div style="width: ' . ($dm->is4k() ? '800px' : '400px') . '">';

// ✅ DOĞRU — Davranışsal karar PHP'de
if ($dm->showVolume()) { /* volume HTML */ }
$cssClass = $dm->layoutClass(); // "layout--embedded"
```

### Frontend Sorumluluk Sınırları (CSS — L3)

CSS tarafında **tüm sunum kararları** yönetilir:

| Sorumluluk | Dosya | İçerik |
|------------|-------|--------|
| Token tanımları | `a-layout-tokens.css` | `--header-h`, `--footer-h`, `--content-h` |
| Token override | `a-layout-tokens.css` media query | Cihaza göre token değerleri |
| Behavioral override | `08_Devices/d-{device}.css` | Hover, touch, scrollbar kuralları |
| Layout grid | `05_Pages/_home-layout.css` | Grid template, gap, split |
| Component yerleşimi | `05_Pages/_home-components.css` | Bileşen boyutu, konumu |

### Cihaz Bazlı Token Değerleri (Referans)

| Token | Embedded (1024) | Wide (1920) | 4K (3840) | Phone (≤767) |
|-------|-----------------|-------------|-----------|--------------|
| `--header-h` | 60px | 70px | 80px | — (bottom tab) |
| `--footer-h` | 90px | 104px | 120px | 80px (kompakt) |
| `--content-h` | 450px | ~906px | ~1960px | — (full scroll) |
| `--home-top-split` | 42% 58% | 1fr 1.2fr 1fr | 1fr 1.2fr 1fr | tek sütun |
| `--widget-grid-cols` | 2 | 3 | 3 | 1 |

### WCAG 2.2 AA Zorunlulukları (Cihaz Bazlı)

| Cihaz | Touch Target | Focus Visible | Contrast |
|-------|-------------|---------------|----------|
| Phone | min 48×48px | `:focus-visible` outline | 4.5:1 |
| Embedded | min 48×48px | `:focus-visible` outline | 4.5:1 |
| Wide | min 24×24px | `:focus-visible` outline | 4.5:1 |
| 4K | min 24×24px (ölçekli) | `:focus-visible` outline | 4.5:1 |

### Katman İhlal Kontrolü

```
L3 (Presentation) → L2 (Routing): ✅ İzinli (PageRouter çağrısı)
L3 (Presentation) → L0 (Infrastructure): ✅ YASAK (PDO, SQL, Repository)
L2 (Routing) → L0 (Infrastructure): ✅ YASAK (Controller→Repository direkt)
```

**İhlal Durumunda:** Derhal revert + `log.md`'ye CRITICAL giriş.

---

## Workflow

### §14 Development Strategy

| Faz | Hedef | Donanım | Süre |
|-----|-------|---------|------|
| Faz 1 — MVP | Mevcut PC/laptop'da temel platform | Mevcut ses kartları (WASAPI/ASIO) | 6–12 ay |
| Faz 2 — Premium | CoreMusic Audio donanım entegrasyonu | PCM3168A, AK4458, XMOS XU316, Class AB | 12–24 ay |
| Faz 3 — Professional | Tam entegre stüdyo ve araç içi | 8.1 surround, multi-room, NAS | 24–36 ay |

---

### §22 Prompt Arşivi

Archives dizinindeki 4 ana prompt dosyası. Bu dosyalar vault'un parçasıdır ve her oturumun başında okunmalıdır.

| Prompt | Amaç | Kullanım | Konum |
|--------|------|----------|-------|
| `prompt0-genel-ana-prompt` | Ana genel prompt: 11 alt domain, 10 panel, 20 analiz görevi, zorunlu kurallar | Tüm agentlar, her analiz görevinde | [[archives/prompt0-genel-ana-prompt-2026-09-01]] |
| `prompt1-spa-router` | SPA Router: Enterprise router, SOLID, PSR, attribute-based, DI, route-cache, subdomain-aware | Backend Architect, UI Designer | [[archives/prompt1-spa-router-2026-09-01]] |
| `prompt2-auth` | Auth: Merkezi auth.coremusic.net, hybrid JWT+session, RBAC, middleware pipeline, CORS | Security Engineer, Backend Architect | [[archives/prompt2-auth-2026-09-01]] |
| `prompt3-api` | API: API-First, Gateway, CQRS, Event Driven, 14 servis, coremusic-shared | Backend Architect, DevOps Engineer | [[archives/prompt3-api-2026-09-01]] |

### §22.1 Prompt-Article Eşleşme Tablosu

| Prompt İçeriği | Vault'daki Karşılığı | ADR |
|----------------|----------------------|-----|
| prompt0: 11 alt domain | brain.md § 4A (Composer Stack) | ADR-087 |
| prompt0: 10 panel | brain.md § 9 (Paneller) | ADR-039 |
| prompt0: 20 analiz görevi | WORKFLOW.md genişletilmiş prompt bölümü | ADR-042 |
| prompt0: Zorunlu Kurallar | CLAUDE.md § 7 (Hard Guardrails) | ADR-007 |
| prompt1: Enterprise Router | architecture/l2-routing/spa-router.md § 1A | ADR-083 |
| prompt2: Central Auth | architecture/k6-k7-security/k06-auth-layer/auth-cross-domain.md, ADR-043 | ADR-043 |
| prompt2: Middleware Pipeline | brain.md § 6 | ADR-010/011/012/013/022 |
| prompt3: API Gateway | ADR-084 (API Gateway Architecture) | ADR-084 |
| prompt3: CQRS | brain.md § 4B (API Architecture) | ADR-086 |
| prompt3: Event Driven | ADR-086 (Event Driven Architecture) | ADR-086 |

---

## Validation

### §19 Edge Cases

| Edge Case | Tetikleyici | Çözüm | ADR |
|-----------|-------------|-------|-----|
| ASIO Device Loss | USB kopması | WASAPI fallback → Null Output | [[ADR-017-dsp-hardware-mode]] |
| Cache Stampede | Yüksek load | Mutex ile single load | [[architecture/k0-isletim-sistemi]] |
| Multi-Tab CSRF | Birden fazla sekme | Token session-bound sabit | [[ADR-010-csrf-protection-strategy]] |
| Layer Violation | L0 → L3 import | Derhal revert | [[CLAUDE.md]] |
| PCM5122 Kullanımı | 8.1 surround denemesi | PCM3168A veya AK4458 | [[brain.md]] ADR-038-8.1-sound-card-chip-selection |
| Network Outage | İnternet kopması | Offline-First + SQLite queue | [[architecture/index]] |
| BCNF Violation | Yeni tablo | 3NF → BCNF audit | [[brain.md]] ADR-040-database-authority |
| Buffer Underrun | CPU %100 | Fade-out → 50ms sessizlik → restart | [[engine.md]] |
| Session Timeout | 3600s idle | Otomatik yeniden auth | [[ADR-011-session-management]] |

---

### §20 Warnings

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

### §23 Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 26.1.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 8 (H1 + 7 English H2 skeleton) |
| ADR Coverage | 001–089 (80 karar: 37 Frozen + 30 Active + 12 Rejected + 1 Draft) |
| Panel Count | 10 (hedef — fiziksel: auth + home + assets; Faz 0) |
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
| Class AB Amplifikatör | K16-K18: 50W/kanal, 8 kanal, MJL21194/MJL21193, ±35V boost, 800W (ADR-089 Draft) |

---

### §24 Doküman İskeleti (8-Bölüm Uyumu — Vault Refactor Engine 2026-09-23)

> **Not:** v25.0.0 → v26.0.0; ADR-042 hibrit ile satır-edit + ekleme (silme yok, §1-§23 korundu). Mojibake temizliği Faz 1'de vault geneli yapıldı (89 düzeltme bu dosyada).

### §24.1 İskelet Eşlemesi

| İskelet Bölümü (H2) | Karşılık Gelen § |
|----------------------|------------------|
| Başlık | H1 + frontmatter (7 zorunlu alan) |
| Purpose | §1 Amaç & Ekosistem Misyonu |
| Scope | §2 Scope |
| Architecture | §4 Tech Stack (§4A, §4B) + §5 K0-K20 + §6 Middleware + §8 Hardware + §9 8.1 Surround + §11 18 BCNF + §12 AI Pipeline + §15 Platform Tiers + §16 Audio Organization |
| Rules | §3 Core Principles + §7 C++ Audio + §10 PHP Security + §17 Hard Guardrails + §18 Coding Standards + §18A + §18B + §18C |
| Workflow | §14 Development Strategy + §22 Prompt Arşivi + [[WORKFLOW.md]] |
| Validation | §19 Edge Cases + §20 Warnings + §23 Quality Report + §24 (bu bölüm) |
| References | §13 ADR Summary + §21 Cross References + PDF Freelancer Mimari Karşılıkları |

### §24.2 Faz 2 Doğrulama (2026-09-23)

- [x] Frontmatter 7 alan tam; version 26.0.0; updated 2026-09-23
- [x] Frozen ADR dokunulmaz (§13 ADR Summary salt-okunur referans)
- [x] §1-§23 korundu, silme yok; yeni bölüm §24 olarak eklendi
- [x] REFACTOR REPORT: FILE: brain.md · PURPOSE: Engineering decisions SSOT · VALIDATION: § + wiki-link korundu · RELATED: [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[MEMORY.md]] · [[index.md]] · [[log.md]]

### §24.3 İlgili Dosyalar

[[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[MEMORY.md]] · [[index.md]] · [[keys.md]] · [[log.md]] · [[.templates/index]]

### §24.4 Faz 2-3 İskelet Yeniden Düzenleme (2026-09-23)

- [x] 7 İngilizce H2 iskeleti uygulandı: `## Purpose / ## Scope / ## Architecture / ## Rules / ## Workflow / ## Validation / ## References`
- [x] Eski numaralı H2 bölümleri `### §N` H3 başlığına dönüştürüldü; § numaraları harfiyen korundu (dış cross-ref'ler: §13, §18A, §18B, §18C, §21, §22)
- [x] İçerik taşındı, silinmedi; §24.1 eşlemesi güncel grubu gösterir
- [x] version 26.0.0 → 26.1.0; updated 2026-09-23

---

## References

### §13 ADR Summary

### §13.1 Frozen (001-037)

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

### §13.2 Active (038-088)

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
| ADR-088 | Gender-Based Social OAuth (cinsiyet bazlı sosyal medya bağlantıları) |
| ADR-089 | Class AB Amplifikatör + 6S LiPo + ±35V Boost (Draft — 50W/kanal, MJL21194/MJL21193) |

---

### §21 Cross References

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § Amaç | [[CLAUDE.md]] | Ana sözleşme |
| § Mimari | [[architecture/index]] | L0-L6 |
| § C++ Audio | [[ADR-017-dsp-hardware-mode]] | XMOS, JUCE |
| § 8.1 Surround | [[brain.md]] ADR-038-8.1-sound-card-chip-selection | PCM3168A, H001 |
| § PHP Middleware | [[ADR-010-csrf-protection-strategy]] | csrf_token |
| § Cache/Vault | [[ADR-022-database-hardened-security]] | AES-256-GCM |
| § 18 BCNF DB | [[brain.md]] ADR-040-database-authority | 18 DB |
| § Audio Org | **DOĞRULAMA GEREKLİ** — `electronic/audio-organization.md` vault'ta yok (Faz 1) | 5 bölüm |
| § Hardware | **DOĞRULAMA GEREKLİ** — `electronic/hardware-roadmap.md` vault'ta yok (Faz 1) | 3 fazlı yol haritası |
| § 22 (Prompt Arsivi) | [[architecture/ai/prompt-engine]] | Prompt üretim motoru |
| § 22 (Prompt Arsivi) | [[CLAUDE#26-prompt-entegrasyonu]] | Boot protokolünde prompt entegrasyonu |
| § UI Design | [[ui-design/01-mockup-index]] | Mockup indeksi — 19 PNG |
| § Mockup PNG'ler | `.ai/.png/home-1024/` (12) + `.ai/.png/home-1920/` (1) + `.ai/.png/shared-1024/` (6) | 19 PNG mockup |

---

### PDF Freelancer Teknik Dokümantasyon v1.0 — Mimari Karşılıkları

### §02 Sistem Mimarisi Eşleştirme

| PDF Katman | Mevcut Karşılık | Durum |
|------------|-----------------|-------|
| L0 Altyapı | K0 İşletim Sistemi + K5 Veri Yönetimi | ✅ |
| L1 Güvenlik | K6 Güvenlik + K7 Middleware | ✅ |
| L2 Servis | K8 Servis + K9 API & Routing | ✅ |
| L3 Uygulama | K10 Uygulama + K11 UX | ✅ |
| 01 Kullanıcı Deneyimi | K11 UX | ✅ |
| 02 Uygulama Servis | K8 Servis | ✅ |
| 03 Native Ses İşleme | K3 Ses Motoru | ✅ |
| 04 Yapay Zeka | K4 Yapay Zeka | ✅ |
| 05 Alan (Domain) | K8 Domain | ✅ |
| 06 Veri Yönetimi | K5 Veri | ✅ |
| 07 Altyapı ve Bulut | K0 OS | ✅ |

### PDF Mimari Kararları

| Karar | Açıklama |
|-------|----------|
| Hibrit Mimari | Online Cloud + Offline-First çalışma |
| Modüler Servis | 7 bağımsız backend servisi |
| 18 BCNF Veritabanı | İlişkisel veri yönetimi |
| 10 Uzmanlık Paneli | 10 alt domain (music, admin, home, car, studio, download, landing, pro, media, auth) |
| 11 Agent Sistemi | AI destekli otomasyon |
| API-First | OpenAPI sözleşmesi ile tüm endpoint'ler |

### İlgili Referanslar
- [[architecture/index]] — Mimari indeks
- [[../CLAUDE.md]] — Anayasa
- [[VISION]] — Vizyon

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-23
**Mode:** Red Team · Human Mode · Truth Mode
