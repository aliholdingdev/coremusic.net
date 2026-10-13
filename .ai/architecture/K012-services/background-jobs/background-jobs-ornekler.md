---
node_id: K012-services/background-jobs
node_type: module
role: ornekler
status: draft
parent_id: K012-services/background-jobs
children: []
depends_on:
  - "background-jobs/background-jobs-anayasa.md"
  - "background-jobs/background-jobs-surucu.md"
  - "background-jobs/index.md"
evidence:
  - "S1 — shared/src/Events/EventDispatcher.php 119 satır (dispatch: 28, addListener: 50, removeListener: 64, getListeners: 82, hasListeners: 101, sortListeners: 109)"
  - "S1 — shared/src/Contracts/Events/IntegrationEventInterface.php 46 satır (interface: 17)"
  - "S1 — shared/src/Api/Registry/ServiceHealth.php 100 satır (checkAll: 26, getAggregatedStatus: 34, isServiceHealthy: 76, getUnhealthyServices: 87)"
  - "S5 — kuyruk/cron/worker/job/task/timer/async/schedule desenleri => 0 isabet (319 PHP dosyası, 2026-10-13)"
validation:
  - "§2 · §3 · §4 kod blokları diskten birebir kopyalanmıştır (2026-10-13 read)"
  - "§5 · §6 · §7 kuyruk/cron kodu YOK — yokluk satırı uydurulmadı"
open_questions:
  - "Q5 — iş motoru bilinmiyor → ⚠️ VERIFICATION REQUIRED"
  - "EventDispatcherTest.php içeriği okunmadı → ⚠️ VERIFICATION REQUIRED"
date: 2026-10-13
updated: 2026-10-13
version: 1.0.0
authority: "SSOT: EventDispatcher.php + IntegrationEventInterface.php + ServiceHealth.php (S1, 2026-10-13 read)"
group: kod
---

# background-jobs — Örnekler (Code Evidence)

> **Kural:** bu dosyadaki her kod bloğu 2026-10-13 tarihinde diskten okunmuş
> gerçek PHP kodunun **birebir** kopyasıdır. Uydurma örnek, taslak pseudokod
> veya "ileride böyle olacak" bloğu **yoktur**. Repo'da bulunmayan kuyruk /
> cron / worker kodu için §7 boşluk tablosu kullanılır.

## §1 Kod Kanıtı Envanteri

| # | Dosya | Satır | Rol | Blok § |
|---|---|---|---|---|
| 1 | shared/src/Events/EventDispatcher.php | 119 | tek mevcut sürücü | §2 · §3 · §4 |
| 2 | shared/src/Contracts/Events/IntegrationEventInterface.php | 46 | olay sözleşmesi | §4 |
| 3 | shared/src/Api/Registry/ServiceHealth.php | 100 | sağlık toplama | §5 |
| 4 | kuyruk/cron/worker kodu | 0 | — | §7 (yokluk) |
| 5 | shared/tests/Events/EventDispatcherTest.php | varlık S1 | test | §8 (içerik okunmadı) |

## §2 EventDispatcher — Sınıf Tanımı ve Kayıt Yapısı

Dosya: `shared/src/Events/EventDispatcher.php` / Satır: 20–23

```php
final class EventDispatcher implements EventDispatcherInterface
{
    private array $listeners = [];
    private array $sortedListeners = [];
```

**Gözlem (S1:20–23):** sınıf `final`'dir; genişletilemez. İki private dizi alan
kayıt deposu ve sıralı önbellek olarak kullanılır. `declare(strict_types=1)`
dosyanın 2. satırındadır (S1:2).

Arayüz bağımlılıkları dosyanın 14–15. satırlarındadır (S1:14–15):

```php
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\StoppableEventInterface;
```

## §3 EventDispatcher — Yayım Metodu (Dispatch)

Dosya: `shared/src/Events/EventDispatcher.php` / Satır: 28–45

```php
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
```

**Gözlemler (S1:28–45):**

| # | Gözlem | Satır |
|---|---|---|
| 1 | olay anahtarı `get_class` — ayrı ad alanı yok | 30 |
| 2 | dinleyici yoksa erken dönüş | 32–34 |
| 3 | döngü `getListeners()` üzerinden — önbellekli | 36 |
| 4 | durdurma `StoppableEventInterface` ile | 37–38 |
| 5 | dinleyici doğrudan çağrılıyor — senkron | 41 |
| 6 | `try/catch` yok — istisna yayılır | 36–42 |
| 7 | aynı `$event` nesnesi döner | 44 |

## §4 EventDispatcher — Kayıt, Kaldırma, Öncelik

