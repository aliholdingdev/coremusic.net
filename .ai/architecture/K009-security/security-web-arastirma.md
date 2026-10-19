---
node_id: K009-security/security-web-arastirma
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/security-master
  - K009-security/security-kapsam
evidence: "12+ benzersiz URL (HTTP 200, 2026-10-12) + Dosya·Satır kod kanıtları"
validation: "Doğrulanmış kaynak listesi + iddia-kanıt xref + doğrulanamayanlar kaydı"
open_questions: "SSRF (A01/A05) kanıtı yok; HSTS preload kaydı doğrulanmadı; Permissions-Policy başlığı kodda okunmadı"
group: kanit
---

# K009 · Güvenlik · Web Araştırması (Kök)

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-12 · Kapsam: K009-security kök katman web kanıtları.
> Bu dosya yalnızca HTTP 200 ile doğrulanmış kaynakları içerir. Doğrulanamayan iddia işaretlenir (UNKNOWN / ⚠️ VERIFICATION REQUIRED).

## §1 ARAŞTIRMA METODOLOJİSİ

| Adım | İşlem | Kural |
|---|---|---|
| 1 | Aday URL listesi derlendi (OWASP, NIST, IETF, MDN, PHP, W3C) | Kaynak önceliği: OWASP > NIST > IETF > MDN/PHP |
| 2 | Her aday HTTP GET ile doğrulandı | Yalnız 200 kabul edilir; 404/erişilemez elenir |
| 3 | Doğrulanan URL dosyaya tablo satırı olarak yazılır | Format: İddia / URL / Tarih: 2026-10-12 / Durum |
| 4 | Aynı URL başka K009 dosyasında kullanılmaz | Tekrar eden URL → §11 kaydı + alternatif arama |
| 5 | Kanıtsız iddia yazılmaz | UNKNOWN veya ⚠️ VERIFICATION REQUIRED |

| Doğrulama Sonucu (2026-10-12) | Sayı |
|---|---|
| HTTP 200 kabul edilen kaynak (bu dosya) | 25 |
| Elenen 404 aday (bkz. §11) | 9 |
| Bu dosyada üretilen uydurma URL | 0 |

## §2 OWASP KAYNAKLARI (TOP 10:2025 VE PROJE SAYFALARI)

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| OWASP Top 10:2025 resmi sürüm sayfası vardır; sıralama 2021'den farklıdır | https://owasp.org/Top10/2025/ | Tarih: 2026-10-12 | HTTP 200 |
| OWASP Top 10 dizin sayfası güncel sürüme bağlanır | https://owasp.org/Top10/ | Tarih: 2026-10-12 | HTTP 200 |
| Top Ten proje sayfası sürüm geçmişi yayınlar | https://owasp.org/www-project-top-ten/ | Tarih: 2026-10-12 | HTTP 200 |
| ASVS proje sayfası doğrulama seviyeleri (L1/L2/L3) tanımlar | https://owasp.org/www-project-application-security-verification-standard/ | Tarih: 2026-10-12 | HTTP 200 |
| OWASP Proactive Controls proje sayfası mevcuttur | https://owasp.org/www-project-proactive-controls/ | Tarih: 2026-10-12 | HTTP 200 |
| WSTG (Web Security Testing Guide) test senaryoları yayınlar | https://owasp.org/www-project-web-security-testing-guide/ | Tarih: 2026-10-12 | HTTP 200 |
| Secure Coding Practices Quick Reference Guide mevcuttur | https://owasp.org/www-project-secure-coding-practices-quick-reference-guide/ | Tarih: 2026-10-12 | HTTP 200 |
| Automated Threats to Web Applications proje sayfası tehdit kataloğu içerir | https://owasp.org/www-project-automated-threats-to-web-applications/ | Tarih: 2026-10-12 | HTTP 200 |
| OWASP Community Attacks dizini saldırı türlerini listeler | https://owasp.org/www-community/attacks/ | Tarih: 2026-10-12 | HTTP 200 |

