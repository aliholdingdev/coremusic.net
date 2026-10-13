---
node_id: K009-security/security-kapsam
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/security-master
  - K010-identitiy
  - K011-application
  - K013-integraion
  - K014-network
evidence:
  - "coremusic-katmanli-mimari-agaci-v2.md §2.9 satır 310–329"
  - "shared/src/Middleware/ envanteri (11 dosya, 2026-10-12)"
  - "shared/src/Security/ envanteri (6 sınıf, 2026-10-12)"
validation:
  - "2026-10-12 — kapsam içi 11 çocuk v2 mini-ağaçıyla karşılaştırıldı"
  - "2026-10-12 — kapsam dışı maddeler K010/K011/K013/K014/K017/K018 sınır tablosuyla çaprazlandı"
open_questions:
  - "A10 SSRF kapsamı K013 ile paylaşımda → sınır onayı bekliyor"
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
| 1 | User entity, hesap, kayıt akışı | K010-identitiy | v2 §2.10 — "Kim olduğun" |
| 2 | Credentials veri modeli (tablo/şema) | K010-identitiy | veri modeli ≠ güvenlik politikası |
| 3 | Roller/izinlerin veri modeli | K010-identitiy | authorization yalnız KARAR kuralını koyar |
| 4 | SQL şema, migration | K007-data | .ai/AGENTS.md §5 domain boundary |
| 5 | API routing sözleşmesi | K013-integraion | K013 API kontratlarını taşır |
| 6 | DNS / firewall / altyapı | K014-network | K009 yalnız TLS gereksinimi koyar |
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
| K014-network → K009 | TLS sağlığı (HSTS ön koşulu) | partial — HSTS header kod kanıtı YOK |
| K010-identitiy ↔ K009 | auth akışı sözleşmesi | ⚠️ VERIFICATION REQUIRED (döngü riski) |
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
| Kullanıcı/hesap modeli (tablo, alan) | Kimlik katmanı tanımı K010-identitiy | K010-identitiy |
| Login/MFA/şifre sıfırlama akışı | Kimlik doğrulama deneyimi K010 | K010-identitiy |
| OAuth2/OIDC sağlayıcı rolü | Kimlik sağlayıcı mimarisi K010 | K010-identitiy |
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
| Çakışma | K010-identitiy dosyaları otoritedir (Authority sırası: .ai/CLAUDE.md §2.1) |

## §KAPSAM KABUL KRİTERLERİ

| # | Kriter | Ölçüt | Doğrulama |
|---|---|---|---|
| 1 | Kapsam 11 çocukla tanımlı | v2 §2.9 satır 318-328 | index.md §GRUPLAR |
| 2 | Her çocuk için 6 dosya | ADR-097 §5.5 | çocuk dizinleri |
| 3 | Kimlik modeline giriş yok | K010 sınırı | §KAPSAM-DIŞI tablosu |
| 4 | Kod kanıtları gerçek | Dosya·Satır | kod-ornekleri.md |
| 5 | Doğrulama ≥25 içerik satırı | v2 §4.2 | security-dogrulama.md |
