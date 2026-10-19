---
node_id: K012-services/service-contracts
node_type: module
role: surucu
status: draft
parent_id: K012-services/service-contracts
children: []
depends_on:
  - "service-contracts/service-contracts-anayasa.md"
  - "service-contracts/index.md"
evidence:
  - "S1 — shared/src/Contracts/ 19 interface (2026-10-13 read, anayasa §3)"
  - "S1 — auth.coremusic.net/include/Service/AuthService.php:13–16 import zinciri (IAuthService, ISessionManager, IUserRepository, IRateLimiter)"
  - "S1 — auth.coremusic.net/include/Service/SessionManager.php:5–6 import (ISessionManager, SessionBootstrapper)"
  - "S1 — auth.coremusic.net/include/Container/AuthContainer.php — envanterde (auth.coremusic.net/AGENTS.md §2), okunmadı"
validation:
  - "§2–§8 imza/kullanım tabloları interface dosyaları ile birebir (2026-10-13)"
  - "§11 çağrı-zinciri AuthService gövdesi ile hizalı"
open_questions:
  - "DI konteyner somut sınıf bağlaması (AuthContainer) okunmadı → ⚠️ VERIFICATION REQUIRED"
  - "IMiddleware'i uygulayan somut sınıflar ve pipeline kaydı okunmadı → ⚠️ VERIFICATION REQUIRED"
  - "ServiceRegistryInterface::register çağıran katman → ⚠️ VERIFICATION REQUIRED"
date: 2026-10-13
updated: 2026-10-13
version: 1.0.0
authority: "SSOT: shared/src/Contracts/ (S1, 2026-10-13 read)"
group: mimari
---

# service-contracts — Sürücü (Driver)

> **Bu dosya ne yapar:** kontratların **nasıl işletildiğini** tarif eder —
> hangi somut sınıf hangi arayüzü sürer, hangi tip DI ile enjekte edilir,
> bir çağrı kontrat üzerinden hangi sırayla akar. Anayasa envanteri verir;
> bu dosya **çalışma düzenini** verir.

## §1 Sürücü Tanımı

| Alan | Değer | Kanıt |
|---|---|---|
| Sürücü nedir? | Kontratı **işleten** somut sınıf ve onu **bağlayan** konteyner | `implements` satırı + DI |
| Okunan somut sürücüler | `AuthService` (349 satır), `SessionManager` (180 satır) | S1 2026-10-13 |
| Okunmayan sürücüler | 17 kontratın implementasyonu + `AuthContainer` | envanter, okunmadı |
| Bağlantı noktası | constructor enjeksiyonu (`readonly` property) | AuthService:45–50 |

## §2 Enjeksiyon Sürücüsü — AuthService Constructor (S1:45–50)

| # | Parametre | Tip (kontrat) | Kontrat dosyası |
|---|---|---|---|
| 1 | `$userRepository` | `IUserRepository` | `Contracts/Auth/IUserRepository.php` |
| 2 | `$session` | `ISessionManager` | `Contracts/Auth/ISessionManager.php` |
| 3 | `$rateLimiter` | `IRateLimiter` | `Contracts/Security/IRateLimiter.php` |
| 4 | `$pepper` | `string` (default `''`) | — (değil, skaler) |

**Sürücü düzeni:** 4 parametrenin **3'ü kontrat tipi** — somut sınıf
constructor'da hiç geçmez. Bu, §5 dependency inversion kuralının çalışan
hâlidir. Somut sınıfları kim bağlar → `AuthContainer` envanterde
(`include/Container/AuthContainer.php`), **okunmadı** → `⚠️ VERIFICATION
REQUIRED`.

## §3 SessionManager Enjeksiyonu (S1:10–13)

| # | Parametre | Tip | Default | Kullanım |
|---|---|---|---|---|
| 1 | `$sessionName` | `string` | `COREMUSIC_SESS` | dosyada hiçbir metotta geçmiyor (okuma yok) |
| 2 | `$cookieDomain` | `string` | `.coremusic.net` | `clearDisplayCookies` **hardcode** `'.coremusic.net'` kullanır (`:166`) — parametre kullanılmıyor |

