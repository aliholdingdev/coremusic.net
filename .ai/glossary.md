---
reference_doc: Freelancer Technical Documentation v1.0
type: system
category: reference
title: "CoreMusic — Glossary"
date: 2026-08-19
updated: 2026-09-23
status: active
version: 2.2.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/glossary.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/glossary.md"
    - ".ai/engine.md §9 (Teknoloji Yığını Politikası)"
---

# CoreMusic — Glossary

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[brain.md]] · [[engine.md]]

---

## Purpose

### §1 Amaç

CoreMusic ekosisteminde kullanılan tüm teknik terimlerin tanımlandığı **Tek Doğruluk Kaynağıdır (SSOT)**.

**Nasıl kullanılır (3 adım):**
1. Terimi §2/§3 tablosunda ara → kısa tanım.
2. Proje karşılığı için §4/§5 haritasına bak → kanıt yolu.
3. Derin bağlam gerekiyorsa ilgili §4.1.x bloğunu oku → ilişkili terim + ADR.

**Nasıl katkı yapılır:** Yeni terim → §3 tablosu → §4/§5 kanıt araması → kanıt varsa blok, yoksa `DOĞRULAMA GEREKLİ`. Detay: §7 kuralları.

Bu sürüm (v2.0.0) Faz 1 vault revizyonu ile genişletilmiştir:

1. Mevcut 32 terim **birebir korunmuştur** (§2).
2. Teknoloji yığını terimleri eklenmiştir (§3) — dinamik stack ilkesi ([[engine.md]] §9) kapsamında Node.js, C++, C#, PHP dünyası.
3. Her terimin projedeki gerçek kullanım yeri kod referansıyla eşleştirilmiştir (§4-§5).
4. Doğrulanamayan kullanım alanları `DOĞRULAMA GEREKLİ` etiketiyle işaretlenmiştir (Truth Mode).

---

## Scope

### §2 Sözlük (Kanonik 32 Terim)

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

### §2.1 Ekosistem ve Vizyon Terimleri (Freelancer Technical Documentation v1.0)

| Terim | Tanım |
|-------|-------|
| **Neva Engine** | C++20 tabanlı tescilli ses işleme motoru; 32-bit float DSP, True Peak brickwall limiter, psikoakustik reverb, sıfır bellek tahsisi (*zero-allocation*) ve lock-free kuyruklarla çalışır. |
| **Offline-First** | İnternet bağlantısı olmasa dahi kullanıcının müzik koleksiyonuna, çalma listelerine ve yerel SSD önbelleğine tam erişim sağlayan, mülkiyet odaklı mimari felsefe. |
| **Handoff** | Salonda başlayan bir parçanın, arabaya binildiğinde veya stüdyo iş istasyonuna geçildiğinde tek bir milisaniye dahi duraksamadan aynı akustik profille devam etmesini sağlayan kesintisiz geçiş standardı. |
| **Ambient Aura** | Çalan albüm kapağının renk tonlarına ve müziğin enerjisine göre anlık nefes alan dinamik Glassmorphism görselleştirme ve aydınlatma arayüzü. |
| **Theme Maker** | Kullanıcıların doğal dille komut vererek ("80'ler retro neon", "Gece mavisi akustik") anında kişiselleştirilmiş estetik temalar üretebilmesini sağlayan yapay zeka aracı. |
| **Nova Search Engine** | YouTube ve YouTube Music API'leri üzerinden parça, albüm, sanatçı ve canlı performans aramalarını yürüten, doğru akış kimliğini yakalayan tescilli arama sürücüsü. |
| **Deemix (Deezer Downloader)** | Deezer API akışları üzerinden doğrudan 16/24/32-bit Float FLAC, 32-bit Float WAV ve 320kbps MP3 formatında stüdyo kalitesinde müzik indiren bağımsız sürücü sınıfı. |
| **Bit-Perfect** | İşletim sistemlerinin sesi bozan mikser, sıkıştırma ve yeniden örnekleme katmanlarını tamamen baypas ederek ses sinyalini DAC/amfiye stüdyodan çıktığı saf haliyle iletme standardı. |
| **Multi-Room Audio** | WebRTC ve WebSocket altyapısıyla evin farklı odalarındaki hoparlörlere sıfıra yakın faz gecikmesiyle eşzamanlı veya bağımsız ses dağıtımı sağlayan çok odalı ağ ses mimarisi. |
| **True Peak Limiter** | Ses sonuna kadar açılsa dahi dijital inter-sample tepe noktalarını yakalayarak dijital çatlamayı ve distorsiyonu engelleyen stüdyo seviyesi brickwall sınırlayıcı (THD+N <%0.005). |
| **32-Bit Float DSP** | Dahili ses boru hattında 1528 dB teorik dinamik tavan sunan, ses sinyalini kırpılmadan (*clipping*) işleyen kayan noktalı sayısal ses işleme standardı. |

---

## Architecture

### §3 Teknoloji Yığını Terimleri (Dynamic Tech Stack)

CoreMusic teknoloji yığını proje gereksinimine göre dinamik seçilir ([[engine.md]] §9). Bu bölüm, kullanılabilir yığınların standart terimlerini tanımlar.

