---
title: "CoreMusic — Mimari Kurallar"
type: guide
category: governance
date: 2026-10-22
updated: 2026-10-22
version: 1.0.0
status: active
authority: "Derived — SSOT: .ai/CLAUDE.md §5-§6B, §9-§13"
docType: claude
---

# CoreMusic — Mimari Kurallar (Architecture Rules)

**docType:** claude · **Klasör:** `.agents/rules/` · **Sorumlu:** architect + MO

**Zorunlu Bağlantılar:** [[RULES]] · [[../../.ai/CLAUDE]] · [[../../.ai/AGENTS]] · [[../workflows/AGENTS]]

> **eli10:** Sistemin katmanları, katmanlar arası tek yön bağımlılık, middleware sırası ve servis/port haritası bu dosyadadır; asıl metin vault'tadır.

---

## 1. Amaç

CoreMusic mimarisinin **katman tanımı, bağımlılık matrisi, middleware sırası, API-First akışı, panel/servis/port haritası** kurallarını vault SSOT'larından özetler. Mimari karar öncesi kullanıcı onayı zorunlu (Guardrail #14).

| Vault kaynağı | Ne alındı |
|---------------|-----------|
| `@.ai/CLAUDE.md` §5 | K0-K15 katman tablosu + hard guardrails |
| `@.ai/CLAUDE.md` §5.1 | Layer Dependency Matrix |
| `@.ai/CLAUDE.md` §6 | Middleware Pipeline (immutable, ADR-010/011/012/013/022) |
| `@.ai/CLAUDE.md` §6A-§6B | API-First, Gateway, BFF×6, CQRS, Event Bus, Shared lib |
| `@.ai/CLAUDE.md` §9-§13 | 10 Panel · 7 Servis · Port Register · Tech Stack · Tier |
| `@.ai/.decisions/index` | ADR referansları (frozen — dokunulmaz) |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Katman tanımı ve bağımlılık yönü | Dil kuralı → [[backend-rules]] · [[frontend-rules]] |
| Middleware sırası ve pipeline | Şifreleme/secret → [[security-rules]] |
| API-First / BFF / CQRS / Event Bus | Şema/BCNF → [[database-rules]] |
| Panel/servis/port haritası | DSP/ses motoru → [[audio-hardware-rules]] |

---

## 3. Mimari

### 3.1 K0-K15 Katmanları (`@.ai/CLAUDE.md` §5 — hedef mimari; bileşen sayıları vault ölçümüdür)

| Katman | Kapsam | Hard Guardrail |
|--------|--------|----------------|
| K15 Medya & Streaming | FFmpeg, FLAC, HLS, DASH, ID3, Radio | Yalnız K14 üzerinden iletişim |
| K14 Ağ & İletişim | HTTP/3, WebRTC P2P, DLNA, UPnP, AirPlay, mDNS | Ağ iletişim standardı |
| K13 CI/CD & Deploy | GitHub Actions, Playwright, Vitest, Docker, K8s | Build/deploy otomasyonu |
| K12 İzleme & Log | Prometheus, Grafana, Sentry, Matomo, Audit | Yalnız K8'den okuma |
| K11 Kullanıcı Deneyimi | ITCSS, BEM, Design Tokens, PWA, WCAG 2.2 AA | Sadece K10 tetikler |
| K10 Uygulama | 10 Panel (music, admin, home, car, studio, download, landing, pro, media, auth) | **Yalnız K9 API üzerinden** |
| K9 API & Routing | Gateway, BFF×6, CQRS, Event Bus, SPA Router, OpenAPI | OpenAPI sözleşmesi ihlal edilemez |
| K8 Servis | Control, Media, Audio, Device, Network, AI, Download + Infra (11) | **Servisler arası doğrudan çağrı YASAK — Event Bus** |
| K7 Middleware | PSR-15 zinciri (§3.3) | K6 doğrulamasını atlayamaz |
| K6 Güvenlik | JWT, RBAC, CSRF, CSP, RateLimit, AES-256, Vault, Audit | **Asla bypass edilemez** |
| K5 Veri Yönetimi | MySQL 9 (18 BCNF, 156 tablo), APCu, SQLite, Restic | 18 DB kesin BCNF |
| K4 Yapay Zeka | Music Analysis, Recommendation, Voice, Edge AI | K5 harici veriye erişemez |
| K3 Ses İşleme | Neva Engine, DSP Chain (31-band EQ, 4 Reverb), Dolby Atmos, DTS:X | Sıfır gecikme; K2'ye sıkı bağımlı |
| K2 Sürücü | ASIO, WASAPI, ALSA, PipeWire, CoreAudio, I2S, USB, BT, DLNA | Donanım-yazılım köprüsü |
| K1 Donanım | XMOS XU316, PCM3168A, AK4458, 8×Class AB | DC-Only + Class AB; parazit yasak |
| K0 İşletim Sistemi | Win, Lin, Mac, RPi5, Docker, Cross-Platform API | Çekirdek servisler |

