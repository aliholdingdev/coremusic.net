---
node_id: K012-services/user-service
node_type: module
role: surucu
status: draft
parent_id: K012-services/user-service
children: []
depends_on:
  - "user-service/user-service-anayasa.md"
  - "user-service/index.md"
evidence:
  - "S1 — auth.coremusic.net/include/Service/AuthService.php 349 satır (loginWithRequest:57, registerWithRequest:136, login:209, register:215, logout:221, isAuthenticated:235, getCurrentUser:240, requestPasswordReset:254, resetPassword:282, validateSessionKey:310)"
  - "S1 — auth.coremusic.net/include/Service/SessionManager.php 180 satır (setAuthUser:15, setRegisteredUser:33, getUserId:46, isAuthenticated:55, destroy:60, regenerateId:79, isIdleExpired:133, touch:139, rotateIfNeeded:144, clearDisplayCookies:161, all:176)"
  - "auth.coremusic.net/AGENTS.md §4 — Argon2id kuralı (kural seviyesi) + §2 envanter"
validation:
  - "§2–§14 metot envanterleri 2026-10-13 read — satır numaraları birebir"
  - "§5 giriş zinciri AuthService.php:57–131 ile birebir"
open_questions:
  - "IUserRepository/IRateLimiter implementasyonu okunmadı → ⚠️ VERIFICATION REQUIRED"
  - "Rate limit sayacının fiziksel deposu (APCu/dosya/DB) → UNKNOWN"
  - "Password VO içi hash algoritması okunmadı → kural≠kanıt (§16)"
date: 2026-10-13
updated: 2026-10-13
version: 1.0.0
authority: "SSOT: auth.coremusic.net/include/Service/ (S1, 2026-10-13 read)"
group: mimari
---

# user-service — Sürücü (Driver)

> **Bu dosya ne yapar:** user-service'in gerçek sürücülerini — `AuthService`
> (iş mantığı) ve `SessionManager` (oturum yazımı) — metot metot tarif eder:
> hangi metot ne zaman çağrılır, hangi sabitle çalışır, hangi kata yazar.
> Anayasa "neyin doğru olduğunu" söyler (bkz `user-service-anayasa.md`);
> bu dosya "nasıl döndüğünü" söyler.

## §1 ELI15 — Sürücü Katmanı Ne Yapar?

| Katman | Sınıf | Dosya | Görev |
|---|---|---|---|
| Service | `AuthService` | `auth.coremusic.net/include/Service/AuthService.php` (349 satır) | Giriş, kayıt, logout, parola sıfırlama, auth_key doğrulama iş mantığı |
| Service | `SessionManager` | `auth.coremusic.net/include/Service/SessionManager.php` (180 satır) | `$_SESSION` yazım/okuma/yaşam döngüsü sarmalayıcısı |
| Kontrat | `IAuthService` | okunmadı | `AuthService`'in uyguladığı arayüz (`implements IAuthService`, satır 28) |
| Kontrat | `ISessionManager` | okunmadı | `SessionManager`'ın uyguladığı arayüz (satır 8) |

**Çalışma ilkesi:** `AuthService` asla `$_SESSION`'a doğrudan yazmaz — her zaman
`$this->session` üzerinden (`setAuthUser`, `setRegisteredUser`, `destroy`).
`SessionManager` iş mantığı bilmez; yalnız oturum anahtarını yazar/siler.
Bu iki sürücü arasındaki kapı **`ISessionManager` kontratıdır**.

## §2 AuthService — Metot Envanteri (S1)

| # | Metot | Satır | Dönüş | Çağrıdığı sürücü |
|---|---|---|---|---|
| 1 | `__construct` | 45–50 | — | 4 enjeksiyon: `IUserRepository`, `ISessionManager`, `IRateLimiter`, `string $pepper` |
| 2 | `loginWithRequest(LoginRequest)` | 57–131 | `AuthResponse` | rate limit ×2, repository ×3, session ×1 |
| 3 | `registerWithRequest(RegisterRequest)` | 136–205 | `AuthResponse` | rate limit ×4, repository ×4, session ×2 |
| 4 | `login(string,string,string,string)` | 209–213 | `array` | `loginWithRequest` sarmalayıcısı (eski IAuthService imzası) |
| 5 | `register(array,string,string)` | 215–219 | `array` | `registerWithRequest` sarmalayıcısı |
| 6 | `logout()` | 221–233 | `void` | repository ×2 (revoke), session ×2 (destroy, clearDisplayCookies) |
| 7 | `isAuthenticated()` | 235–238 | `bool` | `session->isAuthenticated()` passthrough |
| 8 | `getCurrentUser()` | 240–252 | `?array` | session getUserId + repository findByIdHex |
| 9 | `requestPasswordReset(...)` | 254–280 | `array` | rate limit ×3, repository ×2 |
| 10 | `resetPassword(string,string)` | 282–308 | `array` | repository ×3, `Password::create/hashWithPepper` |
| 11 | `validateSessionKey(string)` | 310–348 | `array` | repository `findValidAuthKey` iki koldan (false→true) |

