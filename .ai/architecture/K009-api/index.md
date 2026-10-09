---
title: "K009 API «HÜCRE» — Katman Index"
type: index
category: architecture
version: 1.0.0
status: draft
authority: "SSOT: .ai/architecture/K009-api/index.md — K-space band-1 katmanı (ADR-096)"
updated: 2026-10-08
tier: 3
domain: api
ssot: true
risk: medium
owner: backend
depends-on: [".ai/architecture/00-kspace-anayasa.md"]
---

# K009 API «HÜCRE» — Katman Index

> **Authority:** Bu dosya K009 katmanının tek SSOT'udur (R7.2). Üst otorite:
> `00-kspace-anayasa.md §A.1 K009 kartı` > `.ai/CLAUDE.md §5/§6A` > `rules.md` > bu dosya (R8.1).
> Kurucu karar: `ADR-096-kspace-5000-boundary-model.md §2` + `ADR-084-api-gateway-architecture`.
> **Durum:** `draft` — bant onayı Kapı 10'da 👤 (R10). Bu dosya staging'dir; vault'a yazılmadı.

## Künye

| Alan | Değer |
|---|---|
| K-ID | K009 |
| Kanonik Ad | API |
| Teatral Epitet | «HÜCRE» |
| Bant | Bant 1 — K000-K020 EXISTING FOUNDATION |
| Uçak | SOFTWARE (ADR-096 §2.2) |
| Dizin deseni | `.ai/architecture/K009-api/index.md` (R2.2 — henüz üretilmedi, bu dosya staging taslağı) |
| Tier / Domain | 3 / api |
| Owner (`.ai/AGENTS.md` §4 registry) | backend |
| Risk | medium — dışa açık sözleşme yüzeyi (`api.coremusic.net`); K006 dışı high yok |
| Depends-on | `.ai/architecture/00-kspace-anayasa.md` |
| Bağlı ADR'ler (ls-verified: `.ai/.decisions/accepted/`) | ADR-004 · ADR-009 · ADR-016 · ADR-020 · ADR-021 · ADR-084 · ADR-085 · ADR-086 · ADR-094 · ADR-095 · ADR-096 |
| Kanıt tarihi | 2026-10-08 |
| Bant-kardeş üretimi | band-1 seti (K007-K013) — 7 dosya, staging |
| Şablon/format otoritesi | R3/R4 (iskelet + min-500 + 16 özet + 20 alan EK C) — `rules.md` |

#### §1 Genel Bakış

K009 API «HÜCRE», CoreMusic'in tüm istemciler için **tek giriş noktasıdır**: Gateway
(`api.coremusic.net`) + BFF×6 + CQRS + Event Bus + SPA Router + OpenAPI sözleşmesi (anayasa §5 K9 · §6A).
API-First rejimi (ADR-084) gereği **hiçbir endpoint doğrudan kodlanmaz** — önce OpenAPI, sonra
DTO/Contract/Validation/Use Case gelir; API sözleşmesi ihlal edilemez (§5 K9 hard guardrail).
Repo kanıtı var: `api.coremusic.net/` (gateway) + `shared/src/Api/{Bff,Routing,Versioning,Registry,...}`
(dosya: BffLayer, SpaBff, MobileBff, EmbeddedBff, DesktopBff, RouteTable — ls 2026-10-08).

---

## §2 Özet Satır — 16 Alan (R4.1 / F1 §10.2 kontratı)

| K-ID | KANONİK_AD | TEATRAL_EPİTET | DOMAIN | RUNTIME | SORUMLULUK | GİRDİ | ÇIKTI | İZİNLİ_BAGIMLILIK | YASAK_BAGIMLILIK | DATA_BOUNDARY | SECURITY_BOUNDARY | FAILURE_MODE | OBSERVABILITY | TEST | KANIT |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| K009 | API | «HÜCRE» | api | PHP 8.4 (gateway `api.coremusic.net`) · Vanilla JS (SPA Router — istemci tarafı) | API Gateway (routing/auth/rate limit/validation/logging/correlation) · BFF×6 · CQRS · Event Bus · SPA Router · OpenAPI · Error Contract — 30 bileşen (anayasa §5 K9) | istemci HTTP istekleri (SPA/Mobile/Embedded/Desktop/Admin/Car) · OpenAPI sözleşmesi · K006 auth kararları | version'lı JSON yanıtlar · hata sözleşmesi · K008'e command/query · event yayını | K000-K008 (alt katmanlar) + port/adapter · K007 pipeline (gateway hattı) · K006 authz | K009+ üst katmana doğrudan erişim (H20) · OpenAPI dışı endpoint (§5 K9) · K010'un K008'e bypass'u · H19 veri paylaşımı | iş verisi barındırmaz; yalnız routing/auth/DTO geçişi + rate-limit durumu; kalıcı veri K008/K005 | tek giriş noktası (§6A.1) · JWT doğrulama (RS256 — ADR-095) · CORS/origin (ADR-094) · OpenAPI şema zorlaması | hata sözleşmesi ile 4xx/5xx · gateway timeout → 504 (tasarım ⚠️) · BFF degrade (minimal yanıt) | access log + correlation ID üretimi + latency; metrik/toplama K012'ye devredilir | PHPUnit 11 (`shared/tests/Api/` + `api.coremusic.net/phpunit.xml` — ls 2026-10-08) · contract test ⚠️ | repo: `api.coremusic.net/` + `shared/src/Api/` (ls 2026-10-08) · vault: `.ai/CLAUDE.md §5 K9/§6A.1-6A.5` + `00-kspace-anayasa.md §A.1 K009` · ADR: ADR-084/021/086/094/095 (accepted/ ls) · web: ⚠️ VERIFICATION REQUIRED (F1 EK B) |

**Alan okuma notu:** İZİNLİ ∩ YASAK = ∅ ✓. Hedef ≠ kanıt (H10): "30 bileşen" anayasa hedefi,
"BFF 5/6 dosya + RouteTable" repo kanıtı — birleştirilmez.

