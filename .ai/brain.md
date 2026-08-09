---
title: "CoreMusic — Engineering Brain (Enterprise SSOT)"
type: brain
category: architecture-decisions
updated: 2026-08-10
status: active
version: 21.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# CoreMusic — Engineering Brain (Enterprise SSOT)

**Zorunlu Bağlantılar:** [[CLAUDE.md]] · [[AGENTS.md]] · [[WORKFLOW.md]] · [[index.md]] · [[keys.md]] · [[MEMORY.md]] · [[log.md]]

---

## 1. Amaç

CoreMusic platformunun tüm mühendisleri ve AI ajanları için mimari kararların, donanım/yazılım kısıtlamalarının ve ses işleme spesifikasyonlarının tutulduğu Ana Mühendislik Hafızasıdır (SSOT). `index.md` harita ise, bu belge mühendislik defteridir.

---

## 2. Scope

C++ Audio DSP (ASIO, WASAPI, ring buffer, zero-allocation, 32-bit float PCM), 8+1 Surround (Class AB, XMOS XU316, PCM3168A/AK4458), PHP Middleware Pipeline (SessionManager→Csrf), 11 BCNF DB, AES-256-GCM Credential Vault, 10 panel mimarisi, AI Auto-Download (YouTube→deemix→FLAC), 3 fazlı geliştirme, 5 deployment modu, 5 audio division.

---

## 3. Core Principles

| Prensipl | Açıklama |
|----------|----------|
| SOLID | Tek Sorumluluk, Açık Kapalılık, Yerine Koyma, Arayüz Ayrımı, Bağımlılık Tersi |
| Clean Architecture (L0-L3) | Infrastructure → Security → Routing → Presentation |
| Hexagonal Architecture | Adapter/Port pattern ile bağımsızlık |
| DRY | Tekrarlanan kod yasağı |
| YAGNI | Gereksiz özellik ekleme yasağı |
| Real-Time Thread Model | Audio thread'de blocking operations yasak |
| Zero Code Before Plan | Plan onayı olmadan kod yazma yasağı (ADR-007) |
| MSA Limit | Görev başına max 15 dosya (ADR-042/C5) |

---

## 4. Tech Stack

| Katman | Teknoloji | Versiyon |
|--------|-----------|----------|
| Backend | PHP (strict_types=1) | 8.4+ |
| Frontend | Vanilla JS ES6+ (framework YASAK) | ES2022 |
| CSS | ITCSS + BEM | 7-layer |
| Database | MySQL / MariaDB (PDO, ORM YASAK) | 11 BCNF |
| Audio Engine | C++20, JUCE 9, ASIO SDK 2.3.4 | — |
| Hardware | XMOS XU316, PCM3168A | PCM5122 REDDEDİLMİŞ |
| Rate Limiting | APCu | 60 req/60s |
| Encryption | AES-256-GCM, Argon2id | NIST SP 800-38D |

---

## 5. Architecture Layers L0-L3

| Katman | İçerik | Teknoloji |
|--------|--------|-----------|
| L3 Presentation | Frontend, UI, DOM | Vanilla JS, ITCSS, TrustedTypes |
| L2 Routing | Router, middleware, dispatch | PHP 8.4 PageRouter |
| L1 Security | Session, Auth, CSRF, CSP | Middleware Pipeline |
| L0 Infrastructure | Database, cache, fs | PDO, APCu, Redis |

Bağımlılık: ✅ L3→L2, L2→L1, L1→L0 | ❌ L0→L2/L3, L1→L3, L3→L0. Layer Violation → derhal revert.

---

## 6. Middleware Pipeline (Sıra Değişmez — ADR-010/011/012/013/022)

```
1. SessionManagerMiddleware()    — Session başlat, CSP nonce üret
2. BypassAuthMiddleware()        — Test bypass (prod'da devre dışı)
3. RateLimiterMiddleware()       — APCu: 60 req/60s
4. AuthMiddleware()              — Auth bilgisi inject
5. SecurityHeadersMiddleware()   — CSP strict-dynamic
6. CsrfMiddleware()              — csrf_token doğrulama (POST/PUT/DELETE)
```

CSP nonce üretimi SessionManager içindedir. Sıra değiştirilirse CSP bozulur.

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

## 11. 11 BCNF Databases (ADR-040)

| # | Veritabanı | Amaç | Tablo Sayısı |
|---|------------|------|-------------|
| 1 | coremusic_auth | Kullanıcılar, roller, session, token, credential vault, API key | 12 |
| 2 | coremusic_user | Profiller, tercihler, geçmiş, favoriler | 7 |
| 3 | coremusic_musics | Şarkılar, sanatçılar, türler, sözler, dosyalar | 12 |
| 4 | coremusic_albums | Albüm koleksiyonları | 5 |
| 5 | coremusic_playlist | Kullanıcı ve AI çalma listeleri | 5 |
| 6 | coremusic_catalog | Referans verileri (tür listesi, sanatçı rolleri, enstrümanlar) | 8 |
| 7 | coremusic_logs | Audit trail, analitik, hata logları | 13 |
| 8 | coremusic_media | Cihaz senkronizasyonu, medya metadata | 8 |
| 9 | coremusic_system | Ayarlar, config, cache, EQ, dosya yöneticisi, bildirimler | 13 |
| 10 | coremusic_social | Yorumlar, paylaşımlar, aktivite, dinleme odaları | 9 |
| 11 | coremusic_wireless | WiFi + Bluetooth ağları | 5 |

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