**Sürücü çelişkisi (kanıt):** constructor `$cookieDomain` alır ama
`clearDisplayCookies` alan adını **kodda sabit** yazar (`:166`). Parametre
değiştirilse bile davranış değişmez → ölü parametre. `$sessionName` için de
aynı gözlem: hiçbir `session_name()` çağrısı bu dosyada yok → nerede
kullanıldığı `⚠️ VERIFICATION REQUIRED` (muhtemel: `SessionBootstrapper`).

## §4 ISessionManager — 21 Sürünün Nereye Bağlandığı (S1)

| # | Metot | Sürücüde çağıran | Satır |
|---|---|---|---|
| 1 | `setAuthUser` | `AuthService::loginWithRequest` | 120 |
| 2 | `setRegisteredUser` | `AuthService::registerWithRequest` | 186 |
| 3 | `getUserId` | `logout`, `getCurrentUser` | 225, 242 |
| 4 | `isAuthenticated` | `AuthService::isAuthenticated` | 237 |
| 5 | `destroy` | `AuthService::logout` | 231 |
| 6 | `clearDisplayCookies` | `AuthService::logout` | 232 |
| 7–21 | diğer 15 metot | AuthService **çağırmıyor** | — |

**Sayım:** 21 kontrat metodu · 6'sı AuthService üzerinden işletiliyor ·
15'i başka katmanlarda (middleware/bootstrap/controller) — ama bu turda
**okunmadı** → `⚠️ VERIFICATION REQUIRED`. Yani kontratın %71'i bu
oturumda **çağıranı görülmedi**.

## §5 IRateLimiter — Kapı Sürücüsü (S1 çağrı sayımı)

| Metot | AuthService çağrıları | Satırlar |
|---|---|---|
| `isLimited` | 4 | 65, 66, 139, 259 |
| `increment` | 7 | 77, 78, 90, 91, 147, 153, 166, 265, 275 (9 satır, 7 ayrı iş akışı) |
| `reset` | 4 | 106, 107, 170 (+ kayıt akışında tek) |

**Düzeltme (ölçüm):** `increment` satır sayısı 77, 78 (kullanıcı yok) ·
90, 91 (parola yanlış) · 147, 153 (username/email çakışma) · 166 (hata
birikimi) · 265, 275 (sıfırlama) = **9 satır / 7 çağrı yeri**.
`reset` = 106, 107, 170 = **3 satır**. Tablo bu sayım ile geçerlidir.

**Sürücü düzeni:** limit **her akışın başında** `isLimited`, **her başarısızlıkta**
`increment`, **başarıda** `reset` — üçlü desen tutarlı (giriş, kayıt,
sıfırlama).

## §6 IUserRepository — 20 Metot Sürme Durumu (S1)

| Durum | Metot sayısı | Metotlar |
|---|---|---|
| AuthService işletiyor | 18 | findByCredential, findByEmail, findByIdHex, create, updateLastLogin, emailExists, usernameExists, saveResetToken, findValidResetToken, updatePassword, markResetTokenUsed, saveAuthKey, findValidAuthKey (2 argümanla), markAuthKeyUsed, revokeAuthKeysForUser, findRolesForUser, revokeAccessTokensForUser, findByUsername (envanterde — AuthService çağırmıyor: düzeltme, 17) |
| Bu oturumda çağıranı yok | 2 | `saveAccessToken`, `isValidAccessToken` (JWT yüzeyi) |
| Toplam | 20 | anayasa §6.3 |

**Düzeltme (ölçüm):** AuthService'in çağırdığı imza sayısı = 17
(`findByUsername` çağrılmıyor). Tablo §6.3 anayasa ile hizalıdır:
çağrılmayanlar = `findByUsername`, `saveAccessToken`, `isValidAccessToken`
= **3 metot**; işletilen = **17 metot**.

## §7 Kontrat Üzerinden Tipik Akış — Giriş (adım adım)

