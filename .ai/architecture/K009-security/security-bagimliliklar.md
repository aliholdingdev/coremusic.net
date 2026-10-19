---
node_id: K009-security/security-bagimliliklar
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/security-master
  - K000-isletim-sistemi
  - K007-data
  - K011-application
  - K013-integraion
  - K010-network
  - K016-observability
  - K17-devops
evidence:
  - "shared/src/Middleware/ — 11 middleware (envanter 2026-10-12)"
  - "shared/src/Security/ — 6 sınıf (envanter 2026-10-12)"
  - "auth.coremusic.net/include/Domain/ValueObject/Password.php · 44 (PASSWORD_ARGON2ID)"
  - ".github/workflows/secret-scan.yml (dosya varlığı)"
validation:
  - "2026-10-12 — PHP uzantı bağımlılıkları kod içeriğinden türetildi (password_*, hash_equals, random_bytes, hash_hmac)"
  - "2026-10-12 — K komşu bağımlılıkları v2 §2 ağaç yapısıyla çaprazlandı"
open_questions:
  - "APCu yokluğunda CacheRateLimiter fallback davranışı bilinmiyor → ⚠️ VERIFICATION REQUIRED"
  - "sodium uzantısı kodda KULLANILMIYOR (kod kanıtı yok) → libsodium kararı ⚠️ VERIFICATION REQUIRED"
group: mimari
---

# security-bagimliliklar.md — K009 Bağımlılık Ağacı

## §1 Dış bağımlılıklar (PHP uzantıları — kod kanıtlı)

| # | Uzantı | Kullanım | Kod kanıtı | Zorunluluk |
|---|---|---|---|---|
| 1 | core (password_*) | Argon2id hash/verify | Password.php · 44 | zorunlu |
| 2 | core (hash_equals) | timing-safe karşılaştırma | CsrfMiddleware.php · 42 · ApiKeyRepository.php · 171 · ThemeManager.php · 253, 278 | zorunlu |
| 3 | core (random_bytes) | secret/nonce/JTI üretimi | SecurityHeadersMiddleware.php · 18 · JwtService.php · 51 · ApiKeyRepository.php · 304 · OAuthManager.php · 190 | zorunlu |
| 4 | core (hash_hmac) | pepper katmanı | Password.php · 43 | zorunlu |
| 5 | core (session_*) | oturum yönetimi | SessionLifecycle.php · SessionInitializer.php (sınıf kanıtları) | zorunlu |
| 6 | apcu | rate limit sayacı | ApcuAdapter.php · 5 · CacheRateLimiter.php · 8 | üretim için zorunlu (fallback ⚠️) |
| 7 | openssl | şifreleme yardımcıları | ⚠️ kod kanıtı bu üretimde taranmadı | UNKNOWN |
| 8 | sodium | modern kripto | ⚠️ kod kanıtı YOK (libsodium CS referans) | UNKNOWN — karar gerekli |

## §2 İç mimari bağımlılıklar (K009 içi düğüm → düğüm)

| Kaynak düğüm | Hedef düğüm | İlişki |
|---|---|---|
| K009-security/index | tüm kök MD'ler | katalog |
| K009-security/security-master | security-kapsam · security-bagimliliklar · security-bilesenler · security-kararlar · security-evrensel-dil | anayasa → türev |
| K009-security/security-master | security-kod-ornekleri · security-test-stratejisi | kural → kod/test |
| K009-security/security-kararlar | security-dogrulama · security-web-arastirma | karar → kanıt |
| K009-security/security-bilesenler | 6 çocuk {çocuk}-bilesenler.md | kademe-3 planı |
| K009-security/authentication | K009-security/encryption (Argon2id parametresi) | kod bağı |
| K009-security/authentication | K009-security/rate-limiting (brute-force) | kod bağı |
| K009-security/session-security | K009-security/authentication (giriş akışı) | akış bağı |
| K009-security/session-security | K009-security/authorization (yetki oturumu) | akış bağı |
| K009-security/secrets | K009-security/encryption (hash/IV) | kod bağı |
| K009-security/secrets | K009-security/rate-limiting (API kotası) | politika bağı |
| K009-security/authorization | K009-security/policies (dalga-3) | karar bağı |

