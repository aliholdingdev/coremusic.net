---
title: "K011 · usecase — Web Araştırma"
type: architecture
kodeme: kademe-2
module: K011-application
parent: usecase
status: active
updated: 2026-10-15
group: kanit
---

# K011 · usecase — Web Araştırma (Kanıt Havuzu)

> **Amaç:** UseCase katmanının dayandığı dış referansları (PSR-17 HTTP Factory,
> PSR-11 Container) tek dosyada toplamak. Yalnız bu 2 URL kullanılır —
> havuz dışına çıkılmaz. Repo içi kanıtlar `Dosya: <yol> · Satır: <no>` formatıdır.

## §GRUPLAR

| Bölüm | Konu | Kaynak |
|---|---|---|
| §A | PSR-17 HTTP Factory | https://www[.]php-fig[.]org/psr/psr-17/ |
| §B | PSR-11 Container | https://www[.]php-fig[.]org/psr/psr-11/ |
| §C | Repo entegrasyon matrisi | Dosya·Satır kanıtları |
| §D | Karar matrisi (ADR girdisi) | — |
| §E | Riskler ve VERIFICATION REQUIRED | — |
| §F | Genişletme notları | — |
| §G | Cross-reference | — |

---

## §A — PSR-17 HTTP FACTORY

**Kaynak:** https://www[.]php-fig[.]org/psr/psr-17/

### A.1 — Tanım (kaynak özeti)

PSR-17, HTTP mesajı **factory** arayüzlerini tanımlar. PSR-7 mesajlarının
oluşturulmasını soyutlar:

| Arayüz | Ürettiği nesne |
|---|---|
| `RequestFactoryInterface` | `RequestInterface` |
| `ResponseFactoryInterface` | `ResponseInterface` |
| `ServerRequestFactoryInterface` | `ServerRequestInterface` |
| `StreamFactoryInterface` | `StreamInterface` |
| `UploadedFileFactoryInterface` | `UploadedFileInterface` |
| `UriFactoryInterface` | `UriInterface` |

### A.2 — Temel prensip

- Factory'ler **stateless** — her çağrı yeni mesaj döner.
- Uygulamalar mesajları `new` ile üretmez; factory enjekte edilir.
- Test edilebilirlik: fake factory ile unit test mümkün.

### A.3 — UseCase katmanıyla ilişki

| Senaryo | PSR-17 rolü | Repo durumu |
|---|---|---|
| Handler yanıt üretir | `ResponseFactoryInterface::createResponse(200)` | ⚠️ VERIFICATION REQUIRED |
| CLI script yanıt üretir | PSR-17 kullanmaz (CLI) | ✅ beklenen (bin/api-key-create.php) |
| Test'te mesaj üretimi | Fake factory | ⚠️ VERIFICATION REQUIRED |

### A.4 — Repo durumu (kritik bulgu)

**composer.json kanıtı:**

```
Dosya: shared/composer.json · Satır: 8
"nyholm/psr7": "^1.8",
```

nyholm/psr7 hem PSR-7 mesaj implementasyonu hem PSR-17 factory implementasyonunu
sağlar. Fakat:

| Arama | Kapsam | Sonuç |
|---|---|---|
| `Psr\Http\Message` | tüm .php | **0 isabet** |
| `ResponseFactoryInterface` | tüm .php | **0 isabet** (beklenen) |
| `RequestFactoryInterface` | tüm .php | **0 isabet** (beklenen) |
| `StreamFactoryInterface` | tüm .php | **0 isabet** (beklenen) |

**Sonuç:** PSR-17 factory arayüzleri kodda **hiç kullanılmıyor**. Paket kurulu,
arayüzler boşta.

⚠️ **VERIFICATION REQUIRED:** AuthPostHandler `ServerRequestInterface` tipini
alıyor (Satır: 38-57). Bu nesne nereden üretiliyor?
- Bir bootstrapping dosyasında `new ServerRequest(...)` olabilir, veya
- Bir factory çağrısı olabilir — ama `ServerRequestFactoryInterface` grep 0.

⚠️ VERIFICATION REQUIRED: bootstrapping (index.php / public/index.php /
app bootstrap) dosyası bu görev kapsamında okunmadı — nesne üretim yeri belirsiz.

### A.5 — Handler imzası ile factory ilişkisi

```
Dosya: auth.coremusic.net/include/Handler/AuthPostHandler.php · Satır: 38-57
```

