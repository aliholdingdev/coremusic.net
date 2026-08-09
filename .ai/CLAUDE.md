---
type: guide
category: ai-mandate
title: "CoreMusic — AI Constitution & Master Vault Mandate"
date: 2026-08-08
updated: 2026-08-08
status: active
version: 19.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — AI Constitution & Master Vault Mandate

**Zorunlu Bağlantılar:** [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[MEMORY.md]] · [[log.md]] · [[engine.md]]

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
| **MSA** | Master System Architecture / Sparse Attention — Görev başına max 15 dosya okuma protokolü. |
| **ADR** | Architecture Decision Record — Mimari karar kaydı. Frozen (001-037) ve Active (038-050) olmak üzere iki türdür. |
| **Hard Gate** | Kullanıcı onayı olmadan geçilemeyen kritik faz geçiş noktası. |
| **Zero Code Before Plan** | Plan onayı olmadan kod yazma yasağı. |
| **Zero Hallucination** | Doğrulanamayan bilginin `VERIFICATION REQUIRED` olarak işaretlenmesi. |
| **Layer Violation** | Mimari katman bağımlılık kurallarının ihlali. |
| **CSRF** | Cross-Site Request Forgery — Token key: `csrf_token` (NOT `_csrf_token`). |
| **CSP** | Content Security Policy — nonce-based, strict-dynamic. |
| **BCNF** | Boyce-Codd Normal Form — 9 veritabanı için zorunlu normalizasyon. |
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

## 5. Mimari — L0-L3 Katmanları

Bağımlılık kuralları: ✅ L3→L2, L2→L1, L1→L0 | ❌ L0→L2/L3, L1→L3, L3→L0

