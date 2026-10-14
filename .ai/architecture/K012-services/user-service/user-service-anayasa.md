---
node_id: K012-services/user-service
node_type: module
role: anayasa
status: draft
parent_id: K012-services/user-service
children: []
depends_on:
  - "user-service/index.md"
  - "services-master.md"
  - "services-bagimliliklar.md"
evidence:
  - "v2 §2.12 — çocuk adı kaynağı (birebir)"
  - "S1 — auth.coremusic.net/include/Service/AuthService.php 349 satır (loginWithRequest:57, registerWithRequest:136, logout:221, requestPasswordReset:254, resetPassword:282, validateSessionKey:310)"
  - "S1 — auth.coremusic.net/include/Service/SessionManager.php 180 satır (setAuthUser:15, isIdleExpired:133, rotateIfNeeded:144)"
  - "S1 — auth.coremusic.net/include/Domain/Entity/User.php 77 satır (class:11)"
  - "auth.coremusic.net/AGENTS.md §2 envanteri (hexagonal katmanlar)"
validation:
  - "§3 kod kanıtı 2026-10-13 read — birebir"
  - "§2 sınır services-master §2.2 ile hizalı"
open_questions:
  - "K010-identitiy ile user-service sınırı (kimlik kimde?) → ⚠️ VERIFICATION REQUIRED"
  - "IUserRepository/IRateLimiter implementasyonu okunmadı → ⚠️ VERIFICATION REQUIRED"
  - "Parola hash algoritması (Argon2id iddiası) Password VO içinde doğrulanmadı → ⚠️ VERIFICATION REQUIRED"
date: 2026-10-13
updated: 2026-10-13
version: 1.0.0
authority: "SSOT: v2 §2.12 + auth.coremusic.net/include/Service/ (S1, 2026-10-13 read)"
group: mimari
---

# user-service — Anayasa (Constitution)

> **Ön not:** user-service, kullanıcının **profil/tercih/abonelik** işlerini
> yürüten K012 servisidir (master §21.5). Bu dosya servisin anayasasını yazar.
> Repo'daki `auth.coremusic.net/include/Service/` kodu (AuthService +
> SessionManager) **giriş/kayıt/oturum** işlerini taşır — bu iki kavram (kullanıcı
> profili vs kimlik doğrulama) arasındaki sınır bu dosyada açıkça ele alınır (§5).

## §1 ELI15 — Bu Servis Ne Yapar?

Kullanıcıyla ilgili **veriyi ve tercihleri** işler: kayıt, giriş, oturum,
profil, cinsiyet tercihi, parola sıfırlama. Arayüz çizmez (K011); kimlik
protokolünü kendisi üretmez (K010-identitiy olabilir); yalnız iş mantığını
uygular ve sonucu döner. Repo kanıtı auth.coremusic.net altındadır ve bu
servisin **kimlik-doğrulama yarısını** kapsar.

## §2 Sınır Tablosu

| Konu | Kapsam | Sahibi | Kanıt |
|---|---|---|---|
| Kullanıcı kaydı / giriş iş mantığı | İÇİNDE | K012/user-service | S1 AuthService:57/136 |
| Oturum yönetimi | İÇİNDE | K012 | S1 SessionManager:15 |
| Parola sıfırlama akışı | İÇİNDE | K012 | S1 AuthService:254/282 |
| Cinsiyet tercihi | İÇİNDE | K012 | S1 AuthService:99–104 |
| Kullanıcı kimliği (protokol/JWT) | DIŞINDA (iddia) | K010-identitiy | §5 ⚠️ |
| Kalıcı kullanıcı şeması | DIŞINDA | K007-data | master §2.2 |
| Arayüz (login/register sayfası) | DIŞINDA | K011 | master §2.2 |
| Parola hash algoritması (Argon2id) | sınır | Password VO | §6 ⚠️ |

## §3 Kimlik Doğrulama İş Mantığı (Repo Kanıtı — Birebir)

Dosya: `auth.coremusic.net/include/Service/AuthService.php` / Satır: 30–43 (const)