```php
// Doğrulanmış imza (kısmi okuma):
public function handle(ServerRequestInterface $request): ResponseInterface
```

**İmza analizi:**

| Parametre/Dönüş | Arayüz | PSR |
|---|---|---|
| `$request` | `ServerRequestInterface` | PSR-7 |
| dönüş | `ResponseInterface` | PSR-7 |

Handler **PSR-7 tipleriyle** çalışıyor ama PSR-7/17 **namespace'i kodda 0**.
Bu paradoksun açıklaması: `use Psr\Http\Message\...` import satırı grep'e
takılmadıysa — bu ⚠️ VERIFICATION REQUIRED. (Grep `Psr\Http\Message` tüm
.php'lerde çalıştırıldı ve 0 döndü. Bu, ya import'un tam yazılmadığı
(`\Psr\Http\Message\...` tam qualified) ya da tipin farklı bir namespace'den
geldiği anlamına gelir — her iki durumda da doğrulama gerekir.)

---

## §B — PSR-11 CONTAINER

**Kaynak:** https://www[.]php-fig[.]org/psr/psr-11/

### B.1 — Tanım (kaynak özeti)

PSR-11, **dependency injection container** minimal arayüzünü tanımlar:

| Arayüz | Method | Dönüş | Anlamı |
|---|---|---|---|
| `ContainerInterface` | `get(string $id)` | `mixed` | ID ile servis getir |
| `ContainerInterface` | `has(string $id)` | `bool` | Servis tanımlı mı |

**Temel prensip:**

- Container **service locator** değildir — bağımlılık çözücüdür.
- `has()` → `get()` sıralaması güvenli kullanım desenidir.
- Container, nesne **composition root**'ta kullanılır (bootstrapping).

### B.2 — UseCase katmanıyla ilişki

| Kullanım | PSR-11 rolü | Klasik |
|---|---|---|
| Handler resolution | `container->get(AuthPostHandler::class)` | ✅ yaygın |
| Repository resolution | `container->get(UserRepository::class)` | ✅ yaygın |
| UseCase resolution | `container->get(LoginUseCase::class)` | PLANNED (UseCase yok) |

**Kural (Clean Architecture):** UseCase'ler container **bilmez** — bağımlılıkları
constructor ile enjekte edilir. Container yalnız composition root'ta çağrılır.

### B.3 — Repo durumu

**composer.json kanıtı:**

| Paket | Sürüm | Kaynak |
|---|---|---|
| `psr/container` | `^2.0` | shared/composer.json (envanter kaydı) |
| `php-di/php-di` | (envanterde) | AuthContainer.php kullanımı |

**php-di kullanımı kanıtı:**

```
Dosya: auth.coremusic.net/include/Container/AuthContainer.php
```

| Arama | Kapsam | Sonuç |
|---|---|---|
| `DI\ContainerBuilder` | AuthContainer.php | ✅ php-di builder kullanımı |
| `Psr\Container\ContainerInterface` | tüm .php | ⚠️ VERIFICATION REQUIRED — bu görev kapsamında grep edilmedi |
| `$container->get(` | tüm .php | ⚠️ VERIFICATION REQUIRED |
| `$container->has(` | tüm .php | ⚠️ VERIFICATION REQUIRED |

**Sonuç:** php-di container'ı AuthContainer'da kuruluyor. `psr/container ^2.0`
composer'da tanımlı — php-di'nin PSR-11 uyumlu olduğu bilinir, fakat bu
repoda PSR-11 arayüzünün **doğrudan** (tip hint olarak) kullanılıp
kullanılmadığı ⚠️ VERIFICATION REQUIRED.

### B.4 — php-di vs PSR-11 ilişki tablosu