---

## §3 Tam Kimlik Kartı (EK C — 20 alan · R4.3)

| # | Alan | Değer |
|---|---|---|
| 1 | K-ID | K009 |
| 2 | KANONİK_AD | API |
| 3 | TEATRAL_EPİTET | «HÜCRE» (yalnız sıfat — K-ID'yi ezmez, R2.3) |
| 4 | DOMAIN | api |
| 5 | SUBDOMAIN | gateway · bff (×6) · cqrs · event-bus · spa-router · openapi-contract · error-contract |
| 6 | BOUNDED_CONTEXT | API Sözleşme & Yönlendirme — dışa açılan tek kapı; iş kuralı üretmez, K008'e devreder |
| 7 | RUNTIME | PHP 8.4 (gateway · `api.coremusic.net`) · Vanilla JS ES2022 (SPA Router — istemci) · HTTP/1.1+JSON · WS (9742 tarafı K008) |
| 8 | SORUMLULUK | API Gateway (tek giriş §6A.1) · BFF×6 (SPA/Mobile/Embedded/Desktop/Admin/Car) · CQRS (Write/Read ayrımı §6A.3) · Event Bus (PSR-14 §6A.4) · SPA Router (immutable contract — ADR-021) · OpenAPI (API-First §6A) · Error Contract |
| 9 | GIRDI | istemci istekleri + header (auth/csrf) · OpenAPI şeması · K006 JWT/claim · K007'den devredilmiş doğrulanmış istek · K008 servis yanıtları/event'leri |
| 10 | CIKTI | version'lı JSON yanıt (BFF'e göre minimal/tam) · hata sözleşmesi (4xx/5xx şeması) · K008'e command/query · event yayını (K012'ye log) |
| 11 | IZINLI_BAGIMLILIK | K000-K008 (alt katmanlar), K007 middleware hattı, K006 authz, port/adapter (EK A), PSR-14 (event), OpenAPI 3.x (dış standard) |
| 12 | YASAK_BAGIMLILIK | K009 → K010-K020 doğrudan erişim/geri çağrı (H20) · OpenAPI dışı endpoint kodlamak (§5 K9) · K010'un K008'e bypass etmesi (SPA yalnız ApiClient — §6A.5) · H19 doğrudan veri paylaşımı |
| 13 | DATA_BOUNDARY | Gateway durumdurur (stateless hedef): routing/auth/DTO; DB'ye dokunmaz (repository K008'de) · rate-limit sayacı K007'de · iş verisi yalnız DTO gövdesinden geçer |
| 14 | SECURITY_BOUNDARY | tek giriş noktası `api.coremusic.net` (§6A.1) · JWT doğrulama + rate limit + validation gateway'de · CSP/CORS K007 ile ortak (ADR-094) · OpenAPI şeması harici alan reddi |
| 15 | FAILURE_MODE | hata sözleşmesi: 400/401/403/404/409/422/429/5xx şematik · gateway timeout → 504 (tasarım ⚠️) · BFF degrade: minimal yanıt (Embedded gzip) · OpenAPI ihlali → 422/500 (sözleşme korunur) |
| 16 | OBSERVABILITY | access log · **correlation ID üretimi** (§6A.1 gateway görevi) · latency ölçümü; merkezi metrik/trace K012'ye devredilir |
| 17 | TEST | PHPUnit 11 — `api.coremusic.net/phpunit.xml` + `shared/tests/Api/` (ls 2026-10-08); OpenAPI contract test PLANNED ⚠️; hedef ≥80% (§17) |
| 18 | KANIT | repo: `api.coremusic.net/{index.php,config/routes.php,config/cors.php,include/Container,include/Controller}` · `shared/src/Api/{Bff/BffLayer,SpaBff,MobileBff,EmbeddedBff,DesktopBff,Routing/RouteTable,Versioning,Registry,Middleware,Dto}` (ls 2026-10-08) · vault: `.ai/CLAUDE.md §5 K9 · §6A.1-§6A.5 · §6A` + `00-kspace-anayasa.md §A.1 K009` · ADR: ADR-084/021/086/094/095/020 (accepted/ ls) · web: ⚠️ VERIFICATION REQUIRED |
| 19 | KANIT_TARIHI | 2026-10-08 (repo ls + vault read) |
| 20 | EPİTET_KALİTE_NOTU | «HÜCRE» — canlılığın/temasın tek merkezi metaforu (gateway = tek temas noktası); EK A §A.1 anahtar satırı: `K009 · API · «HÜCRE» · SOFTWARE` |

**R4.4 kart kapıları:** (a) 20 alan dolu ✓ · (b) İZİNLİ ∩ YASAK = ∅ ✓ · (c) KANIT 3'lü (repo | vault+ADR | web ⚠️) ✓ ·
(d) veri sınırı tek: gateway DTO geçişi; kalıcı veri K008/K005'te ✓.

### §3.1 Özet Satır ↔ EK C Tutarlılık Kontrolü (R4.4a)