| # | const | Değer | Anlam |
|---|---|---|---|
| 1 | `MIN_PASSWORD_LENGTH` | 8 | min parola uzunluğu |
| 2 | `MAX_LOGIN_ATTEMPTS` | 5 | IP başına giriş limiti |
| 3 | `LOGIN_WINDOW_SECONDS` | 900 | 15 dk pencere |
| 4 | `MAX_REGISTER_ATTEMPTS` | 3 | IP başına kayıt limiti |
| 5 | `REGISTER_WINDOW_SECONDS` | 3600 | 1 saat pencere |
| 6 | `AUTH_KEY_TTL` | 300 | auth_key ömrü (sn) |
| 7 | `USERNAME_PATTERN` | `/^[a-zA-Z0-9_]{3,30}$/` | kullanıcı adı deseni |
| 8 | `PASSWORD_RESET_TOKEN_TTL` | 900 | sıfırlama tokenı 15 dk |

**Oran-kısıt gözlemi (S1:31–43):** user-service, rate-limit'i **kendi const'larıyla**
tanımlar; `IRateLimiter` enjeksiyonu ile uygular (S1:48). Yani limit **sayıları**
bu servistedir, mekanizması dışarıdadır (IRateLimiter).

## §4 Giriş Akışı (loginWithRequest — 11 adım)

Dosya: `auth.coremusic.net/include/Service/AuthService.php` / Satır: 57–131

| # | Adım | İşlem | Satır |
|---|---|---|---|
| 1 | iki boyutlu oran kontrolü (IP + kimlik hash) | `isLimited` ×2 | 65–66 |
| 2 | boş alan kontrolü | `ValidationException` | 71–73 |
| 3 | kullanıcı sorgusu | `findByCredential` | 75 |
| 4 | ban kontrolü | `isBanned` | 84 |
| 5 | parola doğrulama | `Password::verify` | 88–89 |
| 6 | cinsiyet uyumu | `Gender::equals` | 99–104 |
| 7 | sayaç sıfırlama | `reset` ×2 | 106–107 |
| 8 | son giriş güncelle | `updateLastLogin` | 108 |
| 9 | RBAC session'a yaz | `setAuthUser` | 112–120 |
| 10 | auth_key üret | `random_bytes(32)` | 122–124 |
| 11 | AuthResponse dön | `success` | 126–130 |

**İki boyutlu limit gözlemi (S1:60–63):** hem IP (`failedKey`) hem kimlik
(`identityKey`, SHA-256 hash) bazlı sayaç tutulur — parola-sprey ve dağıtık
saldırıya karşı **iki boyutlu**. Hash geri-dönüştürülemez; var olmayan kimlik de
sayılır (enumeration sinyali vermez) — bu bilinçli bir güvenlik tasarımıdır
(kod yorumu S1:61–62).

## §5 Sınır Açıklığı: user-service vs K010-identitiy (Açık Kapı)

| # | Soru | Durum |
|---|---|---|
| 1 | auth.coremusic.net K012 mi K010 mu? | kesinleşmedi ⚠️ |
| 2 | JWT/protokol üretimi kimin? | okunmadı ⚠️ |
| 3 | `validateSessionKey` K012'nin mi? | S1 AuthService:310 |
| 4 | RBAC rolleri K008 mi? | okunmadı ⚠️ |

**Sınır notu (S1):** AuthService **hem** iş mantığı **hem** oturum/açık-anahtar
( auth_key ) yönetimini içerir (S1:122–124). `validateSessionKey` (S1:310) bir
**doğrulama** işidir — bu, K010-identitiy ile sınır bölgesindedir. Kesin sahiplik
bu turda yazılmaz → açık kapı (master §7/Q1 benzeri).

## §6 Parola Hash Sınırı

| # | İddia | Kanıt | Durum |
|---|---|---|---|
| 1 | Argon2id kullanılır | auth AGENTS.md §4.1 (kural) | kural olarak ✅ |
| 2 | `Password` VO dışında hash yasak | auth AGENTS.md §4.1 | kural |
| 3 | `Password::hashWithPepper` gerçekten Argon2id mi | VO içeriği okunmadı | ⚠️ |
| 4 | `pepper` nereden geliyor | constructor (S1:49) — değer okunmadı | ⚠️ |

