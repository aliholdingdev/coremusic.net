---
title: "CoreMusic — ADR-094: API Pipeline'ına OriginCheck + Koşullu CSRF Eklenmesi (ADR-020 Sıra Genişletmesi)"
type: "architecture-decision"
category: "security"
date: "2026-10-07"
updated: "2026-10-07"
version: "1.0.0"
status: "accepted"
authority: "API middleware sırası (ADR-020 §1.1-B.1) genişletildi: ResponseNormalization → OriginCheck → Cors → RateLimit → Authentication → [CSRF·yalnız session-auth] → RequestValidation → Authorization; kararı Vault Steward + kullanıcı onay zinciri (CoreMusic refactor planı 2026-10-07) verir"
kaynak: "B-F-02/B-F-03 bulguları (Full Codebase Analysis 2026-10-07) + disk kanıtı: api.coremusic.net/index.php pipeline, shared/src/Middleware/OriginCheckMiddleware.php, CsrfMiddleware.php, AuthenticationMiddleware.php:60-66 · şablon .ai/.templates/adr/adr-template.md (Guardrail #16)"
governance: "Red Team · Human Mode · Truth Mode"
---

# CoreMusic — ADR-094: API Pipeline'ına OriginCheck + Koşullu CSRF Eklenmesi

> **Durum:** ✅ **ACCEPTED** · **Tarih:** 2026-10-07 · **Ağırlık:** 1 (varsayılan) · **İlgili ADR:** 010, 012, 020, 022
> **Karar serisi:** `.ai/.decisions/accepted/` · **Slug:** `api-pipeline-origin-csrf` · **Dosya:** `ADR-094-api-pipeline-origin-csrf.md`
> **İlgili kararlar:** [[ADR-010-csrf-protection-strategy]] · [[ADR-012-csp-nonce-strict-dynamic]] · [[ADR-020-api-public-security]] · [[ADR-022-database-hardened-security]] · [[../index.md]]
> **Şablon:** `.ai/.templates/adr/adr-template.md` (Guardrail #16 — §1-§7 iskeleti)
> **Numara gerekçesi (disk kanıtı):** `accepted/` içinde en yüksek numara **ADR-093**; **ADR-091 dolu** (`.ai/.decisions/ADR-091-template-engine-no-eval.md` — register dışı, C-44), **ADR-092, ADR-093 mevcut** → **ilk boş numara 094**. Frozen **001-037'ye dokunulmadı**.

---

## §1 Bağlam (Context)

### §1.1 Bulgu Kanıtları (disk — Full Codebase Analysis 2026-10-07)

| # | Kaynak (dosya:satır) | Kanıt | Etiket |
|---|----------------------|-------|--------|
| 1 | `api.coremusic.net/index.php` (pipeline, değişim öncesi) | `ResponseNormalization → Cors → RateLimit → Authentication → RequestValidation → Authorization` — **OriginCheck yok, CSRF yok** | B-F-02 (High-scope bulgu) |
| 2 | `shared/src/Middleware/CorsMiddleware.php:59-77` | İzin verilmeyen Origin'de yalnız header **eklenmiyor**; istek **çalışmaya devam ediyor** | B-F-03 |
| 3 | `shared/src/Api/Middleware/AuthenticationMiddleware.php:60-66` | Hybrid auth: session (cookie) önce, sonra Bearer → **cookie-auth state-changing yüzey mevcut** | kanıt |
| 4 | `api.coremusic.net/config/routes.php:53+` | `POST /api/v1/auth/login` (public, implemented=true) + 5 POST uç daha | kanıt |
| 5 | `api.coremusic.net/CLAUDE.md` §4 Kural 3 | Sabit sıra ADR-020 §1.1-B.1 olarak dokümante; **Kural 9: sıraya middleware eklemek ADR ister** | governance |

**Web pipeline (ADR-010 frozen sıra #1) OriginCheck içerir; API pipeline içermez** → aynı Origin politikası iki farklı kapıda farklı uygulanıyordu.

### §1.2 Kısıtlamalar

- Bearer/API-key (header-auth) istemcileri CSRF'ye tabi tutulamaz — tarayıcı cross-site'te özel header forging yapamaz; kırıcı etki (legit curl POST'ların 403'ü) kabul edilemez.
- `CsrfMiddleware` (`shared/src/Middleware/CsrfMiddleware.php:35-42`) session token + `x-csrf-token` header/gövde eşleşmesi ister; pipeline request'te `headers`/`body` alanları yoktu.
- Origin yoksa (server-to-server, curl) OriginCheck geçmelidir (mevcut davranış: `OriginCheckMiddleware.php` boş Origin → `$next`).

### §1.3 Web Araştırması

⚠️ **VERIFICATION REQUIRED** — Bu karar oturum içi dahili kanıtlara (dosya:line + ADR-010/020) dayanır; OWASP CSRF Cheat Sheet vb. harici kaynaklar bu oturumda doğrulanmadı. İlgili harici iddialar doğrulanmadan bu ADR'ye dayanılarak güvenlik iddiası kurulmaz.

---

## §2 Karar (Decision)

### §2.1 Karar

API pipeline sırası ADR-020 §1.1-B.1'den şu şekilde genişletilir:

```text
ÖNCE:  ResponseNormalization → Cors → RateLimit → Authentication → RequestValidation → Authorization
SONRA:  ResponseNormalization → OriginCheck → Cors → RateLimit → Authentication → [Csrf·yalnız session] → RequestValidation → Authorization
```

1. **OriginCheck** (paylaşılan `CoreMusic\Middleware\OriginCheckMiddleware`, frozen sıra #1) CORS'tan ÖNCE eklenir; izinsiz browser Origin'i **403** ile reddedilir, Origin yoksa geçilir.
2. **Koşullu CsrfMiddleware**: yalnızca `AuthenticationMiddleware` isteği `_auth_user.method = 'session'` ile kimliklendirdiyse çalışır. Header-auth (Bearer/API key) ve public uçlar CSRF'ye tabi değildir.

### §2.2 Gerekçe

- Aynı Origin/CSRF politikası web ve API kapılarında hizalanır (SSOT davranış).
- Cookie-auth'lı API uçları (session başlatılan `/api/v1/auth/*`) SameSite=Lax dışındaki same-site tehditlere (yetkisiz subdomain) karşı OriginCheck + CSRF ile korunur.
- Mevcut API istemcileri (Bearer/curl; JS çağırıcısı diskte 0 — `grep /api/v1` assets/auth/home = 0) etkilenmez.

---

## §3 Alternatifler (Alternatives)

| # | Alternatif | Neden reddedildi |
|---|-----------|------------------|
| 1 | CorsMiddleware'i reddetmeye çevir (B-F-03 tek başına) | Sorumluluk ayrımı bozulur; Cors header yayıcıdır, kapıcı OriginCheck'tir (ADR-010 rol ayrımı). Web pipeline'da da OriginCheck reddeder. |
| 2 | CsrfMiddleware'i koşulsuz tüm API'ye ekle | Bearer/curl/public POST'lar kırılır (session token yok → hep 403); `/api/v1/auth/login` gibi public uçlar erişilemez hale gelir. |
| 3 | Hiçbir şey yapma (SameSite=Lax'a güven) | SameSite same-site (subdomain) saldırılarını engellemez; B-F-02/B-F-03 bulgusu açık kalır. |

---

## §4 Sonuçlar (Consequences)

**Olumlu:**
- API'de izinsiz Origin 403 (fail-closed) — tarayıcı tabanlı cross-origin side-effect kapandı.
- Cookie-auth state-changing API istekleri CSRF token şartına bağlandı; header-auth akmaya devam eder.
- Web ve API OriginCheck davranışları tek sınıfta birleşti (F-04 scheme normalizasyonu dahil).

**Olumsuz / Risk:**
- API sırası ADR-020 orijinalinden sapar → bu ADR + `api.coremusic.net/CLAUDE.md` §4 Kural 3 senkron tutulmalıdır (doküman drifti riski).
- Koşullu CSRF, `_auth_user.method` bilgisine bağımlıdır; Authentication sırası değişirse CSRF konumu yeniden değerlendirilmelidir.
- `CORS_ALLOWED_ORIGINS` env değeri hâlâ okunmadı (FORMAT bilinmiyor; F-04 normalizasyonu her iki formatı da karşılar — [VERIFICATION REQUIRED] canlı env).

**Risk:** Düşük — pipeline'a iki ek kapı; mevcut JS çağırıcısı yok, Bearer akışı korunur.

---

## §5 Uygulama (Implementation)

### §5.1 Uygulama Adımları (2026-10-07)

1. `api.coremusic.net/index.php`: `OriginCheckMiddleware` + `CsrfMiddleware` import; pipeline'a `OriginCheck` (Cors'tan önce) + koşullu CSRF (Authentication'tan sonra) pipe edildi.
2. `$pipelineRequest` alanlarına `headers` (`x-csrf-token`) + `body` (`$_POST` + JSON) eklendi — CsrfMiddleware sözleşmesi için zorunlu.
3. Doğrulama: `php -l` ✅ · api PHPUnit **32/32** ✅ · shared PHPUnit **311/311** ✅ · shared PHPStan seviye 5 ✅ (2026-10-07).

### §5.2 Geri Dönüş Planı (Rollback)

`api.coremusic.net/index.php` içindeki iki `->pipe(...)` satırı + `$pipelineRequest` headers/body alanları kaldırılır; ADR bu dosyada "superseded" olarak işaretlenir. Kod bağımlılığı yoktur (paylaşılan sınıflar web pipeline'da bağımsız kullanılır).

---

## §6 İlgili Dokümanlar

- `api.coremusic.net/index.php` (pipeline) · `api.coremusic.net/CLAUDE.md` §4 Kural 3/9
- `shared/src/Middleware/OriginCheckMiddleware.php` · `CsrfMiddleware.php` · `CorsMiddleware.php`
- `shared/src/Api/Middleware/AuthenticationMiddleware.php`
- [[ADR-010-csrf-protection-strategy]] · [[ADR-020-api-public-security]] · [[ADR-012-csp-nonce-strict-dynamic]]
- Refactor planı: `C:\.claude\plans\bir-coremusic-projemiz-var-eager-mango.md` (P1-5)

---

## §7 Onay

| Tarih | Karar Veren | Durum | Not |
|-------|-------------|-------|-----|
| 2026-10-07 | Bayram Ali (Vault Steward) · Claude Code (uygulama) | ACCEPTED | CoreMusic Full Codebase Analysis → Refactor Plan onayı (ExitPlanMode) kapsamında P1-5 |