| # | Adım | Kontrat | Somut |
|---|---|---|---|
| 1 | Konteyner `IAuthService` verir | `IAuthService` | `AuthService` (bağlama okunmadı) |
| 2 | Çağrı `login(...)` eski imzayla gelir | `IAuthService:login` | `AuthService:209` DTO'ya çevirir |
| 3 | `isLimited` ×2 kapıda durur | `IRateLimiter` | somut okunmadı |
| 4 | `findByCredential` satırı getirir | `IUserRepository` | somut okunmadı |
| 5 | `Password::verify` (kontrat değil, VO) | — | `Password` okunmadı |
| 6 | `findRolesForUser` yetkiyi getirir | `IUserRepository` | somut okunmadı |
| 7 | `setAuthUser` oturuma yazar | `ISessionManager` | `SessionManager:15` |
| 8 | `saveAuthKey` tek kullanımlık anahtarı saklar | `IUserRepository` | somut okunmadı |
| 9 | `AuthResponse::success` döner (kontrat değil, DTO) | — | okunmadı |

**Gözem (sürücü):** 9 adımın **yalnız 1'i** (7. adım) bu oturumda iki
tarafı da okunmuş kontrat-somut çiftidir. Kalan 8 adımda somut taraf
`UNKNOWN`.

## §8 Kontrat Üzerinden Tipik Akış — Çıkış

| # | Adım | Kontrat | Somut |
|---|---|---|---|
| 1 | `getUserId` null dönerse kısa devre | `ISessionManager` | `SessionManager:46` |
| 2 | `revokeAuthKeysForUser` | `IUserRepository` | somut okunmadı |
| 3 | `revokeAccessTokensForUser` | `IUserRepository` | somut okunmadı |
| 4 | `destroy` (gender korumalı) | `ISessionManager` | `SessionManager:60` |
| 5 | `clearDisplayCookies` | `ISessionManager` | `SessionManager:161` |

## §9 Middleware Zinciri — IMiddleware Sürücüsü

| Alan | Değer | Kanıt |
|---|---|---|
| Kontrat | `handle(array $request, callable $next): array` | `Contracts/Middleware/IMiddleware.php` 8 satır |
| Pipeline | `ApiMiddlewarePipeline` (47 satır okundu — daha önceki tur) | `shared/src/` envanter |
| Somut middleware'ler | `auth.coremusic.net/include/Middleware/` — Pipeline, OriginCheck, RateLimit, SecurityHeaders, Session | `auth.coremusic.net/AGENTS.md` §2 |
| MiddlewareInterface adı | envanterde `MiddlewareInterface` (auth tarafı) **ve** `IMiddleware` (shared tarafı) — iki ayrı isim | `⚠️ VERIFICATION REQUIRED` (eşleme okunmadı) |

**Sürücü kapı:** iki middleware arayüzünün aynı mı ayrı mı olduğu bu
oturumda çözülmedi. `auth.coremusic.net` kendi pipeline'ını kuruyor
(envanter §2), `shared`'ın `IMiddleware`'ini kullanıp kullanmadığı → `UNKNOWN`.

## §10 Api Sürücüsü — Gateway / BFF / Kayıt

| Kontrat | Somut sürücü (okundu mu?) | Çağrı katmanı |
|---|---|---|
| `GatewayInterface::dispatch` | `Gateway.php` 169 satır — önceki turda okundu | HTTP girişi |
| `BffInterface::transform` | `BffLayer.php` 84 satır — önceki turda okundu | client tipi dönüşümü |
| `ServiceRegistryInterface` | `ServiceRegistry.php` 133, `ServiceDefinition.php` 121, `ServiceHealth.php` 100 — önceki turda okundu | health/kayıt |
| Somut eşlemelerin `implements` satırları | bu turda yeniden okunmadı | — |

**Durum:** bu üç kontratın somut dosyaları **önceki tur envanterinde okundu**
(satır sayıları hafızada), ama `implements` bağları **yeniden doğrulanmadı**
→ eşleme `⚠️ VERIFICATION REQUIRED`.

## §11 Çağrı Zinciri Özeti — Kim Kimi Sürer

