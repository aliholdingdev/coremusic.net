---
node_id: K009-security/security-master
node_type: domain
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/index
  - K010-identitiy
  - K011-application
  - K014-network
evidence:
  - "coremusic-katmanli-mimari-agaci-v2.md §2.9 satır 310–329 (master adı: security-master.md)"
  - ".ai/architecture/index.md §5 satır 9 (master dosya adı = security-master.md)"
  - ".ai/.decisions/accepted/ADR-010-csrf-protection-strategy.md"
  - "auth.coremusic.net/AGENTS.md §3 (ADR-010/011/013 sorumlulukları) — ⚠️ ADR-011/013 metni birebir okunmadı"
validation:
  - "2026-10-12 — master adı v2 §2.9 ve v1 registry ile karşılaştırıldı: security-master.md (İngilizce ad korundu, ADR-097 §5 notu)"
  - "2026-10-12 — OWASP eşlemesi security-web-arastirma.md §2'deki HTTP 200 URL kayıtlarıyla desteklendi"
open_questions:
  - "ADR-011 (session) ve ADR-013 (rate limit) birebir metni okunmadı → ⚠️ VERIFICATION REQUIRED"
  - "Fail-open davranışı tespit edilirse veto: auth.coremusic.net/AGENTS.md ve .ai/AGENTS.md §18 fail-safe kuralı"
group: mimari
---

# security-master.md — K009 ANAYASASI

> **Not:** v2 §2.9 bu dosyayı **security-master.md** adıyla tanımıştır (satır 317). ADR-097 §5.5 notunda
> "-master" adlarının Türkçe "-anayasa" ile değiştirilmesi ÖNERİLMİŞ, bu ADR'de DEĞİŞTİRİLMİŞTİR (KURAL 1).
> Bu üretim v2 adını **korur**; değiştirme onayı ayrı ADR'ye bağlıdır.

## §1 Yetki ve amaç

| Alan | Değer |
|---|---|
| Dosya rolü | K009-security domain anayasası — kapsam, ilkeler, sınır, yasak |
| Otorite | Bayram Ali / Vault Steward |
| SSOT konumu | K009 klasörü içindeki tüm MD'ler bu dosyaya tabidir; çelişkide bu dosya + .ai/CLAUDE.md kazanır |
| Kapsanan kademe | 2 (domain) + 3 (module/bileşen planı) |
| Üretim tarihi | 2026-10-12 · Sürüm: K009-v1 (çoklu-MD şema, ADR-097 §5.5) |

## §2 K009 ÇEKİRDEK İLKELER (12 madde)

| # | İlke | Sonuç |
|---|---|---|
| 1 | Fail-safe (fail-open YASAK) | Auth/CSRF/rate-limit bileşeni hata verirse istek REDDEDİLİR |
| 2 | Parametreli sorgu zorunlu | PDO prepared statement; string birleştirme ile SQL YASAK |
| 3 | ORM YASAK | .ai/AGENTS.md §18 — bağımlılık/artırmak ve SQL injection riski |
| 4 | Argon2id tek şifre hash algoritması | PASSWORD_ARGON2ID; MD5/SHA-1/DES ile hash YASAK |
| 5 | Timing-safe karşılaştırma | hash_equals() — doğrudan == / === ile secret/token karşılaştırması YASAK |
| 6 | Rastgelelik kaynağı | random_bytes() / random_int(); mt_rand() secret üretiminde YASAK |
| 7 | Secret log'a yazılmaz | log, vault, chat, hata mesajı — hepsi [REDACTED] |
| 8 | CSRF token her state-changing istekte | csrf_token doğrulamasız POST/PUT/DELETE YASAK |
| 9 | Güvenli session bayrağı | HttpOnly · SameSite · Secure bayrakları zorunlu (kod kanıtı: setcookie) |
| 10 | Katman ihlali = anında revert | .ai/AGENTS.md §18/6 — layer violation'da sistem durur, MO müdahale eder |
| 11 | Zero-hallucination | Doğrulanmayan güvenlik iddiası yazılmaz → ⚠️ VERIFICATION REQUIRED |
| 12 | Onay kapıları | Kullanıcı onayı olmadan büyük security kod değişikliği YASAK (AGENTS.md §1) |

## §3 OWASP Top 10:2021 → K009 eşlemesi

Kaynak: https://owasp.org/www-project-top-ten/ (HTTP 200 · 2026-10-12 · doğrulandı) — detay: security-web-arastirma.md §2.

| OWASP | K009 karşılığı | Kapsayan çocuk | Eşleşme durumu |
|---|---|---|---|
| A01 Broken Access Control | authorization · policies | authorization, policies | partial — policies dalga-3 |
| A02 Cryptographic Failures | encryption | encryption | kapsamda |
| A03 Injection | input-validation + PDO kuralı | input-validation (dalga-3) | kısmi — kod kuralı master §2/2 |
| A04 Insecure Design | threat-model | threat-model (dalga-3) | tasarımı bu anayasa taşır |
| A05 Security Misconfiguration | security headers · CORS · CSP | session-security, secrets | kapsamda (SecurityHeadersMiddleware kanıtı) |
| A06 Vulnerable Components | K018-devops bağımlılık taraması | — (bağımlılık) | K009 yalnız kural koyar, tarama K018 |
| A07 Identification/Auth Failures | authentication | authentication | kapsamda |
| A08 Software/Data Integrity | signatures · CI supply chain | secrets (rotasyon/imza), K018 | kısmi |
| A09 Logging/Monitoring Failures | audit | audit (dalga-3) | log redaksiyonu kanıtlı, denetim dalga-3 |
| A10 SSRF | K013-integraion + origin kontrolü | authorization (OriginCheck) | kısmi |