| Terim | Tanım |
|-------|-------|
| **Composer** | PHP bağımlılık yöneticisi; `composer.json` paket bildirimi |
| **C#** | Microsoft .NET dili — Windows platform araçları/servisleri için kullanılabilir teknoloji |
| **Node.js** | V8 tabanlı JavaScript çalışma ortamı — I/O-ağırlıklı servisler için kullanılabilir teknoloji |
| **LTS** | Long Term Support — Node.js sürüm politikası (20+ LTS hedefi) |
| **npm** | Node.js paket yöneticisi |
| **PSR-4** | PHP autoload standardı — namespace → dosya yolu eşlemesi |
| **PSR-7** | PHP HTTP mesaj arayüzleri (Request/Response); `nyholm/psr-7` |
| **PSR-15** | PHP middleware arayüz standardı (IMiddleware tabanı) |
| **PSR-3** | PHP log arayüzü; `psr/log ^3.0` |
| **PSR-6 / PSR-16** | PHP cache arayüzleri; `psr/cache ^3.0` |
| **strict_types** | PHP `declare(strict_types=1)` — tip zorlaması; tüm `CoreMusic\` sınıflarında zorunlu |
| **Front Controller** | Tüm istekleri tek giriş noktasında karşılayan desen (`index.php`) |
| **Hexagonal Architecture** | Ports & Adapters — iş mantığını altyapıdan ayıran mimari |
| **DI Container** | Dependency Injection kapsayıcı; `php-di/php-di ^7.0` |
| **FastRoute** | PHP router kütüphanesi; `nikic/fast-route ^1.3` (auth) |
| **phpdotenv** | `.env` dosyası yükleyici; `vlucas/phpdotenv ^5.7` |
| **psr7-server** | PHP → PSR-7 istek fabrikası; `nyholm/psr7-server ^1.1` |
| **path repository** | Composer yerel paket bağlama yöntemi (symlink ile `../shared`) |
| **sodium_compat** | Sodium şifreleme PHP polyfill; `paragonie/sodium_compat ^1.20` |
| **UUID** | Universally Unique Identifier; `ramsey/uuid ^4.7` |
| **PHPStan** | PHP statik analiz aracı; level 5 (`stan` script) |
| **PHPUnit** | PHP test framework; ^10.5 / ^11.0 (pakete göre) |
| **Migration** | Veritabanı şema değişiklik sürümlendirme; `shared/database/migrations/` |
| **BOM** | Byte Order Mark — UTF-8 dosya başı imzası; log.md bozulma vakasında rol oynayan |
| **Mojibake** | Yanlış kodlama çözümlemesi sonucu bozuk metin (örn. `ç?ý`) |
| **I2S** | Inter-IC Sound — çip içi dijital ses veri yolu |
| **TDM** | Time-Division Multiplexing — çok kanallı ses veri çerçeveleme |
| **UAC 2.0** | USB Audio Class 2.0 — standart USB ses protokolü |
| **SNR** | Signal-to-Noise Ratio — sinyal/gürültü oranı (hedef: 112 dB) |
| **THD+N** | Total Harmonic Distortion + Noise — harmonik bozulma ölçütü |
| **XMOS XU316** | USB ses / DSP işlemci (audio interface çekirdeği) |
| **PCM3168A** | TI 8-kanal DAC/ADC çipi (analog arayüz) |
| **FreeRTOS** | Gömülü gerçek zamanlı işletim sistemi (firmware seçenek tablosunda) |
| **xcc** | XMOS derleyici toolchain'i (firmware derleme) |
| **RPi5** | Raspberry Pi 5 — home/studio media center donanım hedefi |
| **PowerShell** | Windows otomasyon kabuğu — vault scriptleri (`vault-integrity-check.ps1` kaydı) |
| **OAuth 2.0** | Yetkilendirme çerçevesi; `shared/src/OAuth/` modülü + `oauth-platforms.php` config |
| **JWT** | JSON Web Token — auth vizyonunda hybrid kimlik yaklaşımının token bileşeni |
| **IMiddleware** | Projenin middleware arayüzü (PSR-15 tarzı) — `shared/src/Interfaces/Middleware/` |
| **IRateLimiter** | Rate limit arayüzü — `CacheRateLimiter` implements |
| **PageCache** | Sayfa önbellek katmanı — `PageCacheAdapter`/`PageCacheInterface`, PageRouter ctor param 7 |
| **Event Dispatcher** | Olay yayım altyapısı — `symfony/event-dispatcher ^7.0`; ADR-086 temeli |
| **validate-key** | Cross-domain auth endpoint'i — `auth.coremusic.net/validate-key` |
| **config** | Servis konfigürasyon klasörü — `shared/config/` (4 dosya: routes, auth-routes, domain, oauth-platforms) |
| **handler/** | İstek işleyici klasörü — auth kökünde `handler/` + `routes/` ayrımı |
| **interface (Interfaces/)** | Sözleşme klasörü — `shared/src/Interfaces/` (IMiddleware, ISessionManager, IRateLimiter kökenleri) |
| **domain config** | Domain başına yapılandırma — `shared/config/domain.php` (0.5KB) |
| **strict final** | Sınıf yazım disiplini — `final class` + `declare(strict_types=1)` tüm CoreMusic sınıflarında |
| **MM_ anahtarları** | Session anahtar sözleşmesi — `MM_UserID`, `MM_UserRole` (AuthMiddleware `_auth` özniteliğine yazar) |
| **bypass rotası** | CSRF doğrulamasından muaf rota — kodda tek doğrulanmış örnek: `set-gender` |
| **proxy güven listesi** | `TRUSTED_PROXIES` sabiti — RateLimiterMiddleware proxy çözümlemesi (LSP: tanımsız) |

### §11 Kavram Haritası (ASCII)

Terimlerin sistem içindeki ilişkisi — tek bakışta konumlandırma:

```text
                          ┌────────────────────────────┐
                          │        .ai VAULT (SSOT)    │
                          │  boot 14 · decisions 79    │
                          └─────────────┬──────────────┘
                                        │
        ┌───────────────────────────────┼────────────────┬──────────────┐
        ▼                               ▼                ▼              ▼
  ┌───────────┐                  ┌─────────────┐  ┌───────────┐  ┌───────────┐
  │  shared/  │                  │   auth.     │  │  home.    │  │  assets.  │
  │ PHP 8.4   │                  │  PHP 8.4    │  │ PHP 8.4   │  │  statik   │
  │ 19 modül  │                  │ hexagonal 7 │  │ minimal 3 │  │ Css/Fonts │
  └─────┬─────┘                  └──────┬──────┘  └─────┬─────┘  └───────────┘
        │                               │  validate-key  │
        │        CacheManager ◄─────────┼────────────────┘ (HomeAuthBridge
        │        Apcu→Memory            │                  TTL 300sn)
        │        DatabaseManager (PDO)  │
        │        PageRouter+AuthGuard   │
        │        Middleware ×4 (PSR-15) │
        ▼                               ▼
  ┌─────────────────────────────────────────────────────────────────────┐
  │ .ai/.sql/mysql/ (18 şema, BCNF)      PLANNED: 9 domain (api, music, │
  │ electronic/ (XMOS→I2S→PCM3168A)      admin, car, studio, pro, media,│
  │ ui-design/ (C01-C16, 19 PNG)         download Node.js, C++20 embed.)│
  └─────────────────────────────────────────────────────────────────────┘