| Özet alanı (§2) | EK C karşılığı (§3) | Tutarlı? |
|---|---|---|
| K-ID / KANONİK_AD / TEATRAL_EPİTET | alan 1/2/3 | ✓ |
| DOMAIN | alan 4 | ✓ |
| RUNTIME | alan 7 | ✓ |
| SORUMLULUK | alan 8 | ✓ |
| GİRDİ / ÇIKTI | alan 9/10 | ✓ |
| İZİNLİ / YASAK | alan 11/12 | ✓ (kümeler disjoint) |
| DATA / SECURITY BOUNDARY | alan 13/14 | ✓ |
| FAILURE_MODE | alan 15 | ✓ |
| OBSERVABILITY | alan 16 | ✓ |
| TEST | alan 17 | ✓ |
| KANIT | alan 18/19/20 | ✓ (3'lü + tarih + epitet notu) |

---

## §4 Sorumluluk Derinliği

### §4.1 EK A §A.1 K009 Sorumluluk Maddeleri (anayasa kartı — ana kaynak)

Kaynak: `00-kspace-anayasa.md §A.1` — "Sorumluluk: API Gateway · BFF · REST API · OpenAPI ·
CQRS · Event Bus · SPA Router · Error Contract".

| Kalem | Ne yapar | Durum (repo kanıtı yoksa PLANNED — H1) | Kanıt |
|---|---|---|---|
| API Gateway | Tek giriş `api.coremusic.net`: routing, auth, rate limit, validation, logging, correlation ID | IMPLEMENTED (repo — gateway dizini) | `api.coremusic.net/` (ls 2026-10-08: index.php, config/{app,cors,routes}.php, include/{Container,Controller}, composer.json, phpunit.xml) · `.ai/CLAUDE.md §6A.1` · ADR-084 (accepted/ ls) |
| BFF | Her istemci tipine özel yanıt (§6A.2 tablosu ×6) | PARTIAL — 5/6 BFF dosyası (Admin/Car eksik) | `shared/src/Api/Bff/{BffLayer,SpaBff,MobileBff,EmbeddedBff,DesktopBff}.php` (ls 2026-10-08) · AdminBff/CarBff GÖZLENMEDİ → ⚠️ · §6A.2 |
| REST API | Resource uçları + version'ing | IMPLEMENTED (repo — versioning + routes) | `shared/src/Api/Versioning/` + `api.coremusic.net/config/routes.php` (ls) · §6A |
| OpenAPI | API-First: şema → DTO → Contract → Validation → Use Case → Kod | PLANNED — repo kökünde `*openapi*` dosyası bulunamadı (maxdepth 3, ls 2026-10-08) → ⚠️ | `.ai/CLAUDE.md §6A` (akış) · §5 K9 "OpenAPI ihlal edilemez" · repo: dosya YOK |
| CQRS | Write: Command→UseCase→Repository→Master · Read: Query→ReadModel→Cache→Response | PARTIAL — desen kuralı vault'ta; kod ayrımı grep edilmedi → ⚠️ | `.ai/CLAUDE.md §6A.3` · `shared/src/{Repository,Cache}` (altyapı ls) |
| Event Bus | Servisler arası tek kanal (PSR-14) — K008 Event Boundary ile ortak sahiplik | IMPLEMENTED (kütüphane — K008 §'sinde detay) | `shared/src/Events/` + `Contracts/Events/*` (ls) · ADR-086 · §6A.4 |
| SPA Router | Enterprise router (SOLID, PSR, attribute-based, DI — prompt1 arşivi) · immutable contract | IMPLEMENTED (repo — js/router + RouteTable) | `assets.coremusic.net/js/router/` + `shared/src/Api/Routing/RouteTable.php` (ls) · ADR-021 spa-router-immutable-contract (accepted/ ls) |
| Error Contract | Tek hata şeması: tüm 4xx/5xx aynı gövde yapısı | PARTIAL — davranış kodda, şema dosyası ⚠️ | `.ai/CLAUDE.md §6A` + §9 (hata akışı) · ADR-094 · şema dosyası: ⚠️ VERIFICATION REQUIRED |

### §4.2 Anayasa §5 K-Matrix Satırı (K9) — 30 bileşenin açılımı

Kaynak: `.ai/CLAUDE.md §5` — **K9 API & Routing | Gateway, BFF×6, CQRS, Event Bus, SPA Router,
OpenAPI | 30 | API sözleşmesi (OpenAPI) ihlal edilemez.**

| Kalem (§5 satırı) | Ne yapar | Durum | Kanıt |
|---|---|---|---|
| Gateway | Tek giriş + middleware hattı | IMPLEMENTED | `api.coremusic.net/` (ls) · §6A.1 |
| BFF×6 | SPA · Mobile · Embedded · Desktop · Admin · Car | PARTIAL (5/6 dosya) | `shared/src/Api/Bff/*` (ls) · §6A.2 tablosu · Admin/Car ⚠️ |
| CQRS | yazma/okuma ayrımı | PARTIAL (kural var, ayrım grep'i ⚠️) | §6A.3 · `shared/src/{Repository,Cache}` |
| Event Bus | PSR-14 yayın/abonelik | IMPLEMENTED (kütüphane) | `shared/src/Events/` (ls) · §6A.4 · ADR-086 |
| SPA Router | istemci yönlendirme + immutable contract | IMPLEMENTED | `assets.coremusic.net/js/router/` (ls) · ADR-021 |
| OpenAPI | sözleşme otoritesi | PLANNED ⚠️ (dosya yok) | §6A akışı · §5 K9 · repo: openapi dosyası YOK |
| "30 bileşen" | K9 envanter toplamı | HEDEF (H10) | `.ai/CLAUDE.md §5 K9` |

### §4.3 API-First Sözleşme Hattı (§6A — ADR-084)

```text
OpenAPI Spec → DTO → Contract → Validation → Use Case → Kod
```

| Adım | Ne üretir | Sahip | Durum | Kanıt |
|---|---|---|---|---|
| 1 OpenAPI Spec | endpoint şeması (zorunlu ilk adım) | K009 | PLANNED ⚠️ (dosya yok) | `.ai/CLAUDE.md §6A` · §5 K9 |
| 2 DTO | tipli transfer nesneleri | K009 (`shared/src/Api/Dto/`) | IMPLEMENTED | `shared/src/Api/Dto/{Request,Response}/…` (ls: LoginRequest/LoginResponse) |
| 3 Contract | sözleşmenin kod karşılığı | K009 + `shared/src/Contracts/Api/` | IMPLEMENTED (dizin ls) | `shared/src/Contracts/Api/` (ls 2026-10-08) |
| 4 Validation | giriş doğrulama (K007 #10 + gateway katmanı) | K007/K009 | IMPLEMENTED | `ValidationMiddleware.php` + test (ls) · §6A |
| 5 Use Case | iş akışı (K009 değil — K008 domain/use-case alanı) | K008 | IMPLEMENTED/PARTIAL (K008 §'sinde) | §6A akışı · §6A.5 |
| 6 Kod | endpoint implementasyonu — YALNIZ sözleşmeden sonra | K009/K008 | kural bağlayıcı | §6A "hiçbir endpoint doğrudan kodlanmaz" |

### §4.4 BFF×6 Detayı (§6A.2 — gerçek tablo)

| # | İstemci | BFF | Response profili | Repo dosyası (ls 2026-10-08) | Durum |
|---|---|---|---|---|---|
| 1 | SPA | SPA BFF | Tam veri | `SpaBff.php` | IMPLEMENTED |
| 2 | Mobile | Mobile BFF | Minimal | `MobileBff.php` | IMPLEMENTED |
| 3 | Embedded (RPi5) | Embedded BFF | Ultra-minimal, gzip | `EmbeddedBff.php` | IMPLEMENTED |
| 4 | Desktop | Desktop BFF | Orta boy | `DesktopBff.php` | IMPLEMENTED |
| 5 | Admin | Admin BFF | Full + audit | (gözlenmedi) | PLANNED ⚠️ |
| 6 | Car | Car BFF | Touch-optimized | (gözlenmedi) | PLANNED ⚠️ |

Ortak altyapı: `shared/src/Api/Bff/BffLayer.php` (taban katman — ls) · §6A.2 (anayasa) ·
`.ai/CLAUDE.md §9` (panel/kullanıcı yüzeyi) ile eşleşir.

### §4.5 CQRS & Event Driven (§6A.3 / §6A.4)

| Yön | Akım | Kanıt |
|---|---|---|
| Write | Command → Use Case → Repository → MySQL Master | `.ai/CLAUDE.md §6A.3` · ADR-086 |
| Read | Query → Read Model → Cache → Response | `.ai/CLAUDE.md §6A.3` · `shared/src/Cache` (ls) |
| Olay | Service A → Event Bus (PSR-14) → Service B, C, D | `.ai/CLAUDE.md §6A.4` · `shared/src/Events/` (ls) · ADR-086 |
| Kural | servisler birbirini doğrudan çağırmaz | `.ai/CLAUDE.md §6A.4` · §5 K8 (ile çapraz kilit) |
| Durum | desen vault'ta KURULU; kodda Write/Read ayrım kanıtı grep edilmedi → ⚠️ | §6A.3 · ⚠️ |

### §4.6 SPA → ApiClient Kuralı (§6A.5 — katman kilidi)

```text
SPA → ApiClient → HTTP → Gateway → Middleware → Use Case → Domain → Repository → Infrastructure
```

| Kural | İçerik | İhlal sonucu | Kanıt |
|---|---|---|---|
| SPA yasağı | SPA asla PDO, MySQL, Repository, Entity, Infrastructure, Filesystem, FFmpeg, Redis, Cache veya SQL GÖRMEZ | katman ihlali → revert + CRITICAL (§5.1) | `.ai/CLAUDE.md §6A.5` |
| Tek kapı | tüm istemci trafiği Gateway'den | bypass → güvenlik/sözleşme ihlali | §6A.1 |
| Router bağı | SPA Router yalnız ApiClient üzerinden veri alır | ADR-021 ihlali (immutable contract) | ADR-021 (accepted/ ls) |
| Repo kanıtı | `assets.coremusic.net/js/{router,core}` — ApiClient konumu grep edilmedi → ⚠️ | — | ⚠️ VERIFICATION REQUIRED |

### §4.7 Durum Özeti (H1 — hedef ≠ kanıt)

| Ölçüm | Değer |
|---|---|
| Anayasa §5 K9 hedefi | 30 bileşen (HEDEF) |
| Repo kanıtı (ls 2026-10-08) | gateway dizini + Api/{Bff(5),Routing,Versioning,Registry,Middleware,Dto} + js/router |
| Kapsam | Gateway/SPA Router/REST-Router IMPLEMENTED · BFF 5/6 · CQRS/Error Contract PARTIAL · OpenAPI PLANNED |
| Sözleşme dosyası | `*openapi*` maxdepth 3 tarama: 0 isabet → ⚠️ |
| Test | `api.coremusic.net/phpunit.xml` + `shared/tests/Api/` (ls) |

### §4.8 K009 ADR Haritası (`.ai/.decisions/accepted/` ls-verified 2026-10-08)

| ADR | Karar özeti | K009 etkisi |
|---|---|---|
| ADR-084-api-gateway-architecture | API-First + Gateway + BFF | §4.3 hattının kurucu kararı |
| ADR-021-spa-router-immutable-contract | SPA router sözleşme dışı değişmez | §4.6 router bağı |
| ADR-086-event-driven-architecture | PSR-14 Event Bus | §4.5 olay akışı |
| ADR-094-api-pipeline-origin-csrf | Origin + CSRF API pipeline | gateway + K007 ortak hattı |
| ADR-095-hybrid-jwt-rs256-access-token | Hybrid JWT (RS256) | gateway auth doğrulaması |
| ADR-020-api-public-security | Public API güvenliği | dışa açık uçlar |
| ADR-004-multi-domain-spa | Çoklu domain SPA | istemci/routing modeli |
| ADR-009-clean-url-redirect · ADR-016-url-normalization | Temiz URL + normalizasyon | router/gateway davranışı |
| ADR-085-modular-composer-packages | PSR-4 paketler | `shared/src/Api` paketlenmesi |
| ADR-096-kspace-5000-boundary-model | K-space V2 rejimi | format/bağımlılık kaynağı |

### §4.9 K009 Arayüz Sözleşmeleri (komşularla sınır)

| Komşu | Arayüz | K009'un verdiği | Beklenen | Kanıt |
|---|---|---|---|---|
| K007 MIDDLEWARE | pipeline | doğrulanmış istek + header | correlation ID üretimi | `.ai/CLAUDE.md §6A.1` |
| K008 SERVICES | command/query + event | version'lı JSON + hata sözleşmesi | servis yanıtının DTO'ya sarılması | §6A.3 · §10 |
| K010 APPLICATION | ApiClient sözleşmesi | endpoint erişimi (tek kapı) | yalnız K009 üzerinden | §6A.5 · §5 K10 |
| K011 UX | nonce/CSP yükümlülüğü | nonce'lu script kabulü | satır içi script yok | ADR-012 · K007 §4.3.4 |
| K006 SECURITY | JWT/claim | doğrulanmış kimlik | RS256 token | ADR-095 (accepted/ ls) |
| K012 OBSERVABILITY | correlation + access log | correlation ID + log satırı | merkezi toplama (PLANNED) | §6A.1 · §5 K12 |

### §4.10 Gateway Envanteri (`api.coremusic.net/` — repo ls 2026-10-08)

| # | Dosya/Dizin | Rol | Durum |
|---|---|---|---|
| 1 | `index.php` | gateway giriş noktası (front controller) | VAR |
| 2 | `autoload.php` | PSR-4 autoload kökü | VAR |
| 3 | `config/app.php` | uygulama yapılandırması | VAR |
| 4 | `config/constants.php` | sabitler | VAR |
| 5 | `config/cors.php` | CORS yapılandırması (ADR-094 ile ilişkili) | VAR |
| 6 | `config/routes.php` | routing tablosu | VAR |
| 7 | `include/Container/` | DI kabı | VAR |
| 8 | `include/Controller/` | controller katmanı (pipeline devri noktası) | VAR |
| 9 | `composer.json` / `composer.lock` | PHP bağımlılıkları (php ≥8.4 varsayımı — teyit ⚠️) | VAR |
| 10 | `phpunit.xml` + `tests/Unit/` + `tests/bootstrap.php` | birim test altyapısı | VAR |
| 11 | `.htaccess` · `web.config` | vhost/rewrite (Apache/IIS) | VAR |
| 12 | `vendor/` | Composer bağımlılıkları (çalışma zamanı) | VAR (gitignore) |
| 13 | `AGENTS.md` · `CLAUDE.md` · `CONTEXT.md` · `WORKFLOW.md` | modül dokümantasyonu | VAR |
| 14 | OpenAPI şema dosyası | sözleşme otoritesi (§6A 1. adım) | **YOK → ⚠️** |

### §4.11 İstemci Router / ApiClient Envanteri (`assets.coremusic.net/js/` — ls 2026-10-08)

| # | Dosya | Rol (K009 ile) | Durum |
|---|---|---|---|
| 1 | `js/router/` (dizin) | SPA Router — ADR-021 immutable contract | VAR (dizin) |
| 2 | `router/FetchWrapper.js` | HTTP sarmalayıcı (ApiClient çekirdeği adayı — eşleme ⚠️) | VAR |
| 3 | `router/GuardPipeline.js` | rota koruma hattı (K007 karşılığı istemci tarafı) | VAR |
| 4 | `router/CsrfSyncManager.js` | `csrf_token` senkronu (Guardrail #6 ile uyum) | VAR |
| 5 | `router/AuthBoundaryDetector.js` | kimlik sınırı tespiti | VAR |
| 6 | `router/CacheLayer.js` · `ContentFetcher.js` · `ContentPatcher.js` · `DomPatcher.js` · `FocusManager.js` | route içerik çekme/yama/behavior | VAR |
| 7 | `js/core/EventBus.js` | istemci olay yolu | VAR |
| 8 | `js/core/CoreMusicApp.js` | SPA kabuğu | VAR |
| 9 | `js/core/breakpoints.spec.js` | Vitest spec (repo kanıtı) | VAR |
| 10 | OpenAPI tabanlı otomatik client | şema → client üretimi | PLANNED ⚠️ (şema yok) |

---

## §5 Bağımlılık & Sınır

### §5.1 İZİNLİ Bağımlılıklar (EK A aralık — alt katmanlar + port/adapter)

| Hedef | Yön | Gerekçe | Kanıt |
|---|---|---|---|
| K000-K005 (OS → DATA) | aşağı | anayasa §A.1 K009 "izinli=K000-K008" | `00-kspace-anayasa.md §A.1 K009` |
| K006 SECURITY | aşağı | JWT/claim doğrulama + RBAC (gateway auth görevi) | `.ai/CLAUDE.md §6A.1` · ADR-095 |
| K007 MIDDLEWARE | aşağı | gateway hattı K007 pipeline'ını kullanır/üzerinde taşır | `.ai/CLAUDE.md §6` · ADR-094 |
| K008 SERVICES | aşağı | command/query hedefi; servis yanıtları DTO'ya sarılır | `.ai/CLAUDE.md §6A.3` |
| port/adapter | yan | EK A istisnası (R6.3) | anayasa §A.1 |
| OpenAPI 3.x · PSR-14 | dış | sözleşme/olay standardı | `.ai/CLAUDE.md §6A · §6A.4` |

### §5.2 YASAK Bağımlılıklar

| Yasak | Gerekçe | Kanıt |
|---|---|---|
| OpenAPI dışı endpoint kodlamak | "API sözleşmesi (OpenAPI) ihlal edilemez" — hard guardrail | `.ai/CLAUDE.md §5 K9 satırı` · §6A |
| K009 → K010-K020 erişim / geri çağrı (H20) | klasik yön | `rules.md R6.1` · `ADR-096 §2` |
| K010'un K008'e bypass etmesi (SPA → DB/FFmpeg/Redis) | §6A.5 SPA yasağı | `.ai/CLAUDE.md §6A.5` |
| Doğrudan veri paylaşımı (H19) | veri sınırı ihlali | `rules.md R6.1` |
| Router'ı sözleşme dışı değiştirmek | ADR-021 immutable contract | ADR-021 (accepted/ ls) |
| `SELECT *`/ORM/framework | Guardrail #9/#10 · ADR-001/002 | `.ai/CLAUDE.md §21` · ADR-001/002 (ls) |

### §5.3 Boundary Matrisi

| Boundary | K009 Tanımı | Komşu sahip |
|---|---|---|
| DATA_BOUNDARY | gateway stateless; DB'ye dokunmaz; DTO gövdesi dışında iş verisi tutmaz | K008 (repo) · K005 (DB) |
| SECURITY_BOUNDARY | tek giriş · JWT (RS256) · rate limit (K007) · CORS/origin (ADR-094) · şema zorlaması | K006 (karar) · K007 (uygulama) |
| FAILURE_MODE | tek hata sözleşmesi (4xx/5xx şeması) · timeout 504 (tasarım ⚠️) · BFF degrade | K008 (kök neden) · K012 (alarm) |
| CONTRACT boundary | OpenAPI = tek sözleşme otoritesi; ihlal = katman ihlali | K009 (sahip) |
| RUNTIME boundary | PHP 8.4 gateway süreci + istemci JS router (ayrı çalışma zamanları) | K000 · K011 (tarayıcı) |
| Olay yukarı serbest | event yayını K012'ye/yukarı; senkron geri çağrı yasak (R6.2/H20) | K012 · K008 |

**Sınır ihlali prosedürü:** tespit → derhal revert + `log.md` CRITICAL + 👤 bilgi (R6.5 · anayasa §5.1).

### §5.4 Olay Akışı (R6.2 — yukarı serbest, aşağı izinli)

```text
K012 OBSERVABILITY ← (access log + correlation) ← K009 gateway
        ↑                                              │
        │ (olay)                              [event yayını yukarı serbest]
        └────── K008 (Event Bus PSR-14) ←── command/query ──┘
                     │
             aşağı: K005 repository (yalnız K008 üzerinden)
```

| Akış | Yön | Kural | Kanıt |
|---|---|---|---|
| Gateway → K008 command/query | aşağı (doğru yön) | API katmanı servisi çağırır | §6A.3 |
| K008 → gateway senkron geri | — | yalnızca yanıt; geri çağırır yasak (H20) | `rules.md R6.1` |
| Event → yukarı (K012/K010) | yukarı | olay yayını serbest | `rules.md R6.2` |
| İstemci → gateway | dışarı→içeri | tek kapı | §6A.1 |

### §5.5 Kardeş İlişki Kuralı (R6.4)

Kardeş KNNN'lerle (K000-K008 alt · K010-K020 üst/yatay) ilişki yalnız `refers-to` (doküman linki);
`depends-on` yalnız alt katman yönünde geçerlidir. Yatay `depends-on` döngü üretir → `dep-check` exit 1 (R6.6).

### §5.6 BFF ↔ Panel/İstemci Eşlemesi (§6A.2 + §9 — türetme, ⚠️ işaretli yerler grep edilmedi)

| # | İstemci/panel (§9) | BFF | Backend servis (§10) | Not |
|---|---|---|---|---|
| 1 | Music (`music.coremusic.net`, port 81) | SPA BFF | Control + Media | SPA BFF dosyası VAR (ls) |
| 2 | Home (`home.coremusic.net`, port 81) | SPA/Desktop BFF | Media + AI | repo dizini VAR (ls) |
| 3 | Auth (`auth.coremusic.net`) | SPA BFF | Control | repo dizini VAR (ls) |
| 4 | Media (`media.coremusic.net`, 5000/6000) | SPA BFF | Media | repo dizini VAR (ls) |
| 5 | Download (port 3001) | SPA BFF | Download | panel+servis PLANNED ⚠️ |
| 6 | Admin · Car · Studio · Pro · Landing | Admin BFF / Car BFF / SPA BFF | Control + ilgili servisler | BFF dosyaları eksik (5/6) ⚠️ · paneller §9 hedef notlu |
| 7 | Embedded (RPi5) | Embedded BFF (ultra-minimal, gzip) | Media | dosya VAR (ls) |
| 8 | Mobile | Mobile BFF (minimal) | tümü | dosya VAR (ls) |

> Eşlemeler **türetmedir** (§6A.2 BFF tablosu + §9 panel tablosu); istemci→BFF kod eşlemesi grep
> edilmedi → ⚠️. §9 hedef gerçeği: kod ağında yalnız auth/home(+assets/api/media) dizinleri var (2026-09-08 notu + 2026-10-08 ls).

---

## §6 Girdi-Çıktı · Runtime · Observability-Test

### §6.1 Girdi → Çıktı Haritası

| Aşama | Girdi | Çıktı | Not |
|---|---|---|---|
| İstemci (SPA/Mobile/…) | kullanıcı eylemi | ApiClient HTTP isteği | §6A.5 — DB/FFmpeg görmez |
| Gateway routing | path + version | doğru use-case/BFF | `config/routes.php` + `Api/Versioning` (ls) |
| Gateway auth | JWT/claim | kimlikli istek | ADR-095 · K006 |
| BFF katmanı | servis yanıtı | istemciye özel profil | §6A.2 ×6 |
| Use Case/Repository | command/query | Master/Cache okunur | §6A.3 (K008 alanı) |
| Yanıt | DTO | version'lı JSON + hata şeması | Error Contract |
| Log | istek meta verisi | access log + correlation ID | §6A.1 |

### §6.2 Runtime

| Öğe | Değer | Kanıt |
|---|---|---|
| Gateway host | `api.coremusic.net` — tek giriş | `.ai/CLAUDE.md §6A.1` |
| Gateway stack | PHP 8.4 (strict_types) · port 80 (§11: 80 admin HTTP — gateway vhost ⚠️) | §12 · §11 (gateway portu açık tanım yok → ⚠️) |
| Router (istemci) | Vanilla JS ES6+/ES2022 (ADR-001 — framework yasak) | §12 · ADR-001 (ls) |
| Şema | OpenAPI 3.x (hedef) | §6A · §5 K9 |
| Version'ing | `shared/src/Api/Versioning/` (repo) | ls 2026-10-08 |

### §6.3 Observability

| Sinyal | Kaynak | Durum |
|---|---|---|
| Access log | gateway (§6A.1 logging görevi) | IMPLEMENTED (davranış) · format ⚠️ |
| Correlation ID | gateway üretimi — §6A.1 zorunlu görev | IMPLEMENTED (tanım) · kod grep'i ⚠️ |
| Latency ölçümü | gateway timing | PLANNED (metrik aktarımı K012) |
| Hata olayları | Error Contract → K012 | PARTIAL |

### §6.4 Test

| Katman | Framework | Kanıt | Hedef |
|---|---|---|---|
| Gateway birim | PHPUnit 11 | `api.coremusic.net/phpunit.xml` (ls) | ≥80% (§17) |
| Api sözleşmeleri | PHPUnit | `shared/tests/Api/` (ls 2026-10-08) | ≥80% |
| Router (istemci) | Vitest | `assets.coremusic.net/vitest.config.js` (ls) | ≥80% (§17 Frontend) |
| E2E | Playwright | `assets.coremusic.net/playwright.config.ts` (ls) | §17 / Kapı 11 |
| OpenAPI contract test | (hedef) | PLANNED — şema yok | ≥80% ⚠️ |

### §6.5 Failure Mode Senaryoları

| # | Senaryo | K009 davranışı | Kanıt |
|---|---|---|---|
| 1 | Geçersiz token | 401 (hata sözleşmesi) | ADR-095 · §6A.1 |
| 2 | Yetkisiz rol | 403 | K007 #9 / K006 RBAC |
| 3 | Şema dışı istek (DTO) | 422 + alan hataları | §6A/§6 Validation · §4.3 adım 4 |
| 4 | Rate-limit aşımı | 429 (K007 #3) | ADR-013 · §6 #3 |
| 5 | K008 servisi DOWN | 5xx hata sözleşmesi; BFF degrade (minimal yanıt) | §6A.2 · §6A.3 |
| 6 | OpenAPI dışı endpoint talebi | 404/405 — sözleşme korunur | §5 K9 hard guardrail |
| 7 | BFF profili eksik (Admin/Car) | o istemci profili için yanıt yasağı/eksiklik → 501 (tasarım ⚠️) | §6A.2 ×6 · repo 5/6 |

### §6.6 Uyum & Kapı Bağlantısı (R11)

| Kapı/Kural | K009 durumu |
|---|---|
| KAPI 1 vault oku | Tam (anayasa §A.1 + §6A + §5 K9 + rules + ADR-096 okundu) |
| KAPI 9 hallucination | ⚠️ listesi §7.1 |
| KAPI 10 onay | BEKLİYOR — `status: draft` (R10) |
| Guardrail #1 (Zero Code Before Plan) | API-First akışı ile aynı yönde (§4.3) |
| Guardrail #9/#10 | raw PDO + Vanilla JS (ADR-001/002 ls) · tam grep ⚠️ |

### §6.7 Kabul Kriterleri (katman `ACTIVE` eşiği — R16.2 + R4.4)

| # | Kriter | Ölçüt | Mevcut |
|---|---|---|---|
| K1 | Özet satır 16 alan | tam dolu · İZİNLİ∩YASAK=∅ | ✓ (§2) |
| K2 | EK C 20 alan | tam dolu · KANIT 3'lü | ✓ (§3) |
| K3 | Gateway + BFF×6 + Router kapsamı | her kalem Durum+Kanıt | ✓ (§4.1/§4.4) · 2 BFF + OpenAPI eksik işaretli |
| K4 | Sözleşme kilidi | "OpenAPI ihlal edilemez" belgeli | ✓ (§4.3 · §5.2) |
| K5 | SPA yasağı | §6A.5 kuralı belgeli | ✓ (§4.6) |
| K6 | Web research (R9 3'lü) | ≥%80 kaynaklı | ✗ → ⚠️ (§7.1 G5) |
| K7 | Durum geçişi | draft → ACTIVE = 👤 + kanıt | ✗ bekliyor (Kapı 10) |

### §6.8 Sürümleme & Uyumluluk (version boundary)

| Kural | İçerik | Kanıt |
|---|---|---|
| Version'lama | `shared/src/Api/Versioning/` — URL/header version'laması | repo ls 2026-10-08 |
| Sözleşme değişikliği | OpenAPI değişikliği = sözleşme değişikliği → sürüm artışı (API-First akışı §4.3) | `.ai/CLAUDE.md §6A` · §5 K9 |
| Router değişikliği | SPA Router contract dışı değişmez (immutable) | ADR-021 (accepted/ ls) |
| BRC/IPC versiyonu | servisler arası sözleşme versioning'i ayrı ADR kapsamında | ADR-032 ipc-contract-versioning (accepted/ ls) |
| Kırılma (breaking) | eski istemciye kırılma → 4xx/5xx yerine sürüm koruması (kademeli) | tasarım hedefi ⚠️ (politika dosyası yok) |

---

## §7 Kanıt Kaynakları

| # | Kaynak | Tür (R9) | Kullanım |
|---|---|---|---|
| 1 | `00-kspace-anayasa.md §A.1 K009 - API «HÜCRE»` (+ §A.0) | dosya yolu (vault read 2026-10-08) | Sorumluluk/Sınır/Kanıt — ana kaynak |
| 2 | `.ai/CLAUDE.md §5 K9` · `§6A-§6A.5` · `§11` · `§12` · `§17` | dosya yolu (vault read) | K-matrix + API-First açılımı |
| 3 | `api.coremusic.net/` · `shared/src/Api/**` · `shared/tests/Api/` · `assets.coremusic.net/js/router` (ls 2026-10-08) | repo ls | IMPLEMENTED/PARTIAL/PLANNED |
| 4 | `.ai/.decisions/accepted/` ls: ADR-004/009/016/020/021/084/085/086/094/095/096 | ADR (ls teyitli) | karar atıfları |
| 5 | `rules.md R2/R3/R4/R6/R9` · `ADR-096 §2.4/§2.5` | dosya yolu | format + yön |
| 6 | F1 EK B (37 URL) · prompt1 arşivi (SPA Router) | URL/vault arşivi | research üssü |
| 7 | Aşağıdaki boşluklar | ⚠️ VERIFICATION REQUIRED | R14 research kapısı |

### §7.1 Açık Kanıt Boşlukları (⚠️ defteri)

| # | Boşluk | Etkilenen alan | Kapanış yolu |
|---|---|---|---|
| G1 | OpenAPI şema dosyası repo'da yok (maxdepth 3) | EK C SORUMLULUK/IZINLI | tam repo taraması (`**/*openapi*`) + yoksa şema üretimi (API-First 1. adım) |
| G2 | AdminBff / CarBff dosyaları gözlenmedi | §4.4 BFF×6 | `shared/src/Api/Bff/` tam ls |
| G3 | CQRS Write/Read ayrım kanıtı | §4.5 | use-case/repository grep'i |
| G4 | Error Contract şema dosyası | EK C FAILURE_MODE/OBSERVABILITY | doküman/shema araması |
| G5 | Web kanıtı (URL+tarih) | KANIT web ayağı | F1 EK B research kapısı (R14) |
| G6 | Correlation ID kod uygulaması | §6.3 | gateway kod grep'i |
| G7 | Gateway portu (§11 80 = admin; gateway vhost tanımı) | §6.2 | vhost/nginx yapılandırması okuması |

**Kural:** Boşluklar dosyayı geçersiz kılmaz; `ACTIVE` için R4.4c/R16.2 kapanışı gerekir.

### §7.2 Kanıt Haritası (EK C alan → birincil kanıt)

| EK C alan | Birincil kanıt | İkincil kanıt | Üçüncül |
|---|---|---|---|
| SORUMLULUK | anayasa §A.1 K009 | `.ai/CLAUDE.md §6A` | repo envanter §4.10/§4.11 |
| RUNTIME | `.ai/CLAUDE.md §12` | repo `composer.json`/`phpunit.xml` | ⚠️ (web) |
| GIRDI/CIKTI | `.ai/CLAUDE.md §6A.1-§6A.3` | `shared/src/Api/Dto` (ls) | ⚠️ |
| IZINLI/YASAK | anayasa §A.1 Sınır + `rules.md R6` | ADR-096 §2 | ⚠️ |
| DATA_BOUNDARY | `.ai/CLAUDE.md §6A.5` + §18 | repo: gateway'de DB bağımlılığı grep'i ⚠️ | ⚠️ |
| SECURITY_BOUNDARY | ADR-084/094/095 (ls) | `config/cors.php` (ls) | ⚠️ |
| FAILURE_MODE | §6A hata sözleşmesi | §6.5 senaryoları | ⚠️ (504 tasarımı) |
| OBSERVABILITY | §6A.1 correlation görevi | `shared/src/Log` (ls) | ⚠️ |
| TEST | `phpunit.xml` + `shared/tests/Api/` (ls) | `vitest.config.js` (ls) | ⚠️ |

---

## §8 İlişki & Değişiklik

**Kardeş katmanlar (Bant 1 · K000-K020 — düz metin, wiki-link YASAK, R2.4):**
Alt (izinli): K000 OS «TEMEL TAŞI» · K001 HARDWARE «DEMİRHANE» · K002 DRIVERS «KANAT» ·
K003 AUDIO ENGINE «ÇEKİRDEK» · K004 AI «MIKNATIS» · K005 DATA «FENER» · K006 SECURITY «ÇELİK KAPI» ·
K007 MIDDLEWARE «SUR» · K008 SERVICES «KULE». Üst/yasak yön (H20): K010 APPLICATION «DÜĞÜM» ·
K011 UX «OMURGA» · K012 OBSERVABILITY «AYNA» · K013 CI/CD «PUSULA» · K014 NETWORK «ÇARK» ·
K015 MEDIA «MÜHÜR» · K016 AMPLIFIER «ZAR» · K017 POWER «KANTAR» · K018 THERMAL «MEZİT» ·
K019 PCB «ALEV» · K020 MANUFACTURING «BUZUL». Kardeşler arası ilişki yalnız `refers-to` (R6.4).

**Yatay not:** K014 NETWORK taşıma katmanıdır (HTTP/3, WebSocket); K009 K014'ü yalnız port/adapter
ile kullanır — anayasa §5 K14 satırı: "Ağ iletişim standardı (K15, K10-13)".

**İzinli wiki-linkler:** [[architecture/00-kspace-anayasa]] · [[architecture/rules]] · [[architecture/00-master-index]]

**Bant-1 üretim durumu (2026-10-08 · staging, vault'a yazılmadı):** b1-K007-middleware.md (K007) ·
b1-K008-servisler.md (K008) · b1-K009-api.md (K009 — bu dosya) · b1-K010-uygulama.md (K010) ·
b1-K011-ux.md (K011) · b1-K012-izleme.md (K012) · b1-K013-cicd.md (K013).

**Geçmiş:** 2026-10-08 ilk üretim (bant-1 · staging) | Vault Steward.

**Not (Kapı 10):** Bu dosya `draft`'tır; band-1 seti (K007-K013) birlikte 👤 onayına gider.
Onay öncesi §7.1 boşluklarının (özellikle G1 OpenAPI şeması)Research kapısında kapatılması beklenir
(R4.4c · R16.2 · R11 KAPI 9). Vault yazımı ve `git commit` bu üretim kapsamında DEĞİLDİR (H5/H6).

**Kaynak bağımlılığı:** `depends-on: .ai/architecture/00-kspace-anayasa.md` — anayasa güncellenirse
bu kart §A.1 K009 satırıyla birlikte yeniden gözden geçirilir (R8.1 zinciri).