## §4 Kademe yerleşimi (v2 §4.1 — 8 kademe → K009 karşılığı)

| Kademe | V2 tanımı | K009 karşılığı | Durum |
|---|---|---|---|
| 1 | Registry (K000…K5000) | K009-security kaydı (registry §5) | verified (dosya varlığı) |
| 2 | Domain | K009-security (bu dosya + index.md) | verify-required |
| 3 | Subdomain | 11 çocuk klasörü (authentication … security-testing) | verify-required |
| 4 | Bounded Context | her çocukta güvenlik odası (ör. session odası: cookie/fixation/regenerate) | verify-required |
| 5 | Module | her çocuk MD seti (kapsam/dogrulama/web/ornekler/bilesenler) | verify-required |
| 6 | Component | security-bilesenler.md listesi (middleware + sınıflar) | verify-required |
| 7 | Service | PSR-15 middleware pipeline servisi, CacheRateLimiter, JwtService | IMPLEMENTED (kod kanıtı) |
| 8 | Implementation | shared/src/Middleware/*.php, shared/src/security/*.php, auth.coremusic.net/include/** | IMPLEMENTED (dosya kanıtı) |

## §5 Çocuk rol özeti (ÜRETİM KARARI — v2'de yoktu)

v2 §2.9 çocuklara yalnız ad verir. Aşağıdaki roller bu üretimde tanımlandı; kullanıcı onayı bekliyor → **⚠️ VERIFICATION REQUIRED**.

| # | Çocuk | v2 satır | Üretilen rol (üretim kararı) |
|---|---|---|---|
| 1 | authentication | 318 | Kimlik DOĞRULAMA akışı: kimlik kanıtı kabulü, Argon2id, OAuth/PKCE doğrulama kuralları |
| 2 | authorization | 319 | Yetkilendirme KARARI: rol/izin kontrolü, erişim kararı, Origin/CORS kapıları |
| 3 | policies | 320 | Güvenlik politikası BELGELERİ ve karar kayıtları (dalga-3) |
| 4 | session-security | 321 | Oturum GÜVENLİĞİ: cookie bayrakları, fixation, ID yenileme, CSRF/CSP nonce |
| 5 | secrets | 322 | Secret YAŞAM DÖNGÜSÜ: API key hash, redaksiyon, rotasyon, env yönetimi |
| 6 | encryption | 323 | Şifreleme: AES-GCM/Argon2id parametreleri, IV/nonce üretimi, TLS politikası |
| 7 | input-validation | 324 | Girdi doğrulama: şema doğrulama, allow-list, ReDoS önleme (dalga-3) |
| 8 | rate-limiting | 325 | Sınırlandırma: sayaç depolama, 429 politikası, brute-force koruması |
| 9 | audit | 326 | Denetim izi: güvenlik olayı loglama, redaksiyon, izlenebilirlik (dalga-3) |
| 10 | threat-model | 327 | Tehdit modelleme: STRIDE, saldırı yüzeyi, risk kaydı (dalga-3) |
| 11 | security-testing | 328 | Güvenlik testleri: PHPUnit güvenlik testleri, ZAP, gate kriterleri (dalga-3) |

## §6 Güvenlik kontrol zinciri (kanıt: middleware envanteri 2026-10-12)

Sıra, kod dosyalarının alfabetik envanterinden türetilmiştir; **çalışma sırası runtime'da doğrulanmalıdır** → ⚠️ VERIFICATION REQUIRED (pipeline kaydı: shared/src/Middleware/MiddlewarePipeline.php).

| # | Bileşen | Dosya (kanıt) | Kontrol |
|---|---|---|---|
| 1 | MiddlewarePipeline | shared/src/Middleware/MiddlewarePipeline.php | PSR-15 zincir yürütücüsü |
| 2 | SecurityHeadersMiddleware | shared/src/Middleware/SecurityHeadersMiddleware.php · satır 8, 18, 28, 31 | CSP nonce + X-Frame-Options DENY + header seti |
| 3 | CorsMiddleware | shared/src/Middleware/CorsMiddleware.php · satır 15 | CORS allow-list |
| 4 | OriginCheckMiddleware | shared/src/Middleware/OriginCheckMiddleware.php · satır 15 | Origin doğrulama |
| 5 | AuthMiddleware | shared/src/Middleware/AuthMiddleware.php · satır 7 | oturum/istek kimlik doğrulama |
| 6 | BypassAuthMiddleware | shared/src/Middleware/BypassAuthMiddleware.php · satır 9 | bypass yolu — ADR-008 kapsamı, denetim gerekli |
| 7 | PermissionMiddleware | shared/src/Middleware/PermissionMiddleware.php · satır 15 | yetki kontrolü |
| 8 | CsrfMiddleware | shared/src/Middleware/CsrfMiddleware.php · satır 7, 42 | CSRF token hash_equals |
| 9 | RateLimiterMiddleware | shared/src/Middleware/RateLimiterMiddleware.php · satır 9 | rate limit |
| 10 | ValidationMiddleware | shared/src/Middleware/ValidationMiddleware.php · satır 15 | girdi doğrulama |
| 11 | SessionManagerMiddleware | shared/src/Middleware/SessionManagerMiddleware.php · satır 9 | oturum yönetimi |

## §7 YASAKLAR (K009 geneli — ihlal = veto)

| # | Yasak | Dayanak |
|---|---|---|
| 1 | fail-open (hata durumunda istek serbest bırakma) | .ai/AGENTS.md §16/§18, master §2/1 |
| 2 | MD5 / SHA-1 / DES kullanımı | .ai/AGENTS.md §5 güvenlik standardı |
| 3 | ORM kullanımı | .ai/AGENTS.md §18/7 |
| 4 | SELECT * , string SQL birleştirme | .ai/AGENTS.md §5 |
| 5 | Secret'ın .md / .json / .log'a yazılması | .ai/AGENTS.md §5; auth.coremusic.net/AGENTS.md §4.1 |
| 6 | Session/cookie değerinin log'a yazılması | auth.coremusic.net/AGENTS.md §4 yasaklar |
| 7 | Password ValueObject dışında hash üretimi | auth.coremusic.net/AGENTS.md §4.1 |
| 8 | Subagent'ın git commit atması | .ai/AGENTS.md §13.9/5 (bu üretim için de geçerli) |
| 9 | K010-identitiy kapsamına (user entity, hesap, credential veri modeli) girmek | v2 §2.10 sınırı |
| 10 | K010/K019/K020 çakışan K020 ID'leri gibi ID CONFLICT'i onaysız çözmek | v2 §3 KURAL 2 |

## §8 Sınır komşuları

| Komşu | K009'un verdiği | K009'un almadığı | Risk |
|---|---|---|---|
| K010-identitiy | oturum güvenliği, CSRF/CSP, hash kuralları | user entity, hesap, credentials veri modeli, rollerin veri modeli | döngü → ⚠️ VERIFICATION REQUIRED |
| K011-application | security header/CSP sözleşmesi | sayfa/ekran UI kodu | header sözleşmesi ihlali → revert |
| K013-integraion | origin/CORS/token kuralları | adaptör implementasyonu | SSRF yüzeyi → kısmi (A10 eşlemesi) |
| K014-network | TLS politikası gereksinimi | DNS/firewall/altyapı | NIST SP 800-52r2 → security-dogrulama.md §2 |
| K017-observability | redaksiyon kuralı (log secret yazmaz) | loglama altyapısı, metrik | log sızıntısı → [REDACTED] |
| K018-devops | secret envanteri kuralı | CI pipeline, tarama araçları | GitLeaks ZAP korelasyonu → K018 |

## §9 Kalite standartları (eşleme: .ai/AGENTS.md §16)

| Alan | K009 hedefi | Ölçüm kaynağı |
|---|---|---|
| Security | OWASP Top 10 · CSRF=csrf_token · Argon2id | security-dogrulama.md §1 |
| Backend | strict_types · PSR-12 · prepared statement | auth.coremusic.net/AGENTS.md §4 |
| Test | güvenlik testleri dahil coverage ≥%80 | security-test-stratejisi.md §2 |
| CI | secret-scan temiz (.github/workflows/secret-scan.yml) | K018-devops |

## §10 Açık sorular (master)

| # | Soru | Etiket |
|---|---|---|
| 1 | Middleware çalışma sırası kodda mı sabit? (Pipeline dosyası okunmadı) | ⚠️ VERIFICATION REQUIRED |
| 2 | ADR-011 / ADR-013 metinleri birebir okunmadı | ⚠️ VERIFICATION REQUIRED |
| 3 | BypassAuthMiddleware hangi ortamlarda aktif? (ADR-008 referansı) | ⚠️ VERIFICATION REQUIRED |
| 4 | K009-api ID CONFLICT (v2 §6) çözümü onay bekliyor | ⚠️ ID CONFLICT — VERIFY REQUIRED |
| 5 | Salt pepper (APP_PEPPER) rotasyon politikası tanımlı mı? | ⚠️ VERIFICATION REQUIRED |

## §11 OWASP ASVS eşlemesi (seviye-1 kontroller — seçilmiş)

Kaynak: https://owasp.org/www-project-application-security-verification-standard/ (HTTP 200 · 2026-10-12 · doğrulandı).

| ASVS bölümü | K009 karşılığı | Kanıt dosyası | Durum |
|---|---|---|---|
| V2 Authentication | authentication | auth.coremusic.net/include/Domain/ValueObject/Password.php · 38–49 | IMPLEMENTED |
| V3 Session | session-security | shared/src/Session/SessionLifecycle.php · 48, 52 | IMPLEMENTED |
| V4 Access Control | authorization | shared/src/Middleware/PermissionMiddleware.php · 15 | IMPLEMENTED |
| V5 Validation | input-validation | shared/src/Middleware/ValidationMiddleware.php · 15 | IMPLEMENTED (dalga-3 çocuk) |
| V6 Cryptography | encryption | shared/src/OAuth/OAuthManager.php · 190 | IMPLEMENTED |
| V7 Error Handling & Logging | audit | shared/tests/Unit/Log/LoggerRedactionTest.php · 132 | partial (dalga-3 çocuk) |
| V8 Data Protection | secrets | shared/src/Repository/ApiKeyRepository.php · 171, 304 | IMPLEMENTED |
| V9 Communication | encryption (TLS) | security-dogrulama.md §2 (NIST SP 800-52r2) | verify-required |
| V10 Malicious Code | threat-model | — | ⚠️ VERIFICATION REQUIRED (dalga-3) |
| V11 Business Logic | policies | — | ⚠️ VERIFICATION REQUIRED (dalga-3) |

## §12 Kripto parametre kararları (kanıt: kod + standart)

| Amaç | Algoritma | Parametre | Kod kanıtı | Standart kaynağı |
|---|---|---|---|---|
| Şifre hash | Argon2id | memory_cost 65536 · time_cost 4 · threads 2 | Password.php · 44–48 | OWASP Password Storage CS (HTTP 200 · 2026-10-12) |
| Pepper | HMAC-SHA256 | APP_PEPPER anahtarı, hash öncesi | Password.php · 43 | OWASP Password Storage CS |
| Token/JTI üretimi | random_bytes(16) → hex | 16 bayt entropi | JwtService.php · 51 | PHP manual random-bytes |
| API key üretimi | random_bytes(32) → base64url | 256-bit entropi | ApiKeyRepository.php · 304 | OWASP Secrets Management CS |
| State/nonce | random_bytes(32) → hex | OAuth state + PKCE | BaseOAuthProvider.php · 52, 68 | RFC 7636 (HTTP 200 · 2026-10-12) |
| IV/nonce (AES-GCM) | random_bytes(12) | 96-bit GCM nonce | OAuthManager.php · 190 | NIST SP 800-38D (HTTP 200 · 2026-10-12) |
| UUID | UUIDv7 (zaman sıralı) | time + clockSeq + random | UuidV7.php · 26, 31 | — (iç üretim, ⚠️ uyumluluk doğrulanmadı) |
| Karşılaştırma | hash_equals | timing-safe | CsrfMiddleware.php · 42 | PHP manual hash-equals |

## §13 Oturum/cookie bayrak matrisi (talep)

| Bayrak | Değer | Dayanak | Kod karşılığı |
|---|---|---|---|
| HttpOnly | true | XSS ile cookie hırsızlığına karşı | setcookie kullanımı → ⚠️ satır doğrulanmadı |
| Secure | true (HTTPS) | NIST SP 800-52r2 · W3C secure contexts | security-dogrulama.md §2 |
| SameSite | Lax (min) · CSRF-riskli uçlar için Strict | OWASP Session Management CS | ⚠️ VERIFICATION REQUIRED |
| Path/Domain | en dar kapsam | OWASP Session Management CS | ⚠️ VERIFICATION REQUIRED |
| Ömür | oturum sonu + idle timeout | OWASP Session Management CS | ⚠️ VERIFICATION REQUIRED (timeout değeri bilinmiyor) |
| ID yenileme | giriş sonrası session_regenerate_id | OWASP Session fixation | session-security-dogrulama.md §2 |

## §14 Header envanteri (talep + kanıt)

| Header | Değer | Kanıt |
|---|---|---|
| Content-Security-Policy | nonce tabanlı (request _csp_nonce) | SecurityHeadersMiddleware.php · 18, 31 |
| Content-Security-Policy (fallback) | FALLBACK_CSP sabiti | ResponseEmitter.php · 139 |
| X-Frame-Options | DENY | SecurityHeadersMiddleware.php · 28 · ResponseEmitter.php · 141 |
| X-Content-Type-Options | nosniff (talep) | security-dogrulama.md §2 (MDN) |
| Referrer-Policy | kısıt (talep) | security-dogrulama.md §2 (MDN) |
| Permissions-Policy | en az yetki (talep) | security-dogrulama.md §2 (MDN) |
| Strict-Transport-Security | HTTPS zorunluluğu | session-security-web-arastirma.md §2 (MDN) |
| X-Frame-Options (API) | tek kaynak = IIS customHeaders | ResponseNormalizationMiddleware.php · 56 yorumu |

## §15 Rate limit varsayılanları (tasarım — doğrulanmadı)

| Kural | Varsayılan | Dayanak | Durum |
|---|---|---|---|
| Sayaç deposu | APCu (CacheRateLimiter + ApcuAdapter) | CacheRateLimiter.php · 8 · ApcuAdapter.php · 5 | IMPLEMENTED (depo), limit DEĞERLERİ ⚠️ |
| Sınır başarısızlığı | 429 Too Many Requests | RFC 6585 · MDN 429 (HTTP 200 · 2026-10-12) | verify-required |
| RateLimit başlıkları | RFC 9687 uyumlu başlık seti (talep) | rfc-editor rfc9687 (HTTP 200 · 2026-10-12) | ⚠️ VERIFICATION REQUIRED |
| Auth uçları | brute-force koruması (OAuth BCP) | RFC 9700 (HTTP 200 · 2026-10-12) | ⚠️ VERIFICATION REQUIRED |
| Hata gövdesi | standart hata şeması, secret sızdırmaz | Error Handling CS | verify-required |

## §16 Secret yaşam döngüsü kuralları

| Aşama | Kural | Dayanak |
|---|---|---|
| Üretim | random_bytes + base64url; tahmin edilebilir ID YASAK | ApiKeyRepository.php · 304 |
| Saklama | ham secret saklanmaz; hash (sha256) + prefix index | ApiKeyRepository.php · 22, 171 |
| Dağıtım | yalnız .env / CI secret store; .md/.json/.log YASAK | .ai/AGENTS.md §5 · auth.coremusic.net/AGENTS.md §4 |
| Kullanım | header ile ilet (query string YASAK) | RFC 6750 (HTTP 200 · 2026-10-12) |
| Redaksiyon | log redaksiyon testi zorunlu | LoggerRedactionTest.php · 132 |
| İptal/revoke | token iptal uç noktası | RFC 7009 (HTTP 200 · 2026-10-12) |
| Rotasyon | politika TANIMLANMADI → ⚠️ VERIFICATION REQUIRED | secrets-kapsam.md §4 |

## §17 Denetim ve redaksiyon kuralları

| # | Kural | Durum |
|---|---|---|
| 1 | Güvenlik olayları (auth fail, CSRF fail, rate limit) loglanır — içerik değil, meta | partial (audit çocuğu dalga-3) |
| 2 | Log redaksiyonu [REDACTED] deseni — testi mevcut | IMPLEMENTED (LoggerRedactionTest.php · 132) |
| 3 | Session/cookie değeri log'a yazılmaz | auth.coremusic.net/AGENTS.md §4 yasaklar |
| 4 | Sensitive data sızıntısı → .ai/AGENTS.md §17/3 | bağlayıcı |
| 5 | Log altyapısı sahipliği K017-observability | sınır kararı |

## §18 Uyum yüzeyi (bilgi notu — içerik UNKNOWN)

| Konu | K009 ilişkisi | Durum |
|---|---|---|
| KVKK / GDPR | kişisel veri işleme güvenlik maddesi | ⚠️ VERIFICATION REQUIRED — K020-comilance çocukları (K020 ID CONFLICT) |
| PCI DSS | ödeme kartı verisi YOK (CoreMusic kapsamı) | UNKNOWN — kapsam teyidi gerekli |
| Lisans (OSS) | OWASP/NIST/metinlerin CC lisansları | dependency-manager (K018) |
| SOC2 | kurumsal uyum | ⚠️ VERIFICATION REQUIRED — kapsam yok |

## §19 Revizyon günlüğü

| Sürüm | Tarih | Değişiklik | Kaynak |
|---|---|---|---|
| K009-v1 | 2026-10-12 | çoklu-MD şemaya sıfırdan yazım: kök 12 MD + 6 çocuk × 6 MD | ADR-097 §5.5 · v2 §2.9 |
| K009-v0 | 2026-10-12 (öncesi) | eski düz index/master/dogrulama/bagimliliklar — içerik KORUNMADI (görev tanımı) | görev manifesti |
| — | — | imla/ID CONFLICT işaretleri devralındı (K009-api çakışması, identitiy yazımı) | v2 §3 KURAL 1 |

## §20 İlgili K009 dosyaları

| Dosya | Rol | group |
|---|---|---|
| index.md | künye + çocuk kataloğu + §GRUPLAR | mimari |
| security-kapsam.md | kapsam içi/dışı matrisi | mimari |
| security-bagimliliklar.md | bağımlılık ağacı | mimari |
| security-bilesenler.md | kademe-3 bileşen planı | mimari |
| security-kararlar.md | ADR eşlemesi | mimari |
| security-evrensel-dil.md | terim sözlüğü | mimari |
| security-kod-ornekleri.md | gerçek kod örnekleri | kod |
| security-test-stratejisi.md | test planı | kod |
| security-dogrulama.md | doğrulama matrisi | kanit |
| security-web-arastirma.md | web araştırma kaydı | kanit |
| security-riskler.md | STRIDE risk kaydı | kanit |

## §21 Referanslar

| Kaynak | Kullanım |
|---|---|
| coremusic-katmanli-mimari-agaci-v2.md §2.9 / §3 / §4 | yapı, adlandırma, kural |
| ADR-097 (draft) §5.5 | çoklu-MD + gruplama şeması |
| ADR-010 (accepted) | CSRF stratejisi |
| https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html | doğrulama kuralları (HTTP 200 · 2026-10-12) |
| https://owasp.org/www-project-top-ten/ | OWASP eşlemesi (HTTP 200 · 2026-10-12) |

## §22 KURAL DİLİ — MUST/SHALL SÖZLEŞMESİ (RFC 2119 + RFC 8174)

> K009 kuralları bağlayıcılık düzeyini tek kelimede taşır. Normalize edilmiş sözlük aşağıdadır; yeni kural yazan her dosya bu tabloyu kullanmak zorundadır (ADR-097 §5.5 grup şemasıyla birlikte).

| K009 kelimesi | RFC 2119 karşılığı | Anlamı | İhlal sonucu | Kullanım yeri |
|---|---|---|---|---|
| ZORUNLU | MUST | yapılmadan geçersizdir | VETO / durdur | yasaklar, kapılar |
| ŞART | SHALL | ön koşul; aksi halde akış durur | redd | pipeline sırası, giriş kontrolleri |
| ŞART DEĞİLDİR | SHALL NOT | açık yasak | VETO | §7 yasakları |
| ÖNERİLEN | SHOULD | ihlali gerekçelendirilmedir | rapor + not | iyi uygulama |
| ÖNERİLMEZ | SHOULD NOT | varsayılan yasak, istisna gerekçeli | rapor | risk kabulü |
| İZNİ | MAY | gerçekten seçenekli | serbest | alternatif tasarım |
| BİLİNİYOR | (kayıt) | kanıtlanmış gözlem | — | evidence satırları |
| BİLİNMEZ | (kayıt) | kanıt yok | ⚠️ VERIFICATION REQUIRED / UNKNOWN | open_questions |

| Bağlayıcılık Kuralı | Açıklama |
|---|---|
| Büyük harf tekdüze | MUST / SHALL / SHOULD / MAY yalnız büyük harf yazılır (RFC 8174 belirsizlik kuralı) |
| Kısaltma yok | "mst", "shld" gibi kısaltma kullanılmaz |
| Tek satır = tek seviye | Bir satırda hem MUST hem MAY geçmez |
| İstisna zorunlu | SHOULD ihlali gerekçe satırı olmadan yazılmaz |
| Belirsizlik | Seviye seçilemiyorsa satır ⚠️ VERIFICATION REQUIRED olarak kalır |
| Çeviri | Türkçe sözlük bağlamındadır; alıntıda İngilizce MUST korunur |
| Kapsam | Yalnız K009 dosyaları; genel vault dili bu tabloya bağlı değildir |
| Çelişki | Bu tablo ile eski bir kural satırı çelişirse bu § otoritedir |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §23 HATA SÖZLEŞMESİ — DURUM KODLARI (RFC 9457 + RFC 6585)

> Kaynak: RFC 9457 Problem Details for HTTP APIs · RFC 6585 Additional HTTP Status Codes (HTTP 200 · 2026-10-13, §17 batch'inden bağımsız tahsis). K009, uygulamanın hangi kodu neden döndürdüğü sözleşmesini bu tabloyla sınırlar.

| Kod | K009 tutumu | Zorunlu başlık / gövde | Kanıt durumu |
|---|---|---|---|
| 400 Bad Request | doğrulama hatası — ayrıştırılamayan istek | problem gövdesi | ResponseNormalization var · detay UNKNOWN |
| 401 Unauthorized | kimlik kanıtı yok/yanlış | WWW-Authenticate | §10 satırında ⚠️ işaretli |
| 403 Forbidden | kimlik var, yetki yok | gövde sızdırmaz | PermissionMiddleware kanıtı §6 |
| 404 Not Found | varlık yok — yetki hatası olarak kullanılmaz | — | K009 tutumu: 403 ile karıştırılmaz |
| 405 Method Not Allowed | izin verilmeyen HTTP metodu | Allow başlığı | ⚠️ VERIFICATION REQUIRED |
| 409 Conflict | çakışan durum | problem tipi | ⚠️ VERIFICATION REQUIRED |
| 413 Payload Too Large | gövde boyut sınırı | — | ValidationMiddleware kanıtı §6 |
| 415 Unsupported Media Type | içerik tipi reddi | — | ⚠️ VERIFICATION REQUIRED |
| 429 Too Many Requests | rate limit (RFC 6585) | Retry-After | RateLimiter fail-closed §6 |
| 500 Internal Sunucu | beklenmeyen hata — ayrıntı sızdırılmaz | log + genel mesaj | Error Handling CS (§3) kuralı |
| 503 Service Unavailable | servis/kaynak yok | — | ⚠️ VERIFICATION REQUIRED |

| Hata Kuralı | Kaynak | K009 uygulaması |
|---|---|---|
| Problem Details tipi URN kullanılacaksa genişletilmiş tip yazılır | RFC 9457 | uygulanmadı → UNKNOWN |
| problem+json içerik tipi | RFC 9457 | okunmadı → UNKNOWN |
| 429 yalnız rate-limit için kullanılır | RFC 6585 | RateLimiterMiddleware |
| 429'da Retry-After zorunlu değildir ama önerilir | RFC 6585 | varlığı UNKNOWN |
| Ayrıştırılamayan gövde 400 döner | RFC 9457/9110 (§3 havuzu) | doğrulanmadı → UNKNOWN |
| Sunucu hatası mesajı stack/parola içermez | OWASP Error Handling CS (§3) | kanıt §3'te |
| Hata gövdesi log'a düşmeden önce redaksiyon | LoggerRedactionTest | kanıt §6 |
| Ayrıştırma hatası vs yetki hatası ayrımı | K009 tutumu | 400 ≠ 403 (yukarıdaki tablo) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §24 WELL-KNOWN URI VE ZAMAN DAMGASI (RFC 5785 + RFC 3161)

| Well-known URI | Resmi amaç (RFC 5785) | K009 tutumu | Durum |
|---|---|---|---|
| /.well-known/change-password | parola değiştirme keşfi | K010-identitiy sınırı | ⚠️ VERIFICATION REQUIRED |
| /.well-known/security.txt | güvenlik iletişim kanalı | doğrudan K009'a uygun | UNKNOWN — mevcut değil mi? |
| /.well-known/openid-configuration | OIDC keşfi | K010 devri | UNKNOWN |
| /.well-known/oauth-authorization-server | OAuth sunucu keşfi | K010 devri | UNKNOWN |
| /.well-known/assetlinks.json | uygulama bağlantısı | mobil yüzey yok | K009 kapsamı DIŞI |
| /.well-known/uri-assoc | URI ilişkilendirme | kullanılmıyor | K009 kapsamı DIŞI |
| Özel uzantı (uzantılar /x-* ) | uzantı tanımı | K009'da tanım yok | UNKNOWN |

| Zaman Damgası Kalemi | Resmi kaynak (RFC 3161) | K009 karşılığı | Durum |
|---|---|---|---|
| Zaman damgası istemcisi (TSA) | RFC 3161 Time-Stamp Protocol | imzalı kayıt gereksinimi | — |
| Zaman kaynağı senkronu | RFC 3161 içinde TSA doğrulaması | session/audit zamanı | ⚠️ VERIFICATION REQUIRED |
| İmza + zaman damgası birlikteliği | RFC 3161 | release/imzalama akışı | — |
| Denetlenebilir kayıt | SP 800-92 (§17) ile birlikte | audit dalga-3 | UNKNOWN |

| Uygulama Kuralı | Açıklama |
|---|---|
| Well-known adresi uydurulmaz | Yalnız RFC 5785'te tanımlı ad kullanılır |
| Keşif uçları Auth pipeline'ı dışına konur | Middleware sırası (OriginCheck→…→Permission) değişmez |
| Zaman damgası, oturum zamanı yerine geçmez | İkisi ayrı kavram; biri diğerinin yerine yazılmaz |
| K010 sınırı | OIDC/OAuth keşif uçları kimlik sağlayıcıya aittir |
| Doğrulanmayan uç | Kodda okunmadıysa UNKNOWN kalır; "vardır" yazılmaz |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §25 DENETİM SORUMLULUK MATRİSİ (RACI — K009 KÖK DOSYALARI)

| Dosya | Yazım | Kanıt denetimi | Güvenlik vetosu | Kapanış onayı |
|---|---|---|---|---|
| security-master.md | backend-architect | security-engineer | security-engineer | Vault Steward |
| security-kapsam.md | backend-architect | security-engineer | security-engineer | Vault Steward |
| security-bagimliliklar.md | dependency-manager | security-engineer | security-engineer | Vault Steward |
| security-bilesenler.md | backend-architect | security-engineer | security-engineer | Vault Steward |
| security-kararlar.md | architect | security-engineer | security-engineer | Vault Steward |
| security-evrensel-dil.md | docs-writer | backend-architect | — | Vault Steward |
| security-kod-ornekleri.md | backend-developer | security-engineer | security-engineer | Vault Steward |
| security-test-stratejisi.md | qa-engineer | security-engineer | security-engineer | Vault Steward |
| security-dogrulama.md | security-engineer | qa-engineer | security-engineer | Vault Steward |
| security-web-arastirma.md | security-engineer | research-analyst | security-engineer | Vault Steward |
| security-riskler.md | security-engineer | architect | security-engineer | Vault Steward |
| index.md | backend-architect | security-engineer | security-engineer | Vault Steward |
| authentication/ (6 dosya) | backend-architect | security-engineer | security-engineer | Vault Steward |
| authorization/ (6 dosya) | backend-architect | security-engineer | security-engineer | Vault Steward |
| session-security/ (6 dosya) | backend-architect | security-engineer | security-engineer | Vault Steward |
| secrets/ (6 dosya) | security-engineer | qa-engineer | security-engineer | Vault Steward |
| encryption/ (6 dosya) | security-engineer | qa-engineer | security-engineer | Vault Steward |
| rate-limiting/ (3 dosya) | backend-architect | qa-engineer | security-engineer | Vault Steward |
| input-validation/ · audit/ · threat-model/ · security-testing/ · policies/ (dalga-3) | atanmadı | atanmadı | security-engineer | Vault Steward |

| RACI Kuralı | Açıklama |
|---|---|
| R = yazan | tek R; birden fazla R olmaz |
| A = nihai onay | Vault Steward tek A'dır |
| C = mutlaka görüşülen | güvenlik vetosu olan satırlarda C zorunlu |
| I = bilgilendirilen | commit kararı orkestratöre aittir |
| Veto | security-engineer RET çıkarsa dosya kapanmaz |
| Değişiklik | Rol değişikliği yalnız bu §'de güncellenir |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §26 ÖN-SÜRÜM KALİTE KAPISI (K009 DOSYASI KAPANIŞ KRİTERLERİ)

| # | Kriter | Ölçüt | İhlal |
|---|---|---|---|
| 1 | Frontmatter 10 alan | node_id · node_type · status · parent_id · children · depends_on · evidence · validation · open_questions · group | dosya reddedilir |
| 2 | `parent:` anahtarı yok | yalnız `parent_id:` | dosya reddedilir |
| 3 | Boş liste `[]` ile yazılır | children/depends_on dahil | dosya reddedilir |
| 4 | group eşlemesi | -master/-anayasa/-kapsam/-bagimliliklar/-riskler/-surucu → mimari · -ornekler → kod · -dogrulama → kanit | dosya reddedilir |
| 5 | Başlık formatı | `## §N` + Markdown tablo | dosya reddedilir |
| 6 | Authority son satır | `**Authority:**` silinmez/sağlar | dosya reddedilir |
| 7 | Append-only | mevcut bölüm silinmez | dosya reddedilir |
| 8 | Bölüm sonu ibaresi | eklenen her § sonunda kanıt oranı ibaresi | dosya reddedilir |
| 9 | URL doğrulama | HEAD 200 + resmi başlık | doğrulanmayan iddia ⚠️ |
| 10 | URL tekliği | bir URL tek bir K009 dosyasında | çakışma kaydı açılır |
| 11 | Kod kanıtı | Dosya·Satır; okunmamış satır uydurulmaz | UNKNOWN kalır |
| 12 | Dolgu yasağı | TBD / ... / Yakında / N/A yok | dosya reddedilir |
| 13 | Sınır (K010/K000) | kimlik ve OS güvenliği tanımlanmaz | dosya reddedilir |
| 14 | Yasaklı yapı | eval/exec/shell_exec/inline JS/hard-coded secret yok | VETO + rapor |
| 15 | Kod değişikliği yok | bu görev yalnız `.md` yazar | dosya reddedilir |
| 16 | Commit yok | subagent `git commit` atmaz | görev reddedilir |
| 17 | Kapsam | yalnız hedef klasör yazılır | dosya reddedilir |
| 18 | Ölçüm | sayaç adları ölçüm bloğunda harf harf dökülmez | dolgu sayılır |
| 19 | İmla | adlandırılmış hatalar düzeltilmez | değişiklik geri alınır |
| 20 | `.ai/architecture/index.md` | dokunulmaz | dosya reddedilir |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §27 REVİZYON TETİKLEYİCİLERİ VE KAPANIŞ

| Tetikleyici | Etkilenen § | Aksiyon | Sahip |
|---|---|---|---|
| OWASP Top 10 sürüm değişimi | §3 | eşleme yeniden yazılır (2021 adları kullanılmaz) | security-engineer |
| Middleware pipeline sırası değişimi | §6 | zincir tablosu yeniden ölçülür | backend-architect |
| CSRF token adı değişimi (ADR-010) | §2 · §7 | kural satırı güncellenir | security-engineer |
| Rate limit değeri değişimi (ADR-013) | §15 | varsayılanlar güncellenir | backend-architect |
| MFA gereksinimi değişimi (ADR-059) | §2 · §7 | oturum kuralları güncellenir | security-engineer |
| Yeni dosya grubu (ADR-097) | §20 | dosya haritasına satır eklenir | backend-architect |
| Yeni standart URL doğrulanması | §21 | ilgili dosyanın havuzuna tahsis edilir | security-engineer |
| K009 ↔ K009-api ID CONFLICT kapanışı | §10 | açık soru kapatılır; onaysız yeniden adlandırma yasak | Vault Steward |

| Son Durum (2026-10-13 derinleştirme) | |
|---|---|
| Eklenen bölümler | §22..§27 (append-only) |
| Yeni doğrulanmış URL | 6 (RFC 2119 · 8174 · 6585 · 9457 · 5785 · 3161 — HTTP 200, 2026-10-13) |
| Mevcut bölümler | §1..§21 silinmedi |
| Authority | son satır olarak korundu |
| git commit | ATILMADI (yasak) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §28 SINIR HATIRLATMASI VE İLGİLİ BELGELER (K009 KÖK)

| Konu | Sahibi | K009 tutumu | İlgili § |
|---|---|---|---|
| Kimlik modeli, kullanıcı, hesap, rol TANIMI | K010-identitiy | yalnız tüketici; tanımlanmaz | §7 · §8 |
| İşletim sistemi güvenliği (kernel, disk, servis) | K000-isletim-sistemi/security | yalnız sınır referansı | §7 |
| Ağ katmanı (DNS, TLS sonlandırma, firewall) | K014-network | deployment kanıtı UNKNOWN | §18 |
| Gözlemlenebilirlik (SIEM, metrik, alarm) | K017-observability | audit dalga-3'e devredilir | §17 |
| Dağıtım ve altyapı (CI/CD, konteyner) | K018-devops | php.ini/tls yapılandırması burada | §18 |
| CSRF stratejisi | ADR-010 | `csrf_token` adı bağlayıcı | §7 |
| Parola depolama | ADR-022 | Argon2id 64MB/t=4/p=2 | §12 |
| Vault şifreleme | ADR-022 | AES-256-GCM · 96-bit IV · 16-byte tag | §12 |
| CSP nonce | ADR-012 | istek başına 256-bit rastgele | §14 |
| Rate limit | ADR-013 | 60 istek / 60 sn · APCu | §15 |
| Hybrid oturum | ADR-052 | oturum + JWT birlikte | §13 |
| Merkezî auth | ADR-058 | auth.coremusic.net tek otorite | §7 |
| MFA | ADR-059 | TOTP · pragmarx/google2fa | §7 |

| İlgili Belge | Kullanım |
|---|---|
| `.ai/.decisions/index.md` | ADR listesi (frozen 001-037; yeni güvenlik ADR taslağı ≥088) |
| `AGENTS.md` (kök) | execution loop, persona hiyerarşisi |
| `security-kapsam.md` | kapsam dışı listesi |
| `security-kararlar.md` | ADR eşlemesi |
| `security-web-arastirma.md` §17 | 2026-10-13 yeni kaynak batch'i |
| `security-master.md` §26 | dosya kalite kapısı (bu dosyanın kendi kriteri) |

| Son Ek (§22-§28) | |
|---|---|
| Ekleme yeri | `**Authority:**` satırından hemen önce (append-only) |
| Silinen içerik | 0 |
| Yeni URL | 6 benzersiz (§22-§24) |
| git commit | ATILMADI |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §29 K009 DOSYA KAPI ÖLÇÜM MATRİSİ (2026-10-13)

> Bu §, K009-security kökündeki `.md` dosyalarının 2026-10-13 `Get-Content | .Count`
> ölçümünü ve 40–100 (index) / ≥500 (diğer) kapı durumunu kaydeter. Amaç: hangi
> dosyanın kapıda olduğunu tek bakışta görmek. Dolgu yok — sayılar ölçümdür.

### §29.1 Kök dosya ölçümü (12 dosya)

| Dosya | Satır | group | Kapı hedefi | Durum |
|---|---|---|---|---|
| `index.md` | 100 | mimari | 40–100 | ✅ kapalı |
| `security-master.md` | 473 | mimari | ≥500 | bu § ile kapanıyor |
| `security-web-arastirma.md` | 517 | kanit | ≥500 | ✅ kapalı |
| `security-kod-ornekleri.md` | 279 | kod | ≥500 | ⏳ açık |
| `security-dogrulama.md` | 206 | kanit | ≥500 | ⏳ açık |
| `security-bilesenler.md` | 203 | mimari | ≥500 | ⏳ açık |
| `security-kapsam.md` | 176 | mimari | ≥500 | ⏳ açık |
| `security-bagimliliklar.md` | 167 | mimari | ≥500 | ⏳ açık |
| `security-kararlar.md` | 166 | mimari | ≥500 | ⏳ açık |
| `security-riskler.md` | 143 | kanit | ≥500 | ⏳ açık |
| `security-test-stratejisi.md` | 143 | kod | ≥500 | ⏳ açık |
| `security-evrensel-dil.md` | 115 | mimari | ≥500 | ⏳ açık |

### §29.2 Kademe-3 alt dalga ölçüm notu

6 alt-modül (authentication · authorization · session-security · secrets · encryption · rate-limiting) + 5 dalga-3 iskelet (input-validation · audit · threat-model · security-testing · policies) birlikte 27 content dosyası içerir; **hiçbiri ≥500 değildir** — en yüksek `authorization/authorization-ornekler.md` = 237. Bu dosyaların ≥500'e çıkarılması ayrı derinleştirme turudur (ADR-097 K4 fazı C); bu § yalnız ölçümü kaydeder, genişletme yapmaz. Alt dalga iskelet index'leri (40–100 kapısında) ölçülmüştür:

| Alt dalga iskeleti | Dosya | Satır | Kapı (40–100) |
|---|---|---|---|
| rate-limiting | `rate-limiting-index.md` | 78 | ✅ (content, ≥500 açık) |
| input-validation | `index.md` | 84 | ✅ kapalı |
| audit | `index.md` | 84 | ✅ kapalı |
| policies | `index.md` | 85 | ✅ kapalı |
| threat-model | `index.md` | 85 | ✅ kapalı |
| security-testing | `index.md` | 87 | ✅ kapalı |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-13