Dosya: `shared/src/Events/EventDispatcher.php` / Satır: 50–59 (kayıt)

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

Dosya: `shared/src/Events/EventDispatcher.php` / Satır: 64–77 (kaldırma)

```php
public function removeListener(string $eventName, callable $listener): void
{
    if (!isset($this->listeners[$eventName])) {
        return;
    }
    
    $this->listeners[$eventName] = array_filter(
        $this->listeners[$eventName],
        fn($entry) => $entry['listener'] !== $listener
    );
    
    // Invalidate sorted listeners cache
    unset($this->sortedListeners[$eventName]);
}
```

Dosya: `shared/src/Events/EventDispatcher.php` / Satır: 109–118 (öncelik sıralama)

```php
private function sortListeners(string $eventName): array
{
    $listeners = $this->listeners[$eventName];
    
    usort($listeners, function ($a, $b) {
        return $b['priority'] <=> $a['priority'];
    });
    
    return $listeners;
}
```

**Gözlemler (S1:50–118):**

| # | Gözlem | Satır |
|---|---|---|
| 1 | öncelik varsayılanı 0 | 50 |
| 2 | kayıt girdisi `listener` + `priority` anahtarları | 52–55 |
| 3 | önbellek `unset` ile geçersizleşir (iki yerde) | 58 · 76 |
| 4 | silmede `array_filter` + `!==` referans karşılaştırma | 70–73 |
| 5 | sıralama `usort`, yön `$b <=> $a` (azalan) | 113–115 |
| 6 | `sortListeners` tek private metot | 109 |

## §5 IntegrationEventInterface — Olay Sözleşmesi

Dosya: `shared/src/Contracts/Events/IntegrationEventInterface.php` / Satır: 17–46

```php
interface IntegrationEventInterface
{
    /**
     * Get the event name.
     *
     * @return string Event name
     */
    public function eventName(): string;

    /**
     * Get the source service that originated the event.
     *
     * @return string Source service name
     */
    public function source(): string;

    /**
     * Get the event payload data.
     *
     * @return array Event payload
     */
    public function payload(): array;

    /**
     * Get the timestamp when the event occurred.
     *
     * @return \DateTimeImmutable Event timestamp
     */
    public function occurredOn(): \DateTimeImmutable;
}
```

**Gözlemler (S1:17–46):**

| # | Gözlem | Satır |
|---|---|---|
| 1 | dört metod — eventName, source, payload, occurredOn | 24 · 31 · 38 · 45 |
| 2 | `payload()` `array` — şema tip güvenli değil | 38 |
| 3 | `occurredOn()` immutable — zaman damgası zorunlu | 45 |
| 4 | `source()` köken servis adı taşır | 31 |
| 5 | somut uygulamalar `src/Events/Integration/` (3 dosya) — okunmadı | envanter S1 |

## §6 ServiceHealth — Sağlık Toplama Kodu

Dosya: `shared/src/Api/Registry/ServiceHealth.php` / Satır: 34–71

```php
public function getAggregatedStatus(): array
{
    $healthResults = $this->registry->healthCheck();
    
    $totalServices = count($healthResults);
    $healthyCount = 0;
    $degradedCount = 0;
    $unhealthyCount = 0;
    
    foreach ($healthResults as $result) {
        match ($result['status']) {
            'healthy' => $healthyCount++,
            'degraded' => $degradedCount++,
            'unhealthy' => $unhealthyCount++,
            default => null,
        };
    }
    
    // Determine overall status
    $overallStatus = 'healthy';
    if ($unhealthyCount > 0) {
        $overallStatus = 'unhealthy';
    } elseif ($degradedCount > 0) {
        $overallStatus = 'degraded';
    }
    
    return [
        'overall' => $overallStatus,
        'services' => [
            'total' => $totalServices,
            'healthy' => $healthyCount,
            'degraded' => $degradedCount,
            'unhealthy' => $unhealthyCount,
        ],
        'details' => $healthResults,
        'timestamp' => time(),
    ];
}
```

**Gözlemler (S1:34–71):**

| # | Gözlem | Satır |
|---|---|---|
| 1 | `match` ile üç sayacı güncelleme | 44–49 |
| 2 | `default => null` bilinmeyen durumu sessizce yok sayar | 48 |
| 3 | en-kötü-hal: unhealthy > degraded > healthy | 53–58 |
| 4 | sonuçta `timestamp` unix zamanı | 69 |
| 5 | `details` ham sağlık sonuçlarını taşır | 68 |
| 6 | `checkAll()` yalnız `registry->healthCheck()` çağırır | 26–29 |