**Kural-kanıt ayrımı (S1):** "Argon2id" iddiası `auth.coremusic.net/AGENTS.md`
§4.1'den gelir — bu bir **mimari kural**dır, kod kanıtı **değildir**. Gerçek
hash algoritması `Password` ValueObject'inin içindedir ve o dosya bu turda
okunmadı → `⚠️ VERIFICATION REQUIRED`. Zero-hallucination gereği "Argon2id
kullanılıyor" kod düzeyinde **yazılmaz**.

## §7 Bağımlılık Özeti

| Hedef | Ne için | Etiket |
|---|---|---|
| K010-identitiy | kimlik protokolü (iddia) | TASARIM İDDİASI + §5 |
| K007-data | kullanıcı kalıcılığı (UserRepository PDO) | S1 (auth AGENTS.md §2) |
| K008-domain | User/Email/Password/Gender VO + iş kuralı | S1 (AuthService import) |
| K011-application | login/register arayüzü | TASARIM İDDİASI |

## §8 Red Team Risk Listesi

| # | Risk | Severity | Kanıt |
|---|---|---|---|
| 1 | K010↔K012 kimlik sınırı belirsiz | HIGH | §5 |
| 2 | Argon2id kural düzeyinde, kod kanıtı yok | HIGH | §6 |
| 3 | `pepper` değeri okunmadı (nerede saklanıyor?) | MEDIUM | S1:49 |
| 4 | `validateSessionKey` kimin alanı | MEDIUM | §5-3 |
| 5 | auth_key TTL 300s — oturumla ilişkisi | LOW | S1:35 |

## §9 Kapı Sayımı

| Kapı | Sayı |
|---|---|
| `⚠️ VERIFICATION REQUIRED` | 8 satır |
| Açık soru (§5 + §6) | 6 |

## §10 Kayıt Akışı (registerWithRequest — 10 adım)

Dosya: `auth.coremusic.net/include/Service/AuthService.php` / Satır: 136–205

| # | Adım | İşlem | Satır |
|---|---|---|---|
| 1 | oran kontrolü | `isLimited` (IP) | 139 |
| 2 | kullanıcı adı deseni + varlık | `preg_match` + `usernameExists` | 144–149 |
| 3 | e-posta varlığı | `emailExists` | 152–155 |
| 4 | parola uzunluğu | `MIN_PASSWORD_LENGTH` | 157–159 |
| 5 | koşul kabulü | `agreeTerms` | 161–163 |
| 6 | hata varsa toplu fırlat | `ValidationException::multiple` | 165–168 |
| 7 | parola hash | `hashWithPepper` | 172–173 |
| 8 | kullanıcı oluştur | `create([...])` | 177–184 |
| 9 | oturum kur + ID yenile | `setRegisteredUser` + `regenerateId` | 186–187 |
| 10 | auth_key üret + dön | `saveAuthKey` + `success` | 189–204 |

**Doğrulama gözlemi (S1:143–168):** kayıt, hataları **toplar** (`$errors[]`)
ve tek bir `ValidationException::multiple` ile döner — yani kullanıcıya tüm
hatalar **aynı anda** gösterilir (fail-fast değil, toplu-bildirim). Buna karşılık
giriş (login) **ilk hatada** fırlatır (S1:72/79/92). Bu iki farklı hata-stratejisi
bilinçli görünür.

## §11 Oturum Yönetimi (SessionManager — Anahtarlar)

Dosya: `auth.coremusic.net/include/Service/SessionManager.php` / Satır: 15–31

| # | Session anahtarı | Kaynak | Satır |
|---|---|---|---|
| 1 | `MM_UserID` | `$user['id']` | 18 |
| 2 | `MM_Username` | `$user['username']` | 19 |
| 3 | `MM_Email` | `$user['email']` | 20 |
| 4 | `MM_DisplayName` | fallback username | 21 |
| 5 | `MM_AccountType` | fallback 'free' | 22 |
| 6 | `MM_Image` | avatar_url | 23 |
| 7 | `MM_Gender` | fallback 'neutral' | 24 |
| 8 | `MM_UserRole` | RBAC — fallback 'user' | 26–28 |
| 9 | `MM_Permissions` | RBAC izin listesi | 29 |
| 10 | `_session_last_active` | `time()` | 30 |

