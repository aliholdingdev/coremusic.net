---
title: "CoreMusic — Mimari Kurallar (Architecture Rules)"
type: guide
category: governance
date: 2026-10-22
updated: 2026-11-01
version: 2.0.0
status: active
authority: "Derived — SSOT: .ai/CLAUDE.md §5-§6B, §9-§13 · .ai/.rules/kurallar.md · .ai/.rules/karar-kurallari.md"
docType: claude
---

# CoreMusic — Mimari Kurallar (Architecture Rules)

**docType:** claude · **Klasör:** `.ai/.rules/` · **Sorumlu:** architect + Master Orchestrator

**Zorunlu Bağlantılar:** [[RULES.md]] · [[kurallar.md]] · [[karar-kurallari.md]] · [[senior-mode.md]] · [[.ai/CLAUDE.md]] · [[.ai/AGENTS.md]] · [[backend-rules.md]]

> **eli10:** Sistemin katmanları, katmanlar arası tek yön bağımlılık, middleware sırası, port haritası ve onaylı mimari kararlar bu dosyadadır; asıl metin vault'tadır.

---

## 1. Amaç

CoreMusic mimarisinin **katman tanımı, bağımlılık matrisi, middleware sırası, API-First akışı, panel/servis/port haritası ve ADR kararları** kurallarını vault SSOT'larından özetler. Mimari karar öncesi kullanıcı onayı zorunludur (Guardrail #14 / `KURAL-07`).

| Vault Kaynağı | Ne Alındı |
|---|---|
| `@.ai/CLAUDE.md` §5 | K0-K15 katman tablosu + hard guardrails |
| `@.ai/CLAUDE.md` §5.1 | Layer Dependency Matrix (L0-L6) |
| `@.ai/CLAUDE.md` §6 | Middleware Pipeline (immutable, ADR-010/011/012/013/022) |
| `@.ai/CLAUDE.md` §6A-§6B | API-First, Gateway, BFF×6, CQRS, Event Bus, Shared lib |
| `@.ai/CLAUDE.md` §9-§13 | 10 Panel · 7 Servis · Port Register · Tech Stack · Tier |
| `karar-kurallari.md` | ADR-112, 113, 114, 115 kuralları |
| `@.ai/.decisions/index.md` | ADR referansları (frozen — dokunulmaz) |

---

## 2. Kapsam

| Kapsam | Kapsam Dışı |
|--------|-------------|
| Katman tanımı ve bağımlılık yönü | Dil kuralı → [[backend-rules.md]] · [[frontend-rules.md]] |
| Middleware sırası ve pipeline | Şifreleme/secret → [[security-rules.md]] |
| API-First / BFF / CQRS / Event Bus | Şema/BCNF → [[database-rules.md]] |
| Panel/servis/port haritası | DSP/ses motoru → [[audio-hardware-rules.md]] |
| ADR-112 .. ADR-115 entegrasyonu | Süreç akışı → [[workflow-rules.md]] |

---

## 3. Mimari

### 3.1 K0-K15 Katmanları (`@.ai/CLAUDE.md` §5)

| Katman | Kapsam | Hard Guardrail |
|--------|--------|----------------|
| **K15** Medya & Streaming | FFmpeg, FLAC, HLS, DASH, ID3, Radio | Yalnız K14 üzerinden iletişim |
| **K14** Ağ & İletişim | HTTP/3, WebRTC P2P, DLNA, UPnP, AirPlay, mDNS | Ağ iletişim standardı |
| **K13** CI/CD & Deploy | GitHub Actions, Playwright, Vitest, Docker | Build/deploy otomasyonu; subagent commit yasak (`HL-03`) |
| **K12** İzleme & Log | Prometheus, Grafana, Sentry, Matomo, Audit | Yalnız K8'den okuma |
| **K11** Kullanıcı Deneyimi | ITCSS, BEM, Design Tokens, WCAG 2.2 AA | Sadece K10 tetikler; **Yalnızca Light Mode (`HL-01`)** |
| **K10** Uygulama | 10 Panel (music, admin, home, car, studio, download, landing, pro, media, auth) | **Yalnız K9 API üzerinden** |
| **K9** API & Routing | Gateway, BFF×6, CQRS, Event Bus, SPA Router, OpenAPI | OpenAPI sözleşmesi ihlal edilemez; tek route tablosu (ADR-113) |
| **K8** Servis | Control, Media, Audio, Device, Network, AI, Download + Infra | **Servisler arası doğrudan çağrı YASAK — Event Bus** |
| **K7** Middleware | PSR-15 zinciri (§3.3) | K6 doğrulamasını atlayamaz; sıra değişmez |
| **K6** Güvenlik | JWT, RBAC, CSRF (`csrf_token`), CSP, RateLimit, AES-256, Vault, Audit | **Asla bypass edilemez** |
| **K5** Veri Yönetimi | MySQL 9 (18 BCNF, 156 tablo), APCu, SQLite | 18 DB kesin BCNF; ORM yasak; migration şart (ADR-112) |
| **K4** Yapay Zeka | Music Analysis, Recommendation, Voice, Edge AI | K5 harici veriye erişemez |
| **K3** Ses İşleme | Neva Engine, DSP Chain (31-band EQ, 4 Reverb) | Sıfır gecikme; K2'ye sıkı bağımlı |
| **K2** Sürücü | ASIO, WASAPI, ALSA, PipeWire, CoreAudio, I2S, USB | Donanım-yazılım köprüsü |
| **K1** Donanım | XMOS XU316, PCM3168A, AK4458, MJL21194/93 | **Discrete Class AB zorunlu — Class D yasak (`HL-02`)** |
| **K0** İşletim Sistemi | Windows, Linux, macOS, RPi5, Docker | Çekirdek servisler |