## §7 Kuyruk / Cron / Worker — Yokluk Kaydı (Kod Bloğu YOK)

Repo'da kuyruk, cron, worker, job, task, timer, async veya schedule kodu
**bulunmamaktadır** (S5, 2026-10-13, 319 PHP dosyası). Bu nedenle bu konularda
kod bloğu **üretilmez**; yokluk satırı kaydedilir.

| # | Arama | Sonuç | Davranış |
|---|---|---|---|
| 1 | `**/*Queue*.php` | Repo'da kuyruk seviyesi kod YOK — kaynak: S5 0-isabet |
| 2 | `**/*Cron*.php` · `**/*Timer*.php` | Repo'da zamanlayıcı seviyesi kod YOK — kaynak: S5 0-isabet |
| 3 | `**/*Worker*.php` | Repo'da worker seviyesi kod YOK — kaynak: S5 0-isabet |
| 4 | `**/*Job*.php` · `**/*Task*.php` | Repo'da iş nesnesi seviyesi kod YOK — kaynak: S5 0-isabet |
| 5 | `**/*Async*.php` | Repo'da asenkron çalıştırma seviyesi kod YOK — kaynak: S5 0-isabet |
| 6 | `**/*Schedule*.php` | Repo'da planlama seviyesi kod YOK — kaynak: S5 0-isabet |
| 7 | `**/*Dispatch*.php` | 1 isabet = EventDispatcher.php + test — iş dağıtımı DEĞİL (§3) |

**Yasak:** bu satırlar "örnek olsun" diye sahte bir kuyruk sınıfı ile
doldurulmaz. Motor adı (Q5) açık kapı olarak kalır.

## §8 Test Dosyası — Varlık, İçerik Okunmadı

| # | Dosya | Varlık | İçerik | Durum |
|---|---|---|---|---|
| 1 | shared/tests/Events/EventDispatcherTest.php | ✅ S1 | okunmadı | ⚠️ VERIFICATION REQUIRED |
| 2 | shared/tests/Api/ (9 dosya) | ✅ S1 | okunmadı | ⚠️ VERIFICATION REQUIRED |
| 3 | shared/tests/Middleware/ (9 dosya) | ✅ S1 | okunmadı | ⚠️ VERIFICATION REQUIRED |

**Kural:** test dosyasının **varlığı** S1 kanıtıdır; içeriği (hangi davranışı
doğruladığı) okunmadan iddia edilmez.

## §9 Örnek → Sürücü → Anayasa Eşlemesi

| # | Örnek (§) | Sürücü konusu | Anayasa maddesi |
|---|---|---|---|
| 1 | §3 yayım | senkron döngü | B3 |
| 2 | §4 kayıt | dinleyici ekleme | B4 |
| 3 | §4 kaldırma | dinleyici çıkarma | B5 |
| 4 | §4 sıralama | öncelik azalan | B4 |
| 5 | §5 sözleşme | dört metod | B6–B9 |
| 6 | §6 toplama | en-kötü-hal durum | — (sağlık) |
| 7 | §7 yokluk | motor yok | B1–B2 · Q5 |

## §10 Web Araştırması — İddia Kaydı

Yalnızca 2026-10-13 doğrulamasında HTTP 200 dönen resmi kaynaklar kullanılır.

| İddia | URL | 2026-10-13 | Durum |
|---|---|---|---|
| PSR-14 EventDispatcher arayüzü dispatch metodunu tanımlar | https://www.rfc-editor.org/rfc/rfc9110.html | HTTP 200 | ⚠️ VERIFICATION REQUIRED — PSR-14 sayfası bu oturumda AYRI doğrulanmadı; rfc9110 bir HTTP semantiği RFC'sidir, PSR değildir |
| HTTP yanıtı 304 ile değişmez imzası sorgulanabilir | https://www.rfc-editor.org/rfc/rfc9110.html | HTTP 200 | ✅ — yalnızca bağlam notu |
| Etkinlik-güdümlü mimari, olayları yayınlayan ve dinleyen bileşenlere dayanır | https://www.cncf.io/ | HTTP 200 | ✅ — yalnızca bağlam; **ürün seçimi yapılmadı** |

**Not (red team):** PSR-14 spesifikasyonu (`www.php-fig.org/psr/psr-14/`) bu
oturumda **HTTP 200 olarak doğrulanmadı** → PSR-14 iddiaları yalnızca S1 kod
kanıtına (namespace import satırı, EventDispatcher.php:14) dayanır; URL olarak
yazılmaz.

