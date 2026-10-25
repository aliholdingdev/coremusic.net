---
title: "CoreMusic — auth.coremusic.net PRD (Kimlik Doğrulama Gereksinim Belgesi)"
type: prd
category: auth
version: 1.0.0
status: active
authority: "Derived — SSOT: [[../PRD.md]] (platform + aile indeksi) · bağ: [[CONTEXT.md]] · [[AGENTS.md]]"
updated: 2026-10-24
---

# auth.coremusic.net — PRD

**docType:** prd · **Klasör:** `auth.coremusic.net/` · **Yüzey:** #4 (aile indeksi §5) · **Sorumlu:** Security Engineer (+ Backend Architect)

**Zorunlu Bağlantılar:** [[../PRD.md]] · [[CONTEXT.md]] · [[AGENTS.md]] · [[CLAUDE.md]] · [[WORKFLOW.md]]

**Bağlı platform gereksinimleri (tekrar edilmez, yalnız bağlanır):** PRD-PLT-004 · PRD-PLT-006

---

## 1. Amaç

Bu PRD, `auth.coremusic.net/` yüzeyinin **tek kimlik doğrulama servisi** gereksinimlerini tanımlar: login, register, forgot-password, reset-password, select-gender/set-gender ve logout akışları, OAuth bağlantı uçları, oturum (session) yönetimi, RBAC ve rate limit. SSO'nun tek uygulama noktasıdır; `api`/`home` bu kodu kopyalamaz, `include/` katmanını çağırır (PRD-PLT-004).

> **eli10 (basit):** Bu dosya, "kim bu, girebilir mi" sorusunun tek cevap kapısının kurallarını söyler.
> **eli15 (detay):** Ayrı yazıldı çünkü kimlik katmanı tüm sistemün güvenlik temelidir; gereksinimi
> yoksa her yüzey kendi login'ini icat eder ve SSO kırılır. Okunması, auth değişikliğinde güvenlik
> kontrolünü verir. Yazılması, OWASP kapılarını denetlenebilir kılar. Olmazsa CSRF/session/rate limit
> dağınık ve tutarsız uygulanır.

---

## 2. Kapsam / Kapsam Dışı

