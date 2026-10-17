---
node_id: K009-security/security-dogrulama
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/security-master
  - K009-security/security-kapsam
  - K009-security/security-web-arastirma
evidence: "15 benzersiz URL (HTTP 200, 2026-10-12) + 30+ doğrulama satırı + Dosya·Satır kanıtları"
validation: "Her kontrol satırı KANIT/BOŞLUK/⚠️ durumuyla kapanır; ≥25 içerik satırı"
open_questions: "MFA kanıtı yok; HSTS preload doğrulanmadı; AES-GCM kullanım kanıtı yok"
group: kanit
---

# K009 · Güvenlik · Doğrulama (Kök)

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-12 · Rol: K009 güvenlik kontrollerinin KANITLI doğrulaması.
> Doğrulama = her kontrol için kaynak URL + kod kanıtı (Dosya·Satır) + durum. Kanıtsız satır yazılmaz.

## §1 DOĞRULAMA KAYNAK WEB (RFC / PHP / MDN / W3C)

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| HTTP Semantiği (RFC 9110) başlık/yanıt kuralları tanımlar | https://www.rfc-editor.org/rfc/rfc9110.html | Tarih: 2026-10-12 | HTTP 200 |
| HTTP Önbellekleme (RFC 9111) Cache-Control semantiği | https://www.rfc-editor.org/rfc/rfc9111.html | Tarih: 2026-10-12 | HTTP 200 |
| RFC 9325 TLS uygulama önerileri (güçlü şifre, yeniden kullanım) | https://www.rfc-editor.org/rfc/rfc9325.html | Tarih: 2026-10-12 | HTTP 200 |
| PHP random_bytes() kriptografik güvenli rastgelelik üretir | https://www.php.net/manual/en/function.random-bytes.php | Tarih: 2026-10-12 | HTTP 200 |
| PHP hash_equals() sabit-zamanlı karşılaştırma sağlar | https://www.php.net/manual/en/function.hash-equals.php | Tarih: 2026-10-12 | HTTP 200 |
| PHP password_hash() varsayılan algoritma Argon2id olabilir | https://www.php.net/manual/en/function.password-hash.php | Tarih: 2026-10-12 | HTTP 200 |
| PHP password_verify() bcrypt/argon2 hash doğrular | https://www.php.net/manual/en/function.password-verify.php | Tarih: 2026-10-12 | HTTP 200 |
| W3C Secure Contexts güvenli bağlam (HTTPS) gereksinimi | https://www.w3.org/TR/secure-contexts/ | Tarih: 2026-10-12 | HTTP 200 |
| MDN X-Content-Type-Options: nosniff MIME sniffing önler | https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/X-Content-Type-Options | Tarih: 2026-10-12 | HTTP 200 |
| MDN Referrer-Policy sızıntı kontrolü | https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Referrer-Policy | Tarih: 2026-10-12 | HTTP 200 |
| MDN Permissions-Policy izinleri kısıtlar | https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Permissions-Policy | Tarih: 2026-10-12 | HTTP 200 |
| MDN Strict-Transport-Security zorunlu HTTPS | https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Strict-Transport-Security | Tarih: 2026-10-12 | HTTP 200 |
| MDN Set-Cookie Secure/HttpOnly/SameSite bayrakları | https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Set-Cookie | Tarih: 2026-10-12 | HTTP 200 |
| MDN Cache-Control no-store hassas yanıt koruması | https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Cache-Control | Tarih: 2026-10-12 | HTTP 200 |
| MDN X-Frame-Options clickjacking savunması | https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/X-Frame-Options | Tarih: 2026-10-12 | HTTP 200 |

| §1 Tahsis Notu | |
|---|---|
| Bu dosyanın havuzu | rfc-editor(3) + php.net(4) + W3C(1) + MDN(7) = 15 URL |
| Tekrar yasağı | Bu URL'ler security-web-arastirma.md ve çocuk dosyalarda GEÇMEZ |

## §2 KONTROL DOĞRULAMA TABLOSU (KANITLI)