**Sürücü gözlemi:** 11 metodun yalnızca 4'ü oturuma yazar (`loginWithRequest`,
`registerWithRequest`, `logout`, dolaylı `login/register` sarmalayıcılar);
diğer 7'si salt okuma veya kalıcılaşma. Yazma yüzeyi dar = hata yüzeyi dar.

## §3 AuthService — Sabit Envanteri (S1:30–43)

| Sabit | Satır | Değer | Ne yönetir |
|---|---|---|---|
| `MIN_PASSWORD_LENGTH` | 30 | 8 | Kayıt ve sıfırlama parola uzunluk kapısı |
| `MAX_LOGIN_ATTEMPTS` | 31 | 5 | Giriş limiti eşiği (iki sayaçta da aynı) |
| `LOGIN_WINDOW_SECONDS` | 32 | 900 | Giriş penceresi (15 dk) |
| `MAX_REGISTER_ATTEMPTS` | 33 | 3 | Kayıt limiti eşiği |
| `REGISTER_WINDOW_SECONDS` | 34 | 3600 | Kayıt penceresi (1 sa) |
| `AUTH_KEY_TTL` | 35 | 300 | auth_key ömrü (5 dk) |
| `USERNAME_PATTERN` | 36 | `/^[a-zA-Z0-9_]{3,30}$/` | Kullanıcı adı karakter/düzlük sınırı |
| `LOGIN_RATE_KEY_PREFIX` | 37 | `rate_limit:login:` | İki limit anahtarı öneki (IP + id) |
| `REGISTER_RATE_KEY_PREFIX` | 38 | `rate_limit:register:` | Kayıt limiti öneki |
| `PASSWORD_RESET_RATE_KEY_PREFIX` | 39 | `rate_limit:password_reset:` | Sıfırlama limiti öneki |
| `PASSWORD_RESET_MAX_ATTEMPTS` | 40 | 3 | Sıfırlama limiti eşiği |
| `PASSWORD_RESET_WINDOW_SECONDS` | 41 | 3600 | Sıfırlama penceresi (1 sa) |
| `PASSWORD_RESET_TOKEN_TTL` | 43 | 900 | Sıfırlama token ömrü (public const, 15 dk) |

**İki halka farkı (kod kanıtı):** sıfırlamanın token'ı 900 sn (`:43`) ama
sayaç penceresi 3600 sn (`:41`) — token ölürken sayaç hâlâ canlıdır; bu bilinçli
iki halkalı kapan (anayasa §19).

## §4 Giriş Sürücüsü — loginWithRequest 11 Adım (S1:57–131)

| # | Adım | Satır | Duraklama (throw) | Sayaç etkisi |
|---|---|---|---|---|
| 1 | IP anahtarı kur: `rate_limit:login:{clientIp}` | 59 | — | — |
| 2 | Kimlik anahtarı kur: `rate_limit:login:id:sha256(lower(identity))` | 63 | — | — |
| 3 | İki limitten biri dolu mu? | 65–69 | `RateLimitException::loginRateLimited(900)` | okuma |
| 4 | Boş alan kontrolü (identity veya password boş) | 71–73 | `ValidationException::emptyFields()` | — |
| 5 | `findByCredential(identity)` — yoksa | 76–80 | `AuthenticationException::invalidCredentials()` | iki sayaç da `increment` |
| 6 | `User::fromRow` + `isBanned()` | 82–86 | `AuthenticationException::banned()` | — (sayaç yok) |
| 7 | `Password::create` + `verify(hash, pepper)` — yanlışsa | 88–93 | `AuthenticationException::invalidCredentials()` | iki sayaç da `increment` |
| 8 | Gender gate (§7 bu dosyada) | 99–104 | `AuthenticationException::genderMismatch` | — |
| 9 | Başarı: iki sayacı da `reset`, `updateLastLogin` | 106–108 | — | sıfırlama |
| 10 | RBAC birleştirme + `session->setAuthUser` | 112–120 | — | oturuma yaz |
| 11 | `authKey = bin2hex(random_bytes(32))` + `saveAuthKey` + `AuthResponse::success('/home')` | 122–130 | — | DB'ye tek kullanım anahtarı |

**Sürücü dikkati:** 5. ve 7. adım aynı exception'ı atar (`invalidCredentials`) —
kullanıcı "kullanıcı adı mı şifre mi yanlış" ayrımını **alamaz** (kasten,
enumeration kapısı). Ama **banned** ayrı bir exception'dır (`:85`) — banlı
hesap, doğru parolayla bile ayrışır. Bu ayrışma bilgi sızdırır mı sorusu
Red Team §13'tedir.

## §5 Kayıt Sürücüsü — registerWithRequest Adımları (S1:136–205)

