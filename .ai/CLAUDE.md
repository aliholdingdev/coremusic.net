---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic â€” AI Constitution & Master Vault Mandate"
type: guide
category: ai-mandate
date: 2026-08-08
updated: 2026-09-19
status: active
version: 26.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team Â· Human Mode Â· Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md Â· .ai/AGENTS.md Â· .ai/WORKFLOW.md Â· .ai/brain.md Â· .ai/index.md"
---

# CoreMusic â€” AI Constitution & Master Vault Mandate

**Zorunlu Bağlantılar:** [[AGENTS.md]] Â· [[WORKFLOW.md]] Â· [[index.md]] Â· [[keys.md]] Â· [[brain.md]] Â· [[MEMORY.md]] Â· [[log.md]] Â· [[engine.md]] Â· [[.templates/index]] Â· [[.agents/AGENTS.md]]

**Skills:** `.opencode/skills/` (10 skill â€” Guardrail #16 zorunlu)

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
| **SSOT** | Single Source of Truth â€” Tek Doğruluk Kaynağı. Tüm bilgiler `.ai/` vault'tan okunur. |
| **ADR** | Architecture Decision Record â€” Mimari karar kaydı. Frozen (001-037) ve Active (038-088) olmak üzere iki türdür. |
| **Hard Gate** | Kullanıcı onayı olmadan geçilemeyen kritik faz geçiş noktası. |
| **Zero Code Before Plan** | Plan onayı olmadan kod yazma yasağı. |
| **Zero Hallucination** | Doğrulanamayan bilginin `VERIFICATION REQUIRED` olarak işaretlenmesi. |
| **Layer Violation** | Mimari katman bağımlılık kurallarının ihlali. |
| **CSRF** | Cross-Site Request Forgery â€” Token key: `csrf_token` (NOT `_csrf_token`). |
| **CSP** | Content Security Policy â€” nonce-based, strict-dynamic. |
| **BCNF** | Boyce-Codd Normal Form â€” 18 BCNF veritabanı için zorunlu normalizasyon. |
| **RBAC** | Role-Based Access Control â€” Rol bazlı erişim kontrolü. |
| **OWASP** | Open Web Application Security Project â€” Güvenlik standartları. |
| **ASIO** | Audio Stream Input/Output â€” Düşük gecikmeli ses protokolü. |
| **WASAPI** | Windows Audio Session API â€” Windows ses oturum yönetimi. |
| **DSP** | Digital Signal Processing â€” Dijital sinyal işleme. |
| **FLAC** | Free Lossless Audio Codec â€” Kayıpsız ses formatı. |
| **PCM** | Pulse-Code Modulation â€” Ham ses verisi formatı. |
| **LFE** | Low Frequency Effects â€” Subwoofer kanalı (8.1 surround). |

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

## 5. Mimari â€” K0-K20 (21-Katmanlı Sistem)

*Detaylı metadata için bakınız: [[architecture/master-architecture-index]] Â§2*

| Katman | Kapsam | Katı Kısıtlamalar (Hard Guardrails) |
|--------|--------|-------------------------------------|
| **K15** Medya & Streaming | FFmpeg, FLAC, HLS, DASH, ID3 | Yalnızca K14 üzerinden iletişim |
| **K14** Ağ & İletişim | HTTP/3, WebRTC, DLNA, AirPlay, mDNS | Ağ iletişim standardı (K15, K10-13) |
| **K13** CI/CD & Deploy | GitHub Actions, Playwright, Vitest, Docker | Build ve Deployment otomasyonu |
| **K12** İzleme & Log | App Logs, Prometheus, Grafana | Yalnızca K8 servislerinden okuma yapar |
| **K11** Kullanıcı Deneyimi | ITCSS, BEM, Design Tokens, PWA, A11y | Sadece K10 tarafından tetiklenebilir |
| **K10** Uygulama | Web, Mobile, Car, TV, Studio, Panel | Yalnızca K9 API üzerinden iletişim kurabilir |
| **K9** API & Routing | Gateway, BFF, CQRS, Event Bus, SPA Router | API sözleşmesi (OpenAPI) ihlal edilemez |
| **K8** Servis | Control, Media, Audio, Device, Network, AI, DL | Servisler arası doğrudan çağrı yasaktır, Event Bus kullanılır |
| **K7** Middleware | OriginCheck, Session, CORS, RateLimit | K6 güvenlik doğrulamasını atlayamaz |
| **K6** Güvenlik | Auth, Session, CSRF, RBAC, Encryption | Hard Guardrail: Asla bypass edilemez |
| **K5** Veri Yönetimi | MySQL 9 BCNF, Redis, APCu, Storage | 18 Veritabanı kesinlikle BCNF kurallarına uymalıdır |
| **K4** Yapay Zeka | Müzik Analizi, Recommendation, Auto EQ, Voice | K5 harici veriye erişemez |
| **K3** Ses İşleme Motoru | Neva Engine, DSP, EQ, Crossover | Sıfır gecikme, K2 donanım sürücüsüne sıkı bağımlı |
| **K2** Sürücü | ASIO, WASAPI, ALSA, PipeWire, I2S | Donanım-yazılım köprüsü |
| **K1** Donanım | XMOS XU316, PCM3168A, AK4458, Class AB, DC-DC | Push-Pull DC-DC + Class AB; Sinyal zincirine parazit yasak |
| **K0** İşletim Sistemi | Windows, Linux, macOS, RPi5, ReactOS | Alt seviye işletim sistemi çekirdek servisleri |

### Kritik Bileşenler (K1 Donanım Alt Sistemleri)

| Kod | Bileşen | Kapsam | Teknoloji |
|-----|---------|--------|-----------|
| **H1** | Class AB Amplifikatör | 50W/kanal, 8 kanal modüler, MJL21194/MJL21193 | C++20, STM32/RP2040 MCU |
| **H2** | Güç Kaynağı | Â±40V Push-Pull (SG3525/LM5122), 12V-24V DC Giriş | Voltaj çökmesini önleyen Hi-Fi Filtreleme |
| **H3** | Termal Tasarım | Fischer SK82-150-SA heatsink, Noctua NF-A8 fan | Sıcaklık kontrollü fan |
| **H4** | PCB Tasarım | 6-layer stackup, 200Ã—100mm, 2oz copper, IPC Class 3 | 90Î© USB, 50Î© I2S impedans |
| **H5** | BOM & Üretim | ~1,120 bileşen, ~$415 (1+), ~$293 (100+) | 8 kanal modüler BOM |

### Kritik Bileşenler (K16-K20 Katmanları)

| Kod | Bileşen | Kapsam | Teknoloji |
|-----|---------|--------|-----------|
| **K16** | Class AB Amplifikatör | MJL21194/MJL21193 Darlington, 50W/kanal, THD <0.005% | C++20, STM32/RP2040 MCU telemetri |
| **K17** | Güç Kaynağı Â±35V | LM5122 Boost Converter, 6S LiPo (22.2V), Â±35V simetrik, %96 verim | UVP/OVP/OCP/OTP koruma |
| **K18** | Termal Tasarım | Fischer SK53-100-SA heatsink, 80mm PWM fan, KSD301 thermal cutoff | Sıcaklık kontrollü sessiz fan |
| **K19** | PCB Tasarım | 6-layer stackup, impedance matched, thermal vias, star ground | ENIG finish, 2oz copper |
| **K20** | BOM & Üretim | 1020 bileşen, Mouser/Digikey tedarik, ~$682 sistem maliyeti | Üretim araçları dahil |

### Class AB Amplifikatör Sistemi

| Özellik | Değer |
|---------|-------|
| Topoloji | Class AB Darlington |
| Output | MJL21194/MJL21193 (TO-264) |
| Güç | 50W/kanal @ 8Î© |
| THD | <0.005% @ 1W |
| Pil | 6S LiPo (22.2V nominal) |
| Boost | LM5122 Ã— 2 (Â±35V) |
| Kanal | 1-8 (modüler) |
| Heatsink | Fischer SK53-100-SA (300Ã—75Ã—49mm) |

**İlgili ADR:** [[ADR-089-classab-24v]]

> **L1 Alt Tablolar â€” Service Layer & Data Layer & Infrastructure:**
>
> | Alt Katman | Kapsam | Teknolojiler |
> |------------|--------|-------------|
> | **Service Layer** | Backend servisleri | Control, Media, Audio, Device, AI, Download, Network Audio |
> | **Data Layer** | Veri depolama | MySQL 9 (Primary), SQL Server (Backup/Reporting), MongoDB (Analytics), Redis (Cache), File Storage (Media) |
> | **Infrastructure** | Dağıtım ve operasyon | Docker / Containers, Monitoring (Metrics/Logs), Backup (Disaster Recovery), CI/CD (GitHub Actions) |

### 5.1 Katman Bağımlılık Matrisi

| Kaynak â†’ Hedef | İzinli mi? |
|-----------------|------------|
| L3 â†’ L2 | âœ… Evet |
| L2 â†’ L1 | âœ… Evet |
| L1 â†’ L0 | âœ… Evet |
| L0 â†’ L2/L3 | âŒ Hayır (Layer Violation) |
| L1 â†’ L3 | âŒ Hayır (Layer Violation) |
| L3 â†’ L0 | âŒ Hayır (Layer Violation) |

**Layer Violation İhlali:** Tespit edilirse derhal revert + log CRITICAL.

---

## 6. Middleware Pipeline (Immutable â€” ADR-010/011/012/013/022)

```
OriginCheck â†’ Cors â†’ RateLimiter â†’ SecurityHeaders â†’ SessionManager â†’ Csrf â†’ BypassAuth â†’ Auth â†’ Permission â†’ Validation â†’ Controller
```

| # | Middleware | Görev | Timeout |
|---|-----------|-------|---------|
| 1 | **OriginCheck** | Köken doğrulama (whitelist CORS) | â€” |
| 2 | **Cors** | CORS header yönetimi | â€” |
| 3 | **RateLimiter** | APCu tabanlı, 60 req/60s | 60s |
| 4 | **SecurityHeaders** | CSP strict-dynamic, X-Frame-Options, HSTS | â€” |
| 5 | **SessionManager** | Session başlatır, CSP nonce'u session'a kaydeder | 3600s idle |
| 6 | **Csrf** | `csrf_token` doğrulama (POST/PUT/DELETE) | â€” |
| 7 | **BypassAuth** | Test bypass (`?_bypass=1`), prod'da devre dışı | â€” |
| 8 | **Auth** | Auth bilgisi inject (JWT + Session) | â€” |
| 9 | **Permission** | RBAC yetki kontrolü (regular/premium/studio/car/admin/system) | â€” |
| 10 | **Validation** | Request/DTO validasyonu | â€” |

**Kritik Not:** CSP nonce üretimi SecurityHeaders (#4) içindedir. SessionManager (#5) bu nonce'u session'a kaydeder. Sıra değiştirilirse CSP bozulur. Middleware sırası **DEÄİÅTİRİLEMEZ**.

---

## 6A. API-First Mimari (ADR-084)

CoreMusic'te **hiçbir endpoint doğrudan kodlanmaz.** Önce OpenAPI sözleşmesi hazırlanır.

```
OpenAPI Spec â†’ DTO â†’ Contract â†’ Validation â†’ Use Case â†’ Kod
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
Write: Command â†’ Use Case â†’ Repository â†’ MySQL Master
Read:  Query â†’ Read Model â†’ Cache â†’ Response
```

### Event Driven (ADR-086)

Servisler birbirini doğrudan çağırmaz, event yayınlar:

```
Service A â†’ Event Bus (PSR-14) â†’ Service B, C, D
```

### SPA â†’ ApiClient Kuralı

```
SPA â†’ ApiClient â†’ HTTP â†’ Gateway â†’ Middleware â†’ Use Case â†’ Domain â†’ Repository â†’ Infrastructure
```

SPA **asla** PDO, MySQL, Repository, Entity, Infrastructure, Filesystem, FFmpeg, Redis, Cache veya SQL **görmez.**

---

## 6B. Shared Library â€” Hybrid Yapı (ADR-085 v3.0)

Tek `shared/` dizini + PSR-4 namespace ile modüler ayrım. tek Composer paketi:  bu bir **compsoer paketidir**

```
shared/
â”œâ”€â”€ composer.json              â† Tek paket: coremusic/shared
â”œâ”€â”€ src/
â””â”€â”€ tests/
```

---

## 7. Hard Guardrails (17 Kural)

| # | Kural | Uygulama | İhlal Sonucu |
|---|-------|----------|--------------|
| 1 | Zero Code Before Plan | Plan onayı olmadan kod yok | Kod revert edilir |
| 2 | Vault First | Kod yazmadan önce AI vault'u oku (Â§5 boot protokolü) | Kod geçersiz |
| 3 | Zero Hallucination | Doğrulanamayan bilgi â†’ `VERIFICATION REQUIRED` | İçerik silinir |
| 4 | In-Place Refactoring | Dosya adı/yolu değişmez | Dosya geri yüklenir |
| 5 | Single Source of Truth | Bilgi sadece `.ai/` vault'tan | Harici bilgi reddedilir |
| 6 | CSRF Token = `csrf_token` | `_csrf_token` yasak (2026-05-30) | Token reddedilir |
| 7 | Middleware Order Immutable | Sıra değişmez | Sistem durdurulur |
| 8 | Port 81 = music.coremusic.net | PHP 8.4 | Yanlış port yasak |
| 9 | No ORM | Raw PDO only (ADR-002) | ORM kullanımı reddedilir |
| 10 | No Frameworks | Vanilla JS + ITCSS (ADR-001) | Framework reddedilir |
| 11 | **Mockup Before Frontend** | **KESİNLİKLE YASAK:** Frontend görevinde `.ai/ui-design/00-mockup-index.md` + `.ai/ui-design/01-component-inventory.md` (19 PNG mockup, C01-C16 envanteri) + `.ai/ui-design/tokens/design-tokens-master.md` OKUNMADAN kod yazılamaz. Görsel okunamıyorsa DUR ve bildir. | **Kod derhal revert edilir + CRITICAL log** |
| 12 | Contradiction Gate | Vault'ta çelişki varsa kullanıcıya sor, onay bekle | İşlem durur |
| 13 | Session Continuity | Her oturum başlangıcında geçmiş session'dan devam et | Bağlam kaybolur |
| 14 | Human Approval Gate | Mimari karar öncesi kullanıcı onayı zorunlu | Kod revert edilir |
| 15 | Vault-First Mandatory | AI, .ai/ vault'unu (CLAUDE.md + AGENTS.md + WORKFLOW.md + brain.md + ROLE.md) OKUMADAN hiçbir plan/kod/faaliyet başlatamaz | İşlem derhal durdurulur + revert |
| 16 | Template Mandatory | Yeni dosya oluşturulurken `.ai/.templates/index.md`'den uygun template seçilmek ZORUNLU | Dosya geçersiz |
| 17 | Single Component Responsive | 1024x600 mockup = pixel reference ([[ui-design/screens/00-ascii-art-index]]: Header 60px y:0-60, İçerik 450px y:60-510, Footer 90px y:510-600). Tek component sistemi + responsive CSS. Ayrı HTML/branch YASAK. CSS variables + media queries. Device CSS sadece behavioral override. | Kod revert edilir |

### 7.1 Guardrail #11 Detay â€” Frontend Zorunlu Okuma Protokolü

**âš ï¸ ZORUNLULUK:** Tüm frontend (HTML/CSS/JS/PHP layout) geliştirme görevlerinde aşağıdaki dosyalar OKUNMADAN kod KESİNLİKLE YASAKTIR:

| Sıra | Dosya | İçerik | Kullanım Anı |
|------|-------|--------|-------------|
| 1 | `.ai/ui-design/00-mockup-index.md` | 19 PNG mockup indeksi (home-1024 + home-1920 + shared-1024), hangi görsellerin mevcut olduğu | İlk okunacak â€” hangi ekranlar var? |
| 2 | `.ai/ui-design/01-component-inventory.md` | C01-C16 BEM sınıfları, pixel ölçümleri, token referansları | Bileşen kodlarken |
| 3 | `.ai/ui-design/tokens/design-tokens-master.md` | Renk, boşluk, tipografi, cam token'ları | CSS yazarken |
| 4 | `.ai/ui-design/screens/00-ascii-art-index.md` | 19 PNG'nin piksel düzeyinde ASCII art layout modelleri (desktop 1920: [[screens/B-home/dashboard-1920]]) | Layout hizalamada |
| 5 | `.ai/ui-design/responsive-device-mode.md` | Cihaz bazlı CSS override kuralları â€” **Â§7.4 4K No-Center (4K'da ortalamama YASAK)** ve **Â§12 Geriye Dönük Uyumluluk (fallback ZORUNLU)** bağlayıcıdır | Device-specific CSS'te |

**Referans Sıralaması (çelişki durumunda):** PNG > ASCII art > Component Inventory > Tokens > Implementation Plan.

**İhlal Prosedürü:**
1. Mockup okunmadan kod tespit edilir â†’ Kod derhal revert edilir
2. `log.md`'ye CRITICAL giriş eklenir
3. Vault Steward'a bildirim yapılır
4. Görsel okunamıyorsa DUR ve kullanıcıya bildir

---

## 7A. Hibrit Kodlama Modeli (İnsan + Yapay Zeka)

Bu proje hem insan hem yapay zeka tarafından kodlanmaktadır. Aşağıdaki kurallar her iki taraf için de zorunludur.

| # | Kural | Açıklama |
|---|-------|----------|
| 1 | **Vault-First Mandatory** | AI, vault'u okumadan kod yazamaz. Okuma sırası: CLAUDE.md â†’ AGENTS.md â†’ WORKFLOW.md â†’ index.md â†’ keys.md â†’ brain.md â†’ MEMORY.md â†’ log.md â†’ engine.md â†’ ROLE.md â†’ (Frontend görevlerinde [[ui-design/00-mockup-index]] ve [[ui-design/01-component-inventory]] ZORUNLU) |
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
| 3 | Coverage raporlama esnekliği | Bu satır orijinal listede yok â€” bilinçli silme mi eksiklik mi belirsiz | DOÄRULAMA GEREKLİ (Vault Steward) |
| 4 | BypassAuth devre dışı | Test ortamında aktif edilebilir | Security Engineer |

---

## 9. Servis Haritası â€” 10 Panel

> **Faz 1 gerçeklik notu (2026-09-08):** Bu tablo **hedef mimaridir**. Kod tarafında fiziksel olarak mevcut paneller: auth âœ…, home âœ… (+ assets statik servisi). Diğer panellerin dizini kod ağacında YOK (Test-Path, Faz 0). Durum sütunu hedef tanımı yansıtır.

| # | Panel | Subdomain | Port | Stack | Durum |
|---|-------|-----------|------|-------|-------|
| 1 | Landing | `coremusic.net` | 80 | Vanilla JS | âœ… |
| 2 | Music | `music.coremusic.net` | 81 | PHP 8.4 + JS | âœ… Ana medya |
| 3 | Admin | `admin.coremusic.net` | 80 | PHP 8.4 | âœ… Yönetim |
| 4 | Download | `download.coremusic.net` | 3001 | Node.js + TS | âœ… İndirme |
| 5 | Media | `media.coremusic.net` | 5000/6000 | PHP + FFmpeg | âœ… Medya |
| 6 | Auth | `auth.coremusic.net` | â€” | PHP 8.4 | âœ… Kimlik |
| 7 | Home | `home.coremusic.net` | 81 | Vanilla JS | âœ… Ev merkezi |
| 8 | Car | `car.coremusic.net` | â€” | Vanilla JS | âœ… Araç içi |
| 9 | Studio | `studio.coremusic.net` | 81 | Vanilla JS | âœ… Stüdyo |
| 10 | Pro | `pro.coremusic.net` | 81 | Vanilla JS | âœ… Profesyonel |

**Görünüm Modları:** Home, Pro, Studio â€” her panel için geçerli.

---

## 10. Servis Haritası â€” 7 Backend Servis

| # | Servis | Port | Protocol | Stack | Sorumluluk |
|---|--------|------|----------|-------|------------|
| 1 | Control Service | 81 | HTTP | PHP 8.4 | Auth, session, RBAC |
| 2 | Media Service | 5000/6000 | HTTP | PHP + FFmpeg | Library, metadata, streaming |
| 3 | Audio Service | 9741/9742 | REST/WS | C++20 JUCE | Player, DSP, mixer, EQ |
| 4 | Device Service | â€” | BLE/WiFi/USB | C++20 | Bluetooth, WiFi, USB |
| 5 | Network Audio | â€” | WebRTC/P2P | C++20 | Streaming, multi-room |
| 6 | AI Service | â€” | Internal | PHP + Python | Recommendations |
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
| CSS | ITCSS + BEM | 9-layer |
| Audio Engine | C++20, JUCE 9, ASIO SDK | 2.3.4 |
| Hardware | XMOS XU316, PCM3168A, Class AB Amplifikatör | MJL21194/MJL21193 (ADR-089) |
| Rate Limiting | APCu | 60 req/60s |
| Encryption | AES-256-GCM, Argon2id | NIST SP 800-38D |
| **Database (Primary)** | MySQL 9 | 18 BCNF |
| **Database (Backup/Reporting)** | SQL Server | â€” |
| **Database (Analytics)** | MongoDB | â€” |
| **Cache** | Redis | IMPLEMENTED |
| **Containerization** | Docker | 24+ |
| **CI/CD** | GitHub Actions | â€” |
| **Monitoring** | Metrics/Logs | â€” |
| **Backup** | Disaster Recovery | â€” |

---

## 13. Platform Katmanları (Tier)

| Tier | OS | Durum | Ses Sürücüsü |
|------|-----|-------|-------------|
| **Tier 1 (Primary)** | Windows (XP-11, Server 2012 R2+) | âœ… Ana geliştirme | ASIO, WASAPI |
| **Tier 2** | Linux (Ubuntu, Debian, Fedora) | âœ… Destekli | ALSA, PipeWire |
| **Tier 3** | macOS (Montereyâ€“Sonoma) | âœ… Destekli | CoreAudio |
| **Tier 4** | Raspberry Pi (ARM64) | âœ… Destekli | I2S |
| **Tier 5** | ReactOS | âš ï¸ Experimental | Sınırlı |

---

## 14. Deployment Modları

| Mod | Platform | Donanım |
|-----|----------|---------|
| Home Media Center | Windows/Linux/macOS | PC/Laptop |
| Car Audio System | Windows/Android Auto | Raspberry Pi 5 / PCM3168A |
| Professional Studio | Windows (WASAPI/ASIO) | 8.1 Surround + Class AB |
| NAS Audio Server | Linux | Synology/QNAP |
| DAC Control System | Windows/Linux | XMOS XU316 + PCM3168A |

---

## 15. Tema Motoru (ADR-044)

- **Gender-based:** femaleâ†’pink, maleâ†’blue, neutralâ†’default
- **PHP:** `ThemeEngine.php` â€” DB + user gender çözümleme
- **JS:** `ThemeManager.js` â€” CSS custom properties ile anında geçiş (sayfa yenileme yok)
- **DB:** `user_preferences` tablosu â€” `user_id`, `device_type`, `theme_gender`
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
| Backend (PHP) | â‰¥80% | â‰¥90% | PHPUnit 11 |
| Frontend (JS) | â‰¥80% | â‰¥90% | Vitest |
| Audio Engine (C++) | â‰¥80% | â‰¥90% | Google Test |
| Download Service | â‰¥80% | â‰¥90% | Vitest |

---

## 18. 18 BCNF Veritabanı (ADR-040)

*Detaylı metadata için bakınız: [[architecture/master-architecture-index]] Â§3*

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

**âš ï¸ ZORUNLULUK:** Yeni dosya oluşturulurken `.ai/.templates/index.md`'den uygun template seçilmek ZORUNLU. Template olmadan dosya oluşturulamaz (Guardrail #16).

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
| infrastructure/ | github-actions-template.md | CI/CD pipeline |
| documentation/ | api-doc-template.md | API dokümantasyonu |
| documentation/ | security-audit-template.md | Güvenlik denetimi |
| documentation/ | WikiPage-Template.md | Wiki sayfası |
| hardware/ | arduino-template.md | Arduino/IoT |
| hardware/ | avr-template.md | AVR mikrodenetleyici |
| hardware/ | pic-template.md | PIC mikrodenetleyici |
| query/ | Query-Template.md | SQL sorguları |
| session/ | session-log-template.md | Oturum kaydı |
| other/ | aspnet-template.md | ASP.NET backend |
| other/ | c-template.md | C/C++ geliştirme |

**Kullanım:** Template'i kopyala â†’ Değişkenleri doldur (`{{VARIABLE}}`) â†’ Gereksiz bölümleri kaldır.

**Detay:** [[.ai/.templates/index]]

---

## 19. Audio Engine Standartları

| Özellik | Değer |
|---------|-------|
| Sample Format | Float32 (32-bit) |
| Sample Rate | 48kHz standart |
| Kanal | 2.0 â†’ 8.1 (7.1 surround) |
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
| [[.decisions/draft/ADR-089-classab-24v]] | Class AB Amplifikatör + 6S LiPo + Â±35V Boost | Draft |

---

## 21. Yasak Örüntüleri

| âŒ Yasak | âœ… Doğru |
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
| BCNF violation | 3NF â†’ BCNF audit | [[ADR-040-database-authority]] |
| Session timeout (3600s) | Otomatik yeniden auth | [[ADR-011-session-management]] |
| Layer violation | Derhal revert | CLAUDE.md Â§7 |
| PCM5122 kullanımı | PCM3168A veya AK4458 | [[ADR-038-8.1-sound-card-chip-selection]] |
| Network outage | Offline-First + SQLite queue | â€” |
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
| PHP | Backend | 8.4+ | âœ… Evet |
| MySQL/MariaDB | Database | 9.x | âœ… Evet |
| Node.js | Download Service | LTS | âœ… Evet |
| C++ | Audio Engine | C++20 | âœ… Evet |
| JUCE | Audio Framework | 9.x | âœ… Evet |
| ASIO SDK | Audio Driver | 2.3.4 | âœ… Evet |
| FFmpeg | Media Processing | Latest | âœ… Evet |
| Composer | PHP Dependency | Latest | âœ… Evet |
| npm | JS Dependency | Latest | âœ… Evet |

**ASIO SDK Download:** https://www.steinberg.net/developers/asiosdk-open/

---

## 25. İleriye Yönelik Yol Haritası

| Faz | Hedef | Süre |
|-----|-------|------|
| Faz 1 â€” MVP | Mevcut PC/laptop'da temel platform | 6â€“12 ay |
| Faz 2 â€” Premium | CoreMusic Audio donanım entegrasyonu | 12â€“24 ay |
| Faz 3 â€” Professional | Tam entegre stüdyo ve araç içi | 24â€“36 ay |

---

## 26. İlgili Dokümanlar

| Dosya | Amaç |
|-------|------|
| [[AGENTS.md]] | Agent kayıt defteri, yetkiler, handover |
| [[WORKFLOW.md]] | Süreçler, fazlar, workflow'lar |
| [[index.md]] | Master katalog, tüm vault yapısı |
| [[keys.md]] | Keyword haritası, yönlendirme |
| [[brain.md]] | Mimari kararlar, ADR 001-088 |
| [[MEMORY.md]] | Session hafızası, persistent state |
| [[log.md]] | Audit trail, append-only günlük |
| [[engine.md]] | Orkestrasyon motoru, task dispatch |
| [[archives/prompt0-genel-ana-prompt-2026-09-01]] | Ana genel prompt, tüm sistem kuralları |
| [[archives/prompt1-spa-router-2026-09-01]] | SPA Router mimarisi promptu |
| [[archives/prompt2-auth-2026-09-01]] | Authentication sistemi promptu |
| [[archives/prompt3-api-2026-09-01]] | API mimarisi promptu |
| [[glossary]] | Teknik terimler sözlüğü (75 terim) |

### 26.1 Prompt Entegrasyonu (prompt0-3)

Her oturum başlangıcında sırayla okunur:

| Sıra | Prompt | Dosya | Max Süre | Amaç |
|------|--------|-------|----------|------|
| 1 | prompt0 (Genel Ana) | [[archives/prompt0-genel-ana-prompt-2026-09-01]] | 5s | 11 alt domain, 10 panel, 20 analiz görevi, zorunlu kurallar |
| 2 | prompt1 (SPA Router) | [[archives/prompt1-spa-router-2026-09-01]] | 3s | Enterprise router: SOLID, PSR, attribute-based, DI |
| 3 | prompt2 (Auth) | [[archives/prompt2-auth-2026-09-01]] | 3s | Merkezi auth.coremusic.net, hybrid JWT+session, RBAC |
| 4 | prompt3 (API) | [[archives/prompt3-api-2026-09-01]] | 3s | API-First, Gateway, CQRS, Event Driven |

**Toplam max süre:** 14s

### 26.2 Prompt-Domain Eşleşmesi

| Prompt | Sorumlu Agent'lar | Kullanım Anı |
|--------|-------------------|-------------|
| prompt0 | Tüm agentlar (MO dağıtır) | Her analiz görevinde, 20-adımlı kontrol listesi |
| prompt1 | Backend Architect, UI Designer | SPA router geliştirme, route tasarımı |
| prompt2 | Security Engineer, Backend Architect | Auth middleware, session, JWT, CORS |
| prompt3 | Backend Architect, DevOps Engineer | API gateway, servis mimarisi, CQRS |

### 26.3 Zorunlu Kurallar

1. Prompt kuralları CLAUDE.md ile çelişirse â†’ CLAUDE.md öncelikli (SSOT)
2. Prompt içeriği vault'a işlenmiştir. Tekrar prompt okumak yerine ilgili vault dosyası okunur
3. Prompt versiyonları: `prompt[N]-[topic]-YYYY-MM-DD.md` formatında, tarih güncellendikçe arşivde yeni dosya oluşturulur

---

## 27. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| Â§ 5 Mimari | [[architecture/k0-k5-software/k0-os-layer]] | L0-L6 katmanları |
| Â§ 6 Middleware | [[ADR-010-csrf-protection-strategy]] | Middleware sırası |
| Â§ 9 Paneller | [[decisions/accepted/ADR-043-auth-subdomain-consolidation]] | Auth konsolidasyonu |
| Â§ 12 Teknoloji | [[brain.md]] | Tech stack detayları |
| Â§ 15 Tema | [[ADR-044-dynamic-user-theme-engine]] | Theme engine |
| Â§ 18 DB | [[architecture/k0-k5-software/k5-data-layer/database_master]] | 18 BCNF şemaları |
| Â§ 19 Audio | [[architecture/k0-k5-software/k3-audio-engine]] | Audio engine |
| Â§ 20 ADR | [[decisions/accepted/ADR-042-vault-restructuring-2026-08-03]] | Vault standardı |
| Â§ 20A Master Plan | [[architecture/03-contracts/master-implementation-plan]] | 5 faz, 40 gün implementasyon |
| Â§ 20B ADR-087 | [[decisions/accepted/ADR-087-master-implementation-plan]] | Master plan ADR |
| Â§ 12A UI Design | [[ui-design/00-mockup-index]] | 19 PNG Mockup, C01-C16, 1024x600 SSOT |

---

## 27A. Skills Registry (10 Skill â€” Guardrail #16 Zorunlu)

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

## 27B. Agent Profiles (11 Agent â€” .ai/.agents/)

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

> Detaylı sözlük için bkz: [[glossary]] (75 terim)

---

## 29. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 26.0.0 |
| Status | Red Team Â· Human Mode Â· Truth Mode verified |
| Sections | 30 |
| Hard Guardrails | 17 |
| Soft Constraints | 4 |
| Panels | 10 |
| Services | 7 |
| Databases | 18 BCNF |
| Platform Tiers | 5 |
| Deployment Modes | 5 |
| Audio Divisions | 5 |
| ADR Coverage | 001-089 (80 karar: 37 Frozen + 30 Active + 12 Rejected + 1 Draft) |
| Cross References | 8 |
| Glossary Terms | 30+ |
| Forbidden Patterns | 10 |
| Edge Cases | 10 |
| Skills | 10 |
| Agent Profiles | 11 |
| Critical Warnings | 7 |

---

## 30. .ai Referans Takibi Protokolü (OpenCode Entegrasyonu)

> Detaylı protokol için bkz: [[AGENTS.md]] Â§24

---

## 31. Faz 1 Doğrulama Kaydı (2026-09-08)

Bu dosyada Faz 1 revizyonunda yapılan düzeltmeler:

| # | Düzeltme | Konum | Kanıt |
|---|----------|-------|-------|
| 1 | ADR kapsamı 038-087 â†’ 038-088 (79 karar) | Â§3, Â§29, Â§26 | decisions/ sayımı |
| 2 | 18 â†’ 19 PNG (12+1+6) | Guardrail #11, Â§12A | .ai/.png/ sayımı |
| 3 | Redis L0 hedefâ†’PLANNED notu | Â§5 | CacheManager kod okuma |
| 4 | Glossary 32 â†’ 75 terim | Â§26, Â§28 | glossary.md v2.0.0 |
| 5 | Soft Constraints #3 eksik satırı işaretlendi | Â§8 | Truth Mode â€” DOÄRULAMA GEREKLİ |
| 6 | Panel haritası "hedef mimari" notu | Â§9 | Test-Path Faz 0 |
| 7 | Dinamik stack ilkesi | Â§24 Node.js satırı bağlamı | engine Â§9, ROLE Â§11 |

İlke: Bu dosya anayasadır â€” içerik ekleme/düzeltme yapılırken Guardrail #4 (In-Place) ve #14 (Human Approval) korunmuştur; bu revizyon kullanıcı direktifiyle (2026-09-08, "tüm vault'u satır satır revize et") yetkilendirilmiştir.

---

## 32. CoreMusic Freelancer Teknik Dokümantasyon v1.0

### Â§01 Giriş â€” CoreMusic Tanımı

CoreMusic; trilyon dolarlık küresel dijital medya, otomotiv ses sistemleri ve tüketici elektroniği pazarındaki yapısal açıkları kapatmak ve doğrudan yüksek kârlılığa dönüştürmek amacıyla geliştirilmiş kurumsal seviyede bir **Ticari Dijital Medya Ekosistemi ve Gelir Platformudur**.

**Mülkiyet ve Özgürlük Odaklı Felsefe:** Kullanıcının sahip olduğu kayıpsız ses koleksiyonu (FLAC, WAV, MP3) kalıcı, bağımsız ve ilişkisel bir dijital varlık olarak korunur.

**Kesintisiz Bütünleşik Yaşam Deneyimi:** Akıllı telefondan araç içi bilgi-eğlence panellerine (`car.coremusic.net`), ev medya merkezlerinden (`home.coremusic.net` - RPi5) profesyonel mastering stüdyolarına kadar her temas noktasında yaşayan bütünleşik bir ses standardıdır.

### Â§01.2 Temel Özellikler (10 Ana Başlık)

| # | Özellik | Açıklama |
|---|---------|----------|
| 1 | Hibrit Mimari (Online + Offline) | Kesintisiz yerel akış, çift yönlü bulut eşzamanlaması |
| 2 | Hi-Fi Ses Motoru | C++20 Neva Engine, Zero-Allocation, True Peak Limiter |
| 3 | Ses İşleme Hassasiyeti | 32-bit Float aktif, 64-bit Float yol haritası |
| 4 | Hoparlör Matrisi | 1.0 Mono'dan 8.1 Surround'a (+1 LFE) |
| 5 | Canlı Temalar & AI Theme Maker | Ambient aura, glassmorphism, doğal dil ile tema üretimi |
| 6 | Çapraz Cihaz Ekosistemi | Mobil, tablet, PC, TV, araç, ev medya â€” kesintisiz geçiş |
| 7 | Merkezi Medya Depolama | `media.coremusic.net` â€” otomatik indirme ve indeksleme |
| 8 | Network Audio | DLNA/UPnP, WebRTC/P2P, Multi-Room |
| 9 | AI Müzik Intelligence | Öneri, akıllı playlist, otomatik EQ, ses analizi |
| 10 | Otonom İndirme & Dışa Aktarım | YouTube, Deezer (FLAC), USB, CD yazma |

### Â§02 Sistem Mimarisi

#### 4 Katmanlı Basitleştirilmiş Mimari

```
L3 Uygulama Katmanı â†’ Web, Mobile, Car, TV, Studio, Panel
    â†“
L2 Servis Katmanı â†’ API, Microservices, AI, Media, Sync
    â†“
L1 Güvenlik Katmanı â†’ Auth, Session, CSRF, Rate Limit, Security Headers
    â†“
L0 Altyapı Katmanı â†’ Cloud, On-Premise, Docker, Kubernetes, Storage, Backup
```

#### 7 Katmanlı Detaylı Mimari (PDF)

| # | Katman | Kapsam |
|---|--------|--------|
| 01 | Kullanıcı Deneyimi | Desktop App, Web Portal, Smart TV, Mobile App |
| 02 | Uygulama Servis | User Service, Media, Playlist, AI, Subscription |
| 03 | Native Ses İşleme | Audio Engine (ASIO/WASAPI), Mixer, Effects, DSP |
| 04 | Yapay Zeka | Music Analysis, Recommendation, Voice, AI Generation |
| 05 | Alan (Domain) | Music/Artist/Album, User Profile, Business Rules |
| 06 | Veri Yönetimi | Local DB, Cache, Data Optimization, File Management |
| 07 | Altyapı ve Bulut | Streaming Server, On-Premise, Docker/K8s |

#### Mimari Nitelikler

| Nitelik | Açıklama |
|---------|----------|
| MODÜLER | Her bileşen bağımsız geliştirilebilir |
| ÖLÇEKLENEBİLİR | Dikey ve yatay ölçekleme |
| GÜVENLİ | 10 adımlı middleware, Argon2id, AES-256-GCM |
| SÜRDÜRÜLEBİLİR | Açık mimari, pluggable yapı |
| GELECEÄE HAZIR | AI entegrasyonu, yeni format desteği |

### İlgili Referanslar
- [[VISION]] â€” CoreMusic vizyonu
- [[PROJECTS]] â€” Proje tanımı
- [[WORKFLOW]] â€” Sistem çalışma şekli
- [[architecture/master-architecture-index]] â€” 21 katmanlı mimari

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-09-19
**Mode:** Red Team Â· Human Mode Â· Truth Mode
