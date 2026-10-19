---
node_id: K009-security/security-bilesenler
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/security-kapsam
  - K009-security/security-master
evidence: "35+ bileşen kaydı (Dosya·Satır kanıtlı) + kademe-2/3 planı"
validation: "Her bileşen: rol, kanıt, çocuk eşlemesi, kademe"
open_questions: "Permissions-Policy başlık kodu okunmadı; AES-GCM kanıtı yok"
group: mimari
---

# K009 · Güvenlik · Bileşenler (Kök)

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-12 · Rol: K009 bileşen envanteri + çocuk/kademe eşlemesi.
> Kanıt kuralı: her bileşen Dosya·Satır ile; okunmayan bileşen ⚠️ işaretli.

## §1 BİLEŞEN HARİTASI (KADeme-2)

| # | Bileşen | Dosya | Kanıt (Dosya·Satır) | Çocuk Eşlemesi |
|---|---|---|---|---|
| B-01 | CSRF middleware | shared/src/Middleware/CsrfMiddleware.php | 7/42 | session-security |
| B-02 | Güvenlik başlıkları | shared/src/Middleware/SecurityHeadersMiddleware.php | 8/18/28/31 | encryption (header kripto/HSTS) + session-security (CSP nonce) |
| B-03 | Kimlik doğrulama middleware | shared/src/Middleware/AuthMiddleware.php | 7 | authentication |
| B-04 | Bypass (whitelist) auth | shared/src/Middleware/BypassAuthMiddleware.php | 9 | authentication (risk: bypass akışı) |
| B-05 | Rol/pozisyon yetki | shared/src/Middleware/PermissionMiddleware.php | 15 | authorization |
| B-06 | Rate limit middleware | shared/src/Middleware/RateLimiterMiddleware.php | 9 | rate-limiting |
| B-07 | Girdi doğrulama | shared/src/Middleware/ValidationMiddleware.php | 15 | (dalga-3 input-validation) |
| B-08 | CORS | shared/src/Middleware/CorsMiddleware.php | 15 | session-security (origin) |
| B-09 | Origin kontrolü | shared/src/Middleware/OriginCheckMiddleware.php | 15 | session-security (CSRF/origin) |
| B-10 | Session yöneticisi | shared/src/Middleware/SessionManagerMiddleware.php | 9 | session-security |
| B-11 | Yanıt normalizasyonu | shared/src/Middleware/ResponseNormalizationMiddleware.php | 56 | (header splitting) |
| B-12 | Parola value object | auth.coremusic.net/include/Domain/ValueObject/Password.php | 26/38-49/54-59 | authentication + encryption |
| B-13 | JWT servisi | shared/src/Security/JwtService.php | 22/51 | encryption (karar K-04) |
| B-14 | UUIDv7 üretici | shared/src/Security/UuidV7.php | 12/26/31 | session-security (ID) |
| B-15 | Rate limiter çekirdek | shared/src/Security/CacheRateLimiter.php | 8 | rate-limiting |
| B-16 | Yardımcı güvenlik fonksiyonları | shared/src/Security/SecurityHelper.php | 7 | secrets |
| B-17 | Return URL politikası | shared/src/Security/ReturnUrlPolicy.php | 5 | session-security (open-redirect) |
| B-18 | Oturum anahtarları | shared/src/Session/SessionKeys.php | 5 | session-security |
| B-19 | Oturum yaşam döngüsü | shared/src/Session/SessionLifecycle.php | 17/48/52 | session-security |
| B-20 | Oturum başlatma (Set-Cookie bayrakları) | shared/src/Session/SessionInitializer.php | 40-46 | session-security |
| B-21 | API oturum yöneticisi | shared/src/Session/ApiSessionManager.php | 137 | session-security |
| B-22 | API anahtarı deposu | shared/src/Repository/ApiKeyRepository.php | 22/171/304 | secrets |
| B-23 | OAuth yöneticisi | auth.coremusic.net/.../OAuthManager.php | 29/190 | authentication (K010 sınırı) |
| B-24 | OAuth sağlayıcı temel | auth.coremusic.net/.../BaseOAuthProvider.php | 52/68 | authentication (K010 sınırı) |
| B-25 | Tema yöneticisi (CSP) | shared/src/.../ThemeManager.php | 239/253/278 | encryption (CSP data-URL engeli) |
| B-26 | Yanıt yayını | shared/src/.../ResponseEmitter.php | 139/141 | session-security (no-store) |
| B-27 | Apcu rate-limit adapter | shared/src/.../ApcuAdapter.php | 5 | rate-limiting |
| B-28 | Memory rate-limit adapter | shared/src/.../MemoryAdapter.php | 5 | rate-limiting |