### 3.2 Layer Dependency Matrix (`@.ai/CLAUDE.md` §5.1)

| Kaynak → Hedef | İzinli |
|----------------|--------|
| L6→L5 · L5→L4 · L4→L3 · L3→L2 · L2→L1 · L1→L0 | ✅ |
| L0→L2/L3 · L1→L3 · L3→L0 · Alttan üste ters bağımlılık | ❌ **Layer Violation → derhal revert + log CRITICAL** |

### 3.3 Middleware Pipeline — IMMUTABLE (ADR-010..022 — `@.ai/CLAUDE.md` §6)

```text
OriginCheck → Cors → RateLimiter → SecurityHeaders → SessionManager → Csrf → BypassAuth → Auth → Permission → Validation → Controller
```

| # | Middleware | Görev | Timeout / Kural |
|---|---|---|---|
| 1 | OriginCheck | Whitelist CORS köken doğrulama | — |
| 2 | Cors | CORS header enjeksiyonu | — |
| 3 | RateLimiter | APCu, 60 req/60s | 60s |
| 4 | SecurityHeaders | CSP strict-dynamic, X-Frame-Options, HSTS, nonce üretimi | — |
| 5 | SessionManager | Session başlatma + CSP nonce session'a kaydetme | 3600s idle |
| 6 | Csrf | `csrf_token` doğrulama (`_csrf_token` yasak — POST/PUT/DELETE) | — |
| 7 | BypassAuth | `?_bypass=1` test bypass — yalnız test ortamı, prod'da devre dışı | — |
| 8 | Auth | JWT + Session doğrulama ve enjeksiyon | — |
| 9 | Permission | Standart RBAC rolleri (`HL-09` — uydurma rol yasak) | — |
| 10 | Validation | Request / DTO doğrulama | — |

**Kritik Kural:** CSP nonce #4'te üretilir, #5'te session'a yazılır. **Sıra değiştirilirse CSP çöker — Guardrail #7 ihlali → sistem durdurulur.**

### 3.4 API-First & Sürümleme Mimarisi (ADR-084 & ADR-113)

- **Doğrudan Endpoint Kodlama YASAKTIR:** `OpenAPI Spec → DTO → Contract → Validation → Use Case → Kod`.
- **Tek Route Tablosu (ADR-113):** Yeni uçlar `/api/{version}/...` placeholder'ı ile tek route tablosunda (`config/routes.php`) tanımlanır. Ayrı V2 route tablosu veya kopya route yasaktır.
- **Sürüm Çözümleme:** `v1` default; `v2` URL path (`/api/v2/...`) veya Accept header ile tetiklenir. Davranış farkı yalnız `V2Container` override'ı ile uygulanır.
- **BFF×6:** SPA (tam) · Mobile (minimal) · Embedded/RPi5 (ultra-minimal gzip) · Desktop (orta) · Admin (full+audit) · Car (touch-optimized).
- **CQRS:** `Write: Command→UseCase→Repository→MySQL Master` · `Read: Query→ReadModel→Cache→Response`.
- **Event Bus (ADR-086):** Servisler arası doğrudan çağrı yasaktır; PSR-14 Event Bus kullanılır.
- **SPA İzolasyonu:** SPA asla doğrudan veritabanı, repository veya altyapı sınıflarını görmez; yalnız `ApiClient` ile konuşur.

### 3.5 Repository & Veri Erişimi Standardı (ADR-114)

