---
title: "CoreMusic — Prompt 0: Genel Ana Prompt"
type: prompt
category: general
date: 2026-08-15
updated: 2026-08-15
status: active
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
token_limit: 15000
reference:
  authority: ".ai/CLAUDE.md"
  shared_base: ".ai/archives/prompt-shared-base.md"
  source_of_truth:
    - ".ai/CLAUDE.md"
    - ".ai/AGENTS.md"
    - ".ai/WORKFLOW.md"
    - ".ai/brain.md"
    - ".ai/index.md"
    - ".ai/keys.md"
    - ".ai/MEMORY.md"
  architecture:
    - ".ai/ADR/"
    - ".ai/architecture/"
  adr:
    - ".ai/decisions/accepted/ADR-039-7-service-platform-architecture.md"
    - ".ai/decisions/accepted/ADR-040-database-authority.md"
    - ".ai/decisions/accepted/ADR-042-vault-restructuring-2026-08-03.md"
    - ".ai/decisions/accepted/ADR-043-auth-subdomain-consolidation.md"
    - ".ai/decisions/accepted/ADR-085-modular-composer-packages.md"
    - ".ai/decisions/accepted/ADR-087-master-implementation-plan.md"
  prompts:
    - ".ai/archives/prompt-shared-base.md"
changelog:
  - version: 2.0.0
    date: 2026-08-15
    changes:
      - Tamamen yeniden yazım — SOLID, Clean Code, L0-L6 uyumlu
      - ROLE tekrarı kaldırıldı (shared-base'den referans)
      - Vault cross-reference eklendi
      - 20 analiz görevi detaylandırıldı
---

# CoreMusic — Prompt 0: Genel Ana Prompt

**Ortak Temel:** [[prompt-shared-base]] (ROLE, sistem tanımı, L0-L6, SOLID, Clean Code — bu dosyada tekrar edilmez)

**Zorunlu Bağlantılar:** [[../../CLAUDE.md]] · [[../../AGENTS.md]] · [[../../WORKFLOW.md]] · [[../../brain.md]] · [[../../index.md]] · [[../../keys.md]]

---

## 1. Temel Referans

Bu prompt, CoreMusic ekosisteminin **tamamını** kapsayan genel ana prompttur. Her AI oturumunun başında okunur. Domain bazlı görevlerde prompt1-3 ile birlikte kullanılır.

**Okuma Sırası:** shared-base → bu dosya → (gerekirse) prompt1/2/3

---

## 2. 10 Panel Tanımı

*Detaylı metadata: [[../../brain.md]] §9, [[../../index.md]] §6*

| # | Panel | Subdomain | Port | Stack | Sorumluluk |
|---|-------|-----------|------|-------|------------|
| 1 | Landing | `coremusic.net` | 80 | Vanilla JS | Tanıtım, SEO |
| 2 | Music | `music.coremusic.net` | 81 | PHP 8.4 + JS | Ana medya paneli |
| 3 | Admin | `admin.coremusic.net` | 80 | PHP 8.4 | Yönetim paneli |
| 4 | Download | `download.coremusic.net` | 3001 | Node.js + TS | İndirme servisi |
| 5 | Media | `media.coremusic.net` | 5000/6000 | PHP + FFmpeg | Medya işleme |
| 6 | Auth | `auth.coremusic.net` | — | PHP 8.4 | Kimlik doğrulama |
| 7 | Home | `home.coremusic.net` | — | Vanilla JS | Ev medya merkezi |
| 8 | Car | `car.coremusic.net` | — | Vanilla JS | Araç içi bilgi-eğlence |
| 9 | Studio | `studio.coremusic.net` | — | Vanilla JS | Stüdyo kontrolü |
| 10 | Pro | `pro.coremusic.net` | — | Vanilla JS | Profesyonel panel |

### 2.1 Panel Detayları

#### Panel 1: Landing (coremusic.net)
- **Amaç:** Platform tanıtımı, SEO optimizasyonu
- **Stack:** Vanilla JS, ITCSS
- **Port:** 80
- **Özellikler:** Responsive, erişilebilir, hızlı yükleme

#### Panel 2: Music (music.coremusic.net)
- **Amaç:** Ana medya paneli — müzik dinleme, yönetme, keşfetme
- **Stack:** PHP 8.4 (backend) + Vanilla JS (frontend)
- **Port:** 81 (Kritik: ADR-042)
- **Özellikler:** SPA router, playlist, album, artist, search

#### Panel 3: Admin (admin.coremusic.net)
- **Amaç:** Sistem yönetimi, kullanıcı yönetimi, içerik yönetimi
- **Stack:** PHP 8.4
- **Port:** 80
- **Özellikler:** Dashboard, CRUD operations, audit logs

#### Panel 4: Download (download.coremusic.net)
- **Amaç:** Müzik indirme servisi (Deezer, YouTube)
- **Stack:** Node.js + TypeScript
- **Port:** 3001
- **Özellikler:** Queue management, anti-ban, FLAC output

#### Panel 5: Media (media.coremusic.net)
- **Amaç:** Medya işleme, transcoding, metadata extraction
- **Stack:** PHP + FFmpeg
- **Port:** 5000/6000
- **Özellikler:** File-based vault, auth-gated access

#### Panel 6: Auth (auth.coremusic.net)
- **Amaç:** Merkezi kimlik doğrulama (Identity Provider)
- **Stack:** PHP 8.4
- **Port:** — (subdomain bazlı)
- **Özellikler:** SSO, JWT, session, RBAC

#### Panel 7: Home (home.coremusic.net)
- **Amaç:** Ev medya merkezi (RPi5 optimized)
- **Stack:** Vanilla JS
- **Port:** —
- **Özellikler:** Touch-friendly, multi-room, NAS integration

#### Panel 8: Car (car.coremusic.net)
- **Amaç:** Araç içi bilgi-eğlence sistemi
- **Stack:** Vanilla JS
- **Port:** —
- **Özellikler:** Touch-optimized, simplified UI, voice-ready

#### Panel 9: Studio (studio.coremusic.net)
- **Amaç:** Profesyonel stüdyo kontrolü
- **Stack:** Vanilla JS
- **Port:** —
- **Özellikler:** 8.1 surround, multi-track, ASIO integration

#### Panel 10: Pro (pro.coremusic.net)
- **Amaç:** Profesyonel kullanıcı paneli
- **Stack:** Vanilla JS
- **Port:** —
- **Özellikler:** Advanced EQ, DSP controls, spectrum analysis

---

## 3. 7 Backend Servis

*Detaylı metadata: [[../../brain.md]] §4B, [[ADR-039]]*

| # | Servis | Port | Protocol | Stack | Sorumluluk |
|---|--------|------|----------|-------|------------|
| 1 | Control Service | 81 | HTTP | PHP 8.4 | Auth, session, RBAC |
| 2 | Media Service | 5000/6000 | HTTP | PHP + FFmpeg | Library, metadata, streaming |
| 3 | Audio Service | 9741/9742 | REST/WS | C++20 JUCE | Player, DSP, mixer, EQ |
| 4 | Device Service | — | BLE/WiFi/USB | C++20 | Bluetooth, WiFi, USB |
| 5 | Network Audio | — | WebRTC/P2P | C++20 | Streaming, multi-room |
| 6 | AI Service | — | Internal | PHP + Python | Recommendations |
| 7 | Download Service | 3001 | HTTP/WS | Node.js + TS | Deezer/YouTube indirme |

### 3.1 Servis İletişim Kuralları

- Servisler birbirini **doğrudan çağırmaz** — Event Driven (ADR-086)
- Tüm istemciler **API Gateway** üzerinden bağlanır (ADR-084)
- Servis sınırları **kesin çizgilerle** ayrılmıştır
- Bir servis başka bir servisin DB'ye **erişemez**

---

## 4. 10 Subdomain

*Detaylı metadata: [[../../brain.md]] §9, [[ADR-043]]*

| # | Subdomain | Amaç | Port |
|---|-----------|------|------|
| 1 | `auth.coremusic.net` | Merkezi kimlik servisi | — |
| 2 | `home.coremusic.net` | Ev medya merkezi | — |
| 3 | `pro.coremusic.net` | Profesyonel panel | — |
| 4 | `studio.coremusic.net` | Stüdyo kontrolü | — |
| 5 | `car.coremusic.net` | Araç içi sistem | — |
| 6 | `admin.coremusic.net` | Yönetim paneli | 80 |
| 7 | `download.coremusic.net` | İndirme servisi | 3001 |
| 8 | `media.coremusic.net` | Medya servisi | 5000/6000 |
| 9 | `api.coremusic.net` | API Gateway | — |
| 10 | `coremusic.net` | Landing page | 80 |

### 4.1 Subdomain İzin Verilen Portlar

| Port | Protokol | Kullanım |
|------|----------|----------|
| 80 | HTTP | Admin, Landing |
| 81 | HTTP | Music (Control Service) |
| 443 | HTTPS | Tüm subdomainler |
| 3001 | HTTP/WS | Download Service |
| 5000/6000 | HTTP | Media Service |
| 9741 | HTTP | Audio Service (REST) |
| 9742 | WS | Audio Service (WebSocket) |
| 3306 | TCP | MySQL 18 BCNF DB |

---

## 5. Port Haritası

*Detaylı metadata: [[../../brain.md]] §11*

| Port | Servis | Protokol | Katman |
|------|--------|----------|--------|
| 80 | admin.coremusic.net | HTTP | L2 Routing |
| 81 | music.coremusic.net (Control) | HTTP | L2 Routing |
| 3001 | download.coremusic.net | HTTP/WS | L5 Services |
| 3306 | MySQL 18 BCNF DB | TCP | L0 Infrastructure |
| 5000/6000 | media.coremusic.net | HTTP | L5 Services |
| 9741 | Audio Service (REST) | HTTP | L6 Electronics |
| 9742 | Audio Service (WebSocket) | WS | L6 Electronics |
| 9743 | Neva Player | WS | L6 Electronics |

---

## 6. 18 BCNF Veritabanı

*Detaylı metadata: [[ADR-040]], [[../../architecture/05-data/]]*

| # | Veritabanı | Amaç | Tablo |
|---|------------|------|-------|
| 1 | `coremusic_auth` | Users, roles, sessions, tokens | 13 |
| 2 | `coremusic_user` | Profiles, preferences, history | 7 |
| 3 | `coremusic_musics` | Songs, artists, genres, lyrics | 22 |
| 4 | `coremusic_albums` | Album collections, discs | 5 |
| 5 | `coremusic_playlist` | User and AI playlists | 5 |
| 6 | `coremusic_catalog` | Reference data | 8 |
| 7 | `coremusic_logs` | Audit trail, analytics | 22 |
| 8 | `coremusic_media` | Device sync, media metadata | 8 |
| 9 | `coremusic_system` | Settings, config, cache | 17 |
| 10 | `coremusic_social` | Comments, shares, activity | 9 |
| 11 | `coremusic_wireless` | WiFi + Bluetooth networks | 5 |
| 12 | `coremusic_ai` | User preferences, recommendations | 6 |
| 13 | `coremusic_api` | API keys, rate limits | 4 |
| 14 | `coremusic_cms` | Pages, blog, tags | 8 |
| 15 | `coremusic_download` | Download queue, history | 4 |
| 16 | `coremusic_neva` | EQ presets, DSP settings | 4 |
| 17 | `coremusic_studio` | Studio sessions, tracks | 6 |
| 18 | `coremusic_patch` | Schema versions, migrations | 3 |
| | **TOPLAM** | | **156** |

### 6.1 DB Kuralları

- ORM yasak (ADR-002) — sadece PDO prepared statement
- SELECT * yasak — explicit column list zorunlu
- BCNF zorunlu (ADR-040)
- Soft delete: `is_deleted = 0`
- snake_case naming
- UUID v7 + INT karisik PK

---

## 7. Enterprise Composer Stack

*Detaylı metadata: [[../../brain.md]] §4A, [[ADR-085]]*

### 7.1 Shared Library Yapısı (ADR-085 v3.0)

Tek `shared/` dizini + PSR-4 namespace ile modüler ayrım:

| Kategori | Paket | PSR |
|----------|-------|-----|
| Contracts | `coremusic/contracts` | — |
| HTTP | `coremusic/http` | PSR-7/17/18 |
| Auth | `coremusic/auth` | — |
| Security | `coremusic/security` | — |
| Cache | `coremusic/cache` | PSR-6 |
| Events | `coremusic/events` | PSR-14 |
| Validation | `coremusic/validation` | — |
| Storage | `coremusic/storage` | — |
| Logger | `coremusic/logger` | PSR-3 |
| SDK | `coremusic/sdk` | — |
| API Client | `coremusic/api-client` | — |
| Queue | `coremusic/queue` | — |
| WebSocket | `coremusic/websocket` | — |

**Kural:** Circular dependency yasak. `coremusic/contracts` hiçbir pakete bağımlı değildir.

### 7.2 PSR Standartları

```
psr/log, psr/container, psr/http-message, psr/http-server-handler,
psr/http-server-middleware, psr/http-factory, psr/http-client,
psr/event-dispatcher, psr/cache, psr/simple-cache
```

---

## 8. Audio Engine Kuralları

*Detaylı metadata: [[ADR-017]], [[../../brain.md]] §7*

### 8.1 Zero-Allocation Kuralı

| Yasak | İzin |
|-------|------|
| `malloc()`, `free()`, `new`, `delete` | Stack tahsisi |
| `std::make_shared`, `std::vector` push_back | `std::atomic` |
| I/O blocking | SIMD (SSE2/AVX2/NEON) |
| `throw` | `constexpr`, member değişkenler |

### 8.2 ASIO Callback

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

### 8.3 Thread & Cache

- Audio thread: `THREAD_PRIORITY_TIME_CRITICAL`
- writeHead/readHead: `alignas(64) std::atomic<size_t>`

---

## 9. Güvenlik Standartları

*Detaylı metadata: [[ADR-022]], [[ADR-034]], [[../../architecture/l1-security/]]*

### 9.1 Şifreleme Parametreleri

| Parametre | Değer |
|-----------|-------|
| AES-256-GCM IV | 96-bit (12 byte) |
| AES-256-GCM Tag | 16 byte |
| AES-256-GCM Key | 256-bit (32 byte) |
| Argon2id Memory | 64MB |
| Argon2id Time | 4 iterations |
| Argon2id Threads | 2 |
| CSRF Token Key | `csrf_token` (NOT `_csrf_token`) |
| CSP Nonce | `base64_encode(random_bytes(32))` |

### 9.2 OWASP Top 10:2025 Uyumluluğu

| OWASP | CoreMusic Karşılama |
|-------|---------------------|
| A01 Broken Access Control | RBAC + Permission Guard |
| A02 Security Misconfiguration | CSP strict-dynamic + SecurityHeaders |
| A03 Supply Chain Failures | Composer audit + GitLeaks |
| A04 Cryptographic Failures | AES-256-GCM + Argon2id + RS256 |
| A05 Injection | Prepared statements + DOMParser |
| A06 Insecure Design | Clean Architecture + DDD |
| A07 Authentication Failures | Hybrid Auth + MFA + Rate Limit |
| A08 Software Integrity | CSRF token + JWT signature |
| A09 Logging & Alerting | PSR-3 + audit trail |
| A10 Exceptional Conditions | Error hierarchy + graceful degradation |

---

## 10. 20 Analiz Görevi

*Detaylı metadata: [[../../WORKFLOW.md]] §8.1A*

Her analiz görevinde aşağıdaki 20 adımlık kontrol listesi uygulanır:

| # | Görev | Kaynak | Çıktı |
|---|-------|--------|-------|
| 1 | Kod tabanını tara | Tüm dosyalar | Dosya listesi + yapı |
| 2 | Mimariyi analiz et | L0-L6 katmanları | Katman haritası |
| 3 | Katman ihlallerini tespit et | L0→L2/L3 | İhlal listesi |
| 4 | SOLID ihlallerini raporla | SRP/OCP/LSP/ISP/DIP | İhlal listesi |
| 5 | Clean Code ihlallerini raporla | Okunabilirlik | İhlal listesi |
| 6 | Code Smell raporu | Long methods, large classes | Smell listesi |
| 7 | Duplicate Code analizi | Tekrarlayan kod | Duplicate listesi |
| 8 | Dependency analizi | Modül bağımlılıkları | Bağımlılık grafiği |
| 9 | Dead Code analizi | Kullanılmayan kod | Dead code listesi |
| 10 | Unused Class analizi | Kullanılmayan sınıflar | Class listesi |
| 11 | Unused Method analizi | Kullanılmayan metodlar | Method listesi |
| 12 | Security analizi | OWASP Top 10 | Güvenlik raporu |
| 13 | Performans analizi | CPU, Memory, Query | Performans raporu |
| 14 | SPA Router analizi | Routing yapısı | Router haritası |
| 15 | Authentication Flow analizi | Login/logout akışı | Auth akış diyagramı |
| 16 | Session yönetimini analiz | Cookie, timeout | Session raporu |
| 17 | Middleware Pipeline analizi | Sıra, bağımlılıklar | Pipeline haritası |
| 18 | Service bağımlılıklarını analiz | Servisler arası | Service dependency graph |
| 19 | Refactoring Planı oluştur | Öncelikli düzeltme | Refactoring planı |
| 20 | Yeni mimariyi tasarla | Hedef yapı | Mimari diyagramlar |

### 10.1 Çıktı Formatı

Her analiz sonunda aşağıdaki bölümler oluşturulmalıdır:

1. **Mevcut Durum** — Şu anki durumun tanımı
2. **Tespit Edilen Problemler** — Bulunan sorunların listesi
3. **Risk Analizi** — Her problem için risk seviyesi
4. **Teknik Borçlar** — Gelecekte düzeltilmesi gerekenler
5. **Mimari İhlaller** — Katman/bağımlılık ihlalleri
6. **SOLID İhlalleri** — Prensip ihlalleri
7. **Clean Code İhlalleri** — Kod kalitesi sorunları
8. **Performans Problemleri** — Performans darboğazları
9. **Güvenlik Problemleri** — Güvenlik açıkları
10. **Refactoring Planı** — Düzeltme öncelikleri

---

## 11. Deployment Modları

*Detaylı metadata: [[../../CLAUDE.md]] §14*

| Mod | Platform | Donanım |
|-----|----------|---------|
| Home Media Center | Windows/Linux/macOS | PC/Laptop |
| Car Audio System | Windows/Android Auto | Raspberry Pi 5 / PCM3168A |
| Professional Studio | Windows (WASAPI/ASIO) | 8.1 Surround + Class AB |
| NAS Audio Server | Linux (Docker) | Synology/QNAP |
| DAC Control System | Windows/Linux | XMOS XU316 + PCM3168A |

---

## 12. Platform Tiers

*Detaylı metadata: [[../../CLAUDE.md]] §13*

| Tier | OS | Durum | Ses Sürücüsü |
|------|-----|-------|-------------|
| **Tier 1 (Primary)** | Windows (XP-11, Server 2012 R2+) | ✅ Ana geliştirme | ASIO, WASAPI |
| **Tier 2** | Linux (Ubuntu, Debian, Fedora) | ✅ Destekli | ALSA, PipeWire |
| **Tier 3** | macOS (Monterey–Sonoma) | ✅ Destekli | CoreAudio |
| **Tier 4** | Raspberry Pi (ARM64) | ✅ Destekli | I2S |
| **Tier 5** | ReactOS | ⚠️ Experimental | Sınırlı |

---

## 13. Test Kapsama Hedefleri

*Detaylı metadata: [[../../CLAUDE.md]] §17*

| Modül | Minimum | Hedef | Framework |
|-------|---------|-------|-----------|
| Backend (PHP) | ≥%80 | ≥%90 | PHPUnit 11 |
| Frontend (JS) | ≥%80 | ≥%90 | Vitest |
| Audio Engine (C++) | ≥%80 | ≥%90 | Google Test |
| Download Service | ≥%80 | ≥%90 | Vitest |

### 13.1 Test Piramidi

```
        ┌─────────┐
        │  E2E %10 │  ← Playwright
       ┌┴─────────┴┐
       │ Integration │  ← PHPUnit, Vitest
       │    %20      │
      ┌┴─────────────┴┐
      │    Unit %70     │  ← PHPUnit, Vitest, Google Test
      └────────────────┘
```

---

## 14. Hard Guardrails Özeti

*Detaylı metadata: [[../../CLAUDE.md]] §7, [[prompt-shared-base]] §7*

| # | Kural | Katman |
|---|-------|--------|
| 1 | Zero Code Before Plan | Genel |
| 2 | Vault First | Genel |
| 3 | Zero Hallucination | Genel |
| 4 | In-Place Refactoring | Genel |
| 5 | Single Source of Truth | Genel |
| 6 | CSRF Token = `csrf_token` | L1 Security |
| 7 | Middleware Order Immutable | L1 Security |
| 8 | Port 81 = music.coremusic.net | L2 Routing |
| 9 | No ORM | L0 Infrastructure |
| 10 | No Frameworks | L3 Presentation |
| 11 | Mockup Before Frontend | L3 Presentation |
| 12 | Contradiction Gate | Genel |
| 13 | Session Continuity | Genel |
| 14 | Human Approval Gate | Genel |
| 15 | Vault-First Mandatory | Genel |
| 16 | Template Mandatory | Genel |

---

## 15. Prompt-Domain Eşleşmesi

*Detaylı metadata: [[../../CLAUDE.md]] §26.2, [[../../AGENTS.md]] §14.1*

| Prompt | Sorumlu Agent | Kullanım Anı |
|--------|---------------|-------------|
| prompt0 (bu dosya) | MO (dağıtıyor) | Her görev başında zorunlu |
| prompt1 (SPA Router) | Backend Architect, UI Designer | SPA route tasarımında |
| prompt2 (Auth) | Security Engineer, Backend Architect | Auth middleware'de |
| prompt3 (API) | Backend Architect, DevOps Engineer | API gateway'de |

**Kural:** prompt0 her zaman okunur. prompt1-3 sadece ilgili domain görevlerinde okunur.

---

## 16. Referans Proje Kuralları

*Detaylı metadata: [[../../WORKFLOW.md]] §8.1C*

Referans proje (`C:\www\coremusic.net.old.ref`) incelenirken:

- **KESİNLİKLE kopyalanmayacak:** Auth kodları, Router, Middleware, Session, Login, Controller, Service
- **Sadece referans:** Mimari, klasör yapısı, katman ayrımı, tasarım yaklaşımı
- **Kod tekrar kullanılmayacaktır** — Tüm sistem sıfırdan geliştirilecektir

---

## 17. Cross References

| Bölüm | Hedef Vault Dosyası | İlişki |
|-------|---------------------|--------|
| §2 Paneller | [[../../brain.md]] §9 | 10 panel |
| §3 Servisler | [[../../brain.md]] §4B | 7 servis |
| §4 Subdomainler | [[ADR-043]] | Auth konsolidasyonu |
| §5 Portlar | [[../../brain.md]] §11 | Port haritası |
| §6 DB | [[ADR-040]] | 18 BCNF |
| §7 Composer | [[../../brain.md]] §4A | shared/ hybrid yapı |
| §8 Audio | [[ADR-017]] | C++ kuralları |
| §9 Güvenlik | [[ADR-022]] | Şifreleme |
| §10 Analiz | [[../../WORKFLOW.md]] §8.1A | 20 görev |
| §11 Deploy | [[../../CLAUDE.md]] §14 | 5 mod |
| §12 Platform | [[../../CLAUDE.md]] §13 | 5 tier |
| §13 Test | [[../../CLAUDE.md]] §17 | Coverage |
| §14 Guardrails | [[../../CLAUDE.md]] §7 | 16 kural |
| §15 Prompt | [[../../CLAUDE.md]] §26 | Eşleşme |

---

*Prompt 0: Genel Ana Prompt v2.0.0 — CoreMusic Prompt System*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-08-15*
*Mode: Red Team · Human Mode · Truth Mode*