### 3.2 Layer Dependency Matrix (`@.ai/CLAUDE.md` §5.1)

| Kaynak → Hedef | İzinli |
|----------------|--------|
| L6→L5 · L5→L4 · L4→L3 · L3→L2 · L2→L1 · L1→L0 | ✅ |
| L0→L2/L3 · L1→L3 · L3→L0 | ❌ **Layer Violation → derhal revert + log CRITICAL** |

### 3.3 Middleware Pipeline — IMMUTABLE (ADR-010/011/012/013/022 — `@.ai/CLAUDE.md` §6)

```
OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf → BypassAuth → Auth → Permission → Validation → Controller
```

| # | Middleware | Görev | Timeout |
|---|-----------|-------|---------|
| 1 | OriginCheck | Köken doğrulama (whitelist CORS) | — |
| 2 | Cors | CORS header | — |
| 3 | RateLimiter | APCu, 60 req/60s | 60s |
| 4 | SecurityHeaders | CSP strict-dynamic, X-Frame-Options, HSTS | — |
| 5 | SessionManager | Session + CSP nonce kaydı | 3600s idle |
| 6 | Csrf | `csrf_token` doğrulama (POST/PUT/DELETE) | — |
| 7 | BypassAuth | `?_bypass=1` test bypass — prod'da devre dışı | — |
| 8 | Auth | JWT + Session inject | — |
| 9 | Permission | RBAC (regular/premium/studio/car/admin/system) | — |
| 10 | Validation | Request/DTO doğrulama | — |

**Kritik:** CSP nonce #4'te üretilir, #5'te session'a kaydedilir. **Sıra değiştirilirse CSP bozulur — Guardrail #7 ihlali → sistem durdurulur.**

### 3.4 API-First (ADR-084 — `@.ai/CLAUDE.md` §6A)

- **Hiçbir endpoint doğrudan kodlanmaz:** `OpenAPI Spec → DTO → Contract → Validation → Use Case → Kod`
- **Tek giriş:** `api.coremusic.net` (Gateway: routing, auth, rate limit, validation, logging, correlation ID)
- **BFF×6:** SPA (tam) · Mobile (minimal) · Embedded/RPi5 (ultra-minimal gzip) · Desktop (orta) · Admin (full+audit) · Car (touch-optimized)
- **CQRS:** `Write: Command→UseCase→Repository→MySQL Master` · `Read: Query→ReadModel→Cache→Response`
- **Event Bus (ADR-086):** `Service A → PSR-14 Event Bus → Service B/C D` — doğrudan çağrı yasak
- **SPA → ApiClient kuralı:** SPA **asla** PDO/MySQL/Repository/Entity/Infrastructure/Filesystem/FFmpeg/Redis/Cache/SQL **görmez**

### 3.5 Shared Library (ADR-085 v3.0 — `@.ai/CLAUDE.md` §6B)

Tek `shared/` dizini + PSR-4 namespace; tek composer paketi `coremusic/shared` (`shared/composer.json`).

### 3.6 Portlar & Servisler (`@.ai/CLAUDE.md` §10-§11)

