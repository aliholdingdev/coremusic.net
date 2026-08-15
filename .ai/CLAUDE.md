---
type: guide
category: ai-mandate
title: "CoreMusic — AI Constitution & Master Vault Mandate"
date: 2026-08-08
updated: 2026-08-13
status: active
version: 22.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
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
        purpose: "Architecture Decision Record şablonu"
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
changelog:
  - version: 22.0.0
    date: 2026-08-13
    changes:
      - Added reference section (skills, templates, project_structure)
      - Updated governance format
---

# CoreMusic — AI Constitution & Master Vault Mandate

**Zorunlu Bağlantılar:** [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]] · [[engine.md]] · [[.templates/index]] · [[.agents/AGENTS.md]]

**Skills:** `.opencode/skills/` (10 skill — Guardrail #16 zorunlu)

---

## 1. Amaç

CoreMusic, bireysel kullanıcılar, profesyonel müzik üreticileri, stüdyolar, araç içi bilgi-eğlence ve ev medya merkezleri için tasarlanmış kurumsal seviyede **dijital medya yönetim platformudur.** Bu dosya, tüm AI ajanlarının ve mühendislerin mutlaka uyması gereken anayasal sözleşmedir.

Bu belge tek başına yeterli bilgi içermelidir. Başka bir AI sistemi, yalnızca bu dosyayı okuyarak CoreMusic'in temel kurallarını, yasaklarını ve çalışma prensiplerini tam olarak anlamalıdır.

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Tüm AI ajanları (Claude, Gemini, Cursor, RooCode, OpenCode) | İnsan mühendislerin özel tercihleri |
| Tüm mühendisler ve geliştiriciler | Üçüncü taraf servislerin iç kuralları |
| Tüm servisler ve paneller | Donanım üretimi süreçleri |
| Vault (.ai/) ve tüm dokümantasyon | Kaynak kodu iç detayları |

---

## 3. Terminoloji

| Terim | Tanım |
|-------|-------|
| **SSOT** | Single Source of Truth — Tek Doğruluk Kaynağı. Tüm bilgiler `.ai/` vault'tan okunur. |
| **ADR** | Architecture Decision Record — Mimari karar kaydı. Frozen (001-037) ve Active (038-087) olmak üzere iki türdür. |
| **Hard Gate** | Kullanıcı onayı olmadan geçilemeyen kritik faz geçiş noktası. |
| **Zero Code Before Plan** | Plan onayı olmadan kod yazma yasağı. |
| **Zero Hallucination** | Doğrulanamayan bilginin `VERIFICATION REQUIRED` olarak işaretlenmesi. |
| **Layer Violation** | Mimari katman bağımlılık kurallarının ihlali. |
| **CSRF** | Cross-Site Request Forgery — Token key: `csrf_token` (NOT `_csrf_token`). |
| **CSP** | Content Security Policy — nonce-based, strict-dynamic. |
| **BCNF** | Boyce-Codd Normal Form — 18 BCNF veritabanı için zorunlu normalizasyon. |
| **RBAC** | Role-Based Access Control — Rol bazlı erişim kontrolü. |
| **OWASP** | Open Web Application Security Project — Güvenlik standartları. |
| **ASIO** | Audio Stream Input/Output — Düşük gecikmeli ses protokolü. |
| **WASAPI** | Windows Audio Session API — Windows ses oturum yönetimi. |
| **DSP** | Digital Signal Processing — Dijital sinyal işleme. |
| **FLAC** | Free Lossless Audio Codec — Kayıpsız ses formatı. |
| **PCM** | Pulse-Code Modulation — Ham ses verisi formatı. |
| **LFE** | Low Frequency Effects — Subwoofer kanalı (8.1 surround). |

---

## 4. Sistem Tanımı

CoreMusic; araçta, evde ve profesyonel stüdyoda müzik dinlemek, müzik açmak ve müzik yönetmek için tasarlanmış, otomatik indirme yeteneğine sahip bir medya platformudur.

### 4.1 Platform Tanımı

| Özellik | Değer |
|---------|-------|
| Platform Adı | CoreMusic |
| Platform Türü | Dijital Medya Yönetim Platformu |
| Hedef Kullanıcılar | Bireysel, Profesyonel, Stüdyo, Araç İçi, Ev Medya |
| Temel Teknoloji | PHP 8.4, C++20, Vanilla JS, MySQL 9 |
| Lisans | Kapalı Kaynak |
| Versiyon | 19.0.0 |

### 4.2 Sistem Yetenekleri

CoreMusic yalnızca bir medya oynatıcı değildir. Sistem şu yeteneklere sahiptir:

- Müzik indirme (Otomatik & Manuel)
- Müzik yönetimi (Kütüphane, Albüm, Sanatçı)
- Medya arşivleme (Metadata, Kapak Görselleri)
- Profesyonel ses yönetimi (ASIO, WASAPI, DSP)
- Ev medya merkezi (NAS, Multi-Room)
- Araç içi bilgi-eğlence (Car Infotainment)
- Stüdyo ses sistemi (8.1 Surround, 8x8 I/O)
- NAS medya yönetimi
- AI destekli müzik öneri sistemi
- Çoklu cihaz senkronizasyonu
- Offline First medya platformu
- Streaming altyapısı
- ASIO 32-bit ses desteği
- AI ile otomatik EQ/DSP yönetimi

---

## 5. Mimari — L0-L6 Katmanları

*Detaylı metadata için bakınız: [[architecture/00-overview/architecture-master]] §2*

Bağımlılık kuralları: ✅ L6→L5, L5→L4, L4→L3, L3→L2, L2→L1, L1→L0 | ❌ L0→L2/L3, L1→L3, L3→L0

| Katman | Kapsam | Teknolojiler |
|--------|--------|-------------|
| **L3 Presentation** | Frontend, UI, DOM | Vanilla JS ES6+, ITCSS 9-layer, TrustedTypes, DOMParser |
| **L2 Routing** | SPA router, middleware | PHP 8.4 PageRouter, JS Router.js |
| **L1 Security** | Session, Auth, CSRF, CSP | Middleware pipeline, Argon2id, AES-256-GCM |
| **L0 Infrastructure** | Database, cache, filesystem | PDO MySQL, APCu, Redis, shared memory |

### 5.1 Katman Bağımlılık Matrisi

| Kaynak → Hedef | İzinli mi? |
|-----------------|------------|
| L3 → L2 | ✅ Evet |
| L2 → L1 | ✅ Evet |
| L1 → L0 | ✅ Evet |
| L0 → L2/L3 | ❌ Hayır (Layer Violation) |
| L1 → L3 | ❌ Hayır (Layer Violation) |
| L3 → L0 | ❌ Hayır (Layer Violation) |

**Layer Violation İhlali:** Tespit edilirse derhal revert + log CRITICAL.

---

## 6. Middleware Pipeline (Immutable — ADR-010/011/012/013/022)

```
OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf → BypassAuth → Auth → Permission → Validation → Controller
```

| # | Middleware | Görev | Timeout |
|---|-----------|-------|---------|
| 1 | **OriginCheck** | Köken doğrulama (whitelist CORS) | — |
| 2 | **Cors** | CORS header yönetimi | — |
| 3 | **RateLimiter** | APCu tabanlı, 60 req/60s | 60s |
| 4 | **SecurityHeaders** | CSP strict-dynamic, X-Frame-Options, HSTS | — |
| 5 | **SessionManager** | Session başlatır, CSP nonce üretir | 3600s idle |
| 6 | **Csrf** | `csrf_token` doğrulama (POST/PUT/DELETE) | — |
| 7 | **BypassAuth** | Test bypass (`?_bypass=1`), prod'da devre dışı | — |
| 8 | **Auth** | Auth bilgisi inject (JWT + Session) | — |
| 9 | **Permission** | RBAC yetki kontrolü (regular/premium/studio/car/admin/system) | — |
| 10 | **Validation** | Request/DTO validasyonu | — |

**Kritik Not:** CSP nonce üretimi SessionManager içindedir. Sıra değiştirilirse CSP bozulur. Middleware sırası **DEĞİŞTİRİLEMEZ**.

---

## 6A. API-First Mimari (ADR-084)

CoreMusic'te **hiçbir endpoint doğrudan kodlanmaz.** Önce OpenAPI sözleşmesi hazırlanır.

```
OpenAPI Spec → DTO → Contract → Validation → Use Case → Kod
```

### API Gateway

Tüm istemcilerin tek giriş noktası `api.coremusic.net`'tir. Gateway; routing, auth, rate limit, validation, logging, correlation ID görevini üstlenir.

### BFF (Backend for Frontend)

Her istemci tipi kendi BFF'sini kullanır:

| İstemci | BFF | Response |
|---------|-----|----------|
| SPA | SPA BFF | Tam veri |
| Mobile | Mobile BFF | Minimal |
| Embedded (RPi5) | Embedded BFF | Ultra-minimal, gzip |
| Desktop | Desktop BFF | Orta boy |
| Admin | Admin BFF | Full + audit |
| Car | Car BFF | Touch-optimized |

### CQRS

Yazma ve okuma işlemleri tamamen ayrılır:

```
Write: Command → Use Case → Repository → MySQL Master
Read:  Query → Read Model → Cache → Response
```

### Event Driven (ADR-086)

Servisler birbirini doğrudan çağırmaz, event yayınlar:

```
Service A → Event Bus (PSR-14) → Service B, C, D
```

### SPA → ApiClient Kuralı

```
SPA → ApiClient → HTTP → Gateway → Middleware → Use Case → Domain → Repository → Infrastructure
```

SPA **asla** PDO, MySQL, Repository, Entity, Infrastructure, Filesystem, FFmpeg, Redis, Cache veya SQL **görmez.**

---

## 6B. Modüler Composer Paketleri (ADR-085)

Tek monolitik `coremusic-shared` paketi **YASAK.** Yerine 22 bağımsız `coremusic/*` paketi kullanılır:

```
coremusic/contracts    ← TEMEL (bağımsız)
coremusic/http         ← PSR-7/17/18
coremusic/auth         ← Auth Client, JWT
coremusic/security     ← CSRF, RateLimiter, CSP
coremusic/cache        ← Redis, APCu, File
coremusic/events       ← PSR-14 Event Dispatcher
coremusic/validation   ← Request/DTO validation
coremusic/storage      ← Flysystem abstraction
coremusic/logger       ← PSR-3 Monolog
coremusic/sdk          ← Client SDK
coremusic/api-client   ← Typed API Client
coremusic/queue        ← Message queue
coremusic/websocket    ← WebSocket client/server
... (22 toplam)
```

**Kural:** Circular dependency yasak. `coremusic/contracts` hiçbir pakete bağımlı değildir.

---

## 7. Hard Guardrails (16 Kural)

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | Zero Code Before Plan | Plan onayı olmadan kod yok | Kod revert edilir |
| 2 | Vault First | Kod yazmadan önce AI vault'u oku (§5 boot protokolü) | Kod geçersiz |
| 3 | Zero Hallucination | Doğrulanamayan bilgi → `VERIFICATION REQUIRED` | İçerik silinir |
| 4 | In-Place Refactoring | Dosya adı/yolu değişmez | Dosya geri yüklenir |
| 5 | Single Source of Truth | Bilgi sadece `.ai/` vault'tan | Harici bilgi reddedilir |
| 6 | CSRF Token = `csrf_token` | `_csrf_token` yasak (2026-05-30) | Token reddedilir |
| 7 | Middleware Order Immutable | Sıra değişmez | Sistem durdurulur |
| 8 | Port 81 = music.coremusic.net | PHP 8.4 | Yanlış port yasak |
| 9 | No ORM | Raw PDO only (ADR-002) | ORM kullanımı reddedilir |
| 10 | No Frameworks | Vanilla JS + ITCSS (ADR-001) | Framework reddedilir |
| 11 | Mockup Before Frontend | Frontend görevinde mockup okunmadan kod yazılamaz | Kod revert edilir |
| 12 | Contradiction Gate | Vault'ta çelişki varsa kullanıcıya sor, onay bekle | İşlem durur |
| 13 | Session Continuity | Her oturum başlangıcında geçmiş session'dan devam et | Bağlam kaybolur |
| 14 | Human Approval Gate | Mimari karar öncesi kullanıcı onayı zorunlu | Kod revert edilir |
| 15 | Vault-First Mandatory | AI, .ai/ vault'unu (CLAUDE.md + AGENTS.md + WORKFLOW.md + brain.md + ROLE.md) OKUMADAN hiçbir plan/kod/faaliyet başlatamaz | İşlem derhal durdurulur + revert |
| 16 | Template Mandatory | Yeni dosya oluşturulurken `.ai/.templates/index.md`'den uygun template seçilmek ZORUNLU | Dosya geçersiz |

---

## 7A. Hibrit Kodlama Modeli (İnsan + Yapay Zeka)

Bu proje hem insan hem yapay zeka tarafından kodlanmaktadır. Aşağıdaki kurallar her iki taraf için de zorunludur.

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | **Vault-First Mandatory** | AI, vault'u okumadan kod yazamaz. Okuma sırası: CLAUDE.md → AGENTS.md → WORKFLOW.md → brain.md → ROLE.md → ilgili ADR'ler |
| 2 | **Çelişki Durumu** | Vault'ta çelişki varsa DUR ve kullanıcıya sor. Onay alınmadan hiçbir işlem yapılmaz |
| 3 | **Onay Zorunlu** | Mimari karar, yeni dosya, büyük değişiklik öncesi kullanıcı onayı zorunlu |
| 4 | **Session Continuity** | Her oturum başında geçmiş session'dan devam et. `log.md` ve `MEMORY.md` okunur |
| 5 | **Prompt Uyumu** | Verilen promptlar birebir uygulanır. Kafadan ekleme, değiştirme veya yorumlama yapılmaz |
| 6 | **İnsan Kodlaması** | İnsan kodladığında da aynı kurallar geçerli. Vault referansları kullanılabilir |
| 7 | **AI Kodlaması** | AI kodladığında template zorunlu, plan zorunlu, vault-okuma zorunlu |
| 8 | **Tutarlılık** | İnsan ve AI arasında kod tutarlılığı sağlamak için aynı standartlar kullanılır |

---

## 8. Soft Constraints (4 Kural)

| # | Kural | Esnetme Koşulu | Onay |
|---|-------|----------------|------|
| 1 | %80 test coverage | Geçici %75, teknik borç kabulü | Tech Lead |
| 2 | 30s timeout | Uzun batch işlemi (60s'e kadar) | Tech Lead |
| 4 | BypassAuth devre dışı | Test ortamında aktif edilebilir | Security Engineer |

---

## 9. Servis Haritası — 10 Panel

| # | Panel | Subdomain | Port | Stack | Durum |
|---|-------|-----------|------|-------|-------|
| 1 | Landing | `coremusic.net` | 80 | Vanilla JS | ✅ |
| 2 | Music | `music.coremusic.net` | 81 | PHP 8.4 + JS | ✅ Ana medya |
| 3 | Admin | `admin.coremusic.net` | 80 | PHP 8.4 | ✅ Yönetim |
| 4 | Download | `download.coremusic.net` | 3001 | Node.js + TS | ✅ İndirme |
| 5 | Media | `media.coremusic.net` | 5000/6000 | PHP + FFmpeg | ✅ Medya |
| 6 | Auth | `auth.coremusic.net` | — | PHP 8.4 | ✅ Kimlik |
| 7 | Home | `home.coremusic.net` | 81 | Vanilla JS | ✅ Ev merkezi |
| 8 | Car | `car.coremusic.net` | — | Vanilla JS | ✅ Araç içi |
| 9 | Studio | `studio.coremusic.net` | 81 | Vanilla JS | ✅ Stüdyo |
| 10 | Pro | `pro.coremusic.net` | 81 | Vanilla JS | ✅ Profesyonel |

**Görünüm Modları:** Home, Pro, Studio — her panel için geçerli.

---

## 10. Servis Haritası — 7 Backend Servis

| # | Servis | Port | Protocol | Stack | Sorumluluk |
|---|--------|------|----------|-------|------------|
| 1 | Control Service | 81 | HTTP | PHP 8.4 | Auth, session, RBAC |
| 2 | Media Service | 5000/6000 | HTTP | PHP + FFmpeg | Library, metadata, streaming |
| 3 | Audio Service | 9741/9742 | REST/WS | C++20 JUCE | Player, DSP, mixer, EQ |
| 4 | Device Service | — | BLE/WiFi/USB | C++20 | Bluetooth, WiFi, USB |
| 5 | Network Audio | — | WebRTC/P2P | C++20 | Streaming, multi-room |
| 6 | AI Service | — | Internal | PHP + Python | Recommendations |
| 7 | Download Service | 3001 | HTTP/WS | Node.js + TS | Deezer/YouTube indirme |

---

## 11. Port Kaydı

| Port | Servis | Protokol |
|------|--------|----------|
| 80 | admin.coremusic.net | HTTP |
| 81 | music.coremusic.net (Control) | HTTP |
| 3001 | download.coremusic.net | HTTP/WS |
| 3306 | MySQL 18 BCNF DB | TCP |
| 5000/6000 | media.coremusic.net | HTTP |
| 9741 | Audio Service (REST) | HTTP |
| 9742 | Audio Service (WebSocket) | WS |

---

## 12. Teknoloji Yığını

| Katman | Teknoloji | Versiyon |
|--------|-----------|---------|
| Backend | PHP (strict_types) | 8.4+ |
| Frontend | Vanilla JS ES6+ | ES2022 |
| CSS | ITCSS + BEM | 7-layer |
| Database | MySQL / MariaDB (PDO) | 18 BCNF |
| Audio Engine | C++20, JUCE 9, ASIO SDK | 2.3.4 |
| Hardware | XMOS XU316, PCM3168A | PCM5122 REDDEDİLMİŞ |
| Rate Limiting | APCu | 60 req/60s |
| Encryption | AES-256-GCM, Argon2id | NIST SP 800-38D |

---

## 13. Platform Katmanları (Tier)

| Tier | OS | Durum | Ses Sürücüsü |
|------|-----|-------|-------------|
| **Tier 1 (Primary)** | Windows (XP-11, Server 2012 R2+) | ✅ Ana geliştirme | ASIO, WASAPI |
| **Tier 2** | Linux (Ubuntu, Debian, Fedora) | ✅ Destekli | ALSA, PipeWire |
| **Tier 3** | macOS (Monterey–Sonoma) | ✅ Destekli | CoreAudio |
| **Tier 4** | Raspberry Pi (ARM64) | ✅ Destekli | I2S |
| **Tier 5** | ReactOS | ⚠️ Experimental | Sınırlı |

---

## 14. Deployment Modları

| Mod | Platform | Donanım |
|-----|----------|---------|
| Home Media Center | Windows/Linux/macOS | PC/Laptop |
| Car Audio System | Windows/Android Auto | Raspberry Pi 5 / PCM3168A |
| Professional Studio | Windows (WASAPI/ASIO) | 8.1 Surround + Class AB |
| NAS Audio Server | Linux (Docker) | Synology/QNAP |
| DAC Control System | Windows/Linux | XMOS XU316 + PCM3168A |

---

## 15. Tema Motoru (ADR-044)

- **Gender-based:** female→pink, male→blue, neutral→default
- **PHP:** `ThemeEngine.php` — DB + user gender çözümleme
- **JS:** `ThemeManager.js` — CSS custom properties ile anında geçiş (sayfa yenileme yok)
- **DB:** `user_preferences` tablosu — `user_id`, `device_type`, `theme_gender`
- **Admin:** Bağımsız tema sistemi (kullanıcı temalarından ayrı)

---

## 16. Audio Organizasyonu (5 Bölüm)

| Division | Sorumluluk |
|----------|------------|
| Hardware Division | Özel audio kartları, DAC/ADC, DSP çipleri, amplifikatör |
| Software Division | C++ Audio Engine, DSP Engine, Mixer, sürücüler |
| Studio Division | ASIO, WASAPI, kayıt, monitoring, routing |
| Consumer Division | Bluetooth, WiFi Audio, müzik oynatma, ev ve araç ses |
| Research Division | AI DSP, yeni codec teknolojileri, geleceğin audio donanımları |

---

## 17. Test Kapsama Hedefleri

| Modül | Minimum | Hedef | Framework |
|-------|---------|-------|-----------|
| Backend (PHP) | ≥80% | ≥90% | PHPUnit 11 |
| Frontend (JS) | ≥80% | ≥90% | Vitest |
| Audio Engine (C++) | ≥80% | ≥90% | Google Test |
| Download Service | ≥80% | ≥90% | Vitest |

---

## 18. 18 BCNF Veritabanı (ADR-040)

*Detaylı metadata için bakınız: [[architecture/00-overview/architecture-master]] §3*

| # | Veritabanı | Amaç |
|---|------------|------|
| 1 | `coremusic_auth` | Users, roles, sessions, tokens, credential vault, API keys |
| 2 | `coremusic_user` | Profiles, preferences, history, favorites |
| 3 | `coremusic_musics` | Songs, artists, genres, lyrics, files, podcasts, videos, radio |
| 4 | `coremusic_albums` | Album collections, discs, stats |
| 5 | `coremusic_playlist` | User and AI playlists, collaborators, followers |
| 6 | `coremusic_catalog` | Reference data (genres, artist roles, instruments, moods) |
| 7 | `coremusic_logs` | Application logs, audit trail, analytics, performance metrics |
| 8 | `coremusic_media` | Device sync, media file metadata, access control |
| 9 | `coremusic_system` | Settings, config, cache, EQ, notifications, i18n |
| 10 | `coremusic_social` | Comments, shares, activity, listening rooms, notifications |
| 11 | `coremusic_wireless` | WiFi + Bluetooth networks |
| 12 | `coremusic_ai` | User preference profiles, listening features, recommendations |
| 13 | `coremusic_api` | API keys, rate limits, API call logs, webhooks |
| 14 | `coremusic_cms` | Pages, blog, tags, media assets, FAQs, banners |
| 15 | `coremusic_download` | Download queue, history, cache, source APIs |
| 16 | `coremusic_neva` | EQ presets, DSP settings, routing matrix, spectrum analysis |
| 17 | `coremusic_studio` | Studio sessions, tracks, presets, equipment |
| 18 | `coremusic_patch` | Schema versions, migration logs, patches |

**Toplam:** 18 BCNF veritabanı, 156 tablo.  
**Kurallar:** ORM yasak (ADR-002), SELECT * yasak, prepared statement zorunlu, BCNF zorunlu.

---

## 18A. Template Sistemi (Zorunlu)

**⚠️ ZORUNLULUK:** Yeni dosya oluşturulurken `.ai/.templates/index.md`'den uygun template seçilmek ZORUNLU. Template olmadan dosya oluşturulamaz (Guardrail #16).

| Kategori | Template | Kullanım Alanı |
|----------|----------|----------------|
| adr/ | adr-template.md | Yeni ADR oluştururken |
| adr/ | adr-frontend-template.md | Frontend ADR |
| adr/ | adr-database-template.md | Database ADR |
| adr/ | adr-security-template.md | Güvenlik ADR |
| adr/ | adr-audio-template.md | Audio/Hardware ADR |
| backend/ | php-template.md | PHP backend geliştirme |
| backend/ | nodejs-template.md | Node.js backend |
| frontend/ | js-template.md | JavaScript geliştirme |
| frontend/ | css-template.md | CSS/ITCSS geliştirme |
| testing/ | phpunit-template.md | PHPUnit test |
| testing/ | vitest-template.md | Vitest test |
| infrastructure/ | migration-template.md | DB migration |
| infrastructure/ | docker-template.md | Docker container |
| infrastructure/ | github-actions-template.md | CI/CD pipeline |
| documentation/ | api-doc-template.md | API dokümantasyonu |
| documentation/ | security-audit-template.md | Güvenlik denetimi |
| documentation/ | WikiPage-Template.md | Wiki sayfası |
| hardware/ | arduino-template.md | Arduino/IoT |
| hardware/ | avr-template.md | AVR mikrodenetleyici |
| hardware/ | pic-template.md | PIC mikrodenetleyici |
| query/ | Query-Template.md | SQL sorguları |
| other/ | aspnet-template.md | ASP.NET backend |
| other/ | c-template.md | C/C++ geliştirme |

**Kullanım:** Template'i kopyala → Değişkenleri doldur (`{{VARIABLE}}`) → Gereksiz bölümleri kaldır.

**Detay:** [[.ai/.templates/index]]

---

## 19. Audio Engine Standartları

| Özellik | Değer |
|---------|-------|
| Sample Format | Float32 (32-bit) |
| Sample Rate | 48kHz standart |
| Kanal | 2.0 → 8.1 (7.1 surround) |
| Latency Hedefi | <10ms (ASIO), <20ms (WASAPI) |
| DSP Efektleri | EQ, Reverb, Compressor, Limiter |
| Reverb Modları | Geniş Konser, Düğün Salonu, Oda, Stüdyo |

**C++ Guardrails:** Zero-allocation, lock-free, noexcept, cache-line alignment (64-byte).

---

## 20. Kritik ADR'ler

| ADR | Konu | Durum |
|-----|------|-------|
| [[decisions/accepted/ADR-001-vanilla-js-itcss]] | Vanilla JS + ITCSS, framework yasak | Frozen |
| [[decisions/accepted/ADR-002-pdo-mandatory-no-orm]] | PDO mandatory, ORM yasak | Frozen |
| [[decisions/accepted/ADR-010-csrf-protection-strategy]] | CSRF token = `csrf_token` | Frozen |
| [[decisions/accepted/ADR-011-session-management]] | Session yönetimi | Frozen |
| [[decisions/accepted/ADR-022-database-hardened-security]] | DB güvenlik sertleştirme | Frozen |
| [[decisions/accepted/ADR-038-8.1-sound-card-chip-selection]] | PCM3168A + XMOS XU316 | Active |
| [[decisions/accepted/ADR-040-database-authority]] | 18 BCNF DB otoritesi | Active |
| [[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]] | Vault restructuring, PHP 8.4, port 81 | Active |
| [[decisions/accepted/ADR-044-dynamic-user-theme-engine]] | Dynamic theme engine | Active |

---

## 21. Yasak Örüntüleri

| ❌ Yasak | ✅ Doğru |
|----------|----------|
| `_csrf_token` | `csrf_token` |
| ORM (Eloquent, Doctrine) | Raw PDO |
| `SELECT *` | Explicit columns |
| `innerHTML` | `DOMParser` + `TrustedTypes` |
| React / Vue / Angular | Vanilla JS |
| Hardcoded secrets | `.env` / credential vault |
| `eval()` / `Function()` | Safe alternatives |
| `localStorage` for auth | Session-based auth (HTTPOnly cookie) |
| `sessionStorage` for auth | Session-based auth (HTTPOnly cookie) |
| `var` | `const` / `let` |
| PCM5122 (8.1 surround) | PCM3168A / AK4458 |

---

## 22. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| USB cihaz çıkarma | WASAPI fallback | [[ADR-017-dsp-hardware-mode]] |
| Multi-Tab CSRF | Session-bound tek token | [[ADR-010-csrf-protection-strategy]] |
| BCNF violation | 3NF → BCNF audit | [[ADR-040-database-authority]] |
| Session timeout (3600s) | Otomatik yeniden auth | [[ADR-011-session-management]] |
| Layer violation | Derhal revert | CLAUDE.md §7 |
| PCM5122 kullanımı | PCM3168A veya AK4458 | [[ADR-038-8.1-sound-card-chip-selection]] |
| Network outage | Offline-First + SQLite queue | — |
| Cache stampede | Mutex ile single load | L0 |
| ADR conflict | Escalation protocol | [[engine.md]] |

---

## 23. Kritik Uyarılar

| # | Uyarı | Sonuc |
|---|-------|-------|
| 1 | Middleware sırası değiştirme | CSP nonce üretimi bozulur, güvenlik açığı |
| 2 | `SELECT *` kullanma | SQL injection riski |
| 3 | Hardcoded secret kodda/log'da | Veri sızıntısı |
| 4 | PCM5122 ile 8.1 surround | Sistem hatası (H001 REJECT) |
| 5 | Plan olmadan kod yazma | Mimari bütünlük bozulur |
| 6 | ASIO Exclusive Lock | Aynı anda sadece tek uygulama |
| 7 | DC Offset Riski | Class AB amfide >0.5V DC offset koruma rölesi |

---

## 24. Bağımlılıklar

| Bağımlılık | Tür | Versiyon | Zorunlu mu? |
|------------|-----|---------|-------------|
| PHP | Backend | 8.4+ | ✅ Evet |
| MySQL/MariaDB | Database | 9.x | ✅ Evet |
| Node.js | Download Service | LTS | ✅ Evet |
| C++ | Audio Engine | C++20 | ✅ Evet |
| JUCE | Audio Framework | 9.x | ✅ Evet |
| ASIO SDK | Audio Driver | 2.3.4 | ✅ Evet |
| FFmpeg | Media Processing | Latest | ✅ Evet |
| Composer | PHP Dependency | Latest | ✅ Evet |
| npm | JS Dependency | Latest | ✅ Evet |

**ASIO SDK Download:** https://www.steinberg.net/developers/asiosdk-open/

---

## 25. İleriye Yönelik Yol Haritası

| Faz | Hedef | Süre |
|-----|-------|------|
| Faz 1 — MVP | Mevcut PC/laptop'da temel platform | 6–12 ay |
| Faz 2 — Premium | CoreMusic Audio donanım entegrasyonu | 12–24 ay |
| Faz 3 — Professional | Tam entegre stüdyo ve araç içi | 24–36 ay |

---

## 26. İlgili Dokümanlar

| Dosya | Amaç |
|-------|------|
| [[AGENTS.md]] | Agent kayıt defteri, yetkiler, handover |
| [[WORKFLOW.md]] | Süreçler, fazlar, workflow'lar |
| [[index.md]] | Master katalog, tüm vault yapısı |
| [[keys.md]] | Keyword haritası, yönlendirme |
| [[brain.md]] | Mimari kararlar, ADR 001-087 |
| [[MEMORY.md]] | Session hafızası, persistent state |
| [[log.md]] | Audit trail, append-only günlük |
| [[engine.md]] | Orkestrasyon motoru, task dispatch |
| [[archives/prompt0-genel-ana-prompt-2026-08-13]] | Ana genel prompt, tüm sistem kuralları |
| [[archives/prompt1-spa-router-2026-08-13]] | SPA Router mimarisi promptu |
| [[archives/prompt2-auth-2026-08-13]] | Authentication sistemi promptu |
| [[archives/prompt3-api-2026-08-13]] | API mimarisi promptu |

### 26.1 Prompt Entegrasyonu (prompt0-3)

Her oturum başlangıcında sırayla okunur:

| Sıra | Prompt | Dosya | Max Süre | Amaç |
|------|--------|-------|----------|------|
| 1 | prompt0 (Genel Ana) | [[archives/prompt0-genel-ana-prompt-2026-08-13]] | 5s | 11 alt domain, 10 panel, 20 analiz görevi, zorunlu kurallar |
| 2 | prompt1 (SPA Router) | [[archives/prompt1-spa-router-2026-08-13]] | 3s | Enterprise router: SOLID, PSR, attribute-based, DI |
| 3 | prompt2 (Auth) | [[archives/prompt2-auth-2026-08-13]] | 3s | Merkezi auth.coremusic.net, hybrid JWT+session, RBAC |
| 4 | prompt3 (API) | [[archives/prompt3-api-2026-08-13]] | 3s | API-First, Gateway, CQRS, Event Driven |

**Toplam max süre:** 14s

### 26.2 Prompt-Domain Eşleşmesi

| Prompt | Sorumlu Agent'lar | Kullanım Anı |
|--------|-------------------|-------------|
| prompt0 | Tüm agentlar (MO dağıtır) | Her analiz görevinde, 20-adımlı kontrol listesi |
| prompt1 | Backend Architect, UI Designer | SPA router geliştirme, route tasarımı |
| prompt2 | Security Engineer, Backend Architect | Auth middleware, session, JWT, CORS |
| prompt3 | Backend Architect, DevOps Engineer | API gateway, servis mimarisi, CQRS |

### 26.3 Zorunlu Kurallar

1. Prompt kuralları CLAUDE.md ile çelişirse → CLAUDE.md öncelikli (SSOT)
2. Prompt içeriği vault'a işlenmiştir. Tekrar prompt okumak yerine ilgili vault dosyası okunur
3. Prompt versiyonları: `prompt[N]-[topic]-YYYY-MM-DD.md` formatında, tarih güncellendikçe arşivde yeni dosya oluşturulur

---

## 27. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 5 Mimari | [[architecture/l0-infrastructure]] | L0-L6 katmanları |
| § 6 Middleware | [[ADR-010-csrf-protection-strategy]] | Middleware sırası |
| § 9 Paneller | [[decisions/accepted/ADR-043-auth-subdomain-consolidation]] | Auth konsolidasyonu |
| § 12 Teknoloji | [[brain.md]] | Tech stack detayları |
| § 15 Tema | [[ADR-044-dynamic-user-theme-engine]] | Theme engine |
| § 18 DB | [[architecture/05-data/database_master]] | 18 BCNF şemaları |
| § 19 Audio | [[architecture/06-audio/index]] | Audio engine |
| § 20 ADR | [[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]] | Vault standardı |
| § 20A Master Plan | [[architecture/03-contracts/master-implementation-plan]] | 5 faz, 40 gün implementasyon |
| § 20B ADR-087 | [[decisions/accepted/ADR-087-master-implementation-plan]] | Master plan ADR |

---

## 27A. Skills Registry (10 Skill — Guardrail #16 Zorunlu)

| # | Skill | Amaç | Kullanım |
|---|-------|------|----------|
| 1 | `ui-code-generator` | UI/CSS kod üretimi, responsive tasarım | Frontend geliştirme |
| 2 | `ui-analyzer` | UI analizi, mevcut tasarım değerlendirme | Tasarım inceleme |
| 3 | `skill-maker` | Yeni skill oluşturma, template sistemi | Skill geliştirme |
| 4 | `hallucination-control` | Halüsinasyon kontrolü, doğrulama | Kod yazma öncesi |
| 5 | `human-mode` | İnsan modu iletişimi, onay süreçleri | Kullanıcı etkileşimi |
| 6 | `red-team-truth-mode` | Güvenlik testi, adversarial analiz | Güvenlik denetimi |
| 7 | `prompt-maker` | Prompt mühendisliği, AI talimat tasarımı | Prompt geliştirme |
| 8 | `agent-orchestrator` | Agent görev dağıtımı, multi-agent koordinasyonu | Görev dağıtımı |
| 9 | `composer-sync` | Composer dependency yönetimi | Bağımlılık yönetimi |
| 10 | `database-normalize-maker` | BCNF normalizasyonu, şema tasarımı | DB tasarımı |

**Konum:** `.opencode/skills/*/SKILL.md`
**Kural:** Her skill dosyası vault referansları içerir (CLAUDE.md, AGENTS.md, WORKFLOW.md, brain.md, index.md).
**Yüklenme:** Boot protokolünde otomatik yüklenmez, gerektiğinde `skill` tool'u ile yüklenir.

---

## 27B. Agent Profiles (11 Agent — .ai/.agents/)

| # | Agent | Profil Dosyası |
|---|-------|---------------|
| 1 | Master Orchestrator | [[.agents/master-orchestrator]] |
| 2 | Backend Architect | [[.agents/backend-architect]] |
| 3 | UI Designer | [[.agents/ui-designer]] |
| 4 | Security Engineer | [[.agents/security-engineer]] |
| 5 | Data Engineer | [[.agents/data-engineer]] |
| 6 | Embedded Engineer | [[.agents/embedded-engineer]] |
| 7 | QA Engineer | [[.agents/qa-engineer]] |
| 8 | DevOps Engineer | [[.agents/devops-engineer]] |
| 9 | Audio HW Engineer | [[.agents/audio-hardware-engineer]] |
| 10 | DSP Firmware Engineer | [[.agents/dsp-firmware-engineer]] |
| 11 | Windows SW Engineer | [[.agents/windows-software-engineer]] |

**Konum:** `.ai/.agents/*.md`
**İndeks:** [[.agents/AGENTS.md]]

---

## 28. Sözlük

| Terim | Tanım |
|-------|-------|
| **ACL** | Access Control List — Erişim kontrol listesi |
| **ADR** | Architecture Decision Record — Mimari karar kaydı |
| **AES-256-GCM** | Advanced Encryption Standard, 256-bit, Galois/Counter Mode |
| **ALSA** | Advanced Linux Sound Architecture |
| **APCu** | APC User Cache — PHP önbellek sistemi |
| **Argon2id** | Şifreleme algoritması (64MB/4/2) |
| **ASIO** | Audio Stream Input/Output — Düşük gecikmeli ses |
| **BCNF** | Boyce-Codd Normal Form |
| **BEM** | Block Element Modifier — CSS metodolojisi |
| **BLE** | Bluetooth Low Energy |
| **CQRS** | Command Query Responsibility Segregation |
| **CSP** | Content Security Policy |
| **CSRF** | Cross-Site Request Forgery |
| **DAC** | Digital-to-Analog Converter |
| **DDD** | Domain-Driven Design |
| **DSP** | Digital Signal Processing |
| **FLAC** | Free Lossless Audio Codec |
| **HSTS** | HTTP Strict Transport Security |
| **ITCSS** | It's Time to Create Scaleable Stylesheets |
| **JUCE** | Jules' Utility Class Extension — C++ audio framework |
| **LFE** | Low Frequency Effects |
| **MW** | Middleware |
| **ORM** | Object-Relational Mapping (YASAK) |
| **OWASP** | Open Web Application Security Project |
| **PCM** | Pulse-Code Modulation |
| **PSR** | PHP Standards Recommendations |
| **RBAC** | Role-Based Access Control |
| **SOLID** | Single Responsibility, Open/Closed, Liskov, Interface, Dependency |
| **SPA** | Single Page Application |
| **SSOT** | Single Source of Truth |
| **TTFB** | Time To First Byte |
| **WCAG** | Web Content Accessibility Guidelines |

---

## 29. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 22.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 29 |
| Hard Guardrails | 16 |
| Soft Constraints | 4 |
| Panels | 10 |
| Services | 7 |
| Databases | 18 BCNF |
| Platform Tiers | 5 |
| Deployment Modes | 5 |
| Audio Divisions | 5 |
| ADR Coverage | 001-087 |
| Cross References | 8 |
| Glossary Terms | 30+ |
| Forbidden Patterns | 10 |
| Edge Cases | 10 |
| Skills | 10 |
| Agent Profiles | 11 |
| Critical Warnings | 7 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-12
**Mode:** Red Team · Human Mode · Truth Mode