| Kapsam | Kapsam Dışı |
|--------|-------------|
| `pages/**` (login, register, reset/forgot, select/set-gender, logout) | `include/` modülünün iç yapısı → alt PRD #22 |
| `include/` akış sözleşmesi (Controller → Handler → Service → Repository) | DB şeması `coremusic_auth.sql` → `.ai/.sql/**` (#25) |
| Middleware kullanımı: `index.php:81` OriginCheckMiddleware, `index.php:97` RateLimiterMiddleware (kod `shared/src/Middleware/`; `include/Middleware/` diskte YOK — 2026-10-24) | Ortak middleware paketi → `shared/src/Middleware/` (#10) |
| OAuth uçları + `auth.coremusic.net/config/cors.php` (diskte VAR — 2026-10-24) | Panel UI → `assets` PRD (#3) |

- **Ölçüm (disk kanıtı, 2026-10-12):** yineleme **2032 dosya / 346 dizin / 2378 girdi** (`vendor/` dahil); kök **13 dosya + 7 dizin**; `vendor/` hariç **65 dosya / 37 PHP** — sayı `[[CONTEXT.md]]` §1'dedir, burada tekrarlanmaz.
- **Ön koşul:** ADR-043 (Auth Subdomain Consolidation) — auth tek subdomain'de toplanmıştır.
- **Dokunulmaz:** `config/.env` içeriği (REDACTED — hiçbir yere yazılmaz) · session/cookie değerleri (log'a yazılmaz) · frozen ADR metinleri.

> **eli10 (basit):** Kapsam auth akışları, middleware ve oturum; veritabanı ve ortak paket başka PRD'de.
> **eli15 (detay):** Sınır, güvenlik katmanının ikinci bir kopyasının doğmasını engeller. Okunması, hangi
> çerçevenin burada uygulandığını gösterir. Sınır yoksa iki auth akışı birbirinden sapar.

---

## 3. Gereksinimler (PRD-AUT-001..006)

| ID | Gereksinim | Kabul Kriteri |
|----|-----------|----------------|
| PRD-AUT-001 | Şifre hash'i yalnız Argon2id, `Password` ValueObject dışında üretilmez | Koda gömülü hash fonksiyonu yok; `Password` VO tek üretim noktası |
| PRD-AUT-002 | CSRF (ADR-010) her form/POST'ta geçerli | Token doğrulamasız istek 403; `csrf_token` kuralı ihlal edilmez |
| PRD-AUT-003 | Session yönetimi yalnız `SessionManager` üzerinden (ADR-011) | Superglobal/doğrudan `$_SESSION` yazımı yok; session değeri log'a yazılmaz · **Durum: KARŞILANDI (2026-10-24)** — `index.php:170-171` ve `AutoRedirectHandler.php:53` yalnız `SessionManager` (`get`/`getCookieValue`/`getUserId`) üzerinden okur |
| PRD-AUT-004 | Rate limit her auth ucunda aktif (ADR-013) | Sınır aşımı 429; `RateLimit` middleware'i Pipeline'da kayıtlı |
| PRD-AUT-005 | Hexagonal akış bozulmaz: Controller → Handler → Service → Repository | `pages/**` içine iş mantığı gömülmez; Domain katmanında framework/superglobal yok |
| PRD-AUT-006 | ORM yasak; Repository yalnız PDO prepared statement (ADR-002) | Ham SQL/dinamik string yok; `SELECT *` yasak |

> **eli10 (basit):** Altı kural: doğru hash, CSRF, tek session yolu, rate limit, düz akış, hazır statement.
> **eli15 (detay):** Kimlikler ayrı çünkü güvenlik ihlali denetlenebilir ve geri alınabilir olmalıdır.
> Okunması, auth değişikliği öncesi kontrol verir. Kimliksiz gereksinim denetlenemez; OWASP kapısı
> hedefsiz kalır.

---

## 4. Kabul Kriteri (bu yüzeyin kapıları)

| # | Kapı | Ölçüt |
|---|------|-------|
| 1 | OWASP | PRD-AUT-001..004 — `security-engineer` PASS (PRD-PLT-006) |
| 2 | Mimari | PRD-AUT-005 — hexagonal akış + ADR-043 |
| 3 | Veri | PRD-AUT-006 — PDO prepared; `SELECT *` 0 |
| 4 | Test | `phpunit.xml` + `tests/Unit` (DTO/Entity/ValueObject) yeşil; yeni VO testi eklenir |
| 5 | Sır | `config/.env` REDACTED; secret `.md/.json/.log`'a girmez |

> **eli10 (basit):** Beş kapı: güvenlik, mimari, veri, test, sır.
> **eli15 (detay):** Kapılar üretimden ayrıdır; denetim üretime karışmaz. Okunması, kapanış raporu
> checklist'ini verir. Kapı yoksa "güvenli" iddiası kanıtsız olur.

---

## 5. ADR Bağları

| Karar | ADR | Yüzeye etkisi |
|-------|-----|---------------|
| Auth subdomain konsolidasyonu | ADR-043 | §1 kapsam |
| CSRF | ADR-010 | PRD-AUT-002 |
| Session | ADR-011 | PRD-AUT-003 |
| Rate limiting | ADR-013 | PRD-AUT-004 |
| ORM yasak / PDO | ADR-002 | PRD-AUT-006 |
| Yeni ADR numarası | `.ai/.decisions/index.md` — next: **111** | Akış/sözleşme değişikliğinde |

> **eli10 (basit):** Güvenlik kararları ADR'dedir; bu PRD yalnız bağ verir.
> **eli15 (detay):** Bağ, ADR metninin frozen tek sürüm kalmasını sağlar.

---

## 6. Risk

| # | Risk | Etki | Azaltma |
|---|------|------|---------|
| 1 | Hash'in `Password` VO dışında üretilmesi | Zayıf/çifte hash | PRD-AUT-001 + review |
| 2 | `pages/**`'a iş mantığı gömülmesi | Katman ihlali | PRD-AUT-005 + PRD-PLT-005 |
| 3 | Session/cookie değerinin log'a sızması | Oturum ele geçirme | PRD-AUT-003 + REDACTED denetimi |
| 4 | Rate limit'in ucunun atlanması | Brute-force | PRD-AUT-004 — Pipeline kaydı |
| 5 | `.env` içeriğinin dokümante edilmesi | Secret sızıntısı | Kapı 5 — REDACTED |

> **eli10 (basit):** En büyük tehlike: hash'i ve oturumu bozup bunu testle yakalanmamak.
> **eli15 (detay):** Her riskin azaltması bir kapıya bağlıdır; kapı devre dışı kalırsa risk geri döner.

---

## 7. Sonraki Adım

1. #22 (`auth/include` PRD) ile akış sözleşmesi (Controller→Handler→Service→Repository) derinleştirilir.
2. #1 (kök) ve #5 (home) PRD'leriyle SSO/RBAC akışı çapraz kontrol edilir (PRD-PLT-004).
3. Security denetimi sonucu bu PRD §4 kapısına eklenir; açık bulgu varsa `⚠️ VERIFICATION REQUIRED` + handover.

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
**Last Updated:** 2026-10-24

---

## 8. Son denetim (2026-10-24)

| Madde | Durum | Kanıt / Neden |
|-------|-------|---------------|
| PRD-AUT-003 superglobal | **KAPANDI** | `index.php:170-171` (`SessionManager::get`, `getCookieValue`); `include/Handler/AutoRedirectHandler.php:53` (`ISessionManager::getUserId`, constructor enjeksiyonu). Yeni okuma metodu: `SessionManager::getCookieValue()` (`include/Service/SessionManager.php`). Davranış: `getUserId` ile eski `is_string`/boş kontrolü aynı. Test: `tests/Unit/Handler/AutoRedirectHandlerTest.php` (3), `tests/Unit/Service/SessionManagerTest.php` (+2). |
| §2 `include/Middleware/**` yolu | **KAPANDI** | Dizin diskte YOK (glob doğrulandı). Gerçek konum `shared/src/Middleware/` (CorsMiddleware, OriginCheckMiddleware, RateLimiterMiddleware, SecurityHeadersMiddleware, SessionManagerMiddleware, MiddlewarePipeline, …) — kullanım `index.php:81,97`. §2 satırı güncellendi. ⚠️ Not: `autoload.php:25` hâlâ `CoreMusic\Auth\Middleware\` → `include/Middleware/` eşlemesi taşıyor (ölü eşleme; kaldırma onayı gerekir). `AGENTS.md` §2 de `include/Middleware/` yazıyor (bu revizyonda değiştirilmedi). |
| CORS `config/cors.php` | **KAPANDI** | Dosya VAR: `auth.coremusic.net/config/cors.php`. Maddeden kaldırılmadı; yol yazıldı. Not: `api.coremusic.net/v2/config/cors.php` ayrı dosya. |
| PRD-OAU-004 (SSO) | **KISMEN — ⚠️ VERIFICATION REQUIRED** | Tanım: `shared/src/OAuth/PRD.md:50` (kök `PRD.md`'de yok). Kanıt olan: session anahtarları auth `SessionManager` ile shared `SessionKeys` ile aynı literaller (`MM_UserID`, `MM_Username`, `MM_Email`, `cm_gender`…) — ancak `SessionManager` sabit kullanmıyor, literal yazıyor; `SessionKeys` auth'ta hiç referanslanmıyor. OAuth: `handler/OAuthPostHandler.php` (root, `include/` dışı) state üretir (`:46` `random_bytes(16)`) ve `OAuthManager::validateState` (`:82`) kullanır; ancak `$userId` çağıran tarafından parametre olarak alınır — oturumdan okunduğu kanıtlanamadı. `OAuthPostHandler` `index.php`'de ve `shared/config/auth-routes.php`'de **rota değil** (0 eşleşme). Sonuç: auth içinde çalışan bir OAuth/SSO akışı yok; PRD-PLT-004 "tek kimlik" bu yüzeyde kanıtlanmadı. |
| Test durumu | **KAPANDI** | `vendor\bin\phpunit --no-coverage` → `OK (51 tests, 152 assertions)` (baseline 46 + yeni 5). `php -l` (değişen 5 dosya) → hatasız. |

**Önerilen sonraki adım (karar bekler):** (a) `OAuthPostHandler` için auth route kararı (bağla veya ADR ile kapat); (b) `$userId`'nin session'dan geldiğinin kanıtı veya bağlama; (c) `autoload.php:25` ölü eşlemesinin kaldırılması. Shared/admin dokunulmadı.