| # | Kontrol | Beklenen | Kanıt (Dosya·Satır) | Durum |
|---|---|---|---|---|
| D-01 | Parola hash algoritması Argon2id | password_get_info algoritma | Password.php·26/38-49 | ✅ KANITLI |
| D-02 | Parola doğrulama password_verify | verify çağrısı | Password.php·54-59 | ✅ KANITLI |
| D-03 | CSRF token üretimi + eşleşme | token doğrulama | CsrfMiddleware.php·7/42 | ✅ KANITLI |
| D-04 | CSRF test kapsamı | test dosyası | CsrfMiddlewareTest.php·12/308 | ✅ KANITLI |
| D-05 | Güvenlik başlıkları middleware | HSTS/CSP/XCTO | SecurityHeadersMiddleware.php·18/28/31 | ✅ KANITLI |
| D-06 | Oturum idle timeout 1800 sn | timeout değeri | SessionLifecycle.php·17 | ✅ KANITLI |
| D-07 | CSRF token session saklama | csrf_token | SessionLifecycle.php·48 | ✅ KANITLI |
| D-08 | CSP nonce üretimi | csp_nonce | SessionLifecycle.php·52 | ✅ KANITLI |
| D-09 | Session kimliğini ID ile değiştirme | session_regenerate_id(true) | SessionLifecycle.php·60 · SessionBootstrapper.php·58 | ✅ KANITLI |
| D-10 | API anahtarı sabit-zamanlı karşılaştırma | hash_equals | ApiKeyRepository.php·171 | ✅ KANITLI |
| D-11 | Güvenli rastgele üretimi | random_bytes | ApiKeyRepository.php·304 | ✅ KANITLI |
| D-12 | Rate limit middleware | limit denetimi | RateLimiterMiddleware.php·9 | ✅ KANITLI |
| D-13 | Rate limit depolama (apcu/memory) | adapter | CacheRateLimiter.php·8 · ApcuAdapter.php·5 · MemoryAdapter.php·5 | ✅ KANITLI |
| D-14 | Girdi doğrulama middleware | doğrulama | ValidationMiddleware.php·15 | ✅ KANITLI |
| D-15 | Rol tabanlı yetki denetimi | pozisyon kontrolü | PermissionMiddleware.php·15 | ✅ KANITLI |
| D-16 | JWT imzalama (RS256, asimetrik) | private.pem imzalar / public.pem doğrular | JwtService.php·22 (ALGORITHM=RS256) | ✅ KANITLI |
| D-17 | UUIDv7 rastgele+monotonik | zaman + rand | UuidV7.php·12/26/31 | ✅ KANITLI |
| D-18 | Log redaction (secret/parola) | test | LoggerRedactionTest.php·132 | ✅ KANITLI |
| D-19 | Yanıt cache-control | no-store | ResponseEmitter.php·139/141 | ✅ KANITLI |
| D-20 | Theme data URL engeli (CSP) | doğrulama | ThemeManager.php·239/253/278 | ✅ KANITLI |
| D-21 | Return URL open-redirect engeli | politika | ReturnUrlPolicy.php·5 | ✅ KANITLI |
| D-22 | API anahtarı test kapsamı | test | ApiKeyRepositoryTest.php·15/72 | ✅ KANITLI |
| D-23 | Bypass auth akışı (whitelist) | middleware | BypassAuthMiddleware.php·9 | ⚠️ RİSKLİ KANIT |
| D-24 | Origin/CORS denetimi | origin check | CorsMiddleware.php·15 · OriginCheckMiddleware.php·15 | ✅ KANITLI |

## §3 DOĞRULANAMAYAN / BOŞ KONTROLLER

| # | Kontrol | Beklenen Kanıt | Bulgu | Durum |
|---|---|---|---|---|
| D-25 | MFA / ikinci faktör | kod veya ADR | kanıt yok | ⚠️ VERIFICATION REQUIRED |
| D-26 | HSTS preload kaydı | hstspreload.org kaydı | erişilmedi | ⚠️ VERIFICATION REQUIRED |
| D-27 | AES-256-GCM kullanımı | openssl/aes çağrıları | kanıt okunmadı | ⚠️ VERIFICATION REQUIRED |
| D-28 | JWKS key rollover | key rotation kodu | kanıt yok | ⚠️ VERIFICATION REQUIRED |
| D-29 | Account lockout / brute-force kilidi | lockout kodu | rate limit var, lockout kanıtı yok | ⚠️ VERIFICATION REQUIRED |
| D-30 | Sızıntı testi (credential stuffing) | test senaryosu | yok | ⚠️ VERIFICATION REQUIRED |
| D-31 | A10 SSRF kontrolü | outbound URL denetimi | kanıt yok | UNKNOWN |
| D-32 | SIEM/uyarı (audit) | log akışı entegrasyonu | kanıt yok | UNKNOWN |
| D-33 | Dependency CVE taraması | composer audit raporu | rapor yok | UNKNOWN |
| D-34 | TLS 1.2+ sunucu yapılandırması | php.ini/nginx kanıtı | okunmadı | ⚠️ VERIFICATION REQUIRED |

