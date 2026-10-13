---
node_id: K009-security/authentication/authentication-kapsam
node_type: module
status: verify-required
parent_id: K009-security
children: []
depends_on:
  - K009-security/authentication
  - K009-security/security-kapsam
evidence: "Kod envanteri: AuthMiddleware·7 · BypassAuthMiddleware·9 · Password.php·26/38-59 · JwtService·22/51"
validation: "Kapsam içi/dışı + sınır matrisi + kabul kriterleri"
open_questions: "MFA kapsamı K010'a devredildi · bypass test kapsamı belirsiz"
group: mimari
---

# K009 · authentication · Kapsam

> Authority: Bayram Ali / Vault Steward · Tarih: 2026-10-12 · parent: K009-security/authentication.

## §1 KAPSAM İÇİ

| # | Öğe | Kanıt (Dosya·Satır) | Not |
|---|---|---|---|
| 1 | Session'dan _auth çıkarımı (userId/role/permissions) | AuthMiddleware.php·7-35 | RBAC girdisi |
| 2 | Parola oluşturma kuralları (min 8) | Password.php·16/26-33 | ValidationException |
| 3 | Parola hash'leme (pepper HMAC-SHA256 + Argon2id) | Password.php·38-49 | memory 65536/time 4/threads 2 |
| 4 | Parola doğrulama (password_verify) | Password.php·54-59 | boş pepper → false |
| 5 | Bypass credential okuma (fail-closed) | BypassAuthMiddleware.php·19-37 | tanımsızsa bypass yok |
| 6 | Bypass aktifse loglama | BypassAuthMiddleware (logTestBypass) | gözlemlenebilirlik |
| 7 | Bearer JWT doğrulama (RS256, iss/aud fail-closed) | JwtService.php·22/51 | API istemcileri |
| 8 | API key doğrulama (hash_equals) | ApiKeyRepository.php·171 | servis anahtarları |
| 9 | Rate limit login yüzeyi (fail-closed) | RateLimiterMiddleware·16-30 | /login vb. |

## §2 KAPSAM DIŞI

| Öğe | Neden | Giden Yer |
|---|---|---|
| Kullanıcı hesabı modeli (tablo/alan) | kimlik tanımı | K010-identitiy |
| Kayıt/şifre sıfırlama akış tasarımı | kimlik deneyimi | K010-identitiy |
| MFA/2FA tasarımı ve kanıtı | kimlik faktörleri | K010-identitiy (⚠️ kanıt yok) |
| OAuth2/OIDC sağlayıcı MİMARİSİ | kimlik sağlayıcı | K010-identitiy |
| Rol TANIMI (hangi rol, neye sahip) | yetki modeli | authorization + K010 |
| Oturum çerez/timeout yönetimi | oturum yaşam döngüsü | session-security |
| Anti-automation (rate limit politikası) | ayrı politika | rate-limiting |

## §3 SINIR MATRİSİ

| Soru | Cevap | Sahip |
|---|---|---|
| "Kim bu?" | session/token/key doğrulama | authentication (bu çocuk) |
| "Kim olduğu ne yapabilir?" | yetki kararı | authorization |
| "Oturum ne kadar sürer?" | timeout/rotation | session-security |
| "Hesap nasıl oluşur?" | kayıt/model | K010-identitiy |
| "Kaç deneme?" | kota | rate-limiting |

## §4 BAĞIMLILIKLAR

| Bağ | Tür | Kanıt |
|---|---|---|
| Session (MM_UserID..) | veri | AuthMiddleware·18-22 |
| Password (pepper APP_PEPPER) | yapı | Password.php·41-44 |
| JwtService (private.pem) | yapı | JwtService·25-29 |
| CacheRateLimiter | koruma | RateLimiterMiddleware |
| Logger (securityEvent) | gözlem | Csrf/Bypass log kanıtları |

## §5 RİSK ODAĞI