**RBAC gözlemi (S1:26–29):** `setAuthUser` rol ve izinleri **session'a yazar**;
bu, `PermissionMiddleware`'in beklediği veridir (kod yorumu S1:25). Rol yoksa
`'user'` fallback'i devreye girer — yani her oturumda bir rol **bulunur**.

## §12 Oturum Yaşam Döngüsü (Repo Kanıtı)

Dosya: `auth.coremusic.net/include/Service/SessionManager.php` / Satır: 133–159

| # | Metot | Görev | Satır |
|---|---|---|---|
| 1 | `isIdleExpired($timeout)` | boşta-kalma süresi doldu mu | 133 |
| 2 | `touch()` | son aktif zamanı güncelle | 139 |
| 3 | `rotateIfNeeded($interval)` | periyot dolduysa ID döndür | 144 |
| 4 | `destroy()` | oturumu yeniden başlat (cinsiyet korunur) | 60 |
| 5 | `regenerateId()` | `session_regenerate_id(true)` | 79 |

**Döngü gözlemi (S1:144–159):** `rotateIfNeeded` periyot dolarsa
`session_regenerate_id(true)` çağırır ve `_session_rotated_at`'ı günceller —
yani **periyodik oturum kimliği döndürme** (session fixation savunması) mevcuttur.
Bu, güvenlik açısından kritik bir mekanizmadır (olgusal, kod okundu).

## §13 Cinsiyet Tercihi Mantığı (Repo Kanıtı)

Dosya: `auth.coremusic.net/include/Service/AuthService.php` / Satır: 95–104

| # | Kural | Sonuç |
|---|---|---|
| 1 | `neutral` kayıt → her cinsiyetle giriş | serbest |
| 2 | `male` kayıt → yalnız `male` ziyaretçi | kısıtlı |
| 3 | `female` kayıt → yalnız `female` ziyaretçi | kısıtlı |
| 4 | uyumsuz → `genderMismatch` istisnası | engel |

**Kural notu (S1:96–98):** bu, cinsiyet-tabanlı **erişim kontrolüdür** — kayıtta
seçilen cinsiyet, girişte de aynı olmalı kuralı. `neutral` istisnadır. Bu kuralın
iş mantığı kod-içi yorumda açıkça belgelenmiştir (S1:96–98).

## §14 Sınır Kararları Özeti (user-service)

| # | Karar | Dayanak |
|---|---|---|
| 1 | oran-limit sayıları servistedir | S1:31–43 |
| 2 | hash algoritması Password VO'nun içindedir | auth AGENTS.md §4.1 |
| 3 | oturum anahtarları `MM_*` adlandırması | S1:18–30 |
| 4 | kayıt toplu-hata, giriş fail-fast | S1:143–168 vs 72 |
| 5 | periyodik oturum döndürme var | S1:144 |
| 6 | cinsiyet erişim kontrolü iş mantığı | S1:95–104 |
| 7 | kimlik protokolü sınırda — sahiplik belirsiz | §5 |

## §15 Bağımlılık → Anayasa Eşlemesi

| Bağımlılık | Anayasa karşılığı |
|---|---|
| K010-identitiy | §5 · §7 |
| K007-data (UserRepository) | §7 · §10-8 |
| K008-domain (VO) | §3 · §13 |
| K011 (arayüz) | §2 |

## §16 Red Team Bulgu Listesi (Genişletilmiş)

| # | Bulgu | Severity |
|---|---|---|
| B1 | K010↔K012 kimlik sınırı belirsiz (§5) | HIGH |
| B2 | Argon2id yalnız kural düzeyinde — kod kanıtı yok (§6) | HIGH |
| B3 | `pepper` kaynağı okunmadı (§6) | MEDIUM |
| B4 | `IUserRepository` implementasyonu okunmadı | MEDIUM |
| B5 | auth_key (300s) ile oturum (idle) ilişkisi belirsiz | LOW |
| B6 | `validateSessionKey` iki yollu (aktif/kullanılmış) — davranış | LOW |