| # | Adım | Satır | Duraklama |
|---|---|---|---|
| 1 | IP limiti oku (3/3600) | 138–141 | `RateLimitException::registerRateLimited(3600)` |
| 2 | Kullanıcı adı regex + `usernameExists` | 144–149 | `ConflictException::usernameAlreadyExists()` (+increment) |
| 3 | `Email::create` + `emailExists` | 151–155 | `ConflictException::emailAlreadyExists()` (+increment) |
| 4 | Parola uzunluğu `< 8` | 157–159 | `errors['password']` biriktir |
| 5 | `agreeTerms` yoksa | 161–163 | `errors['agree_terms']` biriktir |
| 6 | Birikmiş hatalar varsa tek seferde | 165–168 | `ValidationException::multiple($errors)` (+increment) |
| 7 | Sayacı `reset` | 170 | — |
| 8 | `Password::create` + `hashWithPepper($pepper)` | 172–173 | — |
| 9 | `userRepository->create([...])` — 7 alan (username, email, password_hash, display_name, gender, account_type='free') | 177–184 | — |
| 10 | `setRegisteredUser` + `regenerateId` (session-fixation savarması) | 186–187 | — |
| 11 | auth_key üret + `saveAuthKey` + `AuthResponse::success` | 189–204 | — |

**Sürücü gözlemi:** kayıtta hata **biriktirilir** (`ValidationException::multiple`,
adım 6), girişte ise **ilk hatada kırılır**. İki farklı hata disiplini —
tasarım kararı, gerekçe dosyada yok → `⚠️ VERIFICATION REQUIRED`.

## §6 İki Boyutlu Limit Sürücüsü (S1:59–69, 77–78, 90–91, 106–107)

| Boyut | Anahtar | Kime vurur | Saldırı tipi |
|---|---|---|---|
| IP | `rate_limit:login:{ip}` | Aynı IP'den çok deneme | Dağıtık olmayan kaba kuvvet |
| Kimlik | `rate_limit:login:id:sha256(identity)` | Tek hesabı çok deneme | Parola-sprey / tek hesap kovalamacısı |

| Olay | IP sayacı | Kimlik sayacı |
|---|---|---|
| Boş alan (adım 4) | dokunulmaz | dokunulmaz |
| Kullanıcı yok (adım 5) | `increment` | `increment` |
| Parola yanlış (adım 7) | `increment` | `increment` |
| Başarı (adım 9) | `reset` | `reset` |
| Limit dolumu (adım 3) | `RateLimitException` | `RateLimitException` (aynı throw, `\|\|`) |

**Sürücü notu:** kimlik anahtarı `strtolower` + `sha256` ile kurulur (`:63`) —
geri-dönüştürülemez; var olmayan kimlik de sayılır (`:62` yorumu), yani
sayaç enumeration sinyali vermez.

## §7 Gender Gate Sürücüsü (S1:99–104)

| Değişken | Kaynak | Satır |
|---|---|---|
| `$userGender` | `Gender::create($user->gender)` — DB'deki kayıt tercihi | 99 |
| `$visitorGender` | `Gender::create($request->visitorGender)` — giriş anındaki seçim | 100 |

| `$userGender` | `$visitorGender` | Sonuç | Satır |
|---|---|---|---|
| neutral | herhangi | geç | 102 (`!isNeutral()` kısa devre) |
| herhangi | neutral | geç | 102 |
| male/female | aynı cinsiyet | geç | 102 (`equals`) |
| male/female | farklı cinsiyet | **`genderMismatch` throw** | 103 |

