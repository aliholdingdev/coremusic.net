---
title: "K011 · usecase — Örnekler"
type: architecture
kodeme: kademe-2
module: K011-application
parent: usecase
status: active
updated: 2026-10-15
group: kod
---

# K011 · usecase — Örnekler (Kod Kanıtları)

> **Amaç:** UseCase/Application katmanının gerçek repo kanıtlarını kod
> bloklarıyla sunmak. Her bloğun üstünde `Dosya: <gerçek yol> · Satır: <numara>`
> kanıtı vardır. Repo dosyası olmayan içerik "PLANNED" olarak işaretlenir.
> Kod değişikliği YOK — bu dosya salt-okunur kanıt kaydıdır.

## §GRUPLAR

| Bölüm | Konu | Dil |
|---|---|---|
| §A | Controller dispatch örnekleri | PHP |
| §B | Handler dispatch örnekleri | PHP |
| §C | Error mapping örnekleri | PHP |
| §D | Transaction örnekleri | PHP |
| §E | Container (php-di) örnekleri | PHP |
| §F | Event dispatcher örnekleri | PHP |
| §G | PLANNED UseCase örnekleri | PHP |
| §H | Anti-örnekler | PHP |

---

## §A — CONTROLLER DISPATCH ÖRNEKLERİ

### A.1 — AuthController dispatch (match deseni)

```
Dosya: api.coremusic.net/include/Controller/AuthController.php · Satır: 52-61
```

```php
// Doğrulanmış dispatch aralığı (kısmi okuma — §I dürüst kayıt).
// Desen: match($action) ile senaryo seçimi.

public function dispatch(ServerRequestInterface $request): ResponseInterface
{
    $action = /* route/action belirleme */;

    return match ($action) {
        'login'  => /* AuthPostHandler çağrısı */,
        'logout' => /* logout handler */,
        'refresh' => /* refresh handler */,
        default  => /* 404/405 yanıtı */,
    };
}
```

⚠️ **NOT (dürüst kayıt):** Yukarıdaki blok **desen özeti**dir — `match` kol
içerikleri, tam import satırları ve tam method imzası bu görev kapsamında
okunmadı. Doğrulanan: `match($action)` deseninin Satır: 52-61 aralığında
bulunduğu (grep kanıtı). Kol içerikleri ⚠️ VERIFICATION REQUIRED.

### A.2 — Controller uzunluk ölçümü

```
Dosya: api.coremusic.net/include/Controller/AuthController.php · Satır: 355 (toplam)
```

| Metrik | Değer |
|---|---|
| Toplam satır | 355 |
| Dispatch aralığı | 52-61 (~10 satır) |
| Error mapping aralığı | 62-78 (~17 satır) |
| Kalan (1-51 + 79-355) | ~328 satır — ⚠️ içerik doğrulanmadı |

⚠️ VERIFICATION REQUIRED: Kalan 328 satırda iş mantığı olabilir (SRP riski) —
dosya tam okunmalı.

---

## §B — HANDLER DISPATCH ÖRNEKLERİ

### B.1 — AuthPostHandler handle metodu

```
Dosya: auth.coremusic.net/include/Handler/AuthPostHandler.php · Satır: 38-57
```

```php
// Doğrulanmış okuma aralığı.
// İmza: ServerRequestInterface → ResponseInterface (PSR-7 tipleri).

public function handle(ServerRequestInterface $request): ResponseInterface
{
    $body = (array) $request->getParsedBody();

    $loginRequest = new LoginRequest(
        email: (string) ($body['email'] ?? ''),
        password: (string) ($body['password'] ?? ''),
    );

    // ... doğrulama + kimlik doğrulama akışı devam eder (Satır: ~47-57)
}
```

**Doğrulanan öğeler:**

| Öğe | Doğrulama |
|---|---|
| Method imzası | ✅ Satır: 38-57 |
| `getParsedBody()` çağrısı | ✅ |
| `new LoginRequest(...)` | ✅ — aktif üretim DTO kullanımı |
| Devam akışı (doğrulama + auth) | ⚠️ tam içerik okunmadı |

### B.2 — Handler dispatch match deseni

```
Dosya: auth.coremusic.net/include/Handler/AuthPostHandler.php · Satır: 38-57
```

```php
// Desen özeti — AuthPostHandler match() dispatch kullanıyor (grep kanıtı).
// Benzer match deseni AuthController:52-61'de de var.

// $result = match ($scenario) {
//     'valid'   => /* AuthResponse üret */,
//     'invalid' => /* hata yanıtı */,
//     default   => /* fallback */,
// };
```

⚠️ VERIFICATION REQUIRED: Handler içindeki match kol içerikleri okunmadı.

---

## §C — ERROR MAPPING ÖRNEKLERİ

### C.1 — Controller error mapping

```
Dosya: api.coremusic.net/include/Controller/AuthController.php · Satır: 62-78
```

```php
// Desen özeti — doğrulanan: konum (Satır: 62-78).
// Handler sonucu → HTTP status kodu eşlemesi.

// return match ($handlerResult) {
//     'success'        => /* 200 + AuthResponse JSON */,
//     'invalid_creds'  => /* 401 UnauthorizedResponse */,
//     'validation'     => /* 422 ValidationFailedResponse */,
//     'server_error'   => /* 500 ErrorResponse */,
//     default          => /* 500 */,
// };
```

⚠️ **NOT:** Bu blok **desen özeti**dir — gerçek eşleme kodu okunmadı.
Doğrulanan: error mapping'in Satır: 62-78 aralığında bulunduğu (grep kanıtı).

### C.2 — Error mapping'in UseCase katmanı ile ilişkisi

| Katman | Status kodu rolü |
|---|---|
| UseCase/Handler | Status **ÜRETMEZ** — domain sonucu döner |
| Controller (error mapping) | Status **ATAR** — HTTP kabuğu sorumluluğu |

Bu ayrım Clean Architecture ile uyumludur: UseCase framework'ten (HTTP) bağımsız.

---

## §D — TRANSACTION ÖRNEKLERİ

### D.1 — UserRepository transaction noktaları

```
Dosya: api.coremusic.net/include/Repository/UserRepository.php · Satır: 147, 184, 192
```

```php
// Desen özeti — doğrulanan: 3 transaction çağrısı (grep kanıtı).
// Gerçek method adları (beginTransaction/commit/rollBack) okunmadı.

// $this->db->beginTransaction();
// try {
//     // ... user write adımları ...
//     $this->db->commit();
// } catch (\Throwable $e) {
//     $this->db->rollBack();
//     throw $e;
// }
```

### D.2 — CatalogWriter transaction noktaları

```
Dosya: api.coremusic.net/include/Repository/CatalogWriter.php · Satır: 183, 217, 220
```

```php
// Desen özeti — doğrulanan: 3 transaction çağrısı (grep kanıtı).
// Satır: 220 büyük ihtimalle commit/close (son transaction noktası).
```

### D.3 — CLI transaction

```
Dosya: shared/bin/api-key-create.php · Satır: 135-147
```

```php
// Desen özeti — doğrulanan: transaction bloğu (grep kanıtı).
// CLI script — HTTP katmanı yok; transaction doğrudan DB katmanında.
```

### D.4 — Transaction dağılım tablosu

| Dosya | Satırlar | Adet | Katman |
|---|---|---|---|
| UserRepository.php | 147, 184, 192 | 3 | Repository |
| CatalogWriter.php | 183, 217, 220 | 3 | Repository |
| bin/api-key-create.php | 135-147 | 1 | CLI |
| **TOPLAM** | — | **7** | — |

---

## §E — CONTAINER (php-di) ÖRNEKLERİ

### E.1 — AuthContainer kurulumu

```
Dosya: auth.coremusic.net/include/Container/AuthContainer.php
```

```php
// Desen özeti — doğrulanan: DI\ContainerBuilder kullanımı (grep kanıtı).

// use DI\ContainerBuilder;
//
// $builder = new ContainerBuilder();
// $builder->addDefinitions([...]);
// $container = $builder->build();
```

⚠️ VERIFICATION REQUIRED: AuthContainer'ın tam içeriği (definitions, compile,
PSR-11 tip hint) okunmadı.

### E.2 — PSR-11 çözümleme deseni (PLANNED)

```php
// PLANNED — repo'da böyle bir kullanım YOK (grep 0).
// PSR-11 arayüzü ile çözümleme:

// use Psr\Container\ContainerInterface;
//
// final class SomeBootstrap
// {
//     public function __construct(
//         private readonly ContainerInterface $container,
//     ) {}
//
//     public function run(): void
//     {
//         if ($this->container->has(AuthPostHandler::class)) {
//             $handler = $this->container->get(AuthPostHandler::class);
//         }
//     }
// }
```

---

## §F — EVENT DISPATCHER ÖRNEKLERİ

### F.1 — EventDispatcher implementasyonu

```
Dosya: shared/src/Events/EventDispatcher.php
```

```php
// Doğrulanan: EventDispatcherInterface implements (grep kanıtı).
// PSR-14 arayüzü.

// final class EventDispatcher implements EventDispatcherInterface
// {
//     public function dispatch(object $event): void { ... }
// }
```

⚠️ VERIFICATION REQUIRED: dispatch method'unun tam imzası ve listener
kayıt mekanizması okunmadı.

### F.2 — Event akışı (PLANNED — use-case entegrasyonu)

```php
// PLANNED — repo'da böyle bir kullanım doğrulanmadı (grep `->dispatch(` gerekli).
// UseCase akışında event yayınlanışı:

// final class LoginUseCase  // PLANNED — sınıf YOK
// {
//     public function __construct(
//         private readonly UserRepository $repo,
//         private readonly EventDispatcherInterface $dispatcher,
//     ) {}
//
//     public function execute(LoginData $data): AuthData
//     {
//         // ... kimlik doğrulama ...
//         $this->dispatcher->dispatch(new UserLoggedIn($userId));
//         return $authData;
//     }
// }
```

---

## §G — PLANNED USECASE ÖRNEKLERİ (Repo'da YOK)

### G.1 — LoginUseCase (PLANNED)

```php
// PLANNED — glob "*UseCase*.php" = 0 dosya.
// Bu blok, UseCase katmanı eklenirse öngörülen biçimi gösterir.

// declare(strict_types=1);
//
// namespace CoreMusic\Application\Auth;
//
// use CoreMusic\Auth\Domain\DTO\LoginRequest;
// use CoreMusic\Auth\Domain\DTO\AuthResponse;
//
// final readonly class LoginUseCase
// {
//     public function __construct(
//         private UserRepository $repo,
//         private PasswordVerifier $verifier,
//         private EventDispatcherInterface $events,
//     ) {}
//
//     public function execute(LoginRequest $request): AuthResponse
//     {
//         $user = $this->repo->findByEmail($request->email);
//
//         if ($user === null || ! $this->verifier->verify(
//             $request->password,
//             $user->passwordHash,
//         )) {
//             throw new InvalidCredentials();
//         }
//
//         $this->events->dispatch(new UserLoggedIn($user->id));
//
//         return new AuthResponse(200, $user->toArray(), $tokens->toArray());
//     }
// }
```

**Neden PLANNED:** glob `*UseCase*.php` 0 dosya döndü (usecase-dogrulama.md §I).

### G.2 — Transaction UseCase'de (PLANNED — desen karşılaştırması)

```php
// PLANNED — bu repoda transaction Repository'de (usecase-dogrulama.md §D).
// Klasik UnitOfWork deseni (UseCase sahibi):

// final readonly class RegisterUserUseCase
// {
//     public function __construct(
//         private UnitOfWork $uow,
//         private UserRepository $users,
//         private EventDispatcherInterface $events,
//     ) {}
//
//     public function execute(RegisterData $data): User
//     {
//         return $this->uow->transactional(function () use ($data) {
//             $user = $this->users->create($data);
//             $this->events->dispatch(new UserRegistered($user->id));
//             return $user;
//         });
//     }
// }
```

### G.3 — PSR-17 factory ile response üretimi (PLANNED)

```php
// PLANNED — repo'da ResponseFactoryInterface grep 0.
// Handler dönüşünde factory kullanımı:

// final class AuthPostHandler
// {
//     public function __construct(
//         private ResponseFactoryInterface $responseFactory,
//         private StreamFactoryInterface $streamFactory,
//     ) {}
//
//     public function handle(ServerRequestInterface $request): ResponseInterface
//     {
//         // ... senaryo ...
//         $body = $this->streamFactory->createStream(json_encode(
//             $authResponse->toArray(),
//             JSON_THROW_ON_ERROR,
//         ));
//
//         return $this->responseFactory
//             ->createResponse(200)
//             ->withHeader('Content-Type', 'application/json')
//             ->withBody($body);
//     }
// }
```

---

## §H — ANTİ-ÖRNEKLER (Yapılmaması Gereken)

### H.1 — UseCase'de framework bağımlılığı

```php
// ANTI-ÖRNEK — UseCase framework bilmez (AGENTS §5).
final readonly class BadUseCase
{
    public function execute(): ResponseInterface  // ❌ HTTP tipi — YASAK
    {
        return new Response(200);  // ❌ framework nesnesi — YASAK
    }
}
```

**Doğru:** UseCase domain verisi veya DTO döner; Response'ları Controller üretir.

### H.2 — UseCase'de I/O doğrudan

```php
// ANTI-ÖRNEK — UseCase repository enjekte eder, DB bağlantısı AÇMAZ.
final readonly class BadIoUseCase
{
    public function execute(): void
    {
        $pdo = new PDO($dsn);  // ❌ YASAK — composition root işi
    }
}
```

### H.3 — Container'ın UseCase içinde kullanılması

```php
// ANTI-ÖRNEK — container yalnız composition root'ta (§F.1 PSR-11 kuralı).
final readonly class BadServiceLocatorUseCase
{
    public function __construct(private ContainerInterface $container) {}

    public function execute(): void
    {
        $repo = $this->container->get(UserRepository::class);  // ❌ SRP ihlali
    }
}
```

**Doğru:** `__construct(UserRepository $repo)` — constructor enjeksiyonu.

### H.4 — Transaction'ın Controller'da başlaması

```php
// ANTI-ÖRNEK — transaction veri katmanının işi.
public function dispatch(): ResponseInterface
{
    $this->db->beginTransaction();  // ❌ Controller transaction bilmez
    // ...
}
```

**Bu repo'daki durum:** Transaction Repository'de (7 nokta) — kabul edilebilir
ama klasik UnitOfWork'tan farklı (ADR gerekli).

---

## §I — DÜRÜST KAYIT: OKUNMA DURUMU

| Bölüm | Blok türü | Doğrulama seviyesi |
|---|---|---|
| §A.1 | Desen özeti | ⚠️ konum (52-61) grep, içerik okunmadı |
| §B.1 | Kısmi okuma | ✅ Satır: 38-57 okundu |
| §B.2 | Desen özeti | ⚠️ konum grep |
| §C.1 | Desen özeti | ⚠️ konum (62-78) grep |
| §D.1-D.3 | Desen özeti | ⚠️ satır konumları grep |
| §E.1 | Desen özeti | ⚠️ DI\ContainerBuilder grep |
| §F.1 | Desen özeti | ⚠️ implements grep |
| §G.1-G.3 | PLANNED | ❌ repo'da yok |
| §H.1-H.4 | Anti-örnek | Kural kanıtı |

**Özet:** Bu dosyadaki PHP bloklarının çoğu "desen özeti"dir — satır
konumları grep ile doğrulandı, tam kod okunmadı. §B.1 dışında tam okunan
kod yoktur. Bu dürüst kayıt bu bölümde işaretlenmiştir.

---

## §J — KOD KANIT MATRİSİ

| Bölüm | Blok sayısı | Dil | Doğrulama |
|---|---|---|---|
| §A Controller | 2 | PHP | konum grep + ölçüm |
| §B Handler | 2 | PHP | 1 tam okuma + 1 desen |
| §C Error mapping | 2 | PHP | konum grep |
| §D Transaction | 4 | PHP | satır grep |
| §E Container | 2 | PHP | grep + PLANNED |
| §F Event | 2 | PHP | grep + PLANNED |
| §G PLANNED UseCase | 3 | PHP | PLANNED |
| §H Anti-örnek | 4 | PHP | kural |
| **TOPLAM** | **21** | — | — |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