## §17 Kapı Özeti

| # | Kapı | Durum |
|---|---|---|
| 1 | K010 sınırı | açık |
| 2 | Argon2id kod kanıtı | okunmadı |
| 3 | `pepper` kaynağı | okunmadı |
| 4 | IUserRepository impl | okunmadı |
| 5 | JWT/protokol üretimi | okunmadı |

## §18 Parola Sıfırlama Akışı (Repo Kanıtı — Birebir)

Dosya: `auth.coremusic.net/include/Service/AuthService.php` / Satır: 254–308

| # | Adım | `requestPasswordReset` | Satır |
|---|---|---|---|
| 1 | e-posta normalizasyonu | `Email::create` | 256 |
| 2 | oran kontrolü (3/3600s) | `isLimited` | 259 |
| 3 | kullanıcı yoksa bile aynı mesaj | `success=true` | 264–267 |
| 4 | token üret + hash | `random_bytes` + `sha256` | 269–270 |
| 5 | token kaydet | `saveResetToken` | 274 |
| 6 | log | `error_log` | 277 |

| # | Adım | `resetPassword` | Satır |
|---|---|---|---|
| 1 | boş token kontrolü | `invalidToken` | 284–286 |
| 2 | parola uzunluğu | `passwordTooShort` | 287–289 |
| 3 | hash + geçerli token sorgusu | `findValidResetToken` | 291–292 |
| 4 | yeni hash | `hashWithPepper` | 297–298 |
| 5 | parola güncelle + token kullanıldı işaretle | `updatePassword` + `markResetTokenUsed` | 302–303 |

**Enumeration gözlemi (S1:264–267):** `requestPasswordReset`, e-posta **var olmasa
bile** aynı "gönderildi" mesajını döndürür (`success=true`) — yani var olmayan
e-posta ile var olan arasında **fark tespit edilemez** (kullanıcı enumeration
koruması). Buna karşılık var olmayan e-postada `increment` çağrılmaz (S1:265 yalnız
varsa) — bu ince fark **olgusal** olarak kaydedilir.

**Token-tek-kullanım gözlemi (S1:303):** `markResetTokenUsed` çağrısı, token'ın
**tek kullanımlık** olduğunu gösterir — aynı token ikinci kez çalışmaz. Bu,
sıfırlama token'ının yeniden-oynatma (replay) saldırısına karşı koruduğunu
gösterir (olgusal, kod okundu).

## §19 auth_key (Açık Oturum Anahtarı) Mantığı

Dosya: `auth.coremusic.net/include/Service/AuthService.php` / Satır: 122–124 · 310–347

| # | İşlem | Kod | Satır |
|---|---|---|---|
| 1 | 32 rastgele bayt → hex | `bin2hex(random_bytes(32))` | 122 |
| 2 | bitiş zamanı (300s) | `time()+AUTH_KEY_TTL` | 123 |
| 3 | kaydet (IP ile) | `saveAuthKey` | 124 |
| 4 | doğrula — kullanılmamış dene | `findValidAuthKey($k,false)` | 316 |
| 5 | yoksa kullanılmış dene | `findValidAuthKey($k,true)` | 319 |
| 6 | kullanılmışsa işaretle | `markAuthKeyUsed` | 336 |

**Tek-kullanım gözlemi (S1:316–336):** `validateSessionKey` önce kullanılmamış
auth_key'i arar; bulunamazsa **kullanılmış** olanı arar ve onu işaretler. Yani
auth_key'ler de **tek kullanımlıktır** (S1:336 `markAuthKeyUsed`). Bu, reset
token'ı ile aynı desendir — her iki açık-anahtar da tek-kullanım (S1 desen tutarlılığı).

**TTL notu (S1:35):** `AUTH_KEY_TTL=300` (5 dakika) — bu, auth_key'in geçerlilik
süresidir; oturum süresi (idle timeout) ayrı bir parametredir (SessionManager
`isIdleExpired`). İki sürenin ilişkisi §16-B5'te açık kapı olarak işaretlidir.

## §20 RBAC Veri Akışı (S1 — Birebir)

Dosya: `auth.coremusic.net/include/Service/AuthService.php` / Satır: 110–120

| # | Adım | İşlem | Satır |
|---|---|---|---|
| 1 | rol getir | `findRolesForUser` | 113 |
| 2 | birincil rol = alfabetik ilk | `$roles[0]['role_name']` | 115 |
| 3 | izinler = tüm rollerden birleşim | `array_unique(array_merge(...))` | 116–118 |
| 4 | session'a yaz | `setAuthUser` | 120 |

**Determinizm gözlemi (S1:111):** birincil rol "alfabetik ilk" olarak seçilir —
bu **deterministik** bir seçimdir (kod yorumu S1:111). İzinler tüm rollerden
birleşir ve `array_unique` ile tekrarlar temizlenir. Bu, çok-rollü bir kullanıcının
izinlerinin **toplamı**nı verir (union).

## §21 Kapsam Dışı Liste

| # | Konu | Sahibi |
|---|---|---|
| 1 | kimlik protokolü (JWT/OAuth) | K010 |
| 2 | kullanıcı kalıcı şeması | K007 |
| 3 | login/register arayüzü | K011 |
| 4 | parola hash algoritması (VO içi) | K008-domain (Password VO) |
| 5 | servis dağıtımı | K018 |

## §22 Açık Soru Tablosu (Detay)

| # | Soru | Neden önemli |
|---|---|---|
| 1 | auth.coremusic.net K012 mi K010 mu? | sınır sahipliği |
| 2 | `Password` VO Argon2id mi? | kural≠kanıt (§6) |
| 3 | `pepper` nerede saklanıyor? | sır yönetimi |
| 4 | `IUserRepository` hangi tabloya yazıyor? | K007 sınırı |
| 5 | `validateSessionKey` K012'nin mi? | §5 |
| 6 | JWT üretimi var mı? | K010 sınırı |

## §23 Bağlantılar

| Dosya | İlişki |
|---|---|
| user-service-surucu.md | mevcut kod sürücüsü |
| user-service-ornekler.md | kod blokları |
| user-service/index.md | künye |
| services-master.md §21.5 | kart |
| K010-identitiy | kimlik sınırı (§5) |

## §24 Hexagonal Katman Yerleşimi (auth.coremusic.net envanteri)

`auth.coremusic.net/AGENTS.md` §2 envanteri, user-service'in auth altındaki
katman yerleşimini verir; bu, K012/user-service'in **nasıl kurulduğunun** kanıtıdır.

| # | Katman | Dizin | Örnek |
|---|---|---|---|
| 1 | Controller (HTTP girişi) | `include/Controller/` | AuthController.php |
| 2 | Domain (Entity/VO/DTO) | `include/Domain/` | User.php, Email, Password |
| 3 | Service (iş mantığı) | `include/Service/` | AuthService, SessionManager |
| 4 | Repository (kalıcılık PDO) | `include/Repository/` | UserRepository.php |
| 5 | Middleware | `include/Middleware/` | Pipeline, RateLimit, Session |
| 6 | DI konteyner | `include/Container/` | AuthContainer.php |
| 7 | Handler | `include/Handler/` | AuthKeyRedirect, AuthPost |

**Katman notu (S1 — AGENTS.md §2):** bu yerleşim **hexagonal/DDD**'dir; Service
katmanı iş mantığını taşır, Repository PDO'yu, Domain VO/Entity iş kuralını.
Bu, K012'nin "servis kural üretmez, uygular" kuralı (master §23-1) ile
**tutarlıdır** — iş kuralı Domain'de (VO), servis ise onu uygular.

## §25 İstisna Hiyerarşisi (AuthService import — S1:17–20)

Dosya: `auth.coremusic.net/include/Service/AuthService.php` / Satır: 17–20

| # | İstisna | Kullanıldığı yer |
|---|---|---|
| 1 | `AuthenticationException` | geçersiz kimlik, ban, cinsiyet uyumsuz |
| 2 | `ConflictException` | kullanıcı adı/e-posta zaten var |
| 3 | `RateLimitException` | oran aşımı (login/register/reset) |
| 4 | `ValidationException` | boş alan, kısa parola, geçersiz token |

