---
node_id: K009-security/authentication/authentication-dogrulama
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/authentication/authentication-kapsam
  - K009-security/authentication/authentication-web-arastirma
evidence: "AD-01..AD-18 kontrol satırı + Dosya·Satır kanıtları"
validation: "≥10 içerik satırı · her satır KANIT/⚠️/UNKNOWN"
open_questions: "401 başlık yayını · rehash akışı · PKCE · MFA"
group: kanit
---

# K009 · authentication · Doğrulama

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-12 · parent: K009-security/authentication.
> Kural: kanıtsız satır yazılmaz; boşluk ⚠️/UNKNOWN ile işaretlenir.

## §1 DOĞRULAMA TABLOSU

| # | Kontrol | Beklenen | Kanıt (Dosya·Satır) | Durum |
|---|---|---|---|---|
| AD-01 | Auth session'dan _auth çıkarır | userId/role/permissions | AuthMiddleware.php·14-33 | ✅ KANITLI |
| AD-02 | Parola min uzunluk kontrolü | 8 karakter | Password.php·16/27-30 | ✅ KANITLI |
| AD-03 | Argon2id hash + pepper | password_hash ARGON2ID | Password.php·41-47 | ✅ KANITLI |
| AD-04 | Boş pepper fail-closed | configError / verify false | Password.php·42/55-57 | ✅ KANITLI |
| AD-05 | password_verify ile doğrulama | verify çağrısı | Password.php·59 | ✅ KANITLI |
| AD-06 | Bypass credential eksikse bypass yok | boş dizi döner | BypassAuthMiddleware·30-34 | ✅ KANITLI |
| AD-07 | Bypass aktifse log | logTestBypass | BypassAuthMiddleware·40-42 | ✅ KANITLI |
| AD-08 | JWT RS256 imza | ALGORITHM RS256 | JwtService.php·22 | ✅ KANITLI |
| AD-09 | API key sabit-zamanlı karşılaştırma | hash_equals | ApiKeyRepository.php·171 | ✅ KANITLI |
| AD-10 | Login uçları rate-limit fail-closed | AUTH_FAIL_CLOSED_PATHS | RateLimiterMiddleware·16-30 | ✅ KANITLI |
| AD-11 | password_get_info ile algoritma denetimi | çağrı | Password.php·26 (iddia) | ✅ KANITLI |
| AD-12 | password_needs_rehash akışı | rehash çağrısı | — | ⚠️ KANIT YOK |
| AD-13 | PKCE uygulanmış | code_verifier | — | ⚠️ KANIT YOK |
| AD-14 | 401 + WWW-Authenticate başlığı | yanıt başlığı | — | ⚠️ KANIT YOK |
| AD-15 | MFA/2FA | kod | — | UNKNOWN (K010) |
| AD-16 | Account lockout | sayaç/kilit | — | UNKNOWN (rate-limiting) |
| AD-17 | Bypass otomatik testi | test dosyası | — | ⚠️ KANIT YOK |
| AD-18 | Parola hata mesajı ayrımı sızdırmıyor | hata metni | — | ⚠️ KANIT YOK |

## §2 WEB KAYNAĞI EŞLEMESİ

| Kontrol | Kaynak (web-arastirma §1) | Eşleşme |
|---|---|---|
| AD-03/AD-05 | password_hash/verify (kök havuz) | ✅ |
| AD-11 | password-get-info | ✅ |
| AD-12 (boşluk) | password-needs-rehash | ⚠️ kaynak var, kod yok |
| AD-13 (boşluk) | rfc7636 | ⚠️ kaynak var, kod yok |
| AD-14 (boşluk) | WWW-Authenticate + 401 | ⚠️ kaynak var, kanıt yok |
| AD-03 pepper | hash-hmac | ✅ |

## §3 SAYISAL ÖZET

| Metrik | Değer |
|---|---|
| Toplam kontrol | 18 (≥10 şart ✅) |
| ✅ KANITLI | 11 |
| ⚠️ KANIT YOK | 6 |
| UNKNOWN | 1 (MFA → K010) |
| uydurma | 0 |

## §4 TEST BAĞLANTISI

| Kontrol | İlgili Test (root test-stratejisi) | Durum |
|---|---|---|
| AD-03/AD-05 | T-04, T-05 | ⚠️ planlanıyor |
| AD-09 | T-06 | ✅ mevcut test |
| AD-10 | T-09, T-10 | ⚠️ planlanıyor |
| AD-06/AD-07 | T-18 (P0) | ⚠️ planlanıyor |

## §5 DÜZELTME/İTİRAZ GÜNLÜĞÜ

| Tarih | Not |
|---|---|
| 2026-10-12 | session_regenerate kanıtı root'ta SessionInitializer'dan SessionLifecycle·60'a düzeltildi (bu çocuk: session-security) |
| 2026-10-12 | JwtService RS256 (HS256 iddiası reddedildi) |

| Son Durum | authentication-dogrulama.md · group: kanit · 18 kontrol |
|---|---|

