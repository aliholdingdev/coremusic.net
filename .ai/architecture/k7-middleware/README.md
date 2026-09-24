---
reference_doc: Freelancer Technical Documentation v1.0
title: "CoreMusic — K7 Middleware Layer"
type: architecture-layer
category: architecture
date: 2026-09-20
updated: 2026-09-24
source: "3 turlu agent tartışması"
status: active
version: 1.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
reference:
  authority: ".ai/CLAUDE.md"
  source_of_truth: ".ai/CLAUDE.md · .ai/AGENTS.md · .ai/brain.md"
---

# K7: Middleware Layer

**Katman:** K7 (Middleware)
**Kapsam:** OriginCheck, CORS, RateLimit, SecurityHeaders, Session, CSRF
**Sorumlu Agent:** Backend Architect
**Bileşen Sayısı:** 35

---

## 1. Genel Bakış

K7 katmanı, K6 güvenliğini ve K9 API'sini birbirine bağlayan middleware pipeline'ını içerir. **Sıra DEĞİŞTİRİLEMEZ** (ADR-010/011/012/013/022).

---

## 2. Pipeline (Sıra Değişmez)

```
1. OriginCheckMiddleware()      → Köken doğrulama (whitelist CORS)
2. CorsMiddleware()             → CORS header'ları (whitelist only)
3. RateLimiterMiddleware()      → APCu: 60 req/60s
4. SecurityHeadersMiddleware()  → CSP nonce üret, strict-dynamic, HSTS, X-Frame
5. SessionManagerMiddleware()   → Session başlat, CSP nonce'u session'a kaydet
6. CsrfMiddleware()             → csrf_token doğrulama (POST/PUT/DELETE)
7. BypassAuthMiddleware()       → Test bypass (production'da devre dışı)
8. AuthMiddleware()             → Auth bilgisi inject (session'dan okur)
9. PermissionMiddleware()       → RBAC yetki kontrolü (regular/premium/studio/car/admin/system)
10. ValidationMiddleware()      → Request/DTO validasyonu
→ Controller
```

---

## 3. Middleware Detayları

### 3.1 OriginCheckMiddleware (#1)

```php
// Whitelist CORS origins
$allowedOrigins = [
    'https://coremusic.net',
    'https://music.coremusic.net',
    'https://admin.coremusic.net',
    'https://home.coremusic.net',
    'http://localhost:81', // Development
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (!in_array($origin, $allowedOrigins)) {
    http_response_code(403);
    exit;
}
```

### 3.2 SecurityHeadersMiddleware (#4)

```php
// CSP nonce üretimi
$nonce = base64_encode(random_bytes(32));

// CSP header
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-{$nonce}' 'strict-dynamic'; style-src 'self' 'nonce-{$nonce}'; img-src 'self' data: https:; font-src 'self'; connect-src 'self' wss://api.coremusic.net; media-src 'self'; object-src 'none'; frame-ancestors 'none'; base-uri 'self'; form-action 'self';");

// Other security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('Referrer-Policy: strict-origin-when-cross-origin');
```

### 3.3 SessionManagerMiddleware (#5)

```php
// Session başlat
session_start([
    'name' => 'COREMUSIC_SESS',
    'cookie_lifetime' => 3600,
    'cookie_httponly' => true,
    'cookie_secure' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
]);

// CSP nonce'u session'a kaydet
$_SESSION['csp_nonce'] = $nonce;
```

---

## 4. Sıra Uyumsuzluğu Riskleri

| Sıra Hatası | Sonuç |
|-------------|-------|
| SecurityHeaders → SessionManager önce | CSP nonce bozulur |
| CSRF → Auth önce | Token doğrulanamaz |
| RateLimit → SecurityHeaders sonra | Rate limit nonce alamaz |
| BypassAuth → Auth sonra | Auth bypass başarısız |

---

## 5. İlgili ADR'ler

| ADR | Konu |
|-----|------|
| ADR-010 | csrf_token key zorunlu |
| ADR-011 | COREMUSIC_SESS, 3600s idle timeout |
| ADR-012 | strict-dynamic, nonce-based CSP |
| ADR-013 | APCu, 60 req/60s |
| ADR-022 | AES-256-GCM, Argon2id |

---

*K7 Middleware Layer v1.0.0 — CoreMusic Architecture*
*Authority: Bayram Ali / Vault Steward*
*Last Updated: 2026-09-24*
*Mode: Red Team · Human Mode · Truth Mode*

---

## Alt Katman Şeması (K7.a.b.c)

> **Revizyon (2026-09-24):** Bu bölüm 3 turlu agent tartışması sonucu eklenmiştir. §1–§5 (mevcut içerik) değiştirilmemiş, silinmemiştir.
> **Bağlayıcı adlandırma kuralı:** .ai/architecture/adlandirma-kurali.md — K{n} → K{n}.a → K{n}.b zorunlu; K{n}.a.b.c (4. düzey) yalnız güçlü kanıtla; ".0." ara düzeyi YASAK; klasör adları lowercase-hyphen; belge içinde K numarası taşınır.

