---
title: "L1 Security Layer"
type: system
layer: L1
status: canonical
date: 2026-08-18
version: 5.0.0
---

# L1 Security Layer

> **Güvenlik işlemleri ve middleware katmanı.** Bu belge L1 katmanının tüm güvenlik bileşenlerini tanımlar ve uzman atamalarını içerir.

## Zorunlu Bağlantılar

- [[../l0-infrastructure/index|L0 Infrastructure]]
- [[../l2-routing/index|L2 Routing]]
- [[../l3-presentation/index|L3 Presentation]]
- [[../index|Architecture Master]]

## 4.1 Sorumluluk Matrisi (Role Attribution)

| Görev | Sorumlu Profil | Onay Gerektiren İşlem |
|-------|----------------|----------------------|
| `AuthController` implementasyonu | security-engineer | — |
| Middleware pipeline sırası | security-engineer + backend-architect | pipeline değişikliği |
| CSRF token üretimi | security-engineer | token key değişikliği |
| `login.php` CSRF entegrasyonu | backend-architect | — |
| Auth middleware middleware yapısı | backend-architect | — |
| Şifre hashleme (Argon2id) | security-engineer | parametre değişikliği |
| SQL injection koruması | security-engineer + data-engineer | — |
| XSS koruması | security-engineer + ui-designer | CSP politikası |
| Session güvenliği | security-engineer | timeout politikası |
| `SessionInitializer` yapısı | backend-architect | — |
| Rate limiting | security-engineer | limit değerleri |
| Credential vault | security-engineer | şifreleme şeması |

**Kural:** Bir güvenlik bileşeninde değişiklik yapan ajan, o bileşenin sorumlu profiline handover mesajı göndermek zorundadır ([[AGENTS.md]] §9 formatı).

## 7.1 Katmanlar

| Katman | Dosya | Kapsam | Durum |
|--------|-------|--------|-------|
| L0 | [[../l0-infrastructure/index]] | Database, cache, filesystem, IPC | IMPLEMENTED (CacheManager, DatabaseManager) |
| **L1** | **bu dosya** | **Middleware, session, auth, CSRF, CSP** | **IMPLEMENTED (4 middleware sınıfı)** |
| L2 | [[../l2-routing/index]] | PageRouter, API Gateway, subdomain routing | IMPLEMENTED (PageRouter, AuthGuard) |
| L3 | [[../l3-presentation/index]] | UI, DOM, responsive | PLANNED (kod), IMPLEMENTED (spec) |

---

## 1. Amaç

L1, CoreMusic platformunun **güvenlik katmanıdır**. Middleware pipeline, session yönetimi, CSRF, CSP, rate limiting ve auth entegrasyonu bu katmanda yürütülür. Kod karşılığı `shared/src/Middleware/` + `shared/src/Security/` altındadır.

Bu sürüm (v5.0.0) Faz 2b revizyonu ile (2026-09-08) gerçek kod karşılıklarıyla güçlendirildi.

---

## 2. Middleware Pipeline (Immutable)

Pipeline sırası **değiştirilemez** (ADR-010/011/012/013/022):

```
1. OriginCheckMiddleware()      — Köken doğrulama (whitelist CORS)
2. CorsMiddleware()             — CORS header'ları (whitelist only)
3. RateLimiterMiddleware()      — APCu: 60 req/60s
4. SecurityHeadersMiddleware()  — CSP nonce üret, strict-dynamic, HSTS, X-Frame
5. SessionManagerMiddleware()   — Session başlat, CSP nonce'u session'a kaydet
6. CsrfMiddleware()             — csrf_token doğrulama (POST/PUT/DELETE)
7. BypassAuthMiddleware()       — Test bypass (production'da devre dışı)
8. AuthMiddleware()             — Auth bilgisi inject (session'dan okur)
9. PermissionMiddleware()       — RBAC yetki kontrolü
10. ValidationMiddleware()      — Request/DTO validasyonu
→ Controller
```