**Sürücü konumu:** gate **parola doğrulamasından sonra** (`:89`), sayacı
sıfırlama noktasından **önce** çalışır — yanlış cinsiyet denemesi parola doğruysa sayaç resetlenmez, ama
`increment` da yapılmaz (adım 8'de sayaç işlemi yok). Yani yanlış gender
girişimi limiti **ne artırır ne azaltır**.

## §8 RBAC Yazım Sürücüsü (S1:110–120)

| Adım | Satır | İş |
|---|---|---|
| 1 | 112 | `$user->toArray()` — temel veri |
| 2 | 113 | `findRolesForUser($user->id)` — repository'den roller+izinler |
| 3 | 114 | `$roles !== []` ise devam (boşsa rol yazımı atlanır) |
| 4 | 115 | Birincil rol = `roles[0]['role_name']` (alfabetik ilk — `:111` yorumu) |
| 5 | 116–118 | İzinler = tüm rollerden birleşim + `array_unique` + `array_values` |
| 6 | 120 | `session->setAuthUser($userData)` — 9 anahtar (§10) |

**Kırılganlık (kod okuması):** `array_merge(...array_map(...))` boş dizi
`$roles`'te 0 argümanla çağrılır → ama `:114` bunu zaten kapılar. `roles[0]`
indeksi `:115`'te korumasızdır; repository sıralamasını garanti edip
etmediği → `⚠️ VERIFICATION REQUIRED`.

## §9 auth_key Sürücüsü — Üretim ve Tek Kullanım (S1:122–124, 310–348)

| Aşama | Satır | İş |
|---|---|---|
| Üretim (giriş) | 122 | `bin2hex(random_bytes(32))` → 64 hex karakter |
| Üretim (kayıt) | 189 | aynı üretim, `created['user_id']` ile |
| Süre | 123 / 190 | `time() + AUTH_KEY_TTL (300)` → `Y-m-d H:i:s` |
| Kalıcılaşma | 124 / 191 | `saveAuthKey(userId, authKey, expiresAt, clientIp)` |
| Doğrulama — kol A | 316 | `findValidAuthKey($authKey, false)` — kullanılmamış |
| Doğrulama — kol B | 319 | kol A boşsa `findValidAuthKey($authKey, true)` — kullanılmış |
| Kol A başarılı | 335–336 | `markAuthKeyUsed(tokenId)` — **tek kullanım damgası** |
| Kol B başarılı | 323–332 | damga YOK — okuma sonu |
| Boş/invalid | 312–314 / 318–321 | `AuthenticationException::invalidCredentials()` |

**Sürücü gözlemi:** iki kol da aynı 8 alanlık diziyi döndürür
(`id, user_id, username, email, display_name, gender, avatar_url, account_type` —
satır 323–332 ve 338–347 birebir aynı). Fark yalnız `markAuthKeyUsed` çağrısı.

## §10 Oturum Yazım Sürücüsü — SessionManager Anahtarları (S1)

| Anahtar | `setAuthUser` satır | `setRegisteredUser` satır | Varsayılan |
|---|---|---|---|
| `MM_UserID` | 18 | 35 | — (boşsa `getUserId` null döner, :49) |
| `MM_Username` | 19 | 36 | — |
| `MM_Email` | 20 | 37 | — |
| `MM_DisplayName` | 21 | 38 | `username` fallback |
| `MM_AccountType` | 22 | 40 | `free` |
| `MM_Image` | 23 | 41 | `''` |
| `MM_Gender` | 24 | 42 | `neutral` |
| `MM_UserRole` | 26–28 | 39 | `setAuthUser`: `user` · `setRegisteredUser`: `free` (⚠️ iki farklı fallback) |
| `MM_Permissions` | 29 | YOK | `[]` |
| `_session_last_active` | 30 | 43 | `time()` |

**Sürücü çelişkisi (kanıt):** `setAuthUser` rol yokluğunda `'user'` yazar
(`:27–28`), `setRegisteredUser` ise `$_SESSION['MM_UserRole']`'a `'free'`
yazar (`:39`) — `free` bir **hesap tipi**, rol değil. İki yazım aynı anahtara
farklı nitelikte değer koyar; `PermissionMiddleware` hangisini okursa
okunsun biri tutarsızdır → `⚠️ VERIFICATION REQUIRED` (anayasa Q1 ile aynı
aile).

## §11 Yaşam Döngüsü Sürücüsü — SessionManager Metotları (S1)

| # | Metot | Satır | Etki |
|---|---|---|---|
| 1 | `getUserId()` | 46–53 | `MM_UserID` yok/boş/non-string → `null` |
| 2 | `isAuthenticated()` | 55–58 | yalnız `getUserId() !== null` |
| 3 | `destroy()` | 60–77 | `cm_gender` sakla → `SessionBootstrapper::restartFresh()` → üç `_*` damgası + `csp_nonce` yenile → `cm_gender` geri koy |
| 4 | `regenerateId()` | 79–82 | `session_regenerate_id(true)` |
| 5 | `get/set/remove` | 84–97 | genel anahtar sarmalayıcı |
| 6 | `setGender/getGender` | 99–107 | `cm_gender` → fallback `MM_Gender` → `neutral` |
| 7 | `setPendingRedirect/consumePendingRedirect` | 109–119 | tek-okuma `_pending_redirect_uri` (unset edilir) |
| 8 | `regenerateCspNonce/getCspNonce` | 121–131 | `bin2hex(random_bytes(32))` |
| 9 | `isIdleExpired(int)` | 133–137 | `_session_last_active > 0 && (now - last) > timeout` |
| 10 | `touch()` | 139–142 | `_session_last_active = now` |
| 11 | `rotateIfNeeded(int)` | 144–159 | bkz §12 |
| 12 | `clearDisplayCookies()` | 161–169 | 5 cookie `time()-3600` ile silinir |
| 13 | `getCookieParams()` | 171–174 | `session_get_cookie_params()` passthrough |
| 14 | `all()` | 176–179 | `$_SESSION` tam kopya |

### §11.1 destroy — Cinsiyet Koruma Kapısı (S1:60–77)

| # | Satır | İş |
|---|---|---|
| 1 | 63 | `$savedGender = $_SESSION['cm_gender'] ?? null` — logout'ta kaybolmasın diye |
| 2 | 65 | `SessionBootstrapper::restartFresh()` — oturumu tam yeniden kurar (içeriği okunmadı) |
| 3 | 67–70 | `_session_last_active`, `_session_created_at`, `_session_rotated_at` = `now` |
| 4 | 71 | `csp_nonce` yeniden `random_bytes(32)` |
| 5 | 74–76 | `$savedGender !== null` ise `cm_gender` geri yazılır |

### §11.2 clearDisplayCookies — Silinen 5 Çerezi (S1:163)

| # | Cookie adı | Süre | Alan |
|---|---|---|---|
| 1 | `MM_Username` | `-3600` | `/` + `.coremusic.net` |
| 2 | `MM_Image` | `-3600` | `/` + `.coremusic.net` |
| 3 | `MM_UserID` | `-3600` | `/` + `.coremusic.net` |
| 4 | `MM_UserDesc` | `-3600` | `/` + `.coremusic.net` |
| 5 | `MM_UserYear` | `-3600` | `/` + `.coremusic.net` |

**Kod okuması:** bu 5 çerez `setAuthUser`'da yazılmaz (`:15–31` yok) —
nerede yazıldığı → `⚠️ VERIFICATION REQUIRED` (muhtemelen başka katman,
kanıt yok).

## §12 Rotasyon Sürücüsü — rotateIfNeeded (S1:144–159)

| Durum | Koşul | İş | Dönüş |
|---|---|---|---|
| İlk çağrı | `_session_rotated_at == 0` (`:148`) | `rotated_at` + `created_at` = now | `false` (`:151`) |
| Pencere içi | `now - rotated_at <= interval` | dokunulmaz | `false` (`:158`) |
| Pencere dışı | `now - rotated_at > interval` (`:153`) | `session_regenerate_id(true)` + `rotated_at = now` | `true` (`:156`) |

**Sürücü notu:** ilk çağrı **rotasyon yapmaz**, yalnız damgayı kurar — yani
rotasyon aralığının tamamı ilk çağrıdan sonra başlar. `true` döndüğü an
eski `session_id` geçersiz (`regenerate_id(true)` = eskiyi sil).

## §13 Boşta Kalma Sürücüsü — isIdleExpired + touch (S1:133–142)

| Parça | Satır | Formül |
|---|---|---|
| `isIdleExpired($t)` | 136 | `last = (int)($_SESSION['_session_last_active'] ?? 0)` |
| `isIdleExpired` (devam) | 137 | `last > 0 && (time() - last) > $t` |
| `touch()` | 141 | `$_SESSION['_session_last_active'] = time()` |

**İki uç davranış (kod kanıtı):**
- `last == 0` (anahtar yok) → `isIdleExpired` **`false` döner** — yani
  "zaman damgası olmayan" oturum asla boşa çıkmaz. Tehlikeli mi, kasıtlı mı →
  gerekçe yok → `⚠️ VERIFICATION REQUIRED`.
- `touch()` çağrısı `setAuthUser`/`setRegisteredUser` içinde **değil** —
  nerede çağrıldığı okunmadı → `⚠️ VERIFICATION REQUIRED`.

## §14 logout Zinciri (S1:221–233)

| # | Satır | İş | Kat |
|---|---|---|---|
| 1 | 225 | `getUserId()` — null ise 2–3. adım atlanır | session |
| 2 | 227 | `revokeAuthKeysForUser(userId)` — bütün auth_key'ler iptal | repository |
| 3 | 228 | `revokeAccessTokensForUser(userId)` — JWT access token'ları iptal (`:223–224` yorumu: P1-9) | repository |
| 4 | 231 | `session->destroy()` — §11.1 (gender korunur) | session |
| 5 | 232 | `session->clearDisplayCookies()` — §11.2 (5 çerez) | session |

**Sürücü emri:** 2–3. adımlar `userId === null`'da **atlanmaz — hiç çalışmaz**;
yani anonim çıkışta revoke yok (zaten anahtar yok varsayımı). Varsayımın
kanıtı kodda yok → `⚠️ VERIFICATION REQUIRED`.

## §15 Parola Sıfırlama Sürücüsü

| Metot | Satır | Kritik adım |
|---|---|---|
| `requestPasswordReset` | 254–280 | limit `3/3600` (`:259`) → `findByEmail` (`:263`) |
| — kullanıcı yoksa | 264–267 | **aynı mesaj** döner (`success: true`) + `increment` — enumeration kapısı |
| — kullanıcı varsa | 269–275 | `random_bytes(32)` ham token, `sha256(token)` **hash'i saklanır** (`:270`), `saveResetToken`, ayrıca `increment` |
| — yan etki | 277 | `error_log` — yalnız `user_id`, token log'a yazılmaz |
| `resetPassword` | 282–308 | boş token → `invalidToken` (`:285`) |
| — uzunluk | 287–289 | `< 8` → `passwordTooShort(8)` |
| — lookup | 291–295 | `sha256(token)` → `findValidResetToken` → null ise `tokenExpired` |
| — yazma | 302–303 | `updatePassword(userId, hash)` **önce**, `markResetTokenUsed(tokenId)` **sonra** |

**Sürücü riski (Red Team):** `updatePassword` (`:302`) `markResetTokenUsed`
(`:303`)'ten **önce** gelir — ikisi arasında çökme olursa token kullanılmamış
kalır, parola ise değişmiştir. Tek-çekirdek PHP'de pratikte dar pencere ama
**işlemsel sarmalama (transaction) yok** → `⚠️ VERIFICATION REQUIRED`
(transaction çağrısı iki satırda da yok).

## §16 K010 Sınır Kapısı — Delege Haritası

| AuthService | Ne yapar | K010'a ne bırakır |
|---|---|---|
| `isAuthenticated()` (`:235`) | yalnız `session->isAuthenticated()` çağırır | kimlik **iddiası** K012 oturumdan gelir |
| `getCurrentUser()` (`:240`) | session userId + `findByIdHex` + `User::fromRow` | kimlik **kaydı** K010 DB'sinden (repository) |
| `validateSessionKey()` (`:310`) | auth_key tek-kullanım kapısı (§9) | yetki **verisi** RBAC'te (`findRolesForUser`) |
| `loginWithRequest` adım 11 (`:110`) | "PermissionMiddleware bunları bekler" yorumu | **okuyan** PermissionMiddleware hangi katman? → `⚠️ VERIFICATION REQUIRED` |

**Argon2id kural≠kanıt:** `auth.coremusic.net/AGENTS.md` §4.3 kuralı der
"Argon2id şifre hash'i; `Password` ValueObject dışında hash üretimi yasak".
Kod kanıtı yalnız şudur: `AuthService` hash'i **yalnız** `Password` üzerinden
üretir (`:172–173` register, `:297–298` reset) ve doğrular (`:88–89` verify).
`Password` sınıfının **içinde** Argon2id'nin gerçekten çağrıldığı bu oturumda
**okunmadı** → kural mevcut, algoritma kanıtı `⚠️ VERIFICATION REQUIRED`.

## §17 Karar Tablosu — Hangi Sürücü Ne Zaman Döner?

| Durum | Çağrılan sürücü | Dönen şey | Satır |
|---|---|---|---|
| Sayfa ilk yükleme, çerez yok | `isAuthenticated` | `false` | 55–58 |
| Giriş formu gönderildi | `loginWithRequest` | `AuthResponse::success('/home')` | 126–130 |
| Giriş başarısız (parola) | `loginWithRequest` | `invalidCredentials` throw | 92 |
| Limit dolumu | `loginWithRequest` | `RateLimitException` throw | 68 |
| Kayıt tamam | `registerWithRequest` | `AuthResponse::success('/home')` | 193–204 |
| Çıkış | `logout` | `void` (revoke + destroy + cookie) | 221–233 |
| auth_key ile dönüş | `validateSessionKey` | 8 alanlı `array` | 338–347 |
| Boşta kalma dolumu | `isIdleExpired` | `true` → çağıran katman ne yapacak? | 133–137 |
| Rotasyon zamanı | `rotateIfNeeded` | `true` (ID yenilendi) | 156 |
| Sıfırlama isteği | `requestPasswordReset` | hep `success: true` mesajı | 266 / 279 |
| Sıfırlama onayı | `resetPassword` | `success: true` + token kullanıldı | 307 |

## §18 Enjeksiyon ve Bağımlılık Sürücüsü

| Bağımlılık | Tip | Nerede kullanılır | Değiştirilebilir mi |
|---|---|---|---|
| `userRepository` | `IUserRepository` (okunmadı) | repository'nin 13 çağrısı (§2, §14, §15) | kontrat üzerinden — evet |
| `session` | `ISessionManager` (okunmadı) | `setAuthUser`, `setRegisteredUser`, `destroy`, `getUserId`, `isAuthenticated`, `clearDisplayCookies` | kontrat üzerinden — evet |
| `rateLimiter` | `IRateLimiter` (okunmadı) | `isLimited`, `increment`, `reset` | kontrat üzerinden — evet |
| `pepper` | `string` (default `''`) | `verify` (`:89`) + `hashWithPepper` (`:173`, `:298`) | constructor argümanı; **boş kalırsa** hash zayıflar → nereden enjekte edildiği `⚠️ VERIFICATION REQUIRED` |

**Sürücü gözlemi:** `pepper` default'u `''` (`:49`) — enjeksiyon unutulursa
sistemsiz çalışır, sessizce. Güvenlik açısından en kritik tek satır.

## §19 Red Team Risk Listesi (Sürücü Gözlemi)

| # | Risk | Kanıt | Ciddiyet |
|---|---|---|---|
| 1 | `pepper` boş default | AuthService:49 | Yüksek (sessiz zayıflama) |
| 2 | `MM_UserRole` iki farklı fallback (`user` vs `free`) | SessionManager:27–28 vs :39 | Yüksek (tutarsız yetki) |
| 3 | `resetPassword` transaction'sız sıralı yazma | AuthService:302–303 | Orta |
| 4 | `isIdleExpired` `last==0` → asla `true` | SessionManager:137 | Orta |
| 5 | `roles[0]` korumasız indeks | AuthService:115 | Orta |
| 6 | banlı hesap ayrı exception (ayrışma) | AuthService:85 | Düşük (bilgi sızıntısı) |
| 7 | `setAuthUser` `MM_Permissions` yazar, `setRegisteredUser` yazmaz | :29 vs 33–44 | Orta (kayıt sonrası yetki boşluğu) |
| 8 | `error_log`'a `user_id` yazılır (PII) | :277, :305 | Düşük (session/cookie log yasağı §4.3'te — log farklı yüzey) |