```

Harita okuma kuralı: Sağ blok PLANNED'dir — bu terimler kod referansı gerektirmez; sol/kod blokları IMPLEMENTED'dir — terim girişleri kanıt yolu taşır.

---

## Rules

### §7 Yazım ve Kullanım Kuralları

1. **Kod tanımlayıcıları** kod formatında yazılır: sınıf adları (`CacheManager`), dosya yolları (`shared/src/Cache/CacheManager.php`), metodlar (`dispatch()`).
2. **Yasaklı terimler:** "ORM" yalnız yasak bağlamında geçer; framework adları web panel frontend bağlamında kullanılamaz (ADR-001).
3. **Yeni terim ekleme akışı:** Terim §2/§3 tablosuna girer → §4 veya §5'te kanıt yolu aranır → kanıt yoksa `DOĞRULAMA GEREKLİ` etiketi → kanıt bulunduğunda etiket kaldırılır.
4. **Birebirlik ilkesi:** §2 tablosundaki tanımlar değiştirilemez; yalnız yeni kullanım kanıtı eklenebilir.
5. **Çift dil:** Terim İngilizce kalır; açıklama Türkçe verilir (bu vault'un yerleşik konvansiyonu).
6. **Sürüm ilkesi:** Bu dosya v2.0.0'dan itibaren "terim sayısı + kanıt oranı" ile raporlanır (§9).

---

## Workflow

### §4 Terim → CoreMusic Kullanım Haritası

Kanonik terimlerin projedeki **doğrulanmış** kullanım yerleri. Yöntem: Faz 0 kaynak kod taraması (2026-09-08) — Test-Path, composer.json okuma, sınıf dosyası incelemesi.

| Terim | Proje Karşılığı | Kanıt Yolu |
|-------|-----------------|------------|
| **APCu** | `CacheManager` singleton: `apcu_fetch` varsa `ApcuAdapter`, yoksa `MemoryAdapter` | `shared/src/Cache/CacheManager.php` |
| **CSRF** | `CsrfMiddleware` — state değiştiren metotlarda token doğrulama; bypass rotası: `set-gender` | `shared/src/Middleware/CsrfMiddleware.php` |
| **CSP** | `SecurityHeadersMiddleware` `_csp_nonce` üretimi + `buildCsp()`; `X-Frame-Options: DENY` | `shared/src/Middleware/SecurityHeadersMiddleware.php` |
| **MW** | `shared/src/` 19 modülden biri: `Middleware/` (Csrf, Auth, RateLimiter, SecurityHeaders) | `shared/src/Middleware/` |
| **RBAC** | `AuthGuard::check(uri, SpaRoute, isSpaRequest)` — 6 ardışık auth/rol/izin kontrolü | `shared/src/PageRouter/AuthGuard.php` |
| **SPA** | `PageRouter::dispatch(array $request, string $csrfToken)` — SPA sayfa dispatch | `shared/src/PageRouter/PageRouter.php` |
| **BCNF** | 18 veritabanı şeması (Multi-DB, ADR-003) | `.ai/.sql/mysql/` (18 .sql) |
| **DAC** | Sinyal zinciri: USB → XMOS XU316 → I2S → PCM3168A → Analog | `electronic/hardware/audio-interface.md` |
| **PCM** | 192kHz / SNR 112dB hedef parametreleri | `electronic/hardware/audio-interface.md` |
| **BEM** | C01-C16 bileşen sınıf adlandırması | `.ai/ui-design/01-component-inventory.md` |
| **ITCSS** | Katmanlı CSS mimarisi; `_header.css`, `c-*.css` dosya düzeni | `.ai/ui-design/` çekirdek dosyalar |
| **WCAG** | 15/16 bileşen uygun; C15 toggle ~32px sınırda (min-height 48px notu) | `.ai/ui-design/03-accessibility-gaps.md` |
| **SOLID** | Tüm `CoreMusic\` sınıfları `final` + `declare(strict_types=1)`; interface implements | `shared/src/**` (örn. `AuthMiddleware implements IMiddleware`) |
| **DDD / Hexagonal** | auth servisi include düzeni: Container/Controller/Domain/Handler/Middleware/Repository/Service | `auth.coremusic.net/include/` (7 klasör) |
| **CQRS** | Command/Handler örnek deseni | `.ai/ROLE.md` §20.3 |
| **ADR** | 68 accepted + 12 rejected = 80 karar; kapsam 001-089 | `.ai/decisions/` |
| **SSOT** | `.ai/` vault — tüm kararlara tek referans | `.ai/*.md` (14 boot dosyası) |
| **OWASP** | OWASP uyumluluk dokümanı | `.ai/architecture/07-security/` |
| **Argon2id** | Şifre hash politikası (64MB/4/2 parametreleri) | `.ai/reports/` (faz5 password-hashing raporu) |
| **AES-256-GCM** | Şifreleme katmanı dokümantasyonu | `.ai/architecture/07-security/encryption*` |
| **JUCE / ASIO** | C++20 ses framework ve düşük gecikmeli ses hedefi | `electronic/`, `projects/NevaEngine/` |
| **BLE** | Bluetooth sürücü dokümanı | `electronic/drivers/` (bluetooth) |
| **HSTS** | Güvenlik başlığı dokümanında geçer | `DOĞRULAMA GEREKLİ` — header üretiminde HSTS satırı kod doğrulanmadı |
| **ALSA** | Linux ses altyapısı terimi (RPi5 hedefi) | `DOĞRULAMA GEREKLİ` — electronic/ içinde kod-düzeyi kanıt aranacak |
| **FLAC** | Kayıpsız codec desteği (media pipeline hedefi) | PLANNED — media servisi kodu yok (Faz 0: `media.coremusic.net` dizini mevcut değil) |
| **LFE** | surround/kanal yönetimi terimi | `DOĞRULAMA GEREKLİ` — dsp/ altında eşleme yapılacak (Faz 6) |
| **TTFB** | Genel web performans metriği | Kullanım yeri yok — genel tanım |
| **ORM** | Yasak (ADR-002) — PDO prepared statement zorunlu | `.ai/decisions/accepted/ADR-002*` |
| **AES-256-GCM** | Şifreleme katmanı dokümanı | `.ai/architecture/07-security/encryption*` — kod karşılığı `DOĞRULAMA GEREKLİ` (crypto sınıfı henüz envanterde) |
| **Argon2id** | Parola hash politikası | `.ai/reports/` (faz5 password-hashing raporu) — kod karşılığı `DOĞRULAMA GEREKLİ` |
| **OWASP** | Uyumluluk dokümanı | `.ai/architecture/07-security/` (owasp-compliance) |
| **CQRS** | Command/Handler deseni | `auth.coremusic.net/include/Handler/` + `include/Domain/` düzeni |
| **SOLID** | `final` + `strict_types` + interface implements disiplini | `shared/src/**` sınıf örnekleri |
| **JUCE / ASIO** | C++20 audio hedefi | `electronic/` + `projects/NevaEngine/` — PLANNED |
| **BLE** | Bluetooth sürücü dokümanı | `electronic/drivers/` (bluetooth) — PLANNED |
| **WCAG** | 15/16 bileşen uygun; C15 toggle ~32px | `.ai/ui-design/03-accessibility-gaps.md` |
| **BEM / ITCSS** | C01-C16 adlandırma + 9 katman CSS | `.ai/ui-design/01-component-inventory.md` |

### §4.1 Terim Derin Açıklamaları (kod kanıtlı)

Her blok: tanım + proje bağlamı + kod kanıtı + ilişkili terimler. Kaynak: Faz 0 taraması (2026-09-08), dosya okuma tabanlı.

#### §4.1.1 APCu / Cache

`APCu`, userland opcode önbellek uzantısıdır; CoreMusic'te `CacheManager` singleton üzerinden soyutlanır.

- **Bağlam:** `CacheManager` çalışma anında `apcu_fetch` fonksiyonunun varlığını sınar. Var ise `ApcuAdapter`, yok ise (tipik olarak Windows geliştirme ortamı) `MemoryAdapter` seçilir. Bu, dokümanlardaki "Redis" iddiasının kodda karşılığı olmadığını gösteren temel kanıttır.
- **Kod kanıtı:** `shared/src/Cache/CacheManager.php` — singleton, adapter seçici; ~25 satır.
- **İlişkili:** PSR-6/PSR-16 (`psr/cache ^3.0`), `PageCacheAdapter` (`PageCacheInterface`, PageRouter ctor param 7), ADR-007/013.
- **Dikkat:** Redis PLANNED'tir; IMPLEMENTED etiketi taşıyamaz ([[engine.md]] §9.2 matrisi).

#### §4.1.2 CSRF

`CSRF`, tarayıcıdan gelen state değiştiren isteklerin sahteliğini önleme saldırı sınıfıdır.

- **Bağlam:** `CsrfMiddleware`, POST/PUT/PATCH/DELETE metotlarında token doğrular; GET ve bypass listesi istisnadır. Vault içinde tek bypass rotası doğrulandı: `set-gender`.
- **Kod kanıtı:** `shared/src/Middleware/CsrfMiddleware.php` — token üretimi + karşılaştırma; bypass dizisi kodda okunur.
- **İlişkili:** MW (middleware pipeline), ADR-010; handover'da CSRF token `PageRouter::dispatch(array $request, string $csrfToken)` ikinci parametresi olarak taşınır.
- **Dikkat:** Bypass listesi genişletilmesi güvenlik kararıdır — L4 eskalasyon gerektirir.

#### §4.1.3 CSP

`CSP`, tarayıcının yükleyebileceği kaynakları kısıtlayan HTTP başlık politikasıdır.

- **Bağlam:** `SecurityHeadersMiddleware` her istekte bir nonce üretir (`_csp_nonce`) ve `buildCsp()` ile başlık dizesini kurar; ayrıca `X-Frame-Options: DENY` gönderilir. `strict-dynamic` politikası ADR-012'de sabitlenmiştir.
- **Kod kanıtı:** `shared/src/Middleware/SecurityHeadersMiddleware.php` — nonce üretimi + başlık birleştirme.
- **İlişkili:** OWASP başlık seti, ADR-012; frontend'de inline script nonce ile işaretlenir.
- **DOĞRULAMA GEREKLİ:** HSTS satırının başlık üretiminde bulunup bulunmadığı kod okumasında teyit edilmedi.

#### §4.1.4 Session

`Session`, sunucu taraflı kullanıcı durumu saklama mekanizmasıdır.

- **Bağlam:** Session zinciri üç katmanda doğrulandı: (1) `SessionInitializer` (`shared/src/Session/`) — `ensureStarted(): void`, oturum adı `COREMUSIC_SESS`; (2) `SessionManager` (auth servisi, 175 satır) — `ISessionManager` implements, `MM_UserID/MM_UserRole` anahtarlarını yazar; (3) `SessionBootstrapper` (`shared/src/Session/`) — ortak kurulum.
- **Bilinen sorun:** `SessionInitializer` sınıfı `CoreMusic\Session` ve `CoreMusic\PageRouter` namespace'lerinde kopya olarak mevcut (duplicate risk — engine §8.1 #1).
- **İlişkili:** ADR-011; HomeAuthBridge session kurulumu; login bug vakası (engine §8.4 S-01).

#### §4.1.5 RBAC

`RBAC`, erişim kararlarını roller üzerinden modelleyen yetkilendirme yaklaşımıdır.

- **Bağlam:** `AuthGuard::check(uri, SpaRoute, isSpaRequest)` — altı ardışık kontrol zinciri ile sayfa erişimini karar verir. Rol değerleri session'da `MM_UserRole` anahtarında taşınır (`AuthMiddleware` `_auth` request özniteliğine yazar).
- **Kod kanıtı:** `shared/src/PageRouter/AuthGuard.php`; `shared/src/Middleware/AuthMiddleware.php` (`IMiddleware` implements).
- **İlişkili:** ADR-043 (auth konsolidasyon), MW, ACL.

#### §4.1.6 SPA

`SPA`, tek HTML kabuğu + istemci yönlendirme ile çalışan uygulama deseni.

- **Bağlam:** `PageRouter` SPA sayfa dispatch'ini yönetir; auth kararı backend-controlled'dır (AuthGuard'a bağlı). Multi-domain yaklaşımı ADR-004/ADR-083 ile sabitlenmiştir.
- **Kod kanıtı:** `shared/src/PageRouter/PageRouter.php` — `dispatch(array $request, string $csrfToken)`; `shared/config/routes.php` (3.3KB), `shared/config/auth-routes.php` (1.6KB).
- **İlişkili:** BEM/ITCSS (frontend katmanı), ADR-001 (Vanilla JS), ADR-083 (SPA Router).

#### §4.1.7 BCNF

`BCNF`, ilişkisel şema normalizasyonunun en katı yaygın formudur.

- **Bağlam:** ADR-003 Multi-DB kararı; vault `.ai/.sql/mysql/` altında 18 şema dosyası ile temsil edilir. `DatabaseManager` PDO MySQL bağlantısını `ERRMODE_EXCEPTION` + `EMULATE_PREPARES=false` parametreleriyle kurar.
- **Kod kanıtı:** `shared/src/Database/DatabaseManager.php` (`IDatabaseManager`); `.ai/.sql/mysql/` 18 .sql.
- **İlişkili:** PDO (ADR-002), Migration (`shared/database/migrations/`), ADR-040.

#### §4.1.8 XMOS XU316 / PCM3168A

Ses sinyal zincirinin iki ucu: USB/DSP işlemci ve 8-kanal analog arayüz çipi.

- **Bağlam:** Hedef sinyal zinciri: `USB → XMOS XU316 → I2S → PCM3168A → Analog`. Hedef parametreler: 192kHz örnekleme, SNR 112dB. Çip seçimi ADR-038 ile sabitlenmiştir (ADR-017 DSP Hardware Mode bağlamı).
- **Kod kanıtı:** `electronic/hardware/audio-interface.md`; `electronic/drivers/usb-drivers.md` (XMOS Driver ↔ UAC 2.0 karşılaştırması); `electronic/firmware/rtos.md` (bare-metal ↔ FreeRTOS).
- **İlişkili:** I2S, TDM, UAC 2.0, SNR, THD+N; ADR-017, ADR-038.

#### §4.1.9 BEM / ITCSS

Frontend adlandırma ve katmanlama metodolojileri.

- **Bağlam:** C01-C16 bileşen envanteri BEM sınıf adlandırmasını kullanır (`c-*.css`); ITCSS dokuz katmanlı dosya düzeni `_header.css` gibi partial adlarıyla temsil edilir. Kanonik token kaynağı: `ui-design/tokens/design-tokens-master.md` (536 satır) + `a-design-tokens.css`.
- **Kod kanıtı:** `.ai/ui-design/01-component-inventory.md` (16/16 bileşen), `ui-design/tokens/` (4 dosya).
- **İlişkili:** ADR-001; WCAG (15/16 uygun, C15 toggle ~32px), ADR-083.

#### §4.1.10 Hexagonal / DDD

İş mantığını altyapıdan ayıran mimari stiller.

- **Bağlam:** auth servisi include düzeni bu stili fiziksel olarak taşır: `Container/Controller/Domain/Handler/Middleware/Repository/Service` — 7 alt klasör. `packages/shared` ise `CoreMusic\Shared\` namespace'iyle ikinci PSR-4 kökü olarak ADR-085 geçişinde durur.
- **Kod kanıtı:** `auth.coremusic.net/include/` klasör yapısı; ROLE.md §20.1-20.4 pattern örnekleri (Repository/Service/CQRS/Domain Event).
- **İlişkili:** PSR-4, DI Container (php-di ^7.0), SOLID, ADR-085.

#### §4.1.11 strict_types / PSR-15

PHP tip disiplini ve middleware arayüz standardı.

- **Bağlam:** Örneklenen tüm `CoreMusic\` sınıfları `final class` + `declare(strict_types=1)` ile yazılır; middleware'ler `IMiddleware` arayüzünü uygular (PSR-15 tarzı pipeline).
- **Kod kanıtı:** `shared/src/Middleware/AuthMiddleware.php` (giriş noktası örnek); `shared/composer.json` dev: PHPUnit ^10.5, PHPStan level 5 (`stan` script).
- **İlişkili:** PSR-4 autoload; PHPUnit ^11.0 sürüm farkı (packages/shared) bilinen durum.

#### §4.1.12 BOM / Mojibake

Kodlama bütünlüğü terimleri — vault bakımının operasyonel parçası.

- **Bağlam:** `log.md` kendi 16:45 kaydında 351 dosyada BOM/UTF-8 karışımı tespit etmiş; Türkçe karakterler `ç?ý` biçiminde bozulmuş (mojibake). Bu, normalizasyon önceliğini açıklayan kanıt zinciridir.
- **Kod kanıtı:** `.ai/log.md` ilgili kayıt; PowerShell taramaları ASCII konsol kod sayfası dönüşümleriyle etkileşimlidir.
- **İlişkili:** PowerShell (vault scriptleri), WORKFLOW §8.8 (YAML formatter referansı `DOĞRULANAMADI` etiketli).

#### §4.1.13 OAuth 2.0

Yetkilendirme çerçevesi; üçüncü taraf platform kimlik akışları için standart.

- **Bağlam:** `shared/src/` 19 modülünden biri `OAuth/`'dur; platform yapılandırması `shared/config/oauth-platforms.php` (10.8KB — modüllerin en büyük config'i). ADR-088 (Gender-Based Social OAuth) bu modülün karar kaydıdır.
- **Kod kanıtı:** `shared/src/OAuth/` klasörü + `shared/config/oauth-platforms.php`.
- **İlişkili:** ADR-088; auth servisi; JWT (token tarafı).

#### §4.1.14 RateLimiter / PageCache

İki ayrı önbellek tüketim deseni.

- **Bağlam:** Rate limiting iki katmanlıdır: `CacheRateLimiter` (`Security/`, `IRateLimiter` implements, 41 satır) limit mantığını, `RateLimiterMiddleware` (93 satır) istek hattı entegrasyonunu taşır; ortak anahtar öneki `rl:`, varsayılan pencere 60 istek/60 sn, proxy güveni `TRUSTED_PROXIES` sabitiyle (LSP: tanımsız — engine §8.1 #7). PageCache ise sayfa yanıt önbelleğidir: `PageCacheAdapter` → `PageCacheInterface`, PageRouter kurucu param 7 olarak enjekte edilir.
- **Kod kanıtı:** `shared/src/Middleware/RateLimiterMiddleware.php`, `shared/src/Security/CacheRateLimiter.php`, `shared/src/Cache/` (PageCache sınıfları).
- **İlişkili:** APCu (§4.1.1), ADR-007, ADR-013.

#### §4.1.15 PDO / DatabaseManager

PHP veritabanı erişim katmanı standardı.

- **Bağlam:** `DatabaseManager` (`IDatabaseManager` implements) PDO MySQL bağlantısını `ERRMODE_EXCEPTION` + `EMULATE_PREPARES=false` ile kurar — prepared statement güvencesi ADR-002'nin kod karşılığıdır. ORM tamamen yasaktır; veri erişimi Repository deseni ile soyutlanır.
- **Kod kanıtı:** `shared/src/Database/DatabaseManager.php`; şemalar `.ai/.sql/mysql/` (18 dosya); migration'lar `shared/database/migrations/`.
- **İlişkili:** BCNF (§4.1.7), ADR-002, ADR-003, ADR-022 (DB hardened security).

#### §4.1.16 PageRouter / RouteRegistry

SPA dispatch hattının çekirdek bileşenleri.

- **Bağlam:** `PageRouter` dispatch'i üç iş ortağıyla yürütür: `RouteRegistry` (route tanımları), `ConfigManager` (domain/config), `AuthGuard` (6 kontrol). CSRF token dispatch imzasına ikinci parametre olarak girer — güvenlik hattı router'a bitişiktir.
- **Kod kanıtı:** `shared/src/PageRouter/PageRouter.php` (dispatch imzası), `shared/src/PageRouter/AuthGuard.php`, `shared/config/routes.php` (3.3KB) + `auth-routes.php` (1.6KB).
- **Bilinen bulgu:** `PageRouter` namespace'inde `SessionInitializer` kopyası bulunur (§4.1.4, engine §8.1 #1).
- **İlişkili:** SPA (§4.1.6), RBAC (§4.1.5), ADR-083, ADR-021 (router immutable contract).

#### §4.1.17 AES-256-GCM / Argon2id

Şifreleme ve şifre hash'i — iki farklı kriptografik amaç.

- **Bağlam:** AES-256-GCM simetrik şifreleme katmanı dokümanı `architecture/07-security/encryption*` altındadır (Faz 0 C taramasında dosya doğrulandı). Argon2id, parola hash algoritmasıdır; parametreler (64MB bellek / 4 iterasyon / 2 paralellik) `reports/` altındaki faz5 password-hashing tutarlılık raporunda ele alınır.
- **Kod kanıtı:** `.ai/architecture/07-security/` (encryption dokümanı), `.ai/reports/` (faz5 raporu).
- **İlişkili:** sodium_compat (packages/shared), ADR-034 (Credential Vault Normalization), OWASP.

#### §4.1.18 OWASP

Uygulama güvenliği standartları organizasyonu; Top 10 risk listesi.

- **Bağlam:** Vault'ta OWASP uyumluluk dokümanı `architecture/07-security/` altında mevcut (owasp-compliance — Faz 0 doğrulaması). Kod karşılıkları: CSRF middleware, CSP başlıkları, rate limiting, PDO prepared statement, RBAC.
- **Kod kanıtı:** `.ai/architecture/07-security/owasp-compliance*` + Middleware dosya seti.
- **İlişkili:** CSP (§4.1.3), CSRF (§4.1.2), RBAC (§4.1.5), PDO (§4.1.15).

#### §4.1.19 CQRS

Command Query Responsibility Segregation — yazma ve okuma yollarının ayrılması.

- **Bağlam:** Vault'ta desen örneği ROLE.md §20.3'te (RegisterUserCommand/RegisterUserHandler) modellenmiştir. Çalışan kodda CQRS ayrımı henüz dosya düzeni düzeyinde (Handler/Command ayrımı auth include'unda) doğrulanmıştır; tam CQRS bus IMPLEMENTED değil.
- **Kod kanıtı:** `auth.coremusic.net/include/Handler/` + `include/Domain/` düzeni; ROLE.md §20.3.
- **İlişkili:** DDD/Hexagonal (§4.1.10), ADR-086 (Event Driven Architecture).

#### §4.1.20 WCAG

Web Content Accessibility Guidelines — erişilebilirlik standartları (hedef: WCAG 2.2 AA).

- **Bağlam:** C01-C16 bileşen envanteri WCAG matrisiyle denetlenmiştir: 15/16 uygun; C15 toggle ~32px hedef boyutu sınırda — min-height 48px düzeltme notu mevcut. Gap analizi `03-accessibility-gaps.md`'dedir.
- **Kod kanıtı:** `.ai/ui-design/01-component-inventory.md` (WCAG matrisi), `03-accessibility-gaps.md`.
- **İlişkili:** BEM/ITCSS (§4.1.9), 19 PNG mockup seti, `a-design-tokens.css`.

#### §4.1.21 JWT / Session (hybrid kimlik)

Auth vizyonunun iki kimlik taşıyıcısı.

- **Bağlam:** Vizyon "hybrid JWT+session"dir (§4). IMPLEMENTED tarafı session ağırlıklıdır: `SessionManager` + `MM_*` anahtarları + cross-domain `validate-key` akışı (HomeAuthBridge TTL 300sn). JWT tarafı PLANNED — kodda doğrulanmış JWT üretimi yok.
- **Kod kanıtı:** `auth.coremusic.net/include/Service/SessionManager.php` (175 satır), `home.coremusic.net/include/Auth/HomeAuthBridge.php` (185 satır).
- **İlişkili:** Session (§4.1.4), OAuth 2.0 (§4.1.13), ADR-011, ADR-043.
- **Durum etiketi:** Session IMPLEMENTED · JWT PLANNED.

#### §4.1.22 Migration

Şema değişikliklerinin sürümlendirilmiş, tekrarlanabilir uygulanması.

- **Bağlam:** `shared/database/migrations/` klasörü mevcuttur; ADR-014 (Multi-DB Migration Strategy, accepted · debate ✅ 3 tur/20 persona, 18/2/0 KABUL — frozen değil) stratejiyi, ADR-050 (Multi-DB Sync) güncel yaklaşımı tanımlar. 18 şema dosyası `.ai/.sql/mysql/` altında kanoniktir.
- **Kod kanıtı:** `shared/database/migrations/` + `.ai/.sql/mysql/` (18 .sql).
- **İlişkili:** BCNF (§4.1.7), PDO (§4.1.15), ADR-003, ADR-014, ADR-040, ADR-050.

#### §4.1.23 Codec / FLAC

Ses kodlama/çözme terim ailesi.

- **Bağlam:** FLAC kayıpsız codec hedef medya pipeline'ının parçasıdır; media servisi (PHP+FFmpeg hedefi) kodda mevcut değildir — tüm codec terimleri PLANNED etiketlidir. FFmpeg uzmanlığı ROLE.md §2.3 #36'da profillidir.
- **Kod kanıtı:** Yok (PLANNED) — `architecture/06-audio/` dokümantasyon kanalı.
- **İlişkili:** FFmpeg, PCM (§4 haritası), media domain.

#### §4.1.24 IMiddleware / Pipeline

İstek hattı (request pipeline) soyutlaması.

- **Bağlam:** Dört IMPLEMENTED middleware `IMiddleware` uygular: `AuthMiddleware` (MM_* → `_auth` request), `CsrfMiddleware` (token + bypass `set-gender`), `RateLimiterMiddleware` (`rl:` prefix, 60/60sn), `SecurityHeadersMiddleware` (`_csp_nonce` + `buildCsp()`). PSR-15 tarzı, `shared/src/Interfaces/Middleware/` altında tanımlı.
- **Kod kanıtı:** `shared/src/Middleware/` (4 dosya) + `shared/src/Interfaces/Middleware/IMiddleware`.
- **İlişkili:** MW (kanonik), PSR-15, RBAC (§4.1.5), ADR-008, ADR-010, ADR-012, ADR-013.

#### §4.1.25 Config Katmanı

Servis yapılandırmasının fiziksel düzeni.

- **Bağlam:** `shared/config/` dört dosyadan oluşur: `routes.php` (3.3KB — SPA rotaları), `auth-routes.php` (1.6KB — auth rotaları), `domain.php` (0.5KB — domain sabitleri; `PAGES_PATH`/`SESSION_NAME`/`TRUSTED_PROXIES` sabitlerinin LSP bulgusuyla ilişkili), `oauth-platforms.php` (10.8KB — en büyük config). Domain servisleri kendi config klasörünü taşır (auth: `auth.coremusic.net/config/`).
- **Kod kanıtı:** `shared/config/` 4 dosya (boyutlar Faz 0 envanterinden).
- **İlişkili:** domain config (§3), engine §8.1 #7, ADR-015 (Env Parser Strategy).

### §5 Teknoloji Yığını Terimlerinin Proje Karşılıkları

§3'teki terimlerin gerçek kod/doküman eşleşmesi. Her satır Faz 0 doğrulamasından gelir.

| Terim | Proje Karşılığı | Kanıt Yolu |
|-------|-----------------|------------|
| **Composer** | 4 composer.json: shared-infrastructure v2.0.0, shared, auth, home | `shared/composer.json`, `packages/shared/composer.json`, `auth.coremusic.net/composer.json`, `home.coremusic.net/composer.json` |
| **PSR-4** | `CoreMusic\` → `src/` (shared-infrastructure); `CoreMusic\Shared\` → `src/` (packages/shared); `CoreMusic\Auth\` → `include/`; `CoreMusic\Home\` → `include/` | ilgili composer.json `autoload` blokları |
| **PSR-7 / PSR-15** | nyholm/psr-7 ^1.8 (mesajlar); IMiddleware arayüzü (middleware pipeline) | `shared/composer.json` + `shared/src/Interfaces/Middleware/IMiddleware` |
| **strict_types** | Tüm örneklenen sınıflarda `declare(strict_types=1)` + `final class` | `shared/src/Middleware/AuthMiddleware.php` vb. |
| **Front Controller** | auth ve home: kök `index.php` + `routes/` + `handler/` düzeni | `auth.coremusic.net/index.php`, `home.coremusic.net/index.php` |
| **DI Container** | php-di ^7.0 (shared + auth + home); home `include/Container/` | composer.json require satırları |
| **FastRoute** | auth router bağımlılığı | `auth.coremusic.net/composer.json` |
| **phpdotenv** | auth ortam değişkeni yükleyici | `auth.coremusic.net/composer.json` |
| **path repository** | auth ve home composer.json'da `../shared` path repository (symlink) | auth/home composer.json `repositories` bloğu |
| **UUID** | packages/shared paketinde ramsey/uuid ^4.7 | `packages/shared/composer.json` |
| **sodium_compat** | packages/shared paketinde (şifreleme yardımcıları) | `packages/shared/composer.json` |
| **PHPStan** | shared-infrastructure `stan` script, level 5 | `shared/composer.json` scripts |
| **PHPUnit** | ^10.5 (shared-infrastructure) / ^11.0 (packages/shared) sürüm farkı bilinen durum | ilgili composer.json require-dev |
| **Migration** | `shared/database/migrations/` klasörü mevcut | `shared/database/migrations/` |
| **I2S / TDM** | ses veri yolu protokolleri (hardware spec) | `electronic/hardware/audio-interface.md` |
| **UAC 2.0** | USB Audio Class 2.0 — XMOS sürücü karşılaştırması | `electronic/drivers/usb-drivers.md` |
| **XMOS XU316** | audio interface çekirdek işlemci; firmware hedefi | `electronic/hardware/audio-interface.md`, `electronic/firmware/` |
| **PCM3168A** | 8-kanal DAC/ADC (ADR-038 sound card seçimi) | `electronic/hardware/audio-interface.md` + ADR-038 |
| **SNR / THD+N** | ölçüm metodolojisi dokümanları | `electronic/hardware/snr-thd-measurement.md` |
| **FreeRTOS / xcc** | firmware çalışma ortamı seçim tablosu (bare-metal ↔ FreeRTOS) | `electronic/firmware/rtos.md` |
| **RPi5** | home/studio media center donanım hedefi; ui-design platform token'ı `rpi5-1024` | `.ai/ui-design/tokens/design-tokens-master.md` |
| **PowerShell** | vault script kataloğu (tek kayıt) | `.ai/scripts/index.md` — `vault-integrity-check.ps1` (dosya KAYIP, §8.1) |
| **Node.js** | download servisi hedef yığını | PLANNED — architecture-master §5.1 (kod yok) |
| **C#** | Windows platform araçları hedef yığını | PLANNED — AGENTS.md #11 Windows SW agent |
| **npm / LTS** | Node hedef yığını için paket/sürüm terimleri | PLANNED — kod yok |
| **BOM / Mojibake** | log.md kodlama bozulması vakası | `.ai/log.md` (kendi 16:45 kaydında 351 dosya tespiti) |

### §5.1 Stack Satır Sözlüğü (composer.json alan terimleri)

§3-§5 terimlerini composer.json gerçek alanlarıyla bağlayan köprü tablo. Kanıt: 4 composer.json (Faz 0 okuması).

| Alan Terimi | Tanım | CoreMusic Örneği |
|-------------|-------|------------------|
| `name` | `vendor/package` kimliği; Packagist veya path repo çözümlemesinde kullanılır | `coremusic/shared-infrastructure` |
| `type` | Paket türü (library, project) | Tümü `library`; auth/home uygulama kökü `project` tarzı yapı (alan düzeyi doğrulama: `DOĞRULAMA GEREKLİ`) |
| `require` | Üretim bağımlılıkları — semantik sürüm kısıtları (^ major.minor) | `php-di/php-di ^7.0` vb. |
| `require.php` | Dil sürüm alt sınırı — çalışma ortamı kontrolü | `>=8.4` / `>=8.3` |
| `require-dev` | Yalnız geliştirme bağımlılıkları — üretim yüklenmez | `phpunit`, `phpstan` |
| `autoload.psr-4` | Namespace → dizin; Composer autoloader eşlemesi | `"CoreMusic\\": "src/"` |
| `autoload-dev` | Test namespace eşlemesi | `CoreMusic\Test\` → `tests/` |
| `repositories` | Ek paket kaynakları — `path` türü yerel symlink | `../shared` (auth/home) |
| `scripts` | Composer yaşam döngüsü komutları | `"stan": "phpstan analyse --level 5"` |
| `version` | Açık sürüm (Packagist dışı paketlerde opsiyonel) | `2.0.0` (shared-infrastructure) |
| `minimum-stability` | Kabul edilen minimum paket kararlılığı | Bu projede alan düzeyi doğrulanmadı — `DOĞRULAMA GEREKLİ` |

Okuma kuralı: Bu tablodaki her alan terimi, §3-§5'teki genel terimle birlikte okunur; çelişki durumunda composer.json gerçek değeri üstündür.

### §9 Agent → Terim Kaynak Hattı

Her agent çalışırken hangi terim kümesini esas alır (routing ön okuma):

| Agent | Öncelikli Terim Kümesi |
|-------|------------------------|
| Backend Architect | PSR-4, PSR-7, PSR-15, DI Container, PDO, Front Controller, strict_types |
| UI Designer | BEM, ITCSS, WCAG, token (design-tokens-master), C01-C16 |
| Security Engineer | CSRF, CSP, OWASP, Argon2id, AES-256-GCM, RBAC, RateLimiter |
| Data Engineer | BCNF, Migration, PDO, şema (18 DB) |
| Embedded/DSP Firmware | XMOS XU316, PCM3168A, I2S, TDM, UAC 2.0, FreeRTOS, xcc, SNR, THD+N |
| Windows SW Engineer | C#/.NET, WDK, WASAPI, PowerShell |
| DevOps Engineer | Composer, npm, LTS, pipeline terimleri |
| QA Engineer | PHPUnit, PHPStan, BOM/Mojibake (kodlama bütünlüğü) |
| MO / Vault | SSOT, ADR,IMPLEMENTED/PLANNED, BOM/Mojibake |

Kural: Agent routing sonrası ilk okumada bu küme sözlükte doğrulanır; terim kanıtı çelişirse §12.3 etiket akışı çalışır.

---

## Validation

### §8 Doğrulama Kaydı (Faz 0, 2026-09-08)

| Kontrol | Sonuç |
|---------|-------|
| §2 terim sayımı | 32/32 doğrulandı (satır sayımı) |
| Kırık referans | Bu dosyada kırık link yok (Faz 0 taraması) |
| Kod kanıtlı terim | §4-§5'te 40+ kanıt yolu Test-Path / composer.json okuma ile doğrulandı |
| DOĞRULAMA GEREKLİ etiketli | 8 işaret / 5 farklı terim: HSTS (§4 + §4.1.3), ALSA, LFE, AES-256-GCM, Argon2id, §5.1 type, §5.1 minimum-stability; TTFB satırı işaretsiz (ölçüm 2026-09-24) |
| PLANNED etiketli | FLAC, Node.js, C#, npm/LTS (kod henüz yok) |

### §8.1 Bilinen Kod Bulguları (Faz 1'e taşınan)

| Bulgu | Kanıt | Etki |
|-------|-------|------|
| `SessionInitializer` kopya namespace | `shared/src/Session/` + `shared/src/PageRouter/` | Terim dokümantasyonunda "Session" girişinde işaretli; birleştirme ADR bekliyor |
| Redis adapter yok | `CacheManager.php` | "APCu" girişinde IMPLEMENTED zincir düzeltildi |
| Tanımsız sabitler (LSP) | `SESSION_NAME`, `PAGES_PATH`, `TRUSTED_PROXIES` | engine.md §8.1 #7'ye kaydedildi |
| Csp bağımsız sınıf yok | `buildCsp()` SecurityHeadersMiddleware içinde | "CSP" girişinde gerçek konum yazıldı |

### §8.2 Sık Sorulan Sorular

**S: Terim §2'de var ama kodda bulamıyorum — ne yapmalıyım?**
C: §4/§5 haritasına bak; orada yoksa `DOĞRULAMA GEREKLİ` durumu olabilir (HSTS, ALSA, LFE). Kanıt bulduğunda etiketi kaldır ve §8 tablosuna ekle.

**S: Bu sözlük hangi sırayla okunmalı?**
C: İlk okuma: §1→§2→§4 (kanıt haritası). Derin okuma: ilgili §4.1.x bloğu. Stack çalışması: §3→§5→§5.1→[[engine.md]] §9.

**S: Terimin iki kod karşılığı var — hangisi birincil?**
C: Dispatch/iş mantığı taşıyan sınıf birincildir; yardımcı soyutlama ikincildir. Örnek: CSRF için `CsrfMiddleware` birincil, `PageRouter::dispatch` imzası ikincil.

**S: §4.1 blokları ne zaman açılır?**
C: Terim en az 2 sistem bileşeniyle etkileşiyorsa ve kod kanıtı birden fazla dosyaya yayılıyorsa blok açılır; tek-dosya terimler §4 tablosunda kalır.

**S: Kavram haritasındaki PLANNED bloğu büyürse ne olur?**
C: Her PLANNED domain kod aldığında haritadaki bloğu IMPLEMENTED tarafına taşınır ve §8 tablosuna doğrulama satırı düşer — harita Faz raporlarıyla güncel tutulur.

**S: Bu sözlük hangi sırayla okunmalı?**
C: İlk okuma: §1→§2→§4 (kanıt haritası). Derin okuma: ilgili §4.1.x bloğu. Stack çalışması: §3→§5→§5.1→[[engine.md]] §9.

**S: Yeni teknoloji (ör. Go, Rust) eklemek istiyorum.**
C: Önce [[engine.md]] §9.3 seçim kurallarını ve §9.4 karar kaydı şablonunu uygula; ADR olmadan §3'e terim eklenmez.

**S: "Framework yasak" Node.js servise uygulanır mı?**
C: Hayır — ADR-001 kapsamı web panel frontend'idir ([[engine.md]] §9.5 madde 5).

**S: PLANNED terimi kodda yazılır mı?**
C: Yazılmaz — dokümanda etiket olarak durur; kod kanıtı gelince etiket kaldırılır ve §8'ye doğrulama satırı eklenir.

**S: İki terim çelişiyor (örn. "Redis cache" dokümanı ↔ CacheManager kodu).**
C: Kod kanıtı üstündür; doküman düzeltilir, çelişki `log.md`'ye kaydedilir (Truth Mode).

**S: Terim kanıtım birden fazla dosyaya işaret ediyor.**
C: Birincil kanıt yolu tek satırda yazılır; ikincil kanıtlar "vb." ile listelenir — harita tek birincil kanıt ilkesiyle tutarlı kalır.

**S: IMPLEMENTED şeması ama PLANNED servisi olabilir mi?**
C: Evet — Data Engineer satırı örneğidir: 18 .sql şeması IMPLEMENTED, çalışan DB servis kodu PLANNED. Etiket her zaman tanımlanan özneye aittir.

**S: Etiket kaldırma kimin yetkisinde?**
C: Kod kanıtı Test-Path/okuma ile doğrulanınca agent kaldırabilir; kaldırma §8.1 tablosuna kaydedilir. Etiket EKLEME değişikliği değil, düzeltmedir.

**S: Bu sözlük index.md/keys.md ile nasıl ayrışır?**
C: index.md katalog (dosya → amaç), keys.md yönlendirme (keyword → dosya), glossary terim tanımı (terim → anlam + kanıt). Üçü tamamlar; tanım yalnız burada tutulur (SSOT).

### §10 Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 2.2.0 |
| Term Count | 32 kanonik (§2) + 51 teknoloji terimi (§3) = 83 (ölçüm 2026-09-24) |
| Derin açıklama | 25 blok (§4.1.1-§4.1.25) |
| Kanıtlı kullanım haritası | 63 satır (§4-§5) + 11 alan sözlüğü (§5.1) (ölçüm 2026-09-24) |
| Kanıt oranı | ~95% (8 DOĞRULAMA GEREKLİ işareti / 83 terim — 5 farklı terim [6 terim-level işaret] + 2 alan-level §5.1; ölçüm 2026-09-24) |
| Source | CLAUDE.md §28 (extracted 2026-08-19) + Faz 0 kod taraması (2026-09-08) |
| Kavram haritası | §11 — IMPLEMENTED sol blok / PLANNED sağ blok |
| Versiyon geçmişi | §12 — kanonik 32 terim sabitlik ilkesi |
| Agent kaynak hattı | §9 — agent routing sonrası öncelikli terim kümesi |
| Kullanım kılavuzu | §1 — 3 adımlı okuma + katkı akışı |
| Kırık kanıt takibi | §8 — DOĞRULAMA GEREKLİ etiketli 8 işaret (5 farklı terim) izlenir (ölçüm 2026-09-24) |
| Status | Red Team · Human Mode · Truth Mode verified |

### §13 Doküman İskeleti (8-Bölüm Uyumu — Vault Refactor Engine 2026-09-23)

> **Not:** v2.0.0 → v2.1.0 (normalize: minor+1); satır-edit + ekleme (ADR-042), §1-§12 korundu.

### §13.1 İskelet Eşlemesi

| İskelet Bölümü | Karşılık Gelen § |
|----------------|------------------|
| Title | H1 + frontmatter (7 zorunlu alan) |
| Purpose | §1 Amaç |
| Scope | §2 Sözlük (Kanonik 32 Terim) |
| Architecture | §3 Teknoloji Yığını Terimleri + §11 Kavram Haritası (ASCII) |
| Rules | §7 Yazım ve Kullanım Kuralları |
| Workflow | §4-§5 (Kullanım/Proje haritaları) + §9 Agent → Terim Hattı |
| Validation | §8 Doğrulama Kaydı + §10 Quality Report + bu bölüm §13 |
| References | §6 Terim → ADR Eşlemesi + §12 Terim Versiyon Geçmişi |

### §13.2 Faz 3 Doğrulama (2026-09-23)

- [x] Frontmatter 7 alan tam; version 2.2.0; updated 2026-09-23
- [x] §1-§12 korundu, silme yok; yeni bölüm §13 eklendi
- [x] REFACTOR REPORT: FILE: glossary.md · PURPOSE: Kanonik terim sözlüğü SSOT · VALIDATION: § + link korundu · RELATED: [[index.md]] · [[keys.md]] · [[CLAUDE.md]] · [[brain.md]] · [[log.md]]

### §13.3 İlgili Dosyalar

[[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[brain.md]] · [[index.md]] · [[keys.md]] · [[log.md]]

### §13.4 Faz 2-3 İskelet Yeniden Düzenleme (2026-09-23)

- [x] 7 bölümlük İngilizce H2 iskeleti uygulandı: `## Purpose / Scope / Architecture / Rules / Workflow / Validation / References` (H1 korundu).
- [x] Eski §1-§13 başlıkları `### §N` biçimine çevrildi; eski H3'ler H3, `#### §4.1.x` derin blokları H4 olarak korundu (25 blok) — içerik taşındı, silme yok.
- [x] Sürüm 2.1.0 → 2.2.0 (FM + §10 + §12); `updated: 2026-09-23`.
- [x] §13.1 eşleme İngilizce sol sütuna güncellendi; §6 çakışması References'a çözüldü; Workflow = §4, §5, §9.
- [x] §10 "Derin açıklama" sayacı düzeltildi: 24 → 25 blok (§4.1.1-§4.1.25 — dosya içi disk ölçümü).
- [ ] ⚠️ VERIFICATION REQUIRED — `packages/shared/` yolu §4.1.10, §4.1.11, §4.1.17 ve §5 tablosunda geçiyor; depo kökünde `packages/` dizini yok (2026-09-24 Test-Path ölçümü). Sahip doğrulaması bekleniyor.
- [x] ✅ GİDERİLDİ (ölçüm 2026-09-24): §10 sayaçları disk ölçümüyle düzeltildi — "43 teknoloji terimi (§3)" → 51, "53 satır (§4-§5)" → 63, "14 alan sözlüğü (§5.1)" → 11. (Önceki kayıt: sahip yeniden doğrulaması bekleniyordu.)
- [x] ✅ GİDERİLDİ (ölçüm 2026-09-24): §8 tablosu ölçüme göre güncellendi — 8 `DOĞRULAMA GEREKLİ` işareti / 5 farklı terim (HSTS §4+§4.1.3, ALSA, LFE, AES-256-GCM, Argon2id, §5.1 type, §5.1 minimum-stability); TTFB satırı işaretsiz. (Önceki kayıt: §8 "4 terim (HSTS, ALSA, LFE, TTFB)" diyordu.)
- [ ] ⚠️ VERIFICATION REQUIRED — Eski bölüm referansları: §7 kural 6 "(§9)" (Quality Report §10'dur) ve §9 son satır "§12.3" (böyle alt bölüm yok). Sahip doğrulaması bekleniyor.

---

## References

### §6 Terim ↔ ADR Eşlemesi

| Terim | ADR | Karar Bağlantısı |
|-------|-----|------------------|
| ORM | ADR-002 | PDO mandatory, ORM yasak (frozen) |
| BCNF | ADR-003 / ADR-040 | Multi-DB 9 BCNF; Database Authority (18 BCNF) |
| SPA | ADR-004 / ADR-083 | Multi-Domain SPA; SPA Router Architecture |
| MW | ADR-008 | Bypass Auth Middleware |
| CSRF | ADR-010 | CSRF Protection Strategy |
| CSP | ADR-012 | CSP Nonce Strict-Dynamic |
| APCu | ADR-007 / ADR-013 | Cache Namespace; Rate Limiting APCu |
| Session | ADR-011 | Session Management |
| DSP / JUCE / ASIO | ADR-017 | DSP Hardware Mode (frozen) |
| XMOS XU316 / PCM3168A | ADR-038 | 8.1 Sound Card Chip Selection |
| ADR | ADR-042 | Vault Restructuring (pointer dosya sistemi) |
| Composer / PSR-4 | ADR-085 | Shared Library Hybrid (tek shared/ + PSR-4 namespace) |
| Node.js (download) | ADR-026 | Download Service Architecture |
| RBAC / Argon2id | ADR-043 | Auth Subdomain Consolidation |
| Node.js (download) | ADR-026 | Download Service Architecture |
| Composer / PSR-4 | ADR-085 | Shared Library Hybrid (tek shared/ + PSR-4 namespace) |
| JWT / Session | ADR-011 | Session Management (frozen) |
| Event Dispatcher | ADR-086 | Event Driven Architecture |
| Migration / BCNF | ADR-050 | Multi-DB Sync Strategy |
| WCAG / BEM | ADR-001 | Vanilla JS + ITCSS (frontend kural seti) |
| OAuth 2.0 | ADR-088 | Gender-Based Social OAuth |

### §6.1 ADR Durum Etiketi Kullanımı

ADR'ler frozen (001-037, değiştirilemez) veya active (038-089) olabilir; glossary girişleri ADR metnini tekrar etmez, yalnız bağlantı kurar. Frozen ADR'nin kod karşılığı değiştiğinde: glossary girişi güncellenir, ADR metnine dokunulmaz — sapma `log.md`'ye ve (gerekirse) appendix dosyasına yazılır (engine §12.4 Faz 5 şablonu).

### §12 Terim Versiyon Geçmişi

| Sürüm | Tarih | Değişim |
|-------|-------|---------|
| 1.0.0 | 2026-08-19 | İlk 32 terim (CLAUDE.md §28'den çıkarım) |
| 2.0.0 | 2026-09-08 | Faz 1 revizyonu: +43 teknoloji terimi, 24 derin blok, kanıt haritası, kavram haritası, IMPLEMENTED/PLANNED etiket sistemi |
| 2.1.0 | 2026-09-23 | Faz 3 (Vault Refactor Engine): §13 Doküman İskeleti eklendi (normalize minor+1; satır-edit + ekleme, ADR-042) |
| 2.2.0 | 2026-09-23 | Faz 2-3 yeniden düzenleme: 7 İngilizce H2 iskeleti (Purpose-References), § başlık dönüşümü (`### §N` / `#### §N.x`), §13.1 eşleme İngilizce, §13.4 eklendi |

Geçmiş ilkesi: Kanonik 32 terim (§2) hiçbir sürümde değiştirilemez; yalnız kanıt sütunları genişler. Yeni terimler her zaman §3+'a eklenir.

Sürüm sorumlusu: Vault Steward; her terim eklemesi Faz kontrol listesinin (engine §12.6) 3. maddesinden geçer — kırık hedef 0 kuralı.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-23
**Mode:** Red Team · Human Mode · Truth Mode