| Katman | Kapsam | Teknolojiler |
|--------|--------|-------------|
| **L3 Presentation** | Frontend, UI, DOM | Vanilla JS ES6+, ITCSS 7-layer, TrustedTypes, DOMParser |
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
SessionManager → BypassAuth → RateLimiter → Auth → SecurityHeaders → Csrf
```

| # | Middleware | Görev | Timeout |
|---|-----------|-------|---------|
| 1 | **SessionManager** | Session başlatır, CSP nonce üretir | 3600s idle |
| 2 | **BypassAuth** | Test bypass (`?_bypass=1`), prod'da devre dışı | — |
| 3 | **RateLimiter** | APCu tabanlı, 60 req/60s | 60s |
| 4 | **Auth** | Auth bilgisi inject, RBAC kontrolü | — |
| 5 | **SecurityHeaders** | CSP strict-dynamic, X-Frame-Options, HSTS | — |
| 6 | **Csrf** | `csrf_token` doğrulama (POST/PUT/DELETE) | — |

**Kritik Not:** CSP nonce üretimi SessionManager içindedir. Sıra değiştirilirse CSP bozulur. Middleware sırası **DEĞİŞTİRİLEMEZ**.

---

## 7. Hard Guardrails (10 Kural)

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | Zero Code Before Plan | Plan onayı olmadan kod yok | Kod revert edilir |
| 2 | MSA Limit = 15 dosya | Görev başına max 15 dosya | Görev parçalanır |
| 3 | Zero Hallucination | Doğrulanamayan bilgi → `VERIFICATION REQUIRED` | İçerik silinir |
| 4 | In-Place Refactoring | Dosya adı/yolu değişmez | Dosya geri yüklenir |
| 5 | Single Source of Truth | Bilgi sadece `.ai/` vault'tan | Harici bilgi reddedilir |
| 6 | CSRF Token = `csrf_token` | `_csrf_token` yasak (2026-05-30) | Token reddedilir |
| 7 | Middleware Order Immutable | Sıra değişmez | Sistem durdurulur |
| 8 | Port 81 = music.coremusic.net | PHP 8.4 | Yanlış port yasak |
| 9 | No ORM | Raw PDO only (ADR-002) | ORM kullanımı reddedilir |
| 10 | No Frameworks | Vanilla JS + ITCSS (ADR-001) | Framework reddedilir |

---

## 8. Soft Constraints (4 Kural)

| # | Kural | Esnetme Koşulu | Onay |
|---|-------|----------------|------|
| 1 | %80 test coverage | Geçici %75, teknik borç kabulü | Tech Lead |
| 2 | 30s timeout | Uzun batch işlemi (60s'e kadar) | Tech Lead |
| 3 | 15 dosya MSA limiti | Büyük refactoring (18'e kadar) | Arch Lead |
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
| 3306 | MySQL 9 BCNF DB | TCP |
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
| Database | MySQL / MariaDB (PDO) | 9 BCNF |
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

## 18. 9 BCNF Veritabanı (ADR-040)

| # | Veritabanı | Amaç |
|---|------------|------|
| 1 | `coremusic_auth` | Users, roles, sessions, Argon2id |
| 2 | `coremusic_user` | Profiles, preferences, history |
| 3 | `coremusic_musics` | Songs, artists, genres, metadata |
| 4 | `coremusic_albums` | Album collections |
| 5 | `coremusic_playlist` | User and AI playlists |
| 6 | `coremusic_catalog` | Download queues, service status |
| 7 | `coremusic_logs` | Application logs, audit trail |
| 8 | `coremusic_media` | Media file metadata |
| 9 | `coremusic_system` | System configuration |

**Kurallar:** ORM yasak (ADR-002), SELECT * yasak, prepared statement zorunlu, BCNF zorunlu.

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
| [[decisions/accepted/ADR-040-database-authority]] | 9 BCNF DB otoritesi | Active |
| [[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]] | MSA=15, PHP 8.4, port 81 | Active |
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
| `localStorage` for auth | Session-based auth |
| `var` | `const` / `let` |
| PCM5122 (8.1 surround) | PCM3168A / AK4458 |

---

## 22. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| Token overflow (>15 dosya) | MSA fallback + görev parçalama | [[ADR-042-vault-restructuring-2026-08-03]] |
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
| JUCE | Audio Framework | 8.x | ✅ Evet |
| ASIO SDK | Audio Driver | 2.3.4 | ✅ Evet |
| FFmpeg | Media Processing | Latest | ✅ Evet |
| Composer | PHP Dependency | Latest | ✅ Evet |
| npm | JS Dependency | Latest | ✅ Evet |

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
| [[brain.md]] | Mimari kararlar, ADR 001-050 |
| [[MEMORY.md]] | Session hafızası, persistent state |
| [[log.md]] | Audit trail, append-only günlük |
| [[engine.md]] | Orkestrasyon motoru, task dispatch |

---

## 27. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 5 Mimari | [[architecture/l0-infrastructure]] | L0-L3 katmanları |
| § 6 Middleware | [[ADR-010-csrf-protection-strategy]] | Middleware sırası |
| § 9 Paneller | [[decisions/accepted/ADR-043-auth-subdomain-consolidation]] | Auth konsolidasyonu |
| § 12 Teknoloji | [[brain.md]] | Tech stack detayları |
| § 15 Tema | [[ADR-044-dynamic-user-theme-engine]] | Theme engine |
| § 18 DB | [[architecture/05-data/database_master]] | 9 BCNF şemaları |
| § 19 Audio | [[architecture/06-audio/index]] | Audio engine |
| § 20 ADR | [[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]] | Vault standardı |

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
| **MSA** | Master System Architecture / Sparse Attention |
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
| Version | 19.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| Sections | 29 |
| Hard Guardrails | 10 |
| Soft Constraints | 4 |
| Panels | 10 |
| Services | 7 |
| Databases | 9 BCNF |
| Platform Tiers | 5 |
| Deployment Modes | 5 |
| Audio Divisions | 5 |
| ADR Coverage | 001-050 |
| Cross References | 8 |
| Glossary Terms | 30+ |
| Forbidden Patterns | 10 |
| Edge Cases | 10 |
| Critical Warnings | 7 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode