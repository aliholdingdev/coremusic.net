---
node_id: K009-security/security-kapsam
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/security-master
  - K0100-identitiy
  - K011-application
  - K013-integraion
  - K010-network
evidence:
  - "coremusic-katmanli-mimari-agaci-v2.md §2.9 satır 310–329"
  - "shared/src/Middleware/ envanteri (11 dosya, 2026-10-12)"
  - "shared/src/Security/ envanteri (6 sınıf, 2026-10-12)"
validation:
  - "2026-10-12 — kapsam içi 11 çocuk v2 mini-ağaçıyla karşılaştırıldı"
  - "2026-10-12 — kapsam dışı maddeler K010/K011/K013/K014/K017/K018 sınır tablosuyla çaprazlandı"
open_questions:
  - "SSRF (A01/A05) kapsamı K013 ile paylaşımda → sınır onayı bekliyor"
  - "A09 audit kapsamı K017 ile paylaşımda → sınır onayı bekliyor"
group: mimari
---

# security-kapsam.md — K009 Kapsam Matrisi

## §1 Kapsam içi (K009 sahibi)

| # | Alan | Kapsam tanımı | Kapsayan çocuk | Kanıt |
|---|---|---|---|---|
| 1 | Authentication | kimlik kanıtı kabulü, hash doğrulama, OAuth/PKCE | authentication | Password.php · 38–59 |
| 2 | Authorization | erişim kararı, rol/izin kontrolü, Origin/CORS | authorization | PermissionMiddleware.php · 15 |
| 3 | CSRF | token üretimi + hash_equals doğrulama | session-security | CsrfMiddleware.php · 42 |
| 4 | CSP / security header | nonce + header seti | session-security | SecurityHeadersMiddleware.php · 18–31 |
| 5 | Crypto | Argon2id, AES-GCM IV, random_bytes, hash_equals | encryption | OAuthManager.php · 190 |
| 6 | Session güvenliği | cookie bayrakları, fixation, ID yenileme | session-security | SessionLifecycle.php · 48, 52 |
| 7 | Rate limit | sayaç, 429, brute-force | rate-limiting | CacheRateLimiter.php · 8 |
| 8 | Secrets | API key hash, redaksiyon, env kuralları | secrets | ApiKeyRepository.php · 22, 171, 304 |
| 9 | Politika | güvenlik kararlarının belgelenmesi | policies (dalga-3) | security-kararlar.md §2 |
| 10 | Girdi doğrulama | allow-list, şema doğrulama, ReDoS | input-validation (dalga-3) | ValidationMiddleware.php · 15 |
| 11 | Denetim | güvenlik olayı izi + redaksiyon | audit (dalga-3) | LoggerRedactionTest.php · 132 |
| 12 | Tehdit modelleme | STRIDE, saldırı yüzeyi | threat-model (dalga-3) | security-riskler.md §1 |
| 13 | Güvenlik testi | PHPUnit güvenlik testleri, gate | security-testing (dalga-3) | security-test-stratejisi.md §2 |

## §2 Kapsam DIŞI (başka K'ye ait)

| # | Alan | Sahip | Gerekçe |
|---|---|---|---|
| 1 | User entity, hesap, kayıt akışı | K0100-identitiy | v2 §2.10 — "Kim olduğun" |
| 2 | Credentials veri modeli (tablo/şema) | K0100-identitiy | veri modeli ≠ güvenlik politikası |
| 3 | Roller/izinlerin veri modeli | K0100-identitiy | authorization yalnız KARAR kuralını koyar |
| 4 | SQL şema, migration | K007-data | .ai/AGENTS.md §5 domain boundary |
| 5 | API routing sözleşmesi | K013-integraion | K013 API kontratlarını taşır |
| 6 | DNS / firewall / altyapı | K010-network | K009 yalnız TLS gereksinimi koyar |
| 7 | Loglama altyapısı, metrik, izleme | K017-observability | redaksiyon kuralı K009'da, altyapı K017'de |
| 8 | CI/CD, secret scan aracı, deploy | K018-devops | tarama K018; kural K009 |
| 9 | DSP/firmware güvenliği | K004-dsp / K005-firmware | donanım yüzeyi |
| 10 | Lisans/uyum belgeleri | K020-* (ID CONFLICT kayıtlı) | v2 §3.1 |

## §3 Sınır matrisi (kim ne verir — kim ne alır)

| Veren → Alıcı | Sözleşme | Durum |
|---|---|---|
| K009 → K011-application | CSP nonce + header seti | IMPLEMENTED (SecurityHeadersMiddleware) |
| K009 → K013-integraion | Origin/CORS allow-list kuralı | IMPLEMENTED (CorsMiddleware, OriginCheckMiddleware) |
| K009 → K007-data | prepared statement zorunluluğu | kural (kod denetimi K007/K019) |
| K010-network → K009 | TLS sağlığı (HSTS ön koşulu) | partial — HSTS header kod kanıtı YOK |
| K0100-identitiy ↔ K009 | auth akışı sözleşmesi | ⚠️ VERIFICATION REQUIRED (döngü riski) |
| K017-observability → K009 | redaksiyon hook'u | partial (test kanıtlı, hook bağlantısı ⚠️) |
| K018-devops → K009 | secret scan raporu | partial (.github/workflows/secret-scan.yml var) |

## §4 OWASP Top 10:2021 kapsam eşlemesi

| OWASP | K009 kapsamı | Kapsam derinliği |
|---|---|---|
| A01 Broken Access Control | authorization · policies · IDOR · OriginCheck | partial (policies dalga-3) |
| A02 Cryptographic Failures | encryption · TLS · hash parametreleri | kapsamda |
| A03 Injection | input-validation · PDO kuralı | partial (input-validation dalga-3) |
| A04 Insecure Design | threat-model · güvenlik ilkeleri | partial (threat-model dalga-3) |
| A05 Security Misconfiguration | header/CORS/CSP ayarları | kapsamda |
| A06 Vulnerable Components | yalnız kural — tarama K018 | sınırda |
| A07 Identification/Auth Failures | authentication · session-security | kapsamda |
| A08 Software/Data Integrity | CI imzası, secret hash | partial (CI K018) |
| A09 Logging/Monitoring Failures | redaksiyon kuralı; izleme K017 | sınırda |
| A10 SSRF | OriginCheck + dış çağrı kuralları | partial (adaptör K013) |

Kaynak: https://owasp.org/www-project-top-ten/ (HTTP 200 · 2026-10-12).

## §5 Kabul kriterleri (K009 tam sayılması için)

| # | Kriter | Ölçüm | Durum |
|---|---|---|---|
| 1 | 11 çocuk klasörü mevcut | dizin envanteri | partial (6/11 çoklu-MD) |
| 2 | Her çocukta 6 MD | satır aralığı ölçümü | partial (6/11) |
| 3 | CSRF doğrulaması hash_equals | CsrfMiddleware.php · 42 | IMPLEMENTED |
| 4 | Şifre hash Argon2id + pepper | Password.php · 43–48 | IMPLEMENTED |
| 5 | Security header seti üretiliyor | SecurityHeadersMiddleware.php · 18–31 | IMPLEMENTED |
| 6 | Rate limit middleware mevcut | RateLimiterMiddleware.php · 9 | IMPLEMENTED (değerler ⚠️) |
| 7 | Security testleri çalışır | shared/tests/Middleware/ | partial — kapsam raporu YOK |
| 8 | Secret log'a sızmıyor | LoggerRedactionTest.php · 132 | IMPLEMENTED (test) |
| 9 | ADR-010/011/013 izlenebilir | security-kararlar.md §2 | partial (011/013 metni ⚠️) |
| 10 | K010 sınırı belgeli | bu dosya §3 | verify-required |

## §6 Öncelik sırası (kalan iş — dalga-3)

| Öncelik | İş | Kapsam etkisi |
|---|---|---|
| P0 | K010 sınır onayı (session/token paylaşımı) | döngü riski kapatılır |
| P1 | policies + input-validation çocukları | A01 + A03 kapsamı derinleşir |
| P2 | audit + threat-model | A04 + A09 kapanır |
| P3 | security-testing | gate kapanır |
| P4 | ADR-011/013 metin okuması | karar izleri kesinleşir |


## §7 Kapsam istisnaları (onay gerektiren)

| # | İstisna | Koşul | Onay |
|---|---|---|---|
| 1 | BypassAuthMiddleware kullanımı | yalnız lokal geliştirme; production'da pasif olmalı | ⚠️ VERIFICATION REQUIRED (ADR-008 izi) |
| 2 | Fallback CSP (ResponseEmitter.php · 139) | yalnız header üretilemediği durum | devre dışı bırakma kararı yok |
| 3 | X-Frame-Options tek kaynak = IIS | API için header IIS customHeaders'ta | ResponseNormalizationMiddleware.php · 56 yorumu |
| 4 | UUIDv7 özel implementasyonu | RFC 9562 uyumu doğrulanmadı | ⚠️ VERIFICATION REQUIRED |
| 5 | Pepper (APP_PEPPER) sabiti | rotasyon politikası yok | ⚠️ VERIFICATION REQUIRED |

## §8 Kapsam ölçüm kaydı (2026-10-12)

| Ölçüm | Sonuç |
|---|---|
| v2 §2.9 çocuk sayısı | 11 |
| Çoklu-MD'ye çevrilen çocuk | 6 (authentication, authorization, session-security, encryption, secrets, rate-limiting) |
| Kalan çocuk | 5 (policies, input-validation, audit, threat-model, security-testing) |
| Kök MD | 12/12 yazıldı |
| Kapsam dışı ihlal taraması | bu üretim yalnız K009-security/ altına yazdı |

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-12

## §KAPSAM-DIŞI (NEGATİF LİSTE)

| Kalem | Neden Dışarıda | Giden Yer |
|---|---|---|
| Kullanıcı/hesap modeli (tablo, alan) | Kimlik katmanı tanımı K0100-identitiy | K0100-identitiy |
| Login/MFA/şifre sıfırlama akışı | Kimlik doğrulama deneyimi K010 | K0100-identitiy |
| OAuth2/OIDC sağlayıcı rolü | Kimlik sağlayıcı mimarisi K010 | K0100-identitiy |
| Fiziksel/altyapı güvenlik (DC, kilit) | Yazılım katmanı değil | Dış (opsyonel) |
| GDPR/kanuni saklama süreleri | Hukuki politika | policies (dalga-3) |
| DSP/ses motoru güvenliği | K004/K005 kapsamı | İlgili katman |
| PCI kart verisi saklama | CoreMusic kapsam dışı (kanıtsız) | UNKNOWN — politika kararı |

## §KAPSAM SINIR MATRİSİ

| Sınır | K009 (bu katman) | Komşu Katman |
|---|---|---|
| Doğrulama (authentication) UYGULAMASI | Evet (AuthMiddleware·7) | K010: kimlik modeli/tanımı |
| Yetkilendirme (authorization) UYGULAMASI | Evet (PermissionMiddleware·15) | K010: rol tanımı |
| Oturum (session) yaşam döngüsü | Evet (SessionLifecycle·17) | K010: kullanıcı oturum kavramı |
| Kripto primitive seçimi | Evet (Password.php·38, JwtService·22) | K010: anahtar saklama sahibi değil |
| Rate limiting | Evet (RateLimiterMiddleware·9) | API katmanı: kota işi |
| Audit olay üretimi | Kısmi | Dalga-3 audit dosyası |