## §3 Komşu K bağımlılıkları

| K | Yön | Sözleşme | Kanıt | Durum |
|---|---|---|---|---|
| K0100-identitiy | K009 → K010 | auth akışı kimlik modelini kullanır | AuthMiddleware.php · 7 | ⚠️ sınır döngüsü |
| K0100-identitiy | K010 → K009 | K009 hash/oturum kurallarını uygular | Password.php · 38–49 | implement |
| K011-application | K009 → K011 | CSP nonce + header sözleşmesi | SecurityHeadersMiddleware.php · 18, 31 | implement |
| K013-integraion | K009 → K013 | Origin/CORS/token kuralı | CorsMiddleware.php · 15 · OriginCheckMiddleware.php · 15 | implement |
| K010-network | K014 → K009 | TLS/HSTS ön koşulu | NIST SP 800-52r2 referansı | verify-required |
| K016-observability | K009 → K017 | redaksiyon kuralı | LoggerRedactionTest.php · 132 | partial |
| K17-devops | K018 → K009 | secret scan çıktısı | .github/workflows/secret-scan.yml | partial |
| K000-isletim-sistemi | K000 → K009 | dosya/izin/örtü modeli | — | verify-required |
| K007-data | K009 → K007 | PDO prepared statement kuralı | auth.coremusic.net/AGENTS.md §4.2 | kural |
| K18-testing | K019 ↔ K009 | güvenlik testi gate'i | shared/tests/Middleware/ | partial |

## §4 Paket bağımlılıkları (composer — kanıt: composer.json)

| Paket | Rol | Kanıt | Durum |
|---|---|---|---|
| phpunit ^10.5 / ^11.0 | test çalıştırıcı | require-dev (agent registry §25.2) | IMPLEMENTED |
| php-di | DI konteyneri (auth container) | auth.coremusic.net/include/Container/AuthContainer.php | IMPLEMENTED |
| fast-route | routing | agent registry §25.2 composer.json kanıtı | IMPLEMENTED |
| guzzlehttp / HTTP istemcisi | OAuth uçlarına erişim | ⚠️ birebir composer satırı okunmadı | UNKNOWN |

## §5 Bağımlılık kuralları

