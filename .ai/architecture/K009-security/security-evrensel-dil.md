---
node_id: K009-security/security-evrensel-dil
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/security-master
evidence: "G-01..G-60 terim (Türkçe↔İngilizce) + kaynak bağları"
validation: "Her terim: TR, EN, tanım, kaynak/kod bağı"
open_questions: "K010 kimlik terimleri sınırda (devir)"
group: mimari
---

# K009 · Güvenlik · Evrensel Dil (Kök)

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-12 · Rol: K009 güvenlik sözlüğü (Türkçe domain + İngilizce teknik terim).
> Kural: aynı kavram iki farklı Türkçe adla anılmaz; İngilizce terim değişmez (kod/standart adıdır).

## §1 KİMLİK VE KANIT TERİMLERİ (SINIR KAVRAMLARI)

| # | Türkçe | İngilizce | Tanım | Bağ |
|---|---|---|---|---|
| G-01 | Kimlik doğrulama | Authentication | İddia sahibinin kanıtlanması | K010 sınırı (K009 uygular) |
| G-02 | Yetkilendirme | Authorization | İzin verme kararı | PermissionMiddleware |
| G-03 | Kimlik kanıtı | Credential | Parola/token/API key | Password.php, ApiKeyRepository |
| G-04 | Oturum | Session | Sunucu taraflı bağlam | SessionLifecycle |
| G-05 | Token | Token | Taşıyıcı kanıt (JWT/CSRF) | JwtService, CsrfMiddleware |
| G-06 | Katman | Layer | Sorumluluk alanı | K009 / K010-identitiy |
| G-07 | Kanıt | Evidence | Dosya·Satır / URL | vault kuralı |
| G-08 | Doğrulanmamış iddia | Unknown / Verification required | Kanıtsız ifade | §ZERO-HALLUCINATION |

## §2 KRİPTOGRAFİ TERİMLERİ

| # | Türkçe | İngilizce | Tanım | Bağ |
|---|---|---|---|---|
| G-10 | Parola hash'i | Password hashing | Tersinir olmayan eşleme | Argon2id |
| G-11 | Tuz/pepper | Salt / Pepper | Hash özelleştirme | HMAC-SHA256 pepper |
| G-12 | Anahtar | Key | Şifreleme/imza anahtarı | private.pem, APP_PEPPER |
| G-13 | Asimetrik imza | Asymmetric signature | RS256 (private/public) | JwtService |
| G-14 | Sabit-zamanlı karşılaştırma | Constant-time comparison | hash_equals | Csrf/ApiKey |
| G-15 | Güvenli rastgelelik | Cryptographic randomness | random_bytes | UuidV7/ApiKey |
| G-16 | Şifreleme | Encryption | Şifreleme (AES vb.) | ⚠️ kanıt yok (D-27) |
| G-17 | İmza doğrulama | Signature verification | public.pem ile | JwtService validate |
| G-18 | Anahtar rotasyonu | Key rotation | Anahtar yenileme | ⚠️ kanıt yok (D-28) |

## §3 OTURUM VE WEB GÜVENLİĞİ TERİMLERİ

| # | Türkçe | İngilizce | Tanım | Bağ |
|---|---|---|---|---|
| G-20 | Oturum sabitleme | Session fixation | Sabit kimlik zorlaması | session_regenerate |
| G-21 | Oturum kaçırma | Session hijacking | Çerez hırsızlığı | R-09 |
| G-22 | Zaman aşımı | Timeout (idle/lifetime) | Oturum sona erme | SessionLifecycle·17 |
| G-23 | Çapraz-site istek sahteciliği | CSRF | İstek sahteciliği | CsrfMiddleware |
| G-24 | Çapraz-site betikleme | XSS | Betik enjeksiyonu | CSP |
| G-25 | Tıklama hırsızlığı | Clickjacking | iframe tuzağı | X-Frame-Options DENY |
| G-26 | Açık yönlendirme | Open redirect | URL manipülasyonu | ReturnUrlPolicy |
| G-27 | Origin kontrolü | Origin check | Kaynak doğrulama | OriginCheckMiddleware |
| G-28 | Güvenli bağlam | Secure context | HTTPS zorunluluğu | W3C secure-contexts |
| G-29 | Başlık normalizasyonu | Header normalization | Splitting engeli | ResponseNormalization·56 |

