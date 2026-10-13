---
node_id: K009-security/security-riskler
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/security-kapsam
  - K009-security/security-kod-ornekleri
evidence: "R-01..R-14 risk kaydı (STRIDE) + kod kanıtı + OWASP eşlemesi"
validation: "Her risk: tehdit, varlık, etki, azaltma, durum"
open_questions: "Bypass whitelist kapsamı; localhost allowlist; SSRF yüzeyi"
group: kanit
---

# K009 · Güvenlik · Riskler (Kök)

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-12 · Rol: K009 STRIDE risk defteri.
> Risk kuralı: her satır tehdit + kanıt (Dosya·Satır veya KANIT YOK) + azaltma + durum.

## §1 STRIDE ÖZETİ

| Kategori | Risk Sayısı | Kritik/High | Kayıt |
|---|---|---|---|
| Spoofing (S) | 3 | 1 | R-01, R-02, R-03 |
| Tampering (T) | 3 | 1 | R-04, R-05, R-06 |
| Repudiation (R) | 1 | 0 | R-07 |
| Information Disclosure (I) | 4 | 1 | R-08, R-09, R-10, R-11 |
| Denial of Service (D) | 2 | 0 | R-12, R-13 |
| Elevation of Privilege (E) | 1 | 1 | R-14 |

## §2 RİSK KAYITLARI

| # | Tehdit (STRIDE) | Varlık/Yüzey | Kanıt | Etki | Azaltma | Durum |
|---|---|---|---|---|---|---|
| R-01 | Spoofing: BypassAuth whitelist yanlış URL ile auth atlama | BypassAuthMiddleware whitelist | BypassAuthMiddleware.php·9 | High | Whitelist denetimi + kod inceleme; ayrı test | AÇIK (test yok) |
| R-02 | Spoofing: JWT sahtecelik | JwtService | JwtService.php·15-30 (RS256) | High | RS256 + iss/aud fail-closed | AZALTILMIŞ |
| R-03 | Spoofing: API anahtarı tahmini | ApiKeyRepository | random_bytes·304, hash_equals·171 | High | 16+ bayt entropi + SHA-256 saklama | AZALTILMIŞ |
| R-04 | Tampering: CSRF ile state-changing istek | CsrfMiddleware | CsrfMiddleware.php·20-50 | High | Double-submit + hash_equals + bypass kalktı | AZALTILMIŞ |
| R-05 | Tampering: Return URL manipülasyonu (open redirect) | ReturnUrlPolicy | ReturnUrlPolicy.php·5-45 | Medium | Allowlist + kontrol karakteri guard | AZALTILMIŞ |
| R-06 | Tampering: Girdi enjeksiyonu (SQL/command) | ValidationMiddleware + ORM yasak | ValidationMiddleware.php·15 · ADR-002 | High | Parametreli PDO + doğrulama | KISMİ (şema kanıtı dalga-3) |
| R-07 | Repudiation: güvenlik olayı loglanmama | Logger securityEvent | LoggerRedactionTest.php·132 · Csrf fail() log | Medium | Redaction testi + olay logları | KISMİ (SIEM yok → D-32) |
| R-08 | Info Disclosure: log/yanıt sızıntısı (secret/parola) | Logger + Response | LoggerRedactionTest.php·132 | High | Redaction testi | AZALTILMIŞ |
| R-09 | Info Disclosure: cookie hırsızlığı | Session çerezi | SessionInitializer.php·44-46 | High | HttpOnly + SameSite=Lax; Secure yalnız HTTPS koşullu | AZALTILMIŞ (koşullu secure → HSTS ile) |
| R-10 | Info Disclosure: header/referrer sızıntısı | Referrer-Policy | SecurityHeadersMiddleware·31 | Medium | strict-origin-when-cross-origin | AZALTILMIŞ |
| R-11 | Info Disclosure: veritabanı anahtar sızıntısı | API key deposu | ApiKeyRepository·12-30 | High | Hash + prefix; ham asla DB'de | AZALTILMIŞ |
| R-12 | DoS: brute-force / credential stuffing | Login/registro uçları | RateLimiterMiddleware·16-30 | High | 60/60 + fail-closed auth yolları | AZALTILMIŞ |
| R-13 | DoS: rate-limit cache çöküşü → fail-open | Cache bağımlılığı | RateLimiterMiddleware fail-closed listesi | Medium | Auth: fail-closed; genel: fail-open (ADR-013) | KABUL (bilinçli) |
| R-14 | Elevation: CSRF bypass / rol karşılaştırma zaafı | Csrf bypass + Permission | CsrfMiddleware·12-17 · PermissionMiddleware·15-45 | High | Bypass kaldırıldı; strict in_array | AZALTILMIŞ |

## §3 KANIT YOK RİSKLERİ (DOĞRULANMAMIŞ)