| Sınır Kuralı | Açıklama |
|---|---|
| Aşım | K009'dan K010'a tek yönlü bağımlılık: K009 kimlik SORGULAMAZ, yalnız token/session DOĞRULAR |
| Belirsiz | Sınır ötesi her iddia K010'da kanıt bekler (⚠️ VERIFICATION REQUIRED) |
| Çakışma | K0100-identitiy dosyaları otoritedir (Authority sırası: .ai/CLAUDE.md §2.1) |

## §KAPSAM KABUL KRİTERLERİ

| # | Kriter | Ölçüt | Doğrulama |
|---|---|---|---|
| 1 | Kapsam 11 çocukla tanımlı | v2 §2.9 satır 318-328 | index.md §GRUPLAR |
| 2 | Her çocuk için 6 dosya | ADR-097 §5.5 | çocuk dizinleri |
| 3 | Kimlik modeline giriş yok | K010 sınırı | §KAPSAM-DIŞI tablosu |
| 4 | Kod kanıtları gerçek | Dosya·Satır | kod-ornekleri.md |
| 5 | Doğrulama ≥25 içerik satırı | v2 §4.2 | security-dogrulama.md |

## §9 OWASP TOP 10:2025 GERÇEK LİSTESİ VE K009 YENİDEN EŞLEMESİ (2026-10-17 DÜZELTME)

> **KRİTİK DÜZELTME KAYDI:** Bu dosyanın §4'ü "OWASP Top 10:2021 kapsam eşlemesi" başlığını taşır ve 2021
> adlandırmasını (A02=Cryptographic Failures, A03=Injection, A10=SSRF) kullanır. 2026-10-17'de
> https://owasp.org/Top10/2025/ sayfası HTTP 200 ile erişilip resmi liste okunduğunda **2025 sıralamasının
> 2021'den farklı olduğu** doğrulanmıştır (kanıt: security-web-arastirma.md §22 satır OW-2025). §4 SİLİNMEDİ
> (append-only); aşağıdaki tablo §4'ün ÖNCEKİ (2021) eşlemesini geçersiz kılar. Çelişkide bu § otoritedir.

| OWASP 2025 | Resmi ad (sayfadan okundu) | K009 karşılığı | Kapsayan çocuk | Eşleşme |
|---|---|---|---|---|
| A01:2025 | Broken Access Control | authorization · Origin/CORS · IDOR · open redirect | authorization | kapsamda (PermissionMiddleware·15 · OriginCheck·72-76) |
| A02:2025 | Security Misconfiguration | header/CSP/CORS ayarı, fallback CSP, bypass listesi | session-security · policies | kapsamda (SecurityHeadersMiddleware·18-31) · kısmi (fallback ⚠️) |
| A03:2025 | Software Supply Chain Failures | bağımlılık bütünlüğü, secret saklama, CI imzası | secrets · bagimliliklar (K018) | kısmi — tarama K018'de; kural K009'da |
| A04:2025 | Cryptographic Failures | Argon2id · AES-256-GCM · hash_equals · random_bytes | encryption | kapsamda (Password·43-49 · OAuthManager·190-218) |
| A05:2025 | Injection | PDO prepared · girdi doğrulama · `unserialize` yasağı | input-validation | kısmi (ValidationMiddleware·15; derin çocuk dalga-3) |
| A06:2025 | Insecure Design | STRIDE, fail-safe, kötüye kullanım senaryosu | threat-model · master §2 | tasarımı bu anayasa taşır (dalga-3) |
| A07:2025 | Authentication Failures | kimlik kanıtı doğrulama, brute-force, bypass denetimi | authentication | kapsamda (AuthMiddleware·7 · fail-closed listesi) |
| A08:2025 | Software or Data Integrity Failures | JWT imzası RS256, API key hash, eklenti bütünlüğü | encryption · secrets | kısmi (JwtService·22 · ApiKey·171) |
| A09:2025 | Security Logging and Alerting Failures | redaksiyon, securityEvent, uyarı akışı | audit | kısmi (LoggerRedactionTest·132; alerting UNKNOWN) |
| A10:2025 | Mishandling of Exceptional Conditions | fail-closed/fail-open kararı, hata sızıntısı, 5xx gövdesi | master §2 · audit | kapsamda kural (fail-open yasak §7/1); uygulama kanıtı kısmi |

| §9 Düzeltme Kuralı | Açıklama |
|---|---|
| Ad disiplini | 2025 adları kullanılır: A02 ≠ Cryptographic Failures (o artık A04); A03 ≠ Injection (o artık A05) |
| SSRF uyarısı | 2025 listesinde SSRF ayrı bir A-kategorisi DEĞİLDİR; erişim kontrolü/girdi doğrulama altına girer — "A10 SSRF" ibaresi 2025'te YANLIŞTIR |
| 2021 tablosu | §4'te tarihsel kayıt olarak kalır; yeni denetimlerde kullanılmaz |
| Revizyon tetikleyicisi | security-master.md §3 için de aynı düzeltme eklendi (§30); §3 silinmedi |
| Doğrulama | URL + resmi liste satır satır security-web-arastirma.md §22'de |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §10 SALDIRI YÜZEYİ ENVANTERİ (K009 GİRİŞ NOKTALARI × KORUMA KATMANI)