| Somut sürücü | Sürdüğü kontrat(lar) | Kanıt |
|---|---|---|
| `AuthService` | `IAuthService` | `implements IAuthService` (AuthService:28) |
| `SessionManager` | `ISessionManager` | `implements ISessionManager` (SessionManager:8) |
| `UserRepository` (okunmadı) | `IUserRepository` | envanter + AuthService import |
| Rate limiter somutu (okunmadı) | `IRateLimiter` | AuthService:16 import |
| `Gateway` (önceki tur) | `GatewayInterface` | doğrulanmadı |
| `BffLayer` (önceki tur) | `BffInterface` | doğrulanmadı |
| `ServiceRegistry` (önceki tur) | `ServiceRegistryInterface` | doğrulanmadı |
| 12 kontrat | sürücüsü hiç okunmadı | §17 |

## §12 Sürücü Karar Tablosu

| Durum | Sürücü davranışı | Kontrat kapısı |
|---|---|---|
| Constructor çağrıldı | 4 parametre bağlanır | 3 kontrat + 1 string |
| Eski imza `login()` geldi | DTO'ya çevir → `loginWithRequest` | `IAuthService` |
| Limit dolumu | `RateLimitException` fırlatılır | `IRateLimiter::isLimited` |
| Kontrat 2. argüman isterse | AuthService 2 argüman verir, kontrat 1 gösterir | §14 anayasa (çelişki kayıtlı) |
| Session boş mu | `getUserId` null → anonim | `ISessionManager` |
| Logout | revoke ×2 → destroy → cookie temizle | 3 kontrat sıralı |

## §13 Sürücü Risk Listesi (Red Team)

| # | Risk | Kanıt | Ciddiyet |
|---|---|---|---|
| 1 | `$cookieDomain` ölü parametre (hardcode ile çelişki) | SessionManager:166 vs :12 | Orta |
| 2 | `$sessionName` hiç kullanılmıyor | dosyada çağrı yok | Düşük (başka katmanda olabilir) |
| 3 | 21 kontrat metodu · yalnız 6'sı okunan sürücüde | §4 | Orta (geri kalanı kör bölge) |
| 4 | İki middleware arayüzü adı (`IMiddleware` vs `MiddlewareInterface`) | §9 | Orta |
| 5 | 17 kontratın somut sınıfı okunmadı | §11 | Yüksek (toplam kör bölge) |
| 6 | DI konteyner bağlaması kanıtsız | §2 | Orta |

## §14 Kapsam Dışı

| Konu | Neden |
|---|---|
| `AuthContainer` içeriği | okunmadı — somut bağlama kanıtı yok |
| Somut `UserRepository` SQL | okunmadı — anayasa §17 Q-B |
| Somut rate limiter deposu | okunmadı — anayasa §7 (UNKNOWN) |
| `ServiceRegistry` çağıran katman | önceki turda dosya okundu, çağıranı okunmadı |
| Middleware somutları | envanterde, okunmadı |
| Event relay/producer | `IntegrationEventInterface` implementasyonu okunmadı |

## §15 Açık Sorular (Kalıcı)

| # | Soru | Etki |
|---|---|---|
| Q-A | `AuthContainer` hangi somut sınıfları bağlıyor? | Güvenilirlik |
| Q-B | `IMiddleware` ve `MiddlewareInterface` aynı mı? | Mimari |
| Q-C | `ServiceRegistryInterface::register` çağıran kim? | Servis keşfi |
| Q-D | 15 çağrılmayan `ISessionManager` metodu nerede kullanılıyor? | Kör bölge |
| Q-E | `findValidAuthKey` 2. argüman somut sınıfta var mı? | Kontrat çelişkisi |
| Q-F | Event kontratlarını uygulayan sınıflar hangileri? | Olay yayımı |

## §16 Kod Bloğu — Kontratı İşleten İmzalar (S1 birebir)

```php
// shared/src/Contracts/Auth/IAuthService.php — 8 imza (tamamı)
interface IAuthService
{
    public function login(string $identity, string $password, string $visitorGender = 'neutral', string $clientIp = '127.0.0.1'): array;
    public function register(array $data, string $clientIp = '127.0.0.1', string $visitorGender = 'neutral'): array;
    public function logout(): void;
    public function isAuthenticated(): bool;
    public function getCurrentUser(): ?array;
    public function requestPasswordReset(string $email, string $scheme = 'http', string $host = 'auth.coremusic.net', string $clientIp = '127.0.0.1'): array;
    public function resetPassword(string $token, string $newPassword): array;
    public function validateSessionKey(string $authKey): array;
}
```