### 1. Kaynak Tablosu (bu şemanın kanıtları)

| # | Kaynak | Sağladığı kanıt |
|---|--------|-----------------|
| 1 | .ai/CLAUDE.md §6 Middleware Pipeline (Immutable — ADR-010/011/012/013/022) | 10 middleware sırası, görev, timeout (düzenleyici kaynak) |
| 2 | .ai/CLAUDE.md §2.1 SSOT Priority Order | Çelişkide CLAUDE.md > index.md (kural #2) |
| 3 | .ai/CLAUDE.md §7 (Guardrail #7), §8/#4, §20, §21, §23 | Sıra değişmezliği, BypassAuth, ADR tablosu, yasaklar, kritik uyarılar |
| 4 | frontend-restructuring-plan.md §2.1 (L7.1–L7.10 satırları) | Düzey-2 eşlemesi: L7.n → K7.n (doğrudan, ara düzey yok) |
| 5 | k7-middleware/README.md §2, §3.1–§3.3, §4, §5 | Sıra listesi, kod kanıtları, risk tablosu, ADR tablosu |
| 6 | k7-middleware/index.md | Pipeline Pozisyonu, PSR-15, sıralama/erken-durdurma tablosu, config, performans metrikleri, bağımlılıklar, fazlar |
| 7 | .ai/.decisions/index.md (ADR-010/011/012/013/022 satırları) | Karar kayıt adları ve durumları |
| 8 | .ai/AGENTS.md §21 (ADR-008-bypass-auth-middleware satırı) | BypassAuth karar referansı |
| 9 | disk glob: k7-middleware/*.md = 14 dosya | Düzey-3 dosya kanıtları (10) + kök kanıtlar (4) + README/index/CLAUDE |
| 10 | .ai/CLAUDE.md §5 K7 satırı + k9-api-routing/README.md §2.1 | K7'nin K6 ile K9 arasındaki sınırı |

### 2. Şema Kuralları (bağlayıcı)

| # | Kural | İhlali |
|---|-------|--------|
| 1 | K7 → K7.a → K7.b zorunlu: her düzey-2 düğümün en az 1 düzey-3 çocuğu vardır | Şema geçersiz sayılır |
| 2 | Düzey-2 = tam 10 düğüm (K7.1–K7.10) ve sıra CLAUDE.md §6 ile birebir aynıdır | Guardrail #7 ihlali — sistem durdurulur |
| 3 | ".0." ara düzeyi kullanılamaz: K7.0.x REDDEDİLDİ; plan L7.1–L7.10 doğrudan K7.1–K7.10'a eşlenir | Şema geçersiz sayılır |
| 4 | Düzey-4 (K7.a.b.c) bu katmanda YOKTUR; bu revizyonda hiç düğüm benimsenmedi | Şema geçersiz sayılır |
| 5 | Düğüm adları lowercase-hyphen (ör. origin-check); her belgede K7 numarası korunur | Şema geçersiz sayılır |
| 6 | Her satırın kanıt sütunu zorunludur; kanıtsız iddia = VERIFICATION REQUIRED (Guardrail #3) | İçerik silinir |

### 3. Düzey-2 Düğümler (K7.a — 10 düğüm, sıra DEĞİŞTİRİLEMEZ)

| Kod | Düğüm adı | Pipeline # (CLAUDE.md §6) | Görev | Düzey-2 kanıtı |
|-----|-----------|:-------------------------:|-------|----------------|
| K7.1 | origin-check | 1 | Köken doğrulama (whitelist CORS) | CLAUDE.md §6 #1 · plan L7.1 · origin-check.md |
| K7.2 | cors | 2 | CORS header yönetimi | CLAUDE.md §6 #2 · plan L7.2 · cors-policy.md |
| K7.3 | rate-limiter | 3 | APCu tabanlı, 60 req/60s (timeout 60s) | CLAUDE.md §6 #3 · plan L7.3 · ADR-013 |
| K7.4 | security-headers | 4 | CSP strict-dynamic, X-Frame-Options, HSTS | CLAUDE.md §6 #4 · plan L7.4 · ADR-012 |
| K7.5 | session-manager | 5 | Session başlatır, CSP nonce'u session'a kaydeder (3600s idle) | CLAUDE.md §6 #5 · plan L7.5 · ADR-011 |
| K7.6 | csrf | 6 | csrf_token doğrulama (POST/PUT/DELETE) | CLAUDE.md §6 #6 · plan L7.6 · ADR-010 |
| K7.7 | bypass-auth | 7 | Test bypass (?_bypass=1), prod'da devre dışı | CLAUDE.md §6 #7 · plan L7.7 · ADR-008 (AGENTS §21) |
| K7.8 | auth | 8 | Auth bilgisi inject (JWT + Session) | CLAUDE.md §6 #8 · plan L7.8 · CLAUDE §26.1 prompt2 |
| K7.9 | permission | 9 | RBAC yetki kontrolü (regular/premium/studio/car/admin/system) | CLAUDE.md §6 #9 · plan L7.9 · CLAUDE §5 K6 |
| K7.10 | validation | 10 | Request/DTO validasyonu | CLAUDE.md §6 #10 · plan L7.10 · request-validation.md |

**Sıra kilidi:** CLAUDE.md §6 "Kritik Not" — CSP nonce üretimi SecurityHeaders (#4) içindedir, SessionManager (#5) bunu session'a kaydeder; sıra değiştirilirse CSP bozulur.

### 4. Düzey-3 Düğümler (K7.a.b — 43 düğüm)

#### K7.1 origin-check — Köken Doğrulama (pipeline #1)

İlk adımdır; whitelist dışındaki kökeni 403 ile erken durdurur. Düzey-3 kanıtları: README §3.1 kod bloğu, index.md sıralama tablosu, disk dosyası origin-check.md.

| Düğüm | Sorumluluk | Kanıt |
|-------|-----------|-------|
| K7.1.1 | origin-whitelist — izinli köken listesi kontrolü (coremusic.net, music/admin/home.coremusic.net, localhost:81) | README §3.1 $allowedOrigins kodu |
| K7.1.2 | reject-403 — whitelist dışı kökende 403 + exit | README §3.1 (http_response_code(403)) · index.md "Origin Check — Evet (403)" |
| K7.1.3 | dev-origin — geliştirme kökeni (http://localhost:81) | README §3.1 satır 5 |
| K7.1.4 | pipeline-konumu — sıranın 1 numaralı olması | CLAUDE.md §6 #1 · plan L7.1 · origin-check.md (disk, "Origin Check Middleware") |

#### K7.2 cors — CORS Header Yönetimi (pipeline #2)

Whitelist-only prensiple header üretir; preflight için max-age tanımlıdır.

| Düğüm | Sorumluluk | Kanıt |
|-------|-----------|-------|
| K7.2.1 | cors-headers — CORS başlıklarının üretilmesi | README §2 #2 · cors-policy.md (disk, "CORS Policy Middleware") |
| K7.2.2 | whitelist-only — yalnız izinli origin'lere izin | README §2 #2 (whitelist only) |
| K7.2.3 | methods-headers-maxage — allowed_methods, allowed_headers, max_age 86400 | index.md §Kod/Konfigürasyon cors bloğu |
| K7.2.4 | pipeline-konumu — sıranın 2 numaralı olması | CLAUDE.md §6 #2 · plan L7.2 |

#### K7.3 rate-limiter — İstek Sınırlama (pipeline #3)

APCu tabanlı 60 req/60s; erken durdurma 429 döndürür.

| Düğüm | Sorumluluk | Kanıt |
|-------|-----------|-------|
| K7.3.1 | apcu-60-60 — APCu sürücüsü, 60 istek / 60 saniye | CLAUDE.md §6 #3 · ADR-013 (decisions: "Rate Limiting APCu") |
| K7.3.2 | timeout-60s — middleware timeout değeri 60s | CLAUDE.md §6 tablo Timeout sütunu |
| K7.3.3 | early-stop-429 — erken durdurma (429 yanıtı) | index.md §Middleware Sıralama Mantığı satır 3 |
| K7.3.4 | composer-ext — predis/predis bağımlılığı (rate limit için) | index.md §Bağımlılıklar Redis satırı |
| K7.3.5 | rate-limit-middleware.md — disk kanıtı | glob: k7-middleware/rate-limit-middleware.md ("Rate Limit Middleware") |

#### K7.4 security-headers — Güvenlik Başlıkları (pipeline #4)

CSP nonce üretimi BU adımdadır (sıra kilidinin çekirdeği).

| Düğüm | Sorumluluk | Kanıt |
|-------|-----------|-------|
| K7.4.1 | csp-nonce — nonce üretimi (base64 random_bytes(32)) ve CSP header'ı | README §3.2 kod · ADR-012 |
| K7.4.2 | strict-dynamic — script-src 'self' 'nonce-...' 'strict-dynamic' | README §3.2 · CLAUDE.md §6 #4 · ADR-012 |
| K7.4.3 | hsts-xfo — HSTS max-age 31536000, X-Frame-Options DENY, nosniff, Referrer-Policy | README §3.2 (header satırları) |
| K7.4.4 | nonce-handoff — nonce'un bir sonraki adıma devri K7.5'e aittir (üretim K7.4'te) | CLAUDE.md §6 Kritik Not · CLAUDE §23 #1 |
| K7.4.5 | security-headers-middleware.md — disk kanıtı | glob: k7-middleware/security-headers-middleware.md |

#### K7.5 session-manager — Oturum Yönetimi (pipeline #5)

Session'ı başlatır ve K7.4'ün nonce'unu session'a kaydeder; 3600s idle timeout.

| Düğüm | Sorumluluk | Kanıt |
|-------|-----------|-------|
| K7.5.1 | session-start — COREMUSIC_SESS, cookie_lifetime 3600, httponly, secure, samesite Lax, strict_mode | README §3.3 kod · ADR-011 |
| K7.5.2 | csp-nonce-kaydi — $_SESSION['csp_nonce'] = $nonce | README §3.3 son satır · CLAUDE.md §6 #5 |
| K7.5.3 | idle-timeout-3600 — 3600s idle (çelişki defteri #2: index config 7200 reddedildi) | CLAUDE.md §6 #5 · ADR-011 (decisions: "Session Management") |
| K7.5.4 | reorder-risk — SecurityHeaders sonra gelmezse CSP nonce bozulur | README §4 satır 1 · CLAUDE §23 #1 |
| K7.5.5 | session-middleware.md — disk kanıtı | glob: k7-middleware/session-middleware.md ("Session Middleware") |

#### K7.6 csrf — CSRF Doğrulama (pipeline #6)

State-changing methodlarda csrf_token doğrulanır.

| Düğüm | Sorumluluk | Kanıt |
|-------|-----------|-------|
| K7.6.1 | token-dogrulama — csrf_token doğrulaması (POST/PUT/DELETE) | CLAUDE.md §6 #6 · README §2 #6 · ADR-010 |
| K7.6.2 | token-adi — anahtar adı csrf_token; _csrf_token YASAK | CLAUDE.md §7 Guardrail #6 · §21 Forbidden Patterns |
| K7.6.3 | reorder-risk — Auth önce gelirse token doğrulanamaz | README §4 satır 2 |
| K7.6.4 | csrf-middleware.md — disk kanıtı (hash_equals kanıtlı) | glob: k7-middleware/csrf-middleware.md · .agents/backend-architect.md ADR-010 satırı |

#### K7.7 bypass-auth — Test Bypass (pipeline #7)

Üretimde devre dışıdır; test ortamında aktif edilebilir.

| Düğüm | Sorumluluk | Kanıt |
|-------|-----------|-------|
| K7.7.1 | bypass-flag — ?_bypass=1 test bypass bayrağı | CLAUDE.md §6 #7 |
| K7.7.2 | prod-guard — production'da devre dışı kalması | README §2 #7 · CLAUDE.md §8 Soft Constraint #4 |
| K7.7.3 | reorder-flag — Auth (#8) ÖNCE gelmelidir, yoksa bypass başarısız olur | README §4 satır 4 |
| K7.7.4 | adr-008-referans — karar referansı | AGENTS.md §21 (ADR-008-bypass-auth-middleware) |
| — | DURUM: disk kanıtı YOK — k7-middleware/ altında bypass-auth.md yok (glob=14 md); kanıt CLAUDE §6 + plan L7.7 | Truth Mode |

#### K7.8 auth — Auth Enjeksiyonu (pipeline #8)

JWT + Session hibrit modelden auth bilgisini isteğe enjekte eder.

| Düğüm | Sorumluluk | Kanıt |
|-------|-----------|-------|
| K7.8.1 | jwt-session-inject — auth bilgisi inject (JWT + Session) | CLAUDE.md §6 #8 |
| K7.8.2 | session-okuma — auth bilgisi session'dan okunur | README §2 #8 |
| K7.8.3 | hybrid-model — merkezi auth.coremusic.net, hybrid JWT+session | CLAUDE.md §26.1 prompt2 satırı |
| K7.8.4 | pipeline-konumu — sıranın 8 numaralı olması | CLAUDE.md §6 #8 · plan L7.8 |
| — | DURUM: disk kanıtı YOK — auth-middleware.md yok (glob=14 md) | Truth Mode |

#### K7.9 permission — RBAC Yetki Kontrolü (pipeline #9)

K6'daki RBAC politikasını istek bazında uygular; atlanamaz.

| Düğüm | Sorumluluk | Kanıt |
|-------|-----------|-------|
| K7.9.1 | rbac-6-rol — regular/premium/studio/car/admin/system rolleri | CLAUDE.md §6 #9 |
| K7.9.2 | k6-uygulama — K6 JWT/RBAC kararlarının middleware uygulayıcısı (K6 asla bypass edilemez) | CLAUDE.md §5 K6 satırı (Hard Guardrail) |
| K7.9.3 | yetki-red — yetkisiz istekte reddi middleware seviyesinde verir | README §2 #9 · index.md "RBAC" (PSR-15 sıralama) |
| K7.9.4 | pipeline-konumu — sıranın 9 numaralı olması | CLAUDE.md §6 #9 · plan L7.9 |
| — | DURUM: disk kanıtı YOK — permission-middleware.md yok (glob=14 md) | Truth Mode |

#### K7.10 validation — Request/DTO Validasyonu (pipeline #10)

Controller'dan hemen öncesi son middleware; API-First sözleşmesinin denetçisidir.

| Düğüm | Sorumluluk | Kanıt |
|-------|-----------|-------|
| K7.10.1 | request-dto — Request/DTO validasyonu | CLAUDE.md §6 #10 · request-validation.md (disk) |
| K7.10.2 | early-stop-422 — doğrulama hatasında erken durdurma (422) | index.md §Middleware Sıralama Mantığı satır 8 |
| K7.10.3 | contract-denetimi — OpenAPI → DTO → Contract → Validation zincirinin halkası | CLAUDE.md §6A · README §6.1 (K9 dokümanı, sınır kanıtı) |
| K7.10.4 | psr15-arayuz — Psr\Http\Server\MiddlewareInterface uygulaması | index.md §PSR-15 Uyumluluğu |

### 5. Sıra Matrisi ve Çelişki Kayıt Defteri (Truth Mode)

Kaynaklar arasındaki sıra farkı (SSOT: CLAUDE.md §2.1 — CLAUDE.md kazanır):

| # | CLAUDE.md §6 (KAZANAN — şema sırası) | README §2 | index.md Pipeline Pozisyonu | Şema karşılığı |
|---|--------------------------------------|-----------|------------------------------|----------------|
| 1 | OriginCheck | 1. OriginCheck | [5] Origin Check | K7.1 |
| 2 | Cors | 2. Cors | [4] CORS | K7.2 |
| 3 | RateLimiter | 3. RateLimiter | [3] Rate Limit | K7.3 |
| 4 | SecurityHeaders | 4. SecurityHeaders | [2] Security Headers | K7.4 |
| 5 | SessionManager | 5. SessionManager | [6] Session | K7.5 |
| 6 | Csrf | 6. Csrf | [7] CSRF | K7.6 |
| 7 | BypassAuth | 7. BypassAuth | — (index.md'de YOK) | K7.7 |
| 8 | Auth | 8. Auth | — (index.md'de YOK) | K7.8 |
| 9 | Permission | 9. Permission | — (index.md'de YOK) | K7.9 |
| 10 | Validation | 10. Validation | [8] Request Validation | K7.10 |
| + | — (CLAUDE §6'da YOK) | — | [1] Compression | kök kanıt (aşağıda) |
| + | — (CLAUDE §6'da YOK) | — | [9] Logging | kök kanıt (aşağıda) |
| + | — (CLAUDE §6'da YOK) | — | [10] Error Handler | kök kanıt (aşağıda) |

**Çelişki kaydı:**

| # | Çelişki | Kazanan değer | Gerekçe |
|---|---------|---------------|---------|
| C1 | Pipeline sırası: CLAUDE §6 vs index.md Pipeline Pozisyonu (farklı 10 adım) | CLAUDE.md §6 | §2.1 SSOT önceliği; Guardrail #7 |
| C2 | Rate limit: APCu 60/60 (CLAUDE, ADR-013) vs driver redis 100/60 (index.md config) | APCu 60/60 | ADR-013 Frozen + CLAUDE kazanır |
| C3 | Session ömrü: 3600s (CLAUDE §6, ADR-011) vs lifetime 7200 (index.md config) | 3600s | ADR-011 Frozen + CLAUDE kazanır |
| C4 | CORS origin listesi: coremusic.net ailesi (README §3.1) vs https://app.coremusic.io (index.md config) | coremusic.net ailesi | README §6 üretildiği kaynak + CLAUDE §9 subdomain tablosu |
| C5 | Compression/Logging/ErrorHandler: index.md pipeline'ında var, CLAUDE §6'da yok | Düzey-2 DEĞİL | 10 adım kilidi (kural #2); kök kanıt olarak kaydedildi |

### 6. Erken Durdurma, ADR ve Performans Kanıtları

**Erken durdurma matrisi (index.md §Middleware Sıralama Mantığı):**

| Düğüm | Erken durdurma | Kod |
|-------|----------------|-----|
| K7.3 rate-limiter | Evet | 429 |
| K7.2 cors | Evet | 403 |
| K7.1 origin-check | Evet | 403 |
| K7.6 csrf | Evet | 403 |
| K7.10 validation | Evet | 422 |
| K7.4/K7.5/K7.8/K7.9 | Hayır | — (yanıt üretmez, devam eder) |

**ADR bağları (README §5 + .ai/.decisions/index.md):**

| ADR | Karar adı (decisions index) | Düğüm |
|-----|------------------------------|-------|
| ADR-010 | CSRF Protection Strategy (Frozen) | K7.6 |
| ADR-011 | Session Management (Frozen) | K7.5 |
| ADR-012 | CSP Nonce Strict-Dynamic (Frozen) | K7.4 |
| ADR-013 | Rate Limiting APCu (Frozen) | K7.3 |
| ADR-022 | Database Hardened Security (Frozen) | sınır — README §5'te listelenir, K7.10/K7.8 üzerinden K5'e referans |
| ADR-008 | bypass-auth-middleware (AGENTS §21) | K7.7 |

**Performans hedefleri (index.md §Performans Metrikleri):**

| Metrik | Hedef | İlgili düğüm |
|--------|-------|--------------|
| Middleware pipeline overhead | < 0.5ms | K7 tümü |
| Ortalama middleware processing time | < 0.05ms | K7 tümü |
| Rate limit lookup latency | < 1ms | K7.3 |
| Session loading overhead | < 2ms | K7.5 |
| Compression CPU overhead | < 5% | kök: compression-middleware.md |

**Bağımlılıklar (index.md §Bağımlılıklar):** PSR-15 (psr/http-server-middleware) · PSR-7 (psr/http-message) · PSR-3 (psr/log) · predis/predis (rate limit) · matthiasmullie/minify (compression) — her biri zorunluluk sütunuyla birlikte bu şemanın K7.1–K7.10 dış altyapı sözleşmesidir.

**Uygulama fazları → düğüm eşlemesi (index.md §Durum: Implementasyon):**

| Faz | Kapsam | Düğüm karşılığı |
|-----|--------|-----------------|
| Faz 1 | PSR-15 Pipeline temel yapısı | K7.1–K7.10 iskeleti + kök: psr15-pipeline.md |
| Faz 2 | Rate limit ve security headers | K7.3, K7.4 |
| Faz 3 | Session ve CSRF koruması | K7.5, K7.6 |
| Faz 4 | Request validation ve error handling | K7.10 + kök: error-handler.md |
| Faz 5 | Logging ve compression optimizasyonu | kök: logging-middleware.md, compression-middleware.md |

### 7. Katman Sınırları (K6 ↔ K7 ↔ K9)

| Sınır | Kural | Kanıt |
|-------|-------|-------|
| K6 → K7 | K6 güvenlik doğrulaması ATLANAMAZ (K7 hard guardrail) | CLAUDE.md §5 K7 satırı |
| K7 → K9 | K9 gateway'i middleware pipeline'ını çağırır: Client → Gateway → Middleware → Use Case | k9-api-routing/README.md §2.1 · CLAUDE.md §6A.5 |
| K7 → K5/K6 | K7 doğrudan DB'ye yazmaz; auth verisi K6 (JWT/session) kaynağından gelir | CLAUDE.md §5, §6 #8 |

### 8. Kök Kanıtlar (düzey-2 yapılamaz — 10 adım kilidi nedeniyle)

| Dosya (disk) | Başlık | Neden kök? |
|--------------|--------|------------|
| psr15-pipeline.md | PSR-15 Pipeline Middleware | Tüm K7.1–K7.10'un taşıyıcı iskeleti; tek bir middleware değil |
| error-handler.md | Error Handler Middleware | CLAUDE §6'da yok; index.md'de [10] — 10 adım kilidi nedeniyle düzey-2 yapılamaz (çelişki C5) |
| logging-middleware.md | Logging Middleware | Aynı neden: index.md [9], CLAUDE §6'da yok (çelişki C5) |
| compression-middleware.md | Compression Middleware | Aynı neden: index.md [1], CLAUDE §6'da yok (çelişki C5) |

### 9. Düzey-4 Durumu

**Bu katmanda düzey-4 düğüm YOKTUR (0 adet).** Doğrulama: tüm şemada benimsenen tek gerçek düzey-4 düğüm K11.1.4.13'tür (frontend-restructuring-plan.md §2.2, c-player.css). K7 için düzey-4 talebi gelirse ek kanıt (yeni disk MD veya plan satırı) zorunludur; uydurma düğüm eklenmez (Guardrail #3).

### 10. Sayım Özeti (K7)

| Seviye | Onaylı hedef (X/Y/Z = T) | Gerçek (2026-09-24 sayımı) | Durum |
|--------|:-------------------------:|:---------------------------:|-------|
| Düzey-2 (K7.a) | 10 | 10 | ✅ |
| Düzey-3 (K7.a.b) | 5 * | 43 | * tanım belirsiz — gerçek şemanın kendisinden sayıldı |
| Düzey-4 (K7.a.b.c) | 140 * | 0 | * tanım belirsiz — kanıt yok, uydurulmadı |
| Toplam T | 190 (X·Y+Z=10·5+140) | — | * bkz. not |

> **Not (Truth Mode):** Hedef formatındaki Y ve Z'nin tanımı paylaşılmadı. X·Y+Z denklemi K7–K12 katmanlarında tutuyor, K13'te tutmuyor (7·5+90=125 ≠ 95 — kaynakta tutarsızlık). Ayrıca "her dosya ≥500 satır" hedefi ile "7 dosya toplamı 1.235" hedefi birlikte sağlanamaz (7×500=3.500). Bu revizyonda öncelik: (1) gerçek kanıt, (2) düzey-2 = onaylı X, (3) dosya başı ≥500 satır. Sapmalar raporlanmıştır.

### 11. Düzey-2 → Kaynak Çapraz Referans Matrisi

| Düğüm | CLAUDE §6 sıra | plan §2.1 | Disk MD (kanıt) | ADR |
|-------|:-:|:-:|---|---|
| K7.1 origin-check | #1 | L7.1 | origin-check.md | — |
| K7.2 cors | #2 | L7.2 | cors-policy.md | — |
| K7.3 rate-limiter | #3 | L7.3 | rate-limit-middleware.md | ADR-013 |
| K7.4 security-headers | #4 | L7.4 | security-headers-middleware.md | ADR-012 |
| K7.5 session-manager | #5 | L7.5 | session-middleware.md | ADR-011 |
| K7.6 csrf | #6 | L7.6 | csrf-middleware.md | ADR-010 |
| K7.7 bypass-auth | #7 | L7.7 | YOK (dürüstlük kaydı §10) | ADR-008 |
| K7.8 auth | #8 | L7.8 | YOK | — |
| K7.9 permission | #9 | L7.9 | YOK | — |
| K7.10 validation | #10 | L7.10 | request-validation.md | — |

**Doğrulama:** 10/10 satır CLAUDE §6 sırasıyla birebir aynı; 7/10 düğümün disk MD'si var, 3/10 yok (uydurulmadı). plan §2.1 L7.1–L7.10 = 10 satır, dosyadaki sıra ile çakışma yok.

### 12. Şema Kuralı Uyum Matrisi (adlandirma-kurali.md #1–#5)

| Kural | K7 Uygulaması | Durum |
|-------|---------------|-------|
| #1 Kök K{n} | K7 (plan §2.1 L7 kökü) | ✅ |
| #2 Düzey-2 K{n}.a | K7.1–K7.10 (küçük harf-tire) | ✅ |
| #3 Düzey-3 K{n}.a.b | K7.a.b (ör. K7.3.1) — 43 düğüm | ✅ |
| #4 Düzey-4 K{n}.a.b.c | K7'de 0 (yalnız gerçek L4 = K11.1.4.13) | ✅ |
| #5 ".0." yasak | Hiçbir düğümde ".0." yok | ✅ |

### 13. Kapsam Dışı ve Bilinen Boşluklar

| # | Boşluk | Durum | Sonraki Eylem |
|---|--------|-------|---------------|
| 1 | K7.7/K7.8/K7.9 disk MD'si yok | Açık (dosya 447) | Kanıt MD gelirse §10 tablosu güncellenir |
| 2 | error-handler/logging/compression CLAUDE §6'da yok (C5) | Açık | 10 adım kilidi nedeniyle düzey-2 yapılmaz; kök kanıt sayılır |
| 3 | index.md config (redis 100/60, 7200s) CLAUDE §6 ile çelişiyor (C2/C3) | Açık — CLAUDE kazanır | index.md config satırı düzeltilmeli (SSOT §2.1) |
| 4 | Faz 5 (logging+compression optimizasyonu) | index.md'de durum: implementasyon | K7 dışı iş — uygulama fazı K13/K12 gözlemiyle doğrulanır |
| 5 | predis/predis bağımlılığı ADR-013 (APCu) ile potansiyel çakışma | İncelenmedi | ADR-013 Frozen — bağımlılık satırı ADR ile teyit edilmeli |

### 14. Revizyon ve Denetim Notu

| Öğe | Değer |
|-----|-------|
| Bu revizyon | v1.1.0 — 2026-09-24 (frontmatter updated + source eklendi, footer güncellendi) |
| Önceki durum | v1.0.0 — 133 satır, yalnız §1–§10 (orijinal içerik silinmedi, H2'ler korundu) |
| Eklenen H2 | ## Alt Katman Şeması (K7.a.b.c) · ## Kanıt Kataloğu |
| Denetim izi | Kanıtlar: CLAUDE.md §5/§6/§7 · plan §2.1 L7.1–L7.10 · k7-middleware/ (14 dosya) · .decisions/index.md (5 ADR satırı) · AGENTS.md §21 (ADR-008) |
| Sonraki tetik | K7'ye yeni dosya/ADR eklenirse §10 Sayım Özeti + §11 matris + Kanıt Kataloğu birlikte yeniden sayılır |

---

## Kanıt Kataloğu

> Bu katmanın diskteki TÜM .md dosyaları (glob: 14 adet) ve hangi sayımın kanıtını taşıdıkları. Dosya başına 2 satır: açıklama + desteklediği sayaç.

- **README.md** (CoreMusic — K7 Middleware Layer) — katman ana belgesi; §2 sıra listesi, §3.1–§3.3 kod kanıtları, §4 risk tablosu, §5 ADR tablosu.
  → Destek: K7.1–K7.10 düzey-2 sırası, K7.1.1–K7.1.3, K7.4.1–K7.4.3, K7.5.1–K7.5.2, K7.6.1–K7.6.3, K7.7.2–K7.7.3 (düzey-3: 43).
- **index.md** (K7 Middleware Katmanı - Genel Bakış) — PSR-15 uyumluluğu, Pipeline Pozisyonu, sıralama/erken-durdurma tablosu, MiddlewareRegistry, config/middleware.php, performans metrikleri, bağımlılıklar, fazlar.
  → Destek: K7.2.3, K7.3.3–K7.3.5, K7.10.2, K7.10.4, çelişki C1–C5, erken-durdurma matrisi, faz eşlemesi.
- **CLAUDE.md** (CoreMusic — K7 Middleware CLAUDE.md) — katmana özgü CLAUDE kestirmesi/şablon girişi.
  → Destek: kanıt kaynakları tablosu (kural #6 için referans), katalog bütünlüğü (14 dosya sayımı).
- **origin-check.md** (Origin Check Middleware) — K7.1'in disk kanıtı.
  → Destek: K7.1, K7.1.4 (düzey-2/3: 2).
- **cors-policy.md** (CORS Policy Middleware) — K7.2'nin disk kanıtı.
  → Destek: K7.2, K7.2.1 (2).
- **rate-limit-middleware.md** (Rate Limit Middleware) — K7.3'ün disk kanıtı.
  → Destek: K7.3, K7.3.5 (2).
- **security-headers-middleware.md** (Security Headers Middleware) — K7.4'ün disk kanıtı.
  → Destek: K7.4, K7.4.5 (2).
- **session-middleware.md** (Session Middleware) — K7.5'in disk kanıtı.
  → Destek: K7.5, K7.5.5 (2).
- **csrf-middleware.md** (CSRF Middleware) — K7.6'nın disk kanıtı (hash_equals).
  → Destek: K7.6, K7.6.4 (2).
- **request-validation.md** (Request Validation Middleware) — K7.10'un disk kanıtı.
  → Destek: K7.10, K7.10.1 (2).
- **psr15-pipeline.md** (PSR-15 Pipeline Middleware) — taşıyıcı iskelet; kök kanıt (§8).
  → Destek: kök sayımı (4 kökten 1), Faz 1 eşlemesi — düzey-2 DEĞİL.
- **error-handler.md** (Error Handler Middleware) — kök kanıt (çelişki C5).
  → Destek: kök sayımı (2/4), Faz 4 eşlemesi — düzey-2 DEĞİL.
- **logging-middleware.md** (Logging Middleware) — kök kanıt (çelişki C5).
  → Destek: kök sayımı (3/4), Faz 5 eşlemesi — düzey-2 DEĞİL.
- **compression-middleware.md** (Compression Middleware) — kök kanıt (çelişki C5).
  → Destek: kök sayımı (4/4), performans metriği (compression CPU <5%), Faz 5 — düzey-2 DEĞİL.

**Düzey-2'de karşılığı OLMAYAN 3 düğüm (dürüstlük kaydı):** K7.7 bypass-auth, K7.8 auth, K7.9 permission — bu üçünün disk MD'si YOK; kanıtları CLAUDE.md §6 #7–#9 + plan §2.1 L7.7–L7.9 + AGENTS §21 (ADR-008)'dir. Dosya eklenirse bu tablo güncellenmelidir.

**Harici kanıt kaynakları (katalog kapsamı):**

| Kaynak | Kullanım |
|--------|----------|
| .ai/CLAUDE.md §6, §7 #7, §8 #4, §20, §21, §23 | Sıra, guardrail, ADR, yasak, uyarı kanıtları |
| .ai/architecture/frontend-restructuring-plan.md §2.1 L7.1–L7.10 | Düzey-2 eşleme kanıtı (10 satır) |
| .ai/.decisions/index.md ADR-010/011/012/013/022 | Karar adları (6 satır) |
| .ai/AGENTS.md §21 | ADR-008 bypass-auth referansı |
| .ai/architecture/adlandirma-kurali.md | Şema adlandırma kuralları (kural #1–#5) |
| disk glob (k7-middleware/*.md) | 14 dosya: 10 düzey-2/3 kanıtı + 4 kök kanıt |

*K7 Alt Katman Şeması + Kanıt Kataloğu v1.1.0 — 2026-09-24 · kaynak: 3 turlu agent tartışması*