| # | İddia | Beklenen Kanıt | Durum |
|---|---|---|---|
| R-15 | HSTS preload ile MITM yüzeyi kapatılıyor | hstspreload.org kaydı | ⚠️ VERIFICATION REQUIRED |
| R-16 | AES-256-GCM ile veri şifreleniyor | openssl/aes kullanım kanıtı | ⚠️ VERIFICATION REQUIRED |
| R-17 | JWKS/anahtar rotasyonu ile uzun dönem imza güvenliği | key rotation kodu | ⚠️ VERIFICATION REQUIRED |
| R-18 | SSRF yüzeyi (A10) dış çağrı denetimiyle kapatılıyor | outbound URL denetimi | UNKNOWN |
| R-19 | Dependency CVE'leri izleniyor | composer audit raporu | UNKNOWN |

## §4 OWASP TOP 10:2025 — RİSK EŞLEŞMESİ

| OWASP | Risk No | Öncelik |
|---|---|---|
| A01 Broken Access Control | R-01, R-05, R-14 | P0 |
| A02 Cryptographic Failures | R-02, R-11, R-16, R-17 | P0 |
| A03 Injection | R-06 | P0 |
| A04 Insecure Design | R-01 (bypass tasarımı), R-18 | P1 |
| A05 Misconfiguration | R-15 (HSTS preload) | P1 |
| A06 Vulnerable Components | R-19 | P1 |
| A07 Auth Failures | R-12, R-09 | P0 |
| A08 Integrity Failures | R-03, R-04 | P0 |
| A09 Logging Failures | R-07 | P2 |
| A10 SSRF | R-18 | P1 |

## §5 AZALTMA — DOSYA DAĞILIMI

| Azaltma Sahibi | Riskler | Giden Dosya |
|---|---|---|
| authentication | R-01, R-12 | children/authentication |
| authorization | R-14 | children/authorization |
| session-security | R-04, R-05, R-09, R-10 | children/session-security |
| encryption | R-02, R-16, R-17 | children/encryption |
| secrets | R-03, R-08, R-11 | children/secrets |
| rate-limiting | R-12, R-13 | children/rate-limiting |
| input-validation (dalga-3) | R-06, R-18 | .ai/architecture/K009-security/input-validation |
| audit (dalga-3) | R-07, R-32 (SIEM) | .ai/architecture/K009-security/audit |

## §6 ÖNCELİKLENDİRME (P0/P1/P2)

| Öncelik | Riskler | Gerekçe |
|---|---|---|
| P0 (hemen) | R-01 | AÇIK durumlu; bypass akışında kanıt/azaltma eksik |
| P1 (yakın) | R-06, R-15, R-16, R-17, R-18, R-19 | doğrulanmamış / kısmi |
| P2 (izleme) | R-07 (SIEM), R-13 (kabul) | bilinçli kabul veya dalga-3 |

| Son Durum | |
|---|---|
| Dosya | security-riskler.md · group: kanit |
| Risk | 14 kayıt + 5 kanıtsız (§3) |
| Açık (P0) | R-01 · R-09 AZALTILMIŞ olarak güncellendi (2026-10-12) |

## §7 RİSK ÖLÇÜM YÖNTEMİ

| Ölçüt | Değerler | Bu Dosyada Kullanım |
|---|---|---|
| Etki | Low / Medium / High | §2 Etki sütunu |
| Olasılık | Tespit edildi / Kısmi / Kanıt yok | Kanıt sütunundan türetilir |
| Öncelik | P0 / P1 / P2 | Etki + durum (AÇIK → P0) |
| Azaltma durumu | AZALTILMIŞ / KISMİ / AÇIK / KABUL | Durum sütunu |

| Ölçüm Kuralı | |
|---|---|
| Kanıt yok → durum asla AZALTILMIŞ yapılmaz | §3 listesinde ⚠️/UNKNOWN |
| Kabul edilen risk | Gerekçesi yazılır (R-13 ADR-013) |
| P0 | Üst karar gerektirebilir (§6) |

## §8 TEHDİT MODELİ BAĞLANTISI (DOSYA-3)

| Tehdit Modeli Girdisi | Bu Dosyadan | Not |
|---|---|---|
| Varlıklar | Oturum, API key, parola hash, private.pem, loglar | §2 |
| Saldırı yüzeyi | Auth bypass, CSRF, open redirect, brute-force, injection | §2/§4 |
| STRIDE sınıflandırması | §1 tablosu | threat-model.md'ye devredildi |
| ADR-097 tasarım kararları | security-kararlar §3 | taslak ADR |

## §9 TARAMA TETİKLEYİCİLERİ

| Tetikleyici | Yeniden Değerlendirilecek |
|---|---|
| Yeni bypass/whitelist eklenmesi | R-01, R-14 |
| Cookie bayrak değişikliği | R-09 |
| JWT algoritma/anahtar değişikliği | R-02, R-17 |
| Rate limit politika değişikliği | R-12, R-13 |
| Yeni outbound çağrı (dış API) | R-18 |
| composer bağımlılık değişimi | R-19 |
| Log formatı değişimi | R-08, R-07 |

| Son Durum (risk · geniş) | |
|---|---|
| Kayıt | 14 kanıtlı risk + 5 kanıtsız iddia |
| P0 | R-01 (bypass), R-09 (cookie kanıtı) |
| Bağ | kapsam + kod-ornekleri; dağılım §5 |