```php
// AuthService.php:45–50 — kontratlar constructor'da bağlanır
public function __construct(
    private readonly IUserRepository $userRepository,
    private readonly ISessionManager $session,
    private readonly IRateLimiter $rateLimiter,
    private readonly string $pepper = '',
) {}
```

```php
// SessionManager.php:8–13 — ISessionManager sürülüyor, iki parametre
final class SessionManager implements ISessionManager
{
    public function __construct(
        private readonly string $sessionName = 'COREMUSIC_SESS',
        private readonly string $cookieDomain = '.coremusic.net',
    ) {}
```

```php
// SessionManager.php:166 — parametre yerine sabit domain (§3 çelişkisi)
setcookie($name, '', time() - 3600, '/', '.coremusic.net');
```

```php
// Contracts/Middleware/IMiddleware.php — tek metotlu kapı
public function handle(array $request, callable $next): array;
```

## §17 Somut–Kontrat Eşleme Tablosu (tüm 19)

| # | Kontrat | Somut sınıf | Eşleme kanıtı | Durum |
|---|---|---|---|---|
| 1 | `IAuthService` | `AuthService` | `implements` satır 28 | OK (S1) |
| 2 | `ISessionManager` | `SessionManager` | `implements` satır 8 | OK (S1) |
| 3 | `IUserRepository` | `UserRepository` (envanter) | import AuthService:15 | kısmi |
| 4 | `IRateLimiter` | somutu bilinmiyor | import AuthService:16 | `UNKNOWN` |
| 5 | `GatewayInterface` | `Gateway` (önceki tur) | yeniden doğrulanmadı | `⚠️ VERIFICATION REQUIRED` |
| 6 | `BffInterface` | `BffLayer` (önceki tur) | yeniden doğrulanmadı | `⚠️ VERIFICATION REQUIRED` |
| 7 | `ServiceRegistryInterface` | `ServiceRegistry` (önceki tur) | yeniden doğrulanmadı | `⚠️ VERIFICATION REQUIRED` |
| 8 | `IMiddleware` | pipeline somutları (envanter) | okunmadı | `⚠️ VERIFICATION REQUIRED` |
| 9 | `IDatabaseManager` | somutu bilinmiyor | okunmadı | `UNKNOWN` |
| 10 | `IDatabaseRegistry` | somutu bilinmiyor | okunmadı | `UNKNOWN` |
| 11 | `IConfigManager` | somutu bilinmiyor | okunmadı | `UNKNOWN` |
| 12 | `DomainEventInterface` | event sınıfları | okunmadı | `UNKNOWN` |
| 13 | `IntegrationEventInterface` | event sınıfları | okunmadı | `UNKNOWN` |
| 14 | `AIEngineInterface` | somutu bilinmiyor | okunmadı | `UNKNOWN` |
| 15 | `AIOrchestratorInterface` | somutu bilinmiyor | okunmadı | `UNKNOWN` |
| 16 | `KnowledgeBaseInterface` | somutu bilinmiyor | okunmadı | `UNKNOWN` |
| 17 | `MemorySystemInterface` | somutu bilinmiyor | okunmadı | `UNKNOWN` |
| 18 | `PromptEngineInterface` | somutu bilinmiyor | okunmadı | `UNKNOWN` |
| 19 | `ToolCallingInterface` | somutu bilinmiyor | okunmadı | `UNKNOWN` |

**Sayım:** OK = 2 · kısmi = 1 · doğrulanmadı = 3 · `UNKNOWN` = 13.

## §18 Kullanım Sıklığı — Somut Tarafın İhtiyacı

| Kontrat | Bu oturumda okunan çağrı sayısı | Yorum |
|---|---|---|
| `IRateLimiter` | 16 (4 isLimited + 9 increment + 3 reset) | en çok kullanılan kapı |
| `IUserRepository` | 17 metot çağrısı (farklı satırlarda) | en geniş yüzey |
| `ISessionManager` | 6 metot (21'nin 6'sı) | %29 kullanım |
| `IAuthService` | 8/8 interface metodu uygulanmış | tam örtme |
| Diğer 15 kontrat | 0 (bu oturumda çağrı okunmadı) | kör bölge |