## §20 Kapsam Dışı — Sürücünün Bilmediği Şeyler

| Konu | Neden bu dosyada yok |
|---|---|
| `IUserRepository` implementasyonu (SQL) | Dosya okunmadı — repository ayrı katman |
| `IRateLimiter` deposu (APCu / dosya / DB) | Kontrat okunmadı → UNKNOWN |
| `SessionBootstrapper::restartFresh()` içeriği | `shared/` tarafı okunmadı (adı: `CoreMusic\Session\SessionBootstrapper`) |
| `Password` VO içi hash algoritması | Sınıf okunmadı — §16 kural≠kanıt |
| `PermissionMiddleware` / `AuthMiddleware` | Bu iki dosyada değil |
| Controller/Handler akışı (`AuthController`, handler'lar) | envanterde var (`auth.coremusic.net/AGENTS.md` §2), okunmadı |
| Sayfa akışı (`pages/login.php` vb.) | envanterde var, okunmadı |

## §21 Açık Sorular (Kalıcı)

| # | Soru | Etki |
|---|---|---|
| Q1 | `pepper` enjeksiyonu hangi dosyada yapılır (container/config)? | Güvenlik |
| Q2 | `MM_UserRole` `free` değeri `PermissionMiddleware`'te nasıl yorumlanır? | Yetki |
| Q3 | `touch()` / `rotateIfNeeded()` çağıran katman (middleware mi, bootstrap mi)? | Oturum güvenliği |
| Q4 | 5 display cookie'si nerede yazılır? | Veri sızıntısı |
| Q5 | `findValidAuthKey` iki kolunun SQL farkı (kullanıldı bayrağı) | Tek-kullanım garantisi |
| Q6 | `user-service` adı auth servisini kapsar mı (K006/K016)? | Sınır |
| Q7 | Rate limit deposu fiziksel olarak nedir? | Dayanıklılık |

## §22 Kod Bloğu — Doğrulama Zinciri (S1 birebir)

```php
// AuthService.php:65–69 — iki limit kapısı (|| kısa devre)
if ($this->rateLimiter->isLimited($failedKey, self::MAX_LOGIN_ATTEMPTS, self::LOGIN_WINDOW_SECONDS)
    || $this->rateLimiter->isLimited($identityKey, self::MAX_LOGIN_ATTEMPTS, self::LOGIN_WINDOW_SECONDS)
) {
    throw RateLimitException::loginRateLimited(self::LOGIN_WINDOW_SECONDS);
}
```

```php
// AuthService.php:102–104 — gender gate (üç koşul tek ifade)
if (!$userGender->isNeutral() && !$visitorGender->isNeutral() && !$visitorGender->equals($userGender)) {
    throw AuthenticationException::genderMismatch((string)$userGender);
}
```

```php
// SessionManager.php:133–137 — boşta kalma kararı
public function isIdleExpired(int $timeoutSeconds): bool
{
    $lastActivity = (int)($_SESSION['_session_last_active'] ?? 0);
    return $lastActivity > 0 && (time() - $lastActivity) > $timeoutSeconds;
}
```

```php
// SessionManager.php:153–157 — rotasyon penceresi
if (($now - $lastRotation) > $intervalSeconds) {
    session_regenerate_id(true);
    $_SESSION['_session_rotated_at'] = $now;
    return true;
}
return false;
```

```php
// AuthService.php:335–336 — auth_key tek kullanım damgası
$tokenId = UserId::fromHex($record['token_id']);
$this->userRepository->markAuthKeyUsed((string)$tokenId);
```

## §23 Çağrı Grafiği — Kim Kimi Çağırır (S1)

| Çağırıcı | Çağrılan | Satır | Tür |
|---|---|---|---|
| `loginWithRequest` | `rateLimiter->isLimited` ×2 | 65–66 | kontrat |
| `loginWithRequest` | `userRepository->findByCredential` | 75 | kontrat |
| `loginWithRequest` | `User::fromRow` | 82 | statik fabrika |
| `loginWithRequest` | `Password::create` + `verify` | 88–89 | VO |
| `loginWithRequest` | `Gender::create` ×2 | 99–100 | VO |
| `loginWithRequest` | `rateLimiter->reset` ×2 | 106–107 | kontrat |
| `loginWithRequest` | `userRepository->findRolesForUser` | 113 | kontrat |
| `loginWithRequest` | `session->setAuthUser` | 120 | kontrat |
| `loginWithRequest` | `userRepository->saveAuthKey` | 124 | kontrat |
| `registerWithRequest` | `Email::create` | 151 | VO |
| `registerWithRequest` | `Password::create` + `hashWithPepper` | 172–173 | VO |
| `registerWithRequest` | `userRepository->create` | 177 | kontrat |
| `registerWithRequest` | `session->setRegisteredUser` + `regenerateId` | 186–187 | kontrat |
| `logout` | `session->getUserId` | 225 | kontrat |
| `logout` | `userRepository->revokeAuthKeysForUser` | 227 | kontrat |
| `logout` | `userRepository->revokeAccessTokensForUser` | 228 | kontrat |
| `logout` | `session->destroy` + `clearDisplayCookies` | 231–232 | kontrat |
| `getCurrentUser` | `userRepository->findByIdHex` | 246 | kontrat |
| `requestPasswordReset` | `userRepository->findByEmail` | 263 | kontrat |
| `requestPasswordReset` | `userRepository->saveResetToken` | 274 | kontrat |
| `resetPassword` | `userRepository->findValidResetToken` | 292 | kontrat |
| `resetPassword` | `userRepository->updatePassword` | 302 | kontrat |
| `resetPassword` | `userRepository->markResetTokenUsed` | 303 | kontrat |
| `validateSessionKey` | `userRepository->findValidAuthKey` ×2 | 316, 319 | kontrat |
| `validateSessionKey` | `userRepository->markAuthKeyUsed` | 336 | kontrat |

**Graf özeti:** 25 çağrı satırının **21'i kontrat** (interfaced), 4'ü VO/statik
fabrika. `AuthService` somut sınıf'a doğrudan bağımlı **değil** — tek istisna
`User::fromRow` (`:82`) ve DTO'lar; hepsi `use` satırlarında (`:5–20`).

## §24 Sarmalayıcı Uyumu — Eski ve Yeni İmza (S1:209–219)

| Metot | Eski imza (IAuthService) | Yeni imza (DTO) | Köprü satır |
|---|---|---|---|
| Giriş | `login(string $identity, string $password, string $visitorGender = 'neutral', string $clientIp = '127.0.0.1'): array` | `loginWithRequest(LoginRequest): AuthResponse` | 211–212 (`new LoginRequest` → `->toArray()`) |
| Kayıt | `register(array $data, string $clientIp = '127.0.0.1', string $visitorGender = 'neutral'): array` | `registerWithRequest(RegisterRequest): AuthResponse` | 217–218 (`RegisterRequest::fromArray` → `->toArray()`) |

**Sürücü gözlemi:** iki köprü de **varsayılan `127.0.0.1`** taşır — gerçek IP
unutulursa rate limit `127.0.0.1`'de birleşir (tüm kullanıcılar tek kova).
Varsayılanı çağıran katmanın doğru IP'yi geçtiği → `⚠️ VERIFICATION REQUIRED`.

## §25 Örnek Kullanım Senaryoları (Senaryo Tabanlı — Kod Yok)

> Bu bölümde PHP örneği **yazılmaz** (vault'ta kod şablonu yerine gerçek dosya
> kanıtı esastır); senaryolar §2'deki envantere bağlanır.

| Senaryo | Adım zinciri | Referans |
|---|---|---|
| Normal giriş | §4 adım 1→11 tamamı | AuthService:57–131 |
| Sprey saldırısı | §4 adım 3 — kimlik anahtarı dolar, IP hâlâ boş olabilir | AuthService:63–69 |
| Kayıt + oturum | §5 adım 1→11, sonra `setRegisteredUser` + `regenerateId` | AuthService:186–187 |
| Güvenli çıkış | §14 beş adım | AuthService:221–233 |
| auth_key dönüşü | §9 kol A → `markAuthKeyUsed` | AuthService:316, 335–336 |
| Boşta kalma | §13 `isIdleExpired` → çağıran katman kararı (dosya dışı) | SessionManager:133–137 |
| Rotasyon | §12 pencere dışı → ID yenileme | SessionManager:144–159 |

## §26 Değişiklik Kaydı

| Tarih | Sürüm | Değişiklik |
|---|---|---|
| 2026-10-13 | 1.0.0 | sürücü yazıldı — AuthService 11 metot + SessionManager 14 metot envanteri, giriş 11 adım, iki boyutlu limit, gender gate, RBAC yazım, auth_key, yaşam döngüsü, logout/sıfırlama zincirleri, karar tablosu (S1 birebir) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