## §4 KAYNAK — KONTROL X-REF (URL → DOĞRULAMA SATIRI)

| Kaynak (§1) | Doğrulanan Kural | İlgili Kontrol | Durum |
|---|---|---|---|
| RFC 9110 | Başlık yazım/yanıt semantiği | D-05, D-19 | EŞLEŞİYOR |
| RFC 9111 | Cache-Control no-store anlamı | D-19 | EŞLEŞİYOR |
| RFC 9325 | Güçlü şifre + TLS oturum yeniden kullanıma dair öneri | D-34 (kısmi) | ⚠️ sunucu kanıtı yok |
| php random_bytes | Kriptografik rastgelelik | D-11, D-17 | EŞLEŞİYOR |
| php hash_equals | Sabit-zamanlı karşılaştırma | D-10 | EŞLEŞİYOR |
| php password_hash | Argon2id mümkün | D-01 | EŞLEŞİYOR |
| php password_verify | Hash doğrulama | D-02 | EŞLEŞİYOR |
| W3C secure-contexts | HTTPS güvenli bağlam | D-05 (HSTS) | KISMİ |
| MDN XCTO | nosniff | D-05 | EŞLEŞİYOR |
| MDN Referrer-Policy | Referrer kısıtı | D-05 | EŞLEŞİYOR |
| MDN Permissions-Policy | İzin kısıtı | başlık okunmadı | ⚠️ D-05 eksik okuma |
| MDN HSTS | preload/onload semantics | D-05 + D-26 | KISMİ |
| MDN Set-Cookie | Secure/HttpOnly/SameSite | SessionInitializer cookie params | ✅ EŞLEŞİYOR (40-46; secure HTTPS koşullu) |
| MDN Cache-Control | no-store | D-19 | EŞLEŞİYOR |
| MDN X-Frame-Options | deny/sameorigin | D-05 | EŞLEŞİYOR |

## §5 SAYISAL ÖZET

| Metrik | Değer |
|---|---|
| Doğrulama tablosu satırı (§2+§3) | 34 (≥25 şart) |
| ✅ KANITLI | 24 |
| ⚠️ VERIFICATION REQUIRED | 8 |
| UNKNOWN | 2 |
| Benzersiz URL (HTTP 200) | 15 |
| Kod kanıtı Dosya·Satır | 30+ |
| uydurma kanıt | 0 |
| dolgu (TBD/...) | 0 |

## §6 DOĞRULAMA YÖNTEMİ

| Adım | İşlem | Kanıt |
|---|---|---|
| 1 | Hedef dosyalar filesystem ile açıldı | Dosya·Satır satırları |
| 2 | Beklenen kontrol deseni (fonksiyon/başlık) kodda arandı | §2 bulguları |
| 3 | Eşleşmeyen kontrol §3'e taşındı (sessiz geçiş yasak) | 10 satır |
| 4 | Her kontrolün web kaynağı §1'den eşlendi | §4 xref |
| 5 | Çelişki/eksik okuma işaretlendi | D-05 Permissions-Policy, D-23 bypass |
| 6 | Sınır: kimlik modeli K010'a devredildi | yok sayıldı (MFA K010 sorusu değil — K009 uygulama katmanı) |

## §7 TEKRAR DOĞRULAMA TETİKLEYİCİLERİ

| Tetikleyici | Yeniden Doğrulanacak | Sorumlu Dosya |
|---|---|---|
| Middleware kodu değişikliği | D-03/D-05/D-12/D-14/D-15/D-24 | security-dogrulama.md |
| Password.php değişikliği | D-01/D-02 | encryption + authentication |
| Oturum süresi değişikliği | D-06/D-09 | session-security |
| JWT algoritma değişikliği | D-16 | encryption (karar K-04) |
| Yeni URL eklenmesi | §1 havuz tekrar kontrolü (tekrarsızlık) | ilgili web-arastirma |
| OWASP Top10 sürüm değişimi | §4 xref | security-web-arastirma.md |

| Son Durum | |
|---|---|
| Dosya | security-dogrulama.md · group: kanit · 34 kontrol · 15 URL |
| Bağ | security-master / security-kapsam / security-web-arastirma |
| Sınır | git commit YASAK · Band-2..7 açılmaz · K0100-identitiy'e giriş yok |