## §11 Açık Sorular (Örnekler Tarafı)

| # | Soru | Kanıt durumu |
|---|---|---|
| 1 | EventDispatcherTest hangi davranışları doğruluyor? | içerik okunmadı ⚠️ |
| 2 | IntegrationEvent somut sınıfları payload'da ne taşıyor? | 3 dosya okunmadı ⚠️ |
| 3 | `checkAll()`'ı çağıran kod nerede? | çağrıran okunmadı ⚠️ |
| 4 | Dinleyici sayısı çalışma zamanında kaç? | bilinmiyor UNKNOWN |
| 5 | İş motoru ne? | Q5 açık |

## §12 Red Team Bulgu Listesi (Örnekler)

| # | Bulgu | Severity |
|---|---|---|
| 1 | Yayım senkronsuz ve sarmalanmamış — istisna çağırana yayılır (§3) | HIGH |
| 2 | `payload()` array — şema güvencesi yok (§5) | MEDIUM |
| 3 | `default => null` bilinmeyen durumu yutar (§6) | MEDIUM |
| 4 | Kuyruk/cron tarafında tek örnek bile yok (§7) | HIGH |
| 5 | Test içeriği okunmadı — doğrulama kapsamı bilinmiyor (§8) | MEDIUM |

## §13 EventDispatcher — Önbellekli Erişim Metotları

Dosya: `shared/src/Events/EventDispatcher.php` / Satır: 82–104

```php
public function getListeners(string $eventName): array
{
    if (!isset($this->listeners[$eventName])) {
        return [];
    }
    
    if (!isset($this->sortedListeners[$eventName])) {
        $this->sortedListeners[$eventName] = $this->sortListeners($eventName);
    }
    
    return array_map(
        fn($entry) => $entry['listener'],
        $this->sortedListeners[$eventName]
    );
}

public function hasListeners(string $eventName): bool
{
    return !empty($this->listeners[$eventName]);
}
```

**Gözlemler (S1:82–104):**

| # | Gözlem | Satır |
|---|---|---|
| 1 | kayıt yoksa boş dizi döner | 84–86 |
| 2 | önbellek yoksa `sortListeners` ile doldurur | 88–90 |
| 3 | `array_map` ile yalnız `listener` anahtarı alınır | 92–95 |
| 4 | `hasListeners` `!empty` ile sorgular | 103 |
| 5 | önbellek ilk çağrıda yazılır, sonraki kullanımda tekrar hesaplanmaz | 88–90 |

**Çokluk notu (S1:92):** `getListeners` her çağrıda `array_map` ile yeni bir dizi
üretir; yani önbellek `$sortedListeners`'ı tutsa da dönen dizi **her seferinde
tazedir** (kopyalanır). Bu, yayım döngüsünde her `dispatch` çağrısında bir dizi
kopyalama maliyeti demektir → ölçüm `⚠️ VERIFICATION REQUIRED` (performans profili
okunmadı).

## §14 Dosya Başlıkları ve ADR Referansları

Her üç dosya da PHPDoc başlığında bir ADR referansı taşır; bu referanslar
sürücünün mimari dayanağını gösterir (S1, dosya satır 5–10).

Dosya: `shared/src/Events/EventDispatcher.php` / Satır: 5–10

```php
/**
 * Event Dispatcher implementation.
 *
 * @file EventDispatcher.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */
```

Dosya: `shared/src/Contracts/Events/IntegrationEventInterface.php` / Satır: 5–10

```php
/**
 * Integration Event interface for cross-service communication.
 *
 * @file IntegrationEventInterface.php
 * @version 1.0.0
 * @see ADR-086-event-driven-architecture
 */
```

Dosya: `shared/src/Api/Registry/ServiceHealth.php` / Satır: 5–10

```php
/**
 * Service Health Aggregator.
 *
 * @file ServiceHealth.php
 * @version 1.0.0
 * @see ADR-084-api-gateway-architecture
 */
```

**Gözlem (S1):** olay yayımı iki dosyası **ADR-086** (olay-güdümlü mimari)'ya,
sağlık toplama ise **ADR-084** (API gateway mimarisi)'ya bağlanır. Sürücünün
olay tarafı ile sağlık tarafının **farklı ADR'ler** altında olduğu görünür —
sağlık toplama asenkron iş değil, gateway denetimidir. ADR metinleri bu oturumda
okunmadı → içerik `⚠️ VERIFICATION REQUIRED`.

## §15 ServiceHealth — Kalan Metotlar (Birebir)

Dosya: `shared/src/Api/Registry/ServiceHealth.php` / Satır: 17–29

```php
final class ServiceHealth
{
    public function __construct(
        private readonly ServiceRegistry $registry
    ) {}

    /**
     * Execute parallel health checks.
     */
    public function checkAll(): array
    {
        return $this->registry->healthCheck();
    }
```

Dosya: `shared/src/Api/Registry/ServiceHealth.php` / Satır: 76–99

```php
public function isServiceHealthy(string $serviceName): bool
{
    $healthResults = $this->registry->healthCheck();
    
    return isset($healthResults[$serviceName]) 
        && $healthResults[$serviceName]['status'] === 'healthy';
}

public function getUnhealthyServices(): array
{
    $healthResults = $this->registry->healthCheck();
    $unhealthy = [];
    
    foreach ($healthResults as $name => $result) {
        if ($result['status'] !== 'healthy') {
            $unhealthy[$name] = $result;
        }
    }
    
    return $unhealthy;
}
```

**Gözlemler (S1:17–99):**

| # | Gözlem | Satır |
|---|---|---|
| 1 | `readonly` constructor injection — `ServiceRegistry` bağımlılığı | 19–21 |
| 2 | `checkAll` DocBlock'u "parallel" der ama kod **tek çağrı** — paralellik görünmüyor | 24–29 |
| 3 | `isServiceHealthy` her çağrıda `healthCheck()`'i yeniden çağırır | 78 |
| 4 | `getUnhealthyServices` `!== 'healthy'` ile filtreler | 93 |
| 5 | DocBlock "parallel" iddiası ile kod arasındaki fark | 24–28 vs 28 |

**"Parallel" iddia-kod farkı (S1:24–29):** `checkAll` DocBlock'u "Execute parallel
health checks" der; ancak gövde yalnız `$this->registry->healthCheck()` çağırır —
paralel yürütme kodda **görünmez**. Gerçek paralellik `ServiceRegistry::healthCheck`
içinde olabilir mi → o metot okunmadı → `⚠️ VERIFICATION REQUIRED`. Red team
bulgusu: DocBlock iddiası kanıtlanmamış (§17-B6).

## §16 Olay Adı İkiliği (get_class vs eventName)

İki farklı adlandırma mekanizması mevcuttur; aralarındaki bağ okunmamıştır.

| # | Mekanizma | Kaynak | Anahtar | Kanıt |
|---|---|---|---|---|
| 1 | yerel yayım | `EventDispatcher::dispatch` | `get_class($event)` | S1:30 |
| 2 | entegrasyon sözleşmesi | `IntegrationEventInterface::eventName()` | `string` dönüşlü | S1:24 |

| # | Soru | Durum |
|---|---|---|
| 1 | `eventName()` değeri `get_class` ile aynı mı? | okunmadı ⚠️ |
| 2 | Entegrasyon olayları yerel `dispatch` ile mi yayınlanıyor? | okunmadı ⚠️ |
| 3 | Farklıysa iki ad nasıl eşleştiriliyor? | UNKNOWN |

**Bu ikilik çözülmeden** entegrasyon olaylarının yerel yayım zincirine girip
girmediği **bilinmez** → §17-B8 olarak red team kaydına eklenir.

## §17 Red Team Bulgu Listesi (Genişletilmiş)

| # | Bulgu | Severity |
|---|---|---|
| B1 | Yayım senkron, sarmalanmamış — istisna çağırana yayılır (§3) | HIGH |
| B2 | Kuyruk/cron/worker tarafında tek kod bloğu bile yok (§7) | HIGH |
| B3 | `checkAll` DocBlock "parallel" der, kod paralel değil (§15) | MEDIUM |
| B4 | `payload()` array — şema güvencesi yok (§5) | MEDIUM |
| B5 | `default => null` bilinmeyen durumu yutar (§6) | MEDIUM |
| B6 | ADR-086 / ADR-084 metinleri okunmadı (§14) | LOW |
| B7 | `getListeners` her çağrıda dizi kopyalar (§13) | LOW |
| B8 | `get_class` vs `eventName()` ikiliği çözülmedi (§16) | MEDIUM |
| B9 | Test içeriği okunmadı — doğrulama kapsamı bilinmiyor (§8) | MEDIUM |

## §18 Değişiklik Kaydı

| Tarih | Sürüm | Değişiklik |
|---|---|---|
| 2026-10-13 | 1.0.0 | örnekler yazıldı — 3 gerçek dosyadan kod blokları (birebir kopya) + 7 desenlik yokluk kaydı |

---

**Authority:** Bayram Ali / Vault Steward · **Mode:** Red Team · Human Mode · Truth Mode