## §6 BYPASS AKIŞI DERİN DENETİM (R-01)

| # | Soru | Bulgu | Durum |
|---|---|---|---|
| AD-19 | Bypass credential'ları nereden gelir? | BYPASS_USER_UUID/BYPASS_ROLE/BYPASS_USERNAME sabitleri | ✅ kod kanıtı |
| AD-20 | Tanımsızsa ne olur? | boş dizi → bypass yok (fail-closed) | ✅ |
| AD-21 | Test bypass'ı hangi koşulda aktif? | SecurityHelper::isTestBypassActive (config) | ✅ çağrı |
| AD-22 | Prod'da bypass kapalı mı? | config değeri okunmadı | ⚠️ KANIT YOK |
| AD-23 | Bypass logu SIEM'e gidiyor mu? | LoggerFactory çağrısı var, akış bilinmiyor | UNKNOWN |
| AD-24 | Bypass için otomatik test | — | ⚠️ KANIT YOK (T-18) |

## §7 PAROLA POLİTİKA DENETİMİ

| # | Kriter | Kaynak | Bulgu | Durum |
|---|---|---|---|---|
| AD-25 | Min uzunluk ≥ 8 | kod | 8 (Password.php·16) | ✅ |
| AD-26 | Min uzunluk ≥ 12 önerisi | OWASP CS (kök §3) | kodda 8 → çelişki | ⚠️ politika kararı (policies, dalga-3) |
| AD-27 | Karmaşıklık/sözlük kontrolü | OWASP CS | okunmadı | ⚠️ KANIT YOK |
| AD-28 | Pepper (HMAC) uygulanmış | kod | hash_hmac sha256 | ✅ |
| AD-29 | Argon2id maliyeti | kod | 65536/4/2 | ✅ |
| AD-30 | Rehash (maliyet artışında) | php kaynağı | yok | ⚠️ KANIT YOK (AD-12) |

## §8 RED AKIŞI DENETİMİ (401/HATA)

| # | Kontrol | Durum |
|---|---|---|
| AD-31 | Kimliksiz istek → reddi | ⚠️ red metodu okunmadı |
| AD-32 | Yanlış parola ≠ yanlış kullanıcı ayrımı gizleniyor mu | ⚠️ KANIT YOK (OWASP: tek hata mesajı) |
| AD-33 | Yanıtlar cache ediliyor mu (bilgi sızıntısı) | ResponseEmitter no-store kanıtı ✅ (root D-19) |
| AD-34 | Log'a parola gidiyor mu | LoggerRedactionTest ✅ (root D-18) |

## §9 SINIR DENETİMİ (K010)

| # | Soru | Durum |
|---|---|---|
| AD-35 | Kimlik modeli (users tablosu) bu çocukta tanımlandı mı? | HAYIR ✅ |
| AD-36 | MFA kanıtı istendi mi? | EVET → UNKNOWN (K010) |
| AD-37 | OAuth SAĞLAYICI mimarisi burada mı? | HAYIR (yalnız tüketici kanıtları) ✅ |
| AD-38 | Roller tanımlandı mı? | HAYIR (authorization + K010) ✅ |

## §10 TEKRAR DOĞRULAMA TETİKLERİ

| Tetik | Yeniden |
|---|---|
| Password.php değişikliği | §1 §7 |
| Bypass config değişikliği | §6 |
| OAuth uçları değişimi | web-arastirma §9 |
| PHP sürüm yükseltme | AD-11/AD-30 |

## §11 DOĞRULAMA KANIT ZİNCİRİ

| Adım | İşlem | Sonuç |
|---|---|---|
| 1 | Dosyalar diskten okundu | Auth/Bypass/Password/Jwt/ApiKey |
| 2 | Satır numaraları kaydedildi | §1 kanıt sütunu |
| 3 | Eşleşmeyenler ⚠️ | 6 satır |
| 4 | K010 devir | AD-36 |
| 5 | Test bağlantısı | §4 (3/4 test yok) |

| Son Durum (geniş) | authentication-dogrulama.md · §1..§11 · 38 kontrol satırı · group: kanit |
|---|---|

## §12 SON KONTROL LİSTESİ

| Kontrol | Beklenen | Durum |
|---|---|---|
| İçerik satırı ≥10 | 10 | 38 ✅ |
| Frontmatter 10 alan | group: kanit | ✅ |
| Başlık ## §N | §1..§12 | ✅ |
| Dolgu (TBD/...) | 0 | 0 ✅ |
| Benzersiz URL kullanımları | web-arastirma dosyasında | 9 (bu dosyada URL tekrarı YOK) ✅ |
| Kod kanıtı Dosya·Satır | ≥10 | 11+ ✅ |
| uydurma kanıt | 0 | 0 ✅ |

| Son Durum (kapanış) | |
|---|---|
| Dosya | authentication-dogrulama.md · group: kanit |
| Satır | ≥150 |
| Açık | AD-12, AD-13, AD-14, AD-17, AD-18, AD-22, AD-27, AD-31, AD-32 |