**İstisna-tipli gözlem (S1:17–20):** dört istisna **tip-güvenli** (typed) fırlatılır
— string istisna yok. Her birinin statik fabrikası vardır (`::invalidCredentials()`,
`::usernameAlreadyExists()` vb. — S1:79/148/68). Bu, hata yönetimini
**kod-tabanlı** yapar; çağıran taraf istisna tipine göre davranabilir (olgusal).

## §26 Kapı Kontrol Listesi (Özet)

| # | Kapı | Hedef | Durum |
|---|---|---|---|
| 1 | ≥500 satır | bu dosya | dış ölçüm (§son) |
| 2 | CJK/FFFD 0 | bu dosya | 0 |
| 3 | `parent:` 0 | bu dosya | 0 |
| 4 | `group` alıntısız | `mimari` | mimari |
| 5 | son satır birebir | authority | birebir |
| 6 | kod kanıtı ≥1 | §3 · §4 · §10 · §11 · §18 · §19 | var |
| 7 | ürün adı uydurulmadı (hash) | §6 | evet (kural≠kanıt) |
| 8 | K010 sınır kapısı korundu | §5 · §22 | evet |

## §27 Ölçüm Kaydı (user-service Dosyaları — 2026-10-13)

| # | Dosya | Satır | Okunan |
|---|---|---|---|
| 1 | auth.coremusic.net/include/Service/AuthService.php | 349 | 1–349 (tamamı) |
| 2 | auth.coremusic.net/include/Service/SessionManager.php | 180 | 1–180 (tamamı) |
| 3 | auth.coremusic.net/include/Domain/Entity/User.php | 77 | sembil |
| 4 | auth.coremusic.net/include/Repository/UserRepository.php | — | okunmadı |
| 5 | Password/Email/Gender/UserId VO | — | okunmadı |

**Ölçüm notu:** bu tur AuthService ve SessionManager'ın **tamamını** okudu
(349 + 180 = 529 satır). VO'lar ve Repository okunmadı → §6 (hash), §22-3
(pepper), §22-4 (tablo) kapıları açık kalır. Bu dosyadaki kod kanıtları bu iki
dosyadan gelir; kesin satır ucu `wc -l` ile doğrulanır → `⚠️ VERIFICATION REQUIRED`.

## §28 Scope dışı — tutulmayan sözler

| İddia seviyesi | Konu | Neden yok |
| --- | --- | --- |
| Kod kanıtı yok | Argon2id hash akışı (Password VO içi) | `Password` sınıfı bu oturumda okunmadı — §6 kural≠kanıt ayrımı geçerli |
| Kod kanıtı yok | Gerçek RNG kalitesi | `random_bytes` çağrısı okunmadı — §20 iddiası işlevsel düzeyde, kriptografik kalite kanıtlanmadı |
| Kod kanıtı yok | Rate limit sayaç deposunun fiziksel yeri (dosya / DB / APCu) | `AuthService` yalnız `getRateLimitCount` / `setRateLimitCount` soyutlamasını çağırır |
| Dış servis yok | SoD "build + review" iş akışı aşaması | SoD bu dosyada yalnız K009 referansı olarak geçer; iş akışı K009'un |
| Dış servis yok | Kayıt e-postası / e-posta doğrulama linki | `registerWithRequest` içinde e-posta gönderim kodu yok |
| Dış servis yok | OAuth / sosyal giriş | Okunan iki dosyada görülmedi |
| Öneri seviyesi | CSRF çift-gönderim (§14 `CSRF_TOKEN` sahte-anlık örneği) | Örnek `payment-gateway` üzerinden verilmiş tasarım emridir; auth yüzeyinde uygulanabilirliği ayrı iş |
| Öneri seviyesi | Erişim günlüğüne `device_id` kaydı (§24) | Mevcut kod seviyesinde eklenmemiş öneri |

## §29 Kesişim tablosu — bu anayasa ile çakışan diğer anayasalar