| Port | Servis |
|------|--------|
| 80 | admin.coremusic.net / landing (coremusic.net) |
| **81** | **music.coremusic.net (Control Service) — Guardrail #8: bu port değişmez** |
| 3001 | download.coremusic.net (Node.js + TS) |
| 3306 | MySQL 18 BCNF |
| 5000/6000 | media.coremusic.net (PHP + FFmpeg) |
| 9741 / 9742 | Audio Service REST / WebSocket (C++20 JUCE) |

**7 Backend Service:** Control (81) · Media (5000/6000) · Audio (9741/9742) · Device (BLE/WiFi/USB) · Network Audio (WebRTC/P2P) · AI (PHP+Python) · Download (3001).

**Gerçeklik notu (vault §9):** Kod ağacında fiziksel olarak mevcut paneller: auth ✅, home ✅ (+ assets). Diğer panel dizinleri hedef mimaridir — "var" diye iddia edilmez (Zero-Hallucination).

### 3.7 Teknoloji Sabitleri (`@.ai/CLAUDE.md` §12, §24 + kök `CLAUDE.md` Web Verification 2026-10-07)

PHP 8.4+ · Vanilla JS ES2022 · ITCSS+BEM · C++20 + JUCE 9 + ASIO SDK 2.3.4 · MySQL 9 (18 BCNF) · APCu (Redis: PLANNED — adapter yok) · Docker 24+ · GitHub Actions · FFmpeg · Node.js LTS · XMOS XU316 + PCM3168A + AK4458 + MJL21194/93 Class AB (ADR-089).

---

## 4. Kurallar

| # | Kural | İhlal |
|---|-------|-------|
| 1 | Katman bağımlılığı tek yön (§3.2) | Revert + CRITICAL log |
| 2 | Middleware sırası değiştirilemez (§3.3) | Sistem durdurulur (Guardrail #7) |
| 3 | Endpoint önce OpenAPI (§3.4) | Kod geçersiz |
| 4 | Servisler arası yalnız Event Bus (K8) | Revert |
| 5 | SPA alt katmanı görmez (§3.4) | Revert |
| 6 | Mimari karar öncesi kullanıcı onayı (Guardrail #14) | Revert |
| 7 | Panel/servis "durum" iddiası disk kanıtlı yazılır | `VERIFICATION REQUIRED` |
| 8 | Frozen ADR'ler yalnız referans alınır, değiştirilmez | Dosya geri yüklenir |

---

## 5. Workflow

```text
Mimari iş talebi
  → Guardrail #14 (kullanıcı onayı — mimari karar)
  → Vault okuma: @.ai/CLAUDE.md §5-§6B + @.ai/brain.md (ADR'ler)
  → Katman/servis/port etkisi analizi (§3.1-§3.6)
  → Layer Violation taraması (§3.2) + Middleware sıra kontrolü (§3.3)
  → Uygulama → mimari denetim (architect-reviewer)
  → log.md append (MO)
```

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---------|--------|
| 1 | Frontmatter 7 alan + docType | Mevcut |
| 2 | Katman tablosu §5 ile eşleşme | K0-K15 eksiksiz |
| 3 | Middleware 10 adet ve sıra birebir | §3.3 = vault §6 |
| 4 | Portlar vault §11 ile eşleşme | 81 = music (Guardrail #8) |
| 5 | Gerçeklik notu (§3.6) korundu | Uydurma "✅ mevcut" yok |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|--------|-----|------|
| Anayasa §5-§6B | [[../../.ai/CLAUDE]] | Katman + middleware + API-first |
| Anayasa §9-§13 | [[../../.ai/CLAUDE]] | Panel/servis/port/stack |
| ADR indeksi | [[../../.ai/.decisions/index]] | Frozen kararlar |
| Mimari kararlar | [[../../.ai/brain]] | ADR özetleri |
| Master kurallar | [[RULES]] | Genel defter |

**Last Updated:** 2026-10-22 · **Şablon:** context-template (Guardrail #16)