| Risk | Kaynak | Durum |
|---|---|---|
| R-01 bypass whitelist kötü yapılandırma | riskler.md | AÇIK |
| R-12 brute-force | riskler.md | AZALTILMIŞ (fail-closed) |
| Parola Politikası gücü (min 8) | Password.php·16 | ⚠️ politika yetersizliği tartışmalı → policies (dalga-3) |

## §6 KABUL KRİTERLERİ

| # | Kriter | Durum |
|---|---|---|
| 1 | Argon2id kanıtı | ✅ |
| 2 | fail-closed doğrulama (boş pepper/credential) | ✅ |
| 3 | bypass loglanıyor | ✅ |
| 4 | MFA kanıtı | ⚠️ YOK (K010) |
| 5 | bypass için otomatik test | ⚠️ YOK |

| Son Durum | authentication-kapsam · group: mimari · parent K009-security |
|---|---|

## §7 GİRDİ/KUTU YÜZEYLERİ

| Yüzey | Girdi | Doğrulama | Kanıt |
|---|---|---|---|
| Login formu | username/password | server-side verify | (akış K010) · Password verify kanıtı |
| Bearer header | JWT | RS256 + iss/aud/exp | JwtService·22 |
| API key header | ham anahtar | prefix → hash_equals | ApiKeyRepository·171 |
| Session cookie | PHP session | session aktifliği + _auth | AuthMiddleware·14-22 |
| Bypass header/config | BYPASS_* sabitleri | tamamı tanımlı değilse bypass yok | BypassAuthMiddleware·19-37 |

| Girdi Kuralı | |
|---|---|
| Ham parola asla log/DB'ye girmez | Password VO dokümanı (Password.php·9) |
| Kanıt yoksa kimlik yok | AuthMiddleware: userId yoksa _auth boş → Permission ilgilenmez |

## §8 ÇIKTI SÖZLEŞMELERİ

| Çıktı | Tüketici | İçerik |
|---|---|---|
| $request['_auth'] | PermissionMiddleware, Controller | userId, role, username, permissions |
| password_hash değeri | DB (users) | Argon2id hash (pepper uygulanmış) |
| JWT (RS256) | API istemcileri | claims + jti |
| securityEvent logları | audit (dalga-3) | bypass/csrf/authz olayları |

## §9 REDDEDİLEN KAPSAM SEÇENEKLERİ

| Seçenek | Neden Red |
|---|---|
| Parolayı taşıyıcı olarak HMAC (eski API) | OWASP: hash sakla, taşıyıcı değil |
| Client-side parola kontrolü | güvenilmez istemci |
| Bypass header'ı istemciden gelsin | R-01 → bypass config'den sabit okunur |
| JWT'yi session yerine her yerde kullanmak | hybrid tasarım (JwtService dokümanı): tarayıcı = session |

## §10 KAPSAM — DOĞRULAMA BAĞLANTISI

| Kapsam Kalemi | Doğrulama Satırı |
|---|---|
| §1-1 (AuthMiddleware) | AD-01 |
| §1-3/4 (Password) | AD-02, AD-03 |
| §1-5 (bypass fail-closed) | AD-04 |
| §1-7 (JWT) | AD-05 |
| §1-8 (API key) | AD-06 |
| §2 MFA (dışarıda) | AD-07 ⚠️ KANIT YOK |

## §11 SINIR KONTROL LİSTESİ

| Kontrol | Durum |
|---|---|
| K010-identitiy'e kimlik modeli yazıldı mı? | HAYIR ✅ |
| K010'dan veri okundu mu (userId/role)? | Session üzerinden EVET (kullanıcı verisi) → ⚠️ sınır notu |
| Oturum politikası bu çocukta mı? | HAYIR (session-security) ✅ |
| Yetki TANIMI burada mı? | HAYIR (authorization) ✅ |

| Son Durum (kapsam · geniş) | authentication-kapsam.md · 6 bölüm daha · group: mimari |
|---|---|