| # | Giriş noktası | Saldırı türü | Koruma katmanı | Kod kanıtı | Durum |
|---|---|---|---|---|---|
| 1 | Login formu (username/password) | brute-force, credential stuffing | RateLimiter fail-closed + Argon2id + pepper | RateLimiterMiddleware·16-30 · Password·43-49 | IMPLEMENTED |
| 2 | Session cookie | fixation, hijacking, XSS hırsızlığı | regenerate_id + HttpOnly/SameSite/Secure | SessionLifecycle·60 · SessionInitializer·40-46 | IMPLEMENTED (bayraklar secure=HTTPS koşullu) |
| 3 | State-changing POST/PUT/DELETE | CSRF | synchronizer token + hash_equals + SameSite | CsrfMiddleware·7/42 | IMPLEMENTED |
| 4 | Bearer JWT header | alg:none, imza zayıflığı, replay | RS256 sabiti + iss/aud fail-closed + jti | JwtService·22/51 | IMPLEMENTED (key rotation ⚠️ D-28) |
| 5 | API key header | anahtar tahmini, log sızıntısı | 256-bit random + SHA-256 hash + prefix + hash_equals | ApiKeyRepository·171/304 | IMPLEMENTED |
| 6 | Query/body girdisi | SQL injection, XSS, ReDoS | PDO prepared + ValidationMiddleware | ValidationMiddleware·15 · ADR-002 | IMPLEMENTED (derinlik analizi dalga-3) |
| 7 | Return URL parametresi | open redirect, token sızıntısı | ALLOWED_HOSTS allowlist + kontrol karakteri ret | ReturnUrlPolicy·5-45 | IMPLEMENTED |
| 8 | Origin header | CORS bypass, CSRF ikinci koruma | allowlist + suffix eşleşmesi kapalı + fail-closed | CorsMiddleware·63-64 · OriginCheck·72-76/108-111 | IMPLEMENTED |
| 9 | Hata/istisna akışı | stack/parola sızıntısı, fail-open | fail-safe kuralı + redaksiyon + genel mesaj | master §7/1 · LoggerRedactionTest·132 | kural IMPLEMENTED · AIEngine·295 veto bulgusu açık |
| 10 | Bypass header/config | yetkisiz erişim | tamamı tanımlı değilse bypass yok (fail-closed) + log | BypassAuthMiddleware·19-37 | IMPLEMENTED (ADR-008 ⚠️ ortam denetimi) |
| 11 | Güvenlik başlığı üretimi | clickjacking, sniffing, MIME confusion | XFO DENY · XCTO nosniff · CSP nonce · COOP/COEP | SecurityHeadersMiddleware·18-31 | IMPLEMENTED |
| 12 | Cache/önbellek anahtarı | md5 ile anahtar üretimi (kripto dışı) | yalnız önbellek anahtarı; parola/imza YOK | RateLimiterMiddleware·72 · PageCacheAdapter·31 | kural içinde (§12 kod-ornekleri taraması) |

| §10 Yüzey Kuralı | Açıklama |
|---|---|
| Yeni giriş noktası | her yeni route/middleware §10 tablosuna satır ekler (append-only) |
| Koruma katmanı yoksa | yüzey "KORUMASIZ" işaretlenir ve riskler.md'ye devredilir |
| Katman derinliği | tek katman yeterli sayılmaz; auth + rate-limit + redaksiyon üçlüsü aranır |
| Bypass yüzeyi | her bypass, whitelist, fallback bir GİRİŞ NOKTASI sayılır ve açıkça kaydedilir |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §11 VERİ AKIŞI VE GÜVEN SINIRI MATRİSİ (İSTEK ÖMRÜ)

| Aşama | Taşınan veri | Güven sınırı | K009 kontrolü | Sınır aşımında |
|---|---|---|---|---|
| 1 · TLS sonlandırma | ham parola, token, cookie | Internet → sunucu | TLS 1.2+ gereksinimi (K010-network) | şifresiz trafik = redd (HSTS) |
| 2 · Middleware pipeline | ham istek (header/body/query) | Dış → uygulama | OriginCheck → CORS → Auth → CSRF → RateLimit → Validation | zincir dışı istek = redd |
| 3 · Kimlik doğrulama | parola hash'i karşılaştırma | İstemci → authentication | pepper + Argon2id + password_verify | hash yanlış = redd (fail-closed) |
| 4 · Yetkilendirme | userId, role, permissions | authentication → authorization | rol/izin denetimi, 403 sızdırmaz | yetkisiz = 403 + log |
| 5 · İş mantığı | kullanıcı verisi | uygulama → K011-application | — (K009 yalnız veri taşıma güveni) | veri sızıntısı = redaksiyon |
| 6 · Veritabanı | parametreli sorgu | uygulama → K007-data | prepared statement zorunlu (ADR-002) | string SQL = VETO |
| 7 · Yanıt | başlık + gövde | uygulama → istemci | header seti + no-store + redaksiyon | hata gövdesi stack içermez |
| 8 · Audit/log | güvenlik olayı meta | uygulama → K017-observability | [REDACTED] deseni | secret log'a = ihlal |

| §11 Sınır Kuralı | Açıklama |
|---|---|
| Tek yönlü | güven her aşamada daralır; hiçbir aşama bir öncekinin doğrulamasını varsaymaz |
| Fail-safe | bir aşama hata verirse istek REDDEDİLİR (fail-open yasak — master §7/1) |
| K010 sınırı | aşama 3-4 kimlik/rol TANIMI üretmez; yalnız tüketir (§KAPSAM-DIŞI) |
| TLS ön koşulu | aşama 1 kanıtı K010-network'tedir; K009 yalnız gereksinimi koyar (§3 sınır matrisi) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §12 KONTROL DERİNLİK MATRİSİ (KONTROL × OWASP 2025 × CWE × KOD)