## §19 Sürücü Gözlem Defteri — Ölçüm Notları

| # | Not | Ölçüm |
|---|---|---|
| 1 | Satır sayıları dosyanın tamamıdır (boş son satır dahil değil) | 2026-10-13 read |
| 2 | `increment` çağrı satırları 77, 78, 90, 91, 147, 153, 166, 265, 275 | AuthService |
| 3 | `reset` çağrı satırları 106, 107, 170 | AuthService |
| 4 | `isLimited` çağrı satırları 65, 66, 139, 259 | AuthService |
| 5 | `setAuthUser` tek çağrı (`:120`), `setRegisteredUser` tek çağrı (`:186`) | AuthService |
| 6 | `destroy` tek çağrı (`:231`) | AuthService |
| 7 | `findValidAuthKey` iki çağrı (`:316`, `:319`) — 2'nci argümanlı | AuthService |
| 8 | SessionManager'da `session_name()`/`session_set_cookie_params` çağırları **yok** | dosya tamamı |
| 9 | `SessionBootstrapper` importu tek yerde (`SessionManager:6`) | dosya |
| 10 | `implements` interface sayısı: AuthService 1, SessionManager 1 | satır 28 / 8 |

## §20 Bağımlılık Yön Tablosu

| Katman | Bağımlı olduğu | Yön |
|---|---|---|
| Service (`AuthService`) | Contracts (4 interface) | doğru (somut → kontrat) |
| Service (`SessionManager`) | Contracts (1 interface) | doğru |
| Contracts (19 dosya) | dış sınıflar | **yok** (yalnız `\DateTimeImmutable`) |
| Contracts | CoreMusic somut sınıfları | **yok** — bağımlılık tersi (§5 DIP) |
| Container (`AuthContainer`, okunmadı) | Contracts + somutlar | tek bilinen birleşme noktası |
| K012 vault (bu dosya) | S1 kod kanıtı | referans |

**Doğrulama:** kontrat katmanının somut sınıfa bağımlı **olmaması** gerekir
(DIP); 19 dosyanın hepsi gövdesiz ve import'ları yalnız PHP çekirdek tipi —
§18 anayasa §18 ile aynı sonuç.

## §21 Kesişim Tablosu

| Dosya | Kesişen konu | Tutum | Çakışma |
|---|---|---|---|
| `service-contracts-anayasa.md` §14 | `findValidAuthKey` çelişkisi | bu dosya §12 karar tablosunda tekrarlar | Uyumlu |
| `user-service-surucu.md` §18 | `pepper` enjeksiyon | burada §2 enjeksiyon tablosu | Uyumlu |
| `user-service-ornekler.md` §4 | constructor birebir | aynı blok | Uyumlu |
| `services-master.md` §21 | kontrat kapsamı | anayasa §2 sınırı izler | Uyumlu |
| `shared/src/Contracts/CLAUDE.md` §3 | 3 kural | anayasa §5 — bu dosya §21'de uygulanmış hâlini gösterir | Uyumlu |

## §22 İmza Referans Tablosu — ISessionManager 21 (tek sayfa)