## §2 KADeme-3 PLANI (ÇOCUK DOSYA BAZLI)

| Çocuk | Kademe-3 Bileşen Odakları | Dosya (bu oturum) |
|---|---|---|
| authentication | AuthMiddleware, BypassAuthMiddleware, Password.php, OAuthManager | children/authentication/* |
| authorization | PermissionMiddleware, rol/pozisyon matrisi, bypass whitelist etkileşimi | children/authorization/* |
| session-security | SessionLifecycle, SessionInitializer, Csrf, Origin/Cors, ReturnUrlPolicy | children/session-security/* |
| encryption | JwtService, Password kripto ayarları, SecurityHeaders (HSTS), Theme CSP | children/encryption/* |
| secrets | ApiKeyRepository, SecurityHelper, SessionKeys, log redaction | children/secrets/* |
| rate-limiting | RateLimiterMiddleware, CacheRateLimiter, Apcu/Memory adapter | children/rate-limiting/* |

## §3 KADeme-3'E DEVREDİLEN (DALGA-3) BİLEŞENLER

| Bileşen | Dosya Kanıtı | Giden Çocuk |
|---|---|---|
| ValidationMiddleware | 15 | input-validation |
| Logger/redaction altyapısı | LoggerRedactionTest.php·132 | audit |
| Threat model kayıtları | ADR-097 | threat-model |
| Test altyapısı | CsrfMiddlewareTest/ApiKeyRepositoryTest | security-testing |
| Politika belgeleri | auth.coremusic.net AGENTS.md §4 | policies |

## §4 BİLEŞEN BAĞIMLILIK GRAFİ (KISALTIŞ)

| Bileşen | Üstüne Binen | Altındaki |
|---|---|---|
| AuthMiddleware | route pipeline | SessionLifecycle, JwtService |
| CsrfMiddleware | pipeline (stateful) | SessionLifecycle (csrf_token) |
| PermissionMiddleware | AuthMiddleware sonrası | rol verisi (K010) |
| RateLimiterMiddleware | pipeline (öncelikli) | CacheRateLimiter → Apcu/Memory |
| SecurityHeadersMiddleware | pipeline (son) | CSP nonce (SessionLifecycle) |
| ValidationMiddleware | controller öncesi | şema (dalga-3) |

| Graf Kuralı | Açıklama |
|---|---|
| Sıra güvenliği | Rate limit → auth → yetki → doğrulama → yanıt başlıkları |
| Bypass riski | BypassAuthMiddleware whitelist'i tüm sırayı atlayabilir (riskler.md) |
| Kanıt | Sıra iddiası kod sırası üzerinden doğrulanacak → ⚠️ VERIFICATION REQUIRED (pipeline sırası okunmadı) |

## §5 BİLEŞEN — ÇOCUK DOSYA EŞLEME MATRİSİ

| Çocuk MD | Kapsadığı Bileşen No |
|---|---|
| authentication/index.md | B-03, B-04, B-12, B-23, B-24 |
| authentication/kapsam.md | B-03, B-04, B-12 |
| authentication/dogrulama.md | B-12 (D-01/D-02), B-03 |
| authentication/web-arastirma.md | Password/Authentication CS (kök havuz hariç yeni URL) |
| authentication/ornekler.md | B-12, B-03 kod parçaları |
| authentication/bilesenler.md | B-03, B-04, B-12, B-23, B-24 kademe-3 |
| authorization/* | B-05 (+ B-04 etkileşimi) |
| session-security/* | B-01, B-02(CSP), B-08, B-09, B-10, B-17, B-18, B-19, B-20, B-21, B-26 |
| encryption/* | B-02(HSTS), B-12(kripto), B-13, B-25 |
| secrets/* | B-16, B-18, B-22 (+ redaction) |
| rate-limiting/* | B-06, B-15, B-27, B-28 |

## §6 BİLEŞEN DURUM TABLOSU

| Durum | Sayı | Bileşen No |
|---|---|---|
| Kanıtlı (Dosya·Satır okundu) | 28 | B-01..B-28 |
| ⚠️ Kısmi kanıt | 1 | pipeline sırası (§4) |
| KANIT YOK | 0 | — |
| K010 sınırında | 2 | B-23, B-24 (kimlik sağlayıcı — tüketici) |

| Son Durum | |
|---|---|
| Dosya | security-bilesenler.md · group: mimari · 28 bileşen |
| Bağ | security-kapsam → security-master |
| Sınır | kimlik modeli K010 · git commit YASAK |

## §7 MIDDLEWARE DETAY KATMANI

| Bileşen | Sorumluluk | Güvenlik Etkisi | Kanıt |
|---|---|---|---|
| CsrfMiddleware | Token üretimi + istek eşleşmesi | CSRF reddi | CsrfMiddleware.php·7/42 |
| SecurityHeadersMiddleware | HSTS/CSP/XCTO/XFO/Referrer başlıkları | Tarayıcı katmanı savunma | SecurityHeadersMiddleware.php·18/28/31 |
| AuthMiddleware | Kimlik doğrulama zorunluluğu | Yetkisiz erişim reddi | AuthMiddleware.php·7 |
| BypassAuthMiddleware | Whitelist uçların auth atlama | Yanlış whitelist → yetkisiz erişim | BypassAuthMiddleware.php·9 |
| PermissionMiddleware | Rol/pozisyon denetimi | Yetki aşımı reddi | PermissionMiddleware.php·15 |
| RateLimiterMiddleware | İstek/kota denetimi | Abuse/brute-force sınırı | RateLimiterMiddleware.php·9 |
| ValidationMiddleware | Şema girdi doğrulama | Injection yüzeyi daraltma | ValidationMiddleware.php·15 |
| CorsMiddleware | ACAO/ACE başlığı | Cross-origin sızıntı | CorsMiddleware.php·15 |
| OriginCheckMiddleware | Origin/Referer doğrulaması | CSRF/origin bypass reddi | OriginCheckMiddleware.php·15 |
| SessionManagerMiddleware | Oturum bağlamı | Session sabitleme yüzeyi | SessionManagerMiddleware.php·9 |
| ResponseNormalizationMiddleware | Başlık/yanıt normalizasyon | Splitting/injection reddi | ResponseNormalizationMiddleware.php·56 |

| Middleware Kuralı | Açıklama |
|---|---|
| Sıra iddiası | §4 sırası kod pipeline'ında okunmadı → ⚠️ VERIFICATION REQUIRED |
| Bypass alanı | B-04 whitelist'i ayrı risk analizi (riskler.md R-01) |
| CSRF dışı | Cacheable GET uçlarında CSRF beklenmez (CsrfMiddleware kuralı) |

## §8 OTURUM BİLEŞENLERİ DETAY

| Bileşen | Anahtar Alan/İşlem | Kanıt | Çocuk |
|---|---|---|---|
| SessionLifecycle | 1800 sn timeout, csrf_token, csp_nonce, destroy | SessionLifecycle.php·17/48/52 | session-security |
| SessionInitializer | Set-Cookie: secure(44)/httponly(45)/samesite=Lax(46) | SessionInitializer.php·40-46 | session-security |
| SessionKeys | Oturum anahtar adları sabiti | SessionKeys.php·5 | session-security/secrets |
| ApiSessionManager | API tarafı oturum yaşam döngüsü | ApiSessionManager.php·137 | session-security |
| ReturnUrlPolicy | Aynı-origin zorunluluğu (open-redirect) | ReturnUrlPolicy.php·5 | session-security |
| UuidV7 | Oturum/token ID üretimi (zaman + rand) | UuidV7.php·12/26/31 | session-security |
| ResponseEmitter | no-store cache başlığı (yanıt sızıntısı) | ResponseEmitter.php·139/141 | session-security |

| Oturum Savunma Matrisi | Kontrol | Durum |
|---|---|---|
| Session fixation | session kimliği ID ile değiştirilir | ✅ SessionLifecycle.php·60 (SessionBootstrapper.php·58) |
| Session timeout | idle 1800 sn | ✅ SessionLifecycle.php·17 |
| Cookie güvenliği | Secure (yalnız HTTPS koşullu) / HttpOnly / SameSite=Lax | ✅ SessionInitializer.php·44-46 |
| CSRF | double-submit token | ✅ CsrfMiddleware.php·42 |
| Open redirect | ReturnUrlPolicy | ✅ ReturnUrlPolicy.php·5 |
| Favicon/CSP | nonce üretimi | ✅ SessionLifecycle.php·52 |

## §9 KRİPTO BİLEŞENLERİ DETAY

| Bileşen | Ayar/İşlem | Kanıt | Durum |
|---|---|---|---|
| Password.php | Argon2id params + password_hash/verify | Password.php·26/38-49/54-59 | ✅ |
| JwtService | RS256 asimetrik imza; private.pem yalnız imzalama; iss/aud fail-closed | JwtService.php·22/51 | ✅ (key-rotation boşluğu encryption dosyasında) |
| UuidV7 | random tabanlı UUIDv7 | UuidV7.php·26/31 | ✅ |
| ApiKeyRepository | random_bytes üretimi + hash_equals | ApiKeyRepository.php·171/304 | ✅ |
| SecurityHeadersMiddleware | HSTS (TLS zorunluluğu) | SecurityHeadersMiddleware.php·18 | ✅ |
| ThemeManager | data:text/html CSP engeli | ThemeManager.php·239/253/278 | ✅ |
| AES-256-GCM kullanımı | — | — | ⚠️ YOK (D-27) |
| JWKS key rotation | — | — | ⚠️ YOK (D-28) |

## §10 ARA YÜZEY / SÖZLEŞME NOTLARI

| Yüzey | Tüketici | Üretici | Not |
|---|---|---|---|
| Session token | Js/SPA (header) | SessionInitializer/ApiSessionManager | Authorization header taşıma |
| API key | ApiClient | ApiKeyRepository | hash_equals reddi |
| CSRF token | Form/JS | CsrfMiddleware | double-submit |
| CSP nonce | Template | SessionLifecycle | ThemeManager ile etkileşim |
| HSTS | Tarayıcı | SecurityHeadersMiddleware | preload ⚠️ |

## §11 GENİŞLETME NOKTALARI (BİLEŞEN EKLEME KURALLARI)

| Kural | Açıklama |
|---|---|
| Yeni middleware | pipeline'a ekle; Dosya·Satır kanıtı bu dosyaya B-kaydı olarak girer |
| Yeni kripto primitive | ADR zorunlu (kararlar.md K-xx); OWASP/NIST kaynağı web-arastirma |
| K010 sınırı geçişi | Kimlik ile ilgili bileşen eklemek → K0100-identitiy onayı |
| Dalga-3 çocuk | input-validation/audit/threat-model/security-testing/policies bileşenleri bu §3 listesinden beslenir |
| Kanıtsız bileşen | §6 tablosuna KANIT YOK olarak yazılır; silinmez/gizlenmez |

| Son Durum (bileşen) | |
|---|---|
| Kayıt | 28 kanıtlı + 1 kısmi + 0 kanıtsız |
| Kademe-2 | kök envanter (§1) |
| Kademe-3 | 6 çocuk odak (§2) + 5 dalga-3 (§3) |