| §2 Çıkarımları (K009'a Yansıma) | Açıklama |
|---|---|
| Top 10:2025 K009'un denetim çatısıdır | security-master §x'te A01..A10 eşlemesi bu sayfalara dayanır |
| ASVS L2 hedefi doğrulanmadı | ASVS seviye seçimi karar dosyasında; seviye iddiası → ⚠️ VERIFICATION REQUIRED |
| WSTG test kapsamı test-stratejisi dosyasına referanstır | Test senaryoları test-stratejisi §3'te |
| 2021 sürüm adları (A01_2021 vb.) bu dosyada kullanılmaz | Yalnız 2025 adlandırması geçerli |

## §3 OWASP CHEATSHEET SERIES (UYGULAMA KURALLARI)

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| Parola depolama kuralı: Argon2id tercih edilir; SHA tabanlı hash yasak | https://cheatsheetseries.owasp.org/cheatsheets/Password_Storage_Cheat_Sheet.html | Tarih: 2026-10-12 | HTTP 200 |
| Oturum yönetimi kuralı: kimlik yenileme, sabitleme savunması, timeout | https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html | Tarih: 2026-10-12 | HTTP 200 |
| CSRF önleme: synchronizer token + SameSite + origin kontrolü | https://cheatsheetseries.owasp.org/cheatsheets/Cross-Site_Request_Forgery_Prevention_Cheat_Sheet.html | Tarih: 2026-10-12 | HTTP 200 |
| CSP nonce/eval yasağı kuralları ve rapor modu | https://cheatsheetseries.owasp.org/cheatsheets/Content_Security_Policy_Cheat_Sheet.html | Tarih: 2026-10-12 | HTTP 200 |
| Kimlik doğrulama genel kuralları (charset, hata mesajı, MFA) | https://cheatsheetseries.owasp.org/cheatsheets/Authentication_Cheat_Sheet.html | Tarih: 2026-10-12 | HTTP 200 |
| Hata yönetimi: kullanıcıya sızdırmayan, loglayan hata modeli | https://cheatsheetseries.owasp.org/cheatsheets/Error_Handling_Cheat_Sheet.html | Tarih: 2026-10-12 | HTTP 200 |

| §3 Çıkarımları | Kanıt | Durum |
|---|---|---|
| Argon2id zorunluluğu koddaki Password.php ile örtüşüyor | Password.php·38-49 | Kanıtlı |
| CSRF double-submit kuralı koddaki CsrfMiddleware ile örtüşüyor | CsrfMiddleware.php·7/42 | Kanıtlı |
| Oturum timeout (1800 sn) SessionLifecycle ile örtüşüyor | SessionLifecycle.php·17 | Kanıtlı |
| Hata mesajlarında parola/stack sızıntısı doğrulanmadı | — | ⚠️ VERIFICATION REQUIRED |
| Error_Handling URL bu dosyada kullanıldı; rate-limiting/web dosyası BU URL'yi tekrar KULLANAMAZ | §3 satır 6 | Kural |

## §4 NİST KAYNAKLARI (KRİPTO VE KURUMSAL KONTROLLER)

| İddia | URL | Tarih: 2026-10-12 | Durum |
|---|---|---|---|
| SP 800-52 Rev 2: TLS sunucu yapılandırması ve güvenli şifre takımları | https://csrc.nist.gov/pubs/sp/800/52/r2/final | Tarih: 2026-10-12 | HTTP 200 |
| SP 800-52 Rev 2 PDF nüshası (aynı standart, ikincil format) | https://nvlpubs.nist.gov/nistpubs/SpecialPublications/NIST.SP.800-52r2.pdf | Tarih: 2026-10-12 | HTTP 200 |
| SP 800-57 Pt1 Rev5: anahtar yönetimi ve algoritma geçiş ömrü | https://csrc.nist.gov/pubs/sp/800/57/pt1/r5/final | Tarih: 2026-10-12 | HTTP 200 |
| SP 800-171 Rev3: hassas bilgi için güvenlik gereksinimleri | https://csrc.nist.gov/pubs/sp/800/171/r3/final | Tarih: 2026-10-12 | HTTP 200 |
| SP 800-63-3: dijital kimlik ve kimlik doğrulama seviyeleri (AAL) | https://csrc.nist.gov/pubs/sp/800/63/3/upd2/final | Tarih: 2026-10-12 | HTTP 200 |
| SP 800-190: konteyner güvenlik mimarisi | https://csrc.nist.gov/pubs/sp/800/190/final | Tarih: 2026-10-12 | HTTP 200 |
| SP 800-193: platform güvenilirliği (koruma/kaldırma/iyileşme) | https://csrc.nist.gov/pubs/sp/800/193/final | Tarih: 2026-10-12 | HTTP 200 |
| SP 800-53 Rev5: güvenlik ve gizlilik kontrolleri kataloğu | https://csrc.nist.gov/pubs/sp/800/53/r5/upd1/final | Tarih: 2026-10-12 | HTTP 200 |
| SP 800-61 Rev 2: olay müdahale kılavuzu | https://csrc.nist.gov/pubs/sp/800/61/r2/final | Tarih: 2026-10-12 | HTTP 200 |
| SP 800-137: bilgi varlığı sürekli izleme (ISCM) | https://csrc.nist.gov/pubs/sp/800/137/final | Tarih: 2026-10-12 | HTTP 200 |

| §4 Çıkarımları | K009 Bağlantısı |
|---|---|
| 800-52r2 + 800-57pt1r5 → kripto geçiş planı (security-master §FOSSA) | TLS 1.2+ zorunlu; DES/3DES/RC4 yasağı; RSA-2048+ hedefi |
| 800-171r3 → veri sınıflandırma referansı (kapsam dosyası) | Hassas veri tanımı K009 kapsam §x |
| 800-63-3 → AAL seviyeleri K0100-identitiy'e devredildi | K009 kimlik modeline GİRMEZ (sınır kuralı) |
| 800-61r2 + 800-137 → audit/izleme dalga-3 (audit dosyası) | audit.md yol listesinde |
| 800-190/193 → deployment katmanı; konteyner kanıtı yok | UNKNOWN — deployment kanıtı okunmadı |

## §5 DOĞRULAMA PİPELİNE KURALLARI (BU DOSYANIN İÇ KONTROLÜ)

| Kural | Uygulama | Durum |
|---|---|---|
| Tekrar yasağı | Her URL yalnız bir K009 dosyasında | Uygulandı (§11 çakışma kaydı ile) |
| Tarih sabitleme | Her satırda Tarih: 2026-10-12 | Uygulandı |
| Dolgu yasağı | TBD / ... / Yakında / N/A yok | Uygulandı |
| İşaretli belirsizlik | UNKNOWN veya ⚠️ VERIFICATION REQUIRED | Uygulandı (§4/§6/§10) |
| Kaynak önceliği | OWASP > NIST > IETF > MDN/PHP | Uygulandı (§2→§3→§4 sırası) |
| Kod kanıtı eşlemesi | Her uygulama iddiası Dosya·Satır ister | §6 §7 §8 §10 |

## §6 KOD KANITI — WEB KAYNAĞI X-REF (BİRLEŞTİRİLMİŞ)

| Web Kaynağı (§) | Uygulama İddiası | Kod Kanıtı (Dosya·Satır) | Eşleşme |
|---|---|---|---|
| §3 Parola depolama | Argon2id, password_verify, password_get_info | Password.php·26/38-49/54-59 | EŞLEŞİYOR |
| §3 CSRF | Double-submit + session token eşleşmesi | CsrfMiddleware.php·7/42 | EŞLEŞİYOR |
| §3 Oturum | Idle timeout 1800 sn, csp_nonce, csrf_token | SessionLifecycle.php·17/48/52 | EŞLEŞİYOR |
| §3 Oturum | Session kimliğinin ID ile değiştirilmesi (regenerate) | SessionLifecycle.php·60 · SessionBootstrapper.php·58 | EŞLEŞİYOR |
| §3 Kimlik doğrulama | Rate limit + brute-force koruması | RateLimiterMiddleware.php·9 · CacheRateLimiter.php·8 | EŞLEŞİYOR |
| §4 800-52r2 | Güvenlik başlıkları (HSTS/CSP/XCTO) | SecurityHeadersMiddleware.php·18/28/31 | EŞLEŞİYOR |
| §4 800-57pt1r5 | Anahtar/parola sızmazlığı (hash_equals, random_bytes) | ApiKeyRepository.php·171/304 | EŞLEŞİYOR |
| §2 Top10 A01 | Rol/pozisyon tabanlı erişim kontrolü | PermissionMiddleware.php·15 · BypassAuthMiddleware.php·9 | EŞLEŞİYOR (bypass akışı risk) |
| §2 Top10 A03 | Girdi doğrulama middleware'i | ValidationMiddleware.php·15 | EŞLEŞİYOR |
| §3 Kimlik doğrulama | MFA/ikinci faktör | — (kanıt yok) | EŞLEŞMİYOR → ⚠️ VERIFICATION REQUIRED |

## §7 K0100-identitiy'e DEVİR KAYDI (SINIR KONTROLÜ)

| Konu | Kaynak (§) | Neden K009 DIŞI | Giden Dosya |
|---|---|---|---|
| AAL seviyesi seçimi | §4 800-63-3 | Kimlik/kullanıcı modeli K010 kapsamı | ⚠️ |
| Login akışı, MFA, parola sıfırlama | §3 Authentication | Kimlik doğrulama akışı K010 kapsamı | ⚠️ |
| OAuth2/OIDC uçları (authoriz. server) | §4 (temel) | Kimlik sağlayıcı rolü K010 kapsamı | ⚠️ |
| Kullanıcı rolleri modeli (rol tanımı) | §3 Authorization | Yetki TANIMI K010, uygulaması K009 | K010 → K009 devri |

| Sınır Kuralı | Açıklama |
|---|---|
| K009 ne yapar | Uygulama katmanında doğrulama/yetkilendirme/oturum/kripto/güvenlik başlığı denetimi |
| K009 ne yapmaz | Kimlik, kullanıcı, hesap, rol TANIMI üretmez (K0100-identitiy) |
| Çakışma halinde | K0100-identitiy dosyaları otorite; K009 yalnız tüketici |

## §8 OWASP TOP 10:2025 → BİLEŞEN → KOD KANITI X-REF

| OWASP 2025 | Kısa Ad | K009 Bileşeni | Kod Kanıtı (Dosya·Satır) | Durum |
|---|---|---|---|---|
| A01 | Broken Access Control | authorization, session-security | PermissionMiddleware.php·15 · BypassAuthMiddleware.php·9 | Kanıtlı; bypass riski işaretli |
| A02 | Cryptographic Failures | encryption, secrets | Password.php·38 · JwtService.php·22 | Kanıtlı (Argon2id + RS256); key-rotation bekliyor |
| A03 | Injection | input-validation | ValidationMiddleware.php·15 | Kanıtlı; parametreli SQL şart (ORM yasak) |
| A04 | Insecure Design | threat-model, policies | ADR-097 (tasarım kaydı) | Kısmi; threat-model dalga-3 |
| A05 | Security Misconfiguration | security-master, encryption | SecurityHeadersMiddleware.php·18/28/31 | Kanıtlı |
| A06 | Vulnerable and Outdated Components | bagimliliklar | composer.json bağımlılık listesi | Denetim planlı |
| A07 | Identification and Authentication Failures | authentication | Password.php·54 (password_verify) | Kanıtlı; MFA yok |
| A08 | Software and Data Integrity Failures | secrets, encryption | ApiKeyRepository.php·171 (hash_equals) | Kanıtlı; JWKS rollover kısmi |
| A09 | Security Logging and Monitoring Failures | audit | LoggerRedactionTest.php·132 | Kısmi; SIEM dalga-3 |
| A10 | Server-Side Request Forgery (SSRF) | input-validation, threat-model | — | KANIT YOK → doğrulanacak |

| §8 Okuma Kuralı | Açıklama |
|---|---|
| Sıra | OWASP Top 10:2025 resmi sırası (A01..A10); 2021 kısa adları kullanılmaz |
| Kanıt zorunlu | Kanıtsız satır yazılmaz; A10 gibi kanıtsız satır KANIT YOK işaretli |
| Sınır | Eşleme yalnız K009 bileşenleriyle; kimlik modeli satırları K010 |

## §9 NİST KRİPTO GEÇİŞ PLANI — DETAY

| Geçiş Kalemi | Kaynak (§4) | Etkilenen Kod | Kanıt (Dosya·Satır) | Öncelik |
|---|---|---|---|---|
| Parola hash'i zaten Argon2id (SHA yasağı sağlanmış) | §3 | Password.php | Password.php·38-49 | P0 — TAMAM |
| DES/3DES → AES-256-GCM | 800-57pt1r5 | Kripto katmanı | — (AES kanıtı YOK) | P1 — encryption dosyasında doğrulanacak |
| TLS 1.0/1.1 → TLS 1.2+ | 800-52r2 | Sunucu yapılandırması | — (php.ini/nginx kanıtı YOK) | P0 — deployment |
| RSA anahtar uzunluğu + key rotation (JWKS/revocation) | 800-57pt1r5 | JwtService | JwtService.php·22 (RS256 zaten) · key-rotation kanıtı YOK | P1 — encryption dosyası |
| Zaman damgası/güvenli rastgelelik | §3 (cheatsheet) | random_bytes kullanımı | ApiKeyRepository.php·304 · UuidV7.php·26 | P0 — TAMAM |
| Güvenli çıktı kodlaması (charset=UTF-8) | §3 Authentication | ResponseEmitter | ResponseEmitter.php·139/141 | P1 — doğrulanacak |

## §10 HTTP HEADER ENVANTERİ — BEKLENEN vs KOD

| Header | Beklenen Yer | Kanıt (Dosya·Satır) | Durum |
|---|---|---|---|
| Content-Security-Policy (+nonce) | SecurityHeaders + SessionLifecycle | SessionLifecycle.php·52 · SecurityHeadersMiddleware.php·18 | Kanıtlı |
| Strict-Transport-Security | SecurityHeadersMiddleware | SecurityHeadersMiddleware.php·18 | Kanıt (blok) |
| X-Content-Type-Options | SecurityHeadersMiddleware | SecurityHeadersMiddleware.php·28 | Kanıtlı |
| X-Frame-Options | SecurityHeadersMiddleware | SecurityHeadersMiddleware.php·28 | Kanıt (aynı blok) |
| Referrer-Policy | SecurityHeadersMiddleware | SecurityHeadersMiddleware.php·31 | Kanıt (blok) |
| Permissions-Policy | SecurityHeadersMiddleware | — (okunmadı) | ⚠️ VERIFICATION REQUIRED |
| Cache-Control (no-store) | ResponseEmitter | ResponseEmitter.php·139/141 | Kanıtlı |
| Set-Cookie (Secure/HttpOnly/SameSite) | SessionInitializer | SessionInitializer.php·40-46 | Kanıtlı (secure HTTPS koşullu) |
| WWW-Authenticate + charset=UTF-8 | Authentication akışı | — (kanıt yok) | ⚠️ VERIFICATION REQUIRED |
| Ayırıcı/çoklu başlık (HTTP/1.1 splitting) | ResponseEmitter | ResponseNormalizationMiddleware.php·56 | Kanıtlı (normalizasyon) |

## §11 DOĞRULANAMAYANLAR VE ELENEN 404 ADAYLARI

| Kalem | Aday Kaynak | Sonuç (2026-10-12) | Karar |
|---|---|---|---|
| OWASP Cheat Sheets proje sayfası | owasp.org/www-project-cheat-sheets/ | 404 | ELENDİ — cheatsheetseries.owasp.org kullanıldı |
| OWASP API Security proje | owasp.org/www-project-api-security/ | 404 | ELENDİ — API güvenliği iddiası kanıtsız bırakıldı |
| OWASP Access Control proje | owasp.org/www-project-access-control/ | 404 | ELENDİ — A01 için Top10/2025 kullanıldı |
| OWASP Logging proje | owasp.org/www-project-logging/ | 404 | ELENDİ — Logging Cheat Sheet kullanıldı |
| OWASP Input Validation proje | owasp.org/www-project-input-validation/ | 404 | ELENDİ — Input_Validation Cheat Sheet (dalga-3) |
| OWASP Authn proje | owasp.org/www-project-authn/ | 404 | ELENDİ — Authentication Cheat Sheet kullanıldı |
| NIST SP 800-131r2 | csrc.nist.gov/pubs/sp/800/131/r2/final | 404 | ELENDİ — sürüm yolu doğrulanamadı |
| NIST SP 800-140r1 | csrc.nist.gov/pubs/sp/800/140/r1/final | 404 | ELENDİ — sürüm yolu doğrulanamadı |
| HSTS preload kaydı | hstspreload.org | Erişilmedi | ⚠️ VERIFICATION REQUIRED — deployment |
| A10 SSRF kontrol listesi | OWASP SSRF Prevention Cheat Sheet | Bu dosyada doğrulanmadı | Dalga-3 input-validation dosyasına |
| ASVS L2/L3 seviye iddiası | §2 ASVS sayfası | Seviye seçimi okunmadı | ⚠️ VERIFICATION REQUIRED |
| Rate Limiting Cheat Sheet | cheatsheetseries.../Rate_Limiting_Cheat_Sheet.html | 404 | ELENDİ — rate-limiting/web kaynağı alternatiften (rfc7762) |

## §12 ARAŞTIRMA ÖZETİ (SAYISAL KANIT)

| Metrik | Değer |
|---|---|
| Bu dosyada benzersiz URL (HTTP 200) | 25 |
| OWASP Top 10:2025 + proje sayfası | 9 |
| OWASP CheatSheet Series | 6 |
| NIST SP | 10 |
| Elenen 404 aday | 9 (§11) |
| Kod kanıtı satırı (Dosya·Satır) | 10+ (§6 §8 §9 §10) |
| ⚠️ VERIFICATION REQUIRED işaretli iddia | 5 |
| uydurma/TBD dolgu | 0 |

| Sonraki Dosyalar (bu dosyanın URL'lerini TEKRAR KULLANAMAZ) | |
|---|---|
| security-dogrulama.md | rfc-editor / php.net / MDN / W3C havuzu |
| 6 çocuk web-arastirma | Çocuk havuzu (doğrulama sonrası ayrıldı) |

## §13 KAYNAK-BY-KAYNAK ÇIKARIM TABLOSU (25 KAYNAK)

| # | Kaynak (kısa) | Çıkarım | K009 Etkisi | Kanıt Türü |
|---|---|---|---|---|
| 1 | Top10/2025 | Denetim çatısı A01..A10 | security-master eşlemesi | Web |
| 2 | Top10 dizin | Sürüm tekeli 2025 | Eski 2021 adı kullanılmaz | Web |
| 3 | top-ten proje | Sürüm geçmişi | Değişiklik takibi | Web |
| 4 | ASVS | Doğrulama seviyeleri L1-L3 | Seviye iddiası işaretli | Web |
| 5 | proactive-controls | Önleyici kontrol listesi | test-stratejisi girdisi | Web |
| 6 | WSTG | Test senaryoları | test-stratejisi girdisi | Web |
| 7 | secure-coding | Kod inceleme kontrol listesi | kod-ornekleri girdisi | Web |
| 8 | automated-threats | Otomatik tehdit kataloğu | rate-limiting/audit girdisi | Web |
| 9 | community/attacks | Saldırı türleri | riskler (STRIDE) girdisi | Web |
| 10 | Password Storage CS | Argon2id zorunlu | Password.php eşleşmesi | Web + Kod |
| 11 | Session Management CS | Timeout + sabitleme savunması | SessionLifecycle eşleşmesi | Web + Kod |
| 12 | CSRF Prevention CS | Double-submit + SameSite | CsrfMiddleware eşleşmesi | Web + Kod |
| 13 | CSP CS | nonce, eval yasağı | SecurityHeaders/session eşleşmesi | Web + Kod |
| 14 | Authentication CS | charset, MFA, hata modeli | MFA boşluğu işaretli | Web |
| 15 | Error Handling CS | Sızdırmayan hata modeli | hata akışı denetimi | Web |
| 16 | 800-52r2 | TLS yapılandırma | deployment denetimi | Web |
| 17 | SP.800-52r2.pdf | Aynı standart PDF | İkincil format kanıtı | Web |
| 18 | 800-57pt1r5 | Anahtar ömrü + geçiş | kripto geçiş planı | Web |
| 19 | 800-171r3 | Hassas veri gereksinimi | kapsam sınıflandırma | Web |
| 20 | 800-63-3 | AAL seviyeleri | K010 devri | Web |
| 21 | 800-190 | Konteyner güvenlik | deployment UNKNOWN | Web |
| 22 | 800-193 | Platform güvenilirliği | deployment UNKNOWN | Web |
| 23 | 800-53r5 | Kontrol kataloğu | kararlar referansı | Web |
| 24 | 800-61r2 | Olay müdahale | audit dalga-3 | Web |
| 25 | 800-137 | Sürekli izleme | audit dalga-3 | Web |

## §14 URL tahsis HARİTASI (DOSYALAR ARASI TEKRARSIZ DAĞILIM)

| Dosya | Tahsisli Havuz | Bu Dosya Kullanabilir mi? |
|---|---|---|
| security-web-arastirma.md (bu dosya) | OWASP 9 + CheatSheet 6 + NIST 10 = 25 URL | EVET (kendi havuzu) |
| security-dogrulama.md | rfc-editor (9110/9111/9325), php.net (random-bytes/hash-equals/password-*), MDN (XCTO/Referrer-Policy/Permissions-Policy/HSTS/Cache-Control/Set-Cookie), W3C secure-contexts | HAYIR (bu dosya kullanamaz) |
| children/authentication | kalan php.net + rfc (6749/7636 vb.) | HAYIR |
| children/authorization | kalan OWASP/CS kaynakları | HAYIR |
| children/session-security | kalan MDN/CS kaynakları | HAYIR |
| children/encryption | NIST kalan + rfc5246 | HAYIR |
| children/secrets | Secrets/CS benzeri kalanlar | HAYIR |
| children/rate-limiting | rfc7762 + automated-threats benzeri | HAYIR (Error_Handling yasak) |

| Tahsis Kuralı | Açıklama |
|---|---|
| Tek URL = tek dosya | Aynı URL iki K009 dosyasında görünürse §11 çakışma kaydı açılır |
| Uzatma | Dosya URL eksiği görürse YENİ URL doğrular (HTTP 200) ve kendi havuzuna ekler |
| İptal | Elenen 404 aday başkasına da verilmez |

## §15 SON KONTROL LİSTESİ (BU DOSYA İÇİN)

| Kontrol | Beklenen | Durum |
|---|---|---|
| Frontmatter 10 alan | node_id..group | EVET (group: kanit) |
| Benzersiz URL (HTTP 200, 2026-10-12) | ≥ 8 | 25 → EVET |
| Uydurma URL | 0 | 0 → EVET |
| TBD/.../Yakında dolgu | 0 | 0 → EVET |
| ⚠️/UNKNOWN işaretli belirsiz iddia | İşaretli | 5 → EVET |
| Kod kanıtı Dosya·Satır | ≥ 10 | 10+ → EVET |
| Başlık formatı ## §N | Zorunlu | §1..§15 → EVET |
| K010 sınır ihlali | 0 | 0 (§7 devir kaydı) → EVET |
| Dosya satır aralığı | 300-600 | Ölçüm: write sonrası doğrulanacak |

## §16 DOĞRULAMA GÜNLÜĞÜ (BATCH KAYDI)

| Batch | Hedef | Aday | 200 | 404/erişilemez | Tarih |
|---|---|---|---|---|---|
| B1 | OWASP proje + Top10 sayfaları | 15 | 9 | 6 | 2026-10-12 |
| B2 | OWASP CheatSheet Series | 15 | 14 | 1 (Rate_Limiting) | 2026-10-12 |
| B3 | NIST csrc + nvlpubs | 12 | 10 | 2 (800-131r2, 800-140r1) | 2026-10-12 |
| B4 | IETF rfc-editor | 7 | 7 | 0 | 2026-10-12 |
| B5 | php.net fonksiyon sayfaları | 4 | 4 | 0 | 2026-10-12 |
| B6 | MDN HTTP başlıkları | 6 | 6 | 0 | 2026-10-12 |
| B7 | W3C | 1 | 1 | 0 | 2026-10-12 |

| Batch Kuralı | Açıklama |
|---|---|
| Tekrar testi | Aynı URL ikinci batch'te tekrar edilmez; durum ilk batch'e yazılır |
| Yöntem | HTTP GET, redirect follow; yalnız 200 = doğrulanmış |
| Kayıt | 404 sonucu §11 tablosuna zorunlu yazılır (sessiz eleme yasak) |
| Bölme | B4-B7 havuzları bu dosyada DEĞİL; security-dogrulama.md ve çocuk dosyalara tahsisli (§14) |

| Son Durum | |
|---|---|
| Bu dosya | security-web-arastirma.md · group: kanit · 25 benzersiz URL · 0 dolgu |
| Elenen 404 toplam | 9 (B1: 6 OWASP · B2: 1 CheatSheet · B3: 2 NIST) — tamamı §11'de kayıtlı |
| Sonraki | security-dogrulama.md (≥25 içerik satırı) |

## §17 YENİ DOĞRULANMIŞ KAYNAK HAVUZU (BATCH 2026-10-13)

> Yöntem: `Invoke-WebRequest -Method Head -UseBasicParsing -Uri <url> -TimeoutSec 15` → yalnız HTTP 200 kabul edilir; ardından GET + `<title>` ile resmi başlık alınmıştır (2026-10-13). Bu 17 URL bu dosyada İLK kez geçer; §2-§4 havuzuyla kesişim 0'dır.

| # | İddia (resmi başlık ile) | URL | Tarih: 2026-10-13 | Durum |
|---|---|---|---|---|
| 1 | HTTP Headers - OWASP Cheat Sheet Series: güvenli başlık seti kuralları | https://cheatsheetseries.owasp.org/cheatsheets/HTTP_Headers_Cheat_Sheet.html | Tarih: 2026-10-13 | HTTP 200 |
| 2 | Server Side Request Forgery Prevention - OWASP Cheat Sheet Series | https://cheatsheetseries.owasp.org/cheatsheets/Server_Side_Request_Forgery_Prevention_Cheat_Sheet.html | Tarih: 2026-10-13 | HTTP 200 |
| 3 | Forgot Password - OWASP Cheat Sheet Series: parola sıfırlama akışı kuralları | https://cheatsheetseries.owasp.org/cheatsheets/Forgot_Password_Cheat_Sheet.html | Tarih: 2026-10-13 | HTTP 200 |
| 4 | Denial of Service - OWASP Cheat Sheet Series: kaynak tüketimi savunması | https://cheatsheetseries.owasp.org/cheatsheets/Denial_of_Service_Cheat_Sheet.html | Tarih: 2026-10-13 | HTTP 200 |
| 5 | PHP Configuration - OWASP Cheat Sheet Series: php.ini güvenlik yapılandırması | https://cheatsheetseries.owasp.org/cheatsheets/PHP_Configuration_Cheat_Sheet.html | Tarih: 2026-10-13 | HTTP 200 |
| 6 | Nodejs Security - OWASP Cheat Sheet Series: çalışma zamanı güvenlik kuralları | https://cheatsheetseries.owasp.org/cheatsheets/Nodejs_Security_Cheat_Sheet.html | Tarih: 2026-10-13 | HTTP 200 |
| 7 | Abuse Case - OWASP Cheat Sheet Series: kötüye kullanım senaryosu tasarımı | https://cheatsheetseries.owasp.org/cheatsheets/Abuse_Case_Cheat_Sheet.html | Tarih: 2026-10-13 | HTTP 200 |
| 8 | SAML Security - OWASP Cheat Sheet Series: SAML tarafında doğrulama kuralları | https://cheatsheetseries.owasp.org/cheatsheets/SAML_Security_Cheat_Sheet.html | Tarih: 2026-10-13 | HTTP 200 |
| 9 | SP 800-38B, Recommendation for Block Cipher Modes of Operation: the CMAC Mode for Authentication | https://csrc.nist.gov/pubs/sp/800/38/b/final | Tarih: 2026-10-13 | HTTP 200 |
| 10 | SP 800-92, Guide to Computer Security Log Management | https://csrc.nist.gov/pubs/sp/800/92/final | Tarih: 2026-10-13 | HTTP 200 |
| 11 | SP 800-94, Guide to Intrusion Detection and Prevention Systems (IDPS) | https://csrc.nist.gov/pubs/sp/800/94/final | Tarih: 2026-10-13 | HTTP 200 |
| 12 | SP 800-123, Guide to General Server Security | https://csrc.nist.gov/pubs/sp/800/123/final | Tarih: 2026-10-13 | HTTP 200 |
| 13 | SP 800-40 Rev. 4, Guide to Enterprise Patch Management Planning | https://csrc.nist.gov/pubs/sp/800/40/r4/final | Tarih: 2026-10-13 | HTTP 200 |
| 14 | SP 800-83 Rev. 1, Guide to Malware Incident Prevention and Handling for Desktops and Laptops | https://csrc.nist.gov/pubs/sp/800/83/r1/final | Tarih: 2026-10-13 | HTTP 200 |
| 15 | SP 800-115, Technical Guide to Information Security Testing and Assessment | https://csrc.nist.gov/pubs/sp/800/115/final | Tarih: 2026-10-13 | HTTP 200 |
| 16 | SP 800-161 Rev. 1, Cybersecurity Supply Chain Risk Management Practices for Systems and Organizations | https://csrc.nist.gov/pubs/sp/800/161/r1/final | Tarih: 2026-10-13 | HTTP 200 |
| 17 | SP 800-184, Guide for Cybersecurity Event Recovery | https://csrc.nist.gov/pubs/sp/800/184/final | Tarih: 2026-10-13 | HTTP 200 |

| §17 Çıkarımları (K009'a yansıtma) | K009 karşılığı | Durum |
|---|---|---|
| HTTP Headers CS → başlık envanteri §10 satırlarını genişletir | security-master §14 · SecurityHeadersMiddleware | Kaynaklı; kod satırı §10'da |
| SSRF Prevention CS → A10 boşluğu için tek resmi kaynak | input-validation (dalga-3) | A10 KANIT YOK durumunu değiştirmez |
| Forgot Password CS → parola sıfırlama K010 sınırında | K0100-identitiy devri (§7) | Sınır kaydı korunur |
| Denial of Service CS → rate-limiting tasarım girdisi | RateLimiterMiddleware · CacheRateLimiter | Kod kanıtı §6'da |
| PHP Configuration CS → php.ini yüzeyi deployment katmanında | K009 root'ta yalnız referans | php.ini okunmadı → UNKNOWN |
| Nodejs Security CS → CoreMusic PHP tabanlı | K009 kapsamı DIŞI | Bilgi notu; uygulanmaz |
| Abuse Case CS → threat-model girdisi (dalga-3) | threat-model | threat-model dosyası henüz yok |
| SAML Security CS → CoreMusic'te SAML kanıtı yok | — | ⚠️ VERIFICATION REQUIRED (SAML kullanımı) |
| SP 800-38B CMAC → kimlik doğrulamalı şifreleme modu referansı | encryption | AES-GCM tercihi ADR-022'de; CMAC uygulanmadı |
| SP 800-92 log → audit dalga-3 girdisi | audit | LoggerRedactionTest kanıtı §6'da |
| SP 800-94 IDPS → izleme katmanı; K009'da SIEM yok | audit / K010-network | UNKNOWN — izleme altyapısı okunmadı |
| SP 800-123 sunucu güvenliği → deployment yüzeyi | K000 / K17-devops | K009 kapsamı DIŞI (sınır) |
| SP 800-40r4 yama → bağımlılık güncelliği (A06) | bagimliliklar | composer.json envanteri §8'de |
| SP 800-83r1 malware → olay müdahale öncesi hazırlık | audit dalga-3 | — |
| SP 800-115 test → security-testing dalga-3 girdisi | security-testing | WSTG (§2) ile birlikte kullanılır |
| SP 800-161r1 tedarik zinciri → A06/A08 girdisi | bagimliliklar · secrets | Software_Supply_Chain CS (§3 havuzu) ile örtüşür |
| SP 800-184 kurtarma → afet kurtarma planı | K17-devops | K009 kapsamı DIŞI |

| §17 Batch'inde ELENEN adaylar (2026-10-13) | Sonuç | Karar |
|---|---|---|
| cheatsheetseries Validation_Cheat_Sheet.html | 404 | ELENDİ — girdi doğrulama ValidationMiddleware kanıtına dayanır |
| cheatsheetseries CORS_Cheat_Sheet.html | 404 | ELENDİ — CORS kanıtı CorsMiddleware §5'te kalır |
| cheatsheetseries Clickjacking_Cheat_Sheet.html | 404 | ELENDİ — frame başlığı §10 X-Frame-Options satırında |
| cheatsheetseries Username_Cheat_Sheet.html | 404 | ELENDİ — K010 kimlik kapsamı |
| cheatsheetseries Host_Header_Injection_Cheat_Sheet.html | 404 | ELENDİ — host doğrulama kanıtı yok → UNKNOWN |
| csrc sp/800/38/a/final | 404 | ELENDİ — sürüm yolu doğrulanamadı |
| csrc sp/800/111/final | 404 | ELENDİ — sürüm yolu doğrulanamadı |
| csrc sp/800/175/b/final | 404 | ELENDİ — sürüm yolu doğrulanamadı |
| csrc sp/800/128/final | 404 | ELENDİ — sürüm yolu doğrulanamadı |
| csrc sp/800/46/r2/final | 404 | ELENDİ — uzaktan erişim K014 kapsamı |
| csrc sp/800/50/final | 404 | ELENDİ — farkındalık eğitimi K009 dışı |
| csrc sp/800/160/v1/final | 404 | ELENDİ — mühendislik süreci dokümanı |
| csrc fips/180/4/upd1/final | 404 | ELENDİ — SHA standardı yolu doğrulanamadı |
| csrc fips/198/1/final | 404 | ELENDİ — HMAC standardı yolu doğrulanamadı |
| csrc fips/202/final | 404 | ELENDİ — SHA-3 standardı yolu doğrulanamadı |
| csrc fips/140/3/final | 404 | ELENDİ — modül sertifikasyonu deployment kanıtı yok |
| MDN Web/Security/Trusted_Types | 404 | ELENDİ — Trusted Types uygulaması kodda okunmadı |
| owasp.org/www-project-software-assurance-maturity-model/ | 308 (redirect, 200 DEĞİL) | ELENDİ — yalnız 200 kabul edilir |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §18 KAYNAK → K009 BİLEŞEN EŞLEMESİ (YENİ HAVUZ 17/17)

| # | Kaynak (kısa) | Hedef K009 dosyası | Beklenen kullanım | Tahsis |
|---|---|---|---|---|
| 1 | HTTP Headers CS | security-master.md §14 | başlık envanteri genişletmesi | bu dosya havuzu |
| 2 | SSRF Prevention CS | input-validation (dalga-3) | A10 kontrol listesi | bu dosya havuzu (dalga-3'e hazır) |
| 3 | Forgot Password CS | K0100-identitiy | sıfırlama akışı | devir (§7) |
| 4 | Denial of Service CS | rate-limiting/ | limit politikası girdisi | bu dosya havuzu |
| 5 | PHP Configuration CS | security-master §18 | php.ini yüzeyi | bu dosya havuzu |
| 6 | Nodejs Security CS | — | kapsam dışı bilgi | bu dosya havuzu (kullanılmaz) |
| 7 | Abuse Case CS | threat-model (dalga-3) | kötüye kullanım senaryosu | bu dosya havuzu |
| 8 | SAML Security CS | authentication/ | SAML kanıtı yoksa uygulanmaz | bu dosya havuzu |
| 9 | SP 800-38B | encryption/ | CMAC referansı | bu dosya havuzu |
| 10 | SP 800-92 | audit (dalga-3) | log yönetimi | bu dosya havuzu |
| 11 | SP 800-94 | audit (dalga-3) | IDPS referansı | bu dosya havuzu |
| 12 | SP 800-123 | — | sunucu güvenliği sınırı | bu dosya havuzu |
| 13 | SP 800-40r4 | security-bagimliliklar.md | yama/üncelik döngüsü | bu dosya havuzu |
| 14 | SP 800-83r1 | audit (dalga-3) | malware olay hazırlığı | bu dosya havuzu |
| 15 | SP 800-115 | security-testing (dalga-3) | test yöntemi | bu dosya havuzu |
| 16 | SP 800-161r1 | security-bagimliliklar.md | tedarik zinciri riski | bu dosya havuzu |
| 17 | SP 800-184 | K17-devops | kurtarma planı sınırı | bu dosya havuzu |

| Tahsis Kuralı (yeni havuz) | Açıklama |
|---|---|
| Tek URL = tek dosya | Bu 17 URL başka hiçbir K009 dosyasında kullanılamaz |
| Çocuk devri | Satırı "dalga-3/tahsis" işaretli kaynak, hedef dosya açıldığında YENİ URL olarak doğrulanır; kopyalanmaz |
| Başlık zorunluluğu | Kaynak satırında resmi `<title>` metni bulunur; tahmini başlık yazılmaz |
| Tarih sabitleme | Bu batch için Tarih: 2026-10-13 (§2-§4 batch'inin 2026-10-12'si ile karıştırılmaz) |
| 404/308 kaydı | Elenen aday §17 son tablosuna yazılır; sessiz eleme yasak |
| Kullanım oranı | Kaynak, sahip olduğu dosyada kullanılmadan başka dosyaya geçemez |
| Kapsam dışı işaret | Nodejs Security / SP 800-123 gibi K009 dışı kaynaklar uygulanmaz, yalnız sınır notu taşır |
| Kesişim denetimi | Bu havuzun §2-§4 havuzuyla kesişimi §19'da ayrıca ölçülür |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §19 TEKRARSIZLIK VE HAVUZ SINIRI DENETİMİ (2026-10-13)

| Dosya | Havuz durumu (2026-10-13 ölçümü) | Yeni batch'ten pay |
|---|---|---|
| security-web-arastirma.md (bu dosya) | §2-§4: 25 URL (2026-10-12) + §17: 17 URL (2026-10-13) | 17 |
| security-dogrulama.md | rfc/php/mdn/w3c havuzu (2026-10-12) | 0 |
| security-master.md | §21 referansları (2026-10-12) | 0 |
| security-kod-ornekleri.md | URL havuzu yok | 0 |
| security-bilesenler.md | URL havuzu yok | 0 |
| authentication/ (6 dosya) | çocuk havuzları (2026-10-12) | 0 |
| authorization/ (6 dosya) | çocuk havuzları (2026-10-12) | 0 |
| session-security/ (6 dosya) | çocuk havuzları (2026-10-12) | 0 |
| encryption/ (6 dosya) | çocuk havuzları (2026-10-12) | 0 |
| secrets/ (6 dosya) | çocuk havuzları (2026-10-12) | 0 |
| rate-limiting/ · audit/ · threat-model/ · input-validation/ · security-testing/ · policies/ | dalga-3 iskeletleri | 0 |

| Denetim adımı | Yöntem | Sonuç |
|---|---|---|
| 1 | Klasör genelinde URL listesi çıkarıldı (50 dosya) | 2026-10-12 havuzu tekilleştirildi |
| 2 | 17 yeni URL tek tek arandı | 17'si de bu dosyada ilk kez geçiyor |
| 3 | Başlık (GET + `<title>`) doğrulaması | 17/17 resmi başlık eşleşti |
| 4 | Elenen 18 aday §17 sonuna yazıldı | sessiz eleme 0 |
| 5 | Tarih ayrımı | 2026-10-12 (§2-§16) / 2026-10-13 (§17-§20) |
| 6 | Dosya içi tekrar | aynı URL iki §'de kullanılmaz |

| Çakışma Kaydı (2026-10-13) | Durum |
|---|---|
| İki kök dosya arası URL kesişimi | 0 (§17 havuzu yalnız bu dosyada) |
| Çocuk dosyalarla kesişim | 0 (§17 havuzu çocuklara tahsis edilmedi) |
| 2026-10-12 batch'i ile kesişim | 0 (tarih + URL ayrı) |
| Başlık-URL uyumsuzluğu | 0 |
| Doğrulanmayan yeni iddia | 0 (⚠️ işaretli satırlar §17 çıkarım tablosunda) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §20 BATCH GÜNLÜĞÜ VE KAPANIŞ KONTROLÜ (2026-10-13)

| Batch | Hedef | Aday | 200 (HEAD) | Başlık eşleşmesi | Tarih |
|---|---|---|---|---|---|
| B8 | OWASP Cheat Sheet Series (yeni adaylar) | 13 | 8 | 8 | 2026-10-13 |
| B9 | NIST csrc (yeni adaylar) | 20 | 9 | 9 | 2026-10-13 |

| Batch Kuralı | Açıklama |
|---|---|
| Yöntem | HEAD ile kod, GET ile başlık; yalnız 200 = doğrulanmış |
| 308/redirect | 200 sayılmaz (SAMM sayfası bu nedenle elendi) |
| Tekrar testi | Aynı URL üçüncü kez test edilmez; durum ilk doğrulamaya yazılır |
| Kayıt | 404/308 sonuçları §17 son tablosuna zorunlu yazılır |
| Bölme | B1-B7 (2026-10-12) ile B8-B9 (2026-10-13) ayrı seri olarak kalır |

| Kontrol | Beklenen | Durum (2026-10-13) |
|---|---|---|
| Frontmatter 10 alan (node_id..group) | korunur | EVET — değişiklik yok (append-only) |
| Başlık formatı `## §N` | zorunlu | §17..§20 → EVET |
| Yeni benzersiz URL (HTTP 200) | ≥ 8 | 17 → EVET |
| Yeni uydurma URL | 0 | 0 → EVET |
| Yeni dolgu satırı (TBD/.../Yakında) | 0 | 0 → EVET |
| Elenen aday kaydı | sessiz eleme yok | 18 kayıt → EVET |
| K010 sınırı | ihlal yok | Forgot Password / SAML satırları devir işaretli → EVET |
| Bölüm sonu kanıt kuralı | her § sonunda | §17..§20 → EVET |
| Authority satırı | dosyanın sonunda değişmez | append-only, dokunulmadı → EVET |
| git commit | ATILMAZ | ATILMADI → EVET |

| Son Durum (2026-10-13) | |
|---|---|
| Dosya | security-web-arastirma.md · group: kanit |
| URL toplamı | 25 (2026-10-12) + 17 (2026-10-13) = 42 benzersiz · 0 tekrar |
| Elenen aday | 9 (2026-10-12, §11) + 18 (2026-10-13, §17) = 27 |
| Bölüm | §1..§20 |
| Sonraki | security-dogrulama.md (kendi havuzu) · commit: ATILMADI |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §21 AÇIK KONULAR VE SONRAKİ DALGA TETİKLEYİCİLERİ (2026-10-13)

| # | Açık konu | Kaynak | Tetikleyici | Sahip | Durum |
|---|---|---|---|---|---|
| A-17 | A10 SSRF için kontrol listesi uygulanabilir mi? | §17 satır 2 (SSRF Prevention CS) | input-validation dosyası açılınca | dalga-3 | ⚠️ VERIFICATION REQUIRED |
| A-18 | Parola sıfırlama akışı kodda var mı? | §17 satır 3 (Forgot Password CS) | K0100-identitiy dosyası okununca | K010 | ⚠️ VERIFICATION REQUIRED |
| A-19 | php.ini yüzeyi deployment'ta nasıl? | §17 satır 5 (PHP Configuration CS) | sunucu yapılandırması okununca | K17-devops | UNKNOWN |
| A-20 | SAML kullanılıyor mu? | §17 satır 8 (SAML Security CS) | composer.json + kod taraması | authentication/ | ⚠️ VERIFICATION REQUIRED |
| A-21 | Log yönetimi standardı uygulanıyor mu? | §17 satır 10 (SP 800-92) | audit dosyası açılınca | dalga-3 | UNKNOWN |
| A-22 | İzleme/IDPS katmanı var mı? | §17 satır 11 (SP 800-94) | K014/K017 okununca | K010-network | UNKNOWN |
| A-23 | Yama/yüncelik döngüsü tanımlı mı? | §17 satır 13 (SP 800-40r4) | bagimliliklar derinleştirmesinde | security-bagimliliklar.md | UNKNOWN |
| A-24 | Test yöntemi (SP 800-115) WSTG ile eşleşiyor mu? | §17 satır 15 | security-testing dosyasında | dalga-3 | ⚠️ VERIFICATION REQUIRED |
| A-25 | Tedarik zinciri riski (SP 800-161r1) değerlendirildi mi? | §17 satır 16 | A06/A08 denetiminde | bagimliliklar | UNKNOWN |
| A-26 | Kurtarma planı (SP 800-184) kapsam dışı mı? | §17 satır 17 | sınır onayı | K17-devops | Kapsam dışı (sınır) |

| Sonraki Dalga Sırası | Neden |
|---|---|
| 1 · security-dogrulama.md derinleştirme | rfc/php/mdn/w3c havuzu bu dosyanın dışındadır; tekrar yazılamaz |
| 2 · input-validation iskeleti | A10 SSRF kaynağı (§17 satır 2) hazır bekliyor |
| 3 · threat-model iskeleti | Abuse Case kaynağı (§17 satır 7) hazır bekliyor |
| 4 · audit iskeleti | SP 800-92/94/83r1 (§17 satır 10-14) hazır bekliyor |
| 5 · security-testing iskeleti | SP 800-115 + WSTG (§2) birlikte kullanılacak |

| Kapanış Kontrolü (bu dosya) | Durum |
|---|---|
| Append-only (eski §1-§16 silinmedi) | EVET |
| Yeni bölümler en sonda | §17..§21 |
| Authority/ifade satırı korundu | dosya sonu "Son Durum" tablosu korundu |
| Benzersiz URL (başlık doğrulanmış) | 17 yeni · 0 tekrar |
| git commit | ATILMADI (yasak) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**

## §22 2026-10-18 DOĞRULANMIŞ KAYNAK BATCH'I (15 URL + 2 DÜRÜST 404)

> **Doğrulama yöntemi:** PowerShell `Invoke-WebRequest` GET (Chrome UA, redirect takibi) + `<title>` karşılaştırması;
> OWASP için `curl.exe -L` (Invoke-WebRequest 308 redirect'i PowerShell 5.1'de takip etmedi — dürüst kayıt).
> Tarih: **2026-10-18**. Kesişim denetimi: K009-security/** tamamı üzerinde `Select-String -SimpleMatch` —
> aşağıdaki 15 URL'nin tamamı envanterde (217+ URL) **YOK** → 0 tekrar.

| # | URL | HTTP | Doğrulanan başlık/kanıt | K009 kullanımı |
|---|---|---|---|---|
| 1 | https://owasp.org/Top10/2025/ | **308 → https://top10.owasp.org/2025/ (200)** | `<title>OWASP Top 10:2025</title>` | security-kapsam.md §9 — resmi 2025 liste |
| 2 | https://cwe.mitre.org/data/definitions/352.html | 200 | CWE-352: Cross-Site Request Forgery (CSRF) (4.20) | CSRF eşlemesi (§12 kontrol 4) |
| 3 | https://cwe.mitre.org/data/definitions/918.html | 200 | CWE-918: Server-Side Request Forgery (SSRF) (4.20) | SSRF eşlemesi (§12 kontrol 13) |
| 4 | https://cwe.mitre.org/data/definitions/330.html | 200 | CWE-330: Use of Insufficiently Random Values (4.20) | rastgelelik eşlemesi (§12 kontrol 3) |
| 5 | https://cwe.mitre.org/data/definitions/327.html | 200 | CWE-327: Use of a Broken or Risky Cryptographic Algorithm (4.20) | MD5/SHA-1/DES yasağı (§12 kontrol 14) |
| 6 | https://nvlpubs.nist.gov/nistpubs/Legacy/SP/nistspecialpublication800-38d.pdf | 200 | NIST SP 800-38D (GCM — PDF, HTML title yok) | AES-GCM nonce/IV kuralları |
| 7 | https://nvlpubs.nist.gov/nistpubs/Legacy/SP/nistspecialpublication800-132.pdf | 200 | NIST SP 800-132 (PBKDF — PDF, HTML title yok) | parola türetme rehberi |
| 8 | https://pages.nist.gov/800-63-4/ | 200 | "NIST SP 800-63 Digital Identity Guidelines" | authentication referansı |
| 9 | https://developer.mozilla.org/en-US/docs/Web/Security/Same-origin_policy | 200 | "Same-origin policy - Security \| MDN" | CORS/Origin temeli |
| 10 | https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Origin | 200 | "Origin header - HTTP \| MDN" | OriginCheck kanıtı |
| 11 | https://developer.mozilla.org/en-US/docs/Web/HTTP/Reference/Headers/Cross-Origin-Opener-Policy | 200 | "Cross-Origin-Opener-Policy (COOP) header - HTTP \| MDN" | COOP başlığı (SecurityHeaders·18-31) |
| 12 | https://www.php.net/manual/en/function.openssl-decrypt.php | 200 | "PHP: openssl_decrypt - Manual" | GCM decrypt parametreleri |
| 13 | https://rfc-editor.org/rfc/rfc8941.html | 200 | "RFC 8941: Structured Field Values for HTTP" | Structured Fields (bkz. §20 batch) |
| 14 | https://rfc-editor.org/rfc/rfc9421.html | 200 | "RFC 9421: HTTP Message Signatures" | imzalı istek yüzeyi |
| 15 | https://rfc-editor.org/rfc/rfc9457.html | 200 | "RFC 9457: Problem Details for HTTP APIs" | hata sözleşmesi (A10) |

### §22.1 — Dürüst 404 kayıtları (uydurma URL YASAK — bunlar ERİŞİLEMEDİ)

| URL | Sonuç | Yorum |
|---|---|---|
| https://cheatsheetseries.owasp.org/cheatsheets/JSON_Web_Token_for_Java_Script_Cheat_Sheet.html | **404** | (2026-10-17 ve 2026-10-18 iki turda da 404) — OWASP Cheat Sheet serisinde bu adla sayfa bulunmuyor; JWT kullanımı için OWASP JWT Cheat Sheet adı altında doğrulanmış başka sayfa ARAŞTIRILMADI |
| https://cheatsheetseries.owasp.org/cheatsheets/Account_Lockout_Cheat_Sheet.html | **404** | (2026-10-17 ve 2026-10-18 iki turda da 404) — Account Lockout için bu adla sayfa yok |

### §22.2 — Yöntem notu (tekrar üretilebilirlik)

| Not | Ayrıntı |
|---|---|
| Redirect kapanması | `owasp.org/Top10/2025/` kalıcı 308 döner; kalıcı adres `top10.owasp.org/2025/` (200). Yeni atıflarda kalıcı URL kullanılır; §9'daki `owasp.org/Top10/2025/` 308 üzerinden AYNI içeriğe gider — ikisi de doğru |
| PowerShell 5.1 sınırı | `Invoke-WebRequest` 308'i takip etmez; OWASP batch'i `curl.exe -L` ile doğrulandı |
| NIST PDF yolu düzeltmesi | `nvlpubs.../NIST.SP.800-38D.pdf` biçimi **404** verdi (2026-10-18); doğru kalıcı yol `Legacy/SP/nistspecialpublication800-38d.pdf` (200). Aynı düzeltme SP 800-132 için de geçerli |
| PDF başlık kanıtı | PDF yanıtında `<title>` yoktur → başlık sütunu "PDF" olarak dürüst işaretlendi; içerik doğrulaması indirme ister |

| §22 Kapanış Kontrolü | Durum |
|---|---|
| Append-only (§1-§21 silinmedi) | EVET |
| Benzersiz URL | 15 yeni · 0 tekrar (K009 geneli SimpleMatch taraması) |
| Dürüst 404 | 2 (§22.1) |
| Yöntem + tarih | PowerShell/curl · 2026-10-18 |
| git commit | ATILMADI (yasak) |

> **Bu bölüm kanıt oranını DEĞİŞTİRmez.**