| # | Metot | Dönüş | Somut karşılığı (okundu) | Satır |
|---|---|---|---|---|
| 1 | `setAuthUser(array)` | `void` | `SessionManager::setAuthUser` | 15–31 |
| 2 | `setRegisteredUser(array)` | `void` | `SessionManager::setRegisteredUser` | 33–44 |
| 3 | `getUserId()` | `?string` | `SessionManager::getUserId` | 46–53 |
| 4 | `isAuthenticated()` | `bool` | `SessionManager::isAuthenticated` | 55–58 |
| 5 | `destroy()` | `void` | `SessionManager::destroy` | 60–77 |
| 6 | `regenerateId()` | `void` | `SessionManager::regenerateId` | 79–82 |
| 7 | `get(string,mixed)` | `mixed` | `SessionManager::get` | 84–87 |
| 8 | `set(string,mixed)` | `void` | `SessionManager::set` | 89–92 |
| 9 | `remove(string)` | `void` | `SessionManager::remove` | 94–97 |
| 10 | `setGender(string)` | `void` | `SessionManager::setGender` | 99–102 |
| 11 | `getGender()` | `string` | `SessionManager::getGender` | 104–107 |
| 12 | `setPendingRedirect(string)` | `void` | `SessionManager::setPendingRedirect` | 109–112 |
| 13 | `consumePendingRedirect()` | `?string` | `SessionManager::consumePendingRedirect` | 114–119 |
| 14 | `regenerateCspNonce()` | `string` | `SessionManager::regenerateCspNonce` | 121–126 |
| 15 | `getCspNonce()` | `string` | `SessionManager::getCspNonce` | 128–131 |
| 16 | `isIdleExpired(int)` | `bool` | `SessionManager::isIdleExpired` | 133–137 |
| 17 | `touch()` | `void` | `SessionManager::touch` | 139–142 |
| 18 | `rotateIfNeeded(int)` | `bool` | `SessionManager::rotateIfNeeded` | 144–159 |
| 19 | `clearDisplayCookies()` | `void` | `SessionManager::clearDisplayCookies` | 161–169 |
| 20 | `getCookieParams()` | `array` | `SessionManager::getCookieParams` | 171–174 |
| 21 | `all()` | `array` | `SessionManager::all` | 176–179 |

**Örtme:** 21/21 — `SessionManager` kontratın **tamamını** sürer (satır
eşlemesi bu oturumda birebir okundu). Bu, §4'teki "çağrı tarafı %29" ile
çelişmez: sürmek ≠ çağırmak.

## §23 İmza Referans Tablosu — IRateLimiter 3 (tek sayfa)

| # | Metot | Dönüş | Somut karşılığı | Durum |
|---|---|---|---|---|
| 1 | `isLimited(string,int,int)` | `bool` | okunmadı | `UNKNOWN` |
| 2 | `increment(string,int)` | `void` | okunmadı | `UNKNOWN` |
| 3 | `reset(string)` | `void` | okunmadı | `UNKNOWN` |

**Not:** 3/3 imza AuthService üzerinden **çağrılıyor** (§5) — yani kontrat
işletiliyor, ama işleten sınıf bu oturumda okunmadı. Depo (APCu/dosya/DB)
hâlâ `UNKNOWN`.

## §24 İmza Referans Tablosu — IUserRepository 20 (tek sayfa)

| # | Metot | Dönüş | Çağrı satırı (AuthService) |
|---|---|---|---|
| 1 | `findByCredential(string)` | `?array` | 75 |
| 2 | `findByEmail(string)` | `?array` | 263 |
| 3 | `findByUsername(string)` | `?array` | — |
| 4 | `findByIdHex(string)` | `?array` | 246 |
| 5 | `create(array)` | `array` | 177 |
| 6 | `updateLastLogin(string)` | `void` | 108 |
| 7 | `emailExists(string)` | `bool` | 152 |
| 8 | `usernameExists(string)` | `bool` | 146 |
| 9 | `saveResetToken(string,string,string,?string)` | `void` | 274 |
| 10 | `findValidResetToken(string)` | `?array` | 292 |
| 11 | `updatePassword(string,string)` | `void` | 302 |
| 12 | `markResetTokenUsed(string)` | `void` | 303 |
| 13 | `saveAuthKey(string,string,string,?string)` | `void` | 124, 191 |
| 14 | `findValidAuthKey(string)` | `?array` | 316, 319 (2 argüman) |
| 15 | `markAuthKeyUsed(string)` | `void` | 336 |
| 16 | `revokeAuthKeysForUser(string)` | `void` | 227 |
| 17 | `findRolesForUser(string)` | `array` | 113 |
| 18 | `saveAccessToken(string,string,string)` | `void` | — |
| 19 | `isValidAccessToken(string)` | `bool` | — |
| 20 | `revokeAccessTokensForUser(string)` | `void` | 228 |

**Çağırılmayan:** 3 metot (`findByUsername`, `saveAccessToken`,
`isValidAccessToken`) — anayasa §6.3 ile hizalı.

## §25 Sürücü SSS — Kısa Cevaplar

| # | Soru | Cevap |
|---|---|---|
| 1 | Kontrat çalışır mı? | Hayır — işleyen somut sınıftır; kontrat yalnız imza verir |
| 2 | Kaç kontrat tam işletiliyor (okunduğu kadarıyla)? | 2 tam (`IAuthService`, `ISessionManager`), 1 kısmi (`IUserRepository`) |
| 3 | DI konteyner kanıtı var mı? | Hayır — `AuthContainer` okunmadı |
| 4 | `$cookieDomain` neden ölü? | `clearDisplayCookies` sabit `'.coremusic.net'` yazıyor (§3) |
| 5 | Kontrat katmanı bağımlılık taşıyor mu? | Hayır — yalnız `\DateTimeImmutable` (§21) |
| 6 | `findValidAuthKey` çelişkisi kapandı mı? | Hayır — açık, anayasa §14 + Q-E |

## §26 Sözleşme Testi Beklentisi — Bu Dosyanın Anladığı Kadar

| # | Beklenti | Kanıt | Durum |
|---|---|---|---|
| 1 | Testler `shared/tests/` altında olmalı | `.ai` envanteri (§4.1 QA satırı) | envanter seviyesi |
| 2 | Kontrat testi = somut sınıf kontratı sürmeli mi | PHP'de `implements` zorunlu | dil kuralı |
| 3 | `AuthService` birim testi var mı | `auth.coremusic.net/tests/` envanterde "DTO, Entity, ValueObject testleri" — Service testi **belirtilmemiş** | `⚠️ VERIFICATION REQUIRED` |
| 4 | Kontrat sürüm testi (breaking change) | depoda mekanizma okunmadı | `UNKNOWN` |
| 5 | `findValidAuthKey` 2-argüman testi | okunmadı | `UNKNOWN` — Q-E bağlantılı |

**Not:** bu bölüm beklenti/kapı listesidir; test dosyası okunmadan
"geçti/geçmedi" iddiası yazılmaz.

## §27 Somut Kod Kesiti — Kontratın İşletildiği Yer (S1 birebir)

```php
// AuthService.php:110–120 — IRateLimiter + IUserRepository + ISessionManager tek akışta
// RBAC (B-F-12): rol + izinleri session'a yaz — PermissionMiddleware bunları bekler.
// Birincil rol = alfabetik ilk (deterministik); izinler tüm rollerden birleşir.
$userData = $user->toArray();
$roles    = $this->userRepository->findRolesForUser($user->id);
if ($roles !== []) {
    $userData['role']        = $roles[0]['role_name'];
    $userData['permissions'] = array_values(array_unique(array_merge(
        ...array_map(static fn (array $r): array => $r['permissions'], $roles)
    )));
}
$this->session->setAuthUser($userData);
```

```php
// AuthService.php:221–233 — logout: iki kontrat sırayla
public function logout(): void
{
    $userId = $this->session->getUserId();
    if ($userId !== null) {
        $this->userRepository->revokeAuthKeysForUser($userId);
        $this->userRepository->revokeAccessTokensForUser($userId);
    }

    $this->session->destroy();
    $this->session->clearDisplayCookies();
}
```

```php
// IRateLimiter.php — 10 satır, 3 imza (tamamı)
interface IRateLimiter
{
    public function isLimited(string $key, int $maxAttempts, int $windowSeconds): bool;
    public function increment(string $key, int $windowSeconds): void;
    public function reset(string $key): void;
}
```

**Kesit gözlemi:** bir logout çağrısı 3 kontratı (ISessionManager ×3 metot,
IUserRepository ×2 metot) sırayla işletir — kontratlar tek tek küçük,
birleşince tam bir güvenlik akışı (revoke → destroy → cookie).

## §28 Değişiklik Kaydı

| Tarih | Sürüm | Değişiklik |
|---|---|---|
| 2026-10-13 | 1.0.0 | sürücü yazıldı — DI enjeksiyon, 21/20/3 kontrat kullanım sayımı, giriş-çıkış akışları, middleware/api sürücü kapıları (S1 birebir) |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