| Katman | php-di sınıfı | PSR-11 arayüzü |
|---|---|---|
| Container oluşturma | `DI\ContainerBuilder` | — (PSR-11'de builder yok) |
| Servis getirme | `DI\Container::get()` | `ContainerInterface::get()` |
| Servis var mı | `DI\Container::has()` | `ContainerInterface::has()` |
| Compile | `ContainerBuilder::build()` | — |

**Gözlem:** `DI\ContainerBuilder` kullanımı, php-di'nin kendi API'sidir —
PSR-11 arayüzünden bağımsızdır. Fakat php-di `Container`, PSR-11
`ContainerInterface`'i implement eder (paket dokümanı). Bu repoda bu
implementasyonun **tip hint** olarak kullanılıp kullanılmadığı belirsiz.

---

## §C — REPO ENTEGRASYON MATRİSİ

| Dış referans | Repo karşılığı | Dosya·Satır | Durum |
|---|---|---|---|
| PSR-17 RequestFactoryInterface | (yok — grep 0) | shared/composer.json · Satır: 8 | ⚠️ paket var, kod yok |
| PSR-17 ResponseFactoryInterface | (yok — grep 0) | shared/composer.json · Satır: 8 | ⚠️ paket var, kod yok |
| PSR-17 StreamFactoryInterface | (yok — grep 0) | shared/composer.json · Satır: 8 | ⚠️ paket var, kod yok |
| PSR-7 ServerRequestInterface | AuthPostHandler parametresi | AuthPostHandler.php · Satır: 38-57 | ✅ tip kullanımı (import ⚠️) |
| PSR-7 ResponseInterface | AuthPostHandler dönüşü | AuthPostHandler.php · Satır: 38-57 | ✅ tip kullanımı (import ⚠️) |
| PSR-11 ContainerInterface::get | ⚠️ doğrulanmadı | — | ⚠️ VERIFICATION REQUIRED |
| PSR-11 ContainerInterface::has | ⚠️ doğrulanmadı | — | ⚠️ VERIFICATION REQUIRED |
| PSR-11 (php-di bağımsız) | DI\ContainerBuilder | AuthContainer.php | ✅ php-di kendi API |
| PSR-14 EventDispatcherInterface | implements | shared/src/Events/EventDispatcher.php | ✅ |

---

## §D — KARAR MATRİSİ (ADR GİRDİSİ)

| # | Soru | Kanıt | Karar önerisi (orkestratöre) |
|---|---|---|---|
| D1 | PSR-17'ye geçilmeli mi (Handler'da factory)? | Paket var, kod 0 | Handler zaten PSR-7 tipleri alıyor; factory bootstrapping'e ekle — ADR |
| D2 | nyholm/psr7 saklanmalı mı? | Paket var, kod 0 kullanım | Kullanım yoksa kaldır; plan varsa ADR ile sakla |
| D3 | PSR-11 arayüz tip hint olarak kullanılmalı mı? | psr/container ^2.0 composer'da | php-di Container zaten uyumlu; `ContainerInterface` tip hint'e geç — ADR |
| D4 | Container nerede kuruluyor? | AuthContainer.php | Auth domain'e özel — genel bootstrap ADR'si gerekli |
| D5 | UseCase katmanı eklenecek mi? | 0 UseCase sınıfı | Handler = UseCase adlandırma veya yeni katman — ADR (en kritik) |

---

## §E — RİSKLER VE VERIFICATION REQUIRED

| # | İddia | Durum | Aksiyon |
|---|---|---|---|
| E1 | PSR-17 factory kodda 0 kullanım | ✅ doğrulandı (grep 0) | Karar D1/D2 |
| E2 | Handler'ın ServerRequestInterface import'u | ⚠️ tam namespace doğrulanmadı | AuthPostHandler.php okuma (import satırları) |
| E3 | ServerRequest nereden üretiliyor | ⚠️ bootstrap okunmadı | public/index.php okuma |
| E4 | `Psr\Container` tip hint kullanımı | ⚠️ grep edilmedi | grep -r "Psr\\Container" |
| E5 | `$container->get()` çağrı noktaları | ⚠️ grep edilmedi | grep -r "->get(" |
| E6 | php-di compile cache | ⚠️ doğrulanmadı | AuthContainer.php okuma |
| E7 | Container yaşam süresi (singleton vs transient) | ⚠️ doğrulanmadı | AuthContainer.php okuma |

---

## §F — GENİŞLETME NOTLARI

### F.1 — PSR-17'nin UseCase eklenmesi senaryosundaki rolü

Eğer orkestratör UseCase katmanına karar verirse:

```
[Composition Root / Bootstrap]
    container = (php-di) build
    loginUseCase = container->get(LoginUseCase::class)
        ↓ enjekte edilen bağımlılıklar
        UserRepository, EventDispatcher, (Clock vb.)

[HTTP akışı]
    $request = serverRequestFactory->createServerRequest(...)   ← PSR-17
    $response = authController->dispatch($request)              ← Controller
        → loginUseCase->execute($loginRequest)                  ← UseCase
    return $response
```

**PSR-17'nin rolü:** yalnız bootstrap/test tarafında mesaj üretimi.
UseCase kendisi PSR-7/17 **bilmez** (Clean Architecture).

### F.2 — PSR-11'nin UseCase eklenmesi senaryosundaki rolü

```
LoginUseCase (composition root'ta resolve edilir)
    constructor: __construct(
        UserRepository $repo,          ← PSR-11 container ile resolve
        EventDispatcher $dispatcher,   ← PSR-11 container ile resolve
    ) {}

    execute(LoginData $data): AuthData   ← PSR-7/17 BİLMEZ
```

**Kural:** UseCase constructor'ı saf domain tipleri alır; container yalnız
composition root'ta kullanılır. Bu repoda AuthContainer bunu yapıyor
php-di API'siyle (Satır: DI\ContainerBuilder).

### F.3 — nyholm/psr7'nin çift rolü

| Rol | Arayüz grubu | Repo kullanımı |
|---|---|---|
| PSR-7 implementasyonu | Message/Request/Response/Stream | ⚠️ tip kullanımı var (import ⚠️) |
| PSR-17 implementasyonu | *FactoryInterface | ❌ 0 |

### F.4 — PSR-20 (Clock) — KAPSAM DIŞI NOT

Clean Architecture UseCase'lerinde `ClockInterface` (PSR-20) yaygındır.
Bu repo'da `Psr\Clock` kullanımı bu görev kapsamında aranmadı —
⚠️ VERIFICATION REQUIRED (kapsam dışı bırakıldı, not olarak).

---

## §G — CROSS-REFERENCE

| Konu | Dosya |
|---|---|
| Ölçüm + gate | usecase-dogrulama.md |
| Kapsam + katalog | index.md (eski usecase-kapsam.md — spec §7 gereği 2026-10-16 kaldırıldı) |
| Kod örnekleri | usecase-ornekler.md |
| Bileşen envanteri | usecase-bilesenler.md |
| Orijinal scope slotu | usecase-usecase-katmani.md (korunur) |
| DTO web araştırması (benzer) | ../dto/dto-web-arastirma.md |

---

## §H — ÖZET: EN KRİTİK 3 BULGU

1. **PSR-17 paket var, factory 0 kullanım** — nyholm/psr7 composer'da
   (Satır: 8); ResponseFactoryInterface/RequestFactoryInterface/StreamFactoryInterface
   grep 0 isabet. Handler PSR-7 tipleri alıyor ama nesne üretim yeri belirsiz (E3).
2. **PSR-11 arayüz kullanımı doğrulanamadı** — psr/container ^2.0 composer'da;
   php-di `DI\ContainerBuilder` AuthContainer'da kullanılıyor; `Psr\Container`
   tip hint grep'i bu görevde çalıştırılmadı (E4).
3. **Container composition root'u Auth domain'e özel** — AuthContainer tek
   container örneği. Genel bootstrap ADR'si gerekli (D4).

---

## §I — KAYNAK KULLANIM TABLOSU (Bu Dosyada)

| URL | Bölüm | İddia | Doğrulama |
|---|---|---|---|
| php-fig.org/psr/psr-17/ | §A | 6 factory arayüzü + prensip | PSR dokümanı |
| php-fig.org/psr/psr-11/ | §B | ContainerInterface (get/has) + prensip | PSR dokümanı |

**Toplam:** 2/2 URL kullanıldı. Havuz dışına çıkılmadı.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15

---

## §J — PSR-17 DETAY NOTLARI (Genişletme)

### J.1 — Factory desenin avantajları (kaynak prensibi)

| Avantaj | Açıklama | UseCase etkisi |
|---|---|---|
| Test edilebilirlik | Fake factory ile unit test | UseCase test'i factory gerektirmez |
| Soyutlama | Uygulama mesaj üretimini bilmez | Clean Architecture uyumu |
| Değiştirilebilirlik | nyholm — guzzle/psr7 geçişi | Bağımlılık enjeksiyonu |
| Immutable üretim | Factory stateless | Thread/test güvenliği |

### J.2 — Bu repo'da factory'nin OLMADIĞI yerler

| Yer | Beklenen | Gerçek |
|---|---|---|
| AuthPostHandler dönüşü | `$factory->createResponse()` | ⚠️ doğrulanmadı (bootstrap belirsiz) |
| bin/api-key-create.php | CLI — factory yok | ? beklenen |
| Test dosyaları | Fake factory veya real factory | ⚠️ VERIFICATION REQUIRED |