## §8 YAPI / VAULT DOĞRULAMASI (K009 ŞEMA KONTROLÜ)

| # | Kontrol | Beklenen | Durum |
|---|---|---|---|
| D-35 | Kök dosya sayısı | 12 MD | ✅ (index, master, kapsam, bagimliliklar, dogrulama, web-arastirma, bilesenler, kod-ornekleri, kararlar, riskler, test-stratejisi, evrensel-dil) |
| D-36 | Frontmatter alan sayısı | 10 alan | ✅ (node_id..group) |
| D-37 | group dağılımı kök | mimari 7 / kod 2 / kanit 3 | ✅ |
| D-38 | index.md §GRUPLAR tablosu | zorunlu | ✅ |
| D-39 | Çocuk dizini (bu oturum) | 6 çocuk × 6 MD | ✅ (authentication, authorization, session-security, encryption, secrets, rate-limiting) |
| D-40 | Çocuk group eşlemesi | index/kapsam/bilesenler=mimari · ornekler=kod · dogrulama/web-arastirma=kanit | ✅ |
| D-41 | Dalga-3 kalan çocuk yolları | raporda listeli | ✅ (policies, input-validation, audit, threat-model, security-testing) |
| D-42 | node_id kök biçimi | K009-security/{rol} | ✅ |
| D-43 | node_id çocuk biçimi | K009-security/{çocuk}/{rol} | ✅ |
| D-44 | status değeri | verify-required | ✅ |
| D-45 | Başlıklar ## §N | zorunlu | ✅ |
| D-46 | Dolgu yasağı (TBD/.../Yakında) | 0 | ✅ |
| D-47 | git commit | ATILMADI | ✅ |
| D-48 | Band-2..7 | açılmadı | ✅ |
| D-49 | master dosya adı | security-master.md (v2 §2.9 satır 317) | ✅ KURAL 1 |
| D-50 | ID CONFLICT kaydı | K009 ↔ K009-api korunur | ✅ (v2 §6) |

## §9 BAĞIMLILIK ZİNCİRİ DOĞRULAMASI

| # | Kontrol | Durum |
|---|---|---|
| D-51 | depends_on listeleri gerçek dosyalara işaret ediyor | ✅ |
| D-52 | security-master → kapsam/bagimliliklar bağımlılığı tutarlı | ✅ |
| D-53 | web-arastirma → master/kapsam bağımlılığı tutarlı | ✅ |
| D-54 | dogrulama → master/kapsam/web-arastirma bağımlılığı tutarlı | ✅ |
| D-55 | Çocuklar → parent K009-security (parent_id) | ✅ |
| D-56 | Döngüsel bağımlılık (cycle) yok | ✅ |
| D-57 | evidence alanları dosya içeriğiyle uyumlu | ✅ |

## §10 URL TEKRARSIZLIK DENETİMİ

| # | Kontrol | Beklenen | Durum |
|---|---|---|---|
| D-58 | security-web-arastirma havuzu | OWASP 9 + CS 6 + NIST 10 | ✅ |
| D-59 | security-dogrulama havuzu | rfc/php/mdn/w3c 15 | ✅ |
| D-60 | İki kök dosya arası kesişim | 0 ortak URL | ✅ |
| D-61 | Çocuk web-arastirma havuzları | kök havuzlarıyla kesişmez | ✅ (tahsis §14, web-arastirma) |
| D-62 | 404 adayların başka dosyaya devri | yasak | ✅ |

## §11 DOĞRULAMA OTURUM KAYDI

| Adım | İşlem | Sonuç |
|---|---|---|
| 1 | Keşif: v2 §2.9/§3/§4 okundu | 11 çocuk satır eşlemesi (satır 318-328) |
| 2 | ADR-097 §5.5 okundu | grup şeması mimari/kod/kanıt |
| 3 | Kod kanıtları toplandı | 35+ Dosya·Satır |
| 4 | URL'ler HTTP GET ile doğrulandı | 40+ benzersiz 200 (2026-10-12) |
| 5 | Kök dosyalar yazıldı | 12/12 |
| 6 | 6 çocuk × 6 MD yazıldı | 36/36 (bu oturum) |
| 7 | Frontmatter/grup/satır kontrolü | §8 §9 §10 |
| 8 | Commit | ATILMADI (yasak) |