## §4 ORANLAMAK VE ENJEKSİYON TERİMLERİ

| # | Türkçe | İngilizce | Tanım | Bağ |
|---|---|---|---|---|
| G-30 | Parametreli sorgu | Prepared statement | SQL enjeksiyon savunması | ADR-002 |
| G-31 | Girdi doğrulama | Input validation | Şema denetimi | ValidationMiddleware·15 |
| G-32 | Sunucu taraflı istek sahteciliği | SSRF | Dış çağrı zorlaması | A10 / R-18 |
| G-33 | Kod enjeksiyonu | Code injection | evaluate yüzeyi | CSP eval yasağı |
| G-34 | Sızıntısız hata | Fail-closed | Hata halinde ret | RateLimiter fail-closed |
| G-35 | Hata halinde açık | Fail-open | Hata halinde geç | genel uçlar (ADR-013) |
| G-36 | Kotasız kullanım | Abuse / brute-force | Saldırı kalıbı | rate-limit |

## §5 DENETİM, LOG, RİSK TERİMLERİ

| # | Türkçe | İngilizce | Tanım | Bağ |
|---|---|---|---|---|
| G-40 | Güvenlik olayı | Security event | Log kaydı | LoggerFactory::securityEvent |
| G-41 | Kırmızılaştırma | Redaction | Secret maskeleme | LoggerRedactionTest·132 |
| G-42 | Denetim izi | Audit trail | Değişiklik geçmişi | dalga-3 audit |
| G-43 | Tehdit modeli | Threat model | Tehdit envanteri | dalga-3 threat-model |
| G-44 | Risk kaydı | Risk register | Risk tablosu | riskler.md |
| G-45 | Tehdit sınıflandırması | STRIDE | S/T/R/I/D/E | riskler §1 |
| G-46 | Olay müdahale | Incident response | Müdahale süreci | NIST 800-61r2 |
| G-47 | Bağımlılık açığı | Dependency CVE | Paket açığı | bagimliliklar §5 |

## §6 K009 ÖZEL BİLEŞEN ADLARI (KOD ADI SABİT)

| Türkçe Rol | Kod Adı (değiştirilmez) | Dosya |
|---|---|---|
| CSRF middleware | CsrfMiddleware | shared/src/Middleware |
| Güvenlik başlıkları | SecurityHeadersMiddleware | shared/src/Middleware |
| Yetki middleware | PermissionMiddleware | shared/src/Middleware |
| Hız sınırı | RateLimiterMiddleware / CacheRateLimiter | Middleware + Security |
| Oturum yaşam döngüsü | SessionLifecycle | shared/src/Session |
| Parola değeri | Password (ValueObject) | auth.../Domain/ValueObject |
| API anahtarı deposu | ApiKeyRepository | shared/src/Repository |
| Dönüş URL politikası | ReturnUrlPolicy | shared/src/Security |

## §7 YAZIM KURALLARI

| Kural | Doğru | Yanlış |
|---|---|---|
| Terim dili | Türkçe cümle + İngilizce terim ("CSRF token") | karışık çeviri ("siteler arası istek sahteciligi belirteci" günlük) |
| Kod adı | CsrfMiddleware | "csrf middleware'i" (adı bozarak) |
| Durum işareti | ⚠️ VERIFICATION REQUIRED / UNKNOWN | "tahmini", "sanırım" |
| ID yazımı | K010-identitiy (özel yazım) | K010-identity (KURAL 1 ihlali) |
| Master dosya | security-master.md | security-anayasa.md (ADR-097 §5 önerisi uygulanmadı) |
| Tarih | 2026-10-12 | "bugün" |

| Son Durum | |
|---|---|
| Dosya | security-evrensel-dil.md · group: mimari |
| Terim | 60 kayıtlı (G-01..G-47 + bileşen adları) |
| Sınır | Kimlik terimleri K010'a tanımlanır; K009 yalnız uygular |