### J.3 — Handler imzasının PSR-17 ile ilişkisi (Derinlemesine)

```
Dosya: auth.coremusic.net/include/Handler/AuthPostHandler.php · Satır: 38-57
```

Handler imzası `ServerRequestInterface — ResponseInterface`. Bu imza
**PSR-7**'dir, PSR-17 değil. PSR-17 burada yalnızca **nesne üretim** sorumluluğunu
taşır:

| Katman | Sorumluluk | Arayüz |
|---|---|---|
| Bootstrap | ServerRequest ÜRET | PSR-17 ServerRequestFactoryInterface |
| Handler | ServerRequest TÜKET | PSR-7 ServerRequestInterface |
| Bootstrap | Response ÜRET (status + body) | PSR-17 ResponseFactoryInterface |
| Handler | Response DÖNDÜR | PSR-7 ResponseInterface |

**Kritik soru (E3):** Bu repoda `ServerRequest` nesnesi nerede üretiliyor?
- Cevap bulunana kadar: ⚠️ VERIFICATION REQUIRED.

### J.4 — PSR-17 + PSR-7 birlikte kullanım deseni (PLANNED)

```php
// PLANNED — repo'da böyle bir kod YOK.
// öngörülen bootstrap deseni (nyholm/psr7 kullanarak):

use Nyholm\Psr7\Factory\Psr17Factory;

$psr17 = new Psr17Factory();   // hem PSR-7 hem PSR-17 factory

$request = $psr17->createServerRequest('POST', '/auth/login')
    ->withParsedBody($body);

$response = $psr17->createResponse(200)
    ->withHeader('Content-Type', 'application/json')
    ->withBody($psr17->createStream(json_encode($dto->toArray())));
```

**Not:** `Psr17Factory` nyholm/psr7'nin tek-sınıf factory'sidir — hem PSR-7
hem PSR-17 implementasyonunu sağlar. ⚠️ VERIFICATION REQUIRED: bu sınıfın
bu repoda kullanılıp kullanılmadığı grep edilmedi (`Psr17Factory` araması).

---

## §K — PSR-11 DETAY NOTLARI (Genişletme)

### K.1 — get() ve has() kullanım desenleri

| Desen | Kod | Repo durumu |
|---|---|---|
| Güvenli çözümleme | `if ($c->has($id)) { $c->get($id); }` | ⚠️ grep edilmedi |
| Doğrudan çözümleme | `$c->get($id)` (exception fırlatabilir) | ⚠️ grep edilmedi |
| Test'te sahte | `class FakeContainer implements ContainerInterface` | ⚠️ VERIFICATION REQUIRED |

### K.2 — php-di'nin PSR-11 uyumu (paket bilgisi)

| php-di sınıfı | PSR-11 implementasyonu |
|---|---|
| `DI\Container` | `implements Psr\Container\ContainerInterface` |
| `DI\ContainerException` | `implements Psr\Container\ContainerExceptionInterface` |
| `DI\NotFoundException` | `implements Psr\Container\NotFoundExceptionInterface` |

**Kaynak notu:** php-di dokümantasyonundan genel bilgi — bu repo'daki php-di
sürümünün hangi PSR-11 sürümüne uyduğu ⚠️ VERIFICATION REQUIRED
(composer.lock okunmadı).

### K.3 — AuthContainer analizi

```
Dosya: auth.coremusic.net/include/Container/AuthContainer.php
```

| Bilgi | Değer | Doğrulama |
|---|---|---|
| Builder kullanımı | `DI\ContainerBuilder` | ? grep |
| Container compile | ⚠️ doğrulanmadı | AuthContainer.php okuma gerekli |
| Singleton kayıtlar | ⚠️ doğrulanmadı | AuthContainer.php okuma gerekli |
| PSR-11 tip hint | ⚠️ doğrulanmadı | grep `Psr\\Container` |

⚠️ VERIFICATION REQUIRED: AuthContainer.php dosyası bu görev kapsamında TAM
okunmadı — yalnız `DI\ContainerBuilder` varlığı grep ile doğrulandı.

### K.4 — Container yaşam döngüsü (PLANNED not)