### 13.2 Active (038-063)

| ADR | Konu |
|-----|------|
| ADR-038 | XMOS XU316 + PCM3168A (PCM5122 REDDEDİLMİŞ) |
| ADR-039 | 7-servis platform mimarisi |
| ADR-040 | 11 BCNF veritabanı otoritesi |
| ADR-041 | DB normalizasyon ek bilgi |
| ADR-042 | MSA limit=15, PHP 8.4, port 81 |
| ADR-043 | Auth subdomain konsolidasyonu |
| ADR-044 | Cinsiyet bazlı dinamik tema |
| ADR-045 | Multi-domain view mode |
| ADR-046 | Cross-view state koruma |
| ADR-047 | Login redirect session bridge |
| ADR-048 | View Transition API entegrasyonu |
| ADR-049 | Startup prompt loader |
| ADR-050 | Multi-DB sync stratejisi |
| ADR-061 | Electronics Architecture (L6 Layer) |
| ADR-062 | DSP Pipeline Architecture |
| ADR-063 | Hardware Design Standards |

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
| 8 | MSA Limit: Görev başına max 15 dosya | Token aşımı |
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
| CSS | ITCSS 7-layer, BEM+BEMIT, custom properties, main.css sadece 01-07 |

---

## 19. Edge Cases

| Edge Case | Tetikleyici | Çözüm | ADR |
|-----------|-------------|-------|-----|
| ASIO Device Loss | USB kopması | WASAPI fallback → Null Output | [[ADR-017-dsp-hardware-mode]] |
| Cache Stampede | Yüksek load | Mutex ile single load | [[architecture/l0-infrastructure]] |
| Multi-Tab CSRF | Birden fazla sekme | Token session-bound sabit | [[ADR-010-csrf-protection-strategy]] |
| Layer Violation | L0 → L3 import | Derhal revert | [[CLAUDE.md]] |
| PCM5122 Kullanımı | 8.1 surround denemesi | PCM3168A veya AK4458 | [[ADR-038-8.1-sound-card-chip-selection]] |
| Network Outage | İnternet kopması | Offline-First + SQLite queue | [[architecture/01-overview/overview]] |
| MSA Limit Aşımı | >15 dosya task | Index fallback | [[ADR-042-vault-restructuring-2026-08-03]] |
| BCNF Violation | Yeni tablo | 3NF → BCNF audit | [[ADR-040-database-authority]] |
| Buffer Underrun | CPU %100 | Fade-out → 50ms sessizlik → restart | [[engine.md]] |
| Session Timeout | 3600s idle | Otomatik yeniden auth | [[ADR-011-session-management]] |

---

## 20. Warnings

| # | Uyarı |
|---|-------|
| 1 | **H001:** PCM5122 ile 8.1 surround YAPILAMAZ. Sadece 2 kanal destekler. PCM3168A veya AK4458 kullanın. |
| 2 | **Middleware:** Sıra değiştirilmez. CSP nonce SessionManager'da üretilir. |
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
| § Mimari | [[architecture/01-overview/overview]] | L0-L3 |
| § C++ Audio | [[ADR-017-dsp-hardware-mode]] | XMOS, JUCE |
| § 8.1 Surround | [[ADR-038-8.1-sound-card-chip-selection]] | PCM3168A, H001 |
| § PHP Middleware | [[ADR-010-csrf-protection-strategy]] | csrf_token |
| § Cache/Vault | [[ADR-022-database-hardened-security]] | AES-256-GCM |
| § 11 BCNF DB | [[ADR-040-database-authority]] | 11 DB |
| § Audio Org | [[electronic/audio-organization]] | 5 bölüm |
| § Hardware | [[electronic/hardware-roadmap]] | 3 fazlı yol haritası |

---

## 22. Quality Report

| Metrik | Değer |
|--------|-------|
| Version | 19.0.0 |
| Status | Red Team · Human Mode · Truth Mode verified |
| ADR Coverage | 001–050 (50 ADR) |
| Panel Count | 10 |
| Service Count | 7 |
| DB Count | 11 BCNF |
| Audio Channels | 8+1 Surround |
| EQ Bands | 31 |
| Hardware Phases | 3 (MVP → Premium → Professional) |
| Platform Tiers | 5 |
| Hard Guardrails | 14 |
| Edge Cases | 10 |
| Warnings | 7 |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode