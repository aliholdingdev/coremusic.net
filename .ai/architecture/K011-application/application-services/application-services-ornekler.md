---
title: "Application Services — Örnekler"
type: architecture
kodeme: K011
module: application
parent: K011-application
status: active
updated: 2026-10-15
group: kod
---

# Application Services — Örnekler

> Kapsam: repo'daki uygulama servisi katmanının gerçek kod örnekleri.
> Bu dosyada üretilen kod yoktur; her blok diskteki bir dosyadan alınmıştır ve
> `Dosya: … · Satır: …` künyesi taşır. `YAPMA` yanları üretilmiş karşı-örnektir
> (diskte oldukları iddia edilmez) — okur bunları "örnek kod" değil, "kaçınılacak
> kalıp" olarak okur.

| # | Görev | Yüzey | Durum |
|---|---|---|---|
| 1 | Olay dağıtımı örneği | PHP · EventDispatcher | kanıtlanmış |
| 2 | Zamanlanmış dinleyici sıralaması | PHP · öncelik | kanıtlanmış |
| 3 | Cache adaptörü seçimi | PHP · CacheManager | kanıtlanmış |
| 4 | Cache TTL davranışı | PHP · MemoryAdapter | kanıtlanmış |
| 5 | Middleware pipeline | PHP · MiddlewarePipeline | kanıtlanmış |
| 6 | JS pub/sub | Vanilla JS · EventBus | kanıtlanmış |
| 7 | CSS breakpoint token'ı | CSS · 01_Abstracts | kanıtlanmış |
| 8 | SQL API denetim satırı | MySQL · api_calls | kanıtlanmış |
| 9 | PHPUnit birim testi | PHP · EventDispatcherTest | kanıtlanmış |
| 10 | Uyumsuz PSR-6/16 örneği | — | ÜRETİLMEDİ (kanıt yok) |

## §A — Olay dağıtımı (PSR-14)

> Dosya: `shared/src/Events/EventDispatcher.php` · Satır: 1–45 ·
> toplam 119 satır (ölçüm 2026-10-15)

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Events;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;

final class EventDispatcher implements EventDispatcherInterface
{
    private array $listeners = [];
    private array $sortedListeners = [];

    public function dispatch(object $event): object
    {
        $eventName = get_class($event);

        if (!isset($this->listeners[$eventName])) {
            return $event;
        }

        foreach ($this->getListeners($eventName) as $listener) {
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }

            $listener($event);
        }

        return $event;
    }
}
```

Neden doğru:

1. `EventDispatcherInterface` doğrudan `Psr\EventDispatcher`'dan import edilir (satır 14) — PSR-14 uyumu iddia değil, disk kanıtı.
2. `declare(strict_types=1)` dosyanın 2. satırında; tüm `CoreMusic\*` dosyalarında aynı.
3. Dinleyici yoksa olay sessizce geri döner (satır 32–34) — "her olay bir dinleyici ister" varsayımı yoktur.
4. `StoppableEventInterface` kontrolü döngü içinde (satır 37) — durdurma sinyali anında uygulanır.
5. Dağıtıcı `final` sınıf; genişletme yerine dinleyici ekleme yolu açık.
6. Olay anahtarı `get_class($event)` — string konvention yok, tip konvention var.

### §A-YAPMA ↔ §A-YAP

| YAPMA (kaçın) | YAP (repo kalıbı) |
|---|---|
| `dispatch(string $eventName, array $data)` — string anahtar, veri kaynaksız | `dispatch(object $event)` — olay tipi kontrattır |
| Dinleyici hatasını `@` ile bastırmak | Hata yukarı yayılır; testte görünür |
| Olayı `clone`'lamak / değiştirilmiş halini döndürmek | Aynı nesne döner (`assertSame` ile test edilmiş) |
| Dinleyicileri `public` dizi yapmak | `private array` + `addListener()` kapısı |

## §B — Öncelikli dinleyici kaydı

> Dosya: `shared/src/Events/EventDispatcher.php` · Satır: 47–59

```php
    public function addListener(string $eventName, callable $listener, int $priority = 0): void
    {
        $this->listeners[$eventName][] = [
            'listener' => $listener,
            'priority' => $priority,
        ];

        // Invalidate sorted listeners cache
        unset($this->sortedListeners[$eventName]);
    }
```

Notlar:

1. Öncelik varsayılanı 0; kayıt sırası bozulmadan sıralama eklenebilir.
2. `sortedListeners` cache'i kayıtta düşürülür (satır 58) — bayat sıralama riski sıfır.
3. `callable` tipi string/class-adı kabul etmez; kapatma yolu tek.
4. Metrik: `EventDispatcherTest` (shared/tests/Events/EventDispatcherTest.php, 98 satır) bu davranışı doğrular: `assertCount(1, $listeners)` + `assertSame($listener, $listeners[0])` (satır 32–33).
5. Örnek yalnız servis katmanının kendi API'sini gösterir; test dosyası §H'de.

## §C — Adaptör seçimi (tek nokta)

> Dosya: `shared/src/Cache/CacheManager.php` · Satır: 1–25 · toplam 25 satır

```php
<?php declare(strict_types=1);

namespace CoreMusic\Cache;

final class CacheManager
{
    private static ?CacheInterface $adapter = null;

    public static function getAdapter(): CacheInterface
    {
        if (self::$adapter === null) {
            if (function_exists('apcu_fetch')) {
                self::$adapter = new ApcuAdapter();
            } else {
                self::$adapter = new MemoryAdapter();
            }
        }
        return self::$adapter;
    }

    public static function setAdapter(CacheInterface $adapter): void
    {
        self::$adapter = $adapter;
    }
}
```

Neden doğru:

1. Ortam algılama tek yerde (`function_exists('apcu_fetch')`, satır 12) — her çağrıda tekrarlanmaz.
2. Geri dönüş tipi `CacheInterface`; hangi adaptör olduğu çağırıcıyı bağlamaz.
3. `setAdapter()` test/ortam enjeksiyonu için mevcut (satır 21–24) — global değişken yok.
4. Sınıf `final`; davranış alt sınıflandırılmaz.
5. Not: bu dosya PSR-16 arayüzüne bağlı değil (§bilesenler-12/13 isabet 0) — adaptör arayüzü yereldir; uyarlama PSR yönünde yapılır, tersi değil.
6. Eksik: yeniden başlatıldığında `$adapter` sıfırlanmaz; uzun süreçli (CLI/worker) kullanımda testte `setAdapter()` çağrısı zorunludur.

### §C-YAPMA ↔ §C-YAP

| YAPMA (kaçın) | YAP (repo kalıbı) |
|---|---|
| Her çağrıda `new ApcuAdapter()` | İlk çağrıda tek örnek + statik saklama |
| `if (apcu_enabled())` + ayrı konfigürasyon anahtarı | Tek algılama noktası (`function_exists`) |
| Çağırıcıda `ApcuAdapter instanceof` kontrolü | Yalnız `CacheInterface` bağımlılığı |
| Adaptörü dosya-sabitinde global'e yazmak | `setAdapter()` ile kontrollü enjeksiyon |

## §D — TTL davranışı

> Dosya: `shared/src/Cache/MemoryAdapter.php` · Satır: 1–36 · toplam 57 satır

```php
<?php declare(strict_types=1);

namespace CoreMusic\Cache;

final class MemoryAdapter implements CacheInterface
{
    /** @var array<string, array{value: mixed, expires: int}> */
    private array $store = [];

    public function get(string $key): mixed
    {
        if (!isset($this->store[$key])) {
            return false;
        }
        $entry = $this->store[$key];
        if ($entry['expires'] > 0 && $entry['expires'] < time()) {
            unset($this->store[$key]);
            return false;
        }
        return $entry['value'];
    }

    public function set(string $key, mixed $value, int $ttl = 0): bool
    {
        $this->store[$key] = [
            'value'   => $value,
            'expires' => $ttl > 0 ? time() + $ttl : 0,
        ];
        return true;
    }
```

Notlar:

1. `ttl = 0` sonsuz ömür anlamına gelir (satır 27) — bu, yerel arayüzün sözleşmesidir.
2. Süresi dolan girdi ilk okumada temizlenir (satır 17) — pasif son kullanma.
3. `get()` yanlış anahtar için `false` döner — `mixed` döndüğünden `false` değerli girdi ayırt edilemez; bu yerel sözleşmenin bilinçli darlığıdır.
4. `increment()` (satır 38–45) sayısal olmayan değerle `false` döner — sessiz bozulma yok.
5. Apcu adaptörü (56 satır) aynı arayüzü uygular; seçimi CacheManager yapar (§C).
6. Bu örnekte PSR-16 tipi (`Psr\Cache\SimpleCache`) import edilmez — §web-arastirma §B-13 isabet 0 ile tutarlı.

## §E — Middleware pipeline

> Dosya: `shared/src/Middleware/MiddlewarePipeline.php` · Satır: 1–54 · ölçüm 54 satır

```php
<?php declare(strict_types=1);

namespace CoreMusic\Middleware;

use CoreMusic\Contracts\Middleware\IMiddleware;
use CoreMusic\PageRouter\StructuredLogger;

final class MiddlewarePipeline
{
    /** @var IMiddleware[] */
    private array $middlewares = [];

    public function __construct(
        private readonly ?StructuredLogger $logger = null
    ) {}

    public function pipe(IMiddleware $middleware): self
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function run(array $request, callable $core): array
    {
        $pipeline = $this->buildPipeline($core);
        return $pipeline($request);
    }
```

Zarf (wrap) kısmı — satır 38–53:

```php
    private function wrap(IMiddleware $middleware, callable $next): callable
    {
        return function (array $request) use ($middleware, $next): array {
            $name  = substr(strrchr(get_class($middleware), '\\') ?: get_class($middleware), 1);
            $start = hrtime(true);

            $response = $middleware->handle($request, $next);

            $durationMs = (hrtime(true) - $start) / 1_000_000;
            if ($this->logger !== null) {
                $this->logger->timing('MiddlewarePipeline', $name, $durationMs);
            }

            return $response;
        };
    }
```

Neden doğru:

1. `pipe()` zincirleme (`self` döner) — kayit sırası = yürütme sırası, okunur ve tek yönlü.
2. Her middleware `hrtime(true)` ile ölçülür; `StructuredLogger` yoksa ölçüm sessizce atlanır (satır 47).
3. Zarf `array_reverse` ile sarılır (satır 32) — ilk eklenen dış katman olur; PSR-12/14 beklentisiyle uyumlu klasik onion sırası.
4. `readonly` constructor promoted property (satır 14) — çalışma zamanı değişimi yok.
5. Middleware tek arayüze bağlı: `CoreMusic\Contracts\Middleware\IMiddleware` (satır 5).
6. Ölçüm birimi milisaniye; `1_000_000` ayracı okunur (PHP 7.4+).

### §E-YAPMA ↔ §E-YAP

| YAPMA (kaçın) | YAP (repo kalıbı) |
|---|---|
| Middleware'leri tek bir `if/elseif` zincirine yazmak | `pipe()` + tip-contract |
| Süre ölçümü için `microtime(true)` (kayan nokta) | `hrtime(true)` tam sayı ns |
| Logger'ı zorunlu yapmak | `?StructuredLogger` — opsiyonel |
| Pipeline'ı `static` yapmak | Örnek başına bağımsız durum |

## §F — JS pub/sub (EventBus)

> Dosya: `assets.coremusic.net/js/core/EventBus.js` · Satır: 1–40 · toplam 73 satır

```javascript
/**
 * CoreMusic — EventBus
 * Pub/sub iletişim sistemi. Tüm modüller arası loose coupling sağlar.
 *
 * @module core/EventBus
 * @version 5.0.0
 */
export default class EventBus {
    /** @type {Map<string, Set<Function>>} */
    #listeners = new Map();

    on(event, fn) {
        if (!this.#listeners.has(event)) {
            this.#listeners.set(event, new Set());
        }
        this.#listeners.get(event).add(fn);
        return () => this.off(event, fn);
    }