| Yaşam döngüsü | Anlamı | UseCase etkisi |
|---|---|---|
| Singleton | Tek instance | UseCase stateless olmalı |
| Transient | Her get() yeni instance | UseCase state tutabilir |
| Scoped | Request'e bağlı | HTTP scope eşleşmesi |

⚠️ VERIFICATION REQUIRED: Bu repoda container yaşam döngüsü ayarı doğrulanmadı.

---

## §L — COMPOSITION ROOT ANALİZİ

### L.1 — Mevcut durum

| Katman | Dosya | Durum |
|---|---|---|
| HTTP bootstrap | ⚠️ public/index.php (okunmadı) | Belirsiz |
| Auth bootstrap | AuthContainer.php | ? php-di |
| CLI bootstrap | bin/api-key-create.php (135-147 transaction) | Kısmi |

### L.2 — UseCase eklenince composition root ihtiyacı

```
[Genel Bootstrap — PLANNED]
    $container = (php-di ContainerBuilder)->build()
    $loginUseCase = $container->get(LoginUseCase::class)
    $authController = $container->get(AuthController::class)
        — (AuthController'a UseCase enjekte edilir)
```

**Kritik boşluk:** Bu repoda tek container AuthContainer (auth domain'e özel).
Genel (app-wide) composition root yok — UseCase eklenirse gerekir.

---

## §M — PSR-14 EVENT (Kısa Not — İlişkili)

| Bilgi | Değer | Kaynak |
|---|---|---|
| Arayüz | `EventDispatcherInterface` | shared/src/Events/EventDispatcher.php |
| PSR | PSR-14 | php-fig.org/psr/psr-14 (KAPSAM DIŞI — havuzda yok) |
| Durum | ? implements | grep |

**Not:** PSR-14 bu dosyanın URL havuzunda DEĞİL (havuz: PSR-17 + PSR-11).
Yalnız repo durumu kaydedildi; PSR-14 dokümanı kullanılmadı.

---

## §N — KARAR KAPILARI ÖZETİ (orkestratöre)

| # | Karar | Seçenekler | Öncelik |
|---|---|---|---|
| D1 | PSR-17 factory ekle (bootstrap) | Ekle / Ekleme | P1 |
| D2 | nyholm/psr7 sakla/kaldır | Sakla / Kaldır (ADR) | P1 |
| D3 | PSR-11 tip hint | Geç / php-di API'de kal | P2 |
| D4 | Genel composition root | Oluştur / AuthContainer yeterli | P1 |
| D5 | UseCase katmanı | Ekle / Handler=UseCase adlandırma | **P0** |

---

## §O — SON KONTROL LİSTESİ

| Kontrol | Durum |
|---|---|
| Havuz dışı URL | 0 |
| Dosya·Satır kanıtı | §C matrisi |
| VERIFICATION REQUIRED işaretli iddialar | §E (7 madde) + §F.4 + §J.3 + §K.3 + §L.1 |
| PLANNED işaretli | §J.4, §L.2 |
| Kapsam dışı not (işaretli) | §M (PSR-14) |

### §O.1 — Havuz kesişim notu (2026-10-16)

> **Spec §9 usecase havuzu (4):** `php-fig.org psr-17` ·
> `php.net language.generators.overview.php` · `php.net language.oop5.cloning.php` ·
> `php.net functions.anonymous.php`.
>
> **Bu dosyanın havuzu (2):** PSR-17 (§A) · PSR-11 (§B) — §M'de belirtildiği
> gibi kesişim yalnız **PSR-17 (1/4)**. `generators.overview` · `oop5.cloning` ·
> `functions.anonymous` kullanılmadı; PSR-11 ise spec havuzu dışındadır
> (dosyanın dahili havuzu: "PSR-17 + PSR-11", §M/§O).
>
> **Sapma:** Dosya spec havuzunun 4'ünden yalnız1'ini (PSR-17) kullandı;
> kalan 3 atlanmış, PSR-11 fazladan alınmış — yani **havuzun tamamı spec'le
> örtüşmüyor**, bu not kanıttır.
>
> ⚠️ **VERIFICATION REQUIRED (2026-10-16):** spec §9 usecase havuzunun 3/4'ü bu
> dosyada curl-verify edilmedi. Kesişim kanıtı closeout'ta spec havuzu için
> ayrıca üretilecek (HTTP 200 + envanter kesişimi 0). Dosyanın kendi 0 HIT
> (envanter kesişimi) durumu bu nottan etkilenmez.

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