- **Yeni Kod Standardı:** `I*` interface + concrete `final class` + `IDatabaseRegistry`/PDO constructor injection + raw prepared statement + explicit kolon.
- **ORM / SELECT \* Yasağı:** ORM kesinlikle yasaktır (`HG-09`). `SELECT *` kullanımı kesinlikle yasaktır; kolonlar tek tek yazılır.

### 3.6 Panel & Servis Haritası

| Port | Servis / Panel | Açıklama |
|---|---|---|
| 80 | `admin.coremusic.net` / `coremusic.net` (landing) | Yönetim paneli & landing; media UI admin'e taşındı (ADR-115) |
| **81** | **`music.coremusic.net` (Control Service)** | **Guardrail #8: bu port asla değişmez** |
| 3001 | `download.coremusic.net` | Node.js + TypeScript indirme yöneticisi |
| 3306 | MySQL 9 (18 BCNF) | Veritabanı motoru |
| 5000/6000 | `media.coremusic.net` | PHP + FFmpeg streaming motoru |
| 9741 / 9742 | Audio Service REST / WebSocket | C++20 JUCE / Neva Engine |

**Gerçeklik Notu (Zero-Hallucination):** Kod ağacında fiziksel olarak mevcut olan subdomain'ler: `admin.coremusic.net`, `api.coremusic.net`, `assets.coremusic.net`, `auth.coremusic.net`, `home.coremusic.net`, `media.coremusic.net`, `shared`, `app-cpp-projects`, `app-nodejs-projects`, `bin`. Henüz kodlanmamış olanlar hedef mimaridir; "var" diye iddia edilemez.

---

## 4. Kurallar

| # | Kural | İhlal Sonucu |
|---|---|---|
| 1 | Katman bağımlılığı tek yön (L6→L0) | Revert + CRITICAL log |
| 2 | Middleware sırası değiştirilemez (10 adım) | Sistem durdurulur (Guardrail #7) |
| 3 | Endpoint önce OpenAPI spec ile tanımlanır | Kod geçersiz sayılır |
| 4 | Servisler arası yalnızca Event Bus | Revert |
| 5 | SPA alt katmanı veya DB'yi görmez | Revert |
| 6 | Mimari karar öncesi kullanıcı onayı zorunludur (ADR) | Revert |
| 7 | Veritabanı şema drift'i migration ile kapatılır (ADR-112) | Revert |
| 8 | API V1/V2 tek rota tablosunda yönetilir (ADR-113) | Rota reddedilir |
| 9 | Donanım discrete Class AB'dir (Class D yasak — `HL-02`) | Tasarım reddedilir |
| 10 | Yalnızca Light Mode geçerlidir (`HL-01`) | Kod revert |

---

## 5. Workflow

```text
Mimari Değişiklik Talebi
  → Guardrail #14 & KURAL-07 (kullanıcı onayı / ADR hazırlığı)
  → Vault okuma: @.ai/CLAUDE.md §5-§6B + karar-kurallari.md (ADR-112..115)
  → Katman / servis / port / şema etkisi analizi (§3.1-§3.6)
  → Layer Violation taraması (§3.2) + Middleware sıra kontrolü (§3.3)
  → Uygulama → mimari denetim (architect-reviewer)
  → durum.md güncelleme + log.md append
```

---

## 6. Doğrulama

| # | Kontrol | Kriter |
|---|---|---|
| 1 | Frontmatter 7 alan + docType | Mevcut |
| 2 | Katman tablosu | K0-K15 eksiksiz |
| 3 | Middleware | 10 adet pipeline sırası birebir |
| 4 | Portlar | 81 = music (Guardrail #8) |
| 5 | ADR Entegrasyonu | ADR-112, 113, 114, 115 kurallara işlendi |
| 6 | Zero-Hallucination | Gerçek subdomain durumu disk kanıtıyla doğrulandı |

---

## 7. Referanslar

| Kaynak | Yol | Amaç |
|---|---|---|
| Anayasa §5-§6B | [[.ai/CLAUDE.md]] | Katman + middleware + API-first |
| Karar Kuralları | [[karar-kurallari.md]] | ADR-112, 113, 114, 115 SSOT |
| Temel Direktifler | [[kurallar.md]] | KURAL-01..10 |
| Hata Listesi | [[hata-listesi.md]] | HL-01..12 |
| Master Kurallar | [[RULES.md]] | Genel aktivite kuralları |
| Backend Kuralları | [[backend-rules.md]] | PHP 8.4 uygulama kuralları |

---

**Authority:** Bayram Ali / Kurucu & Mimari Otorite  
**Last Updated:** 2026-11-01 · **Version:** 2.0.0