| # | Kural | Dayanak |
|---|---|---|
| 1 | Yeni security bağımlılığı yalnız ADR ile eklenir | security-kararlar.md §3 |
| 2 | Kripto kütüphanesi değişikliği (ör. sodium'a geçiş) ADR + doğrulama gerektirir | NIST SP 800-175b referansı |
| 3 | apcu yoksa rate limit fail-open DEĞİL, fail-closed olmalı | master §2/1 |
| 4 | Bağımlılık sürüm değişikliği dependency-manager (K018) + security-engineer onayı | .ai/AGENTS.md §5 |
| 5 | K009 → K010 tek yönlü akış; K010 → K009 yalnız kural tüketimi | §3 |

## §6 Döngü ve kırılganlık kaydı

| # | Risk | Etki | Aksiyon |
|---|---|---|---|
| 1 | K009 ↔ K010 döngüsü (session vs identity) | belirsiz sahiplik | P0 onay |
| 2 | APCu tek nokta (rate limit) | sayaç kaybı → fail-open riski | fallback tasarımı |
| 3 | IIS customHeaders (XFO) çifte kaynak | header drift | tek kaynak kararı |
| 4 | Pepper sabiti | rotasyon imkânsızlığı | rotasyon ADR'si |
| 5 | openssl/sodium kapsamı belirsiz | kripto yığını belirsiz | kod taraması |


## §7 Middleware → yardımcı sınıf bağımlılık matrisi

| Middleware | Türediği yardım | Kanıt | Durum |
|---|---|---|---|
| CsrfMiddleware | SessionLifecycle (csrf_token) | SessionLifecycle.php · 48 | IMPLEMENTED |
| CsrfMiddleware | hash_equals (core) | CsrfMiddleware.php · 42 | IMPLEMENTED |
| SecurityHeadersMiddleware | SessionLifecycle (csp_nonce) | SessionLifecycle.php · 52 | IMPLEMENTED |
| SecurityHeadersMiddleware | random_bytes (core) | SecurityHeadersMiddleware.php · 18 | IMPLEMENTED |
| RateLimiterMiddleware | CacheRateLimiter | CacheRateLimiter.php · 8 | IMPLEMENTED |
| CacheRateLimiter | CacheInterface (ApcuAdapter / MemoryAdapter) | ApcuAdapter.php · 5 · MemoryAdapter.php · 5 | IMPLEMENTED |
| AuthMiddleware | JwtService (varsa bearer akışı) | JwtService.php · 22 | partial — bağlantı ⚠️ |
| AuthMiddleware | SessionManagerMiddleware | SessionManagerMiddleware.php · 9 | IMPLEMENTED |
| PermissionMiddleware | K0100-identitiy rol modeli | PermissionMiddleware.php · 15 | ⚠️ sınır |
| OriginCheckMiddleware | CorsMiddleware allow-list | OriginCheckMiddleware.php · 15 · CorsMiddleware.php · 15 | IMPLEMENTED |
| ValidationMiddleware | şema/kural kaynağı | ValidationMiddleware.php · 15 | IMPLEMENTED (kaynak ⚠️) |
| BypassAuthMiddleware | AuthMiddleware (bypass) | BypassAuthMiddleware.php · 9 | ADR-008 izi ⚠️ |

## §8 Dış uç nokta bağımlılıkları (OAuth — veri yok)

| Uç nokta | Amaç | Kanıt | Durum |
|---|---|---|---|
| OAuth provider uçları | üçüncü taraf kimlik doğrulama | OAuthManager.php · 29 · BaseOAuthProvider.php · 52, 68 | sınıf IMPLEMENTED; provider listesi bilinmiyor |
| API key doğrulama | servisler arası kimlik | ApiKeyRepository.php · 171 | IMPLEMENTED |
| TLS terminasyonu | kanal güvenliği | IIS (ResponseNormalizationMiddleware.php · 56 yorumu) | verify-required |

## §9 Bağımlılık ölçüm kaydı (2026-10-12)

| Ölçüm | Sonuç |
|---|---|
| Middleware dosyası sayısı | 11 |
| Security sınıfı sayısı (shared/src/Security) | 6 |
| Auth domain ValueObject (Password) kanıtı | Password.php · 26–59 okundu |
| Kod kanıtlı dış uzantı | 5 (core) + 1 (apcu) |
| UNKNOWN uzantı | 2 (openssl, sodium) |
| Komşu K bağımlılığı | 10 satır (§3) |

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-12

## §EK — BAĞIMLILIK DENETİM KAYDI

| # | Denetim | Sonuç |
|---|---|---|
| E-01 | Dış kütüphane: firebase/php-jwt (RS256) | kod kanıtı (JwtService use satırı) |
| E-02 | apcu extension (rate limit) | ApcuAdapter·5 |
| E-03 | Yerçekimsiz fallback: MemoryAdapter | MemoryAdapter·5 |
| E-04 | PHP çekirdek: password_*/hash_equals/random_bytes | Password/ApiKey kanıtları |
| E-05 | .env/pepper bağımlılığı (APP_PEPPER) | Password.php fail-closed kanıtı |
| E-06 | composer denetimi (CVE) | ⚠️ çalıştırılmadı → R-19 |

| Denetim Kuralı | |
|---|---|
| Sürüm sabitleme | composer.lock; sürüm iddiası bu dosyada yazılmaz (UNKNOWN) |
| Yeni bağımlılık | ADR + security-kararlar §1 kaydı gerekli |
| Elenen | Vendor içeriği envantere girmez (salt Harici Referans) |

| Son Durum (bağımlılık · geniş) | |
|---|---|
| Harici bağımlılık | JWTServis kütüphanesi + apcu (kanıtlı) |
| Açık | composer audit (R-19) |