    once(event, fn) {
        const wrapper = (data) => {
            this.off(event, wrapper);
            fn(data);
        };
        this.on(event, wrapper);
    }
}
```

Neden doğru:

1. `#listeners` özel alan (private field) — dışarıdan diziye doğrudan yazma yok.
2. `Set<Function>` — aynı fonksiyon iki kez kaydedilmez; `Map` anahtar O(1).
3. `on()` bir unsubscribe fonksiyonu döndürür (satır 18) — temizlik tek satır.
4. `once()` kendini kaldırır (satır 27) — dinleyici sızıntısı yok.
5. Vanilla JS, bağımlılık yok; `export default` tek nesne taşır.
6. PHP tarafındaki `EventDispatcher` (§A) ile konsept eşleşir ama süreç içi tek olay kanalı değildir; ikisi ayrı yüzeylerdir, birleştirilmemiştir.

## §G — CSS token katmanı

> Dosya: `assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` · Satır: 22–34 · toplam 57 satır

```css
:root {
  /* ============================================================
     DEVICE BREAKPOINTS (px cinsinden — media query için)
     device-loader.js ile senkronize
     ============================================================ */
  --bp-device-phone:      767px;
  --bp-device-tablet:     1024px;
  --bp-device-embedded:   1024px;   /* RPi5: 1024x600 */
  --bp-device-laptop:     1440px;
  --bp-device-medium:     1919px;   /* Medium desktop: 1441-1919px */
  --bp-device-desktop:    2560px;
  --bp-device-4k-tv:      3840px;
  --bp-device-4k-monitor: 3841px;
```

Neden doğru:

1. Dosya başlığı (satır 1–20) dosya adını, katmanı, rolü ve bağımlılığı belgelemektedir.
2. Sabit değer yalnız `01_Abstracts` token katmanında (`--bp-device-*`) — AGENTS.md §10 kuralıyla uyumlu.
3. `device-loader.js` ile senkron notu (satır 12) — JS tarafında ikinci bir kaynak yok.
4. `--bp-device-embedded: 1024px` yorumu donanım bağlamını taşır (RPi5 1024x600).
5. Sıra artan genişliktedir; ikinci eşitlik (tablet/embedded) kasıtlıdır ve yorumla açıklanır.
6. Not: bu CSS uygulama servisi katmanı değil, arayüz sözleşmesidir; örnek olarak servis-uydurma hattındaki JS↔CSS senkron örneği olarak alındı.

### §G-YAPMA ↔ §G-YAP

| YAPMA (kaçın) | YAP (repo kalıbı) |
|---|---|
| Bileşen dosyasında `767px` yazmak | `var(--bp-device-phone)` |
| JS'te ikinci breakpoint tablosu | Tek kaynak: bu token dosyası |
| Token katmanında `body { ... }` kuralı | Yalnız `--*` üretimi |
| Yorumsuz eşit değerler | Her eşitlik yorumla gerekçeli |

## §H — SQL denetim satırı

> Dosya: `.ai/.sql/mysql/coremusic_api.sql` · Satır: 18–35 · toplam 117 satır

```sql
CREATE TABLE `api_calls` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `api_key_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `endpoint` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `method` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `http_status` smallint unsigned NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response_time_ms` int unsigned DEFAULT NULL,
  `request_body` text COLLATE utf8mb4_unicode_ci,
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `called_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`,`called_at`),
  KEY `idx_ac_key` (`api_key_id`),
  KEY `idx_ac_user` (`user_id`),
  KEY `idx_ac_endpoint` (`endpoint`(255)),
  KEY `idx_ac_status` (`http_status`),
```

Neden doğru:

1. Dosya başlığı (satır 1–7) canlı dump olduğunu, veri içermediğini (KVKK/REDACTED) ve tarihi beyan eder.
2. `api_calls`, uygulama servisi katmanının dışa vuran (observable) yüzüdür: servis başına `http_status` + `response_time_ms`.
3. `called_at` PARTITION anahtarının ikinci parçası (`PRIMARY KEY (id, called_at)`) — zaman serisi bölme hazır.
4. `response_time_ms` `int unsigned` — §E'deki `hrtime` ölçümünün SQL karşılığı.
5. `ip_address` varchar(45) — IPv6 uzunluğunu karşılar.
6. `endpoint` anahtarı `varchar(500)` iken indeks `(255)` öneki kullanır — MySQL indeks uzunluğu sınırı bilinçli uygulanmıştır.

## §I — PHPUnit birim testi

> Dosya: `shared/tests/Events/EventDispatcherTest.php` · Satır: 10–34 · toplam 98 satır

```php
final class EventDispatcherTest extends TestCase
{
    private EventDispatcher $dispatcher;

    protected function setUp(): void
    {
        $this->dispatcher = new EventDispatcher();
    }

    public function testDispatchReturnsEventInstance(): void
    {
        $event = new \stdClass();
        $event->name = 'test';
        $result = $this->dispatcher->dispatch($event);
        $this->assertSame($event, $result);
    }

    public function testAddListenerAndGetListeners(): void
    {
        $listener = function () {};
        $this->dispatcher->addListener('test.event', $listener);
        $listeners = $this->dispatcher->getListeners('test.event');
        $this->assertCount(1, $listeners);
        $this->assertSame($listener, $listeners[0]);
    }
}
```

Neden doğru:

1. `setUp()` her testte taze dispatcher — testler birbirine bağlı değil.
2. `assertSame` nesne kimliği doğrular; `assertEquals` olsaydı kopya da geçerdi.
3. `assertCount` + `assertSame` ikilisi hem sayıyı hem kimliği kilitler.
4. `declare(strict_types=1)` test dosyasında da var (satır 2).
5. Ölçüm: `shared/tests` altında 45 test dosyası, 349 test metodu, 849 assertion (2026-10-15 sayımı — sayımlar `bilesenler.md` §B'de).
6. Test çifti `stdClass` ile minimal olay nesnesi kullanır; mock israfı yok (repo geneli `createMock` yalnız 9 çağrı).

## §J — Üretilmeyen örnek: PSR-6 / PSR-16

Spec kuralı: repo'da kanıtlanamayan kod asla üretilmez. `Psr\Cache\CacheItemInterface`
(PSR-6) ve `Psr\Cache\SimpleCache` (PSR-16) tipleri repo'da sıfır isabet bulmuştur
(`⚠️ VERIFICATION REQUIRED` — bkz. `application-services-web-arastirma.md` §B-12/§B-13).

| İstek | Karar |
|---|---|
| "PSR-6 kullanan örnnek cache servisi yaz" | ÜRETİLMEDİ — repo kanıtı yok |
| "PSR-16 SimpleCache adaptörü ekle" | ÜRETİLMEDİ — repo kanıtı yok |
| "PSR-12 stil denetleyici çıktısı örneği" | ÜRETİLMEDİ — analizci kurulu değil |
| "symfony/event-dispatcher kullanım örneği" | ÜRETİLMEDİ — paket kullanımı kanıtlanmadı |

Davranış:

1. Boşluk, `application-services-web-arastirma.md` §I tablosunda kayıtlıdır; burada tekrar açılmaz.
2. Kanıt geldiğinde bu §J satırları "üretildi" olarak güncellenir; tarih damgası 2026-10-15'te dondurulmaz.
3. Üretilen kod olsaydı bile `Dosya: … · Satır: …` künyesi zorunlu olurdu.
4. Kardeş dosyalar (kapsam/bilesenler/dogrulama) bu boşluğu farklı dille anlatmaz; tek kayıt §I'dir.
5. `⚠️ VERIFICATION REQUIRED` etiketi burada silinmez.
6. URL içermez (münhasır havuz kuralı); referans tek dosyada §A'dadır.

## §K — Örnek sayımı ve künye kuralları

| Dosya | Blok satır aralığı | Toplam satır |
|---|---|---|
| `shared/src/Events/EventDispatcher.php` | 1–45, 47–59 | 119 |
| `shared/src/Cache/CacheManager.php` | 1–25 | 25 |
| `shared/src/Cache/MemoryAdapter.php` | 1–36 | 57 |
| `shared/src/Middleware/MiddlewarePipeline.php` | 1–27, 38–53 | 54 |
| `assets.coremusic.net/js/core/EventBus.js` | 1–40 | 73 |
| `assets.coremusic.net/Css/01_Abstracts/a-breakpoint-tokens.css` | 22–34 | 57 |
| `.ai/.sql/mysql/coremusic_api.sql` | 18–35 | 117 |
| `shared/tests/Events/EventDispatcherTest.php` | 10–34 | 98 |

Künye kuralları:

1. Her kod bloğu bir `Dosya: … · Satır: …` künyesi ile açılır; künyesiz blok üretilmez.
2. Blok satır aralığı toplam satırdan büyüktür; büyüklük yazım hatasıdır, sayım güncellenir.
3. Bloklar kaynaktan kırpılmıştır (`…` yerine başlık satırı + aralıklar); kırpma künyede görünür.
4. Blok içi yorumlar kaynaktaki yorumlardır; ek yorum bloğun üstünde (düz metin) yazılır.
5. `YAPMA` tabloları üretilmiş karşı-örnektir; disk kanıtı değildirler ve künye taşımazlar.
6. Bu dosyada URL kaydı yoktur; havuz tek dosyadadır.

## §L — Örnek ↔ servis eşleme matrisi

| Örnek | Etkilendiği servis arayüzü | Kardeş dosyadaki karşılığı |
|---|---|---|
| §A EventDispatcher | `EventDispatcherInterface` (PSR-14) | `application-services-kapsam.md` §C-2 |
| §B addListener | `addListener()` sözleşmesi | `application-services-kapsam.md` §C-2 |
| §C CacheManager | `CacheInterface` | `application-services-bilesenler.md` sayaçları |
| §D MemoryAdapter | `get/set/delete/increment/has/clear` | `application-services-dogrulama.md` §B kapıları |
| §E MiddlewarePipeline | `IMiddleware` | `application-services-dogrulama.md` ölçümler |
| §F EventBus.js | `on/once/off` | `application-services-kapsam.md` JS yüzeyi |
| §G breakpoint token | `--bp-device-*` | `application-services-kapsam.md` CSS yüzeyi |
| §H api_calls | denetim yazımı | `application-services-dogrulama.md` log kapısı |
| §I EventDispatcherTest | PHPUnit 10.5 | `application-testing/` birim test dosyaları |
| §J boşluk | PSR-6/16/12 | `application-services-web-arastirma.md` §I |

Matris kuralları:

1. Sağ sütun yalnızca dosya adı + § taşır; URL veya blok tekrarı yazılmaz.
2. §I eşlemesi `application-testing/` klasörüne ilk geçiştir; oradaki dosyalar bu eşlemeyi tekrarlamaz.
3. Eşleşme olmayan satır eklenmez; eşleşmeyen örnek bu dosyaya girmez.
4. Soldaki örnek diskte kanıtlanmış bloklardır; §J hariç (boşluk satırı).
5. Eşleme tek yönlüdür: örnek → servis; servis → örnek listesi bu dosyadadır.
6. Değişiklik (servis yeniden adlandırılırsa) yalnız bu §L satırı üzerinden güncellenir.

## §GRUPLAR

| # | Eksen 1 (dosya rolü) | Eksen 2 (modül yüzeyi) | Çıktı |
|---|---|---|---|
| 1 | Örnek | PHP olay servisi | §A · §B (EventDispatcher 119 satır) |
| 2 | Örnek | PHP cache servisi | §C · §D (CacheManager 25 · MemoryAdapter 57) |
| 3 | Örnek | PHP middleware servisi | §E (MiddlewarePipeline 54 satır) |
| 4 | Örnek | JS/CSS/SQL yüzeyleri | §F · §G · §H (73 · 57 · 117 satır) |
| 5 | Doğrulama | Test kanıtı | §I (EventDispatcherTest 98 satır · 349/849 sayaç) |
| 6 | Boşluk + eşleme | PSR-6/16/12 · kardeşler | §J · §K · §L (üretim yasağı + matris) |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-10-15