| # | Kontrol | OWASP 2025 | CWE (doğrulanmış) | Kod kanıtı | Derinlik |
|---|---|---|---|---|---|
| 1 | Argon2id + pepper | A04 | CWE-522 (§13 kod-ornekleri) | Password·43-49 | tam |
| 2 | Sabit-zamanlı karşılaştırma | A04 · A07 | — (zamanlama saldırısı sınıfı) | Csrf·42 · ApiKey·171 | tam |
| 3 | Kriptografik rastgelelik | A04 | CWE-330 (2026-10-17 doğrulandı) | random_bytes 13 kullanım | tam |
| 4 | CSRF synchronizer token | A01 | CWE-352 (2026-10-17 doğrulandı) | CsrfMiddleware·7/42 | tam |
| 5 | CSP nonce + header seti | A02 | CWE-79 (§13) | SecurityHeaders·18-31 | tam |
| 6 | Cookie bayrakları | A02 · A08 | CWE-614 (envanter) | SessionInitializer·40-46 | tam (HTTPS koşullu) |
| 7 | Rate limit fail-closed | A07 · A10 | CWE-770 (envanter) | RateLimiter·16-30 | tam (limit değerleri ⚠️) |
| 8 | Parametreli SQL | A05 | CWE-89 (envanter) | ADR-002 · ApiKey·22 | tam (kural) |
| 9 | Open redirect allowlist | A01 | CWE-601 (§13) | ReturnUrlPolicy·5-45 | tam |
| 10 | Origin allowlist | A01 | — (CORS yüzeyi) | Cors·63-64 · OriginCheck·72-76 | tam |
| 11 | Log redaksiyonu | A09 | CWE-532 (envanter) | LoggerRedactionTest·132 | tam (test) · alerting UNKNOWN |
| 12 | JWT RS256 + jti | A08 | CWE-347 yüzeyi ⚠️ (kod okunmadı) | JwtService·22/51 | kısmi (rotation ⚠️) |
| 13 | SSRF outbound denetimi | A01 · A05 (2025'te A10 DEĞİL) | CWE-918 (2026-10-17 doğrulandı) | — kanıt yok | YOK → UNKNOWN |
| 14 | Bozuk algoritma yasağı | A04 | CWE-327 (2026-10-17 doğrulandı) | master §7/2 (MD5/SHA-1/DES) | kural |
| 15 | Exception güvenli işleme | A10 | — (RFC 9457 §5 güvenlik notu) | master §23 hata sözleşmesi | kısmi |

| §12 Derinlik Kuralı | Açıklama |
|---|---|
| "tam" | kod kanıtı + doğrulanmış kaynak + (test varsa) test kanıtı birlikte |
| "kısmi" | kanıtın bir halkası eksik; eksik halka açıkça adlandırılır |
| "YOK" | kontrol hiç yok — silinmez, UNKNOWN olarak devam eder (SSRF satırı 13) |
| CWE doğrulama | yalnız HEAD 200 + resmi `<title>` ile eklenir (2026-10-17 batch: 330/327/352/918) |
| Yeni kontrol | her yeni middleware bu tabloya satır alır; almayan kontrol denetimsiz sayılır |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §13 İSTİSNA PROTOKOLÜ (ONAY GEREKTİREN DURUMLARIN İŞLENME ŞEKLİ)

| # | İstisna türü | Mevcut kayıt | İşlem protokolü | Onay |
|---|---|---|---|---|
| 1 | BypassAuthMiddleware | §7 istisna 1 | yalnız lokal; production pasifliği KANITLANIR (env değişkeni okuması) | ⚠️ ADR-008 izi |
| 2 | Fallback CSP | §7 istisna 2 | fallback üretilemediğinde istek REDDEDİLİR mi? kararı yok | ⚠️ devre dışı bırakma kararı bekliyor |
| 3 | Genel uçlarda fail-open rate limit | kod-ornekleri §6 (ADR-013 §3) | bilinçli karar; auth uçları fail-closed | bilinçli kabul |
| 4 | UUIDv7 özel implementasyonu | §7 istisna 4 | RFC 9562 uyumu doğrulanana kadar ⚠️ | ⚠️ VERIFICATION REQUIRED |
| 5 | APP_PEPPER sabiti | §7 istisna 5 | rotasyon politikası yok → parola yeniden hash zinciri tartışmalı | ⚠️ VERIFICATION REQUIRED |
| 6 | localhost/127.0.0.1 allowlist | kod-ornekleri §9 | üretimde bypass yüzeyi | ⚠️ riskler.md |

| §13 Protokol | Adım |
|---|---|
| 1 | İstisna tespit edilir (kod/audit/başka K009 dosyası) |
| 2 | Bu §'ye satır olarak kaydedilir — sessiz geçiş YASAK |
| 3 | Güvenlik etkisi CWE/OWASP ile eşlenir |
| 4 | Onay mercii işaretlenir (Vault Steward / ADR) |
| 5 | İstisna kapatıldığında satır "KAPANDI" olarak güncellenir; silinmez |
| 6 | Kapatma kanıtı yoksa istisna açık kalır; "varsayım ile kapatma" YASAK |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §14 VERİ SINIFLANDIRMASI VE AZALTMA MATRİSİ (K009 KAPSAMI İÇİN)

| Veri sınıfı | Örnek | Koruma | Saklama | Log |
|---|---|---|---|---|
| S1 · Parola (ham) | login girdisi | TLS + anında hash + bellek temizliği | ASLA saklanmaz | ASLA |
| S2 · Parola hash'i + pepper | users.password | Argon2id (64MB/t4/p2) | DB | ASLA |
| S3 · Secret/anahtar | APP_PEPPER, private.pem, API key | env/dosya izni; commit yasak; .gitignore | env/key dosyası | ASLA ([REDACTED]) |
| S4 · Token/oturum | csrf_token, session_id, JWT | hash_equals; HttpOnly cookie | session deposu | ASLA (yalnız meta) |
| S5 · Kişisel veri | e-posta, tercihler | erişim kontrolü (RBAC) | DB | redaksiyon |
| S6 · Güvenlik olayı | auth fail, CSRF fail, 429 | bütünlük (append-only log) | log deposu | evet (bu sınıf log'dur) |
| S7 · Genel veri | tema, kamuya açık profil | — | DB | serbest |

| §14 Azaltma Kuralı | Açıklama |
|---|---|
| En az yetki | S1-S4 yalnız ilgili bileşene akar; başka katmana kopyalanmaz |
| Bellek | ham parola/secret işlendikten sonra değiştirilebilir değişkende tutulmaz (S1) |
| Geri dönüşüm | S3 sızarsa: rotasyon + revokasyon + log taraması (secrets §12 protokolü) |
| Sınıf sınırı | veri sınıfı yükselirse koruma da yükselir; düşüş onay ister |
| Bilinmeyen sınıf | veri sınıfı belirsizse en yüksek sınıf muamelesi görür |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §15 KAPSAM ÖLÇÜM KAYDI (2026-10-17 DERİNLEŞTİRME)

| Ölçüm | Sonuç |
|---|---|
| OWASP Top 10:2025 resmi liste | HTTP 200 · 2026-10-17 · 10/10 satır okundu (§9) |
| §4 (2021) vs §9 (2025) çelişkisi | 4 ad değişti (A02/A03/A04/A05 kaydı + A10) — §9 otorite |
| Eklenen bölüm | §9..§15 (7 bölüm, append-only) |
| Mevcut bölüm | §1..§8 + §KAPSAM-* silinmedi |
| Yeni doğrulanmış CWE | 4 (330 · 327 · 352 · 918 — HEAD 200 + başlık) |
| SSRF eşlemesi düzeltmesi | "A10 SSRF" (2021) → 2025'te A10 ≠ SSRF (§9 uyarı satırı) |
| git commit | ATILMADI (yasak) |

## §16 KRİPTO PARAMETRE KARAR MATRİSİ (HANGİ PRIMITIVE, NEDEN, NE KADAR)

> **Kaynaklar:** NIST SP 800-38D (GCM) · SP 800-132 (PBKDF) — ikisi de 200 ile doğrulandı
> (security-web-arastirma.md §22 satır 6-7, 2026-10-18 Legacy PDF yolu).

| # | Problem | Doğru primitive | K009 kanıtı | Yanlış alternatif (yasak) | Gerekçe |
|---|---|---|---|---|---|
| 1 | Parola saklama | Argon2id (m=65536, t=4, p=2) + pepper | Password·43-49 | MD5/SHA-1/SHA-256 tek başına, bcrypt'siz eski hash | CWE-327; tuzsuz/tek turlu hash kırılır |
| 2 | Veri şifreleme (at-rest) | AES-256-GCM (12B nonce, 16B tag) | encryption E-01..E-08 | AES-CBC (MAC'siz), ECB, DES/3DES | GCM = AEAD: bütünlük + gizlilik tek geçişte |
| 3 | Token imzalama | RS256 (asimetrik) | JwtService·22 | HS256 + ortak sır (imzalama anahtarı = doğrulama anahtarı) | RS256'da private key imzalar, public key yalnız doğrular |
| 4 | Anahtar türetme | HKDF (simetrik) / PBKDF2+salt (parola yüzeyi) | — (⚠️ K009'da açık kanıt yok) | `hash(pw + sabit)` | SP 800-132: tuz benzersiz, iterasyon yüksek |
| 5 | Rastgelelik | `random_bytes` / `randombytes_buf` / CSPRNG | 13 kullanım (kod taraması) | `rand()`, `mt_rand()`, `uniqid()` | CWE-330 — tahmin edilebilir dizi |
| 6 | Karşılaştırma | sabit-zamanlı (`hash_equals` ailesi) | Csrf·42 · ApiKey·171 | `===`, `==`, `strcmp` | CWE-208 zamanlama sızıntısı |
| 7 | Şifreleme modu | GCM'de nonce TEK KULLANIMLIK | encryption §12 | GCM'de sabit nonce / sayaç nonce sıfırlanması | nonce tekrarı = anahtar/ifşa (SP 800-38D §8.1 ⚠️ tam metin indirilmedi) |
| 8 | Parola taşıma | TLS + pepper'lı hash; ham parola log'da YOK | security-kapsam §14 S1 | log'a parola yazma, URL'de parola | S1 sınıfı kuralı |

| §16 Kural | Açıklama |
|---|---|
| Parametre dondurma | Argon2id parametreleri (65536/4/2) ADR ile dondurulur; düşürme = yeni hash gereksinimi |
| Yükseltme | Parametre yükseltme eski hash'leri geçersiz kılmaz — `password_verify` eski parametreyle doğrular, başarılı login'de yeniden hash'lenir |
| GCM sınırı | Aynı (anahtar, nonce) çifti ASLA iki kez kullanılmaz; nonce üretimi rastgeledir (§20.0 matrisi) |
| Yeni primitive | primitive eklenirse bu tabloya satır alır; almayan primitive denetimsizdir |

## §17 CSRF TOKEN AKIŞI (ADIM ADIM GÜVEN SİNYALİ)

> Kod kanıtı: CsrfMiddleware·7/42 · SessionInitializer·40-46 (diskten okundu, 2026-10-12).

| Adım | Aksiyon | Taraf | Güven notu |
|---|---|---|---|
| 1 | Oturum açılır; session_id regen | sunucu | fixation kırılır (SessionLifecycle·60) |
| 2 | CSRF token üretilir: `random_bytes(32)` + session'a bağlanır | sunucu | tahmin edilemez (CWE-330) |
| 3 | Token formda gizli alan / meta olarak istemciye | sunucu→istemci | token HTML çıktısında; XSS ile okunabilir → XSS savunması CSRF'in ÖN Koşulu |
| 4 | İstemci state-changing istekte token'ı header/body ile gönderir | istemci | header `X-CSRF-Token` (JS) / body (form) |
| 5 | Sunucu token'ı session'daki ile `hash_equals` ile karşılaştırır | sunucu | sabit-zamanlı; uyuşmazlık = redd (fail-closed) |
| 6 | Ek katman: SameSite=Lax/Strict + Origin doğruluğu | istemci+sunucu | üçüncü savunma hattı; tek başına yeterli değil (tarayıcı desteği değişken) |
| 7 | Token yaşam döngüsü | sunucu | oturum bitince token da biter; logout'ta session silinir |

| §17 Sınır | Açıklama |
|---|---|
| Token ≠ CAPTCHA | CSRF token oturum bağlama işidir; bot koruması ayrı katman |
| GET güvenli varsayımı | GET/HEAD/GET'te token zorunlu DEĞİL (idempotent); zorunlu tutmak cache'i bozar |
| Bypass yasağı | token doğrulaması atlayan route yok (CsrfMiddleware·12-17 bypass reddi kaydı) |
| CWE | CWE-352 (200 ile doğrulandı, web-arastirma §22 satır 2) |

## §18 CSP (CONTENT SECURITY POLICY) BAŞLIK MATRİSİ VE NONCE AKIŞI

> Kod kanıtı: SecurityHeadersMiddleware·18-31.

| Direktif | Değer (CoreMusic kuralı) | Ne engeller | OWASP 2025 |
|---|---|---|---|
| `default-src` | `'self'` | tanımsız kaynak yükleme (fail-closed fallback) | A02 · A05 |
| `script-src` | `'self'` + nonce | inline script XSS'i | A05 |
| `style-src` | `'self'` | inline style enjeksiyonu (düşük riskli) | A02 |
| `img-src` | `'self'` data: (gerekirse) | veri sızıntısı via dış istek | A01 |
| `frame-ancestors` | `'none'` | clickjacking (XFO yerini alır) | A02 |
| `base-uri` | `'self'` | `<base>` manipülasyonu ile URL kaçırma | A01 |
| `form-action` | `'self'` | form'un dış origin'e post edilmesi | A01 |
| `object-src` | `'none'` | Flash/Plugin yüzeyi (ölü ama savunma derinliği) | A02 |

| §18 Nonce Akışı | Kural |
|---|---|
| Nonce üretimi | her yanıt için `random_bytes(16)` → `<script nonce="...">` + başlıkta aynı değer |
| Nonce ≠ hash | nonce istek başına değişir; hash statiktir — nonce tercih edilir (dinamik sayfa) |
| Fallback kararı | CSP ürekenilemezse ne olur? → karar AÇIK (kapsam §13 istisna 2, ⚠️) |
| Rapor | `report-uri`/`report-to` PLANNED — alerting UNKNOWN (§12 kontrol 11) |

## §19 RATE LIMITING — TOKEN BUCKET HESABI VE FAIL-CLOSED KURALI

> Kod kanıtı: RateLimiterMiddleware·16-30 (AUTH_FAIL_CLOSED_PATHS) · ·9 (K009/API sınırı).

| Parametre | Semantik | K009 kuralı |
|---|---|---|
| Kapasite (burst) | kovadaki maksimum token | auth uçları küçük (ör. 5); genel uçlar daha büyük |
| Doldurma oranı | saniyede eklenen token | kalıcı sınırı = kapasite/dolma süresi |
| 429 yanıt | `Retry-After` header'ı ile | MDN 429/Retry-After kaynakları §20 batch'inde doğrulanacak (⚠️ rate-limiting-web-arastirma.md'de duruyor) |
| Sayım anahtarı | kimlik + IP örneği | brute-force'ta tek IP yeterli değil; hesap başına da sayılır |
| Fail-closed | deposu (APCu/Redis) erişilemezse | auth uçlarında istek REDDEDİLİR; genel uçlarda bilinçli istisna (ADR-013 §3) |

| §19 Hesap Örneği | Değer |
|---|---|
| Login: kapasite 5, dolma 1/dk | 5 başarısızdan sonra 6. istek 429; 1 dk sonra 1 token |
| Durdurma etkisi | 60 sn'de 60 brute-force denemesi → saatte 3600 yerine ~110 (kalıcı 1/dk) |
| Doğru çözüm değildir | yalnız IP limiti (NAT arkasındaki masum kullanıcılar); hesap+IP ikilisi |
| Bypass yüzeyi | limit deposu patlarsa ne olur: auth = REDDET (fail-closed listesi), genel = AÇIK (bilinçli) |

## §20 SECRET ROTATION VE SIZINTI MÜDAHALESİ

> Kod kanıtı: ApiKeyRepository·171/304 (256-bit random + SHA-256 hash + prefix).

| # | Secret | Rotasyon tetikleyicisi | Rotasyon adımı | Durum |
|---|---|---|---|---|
| 1 | APP_PEPPER | politika yok | tüm parola hash'leri geçersiz → kullanıcı parola sıfırlama zinciri gerekir | ⚠️ istisna (§13/5) |
| 2 | JWT RS256 private key | D-28 açık | public key JWKS ile çoklu yayınlama → sonra private key değişimi | ⚠️ D-28 |
| 3 | API key | kullanıcı talebi / sızıntı | yeni key üret (random 256-bit) → eskiyi grace periyodunda reddet → kaldır | IMPLEMENTED üretim var; politika kural |
| 4 | DB parola | sunucu/DB politikası | secrets manager üzerinden; uygulama restart | K018-devops |
| 5 | Session secret/cookie anahtarı | — | ⚠️ K009'da kanıt yok | UNKNOWN |

| §20 Sııntı Müdahalesi (S3 sınıfı — security-kapsam §14) | Adım |
|---|---|
| 1 | Sızıntı tespiti (gitleaks/CI · log taraması · bildirim) |
| 2 | Secret'ı derhal REDDET: env'den kaldır / key'i iptal et (fail-safe) |
| 3 | Rotasyon (yukarıdaki tablo) |
| 4 | Etki analizi: hangi log/DB erişimi vardı (erişim kayıtları) |
| 5 | Devamı: secrets-kapsam.md protokolü (§12) |

## §21 FAIL-SAFE İLKESİ VE FAIL-OPEN YASAĞI (A10 BAĞLANTILI)

| Senaryo | Fail-safe davranış | Fail-open (YASAK) |
|---|---|---|
| Rate limit deposu yok | auth: REDDET | auth: geç (saldırgan depo çökertirse bypass) |
| Origin doğrulanamıyor | reddet | geç (CORS bypass) |
| CSP üretemiyorsa | REDDET (istisna 2 ⚠️ karar bekliyor) | boş header (korumasız sayfa) |
| JWT anahtarı okunamıyor | reddet | alg:none'a düş (imzasız kabul) |
| Parola hash'i doğrulanamıyor (hash bozuksa) | reddet + log + hesap kilidi | "hash yoksa geç" (asla) |
| İstisna yakalanamıyor | genel mesaj + log (RFC 9457 problem+json) | stack trace gövdeye (A10 ihlali) |

| §21 Kural | Açıklama |
|---|---|
| Tanım | Sistem bir güvenlik kontrolünü UYGULAYAMAZSA istek REDDEDİLİR; geçici serbest bırakma YASAK |
| İstisna | yalnız §13 tablosundaki 6 kayıt; her biri onay mercii ile |
| A10 eşlemesi | OWASP 2025 A10 = Exceptional Conditions; boş catch + fail-open aynı sınıfın parçası |
| Denetim | yeni her kontrol bu tabloya "fail-safe ne?" satırı ile girer |

## §22 DERİNLEŞTİRME ÖLÇÜM KAYDI (BU TUR — 2026-10-18)

| Ölçüm | Sonuç |
|---|---|
| Eklenen bölüm | §16..§23 (8 bölüm, append-only) |
| §9-§15 (önceki tur) | korundu |
| §1-§8 + §KAPSAM-* | silinmedi |
| Yeni kod kanıtı | yok — bu tur yalnız §2-§15'teki MEVCUT kanıtları sentezledi (uydurma alıntı 0) |
| Yeni URL | 0 (URL eklemesi security-web-arastirma.md §22'de) |
| git commit | ATILMADI (yasak) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §23 GÜVENLİK BAŞLIKLARI TAM ENVANTERİ (SecurityHeadersMiddleware · 18-31 KARSILIGI)

> Kod kanıtı: `SecurityHeadersMiddleware·18-31` (diskten okundu, 2026-10-12). Bu § mevcut başlık setini
> işlevsel olarak sınıflandırır; başlık çıkarma noktası tek noktadır (ResponseEmitter).

| # | Başlık | Değer | Sınıf | Engellediği | OWASP 2025 |
|---|---|---|---|---|---|
| 1 | Content-Security-Policy | nonce'lu script-src + default-src 'self' | S4-S7 | XSS, kaynak enjeksiyonu | A05 |
| 2 | Strict-Transport-Security | max-age + includeSubDomains | S1-S4 | SSL stripping | A02 |
| 3 | X-Content-Type-Options | nosniff | S5-S7 | MIME sniffing | A02 |
| 4 | X-Frame-Options | DENY | S5-S7 | clickjacking (CSP frame-ancestors yedeği) | A02 |
| 5 | Cross-Origin-Opener-Policy | same-origin | S4-S7 | XS-Leak / Spectre sınıfı sızıntı | A02 |
| 6 | Referrer-Policy | strict-origin-when-cross-origin | S1-S4 | URL/parametre sızıntısı | A01 |
| 7 | Permissions-Policy | kamera/mikrofon kapalı | S5-S7 | istenmeyen cihaz erişimi | A02 |
| 8 | Cache-Control (hassas yanıt) | no-store | S1-S4 | proxy/önbellek sızıntısı | A04 |

| §23 Başlık Kuralı | Açıklama |
|---|---|
| Tek nokta | başlık yalnız bu middleware'den yazılır; el ile `header(` çağrısı yeni istisnadır (tetikleyici: kod-ornekleri §19) |
| Eksik başlık | yukarıdaki 8'den eksik = güvenlik düşüşü → §12 derinlik matrisi satırı güncellenir |
| COEP/CorP | Cross-Origin-Embedder-Policy/Resource-Policy yalnız cross-origin kaynak varsa anlamlı; CoreMusic kanıtı §23/5 (COOP) ile sınırlı |
| Değer değişikliği | herhangi bir değerin gevşetilmesi onay ister (Vault Steward) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-18