CSP nonce üretimi SecurityHeaders (#4) içindedir; SessionManager (#5) bu nonce'u session'a kaydeder. Sıra değiştirilirse CSP bozulur.

---

## 3. Gerçek Kod Karşılıkları (Faz 0 Doğrulaması — 2026-09-08)

| Pipeline Konumu | Gerçek Sınıf | Dosya | Satır | Kanıt Detayı |
|-----------------|--------------|-------|-------|--------------|
| #3 RateLimiter | `RateLimiterMiddleware` | `shared/src/Middleware/RateLimiterMiddleware.php` | 93 | `rl:` prefix, 60 istek/60 sn pencere, `TRUSTED_PROXIES` sabiti |
| #3 destek | `CacheRateLimiter` | `shared/src/Security/CacheRateLimiter.php` | 41 | `IRateLimiter` implements, CacheInterface bağımlı |
| #4 SecurityHeaders | `SecurityHeadersMiddleware` | `shared/src/Middleware/SecurityHeadersMiddleware.php` | — | `_csp_nonce` üretimi + `buildCsp()` + `X-Frame-Options: DENY` |
| #6 Csrf | `CsrfMiddleware` | `shared/src/Middleware/CsrfMiddleware.php` | — | state değiştiren metotlarda token doğrulama; bypass rotası: `set-gender` |
| #8 Auth | `AuthMiddleware` | `shared/src/Middleware/AuthMiddleware.php` | — | `IMiddleware` implements; `MM_UserID/MM_UserRole` → `_auth` request özniteliği |
| Arayüz | `IMiddleware` | `shared/src/Interfaces/Middleware/` | — | PSR-15 tarzı sözleşme |
| Session (L0→L1 köprü) | `SessionInitializer` | `shared/src/Session/` | 52 | `ensureStarted(): void`, oturum adı `COREMUSIC_SESS` |
| Session servisi | `SessionManager` | `auth.coremusic.net/include/Service/SessionManager.php` | 175 | `ISessionManager` implements; `MM_*` anahtarlarını yazar |
| Cross-domain | `HomeAuthBridge` | `home.coremusic.net/include/Auth/HomeAuthBridge.php` | 185 | POST `auth.coremusic.net/validate-key`, TTL 300 sn, 2 retry |

**Bilinen kod bulguları (LSP, 2026-09-08):** `TRUSTED_PROXIES` (RateLimiterMiddleware:24), `SESSION_NAME` (SessionInitializer:26), `PAGES_PATH` (PageRouter:115) sabitleri tanımsız görünüyor — config kökenli; çözüm ADR/onay bekliyor ([[engine.md]] §8.1 #7).

---

## 4. Detay Dosyaları

| Dosya | Konu | Satır (Faz 2b) | Durum |
|-------|------|----------------|-------|
| [[middleware]] | Pipeline detay, sıra kuralları | 510 | ✅ 500+ |
| [[session]] | Session yönetimi, cookie politikası | 562 | ✅ 500+ |
| [[auth]] | Auth entegrasyonu, RBAC | 878 | ✅ 500+ |
| [[csrf]] | CSRF token, doğrulama akışı | 427 | ⏳ genişletilecek |
| [[csp]] | CSP nonce, strict-dynamic | 431 | ⏳ genişletilecek |

**Diğer güvenlik dokümanları (bu klasör dışı):**

| Dosya | Konu | Satır |
|-------|------|-------|
| [[../07-security/index]] | Genel güvenlik index | 185 |
| [[../07-security/middleware-security]] | Middleware güvenlik detay | 434 |
| [[../07-security/session-management]] | Session derin doküman | 260 |
| [[../07-security/security/csrf-protection]] | CSRF protokol detay | 144 |
| [[../08-auth/auth-flow]] | Auth akışı | 319 |

---

## 5. CSRF Standardı

| Parametre | Değer | Kaynak |
|-----------|-------|--------|
| Token key | `csrf_token` (NOT `_csrf_token`) | ADR-010, Guardrail #6 |
| Doğrulama | `hash_equals()` (timing-safe) | brain §10 |
| Metotlar | POST/PUT/DELETE/PATCH | CsrfMiddleware |
| Bypass | `set-gender` (kodda tek doğrulanmış örnek) | CsrfMiddleware |
| Multi-tab | Session-bound tek token | ADR-010 edge case |

---

## 6. CSP Standardı

| Parametre | Değer | Kaynak |
|-----------|-------|--------|
| Nonce üretimi | `base64_encode(random_bytes(32))` | brain §10 |
| Nonce konumu | `SecurityHeadersMiddleware::_csp_nonce` | Kod okuma |
| Session kaydı | #5 SessionManager nonce'u session'a yazar | Pipeline kuralı |
| Politika | strict-dynamic | ADR-012 |
| Frame koruması | `X-Frame-Options: DENY` | `buildCsp()` |
| HSTS | Başlıkta var mı — kod teyidi bekliyor | DOĞRULAMA GEREKLİ |

---

## 7. Rate Limiting Standardı

| Parametre | Değer | Kaynak |
|-----------|-------|--------|
| Limit | 60 istek / 60 saniye | ADR-013 |
| Backend | Cache (APCu→Memory) | `CacheRateLimiter` |
| Anahtar öneki | `rl:` | RateLimiterMiddleware |
| Proxy güveni | `TRUSTED_PROXIES` sabiti | RateLimiterMiddleware:24 (LSP: tanımsız) |
| Genişleme | Redis backend PLANNED | [[../l0-infrastructure/index]] §17 |

---

## 8. Session Standardı

| Parametre | Değer | Kaynak |
|-----------|-------|--------|
| Oturum adı | `COREMUSIC_SESS` | SessionInitializer |
| Idle timeout | 3600 sn | ADR-011 |
| Anahtar sözleşmesi | `MM_UserID`, `MM_UserRole` | AuthMiddleware/SessionManager |
| Request enjeksiyonu | `_auth` özniteliği | AuthMiddleware |
| Cross-domain | validate-key POST, TTL 300 sn, 2 retry | HomeAuthBridge |

---

## 9. RBAC Roller

| Rol | Kapsam |
|-----|--------|
| `regular` | Standart kullanıcı |
| `premium` | Genişletilmiş içerik |
| `studio` | Stüdyo özellikleri |
| `car` | Araç içi profil |
| `admin` | Yönetim paneli |
| `system` | Sistem düzeyi işlemler |

Roller session'da `MM_UserRole` ile taşınır; `AuthGuard::check()` 6 kontrol zincirinde tüketilir ([[../l2-routing/index]]).

---

## 10. Hard Guardrails (L1 Özel)

| # | Kural | Kaynak |
|---|-------|--------|
| 1 | Middleware sırası değişmez | ADR-010/011/012/013/022 |
| 2 | CSRF key `csrf_token` sabittir | ADR-010 |
| 3 | BypassAuth production'da devre dışı | ADR-008 |
| 4 | Secret düz metin yazılmaz — `[REDACTED]` | MEMORY §12 |
| 5 | Hassas veri `.ai/` vault'una yazılmaz | ADR-022 |
| 6 | Argon2id parametreleri (64MB/4/2) değişmez | brain §10 |
| 7 | CSP nonce üretim-teslim sırası korunur | brain §6 |

---

## 11. Edge Cases

| Durum | Çözüm | Kaynak |
|-------|-------|--------|
| Multi-Tab CSRF | Session-bound tek token | ADR-010 |
| Session Timeout | 3600 sn → otomatik yeniden auth | ADR-011 |
| Rate limit aşıldı | 429 + `rl:` penceresi resetlenene kadar blok | RateLimiterMiddleware |
| Proxy arkasında IP | `TRUSTED_PROXIES` çözümlemesi | RateLimiterMiddleware |
| Cross-domain auth key süresi doldu | validate-key TTL 300 sn → yeniden login | HomeAuthBridge |
| Bypass denemesi production'da | BypassAuthMiddleware devre dışı — 403 | ADR-008 |

---

## 12. Diagnostics (tekrarlanabilir)

```powershell
# 1. Middleware sınıf varlığı (beklenen: 4 IMPLEMENTED)
Get-ChildItem -LiteralPath "shared\src\Middleware" -Filter "*.php" | Select-Object Name

# 2. IMiddleware implements kontrolü
Select-String -LiteralPath "shared\src\Middleware\*.php" -Pattern "implements IMiddleware"

# 3. CSRF bypass rotası
Select-String -LiteralPath "shared\src\Middleware\CsrfMiddleware.php" -Pattern "set-gender|bypass"

# 4. CSP nonce üretimi
Select-String -LiteralPath "shared\src\Middleware\SecurityHeadersMiddleware.php" -Pattern "_csp_nonce|buildCsp"

# 5. Rate limit parametreleri
Select-String -LiteralPath "shared\src\Middleware\RateLimiterMiddleware.php" -Pattern "rl:|60|TRUSTED_PROXIES"

# 6. Session anahtar sözleşmesi
Select-String -LiteralPath "shared\src\Middleware\AuthMiddleware.php" -Pattern "MM_UserID|MM_UserRole|_auth"
```

---

## 13. Sık Sorulan Sorular

**S: Pipeline'daki 10 middleware'in kaçı kodda gerçek?**
C: 4 tanesi `shared/src/Middleware/` altında IMPLEMENTED: RateLimiter, SecurityHeaders, Csrf, Auth. Diğerleri (OriginCheck, Cors, SessionManager, BypassAuth, Permission, Validation) hedef pipeline tanımıdır — kod karşılıkları PLANNED.

**S: CSRF bypass rotası ekleyebilir miyim?**
C: Yalnız security-engineer onayıyla. Kodda tek doğrulanmış bypass `set-gender`'dır; her yeni bypass ADR-010 kapsamında değerlendirilir ve pipeline güvenlik incelemesinden geçer.

**S: CSP nonce neden SessionManager'a taşınıyor?**
C: #4 SecurityHeaders üretir, #5 SessionManager session'a kaydeder; bu sıra CSP bozulmadan değiştirilemez (immutable). İstek içinde nonce hem header'a hem form token'larına servis edilir.

**S: `TRUSTED_PROXIES` hatası neden görüyorum?**
C: LSP taramasında sabit tanımsız çıktı (RateLimiterMiddleware:24) — sabit büyük olasılıkla config katmanından (domain.php) gelmeli. Kod düzeltmesi ADR/onay bekliyor ([[engine.md]] §8.1 #7).

**S: Rate limit neden cache üzerinden?**
C: ADR-013 APCu tabanlı sayacı öngörür; `CacheRateLimiter` bu sayacı CacheInterface üzerinden soyutlar — böylece APCu→Memory→(gelecek) Redis geçişi rate limit mantığını değiştirmez.

**S: Session adı neden `COREMUSIC_SESS`?**
C: ADR-011 sabitidir; SessionInitializer `ensureStarted()` içinde kullanır. Değiştirilmesi tüm session verilerini geçersiz kılar — yasak.

**S: Cross-domain auth nasıl güvenli?**
C: `HomeAuthBridge` tek kullanımlık `auth_key`'i 300 sn TTL ile `validate-key`'e gönderir; 2 retry sınırı vardır. Key tekrar kullanılamaz ve başarısız deneme session oluşturmaz.

**S: Bir middleware'i sıraya yeniden koyabilir miyim?**
C: HAYIR — pipeline sırası immutable'dır. İstisna: hayati güvenlik hatası (Frozen ADR istisna kuralı: Vault Steward + İnsan onayı).

---

## 14. İzlenebilirlik Tablosu

| İddia | Kaynak | Doğrulama |
|-------|--------|-----------|
| 4 IMPLEMENTED middleware | `shared/src/Middleware/*.php` | Glob + kod okuma |
| `rl:` prefix / 60-60sn | RateLimiterMiddleware.php | Kod okuma |
| `_csp_nonce` + `buildCsp()` | SecurityHeadersMiddleware.php | Kod okuma |
| bypass `set-gender` | CsrfMiddleware.php | Kod okuma |
| `MM_*` → `_auth` | AuthMiddleware.php | Kod okuma |
| `COREMUSIC_SESS` | SessionInitializer.php | Kod okuma (52 satır) |
| validate-key TTL 300sn/2 retry | HomeAuthBridge.php | Kod okuma (185 satır) |
| 6 kontrol AuthGuard | `shared/src/PageRouter/AuthGuard.php` | Kod okuma |
| 3 tanımsız sabit | LSP taraması 2026-09-08 | engine §8.1 #7 |

---

## 15. Revizyon Geçmişi

| Sürüm | Tarih | Değişiklik |
|-------|-------|------------|
| 4.0.0 | 2026-08-18 | Katman index yapısı |
| 5.0.0 | 2026-09-08 | Faz 2b: §3 gerçek kod karşılıkları; §5-§8 standart tabloları; §12 diagnostics; §13 SSS; §14 izlenebilirlik |

---

## 16. Pipeline Konum Detayları (10 Middleware)

| # | Middleware | Görev | Kod Durumu | Kanıt/Not |
|---|------------|-------|------------|-----------|
| 1 | OriginCheck | Köken doğrulama (whitelist CORS) | PLANNED | Hedef pipeline tanımı |
| 2 | Cors | CORS header yönetimi | PLANNED | Whitelist only |
| 3 | RateLimiter | 60 req/60s | **IMPLEMENTED** | RateLimiterMiddleware + CacheRateLimiter |
| 4 | SecurityHeaders | CSP nonce, X-Frame, HSTS | **IMPLEMENTED** | `_csp_nonce` + `buildCsp()` |
| 5 | SessionManager | Session başlat, nonce sakla | KISMEN — SessionInitializer L0/L1 köprüsü mevcut | `COREMUSIC_SESS` |
| 6 | Csrf | `csrf_token` doğrulama | **IMPLEMENTED** | bypass: `set-gender` |
| 7 | BypassAuth | Test bypass (`?_bypass=1`) | PLANNED | Production'da devre dışı |
| 8 | Auth | `MM_*` → `_auth` inject | **IMPLEMENTED** | AuthMiddleware |
| 9 | Permission | RBAC kontrolü | PLANNED | Roller §9'da tanımlı |
| 10 | Validation | Request/DTO validasyonu | PLANNED | `respect/validation ^2.0` composer'da hazır |

**Oran:** 4 IMPLEMENTED · 1 KISMEN · 5 PLANNED — pipeline tamamlanma oranı %40 (kod düzeyi).

---

## 17. Şifreleme Parametreleri (Referans)

| Algoritma | Parametre | Değer | Kullanım |
|-----------|-----------|-------|----------|
| AES-256-GCM | IV | 96-bit (12 byte) | Credential şifreleme |
| AES-256-GCM | Tag | 16 byte | Bütünlük |
| AES-256-GCM | Key | 256-bit (32 byte) | — |
| Argon2id | Memory | 64MB | Password hash |
| Argon2id | Time | 4 iterasyon | — |
| Argon2id | Threads | 2 | — |
| CSRF | Doğrulama | `hash_equals()` | Timing-safe |
| CSP | Nonce | `base64_encode(random_bytes(32))` | SecurityHeaders |

Kod karşılığı: Şifreleme sınıfları `shared/src/Security/` altında üretilecek (§17 l0 — PLANNED). Parametreler ADR-022 ile sabittir; değişiklik Frozen istisna sürecine tabidir.

---

## 18. OWASP Top 10 Eşleme (2025)

| Risk | L1 Karşılığı | Durum |
|------|--------------|-------|
| A01 Broken Access Control | AuthMiddleware + RBAC (Permission PLANNED) | KISMEN |
| A02 Cryptographic Failures | Argon2id/AES standartları + vault şeması | DOKÜMAN (kod PLANNED) |
| A03 Injection | PDO prepared + `respect/validation` | IMPLEMENTED (PDO) |
| A04 Insecure Design | ADR seti + Hard Gate | PROSES |
| A05 Security Misconfiguration | SecurityHeadersMiddleware | IMPLEMENTED |
| A06 Vulnerable Components | composer require sürüm pinleme | PROSES |
| A07 Auth Failures | Session + rate limit + cross-domain TTL | KISMEN |
| A08 Integrity Failures | CSRF `hash_equals()`, nonce | IMPLEMENTED |
| A09 Logging Failures | `[REDACTED]` politikası + deep-logging dokümanı | DOKÜMAN |
| A10 SSRF | HTTP client kullanım kuralları | PLANNED (guzzle PLANNED) |

Tam denetim akışı: [[../07-security/security/owasp-compliance]] (160 satır).

---

## 19. Güvenlik Test Hedefleri

| Test Türü | Kapsam | Framework | Durum |
|-----------|--------|-----------|-------|
| CSRF doğrulama testi | Token üretim + karşılaştırma + bypass yokluğu | PHPUnit | PLANNED |
| Rate limit testi | 60/60 pencere davranışı | PHPUnit | PLANNED |
| CSP header testi | Nonce üretimi + header birleşimi | PHPUnit | PLANNED |
| Session testi | ensureStarted idempotency | PHPUnit | PLANNED |
| Penetrasyon denetimi | OWASP tam liste | Manuel + red-team skill | PROSES |

Hedefler CLAUDE.md §17 ile uyumludur (≥80% min). Test yazılmadan "%100 güvenli" iddiası yasaktır.

---

## 20. L1 Handover Senaryoları

| Senaryo | Kaynak → Hedef | Öncelik | Format |
|---------|----------------|---------|--------|
| Yeni bypass rotası talebi | backend → security | HIGH | ADR-010 gerekçesi ile |
| Middleware sıra değişikliği önerisi | herhangi → security → MO | L4 escalation | DUR + gerekçe |
| Rate limit eşiği değişikliği | devops → security | MEDIUM | Ölçüm verisi eki |
| Session timeout revizyonu | product → security | MEDIUM | ADR-011 ek karar |
| Güvenlik açığı tespiti | herhangi → security | CRITICAL | Anlık eskalasyon |
| Şifreleme parametre değişikliği | security → Vault Steward + İnsan | L4 | Frozen istisna süreci |

---

## 21. Sıralı Okuma Rehberi (L1 Görevi İçin)

```
1. Bu dosya (index)           — katman haritası + kod karşılıkları
2. [[middleware]] (510)       — pipeline detay kuralları
3. İlgili alt dosya           — csrf / csp / session / auth (görev alanına göre)
4. [[../07-security/index]]   — genel güvenlik dokümanları
5. İlgili ADR'ler             — 008/010/011/012/013/022/034
6. Gerçek kod                 — shared/src/Middleware/*.php (kanıt)
7. [[ULTRA-THINKING.md]] §12  — doğrulama protokolü uygula
```

Kural: Görev, gerçek kod okumadan kapatılamaz (§14 izlenebilirlik).

---

## 22. Ek SSS

**S: Pipeline'da neden Validation en sonda?**
C: Hedef tasarıma göre auth/permission geçmeden validasyon yapılmaz — yetkisiz istek için validasyon maliyeti ödenmez. Sıra immutable'dır.

**S: `respect/validation` composer'da var, neden Validation PLANNED?**
C: Paket bağımlılığı hazır ama ValidationMiddleware sınıfı kodda yok — bağımlılık ≠ implementasyon (§16 oran notu).

**S: OriginCheck ile Cors farkı nedir?**
C: OriginCheck istek kökenini whitelist'te sınar; Cors response header'larını yönetir. İkisi de PLANNED — pipeline'da ilk iki konumdurlar.

**S: BypassAuth production'da açık kalsın mı?**
C: ASLA — ADR-008: yalnız test ortamında `?_bypass=1`. Production ihlali L4 eskalasyon.

**S: `MM_UserID` adı nereden geliyor?**
C: Session anahtar sözleşmesi — `SessionManager` (auth) yazar, `AuthMiddleware` `_auth` request özniteliğine taşır. Değişikliği tüm tüketicileri kırar; ADR gerektirir.

**S: Güvenlik düzeltmesi acilse ADR beklemeden yapılır mı?**
C: Frozen istisna yalnız "hayati güvenlik hatası" içindir (WORKFLOW §7.3): Vault Steward + İnsan onayı ile. Diğer her şey normal onay kapısından geçer.

---

## 23. CSRF Doğrulama Akışı

```
Form render
  → hidden input: name="csrf_token" value=<session nonce>
     (nonce: SecurityHeadersMiddleware üretir, SessionManager session'a yazar)

POST/PUT/DELETE/PATCH isteği
  → CsrfMiddleware
      ├─ bypass listesi kontrolü (tek meşru örnek: set-gender)
      ├─ token yok / eksik        → 403
      └─ hash_equals(session_token, gelen_token)
            ├─ eşleşti  → pipeline devam (#7 BypassAuth → #8 Auth)
            └─ eşleşmedi → 403 + log CRITICAL
```

Kural: Token key `csrf_token` sabittir; `_csrf_token` Guardrail #6 ihlalidir. Multi-tab destekli tek token session-bound'dur (ADR-010).

---

## 24. CSP Header Örneği (biçim referansı)

`buildCsp()` çıktısının beklenen biçimi (örnek — gerçek nonce çalışma anında üretilir):

```text
Content-Security-Policy:
  default-src 'self';
  script-src 'self' 'nonce-<32-byte-base64>' 'strict-dynamic';
  style-src 'self' 'nonce-<...>';
  X-Frame-Options: DENY
```

- Nonce her istekte yenilenir (`random_bytes(32)`, base64).
- strict-dynamic: nonce ile yüklenen script alt kaynakları güvenebilir.
- HSTS satırının üretimde bulunup bulunmadığı kod teyidi bekliyor — DOĞRULAMA GEREKLİ.

---

## 25. Rate Limit Akışı

```
İstek → RateLimiterMiddleware
  → istemci IP çözümle (TRUSTED_PROXIES arkasındaysa gerçek IP)
     → anahtar: "rl:<ip>"
        → CacheRateLimiter (CacheInterface: Apcu→Memory)
           ├─ pencere içinde sayı < 60  → sayac++ → devam
           └─ sayı ≥ 60                  → 429 Too Many Requests
Pencere: 60 sn (kayan/sabit pencere detayı kod okumasında netleşecek)
```

PLANNED genişleme: Redis backend ile çoklu-worker sayaç paylaşımı ([[../l0-infrastructure/index]] §17).

---

## 26. Session Yaşam Döngüsü

```
ensureStarted()                          [SessionInitializer, 52 satır]
  → oturum adı: COREMUSIC_SESS
  → session aktif değilse başlat; `_session_created_at` yaz
       (login bug vakası: null → return true hatası — engine §8.4 S-01)
  → AuthMiddleware: MM_UserID/MM_UserRole oku → _auth request özniteliği
  → idle 3600 sn aşımı → oturum sonlandır → yeniden auth (ADR-011)
  → SessionManager (auth servisi, 175 satır): setAuthUser() MM_* yazar
```

Bilinen risk: `SessionInitializer` iki namespace'te kopya (Session/ + PageRouter/) — birleştirme ADR bekliyor.

---

## 27. Cross-Domain Auth Akışı

```
home.coremusic.net (oturum yok)
  → auth.coremusic.net login başarılı → auth_key üret
     → redirect: home.../auth/callback?auth_key=xxx
        → HomeAuthBridge (185 satır)
           → POST auth.coremusic.net/validate-key  { auth_key }
              ├─ TTL 300 sn içinde → key tek kullanımlık tüketilir
              │     → MM_* session kurulumu → /home erişimi
              └─ süresi doldu / hatalı → login'e geri (2 retry sınırı)
```

Bu akış önceki oturumdaki `ERR_TOO_MANY_REDIRECTS` vakasının çözülen hattıdır — `SessionInitializer` timestamp düzeltmesi + `@mkdir` save path + `CURLOPT_FOLLOWLOCATION` ([[engine.md]] §8.4 S-01).

---

## 28. L1 Kodlama Standartları

| Kural | Uygulama |
|-------|----------|
| `declare(strict_types=1)` + `final class` | Tüm yeni security sınıfları |
| `implements IMiddleware` | Pipeline üyeleri (PSR-15 tarzı) |
| `hash_equals()` | Token karşılaştırma (timing-safe) |
| `random_bytes()` | Nonce/token üretimi (rand() yasak) |
| Constructor injection | Cache/Config bağımlılıkları |
| Exception modu | Sessiz hata geçişi yok |
| Test | PHPUnit, güvenlik senaryoları ayrı test seti |

---

## 29. L1 İlgili ADR Tablosu

| ADR | Konu | L1 Etkisi |
|-----|------|-----------|
| ADR-008 | Bypass Auth Middleware | #7 BypassAuth kuralı |
| ADR-010 | CSRF Stratejisi | #6 Csrf, token key sabiti |
| ADR-011 | Session Management | `COREMUSIC_SESS`, 3600s idle |
| ADR-012 | CSP Nonce strict-dynamic | #4 SecurityHeaders |
| ADR-013 | Rate Limiting APCu | #3 RateLimiter |
| ADR-022 | DB Hardened Security | Argon2id/AES parametreleri |
| ADR-034 | Credential Vault Normalization | Vault şema/doküman |
| ADR-043 | Auth Subdomain Consolidation | Merkezi auth akışı |

---

## 30. Ek SSS

**S: Rate limit sayacı hangi backend'de tutuluyor?**
C: `CacheInterface` üzerinden — APCu varsa paylaşımlı, yoksa process-local Memory. Çoklu worker üretiminde APCu/Redis şart (yoksa sayaçlar bölünür).

**S: Nonce form token'ı ile aynı mı?**
C: Aynı üretim zincirinden geçer: SecurityHeaders üretir, SessionManager saklar; hem CSP header'da hem form `csrf_token`'ında kullanılır. İkisi tek nonce kaynaklıdır (sıra immutable).

**S: 403 mi 429 mu?**
C: CSRF hatası → 403 (yetki/müşterek sır yok). Rate limit aşımı → 429 (kota). Karıştırma: 401 yalnız auth eksikliği için.

**S: Yeni middleware eklenecekse hangi adımlar?**
C: (1) Sıra gerekçesi + ADR gerekli, (2) IMiddleware implements, (3) pipeline dokümanı (bu dosya §2) ve CLAUDE.md §6 güncellemesi, (4) test, (5) log. Sıra değişikliği ise immutable — yalnız Frozen istisna süreci.

**S: Neden 5 middleware PLANNED?**
C: Pipeline hedef mimari olarak tasarlandı; kod hattı parça parça inşa ediliyor (4 IMPLEMENTED + 1 KISMEN). "PLANNED" yazmak boşluk gizlemek değil, doğru ilerleme raporudur.

## 31. Referans Kod İmzaları (kod okumasından)

```php
// shared/src/Middleware/AuthMiddleware.php — IMiddleware implements
final class AuthMiddleware implements IMiddleware
{
    // session'dan MM_UserID/MM_UserRole okur
    // request'e "_auth" özniteliği olarak inject eder
}

// shared/src/Middleware/RateLimiterMiddleware.php — 93 satır
final class RateLimiterMiddleware implements IMiddleware
{
    // "rl:" prefix + pencere sayacı + TRUSTED_PROXIES çözümlemesi
}

// shared/src/Middleware/SecurityHeadersMiddleware.php
final class SecurityHeadersMiddleware implements IMiddleware
{
    // _csp_nonce üretimi (random_bytes(32) → base64)
    // buildCsp(): strict-dynamic + X-Frame-Options: DENY
}

// shared/src/Middleware/CsrfMiddleware.php
final class CsrfMiddleware implements IMiddleware
{
    // POST/PUT/DELETE/PATCH'te csrf_token doğrulama (hash_equals)
    // bypass: set-gender (tek kod kanıtlı örnek)
}

// shared/src/Security/CacheRateLimiter.php — 41 satır
final class CacheRateLimiter implements IRateLimiter
{
    // CacheInterface üzerinden pencere sayacı
}
```

Not: İmzalar davranış özetidir; kesin gövdeler dosyalarda. Kopyalama yasak ([[WORKFLOW.md]] §8.1C).

---

## 32. Security Headers Matrisi

| Header | Üretim Yeri | Değer/İlke | Durum |
|--------|-------------|------------|-------|
| Content-Security-Policy | `buildCsp()` | nonce + strict-dynamic | IMPLEMENTED |
| X-Frame-Options | `buildCsp()` | DENY | IMPLEMENTED |
| HSTS | `buildCsp()`? | max-age + includeSubDomains | DOĞRULAMA GEREKLİ |
| X-Content-Type-Options | headers set | nosniff | Kod teyidi bekliyor |
| Referrer-Policy | headers set | strict-origin-when-cross-origin | Kod teyidi bekliyor |
| Permissions-Policy | — | en kısıtlı başlangıç | PLANNED |

Kural: Yeni header eklemesi `buildCsp()`/header set katmanından yapılır; view dosyalarına dağınık `header()` çağrısı yasaktır.

---

## 33. Ortam Farkları (Dev ↔ Production)

| Konu | Dev (Windows tipik) | Production |
|------|--------------------:|------------|
| Cache backend | MemoryAdapter (APCu yoksa) | APCu zorunlu (Redis PLANNED) |
| Rate limit doğruluğu | Process-local — paylaşılmaz | Global sayaç |
| BypassAuth | `?_bypass=1` testte kullanılabilir | Devre dışı — 403 |
| Session save path | `@mkdir` ile oluşturulur (S-01 düzeltmesi) | Sunucu yapılandırması |
| HSTS | Dev'de kritik değil | Header zorunlu (teyit bekliyor) |

**İhlal uyarısı:** Dev'de "çalıştı" gözlemi, production güvenlik iddiası DEĞİLDİR — pipeline farkları §16 oran tablosuyla okunmalıdır.

---

## 34. Güvenlik Test Senaryoları (Hazır Liste)

| # | Senaryo | Beklenen | Hedef Sınıf |
|---|---------|----------|-------------|
| 1 | CSRF token yok → POST | 403 | CsrfMiddleware |
| 2 | CSRF token uydurma → POST | 403 (hash_equals false) | CsrfMiddleware |
| 3 | `set-gender` bypass rotası | 200 (tek meşru bypass) | CsrfMiddleware |
| 4 | 61. istek / 60 sn | 429 | RateLimiterMiddleware |
| 5 | Proxy arkasında gerçek IP | `TRUSTED_PROXIES` çözümlemesi | RateLimiterMiddleware |
| 6 | İlk istek → nonce üretimi | 32-byte base64 nonce | SecurityHeaders |
| 7 | İkinci istek → yeni nonce | Nonce yenilenir (eskisi geçersiz) | SecurityHeaders |
| 8 | Session 3600sn idle | Oturum kapanır → yeniden auth | SessionInitializer |
| 9 | auth_key 300sn sonra kullanım | Red (TTL aşımı) | HomeAuthBridge |
| 10 | auth_key ikinci kullanım | Red (tek kullanımlık) | HomeAuthBridge |

Liste PHPUnit senaryolarına doğrudan dönüşebilir; her satır bir test metodu iskeletidir.

---

## 35. L1 Risk Kaydı

| # | Risk | Olasılık | Etki | Önlem |
|---|------|----------|------|-------|
| 1 | Bypass listesinin sessiz büyümesi | Orta | Yüksek | Her bypass ADR-010 kaydı |
| 2 | HSTS'in header'da olmaması | Bilinmiyor | Orta | §32 teyit görevi |
| 3 | Pipeline sırasının elle değiştirilmesi | Düşük | Kritik | Immutable kural + review gate |
| 4 | 3 tanımsız sabit üretimde patlaması | Yüksek | Orta | ADR/onay çözümü bekliyor |
| 5 | MemoryAdapter ile rate limit atlanması | Orta (dev) | Orta (prod'da APCu şart) | §33 ortam matrisi |
| 6 | CSRF token'ın localStorage'a yazılması | Düşük | Yüksek | CLAUDE.md §21 yasağı |

---

## 36. Ek SSS

**S: `X-Frame-Options: DENY` yeterli mi, CSP frame-ancestors gerekir mi?**
C: Modern hedef CSP `frame-ancestors`'tır; X-Frame-Options legacy fallback'tir. Kod şu an DENY gönderiyor — frame-ancestors eklenmesi `buildCsp()` genişlemesidir (ADR-012 uyumlu).

**S: Nonce neden her istekte yenilenir?**
C: Tek kullanımlık nonce replay penceresini kapatır. Cache'lenen sayfalarda nonce cache'lenmemelidir — PageCache etkileşimi Faz 2c kapsamında incelenir.

**S: Rate limit IP bazlı — IPv6'da ne olur?**
C: Anahtar `rl:<ip>` — IPv6 adresi doğrudan anahtardır; /64 normalizasyonu PLANNED iyileştirmedir (kod teyidi bekliyor).

**S: Güvenlik log'ları nereye yazılıyor?**
C: PSR-3 sözleşmesi hazır, Monolog/deep-logging akışı 07-security dokümanında (874 satır); kod entegrasyonu Faz 2f kapsamıdır. Secret'lar `[REDACTED]` zorunludur.

**S: Şifre hash'i nerede doğrulanıyor?**
C: Argon2id standartları brain §10'da sabittir; kod karşılığı auth domain'indedir (auth.coremusic.net/include/ — Faz 2g okuması).

**S: Guard zinciri ile L1'in ilişkisi nedir?**
C: AuthGuard L2'dedir (route kararı) ama session sözleşmesini L1'den alır (`MM_*` anahtarları). Katman ayrımı: L1 kimlik doğrular, L2 route bazlı yetki kararını verir.

**S: Bu dosya revizyonlarında neden kod satırı sayıları var?**
C: Faz 2 kuralı: IMPLEMENTED iddiası kod okuma kanıtı ister; satır sayısı dosyanın gerçek ölçeğinin hızlı göstergesidir (engine §12.6 kontrol 1).

---