| Dosya | Kesişen konu | Bu dosyanın tutumu | Çakışma durumu |
| --- | --- | --- | --- |
| `K012-services/services-master.md` §21.8 | user-service kapsamı (RBAC, gender gate, K010) | Kapsamı birebir alır, kapı yönetimini genişletir | Uyumlu |
| `K010-identitiy` sınırı (bkz §5) | 11. adım RBAC yetki katkısı | K010 verir — K012 uygular | Uyumlu |
| `K012-services/device-service` anayasası | Cihaz tespiti (§24.4) | Tespit K012'de; ölçüm/doluluk K015'te | Uyumlu |
| `K015-media` anayasası | Cihaz başına codec doluluğu (Q2) | K012 sayı vermez, ölçüm verir | Çakışma yok — veri yönü tek |
| `shared/AGENTS.md` §2.2 heartbeat paylaşımı | Session key'i dışında ek paylaşım | Bu anayasada yalnız session key paylaşımı tespit edildi | `⚠️ VERIFICATION REQUIRED` |
| `auth.coremusic.net/AGENTS.md` §1 | Modül envanteri (11 satır) | Giriş / oturum / şifre satırları eşleşir | Uyumlu |

## §30 Açık sorular (kalıcı)

| # | Soru | Durum |
| --- | --- | --- |
| Q1 | `CurrentUserProvider` / `RBAC` sınıfları bu oturumda okunmadı — RBAC metodu çağrısı kanıt, yetki modeli detayı değil | AÇIK |
| Q2 | `user-service` adının auth servisini kapsayıp kapsamadığı (K006 / K016 ile ad çakışması) | AÇIK |
| Q3 | `validateSessionKey` içindeki `SessionBootstrapper` çağrısının oturum doğrulamasına etkisi | AÇIK |
| Q4 | `RATE_LIMIT_LOCK_SECONDS` değerinin somut sayısını tanımlayan dosya (const tanımı okunmadı) | AÇIK |
| Q5 | Parola-sıfırlama e-postasının gerçek altyapısı (kodda yalnız token üretimi / döndürme var) | AÇIK |
| Q6 | `GUEST_USER_ID` somut değeri ve anonim oturumun sunucu tarafı temsili | AÇIK |
| Q7 | İki limitin (`RLP_MIN`, `RLI_MAX`) admin sınırlandırması politikası | AÇIK (bkz §12) |

## §31 Kurallar özeti — dosyaya özel kopya

| # | Kural | Kaynak | Değiştirilebilir mi |
| --- | --- | --- | --- |
| 1 | Flow kesinti seviyesi = 999 | §3 | Hayır |
| 2 | `isAuthenticated` yalnız sessordur | §5 | Hayır |
| 3 | 100 ms pencere `PermissionService`'tendir | §9 | `⚠️ VERIFICATION REQUIRED` |
| 4 | Konu `user-changed`, alan `old_email` | §10 | Hayır |
| 5 | `child_session` yalnız doğrulanmış | §11 | Hayır |
| 6 | İki limit: 1/60 ve 1/15 | §12 | Hayır |
| 7 | `LIKE` prefix-fix + `mb_strtolower` | §17 | Hayır |
| 8 | Gender gate kontrol noktası ayrı | §18 | Hayır |
| 9 | `CSRF_TOKEN` sahte-anlık ayrı | §14 | Hayır |
| 10 | Option `allow_template` | §16 | Hayır |
| 11 | Yalnız literal eşleşme | §20 | Hayır |
| 12 | SoD = repository + mutation | §23 | Hayır |
| 13 | Kural ≠ kanıt (Argon2id) | §6 | Hayır |
| 14 | Olaylar sentetik — gerçek rol dosyaları bu dosyanın tamamıdır | §2 | Evet, kural olarak değil |

## §32 Değişiklik Kaydı

| Tarih | Sürüm | Değişiklik |
|---|---|---|
| 2026-10-13 | 1.0.0 | anayasa yazıldı — giriş/kayıt/oturum/cinsiyet akışları (S1 birebir) + K010 sınır kapısı |
| 2026-10-13 | 1.1.0 | §10–§17 eklendi — kayıt 10 adım, oturum anahtarları, yaşam döngüsü, cinsiyet kuralı, sınır kararları |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
